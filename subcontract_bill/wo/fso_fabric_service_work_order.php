<?
/*-------------------------------------------- Comments 
Version          : 
Purpose			 : This form will create fso wise fabric service work order
Functionality	 :	
JS Functions	 :
Created by		 :
Creation date 	 : 31-10-2023
Requirment Client: 
Requirment By    : 
Requirment type  : 
Requirment       : 
Affected page    : 
Affected Code    :              
DB Script        : 
Updated by 		 : 
Update date		 : 
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
echo load_html_head_contents("Fso wise fabric service Work Order", "../../", 1, 1,$unicode,'','');


?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

var colorrangeiddata = trim(return_global_ajax_value("", 'load_color_range_id_val', '', 'requires/fso_fabric_service_work_order_controller'));
colorrangeiddata = JSON.parse(colorrangeiddata);



function calculate_amount(row_num)
{
	var txt_woqnty=(document.getElementById('woqnty_'+row_num).value)*1;
	var txt_rate=(document.getElementById('rate_'+row_num).value)*1;
	var txt_amount=txt_woqnty*txt_rate;
	txt_amount= number_format(txt_amount,2,'.','');
	document.getElementById('amount_'+row_num).value=txt_amount;
}

function check_exchange_rate()
{
	var cbo_currercy=$('#cbo_currency').val();
	var txt_wo_date = $('#txt_wo_date').val();
	var cbo_company_name = $('#cbo_company_name').val();
	var response=return_global_ajax_value( cbo_currercy+"**"+txt_wo_date+"**"+cbo_company_name, 'check_conversion_rate', '', 'requires/fso_fabric_service_work_order_controller');
	var response=response.split("_");
	$('#txt_exchange_rate').val(response[1]);
}

function openmypage_wo_no(page_link,title)
{
	var cbo_company_name=$('#cbo_company_name').val();

	if(cbo_company_name ==0 || cbo_company_name==""){
		alert("Select Company First");
		return;
	}

	page_link+='&company_id='+cbo_company_name;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1090px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{	
		var menu_info=this.contentDoc.getElementById("selected_work_order").value; //alert(menu_info);
        if(menu_info.length){
            var data=menu_info.split('**');
            //console.log(data);
            reset_form('servicebookingknitting_1','','','','','');
			reset_table();
			$("#total_row").val(0);

            $("#update_id").val(data[0]);
            $("#txt_dyeing_wo_order_no").val(data[1]);
            $("#cbo_company_name").val(data[2]).attr('disabled', 'disabled');
            $("#cbo_currency").val(data[3]).attr('disabled', 'disabled');
            $("#txt_exchange_rate").val(data[4]).attr('disabled', 'disabled');
            $("#cbo_pay_mode").val(data[5]).attr('disabled', 'disabled');
            $("#txt_wo_date").val(data[6]).attr('disabled', 'disabled');
            $("#txt_delivery_date").val(data[7]).attr('disabled', 'disabled');

			$("#cbo_dyeing_source").val(data[8]).attr('disabled', 'disabled');
            $("#cbo_dyeing_comp").val(data[9]).attr('disabled', 'disabled');
            $("#txt_attention").val(data[10]);
            $("#txt_remark").val(data[11]);

            $("#cbo_wo_basis").val(data[14]).attr('disabled', 'disabled');
			$("#cbo_proccess_name").val(data[15]).attr('disabled', 'disabled');
			$("#cbo_import_source").val(data[16]).attr('disabled', 'disabled');
			
			$("#txt_tenor").val(data[19]);
			$("#cbo_ready_approval").val(data[20]);

			if (data[13]==1) $("#approved").text("Approved");
			else if (data[13]==3) $("#approved").text("Partial Approved");
			else $("#approved").text("");

            set_button_status(1, permission, 'fnc_dyeing_work_order',1);

			fnc_change_details($("#cbo_wo_basis").val());

			$("#txt_discount_qty").val(data[17]);
			$("#txt_upcharge_qty").val(data[18]);

			if($("#cbo_wo_basis").val()==1 || $("#cbo_wo_basis").val()==2)
			{
				var details_ids = trim(return_global_ajax_value(data[0], 'populate_details_data', '', 'requires/fso_fabric_service_work_order_controller'));
				if(details_ids!=""){
					rows=details_ids.split("**");
					//console.log(details_ids);
					for(var i=0;i<rows.length;i++){
						row=rows[i];
						//console.log(row);
						create_row(row);
					}
					const el = document.querySelector('#print_button');
					if (el.classList.contains("formbutton_disabled")) {
						el.classList.remove("formbutton_disabled");

					}
					//document.getElementById("print_button").disabled = false; 
				}
			}
			else
			{
				var details_data = trim(return_global_ajax_value_post(data[0]+'**'+data[17], 'populate_fso_wise_details_update', '', 'requires/fso_fabric_service_work_order_controller'));
				if(details_data!="")
				{
					$("#basis_details_container").html(details_data);
					const el = document.querySelector('#print_button');
					if (el.classList.contains("formbutton_disabled")) {
						el.classList.remove("formbutton_disabled");

					}
				}
			}

            calculate_total_qnty();
        }
		
	}
}


function select_item_pop()
{
	var cbo_company_name=$('#cbo_company_name').val();
	var cbo_wo_basis=$('#cbo_wo_basis').val();
	var cbo_dyeing_source=$('#cbo_dyeing_source').val();
	var cbo_dyeing_comp=$('#cbo_dyeing_comp').val();
	var process_id=$('#cbo_proccess_name').val();

	if (form_validation('cbo_company_name*cbo_proccess_name','Company name*Process name')==false)
	{
		return;
	}

	if(cbo_wo_basis==1)
	{
		var page_link='requires/fso_fabric_service_work_order_controller.php?action=issue_no_pop&company_id='+cbo_company_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Select Item Popup', 'width=1290px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];       
			var theemail=this.contentDoc.getElementById("selected_work_order");	
			//console.log(theemail.value);
				
			if (theemail.value!="")
			{
				//var row_details=trim(theemail.value).split('***');
				
				var details_data = trim(return_global_ajax_value_post(theemail.value, 'populate_group_details', '', 'requires/fso_fabric_service_work_order_controller'));
				var row_details=details_data.split("***");

				for(j=0;j<row_details.length;j++){
					create_row(row_details[j]);  
				}
				calculate_total_qnty();
			}
			
		}
		
	}
	else
	{
		var page_link='requires/fso_fabric_service_work_order_controller.php?action=select_item_pop&company_id='+cbo_company_name+'&cbo_wo_basis='+cbo_wo_basis+'&process_id='+process_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Select Item Popup', 'width=1220px,height=490px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];       
			var theemail=this.contentDoc.getElementById("selected_fso_no");	
			//console.log(theemail.value);
			if (theemail.value!="")
			{
				var row_details=trim(theemail.value).split('***');
				for(j=0;j<row_details.length;j++){
					create_row(row_details[j]);  
				}
				calculate_total_qnty();
				
			}
		}
	}
	
}

function fso_item_details_popup()
{
	var cbo_company_name=$('#cbo_company_name').val();
	var cbo_buyer_name=$('#cbo_buyer_name').val();

	var po_breakdown_id = $("#po_breakdown_id").val();
	if(cbo_company_name ==0 || cbo_company_name==""){
		alert("Select Company First");
		return;
	}

	var page_link='requires/fso_fabric_service_work_order_controller.php?action=fso_details_list_view&company_id='+cbo_company_name;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'FSO Item Popup', 'width=900px,height=450px,center=1,resize=1,scrolling=0', '../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];       
		var theemail=this.contentDoc.getElementById("selected_fso_details");	
		//console.log(theemail.value);
		if (theemail.value!="")
		{
			//theemail.value=$row[csf('fso_id')]."__".$row[csf('determination_id')]."__".$row[csf('gsm_weight')]."__".$row[csf('color_id')].',';
			var details_data = trim(return_global_ajax_value_post(theemail.value+'**'+po_breakdown_id, 'populate_fso_wise_details', '', 'requires/fso_fabric_service_work_order_controller'));
			if(details_data!="")
			{
				$("#basis_details_container").html(details_data);
			}
		}
	}
}

function issuenopop(){
	if (form_validation('cbo_company_name*cbo_fso_no*cbo_dyeing_source*cbo_dyeing_comp','Company name*Fabric Sales Order no*Dyeing source*Dyeing Company')==false)
	{
		return;
	}
	else
	{
		var cbo_company_name=$('#cbo_company_name').val();
		var cbo_buyer_name=$('#cbo_buyer_name').val();
		var cbo_dyeing_source=$('#cbo_dyeing_source').val();
		var cbo_dyeing_comp=$('#cbo_dyeing_comp').val();
		var po_breakdown_id=$('#po_breakdown_id').val();

		// var update_id=$('#update_id').val();
		var page_link='requires/fso_fabric_service_work_order_controller.php?action=issue_no_pop&company_id='+cbo_company_name+'&buyer_id='+cbo_buyer_name+'&source='+cbo_dyeing_source+'&dyeing_company_id='+cbo_dyeing_comp+'&po_breakdown_id='+po_breakdown_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Select Item Popup', 'width=900px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];       
			var theemail=this.contentDoc.getElementById("selected_work_order");	
			//console.log(theemail.value);
				
			if (theemail.value!="")
			{
				//var rows_data=trim(theemail.value).split('***');
				

				var details_data = trim(return_global_ajax_value_post(theemail.value+'**'+po_breakdown_id, 'populate_group_details', '', 'requires/fso_fabric_service_work_order_controller'));

				var row_details=details_data.split("***");
				for(j=0;j<row_details.length;j++){
					create_row(row_details[j]);  
				}
			}
		}
	}
}

function openprecess(id)
{
	var data=$('#proccessid_'+id).val();
	var page_link='requires/fso_fabric_service_work_order_controller.php?action=process_name_pop_up&row_id='+id+'&data='+data;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Select Item Popup', 'width=400px,height=400px,center=1,resize=1,scrolling=0','../../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];       
		var process_name=this.contentDoc.getElementById("hidden_process_name").value;	
		var process_id=this.contentDoc.getElementById("hidden_process_id").value;		
				
		if (process_id!="")
		{
			$('#proccessname_'+id).val(process_name);
			$('#proccessid_'+id).val(process_id);
			
		}
		
	}
}

function create_row(datas){
	//console.log(datas);
	var msg=0;
	var total_row=Number($("#total_row").val());
	var qty=0;
	var j=Number(total_row);
	var row_datas=datas.split("&&&&");
	var row_data=row_datas[0];
	var details_id='';
	var rate='';
	var amount='';
	var remark='';
	var process_id='';
	var process_name='';
	var wo_qty=0;
	var shade='';
	var proccess_loss='';
	var color_range=0;
	var disabled='';
	var cummu_wo_qty=0;
	var issue_qnty=0;
	var fso_qnty=0;

	if(row_datas.length>1)
	{
		//saved data
		var row_data_update=row_datas[1].split("__");
		details_id=row_data_update[0];
		rate=row_data_update[1];
		amount=row_data_update[2];
		remark=row_data_update[3];
		proccess_loss=row_data_update[4];
		process_id=row_data_update[5];
		process_name=row_data_update[6];
		color_range=row_data_update[7];
		check=row_data_update[8];
		if(check!="h"){
			disabled="disabled='disabled'";

		}
	}

	$("#scanning_tbl").find('tbody tr').each(function() {

		var checking_data = $(this).find('input[name="checking_data[]"]').val();
		if(trim(row_data) == trim(checking_data)){
			msg++;
			return;
		}
		
	});
	if(msg>0){
		alert("Details Already exists");
	}
	else
	{ 	
		var trColor="";
		if (j%2==0) trColor="#E9F3FF"; else trColor="#FFFFFF";
		
		j++;


		if($("#cbo_wo_basis").val()==1)
		{
			var data=row_data.split("__");
			var issue_date = data[0];
			var issue_number = data[1];
			var issue_id = data[2];
			var body_part_id = data[3];
			var body_part_name = data[4];
			var fabrications = data[5];
			var detarmination_id = data[6];
			var color_id = data[7];
			var color_names = data[8];
			var gsm = data[9];
			var dia_width = data[10];
			var style_ref_no = data[11];
			var buyer_name = data[12];
			var buyer_id = data[13];
			var wo_qnty = data[14];
			var fso_id = data[15];

			issue_qnty=wo_qnty; // if not saved then wo qty will issue qty
			if(row_datas.length>1)
			{
				//saved data
				//cummu_wo_qty=row_data_update[9]; // previous wo qty without this system id
				issue_qnty=row_data_update[10]; // issue qnty
			}

			var html="<tr id='tr_"+j+"' bgcolor="+trColor+" align='center' valign='middle' >";
			html+="<td>"+j+" <input type='hidden' name='sl[]' id='sl_"+j+"' value='"+j+"' /><input type='hidden' value='"+trim(row_data)+"' id='checking_data_"+j+"' name='checking_data[]'><input type='hidden' value='"+details_id+"' name='detailsId[]' id='detailsId_"+j+"'></td>";
			html+="<td>"+issue_date+" <input type='hidden' name='issuedate[]' id='issuedate_"+j+"' value='"+issue_date+"' />  <input type='hidden' name='issueid[]' id='issueid_"+j+"' value='"+issue_id+"' /> </td>";
			html+="<td>"+issue_number+" <input type='hidden' name='issueno[]' id='issueno_"+j+"' value='"+issue_number+"' /></td>";
			html+="<td>"+body_part_name+" <input type='hidden' name='bodypart[]' id='bodypart_"+j+"' value='"+body_part_id+"' /></td>";
			html+="<td>"+fabrications+" <input type='hidden' name='fabricdescription[]' id='fabricdescription_"+j+"' value='"+detarmination_id+"' /></td>";
			html+="<td>"+color_names+" <input type='hidden' name='dayingcolor[]' id='dayingcolor_"+j+"' value='"+color_id+"' "+disabled+" /></td>";

			html+="<td><input type='text' name='issueqty[]' disabled='disabled' style='width:67px' id='issueqty_"+j+"' class='text_boxes_numeric' onkeyup='calculate("+j+")' value='"+(Math.round(issue_qnty*100)/100)+"' /></td>";

			html+="<td><input type='text' name='woqnty[]' disabled='disabled' style='width:67px' id='woqnty_"+j+"'  class='text_boxes_numeric'  value='"+(Math.round(wo_qnty*100)/100)+"' onkeyup='calculate("+j+")'/></td>";
			html+="<td><input type='text' name='rate[]' "+disabled+" style='width:57px' id='rate_"+j+"' value='"+(Math.round(rate*100)/100)+"' class='text_boxes_numeric' onkeyup='calculate("+j+")'  /></td>";
			html+="<td><input type='text' name='amount[]' "+disabled+" style='width:67px' id='amount_"+j+"' value='"+(Math.round(amount*100)/100)+"' class='text_boxes_numeric' readonly /></td>";
			html+="<td><input type='text' style='width:105px' name='proccessname[]' id='proccessname_"+j+"'  placeholder='Browse' value='"+process_name+"' class='text_boxes' onfocus='openprecess("+j+")' "+disabled+" /> <input type='hidden' value='"+process_id+"' name='proccessid[]' "+disabled+"  id='proccessid_"+j+"'   /></td>";
			html+="<td><input type='text' class='text_boxes_numeric'style='width:55px' name='processloss[]' id='processloss_"+j+"' placeholder='Process loss'  value='"+proccess_loss+"' "+disabled+" /></td>";
			html+="<td><input type='text' name='remark[]' "+disabled+" style='width:105px' id='remark_"+j+"' value='"+remark+"' class='text_boxes'  /></td>";
			//html+="<td>"+colorrange+"</td>";

			var selected="";
			html+="<td><select class='combo_boxes' id='colorrange_"+j+"' name='colorrange[]' style='width:100px' >";
			html +='<option value="0">Select Range</option>';
			
			for (var k in colorrangeiddata)
			{
				if(k==color_range)
				{
					selected = "selected";
				}
				else
				{
					selected="";
				}
				html += "<option value='" +k+ "' " + selected + ">" +colorrangeiddata[k]+ "</option>";
			}
			html+="</select></td>";

			html+="<td>"+gsm+" <input type='hidden' name='gms[]' id='gms_"+j+"' value='"+gsm+"' /></td>";
			html+="<td>"+dia_width+" <input type='hidden' name='dia[]' id='dia_"+j+"' value='"+dia_width+"' /></td>";

			html+="<td>"+buyer_name+" <input type='hidden' name='buyername[]' id='buyername_"+j+"' value='"+buyer_id+"' /></td>";
			html+="<td>"+style_ref_no+" <input type='hidden' name='styleref[]' id='styleref_"+j+"' value='"+style_ref_no+"' /><input type='hidden' name='fsoid[]' id='fsoid_"+j+"' value='"+fso_id+"' /></td>";
			html+="</tr>";
		}
		else
		{
			var data=row_data.split("__");
			var fso_no = data[0];
			var sales_booking_no = data[1];
			var issue_id = data[2];
			var body_part_id = data[3];
			var body_part_name = data[4];
			var fabrications = data[5];
			var detarmination_id = data[6];
			var color_id = data[7];
			var color_names = data[8];
			var gsm = data[9];
			var dia_width = data[10];
			var style_ref_no = data[11];
			var buyer_name = data[12];
			var buyer_id = data[13];
			var fso_qnty = data[14];
			var fso_id = data[15];
			var colortypeid = data[16];
			var colortypename = data[17];
			var diatypeid = data[18];
			var diatypename = data[19];
			var colorrangeid = data[20];
			var colorrangename = data[21];
			var cummu_wo_qty = data[22];

			if(row_datas.length>1 && $("#cbo_wo_basis").val()==2)
			{
				//saved data for fso basis

				cummu_wo_qty=row_data_update[9]; // previous wo qty without this system id
				wo_qnty=row_data_update[10]; // saved wo qnty

				colorrangename=row_data_update[11];
				colortypeid=row_data_update[12];
				colortypename=row_data_update[13];
				diatypeid=row_data_update[14];
				diatypename=row_data_update[15];
			}

			var balance_fso_qnty = fso_qnty-cummu_wo_qty;
			balance_fso_qnty = number_format(balance_fso_qnty,2,'.','');
			
			var html="<tr id='tr_"+j+"' bgcolor="+trColor+" align='center' valign='middle' >";
			html+="<td>"+j+" <input type='hidden' name='sl[]' id='sl_"+j+"' value='"+j+"' /><input type='hidden' value='"+trim(row_data)+"' id='checking_data_"+j+"' name='checking_data[]'><input type='hidden' value='"+details_id+"' name='detailsId[]' id='detailsId_"+j+"'></td>";
			html+="<td>"+fso_no+" <input type='hidden' name='fsono[]' id='fsono_"+j+"' value='"+fso_no+"' /></td>";
			html+="<td>"+sales_booking_no+" <input type='hidden' name='bookingno[]' id='bookingno_"+j+"' value='"+sales_booking_no+"' /></td>";
			html+="<td>"+body_part_name+" <input type='hidden' name='bodypart[]' id='bodypart_"+j+"' value='"+body_part_id+"' /></td>";
			html+="<td>"+fabrications+" <input type='hidden' name='fabricdescription[]' id='fabricdescription_"+j+"' value='"+detarmination_id+"' /></td>";
			html+="<td>"+color_names+" <input type='hidden' name='dayingcolor[]' id='dayingcolor_"+j+"' value='"+color_id+"' "+disabled+" /></td>";

			html+="<td><input type='text' name='issueqty[]' disabled='disabled' style='width:67px' id='issueqty_"+j+"' class='text_boxes_numeric' onkeyup='calculate("+j+")' value='"+(Math.round(fso_qnty*100)/100)+"' /></td>";
			html+="<td><input type='text' name='prewoqty[]' disabled='disabled' style='width:67px' id='prewoqty_"+j+"' class='text_boxes_numeric' onkeyup='calculate("+j+")' value='"+(Math.round(cummu_wo_qty*100)/100)+"' /></td>";

			if(row_datas.length>1)
			{
				//Saved Data
				html+="<td><input type='text' name='woqnty[]' style='width:67px' id='woqnty_"+j+"'  class='text_boxes_numeric' onkeyup='calculate("+j+")' value='"+(Math.round(wo_qnty*100)/100)+"' placeholder='"+(Math.round(balance_fso_qnty*100)/100)+"'/></td>";
			}
			else
			{
				//Unsaved Data
				html+="<td><input type='text' name='woqnty[]' style='width:67px' id='woqnty_"+j+"'  class='text_boxes_numeric' onkeyup='calculate("+j+")' value='"+(Math.round(balance_fso_qnty*100)/100)+"' placeholder='"+(Math.round(balance_fso_qnty*100)/100)+"'/></td>";
			}

			html+="<td><input type='text' name='rate[]' "+disabled+" style='width:57px' id='rate_"+j+"' value='"+(Math.round(rate*100)/100)+"' class='text_boxes_numeric' onkeyup='calculate("+j+")'  /></td>";
			html+="<td><input type='text' name='amount[]' "+disabled+" style='width:67px' id='amount_"+j+"' value='"+(Math.round(amount*100)/100)+"' class='text_boxes_numeric' readonly /></td>";
			html+="<td><input type='text' style='width:105px' name='proccessname[]' id='proccessname_"+j+"'  placeholder='Browse' value='"+process_name+"' class='text_boxes' onfocus='openprecess("+j+")' "+disabled+" /> <input type='hidden' value='"+process_id+"' name='proccessid[]' "+disabled+"  id='proccessid_"+j+"'   /></td>";
			html+="<td><input type='text' class='text_boxes_numeric'style='width:55px' name='processloss[]' id='processloss_"+j+"' placeholder='Process loss'  value='"+proccess_loss+"' "+disabled+" /></td>";
			html+="<td><input type='text' name='remark[]' "+disabled+" style='width:105px' id='remark_"+j+"' value='"+remark+"' class='text_boxes'  /></td>";
			//html+="<td>"+colorrange+"</td>";

			html+="<td>"+colorrangename+" <input type='hidden' name='colorrange[]' id='colorrange_"+j+"' value='"+colorrangeid+"' /></td>";
			html+="<td>"+gsm+" <input type='hidden' name='gms[]' id='gms_"+j+"' value='"+gsm+"' /></td>";
			html+="<td>"+dia_width+" <input type='hidden' name='dia[]' id='dia_"+j+"' value='"+dia_width+"' /></td>";

			html+="<td>"+colortypename+" <input type='hidden' name='colortype[]' id='colortype_"+j+"' value='"+colortypeid+"' /></td>";
			html+="<td>"+diatypename+" <input type='hidden' name='diatype[]' id='diatypeid_"+j+"' value='"+diatypeid+"' /></td>";


			html+="<td>"+buyer_name+" <input type='hidden' name='buyername[]' id='buyername_"+j+"' value='"+buyer_id+"' /></td>";
			html+="<td>"+style_ref_no+" <input type='hidden' name='styleref[]' id='styleref_"+j+"' value='"+style_ref_no+"' /><input type='hidden' name='fsoid[]' id='fsoid_"+j+"' value='"+fso_id+"' /></td>";
			html+="</tr>";
		}

		//console.log(html);
		$('#scanning_tbl tbody').append(html);
		//calculate_total_qnty();
		$("#total_row").val(j);
		//alert(html);
	}
}

function calculate_total_qnty(){
	var tot_issue=0;  var tot_cummu_wo=0; var tot_wo=0; var tot_amount=0;var prewoqty =0;
	$("#scanning_tbl").find('tbody tr').each(function() {
        var issueqty = $(this).find('input[name="issueqty[]"]').val();
        
		if ($(this).find('input[name="prewoqty[]"]').length) {
			prewoqty = $(this).find('input[name="prewoqty[]"]').val();
		}

        var woqnty = $(this).find('input[name="woqnty[]"]').val();
        var amount = $(this).find('input[name="amount[]"]').val();
        tot_issue+=issueqty*1;
        tot_cummu_wo+=prewoqty*1;
        tot_wo+=woqnty*1;
        tot_amount+=amount*1;
    });
	
	$("#txt_issue_qty_sum").text(Math.round(Number(tot_issue)*100)/100);
	//$("#txt_pre_wo_qty_sum").text(Math.round(Number(tot_cummu_wo)*100)/100);
	$("#txt_wo_qty_sum").text(Math.round(Number(tot_wo)*100)/100);
	$("#txt_wo_amount_sum").text(Math.round(Number(tot_amount)*100)/100);

    if(document.getElementById('txt_pre_wo_qty_sum') !== null)
	{
		$("#txt_pre_wo_qty_sum").text(Math.round(Number(tot_cummu_wo)*100)/100);
    }

	var txt_discount_qty=$("#txt_discount_qty").val()*1;
	var txt_upcharge_qty=$("#txt_upcharge_qty").val()*1;

	var tot_net_wo_sum = tot_amount-txt_discount_qty+txt_upcharge_qty;

	$("#txt_net_wo_amount_sum").text(Math.round(Number(tot_net_wo_sum)*100)/100);
	
	
}

function fn_deleteRow(rid)
{
	var dtls_id=$("#detailsId_"+rid).val();
	var bill_no = trim(return_global_ajax_value(dtls_id, 'check_delete', '', 'requires/fso_fabric_service_work_order_controller'));
	if(bill_no!="")
	{
		alert("Remove not allowed . Bill no: "+bill_no);
		return;
	}

	if($("#tr_" + rid).length)
	{
		 $("#tr_" + rid).remove();
	}
	else
	{
		alert('Row not exists');
	}
	var qty=0;
	$("#scanning_tbl").find('tbody tr').each(function() {
        var programqnty = Number($(this).find('input[name="programqnty[]"]').val());
        qty+=programqnty;
    });
	$("#totalqnty").val(Math.round(Number(qty)*100)/100);
	$("#total_qnty").text(Math.round(Number(qty)*100)/100);

              
}



function fnc_dyeing_work_order(operation)
{
	if (form_validation('cbo_company_name*cbo_proccess_name*cbo_currency*txt_delivery_date*cbo_pay_mode*cbo_dyeing_comp*cbo_currency','Company Name*Process Name*Currency*Delivery Date*Pay Mode*Dyeing Company*Currency')==false)
	{
		return;
	}
 	var j = 0; var k=0;
    var dataString = '';
	var cbo_wo_basis=$("#cbo_wo_basis").val();
	if(cbo_wo_basis==2)
	{
		$("#scanning_tbl").find('tbody tr').each(function () 
		{
			var detailsId = $(this).find('input[name="detailsId[]"]').val();

			var bodypart = $(this).find('input[name="bodypart[]"]').val();
			var fabricdescription = $(this).find('input[name="fabricdescription[]"]').val();
			var gms = $(this).find('input[name="gms[]"]').val();
			var dia = $(this).find('input[name="dia[]"]').val();
			var colortype = $(this).find('input[name="colortype[]"]').val();
			var diatype = $(this).find('input[name="diatype[]"]').val();
			var dayingcolor = $(this).find('input[name="dayingcolor[]"]').val();
			var colorrange = $(this).find('input[name="colorrange[]"]').val();
			var proccessid = $(this).find('input[name="proccessid[]"]').val();

			var processloss = $(this).find('input[name="processloss[]"]').val();
			var woqnty = $(this).find('input[name="woqnty[]"]').val();
			var rate = $(this).find('input[name="rate[]"]').val();
			var amount = $(this).find('input[name="amount[]"]').val();
			var remark = $(this).find('input[name="remark[]"]').val();
			var fsoid = $(this).find('input[name="fsoid[]"]').val();

			if(Number(rate)==0 || Number(amount)==0 || Number(woqnty)==0){
				//alert('Quantity, Rate and Amount can not be empty');
				k++;
				return;
			}
			try 
			{
				j++;

				dataString +='&detailsId_' + j + '=' + detailsId + '&bodypart_' + j + '=' + bodypart + '&fabricdescription_' + j + '=' + fabricdescription + '&gms_' + j + '=' + gms + '&dia_' + j + '=' + dia + '&dayingcolor_' + j + '=' + dayingcolor+ '&colorrange_' + j + '=' + colorrange  + '&proccessid_' + j + '=' + proccessid + '&woqnty_' + j + '=' + woqnty + '&rate_' + j + '=' + rate + '&amount_' + j + '=' + amount + '&remark_' + j + '=' + remark+ '&processloss_' + j + '=' + processloss+ '&colortype_' + j + '=' + colortype+ '&diatype_' + j + '=' + diatype+ '&fsoid_' + j + '=' + fsoid;
			}
			catch (e) {
				//got error no operation
				alert(e);
				return;
			}

		});
	}
	else
	{
		$("#scanning_tbl").find('tbody tr').each(function () 
		{
			var detailsId = $(this).find('input[name="detailsId[]"]').val();
			var issuedate = $(this).find('input[name="issuedate[]"]').val();
			var issueno = $(this).find('input[name="issueno[]"]').val();
			var bodypart = $(this).find('input[name="bodypart[]"]').val();
			var fabricdescription = $(this).find('input[name="fabricdescription[]"]').val();
			var gms = $(this).find('input[name="gms[]"]').val();
			var dia = $(this).find('input[name="dia[]"]').val();
			var dayingcolor = $(this).find('input[name="dayingcolor[]"]').val();
			var colorrange = $(this).find('select[name="colorrange[]"]').val();
			var proccessid = $(this).find('input[name="proccessid[]"]').val();
			var processloss = $(this).find('input[name="processloss[]"]').val();
			var issueqty = $(this).find('input[name="issueqty[]"]').val();
			var woqnty = $(this).find('input[name="woqnty[]"]').val();
			var rate = $(this).find('input[name="rate[]"]').val();
			var amount = $(this).find('input[name="amount[]"]').val();
			var remark = $(this).find('input[name="remark[]"]').val();
			var issueid = $(this).find('input[name="issueid[]"]').val();
			var fsoid = $(this).find('input[name="fsoid[]"]').val();
			if(Number(rate)==0 || Number(amount)==0){
				//alert('Rate and Amount can not be empty');
				k++;
				return;
			}
			try 
			{
				j++;

				dataString +='&detailsId_' + j + '=' + detailsId + '&issueno_' + j + '=' + issueno + '&bodypart_' + j + '=' + bodypart + '&fabricdescription_' + j + '=' + fabricdescription + '&gms_' + j + '=' + gms + '&dia_' + j + '=' + dia + '&dayingcolor_' + j + '=' + dayingcolor+ '&colorrange_' + j + '=' + colorrange  + '&proccessid_' + j + '=' + proccessid  + '&processloss_' + j + '=' + processloss + '&issueqty_' + j + '=' + issueqty+ '&woqnty_' + j + '=' + woqnty + '&rate_' + j + '=' + rate + '&amount_' + j + '=' + amount + '&remark_' + j + '=' + remark+'&issuedate_'+j+'='+issuedate+'&issueid_'+j+'='+issueid+'&fsoid_'+j+'='+fsoid;
			}
			catch (e) {
				//got error no operation
				alert(e);
				return;
			}
		});
	}

	if (k > 0) {
		alert('Quantity, Rate and Amount can not be empty');
        return;
    }

    if (j < 1) {
        alert('No data');
        return;
    }
    var data = "action=save_update_delete_details&operation=" + operation + '&tot_row=' + j + get_submitted_data_string('update_id*txt_dyeing_wo_order_no*cbo_company_name*cbo_fso_no*cbo_import_source*txt_tenor*cbo_currency*cbo_ready_approval*txt_exchange_rate*txt_wo_date*txt_delivery_date*cbo_dyeing_source*cbo_pay_mode*cbo_dyeing_comp*txt_attention*txt_remark*cbo_wo_basis*cbo_proccess_name*txt_discount_qty*txt_upcharge_qty', "../../") + dataString;
    freeze_window(operation);
	http.open("POST","requires/fso_fabric_service_work_order_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_knitting_work_order_details_reponse;
	
}
function fnc_knitting_work_order_details_reponse()
{
	if(http.readyState == 4) 
	{
		var response=trim(http.responseText).split('**');

		if(response[0]==13)
		{
			alert(response[1]);
			release_freezing();
			return;
		}		
		 if(response[0]==111)
		 {
		 	alert('Delete Not allow . Bill already generated ');
		 }else if(response[0]==112)
		 {
		 	alert('Delete Not allow . Bill already generated ');
		 }
		 else{
 			 show_msg(trim(response[0]));
 			 if(response[0]==0 || response[0]==1)
 			 {
 				$("#txt_dyeing_wo_order_no").val(response[2]);
 				$("#update_id").val(response[1]);
				
				if($("#cbo_wo_basis").val()==1 || $("#cbo_wo_basis").val()==2)
				{
					reset_table();
					$("#total_row").val(0);
					var details_ids = trim(return_global_ajax_value(response[1], 'populate_details_data', '', 'requires/fso_fabric_service_work_order_controller'));
					if(details_ids!=""){
						rows=details_ids.split("**");
						//console.log(details_ids);
						for(var i=0;i<rows.length;i++){
							row=rows[i];
							//console.log(row);
							create_row(row);
						}
						const el = document.querySelector('#print_button');
						if (el.classList.contains("formbutton_disabled")) {
							el.classList.remove("formbutton_disabled");

						}
					}
				}
				else
				{
					var details_data = trim(return_global_ajax_value_post(response[1]+'**'+$("#po_breakdown_id").val(), 'populate_fso_wise_details_update', '', 'requires/fso_fabric_service_work_order_controller'));
					if(details_data!="")
					{
						$("#basis_details_container").html(details_data);
						const el = document.querySelector('#print_button');
						if (el.classList.contains("formbutton_disabled")) {
							el.classList.remove("formbutton_disabled");

						}
					}
					
				}

 				
 			 	set_button_status(1, permission, 'fnc_dyeing_work_order',1);
 			 }
 			 else if(response[0]==2)
 			 {
 				reset_form('servicebookingknitting_1','','','','','');
 				reset_table();
 			 	set_button_status(0, permission, 'fnc_dyeing_work_order',1);
 			 }else{
 			 	
 			 }

		 }
		  release_freezing();
		 
	}
}
function print_knitting_work_order(){
	if (form_validation('update_id*cbo_company_name','Work Order* Company Name')==false)
	{
		return;
	}
	var r=confirm("Press \"OK\" to open with Rate & Amount column\nPress \"Cancel\" to open without Rate & Amount column");
	if (r==true)
	{
		show_val_column="1";
	}
	else
	{
		show_val_column="0";
	}
	print_report( $('#update_id').val()+"**"+$('#cbo_company_name').val()+"**"+show_val_column, "print_knitting_work_order", "requires/fso_fabric_service_work_order_controller" ) 
		return;

}


function calculate(i)
{
	var cbo_wo_basis = $("#cbo_wo_basis").val();
	var rate = document.getElementById('rate_' + i).value;
	var copy_rate=$("#copy_rate").is(":checked");

	if(copy_rate )
	{
		$("#scanning_tbl").find('tbody tr').each(function () {
			var rateIdArr = $(this).find('input[name="rate[]"]').attr('id').split('_');
			var row_num = rateIdArr[1];
			if(row_num >= i)
			{
				$('#rate_' + row_num).val(rate);
			}
		});
	}

	
	if(cbo_wo_basis==2)
	{
		var balance_qnty= $("#issueqty_"+i).val()*1 - $("#prewoqty_"+i).val()*1;
		if( balance_qnty < $("#woqnty_"+i).val()*1)
		{
			$("#woqnty_"+i).val(balance_qnty);
		}
	}

	var rate=[];
	var j=0;
	$("input[name='rate[]']").map( function(key){
       rate[j]=Number($(this).val());
       j=j+1;
    });

    var issueqty=[];
    j=0;
    $("input[name='woqnty[]']").map( function(key){
       issueqty[j]=Number($(this).val());
       j=j+1;
    });
    j=0;
    $("input[name='amount[]']").map( function(key){
       var num=rate[j]*issueqty[j];
       $(this).val(Math.round(Number(num)*100)/100);
       j=j+1;
    });

	calculate_total_qnty();
}
function reset_table(){
	$('#scanning_tbl tbody tr').remove();
	$("#totalqnty").val(0);
	$("#total_qnty").text(0);
}

function generate_trim_report_reponse()
{
	if(http.readyState == 4) 
	{
		var file_data=http.responseText.split('****');
		$('#pdf_file_name').html(file_data[1]);
		$('#data_panel2').html(file_data[0] );
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel2').innerHTML+'</body</html>');
		d.close();
	}
}
function empty_fso_no(){
	$("#cbo_fso_no").val("");
}

function fnc_change_details(basis)
{
	if(basis==1)
	{
		//$('#cbo_fso_basis').prop("disabled",true);
		//$('#issue_no_pop').prop("disabled",false);
		$("#basis_details_container").html('<table  width="1700" cellspacing="2" cellpadding="0" border="1" rules="all" class="rpt_table" id="scanning_tbl">'+
                '<thead>'+
					'<tr>'+
						'<th width="30">SL No</th>'+
						'<th width="70">Issue Date</th>'+
						'<th width="100">Issue No</th>'+
						'<th width="80">Body Part</th>'+
	                    '<th width="200">Fabric Construction & Composition</th>'+
						'<th width="100">Fabric Color</th>'+
						'<th width="80">Issue Qty.</th>'+
						'<th width="80">WO Qty.</th>'+
	                    '<th width="70">Rate<input type="checkbox" id="copy_rate" name="copy_rate"></th>'+
	                    '<th width="80">Amount</th>'+
						'<th width="120">Process Name</th>'+
						'<th width="70">Process Loss %</th>'+
						'<th width="100">Remarks</th>'+
						'<th width="100">Color Range</th>'+
	                    '<th width="50">Fab. GSM</th>'+
	                    '<th width="50">Fab. DIA</th>'+
	                    '<th width="100">Buyer Name</th>'+
	                    '<th width="100">Style Ref</th>'+
					'</tr>'+
				'</thead>'+
			'<tbody>'+
			'</tbody>'+
			'<tfoot>'+
			'<tr align="center" valign="middle" >'+
				'<td colspan="15">'+
					'<input type="hidden" name="total_row" id="total_row" value="0">'+
						'</td>'+
					'<td  align="left" id="total_issue_qnty">'+
					'<input type="hidden" name="totalIssueQnty" id="totalIssueQnty" value="0"  />'+
						'</td>'+
					'<td  align="left" id="total_wo_qnty">'+
					'<input type="hidden" name="totalWoQnty" id="totalWoQnty" value="0"  />'+
						'</td>'+
					'<td></td>'+
					'<td  align="left" id="total_rate">'+
					'<input type="hidden" name="totalRate" id="totalRate" value="0"  />'+
						'</td>'+
					'</tr>'+
					'<tr>'+
						'<th colspan="6" align="right">Total</th>'+
						'<th align="right" id="txt_issue_qty_sum">&nbsp;</th>'+
						'<th align="right" id="txt_wo_qty_sum">&nbsp;</th>'+
						'<th align="right">&nbsp;</th>'+
						'<th align="right" id="txt_wo_amount_sum">&nbsp;</th>'+
						'<th colspan="8" align="right">&nbsp;</th>'+
					'</tr>'+
					'<tr>'+
						'<th colspan="9" align="right">Discount &nbsp;</th>'+
						'<th align="right"><input type="text" name="txt_discount_qty" id="txt_discount_qty" class="text_boxes_numeric" style="width:90px;" onkeyup="calculate_total_qnty()"/></th>'+
						'<th colspan="8" align="right">&nbsp;</th>'+
					'</tr>'+
					'<tr>'+
						'<th colspan="9" align="right">Upcharge &nbsp;</th>'+
						'<th align="right"><input type="text" name="txt_upcharge_qty" id="txt_upcharge_qty" class="text_boxes_numeric" style="width:90px;" onkeyup="calculate_total_qnty()"/></th>'+
						'<th colspan="8" align="right">&nbsp;</th>'+
					'</tr>'+
					'<tr>'+
						'<th colspan="9" align="right">Net WO Amount &nbsp;</th>'+
						'<th align="right" id="txt_net_wo_amount_sum">&nbsp;</th>'+
						'<th colspan="8" align="right">&nbsp;</th>'+
					'</tr>'+
					
				'</tfoot>'+
		'</table>');
	}
	else
	{
		$("#basis_details_container").html('<table  width="1700" cellspacing="2" cellpadding="0" border="1" rules="all" class="rpt_table" id="scanning_tbl">'+
                '<thead>'+
					'<tr>'+
						'<th width="30">SL No</th>'+
						'<th width="70">FSO NO</th>'+
						'<th width="100">Booking No</th>'+
						'<th width="80">Body Part</th>'+
	                    '<th width="200">Fabric Construction & Composition</th>'+
						'<th width="100">Fabric Color</th>'+
						'<th width="80">FSO Qty.</th>'+
						'<th width="80">Cummu. WO Qty</th>'+
						'<th width="80">WO Qty.</th>'+
	                    '<th width="70">Rate<input type="checkbox" id="copy_rate" name="copy_rate"></th>'+
	                    '<th width="80">Amount</th>'+
						'<th width="120">Process Name</th>'+
						'<th width="70">Process Loss %</th>'+
						'<th width="100">Remarks</th>'+
						'<th width="100">Color Range</th>'+
	                    '<th width="50">Fab. GSM</th>'+
	                    '<th width="50">Fab. DIA</th>'+
						'<th width="100">Color Type</th>'+
						'<th width="100">Dia Type</th>'+
	                    '<th width="100">Buyer Name</th>'+
	                    '<th width="100">Style Ref</th>'+
					'</tr>'+
				'</thead>'+
			'<tbody>'+
			'</tbody>'+
			'<tfoot>'+
			'<tr align="center" valign="middle" >'+
				'<td colspan="15">'+
					'<input type="hidden" name="total_row" id="total_row" value="0">'+
						'</td>'+
					'<td  align="left" id="total_issue_qnty">'+
					'<input type="hidden" name="totalIssueQnty" id="totalIssueQnty" value="0"  />'+
						'</td>'+
					'<td  align="left" id="total_wo_qnty">'+
					'<input type="hidden" name="totalWoQnty" id="totalWoQnty" value="0"  />'+
						'</td>'+
					'<td></td>'+
					'<td  align="left" id="total_rate">'+
					'<input type="hidden" name="totalRate" id="totalRate" value="0"  />'+
						'</td>'+
					'</tr>'+
					'<tr>'+
						'<th colspan="6" align="right">Total</th>'+
						'<th align="right" id="txt_issue_qty_sum">&nbsp;</th>'+
						'<th align="right" id="txt_pre_wo_qty_sum">&nbsp;</th>'+
						'<th align="right" id="txt_wo_qty_sum">&nbsp;</th>'+
						'<th align="right">&nbsp;</th>'+
						'<th align="right" id="txt_wo_amount_sum">&nbsp;</th>'+
						'<th colspan="10" align="right">&nbsp;</th>'+
					'</tr>'+
					'<tr>'+
						'<th colspan="10" align="right">Discount &nbsp;</th>'+
						'<th align="right"><input type="text" name="txt_discount_qty" id="txt_discount_qty" class="text_boxes_numeric" style="width:90px;" onkeyup="calculate_total_qnty()"/></th>'+
						'<th colspan="10" align="right">&nbsp;</th>'+
					'</tr>'+
					'<tr>'+
						'<th colspan="10" align="right">Upcharge &nbsp;</th>'+
						'<th align="right"><input type="text" name="txt_upcharge_qty" id="txt_upcharge_qty" class="text_boxes_numeric" style="width:90px;" onkeyup="calculate_total_qnty()"/></th>'+
						'<th colspan="10" align="right">&nbsp;</th>'+
					'</tr>'+
					'<tr>'+
						'<th colspan="10" align="right">Net WO Amount &nbsp;</th>'+
						'<th align="right" id="txt_net_wo_amount_sum">&nbsp;</th>'+
						'<th colspan="10" align="right">&nbsp;</th>'+
					'</tr>'+
					
				'</tfoot>'+
		'</table>');
	}
}

function fnc_rate_chk(sl)
{
	var rate= $('#rate_'+sl).val()*1;
	var hidefsorate= $('#hidefsorate_'+sl).val()*1;

	if(hidefsorate > 0)
	{
		if(hidefsorate<rate)
		{
			$('#rate_'+sl).val(hidefsorate);
			$('#rate_'+sl).focus();
			alert("Rate can not be greater than Fso rate.");
		}
	}
}

</script>

</head>

<body onLoad="set_hotkey(); check_exchange_rate();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="servicebooking_1"  autocomplete="off" id="servicebooking_1">
        <fieldset style="width:950px;">
        <legend>Dyeing Order</legend>
            <table  width="900" cellspacing="2" cellpadding="0" border="0" style="">
                <tr>
                    <td width="120" align="right" class="must_entry_caption" colspan="4">WO No </td>
                    <td width="170" colspan="3">
                    	<input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_wo_no('requires/fso_fabric_service_work_order_controller.php?action=dyeing_work_order_popup','Dyeing Order Search')" readonly placeholder="Double Click for Dyeing WO" name="txt_dyeing_wo_order_no" id="txt_dyeing_wo_order_no"/>
                    	<input type="hidden" name="update_id" id="update_id">
                    </td>                       
                    <td></td>
                </tr>
                <tr>
                    <td style="display:inline-block; width:80px;" class="must_entry_caption">WO Company</td>
                    <td>
						<? 
                            echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name", "id,company_name",1, "-- Select Company --", $selected, "check_exchange_rate();fnc_change_details(document.getElementById('cbo_wo_basis').value);","","" );
                        ?>	  
                    </td>

					<td style="display:inline-block; width:80px;" class="must_entry_caption">Process Name</td>
					<td>
						<?
							//"33,68,127,155,156,166,167,168,196,243,338,416,441,442,475,280,287"

							echo create_drop_down( "cbo_proccess_name", 122, $conversion_cost_head_array,"", 1, "-- Select --", "", "fnc_change_details(document.getElementById('cbo_wo_basis').value);","","33,68,88,94,100,127,155,156,159,161,166,167,168,172,196,199,243,268,277,280,287,288,318,322,338,412,416,419,424,427,441,442,472,475,476,478,492" );
						?>
					</td>

					<td style="display:inline-block; width:80px;" class="must_entry_caption">Service Source</td>
                    <td>
                    	
                    	<?
							echo create_drop_down( "cbo_dyeing_source", 122, $knitting_source, "", 1, "-- Select --", 3, "load_drop_down( 'requires/fso_fabric_service_work_order_controller',this.value+'**'+$('#cbo_company_name').val(),'load_drop_down_knitting_com','dyeing_company_td' );",1,"1,3" );

							?>
                    </td>
                    <td style="display:inline-block; width:90px;" class="must_entry_caption">Service Company</td>
                    <td id="dyeing_company_td">
                    	<? 
                            echo create_drop_down( "cbo_dyeing_comp", 122, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name",1, "-- Select Company --", $selected, "","","" );
                        ?>	 
                    </td>
					<? $wo_basis_arr=array(1=>"Challan no",2=>"FSO Wise");?>
                    
					<td style="display:inline-block; width:70px;" class="must_entry_caption"> Basis</td> 
					<td>
                    	<? echo create_drop_down( "cbo_wo_basis", 100, $wo_basis_arr,"", 0, "-- Select --", "", "fnc_change_details(this.value)","" ); ?> 
                    </td>
                    
                </tr>
                <tr>
					<td class="must_entry_caption">FSO/Challan No.</td>
					<td>
						<input type="text" class="text_boxes" name="cbo_fso_no" placeholder="Browse FSO No" onDblClick="select_item_pop()" id="cbo_fso_no" style="width: 110px;" readonly>
						<input type="hidden" class="text_boxes" name="cbo_fso_basis" placeholder="Browse FSO No" onDblClick="fso_item_details_popup()" id="cbo_fso_basis" style="width: 110px;">
                		<input type="hidden" class="text_boxes" name="issue_no_pop" placeholder="Browse Issue no" onDblClick="issuenopop();" id="issue_no_pop" style="width: 85px;">
						
                	</td>
					

					<td width="130" class="must_entry_caption">Wo date</td>
                    <td width="110">
                    	<input class="datepicker" type="text" style="width:110px" name="txt_wo_date" id="txt_wo_date" onChange="check_exchange_rate();" value="<? echo date("d-m-Y")?>"  />	
                    </td>
                	<td width="130" class="must_entry_caption">Delivery Date</td>
                    <td>
                    	<input class="datepicker" type="text" style="width:110px" name="txt_delivery_date" id="txt_delivery_date" value='<?php echo date("d-m-Y");?>' />	
                    </td>
					<td class="must_entry_caption">Currency</td>
                    <td>
						<? 
                        	echo create_drop_down( "cbo_currency", 122, $currency,"", 1, "-- Select --", 1, "check_exchange_rate()",0 );		
                        ?>	
                    </td>
					
                    <td>Exchange Rate</td>
                    <td>
                    	<input style="width:90px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate" placeholder="Exchange Rate"  readonly />  
                    </td>
					
                </tr>
                <tr>
					<td class="must_entry_caption">Pay Mode</td>
                    <td>
                    	<? echo create_drop_down( "cbo_pay_mode", 122, $pay_mode,"", 1, "-- Select --", "", "","" ); ?> 
                    </td>
                    
                    <td>Ready To Approved</td>
					<? $ready_to_approval=array(1=>"Yes",2=>"No");?>
                    <td>
                    	<? echo create_drop_down( "cbo_ready_approval", 122, $ready_to_approval,"", 1, "-- Select Ready Approval --", "", "","" ); ?> 
                    </td>

					<td>Import Source</td>
                    <td>
						<? 
                        	echo create_drop_down( "cbo_import_source", 122, $source,"", 1, "-- Select --", 1, "",0 );		
                        ?>	
                    </td>
					<td>Tenor</td>
                    <td>
						<input type="text" class="text_boxes_numeric" name="txt_tenor" style="width: 90px;" id="txt_tenor" placeholder="In days">
                    </td>
                   
                </tr>
                <tr>
                   
                    <td>Attention</td>   
                    <td colspan="10">
                    	<input class="text_boxes" type="text" style="width:97%;"  name="txt_attention" id="txt_attention"/>
                    	<input type="hidden" class="image_uploader" style="width:162px" value="Lab DIP No" onClick="openmypage( 'requires/fso_fabric_service_work_order_controller.php?action=lapdip_no_popup', 'Lapdip No', 'lapdip')">
                    </td>
                </tr>
                <tr>
                	<td> Remark</td>
                	<td colspan="10"><input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width: 97%;"></td>
                </tr>
				<tr>
					<td colspan="5"></td>
					<td>
						<? 
                        include("../../terms_condition/terms_condition.php");
                        terms_condition(696,'txt_dyeing_wo_order_no','../../','txt_dyeing_wo_order_no');
                        ?>                    
                    </td>
                </tr>
            </table>
			<div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
        </fieldset>
    </form>
    <br/>
    <form name="servicebookingknitting_1"  autocomplete="off" id="servicebookingknitting_1">   
        <fieldset style="width:1710px;">
        <legend>Details</legend>
		<div id="basis_details_container">
            <table  width="1700" cellspacing="2" cellpadding="0" border="1" rules="all" class="rpt_table" id="scanning_tbl">
                <thead>
	                <tr>
	                    <th width="30">SL No</th>
	                    <th width="70">Issue Date</th>
	                    <th width="100">Issue No</th>
	                    <th width="80">Body Part</th>
	                    <th width="200">Fabric Construction & Composition</th>
						<th width="100">Fabric Color</th>
						<th width="80">Issue Qty.</th>
	                    <th width="80">WO Qty.</th>
	                    <th width="70">Rate<input type="checkbox" id="copy_rate" name="copy_rate"></th>
	                    <th width="80">Amount</th>
						<th width="120">Process Name</th>
						<th width="70">Process Loss %</th>
						<th width="100">Remarks</th>
						<th width="100">Color Range</th>
	                    <th width="50">Fab. GSM</th>
	                    <th width="50">Fab. DIA</th>
	                    <th width="100">Buyer Name</th>
	                    <th width="100">Style Ref</th>
	                </tr>
	            </thead>
	            <tbody>
	            	
	            </tbody>
	            <tfoot>
	            	<tr align='center' valign='middle' >

						<td colspan='15'>
							<input type="hidden" name="total_row" id="total_row" value="0">
						</td>
						<td  align='left' id="total_issue_qnty">
							<input type='hidden' name='totalIssueQnty' id='totalIssueQnty' value='0'  />
						</td>
						<td  align='left' id="total_wo_qnty">
							<input type='hidden' name='totalWoQnty' id='totalWoQnty' value='0'  />
						</td>
						<td></td>
						<td  align='left' id="total_rate">
							<input type='hidden' name='totalRate' id='totalRate' value='0'  />
						</td>
					</tr>
					<tr>
						<th colspan='6' align="right">Total</th>
						<th align="right" id="txt_issue_qty_sum">&nbsp;</th>
						<th align="right" id="txt_wo_qty_sum">&nbsp;</th>
						<th align="right">&nbsp;</th>
						<th align="right" id="txt_wo_amount_sum">&nbsp;</th>
						<th colspan='8' align="right">&nbsp;</th>
					</tr>
					<tr>
						<th colspan='9' align="right">Discount &nbsp;</th>
						<th align="right"><input type='text' name='txt_discount_qty' id='txt_discount_qty' value='' class="text_boxes_numeric" style="width:90px;" onkeyup="calculate_total_qnty()"/></th>
						<th colspan='8' align="right">&nbsp;</th>
					</tr>
					<tr>
						<th colspan='9' align="right">Upcharge &nbsp;</th>
						<th align="right"><input type='text' name='txt_upcharge_qty' id='txt_upcharge_qty' value='' class="text_boxes_numeric" style="width:90px;" onkeyup="calculate_total_qnty()"/></th>
						<th colspan='8' align="right">&nbsp;</th>
					</tr>
					<tr>
						<th colspan='9' align="right">Net WO Amount &nbsp;</th>
						<th align="right" id="txt_net_wo_amount_sum">&nbsp;</th>
						<th colspan='8' align="right">&nbsp;</th>
					</tr>
	            </tfoot>
                
            </table>
		</div>
			<table style="width:1710px;">
				<tr>
					<td colspan="20" align="center">

						<? 

							echo load_submit_buttons($permission, "fnc_dyeing_work_order", 0, "", "reset_form('servicebookingknitting_1','','','','','');reset_table()", 1);
							?>

							<a id="print_button" style="cursor: pointer;border: outset 1px #66CC00;text-decoration: none;width:100px;height: 60px;" target="_blank" class="formbutton formbutton_disabled" onClick="print_knitting_work_order()">
							&nbsp;&nbsp;&nbsp;Print&nbsp;&nbsp;&nbsp;
							</a>
					</td>
					
				</tr>
			</table>
        <div id="booking_list_view"></div>
             
        </fieldset>
    </form>
</div>
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$("#cbo_wo_basis").val(2);
	fnc_change_details(2);
</script>
</html>
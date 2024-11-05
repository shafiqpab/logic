<?
/*-------------------------------------------- Comments 
Version          : V1
Purpose			 : This form will create knitting work order
Functionality	 :	
JS Functions	 :
Created by		 : Md. Helal Uddin 
Creation date 	 : 23-06-2020
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
Comments		 : From this version oracle conversion is start
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Dyeing Work Order", "../../", 1, 1,$unicode,'','');

//print_r($_SESSION['logic_erp']['data_arr'][418]);
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	<?
		$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][418] );
		//echo "var field_level_data= ". $data_arr . ";\n";
		echo "var field_level_data22= ". $data_arr . ";\n";

		if($_SESSION['logic_erp']['mandatory_field'][418]!="")
		{
			$mandatory_field_arr= json_encode( $_SESSION['logic_erp']['mandatory_field'][418] );
			echo "var mandatory_field_arr= ". $mandatory_field_arr . ";\n";
		}
	?>


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
	var response=return_global_ajax_value( cbo_currercy+"**"+txt_wo_date+"**"+cbo_company_name, 'check_conversion_rate', '', 'requires/dyeing_work_order_controller');
	var response=response.split("_");
	$('#txt_exchange_rate').val(response[1]);
}

function openmypage_wo_no(page_link,title)
{
	var cbo_company_name=$('#cbo_company_name').val();
	var cbo_buyer_name=$('#cbo_buyer_name').val();
	var cbo_within_group=$('#cbo_within_group').val();
	if(cbo_company_name ==0 || cbo_company_name==""){
		alert("Select Company First");
		return;
	}
	else if(cbo_within_group ==0 || cbo_within_group==""){
		alert('Select Within group First');
		return ;
	}
	page_link+='&company_id='+cbo_company_name+'&buyer_id='+cbo_buyer_name+'&within_group='+cbo_within_group;
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
            $("#cbo_booking_month").val(data[8]).attr('disabled', 'disabled');
            $("#cbo_style_ref_no").val(data[10]).attr('disabled', 'disabled');
            $("#cbo_fabric_booking_no").val(data[11]).attr('disabled', 'disabled');
            $("#cbo_fso_no").val(data[12]).attr('disabled', 'disabled');
            $("#cbo_dyeing_source").val(data[13]).attr('disabled', 'disabled');
            $("#cbo_dyeing_comp").val(data[14]).attr('disabled', 'disabled');
            $("#txt_attention").val(data[15]).attr('disabled', 'disabled');
            $("#txt_remark").val(data[16]).attr('disabled', 'disabled');
            $("#po_breakdown_id").val(data[17]).attr('disabled', 'disabled');
            $("#cbo_wo_basis").val(data[20]).attr('disabled', 'disabled');
            $("#cbo_ready_approval").val(data[21]);

			if (data[19]==1) $("#approved").text("Approved");
			else if (data[19]==3) $("#approved").text("Partial Approved");
			else $("#approved").text("");
            
            set_button_status(1, permission, 'fnc_dyeing_work_order',1);

			fnc_change_details($("#cbo_wo_basis").val());
			if($("#cbo_wo_basis").val()==1)
			{
				var details_ids = trim(return_global_ajax_value(data[0], 'populate_details_data', '', 'requires/dyeing_work_order_controller'));
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
				var details_data = trim(return_global_ajax_value_post(data[0]+'**'+data[17], 'populate_fso_wise_details_update', '', 'requires/dyeing_work_order_controller'));
				if(details_data!="")
				{
					$("#basis_details_container").html(details_data);
					const el = document.querySelector('#print_button');
					if (el.classList.contains("formbutton_disabled")) {
						el.classList.remove("formbutton_disabled");

					}
				}
			}
			
           $("#cbo_within_group").attr('disabled', 'disabled');
            $("#cbo_buyer_name").val(data[9]).attr('disabled', 'disabled');
        }
		
	}
}


function select_item_pop()
{
	var cbo_company_name=$('#cbo_company_name').val();
	var cbo_buyer_name=$('#cbo_buyer_name').val();
	var cbo_within_group=$('#cbo_within_group').val();
	if(cbo_company_name ==0 || cbo_company_name==""){
		alert("Select Company First");
		return;
	}
	else if(cbo_within_group ==0 || cbo_within_group==""){
		alert('Select Within group First');
		return ;
	}
	
	if($('#txt_dyeing_wo_order_no').val()!=""){
		$('#cbo_fso_no').prop('disabled',true);
		return;
	}

	var page_link='requires/dyeing_work_order_controller.php?action=select_item_pop&company_id='+cbo_company_name+'&buyer_id='+cbo_buyer_name+'&within_group='+cbo_within_group;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Select Item Popup', 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];       
		var theemail=this.contentDoc.getElementById("selected_fso_no");	
		//console.log(theemail.value);
		if (theemail.value!="")
		{
			var rows_data=trim(theemail.value).split('__');
			$("#cbo_buyer_name").val(rows_data[0]).attr("disabled",true);
			$("#cbo_company_name").val(rows_data[1]).attr("disabled",true);
			$("#cbo_style_ref_no").val(rows_data[2]).attr("disabled",true);
			$("#cbo_fso_no").val(rows_data[3]);
			$("#cbo_fabric_booking_no").val(rows_data[4]).attr("disabled",true);
			$("#po_breakdown_id").val(rows_data[5]);

			$("#scanning_tbl tbody").html("");
			
		}
	}
}

function fso_item_details_popup()
{
	var cbo_company_name=$('#cbo_company_name').val();
	var cbo_buyer_name=$('#cbo_buyer_name').val();
	var cbo_within_group=$('#cbo_within_group').val();
	var po_breakdown_id = $("#po_breakdown_id").val();
	if(cbo_company_name ==0 || cbo_company_name==""){
		alert("Select Company First");
		return;
	}
	else if(cbo_within_group ==0 || cbo_within_group==""){
		alert('Select Within group First');
		return ;
	}
	var page_link='requires/dyeing_work_order_controller.php?action=fso_details_list_view&company_id='+cbo_company_name+'&buyer_id='+cbo_buyer_name+'&within_group='+cbo_within_group+'&po_breakdown_id='+po_breakdown_id;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'FSO Item Popup', 'width=900px,height=450px,center=1,resize=1,scrolling=0', '../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];       
		var theemail=this.contentDoc.getElementById("selected_fso_details");	
		//console.log(theemail.value);
		if (theemail.value!="")
		{
			//theemail.value=$row[csf('fso_id')]."__".$row[csf('determination_id')]."__".$row[csf('gsm_weight')]."__".$row[csf('color_id')].',';
			var details_data = trim(return_global_ajax_value_post(theemail.value+'**'+po_breakdown_id, 'populate_fso_wise_details', '', 'requires/dyeing_work_order_controller'));
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
		var cbo_within_group=$('#cbo_within_group').val();
		// var update_id=$('#update_id').val();
		var page_link='requires/dyeing_work_order_controller.php?action=issue_no_pop&company_id='+cbo_company_name+'&buyer_id='+cbo_buyer_name+'&source='+cbo_dyeing_source+'&dyeing_company_id='+cbo_dyeing_comp+'&po_breakdown_id='+po_breakdown_id+'&within_group='+cbo_within_group;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Select Item Popup', 'width=900px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];       
			var theemail=this.contentDoc.getElementById("selected_work_order");	
			//console.log(theemail.value);
				
			if (theemail.value!="")
			{
				//var rows_data=trim(theemail.value).split('***');
				
				var details_data = trim(return_global_ajax_value_post(theemail.value+'**'+po_breakdown_id, 'populate_group_details', '', 'requires/dyeing_work_order_controller'));

				var row_details=details_data.split("***");
				for(j=0;j<row_details.length;j++){
					create_row(row_details[j]);  
				}
				
			}
			
		}
	}
}

function openprecess(id){
	
		var data=$('#proccessid_'+id).val();
		var page_link='requires/dyeing_work_order_controller.php?action=process_name_pop_up&row_id='+id+'&data='+data;
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
		console.log(datas);
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
		var color_range='';
		var disabled='';
		if(row_datas.length>1){
			var row_data_update=row_datas[1].split("__");
			details_id=row_data_update[0];
			rate=row_data_update[1];
			amount=row_data_update[2];
			remark=row_data_update[3];
			shade=row_data_update[4];
			proccess_loss=row_data_update[5];
			process_id=row_data_update[6];
			process_name=row_data_update[7];
			color_range=row_data_update[8];
			check=row_data_update[9];
			if(check!="h"){
				disabled="disabled='disabled'";

			}

		}
		//console.log(color_range);
		var data=row_data.split("__");

		
		$("#scanning_tbl").find('tbody tr').each(function() {

            var checking_data = $(this).find('input[name="checking_data[]"]').val();
            if(trim(row_data) == trim(checking_data)){
                msg++;
                return;
            }
            
        });
        if(msg>0){
            alert("Program Already exists");
        }
        else
        { 	
			var trColor="";
			if (j%2==0) trColor="#E9F3FF"; else trColor="#FFFFFF";
        	
        	j++;

        	var colorrange = trim(return_global_ajax_value(j+'**'+color_range, 'load_color_range', '', 'requires/dyeing_work_order_controller'));
        	
        	var html="<tr id='tr_"+j+"' bgcolor="+trColor+" align='center' valign='middle' >";
			html+="<td>"+j+" <input type='hidden' name='sl[]' id='sl_"+j+"' value='"+j+"' /><input type='hidden' value='"+trim(row_data)+"' id='checking_data_"+j+"' name='checking_data[]'><input type='hidden' value='"+details_id+"' name='detailsId[]' id='detailsId_"+j+"'></td>";
			html+="<td>"+data[1]+" <input type='hidden' name='issuedate[]' id='issuedate_"+j+"' value='"+data[0]+"' />  <input type='hidden' name='issueid[]' id='issueid_"+j+"' value='"+data[15]+"' /> </td>";
			html+="<td>"+data[2]+" <input type='hidden' name='issueno[]' id='issueno_"+j+"' value='"+data[2]+"' /></td>";
			html+="<td>"+data[4]+" <input type='hidden' name='bodypart[]' id='bodypart_"+j+"' value='"+data[3]+"' /></td>";
			html+="<td>"+data[6]+" <input type='hidden' name='fabricdescription[]' id='fabricdescription_"+j+"' value='"+data[5]+"' /></td>";
			html+="<td>"+data[7]+" <input type='hidden' name='gms[]' id='gms_"+j+"' value='"+data[7]+"' /></td>";
			html+="<td>"+data[8]+" <input type='hidden' name='dia[]' id='dia_"+j+"' value='"+data[8]+"' /></td>";
			html+="<td>"+data[9]+" <input type='hidden' name='stitchlength[]' id='stitchlength_"+j+"' value='"+data[9]+"' /></td>";
			html+="<td>"+data[11]+" <input type='hidden' name='count[]' id='count_"+j+"' value='"+data[10]+"' /></td>";
			html+="<td>"+data[14]+" <input type='hidden' name='dayingcolor[]' id='dayingcolor_"+j+"' value='"+data[13]+"' "+disabled+" /></td>";
			html+="<td>"+colorrange+"</td>";	
			html+="<td><input style='width:55px' type='text' name='shade[]' id='shade_"+j+"' placeholder='Shade' class='text_boxes_numeric' "+disabled+" value='"+shade+"' /></td>";
			html+="<td><input type='text' class='text_boxes_numeric'style='width:55px' name='processloss[]' id='processloss_"+j+"' placeholder='Process loss'  value='"+proccess_loss+"' "+disabled+" /></td>";
			html+="<td><input type='text' style='width:105px' name='proccessname[]' id='proccessname_"+j+"'  placeholder='Browse' value='"+process_name+"' class='text_boxes' onfocus='openprecess("+j+")' "+disabled+" /> <input type='hidden' value='"+process_id+"' name='proccessid[]' "+disabled+"  id='proccessid_"+j+"'   /></td>";
			
			html+="<td><input type='text' name='issueqty[]' disabled='disabled' style='width:67px' id='issueqty_"+j+"' class='text_boxes_numeric' onkeyup='calculate()' value='"+(Math.round(data[12]*100)/100)+"' /></td>";
			html+="<td><input type='text' name='woqnty[]' disabled='disabled' style='width:67px' id='woqnty_"+j+"'  class='text_boxes_numeric'  value='"+(Math.round(data[12]*100)/100)+"' /></td>";
			html+="<td><input type='text' name='rate[]' "+disabled+" style='width:57px' id='rate_"+j+"' value='"+(Math.round(rate*100)/100)+"' class='text_boxes_numeric' onkeyup='calculate()'  /></td>";
			html+="<td><input type='text' name='amount[]' "+disabled+" style='width:67px' id='amount_"+j+"' value='"+(Math.round(amount*100)/100)+"' class='text_boxes_numeric' readonly /></td>";
			html+="<td><input type='text' name='remark[]' "+disabled+" style='width:105px' id='remark_"+j+"' value='"+remark+"' class='text_boxes'  /></td>";
			
			html+="</tr>";
			//console.log(html);
			$('#scanning_tbl tbody').append(html);
			//calculate_total_qnty();
			$("#total_row").val(j);
			//alert(html);
		}
}

function calculate_total_qnty(){
	var qty=0;
	$("#scanning_tbl").find('tbody tr').each(function() {
        var programqnty = Number($(this).find('input[name="programqnty[]"]').val());
        qty+=programqnty;
    });
	$("#totalqnty").val(Math.round(Number(qty)*100)/100);
	$("#total_qnty").text(Math.round(Number(qty)*100)/100);
}

function fn_deleteRow(rid)
{
	var dtls_id=$("#detailsId_"+rid).val();
	var bill_no = trim(return_global_ajax_value(dtls_id, 'check_delete', '', 'requires/dyeing_work_order_controller'));
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
	if (form_validation('cbo_company_name*cbo_within_group*cbo_fso_no*cbo_currency*txt_delivery_date*cbo_pay_mode*cbo_dyeing_comp','Company Name*Within Group*FSO NO*Currency*Delivery Date*Pay Mode*Dyeing Company')==false)
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
			//var issuedate = $(this).find('input[name="issuedate[]"]').val();
			//var issueno = $(this).find('input[name="issueno[]"]').val();
			var bodypart = $(this).find('input[name="bodypart[]"]').val();
			var fabricdescription = $(this).find('input[name="fabricdescription[]"]').val();
			var gms = $(this).find('input[name="gms[]"]').val();
			var dia = $(this).find('input[name="dia[]"]').val();
			//var stitchlength = $(this).find('input[name="stitchlength[]"]').val();
			//var count = $(this).find('input[name="count[]"]').val();
			var dayingcolor = $(this).find('input[name="dayingcolor[]"]').val();
			var colorrange = $(this).find('input[name="colorrange[]"]').val();
			var proccessid = $(this).find('input[name="proccessid[]"]').val();
			var shade = $(this).find('input[name="shade[]"]').val();
			//var processloss = $(this).find('input[name="processloss[]"]').val();
			//var issueqty = $(this).find('input[name="issueqty[]"]').val();
			var woqnty = $(this).find('input[name="woqnty[]"]').val();
			var rate = $(this).find('input[name="rate[]"]').val();
			var amount = $(this).find('input[name="amount[]"]').val();
			var remark = $(this).find('input[name="remark[]"]').val();
			//var issueid = $(this).find('input[name="issueid[]"]').val();
			if(Number(rate)==0 || Number(amount)==0 || Number(woqnty)==0){
				//alert('Quantity, Rate and Amount can not be empty');
				k++;
				return;
			}
			try 
			{
				j++;

				dataString +='&detailsId_' + j + '=' + detailsId + '&bodypart_' + j + '=' + bodypart + '&fabricdescription_' + j + '=' + fabricdescription + '&gms_' + j + '=' + gms + '&dia_' + j + '=' + dia + '&dayingcolor_' + j + '=' + dayingcolor+ '&colorrange_' + j + '=' + colorrange  + '&proccessid_' + j + '=' + proccessid + '&shade_' + j + '=' + shade + '&woqnty_' + j + '=' + woqnty + '&rate_' + j + '=' + rate + '&amount_' + j + '=' + amount + '&remark_' + j + '=' + remark;
			}
			catch (e) {
				//got error no operation
				alert(e);
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
			var stitchlength = $(this).find('input[name="stitchlength[]"]').val();
			var count = $(this).find('input[name="count[]"]').val();
			var dayingcolor = $(this).find('input[name="dayingcolor[]"]').val();
			var colorrange = $(this).find('select[name="colorrange[]"]').val();
			var proccessid = $(this).find('input[name="proccessid[]"]').val();
			var shade = $(this).find('input[name="shade[]"]').val();
			var processloss = $(this).find('input[name="processloss[]"]').val();
			var issueqty = $(this).find('input[name="issueqty[]"]').val();
			var woqnty = $(this).find('input[name="woqnty[]"]').val();
			var rate = $(this).find('input[name="rate[]"]').val();
			var amount = $(this).find('input[name="amount[]"]').val();
			var remark = $(this).find('input[name="remark[]"]').val();
			var issueid = $(this).find('input[name="issueid[]"]').val();
			if(Number(rate)==0 || Number(amount)==0){
				//alert('Rate and Amount can not be empty');
				k++;
				return;
			}
			try 
			{
				j++;

				dataString +='&detailsId_' + j + '=' + detailsId + '&issueno_' + j + '=' + issueno + '&bodypart_' + j + '=' + bodypart + '&fabricdescription_' + j + '=' + fabricdescription + '&gms_' + j + '=' + gms + '&dia_' + j + '=' + dia + '&stitchlength_' + j + '=' + stitchlength + '&count_' + j + '=' + count + '&dayingcolor_' + j + '=' + dayingcolor+ '&colorrange_' + j + '=' + colorrange  + '&proccessid_' + j + '=' + proccessid + '&shade_' + j + '=' + shade  + '&processloss_' + j + '=' + processloss + '&issueqty_' + j + '=' + issueqty+ '&woqnty_' + j + '=' + woqnty + '&rate_' + j + '=' + rate + '&amount_' + j + '=' + amount + '&remark_' + j + '=' + remark+'&issuedate_'+j+'='+issuedate+'&issueid_'+j+'='+issueid ;
			}
			catch (e) {
				//got error no operation
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
    var data = "action=save_update_delete_details&operation=" + operation + '&tot_row=' + j + get_submitted_data_string('update_id*txt_dyeing_wo_order_no*cbo_booking_month*cbo_company_name*cbo_fso_no*po_breakdown_id*cbo_buyer_name*cbo_style_ref_no*cbo_fabric_booking_no*cbo_currency*cbo_ready_approval*txt_exchange_rate*txt_wo_date*txt_delivery_date*cbo_dyeing_source*cbo_pay_mode*cbo_dyeing_comp*txt_attention*txt_remark*cbo_within_group*cbo_wo_basis', "../../") + dataString;
    freeze_window(operation);
	http.open("POST","requires/dyeing_work_order_controller.php",true);
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
				
				if($("#cbo_wo_basis").val()==1)
				{
					reset_table();
					$("#total_row").val(0);
					var details_ids = trim(return_global_ajax_value(response[1], 'populate_details_data', '', 'requires/dyeing_work_order_controller'));
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
					var details_data = trim(return_global_ajax_value_post(response[1]+'**'+$("#po_breakdown_id").val(), 'populate_fso_wise_details_update', '', 'requires/dyeing_work_order_controller'));
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
	print_report( $('#update_id').val()+"**"+$('#cbo_company_name').val()+"**"+show_val_column, "print_knitting_work_order", "requires/dyeing_work_order_controller" ) 
		return;

}


function calculate()
{
	var rate=[];
	var j=0;
	$("input[name='rate[]']").map( function(key){
       rate[j]=Number($(this).val());
       j=j+1;
    });

    var issueqty=[];
    j=0;
    $("input[name='issueqty[]']").map( function(key){
       issueqty[j]=Number($(this).val());
       j=j+1;
    });
    j=0;
    $("input[name='amount[]']").map( function(key){
       var num=rate[j]*issueqty[j];
       $(this).val(Math.round(Number(num)*100)/100);
       j=j+1;
    });

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
		$('#cbo_fso_basis').prop("disabled",true);
		$('#issue_no_pop').prop("disabled",false);
		$("#basis_details_container").html('<table  width="1700" cellspacing="2" cellpadding="0" border="1" rules="all" class="rpt_table" id="scanning_tbl">'+
                '<thead>'+
				'<tr>'+
					'<th width="30">SL No</th>'+
					'<th width="70">Issue Date</th>'+
					'<th width="100">Issue No</th>'+
					'<th width="80">Body Part</th>'+
	                    '<th width="200">Fabric Construction & Composition</th>'+
	                    '<th width="50">GSM</th>'+
	                    '<th width="50">DIA</th>'+
	                    '<th width="50">S.L</th>'+
	                    '<th width="100">Yarn Count</th>'+
	                    '<th width="100">Dyeing Color Name</th>'+
	                    '<th width="100">Color Range</th>'+
	                    '<th width="70">Shade %</th>'+
	                    '<th width="70">Process Loss %</th>'+
	                    '<th width="120">Process Name</th>'+
	                    '<th width="80">Issue Qty.</th>'+
	                    '<th width="80">WO Qty.</th>'+
	                    '<th width="70">Rate</th>'+
	                    '<th width="80">Amount</th>'+
	                    '<th>Remarks</th>'+
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
					'</tfoot>'+
            '</table>');
	}
	else
	{
		$('#issue_no_pop').prop("disabled",true);
		$('#cbo_fso_basis').prop("disabled",false);
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

function chek_field_lvl_access(company)
{
	if (typeof field_level_data22[company] !== 'undefined') 
	{
		if(field_level_data22[company]["cbo_wo_basis"]["is_disable"]==1)
		{
			$('#cbo_wo_basis').attr("disabled","disabled");
		}
		else
		{
			$('#cbo_wo_basis').removeAttr("disabled");
		}
	}
	else
	{
		$('#cbo_wo_basis').removeAttr("disabled");
	}

	if (typeof field_level_data22[company] !== 'undefined') 
	{
		if(field_level_data22[company]["cbo_wo_basis"]["defalt_value"]==1)
		{
			$('#cbo_wo_basis').val(1);
			fnc_change_details(1);
		}
		else
		{
			$('#cbo_wo_basis').val(2);
			fnc_change_details(2);
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
                    <td width="130" align="right" class="must_entry_caption" colspan="3">WO No </td>              <!-- 11-00030  -->
                    <td width="170" colspan="3">
                    	<input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_wo_no('requires/dyeing_work_order_controller.php?action=dyeing_work_order_popup','Dyeing Order Search')" readonly placeholder="Double Click for Dyeing WO" name="txt_dyeing_wo_order_no" id="txt_dyeing_wo_order_no"/>
                    	<input type="hidden" name="update_id" id="update_id">
                    </td>                       
                    <td></td>
                </tr>
                <tr>
                    <td class="">Booking Month</td>   
                    <td> 
                    <? 
                    	echo create_drop_down( "cbo_booking_month", 172, $months,"", 1, "-- Select --", "", "",0 );		
                   				
                    ?>
                    </td>
                    <td class="must_entry_caption">Company Name</td>
                    <td>
						<? 
                            echo create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name", "id,company_name",1, "-- Select Company --", $selected, "check_exchange_rate();chek_field_lvl_access(this.value);load_drop_down( 'requires/dyeing_work_order_controller',document.getElementById('cbo_within_group').value+'_'+ this.value, 'load_drop_down_buyer', 'buyer_td' );","","" );
                        ?>	  
                    </td>
                    <td  class="must_entry_caption">Within Group</td>
                    <td>
                    	<?
							echo create_drop_down("cbo_within_group", 162, $yes_no, "", 0, "--  --", 0, "load_drop_down( 'requires/dyeing_work_order_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_buyer', 'buyer_td' );empty_fso_no();");
						?>
                    </td>
                    
                </tr>
                <tr>
                	<td class="must_entry_caption">Selected FSO No</td>
                    <td >
                    	<input type="text" class="text_boxes" name="cbo_fso_no" placeholder="Browse FSO No" onDblClick="select_item_pop()" id="cbo_fso_no" style="width: 160px;">
                    	<input type="hidden" name="po_breakdown_id" id="po_breakdown_id" >
                    </td>
                     <td>Buyer name</td>
                    <td id="buyer_td">
						<?
                            echo create_drop_down( "cbo_buyer_name", 172, $blank_arr,"", 1, "-- Select Buyer --", $selected, "",0 );
                        ?> 
                    </td> 
                    <td width="130" class="">Style Ref. No </td>
                    <td width="170">
                    	<input type="text" class="text_boxes" placeholder="Style ref. no" name="cbo_style_ref_no" id="cbo_style_ref_no" style="width: 160px;">
                    </td>
                </tr>
                <tr>
                	 <td class="">Fab. Booking No</td>
                    <td>
                    	<input type="text" class="text_boxes" placeholder="Fab booking no" name="cbo_fabric_booking_no" style="width: 160px;" id="cbo_fabric_booking_no">
                    </td>
                    <td class="must_entry_caption">Currency</td>
                    <td>
						<? 
                        	echo create_drop_down( "cbo_currency", 172, $currency,"", 1, "-- Select --", 1, "check_exchange_rate()",0 );		
                        ?>	
                    </td>
                    <td>Exchange Rate</td>
                    <td>
                    	<input style="width:160px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate" placeholder="Exchange Rate"  readonly />  
                    </td>
                   
                   
                </tr>
                <tr>
                	<td width="130">Wo date</td>
                    <td width="170">
                    	<input class="datepicker" type="text" style="width:160px" name="txt_wo_date" id="txt_wo_date" onChange="check_exchange_rate();" value="<? echo date("d-m-Y")?>"  />	
                    </td>
                	<td width="130" class="must_entry_caption">Delivery Date</td>
                    <td width="170">
                    	<input class="datepicker" type="text" style="width:160px" name="txt_delivery_date" id="txt_delivery_date" value='<?php echo date("d-m-Y");?>' />	
                    </td>
                    <td class="must_entry_caption">Pay Mode</td>
                    <td>
                    	<? echo create_drop_down( "cbo_pay_mode", 172, $pay_mode,"", 1, "-- Select Pay Mode --", "", "","" ); ?> 
                    </td>
                </tr>
                <tr>
                	<td width="130" class="must_entry_caption">Dyeing Source</td>
                    <td width="170">
                    	
                    	<?
							echo create_drop_down( "cbo_dyeing_source", 172, $knitting_source, "", 1, "-- Select --", 3, "load_drop_down( 'requires/dyeing_work_order_controller',this.value+'**'+$('#cbo_company_name').val(),'load_drop_down_knitting_com','dyeing_company_td' );",1,"1,3" );

							?>
                    </td>
                    <td class="must_entry_caption">Dyeing Company</td>
                    <td id="dyeing_company_td">
                    	<? 
                            echo create_drop_down( "cbo_dyeing_comp", 172, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name",1, "-- Select Company --", $selected, "","","" );
                        ?>	 
                    </td>
                   <td class="must_entry_caption">Ready To Approved</td>
					<? $ready_to_approval=array(1=>"Yes",2=>"No");?>
                    <td>
                    	<? echo create_drop_down( "cbo_ready_approval", 172, $ready_to_approval,"", 1, "-- Select Ready Approval --", "", "","" ); ?> 
                    </td>
                </tr>
               
                <tr>
                   
                    <td>Attention</td>   
                    <td colspan="3">
                    	<input class="text_boxes" type="text" style="width:97%;"  name="txt_attention" id="txt_attention"/>
                    	<input type="hidden" class="image_uploader" style="width:162px" value="Lab DIP No" onClick="openmypage( 'requires/dyeing_work_order_controller.php?action=lapdip_no_popup', 'Lapdip No', 'lapdip')">
                    </td>
                    <td>
						<? 
                        include("../../terms_condition/terms_condition.php");
                        terms_condition(418,'txt_dyeing_wo_order_no','../../','txt_dyeing_wo_order_no');
                        ?>                    
                    </td>

					<? $wo_basis_arr=array(1=>"Challan no",2=>"FSO Wise");?>
                    <td>
						<span style="color:blue;"> Basis &nbsp;&nbsp;&nbsp;</span> 
                    	<? echo create_drop_down( "cbo_wo_basis", 122, $wo_basis_arr,"", 0, "-- Select --", "", "fnc_change_details(this.value)","" ); ?> 
                    </td>
                </tr>
                <tr>
                	<td> Remark</td>
                	<td colspan="3"><input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width: 97%;"></td>
                	<td colspan="2">
						<span>FSO Basis</span>
						<input type="text" class="text_boxes" name="cbo_fso_basis" placeholder="Browse FSO No" onDblClick="fso_item_details_popup()" id="cbo_fso_basis" style="width: 82px;" disabled>
						<span>Challan Basis</span>
                		<input type="text" class="text_boxes" name="issue_no_pop" placeholder="Browse Issue no" onDblClick="issuenopop();" id="issue_no_pop" style="width: 85px;">
						
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
	                    <th width="50">GSM</th>
	                    <th width="50">DIA</th>
	                    <th width="50">S.L</th>
	                    <th width="100">Yarn Count</th>
	                    <th width="100">Dyeing Color Name</th>
	                    <th width="100">Color Range</th>
	                    <th width="70">Shade %</th>
	                    <th width="70">Process Loss %</th>
	                    <th width="120">Process Name</th>
	                    <th width="80">Issue Qty.</th>
	                    <th width="80">WO Qty.</th>
	                    <th width="70">Rate</th>
	                    <th width="80">Amount</th>
	                    <th>Remarks</th>
	                    
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
</html>
<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Weaving Plan Entry
Functionality	:	
JS Functions	:
Created by		:	Md. Helal Uddin
Creation date 	: 	22-01-2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start(); 
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
	require_once('../includes/common.php');
	extract($_REQUEST);
	$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents("Weaving Plan Entry", "../", 1, 1,'','','');

	?>	
	<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";  
var permission = '<? echo $permission; ?>';

function fnc_weaving_plan_entry(operation)
{
	freeze_window(operation);
	if(operation==4)
	{ 
		print_report( $('#update_id').val(), "print", "requires/weaving_plan_entry_controller" ) ;
		release_freezing();
		return;
	}

	if (form_validation('txt_system_no*update_id','System No*System No')==false)
	{
		release_freezing();
		return;
	}
	
	var row_num=$('#yarn_dyeing_breakdown tbody tr').length;
	var data_all=""; var z=1;
	for (var i=1; i<=row_num; i++)
	{
		data_all+="&dtls_id_" + z + "='" + $('#dtls_id_'+i).val()+"&product_id_" + z + "='" + $('#product_id_'+i).val()+"'&wrap_kg_" + z + "='" + $('#wrap_kg_'+i).val()+"'&weft_kg_" + z + "='" + $('#weft_kg_'+i).val()+"'&total_kg_" + z + "='" + $('#total_kg_'+i).val()+"'";
		z++;
	}

	if(data_all.length <= 0)
	{
		alert('Warp Plan and Weft Plan');
		release_freezing();
		return;
	}

	var data="action=save_update_delete_dtls&operation="+operation+get_submitted_data_string('update_id*txt_weave*txt_ends_x_pick_greige*txt_ref*txt_greige_fabric_width_inch*txt_reed*txt_required_greige_mtr*txt_reed_space*txt_required_warp_length_mtr*txt_ground_ends*txt_extra_selvedge_ends*txt_spo_receive_date*txt_total_ends*txt_total_allowance*txt_previous_status*txt_balance_qty*warp_plan_data*weft_plan_data*dtls_id*cbo_template_id',"../")+data_all+"&row_num='"+row_num+"'";
	
	
	
	http.open("POST","requires/weaving_plan_entry_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange=fnc_weaving_plan_entry_reply_info;
}

function fnc_weaving_plan_entry_reply_info()
{
	if(http.readyState == 4) 
	{
		 //alert(http.responseText);return;
		 var reponse=trim(http.responseText).split('**');	

		 show_msg(reponse[0]);

		 if((reponse[0]==0 || reponse[0]==1))
		 {
			reset_details_part();
			load_break_down();
			set_button_status(1, permission, 'fnc_yarn_demand_entry',1);
		 }
		 else if(reponse[0]==2)
		 {
			reset_details_part();
			load_break_down();
			set_button_status(0, permission, 'fnc_yarn_demand_entry',1);
		 }
		 else if(reponse[0]==10)
		 {
			alert(reponse[1]);
		 }
		 
		 release_freezing();	
	}
}
	
function fnc_weaving_plan_mst(operation)
{
	if(operation==4)
	{ 
		print_report( $('#update_id').val(), "print", "requires/weaving_plan_entry_controller" ) 
		return;
	}

	if (form_validation('cbo_company_name*txt_req_sale_no','Company*Product No')==false)
	{
		return;
	}

	
	try {
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*update_id*cbo_basis*txt_despo_product_no*txt_req_sale_no*txt_req_sale_dtls_id*cbo_gmts_item_id*txt_mill_code*txt_pi_no*txt_style_design*cbo_color_id*txt_spo_number*hidden_construction_id*cbo_buyer_id*hidden_composition_id*cbo_finish_type*txt_finished_fabric_width_inch*txt_final_delivery_date*txt_pp_delivery_date*txt_pp_qnty*txt_extension*hidden_color_type*txt_order_qnty*hidden_determination_id',"../");
	} catch (error) {
		console.log(error);
	}
	
	freeze_window(operation);
	
	http.open("POST","requires/weaving_plan_entry_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange=fnc_weaving_plan_mst_replay_info;
}

function fnc_weaving_plan_mst_replay_info()
{
	if(http.readyState == 4) 
	{
		 //alert(http.responseText);return;
		 var reponse=trim(http.responseText).split('**');	

		 show_msg(reponse[0]);

		 if((reponse[0]==0 || reponse[0]==1))
		 {
			if(reponse[0]==0)
			{
				$('#txt_system_no').val(reponse[1]);
		 		$('#update_id').val(reponse[2]);
			}
			set_button_status(1, permission, 'fnc_weaving_plan_mst',1);
		 	load_break_down();
		 }
		 else if(reponse[0]==2)
		 {
			load_break_down();
			reset_master_part();
			reset_details_part();
			set_button_status(0, permission, 'fnc_weaving_plan_mst',1);
		 }
		
		 release_freezing();	
	}
}

function openmypage_SystemNo()
{
	if (form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	else
	{
		var cbo_company_name = $("#cbo_company_name").val();
		var title = 'System No Popup';
		var page_link = 'requires/weaving_plan_entry_controller.php?action=system_no_popup';
		
		page_link+='&cbo_company_name='+cbo_company_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width=850px,height=350px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var dtls_id=this.contentDoc.getElementById("dtls_id").value;
			var all_data=this.contentDoc.getElementById("all_data").value;
			

			try
			{
				var data = all_data.split("*");
				
				
				//Destructuring => it assign array value to corresponding variable , need to keep in sequence ;
				var [update_id,company_id,system_no,despo_no,planing_basis,gmt_item_id,req_sales_order_no,req_sales_order_dtls_id,pi_no,mill_code,txt_color,color_id,style_degin,extension,spo_number,color_type,txt_construction,construction_id,buyer_id,composition,composition_id,finish_type,finished_fabric_width_inch,final_delivery_date,order_qnty,pp_delivery_date,pp_qnty] =[...data];

				reset_master_part();
				reset_details_part();

				$("#update_id").val(update_id);
				$("#txt_system_no").val(system_no);
				$("#cbo_company_name").val(company_id);
				$("#txt_despo_product_no").val(despo_no);
				$("#cbo_basis").val(planing_basis);
				$("#cbo_gmts_item_id").val(gmt_item_id);
				$("#txt_req_sale_no").val(req_sales_order_no);
				$("#txt_req_sale_dtls_id").val(req_sales_order_dtls_id);
				$("#txt_pi_no").val(pi_no);
				$("#txt_mill_code").val(mill_code);
				$("#txt_color").val(txt_color);
				$("#cbo_color_id").val(color_id);
				$("#txt_style_design").val(style_degin);
				$("#txt_extension").val(extension);
				$("#txt_spo_number").val(spo_number);
				$("#hidden_color_type").val(color_type);
				$("#txt_finish_construction").val(txt_construction);
				$("#hidden_construction_id").val(construction_id);
				$("#cbo_buyer_id").val(buyer_id);
				$("#txt_fabric_composition").val(composition);
				$("#hidden_composition_id").val(composition_id);
				$("#cbo_finish_type").val(finish_type);
				$("#txt_finished_fabric_width_inch").val(finished_fabric_width_inch);
				$("#txt_final_delivery_date").val(final_delivery_date);
				$("#txt_order_qnty").val(order_qnty);
				$("#txt_pp_delivery_date").val(pp_delivery_date);
				$("#txt_pp_qnty").val(pp_qnty);
				set_button_status(1, permission, 'fnc_weaving_plan_mst',1);
				load_break_down();
			}
			catch(err)
			{
				console.log(err);
			}
		}

	}
}

function open_warp_plan()
{
	if (form_validation('txt_warp_yarn_lot_and_brand','Warp Yarn Lot and Brand')==false)
	{
		return;
	}
	else
	{
		var hidden_warp_prod_id = $("#hidden_warp_prod_id").val();
		var noOfRow = hidden_warp_prod_id.split("*,*");
		var row = noOfRow.length;
		var column = prompt("Enter no of column:");
		//console.log(typeof parseInt(column)+' and '+column.length)
		if(isNaN(parseInt(column)) || column.length ==0 )
		{
			alert('Invalid column no');return;
		}
		var width = 7*80 + 6 * 80 ;
		var height = 100 + parseInt(row) * 23;
		var plan_data = $('#warp_plan_data').val();
		var txt_total_ends = $('#txt_total_ends').val() * 1;
		var txt_required_warp_length_mtr = $('#txt_required_warp_length_mtr').val() * 1;
		var txt_order_qnty = (($('#txt_order_qnty').val() * 1 ) + 300) / 1.0936;
		var title = 'Warp Plan';
		var page_link = 'requires/weaving_plan_entry_controller.php?hidden_warp_prod_id='+hidden_warp_prod_id+'&action=warp_plan_popup'+'&rows='+parseInt(row)+'&column='+parseInt(column)+'&width='+parseInt(width)+'&plan_data='+plan_data+'&txt_total_ends='+txt_total_ends+'&txt_required_warp_length_mtr='+txt_required_warp_length_mtr+'&txt_order_qnty='+txt_order_qnty;

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width='+width+'px,height='+height+'px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var data=this.contentDoc.getElementById("data").value;
			$('#warp_plan_data').val(data);
			load_break_down();
		}
	}
}
function open_weft_plan()
{
	if (form_validation('txt_weft_yarn_lot_and_brand','Weft Yarn Lot and Brand')==false)
	{
		return;
	}
	else
	{
		var hidden_weft_prod_id = $("#hidden_weft_prod_id").val();
		var noOfRow = hidden_weft_prod_id.split("*,*");
		var row = noOfRow.length;
		var column = prompt("Enter no of column:");
		if(isNaN(parseInt(column)) || column.length ==0 )
		{
			alert('Invalid column no');return;
		}
		var width = 7*80 + 6 * 80 ;
		var height = 100 + parseInt(row) * 23;
		var plan_data = $('#weft_plan_data').val();
		var txt_total_ends = $('#txt_total_ends').val();
		var txt_required_warp_length_mtr = $('#txt_required_warp_length_mtr').val() * 1;
		var txt_reed_space 	= $('#txt_reed_space').val() * 1;
		var txt_reed 		= $('#txt_reed').val() * 1;
		var txt_g_pick 		= 1; // need to added in input field
		var txt_order_qnty = (($('#txt_order_qnty').val() * 1 ) + 300) / 1.0936;
		var title = 'Weft Plan';
		var page_link = 'requires/weaving_plan_entry_controller.php?hidden_weft_prod_id='+hidden_weft_prod_id+'&action=weft_plan_popup'+'&rows='+parseInt(row)+'&column='+parseInt(column)+'&width='+parseInt(width)+'&plan_data='+plan_data+'&txt_total_ends='+txt_total_ends+'&txt_required_warp_length_mtr='+txt_required_warp_length_mtr+'&txt_order_qnty='+txt_order_qnty+'&txt_reed_space='+txt_reed_space+'&txt_g_pick='+txt_g_pick+'&txt_reed='+txt_reed;

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width='+width+'px,height='+height+'px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var data=this.contentDoc.getElementById("data").value;
			//var no_of_column=this.contentDoc.getElementById("no_of_column").value;
			//var no_of_row=this.contentDoc.getElementById("no_of_row").value;
			$('#weft_plan_data').val(data);
			load_break_down();
		}
	}
}
const load_break_down = () => {
	$("#weaving_plan_break_down_list_view").html(`
	<table align="right" cellspacing="0" width="810"  border="1" rules="all" class="rpt_table" id="yarn_dyeing_breakdown"><thead></thead><tbody></tbody></table>`);

	var param = $("#update_id").val()+'**'+$("#hidden_warp_prod_id").val()+'**'+$("#warp_plan_data").val()+'**'+$("#hidden_weft_prod_id").val()+'**'+$("#weft_plan_data").val();
	console.log(`param=${param}`);
	show_list_view(param,'show_weaving_plan_break_down_listview','weaving_plan_break_down_list_view','requires/weaving_plan_entry_controller','');
}
function openmypage_ProductNo()
{
	
	if (form_validation('cbo_company_name*cbo_basis','Company*Basis')==false)
	{
		return;
	}
	else
	{
		var basis = $("#cbo_basis").val() * 1;
		var cbo_company_name = $("#cbo_company_name").val();

		if(basis == 1)
		{
			var title = 'Requisition Info';
			var page_link = 'requires/weaving_plan_entry_controller.php?action=requisition_popup';
		}
		else
		{
			var title = 'Sales Order Info';
			var page_link = 'requires/weaving_plan_entry_controller.php?action=sales_order_popup';
		}
		page_link+='&cbo_company_name='+cbo_company_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width=850px,height=350px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var dtls_id=this.contentDoc.getElementById("dtls_id").value;
			var all_data=this.contentDoc.getElementById("all_data").value;
			

			try
			{
				var data = all_data.split("*");
				
				//Destructuring => it assign array value to corresponding variable , need to keep in sequence ;
				var [txt_req_sale_dtls_id,company_id,txt_req_sale_no,buyer_id,determination_id,constuction_id,composition_id,product_type,weave_design,finish_type,txt_color,fabric_weight,fabric_weight_type,finish_width,cutable_width,wash_type,offer_qnty,uom,buyer_target_price,amount,txt_style_design,txt_finish_construction,composition_str,txt_final_delivery_date,item_number_id,color_type] =[...data] 
				console.clear();
				console.log(txt_finish_construction,composition_str)

				$("#txt_req_sale_no").val(txt_req_sale_no);
				$("#txt_req_sale_dtls_id").val(txt_req_sale_dtls_id);
				$("#txt_style_design").val(txt_style_design);
				$("#txt_color").val(txt_color);
				$("#txt_finish_construction").val(txt_finish_construction);
				$("#hidden_construction_id").val(constuction_id);
				$("#txt_fabric_composition").val(composition_str);
				$("#hidden_composition_id").val(composition_id);
				$("#cbo_buyer_id").val(buyer_id);
				$("#cbo_finish_type").val(finish_type);
				$("#txt_order_qnty").val(offer_qnty);
				$("#txt_weave").val(weave_design);
				$("#txt_finished_fabric_width_inch").val(cutable_width);
				$("#txt_final_delivery_date").val(txt_final_delivery_date);
				$("#cbo_gmts_item_id").val(item_number_id);
				$("#hidden_determination_id").val(determination_id);
				$("#hidden_color_type").val(product_type);
				$("#txt_spo_number").val(color_type);
			}
			catch(err)
			{
				alert(err);
			}

			

		}

	}
}

function openmypage_yarn_lot(type)
{
	if (form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	else
	{
		var title = 'Yarn Lot Info';
		var cbo_company_name = $("#cbo_company_name").val();
		var page_link = 'requires/weaving_plan_entry_controller.php?action=yarn_lot_popup&type='+type+'&cbo_company_name='+cbo_company_name;
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width=850px,height=350px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var product_ids=this.contentDoc.getElementById("product_id").value;
			var all_data=this.contentDoc.getElementById("all_data").value;
			var lot_with_name=this.contentDoc.getElementById("lot_with_name").value;
			
			try
			{
				var data = all_data.split("*");
				
				//console.clear();
				
				if(type == 1 )
				{
					$("#txt_warp_yarn_lot_and_brand").val(lot_with_name);
					$("#hidden_warp_prod_id").val(product_ids);
					$("#hidden_warp_prod_data").val(all_data);
				}
				else
				{
					$("#txt_weft_yarn_lot_and_brand").val(lot_with_name);
					$("#hidden_weft_prod_id").val(product_ids);
					$("#hidden_weft_prod_data").val(all_data);
				}
				
			}
			catch(err)
			{
				alert(err);
			}
		}
	}
}
function add_img(row)
{
	if( form_validation('update_id','System No')==false )
	{
		return;
	}
	file_uploader ( '../', document.getElementById('update_id').value+row,'', 'weaving_plan_entry', 0 ,1);
}
const requiredGreige = () =>{
	var txt_order_qnty = $('#txt_order_qnty').val() * 1;
	var required_greige = (((txt_order_qnty * 100) / (100 - 8) ) / 1.0936) * 1;
	console.log(`${required_greige} = (((${txt_order_qnty} * 100) / (100 - 8) ) / 1.0936) * 1`);
	$("#txt_required_greige_mtr").val(required_greige.toFixed(6));
	requiredWrapLength();
}
const requiredWrapLength = () =>{
	var txt_required_greige_mtr = $("#txt_required_greige_mtr").val() * 1;
	var txt_required_warp_length_mtr = ((txt_required_greige_mtr * 100 ) / (100 - 6)) * 100 ;
	$("#txt_required_warp_length_mtr").val(txt_required_warp_length_mtr.toFixed(6));
}
const groundEnds = () =>{
	var txt_total_ends = $("#txt_total_ends").val() * 1 - 14;
	$("#txt_ground_ends").val(txt_total_ends);
}

function put_details_data(dtls_id)
{
	
	reset_details_part();
	if(dtls_id.length == 0) return;
	$("#dtls_id").val(dtls_id);
	get_php_form_data( dtls_id, "populate_dtls_data", "requires/weaving_plan_entry_controller" );
	set_button_status(1, permission, 'fnc_weaving_plan_entry',2);
}

function reset_details_part()
{
	$('#warp_plan_data').val('');
	$('#weft_plan_data').val('');
	$('#txt_weave').val('');
	$('#txt_ends_x_pick_greige').val('');
	$('#txt_ref').val('');
	$('#txt_greige_fabric_width_inch').val('');
	$('#txt_reed').val('');
	$('#txt_reed_space').val('');
	$('#txt_required_greige_mtr').val('');
	$('#txt_required_warp_length_mtr').val('');
	$('#txt_ground_ends').val('');
	$('#txt_extra_selvedge_ends').val('');
	$('#txt_spo_receive_date').val('');
	$('#txt_total_ends').val('');
	$('#txt_total_allowance').val('');
	$('#txt_previous_status').val('');
	$('#txt_balance_qty').val('');
	$('#cbo_template_id').val('');
	$('#txt_warp_yarn_lot_and_brand').val(``);
	$('#txt_weft_yarn_lot_and_brand').val(``);
	$('#hidden_warp_prod_id').val(``);
	$('#hidden_weft_prod_id').val(``);
	$("#dtls_id").val('');
	set_button_status(0, permission, 'fnc_weaving_plan_entry',2);
}
function reset_master_part()
{
	$("#update_id").val('');
	$("#txt_system_no").val('');
	$("#txt_despo_product_no").val('');
	$("#cbo_basis").val('');
	$("#cbo_gmts_item_id").val('');
	$("#txt_req_sale_no").val('');
	$("#txt_req_sale_dtls_id").val('');
	$("#txt_pi_no").val('');
	$("#txt_mill_code").val('');
	$("#txt_color").val('');
	$("#cbo_color_id").val('');
	$("#txt_style_design").val('');
	$("#txt_extension").val('');
	$("#txt_spo_number").val('');
	$("#hidden_color_type").val('');
	$("#txt_finish_construction").val('');
	$("#hidden_construction_id").val('');
	$("#cbo_buyer_id").val('');
	$("#txt_fabric_composition").val('');
	$("#hidden_composition_id").val('');
	$("#cbo_finish_type").val('');
	$("#txt_finished_fabric_width_inch").val('');
	$("#txt_final_delivery_date").val('');
	$("#txt_order_qnty").val('');
	$("#txt_pp_delivery_date").val('');
	$("#txt_pp_qnty").val('');
	set_button_status(0, permission, 'fnc_weaving_plan_mst',1);
}

</script>
</head>
<style type="text/css">
	.text_boxes_modify {
	  height: 18px;
	  font-size: 13px;
	  line-height: 16px;
	  padding: 0 5px;
	  text-align: left;
	  border: 1px solid #676767;
	  border-radius: 3px;
	  border-radius: .5em;
	}
</style>
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../",$permission); ?>
		<fieldset style="width:850px;"><br>
			<legend>Product Planning Sheet</legend> 
			<form name="weaving_plan_entry_1" id="weaving_plan_entry_1"> 
				<fieldset style="width:820px;">
					<table width="810" align="center" border="0">
						<tr>
							<td width="200"></td>
							<td  align="right"><strong>System No</strong></td>
							<td  align="left" width="200">
								<input type="text" name="txt_system_no" id="txt_system_no" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_SystemNo();"  readonly/>
								<input type="hidden" name="update_id" id="update_id">
							</td>
							<td></td>
						</tr>
						<tr>
							<td width="200" class="must_entry_caption">Company</td>
							<td>
								<?
									
									echo create_drop_down("cbo_company_name",160,"SELECT company_name,id from lib_company where status_active = 1 and is_deleted = 0 order by company_name","id,company_name", 1, "-- Select --", $selected,"","",'');
								?>
							</td>
							<td width="200">Product Number</td>
							<td>
								<input type="text" name="txt_despo_product_no" id="txt_despo_product_no" class="text_boxes" style="width:150px;">
							</td>
						</tr>
						
						<tr>
							<td width="200" class="must_entry_caption">Basis</td>
							<td>
								<?
									$req_basis = array(1=>"Requisition",2=>"Sales Order");
									echo create_drop_down("cbo_basis",160,$req_basis,"", 1, "-- Select --", $selected,"","",'');
								?>
							</td>
							<td width="200"> Garments Name </td>
							<td>
								<?
								echo create_drop_down("cbo_gmts_item_id",160,$garments_item,"", 1, "-- Select --", $selected,"","",'');
								?>
							</td>
						</tr>
						<tr>
							<td width="200" class="must_entry_caption">Ref. No</td>
							<td>
								<input type="text" name="txt_req_sale_no" id="txt_req_sale_no" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_ProductNo();"  readonly/>
								<input type="hidden" name="txt_req_sale_dtls_id" id="txt_req_sale_dtls_id">
							</td>
							<td width="200">PI Number</td>
							<td>
								<input type="text" name="txt_pi_no" id="txt_pi_no" style="width:150px" class="text_boxes"/>
							</td>
						</tr>
						<tr>
							<td width="200">Mill Code</td>
							<td>
								<input type="text" name="txt_mill_code" id="txt_mill_code" style="width:150px" class="text_boxes"/>
							</td>
							<td width="200">Color</td>
							<td>
								<input type="text" name="txt_color" id="txt_color" style="width:150px" class="text_boxes"/>
								<input type="hidden" name="cbo_color_id" id="cbo_color_id" />
							</td>
						</tr>
						<tr>
							<td width="200">Style/Design</td>
							<td>
								<input type="text" name="txt_style_design" id="txt_style_design" style="width:150px" class="text_boxes"/>
							</td>
							<td width="200">Extension</td>
							<td>
								<input type="text" name="txt_extension" id="txt_extension" style="width:150px" class="text_boxes"/>
							</td>
						</tr>
						
						
					</table>
				</fieldset>                 
				<fieldset style="width:810px; margin-top:10px">
					<legend>Finished Fabric Details</legend>
					<table cellpadding="1" cellspacing="1" border="0" width="100%">
						<tr>
							<td width="200">SPO Number</td>
							<td>
								<input type="text" name="txt_spo_number" id="txt_spo_number" style="width:150px" class="text_boxes"/>
								<input type="hidden" id="hidden_color_type">
							</td>
							<td width="200">Finish Construction</td>
							<td>
								<input type="text" name="txt_finish_construction" id="txt_finish_construction" style="width:150px" class="text_boxes"/>
								<input type="hidden" id="hidden_construction_id">
							</td>
						</tr>
						<tr>
							<td width="200">Customer</td>
							<td>
								<? echo create_drop_down( "cbo_buyer_id", 160, "SELECT id,buyer_name from lib_buyer where status_active = 1 and is_deleted = 0 order by buyer_name","id,buyer_name", 1, "-- Select Customer --", $selected, "" ); ?>
							</td>
							<td width="200">Fabric Composition (Warp x Weft)</td>
							<td>
								<input type="text" name="txt_fabric_composition" id="txt_fabric_composition" style="width:150px" class="text_boxes"/>
								<input type="hidden" id="hidden_composition_id">
								<input type="hidden" id="hidden_determination_id">
							</td>
						</tr>
						<tr>
							<td width="200">Finish Type</td>
							<td>
								<? 
									$finish_types = array(1=>"Regular",2=>"Peach",3=>"Brush");
									echo create_drop_down("cbo_finish_type", 160, $finish_types, "", 1, "Select", 0, "");
								?>
							</td>
							<td width="200">Finished Fabric Width (Inch)</td>
							<td>
								<input type="text" name="txt_finished_fabric_width_inch" id="txt_finished_fabric_width_inch" style="width:150px" class="text_boxes"/>
							</td>
						</tr>
						<tr>
							<td width="200">Final Delivery Date</td>
							<td>
								<input  type="text" style="width:150px" class="datepicker" placeholder="Select Date"  name="txt_final_delivery_date" id="txt_final_delivery_date"/>
							</td>
							<td width="200">Order Qnty(Yds)</td>
							<td>
								<input type="text" name="txt_order_qnty" id="txt_order_qnty" style="width:150px" class="text_boxes_numeric" onkeyup="requiredGreige()"/>
							</td>
						</tr>
						<tr>
							<td width="200">PP Delivery Date</td>
							<td>
								<input  type="text" style="width:150px" class="datepicker" placeholder="Select Date"  name="txt_pp_delivery_date" id="txt_pp_delivery_date"/>
							</td>
							<td width="200">PP Qnty(Yds)</td>
							<td>
								<input type="text" name="txt_pp_qnty" id="txt_pp_qnty" style="width:150px" class="text_boxes_numeric"/>
							</td>
						</tr>
						<tr style="height: 30px;">
							<td colspan="4"></td>
						</tr>
						<tr>
							<td colspan="4"  align="center" >
								<? 
								echo load_submit_buttons($permission, "fnc_weaving_plan_mst", 0,0,"reset_form('weaving_plan_entry_1','weaving_plan_list_view','','','disable_enable_fields(\'cbo_company_id\',0)');",1);
								?>
							</td>
						</tr>
                    </table>
					
                </fieldset> 
                <fieldset style="width:810px; margin-top:10px">
					<legend>Greige Fabric Details</legend>
					<table cellpadding="1" cellspacing="1" border="0" width="100%">
						<tr>
							<td width="200">Weave</td>
							<td>
								<input type="text" name="txt_weave" id="txt_weave" style="width:150px" class="text_boxes"/>
							</td>
							<td width="200">Ends X Pick (Greige)</td>
							<td>
								<input type="text" name="txt_ends_x_pick_greige" id="txt_ends_x_pick_greige" style="width:150px" class="text_boxes"/>
							</td>
						</tr>
						<tr>
							<td width="200">REF</td>
							<td>
								<input type="text" name="txt_ref" id="txt_ref" style="width:150px" class="text_boxes"/>
							</td>
							<td width="200">Greige Fabric Width (Inch)</td>
							<td>
								<input type="text" name="txt_greige_fabric_width_inch" id="txt_greige_fabric_width_inch" style="width:150px" class="text_boxes"/>
							</td>
						</tr>
						<tr>
							<td width="200">REED Count</td>
							<td>
								<input type="text" name="txt_reed" id="txt_reed" style="width:150px" class="text_boxes"/>
							</td>
							<td width="200">Required Greige(Mtr)</td>
							<td>
								<input type="text" name="txt_required_greige_mtr" id="txt_required_greige_mtr" style="width:150px" class="text_boxes"/>
							</td>
						</tr>
						<tr>
							<td width="200">REED Space</td>
							<td>
								<input type="text" name="txt_reed_space" id="txt_reed_space" style="width:150px" class="text_boxes"/>
							</td>
							<td width="200">Required Warp Length(Mtr)</td>
							<td>
								<input type="text" name="txt_required_warp_length_mtr" id="txt_required_warp_length_mtr" style="width:150px" class="text_boxes"/>
							</td>
						</tr>
						<tr>
							<td width="200">Warp Yarn Lot and Brand</td>
							<td>
								<input type="text" name="txt_warp_yarn_lot_and_brand" id="txt_warp_yarn_lot_and_brand" placeholder="Double click to Browse" ondblclick="openmypage_yarn_lot(1)" style="width:150px" class="text_boxes"/>
								<input type="hidden" id="hidden_warp_prod_id" >
								<input type="hidden" id="hidden_warp_prod_data" >
								
							</td>
							<td width="200">Ground Ends</td>
							<td>
								<input type="text" name="txt_ground_ends" id="txt_ground_ends" style="width:150px" class="text_boxes" />

							</td>
						</tr>
						<tr>
							<td width="200">Weft Yarn Lot and Brand</td>
							<td>
								<input type="text" name="txt_weft_yarn_lot_and_brand" id="txt_weft_yarn_lot_and_brand" placeholder="Double click to Browse" ondblclick="openmypage_yarn_lot(2)" style="width:150px" class="text_boxes"/>
								<input type="hidden" id="hidden_weft_prod_id" >
								<input type="hidden" id="hidden_weft_prod_data" >
								
							</td>
							<td width="200">Extra Selvedge Ends</td>
							<td>
								<input type="text" name="txt_extra_selvedge_ends" id="txt_extra_selvedge_ends" style="width:150px" class="text_boxes"/>
							</td>
						</tr>
						<tr>
							<td width="200">SPO Receive Date</td>
							<td>
								<input  type="text" style="width:150px" class="datepicker" placeholder="Select Date"  name="txt_spo_receive_date" id="txt_spo_receive_date"/>
							</td>
							<td width="200">Total Ends</td>
							<td>
								<input type="text" name="txt_total_ends" id="txt_total_ends" style="width:150px" class="text_boxes" onkeyup="groundEnds()" />
							</td>
						</tr>
						<tr>
							<td width="200"></td>
							<td>
								
							</td>
							<td width="200">Total Allowance</td>
							<td>
								<input type="text" name="txt_total_allowance " id="txt_total_allowance" style="width:150px" class="text_boxes"/>
							</td>
						</tr>
						<tr>
							<td width="200"><b>Previous Status</b></td>
							<td>
								<input  type="text" style="width:150px" class="text_boxes" placeholder="Previous Status"  name="txt_previous_status" id="txt_previous_status"/>
							</td>
							<td width="200"><b>Balance Qty</b></td>
							<td>
								<input type="text" name="txt_balance_qty" id="txt_balance_qty" style="width:150px" class="text_boxes_numeric"/>
							</td>
						</tr>
						<tr>
							<td width="200"><b>Template Process</b></td>
							<td>
								<?
									echo create_drop_down("cbo_template_id",160,"SELECT ID,TEMPLATE_NAME FROM LIB_TEMPLATE_PROCESS_MST WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0 GROUP BY ID,TEMPLATE_NAME","ID,TEMPLATE_NAME", 1, "-- Select --", $selected,"","",'');
								?>
							</td>
							
						</tr>
						<tr>
							<td colspan="4">&nbsp;</td>
						</tr>
						<tr>
							<td width="200">Warp Plan</td>
							<td>
								<input  type="text" style="width:150px" class="text_boxes_modify"  placeholder="Double click to Browse" ondblclick="open_warp_plan()"  name="warp_plan_data" id="warp_plan_data" readonly />
								
							</td>
							<td width="200">Weft Plan</td>
							<td>
								<input type="text" name="weft_plan_data" id="weft_plan_data" style="width:150px" class="text_boxes_modify" placeholder="Double click to Browse" ondblclick="open_weft_plan()" readonly />
								
							</td>
						</tr>
					</table>
				</fieldset>
                <table width="810">
                	<tr>
                		<td colspan="4" align="center" class="button_container">
                			<? 
                			echo load_submit_buttons($permission, "fnc_weaving_plan_entry", 0,0,"reset_form('weaving_plan_entry_1','weaving_plan_list_view','','','disable_enable_fields(\'cbo_company_id\',0)');",2);
                			?> 
                			<input type="button" class="formbutton" id="print" style="width:80px; display: none;" value="Print" onClick="fnc_weaving_plan_entry(4)" />

							<input type="hidden" id="dtls_id" value="">
                			
                		</td>	  
                	</tr>
                </table>
            </form>
			<div id="weaving_plan_break_down_list_view" style="margin-top:10px">
				<table align="right" cellspacing="0" width="810"  border="1" rules="all" class="rpt_table" id="yarn_dyeing_breakdown">
					
					<thead>
						
					</thead>
					<tbody>
						
					</tbody>
				</table>
			</div>
            <div id="weaving_plan_list_view" style="margin-top:10px"></div>
        </fieldset>
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
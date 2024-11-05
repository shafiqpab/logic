<?
/*-------------------------------------------- Commentssss
Purpose			: 	This form will create batch creation
Functionality	:
JS Functions	:
Created by		:	Kausar
Creation date 	: 	13.05.2013
Updated by 		: 	Fuad
Update date		: 	20.05.2013
Report by		:	Aziz
Creation date 	: 	7.05.2014
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

echo load_html_head_contents("Batch Creation Info", "../", 1, 1,'','','');
?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	var permission='<? echo $permission; ?>';
	var scanned_barcode=new Array();
	var scanned_lotNo=new Array();

	<?
		$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][408] );
		echo "var field_level_data= ". $data_arr . ";\n";

		if($_SESSION['logic_erp']['mandatory_field'][408]!="")
		{
			$mandatory_field_arr= json_encode( $_SESSION['logic_erp']['mandatory_field'][408] );
			echo "var mandatory_field_arr= ". $mandatory_field_arr . ";\n";
		}
	?>

	<?php
/*
|--------------------------------------------------------------------------
| for body part type
|--------------------------------------------------------------------------
|
 */
	$body_part_arr = return_library_array("SELECT id, body_part_type FROM lib_body_part WHERE status_active = 1 AND is_deleted =0", 'id', 'body_part_type');
	$jsbody_part_arr = json_encode($body_part_arr);
	echo "var body_part_arr = " . $jsbody_part_arr . ";\n";

	$jscolor_type_array= json_encode($color_type);
	echo "var js_colortype_name_array = ". $jscolor_type_array . ";\n";
?>

	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name", "color_name" ), 0, -1); ?>];
	$(document).ready(function(e)
	{
		$("#txt_batch_color").autocomplete({
			source: str_color
		});
	});

	function openmypage_fabricBooking()
	{
		var cbo_company_id 		= $('#cbo_company_id').val();
		var cbo_batch_against 	= $('#cbo_batch_against').val();
		var batch_for 			= $('#cbo_batch_for').val();

		if (form_validation('cbo_batch_against*cbo_batch_for*cbo_company_id','Batch Against*Batch For*Company')==false)
		{
			return;
		}
		else
		{
			var title 		= 'Booking Selection Form';
			var page_link 	= 'requires/batch_creation_controller.php?cbo_company_id='+cbo_company_id+'&batch_against='+cbo_batch_against+'&action=fabricBooking_popup';

			emailwindow 	= dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1150px,height=400px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var batch_against 			= $("#cbo_batch_against"). val();
				var theform 				= this.contentDoc.forms[0];
				var theemail 				= this.contentDoc.getElementById("hidden_booking_id").value;
				var theename 				= this.contentDoc.getElementById("hidden_booking_no").value;
				var theecolor_id 			= this.contentDoc.getElementById("hidden_color_id").value;
				var theecolor 				= this.contentDoc.getElementById("hidden_color").value;
				var job_no 					= this.contentDoc.getElementById("hidden_job_no").value;
				var booking_without_order 	= this.contentDoc.getElementById("booking_without_order").value;
				var search_type 			= this.contentDoc.getElementById("hidden_search_type").value;
				var within_group 			= this.contentDoc.getElementById("hidden_within_group").value;
				var hidden_sales_id 		= this.contentDoc.getElementById("hidden_sales_id").value;
				var hidden_color_type 		= this.contentDoc.getElementById("hidden_color_type").value;
				var hidden_entry_form 		= this.contentDoc.getElementById("hidden_entry_form").value;
				var hidden_sales_remarks 	= this.contentDoc.getElementById("hidden_sales_remarks").value;
				var is_sales 	 			= this.contentDoc.getElementById("hidden_is_sales").value;

				if(search_type == 7){
					var hidden_sales_booking_no=this.contentDoc.getElementById("hidden_sales_booking_no").value;
					$('#txt_sales_booking_no').val(hidden_sales_booking_no);
					//get_php_form_data(hidden_sales_id,'load_process_loss_from_fso','requires/batch_creation_controller');
					get_php_form_data(hidden_sales_id,'load_process_loss_from_fso','requires/batch_creation_controller' );
				}

				$('#txt_booking_no_id').val(theemail);
				$('#txt_booking_no').val(theename);
				$('#txt_batch_color_id').val(theecolor_id);
				$('#txt_batch_color').val(theecolor);
				$('#booking_without_order').val(booking_without_order);
				$('#txt_within_group').val(within_group);
				$('#txt_search_type').val(search_type);
				$('#txt_sales_id').val(hidden_sales_id);
				$('#txt_color_type').val(hidden_color_type);
				$('#txt_remarks').val(hidden_sales_remarks);
				$('#hidden_booking_entry_form').val(hidden_entry_form);

				reset_form('','','cboProgramNo_1*cboPoNo_1*poId_1*cboItemDesc_1*cboBodyPart_1*txtRollNo_1*barcodeNo_1*hideRollNo_1*txtBatchQnty_1*txt_total_batch_qnty*txtPoBatchNo_1*hide_job_no*txtQtyPcs_1*txtSize_1*txtLotNo_1*txtRemarks_1*txt_total_qtyPcs','',"$('#tbl_item_details tbody tr:not(:first)').remove();",'');

				var roll_maintained=$('#roll_maintained').val();
				var batch_maintained=$('#batch_maintained').val();
				var fabric_source=$('#fabric_source').val();
				//alert(booking_without_order+'='+is_sales);
				if(roll_maintained!=1)
				{
					if(booking_without_order==0) // With order, include salce order
					{
						load_drop_down( 'requires/batch_creation_controller', theename+"**"+search_type+"**"+cbo_company_id+"**"+is_sales+"**"+hidden_sales_id, 'load_drop_down_program', 'programNoTd_1' );
						var length=$("#cboProgramNo_1 option").length;
						if(length==2)
						{
							$('#cboProgramNo_1').val($('#cboProgramNo_1 option:last').val());
							load_drop_down( 'requires/batch_creation_controller', $('#cboProgramNo_1').val()+'**'+1+"**"+theename+'**'+theecolor_id+'**'+cbo_company_id+"**"+is_sales+"**"+hidden_sales_id, 'load_drop_down_po_from_program', 'poNoTd_1');
						}
						else
						{
							load_drop_down( 'requires/batch_creation_controller', theename+'**'+theecolor_id+'**'+is_sales+'**'+hidden_sales_id, 'load_drop_down_po', 'poNoTd_1' );
						}

						var po_length=$("#cboPoNo_1 option").length;
						if(po_length==2)
						{
							var program_no=$('#cboProgramNo_1').val();
							$('#cboPoNo_1').val($('#cboPoNo_1 option:last').val());
							load_drop_down( 'requires/batch_creation_controller', $('#cboPoNo_1').val()+'**'+1+'**'+booking_without_order+'**'+program_no+'**'+batch_maintained+'**'+$('#fabric_source').val(), 'load_drop_down_item_desc', 'itemDescTd_1' );
						}
						else
						{
							$("#cboItemDesc_1 option[value!='0']").remove();
						}

						var item_length=$("#cboItemDesc_1 option").length;
						if(item_length==2)
						{
							$('#cboItemDesc_1').val($('#cboItemDesc_1 option:last').val());
							var prod_id=$('#cboItemDesc_1').val();
							load_drop_down( 'requires/batch_creation_controller', $('#cboPoNo_1').val()+'**'+1+'**'+booking_without_order+'**'+prod_id, 'load_drop_down_body_part', 'bodyPartTd_1' );
							var body_part_length=$("#cboBodyPart_1 option").length;
							if(body_part_length==2)
							{
								$('#cboBodyPart_1').val($('#cboBodyPart_1 option:last').val());
							}
						}
					}
					else // Sample, without order
					{
						load_drop_down( 'requires/batch_creation_controller', theename+"**"+search_type+"**"+cbo_company_id+"**"+is_sales+"**"+hidden_sales_id, 'load_drop_down_program', 'programNoTd_1' );
						var prog_length=$("#programNoTd_1 option").length;
						$("#cboPoNo_1 option[value!='0']").remove();
						if (prog_length>1) // Sample, without order for program
						{
							if(prog_length==2)
							{
								$('#cboProgramNo_1').val($('#cboProgramNo_1 option:last').val());
								load_drop_down( 'requires/batch_creation_controller', $('#cboPoNo_1').val()+'**'+1+'**'+booking_without_order+'**'+$('#cboProgramNo_1').val()+'**'+batch_maintained+'**'+$('#fabric_source').val(), 'load_drop_down_item_desc', 'itemDescTd_1' );
							}
							else
							{
								$("#cboItemDesc_1 option[value!='0']").remove();
							}

							var item_length=$("#cboItemDesc_1 option").length;
							if(item_length==2)
							{
								// alert('multi item data');
								$('#cboItemDesc_1').val($('#cboItemDesc_1 option:last').val());
								var prod_id=$('#cboItemDesc_1').val();
								load_drop_down( 'requires/batch_creation_controller', theename+'**'+1+'**'+booking_without_order+'**'+prod_id+'**1**'+$('#cboProgramNo_1').val(), 'load_drop_down_body_part', 'bodyPartTd_1' );
								var body_part_length=$("#cboBodyPart_1 option").length;
								if(body_part_length==2)
								{
									$('#cboBodyPart_1').val($('#cboBodyPart_1 option:last').val());
								}
							}
						}
						else // Sample, without order without program
						{
							$("#cboProgramNo_1 option[value!='0']").remove();
							//$("#cboPoNo_1 option[value!='0']").remove();
							load_drop_down( 'requires/batch_creation_controller', theename+'**1**'+booking_without_order+"**0**"+batch_maintained, 'load_drop_down_item_desc', 'itemDescTd_1' );
							var item_length=$("#cboItemDesc_1 option").length;
							if(item_length==2)
							{
								// alert(theename+'Test');
								$('#cboItemDesc_1').val($('#cboItemDesc_1 option:last').val());
								var prod_id=$('#cboItemDesc_1').val();
								load_drop_down('requires/batch_creation_controller',theename+'**'+1+'**'+booking_without_order+'**'+prod_id,'load_drop_down_body_part','bodyPartTd_1');
								var body_part_length=$("#cboBodyPart_1 option").length;
								if(body_part_length==2)
								{
									$('#cboBodyPart_1').val($('#cboBodyPart_1 option:last').val());
								}
							}
						}
					}
				}
				else
				{
					<?
					/*$scanned_barcode_data=sql_select("select barcode_no from pro_roll_details where entry_form=64 and status_active=1 and is_deleted=0");
					foreach($scanned_barcode_data as $row)
					{
					$scanned_barcode_array[]=$row[csf('barcode_no')];
					}
					$jsscanned_barcode_array= json_encode($scanned_barcode_array);
					echo "scanned_barcode = ". $jsscanned_barcode_array . ";\n";*/
					?>
				}
				var booking =this.contentDoc.getElementById("hidden_booking_no").value; //Access form field with id="emailfield"
				show_list_view(booking+'**'+booking_without_order+'**'+batch_against+'**'+search_type+'**'+hidden_entry_form,'show_color_listview','list_color','requires/batch_creation_controller','');
			}
		}
	}

	function active_inactive()
	{
		reset_form('','list_color','txt_booking_no*txt_booking_no_id*txt_ext_no*cbo_color_range*txt_organic*txt_process_name*txt_process_id*txt_du_req_hr*txt_du_req_min*txt_exp_load_hr*txt_exp_load_min*txt_batch_color*cboProgramNo_1*cboPoNo_1*poId_1*cboItemDesc_1*cboBodyPart_1*cboDiaWidthType_1*txtRollNo_1*barcodeNo_1*hideRollNo_1*txtBatchQnty_1*txt_total_batch_qnty*txtPoBatchNo_1*hide_job_no*txtQtyPcs_1*txtSize_1*txtLotNo_1*txt_total_qtyPcs*cbo_shift_name*txt_dyeing_pdo*txtRemarks_1*txt_batch_delivery_date','','$(\'#tbl_item_details tbody tr:not(:first)\').remove();','');
		var batch_against= $('#cbo_batch_against').val();
		var batch_for= $('#cbo_batch_for').val();
		$('#booking_without_order').val(0);

		$('#programNoTd_1').html('<select name="cboProgramNo[]" id="cboProgramNo_1" class="combo_boxes" style="width:80px"><option value="0">-- Select --</option></select>');

		if(batch_against==1 || batch_against==3)
		{
			$('#txt_ext_no').attr('disabled','disabled');
			$('#txt_batch_color').attr('disabled','disabled');
			$('#txt_booking_no').removeAttr('disabled','disabled');
			$('#cbo_color_range').removeAttr('disabled','disabled');
			$('#txt_process_name').removeAttr('disabled','disabled');
			$('#txt_batch_number').removeAttr('readOnly','readOnly');
			//$('#txtBatchQnty_1').removeAttr('disabled','disabled');

			$('#poNoTd_1').html('<select name="cboPoNo[]" id="cboPoNo_1" class="combo_boxes" style="width:130px"><option value="0">-- Select Po Number --</option></select>');
			$('#itemDescTd_1').html('<select name="cboItemDesc[]" id="cboItemDesc_1" class="combo_boxes" style="width:180px"><option value="0">-- Select Item Desc --</option></select>');
			$('#bodyPartTd_1').html('<select name="cboBodyPart[]" id="cboBodyPart_1" class="combo_boxes" style="width:120px"><option value="0">-- Select Body Part --</option></select>');
			$('#txtRollNo_1').removeAttr('disabled','disabled');
		}
		else if(batch_against==2)
		{
			//$('#txt_ext_no').removeAttr('disabled','disabled');
			$('#txt_batch_number').val('');
			$('#txt_batch_number').attr('readOnly','readOnly');
			$('#txt_booking_no_id').val('');
			$('#txt_booking_no').attr('disabled','disabled');
			//$('#txtBatchQnty_1').attr('disabled','disabled');
			$('#txt_batch_color').attr('disabled','disabled');
			$('#update_id').val('');
			$('#hide_update_id').val('');
			$('#hide_batch_against').val('');
			$('#cbo_color_range').attr('disabled','disabled');
			$('#txt_process_name').attr('disabled','disabled');

			$('#programNoTd_1').html('<select name="cboProgramNo[]" id="cboProgramNo_1" class="combo_boxes" style="width:80px" disabled="disabled"><option value="0">-- Select --</option></select>');
			$('#poNoTd_1').html('<select name="cboPoNo[]" id="cboPoNo_1" class="combo_boxes" style="width:130px" disabled="disabled"><option value="0">-- Select Po Number --</option></select>');
			$('#itemDescTd_1').html('<select name="cboItemDesc[]" id="cboItemDesc_1" class="combo_boxes" style="width:180px" disabled="disabled"><option value="0">-- Select Item Desc --</option></select>');
			$('#bodyPartTd_1').html('<select name="cboBodyPart[]" id="cboBodyPart_1" class="combo_boxes" style="width:120px" disabled="disabled"><option value="0">-- Select Body Part --</option></select>');

			$('#txtRollNo_1').attr('disabled','disabled');

		}
		else if(batch_against==5)
		{
			$('#txt_ext_no').attr('disabled','disabled');
			$('#txt_batch_color').removeAttr('disabled','disabled');
			$('#txt_booking_no').attr('disabled','disabled');
			$('#txt_batch_number').removeAttr('readOnly','readOnly');
			//$('#txtBatchQnty_1').removeAttr('disabled','disabled');
			$('#cbo_color_range').removeAttr('disabled','disabled');
			$('#txt_process_name').removeAttr('disabled','disabled');

			$('#poNoTd_1').html('<input type="text" name="cboPoNo[]" id="cboPoNo_1" class="text_boxes" style="width:118px;" placeholder="Double Click to Search" onDblClick="openmypage_po(1)" readonly="readonly" />');
			$('#itemDescTd_1').html('<select name="cboItemDesc[]" id="cboItemDesc_1" class="combo_boxes" style="width:180px"><option value="0">-- Select Item Desc --</option></select>');
			$('#bodyPartTd_1').html('<select name="cboBodyPart[]" id="cboBodyPart_1" class="combo_boxes" style="width:120px"><option value="0">-- Select Body Part --</option></select>');
			$('#txtRollNo_1').removeAttr('disabled','disabled');
		}

		var roll_maintained=$('#roll_maintained').val();
		if(roll_maintained==1)
		{
			$('#cboProgramNo_1').attr('disabled','disabled');
			$('#cboPoNo_1').attr('disabled','disabled');
			$('#cboItemDesc_1').attr('disabled','disabled');
			$('#cboBodyPart_1').attr('disabled','disabled');
			$('#txtRollNo_1').attr('disabled','disabled');
		}
		else
		{
			$('#cboProgramNo_1').removeAttr('disabled','disabled');
			$('#cboPoNo_1').removeAttr('disabled','disabled');
			$('#cboItemDesc_1').removeAttr('disabled','disabled');
			$('#cboBodyPart_1').removeAttr('disabled','disabled');
			$('#txtRollNo_1').removeAttr('disabled','disabled');
		}
	}

	function active_inactive_delete()
	{
		reset_form('','','txt_ext_no*txt_batch_weight*update_id*txt_batch_sl_no*hide_update_id*cboProgramNo_1*cboPoNo_1*poId_1*cboItemDesc_1*cboBodyPart_1*cboDiaWidthType_1*txtRollNo_1*txtSize_1*txtLotNo_1*barcodeNo_1*hideRollNo_1*txtBatchQnty_1*txt_total_batch_qnty*txtPoBatchNo_1*hide_job_no*txt_deleted_id*txtQtyPcs_1*txt_total_qtyPcs*cbo_shift_name*txt_dyeing_pdo*txtRemarks_1','','$(\'#tbl_item_details tbody tr:not(:first)\').remove();','');
		var batch_against= $('#cbo_batch_against').val();
		var batch_for= $('#cbo_batch_for').val();
		$('#booking_without_order').val(0);

		$('#programNoTd_1').html('<select name="cboProgramNo[]" id="cboProgramNo_1" class="combo_boxes" style="width:80px"><option value="0">-- Select --</option></select>');

		if(batch_against==1 || batch_against==3)
		{
			$('#txt_ext_no').attr('disabled','disabled');
			$('#txt_batch_color').attr('disabled','disabled');
			$('#txt_booking_no').removeAttr('disabled','disabled');
			$('#cbo_color_range').removeAttr('disabled','disabled');
			$('#txt_process_name').removeAttr('disabled','disabled');
			$('#txt_batch_number').removeAttr('readOnly','readOnly');
			$('#txtBatchQnty_1').removeAttr('disabled','disabled');

			//$('#poNoTd_1').html('<select name="cboPoNo[]" id="cboPoNo_1" class="combo_boxes" style="width:130px"><option value="0">-- Select Po Number --</option></select>');
			$('#itemDescTd_1').html('<select name="cboItemDesc[]" id="cboItemDesc_1" class="combo_boxes" style="width:180px"><option value="0">-- Select Item Desc --</option></select>');
			$('#bodyPartTd_1').html('<select name="cboBodyPart[]" id="cboBodyPart_1" class="combo_boxes" style="width:120px"><option value="0">-- Select Body Part --</option></select>');
			$('#txtRollNo_1').removeAttr('disabled','disabled');
		}
		else if(batch_against==2)
		{
			$('#txt_ext_no').removeAttr('disabled','disabled');
			$('#txt_batch_number').val('');
			$('#txt_batch_number').attr('readOnly','readOnly');
			$('#txt_booking_no_id').val('');
			$('#txt_booking_no').attr('disabled','disabled');
			$('#txtBatchQnty_1').attr('disabled','disabled');
			$('#txt_batch_color').attr('disabled','disabled');
			$('#update_id').val('');
			$('#hide_update_id').val('');
			$('#hide_batch_against').val('');
			$('#cbo_color_range').attr('disabled','disabled');
			$('#txt_process_name').attr('disabled','disabled');

			//$('#programNoTd_1').html('<select name="cboProgramNo[]" id="cboProgramNo_1" class="combo_boxes" style="width:80px" disabled="disabled"><option value="0">-- Select --</option></select>');
			//$('#poNoTd_1').html('<select name="cboPoNo[]" id="cboPoNo_1" class="combo_boxes" style="width:130px" disabled="disabled"><option value="0">-- Select Po Number --</option></select>');
			$('#itemDescTd_1').html('<select name="cboItemDesc[]" id="cboItemDesc_1" class="combo_boxes" style="width:180px" disabled="disabled"><option value="0">-- Select Item Desc --</option></select>');
			$('#bodyPartTd_1').html('<select name="cboBodyPart[]" id="cboBodyPart_1" class="combo_boxes" style="width:120px" disabled="disabled"><option value="0">-- Select Body Part --</option></select>');

			$('#txtRollNo_1').attr('disabled','disabled');

		}
		else if(batch_against==5)
		{
			$('#txt_ext_no').attr('disabled','disabled');
			$('#txt_batch_color').removeAttr('disabled','disabled');
			$('#txt_booking_no').attr('disabled','disabled');
			$('#txt_batch_number').removeAttr('readOnly','readOnly');
			$('#txtBatchQnty_1').removeAttr('disabled','disabled');
			$('#cbo_color_range').removeAttr('disabled','disabled');
			$('#txt_process_name').removeAttr('disabled','disabled');

			//$('#poNoTd_1').html('<input type="text" name="cboPoNo[]" id="cboPoNo_1" class="text_boxes" style="width:118px;" placeholder="Double Click to Search" onDblClick="openmypage_po(1)" readonly="readonly" />');
			$('#itemDescTd_1').html('<select name="cboItemDesc[]" id="cboItemDesc_1" class="combo_boxes" style="width:180px"><option value="0">-- Select Item Desc --</option></select>');
			$('#bodyPartTd_1').html('<select name="cboBodyPart[]" id="cboBodyPart_1" class="combo_boxes" style="width:120px"><option value="0">-- Select Body Part --</option></select>');
			$('#txtRollNo_1').removeAttr('disabled','disabled');
		}

		var roll_maintained=$('#roll_maintained').val();
		if(roll_maintained==1)
		{
			$('#cboProgramNo_1').attr('disabled','disabled');
			$('#cboPoNo_1').attr('disabled','disabled');
			$('#cboItemDesc_1').attr('disabled','disabled');
			$('#cboBodyPart_1').attr('disabled','disabled');
			$('#txtRollNo_1').attr('disabled','disabled');
		}
		else
		{
			$('#cboProgramNo_1').removeAttr('disabled','disabled');
			$('#cboPoNo_1').removeAttr('disabled','disabled');
			$('#cboItemDesc_1').removeAttr('disabled','disabled');
			$('#cboBodyPart_1').removeAttr('disabled','disabled');
			$('#txtRollNo_1').removeAttr('disabled','disabled');
		}

		<?
		/*$scanned_barcode_data=sql_select("select barcode_no from pro_roll_details where entry_form=64 and status_active=1 and is_deleted=0");
		foreach($scanned_barcode_data as $row)
		{
		$scanned_barcode_array[]=$row[csf('barcode_no')];
		}
		$jsscanned_barcode_array= json_encode($scanned_barcode_array);
		echo "scanned_barcode = ". $jsscanned_barcode_array . ";\n";*/
		?>
	}

	function add_break_down_tr( i )
	{
		var is_approved=$('#is_approved_id').val();//approval requisition item Change not allowed
		if(is_approved==1)
		{
			alert("This Work Order is Approved. So Change Not Allowed");
			return;
		}

		if (form_validation('cbo_batch_against*cbo_batch_for*cbo_company_id','Batch Against*Batch For*Company Name')==false)
		{
			return;
		}

		var batch_against= $('#cbo_batch_against').val();
		var batch_for= $('#cbo_batch_for').val();
		var roll_maintained=$('#roll_maintained').val();
		var booking_without_order=$('#booking_without_order').val();

		//if(batch_against!=2)
		//{
		//var row_num=$('#tbl_item_details tbody tr').length;
		var lastTrId = $('#tbl_item_details tbody tr:last').attr('id').split('_');
		var row_num=lastTrId[1];
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;

			$("#tbl_item_details tbody tr:last").clone().find("input,select").each(function(){

				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { return name },
					'value': function(_, value) { return value }
				});

			}).end().appendTo("#tbl_item_details");

			$("#tbl_item_details tbody tr:last").removeAttr('id').attr('id','tr_'+i);
			$('#tr_' + i).find("td:eq(0)").removeAttr('id').attr('id','slTd_'+i);
			$('#tr_' + i).find("td:eq(0)").text(i);
			$('#tr_' + i).find("td:eq(1)").removeAttr('id').attr('id','programNoTd_'+i);
			$('#tr_' + i).find("td:eq(2)").removeAttr('id').attr('id','poNoTd_'+i);
			$('#tr_' + i).find("td:eq(3)").removeAttr('id').attr('id','itemDescTd_'+i);
			$('#tr_' + i).find("td:eq(4)").removeAttr('id').attr('id','bodyPartTd_'+i);
			$('#tr_' + i).find("td:eq(12)").removeAttr('id').attr('id','ColorTypeTd_'+i);
			$('#cboColorType_'+i).removeAttr("onChange").attr("onChange","fnc_copy_colorType("+i+");");

			$('#updateIdDtls_'+i).removeAttr("value").attr("value","");
			$('#poId_'+i).removeAttr("value").attr("value","");
			$('#txtRollNo_'+i).removeAttr("value").attr("value","");
			$('#barcodeNo_'+i).removeAttr("value").attr("value","");
			$('#hideRollNo_'+i).removeAttr("value").attr("value","");
			$('#txtBatchQnty_'+i).removeAttr("value").attr("value","");
			$('#txtPoBatchNo_'+i).removeAttr("value").attr("value","");
			$('#txtQtyPcs_'+i).removeAttr("value").attr("value","");
			$('#txtSize_'+i).removeAttr("value").attr("value","");
			$('#txtLotNo_'+i).removeAttr("value").attr("value","");
			$('#txtRemarks_'+i).removeAttr("value").attr("value","");

			if(batch_against!=5 && booking_without_order==0)
			{
				$("#cboItemDesc_"+i+" option[value!='0']").remove();
				$("#cboBodyPart_"+i+" option[value!='0']").remove();
			}

			$('#cboDiaWidthType_'+i).val(0);

			if(batch_against==5)
			{
				$("#cboProgramNo_"+i+" option[value!='0']").remove();
			}

			if(roll_maintained==1)
			{
				$('#cboProgramNo_'+i).attr('disabled','disabled');
				$('#cboPoNo_'+i).attr('disabled','disabled');
				$('#cboItemDesc_'+i).attr('disabled','disabled');
				$('#cboBodyPart_'+i).attr('disabled','disabled');
				$('#txtRollNo_'+i).attr('disabled','disabled');
				$('#txtRollNo_'+i).attr('placeholder','Display');
				//$('#txtRollNo_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_rollnum("+i+");");
			}
			else
			{
				$('#cboProgramNo_'+i).removeAttr('disabled','disabled');
				$('#cboPoNo_'+i).removeAttr('disabled','disabled');
				$('#cboItemDesc_'+i).removeAttr('disabled','disabled');
				$('#cboBodyPart_'+i).removeAttr('disabled','disabled');
				$('#txtRollNo_'+i).removeAttr('disabled','disabled');
				$('#txtRollNo_'+i).attr('placeholder','Write');
				//$('#txtRollNo_'+i).removeAttr('onDblClick','onDblClick');
			}

			if(batch_against==5)
			{
				if(roll_maintained!=1)
				{
					$('#cboPoNo_'+i).removeAttr("value").attr("value","");
				}
				$('#cboPoNo_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_po("+i+");");
			}
			else
			{
				$('#cboPoNo_'+i).val(0);
				$('#cboProgramNo_'+i).val(0);
			}

			$('#increase_'+i).removeAttr("value").attr("value","+");
			$('#decrease_'+i).removeAttr("value").attr("value","-");
			$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
		}

		set_all_onclick();
		calculate_batch_qnty();
		calculate_qtyPcs();
		//}
	}

	function fn_deleteRow(rowNo)
	{
			var is_approved=$('#is_approved_id').val();//approval requisition item Change not allowed
			if(is_approved==1)
			{
				alert("This Work Order is Approved. So Change Not Allowed");
				return;
			}
		//if($('#cbo_batch_against').val()!=2)
		//{
			var numRow = $('#tbl_item_details tbody tr').length;
			//if(numRow==rowNo && rowNo!=1)
			if( numRow==1)
			{
				return false;
			}
			if(rowNo!=0)
			{
				var updateIdDtls=$('#updateIdDtls_'+rowNo).val();
				var txt_deleted_id=$('#txt_deleted_id').val();
				var selected_id='';

				if(updateIdDtls!='')
				{
					if(txt_deleted_id=='') selected_id=updateIdDtls; else selected_id=txt_deleted_id+','+updateIdDtls;
					$('#txt_deleted_id').val( selected_id );
				}
				var bar_code =$("#barcodeNo_"+rowNo).val();
				var index = scanned_barcode.indexOf(bar_code);
				scanned_barcode.splice(index,1);
				//$('#tbl_item_details tbody tr:last').remove();
				$('#tr_'+rowNo).remove();
			}
			else
			{
				return false;
			}

			calculate_batch_qnty();
			calculate_qtyPcs();
		//}
	}

	function calculate_batch_qnty()
	{
		var total_batch_qnty='';
		$("#tbl_item_details tbody").find('tr').each(function()
		{
			var batchQnty=$(this).find('input[name="txtBatchQnty[]"]').val();
			total_batch_qnty=total_batch_qnty*1+batchQnty*1;
		});

		$('#txt_total_batch_qnty').val(total_batch_qnty.toFixed(2));
		//var numRow = $('#tbl_item_details tbody tr').length;
		//var ddd={ dec_type:2, comma:0}
		//math_operation( "txt_total_batch_qnty", "txtBatchQnty_", "+",numRow,ddd );
		/*var lastTrId = $('#tbl_item_details tbody tr:last').attr('id').split('_');
		var numRow=lastTrId[1]; var total_batch_qnty=0;
		for(var i=1; i<=numRow; i++)
		{
			var batchQnty=$('#txtBatchQnty_'+i).val()*1;
			if(batchQnty!="undefined")
			{
				total_batch_qnty=total_batch_qnty*1+batchQnty*1;
			}
			try
			{

			}
			catch(e)
			{
				//got error no operation
			}
			finally
			{
				total_batch_qnty=total_batch_qnty*1+batchQnty*1;
			}
		}

		$('#txt_total_batch_qnty').val(total_batch_qnty);*/

		var txt_batch_weight = $('#txt_total_batch_qnty').val()*1 + $('#txt_tot_trims_weight').val()*1;
		$("#txt_batch_weight").val(txt_batch_weight.toFixed(2));
	}

	function calculate_qtyPcs()
	{
		var total_qtyPcs='';
		var total_collar_qtyPcs=0;
		var total_cuff_qtyPcs=0;

		$("#tbl_item_details tbody").find('tr').each(function(){
			var qtyPcs=$(this).find('input[name="txtQtyPcs[]"]').val();
			total_qtyPcs=total_qtyPcs*1+qtyPcs*1;

			//for cuff and collar qty pcs.
			var bodyPartId=$(this).find('select[name="cboBodyPart[]"]').val();
			if(body_part_arr[bodyPartId] == 40)
			{
				total_collar_qtyPcs=total_collar_qtyPcs*1+qtyPcs*1;
			}
			else if(body_part_arr[bodyPartId] == 50)
			{
				total_cuff_qtyPcs=total_cuff_qtyPcs*1+qtyPcs*1;
			}
		});

		$('#txt_total_qtyPcs').val(total_qtyPcs);
		$('#txt_color_qty').val(total_collar_qtyPcs);
		$('#txt_cuff_qty').val(total_cuff_qtyPcs);
	}

	function roll_maintain()
	{
		reset_form('','list_color','txt_booking_no*txt_booking_no_id*booking_without_order*txt_batch_color*cboProgramNo_1*cboPoNo_1*poId_1*cboItemDesc_1*cboBodyPart_1*txtRollNo_1*barcodeNo_1*hideRollNo_1*txtBatchQnty_1*txt_total_batch_qnty*txtPoBatchNo_1*hide_job_no*txtQtyPcs_1*txtSize_1*txtLotNo_1*txt_total_qtyPcs*cbo_shift_name*txt_dyeing_pdo*txtRemarks_1','','$(\'#tbl_item_details tbody tr:not(:first)\').remove();','');
		get_php_form_data($('#cbo_company_id').val(),'roll_maintained','requires/batch_creation_controller' );

		var roll_maintained=$('#roll_maintained').val();
		var batch_against= $('#cbo_batch_against').val();
		var batch_for= $('#cbo_batch_for').val();

		if(batch_against==1 || batch_against==3)
		{
			$('#txtRollNo_1').removeAttr('disabled','disabled');
		}
		else if(batch_against==2)
		{
			$('#txtRollNo_1').attr('disabled','disabled');
		}
		else
		{
			$('#txtRollNo_1').removeAttr('disabled','disabled');
		}

		if(roll_maintained==1)
		{
			$('#cboProgramNo_1').attr('disabled','disabled');
			$('#cboPoNo_1').attr('disabled','disabled');
			$('#cboItemDesc_1').attr('disabled','disabled');
			$('#cboBodyPart_1').attr('disabled','disabled');
			$('#txtRollNo_1').attr('disabled','disabled');
			$('#txtRollNo_1').attr('placeholder','Display');
			//$('#txtRollNo_1').attr('onDblClick','openmypage_rollnum(1);');
			$('#barcode_div').show();
		}
		else
		{
			$('#cboProgramNo_1').removeAttr('disabled','disabled');
			$('#cboPoNo_1').removeAttr('disabled','disabled');
			$('#cboItemDesc_1').removeAttr('disabled','disabled');
			$('#cboBodyPart_1').removeAttr('disabled','disabled');
			$('#txtRollNo_1').removeAttr('disabled','disabled');
			$('#txtRollNo_1').attr('placeholder','Write');
			//$('#txtRollNo_1').removeAttr('onDblClick','onDblClick');
			$('#barcode_div').hide();
		}
	}

	function openmypage_rollnum(row_no)
	{
		var cbo_company_id=$('#cbo_company_id').val();
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}

		var title = 'Roll Selection Form';
		var page_link = 'requires/batch_creation_controller.php?cbo_company_id='+cbo_company_id+'&action=roll_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=600px,height=350px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("hidden_roll_no").value;	 //Access form field with id="emailfield"
			var rollId=this.contentDoc.getElementById("hidden_roll_table_id").value;	 //Access form field with id="emailfield"
			var rollQnty=this.contentDoc.getElementById("hidden_roll_qnty").value;	 //Access form field with id="emailfield"

			$('#txtRollNo_'+row_no).val(theemail);
			$('#hideRollNo_'+row_no).val(rollId);
			$('#txtBatchQnty_'+row_no).val(rollQnty);
			calculate_batch_qnty();
			calculate_qtyPcs();
		}
	}

	function openmypage_po(row_no)
	{
		var cbo_company_id=$('#cbo_company_id').val();
		var batch_against=$('#cbo_batch_against').val();
		var hide_job_no=$('#hide_job_no').val();
		var no_of_row=$('#tbl_item_details tbody tr').length;

		if(form_validation('cbo_batch_against*cbo_company_id','Batch Against*Company')==false)
		{
			return;
		}

		var title = 'PO Selection Form';
		var page_link = 'requires/batch_creation_controller.php?cbo_company_id='+cbo_company_id+'&batch_against='+batch_against+'&hide_job_no='+hide_job_no+'&no_of_row='+no_of_row+'&action=po_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=370px,center=1,resize=1,scrolling=0','');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var po_id=this.contentDoc.getElementById("po_id").value; //Access form field with id="emailfield"
			var po_no=this.contentDoc.getElementById("po_no").value; //Access form field with id="emailfield"
			var job_no=this.contentDoc.getElementById("job_no").value; //Access form field with id="emailfield"

			$('#poId_'+row_no).val(po_id);
			$('#cboPoNo_'+row_no).val(po_no);
			$('#hide_job_no').val(job_no);

			load_drop_down( 'requires/batch_creation_controller', po_id+"**"+row_no, 'load_drop_down_program_against_po', 'programNoTd_'+row_no );
			var length=$("#cboProgramNo_"+row_no+" option").length;
			if(length==2)
			{
				$('#cboProgramNo_'+row_no).val($('#cboProgramNo_'+row_no+' option:last').val());
			}

			var program_no=$('#cboProgramNo_'+row_no).val();
			var batch_maintained=$('#batch_maintained').val();
			load_drop_down( 'requires/batch_creation_controller', po_id+"**"+row_no+"**0**"+program_no+"**"+batch_maintained, 'load_drop_down_item_desc', 'itemDescTd_'+row_no );
			var item_length=$("#cboItemDesc_"+row_no+" option").length;
			if(item_length==2)
			{
				$('#cboItemDesc_'+row_no).val($('#cboItemDesc_'+row_no+' option:last').val());
				var prod_id=$('#cboItemDesc_'+row_no).val();
				load_drop_down( 'requires/batch_creation_controller', po_id+'**'+row_no+'**0**'+prod_id, 'load_drop_down_body_part', 'bodyPartTd_'+row_no );
				var body_part_length=$('#cboBodyPart_'+row_no+' option').length;
				if(body_part_length==2)
				{
					$('#cboBodyPart_'+row_no).val($('#cboBodyPart_'+row_no+' option:last').val());
				}
			}
			$('#txtPoBatchNo_'+row_no).val('');
		}
	}

	function generate_report_file(data,action,page)
	{
		window.open("requires/batch_creation_controller.php?data=" + data+'&action='+action, true );
	}

	function fnc_batch_creation(operation)
	{
		if(operation == 1 || operation == 2)
		{
			var is_approved=$('#is_approved_id').val();//approval requisition item Change not allowed
			if(is_approved==1)
			{
				alert("This Work Order is Approved. So Change Not Allowed");
				return;
			}
		}

		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+$('#cbo_working_company_id').val(),'batch_card_print','requires/batch_creation_controller');
			return;
		}
		else if(operation==5)//Print Button 2;
		{
			var update_id=$('#update_id').val();

			if(update_id=="")
			{
				alert("Save Data First");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+$('#txt_booking_no_id').val()+'*'+$('#cbo_working_company_id').val()+'*'+$('#roll_maintained').val(),'batch_card_print_2','requires/batch_creation_controller');
			return;
		}
		else if(operation==6)//Print Button 3;
		{
			var update_id=$('#update_id').val();

			if(update_id=="")
			{
				alert("Save Data First");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+$('#txt_booking_no_id').val()+'*'+$('#cbo_working_company_id').val(),'batch_card_print_3','requires/batch_creation_controller');
			return;
		}
		else if(operation==7)//Print Button 4;
		{
			var update_id=$('#update_id').val();

			if(update_id=="")
			{
				alert("Save Data First");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+$('#txt_booking_no_id').val()+'*'+$('#cbo_working_company_id').val()+'*'+$('#roll_maintained').val(),'batch_card_print_4','requires/batch_creation_controller');
			return;
		}
		else if(operation==8)//Print Button 5;
		{
			var update_id=$('#update_id').val();

			if(update_id=="")
			{
				alert("Save Data First");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+$('#txt_booking_no_id').val()+'*'+$('#cbo_working_company_id').val()+'*'+$('#roll_maintained').val(),'batch_card_print_5','requires/batch_creation_controller');
			return;
		}
		else if(operation==9)//Print Button 6;
		{
			var update_id=$('#update_id').val();

			if(update_id=="")
			{
				alert("Save Data First");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+$('#txt_booking_no_id').val()+'*'+$('#cbo_working_company_id').val()+'*'+$('#roll_maintained').val(),'batch_card_print_6','requires/batch_creation_controller');
			return;
		}
		else if(operation==10)//Print Button 7;
		{
			var update_id=$('#update_id').val();

			if(update_id=="")
			{
				alert("Save Data First");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+$('#txt_booking_no_id').val()+'*'+$('#cbo_working_company_id').val()+'*'+$('#roll_maintained').val(),'batch_card_print_7','requires/batch_creation_controller');
			return;
		}
		else if(operation==11)//Print Button 8;
		{
			var update_id=$('#update_id').val();

			if(update_id=="")
			{
				alert("Save Data First");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+$('#txt_booking_no_id').val()+'*'+$('#cbo_working_company_id').val()+'*'+$('#roll_maintained').val(),'batch_card_print_8','requires/batch_creation_controller');
			return;
		}
		else if(operation==12)//Print Button 9;
		{
			var update_id=$('#update_id').val();

			if(update_id=="")
			{
				alert("Save Data First");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+$('#txt_booking_no_id').val()+'*'+$('#cbo_working_company_id').val()+'*'+$('#roll_maintained').val(),'batch_card_print_9','requires/batch_creation_controller');
			return;
		}
		else if(operation==13)//Print Button 10;
		{
			var update_id=$('#update_id').val();

			if(update_id=="")
			{
				alert("Save Data First");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+$('#txt_booking_no_id').val()+'*'+$('#cbo_working_company_id').val()+'*'+$('#roll_maintained').val(),'batch_card_print_10','requires/batch_creation_controller');
			return;
		}
		else if(operation==14)//Print Button 11;
		{
			var update_id=$('#update_id').val();

			if(update_id=="")
			{
				alert("Save Data First");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+$('#txt_booking_no_id').val()+'*'+$('#cbo_working_company_id').val()+'*'+$('#roll_maintained').val(),'batch_card_print_11','requires/batch_creation_controller');
			return;
		}
		else if(operation==15)//Print Button 12;
		{
			var update_id=$('#update_id').val();

			if(update_id=="")
			{
				alert("Save Data First");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+$('#txt_booking_no_id').val()+'*'+$('#cbo_working_company_id').val()+'*'+$('#roll_maintained').val(),'batch_card_print_12','requires/batch_creation_controller');
			return;
		}
		else if(operation==16)//Prog.Wise;
		{
			var update_id=$('#update_id').val();

			if(update_id=="")
			{
				alert("Save Data First");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+$('#txt_booking_no_id').val()+'*'+$('#cbo_working_company_id').val()+'*'+$('#roll_maintained').val(),'batch_card_prog_wise','requires/batch_creation_controller');
			return;
		}
		else if(operation==17)//Print Button 14;
		{
			var update_id=$('#update_id').val();

			if(update_id=="")
			{
				alert("Save Data First");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+$('#txt_booking_no_id').val()+'*'+$('#cbo_working_company_id').val()+'*'+$('#roll_maintained').val(),'batch_card_print_14','requires/batch_creation_controller');
			return;
		}
		else if(operation==18)// Batch Card 15;
		{
			var update_id=$('#update_id').val();

			if(update_id=="")
			{
				alert("Save Data First");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+$('#txt_booking_no_id').val()+'*'+$('#cbo_working_company_id').val()+'*'+$('#roll_maintained').val(),'batch_card_print_15','requires/batch_creation_controller');
			return;
		}
		else if(operation==19)//Print Button 2;
		{
			var update_id=$('#update_id').val();

			if(update_id=="")
			{
				alert("Save Data First");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+$('#txt_booking_no_id').val()+'*'+$('#cbo_working_company_id').val()+'*'+$('#roll_maintained').val(),'batch_card_print_19','requires/batch_creation_controller');
			return;
		}
		else if(operation==20) // Batch Card 17;
		{
			var update_id=$('#update_id').val();

			if(update_id=="")
			{
				alert("Save Data First");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+$('#txt_booking_no_id').val()+'*'+$('#cbo_working_company_id').val()+'*'+$('#roll_maintained').val(),'batch_card_print_17','requires/batch_creation_controller');
			return;
		}
		else if(operation==23) // Batch Card 17 back;
		{
			var update_id=$('#update_id').val();

			if(update_id=="")
			{
				alert("Save Data First");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+$('#txt_booking_no_id').val()+'*'+$('#cbo_working_company_id').val()+'*'+$('#roll_maintained').val(),'batch_card_print_17_back','requires/batch_creation_controller');
			return;
		}
		else if(operation==21)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+$('#cbo_working_company_id').val()+'*'+$('#cbo_batch_against').val(),'batch_card_print_18','requires/batch_creation_controller');
			return;
		}
		else if(operation==22)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+$('#cbo_working_company_id').val(),'batch_card_print_20','requires/batch_creation_controller');
			return;
		}
		else if(operation==24)//Print Button 20;
		{
			var update_id=$('#update_id').val();

			if(update_id=="")
			{
				alert("Save Data First");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+$('#txt_booking_no_id').val()+'*'+$('#cbo_working_company_id').val()+'*'+$('#roll_maintained').val(),'batch_card_print_21','requires/batch_creation_controller');
			return;
		}
		else if(operation==25)//Print Button 21;
		{
			var update_id=$('#update_id').val();

			if(update_id=="")
			{
				alert("Save Data First");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+$('#txt_booking_no_id').val()+'*'+$('#cbo_working_company_id').val()+'*'+$('#roll_maintained').val(),'batch_card_print_22','requires/batch_creation_controller');
			return;
		}
		else if(operation==26)//Print Button 22;
		{
			var update_id=$('#update_id').val();

			if(update_id=="")
			{
				alert("Save Data First");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+$('#txt_booking_no_id').val()+'*'+$('#cbo_working_company_id').val()+'*'+$('#roll_maintained').val()+'*'+$('#txt_ext_no').val(),'batch_card_print_23','requires/batch_creation_controller');
			return;
		}
		else if(operation==27)//Print Button atch Card 2.1;
		{
			var update_id=$('#update_id').val();

			if(update_id=="")
			{
				alert("Save Data First");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+$('#txt_booking_no_id').val()+'*'+$('#cbo_working_company_id').val()+'*'+$('#roll_maintained').val(),'batch_card_print_2v1','requires/batch_creation_controller');
			return;
		}

		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][408]); ?>')
		{
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][408]); ?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][408]); ?>')==false) {return;}
		}

		if( $("#ext_from").val() != 0)
		{
			alert("This Batch No is already extended. Update is not allowed.");
			return;
		}
		if($('#batch_no_creation').val()!=1)
		{
			if( form_validation('txt_batch_number','Batch Number')==false )
			{
				alert("Plesae Insert Batch No.");
				$('#txt_batch_number').focus();
				return;
			}
		}

		if($('#txt_batch_weight').val()*1 < 0.1)
		{
			alert('Please Insert Batch Weight.');
			$('#txt_batch_weight').focus();
			return;
		}

		if( form_validation('cbo_batch_against*cbo_batch_for*cbo_company_id*txt_batch_date*txt_batch_weight*cbo_working_company_id*txt_batch_color*txt_process_name','Batch Against*Batch For*Company*Batch Date*Batch Weight*Working Company*Batch Color*Process')==false )
		{
			return;
		}

		if(($('#cbo_batch_against').val()==1 || $('#cbo_batch_against').val()==3) && $('#txt_booking_no').val()=="")
		{
			alert("Please Select Booking No");
			$('#txt_booking_no').focus();
			return;
		}

		if($('#cbo_batch_against').val()==2 && $('#txt_ext_no').val()=="")
		{
			alert("Please Insert Extention No.");
			$('#txt_ext_no').focus();
			return;
		}
		var save_data=$('#save_data').val();

		var txt_batch_weight=$('#txt_batch_weight').val();
		var batch_qty=$('#txt_total_batch_qnty').val()*1+$('#txt_tot_trims_weight').val()*1;
		var tot_batch_weight=Math.round(batch_qty * 1e12) / 1e12;

		if(txt_batch_weight!=tot_batch_weight)
		{
			alert('Batch Weight and Total Batch Qnty+Trims Weight should be same.');
			return;
		}

		if( save_data != "" && $("#txt_tot_trims_weight").val() =="" )
		{
			alert('Total Trims Weight does not syncronized with Trims Weight popup.');
			return;
		}

		var style_breakdown_variable_sett=$('#style_breakdown_variable_sett').val();
		if (style_breakdown_variable_sett==1)
		{
			var txt_style_breakdown_qnty=$('#txt_style_breakdown_qnty').val()*1;
			if(txt_style_breakdown_qnty>txt_batch_weight)
			{
				alert("Batch weight can not be greater than style breakdown qnty");
				return;
			}
		}
		// alert(txt_style_breakdown_qnty +'='+ txt_batch_weight +'='+ tot_batch_weight);return;


		var txt_deleted_id=$('#txt_deleted_id').val();
		var row_num=$('#tbl_item_details tbody tr').length;
		var data_all="";

		/*for (var i=1; i<=row_num; i++)
		{
			if($('#cbo_batch_against').val()==1 || $('#cbo_batch_against').val()==5)
			{
				if (form_validation('cboPoNo_'+i,'PO NO')==false)
				{
					return;
				}
			}

			if (form_validation('cboItemDesc_'+i+'*txtBatchQnty_'+i+'*cboDiaWidthType_'+i,'Item Description*Batch Qnty*Dia/ W. Type')==false)
			{
				return;//cboDiaWidthType_1
			}

			var po_field='';
			if($('#cbo_batch_against').val()==5)
			{
				po_field='poId_'+i;
			}
			else if($('#cbo_batch_against').val()==2)
			{
				if($('#hide_batch_against').val()==5)
				{
					po_field='poId_'+i;
				}
				else
				{
					po_field='cboPoNo_'+i;
				}
			}
			else
			{
				po_field='cboPoNo_'+i;
			}

			data_all+=get_submitted_data_string(po_field+'*cboItemDesc_'+i+'*cboBodyPart_'+i+'*txtRollNo_'+i+'*hideRollNo_'+i+'*txtBatchQnty_'+i+'*updateIdDtls_'+i+'*cboProgramNo_'+i+'*txtPoBatchNo_'+i+'*cboDiaWidthType_'+i+'*barcodeNo_'+i,"../",i);
		}*/

		var j=0; var breakOut = true; var error=0; error_barcode=all_po_ids='';
		$("#tbl_item_details tbody").find('tr').each(function()
		{
			if(breakOut==false || error==1)
			{
				return;
			}
			var trId = $(this).attr('id').split('_');
			var i=trId[1];

			var roll_maintained_id = $('#roll_maintained').val();
			if(roll_maintained_id==1)
			{
				if(operation!=2 && operation!=1)
				{
					if(($('#hiddenRollqty_'+i).val()*1)!=($('#txtBatchQnty_'+i).val()*1))
					{
						alert($('#hiddenRollqty_'+i).val()+"***"+$('#txtBatchQnty_'+i).val())
						error=1;
						error_barcode=$('#barcodeNo_'+i).val();
						return;
					}
				}
			}

			if($('#cbo_batch_against').val()==1 || $('#cbo_batch_against').val()==5)
			{
				if (form_validation('cboPoNo_'+i,'PO NO')==false)
				{
					breakOut = false;
					return false;
				}
			}

			if (form_validation('cboItemDesc_'+i+'*cboBodyPart_'+i+'*txtBatchQnty_'+i+'*cboDiaWidthType_'+i,'Item Description*Body Part*Batch Qnty*Dia/ W. Type')==false)
			{
				breakOut = false;
				return false;
			}

			var po_field='';
			if($('#cbo_batch_against').val()==5)
			{
				po_field='poId_';
			}
			else if($('#cbo_batch_against').val()==2)
			{
				if($('#hide_batch_against').val()==5)
				{
					po_field='poId_';
				}
				else
				{
					po_field='cboPoNo_';
				}
			}
			else
			{
				po_field='cboPoNo_';
			}

			j++;
			data_all+="&"+po_field + j + "='" + $('#'+po_field+i).val()+"'"+"&cboItemDesc_" + j + "='" + $('#cboItemDesc_'+i).val()+"'"+"&cboBodyPart_" + j + "='" + $('#cboBodyPart_'+i).val()+"'"+"&txtRollNo_" + j + "='" + $('#txtRollNo_'+i).val()+"'"+"&hideRollNo_" + j + "='" + $('#hideRollNo_'+i).val()+"'"+"&txtBatchQnty_" + j + "='" + $('#txtBatchQnty_'+i).val()+"'"+"&updateIdDtls_" + j + "='" + $('#updateIdDtls_'+i).val()+"'"+"&cboProgramNo_" + j + "='" + $('#cboProgramNo_'+i).val()+"'"+"&txtPoBatchNo_" + j + "='" + $('#txtPoBatchNo_'+i).val()+"'"+"&cboDiaWidthType_" + j + "='" + $('#cboDiaWidthType_'+i).val()+"'"+"&barcodeNo_" + j + "='" + $('#barcodeNo_'+i).val()+"'"+"&txtQtyPcs_" + j + "='" + $('#txtQtyPcs_'+i).val()+"'"+"&txtSize_" + j + "='" + $('#txtSize_'+i).val()+"'"+"&txtRemarks_" + j + "='" + $('#txtRemarks_'+i).val()+"'"+"&cboColorType_" + j + "='" + $('#cboColorType_'+i).val()+"'";

			all_po_ids += $("#"+po_field + i).val() + ",";
		});

		if(error==1)
		{
			alert("Roll Qty and Batch Qty not match in Barcode No "+error_barcode);
			return;
		}

		if(breakOut==false)
		{
			return;
		}

		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_batch_sl_no*cbo_batch_against*cbo_batch_for*cbo_company_id*batch_no_creation*batch_maintained*txt_batch_number*txt_batch_date*txt_batch_weight*txt_tot_trims_weight*txt_booking_no_id*txt_booking_no*txt_ext_no*txt_batch_color*cbo_color_range*txt_organic*txt_process_id*txt_du_req_hr*txt_du_req_min*txt_exp_load_hr*txt_exp_load_min*update_id*booking_without_order*hide_update_id*hide_batch_against*roll_maintained*txt_remarks*cbo_ready_to_approved*txt_cuff_qty*txt_color_qty*cbo_machine_name*save_data*txt_sales_booking_no*txt_search_type*txt_sales_id*cbo_working_company_id*txt_process_seq*cbo_floor*unloaded_batch*ext_from*hidden_booking_entry_form*txt_service_booking*service_booking_id*txt_batch_color_id*cbo_double_dyeing*cbo_batch_type*cbo_shift_name*txt_dyeing_pdo*style_breakdown_str*txt_batch_delivery_date',"../")+data_all+'&total_row='+row_num+'&txt_deleted_id='+txt_deleted_id+'&all_po_ids='+all_po_ids;
		//alert(data);return;
		freeze_window(operation);

		http.open("POST","requires/batch_creation_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_batch_creation_Reply_info;
	}

	function fnc_batch_creation_Reply_info()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');

			show_msg(reponse[0]);
			if(reponse[0]==15)
			{
				setTimeout('fnc_batch_creation('+ reponse[1] +')',8000);
				return;
			}
			else if(reponse[0]==13)
			{
				alert("Batch is used");
				release_freezing();
				return;
			}
			else if(reponse[0]==23 || reponse[0]==11)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			else if(reponse[0]==14)
			{
				alert(reponse[2]);
				release_freezing();
				return;
			}
			else if(reponse[0]==17)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			else if(reponse[0]==101)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			else if((reponse[0]==0 || reponse[0]==1))
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_batch_sl_no').value = reponse[2];
				document.getElementById('txt_batch_number').value = reponse[3];
				var batch_against=$('#cbo_batch_against').val();
				$('#txt_booking_no').attr('disabled',true);
				if(batch_against==2)
				{
					document.getElementById('hide_update_id').value = reponse[1];
				}
				else
				{
					document.getElementById('hide_update_id').value = '';
				}

				var batch_for=$('#cbo_batch_for').val();
				var roll_maintained=$('#roll_maintained').val();
				var batch_maintained=$('#batch_maintained').val();

				show_list_view(batch_against+'**'+batch_for+'**'+reponse[1]+'**'+roll_maintained+'**'+batch_maintained,'batch_details','batch_details_container','requires/batch_creation_controller','');

				$('#txt_deleted_id').val('');
				set_button_status(1, permission, 'fnc_batch_creation',1);
			}
			else if(reponse[0]==2)
			{
				var batch_for=$('#cbo_batch_for').val();
				var roll_maintained=$('#roll_maintained').val();
				var batch_maintained=$('#batch_maintained').val();
				active_inactive_delete();
				set_button_status(0, permission, 'fnc_batch_creation',1);
			}
			release_freezing();
		}
	}

	function openmypage_batchNo()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var batch_against = $('#cbo_batch_against').val();
		var batch_for = $('#cbo_batch_for').val();
		var roll_maintained = $('#roll_maintained').val();
		var batch_maintained=$('#batch_maintained').val();
		var search_type = $('#txt_search_type').val();

		if (form_validation('cbo_batch_against*cbo_batch_for*cbo_company_id','Batch Against*Batch For*Company')==false)
		{
			return;
		}
		else
		{
			var title = 'Batch No Selection Form';
			var page_link = 'requires/batch_creation_controller.php?cbo_company_id='+cbo_company_id+'&batch_against='+batch_against+'&action=batch_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=370px,center=1,resize=1,scrolling=0','');

			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;	 //Access form field with id="emailfield"
				var batch_no=this.contentDoc.getElementById("hidden_batch_no").value;
				var load_unload=this.contentDoc.getElementById("hidden_load_unload").value;
				var unloaded_batch=this.contentDoc.getElementById("hidden_unloaded_batch").value;
				var ext_from=this.contentDoc.getElementById("hidden_ext_from").value;

				//alert(load_unload);return;
				if(batch_id!="")
				{
					freeze_window(5);
					get_php_form_data(batch_against+'**'+batch_for+'**'+batch_id+'**'+batch_no+'**'+cbo_company_id+'**'+unloaded_batch+'**'+ext_from, "populate_data_from_search_popup", "requires/batch_creation_controller" );
					if(load_unload == 1)
					{
						$('#txt_booking_no').attr('disabled','disabled');
					}
					show_list_view(batch_against+'**'+batch_for+'**'+batch_id+'**'+roll_maintained+'**'+batch_maintained,'batch_details','batch_details_container','requires/batch_creation_controller','');
					release_freezing();
					if(roll_maintained==1)
					{
					<?
					/*$scanned_barcode_data=sql_select("select barcode_no from pro_roll_details where entry_form=64 and status_active=1 and is_deleted=0");
					foreach($scanned_barcode_data as $row)
					{
					$scanned_barcode_array[]=$row[csf('barcode_no')];
					}
					$jsscanned_barcode_array= json_encode($scanned_barcode_array);
					echo "scanned_barcode = ". $jsscanned_barcode_array . ";\n";*/
					?>
					}
					$('#txt_deleted_id').val('');
					calculate_batch_qnty();
					calculate_qtyPcs();
				}
			}
		}
	}

	function openmypage_process()
	{
		var txt_process_id = $('#txt_process_id').val();
		var txt_process_seq = $('#txt_process_seq').val();


		var title = 'Process Name Selection Form';
		var page_link = 'requires/batch_creation_controller.php?txt_process_id='+txt_process_id+'&process_seq='+txt_process_seq+'&action=process_name_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var process_id=this.contentDoc.getElementById("hidden_process_id").value;	 //Access form field with id="emailfield"
			var process_name=this.contentDoc.getElementById("hidden_process_name").value;
			var process_seq=this.contentDoc.getElementById("hidden_process_seq").value;

			$('#txt_process_id').val(process_id);
			$('#txt_process_name').val(process_name);
			$('#txt_process_seq').val(process_seq);
		}
	}

	function load_item_desc(value,id)
	{
		//alert(value+'='+id);
		var item_id=id.split("_");
		var row_no=item_id[1];
		var itemTdId='itemDescTd_'+row_no;
		var booking_without_order=$('#booking_without_order').val();
		var booking_no=$('#txt_booking_no').val();
		var color=$('#txt_batch_color').val();
		var batch_against=$('#cbo_batch_against').val();
		var batch_maintained=$('#batch_maintained').val();
		var fabric_source=$('#fabric_source').val();

		if(item_id[0]=='cboProgramNo')
		{
			if(batch_against!=5) // not without booking
			{
				if (booking_without_order==0) // with order
				{
					var po_length=$("#cboPoNo_"+row_no+" option").length;
					if(po_length==2)
					{
						$('#cboPoNo_'+row_no).val($('#cboPoNo_'+row_no+' option:last').val());
						load_drop_down( 'requires/batch_creation_controller', $('#cboPoNo_'+row_no).val()+'**'+row_no+'**'+booking_without_order+'**'+value+'**'+batch_maintained+'**'+fabric_source, 'load_drop_down_item_desc', itemTdId );
					}
					else
					{
						$("#cboItemDesc_"+row_no+" option[value!='0']").remove();
					}
					var item_length=$("#cboItemDesc_"+row_no+" option").length;
					if(item_length==2)
					{
						$('#cboItemDesc_'+row_no).val($('#cboItemDesc_'+row_no+' option:last').val());
						var prod_id=$('#cboItemDesc_'+row_no).val();
						load_drop_down( 'requires/batch_creation_controller', value+'**'+row_no+'**0**'+prod_id, 'load_drop_down_body_part', 'bodyPartTd_'+row_no );
						var body_part_length=$('#cboBodyPart_'+row_no+' option').length;
						if(body_part_length==2)
						{
							$('#cboBodyPart_'+row_no).val($('#cboBodyPart_'+row_no+' option:last').val());
						}
					}
					else
					{
						$("#cboBodyPart_"+row_no+" option[value!='0']").remove();
					}
				}
				else // without order for program
				{

					load_drop_down( 'requires/batch_creation_controller', $('#cboPoNo_'+row_no).val()+'**'+row_no+'**'+booking_without_order+'**'+value+'**'+batch_maintained+'**'+fabric_source+'**'+booking_no, 'load_drop_down_item_desc', itemTdId );



					var item_length=$("#cboItemDesc_"+row_no+" option").length;
					if(item_length==2)
					{
						//alert('single item');
						var booking_no=$('#txt_booking_no').val();
						$('#cboItemDesc_'+row_no).val($('#cboItemDesc_'+row_no+' option:last').val());
						var prod_id=$('#cboItemDesc_'+row_no).val();
						//alert(prod_id+'=single item');
						load_drop_down( 'requires/batch_creation_controller', booking_no+'**'+row_no+'**1**'+prod_id+'**1**'+value, 'load_drop_down_body_part', 'bodyPartTd_'+row_no );
						var body_part_length=$('#cboBodyPart_'+row_no+' option').length;
						if(body_part_length==2)
						{
							$('#cboBodyPart_'+row_no).val($('#cboBodyPart_'+row_no+' option:last').val());
						}
					}
					else
					{
						$("#cboBodyPart_"+row_no+" option[value!='0']").remove();
					}

					/*var prog_length=$("#cboProgramNo_"+row_no+" option").length;
					//alert(prog_length);
					if(prog_length==3)
					{
						//$('#cboProgramNo_'+row_no).val($('#cboProgramNo_'+row_no+' option:last').val());
						load_drop_down( 'requires/batch_creation_controller', $('#cboPoNo_'+row_no).val()+'**'+row_no+'**'+booking_without_order+'**'+value+'**'+batch_maintained+'**'+fabric_source+'**'+booking_no, 'load_drop_down_item_desc', itemTdId );
					}
					else
					{
						$("#cboItemDesc_"+row_no+" option[value!='0']").remove();
					}
					var item_length=$("#cboItemDesc_"+row_no+" option").length;
					if(item_length==2)
					{
						//alert('single item');
						var booking_no=$('#txt_booking_no').val();
						$('#cboItemDesc_'+row_no).val($('#cboItemDesc_'+row_no+' option:last').val());
						var prod_id=$('#cboItemDesc_'+row_no).val();
						//alert(prod_id+'=single item');
						load_drop_down( 'requires/batch_creation_controller', booking_no+'**'+row_no+'**1**'+prod_id+'**1**'+value, 'load_drop_down_body_part', 'bodyPartTd_'+row_no );
						var body_part_length=$('#cboBodyPart_'+row_no+' option').length;
						if(body_part_length==2)
						{
							$('#cboBodyPart_'+row_no).val($('#cboBodyPart_'+row_no+' option:last').val());
						}
					}
					else
					{
						$("#cboBodyPart_"+row_no+" option[value!='0']").remove();
					}*/
				}
			}
		}
		else
		{
			//alert(value+'=TT');
			var program_no=$('#cboProgramNo_'+row_no).val();
			load_drop_down( 'requires/batch_creation_controller', value+'**'+row_no+'**'+booking_without_order+'**'+program_no+'**'+batch_maintained+'**'+fabric_source, 'load_drop_down_item_desc', itemTdId );

			var item_length=$("#cboItemDesc_"+row_no+" option").length;
			if(item_length==2)
			{
				$('#cboItemDesc_'+row_no).val($('#cboItemDesc_'+row_no+' option:last').val());
				var prod_id=$('#cboItemDesc_'+row_no).val();
				load_drop_down( 'requires/batch_creation_controller', value+'**'+row_no+'**0**'+prod_id, 'load_drop_down_body_part', 'bodyPartTd_'+row_no );
				var body_part_length=$('#cboBodyPart_'+row_no+' option').length;
				if(body_part_length==2)
				{
					$('#cboBodyPart_'+row_no).val($('#cboBodyPart_'+row_no+' option:last').val());
				}
			}
			else
			{
				$("#cboBodyPart_"+row_no+" option[value!='0']").remove();
			}
		}

		/*var item_length=$("#cboItemDesc_"+row_no+" option").length;
		if(item_length==2)
		{
			$('#cboItemDesc_'+row_no).val($('#cboItemDesc_'+row_no+' option:last').val());
			var prod_id=$('#cboItemDesc_'+row_no).val();
			load_drop_down( 'requires/batch_creation_controller', value+'**'+row_no+'**0**'+prod_id, 'load_drop_down_body_part', 'bodyPartTd_'+row_no );
			var body_part_length=$('#cboBodyPart_'+row_no+' option').length;
			if(body_part_length==2)
			{
				$('#cboBodyPart_'+row_no).val($('#cboBodyPart_'+row_no+' option:last').val());
			}
		}
		else
		{
			$("#cboBodyPart_"+row_no+" option[value!='0']").remove();
		}*/
		$('#txtPoBatchNo_'+row_no).val('');
	}

	function load_body_part(value,id)
	{
		//alert(value+'='+id);
		var body_part_id=id.split("_");
		var row_no=body_part_id[1];
		var bodyPartTdId='itemDescTd_'+row_no;
		var cboProgramNo=$('#cboProgramNo_'+row_no).val();
		var booking_without_order=$('#booking_without_order').val();
		var booking_no=$('#txt_booking_no').val();
		var batch_against=$('#cbo_batch_against').val();
		if(batch_against==5)
		{
			var poId=$('#poId_'+row_no).val();
		}
		else
		{
			var poId=$('#cboPoNo_'+row_no).val();
		}

		if(booking_without_order==1)
		{
			//alert(cboProgramNo+'=multi item desc onchange');
			if (cboProgramNo>0)
			{
				load_drop_down( 'requires/batch_creation_controller', booking_no+'**'+row_no+'**1**'+value+'**1**'+cboProgramNo, 'load_drop_down_body_part', 'bodyPartTd_'+row_no );
			}
			else
			{
				load_drop_down( 'requires/batch_creation_controller', booking_no+'**'+row_no+'**1**'+value, 'load_drop_down_body_part', 'bodyPartTd_'+row_no );
			}

		}
		else
		{
			load_drop_down( 'requires/batch_creation_controller', poId+'**'+row_no+'**0**'+value, 'load_drop_down_body_part', 'bodyPartTd_'+row_no );
		}

		var body_part_length=$('#cboBodyPart_'+row_no+' option').length;
		if(body_part_length==2)
		{
			$('#cboBodyPart_'+row_no).val($('#cboBodyPart_'+row_no+' option:last').val());
		}
	}

	function load_onchange(value,id)
	{
		//alert(value+'='+id);
		var body_part_id=id.split("_");
		var row_no=body_part_id[1];
		//var txtQtyPcs='txtQtyPcs_'+row_no;
		// alert(txtQtyPcs+'='+roll_maintained);

		var body_part_type=return_global_ajax_value( value, 'body_part_type_action', '', 'requires/batch_creation_controller');
		body_part_type=trim(body_part_type);

		var roll_maintained=$('#roll_maintained').val();
		if(roll_maintained==0 && (body_part_type == 40 || body_part_type == 50))
		{
			$('#txtQtyPcs_'+row_no).removeAttr('disabled','disabled');
			$('#txtSize_'+row_no).removeAttr('disabled','disabled');
		}
		else
		{
			$('#txtQtyPcs_'+row_no).attr('disabled','disabled');
			$('#txtSize_'+row_no).attr('disabled','disabled');
		}
	}

	function put_country_data(color_id,color)
	{
		var batch_against = $('#cbo_batch_against').val();
		var booking_without_order=$('#booking_without_order').val();
		var booking_no=$('#txt_booking_no').val();
		var company_id=$('#cbo_company_id').val();
		var search_type=$('#txt_search_type').val();
		var prev_color=$('#txt_batch_color').val();
		var update_id=$('#update_id').val();
		var roll_maintained=$('#roll_maintained').val();
		var batch_maintained=$('#batch_maintained').val();
		var fabric_source=$('#fabric_source').val();
		//alert(prev_color+"##"+color);
		if(update_id!='' && roll_maintained==1)
		{
			var batch_num=return_global_ajax_value( company_id+'**'+update_id, 'dyeing_check_batch', '', 'requires/batch_creation_controller');
			batch_no=trim(batch_num);
			if(batch_no!='')
			{
				alert('This batch no is used in another table ='+batch_no);
				return;
			}
		}

		if(prev_color!=color)
		{
			if(batch_against!=2)
			{
				//if(update_id!='' && roll_maintained==1) 
				if(update_id!='')
				{
					//alert('This batch no is used in another table ='+batch_no);
					reset_form('','','txt_batch_sl_no*txt_batch_weight*txt_tot_trims_weight*save_data*txt_batch_number*txt_ext_no*cbo_color_range*txt_organic*txt_process_name*txt_process_id*txt_du_req_hr*txt_du_req_min*txt_exp_load_hr*txt_exp_load_min*update_id*hide_update_id*hide_batch_against*txt_total_batch_qnty*hide_job_no*txt_deleted_id*cbo_shift_name*txt_dyeing_pdo*txt_batch_delivery_date','',"$('#tbl_item_details tbody tr:not(:first)').remove();",'');
					$('#txt_batch_color').val(color);
				}
				else
				{
					/*reset_form('','', 'txt_batch_sl_no*txt_batch_weight*txt_tot_trims_weight*save_data*txt_batch_number*txt_ext_no*cbo_color_range*txt_organic*txt_process_name*txt_process_id*txt_du_req_hr*txt_du_req_min*update_id*hide_update_id*hide_batch_against*txt_total_batch_qnty*hide_job_no*txt_deleted_id*txt_batch_delivery_date','',"$('#tbl_item_details tbody tr:not(:first)').remove();",'');*/
					$('#txt_batch_color').val(color);
				}


				$("#batch_details_container").find('select,input:not([type=button])').val('');

				if(roll_maintained!=1)
				{
					if(booking_without_order==1)
					{
						var is_sales = 0;
						if($("#txt_sales_id").val()){
							var is_sales = 1;
						}
						load_drop_down( 'requires/batch_creation_controller', booking_no+"**"+search_type+"**"+company_id+"**"+is_sales+"**"+$("#txt_sales_id").val(), 'load_drop_down_program', 'programNoTd_1' );
						var prog_length=$("#programNoTd_1 option").length;
						$("#cboPoNo_1 option[value!='0']").remove();
						if (prog_length>1) // Sample, without order for program
						{
							//alert();
							if(prog_length==2)
							{
								$('#cboProgramNo_1').val($('#cboProgramNo_1 option:last').val());
								load_drop_down( 'requires/batch_creation_controller', $('#cboPoNo_1').val()+'**'+1+'**'+booking_without_order+'**'+$('#cboProgramNo_1').val()+'**'+batch_maintained+'**'+fabric_source, 'load_drop_down_item_desc', 'itemDescTd_1' );
							}
							else
							{
								$("#cboItemDesc_1 option[value!='0']").remove();
							}
							var item_length=$("#cboItemDesc_1 option").length;
							if(item_length==2)
							{
								// alert('multi item data');
								$('#cboItemDesc_1').val($('#cboItemDesc_1 option:last').val());
								var prod_id=$('#cboItemDesc_1').val();
								load_drop_down( 'requires/batch_creation_controller', booking_no+'**'+1+'**'+booking_without_order+'**'+prod_id+'**1**'+$('#cboProgramNo_1').val(), 'load_drop_down_body_part', 'bodyPartTd_1' );
								var body_part_length=$("#cboBodyPart_1 option").length;
								if(body_part_length==2)
								{
									$('#cboBodyPart_1').val($('#cboBodyPart_1 option:last').val());
								}
							}
						}
						else // Sample, without order without program
						{
							$("#cboProgramNo_1 option[value!='0']").remove();
							//$("#cboPoNo_1 option[value!='0']").remove();
							load_drop_down( 'requires/batch_creation_controller', booking_no+'**1**'+booking_without_order+"**0**"+batch_maintained, 'load_drop_down_item_desc', 'itemDescTd_1' );
							var item_length=$("#cboItemDesc_1 option").length;
							if(item_length==2)
							{
								$('#cboItemDesc_1').val($('#cboItemDesc_1 option:last').val());
								var prod_id=$('#cboItemDesc_1').val();
								load_drop_down('requires/batch_creation_controller',booking_no+'**1**'+booking_without_order+'**'+prod_id,'load_drop_down_body_part','bodyPartTd_1');
								var body_part_length=$("#cboBodyPart_1 option").length;
								if(body_part_length==2)
								{
									$('#cboBodyPart_1').val($('#cboBodyPart_1 option:last').val());
								}
							}
						}
					}
					else
					{
						var is_sales = 0;
						if($("#txt_sales_id").val()){
							var is_sales = 1;
						}
						load_drop_down( 'requires/batch_creation_controller', booking_no+'**'+color_id+'**'+is_sales+'**'+$("#txt_sales_id").val(), 'load_drop_down_po', 'poNoTd_1' );
						var length=$("#cboProgramNo_1 option").length;
						if(length>2) $('#cboProgramNo_1').val(0);
						var po_length=$("#cboPoNo_1 option").length;
						if(po_length==2)
						{
							var program_no=$('#cboProgramNo_1').val();
							$('#cboPoNo_1').val($('#cboPoNo_1 option:last').val());
							load_drop_down( 'requires/batch_creation_controller', $('#cboPoNo_1').val()+'**'+1+'**'+booking_without_order+'**'+program_no+'**'+batch_maintained+'**'+fabric_source, 'load_drop_down_item_desc', 'itemDescTd_1' );
							var item_length=$("#cboItemDesc_1 option").length;
							if(item_length==2)
							{
								$('#cboItemDesc_1').val($('#cboItemDesc_1 option:last').val());
								var prod_id=$('#cboItemDesc_1').val();
								load_drop_down( 'requires/batch_creation_controller', $('#cboPoNo_1').val()+'**'+1+'**'+booking_without_order+'**'+prod_id, 'load_drop_down_body_part', 'bodyPartTd_1' );
								var body_part_length=$('#cboBodyPart_1 option').length;
								if(body_part_length==2)
								{
									$('#cboBodyPart_1').val($('#cboBodyPart_1 option:last').val());
								}
							}
							else
							{
								$("#cboBodyPart_1 option[value!='0']").remove();
							}
						}
						else
						{
							$("#cboItemDesc_1 option[value!='0']").remove();
						}
					}
				}
				set_button_status(0, permission, 'fnc_batch_creation',1);
			}
			else
			{
				alert("Not For Re-Dyeing");
			}
		}
	}

	function fnc_move_cursor(val,id, field_id,lnth,max_val)
	{
		var str_length=val.length;
		if(str_length==lnth)
		{
			$('#'+field_id).select();
			$('#'+field_id).focus();
		}

		if(val>max_val)
		{
			document.getElementById(id).value=max_val;
		}
	}

	function openmypage_barcode()
	{
		var company_id=$('#cbo_company_id').val();
		var batch_against = $('#cbo_batch_against').val();
		var booking_without_order=$('#booking_without_order').val();
		var booking_no=$('#txt_booking_no').val();
		var booking_id=$('#txt_booking_no_id').val();
		var sales_id=$('#txt_sales_id').val();
		var search_type=$('#txt_search_type').val();
		var txt_batch_color_id = $('#txt_batch_color_id').val();
		var txt_color_type = $('#txt_color_type').val();


		if (form_validation('cbo_company_id*cbo_batch_against','Company*Batch Against')==false)
		{
			return;
		}

		/*if(batch_against==2)
		{
			alert("Not For Re-Dyeing");
			return;
		}*/

		if(batch_against==1 || batch_against==3)
		{
			if (form_validation('txt_booking_no','Fabric Booking No')==false)
			{
				return;
			}
		}

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/batch_creation_controller.php?company_id='+company_id+'&batch_against='+batch_against+'&booking_without_order='+booking_without_order+'&booking_no='+booking_no+'&search_type='+search_type+'&sales_id='+sales_id+'&txt_batch_color_id='+txt_batch_color_id+'&color_type='+txt_color_type+'&action=barcode_popup'+'&booking_id='+booking_id,'Barcode Popup', 'width=1340px,height=350px,center=1,resize=1,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var barcode_nos=this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos
			if(barcode_nos!="")
			{
				//alert(booking_without_order);
				//console.log(barcode_nos+"__"+batch_against+"__"+booking_without_order+"__"+booking_no+"__"+search_type+"__"+sales_id+"__"+booking_id);
				barcode_data(barcode_nos,batch_against,booking_without_order,booking_no,search_type,sales_id,booking_id);
			}
		}
	}

	if(scanned_barcode==null)
	{
		scanned_barcode=new Array();
	}

	$('#txt_bar_code_num').live('keydown', function(e) {
		if (e.keyCode === 13)
		{
			e.preventDefault();
			var bar_code=$('#txt_bar_code_num').val();
		/*	var is_barcode_scanned=return_global_ajax_value(bar_code, 'check_if_barcode_scanned', '', 'requires/batch_creation_controller');
			if(is_barcode_scanned == 1){
				alert('Sorry! Barcode Already Scanned.');
				$('#txt_bar_code_num').val('');
				return;
			}
			if(scanned_barcode!=null)
			{
				if( jQuery.inArray( bar_code, scanned_barcode )>-1)
				{
					alert('Sorry! Barcode Already Scanned.');
					$('#txt_bar_code_num').val('');
					return;
				}
			}*/

			if (form_validation('cbo_company_id*cbo_batch_against','Company*Batch Against')==false)
			{
				$('#txt_bar_code_num').val('');
				return;
			}

			var batch_against = $('#cbo_batch_against').val();
			var booking_without_order=$('#booking_without_order').val();
			var booking_no=$('#txt_booking_no').val();
			var booking_id=$('#txt_booking_no_id').val();
			var sales_id=$('#txt_sales_id').val();
			var search_type=$('#txt_search_type').val();

			if(batch_against==2)
			{
				alert("Not For Re-Dyeing");
				$('#txt_bar_code_num').val('');
				return;
			}

			if(batch_against==1 || batch_against==3)
			{
				if (form_validation('txt_booking_no','Fabric Booking No')==false)
				{
					$('#txt_bar_code_num').val('');
					return;
				}
			}

			barcode_data(bar_code,batch_against,booking_without_order,booking_no,search_type,sales_id,booking_id);
		}
	});

	function barcode_data(barcode_nos,batch_against,booking_without_order,booking_no,search_type,sales_id,booking_id)
	{
		var barcode_data=return_global_ajax_value(barcode_nos+"**"+batch_against+"**"+booking_without_order+"**"+booking_no+"**"+search_type+"**"+sales_id+"**"+booking_id, 'populate_barcode_data', '', 'requires/batch_creation_controller');
		//console.log(barcode_data);
		//return;

		//alert(barcode_data);return;

		if(barcode_data==0)
		{
			alert('Barcode is Not Valid');
			$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
			{
				$('#messagebox_main', window.parent.document).html('Barcode is Not Valid.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
			});
			$('#txt_bar_code_num').val('');
			return;
		}
		if(barcode_data==999)
		{
			alert('Grey Roll Receive From Process not found');
			$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
			{
				$('#messagebox_main', window.parent.document).html('Barcode is Not Valid.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
			});
			$('#txt_bar_code_num').val('');
			return;
		}

		var barcode_datas=barcode_data.split("#");
		for(var k=0; k<barcode_datas.length; k++)
		{
			var row_num=$('#tbl_item_details tbody tr').length;
			var barcodeNo=$('#barcodeNo_'+row_num).val();

			var data=barcode_datas[k].split("**");
			var booking_prog_pi_no=data[0];
			var promram_id=data[1];
			var prod_id=data[2];
			var product_name_details=data[3];
			var roll_id=data[4];
			var roll_no=data[5];
			var po_breakdown_id=data[6];
			var po_number=data[7];
			var qnty=data[8];
			var barcode_no=data[9];
			var body_part_id=data[10];
			var body_part_name=data[11];
			var widthDiaType=data[12];
			var qtyPcs=data[13];
			var item_size=data[14];
			var color_types=data[15];
			var selected_dia_type=data[16];
			var selected_color_type=data[17];
			var lotNo=data[18];

			//=========Lot Mixing validation Start==========
			if(scanned_lotNo.length==0)
			{
				scanned_lotNo.push( lotNo );
			}
			else if( jQuery.inArray( lotNo, scanned_lotNo )==-1 &&  scanned_lotNo.length>0)
			{
				var r=confirm("Lot Mixing Allow");
				// alert(r);
				if(r==false) { return; } // if cancle (false) is return
			}
			//=========Lot Mixing validation End==========

			if(scanned_barcode==null)
			{
				scanned_barcode=new Array();
			}

			if( jQuery.inArray( barcode_no, scanned_barcode ) == -1)
			{
				if(barcodeNo!="")
				{
					add_break_down_tr( row_num );
					row_num++;
				}

				if(promram_id!=0)
				{
					$("#cboProgramNo_"+row_num+" option[value='"+promram_id+"']").remove();
					$("#cboProgramNo_"+row_num).append("<option selected value='"+promram_id+"'>"+promram_id+"</option>");
				}

				if($('#cbo_batch_against').val()==5)
				{
					$('#cboPoNo_'+row_num).val(po_number);
					$('#poId_'+row_num).val(po_breakdown_id);
				}
				else
				{
					if(booking_without_order==1)
					{
						$("#cboPoNo_"+row_num+" option[value!='0']").remove();
					}
					else
					{
						$("#cboPoNo_"+row_num+" option[value='"+po_breakdown_id+"']").remove();
						$("#cboPoNo_"+row_num).append("<option selected value='"+po_breakdown_id+"'>"+po_number+"</option>");
					}
				}

				$("#cboItemDesc_"+row_num+" option[value='"+prod_id+"']").remove();
				$("#cboItemDesc_"+row_num).append("<option selected value='"+prod_id+"'>"+product_name_details+"</option>");
				$("#itemDescTd_"+row_num).prop('title', product_name_details);
				$("#cboBodyPart_"+row_num+" option[value='"+body_part_id+"']").remove();
				$("#cboBodyPart_"+row_num).append("<option selected value='"+body_part_id+"'>"+body_part_name+"</option>");

				$('#txtRollNo_'+row_num).val(roll_no);
				$('#hideRollNo_'+row_num).val(roll_id);
				$('#txtBatchQnty_'+row_num).val(qnty);
				$('#hiddenRollqty_'+row_num).val(qnty);
				$('#txtBatchQnty_'+row_num).attr('disabled','disabled');
				$('#barcodeNo_'+row_num).val(barcode_no);
			    // $('#cboDiaWidthType_'+row_num).val(widthDiaType);
			    $('#cboDiaWidthType_'+row_num).val(selected_dia_type);

				$('#txtQtyPcs_'+row_num).val(qtyPcs);
				$('#txtSize_'+row_num).val(item_size);
				$('#hiddenQtyPcs_'+row_num).val(qtyPcs);
				$('#txtRemarks_'+row_num).val();//dtlsremarks

				if(color_types !="")
				{
					var color_types = color_types.split(",");
					var selectbox = $("#cboColorType_"+row_num);
					selectbox.empty();
					var list = '';
					list +='<option value="'+0+'">Select</option>';
					for (var j = 0; j < color_types.length; j++)
					{
					    list += "<option value='" +color_types[j]+ "'>" +js_colortype_name_array[color_types[j]]+ "</option>";
					}
					selectbox.html(list);
				}
				$('#cboColorType_'+row_num).val(selected_color_type);
				$('#txtLotNo_'+row_num).val(lotNo);

				scanned_barcode.push(barcode_no);
			}
			else
			{
				alert('Sorry! Barcode Already Scanned.');
			}
		}
		$('#txt_bar_code_num').val('');
		set_all_onclick();
		calculate_batch_qnty();
		calculate_qtyPcs();
	}

	function openmypage_trims()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var save_data = $('#save_data').val();

		if (form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}

		var page_link='requires/batch_creation_controller.php?save_data='+save_data+'&action=trims_weight_popup';
		var title='Trims Weight ';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=390px,center=1,resize=1,scrolling=0','');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var save_data=this.contentDoc.getElementById("save_data").value;
			var tot_trims_wgt=this.contentDoc.getElementById("tot_trims_qnty").value;
			$('#save_data').val(save_data);
			$('#txt_tot_trims_weight').val( tot_trims_wgt );
			calculate_batch_qnty();
			calculate_qtyPcs();
		}
	}

	function fnc_load_report_format(data)
	{
		var report_ids='';
		var report_ids = return_global_ajax_value( data, 'load_report_format', '', 'requires/batch_creation_controller');
		print_report_button_setting(report_ids);
	}

	function print_report_button_setting(report_ids)
	{
		if(trim(report_ids)=="")
		{
			$("#Print_1").hide();
			$("#Print2").hide();
			$("#Print3").hide();
			$("#Print4").hide();
			$("#Print5").hide();
			$("#Print6").hide();
			$("#Print7").hide();
			$("#Print8").hide();
			$("#Print9").hide();
			$("#Print10").hide();
			$("#Print11").hide();
			$("#Print12").hide();
			$("#Print13").hide();
			$("#Print15").hide();
			$("#Print16").hide();
			$("#Print17").hide();
			$("#Print17_back").hide();
			$("#Print18").hide();
			$("#Print20").hide();
			$("#Print21").hide();
			$("#Print22").hide();
			$("#Print2v1").hide();
		}
		else
		{
			$("#Print_1").hide();
			$("#Print2").hide();
			$("#Print3").hide();
			$("#Print4").hide();
			$("#Print5").hide();
			$("#Print6").hide();
			$("#Print7").hide();
			$("#Print8").hide();
			$("#Print9").hide();
			$("#Print10").hide();
			$("#Print11").hide();
			$("#Print12").hide();
			$("#Print13").hide();
			$("#Print15").hide();
			$("#Print16").hide();
			$("#Print17").hide();
			$("#Print17_back").hide();
			$("#Print18").hide();
			$("#Print19").hide();
			$("#Print20").hide();
			$("#Print21").hide();
			$("#Print22").hide();
			$("#Print2v1").hide();
			var report_id=report_ids.split(",");
			for (var k=0; k<report_id.length; k++)
			{
				if(report_id[k]==86)
				{
					$("#Print_1").show();
				}
				else if(report_id[k]==185)
				{
					$("#Print2").show();
				}
				else if(report_id[k]==186)
				{
					$("#Print3").show();
				}
				if(report_id[k]==187)
				{
					$("#Print4").show();
				}
				if(report_id[k]==224)
				{
					$("#Print5").show();
				}
				if(report_id[k]==225)
				{
					$("#Print6").show();
				}
				if(report_id[k]==226)
				{
					$("#Print7").show();
				}
				if(report_id[k]==220)
				{
					$("#Print8").show();
				}
				if(report_id[k]==235)
				{
					$("#Print9").show();
				}
				if(report_id[k]==274)
				{
					$("#Print10").show();
				}
				if(report_id[k]==241)
				{
					$("#Print11").show();
				}
				if(report_id[k]==269)
				{
					$("#Print12").show();
				}
				if(report_id[k]==324)
				{
					$("#Print13").show();
				}
				if(report_id[k]==280)
				{
					$("#Print14").show();
				}
				if(report_id[k]==304)
				{
					$("#Print15").show();
				}
				if(report_id[k]==719)
				{
					$("#Print16").show();
				}
				if(report_id[k]==723)
				{
					$("#Print17").show();
					$("#Print17_back").show();
				}
				if(report_id[k]==339)
				{
					$("#Print18").show();
				}
				if(report_id[k]==370)
				{
					$("#Print19").show();
				}
				if(report_id[k]==768)
				{
					$("#Print20").show();
				}
				if(report_id[k]==404)
				{
					$("#Print21").show();
				}
				if(report_id[k]==419)
				{
					$("#Print22").show();
				}
				else if(report_id[k]==3)
				{
					$("#Print2v1").show();
				}
			}
		}
	}

	function openmypage_serviceBooking(type) //Service Booking Knitting
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_knitting_source = 1;
		var cbo_knitting_company = $('#cbo_working_company_id').val();

		if (form_validation('cbo_working_company_id','Working Company')==false)
		{
			return;
		}

		var title = 'Service Booking Selection Form';
		var page_link = 'requires/batch_creation_controller.php?cbo_company_id='+cbo_company_id+'&cbo_knitting_source='+cbo_knitting_source+'&cbo_knitting_company='+cbo_knitting_company+'&action=serviceBooking_popup';
		var popup_width="1070px";

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=400px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var service_hidden_booking_id=this.contentDoc.getElementById("service_hidden_booking_id").value;
			var service_hidden_booking_no=this.contentDoc.getElementById("service_hidden_booking_no").value;

			if(service_hidden_booking_id!="")
			{
				freeze_window(5);
				$('#txt_service_booking').val(service_hidden_booking_no).attr('disabled',true);
				$('#service_booking_id').val(service_hidden_booking_id);
				release_freezing();
			}
		}
	}

	function copyDiaWidthType(id,value)
	{
		var idArr = id.split("_");
		var sl = idArr[1]*1;
		//alert(sl);
		if($('#DiaWidthTypes').is(':checked'))
		{
			$("#tbl_item_details").find('tbody tr').each(function()
			{
				var DiaWidthType = $(this).find('select[name="cboDiaWidthType[]"]').attr("id");
				var DiaWidthTypeSlArr = DiaWidthType.split("_");
				// copy only that and below selected data
				if( sl <= DiaWidthTypeSlArr[1]*1 )
				{
					$("#cboDiaWidthType_"+DiaWidthTypeSlArr[1]*1).val(value);
				}
			});
		}
	}
	function fnc_copy_colorType(i)
	{
		if($('#colorTypeChk').is(':checked'))
		{
			var value = $("#cboColorType_"+i).val();
			var po_no = $("#cboPoNo_"+i).val();
			$("#tbl_item_details").find('tbody tr').each(function()
			{
				var cboColorType = $(this).find('select[name="cboColorType[]"]').attr("id");
				var cboColorTypeSlArr = cboColorType.split("_");
				// copy only that and below selected data
				if( i <= cboColorTypeSlArr[1]*1 )
				{
					var po_chk = $("#cboPoNo_"+cboColorTypeSlArr[1]*1).val();
					if(po_no == po_chk)
					{
						$("#cboColorType_"+cboColorTypeSlArr[1]*1).val(value);
					}

				}
			});
		}
	}

	function fnc_style_wise_popup()
	{
		var title = 'Style Info';
		var cbo_company_id = $('#cbo_company_id').val();
		var booking_without_order = $('#booking_without_order').val();
		var roll_maintained = $('#roll_maintained').val();
		var txt_search_type = $('#txt_search_type').val();
		var update_id = $('#update_id').val();
		var txt_sales_booking_no = $('#txt_sales_booking_no').val();
		var sales_booking_no = txt_sales_booking_no.split('-');
		var hdn_fso_id = $('#txt_sales_id').val();
		var txt_color_id = $('#txt_batch_color_id').val();
		var style_breakdown_variable_sett = $('#style_breakdown_variable_sett').val();

		var txt_po_job = $('#txt_po_job').val();
		var style_breakdown_str = $('#style_breakdown_str').val();
		var cbo_body_part = $('#cbo_body_part').val();
		var fabric_desc_id = $('#txt_fabric_description_id').val();
		var txt_gsm = $('#txt_gsm').val();
		var txt_dia = $('#txt_dia').val();
		var uom = $('#hdn_uom').val();
		var update_dtls_id = $('#update_dtls_id').val();


		if (update_id=="")
		{
			alert('Please save first');return;
		}
		// txt_search_type == 7 is sales order
		if(sales_booking_no[1] != 'SMN' && txt_search_type == 7 && roll_maintained==1 && style_breakdown_variable_sett==1)
		{
			var action = "style_wise_popup";
			var popup_width='1250x';

			var page_link = 'requires/batch_creation_controller.php?cbo_company_id='+cbo_company_id+'&hdn_fso_id='+hdn_fso_id+'&txt_color_id='+txt_color_id+'&update_id='+update_id+'&style_breakdown_str='+style_breakdown_str+'&action='+action;

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=370px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{

				var theform=this.contentDoc.forms[0];
				var save_string=this.contentDoc.getElementById("save_string").value;
				var tot_breakdown_qnty=this.contentDoc.getElementById("tot_breakdown_qnty").value;

				//var all_po_id=this.contentDoc.getElementById("all_po_id").value;

				//var distribution_method=this.contentDoc.getElementById("distribution_method").value;
				//var hide_deleted_id=this.contentDoc.getElementById("hide_deleted_id").value;

				$('#style_breakdown_str').val(save_string);
				$('#txt_style_breakdown_qnty').val(tot_breakdown_qnty);
			}
		}
		else
		{
			alert('Style wise popup not allowed non order sample, only for sales order and roll level');
			return;
		}
	}

</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;">
		<? echo load_freeze_divs ("../",$permission); ?>
		<div style="width:1070px; float:left" align="center">
			<fieldset style="width:1070px;">
				<legend>Batch Creation</legend>
				<form name="batchcreation_1" id="batchcreation_1">
					<fieldset style="width:1000px;">
						<table width="1069" align="center" border="0">
							<tr>
								<td width="110" colspan="4" align="right"><b>Batch Serial No</b></td>
								<td colspan="2">
									<input type="text" name="txt_batch_sl_no" id="txt_batch_sl_no" class="text_boxes" style="width:160px;" placeholder="Display" disabled />
									<input type="hidden" name="is_approved_id" id="is_approved_id" value="">
								</td>
							</tr>
							<tr><td></td></tr>
							<tr>
								<td width="100" class="must_entry_caption">Batch Against</td>
								<td>
									<?
									echo create_drop_down( "cbo_batch_against", 144, $batch_against,"", 1, '--- Select ---', 1, "active_inactive();",'','1,2,3,5,7','','','',1 );
									?>
								</td>
								<td  width="130">Fabric Sales/Booking No</td>
								<td>
									<input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:134px;" placeholder="Double Click To Search" onDblClick="openmypage_fabricBooking();" readonly tabindex="9"/>
									<input type="hidden" name="txt_booking_no_id" id="txt_booking_no_id"/>
									<input type="hidden" name="txt_sales_id" id="txt_sales_id"/>
									<input type="hidden" name="booking_without_order" id="booking_without_order"/>
									<input type="hidden" name="txt_sales_booking_no" id="txt_sales_booking_no"/>
									<input type="hidden" name="txt_color_type" id="txt_color_type"/>
									<input type="hidden" name="hidden_booking_entry_form" id="hidden_booking_entry_form"/>
								</td>
								<td width="110" class="must_entry_caption">Batch Date</td>
								<td>
									<input type="text" name="txt_batch_date" id="txt_batch_date" class="datepicker" style="width:132px;" tabindex="6" value="<? echo date("d-m-Y"); ?>" />
								</td>
								<td width="110" class="must_entry_caption">Batch Weight </td>
								<td>
									<input type="text" name="txt_batch_weight" id="txt_batch_weight" class="text_boxes_numeric" style="width:132px;" tabindex="7" disabled />
								</td>
							</tr>
							<tr>
							<td class="must_entry_caption">LC Company</td>
								<td>
									<?
									echo create_drop_down( "cbo_company_id", 144, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name",'id,company_name', 1, '--Select Company--', 0,"get_php_form_data(this.value,'batch_no_creation','requires/batch_creation_controller' );roll_maintain();get_php_form_data(this.value,'load_fabric_source_from_variable_settings','requires/batch_creation_controller');get_php_form_data(this.value,'load_variable_style_breakdown','requires/batch_creation_controller' );fnc_load_report_format(this.value);",'','','','','',3);
									?>
								</td>

                                <td class="must_entry_caption">Working Company</td>
								<td>
									<?
									echo create_drop_down( "cbo_working_company_id", 144, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name",'id,company_name', 1, '--Select Working--', 0,"load_drop_down('requires/batch_creation_controller',this.value, 'load_drop_down_floor', 'td_floor' );",'','','','','',3);
									?>
								</td>
								<td class="must_entry_caption">Batch Number</td>
								<td>
									<input type="text" name="txt_batch_number" id="txt_batch_number" class="text_boxes" style="width:132px;" placeholder="Double Click To Edit" onDblClick="openmypage_batchNo()" tabindex="4" />
								</td>

								<td>Total Trims Weight</td>
								<td>
									<input type="text" name="txt_tot_trims_weight" id="txt_tot_trims_weight" class="text_boxes_numeric" style="width:132px;" tabindex="8" onDblClick="openmypage_trims()" placeholder="Double Click To Search" readonly />
									<input type="hidden" name="save_data" id="save_data" class="text_boxes" style="width:100px;">
								</td>

							</tr>
                            <tr>
							<td class="must_entry_caption">Batch Color</td>
								<td>
									<input type="text" name="txt_batch_color" id="txt_batch_color" class="text_boxes" value="" style="width:132px;" tabindex="10" disabled />
									<input type="hidden" name="txt_batch_color_id" id="txt_batch_color_id" class="text_boxes" />
								</td>
								<td>Color Range</td>
								<td>
									<?
									echo create_drop_down( "cbo_color_range", 144, $color_range,"",1, "-- Select --", 0, "" );
									?>
								</td>

								<td>Extention No.</td>
								<td>
									<input type="text" name="txt_ext_no" id="txt_ext_no" class="text_boxes_numeric" style="width:132px;" disabled="disabled" tabindex="5" />
								</td>
								<td>Collar Qty (Pcs)</td>
								<td>
									<input type="text" name="txt_color_qty" id="txt_color_qty" class="text_boxes_numeric" style="width:132px;"/>
								</td>
							</tr>
							<tr>
							<td class="must_entry_caption">Batch For</td>
								<td>
									<?
									echo create_drop_down( "cbo_batch_for", 144, $batch_for,"", 0, '--- Select ---', 1, "",'1','1','','','',2 );
									?>
								</td>
								<td class="">Batch Type</td>
								<td>
									<?

									echo create_drop_down( "cbo_batch_type", 144, $batch_type_arr,"",1, "-- Select --", 0, "" );
									?>
								</td>
								<td>Service Booking</td>
									<td>
										<input type="text" name="txt_service_booking" id="txt_service_booking" class="text_boxes" style="width:132px" placeholder="Double Click to Search" onDblClick="openmypage_serviceBooking(2);" >
										<input type="hidden" name="service_booking_id" id="service_booking_id">

									</td>
									<td>Cuff Qty (Pcs)</td>
								<td>
									<input type="text"  name="txt_cuff_qty" id="txt_cuff_qty" class="text_boxes_numeric" style="width:132px;"/>
								</td>

							</tr>
							<tr>


							<td>Floor</td>
								<td id="td_floor">
									<?
									echo create_drop_down("cbo_floor", 144, $blank_array,"", 1, "-- Select Floor--", 0, "",0,"","","","");
									?></td>
							<td>Dyeing Machine</td>
									<td id="td_dyeing_machine"><?
									echo create_drop_down("cbo_machine_name", 144, $blank_array,"", 1, "-- Select Machine --", 0, "",0,"","","","");
									?></td>

									<td> Multi Dyeing</td>
									<td>
										<?

										echo create_drop_down("cbo_double_dyeing", 144, $yes_no,"", 1, "-- Select --", 0, "",0,"","","","");
										?>
									</td>
									<td>Process Name</td>
								<td>
									<input type="text" name="txt_process_name" id="txt_process_name" class="text_boxes" style="width:132px;" placeholder="Double Click To Search" onDblClick="openmypage_process();" tabindex="13" readonly />
									<input type="hidden" name="txt_process_id" id="txt_process_id" value="" />
									<input type="hidden" name="txt_process_seq" id="txt_process_seq" value="" />
								</td>

							</tr>
							<tr>
							<td>Duration Req.</td>
								<td>
									<input type="text" name="txt_du_req_hr" id="txt_du_req_hr" class="text_boxes_numeric" placeholder="Hour" onKeyUp="fnc_move_cursor(this.value,'txt_du_req_hr','txt_end_date',2,23)" style="width:56px;" />&nbsp;
									<input type="text" name="txt_du_req_min" id="txt_du_req_min" class="text_boxes_numeric" onKeyUp="fnc_move_cursor(this.value,'txt_du_req_min','txt_end_date',2,59)" placeholder="Minute" style="width:56px;" />
								</td>
								<td>Organic</td>
								<td>
									<input type="text" name="txt_organic" id="txt_organic" class="text_boxes" style="width:132px;" tabindex="12" />
								</td>
								<td>Ready to approve</td>
									<td>
										<?

										echo create_drop_down("cbo_ready_to_approved", 144, $yes_no,"", 1, "-- Select --", 0, "",0,"","","","");
										?>
									</td>
								<td>Shift Name</td>
								<td>
									<?

									echo create_drop_down("cbo_shift_name", 144, $shift_name,"", 1, "-- Select --", 0, "",0,"","","","");
									?>
								</td>
							</tr>
								<tr>
									<td>Expected Load Time</td>
									<td>
										<input type="text" name="txt_exp_load_hr" id="txt_exp_load_hr" class="text_boxes_numeric" placeholder="Hour" onKeyUp="fnc_move_cursor(this.value,'txt_exp_load_hr','txt_end_date',2,23)" style="width:56px;" />&nbsp;
										<input type="text" name="txt_exp_load_min" id="txt_exp_load_min" class="text_boxes_numeric" onKeyUp="fnc_move_cursor(this.value,'txt_exp_load_min','txt_end_date',2,59)" placeholder="Minute" style="width:56px;" />
									</td>
									<td>Dyeing PDO</td>
									<td><input type="text" name="txt_dyeing_pdo" id="txt_dyeing_pdo" class="text_boxes" style="width:132px;" /></td>
									<td>Remarks</td>
									<td><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:132px;" /></td>
									<td id="display" style="display:none">Style Breakdown</td>
									<td id="display2" style="display:none">
										<input type="text" name="txt_style_breakdown_qnty" id="txt_style_breakdown_qnty" class="text_boxes_numeric" style="width:132px;" placeholder="Double Click" onDblClick="fnc_style_wise_popup();" readonly="readonly"/>
										<input type="hidden" name="style_breakdown_str" id="style_breakdown_str">
									</td>
									
								</tr>
								<tr>
									<td width="110" class="">Batch Delivery Date</td>
									<td>
										<input type="text" name="txt_batch_delivery_date" id="txt_batch_delivery_date" class="datepicker" style="width:132px;" tabindex="6" value="<? //echo date("d-m-Y"); ?>" />
									</td>

									<td colspan="6" align="center">
										<?
										include("../terms_condition/terms_condition.php");
										terms_condition(64,'txt_batch_sl_no','../','txt_sales_id');
										?>
									</td>
								</tr>
								<tr>
									<td align="center" id="approved" colspan="8" style="color:#F00;"></td>
								</tr>
							</table>
                    </fieldset>
                    <fieldset style="width:1141px; margin-top:10px">

                         <legend>Item Details</legend>
                        <div style="margin-bottom:5px; display:none" id="barcode_div">
                            <strong>Roll Number</strong>
                            <input type="text" name="txt_bar_code_num" id="txt_bar_code_num" class="text_boxes_numeric" style="width:170px;" onDblClick="openmypage_barcode()" placeholder="Browse/Write/scan"/>
                        </div>
                        <table cellpadding="0" cellspacing="0" width="1450" class="rpt_table" border="1" rules="all" id="tbl_item_details">
                            <thead>
                                <th>SL</th>
                                <th>Program No</th>
                                <th class="must_entry_caption">PO No./FSO No</th>
                                <th class="must_entry_caption">Item Description</th>
                                <th class="must_entry_caption">Body Part</th>
                                <th class="must_entry_caption">Dia/ W. Type<br><input type="checkbox" checked id="DiaWidthTypes" name="DiaWidthTypes"/></th>
                                <th>Roll No.</th>
                                <th>Barcode No.</th>
                                <th class="must_entry_caption">Batch Qnty</th>
                                <th>Qty In Pcs</th>
                                <th>Item Size</th>
                                <th>PO Batch No</th>
                                <th>Color Type<br><input type="checkbox" checked id="colorTypeChk" name="colorTypeChk"/></th>
                                <th>Lot No</th>
                                <th>Remarks</th>
                                <th></th>
                            </thead>
                            <tbody id="batch_details_container">
                                <tr class="general" id="tr_1">
                                    <td id="slTd_1" width="30">1</td>
                                    <td id="programNoTd_1">
                                        <?
                                        echo create_drop_down("cboProgramNo_1", 80, $blank_array,"", 1, "-- Select --", 0, "", "", "", "", "", "", "", "", "cboProgramNo[]" );
                                        ?>
                                    </td>
                                    <td id="poNoTd_1">
                                        <?
                                        echo create_drop_down( "cboPoNo_1", 130, $blank_array,"", 1, "-- Select Po Number --", 0, "", "", "", "", "", "", "", "", "cboPoNo[]" );
                                        ?>
                                    </td>
                                    <td id="itemDescTd_1">
                                        <?
                                        echo create_drop_down( "cboItemDesc_1", 180, $blank_array,"", 1, "-- Select Item Desc --", 0, "", "", "", "", "", "","","","cboItemDesc[]");
                                        ?>
                                    </td>
                                    <td id="bodyPartTd_1">
                                        <?
                                        echo create_drop_down( "cboBodyPart_1", 120, $blank_array,"", 1, "-- Select --", 0, "", "", "", "", "", "", "", "", "cboBodyPart[]" );
                                        ?>
                                    </td>
                                    <td>
                                        <?
                                        echo create_drop_down( "cboDiaWidthType_1", 90, $fabric_typee,"",1, "-- Select --", 0, "copyDiaWidthType(this.id,this.value)", "", "", "", "", "", "", "", "cboDiaWidthType[]" );
                                        ?>
                                    </td>
                                    <td>
                                        <input type="text" name="txtRollNo[]" id="txtRollNo_1" class="text_boxes_numeric" style="width:50px" disabled="disabled" /> <!--onDblClick="openmypage_rollnum(1)"-->
                                        <input type="hidden" name="hideRollNo[]" id="hideRollNo_1" class="text_boxes" readonly />
                                        <input type="hidden" name="poId[]" id="poId_1" class="text_boxes" readonly />
                                        <input type="hidden" name="hiddenRollqty[]" id="hiddenRollqty_1" class="text_boxes" readonly />
                                        <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_1" class="text_boxes" readonly />
                                        <input type="hidden" name="hiddenQtyPcs[]" id="hiddenQtyPcs_1" class="text_boxes" readonly />
                                        <!--<input type="hidden" name="barcodeNo_1" id="barcodeNo_1"/>-->
                                    </td>
                                    <td>
                                        <input type="text" name="barcodeNo[]" id="barcodeNo_1" class="text_boxes_numeric" style="width:70px" placeholder="Display" readonly />
                                    </td>
                                    <td>
                                        <input type="text" name="txtBatchQnty[]" id="txtBatchQnty_1" class="text_boxes_numeric" onKeyUp="calculate_batch_qnty();" style="width:60px" />
                                    </td>
                                    <td>
                                        <input type="text" name="txtQtyPcs[]" id="txtQtyPcs_1" class="text_boxes_numeric" onKeyUp="calculate_qtyPcs();" style="width:60px;" disabled />
                                    </td>
                                    <td>
                                        <input type="text" name="txtSize[]" id="txtSize_1" class="text_boxes" style="width:60px;" disabled />
                                    </td>
                                    <td>
                                        <input type="text" name="txtPoBatchNo[]" id="txtPoBatchNo_1" class="text_boxes_numeric" style="width:45px" disabled />
                                    </td>
									<td id="ColorTypeTd_1">
                                        <?
                                        echo create_drop_down( "cboColorType_1", 100, $color_type,"", 1, "-- Select --", 0, "fnc_copy_colorType(1)", "", "", "", "", "", "", "", "cboColorType[]" );
                                        ?>
                                    </td>
                                    <td>
                                        <input type="text" name="txtLotNo[]" id="txtLotNo_1" class="text_boxes" style="width:60px;" disabled />
                                    </td>
                                    <td>
                                        <input type="text" name="txtRemarks[]" id="txtRemarks_1" class="text_boxes" style="width:100px;" />
                                    </td>
                                    <td width="65">
                                        <input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1)" />
                                        <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="tbl_bottom">
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>Sum</td>
                                <td><input type="text" name="txt_total_batch_qnty" id="txt_total_batch_qnty" class="text_boxes_numeric" style="width:60px" readonly /></td>
                                <td><input type="text" name="txt_total_qtyPcs" id="txt_total_qtyPcs" class="text_boxes_numeric" style="width:60px" readonly /></td>
                                <td><input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes_numeric" readonly /></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tfoot>
                        </table>
                    </fieldset>
                    <table width="1140">
                        <tr>
                            <td colspan="5" align="center" class="button_container">
                                <?
                                $date=date('d-m-Y');
                                echo load_submit_buttons($permission, "fnc_batch_creation",0,0,"reset_form('batchcreation_1','list_color','','cbo_batch_against,1*txt_batch_date,".$date."','disable_enable_fields(\'txt_booking_no*txt_batch_color*cboPoNo_1*cboItemDesc_1*cboDiaWidthType_1*txtRollNo_1*hideRollNo_1*txtBatchQnty_1*hide_job_no*cbo_shift_name*txt_dyeing_pdo\',0)'); $('#txt_ext_no').val(''); $('#txt_ext_no').attr('disabled','disabled');$('#txt_batch_number').removeAttr('readOnly','readOnly');$('#tbl_item_details tbody tr:not(:first)').remove();",1);
                                ?>
                                <input type="button" id="Print_1" value="Batch Card 1" class="formbutton" onClick="fnc_batch_creation(4)" style="width:110px; display: none;">
                                <input type="button" id="Print2" value="Batch Card 2" class="formbutton" onClick="fnc_batch_creation(5)" style="width:110px; display: none;">
                                <input type="button" id="Print3" value="Batch Card 3" class="formbutton" onClick="fnc_batch_creation(6)" style="width:110px; display: none;">
                                <input type="button" id="Print4" value="Batch Card 4" class="formbutton" onClick="fnc_batch_creation(7)" style="width:110px; display: none;">
                                <input type="button" id="Print5" value="Batch Card 5" class="formbutton" onClick="fnc_batch_creation(8)" style="width:110px; display: none;">
                                <input type="button" id="Print6" value="Batch Card 6" class="formbutton" onClick="fnc_batch_creation(9)" style="width:110px; display: none;">
                                <input type="button" id="Print7" value="Batch Card 7" class="formbutton" onClick="fnc_batch_creation(10)" style="width:110px; display: none;">
                                <input type="button" id="Print8" value="Batch Card 8" class="formbutton" onClick="fnc_batch_creation(11)" style="width:110px; display: none;">
                                <input type="button" id="Print9" value="Batch Card 9" class="formbutton" onClick="fnc_batch_creation(12)" style="width:110px; display: none;">
                                <input type="button" id="Print10" value="Batch Card 10" class="formbutton" onClick="fnc_batch_creation(13)" style="width:110px; display: none;">
                                <input type="button" id="Print11" value="Batch Card 11" class="formbutton" onClick="fnc_batch_creation(14)" style="width:110px; display: none;">
                                <input type="button" id="Print12" value="Batch Card 12" class="formbutton" onClick="fnc_batch_creation(15)" style="width:110px; display: none;">
                                <input type="button" id="Print13" value="Prog.Wise" class="formbutton" onClick="fnc_batch_creation(16)" style="width:110px; display: none;">
                                <input type="button" id="Print14" value="Batch Card 14" class="formbutton" onClick="fnc_batch_creation(17)" style="width:110px; display: none;">
                                <input type="button" id="Print15" value="Batch Card 15" class="formbutton" onClick="fnc_batch_creation(18)" style="width:110px; display: none;">
								<input type="button" id="Print16" value="Batch Card 16" class="formbutton" onClick="fnc_batch_creation(19)" style="width:110px; display: none;">
								<input type="button" id="Print17" value="Batch Card 17" class="formbutton" onClick="fnc_batch_creation(20)" style="width:110px; display: none;">
								<input type="button" id="Print17_back" value="Batch Card 17 (Back Page)" class="formbutton" onClick="fnc_batch_creation(23)" style="width:110px; display: none;">
								<input type="button" id="Print18" value="Batch Card 18" class="formbutton" onClick="fnc_batch_creation(21)" style="width:110px; display: none;">
								<input type="button" id="Print19" value="Batch Card 19" class="formbutton" onClick="fnc_batch_creation(22)" style="width:110px; display: none;">
								<input type="button" id="Print20" value="Batch Card 20 (Back)" class="formbutton" onClick="fnc_batch_creation(24)" style="width:120px; display: none;">
								<input type="button" id="Print21" value="Batch Card 21" class="formbutton" onClick="fnc_batch_creation(25)" style="width:120px; display: none;">
								<input type="button" id="Print22" value="Batch Card 22" class="formbutton" onClick="fnc_batch_creation(26)" style="width:120px; display: none;">
								<input type="button" id="Print2v1" value="Batch Card 2.1" class="formbutton" onClick="fnc_batch_creation(27)" style="width:110px; display: none;">

                                <input type="hidden" name="update_id" id="update_id"/>
                                <input type="hidden" name="hide_update_id" id="hide_update_id"/>
                                <input type="hidden" name="hide_batch_against" id="hide_batch_against"/>
                                <input type="hidden" name="roll_maintained" id="roll_maintained" readonly>
                                <input type="hidden" name="batch_no_creation" id="batch_no_creation" readonly>
                                <input type="hidden" name="batch_maintained" id="batch_maintained" readonly>
                                <input type="hidden" name="hide_job_no" id="hide_job_no" readonly><!--For Duplication Check-->
                                <input type="hidden" name="txt_within_group" id="txt_within_group" readonly><!--For Duplication Check-->
                                <input type="hidden" name="txt_search_type" id="txt_search_type" readonly><!--For Duplication Check-->
                                <input type="hidden" name="fabric_source" id="fabric_source" readonly>
                                <input type="hidden" name="unloaded_batch" id="unloaded_batch" readonly>
                                <input type="hidden" name="ext_from" id="ext_from" readonly>
                                <input type="hidden" name="style_breakdown_variable_sett" id="style_breakdown_variable_sett">
                            </td>
                        </tr>
                    </table>
                </form>
            </fieldset>
        </div>
        <div style="width:10px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
        <div id="list_color" style="width:410px; overflow:auto; float:left; margin-left:300px; padding-top:5px; margin-top:5px; position:relative;"></div>
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
//$('#txt_process_id').val(mandatory_subprocess);
load_drop_down('requires/batch_creation_controller',document.getElementById('cbo_floor').value, 'load_drop_machine', 'td_dyeing_machine');

	$(document).ready(function()
	{
		for (var property in mandatory_field_arr) {
		  $("#"+mandatory_field_arr[property]).parent().prev('td').css("color", "blue");
		}
	});
</script>
</html>
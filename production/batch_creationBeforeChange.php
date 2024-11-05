<?
/*-------------------------------------------- Comments
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
	<?
		$scanned_barcode_data=sql_select("select barcode_no from pro_roll_details where entry_form=64 and status_active=1 and is_deleted=0");
		foreach($scanned_barcode_data as $row)
		{
			$scanned_barcode_array[]=$row[csf('barcode_no')];
		}
		$jsscanned_barcode_array= json_encode($scanned_barcode_array);
		echo "scanned_barcode = ". $jsscanned_barcode_array . ";\n";
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
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_batch_against = $('#cbo_batch_against').val();
		var batch_for = $('#cbo_batch_for').val();
		
		if (form_validation('cbo_batch_against*cbo_batch_for*cbo_company_id','Batch Against*Batch For*Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Booking Selection Form';	
			var page_link = 'requires/batch_creation_controller.php?cbo_company_id='+cbo_company_id+'&batch_against='+cbo_batch_against+'&action=fabricBooking_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=980px,height=400px,center=1,resize=1,scrolling=0','');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var theemail=this.contentDoc.getElementById("hidden_booking_id").value;	 //Access form field with id="emailfield"
				var theename=this.contentDoc.getElementById("hidden_booking_no").value; //Access form field with id="emailfield"
				var theecolor_id=this.contentDoc.getElementById("hidden_color_id").value; //Access form field with id="emailfield"
				var theecolor=this.contentDoc.getElementById("hidden_color").value; //Access form field with id="emailfield"
				var job_no=this.contentDoc.getElementById("hidden_job_no").value; //Access form field with id="emailfield"
				var booking_without_order=this.contentDoc.getElementById("booking_without_order").value; //Access form field with id="emailfield"
				
				$('#txt_booking_no_id').val(theemail);
				$('#txt_booking_no').val(theename);
				$('#txt_batch_color').val(theecolor);
				$('#booking_without_order').val(booking_without_order);
				
				reset_form('','','cboProgramNo_1*cboPoNo_1*poId_1*cboItemDesc_1*cboBodyPart_1*txtRollNo_1*barcodeNo_1*hideRollNo_1*txtBatchQnty_1*txt_total_batch_qnty*txtPoBatchNo_1*hide_job_no','',"$('#tbl_item_details tbody tr:not(:first)').remove();",'');
				
				var roll_maintained=$('#roll_maintained').val();
				var batch_maintained=$('#batch_maintained').val();
				if(roll_maintained!=1)
				{
					if(booking_without_order==0)
					{
						load_drop_down( 'requires/batch_creation_controller', theename, 'load_drop_down_program', 'programNoTd_1' );
						var length=$("#cboProgramNo_1 option").length;
						if(length==2)
						{
							$('#cboProgramNo_1').val($('#cboProgramNo_1 option:last').val());
							load_drop_down( 'requires/batch_creation_controller', $('#cboProgramNo_1').val()+'**'+1+"**"+theename+'**'+theecolor_id, 'load_drop_down_po_from_program', 'poNoTd_1');
						}
						else
						{
							load_drop_down( 'requires/batch_creation_controller', theename+'**'+theecolor_id, 'load_drop_down_po', 'poNoTd_1' );
						}
						
						var po_length=$("#cboPoNo_1 option").length;
						if(po_length==2)
						{
							var program_no=$('#cboProgramNo_1').val();
							$('#cboPoNo_1').val($('#cboPoNo_1 option:last').val());
							load_drop_down( 'requires/batch_creation_controller', $('#cboPoNo_1').val()+'**'+1+'**'+booking_without_order+'**'+program_no+'**'+batch_maintained, 'load_drop_down_item_desc', 'itemDescTd_1' );
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
					else
					{
						$("#cboProgramNo_1 option[value!='0']").remove();
						$("#cboPoNo_1 option[value!='0']").remove();
						load_drop_down( 'requires/batch_creation_controller', theename+'**1**'+booking_without_order+"**0**"+batch_maintained, 'load_drop_down_item_desc', 'itemDescTd_1' );
						var item_length=$("#cboItemDesc_1 option").length;
						if(item_length==2)
						{
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
				else
				{
					<?
						$scanned_barcode_data=sql_select("select barcode_no from pro_roll_details where entry_form=64 and status_active=1 and is_deleted=0");
						foreach($scanned_barcode_data as $row)
						{
							$scanned_barcode_array[]=$row[csf('barcode_no')];
						}
						$jsscanned_barcode_array= json_encode($scanned_barcode_array);
						echo "scanned_barcode = ". $jsscanned_barcode_array . ";\n";
					?>
				}
				
				show_list_view(theename+'**'+booking_without_order,'show_color_listview','list_color','requires/batch_creation_controller','');	
			}
		}
	}
	
	function active_inactive()
	{
		reset_form('','list_color','txt_booking_no*txt_booking_no_id*txt_ext_no*cbo_color_range*txt_organic*txt_process_name*txt_process_id*txt_du_req_hr*txt_du_req_min*txt_batch_color*cboProgramNo_1*cboPoNo_1*poId_1*cboItemDesc_1*cboBodyPart_1*cboDiaWidthType_1*txtRollNo_1*barcodeNo_1*hideRollNo_1*txtBatchQnty_1*txt_total_batch_qnty*txtPoBatchNo_1*hide_job_no','','$(\'#tbl_item_details tbody tr:not(:first)\').remove();','');
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
			
			$('#poNoTd_1').html('<select name="cboPoNo[]" id="cboPoNo_1" class="combo_boxes" style="width:130px"><option value="0">-- Select Po Number --</option></select>');
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
			$('#txtBatchQnty_1').removeAttr('disabled','disabled');
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
		reset_form('','','txt_ext_no*txt_batch_weight*update_id*txt_batch_sl_no*hide_update_id*cboProgramNo_1*cboPoNo_1*poId_1*cboItemDesc_1*cboBodyPart_1*cboDiaWidthType_1*txtRollNo_1*barcodeNo_1*hideRollNo_1*txtBatchQnty_1*txt_total_batch_qnty*txtPoBatchNo_1*hide_job_no','','$(\'#tbl_item_details tbody tr:not(:first)\').remove();','');
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
			$scanned_barcode_data=sql_select("select barcode_no from pro_roll_details where entry_form=64 and status_active=1 and is_deleted=0");
			foreach($scanned_barcode_data as $row)
			{
				$scanned_barcode_array[]=$row[csf('barcode_no')];
			}
			$jsscanned_barcode_array= json_encode($scanned_barcode_array);
			echo "scanned_barcode = ". $jsscanned_barcode_array . ";\n";
		?>
	}
	
	function add_break_down_tr( i )
	{ 
		if (form_validation('cbo_batch_against*cbo_batch_for*cbo_company_id','Batch Against*Batch For*Company Name')==false)
		{
			return;
		}
		
		var batch_against= $('#cbo_batch_against').val();
		var batch_for= $('#cbo_batch_for').val();
		var roll_maintained=$('#roll_maintained').val();
		var booking_without_order=$('#booking_without_order').val();
		
		if(batch_against!=2)
		{
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
				
				$('#updateIdDtls_'+i).removeAttr("value").attr("value","");
				$('#poId_'+i).removeAttr("value").attr("value","");
				$('#txtRollNo_'+i).removeAttr("value").attr("value","");
				$('#barcodeNo_'+i).removeAttr("value").attr("value","");
				$('#hideRollNo_'+i).removeAttr("value").attr("value","");
				$('#txtBatchQnty_'+i).removeAttr("value").attr("value","");
				$('#txtPoBatchNo_'+i).removeAttr("value").attr("value","");
				
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
						$('#cboPoNo_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_po("+i+");");
					}
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
		}
	}
	
	function fn_deleteRow(rowNo) 
	{ 
		if($('#cbo_batch_against').val()!=2)
		{
			var numRow = $('#tbl_item_details tbody tr').length; 
			//if(numRow==rowNo && rowNo!=1)
			if(rowNo!=1)
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
		}
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
	}
	
	function roll_maintain()
	{
		reset_form('','list_color','txt_booking_no*txt_booking_no_id*booking_without_order*txt_batch_color*cboProgramNo_1*cboPoNo_1*poId_1*cboItemDesc_1*cboBodyPart_1*txtRollNo_1*barcodeNo_1*hideRollNo_1*txtBatchQnty_1*txt_total_batch_qnty*txtPoBatchNo_1*hide_job_no','','$(\'#tbl_item_details tbody tr:not(:first)\').remove();','');
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
		if(operation==4)
		{
			 var report_title=$( "div.form_caption" ).html();
			 generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title,'batch_card_print','requires/batch_creation_controller');
			 return;
		}
		
		if(operation==5)//Print Button 2;
		{
			var update_id=$('#update_id').val();
			
			if(update_id=="")
			{
				alert("Save Data First");return;
			}
			 var report_title=$( "div.form_caption" ).html();
			 generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title,'batch_card_print_2','requires/batch_creation_controller');
			 return;
		}
			
		/*if(operation==2)
		{
			show_msg('13');
			return;
		}*/
		
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
		
		if( form_validation('cbo_batch_against*cbo_batch_for*cbo_company_id*txt_batch_date*txt_batch_weight*txt_batch_color','Batch Against*Batch For*Company*Batch Date*Batch Weight*Batch Color')==false )
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
		//alert(save_data);return;
		var txt_batch_weight=$('#txt_batch_weight').val();
		var batch_qty=$('#txt_total_batch_qnty').val()*1+$('#txt_tot_trims_weight').val()*1;
		var tot_batch_weight=Math.round(batch_qty * 1e12) / 1e12; 
		///alert(txt_batch_weight+"=="+tot_batch_weight);return;
		if(txt_batch_weight!=tot_batch_weight)
		{
			alert('Batch Weight and Total Batch Qnty+Trims Weight should be same.');
			return;
		}
		
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
		
		var j=0; var breakOut = true; 
		$("#tbl_item_details tbody").find('tr').each(function()
		{
			if(breakOut==false)
			{
				return;	
			}
			var trId = $(this).attr('id').split('_');
			var i=trId[1];
			//alert(i);return;
			if($('#cbo_batch_against').val()==1 || $('#cbo_batch_against').val()==5)
			{
				if (form_validation('cboPoNo_'+i,'PO NO')==false)
				{
					breakOut = false;
					return false;
				}
			}
			
			if (form_validation('cboItemDesc_'+i+'*txtBatchQnty_'+i+'*cboDiaWidthType_'+i,'Item Description*Batch Qnty*Dia/ W. Type')==false)
			{
				breakOut = false;
				return false;//cboDiaWidthType_1
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
			data_all+="&"+po_field + j + "='" + $('#'+po_field+i).val()+"'"+"&cboItemDesc_" + j + "='" + $('#cboItemDesc_'+i).val()+"'"+"&cboBodyPart_" + j + "='" + $('#cboBodyPart_'+i).val()+"'"+"&txtRollNo_" + j + "='" + $('#txtRollNo_'+i).val()+"'"+"&hideRollNo_" + j + "='" + $('#hideRollNo_'+i).val()+"'"+"&txtBatchQnty_" + j + "='" + $('#txtBatchQnty_'+i).val()+"'"+"&updateIdDtls_" + j + "='" + $('#updateIdDtls_'+i).val()+"'"+"&cboProgramNo_" + j + "='" + $('#cboProgramNo_'+i).val()+"'"+"&txtPoBatchNo_" + j + "='" + $('#txtPoBatchNo_'+i).val()+"'"+"&cboDiaWidthType_" + j + "='" + $('#cboDiaWidthType_'+i).val()+"'"+"&barcodeNo_" + j + "='" + $('#barcodeNo_'+i).val()+"'";
			
			//data_all+=get_submitted_data_string(po_field+'*cboItemDesc_'+i+'*cboBodyPart_'+i+'*txtRollNo_'+i+'*hideRollNo_'+i+'*txtBatchQnty_'+i+'*updateIdDtls_'+i+'*cboProgramNo_'+i+'*txtPoBatchNo_'+i+'*cboDiaWidthType_'+i+'*barcodeNo_'+i,"../",i);
			
			
		});
		
		if(breakOut==false)
		{
			return;	
		}
		//alert(data_all);return;
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_batch_sl_no*cbo_batch_against*cbo_batch_for*cbo_company_id*batch_no_creation*batch_maintained*txt_batch_number*txt_batch_date*txt_batch_weight*txt_tot_trims_weight*txt_booking_no_id*txt_booking_no*txt_ext_no*txt_batch_color*cbo_color_range*txt_organic*txt_process_id*txt_du_req_hr*txt_du_req_min*update_id*booking_without_order*hide_update_id*hide_batch_against*roll_maintained*txt_remarks*txt_cuff_qty*txt_color_qty*cbo_machine_name*save_data',"../")+data_all+'&total_row='+row_num+'&txt_deleted_id='+txt_deleted_id;
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
			//release_freezing(); alert(http.responseText);return;
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
			else if((reponse[0]==0 || reponse[0]==1))
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_batch_sl_no').value = reponse[2];
				document.getElementById('txt_batch_number').value = reponse[3];
				var batch_against=$('#cbo_batch_against').val();
				
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
				//alert(theemail);return;
				if(batch_id!="")
				{
					freeze_window(5);
					get_php_form_data(batch_against+'**'+batch_for+'**'+batch_id, "populate_data_from_search_popup", "requires/batch_creation_controller" );
				    show_list_view(batch_against+'**'+batch_for+'**'+batch_id+'**'+roll_maintained+'**'+batch_maintained,'batch_details','batch_details_container','requires/batch_creation_controller','');
					release_freezing();
					if(roll_maintained==1)
					{
						<?
							$scanned_barcode_data=sql_select("select barcode_no from pro_roll_details where entry_form=64 and status_active=1 and is_deleted=0");
							foreach($scanned_barcode_data as $row)
							{
								$scanned_barcode_array[]=$row[csf('barcode_no')];
							}
							$jsscanned_barcode_array= json_encode($scanned_barcode_array);
							echo "scanned_barcode = ". $jsscanned_barcode_array . ";\n";
						?>
					}
					$('#txt_deleted_id').val('');
					calculate_batch_qnty();
				} 
			}
		}
	}
	
	function openmypage_process()
	{
		var txt_process_id = $('#txt_process_id').val();
		var title = 'Process Name Selection Form';	
		var page_link = 'requires/batch_creation_controller.php?txt_process_id='+txt_process_id+'&action=process_name_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var process_id=this.contentDoc.getElementById("hidden_process_id").value;	 //Access form field with id="emailfield"
			var process_name=this.contentDoc.getElementById("hidden_process_name").value;
			$('#txt_process_id').val(process_id);
			$('#txt_process_name').val(process_name);
		}
	}
	
	function load_item_desc(value,id)
	{
		var item_id=id.split("_");
		var row_no=item_id[1];
		var itemTdId='itemDescTd_'+row_no;
		var booking_without_order=$('#booking_without_order').val();
		var booking_no=$('#txt_booking_no').val();
		var color=$('#txt_batch_color').val();
		var batch_against=$('#cbo_batch_against').val();
		var batch_maintained=$('#batch_maintained').val();
			
		if(item_id[0]=='cboProgramNo')
		{
			if(batch_against!=5)
			{
				var color_id =return_global_ajax_value( booking_no+'**'+color, 'populate_color_id', '', 'requires/batch_creation_controller');
				
				var poNoTd='poNoTd_'+row_no;
				load_drop_down( 'requires/batch_creation_controller', value+'**'+row_no+'**'+booking_no+'**'+color_id, 'load_drop_down_po_from_program', poNoTd);
				var po_length=$("#cboPoNo_"+row_no+" option").length;
				if(po_length==2)
				{
					$('#cboPoNo_'+row_no).val($('#cboPoNo_'+row_no+' option:last').val());
					load_drop_down( 'requires/batch_creation_controller', $('#cboPoNo_'+row_no).val()+'**'+row_no+'**'+booking_without_order+'**'+value+'**'+batch_maintained, 'load_drop_down_item_desc', itemTdId );
				}
				else
				{
					$("#cboItemDesc_"+row_no+" option[value!='0']").remove();
				}
			}
		}
		else
		{
			var program_no=$('#cboProgramNo_'+row_no).val();
			load_drop_down( 'requires/batch_creation_controller', value+'**'+row_no+'**'+booking_without_order+'**'+program_no+'**'+batch_maintained, 'load_drop_down_item_desc', itemTdId );
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
		$('#txtPoBatchNo_'+row_no).val('');
	}
	
	function load_body_part(value,id)
	{
		var body_part_id=id.split("_");
		var row_no=body_part_id[1];
		var bodyPartTdId='itemDescTd_'+row_no;
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
			load_drop_down( 'requires/batch_creation_controller', booking_no+'**'+row_no+'**1**'+value, 'load_drop_down_body_part', 'bodyPartTd_'+row_no );
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
	
	function put_country_data(color_id,color)
	{
		var batch_against = $('#cbo_batch_against').val();
		var booking_without_order=$('#booking_without_order').val();
		var booking_no=$('#txt_booking_no').val();
		var prev_color=$('#txt_batch_color').val();
		var roll_maintained=$('#roll_maintained').val();
		var batch_maintained=$('#batch_maintained').val();

		if(prev_color!=color)
		{
			if(batch_against!=2)
			{
				reset_form('','','txt_batch_sl_no*txt_batch_weight*txt_tot_trims_weight*save_data*txt_batch_number*txt_ext_no*cbo_color_range*txt_organic*txt_process_name*txt_process_id*txt_du_req_hr*txt_du_req_min*update_id*hide_update_id*hide_batch_against*txt_total_batch_qnty*hide_job_no*txt_deleted_id','',"$('#tbl_item_details tbody tr:not(:first)').remove();",'');
				
				$('#txt_batch_color').val(color);
				$("#batch_details_container").find('select,input:not([type=button])').val('');
				
				if(roll_maintained!=1)
				{
					if(booking_without_order==1)
					{
						$("#cboProgramNo_1 option[value!='0']").remove();
						$("#cboPoNo_1 option[value!='0']").remove();
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
					else
					{
						load_drop_down( 'requires/batch_creation_controller', booking_no+'**'+color_id, 'load_drop_down_po', 'poNoTd_1' );
						var length=$("#cboProgramNo_1 option").length;
						if(length>2) $('#cboProgramNo_1').val(0);
						var po_length=$("#cboPoNo_1 option").length;
						if(po_length==2)
						{
							var program_no=$('#cboProgramNo_1').val();
							$('#cboPoNo_1').val($('#cboPoNo_1 option:last').val());
							load_drop_down( 'requires/batch_creation_controller', $('#cboPoNo_1').val()+'**'+1+'**'+booking_without_order+'**'+program_no+'**'+batch_maintained, 'load_drop_down_item_desc', 'itemDescTd_1' );
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
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/batch_creation_controller.php?company_id='+company_id+'&action=barcode_popup','Barcode Popup', 'width=940px,height=350px,center=1,resize=1,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var barcode_nos=this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos
			if(barcode_nos!="")
			{
				barcode_data(barcode_nos);
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
			
			if( jQuery.inArray( bar_code, scanned_barcode )>-1) 
			{ 
				alert('Sorry! Barcode Already Scanned.'); 
				$('#txt_bar_code_num').val('');
				return; 
			}
			
			barcode_data(bar_code);
		}
	});
	
	function barcode_data(barcode_nos)
	{
		var barcode_data=return_global_ajax_value( barcode_nos, 'populate_barcode_data', '', 'requires/batch_creation_controller');
		
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
		var barcode_datas=barcode_data.split("_");
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
					$("#cboPoNo_"+row_num+" option[value='"+po_breakdown_id+"']").remove();
					$("#cboPoNo_"+row_num).append("<option selected value='"+po_breakdown_id+"'>"+po_number+"</option>");
				}
				
				$("#cboItemDesc_"+row_num+" option[value='"+prod_id+"']").remove();
				$("#cboItemDesc_"+row_num).append("<option selected value='"+prod_id+"'>"+product_name_details+"</option>");
				
				$("#cboBodyPart_"+row_num+" option[value='"+body_part_id+"']").remove();
				$("#cboBodyPart_"+row_num).append("<option selected value='"+body_part_id+"'>"+body_part_name+"</option>");
				
				$('#txtRollNo_'+row_num).val(roll_no);
				$('#hideRollNo_'+row_num).val(roll_id);
				$('#txtBatchQnty_'+row_num).val(qnty);
				$('#barcodeNo_'+row_num).val(barcode_no);
				$('#cboDiaWidthType_'+row_num).val(widthDiaType);
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
			}
		
	}

</script>
</head>

<body onLoad="set_hotkey();">
<div style="width:100%;">
	<? echo load_freeze_divs ("../",$permission); ?>
    <div style="width:995px; float:left" align="center">
    <fieldset style="width:995px;">
    <legend>Batch Creation</legend> 
        <form name="batchcreation_1" id="batchcreation_1"> 
            <fieldset style="width:970px;">
                <table width="830" align="center" border="0">
                    <tr>
                        <td width="110" colspan="2" align="right"><b>Batch Serial No</b></td>
                        <td colspan="2">
                            <input type="text" name="txt_batch_sl_no" id="txt_batch_sl_no" class="text_boxes" style="width:160px;" placeholder="Display" disabled />
                        </td>
                    </tr>
                    <tr><td></td></tr>
                    <tr>
                        <td width="110" class="must_entry_caption">Batch Against</td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_batch_against", 172, $batch_against,"", 1, '--- Select ---', 1, "active_inactive();",'','1,2,3,5','','','',1 );
                            ?>                              
                        </td>
                        <td width="130" class="must_entry_caption">Batch Date</td>
                        <td>
                            <input type="text" name="txt_batch_date" id="txt_batch_date" class="datepicker" style="width:160px;" tabindex="6" value="<? echo date("d-m-Y"); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Batch For</td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_batch_for", 172, $batch_for,"", 0, '--- Select ---', 1, "",'1','1','','','',2 );
                            ?>                              
                        </td>
                        <td width="110" class="must_entry_caption">Batch Weight </td>
                        <td>
                            <input type="text" name="txt_batch_weight" id="txt_batch_weight" class="text_boxes_numeric" style="width:160px;" tabindex="7" />
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Company</td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_company_id", 172, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '--Select Company--', 0,"get_php_form_data(this.value,'batch_no_creation','requires/batch_creation_controller' );roll_maintain();load_drop_down( 'requires/batch_creation_controller',this.value, 'load_drop_machine', 'machine_td' );",'','','','','',3);
                            ?>                              
                        </td>
                        <td>Total Trims Weight</td>
                        <td>
                            <input type="text" name="txt_tot_trims_weight" id="txt_tot_trims_weight" class="text_boxes_numeric" style="width:160px;" tabindex="8" onDblClick="openmypage_trims()" placeholder="Double Click To Search" readonly />
                        <input type="hidden" name="save_data" id="save_data" class="text_boxes" style="width:100px;">
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Batch Number</td>
                        <td>
                            <input type="text" name="txt_batch_number" id="txt_batch_number" class="text_boxes" style="width:160px;" placeholder="Double Click To Edit" onDblClick="openmypage_batchNo()" tabindex="4" />
                        </td>
                        <td>Fabric Booking No</td>
                        <td>
                            <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:160px;" placeholder="Double Click To Search" onDblClick="openmypage_fabricBooking();" readonly tabindex="9"/>
                            <input type="hidden" name="txt_booking_no_id" id="txt_booking_no_id"/>
                            <input type="hidden" name="booking_without_order" id="booking_without_order"/>
                        </td>
                    </tr>
                    <tr>
                        <td>Extention No.</td>
                        <td>
                            <input type="text" name="txt_ext_no" id="txt_ext_no" class="text_boxes_numeric" style="width:160px;" disabled="disabled" tabindex="5" />
                        </td>
                        <td class="must_entry_caption">Batch Color</td>
                        <td>
                            <input type="text" name="txt_batch_color" id="txt_batch_color" class="text_boxes" value="" style="width:160px;" tabindex="10" disabled />
                        </td>
                    </tr>
                    <tr>
                        <td>Color Range</td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_color_range", 172, $color_range,"",1, "-- Select --", 0, "" );
                            ?>
                        </td>
                        <td>Organic</td>
                        <td>
                            <input type="text" name="txt_organic" id="txt_organic" class="text_boxes" style="width:160px;" tabindex="12" />
                        </td>
                    </tr>
                    <tr>
                    	<td>Process Name</td>
                        <td>
                            <input type="text" name="txt_process_name" id="txt_process_name" class="text_boxes" style="width:160px;" placeholder="Double Click To Search" onDblClick="openmypage_process();" tabindex="13" readonly />
                            <input type="hidden" name="txt_process_id" id="txt_process_id" value="" />
                        </td>
                        <td>Duration Req.</td>
                        <td>
                            <input type="text" name="txt_du_req_hr" id="txt_du_req_hr" class="text_boxes_numeric" placeholder="Hour" onKeyUp="fnc_move_cursor(this.value,'txt_du_req_hr','txt_end_date',2,23)" style="width:70px;" />&nbsp;
                            <input type="text" name="txt_du_req_min" id="txt_du_req_min" class="text_boxes_numeric" onKeyUp="fnc_move_cursor(this.value,'txt_du_req_min','txt_end_date',2,59)" placeholder="Minute" style="width:70px;" />
                        </td>
                    </tr>
                    <tr>
                    	<td>Collar Qty (Pcs)</td>
                        <td>
                            <input type="text" name="txt_color_qty" id="txt_color_qty" class="text_boxes_numeric" style="width:160px;"/>
                        </td>
                        <td>Cuff Qty (Pcs)</td>
                      	<td>
                            <input type="text" name="txt_cuff_qty" id="txt_cuff_qty" class="text_boxes_numeric" style="width:160px;"/>
                        </td>
                    </tr>
                    <tr>
                    	<td>Dyeing Machine</td>
                        <td  id="machine_td"><? echo create_drop_down("cbo_machine_name", 172, $blank_array,"", 1, "-- Select Machine --", 0, "",0,"","","","");?></td>
                        <td>Remarks</td>
                        <td><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:160px;" /></td>
                    </tr>
                 </table>
            </fieldset>                 
            <fieldset style="width:990px; margin-top:10px">
            <legend>Item Details</legend>
            	<div style="margin-bottom:5px; display:none" id="barcode_div">
                    <strong>Roll Number</strong>&nbsp;&nbsp;
                    <input type="text" name="txt_bar_code_num" id="txt_bar_code_num" class="text_boxes_numeric" style="width:170px;" onDblClick="openmypage_barcode()" placeholder="Browse/Write/scan"/>
                </div>
                <table cellpadding="0" cellspacing="0" width="985" class="rpt_table" border="1" rules="all" id="tbl_item_details">
                    <thead>
                    	<th>SL</th>
                        <th>Program No</th>
                        <th class="must_entry_caption">PO No.</th>
                        <th class="must_entry_caption">Item Description</th>
                        <th>Body Part</th>
                        <th class="must_entry_caption">Dia/ W. Type</th>
                        <th>Roll No.</th>
                        <th>Barcode No.</th>
                        <th class="must_entry_caption">Batch Qnty</th>
                        <th>PO Batch No</th>
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
									echo create_drop_down( "cboDiaWidthType_1", 90, $fabric_typee,"",1, "-- Select --", 0, "", "", "", "", "", "", "", "", "cboDiaWidthType[]" );
                                ?>
                            </td>                              
                            <td>
                                <input type="text" name="txtRollNo[]" id="txtRollNo_1" class="text_boxes_numeric" style="width:50px" disabled="disabled" /> <!--onDblClick="openmypage_rollnum(1)"-->
                                <input type="hidden" name="hideRollNo[]" id="hideRollNo_1" class="text_boxes" readonly />
                                <input type="hidden" name="poId[]" id="poId_1" class="text_boxes" readonly />
                                <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_1" class="text_boxes" readonly />
                                <!--<input type="hidden" name="barcodeNo_1" id="barcodeNo_1"/>-->
                            </td>
                            <td>
                                <input type="text" name="barcodeNo[]" id="barcodeNo_1" class="text_boxes_numeric" style="width:70px" placeholder="Display" readonly />
                            </td>
                            <td>
                                <input type="text" name="txtBatchQnty[]" id="txtBatchQnty_1" class="text_boxes_numeric" onKeyUp="calculate_batch_qnty();" style="width:60px" />
                            </td>
                            <td>
                                <input type="text" name="txtPoBatchNo[]" id="txtPoBatchNo_1" class="text_boxes_numeric" style="width:45px" disabled />
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
                        <td><input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes_numeric" readonly /></td>
                        <td>&nbsp;</td>
                    </tfoot>
                </table>
            </fieldset> 
            <table width="985">
                <tr>
                    <td colspan="5" align="center" class="button_container">
                        <? 
                            $date=date('d-m-Y');
                            echo load_submit_buttons($permission, "fnc_batch_creation",0,1,"reset_form('batchcreation_1','list_color','','cbo_batch_against,1*txt_batch_date,".$date."','disable_enable_fields(\'txt_booking_no*txt_batch_color*cboPoNo_1*cboItemDesc_1*cboDiaWidthType_1*txtRollNo_1*hideRollNo_1*txtBatchQnty_1*hide_job_no\',0)'); $('#txt_ext_no').val(''); $('#txt_ext_no').attr('disabled','disabled');$('#txt_batch_number').removeAttr('readOnly','readOnly');$('#tbl_item_details tbody tr:not(:first)').remove();",1);
                        ?> 
                        <input type="button" value="Print 2" class="formbutton" onClick="fnc_batch_creation(5)" style="width:80px;">
                        <input type="hidden" name="update_id" id="update_id"/>
                        <input type="hidden" name="hide_update_id" id="hide_update_id"/>
                        <input type="hidden" name="hide_batch_against" id="hide_batch_against"/>
                        <input type="hidden" name="roll_maintained" id="roll_maintained" readonly>
                        <input type="hidden" name="batch_no_creation" id="batch_no_creation" readonly>
                        <input type="hidden" name="batch_maintained" id="batch_maintained" readonly>
                        <input type="hidden" name="hide_job_no" id="hide_job_no" readonly><!--For Duplication Check-->
                    </td>	  
                </tr>
            </table>
        </form>
    </fieldset>
    </div>
    <div style="width:10px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
    <div id="list_color" style="width:330px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	//$('#txt_process_id').val(mandatory_subprocess);
</script>
</html>
<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Roll Wise Finish Fabric Requisition For Transfer
				
Functionality	:	
JS Functions	:
Created by		:	Md. Abu Sayed
Creation date 	: 	04-11-2021
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
echo load_html_head_contents("Roll Wise Finish Fabric Requisition For Transfer","../../", 1, 1, $unicode,'',''); 

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	var scanned_barcode=new Array();  var scanned_barcode_issue =new Array(); var barcode_rollTableId_array=new Array();
	var barcode_trnasId_array =new Array(); var barcode_dtlsId_array=new Array(); var barcode_trnasId_to_array=new Array();
	//var batch_batcode_arr=new Array();
	<?
	//$sql_batch=sql_select("select barcode_no from  pro_roll_details where entry_form=82 and  status_active=1 and is_deleted=0");
	//$batch_batcode_arr=array();
	//foreach($sql_batch as $inf)
	//{
	//$batch_batcode_arr[]=$inf[csf('barcode_no')];	
	//}
	//$jsbatch_batcode_arr= json_encode($batch_batcode_arr);
	//echo "var batch_batcode_arr = ". $jsbatch_batcode_arr . ";\n"
	?>
	function openmypage_issue()
	{ 
		var cbo_company_id = $('#cbo_to_company_id').val();
		var cbo_to_company_id = $('#cbo_to_company_id').val();
		var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();
		if (form_validation('cbo_to_company_id','Requisition Company')==false)
		{
			return;
		}
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/roll_wise_finish_fabric_requisition_for_transfer_controller.php?cbo_company_id='+cbo_company_id+'&cbo_transfer_criteria='+cbo_transfer_criteria+'&cbo_to_company_id='+cbo_to_company_id+'&action=issue_popup','Requisition Popup', 'width=780px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var issue_id=this.contentDoc.getElementById("hidden_system_id").value;	 //challan Id and Number
			
			if(issue_id!="")
			{
				fnc_reset_form(1);
				get_php_form_data(issue_id, "populate_data_from_data", "requires/roll_wise_finish_fabric_requisition_for_transfer_controller");
					
				var barcode_nos=return_global_ajax_value( issue_id, 'barcode_nos', '', 'requires/roll_wise_finish_fabric_requisition_for_transfer_controller');
				if(trim(barcode_nos)!="")
				{
					
					create_row(1,barcode_nos,3);
					/*var barcode_upd=barcode_nos.split(",");
					for(var k=0; k<barcode_upd.length; k++)
					{
						create_row(1,barcode_upd[k]);
					}*/
				}
				set_button_status(1, permission, 'fnc_finish_fabric_issue_roll_wise',1);
				// $("#print_barcode").removeClass('formbutton_disabled');
				// $("#print_barcode").addClass('formbutton');
				// $("#btn_fabric_details").removeClass('formbutton_disabled');
			    // $("#btn_fabric_details").addClass('formbutton');
				// $("#btn_mc").removeClass('formbutton_disabled');
			    // $("#btn_mc").addClass('formbutton');
			    // $("#btn_mc_6").removeClass('formbutton_disabled');
			    // $("#btn_mc_6").addClass('formbutton');
			}
		}
	}
	
	function openmypage_barcode()
	{ 
		var company_id=$('#cbo_company_id').val();
		var cbo_transfer_criteria=$('#cbo_transfer_criteria').val();
		var cbo_store_name=$('#cbo_store_name').val();
		var cbo_store_name_from=$('#cbo_store_name_from').val();
		if (form_validation('cbo_transfer_criteria','Requisition Criteria')==false)
		{
			return;
		}
		else
		{
			if(cbo_transfer_criteria==1)
			{
				if (form_validation('cbo_company_id*cbo_to_company_id','From Company*To Company')==false)
				{
					alert("Please Select Both Company Field");return;
				}
			}
			else
			{
				if (form_validation('cbo_company_id*cbo_store_name*cbo_store_name_from','From Company*Store')==false)
				{
					if(company_id<1)
					{
						alert("Please Select From Company Field");return;
					}
					else if (cbo_store_name<1)
					{
						alert("Please Select To Store Field");return;
					}
					else
					{
						alert("Please Select From Store Field");return;
					}
					
				}
			}
		}
		var system_id=$('#update_id').val()*1;
		var j=0; var product_id=''; 
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
			var productId=$(this).find('input[name="productId[]"]').val();
			
			j++;
			product_id+=productId+',';
		});
		product_id = product_id.replace(/,\s*$/, ""); // remove last comma
		if (system_id=="")
		{ 
			var product_ids = 0;
		}
		else
		{
			var product_ids = product_id;
		}
		if (product_ids!="") 
		{
			var product_ids = product_ids.split(",");
			function onlyUnique(value, index, self) {
				return self.indexOf(value) === index;
			}
			var product_ids = product_ids.filter(onlyUnique);			
		}
		// alert(product_ids);
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/roll_wise_finish_fabric_requisition_for_transfer_controller.php?company_id='+company_id+'&cbo_transfer_criteria='+cbo_transfer_criteria+'&cbo_store_name='+cbo_store_name+'&cbo_store_name_from='+cbo_store_name_from+'&action=barcode_popup'+'&productId='+product_ids,'Barcode Popup', 'width=1155px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			// $('#barcode_check_id').val(1);

			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var barcode_nos=this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos

			/*var j=0; var dataString=''; 
			$("#scanning_tbl").find('tbody tr').each(function()
			{			
				var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
				
				j++;
				dataString+='&dtlsId_' + j + '=' + dtlsId;
				// alert(dataString);
			});*/

			fnc_reset_form(2);
			// var scanned_barcode=new Array();

			var system_id=$('#update_id').val();
			var save_barcode_nos=return_global_ajax_value( system_id, 'barcode_nos', '', 'requires/roll_wise_finish_fabric_requisition_for_transfer_controller');
			if(trim(save_barcode_nos)!="")
			{
				create_row(1,save_barcode_nos,3);
			}
			//var update_id=$('#update_id').val();
			//if (update_id=="") {
				//fnc_reset_form(2);
			//}

			if(barcode_nos!="")
			{
				var barcode_upd=barcode_nos.split(",");
				for(var k=0; k<barcode_upd.length; k++)
				{
					create_row(0,barcode_upd[k],3);
				}
				set_all_onclick();
			}
		}
	}
	
	function generate_report_file(data,action)
	{
		window.open("requires/roll_wise_finish_fabric_requisition_for_transfer_controller.php?data=" + data+'&action='+action, true );
	}

	function fnc_finish_fabric_issue_roll_wise( operation )
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		if(operation==4)
		{
			//alert();return;
			
			 /*print_report( $('#cbo_company_id').val()+'*'+$('#txt_requisition_no').val()+'*'+$('#update_id').val()+'*'+$('#txt_layPlan_No').val()+'*'+$('#cbo_template_id').val()+'*'+$('#hdn_variable_setting_status').val()+'*'+$('#hdn_variable_colorSize_status').val(), "grey_delivery_print", "requires/woven_fabric_requisition_for_cutting_entry_controller" );
			return;*/

			if ($('#update_id').val()=="") 
			{
				alert("Please Save First.");return;
			}

			// Press OK to show Summery and Press Cancel to Show Barcode wise
			var show_report_format = "0";
	        var r = confirm("Press \"OK\" to show Summery.\nPress \"Cancel\" to Show Barcode wise.");
	        if (r == true)
			{
	            show_report_format = "1";
	        }
	        else
			{
	            show_report_format = "0";
	        }
			var report_title = $("div.form_caption").html();
			generate_report_file($('#cbo_to_company_id').val() + '*' + $('#txt_transfer_no').val() + '*' + report_title + '*' + $('#update_id').val() + '*' + show_report_format+ '*' + $('#cbo_company_id').val()+ '*' + $('#txt_transfer_date').val()+ '*' + $('#txt_challan_no').val()+ '*' + $('#cbo_store_name_from').val()+ '*' + $('#cbo_store_name').val()+ '*' + $('#cbo_item_category').val()+ '*' + $('#cbo_transfer_criteria').val()+ '*' + $('#txt_remarks').val(), 'roll_issue_no_of_copy_print1');
	        return;


		}
		var transfer_criteria=$('#cbo_transfer_criteria').val()*1;
		if(form_validation('cbo_transfer_criteria','Requisition Criteria')==false)
		{
			return;
		}
		else
		{
			if(transfer_criteria==1)
			{
				if(form_validation('cbo_company_id*cbo_to_company_id*txt_transfer_date*cbo_store_name*cbo_store_name_from','From Company*To Company*Requisition Date*To Store*From Store')==false)
				{
					return; 
				}
			}
			else
			{
				if(form_validation('cbo_company_id*cbo_store_name*cbo_store_name_from*txt_transfer_date','From Company*To Store*From Store*Requisition Date')==false)
				{
					return; 
				}
			}
		}
                
        var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_transfer_date').val(), current_date)==false)
		{
			alert("Requisition Date Can not Be Greater Than Current Date");
			return;
		}

		var is_approved=$('#is_approved').val();		
		if(is_approved==1 || is_approved==3)
		{
			alert("Requisition is Approved. So Change Not Allowed");
			return;	
		}
                
		
		var j=0; var dataString=''; 
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var fromStoreId=$(this).find('input[name="fromStoreId[]"]').val();
			var toOrderId=$(this).find('input[name="toOrderId[]"]').val();
			var tobookingNo=$(this).find('input[name="tobookingNo[]"]').val();
			var tobookingId=$(this).find('input[name="toBookingMstId[]"]').val();
			var recvBasis=$(this).find('input[name="recvBasis[]"]').val();
			var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
			var progBookPiId=$(this).find('input[name="progBookPiId[]"]').val();
			var productId=$(this).find('input[name="productId[]"]').val();
			var orderId=$(this).find('input[name="orderId[]"]').val();
			var rollId=$(this).find('input[name="rollId[]"]').val();
			var rollWgt=$(this).find('input[name="rollWgt[]"]').val();
			var hiddenQtyInPcs=$(this).find('input[name="hiddenQtyInPcs[]"]').val();
			var cboToBodyPart=$(this).find('select[name="cboToBodyPart[]"]').val();
			var bodyPartId=$(this).find('input[name="bodyPartId[]"]').val();
			var rollNo=$(this).find("td:eq(3)").text();
			var constructCompo=$(this).find("td:eq(5)").text();
			
			var batchId=$(this).find('input[name="batchId[]"]').val();
			var yarnCount=$(this).find('input[name="yarnCount[]"]').val();
			var colorId=$(this).find('input[name="colorId[]"]').val();
			var rejectQtyVal=$(this).find('input[name="rejectQtyVal[]"]').val();
			var brandId=$(this).find('input[name="brandId[]"]').val();
			var rack=$(this).find('input[name="rack[]"]').val();
			var shelf=$(this).find('input[name="shelf[]"]').val();
			
			var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
			var transId=$(this).find('input[name="transId[]"]').val();
			var transIdTo=$(this).find('input[name="transIdTo[]"]').val();
			var rolltableId=$(this).find('input[name="rolltableId[]"]').val();
			var febDescripId=$(this).find('input[name="febDescripId[]"]').val();
			var machineNoId=$(this).find('input[name="machineNoId[]"]').val();
			var gsm=$(this).find('input[name="prodGsm[]"]').val();
			var diaWidth=$(this).find('input[name="diaWidth[]"]').val();
			var knitDetailsId=$(this).find('input[name="knitDetailsId[]"]').val();
			var transferEntryForm=$(this).find('input[name="transferEntryForm[]"]').val();
			var bookWithoutOrder=$(this).find('input[name="bookWithoutOrder[]"]').val();
			var rollMstId=$(this).find('input[name="rollMstId[]"]').val();
			var rollAmount=$(this).find('input[name="rollAmount[]"]').val();
			var fromBookingWithoutOrder=$(this).find('input[name="fromBookingWithoutOrder[]"]').val();
			
			j++;
			dataString+='&fromStoreId_' + j + '=' + fromStoreId +'&toOrderId_' + j + '=' + toOrderId +'&recvBasis_' + j + '=' + recvBasis + '&barcodeNo_' + j + '=' + barcodeNo + '&progBookPiId_' + j + '=' + progBookPiId + '&productId_' + j + '=' + productId + '&orderId_' + j + '=' + orderId + '&rollId_' + j + '=' + rollId + '&rollWgt_' + j + '=' + rollWgt + '&batchId_' + j + '=' + batchId + '&colorId_' + j + '=' + colorId + '&rejectQtyVal_' + j + '=' + rejectQtyVal + '&rack_' + j + '=' + rack + '&shelf_' + j + '=' + shelf + '&dtlsId_' + j + '=' + dtlsId + '&transId_' + j + '=' + transId + '&transIdTo_' + j + '=' + transIdTo + '&rolltableId_' + j + '=' + rolltableId + '&rollNo_' + j + '=' + rollNo+ '&febDescripId_' + j + '=' + febDescripId+ '&machineNoId_' + j + '=' + machineNoId+ '&gsm_' + j + '=' + gsm+ '&diaWidth_' + j + '=' + diaWidth+ '&knitDetailsId_' + j + '=' + knitDetailsId+ '&transferEntryForm_' + j + '=' + transferEntryForm+ '&bookWithoutOrder_' + j + '=' + bookWithoutOrder + '&rollMstId_' + j + '=' + rollMstId + '&constructCompo_' + j + '=' + constructCompo + '&rollAmount_' + j + '=' + rollAmount + '&fromBookingWithoutOrder_' + j + '=' + fromBookingWithoutOrder + '&hiddenQtyInPcs_' + j + '=' + hiddenQtyInPcs + '&bodyPartId_' + j + '=' + bodyPartId + '&cboToBodyPart_' + j + '=' + cboToBodyPart + '&tobookingNo_' + j + '=' + tobookingNo + '&toBookingMstId_' + j + '=' + tobookingId;
		});
		
		if(j<1)
		{
			alert('No data');
			return;
		}
		
		if (transfer_criteria==1 || transfer_criteria==4)
		{
			var jk=1;
			for (var jk; j>=jk; jk++) 
			{
				if ($('#txtToOrder_'+jk).val()=="") 
				{
					alert('Please Browse To Order');
					$('#txtToOrder_'+jk).focus();
					return;
				}
			}
		}
		// alert(dataString);return;
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_transfer_no*cbo_transfer_criteria*cbo_company_id*cbo_to_company_id*txt_transfer_date*txt_challan_no*cbo_item_category*cbo_store_name*cbo_store_name_from*update_id*txt_deleted_id*txt_deleted_trnsf_dtls_id*txt_deleted_trans_id*txt_deleted_prod_id*txt_deleted_prod_qty*txt_deleted_source_roll_id*txt_deleted_barcode*txt_remarks*cbo_ready_to_approved',"../../")+dataString;
		
		// alert(data);return;
		freeze_window(operation);
		
		http.open("POST","requires/roll_wise_finish_fabric_requisition_for_transfer_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_finish_fabric_issue_roll_wise_Reply_info;
	}

	function fnc_finish_fabric_issue_roll_wise_Reply_info()
	{
		if(http.readyState == 4) 
		{
			//release_freezing();return;
			var response=trim(http.responseText).split('**');
                        
            if (response[0] * 1 == 20 * 1) 
            {
                alert(response[1]);
                release_freezing();
                return;
            }
			show_msg(response[0]);
			
			if((response[0]==0 || response[0]==1))
			{
				fnc_reset_form(2);
				document.getElementById('update_id').value = response[1];
				var barcode_nos=return_global_ajax_value( response[1], 'barcode_nos', '', 'requires/roll_wise_finish_fabric_requisition_for_transfer_controller');
				if(trim(barcode_nos)!="")
				{
					create_row(1,barcode_nos,3);
				}

				
				document.getElementById('txt_transfer_no').value = response[2];
				$('#cbo_transfer_criteria').attr('disabled',true);
				$('#cbo_company_id').attr('disabled',true);
				$('#cbo_to_company_id').attr('disabled',true);
				$('#txt_deleted_id').val( '' );
				$('#txt_deleted_barcode').val( '' );
				$('#txt_deleted_trnsf_dtls_id').val( '' );
				$('#txt_deleted_trans_id').val( '' );
				$('#txt_deleted_prod_id').val( '' );
				$('#txt_deleted_prod_qty').val( '' );
				add_dtls_data(response[3]);
				set_button_status(1, permission, 'fnc_finish_fabric_issue_roll_wise',1);
				// $("#print_barcode").removeClass('formbutton_disabled');
				// $("#print_barcode").addClass('formbutton');
				// $("#btn_fabric_details").removeClass('formbutton_disabled');
			    // $("#btn_fabric_details").addClass('formbutton');
				// $("#btn_mc").removeClass('formbutton_disabled');
			    // $("#btn_mc").addClass('formbutton');
			}
			release_freezing();
		}
	}
	
	
	// var scanned_barcode=new Array();
	function create_row(is_update,barcode_no,barcode_check)
	{
		var row_num=$('#txt_tot_row').val();
		//var bar_code=$('#txt_bar_code_num').val();
		var bar_code=trim(barcode_no);
		var num_row =$('#scanning_tbl tbody tr').length; 
		var febDescripId=$('#febDescripId_'+row_num).val();
		var orderId=$('#orderId_'+row_num).val();
		// alert(febDescripId);
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();
		if (form_validation('cbo_transfer_criteria','Requisition Criteria')==false)
		{
			return;
		}
		else
		{
			if(cbo_transfer_criteria==1)
			{
				if (form_validation('cbo_company_id*cbo_company_id','From Company*To Company')==false)
				{
					alert("Please Select Both Company Field");return;
				}
			}
			else
			{
				if (form_validation('cbo_company_id*cbo_store_name','From Company*Store')==false)
				{
					alert("Please Select To Store Field");return;
				}
			}
			
		}
		var system_id=$('#update_id').val();
		if(is_update==0) // Save event
		{
			
			var barcode_data=return_global_ajax_value( bar_code+"**"+system_id, 'populate_barcode_data', '', 'requires/roll_wise_finish_fabric_requisition_for_transfer_controller');
			//alert(barcode_data);return;
			var barcode_data_all=new Array();
			barcode_data_all=barcode_data.split("**");
			//alert(job_no);return;
			
			
			var rcv_id=barcode_data_all[0];
			var company_id=barcode_data_all[1];
			var body_part_name=barcode_data_all[2];
			var receive_basis=barcode_data_all[3];
			var receive_basis_id=barcode_data_all[4];
			var receive_date=barcode_data_all[5];
			var booking_no=barcode_data_all[6];
			var booking_id=barcode_data_all[7];
			var color=barcode_data_all[8];
			var knitting_source_id=barcode_data_all[9];
			var knitting_source=barcode_data_all[10];
			var store_id=barcode_data_all[11];
			var knitting_company_id=barcode_data_all[12];
			//var yarn_lot=barcode_data_all[13];
			var batch_id=barcode_data_all[13];
			//var yarn_count=barcode_data_all[14];
			var batch_no=barcode_data_all[14];
			var reject_qty=barcode_data_all[15];
			//var reject_qty = 1;
			//var stitch_length=barcode_data_all[15];
			var rack=barcode_data_all[16];
			var self=barcode_data_all[17];
			var knitting_company_name=barcode_data_all[18];
			var dtls_id=barcode_data_all[19];
			var prod_id=barcode_data_all[20];
			var febric_description_id=barcode_data_all[21];
			var compsition_description=barcode_data_all[22];
			 //alert(febric_description_id);
			var gsm=barcode_data_all[23];
			var width=barcode_data_all[24];
			var roll_id=barcode_data_all[25];
			var roll_no=barcode_data_all[26];
			var po_breakdown_id=barcode_data_all[27];
			var qnty=barcode_data_all[28];
			var barcode_no=barcode_data_all[29];
			var job_no=barcode_data_all[30];
			var buyer_id=barcode_data_all[31];
			var color_id=barcode_data_all[34];
			var buyer_name=barcode_data_all[32];
			var po_number=barcode_data_all[33];
			var store_name=barcode_data_all[35];
			
			var bordy_part_id=barcode_data_all[36];
			var brand_id=barcode_data_all[37];
			var machine_no_id=barcode_data_all[38];
			var entry_form=barcode_data_all[39];
			var book_without_order=barcode_data_all[40];
			var rollMstId=barcode_data_all[41];
			var bookingNo_fab=barcode_data_all[42];
			var amount=barcode_data_all[43];
			var from_book_without_order=barcode_data_all[44];
			var qtyInPcs=barcode_data_all[45];
			var internal_ref=barcode_data_all[46];

			//alert('reject_qty :'+reject_qty);

			if(barcode_data_all[0]==0)
			{
				alert('Barcode is Not Valid');
				$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
				 {
					$('#messagebox_main', window.parent.document).html('Barcode is Not Valid.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
				 });
				 $('#txt_bar_code_num').val('');
				return; 
			}
			else if(barcode_data_all[0]==-1)
			{
				alert('Sorry! Barcode Already Scanned. Id : '+barcode_data_all[1]);
				$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
				 {
					$('#messagebox_main', window.parent.document).html('Sorry! Barcode Already Scanned.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
				 });
				 $('#txt_bar_code_num').val('');
				return; 
			}

			if(company_id != cbo_company_id)
			{
				alert('Multiple company not allowed');
				return;
			}
			// alert(febric_description_id +'!='+ febDescripId);
			if (febDescripId!="") 
			{
				// alert(orderId +'!='+ po_breakdown_id+'=======');
				if( (febric_description_id != febDescripId) || (orderId != po_breakdown_id) )
				{
					alert('Multiple Fabrication not allowed');
					return;
				}
			}

			// alert(scanned_barcode);
			//var colse_barcode_check= $('#barcode_check_id').val()*1;
			// var colse_barcode_check= 1;
			// alert(barcode_check);
			if (barcode_check==3) // 3 onclose
			{
				scanned_barcode=new Array();
			}

			if( jQuery.inArray( bar_code, scanned_barcode )>-1) 
			{ 
				alert('Sorry! Barcode Already Scanned.'); 
				$('#txt_bar_code_num').val('');
				return; 
			}
			//alert(barcode_data);return;
			
			
			var bar_code_no=$('#barcodeNo_'+row_num).val();
			if(bar_code_no!="")
			{
				row_num++;
				$("#scanning_tbl tbody tr:first").clone().find("td,input,select").each(function()
				{
					$(this).attr({ 
					  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
					  'value': function(_, value) { return value }              
					});
				}).end().prependTo("#scanning_tbl");
				$("#scanning_tbl tbody tr:first").removeAttr('id').attr('id','tr_'+row_num);//decrease_1
				$("#scanning_tbl tbody tr:first").find('input','select').val("");
				$("#decrease_"+row_num).val("-");
			}
			
			scanned_barcode.push(bar_code);
			//batch_batcode_arr.push(bar_code);
			$("#sl_"+row_num).text(row_num);
			$("#fromStore_"+row_num).text(store_name);
			$("#barcode_"+row_num).text(barcode_no);
			$("#roll_"+row_num).text(roll_no);
			$("#bodyPart_"+row_num).text(body_part_name);
			$("#cons_"+row_num).text(compsition_description);
			$("#batchNo_"+row_num).text(batch_no);
			$("#rejectQty_"+row_num).text(reject_qty);
			$("#gsm_"+row_num).text(gsm);
			$("#dia_"+row_num).text(width);
			$("#color_"+row_num).text(color);
			//$("#diaType_"+row_num).text('');
			$("#rollWeight_"+row_num).text(qnty);
			$("#qtyInPcs_"+row_num).text(qtyInPcs);

			$("#buyer_"+row_num).text(buyer_name);
			$("#booking_"+row_num).text(bookingNo_fab);
			if(from_book_without_order==1)
			{
				$("#job_"+row_num).text("");
				$("#order_"+row_num).text("");
				$("#intRef_"+row_num).text("");
			}
			else
			{
				$("#job_"+row_num).text(job_no);
				$("#order_"+row_num).text(po_number);
				$("#intRef_"+row_num).text(internal_ref);
			}
			$("#knitCompany_"+row_num).text(knitting_company_name);
			$("#basis_"+row_num).text(receive_basis);
			$("#progBookPiNo_"+row_num).text(booking_no);
			$("#barcodeNo_"+row_num).val(barcode_no);
			$("#recvBasis_"+row_num).val(receive_basis_id);
			$("#progBookPiId_"+row_num).val(booking_id);
			$("#productId_"+row_num).val(prod_id);
			$("#orderId_"+row_num).val(po_breakdown_id);
			$("#rollId_"+row_num).val(roll_id);
			$("#rollWgt_"+row_num).val(qnty);
			$("#hiddenQtyInPcs_"+row_num).val(qtyInPcs);
			$("#batchId_"+row_num).val(batch_id);
			$("#rejectQtyVal_"+row_num).val(reject_qty);
			$("#colorId_"+row_num).val(color_id);
			$("#brandId_"+row_num).val(brand_id);
			$("#rack_"+row_num).val(rack);
			$("#shelf_"+row_num).val(self);
			$("#fromStoreId_"+row_num).val(store_id);
			$("#bodyPartId_"+row_num).val(bordy_part_id);
			if (cbo_transfer_criteria==2 && from_book_without_order==0) 
			{
				$("#txtToOrder_"+row_num).val(po_number);
			}
			else
			{
				$("#txtToOrder_"+row_num).val(up_to_po_no);
			}
			$("#toOrderId_"+row_num).val();
			$("#tobookingNo_"+row_num).val();
			$("#toBookingMstId_"+row_num).val();
			$("#febDescripId_"+row_num).val(febric_description_id);
			$("#machineNoId_"+row_num).val(machine_no_id);
			$("#prodGsm_"+row_num).val(gsm);
			$("#diaWidth_"+row_num).val(width);
			$("#knitDetailsId_"+row_num).val(dtls_id);
			$("#transferEntryForm_"+row_num).val(entry_form);
			if(cbo_transfer_criteria == 2)
			{
				$("#bookWithoutOrder_"+row_num).val(from_book_without_order);
			}
			else
			{
				$("#bookWithoutOrder_"+row_num).val("0");
			}
			$("#rollMstId_"+row_num).val(rollMstId);
			$("#rollAmount_"+row_num).val(amount);
			$("#cons_"+row_num).prop("title","prod id = "+prod_id);
			$("#fromBookingWithoutOrder_"+row_num).val(from_book_without_order);
			$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
			$('#txtToOrder_'+row_num).removeAttr("onDblClick").attr("onDblClick","opneToOrder("+row_num+");");
			$("#cboToBodyPart_"+row_num).removeAttr('onchange').attr("onchange","copyBodyPart(this.id,this.value);");  
			$('#cboToBodyPart_'+row_num).html('<option value="'+0+'">--Select--</option>');
			$('#txt_tot_row').val(row_num);
			$('#txt_bar_code_num').val('');
			$('#txt_bar_code_num').focus();
			if(cbo_transfer_criteria == 2)
			{
				$('#cboToBodyPart_'+row_num).append('<option value="'+bordy_part_id+'" selected>'+body_part_name+'</option>');
			}
		}
		else // update event
		{
			var barcode_data=return_global_ajax_value( bar_code+'**'+system_id, 'populate_barcode_data_update', '', 'requires/roll_wise_finish_fabric_requisition_for_transfer_controller');
			//alert(barcode_data);return;
			var all_to_order_str = ""; var all_sample_non_order_booking="";
			var barcode_data_all=new Array(); var barcode_data_ref=new Array();
			barcode_data_ref=barcode_data.split("__");
			for(var k=0;k<barcode_data_ref.length;k++)
			{
				barcode_data_all=barcode_data_ref[k].split("**");
				var rcv_id=barcode_data_all[0];
				var company_id=barcode_data_all[1];
				var body_part_name=barcode_data_all[2];
				var receive_basis=barcode_data_all[3];
				var receive_basis_id=barcode_data_all[4];
				var receive_date=barcode_data_all[5];
				var booking_no=barcode_data_all[6];
				var booking_id=barcode_data_all[7];
				var color=barcode_data_all[8];
				var knitting_source_id=barcode_data_all[9];
				var knitting_source=barcode_data_all[10];
				var store_id=barcode_data_all[11];
				var knitting_company_id=barcode_data_all[12];
				//var yarn_lot=barcode_data_all[13];
				var batch_id=barcode_data_all[13];
				//var yarn_count=barcode_data_all[14];
				var batch_no=barcode_data_all[14];
				var reject_qty=barcode_data_all[15];
				var rack=barcode_data_all[16];
				var self=barcode_data_all[17];
				var knitting_company_name=barcode_data_all[18];
				var dtls_id=barcode_data_all[19];
				var prod_id=barcode_data_all[20];
				var febric_description_id=barcode_data_all[21];
				var compsition_description=barcode_data_all[22];
				var gsm=barcode_data_all[23];
				var width=barcode_data_all[24];
				var roll_id=barcode_data_all[25];
				var roll_no=barcode_data_all[26];
				var po_breakdown_id=barcode_data_all[27];
				var qnty=barcode_data_all[28];
				var barcode_no=barcode_data_all[29];
				var job_no=barcode_data_all[30];
				var buyer_id=barcode_data_all[31];
				var color_id=barcode_data_all[34];
				var buyer_name=barcode_data_all[32];
				var po_number=barcode_data_all[33];
				var store_name=barcode_data_all[35];
				var bordy_part_id=barcode_data_all[36];
				var brand_id=barcode_data_all[37];
				var machine_no_id=barcode_data_all[38];
				var entry_form=barcode_data_all[39];
				var book_without_order=barcode_data_all[40];
				var up_roll_id=barcode_data_all[41];
				var up_dtls_id=barcode_data_all[42];
				var up_trans_id=barcode_data_all[43];
				var up_to_trans_id=barcode_data_all[44];
				var up_to_po_no=barcode_data_all[45];
				var up_to_po_id=barcode_data_all[46];
				var barcode_for_issue=barcode_data_all[47];
				var rollMstId=barcode_data_all[48];
				var bookingNo_fab=barcode_data_all[49];
				var rollAmount=barcode_data_all[50];
				var fromProductUp=barcode_data_all[51]; 
				var from_book_without_order=barcode_data_all[52];
				var splited_barcode=barcode_data_all[53];
				var qtyInPcs=barcode_data_all[54];
				var internal_ref=barcode_data_all[55];
				var to_job=barcode_data_all[56];
				var update_formBodyPart=barcode_data_all[57];
				var to_body_part_id=barcode_data_all[58];
				var to_booking_no=barcode_data_all[59];
				var to_booking_id=barcode_data_all[60];

				if(company_id != cbo_company_id)
				{
					alert('Multiple company not allowed');
					return;
				}

				var bar_code_no=$('#barcodeNo_'+row_num).val();
				if(bar_code_no!="")
				{
					row_num++;
					$("#scanning_tbl tbody tr:first").clone().find("td,input,select").each(function()
					{
						$(this).attr({ 
						  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
						  'value': function(_, value) { return value }              
						});
					}).end().prependTo("#scanning_tbl");
					$("#scanning_tbl tbody tr:first").removeAttr('id').attr('id','tr_'+row_num);
				}
				
				//alert(book_without_order+"=="+job_no+"=="+po_number+"=="+po_breakdown_id);
				$("#sl_"+row_num).text(row_num);
				$("#fromStore_"+row_num).text(store_name);
				$("#barcode_"+row_num).text(barcode_no);
				$("#roll_"+row_num).text(roll_no);
				$("#bodyPart_"+row_num).text(body_part_name);
				$("#cons_"+row_num).text(compsition_description);
				$("#batchNo_"+row_num).text(batch_no);
				$("#rejectQty_"+row_num).text(reject_qty);
				$("#gsm_"+row_num).text(gsm);
				$("#dia_"+row_num).text(width);
				$("#color_"+row_num).text(color);
				//$("#diaType_"+row_num).text('');
				$("#rollWeight_"+row_num).text(qnty);
				$("#qtyInPcs_"+row_num).text(qtyInPcs);
				$("#buyer_"+row_num).text(buyer_name);
				$("#booking_"+row_num).text(bookingNo_fab);

				if(from_book_without_order==1)
				{
					$("#job_"+row_num).text("");
					$("#order_"+row_num).text("");
					$("#intRef_"+row_num).text("");
				}
				else
				{
					$("#job_"+row_num).text(job_no);
					$("#order_"+row_num).text(po_number);
					$("#intRef_"+row_num).text(internal_ref);
				}
				$("#knitCompany_"+row_num).text(knitting_company_name);
				$("#basis_"+row_num).text(receive_basis);
				$("#progBookPiNo_"+row_num).text(booking_no);
				$("#barcodeNo_"+row_num).val(barcode_no);
				$("#recvBasis_"+row_num).val(receive_basis_id);
				$("#progBookPiId_"+row_num).val(booking_id);
				$("#productId_"+row_num).val(prod_id);
				$("#orderId_"+row_num).val(po_breakdown_id);
				$("#rollId_"+row_num).val(roll_id);
				$("#rollWgt_"+row_num).val(qnty);
				$("#hiddenQtyInPcs_"+row_num).val(qtyInPcs);
				$("#batchId_"+row_num).val(batch_id);
				$("#rejectQtyVal_"+row_num).val(reject_qty);
				$("#colorId_"+row_num).val(color_id);
				$("#bodyPartId_ "+row_num).val(update_formBodyPart);
				$("#hideBodyPartForUpdate_"+row_num).val(to_body_part_id);	
				$("#brandId_"+row_num).val(brand_id);
				$("#rack_"+row_num).val(rack);
				$("#shelf_"+row_num).val(self);
				$("#fromStoreId_"+row_num).val(store_id);

				if(cbo_transfer_criteria == 1 || cbo_transfer_criteria == 4)
				{
					$("#txtToOrder_"+row_num).val(up_to_po_no);
				}
				$("#toOrderId_"+row_num).val(up_to_po_id);
				$("#toJob_"+row_num).text(to_job);
				$("#tobookingNo_"+row_num).val(to_booking_no);
				$("#toBookingMstId_"+row_num).val(to_booking_id);
				$("#febDescripId_"+row_num).val(febric_description_id);
				$("#machineNoId_"+row_num).val(machine_no_id);
				$("#prodGsm_"+row_num).val(gsm);
				$("#diaWidth_"+row_num).val(width);
				$("#knitDetailsId_"+row_num).val(dtls_id);
				$("#transferEntryForm_"+row_num).val(entry_form);
				//$("#bookWithoutOrder_"+row_num).val(book_without_order);


				if(cbo_transfer_criteria == 2)
				{
					$("#bookWithoutOrder_"+row_num).val(from_book_without_order);
				}
				else
				{
					$("#bookWithoutOrder_"+row_num).val("0");
				}

				$("#fromBookingWithoutOrder_"+row_num).val(from_book_without_order);
				$("#dtlsId_"+row_num).val(up_dtls_id);
				$("#transId_"+row_num).val(up_trans_id);
				$("#transIdTo_"+row_num).val(up_to_trans_id);
				$("#rolltableId_"+row_num).val(up_roll_id);
				$("#barcodeIssue_"+row_num).val(barcode_for_issue);
				$("#rollMstId_"+row_num).val(rollMstId);
				$("#rollAmount_"+row_num).val(rollAmount);
				$("#fromProductUp_"+row_num).val(fromProductUp);
				$("#cons_"+row_num).prop("title","prod id = "+prod_id+", from prod id = "+fromProductUp);

				if (barcode_no==barcode_for_issue ) 
				{
					$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_message("+row_num+");");
					$('#txtToOrder_'+row_num).removeAttr("onDblClick").attr("onDblClick","opneToOrder("+row_num+");");
					$("#cboToBodyPart_"+row_num).removeAttr('onchange').attr("onchange","copyBodyPart(this.id,this.value);"); 

				}
				else if(barcode_no==splited_barcode)
				{
					$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_message_split("+row_num+");");
					$('#txtToOrder_'+row_num).removeAttr("onDblClick").attr("onDblClick","opneToOrder("+row_num+");");
					$("#cboToBodyPart_"+row_num).removeAttr('onchange').attr("onchange","copyBodyPart(this.id,this.value);"); 
				}
				else
				{
					$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
					$('#txtToOrder_'+row_num).removeAttr("onDblClick").attr("onDblClick","opneToOrder("+row_num+");");
					$("#cboToBodyPart_"+row_num).removeAttr('onchange').attr("onchange","copyBodyPart(this.id,this.value);"); 
				}

				$('#txt_tot_row').val(row_num);
				$('#txt_bar_code_num').val('');
				$('#txt_bar_code_num').focus();
				if(cbo_transfer_criteria == 2)
				{
					$('#cboToBodyPart_'+row_num).html('<option value="'+0+'">Select</option>');
					$('#cboToBodyPart_'+row_num).append('<option value="'+bordy_part_id+'" selected>'+body_part_name+'</option>');
				}
				else
				{
					
					if($("#bookWithoutOrder_"+row_num).val() == 0){
						all_to_order_str +=up_to_po_id+',';
					}

					if($("#bookWithoutOrder_"+row_num).val() == 1){
						all_sample_non_order_booking +=up_to_po_id+',';
					}
				}
			}
			if(all_to_order_str !=""){
				load_body_part_order_wise(all_to_order_str);
			}
			
			if(all_sample_non_order_booking !="")
			{
				load_body_part_sample_wise(all_sample_non_order_booking);
			}
		}
		calculate_total();
	}
	function load_body_part_order_wise(order_ids)
	{
		var bodypart_result = return_global_ajax_value(order_ids, 'bodypart_list_order_wise', '', 'requires/roll_wise_finish_fabric_requisition_for_transfer_controller');
		var JSONObject = JSON.parse(bodypart_result);

		var cbo_transfer_criteria=$('#cbo_transfer_criteria').val();

		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var fromBookingWithoutOrder = $(this).find('input[name="fromBookingWithoutOrder[]"]').val();
			

			var toOrderIdArr = $(this).find('input[name="toOrderId[]"]').attr("id");
			var toOrderIdArr = toOrderIdArr.split("_");

			if(cbo_transfer_criteria == 2)
			{
				var toOrder_id = $(this).find('input[name="orderId[]"]').val();
			}else{
				var toOrder_id = $(this).find('input[name="toOrderId[]"]').val();
			}
			
			//alert(cbo_transfer_criteria + '_' + fromBookingWithoutOrder);
			if( (cbo_transfer_criteria == 2  && fromBookingWithoutOrder ==0) || cbo_transfer_criteria != 2)
			{	
				//alert('he');
				$(this).find('select[name="cboToBodyPart[]"]').html('<option value="'+0+'">Select</option>');
		 		for (var key of Object.keys(JSONObject[toOrder_id]).sort())
				{
	 				$(this).find('select[name="cboToBodyPart[]"]').append('<option value="'+key+'">'+JSONObject[toOrder_id][key]+'</option>');
				};
			}

			var to_body_part_id = $("#hideBodyPartForUpdate_"+toOrderIdArr[1]*1).val();
			$("#cboToBodyPart_"+toOrderIdArr[1]*1).val(to_body_part_id);

		});
	}

	function load_body_part_sample_wise(order_ids)
	{
		var bodypart_result = return_global_ajax_value(order_ids, 'bodypart_list_sample_wise', '', 'requires/roll_wise_finish_fabric_requisition_for_transfer_controller');
		var JSONObject = JSON.parse(bodypart_result);

		var cbo_transfer_criteria=$('#cbo_transfer_criteria').val();

		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var fromBookingWithoutOrder = $(this).find('input[name="fromBookingWithoutOrder[]"]').val();
			var barcodeNo = $(this).find('input[name="barcodeNo[]"]').val();

			var toOrderIdArr = $(this).find('input[name="toOrderId[]"]').attr("id");
			var toOrderIdArr = toOrderIdArr.split("_");

			if(cbo_transfer_criteria == 2)
			{
				var toOrder_id = $(this).find('input[name="orderId[]"]').val();
			}else{
				var toOrder_id = $(this).find('input[name="toOrderId[]"]').val();
			}

			//alert(cbo_transfer_criteria + '_' + fromBookingWithoutOrder);
			if( (cbo_transfer_criteria == 2  && fromBookingWithoutOrder ==1) || cbo_transfer_criteria != 2)
			{	
				//alert(barcodeNo + '_' + toOrder_id);
				$(this).find('select[name="cboToBodyPart[]"]').html('<option value="'+0+'">Select</option>');
		 		for (var key of Object.keys(JSONObject[toOrder_id]).sort())
				{
	 				$(this).find('select[name="cboToBodyPart[]"]').append('<option value="'+key+'">'+JSONObject[toOrder_id][key]+'</option>');
				};
			}

			var to_body_part_id = $("#hideBodyPartForUpdate_"+toOrderIdArr[1]*1).val();
			$("#cboToBodyPart_"+toOrderIdArr[1]*1).val(to_body_part_id);
		});
	}
	
	$('#txt_bar_code_num').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			e.preventDefault();
			var bar_code=$('#txt_bar_code_num').val();
			create_row(0,bar_code,4);
		}
	});
	
	function add_dtls_data( data )
	{
		var barcode_datas=data.split(",");
		for(var k=0; k<barcode_datas.length; k++)
		{
			var datas=barcode_datas[k].split("__");
			var barcode_no=datas[0];
			var dtls_id=datas[1];
			var trans_id=datas[2];
			var to_trans_id=datas[3];
			var roll_table_id=datas[4];
			
			barcode_dtlsId_array[barcode_no] = dtls_id;
			barcode_trnasId_array[barcode_no] = trans_id;
			barcode_trnasId_to_array[barcode_no] = to_trans_id;
			barcode_rollTableId_array[barcode_no] = roll_table_id;
		}
		
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
			var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
			
			if(dtlsId=="") 
			{
				$(this).find('input[name="dtlsId[]"]').val(barcode_dtlsId_array[barcodeNo]);
				$(this).find('input[name="transId[]"]').val(barcode_trnasId_array[barcodeNo]);
				$(this).find('input[name="transIdTo[]"]').val(barcode_trnasId_to_array[barcodeNo]);	
				$(this).find('input[name="rolltableId[]"]').val(barcode_rollTableId_array[barcodeNo]);	
			}
		});
	}

	function fn_message(rowId)
	{
		alert("Barcode already used for Issue");
		return;
	}

	function fn_message_split(rowId)
	{
		alert("Split Reference Found.");
		return;
	}

	function fn_deleteRow( rid )
	{
		var bar_code =$("#barcodeNo_"+rid).val();
		/*if( jQuery.inArray( bar_code, batch_batcode_arr )>-1) 
		{ 
			alert('Sorry! Barcode Already Scanned.'); 
			$('#txt_bar_code_num').val('');
			return; 
		}*/
		var num_row =$('#scanning_tbl tbody tr').length;
		var rolltableId =$("#rolltableId_"+rid).val();
		var txt_deleted_id=$('#txt_deleted_id').val();
		var txt_deleted_barcode=$('#txt_deleted_barcode').val();
		var txt_deleted_trnsf_dtls_id=$('#txt_deleted_trnsf_dtls_id').val();
		var trnsf_dtlsId =$("#dtlsId_"+rid).val();

		var txt_deleted_trans_id=$('#txt_deleted_trans_id').val();
		var transaction_dtlsId =$("#transIdTo_"+rid).val();

		var txt_deleted_prod_id=$('#txt_deleted_prod_id').val();
		var production_dtlsId =$("#productId_"+rid).val();

		var txt_deleted_prod_qty=$('#txt_deleted_prod_qty').val();
		var deleted_production_qty =$("#rollWgt_"+rid).val();

		var deleted_production_amount =$("#rollAmount_"+rid).val();
		var txt_up_prod_id =$("#fromProductUp_"+rid).val();
		var txt_roll_source_id =$("#rollMstId_"+rid).val();
		var txt_deleted_source_roll_id = $('#txt_deleted_source_roll_id').val();
		
		if(num_row==1)
		{
			$('#tr_'+rid+' td:not(:last-child)').each(function(index, element) {
				$(this).html('');
			});
			
			$('#tr_'+rid).find(":input:not(:button)").val('');
		}
		else
		{
			$("#tr_"+rid).remove();
		}
		
		var selected_id='';var selected_barcode='';var selected_trnsf_dtls_id='';var selected_trans_dtls_id='';var selected_prod_dtls_id='';var selected_prod_qty=''; var deleted_source_roll_id = "";
		if(rolltableId!='')
		{
			if(txt_deleted_id=='') selected_id=rolltableId; else selected_id=txt_deleted_id+','+rolltableId;
			$('#txt_deleted_id').val( selected_id );

			if(txt_deleted_barcode=='') selected_barcode=bar_code; else selected_barcode=txt_deleted_barcode+','+bar_code;
			$('#txt_deleted_barcode').val( selected_barcode );

			if(txt_deleted_trnsf_dtls_id=='') selected_trnsf_dtls_id=trnsf_dtlsId; else selected_trnsf_dtls_id=txt_deleted_trnsf_dtls_id+','+trnsf_dtlsId;
			$('#txt_deleted_trnsf_dtls_id').val( selected_trnsf_dtls_id );

			if(txt_deleted_trans_id=='') selected_trans_dtls_id=transaction_dtlsId; else selected_trans_dtls_id=txt_deleted_trans_id+','+transaction_dtlsId;
			$('#txt_deleted_trans_id').val( selected_trans_dtls_id );

			if(txt_deleted_prod_id=='') selected_prod_dtls_id=production_dtlsId; else selected_prod_dtls_id=txt_deleted_prod_id+','+production_dtlsId;
			$('#txt_deleted_prod_id').val( selected_prod_dtls_id );


			if(txt_deleted_prod_qty=='') selected_prod_qty=production_dtlsId+'='+deleted_production_qty+'='+deleted_production_amount+'='+txt_up_prod_id; else selected_prod_qty=txt_deleted_prod_qty+','+production_dtlsId+'='+deleted_production_qty+'='+deleted_production_amount+'='+txt_up_prod_id;
			$('#txt_deleted_prod_qty').val( selected_prod_qty );

			if(txt_deleted_source_roll_id=='') deleted_source_roll_id=txt_roll_source_id; else deleted_source_roll_id=txt_deleted_source_roll_id+','+txt_roll_source_id;
			$('#txt_deleted_source_roll_id').val( deleted_source_roll_id );

			//if(txt_deleted_prod_qty=='') selected_prod_qty=deleted_production_qty; else selected_prod_qty=txt_deleted_prod_qty+','+deleted_production_qty;
			//$('#txt_deleted_prod_qty').val( selected_prod_qty );
		}
		
		var index = scanned_barcode.indexOf(bar_code);
		scanned_barcode.splice(index,1);
		calculate_total();
	}
	
	function opneToOrder(str)
	{
		var cbo_to_company_id = $('#cbo_to_company_id').val();
		if (form_validation('cbo_to_company_id','Company')==false)
		{
			return;
		}
		var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();
		if(cbo_transfer_criteria==2)
		{
			alert("Store to Store basis Order Selection not allowed.");
			return;
		}
		else
		{
			var j=0; var item_desc_ids='';var item_gsm=""; var item_dia="";var color_id="";
			$("#scanning_tbl").find('tbody tr').each(function()
			{
				var febDescripId=$(this).find('input[name="febDescripId[]"]').val();
				var gsm=$(this).find('input[name="prodGsm[]"]').val();
				var diaWidth=$(this).find('input[name="diaWidth[]"]').val();
				var colorID=$(this).find('input[name="colorId[]"]').val();
				//alert(febDescripId +'='+gsm +'=' +diaWidth+'=' +colorID);
				j++;
				item_desc_ids+=febDescripId+',';
				item_gsm+=gsm+',';
				item_dia+=diaWidth+',';
				color_id+=colorID+',';
			});
			item_desc_ids = item_desc_ids.replace(/,\s*$/, ""); // remove last comma
			item_gsm = item_gsm.replace(/,\s*$/, ""); // remove last comma
			item_dia = item_dia.replace(/,\s*$/, ""); // remove last comma
			color_id = color_id.replace(/,\s*$/, ""); // remove last comma
			var item_desc_ids = item_desc_ids.split(",");
			var item_gsm = item_gsm.split(",");
			var item_dia = item_dia.split(",");
			var color_id = color_id.split(",");
			// alert(item_gsm);
			function onlyUnique(value, index, self) {
				return self.indexOf(value) === index;
			}
			var item_desc_ids = item_desc_ids.filter(onlyUnique);
			var item_gsm = item_gsm.filter(onlyUnique);
			var item_dia = item_dia.filter(onlyUnique);
			var color_id = color_id.filter(onlyUnique);
			// alert(uniqueitem_gsm);
			var page_link='requires/roll_wise_finish_fabric_requisition_for_transfer_controller.php?cbo_to_company_id='+cbo_to_company_id+'&action=to_order_popup'+'&item_desc_ids='+item_desc_ids+'&item_gsm='+item_gsm+'&item_dia='+item_dia+'&color_id='+color_id;
			var title='Batch Number Popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1020px,height=390px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var order_ref=this.contentDoc.getElementById("order_id").value.split("_"); 
				var order_id=order_ref[0];
				var order_no=order_ref[1];
				var job_no=order_ref[2];
				var booking_no=order_ref[3];
				var booking_id=order_ref[4];
				
				if(order_no!="")
				{
					$('#toOrderId_'+str).val(order_id);
					$('#txtToOrder_'+str).val(order_no);
					$('#toJob_'+str).text(job_no);
					$('#tobookingNo_'+str).val(booking_no);
					$('#toBookingMstId_'+str).val(booking_id);
					var first_tr=str-1;
					for(var k=first_tr; k>=1; k--)
					{
						$("#toOrderId_"+k).val(order_id);
						$("#txtToOrder_"+k).val(order_no);
						$("#toJob_"+k).text(job_no);
						$("#tobookingNo_"+k).val(booking_no);
						$("#toBookingMstId_"+k).val(booking_id);
					}
				}
				if(order_id!="")
				{
					var bodypart_result = return_global_ajax_value(order_id, 'bodypart_list', '', 'requires/roll_wise_finish_fabric_requisition_for_transfer_controller');
					var JSONObject = JSON.parse(bodypart_result);

					$("#scanning_tbl").find('tbody tr').each(function()
					{
						var bodyPartId = $(this).find('select[name="cboToBodyPart[]"]').attr("id");
						var bodyPartIdSlArr = bodyPartId.split("_");
						// copy only that and below selected data
						if( str >= bodyPartIdSlArr[1]*1 )
						{
							$(this).find('select[name="cboToBodyPart[]"]').html('<option value="'+0+'">Select</option>');
							for (var key of Object.keys(JSONObject).sort())
							{
								$(this).find('select[name="cboToBodyPart[]"]').append('<option value="'+key+'">'+JSONObject[key]+'</option>');
							};
						}
					});
				}
			}
		}
	}
	function copyBodyPart(id,value)
	{
		var idArr = id.split("_");
		var sl = idArr[1]*1;
		if($('#bodyPartIds').is(':checked'))
		{
			$("#scanning_tbl").find('tbody tr').each(function()
			{
				var bodyPartId = $(this).find('select[name="cboToBodyPart[]"]').attr("id");
				var bodyPartIdSlArr = bodyPartId.split("_");
				// copy only that and below selected data
				if( sl >= bodyPartIdSlArr[1]*1 )
				{
					$("#cboToBodyPart_"+bodyPartIdSlArr[1]*1).val(value);
				}
			});
		}		
	}
	function fnc_reset_form(source)
	{
		$('#scanning_tbl tbody tr').remove();
						
		var html='<tr id="tr_1" align="center" valign="middle"><td width="20" id="sl_1"></td><td width="100" id="fromStore_1"></td><td width="70" id="barcode_1"></td><td width="45" id="roll_1"></td><td style="word-break:break-all;" width="80" id="bodyPart_1"></td><td style="word-break:break-all;" width="150" id="cons_1" title="" ></td><td style="word-break:break-all;" width="100" id="batchNo_1" align="center" title=""></td><td style="word-break:break-all;" width="40" id="gsm_1"></td><td style="word-break:break-all;" width="40" id="dia_1"></td><td style="word-break:break-all;" width="120" id="color_1"></td><td width="60" id="rollWeight_1" align="right"></td><td width="60" id="qtyInPcs_1" align="right"></td><td style="word-break:break-all;" width="100" id="rejectQty_1" align="right"></td><td style="word-break:break-all;" width="60" id="buyer_1"></td><td style="word-break:break-all;" width="80" id="booking_1"></td><td style="word-break:break-all;" width="80" id="job_1"></td><td style="word-break:break-all;" width="80" id="intRef_1"></td><td style="word-break:break-all;" width="80" id="order_1" align="left"></td><td style="word-break:break-all;" width="100" id="knitCompany_1"></td><td style="word-break:break-all;" width="70" id="basis_1"></td><td width="90" id="toOrder_1" placeholder="Browse For Order"><input type="text"  class="text_boxes" id="txtToOrder_1" name="txtToOrder[]" style="width:60px;" onDblClick="opneToOrder(1)" placeholder="Browse For Order" readonly/></td><td width="90" align="center" id="toBodyPartTd_1"><? echo create_drop_down( "cboToBodyPart_1", 90,$blank_array,"", 1, "--Select--", 0, "copyBodyPart(this.id,this.value)",0,"","","","","","","cboToBodyPart[]","onchange_void" ); ?></td><td style="word-break:break-all;" width="80" id="toJob_1"></td><td style="word-break:break-all;" width="115" id="progBookPiNo_1"></td><td id="button_1" align="center"><input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" /><input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/><input type="hidden" name="recvBasis[]" id="recvBasis_1"/><input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/><input type="hidden" name="productId[]" id="productId_1"/><input type="hidden" name="orderId[]" id="orderId_1"/><input type="hidden" name="rollId[]" id="rollId_1"/><input type="hidden" name="rollWgt[]" id="rollWgt_1"/><input type="hidden" name="batchId[]" id="batchId_1"/><input type="hidden" name="yarnCount[]" id="yarnCount_1"/><input type="hidden" name="colorId[]" id="colorId_1"/><input type="hidden" name="rejectQtyVal[]" id="rejectQtyVal_1"/><input type="hidden" name="brandId[]" id="brandId_1"/><input type="hidden" name="rack[]" id="rack_1"/><input type="hidden" name="shelf[]" id="shelf_1"/><input type="hidden" name="fromStoreId[]" id="fromStoreId_1"/><input type="hidden" name="toOrderId[]" id="toOrderId_1"/><input type="hidden" name="tobookingNo[]" id="tobookingNo_1"/><input type="hidden" name="toBookingMstId[]" id="toBookingMstId_1"/><input type="hidden" name="dtlsId[]" id="dtlsId_1"/><input type="hidden" name="transId[]" id="transId_1"/><input type="hidden" name="transIdTo[]" id="transIdTo_1"/><input type="hidden" name="rolltableId[]" id="rolltableId_1"/><input type="hidden" name="febDescripId[]" id="febDescripId_1"/><input type="hidden" name="machineNoId[]" id="machineNoId_1"/><input type="hidden" name="prodGsm[]" id="prodGsm_1"/><input type="hidden" name="diaWidth[]" id="diaWidth_1"/><input type="hidden" name="knitDetailsId[]" id="knitDetailsId_1"/><input type="hidden" name="transferEntryForm[]" id="transferEntryForm_1"/><input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_1"/><input type="hidden" name="barcodeIssue[]" id="barcodeIssue_1"/><input type="hidden" name="rollMstId[]" id="rollMstId_1"/><input type="hidden" name="rollAmount[]" id="rollAmount_1"/><input type="hidden" name="fromProductUp[]" id="fromProductUp_1"/><input type="hidden" name="fromBookingWithoutOrder[]" id="fromBookingWithoutOrder_1"/><input type="hidden" name="hiddenQtyInPcs[]" id="hiddenQtyInPcs_1"/><input type="hidden" name="bodyPartId[]" id="bodyPartId_1"/><input type="hidden" name="hideBodyPartForUpdate[]" id="hideBodyPartForUpdate_1"/></td></tr>'; 

		if(source!=2)
		{
			$('#txt_transfer_no').val('');
			$('#update_id').val('');
			$('#cbo_transfer_criteria').val(0);
			$('#cbo_company_id').val(0);
			$('#cbo_to_company_id').val(0);
			$('#cbo_to_company_id').attr('disabled',true);
			
			
			$('#txt_transfer_date').val('');
			$('#txt_challan_no').val('');
			$('#cbo_store_name').val(0);
		}
			$('#txt_deleted_id').val('');
			$('#txt_deleted_barcode').val('');
			$('#txt_deleted_trnsf_dtls_id').val('');
			$('#txt_deleted_trans_id').val('');
			$('#txt_deleted_prod_id').val('');
			$('#txt_deleted_prod_qty').val('');
			$('#txt_deleted_source_roll_id').val('');
		
		$('#txt_tot_row').val(1);


		$("#scanning_tbl tbody").html(html);	
	}
	
	function active_inactive(str)
	{
		$('#cbo_to_company_id').val(0);
		load_drop_down( 'requires/roll_wise_finish_fabric_requisition_for_transfer_controller',$('#cbo_transfer_criteria').val()+'_'+str, 'load_drop_store_balnk', 'store_td' );
		if(str==1)
		{
			$('#cbo_company_id').removeAttr('disabled','disabled');	
			$('#cbo_to_company_id').val(0);
		}
		else
		{
			$('#cbo_company_id').attr('disabled','disabled');
			$('#cbo_to_company_id').val(0);
			$('#cbo_company_id').val(0);
		}

		var html='<tr id="tr_1" align="center" valign="middle"><td width="20" id="sl_1"></td><td width="100" id="fromStore_1"></td><td width="70" id="barcode_1"></td><td width="45" id="roll_1"></td><td style="word-break:break-all;" width="80" id="bodyPart_1"></td><td style="word-break:break-all;" width="150" id="cons_1" title="" ></td><td style="word-break:break-all;" width="100" id="batchNo_1" align="center" title=""></td><td style="word-break:break-all;" width="40" id="gsm_1"></td><td style="word-break:break-all;" width="40" id="dia_1"></td><td style="word-break:break-all;" width="120" id="color_1"></td><td width="60" id="rollWeight_1" align="right"></td><td width="60" id="qtyInPcs_1" align="right"></td><td style="word-break:break-all;" width="100" id="rejectQty_1" align="right"></td><td style="word-break:break-all;" width="60" id="buyer_1"></td><td style="word-break:break-all;" width="80" id="booking_1"></td><td style="word-break:break-all;" width="80" id="job_1"></td><td style="word-break:break-all;" width="80" id="intRef_1"></td><td style="word-break:break-all;" width="80" id="order_1" align="left"></td><td style="word-break:break-all;" width="100" id="knitCompany_1"></td><td style="word-break:break-all;" width="70" id="basis_1"></td><td width="90" id="toOrder_1" placeholder="Browse For Order"><input type="text"  class="text_boxes" id="txtToOrder_1" name="txtToOrder[]" style="width:60px;" onDblClick="opneToOrder(1)" placeholder="Browse For Order" readonly/></td><td width="90" align="center" id="toBodyPartTd_1"><? echo create_drop_down( "cboToBodyPart_1", 90,$blank_array,"", 1, "--Select--", 0, "copyBodyPart(this.id,this.value)",0,"","","","","","","cboToBodyPart[]","onchange_void" ); ?></td><td style="word-break:break-all;" width="80" id="toJob_1"></td><td style="word-break:break-all;" width="115" id="progBookPiNo_1"></td><td id="button_1" align="center"><input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" /><input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/><input type="hidden" name="recvBasis[]" id="recvBasis_1"/><input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/><input type="hidden" name="productId[]" id="productId_1"/><input type="hidden" name="orderId[]" id="orderId_1"/><input type="hidden" name="rollId[]" id="rollId_1"/><input type="hidden" name="rollWgt[]" id="rollWgt_1"/><input type="hidden" name="batchId[]" id="batchId_1"/><input type="hidden" name="yarnCount[]" id="yarnCount_1"/><input type="hidden" name="colorId[]" id="colorId_1"/><input type="hidden" name="rejectQtyVal[]" id="rejectQtyVal_1"/><input type="hidden" name="brandId[]" id="brandId_1"/><input type="hidden" name="rack[]" id="rack_1"/><input type="hidden" name="shelf[]" id="shelf_1"/><input type="hidden" name="fromStoreId[]" id="fromStoreId_1"/><input type="hidden" name="toOrderId[]" id="toOrderId_1"/><input type="hidden" name="tobookingNo[]" id="tobookingNo_1"/><input type="hidden" name="toBookingMstId[]" id="toBookingMstId_1"/><input type="hidden" name="dtlsId[]" id="dtlsId_1"/><input type="hidden" name="transId[]" id="transId_1"/><input type="hidden" name="transIdTo[]" id="transIdTo_1"/><input type="hidden" name="rolltableId[]" id="rolltableId_1"/><input type="hidden" name="febDescripId[]" id="febDescripId_1"/><input type="hidden" name="machineNoId[]" id="machineNoId_1"/><input type="hidden" name="prodGsm[]" id="prodGsm_1"/><input type="hidden" name="diaWidth[]" id="diaWidth_1"/><input type="hidden" name="knitDetailsId[]" id="knitDetailsId_1"/><input type="hidden" name="transferEntryForm[]" id="transferEntryForm_1"/><input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_1"/><input type="hidden" name="barcodeIssue[]" id="barcodeIssue_1"/><input type="hidden" name="rollMstId[]" id="rollMstId_1"/><input type="hidden" name="rollAmount[]" id="rollAmount_1"/><input type="hidden" name="fromProductUp[]" id="fromProductUp_1"/><input type="hidden" name="fromBookingWithoutOrder[]" id="fromBookingWithoutOrder_1"/><input type="hidden" name="hiddenQtyInPcs[]" id="hiddenQtyInPcs_1"/><input type="hidden" name="bodyPartId[]" id="bodyPartId_1"/><input type="hidden" name="hideBodyPartForUpdate[]" id="hideBodyPartForUpdate_1"/></td></tr>'; 

		$("#scanning_tbl tbody").html(html);
	}
	
	function copy_all(str)
	{
		//alert(str);
		var data_value=$("#txtToOrder_"+str).val();
		var first_tr=str-1;
		
		if(data_value!="")
		{
			for(var k=first_tr; k>=1; k--)
			{
				$("#txtToOrder_"+k).val(data_value);
			}
		}
	}

	function page_reload()
	{
		window.location.reload();
	}

	function calculate_total()
	{
		var total_roll_weight='';
		var total_roll_qtyInPcs='';
		var total_roll_rejectqty='';
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var rollWgt=$(this).find('input[name="rollWgt[]"]').val();
			var hiddenQtyInPcs=$(this).find('input[name="hiddenQtyInPcs[]"]').val();
			var rollReject=$(this).find('input[name="rejectQtyVal[]"]').val();
			//alert(rollWgt);
			total_roll_weight=total_roll_weight*1+rollWgt*1;
			total_roll_qtyInPcs=total_roll_qtyInPcs*1+hiddenQtyInPcs*1;
			total_roll_rejectqty=total_roll_rejectqty*1+rollReject*1;
		});
	
		$("#total_rollwgt").text(total_roll_weight.toFixed(2));	
		$("#total_qtyInPcs").text(total_roll_qtyInPcs.toFixed(2));
		$("#total_qtyReject").text(total_roll_rejectqty.toFixed(2));
	}	

	function company_on_change(company)
	{
		if (form_validation('cbo_transfer_criteria','Transfer Criteria')==false)
		{
			return;
		}

		if($("#cbo_transfer_criteria").val() != 1)
		{
			$("#cbo_to_company_id").val(company);
			//load_drop_down( 'requires/roll_wise_finish_fabric_requisition_for_transfer_controller',$('#cbo_transfer_criteria').val()+'_'+company, 'load_drop_store', 'store_td' );
			load_drop_down( 'requires/roll_wise_finish_fabric_requisition_for_transfer_controller',$('#cbo_transfer_criteria').val()+'_'+company, 'load_drop_store_to', 'store_td' );

		}
		else
		{
			if($("#cbo_company_id").val() == $("#cbo_to_company_id").val())
			{
				$("#cbo_to_company_id").val(0);
			}
		}
	}
	function from_company_on_change(from_company)
	{
		load_drop_down( 'requires/roll_wise_finish_fabric_requisition_for_transfer_controller',$('#cbo_transfer_criteria').val()+'_'+from_company, 'load_drop_store_from', 'from_store_td' );
	}
	
	function to_company_on_change(to_company)
	{
		if(($("#cbo_company_id").val() == $("#cbo_to_company_id").val()) && $('#cbo_transfer_criteria').val() ==1)
		{
			$("#cbo_to_company_id").val(0);
			return;
		}
		if($("#cbo_transfer_criteria").val() != 1)
		{
			$("#cbo_company_id").val(to_company);
			load_drop_down( 'requires/roll_wise_finish_fabric_requisition_for_transfer_controller',$('#cbo_transfer_criteria').val()+'_'+to_company, 'load_drop_store_from', 'from_store_td' );

		}
		//load_drop_down( 'requires/roll_wise_finish_fabric_requisition_for_transfer_controller',$('#cbo_transfer_criteria').val()+'_'+to_company, 'load_drop_store', 'store_td' );
		load_drop_down( 'requires/roll_wise_finish_fabric_requisition_for_transfer_controller',$('#cbo_transfer_criteria').val()+'_'+to_company, 'load_drop_store_to', 'store_td' );

	}
	function store_on_change(str)
	{
		if($("#cbo_transfer_criteria").val() == 2)
		{
			// $("#cbo_store_name").val(str);
			// load_drop_down( 'requires/roll_wise_finish_fabric_transfer_entry_controller',company, 'load_drop_store_from', 'store_td' );
			var tns = $("#cbo_store_name_from").val();
			var tnss = $("#cbo_store_name").val();
			
			console.log(tnss);
			if($("#cbo_store_name_from").val() == $("#cbo_store_name").val())
			{
				$("#cbo_store_name").val(0);
			}
		}
		else if($("#cbo_transfer_criteria").val() == 4)
		{
			var tns = $("#cbo_store_name_from").val();
			$("#cbo_store_name").val(tns);
		}
	}

</script>
</head>
<body onLoad="set_hotkey();$('#txt_bar_code_num').focus();">
    <div align="center" style="width:100%;">
		<? echo load_freeze_divs ("../../",$permission); ?>
        <form name="rollscanning_1" id="rollscanning_1"  autocomplete="off"  >
            <fieldset style="width:810px;">
				<legend>Roll Scanning</legend>
                <table cellpadding="0" cellspacing="2" width="800">
                    <tr>
                        <td colspan="2"></td>
                        <td align="right"><b>Transfer System No</b></td>
                        <td><input type="text" name="txt_transfer_no" id="txt_transfer_no" class="text_boxes" style="width:148px;"  onDblClick="openmypage_issue()" placeholder="Double Click To Search" readonly/><input type="hidden" name="update_id" id="update_id"/></td>
                    </tr>
                    <tr>
                        <td align="right" class="must_entry_caption">Requisition Criteria</td>
                        <td>
                            <?
                                echo create_drop_down("cbo_transfer_criteria", 160,$item_transfer_criteria,"", 1,"-- Select --",'0',"active_inactive(this.value);",'','1,2,4');
                            ?>
                        </td>
                        <td class="must_entry_caption" align="right">Requisition Company</td>
                        <td>
                            <? 
								echo create_drop_down( "cbo_to_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "to_company_on_change(this.value)",0 );
							?>
                        </td>
                        <td align="right" class="must_entry_caption">Company</td>
                        <td>
                            <? 
								echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "company_on_change(this.value);from_company_on_change(this.value);",1 );
							?>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" class="must_entry_caption">Requisition Date</td>
                        <td>
                            <input type="text" name="txt_transfer_date" id="txt_transfer_date" class="datepicker" style="width:148px;" readonly placeholder="Select Date" />
                        </td>
                        <td align="right">Challan No.</td>
                        <td>
                            <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:148px;" maxlength="20" title="Maximum 20 Character" />
                        </td>
                        <td align="right" class="must_entry_caption">From Store</td>
						<td id="from_store_td">
							<?
								echo create_drop_down( "cbo_store_name_from", 160, $blank_array,"", 1, "--Select store--", 0, "store_on_change(this.value);" );
							?>	
						</td>
                       
                       
                    </tr>
                    <!--<tr>
                    	<td height="5" colspan="6"></td>
                    </tr>-->
                    <tr>
                    	<td align="right" class="must_entry_caption">To Store</td>
                        <td id="store_td">
                            <?
                                echo create_drop_down( "cbo_store_name", 160, $blank_array,"", 1, "--Select store--", 0, "store_on_change(this.value);" );
                            ?>	
                        </td>
                    	
						<td align="right"><strong>Roll Number</strong></td>
                        <td>
                            <input type="text" name="txt_bar_code_num" id="txt_bar_code_num" class="text_boxes_numeric" style="width:148px;" onDblClick="openmypage_barcode()" placeholder="Browse/Write/scan"/>
                        </td>
						<td align="right">Item Category</td>
                        <td>
							<?
                            	echo create_drop_down( "cbo_item_category", 160, $item_category,'', 0, '', '', '','1',2 );
                            ?>
                        </td>
                    </tr>
                    <tr>
                    	<td align="right">Ready To Approved</td>
                        <td>
                        	<input type="hidden" name="is_approved" id="is_approved" value="">
							<? echo create_drop_down( "cbo_ready_to_approved", 160, $yes_no,"", 1, "-- Select--", 2, "","","" );?>
                        </td>
                        <td align="right">Remarks</td>
                        <td>
                            <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:148px;" />
                        </td>
                    </tr>
                </table>
			</fieldset>
			<br>
            <div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
            <br>
			<fieldset style="width:1990px;text-align:left;">
				<style>
                    #scanning_tbl tr td
                    {
                        background-color:#FFF;
                        color:#000;
                        border: 1px solid #666666;
                        line-height:12px;
                        height:20px;
                        overflow:auto;
                    }
                </style>
                <div id="test_dv"></div>
				<table cellpadding="0" width="1970" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="20">SL</th>
                        <th width="100">From Store</th>
                        <th width="70">Barcode No</th>
                        <th width="45">Roll No</th>
                        <th width="80">Body Part</th>
                        <th width="150">Fabric description</th>
                        <th width="100">Batch No</th>
                        <th width="40">GSM</th>
                        <th width="40">Dia</th>
                        <th width="120">Color</th>
                        <th width="60">Roll Wgt.</th>
                        <th width="60">Qty. In Pcs</th>
                        <th width="100">Reject Qty</th>
                        <th width="60">Buyer</th>
                        <th width="80">Booking</th>
                        <th width="80">Job No</th>
                        <th width="80">Internal Ref.</th>
                        <th width="80">Order No</th>
                        <th width="100">Finishing Company</th>
                        <th width="70">Basis</th>
                        <th width="90">To Order</th>
                        <th width="90">Body Part<br><input type="checkbox" checked id="bodyPartIds" name="bodyPartIds"/></th>
                        <th width="80">To Job</th>
                        <th width="115">Fiish fabric Roll<br> Delivery Challan</th>
                        <th></th>
                    </thead>
                 </table>
                 <div style="width:1990px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="1970" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody>
                        	<tr id="tr_1" align="center" valign="middle">
                                <td width="20" id="sl_1"></td>
                                <td width="100" id="fromStore_1"></td>
                                <td width="70" id="barcode_1"></td>
                                <td width="45" id="roll_1"></td>
                                <td style="word-break:break-all;" width="80" id="bodyPart_1"></td>
                                <td style="word-break:break-all;" width="150" id="cons_1" align="left" title=""></td>
                                <td style="word-break:break-all;" width="100" id="batchNo_1" align="center" title=""></td>
                                <td style="word-break:break-all;" width="40" id="gsm_1"></td>
                                <td style="word-break:break-all;" width="40" id="dia_1"></td>
                                <td style="word-break:break-all;" width="120" id="color_1"></td>
                                <td width="60" align="right" id="rollWeight_1"></td>
                                <td width="60" align="right" id="qtyInPcs_1"></td>
								<td style="word-break:break-all;" width="100" id="rejectQty_1" align="right"></td>
                                <td style="word-break:break-all;" width="60" id="buyer_1"></td>
                                <td style="word-break:break-all;" width="80" id="booking_1"></td>
                                <td style="word-break:break-all;" width="80" id="job_1"></td>
                                <td style="word-break:break-all;" width="80" id="intRef_1"></td>
                                <td style="word-break:break-all;" width="80" id="order_1" align="left"></td>
                                <td style="word-break:break-all;" width="100" id="knitCompany_1"></td>
                                <td style="word-break:break-all;" width="70" id="basis_1"></td>
                                <td width="90" id="toOrder_1"><input type="text"  class="text_boxes" id="txtToOrder_1" name="txtToOrder[]" style="width:75px;" onDblClick="opneToOrder(1)" placeholder="Browse For Order" value="" readonly /></td>
                                <td width="90" align="center" id="toBodyPartTd_1">
								<? echo create_drop_down( "cboToBodyPart_1", 90,$blank_array,"", 1, "--Select--", 0, "copyBodyPart(this.id,this.value)",0,"","","","","","","cboToBodyPart[]","onchange_void" ); 

								?>
								</td>
                                <td style="word-break:break-all;" width="80" id="toJob_1"></td>
                                <td style="word-break:break-all;" width="115" id="progBookPiNo_1"></td>
                                <td id="button_1" align="center">
                                    <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                                    <input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/>
                                    <input type="hidden" name="recvBasis[]" id="recvBasis_1"/>
                                    <input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/>
                                    <input type="hidden" name="productId[]" id="productId_1"/>
                                    <input type="hidden" name="orderId[]" id="orderId_1"/>
                                    <input type="hidden" name="rollId[]" id="rollId_1"/>
                                    <input type="hidden" name="rollWgt[]" id="rollWgt_1"/>
                                    <input type="hidden" name="batchId[]" id="batchId_1"/>
                                    <input type="hidden" name="yarnCount[]" id="yarnCount_1"/>
                                    <input type="hidden" name="colorId[]" id="colorId_1"/>
                                    <input type="hidden" name="rejectQtyVal[]" id="rejectQtyVal_1"/>
                                    <input type="hidden" name="brandId[]" id="brandId_1"/>
                                    <input type="hidden" name="rack[]" id="rack_1"/>
                                    <input type="hidden" name="shelf[]" id="shelf_1"/>
                                    <input type="hidden" name="fromStoreId[]" id="fromStoreId_1"/>
                                    <input type="hidden" name="toOrderId[]" id="toOrderId_1"/>
                                    <input type="hidden" name="tobookingNo[]" id="tobookingNo_1"/>
                                    <input type="hidden" name="toBookingMstId[]" id="toBookingMstId_1"/>
                                    <input type="hidden" name="dtlsId[]" id="dtlsId_1"/>
                                    <input type="hidden" name="transId[]" id="transId_1"/>
                                    <input type="hidden" name="transIdTo[]" id="transIdTo_1"/>
                                    <input type="hidden" name="rolltableId[]" id="rolltableId_1"/>
                                    <input type="hidden" name="febDescripId[]" id="febDescripId_1"/>
                                    <input type="hidden" name="machineNoId[]" id="machineNoId_1"/>
                                    <input type="hidden" name="prodGsm[]" id="prodGsm_1"/>
                                    <input type="hidden" name="diaWidth[]" id="diaWidth_1"/>
                                    <input type="hidden" name="knitDetailsId[]" id="knitDetailsId_1"/>
                                    <input type="hidden" name="transferEntryForm[]" id="transferEntryForm_1"/>
                                    <input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_1"/>
                                    <input type="hidden" name="barcodeIssue[]" id="barcodeIssue_1"/>
                                    <input type="hidden" name="rollMstId[]" id="rollMstId_1"/>
                                    <input type="hidden" name="rollAmount[]" id="rollAmount_1"/>
                                    <input type="hidden" name="fromProductUp[]" id="fromProductUp_1"/>
                                    <input type="hidden" name="fromBookingWithoutOrder[]" id="fromBookingWithoutOrder_1"/>
                                    <input type="hidden" name="hiddenQtyInPcs[]" id="hiddenQtyInPcs_1"/>
                                    <input type="hidden" name="bodyPartId[]" id="bodyPartId_1"/>
                                    <input type="hidden" name="hideBodyPartForUpdate[]" id="hideBodyPartForUpdate_1"/>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                        	<th colspan="10" style="text-align: right;"><strong>Total Qnty</strong></th>
                        	<th style="text-align: right;" id="total_rollwgt"><strong></strong></th>
                            <th style="text-align: right;" id="total_qtyInPcs"><strong></strong></th>
                            <th style="text-align: right;" id="total_qtyReject"><strong></strong></th>
                        	<th colspan="11"></th>

                        </tfoot>
                	</table>
                </div>
                <br>
                <table width="1340" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                    <tr>
                        <td align="center" class="button_container">
                        	<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
                            <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes" value="">
                            <input type="hidden" name="txt_deleted_barcode" id="txt_deleted_barcode" class="text_boxes" value="">
                            <input type="hidden" name="txt_deleted_trnsf_dtls_id" id="txt_deleted_trnsf_dtls_id" class="text_boxes" value="">
                            <input type="hidden" name="txt_deleted_trans_id" id="txt_deleted_trans_id" class="text_boxes" value="">
                            <input type="hidden" name="txt_deleted_prod_id" id="txt_deleted_prod_id" class="text_boxes" value="">
                            <input type="hidden" name="txt_deleted_prod_qty" id="txt_deleted_prod_qty" class="text_boxes" value="">
                            <input type="hidden" name="txt_deleted_source_roll_id" id="txt_deleted_source_roll_id" class="text_boxes" value="">

                            <? 
                            	echo load_submit_buttons($permission,"fnc_finish_fabric_issue_roll_wise",0,1,"fnc_reset_form(1);page_reload()",1);
                            ?>
                           
                        </td>
                    </tr>  
                </table>
			</fieldset>
        </form>	 
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
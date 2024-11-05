<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Grey Fabric Issue Roll Wise
				
Functionality	:	
JS Functions	:
Created by		:	Ashraful
Creation date 	: 	18-02-2015
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
echo load_html_head_contents("Woven Fabric Issue Roll Wise","../../", 1, 1, $unicode,'',''); 


?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	var scanned_barcode=new Array();
	<? 
	$scanned_barcode_array=array(); $barcode_dtlsId_array=array(); $barcode_trnasId_array=array(); $barcode_rollTableId_array=array();
	//$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
	//$company_name_array=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	//$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	//$batch_name_array=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	
	$jsbarcode_buyer_name_array= json_encode($buyer_name_array);
	echo "var jsbuyer_name_array = ". $jsbarcode_buyer_name_array . ";\n";

	$composition_arr=array(); $constructtion_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	$jsconstructtion_arr=json_encode($constructtion_arr);
	echo "var constructtion_arr = ". $jsconstructtion_arr . ";\n";
	
	$jscomposition_arr=json_encode($composition_arr);
	echo "var composition_arr = ". $jscomposition_arr . ";\n";

	/*$sql_item_description=sql_select("SELECT a.lib_yarn_count_deter_id ,b.po_break_down_id,a.item_number_id,a.body_part_id FROM wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id = b.pre_cost_fabric_cost_dtls_id and b.cons!=0 GROUP BY a.lib_yarn_count_deter_id ,b.po_break_down_id,a.item_number_id,a.body_part_id");
	$item_description_id_arr=array();
	foreach($sql_item_description as $ival)
	{
		$item_description_id_arr[$ival[csf('po_break_down_id')]][$ival[csf('body_part_id')]][$ival[csf('lib_yarn_count_deter_id')]]=$ival[csf('item_number_id')];
	} 
	$js_item_description_id_arrs= json_encode($item_description_id_arr);
	echo "item_description_id_arr = ". $js_item_description_id_arrs . ";\n";*/
	?>
	
	function openmypage_issue()
	{ 
		var cbo_company_id = $('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/woven_finish_fabric_issue_roll_wise_controller.php?cbo_company_id='+cbo_company_id+'&action=issue_popup','Issue Popup', 'width=780px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var issue_id=this.contentDoc.getElementById("hidden_system_id").value;	 //challan Id and Number
			
			if(issue_id!="")
			{
				fnc_reset_form();
				get_php_form_data(issue_id, "populate_data_from_data", "requires/woven_finish_fabric_issue_roll_wise_controller");
				//var barcode_nos=return_global_ajax_value( issue_id, 'barcode_nos', '', 'requires/woven_finish_fabric_issue_roll_wise_controller');
				var html=return_global_ajax_value(issue_id, 'populate_barcode_data_update', '', 'requires/woven_finish_fabric_issue_roll_wise_controller');
				if(trim(html)!="")
				{
					$("#scanning_tbl tbody").html(html);
					calculate_total();
					
					if($("#cbo_issue_purpose").val()==44)
					{
						var reprocess_data='';
						$("#scanning_tbl").find('tbody tr').each(function()
						{
							var reProcess=$(this).find('input[name="reProcess[]"]').val();
							var preRerocess=$(this).find('input[name="preRerocess[]"]').val();
							var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
							
							if(reprocess_data="") reprocess_data=reprocess_data+",";
							reprocess_data=reprocess_data+barcodeNo+"_"+reProcess;
						});
						get_php_form_data(reprocess_data, "populate_data_for_validation", "requires/woven_finish_fabric_issue_roll_wise_controller");
					}
				}
				set_button_status(1, permission, 'fnc_grey_fabric_issue_roll_wise',1);
				
				$("#print1").removeClass('formbutton_disabled');
				$("#print1").addClass('formbutton');
				$("#print2").removeClass('formbutton_disabled');
				$("#print2").addClass('formbutton');
				$("#print3").removeClass('formbutton_disabled');
				$("#print3").addClass('formbutton');
				$("#print4").removeClass('formbutton_disabled');
				$("#print4").addClass('formbutton');
				$("#print5").removeClass('formbutton_disabled');
				$("#print5").addClass('formbutton');

				$("#print_barcode").removeClass('formbutton_disabled');
				$("#print_barcode").addClass('formbutton');
				$("#btn_fabric_details").removeClass('formbutton_disabled');
				$("#btn_fabric_details").addClass('formbutton');
                calculate_total();
			}
		}
	}
	
	function openmypage_barcode()
	{ 
		var company_id=$('#cbo_company_id').val();
		var batch_id=$('#txt_batch_id').val();
		var txt_rqn_id=$('#txt_rqn_id').val()*1;
		var txt_rqn_qnty =$('#txt_rqn_qnty').val()*1;
		var hdn_issue_requisition_madatory_vari =$('#hdn_issue_requisition_madatory_vari').val()*1;
		var cbo_store_name=$('#cbo_store_name').val();
		if(hdn_issue_requisition_madatory_vari==1)
		{
			if (form_validation('cbo_company_id*cbo_store_name*txt_rqn_no','Company*Store Name*Requisition No')==false)
			{
				return;
			}
		}
		else
		{
			if (form_validation('cbo_company_id*cbo_store_name','Company*Store Name')==false)
			{
				return;
			}
		}
			
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/woven_finish_fabric_issue_roll_wise_controller.php?company_id='+company_id+'&batch_id='+batch_id+'&cbo_store_name='+cbo_store_name+'&txt_rqn_id='+txt_rqn_id+'&txt_rqn_qnty='+txt_rqn_qnty+'&hdn_issue_requisition_madatory_vari='+hdn_issue_requisition_madatory_vari+'&action=barcode_popup','Barcode Popup', 'width=850px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{										
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var barcode_nos=this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos
			if(barcode_nos!="")
			{
				create_row(0,barcode_nos);
				//set_all_onclick();
			}
            calculate_total();
		}
	}
	
	function reqsn_popup()
	{ 
		var company_id=$('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/woven_finish_fabric_issue_roll_wise_controller.php?company_id='+company_id+'&action=requisition_popup','Requisition Popup', 'width=780px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var reqsn_id=(this.contentDoc.getElementById("hidden_reqn_id").value).split("_"); //Barcode Nos
			if(reqsn_id!="")
			{
				$('#txt_rqn_no').val(reqsn_id[1]);	
				$('#txt_rqn_id').val(reqsn_id[0]);	
				$('#txt_rqn_qnty').val(reqsn_id[2]);	
				//var list_view = trim(return_global_ajax_value(reqsn_id[0], 'populate_list_view', '', 'requires/woven_finish_fabric_issue_roll_wise_controller'));
				//$("#list_product_container").html(list_view);
			}
		}
	}
	/*$('#txt_rqn_no').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			e.preventDefault();
			scan_rqn_no(this.value); 
		}
	});*/
	
	/*function scan_rqn_no(str)
	{
		var response=return_global_ajax_value(str, 'check_reqn_no', '', 'requires/woven_finish_fabric_issue_roll_wise_controller');
		if(response==0)
		{
			alert("Requisition No not found");	
			$("#txt_rqn_no").val('');
		}
		else
		{
			var list_view = trim(return_global_ajax_value(response, 'populate_list_view', '', 'requires/woven_finish_fabric_issue_roll_wise_controller'));
			$("#list_product_container").html(list_view);
		}
   }*/

	function generate_report_file(data,action)
	{
		window.open("requires/woven_finish_fabric_issue_roll_wise_controller.php?data=" + data+'&action='+action, true );
	}

	function fnc_report_print2(type)
	{
		if (type==6) // print_1
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val()+'*'+report_title,'finish_issue_print');
			return;
		}
		else{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val()+'*'+report_title,'finish_issue_print2');
			return;
		}
	}
	
	function func_print_button(btn)
	{
		var report_title=$( "div.form_caption" ).html();
		if(btn == 3)
		{
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val()+'*'+report_title,'actn_print_button_3');
			return;
		}
		else if(btn == 4)
		{
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val()+'*'+report_title,'actn_print_button_4');
			return;
		}
		else
		{
			if ($("#cbo_dyeing_source").val() ==3)
			{
				//alert(3);
                var show_cbo_dyeing_source = "0";
				var r = confirm("Press \"OK\" to open with actual buyer or Cancel.");
				//var r = confirm("'Do you want to print with actual buyer?' Ok/Cancel");
				if (r == true)
				{
					show_cbo_dyeing_source = "1";
				}
				else
				{
					show_cbo_dyeing_source = "0";
				}
            }

			generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_issue_no').val() + '*' + report_title + '*' + $('#update_id').val() + '*' + $('#cbo_dyeing_source').val()+ '*' + $("#no_copy").val()+ '*' + $("#cbo_dyeing_company").val()+ '*' + show_cbo_dyeing_source, 'roll_issue_no_of_copy_print');
	        return;
		}
	}

	function fnc_grey_fabric_issue_roll_wise( operation )
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		/*if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val()+'*'+report_title,'finish_issue_print');
			return;
		}*/

		

	 	if(form_validation('cbo_company_id*cbo_dyeing_source*cbo_dyeing_company*txt_issue_date*cbo_issue_purpose*cbo_store_name','Company*Dyeing Source*Dyeing Company*Issue Date*Issue Purpose*Store Name')==false)
		{
			return; 
		}
                var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_issue_date').val(), current_date)==false)
		{
			alert("Issue Date Can not Be Greater Than Current Date");
			return;
		}
                
		var j=0; var dataString='';
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var recvBasis=$(this).find('input[name="recvBasis[]"]').val();
			var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
			var batchId=$(this).find('input[name="batchId[]"]').val();
			var hdnBatchName=$(this).find('input[name="hdnBatchName[]"]').val(); 
			var progBookPiId=$(this).find('input[name="progBookPiId[]"]').val();
			var knittingcomId=$(this).find('input[name="knittingcomId[]"]').val();
			
			var productId=$(this).find('input[name="productId[]"]').val();
			var orderId=$(this).find('input[name="orderId[]"]').val();
			var rollId=$(this).find('input[name="rollId[]"]').val();
			var rollWgt=$(this).find('input[name="rollWgt[]"]').val();
			var rollNo=$(this).find('input[name="hdnRollNo[]"]').val();

			
			var bodyPartId=$(this).find('input[name="bodyPartId[]"]').val();
			var colorId=$(this).find('input[name="colorId[]"]').val();
			var deterId=$(this).find('input[name="deterId[]"]').val();
			var diatypeId=$(this).find('input[name="diatypeId[]"]').val();
			
			var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
			var transId=$(this).find('input[name="transId[]"]').val();
			var rolltableId=$(this).find('input[name="rolltableId[]"]').val();
			
			var reProcess=$(this).find('input[name="reProcess[]"]').val();
			var preRerocess=$(this).find('input[name="preRerocess[]"]').val();
			var bwoNo=$(this).find('input[name="bwoNo[]"]').val();
			var booking_without_order_status=$(this).find('input[name="bookingWithoutOrderStatus[]"]').val();	

			var floor_id=$(this).find('input[name="floorId[]"]').val();
			var room_id=$(this).find('input[name="roomId[]"]').val();
			var rack_id=$(this).find('input[name="rackId[]"]').val();
			var shelf_id=$(this).find('input[name="shelfId[]"]').val();	
			var bin_id=$(this).find('input[name="binId[]"]').val();
			var shade_id=$(this).find('input[name="hdnShadeId[]"]').val();
			var shade_id=$(this).find('input[name="hdnShadeId[]"]').val();
			var jobId=$(this).find('input[name="jobId[]"]').val();


			var bookingIdOriginal=$(this).find('input[name="hdnBookingId[]"]').val();
			var bookingNoOriginal=$(this).find('input[name="hdnBookingNo[]"]').val();
			var hdnRfId=$(this).find('input[name="hdnRfId[]"]').val();
			var hdnManualRollNo=$(this).find('input[name="hdnManualRollNo[]"]').val();
			var hdnFabricRef=$(this).find('input[name="hdnFabricRef[]"]').val();
			var hdnRdNo=$(this).find('input[name="hdnRdNo[]"]').val();
			var hdnOriginalGsm=$(this).find('input[name="hdnOriginalGsm[]"]').val();
			var hdnOriginalDia=$(this).find('input[name="hdnOriginalDia[]"]').val();
			var hdnWeightEditable=$(this).find('input[name="hdnWeightEditable[]"]').val();
			var hdnCutWidth=$(this).find('input[name="hdnCutWidth[]"]').val();
			var weightTypeId=$(this).find('input[name="weightTypeId[]"]').val();
			var orderUomId=$(this).find('input[name="orderUomId[]"]').val();
			
			var hdnOrdRate=$(this).find('input[name="hdnOrdRate[]"]').val();
			var hdnOrdAmnt=$(this).find('input[name="hdnOrdAmnt[]"]').val();
			var hdnConsRate=$(this).find('input[name="hdnConsRate[]"]').val();
			var hdnConsAmnt=$(this).find('input[name="hdnConsAmnt[]"]').val();
			//var txtRemarks=$(this).find('input[name="txtRemarks[]"]').val();

			var hdnOrderIle=$(this).find('input[name="hdnOrderIle[]"]').val();
			var hdnOrderIleCost=$(this).find('input[name="hdnOrderIleCost[]"]').val();
			var hdnConsIle=$(this).find('input[name="hdnConsIle[]"]').val();
			var hdnConsIleCost=$(this).find('input[name="hdnConsIleCost[]"]').val();



			if(floor_id==""){
				floor_id=0;
			}
			if(room_id==""){
				room_id=0;
			}
			if(rack_id==""){
				rack_id=0;
			}
			if(shelf_id==""){
				shelf_id=0;
			}
			if(bin_id==""){
				bin_id=0;
			}

			j++;
			dataString+='&recvBasis_' + j + '=' + recvBasis + '&barcodeNo_' + j + '=' + barcodeNo + '&progBookPiId_' + j + '=' + progBookPiId + '&productId_' + j + '=' + productId + '&orderId_' + j + '=' + orderId + '&rollId_' + j + '=' + rollId + '&rollWgt_' + j + '=' + rollWgt + '&bodyPartId_' + j + '=' + bodyPartId + '&deterId_' + j + '=' + deterId + '&colorId_' + j + '=' + colorId + '&diatypeId_' + j + '=' + diatypeId + '&batchId_' + j + '=' + batchId + '&hdnBatchName_' + j + '=' + hdnBatchName  + '&dtlsId_' + j + '=' + dtlsId + '&transId_' + j + '=' + transId + '&rolltableId_' + j + '=' + rolltableId + '&rollNo_' + j + '=' + rollNo+ '&knittingcomId_' + j + '=' + knittingcomId+ '&reProcess_' + j + '=' + reProcess+ '&preRerocess_' + j + '=' + preRerocess+ '&bwoNo_' + j + '=' + bwoNo+ '&booking_without_order_status_' + j + '=' + booking_without_order_status + '&floor_' + j + '=' + floor_id+ '&room_' + j + '=' + room_id+ '&rack_' + j + '=' + rack_id+ '&self_' + j + '=' + shelf_id+ '&binBox_' + j + '=' + bin_id+ '&shadeId_' + j + '=' + shade_id+ '&bookingIdOriginal_' + j + '=' + bookingIdOriginal+ '&bookingNoOriginal_' + j + '=' + bookingNoOriginal+ '&hdnRfId_' + j + '=' + hdnRfId+ '&hdnManualRollNo_' + j + '=' + hdnManualRollNo+ '&hdnFabricRef_' + j + '=' + hdnFabricRef+ '&hdnRdNo_' + j + '=' + hdnRdNo+ '&hdnOriginalGsm_' + j + '=' + hdnOriginalGsm+ '&hdnOriginalDia_' + j + '=' + hdnOriginalDia+ '&hdnWeightEditable_' + j + '=' + hdnWeightEditable+ '&hdnCutWidth_' + j + '=' + hdnCutWidth+ '&weightTypeId_' + j + '=' + weightTypeId+ '&orderUomId_' + j + '=' + orderUomId+ '&hdnOrdRate_' + j + '=' + hdnOrdRate+ '&hdnConsRate_' + j + '=' + hdnConsRate+ '&hdnOrdAmnt_' + j + '=' + hdnOrdAmnt+ '&hdnConsAmnt_' + j + '=' + hdnConsAmnt+ '&hdnOrderIle_' + j + '=' + hdnOrderIle+ '&hdnOrderIleCost_' + j + '=' + hdnOrderIleCost+ '&hdnConsIle_' + j + '=' + hdnConsIle+ '&hdnConsIleCost_' + j + '=' + hdnConsIleCost+ '&jobId_' + j + '=' + jobId; 
			
		});
		
		if(j<1)
		{
			alert('No data');
			return;
		}
		
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_issue_no*cbo_company_id*cbo_dyeing_source*cbo_dyeing_company*txt_issue_date*cbo_issue_purpose*cbo_store_name*cbo_location_name*update_id*txt_deleted_id*txt_rqn_no',"../../")+dataString;
		//alert(data);return;
		freeze_window(operation);
		
		http.open("POST","requires/woven_finish_fabric_issue_roll_wise_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_grey_fabric_issue_roll_wise_Reply_info;
	}

	function fnc_grey_fabric_issue_roll_wise_Reply_info()
	{
		if(http.readyState == 4) 
		{
			//alert(trim(http.responseText))
			//release_freezing();return;
			var response=trim(http.responseText).split('**');
			if(response[0]*1 == 20)
			{
					alert(response[1]);
					release_freezing();
					return;
			} 
			show_msg(response[0]);
			
			if((response[0]==0 || response[0]==1))
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_issue_no').value = response[2];
				$('#txt_deleted_id').val( '' );
				add_dtls_data(response[3]);
				set_button_status(1, permission, 'fnc_grey_fabric_issue_roll_wise',1);

				$("#print1").removeClass('formbutton_disabled');
				$("#print1").addClass('formbutton');
				$("#print2").removeClass('formbutton_disabled');
				$("#print2").addClass('formbutton');
				$("#print3").removeClass('formbutton_disabled');
				$("#print3").addClass('formbutton');
				$("#print4").removeClass('formbutton_disabled');
				$("#print4").addClass('formbutton');
				$("#print5").removeClass('formbutton_disabled');
				$("#print5").addClass('formbutton');
				
				$("#print_barcode").removeClass('formbutton_disabled');
				$("#print_barcode").addClass('formbutton');
				$("#btn_fabric_details").removeClass('formbutton_disabled');
				$("#btn_fabric_details").addClass('formbutton');
			}
			release_freezing();
		}
	}
	
	function create_row(is_update,barcode_nos)
	{		
		var cbo_company_id = $('#cbo_company_id').val();
		var txt_rqn_id = $('#txt_rqn_id').val();

		var hdn_issue_requisition_madatory_vari=$('#hdn_issue_requisition_madatory_vari').val()*1;
		var txt_rqn_no=$('#txt_rqn_no').val();
		if(hdn_issue_requisition_madatory_vari==1)
		{
			if (form_validation('cbo_company_id*cbo_store_name*txt_rqn_no','Company*Store*Reqsn No')==false)
			{
				$('#txt_bar_code_num').val();
				return;
			}
		}
		else
		{
			if (form_validation('cbo_company_id*cbo_store_name','Company*Store')==false)
			{
				$('#txt_bar_code_num').val();
				return;
			}
		}


		

		var cbo_store_name = $('#cbo_store_name').val();

		if( jQuery.inArray( barcode_nos, scanned_barcode )>-1) 
		{
			alert('Barcode is already scanned.');
			$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
			{
				$('#messagebox_main', window.parent.document).html('Barcode is already scanned.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
			});
			$('#txt_bar_code_num').val('');
			return; 
		}

		var barcode_data=trim(return_global_ajax_value(barcode_nos+'_'+cbo_store_name+'_'+txt_rqn_id, 'populate_barcode_data', '', 'requires/woven_finish_fabric_issue_roll_wise_controller'));

		var row_num=$('#txt_tot_row').val();
		//var bar_code=$('#txt_bar_code_num').val();
		//var bar_code=trim(barcode_no);
		var num_row =$('#scanning_tbl tbody tr').length; 

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
		if(barcode_data==99)
		{
			alert('Barcode is already scanned.');
			$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
			{
				$('#messagebox_main', window.parent.document).html('Barcode is already scanned.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
			});
			$('#txt_bar_code_num').val('');
			return; 
		}
		if(barcode_data==990)
		{
			alert('Barcode is not valid.');
			$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
			{
				$('#messagebox_main', window.parent.document).html('Barcode is not valid.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
			});
			$('#txt_bar_code_num').val('');
			return; 
		}
		
		var barcode_datas=barcode_data.split("____");

		for(var k=0; k<barcode_datas.length; k++)
		{
			var data=barcode_datas[k].split("**");
			var bar_code=data[0];

			if( jQuery.inArray( bar_code, scanned_barcode )>-1) 
			{
				alert('Barcode is already scanned.');
				$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
				{
					$('#messagebox_main', window.parent.document).html('Barcode '+ bar_code +' is already scanned.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
				});
				$('#txt_bar_code_num').val('');
				return; 
			}

			scanned_barcode.push(bar_code);
			var company_id=data[1];
			var roll_no=data[2];
			var roll_id=data[3];
			var body_part=data[5];
			var body_part_id=data[4];
			var bwo=data[6];
			var receive_basis=data[7];
			var receive_basis_id=data[8];
			var booking_no=data[9];
			var booking_id=data[10];
			var color=data[11];
			var color_id=data[12];
			var knitting_source_id=data[13];
			var knitting_source=data[14];
			var knitting_company_id=data[15];
			var knit_company=data[16];
			var batch_id=data[17];
			var diawidth_type_id=data[18];
			var diawidth_type=data[19];
			var batch_name=data[20];
			
			
			//var brand_id=data[21];
			//var rack=data[22];
			//var shelf=data[23];
			var prod_id=data[21];
			var deter_id=data[22];
			//alert(data[22])
			var cons_comp=constructtion_arr[deter_id]+", "+composition_arr[deter_id];
			var gsm=data[23];
			var width=data[24];
			var qnty=data[25];
			var rate=data[26];
			var booking_without_order=data[27];
			var reprocess=data[28];
			var woPiNo=data[29];

			var floor_id=data[30];
			var room_id=data[31];
			var rack_id=data[32];
			var shelf_id=data[33];
			var bin_id=data[34];

			var floor=data[35];
			var room=data[36];
			var rack=data[37];
			var shelf=data[38];
			var bin=data[39];
			var barcode_store_id=data[40];
			var shade_id=data[41];
			var shade_name=data[42];

			//------------------
			var bookingNoOriginal=data[43];
			var bookingIdOriginal=data[44];
			var rfId=data[45];
			var manual_roll_no=data[46];
			var fabric_ref=data[47];
			var rd_no=data[48];
			var original_gsm=data[49];
			var weight_editable=data[50];
			var weight_type_id=data[51];
			var weight_type=data[52];
			var order_uom_id=data[53];
			var order_uom_name=data[54];
			var cutable_width=data[55];

			var order_rate=data[56];
			var cons_rate=data[57];
			var order_amount=data[58];
			var cons_amount=data[59];
					
			var original_dia=data[60];	


			var order_ile=data[61];	
			var order_ile_cost=data[62];	
			var cons_ile=data[63];	
			var cons_ile_cost=data[64];	
			var prev_reprocess=data[65];
			//----------------


			var po_id=data[66];
			var buyer_id=data[67];
			var buyer_name=jsbuyer_name_array[buyer_id];
			var po_no=data[68];
			var job_no=data[69];
			var styleRef=data[70];
			var job_id=data[71];
			//var is_sales=data[33];


			if(cbo_company_id!=company_id)
			{
				alert("Barcode Current Company Not Matched with selected company");
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
				//alert(row_num);
			}
			
			//var cons_comp=constructtion_arr[roll_details_array[bar_code]['deter_d']]+", "+composition_arr[roll_details_array[bar_code]['deter_d']];
			
			$("#sl_"+row_num).text(row_num);
			$("#barcode_"+row_num).text(bar_code);
			$("#hdnRollNo_"+row_num).val(roll_no);
			$("#batch_"+row_num).text(batch_name);
			$("#hdnBatchName_"+row_num).val(batch_name);
			//$("#prodId_"+row_num).text(prod_id);
			$("#bodyPart_"+row_num).text(body_part);
			$("#cons_"+row_num).text(cons_comp);
			//$("#gsm_"+row_num).text(gsm);
			$("#dia_"+row_num).text(width);
			$("#color_"+row_num).text(color);
			//$("#diaType_"+row_num).text(diawidth_type)
			$("#rollWeight_"+row_num).text(qnty);
			//$("#buyer_"+row_num).text(buyer_name);
			$("#job_"+row_num).text(job_no);
			$("#order_"+row_num).text(po_no);
			//$("#knitCompany_"+row_num).text(knit_company);
			$("#knittingcomId_"+row_num).val(knitting_company_id);
			//$("#basis_"+row_num).text(receive_basis);
			//$("#progBookPiNo_"+row_num).text(booking_no);
			//$("#woPiNo_"+row_num).text(woPiNo);
			$("#barcodeNo_"+row_num).val(bar_code);
			$("#recvBasis_"+row_num).val(receive_basis_id);
			$("#progBookPiId_"+row_num).val(booking_id);
			$("#productId_"+row_num).val(prod_id);
			$("#orderId_"+row_num).val(po_id);
			$("#rollId_"+row_num).val(roll_id);
			$("#rollWgt_"+row_num).val(qnty);
			$("#reProcess_"+row_num).val(reprocess);
			$("#preRerocess_"+row_num).val(prev_reprocess);
			$("#bwoNo_"+row_num).val(bwo);
			$("#bookingWithoutOrderStatus_"+row_num).val(booking_without_order);
			
			//$("#dtlsId_"+row_num).val(barcode_dtlsId_array[bar_code]);
			//$("#transId_"+row_num).val(barcode_dtlsId_array[bar_code]);
			//$("#yarnLot_"+row_num).val(roll_details_array[bar_code]['yarn_lot']);
			//$("#yarnCount_"+row_num).val(roll_details_array[bar_code]['yarn_count']);
			$("#colorId_"+row_num).val(color_id);
			
			$("#deterId_"+row_num).val(deter_id);
			$("#diatypeId_"+row_num).val(diawidth_type_id);
			$("#bodyPartId_"+row_num).val(body_part_id);
			$("#batchId_"+row_num).val(batch_id);
			
			//$("#dtlsId_"+row_num).val();
			//$("#transId_"+row_num).val(barcode_trnasId_array[bar_code]);
			//$("#rolltableId_"+row_num).val(barcode_rollTableId_array[bar_code]);
			$("#dtlsId_"+row_num).val('');
			$("#transId_"+row_num).val('');
			$("#rolltableId_"+row_num).val('');
			
			//$("#rollRate_"+row_num).val(rate);
			$("#jobNo_"+row_num).val(job_no);
			$("#jobId_"+row_num).val(job_id);
	
			$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
			
			
			$("#floorId_"+row_num).val(floor_id);
			$("#roomId_"+row_num).val(room_id);
			$("#rackId_"+row_num).val(rack_id);
			$("#shelfId_"+row_num).val(shelf_id);
			$("#binId_"+row_num).val(bin_id);
			$("#floor_"+row_num).text(floor);
			$("#room_"+row_num).text(room);
			$("#rack_"+row_num).text(rack);
			$("#shelf_"+row_num).text(shelf);
			$("#bin_"+row_num).text(bin);
			$("#hdnStoreId_"+row_num).val(barcode_store_id);
			$("#hdnShadeId_"+row_num).val(shade_id);
			$("#shade_"+row_num).text(shade_name);


			//-----------

			$("#bookingNo_"+row_num).text(bookingNoOriginal);
			$("#hdnBookingNo_"+row_num).val(bookingNoOriginal);
			$("#hdnBookingId_"+row_num).val(bookingIdOriginal);
			$("#styleRef_"+row_num).text(styleRef);
			$("#rfId_"+row_num).text(rfId);
			$("#hdnRfId_"+row_num).val(rfId);
			$("#manualRollNo_"+row_num).text(manual_roll_no);
			$("#hdnManualRollNo_"+row_num).val(manual_roll_no);
			$("#fabricRef_"+row_num).text(fabric_ref);
			$("#hdnFabricRef_"+row_num).val(fabric_ref);
			$("#rdNo_"+row_num).text(rd_no);
			$("#hdnRdNo_"+row_num).val(rd_no);
			$("#originalGsm_"+row_num).text(original_gsm);
			$("#hdnOriginalGsm_"+row_num).val(original_gsm);
			$("#hdnOriginalDia_"+row_num).val(original_dia);
			$("#weightEditable_"+row_num).text(weight_editable);
			$("#hdnWeightEditable_"+row_num).val(weight_editable);
			$("#weightTypeId_"+row_num).val(weight_type_id);
			$("#weightType_"+row_num).text(weight_type);
			$("#orderUomId_"+row_num).val(order_uom_id);
			$("#orderUom_"+row_num).text(order_uom_name);
			$("#cutWidth_"+row_num).text(cutable_width);
			$("#hdnCutWidth_"+row_num).val(cutable_width);

			$("#hdnOrdRate_"+row_num).val(order_rate);
			$("#hdnConsRate_"+row_num).val(cons_rate);
			$("#hdnOrdAmnt_"+row_num).val(order_amount);
			$("#hdnConsAmnt_"+row_num).val(cons_amount);

			$("#hdnOrderIle_"+row_num).val(order_ile);
			$("#hdnOrderIleCost_"+row_num).val(order_ile_cost);
			$("#hdnConsIle_"+row_num).val(cons_ile);
			$("#hdnConsIleCost_"+row_num).val(cons_ile_cost);

			//$("#ordRate_"+row_num).text(order_rate);
			//$("#consRate_"+row_num).text(cons_rate);
			//$("#ordAmnt_"+row_num).text(order_amount);
			//$("#consAmnt_"+row_num).text(cons_amount);
			$("#txtIle_"+row_num).text(order_ile_cost);
			

			//------------

			// var garments_description_id=0;
			
			//  garments_description_id=item_description_id_arr[po_id][body_part_id][deter_id];
			 //if(garments_description_id!='') $("#cboItemName_"+row_num).val(garments_description_id);
				
		}
		
		$('#txt_tot_row').val(row_num);
		$('#txt_bar_code_num').val('');
		$('#txt_bar_code_num').focus();
		calculate_total();

	}
	
	$('#txt_bar_code_num').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			e.preventDefault();
			
			var bar_code=$('#txt_bar_code_num').val();
			
			if( jQuery.inArray( bar_code, scanned_barcode )>-1) 
			{
				alert('Barcode is already scanned.');
				$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
				{
					$('#messagebox_main', window.parent.document).html('Barcode is already scanned.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
				});
				$('#txt_bar_code_num').val('');
				return; 
			}
			create_row(0,bar_code);
		}
	});
	
	function add_dtls_data( data )
	{
		var barcode_dtlsId_array=new Array(); var barcode_trnasId_array=new Array(); var barcode_rollTableId_array=new Array();
		var barcode_datas=data.split(",");
		for(var k=0; k<barcode_datas.length; k++)
		{
			var datas=barcode_datas[k].split("__");
			var barcode_no=datas[0];
			var dtls_id=datas[1];
			var trans_id=datas[2];
			var roll_table_id=datas[3];
			
			barcode_dtlsId_array[barcode_no] = dtls_id;
			barcode_trnasId_array[barcode_no] = trans_id;
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
				$(this).find('input[name="rolltableId[]"]').val(barcode_rollTableId_array[barcodeNo]);	
			}
		});
	}
	
	function fn_deleteRow( rid )
	{
		var num_row =$('#scanning_tbl tbody tr').length;
		var bar_code =$("#barcodeNo_"+rid).val();
		var rolltableId =$("#rolltableId_"+rid).val();
		var txt_deleted_id=$('#txt_deleted_id').val();
		
		/*if( jQuery.inArray( bar_code,issue_barcode )>-1) 
		{ 
			alert('Sorry! Barcode Already Received by Cutting.'); 
			return; 
		}*/
	
	
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
		
		//var selected_id='';
		if(rolltableId!='')
		{
			if(trim(txt_deleted_id)=='') txt_deleted_id=rolltableId; else txt_deleted_id=txt_deleted_id+','+rolltableId;
			$('#txt_deleted_id').val(txt_deleted_id);
		}
		
		var index = scanned_barcode.indexOf(bar_code);
		//alert(index);
		scanned_barcode.splice(index,1);
                calculate_total();
	}
	
	function openmypage_batchNo()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var page_link='requires/woven_finish_fabric_issue_roll_wise_controller.php?cbo_company_id='+cbo_company_id+'&action=batch_number_popup';
			var title='Batch Number Popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=820px,height=390px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;
				var batch_no=this.contentDoc.getElementById("hidden_batch_no").value; //Access form field with id="emailfield"
				
				if(batch_id!="")
				{
					$('#txt_batch_no').val(batch_no);
					$('#txt_batch_id').val(batch_id);
				}
			}
		}
	}
	
	function check_batch(data)
	{
		if(data=="")
		{
			$('#txt_batch_id').val('');
			return;	
		}
		var cbo_company_id=$('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			$('#txt_batch_no').val('');
			$('#txt_batch_id').val('');
			return;
		}
		var batch_id=return_global_ajax_value( data+"**"+cbo_company_id, 'check_batch_no', '', 'requires/woven_finish_fabric_issue_roll_wise_controller');
		if(batch_id==0)
		{
			alert("Batch No Found");
			$('#txt_batch_no').val('');
			
			return;
		}
		else $('#txt_batch_id').val(batch_id);
		
	}
	
	function fnc_reset_form()
	{
		$('#scanning_tbl tbody tr').remove();
		
		$('#cbo_company_id').val(0);
		$('#cbo_company_id').attr('disabled',false);
		$('#cbo_dyeing_source').val(0);
		$('#cbo_dyeing_company').val(0);
		$('#txt_tot_row').val(1);
		$('#update_id').val('');
		$('#txt_issue_no').val('');
		$('#txt_issue_date').val('');
		$('#txt_deleted_id').val('');
		$('#cbo_issue_purpose').val(0);
		$('#txt_batch_no').val('');
		$('#txt_batch_id').val('');
		$('#roll_weight_total').val('');
		
		//$("#scanning_tbl tbody").html(html);	

		fnc_details_row_blank()
	}
	/*<td width="100"><input style="width:80px;" type="text" class="text_boxes" name="txtRemarks[]" id="txtRemarks_1"/></td>*/
	function fnc_details_row_blank()  
	{
		var html='<tr style="word-break:break-all;" id="tr_1" align="center" valign="middle"><td style="word-break:break-all;" width="35" id="sl_1"></td><td style="word-break:break-all;" width="100" id="bookingNo_1"></td><td style="word-break:break-all;" width="80" id="job_1"></td><td style="word-break:break-all;" width="100" id="styleRef_1"></td><td style="word-break:break-all;" width="80" id="order_1" align="left"></td><td style="word-break:break-all;" width="80" id="barcode_1"></td><td style="word-break:break-all;" width="100" id="rfId_1"></td><td style="word-break:break-all;" width="70" id="shade_1"></td><td style="word-break:break-all;" width="50" id="manualRollNo_1"></td><td style="word-break:break-all;" width="60" id="batch_1"></td><td style="word-break:break-all;" width="80" id="bodyPart_1"></td><td style="word-break:break-all;" width="130" id="cons_1" align="left"></td><td style="word-break:break-all;" width="100" id="fabricRef_1" align="left"></td><td style="word-break:break-all;" width="100" id="rdNo_1" align="left"></td><td style="word-break:break-all;" width="70" id="color_1"></td><td style="word-break:break-all;" width="50" id="originalGsm_1"></td><td style="word-break:break-all;" width="50" id="weightEditable_1"></td><td style="word-break:break-all;" width="50" id="weightType_1"></td><td style="word-break:break-all;" width="50" id="orderUom_1"></td><td style="word-break:break-all;" width="50" id="dia_1"></td><td style="word-break:break-all;" width="50" id="cutWidth_1"></td><td style="word-break:break-all;" width="70" align="right" id="rollWeight_1"></td><td style="word-break:break-all;" width="50" align="right" id="txtBalancePI_1"></td><td style="word-break:break-all;" width="50" align="right" id="txtIle_1"></td><td style="word-break:break-all;" width="70" align="right" id="floor_1"></td><td style="word-break:break-all;" width="70" align="right" id="room_1"></td><td style="word-break:break-all;" width="70" align="right" id="rack_1"></td><td style="word-break:break-all;" width="70" align="right" id="shelf_1"></td><td style="word-break:break-all;" width="70" align="right" id="bin_1"></td><td id="button_1" align="center" width="50"><input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" /><input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/><input type="hidden" name="recvBasis[]" id="recvBasis_1"/><input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/><input type="hidden" name="productId[]" id="productId_1"/><input type="hidden" name="orderId[]" id="orderId_1"/><input type="hidden" name="rollId[]" id="rollId_1"/><input type="hidden" name="rollWgt[]" id="rollWgt_1"/><input type="hidden" name="colorId[]" id="colorId_1"/><input type="hidden" name="bodyPartId[]" id="bodyPartId_1"/><input type="hidden" name="batchId[]" id="batchId_1"/><input type="hidden" name="hdnBatchName[]" id="hdnBatchName_1"/><input type="hidden" name="dtlsId[]" id="dtlsId_1"/><input type="hidden" name="transId[]" id="transId_1"/><input type="hidden" name="rolltableId[]" id="rolltableId_1"/><input type="hidden" name="deterId[]" id="deterId_1"/><input type="hidden" name="diatypeId[]" id="diatypeId_1"/><input type="hidden" name="knittingcomId[]" id="knittingcomId_1"/><input type="hidden" name="jobNo[]" id="jobNo_1"/><input type="hidden" name="jobId[]" id="jobId_1"/><input type="hidden" name="reProcess[]" id="reProcess_1"/><input type="hidden" name="preRerocess[]" id="preRerocess_1"/><input type="hidden" name="bwoNo[]" id="bwoNo_1"/><input type="hidden" name="bookingWithoutOrderStatus[]" id="bookingWithoutOrderStatus_1"/><input type="hidden" name="floorId[]" id="floorId_1"/><input type="hidden" name="roomId[]" id="roomId_1"/><input type="hidden" name="rackId[]" id="rackId_1"/><input type="hidden" name="shelfId[]" id="shelfId_1"/><input type="hidden" name="binId[]" id="binId_1"/><input type="hidden" name="hdnStoreId[]" id="hdnStoreId_1"/><input type="hidden" name="hdnShadeId[]" id="hdnShadeId_1"/><input type="hidden" name="hdnBookingId[]" id="hdnBookingId_1"/><input type="hidden" name="hdnBookingNo[]" id="hdnBookingNo_1"/><input type="hidden" name="hdnRfId[]" id="hdnRfId_1"/><input type="hidden" name="hdnRollNo[]" id="hdnRollNo_1"/><input type="hidden" name="hdnManualRollNo[]" id="hdnManualRollNo_1"/><input type="hidden" name="hdnRdNo[]" id="hdnRdNo_1"/><input type="hidden" name="hdnFabricRef[]" id="hdnFabricRef_1"/><input type="hidden" name="weightTypeId[]" id="weightTypeId_1"/><input type="hidden" name="orderUomId[]" id="orderUomId_1"/><input type="hidden" name="hdnOriginalGsm[]" id="hdnOriginalGsm_1"/><input type="hidden" name="hdnOriginalDia[]" id="hdnOriginalDia_1"/><input type="hidden" name="hdnWeightEditable[]" id="hdnWeightEditable_1"/><input type="hidden" name="hdnCutWidth[]" id="hdnCutWidth_1"/><input type="hidden" name="hdnOrdRate[]" id="hdnOrdRate_1"/><input type="hidden" name="hdnConsRate[]" id="hdnConsRate_1"/><input type="hidden" name="hdnOrdAmnt[]" id="hdnOrdAmnt_1"/><input type="hidden" name="hdnConsAmnt[]" id="hdnConsAmnt_1"/><input type="hidden" name="hdnOrderIle[]" id="hdnOrderIle_1"/><input type="hidden" name="hdnOrderIleCost[]" id="hdnOrderIleCost_1"/><input type="hidden" name="hdnConsIle[]" id="hdnConsIle_1"/><input type="hidden" name="hdnConsIleCost[]" id="hdnConsIleCost_1"/></td></tr>'; 

		$('#roll_weight_total').val('');

		$("#scanning_tbl tbody").html(html);
	}

	
	function barcode_print()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val(),'issue_challan_print');
	}
	
	function fabric_details()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_issue_no').val()+'*'+$('#update_id').val(),'fabric_details_print');
	}
	
	
        
	function calculate_total()
	{
		var total_roll_weight='';
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var rollWgt=$(this).find('input[name="rollWgt[]"]').val()*1;
			total_roll_weight=total_roll_weight*1+rollWgt*1;
		});
		$("#roll_weight_total").val(total_roll_weight.toFixed(2));	
	}

	function print_report_button_setting(com_id)
	{
		var report_ids=return_global_ajax_value( com_id, 'check_report_button', '', 'requires/woven_finish_fabric_issue_roll_wise_controller');
		//alert(report_ids);
		if(trim(report_ids)!="")
		{
			$("#print1").hide();
			$("#print2").hide();
			$("#print3").hide();
			$("#print4").hide();
			$("#print5").hide();
			$("#print_barcode").hide();
			$("#btn_fabric_details").hide();		

			var report_id=report_ids.split(",");
			for (var k=0; k<report_id.length; k++)
			{
				if(report_id[k]==86) $("#print1").show();
				if(report_id[k]==66) $("#print2").show();
				if(report_id[k]==85) $("#print3").show();
				if(report_id[k]==89) $("#print4").show();
				if(report_id[k]==129) $("#print5").show();
				if(report_id[k]==68) $("#print_barcode").show();
				if(report_id[k]==69) $("#btn_fabric_details").show();
			}
		}
		else
		{
			$("#print1").hide();
			$("#print2").hide();
			$("#print3").hide();
			$("#print4").hide();
			$("#print5").hide();
			$("#print_barcode").hide();
			$("#btn_fabric_details").hide();			
		}
	}
	function requsition_variable(data)
	{
		var varible_data=return_global_ajax_value( data, 'varible_inv_issue_requisition_madatory', '', 'requires/woven_finish_fabric_issue_roll_wise_controller');
		$("#hdn_issue_requisition_madatory_vari").val(varible_data);
		if(varible_data==1)
		{
			//$("#txt_rqn_no").removeAttr("disabled");
			$("#txt_rqn_no").attr("disabled",false);
		}
		else
		{
			$("#txt_rqn_no").attr("disabled",true);
			$('#txt_rqn_no').val('');
			$('#txt_rqn_qnty').val('');
		}
	}
</script>
</head>
<body onLoad="set_hotkey();$('#txt_bar_code_num').focus();">
    <div align="center" style="width:100%;">
		<? echo load_freeze_divs ("../../",$permission); ?>
        <form name="rollscanning_1" id="rollscanning_1"  autocomplete="off"  >
         <div style="width:1300px;"> 
         <div style="float:left; " align="center">
            <fieldset style="width:810px;">
				<legend>Roll Scanning</legend>
                <table cellpadding="0" cellspacing="2" width="800">
                    <tr>
                        <td colspan="6" align="center"><b>Issue No&nbsp;</b>
                        	<input type="text" name="txt_issue_no" id="txt_issue_no" class="text_boxes" style="width:140px;" onDblClick="openmypage_issue()" placeholder="Double Click To Search" readonly/>
                            <input type="hidden" name="update_id" id="update_id"/>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" class="must_entry_caption">Company</td>
                        <td>
                            <? 
                                //echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond","id,company_name", 1, "-- Select --", 0, "",0 );

                                echo create_drop_down("cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Company--", 0, "load_drop_down('requires/woven_finish_fabric_issue_roll_wise_controller', this.value, 'load_drop_down_store', 'store_td' );print_report_button_setting(this.value);requsition_variable(this.value);load_drop_down( 'requires/woven_finish_fabric_issue_roll_wise_controller', this.value, 'load_drop_down_location', 'location_td' );");
                            ?>
                        </td>
                        <td class="must_entry_caption" align="right">Service Source</td>
                        <td>
							<?
                                echo create_drop_down( "cbo_dyeing_source", 152, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'requires/woven_finish_fabric_issue_roll_wise_controller',this.value+'**'+$('#cbo_company_id').val(),'load_drop_down_knitting_com','dyeing_company_td' );","","1,3" );
                            ?>
                        </td>
                        <td align="right" class="must_entry_caption">Service Company</td>
                        <td id="dyeing_company_td">
                            <?
                                echo create_drop_down( "cbo_dyeing_company", 152, $blank_array,"", 1, "-- Select --", $selected, "","","" );
                            ?>
                        </td>
                    </tr>
                    <tr>
                    	<td align="right" class="must_entry_caption" width="100">Issue Date</td>
                        <td><input type="text" name="txt_issue_date" id="txt_issue_date" class="datepicker" style="width:140px;" value="<? echo date("d-m-Y"); ?>" readonly /></td>
                        <td align="right" class="must_entry_caption" width="100">Issue Purpose</td>
                        <td>
							<? 
								echo create_drop_down( "cbo_issue_purpose", 152, $yarn_issue_purpose,"", 1, "-- Select Purpose --",9, "","","3,9,10,44" ); 
							?>
                        </td>
                        <!-- <td align="right">Batch No</td> -->
                        <td align="right">Location</td>
                       	<td id="location_td">
							<?
								echo create_drop_down("cbo_location_name", 152, $blank_array, 1, "-- Select Location --", 0, "");
							?>
						</td>
                        <td>
                        	<input type="hidden" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:140px;" placeholder="Write / Browse" onDblClick="openmypage_batchNo();" onChange="check_batch(this.value);" />
                            <input type="hidden" id="txt_batch_id" name="txt_batch_id"/>
                        </td>
                    </tr>
                    <tr>
                    	<td height="5" colspan="6"></td>
                    </tr>
                    
                    <tr>
                    	<td align="right"  width="100"><strong>Reqsn. No</strong></td>
                        <td>
                        <input type="text" name="txt_rqn_no" id="txt_rqn_no" class="text_boxes" style="width:140px; text-align:left" onDblClick="reqsn_popup()" placeholder="Browse" readonly />
                        <input type="hidden" id="txt_rqn_id" name="txt_rqn_id"/>
                        <input type="hidden" id="txt_rqn_qnty" name="txt_rqn_qnty"/>
                        </td>
                        <td align="right"  width="100"><strong>Roll Number</strong></td>
                        <td>
						<input type="text" name="txt_bar_code_num" id="txt_bar_code_num" class="text_boxes_numeric" style="width:140px;" onDblClick="openmypage_barcode()" placeholder="Browse/Write/scan"/>
                        </td>
                        <td align="right" class="must_entry_caption">Store Name</td>
                        <td id="store_td">
                        	<?
								echo create_drop_down("cbo_store_name", 152, $blank_array, "", 1, "-- Select Store --", 0, "");
							?>
                        </td>
                    </tr>

                </table>
    
			</fieldset>
            </div>&nbsp;&nbsp;&nbsp;
            <div id="list_product_container" style="max-height:auto; float:left; width:450px; overflow: hidden; padding-top:0px; margin-left:10px;"></div>
            </div>
            
			<br><br><br>
           
			<fieldset style="width:1720px;text-align:left; margin-top:20px;">
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
				<table cellpadding="0" width="2525" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="35">SL</th>
                        <th width="100">Booking No</th>
                        <th width="80">Job No</th>
                        <th width="100">Style No</th>
                        <th width="80">Order No</th>
                        <th width="80">Barcode No</th>
                        <th width="100">RF Id</th>
                        <th width="70">Shrinkage Shade</th>
                        <th width="50">Manual Roll No</th>
                        <th width="60">Batch No</th>
                        <th width="80">Body Part</th>
                        <th width="130">Construction/ Composition</th>
                        <th width="100">Fabric Ref</th>
                        <th width="100">RD No</th>
                        <th width="70">Color</th>
                        <th width="50">Weight</th>
                        <th width="50">Actual weight</th>
                        <th width="50">weight Type</th>
                        <th width="50">UOM</th>
                        <th width="50">Full Width</th>
                        <th width="50">Cut. Width</th>
                        <th width="70">Issue Qty.</th>
                        <th width="50">Balance WO/PI </th>
                        <th width="50">ILE%</th>	

                        <th width="70">Floor</th>
                        <th width="70">Room</th>
                        <th width="70">Rack</th>
                        <th width="70">Shelf</th>
                        <th width="70">Bin</th>
                       <!--  <th width="100">Remarks</th> -->
                       
                        <th width="50">&nbsp;</th>
                    </thead>
                 </table>
                 <div style="width:2543px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="2525" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody>
                        	<tr id="tr_1" align="center" valign="middle">
                                <td style="word-break:break-all;" width="35" id="sl_1"></td>
                                <td style="word-break:break-all;" width="100" id="bookingNo_1"></td>
                                <td style="word-break:break-all;" width="80" id="job_1"></td>
                                <td style="word-break:break-all;" width="100" id="styleRef_1"></td>
                                <td style="word-break:break-all;" width="80" id="order_1" align="left"></td>
                                <td style="word-break:break-all;" width="80" id="barcode_1"></td>
                                <td style="word-break:break-all;" width="100" id="rfId_1"></td>
                                <td style="word-break:break-all;" width="70" id="shade_1"></td>
                                <td style="word-break:break-all;" width="50" id="manualRollNo_1"></td>
                                <td style="word-break:break-all;" width="60" id="batch_1"></td>
                                <td style="word-break:break-all;" width="80" id="bodyPart_1"></td>
                                <td style="word-break:break-all;" width="130" id="cons_1" align="left"></td>
                                <td style="word-break:break-all;" width="100" id="fabricRef_1"></td>
                                <td style="word-break:break-all;" width="100" id="rdNo_1"></td>
                                <td style="word-break:break-all;" width="70" id="color_1"></td>
                                <td style="word-break:break-all;" width="50" id="originalGsm_1"></td>
                                <td style="word-break:break-all;" width="50" id="weightEditable_1"></td>
                                <td style="word-break:break-all;" width="50" id="weightType_1"></td>
                                <td style="word-break:break-all;" width="50" id="orderUom_1"></td>
                                <td style="word-break:break-all;" width="50" id="dia_1"></td>
                                <td style="word-break:break-all;" width="50" id="cutWidth_1"></td>
                                <td style="word-break:break-all;" width="70" align="right" id="rollWeight_1"></td>
                                <td style="word-break:break-all;" width="50" align="right" id="txtBalancePI_1"></td>
                                <td style="word-break:break-all;" width="50" align="right" id="txtIle_1"></td>

    
                                <td style="word-break:break-all;" width="70" align="right" id="floor_1"></td>
                                <td style="word-break:break-all;" width="70" align="right" id="room_1"></td>
                                <td style="word-break:break-all;" width="70" align="right" id="rack_1"></td>
                                <td style="word-break:break-all;" width="70" align="right" id="shelf_1"></td>
                                <td style="word-break:break-all;" width="70" align="right" id="bin_1"></td>
                               <!--  <td style="word-break:break-all;" width="100"><input type="text" id="txtRemarks_<? echo $i; ?>" class="text_boxes"  style="width:80px" name="txtRemarks[]" /></td> -->


                                <td id="button_1" align="center" width="50">
                                    <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                                    <input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/>
                                    <input type="hidden" name="recvBasis[]" id="recvBasis_1"/>
                                    <input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/>
                                    <input type="hidden" name="productId[]" id="productId_1"/>
                                    <input type="hidden" name="orderId[]" id="orderId_1"/>
                                    <input type="hidden" name="rollId[]" id="rollId_1"/>
                                    <input type="hidden" name="rollWgt[]" id="rollWgt_1"/>
                                    <input type="hidden" name="colorId[]" id="colorId_1"/>
                                    <input type="hidden" name="bodyPartId[]" id="bodyPartId_1"/>
                                    <input type="hidden" name="batchId[]" id="batchId_1"/>
                                    <input type="hidden" name="dtlsId[]" id="dtlsId_1"/>
                                    <input type="hidden" name="transId[]" id="transId_1"/>
                                    <input type="hidden" name="rolltableId[]" id="rolltableId_1"/>
                                    <input type="hidden" name="deterId[]" id="deterId_1"/>
                                    <input type="hidden" name="diatypeId[]" id="diatypeId_1"/>
                                    <input type="hidden" name="knittingcomId[]" id="knittingcomId_1"/>
                                  
                                    <input type="hidden" name="jobNo[]" id="jobNo_1"/>
                                    <input type="hidden" name="jobId[]" id="jobId_1"/>
                                    <input type="hidden" name="reProcess[]" id="reProcess_1"/>
                                    <input type="hidden" name="preRerocess[]" id="preRerocess_1"/>
                                    <input type="hidden" name="bwoNo[]" id="bwoNo_1"/>
                                    <input type="hidden" name="bookingWithoutOrderStatus[]" id="bookingWithoutOrderStatus_1"/>
                                    <input type="hidden" name="floorId[]" id="floorId_1"/>
                                    <input type="hidden" name="roomId[]" id="roomId_1"/>
                                    <input type="hidden" name="rackId[]" id="rackId_1"/>
                                    <input type="hidden" name="shelfId[]" id="shelfId_1"/>
									<input type="hidden" name="binId[]" id="binId_1"/>
									<input type="hidden" name="hdnStoreId[]" id="hdnStoreId_1"/>
									<input type="hidden" name="hdnShadeId[]" id="hdnShadeId_1"/>
									<input type="hidden" name="hdnBookingId[]" id="hdnBookingId_1"/>
									<input type="hidden" name="hdnBookingNo[]" id="hdnBookingNo_1"/>
									<input type="hidden" name="hdnRfId[]" id="hdnRfId_1"/>
									<input type="hidden" name="hdnManualRollNo[]" id="hdnManualRollNo_1"/>
									<input type="hidden" name="hdnFabricRef[]" id="hdnFabricRef_1"/>
									<input type="hidden" name="hdnRdNo[]" id="hdnRdNo_1"/>
									<input type="hidden" name="hdnRollNo[]" id="hdnRollNo_1"/>
									<input type="hidden" name="weightTypeId[]" id="weightTypeId_1"/>
									<input type="hidden" name="orderUomId[]" id="orderUomId_1"/>
									<input type="hidden" name="hdnOriginalGsm[]" id="hdnOriginalGsm_1"/>
									<input type="hidden" name="hdnOriginalDia[]" id="hdnOriginalDia_1"/>
									<input type="hidden" name="hdnWeightEditable[]" id="hdnWeightEditable_1"/>
									<input type="hidden" name="hdnCutWidth[]" id="hdnCutWidth_1"/>
									<input type="hidden" name="hdnOrdRate[]" id="hdnOrdRate_1"/>
									<input type="hidden" name="hdnConsRate[]" id="hdnConsRate_1"/>
									<input type="hidden" name="hdnOrdAmnt[]" id="hdnOrdAmnt_1"/>
									<input type="hidden" name="hdnConsAmnt[]" id="hdnConsAmnt_1"/>
									<input type="hidden" name="hdnBatchName[]" id="hdnBatchName_1"/>
									<input type="hidden" name="hdnOrderIle[]" id="hdnOrderIle_1"/>
									<input type="hidden" name="hdnOrderIleCost[]" id="hdnOrderIleCost_1"/>
									<input type="hidden" name="hdnConsIle[]" id="hdnConsIle_1"/>
									<input type="hidden" name="hdnConsIleCost[]" id="hdnConsIleCost_1"/>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <th colspan="21">Grand Total</th>
                            <th width="70"><strong><input type="text" name="roll_weight_total" style="width:70px; text-align: right;"  id="roll_weight_total" readonly disabled></strong></th>
                            <th colspan="8"> </th>
                        </tfoot>
                	</table>
                </div>
                <br>
                <table width="1340" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                    <tr>
                        <td align="center" class="button_container">
                        	<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
                            <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes" value="">
                            <input type="hidden" name="hdn_issue_requisition_madatory_vari" id="hdn_issue_requisition_madatory_vari" class="text_boxes">
                            <? 
                            echo load_submit_buttons($permission,"fnc_grey_fabric_issue_roll_wise",0,0,"fnc_reset_form()",1);
                            ?>

                            <input type="button" name="print1" id="print1" class="formbutton_disabled" value="Print1" onClick="fnc_report_print2(6);" style=" width:100px; display:none">
                            <input type="button" name="print2" id="print2" class="formbutton_disabled" value="Print2" onClick="fnc_report_print2(5);" style=" width:100px; display:none">
                            
                            <input type="button" name="print3" id="print3" class="formbutton_disabled" value="Print3" onClick="func_print_button(3);" style=" width:100px; display:none">
                            <input type="button" name="print4" id="print4" class="formbutton_disabled" value="Print4" onClick="func_print_button(4);" style=" width:100px; display:none">
                            <input type="text" value="1"  title="No. of copy" placeholder="No. of copy" id="no_copy" class="text_boxes_numeric" style="width:55px;"/>
                            <input type="button" name="print5" id="print5" class="formbutton_disabled" value="Print5" onClick="func_print_button(7);" style=" width:100px; display:none">
                            
                            <input type="button" name="print_barcode" id="print_barcode" class="formbutton_disabled" value="Print Barcode" onClick="barcode_print();" style=" width:100px; display:none">
                            <input type="button" name="btn_fabric_details" id="btn_fabric_details" class="formbutton_disabled" value="Fabric Details" onClick="fabric_details();" style=" width:100px; display:none">
                        </td>
                    </tr>  
                </table>
			</fieldset>
        </form>	
     
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
if($('#cbo_company_id').val()!=0)
{
	$("#cbo_dyeing_source").val('1');
	load_drop_down( 'requires/woven_finish_fabric_issue_roll_wise_controller',1+'**'+$('#cbo_company_id').val(),'load_drop_down_knitting_com','dyeing_company_td' );
}
</script>
</html>

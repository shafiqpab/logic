<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knit Finish Fabric Roll Receive By Store
Functionality	:	
JS Functions	:
Created by		:	Ashraful 
Creation date 	: 	12-03-2015
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
echo load_html_head_contents("Grey Issue Info","../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
<? 

	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][68] );
	echo "var field_level_data= ". $data_arr . ";\n";

	if($_SESSION['logic_erp']['mandatory_field'][68]!="")
	{
		$mandatory_field_arr= json_encode( $_SESSION['logic_erp']['mandatory_field'][68] );
		echo "var mandatory_field_arr= ". $mandatory_field_arr . ";\n";
	}

    $company_name_array=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	
?>
  function generate_report_file(data,action)
	{
		window.open("requires/finish_feb_roll_receive_by_store_controller.php?data=" + data+'&action='+action, true );
	}

	function fnc_grey_receive_roll_wise( operation )
	{
		/*if(operation==2)
		{
		show_msg('13');
		return;
		}*/
		if(operation==4)
		{
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_system_no').val()+'*'+$('#update_id').val()+'*'+$('#txt_delivery_date').val(),'finish_delivery_print');
		return;
		}
		
	 	if(form_validation('txt_delivery_date*cbo_company_id*cbo_knitting_source*txt_challan_no*cbo_location*cbo_store_name','Delivery Date*Company*Knitting Source*Challan No*LC company location*Store Name')==false)
		{
		return; 
		}
		
		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][68]); ?>') 
		{
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][68]); ?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][68]); ?>')==false) {return;}
		}

        var current_date = '<? echo date("d-m-Y"); ?>';
        if (date_compare($('#txt_delivery_date').val(), current_date) == false) {
            alert("Receive Date Can not Be Greater Than Current Date");
            return;
        }
                
		var j=0; var dataString='';
		$("#scanning_tbl").find('tbody tr').each(function()
		{

			var store_update_upto=$('#store_update_upto').val()*1;
		
			var floor=$(this).find('select[name="cboFloor[]"]').val();
			var room=$(this).find('select[name="cboRoom[]"]').val();
			var rack=$(this).find('select[name="txtRack[]"]').val();
			var self=$(this).find('select[name="txtShelf[]"]').val();
			var binBox=$(this).find('select[name="txtBin[]"]').val();
			if(store_update_upto > 1)
			{
				if(store_update_upto==5 && (floor==0 || room==0 || rack==0 || self==0))
				{
					alert("Up To Shelf Value Full Fill Required For Inventory");return;
				}
				else if(store_update_upto==4 && (floor==0 || room==0 || rack==0))
				{
					alert("Up To Rack Value Full Fill Required For Inventory");return;
				}
				else if(store_update_upto==3 && (floor==0 || room==0))
				{
					alert("Up To Room Value Full Fill Required For Inventory");return;
				}
				else if(store_update_upto==2 && floor==0)
				{
					alert("Up To Floor Value Full Fill Required For Inventory");return;
				}
			}


			if($(this).find('input[name="checkRow[]"]').is(':checked'))
			{
			var activeId=1; 
			}
			else
			{
			var activeId=0; 	
			}

			var updateDetailsId=$(this).find('input[name="updateDetaisId[]"]').val();
			var transId=$(this).find('input[name="transId[]"]').val();
			var rollTableId=$(this).find('input[name="rollTableId[]"]').val();
			var productionId=$(this).find('input[name="productionId[]"]').val();
			var productionDtlId=$(this).find('input[name="productionDtlsId[]"]').val();
			var rollId=$(this).find('input[name="rollId[]"]').val();
			var batchId=$(this).find('input[name="batchID[]"]').val();
			var bodyPart=$(this).find('input[name="bodyPartId[]"]').val();
			 var colorId=$(this).find('input[name="colorId[]"]').val();
			var deterId=$(this).find('input[name="deterId[]"]').val();
			var productId=$(this).find('input[name="productId[]"]').val();
			var orderId=$(this).find('input[name="orderId[]"]').val();
			var buyerId=$(this).find('input[name="buyerId[]"]').val();
			var rollQty=$(this).find('input[name="rollQty[]"]').val();
			var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
			var reprocess=$(this).find('input[name="reProcess[]"]').val();
			var preReprocess=$(this).find('input[name="prereProcess[]"]').val();
			var IsSalesId=$(this).find('input[name="IsSalesId[]"]').val();
			var rollNo=$(this).find("td:eq(2)").text();
			var rolldia=$(this).find("td:eq(9)").text();
			var rollGsm=$(this).find("td:eq(8)").text();
			var currentWgt=$(this).find('input[name="currentQty[]"]').val();
			var rejectQty=$(this).find("td:eq(11)").text();
			//var room=$(this).find('select[name="roomName[]"] option:selected').val();
			//var rack=$(this).find('select[name="rackName[]"] option:selected').val();
			//var self=$(this).find('select[name="selfName[]"] option:selected').val();
			var job_no=$(this).find('input[name="JobNumber[]"]').val();
			var wideTypeId=$(this).find('input[name="wideTypeId[]"]').val();
			var dyeingCharge=$(this).find('input[name="dyeingCharge[]"]').val();
			var greyRate=$(this).find('input[name="greyRate[]"]').val();
			var bookingWithoutOrder=$(this).find('input[name="bookingWithoutOrder[]"]').val();
			var bookingNumber=$(this).find('input[name="bookingNumber[]"]').val();
			//+ '&rack_' + j + '=' + rack
			 
			 var systemId=trim($(this).find("td:eq(23)").text());
			j++;
		dataString+='&rollId_' + j + '=' + rollId  + '&bodyPart_' + j + '=' + bodyPart + '&colorId_' + j + '='+colorId  + '&productId_' + j + '='+ productId + '&orderId_' + j + '=' + orderId + '&rollGsm_' + j + '=' + rollGsm + '&rollQty_' + j + '=' + rollQty + '&currentWgt_' + j + '=' + currentWgt+ '&deterId_' + j + '=' + deterId +'&rejectQty_' + j + '=' + rejectQty+ '&job_no_' + j + '=' + job_no + '&floor_' + j + '=' +floor + '&room_' + j + '=' + room + '&rack_' + j + '=' + rack + '&self_' + j + '=' + self + '&binBox_' + j + '=' + binBox + '&rolldia_' + j + '=' + rolldia + '&updateDetailsId_' + j + '=' + updateDetailsId+ '&activeId_' + j + '=' + activeId+ '&barcodeNo_' + j + '=' + barcodeNo+ '&rollNo_' + j + '=' + rollNo+ '&systemId_' + j + '=' + systemId+ '&batchId_' + j + '=' + batchId + '&productionId_' + j + '=' + productionId+ '&productionDtlId_' + j + '=' + productionDtlId+ '&wideTypeId_' + j + '=' + wideTypeId+ '&buyerId_' + j + '=' + buyerId+ '&rollTableId_' + j + '=' + rollTableId+ '&transId_' + j + '=' + transId+ '&greyRate_' + j + '=' + greyRate+ '&dyeingCharge_' + j + '=' + dyeingCharge+ '&reprocess_' + j + '=' + reprocess+ '&preReprocess_' + j + '=' + preReprocess+ '&IsSalesId_' + j + '=' + IsSalesId + '&bookingWithoutOrder_' + j + '=' + bookingWithoutOrder+ '&bookingNumber_' + j + '=' + bookingNumber;

		
		});
		
		
		if(j<1)
		{
			alert('No data found');
			return;
		}
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_delivery_date*txt_challan_no*cbo_company_id*cbo_knitting_source*knit_company_id*cbo_location*cbo_store_name*knit_location_id*update_id*txt_system_no*txt_boe_mushak_challan_no*txt_boe_mushak_challan_date',"../../")+dataString;
		// alert(data);return; 
		
		freeze_window(operation);

		http.open("POST","requires/finish_feb_roll_receive_by_store_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_grey_delivery_roll_wise_Reply_info;
	}

	function fnc_grey_delivery_roll_wise_Reply_info()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);

			if(response[0]==20 )
			{
				alert(response[1]);
				release_freezing();
				return;
			}

			if((response[0]==0 || response[0]==1))
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_system_no').value = response[2];
				
				if(trim(response[3])!="")
				{
					var all_id=(response[3]).split(",");
					var k=0;
					for(k=1;k<=all_id.length;k++)
					{
					var tr_id=(all_id[k-1]).split("__");
					$("#updateDetaisId_"+tr_id[0]).val(tr_id[1]);
					$("#transId_"+tr_id[0]).val(tr_id[2]);
					$("#rollTableId_"+tr_id[0]).val(tr_id[3]);
					$("#rollQty_"+tr_id[0]).val(tr_id[4]);
					}
				}
				set_button_status(1, permission, 'fnc_grey_receive_roll_wise',1);
				$("#print1").removeClass('formbutton_disabled');
				$("#print1").addClass('formbutton');
				$("#btn_fabric_details").removeClass('formbutton_disabled');
				$("#btn_fabric_details").addClass('formbutton');
			
			}
			else if(response[0]==2)
			{
				location.reload();
			}
			release_freezing();
		}
	}

	function openmypage_challan()
	{
		var company_id = $("#cbo_company_id").val();
		var cbo_is_sales = $("#cbo_is_sales").val();
		var disabled = $("#cbo_is_sales").prop('disabled');
		if(disabled==true){ disabled=1;}else{ disabled=0;}

		var page_link='requires/finish_feb_roll_receive_by_store_controller.php?action=challan_popup&company_id='+company_id+'&is_sales='+cbo_is_sales+'&is_disabled='+disabled; 
		var title="Search Challan Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var grey_recv_no=this.contentDoc.getElementById("hidden_challan_no").value.split("_");
			var grey_recv_id=this.contentDoc.getElementById("hidden_challan_id").value;
			$("#txt_challan_no").val(grey_recv_no[0]);
			$("#cbo_company_id").val(grey_recv_no[1]);  
			$("#cbo_knitting_source").val(grey_recv_no[2]); 
			$("#knit_company_id").val(grey_recv_no[3]); 
			$("#txt_knitting_company").val(grey_recv_no[4]); 
			$("#knit_location_id").val(grey_recv_no[7]); 
			$("#txt_knitting_location").val(grey_recv_no[8]); 

			//$row[csf('sys_number')]."_".$row[csf('company_id')]."_".$row[csf('knitting_source')]."_".$row[csf('knitting_company')]."_".$knit_comp."_".$dyeing_charge_basis."_".$is_sales."_".$row[csf('location_id')]."_".$location_name

			get_php_form_data( grey_recv_no[1], 'company_wise_report_button_setting','requires/finish_feb_roll_receive_by_store_controller' );

			var is_sales = grey_recv_no[6];
			load_drop_down( 'requires/finish_feb_roll_receive_by_store_controller',grey_recv_no[1] , 'load_drop_down_location', 'location_td' );
			show_list_view(grey_recv_no[0]+'_'+grey_recv_no[5]+'_'+is_sales,'finish_item_details','list_view_container','requires/finish_feb_roll_receive_by_store_controller','');
			set_button_status(0, permission, 'fnc_grey_receive_roll_wise',1);
                        var rollQntyTotal = 0;  var rejectQntyTotal = 0; var greyWgtQntyTotal = 0;
                         $("#scanning_tbl").find('tbody tr').each(function(){
                            rollQntyTotal+=$(this).find('input[name="currentQty[]"]').val()*1;
                            rejectQntyTotal+=$(this).find('input[name="rejectQnty[]"]').val()*1;
                            greyWgtQntyTotal+=$(this).find('input[name="usedQnty[]"]').val()*1;
                        });
                        $("#rollQntyTotal").html(rollQntyTotal);
                         $("#rejectQntyTotal_id").html(number_format(rejectQntyTotal,2));
                        $("#usedQntyTotal_id").html(number_format(greyWgtQntyTotal,2));
			release_freezing();
		}
	}

	$('#txt_challan_no').live('keydown', function(e)
	 {
	 if (e.keyCode === 13)
	 {
	 e.preventDefault();
	 scan_challan_no(this.value); 
	 }
	});	


 	function scan_challan_no(str)
	{
		var response=return_global_ajax_value(str, 'check_challan_no', '', 'requires/finish_feb_roll_receive_by_store_controller');
		if(response==2)
		{
			alert("Invalid Challan No.");	
			$("#txt_challan_no").val('');
		}
		else if(response==3)
		{
			alert("Delivery challan received in Knit Finish Fabric Receive By Garments page");	
			$("#txt_challan_no").val('');
		}
		else if(response==0)
		{
			alert("All Barcode In This Challan Are Saved");	
			$("#txt_challan_no").val('');
		}
		else
		{
			get_php_form_data(str, "load_php_form", "requires/finish_feb_roll_receive_by_store_controller" );
			show_list_view(str+"_"+$('#dyeing_charge_basis').val(),'finish_item_details','list_view_container','requires/finish_feb_roll_receive_by_store_controller','');
			total_crnDelvQty_rejQty_prodQty();
		}
   }
	function total_crnDelvQty_rejQty_prodQty(){
		var rollQntyTotal = 0; var rollProductionQntyTotal = 0;var rollRejectQntyTotal = 0;//prodQty_2 / rejectQty_2
		$("#scanning_tbl").find('tbody tr').each(function(){
		   rollQntyTotal+=$(this).find('input[name="currentQty[]"]').val()*1;
		   rollProductionQntyTotal+=$(this).find('input[name="usedQnty[]"]').val()*1;
		   rollRejectQntyTotal+=$(this).find('input[name="rejectQnty[]"]').val()*1;


		});
		$("#rollQntyTotal").html(number_format(rollQntyTotal,2));
		$("#usedQntyTotal_id").html(number_format(rollProductionQntyTotal,2));
		$("#rejectQntyTotal_id").html(number_format(rollRejectQntyTotal,2));
	}
	function open_mrrpopup()
	{
		var company_id = $("#cbo_company_id").val(); 
		var page_link='requires/finish_feb_roll_receive_by_store_controller.php?&action=update_system_popup&company_id='+company_id;
		var title='Finish Fabric Receive By Store';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var grey_recv_no=this.contentDoc.getElementById("hidden_receive_no").value;
			var grey_recv_id=this.contentDoc.getElementById("hidden_update_id").value;
			var grey_challan_no=this.contentDoc.getElementById("hidden_challan_no").value;
			
			$("#txt_system_no").val(grey_recv_no);
			$("#txt_challan_no").val(grey_challan_no);
			$("#update_id").val(grey_recv_id);
			$("#txt_challan_no").attr('disabled','true');
			if(trim(grey_recv_id)!="")
			{
				get_php_form_data(grey_recv_id, "load_php_form_update", "requires/finish_feb_roll_receive_by_store_controller" );
				var cbo_company_id=$("#cbo_company_id").val();
				
				// get_php_form_data( cbo_company_id, 'company_wise_report_button_setting','requires/finish_feb_roll_receive_by_store_controller' );

				show_list_view(grey_challan_no+"_"+grey_recv_id+"_"+cbo_company_id,'finish_item_details_update','list_view_container','requires/finish_feb_roll_receive_by_store_controller','');
				set_button_status(1, permission, 'fnc_grey_receive_roll_wise',1);
				$("#print1").removeClass('formbutton_disabled');
				$("#print1").addClass('formbutton');
				$("#btn_fabric_details").removeClass('formbutton_disabled');
				$("#btn_fabric_details").addClass('formbutton');
	                        
	            var rollQntyTotal = 0; var rejectQntyTotal = 0; var greyWgtQntyTotal = 0;
	             $("#scanning_tbl").find('tbody tr').each(function(){
	                rollQntyTotal+=$(this).find('input[name="currentQty[]"]').val()*1;
	                rejectQntyTotal+=$(this).find('input[name="rejectQnty[]"]').val()*1;
	                greyWgtQntyTotal+=$(this).find('input[name="usedQnty[]"]').val()*1;
	            });
	            $("#rollQntyTotal").html(number_format(rollQntyTotal,2));
	            $("#rejectQntyTotal_id").html(number_format(rejectQntyTotal,2));
	            $("#usedQntyTotal_id").html(number_format(greyWgtQntyTotal,2));
                     
			}
		}
		
	}
     
	function set_focus()
	{
	$("#txt_challan_no").focus();	
	}
	
	function fabric_details()
	{
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_system_no').val()+'*'+$('#update_id').val()+'*'+$('#txt_delivery_date').val(),'fabric_details_print');

		//generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_system_no').val()+'*'+$('#update_id').val()+'*'+$('#txt_delivery_date').val(),'finish_delivery_print');
	}
	
    function fnc_rollQntyChange()
    {
         var rollQntyTotal = 0;
        $("#scanning_tbl").find('tbody tr').each(function(){
          rollQntyTotal+=$(this).find('input[name="currentQty[]"]').val()*1;
        });  
        $("#rollQntyTotal").html(rollQntyTotal);
    }

	function fnc_reset(){
		reset_form('rollscanning_1','list_view_container','','','');

		fnc_details_row_blank();
	}

	function fnc_details_row_blank()
	{
		$("#scanning_tbl").html('<tbody id="list_view_container"><tr id="tr_1" align="center" valign="middle"><td width="40" id="sl_1" >1&nbsp;&nbsp;<input type="checkbox" id="checkRow_1" name="checkRow[]" ></td><td width="80" id="barcode_1"></td><td width="45" id="rollNo_1"></td><td width="60" id="batchNo_1"></td><td width="80" id="bodyPart_1" style="word-break:break-all;" align="left"></td><td width="80" id="cons_1" style="word-break:break-all;" align="left"></td><td width="80" id="comps_1" style="word-break:break-all;" align="left"></td><td width="70" id="color_1"></td><td width="40" id="gsm_1"></td><td width="40" id="dia_1" style="word-break:break-all;"></td><td width="50" id="rollWgt_1"><input type="text" id="currentQty_1" class="text_boxes_numeric"  style="width:35px" name="currentQty[]" onChange="fnc_rollQntyChange()"/></td><td width="50" id="rejectQty_1"></td><td width="50" id="processLoss_1"></td><td width="50" id="usedQty_1"></td><td width="50" align="center" id="floorTd_1" class="floor_td_to"><? echo create_drop_down( "cboFloor_1", 50,$blank_array,"", 1, "--Select--", 0, "change_floor(this.value,this.id);",0,"","","","","","","cboFloor[]","onchange_void"); ?></td><td width="50" align="center" id="roomTd_1"><? echo create_drop_down( "cboRoom_1", 50,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","cboRoom[]","onchange_void" ); ?></td><td width="50" align="center" id="rackTd_1"><? echo create_drop_down( "txtRack_1", 50,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","txtRack[]","onchange_void" ); ?></td><td width="50" align="center" id="shelfTd_1"><? echo create_drop_down( "txtShelf_1", 50,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","txtShelf[]","onchange_void" ); ?></td><td width="50" align="center" id="binTd_1"><? echo create_drop_down( "txtBin_1", 50,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","txtBin[]","onchange_void" ); ?></td><td width="60" id="wideType_1"></td><td width="45" id="year_1" align="center"></td><td width="45" id="job_1"></td><td width="65" id="buyer_1"></td><td width="80" id="order_1" style="word-break:break-all;" align="left"></td><td width="60" id="prodId_1"></td><td width="" id="systemId_1" style="word-break:break-all;"><input type="hidden" name="barcodeNo[]" id="barcodeNo_1" value="1"/><input type="hidden" name="productionId[]" id="productionId_1" value=""/><input type="hidden" name="productionDtlsId[]" id="productionDtlsId_1" value=""/><input type="hidden" name="deterId[]" id="deterId_1" value=""/><input type="hidden" name="productId[]" id="productId_1" value=""/><input type="hidden" name="orderId[]" id="orderId_1" value=""/><input type="hidden" name="rollId[]" id="rollId_1" value=""/><input type="hidden" name="rollQty[]" id="rollQty_1"  value="" /><input type="hidden" name="batchID[]" id="batchID_1"  value="" /><input type="hidden" name="bodyPartId[]" id="bodyPartId_1" value=""/> <input type="hidden" name="colorId[]" id="colorId_1" value=""/> <input type="hidden" name="updateDetaisId[]" id="updateDetaisId_1"  /> <input type="hidden" name="wideTypeId[]" id="wideTypeId_1" /> <input type="hidden" name="JobNumber[]" id="JobNumber_1"  /> <input type="hidden" name="wideTypeId[]" id="wideTypeId_1" /> <input type="hidden" name="JobNumber[]" id="JobNumber_1"  /><input type="hidden" name="reProcess[]" id="reProcess_1"/><input type="hidden" name="prereProcess[]" id="prereProcess_1"/><input type="hidden" name="IsSalesId[]" id="IsSalesId_1"/><input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_1"/><input type="hidden" name="bookingNumber[]" id="bookingNumber_1"/><input type="hidden" name="rejectQnty[]" id="rejectQnty_1"/><input type="hidden" name="usedQnty[]" id="usedQnty_1"/></td>  </tr></tbody><tfoot><tr><th colspan="10">Total</th><th id="rollQntyTotal"></th><th id="rejectQntyTotal_id"></th><th id="processLossTotal_id"></th><th id="usedQntyTotal_id"></th><th colspan="10"></th></tr></tfoot>');
	}


	function fn_load_floor(store_id)
	{
		// alert(store_id);return;
		// var com_id=$('#cbo_company_id').val();
		// var location_id=$('#cbo_location').val();
		// var all_data=com_id + "__" + store_id + "__" + location_id;
		var com_id=$('#cbo_company_id').val();
		var all_data=com_id + "__" + store_id;
		//alert(all_data);return;
		var floor_result = return_global_ajax_value(all_data, 'floor_list', '', 'requires/finish_feb_roll_receive_by_store_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(floor_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(floor_result);
		for(var i=1; i<=tbl_length; i++)
		{
			$('#cboFloor_'+i).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject))
			{
				$('#cboFloor_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};

			// $('#cboFloor_'+i).attr("onchange","change_floor(this.value,this.id);")
			

			// $('#cboRoom_'+i).html('<option value="'+0+'">Select</option>');
			// $('#txtRack_'+i).html('<option value="'+0+'">Select</option>');
			// $('#txtShelf_'+i).html('<option value="'+0+'">Select</option>');
			// $('#txtBin_'+i).html('<option value="'+0+'">Select</option>');
		}
	}

	function fn_load_room(floor_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_company_id').val();
		var location_id=0;
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
		//alert(all_data);return;
		var room_result = return_global_ajax_value(all_data, 'room_list', '', 'requires/grey_fabric_receive_roll_controller');
		var tbl_length=$('#table_body tbody tr').length;
		//alert(room_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(room_result);
		for(var i=sequenceNo; i<=tbl_length; i++)
		{
			$('#cbo_room_to_'+i).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#cbo_room_to_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}
	}

	function fn_load_room(floor_id, sequenceNo)
	{
	
		// alert(floor_id);return;
		var com_id=$('#cbo_company_id').val();
		var location_id=0;
		//var location_id=$('#cbo_location').val();
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
		//alert(all_data);return;
		var room_result = return_global_ajax_value(all_data, 'room_list', '', 'requires/finish_feb_roll_receive_by_store_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(room_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(room_result);
		for(var i=sequenceNo; i<=tbl_length; i++)
		{
			$('#cboRoom_'+i).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#cboRoom_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}

		// $("#scanning_tbl").find('tbody tr').each(function()
		// {
		// 	var roomId = $(this).find('select[name="cboRoom[]"]').attr("id");
		// 	var roomIdSlArr = roomId.split("_");
		// 	// copy only that and below selected data
		// 	if( sequenceNo >= roomIdSlArr[1]*1 )
		// 	{
		// 		$(this).find('select[name="cboRoom[]"]').html('<option value="'+0+'">Select</option>');
		// 		for (var key of Object.keys(JSONObject).sort())
		// 		{
		// 			$(this).find('select[name="cboRoom[]"]').append('<option value="'+key+'">'+JSONObject[key]+'</option>');
		// 		};
		// 	}
		// });
	}

	function fn_load_rack(room_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_company_id').val();
		var location_id=0;
		//var location_id=$('#cbo_location').val();
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + room_id;
		//alert(all_data);return;
		var rack_result = return_global_ajax_value(all_data, 'rack_list', '', 'requires/finish_feb_roll_receive_by_store_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(rack_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(rack_result);

		for(var i=sequenceNo; i<=tbl_length; i++)
		{
			$('#txtRack_'+i).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#txtRack_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}

		// $("#scanning_tbl").find('tbody tr').each(function()
		// {
		// 	var rackId = $(this).find('select[name="txtRack[]"]').attr("id");
		// 	var rackIdSlArr = rackId.split("_");
		// 	// copy only that and below selected data
		// 	if( sequenceNo >= rackIdSlArr[1]*1 )
		// 	{
		// 		$(this).find('select[name="txtRack[]"]').html('<option value="'+0+'">Select</option>');
		// 		for (var key of Object.keys(JSONObject).sort())
		// 		{
		// 			$(this).find('select[name="txtRack[]"]').append('<option value="'+key+'">'+JSONObject[key]+'</option>');
		// 		};
		// 	}
		// });
	}

	function fn_load_shelf(rack_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_company_id').val();
		var location_id=0;
		//var location_id=$('#cbo_location').val();
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + rack_id;
		//alert(all_data);return;
		var shelf_result = return_global_ajax_value(all_data, 'shelf_list', '', 'requires/finish_feb_roll_receive_by_store_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(shelf_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(shelf_result);

		for(var i=sequenceNo; i<=tbl_length; i++)
		{
			$('#txtShelf_'+i).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#txtShelf_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}

		// $("#scanning_tbl").find('tbody tr').each(function()
		// {
		// 	var shelfId = $(this).find('select[name="txtShelf[]"]').attr("id");
		// 	var shelfIdSlArr = shelfId.split("_");
		// 	// copy only that and below selected data
		// 	if( sequenceNo >= shelfIdSlArr[1]*1 )
		// 	{
		// 		$(this).find('select[name="txtShelf[]"]').html('<option value="'+0+'">Select</option>');
		// 		for (var key of Object.keys(JSONObject).sort())
		// 		{
		// 			$(this).find('select[name="txtShelf[]"]').append('<option value="'+key+'">'+JSONObject[key]+'</option>');
		// 		};
		// 	}
		// });
	}

	function fn_load_bin(shelf_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_company_id').val();
		var location_id=0;
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + shelf_id;
		//alert(all_data);return;
		var shelf_result = return_global_ajax_value(all_data, 'bin_list', '', 'requires/finish_feb_roll_receive_by_store_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(shelf_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(shelf_result);

		for(var i=sequenceNo; i<=tbl_length; i++)
		{
			$('#txtBin_'+i).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#txtBin_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}

		// $("#scanning_tbl").find('tbody tr').each(function()
		// {
		// 	var shelfId = $(this).find('select[name="txtBin[]"]').attr("id");
		// 	var binIdSlArr = shelfId.split("_");
		// 	// copy only that and below selected data
		// 	if( sequenceNo >= binIdSlArr[1]*1 )
		// 	{
		// 		$(this).find('select[name="txtBin[]"]').html('<option value="'+0+'">Select</option>');
		// 		for (var key of Object.keys(JSONObject).sort())
		// 		{
		// 			$(this).find('select[name="txtBin[]"]').append('<option value="'+key+'">'+JSONObject[key]+'</option>');
		// 		};
		// 	}
		// });
	}

	function copy_all(str)
	{
		var data=str.split("_");
		
		var trall=$('#scanning_tbl tbody tr').length;
		
		var copy_tr=parseInt(trall);
	
		if($('#floorIds').is(':checked'))
		{
			if(data[1]==0) data_value=$("#cboFloor_"+data[0]).val();
		}
		if($('#roomIds').is(':checked'))
		{
			if(data[1]==1) data_value=$("#cboRoom_"+data[0]).val();
		}
		if($('#rackIds').is(':checked'))
		{
			if(data[1]==2) data_value=$("#txtRack_"+data[0]).val();
		}
		if($('#shelfIds').is(':checked'))
		{
			if(data[1]==3) data_value=$("#txtShelf_"+data[0]).val();
		}
		if($('#binIds').is(':checked'))
		{
			if(data[1]==4) data_value=$("#txtBin_"+data[0]).val();
		}

		var first_tr=parseInt(data[0])+1;
		
		for(var k=first_tr; k<=copy_tr; k++)
		{
			if($('#floorIds').is(':checked'))
			{
				console.log(data_value);
				if(data[1]==0) 	$("#cboFloor_"+k).val(data_value);
			}
			if($('#roomIds').is(':checked'))
			{
				if(data[1]==1) 	$("#cboRoom_"+k).val(data_value);
			}
			if($('#rackIds').is(':checked'))
			{
				if(data[1]==2) 	$("#txtRack_"+k).val(data_value);
			}
			if($('#shelfIds').is(':checked'))
			{
				if(data[1]==3) 	$("#txtShelf_"+k).val(data_value);
			}
			if($('#binIds').is(':checked'))
			{
				if(data[1]==4) 	$("#txtBin_"+k).val(data_value);
			}	
		}
	}

	// function copy_all(str)
	// {
	// 	var data=str.split("_");
	// 	console.log(data);
	// 	var trall=$('#scanning_tbl tbody tr').length;
	// 	//var trall=$("#txt_tr_length").val();
	// 	var copy_tr=parseInt(trall);
		
	// 	if($('#floorIds').is(':checked'))
	// 	{
	// 		if(data[1]==0) data_value=$("#cboFloor_"+data[0]).val();
	// 	}
	// 	if($('#roomIds').is(':checked'))
	// 	{
	// 		if(data[1]==1) data_value=$("#cboRoom_"+data[0]).val();
	// 	}
	// 	if($('#rackIds').is(':checked'))
	// 	{
	// 		if(data[1]==2) data_value=$("#txtRack_"+data[0]).val();
	// 	}
	// 	if($('#shelfIds').is(':checked'))
	// 	{
	// 		if(data[1]==3) data_value=$("#txtShelf_"+data[0]).val();
	// 	}
	// 	if($('#binIds').is(':checked'))
	// 	{
	// 		if(data[1]==4) data_value=$("#txtBin_"+data[0]).val();
	// 	}

	// 	$("#scanning_tbl").find('tbody tr').each(function()
	// 	{

	// 		var floorId = $(this).find('select[name="cboFloor[]"]').attr("id");
	// 		var floorIdSlArr = floorId.split("_");
	// 		//console.log(floorIdSlArr[1]);
			
	// 		// copy only that and below selected data
	// 		if( data[0] >= floorIdSlArr[1]*1 )
	// 		{
	// 			if($('#floorIds').is(':checked'))
	// 			{
					
	// 				if(data[1]==0) 	$(this).find('select[name="cboFloor[]"]').val(data_value);
	// 			}

	// 			if($('#roomIds').is(':checked'))
	// 			{
	// 				if(data[1]==1) $(this).find('select[name="cboRoom[]"]').val(data_value);
	// 			}
	// 			if($('#rackIds').is(':checked'))
	// 			{
	// 				if(data[1]==2) $(this).find('select[name="txtRack[]"]').val(data_value);
	// 			}
	// 			if($('#shelfIds').is(':checked'))
	// 			{
	// 				if(data[1]==3) $(this).find('select[name="txtShelf[]"]').val(data_value);
	// 			}
	// 			if($('#binIds').is(':checked'))
	// 			{
	// 				if(data[1]==4) $(this).find('select[name="txtBin[]"]').val(data_value);
	// 			}
	// 		}
	// 	});
	// }

	// function copy_all(str)
	// {
	
	// 	var data=str.split("_");
	// 	var trall=$("#list_view_container tr").length;
	// 	var copy_tr=parseInt(trall);
	// 	if(data[1]==0) data_value=$("#roomName_"+data[0]).val();
	// 	if(data[1]==1) data_value=$("#rackName_"+data[0]).val();
	// 	if(data[1]==2) data_value=$("#selfName_"+data[0]).val();
	// 	var first_tr=parseInt(data[0])+1;
	// 	for(var k=first_tr; k<=copy_tr; k++)
	// 	{
	// 	if(data[1]==0) 	$("#roomName_"+k).val(data_value);
	// 	if(data[1]==1) 	$("#rackName_"+k).val(data_value);
	// 	if(data[1]==2) 	$("#selfName_"+k).val(data_value);
	// 	}
	// }

	function reset_room_rack_shelf(id,fieldName)
	{
		var numRow=$('#table_body tbody tr').length;
		if (fieldName=="cbo_store_name") 
		{			
			$("#scanning_tbl").find('tbody tr').each(function()
			{
 				$(this).find('select[name="cboFloor[]"]').val("");
				$(this).find('select[name="cboRoom[]"]').val("");
				$(this).find('select[name="txtRack[]"]').val("");
				$(this).find('select[name="txtShelf[]"]').val("");
				$(this).find('select[name="txtBin[]"]').val("");
			});
		}
		else if (fieldName=="cbo_floor_to") 
		{
			$("#scanning_tbl").find('tbody tr').each(function()
			{
				$(this).find('select[name="cboRoom[]"]').val("");
				$(this).find('select[name="txtRack[]"]').val("");
				$(this).find('select[name="txtShelf[]"]').val("");
				$(this).find('select[name="txtBin[]"]').val("");
			});
		}
		else if (fieldName=="cbo_room_to")  
		{
			$("#scanning_tbl").find('tbody tr').each(function()
			{
				$(this).find('select[name="txtRack[]"]').val("");
				$(this).find('select[name="txtBin[]"]').val("");
			});
		}
		else if (fieldName=="txt_rack_to")  
		{
			$("#scanning_tbl").find('tbody tr').each(function()
			{
				$(this).find('select[name="txtBin[]"]').val("");
			});
		}
	}


	function change_floor(value,id)
    {
	    var id=id.split('_');
		var roomTd='roomTd_'+id[1];		
		load_drop_down( 'requires/finish_feb_roll_receive_by_store_controller', value+"_"+roomTd, 'load_drop_down_room', roomTd);
    }

    function change_room(value,id)
    {     	
    	var id=id.split('_');
		var rackTd='rackTd_'+id[1];	
		load_drop_down( 'requires/finish_feb_roll_receive_by_store_controller', value+"_"+rackTd, 'load_drop_down_rack', rackTd);
    }

    function change_rack(value,id)
    {
    	var id=id.split('_');
		var shelfTd='shelfTd_'+id[1];
		//alert(value+'='+id+'='+shelfTd);		
		load_drop_down( 'requires/finish_feb_roll_receive_by_store_controller', value+"_"+shelfTd, 'load_drop_down_shelf', shelfTd);
    }

    function change_shelf(value,id)
    {
    	var id=id.split('_');
		var binTd='binTd_'+id[1];
		//alert(value+'='+id+'='+binTd);		
		load_drop_down( 'requires/finish_feb_roll_receive_by_store_controller', value+"_"+binTd, 'load_drop_down_bin', binTd);
    }

	function check_all(tot_check_box_id)
	{
		if ($('#'+tot_check_box_id).is(":checked"))
		{ 
			$('#scanning_tbl tbody tr').each(function() {
				$('#scanning_tbl tbody tr input:checkbox').attr('checked', true);
			});
		}
		else
		{ 
			$('#scanning_tbl tbody tr').each(function() {

				if(!$(this).find('[type=checkbox]').is(':disabled'))
				{
					$(this).find('[type=checkbox]').attr('checked', false);
				}

				//$('#scanning_tbl tbody tr input:checkbox').attr('checked', false);
			});
		} 
	}

</script>
<style>tr, td{word-break: break-all;}</style>
</head>

<body onLoad="set_hotkey(); set_focus()">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?>  		 
    <form name="rollscanning_1" id="rollscanning_1"  autocomplete="off"  >
            <fieldset style="width:810px;">
				<legend>Issue Challan Scanning</legend>
                <table cellpadding="0" cellspacing="2" width="900">
                    <tr>
     
                        <td align="right" colspan="3" width="100">Receive No</td>
                        <td colspan="3" align="left">
                        	<input type="text" name="txt_system_no" id="txt_system_no" class="text_boxes" style="width:140px;" onDblClick="open_mrrpopup()" placeholder="Browse For System No" />
                            <input type="hidden" name="update_id" id="update_id"/>
                            <input type="hidden" name="dyeing_charge_basis" id="dyeing_charge_basis" value="0"/>
                        </td>
                    </tr>
                    <tr>
                        <td align="right"  width="100">Challan No</td>
                        <td  align="left">
                        	<input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:140px;" onDblClick="openmypage_challan()" placeholder="Scan/Browse/Write" /></td>
                        <td align="right" class="must_entry_caption" width="">Receive Date</td>
                        <td width="160"><input type="text" name="txt_delivery_date" id="txt_delivery_date" class="datepicker" style="width:140px;"  placeholder="Select Date" readonly/></td>
                        <td align="right" class="must_entry_caption">Company</td>
                        <td>
                            <? 
                                echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0","id,company_name", 1, "--Select--", 0, "load_drop_down('requires/finish_feb_roll_receive_by_store_controller',this.value, 'load_drop_down_location','location_td');" );//$company_cond
                                //load_drop_down( 'requires/finish_feb_roll_receive_by_store_controller', this.value, 'load_drop_down_store', 'store_td' ); 
                              ?>
                              <input type="hidden" name="store_update_upto" id="store_update_upto">
                        </td>
                    </tr>
                    <tr>
                      <td align="right">Prod. Source </td>
                        <td>
							<? 
								echo create_drop_down("cbo_knitting_source",152,$knitting_source,"", 1, "-- Display --", 0,"",1); 
							?>
                        </td>
                    <td align="right">Dye/Finish Company</td>
                        <td id="knitting_com"> 
                        	<input type="text" name="txt_knitting_company" id="txt_knitting_company" class="text_boxes" style="width:140px;"  disabled readonly placeholder="Display" />
                            <input type="hidden" name="knit_company_id" id="knit_company_id"/>
                        </td>

                      <!--   <td align="right" >Store Name</td>
                      	<td id="store_td">
                      		<?	
                      			//echo create_drop_down( "cbo_store_name", 152, "select id, store_name from lib_store_location where status_active=1 and is_deleted=0","id,store_name", 1, "--- Select Store ---", 1, "" );
                      		?>
                      	</td> -->

                      	<td align="right" class="must_entry_caption">LC Company Location</td>
                            <td id="location_td">
								<?
								echo create_drop_down("cbo_location", 152, $blank_array, "", 1, "-- Select Location --", 0, "");
								?>
                            </td>
                    </tr>
                   <tr>
                   	<td align="right" class="must_entry_caption">Store Name</td>
                      	<td id="store_td">
                      		<?	
                      			//echo create_drop_down( "cbo_store_name", 152, $blank_array,"",1, "--Select--", 1, "" );
                      			//echo create_drop_down( "cbo_store_name", 152, "select id, store_name from lib_store_location where status_active=1 and is_deleted=0","id,store_name", 1, "--- Select Store ---", 1, "" );

                      			echo create_drop_down( "cbo_store_name", 152, $blank_array,"", 1, "--Select store--", 0, "fnc_details_row_blank();fn_load_floor(this.value);" );
                      		?>
                      	</td>
						    <td align="right">BOE/Mushak Challan No</td>                                              
							<td> 
								<input type="text" name="txt_boe_mushak_challan_no" id="txt_boe_mushak_challan_no" class="text_boxes" style="width:140px">
							</td>
							<td align="right">BOE/Mushak Challan Date</td>                                              
							<td> 
								<input type="text" name="txt_boe_mushak_challan_date" id="txt_boe_mushak_challan_date" class="datepicker" style="width:140px">
							</td>
						</tr>
						<tr>
                      	<td align="right">Dyeing Location</td>
                      	<td>
                      		<input type="text" name="txt_knitting_location" id="txt_knitting_location" class="text_boxes" style="width:140px;" placeholder="Display" disabled/>
                            <input type="hidden" name="knit_location_id" id="knit_location_id"/>
                      	</td>
                   </tr>
                </table>
			</fieldset> 
            <br/>
              <fieldset style="width:1510px;text-align:left">
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
				<table cellpadding="0" width="1480" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="40">SL<input type="checkbox" id="all_check" onClick="check_all('all_check')" checked="checked"/></th>
                        <th width="80">Barcode No</th>
                        <th width="45">Roll No</th>
                        <th width="60">Batch No</th>
                        <th width="80">Body Part</th>
                        <th width="80">Construction</th>
                        <th width="80">Composition</th>
                        <th width="70">Color</th>
                        <th width="40">GSM</th>
                        <th width="40">Dia</th>
                        <th width="50">Roll Qty.</th>
                        <th width="50">Reject Qty.</th>
                        <th width="50">Process Loss</th>
                        <th width="50">Grey Wgt.</th>
                        <th width="50">Floor<br><input type="checkbox" checked id="floorIds" name="floorIds"/></th>
                        <th width="50">Room<br><input type="checkbox" checked id="roomIds" name="roomIds"/></th>
                        <th width="50">Rack<br><input type="checkbox" checked id="rackIds" name="rackIds"/></th>
                        <th width="50">Shelf<br><input type="checkbox" checked id="shelfIds" name="shelfIds"/></th>
                        <th width="50">Bin/Box<br><input type="checkbox" checked id="binIds" name="binIds"/></th>
                        <th width="60">Dia/  Width Type</th>
                        <th width="45">Year</th>
                        <th width="45">Job No</th>
                        <th width="65">Buyer</th>
                        <th width="80">Order No</th>
                        <th width="60">Product Id</th>
                        <th width="">System Id</th>
                    </thead>
                 </table>
                 <div style="width:1510px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="1480" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    <tbody id="list_view_container">
                        	<tr id="tr_1" align="center" valign="middle">
                                <td width="40" id="sl_1" >1&nbsp;&nbsp;
                               		<input type="checkbox" id="checkRow_<? echo $j; ?>" name="checkRow[]" ></td>
                                <td width="80" id="barcode_1"></td>
                                <td width="45" id="rollNo_1"></td>
                                <td width="60" id="batchNo_1"></td>
                                <td width="80" id="bodyPart_1" style="word-break:break-all;" align="left"></td>
                                <td width="80" id="cons_1" style="word-break:break-all;" align="left"></td>
                                <td width="80" id="comps_1" style="word-break:break-all;" align="left"></td>
                                <td width="70" id="color_1"></td>
                                <td width="40" id="gsm_1"></td>
                                <td width="40" id="dia_1" style="word-break:break-all;"></td>
                                <td width="50" id="rollWgt_1">
                                    <input type="text" id="currentQty_1" class="text_boxes_numeric"  style="width:35px" name="currentQty[]" onChange="fnc_rollQntyChange()"/>
                                </td>
                                <td width="50" id="rejectQty_1"></td>
                                <td width="50" id="processLoss_1"></td>
                                <td width="50" id="usedQty_1"></td>
                                <!-- <td width="50" id="room_1">
                                	<input type="text" id="roomName_1" class="text_boxes"  style="width:35px" name="roomName[]"/>
                                </td>
                                <td width="50" id="rack_1"><input type="text" id="rackName_1" class="text_boxes"  style="width:35px" name="rackName[]"/></td>
                                <td width="50" id="self_1"><input type="text" id="selfName_1" class="text_boxes"  style="width:35px" name="selfName[]"/></td> -->



                                <td width="50" align="center" id="floorTd_1" class="floor_td_to">
								<? echo create_drop_down( "cboFloor_1", 50,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","cboFloor[]" ,"onchange_void"); ?>
								</td>
								<td width="50" align="center" id="roomTd_1">
		                        <? echo create_drop_down( "cboRoom_1", 50,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","cboRoom[]","onchange_void" ); ?>		            
		                        </td>
								<td width="50" align="center" id="rackTd_1">
								<? echo create_drop_down( "txtRack_1", 50,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","txtRack[]","onchange_void" ); ?>
								</td>
								<td width="50" align="center" id="shelfTd_1">
								<? echo create_drop_down( "txtShelf_1", 50,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","txtShelf[]","onchange_void" ); ?>
								</td>
								<td width="50" align="center" id="binTd_1">
								<? echo create_drop_down( "txtBin_1", 50,$blank_array,"", 1, "--Select--", 0, "",0,"","","","","","","txtBin[]","onchange_void" ); ?>
								</td>


                                <td width="60" id="wideType_1"></td>
                                <td width="45" id="year_1" align="center"></td>
                                <td width="45" id="job_1"></td>
                                <td width="65" id="buyer_1"></td>
                                <td width="80" id="order_1" style="word-break:break-all;" align="left"></td>
                                <td width="60" id="prodId_1"></td>
                                <td width="" id="systemId_1" style="word-break:break-all;">
                                    <input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $j;?>" value="<? echo $key; ?>"/>
                                    <input type="hidden" name="productionId[]" id="productionId_<? echo $j;?>" value=""/>
                                    <input type="hidden" name="productionDtlsId[]" id="productionDtlsId_<? echo $j;?>" value=""/>
                                    <input type="hidden" name="deterId[]" id="deterId_1" value=""/>
                                    <input type="hidden" name="productId[]" id="productId_<? echo $j;?>" value=""/>
                                    <input type="hidden" name="orderId[]" id="orderId_<? echo $j;?>" value=""/>
                                    <input type="hidden" name="rollId[]" id="rollId_<? echo $j;?>" value=""/>
                                    <input type="hidden" name="rollQty[]" id="rollQty_<? echo $j;?>"  value="" />
                                    <input type="hidden" name="batchID[]" id="batchID_<? echo $j;?>"  value="" />
                                    <input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $j; ?>" value=""/> 
                                    <input type="hidden" name="colorId[]" id="colorId_<? echo $j; ?>" value=""/> 
                                    <input type="hidden" name="updateDetaisId[]" id="updateDetaisId_<? echo $j; ?>"  /> 
                                    <input type="hidden" name="wideTypeId[]" id="wideTypeId_<? echo $j; ?>" /> 
                                    <input type="hidden" name="JobNumber[]" id="JobNumber_<? echo $j; ?>"  /> 
                                    <input type="hidden" name="wideTypeId[]" id="wideTypeId_<? echo $j; ?>" /> 
                                    <input type="hidden" name="JobNumber[]" id="JobNumber_<? echo $j; ?>"  />
                                    <input type="hidden" name="reProcess[]" id="reProcess_1"/>
                                    <input type="hidden" name="prereProcess[]" id="prereProcess_1"/>
                                    <input type="hidden" name="IsSalesId[]" id="IsSalesId_1"/>
                                    <input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_1"/>
                                    <input type="hidden" name="bookingNumber[]" id="bookingNumber_1"/>

                                    <input type="hidden" name="rejectQnty[]" id="rejectQnty_1"/>
                                    <input type="hidden" name="usedQnty[]" id="usedQnty_1"/>
                             </td>  
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="10">Total</th>
                                <th id="rollQntyTotal"></th>
                                <th id="rejectQntyTotal_id"></th>
                                <th id="processLossTotal_id"></th>
                                <th id="usedQntyTotal_id"></th>
                                <th colspan="10"></th>
                            </tr>
                        </tfoot>
                	</table>
                    <table width="1480" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                    <tr>
                        <td align="center" class="button_container">
                        	<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
                            <? 
                               echo load_submit_buttons($permission,"fnc_grey_receive_roll_wise",0,0,"fnc_reset()",1);
                            ?>
                            <input type="button" name="print1" id="print1" class="formbutton_disabled" value="Print" style=" width:100px" onClick="fnc_grey_receive_roll_wise(4);">
                            <input type="button" name="btn_fabric_details" id="btn_fabric_details" class="formbutton_disabled" value="Fabric Details" style=" width:100px" onClick="fabric_details();">
                        </td>
                        <td style="display: none;"><?  echo create_drop_down( "cbo_is_sales", 50, $yes_no,"", 1, "--Select--", 0,"",0 ); ?></td>
                    </tr>  
                </table>
                </div>
              </fieldset>  
                  <!-- ========================== Child table end ============================ -->   

    			<div style="width:990px; margin-top:5px" id="list_view_container"></div>

		</form>
	</div>        
</body>  
<script>
	$(document).ready(function() {
		for (var property in mandatory_field_arr) {
			$("#"+mandatory_field_arr[property]).parent().prev('td').css("color", "blue");
		}
	});
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>

<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Finish Fabric Roll Issue Return Roll Wise
				
Functionality	:	
JS Functions	:
Created by		:	Kaiyum
Creation date 	: 	18-3-2017
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
echo load_html_head_contents("Grey Fabric Issue Return Roll Wise","../../", 1, 1, $unicode,'',''); 

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	var scanned_barcode=new Array();  var scanned_barcode_issue =new Array(); var batch_batcode_arr=new Array();var barcode_rollTableId_array=new Array();
	var barcode_trnasId_array =new Array(); var barcode_dtlsId_array=new Array(); var barcode_trnasId_to_array=new Array(); var barcode_return_rollId_array= new Array();
	function openmypage_issue()
	{ 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/finish_roll_issue_return_controller.php?action=issue_popup','Transfer Popup', 'width=780px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var issue_id=this.contentDoc.getElementById("hidden_system_id").value;	 //challan Id and Number
			
			if(issue_id!="")
			{
				fnc_reset_form();
				get_php_form_data(issue_id, "populate_data_from_data", "requires/finish_roll_issue_return_controller");
				/*var barcode_nos=return_global_ajax_value( issue_id, 'barcode_nos', '', 'requires/finish_roll_issue_return_controller');
				if(trim(barcode_nos)!="")
				{
					create_row(1,issue_id);
				}*/
				create_row(1,issue_id);
				set_button_status(1, permission, 'fnc_grey_fabric_issue_return_roll_wise',1);
			}
		}
	}
	
	function set_master_form_data(ref_no,is_barcode)
	{
		var company_id=$('#cbo_company_id').val();
		if(company_id==0)
		{
			get_php_form_data(ref_no+'__'+is_barcode, "populate_master_from_data", "requires/finish_roll_issue_return_controller");
		}
	}
	
	function openmypage_barcode()
	{ 
		var company_id=$('#cbo_company_id').val();
		var issue_id=$('#txt_issue_id').val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/finish_roll_issue_return_controller.php?company_id='+company_id+'&issue_id='+issue_id+'&action=barcode_popup','Barcode Popup', 'width=1050px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var barcode_nos=this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos
			get_php_form_data(barcode_nos+'__'+1, "populate_master_from_data", "requires/finish_roll_issue_return_controller");
			if(barcode_nos!="")
			{
				create_row(0,barcode_nos);
				
				/* var proQtyTotal =0;
				var qcQntyTotal = 0;
				$("#scanning_tbl").find('tbody tr').each(function()
				{
					proQtyTotal+=$(this).find('td:nth-child(18)').html()*1;
					qcQntyTotal+=$(this).find('input[name="currentDelivery[]"]').val()*1;
				 });
				 //alert(qcQntyTotal);
				 $("#total_prodQnty").html(proQtyTotal);*/
				set_all_onclick();
			}
		}
	}
	
	function openmypage_issuechallan()
	{
		var company_id=$('#cbo_company_id').val();
		var issue_id=$('#txt_issue_id').val();

		if($('#update_id').val() != "")
		{
			alert("Multiple issues no not allowed in same challan");
			return;
		}


		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/finish_roll_issue_return_controller.php?company_id='+company_id+'&issue_id='+issue_id+'&action=challan_popup','Challan Popup', 'width=880px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var issue_nos=this.contentDoc.getElementById("hidden_issue_id").value.split(","); //Issue Nos
			$('#cbo_company_id').val(issue_nos[1]);
			$('#cbo_location_name').val(issue_nos[3]);
			$('#store_update_upto').val(issue_nos[4]);

			if(issue_nos[0]!="")
			{
				create_row(2,issue_nos[0]);
				set_all_onclick();

				load_drop_down( 'requires/finish_roll_issue_return_controller', issue_nos[1]+'_'+issue_nos[3], 'load_drop_down_store', 'store_td' );
			}
			$('#txt_issue_id').val(issue_nos[2]);
			$('#txt_issue_challan_no').val(issue_nos[0]);
		}
	}
	
	function generate_report_file(data,action)
	{
		window.open("requires/finish_roll_issue_return_controller.php?data=" + data+'&action='+action, true );
	}

	function fnc_grey_fabric_issue_return_roll_wise( operation )
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_system_no').val()+'*'+$('#update_id').val()+'*'+report_title,'grey_issue_return_print');
			return;
		}
		if(operation==5)
		{
			var update_id=$('#update_id').val();
			if(update_id<1)
			{
				alert("Save Data First.");return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_system_no').val()+'*'+$('#update_id').val()+'*'+report_title,'grey_issue_return_print_grouping');
			return;
		}
		if(form_validation('cbo_company_id*txt_issue_id*cbo_to_store*txt_issue_rtn_date','From Company*Issue No*To Store*Transfer Date')==false)
		{
			return; 
		}
                
                var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_issue_rtn_date').val(), current_date)==false)
		{
			alert("Issue Return Date Can not Be Greater Than Current Date");
			return;
		}
                
		var j=0; var dataString='';var m=1; var validate_stat=0;
		var store_update_upto=$('#store_update_upto').val()*1;
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var floor_id = 0;var room_id=0;var rack_id=0;var shelf_id=0;var bin_id=0;
			if($(this).find('input[name="txtcheck[]"]').attr('checked'))
			{
				//var fromStoreId=$(this).find('input[name="fromStoreId[]"]').val();
				//var toOrderId=$(this).find('input[name="toOrderId[]"]').val();
				
				var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
				var recvBasis=$(this).find('input[name="recvBasis[]"]').val();
				var progBookPiId=$(this).find('input[name="progBookPiId[]"]').val();
				var productId=$(this).find('input[name="productId[]"]').val();
				var orderId=$(this).find('input[name="orderId[]"]').val();
				var BookWithoutOrd=$(this).find('input[name="BookWithoutOrd[]"]').val();
				var rollId=$(this).find('input[name="rollId[]"]').val();
				var rollWgt=$(this).find('input[name="rollWgt[]"]').val();
				var rollNo=$(this).find("td:eq(3)").text();
				
				var yarnLot=$(this).find('input[name="yarnLot[]"]').val();
				var yarnCount=$(this).find('input[name="yarnCount[]"]').val();
				var colorId=$(this).find('input[name="colorId[]"]').val();
				var stichLn=$(this).find('input[name="stichLn[]"]').val();
				var brandId=$(this).find('input[name="brandId[]"]').val();

				floor_id=$(this).find('select[name="cboFloor[]"]').val();
				room_id=$(this).find('select[name="cboRoom[]"]').val();
				rack_id=$(this).find('select[name="txtRack[]"]').val();
				shelf_id=$(this).find('select[name="txtShelf[]"]').val();	
				bin_id=$(this).find('select[name="txtBin[]"]').val();
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

				//alert(store_update_upto + '_' + ' floor_id='+floor_id + 'room_id=' + room_id + 'rack_id=' + rack_id + 'shelf_id=' +shelf_id + 'bin_id=' +bin_id );

				if(store_update_upto==6 && (floor_id==0 || room_id==0 || rack_id==0 || shelf_id==0 || bin_id==0))
				{
					alert("Up To Bin/Box Value Full Fill Required For Inventory");
					validate_stat +=1;
				}
				else if(store_update_upto==5 && (floor_id==0 || room_id==0 || rack_id==0 || shelf_id==0))
				{
					alert("Up To Shelf Value Full Fill Required For Inventory");
					validate_stat +=1;
				}
				else if(store_update_upto==4 && (floor_id==0 || room_id==0 || rack_id==0))
				{
					alert("Up To Rack Value Full Fill Required For Inventory");
					validate_stat +=1;
				}
				else if(store_update_upto==3 && (floor_id==0 || room_id==0))
				{
					alert("Up To Room Value Full Fill Required For Inventory");
					validate_stat +=1;
				}
				else if(store_update_upto==2 && floor_id==0)
				{
					alert("Up To Floor Value Full Fill Required For Inventory");
					validate_stat +=1;
				}


				
				var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
				var transId=$(this).find('input[name="transId[]"]').val();
				var rolltableId=$(this).find('input[name="rolltableId[]"]').val();
				var updateRetRollTableId=$(this).find('input[name="updateRetRollTableId[]"]').val();
				var febDescripId=$(this).find('input[name="febDescripId[]"]').val();
				var machineNoId=$(this).find('input[name="machineNoId[]"]').val();
				var gsm=$(this).find('input[name="prodGsm[]"]').val();
				var diaWidth=$(this).find('input[name="diaWidth[]"]').val();
				var rollRate=$(this).find('input[name="rollRate[]"]').val();
				var rollAmt=$(this).find('input[name="rollAmt[]"]').val();
				var colorRange=$(this).find('input[name="colorRange[]"]').val();
				var bodyPartData=$(this).find('input[name="bodyPartData[]"]').val();
				var batchId=$(this).find('input[name="batchId[]"]').val();
				var txtRemark=$(this).find('input[name="txtRemarks[]"]').val();
	
				j++;
				dataString+='&recvBasis_' + j + '=' + recvBasis + '&barcodeNo_' + j + '=' + barcodeNo + '&progBookPiId_' + j + '=' + progBookPiId + '&productId_' + j + '=' + productId + '&orderId_' + j + '=' + orderId+ '&BookWithoutOrd_' + j + '=' + BookWithoutOrd + '&rollId_' + j + '=' + rollId + '&rollWgt_' + j + '=' + rollWgt + '&yarnLot_' + j + '=' + yarnLot + '&yarnCount_' + j + '=' + yarnCount + '&colorId_' + j + '=' + colorId + '&stichLn_' + j + '=' + stichLn + '&brandId_' + j + '=' + brandId + '&dtlsId_' + j + '=' + dtlsId + '&transId_' + j + '=' + transId + '&rolltableId_' + j + '=' + rolltableId + '&rollNo_' + j + '=' + rollNo+ '&febDescripId_' + j + '=' + febDescripId+ '&machineNoId_' + j + '=' + machineNoId+ '&gsm_' + j + '=' + gsm+ '&diaWidth_' + j + '=' + diaWidth+ '&rollRate_' + j + '=' + rollRate+ '&rollAmt_' + j + '=' + rollAmt+ '&colorRange_' + j + '=' + colorRange + '&floorId_' + j + '=' + floor_id + '&roomId_' + j + '=' + room_id + '&rack_' + j + '=' + rack_id + '&shelf_' + j + '=' + shelf_id + '&bin_' + j + '=' + bin_id + '&bodyPartData_' + j + '=' + bodyPartData + '&batchId_' + j + '=' + batchId + '&txtRemark_' + j + '=' + txtRemark + '&updateRetRollTableId_' + j + '=' + updateRetRollTableId;
			}
			
		});
		
		if(j<1)
		{
			alert('No data');
			return;
		}

		if(validate_stat >0)
		{
			return;
		}
		
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_system_no*update_id*txt_issue_id*cbo_company_id*cbo_to_store*txt_challan_no*txt_issue_rtn_date',"../../")+dataString;
		//alert(data);return;
		
		freeze_window(operation);
		
		http.open("POST","requires/finish_roll_issue_return_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_grey_fabric_issue_roll_wise_Reply_info;
	}

	function fnc_grey_fabric_issue_roll_wise_Reply_info()
	{
		if(http.readyState == 4) 
		{
			//release_freezing();return;
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
			
			if((response[0]==0 || response[0]==1))
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_system_no').value = response[2];
				$('#cbo_transfer_criteria').attr('disabled',true);
				$('#cbo_company_id').attr('disabled',true);
				$('#cbo_to_company_id').attr('disabled',true);
				$('#txt_deleted_id').val( '' );
				add_dtls_data(response[3]);
				set_button_status(1, permission, 'fnc_grey_fabric_issue_return_roll_wise',1);
				$("#print_barcode").removeClass('formbutton_disabled');
				$("#print_barcode").addClass('formbutton');
				$("#btn_fabric_details").removeClass('formbutton_disabled');
			    $("#btn_fabric_details").addClass('formbutton');
				$("#btn_mc").removeClass('formbutton_disabled');
			    $("#btn_mc").addClass('formbutton');
			}
			else if(response[0]==20 )
			{
				alert(response[1]);
				release_freezing();
				return;
			}
			release_freezing();
		}
	}
	
	
	//var scanned_barcode=new Array();
	function create_row(is_update,barcode_no)
	{
		var row_num=$('#txt_tot_row').val();
		var bar_code=trim(barcode_no);
		var num_row =$('#scanning_tbl tbody tr').length; 

		var cbo_company_id = $('#cbo_company_id').val();
		var system_id=$('#update_id').val();
		if(is_update==0)
		{
			
			var barcode_data=return_global_ajax_value( bar_code+'_'+$("#txt_issue_id").val(), 'populate_barcode_data', '', 'requires/finish_roll_issue_return_controller');
			//alert( barcode_data); return;
			var barcode_data_all=new Array(); var barcode_data_ref=new Array();
			barcode_data_ref=barcode_data.split("__");
			//alert(barcode_data_ref[0]);return;
			var total_roll_wgt=0;
			for(var k=0;k<barcode_data_ref.length;k++)
			{
				barcode_data_all=barcode_data_ref[k].split("**");
				var rcv_id=barcode_data_all[0];var company_id=barcode_data_all[1];var body_part_name=barcode_data_all[2];var receive_basis=barcode_data_all[3];
				var receive_basis_id=barcode_data_all[4];var receive_date=barcode_data_all[5];var booking_no=barcode_data_all[6];var booking_id=barcode_data_all[7];
				var color=barcode_data_all[8];var knitting_source_id=barcode_data_all[9];var knitting_source=barcode_data_all[10];var store_id=barcode_data_all[11];
				var knitting_company_id=barcode_data_all[12];var yarn_lot=barcode_data_all[13];var yarn_count=barcode_data_all[14];var stitch_length=barcode_data_all[15];
				var rack=barcode_data_all[16];var self=barcode_data_all[17];var knitting_company_name=barcode_data_all[18];var dtls_id=barcode_data_all[19];
				var prod_id=barcode_data_all[20];var febric_description_id=barcode_data_all[21];var compsition_description=barcode_data_all[22];var gsm=barcode_data_all[23];
				var width=barcode_data_all[24];var roll_id=barcode_data_all[25];var roll_no=barcode_data_all[26];var po_breakdown_id=barcode_data_all[27];
				var qnty=barcode_data_all[28];var barcode_no=barcode_data_all[29];var job_no=barcode_data_all[30];var buyer_id=barcode_data_all[31];
				var buyer_name=barcode_data_all[32];var po_number=barcode_data_all[33];var file_no=barcode_data_all[34]; var color_id=barcode_data_all[35];
				var store_name=barcode_data_all[36];var bordy_part_id=barcode_data_all[37];var brand_id=barcode_data_all[38];var machine_name=barcode_data_all[39];
				var machine_no_id=barcode_data_all[40];var entry_form=barcode_data_all[41];var issue_roll_tbl_id=barcode_data_all[42]; var roll_rate=barcode_data_all[43];
				var roll_amt=barcode_data_all[44];var color_range_id=barcode_data_all[45];var booking_without_order=barcode_data_all[46];var batch_id=barcode_data_all[47];
				//alert(barcode_data_all[0]);
				//alert(batch_id);//return;

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
				if(barcode_data_all[0]==-1)
				{
					alert('Barcode is Already Receive By Cutting');
					$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
					 {
						$('#messagebox_main', window.parent.document).html('Barcode is Already Receive By Batch.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
					 });
					 $('#txt_issue_challan_no').val('');
					return; 
				}
			

				if( jQuery.inArray( barcode_no, scanned_barcode )>-1) 
				{ 
					alert('Sorry! Barcode Already Scanned.'); 
					$('#txt_bar_code_num').val('');
					return; 
				}
				
				//alert(barcode_data);return;

				var onchange_value="";
				var bar_code_no=$('#barcodeNo_'+row_num).val();
				if(bar_code_no!="")
				{
					row_num++;
					$("#scanning_tbl tbody tr:first").clone().find("td,input,select").each(function()
					{
						onchange_value="";
						$(this).attr({ 
						  'id': function(_, id) { 
						  	var id=id.split("_"); 
						  	
						  	var check_id=id[0];
						  	if(check_id=="cboFloor")
					  		{
					  			 
					  			onchange_value="fn_load_room(this.value, "+row_num+");copy_all(\'"+row_num+"_0\');reset_room_rack_shelf("+row_num+",\'cbo_floor_to\')";
					  		}
					  		else if(check_id=="cboRoom")
					  		{
					  			onchange_value="fn_load_rack(this.value, "+row_num+");copy_all(\'"+row_num+"_1\');reset_room_rack_shelf("+row_num+",\'cbo_room_to\')";
					  		}
					  		else if(check_id=="txtRack")
					  		{
					  			onchange_value="fn_load_shelf(this.value, "+row_num+");copy_all(\'"+row_num+"_2\');reset_room_rack_shelf("+row_num+",\'txt_rack_to\')";
					  		}
					  		else if(check_id=="txtShelf")
					  		{
					  			onchange_value="fn_load_bin(this.value, "+row_num+");copy_all(\'"+row_num+"_3\');reset_room_rack_shelf("+row_num+",\'txt_shelf_to\')";
					  		}
					  		else if(check_id=="txtBin")
					  		{
					  			onchange_value="copy_all(\'"+row_num+"_4\')";
					  		}

						  	return id[0] +"_"+ row_num 
						  },
						  'value': function(_, value) { return value },
						  'onchange': function(){
						  	return onchange_value;
						  }             
						});
					}).end().prependTo("#scanning_tbl");
					$("#scanning_tbl tbody tr:first").find('td,input').removeAttr('onchange');
					$("#scanning_tbl tbody tr:first").removeAttr('id').attr('id','tr_'+row_num);//decrease_1
					$("#scanning_tbl tbody tr:first").find(':input:not(:button)','select').val("");
					$("#scanning_tbl tbody tr:first").find(':input(:checkbox)').attr('checked',false).attr('disabled',false);
					
				}
				
				scanned_barcode.push(barcode_no);
				//batch_batcode_arr.push(barcode_no);
				
				$("#sl_"+row_num).text(row_num);
				$("#barcode_"+row_num).text(barcode_no);
				$("#roll_"+row_num).text(roll_no);
				$("#bodyPart_"+row_num).text(body_part_name);
				$("#cons_"+row_num).text(compsition_description);
				$("#gsm_"+row_num).text(gsm);
				$("#dia_"+row_num).text(width);
				$("#color_"+row_num).text(color);
				//$("#diaType_"+row_num).text('');machine_name
				$("#rollWeight_"+row_num).text(qnty);
				$("#buyer_"+row_num).text(buyer_name);
				$("#job_"+row_num).text(job_no);
				$("#order_"+row_num).text(po_number);
				$("#file_"+row_num).text(file_no);
				$("#knitCompany_"+row_num).text(knitting_company_name);
				$("#machine_"+row_num).text(machine_name);
				$("#progBookPiNo_"+row_num).text(booking_no);
				$("#prodBasis_"+row_num).text(receive_basis);
				
				//alert(total_roll_wgt);
				
				
				$("#barcodeNo_"+row_num).val(barcode_no);
				$("#recvBasis_"+row_num).val(receive_basis_id);
				$("#progBookPiId_"+row_num).val(booking_id);
				$("#productId_"+row_num).val(prod_id);
				$("#orderId_"+row_num).val(po_breakdown_id);
				$("#BookWithoutOrd_"+row_num).val(booking_without_order);
				$("#rollId_"+row_num).val(roll_id);
				$("#rollWgt_"+row_num).val(qnty);
				$("#yarnLot_"+row_num).val(yarn_lot);
				$("#yarnCount_"+row_num).val(yarn_count);
				$("#colorId_"+row_num).val(color_id);
				$("#stichLn_"+row_num).val(stitch_length);
				$("#brandId_"+row_num).val(brand_id);
				$("#rack_"+row_num).val(rack);
				$("#shelf_"+row_num).val(self);
				$("#febDescripId_"+row_num).val(febric_description_id);
				$("#machineNoId_"+row_num).val(machine_no_id);
				$("#prodGsm_"+row_num).val(gsm);
				$("#diaWidth_"+row_num).val(width);
				$("#rolltableId_"+row_num).val(issue_roll_tbl_id);
				$("#rollRate_"+row_num).val(roll_rate);
				$("#rollAmt_"+row_num).val(roll_amt);
				$("#colorRange_"+row_num).val(color_range_id);
				$("#bodyPartData_"+row_num).val(bordy_part_id);
				$("#batchId_"+row_num).val(batch_id);
				
			}
			
			$('#txt_tot_row').val(row_num);
			//$('#total_rollwgt').val(total_roll_wgt);
			$('#txt_bar_code_num').val('');
			$('#txt_bar_code_num').focus();
			
			//barcode_data_all=barcode_data.split("**");
			//alert(total_roll_wgt);//return;
			
		}
		else if(is_update==2)
		{
			if($("#txt_issue_id").val() != "")
			{
				alert("Multiple issues no not allowed in same challan");
				return;
			}

			var barcode_data=return_global_ajax_value( bar_code+'_'+$("#txt_issue_id").val(), 'populate_issue_data', '', 'requires/finish_roll_issue_return_controller');
			//alert( barcode_data); return;
			var barcode_data_all=new Array(); var barcode_data_ref=new Array();
			barcode_data_ref=barcode_data.split("__");
			//alert(barcode_data_ref[0]);return;
			for(var k=0;k<barcode_data_ref.length;k++)
			{
				barcode_data_all=barcode_data_ref[k].split("**");
				var rcv_id=barcode_data_all[0];var company_id=barcode_data_all[1];var body_part_name=barcode_data_all[2];var receive_basis=barcode_data_all[3];
				var receive_basis_id=barcode_data_all[4];var receive_date=barcode_data_all[5];var booking_no=barcode_data_all[6];var booking_id=barcode_data_all[7];
				var color=barcode_data_all[8];var knitting_source_id=barcode_data_all[9];var knitting_source=barcode_data_all[10];var store_id=barcode_data_all[11];
				var knitting_company_id=barcode_data_all[12];var yarn_lot=barcode_data_all[13];var yarn_count=barcode_data_all[14];var stitch_length=barcode_data_all[15];
				var rack=barcode_data_all[16];var self=barcode_data_all[17];var knitting_company_name=barcode_data_all[18];var dtls_id=barcode_data_all[19];
				var prod_id=barcode_data_all[20];var febric_description_id=barcode_data_all[21];var compsition_description=barcode_data_all[22];var gsm=barcode_data_all[23];
				var width=barcode_data_all[24];var roll_id=barcode_data_all[25];var roll_no=barcode_data_all[26];var po_breakdown_id=barcode_data_all[27];
				var qnty=barcode_data_all[28];var barcode_no=barcode_data_all[29];var job_no=barcode_data_all[30];var buyer_id=barcode_data_all[31];
				var buyer_name=barcode_data_all[32];var po_number=barcode_data_all[33];var file_no=barcode_data_all[34]; var color_id=barcode_data_all[35];
				var store_name=barcode_data_all[36];var bordy_part_id=barcode_data_all[37];var brand_id=barcode_data_all[38];var machine_name=barcode_data_all[39];
				var machine_no_id=barcode_data_all[40];var entry_form=barcode_data_all[41];var issue_roll_tbl_id=barcode_data_all[42];var roll_rate=barcode_data_all[43];
				var roll_amt=barcode_data_all[44];var color_range_id=barcode_data_all[45];var booking_without_order=barcode_data_all[46];var batch_id=barcode_data_all[47];
				//alert(rcv_id);
				
				
				//alert(barcode_data_all[0]);
				
				if(barcode_data_all[0]==0)
				{
					alert('Barcode is Not Valid');
					$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
					 {
						$('#messagebox_main', window.parent.document).html('Barcode is Not Valid.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
					 });
					 $('#txt_issue_challan_no').val('');
					return; 
				}
				
				if(barcode_data_all[0]==-1)
				{
					alert('Barcode is Already Receive By Batch');
					$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
					 {
						$('#messagebox_main', window.parent.document).html('Barcode is Already Receive By Batch.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
					 });
					 $('#txt_issue_challan_no').val('');
					return; 
				}

				if( jQuery.inArray( barcode_no, scanned_barcode )>-1) 
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
						onchange_value=""
						$(this).attr({ 
						  	'id': function(_, id) 
						  	{ 
						  	var id=id.split("_"); 
						  	var check_id=id[0];
						  	if(check_id=="cboFloor")
					  		{
					  			 
					  			onchange_value="fn_load_room(this.value, "+row_num+");copy_all(\'"+row_num+"_0\');reset_room_rack_shelf("+row_num+",\'cbo_floor_to\')";
					  		}
					  		else if(check_id=="cboRoom")
					  		{
					  			onchange_value="fn_load_rack(this.value, "+row_num+");copy_all(\'"+row_num+"_1\');reset_room_rack_shelf("+row_num+",\'cbo_room_to\')";
					  		}
					  		else if(check_id=="txtRack")
					  		{
					  			onchange_value="fn_load_shelf(this.value, "+row_num+");copy_all(\'"+row_num+"_2\');reset_room_rack_shelf("+row_num+",\'txt_rack_to\')";
					  		}
					  		else if(check_id=="txtShelf")
					  		{
					  			onchange_value="fn_load_bin(this.value, "+row_num+");copy_all(\'"+row_num+"_3\');reset_room_rack_shelf("+row_num+",\'txt_shelf_to\')";
					  		}
					  		else if(check_id=="txtBin")
					  		{
					  			onchange_value="copy_all(\'"+row_num+"_4\')";
					  		}

						  	return id[0] +"_"+ row_num 
							},
						  'value': function(_, value) { return value },
						  'onchange': function(){
						  	return onchange_value;
						  }              
						});
					}).end().prependTo("#scanning_tbl");
					$("#scanning_tbl tbody tr:first").find('td,input').removeAttr('onchange');
					$("#scanning_tbl tbody tr:first").removeAttr('id').attr('id','tr_'+row_num);//decrease_1
					$("#scanning_tbl tbody tr:first").find(':input:not(:button)','select').val("");
					$("#scanning_tbl tbody tr:first").find(':input(:checkbox)').attr('checked',false).attr('disabled',false);
					
				}
				
				scanned_barcode.push(barcode_no);
				//batch_batcode_arr.push(barcode_no);
				
				$("#sl_"+row_num).text(row_num);
				$("#barcode_"+row_num).text(barcode_no);
				$("#roll_"+row_num).text(roll_no);
				$("#bodyPart_"+row_num).text(body_part_name);
				$("#cons_"+row_num).text(compsition_description);
				$("#gsm_"+row_num).text(gsm);
				$("#dia_"+row_num).text(width);
				$("#color_"+row_num).text(color);
				//$("#diaType_"+row_num).text('');machine_name
				$("#rollWeight_"+row_num).text(qnty);
				$("#buyer_"+row_num).text(buyer_name);
				$("#job_"+row_num).text(job_no);
				$("#order_"+row_num).text(po_number);
				$("#file_"+row_num).text(file_no);
				$("#knitCompany_"+row_num).text(knitting_company_name);
				$("#machine_"+row_num).text(machine_name);
				$("#progBookPiNo_"+row_num).text(booking_no);
				$("#prodBasis_"+row_num).text(receive_basis);
				
				
				
				
				$("#barcodeNo_"+row_num).val(barcode_no);
				$("#recvBasis_"+row_num).val(receive_basis_id);
				$("#progBookPiId_"+row_num).val(booking_id);
				$("#productId_"+row_num).val(prod_id);
				$("#orderId_"+row_num).val(po_breakdown_id);
				$("#BookWithoutOrd_"+row_num).val(booking_without_order);
				$("#rollId_"+row_num).val(roll_id);
				$("#rollWgt_"+row_num).val(qnty);
				$("#yarnLot_"+row_num).val(yarn_lot);
				$("#yarnCount_"+row_num).val(yarn_count);
				$("#colorId_"+row_num).val(color_id);
				$("#stichLn_"+row_num).val(stitch_length);
				$("#brandId_"+row_num).val(brand_id);
				$("#rack_"+row_num).val(rack);
				$("#shelf_"+row_num).val(self);
				$("#febDescripId_"+row_num).val(febric_description_id);
				$("#machineNoId_"+row_num).val(machine_no_id);
				$("#prodGsm_"+row_num).val(gsm);
				$("#diaWidth_"+row_num).val(width);
				$("#rolltableId_"+row_num).val(issue_roll_tbl_id);
				$("#rollRate_"+row_num).val(roll_rate);
				$("#rollAmt_"+row_num).val(roll_amt);
				$("#colorRange_"+row_num).val(color_range_id);
				$("#bodyPartData_"+row_num).val(bordy_part_id);
				$("#batchId_"+row_num).val(batch_id);
			}
			
			$('#txt_tot_row').val(row_num);
			$('#txt_bar_code_num').val('');
			$('#txt_bar_code_num').focus();
			//barcode_data_all=barcode_data.split("**");
			//alert(barcode_data);return;
		}
		else
		{
			//var barcode_data=return_global_ajax_value( bar_code+'**'+system_id, 'populate_barcode_data_update', '', 'requires/finish_roll_issue_return_controller');
			var barcode_data=return_global_ajax_value(bar_code, 'populate_barcode_data_update', '', 'requires/finish_roll_issue_return_controller');
			//alert(barcode_data);return;
			var barcode_data_all=new Array(); var barcode_data_ref=new Array();
			barcode_data_ref=barcode_data.split("__");
			//alert(barcode_data_ref[0]);return;
			for(var k=0;k<barcode_data_ref.length;k++)
			{
				barcode_data_all=barcode_data_ref[k].split("**");
				var rcv_id=barcode_data_all[0];var company_id=barcode_data_all[1];var body_part_name=barcode_data_all[2];var receive_basis=barcode_data_all[3];
				var receive_basis_id=barcode_data_all[4];var receive_date=barcode_data_all[5];var booking_no=barcode_data_all[6];var booking_id=barcode_data_all[7];
				var color=barcode_data_all[8];var knitting_source_id=barcode_data_all[9];var knitting_source=barcode_data_all[10];var store_id=barcode_data_all[11];
				var knitting_company_id=barcode_data_all[12];var yarn_lot=barcode_data_all[13];var yarn_count=barcode_data_all[14];var stitch_length=barcode_data_all[15];
				var rack=barcode_data_all[16];var self=barcode_data_all[17];var knitting_company_name=barcode_data_all[18];var dtls_id=barcode_data_all[19];
				var prod_id=barcode_data_all[20];var febric_description_id=barcode_data_all[21];var compsition_description=barcode_data_all[22];var gsm=barcode_data_all[23];
				var width=barcode_data_all[24];var roll_id=barcode_data_all[25];var roll_no=barcode_data_all[26];var po_breakdown_id=barcode_data_all[27];
				var qnty=barcode_data_all[28];var barcode_no=barcode_data_all[29];var job_no=barcode_data_all[30];var buyer_id=barcode_data_all[31];
				var buyer_name=barcode_data_all[32];var po_number=barcode_data_all[33];var file_no=barcode_data_all[34]; var color_id=barcode_data_all[35];
				var store_name=barcode_data_all[36];var bordy_part_id=barcode_data_all[37];var brand_id=barcode_data_all[38];var machine_name=barcode_data_all[39];
				var machine_no_id=barcode_data_all[40];var entry_form=barcode_data_all[41];var issue_roll_tbl_id=barcode_data_all[42]; var roll_rate=barcode_data_all[43];
				var roll_amt=barcode_data_all[44];var color_range_id=barcode_data_all[45]; var update_dtls_id=barcode_data_all[46]; var booking_without_order=barcode_data_all[47];


				var floor_id=barcode_data_all[48];
				var room_id=barcode_data_all[49];
				var bin_box=barcode_data_all[50];

				var batch_id=barcode_data_all[51];
				var remarks=barcode_data_all[52];
				var updateRetRollTableId=barcode_data_all[53];
				var updateTransTableId=barcode_data_all[54];
				
				//alert(rcv_id);
				/*if(barcode_data_all[0]==0)
				{
					alert('Barcode is Not Valid');
					$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
					 {
						$('#messagebox_main', window.parent.document).html('Barcode is Not Valid.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
					 });
					 $('#txt_bar_code_num').val('');
					return; 
				}
				if(barcode_data_all[0]==-1)
				{
					alert('Barcode is Already Receive By Batch');
					$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
					 {
						$('#messagebox_main', window.parent.document).html('Barcode is Already Receive By Batch.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
					 });
					 $('#txt_issue_challan_no').val('');
					return; 
				}
				
				if( jQuery.inArray( barcode_no, scanned_barcode )>-1) 
				{ 
					alert('Sorry! Barcode Already Scanned.'); 
					$('#txt_bar_code_num').val('');
					return; 
				}*/
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
					$("#scanning_tbl tbody tr:first").find('td,input').removeAttr('onchange');
					$("#scanning_tbl tbody tr:first").removeAttr('id').attr('id','tr_'+row_num);//decrease_1
					$("#scanning_tbl tbody tr:first").find(':input:not(:button)','select').val("");
					$("#scanning_tbl tbody tr:first").find(':input(:checkbox)').attr('checked',false).attr('disabled',false);
					
				}
				
				scanned_barcode.push(barcode_no);
				//batch_batcode_arr.push(barcode_no);
				
				$("#sl_"+row_num).text(row_num);
				$("#barcode_"+row_num).text(barcode_no);
				$("#roll_"+row_num).text(roll_no);
				$("#bodyPart_"+row_num).text(body_part_name);
				$("#cons_"+row_num).text(compsition_description);
				$("#gsm_"+row_num).text(gsm);
				$("#dia_"+row_num).text(width);
				$("#color_"+row_num).text(color);
				//$("#diaType_"+row_num).text('');machine_name
				$("#rollWeight_"+row_num).text(qnty);
				$("#buyer_"+row_num).text(buyer_name);
				$("#job_"+row_num).text(job_no);
				$("#order_"+row_num).text(po_number);
				$("#file_"+row_num).text(file_no);
				$("#knitCompany_"+row_num).text(knitting_company_name);
				$("#machine_"+row_num).text(machine_name);
				$("#progBookPiNo_"+row_num).text(booking_no);
				$("#prodBasis_"+row_num).text(receive_basis);
								
				$("#barcodeNo_"+row_num).val(barcode_no);
				$("#recvBasis_"+row_num).val(receive_basis_id);
				$("#progBookPiId_"+row_num).val(booking_id);
				$("#productId_"+row_num).val(prod_id);
				$("#orderId_"+row_num).val(po_breakdown_id);
				$("#BookWithoutOrd_"+row_num).val(booking_without_order);
				$("#rollId_"+row_num).val(roll_id);
				$("#rollWgt_"+row_num).val(qnty);
				$("#yarnLot_"+row_num).val(yarn_lot);
				$("#yarnCount_"+row_num).val(yarn_count);
				$("#colorId_"+row_num).val(color_id);
				$("#stichLn_"+row_num).val(stitch_length);
				$("#brandId_"+row_num).val(brand_id);
				$("#rack_"+row_num).val(rack);
				$("#shelf_"+row_num).val(self);
				$("#febDescripId_"+row_num).val(febric_description_id);
				$("#machineNoId_"+row_num).val(machine_no_id);
				$("#prodGsm_"+row_num).val(gsm);
				$("#diaWidth_"+row_num).val(width);
				$("#rolltableId_"+row_num).val(issue_roll_tbl_id);
				$("#rollRate_"+row_num).val(roll_rate);
				$("#rollAmt_"+row_num).val(roll_amt);
				$("#colorRange_"+row_num).val(color_range_id);
				$("#bodyPartData_"+row_num).val(bordy_part_id);
				$("#batchId_"+row_num).val(batch_id);
				$("#dtlsId_"+row_num).val(update_dtls_id);
				$("#transId_"+row_num).val(updateTransTableId);
				$("#updateRetRollTableId_"+row_num).val(updateRetRollTableId);

				if (floor_id!=0) 
				{
					change_floor(floor_id,"cboFloor_"+row_num);
				}
				if (room_id!=0) 
				{
					change_room(room_id,"cboRoom_"+row_num);
				}
				if (rack!=0) 
				{
					change_rack(rack,"txtRack_"+row_num);
				}
				if (self!=0) 
				{
					change_shelf(self,"txtShelf_"+row_num);
				}

				$("#cboFloor_"+row_num).removeAttr('onchange').attr("onchange","fn_load_room(this.value,"+row_num+"); copy_all('"+row_num+"_0'); reset_room_rack_shelf("+row_num+",'cbo_floor_to');"); 
				$("#cboRoom_"+row_num).removeAttr('onchange').attr("onchange","fn_load_rack(this.value,"+row_num+"); copy_all('"+row_num+"_1'); reset_room_rack_shelf("+row_num+",'cbo_room_to');"); 
				$("#txtRack_"+row_num).removeAttr('onchange').attr("onchange","fn_load_shelf(this.value,"+row_num+"); copy_all('"+row_num+"_2'); reset_room_rack_shelf("+row_num+",'txt_rack_to');"); 
				$("#txtShelf_"+row_num).removeAttr('onchange').attr("onchange","fn_load_bin(this.value,"+row_num+"); copy_all('"+row_num+"_3');"); 
				$("#txtBin_"+row_num).removeAttr('onchange').attr("onchange","copy_all('"+row_num+"_4');"); 

				//alert('floor_id='+floor_id+'room_id='+room_id+'rack='+rack+'self='+self+'bin_box='+bin_box);
				$("#cboFloor_"+row_num).val(floor_id).attr('disabled',true);
				$("#cboRoom_"+row_num).val(room_id).attr('disabled',true);
				$("#txtRack_"+row_num).val(rack).attr('disabled',true);
				$("#txtShelf_"+row_num).val(self).attr('disabled',true);				
				$("#txtBin_"+row_num).val(bin_box).attr('disabled',true);	
				$("#txtRemark_"+row_num).val(remarks);

				
				$("#txtcheck_"+row_num).attr('checked',true);
				$("#txtcheck_"+row_num).attr('disabled',true);
			}
			
			$('#txt_tot_row').val(row_num);
			$('#txt_bar_code_num').val('');
			$('#txt_bar_code_num').focus();
			
			
			
		}
		//alert(up_roll_id);
		calculate_total();
	}
	
	function calculate_total()
	{
		var total_roll_weight='';
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var rollWgt=$(this).find('input[name="rollWgt[]"]').val();
				//alert(rollWgt);
			total_roll_weight=total_roll_weight*1+rollWgt*1;
		});
	
		$("#total_rollwgt").text(total_roll_weight.toFixed(2));	
		//$("#total_rollwgt").text(total_roll_wgt);
	}
	
	$('#txt_bar_code_num').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			e.preventDefault();
			var bar_code=$('#txt_bar_code_num').val();
			create_row(0,bar_code);
			set_master_form_data(bar_code,1);
		}
	});
	
	$('#txt_issue_challan_no').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			e.preventDefault();
			var issue_no=$('#txt_issue_challan_no').val();
			create_row(2,issue_no);
			set_master_form_data(issue_no,2);
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
			var return_roll_table_id=datas[2];
			var trans_table_id=datas[3];
			
			barcode_dtlsId_array[barcode_no] = dtls_id;
			barcode_return_rollId_array[barcode_no] = return_roll_table_id;
			barcode_trnasId_to_array[barcode_no] = trans_table_id;
		}
		
		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
			var dtlsId=$(this).find('input[name="dtlsId[]"]').val();
			
			if(dtlsId=="") 
			{
				$(this).find('input[name="dtlsId[]"]').val(barcode_dtlsId_array[barcodeNo]);
				$(this).find('input[name="updateRetRollTableId[]"]').val(barcode_return_rollId_array[barcodeNo]);
				$(this).find('input[name="transId[]"]').val(barcode_trnasId_to_array[barcodeNo]);
			}
			if($(this).find('input[name="txtcheck[]"]').attr('checked'))
			{
				$(this).find('input[name="txtcheck[]"]').attr('disabled',true);
			}
			
		});
	}
	
	function fnc_reset_form()
	{
		$('#scanning_tbl tbody tr').remove();
						
		var html='<tr id="tr_1" align="center" valign="middle"><td id="button_1" align="center" width="40"><input type="checkbox" id="txtcheck_1" name="txtcheck[]" /><input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/><input type="hidden" name="recvBasis[]" id="recvBasis_1"/><input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/><input type="hidden" name="productId[]" id="productId_1"/><input type="hidden" name="orderId[]" id="orderId_1"/><input type="hidden" name="BookWithoutOrd[]" id="BookWithoutOrd_1"/><input type="hidden" name="rollId[]" id="rollId_1"/><input type="hidden" name="rollWgt[]" id="rollWgt_1"/><input type="hidden" name="yarnLot[]" id="yarnLot_1"/><input type="hidden" name="yarnCount[]" id="yarnCount_1"/><input type="hidden" name="colorId[]" id="colorId_1"/><input type="hidden" name="stichLn[]" id="stichLn_1"/><input type="hidden" name="brandId[]" id="brandId_1"/><input type="hidden" name="rack[]" id="rack_1"/><input type="hidden" name="shelf[]" id="shelf_1"/><input type="hidden" name="dtlsId[]" id="dtlsId_1"/><input type="hidden" name="transId[]" id="transId_1"/><input type="hidden" name="rolltableId[]" id="rolltableId_1"/><input type="hidden" name="febDescripId[]" id="febDescripId_1"/><input type="hidden" name="machineNoId[]" id="machineNoId_1"/><input type="hidden" name="prodGsm[]" id="prodGsm_1"/><input type="hidden" name="diaWidth[]" id="diaWidth_1"/><input type="hidden" name="rollRate[]" id="rollRate_1"/><input type="hidden" name="rollAmt[]" id="rollAmt_1"/><input type="hidden" name="colorRange[]" id="colorRange_1"/></td><input type="hidden" name="bodyPartData[]" id="bodyPartData_1"/></td><input type="hidden" name="batchId[]" id="batchId_1"/><input type="hidden" name="updateRetRollTableId[]" id="updateRetRollTableId_1"/></td><td width="30" id="sl_1"></td><td width="70" id="barcode_1"></td><td width="45" id="roll_1"></td><td style="word-break:break-all;" width="80" id="bodyPart_1"></td><td style="word-break:break-all;" width="150" id="cons_1"></td><td style="word-break:break-all;" width="40" id="gsm_1"></td><td style="word-break:break-all;" width="40" id="dia_1"></td><td style="word-break:break-all;" width="70" id="color_1"></td><td width="60" id="rollWeight_1" align="right"></td><td style="word-break:break-all;" width="60" id="buyer_1"></td><td style="word-break:break-all;" width="80" id="job_1"></td><td style="word-break:break-all;" width="100" id="order_1" align="left"><td style="word-break:break-all;" width="80" id="file_1" align="left"></td><td style="word-break:break-all;" width="110" id="knitCompany_1"></td><td style="word-break:break-all;" width="70" id="machine_1"></td><td width="90" id="progBookPiNo_1"></td>  <td width="70" align="center" id="floorTd_1" class="floor_td_to"><? echo create_drop_down( "cboFloor_1", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_room(this.value, 1); copy_all(\'1_0\'); reset_room_rack_shelf(1,\'cbo_floor_to\');",0,"","","","","","","cboFloor[]" ,"onchange_void"); ?></td><td width="70" align="center" id="roomTd_1"><? echo create_drop_down( "cboRoom_1", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_rack(this.value, 1);copy_all(\'1_1\');reset_room_rack_shelf(1,\'cbo_room_to\');",0,"","","","","","","cboRoom[]","onchange_void" ); ?></td><td width="70" align="center" id="rackTd_1"><? echo create_drop_down( "txtRack_1", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_shelf(this.value, 1);copy_all(\'1_2\');reset_room_rack_shelf(1,\'txt_rack_to\');",0,"","","","","","","txtRack[]","onchange_void" ); ?></td><td width="70" align="center" id="shelfTd_1"><? echo create_drop_down( "txtShelf_1", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_bin(this.value, 1);copy_all(\'1_3\');reset_room_rack_shelf(1,\'txt_shelf_to\');",0,"","","","","","","txtShelf[]","onchange_void" ); ?></td><td width="70" align="center" id="binTd_1"><? echo create_drop_down( "txtBin_1", 50,$blank_array,"", 1, "--Select--", 0, "copy_all(\'1_4\');",0,"","","","","","","txtBin[]","onchange_void" ); ?></td>  <td style="word-break:break-all;" width="100" id="prodBasis_1"></td><td width="70" align="center" id="remarksTd_1"><input type="text" class="text_boxes" style="width: 80px;" id="txtRemark_1" name="txtRemarks[]"></td></tr>';

		

		
		$('#txt_system_no').val('');
		$('#update_id').val('');
		$('#txt_bar_code_num').val('');
		$('#txt_issue_challan_no').val('');
		$('#cbo_company_id').val(0);
		$('#cbo_company_id').attr('disabled',true);
		$('#txt_tot_row').val(1);
		
		$('#txt_issue_rtn_date').val('');
		$('#txt_challan_no').val('');
		$('#cbo_to_store').val(0);
		$('#txt_deleted_id').val('');
		$("#scanning_tbl tbody").html(html);	
	}
	
	
	function check_all()
	{
		if($('#txt_check_all').attr('checked'))
		{
			$('#scanning_tbl tbody').find('tr').each(function(index, element) {
				$('input:checkbox:not(:disabled)').attr('checked','checked');
            });
		}
		else
		{
			$('#scanning_tbl tbody').find('tr').each(function(index, element) {
				$('input:checkbox:not(:disabled)').removeAttr('checked');
            });
		}
	}
	
	function fn_load_floor(store_id)
	{
		// alert(store_id);return;
		var com_id=$('#cbo_company_id').val();
		var location_id=$('#cbo_location_name').val();
		var all_data=com_id + "__" + store_id + "__" + location_id;
		//alert(all_data);return;
		var floor_result = return_global_ajax_value(all_data, 'floor_list', '', 'requires/finish_roll_issue_return_controller');
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
		}
	}

	function fn_load_room(floor_id, sequenceNo)
	{
		// alert(floor_id);return;
		console.log(floor_id);
		var com_id=$('#cbo_company_id').val();
		var location_id=$('#cbo_location_name').val();
		var store_id=$('#cbo_to_store').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
		//alert(all_data);return;
		//console.log(all_data);
		var room_result = return_global_ajax_value(all_data, 'room_list', '', 'requires/finish_roll_issue_return_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		
		var JSONObject = JSON.parse(room_result);

		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var roomId = $(this).find('select[name="cboRoom[]"]').attr("id");
			var roomIdSlArr = roomId.split("_");
			// copy only that and below selected data
			if( sequenceNo >= roomIdSlArr[1]*1 )
			{

				if(document.getElementById('txtcheck_'+roomIdSlArr[1]*1).disabled)
				{

				}
				else
				{
					$(this).find('select[name="cboRoom[]"]').html('<option value="'+0+'">Select</option>');
					for (var key of Object.keys(JSONObject).sort())
					{
						$(this).find('select[name="cboRoom[]"]').append('<option value="'+key+'">'+JSONObject[key]+'</option>');
					};
				}


				
			}
		});
	}

	function fn_load_rack(room_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_company_id').val();
		var location_id=$('#cbo_location_name').val();
		var store_id=$('#cbo_to_store').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + room_id;
		// alert(all_data);return;
		var rack_result = return_global_ajax_value(all_data, 'rack_list', '', 'requires/finish_roll_issue_return_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(rack_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(rack_result);

		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var rackId = $(this).find('select[name="txtRack[]"]').attr("id");
			var rackIdSlArr = rackId.split("_");
			// copy only that and below selected data
			if( sequenceNo >= rackIdSlArr[1]*1 )
			{
				if(document.getElementById('txtcheck_'+rackIdSlArr[1]*1).disabled)
				{

				}
				else
				{
					$(this).find('select[name="txtRack[]"]').html('<option value="'+0+'">Select</option>');
					for (var key of Object.keys(JSONObject).sort())
					{
						$(this).find('select[name="txtRack[]"]').append('<option value="'+key+'">'+JSONObject[key]+'</option>');
					};
				}
			}
		});
	}

	function fn_load_shelf(rack_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_company_id').val();
		var location_id=$('#cbo_location_name').val();
		var store_id=$('#cbo_to_store').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + rack_id;
		//alert(all_data);return;
		var shelf_result = return_global_ajax_value(all_data, 'shelf_list', '', 'requires/finish_roll_issue_return_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(shelf_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(shelf_result);

		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var shelfId = $(this).find('select[name="txtShelf[]"]').attr("id");
			var shelfIdSlArr = shelfId.split("_");
			// copy only that and below selected data
			if( sequenceNo >= shelfIdSlArr[1]*1 )
			{
				if(document.getElementById('txtcheck_'+shelfIdSlArr[1]*1).disabled)
				{

				}
				else
				{
					$(this).find('select[name="txtShelf[]"]').html('<option value="'+0+'">Select</option>');
					for (var key of Object.keys(JSONObject).sort())
					{
						$(this).find('select[name="txtShelf[]"]').append('<option value="'+key+'">'+JSONObject[key]+'</option>');
					};
				}
			}
		});

	}

	function fn_load_bin(shelf_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_company_id').val();
		var location_id=$('#cbo_location_name').val();
		var store_id=$('#cbo_to_store').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + shelf_id;
		//alert(all_data);return;
		var bin_result = return_global_ajax_value(all_data, 'bin_list', '', 'requires/finish_roll_issue_return_controller');
		var tbl_length=$('#scanning_tbl tbody tr').length;
		//alert(bin_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(bin_result);

		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var shelfId = $(this).find('select[name="txtBin[]"]').attr("id");
			var binIdSlArr = shelfId.split("_");
			// copy only that and below selected data
			if( sequenceNo >= binIdSlArr[1]*1 )
			{
				if(document.getElementById('txtcheck_'+binIdSlArr[1]*1).disabled)
				{

				}
				else
				{
					$(this).find('select[name="txtBin[]"]').html('<option value="'+0+'">Select</option>');
					for (var key of Object.keys(JSONObject).sort())
					{
						$(this).find('select[name="txtBin[]"]').append('<option value="'+key+'">'+JSONObject[key]+'</option>');
					};
				}
			}
		});


	}
	function reset_room_rack_shelf(id,fieldName)
	{
		var numRow=$('#scanning_tbl tbody tr').length;
		if (fieldName=="cbo_to_store")
		{
			$("#scanning_tbl").find('tbody tr').each(function()
			{
 				$(this).find('select[name="cboFloor[]"]').val("0");
				$(this).find('select[name="cboRoom[]"]').val("0");
				$(this).find('select[name="txtRack[]"]').val("0");
				$(this).find('select[name="txtShelf[]"]').val("0");
				$(this).find('select[name="txtBin[]"]').val("0");
			});
		}
		else if (fieldName=="cbo_floor_to")
		{
			$("#scanning_tbl").find('tbody tr').each(function()
			{
				var floorId = $(this).find('select[name="cboFloor[]"]').attr("id");
				var floorIdSlArr = floorId.split("_");

				// copy only that and below selected data
				if( id >= floorIdSlArr[1]*1 )
				{
					if(document.getElementById('txtcheck_'+floorIdSlArr[1]*1).disabled)
					{
						
					}else{
						$(this).find('select[name="cboRoom[]"]').val("0");
						$(this).find('select[name="txtRack[]"]').val("0");
						$(this).find('select[name="txtShelf[]"]').val("0");
						$(this).find('select[name="txtBin[]"]').val("0");
					}
				}
			});
		}
		else if (fieldName=="cbo_room_to")
		{
			$("#scanning_tbl").find('tbody tr').each(function()
			{
				var floorId = $(this).find('select[name="cboFloor[]"]').attr("id");
				var floorIdSlArr = floorId.split("_");

				// copy only that and below selected data
				if( id >= floorIdSlArr[1]*1 )
				{
					$(this).find('select[name="txtRack[]"]').val("0");
					$(this).find('select[name="txtShelf[]"]').val("0");
					$(this).find('select[name="txtBin[]"]').val("0");
				}
			});
		}
		else if (fieldName=="txt_rack_to")
		{
			$("#scanning_tbl").find('tbody tr').each(function()
			{
				var floorId = $(this).find('select[name="cboFloor[]"]').attr("id");
				var floorIdSlArr = floorId.split("_");

				// copy only that and below selected data
				if( id >= floorIdSlArr[1]*1 )
				{
					$(this).find('select[name="txtShelf[]"]').val("0");
					$(this).find('select[name="txtBin[]"]').val("0");
				}
			});
		}
		else if (fieldName=="txt_shelf_to")
		{
			$("#scanning_tbl").find('tbody tr').each(function()
			{
				var floorId = $(this).find('select[name="cboFloor[]"]').attr("id");
				var floorIdSlArr = floorId.split("_");

				// copy only that and below selected data
				if( id >= floorIdSlArr[1]*1 )
				{
					$(this).find('select[name="txtBin[]"]').val("");
				}
			});
		}
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

		$("#scanning_tbl").find('tbody tr').each(function()
		{
			var floorId = $(this).find('select[name="cboFloor[]"]').attr("id");
			var floorIdSlArr = floorId.split("_");

			// copy only that and below selected data
			if( data[0] >= floorIdSlArr[1]*1 )
			{
				if(document.getElementById('txtcheck_'+floorIdSlArr[1]*1).disabled)
				{
					
				}
				else
				{
					if($('#floorIds').is(':checked'))
					{
						if(data[1]==0) 	$(this).find('select[name="cboFloor[]"]').val(data_value);
					}

					if($('#roomIds').is(':checked'))
					{
						if(data[1]==1) $(this).find('select[name="cboRoom[]"]').val(data_value);
					}
					if($('#rackIds').is(':checked'))
					{
						if(data[1]==2) $(this).find('select[name="txtRack[]"]').val(data_value);
					}
					if($('#shelfIds').is(':checked'))
					{
						if(data[1]==3) $(this).find('select[name="txtShelf[]"]').val(data_value);
					}
					if($('#binIds').is(':checked'))
					{
						if(data[1]==4) $(this).find('select[name="txtBin[]"]').val(data_value);
					}
				}
			}
		});
	}

	function change_floor(value,id)
    {
	    var id=id.split('_');
		var roomTd='roomTd_'+id[1];		
		load_drop_down( 'requires/finish_roll_issue_return_controller', value+"_"+roomTd, 'load_drop_down_room', roomTd);
    }

    function change_room(value,id)
    {     	
    	var id=id.split('_');
		var rackTd='rackTd_'+id[1];	
		load_drop_down( 'requires/finish_roll_issue_return_controller', value+"_"+rackTd, 'load_drop_down_rack', rackTd);
    }

    function change_rack(value,id)
    {
    	var id=id.split('_');
		var shelfTd='shelfTd_'+id[1];
		//alert(value+'='+id+'='+shelfTd);		
		load_drop_down( 'requires/finish_roll_issue_return_controller', value+"_"+shelfTd, 'load_drop_down_shelf', shelfTd);
    }

    function change_shelf(value,id)
    {
    	var id=id.split('_');
		var binTd='binTd_'+id[1];
		//alert(value+'='+id+'='+binTd);		
		load_drop_down( 'requires/finish_roll_issue_return_controller', value+"_"+binTd, 'load_drop_down_bin', binTd);
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
                        <td colspan="6" align="center"><b>System No&nbsp;</b>
                        	<input type="text" name="txt_system_no" id="txt_system_no" class="text_boxes" style="width:140px;"  onDblClick="openmypage_issue()" placeholder="Double Click To Search" readonly/>
                            <input type="hidden" name="update_id" id="update_id"/>
                        </td>
                    </tr>
                    <tr>
                    	<td>Barcode Number</td>
                        <td>
                            <input type="text" name="txt_bar_code_num" id="txt_bar_code_num" class="text_boxes_numeric" style="width:150px;" onDblClick="openmypage_barcode()" placeholder="Browse/Write/scan"/>
                        </td>
                        <td class="must_entry_caption">Issue Number</td>
                        <td>
                            <input type="text" name="txt_issue_challan_no" id="txt_issue_challan_no" class="text_boxes" style="width:150px;" onDblClick="openmypage_issuechallan()" placeholder="Browse/Write/scan"/>
                            <input type="hidden" id="txt_issue_id" name="txt_issue_id" />
                        </td>

                        <td class="must_entry_caption">Company</td>
                        <td>
                            <? 
								echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down('requires/finish_roll_issue_return_controller', this.value, 'load_drop_down_store', 'store_td' );",1 );
							?>
							<input type="hidden" id="cbo_location_name" name="cbo_location_name" />
							<input type="hidden" id="store_update_upto" name="store_update_upto" />

                        </td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">To Store</td>
                        <td id="store_td">
                            <?
								//echo create_drop_down( "cbo_to_store", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id  and b.category_type=2 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1,"--Select store--",0,"");
								echo create_drop_down( "cbo_to_store", 160, $blank_array,"", 1, "--Select store--", 0, "fn_load_floor(this.value);" );
						    ?>	
                        </td>
                        <td class="must_entry_caption">Transfer Date</td>
                        <td>
                            <input type="text" name="txt_issue_rtn_date" id="txt_issue_rtn_date" class="datepicker" style="width:150px;" readonly placeholder="Select Date" />
                        </td>
                        <td>Challan No.</td>
                        <td>
                            <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:148px;" maxlength="20" title="Maximum 20 Character" />
                        </td>
                    </tr>
                    <tr>
                    	<td height="5" colspan="6"></td>
                    </tr>
                    <tr>
                    	
                    </tr>
                </table>
			</fieldset>
			<br>
			<fieldset style="width:1370px;text-align:left">
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
				<table cellpadding="0" width="1765" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="40">Check All <br><input type="checkbox" id="txt_check_all" name="txt_check_all" onClick="check_all(this.value)" /></th>
                    	<th width="30">SL</th>
                        <th width="70">Barcode No</th>
                        <th width="45">Roll No</th>
                        <th width="80">Body Part</th>
                        <th width="150">Construction/ Composition</th>
                        <th width="40">GSM</th>
                        <th width="40">Dia</th>
                        <th width="70">Color</th>
                        <th width="60">Roll Wgt.</th>
                        <th width="60">Buyer</th>
                        <th width="80">Job No</th>
                        <th width="100">Order No</th>
                        <th width="80">File No</th>
                        <th width="110">Knit Company</th>
                        <th width="70">Machine No</th>
                        <th width="90">Program/ Booking /Pi No</th>
                        <th width="70"><input type="checkbox" checked id="floorIds" name="floorIds"/><br>Floor</th>
						<th width="70"><input type="checkbox" checked id="roomIds" name="roomIds"/><br>Room</th>
						<th width="70"><input type="checkbox" checked id="rackIds" name="rackIds"/><br>Rack</th>
						<th width="70"><input type="checkbox" checked id="shelfIds" name="shelfIds"/><br>Shelf</th>
						<th width="70"><input type="checkbox" checked id="binIds" name="binIds"/><br>Bin/Box</th>
                        <th width="100">Production Basis</th>
                        <th width="100">Remarks</th>
                    </thead>
                 </table>
                 <div style="width:1783px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="1765" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody>
                        	<tr id="tr_1" align="center" valign="middle">
                            	<td id="button_1" align="center" width="40">
                                	<input type="checkbox" id="txtcheck_1" name="txtcheck[]" />
                            	  	<input type="hidden" name="barcodeNo[]" id="barcodeNo_1"/>
                                    <input type="hidden" name="recvBasis[]" id="recvBasis_1"/>
                                    <input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/>
                                    <input type="hidden" name="productId[]" id="productId_1"/>
                                    <input type="hidden" name="orderId[]" id="orderId_1"/>
                                    <input type="hidden" name="BookWithoutOrd[]" id="BookWithoutOrd_1"/>
                                    <input type="hidden" name="rollId[]" id="rollId_1"/>
                                    <input type="hidden" name="rollWgt[]" id="rollWgt_1"/>
                                    <input type="hidden" name="yarnLot[]" id="yarnLot_1"/>
                                    <input type="hidden" name="yarnCount[]" id="yarnCount_1"/>
                                    <input type="hidden" name="colorId[]" id="colorId_1"/>
                                    <input type="hidden" name="stichLn[]" id="stichLn_1"/>
                                    <input type="hidden" name="brandId[]" id="brandId_1"/>
                                    <input type="hidden" name="rack[]" id="rack_1"/>
                                    <input type="hidden" name="shelf[]" id="shelf_1"/>
                                    <input type="hidden" name="dtlsId[]" id="dtlsId_1"/>
                                    <input type="hidden" name="transId[]" id="transId_1"/>
                                    <input type="hidden" name="rolltableId[]" id="rolltableId_1"/>
                                    <input type="hidden" name="febDescripId[]" id="febDescripId_1"/>
                                    <input type="hidden" name="machineNoId[]" id="machineNoId_1"/>
                                    <input type="hidden" name="prodGsm[]" id="prodGsm_1"/>
                                    <input type="hidden" name="diaWidth[]" id="diaWidth_1"/>
                                    <input type="hidden" name="rollRate[]" id="rollRate_1"/>
                                    <input type="hidden" name="rollAmt[]" id="rollAmt_1"/>
                                    <input type="hidden" name="colorRange[]" id="colorRange_1"/>
                                    <input type="hidden" name="bodyPartData[]" id="bodyPartData_1"/>
                                    <input type="hidden" name="batchId[]" id="batchId_1"/>
                                    <input type="hidden" name="updateRetRollTableId[]" id="updateRetRollTableId_1"/>
                                </td>
                                <td width="30" id="sl_1"></td>
                                <td width="70" id="barcode_1"></td>
                                <td width="45" id="roll_1"></td>
                                <td style="word-break:break-all;" width="80" id="bodyPart_1"></td>
                                <td style="word-break:break-all;" width="150" id="cons_1" align="left"></td>
                                <td style="word-break:break-all;" width="40" id="gsm_1"></td>
                                <td style="word-break:break-all;" width="40" id="dia_1"></td>
                                <td style="word-break:break-all;" width="70" id="color_1"></td>
                                <td width="60" align="right" id="rollWeight_1"></td>
                                <td style="word-break:break-all;" width="60" id="buyer_1"></td>
                                <td style="word-break:break-all;" width="80" id="job_1"></td>
                                <td style="word-break:break-all;" width="100" id="order_1" align="left"></td>
                                <td style="word-break:break-all;" width="80" id="file_1" align="left"></td>
                                <td style="word-break:break-all;" width="110" id="knitCompany_1"></td>
                                <td style="word-break:break-all;" width="70" id="machine_1"></td>
                                <td width="90" id="progBookPiNo_1"></td>

                                <td width="70" align="center" id="floorTd_1" class="floor_td_to">
									<? echo create_drop_down( "cboFloor_1", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_room(this.value, 1); copy_all('1_0'); reset_room_rack_shelf(1,'cbo_floor_to');",0,"","","","","","","cboFloor[]" ,"onchange_void"); ?>
								</td>
								<td width="70" align="center" id="roomTd_1">
		                        	<? echo create_drop_down( "cboRoom_1", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_rack(this.value, 1); copy_all('1_1'); reset_room_rack_shelf(1,'cbo_room_to');",0,"","","","","","","cboRoom[]","onchange_void" ); ?></td>
								
								<td width="70" align="center" id="rackTd_1">
									<? echo create_drop_down( "txtRack_1", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_shelf(this.value, 1); copy_all('1_2'); reset_room_rack_shelf(1,'txt_rack_to');",0,"","","","","","","txtRack[]","onchange_void" ); ?>
								</td>
								<td width="70" align="center" id="shelfTd_1">
									<? echo create_drop_down( "txtShelf_1", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_bin(this.value, 1); copy_all('1_3');reset_room_rack_shelf(1,'txt_shelf_to');",0,"","","","","","","txtShelf[]","onchange_void" ); ?>
								</td>
								<td width="70" align="center" id="binTd_1">
									<? echo create_drop_down( "txtBin_1", 50,$blank_array,"", 1, "--Select--", 0, "copy_all('1_4');",0,"","","","","","","txtBin[]","onchange_void" ); ?>
								</td>
                                <td id="prodBasis_1" width="100">&nbsp;</td>
								<td width="70" align="center" id="remarksTd_1">
									<input type="text" class="text_boxes" style="width: 80px;" id="txtRemark_1" name="txtRemarks[]">
								</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="9">Total</th>
                                <th id="total_rollwgt"></th>
                                <th colspan="14"> </th>
                            </tr>
                        </tfoot>
                	</table>
                </div>
                <br>
                <table width="1540" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                    <tr>
                        <td align="center" class="button_container">
                        	<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
                            <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes" value="">
                            <? 
                            	echo load_submit_buttons($permission,"fnc_grey_fabric_issue_return_roll_wise",0,1,"fnc_reset_form()",1);
                            ?>
                            <input type="button" class="formbutton" style="width:120px" value="Print Groupping" onClick="fnc_grey_fabric_issue_return_roll_wise(5)" />
                        </td>
                    </tr>  
                </table>
			</fieldset>
        </form>	 
    </div>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

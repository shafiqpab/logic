<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Order To Order Transfer Entry
				
Functionality	:	
JS Functions	:
Created by		:	Fuad Shahriar 
Creation date 	: 	26-06-2013
Updated by 		: 	Kausar (Creating Report), Md Didar (order info pop up search pannel=>booking no)	   	
Update date		: 	14-12-2013,06-03-2018 
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
echo load_html_head_contents("Grey Fabric Order To Order Transfer Info","../../", 1, 1, '','',''); 

?>	

<script>
var permission='<? echo $permission; ?>';
var scanned_barcode=new Array();  var scanned_barcode_issue =new Array(); var barcode_rollTableId_array=new Array();
var barcode_trnasId_array =new Array(); var barcode_dtlsId_array=new Array(); var barcode_trnasId_to_array=new Array();
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

var tableFilters = {
			col_0: "none", 
			col_operation: {
				id: ["value_total_roll_qnty"],
				col: [16],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}

function openmypage_systemId()
{
	var cbo_company_id = $('#cbo_company_id').val();
	var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();

	if (form_validation('cbo_company_id','Company')==false)
	{
		return;
	}
	
	var title = 'Item Transfer Info';	
	var page_link = 'requires/grey_fabric_transfer_roll_milti_criteria_controller.php?cbo_company_id='+cbo_company_id+'&cbo_transfer_criteria='+cbo_transfer_criteria+'&action=orderToorderTransfer_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=380px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var transfer_id=this.contentDoc.getElementById("transfer_id").value; //Access form field with id="emailfield"
		
		get_php_form_data(transfer_id, "populate_data_from_transfer_master", "requires/grey_fabric_transfer_roll_milti_criteria_controller" );
		var barcode_nos=return_global_ajax_value( transfer_id, 'barcode_nos', '', 'requires/grey_fabric_transfer_roll_milti_criteria_controller');
		if(trim(barcode_nos)!="")
		{			
			create_row(1,barcode_nos);
		}

		show_list_view(transfer_id+"**"+$('#txt_from_order_id').val(),'show_transfer_listview','tbl_details','requires/grey_fabric_transfer_roll_milti_criteria_controller','');

		setFilterGrid('scanning_tbl',-1,tableFilters);
		disable_enable_fields( 'cbo_company_id*txt_from_order_no*txt_to_order_no', 1, '', '' );
	}
}




function fnc_grey_transfer_entry(operation)
{
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "grey_fabric_order_to_order_transfer_print", "requires/grey_fabric_transfer_roll_milti_criteria_controller" ) 
		return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		if( form_validation('cbo_company_id*txt_transfer_date*txt_from_order_no*txt_to_order_no','Company*Transfer Date*From Order No*To Order No')==false )
		{
			return;
		}
                
        var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_transfer_date').val(), current_date)==false)
		{
			alert("Transfer Date Can not Be Greater Than Current Date");
			return;
		}
		
		var row_num=$('#scanning_tbl tbody tr').length-1;
		var txt_deleted_id=''; var selected_row=0; var i=0; var data_all=''; var txt_deleted_barcode_no = '';
		
		for (var j=1; j<=row_num; j++)
		{
			var updateIdDtls=$('#dtlsId_'+j).val();
			
			if(updateIdDtls!="" && $('#tbl_'+j).is(':not(:checked)'))
			{
				var transIdFrom=$('#transIdFrom_'+j).val();
				var transIdTo=$('#transIdTo_'+j).val();
				var rolltableId=$('#rolltableId_'+j).val();
				var rollId=$('#rollId_'+j).val();
				var delBarcodeNo=$('#barcodeNo_'+j).val();
				var constructCompo=$(this).find("td:eq(5)").text();
				
				selected_row++;
				if(txt_deleted_id=="") txt_deleted_id=updateIdDtls+"_"+transIdFrom+"_"+transIdTo+"_"+rolltableId+"_"+rollId+"_"+delBarcodeNo;  
				else txt_deleted_id+=","+updateIdDtls+"_"+transIdFrom+"_"+transIdTo+"_"+rolltableId+"_"+rollId+"_"+delBarcodeNo;  

				if(txt_deleted_barcode_no=="") txt_deleted_barcode_no = $('#barcodeNo_'+j).val();
				else txt_deleted_barcode_no += "," + $('#barcodeNo_'+j).val();
			}
			// alert('Test+save');//return;
			if($('#tbl_'+j).is(':checked'))
			{
				var constructCompo=$(this).find("td:eq(6)").text();
				i++;
				data_all+="&barcodeNo_" + i + "='" + $('#barcodeNo_'+j).val()+"'"+"&rollNo_" + i + "='" + $('#rollNo_'+j).val()+"'"+"&progId_" + i + "='" + $('#progId_'+j).val()+"'"+"&productId_" + i + "='" + $('#productId_'+j).val()+"'"+"&rollId_" + i + "='" + $('#rollId_'+j).val()+"'"+"&rollWgt_" + i + "='" + $('#rollWgt_'+j).val()+"'"+"&yarnLot_" + i + "='" + $('#yarnLot_'+j).val()+"'"+"&yarnCount_" + i + "='" + $('#yarnCount_'+j).val()+"'"+"&stichLn_" + i + "='" + $('#stichLn_'+j).val()+"'"+"&brandId_" + i + "='" + $('#brandId_'+j).val()+"'"+"&floorsId_" + i + "='" + $('#floorsId_'+j).val()+"'"+"&roomHidd_" + i + "='" + $('#roomHidd_'+j).val()+"'"+"&rack_" + i + "='" + $('#rack_'+j).val()+"'"+"&shelf_" + i + "='" + $('#shelf_'+j).val()+"'"+"&fabricDesc_" + i + "='" + $('#fabricDesc_'+j).text()+"'"+"&febDescripId_" + i + "='" + $('#febDescripId_'+j).val()+"'"+"&gsm_" + i + "='" + $('#gsm_'+j).val()+"'"+"&diaWidth_" + i + "='" + $('#diaWidth_'+j).val()+"'"+"&dtlsId_" + i + "='" + $('#dtlsId_'+j).val()+"'"+"&transIdFrom_" + i + "='" + $('#transIdFrom_'+j).val()+"'"+"&transIdTo_" + i + "='" + $('#transIdTo_'+j).val()+"'"+"&rolltableId_" + i + "='" + $('#rolltableId_'+j).val()+"'"+"&transRollId_" + i + "='" + $('#transRollId_'+j).val()+"'" + "&colorNameId_" + i + "='" + $('#colorNameId_'+j).val() + "'" + "&storeId_" + i + "='" + $('#storeId_'+j).val() + "'" + "&rollAmount_" + i + "='" + $('#rollAmount_'+j).val() + "'";
						
				selected_row++;
			}
		} 
		// alert(data_all);return;
		if(selected_row<1 || selected_row==0)
		{
			alert("Please Select Barcode No.");
			return;
		}
		
		var dataString = "cbo_transfer_criteria*cbo_company_id*cbo_company_id_to*txt_transfer_date*txt_challan_no*cbo_item_category*cbo_location*cbo_location_to*txt_remarks*txt_from_order_id*txt_to_order_id*txt_from_order_no*update_id*cbo_store_name*cbo_store_name_to";
		// alert(dataString);return;
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../")+'&total_row='+i+'&txt_deleted_id='+txt_deleted_id+'&txt_deleted_barcode_no='+txt_deleted_barcode_no+data_all;
		
		// alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/grey_fabric_transfer_roll_milti_criteria_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_grey_transfer_entry_reponse;
	}
}

function fnc_grey_transfer_entry_reponse()
{	
	if(http.readyState == 4) 
	{	  		
		var reponse=trim(http.responseText).split('**');		
		//alert(http.responseText);release_freezing();return;
        if (reponse[0] * 1 == 20 * 1) 
        {
            alert(reponse[1]);
            release_freezing();
            return;
        }
		show_msg(reponse[0]); 	
			
		if(reponse[0]==0 || reponse[0]==1)
		{
			$("#update_id").val(reponse[1]);
			$("#txt_system_id").val(reponse[2]);			

			show_list_view(reponse[1]+"**"+$('#txt_from_order_id').val(),'show_transfer_listview','tbl_details','requires/grey_fabric_transfer_roll_milti_criteria_controller','');
			setFilterGrid('scanning_tbl',-1,tableFilters);
			set_button_status(1, permission, 'fnc_grey_transfer_entry',1,1);
			disable_enable_fields( 'cbo_company_id*txt_from_order_no*txt_to_order_no', 1, '', '' );

			var barcode_nos=return_global_ajax_value( reponse[1], 'barcode_nos', '', 'requires/grey_fabric_transfer_roll_milti_criteria_controller');
			if(trim(barcode_nos)!="")
			{
				create_row(1,barcode_nos);
			}
		}	
		release_freezing();
	}
}

function openmypage_orderInfo(type)
{
	var txt_order_no = $('#txt_'+type+'_order_no').val();
	var txt_order_id = $('#txt_'+type+'_order_id').val();

	if (form_validation('txt_'+type+'_order_no','Order No')==false)
	{
		alert("Please Select Order No.");
		return;
	}
	
	var title = 'Order Info';	
	var page_link = 'requires/grey_fabric_transfer_roll_milti_criteria_controller.php?txt_order_no='+txt_order_no+'&txt_order_id='+txt_order_id+'&type='+type+'&action=orderInfo_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=300px,center=1,resize=1,scrolling=0','../');
}

	function check_all(tot_check_box_id)
	{
		if ($('#'+tot_check_box_id).is(":checked"))
		{ 
			$('#scanning_tbl tbody tr').each(function() 
			{
				//$('#scanning_tbl tbody tr input:checkbox').attr('checked', true);
				if($(this).css('display') == 'none')
				{
					$(this).find('input[name="check[]"]').attr('checked', false);
					
				}
				else
				{
					$(this).find('input[name="check[]"]').attr('checked', true);
				}
				
				
			});
		}
		else
		{ 
			$('#scanning_tbl tbody tr').each(function() {
				$('#scanning_tbl tbody tr input:checkbox').attr('checked', false);
			});
		}
		
		var row_num=$('#scanning_tbl tbody tr').length;
		var selected_roll_wgt=0;
		for (var j=1; j<=row_num; j++)
		{	

			if($('#tbl_'+j).is(':checked'))
			{
				selected_roll_wgt += $('#rollWgt_'+j).val()*1;
			}
		}
		$("#selected_roll_wgt_show").text(selected_roll_wgt.toFixed(2));
	}
	
	function reset_form_all()
	{
		disable_enable_fields('cbo_company_id*txt_from_order_no*txt_to_order_no',0);
		reset_form('transferEntry_1','tbl_details','','','');
	}

	/*function fnc_company_onchang(id){
		$("#cbo_company_to_id").val(id);
	}*/

	function show_selected_total(str)
	{
		// alert('Test');
		var roll_wgt=0; var pre_wgt = 0;
		roll_wgt =$('#rollWgt_'+str).val()*1;
		pre_wgt = $("#selected_roll_wgt_show").text()*1;
		if($('#tbl_'+str).is(":checked"))
		{
			$("#selected_roll_wgt_show").text(pre_wgt+roll_wgt);
		}
		else
		{
			$("#selected_roll_wgt_show").text(pre_wgt-roll_wgt);
		}
	}

// active code here



function active_inactive(str)
{
	reset_form('transferEntry_1','tbl_details','','',"",'cbo_transfer_criteria');
	
	if(str==1)
	{
		$('#cbo_company_id_to').removeAttr('disabled','disabled');	
		$('#cbo_location_to').removeAttr('disabled','disabled');
		$('#txt_to_order_no').val('').removeAttr('disabled','disabled');
	}
	else
	{
		$('#cbo_location_to').val('0').removeAttr('disabled','disabled');
		if(str==4)
		{
			$('#txt_from_order_no').val("").attr('disabled',false).attr('placeholder','Browse');
			$('#txt_from_order_id').val("");
			$('#txt_to_order_no').val("").attr('disabled',false).attr('placeholder','Browse');
			$('#txt_to_order_id').val("");
			$('#cbo_company_id_to').val('0').attr('disabled','disabled');
			// $('#cbo_location_to').val('0').attr('disabled','disabled');
		}
		else
		{
			//$('#txt_from_order_no').val('').attr('disabled',true).attr('placeholder','Display');
			$('#txt_to_order_no').val('').attr('disabled','disabled').attr('placeholder','Display');
			$('#cbo_company_id_to').val('0').attr('disabled','disabled');
			$('#cbo_location_to').val('0').attr('disabled','disabled');
			
		} 
	}

	var html='<tr bgcolor="#FFFFFF" id="tr_1"><td id="ctd_1" width="40" align="center" valign="middle"><input type="checkbox" id="tbl_1" name="check[]" onclick="show_selected_total(1)"/><input type="hidden" name="barcodeNo[]" id="barcodeNo_1" value=""/><input type="hidden" name="recvBasis[]" id="recvBasis_1"/><input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/><input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_1"/><input type="hidden" name="rollNo[]" id="hiddenRollno_1" value=""/><input type="hidden" name="progId[]" id="progId_1" value=""/><input type="hidden" name="productId[]" id="productId_1" value=""/><input type="hidden" name="rollId[]" id="rollId_1" value=""/><input type="hidden" name="rollWgt[]" id="rollWgt_1" value=""/><input type="hidden" name="yarnLot[]" id="yarnLot_1" value=""/><input type="hidden" name="yarnCount[]" id="yarnCount_1" value=""/><input type="hidden" name="stichLn[]" id="stichLn_1" value=""/><input type="hidden" name="brandId[]" id="brandId_1" value=""/><input type="hidden" name="floorsId[]" id="floorsId_1" value=""/><input type="hidden" name="roomHidd[]" id="roomHidd_1" value=""/><input type="hidden" name="rack[]" id="rack_1" value=""/><input type="hidden" name="shelf[]" id="shelf_1" value=""/><input type="hidden" name="dtlsId[]" id="dtlsId_1" value=""/><input type="hidden" name="transIdFrom[]" id="transIdFrom_1" value=""/><input type="hidden" name="transIdTo[]" id="transIdTo_1" value=""/><input type="hidden" name="rolltableId[]" id="rolltableId_1" value=""/><input type="hidden" name="transRollId[]" id="transRollId_1" value=""/><input type="hidden" name="colorName[]" id="colorNameId_1" value=""/><input type="hidden" name="colorType[]" id="colorTypeId_1" value=""/><input type="hidden" name="bodeyPart[]" id="bodyPartId_1" value=""/><input type="hidden" name="storeId[]" id="storeId_1" value=""/><input type="hidden" name="rollAmount[]" id="rollAmount_1" value=""/><input type="hidden" name="knitDetailsId[]" id="knitDetailsId_1" value=""/><input type="hidden" name="febDescripId[]" id="febDescripId_1"/><input type="hidden" name="gsm[]" id="gsm_1"><input type="hidden" name="diaWidth[]" id="diaWidth_1"/></td><td width="40" id="sl_1"></td><td width="80" id="barCodeNo_1"></td><td width="50" id="rollNo_1"></td><td width="70" id="programNo_1"></td><td width="60" id="prodId_1"></td><td width="180" id="fabricDesc_1"></td><td width="80" id="ycount_1"></td><td width="70" id="brandsId_1"></td><td width="80" id="yarnLots_1"></td><td align="center" width="80" id="colorNames_1"></td><td width="80" id="colorTypeName_1"></td><td width="100" id="bodyPartName_1"></td><td width="120" id="floors_td_<? echo $i;?>"><?echo create_drop_down( "floors_1", 100, "","",1, "--Select Floor--");?></td><td width="100" id="rooms_td_<? echo $i;?>"><?echo create_drop_down( "rooms_1", 100, "","",1, "--Select Room--");?></td><td width="100" id="racks_td_<? echo $i;?>"><?echo create_drop_down( "racks_1", 100, "","",1, "--Select Rack--");?></td><td width="80" id="self_td_<? echo $i;?>"><?echo create_drop_down( "self_1", 80, "","",1, "--Select Self--");?></td><td width="80" id="stitchLength_1"></td><td width="" id="qnty_1" align="right" style="padding-right:2px"></td><tr>'; 


		$("#scanning_tbl tbody").append(html);
		$('#txt_tot_row').val(1);

}


function company_on_change(company)
{
	if (form_validation('cbo_transfer_criteria','cbo_transfer_criteria')==false) return;

	load_drop_down( 'requires/grey_fabric_transfer_roll_milti_criteria_controller',company, 'load_drop_down_location', 'from_location_td' );

	if($("#cbo_transfer_criteria").val() != 1)
	{
		$("#cbo_company_id_to").val(company);
		load_drop_down( 'requires/grey_fabric_transfer_roll_milti_criteria_controller',company, 'load_drop_down_location_to', 'to_location_td' );
	}
	
}
function to_company_on_change(to_company)
{
	if($('#cbo_company_id').val()*1 == to_company && $('#cbo_transfer_criteria').val()*1 == 1 )
	{
		alert('Same Company Transfer is not allowed!!'); 
		$('#cbo_company_id_to').val('0'); return;
	}
	load_drop_down('requires/grey_fabric_transfer_roll_milti_criteria_controller',to_company, 'load_drop_down_location_to', 'to_location_td');
}

function openmypage_orderNo(type)
{
	var cbo_company_id = $('#cbo_company_id').val();
	//var cbo_company_to_id = $('#cbo_company_to_id').val();

	if (form_validation('cbo_company_id','Company')==false)
	{
		return;
	}


	if(type=="to")
	{
		if( $("#txt_from_order_no").val() == "") 
		{
			if (form_validation('txt_from_order_no','From Order*To Company')==false)
			{
				return;
			}
		}
		if( $("#cbo_transfer_criteria").val() ==1) 
		{
			if (form_validation('cbo_company_id_to','To Company')==false)
			{
				return;
			}
		}
	}
	
	var txt_from_order_id = $("#txt_from_order_id").val();
	var transfer_criteria = $("#cbo_transfer_criteria").val();
	var cbo_company_id_to = $("#cbo_company_id_to").val();
	
	var title = 'Order Info';	
	var page_link = 'requires/grey_fabric_transfer_roll_milti_criteria_controller.php?cbo_company_id='+cbo_company_id+'&type='+type+'&action=order_popup&txt_from_order_id='+txt_from_order_id+'&cbo_company_id_to='+cbo_company_id_to;
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1230px,height=420px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var order_id=this.contentDoc.getElementById("order_id").value; //Access form field with id="emailfield"
		// alert(order_id+"**"+type+"**"+transfer_criteria);return;
		get_php_form_data(order_id+"**"+type+"**"+transfer_criteria, "populate_data_from_order", "requires/grey_fabric_transfer_roll_milti_criteria_controller" );
		if(type=='from')
		{
			show_list_view(order_id,'show_dtls_list_view','tbl_details','requires/grey_fabric_transfer_roll_milti_criteria_controller','');
			setFilterGrid('scanning_tbl',-1,tableFilters);
		}
		
	}
}

	function create_row(is_update,barcode_no)
	{
		// alert(is_update+'='+barcode_no);
		var row_num=$('#txt_tot_row').val();
		//var bar_code=$('#txt_bar_code_num').val();
		var bar_code=trim(barcode_no);
		var num_row =$('#scanning_tbl tbody tr').length; 
		
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();
		if (form_validation('cbo_transfer_criteria','Transfer Criteria')==false)
		{
			return;
		}
		else
		{
			if(cbo_transfer_criteria==1)
			{
				if (form_validation('cbo_company_id*cbo_company_id_to','From Company*To Company')==false)
				{
					alert("Please Select Both Company Field");return;
				}
			}
			else
			{ //*cbo_store_name // *Stor
				if (form_validation('cbo_company_id','From Companye')==false)
				{
					alert("Please Select To Company Field");return;
				}
			}
			
		}
		var system_id=$('#update_id').val();
		if(is_update==0)
		{
			
			var barcode_data=return_global_ajax_value( bar_code+"**"+system_id, 'populate_barcode_data', '', 'requires/grey_fabric_transfer_roll_milti_criteria_controller');
			// alert(barcode_data);return;
			var barcode_data_all=new Array();
			barcode_data_all=barcode_data.split("**");

			var rcv_id=barcode_data_all[0];
			var company_id=barcode_data_all[1];
			var receive_basis=barcode_data_all[2];
			var receive_basis_id=barcode_data_all[3];
			var receive_date=barcode_data_all[4];
			var booking_no=barcode_data_all[5];
			var booking_id=barcode_data_all[6];
			var color=barcode_data_all[7];
			var knitting_source_id=barcode_data_all[8];
			var knitting_source=barcode_data_all[9];
			var to_store=barcode_data_all[10];
			var knitting_company_id=barcode_data_all[11];
			var yarn_count=barcode_data_all[12];
			var knitting_company_name=barcode_data_all[13];
			var dtls_id=barcode_data_all[14];
			var to_prod_id=barcode_data_all[15];
			var roll_id=barcode_data_all[16];
			var po_breakdown_id=barcode_data_all[17];
			var job_no=barcode_data_all[18];
			var buyer_id=barcode_data_all[19];
			var buyer_name=barcode_data_all[20]; 
			var po_number=barcode_data_all[21];
			var color_id=barcode_data_all[22];
			var store_name=barcode_data_all[23];
			var brand_id=barcode_data_all[24];
			var machine_no_id=barcode_data_all[25];
			var entry_form=barcode_data_all[26];
			var book_without_order=barcode_data_all[27];
			var rollMstId=barcode_data_all[28];
			var bookingNo_fab=barcode_data_all[29];
			var amount=barcode_data_all[30];
			var from_book_without_order=barcode_data_all[31];
			var color_names_id=barcode_data_all[32];
			var color_type_id=barcode_data_all[33]; 
			var body_part_id=barcode_data_all[34];
			var store_id=barcode_data_all[35];
			var transRollId=barcode_data_all[36];
			var barcode_no=barcode_data_all[37];
			var roll_no=barcode_data_all[38];
			var program_no=barcode_data_all[39];
			var prod_id=barcode_data_all[40];
			var fabric_desc=barcode_data_all[41];
			var ycount=barcode_data_all[42];
			var brand_name=barcode_data_all[43];
			var yarn_lot=barcode_data_all[44];
			var color_name=barcode_data_all[45];
			var color_type_name=barcode_data_all[46];
			var body_part_name=barcode_data_all[47];
			var rack=barcode_data_all[48];
			var self=barcode_data_all[49];
			var stitch_length=barcode_data_all[50];
			var qnty=barcode_data_all[51];
			var febric_description_id=barcode_data_all[52];
			var gsm=barcode_data_all[53];
			var width=barcode_data_all[54];
			var floor_id=barcode_data_all[55];
			var room=barcode_data_all[56];


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

			if( jQuery.inArray( bar_code, scanned_barcode )>-1) 
			{ 
				alert('Sorry! Barcode Already Scanned.'); 
				$('#txt_bar_code_num').val('');
				return; 
			}
			//alert(row_num);return;
			var bar_code_no=$('#barcodeNo_'+row_num).val();
			// alert(bar_code_no);
			if(bar_code_no!="")
			{
				row_num++;
				$("#scanning_tbl tbody tr:first").clone().find("td,input,select").each(function()
				{
					//alert($(this).attr('id'));
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

			var transfer_criteria = $("#cbo_transfer_criteria").val();
			get_php_form_data(po_breakdown_id+"**"+"from"+"**"+transfer_criteria, "populate_data_from_order", "requires/grey_fabric_transfer_roll_milti_criteria_controller" );


			$("#barcodeNo_"+row_num).val(barcode_no);
			$("#hiddenRollno_"+row_num).val(roll_no);
			$("#progId_"+row_num).val(program_no);
			$("#productId_"+row_num).val(prod_id);
			$("#rollId_"+row_num).val(roll_id);
			$("#rollWgt_"+row_num).val(qnty);
			$("#yarnLot_"+row_num).val(yarn_lot);
			$("#yarnCount_"+row_num).val(yarn_count);
			$("#stichLn_"+row_num).val(stitch_length);
			$("#brandId_"+row_num).val(brand_id);
			$("#rack_"+row_num).val(rack);
			$("#shelf_"+row_num).val(self);
			//$("#dtlsId_"+row_num).val(dtls_id);
			$("#transIdFrom_"+row_num).val();
			$("#transIdTo_"+row_num).val();
			$("#rolltableId_"+row_num).val();
			$("#transRollId_"+row_num).val(transRollId);
			$("#colorNameId_"+row_num).val(color_names_id);
			$("#colorTypeId_"+row_num).val(color_type_id);
			$("#bodyPartId_"+row_num).val(body_part_id);
			$("#storeId_"+row_num).val(store_id);
			$("#rollAmount_"+row_num).val(amount);
			$("#knitDetailsId_"+row_num).val(dtls_id);
			$("#recvBasis_"+row_num).val(receive_basis_id);
			$("#progBookPiId_"+row_num).val(booking_id);
			if(cbo_transfer_criteria == 2)
			{
				$("#bookWithoutOrder_"+row_num).val(from_book_without_order);
			}
			else
			{
				$("#bookWithoutOrder_"+row_num).val("0");
			}

			$("#sl_"+row_num).text(row_num);
			$("#barCodeNo_"+row_num).text(barcode_no);
			$("#rollNo_"+row_num).text(roll_no);
			$("#programNo_"+row_num).text(program_no);			
			$("#prodId_"+row_num).text(prod_id);
			$("#fabricDesc_"+row_num).text(fabric_desc);
			$("#ycount_"+row_num).text(ycount);
			$("#brandsId_"+row_num).text(brand_name);
			$("#yarnLots_"+row_num).text(yarn_lot);
			$("#colorNames_"+row_num).text(color_name);
			$("#colorTypeName_"+row_num).text(color_type_name);
			$("#bodyPartName_"+row_num).text(body_part_name);
			$("#floors_"+row_num).text(floor_id);
			$("#roomHidd_"+row_num).text(room);
			$("#racks_"+row_num).text(rack);
			$("#self_"+row_num).text(self);
			$("#stitchLength_"+row_num).text(stitch_length);
			$("#qnty_"+row_num).text(qnty);
			$("#febDescripId_"+row_num).val(febric_description_id);
			$("#gsm_"+row_num).val(gsm);
			$("#diaWidth_"+row_num).val(width);

			//$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
			//$('#txtToOrder_'+row_num).removeAttr("onDblClick").attr("onDblClick","opneToOrder("+row_num+");");
			
			$('#txt_tot_row').val(row_num);
			$('#txt_bar_code_num').val('');
			$('#txt_bar_code_num').focus();
		}
		else
		{
			var barcode_data=return_global_ajax_value( bar_code+'**'+system_id, 'populate_barcode_data_update', '', 'requires/grey_fabric_transfer_roll_milti_criteria_controller');
			// alert(barcode_data);return;

			var barcode_data_all=new Array(); var barcode_data_ref=new Array();
			barcode_data_ref=barcode_data.split("__");
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
				var qnty=barcode_data_all[28];var barcode_no=barcode_data_all[29];var job_no=barcode_data_all[30];var buyer_id=barcode_data_all[31];var color_id=barcode_data_all[34];
				var buyer_name=barcode_data_all[32];var po_number=barcode_data_all[33];var store_name=barcode_data_all[35];var bordy_part_id=barcode_data_all[36];
				var brand_id=barcode_data_all[37];var machine_no_id=barcode_data_all[38];var entry_form=barcode_data_all[39];var book_without_order=barcode_data_all[40];
				var up_roll_id=barcode_data_all[41];var up_dtls_id=barcode_data_all[42]; var up_trans_id=barcode_data_all[43];var up_to_trans_id=barcode_data_all[44];
				var up_to_po_no=barcode_data_all[45];var up_to_po_id=barcode_data_all[46];var barcode_for_issue=barcode_data_all[47];var rollMstId=barcode_data_all[48];var bookingNo_fab=barcode_data_all[49]; var rollAmount=barcode_data_all[50]; var fromProductUp=barcode_data_all[51]; 
				var from_book_without_order=barcode_data_all[52];
				var splited_barcode=barcode_data_all[53];
				
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
				/*$("#sl_"+row_num).text(row_num);
				$("#fromStore_"+row_num).text(store_name);
				$("#barcode_"+row_num).text(barcode_no);
				$("#roll_"+row_num).text(roll_no);
				$("#bodyPart_"+row_num).text(body_part_name);
				$("#cons_"+row_num).text(compsition_description);
				$("#gsm_"+row_num).text(gsm);
				$("#dia_"+row_num).text(width);
				$("#color_"+row_num).text(color);
				//$("#diaType_"+row_num).text('');
				$("#rollWeight_"+row_num).text(qnty);
				$("#buyer_"+row_num).text(buyer_name);
				$("#booking_"+row_num).text(bookingNo_fab);

				if(from_book_without_order==1)
				{
					$("#job_"+row_num).text("");
					$("#order_"+row_num).text("");
				}
				else
				{
					$("#job_"+row_num).text(job_no);
					$("#order_"+row_num).text(po_number);
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
				$("#yarnLot_"+row_num).val(yarn_lot);
				$("#yarnCount_"+row_num).val(yarn_count);
				$("#colorId_"+row_num).val(color_id);
				$("#stichLn_"+row_num).val(stitch_length);
				$("#brandId_"+row_num).val(brand_id);
				$("#rack_"+row_num).val(rack);
				$("#shelf_"+row_num).val(self);
				$("#fromStoreId_"+row_num).val(store_id);

				if(cbo_transfer_criteria == 1)
				{
					$("#txtToOrder_"+row_num).val(up_to_po_no);
				}
				$("#toOrderId_"+row_num).val(up_to_po_id);
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

				}
				else if(barcode_no==splited_barcode)
				{
					$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_message_split("+row_num+");");
					$('#txtToOrder_'+row_num).removeAttr("onDblClick").attr("onDblClick","opneToOrder("+row_num+");");
				}
				else
				{
					$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
					$('#txtToOrder_'+row_num).removeAttr("onDblClick").attr("onDblClick","opneToOrder("+row_num+");");
				}
				
				$('#txt_tot_row').val(row_num);
				$('#txt_bar_code_num').val('');
				$('#txt_bar_code_num').focus();*/

				// ========================================== New below ======================================
				$("#barcodeNo_"+row_num).val(barcode_no);
				$("#hiddenRollno_"+row_num).val(roll_no);
				$("#progId_"+row_num).val(program_no);
				$("#productId_"+row_num).val(prod_id);
				$("#rollId_"+row_num).val(roll_id);
				$("#rollWgt_"+row_num).val(qnty);
				$("#yarnLot_"+row_num).val(yarn_lot);
				$("#yarnCount_"+row_num).val(yarn_count);
				$("#stichLn_"+row_num).val(stitch_length);
				$("#brandId_"+row_num).val(brand_id);
				$("#rack_"+row_num).val(rack);
				$("#shelf_"+row_num).val(self);
				$("#dtlsId_"+row_num).val(up_dtls_id);
				$("#transIdFrom_"+row_num).val();
				$("#transIdTo_"+row_num).val();
				$("#rolltableId_"+row_num).val();
				$("#transRollId_"+row_num).val(transRollId);
				$("#colorNameId_"+row_num).val(color_names_id);
				$("#colorTypeId_"+row_num).val(color_type_id);
				$("#bodyPartId_"+row_num).val(body_part_id);
				$("#storeId_"+row_num).val(store_id);
				$("#rollAmount_"+row_num).val(amount);
				$("#knitDetailsId_"+row_num).val(dtls_id);
				$("#recvBasis_"+row_num).val(receive_basis_id);
				$("#progBookPiId_"+row_num).val(booking_id);
				if(cbo_transfer_criteria == 2)
				{
					$("#bookWithoutOrder_"+row_num).val(from_book_without_order);
				}
				else
				{
					$("#bookWithoutOrder_"+row_num).val("0");
				}

				$("#sl_"+row_num).text(row_num);
				$("#barCodeNo_"+row_num).text(barcode_no);
				$("#rollNo_"+row_num).text(roll_no);
				$("#programNo_"+row_num).text(program_no);			
				$("#prodId_"+row_num).text(prod_id);
				$("#fabricDesc_"+row_num).text(fabric_desc);
				$("#ycount_"+row_num).text(ycount);
				$("#brandsId_"+row_num).text(brand_name);
				$("#yarnLots_"+row_num).text(yarn_lot);
				$("#colorNames_"+row_num).text(color_name);
				$("#colorTypeName_"+row_num).text(color_type_name);
				$("#bodyPartName_"+row_num).text(body_part_name);
				$("#floors_"+row_num).text(floor_id);
				$("#roomHidd_"+row_num).text(room);
				$("#racks_"+row_num).text(rack);
				$("#self_"+row_num).text(self);
				$("#stitchLength_"+row_num).text(stitch_length);
				$("#qnty_"+row_num).text(qnty);
				$("#febDescripId_"+row_num).val(febric_description_id);
				$("#gsm_"+row_num).val(gsm);
				$("#diaWidth_"+row_num).val(width);

				//$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
				//$('#txtToOrder_'+row_num).removeAttr("onDblClick").attr("onDblClick","opneToOrder("+row_num+");");
				
				$('#txt_tot_row').val(row_num);
				$('#txt_bar_code_num').val('');
				$('#txt_bar_code_num').focus();
				
			}
		}

		//calculate_total();
	}
	
	$('#txt_bar_code_num').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			e.preventDefault();
			var bar_code=$('#txt_bar_code_num').val();
			create_row(0,bar_code);
		}
	});

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

	function openmypage_barcode()
	{ 
		var company_id=$('#cbo_company_id').val();
		var cbo_transfer_criteria=$('#cbo_transfer_criteria').val();
		var cbo_to_store=$('#cbo_store_name').val();
		if (form_validation('cbo_transfer_criteria','Transfer Criteria')==false)
		{
			return;
		}
		else
		{
			if(cbo_transfer_criteria==1)
			{
				if (form_validation('cbo_company_id*cbo_company_id_to*cbo_location*cbo_store_name','From Company*To Company*Location*Store')==false)
				{
					return; //alert("Please Select Both Company Field");
				}
			}
			else
			{
				if (form_validation('cbo_company_id*cbo_location*cbo_store_name','From Company*Location*Store')==false)
				{
					if(company_id<1)
					{
						return; //alert("Please Select From Company Field");
					}
					else
					{
						return; //alert("Please Select Store Field");
					}
					
				}
			}
			
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/grey_fabric_transfer_roll_milti_criteria_controller.php?company_id='+company_id+'&cbo_transfer_criteria='+cbo_transfer_criteria+'&cbo_to_store='+cbo_to_store+'&action=barcode_popup','Barcode Popup', 'width=980px,height=350px,center=1,resize=1,scrolling=0','../')

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var barcode_nos=this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos
			
			if(barcode_nos!="")
			{
				var barcode_upd=barcode_nos.split(",");
				for(var k=0; k<barcode_upd.length; k++)
				{
					create_row(0,barcode_upd[k]);
				}
				set_all_onclick();
			}
		}
	}

	

</script>
</head>

<body onLoad="set_hotkey();$('#txt_bar_code_num').focus();">
<div align="center" style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?>    		 
    <form name="transferEntry_1" id="transferEntry_1" autocomplete="off" >
        <fieldset style="width:760px;">
        	<legend>Roll Wise Grey Fabric Order To Order Transfer Entry</legend>
        	<br>
            <fieldset style="width:750px;">
                <table cellpadding="0" cellspacing="2" width="800">
                    <tr>
                        <td colspan="6" align="center"><b>Transfer System No&nbsp;</b>
                        	<input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:140px;"  onDblClick="openmypage_systemId()" placeholder="Double Click To Search" readonly/>
                            <input type="hidden" name="update_id" id="update_id"/>
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Transfer Criteria</td>
                        <td>
                            <?
                                echo create_drop_down("cbo_transfer_criteria", 160,$item_transfer_criteria,"", 1,"-- Select --",'0',"active_inactive(this.value);",'','1,2,4');
                            ?>
                        </td>

                        <td class="must_entry_caption">Company</td>
                        <td>
                            <? 
								echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "company_on_change(this.value);" );
							?>
                        </td>
                        <td>To Company</td>
                        <td>
                            <? 
								echo create_drop_down( "cbo_company_id_to", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "to_company_on_change(this.value);",1 );
							?>
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Transfer Date</td>
                        <td>
                            <input type="text" name="txt_transfer_date" id="txt_transfer_date" class="datepicker" style="width:148px;" readonly placeholder="Select Date" />
                        </td>
                        <td width="100" class="must_entry_caption">Location</td>
                        <td id="from_location_td">
                            <?
                               echo create_drop_down( "cbo_location", 160, $blank_array,"", 1, "--Select location--", 0, "" );
                            ?>	
                        </td> 
                        <td width="100" class="">To Location</td>
                        <td id="to_location_td">
                            <?
                               echo create_drop_down( "cbo_location_to", 160, $blank_array,"", 1, "--Select location--", 0, "",1 );
                            ?>	
                        </td> 
                    </tr>
                  
                    <tr>                      
                        <td>Challan No.</td>
                        <td>
                            <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:148px;" maxlength="20" title="Maximum 20 Character" />
                        </td>                                              
                        <td>Item Category</td>
                        <td>
							<?
                            	echo create_drop_down( "cbo_item_category", 160, $item_category,'', 0, '', '', '','1',13 );
                            ?>
                        </td>
                        <td>Remarks</td>
                        <td>
                            <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:148px;" />
                        </td>
                    </tr>
                    <tr>
                    	
                        <td><strong>Roll Number</strong></td>
                        <td>
                            <input type="text" name="txt_bar_code_num" id="txt_bar_code_num" class="text_boxes_numeric" style="width:148px;" onDblClick="openmypage_barcode()" placeholder="Browse/Write/scan"/>
                        </td>
                         
                    </tr>
                </table>
            </fieldset>
            <br>
            <table width="750" cellspacing="2" cellpadding="2" border="0" id="tbl_dtls">
                <tr>
                    <td width="49%" valign="top">
                        <fieldset>
                        <legend>From Order</legend>
                            <table id="from_order_info" cellpadding="0" cellspacing="1" width="100%">										
                                <tr>
                                    <td width="30%" class="must_entry_caption">Order No</td>
                                    <td>
                                        <input type="text" name="txt_from_order_no" id="txt_from_order_no" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_orderNo('from');" readonly />
                                        <input type="hidden" name="txt_from_order_id" id="txt_from_order_id" readonly>
                                    </td>
                                </tr>
                                <tr>
                                	<td class="must_entry_caption">From Store</td>
			                        <td id="from_store_td">
			                            <?
			                            echo create_drop_down( "cbo_store", 160, $blank_array,"", 1, "--Select store--", 0,"", "" );
			                            //load_drop_down( 'requires/grey_fabric_transfer_roll_milti_criteria_controller',this.value+'_'+$('#cbo_company_id').val(), 'load_drop_floor', 'floor_td_$i');
			                            ?>
			                        </td>
                                </tr>
                                 <tr>
                                    <td>Order Qnty</td>
                                    <td>
                                        <input type="text" name="txt_from_po_qnty" id="txt_from_po_qnty" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
                                <tr>	
                                    <td>Buyer</td>
                                    <td>
                                         <? 
                                            echo create_drop_down( "cbo_from_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Display", '', "" ,1);   
                                        ?>	  	
                                    </td>
                                </tr>						
                                <tr>
                                    <td>Style Ref.</td>
                                    <td>
                                        <input type="text" name="txt_from_style_ref" id="txt_from_style_ref" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
                                <tr>
                                    <td>Job No</td>						
                                    <td>                       
                                        <input type="text" name="txt_from_job_no" id="txt_from_job_no" class="text_boxes" style="width:150px" disabled="disabled" placeholder="Display" />
                                    </td>
                                </tr>
                                 <tr>
                                    <td>Fabric Booking No</td>						
                                    <td>                       
                                        <input type="text" name="txt_from_booking_no" id="txt_from_booking_no" class="text_boxes" style="width:150px" disabled="disabled" placeholder="Display" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Internal Ref. No</td>						
                                    <td>                       
                                        <input type="text" name="txt_from_internal_ref_no" id="txt_from_internal_ref_no" class="text_boxes" style="width:150px" disabled="disabled" placeholder="Display"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Gmts Item</td>
                                    <td>
                                        <input type="text" name="txt_from_gmts_item" id="txt_from_gmts_item" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
                                <tr>
                                    <td>Shipment Date</td>						
                                    <td>
                                        <input type="text" name="txt_from_shipment_date" id="txt_from_shipment_date" class="datepicker" style="width:150px" disabled="disabled" placeholder="Display" />
                                  
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                    <td width="2%" valign="top"></td>
                    <td width="49%" valign="top">
                        <fieldset>
                        <legend>To Order</legend>					
                            <table id="to_order_info"  cellpadding="0" cellspacing="1" width="100%" >				
                                <tr>
                                    <td width="30%" class="must_entry_caption">Order No</td>
                                    <td>
                                        <input type="text" name="txt_to_order_no" id="txt_to_order_no" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_orderNo('to');" readonly />
                                        <input type="hidden" name="txt_to_order_id" id="txt_to_order_id" readonly>
                                    </td>
                                </tr>
                                <tr>
                                	<td class="must_entry_caption">To Store</td>
			                        <td id="to_store_td">
			                            <?
			                                echo create_drop_down( "cbo_to_store", 160, $blank_array,"", 1, "--Select store--", 0, "" );
			                            ?>	
			                        </td>
                                </tr>
                                 <tr>
                                    <td>Order Qnty</td>
                                    <td>
                                        <input type="text" name="txt_to_po_qnty" id="txt_to_po_qnty" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
                                <tr>	
                                    <td>Buyer</td>
                                    <td>
                                         <? 
                                            echo create_drop_down( "cbo_to_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Display", '', "" ,1);   
                                        ?>	  	
                                    </td>
                                </tr>						
                                <tr>
                                    <td>Style Ref.</td>
                                    <td>
                                        <input type="text" name="txt_to_style_ref" id="txt_to_style_ref" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
                                <tr>
                                    <td>Job No</td>						
                                    <td>                       
                                        <input type="text" name="txt_to_job_no" id="txt_to_job_no" class="text_boxes" style="width:150px" disabled="disabled" placeholder="Display"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Fabric Booking No</td>						
                                    <td>                       
                                        <input type="text" name="txt_to_booking_no" id="txt_to_booking_no" class="text_boxes" style="width:150px" disabled="disabled" placeholder="Display"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Internal Ref. No</td>						
                                    <td>                       
                                        <input type="text" name="txt_to_internal_ref_no" id="txt_to_internal_ref_no" class="text_boxes" style="width:150px" disabled="disabled" placeholder="Display"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Gmts Item</td>
                                    <td>
                                        <input type="text" name="txt_to_gmts_item" id="txt_to_gmts_item" class="text_boxes" style="width:150px;" disabled="disabled" placeholder="Display" /></td>
                                </tr>
                                <tr>
                                    <td>Shipment Date</td>						
                                    <td>
                                        <input type="text" name="txt_to_shipment_date" id="txt_to_shipment_date" class="datepicker" style="width:150px" disabled="disabled" placeholder="Display" />
                                
                                    </td>
                                </tr>											
                            </table>                  
                       </fieldset>	
                    </td>
                </tr>
			</table>	
        </fieldset>
		<table cellpadding="0" width="1570" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
            <thead>
            	<th width="44"><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></th>
            	<th width="40">SL</th>
                <th width="80">Barcode No</th>
                <th width="50">Roll No</th>
                <th width="70">Program No</th>
                <th width="60">Product Id</th>
                <th width="180">Fabric Description</th>
                <th width="80">Y/Count</th>
                <th width="70">Y/Brand</th>
                <th width="80">Y/Lot</th>
                <th width="80">Color</th>
                <th width="80">Color Type</th>
                <th width="100">Body Part</th>
                <th width="120">Floor</th>
                <th width="100">Room</th>
                <th width="100">Rack</th>
                <th width="80">Shelf</th>
                <th width="80">Stitch Length</th>
                <th width="">Roll Wgt.</th>
            </thead>
        </table>
        <fieldset style="width:1590px;text-align:left">
            <div style="width:1590px; max-height:250px; overflow-y:scroll" align="left">
             	<table cellpadding="0" cellspacing="0" width="1570" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                	<tbody id="tbl_details">
                    	<tr bgcolor="#FFFFFF"  id="tr_1">
							<td width="40" id="ctd_1" align="center" valign="middle">
								<input type="checkbox" id="tbl_1" name="check[]" onclick="show_selected_total('1')"/>
								<input type="hidden" name="barcodeNo[]" id="barcodeNo_1" value=""/>
								<input type="hidden" name="recvBasis[]" id="recvBasis_1"/>
								<input type="hidden" name="progBookPiId[]" id="progBookPiId_1"/>
								<input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_1"/>
			                    <input type="hidden" name="rollNo[]" id="hiddenRollno_1" value=""/>
			                    <input type="hidden" name="progId[]" id="progId_1" value=""/>
			                    <input type="hidden" name="productId[]" id="productId_1" value=""/>
			                    <input type="hidden" name="rollId[]" id="rollId_1" value=""/>
			                    <input type="hidden" name="rollWgt[]" id="rollWgt_1" value=""/>
			                    <input type="hidden" name="yarnLot[]" id="yarnLot_1" value=""/>
			                    <input type="hidden" name="yarnCount[]" id="yarnCount_1" value=""/>
			                    <input type="hidden" name="stichLn[]" id="stichLn_1" value=""/>
			                    <input type="hidden" name="brandId[]" id="brandId_1" value=""/>
			                    <input type="hidden" name="floorsId[]" id="floorsId_1" value=""/>
			                    <input type="hidden" name="roomHidd[]" id="roomHidd_1" value=""/>
			                    <input type="hidden" name="rack[]" id="rack_1" value=""/>
			                    <input type="hidden" name="shelf[]" id="shelf_1" value=""/>
			                    <input type="hidden" name="dtlsId[]" id="dtlsId_1" value=""/>
			                    <input type="hidden" name="transIdFrom[]" id="transIdFrom_1" value=""/>
			                    <input type="hidden" name="transIdTo[]" id="transIdTo_1" value=""/>
			                    <input type="hidden" name="rolltableId[]" id="rolltableId_1" value=""/>
			                    <input type="hidden" name="transRollId[]" id="transRollId_1" value=""/>
			                    <input type="hidden" name="colorName[]" id="colorNameId_1" value=""/>
			                    <input type="hidden" name="colorType[]" id="colorTypeId_1" value=""/>
			                    <input type="hidden" name="bodeyPart[]" id="bodyPartId_1" value=""/>
			                    <input type="hidden" name="storeId[]" id="storeId_1" value=""/>
			                    <input type="hidden" name="rollAmount[]" id="rollAmount_1" value=""/>
			                    <input type="hidden" name="knitDetailsId[]" id="knitDetailsId_1"/>
								<input type="hidden" name="febDescripId[]" id="febDescripId_1"/>
			                    <input type="hidden" name="gsm[]" id="gsm_1">
								<input type="hidden" name="diaWidth[]" id="diaWidth_1"/></td>
							<td width="40" id="sl_1"></td>
							<td width="80" id="barCodeNo_1"></td>
							<td width="50" id="rollNo_1"></td>
							<td width="70" id="programNo_1"></td>
							<td width="60" id="prodId_1"></td>
							<td width="180" id="fabricDesc_1"></td>
							<td width="80" id="ycount_1"></td>
							<td width="70" id="brandsId_1"></td>
							<td width="80" id="yarnLots_1"></td>
							<td width="80" id="colorNames_1" align="center"></td>
			                <td width="80" id="colorTypeName_1"></td>
			                <td width="100" id="bodyPartName_1"></td>
							<td width="120" id="floors_1"></td>
							<td width="100" id="rooms_1"></td>
							<td width="100" id="racks_1"></td>
							<td width="80" id="self_1"></td>
							<td width="80" id="stitchLength_1"></td>
							<td width="" id="qnty_1" align="right" style="padding-right:2px"></td>
						</tr>
                    </tbody>
            	</table>
            </div>
            <table cellpadding="0" cellspacing="0" width="1570" border="1" rules="all" class="rpt_table">
            	<tfoot>
            		<tr>
                    	<th width="40"></th>
                    	<th width="40"></th>
                        <th width="80"></th>
                        <th width="50"></th>
                        <th width="70"></th>
                        <th width="60"></th>
                        <th width="180"></th>
                        <th width="80"></th>
                        <th width="70"></th>
                        <th width="80"></th>
                        <th width="80"></th>
                        <th width="80"></th>
                        <th width="100"></th>
                        <th width="120"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="80"></th>
                        <th width="80">Total</th>
                        <th width="" id="value_total_roll_qnty"></th>
                	</tr>
                	<tr>
                    	<th width="40"></th>
                    	<th width="40"></th>
                        <th width="80"></th>
                        <th width="50"></th>
                        <th width="70"></th>
                        <th width="60"></th>
                        <th width="180"></th>
                        <th width="80"></th>
                        <th width="70"></th>
                        <th width="80"></th>
                        <th width="80"></th>
                        <th width="80"></th>
                        <th width="100"></th>
                        <th width="120"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="80"></th>
                        <th width="80">Selected Total</th>
                        <th width="" id="selected_roll_wgt_show">&nbsp;</th>
                	</tr>
                </tfoot>
            </table>
            <br>
            <table width="1570" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                <tr>
                    <td align="center" class="button_container">
                    	<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">

                        <? 
                        	echo load_submit_buttons($permission, "fnc_grey_transfer_entry", 0,1,"reset_form_all()",1);
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

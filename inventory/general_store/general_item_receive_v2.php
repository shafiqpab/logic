<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create General Receive V2 Entry 
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	19/11/2022
Updated by 		: 	Rakib	
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
//-------------------------------------------------------------------------------------------
echo load_html_head_contents("General Receive V2 Entry", "../../", 1, 1,'','',''); 

?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	const open_mrrpopup = () => {
		var company = $("#cbo_company_id").val();
		if ( form_validation('cbo_company_id','Company Name')==false ) { return; }
		var page_link='requires/general_item_receive_v2_controller.php?action=mrr_popup&company='+company;
		var title="Search MRR Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1070px,height=400px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var mrr_id=this.contentDoc.getElementById("hidden_recv_number").value; // mrr number
			get_php_form_data(mrr_id, "populate_data_from_data", "requires/general_item_receive_v2_controller");
			
			$("#tbl_master").find('input,select').attr("disabled", true);
			disable_enable_fields( 'txt_mrr_no*txt_remarks*txt_sup_ref*btn_fileadd', 0, "", "" );
			var posted_in_account=$("#hidden_posted_in_account").val()*1;

			if(posted_in_account==1) $("#accounting_posted_status").text("Already Posted In Accounting.");
			else $("#accounting_posted_status").text("");
			

			var txt_wo_pi_req_id = $("#txt_wo_pi_req_id").val();
			var txt_wo_pi_req = $("#txt_wo_pi_req").val();

			$("#txt_wo_pi_req_id").val(txt_wo_pi_req_id);
			$("#txt_wo_pi_req").val(txt_wo_pi_req);

			var cbo_receive_basis=$("#cbo_receive_basis").val();	
			if(cbo_receive_basis==1 || cbo_receive_basis==2 || cbo_receive_basis==7)
			{
				show_list_view($("#txt_wo_pi_req_id").val()+"**"+$("#cbo_receive_basis").val()+"**"+company+"**"+$("#cbo_currency_id").val()+"**"+$("#txt_exchange_rate").val()+"**"+$("#cbo_source").val()+"**"+$("#update_id").val()+"**"+$("#cbo_store_name").val(),'show_fabric_desc_listview_update','list_fabric_desc_container','requires/general_item_receive_v2_controller','setFilterGrid(\'list_fabric_desc_container\',-1)');

				load_details_data(booking_id,booking_no,booking_without_order,trims_recv_id,material_source);
			}
			else 
			{
				reset_form('','list_product_container','','','','');
			}
			set_button_status(0, permission, 'fnc_general_receive',1,1);
		}
	}
	
	const openmypage_wo_pi_popup = (page_link,title) => {
		if( form_validation('cbo_company_id*cbo_receive_basis','Company Name*Receive Basis')==false ) { return; }
		var company = $("#cbo_company_id").val();
		var receive_basis = $("#cbo_receive_basis").val();
		page_link=`requires/general_item_receive_v2_controller.php?action=wopi_popup&company=${company}&receive_basis=${receive_basis}`;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px, height=420px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var rowID=this.contentDoc.getElementById("hidden_tbl_id").value; // wo/pi table id
			var wopiNumber=this.contentDoc.getElementById("hidden_wopi_number").value; // wo/pi number
			if (rowID!="")
			{
				//freeze_window(5);
				//$("#txt_wo_pi_req").val(wopiNumber);
				$("#txt_wo_pi_req_id").val(rowID);
				//alert(rowID);
				get_php_form_data(`${receive_basis}**${rowID}`, "populate_data_from_wopi_popup", "requires/general_item_receive_v2_controller");

				var cbo_currency_id = $("#cbo_currency_id").val();
				var txt_exchange_rate = $("#txt_exchange_rate").val();
				var cbo_source = $("#cbo_source").val();
				show_list_view(`${rowID}**${receive_basis}**${company}**${cbo_currency_id}**${txt_exchange_rate}**${cbo_source}`, 'show_fabric_desc_listview', 'list_fabric_desc_container', 'requires/general_item_receive_v2_controller', 'setFilterGrid("list_fabric_desc_container",-1)' ) ;
				$('#check_qnty').attr('checked',true);
				$("#cbo_receive_basis").attr("disable",true);
				$("#cbo_company_id").attr("disable",true);
				$('#cbo_store_name').val(0);
				calculate(1);
				release_freezing();
			}
		}
	}
	
	
	function fn_load_floor(store_id)
	{
		if( form_validation('cbo_company_id*txt_wo_pi_req','Company Name*WO PI REQ No')==false )
		{
			$('#cbo_store_name').val(0);
			return;
		}		
		var com_id=$('#cbo_company_id').val();
		var all_data=com_id + "__" + store_id;
		//alert(all_data);return;
		var floor_result = return_global_ajax_value(all_data, 'floor_list', '', 'requires/general_item_receive_v2_controller');
		var tbl_length=$('#tbl_fabric_desc_item tbody tr').length;
		//alert(floor_result+"="+tbl_length);//return;
		var JSONObject = JSON.parse(floor_result);
		for(var i=1; i<=tbl_length; i++)
		{
			$('#cbo_floor_to_'+i).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				//alert(Object.keys(JSONObject));
				$('#cbo_floor_to_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}
	}
	
	function fn_load_room(floor_id, sequenceNo)
	{
		var com_id=$('#cbo_company_id').val();
		var location_id=0;
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
		//alert(all_data);return;
		var room_result = return_global_ajax_value(all_data, 'room_list', '', 'requires/general_item_receive_v2_controller');
		var tbl_length=$('#tbl_fabric_desc_item tbody tr').length;
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

	function fn_load_rack(room_id, sequenceNo)
	{
		var com_id=$('#cbo_company_id').val();
		var location_id=0;
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + room_id;
		//alert(all_data);return;
		var rack_result = return_global_ajax_value(all_data, 'rack_list', '', 'requires/general_item_receive_v2_controller');
		var tbl_length=$('#tbl_fabric_desc_item tbody tr').length;
		var JSONObject = JSON.parse(rack_result);
		for(var i=sequenceNo; i<=tbl_length; i++)
		{
			$('#txt_rack_to_'+i).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#txt_rack_to_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}
	}

	function fn_load_shelf(rack_id, sequenceNo)
	{
		var com_id=$('#cbo_company_id').val();
		var location_id=0;
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + rack_id;
		//alert(all_data);return;
		var shelf_result = return_global_ajax_value(all_data, 'shelf_list', '', 'requires/general_item_receive_v2_controller');
		var tbl_length=$('#tbl_fabric_desc_item tbody tr').length;
		var JSONObject = JSON.parse(shelf_result);
		for(var i=sequenceNo; i<=tbl_length; i++)
		{
			$('#txt_shelf_to_'+i).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#txt_shelf_to_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}
	}

	function fn_load_bin(shelf_id, sequenceNo)
	{
		var com_id=$('#cbo_company_id').val();
		var location_id=0;
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + shelf_id;
		//alert(all_data);return;
		var shelf_result = return_global_ajax_value(all_data, 'bin_list', '', 'requires/general_item_receive_v2_controller');
		var tbl_length=$('#tbl_fabric_desc_item tbody tr').length;
		var JSONObject = JSON.parse(shelf_result);
		for(var i=sequenceNo; i<=tbl_length; i++)
		{
			$('#txt_bin_to_'+i).html('<option value="'+0+'">Select</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				$('#txt_bin_to_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}
	}

	function copy_all(str)
	{
		var data=str.split("_");
		var trall=$("#tbl_fabric_desc_item tbody tr").length-1;
		//alert(trall);
		var copy_tr=parseInt(trall);
		if($('#floorIds').is(':checked'))
		{
			if(data[1]==0) data_value=$("#cbo_floor_to_"+data[0]).val();
		}
		if($('#roomIds').is(':checked'))
		{
			if(data[1]==1) data_value=$("#cbo_room_to_"+data[0]).val();
		}
		if($('#rackIds').is(':checked'))
		{
			if(data[1]==2) data_value=$("#txt_rack_to_"+data[0]).val();
		}
		if($('#shelfIds').is(':checked'))
		{
			if(data[1]==3) data_value=$("#txt_shelf_to_"+data[0]).val();
		}
		if($('#binIds').is(':checked'))
		{
			if(data[1]==4) data_value=$("#txt_bin_to_"+data[0]).val();
		}

		var first_tr=parseInt(data[0])+1;
		for(var k=first_tr; k<=copy_tr; k++)
		{
			if($('#floorIds').is(':checked'))
			{
				if(data[1]==0) 	$("#cbo_floor_to_"+k).val(data_value);
			}
			if($('#roomIds').is(':checked'))
			{
				if(data[1]==1) 	$("#cbo_room_to_"+k).val(data_value);
			}
			if($('#rackIds').is(':checked'))
			{
				if(data[1]==2) 	$("#txt_rack_to_"+k).val(data_value);
			}
			if($('#shelfIds').is(':checked'))
			{
				if(data[1]==3) 	$("#txt_shelf_to_"+k).val(data_value);
			}
			if($('#binIds').is(':checked'))
			{
				if(data[1]==4) 	$("#txt_bin_to_"+k).val(data_value);
			}	
		}
	}

	function reset_room_rack_shelf(id,fieldName)
	{
		var numRow=$('#tbl_fabric_desc_item tbody tr').length;
		if (fieldName=="cbo_store_name") 
		{			
			for (var i = 1;numRow>=i; i++) 
			{
				$("#cbo_floor_to_"+i).val('');
				$("#cbo_room_to_"+i).val('');
				$("#txt_rack_to_"+i).val('');
				$("#txt_shelf_to_"+i).val('');
				$("#txt_bin_to_"+i).val('');
			}
		}
		else if (fieldName=="cbo_floor_to") 
		{
			for (var i = id; numRow>=i; i++) 
			{
				$("#cbo_room_to_"+i).val('');
				$("#txt_rack_to_"+i).val('');
				$("#txt_shelf_to_"+i).val('');
				$("#txt_bin_to_"+i).val('');
			}
			
		}
		else if (fieldName=="cbo_room_to")  
		{
			for (var i = id; numRow>=i; i++) 
			{
				$("#txt_rack_to_"+i).val('');
				$("#txt_shelf_to_"+i).val('');
				$("#txt_bin_to_"+i).val('');
			}
		}
		else if (fieldName=="txt_rack_to")  
		{
			for (var i = id; numRow>=i; i++) 
			{
				$("#txt_shelf_to_"+i).val('');
				$("#txt_bin_to_"+i).val('');
			}
		}
		else if (fieldName=="txt_shelf_to")  
		{
			for (var i = id; numRow>=i; i++) 
			{
				$("#txt_bin_to_"+i).val('');
			}
		}
	}
	

	function check_exchange_rate()
	{
		var cbo_company_id=$('#cbo_company_id').val();
		var cbo_currency_id=$('#cbo_currency_id').val();
		var receive_date = $('#txt_receive_date').val();
		if( form_validation('cbo_company_id*cbo_currency_id*txt_receive_date','Company Name*Currency*Date')==false )
		{
			return;
		}
		var response=return_global_ajax_value( cbo_currency_id+"**"+receive_date+"**"+cbo_company_id, 'check_conversion_rate', '', 'requires/general_item_receive_v2_controller');
		var response=response.split("_");
		$('#txt_exchange_rate').val(response[1]);
		$('#txt_exchange_rate').attr('disabled','disabled');
	}
	
	
	function set_receive_basis(i)
	{
		if(i==1)
		{
			disable_enable_fields( 'cbo_company_id*cbo_receive_basis*txt_booking_pi_no', 0, '', '' );
		}
		
		var recieve_basis = $('#cbo_receive_basis').val();
		var cbo_company_id = $('#cbo_company_id').val();
		
		$('#booking_without_order').val('');
		$('#txt_booking_pi_no').val('');	
		$('#txt_wo_pi_id').val('');
		
		//$('#cbo_supplier_name').val(0);
		//$('#cbo_source').val(0);
		//$('#cbo_currency_id').val(2);
		
		var list_view_wo =trim(return_global_ajax_value( recieve_basis, 'mrr_details', '', 'requires/general_item_receive_v2_controller'));
		$('#list_fabric_desc_container').html('');
		$('#list_fabric_desc_container').html(list_view_wo);
	}
	
	function load_supplier()
	{
		var receive_purpose = $("#cbo_receive_purpose").val();
		var receive_basis = $("#cbo_receive_basis").val();
		var company = $("#cbo_company_id").val();
	
		if(form_validation('cbo_company_id','Company')==false )
		{
			$("#cbo_receive_purpose").val(0);
			return;
		}
	
		$("#txt_receive_chal_no").val('');
		$("#update_id").val('');
		$("#cbo_party").val(0);
		//$('#loanParty_td').css('color','black');
		
		if(receive_purpose==15 || receive_purpose==50 || receive_purpose==51)
		{
			load_drop_down( 'requires/general_item_receive_v2_controller', company, 'load_drop_down_supplier_from_issue', 'supplier' );
			if($('#cbo_supplier option').length==2)
			{
				$('#cbo_supplier').val($('#cbo_supplier option:last').val());
			}
			$('#cbo_party').attr('disabled','disabled');
		}
		else if(receive_purpose==5)
		{
			load_drop_down( 'requires/general_item_receive_v2_controller',company, 'load_drop_down_supplier', 'supplier' );
			load_drop_down( 'requires/general_item_receive_v2_controller',company, 'load_drop_down_party', 'loanParty' );
			$('#cbo_party').removeAttr('disabled','disabled');
			$('#loanParty_td').css('color','blue');
		}
		else if(receive_purpose==16)
		{
			load_drop_down( 'requires/general_item_receive_v2_controller',company, 'load_drop_down_supplier', 'supplier' );
			$('#cbo_party').attr('disabled','disabled');
			$('#cbo_color').val($('#cbo_color option:last').val());
			$('#cbo_color').attr('disabled','disabled');
		}
		else
		{
			load_drop_down( 'requires/general_item_receive_v2_controller',company, 'load_drop_down_supplier', 'supplier' );
			$('#cbo_party').attr('disabled','disabled');
		}
	
		if(receive_basis==4) $('#cbo_supplier').removeAttr('disabled','disabled');
		else $('#cbo_supplier').attr('disabled','disabled');
	
		//$("#tbl_child").find("input[type=text],input[type=hidden],select").val('');
		//$('#cbo_uom').val(12);
		//$('#percentage1').val(100);
	}	
	
	function load_details_data(booking_pi_id,booking_pi_no,booking_without_order,mst_id)
	{
		var cbo_company_id=$('#cbo_company_id').val();
		var recieve_basis=$('#cbo_receive_basis').val();
		var recieve_purpose=$('#cbo_receive_purpose').val();
		var exchange_rate=$('#txt_exchange_rate').val();
		var cbo_source=$('#cbo_source').val();
		var cbo_store_name=$('#cbo_store_name').val();
		var grn_receive_basis=$('#grn_receive_basis').val();
		if(recieve_basis==19) var grn_mst_id=$('#txt_wo_pi_id').val(); else grn_mst_id=0;
		show_list_view(booking_pi_id+"**"+recieve_basis+"**"+booking_without_order+"**"+cbo_company_id+"**"+cbo_source+"**"+exchange_rate+"**"+mst_id+"**"+grn_mst_id+"**"+cbo_store_name+"**"+recieve_purpose+"**"+grn_receive_basis, 'show_fabric_desc_listview_update', 'list_fabric_desc_container', 'requires/general_item_receive_v2_controller', 'setFilterGrid("list_fabric_desc_container",-1)' ) ;
		calculate(1);
		set_button_status(1, permission, 'fnc_trims_receive',1,1);
	}	
	
	function fnc_general_receive(operation)
	{
		if(operation==4)
		{
			alert("Under Construction....");
			//var report_title=$( "div.form_caption" ).html();
			//print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "trims_receive_entry_print", "requires/general_item_receive_v2_controller" ) 
			return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if(operation==2)
			{
				show_msg('13');
				return;
			}
			if ($("#is_posted_account").val()*1 == 1) {
				alert("Already Posted In Accounting. Save Update Delete Restricted.");
				return;
			}
			
			if( form_validation('cbo_company_id*cbo_receive_basis*txt_wo_pi_req*txt_receive_date*cbo_store_name*txt_exchange_rate*cbo_source*cbo_pay_mode*cbo_supplier*cbo_receive_purpose','Company*Receive Basis*WO PI Req*Received Date*store*Exchange Rate*Source*Pay Mode*Supplier*Receive Purpose')==false ) { return; }
			
			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_receive_date').val(), current_date)==false)
			{
				alert("Receive Date Can not Be Greater Than Current Date");
				return;
			}
			
			if(($('#cbo_receive_basis').val()==1 || $('#cbo_receive_basis').val()==2 || $('#cbo_receive_basis').val()==7) && $('#txt_wo_pi_req').val()=="")
			{
				alert("Please Select WO/PI/Req No");
				$('#txt_wo_pi_req').focus();
				return;
			}
			
			var j=0; var i=1; var dataString='';
			$("#tbl_fabric_desc_item").find('tbody tr').not(':first').each(function()
			{
				var category           = $('#category_'+i).attr('title');
				var group              = $('#group_'+i).attr('title');
				var description        = encodeURIComponent($('#description_'+i).html());
				var size               = encodeURIComponent($('#size_'+i).html());
				var subGroup           = encodeURIComponent($('#subGroup_'+i).html());
				var itemNumber         = encodeURIComponent($('#itemNumber_'+i).html());
				var itemCode           = encodeURIComponent($('#itemCode_'+i).html());
				var uom                = $('#uom_'+i).attr('title');
				var woPiReqQnty        = $('#woPiReqQnty_'+i).html();
				var receiveqnty        = $(this).find('input[name="receiveqnty[]"]').val();
				var txtLot             = encodeURIComponent($(this).find('input[name="txtLot[]"]').val());
				var rate               = $('#rate_'+i).attr('title');
				var ilePersent         = $('#ilePersent_'+i).val();
				var amount             = $('#amount_'+i).attr('title');
				var woPiBalQnty        = $('#woPiBalQnty_'+i).attr('title');
				var comments           = encodeURIComponent($(this).find('input[name="comments[]"]').val());
				var bookCurrency       = $('#bookCurrency_'+i).attr('title');
				var txtWarrentyExpDate = $(this).find('input[name="txtWarrentyExpDate[]"]').val();
				var txtSerial          = $(this).find('input[name="txtSerial[]"]').val();
				var txtSerialQty       = $(this).find('input[name="txtSerialQty[]"]').val();
				var brand              = encodeURIComponent($('#brand_'+i).html());
				var origin             = $('#origin_'+i).attr('title');
				var model              = encodeURIComponent($('#model_'+i).html());
				var floorID            = $('#cbo_floor_to_'+i).val();
				var roomID             = $('#cbo_room_to_'+i).val();
				var rackID             = $('#txt_rack_to_'+i).val();
				var shelfID            = $('#txt_shelf_to_'+i).val();
				var binID              = $('#txt_bin_to_'+i).val();

				var updatedtlsid = $(this).find('input[name="updatedtlsid[]"]').val();
				var piWoDtlsId   = $(this).find('input[name="piWoDtlsId[]"]').val();
				var prodId       = $(this).find('input[name="prodId[]"]').val();

				if(receiveqnty>0)
				{
					j++;
					//dataString+='&category'+j+'='+category+'&group'+j+'='+group+'&description'+j+'='+description+'&size'+j+'='+size+'&subGroup'+j+'='+subGroup+'&itemNumber'+j+'='+itemNumber+'&itemCode'+j+'='+itemCode+'&uom'+j+'='+uom+'&woPiReqQnty'+j+'='+woPiReqQnty+'&receiveqnty'+j+'='+receiveqnty+'&txtLot'+j+'='+txtLot+'&rate'+j+'='+rate+'&ilePersent'+j+'='+ilePersent+'&amount'+j+'='+amount+'&woPiBalQnty'+j+'='+woPiBalQnty+'&comments'+j+'='+comments+'&bookCurrency'+j+'='+bookCurrency+'&txtWarrentyExpDate'+j+'='+txtWarrentyExpDate+'&txtSerial'+j+'='+txtSerial+'&txtSerialQty'+j+'='+txtSerialQty+'&brand'+j+'='+brand+'&origin'+j+'='+origin+'&model'+j+'='+model+'&floorID'+j+'='+floorID+'&roomID'+j+'='+roomID+'&rackID'+j+'='+rackID+'&shelfID'+j+'='+shelfID+'&binID'+j+'='+binID+'&updatedtlsid'+j+'='+updatedtlsid+'&piWoDtlsId'+j+'='+piWoDtlsId+'&prodId'+j+'='+prodId;
					dataString+=`&category${j}=${category}&group${j}=${group}&description${j}=${description}&size${j}=${size}&subGroup${j}=${subGroup}&itemNumber${j}=${itemNumber}&itemCode${j}=${itemCode}&uom${j}=${uom}&woPiReqQnty${j}=${woPiReqQnty}&receiveqnty${j}=${receiveqnty}&txtLot${j}=${txtLot}&rate${j}=${rate}&ilePersent${j}=${ilePersent}&amount${j}=${amount}&woPiBalQnty${j}=${woPiBalQnty}&comments${j}=${comments}&bookCurrency${j}=${bookCurrency}&txtWarrentyExpDate${j}=${txtWarrentyExpDate}&txtSerial${j}=${txtSerial}&txtSerialQty${j}=${txtSerialQty}&brand${j}=${brand}&origin${j}=${origin}&model${j}=${model}&floorID${j}=${floorID}&roomID${j}=${roomID}&rackID${j}=${rackID}&shelfID${j}=${shelfID}&binID${j}=${binID}&updatedtlsid${j}=${updatedtlsid}&piWoDtlsId${j}=${piWoDtlsId}&prodId${j}=${prodId}`;
				}
				i++;
			});
				
			if(j<1)
			{
				alert('No data');return;
			}			
		
			var data=`action=save_update_delete&operation=${operation}&tot_row=${j}`+get_submitted_data_string('txt_mrr_no*cbo_company_id*cbo_receive_basis*txt_wo_pi_req*txt_wo_pi_req_id*txt_ref_no*txt_receive_date*cbo_currency_id*cbo_store_name*txt_exchange_rate*txt_challan_no*txt_challan_date_mst*cbo_source*cbo_pay_mode*cbo_supplier*cbo_receive_purpose*cbo_loan_party*txt_lc_no*hidden_lc_id*txt_sup_ref*txt_boe_mushak_challan_no*txt_boe_mushak_challan_date*txt_addi_popup*txt_addi_info*txt_remarks*update_id',"../../")+dataString;
			//alert(data);return;
			//freeze_window(operation);
			http.open("POST","requires/general_item_receive_v2_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange =fnc_general_receive_Reply_info;
		}
	}
	
	function fnc_general_receive_Reply_info()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);return;
			var reponse=trim(http.responseText).split('**');	
			if(reponse[0]==20 || reponse[0]==40 )
			{
				alert(reponse[1]); release_freezing();
				return;	
			}
			else
			{	
				show_msg(reponse[0]);
				if(reponse[0]==0 || reponse[0]==1 )
				{
					document.getElementById('update_id').value = reponse[1];
					document.getElementById('txt_mrr_no').value = reponse[2];

					$('#cbo_company_id').attr('disabled',true);
					$('#cbo_store_name').attr('disabled',true);
					$('#txt_wo_pi_req').attr('disabled',true);
					$('#cbo_receive_basis').attr('disabled',true);
					$('#cbo_receive_purpose').attr('disabled',true);

					var booking_id = $("#txt_wo_pi_req_id").val();
					var booking_no = $("#txt_wo_pi_req").val();
					
					var booking_without_order=0;
					load_details_data(booking_id,booking_no,booking_without_order,reponse[1]);
					set_button_status(1, permission, 'fnc_general_receive',1,1);	
				}
				if(reponse[0]==2)
				{
					show_msg(reponse[0]);
					reset_form('generalreceive_1','list_container_trims*list_fabric_desc_container','','','','');
					$('#cbo_company_id').attr("disabled",false);
					$('#cbo_store_name').attr("disabled",false);
					$('#txt_wo_pi_req').attr("disabled",false);
					$('#cbo_receive_basis').attr("disabled",false);
					set_button_status(0, permission, 'fnc_general_receive',1,1);
					release_freezing();
				}
			}
			release_freezing();	
		}
	}
	
	
	
	// Calculate ILE ----------
	const fn_calculate_ile = (item_category,item_group,rate,i) => {
		var company=$('#cbo_company_id').val();
		var source=$('#cbo_source').val();
		var responseHtml = return_ajax_request_value(company+'**'+source+'**'+rate+'**'+item_category+'**'+item_group, 'show_ile', 'requires/general_item_receive_controller');
		var splitResponse="";
		if(responseHtml!="")
		{
			splitResponse = responseHtml.split("**");
			$("#ilePersent_"+i).html((splitResponse[1]*1).toFixed(2));
		}
		else
		{
			$("#ilePersent_"+i).html('0.00');
		}		
	}
	
	const calculate = (i) => {
		var currency_id 	= $("#cbo_currency_id").val()*1;
		var exchangeRate 	= $("#txt_exchange_rate").val()*1;
		var quantity 		= $('#receiveqnty_'+i).val()*1;
		var bl_qnty 		= $('#woPiBalQnty_'+i).attr("title")*1;
		var rate			= $('#rate_'+i).attr("title")*1;
		var item_category   = $('#category_'+i).attr('title')*1;
		var item_group      = $('#group_'+i).attr('title')*1;

		fn_calculate_ile(item_category,item_group,rate,i);

		var ile_cost 		= $('#ilePersent_'+i).html()*1;
		var amount 			= quantity*1*(rate*1+ile_cost*1);

		var bookCurrency 	= (rate*1+ile_cost*1)*exchangeRate*1*quantity*1;
		if(quantity>bl_qnty)
		{
			alert("Receive Quantity Not Allow Over Balance Quantity");
			$('#receiveqnty_'+i).val("0.00");
			$('#amount_'+i).attr("title",0);
			$('#amount_'+i).html("0.00");

			var ddd={ dec_type:5, comma:0, currency:''}
			var numRow = $('table#tbl_fabric_desc_item tbody tr').length-1;
			math_operation( "tot_rcv_qnty", "receiveqnty_", "+", numRow,ddd );
			$('#receiveqnty_'+i).focus();
			return;
		}
		//alert(quantity+"=="+rate+"=="+amount+"=="+bookCurrency+"=="+ile_cost);
		$('#amount_'+i).html(number_format_common(amount,"","",1));
		$('#amount_'+i).attr("title",amount);
		$('#bookCurrency_'+i).html(number_format_common(bookCurrency,"","",1));
		$('#bookCurrency_'+i).attr("title",bookCurrency);
		
		var ddd={ dec_type:5, comma:0, currency:''}
		var numRow = $('table#tbl_fabric_desc_item tbody tr').length-1;
		math_operation( "tot_rcv_qnty", "receiveqnty_", "+", numRow,ddd );		
	}
	
	const load_exchange_rate = () => {
		var currency_id= $("#cbo_currency_id").val();
		var company_id= $("#cbo_company_id").val();
		if(currency_id>0) {
			get_php_form_data(currency_id+"**"+company_id, "get_library_exchange_rate", "requires/general_item_receive_v2_controller");
		}
	}

	const openmypage_addiInfo = () => {
		var title = "Additional Info Details";
		var pre_addi_info = $('#txt_addi_info').val();
		page_link='requires/general_item_receive_v2_controller.php?action=addi_info_popup&pre_addi_info='+pre_addi_info;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=700px, height=350px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var addi_info_string=this.contentDoc.getElementById("txt_string").value;
			$('#txt_addi_info').val(addi_info_string);
		}
	}
	
	const popup_serial = (i) => {
		var serialno = $("#txtSerial_"+i).val();
		var serialqty = $("#txtSerialQty_"+i).val();
		var serialString=serialno+'**'+serialqty;
		var page_link="requires/general_item_receive_v2_controller.php?action=serial_popup&serialString='"+serialString+"'";
		var title="Serial Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=400px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var txt_string=this.contentDoc.getElementById("txt_string").value; 
			var txt_qty=this.contentDoc.getElementById("txt_qty").value;
			$("#txtSerial_"+i).val(txt_string);
			$("#txtSerialQty_"+i).val(txt_qty);
		}
	}

	const fn_fill_qnty = () => {
		var i=1;
		if($('#check_qnty').is(':checked'))
		{
			$("#tbl_fabric_desc_item").find('tbody tr').each(function(index, element) {
				var receive_qnty=$(this).find('input[name="receiveqnty[]"]').val()*1;
				if(receive_qnty<=0)
				{
					$(this).find('input[name="receiveqnty[]"]').val(trim($(this).find('td[name="woPiBalQnty[]"]').text()));
					calculate(i);
					i++;
				}
            });
		}
		else
		{
			$("#tbl_fabric_desc_item").find('tbody tr').each(function(index, element) {
				$(this).find('input[name="receiveqnty[]"]').val("");
				$(this).find('input[name="receiveqnty[]"]').attr("placeholder",$(this).find('td[name="woPiBalQnty[]"]').text());
				calculate(i);
				i++;
            });
		}
	}
	

</script>
<body onLoad="set_hotkey();load_exchange_rate();">
<div style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission); ?>
    <form name="generalreceive_1" id="generalreceive_1">
    	<div style="width:1750px;">        
            <fieldset style="width:1750px">
            <legend>General Item Receive Master Part</legend>
			<fieldset style="width:1200px">
            <table cellpadding="0" cellspacing="2" width="100%" id="tbl_master">
                <tr>
                    <td align="right" colspan="5"><strong>Receive/MRR</strong></td>
                    <td colspan="5">                        
                        <input type="text" name="txt_mrr_no" id="txt_mrr_no" class="text_boxes" style="width:145px" placeholder="Double Click" onDblClick="open_mrrpopup();" >
						<input type="hidden" name="update_id" id="update_id" />
						<input type="hidden" id="hidden_posted_in_account" name="hidden_posted_in_account" />
                    </td>
                </tr>
                <tr>
                	<td colspan="10" height="10"></td>
                </tr>
                <tr>
                    <td width="90" class="must_entry_caption"> Company </td>
                    <td width="150">
						<? 
						//load_room_rack_self_bin('requires/general_item_receive_v2_controller*4', 'store','store_td', this.value);
						echo create_drop_down( "cbo_company_id", 142, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/general_item_receive_v2_controller', this.value, 'load_drop_down_supplier', 'supplier' );load_drop_down( 'requires/general_item_receive_v2_controller', this.value, 'load_drop_down_loan_party', 'loan_party_td');load_drop_down('requires/general_item_receive_v2_controller', this.value, 'load_drop_down_store','store_td');" );
                        ?>
                        <input type="hidden" name="variable_string_inventory" id="variable_string_inventory" />
                        <input type="hidden" id="is_rate_optional" name="is_rate_optional">
                        <input type="hidden" id="variable_lot" name="variable_lot" />
                    </td>
                    <td class="must_entry_caption" width="90"> Receive Basis </td>
                    <td width="150">
                        <? 
                        	echo create_drop_down("cbo_receive_basis",142,$receive_basis_arr,"",1,"-- Select --",0,"set_receive_basis(1);","",'1,2,7');
                        ?>
                    </td>
                    <td width="90" class="must_entry_caption">WO/PI/Req.No</td>
                    <td width="150"><input class="text_boxes"  type="text" name="txt_wo_pi_req" id="txt_wo_pi_req" onDblClick="openmypage_wo_pi_popup('xx','Order Search')"  placeholder="Double Click" style="width:130px;"  readonly />
                    <input type="hidden" id="txt_wo_pi_req_id" name="txt_wo_pi_req_id" value="" />
                    <input type="hidden" id="txt_ref_no" name="txt_ref_no" value="" />
                    </td>
                	<td width="90" class="must_entry_caption"> Received Date </td>
                    <td width="150">
                        <input class="datepicker" type="text" style="width:130px" name="txt_receive_date" id="txt_receive_date" value="<? echo date("d-m-Y")?>" onChange="check_exchange_rate();"/>
                    </td>
                    <td width="90" >Currency</td>
                    <td width="150"> 
                        <?
                           echo create_drop_down( "cbo_currency_id", 142,$currency,"", 1, "Select Currency ", 0, "load_exchange_rate();",1 );
                        ?>
                    </td>
                </tr> 
                <tr>
                	<td class="must_entry_caption">Store Name</td>
                    <td id="store_td"><?=create_drop_down( "cbo_store_name", 142, $blank_array,"",1, "--Select store--", 1, "" );?></td>
                    <td class="must_entry_caption">Exchange Rate</td>
                    <td>
                        <input type="text" name="txt_exchange_rate" id="txt_exchange_rate" class="text_boxes_numeric" style="width:130px" disabled/>
                    </td>
                    <td> Challan/Bill No</td>
                    <td><input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:130px" ></td>
                    <td>Challan/Bill Date</td>
                    <td><input type="text" name="txt_challan_date_mst" id="txt_challan_date_mst" class="datepicker" style="width:130px" readonly></td>
                    <td class="must_entry_caption">Source</td>
                    <td id="sources">
                    <?
                        echo create_drop_down( "cbo_source", 142, $source,"", 1, "-- Select --", $selected, "",1 );
                    ?>
                    </td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Pay Mode</td>
                    <td ><?=create_drop_down( "cbo_pay_mode", 142, $pay_mode,"", 1, "-- Select --", $selected, "",1 );?></td>
                    <td width="" class="must_entry_caption">Supplier</td>
                    <td id="supplier" >
                    <?
                        echo create_drop_down( "cbo_supplier", 142, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and  b.party_type in(1,5,6,7,8,30,36,37,39,92) and a.status_active=1 and a.is_deleted=0  group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",1 );
                    ?>
                    </td>
                    <td class="must_entry_caption">Receive Purpose</td>
                   	<td>
                        <?
                            echo create_drop_down( "cbo_receive_purpose", 142, $general_issue_purpose,"", 1, "--Select--", $selected, "","","5");
                        ?>
                   	</td>
                    <td>Loan Party </td>
                    <td id="loan_party_td">
                    <?
                        echo create_drop_down( "cbo_loan_party", 142, $blank_array,"", 1, "-- Select Loan Party --", $selected, "","","" );
                    ?>
                    </td>
                    <td> L/C No</td>
                    <td id="lc_no">
                    <input class="text_boxes"  type="text" name="txt_lc_no" id="txt_lc_no" style="width:130px;" placeholder="Display" onDblClick="popuppage_lc()" readonly disabled  />
                    <input type="hidden" name="hidden_lc_id" id="hidden_lc_id" />
                    </td>
                </tr>
                <tr>
					<td>Supplier Ref</td>       
                    <td> 
                        <input type="text" name="txt_sup_ref" id="txt_sup_ref" class="text_boxes" style="width:130px">
                    </td>
                	<td>BOE/Mushak Challan No</td>
                    <td> 
                        <input type="text" name="txt_boe_mushak_challan_no" id="txt_boe_mushak_challan_no" class="text_boxes" style="width:130px">
                    </td>
                    <td>BOE/Mushak Challan Date</td>                                              
                    <td> 
                        <input type="text" name="txt_boe_mushak_challan_date" id="txt_boe_mushak_challan_date" class="datepicker" style="width:130px">
                    </td>                    
                    <td >Addi. Info</td>
                    <td>
                    <input type="text" id="txt_addi_popup" name="txt_addi_popup" class="text_boxes" onDblClick="openmypage_addiInfo()"  placeholder="Double Click" style="width:130px;" readonly >
                    <input type='hidden' id="txt_addi_info" name="txt_addi_info" value="">
                    </td> 
                    <td >Remarks</td>
                    <td>
                    <input type="text" id="txt_remarks" name="txt_remarks" class="text_boxes" style="width:130px;" maxlength="255" >
                    </td>
                </tr>
				<tr>
					<td>File</td>
					<td><input type="button" class="image_uploader" style="width:130px" id="btn_fileadd" value="CLICK TO ADD FILE" onClick="file_uploader ( '../../', document.getElementById('txt_recieved_id').value,'', 'general_item_receive_v2', 2 ,1)"></td>        
                </tr>
            </table>
			</fieldset>

            <fieldset style="width:1950px; margin-top:10px;">
                 	<legend>General Item Receive Entry Details Part</legend>
                    <? $i=1; ?>
                    	<table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" id="tbl_fabric_desc_item">
                        	<thead>
								<tr>
									<th width="30">SL</th>
                                    <th width="100">Item Category</th>
									<th width="100">Item Group</th>
									<th width="150">Item Description</th>
									<th width="60">Item Size</th>					
									<th width="70">Item Sub Group</th>
									<th width="60">Item Number</th>
                                    <th width="60">Item Code</th>
									<th width="50">UOM</th>
                                    <th width="70">WO/PI/Req Qty</th>
                                    <th width="70" class="must_entry_caption"><input type="checkbox" checked id="check_qnty" name="check_qnty" onChange="fn_fill_qnty()"/><br>Receive Qty</th>
									<th width="60">Lot</th>
									<th width="60" class="must_entry_caption">Rate</th>
									<th width="50">ILE%</th>
                                    <th width="80" class="must_entry_caption">Amount</th>
                                    <th width="70">Previous Receive Qty</th>
									<th width="70">Balance PI/WO/Req Qty</th>
                                    <th width="100">Comments</th>
									<th width="80">Book Currency</th>
                                    <th width="60">Warranty Exp. Date</th>
									<th width="60">Serial No</th>
									<th width="60">Brand</th>
									<th width="60">Origin</th>					
									<th width="60">Model</th>
                                    <th width="50"><input type="checkbox" checked id="floorIds" name="floorIds"/><br>Floor</th>
									<th width="50"><input type="checkbox" checked id="roomIds" name="roomIds"/><br>Room</th>
									<th width="50"><input type="checkbox" checked id="rackIds" name="rackIds"/><br>Rack</th>
									<th width="50"><input type="checkbox" checked id="shelfIds" name="shelfIds"/><br>Shelf</th>
									<th><input type="checkbox" checked id="binIds" name="binIds"/><br>Bin/Box</th>
								</tr>
                            </thead>
                            <tbody id="list_fabric_desc_container">
                            	<tr id="row_1" align="center">
                                    <td id="sl_1">
										<input type="hidden" name="updatedtlsid[]" id="updatedtlsid_1" value="" readonly>
										<input type="hidden" name="piWoDtlsId[]" id="piWoDtlsId_1" value="" readonly>
										<input type="hidden" name="prodId[]" id="prodId_1" value="" readonly>
									</td>
                                    <td id="category_1"></td>
                                    <td id="group_1"></td>
                                    <td id="description_1"></td>
                                    <td id="size_1"></td>
                                    <td id="subGroup_1"></td>
                                    <td id="itemNumber_1"></td>
                                    <td id="itemCode_1"></td>
                                    <td id="uom_1"></td>
                                    <td id="woPiReqQnty_1" align="right"></td>
                                    <td id="tdreceiveqnty_1"><input type="text" name="receiveqnty[]" id="receiveqnty_1" class="text_boxes_numeric" style="width:65px;" value="" onBlur="calculate(1);"/></td>
									<td id="lot_1"><input type="text" name="txtLot[]" id="txtLot_" class="text_boxes" style="width:55px;" value="" /></td>
                                    <td id="rate_1"><input type="text" name="txtRate[]" id="txtRate_1" class="text_boxes_numeric" style="width:55px;" value="" onBlur="calculate(1);"/></td>
                                    <td id="ilePersent_1" value="" align="right"></td>
                                    <td id="amount_1" value="" align="right"></td>
                                    <td id="prevRcvQnty_1" align="right"></td>
                                    <td id="woPiBalQnty_1" name="woPiBalQnty[]" align="right"></td>
                                    <td id="omments_1"><input type="text" name="txtComments[]" id="txtComments_1" class="text_boxes" style="width:90px;" value="" /></td>
                                    <td id="bookCurrency_1" align="right"></td>							
                                    <td id="tdWarrantyExpDate_1"><input type="text" name="txtWarrentyExpDate[]" id="txtWarrentyExpDate_1" class="datepicker" style="width:55px;" value="" placeholder="Select Date"/></td>
									<td id="serial_1">
										<input type="text" name="txtSerial[]" id="txtSerial_1" class="text_boxes" style="width:55px;" placeholder="Double Click" onDblClick="popup_serial(1)" />
		                                <input name="txtSerialQty[]" id="txtSerialQty_1" type="hidden" />
									</td>
                                    <td id="brand_1"></td>
                                    <td id="origin_1"></td>
                                    <td id="model_1"></td>
                                    <td align="center" id="floor_td_to" class="floor_td_to"><p>
										<? 										
										$i=1; $argument = "'".$i.'_0'."'";
										echo create_drop_down( "cbo_floor_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_room(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cbo_floor_to');",0,"","","","","","","cbo_floor_to[]" ,"onchange_void"); ?>
                                    </p></td>
                                    <td align="center" id="room_td_to"><p>
										<? $argument = "'".$i.'_1'."'";
										echo create_drop_down( "cbo_room_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_rack(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cbo_room_to');",0,"","","","","","","cbo_room_to[]","onchange_void" ); ?>
                                    </p>
                                    </td>
                                    <td align="center" id="rack_td_to"><p>
										<? $argument = "'".$i.'_2'."'";
										echo create_drop_down( "txt_rack_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_shelf(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txt_rack_to');",0,"","","","","","","txt_rack_to[]","onchange_void" ); ?>
                                    </p></td>
                                    <td align="center" id="shelf_td_to"><p>
										<? $argument = "'".$i.'_3'."'";
										echo create_drop_down( "txt_shelf_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_bin(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txt_shelf_to');",0,"","","","","","","txt_shelf_to[]","onchange_void" ); ?>
                                    </p></td>
                                    <td align="center" id="bin_td_to"><p>
										<? $argument = "'".$i.'_4'."'"; 
										echo create_drop_down( "txt_bin_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "copy_all($argument);",0,"","","","","","","txt_bin_to[]","onchange_void" ); ?>
                                    </p>                                    
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>					
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>Total</th>
                                <th><input type="text" id="tot_rcv_qnty" name="tot_rcv_qnty" style="width:65px;" class="text_boxes_numeric" readonly disabled /></th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>					
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
								<th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>					
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                            </tfoot>
                        </table>
                     <!--<div id="list_container"></div>--> 
                </fieldset>
                 <table width="100%">
                    <tr>
                        <td width="80%" align="center"> 
                        <?
						//cbo_company_id*cbo_receive_basis*txt_receive_date*cbo_store_name*cbo_currency_id*floorIds*roomIds*rackIds*shelfIds*binIds 
                        echo load_submit_buttons($permission, "fnc_general_receive", 0,1,"reset_form('generalreceive_1','list_container_trims*list_fabric_desc_container','','','set_receive_basis(1);','')",1);
                        
                        ?>
						<!--<input type="button" id="btn_print_2" name="btn_print_2" value="Print2" class="formbutton" style="width:100px;" onClick="fnc_trims_receive(5)" />-->
                        <input type="hidden" id="is_posted_account" name="is_posted_account" value=""/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" align="center">
                            <div id="accounting_posted_status" style=" color:red; font-size:24px;";  ></div>
                        </td>
                    </tr>
                </table> 
            <br>
            <div style="width:650px;" id="list_container_trims"></div>
		</fieldset>
        </div>  
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$('input[name^="receiveqnty"]').live('keydown', function(e) {
	
	switch (e.keyCode) {
			case 38:
				var target_id_arr=e.target.id.split('_');
				var row_num=parseInt(target_id_arr[1])-1;
				//alert(row_num);
				//$('#receiveqnty_'+row_num).focus();
				$('#receiveqnty_'+row_num).select();
				break;
			case 40:
				var target_id_arr=e.target.id.split('_');
				var row_num=parseInt(target_id_arr[1])+1;
				//alert(row_num);
				//$('#receiveqnty_'+row_num).focus();
				$('#receiveqnty_'+row_num).select();
				break;
	}
});

</script>
</html>
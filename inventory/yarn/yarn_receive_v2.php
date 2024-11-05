<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Receive V2 Entry 
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	15/05/2022
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
$payment_yes_no=array(0=>"yes", 1=>"No");
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yarn Receive V2 Entry", "../../", 1, 1,'','',''); 

?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	
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
		
		var list_view_wo =trim(return_global_ajax_value( recieve_basis, 'mrr_details', '', 'requires/yarn_receive_v2_controller'));
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
			load_drop_down( 'requires/yarn_receive_v2_controller', company, 'load_drop_down_supplier_from_issue', 'supplier' );
			if($('#cbo_supplier option').length==2)
			{
				$('#cbo_supplier').val($('#cbo_supplier option:last').val());
			}
			$('#cbo_party').attr('disabled','disabled');
		}
		else if(receive_purpose==5)
		{
			load_drop_down( 'requires/yarn_receive_v2_controller',company, 'load_drop_down_supplier', 'supplier' );
			load_drop_down( 'requires/yarn_receive_v2_controller',company, 'load_drop_down_party', 'loanParty' );
			$('#cbo_party').removeAttr('disabled','disabled');
			$('#loanParty_td').css('color','blue');
		}
		else if(receive_purpose==16)
		{
			load_drop_down( 'requires/yarn_receive_v2_controller',company, 'load_drop_down_supplier', 'supplier' );
			$('#cbo_party').attr('disabled','disabled');
			$('#cbo_color').val($('#cbo_color option:last').val());
			$('#cbo_color').attr('disabled','disabled');
		}
		else
		{
			load_drop_down( 'requires/yarn_receive_v2_controller',company, 'load_drop_down_supplier', 'supplier' );
			$('#cbo_party').attr('disabled','disabled');
		}
	
		if(receive_basis==4) $('#cbo_supplier').removeAttr('disabled','disabled');
		else $('#cbo_supplier').attr('disabled','disabled');
	
		//$("#tbl_child").find("input[type=text],input[type=hidden],select").val('');
		//$('#cbo_uom').val(12);
		//$('#percentage1').val(100);
	}

	
	function openmypage_wo_pi_popup()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var recieve_basis = $('#cbo_receive_basis').val();
		var update_id = $('#update_id').val();
		var exchange_rate = $('#txt_exchange_rate').val()*1;
		var cbo_currency_id = $('#cbo_currency_id').val();
		var cbo_source = $('#cbo_source').val();
		var cbo_supplier_name = $('#cbo_supplier').val();
		var cbo_receive_purpose = $('#cbo_receive_purpose').val();
		//alert(exchange_rate);
		if (form_validation('cbo_company_id*cbo_receive_basis*cbo_receive_purpose','Company*basis*purpose')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'WO/PI/GRN Selection Form';	
			var page_link = 'requires/yarn_receive_v2_controller.php?cbo_company_id='+cbo_company_id+'&recieve_basis='+recieve_basis+'&update_id='+update_id+'&cbo_currency_id='+cbo_currency_id+'&cbo_source='+cbo_source+'&cbo_supplier_name='+cbo_supplier_name+'&cbo_receive_purpose='+cbo_receive_purpose+'&action=wo_pi_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1120px,height=450px,center=1,resize=1,scrolling=0','../');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var theemail=this.contentDoc.getElementById("hidden_wo_pi_id").value;	 //Knit Id for Kintting Plan
				var theename=this.contentDoc.getElementById("hidden_wo_pi_no").value; //all data for Kintting Plan
				var all_data=this.contentDoc.getElementById("hidden_data").value; //Access form field with id="emailfield"
				var rcv_basis=this.contentDoc.getElementById("receive_basis").value; //Access form field with id="emailfield"
				//alert(theemail+"**"+theename+"**"+booking_without_order+"**"+all_data)
				var data=all_data.split("**");

				freeze_window(5);
				set_receive_basis(0);
				
				$('#txt_booking_pi_no').val(theename);
				$('#txt_wo_pi_id').val(theemail);
				$('#grn_receive_basis').val(rcv_basis);
				$('#cbo_supplier').val(data[0]);
				$('#cbo_currency_id').val(data[1]);	
				$('#cbo_source').val(data[2]);
				$('#grn_wo_pi_id').val(data[3]);	
				$('#grn_wo_pi_no').val(data[4]);
				$('#cbo_receive_basis').attr("disabled",true);
				$('#cbo_receive_purpose').attr("disabled",true);
				$('#cbo_supplier').attr("disabled",true);
				$('#cbo_currency_id').attr("disabled",true);
				var exchange_rate = $('#txt_exchange_rate').val()*1;
				var cbo_receive_purpose = $('#cbo_receive_purpose').val()*1;
				
				load_exchange_rate();
				//alert("test");return;
				var yarn_receive_basis=$('#cbo_receive_basis').val();
				//alert(exchange_rate);
				
				show_list_view(theemail+"**"+yarn_receive_basis+"**"+data[3]+"**"+cbo_company_id+"**"+data[2]+"**"+exchange_rate+"**"+cbo_receive_purpose+"**"+rcv_basis, 'show_fabric_desc_listview', 'list_fabric_desc_container', 'requires/yarn_receive_v2_controller', 'setFilterGrid("list_fabric_desc_container",-1)' ) ;
				$('#check_qnty').attr('checked',true);
				//$('#txt_booking_pi_no').attr('disabled',true);
				$('#cbo_store_name').val(0);
				calculate(1);
				release_freezing();
			}
		}
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
		show_list_view(booking_pi_id+"**"+recieve_basis+"**"+booking_without_order+"**"+cbo_company_id+"**"+cbo_source+"**"+exchange_rate+"**"+mst_id+"**"+grn_mst_id+"**"+cbo_store_name+"**"+recieve_purpose+"**"+grn_receive_basis, 'show_fabric_desc_listview_update', 'list_fabric_desc_container', 'requires/yarn_receive_v2_controller', 'setFilterGrid("list_fabric_desc_container",-1)' ) ;
		calculate(1);
		set_button_status(1, permission, 'fnc_trims_receive',1,1);
	}
	
	
	
	
	function fnc_trims_receive(operation)
	{
		if(operation==4)
		{
			alert("Under Construction....");
			 //var report_title=$( "div.form_caption" ).html();
			 //print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "trims_receive_entry_print", "requires/yarn_receive_v2_controller" ) 
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
			
			if( form_validation('cbo_company_id*cbo_receive_basis*cbo_receive_purpose*txt_receive_date*txt_receive_chal_no*cbo_supplier*txt_exchange_rate*cbo_store_name*cbo_source','Company*Receive Basis*Receive Purpose*Received Date*Challan No*Supplier Name*Exchange Rate*strore*Source')==false )
			{
				return;
			}
			
			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_receive_date').val(), current_date)==false)
			{
				alert("Receive Date Can not Be Greater Than Current Date");
				return;
			}
			
			if(($('#cbo_receive_basis').val()==1 || $('#cbo_receive_basis').val()==2) && $('#txt_booking_pi_no').val()=="")
			{
				alert("Please Select WO/PI No");
				$('#txt_booking_pi_no').focus();
				return;
			}
			
			var meterial_source=$('#meterial_source').val();
			var j=0; var i=1; var dataString='';
			$("#tbl_fabric_desc_item").find('tbody tr').not(':first').each(function()
			{
				var count=$('#count_'+i).attr('title');
				var composition=$('#composition_'+i).attr('title');
				var comPersent=$('#comPersent_'+i).attr('title');
				var yarnType=$('#yarnType_'+i).attr('title');
				var color=$('#color_'+i).attr('title');
				var TxtLot=$('#TxtLot_'+i).val();
				var TxtBrand=$('#TxtBrand_'+i).val();
				
				var floorID=$('#cbo_floor_to_'+i).val();
				var roomID=$('#cbo_room_to_'+i).val();
				var rackID=$('#txt_rack_to_'+i).val();
				var shelfID=$('#txt_shelf_to_'+i).val();
				var binID=$('#txt_bin_to_'+i).val();
				
				var receiveqnty=$('#receiveqnty_'+i).val();
				//var itemdescription=encodeURIComponent($('#item_descrip_'+i).html());
				//var gmtscolorid=$(this).find('input[name="gmtscolorid[]"]').val();
				var greyqnty=$('#greyqnty_'+i).val();
				var uom=$('#uom_'+i).attr('title');
				var rate=$('#rate_'+i).attr('title');
				var avgRate=$('#avgRate_'+i).attr('title');
				var greyRate=$('#greyRate_'+i).attr('title');
				var DCharge=$('#DCharge_'+i).html();
				
				var ilePersent=$('#ilePersent_'+i).attr('title');
				var amount=$('#amount_'+i).attr('title');
				var bookCurrency=$('#bookCurrency_'+i).attr('title');
				var woPiBalQnty=$('#woPiBalQnty_'+i).attr('title');
				var overRcvQnty=$('#overRcvQnty_'+i).attr('title');
				
				//var cbo_floor_to=$(this).find('input[name="cbo_floor_to[]"]').val();
				var noOfBag=$('#noOfBag_'+i).val();
				var conPerBag=$('#conPerBag_'+i).val();
				var loseCone=$('#loseCone_'+i).val();
				var wetPerBag=$('#wetPerBag_'+i).val();
				var wetPerCon=$('#wetPerCon_'+i).val();
				var productCode=$('#productCode_'+i).html();
				var updatedtlsid=$('#updatedtlsid_'+i).val();
				var piWoDtlsId=$('#piWoDtlsId_'+i).val();
				var previousprodid=$('#previousprodid_'+i).val();
				var dtlsRemarks=$('#dtlsRemarks_'+i).val();
				
				//alert(productCode);
				
				if(receiveqnty>0 && TxtLot !="" && TxtBrand !="" )	
				{
					j++;
					dataString+='&count' + j + '=' + count + '&composition' + j + '=' + composition + '&comPersent' + j + '=' + comPersent + '&yarnType' + j + '=' + yarnType + '&color' + j + '=' + color+ '&TxtLot' + j + '=' + TxtLot + '&TxtBrand' + j + '=' + TxtBrand + '&floorID' + j + '=' + floorID + '&roomID' + j + '=' + roomID + '&rackID' + j + '=' + rackID + '&shelfID' + j + '=' + shelfID + '&binID' + j + '=' + binID  + '&receiveqnty' + j + '=' + receiveqnty+ '&greyqnty' + j + '=' + greyqnty + '&uom' + j + '=' + uom+ '&rate' + j + '=' + rate + '&avgRate' + j + '=' + avgRate + '&greyRate' + j + '=' + greyRate + '&DCharge' + j + '=' + DCharge + '&ilePersent' + j + '=' + ilePersent + '&amount' + j + '=' + amount + '&bookCurrency' + j + '=' + bookCurrency+ '&woPiBalQnty' + j + '=' + woPiBalQnty+ '&overRcvQnty' + j + '=' + overRcvQnty+ '&noOfBag' + j + '=' + noOfBag+ '&conPerBag' + j + '=' + conPerBag+ '&loseCone' + j + '=' + loseCone+ '&wetPerBag' + j + '=' + wetPerBag+ '&wetPerCon' + j + '=' + wetPerCon+ '&productCode' + j + '=' + productCode+ '&dtlsRemarks' + j + '=' + dtlsRemarks+ '&updatedtlsid' + j + '=' + updatedtlsid+ '&piWoDtlsId' + j + '=' + piWoDtlsId+ '&previousprodid' + j + '=' + previousprodid;
				
				}
				
				i++;
			});
			
			//alert(dataString);return;
				
			if(j<1)
			{
				alert('No data');return;
			}
			
			//alert(dataString);return;
			
			var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_recieved_id*cbo_company_id*cbo_receive_basis*cbo_receive_purpose*txt_receive_date*txt_receive_chal_no*cbo_store_name*cbo_supplier*cbo_party*cbo_currency_id*txt_exchange_rate*cbo_source*txt_mst_remarks*txt_booking_pi_no*txt_wo_pi_id*grn_receive_basis*grn_wo_pi_no*grn_wo_pi_id*update_id',"../../")+dataString;
		
			//alert(data); return;
			
			freeze_window(operation);
			http.open("POST","requires/yarn_receive_v2_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange =fnc_trims_receive_Reply_info;
		}
	}
	
	function fnc_trims_receive_Reply_info()
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
					document.getElementById('txt_recieved_id').value = reponse[2];
					$('#cbo_company_id').attr('disabled',true);
					$('#cbo_store_name').attr('disabled',true);
					$('#txt_booking_pi_no').attr('disabled',true);
					$('#cbo_receive_basis').attr('disabled',true);
					$('#cbo_receive_purpose').attr('disabled',true);
					if($('#cbo_receive_basis').val()*1==19)
					{
						var booking_id=$("#grn_wo_pi_id").val();
						var booking_no=$("#grn_wo_pi_no").val();
					}
					else
					{
						var booking_id=$("#txt_wo_pi_id").val();
						var booking_no=$("#txt_booking_pi_no").val();
					}
					
					var booking_without_order=0;
					load_details_data(booking_id,booking_no,booking_without_order,reponse[1]);
					set_button_status(1, permission, 'fnc_trims_receive',1,1);	
				}
				if(reponse[0]==2)
				{
					show_msg(reponse[0]);
					reset_form('trimsreceive_1','list_container_trims*list_fabric_desc_container','','','','');
					$('#cbo_company_id').attr("disabled",false);
					$('#cbo_store_name').attr("disabled",false);
					$('#txt_booking_pi_no').attr("disabled",false);
					$('#cbo_receive_basis').attr("disabled",false);
					set_button_status(0, permission, 'fnc_trims_receive',1,1);
					release_freezing();	
				}
			}
			release_freezing();	
		}
	}
	
	
	
	function yarn_receive_popup()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var page_link='requires/yarn_receive_v2_controller.php?cbo_company_id='+cbo_company_id+'&action=yarn_receive_popup_search';
			var title='Yarn Receive Form';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1020px,height=420px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var trims_recv_id=this.contentDoc.getElementById("hidden_recv_id").value;

				if(trims_recv_id!="")
				{
					freeze_window(5);
					reset_form('trimsreceive_1','list_container_trims*list_fabric_desc_container','','','','');
					
					var posted_in_account=this.contentDoc.getElementById("hidden_posted_in_account").value; // is posted accounct
					$("#is_posted_account").val(posted_in_account);
					if(posted_in_account==1) document.getElementById("accounting_posted_status").innerHTML="Already Posted In Accounting.";
					else 	document.getElementById("accounting_posted_status").innerHTML="";
					
					get_php_form_data(trims_recv_id, "populate_data_from_trims_recv", "requires/yarn_receive_v2_controller" );
					if($('#cbo_receive_basis').val()*1==19)
					{
						var booking_id=$("#grn_wo_pi_id").val();
						var booking_no=$("#grn_wo_pi_no").val();
					}
					else
					{
						var booking_id=$("#txt_wo_pi_id").val();
						var booking_no=$("#txt_booking_pi_no").val();
					}
					
					var booking_without_order=0;
					load_details_data(booking_id,booking_no,booking_without_order,trims_recv_id);
					//$('#check_qnty').attr('checked',true);
					$('#txt_booking_pi_no').attr('disabled',true);
					$('#cbo_receive_basis').attr('disabled',true);
					set_button_status(1, permission, 'fnc_trims_receive',1,1);	
					release_freezing();
				}
			}
		}
	}
	
	function calculate(i)
	{
		var currency_id 	= $("#cbo_currency_id").val()*1;
		var exchangeRate 	= $("#txt_exchange_rate").val()*1;
		var quantity 		= $('#receiveqnty_'+i).val()*1;
		var bl_qnty 		= $('#woPiBalQnty_'+i).attr("title")*1;
		var rate			= $('#rate_'+i).attr("title")*1;	 
		var ile_cost 		= ($('#ilePersent_'+i).attr("title")*1)*rate;
		var amount 			= quantity*(rate+ile_cost);
		var amount_t 		= quantity*rate; 
		var bookCurrency 	= (rate*1+ile_cost*1)*exchangeRate*1*quantity*1;
		//alert(bookCurrency+"-"+rate*1+"-"+ile_cost*1+"-"+exchangeRate*1+"-"+quantity*1);
		if(quantity>bl_qnty)
		{
			alert("Receive Quantity Not Allow Over Balance Quantity");
			$('#receiveqnty_'+i).val(0);
			$('#amount_'+i).attr("title",0);
			$('#amount_'+i).html("0");
			//$('#bookCurrency_'+i).attr("title",0);
			//$('#bookCurrency_'+i).html("0");
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
	
	function load_exchange_rate()
	{
		var currency_id= $("#cbo_currency_id").val();
		var company_id= $("#cbo_company_id").val();
		if(currency_id>0) get_php_form_data(currency_id+"**"+company_id, "get_library_exchange_rate", "requires/yarn_receive_v2_controller" );
	}
	
	
	function fn_load_floor(store_id)
	{		
		var com_id=$('#cbo_company_id').val();
		//var location_id=$('#cbo_location_name').val();
		//var all_data=com_id + "__" + store_id + "__" + location_id;
		var all_data=com_id + "__" + store_id;
		//alert(all_data);return;
		var floor_result = return_global_ajax_value(all_data, 'floor_list', '', 'requires/yarn_receive_v2_controller');
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
		// alert(floor_id);return;
		var com_id=$('#cbo_company_id').val();
		//var location_id=$('#cbo_location_name').val();
		var location_id=0;
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
		//alert(all_data);return;
		var room_result = return_global_ajax_value(all_data, 'room_list', '', 'requires/yarn_receive_v2_controller');
		var tbl_length=$('#tbl_fabric_desc_item tbody tr').length;
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

	function fn_load_rack(room_id, sequenceNo)
	{
		// alert(floor_id);return;
		var com_id=$('#cbo_company_id').val();
		//var location_id=$('#cbo_location_name').val();
		var location_id=0;
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + room_id;
		//alert(all_data);return;
		var rack_result = return_global_ajax_value(all_data, 'rack_list', '', 'requires/yarn_receive_v2_controller');
		var tbl_length=$('#tbl_fabric_desc_item tbody tr').length;
		//alert(rack_result+"="+tbl_length);//return;
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
		// alert(floor_id);return;
		var com_id=$('#cbo_company_id').val();
		//var location_id=$('#cbo_location_name').val();
		var location_id=0;
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + rack_id;
		//alert(all_data);return;
		var shelf_result = return_global_ajax_value(all_data, 'shelf_list', '', 'requires/yarn_receive_v2_controller');
		var tbl_length=$('#tbl_fabric_desc_item tbody tr').length;
		//alert(shelf_result+"="+tbl_length);//return;
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
		// alert(floor_id);return;
		var com_id=$('#cbo_company_id').val();
		//var location_id=$('#cbo_location_name').val();
		var location_id=0;
		var store_id=$('#cbo_store_name').val();
		var all_data=com_id + "__" + location_id + "__" + store_id + "__" + shelf_id;
		//alert(all_data);return;
		var shelf_result = return_global_ajax_value(all_data, 'bin_list', '', 'requires/yarn_receive_v2_controller');
		var tbl_length=$('#tbl_fabric_desc_item tbody tr').length;
		//alert(shelf_result+"="+tbl_length);//return;
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

</script>
<body onLoad="set_hotkey();load_exchange_rate();">
<div style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission); ?>
    <form name="trimsreceive_1" id="trimsreceive_1">
    	<div style="width:1750px;">        
            <fieldset style="width:1750px">
            <legend>Yarn Receive Entry Master Part</legend>
			<fieldset style="width:1200px">
            <table cellpadding="0" cellspacing="2" width="100%">
                <tr>
                    <td align="right" colspan="3"><strong> Yarn Receive/MRR </strong></td>
                    <td>
                        <input type="hidden" name="update_id" id="update_id" />
                        <input type="text" name="txt_recieved_id" id="txt_recieved_id" class="text_boxes" style="width:145px" placeholder="Double Click" onDblClick="yarn_receive_popup();" >
                    </td>
                </tr>
                <tr>
                	<td colspan="6" height="10"></td>
                </tr>
                <tr>
                    <td width="90" class="must_entry_caption"> Company </td>
                    <td width="150">
						<? 
						//load_room_rack_self_bin('requires/yarn_receive_v2_controller*4', 'store','store_td', this.value);
						echo create_drop_down( "cbo_company_id", 142, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down( 'requires/yarn_receive_v2_controller', this.value, 'load_drop_down_supplier', 'supplier' );load_drop_down( 'requires/yarn_receive_v2_controller', this.value, 'load_drop_down_store', 'store_td');reset_form('trimsreceive_1','list_container_trims*list_fabric_desc_container','','','set_receive_basis(0);','cbo_company_id*cbo_receive_basis*cbo_receive_purpose*txt_receive_date*cbo_store_name*cbo_currency_id*floorIds*roomIds*rackIds*shelfIds*binIds');" );
                        ?>
                        <input type="hidden" id="variable_recv_level" name="variable_recv_level" />
                    </td>
                    <td class="must_entry_caption" width="90"> Receive Basis </td>
                    <td width="150">
                        <? 
                        	echo create_drop_down("cbo_receive_basis",142,$receive_basis_arr,"",1,"-- Select --",0,"set_receive_basis(1);","",'1,2,19');
                        ?>
                    </td>
                    <td width="90" class="must_entry_caption">Receive Purpose</td>
                   	<td width="150">
                        <?
                            echo create_drop_down( "cbo_receive_purpose", 142, $yarn_issue_purpose,"", 1, "-- Select Purpose --", 16, "load_supplier();", "","2,5,6,7,12,15,16,38,43,46,50,51");
                        ?>
                   	</td>
                	<td width="90" class="must_entry_caption"> Received Date </td>
                    <td width="150">
                        <input class="datepicker" type="text" style="width:130px" name="txt_receive_date" id="txt_receive_date" value="<? echo date("d-m-Y")?>" />
                    </td>
                    <td width="90" class="must_entry_caption"> Challan No </td>
                    <td width="150">
                        <input type="text" name="txt_receive_chal_no" id="txt_receive_chal_no" class="text_boxes" style="width:130px" >
                    </td>
                </tr> 
                <tr>
                    
                    <td class="must_entry_caption" id="supplier_td"> Supplier </td>
                    <td id="supplier">
                        <?
                          echo create_drop_down( "cbo_supplier", 142, $blank_array,"", 1, "--- Select Supplier ---", $selected, "",1);
                        ?>
                    </td>
                    <td> Loan Party </td>
                    <td id="loanParty">
                        <?
                            echo create_drop_down( "cbo_party", 142, $blank_array,"", 1, "--- Select Party ---", $selected, "",1);
                        ?>
                    </td>
                    <td>Currency</td>
                    <td> 
                        <?
                           echo create_drop_down( "cbo_currency_id", 142,$currency,"", 1, "Select Currency ", 0, "load_exchange_rate();",1 );
                        ?>
                    </td>
                    <td class="must_entry_caption">Exchange Rate</td>
                    <td>
                        <input type="text" name="txt_exchange_rate" id="txt_exchange_rate" class="text_boxes_numeric" style="width:130px" onBlur="fn_calile()" disabled/>
                    </td>
                    <td>Remarks</td>
                    <td><input type="text" id="txt_mst_remarks" name="txt_mst_remarks" class="text_boxes" style="width:130px" /></td>
                </tr>

                <tr>
                	<td class="must_entry_caption" ><strong>WO/PI/GRN</strong></td>
                    <td>
                    <input type="text" name="txt_booking_pi_no" id="txt_booking_pi_no" class="text_boxes" style="width:130px"  placeholder="Double Click to Search" onDblClick="openmypage_wo_pi_popup();" readonly>
                    <input type="hidden" id="txt_wo_pi_id" name="txt_wo_pi_id" value="" />
                    <input type="hidden" id="grn_receive_basis" name="grn_receive_basis" value="" />
                    <input type="hidden" id="grn_wo_pi_no" name="grn_wo_pi_no" value="" />
                    <input type="hidden" id="grn_wo_pi_id" name="grn_wo_pi_id" value="" />
                    </td>
                    <td class="must_entry_caption"> Store Name </td>
                    <td id="store_td">
						<?
                        echo create_drop_down( "cbo_store_name", 142, $blank_array,"", 1, "-- Select Store --", 0, "" );
                        ?>
                    </td>
                    <td class="must_entry_caption">Source</td>
                    <td width="160" id="sources">
                        <?
                            echo create_drop_down( "cbo_source", 142, $source,"", 1, "-- Select --", $selected, "",0 );
                        ?>
                    </td>
                </tr>
            </table>
			</fieldset>

            <fieldset style="width:1800px; margin-top:10px;">
                 	<legend>Yarn Receive Entry details part</legend>
                    <? $i=1; ?>
                    	<table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" id="tbl_fabric_desc_item">
                        	<thead>
								<tr>
									<th width="50" rowspan="2">Count</th>
									<th width="130" rowspan="2">Composition</th>
									<th width="30" rowspan="2">%</th>
									<th width="80" rowspan="2">Yarn Type</th>					
									<th width="80" rowspan="2">Color</th>
									<th width="55" rowspan="2" class="must_entry_caption">Lot/ Batch</th>
									<th width="65" rowspan="2" class="must_entry_caption">Brand</th>
                                    <th width="50" rowspan="2"><input type="checkbox" checked id="floorIds" name="floorIds"/><br>Floor</th>
									<th width="50" rowspan="2"><input type="checkbox" checked id="roomIds" name="roomIds"/><br>Room</th>
									<th width="50" rowspan="2"><input type="checkbox" checked id="rackIds" name="rackIds"/><br>Rack</th>
									<th width="50" rowspan="2"><input type="checkbox" checked id="shelfIds" name="shelfIds"/><br>Shelf</th>
									<th width="50" rowspan="2"><input type="checkbox" checked id="binIds" name="binIds"/><br>Bin/Box</th>
                                    <th width="110" colspan="2">Recv. Qty</th>
									<th width="40" rowspan="2">UOM</th>
                                    <th colspan="3" width="150">Rate</th>
                                    <th width="50" rowspan="2">ILE%</th>
									<th width="80" rowspan="2">Amount</th>
                                    <th width="55" rowspan="2">Rate(TK)</th>
									<th width="80" rowspan="2">Book Currency</th>
									<th width="70" rowspan="2">Bal. PI/ Ord. Qty</th>					
									<th width="70" rowspan="2">Allowed Qty</th>
									<th width="50" rowspan="2">No. Of Bag</th>
									<th width="50" rowspan="2">Cone Per Bag</th>
                                    <th width="50" rowspan="2">Loose Cone</th>
									<th width="50" rowspan="2">Weight per Bag</th>
									<th width="50" rowspan="2">Wght @ Cone</th>
                                    <th width="50" rowspan="2">Product Code</th>
									<th rowspan="2">Remarks</th>
								</tr>
                                <tr>
									<th width="55">Recv. Qnty</th>
									<th width="55">Grey Qnty</th>
                                   	<th width="55">Rate</th>
									<th width="55">Grey Rate(TK)</th>
									<th width="40">D. Charge(TK)</th>
								</tr>
                            </thead>
                            <tbody id="list_fabric_desc_container">
                            	<tr id="row_1" align="center">
                                    <td id="count_1"></td>
                                    <td id="composition_1"></td>
                                    <td id="comPersent_1"></td>
                                    <td id="yarnType_1"></td>
                                    <td id="color_1"></td>
                                    <td id="tdlot_1"><input type="text" name="TxtLot[]" id="TxtLot_1" class="text_boxes" style="width:40px;" value="" /></td>
                                    <td id="tdbrand_1"><input type="text" name="TxtBrand[]" id="TxtBrand_1" class="text_boxes" style="width:60px;" value="" /></td>
                                    <td align="center" id="floor_td_to" class="floor_td_to"><p>
									<? 
									$i=1;
									$argument = "'".$i.'_0'."'";
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
                                    </p></td>
                                    <td id="tdreceiveqnty_1"><input type="text" name="receiveqnty[]" id="receiveqnty_1" class="text_boxes_numeric" style="width:50px;" value="" onBlur="calculate(1);"/></td>
                                    <td id="tdgreyqnty_1"><input type="text" name="greyqnty[]" id="greyqnty_1" class="text_boxes_numeric" style="width:45px;" value="" onBlur="calculate(1);"/></td>
                                    <td id="uom_1"></td>
                                    <td id="rate_1"></td>
                                    <td id="greyRate_1"></td>
                                    <td id="DCharge_1"></td>
                                    <td id="ilePersent_1"></td>
                                    <td id="amount_1"></td>
                                    <td id="avgRate_1"></td>
                                    <td id="bookCurrency_1"></td>
                                    <td id="woPiBalQnty_1"></td>
                                    <td id="overRcvQnty_1"></td>
                                    <td id="tdNoOfBag_1"><input type="text" name="noOfBag[]" id="noOfBag_1" class="text_boxes_numeric" style="width:40px;" value="" /></td>
                                    <td id="tdConPerBag_1"><input type="text" name="conPerBag[]" id="conPerBag_1" class="text_boxes_numeric" style="width:40px;" value="" /></td>
                                    <td id="tdLoseCone_1"><input type="text" name="loseCone[]" id="loseCone_1" class="text_boxes_numeric" style="width:40px;" value="" /></td>
                                   <td id="tdWetPerBag_1"><input type="text" name="wetPerBag[]" id="wetPerBag_1" class="text_boxes_numeric" style="width:40px;" value="" /></td>
                                   <td id="tdWetPerCon_1"><input type="text" name="wetPerCon[]" id="wetPerCon_1" class="text_boxes_numeric" style="width:40px;" value="" /></td>
                                    <td id="productCode_1"></td>
                                    <td id="remarks_1">
                                    <input type="text" name="dtlsRemarks[]" id="dtlsRemarks_1" class="text_boxes" style="width:40px;" value="" />
                                    <input type="hidden" name="updatedtlsid[]" id="updatedtlsid_1" value="" readonly>
                                    <input type="hidden" name="piWoDtlsId[]" id="piWoDtlsId_1" value="" readonly>
                                    <input type="hidden" name="previousprodid[]" id="previousprodid_1" value="" readonly>
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
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                
                                <th>Total</th>
                                <th><input type="text" id="tot_rcv_qnty" name="tot_rcv_qnty" style="width:50px;" class="text_boxes_numeric" readonly disabled /></th>
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
                        echo load_submit_buttons($permission, "fnc_trims_receive", 0,1,"reset_form('trimsreceive_1','list_container_trims*list_fabric_desc_container','','','set_receive_basis(1);','')",1);
                        
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
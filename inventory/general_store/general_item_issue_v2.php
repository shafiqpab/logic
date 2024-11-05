<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Trims Receive Entry 
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	10/07/2021
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
echo load_html_head_contents("Trims Receive Entry", "../../", 1, 1,'','1',''); 

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
		$('#txt_booking_pi_id').val('');
		
		//$('#cbo_supplier_name').val(0);
		//$('#cbo_source').val(0);
		//$('#cbo_currency_id').val(2);
		
		var list_view_wo =trim(return_global_ajax_value( recieve_basis, 'mrr_details', '', 'requires/trims_receive_multi_ref_entry_v3_controller'));
		$('#list_fabric_desc_container').html('');
		$('#list_fabric_desc_container').html(list_view_wo);
	}
	
	
	
	function openmypage_wo_pi_popup()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var recieve_basis = $('#cbo_receive_basis').val();
		var update_id = $('#update_id').val();
		var exchange_rate = $('#txt_exchange_rate').val()*1;
		var cbo_currency_id = $('#cbo_currency_id').val();
		var cbo_source = $('#cbo_source').val();
		var cbo_supplier_name = $('#cbo_supplier_name').val();
		//alert(exchange_rate);
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'WO/PI Selection Form';	
			//var page_link = 'requires/trims_receive_multi_ref_entry_v3_controller.php?cbo_company_id='+cbo_company_id+'&recieve_basis='+recieve_basis+'&update_id='+update_id+'&dtls_id='+dtls_id+'&action=wo_pi_popup';
			var page_link = 'requires/trims_receive_multi_ref_entry_v3_controller.php?cbo_company_id='+cbo_company_id+'&recieve_basis='+recieve_basis+'&update_id='+update_id+'&cbo_currency_id='+cbo_currency_id+'&cbo_source='+cbo_source+'&cbo_supplier_name='+cbo_supplier_name+'&action=wo_pi_popup';
			if(recieve_basis==1){ var width="1100px"; }else{ var width="1300px"; }
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+width+',height=450px,center=1,resize=1,scrolling=0','../');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var theemail=this.contentDoc.getElementById("hidden_wo_pi_id").value;	 //Knit Id for Kintting Plan
				var theename=this.contentDoc.getElementById("hidden_wo_pi_no").value; //all data for Kintting Plan
				var booking_without_order=this.contentDoc.getElementById("booking_without_order").value; //It will be Used later
				var all_data=this.contentDoc.getElementById("hidden_data").value; //Access form field with id="emailfield"
				var rcv_basis=this.contentDoc.getElementById("receive_basis").value; //Access form field with id="emailfield"
				var hid_booking_type=this.contentDoc.getElementById("hid_booking_type").value;
				//alert(theemail);
				//alert(theemail+"**"+theename+"**"+booking_without_order+"**"+all_data)
				var data=all_data.split("**");

				freeze_window(5);
				set_receive_basis(0);
				
				if(booking_without_order==1)
				{
					//$('#txt_buyer_order').attr('disabled','disabled');
					$('#txt_receive_qnty').removeAttr('disabled','disabled');
				}
				else
				{
					//$('#txt_buyer_order').removeAttr('disabled','disabled');
					$('#txt_receive_qnty').attr('disabled','disabled');
				}
				
				$('#txt_booking_pi_no').val(theename);
				$('#txt_booking_pi_id').val(theemail);
				$('#booking_without_order').val(booking_without_order);
				$('#cbo_receive_basis').val(rcv_basis);
				$('#cbo_currency_id').val(data[1]);	
				$('#cbo_source').val(data[2]);
				$('#txt_lc_no').val(data[3]);	
				$('#lc_id').val(data[4]);
				$('#cbo_pay_mode').val(data[5]);
				$('#meterial_source').val(data[6]);
				if(data[5]==3 || data[5]==5)
				{
					load_drop_down( 'requires/trims_receive_multi_ref_entry_v3_controller', document.getElementById('cbo_company_id').value+'_'+data[5], 'load_drop_down_supplier', 'supplier_td_id' );
				}
				
				$('#cbo_supplier_name').val(data[0]);
				load_exchange_rate();
				//alert("test");return;
		
				if(rcv_basis==1) var booking_no_pi_id=theemail; else var booking_no_pi_id=theename;
				var exchange_rate = $('#txt_exchange_rate').val()*1;
				//alert(exchange_rate);
				show_list_view(booking_no_pi_id+"**"+rcv_basis+"**"+booking_without_order+"**"+cbo_company_id+"**"+data[2]+"**"+exchange_rate+"**"+hid_booking_type+"**"+theemail, 'show_fabric_desc_listview', 'list_fabric_desc_container', 'requires/trims_receive_multi_ref_entry_v3_controller', 'setFilterGrid("list_fabric_desc_container",-1)' ) ;
				$('#check_qnty').attr('checked',true);
				$('#txt_booking_pi_no').attr('disabled',true);
				$('#cbo_store_name').val(0);
				calculate(1);
				release_freezing();
			}
		}
	}
	
	
	
	function load_details_data(booking_pi_id,booking_pi_no,booking_without_order,mst_id,material_source)
	{
		$('#txt_booking_pi_no').val(booking_pi_no);
		$('#txt_booking_pi_id').val(booking_pi_id);
		$('#booking_without_order').val(booking_without_order);
		$('#meterial_source').val(material_source);
		var cbo_company_id=$('#cbo_company_id').val();
		var recieve_basis=$('#cbo_receive_basis').val();
		var exchange_rate=$('#txt_exchange_rate').val();
		var cbo_source=$('#cbo_source').val();
		var cbo_store_name=$('#cbo_store_name').val();
		if(recieve_basis==1) var booking_no_pi_id=booking_pi_id; else var booking_no_pi_id=booking_pi_no;
		show_list_view(booking_no_pi_id+"**"+recieve_basis+"**"+booking_without_order+"**"+cbo_company_id+"**"+cbo_source+"**"+exchange_rate+"**"+mst_id+"**"+booking_pi_id+"**"+cbo_store_name+"**"+booking_pi_id, 'show_fabric_desc_listview_update', 'list_fabric_desc_container', 'requires/trims_receive_multi_ref_entry_v3_controller', 'setFilterGrid("list_fabric_desc_container",-1)' ) ;
		calculate(1);
		set_button_status(1, permission, 'fnc_trims_receive',1,0);
	}
	

	function fnc_trims_receive(operation)
	{
		if(operation==4)
		{
			 var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "trims_receive_entry_print", "requires/trims_receive_multi_ref_entry_v3_controller" ) 
			 return;
		}
		if(operation==5)
		{
			var rcv_id=$("#txt_recieved_id").val();
			if( rcv_id=="" )
			{
				alert("Save Data First");return;
			}
			
			 var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "trims_receive_entry_print2", "requires/trims_receive_multi_ref_entry_v3_controller" ) 
			 return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if ($("#is_posted_account").val()*1 == 1) {
				alert("Already Posted In Accounting. Save Update Delete Restricted.");
				return;
			}
			
			if( form_validation('cbo_company_id*cbo_receive_basis*txt_receive_date*txt_receive_chal_no*cbo_store_name*cbo_source*cbo_supplier_name','Company*Receive Basis*Received Date*Challan No*Store Name*Source*Supplier')==false )
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

            var store_update_upto=$('#store_update_upto').val()*1;

			var meterial_source=$('#meterial_source').val();
			var j=0; var i=1; var dataString='';
			$("#tbl_fabric_desc_item").find('tbody tr').not(':first').each(function()
			{
				var po_id=$('#po_no_'+i).attr('title');
				var ref_no=$('#ref_no_'+i).html();
				var styleref=$('#style_no_'+i).html();
				var cboitemgroup=$('#item_group_'+i).attr('title');
				var itemdescription=encodeURIComponent($('#item_descrip_'+i).html());
				var brandSupref=$('#brand_supp_'+i).html();
				var gmtssizeId=$('#gmt_size_'+i).attr('title');
				var itemcolorid=encodeURIComponent($('#item_color_'+i).attr('title'));
				var gmtscolorid=$(this).find('input[name="gmtscolorid[]"]').val();
				var itemsizeid=encodeURIComponent($('#item_size_'+i).attr('title'));
				var cbouom=$('#uom_'+i).attr('title');
				var wopiqnty=$('#WOPIQnty_'+i).html();
				
				//var cbo_floor_to=$(this).find('input[name="cbo_floor_to[]"]').val();
				var cbo_floor_to=$('#cbo_floor_to_'+i).val();
				var cbo_room_to=$('#cbo_room_to_'+i).val();
				var txt_rack_to=$('#txt_rack_to_'+i).val();
				var txt_shelf_to=$('#txt_shelf_to_'+i).val();
				var txt_bin_to=$('#txt_bin_to_'+i).val();

                if(store_update_upto > 1)
                {
                    if(store_update_upto==6 && (cbo_floor_to==0 || cbo_room_to==0 || txt_rack_to==0 || txt_shelf_to==0 || txt_bin_to==0))
                    {
                        alert("Up To Bin Value Full Fill Required For Inventory");
                        j=0;
                        i = 1;
                        return false;
                    }
                    else if(store_update_upto==5 && (cbo_floor_to==0 || cbo_room_to==0 || txt_rack_to==0 || txt_shelf_to==0))
                    {
                        alert("Up To Shelf Value Full Fill Required For Inventory");
                        j=0;
                        i = 1;
                        return false;
                    }
                    else if(store_update_upto==4 && (cbo_floor_to==0 || cbo_room_to==0 || txt_rack_to==0))
                    {
                        alert("Up To Rack Value Full Fill Required For Inventory");
                        j=0;
                        i = 1;
                        return false;
                    }
                    else if(store_update_upto==3 && (cbo_floor_to==0 || cbo_room_to==0))
                    {
                        alert("Up To Room Value Full Fill Required For Inventory");
                        j=0;
                        i = 1;
                        return false;
                    }
                    else if(store_update_upto==2 && cbo_floor_to==0)
                    {
                        alert("Up To Floor Value Full Fill Required For Inventory");
                        j=0;
                        i = 1;
                        return false;
                    }
                }

				var receiveqnty=$(this).find('input[name="receiveqnty[]"]').val();
				//var rate=$(this).find('input[name="rate[]"]').val();
				var rate=$('#tdrate_'+i).attr("title");
				var ile=$(this).find('input[name="ile[]"]').val();
				var amount=$(this).find('input[name="amount[]"]').val();
				var rejectrecvqnty=$(this).find('input[name="rejectrecvqnty[]"]').val();
				var bookcurrency=$(this).find('input[name="bookcurrency[]"]').val();
				var blqty=$(this).find('input[name="blqty[]"]').val();
				var blamt=$(this).find('input[name="blamt[]"]').val();
				var cbopaymentoverrecv=$(this).find('select[name="cbopaymentoverrecv[]"]').val();
				
				var updatedtlsid=$(this).find('input[name="updatedtlsid[]"]').val();
				var updatetransid=$(this).find('input[name="updatetransid[]"]').val();
				var previousprodid=$(this).find('input[name="previousprodid[]"]').val();
				if(meterial_source==3)
				{
					if(receiveqnty>0)	
					{
						j++;
						dataString+='&po_id' + j + '=' + po_id + '&styleref' + j + '=' + styleref + '&ref_no' + j + '=' + ref_no + '&cboitemgroup' + j + '=' + cboitemgroup + '&itemdescription' + j + '=' + itemdescription+ '&brandSupref' + j + '=' + brandSupref + '&gmtssizeId' + j + '=' + gmtssizeId  + '&itemcolorid' + j + '=' + itemcolorid+ '&gmtscolorid' + j + '=' + gmtscolorid + '&itemsizeid' + j + '=' + itemsizeid+ '&cbouom' + j + '=' + cbouom + '&receiveqnty' + j + '=' + receiveqnty + '&rate' + j + '=' + rate + '&ile' + j + '=' + ile + '&amount' + j + '=' + amount + '&rejectrecvqnty' + j + '=' + rejectrecvqnty+ '&bookcurrency' + j + '=' + bookcurrency+ '&blqty' + j + '=' + blqty+ '&blamt' + j + '=' + blamt+ '&cbopaymentoverrecv' + j + '=' + cbopaymentoverrecv+ '&updatedtlsid' + j + '=' + updatedtlsid+ '&updatetransid' + j + '=' + updatetransid+ '&previousprodid' + j + '=' + previousprodid+ '&wopiqnty' + j + '=' + wopiqnty+ '&cbo_floor_to' + j + '=' + cbo_floor_to+ '&cbo_room_to' + j + '=' + cbo_room_to+ '&txt_rack_to' + j + '=' + txt_rack_to+ '&txt_shelf_to' + j + '=' + txt_shelf_to+ '&txt_bin_to' + j + '=' + txt_bin_to;
					
					}
				}
				else
				{
					if(cbopaymentoverrecv==1)
					{
						if(receiveqnty>0 && rate>0)	
						{
							j++;
							dataString+='&po_id' + j + '=' + po_id + '&styleref' + j + '=' + styleref + '&ref_no' + j + '=' + ref_no + '&cboitemgroup' + j + '=' + cboitemgroup + '&itemdescription' + j + '=' + itemdescription+ '&brandSupref' + j + '=' + brandSupref + '&gmtssizeId' + j + '=' + gmtssizeId  + '&itemcolorid' + j + '=' + itemcolorid+ '&gmtscolorid' + j + '=' + gmtscolorid + '&itemsizeid' + j + '=' + itemsizeid+ '&cbouom' + j + '=' + cbouom + '&receiveqnty' + j + '=' + receiveqnty + '&rate' + j + '=' + rate + '&ile' + j + '=' + ile + '&amount' + j + '=' + amount + '&rejectrecvqnty' + j + '=' + rejectrecvqnty+ '&bookcurrency' + j + '=' + bookcurrency+ '&blqty' + j + '=' + blqty+ '&blamt' + j + '=' + blamt+ '&cbopaymentoverrecv' + j + '=' + cbopaymentoverrecv+ '&updatedtlsid' + j + '=' + updatedtlsid+ '&updatetransid' + j + '=' + updatetransid+ '&previousprodid' + j + '=' + previousprodid+ '&wopiqnty' + j + '=' + wopiqnty+ '&cbo_floor_to' + j + '=' + cbo_floor_to+ '&cbo_room_to' + j + '=' + cbo_room_to+ '&txt_rack_to' + j + '=' + txt_rack_to+ '&txt_shelf_to' + j + '=' + txt_shelf_to+ '&txt_bin_to' + j + '=' + txt_bin_to;
						
						}
					}
					else
					{
						//alert(cboitemgroup+"="+receiveqnty);
						if(receiveqnty>0)	
						{
							j++;
							dataString+='&po_id' + j + '=' + po_id + '&styleref' + j + '=' + styleref + '&ref_no' + j + '=' + ref_no + '&cboitemgroup' + j + '=' + cboitemgroup + '&itemdescription' + j + '=' + itemdescription+ '&brandSupref' + j + '=' + brandSupref + '&gmtssizeId' + j + '=' + gmtssizeId  + '&itemcolorid' + j + '=' + itemcolorid+ '&gmtscolorid' + j + '=' + gmtscolorid + '&itemsizeid' + j + '=' + itemsizeid+ '&cbouom' + j + '=' + cbouom + '&receiveqnty' + j + '=' + receiveqnty + '&rate' + j + '=' + rate + '&ile' + j + '=' + ile + '&amount' + j + '=' + amount + '&rejectrecvqnty' + j + '=' + rejectrecvqnty+ '&bookcurrency' + j + '=' + bookcurrency+ '&blqty' + j + '=' + blqty+ '&blamt' + j + '=' + blamt+ '&cbopaymentoverrecv' + j + '=' + cbopaymentoverrecv+ '&updatedtlsid' + j + '=' + updatedtlsid+ '&updatetransid' + j + '=' + updatetransid+ '&previousprodid' + j + '=' + previousprodid+ '&wopiqnty' + j + '=' + wopiqnty+ '&cbo_floor_to' + j + '=' + cbo_floor_to+ '&cbo_room_to' + j + '=' + cbo_room_to+ '&txt_rack_to' + j + '=' + txt_rack_to+ '&txt_shelf_to' + j + '=' + txt_shelf_to+ '&txt_bin_to' + j + '=' + txt_bin_to;
						
						}
					}
					
				}
				
				i++;
			});
			if(j<1)
			{
				return;
			}
			
			//alert(dataString);return;
			
			var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_recieved_id*cbo_company_id*cbo_receive_basis*txt_receive_date*txt_booking_pi_no*txt_booking_pi_id*booking_without_order*txt_receive_chal_no*txt_challan_date*lc_id*cbo_source*cbo_store_name*cbo_pay_mode*cbo_supplier_name*cbo_currency_id*txt_exchange_rate*update_id*meterial_source*txt_remarks*txt_gate_entry*txt_gate_entry_date',"../../")+dataString;
		
			//alert(data); return;
			
			freeze_window(operation);
			http.open("POST","requires/trims_receive_multi_ref_entry_v3_controller.php",true);
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
				alert(reponse[1]);
				release_freezing();
				return;	
			}
			else
			{	
				show_msg(reponse[0]);
				if(reponse[0]==0 || reponse[0]==1 )
				{
					document.getElementById('update_id').value = reponse[1];
					document.getElementById('txt_recieved_id').value = reponse[2];
					$('#cbo_company_id').attr('disabled','disabled');
					$('#cbo_store_name').attr('disabled','disabled');
					$('#txt_booking_pi_no').attr('disabled',true);
					$('#cbo_receive_basis').attr('disabled',true);
					//reset_form('trimsreceive_1','list_container_trims','','','','update_id*txt_recieved_id*cbo_company_id*cbo_receive_basis*txt_receive_date*txt_receive_chal_no*txt_challan_date*cbo_pay_mode*cbo_supplier_name*cbo_store_name*cbo_currency_id*txt_exchange_rate*lc_id*txt_lc_no*cbo_source');
					//if(reponse[0]!=2)
					//{
						//show_list_view(reponse[1],'show_trims_listview','list_container_trims','requires/trims_receive_multi_ref_entry_v3_controller','');
					//}					
					var booking_id=$("#txt_booking_pi_id").val();
					var booking_no=$("#txt_booking_pi_no").val();
					var booking_without_order=$("#booking_without_order").val();
					var material_source=$("#meterial_source").val();
					load_details_data(booking_id,booking_no,booking_without_order,reponse[1],material_source);
					
					$('#ile_td').html('ILE%');
					//set_receive_basis();
					set_button_status(1, permission, 'fnc_trims_receive',1,0);
				}
				if(reponse[0]==2)
				{
					show_msg(reponse[0]);
					reset_form('trimsreceive_1','list_container_trims*list_fabric_desc_container','','','','');
					$('#cbo_company_id').attr("disabled",false);
					$('#cbo_store_name').attr("disabled",false);
					$('#txt_booking_pi_no').attr("disabled",false);
					$('#cbo_receive_basis').attr("disabled",false);
					set_button_status(0, permission, 'fnc_trims_receive',1,0);
					release_freezing();	
				}
			}
			release_freezing();	
		}
	}
	
	function trims_receive_popup()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var page_link='requires/trims_receive_multi_ref_entry_v3_controller.php?cbo_company_id='+cbo_company_id+'&action=trims_receive_popup_search';
			var title='Trims Receive Form';
			
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
					
					get_php_form_data(trims_recv_id, "populate_data_from_trims_recv", "requires/trims_receive_multi_ref_entry_v3_controller" );
					//show_list_view(reponse[1],'show_trims_listview','list_container_trims','requires/trims_receive_multi_ref_entry_v3_controller','');
					//show_list_view(trims_recv_id,'show_trims_listview','list_container_trims','requires/trims_receive_multi_ref_entry_v3_controller','');
					
					var booking_id=$("#txt_booking_pi_id").val();
					var booking_no=$("#txt_booking_pi_no").val();
					var booking_without_order=$("#booking_without_order").val();
					var material_source=$("#meterial_source").val();
					load_details_data(booking_id,booking_no,booking_without_order,trims_recv_id,material_source);
					$('#check_qnty').attr('checked',true);
					$('#txt_booking_pi_no').attr('disabled',true);
					$('#cbo_receive_basis').attr('disabled',true);
					set_button_status(1, permission, 'fnc_trims_receive',1,0);
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
		var bl_qnty 		= $('#blqty_'+i).val()*1;
		var rate			= $('#rate_'+i).val()*1;	 
		var ile_cost 		= $('#ile_'+i).val()*1;
		var amount 			= quantity*(rate+ile_cost);
		var amount_t 		= quantity*rate; 
		var bookCurrency 	= (rate*1+ile_cost*1)*exchangeRate*1*quantity*1;
		//alert(quantity+"=="+rate+"=="+amount_t);
		$('#amount_'+i).val(number_format_common(amount,"","",currency_id));
		$('#bookcurrency_'+i).html(number_format_common(bookCurrency,"","",1));
		
		var ddd={ dec_type:5, comma:0, currency:''}
		var numRow = $('table#tbl_fabric_desc_item tbody tr').length-1;
		//alert(numRow);
		math_operation( "tot_rcv_qnty", "receiveqnty_", "+", numRow,ddd );
	}
	
	function load_exchange_rate()
	{
		var currency_id= $("#cbo_currency_id").val();
		get_php_form_data(currency_id, "get_library_exchange_rate", "requires/trims_receive_multi_ref_entry_v3_controller" );
		//if(currency_id==1) var exchange_rate=1; else var exchange_rate=80;
		//$("#txt_exchange_rate").val(exchange_rate);
	}
	
	$('#txt_receive_chal_no').live('keydown', function(e)
		{
		if (e.keyCode === 13){
			var rec_basis=$('#cbo_receive_basis').val();
			if(rec_basis==12){
				 fnc_trims_receive_by_scan(this.value);		
			}
			else
			{
				alert("Please Choose Receive Basis Delivery Challan(Int.)");
				$('#txt_receive_chal_no').val('');		
			}
		
		}
	});



	function fnc_trims_receive_by_scan_Reply_info()
	{
	
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');	
			
			if(reponse[0]==40)
			{
				alert(reponse[1]);	
			}
			else if(reponse[0]==12)
			{
				alert("Any Data Not Found in this Challan.");	
			}
			else
			{	
				show_msg(reponse[0]);
				if((reponse[0]==0 || reponse[0]==1))
				{
					
					if(reponse[4])document.getElementById('txt_receive_chal_no').value = reponse[4];
					document.getElementById('update_id').value = reponse[1];
					document.getElementById('txt_recieved_id').value = reponse[2];
					$('#cbo_company_id').attr('disabled','disabled');
					$('#cbo_supplier_name').val(147);
					
					
					//reset_form('trimsreceive_1','list_container_trims*list_fabric_desc_container','','','cbo_company_id','');
					get_php_form_data(reponse[1], "populate_data_from_trims_recv", "requires/trims_receive_multi_ref_entry_v3_controller" );
					
					
					show_list_view(reponse[1],'show_trims_listview','list_container_trims','requires/trims_receive_multi_ref_entry_v3_controller','');
					
					reset_form('trimsreceive_1','','','','','update_id*txt_recieved_id*cbo_company_id*cbo_receive_basis*txt_receive_date*txt_receive_chal_no*txt_challan_date*cbo_supplier_name*cbo_store_name*cbo_currency_id*txt_exchange_rate*lc_id*txt_lc_no*cbo_source*txt_booking_pi_no*txt_booking_pi_id*booking_without_order*floorIds*roomIds*rackIds*shelfIds*binIds');
					$('#ile_td').html('ILE%');
					$('#cbo_payment_over_recv').val(0);
					set_button_status(0, permission, 'fnc_trims_receive',1,0);
				}
			}
			release_freezing();	
		}
	}

	function fn_pament_over(str,pay_val)
	{
		$('.common_color_'+str).val(pay_val);
	}
	
	function fn_fill_qnty()
	{
		var i=1;
		if($('#check_qnty').is(':checked'))
		{
			$("#tbl_fabric_desc_item").find('tbody tr').each(function(index, element) {
				var issue_qnty=$(this).find('input[name="receiveqnty[]"]').val()*1;
				if(issue_qnty<=0)
				{
					$(this).find('input[name="receiveqnty[]"]').val($(this).find('input[name="blqty[]"]').val()*1)
				}
                

            });
		}
		else
		{
			$("#tbl_fabric_desc_item").find('tbody tr').each(function(index, element) {
				$(this).find('input[name="receiveqnty[]"]').val("");
				$(this).find('input[name="receiveqnty[]"]').attr("placeholder",$(this).find('input[name="blqty[]"]').val());
            });
			
		}
		calculate(1);
	}
	
	
	function fn_load_floor(store_id)
	{		
		var com_id=$('#cbo_company_id').val();
		//var location_id=$('#cbo_location_name').val();
		//var all_data=com_id + "__" + store_id + "__" + location_id;
		var all_data=com_id + "__" + store_id;
		//alert(all_data);return;
		var floor_result = return_global_ajax_value(all_data, 'floor_list', '', 'requires/trims_receive_multi_ref_entry_v3_controller');
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
		var room_result = return_global_ajax_value(all_data, 'room_list', '', 'requires/trims_receive_multi_ref_entry_v3_controller');
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
		var rack_result = return_global_ajax_value(all_data, 'rack_list', '', 'requires/trims_receive_multi_ref_entry_v3_controller');
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
		var shelf_result = return_global_ajax_value(all_data, 'shelf_list', '', 'requires/trims_receive_multi_ref_entry_v3_controller');
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
		var shelf_result = return_global_ajax_value(all_data, 'bin_list', '', 'requires/trims_receive_multi_ref_entry_v3_controller');
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

    function company_on_change(company)
    {
        var data='cbo_company_id='+company+'&action=upto_variable_settings';

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function()
        {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("store_update_upto").value = this.responseText;
            }
        }
        xmlhttp.open("POST", "requires/trims_receive_multi_ref_entry_v3_controller.php", true);
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send(data);
    }



</script>
<body onLoad="set_hotkey();load_exchange_rate();">
<div style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission); ?>
    <form name="trimsreceive_1" id="trimsreceive_1">
    	<div style="width:1350px;">        
            <fieldset style="width:1350px">
            <legend>General Item Issue Master Part</legend>
			<fieldset style="width:1150px">
            <table cellpadding="0" cellspacing="2" width="100%">
                <tr>
                    <td align="right" colspan="5"><strong> Issue ID </strong></td>
                    <td colspan="5">
                        <input type="hidden" name="update_id" id="update_id" />
                        <input type="text" name="txt_recieved_id" id="txt_recieved_id" class="text_boxes" style="width:120px" placeholder="Double Click" onDblClick="trims_receive_popup();" >
                    </td>
                </tr>
                <tr>
                    <td width="100" class="must_entry_caption"> Company </td>
                    <td width="150">
						<? 
						//load_room_rack_self_bin('requires/trims_receive_multi_ref_entry_v3_controller*4', 'store','store_td', this.value);
						echo create_drop_down( "cbo_company_id", 142, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down( 'requires/trims_receive_multi_ref_entry_v3_controller',this.value+'_'+document.getElementById('cbo_pay_mode').value, 'load_drop_down_supplier', 'supplier_td_id' );reset_form('trimsreceive_1','list_container_trims*list_fabric_desc_container','','','set_receive_basis(0);','cbo_company_id*cbo_receive_basis*txt_receive_date*cbo_store_name*cbo_currency_id*floorIds*roomIds*rackIds*shelfIds*binIds');load_drop_down( 'requires/trims_receive_multi_ref_entry_v3_controller', this.value, 'load_drop_down_store', 'store_td');company_on_change(this.value);get_php_form_data(this.value,'print_button_variable_setting','requires/trims_receive_multi_ref_entry_v3_controller' );" );
                        ?>
                    </td>
                    <td width="100" class="must_entry_caption"> Issue Purpose </td>
                    <td width="150">
                        <? 
                        	echo create_drop_down("cbo_receive_basis",122,$receive_basis_arr,"",1,"-- Select --",0,"set_receive_basis(1);","",'1,2');
                        ?>
                    </td>
                    <td width="100" class="must_entry_caption"> Issue Date </td>
                    <td width="150">
                        <input class="datepicker" type="text" style="width:110px" name="txt_receive_date" id="txt_receive_date" value="<? echo date("d-m-Y")?>" />
                    </td>
                    <td width="100" >Challan No</td>
                    <td width="150">
                    	<input type="text" name="txt_lc_no" id="txt_lc_no" class="text_boxes" style="width:130px" placeholder="Display" disabled>
                        <input type="hidden" name="lc_id" id="lc_id" />
                    </td>
                    <td width="100" >Loan Party</td>
                    <td> 
                        <?
                           echo create_drop_down( "cbo_currency_id", 200,$currency,"", 1, "Select Currency ", 0, "load_exchange_rate();",0 );
                        ?>
                    </td>
                </tr> 
                <tr>
                	<td class="must_entry_caption" ><strong>Req No</strong></td>
                    <td height="25">
                    <input type="text" name="txt_booking_pi_no" id="txt_booking_pi_no" class="text_boxes" style="width:130px"  placeholder="Double Click to Search" onDblClick="openmypage_wo_pi_popup();" readonly>
                    <input type="hidden" name="txt_booking_pi_id" id="txt_booking_pi_id" class="text_boxes">
                    <input type="hidden" name="booking_without_order" id="booking_without_order"/>
                    <input type="hidden" name="meterial_source" id="meterial_source"/>
                     </td>
                    <td width="100" class="must_entry_caption"> Issue Source </td>
                    <td>
                        <input type="text" name="txt_receive_chal_no" id="txt_receive_chal_no" class="text_boxes" style="width:110px" placeholder="Write">
                    </td>
                    <td width="100"> Issue To </td>
                    <td>
                        <input class="datepicker" type="text" style="width:110px" name="txt_challan_date" id="txt_challan_date"/>
                    </td>
                    <td class="must_entry_caption">Location</td>
                    <td>
						<?
							echo create_drop_down( "cbo_source", 142, $source,"", 1, "-- Select --", 0, "",1 );
						?>
                    </td>
                    <td>Pay Mode</td>
                    <td><? echo create_drop_down( "cbo_pay_mode", 200, $pay_mode,"", 1, "-- Select Pay Mode --", "", "load_drop_down( 'requires/trims_receive_multi_ref_entry_v3_controller', document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_supplier', 'supplier_td_id' )",1 ); ?></td>
                </tr>
                <tr>
				<td class="must_entry_caption">Location</td>
                    <td>
						<?
							echo create_drop_down( "cbo_source", 142, $source,"", 1, "-- Select --", 0, "",1 );
						?>
                    </td>
                	<td class="must_entry_caption">Store Name</td>
                    <td id="supplier_td_id"><? echo create_drop_down( "cbo_supplier_name", 122, $blank_array,"", 1, "-- Select Supplier --", 0, "",1 );?></td>
                    <td>Division</td>
                    <td>
                    	<input type="text" name="txt_exchange_rate" id="txt_exchange_rate" class="text_boxes_numeric" style="width:110px" value="" readonly>
                        <input type="hidden" name="store_update_upto" id="store_update_upto">
                    </td>
                    <td>Department</td>
                    <td>
						<?
							echo create_drop_down( "cbo_source", 142, $source,"", 1, "-- Select --", 0, "",1 );
						?>
                    </td>
                    <td>Section</td>
                    <td><? echo create_drop_down( "cbo_pay_mode", 200, $pay_mode,"", 1, "-- Select section --", "", "load_drop_down( 'requires/trims_receive_multi_ref_entry_v3_controller', document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_supplier', 'supplier_td_id' )",1 ); ?></td>
                </tr>
				<tr>
					<td></td>
                	<td></td>
					<td></td>
                	<td></td>
					<td></td>
                	<td></td>
                    <!-- <td>Gate Entry Date</td>
                    <td>
						<input class="datepicker" type="text" style="width:130px" name="txt_gate_entry_date" id="txt_gate_entry_date"/>
                    </td> -->
					<td></td>
                	<td></td>
                </tr>
            </table>
			</fieldset>

			<fieldset style="width:1820px; margin-top:10px;">
                 	<legend>General Item Issue Entry Details Part</legend>
                    <? $i=1; ?>
                    	<table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" id="tbl_fabric_desc_item">
                        	<thead>
							<tr>
									<th colspan="28"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
								</tr>
								<tr>
									<th width="30">SL</th>
                                    <th width="80">Item Category</th>
									<th width="100">Item Group</th>
									<th width="150">Item Description</th>
									<th width="60">Item Size</th>					
									<th width="70" >Item Sub Group</th>
									<th width="60">Item Number</th>
                                    <th width="60">Item Code</th>
									<th width="50">UOM</th>
                                    <th width="70">WO/PI/Req Qty</th>
                                    <th width="70" class="must_entry_caption"><input type="checkbox" checked id="chk_rcv_qnty" name="chk_rcv_qnty"/><br>Issue Qty</th>
									<th width="50" class="must_entry_caption">Current Stock</th>
									<th width="50">Return Qty</th>
                                    <th width="80" >Machine Category</th>
                                    <th width="70">Issue To Floor</th>
									<th width="70">Line No</th>
									<th width="70">Machine No</th>
                                    <th width="80">Remarks</th>
									<th width="80">Buyer Order</th>
									<th width="80">Serial No</th>
                                    <!-- <th width="50">Cons. Rate</th> -->
									<!-- <td class="must_entry_caption" ><strong>Buyer Order</strong>
									</td> -->
									<!-- <td class="must_entry_caption" ><strong>Serial No</strong>
									</td> -->
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
                                    <td id="sl_1"></td>
                                    <td id="category_1"></td>
                                    <td id="group_1"></td>
                                    <td id="description_1"></td>
                                    <td id="size_1"></td>
                                    <td id="subGroup_1"></td>
                                    <td id="itemNumber_1"></td>
                                    <td id="itemCode_1"></td>
                                    <td id="uom_1"></td>
                                    <td id="woPiReqQnty_1"></td>
                                    <td id="tdreceiveqnty_1"><input type="text" name="receiveqnty[]" id="receiveqnty_1" class="text_boxes_numeric" style="width:50px;" value="" onBlur="calculate(1);"/></td>
                                    <td id="rate_1"><input type="text" name="txtRate[]" id="txtRate_1" class="text_boxes_numeric" style="width:40px;" value="" onBlur="calculate(1);"/></td>
                                    <td id="ile_1"></td>
                                    <td id="amount_1"></td>
                                    <td id="prevRcvQnty_1"></td>
                                    <td id="BalWoPiReqQnty_1"></td>
                                    <td id="machine_1"></td>
                                    <td id="Comments_1"><input type="text" name="txtComments[]" id="txtComments_1" class="text_boxes" style="width:60px;" value="" /></td>
									<td><input type="text" name="txt_booking_pi_no" id="txt_booking_pi_no" class="text_boxes" style="width:130px"  placeholder="Double Click to Search" onDblClick="openmypage_wo_pi_popup();" readonly></td>
									<td><input type="text" name="txt_booking_pi_no" id="txt_booking_pi_no" class="text_boxes" style="width:130px"  placeholder="Double Click to Search" onDblClick="openmypage_wo_pi_popup();" readonly></td>
									<td id="brand_1"></td>
                                    <!-- <td id="warentyExpDate_1"><input type="text" name="txtWarentyExpDate[]" id="txtWarentyExpDate_1" class="datepicker" style="width:60px;" value="" /></td> -->
                                    <td id="origin_1"></td>
                                    <td id="model_1"></td>
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
                                    </p>
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
                            </tfoot>
                        </table>
                     <!--<div id="list_container"></div>--> 
                </fieldset>
                 <table width="100%">
                    <tr>
                        <td width="80%" align="center"> 
                        <?
						//cbo_company_id*cbo_receive_basis*txt_receive_date*cbo_store_name*cbo_currency_id*floorIds*roomIds*rackIds*shelfIds*binIds 
                        echo load_submit_buttons($permission, "fnc_general_receive", 0,1,"reset_form('trimsreceive_1','list_container_trims*list_fabric_desc_container','','','set_receive_basis(1);','')",1);
                        
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
<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Trims Receive Entry 
Functionality	:	
JS Functions	:
Created by		:	Fuad Shahriar 
Creation date 	: 	11/09/2013
Updated by 		: 	Kausar (Creating Print Report)	
Update date		: 	12-01-2014	   
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
echo load_html_head_contents("Trims Receive Entry", "../../", 1, 1,'','1',''); 

?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	<? 
	if($_SESSION['logic_erp']['data_arr'][350]!="")
	{
		$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][350] ); 
		if($data_arr) echo "var field_level_data= ". $data_arr . ";\n";
		//echo "alert(JSON.stringify(field_level_data));";
	}
	?>
	
	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name", "color_name" ), 0, -1); ?>];
	var str_size = [<? echo substr(return_library_autocomplete( "select size_name from lib_size group by size_name", "size_name" ), 0, -1); ?>];

	$(document).ready(function(e)
	 {
          $("#txt_item_color").autocomplete({
			 source: str_color
		  });
		  
		  $("#txt_item_size").autocomplete({
			 source: str_size
		  });
     });

	function set_receive_basis()
	{
		var recieve_basis = $('#cbo_receive_basis').val();
		var cbo_company_id = $('#cbo_company_id').val();
		
		$('#booking_without_order').val('');
		$('#txt_booking_pi_no').val('');	
		$('#txt_booking_pi_id').val('');
		$('#all_po_id').val('');
		$('#save_data').val('');
		$('#hidden_sensitivity').val('');
		$('#cbo_supplier_name').val(0);
		$('#cbo_source').val(0);
		$('#cbo_currency_id').val(2);
		load_exchange_rate()
		//$('#txt_exchange_rate').val(80);
		$('#txt_lc_no').val('');	
		$('#lc_id').val('');
		$('#cbo_item_group').val(0);	
		$('#txt_item_description').val('');
		$('#hidden_item_description').val('');
		$('#cbo_uom').val(0);	
		$('#txt_brand_supref').val('');
		$('#cbo_payment_over_recv').val(0);
		
		$('#txt_receive_qnty').val('');
		$('#txt_amount').val('');
		$('#txt_rate').val('');
		$('#txt_hidden_rate').val('');
		$('#txt_book_currency').val('');
		$('#txt_ile').val('');
		$('#ile_td').html('ILE%');
		$('#txt_bl_qty').val('');
		$('#txt_buyer_order').val('');
		$('#txt_buyer_order').attr('readonly','readonly');
		$('#txt_buyer_order').attr('onClick','openmypage_po();');	
		$('#txt_buyer_order').attr('placeholder','Single Click');
		
		$('#txt_gmts_color').val('');
		$('#txt_gmts_color_id').val('');
		$('#txt_gmts_size').val('');
		$('#txt_gmts_size_id').val('');
		$('#txt_item_color').val('');
		$('#txt_item_color_id').val('');
		$('#txt_item_size').val('');
		
		if(recieve_basis == 2){
			$('#txt_rate').attr('disabled','disabled');
		}
		
		if(recieve_basis == 4 || recieve_basis == 6 )
        {
			$('#txt_booking_pi_no').attr('disabled','disabled');
			$('#cbo_supplier_name').removeAttr('disabled','disabled');	
			$('#cbo_source').removeAttr('disabled','disabled');	
			$('#cbo_currency_id').removeAttr('disabled','disabled');
			$('#cbo_item_group').removeAttr('disabled','disabled');
			$('#txt_item_description').removeAttr('disabled','disabled');	
			$('#txt_brand_supref').removeAttr('disabled','disabled');
			$('#txt_item_color').removeAttr('disabled','disabled');
			$('#txt_item_size').removeAttr('disabled','disabled');
			$('#cbo_payment_over_recv').attr('disabled','disabled');
        }
        else
        { 
			$('#txt_booking_pi_no').removeAttr('disabled','disabled');
			$('#cbo_supplier_name').attr('disabled','disabled');	
			$('#cbo_source').attr('disabled','disabled');	
			$('#cbo_currency_id').attr('disabled','disabled');
			$('#cbo_item_group').attr('disabled','disabled');
			$('#txt_item_description').attr('disabled','disabled');	
			$('#txt_brand_supref').attr('disabled','disabled');
			$('#txt_item_color').attr('disabled','disabled');
			$('#txt_item_size').attr('disabled','disabled');
			$('#cbo_payment_over_recv').removeAttr('disabled','disabled');
        }
		$('#list_fabric_desc_container').html('');
	}
	
	function openmypage_wo_pi_popup()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var recieve_basis = $('#cbo_receive_basis').val();
		var update_id = $('#update_id').val();
		var dtls_id = $('#update_dtls_id').val();
		
		if (form_validation('cbo_company_id*cbo_receive_basis','Company*Receive Basis')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'WO/PI Selection Form';	
			var page_link = 'requires/trims_receive_multi_ref_entry_controller.php?cbo_company_id='+cbo_company_id+'&recieve_basis='+recieve_basis+'&update_id='+update_id+'&dtls_id='+dtls_id+'&action=wo_pi_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1210px,height=450px,center=1,resize=1,scrolling=0','../');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var theemail=this.contentDoc.getElementById("hidden_wo_pi_id").value;	 //Knit Id for Kintting Plan
				var theename=this.contentDoc.getElementById("hidden_wo_pi_no").value; //all data for Kintting Plan
				var booking_without_order=this.contentDoc.getElementById("booking_without_order").value; //It will be Used later
				var all_data=this.contentDoc.getElementById("hidden_data").value; //Access form field with id="emailfield"
				
				var data=all_data.split("**");

				freeze_window(5);
				set_receive_basis();
				
				if(booking_without_order==1)
				{
					$('#txt_buyer_order').attr('disabled','disabled');
					$('#txt_receive_qnty').removeAttr('disabled','disabled');
				}
				else
				{
					$('#txt_buyer_order').removeAttr('disabled','disabled');
					$('#txt_receive_qnty').attr('disabled','disabled');
				}
				
				$('#txt_booking_pi_no').val(theename);
				$('#txt_booking_pi_id').val(theemail);
				$('#booking_without_order').val(booking_without_order);
				//alert(data[0]);
				
				$('#cbo_currency_id').val(data[1]);	
				$('#cbo_source').val(data[2]);
				$('#txt_lc_no').val(data[3]);	
				$('#lc_id').val(data[4]);
				$('#meterial_source').val(data[5]);
				$('#hidd_pay_mode').val(data[6]);
				load_drop_down( 'requires/trims_receive_multi_ref_entry_controller',cbo_company_id+"_"+data[6], 'load_drop_down_supplier', 'supplier_td_id' );
				$('#cbo_supplier_name').val(data[0]);
				
				var booking_pi_id = $('#txt_booking_pi_id').val();
				//alert(booking_pi_id);
				load_exchange_rate();
		
				if(recieve_basis==1) var booking_no_pi_id=theemail; else var booking_no_pi_id=theename;

				show_list_view(booking_no_pi_id+"**"+recieve_basis+"**"+booking_without_order+"**"+booking_pi_id,'show_fabric_desc_listview','list_fabric_desc_container','requires/trims_receive_multi_ref_entry_controller','setFilterGrid(\'tbl_list_search\',-1);');
				
				release_freezing();
			}
		}
	}
	
	function load_description(booking_pi_id,booking_pi_no,booking_without_order)
	{
		var recieve_basis=$('#cbo_receive_basis').val();
		if(recieve_basis==1 || recieve_basis==2)
		{		
			if(recieve_basis==1) var booking_no_pi_id=booking_pi_id; else var booking_no_pi_id=booking_pi_no;
			
			show_list_view(booking_no_pi_id+"**"+recieve_basis+"**"+booking_without_order,'show_fabric_desc_listview','list_fabric_desc_container','requires/trims_receive_multi_ref_entry_controller','setFilterGrid(\'tbl_list_search\',-1);');
		}
	}
	
	function set_form_data(data)
	{
		$('#txt_receive_qnty').val('');
		$('#txt_buyer_order').val('');
		$('#all_po_id').val('');
		$('#save_data').val('');
		
		var recieve_basis = $('#cbo_receive_basis').val();
		var booking_without_order = $('#booking_without_order').val();
		var data=data.split("**");
		
		var desc=data[1];
		
		if(data[9]!="") desc=desc+", "+data[9];
		if(data[10]!="" && data[10]!='0') desc=desc+", "+data[10];
		
		$('#cbo_item_group').val(data[0]);
		$('#txt_item_description').val(desc);
		$('#hidden_item_description').val(data[1]);
		$('#txt_brand_supref').val(data[2]);
		$('#hidden_sensitivity').val(data[3]);
		$('#txt_gmts_color_id').val(data[4]);
		$('#txt_gmts_color').val(data[5]);
		$('#txt_gmts_size_id').val(data[6]);
		$('#txt_gmts_size').val(data[7]);
		$('#txt_item_color_id').val(data[8]);
		$('#txt_item_color').val(data[9]);
		$('#txt_item_size').val(data[10]);
		$('#txt_hidden_rate').val(data[13]);
		$('#txt_rate').val(data[13]).attr('disabled',true);
		var item_description=encodeURIComponent((data[1]));
		var brand_supref=encodeURIComponent((data[2]));

		get_php_form_data(data[0]+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_source').value+'_'+document.getElementById('txt_rate').value, 'show_ile_load_uom', 'requires/trims_receive_multi_ref_entry_controller' );
		
		var booking_no_pi_id=$('#txt_booking_pi_id').val(); 
		var booking_no_pi_no=$('#txt_booking_pi_no').val();
		
		if(recieve_basis==1 || recieve_basis==2)
		{
			get_php_form_data(recieve_basis+'_'+booking_no_pi_id+'_'+booking_no_pi_no+'_'+data[0]+'_'+item_description+'_'+brand_supref+'_'+data[3]+'_'+data[4]+'_'+data[6]+'_'+data[8]+'_'+data[10]+'_'+booking_without_order+'_'+data[11]+'_'+data[13], 'put_balance_qnty', 'requires/trims_receive_multi_ref_entry_controller' );
		}
		set_button_status(0, permission, 'fnc_trims_receive',1,1);
	}
	
	function openmypage_po(call_source = "", pi_basis_id="")
	{
		//alert()
		var receive_basis=$('#cbo_receive_basis').val();
		var booking_no=$('#txt_booking_pi_no').val();
		var booking_pi_id=$('#txt_booking_pi_id').val();
		var cbo_company_id = $('#cbo_company_id').val();
		var save_data = $('#save_data').val();
		var all_po_id = $('#all_po_id').val();
		var txt_receive_qnty = $('#txt_receive_qnty').val();  //alert(txt_receive_qnty);
		var item_group=$('#cbo_item_group').val();
		//var hidden_item_description='"'+$('#hidden_item_description').val()+'"';
		//var txt_item_description='"'+$('#txt_item_description').val()+'"';
		var hidden_item_description=encodeURIComponent("'"+($('#hidden_item_description').val())+"'");
		var txt_item_description=encodeURIComponent("'"+($('#txt_item_description').val())+"'");
		var brand_supref=encodeURIComponent("'"+($('#txt_brand_supref').val())+"'");
		var sensitivity=$('#hidden_sensitivity').val();
		var order_uom=$('#cbo_uom').val();
		var gmts_color_id=$('#txt_gmts_color_id').val();
		var gmts_size_id=$('#txt_gmts_size_id').val();
		var item_color_id=$('#txt_item_color_id').val();
		var item_size=$('#txt_item_size').val();
		//var item_size=encodeURIComponent("'"+($('#txt_item_size').val())+"'");
		var txt_hidden_rate=$('#txt_hidden_rate').val();
		var cbo_payment_over_recv=$('#cbo_payment_over_recv').val();
		//alert(111)
		var item_description='';
		
		if(receive_basis==2 || receive_basis==1) item_description=hidden_item_description; else item_description=txt_item_description;

		if (form_validation('cbo_company_id*cbo_receive_basis','Company*Receive Basis')==false)
		{
			return;
		}
		//alert(1)	
		if(receive_basis==2 && booking_no=="")
		{
			alert("Please Select Booking No.");
			$('#txt_booking_pi_no').focus();
			return false;
		}
		else if(item_group==0)
		{
			alert("Please Select Item Group.");
			$('#cbo_item_group').focus();
			return false;
		}
		else if(txt_item_description=="")
		{
			alert("Please Select Item Description.");
			$('#txt_item_description').focus();
			return false;
		}
		//alert(2222)
		var title = 'PO Info';
		var txt_rate=$('#txt_rate').val();
		if((txt_rate!="" && receive_basis!=4) || receive_basis==4 || receive_basis==6)
		{
			var page_link = 'requires/trims_receive_multi_ref_entry_controller.php?receive_basis='+receive_basis+'&cbo_company_id='+cbo_company_id+'&booking_no='+booking_no+'&booking_pi_id='+booking_pi_id+'&item_group='+item_group+'&item_description='+item_description+'&brand_supref='+brand_supref+'&order_uom='+order_uom+'&sensitivity='+sensitivity+'&gmts_color_id='+gmts_color_id+'&gmts_size_id='+gmts_size_id+'&item_color_id='+item_color_id+'&item_size='+item_size+'&all_po_id='+all_po_id+'&save_data='+save_data+'&txt_receive_qnty='+txt_receive_qnty+'&call_source='+call_source+'&wo_pi_basis_id='+receive_basis+'&txt_rate='+txt_rate+'&txt_hidden_rate='+txt_hidden_rate+'&cbo_payment_over_recv='+cbo_payment_over_recv+'&action=po_popup';
		  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var save_string=this.contentDoc.getElementById("save_string").value;	 //Access form field with id="emailfield"
				var tot_trims_qnty=this.contentDoc.getElementById("tot_trims_qnty").value; //Access form field with id="emailfield"
				var all_po_id=this.contentDoc.getElementById("all_po_id").value; //Access form field with id="emailfield"
				var all_po_no=this.contentDoc.getElementById("all_po_no").value; //Access form field with id="emailfield"
				var all_balance=this.contentDoc.getElementById("all_balance").value; //Access form field with id="emailfield"
				
				$('#save_data').val(save_string);
				$('#txt_receive_qnty').val(tot_trims_qnty);
				$('#all_po_id').val(all_po_id);
				$('#txt_buyer_order').val(all_po_no);
				$('#txt_color_size_balance').val(all_balance);
				$('#cbo_payment_over_recv').attr("disabled",true);
				calculate();
			}
		}
		
	}
	
	function fnc_trims_receive(operation)
	{
		if(operation==4)
		{
			if (form_validation('cbo_company_id*cbo_receive_basis*update_id','Company*Receive Basis*Update Id')==false)
			{
				return;
			}
			 var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "trims_receive_entry_print", "requires/trims_receive_multi_ref_entry_controller" ) 
			 return;
		}
		else if(operation==5)
		{
			if (form_validation('cbo_company_id*cbo_receive_basis*update_id','Company*Receive Basis*Update Id')==false)
			{
				return;
			}
			 var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "trims_receive_entry_print_2", "requires/trims_receive_multi_ref_entry_controller" ) 
			 return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			/*if(operation==2)
			{
				show_msg('13');
				return;
			}*/
			
			if($('#cbo_receive_basis').val()==12 && operation!=0)
			{
				alert("Delivery Challan Basis Update/Delete Not Allowed");return;
			}
			
			if ($("#is_posted_account").val()*1 == 1) {
				alert("Already Posted In Accounting. Save Update Delete Restricted.");
				return;
			}
			//
			if( form_validation('cbo_company_id*cbo_receive_basis*txt_receive_date*txt_receive_chal_no*cbo_store_name*cbo_source*cbo_supplier_name*cbo_currency_id*txt_exchange_rate*cbo_item_group*txt_item_description*txt_receive_qnty','Company*Receive Basis*Received Date*Challan No*Store Name*Supplier*Source*Currency*Exchange Rate*Item Group*Item Description*Receive Qnty')==false )
			{
				return;
			}
			
			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_receive_date').val(), current_date)==false)
			{
				alert("Receive Date Can not Be Greater Than Current Date");
				return;
			}
			var meterial_source=$('#meterial_source').val();
			if($('#cbo_payment_over_recv').val()==0 && meterial_source != 3 && $('#cbo_receive_basis').val()!=4 && $('#cbo_receive_basis').val()!=6 )
			{
				if(form_validation('txt_hidden_rate','Rate')==false )
				{
					alert("Zero rate not allow");return;
				}
			}
			
			if(($('#cbo_receive_basis').val()==1 || $('#cbo_receive_basis').val()==2) && $('#txt_booking_pi_no').val()=="")
			{
				alert("Please Select WO/PI No");
				$('#txt_booking_pi_no').focus();
				return;
			}
			
			if($('#booking_without_order').val()!=1 && $('#txt_buyer_order').val()=="")
			{
				alert("Please Select Buyer Order");
				$('#txt_buyer_order').focus();
				return;
			}
			// Store upto validation start
			var store_update_upto=$('#store_update_upto').val()*1;
			var cbo_floor=$('#cbo_floor').val()*1;
			var cbo_room=$('#cbo_room').val()*1;
			var txt_rack=$('#txt_rack').val()*1;
			var txt_shelf=$('#txt_shelf').val()*1;
			var cbo_bin=$('#cbo_bin').val()*1;
			
			if(store_update_upto > 1)
			{
				if(store_update_upto==6 && (cbo_floor==0 || cbo_room==0 || txt_rack==0 || txt_shelf==0 || cbo_bin==0))
				{
					alert("Up To Bin Value Full Fill Required For Inventory");return;
				}
				else if(store_update_upto==5 && (cbo_floor==0 || cbo_room==0 || txt_rack==0 || txt_shelf==0))
				{
					alert("Up To Shelf Value Full Fill Required For Inventory");return;
				}
				else if(store_update_upto==4 && (cbo_floor==0 || cbo_room==0 || txt_rack==0))
				{
					alert("Up To Rack Value Full Fill Required For Inventory");return;
				}
				else if(store_update_upto==3 && (cbo_floor==0 || cbo_room==0))
				{
					alert("Up To Room Value Full Fill Required For Inventory");return;
				}
				else if(store_update_upto==2 && cbo_floor==0)
				{
					alert("Up To Floor Value Full Fill Required For Inventory");return;
				}
			}
			// Store upto validation End
			//*hidden_item_description*txt_item_description
			
			var txt_item_description=encodeURIComponent("'"+$('#txt_item_description').val()+"'");
			var hidden_item_description=encodeURIComponent("'"+$('#hidden_item_description').val()+"'");
			var txt_brand_supref=encodeURIComponent("'"+$('#txt_brand_supref').val()+"'");
			//var txt_receive_chal_no=encodeURIComponent("'"+$('#txt_receive_chal_no').val().replace(/(\r\n|\n|\r)/gm, "")+"'");
			var txt_receive_chal_no=$('#txt_receive_chal_no').val().replace(/(\r\n|\n|\r)/gm, "");
			//alert($('#txt_receive_chal_no').val()+"="+txt_receive_chal_no);
			var data_string="&txt_item_description="+txt_item_description+"&hidden_item_description="+hidden_item_description+"&txt_brand_supref="+txt_brand_supref+"&txt_receive_chal_no="+txt_receive_chal_no;
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_remarks*txt_recieved_id*cbo_company_id*cbo_receive_basis*txt_receive_date*txt_booking_pi_no*txt_booking_pi_id*booking_without_order*txt_challan_date*lc_id*cbo_source*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*cbo_supplier_name*cbo_currency_id*txt_exchange_rate*cbo_item_group*cbo_uom*txt_receive_qnty*txt_reject_recv_qnty*txt_rate*txt_hidden_rate*txt_book_currency*txt_buyer_order*txt_ile*txt_amount*update_id*all_po_id*update_dtls_id*update_trans_id*save_data*previous_prod_id*hidden_sensitivity*txt_gmts_color_id*txt_gmts_color*txt_item_color_id*txt_item_color*txt_gmts_size_id*txt_gmts_size*txt_item_size*cbo_payment_over_recv*meterial_source*hidd_pay_mode*txt_addi_info*store_update_upto',"../../")+data_string;
			
			//alert(data);return;
			
			freeze_window(operation);
			
			http.open("POST","requires/trims_receive_multi_ref_entry_controller.php",true);
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
			if(reponse[0]==40)
			{
				alert(reponse[1]);
				release_freezing();
				return;		
			}
			else if(reponse[0]==20)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
            else if(reponse[0]==50)
            {
                alert(reponse[1]);
                release_freezing();
                return;
            }
			else
			{	
				show_msg(reponse[0]);
				if((reponse[0]==0 || reponse[0]==1 || reponse[0]==2))
				{
					document.getElementById('update_id').value = reponse[1];
					document.getElementById('txt_recieved_id').value = reponse[2];
					$('#cbo_company_id').attr('disabled','disabled');
					var company_id = $('#cbo_company_id').val();
					var store_id = $('#cbo_store_name').val();
					
					show_list_view(reponse[1]+'_'+company_id+'_'+store_id,'show_trims_listview','list_container_trims','requires/trims_receive_multi_ref_entry_controller','');
					
					reset_form('trimsreceive_1','','','','','update_id*txt_recieved_id*cbo_company_id*cbo_receive_basis*txt_receive_date*txt_receive_chal_no*txt_challan_date*cbo_supplier_name*cbo_store_name*cbo_currency_id*txt_exchange_rate*lc_id*txt_lc_no*cbo_source*txt_booking_pi_no*txt_booking_pi_id*booking_without_order*meterial_source*is_posted_account*store_update_upto');
					$('#ile_td').html('ILE%');
					$('#cbo_payment_over_recv').val(0);
					
					set_button_status(1, permission, 'fnc_trims_receive',1,1);	
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
			var page_link='requires/trims_receive_multi_ref_entry_controller.php?cbo_company_id='+cbo_company_id+'&action=trims_receive_popup_search';
			var title='Trims Receive Form';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=390px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var trims_recv_id=this.contentDoc.getElementById("hidden_recv_id").value;

				if(trims_recv_id!="")
				{
					freeze_window(5);
					reset_form('trimsreceive_1','list_container_trims*list_fabric_desc_container','','','cbo_company_id*is_posted_account*store_update_upto','');
					
					var posted_in_account=this.contentDoc.getElementById("hidden_posted_in_account").value; // is posted accounct
					$("#is_posted_account").val(posted_in_account);
					
					if(posted_in_account==1) document.getElementById("accounting_posted_status").innerHTML="Already Posted In Accounting.";
					else  document.getElementById("accounting_posted_status").innerHTML="";
					get_php_form_data(trims_recv_id, "populate_data_from_trims_recv", "requires/trims_receive_multi_ref_entry_controller" );
					
					/*var booking_pi_no = $('#txt_booking_pi_no').val();
					var booking_pi_id = $('#txt_booking_pi_id').val();
					var booking_without_order = $('#booking_without_order').val();
					var recieve_basis = $('#cbo_receive_basis').val();
					
					if(recieve_basis==1) var booking_no_pi_id=booking_pi_id; else if(recieve_basis==2) var booking_no_pi_id=booking_pi_no;
					if(recieve_basis==1 || recieve_basis==2)
					{
						show_list_view(booking_no_pi_id+"**"+recieve_basis+"**"+booking_without_order,'show_fabric_desc_listview','list_fabric_desc_container','requires/trims_receive_multi_ref_entry_controller','');
					}*/
					var company_id = $('#cbo_company_id').val();
					var store_id = $('#cbo_store_name').val();
					show_list_view(trims_recv_id+'_'+company_id+'_'+store_id,'show_trims_listview','list_container_trims','requires/trims_receive_multi_ref_entry_controller','');
					set_button_status(0, permission, 'fnc_trims_receive',1,1);	
					release_freezing();
				}
			}
		}
	}
	
	function calculate()
	{
		//amount and book currency calculate--------------//
		var currency_id 	= $("#cbo_currency_id").val();
		var quantity 		= $("#txt_receive_qnty").val();
		var exchangeRate 	= $("#txt_exchange_rate").val();
		var hidden_rate		= $('#txt_hidden_rate').val()*1;
		var rate			= $('#txt_rate').val();		 
		var ile_cost 		= $("#txt_ile").val();
		if(hidden_rate!="" && hidden_rate!=undefined)
		{
			var amount 			= quantity*1*(hidden_rate*1+ile_cost*1);
			var bookCurrency 	= (hidden_rate*1+ile_cost*1)*exchangeRate*1*quantity*1; 
		}
		else
		{
			var amount 			= quantity*1*(rate*1+ile_cost*1); 
			var bookCurrency 	= (rate*1+ile_cost*1)*exchangeRate*1*quantity*1;
		}
		
		
		$("#txt_amount").val(number_format_common(amount,"","",currency_id));
		$("#txt_amount").attr('title',amount);
		$("#txt_book_currency").val(number_format_common(bookCurrency,"","",1));
	}
	
	/*function load_exchange_rate()
	{
		var currency_id 	= $("#cbo_currency_id").val();
		if(currency_id==1)
		{
			var exchange_rate=1;
			$('#txt_exchange_rate').attr('disabled','disabled');
		}
		else 
		{
			var exchange_rate=80;
			$('#txt_exchange_rate').removeAttr('disabled','disabled');	
		}
		$("#txt_exchange_rate").val(exchange_rate);
	}*/
	
	function load_exchange_rate()
	{
		//var currency_id= $("#cbo_currency_id").val();
		//get_php_form_data(currency_id, "get_library_exchange_rate", "requires/trims_receive_multi_ref_entry_controller" );
		//if(currency_id==1) var exchange_rate=1; else var exchange_rate=80;
		//$("#txt_exchange_rate").val(exchange_rate);
		
		if(form_validation('cbo_company_id*cbo_currency_id','Company*Currency')==false)
		{
			return;
		}
		var company_id=$("#cbo_company_id").val();
		var val=$("#cbo_currency_id").val();
		if(val==1)
		{
			$("#txt_exchange_rate").val(1);
			//$("#txt_exchange_rate").attr("disabled",true);
		}
		else
		{
			var recv_date = $('#txt_receive_date').val();
			var response=return_global_ajax_value( val+"**"+recv_date+"**"+company_id, 'check_conversion_rate', '', 'requires/trims_receive_multi_ref_entry_controller');
			$('#txt_exchange_rate').val(response);
			//$("#txt_exchange_rate").attr("disabled",false);
		}
	}
	
	
	function openmypage_goodsPlacement()
	{
		var update_dtls_id = $('#update_dtls_id').val();
		
		if (form_validation('update_dtls_id','Save First')==false)
		{
			alert('Please Save First.');
			return;
		}
		else
		{ 	
			var page_link='requires/trims_receive_multi_ref_entry_controller.php?update_dtls_id='+update_dtls_id+'&action=goods_placement_popup';
			var title='Goods Placement Entry Form';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../');
		}
	}



	$('#txt_receive_chal_no').live('keydown', function(e){
		if (e.keyCode === 13)
		{
			var rec_basis=$('#cbo_receive_basis').val();
			var cbo_company_id=$('#cbo_company_id').val();
			var txt_receive_date=$('#txt_receive_date').val();
			if(rec_basis==12)
			{
				get_php_form_data(this.value+"__"+cbo_company_id+"__"+txt_receive_date, "populate_data_from_delivery_challan", "requires/trims_receive_multi_ref_entry_controller" );
				fnc_trims_receive_by_scan(this.value);		
			}
			else
			{
				alert("Please Choose Receive Basis Delivery Challan(Int.)");
				$('#txt_receive_chal_no').val('');		
			}
		
		}
	});

	function fnc_trims_receive_by_scan(operation)
	{

		if( form_validation('cbo_company_id*cbo_receive_basis*txt_receive_date*txt_receive_chal_no*cbo_store_name','Company*Receive Basis*Received Date*Challan No*Store Name')==false )
		{
			return;
		}
		
		// Store upto validation start
		var store_update_upto=$('#store_update_upto').val()*1;
		var cbo_floor=$('#cbo_floor').val()*1;
		var cbo_room=$('#cbo_room').val()*1;
		var txt_rack=$('#txt_rack').val()*1;
		var txt_shelf=$('#txt_shelf').val()*1;
		var cbo_bin=$('#cbo_bin').val()*1;
		
		if(store_update_upto > 1)
		{
			if(store_update_upto==6 && (cbo_floor==0 || cbo_room==0 || txt_rack==0 || txt_shelf==0 || cbo_bin==0))
			{
				alert("Up To Bin Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==5 && (cbo_floor==0 || cbo_room==0 || txt_rack==0 || txt_shelf==0))
			{
				alert("Up To Shelf Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==4 && (cbo_floor==0 || cbo_room==0 || txt_rack==0))
			{
				alert("Up To Rack Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==3 && (cbo_floor==0 || cbo_room==0))
			{
				alert("Up To Room Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==2 && cbo_floor==0)
			{
				alert("Up To Floor Value Full Fill Required For Inventory");return;
			}
		}
		
		var data="action=save_by_scan&"+get_submitted_data_string('cbo_company_id*cbo_receive_basis*txt_receive_date*txt_receive_chal_no*cbo_store_name*txt_challan_date*cbo_source*cbo_supplier_name*cbo_currency_id*txt_exchange_rate*update_id*store_update_upto*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin',"../../");
		freeze_window(operation);
		http.open("POST","requires/trims_receive_multi_ref_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_trims_receive_by_scan_Reply_info;
	}


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
			else if(reponse[0]==20)
			{
				alert("Goods of this Challan is not Garments Accessories. \n So, receiving not allowed here.");release_freezing();return;	
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
					var company_id=$('#cbo_company_id').val();
					var store_id = $('#cbo_store_name').val();
					
					
					reset_form('trimsreceive_1','list_container_trims*list_fabric_desc_container','','','cbo_company_id*is_posted_account*store_update_upto','');
					get_php_form_data(reponse[1], "populate_data_from_trims_recv", "requires/trims_receive_multi_ref_entry_controller" );
					show_list_view(reponse[1]+'_'+company_id+'_'+store_id,'show_trims_listview','list_container_trims','requires/trims_receive_multi_ref_entry_controller','');
					
					reset_form('trimsreceive_1','','','','','update_id*txt_recieved_id*cbo_company_id*cbo_receive_basis*txt_receive_date*txt_receive_chal_no*txt_challan_date*cbo_supplier_name*cbo_store_name*cbo_currency_id*txt_exchange_rate*lc_id*txt_lc_no*cbo_source*txt_booking_pi_no*txt_booking_pi_id*booking_without_order*meterial_source*is_posted_account*store_update_upto');
					$('#ile_td').html('ILE%');
					$('#cbo_payment_over_recv').val(0);
					set_button_status(0, permission, 'fnc_trims_receive',1,1);	
				}
			}
			release_freezing();	
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
	    xmlhttp.open("POST", "requires/trims_receive_multi_ref_entry_controller.php", true);
	    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	    xmlhttp.send(data);
	}

	function openmypage_addiInfo()
	{
		var title = "Additional Info Details";
		var pre_addi_info = $('#txt_addi_info').val();
		page_link='requires/trims_receive_multi_ref_entry_controller.php?action=addi_info_popup&pre_addi_info='+pre_addi_info;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=700px, height=350px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var addi_info_string=this.contentDoc.getElementById("txt_string").value;
			$('#txt_addi_info').val(addi_info_string);
		}

	}
    function remove_audited(){
        $('#audited').html('');
    }

	function print_report_button_setting(report_ids)
	{
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
			{
				if(report_id[k]==86)
				{
					$("#Print_1").show();	 
				}
				if(report_id[k]==110)
				{
					$("#Print_2").show();	 
				}		
			}
	}


</script>
<body onLoad="set_hotkey();">
<div style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission); ?>
    <form name="trimsreceive_1" id="trimsreceive_1">
    	<div style="width:660px; float:left;" align="center">        
            <fieldset style="width:650px">
            <legend>Trims Receive Entry</legend>
			<fieldset style="width:650px">
            <table cellpadding="0" cellspacing="2" width="100%">
                <tr>
                    <td align="right" colspan="3"><strong> Received ID </strong></td>
                    <td>
                        <input type="hidden" name="update_id" id="update_id" />
                        <input type="text" name="txt_recieved_id" id="txt_recieved_id" class="text_boxes" style="width:145px" placeholder="Double Click" onDblClick="trims_receive_popup();" >
                    </td>
                </tr>
                <tr>
                	<td colspan="6" height="10"></td>
                </tr>
                <tr>
                    <td width="100" class="must_entry_caption"> Company </td>
                    <td>
						<? 
							echo create_drop_down( "cbo_company_id", 142, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_room_rack_self_bin('requires/trims_receive_multi_ref_entry_controller*4', 'store','store_td', this.value);load_drop_down( 'requires/trims_receive_multi_ref_entry_controller',this.value, 'load_drop_down_supplier', 'supplier_td_id' );load_drop_down( 'requires/trims_receive_multi_ref_entry_controller',this.value, 'get_receive_basis', 'receive_basis_td' );company_on_change(this.value);load_exchange_rate();get_php_form_data(this.value, 'set_print_button', 'requires/trims_receive_multi_ref_entry_controller');" );
								//load_drop_down( 'requires/trims_receive_multi_ref_entry_controller',this.value, 'load_drop_down_store', 'store_td' )
                        ?>
                    </td>
                    <td class="must_entry_caption"> Receive Basis </td>
                    <td id="receive_basis_td">
                        <? 
                        	echo create_drop_down("cbo_receive_basis",122,$receive_basis_arr,"",1,"-- Select --",0,"set_receive_basis();","",'1,2,4,6,12');
                        ?>
                    </td>
                    <td width="100" class="must_entry_caption"> Received Date </td>
                    <td>
                        <input class="datepicker" type="text" style="width:110px" name="txt_receive_date" id="txt_receive_date" value="<? echo date("d-m-Y")?>" readonly/>
                    </td>
                </tr> 
                <tr>
                	<td class="must_entry_caption">Store Name </td>
                	<td id="store_td">
						<?
                            echo create_drop_down( "cbo_store_name", 142, $blank_array,"",1, "--Select store--", 1, "" );
                        ?>
                    </td> 
                    <td width="100" class="must_entry_caption"> Challan No </td>
                    <td>
                        <input type="text" name="txt_receive_chal_no" id="txt_receive_chal_no" class="text_boxes" style="width:110px" >
                    </td>
                    <td width="100"> Challan Date </td>
                    <td>
                        <input class="datepicker" type="text" style="width:110px" name="txt_challan_date" id="txt_challan_date" readonly />
                    </td>
                </tr>
                <tr>
                    <td>LC No</td>
                    <td>
                    	<input type="text" name="txt_lc_no" id="txt_lc_no" class="text_boxes" style="width:130px" placeholder="Display" disabled>
                        <input type="hidden" name="lc_id" id="lc_id" />
                    </td>
                    <td>Currency</td>
                    <td> 
                        <?
                           echo create_drop_down( "cbo_currency_id", 122,$currency,"", 0, "", 2, "load_exchange_rate();",1 );
                        ?>
                    </td>
                    <td class="must_entry_caption">Source</td>
                    <td>
						<?
							echo create_drop_down( "cbo_source", 122, $source,"", 1, "-- Select --", 0, "get_php_form_data(document.getElementById('cbo_item_group').value+'_'+document.getElementById('cbo_company_id').value+'_'+this.value+'_'+document.getElementById('txt_rate').value, 'show_ile_load_uom', 'requires/trims_receive_multi_ref_entry_controller' );",1 );
						?>
                    </td>
                </tr>
                <tr>
                	<td class="must_entry_caption">Supplier</td>
                    <td id="supplier_td_id"> 
                        <?
                           echo create_drop_down( "cbo_supplier_name", 142, $blank_array,"", 1, "-- Select Supplier --", 0, "",1 );
                        ?>
                    </td>
                    <td>Exchange Rate</td>
                    <td>
                    	<input type="text" name="txt_exchange_rate" id="txt_exchange_rate" class="text_boxes_numeric" style="width:110px" value="" readonly>
                    </td>
                    <td>Addi. Info</td>
                    <td>
                        <input type="text" id="txt_addi_popup" name="txt_addi_popup" class="text_boxes" onDblClick="openmypage_addiInfo()"  placeholder="Double Click" style="width:110px;" readonly >
                        <input type='hidden' id="txt_addi_info" name="txt_addi_info" value="">
                    </td>
                </tr>
				<tr>	                
	                <td>File</td>
	                <td> <input type="button" class="image_uploader" style="width:140px" value="CLICK TO ADD FILE" onClick="file_uploader ( '../../', document.getElementById('txt_recieved_id').value,'', 'trims_receive_entry_multi_ref.', 2 ,1)"> </td>
	                </tr>
            </table>
			</fieldset>
            <br>
            <fieldset style="width:650px">
            <legend>New Entry</legend>
                <table cellpadding="0" cellspacing="2" width="100%">
                    <tr>
                        <td>WO/PI</td>
                        <td>
                            <input type="text" name="txt_booking_pi_no" id="txt_booking_pi_no" class="text_boxes" style="width:140px"  placeholder="Double Click to Search" onDblClick="openmypage_wo_pi_popup();" readonly>
                            <input type="hidden" name="txt_booking_pi_id" id="txt_booking_pi_id" class="text_boxes">
                            <input type="hidden" name="booking_without_order" id="booking_without_order"/>
                            <input type="hidden" name="meterial_source" id="meterial_source"/>
                            <input type="hidden" name="hidd_pay_mode" id="hidd_pay_mode"/>
                        </td>
                        <td width="100">Order UOM</td>
                        <td>
                            <? echo create_drop_down( "cbo_uom", 112, $unit_of_measurement,"", 1, "-- Select UOM --", '0', "",1 ); ?>
                        </td>
                        <td>Amount</td>
                        <td>
                            <input type="text" name="txt_amount" id="txt_amount" class="text_boxes_numeric" style="width:80px;" readonly disabled />	
                        </td>
                    </tr>
                    <tr>
                        <td  width="85" class="must_entry_caption">Item Group</td>
                        <td>
                        <?
                            echo create_drop_down( "cbo_item_group", 152, "select id,item_name from lib_item_group where item_category=4 and status_active=1 and is_deleted=0 order by item_name", "id,item_name", 1, "-- Select --", 0,  "get_php_form_data(this.value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_source').value+'_'+document.getElementById('txt_rate').value, 'show_ile_load_uom', 'requires/trims_receive_multi_ref_entry_controller' );",1 );
                         ?>	
                        </td>
                        <td class="must_entry_caption"  width="80">Receive Qnty</td>
                        <td>
                            <input type="text" name="txt_receive_qnty" id="txt_receive_qnty" class="text_boxes_numeric" style="width:100px;" onKeyUp="calculate();" disabled/>	
                        </td>
                        <td>Reject Qnty</td>
                        <td>
                            <input type="text" name="txt_reject_recv_qnty" id="txt_reject_recv_qnty" class="text_boxes_numeric" style="width:80px;" />	
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Item Description </td>
                        <td>
                            <input type="text" name="txt_item_description" id="txt_item_description" class="text_boxes" style="width:140px" disabled/>
                            <input type="hidden" name="hidden_item_description" id="hidden_item_description" disabled/>
                        </td>
                        <td class="must_entry_caption">Rate</td>   
                        <td><input name="txt_rate" id="txt_rate" class="text_boxes_numeric" type="text" style="width:100px;" onBlur="get_php_form_data(document.getElementById('cbo_item_group').value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_source').value+'_'+this.value, 'show_ile_load_uom', 'requires/trims_receive_multi_ref_entry_controller' );calculate();"/>
                        	<input type="hidden" name="txt_hidden_rate" id="txt_hidden_rate" class="text_boxes_numeric"></td>
                        <td>Book Keeping Currency</td>
                        <td><input type="text" name="txt_book_currency" id="txt_book_currency" class="text_boxes_numeric" style="width:80px;" readonly disabled /></td>
                    </tr>
                    <tr>
                    	<td>Brand/Sup Ref</td>
                        <td>
                            <input type="text" name="txt_brand_supref" id="txt_brand_supref" class="text_boxes" style="width:140px"/>
                        </td>
                        <td id="ile_td">ILE%</td>   
                        <td><input name="txt_ile" id="txt_ile" class="text_boxes_numeric" type="text" style="width:100px;" placeholder="ILE COST" readonly disabled /></td>
                        <td>Balance PI / Order Qnty</td>
                        <td><input class="text_boxes_numeric"  name="txt_bl_qty" id="txt_bl_qty" type="text" style="width:80px;" readonly disabled /></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Buyer Order</td>
                        <td>
                            <input type="text" name="txt_buyer_order" id="txt_buyer_order" class="text_boxes" style="width:140px;" onClick="openmypage_po()" placeholder="Single Click" readonly/>	
                        </td>
                        <td><!--Gmts--> Item Color</td>
                        <td>
                        	<input type="text" name="txt_item_color" id="txt_item_color" class="text_boxes" style="width:100px;" disabled/>
                            <input type="hidden" name="txt_gmts_color_id" id="txt_gmts_color_id" readonly>
                            <input type="hidden" name="txt_gmts_color" id="txt_gmts_color" readonly>
                            <input type="hidden" name="txt_item_color_id" id="txt_item_color_id" readonly>
                        </td>
                        <td>Payment For.</td>
                        <? $payment_yes_no=array(0=>"Yes",1=>"No"); ?>
                        <td><? echo create_drop_down( "cbo_payment_over_recv", 85, $payment_yes_no,"", 0, "-- Select --", '0', "",0 ); ?></td>
                    </tr> 
                    <tr>
                       <td>Floor</td>                       
                       	<td id="floor_td">
							<? echo create_drop_down( "cbo_floor", 150,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
						</td>
                        <td><!--Gmts-->Item Size</td>   
                        <td>
                        	<input type="text" name="txt_item_size" id="txt_item_size" class="text_boxes" style="width:100px;" disabled/>
                            <input type="hidden" name="txt_gmts_size" id="txt_gmts_size" readonly>
                            <input type="hidden" name="txt_gmts_size_id" id="txt_gmts_size_id" readonly>
                        </td>
                        <td>Color/Size Balance</td>
                        <td><input type="text" name="txt_color_size_balance" id="txt_color_size_balance" class="text_boxes" style="width:80px;" disabled/></td>
                    </tr>
                    <tr>
                    	<td>Room</td>
                        <td id="room_td">
							<? echo create_drop_down( "cbo_room", 150,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
						</td>
						<td>Remarks</td>
                        <td colspan="3"><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:300px;"/></td>
                    </tr>
                    <tr>
                    	<td>Rack</td>   
                        <td id="rack_td">
							<? echo create_drop_down( "txt_rack", 150,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
						</td>
                    </tr>
                    <tr>
                    	<td>Shelf</td>   
                    	 <td id="shelf_td">
							<? echo create_drop_down( "txt_shelf", 150,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
						</td>
                    </tr> 
                    <tr>
                    	<td>Box/Bin</td>
                        <td id="bin_td">
							<? echo create_drop_down( "cbo_bin", 150,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
						</td>
                    </tr>
                    <tr>
                    	<td></td>
                        <td><input type="button" class="formbuttonplasminus" style="width:150px" value="Goods Placement" onClick="openmypage_goodsPlacement();"></td>
                    </tr>
                    <tr>
                        <td colspan="6" align="center" class="button_container">
                            <div id="audited" style="float:left; font-size:24px; color:#FF0000;"></div>
                            <? 
                                echo load_submit_buttons($permission, "fnc_trims_receive", 0,0,"reset_form('trimsreceive_1','list_container_trims*list_fabric_desc_container','','','disable_enable_fields(\'cbo_company_id\');set_receive_basis();remove_audited();','cbo_company_id*cbo_receive_basis*txt_receive_date*is_posted_account*store_update_upto')",1);

                            ?>
                            <input id="Print_2" name="Print_2" type="button" class="formbutton" style="width:80px;display:none;" onClick="fnc_trims_receive(5)" value="Print 2">
                            <input id="Print_1" name="Print_1" type="button" class="formbutton" style="width:80px;display:none;" onClick="fnc_trims_receive(4)" value="Print">

                            <input type="hidden" name="save_data" id="save_data" readonly>
                            <input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
                            <input type="hidden" name="update_trans_id" id="update_trans_id" readonly>
                            <input type="hidden" name="previous_prod_id" id="previous_prod_id" readonly>
                            <input type="hidden" name="hidden_sensitivity" id="hidden_sensitivity" readonly>
                            <input type="hidden" name="all_po_id" id="all_po_id" readonly>
                            <input type="hidden" id="is_posted_account" name="is_posted_account" value=""/>
                            <input type="hidden" name="store_update_upto" id="store_update_upto">
                        </td>	  
                    </tr>
                    <tr>
                        <td colspan="6" align="center">
                            <div id="accounting_posted_status" style=" color:red; font-size:24px;";  ></div>
                        </td>
                    </tr>
                 </table>
            </fieldset>
            <br>
            <div style="width:650px;" id="list_container_trims"></div>
		</fieldset>
        </div>  
        <div style="width:5px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
        <div id="list_fabric_desc_container" style="overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>$('#cbo_payment_over_recv').val(0);</script>
</html>
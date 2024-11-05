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
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id,store_location_id,item_cate_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];

if ($company_id>0) {
    $company_credential_cond = "and comp.id in($company_id)";
}

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Trims Receive Entry", "../../", 1, 1,'','1','');
?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	<? 
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][24] );
	if($data_arr) echo "var field_level_data= ". $data_arr . ";\n";
	//echo "alert(JSON.stringify(field_level_data));";
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
		
		
		if(recieve_basis == 1 || recieve_basis == 2)
		{
			$('#txt_rate').attr('disabled', true);
		}
		else
		{
			$('#txt_rate').attr('disabled', false);
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
		//alert("clicked");
		if (form_validation('cbo_company_id*cbo_receive_basis','Company*Receive Basis')==false)
		{
			return;
		}
		else
		{ 
			var title = 'WO/PI Selection Form';	
			var page_link = 'requires/trims_receive_entry_controller_for_stock_correction.php?cbo_company_id='+cbo_company_id+'&recieve_basis='+recieve_basis+'&action=wo_pi_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1310px,height=420px,center=1,resize=1,scrolling=0','../');
			
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
				if(data[6]!="")
				{
					load_drop_down( 'requires/trims_receive_entry_controller_for_stock_correction',cbo_company_id+'**'+data[6], 'load_drop_down_supplier', 'supplier_td_id' );
					$('#suplier_type').val(data[6]);
				}
				
				
				$('#txt_booking_pi_no').val(theename);
				$('#txt_booking_pi_id').val(theemail);
				$('#booking_without_order').val(booking_without_order);
				$('#cbo_supplier_name').val(data[0]);
				$('#cbo_currency_id').val(data[1]);	
				$('#cbo_source').val(data[2]);
				$('#txt_lc_no').val(data[3]);	
				$('#lc_id').val(data[4]);
				$('#txt_wo_pi_basis_id').val(data[5]);
				$('#meterial_source').val(data[7]);
				if(data[7]==0) $('#txt_rate').val(0);
				
				load_exchange_rate();
		
				if(recieve_basis==1) var booking_no_pi_id=theemail; else var booking_no_pi_id=theename;

				show_list_view(booking_no_pi_id+"**"+recieve_basis+"**"+booking_without_order,'show_fabric_desc_listview','list_fabric_desc_container','requires/trims_receive_entry_controller_for_stock_correction','setFilterGrid(\'tbl_list_search\',-1);');
				
				release_freezing();
			}
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
		$('#wo_order_id').val(data[11]);
		$('#wo_order_qty').val(data[12]);

		
		get_php_form_data(data[0]+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_source').value+'_'+document.getElementById('txt_rate').value, 'show_ile_load_uom', 'requires/trims_receive_entry_controller_for_stock_correction' );
		
				
		var booking_no_pi_id=$('#txt_booking_pi_id').val(); 
		var booking_no_pi_no=$('#txt_booking_pi_no').val();
		//var item_description=encodeURIComponent(trim(data[1]));
		var item_description=encodeURIComponent(data[1]);
		var brand_sup_ref=encodeURIComponent(trim(data[2]));
		if(recieve_basis==1 || recieve_basis==2)
		{
			//alert(data[10]); return;
			//get_php_form_data(recieve_basis+'_'+booking_no_pi_id+'_'+booking_no_pi_no+'_'+data[0]+'_'+data[1]+'_'+data[2]+'_'+data[3]+'_'+data[4]+'_'+data[6]+'_'+data[8]+'_'+data[10]+'_'+booking_without_order+'_'+data[11], 'put_balance_qnty', 'requires/trims_receive_entry_controller_for_stock_correction' );
			get_php_form_data(recieve_basis+'__'+booking_no_pi_id+'__'+booking_no_pi_no+'__'+data[0]+'__'+item_description+'__'+brand_sup_ref+'__'+data[3]+'__'+data[4]+'__'+data[6]+'__'+data[8]+'__'+data[10]+'__'+booking_without_order+'__'+data[11]+'____'+data[13], 'put_balance_qnty', 'requires/trims_receive_entry_controller_for_stock_correction' );
		}
		
		set_button_status(0, permission, 'fnc_trims_receive',1,1);
	}
	
	function set_rate_balance(data)
	{
		var recieve_basis = $('#cbo_receive_basis').val();
		var booking_no_pi_id=$('#txt_booking_pi_id').val(); 
		var booking_no_pi_no=$('#txt_booking_pi_no').val();
		var data=data.split("_");
		//var txt_item_description = encodeURIComponent(trim(data[1]));
		var txt_item_description = encodeURIComponent(data[1]);
		var txt_brand_supref = encodeURIComponent(trim(data[2]));
		get_php_form_data(recieve_basis+'__'+booking_no_pi_id+'__'+booking_no_pi_no+'__'+data[0]+'__'+txt_item_description+'__'+txt_brand_supref+'__'+data[3]+'__'+data[4]+'__'+data[5]+'__'+data[6]+'__'+data[7]+'__'+data[8]+'__'+data[9]+'__'+data[11]+'__'+data[10], 'put_balance_qnty', 'requires/trims_receive_entry_controller_for_stock_correction' );
	}
	
	function openmypage_po()
	{
		var receive_basis=$('#cbo_receive_basis').val();
		var booking_no=$('#txt_booking_pi_no').val()+" ";
		var booking_pi_id=$('#txt_booking_pi_id').val();
		var wo_pi_basis_id=$('#txt_wo_pi_basis_id').val();
		var cbo_company_id = $('#cbo_company_id').val();
		var save_data = $('#save_data').val();
		var all_po_id = $('#all_po_id').val();
		var txt_receive_qnty = $('#txt_receive_qnty').val(); 
		var item_group=$('#cbo_item_group').val();
		//var hidden_item_description=$('#hidden_item_description').val();
		//var txt_item_description=$('#txt_item_description').val();
		var hidden_item_description=encodeURIComponent($('#hidden_item_description').val());
		var txt_item_description=encodeURIComponent($('#txt_item_description').val());
		var brand_supref=encodeURIComponent(trim($('#txt_brand_supref').val()));
		var sensitivity=$('#hidden_sensitivity').val();
		var order_uom=$('#cbo_uom').val();
		var gmts_color_id=$('#txt_gmts_color_id').val();
		var gmts_size_id=$('#txt_gmts_size_id').val();
		var item_color_id=$('#txt_item_color_id').val();
		var item_size=$('#txt_item_size').val();
		var txt_rate=$('#txt_rate').val();
		var txt_hidden_rate=$('#txt_hidden_rate').val();
		
		var item_description='';
		
		if( receive_basis==2 || receive_basis==1 ) item_description=hidden_item_description; else item_description=txt_item_description;

		if (form_validation('cbo_company_id*cbo_receive_basis','Company*Receive Basis')==false)
		{
			return;
		}
		
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
		
		var title = 'PO Info';	
		var page_link = 'requires/trims_receive_entry_controller_for_stock_correction.php?receive_basis='+receive_basis+'&cbo_company_id='+cbo_company_id+'&booking_no='+booking_no+'&booking_pi_id='+booking_pi_id+'&wo_pi_basis_id='+wo_pi_basis_id+'&item_group='+item_group+'&item_description='+item_description+'&brand_supref='+brand_supref+'&order_uom='+order_uom+'&sensitivity='+sensitivity+'&gmts_color_id='+gmts_color_id+'&gmts_size_id='+gmts_size_id+'&item_color_id='+item_color_id+'&item_size='+item_size+'&all_po_id='+all_po_id+'&save_data='+save_data+'&txt_receive_qnty='+txt_receive_qnty+'&txt_rate='+txt_hidden_rate+'&action=po_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=740px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var save_string=this.contentDoc.getElementById("save_string").value;	 //Access form field with id="emailfield"
			var tot_trims_qnty=this.contentDoc.getElementById("tot_trims_qnty").value; //Access form field with id="emailfield"
			var all_po_id=this.contentDoc.getElementById("all_po_id").value; //Access form field with id="emailfield"
			var all_po_no=this.contentDoc.getElementById("all_po_no").value; //Access form field with id="emailfield"

			$('#save_data').val(save_string);
			$('#txt_receive_qnty').val(tot_trims_qnty);
			$('#all_po_id').val(all_po_id);
			$('#txt_buyer_order').val(all_po_no);
			
			calculate();
		}
	}
	
	function fnc_trims_receive(operation)
	{
		if(operation==4)
		{
			 var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "trims_receive_entry_print", "requires/trims_receive_entry_controller_for_stock_correction" ) 
			 return;
		}
		else if(operation==5)
		{
			 var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "trims_receive_entry_print_2", "requires/trims_receive_entry_controller_for_stock_correction" ) 
			 return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			/*if(operation==2)
			{
				show_msg('13');
				return;
			}*/
			
			if ($("#is_posted_account").val()*1 == 1) {
				alert("Already Posted In Accounting. Save Update Delete Restricted.");
				return;
			}
			
			var rcv_basis=$('#cbo_receive_basis').val()*1;
			if(rcv_basis==6)
			{
				if( form_validation('cbo_company_id*cbo_receive_basis*txt_receive_date*txt_receive_chal_no*cbo_store_name*cbo_source*cbo_item_group*txt_item_description*txt_receive_qnty','Company*Receive Basis*Received Date*Challan No*Store Name*Source*Item Group*Item Description*Receive Qnty')==false )
				{
					return;
				}
			}
			else
			{
				if( form_validation('cbo_company_id*cbo_receive_basis*txt_receive_date*txt_receive_chal_no*cbo_store_name*cbo_supplier_name*cbo_source*cbo_item_group*txt_item_description*txt_receive_qnty','Company*Receive Basis*Received Date*Challan No*Store Name*Supplier*Source*Item Group*Item Description*Receive Qnty')==false )
				{
					return;
				}
			}
			
			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_receive_date').val(), current_date)==false)
			{
				alert("Receive Date Can not Be Greater Than Current Date");
				return;
			}
			var meterial_source=$('#meterial_source').val();
			
			if($('#cbo_payment_over_recv').val()==0 && meterial_source != 3)
			{
				if(form_validation('txt_rate','Rate')==false )
				{
					return;
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
			
			//var hidden_item_description=encodeURIComponent(trim($('#hidden_item_description').val()));
			// txt_rate txt_hidden_rate txt_amount txt_hidden_amount
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_recieved_id*cbo_company_id*cbo_receive_basis*txt_receive_date*txt_booking_pi_no*txt_booking_pi_id*booking_without_order*txt_receive_chal_no*txt_challan_date*lc_id*cbo_source*cbo_store_name*cbo_supplier_name*cbo_currency_id*txt_exchange_rate*cbo_item_group*cbo_uom*txt_item_description*txt_receive_qnty*txt_reject_recv_qnty*txt_brand_supref*txt_rate*txt_hidden_rate*txt_book_currency*txt_buyer_order*txt_ile*txt_amount*txt_hidden_amount*update_id*all_po_id*update_dtls_id*update_trans_id*save_data*previous_prod_id*hidden_sensitivity*txt_gmts_color_id*txt_gmts_color*txt_item_color_id*txt_item_color*txt_gmts_size_id*txt_gmts_size*txt_item_size*cbo_payment_over_recv*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*txt_remarks*suplier_type*hidden_item_description*meterial_source*wo_order_qty*wo_order_id*store_update_upto',"../../");
			
			// alert(data);return;
			
			freeze_window(operation);
			
			http.open("POST","requires/trims_receive_entry_controller_for_stock_correction.php",true);
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
			if(reponse[0]*1==40*1)
			{
				alert(reponse[1]);
				release_freezing();
				return;	
			}
			else if(reponse[0]*1==20*1)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			else if(reponse[0]*1==18*1)
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
					show_list_view(reponse[1],'show_trims_listview','list_container_trims','requires/trims_receive_entry_controller_for_stock_correction','');
					
					reset_form('trimsreceive_1','','','','','update_id*txt_recieved_id*cbo_company_id*cbo_receive_basis*txt_receive_date*txt_wo_pi_basis_id*txt_receive_chal_no*txt_challan_date*txt_booking_pi_no*txt_booking_pi_id*cbo_supplier_name*cbo_store_name*cbo_currency_id*txt_exchange_rate*lc_id*txt_lc_no*cbo_source*booking_without_order*meterial_source*store_update_upto');
					$('#ile_td').html('ILE%');
					$('#cbo_payment_over_recv').val(0);
					set_button_status(0, permission, 'fnc_trims_receive',1,1);	
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
			var page_link='requires/trims_receive_entry_controller_for_stock_correction.php?cbo_company_id='+cbo_company_id+'&action=trims_receive_popup_search';
			var title='Trims Receive Form';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=390px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var trims_recv_id=this.contentDoc.getElementById("hidden_recv_id").value;

				if(trims_recv_id!="")
				{
					freeze_window(5);
					reset_form('trimsreceive_1','list_container_trims*list_fabric_desc_container','','','cbo_company_id','');
					
					var posted_in_account=this.contentDoc.getElementById("hidden_posted_in_account").value; // is posted accounct
					$("#is_posted_account").val(posted_in_account);
					if(posted_in_account==1) document.getElementById("accounting_posted_status").innerHTML="Already Posted In Accounting.";
					else 					 document.getElementById("accounting_posted_status").innerHTML="";
					
					get_php_form_data(trims_recv_id, "populate_data_from_trims_recv", "requires/trims_receive_entry_controller_for_stock_correction" );
					
					var booking_pi_no = $('#txt_booking_pi_no').val();
					var booking_pi_id = $('#txt_booking_pi_id').val();
					var booking_without_order = $('#booking_without_order').val();
					var recieve_basis = $('#cbo_receive_basis').val();
					
					if(recieve_basis==1) var booking_no_pi_id=booking_pi_id; else if(recieve_basis==2) var booking_no_pi_id=booking_pi_no;

					if(recieve_basis==1 || recieve_basis==2)
					{
						show_list_view(booking_no_pi_id+"**"+recieve_basis+"**"+booking_without_order,'show_fabric_desc_listview','list_fabric_desc_container','requires/trims_receive_entry_controller_for_stock_correction','setFilterGrid(\'tbl_list_search\',-1);');
					}
					
					show_list_view(trims_recv_id,'show_trims_listview','list_container_trims','requires/trims_receive_entry_controller_for_stock_correction','');
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
		var rate			= $('#txt_rate').val();	 
		var hidden_rate		= $('#txt_hidden_rate').val();	 
		var ile_cost 		= $("#txt_ile").val();
		var amount 			= quantity*1*(rate*1+ile_cost*1); 
		// alert(quantity+'='+hidden_rate+'='+ile_cost);
		var hidden_amount 	= quantity*1*(hidden_rate*1+ile_cost*1); 
		var bookCurrency 	= (rate*1+ile_cost*1)*exchangeRate*1*quantity*1;
		$("#txt_amount").val(number_format_common(amount,"","",currency_id));
		$('#txt_hidden_amount').val(hidden_amount);
		$("#txt_book_currency").val(number_format_common(bookCurrency,"","",1));
	}
	
	function load_exchange_rate()
	{
		var currency_id= $("#cbo_currency_id").val();
		get_php_form_data(currency_id, "get_library_exchange_rate", "requires/trims_receive_entry_controller_for_stock_correction" );
		//if(currency_id==1) var exchange_rate=1; else var exchange_rate=80;
		//$("#txt_exchange_rate").val(exchange_rate);
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
			var page_link='requires/trims_receive_entry_controller_for_stock_correction.php?update_dtls_id='+update_dtls_id+'&action=goods_placement_popup';
			var title='Goods Placement Entry Form';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../');
		}
	}

	/*function company_on_change(company)
	{
		var item_category = $('#cbo_item_category').val();
		page_link = 'cbo_company_id='+company+'&action=requ_variable_settings';

		$.ajax({
			url: 'requires/trims_receive_entry_controller_for_stock_correction.php',
			type: 'POST',
			data: page_link,
			success: function (response)
			{
				$('#store_update_upto').val(response);
			}
		});
	}*/


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
	    xmlhttp.open("POST", "requires/trims_receive_entry_controller_for_stock_correction.php", true);
	    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	    xmlhttp.send(data);
	}

</script>
<body onLoad="set_hotkey();load_exchange_rate();">
<div style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission); ?>
    <form name="trimsreceive_1" id="trimsreceive_1" data-entry_form="24">
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
                    <td width="70" class="must_entry_caption"> Company </td>
                    <td>
						<? 
							echo create_drop_down( "cbo_company_id", 142, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond  $company_credential_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down( 'requires/trims_receive_entry_controller_for_stock_correction',this.value, 'load_drop_down_supplier', 'supplier_td_id' );set_receive_basis();load_room_rack_self_bin('requires/trims_receive_entry_controller_for_stock_correction*4', 'store','store_td', this.value);company_on_change(this.value);" );
								//load_drop_down( 'requires/trims_receive_entry_controller_for_stock_correction',this.value, 'load_drop_down_store', 'store_td' );
                        ?>
                    </td>
                    <td width="80" class="must_entry_caption"> Receive Basis </td>
                    <td>
                        <? 
                        	echo create_drop_down("cbo_receive_basis",122,$receive_basis_arr,"",1,"-- Select --",0,"set_receive_basis();","",'1,2,4,6');
                        ?>
                    </td>
                    <td width="80" class="must_entry_caption"> Received Date </td>
                    <td>
                        <input class="datepicker" type="text" style="width:110px" name="txt_receive_date" id="txt_receive_date" value="<? echo date("d-m-Y")?>" />
                    </td>
                </tr> 
                <tr>
                	<td>WO/PI</td>
                    <td>
                    	<input type="text" name="txt_booking_pi_no" id="txt_booking_pi_no" class="text_boxes" style="width:130px"  placeholder="Double Click to Search" onDblClick="openmypage_wo_pi_popup();" readonly>
                        <input type="hidden" name="txt_booking_pi_id" id="txt_booking_pi_id" class="text_boxes">
                        <input type="hidden" name="booking_without_order" id="booking_without_order"/>
                        <input type="hidden" name="meterial_source" id="meterial_source"/>
                         
                    </td>
                    <td width="100" class="must_entry_caption"> Challan No </td>
                    <td>
                        <input type="text" name="txt_receive_chal_no" id="txt_receive_chal_no" class="text_boxes" style="width:110px" >
                    </td>
                    <td width="100"> Challan Date </td>
                    <td>
                        <input class="datepicker" type="text" style="width:110px" name="txt_challan_date" id="txt_challan_date"/>
                    </td>
                </tr>
                <tr>
                    <td>LC No</td>
                    <td>
                    	<input type="text" name="txt_lc_no" id="txt_lc_no" class="text_boxes" style="width:130px" placeholder="Display" disabled>
                        <input type="hidden" name="lc_id" id="lc_id" />
                        <input type="hidden" name="txt_wo_pi_basis_id" id="txt_wo_pi_basis_id" style="width:30px">
                    </td>
                    <td class="must_entry_caption">Source</td>
                    <td>
						<?
							echo create_drop_down( "cbo_source", 122, $source,"", 1, "-- Select --", 0, "get_php_form_data(document.getElementById('cbo_item_group').value+'_'+document.getElementById('cbo_company_id').value+'_'+this.value+'_'+document.getElementById('txt_rate').value, 'show_ile_load_uom', 'requires/trims_receive_entry_controller_for_stock_correction' );",1 );
						?>
                    </td>
                    <td class="must_entry_caption">Store Name </td>
                    <td id="store_td">
						<?
                            echo create_drop_down( "cbo_store_name", 122, $blank_array,"",1, "--Select store--", 1, "" );
                        ?>
                    </td> 
                </tr>
                <tr>
                    <td>Supplier</td>
                    <td id="supplier_td_id"> 
                        <?

                           echo create_drop_down( "cbo_supplier_name", 142, $blank_array,"", 1, "-- Select Supplier --", 0, "",1 );
                        ?>
                    </td>
                    <td>Currency</td>
                    <td> 
                        <?
                           echo create_drop_down( "cbo_currency_id", 122,$currency,"", 0, "", 2, "load_exchange_rate();",1 );
                        ?>
                    </td>
                    <td>Exchange Rate</td>
                    <td>
                    	<input type="text" name="txt_exchange_rate" id="txt_exchange_rate" class="text_boxes_numeric" style="width:110px" value="" readonly>
                    </td>
                </tr>
            </table>
			</fieldset>
            <br>
            <fieldset style="width:650px">
            <legend>New Entry</legend>
                <table cellpadding="0" cellspacing="2" width="100%">
                    <tr>
                        <td width="85" class="must_entry_caption">Item Group</td>
                        <td>
                        <?
                            echo create_drop_down( "cbo_item_group", 152, "select id,item_name from lib_item_group where item_category=4 and status_active=1 and is_deleted=0 order by item_name", "id,item_name", 1, "-- Select --", 0,  "get_php_form_data(this.value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_source').value+'_'+document.getElementById('txt_rate').value, 'show_ile_load_uom', 'requires/trims_receive_entry_controller_for_stock_correction' );",1 );
                         ?>	
                        </td>
                        <td width="80">Order UOM</td>
                        <td>
                            <? echo create_drop_down( "cbo_uom", 92, $unit_of_measurement,"", 1, "-- Select UOM --", '0', "",1 ); ?>
                        </td>
                        <td>Amount</td>
                        <td>
                            <input type="text" name="txt_amount" id="txt_amount" class="text_boxes_numeric" style="width:80px;" readonly disabled />
                            <input type="hidden" name="txt_hidden_amount" id="txt_hidden_amount" class="text_boxes_numeric">	
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Item Description </td>
                        <td>
                            <input type="text" name="txt_item_description" id="txt_item_description" class="text_boxes" style="width:140px" disabled/>
                            <input type="hidden" name="hidden_item_description" id="hidden_item_description" disabled/>
                        </td>
                        <td class="must_entry_caption">Receive Qnty</td>
                        <td>
                            <input type="text" name="txt_receive_qnty" id="txt_receive_qnty" class="text_boxes_numeric" style="width:80px;" onKeyUp="calculate();" disabled/>	
                        </td>
                        <td>Reject Qnty</td>
                        <td>
                            <input type="text" name="txt_reject_recv_qnty" id="txt_reject_recv_qnty" class="text_boxes_numeric" style="width:80px;" />	
                        </td>
                    </tr>
                    <tr>
                    	<td>Brand/Sup Ref</td>
                        <td>
                            <input type="text" name="txt_brand_supref" id="txt_brand_supref" class="text_boxes" style="width:140px"/>
                        </td>
                        <td class="must_entry_caption">Rate</td>   
                        <td><input name="txt_rate" id="txt_rate" class="text_boxes_numeric" type="text" style="width:80px;" onBlur="get_php_form_data(document.getElementById('cbo_item_group').value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_source').value+'_'+this.value, 'show_ile_load_uom', 'requires/trims_receive_entry_controller_for_stock_correction' );calculate();"/>
                        <input type="hidden" name="txt_hidden_rate" id="txt_hidden_rate" class="text_boxes_numeric"></td>
                        <td>Book Keeping Currency</td>
                        <td><input type="text" name="txt_book_currency" id="txt_book_currency" class="text_boxes_numeric" style="width:80px;" readonly disabled /></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Buyer Order</td>
                        <td>
                            <input type="text" name="txt_buyer_order" id="txt_buyer_order" class="text_boxes" style="width:140px;" onClick="openmypage_po()" placeholder="Single Click" readonly/>	
                        </td>
                        <td id="ile_td">ILE%</td>   
                        <td><input name="txt_ile" id="txt_ile" class="text_boxes_numeric" type="text" style="width:80px;" placeholder="ILE COST" readonly disabled /></td>
                        <td>Balance PI / Order Qnty</td>
                        <td><input class="text_boxes_numeric"  name="txt_bl_qty" id="txt_bl_qty" type="text" style="width:80px;" readonly disabled /></td>
                    </tr> 
                    <tr>
                        <td><!--Gmts--> Item Color</td>
                        <td>
                        	<input type="text" name="txt_item_color" id="txt_item_color" class="text_boxes" style="width:140px;" disabled/>
                            <input type="hidden" name="txt_gmts_color_id" id="txt_gmts_color_id" readonly>
                            <input type="hidden" name="txt_gmts_color" id="txt_gmts_color" readonly>
                            <input type="hidden" name="txt_item_color_id" id="txt_item_color_id" readonly>
                        </td>
                        <td><!--Gmts-->Item Size</td>   
                        <td>
                        	<input type="text" name="txt_item_size" id="txt_item_size" class="text_boxes" style="width:80px;" disabled/>
                            <input type="hidden" name="txt_gmts_size" id="txt_gmts_size" readonly>
                            <input type="hidden" name="txt_gmts_size_id" id="txt_gmts_size_id" readonly>
                        </td>
                        <td>Payment For Over Rcv. Qty.</td>
                        <? $payment_yes_no=array(0=>"Yes",1=>"No"); ?>
                        <td><? echo create_drop_down( "cbo_payment_over_recv", 92, $payment_yes_no,"", 0, "-- Select --", '0', "",0 ); ?></td>
                    </tr>
                     <tr>
                    	<td>Floor</td>                       
                       	<td id="floor_td">
							<? echo create_drop_down( "cbo_floor", 150,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
						</td>
                       <td></td>
                       <td></td>
                    </tr>                                 
                    <tr>
                    	 <td>Room</td>
                        <td id="room_td">
							<? echo create_drop_down( "cbo_room", 150,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
						</td>
                       <td></td>
                       <td></td>
                    </tr>
                    <tr>
                    	<td>Rack</td>   
                        <td id="rack_td">
							<? echo create_drop_down( "txt_rack", 150,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
						</td>
                        <td></td>
                       <td></td>
                    </tr>
                    <tr>
                    	<td>Shelf</td>   
                    	 <td id="shelf_td">
							<? echo create_drop_down( "txt_shelf", 150,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
						</td>
                        <td></td>   
                        <td></td>
                    </tr>
                    <tr>
                    	<td>Box/Bin</td>
                        <td id="bin_td">
							<? echo create_drop_down( "cbo_bin", 150,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
						</td>
                        <td></td>   
                        <td></td>        	
                    </tr>
                    <tr>
                       
                        <td>Remarks</td>
	                    <td colspan="5">
	                        <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:311px;" />	
	                    </td>
                       
                    </tr> 
                    <tr style="display:none">
                    	<td></td>
                        <td colspan="4"><input type="hidden" class="formbuttonplasminus" style="width:150px" value="Goods Placement" onClick="openmypage_goodsPlacement();"></td>
                    </tr>
                    <tr>
                        <td colspan="6" align="center" class="button_container">
                            <? //trimsreceive_1
                                echo load_submit_buttons($permission, "fnc_trims_receive", 0,1,"reset_form('trimsreceive_1','list_container_trims*list_fabric_desc_container','','','disable_enable_fields(\'cbo_company_id\');set_receive_basis();','cbo_company_id*cbo_receive_basis*txt_receive_date*txt_receive_chal_no*txt_challan_date*cbo_store_name')",1);
                            ?>
                            
                            
                            <input id="Print1" type="button" class="formbutton" style="width:80px" onClick="fnc_trims_receive(5)" name="print" value="Print 2">
                            
                            <input type="hidden" name="save_data" id="save_data" readonly>
                            <input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
                            <input type="hidden" name="update_trans_id" id="update_trans_id" readonly>
                            <input type="hidden" name="previous_prod_id" id="previous_prod_id" readonly>
                            <input type="hidden" name="hidden_sensitivity" id="hidden_sensitivity" readonly>
                            <input type="hidden" name="all_po_id" id="all_po_id" readonly>
                            <input type="hidden" id="suplier_type" name="suplier_type" value="2" />
                            <input type="hidden" id="wo_order_qty" name="wo_order_qty" value="2" />
                            <input type="hidden" id="wo_order_id" name="wo_order_id" value="2" />
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
        <div style="width:10px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
        <div id="list_fabric_desc_container" style="overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>$('#cbo_payment_over_recv').val(0);</script>
</html>
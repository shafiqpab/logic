<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Trims Issue Entry 
Functionality	:	
JS Functions	:
Created by		:	Fuad Shahriar 
Creation date 	: 	22/09/2013
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
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];

if ($company_id !='') {
    $company_credential_cond = "and comp.id in($company_id)";
}

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Trims Issue Entry", "../../", 1, 1,'','1',''); 

?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	<? 
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][25] ); 
	if($data_arr) echo "var field_level_data= ". $data_arr . ";\n";
	//echo "alert(JSON.stringify(field_level_data));";
	?>

	function openmypage_po()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var all_po_id = $('#all_po_id').val();
		var buyer_name=$('#cbo_buyer_name').val();
		var cbo_store_name=$('#cbo_store_name').val();

		if (form_validation('cbo_company_id*cbo_store_name','Company*Store Name')==false)
		{
			return;
		}
			
		var title = 'PO Info';	
		var page_link = 'requires/trims_issue_entry_controller.php?cbo_company_id='+cbo_company_id+'&all_po_id='+all_po_id+'&buyer_name='+buyer_name+'&action=po_search_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=380px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var hidden_order_id=this.contentDoc.getElementById("hidden_order_id").value; //Access form field with id="emailfield"
			var hidden_order_no=this.contentDoc.getElementById("hidden_order_no").value; //Access form field with id="emailfield"
			var hide_buyer=this.contentDoc.getElementById("hide_buyer").value; //Access form field with id="emailfield"
			
			if(hidden_order_id!=all_po_id)
			{
				reset_form('','','hidden_prod_id*txt_item_description*save_data*distribution_method_id*txt_issue_qnty*cbo_item_group*txt_item_color*txt_item_color_id*gmts_color_id*txt_item_size*gmts_size_id*txt_brand_supref*txt_rack*txt_shelf*cbo_uom*txt_yet_to_issue*txt_received_qnty*txt_cumulative_issued','','','cbo_company_id*cbo_location*cbo_store_name*is_posted_account*store_update_upto');
			}
			
			$('#all_po_id').val(hidden_order_id);
			$('#txt_buyer_order').val(hidden_order_no);
			$('#cbo_buyer_name').val(hide_buyer);
			var store_update_upto=$('#store_update_upto').val();
			
			show_list_view(hidden_order_id+'****'+cbo_store_name+'**'+store_update_upto,'create_itemDesc_search_list_view','list_fabric_desc_container','requires/trims_issue_entry_controller','setFilterGrid(\'tbl_list_search\',-1);');
		}
	}
	
	function openpage_booking()
	{
		var cbo_company_id 	= $('#cbo_company_id').val();
		var cbo_basis 		= $('#cbo_basis').val();
		if (form_validation('cbo_company_id*cbo_basis*cbo_store_name','Company*Issue Basis*Store Name')==false)
		{
			return;
		}
			
		var title = 'Trims Booking/Requisition Info';	
		var page_link = 'requires/trims_issue_entry_controller.php?cbo_company_id='+cbo_company_id+'&cbo_basis='+cbo_basis+'&action=booking_search_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var hidden_booking_id=this.contentDoc.getElementById("hidden_booking_id").value; //Access form field with id="emailfield"
			var hidden_booking_no=this.contentDoc.getElementById("hidden_booking_no").value; //Access form field with id="emailfield"
			var hide_buyer=this.contentDoc.getElementById("hide_buyer").value; //Access form field with id="emailfield"
			
			reset_form('','list_fabric_desc_container','txt_booking_id*txt_booking_no*txt_buyer_order*all_po_id*cbo_buyer_name*txt_issue_qnty*txt_item_description*hidden_prod_id*cbo_item_group*txt_item_color*txt_brand_supref*txt_item_size*txt_shelf','','','cbo_company_id*cbo_location*cbo_store_name*is_posted_account*store_update_upto');
			$("#tbl_display_info").find('input,select').val('');
			
			$('#txt_booking_id').val(hidden_booking_id);
			$('#txt_booking_no').val(hidden_booking_no);
			$('#cbo_buyer_name').val(hide_buyer);

			if(cbo_basis==2)
			{
				show_list_view(hidden_booking_id,'create_itemDesc_search_list_view_on_booking','list_fabric_desc_container','requires/trims_issue_entry_controller','setFilterGrid(\'tbl_list_search\',-1);');
			}
			else
			{
				show_list_view(hidden_booking_id,'create_itemDesc_search_list_view_on_requisition','list_fabric_desc_container','requires/trims_issue_entry_controller','setFilterGrid(\'tbl_list_search\',-1);');
			}
			
		}
	}
	
	function openmypage_issueQty()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_basis = $('#cbo_basis').val();
		var save_data = $('#save_data').val();
		var all_po_id = $('#selected_po_id').val();
		var issueQnty = $('#txt_issue_qnty').val();
		var prod_id = $('#hidden_prod_id').val();
		var cbo_store_name = $('#cbo_store_name').val();
		var cbo_floor = $('#cbo_floor').val();
		var cbo_room = $('#cbo_room').val();
		var txt_rack = $('#txt_rack').val();
		var txt_shelf = $('#txt_shelf').val();
		var cbo_bin = $('#cbo_bin').val();
		var distribution_method = $('#distribution_method_id').val();
		var conversion_factor = $('#txt_conversion_faction').val()*1;
		var txt_yet_to_issue = $('#txt_yet_to_issue').val()*1;
		var txt_booking_id = $('#txt_booking_id').val()*1;
		var update_id = $('#update_id').val()*1;
		
		if(cbo_basis==2)
		{
			if (form_validation('cbo_company_id*cbo_store_name*txt_item_description','Company*Store Name*Item Description')==false)
			{
				return;
			}
		}
		else
		{
			if (form_validation('cbo_company_id*cbo_store_name*txt_buyer_order*txt_item_description','Company*Store Name*Buyer Order*Item Description')==false)
			{
				return;
			}
		}
		
		
			
		var title = 'PO Info';	
		var page_link = 'requires/trims_issue_entry_controller.php?cbo_company_id='+cbo_company_id+'&save_data='+save_data+'&all_po_id='+all_po_id+'&issueQnty='+issueQnty+'&prev_method='+distribution_method+'&prod_id='+prod_id+'&conversion_factor='+conversion_factor+'&cbo_store_name='+cbo_store_name+'&cbo_floor='+cbo_floor+'&cbo_room='+cbo_room+'&txt_rack='+txt_rack+'&txt_shelf='+txt_shelf+'&cbo_bin='+cbo_bin+'&cbo_basis='+cbo_basis+'&txt_yet_to_issue='+txt_yet_to_issue+'&txt_booking_id='+txt_booking_id+'&update_id='+update_id+'&action=po_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=420px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var save_string=this.contentDoc.getElementById("save_string").value;	 //Access form field with id="emailfield"
			var tot_trims_qnty=this.contentDoc.getElementById("tot_trims_qnty").value; //Access form field with id="emailfield"
			var total_garmrnts_qty=this.contentDoc.getElementById("total_garmrnts_qty").value; 
			var all_po_id=this.contentDoc.getElementById("all_po_id").value; //Access form field with id="emailfield"
			var all_po_no=this.contentDoc.getElementById("all_po_no").value; //Access form field with id="emailfield"
			var distribution_method=this.contentDoc.getElementById("distribution_method").value; //Access form field with id="emailfield"
			// alert(total_garmrnts_qty);
			$('#save_data').val(save_string);
			$('#txt_issue_qnty').val(tot_trims_qnty);
			$('#txt_garments_qty').val(total_garmrnts_qty);
			$('#selected_po_id').val(all_po_id);
			$('#txt_buyer_order').val(all_po_no);
			$('#distribution_method_id').val(distribution_method);
			$('#cbo_store_name').attr('disabled',true);
			
			if(all_po_id!="")
			{
				//get_php_form_data(all_po_id+"**"+$('#hidden_prod_id').val(), 'get_trim_cum_info', 'requires/trims_issue_entry_controller' );
			}
		}
	}
	
	function generate_report_file(data,action,page)
	{
		window.open("requires/trims_issue_entry_controller.php?data=" + data+'&action='+action, true );
	}
		
	function fnc_trims_issue(operation)
	{
		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][25]); ?>') 
		{
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][25]); ?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][25]); ?>')==false) {return;}
		}
		if(operation==4)
		{
			if( form_validation('txt_system_id','Issue No')==false ){
				return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val()+'*'+report_title+'*'+$('#txt_received_qnty').val()+'*'+$('#txt_cumulative_issued').val()+'*'+$('#txt_yet_to_issue').val(),'trims_issue_entry_print');

			// print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "trims_issue_entry_print", "requires/trims_issue_entry_controller" ) 
			return;
		}
		else if(operation==5)
		{
			// alert(operation);
			if( form_validation('txt_system_id','Issue No')==false ){
				return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val()+'*'+report_title,'trims_issue_entry_print2');
			return;
		}
		else if(operation==6)
		{
			if( form_validation('txt_system_id','Issue No')==false ){
				return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val()+'*'+report_title,'trims_issue_entry_print3');
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
			
			var issue_purpose =$("#cbo_issue_purpose").val();
			if(issue_purpose==55){
				if( form_validation('cbo_company_id*txt_issue_date*cbo_issue_purpose*cbo_location*cbo_basis*cbo_store_name','Company*Issue Date*Issue Purpose*Location*Issue Basis*Store Name')==false ){
					return;
				}
			}else{
				if( form_validation('cbo_company_id*txt_issue_date*cbo_issue_purpose*cbo_location*cbo_basis*cbo_store_name*cbo_sewing_source*cbo_sewing_company*cbo_location','Company*Issue Date*Issue Purpose*Location*Issue Basis*Store Name*Sewing Source*Sewing Company*Location')==false ){
					return;
				}
			}
			
			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_issue_date').val(), current_date)==false)
			{
				alert("Issue Date Can not Be Greater Than Current Date");
				return;
			}	
			
			var issueBasis =$("#cbo_basis").val();
			if(issueBasis==1)
			{
				if( form_validation('txt_buyer_order','Buyer Order')==false )
				{
					return;
				}
			}
			else if(issueBasis==2)
			{
				if( form_validation('txt_booking_no',' Booking No')==false )
				{
					return;
				}
			}
			
			//
			
			var sewing_source =$("#cbo_sewing_source").val();
			if(sewing_source==1)
			{
				if( form_validation('txt_item_description*txt_issue_qnty*cbo_location_swing','Item Description*Issue Qnty*Sweing Location')==false )
				{
					return;
				}
			}
			else
			{
				if( form_validation('txt_item_description*txt_issue_qnty','Item Description*Issue Qnty')==false )
				{
					return;
				}
			}
			
			
			if(($("#txt_issue_qnty").val()*1 > $("#txt_yet_to_issue").val()*1+$("#hidden_issue_qnty").val()*1)) 
			{
				alert("Issue Quantity Excededs Receive Quantity.");
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

			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_system_id*cbo_company_id*txt_issue_date*txt_issue_chal_no*cbo_basis*txt_booking_no*txt_booking_id*cbo_store_name*cbo_sewing_source*cbo_sewing_company*cbo_location*cbo_location_swing*txt_attention*txt_remarks*cbo_item_group*cbo_uom*txt_item_description*hidden_prod_id*txt_item_color_id*txt_item_size*gmts_color_id*gmts_size_id*txt_brand_supref*cbo_location*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*txt_buyer_order*txt_issue_qnty*txt_garments_qty*hidden_issue_qnty*update_id*all_po_id*update_dtls_id*update_trans_id*save_data*previous_prod_id*cbo_issue_purpose*cbo_floor_swing*cbo_sewing_line*txt_cons_rate*selected_po_id*store_update_upto',"../../");
			
			freeze_window(operation);
			
			http.open("POST","requires/trims_issue_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange =fnc_trims_issue_Reply_info;
		}
	}
	
	function fnc_trims_issue_Reply_info()
	{
		if(http.readyState == 4) 
		{
			 //alert(http.responseText);
			//release_freezing();	return;
			var reponse=trim(http.responseText).split('**');	
			if (reponse[0] * 1 == 20 * 1) 
		   {
				alert(reponse[1]);
				release_freezing();
				return;
			} 	
                        
			show_msg(reponse[0]);
			
			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_system_id').value = reponse[2];
				$('#cbo_company_id').attr('disabled','disabled');
				$('#cbo_location').attr('disabled','disabled');
				$('#cbo_store_name').attr('disabled','disabled');

				reset_form('trimsissue_1','','','','','update_id*txt_system_id*cbo_company_id*txt_issue_date*txt_issue_chal_no*cbo_store_name*cbo_sewing_source*cbo_sewing_company*cbo_location_swing*all_po_id*selected_po_id*txt_buyer_order*cbo_buyer_name*cbo_basis*txt_booking_no*txt_booking_id*txt_attention*txt_remarks*cbo_issue_purpose*cbo_floor_swing*cbo_sewing_line*store_update_upto*cbo_location*is_posted_account*store_update_upto');
				
				
				//$('#tbl_dtls').find('input,select').not( "#cbo_floor_swing").not( "#cbo_sewing_line").not( ".formbutton").val("");
				var store_update_upto=$('#store_update_upto').val();
				
				show_list_view(reponse[1]+'**'+store_update_upto,'show_trims_listview','div_details_list_view','requires/trims_issue_entry_controller','');
				//show_list_view(reponse[4]+'****'+reponse[5],'create_itemDesc_search_list_view','list_fabric_desc_container','requires/trims_issue_entry_controller','setFilterGrid(\'tbl_list_search\',-1);');
				set_button_status(0, permission, 'fnc_trims_issue',1,1);
			}
			else if(reponse[0]==17)
			{
				alert(reponse[1]);
			}
			else if(reponse[0]==11)
			{
				alert(reponse[1]);
			}
			
			release_freezing();	
		}
	}
	
	function openmypage_systemId()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var page_link='requires/trims_issue_entry_controller.php?cbo_company_id='+cbo_company_id+'&action=trims_issue_popup_search';
			var title='Trims Issue Form';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=820px,height=390px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var trims_issue_id=this.contentDoc.getElementById("hidden_issue_id").value;

				if(trims_issue_id!="")
				{
					freeze_window(5);
					reset_form('trimsissue_1','div_details_list_view*list_fabric_desc_container','','','','cbo_company_id*is_posted_account*store_update_upto');
					
					var posted_in_account=this.contentDoc.getElementById("hidden_posted_in_account").value; // is posted accounct
					$("#is_posted_account").val(posted_in_account);
					if(posted_in_account==1) document.getElementById("accounting_posted_status").innerHTML="Already Posted In Accounting.";
					else 					 document.getElementById("accounting_posted_status").innerHTML="";
					
					get_php_form_data(trims_issue_id, "populate_data_from_trims_issue", "requires/trims_issue_entry_controller" );
					$('#cbo_store_name').attr('disabled',true);
					var store_update_upto=$('#store_update_upto').val();
					show_list_view(trims_issue_id+'**'+store_update_upto,'show_trims_listview','div_details_list_view','requires/trims_issue_entry_controller','');
					set_button_status(0, permission, 'fnc_trims_issue',1,1);
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
		var ile_cost 		= $("#txt_ile").val();
		var amount 			= quantity*1*(rate*1+ile_cost*1); 
		var bookCurrency 	= (rate*1+ile_cost*1)*exchangeRate*1*quantity*1;
		$("#txt_amount").val(number_format_common(amount,"","",currency_id));
		$("#txt_book_currency").val(number_format_common(bookCurrency,"","",1));
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
			var page_link='requires/trims_issue_entry_controller.php?update_dtls_id='+update_dtls_id+'&action=goods_placement_popup';
			var title='Goods Placement Entry Form';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../');
			
		}
	}
	
	function set_form_data(data)
	{
		reset_form('trimsissue_1','','','','','update_id*txt_system_id*cbo_company_id*txt_issue_date*txt_issue_chal_no*cbo_store_name*cbo_sewing_source*cbo_sewing_company*cbo_location_swing*all_po_id*selected_po_id*txt_buyer_order*cbo_buyer_name*cbo_basis*txt_booking_no*txt_booking_id*txt_attention*txt_remarks*cbo_issue_purpose*cbo_floor_swing*cbo_sewing_line*cbo_location*is_posted_account*store_update_upto');
		var data=data.split("**");
		$('#hidden_prod_id').val(data[0]);
		$('#cbo_item_group').val(data[1]);
		$('#txt_item_description').val(data[2]);
		$('#txt_item_color').val(data[3]);
		$('#gmts_color_id').val(data[4]);
		$('#txt_item_size').val(data[5]);
		$('#gmts_size_id').val(data[6]);
		$('#txt_brand_supref').val(data[7]);
		$('#cbo_uom').val(data[8]);
		$('#txt_conversion_faction').val(data[16]);		
		$('#txt_item_color_id').val(data[11]);
		$('#txt_global_stock').val(data[12]);
		$('#txt_cumulative_issued').val(data[13]);
		$('#txt_yet_to_issue').val(data[14]);
		$('#txt_received_qnty').val(data[15]);
		$('#txt_buyer_order').val(data[17]);
		$('#selected_po_id').val(data[18]);
		$('#txt_cons_rate').val(data[19]);

		var issueBasis =$("#cbo_basis").val();
		
		if ( issueBasis != 3)
		{
			$('#cbo_floor').val(data[20]).attr('disabled','disabled');
			$('#cbo_room').val(data[21]).attr('disabled','disabled');
			$('#txt_rack').val(data[9]).attr('disabled','disabled');
			$('#txt_shelf').val(data[10]).attr('disabled','disabled');
			$('#cbo_bin').val(data[22]).attr('disabled','disabled');
		}
		$('#cbo_location').attr('disabled','disabled');
		$('#cbo_store_name').attr('disabled','disabled');
		//alert(data[9]+'='+data[10]+'='+data[20]+'='+data[21]+'='+data[22]);

		var txt_booking_id = $('#txt_booking_id').val();
		var prod_id = $('#hidden_prod_id').val();
		var store_id = $('#cbo_store_name').val();
		var all_po_id = $('#selected_po_id').val();
		var all_po_no = $('#txt_buyer_order').val();

		

		if(issueBasis==2 && txt_booking_id!="")
		{
			get_php_form_data(txt_booking_id+"**"+$('#hidden_prod_id').val(), 'get_trim_cum_info_for_trims_booking', 'requires/trims_issue_entry_controller' );
		}
		if(issueBasis==3)
		{
			$('#all_po_id').val(data[18]);
		}

		if(issueBasis==3 && txt_booking_id!="")
		{
			get_php_form_data(txt_booking_id+"**"+prod_id+"**"+store_id+"**"+all_po_id+"**"+all_po_no, 'get_trim_trans_floor_room_rack', 'requires/trims_issue_entry_controller' );
		}
		
		if(issueBasis!=2) 
		{
			openmypage_issueQty();
		}
		
		set_button_status(0, permission, 'fnc_trims_issue',1,1);
	}

	function get_trim_cum_stock(ref_id,ref_type) 
	{
		var prod_id = $('#hidden_prod_id').val();
		var all_po_id = $('#all_po_id').val();
		var store_id = $('#cbo_store_name').val();
		if(ref_type==1)
		{
			var room_rack_data=ref_id;
		}
		if(ref_type==2)
		{
			var room_rack_data= $('#cbo_floor').val()+"**"+ref_id;
		}
		if(ref_type==3)
		{
			var room_rack_data=$('#cbo_floor').val()+"**"+$('#cbo_room').val()+"**"+ref_id;
		}
		if(ref_type==4)
		{
			var room_rack_data=$('#cbo_floor').val()+"**"+$('#cbo_room').val()+"**"+$('#txt_rack').val()+"**"+ref_id;
		}
		if(ref_type==5)
		{
			var room_rack_data=$('#cbo_floor').val()+"**"+$('#cbo_room').val()+"**"+$('#txt_rack').val()+"**"+$('#txt_shelf').val()+"**"+ref_id;
		}
		// alert(prod_id+'='+all_po_id+'='+store_id+'='+room_rack_data+'='+ref_id+'='+ref_type);
		
		get_php_form_data(prod_id+"**"+all_po_id+"**"+store_id+"**"+room_rack_data, 'get_trim_store_wise_stock_for_requisition', 'requires/trims_issue_entry_controller' );
	}
	
	function enable_disable()
	{
		var issueBasis =$("#cbo_basis").val();
		reset_form('','list_fabric_desc_container','txt_booking_id*txt_booking_no*txt_buyer_order*all_po_id*cbo_buyer_name*txt_issue_qnty*txt_item_description*hidden_prod_id','','','');
		$("#tbl_display_info").find('input,select').val('');
		
		$('#txt_issue_qnty').attr('readonly','readonly');
		$('#txt_issue_qnty').attr('onClick','openmypage_issueQty();');	
		$('#txt_issue_qnty').attr('placeholder','Single Click');
		
		if(issueBasis==2)
		{
			$("#txt_buyer_order").attr("disabled",true);
			$('#txt_issue_qnty').removeAttr('readonly','readonly');
			$('#txt_issue_qnty').removeAttr('onClick','onClick');
			$('#txt_issue_qnty').attr('placeholder','Write');
			$("#txt_booking_no").removeAttr("disabled");			
		}
		else if(issueBasis==3)
		{
			$("#txt_booking_no").removeAttr("disabled");
			$("#txt_buyer_order").attr("disabled",true);	
			$('#txt_issue_qnty').attr('readonly','readonly');
			$('#txt_issue_qnty').attr('placeholder','Click');
		}
		else
		{	
			$("#txt_booking_no").attr("disabled",true);
			$("#txt_buyer_order").attr("disabled",false);
			$('#txt_issue_qnty').attr('readonly','readonly');
		}
	}
	
	function load_location(sewing_company)
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_sewing_source = $('#cbo_sewing_source').val();
		var cbo_sewing_company = $('#cbo_sewing_company').val();
		//	alert(cbo_sewing_source);
		if(cbo_sewing_source==1)
		{
			load_drop_down( 'requires/trims_issue_entry_controller',cbo_sewing_company, 'load_drop_down_location_by_swing', 'swing_location_td' );
		}
		else
		{
			//load_drop_down( 'requires/trims_issue_entry_controller',cbo_company_id, 'load_drop_down_location_by_swing', 'swing_location_td' );
			$("#swing_location_td").html('<select name="cbo_location_swing" id="cbo_location_swing" class="combo_boxes " style="width:132px" onchange="">\n<option data-attr="" value="0">-- Select Location --</option>\n</select>');
		}
	}

	function chk_purpose_condition(val)
	{
		if(val==55){
			$('#cbo_sewing_source').val(0);
			$('#cbo_sewing_company').val(0);
			$('#cbo_location_swing').val(0);
			$("#cbo_sewing_source").attr("disabled",true);
			$("#cbo_sewing_company").attr("disabled",true);
			$("#cbo_location_swing").attr("disabled",true);
			$('#sewing_source_td font').css({color:'black'});
			$('#sewing_com_td font').css({color:'black'});
			load_drop_down( 'requires/trims_issue_entry_controller', document.getElementById('cbo_location_swing').value+'_'+document.getElementById('cbo_sewing_company').value+'_'+document.getElementById('cbo_sewing_source').value+'_'+val, 'load_drop_down_floor', 'swing_floor_td')
		}else{
			$("#cbo_sewing_source").attr("disabled",false);
			$("#cbo_sewing_company").attr("disabled",false);
			$('#sewing_source_td font').css({color:'blue'});
			$('#sewing_com_td font').css({color:'blue'});
			//$('#sewing_com_td').css('color','blue');
		}
	}

	function company_on_change(company)
	{
		var com_all_data = return_global_ajax_value(company, 'com_wise_all_data', '', 'requires/trims_issue_entry_controller');
		//alert(com_all_data);
		var com_all_data_arr=com_all_data.split("**");
		$('#store_update_upto').val(com_all_data_arr[0]);
		
		var JSONObject_location = JSON.parse(com_all_data_arr[1]);
		$('#cbo_location').html('<option value="'+0+'">Select</option>');
		for (var key of Object.keys(JSONObject_location).sort())
		{
			$('#cbo_location').append('<option value="'+key+'">'+JSONObject_location[key]+'</option>');
		}
		
		var JSONObject_print_report = JSON.parse(com_all_data_arr[2]);
		$('#Print4').hide();
		$('#Print2').hide();
		$('#Print3').hide();
		for (var key of Object.keys(JSONObject_print_report).sort())
		{
			if(JSONObject_print_report[key]==86){$('#Print4').show();}
			if(JSONObject_print_report[key]==116){$('#Print2').show();}
			if(JSONObject_print_report[key]==136){$('#Print3').show();}
		}
	}
	
	function floor_room_rack(store_id)
	{
		var company_id=$("#cbo_company_id").val();
		var store_all_data = return_global_ajax_value(store_id+'_'+company_id, 'stoe_wise_all_data', '', 'requires/trims_issue_entry_controller');
		var store_all_data_arr=store_all_data.split("**");
		
		var JSONObject_floor = JSON.parse(store_all_data_arr[0]);
		$('#cbo_floor').html('<option value="'+0+'">Select</option>');
		for (var key of Object.keys(JSONObject_floor).sort())
		{
			$('#cbo_floor').append('<option value="'+key+'">'+JSONObject_floor[key]+'</option>');
		}
		
		var JSONObject_room = JSON.parse(store_all_data_arr[1]);
		$('#cbo_room').html('<option value="'+0+'">Select</option>');
		for (var key of Object.keys(JSONObject_room).sort())
		{
			$('#cbo_room').append('<option value="'+key+'">'+JSONObject_room[key]+'</option>');
		}
		
		var JSONObject_rack = JSON.parse(store_all_data_arr[2]);
		$('#txt_rack').html('<option value="'+0+'">Select</option>');
		for (var key of Object.keys(JSONObject_rack).sort())
		{
			$('#txt_rack').append('<option value="'+key+'">'+JSONObject_rack[key]+'</option>');
		}
		
		var JSONObject_self = JSON.parse(store_all_data_arr[3]);
		$('#txt_shelf').html('<option value="'+0+'">Select</option>');
		for (var key of Object.keys(JSONObject_self).sort())
		{
			$('#txt_shelf').append('<option value="'+key+'">'+JSONObject_self[key]+'</option>');
		}
		
		var JSONObject_bin = JSON.parse(store_all_data_arr[4]);
		$('#cbo_bin').html('<option value="'+0+'">Select</option>');
		for (var key of Object.keys(JSONObject_bin).sort())
		{
			$('#cbo_bin').append('<option value="'+key+'">'+JSONObject_bin[key]+'</option>');
		}
		
		// var unRefreshId = "cbo_company_id*cbo_location*cbo_store_name*txt_delivery_date*store_update_upto";
		//load_drop_down('requires/trims_issue_entry_controller', this.value+'_'+$data[1], 'load_drop_floor','floor_td');load_drop_down('requires/trims_issue_entry_controller', this.value+'_'+$data[1], 'load_drop_room','room_td');load_drop_down('requires/trims_issue_entry_controller', this.value+'_'+$data[1], 'load_drop_rack','rack_td');load_drop_down('requires/trims_issue_entry_controller', this.value+'_'+$data[1], 'load_drop_shelf','shelf_td');load_drop_down('requires/trims_issue_entry_controller', this.value+'_'+$data[1], 'load_drop_bin','bin_td');
	}
	

	function reset_on_change(id)
	{
		
		if(id =="cbo_store_name")
		{
			
		}
		else if(id =="cbo_location")
		{
			// var unRefreshId = "cbo_company_id*cbo_location*txt_delivery_date*store_update_upto";
			$('#cbo_floor').html('<option value="'+0+'">Select</option>');
			$('#cbo_room').html('<option value="'+0+'">Select</option>');
			$('#txt_rack').html('<option value="'+0+'">Select</option>');
			$('#txt_shelf').html('<option value="'+0+'">Select</option>');
			$('#cbo_bin').html('<option value="'+0+'">Select</option>');
		}
		else if(id =="cbo_company_id")
		{
			// var unRefreshId = "cbo_company_id*txt_delivery_date*store_update_upto";
			$('#cbo_store_name').html('<option value="'+0+'">Select</option>');
			$('#cbo_floor').html('<option value="'+0+'">Select</option>');
			$('#cbo_room').html('<option value="'+0+'">Select</option>');
			$('#txt_rack').html('<option value="'+0+'">Select</option>');
			$('#txt_shelf').html('<option value="'+0+'">Select</option>');
			$('#cbo_bin').html('<option value="'+0+'">Select</option>');
		}
		// reset_form('finishFabricEntry_1', 'list_container_finishing*roll_details_list_view*list_fabric_desc_container', '', '', '', unRefreshId);
	}
	
	
</script>
<body onLoad="set_hotkey()" oncontextmenu="return false;">
<div style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission); ?><br />    		 
    <form name="trimsissue_1" id="trimsissue_1" autocomplete="off" >
    <div style="width:680px;float:left; margin-left:10px">   
        <fieldset style="width:680px;">
        <legend>Trims Issue Entry</legend>
        <br>
        	<fieldset style="width:680px;">
                <table width="678" cellspacing="2" cellpadding="0" border="0" id="tbl_master" align="center">
                    <tr>
                        <td colspan="3" align="right"><strong>Issue No</strong></td>
                        <td colspan="3" align="left">
                        	 <input type="hidden" name="update_id" id="update_id" />
                            <input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemId();" readonly />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="95" class="must_entry_caption"> Company </td>
                        <td width="150">
                            <? 
                                echo create_drop_down( "cbo_company_id", 132, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond $company_credential_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "reset_on_change(this.id);company_on_change(this.value);" );
                                //load_drop_down( 'requires/trims_issue_entry_controller', this.value, 'load_drop_down_location_by_swing', 'swing_location_td' );
                                //load_room_rack_self_bin('requires/trims_issue_entry_controller*4', 'store','store_td', this.value);
                            ?>
                        </td>
                        <td width="98" class="must_entry_caption"> Issue Date </td>
                        <td width="143">
                            <input class="datepicker" type="text" style="width:120px" name="txt_issue_date" id="txt_issue_date" value="<? echo date("d-m-Y"); ?>"/>
                        </td>
                        <td width="90" class="must_entry_caption">Issue Purpose</td>
                        <td>
                        	<? 
                                echo create_drop_down( "cbo_issue_purpose", 132, $yarn_issue_purpose,"", 1, "-- Select --", 36, "load_drop_down( 'requires/trims_issue_entry_controller', document.getElementById('cbo_location_swing').value+'_'+document.getElementById('cbo_sewing_company').value+'_'+document.getElementById('cbo_sewing_source').value+'_'+this.value, 'load_drop_down_floor', 'swing_floor_td');chk_purpose_condition(this.value)","","3,4,8,26,29,30,36,37,41,42,31,77,83" );
                            ?>
                        </td>
                    </tr> 
                    <tr>
                       <td class="must_entry_caption">Location</td>                                              
                        <td id="location_td">
                            <? //load_drop_down( 'requires/trims_issue_entry_controller', this.value, 'load_drop_down_floor', 'swing_floor_td' );
                                echo create_drop_down( "cbo_location", 132, $blank_array,"", 1, "-- Select Location --", 0, "reset_on_change(this.id);load_drop_down('requires/trims_issue_entry_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_store','store_td');" );
                            ?>
                        </td>
                        <td class="must_entry_caption">Issue Basis</td>
                        <td>
                            <? 
                                $trims_issue_basis=array(1=>"With Order",2=>"Without Order",3=>"Requisition");
                                echo create_drop_down( "cbo_basis", 132, $trims_issue_basis,"",0, "-- Select Basis --", $selected, "enable_disable();", "", "");
                            ?>
                        </td>
                        <td>Booking / Req. No</td>
                        <td>
                            <input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:120px"  placeholder="Double Click to Search" onDblClick="openpage_booking();" readonly disabled />
                            <input type="hidden" name="txt_booking_id" id="txt_booking_id" />
                        </td>
                    </tr>
                    <tr>

                    	<td width="100">Issue Challan No </td>
                        <td>
                            <input type="text" name="txt_issue_chal_no" id="txt_issue_chal_no" class="text_boxes" style="width:120px" >
                        </td>
                       
                        
                        <td class="must_entry_caption" id="sewing_source_td">Sewing Source </td>
                        <td>
                            <?
                                echo create_drop_down("cbo_sewing_source", 132, $knitting_source,"", 1, "-- Select --", 0,"load_drop_down( 'requires/trims_issue_entry_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_sewing_com','sewing_com');",0,'1,3');
                            ?>
                        </td>
                        <td class="must_entry_caption" id="sewing_com_td">Sewing Comp.</td>
                        <td id="sewing_com">
                            <?
                                echo create_drop_down( "cbo_sewing_company", 132, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Sewing Company--", "", "load_location(this.value)","" );
                                //echo create_drop_down( "cbo_sewing_company", 132, $blank_array,"",1, "--Select Sewing Company--", 1, "" );
                            ?>
                        </td>
                    </tr>
                    <tr>

                    	<td class="must_entry_caption">Store Name </td>
                        <td id="store_td">
                            <?
                                echo create_drop_down( "cbo_store_name", 132, $blank_array,"",1, "--Select store--", 1, "" );
                            ?>
                        </td> 
                        <td>Sewing Location</td>                                              
                        <td id="swing_location_td">
                            <? //load_drop_down( 'requires/trims_issue_entry_controller', this.value, 'load_drop_down_floor', 'swing_floor_td' );
                                echo create_drop_down( "cbo_location_swing", 132, $blank_array,"", 1, "-- Select Location --", 0, "" );
                            ?>
                        </td>
						<td>Attention</td>                  
                        <td >
							<input type="text" name="txt_attention" id="txt_attention" class="text_boxes" style="width:120px" >
                        </td>
                    </tr>
                    <tr>
						<td>Remarks</td>
                        <td colspan="5"><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:355px" ></td>
                    </tr>
                </table>
            </fieldset>
            <br>
            <table width="680" cellspacing="2" cellpadding="2" border="0" id="tbl_dtls" align="center">
                <tr>
                    <td width="65%" valign="top">
                        <fieldset>
                        <legend>New Entry</legend>
                            <table cellpadding="0" cellspacing="2" width="100%">
                                <tr>
                                    <td class="must_entry_caption" width="90">Buyer Order</td>
                                    <td>
                                        <input type="text" name="txt_buyer_order" id="txt_buyer_order" class="text_boxes" style="width:135px;" onDblClick="openmypage_po()" placeholder="Double click to search" readonly/>	
                                    </td>
                                    <td width="90">UOM</td>
                                    <td>
                                        <?
                                            echo create_drop_down( "cbo_uom", 100, $unit_of_measurement,"", 1, "-- UOM --", '0', "",1 );
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Item Group</td>
                                    <td>
                                    <?
                                        echo create_drop_down( "cbo_item_group", 146, "select id,item_name from lib_item_group where item_category=4 and status_active=1 and is_deleted=0 order by item_name", "id,item_name", 1, "-- Select --", 0,  "",1 );
                                    ?>	
                                    </td>
                                    <td>Brand/Sup Ref</td>
                                    <td>
                                        <input type="text" name="txt_brand_supref" id="txt_brand_supref" class="text_boxes" style="width:90px" disabled/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="must_entry_caption">Item Desc.</td>
                                    <td>
                                        <input type="text" name="txt_item_description" id="txt_item_description" class="text_boxes" style="width:135px" disabled readonly />
                                        <input type="hidden" name="hidden_prod_id" id="hidden_prod_id" disabled/>
                                    </td>
                                    <td><!--Gmts-->Item Size</td>   
                                    <td>
                                        <input type="text" name="txt_item_size" id="txt_item_size" class="text_boxes" style="width:90px;" disabled/>
                                        
                                    </td>
                              </tr> 
                                <tr>
                                    <td><!--Gmts--> Item Color</td>
                                    <td>
                                        <input type="text" name="txt_item_color" id="txt_item_color" class="text_boxes" style="width:135px;" disabled/>
                                        <input type="hidden" name="txt_item_color_id" id="txt_item_color_id" readonly>
                                    </td>
                                    <td class="must_entry_caption">Issue Qnty</td>
                                    <td>
                                        <input type="text" name="txt_issue_qnty" id="txt_issue_qnty" class="text_boxes_numeric" style="width:90px;" onClick="openmypage_issueQty()"placeholder="Single Click" readonly/>
										<input type="hidden" name="txt_garments_qty" id="txt_garments_qty">	
                                    </td>
                                </tr>
                                <tr>
                                	<td>Floor</td>
									<td id="floor_td">
										<? echo create_drop_down( "cbo_floor", 132,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                   
                                    <td>Sewing Floor</td>
                                     <td id="swing_floor_td">
                                     <?php
									 //get_php_form_data(document.getElementById('cbo_source').value,'line_disable_enable','requires/trims_issue_entry_controller'); load_drop_down( 'requires/trims_issue_entry_controller', this.value+'_'+document.getElementById('cbo_location').value'load_drop_down_sewing_line_floor', 'sewing_line_td' );
											//echo create_drop_down( "cbo_floor_swing", 146, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0  order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );
											echo create_drop_down( "cbo_floor_swing", 100, $blank_array,"", 1, "-- Select Floor --", 0, "" ); 
										?>
                                     </td>
                                   
                                </tr>                               
                                <tr>
                                    <td>Room</td>
									<td id="room_td">
										<? echo create_drop_down( "cbo_room", 132,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                     
                                    <td>Sewing Line No</td> 
                                    <td id="sewing_line_td" colspan="2">            
                                        <?
                                            echo create_drop_down( "cbo_sewing_line", 100, $blank_array,"", 1, "Select Line", $selected, "",1,0 );		
                                        ?>	
                                    </td> 
                                </tr>
                                <tr>
                                	<td>Rack</td>
									<td id="rack_td">
										<? echo create_drop_down( "txt_rack", 132,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
									<td>Garments color</td>
									<td>
                                    <input type="text" name="txt_gmts_color" id="txt_gmts_color" class="text_boxes" style="width:90px;" disabled/>
                                    <input type="hidden" name="gmts_color_id" id="gmts_color_id" class="text_boxes" disabled/>
                                    </td>
                                </tr>
                                <tr>
                                	<td>Shelf</td>
									<td id="shelf_td">
										<? echo create_drop_down( "txt_shelf", 132,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
									<td>garments Size</td>
									<td>
                                    <input type="text" name="txt_gmts_size" id="txt_gmts_size" class="text_boxes" style="width:90px;" disabled/>
                                    <input type="hidden" name="gmts_size_id" id="gmts_size_id" class="text_boxes" disabled/>
                                    </td>	
                                </tr>
                                <tr>
                                	<td>Bin/Box</td>
									<td id="bin_td">
										<? echo create_drop_down( "cbo_bin", 132,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
									<td></td>
									<td></td>
                                </tr>
                                <tr>
                                	<td align="right" colspan="3" style="display:none"><input type="button" class="formbuttonplasminus" style="width:150px" value="Goods Placement" onClick="openmypage_goodsPlacement();"></td> 
                                </tr> 
                             </table>
                        </fieldset>
					</td>
                    <td width="2%" valign="top"></td>
					<td width="33%" valign="top">
						<fieldset>
                        <legend>Display</legend>					
                            <table id="tbl_display_info"  cellpadding="0" cellspacing="1" width="100%" >				
                                <tr>
                                    <td>Recv. Qty</td>						
                                	<td>
                                    	<input type="text" name="txt_received_qnty" id="txt_received_qnty" class="text_boxes_numeric" placeholder="Display" style="width:90px" disabled />
                                    </td>
								</tr>
                                <tr>
                                    <td>Cumul. Issued</td>
                                    <td><input type="text" name="txt_cumulative_issued" id="txt_cumulative_issued" class="text_boxes_numeric" placeholder="Display" style="width:90px" disabled /></td>
                                </tr>
                                <tr>
                                    <td>Yet to Issue</td>
                                    <td><input type="text" name="txt_yet_to_issue" id="txt_yet_to_issue" class="text_boxes_numeric" placeholder="Display" style="width:90px" disabled /></td>
                                </tr>					
                               	<tr>
                                    <td>Buyer</td>						
                                    <td>
                                    <?
										echo create_drop_down( "cbo_buyer_name", 101, "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, " Display ", 0, "",1 );
									?>
                                    </td>
                                </tr>	
                                <tr>
                                    <td>Global Stock</td>
                                    <td><input type="text" name="txt_global_stock" id="txt_global_stock" placeholder="Display" class="text_boxes_numeric" style="width:90px" disabled /></td>
                                </tr>							
                            </table>                  
                       </fieldset>	
              		</td>
				</tr>
                <tr>
                    <td align="center" colspan="3" class="button_container" width="100%">
                        <?
                            echo load_submit_buttons($permission, "fnc_trims_issue", 0,0,"reset_form('trimsissue_1','div_details_list_view*list_fabric_desc_container','','','disable_enable_fields(\'cbo_company_id\');','cbo_company_id*cbo_location*cbo_store_name*txt_issue_date*cbo_issue_purpose*cbo_basis*cbo_sewing_source*cbo_sewing_company*cbo_location_swing*is_posted_account*store_update_upto')",1);
                        ?>
						<span id="button_data_panel"></span>
                        <input type="hidden" name="save_data" id="save_data" readonly>
                        <input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
                        <input type="hidden" name="update_trans_id" id="update_trans_id" readonly>
                        <input type="hidden" name="previous_prod_id" id="previous_prod_id" readonly>
                        <input type="hidden" name="all_po_id" id="all_po_id" readonly>
                        <input type="hidden" name="selected_po_id" id="selected_po_id" readonly>
                        <input type="hidden" name="hidden_issue_qnty" id="hidden_issue_qnty" readonly>
                        <input type="hidden" name="distribution_method_id" id="distribution_method_id" readonly />
                        <input type="hidden" name="txt_conversion_faction" id="txt_conversion_faction" />
                        <input type="hidden" name="txt_cons_rate" id="txt_cons_rate" />
                        <input type="hidden" id="is_posted_account" name="is_posted_account" value=""/>
                        <input type="hidden" name="store_update_upto" id="store_update_upto">
                        
                        <input id="Print4" type="button" class="formbutton" style="width:80px; display:none" onClick="fnc_trims_issue(4)" name="print" value="Print">
                        <input id="Print2" type="button" class="formbutton" style="width:80px; display:none" onClick="fnc_trims_issue(5)" name="print" value="Print 2">
                        <input id="Print3" type="button" class="formbutton" style="width:80px; display:none" onClick="fnc_trims_issue(6)" name="print" value="Print 3">
                    </td>
                </tr>
                <tr>
                    <td colspan="6" align="center">
                        <div id="accounting_posted_status" style=" color:red; font-size:24px;";  ></div>
                    </td>
                </tr>
			</table>
            <div style="width:680px;" id="div_details_list_view"></div>
		</fieldset>
	</div>
    </form>
    <form name="trimsissue_2" id="trimsissue_2" autocomplete="off" >
    <div id="list_fabric_desc_container" style="width:750px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:15px"></div>
    </form>
</div>   
<!-- <div id="devtools-orientation"></div> -->
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	var company_id=$('#cbo_company_id').val();
	if(company_id!=0)
	{
		$('#cbo_sewing_source').val(1);
		$('#cbo_sewing_company').val(company_id);
		$("#cbo_sewing_company").change();
		var length=$("#cbo_location_swing option").length;
		if(length==2)
		{
			$('#cbo_location_swing').val($('#cbo_location_swing option:last').val());
		}
	}





/*
	 (function () {
	'use strict';

	const devtools = {
		isOpen: false,
		orientation: undefined
	};

	const threshold = 160;

	const emitEvent = (isOpen, orientation) => { 
		window.dispatchEvent(new CustomEvent('devtoolschange', {
			detail: {
				isOpen,
				orientation
			}
		}));
	};

	setInterval(() => {
		console.log(window.outerWidth +"__"+ window.innerWidth);
		const widthThreshold = window.outerWidth - window.innerWidth > threshold;
		const heightThreshold = window.outerHeight - window.innerHeight > threshold;
		const orientation = widthThreshold ? 'vertical' : 'horizontal';

		if (
			!(heightThreshold && widthThreshold) &&
			((window.Firebug && window.Firebug.chrome && window.Firebug.chrome.isInitialized) || widthThreshold || heightThreshold)
		) {
			if (!devtools.isOpen || devtools.orientation !== orientation) {
				emitEvent(true, orientation);
			}

			devtools.isOpen = true;
			devtools.orientation = orientation;
		} else {
			if (devtools.isOpen) {
				emitEvent(false, undefined);
			}

			devtools.isOpen = false;
			devtools.orientation = undefined;
		}
	}, 500);

	if (typeof module !== 'undefined' && module.exports) {
		module.exports = devtools;
	} else {
		window.devtools = devtools;
	}
})();*/
</script>


		<!-- <script type="module">
			const stateElement = document.querySelector('#tbl_dtls');
			//const orientationElement = document.querySelector('#devtools-orientation');

			stateElement.textContent = window.devtools.isOpen ? 'yes' : 'no';
			//orientationElement.textContent = window.devtools.orientation ? window.devtools.orientation : '';

			window.addEventListener('devtoolschange', event => {
				stateElement.textContent = event.detail.isOpen ? 'yes' : 'no';
				//orientationElement.textContent = event.detail.orientation ? event.detail.orientation : '';
			});
		</script> -->
</html>
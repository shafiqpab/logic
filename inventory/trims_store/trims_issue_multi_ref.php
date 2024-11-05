<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Trims Issue Multi Reference Entry 
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	10/12/2017
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

$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];

if ($company_id !='') {
    $company_credential_cond = "and comp.id in($company_id)";
}

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Trims Issue Multi Ref Entry", "../../", 1, 1,'','1',''); 

?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	function openmypage_po()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var all_po_id = $('#all_po_id').val();
		var cbo_store_name=$('#cbo_store_name').val();

		if (form_validation('cbo_company_id*cbo_store_name','Company*Store Name')==false)
		{
			return;
		}
			
		var title = 'PO Info';	
		var page_link = 'requires/trims_issue_multi_ref_controller.php?cbo_company_id='+cbo_company_id+'&all_po_id='+all_po_id+'&action=po_search_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var hidden_order_id=this.contentDoc.getElementById("hidden_order_id").value; //Access form field with id="emailfield"
			var hidden_order_no=this.contentDoc.getElementById("hidden_order_no").value; //Access form field with id="emailfield"
			var hide_buyer=this.contentDoc.getElementById("hide_buyer").value; //Access form field with id="emailfield"
			
			$('#all_po_id').val(hidden_order_id);
			$('#txt_buyer_order').val(hidden_order_no);
			
			show_list_view_post(hidden_order_id+'****'+cbo_store_name,'create_itemDesc_search_list_view','list_fabric_desc_container','requires/trims_issue_multi_ref_controller','');
			$('#check_qnty').attr('checked',true);
			calculate(1);

			//setFilterGrid(\'list_fabric_desc_container\',-1);

			var tableFilters = 
			{
				col_operation: {
					id: ["tot_issue_qnty","tot_rcv_qnty","tot_cuml_qnty","tot_yet_issue_qnty"],
					col: [14,15,16,17],
					operation: ["sum","sum","sum","sum"],
					write_method: ["setvalue","setvalue","setvalue","setvalue"]
				}
			}
			setFilterGrid('list_fabric_desc_container',-1,tableFilters);

		}
	}
	
	function openpage_booking()
	{
		var cbo_company_id 	= $('#cbo_company_id').val();
		var cbo_basis 		= $('#cbo_basis').val();
		var cbo_store_name=$('#cbo_store_name').val();
		if (form_validation('cbo_company_id*cbo_basis*cbo_store_name','Company*Issue Basis*Store Name')==false)
		{
			return;
		}
			
		var title = 'Sample Trims Booking Info';	
		var page_link = 'requires/trims_issue_multi_ref_controller.php?cbo_company_id='+cbo_company_id+'&cbo_basis='+cbo_basis+'&action=booking_search_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var hidden_booking_id=this.contentDoc.getElementById("hidden_booking_id").value; //Access form field with id="emailfield"
			var hidden_booking_no=this.contentDoc.getElementById("hidden_booking_no").value; //Access form field with id="emailfield"
			var hide_buyer=this.contentDoc.getElementById("hide_buyer").value; //Access form field with id="emailfield"
			var po_id=this.contentDoc.getElementById("hidden_po_id").value; //Access form field with id="emailfield"
			var po_no=this.contentDoc.getElementById("hidden_po_no").value; //Access form field with id="emailfield"
			var prod_id=this.contentDoc.getElementById("hidden_prod_id").value; //Access form field with id="emailfield"
			
			$('#txt_booking_id').val(hidden_booking_id);
			$('#txt_booking_no').val(hidden_booking_no);
			$('#all_po_id').val(po_id);
			$('#txt_buyer_order').val(po_no);
			$('#txt_buyer_order').attr('disabled',true);
			$('#cbo_store_name').attr('disabled',true);
			show_list_view_post(po_id+'**'+prod_id+'**'+cbo_store_name+'**'+hidden_booking_id,'create_itemDesc_search_list_view_req','list_fabric_desc_container','requires/trims_issue_multi_ref_controller','');
			$('#check_qnty').attr('checked',true);

			var tableFilters = 
				{
					col_operation: {
						id: ["tot_issue_qnty","tot_rcv_qnty","tot_cuml_qnty","tot_yet_issue_qnty"],
						col: [14,15,16,17],
						operation: ["sum","sum","sum","sum"],
						write_method: ["setvalue","setvalue","setvalue","setvalue"]
					}
				}
			
			setFilterGrid('list_fabric_desc_container',-1,tableFilters);

		}
	}
	
	
	function generate_report_file(data,action,page)
	{
		window.open("requires/trims_issue_multi_ref_controller.php?data=" + data+'&action='+action, true );
	}
		
	function fnc_trims_issue(operation)
	{
		if(operation==4)
		{
			 var report_title=$( "div.form_caption" ).html();
			 generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val()+'*'+report_title,'trims_issue_entry_print');return;
		}
		else if(operation==5)
		{
			 var report_title=$( "div.form_caption" ).html();
			 generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val()+'*'+report_title,'trims_issue_entry_print2');return;

		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if ($("#is_posted_account").val()*1 == 1) {
				alert("Already Posted In Accounting. Save Update Delete Restricted.");
				return;
			}
			
			if(operation==2)
			{
				show_msg('13');
				return;
			}
			
			if( form_validation('cbo_company_id*txt_issue_date*cbo_issue_purpose*cbo_basis*cbo_store_name*cbo_sewing_source*cbo_sewing_company*all_po_id','Company*Issue Date*Issue Purpose*Issue Basis*Store Name*Sewing Source*Sewing Company*Buyer Order')==false )
			{
				return;
			}
			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_issue_date').val(), current_date)==false)
			{
				alert("Issue Date Can not Be Greater Than Current Date");
				return;
			}
			
			//alert(operation);return;	
			
			/*var issueBasis =$("#cbo_basis").val();
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
			
			if( form_validation('txt_item_description*txt_issue_qnty','Item Description*Issue Qnty')==false )
			{
				return;
			}
			
			if(($("#txt_issue_qnty").val()*1 > $("#txt_yet_to_issue").val()*1+$("#hidden_issue_qnty").val()*1)) 
			{
				alert("Issue Quantity Excededs Receive Quantity.");
				return;
			}*/			
			
			var j=0; var i=1; var dataString='';
			$("#list_fabric_desc_container").find('tbody tr').not(':first').each(function()
			{
				var po_id=$('#po_no_'+i).attr('title');
				var cboitemgroup=$('#item_group_'+i).attr('title');
				var prod_id=$('#item_descrip_'+i).attr('title');
				var itemdescription=$('#item_descrip_'+i).html();
				var brandSupref=$('#brand_supp_'+i).html();
				
				var gmtscolorid=$('#gmtcolor_'+i).attr('title');
				var gmtssizeId=$('#gmtsize_'+i).attr('title');
				var itemcolorid=$('#item_color_'+i).attr('title');
				var itemsizeid=$('#item_size_'+i).attr('title');
				var cbouom=$('#uom_'+i).attr('title');
				
				var issueqnty=$(this).find('input[name="issueqnty[]"]').val();
				var receiveqnty=$(this).find('input[name="receiveqnty[]"]').val();
				var cuissue=$(this).find('input[name="cuissue[]"]').val();
				var yettoissue=$(this).find('input[name="yettoissue[]"]').val();
				var cbofloor=$(this).find('select[name="cbofloor[]"]').val();
				var cboline=$(this).find('select[name="cboline[]"]').val();
				var globalstock=$(this).find('input[name="globalstock[]"]').val();

				//var cboRecFloor=$(this).find('select[name="cboRecFloor[]"]').val();
	//				var cboRecRoom=$(this).find('select[name="cboRecRoom[]"]').val();
	//				var cboRecRack=$(this).find('select[name="cboRecRack[]"]').val();
 //				var cboRecShelf=$(this).find('select[name="cboRecShelf[]"]').val();
 //				var cboRecBin=$(this).find('select[name="cboRecBin[]"]').val();

				
				var cboRecFloor=$('#td_cboRecFloor_'+i).attr('title');
				var cboRecRoom=$('#td_cboRecRoom_'+i).attr('title');
				var cboRecRack=$('#td_cboRecRack_'+i).attr('title');
				var cboRecShelf=$('#td_cboRecShelf_'+i).attr('title');
				var cboRecBin=$('#td_cboRecBin_'+i).attr('title');
				

				var updatedtlsid=$(this).find('input[name="updatedtlsid[]"]').val();
				var updatetransid=$(this).find('input[name="updatetransid[]"]').val();
				var previousprodid=$(this).find('input[name="previousprodid[]"]').val();
				
				if((issueqnty*1 > yettoissue*1) || (issueqnty*1 > globalstock*1) ) 
				{
					alert("Issue Quantity Not Allow Over Mrr Stock Or Global Stock");
					return;
				}
				
				if(issueqnty>0)	
				{
					j++;
					dataString+='&po_id' + j + '=' + po_id + '&prod_id' + j + '=' + prod_id  +'&cboitemgroup' + j + '=' + cboitemgroup + '&itemdescription' + j + '=' + itemdescription+ '&brandSupref' + j + '=' + brandSupref  + '&itemcolorid' + j + '=' + itemcolorid + '&itemsizeid' + j + '=' + itemsizeid + '&gmtscolorid' + j + '=' + gmtscolorid + '&gmtssizeId' + j + '=' + gmtssizeId + '&cbouom' + j + '=' + cbouom + '&issueqnty' + j + '=' + issueqnty + '&receiveqnty' + j + '=' + receiveqnty + '&cuissue' + j + '=' + cuissue + '&yettoissue' + j + '=' + yettoissue + '&cbofloor' + j + '=' + cbofloor + '&cboline' + j + '=' + cboline + '&globalstock' + j + '=' + globalstock + '&updatedtlsid' + j + '=' + updatedtlsid+ '&updatetransid' + j + '=' + updatetransid+ '&previousprodid' + j + '=' + previousprodid+ '&cboRecFloor' + j + '=' + cboRecFloor+ '&cboRecRoom' + j + '=' + cboRecRoom+ '&cboRecRack' + j + '=' + cboRecRack+ '&cboRecShelf' + j + '=' + cboRecShelf+ '&cboRecBin' + j + '=' + cboRecBin;						
				}
				i++;
			});
				
			if(j<1)
			{
				alert('No data');return;
			}
			
			//alert(dataString); return;

			var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_system_id*cbo_company_id*txt_issue_date*txt_issue_chal_no*cbo_basis*txt_booking_no*txt_booking_id*cbo_store_name*cbo_sewing_source*cbo_sewing_company*cbo_sewing_location_name*cbo_floor_unit_name*txt_remarks*cbo_extra_status*cbo_issue_purpose*update_id',"../../")+dataString;	

			//alert(data);return;		

			freeze_window(operation);
			
			http.open("POST","requires/trims_issue_multi_ref_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange =fnc_trims_issue_Reply_info;
		}
	}
	


	function fnc_trims_issue_Reply_info()
	{
		

		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');	
			show_msg(reponse[0]);
			if((reponse[0]==0 || reponse[0]==1))
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_system_id').value = reponse[2];
				$('#cbo_company_id').attr('disabled','disabled');
				var cbo_store_name=$('#cbo_store_name').val();
				var cbo_basis = $('#cbo_basis').val();
				var txt_booking_id = $('#txt_booking_id').val();
				show_list_view_post(reponse[1]+'****'+cbo_store_name+'**'+cbo_basis+'**'+txt_booking_id,'create_itemDesc_search_list_view_update','list_fabric_desc_container','requires/trims_issue_multi_ref_controller','setFilterGrid(\'list_fabric_desc_container\',-1);');
				set_button_status(1, permission, 'fnc_trims_issue',1,1);
			}
			else if(reponse[0]==50)
			{
				alert(reponse[1]);
				release_freezing();return;
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
			var page_link='requires/trims_issue_multi_ref_controller.php?cbo_company_id='+cbo_company_id+'&action=trims_issue_popup_search';
			var title='Trims Issue Form';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=820px,height=390px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var trims_issue_id=this.contentDoc.getElementById("hidden_issue_id").value;

				if(trims_issue_id!="")
				{
					freeze_window(5);
					//reset_form('trimsissue_1','div_details_list_view*list_fabric_desc_container','','','','cbo_company_id');
					
					var posted_in_account=this.contentDoc.getElementById("hidden_posted_in_account").value; // is posted accounct
					$("#is_posted_account").val(posted_in_account);
					if(posted_in_account==1) document.getElementById("accounting_posted_status").innerHTML="Already Posted In Accounting.";
					else 					 document.getElementById("accounting_posted_status").innerHTML="";
					
					
					get_php_form_data(trims_issue_id, "populate_data_from_trims_issue", "requires/trims_issue_multi_ref_controller" );
					$('#cbo_store_name').attr('disabled',true);
					var cbo_store_name = $('#cbo_store_name').val();
					var cbo_basis = $('#cbo_basis').val();
					var txt_booking_id = $('#txt_booking_id').val();
					show_list_view_post(trims_issue_id+'****'+cbo_store_name+'**'+cbo_basis+'**'+txt_booking_id,'create_itemDesc_search_list_view_update','list_fabric_desc_container','requires/trims_issue_multi_ref_controller','');
					$('#check_qnty').attr('checked',true);

					//setFilterGrid(\'list_fabric_desc_container\',-1);

					var tableFilters = 
					{
						col_operation: {
							id: ["tot_issue_qnty","tot_rcv_qnty","tot_cuml_qnty","tot_yet_issue_qnty"],
							//col: [12,13,14,15],
							col: [14,15,16,17],
							operation: ["sum","sum","sum","sum"],
							write_method: ["setvalue","setvalue","setvalue","setvalue"]
						}
					}
					setFilterGrid("list_fabric_desc_container",-1,tableFilters);
			


					//set_button_status(1, permission, 'fnc_trims_issue',1,1);
					set_button_status(1, permission, 'fnc_trims_issue',1,1);
					calculate(1);
					release_freezing();
				}
							 
			}
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
			var page_link='requires/trims_issue_multi_ref_controller.php?update_dtls_id='+update_dtls_id+'&action=goods_placement_popup';
			var title='Goods Placement Entry Form';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../');
			
		}
	}
	
	function set_form_data(data)
	{
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
		$('#txt_rack').val(data[9]);
		$('#txt_shelf').val(data[10]);
		$('#txt_item_color_id').val(data[11]);
		$('#txt_global_stock').val(data[12]);
		$('#txt_cumulative_issued').val(data[13]);
		$('#txt_yet_to_issue').val(data[14]);
		$('#txt_received_qnty').val(data[15]);
		$('#txt_buyer_order').val(data[17]);
		$('#selected_po_id').val(data[18]);
		$('#txt_cons_rate').val(data[19]);
		
		var issueBasis =$("#cbo_basis").val();
		var txt_booking_id = $('#txt_booking_id').val();
		if(issueBasis==2 && txt_booking_id!="")
		{
			get_php_form_data(txt_booking_id+"**"+$('#hidden_prod_id').val(), 'get_trim_cum_info_for_trims_booking', 'requires/trims_issue_multi_ref_controller' );
		}
	}
	
	function enable_disable()
	{
		var issueBasis =$("#cbo_basis").val();
		//reset_form('','','txt_booking_id*txt_booking_no*txt_buyer_order*all_po_id*txt_issue_qnty*txt_item_description*hidden_prod_id','','','');
		
		if(issueBasis==3)	
		{
			$("#txt_booking_no").removeAttr("disabled");
			$("#txt_buyer_order").attr("disabled",true);
		}
		else
		{	
			$("#txt_buyer_order").removeAttr("disabled");
			$("#txt_booking_no").attr("disabled",true);	
		}
	}	
	
	function changeHeader(id)
	{
		//alert(id);
		//var select = document.getElementById("headerSelector");
        var dynamicTitle = document.getElementById("dynamicTitle");

		if(id==3){
			dynamicTitle.innerHTML = 'Req. Qty';
		}
		else{
			dynamicTitle.innerHTML = 'Recv. Qty';
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
			load_drop_down( 'requires/trims_issue_multi_ref_controller',sewing_company, 'load_drop_down_sewing_location', 'location_sewing_td' );
		}
		else
		{
			load_drop_down( 'requires/trims_issue_multi_ref_controller',cbo_company_id, 'load_drop_down_sewing_location', 'location_sewing_td' );
		}
	}

	function fn_load_sewing_floor()
	{
		var issue_purpose = $('#cbo_issue_purpose').val();
		var cbo_sewing_company = $('#cbo_sewing_company').val();
		var all_data=cbo_sewing_company + "__sep__" + issue_purpose;

		var tbl_length=$("#list_fabric_desc_container").find('tbody tr').length;

		var sewing_floor_result = return_global_ajax_value(all_data, 'sewing_floor_list', '', 'requires/trims_issue_multi_ref_controller');

		var JSONObject = JSON.parse(sewing_floor_result);
		for(var i=1; i<=tbl_length; i++)
		{
			$('#cbofloor_'+i).html('<option value="'+0+'">-- Select --</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				//alert(Object.keys(JSONObject));
				$('#cbofloor_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}
	}
	
	
	function calculate(i)
	{
		var quantity 		= $('#issueqnty_'+i).val()*1;
		var yettoissue 		= $('#yettoissue_'+i).val()*1;
		var globalstock		= $('#globalstock_'+i).val()*1;
		var prev_qnty		= $('#issueqnty_'+i).attr('placeholder')*1;
		if(quantity>yettoissue || quantity>globalstock)
		{
			alert("Issue Quantity Not Allow Over Mrr Stock Or Global Stock");
			if(quantity>globalstock)
			{
				$('#issueqnty_'+i).val(0);
			}
			else
			{
				$('#issueqnty_'+i).val(prev_qnty);
			}
			
			return;
		}
		var ddd={ dec_type:5, comma:0, currency:''}
		var numRow = $('table#list_fabric_desc_container tbody tr').length-1;
		// alert(numRow);
		//math_operation( "tot_issue_qnty", "issueqnty_", "+", numRow,ddd );
		//math_operation( "tot_rcv_qnty", "receiveqnty_", "+", numRow,ddd );
		//math_operation( "tot_cuml_qnty", "cuissue_", "+", numRow,ddd );
		//math_operation( "tot_yet_issue_qnty", "yettoissue_", "+", numRow,ddd );
		var total_issue_qnty=0;var total_receive_qnty=0;var total_cuml_qnty=0;var total_yet_issue_qnty=0;
		$("#list_fabric_desc_container").find('tbody tr').not(':first').each(function(index, element) 
		{
			if($(this).css('display') != 'none')
			{
				total_issue_qnty+=$(this).find('input[name="issueqnty[]"]').val()*1;
				total_receive_qnty+=$(this).find('input[name="receiveqnty[]"]').val()*1;
				total_cuml_qnty+=$(this).find('input[name="cuissue[]"]').val()*1;
				total_yet_issue_qnty+=$(this).find('input[name="yettoissue[]"]').val()*1;
			}
		});
		$('#tot_issue_qnty').val(total_issue_qnty);
		$('#tot_rcv_qnty').val(total_receive_qnty);
		$('#total_cuml_qnty').val(total_cuml_qnty);
		$('#tot_yet_issue_qnty').val(total_yet_issue_qnty);
	}

	
	function set_receive_basis()
	{
		var cbo_basis = $('#cbo_basis').val();
		var list_view_wo =trim(return_global_ajax_value( cbo_basis, 'mrr_details', '', 'requires/trims_receive_multi_ref_entry_v2_controller'));
		$('#list_fabric_desc_container').html('');
		$('#list_fabric_desc_container').html(list_view_wo);
	}
	
	function fn_fill_qnty()
	{
		var i=1;
		if($('#check_qnty').is(':checked'))
		{
			$("#list_fabric_desc_container").find('tbody tr').each(function(index, element) {
				var issue_qnty=$(this).find('input[name="issueqnty[]"]').val()*1;
				if(issue_qnty<=0)
				{
					if($(this).css('display') != 'none'){
						$(this).find('input[name="issueqnty[]"]').val($(this).find('input[name="yettoissue[]"]').val()*1);
					}
				}
                
            });
		}
		else
		{
			$("#list_fabric_desc_container").find('tbody tr').each(function(index, element) {
				if($(this).css('display') != 'none'){
					$(this).find('input[name="issueqnty[]"]').val("");
				}
            });
		}
		
		var total_issue_qnty=0;var total_receive_qnty=0;var total_cuml_qnty=0;var total_yet_issue_qnty=0;
		$("#list_fabric_desc_container").find('tbody tr').not(':first').each(function(index, element) 
		{
			if($(this).css('display') != 'none')
			{
				total_issue_qnty+=$(this).find('input[name="issueqnty[]"]').val()*1;
				total_receive_qnty+=$(this).find('input[name="receiveqnty[]"]').val()*1;
				total_cuml_qnty+=$(this).find('input[name="cuissue[]"]').val()*1;
				total_yet_issue_qnty+=$(this).find('input[name="yettoissue[]"]').val()*1;
			}
		});
		$('#tot_issue_qnty').val(total_issue_qnty);
		$('#tot_rcv_qnty').val(total_receive_qnty);
		$('#total_cuml_qnty').val(total_cuml_qnty);
		$('#tot_yet_issue_qnty').val(total_yet_issue_qnty);
		//calculate(1);
	}
	
	
	function fn_floor(str)
	{
		var tbl_length=$("#list_fabric_desc_container").find('tbody tr').length;
		var floor_val=$("#cbofloor_"+str).val()*1;
		var com_id=$('#cbo_company_id').val();
		var all_data=com_id + "__" + floor_val;
		var floor_result = return_global_ajax_value(all_data, 'floor_list', '', 'requires/trims_issue_multi_ref_controller');
		/*for(var i=str; i<=tbl_length;i++)
		{
			$("#cbofloor_"+i).val(floor_val);
		}*/

		for(var i=str; i<=tbl_length;i++)
		{
			$("#cbofloor_"+i).val(floor_val);
		}
		
		var JSONObject = JSON.parse(floor_result);
		for(var i=str; i<=tbl_length; i++)
		{
			$('#cboline_'+i).html('<option value="'+0+'">-- Select --</option>');
			for (var key of Object.keys(JSONObject).sort())
			{
				//alert(Object.keys(JSONObject));
				$('#cboline_'+i).append('<option value="'+key+'">'+JSONObject[key]+'</option>');
			};
		}
	}
	
	function fn_line(str)
	{
		var tbl_length=$("#list_fabric_desc_container").find('tbody tr').length;
		var line_val=$("#cboline_"+str).val()*1;
		/*for(var i=str; i<=tbl_length;i++)
		{
			$("#cboline_"+i).val(line_val);
		}*/
		
		for(var i=str; i<=tbl_length;i++)
		{
			//alert(i);
			$("#cboline_"+i).val(line_val);
		}
		
	}
	
	
  
 

 


</script>
<body onLoad="set_hotkey()">
<div style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission); ?><br />    		 
    <form name="trimsissue_1" id="trimsissue_1" autocomplete="off" >
    <div style="width:1500px;" align="left">   
        <fieldset style="width:1500px;">
        <legend>Trims Issue Entry</legend>
        <br>
        	<fieldset style="width:1250px;">
                <table width="100%" cellspacing="2" cellpadding="0" border="0" id="tbl_master" align="left">
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
                                echo create_drop_down( "cbo_company_id", 132, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond $company_credential_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down( 'requires/trims_issue_multi_ref_controller',this.value, 'load_drop_down_store', 'store_td' );load_drop_down( 'requires/trims_issue_multi_ref_controller', this.value, 'load_drop_down_sewing_location', 'location_sewing_td' );load_drop_down( 'requires/trims_issue_multi_ref_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                            ?>
                        </td>
                        <td width="98" class="must_entry_caption"> Issue Date </td>
                        <td width="143">
                            <input class="datepicker" type="text" style="width:120px" value="<? echo date('d-m-Y');?>" name="txt_issue_date" id="txt_issue_date"/>
                        </td>
                        <td width="90" class="must_entry_caption">Issue Purpose</td>
                        <td>
                            <? 
                                echo create_drop_down( "cbo_issue_purpose", 132, $yarn_issue_purpose,"", 1, "-- Select --", 36,"fn_load_sewing_floor();","","3,4,8,26,29,30,31,36,37,41,42" );
                            ?>
                       </td>
					   <td width="100">Issue Challan No </td>
                        <td>
                            <input type="text" name="txt_issue_chal_no" id="txt_issue_chal_no" class="text_boxes" style="width:120px" >
                        </td>
                        <td class="must_entry_caption">Issue Basis</td>
                        <td>
                            <? 
                                $trims_issue_basis=array(1=>"With Order",2=>"Without Order",3=>"Requisition");
								
                                echo create_drop_down( "cbo_basis", 132, $trims_issue_basis,"",0, "-- Select Basis --", $selected, "enable_disable();changeHeader(this.value);", "", "1,3");
                            ?>
                        </td>
                    </tr> 
                    <tr>
                       
                        <td>Location</td>                                              
                        <td id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location_name", 132, $blank_array,"", 1, "-- Select Location --", 0, "" );
                            ?>
                        </td>
						<td class="must_entry_caption">Store Name </td>
                        <td id="store_td">
                            <?
                                echo create_drop_down( "cbo_store_name", 132, $blank_array,"",1, "--Select store--", 1, "" );
                            ?>
                        </td> 
                        <td>Requisition No</td>
                        <td>
                            <input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:120px"  placeholder="Double Click to Search" onDblClick="openpage_booking();" readonly disabled />
                            <input type="hidden" name="txt_booking_id" id="txt_booking_id" />
                        </td>
                        <td class="must_entry_caption">Sewing Source </td>
                        <td>
                            <?
                                echo create_drop_down("cbo_sewing_source", 132, $knitting_source,"", 1, "-- Select --", 0,"load_drop_down( 'requires/trims_issue_multi_ref_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_knitting_com','sewing_com');",0,'1,3');
                            ?>
                        </td>
                        <td class="must_entry_caption">Working Company</td>
                        <td id="sewing_com">
                            <?
                                echo create_drop_down( "cbo_sewing_company", 132, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Sewing Company--", "", "load_drop_down( 'requires/trims_issue_multi_ref_controller', this.value, 'load_drop_down_sewing_location', 'location_sewing_td' );fn_load_sewing_floor();","" );
                                //echo create_drop_down( "cbo_sewing_company", 132, $blank_array,"",1, "--Select Sewing Company--", 1, "" );
                            ?>
                        </td>
                    </tr>
                    <tr>
                    	<td>Sew. Location</td>                                              
                        <td id="location_sewing_td">
                            <? //load_drop_down( 'requires/trims_issue_multi_ref_controller', this.value, 'load_drop_down_floor', 'floor_td' );
                                echo create_drop_down( "cbo_sewing_location_name", 132, $blank_array,"", 1, "-- Select Location --", 0, "" );
                            ?>
                        </td>
                        <td>Floor/Unit</td>
                        <td id="floor_unit_td">
                        	<? //load_drop_down( 'requires/trims_issue_multi_ref_controller', this.value, 'load_drop_down_floor', 'floor_td' );
                                echo create_drop_down( "cbo_floor_unit_name", 132, $blank_array,"", 1, "-- Select Floor/Unit --", 0, "" );
                            ?>
                        </td>
                        <td>Remarks</td>
                        <td><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:120px" ></td>
						<td>Additional/Extra</td>
						<td>
							<?
								echo create_drop_down("cbo_extra_status", 132, $yes_no,"", 1,"-- Select --", 2,"");
							?>
						</td>
                    </tr>
                </table>
            </fieldset>
            <br>
            <table width="100%" border="0" cellpadding="0"  cellspacing="2" >
                <tr>
                <td> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td align="right" class="must_entry_caption" width="100"><strong>Buyer Order</strong></td>
                    <td height="25" style="border-bottom:0px solid #666">
                    <input type="text" name="txt_buyer_order" id="txt_buyer_order" class="text_boxes" style="width:135px;" onDblClick="openmypage_po()" placeholder="Double click to search" readonly/>
                    <input type="hidden" name="all_po_id" id="all_po_id" readonly>
                    </td>
                 </tr>
            </table>
            
            <fieldset style="width:2140px; margin-top:10px;">
                <legend>Trims Issue Entry details part</legend>
                <table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" id="tbl_fabric_desc_item">
                	<thead>
						<th width="120">Order No</th>
                        <th width="80">Job No</th>
                        <th width="100">Style No</th>
                        <th width="100">Internal Ref</th>
                        <th width="80">Article No.</th>
                        <th width="100">Item Group</th>
                        <th width="50">Product ID</th>
                        <th width="140">Item Description</th>
                        <th width="100">Brand/ Sup Ref</th>
						<th width="80">Gmts Color</th>
						<th width="80">Gmts Size</th>
                        <th width="80">Item Color</th>
                        <th width="80">Item Size</th>
                        <th width="80">UOM</th>
                        <th width="75"><input type="checkbox" id="check_qnty" name="check_qnty" onChange="fn_fill_qnty()" /><br>Issue Qnty</th>
                        <th width="75" id="dynamicTitle">Recv. Qty</th>
                        <th width="75">Cumul. Issued</th>
                        <th width="75">Yet to Issue</th>
                        <th width="60">Sewing Floor</th>
                        <th width="60">Floor</th>
                        <th width="60">Room</th>
                        <th width="60">Rack</th>
                        <th width="60">Shelf</th>
                        <th width="60">Bin/Box</th>
                        <th width="100">Sewing Line No</th>
                        <th width="100">Stock Qty</th>
                    </thead>
                </table>
                <table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" id="list_fabric_desc_container">
                	<tbody> 
                        <tr id="row_1" align="center">
                            <td width="120" id="po_no_1"></td>
							<td width="80" id="job_no_1"></td>
                            <td width="100" id="style_no_1"></td>
                            <td width="100" id="internal_ref_1"></td>
                            <td width="80" id="article_no_1"></td>
                            <td width="100" id="item_group_1"></td>
                            <td width="50" id="prod_id_1"></td>
                            <td width="140" id="item_descrip_1"></td>
                            <td width="100" id="brand_supp_1"></td>
							<td width="80" id="gmts_color_1"></td>
                            <td width="80" id="gmts_size_1"></td>
                            <td width="80" id="item_color_1"></td>
                            <td width="80" id="item_size_1"></td>
                            <td width="80" id="uom_1"></td>
                            <td width="75" id="tdissueqnty_1">
                            <input type="text" name="issueqnty[]" id="issueqnty_1" class="text_boxes_numeric" style="width:60px;" onBlur="calculate(1);"  /> 
                            </td>
                            <td width="75" id="tdreceiveqnty_1">
                            <input type="text" name="receiveqnty[]" id="receiveqnty_1" class="text_boxes_numeric" style="width:60px;" value=""  readonly disabled />
                            </td>
                            <td width="75" id="tdcuissue_1">
                                <input name="cuissue[]" id="cuissue_1" class="text_boxes_numeric" type="text" style="width:60px;" value=""  readonly disabled/>
                            </td>
                            <td width="75" id="tdyettoissue_1">
                            <input class="text_boxes_numeric"  name="yettoissue[]" id="yettoissue_1" value="" type="text" style="width:60px;" readonly />
                            </td>
                            <td width="60" id="td_floor_1">
                            <?
							echo create_drop_down( "cbofloor_1", 60, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 order by floor_name","id,floor_name", 1, "-- Select --", $selected,"fn_floor(1);",0,"","","","","","","cbofloor[]"); 
							?>
                            </td>
							<td width="60" id="td_cboRecFloor_1">
								<? //echo create_drop_down( "cboRecFloor_1", 60,$blank_array,"", 1, "--Select--", 0,"",1,"","","","","","","cboRecFloor[]"); ?>
							</td>
							<td width="60" id="td_cboRecRoom_1">
										<? //echo create_drop_down( "cboRecRoom_1", 60,$blank_array,"", 1, "--Select--", 0, "",1,"","","","","","","cboRecRoom[]" );  ?>
							</td>
							<td width="60" id="td_cboRecRack_1">
										<? //echo create_drop_down( "cboRecRack_1", 60,$blank_array,"", 1, "--Select--", 0, "",1,"","","","","","","cboRecRack[]" ); ?>
						    </td>
							<td width="60" id="td_cboRecShelf_1">
										<? //echo create_drop_down( "cboRecShelf_1", 60,$blank_array,"", 1, "--Select--", 0,"",1,"","","","","","","cboRecShelf[]" );  ?>
							</td>
							<td width="60" id="td_cboRecBin_1">
										<? //echo create_drop_down( "cboRecBin_1", 60,$blank_array,"", 1, "--Select--", 0,"",1,"","","","","","","cboRecBin[]" ); ?>
							</td>


                            <td width="100" id="td_line_1">
                            <?
							echo create_drop_down( "cboline_1", 100, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 order by line_name","id,line_name", 1, "-- Select --", $selected,"fn_line(1);",0,"","","","","","","cboline[]"); 
							?>
                            </td>
                            <td width="100" id="tdglobalstock_1">
                            <input class="text_boxes_numeric"  name="globalstock[]" id="globalstock_1" value="" type="text" style="width:70px;" readonly disabled />
                            <input type="hidden" name="updatedtlsid[]" id="updatedtlsid_1" value="" readonly>
                            <input type="hidden" name="updatetransid[]" id="updatetransid_1" value="" readonly>
                            <input type="hidden" name="previousprodid[]" id="previousprodid_1" value="" readonly>
                            
                            </td>
                        </tr>
                    </tbody>
					
                </table>
				<table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" id="list_fabric_desc_container_footer">
					<tfoot>
						<tr>
							<th width="120">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="50">&nbsp;</th>
							<th width="140">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80" align="center">Total</th>
							<th width="75"><input type="text" id="tot_issue_qnty" name="tot_issue_qnty" style="width:60px;" class="text_boxes_numeric" readonly disabled /></th>

							<th width="75"><input type="text" id="tot_rcv_qnty" name="tot_rcv_qnty" style="width:60px;" class="text_boxes_numeric" readonly disabled /></th>
							<th width="75"><input type="text" id="tot_cuml_qnty" name="tot_cuml_qnty" style="width:60px;" class="text_boxes_numeric" readonly disabled /></th>		
							<th width="75"><input type="text" id="tot_yet_issue_qnty" name="tot_yet_issue_qnty" style="width:60px;" class="text_boxes_numeric" readonly disabled /></th>
							


							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
						</tr>
                        
                    </tfoot>
				</table>
            </fieldset>
            	<table width="100%">
                    <tr>
                        <td width="80%" align="center"> 
                        <? 
                       echo load_submit_buttons($permission, "fnc_trims_issue", 0,1,"window.location.reload();",1);                        
                        ?>
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
                <div style="width:680px;" id="div_details_list_view"></div>
		</fieldset>
	</div>
    </form>
</div>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	
$('input[name^="issueqnty"]').live('keydown', function(e) {
	
	switch (e.keyCode) {
			case 38:
				var target_id_arr=e.target.id.split('_');
				var row_num=parseInt(target_id_arr[1])-1;
				//$('#issueqnty_'+row_num).focus();
				$('#issueqnty_'+row_num).select();
				break;
			case 40:
				var target_id_arr=e.target.id.split('_');
				var row_num=parseInt(target_id_arr[1])+1;
				//$('#issueqnty_'+row_num).focus();
				$('#issueqnty_'+row_num).select();
				break;
	}
});
	
	
var company_id=$('#cbo_company_id').val();
if(company_id!=0)
{
	$('#cbo_sewing_source').val(1);
	$('#cbo_sewing_company').val(0);
	var length=$("#cbo_sewing_location_name option").length;
	if(length==2)
	{
		$('#cbo_sewing_location_name').val($('#cbo_sewing_location_name option:last').val());
	}
}
	
	
</script>
</html>
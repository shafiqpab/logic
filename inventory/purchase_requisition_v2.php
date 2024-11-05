<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create purchase requistion
				
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	18-03-2023
Updated by 		:		
Update date		: 	
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//print_r($_SESSION['logic_erp']['data_arr'][69][3]);

//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];
$location_id = $userCredential[0][csf('location_id')];

$company_credential_cond = "";

if ($company_id >0) {
    $company_credential_cond = "and comp.id in($company_id)";
}

if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)"; 
}

if($item_cate_id !='') {
    $item_cate_credential_cond = $item_cate_id ;  
}
//========== user credential end ==========

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Purchase Requistion Info","../", 1, 1, $unicode);
?>	
<link rel="stylesheet" href="../sweetalert2/sweetalert2.css">
<!-- Include SweetAlert2 JavaScript -->
<script  type="text/javascript" src="../sweetalert2/sweetalert2.all.js"></script>
<script src="../js/socket.io.js"></script>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission='<? echo $permission; ?>';
<?
$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][69] );
echo "var field_level_data= ". $data_arr . ";\n";
?>
var socket;
var socket_url = '<?=$_SESSION['socket_url'];?>';
	$( document ).ready(function() {
		try {
			if(socket_url !="")
			{
				socket =io.connect('<?=$_SESSION['socket_url'];?>');
				console.error("Socket connected");
				//showNotification("Socket connected",'success');
			}
		}
		catch (error)
		{
			console.error("Unable to connect to the server:", error);
			//showNotification(error,'error');
		}
	});
	function openmypage_requisition()
	{
		if (form_validation('cbo_company_name','Company Name')==false)
		{
		  return;
		}
		else
		{
		
	  		var cbo_company_name = $("#cbo_company_name").val();
	  		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/purchase_requisition_v2_controller.php?cbo_company_name='+cbo_company_name+'&action=purchase_requisition_popup', 'Purchase Requisition Search', 'width=1140px,height=400px,center=1,resize=1,scrolling=0','../')
	  		emailwindow.onclose=function()
	  		{
	  			var theemail=this.contentDoc.getElementById("selected_job");
	  			if (theemail.value!="")
	  			{
	  				freeze_window(5);
	  				get_php_form_data(theemail.value, "load_php_requ_popup_to_form","requires/purchase_requisition_v2_controller" );
	  				show_list_view(theemail.value,'purchase_requisition_list_view_dtls','purchase_requisition_list_view_dtls','requires/purchase_requisition_v2_controller','setFilterGrid("list_view",-1)');
	  				disable_enable_fields('cbo_company_name*cbo_store_name*cbo_location_name*txt_date_from*cbo_req_basis',1);
	  				set_button_status(1, permission, 'fnc_purchase_requisition',1,0);
	  				release_freezing();
	  			}
	  		}
		
		}
	}
	
	function openmypage_manual_requisition()
	{
		var cbo_company_name = $("#cbo_company_name").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/purchase_requisition_v2_controller.php?cbo_company_name='+cbo_company_name+'&action=purchase_manual_requisition_popup', 'Purchase Manual Requisition Search', 'width=900px,height=400px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("txt_manual_req");
			if (theemail.value!="")
			{
				//freeze_window(5);
				document.getElementById('txt_manual_req').value = theemail.value;
				//release_freezing();
			}
		}
	}
			
	function fnc_purchase_requisition( operation )
	{
		var is_approved=$('#is_approved').val();
		
		if(is_approved==1 || is_approved==3)
		{
			alert("Requisition is Approved. So Change Not Allowed");
			return;	
		}

		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][69]);?>')
		{
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][69]);?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][69]);?>')==false)
			{
				return;
			}
		}
		
		if (form_validation('cbo_company_name*cbo_location_name*cbo_store_name*txt_date_from','Company Name*Location*Item Catagory*Store Name*Requisation Date')==false)
		{
			return;
		}
		else
		{
			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_requisition_no*cbo_company_name*cbo_location_name*cbo_division_name*cbo_department_name*cbo_section_name*txt_date_from*cbo_store_name*cbo_pay_mode*cbo_source*cbo_currency_name*txt_date_delivery*txt_remark*txt_reference*txt_manual_req*update_id*txt_req_by*cbo_ready_to_approved*txt_iso_no*cbo_priority_id*cbo_requisition_id*txt_tenor*justification_value*cbo_req_basis',"../");
			/*-------------additional code-----------*/
			
			freeze_window(operation);
			http.open("POST","requires/purchase_requisition_v2_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_purchase_requisition_reponse;
		}
	}

	function fnc_purchase_requisition_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			// if (reponse[0].length>2) reponse[0]=10;
			
			show_msg(reponse[0]);
			if( reponse[0]==0 ||  reponse[0]==1)
			{
				document.getElementById('txt_requisition_no').value = reponse[1];
				document.getElementById('update_id').value = reponse[2];
				disable_enable_fields('cbo_company_name*cbo_store_name*cbo_location_name*txt_date_from*cbo_req_basis',1);
				set_button_status(1, permission, 'fnc_purchase_requisition',1,0);
				if(document.getElementById("cbo_ready_to_approved").value == 1)
				{
					if(socket_url !="")
					{
						var approval_data = [];
						var obj = {
							ref_id : document.getElementById('update_id').value,
							entry_form : 1
						}
						approval_data.push(obj);
						socket.emit('message',{approval_data: approval_data});
						socket.emit('message_count', { user_id: '<?=$_SESSION['logic_erp']['user_id'];?>'});
					}
				}
			}
			else if( reponse[0]==2)
			{
				reset_form('purchaserequisition_1*purchaserequisition_2','item_category_div*purchase_requisition_list_view_dtls','','','disable_enable_fields(\'cbo_company_name\');$(\'#tbl_purchase_item tbody tr:not(:first)\').remove();')
				/*------write new code below if necerssary-------*/
			}
			else if(reponse[0]==15) 
			{ 
				 setTimeout('fnc_purchase_requisition( 0 )',8000); 
			}
			else if(reponse[0]==11) 
			{ 
				 alert(reponse[1]);
			}
			else if(reponse[0]==21) 
			{ 
				 alert(reponse[1]);
			}
			

			
			release_freezing();
		}
	}


	function openmypage()
	{
		var txt_requisition_no=$('#txt_requisition_no').val();
		var cbo_company_name=$('#cbo_company_name').val();
		var update_id=$('#update_id').val();		
		var cbo_store_name=$('#cbo_store_name').val();
		var cbo_req_basis=$('#cbo_req_basis').val();
		if(txt_requisition_no=="")
		{
			alert("Save Data First");return;
		}
		
		if (form_validation('cbo_company_name*cbo_store_name','Company Name*Item Catagory*Store Name')==false)
		{
			return;
		}
		else
		{
			var data=cbo_company_name+"_"+cbo_store_name+"_"+update_id+"_"+cbo_req_basis;
			if(cbo_req_basis==4)
			{
				var page_link='requires/purchase_requisition_v2_controller.php?action=account_order_popup&data='+data;
			}
			else
			{
				var page_link='requires/purchase_requisition_v2_controller.php?action=item_issue_requisition_popup_search&data='+data;
			}
			
			var title='Search Item Account';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1280px,height=410px,center=1,resize=0,scrolling=0','')
			
			emailwindow.onclose=function()
			{
				var theemail=this.contentDoc.getElementById("item_1").value;
				var re_order_lebel=this.contentDoc.getElementById("re_order_lebel").value;
				var issue_req_dtls=this.contentDoc.getElementById("issue_req_dtls_1").value;
				//alert(theemail);return; 
				if(theemail!="")
				{
					var row=0;
					//alert(issue_req_dtls);return;
					var data=theemail+"**"+row+"**"+cbo_store_name+"**"+re_order_lebel+"**"+$('#update_id').val()+"**"+cbo_req_basis+"**"+issue_req_dtls;
					var list_view_orders = return_global_ajax_value( data, 'load_php_popup_to_form', '', 'requires/purchase_requisition_v2_controller');
					$("#tbl_purchase_item tbody").html(list_view_orders);
					set_all_onclick();
					release_freezing();

					setFieldLevelAccess($('#cbo_company_name').val());
				}
			}
		}
	}

	function calculate_val()
	{
		var tot_row=$('#tbl_purchase_item'+' tbody tr').length;  
		//alert(tot_row);
		for(var i=1; i<=tot_row; i++)
		{
			var quantity_val=parseFloat(Number($('#quantity_'+i).val()));
			var rate_val=parseFloat(Number($('#rate_'+i).val()));
			var attached_val=quantity_val*rate_val;
			//alert(quantity_val+"="+rate_val+"="+attached_val);
			document.getElementById('amount_'+i).value = number_format (attached_val, 2,'.',"");
		}
	}

	function fnc_purchase_requisition_dtls( operation )
	{
		if (form_validation('update_id*quantity_1','Master Table*Quantity')==false)
		{
		  return;
		}
		else
		{
			var is_approved=$('#is_approved').val();
			
			if(is_approved==1 || is_approved==3)
			{
				alert("Requisition is Approved. So Change Not Allowed");
				return;	
			}
			
			const btn = (operation==0) ? document.getElementById('save2') : document.getElementById('update2');
			btn.disabled=true;
			
			var tot_row=$('#tbl_purchase_item'+' tbody tr').length;
			var update_id=document.getElementById('update_id').value;
			var cbo_pay_mode=document.getElementById('cbo_pay_mode').value;
			var budge_validation=document.getElementById('budge_validation').value;
			var cbo_req_basis=document.getElementById('cbo_req_basis').value;
			var data="action=save_update_delete_dtls&operation="+operation +"&tot_row="+tot_row+"&update_id="+update_id+"&cbo_pay_mode="+cbo_pay_mode+"&cbo_req_basis="+cbo_req_basis;
			//var update_id=document.getElementById('update_id').value;
			var data1='';
			
			for(var i=1; i<=tot_row; i++)
			{
				if(trim($("#quantity_"+i).val())!="" && trim($("#itemdescription_"+i).val())!="")
				{
					if($("#rate_"+i).val()*1<=0 && budge_validation==1)
					{
						alert("Please Input Rate");
						$("#rate_"+i).focus();
						return
					}
					/*-------additional code------------- cboItemCategory_ 1 */
					
					// data1+=get_submitted_data_string('subgroup_'+i+'*itemdescription_'+i+'*itemsize_'+i+'*hiddenitemgroupid_'+i+'*txtreqfor_'+i+'*txtuom_'+i+'*quantity_'+i+'*txtbrand_'+i+'*txtmodelname_'+i+'*cboOrigin_'+i+'*rate_'+i+'*amount_'+i+'*stock_'+i+'*reorderlable_'+i+'*txtvehicle_'+i+'*txtremarks_'+i+'*cbostatus_'+i+'*item_'+i+'*hiddenid_'+i+'*cboItemCategory_'+i+'*txtdatedelivery_'+i+'*txtused_'+i+'*hiddenissreqdtlsid_'+i+'*mstId_'+i+'*txtIndent_'+i,"../",i);

					data1+=get_submitted_data_string('subgroup_'+i+'*itemdescription_'+i+'*itemsize_'+i+'*hiddenitemgroupid_'+i+'*txtreqfor_'+i+'*txtuom_'+i+'*quantity_'+i+'*txtbrand_'+i+'*txtmodelname_'+i+'*cboOrigin_'+i+'*rate_'+i+'*amount_'+i+'*stock_'+i+'*reorderlable_'+i+'*txtvehicle_'+i+'*txtremarks_'+i+'*cbostatus_'+i+'*item_'+i+'*hiddenid_'+i+'*cboItemCategory_'+i+'*txtdatedelivery_'+i+'*txtused_'+i+'*hiddenissreqdtlsid_'+i+'*mstId_'+i+'*txtIndent_'+i+'*itemnumber_'+i+'*cboServiceFor_'+i  ,"../",i);
					
				}
			}
			
		    data=data+data1;
		    //alert (data1);return;

		    freeze_window(operation);
		    http.open("POST","requires/purchase_requisition_v2_controller.php",true);
		    http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		    http.send(data);
		    http.onreadystatechange = fnc_purchase_requisition_dtls_reponse;
		}
	}

	function fnc_purchase_requisition_dtls_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			if( reponse[0]==11)
			{
                show_msg(reponse[0]);
				const btn = (reponse[0]==1) ?  document.getElementById('update2') :document.getElementById('save2');
				btn.disabled=false;
				alert(reponse[1]);release_freezing();return;
			}
			if( reponse[0]==24)
			{
                const btn = (reponse[0]==1) ?  document.getElementById('update2') :document.getElementById('save2');
				btn.disabled=false;
				alert("Work Order Done Update Or Delete Not allow");release_freezing();return;
			}
            if( reponse[0]==55)
            {
                show_msg(10);
				const btn = (reponse[0]==1) ?  document.getElementById('update2') :document.getElementById('save2');
				btn.disabled=false;
                alert(reponse[1]);
                release_freezing();return;
            }
			if( reponse[0]==0 ||  reponse[0]==1 ||  reponse[0]==2)
			{
                show_msg(reponse[0]);
				show_list_view(reponse[1],'purchase_requisition_list_view_dtls','purchase_requisition_list_view_dtls','requires/purchase_requisition_v2_controller','setFilterGrid("list_view",-1)');
				reset_form('purchaserequisition_2','','','','$(\'#tbl_purchase_item tbody tr:not(:first)\').remove();',0);
				$('#itemaccount_1').attr('disabled',false);
				const btn = (reponse[0]==1) ?  document.getElementById('update2') :document.getElementById('save2');
				btn.disabled=false;
				set_button_status(0, permission, 'fnc_purchase_requisition_dtls',2);
			}
			release_freezing();
		}
	}
	
	function generate_report(type)
	{
		//alert(type);
		if ( $('#txt_requisition_no').val()=='')
		{
			alert ('Requisition Not Save.');
			return;
		} 
		if(type==3)
		{
			var show_item="";
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remark').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_2", "requires/purchase_requisition_v2_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(type==5)
		{
			var show_item="";
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remark').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val()+'*'+$('#is_approved').val(), "purchase_requisition_print_3", "requires/purchase_requisition_v2_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(type==8)
		{
			var show_item="";
			var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remark').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_8", "requires/purchase_requisition_v2_controller" ) ;
			//return;
			show_msg("3");
		}else if(type==26)
		{
			var show_item="";
			var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remark').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_26", "requires/purchase_requisition_v2_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(type==6)
		{

			var show_item="";
			var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remark').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_4", "requires/purchase_requisition_v2_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(type==9)
		{

			var show_item="";
			var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remark').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_9", "requires/purchase_requisition_v2_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(type==7)
		{
			var show_item="";
			var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remark').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_5", "requires/purchase_requisition_v2_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(type==10)
		{

			
			var show_item="";
			//var r=confirm("Press  \"Cancel\"  to hide  Last Rate & Req Value \nPress  \"OK\"  to Show Last Rate & Req Value");
			var r=confirm("Press  \"Cancel\"  to hide Last Rec. Date & Last Rec. Qty & Last Rate & Req. Value \nPress  \"OK\"  to Show Last Rec. Date & Last Rec. Qty & Last Rate & Req. Value");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
			
			var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remark').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_10", "requires/purchase_requisition_v2_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(type==11)
		{
			var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remark').val()+'*'+type+'*'+''+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_11", "requires/purchase_requisition_v2_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(type==12)
		{

			var show_item="";
			var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remark').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_4_akh", "requires/purchase_requisition_v2_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(type==13)
		{
			var show_item="";
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remark').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_13", "requires/purchase_requisition_v2_controller" ) ;
			show_msg("3");
		}
		else if(type==14)
		{
			var show_item="";
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remark').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_14", "requires/purchase_requisition_v2_controller" ) ;
			show_msg("3");
		}
		else if(type==15)
		{
			var show_item="";
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remark').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_15", "requires/purchase_requisition_v2_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(type==16)
		{
			var show_item="";
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remark').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_16", "requires/purchase_requisition_v2_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(type==17)
		{
			var show_item="";
			var r=confirm("Press \"OK\" Show With Model / Article, Size/MSR, Brand \nPress \"Cancel\" Show Without Model / Article, Size/MSR, Brand");
			if(r==true){ show_item=1; }else{ show_item=0; }
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remark').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_category_wise_print", "requires/purchase_requisition_v2_controller" ) ;
			show_msg("3");
		}
		else if(type==18)
		{
			var show_item="";
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remark').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_18", "requires/purchase_requisition_v2_controller" ) ;
			show_msg("3");
		}
		else if(type==19)
		{
			var show_item="";
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remark').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_19", "requires/purchase_requisition_v2_controller" ) ;
			show_msg("3");
		}
		else if(type==20)
		{
			var show_item="";
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remark').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_20", "requires/purchase_requisition_v2_controller" ) ;
			show_msg("3");
		}
		else if(type==21)
		{
			var show_item="";
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remark').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_21", "requires/purchase_requisition_v2_controller" ) ;
			show_msg("3");
		}
		else if(type==22)
		{
			var show_item="";
			var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remark').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_22", "requires/purchase_requisition_v2_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(type==23)
		{
			var show_item="";
			var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remark').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_23", "requires/purchase_requisition_v2_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(type==24)
		{
			var show_item="";
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remark').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_24", "requires/purchase_requisition_v2_controller" ) ;
			show_msg("3");
		}
		else if(type==25)
		{
			var show_item="";
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remark').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_25", "requires/purchase_requisition_v2_controller" );
			show_msg("3");
		}
		else if(type==27)
		{
			var show_item="";
			var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remark').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_27", "requires/purchase_requisition_v2_controller" ) ;
			show_msg("3");
		}
		else
		{	
			 var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#is_approved').val()+'*'+''+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print", "requires/purchase_requisition_v2_controller" ) ;
			//return;
			show_msg("3");

		}
	}

	function openmypage_unapprove_request()
	{
		if (form_validation('txt_requisition_no','Req. Number')==false)
		{
			return;
		}
		
		var txt_requisition_no=document.getElementById('txt_requisition_no').value;
		var txt_un_appv_request=document.getElementById('txt_un_appv_request').value;
		
		var data=txt_requisition_no+"_"+txt_un_appv_request;
		
		var title = 'Un Approval Request';	
		var page_link = 'requires/purchase_requisition_v2_controller.php?data='+data+'&action=unapp_request_popup';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
			var unappv_request=this.contentDoc.getElementById("hidden_appv_cause");
			
			$('#txt_un_appv_request').val(unappv_request.value);
		}
	}

	function openmypage_not_approve_cause()
	{
		if (form_validation('txt_requisition_no','Req. Number')==false)
		{
			return;
		}
		
		var txt_not_approve_cause=document.getElementById('txt_not_approve_cause').value;
		
		
		var data=txt_not_approve_cause;
		
		var title = 'Not Appv. Cause';	
		var page_link = 'requires/purchase_requisition_v2_controller.php?data='+data+'&action=not_approve_cause_popup';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=250px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
			
		}
	}
	function fnc_justification(type){
		if(type==1 || type==2){
			document.getElementById("justification").classList.remove("formbutton");
			document.getElementById("justification").classList.add("formbutton_disabled");
			$('#justification_value').val('');
		}
		if(type==3 || type==4){
			document.getElementById("justification").classList.remove("formbutton_disabled");
			document.getElementById("justification").classList.add("formbutton");
		}
	}

	function fnc_justification_add(page_link,title)
	{
		var txt_justification_value=document.getElementById('justification_value').value
		page_link=page_link+'&txt_justification_value='+txt_justification_value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=150px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("txt_justification_value");
			if (theemail.value!="")
			{
				document.getElementById('justification_value').value=theemail.value;
			}
		}
	}
	
	
	
	function call_print_button_for_mail(mail_address,mail_body){
			if(document.getElementById('cbo_ready_to_approved').value==1){
			 var returnValue=return_global_ajax_value(document.getElementById('update_id').value+'__'+mail_address+'__'+mail_body, 'pending_purchase_requisition_for_approval', '', '../auto_mail/pending_purchase_requisition_for_approval_mail_notification');
			}
			else{alert('Please Select Ready To Approved Yes')}
			
	}

	function fn_reset_form(){
		reset_form('purchaserequisition_2','approved','','','$(\'#tbl_purchase_item tbody tr:not(:first)\').remove();');
		$("#itemaccount_1").attr("disabled", false);

	}
	
	function fn_add_row()
	{
		var i=$('#tbl_purchase_item tbody tr').length;
		var item_description=$('#itemdescription_'+i).val();
		if (item_description=="" || item_description==null)
		{
			return false;
		}
		else
		{
			i++;
			$("#tbl_purchase_item tbody tr:last").clone().find("input,select").each(function(){
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function (_, name) {var name=name.split("_"); return name[0] +"_"+ i},
					'value': function (_, value) {return ""}
				});    
			}).end().appendTo("#tbl_purchase_item");
			
			
			$('#cboItemCategory_'+i).val(114);
			$('#txtuom_'+i).val(94);
			$('#cbostatus_'+i).val(1);
			$('#itemdescription_'+i).removeAttr("readonly");
			$('#cboServiceFor_'+i).attr("disabled",false);
			set_all_onclick();
			return;
		}
	}
	
	function fn_acc_req_cap(str)
	{
		//alert(str);
		if(str==4)
		{
			$("#item_source_caption").html("Item Account :");
		}
		else
		{
			$("#item_source_caption").html("Item Issue Req :");
		}
	}
	
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
 	<?  echo load_freeze_divs ("../",$permission);  ?>
    <fieldset style="width:1320px;height:auto;">
		<legend>Purchase Requisition</legend> 
		<form name="purchaserequisition_1" id="purchaserequisition_1" autocomplete="off"> 
			<table cellpadding="0" cellspacing="2" width="100%">
				<tr>
					<td colspan="10" align="center" ><b>Requisition No</b>
					<input name="txt_requisition_no"  id="txt_requisition_no" placeholder="Double Click to Search" onDblClick="openmypage_requisition();" readonly  style="width:148px "  class="text_boxes"/>
					</td>
				</tr>
				<tr><td height="15"></td></tr>
				<tr>
					<td width="90" class="must_entry_caption">Company</td>
					<td width="150">
						<input type="hidden" name="update_id" id="update_id" value="">
						<input type="hidden" name="is_approved" id="is_approved" value="">
						<input type="hidden" name="budge_validation" id="budge_validation" value="0">
						<? 
							echo create_drop_down( "cbo_company_name", 140,"select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/purchase_requisition_v2_controller', this.value, 'load_drop_down_location','location_td');load_drop_down( 'requires/purchase_requisition_v2_controller', this.value, 'load_drop_down_division','division_td');load_drop_down( 'requires/purchase_requisition_v2_controller',this.value+'_'+document.getElementById('cbo_location_name').value, 'load_drop_down_stor','stor_td');get_php_form_data( this.value, 'company_wise_report_button_setting','requires/purchase_requisition_v2_controller' );" );
						?>   	
					</td>
					<td width="90" class="must_entry_caption">Req. Date</td>
					<td>
						<input type="text" name="txt_date_from"  style="width:148px"  id="txt_date_from" class="datepicker" value="<? echo date("d-m-Y");?>" />
					</td>
					<td width="90">Delivery Date</td>
					<td>
						<input type="text" name="txt_date_delivery"  style="width:150px"  id="txt_date_delivery" class="datepicker" value="" />
					</td>
					<td width="90">ISO No.</td>
					<td>
						<input type="text" name="txt_iso_no"  id="txt_iso_no" style="width:148px " class="text_boxes_numeric" />
					</td>
					<td width="90">Not Appv. Cause</td>
					<td width="140" align="">
						<Input name="txt_not_approve_cause" class="text_boxes" readonly placeholder="Double Click for Browse" id="txt_not_approve_cause" onClick="openmypage_not_approve_cause()" >
					</td>
				</tr>
				<tr>
					<td class="must_entry_caption">Location</td>
					<td id="location_td" width="150">
						<? 
							echo create_drop_down( "cbo_location_name", 140,$blank_array,"", 1, "-- Select --", $selected, "load_drop_down( 'requires/purchase_requisition_v2_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_stor','stor_td');" );
						?> 	
					</td>
					<td class="must_entry_caption">Store Name</td>
					<td id="stor_td">
						<? 
							echo create_drop_down( "cbo_store_name", 160,$blank_array,"", 1, "-- Select --", $selected, "" );
						?> 
					</td>
					<td>Req.By</td>
					<td>
						<input type="text" name="txt_req_by"  id="txt_req_by" style="width:150px " value="" class="text_boxes" maxlength="50" title="Maximum 50 Character"/>
					</td>
					<td>Ready To Approved</td>
					<td><? echo create_drop_down( "cbo_ready_to_approved", 160, $yes_no,"", 1, "-- Select--", 2, "","","" );?> </td>
					<td>Tenor</td>
					<td width="140"><input type="text" class="text_boxes_numeric" name="txt_tenor" id="txt_tenor" /></td>
				</tr>
				<tr>
					<td>For Division</td>
					<td id="division_td" width="150">
					<? 
						echo create_drop_down( "cbo_division_name", 140,$blank_array,"", 1, "-- Select --", $selected, "" );
					?> 	
					</td>
					<td>Pay Mode</td>
					<td>
						<? 
							echo create_drop_down( "cbo_pay_mode", 160,$pay_mode,"", 1, "-- Select --", $selected, "","" );
						?> 
					</td>
					<td>Manual Req. No.</td>
					<td>
						<input type="text" name="txt_manual_req"  id="txt_manual_req" style="width:150px " class="text_boxes" onDblClick="openmypage_manual_requisition();" placeholder="Browse Or Write" />
					</td>
					<td align="">Un-approve request</td> 
					<td align="">
						<Input name="txt_un_appv_request" class="text_boxes" readonly placeholder="Double Click for Browse" ID="txt_un_appv_request" style="width:148px"  onClick="openmypage_unapprove_request()" disabled="disabled">
					</td>
					<td>&nbsp;</td>
					<td>
						<input type="button" id="justification" class="formbutton_disabled" style="width:140px;" value="Justification" onClick="fnc_justification_add('requires/purchase_requisition_v2_controller.php?action=rmg_process_loss_popup','justification')" />
						<!-- <input type="button" id="set_button" class="image_uploader" style="width:140px;" value="Process Loss %" onClick="open_rmg_process_loss_popup('requires/fabric_booking_urmi_controller.php?action=rmg_process_loss_popup','Process Loss %')" /> -->
						<input type="hidden" name="justification_value" id="justification_value">
					</td> 
				</tr>
				<tr>
					<td>For Department</td>
					<td width="150" id="department_td">
					<? 
						echo create_drop_down( "cbo_department_name", 140,$blank_array,"", 1, "-- Select --", $selected, "" );
					?> 	
					</td>
					<td>Source</td>
					<td id="mode_td">
						<? 
							echo create_drop_down( "cbo_source", 160, $source,"", 0, "", 3, "","" );
						?> 
					</td>
					<td>Remarks</td>
					<td>
						<input type="text" name="txt_remark"  id="txt_remark" style="width:150px " value="" class="text_boxes" maxlength="800" title="Maximum 800 Character"/>
					</td>
					<td>Priority</td>
					<td>
						<? echo create_drop_down( "cbo_priority_id",160,$priority_array,'',0,'--Select--',0,"",0);?>
					</td>
					<td>&nbsp;</td>
					<td width="140" height="10">
						<?
						include("../terms_condition/terms_condition.php");
						terms_condition(69,'txt_requisition_no','../');
						?>
					</td>
				</tr>
				<tr>
					<td>For Section</td>
					<td width="150" id="section_td">
						<? 
							echo create_drop_down( "cbo_section_name", 140,$blank_array,"", 1, "-- Select --", $selected, "" );
						?> 	
					</td>
					<td>Currency</td>
					<td> 
						<?
							echo create_drop_down( "cbo_currency_name", 160,$currency,"", 1, "-- Select --", 1, "" );
						?> 
					</td>
					<td>Reference</td>
					<td>
						<input type="text" name="txt_reference"  id="txt_reference" style="width:150px " value="" class="text_boxes"/>
					</td>
					<td>Type of Requisition</td>
					<td>
						<? echo create_drop_down( "cbo_requisition_id",160,$requisition_array,'',0,'--Select--',0,"onChange=fnc_justification(this.value)",0);?>
					</td>
					<td>Add Image</td>
					<td >
						<input type="button" class="image_uploader" style="width:140px" value="ADD/VIEW IMAGE" onClick="file_uploader ( '../', document.getElementById('update_id').value,'', 'purchase_requisition', 0 ,1)"> 
					</td>
				</tr>
				<tr>
                	<td>Basis</td>
					<td> 
						<?
							echo create_drop_down( "cbo_req_basis", 140, $receive_basis_arr,"", 0, "-- Select --", 4, "fn_acc_req_cap(this.value)","","4,7" );
						?> 
					</td>
					<td colspan="6">&nbsp;</td>
					<td >Add File</td>
					<td >
						<input type="button" class="image_uploader" style="width:140px" value="CLICK TO ADD FILE" onClick="file_uploader ( '../', document.getElementById('update_id').value,'', 'purchase_requisition', 2 ,1)"> 
					</td>
				</tr>
				<tr>
					<td colspan="10" align="center" class="button_container">
						<div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
						
						<? echo create_drop_down( "cbo_template_id", 100, $report_template_list,'', 0, '', 0, "");?>&nbsp;	
						<?
							echo load_submit_buttons($permission, "fnc_purchase_requisition", 0,0,"reset_form('purchaserequisition_1','','','','disable_enable_fields(\'cbo_company_name\');$(\'#tbl_purchase_item tbody tr:not(:first)\').remove();')",1);//item_category_div*purchase_requisition_list_view_dtls*approved
						?>
						<input type="button" name="searchnew" id="searchnew" value="Print 14" onClick="generate_report(14)" style="width:100px;display:none;" class="formbuttonplasminus" />
						<input type="button" name="search16" id="search16" value="Print 15" onClick="generate_report(18)" style="width:100px;display:none;" class="formbuttonplasminus" />

						<input type="button" name="search17" id="search17" value="Print 16" onClick="generate_report(19)" style="width:100px;display:none;" class="formbuttonplasminus" />
						<input type="button" name="search18" id="search18" value="Print 17" onClick="generate_report(20)" style="width:100px;display:none;" class="formbuttonplasminus" />
						<input type="button" name="search19" id="search19" value="Print 18" onClick="generate_report(21)" style="width:100px;display:none;" class="formbuttonplasminus" />
						<input type="button" name="search20" id="search20" value="Print 19" onClick="generate_report(22)" style="width:100px;display:none;" class="formbuttonplasminus" />
						<input type="button" name="search24" id="search24" value="Print 20" onClick="generate_report(25)" style="width:100px;display:none;" class="formbuttonplasminus" />
						<input type="button" name="search" id="search1" value="With Group" onClick="generate_report(1)" style="width:100px;display:none;" class="formbuttonplasminus" />
						<input type="button" name="search" id="search2" value="WithOut group" onClick="generate_report(2)" style="width:100px;display:none;" class="formbuttonplasminus" />
						<input type="button" name="search" id="search3" value="Print Report" onClick="generate_report(3)" style="width:100px;display:none;" class="formbuttonplasminus" />
						<input type="button" name="search" id="search4" value="Print Report 2" onClick="generate_report(4)" style="width:100px;display:none;" class="formbuttonplasminus" />
						<input type="button" name="search11" id="search11" value="Print Report 11" onClick="generate_report(11)" style="width:100px;display:none;" class="formbuttonplasminus" />
						<input type="button" name="search" id="search5" value="Print Report 3" onClick="generate_report(5)" style="width:100px;display:none;" class="formbuttonplasminus" />

						<input type="button" name="search" id="search6" value="Print Report 4" onClick="generate_report(6)" style="width:100px;display:none;" class="formbuttonplasminus" />
						<input type="button" name="search" id="search12" value="Print Report 5" onClick="generate_report(12)" style="width:100px;display:none;" class="formbuttonplasminus" />
						<input type="button" name="search" id="search7" value="Print 5" onClick="generate_report(7)" style="width:100px;display:none;" class="formbuttonplasminus" />

						<input type="button" name="search" id="search8" value="Print Report 6" onClick="generate_report(8)" style="width:100px;display:none;" class="formbuttonplasminus" />

						<input type="button" name="search" id="search25" value="Print 21" onClick="generate_report(26)" style="width:100px;display:none;" class="formbuttonplasminus" />

						<input type="button" name="search" id="search9" value="Print Report 7" onClick="generate_report(9)" style="width:100px;display:none;" class="formbuttonplasminus" />
						<input type="button" name="search" id="search10" value="Print Report 8" onClick="generate_report(10)" style="width:100px;display:none;" class="formbuttonplasminus" /> 
						<input type="button" name="search" id="search21" value="Print 9" onClick="generate_report(24)" style="width:100px;display:none;" class="formbuttonplasminus" /> 
						
						
						
						<input type="button" name="search" id="search13" value="Print Report 13" onClick="generate_report(13)" style="width:100px;display:none;" class="formbuttonplasminus" />
						<input type="button" name="search" id="search14" value="Re-Order Level" onClick="generate_report(15)" style="width:100px;display:none;" class="formbuttonplasminus" /> 
						<input type="button" name="search" id="search15" value="Item wise" onClick="generate_report(16)" style="width:100px;display:none;" class="formbuttonplasminus" />  
						<input type="button" name="search_category_wise" id="search_category_wise" value="Category Wise" onClick="generate_report(17)" style="width:100px;display:none;" class="formbuttonplasminus" />
						<input type="button" name="search23" id="search23" value="Print Out5" onClick="generate_report(23)" style="width:100px;" class="formbuttonplasminus" />
						<input type="button" name="search" id="search26" value="Print 22" onClick="generate_report(27)" style="width:100px;display:none;" class="formbuttonplasminus" />
						<input type="button" value="Mail Send" onClick="fnSendMail('../','update_id',1,1,0,0);"  class="formbuttonplasminus" style="width:80px;">     
					</td>		
				</tr>
			</table>
		</form>
  	</fieldset>
    <fieldset style="width:1550px; margin-top:10px;">
    	<table class="rpt_table" width="100%" cellspacing="1">
        	<tr>
            	<th width="50%" align="right" id="item_source_caption" style="font-size:18px; font-weight:bold;">Item Account : </th>
            	<th align="left">
                <input type="text" name="itemaccount_1" id="itemaccount_1" class="text_boxes" value="" style="width:120PX;" maxlength="200" placeholder="Double click"  onDblClick="openmypage()" readonly />
                <input type="hidden" name="update_id" id="update_id" />
                <input type="hidden" name="hidden_update_id" id="hidden_update_id" /> <!-- for update --> 
                <input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes_numeric"  readonly= "readonly" value="0" />
                &nbsp;
                <span>
                <input type="button" id="btnplus" name="btnplus" value="Add Service" class="formbuttonplasminus" style="width:130px;" onClick="fn_add_row();" />
                </span>
                </th>
            </tr>
        </table>
        <legend>Purchase Requisition Details</legend>
        <form name="purchaserequisition_2" id="purchaserequisition_2" autocomplete="off">
        	<table class="rpt_table" cellspacing="1" width="1550" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="90">Item Category</th>
                        <th width="85">Item Group</th>
                        <th width="70">Item Sub. Group</th>
                        <th class="must_entry_caption" width="130">Item Description</th>
                        <th width="70">Item Size</th>
                        <th width="100">Item Number</th>
                        <th width="80" style="display:none">Required For</th>
                        <th width="65">Order UOM</th>
                        <th class="must_entry_caption" title="Must Entry Field." width="65"> <font color="blue">Quantity</font></th>
                        <th width="65" id="req_rate_caption">Rate</th>
                        <th width="65">Amount</th>
                        <th width="65">Stock</th>
                        <th width="65">Re-Order Level</th>
                        <th width="80">Vehicle No</th>
                        <th width="90">Remarks</th>
                        <th width="65">Status</th>
                        <th width="70" style="display:none">Used For</th>
                        <th width="70">Brand</th>
                        <th width="70">Model</th>
                        <th width="70">Origin</th>
                        <th width="70">Delivery Date</th>
                        <th width="70">Item Issue Req/Dem No</th>
						<th width="70">Service For</th>
                    </tr>
                </thead>
               
          </table>
       		<div id="item_category_div" style="max-height:200px; overflow-y:scroll;" width="1550">
            <table class="rpt_table" width="1530" cellspacing="1" id="tbl_purchase_item" border="1" rules="all">
                <tbody>
                    <tr class="general" >
                        <td width="90">
							<?
                                //function create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name, $additionalClass, $additionalAttributes ) 
								echo create_drop_down( "cboItemCategory_1", 90,$item_category,"", 1, "-- Select --", $selected, "",1,"","","","1,2,3,12,13,14,24,25,35");
                             ?> 
                        </td>
                        <td width="85">
                        <input type="text" name="txtitemgroupid_1" id="txtitemgroupid_1" class="text_boxes" value="" style="width:72px;" maxlength="200" readonly/>
                        <input type="hidden" name="hiddenitemgroupid_1" id="hiddenitemgroupid_1"/>
						<input type="hidden" name="hiddenissreqdtlsid_1" id="hiddenissreqdtlsid_1" />
                        </td>
                        <td width="70">
                            <input type="text" name="subgroup_1" id="subgroup_1" class="text_boxes" value="" style="width:60px;" maxlength="200" readonly />
                        </td>
                        <td width="130">
                            <input type="text" name="itemdescription_1" id="itemdescription_1" class="text_boxes" value="" style="width:115px;" maxlength="200" readonly />
							<input type="hidden" name="item_1" id="item_1" value="" />
                        </td>
                        <td width="70" id="group_td">
                            <input type="hidden" name="hiddenid_1" id="hiddenid_1" />
                            <input type="text" name="itemsize_1" id="itemsize_1" class="text_boxes" value="" style="width:55px;" maxlength="200" readonly />
                        </td>
						<td width="100">
                            <input type="text" name="itemnumber_1" id="itemnumber_1" class="text_boxes" value="" style="width:80px;" maxlength="200" readonly />
                        </td>
                        <td style="display:none">
                            <?
                                echo create_drop_down( "txtreqfor_1", 80, $use_for,'', 1, '-- Select --',0,'',0,''); 
                            ?>
                        </td> 
                        <td width="65">
                        	<?
                                echo create_drop_down( "txtuom_1", 60, $unit_of_measurement,'', 1, '-- Select --',$selected,'',1,''); 
                            ?>
                        </td> 
                        <td width="65">
                            <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" autocomplete="off" value="" style="width:50px;" onKeyUp="calculate_val()"/>
                        </td>
                        <td width="65">
                            <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" autocomplete="off" value="" style="width:50px;" onKeyUp="calculate_val()" />
                        </td>
                        <td width="65">
                            <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" autocomplete="off" value="" style="width:50px; text-align:right;" readonly />
                        </td>
                        <td width="65">
                            <input type="text" name="stock_1" id="stock_1" class="text_boxes_numeric" value="" style="width:50px;" maxlength="200" readonly />
                        </td>
                        <td width="65">
                            <input type="text" name="reorderlable_1" id="reorderlable_1" class="text_boxes_numeric" value="" style="width:50px;" maxlength="200" readonly />
                        </td>
                        <td width="80">
                            <input type="text" name="txtvehicle_1" id="txtvehicle_1" class="text_boxes" value="" style="width:70px;" />
                        </td>
                        <td width="90">
                            <input type="text" name="txtremarks_1" id="txtremarks_1" class="text_boxes" value="" style="width:80px;" />
                        </td>
                        <td width="65">
                            <? echo create_drop_down( "cbostatus_1", 60, $row_status,'', 0, '',1,0); ?> 
                        </td>
            
                          <td width="70" style="display:none"><Input type="text" name="txtused_1" id="txtused_1"  style="width:60px" class="text_boxes" autocomplete="off" /></td>
                          <td width="70"><Input type="text" name="txtbrand_1" ID="txtbrand_1"  style="width:55px" class="text_boxes" autocomplete="off" /></td>
            
                          <td width="70"><Input type="text" name="txtmodelname_1" ID="txtmodelname_1"  style="width:55px" class="text_boxes" autocomplete="off" /></td>
            
                          <td width="70"><? //new
                          echo create_drop_down( "cboOrigin_1", 65, "select country_name,id from lib_country comp where is_deleted=0  and status_active=1 order by country_name",'id,country_name', 1, '--- Select Country ---', 0 );            
                          ?></td>
                          
                           <td width="70"><input type="text" name="txtdatedelivery_1"  style="width:50px"  id="txtdatedelivery_1" class="datepicker" value="" /></td>

						   <td width="70">
                            <input type="text" name="txtIndent_1" id="txtIndent_1" class="text_boxes" disabled value="" style="width:50px;" />
							<input type="hidden" name = "mstId_1" id="mstId_1" value="">
                        </td>
						<td width="70">
							<?								    
								echo create_drop_down( "cboServiceFor_1", 70, $service_for_arr, "", 1, "-- Select --",$selected, 0, 1, "", "", "", "", "", "", "cboServiceFor[]", "cboServiceFor_1" );							   	
							?>
						</td>
                    </tr>
                </tbody>
                </table>

                <table width="100%">
                	<tr>
                        <td colspan="20" height="20" valign="middle" align="center" class="button_container"> 
                            <?
                                echo load_submit_buttons( $permission, "fnc_purchase_requisition_dtls", 0,0 ,"fn_reset_form()",2);		
                            ?>
                        </td>    
                    </tr>
                </table>
            </div>
        </form>
    </fieldset>
    
    <fieldset style="width:1100px; margin-top:10px;">
            <legend>Purchase Requisition List</legend>
            <div id="purchase_requisition_list_view_dtls" overflow:auto;> </div>
   </fieldset>
</div>
</body> 
<script type="text/javascript">
    $(document).ready(function () {
		$("#cbo_division_name").val(0);
		
    });


 

</script>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>  
</html>

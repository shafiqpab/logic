<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create for PI entry
Functionality	:	
JS Functions	:
Created by		:	 
Creation date 	: 	
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
echo load_html_head_contents("Pro Forma Invoice", "../../", 1, 1,'','',''); 
?> 	

<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	
	var permission='<? echo $permission; ?>';
	var str_color = [<? echo substr(return_library_autocomplete( "select distinct(color_name) from lib_color", "color_name"  ), 0, -1); ?>];
	var str_size = [<? echo substr(return_library_autocomplete( "select distinct(size_name) from lib_size", "size_name"  ), 0, -1); ?>];
	var str_composition = [<? echo substr(return_library_autocomplete( "select distinct(fabric_composition) from com_pi_item_details", "fabric_composition"  ), 0, -1); ?>];
	var str_construction = [<? echo substr(return_library_autocomplete( "select distinct(fabric_construction) from com_pi_item_details", "fabric_construction"  ), 0, -1); ?>];
	var str_dia_width = [<? echo substr(return_library_autocomplete( "select distinct(dia_width) from com_pi_item_details", "dia_width"  ), 0, -1); ?>];
	
	$(document).ready(function(){
		//var cbo_item_category_id=$('#cbo_item_category_id').val();
		load_drop_down( 'requires/pi_dyes_chemical_controller',document.getElementById('cbo_importer_id').value, 'load_supplier_dropdown', 'supplier_td' );
		show_list_view(document.getElementById('cbo_pi_basis_id').value+'_'+''+'_'+'1', 'pi_details', 'pi_details_container', 'requires/pi_dyes_chemical_controller', '' ) ;
	})

	function fnc_pi_mst( operation )
	{
		
		//var cbo_item_category_id=$('#cbo_item_category_id').val();
		var cbo_goods_rcv_status=$('#cbo_goods_rcv_status').val();
		//alert(cbo_item_category_id +"="+ cbo_goods_rcv_status);return;
		
		if(operation==4)
		{ 
			 print_report( $('#cbo_importer_id').val()+'*'+$('#update_id').val()+'*'+'227', "print", "requires/pi_print_urmi" );
			 return;
		}
		 
		var is_approved=$('#is_approved').val();//Chech The Approval requisition item.. Change not allowed
				
		if(is_approved==1)
		{
			alert("PI is Approved. So Change Not Allowed");
			return;	
		}
		 
		if (form_validation('cbo_importer_id*cbo_supplier_id*pi_number*pi_date*cbo_currency_id*cbo_source_id*cbo_pi_basis_id*cbo_goods_rcv_status','*Importer*Supplier*Pi Number*Pi Date*Currency*Source*Pi Basis*Goods Rcv Status')==false)
		{
			return;
		}
		else
		{ 
			var export_pi_id=$("#export_pi_id").val();
			if(export_pi_id>0)
			{
				var row_num=$('#tbl_pi_item tbody tr').length;
				var data_all="";
				
				for (var j=1; j<=row_num; j++)
				{
					data_all+="&workOrderNo_" + j + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + j + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + j + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&construction_" + j + "='" + $('#construction_'+j).val()+"'"+"&composition_" + j + "='" + $('#composition_'+j).val()+"'"+"&colorId_" + j + "='" + $('#colorId_'+j).val()+"'"+"&gsm_" + j + "='" + $('#gsm_'+j).val()+"'"+"&diawidth_" + j + "='" + $('#diawidth_'+j).val()+"'"+"&uom_" + j + "='" + $('#uom_'+j).val()+"'"+"&quantity_" + j + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + j + "='" + $('#rate_'+j).val()+"'"+"&amount_" + j + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + j + "='" + $('#updateIdDtls_'+j).val()+"'"+"&hideDeterminationId_" + j + "='" + $('#hideDeterminationId_'+j).val()+"'";
				}
				
				if(data_all=="")
				{
					alert("No Item");
					return;
				}
				
				var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+get_submitted_data_string('cbo_importer_id*cbo_supplier_id*pi_number*pi_date*last_shipment_date*pi_validity_date*cbo_currency_id*cbo_source_id*hs_code*txt_internal_file_no*intendor_name*cbo_pi_basis_id*txt_remarks*hide_approved_status*update_id*cbo_goods_rcv_status*export_pi_id*within_group*txt_total_amount*txt_upcharge*txt_discount*txt_total_amount_net*cbo_ready_to_approved*txt_lc_group_no*hiddn_user_id',"../../")+data_all;
			}
			else
			{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_importer_id*cbo_supplier_id*pi_number*pi_date*last_shipment_date*pi_validity_date*cbo_currency_id*cbo_source_id*hs_code*txt_internal_file_no*intendor_name*cbo_pi_basis_id*txt_remarks*hide_approved_status*update_id*cbo_goods_rcv_status*export_pi_id*within_group*txt_total_amount*txt_upcharge*txt_discount*txt_total_amount_net*cbo_ready_to_approved*txt_lc_group_no*hiddn_user_id',"../../");	
			}
			
			freeze_window(operation);
			http.open("POST","requires/pi_dyes_chemical_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_pi_mst_reponse;
		}
	}
	
	function fnc_pi_mst_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			
			show_msg(trim(reponse[0]));
			
			if((reponse[0]==0 || reponse[0]==1))
			{
				document.getElementById('txt_system_id').value = reponse[1];
				document.getElementById('update_id').value = reponse[1];
				set_button_status(1, permission, 'fnc_pi_mst',1);
			
				//$('#cbo_item_category_id').attr('disabled','true');
				$('#cbo_importer_id').attr('disabled','true');
				$('#cbo_supplier_id').attr('disabled','true');
				$('#cbo_pi_basis_id').attr('disabled','true');
				$('#cbo_goods_rcv_status').attr('disabled','true');
			}
			else if(reponse[0]==2)
			{
				reset_form('pimasterform_1','pi_details_container','','cbo_currency_id,2*cbo_pi_basis_id,2',"disable_enable_fields('cbo_item_category_id*cbo_pi_basis_id',0)");
			}
			else if(reponse[0]==14)
			{
				alert(reponse[1]);
			}
			else if(reponse[0]==16)
			{
				alert("This PI is already Approved. So You can not change/delete it.");
			}		
			release_freezing();
		}
	}
	
	function add_auto_complete(i)
	{
		 $("#colorName_"+i).autocomplete({
			 source: str_color
		  });
		  $("#itemColor_"+i).autocomplete({
			 source: str_color
			});
		  $("#sizeName_"+i).autocomplete({
			 source: str_size
		  });
		  $("#composition_"+i).autocomplete({
			 source: str_composition
		  });
		  $("#construction_"+i).autocomplete({
			 source: str_construction
		  });
		  $("#diawidth_"+i).autocomplete({
			 source: str_dia_width
		  });
	}
	
	function add_break_down_tr( i )
	{ 
		var row_num=$('#tbl_pi_item tbody tr').length;
		if (row_num!=i)
		{
			return false;
		}
		else
		{ 
			i++;
	
			$("#tbl_pi_item tbody tr:last").clone().find("input,select").each(function(){
				  
			$(this).attr({ 
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
			  'value': function(_, value) { return "" }              
			});
			 
			}).end().appendTo("#tbl_pi_item");
				
			$("#tbl_pi_item tbody tr:last").removeAttr('id').attr('id','row_'+i);
			
			
			$('#rate_'+i).removeAttr("onKeyUp").attr("onKeyUp","calculate_amount("+i+")");
			$('#quantity_'+i).removeAttr("onKeyUp").attr("onKeyUp","calculate_amount("+i+")");
			$('#itemdescription_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_item_desc("+i+")");
			
			$('#construction_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_fabricDescription("+i+")");
			if(category==2) {$('#uom_'+i).val(0);}
			if(category==13) {$('#uom_'+i).val(12);}
			
			$('#increase_'+i).removeAttr("value").attr("value","+");
			$('#decrease_'+i).removeAttr("value").attr("value","-");
			$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
			   
			add_auto_complete(i);
			set_all_onclick();
		}
	}
	
	function fn_deleteRow(rowNo) 
	{ 
		var numRow = $('table#tbl_pi_item tbody tr').length; 
		if(numRow==rowNo && rowNo!=1)
		{
			var updateIdDtls=$('#updateIdDtls_'+rowNo).val();
			var txt_deleted_id=$('#txt_deleted_id').val();
			var selected_id='';
		
			if(updateIdDtls!='')
			{
				if(txt_deleted_id=='') selected_id=updateIdDtls; else selected_id=txt_deleted_id+','+updateIdDtls;
				$('#txt_deleted_id').val( selected_id );
			}
	
			$('#tbl_pi_item tbody tr:last').remove();
			calculate_total_amount(1);
		}
		else
		{
			return false;
		}
	}
	
	function check_amount(i)
	{
		var pi_basis_id = $('#cbo_pi_basis_id').val();
		if(pi_basis_id==1)
		{
			var bl_amnt=$('#amount_'+i).attr('placeholder')*1;
			var pi_amnt=$('#amount_'+i).val()*1;
			if(pi_amnt>bl_amnt)
			{
				alert("Amount Exceeds WO Balance Amount.");
				$('#amount_'+i).val('');
			}
		}
		calculate_total_amount(1);
	}
	
	function calculate_amount(i)
	{
		var pi_basis_id = $('#cbo_pi_basis_id').val();
		if(pi_basis_id==1)
		{
			var bl_qty=$('#quantity_'+i).attr('placeholder')*1;
			var pi_qty=$('#quantity_'+i).val()*1;
			if(pi_qty>bl_qty)
			{
				alert("Quantity Exceeds WO Balance Qty.");
				$('#quantity_'+i).val('');
			}
		}
		
		var ddd={ dec_type:5, comma:0, currency:''}
		math_operation( 'amount_'+i, 'quantity_'+i+'*rate_'+i, '*','',ddd);
		calculate_total_amount(1);
	}
	
	function calculate_total_amount(type)
	{
		if(type==1)
		{
			var ddd={ dec_type:5, comma:0, currency:''}
			var numRow = $('table#tbl_pi_item tbody tr').length; 
			//alert(numRow);
			math_operation( "txt_total_amount", "amount_", "+", numRow,ddd );
		}
		
		var txt_total_amount=$('#txt_total_amount').val();
		var txt_upcharge=$('#txt_upcharge').val();
		var txt_discount=$('#txt_discount').val();
		
		var net_tot_amnt=txt_total_amount*1+txt_upcharge*1-txt_discount*1;
		$('#txt_total_amount_net').val(net_tot_amnt.toFixed(4));
	}
	
	function fnCheckUnCheckAll(checkVal)
	{
		for (Looper=0; Looper < document.pimasterform_2.length ; Looper++ )
		{
			var strType = document.pimasterform_2.elements[Looper].type;
			if (strType=="checkbox")
			{
				document.pimasterform_2.elements[Looper].checked=checkVal;
			}   
		}
	}
	
	function fnc_pi_item_details( operation )
	{
		
		//var cbo_item_category_id = $('#cbo_item_category_id').val();
		var cbo_pi_basis_id = $('#cbo_pi_basis_id').val();
		var update_id = $('#update_id').val();
		var txt_upcharge = $('#txt_upcharge').val();
		var txt_discount = $('#txt_discount').val();


		var txt_deleted_id = $('#txt_deleted_id').val();
		var cbo_currency_id = $('#cbo_currency_id').val();
		var cbo_goods_rcv_status = $('#cbo_goods_rcv_status').val();
		var txt_order_type = $('#txt_order_type').val();
		
		//var txt_total_amount = $('#txt_total_amount').val();
		//var txt_total_amount_net = $('#txt_total_amount_net').val();
	
		var txt_total_amount=0; var txt_total_amount_net=0;
		
		if(update_id=='')
		{
			alert('Please Save PI First');
			return false;
		}
		
		if(operation==2 && cbo_pi_basis_id==2)
		{
			show_msg('13');
			return false;
		}
		
		var row_num=$('#tbl_pi_item tbody tr').length;
		var data_all=""; var i=0; var selected_row=0;
		var data_all_checked="";
		if(operation==2 && cbo_pi_basis_id==1)
		{
			
			txt_deleted_id='';
			for (var j=1; j<=row_num; j++)
			{
				var updateIdDtls=$('#updateIdDtls_'+j).val();
				
				if($('#workOrderChkbox_'+j).is(':checked') && updateIdDtls!="")
				{
					selected_row++;
					if(txt_deleted_id=="") txt_deleted_id=updateIdDtls; else txt_deleted_id+=","+updateIdDtls;
				}
				if(updateIdDtls!="" && $('#workOrderChkbox_'+j).is(':not(:checked)')) 
				{
					i++;
					
					data_all+="&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'";
					
				
					//alert(data_all);		
					txt_total_amount+=$('#amount_'+j).val()*1;
				}
				
				// is checked
				if(updateIdDtls!="" && $('#workOrderChkbox_'+j).is(':checked')) 
				{
					i++;
					data_all_checked+="&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'";
					//txt_total_amount+=$('#amount_'+j).val()*1;
				}
				
				
			}
		}
		else
		{
		
			if(cbo_pi_basis_id==1)
			{
				for (var j=1; j<=row_num; j++)
				{
					var updateIdDtls=$('#updateIdDtls_'+j).val();
					
					if($('#workOrderChkbox_'+j).is(':checked') || updateIdDtls!="")
					{
						if (form_validation('workOrderNo_'+j+'*quantity_'+j+'*rate_'+j,'WO*Qunatity*Rate')==false)
						{
							return;
						}
						
						i++;
						data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&itemgroupid_" + i + "='" + $('#itemgroupid_'+j).val()+"'"+"&itemCategoryId_" + i + "='" + $('#itemCategoryId_'+j).val()+"'"+ "&itemdescription_" + i + "='" + $('#itemdescription_'+j).val()+"'"+"&itemSize_" + i + "='" + $('#itemSize_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).val()+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&itemProdId_" + i + "='" + $('#itemProdId_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'"+"&hideTransData_" + i + "='" + $('#hideTransData_'+j).val()+"'";
						
						txt_total_amount+=$('#amount_'+j).val()*1;
						if($('#workOrderChkbox_'+j).is(':checked')) selected_row++;
					}
				}
			}
			else
			{
				for (var j=1; j<=row_num; j++)
				{
					if (form_validation('itemgroupid_'+j+'*itemCategoryId_'+j+'*itemdescription_'+j+'*quantity_'+j+'*rate_'+j,'Item Group*Item Category*Item Description*Qunatity*Rate')==false)
					{
						return;
					}
					
					i++;
					data_all+=get_submitted_data_string('itemgroupid_'+i+'*itemCategoryId_'+i+'*itemdescription_'+i+'*itemSize_'+i+'*uom_'+i+'*quantity_'+i+'*rate_'+i+'*amount_'+i+'*itemProdId_'+i+'*updateIdDtls_'+i,"../../",i);
					
					txt_total_amount+=$('#amount_'+j).val()*1;
					selected_row++;
				}
			}
		
		}
		
		//alert(data_all);return;
	
		if(selected_row<1)
		{
			alert("Please Select WO");
			return;
		}
		//alert(data_all_checked);return;
		
		txt_total_amount_net=txt_total_amount*1+txt_upcharge*1-txt_discount*1;
		
		var data="action=save_update_delete_dtls&operation="+operation+'&cbo_pi_basis_id='+cbo_pi_basis_id+'&total_row='+i+'&update_id='+update_id+'&txt_total_amount='+txt_total_amount+'&txt_upcharge='+txt_upcharge+'&txt_discount='+txt_discount+'&txt_total_amount_net='+txt_total_amount_net+'&txt_deleted_id='+txt_deleted_id+'&cbo_currency_id='+cbo_currency_id+'&cbo_goods_rcv_status='+cbo_goods_rcv_status+'&txt_order_type='+txt_order_type+data_all+data_all_checked;
		
		//alert(data); return; //txt_order_type
		
		freeze_window(operation);
		
		http.open("POST","requires/pi_dyes_chemical_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_pi_item_details_reponse;
	}
			 
	function fnc_pi_item_details_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);release_freezing();
			var reponse=http.responseText.split('**'); 
			show_msg(trim(reponse[0]));
			
			if((reponse[0]==0 || reponse[0]==1 || reponse[0]==2))
			{	
				var cbo_pi_basis_id = document.getElementById('cbo_pi_basis_id').value;
				var item_category = "";//document.getElementById('cbo_item_category_id').value;
				var goods_rcv_status = document.getElementById('cbo_goods_rcv_status').value;
				show_list_view(cbo_pi_basis_id+'_'+''+'_'+'2'+'_'+reponse[1]+'_'+goods_rcv_status, 'pi_details', 'pi_details_container', 'requires/pi_dyes_chemical_controller', '' ) ;
				var numRow = $('table#tbl_pi_item tbody tr').length; 
				if(reponse[0]==2)
				{
					$('#txt_tot_row').val(numRow);
				}
				else
				{
					$('#txt_tot_row').val(numRow);
				}
				
				if(cbo_pi_basis_id==2 && (item_category==2 || item_category==3 || item_category==13 || item_category==14))
				{
					add_break_down_tr(numRow);		
				}
			}
			else if(reponse[0]==11)
			{
				alert(reponse[1]);release_freezing();return;
			}
			else if(reponse[0]==14)
			{
				alert(reponse[1]);release_freezing();return;
			}
			else if(reponse[0]==16)
			{
				alert("This PI is already Approved. So You can't change it.");release_freezing();return;
			}
			
			set_button_status(reponse[2], permission, 'fnc_pi_item_details',2);
			release_freezing();
		}
	}
	 
	function openmypage()
	{
		var item_category_id 	= '';//$('#cbo_item_category_id').val();
		var importer_id 	= $('#cbo_importer_id').val();
		var supplier_id		= $('#cbo_supplier_id').val();
		
		if (form_validation('cbo_importer_id','Importer Name')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'PI Selection Form';	
			var page_link = 'requires/pi_dyes_chemical_controller.php?item_category_id='+item_category_id+'&importer_id='+importer_id+'&supplier_id='+supplier_id+'&action=pi_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=450px,center=1,resize=1,scrolling=0','../');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var theemail=this.contentDoc.getElementById("txt_selected_pi_id") //Access form field with id="emailfield"
				
				if(theemail.value!="")
				{
					freeze_window(5);
					get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/pi_dyes_chemical_controller" );
					//alert(cbo_pi_basis_id);
					show_list_view($('#cbo_pi_basis_id').val()+'_'+''+'_'+'2'+'_'+theemail.value+'_'+$('#cbo_goods_rcv_status').val(),'pi_details','pi_details_container', 'requires/pi_dyes_chemical_controller','');
					var txt_tot_row = $('#txt_tot_row').val(); 
					if(txt_tot_row==0)
						set_button_status(0, permission, 'fnc_pi_item_details',2);
					else 
						set_button_status(1, permission, 'fnc_pi_item_details',2);
					
					var cbo_pi_basis_id=$('#cbo_pi_basis_id').val();
					/*if(cbo_pi_basis_id==2 && (item_category==2 || item_category==3 || item_category==13 || item_category==14))
					{
						add_break_down_tr(txt_tot_row);	
					}*/
					
					calculate_total_amount(1);
					release_freezing();
				} 
			}
		}
	}

	function openmypage_fabricDescription(row_num)
	{
		var tot_row=$("#tbl_pi_item tbody tr").length;
		var item_category = $('#cbo_item_category_id').val();
		
		var prev_attached_id=fabricNature='';
		
		if(item_category==2 || item_category==13)
		{
			fabricNature=2;
		}
		else
		{
			fabricNature=3;
		}
		
		for(var j=1; j<=tot_row; j++)
		{
			var deter_id=$('#hideDeterminationId_'+j).val();
			
			if(deter_id!="")
			{
				if(prev_attached_id=="") prev_attached_id=deter_id; else prev_attached_id+=","+deter_id;
			}
		}
		
		var title = 'Fabric Description Info';	
		var page_link = 'requires/pi_dyes_chemical_controller.php?action=fabricDescription_popup&fabricNature='+fabricNature+"&prev_attached_id="+prev_attached_id;
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var txt_selected_id=this.contentDoc.getElementById("txt_selected_id").value;	 //Access form field with id="emailfield"
			var txt_selected=this.contentDoc.getElementById("txt_selected").value; //Access form field with id="emailfield"
	
			var determinationId_arr = txt_selected_id.split(',');
			var arr = txt_selected.split(',');
			var row_id=$("#tbl_pi_item tbody tr:last").attr('id').split('_');
			
			//var i=parseInt(row_id[1]);
			var i=$("#tbl_pi_item tbody tr").length;
			
			$(arr).each(function(index, element) 
			{
				var all_data = this.split('**');
				var construction  = all_data[0];
				var composition  = all_data[1];
				var gsn_weight  = all_data[2];
				var determinationId  = determinationId_arr[index];
				
				if(index!=0 )//|| constr!=""
				{
					var constr= $('#construction_'+i).val();
					if(constr!="")
					{
						//var last_row=$("#tbl_pi_item tbody tr:last").attr('id').split('_');
						//z=parseInt(row_id[1]);
						z=$("#tbl_pi_item tbody tr").length;
						
						add_break_down_tr(z);
						i++;
					}
	
					$('#construction_'+i).val(construction);
					$('#composition_'+i).val(composition);
					$('#hideDeterminationId_'+i).val(determinationId);
					
					if(item_category==2 || item_category==13)
					{
						$('#gsm_'+i).val(gsn_weight);
					}
					else
					{
						$('#weight_'+i).val(gsn_weight);
					}
				}
				else
				{
					$('#construction_'+row_num).val(construction);
					$('#composition_'+row_num).val(composition);
					$('#hideDeterminationId_'+row_num).val(determinationId);
					
					if(item_category==2 || item_category==13)
					{
						$('#gsm_'+row_num).val(gsn_weight);
					}
					else
					{
						$('#weight_'+row_num).val(gsn_weight);
					}
				}
				
				index++;
			});
			
			var last_row_id=$("#tbl_pi_item tbody tr:last").attr('id').split('_');
			var last_ro_no=last_row_id[1];
			var constr 	= $('#construction_'+last_ro_no).val();
			if(constr!="") add_break_down_tr(last_ro_no);	
		}
	}
	
	function openmypage_test_item(row_num)
	{
		if (form_validation('cboTestFor_'+row_num,'Test For')==false)
		{
			return;
		}
		
		var cboTestFor = $('#cboTestFor_'+row_num).val();
		var prev_attached_id=$('#testItemId_'+row_num).val();

		var title = 'Test Item Info';	
		var page_link = 'requires/pi_dyes_chemical_controller.php?action=testItem_popup&cboTestFor='+cboTestFor+"&prev_attached_id="+prev_attached_id;
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=370px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var txt_selected_id=this.contentDoc.getElementById("txt_selected_id").value;	 //Access form field with id="emailfield"
			var txt_selected=this.contentDoc.getElementById("txt_selected").value; //Access form field with id="emailfield"
			$('#testItemId_'+row_num).val(txt_selected_id);
			$('#txtTestItem_'+row_num).val(txt_selected);
		}
	}

	function openmypage_wo(row_num)
	{
		//alert(row_num);
		var item_category_id	= '';//$('#cbo_item_category_id').val();
		var importer_id 	= $('#cbo_importer_id').val();
		var supplier_id		= $('#cbo_supplier_id').val();
		var goods_rcv_status= $('#cbo_goods_rcv_status').val();
		var cbo_pi_basis_id= $('#cbo_pi_basis_id').val();
		
		if($('#txt_system_id').val()=="")
		{
			alert("Save Data First.");return;
		}
		
		/*if (form_validation('cbo_item_category_id','Item Category')==false)
		{
			return;
		}*/
		//else
		//{
			//$prev_pi_qty_arr[$row[csf('booking_no')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('uom')]] 
			var prev_wo_ids=''; var prev_wo_feb_datas='';
			var row_num=$('#tbl_pi_item tbody tr').length;
			for (var j=1; j<=row_num; j++)
			{
				var hideWoDtlsId=$('#hideWoDtlsId_'+j).val();
				if(hideWoDtlsId!="")
				{
					if(prev_wo_ids=="") prev_wo_ids=hideWoDtlsId; else prev_wo_ids+=","+hideWoDtlsId;
				}
				if(item_category_id==2 || item_category_id==3)
				{
					
					var hideWoId=$('#hideWoId_'+j).val();
					var workOrderNo=$('#workOrderNo_'+j).val();
					var hideDeterminationId=$('#hideDeterminationId_'+j).val();
					var colorName=$('#colorName_'+j).val();
					var construction=$('#construction_'+j).val();
					var composition=$('#composition_'+j).val();
					var gsm=$('#gsm_'+j).val();
					var diawidth=$('#diawidth_'+j).val();
					var uom=$('#uom_'+j).val();
					if(hideWoId!="")
					{
						if(prev_wo_feb_datas=="") prev_wo_feb_datas=hideWoId+"**"+workOrderNo+"**"+hideDeterminationId+"**"+colorName+"**"+construction+"**"+composition+"**"+gsm+"**"+diawidth+"**"+uom; 
						else prev_wo_feb_datas+="***"+hideWoId+"**"+workOrderNo+"**"+hideDeterminationId+"**"+colorName+"**"+construction+"**"+composition+"**"+gsm+"**"+diawidth+"**"+uom;
					}
				}
			}
			//alert(prev_wo_feb_datas);return;
			var title = 'WO Selection Form';	
			var page_link = 'requires/pi_dyes_chemical_controller.php?item_category_id='+item_category_id+'&importer_id='+importer_id+'&supplier_id='+supplier_id+'&goods_rcv_status='+goods_rcv_status+'&prev_wo_ids='+prev_wo_ids+'&prev_wo_feb_datas='+prev_wo_feb_datas+'&action=wo_popup';
			//alert(page_link);
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=450px,center=1,resize=1,scrolling=0','../');

			emailwindow.onclose=function()
			{

				var theform=this.contentDoc.forms[0]; //("search_order_frm"); //Access the form inside the modal window
				var theemail=this.contentDoc.getElementById("txt_selected_wo_id"); //Access form field with id="emailfield"
				var order_type=this.contentDoc.getElementById("order_non_order_type").value; //Access form field with id="emailfield"
				$('#txt_order_type').val(order_type);
				//alert(theemail.value);//return;
				
				if (theemail.value!="")
				{
					var tot_row=$('#txt_tot_row').val();
					var data=theemail.value+"**"+item_category_id+"**"+tot_row+"**"+order_type+"**"+goods_rcv_status+"**"+importer_id+"**"+cbo_pi_basis_id;
	  
					//var list_view_wo =trim(return_global_ajax_value( data, 'populate_data_wo_form', '', 'requires/pi_dyes_chemical_controller'));
  
					/*if(list_view_wo=="") 
					{
						alert("This Work order has already been taged to another PI.");
						return;
					}*/
					freeze_window(5);
					var list_view_wo =trim(return_global_ajax_value( data, 'populate_data_wo_form', '', 'requires/pi_dyes_chemical_controller'));
					//alert(list_view_wo);return;
					if(list_view_wo!="")
					{
						var wo_no=$('#workOrderNo_'+row_num).val(); 
					
						if(wo_no=="")
						{
							$("#row_"+row_num).remove();
						}
						
						$('#cbo_importer_id').attr('disabled',true);
						$('#cbo_supplier_id').attr('disabled',true);
		
						$("#tbl_pi_item tbody:last").append(list_view_wo);	
							
						var numRow = $('table#tbl_pi_item tbody tr').length; 
						//alert(numRow);
						$('#txt_tot_row').val(numRow);
						calculate_total_amount(1);
					}
					release_freezing();
				} 
			}
		//}
	}

	function openmypage_item_desc(row_num)
	{
		//var item_category = $('#cbo_item_category_id').val();
		//if(item_category==24) item_category=1;
		//if (form_validation('cbo_item_category_id','Item Category')==false)
		//{
		//	return;
		//}
		//else
		//{ 	
			var title = 'Item Description Form';	
			//var page_link = 'requires/pi_dyes_chemical_controller.php?item_category='+''+'&action=itemDesc_popup';
			var page_link = 'requires/pi_dyes_chemical_controller.php?action=itemDesc_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=820px,height=450px,center=1,resize=1,scrolling=0','../');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var theemail=this.contentDoc.getElementById("txt_selected_item_id") //Access form field with id="emailfield"
				
				if (theemail.value!="")
				{
					freeze_window(5);
					
					var tot_row=$('#txt_tot_row').val();
					var data=theemail.value+"**"+''+"**"+tot_row;
	
					var list_view_wo =return_global_ajax_value( data, 'populate_data_item_form', '', 'requires/pi_dyes_chemical_controller');
					
					var item_desc=$('#itemdescription_'+row_num).val(); 
					//alert(item_desc+"**"+row_num);
					if(item_desc=="")
					{
						$("#row_"+row_num).remove();
					}
					
					$("#tbl_pi_item tbody:last").append(list_view_wo);	
						
					var numRow = $('table#tbl_pi_item tbody tr').length; 
					$('#txt_tot_row').val(numRow);
					calculate_total_amount(1);
					release_freezing();
				} 
			}
		//}
	}

	function control_composition(id,type)
	{
		var cbocompone=(document.getElementById('yarnCompositionItem1_'+id).value);
		var cbocomptwo=(document.getElementById('yarnCompositionItem2_'+id).value);
		var percentone=(document.getElementById('yarnCompositionPercentage1_'+id).value)*1;
		var percenttwo=(document.getElementById('yarnCompositionPercentage2_'+id).value)*1;
		var row_num=$('#tbl_pi_item tbody tr').length;
		
		if(type=='percent_one' && percentone>100)
		{
			alert("Greater Than 100 Not Allwed");
			document.getElementById('yarnCompositionPercentage1_'+id).value="";
		}
		
		if(type=='percent_one' && percentone<=0)
		{
			alert("0 Or Less Than 0 Not Allwed")
			document.getElementById('yarnCompositionPercentage1_'+id).value="";
			document.getElementById('yarnCompositionPercentage1_'+id).disabled=true;
			document.getElementById('yarnCompositionItem1_'+id).value=0;
			document.getElementById('yarnCompositionItem1_'+id).disabled=true;
			document.getElementById('yarnCompositionPercentage2_'+id).value=100;
			document.getElementById('yarnCompositionPercentage2_'+id).disabled=false;
			document.getElementById('yarnCompositionItem2_'+id).disabled=false;
		}
		if(type=='percent_one' && percentone==100)
		{
			document.getElementById('yarnCompositionPercentage2_'+id).value="";
			document.getElementById('yarnCompositionItem2_'+id).value=0;
			document.getElementById('yarnCompositionPercentage2_'+id).disabled=true;
			document.getElementById('yarnCompositionItem2_'+id).disabled=true;
		}
		
		if(type=='percent_one' && percentone < 100 && percentone > 0 )
		{
			document.getElementById('yarnCompositionPercentage2_'+id).value=100-percentone;
			document.getElementById('yarnCompositionPercentage2_'+id).disabled=false;
			document.getElementById('yarnCompositionItem2_'+id).disabled=false;
		}
		
		if(type=='comp_one' && cbocomptwo!=0 && cbocompone==cbocomptwo )
		{
			alert("Same Composition Not Allowed");
			document.getElementById('yarnCompositionItem1_'+id).value=0;
		}
		
		if(type=='percent_two' && percenttwo>100)
		{
			alert("Greater Than 100 Not Allwed")
			document.getElementById('yarnCompositionPercentage2_'+id).value="";
		}
		if(type=='percent_two' && percenttwo<=0)
		{
			alert("0 Or Less Than 0 Not Allwed")
			document.getElementById('yarnCompositionPercentage2_'+id).value="";
			document.getElementById('yarnCompositionPercentage2_'+id).disabled=true;
			document.getElementById('yarnCompositionItem2_'+id).value=0;
			document.getElementById('yarnCompositionItem2_'+id).disabled=true;
			document.getElementById('yarnCompositionPercentage1_'+id).value=100;
			document.getElementById('yarnCompositionPercentage1_'+id).disabled=false;
			document.getElementById('yarnCompositionItem1_'+id).disabled=false;
		}
		if(type=='percent_two' && percenttwo==100)
		{
			document.getElementById('yarnCompositionPercentage1_'+id).value="";
			document.getElementById('yarnCompositionItem1_'+id).value=0;
			document.getElementById('yarnCompositionPercentage1_'+id).disabled=true;
			document.getElementById('yarnCompositionItem1_'+id).disabled=true;
		}
		
		if(type=='percent_two' && percenttwo<100 && percenttwo>0)
		{
			document.getElementById('yarnCompositionPercentage1_'+id).value=100-percenttwo;
			document.getElementById('yarnCompositionPercentage1_'+id).disabled=false;
			document.getElementById('yarnCompositionItem1_'+id).disabled=false;
		}
		
		if(type=='comp_two'&& cbocompone!=0 && cbocomptwo==cbocompone)
		{
			alert("Same Composition Not Allowed");
			document.getElementById('yarnCompositionItem2_'+id).value=0;
		}
	}


	function openmypage_exportPi()
	{
		var title = 'Export PI Selection Form';	
		var page_link = 'requires/pi_dyes_chemical_controller.php?action=export_pi_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=450px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("txt_selected_data") //Access form field with id="emailfield"
			
			if(theemail.value!="")
			{
				freeze_window(5);
				
				var datas=theemail.value.split("_");
				
				$("#export_pi_id").val(datas[0]);
				$("#within_group").val(datas[1]);
				
				$("#cbo_item_category_id option[value!='0']").remove();
				$("#cbo_item_category_id").append("<option selected value='"+datas[22]+"'>"+datas[23]+"</option>");
				
				$("#cbo_importer_id option[value!='0']").remove();
				$("#cbo_importer_id").append("<option selected value='"+datas[2]+"'>"+datas[12]+"</option>");
				
				$("#cbo_supplier_id option[value!='0']").remove();
				$("#cbo_supplier_id").append("<option selected value='"+datas[3]+"'>"+datas[13]+"</option>");
				
				$("#pi_number").val(datas[4]);
				$("#pi_date").val(datas[5]);
				$("#last_shipment_date").val(datas[6]);
				$("#pi_validity_date").val(datas[7]);
				$("#cbo_currency_id").val(datas[8]);
				$("#hs_code").val(datas[9]);
				$("#txt_internal_file_no").val(datas[10]);
				$("#txt_remarks").val(datas[11]);
				$("#update_id").val(datas[18]);
				$("#txt_system_id").val(datas[18]);
				$("#cbo_source_id").val(datas[19]);
				$("#intendor_name").val(datas[20]);
				$("#txt_lc_group_no").val(datas[24]);
				$("#cbo_goods_rcv_status").val(datas[21]);
				$("#cbo_pi_basis_id").val(2);
				
				//$('#pi_number').attr('readOnly','readOnly');
				//$('#pi_number').removeAttr('readOnly','readOnly');
				
				disable_enable_fields('cbo_item_category_id*cbo_importer_id*cbo_supplier_id*pi_number*pi_date*last_shipment_date*pi_validity_date*cbo_currency_id*hs_code*txt_internal_file_no*cbo_pi_basis_id',1,'','');
				
				if(datas[18]*1>0)
				{
					show_list_view(datas[18]+"**"+datas[14]+"**"+datas[15]+"**"+datas[16]+"**"+datas[17],'export_pi_details_update','pi_details_container','requires/pi_dyes_chemical_controller','');
					set_button_status(1, permission, 'fnc_pi_mst',1);
				}
				else
				{
					show_list_view(datas[0]+"**"+datas[14]+"**"+datas[15]+"**"+datas[16]+"**"+datas[17], 'export_pi_details', 'pi_details_container', 'requires/pi_dyes_chemical_controller','');
					set_button_status(0, permission, 'fnc_pi_mst',1);
				}
				release_freezing();
			} 
		}
	}

	function reset_fnc()
	{
		location.reload(); 
	}
	
	function openmypage_user()
	{
		var title = 'Approval User';	
		var menu_id=document.getElementById('active_menu_id').value
		var page_link = 'requires/pi_dyes_chemical_controller.php?action=approvalUser_popup&menu_id='+menu_id;
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=410px,height=370px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail_id=this.contentDoc.getElementById("hdn_user_id").value; //Access form field with id="emailfield"
			var theemail_name=this.contentDoc.getElementById("hdn_user_name").value;
			if(theemail_id!="")
			{
				freeze_window(5);
				
				$("#hiddn_user_id").val( theemail_id );
				$("#txt_user_name").val( theemail_name );
			}
			release_freezing();
		}
	}
	
	function fnc_print_pi()
	{
		if($('#txt_system_id').val()=="")
		{
			alert("Please Save First.");
			return;
		}
		else
		{
			if($('#cbo_item_category_id').val()==4)
			{
				print_report( $('#cbo_importer_id').val()+'*'+$('#update_id').val()+'*'+227, "print_pi", "requires/pi_print_urmi");
			}
			else
			{
				alert("Only Accessories Item Print Allowed.");
				return;
			}
		}
	}

</script>

<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission); ?>
        <div>
        <form name="pimasterform_1" id="pimasterform_1" autocomplete="off"> 
            <fieldset style="width:900px;">
            <legend>PI Details</legend>
                <table width="100%" border="0" cellpadding="0" cellspacing="2">
                    <tr height="25">
                        <td colspan="6" valign="middle" align="center" style="border-bottom:0px solid #666">
                            <strong>System ID</strong>&nbsp;&nbsp;<input type="text" name="txt_system_id" id="txt_system_id" style="width:140px" class="text_boxes" readonly/>
                            &nbsp;&nbsp;&nbsp;<strong style="display: none;">Get Export PI</strong>&nbsp;&nbsp;
                            <input type="hidden" name="txt_export_pi" id="txt_export_pi" style="width:140px" class="text_boxes" placeholder="Double click for Export PI" onDblClick="openmypage_exportPi()" readonly />
                            <input type="hidden" name="export_pi_id" id="export_pi_id"/>
                            <input type="hidden" name="within_group" id="within_group"/>
                        </td>
                    </tr>
                    <tr>
                        <!-- <td class="must_entry_caption" id="category_td" width="110">Item Category</td> --><input type="hidden" name="is_approved" id="is_approved" value="">
                        <!-- <td><?php //echo create_drop_down( "cbo_item_category_id", 151, $item_category,'', 1, '--Select--',0,"",0,'','','','74,72,79,73,71,77,78,75,76,1,2,3,4,12,13,14,24,25,31'); ?>  
                        </td> -->
                        <td class="must_entry_caption"  width="110">Importer</td>
                        <td id="importer_td"><?php echo create_drop_down( "cbo_importer_id", 151,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name",'id,company_name', 1, 'Select',0,"load_drop_down( 'requires/pi_dyes_chemical_controller',this.value, 'load_supplier_dropdown', 'supplier_td' );",0); ?>       
                        </td>
                        <td class="must_entry_caption">Supplier</td>
                        <td id="supplier_td"><? echo create_drop_down( "cbo_supplier_id", 151, $blank_array,"", 1, "-- Select Supplier --", 0, "" ); ?></td>	<td class="must_entry_caption">PI Basis</td>
                        <td><?php echo create_drop_down( "cbo_pi_basis_id", 151, $pi_basis,'', 0, '',1,"show_list_view(this.value+'_'+''+'_'+'1', 'pi_details', 'pi_details_container', 'requires/pi_dyes_chemical_controller', '' ) ;",0); ?>  
                        </td>				
                    </tr>
                    <tr>
                        <td class="must_entry_caption">PI No</td>
                        <td><input type="text" name="pi_number" id="pi_number" class="text_boxes" style="width:140px" placeholder="Double click for PI" onDblClick="openmypage()" maxlength="100" /></td>
                        <td class="must_entry_caption">PI Date</td>
                        <td><input type="text" name="pi_date" id="pi_date" class="datepicker"  style="width:140px" /></td>
                        <td>Last Shipment Date</td>
                        <td><input type="text" name="last_shipment_date"  style="width:140px"  id="last_shipment_date" class="datepicker" value="" /></td>
                    </tr>
                    <tr>
                        <td>PI Validity Date</td>
                        <td><input type="text" name="pi_validity_date" id="pi_validity_date"  style="width:140px"  class="datepicker" value="" /></td>
                        <td class="must_entry_caption">Currency</td>
                        <td><?php echo create_drop_down( "cbo_currency_id", 151,$currency,'',0,'',2,0,0); ?></td>
                        <td  class="must_entry_caption">Source</td>                        	
                        <td><?php echo create_drop_down( "cbo_source_id", 151, $source,'', 0, '',0,0); ?></td>
                    </tr>
                    <tr>
                        <td>HS Code</td>
                        <td><input type="text" name="hs_code" id="hs_code" class="text_boxes"  style="width:140px"  value=""  maxlength="30" /></td>
                        <td>Internal File No</td>
                        <td><input type="text" name="txt_internal_file_no" id="txt_internal_file_no"  style="width:140px"  class="text_boxes_numeric"  maxlength="50" /></td>
                        <td>Indentor Name</td>
                        <td><?php echo create_drop_down( "intendor_name", 151,"select a.id,a.supplier_name from lib_supplier a,lib_supplier_party_type b where a.id = b.supplier_id and b.party_type = 40 group by  a.id,a.supplier_name order by a.supplier_name",'id,supplier_name', 1, 'Select',0,0,0); ?>       
                        </td>
                    </tr>
                    <tr>
                        
                        <td class="must_entry_caption">Goods Rcv Status</td>
                        <td><?php echo create_drop_down( "cbo_goods_rcv_status", 151, $acceptance_time,'', 0, '',2,"",0); ?></td>
                        <td>LC Group No.</td>
                        <td><input type="text" id="txt_lc_group_no" name="txt_lc_group_no" style="width:140px" class="text_boxes_numeric" /></td> 
                    </tr>
                    <tr>
                        <td>Remarks</td>
                        <td colspan="3"><input type="text" name="txt_remarks" id="txt_remarks"  style="width:450px;"  class="text_boxes" /></td>
                        <td>Ready To Approved</td>  
                        <td><? echo create_drop_down( "cbo_ready_to_approved", 151, $yes_no,"", 1, "-- Select--", 2, "","","" ); ?></td>   
                    </tr>
                    <tr>
                    	<td>Approval User</td>
                    	<td><input type="text" name="txt_user_name" id="txt_user_name"  style="width:140px;" class="text_boxes" placeholder="Browse Approval User" onDblClick="openmypage_user();" readonly /><input type="hidden" name="hiddn_user_id" id="hiddn_user_id"></td>
                    	<td></td>
                        <td><input type="button" id="image_button" class="image_uploader" style="width:152px;" value="CLICK TO ADD FILE" onClick="file_uploader( '../../', document.getElementById('update_id').value,'', 'proforma_invoice',2,1)" /></td>
                    </tr>
                    <tr>
                    	<td colspan="6" height="15"></td>
                    </tr>
                    <tr>
                        <td colspan="6" height="50" valign="middle" align="center" class="button_container">
                        <div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
                        <input type="hidden" name="update_id" id="update_id" value="" readonly/>
                        <input type="hidden" name="hide_approved_status" id="hide_approved_status" value="" readonly />
                        <input type="hidden" name="hide_attached_status" id="hide_attached_status" value="" readonly />
                        <input type="hidden" name="txt_order_type" id="txt_order_type"/>
                        
                        <? echo load_submit_buttons( $permission, "fnc_pi_mst", 0,1 ,"reset_fnc();",1);		
                        //reset_form('pimasterform_1','pi_details_container*approved','','cbo_currency_id,2*cbo_pi_basis_id,2','disable_enable_fields(\'cbo_item_category_id*cbo_pi_basis_id\',0)')
                        ?>
                        <input type="hidden" name="printBtn" id="printBtn" value="PI-Print" onClick="fnc_print_pi()" style="width:100px" class="formbutton" />
                        </td>                          			
                    </tr>                        
                </table>
            </fieldset>
        </form>
            <form name="pimasterform_2" id="pimasterform_2" autocomplete="off">
                <fieldset style="width:1050px; margin-top:10px;">
                    <legend>PI Item Details</legend>
                    <div id="pi_details_container"></div>
                </fieldset>
            </form>
        </div>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
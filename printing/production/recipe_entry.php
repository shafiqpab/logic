<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Recipe Entry For Embellishment 
Functionality	:	
JS Functions	:
Created by		:	Kausar  
Creation date 	: 	30.06.2018
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
echo load_html_head_contents("Recipe Entry Info", "../../", 1, 1,$unicode,1);

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	function fnc_openmypage_po()
	{
		if (form_validation('cbo_company_id*cbo_within_group','Company*Within Group')==false)
		{
			return;
		}
		else
		{ 
			var cbo_company_id = $('#cbo_company_id').val();
			var cbo_within_group = $('#cbo_within_group').val();	
			var title = 'Embl. Order Pop-up Info';	
			
			var page_link = 'requires/recipe_entry_controller.php?action=emblorder_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&cbo_company_id='+cbo_company_id+'&cbo_within_group='+cbo_within_group, title, 'width=930px,height=460px,center=1,resize=1,scrolling=0','../');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]
				var theemailjob=this.contentDoc.getElementById("all_subcon_job").value;
				var theemaildtls=this.contentDoc.getElementById("all_sub_dtls_id").value;
				var theemailcolor=this.contentDoc.getElementById("all_color_id").value;
				if(theemaildtls!="")
				{
					freeze_window(5);
					var data=theemailjob+'_'+theemaildtls+'_'+theemailcolor;
					get_php_form_data( data, "load_php_data_to_form", "requires/recipe_entry_controller" );
					//$str=$row[csf('job_no_prefix_num')].'___'.$row[csf('order_id')].'___'.$row[csf('order_no')].'___'.$row[csf('item_id')].'___'.$row[csf('body_part')].'___'.$row[csf('main_process_id')].'___'.$row[csf('embellishment_type')].'___'.$row[csf('color_id')].'___'.$color_arr[$row[csf('color_id')]];
					
					/*load_drop_down( 'requires/recipe_entry_controller', estr_data[6], 'load_drop_down_embl_type', 'embl_type_td');
					$('#cbo_buyer_name').val( estr_data[0] );
					$('#hid_job_no').val(estr_data[1]);
					$('#txt_order_id').val(estr_data[2]);
					$('#txt_order').val(estr_data[3]);
					
					$('#hid_item_id').val(estr_data[4]);
					$('#hid_bodypart_id').val(estr_data[5]);
					$('#cboEmblName').val(estr_data[6]);
					$('#cboEmblType').val(estr_data[7]);
					$('#txt_pocolor_id').val(estr_data[8]);
					$('#txt_po_color').val(estr_data[9]);
					
					$('#txtbuyerPoId').val(estr_data[10]);
					//$('#txtbuyerPo').val(estr_data[11]);
					//$('#txtstyleRef').val(estr_data[12]);
					$('#txt_ord_qnty').val(estr_data[13]);
					$('#txtbuyerPo').val(estr_data[14]);
					$('#txtstyleRef').val(estr_data[15]);
					
					$('#cbo_company_id').attr('disabled',true);
					$('#cbo_within_group').attr('disabled',true);*/
					set_button_status(0, permission, 'fnc_recipe_entry',1,1);
					release_freezing();
				} 
			}
		}
	}
	
	function fnc_load_party(company,val)
	{
		load_drop_down( 'requires/recipe_entry_controller', company+'_'+val, 'load_drop_down_buyer', 'buyer_td_id' );
	}

	function show_details()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_store_id = $('#cbo_store_id').val();
		var variable_status = $('#txt_variable_status').val();
		if(form_validation('cbo_company_id*cbo_store_id','Company*Store')==false)
		{
			return;
		}
		var list_view_orders = return_global_ajax_value( cbo_company_id+"******"+cbo_store_id+"**"+variable_status, 'item_details', '', 'requires/recipe_entry_controller');
		if(list_view_orders!='')
		{
			$("#list_container_recipe_items").html(list_view_orders);
			setFilterGrid('tbl_list_search',-1);
		}
	}

	function color_row(tr_id)
	{
		var txt_ratio=$('#txt_ratio_'+tr_id).val();
		if(txt_ratio.indexOf(".") >= 0)
		{
			var r=txt_ratio.split('.');
			if(r[1].length>4)
			{
				var after_decimel= r[1].substr(0,4);
				$('#txt_ratio_'+tr_id).val(r[0]+'.'+after_decimel);
			}
		}

		var stock_check=$('#stock_qty_'+tr_id).text()*1;
		var txtRatio=$('#txt_ratio_'+tr_id).val()*1;
		if(txtRatio>0)
		{
			if(stock_check<=0)
			{
				alert("No Stock Qty.");
				$('#txt_ratio_'+tr_id).val('');	
				
				$('#search' + tr_id).css('background-color','#FFFFCC');
				$('#txt_ratio_' + tr_id).css('background-color','White');
			}
			else
			{
				$('#search' + tr_id).css('background-color','yellow');
				$('#txt_ratio_' + tr_id).css('background-color','White');
			}
		}
		fnc_tot_ratio_calculate();
	}
		
		
	function openmypage_itemLot(id)
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var txt_prod_id = $('#txt_prod_id_'+id).val()*1;
		var txt_item_lot = $('#txt_lot_'+id).val();
		//alert  (txt_prod_id);
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var title = 'Item Lot Selection Form';
			var page_link = 'requires/recipe_entry_controller.php?cbo_company_id='+cbo_company_id+'&txt_prod_id='+txt_prod_id+'&txt_item_lot='+txt_item_lot+'&action=itemLot_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=350px,height=390px,center=1,resize=1,scrolling=0','../');

			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var item_lot=this.contentDoc.getElementById("item_lot").value;
				if(item_lot!="")
				{
					freeze_window(5);
					document.getElementById("txt_lot_"+id).value=item_lot;
					release_freezing();
				}
			}
		}
	}	
			
	function fnc_recipe_entry(operation)
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_location').val(), "recipe_entry_print", "requires/recipe_entry_controller") 
			//return;
			show_msg("3");
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			var total_ratio=$('#txt_total_ratio').val()*1;
			if(total_ratio<100 || total_ratio >100)
			{
				alert("Total Ratio Should be 100");
				return;
			}
			
			if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][220]);?>')
			{
				if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][220]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][220]);?>')==false)
				{
					
					return;
				}
			}
			var recipe_for=$('#cbo_recipe_for').val();
			if(recipe_for!=3)
			{
				if($('#copy_id').val()==2)
				{
					if( form_validation('cbo_recipe_for*cbo_company_id*txt_recipe_date*cbo_within_group*txt_po_color*txt_multi_color*txt_total_ratio*cbo_store_id*cbo_location','Recipe For*Company*Recipe Date*Within Group*Color*Color*Total Ratio*Store*Location')==false )
					{
						return;
					}
				}
				else
				{
					if( form_validation('cbo_recipe_for*cbo_company_id*txt_recipe_date*cbo_within_group*txt_po_color*txt_total_ratio','Recipe For*Company*Recipe Date*Within Group*Color*Total Ratio')==false )
					{
						return;
					}
				}
				
			}
			else
			{
				if($('#copy_id').val()==2)
				{
					if( form_validation('cbo_recipe_for*cbo_company_id*txt_recipe_date*txt_multi_color*cbo_store_id*cbo_item_category*txt_item_group*txt_total_ratio*cbo_store_id*cbo_location','Recipe For*Company*Recipe Date*Color*Store*Item Category*Item Group*Total Ratio*Store*location')==false )
					{
						return;
					}
				}
				else
				{
					if( form_validation('cbo_recipe_for*cbo_company_id*txt_recipe_date*cbo_item_category*txt_item_group*txt_total_ratio*cbo_store_id','Recipe For*Company*Recipe Date*Item Category*Item Group*Total Ratio*Store')==false )
					{
						return;
					}
				}
			}
			
			var row_num=$('#tbl_list_search tbody tr').length-1;
			//alert (row_num);return;
			var data_all=""; var i=0; var ratio_per=0;
	
			for(var j=1; j<=row_num; j++)
			{
				var txt_ratio=$('#txt_ratio_'+j).val();
				var updateIdDtls=$('#updateIdDtls_'+j).val();
				ratio_per=ratio_per+(txt_ratio*1);
				if(updateIdDtls!="" || txt_ratio*1>0)  //txt_lot_1
				{
					i++;
					data_all+="&txt_seqno_" + i + "='" + $('#txt_seqno_'+j).val()+"'"+"&product_id_" + i + "='" + $('#product_id_'+j).text()+"'"+"&txt_lot_" + i + "='" + $('#txt_lot_'+j).val()+"'"+"&txt_comments_" + i + "='" + $('#txt_comments_'+j).val()+"'"+"&txt_ratio_" + i + "='" + $('#txt_ratio_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'";
					//alert(data_all);return;
				}
			}
			if($('#copy_id').val()==2)
			{
				if(number_format(ratio_per,4,'.','' ) !=100)
				{
					alert("Ratio Total Less Or More 100 is not Allowed.");
					return;
				}
				if(i<1)
				{
					alert("Please Insert Ratio At Least One Item");
					return;
				}
			}
			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_sys_id*cbo_recipe_for*txt_recipe_des*txt_recipe_date*cbo_company_id*cbo_location*cbo_within_group*txt_order_id*txt_order*cbo_buyer_name*txt_pocolor_id*txt_po_color*hid_job_no*hid_item_id*hid_bodypart_id*cboEmblName*cboEmblType*txt_remarks*update_id*txt_multi_color*cbo_store_id*cbo_item_category*item_group_id*hidd_group_code*hidd_cons_uom*hidd_order_uom*txt_subgroup_name*txt_description*txt_item_size*hidd_newprod_id*txtbuyerPoId*copy_id*update_id_check*txt_variable_status',"../../")+data_all+'&total_row='+i;
			//alert(data);
			
			//alert (data);return;
			freeze_window(operation);
			http.open("POST","requires/recipe_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fnc_recipe_entry_Reply_info;
		}
	}
	
	function fnc_recipe_entry_Reply_info()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			
			if(trim(reponse[0])=='emblRequ'){
				alert("Dyes And Chemical Issue Requisition Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}

			if(trim(reponse[0])==555){
				alert("The color is already exists");
				release_freezing();
				return;
			}

			if(trim(reponse[0])==20){
				alert("The PO is already exists");
				release_freezing();
				return;
			}
			
			if(trim(reponse[0])=='emblProduction'){
				alert("Production Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}	

			show_msg(trim(reponse[0]));
			
			if((reponse[0]==0 || reponse[0]==1))
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_sys_id').value = reponse[2];
				document.getElementById('update_id_check').value = reponse[1];
				
				$('#copy_id').val(2);
				$('#copy_id').prop('checked', false);
				$('#copy_id').removeAttr('disabled','disabled');
				
				show_list_view(reponse[1], 'recipe_item_details', 'recipe_items_list_view', 'requires/recipe_entry_controller', '' ) ;
				setFilterGrid('tbl_list_search',-1);
				$('#txt_multi_color').val('');
				
				$('#hidd_newprod_id').val('');
				$('#cbo_item_category').val(0);
				$('#txt_item_group').val('');
				$('#txt_subgroup_name').val('');
				$('#txt_description').val('');
				$('#txt_item_size').val('');
				$('#hidd_cons_uom').val('');
				$('#hidd_order_uom').val('');
				$('#hidd_group_code').val('');

				$('#rec_color_td font strong').css({'color':'blue'});
				$('#rec_store_td font strong').css({'color':'blue'});

				$('#list_container_recipe_items').html('');
				var recipe_for=$('#cbo_recipe_for').val();
				fnc_disable_prod(recipe_for);
				$('#cbo_store_id').attr('disabled',true);
				set_button_status(0, permission, 'fnc_recipe_entry',1,1);	
			}
			if(reponse[0]==2)
			{
				location.reload();
			}
			release_freezing();	
		}
	}
	
	function openmypage_sysNo()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_within_group = $('#cbo_within_group').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'System ID Pop-up Info';	
			var page_link = 'requires/recipe_entry_controller.php?cbo_company_id='+cbo_company_id+'&cbo_within_group='+cbo_within_group+'&action=systemid_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=390px,center=1,resize=1,scrolling=0','../');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var update_id=this.contentDoc.getElementById("hidden_update_id").value;	 //Access form field with id="emailfield"
				
				if(update_id!="")
				{
					freeze_window(5);
					$('#list_container_recipe_items').html('');
					get_php_form_data(update_id, "populate_data_from_search_popup", "requires/recipe_entry_controller" );
					
					show_list_view(update_id, 'recipe_item_details', 'recipe_items_list_view', 'requires/recipe_entry_controller', '' ) ;
					//reset_form('','','copy_id*copy_sub_process*cbo_sub_process*txt_subprocess_remarks','','');
					$('#copy_id').removeAttr('disabled','disabled');
					$('#copy_id').val(2);
					set_button_status(0, permission, 'fnc_recipe_entry',1,1);
					fnc_tot_ratio_calculate();
					$('#cbo_store_id').attr('disabled',true);
					release_freezing();
				} 
			}
		}
	}
	
	function fnc_item_details(color_data)
	{
		var exdata=color_data.split('__');
		var color_id=exdata[0];
		var color_name=exdata[1];
		
		var new_prod_id=exdata[2];
		var item_category_id=exdata[3];
		var group_id=exdata[4];
		var item_name=exdata[5];
		var sub_group_name=exdata[6];
		var item_description=exdata[7];
		var item_size=exdata[8];
		var trim_uom=exdata[9];
		var item_code=exdata[10];
		//var store_id=exdata[11];
		//$multicolor_id.'__'.$color_arr[$multicolor_id].'__'.$new_prod_id.'__'.$item_category_id.'__'.$group_id.'__'.$item_name.'__'.$sub_group_name.'__'.$item_description.'__'.$item_size.'__'.$trim_uom.'__'.$item_code
		$(".accordion_h").each(function() {
		
			 var tid=$(this).attr('id'); 
			 tid=tid+"span";
			 $('#'+tid).html("+");
		});
		
		$('#accordion_h'+color_id+'span').html("-");
		var update_id= $('#update_id_check').val();
		var cbo_company_id= $('#cbo_company_id').val();
		var cbo_store_id= $('#cbo_store_id').val();
		
		show_list_view(cbo_company_id+"**"+color_id+"**"+update_id+"**"+cbo_store_id, 'item_details', 'list_container_recipe_items', 'requires/recipe_entry_controller', '');
		setFilterGrid('tbl_list_search',-1);
		$('#txt_multi_color').val(color_name);
		
		$('#hidd_newprod_id').val(new_prod_id);
		//$('#cbo_store_id').val(store_id);
		$('#cbo_item_category').val(item_category_id);
		$("#item_group_id").val(group_id);
		
		$('#txt_item_group').val(item_name);
		$('#txt_subgroup_name').val(sub_group_name);
		$('#txt_description').val(item_description);
		$('#txt_item_size').val(item_size);
		$('#hidd_cons_uom').val(trim_uom);
		$('#hidd_order_uom').val(trim_uom);
		$('#hidd_group_code').val(item_code);
		
		//$("#cbo_store_id").attr("disabled",true);
		$("#cbo_item_category").attr("disabled",true);
		//$('#cbo_store_id')attr("disabled",true);
		$("#txt_item_group").attr("disabled",true);
		$("#txt_description").attr("disabled",true);
		$("#txt_item_size").attr("disabled",true);
		$("#txt_subgroup_name").attr("disabled",true);
		
		fnc_tot_ratio_calculate();
		set_button_status(1, permission, 'fnc_recipe_entry',1,1);
	}
	 
	function seq_no_val(id)
	{
		var row_num=$('#tbl_list_search tbody tr').length-1;
		var seq_no =new Array(); 
		var k=0;
		for(var j=1; j<=row_num; j++)
		{
			if(j!=id)
			{
				 
				if( $('#txt_seqno_'+j).val()*1>0) 
				{ 
					seq_no[k]=$('#txt_seqno_'+j).val()*1;
					k++;
				}
			}
		}
		var largest=0; 
		if(seq_no!='')
		{
			var largest = Math.max.apply(Math, seq_no);
		}
		if(largest=='')
		{
			largest=0;
		}//alert (largest)
		
		largest=largest+1;
		for(var i=1;i<=largest;i++)
		{
			if ($('#txt_ratio_'+id).val()!='')
			{
				if ($('#txt_seqno_'+id).val()=='')
				{
					$('#txt_seqno_'+id).val(largest);
				}
			}
			else
			{
				$('#txt_seqno_'+id).val('');
			}
		}
	}
	
	function row_sequence(row_id)
	{
		var row_num=$('#tbl_list_search tbody tr').length-1;
		var txt_seq=$('#txt_seqno_'+row_id).val();
		//var seq_no=1;
		if(txt_seq=="")
		{
			return;	
		}
		
		for(var j=1; j<=row_num; j++)
		{
			if(j==row_id)
			{
				continue;
			}
			else
			{
				var txt_seq_check=$('#txt_seqno_'+j).val();
				
				if(txt_seq==txt_seq_check)
				{
					alert("Duplicate Seq No. "+txt_seq);
					$('#txt_seqno_'+row_id).val('');
					return;
				}
			}
		}
	}	
	
	function fnResetForm()
	{
		$("#cbo_company_id").attr("disabled",false); 
		$("#cbo_recipe_for").attr("disabled",false); 
		$("#cbo_within_group").attr("disabled",false); 
		reset_form('recipeEntry_1','list_container_recipe_items*recipe_items_list_view','',"$('#cbo_within_group').val()*1,$('#copy_id').val()*2",'');
	}
	
	function openmypage_item_group()
	{
		if ( form_validation('cbo_company_id*cbo_item_category','Company Name*Item Category')==false )
		{
			return;
		}
		else
		{
			var category=document.getElementById('cbo_item_category').value;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/recipe_entry_controller.php?category='+category+'&action=itemgroup_popup','Search Group Name', 'width=580px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemail=this.contentDoc.getElementById("item_str").value;
				var exdata=theemail.split('_');
				var id=exdata[0];
				var item_group_code=exdata[1];
				var item_name=exdata[2];
				var order_uom=exdata[3];
				var trim_uom=exdata[4];
				$("#cbo_item_category").attr("disabled",true);
				$("#item_group_id").val(id);
				$("#hidd_group_code").val(item_group_code);
				$("#txt_item_group").val(item_name);
				$("#hidd_order_uom").val(order_uom);
				$("#hidd_cons_uom").val(trim_uom);
				release_freezing();
			}
		}
	}
	
	function fnc_disable_prod(val)
	{
		if(val==3)
		{
			$("#cbo_item_category").attr("disabled",false);
			$("#txt_item_group").attr("disabled",false);
			$("#txt_description").attr("disabled",false);
			$("#txt_item_size").attr("disabled",false);
			$("#txt_subgroup_name").attr("disabled",false);
			$("#cbo_store_id").attr("disabled",false);
		}
		else
		{
			$("#cbo_item_category").attr("disabled",true);
			$("#txt_item_group").attr("disabled",true);
			$("#txt_description").attr("disabled",true);
			$("#txt_item_size").attr("disabled",true);
			$("#txt_subgroup_name").attr("disabled",true);
			$("#cbo_store_id").attr("disabled",true);
		}
	}
	
	function fnc_tot_ratio_calculate()
	{
		var row_num=$('#tbl_list_search tbody tr').length-1;
		var total_ratio=0;
		for(var j=1; j<=row_num; j++)
		{
			total_ratio=(total_ratio*1)+($('#txt_ratio_'+j).val()*1);
		}
		if(number_format(total_ratio,4,'.','' ) >100)
		{
			var decress_value=total_ratio-100;
			alert("Total Ratio Should be 100\n Please Decress "+decress_value+" % Ratio");

			$("#txt_total_ratio").val('');
			return;
		}
		else
		{
			$('#txt_total_ratio').val( number_format(total_ratio,4,'.','' ) );
		}
	}
	
	function copy_check(type)
	{
		if(type==1)
		{
			$("#list_container_recipe_items").html('');
			show_list_view($('#update_id').val(), 'recipe_item_details', 'recipe_items_list_view', 'requires/recipe_entry_controller', '' ) ;
			$('#txt_sys_id').val('');
			$('#update_id').val('');
			$('#txt_max_seq').val('');
			$('#txt_recipe_date').val('');
			$('#txt_order').val('');
			$('#txt_order_id').val('');
			$('#hid_job_no').val('');
			$('#hid_item_id').val('');
			$('#hid_bodypart_id').val('');
			$('#txtbuyerPo').val('');
			$('#txtbuyerPoId').val('');
			$('#txtstyleRef').val('');
			$('#txt_po_color').val('');
			$('#txt_pocolor_id').val('');
			$('#cboEmblName').val('');
			$('#cboEmblType').val('');
			$('#txt_total_ratio').val(100);
			$("#show").attr("disabled",true);
			$('#rec_color_td font strong').css({'color':'black'});
			$('#rec_store_td font strong').css({'color':'black'});
		}

		/*if ( document.getElementById('copy_id').checked==true)
		{
			document.getElementById('copy_id').value=1;
			set_button_status(0, permission, 'fnc_recipe_entry',1,1);
			//alert(chk );
		}
		else if(document.getElementById('copy_id').checked==false)
		{
			document.getElementById('copy_id').value=2;
		}*/

		if ( document.getElementById('copy_id').checked==true)
		{
			document.getElementById('copy_id').value=1;
			set_button_status(0, permission, 'fnc_recipe_entry',1,1);
			//alert(chk );
		}
		else if(document.getElementById('copy_id').checked==false)
		{
			document.getElementById('copy_id').value=2;
		}
	}
	
	
function chk_printing_level_variabe(company)
{
   var status = return_global_ajax_value(company, 'chk_printing_level_variabe', '', 'requires/recipe_entry_controller').trim();
   status = status.split("**");
   
   //alert(status);
	$('#txt_variable_status').val(status[0]);
}
function store_load()
{
	var company = $('#cbo_company_id').val();
	var location_name = $('#cbo_location').val();
	load_drop_down('requires/recipe_entry_controller',company+'__'+location_name, 'load_drop_down_store', 'store_td');
}
	
</script>
</head>

<body onLoad="set_hotkey();store_load();">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission); ?>
	<fieldset style="width:950px;">
	<legend>Recipe Entry</legend> 
		<form name="recipeEntry_1" id="recipeEntry_1"> 
			<fieldset style="width:920px;">
                <table width="910" align="center" border="0">
                    <tr>
                        <td colspan="3" align="right"><strong>System ID</strong></td>
                        <td colspan="2" align="left">
                        	<input type="text" name="txt_sys_id" id="txt_sys_id" class="text_boxes" style="width:140px;" placeholder="Double click to search" onDblClick="openmypage_sysNo();" readonly />
                        	<input type="hidden" name="txt_max_seq" id="txt_max_seq" class="text_boxes" value="0" style="width:40px;" />
                            <input type="hidden" id="txt_variable_status" name="txt_variable_status" value="" />
                        </td>
                        <td>
                       		<strong>Copy</strong>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="copy_id" id="copy_id" onClick="copy_check(1)" value="2" disabled >
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption" width="110">Recipe For</td>
                        <td><? echo create_drop_down("cbo_recipe_for", 150, $recipe_for,"", 1,"-Select-", 2,"fnc_disable_prod(this.value);"); ?></td>
                        <td width="110">Recipe Description</td>
                        <td><input type="text" name="txt_recipe_des" id="txt_recipe_des" class="text_boxes" style="width:140px;" /></td>
                        <td class="must_entry_caption">Recipe Date</td>
                        <td><input type="text" name="txt_recipe_date" id="txt_recipe_date" class="datepicker" value="<? echo date("d-m-Y")?>" style="width:140px;" readonly/></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Company Name</td>
                        <td><? echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Select Company-", 0, "load_drop_down('requires/recipe_entry_controller', this.value, 'load_drop_down_location', 'location_td'); load_drop_down('requires/recipe_entry_controller', this.value+'__'+document.getElementById('cbo_location').value, 'load_drop_down_store', 'store_td'); fnc_load_party(this.value,document.getElementById('cbo_within_group').value);chk_printing_level_variabe(this.value)"); ?></td>
                        <td class="must_entry_caption" >Location</td>
                        <td id="location_td"><? echo create_drop_down("cbo_location", 150, $blank_array,"", 1,"-Select Location-", 0,""); ?></td>
                        <td class="must_entry_caption">Within Group</td>
                        <td>
                            <?php echo create_drop_down( "cbo_within_group", 150, $yes_no,"", 0, "--  --", 0, "fnc_load_party( document.getElementById('cbo_company_id').value, this.value);" ); ?>
                        </td>
                    </tr>
                    <tr>
                    	<td>Order No.</td>
                        <td>
                            <input type="text" name="txt_order" id="txt_order" class="text_boxes" value="" style="width:140px;" readonly placeholder="Browse" onDblClick="fnc_openmypage_po();" />
                            <input type="hidden" name="txt_order_id" id="txt_order_id" class="text_boxes" value="" style="width:60px;" />
                            <input type="hidden" name="hid_job_no" id="hid_job_no" class="text_boxes" value="" style="width:60px;" />
                            <input type="hidden" name="hid_item_id" id="hid_item_id" class="text_boxes" value="" style="width:60px;" />
                            <input type="hidden" name="hid_bodypart_id" id="hid_bodypart_id" class="text_boxes" value="" style="width:60px;" />
                        </td>
                        <td>Party Name</td>
                        <td id="buyer_td_id"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "--Select Party--", $selected, "",1 ); ?></td>
                        
                        <td>Buyer Po</td>
                        <td><input name="txtbuyerPo" id="txtbuyerPo" type="text" class="text_boxes" style="width:140px" readonly />
                            <input name="txtbuyerPoId" id="txtbuyerPoId" type="hidden" class="text_boxes" style="width:70px" />
                        </td>
                    </tr>
                    <tr>
                    	<td>Buyer Style</td>
                    	<td><input name="txtstyleRef" id="txtstyleRef" type="text" class="text_boxes" style="width:140px" readonly /></td>
                        
                        <td class="must_entry_caption">Color</td>
                        <td><input type="text" name="txt_po_color" id="txt_po_color" class="text_boxes" style="width:140px;" disabled placeholder="Display"/><input type="hidden" name="txt_pocolor_id" id="txt_pocolor_id" class="text_boxes" value="" style="width:60px;" /></td>
                    	<td>Embl. Name</td>
                        <td><? echo create_drop_down( "cboEmblName", 150, $emblishment_name_array,"", 1, "--Select--",0,"", 1,"" ); ?></td>
                    </tr>
                    <tr>
                    	<td>Embl. Type</td>
                        <td id="embl_type_td"><? echo create_drop_down( "cboEmblType", 150, $blank_array,"", 1, "--Select--",0,"", 1,"" ); ?></td>
                    	<td>Remarks</td>
                        <td ><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" value="" style="width:140px;" /></td>

                        <td>Order Qty.</td>
                        <td ><input type="text" name="txt_ord_qnty" id="txt_ord_qnty" class="text_boxes" value="" style="width:140px;" / disabled></td>
                    </tr>
                    <tr>
                    	<td></td>
                    	<td></td>
                    	<td></td>
                    	<td></td>
                    	<td></td>
                    	
                    	<td><input type="button" class="image_uploader" style="width:150px" value="ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'embl_recipe_entry', 0 ,1)"></td>
                    </tr>
                </table>
			</fieldset>   
                          
            <fieldset style="width:940px; margin-top:10px">
            <legend>Item Details</legend>
 				<table cellpadding="1" cellspacing="1" border="0" width="100%">
                	<tr>
                    	<td width="100" align="center" class="must_entry_caption"><strong>Color</strong></td>
                        <td width="145" align="center" class="must_entry_caption"><strong>Store</strong></td>
                        <td width="140" align="center"><strong>Item Category</strong></td>
                        <td width="100" align="center"><strong>Item Group</strong></td>
                        <td width="90" align="center"><strong>Sub Group Name</strong></td>
                        <td width="200" align="center"><strong>Item Description</strong></td>
                        <td width="80" align="center"><strong>Item Size</strong></td>
                        <td>&nbsp;</td>
                    </tr>
 					<tr>
 						<td align="center"><input type="text" name="txt_multi_color" id="txt_multi_color" class="text_boxes" value="" placeholder="Write" style="width:80px;" /></td>
                        <td align="center" id="store_td"><? echo create_drop_down( "cbo_store_id", 140, $blank_array,"", "1", "-Store-", 0, "","0","","","","","","" );  ?></td>
 						<td align="center"><? echo create_drop_down( "cbo_item_category", 130, $item_category,"", "1", "--Select--", 0, "","1","5,6,7,22,23","","","","","" );  ?></td>
                        <td align="center">
                        	<input type="hidden" id="item_group_id" />
                            <input type="hidden" id="hidd_group_code" />
                            <input type="hidden" id="hidd_order_uom" />
                            <input type="hidden" id="hidd_cons_uom" />
                            <input type="hidden" id="hidd_newprod_id" />
                            
                            <Input name="txt_item_group" ID="txt_item_group" style="width:85px" value="" class="text_boxes" placeholder="Double Click to Search" onDblClick="openmypage_item_group();" readonly disabled />
                        </td>
                		<td align="center"><Input name="txt_subgroup_name" ID="txt_subgroup_name"  style="width:70px" class="text_boxes" placeholder="Write" disabled ></td>
                        <td align="center"><Input name="txt_description" ID="txt_description"  style="width:180px" class="text_boxes" placeholder="Write" disabled ></td>
                        <td align="center"><Input name="txt_item_size" ID="txt_item_size"  style="width:60px" class="text_boxes" placeholder="Write" disabled ></td>
                        <td id="button_th" align="right">
                        	<input type="button" value="Show Items" name="show" id="show" class="formbuttonplasminus" style="width:70px;" onClick="show_details()"/>
                    	</td> 
 					</tr>
                    <tr>
                    	<td colspan="4" align="right" class="must_entry_caption"><strong>Total Ratio :</strong></td>
                        <td colspan="4"><strong><Input name="txt_total_ratio" ID="txt_total_ratio"  style="width:100px" class="text_boxes_numeric" placeholder="Dispaly" disabled ></strong></td>
                    </tr>
                </table>
                <div id="list_container_recipe_items" style="margin-top:10px"></div>
            </fieldset> 
            <table width="910">
            	<tr>
                    <td colspan="4" align="center" class="button_container">
						<? echo load_submit_buttons($permission, "fnc_recipe_entry", 0,1,"fnResetForm();",1); ?> 
                        <input type="hidden" name="update_id" id="update_id"/>
                        <input type="hidden" name="update_id_check" id="update_id_check"/>
                        <input type="hidden" name="update_dtls_id" id="update_dtls_id"/>
                    </td>	  
                </tr>
            </table>
		</form>
        <div id="recipe_items_list_view" style="margin-top:10px"></div>
	</fieldset>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
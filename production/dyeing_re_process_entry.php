<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Topping Adding Stripping Recipe Entry
Functionality	:
JS Functions	:
Created by		:	Fuad
Creation date 	: 	11.03.2015
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
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Recipe Entry Info", "../", 1, 1,$unicode,1);

?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	var permission='<? echo $permission; ?>';
	var  subprocessForWashArr=['93','94','95','96','97','98','140','141','142','143','160','161','162','163','164','165','166','167','168','169'];


	function show_details()
	{
		var cbo_dyeing_re_process = $('#cbo_dyeing_re_process').val();
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_sub_process = $('#cbo_sub_process').val();
		var recipe_id=$('#txt_recipe_no').val();
		var cbo_store_name=$('#cbo_store_name').val();
		var check_id = $('#check_id').val();
		var update_id='';

		if(form_validation('cbo_dyeing_re_process*txt_recipe_no*cbo_company_id*txt_batch_no*cbo_sub_process*cbo_store_name','Dyeing Re-Process*Recipe No*Company*Batch No*Sub Process*Store')==false)
		{
			return;
		}

		var list_view_orders = return_global_ajax_value( cbo_company_id+'**'+cbo_sub_process+'**'+update_id+'**'+recipe_id+'**'+cbo_dyeing_re_process+"**1**********"+cbo_store_name+'**'+check_id, 'item_details', '', 'requires/dyeing_re_process_entry_controller');
		if(list_view_orders!='')
		{
			$("#list_container_recipe_items").html(list_view_orders);
			setFilterGrid('tbl_list_search',-1);
		}
	}

	function fnc_recipe_entry(operation)
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_labdip_no').val()+'*'+report_title+'*'+$('#txt_recipe_no').val()+'*'+$('#cbo_template_id').val(), "recipe_entry_print", "requires/dyeing_re_process_entry_controller")
			//return;
			show_msg("3");
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if(form_validation('cbo_dyeing_re_process*txt_recipe_no*cbo_company_id*txt_batch_weight*txt_recipe_date*cbo_sub_process*cbo_store_name','Dyeing Re-Process*Recipe No*Company*Batch Weight*Recipe Date*Sub Process')==false)
			{
				return;
			}

				var sub_process_data=$("#cbo_sub_process").val();
				if(subprocessForWashArr.indexOf(sub_process_data) !== -1)  //For Wash/Migration not exists 
				  {
					  var check_pro=1;
				  }
				  else
				  { 
				  	var check_pro=2;
				  }
				 // alert(check_pro);

			//if(!(sub_process_data==93 || sub_process_data==94 || sub_process_data==95 || sub_process_data==96 || sub_process_data==97 || sub_process_data==98 || sub_process_data == 140 || sub_process_data == 141 || sub_process_data == 142 || sub_process_data == 143 ))
			if(check_pro==2) //Wash not exists
			{
	  			var row_num=$('#tbl_list_search tbody tr').length-1;

	  			var data_all=""; var i=0;

				var is_checked_chk="";
	  			for(var j=1; j<=row_num; j++)
	  			{
	  				var txt_ratio=$('#txt_ratio_'+j).val();
	  				var updateIdDtls=$('#updateIdDtls_'+j).val();
	  				var dose=$('#cbo_dose_base_'+j).val();

						//new dev
						/*if ($('#chek_'+j).is(":checked"))
						{*/
							//is_checked_chk+= $('#chek_id_'+i).val();

	  							if(updateIdDtls!="" || txt_ratio*1>0)
				  				{

				  					if (form_validation('cbo_dose_base_'+j,'Dose Base')==false)
				  					{
				  						return;
				  					}

				  					i++;
				  					data_all+="&txt_subprocess_remarks"+"='" + $('#txt_subprocess_remarks').val()+"'"+"&txt_seqno_" + i + "='" + $('#txt_seqno_'+j).val()+"'"+"&product_id_" + i + "='" + $('#product_id_'+j).val()+"'"+"&txt_item_lot_" + i + "='" + $('#txt_item_lot_'+j).val()+"'"+"&cbo_dose_base_" + i + "='" + $('#cbo_dose_base_'+j).val()+"'"+"&txt_ratio_" + i + "='" + $('#txt_ratio_'+j).val()+"'"+"&recipe_qty_" + i + "='" + $('#recipe_qty_'+j).text()+"'"+"&cbo_adj_type_" + i + "='" + $('#cbo_adj_type_'+j).val()+"'"+"&txt_adj_per_" + i + "='" + $('#txt_adj_per_'+j).val()+"'"+"&adj_qnty_" + i + "='" + $('#adj_qnty_'+j).val()+"'"+"&txt_adj_ratio_" + i + "='" + $('#txt_adj_ratio_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'"+"&txt_remarks_" + i + "='" + $('#txt_remarks_'+j).val()+"'"+"&chek_id_" + i + "='" + $('#chek_id_'+j).val()+"'";
				  				}

						}

	  			//}
				/*if(is_checked_chk=="")
				{
					alert("Please Check At Least One Row");
					return;
				}*/

					}
				else
				{
					var i=0;
					if(operation==0)
					{
						data_all="&txt_subprocess_remarks"+"='" + $('#txt_subprocess_remarks').val()+"'";
					}
					else if(operation==1)
	  				{
	  				data_all="&txt_subprocess_remarks"+"='" + $('#txt_subprocess_remarks').val()+"'"+"&updateIdDtls_"+ 1 +"='" + $('#updateIdDtls_'+1).val()+"'"+"&txt_remarks_"+ 1 +"='" + $('#txt_remarks_'+1).val()+"'"+"&txt_ratio_"+ 1 +"='" + $('#txt_ratio_'+1).val()+"'"+"&txt_seqno_"+ 1 +"='" + $('#txt_seqno_'+1).val()+"'"+"&cbo_dose_base_"+ 1 +"='" + $('#cbo_dose_base_'+1).val()+"' "+"&chek_id_"+ 1 +"='" + $('#chek_id_'+1).val()+"'";
	  				}
				}
	  			//alert (data_all);return;

	  			//if(!(sub_process_data==93 || sub_process_data==94 || sub_process_data==95 || sub_process_data==96 || sub_process_data==97 || sub_process_data==98 || sub_process_data == 140 || sub_process_data == 141 || sub_process_data == 142 || sub_process_data == 143 ))
						if(check_pro==2) //Wash not exists
	  					{
	  			  			if(i<1)
	  			  			{
	  			  				alert("Please Insert Ratio At Least One Item");
	  			  				return;
	  			  			}
	  					}
	  			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_dyeing_re_process*txt_recipe_no*txt_labdip_no*cbo_company_id*cbo_location*txt_recipe_date*cbo_order_source*txt_booking_no*txt_booking_id*txt_recipe_des*txt_batch_id*cbo_buyer_name*cbo_method*txt_color*txt_color_id*cbo_color_range*txt_batch_weight*txt_liquor*txt_batch_ratio*txt_liquor_ratio*cbo_sub_process*txt_remarks*update_id*txt_total_liquor_ratio*txt_liquor_ratio_dtls*cbo_lc_company_id*cbo_store_name*txt_subprocess_seq*txt_pick_up*surpls_solution*cbo_ready_to_approve*cbo_machine_name*txt_in_charge_id*check_id*copy_id*update_id_check*txt_copy_from*txt_sub_tank',"../")+data_all+'&total_row='+i;

			//alert (data);//return;
			freeze_window(operation);
			http.open("POST","requires/dyeing_re_process_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fnc_recipe_entry_Reply_info;
		}
	}

	function fnc_recipe_entry_Reply_info()
	{
		if(http.readyState == 4)
		{
			//release_freezing();
			//alert(http.responseText);return;
			var reponse=trim(http.responseText).split('**');
			show_msg(trim(reponse[0]));
			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_sys_id').value = reponse[1];
				var recipe_id = $('#txt_recipe_no').val();
				var dyeing_re_process = $('#cbo_dyeing_re_process').val();
				show_list_view(reponse[1], 'recipe_item_details', 'recipe_items_list_view', 'requires/dyeing_re_process_entry_controller', '' ) ;
				show_list_view(reponse[1]+"**"+recipe_id+"**"+dyeing_re_process,'recipe_items','recipe_items','requires/dyeing_re_process_entry_controller','');
				$('#list_container_recipe_items').html('');
				 
				$('#copy_id').removeAttr('disabled','disabled');
				$('#copy_id').attr('checked', false);
				document.getElementById('copy_id').value=2;

				$('#cbo_sub_process').val(0);
				$('#txt_total_liquor_ratio').val('');
				$('#txt_liquor_ratio_dtls').val('');
				$('#txt_subprocess_remarks').val('');
				if(reponse[0]==2)
				{
					release_freezing();
					fnResetForm();
				}

				disable_enable_fields('cbo_dyeing_re_process*cbo_company_id*txt_recipe_no*cbo_store_name',1);
				set_button_status(0, permission, 'fnc_recipe_entry',1,1);
			}
			else if(reponse[0]==11)
			{
				alert('Duplicate Sub Process Not Alllowed.');
			}
			else if(reponse[0]==14)
			{
				alert('Bellow Dyes And Chemical Issue Requisition Found. Change Not Allowed.\n Requisition No: '+reponse[1]);
			}
			else if(reponse[0]==13)
			{
				alert('Already Unload Done,Topping adding stripping recipe not allowed.');
			}
			else if(reponse[0]==15)
			{
				alert(reponse[2]);
				release_freezing();
				return;
			}
			release_freezing();
		}
	}

	function openmypage_recipeNo()
	{
		var cbo_dyeing_re_process = $('#cbo_dyeing_re_process').val();
		var cbo_company_id = $('#cbo_company_id').val();

		if (form_validation('cbo_dyeing_re_process','Dyeing Re-Process')==false)
		{

			return;
		}
		else
		{
			var title = 'Recipe Selection Form';
			var page_link='requires/dyeing_re_process_entry_controller.php?cbo_company_id='+cbo_company_id+'&cbo_dyeing_re_process='+cbo_dyeing_re_process+'&action=recipe_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1020px,height=390px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var recipe_data=this.contentDoc.getElementById("hidden_recipe_id").value;	 //Access form field with id="emailfield"
				//alert(recipe_id);
				var recipe_data=recipe_data.split("_");
				recipe_id=recipe_data[0];
				batch_id=recipe_data[1];
				batch_no=recipe_data[2];

				if(recipe_id!="")
				{
					freeze_window(5);
					$('#list_container_recipe_items').html('');

					if(cbo_dyeing_re_process==1)
					{
						var data =return_global_ajax_value(batch_no, 'load_batch', '', 'requires/dyeing_re_process_entry_controller');
						var batchNos = eval("(" + data + ")");

						$("#txt_batch_no option[value!='0']").remove();
						for(var i=0; i<batchNos.length; i++)
						{
							$("#txt_batch_no").append("<option value='"+batchNos[i].id+"'>"+batchNos[i].name+"</option>");
						}
						get_php_form_data(recipe_id, "populate_data_from_recipe_search_popup", "requires/dyeing_re_process_entry_controller" );
					}
					else
					{
						get_php_form_data(recipe_id, "populate_data_from_search_popup", "requires/dyeing_re_process_entry_controller" );
					}
					var cbo_company_id = $('#cbo_company_id').val();
					load_drop_down('requires/dyeing_re_process_entry_controller', cbo_company_id, 'load_drop_machine', 'td_dyeing_machine' );
					show_list_view(recipe_id+"**"+cbo_dyeing_re_process, 'recipe_item_details', 'recipe_items_list_view', 'requires/dyeing_re_process_entry_controller', '' ) ;
					release_freezing();
				}
			}
		}
	}

	function openmypage_sysNo()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_dyeing_re_process = $('#cbo_dyeing_re_process').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var title = 'System ID Selection Form';
			var page_link = 'requires/dyeing_re_process_entry_controller.php?cbo_company_id='+cbo_company_id+'&action=systemid_popup'+'&cbo_dyeing_re_process='+cbo_dyeing_re_process;

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=390px,center=1,resize=1,scrolling=0','');

			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var update_id=this.contentDoc.getElementById("hidden_update_id").value;	 //Access form field with id="emailfield"

				if(update_id!="")
				{
					freeze_window(5);

					reset_form('recipeEntry_1','list_container_recipe_items*recipe_items_list_view*recipe_items','copy_id','','','check_id');
					get_php_form_data(update_id, "populate_data_from_recipe", "requires/dyeing_re_process_entry_controller" );
					var recipe_id = $('#txt_recipe_no').val();
					var dyeing_re_process = $('#cbo_dyeing_re_process').val();

					show_list_view(update_id, 'recipe_item_details', 'recipe_items_list_view', 'requires/dyeing_re_process_entry_controller', '' ) ;
					show_list_view(update_id+"**"+recipe_id+"**"+dyeing_re_process,'recipe_items','recipe_items','requires/dyeing_re_process_entry_controller','');
					//$('#cbo_store_name').attr('disabled',true); // Disabled no need
					set_button_status(0, permission, 'fnc_recipe_entry',1,1);
					$('#copy_id').removeAttr('disabled','disabled');
					release_freezing();
				}
			}
		}
	}

	function load_batch_data(batch_id)
	{
		get_php_form_data(batch_id, "populate_data_from_batch", "requires/dyeing_re_process_entry_controller" );
	}

	function reset_div()
	{
		$('#list_container_recipe_items').html('');
		$(".accordion_h").each(function()
		{
			 var tid=$(this).attr('id');
			 tid=tid+"span";
			 $('#'+tid).html("+");
		});
		set_button_status(0, permission, 'fnc_recipe_entry',1,0);

		$('#txt_total_liquor_ratio').val('');
		$('#txt_liquor_ratio_dtls').val('');
	}

	function fnc_item_details(sub_process_id, type,process_remark,store_id)
	{
		if (form_validation('txt_batch_no*txt_batch_weight*txt_batch_ratio','Batch No*Batch Weight*Liquor Ratio')==false)
		{
			return;
		}

		$(".accordion_h").each(function() {
			 var tid=$(this).attr('id');
			 tid=tid+"span";
			 $('#'+tid).html("+");
		});

		$('#accordion_h'+sub_process_id+'span').html("-");
		var cbo_dyeing_re_process = $('#cbo_dyeing_re_process').val();
		var cbo_company_id= $('#cbo_company_id').val();
		var recipe_id=$('#txt_recipe_no').val();
		var copy_id=$('#copy_id').val();
	
	

		var txt_batch_weight = $('#txt_batch_weight').val();
		//var total_liquor= $('#txt_liquor').val();

		var actual_batch_weight = $('#txt_actual_batch_wgt').val();

		$('#cbo_sub_process').val(sub_process_id);
		$('#cbo_store_name').val(store_id);
		var update_id=$('#update_id').val();
		$('#cbo_store_name').attr("disabled",true);

		var update_id='';
		if(type==1)
		{
			 update_id= $('#update_id').val();
			if(update_id=='')
			{
				var update_id= $('#update_id_check').val();
			}
		   
		}
		else
		{
			update_id='';
		}
		//alert(update_id);
		 if(subprocessForWashArr.indexOf(sub_process_id) !== -1)  //For Wash/Migration
		  {
			  var check_pro=1;
		  }
		  else
		  { 
			var check_pro=2;
		  }
		  
		//if(sub_process_id==93 || sub_process_id==94 || sub_process_id==95 || sub_process_id==96 || sub_process_id==97 || sub_process_id==98 || sub_process_id == 140 || sub_pr//ocess_id == 141 || sub_process_id == 142 || sub_process_id == 143 )
		if(check_pro==1)
		{
			$("#txt_subprocess_remarks").removeAttr("disabled",true);
		}
		else
		{
			$("#txt_subprocess_remarks").removeAttr("disabled",true);
			$('#txt_subprocess_remarks').val('');
		}
		$('#cbo_sub_process').val(sub_process_id);
		$('#txt_subprocess_remarks').val(process_remark);
		var sub_process = $('#cbo_sub_process').val();
		var copy_val=$('#copy_id').val()*1;

		subprocess_change(sub_process);
		var total_liquor= $('#txt_total_liquor_ratio').val();
		var actual_total_liquor= $('#hide_actual_liquor').val();
		var cbo_store_name=$('#cbo_store_name').val();
		
		var checked_id= $('#check_id').val();
		lib_check_sub(checked_id);
		var check_id= $('#check_id').val();
		//alert(check_id);
		show_list_view(cbo_company_id+'**'+sub_process_id+"**"+update_id+"**"+recipe_id+"**"+cbo_dyeing_re_process+"**0**"+txt_batch_weight+"**"+total_liquor+"**"+actual_batch_weight+"**"+actual_total_liquor+"**"+cbo_store_name+"**"+check_id+"**"+copy_val, 'item_details', 'list_container_recipe_items', 'requires/dyeing_re_process_entry_controller', '');

		setFilterGrid('tbl_list_search',-1);

		// if(type==1)
		// {
		// 	if(update_id=="") set_button_status(0, permission, 'fnc_recipe_entry',1,1);
		// 	else set_button_status(1, permission, 'fnc_recipe_entry',1,1);
		// }
		// else
		// {
		// 	set_button_status(0, permission, 'fnc_recipe_entry',1,1);
		// }
		if(copy_val==1)
		{
			caculate_tot_liquor(1);
		}
		//alert(update_id);
		if(copy_val==2 && update_id=="")
		{
			//set_button_status(1, permission, 'fnc_recipe_entry',1,1);
			set_button_status(0, permission, 'fnc_recipe_entry',1,0);
		}
		else if(copy_val==2 && update_id)
		{
			set_button_status(1, permission, 'fnc_recipe_entry',1,1);
			 
		}
		else
		{
			set_button_status(0, permission, 'fnc_recipe_entry',1,0);
		}
	}

	function openmypage_itemLot(id)
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var txt_prod_id = $('#product_id_'+id).val();
		var txt_item_lot = $('#txt_item_lot_'+id).val();
		//alert  (txt_prod_id);
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var title = 'Item Lot Selection Form';
			var page_link = 'requires/dyeing_re_process_entry_controller.php?cbo_company_id='+cbo_company_id+'&txt_prod_id='+txt_prod_id+'&txt_item_lot='+txt_item_lot+'&action=itemLot_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=350px,height=390px,center=1,resize=1,scrolling=0','');

			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var item_lot=this.contentDoc.getElementById("item_lot").value;
				if(item_lot!="")
				{
					freeze_window(5);
					document.getElementById("txt_item_lot_"+id).value=item_lot;
					release_freezing();
				}
			}
		}
	}

	function calculate(i,field_id)
	{
		//var total_liquor=$("#txt_liquor").val()*1;
		var total_liquor=$("#txt_total_liquor_ratio").val()*1;
		var batch_weight=$("#txt_batch_weight").val()*1;
		var cbo_dose_base=$("#cbo_dose_base_"+i).val()*1;
		var txt_ratio=$("#txt_ratio_"+i).val()*1;
		var txt_adj_ratio=$("#txt_adj_ratio_"+i).val()*1;
		var txt_adj_per = $("#txt_adj_per_"+i).val()*1;
		var cbo_adj_type = $("#cbo_adj_type_"+i).val();
	    var adj_qnty=$("#adj_qnty_"+i).val()*1;
		var recipe_qty=$("#recipe_qty_"+i).text()*1; 
		var hidd_adj_qnty=$("#hiddenadj_qnty_"+i).val()*1;
		var hidd_adj_ratio=$("#txt_hiddadj_ratio_"+i).val()*1;
		var stock_qty=$('#stock_qty_'+i).text()*1;
		//alert(txt_adj_per);adj_qnty_
		if(stock_qty<=0)
		{
			alert("No Stock Qty.");
			$("#txt_adj_ratio_"+i).val('');
			$("#txt_adj_per_"+i).val('');
			$("#adj_qnty_"+i).val('');
			return;
		}

		if(field_id=="txt_adj_per_")
		{
			var ratio=(txt_adj_per/100)*txt_ratio;

			if(cbo_dose_base==1)
			{
				//alert(ratio);
				var adj_qnty=(total_liquor*ratio)/1000;
			}
			else
			{

				var adj_qnty=(batch_weight*ratio)/100;
					//alert(ratio+'='+adj_qnty);
			}

			$('#txt_adj_ratio_'+i).val(ratio.toFixed(6));
			$('#adj_qnty_'+i).val(adj_qnty.toFixed(6));
			if(adj_qnty>stock_qty)
			{
				alert('Req. qty  is not allowed greater then stock qty.');
				$('#txt_adj_per_'+i).val('');
				$('#adj_qnty_'+i).val(hidd_adj_qnty);
				$('#txt_adj_per_'+i).focus();
				$('#txt_seqno_'+i).val('');
				return;
			}
		}
		else if(field_id=="adj_qnty_")
		{
			var adj_per=(adj_qnty*100)/recipe_qty;
			var ratio=(adj_per/100)*txt_ratio;

			$('#txt_adj_ratio_'+i).val(ratio.toFixed(6));
			$('#txt_adj_per_'+i).val(adj_per.toFixed(6));

			if(adj_qnty>stock_qty)
			{
				alert('Req. qty is not allowed greater then stock qty.');
				 
				$('#txt_adj_per_'+i).val('');
				$('#adj_qnty_'+i).val(hidd_adj_qnty);
				$('#adj_qnty_'+i).focus();
				$('#txt_seqno_'+i).val('');
				return;
			}

		}
		else if(field_id=="txt_adj_ratio_")
		{
			if(cbo_dose_base==1)
			{
				var adj_qnty=(total_liquor*txt_adj_ratio)/1000;

				var adj_per=(adj_qnty*100)/recipe_qty;
				//alert(adj_per);
				if(adj_per=='Infinity')
				{
					$('#txt_adj_per_'+i).val(0);
				}
				else
				{
					$('#txt_adj_per_'+i).val(adj_per.toFixed(6));
				}

				$('#adj_qnty_'+i).val(adj_qnty.toFixed(6));
			}
			else
			{
				var adj_qnty=(batch_weight*txt_adj_ratio)/100;
				var adj_per=(batch_weight*100)/recipe_qty;

				if(adj_per=='Infinity')
				{
					$('#txt_adj_per_'+i).val(0);
				}
				else
				{
					$('#txt_adj_per_'+i).val(adj_per.toFixed(6));
				}

				$('#adj_qnty_'+i).val(adj_qnty.toFixed(6));

			}
			if(adj_qnty>stock_qty)
			{
				alert('Req. qty is not allowed greater then stock qty.');
				 
				$('#txt_adj_per_'+i).val('');
				$('#txt_adj_ratio_'+i).val(hidd_adj_ratio);
				$('#adj_qnty_'+i).val(hidd_adj_qnty);
				$('#txt_adj_ratio_'+i).focus();
				$('#txt_seqno_'+i).val('');
				return;
			}
			
		}
		else if(field_id=="txt_ratio_")
		{
			if(cbo_dose_base==1)
			{
				var adj_qnty=(total_liquor*txt_ratio)/1000;

				var adj_per=(adj_qnty*100)/recipe_qty;
				//alert(total_liquor+'='+txt_ratio);
				if(adj_per=='Infinity')
				{
					$('#txt_adj_per_'+i).val(0);
				}
				else
				{
					$('#txt_adj_per_'+i).val(adj_per.toFixed(6));
				}

				$('#adj_qnty_'+i).val(adj_qnty.toFixed(6));
			}
			else
			{
				var adj_qnty=(batch_weight*txt_ratio)/100;
				var adj_per=(batch_weight*100)/recipe_qty;
				//alert(recipe_qty);
				if(adj_per=='Infinity')
				{
					$('#txt_adj_per_'+i).val(0);
				}
				else
				{
					$('#txt_adj_per_'+i).val(adj_per.toFixed(6));
				}

				$('#adj_qnty_'+i).val(adj_qnty.toFixed(6));

			}
			if(adj_qnty>stock_qty)
			{
				alert('Req. qty is not allowed greater then stock qty.');
				 
				$('#adj_qnty_'+i).val(hidd_adj_qnty);
				 
				$('#txt_ratio_'+i).val('');
				$('#txt_seqno_'+i).val('');
				$('#txt_ratio_'+i).focus();
				return;
			}
		}
	}

	function color_row(tr_id)
	{
		var txt_ratio=$('#txt_ratio_'+tr_id).val()*1;
		var stock_check=$('#stock_qty_'+tr_id).text()*1;

		if(form_validation('cbo_dose_base_'+tr_id,'Dose Base')==false)
		{
			$('#txt_ratio_'+tr_id).val('');
			return;
		}
		else
		{
			if(txt_ratio>0)
			{
				if(stock_check<=0)
				{
					//$('#txt_ratio_' + tr_id).css('background-color','Red');
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
			else
			{
				$('#search' + tr_id).css('background-color','#FFFFCC');
				$('#txt_ratio_' + tr_id).css('background-color','White');
			}
		}

		/*//alert (stock_check);
		if(txt_ratio<=0)
		{
			$('#txt_ratio_'+tr_id).val('');
			$('#txt_ratio_' + tr_id).css('background-color','White');
			return;
		}

		if(stock_check<=0)
		{
			$('#txt_ratio_' + tr_id).css('background-color','Red');
		}
		else
		{
			$('#txt_ratio_' + tr_id).css('background-color','White');
		}*/
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

	function load_sub_process()
	{
		/*var cbo_dyeing_re_process=$('#cbo_dyeing_re_process').val();
		if(cbo_dyeing_re_process==3)
		{
			$("#cbo_sub_process option[value='90']").remove();
			if($('#cbo_sub_process option:last').val()!=70)
			{
				$("#cbo_sub_process").append('<option value="70">Color Remove</option><option value="90">Others</option>');
			}
		}
		else
		{
			$("#cbo_sub_process option[value='70']").remove();
			$("#cbo_sub_process").val(0);
		}*/

		var cbo_dyeing_re_process=$('#cbo_dyeing_re_process').val();
		reset_form('recipeEntry_1','list_container_recipe_items*recipe_items_list_view*batch_type','','','','txt_sys_id*cbo_dyeing_re_process*cbo_company_id*cbo_location*cbo_method*txt_remarks*check_id');

		if(cbo_dyeing_re_process==1)
		{
			$('#batch_td').html('<select name="txt_batch_no" id="txt_batch_no" class="combo_boxes" onChange="load_batch_data(this.value);" style="width:80px"><option value="0">-- Select --</option></select>');
		}
		else
		{
			$('#batch_td').html('<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:70px;" placeholder="Display" disabled />');
		}
	}

	function caculate_tot_liquor()
	{
		var batch_weight=$('#txt_batch_weight').val();
		var tot_liquor=($('#txt_batch_ratio').val()*1)*($('#txt_liquor_ratio').val()*1)*(batch_weight*1);
		$('#txt_liquor').val(tot_liquor);

		var tot_liquor_dtls=($('#txt_batch_ratio').val()*1)*($('#txt_liquor_ratio_dtls').val()*1)*(batch_weight*1);
		$('#txt_total_liquor_ratio').val(tot_liquor_dtls);
	}


	function subprocess_change(sub_process)
	{
		var cbo_company_id=$('#cbo_company_id').val();
		var recipe_no=$('#txt_recipe_no').val();
		var update_id=$('#update_id').val();
		var update_id_check=$('#update_id_check').val();

		//if(sub_process==93 || sub_process==94 || sub_process==95 || sub_process==96 || sub_process==97 || sub_process==98 || sub_process == 140 || sub_process == 141 || sub_process == 142 || sub_process == 143 )
		 if(subprocessForWashArr.indexOf(sub_process) !== -1) 
		{
			$("#txt_subprocess_remarks").removeAttr("disabled",true);
			$('#txt_subprocess_remarks').focus();
			$('#show').css('display','none');


		}
		else
		{
			//$("#txt_subprocess_remarks").attr("disabled",true);
			$("#txt_subprocess_remarks").removeAttr("disabled",true);
			//$('#txt_subprocess_remarks').val('');
			$('#txt_subprocess_remarks').focus();
			$('#show').css('display','block');
		}

		get_php_form_data(cbo_company_id+'**'+sub_process+'**'+recipe_no+'**'+update_id+'**'+update_id_check, "ratio_data_from_dtls", "requires/dyeing_re_process_entry_controller" );
	}

function chk_items_fnc()
	{

	 //alert('ok');

	}

/*function check_all(tot_check_box_id)
{
	if ($('#'+tot_check_box_id).is(":checked"))
	{
		$('#tbl_list_search tbody tr').each(function() {
			$('#tbl_list_search tbody tr input:checkbox').attr('checked', true);
		});
	}
	else
	{
		$('#tbl_list_search tbody tr').each(function() {
			$('#tbl_list_search tbody tr input:checkbox').attr('checked', false);
		});
	}
}*/
//auto check when ration insert
function auto_put_check(k)
{
//alert(k);
var row_numm=$('#tbl_list_search tbody tr').length-1;
for(var k; k<=row_numm; k++)
	{
		var txt_ratios=$('#txt_ratio_'+k).val();
		if(txt_ratios!=""){
				$('#chek_'+k).attr('checked', true);
			}
	}
}
function fnc_chk_status(i)
{
	if($('#chek_'+i).is(":checked"))
	{
		$('#chek_id_'+i).val(1);
	}
	else
	{
		$('#chek_id_'+i).val(0)
	}
}
function fn_recipe_calc(operation)
{
	// alert(operation);
	if($('#update_id').val()!="")
	{
	   	if(operation=="btn_recipe_calc")
		{
            //alert(operation);return;
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_labdip_no').val()+'*'+$('#txt_yarn_lot').val()+'*'+$('#txt_brand').val()+'*'+$('#txt_count').val()+'*'+$('#txt_pick_up').val()+'*'+$('#surpls_solution').val()+'*'+$('#txt_batch_id').val()+'*'+$('#cbo_sub_process').val()+'*'+report_title, "recipe_entry_print_2", "requires/dyeing_re_process_entry_controller")
			//return;
			show_msg("3");
		}
		if(operation=="btn_recipe_calc_4")
		{
            var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_labdip_no').val()+'*'+report_title, "recipe_entry_print_4", "requires/dyeing_re_process_entry_controller")
			//return;
			show_msg("3");
		}
		if(operation=="btn_recipe_calc_5")
		{
            var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_labdip_no').val()+'*'+report_title, "recipe_entry_print_5", "requires/dyeing_re_process_entry_controller")
			//return;
			show_msg("3");
		}
		if(operation=="btn_recipe_calc_9")
		{
            var report_title='Topping Adding Stripping Recipe Entry';
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_labdip_no').val()+'*'+report_title+'*'+$('#cbo_company_id').val(), "recipe_entry_print_9", "requires/dyeing_re_process_entry_controller")
			//return;
			show_msg("3");
		}
		if(operation=="btn_recipe_calc_10")
		{
            var report_title='Topping Adding Stripping Recipe Entry';
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_labdip_no').val()+'*'+report_title+'*'+$('#cbo_company_id').val(), "recipe_entry_print_10", "requires/dyeing_re_process_entry_controller")
			//return;
			show_msg("3");
		}
	}
}
	function lib_check_sub(type)
	{
		if ( document.getElementById('check_id').checked==true)
		{
			var chk=document.getElementById('check_id').value=1;
			//set_button_status(0, permission, 'fnc_recipe_entry',1,1);
			//alert(chk );
		}
		else if(document.getElementById('check_id').checked==false)
		{
			var chk=document.getElementById('check_id').value=2;
		}
		//alert(chk );
	}
	function fnc_incharge_name()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		 
		var txt_batch_no = $('#txt_batch_no').val();
		var txt_recipe_no = $('#txt_recipe_no').val();
		 
		 
		if (form_validation('txt_recipe_no*txt_batch_no','Recipe No*Batch No')==false)
		{
			return;
		}
		else
		{
			var page_link='requires/dyeing_re_process_entry_controller.php?cbo_company_id='+cbo_company_id+'&txt_batch_no='+txt_batch_no+'&action=incharge_name_popup';
			var title='In Charge Info From Library';
			//alert(page_link);

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=290px,height=270px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var incharge_hdn=this.contentDoc.getElementById("incharge_hdn").value;
				if(trim(incharge_hdn)!="")
				{
					incharge_data=incharge_hdn.split("_");
					charge_id=incharge_data[0];
					charge_name=incharge_data[1];
					freeze_window(5);
					$('#txt_in_charge').val(charge_name);
					$('#txt_in_charge_id').val(charge_id);
					release_freezing();
				}
			}
		}
	}

	function copy_check(type)
	{
		var recipe_prev_id=$('#txt_sys_id').val();
		var working_company_id = $('#cbo_company_id').val();
		var recipe_date = $('#txt_recipe_date').val();

		if(type==1)
		{

				// var response = return_global_ajax_value( working_company_id+'**'+recipe_date, 'recipe_previ_copy_check', '', 'requires/recipe_entry_controller');
				// if(response=="")
				// {
				// 	alert('Restricted, please select recipe from 7th march, 19 onward to copy.');
				// 	$('#copy_id').attr('checked', false);
				// 	document.getElementById('copy_id').value=2;
				// 	return;
				// }

			$("#list_container_recipe_items").html('');
			show_list_view($('#txt_sys_id').val(), 'recipe_item_details', 'recipe_items_list_view', 'requires/dyeing_re_process_entry_controller', '' ) ;
			$('#update_id').val('');
			$('#txt_sys_id').val('');
			$('#txt_batch_no').val(0);
			$('#txt_recipe_date').val('');
			$('#txt_labdip_no').val('');
			//$('#txt_recipe_no').val('');
			//$('#txt_batch_no').val('');
			//$('#txt_batch_id').val('');
			//$('#txt_batch_weight').val('');
			$('#txt_booking_no').val('');
			$('#txt_booking_id').val('');
			$('#txt_color').val('');
			$('#txt_color_id').val('');
			$('#txt_trims_weight').val('');
			$('#txt_yarn_lot').val('');
			$('#txt_brand').val('');
			$('#txt_count').val('');
			$('#txt_order').val('');
			$("#cbo_machine_name").attr("disabled",false);
			$("#txt_recipe_no").attr("disabled",false);

		}
		if(type==1)
		{
			$('#txt_copy_from').val(recipe_prev_id);

		}
		else
		{
			$('#txt_copy_from').val('');

		}

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
		//alert(type );
	}
	function fnResetForm()
	{
		//alert(33);
		$("#cbo_company_id").attr("disabled",false);
		  $('#copy_id').val(2);
		//reset_form('recipeEntry_1','list_container_recipe_items*recipe_items_list_view','',$('#copy_id').val()*2,'variable_lot');
		reset_form('recipeEntry_1','list_container_recipe_items*recipe_items_list_view*recipe_items','','','disable_enable_fields(\'cbo_company_id*cbo_dyeing_re_process*txt_recipe_no*txt_total_liquor_ratio*txt_liquor_ratio_dtls\',0)');
	}
</script>
</head>

<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",$permission); ?>
	<fieldset style="width:1300px;">
	<legend>Recipe Entry</legend>
		<form name="recipeEntry_1" id="recipeEntry_1">
			<fieldset style="width:1110px; margin-bottom:10px">
            	<table width="1100"><tr><td width="910" valign="top">
                    <table width="910" border="0" cellpadding="2" cellspacing="2">
                        <tr>
                            <td colspan="3" align="right"><strong>System ID</strong></td>
                            <td align="left">
                                <input type="text" name="txt_sys_id" id="txt_sys_id" class="text_boxes" style="width:140px;" placeholder="Double click to search" onDblClick="openmypage_sysNo();" readonly />
                                <input type="hidden" name="update_id" id="update_id"/>
								<input type="hidden" name="update_id_check" id="update_id_check"/>
                            </td>
                            <td colspan="2">
							 
                       		<strong>Copy</strong>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="copy_id" id="copy_id" onClick="copy_check(1)" value="2" disabled > &nbsp;&nbsp;
                        
                                <div id="batch_type" style="color:#F00; font-size:18px"></div>
                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption" width="120">Dyeing Re-Process 123</td>
                            <td>
                                <?
                                    echo create_drop_down("cbo_dyeing_re_process", 152, $dyeing_re_process,"", 1,"-- Select Re_Process --", 0,"load_sub_process();");
                                ?>
                            </td>
                            <td class="must_entry_caption" width="120">Recipe No</td>
                            <td>
                                <input type="text" name="txt_recipe_no" id="txt_recipe_no" class="text_boxes" style="width:140px;" placeholder="Double click to search" onDblClick="openmypage_recipeNo();" readonly/>
                            </td>
                            <td>Actual Batch Weight</td>
                            <td>
                                <input type="text" name="txt_actual_batch_wgt" id="txt_actual_batch_wgt" class="text_boxes_numeric" style="width:140px;" placeholder="Display" disabled/>
                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Labdip No</td>
                            <td>
                                <input type="text" name="txt_labdip_no" id="txt_labdip_no" class="text_boxes" style="width:140px;" />
                            </td>
                             <td>Recipe Description</td>
                            <td>
                                <input type="text" name="txt_recipe_des" id="txt_recipe_des" class="text_boxes" style="width:140px;" />
                            </td>
                            <td class="must_entry_caption" width="110">Batch No</td>
                            <td>
                                <span id="batch_td">
                                	<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:70px;" placeholder="Display" disabled />
                                </span>
                                <input type="text" name="txt_batch_weight" id="txt_batch_weight" class="text_boxes_numeric" style="width:58px;" onKeyUp="caculate_tot_liquor()"/>
                            	<input type="hidden" name="txt_batch_id" id="txt_batch_id" style="width:40px;" />

                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Working Company</td>
                            <td>
                                <?
                                    echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down('requires/dyeing_re_process_entry_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down('requires/dyeing_re_process_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td_id' );load_room_rack_self_bin('requires/dyeing_re_process_entry_controller*5_6_7_23', 'store','store_td', this.value);get_php_form_data( this.value, 'company_wise_report_button_setting','requires/dyeing_re_process_entry_controller' );load_drop_down('requires/dyeing_re_process_entry_controller', this.value, 'load_drop_machine', 'td_dyeing_machine' );" );
                                ?>
                            </td>
                            <td>Location</td>
                            <td id="location_td">
                                <?
                                    echo create_drop_down("cbo_location", 152, $blank_array,"", 1,"-- Select Location --", 0,"");
                                ?>
                            </td>
                            <td class="must_entry_caption">Recipe Date</td>
                            <td>
                                <input type="text" name="txt_recipe_date" id="txt_recipe_date" class="datepicker" style="width:140px;" readonly tabindex="6" />
                            </td>
                        </tr>
                        <tr>
                            <td>Order Source</td>
                            <td>
                                <?
                                    echo create_drop_down("cbo_order_source", 152, $order_source,"", 1,"-- Select Source --", $selected,"",1,"","","","");
                                ?>
                            </td>
                            <td>Buyer Name</td>
                            <td id="buyer_td_id">
                                <?
                                   echo create_drop_down( "cbo_buyer_name", 152, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0 );
                                ?>
                            </td>
                            <td>Booking</td>
                            <td>
                                <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:140px;" disabled/>
                                <input type="hidden" name="txt_booking_id" id="txt_booking_id" class="text_boxes" style="width:120px;" />
                            </td>
                        </tr>
                        <tr>
                            <td>Color</td> <!--class="must_entry_caption"-->
                            <td>
                                <input type="text" name="txt_color" id="txt_color" class="text_boxes" value="" style="width:140px;" tabindex="10" disabled />
                                <input type="hidden" name="txt_color_id" id="txt_color_id" class="text_boxes" value="" style="width:120px;"/>
                            </td>
                            <td>Color Range</td>
                            <td>
                                <?
                                    echo create_drop_down("cbo_color_range", 152, $color_range,"", 1,"-- Select --", '','',1);
                                ?>
                            </td>
                            <td>Method</td>
                            <td>
                                <?
                                   echo create_drop_down( "cbo_method", 152, $dyeing_method,"", 1, "--Select Method--", $selected, "",0 );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Batch Ratio</td>
                            <td colspan="0">
                                <input type="text" name="txt_batch_ratio" id="txt_batch_ratio" class="text_boxes_numeric" value="" style="width:140px;" placeholder="Batch" onKeyUp="caculate_tot_liquor()" /><strong></strong><input type="text" name="txt_liquor_ratio" id="txt_liquor_ratio" class="text_boxes_numeric" value="" style="width:62px;display:none;" placeholder="Liquor" onKeyUp="caculate_tot_liquor()" />
                            </td>
                            <td style="display:none">Total Liquor(ltr)</td>
                            <td style="display:none">
                                <input type="text" name="txt_liquor" id="txt_liquor" class="text_boxes_numeric" value="" style="width:140px;display:none" readonly disabled />
                                <input type="hidden" name="hide_actual_liquor" id="hide_actual_liquor" />
                            </td>
                            <td>Trims Weight</td>
                            <td>
                                <input type="text" name="txt_trims_weight" id="txt_trims_weight" class="text_boxes_numeric" value="" style="width:140px;" disabled />
                            </td>
                            <td>Yarn Lot</td>
                            <td colspan="0">
                                <input type="text" name="txt_yarn_lot" id="txt_yarn_lot" class="text_boxes" value="" style="width:140px;" disabled />
                            </td>
                        </tr>
                        <tr>

                            <td>Brand</td>
                            <td>
                                <input type="text" name="txt_brand" id="txt_brand" class="text_boxes" value="" style="width:140px;" disabled />
                            </td>
                            <td>Count</td>
                            <td>
                                <input type="text" name="txt_count" id="txt_count" class="text_boxes" value="" style="width:140px;" disabled />
                            </td>
                             <td>Order No.</td>
                            <td>
                                <input type="text" name="txt_order" id="txt_order" class="text_boxes" value="" style="width:140px;" disabled />
                            </td>
                        </tr>
                         <tr>

                            <td>Pick Up(%)</td>
                            <td>
                                <input type="text" name="txt_pick_up" id="txt_pick_up" class="text_boxes_numeric" style="width:140px;" />
                            </td>
                            <td>Surplus Solution</td>
                            <td>
                                <input type="text" name="surpls_solution" id="surpls_solution" class="text_boxes_numeric" style="width:140px;" />
                            </td>
                            <td>Ready To Approve</td>
                            <td>
                                <?php
                                	echo create_drop_down('cbo_ready_to_approve', 152, $yes_no, '', 1, '-- Select --', 0, '');
                                ?>
                            </td>
                        </tr>
                        <tr>

                            <td>Remarks</td>
                            <td>
                                <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" value="" style="width:140px;" /> 
                            </td>
							<td id="">In-Charge</td>
							<td>
							<input type="text" name="txt_in_charge" id="txt_in_charge" placeholder="Browse" onDblClick="fnc_incharge_name(); " class="text_boxes" style="width:140px" readonly >
							<input type="hidden" name="txt_in_charge_id" id="txt_in_charge_id" class="text_boxes" style="width:40px" disabled="disabled">
							</td>

							<td>Machine No</td>
							<td id="td_dyeing_machine">
								<?
									echo create_drop_down("cbo_machine_name", 152, $blank_array,"", 1,"-- Select --", '','',1);
								?>
							</td>
                            <td>
                            	<div id="approved_mst" style="color:#F00; float:right; font-size:20px"></div> 
                            </td>
							<td style="display:none">LC Company</td>
                            <d style="display:none">
                                <?
								//load_drop_down('requires/dyeing_re_process_entry_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down('requires/dyeing_re_process_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td_id' );
                                    echo create_drop_down( "cbo_lc_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "" );
                                ?>
                            </td>
                       </tr>
					   <tr>
                         <td>Copy From</td>
                        <td>
                            <input type="text" name="txt_copy_from" id="txt_copy_from" class="text_boxes" value="" style="width:140px;" disabled="disabled" />
                        </td>
                        <td>Sub Tank</td>
                        <td>
                            <input type="text" name="txt_sub_tank" id="txt_sub_tank" class="text_boxes_numeric" value="" style="width:140px;" />

                        </td>
                   </tr>
                    </table>
                </td><td valign="top"><div style="margin-top:20px" id="recipe_items"></div></td></tr></table>
			</fieldset>
            <fieldset style="width:1300px; margin-top:10px">
            <legend>Item Details</legend>
 				<table cellpadding="0" cellspacing="0" border="0" width="75%">
                	<tr>
                    	<td width="80" class="must_entry_caption"><b>Sub Process</b></td>
                    	<td width="160" align="left">
							<?
                                echo create_drop_down( "cbo_sub_process", 160, $dyeing_sub_process,"", 1, "-- Select Sub Process --", 0, "reset_div();subprocess_change(this.value);","0");
                            ?>
                        </td>
                        <td width="35"> <input type="text" name="txt_subprocess_seq" id="txt_subprocess_seq" class="text_boxes_numeric" style="width:35px;" placeholder="Seq.No" /></td>
                        <td width="70" class="must_entry_caption">Store Name</td>
                    	<td width="160" id="store_td">
							<?
                                 echo create_drop_down( "cbo_store_name", 160, "select lib_store_location.id,lib_store_location.store_name from lib_store_location,lib_store_location_category where lib_store_location.id=lib_store_location_category.store_location_id and lib_store_location.status_active=1 and lib_store_location.is_deleted=0  and lib_store_location_category.category_type in(5,6,7,23) group by lib_store_location.id,lib_store_location.store_name order by lib_store_location.store_name","id,store_name", 1, "-- Select Store --", $storeName, "" );
                            ?>
                        </td>
                        <td style=""> <input type="text" name="txt_subprocess_remarks" id="txt_subprocess_remarks" class="text_boxes" value="" style="width:90px;" disabled /></td>
                        <td style="" width="80">Liquor Ratio</td>
                        <td style=""> <input type="text" name="txt_liquor_ratio_dtls" id="txt_liquor_ratio_dtls" class="text_boxes_numeric" value=""  onBlur="caculate_tot_liquor()" style="width:40px;" />&nbsp;Total Liquor(ltr) &nbsp;<input type="text" name="txt_total_liquor_ratio" id="txt_total_liquor_ratio" class="text_boxes_numeric" value="" style="width:50px;" disabled /><input type="checkbox" name="check_id" id="check_id" onClick="lib_check_sub(1)" value="1"  checked></td>
                        <td colspan="2"><input type="button" value="Show Items" name="show" id="show" class="formbuttonplasminus" style="width:70px" onClick="show_details()"/></td>                	</tr>
                </table>
                <div id="list_container_recipe_items" style="margin-top:10px"></div>
            </fieldset>
            <table width="910">
            	<tr>
                    <td colspan="4" align="center" class="button_container">
						<?
						echo create_drop_down( "cbo_template_id", 100, $report_template_list,'', 0, '', 0, "check_company();");
						?>
						&nbsp;
						<?
                          echo load_submit_buttons($permission,"fnc_recipe_entry", 0,1,"fnResetForm();",1);
                        ?>
                        <input type="button" name="btn_recipe_calc" id="btn_recipe_calc" class="formbutton" value="Print 2" style=" width:100px;display:none;" onClick="fn_recipe_calc(this.id);">
						<input type="button" name="btn_recipe_calc_4" id="btn_recipe_calc_4" class="formbutton" value="Print 4" style=" width:100px;   display:none;" onClick="fn_recipe_calc(this.id);" >
						<input type="button" name="btn_recipe_calc_5" id="btn_recipe_calc_5" class="formbutton" value="Print 5" style=" width:100px;   display:none;" onClick="fn_recipe_calc(this.id);" >
						<input type="button" name="btn_recipe_calc_9" id="btn_recipe_calc_9" class="formbutton" value="Print 9" style=" width:100px;   display:none;" onClick="fn_recipe_calc(this.id);" >
						<input type="button" name="btn_recipe_calc_10" id="btn_recipe_calc_10" class="formbutton" value="Print 10" style=" width:100px;   display:none;" onClick="fn_recipe_calc(this.id);" >
                    </td>
                </tr>
            </table>
		</form>
        <div id="recipe_items_list_view" style="margin-top:10px"></div>
	</fieldset>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
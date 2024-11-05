<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Recipe Entry 
Functionality	:
JS Functions	:
Created by		:	Fuad
Creation date 	: 	21.07.2013
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
echo load_html_head_contents("YD Recipe Entry Info", "../", 1, 1,$unicode,1);

?>
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
var permission='<? echo $permission; ?>';



	function show_details()
	{
		var val=$('#copy_sub_process').val();
		//alert(val);return;
		//var cbo_company_id = $('#cbo_company_id').val();
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_sub_process = $('#cbo_sub_process').val();
		var cbo_store_name = $('#cbo_store_name').val();
		var variable_lot = $('#variable_lot').val();
		if(form_validation('cbo_company_id*cbo_sub_process*cbo_store_name','Company*Sub Process*Store')==false)
		{
			return;
		}
		if(val==2)
		{
			var list_view_orders = return_global_ajax_value( cbo_company_id+'**'+cbo_sub_process+'******'+cbo_store_name+'**'+variable_lot, 'item_details', '', 'requires/yd_recipe_entry_controller');
			if(list_view_orders!='')
			{
				//$("#tbl_list tr").remove();
				$("#list_container_recipe_items").html(list_view_orders);
				set_all_onclick();
				setFilterGrid('tbl_list_search',-1);
			}
		}
		else
		{
			alert("Execute Not Allowed.");
			return;
		}

		//show_list_view(cbo_company_id+'**'+cbo_sub_process, 'item_details', 'list_container_recipe_items', 'requires/yd_recipe_entry_controller','setFilterGrid("tbl_list_search",-1)');
		//set_button_status(0, permission, 'fnc_recipe_entry',1);
	}

	function color_row(tr_id)
	{
		var txt_ratio=$('#txt_ratio_'+tr_id).val()*1;
		var stock_check=$('#stock_qty_'+tr_id).text()*1;
		var sub_process=$('#cbo_sub_process').val();
		var txt_batch_weight=$('#txt_batch_weight').val()*1;
		var cbo_dose_base=$('#cbo_dose_base_'+tr_id).val();
		var txt_total_liquor=$('#txt_total_liquor_ratio').val()*1;
		var variable_stock=$('#variable_stock').val()*1;
		//alert(variable_stock);
		
		
		if(cbo_dose_base==1)
		{
			var recipe_qnty = (txt_total_liquor*txt_ratio)/1000;
		}
		else {
			var recipe_qnty = (txt_batch_weight*txt_ratio)/100;
		}
		//alert(recipe_qnty);
		if(form_validation('cbo_dose_base_'+tr_id,'Dose Base')==false)
		{
			$('#txt_ratio_'+tr_id).val('');
			return;
		}
		else
		{
			if(txt_ratio>0)
			{
				//if(stock_check<=0)
				if(recipe_qnty>stock_check)
				{
					//$('#txt_ratio_' + tr_id).css('background-color','Red');
					if(variable_stock==1)//Stock check
					{
						if(sub_process==93 || sub_process==94 || sub_process==95 || sub_process==96 || sub_process==97 || sub_process==98 )
						{
							$('#txt_ratio_'+tr_id).val();
						}
						else
						{
							//alert("No Stock Qty.");
							alert("Recipe qty should not over the stock.");
							$('#txt_ratio_'+tr_id).val('');
						}
					}

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

	function fnc_recipe_entry(operation)
	{
		
		
		//alert(operation);
		
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_labdip_no').val()+'*'+report_title, "recipe_entry_print", "requires/yd_recipe_entry_controller")
			// return;
			//show_msg("3");
		}
		else if(operation==0 || operation==1 || operation==2)
		{

			/*if(operation==2)
			{
				show_msg('13');
				return;
			}*/
			//alert(1);
			if( form_validation('txt_labdip_no*txt_batch_no*cbo_company_id*txt_recipe_date*txt_color*txt_total_liquor_ratio*cbo_store_name','Labdip No*Batch*Company*Recipe Dateotal Liquor*Store')==false )
			{
				return;
			}
			//alert(2);
			var delete_cause='';
			if(operation==2){
				var cbo_company_id = $('#cbo_company_id').val();
				var update_id_check = $('#update_id_check').val();
				var cbo_sub_process = $('#cbo_sub_process').val();
				var txt_batch_id = $('#txt_batch_id').val();

				var response = return_global_ajax_value( cbo_company_id+'**'+update_id_check+'**'+cbo_sub_process+'**'+txt_batch_id, 'item_req_check_data', '', 'requires/yd_recipe_entry_controller');
				response=trim(response);
				//alert(response);
				var response=response.split("**");

				 if(response[0]==11)
				 {
					alert('Bellow Dyes And Chemical Issue Requisition Found. Change Not Allowed.\n Requisition No: '+response[2]);
					return;
				 }
				 else  if(response[0]==133)
				 {
				 	alert('Bellow Dyeing Re Process OR Recipe Entry For Gmts Wash/Dyeing/Printing Found. Change Not Allowed.\n Recipe No: '+response[2]);
					return;
				 }
				 else  if(response[0]==13)
				 {
				 	alert('Unload and Shade Matched');
					return;
				 }
				delete_cause = prompt("Please enter your delete cause", "");
				if(delete_cause==""){
					alert("You have to enter a delete cause");
					release_freezing();
					return;
				}
				if(delete_cause==null){
					release_freezing();
					return;
				}
				var r=confirm("Press OK to Delete Or Press Cancel");
				if(r==false){
					release_freezing();
					return;
				}
			}

			var copy_val=$('#copy_id').val();
			//alert(3);
			if(copy_val==1) //Copy Item
			{
				var cbo_company_id = $('#cbo_company_id').val();
				var update_id_check = $('#update_id_check').val();
				/*var response = return_global_ajax_value( cbo_company_id+'**'+update_id_check, 'item_stock_details', '', 'requires/yd_recipe_entry_controller');
				var response=response.split("_");
				//alert(response[0]);
				if(response[0]==1)
				{
					var r=confirm("Press  \"Cancel\"  to entry zero stock\nPress  \"OK\"  to  Entry Zero Stock");
					if (r==false)
					{
						//alert('Zero Stock Not Allowed');
						return;
					}

				}*/

				/*var row_num=$('#tbl_list_search tbody tr').length-1;

				for(var j=1; j<=row_num; j++)
				{
					var stockcheck=$('#stock_qty_'+j).text()*1;
					//alert(stockcheck);
					if(stockcheck<=0)
					{
						alert('Stock Zero Qty Found,Not Allowed');
						$('#txt_ratio_'+j).val('');
						$('#txt_seqno_'+j).val('');
						return;
					}
				}*/
			}
			

			var sub_process_data=$("#cbo_sub_process").val()*1;
			if(!(sub_process_data==93 || sub_process_data==94 || sub_process_data==95 || sub_process_data==96 || sub_process_data==97 || sub_process_data==98))
			{
				if( $('#list_container_recipe_items').html() == "")
				{
					alert("Please Select Item");
					return;
				}
			}

			if(!(sub_process_data==93 || sub_process_data==94 || sub_process_data==95 || sub_process_data==96 || sub_process_data==97 || sub_process_data==98))
			{
				//alert(5);
				var row_num=$('#tbl_list_search tbody tr').length-1;
				//alert (row_num);return;
				var data_all=""; var k=1;

				for(var j=1; j<=row_num; j++)
				{
					var txt_ratio=$('#txt_ratio_'+j).val();
					//var updateIdDtls=$('#updateIdDtls_'+j).val();
					//var dose=$('#cbo_dose_base_'+j).val();
					/*if(updateIdDtls!="")
					{
						if (form_validation('txt_ratio_'+j,'Ratio')==false)
						{
							return;
						}
					}*/
					//alert(updateIdDtls+'='+txt_ratio);
					if(txt_ratio*1>0)
					{
						/*if (form_validation('cbo_dose_base_'+j,'Dose Base')==false)
						{
							return;
						}*/
						
						
						//alert($('#txt_seqno_'+j).val()+'==='+j+'==='+row_num);
						data_all+="&txt_seqno_" + k + "='" + $('#txt_seqno_'+j).val()+"'"+"&product_id_" + k + "='" + $('#product_id_'+j).text()+"'"+"&txt_item_lot_" + k + "='" + $('#txt_item_lot_'+j).val()+"&txt_comments_" + k + "='" + $('#txt_comments_'+j).val()+"'"+"&cbo_dose_base_" + k + "='" + $('#cbo_dose_base_'+j).val()+"'"+"&txt_ratio_" + k + "='" + $('#txt_ratio_'+j).val()+"'"+"&updateIdDtls_" + k + "='" + $('#updateIdDtls_'+j).val()+"'"+"&txt_subprocess_remarks"+ "='" + $('#txt_subprocess_remarks').val()+"'";
						k++;
						//alert(data_all+'=');
						//alert(data_all);return;

					}
				}
			}
			else //update_dtls_id
			{
 				var k=0;
				// if($('#txt_comments_'+1).val()=='') var comments='';
				if(operation==0)
				{
					data_all="&txt_subprocess_remarks"+"='" + $('#txt_subprocess_remarks').val()+"'";
				}
				else if(operation==1)
				{
					data_all="&txt_subprocess_remarks"+"='" + $('#txt_subprocess_remarks').val()+"'"+"&updateIdDtls_"+ 1 +"='" + $('#updateIdDtls_'+1).val()+"'"+"&txt_comments_"+ 1 +"='" + $('#txt_comments_'+1).val()+"'"+"&txt_ratio_"+ 1 +"='" + $('#txt_ratio_'+1).val()+"'"+"&txt_seqno_"+ 1 +"='" + $('#txt_seqno_'+1).val()+"'"+"&cbo_dose_base_"+ 1 +"='" + $('#cbo_dose_base_'+1).val()+"'";
				}
			}
			//alert(data_all);return;
			/*if(!(sub_process_data==93 || sub_process_data==94 || sub_process_data==95 || sub_process_data==96 || sub_process_data==97 || sub_process_data==98))
			{
			   if(k<1)
				{
					alert("Please Insert Ratio At Least One Item");
					return;
				}

			}*/
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_labdip_no*cbo_company_id*cbo_location*txt_recipe_date*txt_comosition*txt_count*cbo_yarn_type*txt_yarn_lot*txt_batch_id*cbo_buyer_name*txt_color*cbo_color_range*txt_liquor*txt_batch_ratio*txt_liquor_ratio*cbo_sub_process*txt_subprocess_remarks*txt_batch_weight*txt_remarks*copy_id*update_id_check*update_id*txt_total_liquor_ratio*txt_liquor_ratio_dtls*txt_copy_from*cbo_store_name*txt_subprocess_seq*txt_sys_id*txt_sys_number',"../")+data_all+'&total_row='+k+'&delete_cause='+delete_cause;
			//alert(data);
			freeze_window(operation);
		   http.open("POST","requires/yd_recipe_entry_controller.php",true);
		   http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		   http.send(data);
		  http.onreadystatechange=fnc_recipe_entry_Reply_info;
		}
		
		//alert (data);return;
		
		
	}

	function fnc_recipe_entry_Reply_info()
	{
		if(http.readyState == 4)
		{
			//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			show_msg(trim(reponse[0]));

			if((reponse[0]==0 || reponse[0]==1 || reponse[0]==2))
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_sys_id').value = reponse[1];
				document.getElementById('txt_sys_number').value = reponse[4];
				document.getElementById('update_id_check').value = reponse[1];
				if(reponse[0]==0)
				{
					document.getElementById('txt_recipe_serial_no').value = reponse[3];
				}
				/*if(!(reponse[2]==93 || reponse[2]==94 || reponse[2]==95 || reponse[2]==96 || reponse[2]==97 || reponse[2]==98))
				{
				show_list_view(reponse[1], 'recipe_item_details', 'recipe_items_list_view', 'requires/yd_recipe_entry_controller', '' ) ;
				setFilterGrid('tbl_list_search',-1);
				$('#list_container_recipe_items').html('');
				$('#copy_id').val(2);
				$('#copy_id').removeAttr('disabled','disabled');
				$('#copy_sub_process').attr('disabled','disabled');
				reset_form('','','copy_id*copy_sub_process*cbo_sub_process','','');
				set_button_status(0, permission, 'fnc_recipe_entry',1,1);
				}
				else
				{
					show_list_view(reponse[1], 'recipe_item_details', 'recipe_items_list_view', 'requires/yd_recipe_entry_controller', '' ) ;
				}*/
				var sub_process = $('#cbo_sub_process').val();
				//subprocess_change(sub_process);
				show_list_view(reponse[1], 'recipe_item_details', 'recipe_items_list_view', 'requires/yd_recipe_entry_controller', '' ) ;
				setFilterGrid('tbl_list_search',-1);
				$('#list_container_recipe_items').html('');
				$('#copy_id').val(2);
				$('#copy_id').removeAttr('disabled','disabled');
				$('#btn_recipe_calc').removeAttr('disabled','disabled').attr('class','formbutton');
				//$('#btn_recipe_calc').toggleClass('formbutton_disabled');
				$('#copy_sub_process').attr('disabled','disabled');
				$('#cbo_company_id').attr('disabled','disabled');
				//$('#cbo_working_company_id').attr('disabled','disabled');
				$('#cbo_store_name').attr('disabled','disabled');
				//reset_form('','','copy_id*copy_sub_process*cbo_sub_process*txt_subprocess_remarks*txt_total_liquor_ratio*txt_liquor_ratio_dtls','','');
				reset_form('','','copy_id*copy_sub_process*cbo_sub_process*txt_subprocess_remarks','','');
				set_button_status(0, permission, 'fnc_recipe_entry',1,1);


			}
			else if(reponse[0]==2)
			{
				release_freezing();
				fnResetForm();
			}
			else if(reponse[0]==14)
			{
				alert('Further Recipe for same Batch No not allowed.\n Recipe No: '+reponse[2]);
			}
			else if(reponse[0]==13)
			{
				alert('Unload and Shade Matched');
			}
			else if(reponse[0]==20)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			if(operation==2)
			 {
				if(reponse[0]==11)
				{
					alert('Bellow Dyes And Chemical Issue Requisition Found. Change Not Allowed.\n Requisition No: '+reponse[2]);
				}
				else
				{
					alert('Bellow Dyeing Re Process OR Recipe Entry For Gmts Wash/Dyeing/Printing Found. Change Not Allowed.\n Recipe No: '+reponse[2]);
				}
			  }

			release_freezing();
		}
	}

	function fnc_recipe_print2()
	{
		if($('#txt_sys_number').val()=="")
		{
			alert("Must Need System Id. Please Save Data First!!!");
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_labdip_no').val()+'*'+$('#txt_sys_number').val()+'*'+report_title, "recipe_entry_print_2", "requires/yd_recipe_entry_controller");
		// return;
		//show_msg("3");
		
	}

    function fn_recipe_calc(operation)
    {
        if(operation=="btn_recipe_calc")
		{
            //alert(operation);return;
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_labdip_no').val()+'*'+$('#txt_yarn_lot').val()+'*'+$('#txt_brand').val()+'*'+$('#txt_count').val()+'*'+$('#txt_pick_up').val()+'*'+$('#surpls_solution').val()+'*'+$('#txt_batch_id').val()+'*'+$('#cbo_sub_process').val()+'*'+report_title, "recipe_entry_print_2", "requires/yd_recipe_entry_controller")
			//return;
			show_msg("3");
		}
    }

	function openmypage_sysNo()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		//var cbo_working_company_id = $('#cbo_working_company_id').val();
		if(cbo_company_id==0)
		{
			if (form_validation('cbo_company_id','Company')==false)
			{
				return;
			}
		}

		var title = 'System ID Selection Form';
		var page_link = 'requires/yd_recipe_entry_controller.php?cbo_company_id='+cbo_company_id+'&action=systemid_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=390px,center=1,resize=1,scrolling=0','');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var update_id=this.contentDoc.getElementById("hidden_update_id").value;	 //Access form field with id="emailfield"

			if(update_id!="")
			{
				freeze_window(5);
				$('#list_container_recipe_items').html('');
				get_php_form_data(update_id, "populate_data_from_search_popup", "requires/yd_recipe_entry_controller" );
				var sub_process = $('#cbo_sub_process').val();
 				show_list_view(update_id, 'recipe_item_details', 'recipe_items_list_view', 'requires/yd_recipe_entry_controller', '' ) ;
				setFilterGrid('tbl_list_search',-1);
				reset_form('','','copy_id*copy_sub_process*cbo_sub_process*txt_subprocess_remarks','','');
				rcv_variable_check(1);stock_variable_check();
				set_button_status(0, permission, 'fnc_recipe_entry',1,1);
				$('#copy_id').removeAttr('disabled','disabled');
				release_freezing();
			}

		}

	}

	/*function openmypage_booking()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_buyer_name = $('#cbo_buyer_name').val();

		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var title = 'Booking No Selection Form';
			var page_link = 'requires/yd_recipe_entry_controller.php?cbo_company_id='+cbo_company_id+'&cbo_buyer_name='+cbo_buyer_name+'&action=booking_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=390px,center=1,resize=1,scrolling=0','');

			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var booking_id=this.contentDoc.getElementById("hidden_booking_id").value;
				var booking_no=this.contentDoc.getElementById("hidden_booking_no").value;
				var color=this.contentDoc.getElementById("hidden_color").value;
				var color_id=this.contentDoc.getElementById("hidden_color_id").value;
				var job_no=this.contentDoc.getElementById("hidden_job_no").value;
				var booking_without_order=this.contentDoc.getElementById("booking_without_order").value;
				if(booking_id!="")
				{
					freeze_window(5);
					document.getElementById("txt_booking_id").value=booking_id;
					document.getElementById("txt_booking_order").value=booking_no;
					document.getElementById("txt_color").value=color;
					document.getElementById("txt_color_id").value=color_id;
					document.getElementById("txt_order").value=job_no;
					document.getElementById("txt_booking_type").value=booking_without_order;
					release_freezing();
				}
			}
		}
	}*/

	function reset_div()
	{
		var val=$('#copy_sub_process').val();
		if(val==2)
		{
			$('#list_container_recipe_items').html('');
			$(".accordion_h").each(function() {

				 var tid=$(this).attr('id');
				 tid=tid+"span";
				 $('#'+tid).html("+");
			});

			set_button_status(0, permission, 'fnc_recipe_entry',1,0);
		}
		else if(val==1)
		{
			set_button_status(1, permission, 'fnc_recipe_entry',1,0);
		}

		//$('#txt_total_liquor_ratio').val('');
		//$('#txt_liquor_ratio_dtls').val('');
	}

	function fnc_item_details(sub_process_id,process_remark,store_id)
	{
		$(".accordion_h").each(function() {

			 var tid=$(this).attr('id');
			 tid=tid+"span";
			 $('#'+tid).html("+");
		});

		$('#accordion_h'+sub_process_id+'span').html("-");
		var val=$('#copy_sub_process').val();
		var copy_id=$('#copy_id').val();
		var update_id= $('#update_id_check').val();
		var cbo_company_id= $('#cbo_company_id').val();
		if(sub_process_id==93 || sub_process_id==94 || sub_process_id==95 || sub_process_id==96 || sub_process_id==97 || sub_process_id==98)
		{
			$("#txt_subprocess_remarks").removeAttr("disabled",true);
		}
		else
		{
			//$("#txt_subprocess_remarks").attr("disabled",true);
			$("#txt_subprocess_remarks").removeAttr("disabled",true);
			$('#txt_subprocess_remarks').val('');
		}
 //alert(process_remark);
		$('#cbo_sub_process').val(sub_process_id);
		$('#cbo_store_name').val(store_id);
		$('#cbo_store_name').attr("disabled",true);
		$('#txt_subprocess_remarks').val(process_remark);
		var sub_process = $('#cbo_sub_process').val();
		var copy_val=$('#copy_id').val();
		subprocess_change(sub_process);
		var cbo_store_name = $('#cbo_store_name').val();
		var variable_lot = $('#variable_lot').val();
		show_list_view(cbo_company_id+'**'+sub_process_id+"**"+update_id+"**"+copy_val+"**"+cbo_store_name+"**"+variable_lot, 'item_details', 'list_container_recipe_items', 'requires/yd_recipe_entry_controller', '');
		setFilterGrid('tbl_list_search',-1);
		$('#copy_sub_process').removeAttr('disabled','disabled');

		if(copy_id==2)
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
		var txt_prod_id = $('#txt_prod_id_'+id).val();
		var txt_item_lot = $('#txt_item_lot_'+id).val();
		//alert  (txt_prod_id);
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var title = 'Item Lot Selection Form';
			var page_link = 'requires/yd_recipe_entry_controller.php?cbo_company_id='+cbo_company_id+'&txt_prod_id='+txt_prod_id+'&txt_item_lot='+txt_item_lot+'&action=itemLot_popup';

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

	function caculate_tot_liquor()
	{
		var batch_weight=$('#txt_batch_weight').val();
		var tot_liquor=($('#txt_batch_ratio').val()*1)*($('#txt_liquor_ratio').val()*1)*(batch_weight*1);

		$('#txt_liquor').val(tot_liquor);

		var tot_liquor_dtls=($('#txt_batch_ratio').val()*1)*($('#txt_liquor_ratio_dtls').val()*1)*(batch_weight*1);
		if(($('#txt_liquor_ratio_dtls').val()*1)>0)
		{
			$('#txt_total_liquor_ratio').val(tot_liquor_dtls);
		}
		else
		{
			$('#txt_total_liquor_ratio').val("");
		}

	}

	function caculate_liquor_ratio()
	{
		var batch_weight=$('#txt_batch_weight').val();
		var tot_liquor=($('#txt_batch_ratio').val()*1)*($('#txt_liquor_ratio').val()*1)*(batch_weight*1);

		$('#txt_liquor').val(tot_liquor);

		var liquor_ratio=($('#txt_total_liquor_ratio').val()*1)/(($('#txt_batch_ratio').val()*1)*(batch_weight*1));
		if(($('#txt_total_liquor_ratio').val()*1)>0)
		{
			$('#txt_liquor_ratio_dtls').val(liquor_ratio);
		}
		else
		{
			$('#txt_liquor_ratio_dtls').val("");
		}

	}

	function openmypage_batchNo()
	{
		var cbo_company_id = $('#cbo_company_id').val();

		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var title = 'Batch No Selection Form';
			var page_link = 'requires/yd_recipe_entry_controller.php?cbo_company_id='+cbo_company_id+'&action=batch_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=690px,height=390px,center=1,resize=1,scrolling=0','');

			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;	 //Access form field with id="emailfield"
				//var batch_type=this.contentDoc.getElementById("hidden_batch_type").value;	 //Access form field with id="emailfield"
				//alert(batch_id);
				//return;

				if(batch_id!="")
				{
					freeze_window(5);
					var batch_val=batch_id.split('_');
					//alert(batch_val[1]);
					$('#txt_batch_id').val(batch_val[0]);
					$('#txt_batch_ratio').val(1);
					$('#txt_trims_weight').val(batch_val[3]);
					get_php_form_data(cbo_company_id+'**'+batch_val[0]+'**'+batch_val[1]+'**'+batch_val[2], "load_data_from_batch", "requires/yd_recipe_entry_controller" );
					caculate_tot_liquor();
					release_freezing();
				}
			}
		}
	}

	function copy_check(type)
	{
		var recipe_prev_id=$('#txt_sys_id').val();
 		var working_company_id = $('#cbo_working_company_id').val();
		var recipe_date = $('#txt_recipe_date').val();
	
		
		if(type==1)
		{
			
				var response = return_global_ajax_value( working_company_id+'**'+recipe_date, 'recipe_previ_copy_check', '', 'requires/yd_recipe_entry_controller');
				if(response=="")
				{
					alert('Restricted, please select recipe from 7th march, 19 onward to copy.');
					$('#copy_id').attr('checked', false);
					document.getElementById('copy_id').value=2;
					return;
				}	
			
			$("#list_container_recipe_items").html('');
			show_list_view($('#txt_sys_id').val(), 'recipe_item_details', 'recipe_items_list_view', 'requires/yd_recipe_entry_controller', '' ) ;
			$('#update_id').val('');
			$('#txt_sys_id').val('');
			$('#txt_recipe_date').val('');
			$('#txt_labdip_no').val('');
			$('#txt_batch_no').val('');
			$('#txt_batch_id').val('');
			$('#txt_batch_weight').val('');
			$('#txt_booking_order').val('');
			$('#txt_booking_id').val('');
			$('#txt_color').val('');
			$('#txt_color_id').val('');
			$('#txt_trims_weight').val('');
			$('#txt_yarn_lot').val('');
			$('#txt_brand').val('');
			$('#txt_count').val('');
			$('#txt_order').val('');
			
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

	function copy_check_sub(type)
	{
		if ( document.getElementById('copy_sub_process').checked==true)
		{
			var chk=document.getElementById('copy_sub_process').value=1;
			//set_button_status(0, permission, 'fnc_recipe_entry',1,1);
			//alert(chk );
		}
		else if(document.getElementById('copy_sub_process').checked==false)
		{
			var chk=document.getElementById('copy_sub_process').value=2;
		}
		//alert(chk );
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
		/*alert (seq_no+"=="+largest)
		if ($('#txt_seqno_'+id).val()!='')
		{
			$('#txt_max_seq').val(largest*1);
		}*/

		largest=largest+1;
		/*var max_seq=$('#txt_max_seq').val()*1;
		if ($('#txt_ratio_'+id).val()!='')
		{
			max_seq=max_seq+1;
		}*/
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
		//$('#txt_max_seq').val(max_seq);
		//row_sequence(id)
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
					//alert("Duplicate Seq No. "+txt_seq);
					//$('#txt_seqno_'+row_id).val('');
					//return;
				}
			}
		}
	}
	function subprocess_change(sub_process)
	{
		if(sub_process==93 || sub_process==94 || sub_process==95 || sub_process==96 || sub_process==97 || sub_process==98)
		{
			$("#txt_subprocess_remarks").removeAttr("disabled",true);
			$('#txt_subprocess_remarks').focus();
			$('#show').css('display','none');


		}
		else
		{
			$("#txt_subprocess_remarks").removeAttr("disabled",true);
			//$("#txt_subprocess_remarks").attr("disabled",true);
			//$('#txt_subprocess_remarks').val('');
			$('#txt_subprocess_remarks').focus();
			$('#show').css('display','block');
		}
		var cbo_company_id=$('#cbo_company_id').val();
		var update_id=$('#update_id').val();
		var update_id_check=$('#update_id_check').val();


		get_php_form_data(cbo_company_id+'**'+sub_process+'**'+update_id_check, "ratio_data_from_dtls", "requires/yd_recipe_entry_controller" );
	}
	function fnResetForm()
	{
		//alert(33);
		$("#cbo_company_id").attr("disabled",false);
		//disable_enable_fields(\'cbo_company_id*cbo_company_id\',0)
		//reset_form('slittingsqueezing_1','','','','$(\'#list_fabric_desc_container tr:not(:first)\').remove();');
		reset_form('recipeEntry_1','list_container_recipe_items*recipe_items_list_view','',$('#copy_id').val()*2,'variable_lot');
	}
	function check_batch_weigth() // issue=4884**Raised By=Mamun
	{
	 	var hidden_batch_weight=$('#txt_hidden_batch_weight').val()*1;
		var txt_batch_weight=$('#txt_batch_weight').val()*1;
		// alert(txt_batch_weight+'='+hidden_batch_weight);
		if(txt_batch_weight>hidden_batch_weight)
		{
			alert('Batch weight can not exceed');
			$('#txt_batch_weight').val(hidden_batch_weight);
		}
		
  	}
	
	function rcv_variable_check(str)
	{
		//alert(str);return;
		var company_id=$('#cbo_company_id').val();
		var lots_variable=return_global_ajax_value( company_id, 'populate_data_lib_data', '', 'requires/yd_recipe_entry_controller');
		$('#variable_lot').val(lots_variable);
		if(str==2)
		{
			reset_form('','list_container_recipe_items*recipe_items_list_view','','','variable_lot');
		}
		
		/*if(lots_variable==1)
		{
			$('#lot_caption').css('color', 'blue');
		}
		else
		{
			$('#lot_caption').css('color', 'black');
		}*/
	}
	
	function stock_variable_check()
	{
		var company_id=$('#cbo_company_id').val();
		get_php_form_data(company_id, "populate_stock_data", "requires/yd_recipe_entry_controller" );
	}
	
</script>
</head>

<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",$permission); ?>
	<fieldset style="width:970px;">
	<legend>Recipe Entry</legend>
		<form name="recipeEntry_1" id="recipeEntry_1">
			<fieldset style="width:970px;">
				<table width="910" align="center" border="0">
                    <tr>
                        <td colspan="3" align="right"><strong>System ID</strong></td>
                        <td align="left">
                         <input type="text" name="txt_sys_number" id="txt_sys_number" class="text_boxes" style="width:140px;" placeholder="Double click to search" onDblClick="openmypage_sysNo();" readonly />
                        <input type="hidden" name="txt_sys_id" id="txt_sys_id" class="text_boxes" style="width:140px;" placeholder=" " readonly />
                        <input type="hidden" name="txt_max_seq" id="txt_max_seq" class="text_boxes" value="0" style="width:40px;" />
                        </td>
                        <td style="display: none;">
                       		<strong>Copy</strong>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="copy_id" id="copy_id" onClick="copy_check(1)" value="2" disabled >
                        </td>
                        <td>
                        	<div id="batch_type" style="color:#F00; font-size:18px"></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">&nbsp;</td>
                    </tr>
                	<tr>
                        <td class="must_entry_caption">Labdip No</td>
                        <td>
                            <input type="text" name="txt_labdip_no" id="txt_labdip_no" class="text_boxes" style="width:140px;" />
                        </td>
                         <td class="must_entry_caption">Company</td>
                         <td>
                            <?
							//load_drop_down('requires/yd_recipe_entry_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down('requires/yd_recipe_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td_id' );
                                echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down('requires/yd_recipe_entry_controller', this.value, 'load_drop_down_location', 'location_td' );rcv_variable_check(2);stock_variable_check();load_room_rack_self_bin('requires/yd_recipe_entry_controller*5_6_7_23', 'store','store_td', this.value);" );
                            ?>
                            <input type="hidden" id="variable_lot" name="variable_lot" />
                            <input type="hidden" id="variable_stock" name="variable_stock" />
                        </td>
                        <td>Location</td>
                        <td id="location_td">
                            <?
                                echo create_drop_down("cbo_location", 152, $blank_array,"", 1,"-- Select Location --", 0,"");
                            ?>
                        </td>
                        
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Batch No</td>
                        <td>
                            <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:140px;" placeholder="Double click to search" onDblClick="openmypage_batchNo();" readonly />
							<input type="hidden" name="txt_hidden_batch_weight" id="txt_hidden_batch_weight" class="text_boxes_numeric" style="width:20px;" />
							<input title="With Trims Wgt" type="hidden" name="txt_batch_weight" id="txt_batch_weight" class="text_boxes_numeric" onBlur="check_batch_weigth()" style="width:58px;" />
							<input type="hidden" name="txt_batch_id" id="txt_batch_id" style="width:40px;"/>
                        </td>

                        
                        <td  class="must_entry_caption">Party</td>
                        <td id="buyer_td">
                            <? echo create_drop_down( "cbo_buyer_name", 152, $blank_array,"", 1, "-- Select Party --", $selected, "",1 );?>
                        </td>
                        <td class="must_entry_caption">Recipe Date</td>
                        <td>
                        	<input type="text" name="txt_recipe_date" id="txt_recipe_date" class="datepicker" style="width:140px;" readonly tabindex="6" /> &nbsp;
                        </td>
                    </tr>

                    <tr>
                        <td class="must_entry_caption">Color</td>
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
                        <td class="">Batch Ratio</td>
                        <td colspan="0">
                            <input type="text" name="txt_batch_ratio" id="txt_batch_ratio" class="text_boxes_numeric" value="" style="width:140px;" placeholder="Batch" onBlur="caculate_tot_liquor()" />
                            <input type="text" name="txt_liquor_ratio" id="txt_liquor_ratio" class="text_boxes_numeric" value="" style="width:62px; display:none;" placeholder="Liquor" onBlur="caculate_tot_liquor()" />
                        </td>
                    </tr>
                    <tr>
                        <td>Yarn Lot</td>
                        <td colspan="0">
                            <input type="text" name="txt_yarn_lot" id="txt_yarn_lot" class="text_boxes" value="" style="width:140px;" disabled />
                        </td>
                        <td>Count</td>
                        <td>
                            <input type="text" name="txt_count" id="txt_count" class="text_boxes" value="" style="width:140px;" disabled />
                        </td>
                        <td>Yarn Composition</td>
                        <td>
                            <input type="text" name="txt_comosition" id="txt_comosition" class="text_boxes" style="width:140px;" />
                        </td>
                        
                    </tr>
                    <tr>
                        <td>Yarn Type</td>
                        <td>
                            <?
							   echo create_drop_down( "cbo_yarn_type", 152,$yarn_type,"", 1, "--Select Method--", $selected, "",0 );
 							?>
                        </td>
                        <td>Batch Qty</td>
                        <td>
                            <input type="text" name="txt_trims_weight" id="txt_trims_weight" class="text_boxes_numeric" value="" style="width:140px;" disabled />
                        </td>
                        <td>Remarks</td>
                        <td>
                            <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" value="" style="width:140px;" />
                        </td>
                    </tr>
					<tr style="display: none">
                        <td class="must_entry_caption">Order Source</td>
                        <td>
                            <?
                                echo create_drop_down("cbo_order_source", 152, $order_source,"", 1,"-- Select Source --", $selected,"",1,"","","","");
                            ?>
                        </td>
                        <td>Booking</td>
                        <td>
                        	<input type="text" name="txt_booking_order" id="txt_booking_order" class="text_boxes" style="width:140px;" disabled/>
                            <input type="hidden" name="txt_booking_id" id="txt_booking_id" class="text_boxes" style="width:120px;" />
                            <input title="With Trims Wgt" type="text" name="txt_batch_weight" id="txt_batch_weight" class="text_boxes_numeric" onBlur="check_batch_weigth()" style="width:58px;" />
                            <input type="text" name="txt_recipe_serial_no" id="txt_recipe_serial_no" class="text_boxes_numeric" style="width:50px;" placeholder="Serial No" readonly tabindex="7" />
                        </td>
                        <td>Method</td>
                        <td>
                            
							<?
							$dyeing_method_arr=array();
							foreach($dyeing_method as $key=>$val)
							{
								$dyeing_method_arr[$key]=$key.'-'.$val;
							}
							
							   echo create_drop_down( "cbo_method", 152, $dyeing_method_arr,"", 1, "--Select Method--", $selected, "",0 );
 							?>
                        </td>
                        <td>Pick Up(%)</td>
                        <td>
                            <input type="text" name="txt_pick_up" id="txt_pick_up" class="text_boxes_numeric" style="width:140px;" />
                        </td>
						<td>Surplus Solution</td>
                        <td>
                            <input type="text" name="surpls_solution" id="surpls_solution" class="text_boxes_numeric" style="width:140px;" />
                        </td>
                        <td>Brand</td>
                        <td>
                            <input type="text" name="txt_brand" id="txt_brand" class="text_boxes" value="" style="width:140px;" disabled />
                        </td>
                        <td class="must_entry_caption" style="display:none">Total Liquor(ltr)</td>
                        <td style="display:none">
                            <input type="text" name="txt_liquor" id="txt_liquor" class="text_boxes_numeric" value="" style="width:140px; display:none" readonly />
                        </td>
                        <td>Trims Weight</td>
                        <td>
                            <input type="text" name="txt_trims_weight" id="txt_trims_weight" class="text_boxes_numeric" value="" style="width:140px;" disabled />
                        </td>
                        <td>Order/FSO No.</td>
                        <td>
                            <input type="text" name="txt_order" id="txt_order" class="text_boxes" value="" style="width:140px;" disabled />
                            <input type="hidden" name="txt_booking_type" id="txt_booking_type" class="text_boxes" value="" style="width:60px;" />
                        </td>
                         <td>Copy From</td>
                        <td>
                            <input type="text" name="txt_copy_from" id="txt_copy_from" class="text_boxes" value="" style="width:140px;" disabled="disabled" />
                        </td>
                    </tr>
                    <tr>
                       
                   </tr>
             </table>
			</fieldset>
            <fieldset style="width:970px; margin-top:10px">
            <legend>Item Details</legend>
 				<table cellpadding="0" cellspacing="0" border="0" width="100%">
                	<tr>
                        <td style="display: none;">
                       		<strong>Replace Sub-Process</strong>&nbsp;&nbsp;<input type="checkbox" name="copy_sub_process" id="copy_sub_process" onClick="copy_check_sub(1)" value="2" disabled >
                        </td>
                    	<td width="70" class="must_entry_caption"><b>Sub Process</b></td>
                    	<td width="150">
							<?
                                echo create_drop_down( "cbo_sub_process", 150, $dyeing_sub_process,"", 1, "-- Select Sub Process --", 0, "reset_div(); subprocess_change(this.value);","","","","",'70');
                            ?>
                        </td>
                        <td width="35" style="display: none;"> <input type="text" name="txt_subprocess_seq" id="txt_subprocess_seq" class="text_boxes_numeric" style="width:35px;" placeholder="Seq.No" /></td>
                        <td width="70" class="must_entry_caption">Store Name</td>
                    	<td width="150" id="store_td">
							<?
                                 echo create_drop_down( "cbo_store_name", 160, "select lib_store_location.id,lib_store_location.store_name from lib_store_location,lib_store_location_category where lib_store_location.id=lib_store_location_category.store_location_id and lib_store_location.status_active=1 and lib_store_location.is_deleted=0  and lib_store_location_category.category_type in(5,6,7,23) group by lib_store_location.id,lib_store_location.store_name order by lib_store_location.store_name","id,store_name", 1, "-- Select Store --", $storeName, "" );
                            ?>
                        </td>
                        <td width="60"  style="display: none;"><input type="text" name="txt_subprocess_remarks" id="txt_subprocess_remarks" class="text_boxes" placeholder="Remarks" value="" style="width:55px;" disabled /></td>
						
                        <td class="must_entry_caption" width="70">Liquor Ratio</td>
                        <td class="must_entry_caption" width="70"> <input type="text" name="txt_liquor_ratio_dtls" id="txt_liquor_ratio_dtls" class="text_boxes_numeric" value=""  onBlur="caculate_tot_liquor()" style="width:80px;" /></td>

						<td class="must_entry_caption" width="70">Total Liquor(ltr)</td>
						<td class="must_entry_caption" width="70">
						<input type="text" name="txt_total_liquor_ratio" id="txt_total_liquor_ratio" class="text_boxes_numeric" value="" style="width:80px;" onBlur="caculate_liquor_ratio()" /></td>

                        <td id="button_th" width="80">
                        <input type="button" value="Show Items" name="show" id="show" class="formbuttonplasminus" style="width:70px; float:left" onClick="show_details()"/></td>                	</tr>
                </table>
                <div id="list_container_recipe_items" style="margin-top:10px"></div>
            </fieldset>
            <table width="910">
            	<tr>
                    <td colspan="4" align="center" class="button_container">
						<? //reset_form('recipeEntry_1','list_container_recipe_items*recipe_items_list_view','','copy_id*2','disable_enable_fields(\'cbo_company_id*cbo_company_id\',0)');$('#cbo_company_id').attr('disabled','false');
                        	echo load_submit_buttons($permission, "fnc_recipe_entry", 0,1,"fnResetForm();",1);
							?>
						<input type="button" id="print2" name="print2" value="Print 2" style="width:80px;display:none" class="formbutton" onClick="fnc_recipe_print2()">
                       
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
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>

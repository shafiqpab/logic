<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Recipe Entry For Gmts wash/Dyeing/Printing
Functionality	:	
JS Functions	:
Created by		:	Tajik 
Creation date 	: 	19.09.2017
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

	function show_details()
	{
		var val=$('#copy_sub_process').val();
		//alert(val);return;
		
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_sub_process = $('#cbo_sub_process').val();
		
		if(form_validation('cbo_company_id*cbo_sub_process','Company*Sub Process')==false)
		{
			return;
		}
		if(val==2)
		{
			var list_view_orders = return_global_ajax_value( cbo_company_id+'**'+cbo_sub_process, 'item_details', '', 'requires/recipe_entry_controller');
			if(list_view_orders!='')
			{
				$("#list_container_recipe_items").html(list_view_orders);
				setFilterGrid('tbl_list_search',-1);
			}
		}
		else
		{
			alert("Execute Not Allowed.");
			return;
		}
	}

	function color_row(tr_id)
	{
		var txt_ratio=$('#txt_ratio_'+tr_id).val()*1;
		var stock_check=$('#stock_qty_'+tr_id).text()*1;
		var sub_process=$('#cbo_sub_process').val();
		
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
					
					/*if(sub_process==93 || sub_process==94 || sub_process==95 || sub_process==96 || sub_process==97 || sub_process==98 )
					{
						$('#txt_ratio_'+tr_id).val();	
					}
					else
					{*/
						alert("No Stock Qty.");
						$('#txt_ratio_'+tr_id).val('');	
					//}
					
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
	}
			
	function fnc_recipe_entry(operation)
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_labdip_no').val()+'*'+report_title+'*'+$('#cbo_location').val(), "recipe_entry_print", "requires/recipe_entry_controller") 
			//return;
			show_msg("3");
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			
			if(operation==2)
			{
				show_msg('13');
				return;
			}
			
			if( form_validation('txt_labdip_no*txt_batch_no*cbo_company_id*txt_recipe_date*cbo_order_source*txt_color*txt_total_liquor_ratio','Labdip No*Batch*Company*Recipe Date*Order Source*Color*Total Liquor')==false )
			{
				return;
			}
			
			
			var copy_val=$('#copy_id').val();
			
			if(copy_val==1) //Copy Item
			{
				var cbo_company_id = $('#cbo_company_id').val();
				var update_id_check = $('#update_id_check').val();
				var response = return_global_ajax_value( cbo_company_id+'**'+update_id_check, 'item_stock_details', '', 'requires/recipe_entry_controller');
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
					
				}
			}
			if(copy_val==2)
			{
				var sub_process_data=$("#cbo_sub_process").val()*1;
				/*if(!(sub_process_data==93 || sub_process_data==94 || sub_process_data==95 || sub_process_data==96 || sub_process_data==97 || sub_process_data==98))
				{*/
					if( $('#list_container_recipe_items').html() == "")
					{
						alert("Please Select Item");
						return;
					}
				//}
				
			/*if(!(sub_process_data==93 || sub_process_data==94 || sub_process_data==95 || sub_process_data==96 || sub_process_data==97 || sub_process_data==98))
			{*/
				var row_num=$('#tbl_list_search tbody tr').length-1;
				//alert (row_num);return;
				var data_all=""; var i=0;
		
				for(var j=1; j<=row_num; j++)
				{
					var txt_ratio=$('#txt_ratio_'+j).val();
					var updateIdDtls=$('#updateIdDtls_'+j).val();
					var dose=$('#cbo_dose_base_'+j).val();
					
					if(updateIdDtls!="" || txt_ratio*1>0)
					{
						if (form_validation('cbo_dose_base_'+j,'Dose Base')==false)
						{
							return;
						}
						
						i++;
						data_all+="&txt_seqno_" + i + "='" + $('#txt_seqno_'+j).val()+"'"+"&product_id_" + i + "='" + $('#product_id_'+j).text()+"'"+"&txt_item_lot_" + i + "='" + $('#txt_item_lot_'+j).val()+"&txt_comments_" + i + "='" + $('#txt_comments_'+j).val()+"'"+"&cbo_dose_base_" + i + "='" + $('#cbo_dose_base_'+j).val()+"'"+"&txt_ratio_" + i + "='" + $('#txt_ratio_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'";
						//alert(data_all);return;
					}
				}
			/*}
			else //update_dtls_id
			{ 
				 var i=0;
				// if($('#txt_comments_'+1).val()=='') var comments='';
				if(operation==0)
				{
					data_all="&txt_subprocess_remarks"+"='" + $('#txt_subprocess_remarks').val()+"'";
				}
				else if(operation==1) 
				{
				data_all="&txt_subprocess_remarks"+"='" + $('#txt_subprocess_remarks').val()+"'"+"&updateIdDtls_"+ 1 +"='" + $('#updateIdDtls_'+1).val()+"'"+"&txt_comments_"+ 1 +"='" + $('#txt_comments_'+1).val()+"'"+"&txt_ratio_"+ 1 +"='" + $('#txt_ratio_'+1).val()+"'"+"&txt_seqno_"+ 1 +"='" + $('#txt_seqno_'+1).val()+"'"+"&cbo_dose_base_"+ 1 +"='" + $('#cbo_dose_base_'+1).val()+"'";
				}
			}*/
				//alert(data_all);
				/*	if(!(sub_process_data==93 || sub_process_data==94 || sub_process_data==95 || sub_process_data==96 || sub_process_data==97 || sub_process_data==98))
					{*/
					   if(i<1)
						{
							alert("Please Insert Ratio At Least One Item");
							return;
						}
				
				//	}
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_labdip_no*cbo_company_id*cbo_location*txt_recipe_date*cbo_order_source*txt_booking_order*txt_booking_id*txt_recipe_des*txt_batch_id*cbo_buyer_name*cbo_method*txt_color*cbo_color_range*txt_liquor*txt_batch_ratio*txt_liquor_ratio*txt_booking_type*cbo_sub_process*txt_batch_weight*txt_remarks*copy_id*update_id*txt_total_liquor_ratio*txt_liquor_ratio_dtls*txt_time*txt_temparature*txt_ph',"../")+data_all+'&total_row='+i;
				//alert(data);
				
				
			}
			else if(copy_val==1)
			{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_labdip_no*cbo_company_id*cbo_location*txt_recipe_date*cbo_order_source*txt_booking_order*txt_booking_id*txt_recipe_des*txt_batch_id*cbo_buyer_name*cbo_method*txt_color*cbo_color_range*txt_liquor*txt_batch_ratio*txt_liquor_ratio*txt_booking_type*cbo_sub_process*txt_subprocess_remarks*txt_batch_weight*txt_remarks*copy_id*update_id_check*txt_total_liquor_ratio*txt_liquor_ratio_dtls*txt_time*txt_temparature*txt_ph',"../");
			}
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

			show_msg(trim(reponse[0]));
			
			if((reponse[0]==0 || reponse[0]==1))
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_sys_id').value = reponse[1];
				document.getElementById('update_id_check').value = reponse[1];
				
				var sub_process = $('#cbo_sub_process').val();
				show_list_view(reponse[1], 'recipe_item_details', 'recipe_items_list_view', 'requires/recipe_entry_controller', '' ) ;
				setFilterGrid('tbl_list_search',-1);
				$('#list_container_recipe_items').html('');
				$('#copy_id').val(2);
				$('#copy_id').removeAttr('disabled','disabled');
				$('#copy_sub_process').attr('disabled','disabled');
				//reset_form('','','copy_id*copy_sub_process*cbo_sub_process*txt_subprocess_remarks*txt_total_liquor_ratio*txt_liquor_ratio_dtls','','');
				reset_form('','','copy_id*copy_sub_process*cbo_sub_process*txt_subprocess_remarks','','');
				set_button_status(0, permission, 'fnc_recipe_entry',1,1);	
				
				
			}
			else if(reponse[0]==14)
			{
				alert('Further recipe for same batch No not allowed.\n Recipe No: '+reponse[2]);
			}
			
			release_freezing();	
		}
	}
	
	function openmypage_sysNo()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'System ID Selection Form';	
			var page_link = 'requires/recipe_entry_controller.php?cbo_company_id='+cbo_company_id+'&action=systemid_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1110px,height=390px,center=1,resize=1,scrolling=0','');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var update_id=this.contentDoc.getElementById("hidden_update_id").value;	 //Access form field with id="emailfield"
				
				if(update_id!="")
				{
					freeze_window(5);
					$('#list_container_recipe_items').html('');
					get_php_form_data(update_id, "populate_data_from_search_popup", "requires/recipe_entry_controller" );
					var sub_process = $('#cbo_sub_process').val();
					
					show_list_view(update_id, 'recipe_item_details', 'recipe_items_list_view', 'requires/recipe_entry_controller', '' ) ;
					setFilterGrid('tbl_list_search',-1);
					reset_form('','','copy_id*copy_sub_process*cbo_sub_process*txt_subprocess_remarks','','');
					set_button_status(0, permission, 'fnc_recipe_entry',1,1);
					$('#copy_id').removeAttr('disabled','disabled');			
					release_freezing();
				} 
			}
		}
	}
	
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
	
	function fnc_item_details(sub_process_id,process_remark)
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
		/*if(sub_process_id==93 || sub_process_id==94 || sub_process_id==95 || sub_process_id==96 || sub_process_id==97 || sub_process_id==98)
		{
			$("#txt_subprocess_remarks").removeAttr("disabled",true);
		}
		else
		{*/
			$("#txt_subprocess_remarks").attr("disabled",true); 
			$('#txt_subprocess_remarks').val('');
	//	}
		
		$('#cbo_sub_process').val(sub_process_id);
		$('#txt_subprocess_remarks').val(process_remark);
		var sub_process = $('#cbo_sub_process').val();
		var copy_val=$('#copy_id').val();
		subprocess_change(sub_process);
		show_list_view(cbo_company_id+'**'+sub_process_id+"**"+update_id+"**"+copy_val, 'item_details', 'list_container_recipe_items', 'requires/recipe_entry_controller', '');
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
			var page_link = 'requires/recipe_entry_controller.php?cbo_company_id='+cbo_company_id+'&txt_prod_id='+txt_prod_id+'&txt_item_lot='+txt_item_lot+'&action=itemLot_popup';
			  
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
			var page_link = 'requires/recipe_entry_controller.php?cbo_company_id='+cbo_company_id+'&action=batch_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=410px,center=1,resize=1,scrolling=0','');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;	 //Access form field with id="emailfield"
				//alert(theemail);return;
				if(batch_id!="")
				{
					freeze_window(5);
					var batch_val=batch_id.split('_');
					$('#txt_batch_id').val(batch_val[0]);
					get_php_form_data(cbo_company_id+'**'+batch_val[0], "load_data_from_batch", "requires/recipe_entry_controller" );
					caculate_tot_liquor();
					release_freezing();
				} 
			}
		}
	}	
	
	function copy_check(type)
	{  
		if(type==1)
		{
			$("#list_container_recipe_items").html('');
			show_list_view($('#txt_sys_id').val(), 'recipe_item_details', 'recipe_items_list_view', 'requires/recipe_entry_controller', '' ) ;
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
					alert("Duplicate Seq No. "+txt_seq);
					$('#txt_seqno_'+row_id).val('');
					return;
				}
			}
		}
	}	
	function subprocess_change(sub_process)
	{
		/*if(sub_process==93 || sub_process==94 || sub_process==95 || sub_process==96 || sub_process==97 || sub_process==98)
		{
			$("#txt_subprocess_remarks").removeAttr("disabled",true); 
			$('#txt_subprocess_remarks').focus();
			$('#show').css('display','none');	 
			
			  
		}
		else
		{*/
			$("#txt_subprocess_remarks").attr("disabled",true); 
			$('#txt_subprocess_remarks').val('');  
			$('#show').css('display','block');	
		/*}*/
		var cbo_company_id=$('#cbo_company_id').val();
		var update_id=$('#update_id').val();
		var update_id_check=$('#update_id_check').val();
		
		
		get_php_form_data(cbo_company_id+'**'+sub_process+'**'+update_id_check, "ratio_data_from_dtls", "requires/recipe_entry_controller" );
	}
	function fnResetForm()
	{
		//alert(33);
		$("#cbo_company_id").attr("disabled",false); 
		//disable_enable_fields(\'cbo_company_id*cbo_company_id\',0)
		//reset_form('slittingsqueezing_1','','','','$(\'#list_fabric_desc_container tr:not(:first)\').remove();');
		reset_form('recipeEntry_1','list_container_recipe_items*recipe_items_list_view','',$('#copy_id').val()*2,'');
	}
</script>
</head>

<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",$permission); ?>
	<fieldset style="width:950px;">
	<legend>Recipe Entry</legend> 
		<form name="recipeEntry_1" id="recipeEntry_1"> 
			<fieldset style="width:920px;">
				<table width="910" align="center" border="0">
                    <tr>
                        <td colspan="3" align="right"><strong>System ID</strong></td>
                        <td align="left">
                        <input type="text" name="txt_sys_id" id="txt_sys_id" class="text_boxes" style="width:140px;" placeholder="Double click to search" onDblClick="openmypage_sysNo();" readonly />
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
                         <td>Recipe Description</td>
                        <td>
                            <input type="text" name="txt_recipe_des" id="txt_recipe_des" class="text_boxes" style="width:140px;" />
                        </td>
                        <td class="must_entry_caption">Batch No</td>
                        <td>
                            <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:70px;" placeholder="Double click to search" onDblClick="openmypage_batchNo();" /><input type="text" name="txt_batch_weight" id="txt_batch_weight" class="text_boxes_numeric" style="width:58px;"  /><input type="hidden" name="txt_batch_id" id="txt_batch_id" style="width:40px;" />
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Company Name</td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down('requires/recipe_entry_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down('requires/recipe_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td_id' );" );
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
                        	<input type="text" name="txt_recipe_date" id="txt_recipe_date" class="datepicker" style="width:140px;" tabindex="6" />
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Order Source</td>
                        <td>
                            <?
                                echo create_drop_down("cbo_order_source", 152, $order_source,"", 1,"-- Select Source --", $selected,"",1,"","","","");
                            ?>
                        </td>
                        <td>Party Name</td>
                        <td id="buyer_td_id">
                            <?
							   echo create_drop_down( "cbo_buyer_name", 152, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1 );  
 							?>
                        </td>
                        <td>Booking</td>
                        <td>
                        	<input type="text" name="txt_booking_order" id="txt_booking_order" class="text_boxes" style="width:140px;" disabled/>
                            <input type="hidden" name="txt_booking_id" id="txt_booking_id" class="text_boxes" style="width:120px;" />
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
                        <td>Method</td>
                        <td>
                            <?
							   echo create_drop_down( "cbo_method", 152, $dyeing_method,"", 1, "--Select Method--", $selected, "",0 );  
 							?>
                        </td>
                    </tr>
                    <tr>
                        <td class="">Batch Ratio</td>
                        <td colspan="0">
                            <input type="text" name="txt_batch_ratio" id="txt_batch_ratio" class="text_boxes_numeric" value="" style="width:140px;" placeholder="Batch" onBlur="caculate_tot_liquor()" /> 
                            <input type="text" name="txt_liquor_ratio" id="txt_liquor_ratio" class="text_boxes_numeric" value="" style="width:62px; display:none;" placeholder="Liquor" onBlur="caculate_tot_liquor()" />
                        </td>
                        <td class="must_entry_caption" style="display:none">Total Liquor(ltr)</td>
                        <td style="display:none">
                            <input type="text" name="txt_liquor" id="txt_liquor" class="text_boxes_numeric" value="" style="width:140px; display:none" readonly />
                        </td>
                        <td>Trims Weight</td>
                        <td>
                            <input type="text" name="txt_trims_weight" id="txt_trims_weight" class="text_boxes_numeric" value="" style="width:140px;" disabled />
                        </td>
                        <td>Order No.</td>
                        <td>
                            <input type="text" name="txt_order" id="txt_order" class="text_boxes" value="" style="width:140px;" disabled />
                            <input type="hidden" name="txt_booking_type" id="txt_booking_type" class="text_boxes" value="" style="width:60px;" />
                        </td>
                    </tr>
                    <tr>
                        <td>Yarn Lot</td>
                        <td colspan="0">
                            <input type="text" name="txt_yarn_lot" id="txt_yarn_lot" class="text_boxes" value="" style="width:140px;" disabled />
                        </td>
                        <td>Brand</td>
                        <td>
                            <input type="text" name="txt_brand" id="txt_brand" class="text_boxes" value="" style="width:140px;" disabled />
                        </td>
                        <td>Count</td>
                        <td>
                            <input type="text" name="txt_count" id="txt_count" class="text_boxes" value="" style="width:140px;" disabled />
                        </td>
                    </tr>
                    <tr>
                        <td>Remarks</td>
                        <td colspan="5">
                            <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" value="" style="width:745px;" />
                        </td>
                   </tr>
             </table>
			</fieldset>                 
            <fieldset style="width:920px; margin-top:10px">
            <legend>Item Details</legend>
 				<table cellpadding="0" cellspacing="0" border="0" width="100%">

 					<tr>
 						<td width="100"></td>
 						<td width="160" class="must_entry_caption" align="center"><b>Step</b></td>
 						<td class="must_entry_caption" width="80" align="center">Liquor Ratio</td>
 						<td class="must_entry_caption" width="80" align="center">Total Liquor(ltr)</td>
 						<td width="100" align="center">Time (min)</td>
 						<td width="100" align="center">Temp (&#8451;)</td>
 						<td width="100" align="center">PH</td>
 						<td width="100"></td>
 					</tr>
 					<tr>
 						<td>
                       		<strong>Replace Step</strong>&nbsp;&nbsp;<input type="checkbox" name="copy_sub_process" id="copy_sub_process" onClick="copy_check_sub(1)" value="2" disabled >
                        </td>
                        <td width="160">						 
							<?
                                echo create_drop_down( "cbo_sub_process", 160, $dyeing_sub_process,"", 1, "-- Select Sub Process --", 0, "reset_div(); subprocess_change(this.value);","","","","",'70');
                            ?>

                            <input type="hidden" name="txt_subprocess_remarks" id="txt_subprocess_remarks" class="text_boxes" value="" style="width:110px;" disabled />
                        </td>
                        <td>
                        	<input type="text" name="txt_liquor_ratio_dtls" id="txt_liquor_ratio_dtls" class="text_boxes_numeric" value=""  onBlur="caculate_tot_liquor()" style="width:80px;" />
                        </td>
                        <td>
                        	<input type="text" name="txt_total_liquor_ratio" id="txt_total_liquor_ratio" class="text_boxes_numeric" value="" style="width:80px;" readonly />
                        </td>

                        <td>
                        	<input type="text" name="txt_time" id="txt_time" class="text_boxes_numeric" style="width:90px;">
                        </td>
                        <td>
                        	<input type="text" name="txt_temparature" id="txt_temparature" class="text_boxes_numeric" style="width:90px;">
                        </td>
                        <td>
                        	<input type="text" name="txt_ph" id="txt_ph" class="text_boxes_numeric" style="width:90px;">
                        </td>
                        
                        <td id="button_th" width="100">
                        	<input type="button" value="Show Items" name="show" id="show" class="formbuttonplasminus" style="width:70px; float:left" onClick="show_details()"/>
                    	</td> 
 					</tr>


                	<!--<tr>
                         <td>
                       		<strong>Replace Step</strong>&nbsp;&nbsp;<input type="checkbox" name="copy_sub_process" id="copy_sub_process" onClick="copy_check_sub(1)" value="2" disabled >
                        </td> -->
                    	<!-- <td width="70" class="must_entry_caption" align="right"><b>Step &nbsp;</b></td> -->
                    	<!-- <td width="160">						 
							<?
                                //echo create_drop_down( "cbo_sub_process", 150, $dyeing_sub_process,"", 1, "-- Select Sub Process --", 0, "reset_div(); subprocess_change(this.value);","","","","",'70');
                            ?>
                        </td> --> 
                        <!-- <td width="110"><input type="hidden" name="txt_subprocess_remarks" id="txt_subprocess_remarks" class="text_boxes" value="" style="width:110px;" disabled /></td> -->

                        <!-- <td class="must_entry_caption" width="80">&nbsp;Liquor Ratio</td> -->
                        <!-- <td class="must_entry_caption" width="270"> 
                        	<input type="text" name="txt_liquor_ratio_dtls" id="txt_liquor_ratio_dtls" class="text_boxes_numeric" value=""  onBlur="caculate_tot_liquor()" style="width:60px;" />
                        	&nbsp;Total Liquor(ltr) &nbsp;<input type="text" name="txt_total_liquor_ratio" id="txt_total_liquor_ratio" class="text_boxes_numeric" value="" style="width:70px;" readonly />
                        </td> -->
                        <!-- <td id="button_th" width="80">
                        <input type="button" value="Show Items" name="show" id="show" class="formbuttonplasminus" style="width:70px; float:left" onClick="show_details()"/>
                    	</td>               	
                    </tr> --> 
                </table>
                <div id="list_container_recipe_items" style="margin-top:10px"></div>
            </fieldset> 
            <table width="910">
            	<tr>
                    <td colspan="4" align="center" class="button_container">
						<? //reset_form('recipeEntry_1','list_container_recipe_items*recipe_items_list_view','','copy_id*2','disable_enable_fields(\'cbo_company_id*cbo_company_id\',0)');$('#cbo_company_id').attr('disabled','false');
                        	echo load_submit_buttons($permission, "fnc_recipe_entry", 0,1,"fnResetForm();",1);
                        ?> 
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
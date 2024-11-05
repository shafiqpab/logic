<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Recipe Entry For Gmts wash [Sweater]
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	23.03.2020 
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
echo load_html_head_contents("Recipe Entry For Gmts wash [Sweater]", "../../", 1, 1,$unicode,1);
?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	function show_details()
	{
		var val=$('#copy_sub_process').val();
		//alert(val);return;
		var cbo_company_name = $('#cbo_company_name').val();
		var cbo_sub_process = $('#cbo_sub_process').val();
		var cbo_store_id = $('#cbo_store_id').val();
		var variable_lot = $('#variable_lot').val();
		
		if(form_validation('cbo_company_name*cbo_sub_process*cbo_store_id','Company*Sub Process*Store')==false)
		{
			return;
		}
		$('#cbo_company_name').attr('disabled','disabled');
		$('#cbo_store_id').attr('disabled','disabled');
		var list_view_orders = return_global_ajax_value( cbo_company_name+'**'+cbo_sub_process+'**'+cbo_store_id+'******'+variable_lot, 'item_details', '', 'requires/recipe_entry_controller');

		if(list_view_orders!='')
		{
			$("#list_container_recipe_items").html(list_view_orders);
			setFilterGrid('tbl_list_search',-1);
		}
	}

	function color_row(tr_id)
	{
		var txt_ratio=$('#txt_ratio_'+tr_id).val()*1;
		var stock_check=$('#stock_qty_'+tr_id).text()*1;
		var sub_process=$('#cbo_sub_process').val();
		
		if(txt_ratio>0)
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
		else
		{
			$('#search' + tr_id).css('background-color','#FFFFCC');
			$('#txt_ratio_' + tr_id).css('background-color','White');
		}
	}
			
	function fnc_recipe_entry(operation)
	{
		var btn_type=operation;
		if(operation==4 || operation==5)
		{
			var action="";
			
			if(btn_type==4) action="recipe_entry_print"; else if(btn_type==5) action="recipe_entry_printb1";
			if ( $('#txt_sys_id').val()=='')
			{
				alert ('System ID Not Save.');
				return;
			}
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_labdip_no').val()+'*'+$('#txt_batch_id').val()+'*'+report_title, action, "requires/recipe_entry_controller") 
			//return;
			show_msg("3");
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			/*if(operation==2)
			{
				show_msg('13');
				return;
			}*/
			
			if( form_validation('txt_batch_no*cbo_company_name*txt_recipe_date*txt_color','Batch*Company*Recipe Date*Color')==false )
			{
				return;
			}
			
			var copy_val=$('#copy_id').val();
			if(copy_val==1) //Copy Item
			{
				var cbo_company_name = $('#cbo_company_name').val();
				var update_id_check = $('#update_id_check').val();
			}
			if(copy_val==2)
			{
				var sub_process_data=$("#cbo_sub_process").val()*1;
				if( $('#list_container_recipe_items').html() == "")
				{
					alert("Please Select Item");
					return;
				}
				var row_num=$('#tbl_list_search tbody tr').length-1;
				//alert (row_num);return;
				var data_all=""; var i=0;
		
				for(var j=1; j<=row_num; j++)
				{
					var txt_ratio=$('#txt_ratio_'+j).val();
					var updateIdDtls=$('#updateIdDtls_'+j).val();
					
					if(updateIdDtls!="" || txt_ratio*1>0)
					{
						i++;
						data_all+="&txt_seqno_" + i + "='" + $('#txt_seqno_'+j).val()+"'"+"&product_id_" + i + "='" + $('#product_id_'+j).text()+"'"+"&txt_item_lot_" + i + "='" + $('#txt_item_lot_'+j).val()+"&txt_comments_" + i + "='" + $('#txt_comments_'+j).val()+"'"+"&cbo_dose_base_" + i + "='" + $('#cbo_dose_base_'+j).val()+"'"+"&txt_ratio_" + i + "='" + $('#txt_ratio_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'";
						//alert(data_all);return;
					}
				}
			
			    if(i<1)
				{
					alert("Please Insert Dosage At Least One Item");
					return;
				}
				
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_labdip_no*cbo_company_name*cbo_location*txt_recipe_date*cbo_order_source*txt_booking_order*txt_booking_id*txt_recipe_des*txt_batch_id*cbo_party_name*cbo_method*txt_color*txt_color_id*cbo_color_range*txt_liquor*txt_batch_ratio*txt_liquor_ratio*txt_booking_type*cbo_sub_process*txt_batch_weight*txt_remarks*copy_id*update_id*txt_total_liquor_ratio*txt_liquor_ratio_dtls*txt_time*txt_temparature*txt_ph*cbo_store_id*cbo_item_category*item_group_id*hidd_group_code*hidd_cons_uom*hidd_order_uom*txt_subgroup_name*txt_description*txt_item_size*hidd_newprod_id*cbo_within_group*txt_sys_id*txt_copy_from',"../../")+data_all+'&total_row='+i;
				//alert(data);
			}
			else if(copy_val==1)
			{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_labdip_no*cbo_company_name*update_id_check*cbo_location*txt_recipe_date*cbo_order_source*txt_booking_order*txt_booking_id*txt_recipe_des*txt_batch_id*cbo_party_name*cbo_method*txt_color*txt_color_id*cbo_color_range*txt_liquor*txt_batch_ratio*txt_liquor_ratio*txt_booking_type*cbo_sub_process*txt_batch_weight*txt_remarks*copy_id*update_id*txt_total_liquor_ratio*txt_liquor_ratio_dtls*txt_time*txt_temparature*txt_ph*cbo_store_id*cbo_item_category*item_group_id*hidd_group_code*hidd_cons_uom*hidd_order_uom*txt_subgroup_name*txt_description*txt_item_size*hidd_newprod_id*cbo_within_group*txt_sys_id*txt_copy_from',"../../");
			}
			//alert (data);
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
				document.getElementById('txt_sys_id').value = reponse[3];
				document.getElementById('update_id_check').value = reponse[1];
				
				var sub_process = $('#cbo_sub_process').val();
				show_list_view(reponse[1], 'recipe_item_details', 'recipe_items_list_view', 'requires/recipe_entry_controller', '' ) ;
				setFilterGrid('tbl_list_search',-1);
				$('#list_container_recipe_items').html('');
				$('#copy_id').val(2);
				$('#copy_id').removeAttr('disabled','disabled');
				$('#copy_sub_process').attr('disabled','disabled');
				set_button_status(0, permission, 'fnc_recipe_entry',1,1);	
				//reset_form('','','copy_id*copy_sub_process*cbo_sub_process*txt_subprocess_remarks','','');
				release_freezing();	
			}
			else if(reponse[0]==14)
			{
				alert('Further recipe for same batch No not allowed.\n Recipe No: '+reponse[2]);
			}
			if(reponse[0]==50)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			release_freezing();	
		}
	}
	
	function openmypage_sysNo()
	{
		var cbo_company_name = $('#cbo_company_name').val();
		
		if (form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'System ID Selection Form';	
			var page_link = 'requires/recipe_entry_controller.php?cbo_company_name='+cbo_company_name+'&action=systemid_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1110px,height=390px,center=1,resize=1,scrolling=0','../');
			
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
					load_drop_down('requires/recipe_entry_controller',1, 'load_drop_down_type', 'type_td');
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
		var cbo_company_name= $('#cbo_company_name').val();
		$("#txt_subprocess_remarks").attr("disabled",true); 
		$('#txt_subprocess_remarks').val('');
		
		$('#txt_subprocess_remarks').val(process_remark);
		var process_id_chk = $('#process_id_chk').val();
		load_drop_down('requires/recipe_entry_controller',process_id_chk, 'load_drop_down_type', 'type_td');
		$('#cbo_sub_process').val(sub_process_id);
		$('#txt_subprocess_remarks').val(process_remark);
		
		var copy_val=$('#copy_id').val();
		var cbo_store_id = $('#cbo_store_id').val();
		var sub_process = $('#cbo_sub_process').val();
		subprocess_change(sub_process);
		var variable_lot = $('#variable_lot').val();
		show_list_view(cbo_company_name+'**'+sub_process_id+"**"+cbo_store_id+"**"+update_id+"**"+copy_val+"**"+variable_lot, 'item_details', 'list_container_recipe_items', 'requires/recipe_entry_controller', '');
		setFilterGrid('tbl_list_search',-1);
		//$('#cbo_sub_process').attr('disabled','disabled');
		$('#cbo_company_name').attr('disabled','disabled');
		$('#cbo_store_id').attr('disabled','disabled');
		$('#cbo_location').attr('disabled','disabled');
		
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
		var cbo_company_name = $('#cbo_company_name').val();
		var txt_prod_id = $('#txt_prod_id_'+id).val();
		var txt_item_lot = $('#txt_item_lot_'+id).val();
		//alert  (txt_prod_id);
		if (form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Item Lot Selection Form';	
			var page_link = 'requires/recipe_entry_controller.php?cbo_company_name='+cbo_company_name+'&txt_prod_id='+txt_prod_id+'&txt_item_lot='+txt_item_lot+'&action=itemLot_popup';
			  
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
		
		if(($('#txt_liquor_ratio_dtls').val()*1)>0) $('#txt_total_liquor_ratio').val(tot_liquor_dtls);
		else $('#txt_total_liquor_ratio').val("");
	}
	
	function openmypage_batchNo()
	{
		var cbo_company_name = $('#cbo_company_name').val();
		if (form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Batch No Selection Form';	
			var page_link = 'requires/recipe_entry_controller.php?cbo_company_name='+cbo_company_name+'&action=batch_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=780px,height=410px,center=1,resize=1,scrolling=0','../');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
				var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;	 //Access form field with id="emailfield"
				//alert(theemail);return;
				if(batch_id!="")
				{
					freeze_window(5);
					var batch_val=batch_id.split('_');
					$('#txt_batch_id').val(batch_val[0]);
					get_php_form_data(cbo_company_name+'**'+batch_val[0], "load_data_from_batch", "requires/recipe_entry_controller" );
					$('#cbo_location').attr('disabled','disabled');
					var process_id_chk = $('#process_id_chk').val();
					//alert(process_id_chk);
					load_drop_down('requires/recipe_entry_controller',process_id_chk, 'load_drop_down_type', 'type_td');
					caculate_tot_liquor();
					release_freezing();
				} 
			}
		}
	}	
	
	function copy_check(type)
	{  
		var recipe_prev_id=$('#update_id').val();
		if(type==1)
		{
			$("#list_container_recipe_items").html('');
			show_list_view($('#update_id').val(), 'recipe_item_details', 'recipe_items_list_view', 'requires/recipe_entry_controller', '' ) ;
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
		//alert(recipe_prev_id);
		if(type==1) $('#txt_copy_from').val(recipe_prev_id); else $('#txt_copy_from').val('');
		
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
		}
		else if(document.getElementById('copy_sub_process').checked==false)
		{
			var chk=document.getElementById('copy_sub_process').value=2;
		}
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
	
	function subprocess_change(sub_process)
	{
		$("#txt_subprocess_remarks").attr("disabled",true); 
		$('#txt_subprocess_remarks').val('');  
		$('#show').css('display','block');	
		var cbo_company_name=$('#cbo_company_name').val();
		var update_id=$('#update_id').val();
		var update_id_check=$('#update_id_check').val();
		
		get_php_form_data(cbo_company_name+'**'+sub_process+'**'+update_id_check, "ratio_data_from_dtls", "requires/recipe_entry_controller" );
	}
	
	function fnResetForm()
	{
		//alert(33);
		$("#cbo_company_name").attr("disabled",false); 
		reset_form('recipeEntry_1','list_container_recipe_items*recipe_items_list_view','',$('#copy_id').val()*2,'');
	}

	function fnc_load_party(type,within_group)
	{
		if ( form_validation('cbo_company_name','Company')==false )
		{
			$('#cbo_within_group').val(1);
			return;
		}
		var company = $('#cbo_company_name').val();
		var party_name = $('#cbo_party_name').val();
		var location_name = $('#cbo_location_name').val();
		
		if(within_group==1 && type==1)
		{
			load_drop_down( 'requires/recipe_entry_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td_id' );
		}
		else if(within_group==2 && type==1)
		{
			load_drop_down( 'requires/recipe_entry_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td_id' );
			$('#txt_order_no').removeAttr('onDblClick','onDblClick');
		}
		else if(within_group==1 && type==2)
		{
			load_drop_down( 'requires/recipe_entry_controller', party_name+'_'+2, 'load_drop_down_location', 'party_location_td' ); 
		} 
	}

	function location_select()
	{
		if($('#cbo_location option').length==2)
		{
			if($('#cbo_location option:first').val()==0)
			{
				$('#cbo_location').val($('#cbo_location option:last').val());
			}
		}
		else if($('#cbo_location option').length==1)
		{
			$('#cbo_location').val($('#cbo_location option:last').val());
		}	
	}

</script>
</head>

<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission); ?>
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
                        <td><strong>Copy</strong>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="copy_id" id="copy_id" onClick="copy_check(1)" value="2" disabled ></td>
                        <td><div id="batch_type" style="color:#F00; font-size:18px"></div></td>
                    </tr>
                	<tr>
                        <td>Labdip No</td>
                        <td><input type="text" name="txt_labdip_no" id="txt_labdip_no" class="text_boxes" style="width:140px;" /></td>
                        <td>Recipe Description</td>
                        <td><input type="text" name="txt_recipe_des" id="txt_recipe_des" class="text_boxes" style="width:140px;" /></td>
                        <td class="must_entry_caption">Batch No</td>
                        <td><input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:70px;" placeholder="Browse" onDblClick="openmypage_batchNo();" readonly/>
                        <input type="text" name="txt_ext_no" id="txt_ext_no" class="text_boxes_numeric" style="width:58px;" placeholder="Ext. No." />
                        
                        <input type="hidden" name="txt_batch_id" id="txt_batch_id" style="width:40px;" /></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">W. Company</td>
                        <td><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/recipe_entry_controller', this.value, 'load_drop_down_location', 'location_td'); location_select(); fnc_load_party(1, document.getElementById('cbo_within_group').value); load_drop_down('requires/recipe_entry_controller', this.value+'__'+document.getElementById('cbo_location').value, 'load_drop_down_store', 'store_td'); get_php_form_data( this.value, 'company_wise_report_button_setting', 'requires/recipe_entry_controller');"); ?>
                        <input type="hidden" id="variable_lot" name="variable_lot" />
                        </td>
                        <td>W. Location</td>
                        <td id="location_td"><? echo create_drop_down("cbo_location", 152, $blank_array,"", 1,"-- Select Location --", 0,""); ?></td>
                        <td class="must_entry_caption">Recipe Date</td>
                        <td><input type="text" name="txt_recipe_date" id="txt_recipe_date" class="datepicker" style="width:140px;" value="<? echo date("d-m-Y"); ?>" tabindex="6" /></td>
                    </tr>
                    <tr>
                        <td>Within Group</td>
                        <td><?php echo create_drop_down( "cbo_within_group", 150, $yes_no,"", 0, "-- Select --",0, "fnc_load_party(1,this.value); ",1 ); ?></td>
                        <td>Party Name</td>
                        <td id="buyer_td_id"><? echo create_drop_down( "cbo_party_name", 152, $blank_array,"", 1, "-- Select Party --", $selected, "",1 ); ?></td>
                        <td class="must_entry_caption">Color</td>
                        <td>
                            <input type="text" name="txt_color" id="txt_color" class="text_boxes" value="" style="width:140px;" tabindex="10" disabled />
                            <input type="hidden" name="txt_color_id" id="txt_color_id" class="text_boxes" value="" style="width:120px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td>Color Range</td>
                        <td><? echo create_drop_down("cbo_color_range", 152, $color_range,"", 1,"-- Select --", '','',''); ?></td>
						<td>Batch Dosage</td>
                        <td>
                            <input type="text" name="txt_batch_ratio" id="txt_batch_ratio" class="text_boxes_numeric" value="1" style="width:140px;" placeholder="Batch" onBlur="caculate_tot_liquor()" /> 
                            <input type="text" name="txt_liquor_ratio" id="txt_liquor_ratio" class="text_boxes_numeric" value="" style="width:62px; display:none;" placeholder="Liquor" onBlur="caculate_tot_liquor()" />
                        </td>
                        <td class="must_entry_caption" style="display:none">Total Liquor(ltr)</td>
                        <td style="display:none"><input type="text" name="txt_liquor" id="txt_liquor" class="text_boxes_numeric" value="" style="width:140px; display:none" readonly /></td>
                        <td>Order No.</td>
                        <td>
                            <input type="text" name="txt_order" id="txt_order" class="text_boxes" value="" style="width:140px;" disabled />
                            <input type="hidden" name="txt_booking_type" id="txt_booking_type" class="text_boxes" value="" style="width:60px;" />
                        </td>
                    </tr>
                    <tr style="display: none;">
                        <td>Yarn Lot</td>
                        <td><input type="text" name="txt_yarn_lot" id="txt_yarn_lot" class="text_boxes" value="" style="width:140px;" disabled /></td>
                        <td>Brand</td>
                        <td><input type="text" name="txt_brand" id="txt_brand" class="text_boxes" value="" style="width:140px;" disabled /></td>
                        <td>Count</td>
                        <td><input type="text" name="txt_count" id="txt_count" class="text_boxes" value="" style="width:140px;" disabled /></td>
                        <td><? echo create_drop_down( "cbo_method", 152, $dyeing_method,"", 1, "--Select Method--", $selected, "",0 ); ?></td>
                        
                    </tr>
                    <tr>
                    	<td>Batch Weight</td>
                        <td><input type="text" name="txt_batch_weight" id="txt_batch_weight" class="text_boxes_numeric" style="width:140px;" disabled="" /></td>
                    	<td style="display: none;">
                        	<input type="text" name="txt_booking_order" id="txt_booking_order" class="text_boxes" style="width:140px;" disabled/>
                            <input type="hidden" name="txt_booking_id" id="txt_booking_id" class="text_boxes" style="width:120px;" />
                        </td>
                        <td style="display: none;"><? echo create_drop_down("cbo_order_source", 152, $order_source,"", 1,"-- Select Source --", $selected,"",1,"","","",""); ?></td>
                        <td style="display: none;">Trims Weight</td>
                        <td style="display: none;"><input type="text" name="txt_trims_weight" id="txt_trims_weight" class="text_boxes_numeric" value="" style="width:140px;" disabled /></td>
                    </tr>
                    <tr>
                    	<td>Copy From</td>
                        <td><input type="text" name="txt_copy_from" id="txt_copy_from" class="text_boxes" value="" style="width:140px;" disabled="disabled" /></td>
                        <td>Remarks</td>
                        <td colspan="3"><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" value="" style="width:480px;" /></td>
                   </tr>
             </table>
			</fieldset>                 
            <fieldset style="width:920px; margin-top:10px">
            <legend>Item Details</legend>
 				<table cellpadding="0" cellspacing="0" border="0" width="100%">
 					<tr>
 						<td width="100"></td>
 						<td width="80" class="must_entry_caption" align="center"><b>Wash Type</b></td>
 						<td width="80" class="must_entry_caption" align="center"><b>Store</b></td>
 						<td width="80" align="center">Liquor Dosage</td>
 						<td width="80" align="center">Total Liquor(ltr)</td>
 						<td width="100" align="center">Time (min)</td>
 						<td width="100" align="center">Temp (&#8451;)</td>
 						<td width="100" align="center">PH</td>
 						<td width="100"></td>
 					</tr>
 					<tr>
 						<td><strong>Replace Step</strong>&nbsp;&nbsp;<input type="checkbox" name="copy_sub_process" id="copy_sub_process" onClick="copy_check_sub(1)" value="2" disabled ></td>
                        <td width="80" id="type_td"><? echo create_drop_down( "cbo_sub_process", 80, $blank_array,"", 1, "-- Select Wash Type --", 0, "","","","","",'70'); ?>
                            <input type="hidden" name="txt_subprocess_remarks" id="txt_subprocess_remarks" class="text_boxes" value="" style="width:110px;" disabled />
                        </td>
                        <td align="center" id="store_td"><? echo create_drop_down( "cbo_store_id", 80, $blank_array,"", "1", "-Store-", 0, "","1","","","","","","" );  ?></td>
                        <td><input type="text" name="txt_liquor_ratio_dtls" id="txt_liquor_ratio_dtls" class="text_boxes_numeric" value="" onBlur="caculate_tot_liquor();" style="width:80px;" /></td>
                        <td><input type="text" name="txt_total_liquor_ratio" id="txt_total_liquor_ratio" class="text_boxes_numeric" value="" style="width:80px;"  /></td>
                        <td><input type="text" name="txt_time" id="txt_time" class="text_boxes_numeric" style="width:90px;"> </td>
                        <td><input type="text" name="txt_temparature" id="txt_temparature" class="text_boxes_numeric" style="width:90px;"></td>
                        <td><input type="text" name="txt_ph" id="txt_ph" class="text_boxes_numeric" style="width:90px;"></td>
                        <td id="button_th" width="100">
                        	<input type="button" value="Show Items" name="show" id="show" class="formbuttonplasminus" style="width:70px; float:left" onClick="show_details()"/>
                        	<input type="hidden" id="item_group_id" />
                            <input type="hidden" id="hidd_group_code" />
                            <input type="hidden" id="hidd_order_uom" />
                            <input type="hidden" id="hidd_cons_uom" />
                            <input type="hidden" id="hidd_newprod_id" />
                            <input type="hidden" id="cbo_item_category" />
                            <input type="hidden" id="txt_subgroup_name" />
                            <input type="hidden" id="txt_description" />
                            <input type="hidden" id="txt_item_size" />
                    	</td>
 					</tr>
                </table>
                <div id="list_container_recipe_items" style="margin-top:10px"></div>
            </fieldset> 
            <table width="910">
            	<tr>
                    <td colspan="4" align="center" class="button_container">
						<? echo load_submit_buttons($permission, "fnc_recipe_entry", 0,1,"fnResetForm();",1); ?> 
                        <input type="button" name="print" id="print" value="Print" onClick="fnc_recipe_entry(4);" style="width:100px;display:none;" class="formbuttonplasminus" />
                        <input type="button" name="print1" id="print1" value="Print B1" onClick="fnc_recipe_entry(5);" style="width:100px;display:none;" class="formbuttonplasminus" />
                        <input type="hidden" name="update_id" id="update_id"/>
                        <input type="hidden" name="update_id_check" id="update_id_check"/>
                        <input type="hidden" name="update_dtls_id" id="update_dtls_id"/>
                        <input type="hidden" name="process_id_chk" id="process_id_chk" value="0" />
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
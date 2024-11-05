<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Color Ingredients
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	24-12-2019
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
echo load_html_head_contents("Color Ingredients","../../", 1, 1, $unicode,1,'');

?>	
<script>

	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var mandatory_field=new Array();
	var mandatory_message=new Array();
	<?
	
	if(isset($_SESSION['logic_erp']['mandatory_field'][701]))
	{
		echo "var mandatory_field = '". implode('*',$_SESSION['logic_erp']['mandatory_field'][701]) . "';\n";
		echo "var mandatory_message = '". implode('*',$_SESSION['logic_erp']['mandatory_message'][701]) . "';\n";
	}
	?>
	// Master Form-----------------------------------------------------------------------------
	function fnc_newColor_ref(page_link,title)
	{
		var page_link='requires/color_ingredients_controller.php?action=colorrefentry_popup';
		var title="Color Ref. Entry Popup";
		var data=$('#cbo_company_name').val();
		var user_id='<? echo $_SESSION['logic_erp']['user_id']; ?>';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&data='+data, title, 'width=1200px,height=450px,center=1,resize=0,scrolling=0','../')
		release_freezing();
		emailwindow.onclose=function()
		{
			//load_drop_down( 'requires/color_ingredients_controller', '', 'load_drop_template_name', 'template_td');
		}
	}
	
	function fnc_coloringredients_entry( operation )
	{
		var type=0;
		freeze_window(operation);
		if( operation==6)
		{
			var txt_sysNo=trim($('#txt_sysNo').val());
			if(txt_sysNo=="")
			{
				alert("System Id Not Found"); release_freezing(); return;
			}
			type=6; operation=0;
		}
		else if( operation==7)
		{
			var txt_sysNo=trim($('#txt_sysNo').val());
			if(txt_sysNo=="")
			{
				alert("System Id Not Found"); release_freezing(); return;
			}
			type=7; operation=0;
		}
		
		 if(operation==4)
		{
			if ( $('#txt_sys_id').val()=='')
			{
				alert ('System ID Not Save.');
				release_freezing();
				return;
			}
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#txt_update_id').val()+'*'+$('#txt_sysNo').val()+'*'+$('#hid_colorref_id').val()+'*'+report_title, "lab_recipe_card_print", "requires/color_ingredients_controller") 
			//return;
			show_msg("3");
			release_freezing();
		} 
		if(operation==5)
		{
			if ( $('#txt_sys_id').val()=='')
			{
				alert ('System ID Not Save.');
				release_freezing();
				return;
			}
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#txt_update_id').val()+'*'+$('#txt_sysNo').val()+'*'+$('#hid_colorref_id').val()+'*'+report_title, "lab_recipe_card_print2", "requires/color_ingredients_controller") 
			//return;
			show_msg("3");
			release_freezing();
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if(operation==2)
			{
				alert("Delete Restricted");
				release_freezing();
				return;
			}
			
			if(mandatory_field!="") 
			{
				if (form_validation(mandatory_field,mandatory_message)==false)
				{
					release_freezing();
					return;
				}
			}
			
			if( form_validation('txt_color_ref*cbo_lab_company_name*cbo_store_name*cbo_company_name*cbo_section*cbo_lab_source*cbo_buyer_name','Color Ref.*LabCompany*Store*Req Company*Section*Lab Source*Client')==false )
			{
				release_freezing();
				return;
			}
			else
			{
				var row_num=$('#tbl_list_search tbody tr').length-1;
				//alert (row_num);return;
				var data_all=""; var i=0;
				for(var j=1; j<=row_num; j++)
				{
					var txt_ratio=$('#txt_ratio_'+j).val();
					var updateIdDtls=$('#updateIdDtls_'+j).val();
					var dose=$('#cbo_dose_base_'+j).val();
	
					if(txt_ratio*1>0)
					{
						/*if (form_validation('cbo_dose_base_'+j,'Dose Base')==false)
						{
							release_freezing();
							return;
						}*/
	
						i++;
						data_all+="&txt_seqno_" + i + "='" + $('#txt_seqno_'+j).val()+"'"+"&product_id_" + i + "='" + $('#product_id_'+j).text()+"'"+"&txt_item_lot_" + i + "='" + $('#txt_item_lot_'+j).val()+"'"+"&txt_comments_" + i + "='" + $('#txt_comments_'+j).val()+"'"+"&cbo_dose_base_" + i + "='" + $('#cbo_dose_base_'+j).val()+"'"+"&txt_ratio_" + i + "='" + $('#txt_ratio_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'";
						//alert(data_all); release_freezing(); return;
					}
				}
				
				/*if(i<1)//ISD-23-19360
				{
					alert("Please Insert Ratio At Least One Item");
					release_freezing();
					return;
				}*/
				
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_color_ref*hid_colorref_id*txt_correction*cbo_company_name*cbo_section*cbo_buyer_name*txt_colorDesc*txt_pantone*txt_dyeCode*txt_dyeType*txt_shadeGrp*txt_shadeGrpColor*txt_shadeNo*txt_shadeBrightness*txt_commonlyUsed*txt_fabricTypeCps*txt_merchan_remarks*txt_update_id*txt_sysNo*cbosample_no*cbo_lab_company_name*cbo_store_name*txt_order_no*txt_style_ref*cbo_ratio*txt_yarn_lot*txt_lab_recipie_date*txt_construction*txt_blend*cbo_shade*txt_cmc_de*txt_whiteness*txt_primary_source*txt_ref_no*txt_disperse_dying*txt_reactive_dying*txt_matching_standard*cbo_lab_source*cbo_dyeing_part*cbo_color_range*cbo_dyeing_upto',"../../")+data_all+'&total_row='+i+"&type="+type;
				
				//alert(data); release_freezing(); return;
				
				http.open("POST","requires/color_ingredients_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_coloringredients_response;
			}
		}
	}
	
	function fnc_coloringredients_response()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);
			var reponse=trim(http.responseText).split('**');

			show_msg(trim(reponse[0]));
			if(reponse[0]==11)
			{
				alert(reponse[1]);release_freezing();return;
			}
			
			if(reponse[0]==0 || reponse[0]==1)
			{
				$("#txt_color_ref").attr("disabled",true).attr("ondblclick","");
			}
			
			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
			{
				document.getElementById('txt_update_id').value = reponse[1];
				document.getElementById('txt_sysNo').value = reponse[2];
				document.getElementById('txt_correction').value = reponse[3];
				document.getElementById('cbosample_no').value = reponse[4];
				
				var list_view_orders = return_global_ajax_value( $('#cbo_lab_company_name').val()+'***'+reponse[1]+'***'+$('#cbo_store_name').val(), 'item_details', '', 'requires/color_ingredients_controller');
				if(list_view_orders!='')
				{
					$("#list_container_items").html(list_view_orders);
					setFilterGrid('tbl_list_search',-1);
				}
				if(reponse[0]==2)
					set_button_status(0, permission, 'fnc_coloringredients_entry',1,0);
				else
					set_button_status(1, permission, 'fnc_coloringredients_entry',1,0);
			}
			release_freezing();
		}
	}
	
	function generate_report_file(data,action,page)
	{
		window.open("requires/color_ingredients_controller.php?data=" + data+'&action='+action, true );
	}
	
	function fnc_openmyPage_colorRef()
	{
		var page_link='requires/color_ingredients_controller.php?action=colorref_popup';
		var title="Color Ref. Search Popup";
		var data=$('#cbo_company_name').val();
		var k=1;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&data='+data, title, 'width=630px,height=450px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var str_data=this.contentDoc.getElementById("selected_str_data").value; // product ID
			if(str_data!="")
			{
				get_php_form_data(str_data, "populate_data_from_search_popup", "requires/color_ingredients_controller" );
				//get_php_form_data( str_data, 'company_wise_report_button_setting','requires/color_ingredients_controller' );
				
				/*var list_view_orders = return_global_ajax_value( $('#cbo_company_name').val()+'***0', 'item_details', '', 'requires/color_ingredients_controller');
				if(list_view_orders!='')
				{
					$("#list_container_items").html(list_view_orders);
					setFilterGrid('tbl_list_search',-1);
				}*/
			}
		}
	}
	
	function fn_item_details(store_id)
	{
		if( form_validation('cbo_lab_company_name*cbo_store_name','Lab Company*Store')==false )
		{
			return;
		}
		var list_view_orders = return_global_ajax_value( $('#cbo_lab_company_name').val()+'***0***'+store_id, 'item_details', '', 'requires/color_ingredients_controller');
		if(list_view_orders!='')
		{
			$("#list_container_items").html(list_view_orders);
			setFilterGrid('tbl_list_search',-1);
		}
		disable_enable_fields( "cbo_lab_company_name*cbo_store_name", 1, "", "" );
	}
	
	function fnc_openmyPage_sys()
	{
		if( form_validation('cbo_lab_company_name','Lab Company')==false )
		{
			return;
		}
		var cbo_company_id=$('#cbo_lab_company_name').val();
		var title = 'System ID Selection Form';
		var page_link = 'requires/color_ingredients_controller.php?cbo_company_id='+cbo_company_id+'&action=systemid_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=390px,center=1,resize=1,scrolling=0','../');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var update_id=this.contentDoc.getElementById("hidden_update_id").value;	 //Access form field with id="emailfield"

			if(update_id!="")
			{
				freeze_window(5);
				$('#list_container_items').html('');
				get_php_form_data(update_id, "populate_mstdata_from_search_popup", "requires/color_ingredients_controller" );
				disable_enable_fields( "cbo_lab_company_name*cbo_store_name", 1, "", "" );
				var list_view_orders = return_global_ajax_value( $('#cbo_lab_company_name').val()+'***'+update_id+'***'+$('#cbo_store_name').val(), 'item_details', '', 'requires/color_ingredients_controller');
				if(list_view_orders!='')
				{
					$("#list_container_items").html(list_view_orders);
					setFilterGrid('tbl_list_search',-1);
				}
				
				set_button_status(1, permission, 'fnc_coloringredients_entry',1,1);
				$("#txt_color_ref").attr("disabled",true).attr("ondblclick","");
				release_freezing();
			}
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
	
	function fnc_copy_cost_sheet( operation )
	{
		//alert( $('#txt_update_id').val() );
		var data_copy="action=copy_cost_sheet&operation="+operation+get_submitted_data_string('txt_costSheetNo*txt_update_id*txt_styleRef*cbo_season_id*cbo_buyer_id*hid_qc_no',"../../");
		var data=data_copy;
		//alert(data);
		//return;
		freeze_window(operation);
		http.open("POST","requires/color_ingredients_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_qcosting_entry_response;
	}
	
	function fnc_reset()
	{
		location.reload();
	}
	function load_buyer(source){
		if( form_validation('cbo_company_name','Req Company')==false )
		{
			return;
		}
		else{
			var req_company=$('#cbo_company_name').val();
			var buyer_data=req_company+'_'+source;
			load_drop_down( 'requires/color_ingredients_controller', buyer_data, 'load_drop_down_buyer', 'buyer_td');
		}		
	}
	
	
	
	
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission);  ?>
        <form name="colorIngredients_1" id="colorIngredients_1" autocomplete="off">
            <fieldset style="width:1200px;">
            <table width="1200px" cellspacing="2" cellpadding="0" border="0">
            	<tr>
                    <td colspan="5" align="right"><strong>Lab ID</strong></td>
                    <td colspan="5">
                    	<input style="width:110px;" type="text" class="text_boxes" name="txt_sysNo" id="txt_sysNo" placeholder="Browse" onDblClick="fnc_openmyPage_sys();" readonly />
                        <input style="width:40px;" type="hidden" name="txt_update_id" id="txt_update_id"/>
                    </td>
                </tr>
                <tr>
                    <td width="120" class="must_entry_caption"><strong>Color Ref.</strong></td>
                    <td width="130">
                    	<input style="width:110px;" type="text" class="text_boxes" name="txt_color_ref" id="txt_color_ref" placeholder="Browse" onDblClick="fnc_openmyPage_colorRef();" readonly />
                        <input type="hidden" id="hid_colorref_id">
                    </td>
                    <td width="90"><strong>Correction</strong></td>
                    <td width="130"><input style="width:30px;" type="text" class="text_boxes_numeric" name="txt_correction" id="txt_correction" value="1" readonly disabled /><strong title="Sample No">&nbsp; S.N: &nbsp;</strong><? echo create_drop_down( "cbosample_no", 40, $dyeinglab_dyecode_arr,"", 1, "Select", 1, "", 1, "", "", "", "", "", "", "" ); ?></td>
                    <td width="110" class="must_entry_caption"><strong>Lab Company</strong></td>
                    <td width="130"><? echo create_drop_down( "cbo_lab_company_name", 120, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-Lab Company-", $selected, "load_drop_down( 'requires/color_ingredients_controller', this.value, 'load_drop_down_store', 'store_td' );get_php_form_data(this.value, 'company_wise_report_button_setting','requires/color_ingredients_controller'); "); ?></td>
                    <td width="110" class="must_entry_caption"><strong>Store Name</strong></td>
                    <td id="store_td" width="130"><? echo create_drop_down( "cbo_store_name", 120, $blank_array,"",1, "--Select store--", 1, "" );; ?></td>
                    <td width="110" class="must_entry_caption"><strong>Req Company</strong></td>
                    <td width="130"><? echo create_drop_down( "cbo_company_name", 120, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-Lab Location-", $selected, ""); ?></td>
                </tr>
                <tr>
					<td class="must_entry_caption"><strong>Lab Source</strong></td>
                    <td><? echo create_drop_down( "cbo_lab_source", 120, $lab_source_arr,"", 1, "-- Select Lab Source --", "1", "load_buyer(this.value)","" ); ?></td>
                    <td class="must_entry_caption"><strong>Client</strong></td>
                    <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-Buyer-", $selected, "" ); ?></td>
                	<td class="must_entry_caption"><strong>Color Desc.</strong></td>
                    <td><input style="width:110px;" type="text" class="text_boxes" name="txt_colorDesc" id="txt_colorDesc" /></td>
                    <td class="must_entry_caption"><strong>Pantone</strong></td>
                    <td><input name="txt_pantone" id="txt_pantone" class="text_boxes" type="text" style="width:110px;" value="" /></td>
					<td class="must_entry_caption"><strong>Section</strong></td>
                    <td><? echo create_drop_down( "cbo_section", 120, $lab_section,"", 1, "-Section-", 2, ""); ?></td>
                    
                </tr>
				<tr>
                	<td><strong>Order No</strong></td>
					<td><input style="width:110px;" type="text" class="text_boxes" name="txt_order_no" id="txt_order_no" /></td>
                    <td><strong>Style Ref</strong></td>
					<td><input style="width:110px;" type="text" class="text_boxes" name="txt_style_ref" id="txt_style_ref" /></td>
					<td><strong>L.Ratio</strong></td>
					<td ><? echo create_drop_down( "cbo_ratio", 120, $liquor_ratioArr,"", 1, "-Section-", $selected, ""); ?></td>
					<td><strong>Yarn Lot</strong></td>
					<td><input style="width:110px;" type="text" class="text_boxes" name="txt_yarn_lot" id="txt_yarn_lot" /></td>
					<td><strong>Lab Recipe Date</strong></td>
                    <td><input class="datepicker" type="text" style="width:110px" name="txt_lab_recipie_date" id="txt_lab_recipie_date" value="<?=date("d-m-Y"); ?>" disabled /></td>
                </tr>
				<tr>
					<td><strong>F.Construction</strong></td>
					<td><input style="width:110px;" type="text" class="text_boxes" name="txt_construction" id="txt_construction" /></td>
					<td><strong>Blend</strong></td>
					<td><input style="width:110px;" type="text" class="text_boxes" name="txt_blend" id="txt_blend" /></td>
					<td><strong>Shade Group</strong></td>
					<td ><? echo create_drop_down( "cbo_shade", 120, $shade_groupArr,"", 1, "-Section-", $selected, ""); ?></td>
					<td><strong>CMC DE</strong></td>
					<td><input style="width:110px;" type="text" class="text_boxes" name="txt_cmc_de" id="txt_cmc_de" /></td>
					<td><strong>Whiteness</strong></td>
					<td><input style="width:110px;" type="text" class="text_boxes" name="txt_whiteness" id="txt_whiteness" /></td>
                </tr>
				<tr>
					<td><strong>Primary Light Source</strong></td>
					<td><input style="width:110px;" type="text" class="text_boxes" name="txt_primary_source" id="txt_primary_source" /></td>
					<td><strong>Ref No</strong></td>
					<td><input style="width:110px;" type="text" class="text_boxes" name="txt_ref_no" id="txt_ref_no" /></td>
					<td><strong>Disperse Dying</strong></td>
					<td><input style="width:110px;" type="text" class="text_boxes" name="txt_disperse_dying" id="txt_disperse_dying" /></td>
					<td><strong>Reactive Dying</strong></td>
					<td><input style="width:110px;" type="text" class="text_boxes" name="txt_reactive_dying" id="txt_reactive_dying" /></td>
					<td><strong>Matching Standard</strong></td>
					<td><input style="width:110px;" type="text" class="text_boxes" name="txt_matching_standard" id="txt_matching_standard" /></td>
                </tr>
                <tr>
                	<td><strong>Shade Grp & Bright</strong></td>
                    <td>
                    	<input style="width:40px;" type="text" class="text_boxes" name="txt_shadeGrp" id="txt_shadeGrp" disabled />&nbsp;
                        <input style="width:50px;" type="text" class="text_boxes" name="txt_shadeGrpColor" id="txt_shadeGrpColor" disabled />
                    </td>
                    <td><strong>Shade</strong></td>
                    <td>
                    	<input style="width:40px;" type="text" class="text_boxes" name="txt_shadeNo" id="txt_shadeNo" />&nbsp;
                        <input style="width:50px;" type="text" class="text_boxes" name="txt_shadeBrightness" id="txt_shadeBrightness" disabled />
					</td>
					<td><strong>Dye Type</strong></td>
                    <td>
                    	<input style="width:40px;" type="text" class="text_boxes" name="txt_dyeCode" id="txt_dyeCode" disabled />&nbsp;
                    	<input style="width:50px;" type="text" class="text_boxes" name="txt_dyeType" id="txt_dyeType" disabled />
                    </td>
                    <td><strong>Fabric Type/CPS</strong></td>
                    <td><input style="width:110px;" type="text" class="text_boxes" name="txt_fabricTypeCps" id="txt_fabricTypeCps" /></td>
                    <td><strong>Remarks</strong></td>
                    <td>
                    	<input style="width:110px;" type="text" class="text_boxes" name="txt_merchan_remarks" id="txt_merchan_remarks" />
                    	<!--Commonly Used--><input style="width:110px; display:none" type="text" class="text_boxes" name="txt_commonlyUsed" id="txt_commonlyUsed" />
                    </td>
                </tr>
				<tr>
                	<td><strong>Dyeing Part</strong></td>
                    <td ><? echo create_drop_down( "cbo_dyeing_part", 120, $fabric_dyeing_part_arr,"", 1, "-- Select --", $selected, ""); ?></td>
                    <td><strong>Color Range</strong></td>
					<td ><? echo create_drop_down( "cbo_color_range", 120, $color_range,"", 1, "-- Select --", $selected, ""); ?></td>
					<td><strong>Dyeing Upto</strong></td>
                    <td ><? echo create_drop_down( "cbo_dyeing_upto", 120, $dyeing_sub_process,"", 1, "-- Select --", $selected, "","","92,117"); ?></td>
                    <td><strong></strong></td>
                    <td></td>
                    <td><strong></strong></td>
                    <td></td>
                </tr>
                
            </table>
            </fieldset>
            <br>
            <fieldset style="width:1200px;">
                <div id="list_container_items" style="margin-top:10px"></div>
                <table width="1200" cellspacing="2" cellpadding="0" border="0">
                	<tbody>
                    	<tr>
                        	<td align="center"><input type="button" id="set_button" class="image_uploader" style="width:200px;" value="Create Color Reference" onClick="fnc_newColor_ref();" /></td>
                            <td align="center"><input type="button" id="set_button" class="image_uploader" style="width:200px;" value="Copy into New Correction" onClick="fnc_coloringredients_entry(6);" /></td>
                            <td align="center"><input type="button" id="set_button" class="image_uploader" style="width:200px;" value="Copy into New Sample" onClick="fnc_coloringredients_entry(7);" /></td>
                		</tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td align="center" colspan="3" valign="middle" class="button_container">
							<? echo load_submit_buttons($permission,"fnc_coloringredients_entry",0,0,"fnc_reset();",1); ?><input type="button" value="Print 2" onClick="fnc_coloringredients_entry(5);" style="width:100px;display: none;" name="print2" id="print2" class="formbutton" />
							<input type="button" value="Print" onClick="fnc_coloringredients_entry(4);" style="width:100px;display: none;" name="print1" id="print1" class="formbutton" /></td>
                        </tr>
                    </tfoot>      
                </table>
            </fieldset>
        </form>
        </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
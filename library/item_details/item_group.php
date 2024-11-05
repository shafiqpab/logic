<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Item Group Info", "../../", 1, 1,$unicode,'','');
$user_id=$_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT item_cate_id FROM user_passwd where id=$user_id");
$item_cat_cond = $userCredential[0][csf("item_cate_id")];
if($item_cat_cond!=''){$item_cat_id_cond=" and item_category in ($item_cat_cond)";}

?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';
			
	function fnc_item_group( operation )
	{
		//alert($('#hide_group_code').val());return;
		/* if( $('#hide_group_code').val() == 1 && $('#txt_group_code').val() == "" ) 
		{	
			var bgcolor='-moz-linear-gradient(bottom, rgb(254,151,174) 0%, rgb(255,255,255) 10%, rgb(254,151,174) 96%)';					
			$('#messagebox_main', window.parent.document).fadeTo(100,1,function(){  //start fading the messagebox
				$(this).html('Please Insert Group Code').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
			});	
			$('#txt_group_code').focus();
			document.getElementById('txt_group_code').style.backgroundImage=bgcolor;
			return;	
		} */
		
		if (form_validation('cbo_item_category*txt_item_name*cbo_order_uom*cbo_cons_uom*txt_conversion_factor*cbo_fancy_item','Item Category*Item Group*Order UOM*Cons UOM*Conversion Factor*Fancy Item')==false)
		{
			return;
		}

		
		if(document.getElementById('txt_conversion_factor').value==0)
		{      
			 var bgcolor='-moz-linear-gradient(bottom, rgb(254,151,174) 0%, rgb(255,255,255) 10%, rgb(254,151,174) 96%)';
			 $('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
			 { 
				$(this).html('Value must be Greater than zero').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
			 });
			 document.getElementById('txt_conversion_factor').focus();
			 document.getElementById('txt_conversion_factor').style.backgroundImage=bgcolor;
			 return;
		}
		if(document.getElementById('cbo_order_uom').value==document.getElementById('cbo_cons_uom').value &&  document.getElementById('txt_conversion_factor').value>1 )
		{      
			 var bgcolor='-moz-linear-gradient(bottom, rgb(254,151,174) 0%, rgb(255,255,255) 10%, rgb(254,151,174) 96%)';
			 $('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
			 { 
				$(this).html('Value must be one').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
			 });
			 document.getElementById('txt_conversion_factor').focus();
			 document.getElementById('txt_conversion_factor').style.backgroundImage=bgcolor;
			 return;
		}
		else
		{
			eval(get_submitted_variables('cbo_item_category*txt_group_code*txt_item_name*txt_main_group_id*cbo_trim_type*cbo_order_uom*cbo_cons_uom*txt_conversion_factor*cbo_fancy_item*cbo_status*cbo_cal_parameter*cbo_ratecal_parameter*update_id'));
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_item_category*txt_group_code*txt_item_name*txt_main_group_id*cbo_trim_type*cbo_order_uom*cbo_cons_uom*txt_conversion_factor*cbo_fancy_item*cbo_status*cbo_cal_parameter*cbo_ratecal_parameter*cbo_ordUOMDecPlaceRate*cbo_ordUOMDecPlaceAmt*cbo_consUOMDecPlaceRate*cbo_consUOMDecPlaceAmt*update_id*txt_hs_code*chk_zipper*cbo_section*cbo_ordUOMDecPlaceQnt*cbo_consUOMDecPlaceQnt',"../../");
			 
			freeze_window(operation);
			 
			http.open("POST","requires/item_group_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_item_group_reponse;
		}
	}
	
	function fnc_item_group_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==5555)
			{
				alert("Delete is not allowed for this item");
				$('#messagebox_main', window.parent.document).html('Data is not Deleted Successfully').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500); 
			}
			else if(reponse[0]==30)
			{
				alert(reponse[1]);release_freezing(); return;
			}
			else if(reponse[0]==50)
			{
				alert("Delete is not allowed for this item");
				$('#messagebox_main', window.parent.document).html('Data is not Deleted Successfully').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500); 
			}
			else
			{
				if (reponse[0].length>2) reponse[0]=10;
				if (reponse[0]==1)
				{
					if (reponse[2].length>0) alert('Transaction Found. '+reponse[2]+' Update Not Possible');
				}
				show_msg(reponse[0]);
			}
			show_list_view(reponse[1],'item_group_list_view','item_group_list_view','../item_details/requires/item_group_controller','setFilterGrid("list_view",-1)');
			reset_form('itemgroup_1','','','cbo_fancy_item,2*txt_conversion_factor,1','','');
			disable_enable_fields('txt_group_code');
			
			set_button_status(0, permission, 'fnc_item_group',1);
			release_freezing();
		}
	}
	
	function set_con_factor_value()
	{
		var order_uom=document.getElementById('cbo_order_uom').value;
		var cons_uom=document.getElementById('cbo_cons_uom').value;
		if( cons_uom*1 == order_uom*1)
		{
			document.getElementById('txt_conversion_factor').value=1;
		}
		else
		{
			document.getElementById('txt_conversion_factor').value="";
		}
	}
	
	function item_category_add(id,type)
	{		
		get_php_form_data(id,type,'requires/item_group_controller');
	}

	function trim_fancy_disable(category)
	{
		if (category==4)
		{
			$('#cbo_trim_type').val('');
			//$('#cbo_trim_type').attr('disabled','disabled');
			$('#cbo_fancy_item').val('2');
			//$('#cbo_fancy_item').attr('disabled','disabled');
		}
		else
		{
			$('#cbo_trim_type').removeAttr('disabled','disabled');
			$('#cbo_fancy_item').removeAttr('disabled','disabled');
		}
	}
	
	function open_rate_popup(id)
	{ 
		if(id=="")
		{
		 	alert("Save Data First");
			return;
		}
		var  cbo_item_category= document.getElementById('cbo_item_category').value;
		
		var page_link="requires/item_group_controller.php?action=open_rate_popup_view&mst_id="+trim(id)+"&cbo_item_category="+trim(cbo_item_category);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Rate Pop Up", 'width=400px,height=300px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
		}		
	}

	function openmypage_main_group()
	{
		if (form_validation('cbo_item_category','Item Category')==false)
		{
			return;
		}

		var cbo_item_category = $('#cbo_item_category').val();
		var txt_main_group_id = $('#txt_main_group_id').val();
		var title = 'Main Group Selection Form';
		var page_link='requires/item_group_controller.php?action=main_group_popup&txt_main_group_id='+txt_main_group_id+'&cbo_item_category='+cbo_item_category;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var main_group_id=this.contentDoc.getElementById("hidden_main_group_id").value;	 //Access form field with id="emailfield"
			var main_group_name=this.contentDoc.getElementById("hidden_main_group_name").value;
			$('#txt_main_group_id').val(main_group_id);
			$('#txt_main_group').val(main_group_name);
		}
	}
	
	function fnc_copy_zipper(type)
	{
		if(type==1)
		{
			if(document.getElementById('chk_zipper').checked==true)
			{
				document.getElementById('chk_zipper').value=1;
			}
			else if(document.getElementById('chk_zipper').checked==false)
			{
				document.getElementById('chk_zipper').value=2;
			}
		}
	}

</script>
</head>
<body onLoad="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission);  ?>
<div align="center">
	<form name="excelImport_1" id="excelImport_1" action="item_group_import_excel.php" enctype="multipart/form-data" method="post">
    	<table cellpadding="0" cellspacing="2" width="850" style="padding-left: 5px; padding-right: 5px;">
    		<tr>
    			<td width="200" align="left"><input type="file" id="uploadfile" name="uploadfile" class="image_uploader" required style="width:200px" /></td>
    			<td width="200" align="left"><input type="submit" name="submit" value="Excel File Upload" class="formbutton" style="width:110px" /></td>                
             	<td width="540" align="right"><a href="../../excel_format/item_group_up_requirement.xls"><input type="button" value="Excel Format Download" name="excel" id="excel" class="formbutton" style="width:150px"/></a></td>
            </tr>
    	</table>
    </form>
	<fieldset style="width:960px;">
		<legend>Item Group Info</legend>
		<form name="itemgroup_1" id="itemgroup_1">	
			<table cellpadding="0" cellspacing="2" >
				<tr>
					<td colspan="2" valign="top">
						<table  cellpadding="0" cellspacing="2" width="100%">
			 				<tr>
								<td width="150" class="must_entry_caption">Item Category</td>
								<td width="170"><? echo create_drop_down( "cbo_item_category", 155,$item_category,"", '1', '---- Select ----', 0, "trim_fancy_disable(this.value);","","$item_cat_cond","","","1,2,3,12,13,14,24,25,28,30,31,42,43,71,72,73,74,75,76,77,78,79,80,81,82,83,86,95,96,98,100,102,103,104,105,108,109,112"); ?></td>
								<!-- item_category_add(this.value,7); -->
								<td width="150" id="txt_group_code_td">Group Code</td>
								<td id="group_code" width="170"><Input name="txt_group_code" ID="txt_group_code"   style="width:145px" value="" class="text_boxes" autocomplete="off" maxlength="25" title="Maximum 25 Character"></td>
								<td width="150" class="must_entry_caption">Item Group</td>
								<td><Input name="txt_item_name" ID="txt_item_name"   style="width:137px" value="" class="text_boxes" autocomplete="off" maxlength="50" title="Maximum 50 Character">
								</td>								
							</tr>	 
							<tr>
								<td>Trims Type</td>
								<td><? echo create_drop_down( "cbo_trim_type", 155, $trim_type,"", "1", "---- Select ----", 0, "" ); ?></td>
								<td	class="must_entry_caption">Order UOM</td>
								<td><? echo create_drop_down( "cbo_order_uom", 70, $unit_of_measurement,"", "", "", 1, "set_con_factor_value()","" ); ?>&nbsp;<span style="color:red">Higher UOM</span></td>
								<td class="must_entry_caption">Cons UOM</td>
								<td><? echo create_drop_down( "cbo_cons_uom", 70, $unit_of_measurement,"", "", "", 1, "set_con_factor_value()","" );?>&nbsp;<span style="color:red">Lower UOM</span></td>
							</tr> 
							<tr id="fancy_item_tr_id" >
								<td>Conv. Factor</td>
								<td><input name="txt_conversion_factor" id="txt_conversion_factor"   style="width:143px" class="text_boxes_numeric" value="1"></td>
								<td class="must_entry_caption">Fancy Item</td>
								<td><? echo create_drop_down( "cbo_fancy_item", 155, $yes_no,"", "", "", 2, "" ); ?> </td>
								<td>Cal Parameter</td>
								<td><? echo create_drop_down( "cbo_cal_parameter", 150, $cal_parameter,"", 1, "--Select--", 0, "","","","","","14" ); ?></td>
							</tr>
							<tr>
                            	<td>Rate Cal Parameter</td>
								<td><? echo create_drop_down( "cbo_ratecal_parameter", 154, $cal_parameter,"", 1, "--Select--", 0, "","","2,4,14" ); ?></td>
                                <td>Main Group</td>
                                <td>
                                	<input type="text" name="txt_main_group" id="txt_main_group" class="text_boxes" style="width:145px;" placeholder="Double Click To Search" onDblClick="openmypage_main_group();" readonly />
                        			<input type="hidden" name="txt_main_group_id" id="txt_main_group_id" value="" />
                        		</td>								
								<td>HS Code</td>
								<td>
									<input type="text" name="txt_hs_code" id="txt_hs_code" class="text_boxes" style="width:145px;" />	
									<input type="hidden" name="update_id" id="update_id" >
									<input type="hidden" name="hide_group_code" id="hide_group_code">	
								</td>					
							</tr>
							<tr>						
								<td>Ord UOM Dec Place Qnt</td>
								<td><? echo create_drop_down( "cbo_ordUOMDecPlaceQnt", 155, $dec_place_other_item, "", 1, 0, 0, "" ); ?></td>
                                <td>Ord UOM Dec Place Rate</td>
								<td><? echo create_drop_down( "cbo_ordUOMDecPlaceRate", 155, $dec_place_other_item, "", 1, 0, 0, "" ); ?></td>
								<td>Ord UOM Dec Place Amt</td>
								<td><? echo create_drop_down( "cbo_ordUOMDecPlaceAmt", 155, $dec_place_other_item, "", 1, 0, 0, "" ); ?></td>
							</tr>
							<tr>						
								<td>Cons UOM Dec Place Qnt</td>
								<td><? echo create_drop_down( "cbo_consUOMDecPlaceQnt", 155, $dec_place_other_item, "", 1, 0, 0, "" ); ?></td>
                                <td>Cons UOM Dec Place Rate</td>
								<td><? echo create_drop_down( "cbo_consUOMDecPlaceRate", 155, $dec_place_other_item, "", 1, 0, 0, "" ); ?></td>
								<td>Cons UOM Dec Place Amt</td>
								<td><? echo create_drop_down( "cbo_consUOMDecPlaceAmt", 155, $dec_place_other_item, "", 1, 0, 0, "" ); ?></td>
								
							</tr>
							<tr>
								<td>Section</td>
								<td>
									<? echo create_drop_down( "cbo_section", 155, $trims_section,"", 1, "-- Select Section --",0,""); ?>
								</td>
                                <td>Status</td>
								<td><? echo create_drop_down( "cbo_status", 155, $row_status,"", "", "", 1, "" ); ?></td>
                                <td>Is Zipper for W/O &nbsp; &nbsp; <input type="checkbox" name="chk_zipper" id="chk_zipper" onClick="fnc_copy_zipper(1);" value="2" ></td>
								<td><input type="button" id="rate_pop_up" class="image_uploader" style="width:142px;" value="Rate Pop Up" onClick="open_rate_popup(document.getElementById('update_id').value)" /> </td>
							</tr>
			    		</table>
					</td>
			  	</tr>
				<tr>
				  <td colspan="4" align="center" class="button_container">
						<? 
						$dd="disable_enable_fields( 'txt_group_code',0)";
						echo load_submit_buttons( $permission, "fnc_item_group", 0,0 ,"reset_form('itemgroup_1','','','cbo_fancy_item,2*txt_conversion_factor,1',$dd)",1);
						?>	
					</td>				
				</tr>
			</table>
		</form>	
	</fieldset>	
	<div style="width:100%; float:left; margin:auto" align="center">
		<fieldset style="width:950px; margin-top:20px">
			<legend>List View </legend>
            <div style="text-align:center;"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --","","","","2,3,4" ); ?></div>
			<div style="width:950px; margin-top:10px" id="item_group_list_view" align="left">
			<?
				$arr=array (0=>$item_category,3=>$trim_type,4=>$unit_of_measurement,5=>$unit_of_measurement,7=>$dec_place_other_item,8=>$dec_place_other_item,9=>$cal_parameter,10=>$row_status);
				echo  create_list_view ( "list_view", "Item Catagory,Item Group Code,Item Group Name,Item Type,Order UOM,Cons. UOM,Conv. Factor,Order UOM Dec. Point,Cons UOM Dec. Point,Cal Parameter,Status", "120,100,150,80,50,50,50,60,60,80","940","220",0, "SELECT id,item_category,item_group_code,item_name,trim_type,order_uom,trim_uom,conversion_factor,order_uom_decimal_point,cons_uom_decimal_point,cal_parameter,status_active from lib_item_group where is_deleted=0 $item_cat_id_cond", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "item_category,0,0,trim_type,order_uom,trim_uom,0,order_uom_decimal_point,cons_uom_decimal_point,cal_parameter,status_active", $arr , "item_category,item_group_code,item_name,trim_type,order_uom,trim_uom,conversion_factor,order_uom_decimal_point,cons_uom_decimal_point,cal_parameter,status_active", "../item_details/requires/item_group_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,2,1,1' ) ;
			?>
			</div>
		</fieldset>	
	</div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

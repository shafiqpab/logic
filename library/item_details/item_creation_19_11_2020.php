<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Item Creation
				
Functionality	:	
JS Functions	:
Created by		:	CTO/sohel 
Creation date 	: 	08-04-2013
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
/*print_r($_SESSION['logic_erp']['mandatory_field'][420]);die;*/
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------

echo load_html_head_contents("Item Creation Info", "../../", 1, 1,$unicode,'','');
?> 
<script>
function form_validation_item(control,msg_text)
{

  control=control.split("*");
  msg_text=msg_text.split("*");
  var bgcolor='-moz-linear-gradient(bottom, rgb(254,151,174) 0%, rgb(255,255,255) 10%, rgb(254,151,174) 96%)';
  var new_elem="";
  for (var i=0; i<control.length; i++)
  {
	  	var type = document.getElementById(control[i]).type;
		var tag = document.getElementById(control[i]).tagName;
		document.getElementById(control[i]).style.backgroundImage="";
		var cls=$('#'+control[i]).attr('class');

		if( cls=="text_boxes_numeric" ) //if ( type == 'text' || type == 'password' || type == 'textarea' )
		{
			if (trim(document.getElementById(control[i]).value)=="")
			{
				 document.getElementById(control[i]).focus();
				 document.getElementById(control[i]).style.backgroundImage=bgcolor;
				 $('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
				 {
					$(this).html('Please Fill up '+msg_text[i]+' field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
				 });
				 return 0;
			}
		}

		if ( type == 'text' || type == 'password' || type == 'textarea' )
		{
			if (trim(document.getElementById(control[i]).value)=="")
			{
				 document.getElementById(control[i]).focus();
				 document.getElementById(control[i]).style.backgroundImage=bgcolor;
				 $('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
				 {
					$(this).html('Please Fill up '+msg_text[i]+' field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
				 });
				 return 0;
			}
		}
		else if (type == 'select-one' || type=='select' )
		{
			//alert(control[i]);
			 if ( trim(document.getElementById(control[i]).value)==0)
			 {
				 document.getElementById(control[i]).focus();
				 document.getElementById(control[i]).style.backgroundImage=bgcolor;
				 $('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
				 {
					$(this).html('Please Select  '+msg_text[i]+' field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);

				 });
				 return 0;
			 }
		}
		else if (type == 'checkbox' || type == 'radio')
		{
			 document.getElementById(control[i]).style.backgroundImage=bgcolor;
			 if (new_elem=="") new_elem=control[i]; else new_elem=new_elem+","+control[i];
		}
		else if (type == 'hidden' )
		{
			if(trim(document.getElementById(control[i]).value)=='')
			{
				if(msg_text[i]!='')
				{
					$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
					 {
						$(this).html('Please Fill up or Select '+msg_text[i]+' field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);

					 });
					 return 0;
				}
				else
				{
					$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
					 {
						$(this).html('Please fill up master field Value').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);

					 });
					 return 0;

				}

			}
		}

  }
  return 1;

}

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';
	
	//var company_id=document.getElementById('cbo_company_name').value;
	//var item_category_id=document.getElementById('cbo_item_category').value;
	//var item_group_id=document.getElementById('item_group_id').value;
	var str_group = [<? echo substr(return_library_autocomplete( "select sub_group_code from product_details_master  group by sub_group_code", "sub_group_code" ), 0, -1); ?>];

	$(document).ready(function(e){
            $("#txt_subgroup_code").autocomplete({
			 source: str_group
		  });
     });

	var str_group_name = [<? echo substr(return_library_autocomplete( "select sub_group_name from product_details_master  group by sub_group_name", "sub_group_name" ), 0, -1); ?>];

	$(document).ready(function(e){
            $("#txt_item_code").autocomplete({
			 source: str_group_name
		  });
     });


	var str_item_code = [<? echo substr(return_library_autocomplete( "select item_code from product_details_master  group by item_code", "item_code" ), 0, -1); ?>];

	$(document).ready(function(e){
            $("#txt_subgroup_name").autocomplete({
			 source: str_item_code
		  });
     });

  /* var str_description = [<? //echo substr(return_library_autocomplete( "select item_description from product_details_master  group by item_description", "item_description" ), 0, -1); ?>];

	$(document).ready(function(e){
            $("#txt_description").autocomplete({
			 source: str_description
		  });
     });*/


     var str_item_size = [<? echo substr(return_library_autocomplete( "select item_size from product_details_master  group by item_size", "item_size" ), 0, -1); ?>];

	$(document).ready(function(e){
            $("#txt_item_size").autocomplete({
			 source: str_item_size
		  });
     });

	function openmypage()
	{
		if ( form_validation('cbo_company_name*cbo_item_category','Company Name*Item Category')==false )
		{
			return;
		}
		else
		{
			var category=document.getElementById('cbo_item_category').value;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/item_creation_controller.php?category='+category+'&action=order_popup','Search Group Code', 'width=950px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemail=this.contentDoc.getElementById("item_id");
				document.getElementById('item_group_id').value = theemail.value;
				get_php_form_data(theemail.value, "load_php_popup_to_form", "requires/item_creation_controller" );
				set_button_status(0, permission, 'fnc_item_creation',1);
				fn_list_show($('#cbo_item_category').val(),$('#cbo_company_name').val(),$('#item_group_id').val())
				release_freezing();
			}
		}
	}
			
	function fnc_item_creation( operation )
	{
		/*if(operation==2)
		{
			show_msg('13');
			return;
		}*/
		
		if(operation==2)
	  	{
	  	  var update_id=$('#update_id').val();
		 
	  	  var product_id=return_global_ajax_value(update_id,'product_id_check','', 'requires/item_creation_controller');
	  	  
	  	//alert(product_id);return;
	  	  if(trim(product_id)!='')
	  	  {
	  		alert('Data Can not be Deleted .Data has already been used.'+product_id); 
	  	 	return;
	  	  }
	  	 }
		
		if (form_validation('cbo_company_name*cbo_item_category*txt_item_group','Company*Item Catagory*Grop Code')==false)
		{
			return;
		}

		if('<?php echo implode('*',$_SESSION['logic_erp']['mandatory_field'][420]);?>'){
			if (form_validation_item('<?php echo implode('*',$_SESSION['logic_erp']['mandatory_field'][420]);?>','<?php echo implode('*',$_SESSION['logic_erp']['field_message'][420]);?>')==false)
			{
				
				return;
			}
		}
		
		/*var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_item_category*txt_item_group*item_group_id*txt_subgroup_code*txt_subgroup_name*txt_item_code*txt_description*txt_item_size*txt_reorder_label*txt_min_label*txt_max_label*cbo_cons_uom*txt_item_account*cbo_status*txt_brand*cbo_origin*update_id*update_status_active',"../../");*/
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_item_category*txt_item_group*item_group_id*txt_subgroup_code*txt_subgroup_name*txt_item_code*txt_description*txt_item_size*txt_reorder_label*txt_min_label*txt_max_label*cbo_cons_uom*txt_item_account*cbo_status*txt_brand*cbo_origin*update_id*txt_model_name*update_status_active*cbo_fixed_asset*txt_item_no',"../../");
		
		freeze_window(operation);
		http.open("POST","requires/item_creation_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_item_creation_reponse;
	}

	function fnc_item_creation_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			
			/*if(reponse[0]==50)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			if(reponse[0]==101 || reponse[0]==102)
			{
				alert(" You can not cancell or InActive This Item.This Information is used in another Table");
				document.getElementById('cbo_status').value =1;
				release_freezing();
			}*/
			
			if(reponse[0]==11)
			{
				alert(reponse[1]); release_freezing(); return;
			}
			else
			{
				document.getElementById('update_id').value =reponse[1];
				var item_category=$('#cbo_item_category').val();
				var Company=$('#cbo_company_name').val();
				var item_group=$('#txt_item_group').val();
				
				var item_group_id=$('#item_group_id').val();
				//var item=0;
				//var item_group=0;
				show_msg(reponse[0]);
				//show_list_view(reponse[2],'item_creation_list_view','item_creation_list_view','../item_details/requires/item_creation_controller','setFilterGrid("list_view",-1)');
				
				show_list_view(item_category+'**'+Company+'**'+item_group_id+'**'+item_group,'item_creation_list_view','item_creation_list_view','requires/item_creation_controller','setFilterGrid("list_view",-1)');
				//reset_form('itemcreation_1','','');
				/*reset_form('','','txt_subgroup_code*txt_subgroup_name*txt_item_code*txt_description*txt_item_size*txt_reorder_label*txt_min_label*txt_max_label*txt_item_account*cbo_status*txt_brand*cbo_origin','','');*/
				reset_form('','','txt_subgroup_code*txt_subgroup_name*txt_item_code*txt_description*txt_item_size*txt_reorder_label*txt_min_label*txt_max_label*txt_item_account*cbo_status*txt_brand*txt_model_name*cbo_origin','','');
				$('#txt_subgroup_name').focus();
				//$('#'+txt_subgroup_name).focus();
				disable_enable_fields('cbo_company_name*cbo_item_category*txt_item_group*txt_subgroup_code*txt_item_code*txt_item_account');
				set_button_status(0, permission, 'fnc_item_creation',1);
				release_freezing();
			}
		}
	}

	function fnc_set_namecode(val)
	{
		var item_category=document.getElementById('cbo_item_category').value;
		var item_group=document.getElementById('txt_item_group').value.split('-');
		var subgroup_code=document.getElementById('txt_subgroup_code').value;
		var item_code=item_category+'-'+item_group[0]+'-'+subgroup_code+'-'+val;
		document.getElementById('txt_item_account').value = item_code;
	}
	
	function fnc_item_category_add(category,type)
	{
		get_php_form_data(category,type,'requires/item_creation_controller');
	}
	
	
	function check_save_data(size_val)
	{
		var list_view_orders=return_global_ajax_value($('#cbo_company_name').val()+"**"+$('#cbo_item_category').val()+"**"+$('#item_group_id').val()+"**"+$('#txt_subgroup_code').val()+"**"+$('#txt_subgroup_name').val()+"**"+$('#txt_item_code').val()+"**"+$('#txt_description').val()+"**"+size_val, "load_check_value_to_form",'', "requires/item_creation_controller" );
		if(list_view_orders==12)
		{
			alert ("Duplicate Value Found.");
			return;
		}
/*		else if(list_view_orders==12)
		{
			//alert ("Duplicate Value Found.");
			//return;
		}*/
	}

	function fn_list_show(item_id,Company,item_group)
	{	if(item_id>0 && Company>0){
		show_list_view(item_id+'**'+Company+'**'+item_group,'item_creation_list_view','item_creation_list_view','requires/item_creation_controller','setFilterGrid("list_view",-1)');
		}
	}

	function fnc_test_parameter()
	{
		var update_id=$('#update_id').val();
		var group_id=$('#item_group_id').val();
		var description=$('#txt_description').val();
		var item_category=$('#cbo_item_category').val();
		var company_name=$('#cbo_company_name').val();
		if(update_id=='')
		{
			alert('Please Save item'); return;
		}
		else
		{
			var data=update_id+"_"+group_id+"_"+description+"_"+item_category+"_"+company_name;
			//alert(data); 
			var title = 'Test Parameter';
			var page_link = 'requires/item_creation_controller.php?data='+data+'&action=test_parameter_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=370px,center=1,resize=1,scrolling=0','../');
			/*emailwindow.onclose=function()
			{
				var qnty_tot=this.contentDoc.getElementById("hidden_qnty_tot").value;
				$('#txt_reject_qty').val(qnty_tot);
			}*/
		}
	}
	
</script>
</head>
<body onLoad="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission); ?>
<div align="center">
	<fieldset style="width:900px;">
		<legend>Item Creation Info</legend>
		<form name="itemcreation_1" id="itemcreation_1">	
        <p id="demo_message"></p>
		<table cellpadding="3" cellspacing="3">
            <tr>
                <td width="100" class="must_entry_caption" align="right">Company</td>
                <td width="170"> <input type="hidden" name="txt_hidden_item_group" id="txt_hidden_item_group" value="" ><input type="hidden" name="update_id" id="update_id" > <input type="hidden" name="update_status_active" id="update_status_active" > 
					<? 
						echo create_drop_down( "cbo_company_name", 155, "select company_name,id from lib_company comp where is_deleted=0  and status_active=1 $company_cond  order by company_name",'id,company_name', 1, '--- Select Company ---', 0, "fn_list_show($('#cbo_item_category').val(),this.value,'0');" );
                    ?> 
                </td>
                <td width="120" class="must_entry_caption" align="right">Item Category</td>
                <td width="170"><input type="hidden" name="set_id" id="set_id" >                                 
					<?
						echo create_drop_down( "cbo_item_category", 155, $item_category,"", "1", "--- Select---", 0, "fn_list_show(this.value,$('#cbo_company_name').val(),'0');fnc_item_category_add(this.value,'category_add');","","","","","1,2,3,12,13,14,24,25" );
						//fnc_item_category_add(this.value,category_add);
                    ?>
                </td>
                <td width="120" class="must_entry_caption" align="right">Item Group</td>
                <td><input type="hidden" id="item_group_id" />
                    <Input name="txt_item_group" id="txt_item_group"   style="width:145px" value="" class="text_boxes" autocomplete="off" maxlength="50" title="Maximum 50" placeholder="Double Click to Search" onKeyUp="if (this.value!='') get_php_form_data(this.value+'_blur'+document.getElementById('cbo_item_category').value, 'load_php_popup_to_form', 'requires/item_creation_controller')" onDblClick="openmypage()"  readonly />
                </td>
            </tr>
            <tr>
                <td align="right">Sub Group Code</td>
                <td><Input name="txt_subgroup_code" id="txt_subgroup_code" style="width:145px" class="text_boxes" autocomplete="off"  maxlength="50" title="Maximum 50 Character" ></td>
                <td align="right">Sub Group Name</td>
                <td><Input name="txt_subgroup_name" id="txt_subgroup_name"  style="width:145px" class="text_boxes" autocomplete="off"  maxlength="50" title="Maximum 50 Character"></td>
                <td align="right">Item Code</td>
                <td><Input name="txt_item_code" id="txt_item_code"  onBlur="fnc_set_namecode(this.value)" style="width:145px"  class="text_boxes" autocomplete="off" /></td>
            </tr> 
            <tr>
                <td align="right">Item Description</td>
                <td><Input name="txt_description" id="txt_description"  style="width:145px"  class="text_boxes" autocomplete="off"  maxlength="250" title="Maximum 250 Character" onKeyUp="this.value = this.value.replace(/[`~#$^&*()_|\(\)\\]/gi, '')"></td> 
                <td align="right">Item Size</td>
                <td><Input name="txt_item_size" id="txt_item_size"  style="width:145px" class="text_boxes" autocomplete="off" onBlur="check_save_data(this.value);"></td>
                <td align="right">Re-Order Level</td>
                <td><Input name="txt_reorder_label" id="txt_reorder_label"  style="width:145px" class="text_boxes_numeric" autocomplete="off"/></td>
            </tr>
            <tr>
                <td align="right">Min Level</td>
                <td><Input name="txt_min_label" id="txt_min_label"  style="width:145px" class="text_boxes_numeric" autocomplete="off" /></td>
                <td align="right">Max Level</td>
                <td><Input name="txt_max_label" id="txt_max_label"  style="width:145px" class="text_boxes_numeric" autocomplete="off" /></td>
                <td align="right">Order UOM </td>
                <td>
					<?
						echo create_drop_down( "cbo_order_uom", 155, $unit_of_measurement,"", "1", "--- Select---", 0, "","1" );
                    ?>
                </td>
            </tr>
            <tr>
                <td align="right">Cons UOM</td>
                <td id="cons_td"> 
					<?
						echo create_drop_down( "cbo_cons_uom", 155, $unit_of_measurement,"", "1", "--- Select---", 0, "","1" );
                    ?>
                </td>
                <td align="right">Item Account</td>
                <td>
                    <Input name="txt_item_account" id="txt_item_account" style="width:145px" class="text_boxes" readonly   maxlength="50" title="Maximum 50 Character">                </td>
                <td align="right">Status </td>
                <td>
					<?
						echo create_drop_down( "cbo_status", 155, $row_status,"", "", "", 1, "" );
                    ?>
                </td>		
            </tr>
            <tr>
                <td align="right">Brand</td>
                <td><Input name="txt_brand" id="txt_brand"  style="width:145px" class="text_boxes" autocomplete="off" /></td>
                <td align="right">Origin</td>
                <td>
				<?
                echo create_drop_down( "cbo_origin", 155, "select country_name,id from lib_country comp where is_deleted=0  and status_active=1 order by country_name",'id,country_name', 1, '--- Select Country ---', 0 );            
                ?>
                </td>
                <td align="right">Model</td>
                <td><input type="text" name="txt_model_name" id="txt_model_name"  style="width:145px" class="text_boxes" autocomplete="off"  maxlength="50" title="Maximum 50 Character"></td>
                
            </tr>
            <tr>
                <td align="right">Fixed Asset</td>
                <td>
                <?
                	echo create_drop_down( "cbo_fixed_asset", 155, $yes_no,'', 0, '--Select--', 2 );            
                ?>
                </td>
                <td align="right">&nbsp;</td>
                <td colspan="2">
					<input type="button" id="image_button" class="image_uploader" style="width:155px" value="Test Parameter" onClick="fnc_test_parameter()" />
				</td>
                 <td>
					<input type="button" id="image_button" class="image_uploader" style="width:155px" value="Add Image" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'item_creation', 0 ,1,0,0)" />
				</td>					
            </tr>
            <tr>
                <td align="right">Item Number</td>
                <td>
               <input type="text" name="txt_item_no" id="txt_item_no"  style="width:145px" class="text_boxes" autocomplete="off"  maxlength="50" title="Maximum 50 Character">
                </td>
                <td align="right">&nbsp;</td>
                <td colspan="3">
					
				</td>					
            </tr>
            <tr>
                <td colspan="6" align="center">&nbsp;
                    <input type="hidden" name="hide_item_code" id="hide_item_code">
                </td>					
            </tr>
		</table>
        <table>
            <tr>
                <td width="670"></td>
                <td width="157"></td>
            </tr>
            <tr><td colspan="6"></td></tr>
            <tr>
                <td colspan="4" align="center" class="button_container">
					<? 
                        $dd="disable_enable_fields('cbo_company_name*cbo_item_category*txt_item_group*txt_subgroup_code*txt_item_code*txt_item_account',0)";
                        echo load_submit_buttons( $permission, "fnc_item_creation", 0,0 ,"reset_form('itemcreation_1','','','',$dd)",1);//itemcreation_1
                    ?>	
                </td>				
            </tr>
        </table>
		</form>	
	</fieldset>	
    <fieldset style="width:1280px;">
        <legend>Item Creation List View</legend>
            <div style="width:1280px;"id="item_creation_list_view"></div>
    </fieldset>	
</div>
</body>
<script>
	/*$(document).ready(function() {
		setFilterGrid("list_view",-1);
		//show_list_view('','item_creation_list_view','item_creation_list_view','requires/item_creation_controller','setFilterGrid("list_view",-1)');
	});*/
	
	function handlePaste (e) 
	{
		var clipboardData, pastedData;
		// Stop data actually being pasted into div
		e.stopPropagation();
    	e.preventDefault();
		// Get pasted data via clipboard API
    	clipboardData = e.clipboardData || window.clipboardData;
    	pastedData = clipboardData.getData('Text');
    	pastedData = pastedData.replace(/[`~#$^&*()_|\(\)\\]/gi, '');    
    	// pastedData = pastedData.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');    
    	// Do whatever with pasteddata
    	// alert(pastedData);
    	document.getElementById('txt_description').value=pastedData;
	}

	document.getElementById('txt_description').addEventListener('paste', handlePaste);
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

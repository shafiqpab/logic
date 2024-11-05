<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Garment Item
Functionality	:	 
JS Functions	:
Created by		:	Rehan Uddin 
Creation date 	: 03-12-2016
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

//print_r( get_garments_item_array(2) );
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Garment Item","../../", 1, 1,$unicode,'','');
 ?>	

<script language="javascript">
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission='<? echo $permission; ?>';

	function fnc_garment_item( operation )
	{ 
		freeze_window(operation);
		if(form_validation('txt_product_nature*txt_item_name*cbo_product_category*cbo_product_type','Product Nature*Item Name*Product Category*Product Type')==false)
		{
			release_freezing();
			return;
		}
		
		var default_value_id=$("#default_value_id").val();
		if(default_value_id==1 && operation==2)
		{
			alert("Default Value can not be deleted!!!");
			release_freezing();
			return;
		}
		else
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_item_name*txt_commercial_name*cbo_product_category*cbo_product_type*txt_standard_smv*txt_efficiency*cbo_status*update_id*default_value_id*txt_hs_code*txt_notes*hidd_product_nature_id*txt_product_code',"../../");
			
			http.open("POST","requires/garment_item_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_garment_item_reponse;
		}
	}

	function fnc_garment_item_reponse()
	{
		if(http.readyState==4)
		{
			var reponse=trim(http.responseText).split('**');
			
			if(reponse[0]==50)
			{
				alert('Some Entries Found For This Item, Deleting Not Allowed.');
				release_freezing();
				return;
			}
			if (reponse[0].length>2) reponse[0]=10;
			
			show_msg(reponse[0]);
			if(reponse[0]!=10)
			{
				show_list_view('','show_list_view_item','garment_item_list_view','requires/garment_item_controller','setFilterGrid("list_view",-1)');
				reset_form('garmentitem1','','');
				set_button_status(0, permission, 'fnc_garment_item',1);
			}
			release_freezing();
		} 
	}
		
	function open_notes_popup()
	{
		var title='Operation Templete';
		var hdn_notes=$('#hdn_notes').val();
		var page_link='requires/garment_item_controller.php?action=notes_popup&hdn_notes='+hdn_notes;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=370px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var notes_dtls_data=this.contentDoc.getElementById("notes_dtls_data").value;
			$('#txt_notes').val(notes_dtls_data);
			$('#hdn_notes').val(notes_dtls_data);
		};
	}

	function fn_reset_form()
	{
		set_button_status(0, permission, 'fnc_garment_item',1);
		reset_form('garmentitem1','','');
	}
	
	function fnc_product_nature()
	{
		var product_nature_id = $('#hidd_product_nature_id').val();
		var title = 'Product Nature Selection Form';	
		var page_link = 'requires/garment_item_controller.php?product_nature_id='+product_nature_id+'&action=product_nature_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=350px,height=350px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var party_id=this.contentDoc.getElementById("hidd_product_nature_id").value;	 //Access form field with id="emailfield"
			var party_name=this.contentDoc.getElementById("txt_product_nature").value;
			$('#hidd_product_nature_id').val(party_id);
			$('#txt_product_nature').val(party_name);
		}
	}

</script>

</head>
    <body onLoad="set_hotkey()"> 
<div align="center" style="width:100%">
   <? echo load_freeze_divs ("../../",$permission);  ?>
   <fieldset style="width:800px">
       <legend>Garment Item </legend>
       <form name="garmentitem1" id="garmentitem1" autocomplete="off">
            <table cellpadding="0" cellspacing="2" width="100%">
                <tr>
                	<td width="100" class="must_entry_caption">Product Nature</td>
                    <td width="150"><input type="text" id="txt_product_nature" name="txt_product_nature" maxlength="50" class="text_boxes" style="width:120px;" readonly placeholder="Browse" onDblClick="fnc_product_nature();" /> <input type="hidden" name="hidd_product_nature_id" id="hidd_product_nature_id"></td>
                    <td width="100" class="must_entry_caption">Item Name </td>
                    <td width="150"><input type="text" id="txt_item_name" name="txt_item_name" maxlength="50" class="text_boxes" style="width:120px;" /> </td>
                    <td width="100">Gmts Prod Code</td>
                    <td><input type="text" id="txt_product_code" name="txt_product_code" maxlength="50" class="text_boxes" style="width:120px;" /> </td>
                </tr>
                <tr>
                	<td width="100" class="must_entry_caption">Product Category </td>
                    <td><?=create_drop_down( "cbo_product_category", 130, $product_category,'', 1,"--- Select Category ---",0,"load_drop_down( 'requires/garment_item_controller', this.value, 'load_drop_down_product_type', 'product_type' );"); ?> </td>
                    <td class="must_entry_caption" id="">Product Type </td>
                    <td id="product_type"><?=create_drop_down( "cbo_product_type", 130, $blank_array,'', 1,"--- Select Product Type ---",0); ?></td>
					<td width="100">Commercial Name </td>
                    <td><input type="text" id="txt_commercial_name" name="txt_commercial_name" maxlength="50" class="text_boxes" style="width:120px;" /> </td>
                </tr>
                <tr>
                	<td>Efficiency% </td>
                    <td><input type="text" id="txt_efficiency" name="txt_efficiency" maxlength="20" class="text_boxes_numeric" style="width:120px;" /></td>
                    <td>HS Code</td>
                    <td><input type="text" id="txt_hs_code" name="txt_hs_code" maxlength="20" class="text_boxes" style="width:120px;" /></td>
					<td>Standard SMV </td>
                    <td><input type="text" id="txt_standard_smv" name="txt_standard_smv" maxlength="20" class="text_boxes_numeric" style="width:120px;" /></td>
                    
                </tr>
                <tr>
                	<td>Operation Templete</td>
                    <td colspan="2">
                        <input type="text" id="txt_notes" name="txt_notes" class="text_boxes" style="width:220px;" onClick="open_notes_popup();" readonly placeholder="Browse" />
                        <input type="hidden" id="hdn_notes" class="text_boxes" style="width:100px;" />
                    </td>
                    <td>&nbsp;</td>
                    <td>Status</td>                          
                    <td><?=create_drop_down("cbo_status",130,$row_status,'','',''); ?>
                        <input type="hidden" name="update_id" id="update_id">
                        <input type="hidden" name="default_value_id" id="default_value_id">
                    </td>
                </tr>
                <tr>
                    <td colspan="6" align="center" class="button_container">
						<?=load_submit_buttons( $permission, "fnc_garment_item", 0,0 ,"fn_reset_form()",1); ?>
                    </td>
                </tr>
                <tr>
                	<td colspan="6" height="15">&nbsp;</td>
                </tr>
                <tr>
                    <td align="center" colspan="6" id="garment_item_list_view"> 
						<?
                         $arr=array (3=>$product_category,4=>$product_types,7=>$row_status);
						 echo create_list_view ( "list_view", "Item Name,Gmts Prod Code,Commercial Name,Product Category,Product Type,SMV,Efficiency,Status,HS Code","200,100,130,90,80,70,70,90,100","970","220",0, "select id,item_name,product_code,commercial_name,product_category_id,product_type_id,status_active,standard_smv,efficiency,hs_code from lib_garment_item where is_deleted=0 and status_active=1", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,0,product_category_id,product_type_id,0,0,status_active,0", $arr , "item_name,product_code,commercial_name,product_category_id,product_type_id,standard_smv,efficiency,status_active,hs_code", "../merchandising_details/requires/garment_item_controller", 'setFilterGrid("list_view",-1);') ;
                        ?>                                             
                    </td>
                </tr>
            </table>
          </form>
      </fieldset>
	</div>
	<script src="../../includes/functions_bottom.js" type="text/javascript"> </script>
    </body>
</html>
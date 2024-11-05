<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
// echo load_html_head_contents("Item Details Entry", "../../", 1, 1,$unicode,'','');
echo load_html_head_contents("Item Details Entry","../../",1 ,1 ,$unicode,1,'','' );
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';

function search_rate()
{
	if (form_validation('search_item_category*search_item_group*show_textsearch_item_description','Item Category*Item Group*Item Description')==false)
	{
		return;
	}

	var data="action=get_search_rate"+get_submitted_data_string('search_item_category*search_item_group*search_item_description',"../../");
	// alert(data);
	freeze_window(operation);
	http.open("POST","requires/supplier_wise_rate_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = supplier_rate_response;


}
	function supplier_rate_response()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText);
			$('#item_data').html(reponse);
		}

			release_freezing();
	}
	function fnc_item_details( operation )
	{

		if (form_validation('cbo_item_category*cbo_item_group*cbo_order_uom*cbo_cons_uom*txt_item_description','Item Category*Item Group*Order UOM*Cons UOM*Item Discripttion')==false)
		{
			return;
		}else {
			
      		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_item_category*cbo_item_group*txt_item_description*cbo_order_uom*cbo_cons_uom*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/supplier_wise_rate_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_item_details_reponse;
    }

}
      function fnc_item_details_reponse()
      {
        if(http.readyState == 4)
        {
          var reponse=trim(http.responseText).split('**');

          show_msg(reponse[0]);
        }
          //show_list_view(reponse[1],'item_group_list_view','item_group_list_view','../item_details/requires/item_group_controller','setFilterGrid("list_view",-1)');
          reset_form('item_details_mst','','');
          set_button_status(0, permission, 'fnc_item_details',1);
          release_freezing();
        }

function getItemDescription(groupId){
	load_drop_down('requires/supplier_wise_rate_controller',groupId+','+document.getElementById('search_item_category').value, 'load_drop_down_item_description', 'td_serach_item_description');
	set_multiselect('search_item_description','0','0','','');
}
</script>

</head>
<body onLoad="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission);  ?>


<div style="width:980px; min-height:200px;">
	<div style="width:480px;float:left">
		<div style="width:400px;float:left">
			<fieldset>
				<legend>Item Details Entry</legend>
				<form name="item_details_mst" id="item_details_mst">
					<table cellpadding="0" cellspacing="2" >
						<tr>
							<td colspan="2" valign="top">
								<table  cellpadding="0" cellspacing="2" width="100%">
					 				<tr>
										<td width="50" class="must_entry_caption">Item Category</td>

										<td>
		                  <input type="hidden" name="update_id" id="update_id" />
		                    <?
											//function create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name )
											echo create_drop_down( "cbo_item_category", 155,$item_category,"", '1', '---- Select ----', 0, "load_drop_down( 'requires/supplier_wise_rate_controller',this.value, 'load_drop_down_item_group', 'td_item_group')" );
								        ?>
						   				</td>
		                </tr>
		                <tr>
		                  <td width="50" class="must_entry_caption">Item Group</td>

		                  <td id="td_item_group" width="155">
		                                  <?
		                    echo create_drop_down( "cbo_item_group", 155,$blank_array, '', 1, '---- Select ----'  );
		                      ?>
		                    </td>
		                  </tr>

		                  <tr>
		                    <td width="50" class="must_entry_caption">Item Description</td>

		    								<td width="155"> <!-- Calander-->
		                                        <Input name="txt_item_description" ID="txt_item_description"   style="width:145px" value="" class="text_boxes" autocomplete="off" maxlength="50" title="Maximum 50 Character">
		    								</td>
		                    </tr>
		                  <tr>
		                    <td width="50"	class="must_entry_caption">Item Unit:</td>

										    <td 	width="155" class="must_entry_caption">Order UOM:

		                      <?
		                        echo create_drop_down( "cbo_order_uom", 85, $unit_of_measurement,"", "", "", 1, "set_con_factor_value()","" );
		                      ?>
		                    </td>

									     </tr>
											 <tr>

		 								    <td width="50"  ></td>

												 <td width="155" class="must_entry_caption"> Cons UOM:<!-- Calander-->
													<span style="padding-left:4px;">
													 <?
																		 echo create_drop_down( "cbo_cons_uom", 85, $unit_of_measurement,"", "", "", 1, "set_con_factor_value()","" );
													 ?>
												 </span>
		     								</td>
		 							     </tr>
					    		</table>
							</td>
					  	</tr>
						<tr>
						  <td width="670"></td>
						  <td width="157"></td>
					  	</tr>
						<tr><td colspan="6"></td></tr>
						<tr>
						  <td colspan="4" align="center" class="button_container">
								<?
								echo load_submit_buttons( $permission, "fnc_item_details", 0,0 ,"reset_form('item_details_mst','','')",1);
								?>
							</td>
						</tr>
					</table>
				</form>
			</fieldset>
		</div>

		<div style="margin-top:30px;">
		<fieldset style="width:400px;">
			<legend>Item Rate</legend>
			<form name="item_details_mst" id="item_details_mst">
				<table cellpadding="0" cellspacing="2" >
					<tr>
						<td colspan="2" valign="top">
							<table  cellpadding="0" cellspacing="2" width="100%">
				 				<tr>
									<td width="55" class="must_entry_caption">Select Category</td>
									<td>
										<?
										//function create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name )
										echo create_drop_down( "search_item_category", 155,$item_category,"", '1', '---- Select ----', 0, "load_drop_down( 'requires/supplier_wise_rate_controller',this.value, 'load_drop_down_search_item_group', 'td_serach_item_group')" );
							        ?>
					   				</td>
	                </tr>
	                <tr>
	                  <td width="55" class="must_entry_caption">Select Group</td>
	                  <td id="td_serach_item_group" width="155">
	                                  <?
	                    echo create_drop_down( "search_item_group", 155,$blank_array, '', 1, '---- Select ----');
	                      ?>
	                    </td>
	                  </tr>

	                  <tr>
	                    <td width="55" class="must_entry_caption">Select Item</td>
	    								<td id="td_serach_item_description" width="155"> <!-- Calander-->
	                      <?
	                        echo create_drop_down( "search_item_description", 155,$blank_array, '', 1, '---- Select ----'  );
	                        ?>
	    								</td>
	                    </tr>



				    		</table>
						</td>
				  	</tr>
					<tr>
					  <td width="670"></td>
					  <td width="157"></td>
				  	</tr>
					<tr><td colspan="6"></td></tr>
					<tr>
					  <td colspan="4" align="center" class="button_container">

							<!-- <input id="submit" class="formbutton" value="Submit" name="submit" onclick="fnc_item_details(0)" style="width:80px" type="button"> -->
							<input id="submit" class="formbutton" value="Submit" name="submit" onclick="search_rate()" style="width:80px" type="button">
							<?

							// echo load_submit_buttons( $permission, "fnc_item_details", 0,0 ,"reset_form('item_details_mst','','')",1);
							?>
						</td>
					</tr>
				</table>
			</form>
		</fieldset>
		</div>
	</div>

<div style="width:500px; float: left;" id="item_data"></div>
</div>

</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	function updateSupplierRate(item_id, item_name){
		openmypage('requires/supplier_wise_rate_controller.php?action=order_popup&item_id='+item_id,item_name);
	}

	function openmypage(page_link,title){
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=450px,center=1,resize=0,scrolling=0','../')

		emailwindow.onclose = function(){
			
			var totalRow = this.contentDoc.getElementById('supplier_rate_row_num').value;
			var itemId = this.contentDoc.getElementById('item_id').value;
			var itemCategoryId = this.contentDoc.getElementById('item_cat_id').value;	
			var itemGroupId = this.contentDoc.getElementById('item_group_id').value;	
			// alert(totalRow);
			var data_all = "";
			var add_separator = 0;

			for(var row = 1; row <= totalRow; row++){


				var supplierName = this.contentDoc.getElementById('suppliername_'+ row).value;
				var rate  = this.contentDoc.getElementById('rate_'+ row).value;
				var effectiveDate  = this.contentDoc.getElementById('effectivedate_'+ row).value;
				var updateId = this.contentDoc.getElementById('supplierwiserate_'+ row).value;

				if (add_separator!=0) data_all +="_";
				data_all += updateId + '*' + supplierName + '*' + rate + '*' + effectiveDate + '*' + itemCategoryId + '*' + itemGroupId + '*' + itemId;
				add_separator++;			

			}
			
			var data="action=save_update_delete_supplier_rate&operation="+operation+'&data='+data_all;
		 	http.open("POST","requires/supplier_wise_rate_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_supplier_wise_rate_reponse;
		}

		
	}

	function fnc_supplier_wise_rate_reponse()
	{	
		if(http.readyState == 4){
		var reponse=trim(http.responseText).split('**');
			if(reponse[0]=='1')
				{
					$('#messagebox_main', window.parent.document).fadeTo(100,1,function(){ 
					$(this).html('Supplier wise rate successfully updated').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
					});
					search_rate();
				}
		}
	}

	

function fn_deletebreak_down_tr(rowNo,num_tbl)
{

			var r=confirm("Are you sure?")
			var row_num=$("#tbl_supplier_rate_"+num_tbl+" tr").length-1;
				
				$('#tbl_supplier_rate_'+num_tbl+ 'tr:eq("+row_num+")').remove()
				// var numRow = $('table#tbl_lab_test_cost tbody tr').length; 
				// set_sum_value( 'txtratelabtest_sum', 'txtrate_', 'tbl_lab_test_cost' );
}
</script>

</html>

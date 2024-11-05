<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yarn Count Information", "../../", 1, 1,$unicode,'','');

?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

		var permission='<? echo $permission; ?>';

function fnc_style_ref_info( operation )
{

	if (form_validation('txt_style_name*cbo_buyer_name*txt_style_short_name','Style Name*Buyer Name*Short Name')==false)
	{
		return;
	}
	else
	{
		
		var cbo_buyer_brand_id=$("#cbo_buyer_brand_id").val();
		var cbo_level_type_id=$("#cbo_level_type_id").val();
		var cbo_design_type=$("#cbo_design_type").val();
		var txt_division=$("#txt_division").val();
		var cbo_product_department=$("#cbo_product_department").val();
		var txt_style_name=$("#txt_style_name").val();
		var txt_style_short_name=$("#txt_style_short_name").val(); 
		var cbo_buyer_name=$("#cbo_buyer_name").val();
		var cbo_status=$("#cbo_status").val();
		var update_id=$("#update_id").val();
		var cbo_gmt_item=$("#cbo_gmt_item").val();
		var cbo_order_uom=$("#cbo_order_uom").val();
		var cbo_department_id=$("#cbo_department_id").val();

		var set_breck_down=$("#set_breck_down").val();
		var tot_set_qnty=$("#tot_set_qnty").val();
		var txt_sew_smv=$("#txt_sew_smv").val();

		var data="action=save_update_delete&operation="+operation+"&cbo_buyer_brand_id="+cbo_buyer_brand_id+"&cbo_level_type_id="+cbo_level_type_id+"&cbo_design_type="+cbo_design_type+"&txt_division="+txt_division+"&cbo_product_department="+cbo_product_department+"&txt_style_name="+txt_style_name+"&txt_style_short_name="+txt_style_short_name+"&cbo_buyer_name="+cbo_buyer_name+"&cbo_status="+cbo_status+"&update_id="+update_id +"&cbo_gmt_item="+cbo_gmt_item+"&cbo_order_uom="+cbo_order_uom+"&cbo_department_id="+cbo_department_id+"&set_breck_down="+set_breck_down+"&tot_set_qnty="+tot_set_qnty+"&txt_sew_smv="+txt_sew_smv;

		if(operation == 2)
		{
	 		if( !confirm("Are you Sure to Delete?"))
			{
				return;
			}
		}
		freeze_window(operation);

		http.open("POST","requires/style_ref_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_style_ref_info_reponse;
	}
}

function fnc_style_ref_info_reponse()
{
	if(http.readyState == 4)
	{
		//release_freezing(); return;
		console.log(http.responseText);
		var reponse=trim(http.responseText).split('**');
		if(reponse[0]==15)
		{
			 setTimeout('fnc_style_ref_info( 0 )',8000);
		}
		else
		{
			show_msg(reponse[0]);
		/*	if (reponse[0].length>2) reponse[0]=10;

			show_msg(reponse[0]);show_list_view( data, action, div, path, extra_func, is_append ) */
			show_list_view('','search_list_view','list_view_details','requires/style_ref_controller','setFilterGrid("list_view",-1)');
			reset_form('styleRef_1','',''); //Issue id -5068 Team
			set_button_status(0, permission, 'fnc_style_ref_info',1);
			release_freezing();
		}
	}
}

	function open_set_popup(unit_id)
	{
		var txt_quotation_id=document.getElementById('update_id').value;
		var set_breck_down=document.getElementById('set_breck_down').value;
		var tot_set_qnty=document.getElementById('tot_set_qnty').value;
		var txt_inquery_id=0;
		var set_smv_id=0;
		var txt_style_ref=document.getElementById('txt_style_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var cbo_company_name=0;
		var item_id=document.getElementById('cbo_gmt_item').value;

		var page_link="requires/style_ref_controller.php?txt_quotation_id="+trim(txt_quotation_id)+"&action=open_set_list_view&set_breck_down="+set_breck_down+"&tot_set_qnty="+tot_set_qnty+'&unit_id='+unit_id+'&txt_inquery_id='+txt_inquery_id+'&set_smv_id='+set_smv_id+'&txt_style_ref='+txt_style_ref+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name+'&item_id='+item_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Item Details", 'width=860px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var set_breck_down=this.contentDoc.getElementById("set_breck_down")
			var item_id=this.contentDoc.getElementById("item_id")
			var tot_set_qnty=this.contentDoc.getElementById("tot_set_qnty")
			var tot_smv_qnty=this.contentDoc.getElementById('tot_smv_qnty');
			document.getElementById('set_breck_down').value=set_breck_down.value;
			document.getElementById('cbo_gmt_item').value=item_id.value;
			document.getElementById('tot_set_qnty').value=tot_set_qnty.value;
			document.getElementById('txt_sew_smv').value=tot_smv_qnty.value;
			//calculate_cm_cost_with_method();
			//fnc_calculate_dep_oper_interest_income();
		}
	}

</script>

</head>

<body onLoad="set_hotkey()">

<div align="center">
     <? echo load_freeze_divs ("../../", $permission);  ?>
	<fieldset style="width:600px;">
		<legend>Style Reference Info</legend>
		<form name="styleRef_1" id="styleRef_1">
			<table cellpadding="0" cellspacing="3" width="100%">
				<tr>
					
					
					
					
					<td valign="top" colspan="4" style="justify-content: center;text-align: center;">
						Style ID
						<input type="text" name="txt_style_id" id="txt_style_id" class="text_boxes" style="width:150px" maxlength="50" title="" />
					</td>
					


				</tr>
			 	<tr>
					<td width="100" class="must_entry_caption">Style Name</td>
					<td>
						<input type="text" name="txt_style_name" id="txt_style_name" class="text_boxes" style="width:120px" maxlength="50" title="Maximum 50 Character" />
					</td>

					<td width="100" class="must_entry_caption">Buyer Name </td>
					<td valign="top">
						<?
						/*create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name )*/


							echo create_drop_down( "cbo_buyer_name", 130, "select id, buyer_name from lib_buyer where status_active=1 ORDER BY buyer_name ASC","id,buyer_name", "1", "Select Buyer Name", 0, "load_drop_down( 'requires/style_ref_controller',this.value, 'load_drop_down_buyer_brand', 'brand_td' );load_drop_down( 'requires/style_ref_controller',this.value, 'load_drop_down_buyer_division', 'division_td' );" );
						?>
					</td>
				</tr>

				<tr>
					<td width="100" >Buyer Brand</td>
					<td id="brand_td">
						<?php 

							echo create_drop_down( "cbo_buyer_brand_id", 130, "select id, brand_name from lib_buyer_brand where  status_active=1 and is_deleted=0 order by id","id,brand_name", "1", "Select Buyer Brand", 0, "" );
						 ?>
					</td>

					<td width="100" >Division </td>
					<td valign="top" id="division_td">
						<!-- <input type="text" name="txt_division" id="txt_division" class="text_boxes" style="width:120px" maxlength="50"  /> -->
						<?php echo create_drop_down( "txt_division", 130, $blank_array,"", "1", "Select Division", 0, "" ); ?>
					</td>
                    
                    
				</tr>

				<tr>
					 
					
                    <td width="100" >Department</td>
					<td id="department_td">
						<?php 

							echo create_drop_down( "cbo_department_id", 130, $blank_array,"", "1", "Select Department", 0, "" );
						 ?>
					</td>
                    
                    <td width="100" >Fashion/Order Type </td>
					<td valign="top">
						<?
						

							echo create_drop_down( "cbo_level_type_id", 130, "select level_type,id from lib_complexity_level where status_active=1 ORDER BY level_type ASC","id,level_type", "1", "Select", 0, "" );
						?>
					</td>
					
				</tr>

				<tr>
					<td width="100" >Design Type</td>
					<td >
						<?php 
							 $design_type=array(1=>"Custom Design",2=>"In House");
							echo create_drop_down( "cbo_design_type", 130, $design_type,"", "", "", 1, "" );
						 ?>
					</td>
                   <td width="100" class="must_entry_caption">Style Nick Name</td>
					<td>
						<input type="text" name="txt_style_short_name" id="txt_style_short_name" class="text_boxes" style="width:120px" maxlength="20" title="Maximum 20 Character" />
				   </td>
					
					
				</tr>

				<tr>
					<td >Prod. Dept.</td>
					<td>
						<? echo create_drop_down( "cbo_product_department", 130, $product_dept, "", 1, "-Select-", $selected, "", "", "" ); ?>
						
					</td>
					<td>Status</td>
					<td>
						<?
							echo create_drop_down( "cbo_status", 130, $row_status,"", "", "", 1, "" );
						?>
                        <input type="hidden" name="update_id" id="update_id" >
					</td>
				</tr>
				<tr>
					<td width="100" >Order UOM </td>
					<!-- <td valign="top">
						<? //echo create_drop_down( "cbo_order_uom",130, $unit_of_measurement, "",0, "", 1, "","","1,58" ); ?>
					</td> -->
					<td><? echo create_drop_down( "cbo_order_uom",55, $unit_of_measurement, "",0, "", 1, "","","1,58" ); ?>
                            <input type="button" id="set_button" class="image_uploader" style="width:75px;" value="Style Type/Item" onClick="open_set_popup(document.getElementById('cbo_order_uom').value);" />
                            <input type="hidden" id="set_breck_down" />
                            <input type="hidden" id="cbo_gmt_item"  />
                            <input type="hidden" id="tot_set_qnty" />
                            <input type="hidden" id="txt_sew_smv" />
                        </td>
                        <td width="100">
                            	File
                            </td>
                             <td>
                            	<input type="button" class="image_uploader" style="width:135px" value="CLICK TO ADD FILE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'style_ref_entry', 2 ,1)">
                            </td>
					
			  </tr>
                 <tr>
                            <td>Images</td>
                        	<td>
                            	<input type="button" class="image_uploader" style="width:160px" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'style_ref_entry', 0 ,1)">

                            </td>
                            
                        </tr>
                        <tr>
                        <td colspan="4" align="center"> </br></td>
                        </tr>

				<tr>
				  <td colspan="4" align="center" class="button_container">
						<?
							echo load_submit_buttons( $permission, "fnc_style_ref_info", 0,0 ,"reset_form('styleRef_1','','')");
						?>
					</td>
				</tr>

			</table>
		</form>
	</fieldset>
	<fieldset style="width:670px; margin-top:10px">
		<legend>List View</legend>
		<form>
			<div style="width:660px;" id="list_view_details">
                            <?
							$buyer_arr=return_library_array("select id, buyer_name from lib_buyer where status_active=1","id","buyer_name");
							$buyer_brand_arr=return_library_array("select id, brand_name from lib_buyer_brand where status_active=1","id","brand_name");
							$arr=array (2=>$buyer_arr,3=>$buyer_brand_arr,5=>$row_status);
							echo  create_list_view ( "list_view", "Style Id No,Style Name, Buyer Name,Brand,Nick Name, Status", "150,150,150,100,100,100","800","220",0, "select id,style_ref_name,buyer_id,status_active,style_no,short_name,buyer_brand_id from lib_style_ref where is_deleted=0 order by id desc", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,buyer_id,buyer_brand_id,0,status_active", $arr , "style_no,style_ref_name,buyer_id,buyer_brand_id,short_name,status_active", "requires/style_ref_controller", 'setFilterGrid("list_view",-1);' ) ;
							/*create_list_view( $table_id, $tbl_header_arr, $td_width_arr, $tbl_width, $tbl_height, $tbl_border, $query, $onclick_fnc_name, $onclick_fnc_param_db_arr, $onclick_fnc_param_sttc_arr,  $show_sl, $field_printed_from_array_arr,  $data_array_name_arr, $qry_field_list_array, $controller_file_path, $filter_grid_fnc, $fld_type_arr, $summary_flds, $check_box_all ,$new_conn )*/

							 ?>
            </div>
		</form>
	</fieldset>
</div>

</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

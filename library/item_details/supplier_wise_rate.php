<?
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
// echo load_html_head_contents("Item Details Entry", "../../", 1, 1,$unicode,'','');
//echo load_html_head_contents("Item Details Entry","../../",1 ,1 ,$unicode,1,'','' );
echo load_html_head_contents("Yarn Count Information", "../../", 1, 1, $unicode, '', '');

?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';
function search_rate()
{
	if (form_validation('search_item_category*search_item_group*search_item_description','Item Category*Item Group*Item Description')==false)
	{
		return;
	}

	var data="action=get_search_rate"+get_submitted_data_string('search_item_category*search_item_group*hidden_item_description',"../../");
	// alert(data);
	freeze_window(1);
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
			release_freezing();

		}


	}
	function fnc_item_details( operation )
	{

		if (form_validation('cbo_item_category*cbo_item_group*cbo_order_uom*cbo_cons_uom*txt_item_description','Item Category*Item Group*Order UOM*Cons UOM*Item Discripttion')==false)
		{
			return;
		}
		else {
      		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_item_category*cbo_item_group*txt_item_description*txt_item_code*cbo_order_uom*cbo_cons_uom*update_id*txt_tag_buyer_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/supplier_wise_rate_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_item_details_reponse;
    	}
	}
	      function fnc_item_details_reponse(){
	        if(http.readyState == 4 && http.readyState != 1){
	          var reponse=trim(http.responseText).split('**');

	          show_msg(reponse[0]);
	          show_list_view(reponse[2],'get_item_rate_list','item_rate','../item_details/requires/supplier_wise_rate_controller','setFilterGrid("list_view",-1)');
	          set_button_status(0, permission, 'fnc_item_details',1);

	          reset_form('itemdetailsmst_1','','', 'cbo_order_uom,1*cbo_cons_uom,1');
	        	$('#cbo_item_group').attr('disabled',false);
	        	$('#cbo_item_category').attr('disabled',false);
	        	console.log("fnc_item_details_reponse");
	          release_freezing();

	        }
	        else{
	          release_freezing();
	        }
        }

function getItemDescription(){
	/*load_drop_down('requires/supplier_wise_rate_controller',groupId+','+document.getElementById('search_item_category').value, 'load_drop_down_item_description', 'td_serach_item_description');
	set_multiselect('search_item_description','0','0','','');*/
	if(form_validation('search_item_category*search_item_group','Item Category*Item Group') == false){
		return;
	}
	else{
		var item_category=document.getElementById('search_item_category').value;
		var item_group =document.getElementById('search_item_group').value
		var page_link='requires/supplier_wise_rate_controller.php?action=openpopup_item_description&item_category='+item_category+'&item_group='+item_group;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Item Description','width=450px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{	var comma;
			var itemdescription=this.contentDoc.getElementById("itemdescription").value;
			var itemdescription=itemdescription.split("**");
			var description_id = [];
			var description_name = [];
			for(var b=0; b<itemdescription.length; b++)
			{
				var exdata="";
				var exdata=itemdescription[b].split("__");
				description_id.push(exdata[0]);
				description_name.push(exdata[1]);
			}
			document.getElementById('hidden_item_description').value=description_id.toString();
			document.getElementById('search_item_description').value=description_name.join('*');
			search_rate();

		}
	}


}
function clearItemDescription(){
	var item_des = document.getElementById('hidden_item_description').value;
	var item_id = document.getElementById('search_item_description').value;
	if(item_des !='' || item_id != ''){
		document.getElementById('hidden_item_description').value = " ";
		document.getElementById('search_item_description').value = " ";
	}

}

function getItemGroupUom(groupId){
	load_drop_down('requires/supplier_wise_rate_controller',groupId, 'load_drop_down_item_group_order_uom', 'td_item_group_order_uom');

	load_drop_down('requires/supplier_wise_rate_controller',groupId, 'load_drop_down_item_group_cons_uom', 'td_item_group_cons_uom');

}
function openmypage_tag_buyer()
	{
		var txt_tag_buyer_id = $('#txt_tag_buyer_id').val();
		var title = 'Buyer Name Selection Form';	
		var page_link = 'requires/supplier_wise_rate_controller.php?txt_tag_buyer_id='+txt_tag_buyer_id+'&action=buyer_name_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var buyer_id=this.contentDoc.getElementById("hidden_buyer_id").value;	 //Access form field with id="emailfield"
			var buyer_name=this.contentDoc.getElementById("hidden_buyer_name").value;
			$('#txt_tag_buyer_id').val(buyer_id);
			$('#cbo_tag_buyer').val(buyer_name);
		}
	}



</script>

</head>
<body onLoad="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission);  ?>
<div style="width:980px; min-height:200px;">

	<div style="width:480px;float:left">
		<div style="width:430px;">
			<fieldset>
				<legend>Item Details Entry</legend>
				<form name="itemdetailsmst_1" id="itemdetailsmst_1">
					<table cellpadding="0" cellspacing="2" >
						<tr>
							<td colspan="2" valign="top">
								<table  cellpadding="0" cellspacing="2" width="100%">
					 				<tr>
										<td width="50" class="must_entry_caption">Item Category</td>

										<td>
		                  				<input type="hidden" name="update_id" id="update_id" />
		                    <?
											echo create_drop_down( "cbo_item_category", 155,$item_category,"", '1', '---- Select ----', 0, "load_drop_down( 'requires/supplier_wise_rate_controller',this.value, 'load_drop_down_item_group', 'td_item_group')","","","","","1,2,3,12,13,14,24,25" );
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
		                                        <Input name="txt_item_description" ID="txt_item_description"   style="width:145px" value="" class="text_boxes" autocomplete="off">
		    								</td>
		                    </tr>
		                  <tr>
		                  	<tr>
			                    <td width="50">Item Code</td>
			                    <td width="155"><Input name="txt_item_code" ID="txt_item_code" style="width:145px" value="" class="text_boxes" autocomplete="off"></td>
		                    </tr>
							<tr>
								<td width="50">Tag Buyer</td>
								<td>
									<input type="text" name="cbo_tag_buyer" id="cbo_tag_buyer" class="text_boxes" style="width:145px;" placeholder="Double Click To Search" onDblClick="openmypage_tag_buyer();" readonly />
									<input type="hidden" name="txt_tag_buyer_id" id="txt_tag_buyer_id" />				
				  				</td>
		                	</tr>
		                  <tr>
		                    <td width="50"	class="">Item Unit:</td>

										    <td 	width="155" class="" id="td_item_group_order_uom">Order UOM:

		                      <?
		                      	asort($unit_of_measurement);
		                        echo create_drop_down( "cbo_order_uom", 85, $unit_of_measurement,"", "", "", 1, "set_con_factor_value()",1 );
		                      ?>
		                    </td>

									     </tr>
											 <tr>

		 								    <td width="50"   ></td>

												 <td width="155" id="td_item_group_cons_uom" class=""> Cons UOM:<!-- Calander-->
													<span style="padding-left:4px;">
													 <?echo create_drop_down( "cbo_cons_uom", 85, $unit_of_measurement,"", "", "", 1, "set_con_factor_value()",1 );
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
								echo load_submit_buttons( $permission, "fnc_item_details", 0,0 ,"reset_form('itemdetailsmst_1','','','cbo_order_uom,1*cbo_cons_uom,1', '', '')",1);
								?>
							</td>
						</tr>

					</table>
				</form>
			</fieldset>
		</div>

		<div id="item_rate">
			<?
					$itemGroup = return_library_array( "select id,item_name from lib_item_group order by item_name",'id','item_name');

					/*$idToDesArr =  array(0=>$itemGroup, 2=>$unit_of_measurement, 3=>$unit_of_measurement);
				  echo  create_list_view ( "list_view", "Item Group,Item Description,Order Uom,Cons Uom", "80,100,70,70","390","220",0, "select lib_item_details.id, lib_item_details.item_description, lib_item_details.item_group_id, lib_item_group.trim_uom as cons_uom, lib_item_group.order_uom  from lib_item_details left outer join lib_item_group on lib_item_group.id = lib_item_details.item_group_id  where lib_item_details.is_deleted=0 order by lib_item_details.item_group_id", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "item_group_id,0,order_uom,cons_uom", $idToDesArr , "item_group_id,item_description,order_uom,cons_uom", "requires/supplier_wise_rate_controller", 'setFilterGrid("list_view",-1);' ) ;*/
				  $idToDesArr =  array(0=>$itemGroup, 3=>$unit_of_measurement, 4=>$unit_of_measurement);
				  echo  create_list_view ( "list_view", "Item Group,Item Description,Item Code,Order Uom,Cons Uom", "80,100,70,50,50","420","220",0, "select lib_item_details.id, lib_item_details.item_description, lib_item_details.item_code, lib_item_details.item_group_id, lib_item_group.trim_uom as cons_uom, lib_item_group.order_uom  from lib_item_details left outer join lib_item_group on lib_item_group.id = lib_item_details.item_group_id  where lib_item_details.is_deleted=0 order by lib_item_details.item_group_id", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "item_group_id,0,0,order_uom,cons_uom", $idToDesArr , "item_group_id,item_description,item_code,order_uom,cons_uom", "requires/supplier_wise_rate_controller", 'setFilterGrid("list_view",-1);' ) ;

			 ?>
		</div>

		<div style="margin-top:30px;">
		<fieldset style="width:430px;">
			<legend>Item Rate</legend>
			<form name="itemdetails_search" id="itemdetails_search">
				<table cellpadding="0" cellspacing="2" >
					<tr>
						<td colspan="2" valign="top">
							<table  cellpadding="0" cellspacing="2" width="100%">
				 				<tr>
									<td width="55" class="must_entry_caption">Select Category</td>
									<td>
										<?
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
	    				<td id="td_serach_item_description" width="155">
	                    <?
	                        //echo create_drop_down( "search_item_description", 155,$blank_array, '', 1, '---- Select ----'  );
	                    ?>
	                    <input type="text" placeholder="Browse"  id="search_item_description"  name="search_item_description" readonly class="text_boxes" style="width:142px" onDblClick="getItemDescription()"/>
                        <input type="hidden" id="hidden_item_description" name="hidden_item_description" class="text_boxes" style="width:155px" />
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
					<!-- <tr>
					  <td colspan="4" align="center" class="button_container">
							<input id="submit" class="formbutton" value="Submit" name="submit" onclick="search_rate()" style="width:80px" type="button">
							<?
							?>
						</td>
					</tr> -->
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
		openmypage('requires/supplier_wise_rate_controller.php?action=supplier_rate_popup&item_id='+item_id,item_name);
	}

	function openmypage(page_link,title){
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=770px,height=450px,center=1,resize=0,scrolling=0','../')

		emailwindow.onclose = function(){

			var totalRow = this.contentDoc.getElementById('supplier_rate_row_num').value;
			var itemId = this.contentDoc.getElementById('item_id').value;
			var itemCategoryId = this.contentDoc.getElementById('item_cat_id').value;
			var itemGroupId = this.contentDoc.getElementById('item_group_id').value;
			var supplierRowIds = this.contentDoc.getElementById('supplier_ids').value;
			var idsToDelete = this.contentDoc.getElementById('deleted_db_ids').value;
			var temp = supplierRowIds.split(",");
			var loopLimit = temp.length -1;
			var data_all = "";
			var add_separator = 0;

			for(var row = 0; row <= loopLimit; row++){

				var supplierName = this.contentDoc.getElementById('suppliername_'+ temp[row]).value;
				var rate  = this.contentDoc.getElementById('rate_'+ temp[row]).value;
				var supplier_code  = this.contentDoc.getElementById('supplierCode_'+ temp[row]).value;
				var remarks  = this.contentDoc.getElementById('remarks_'+ temp[row]).value;
				var effectiveDate  = this.contentDoc.getElementById('effectivedate_'+ temp[row]).value;
				var updateId = this.contentDoc.getElementById('supplierwiserate_'+ temp[row]).value;

				if (supplierName == 0 || rate == 0 || effectiveDate == '') {

					alert('You should fill up all the fields for row ' + (row + 1));

					return false;
				}

				if (add_separator!=0) data_all +="_";
				data_all += updateId + '*' + supplierName + '*' + rate + '*' + effectiveDate + '*' + itemCategoryId + '*' + itemGroupId + '*' + itemId + '*' + idsToDelete + '*' + supplier_code + '*' + remarks;
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
				var numRow = $('table#tbl_lab_test_cost tbody tr').length;
				set_sum_value( 'txtratelabtest_sum', 'txtrate_', 'tbl_lab_test_cost' );
}
</script>

</html>

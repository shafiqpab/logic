<?php
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Sub Contract Material Receive Return
Functionality	:	
JS Functions	:
Created by		:	Sapayth
Creation date 	: 	26-11-2020
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
echo load_html_head_contents('Sub-Contract Material Receive Return', '../', 1, 1, $unicode, 1, '');
?>

<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../logout.php';
	var permission='<?php echo $permission; ?>';

	

	function openmypage_rec_id(companyId) {
		if ( !form_validation('cbo_company_name','Company Name') ) {
			return;
		}
		page_link='requires/sub_contract_material_receive_return_controller.php?action=receive_popup&data='+companyId;
		title = 'Search Receive ID';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=780px, height=400px, center=1, resize=0, scrolling=0','')
		emailwindow.onclose=function() {
			// var theform=this.contentDoc.forms[0]//("search_subcontract_frm"); //Access the form inside the modal window
			var rcvMstData=this.contentDoc.getElementById("hidden_rcv_mst_id").value;
			var type = 1;
			if (rcvMstData!="") {
				freeze_window(5);
				console.log(rcvMstData);
				rcvMstData=rcvMstData.split('_');
				get_php_form_data( rcvMstData[0]+'***1', 'load_mst_data_to_form', "requires/sub_contract_material_receive_return_controller" );
				show_list_view(rcvMstData[0]+'***1', 'subcontract_receive_stock_list_view', 'stock_list_area', 'requires/sub_contract_material_receive_return_controller', '');
				release_freezing();
			}
		}
	}

	function openmypage_return_no(companyId) {
		if ( !form_validation('cbo_company_name','Company Name') ) {
			return;
		}
		page_link='requires/sub_contract_material_receive_return_controller.php?action=return_popup&data='+companyId;
		title = 'Search Return No';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=780px, height=400px, center=1, resize=0, scrolling=0','')
		emailwindow.onclose=function() {
			// var theform=this.contentDoc.forms[0]//("search_subcontract_frm"); //Access the form inside the modal window
			var theform=this.contentDoc.forms[0];
			var returnMstId=this.contentDoc.getElementById("hidden_return_mst_id").value;
			var recvMstId=this.contentDoc.getElementById("hidden_recv_mst_id").value;
		
			var type = 2;
			if (returnMstId.value!="") {
				freeze_window(5);
				get_php_form_data(returnMstId+'***'+type, 'load_mst_data_to_form', "requires/sub_contract_material_receive_return_controller");
				show_list_view(recvMstId+'***'+1, 'subcontract_receive_stock_list_view', 'stock_list_area', 'requires/sub_contract_material_receive_return_controller', '');
				show_list_view(returnMstId, 'subcontract_return_stock_list_view', 'return_list_area', 'requires/sub_contract_material_receive_return_controller', '');
				release_freezing();
			}
		}
	}

	function put_data_dtls_part(id, type) {

		var data = id + '_' + type;
		show_list_view(data, 'show_rcv_listview', 'receive_list_tbody', 'requires/sub_contract_material_receive_return_controller', '');

		if (type==2) {
			set_button_status(1, permission, 'fnc_material_receive_return',1);
		} else {
			set_button_status(0, permission, 'fnc_material_receive_return',1);
		}
	}
	
	function fnc_material_receive_return ( operation ) {
		
		if(operation==2)
		{
			alert("Delete Restricted.");
			return;
		}
		
		if(operation==4)
		{
			if ($('#txt_return_no').val()==""){
				alert("Plz Save Data");
				return;
			}
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#hdn_update_id').val()+'*'+$('#txt_return_no').val()+'*'+report_title+'*'+$('#hdn_location_id').val(), "material_receive_return_print", "requires/sub_contract_material_receive_return_controller") 
			//return;
			show_msg("3");
		}
		if( !form_validation('txt_return_date*txt_return_quantity','Return Date*Return Quantity') ) {
			return;
		}
		if(operation==1 || operation==0)
		{
			//var balance=Number($("#txt_balance_qnty").val());
			
			// var balance = trim(return_global_ajax_value(document.getElementById('txt_receive_no').value+'**'+document.getElementById('hdn_rcv_dtls_id').value, 'get_balance_qnty', '', 'requires/sub_contract_material_receive_return_controller'));
			var prev=Number($("#previous_rec_qty").val());
			var rec_balance=Number($("#txt_rec_balance").val());
			var qnty=Number($("#txt_return_quantity").val());
			if(qnty>rec_balance)
			{
				 alert('Return Qnty Can not be greater than Balance Quantity');
				 return;
			}
		}
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_party_name*txt_return_date*txt_receive_challan*txt_remarks*hdn_mat_rcv_id*hdn_location_id*cbo_item_category*txt_material_description*hdn_rcv_dtls_id*hdn_rcv_id*hdn_fabric_dtls_id*hdn_job_dtls_id*hdn_job_id*hdn_job_break_id*hdn_buyer_po_id*hdn_order_no_id*txt_return_quantity*txt_return_no*hdn_update_id*hdn_return_dtls_id*txt_lot_no*txt_brand*txt_roll*txt_cone*txt_rec_balance*ref_trans_type*ref_entry_form', '../');
		freeze_window(operation);
		http.open("POST","requires/sub_contract_material_receive_return_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_material_return_response;
	}

	function fnc_material_return_response() {
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
			var type = 2;
			//if (response[0].length>3) reponse[0]=10;
			show_msg(response[0]);
			//$('#cbo_uom').val(12);
			if(response[0]==14)
			{
				alert(response[1]);
				release_freezing();
				return;
				
			}
			if(response[0]==0 || response[0]==1)
			{
				var updateId = response[2];
				document.getElementById('txt_return_no').value= response[1];
				document.getElementById('hdn_update_id').value = updateId;
				set_button_status(1, permission, 'fnc_material_receive_return',1);
				show_list_view(response[3]+'***'+1, 'subcontract_receive_stock_list_view', 'stock_list_area', 'requires/sub_contract_material_receive_return_controller', '');
				show_list_view(updateId, 'subcontract_return_stock_list_view', 'return_list_area', 'requires/sub_contract_material_receive_return_controller', '');
				// show_list_view(response[2],'subcontract_receive_dtls_list_view','receive_list_view','requires/sub_contract_material_receive_controller','setFilterGrid("list_view",-1)');
			}
			release_freezing();
		}
	}

	var str_brand = [<? echo substr(return_library_autocomplete( "select brand_name from lib_brand group by brand_name", "brand_name" ), 0, -1); ?>];
	function set_auto_complete_brand(type)
	{
		if(type=='brand_return')
		{
			$(".txtbrand").autocomplete({
			source: str_brand
			});
		}
	}

	function fn_sub_con_mat_receive_return_print()
	{
	
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var page_link='requires/sub_contract_material_receive_return_controller.php?action=return_multy_number_popup&company='+company+'&cbo_return_to='+$("#cbo_party_name").val(); 
		var title="Search Return Number Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=410px,center=1,resize=0,scrolling=0',' ')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var returnId=this.contentDoc.getElementById("hidden_return_id").value; // mrr id
			var returnNumber=this.contentDoc.getElementById("hidden_return_number").value; // mrr number
			var report_title=$( "div.form_caption" ).html();

			print_report( $('#cbo_company_name').val()+'*'+returnId+'*'+returnNumber+'*'+report_title+'*'+$('#hdn_location_id').val(), "material_multi_receive_return_print", "requires/sub_contract_material_receive_return_controller" ) 
			return;
		}
	}

</script>
</head>
<body onLoad="set_hotkey();set_auto_complete_brand('brand_return');">
    <div align="center" style="width:100%;">
	    <?php echo load_freeze_divs ('../', $permission); ?>
	    <fieldset style="width: 60%;">
	    <legend>Sub-Contract Material Receive Return</legend>
	        <form name="materialRcvReturn_1" id="materialRcvReturn_1" autocomplete="off">
	            <table style="width: 100%;" cellspacing="2" cellpadding="0" border="0">
	            	<tr>
	            		<td></td>
	            		<td></td>
	            		<td align="right">Return Number</td>
	            		<td>
	            			<input class="text_boxes" type="text" name="txt_return_no" id="txt_return_no" onDblClick="openmypage_return_no(document.getElementById('cbo_company_name').value)" placeholder="Double Click" style="width:160px;" readonly/>
	            			<input type="hidden" id="hdn_update_id" name="hdn_update_id">
	            			<input type="hidden" id="hdn_mat_rcv_id" name="hdn_mat_rcv_id">
	            			<input type="hidden" id="hdn_location_id" name="hdn_location_id">
	            		</td>
	            		<td></td>
	            		<td></td>
	            	</tr>
	                <tr>
	                    <td width="130" align="right" class="must_entry_caption">Company Name</td>
	                    <td width="170"> 
	                        <?php echo create_drop_down( 'cbo_company_name', 172, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", 'id,company_name', 1, '-- Select Company --', $selected, "load_drop_down( 'requires/sub_contract_material_receive_return_controller', this.value, 'load_drop_down_party', 'party_td' );"); ?>
	                    </td>
	                    <td width="130" align="right">Receive ID</td>
	                    <td width="170">
	                        <input class="text_boxes"  type="text" name="txt_receive_no" id="txt_receive_no" onDblClick="openmypage_rec_id(document.getElementById('cbo_company_name').value)" placeholder="Double Click" style="width:160px;" readonly/>
							<input type="hidden" id="ref_trans_type" value="">
	            			<input type="hidden" id="ref_entry_form" value="">
	                    </td>
	                    <td  width="130" class="must_entry_caption" align="right">Return Date</td>
	                    <td>
	                        <input type="text" name="txt_return_date" id="txt_return_date"  class="datepicker" style="width:160px" />             
	                    </td>
	                </tr>
	                <tr>
	                    <td align="right" class="must_entry_caption">Return To</td>
	                    <td id="party_td">
	                        <?php echo create_drop_down( "cbo_party_name", 172, $blank_array,"", 1, "-- Select Party --", $selected, "" );?>
	                    </td>
	                    <td width="130" class="must_entry_caption" align="right">Receive Challan</td>
	                    <td width="170">
	                        <input class="text_boxes" type="text" name="txt_receive_challan" id="txt_receive_challan" style="width:160px;" />  
	                    </td>
	                    <td width="130" align="right">Remarks</td>
	                    <td>
	                        <input class="text_boxes" type="text" name="txt_remarks" id="txt_remarks" style="width:160px" />             
	                    </td>
	                </tr>
	            </table>
	            <br>
	            <br>
	            <fieldset style="width:100%;">
	    	        <legend>Metarial Details Entry</legend>
		            <table cellpadding="0" cellspacing="2" border="0" style="width: 100%;">
		                <thead class="form_table_header">
		                    <tr align="center" >
		                        <th width="100" class="must_entry_caption">Item Category</th>
		                        <th width="100" class="must_entry_caption">Material Description</th>
		                        <th width="60">Color</th>
		                        <th width="60">GMTS Size</th>
								<th width="60">Lot No.</th>
								<th width="60">Brand</th>
		                        <th width="40">GSM</th>
		                        <th width="70">Stitch Length</th>
		                        <th width="40">Grey Dia/ Width</th>
		                        <th width="50">M/C Dia</th>
		                        <th width="50">M/C Gauge</th>
		                        <th width="40">Fin. Dia/ Width</th>
		                        <th width="60">Dia UOM</th>
		                        <th width="30">Roll /Bag</th>
		                        <th width="70" class="must_entry_caption">Return Qty</th>
		                        <th width="60">Rate</th>
								<th width="60">Balance</th>
		                        <th width="60">UOM</th>		                        
		                        <th width="30">Cone</th>
		                    </tr>
		                </thead> 
		                <tbody id="receive_list_tbody">
		                	<tr>
			                    <td>
			                    	<input type="hidden" name="txt_order_no_id" id="txt_order_no_id">
			                    	<input type="hidden" name="txt_item_id" id="txt_item_id">
			                        <?php
			                        	echo create_drop_down( "cbo_item_category", 100, $item_category,"", 1, "--Select Item--",0,"change_uom(this.value);hide_material_description(this.value);", "","1,2,3,4,13,14,30" );
			                        ?>
			                    </td>
			                    <td>
			                        <input type="text" id="txt_material_description" name="txt_material_description" class="text_boxes" style="width:140px" title="Maximum 200 Character" >
			                    </td>
			                    <td id="color_td">
					                <?php
					                   echo create_drop_down( "cbo_color", 60, $blank_array, "", 1, "-Select-", 0,"","","" );
					                ?>
			                    </td>
			                    <td>
			                    	<input type="text" id="txtsize" name="txtsize" class="text_boxes txt_size" style="width:60px"/>
			                    </td>
								<td>
                    				<input type="text" id="txt_lot_no" name="txt_lot_no" class="text_boxes txt_size" style="width:60px"/>
                    			</td>
								<td>
                    				<input type="text" id="txt_brand" name="txt_brand" class="text_boxes txtbrand" style="width:60px"/>
                    			</td>
			                    <td>
			                        <input name="txt_gsm" id="txt_gsm" class="text_boxes_numeric" type="text"  style="width:40px" value=""/>
			                    </td>
			                    <td>
			                        <input name="txt_stitch_length" id="txt_stitch_length" class="text_boxes" type="text"  style="width:70px" value=""/>
			                    </td>
			                    <td>
			                        <input name="txt_grey_dia" id="txt_grey_dia" class="text_boxes" type="text"  style="width:40px" />
			                    </td>
			                     <td>
			                        <input name="txt_mc_dia" id="txt_mc_dia" class="text_boxes" type="text"  style="width:50px" />
			                    </td>
			                     <td>
			                        <input name="txt_mc_gauge" id="txt_mc_gauge" class="text_boxes" type="text"  style="width:50px" />
			                    </td>
			                    <td>
			                        <input name="txt_fin_dia" id="txt_fin_dia" class="text_boxes" type="text"  style="width:40px" />
			                    </td>
			                    <td>
			                    	<?php echo create_drop_down( "cbo_dia_uom",60, $unit_of_measurement,"", 1, "-UOM-",0,"", "","25,29" );?>
			                    </td>
			                    <td>
			                        <input name="txt_roll" id="txt_roll" class="text_boxes_numeric" type="text"  style="width:30px" />
			                    </td>
			                    <td>
			                        <input name="txt_return_quantity" previous_rec_qty="" id="txt_return_quantity" class="text_boxes_numeric" type="text"  style="width:65px" />
			                    </td>
			                    <td>
			                        <input name="txt_rec_rate" id="txt_rec_rate" class="text_boxes_numeric" type="text"  style="width:55px" />
			                    </td>
								<td>
			                        <input name="txt_rec_balance" id="txt_rec_balance" class="text_boxes_numeric" type="text"  style="width:55px" />
			                    </td>
			                    <td>
			                        <?php echo create_drop_down( "cbo_uom",60, $unit_of_measurement,"", 1, "-UOM-",'',"", "","" );?>
			                    </td>
			                    <td>
			                        <input name="txt_cone" id="txt_cone" class="text_boxes_numeric" type="text"  style="width:30px" />
			                    </td>
			                </tr>
		                </tbody>
		            </table>
	            </fieldset>

	            <table cellspacing="2" cellpadding="0" border="0">
	                <tr>
	                    <td align="center" colspan="13" valign="middle" class="button_container">
	                        <?php echo load_submit_buttons($permission, "fnc_material_receive_return", 0,0,"reset_form('materialRcvReturn_1','receive_list_view','','cbo_status,2', 'disable_enable_fields(\'cbo_company_name\',0)')",1); ?> &nbsp;
                            <input type="button" value="Print" onClick="fnc_material_receive_return(4)"  style="width:100px;" name="print_1" id="print_1" class="formbutton" />
							<input type="button" name="print2" value="Print Multi Return Number" id="print2" class="formbutton" style="width: 180px;" onClick="fn_sub_con_mat_receive_return_print()" />
	                    </td>
	                </tr>
	                <br/>
	                <tr align="center">
	                    <td colspan="13" id="receive_list_area">
	                    	<?php
	                    		/*$sql = "select b.id, b.mst_id, b.item_category_id, b.material_description, b.color_id, b.size_id, b.gsm, b.stitch_length, b.grey_dia, b.mc_dia, b.mc_gauge, b.fin_dia, b.dia_uom, b.rate, b.uom, b.subcon_roll, b.rec_cone, b.order_id, b.buyer_po_id, b.job_id, b.job_dtls_id, b.job_break_id, b.fabric_details_id, a.quantity
									from sub_material_return_dtls a, sub_material_dtls b
									where a.id=2 and a.is_deleted=0 and b.is_deleted=0 and a.receive_dtls_id = b.id";

									echo create_list_view('return_list_view', 'Item Category,Material Description,Color,GMTS Size,GSM,Stitch Length,Grey Dia/Width,M/C Dia,M/C Gauge,Fin. Dia/Width,Dia UOM,Roll/Bag,Return Qty,Rate,UOM,Cone', '80,60,60,80,130,70,60,60,60,60,60,60,70,60,60,60', '1280', '250', 0, $sql, 'get_php_form_data', 'id', '', 1, '0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0', '', 'item_category_id,material_description,color_id,size_id,gsm,stitch_length,grey_dia,mc_dia,mc_gauge,fin_dia,dia_uom,subcon_roll,quantity,rate,uom,rec_cone', 'requires/sub_contract_material_receive_return_controller', '', '0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0','0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0', '');*/

										// create_list_view($table_id, $tbl_header_arr, $td_width_arr, $tbl_width, $tbl_height, $tbl_border, $query, $onclick_fnc_name, $onclick_fnc_param_db_arr, $onclick_fnc_param_sttc_arr, $show_sl, $field_printed_from_array_arr, $data_array_name_arr, $qry_field_list_array, $controller_file_path, $filter_grid_fnc, $fld_type_arr, $summary_flds, $check_box_all, $new_conn)
	                    	?>
	                    </td>	
	                </tr>
	          </table>
	        </form>
	        <div id="return_list_area" style="width:100%; margin:10px auto;"></div>
	    </fieldset>
	    <div id="stock_list_area" style="width:500px; margin:10px auto;"></div>
   </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
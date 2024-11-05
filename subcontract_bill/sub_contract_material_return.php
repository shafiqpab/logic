<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Subcontract Material Return						
Functionality	:	
JS Functions	:
Created by		:	Sohel 
Creation date 	: 	02-05-2013
Updated by 		: 		
Update date		: 
Oracle Convert 	:	Kausar		
Convert date	: 	21-05-2014	   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start(); 
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sub-Contract Material Return Info", "../", 1,1, $unicode,1,'');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';


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
	
	function openmypage_return_id()
	{ 
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value;
		emailwindow=dhtmlmodal.open('EmailBox','iframe', 'requires/sub_contract_material_return_controller.php?action=return_id_popup&data='+data,'Subcontract Material Return Popup', 'width=800px, height=400px, center=1, resize=0, scrolling=0','')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("return_id");
			if (theemail.value!="")
			{
				freeze_window(5);
				get_php_form_data(theemail.value, "load_php_data_to_form_mst", "requires/sub_contract_material_return_controller" );
				show_list_view(theemail.value,'material_return_list_view','material_return_list_view','requires/sub_contract_material_return_controller','setFilterGrid("list_view",-1)');
				reset_form('','','txt_orderno*cbo_itemcategory*txt_description*txt_quantity*cbo_uom');
				set_button_status(0, permission, 'fnc_material_return',1,1);
				release_freezing();
			}
		}
	}
	
	function job_search_popup()
	{
		if ( form_validation('cbo_company_name*cbo_company_supplier','Company Name*Supplier Name')==false )
		{
			return;
		}
		else
		{
			var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_company_supplier').value;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/sub_contract_material_return_controller.php?action=job_popup&data='+data, 'Order Selection Form', 'width=810px,height=400px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				var theemail=this.contentDoc.getElementById("order_id").value;
				var theemailval=this.contentDoc.getElementById("order_no").value;
				if (theemail!="")
				{
					freeze_window(5);
					$("#order_no_id").val(theemail);
					$("#txt_orderno").val(theemailval);
					release_freezing();
				}
			}
		}
	}
	
	function openmypage()
	{
		if ( form_validation('order_no_id*cbo_itemcategory','order no*item category')==false )
		{
			return;
		}
		else
		{
			var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_company_supplier').value+"_"+document.getElementById('order_no_id').value+"_"+document.getElementById('cbo_itemcategory').value;
			var page_link='requires/sub_contract_material_return_controller.php?action=material_description_return_popup&data='+data;
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Material Description Selection Form', 'width=1010px,height=400px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{	
				var theemail=this.contentDoc.getElementById("description_id").value;
				var theemailval=this.contentDoc.getElementById("material_description").value;
				var response=theemailval.split('__');
				if(theemail!="")
				{
					freeze_window(5);
					$("#description_id").val(theemail);
					$("#txt_description").val(response[0]);
					$("#txt_dia").val(response[1]);
					$("#txt_rec_challan_no").val(response[2]);
					$("#txt_gsm").val(response[3]);
					$("#txt_fin_dia").val(response[4]);
					$("#txt_color_id").val(response[5]);
					$("#txt_color_show").val(response[6]);
					$("#txt_size_id").val(response[7]);
					$("#txt_size_show").val(response[8]);
					$("#txt_lot_no").val(response[9]);
					$("#txt_brand").val(response[10]);
					
					
					//var data_dtls=$("#order_no_id").val()+"_"+theemail;
					//get_php_form_data( data_dtls, "load_php_data_for_dtls", "requires/sub_contract_material_return_controller" );
					release_freezing();
				}
			}
		}
	}

	function fnc_material_return( operation )
	{
		if ( form_validation('cbo_company_name*cbo_company_supplier*txt_return_date*txt_orderno*txt_description*txt_quantity','Company Name*Supplier Name*Return Date*order no*description*quantity')==false )
		{
			return;
		}
		else
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_return_no*cbo_company_name*cbo_location_name*cbo_company_supplier*txt_return_date*txt_return_challan*txt_remarks*update_id*txt_orderno*order_no_id*cbo_itemcategory*txt_description*txt_rec_challan_no*txt_quantity*txt_cone*cbo_uom*updateid_1*txt_roll*txt_dia*txt_gsm*txt_fin_dia*txt_color_id*cbo_forwarder*txt_transport_company*txt_size_id*txt_lot_no*txt_brand',"../");
			
			freeze_window(operation);
			http.open("POST","requires/sub_contract_material_return_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_material_return_response;
		}
	}

	function fnc_material_return_response()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);
			var response=trim(http.responseText).split('**');
			//if (response[0].length>3) response[0]=10;	
			show_msg(response[0]);
			if(trim(response[0])=='17'){
				alert(response[1]);
				release_freezing();
				return;
			}
			
			var dtlsRow=0;
			if(response[0]==2) var dtlsRow=response[4];
			if(dtlsRow<2 && response[0]==2)
			{
				location.reload();
			}
			else
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('updateid_1').value = response[2];
				document.getElementById('txt_return_no').value = response[3];
				//set_button_status(0, permission, 'fnc_material_return',1);
				show_list_view(response[1],'material_return_list_view','material_return_list_view','requires/sub_contract_material_return_controller','setFilterGrid("list_view",-1)');
				
				reset_form('','','cbo_itemcategory*txt_description*description_id*txt_dia*txt_quantity*cbo_uom*txt_roll*txt_cone');
				set_button_status(0, permission, 'fnc_material_return',1,0);
			}
			release_freezing();
		}
	}
	
	function change_uom(item)
	{
		if(item==1 || item==2 || item==13)
		{
			document.getElementById('cbo_uom').value= 12;
		}
		else if(item==3 || item==14)
		{
			document.getElementById('cbo_uom').value= 27;
		}
		else
		{
			document.getElementById('cbo_uom').value= 1;
		}
		
		if (item==1)
		{
			$('#txt_cone').removeAttr('disabled','disabled');	
		}
		else
		{
			$('#txt_cone').val('');
			$('#txt_cone').attr('disabled','disabled');
		}
	}
	
	
	/*function generate_report(type)
	{
		if ( $('#txt_return_no').val()=='')
		{
			alert ('Return ID Not Save.');
			return;
		}
		else
		{		
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_return_no').val()+'*'+report_title+'*'+type, "inventory_return_print", "requires/sub_contract_material_return_controller" ) 
			//return;
			show_msg("3");
		}
	}*/	
	
	function generate_report(type)
	{
		if ( $('#txt_return_no').val()=='')
		{
			alert ('Return ID Not Save.');
			return;
		}
		else
		{		
			var report_title=$( "div.form_caption" ).html();
			//print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_bill_no').val()+'*'+report_title+'*'+type, "fabric_finishing_print", "requires/sub_fabric_finishing_bill_issue_controller") 
			generate_report_file( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_return_no').val()+'*'+report_title+'*'+type+'*'+$('#cbo_location_name').val(), "inventory_return_print", "requires/sub_contract_material_return_controller");
			//return;
			show_msg("3");
		}
	}
	
	function generate_report_file(data,action,page)
	{
		window.open("requires/sub_contract_material_return_controller.php?data=" + data+'&action='+action, true );
	}
</script>
</head>
<body onLoad="set_hotkey();set_auto_complete_brand('brand_return');">
<div style="width:100%;" align="center">																										
	<? echo load_freeze_divs ("../",$permission);  ?>
    <fieldset style="width:1100px;">
    <legend>Sub-Contract Material Return</legend>
        <form name="materialreturn_1" id="materialreturn_1" autocomplete="off"> 
            <table  width="730" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td width="100" ><b>Return ID</b></td>
                    <td width="140"><input type="hidden" name="update_id" id="update_id">
                        <input class="text_boxes" type="text" name="txt_return_no" id="txt_return_no"  onDblClick="openmypage_return_id()"  placeholder="Double Click" style="width:130px;" readonly/>
                    </td>
                    <td  width="100" class="must_entry_caption">Company Name</td>
                    <td width="140"> 
						<? echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sub_contract_material_return_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/sub_contract_material_return_controller', this.value, 'load_drop_down_company_supplier', 'return_to_td' );"); ?>
                    </td>
                    <td width="100" >Location Name</td>
                    <td width="140" id="location_td">
						<? 
							echo create_drop_down( "cbo_location_name", 140, $blank_array,"", 1, "-- Select Location --", $selected, "" );
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Return To</td>
                    <td id="return_to_td">
						<?
							echo create_drop_down( "cbo_company_supplier", 140, $blank_array,"", 1, "-- Select Party --", $selected, "",0 );
                        ?> 
                    </td>
                    <td class="must_entry_caption">Return Date</td>
                    <td>
                        <input type="text" name="txt_return_date" id="txt_return_date"  class="datepicker" style="width:130px" />             
                    </td>
                    <td>Return Challan No</td>
                    <td>
                        <input type="text" name="txt_return_challan" id="txt_return_challan"  class="text_boxes" style="width:130px;" />
                    </td>
                </tr>
                <tr>
                    <td>Forwarder</td>
                    <td>
                         <?
                              echo create_drop_down( "cbo_forwarder", 140, "select b.id,b.supplier_name from lib_supplier_party_type a, lib_supplier b where b.id=a.supplier_id and a.party_type in (30,32) group by b.id,b.supplier_name","id,supplier_name", 1, "-- Select Forwarder --", $selected, "",0 );	
                         ?> 
                    </td>
                    <td>Transport Company</td>
                    <td>
                        <input type="text" name="txt_transport_company" id="txt_transport_company" class="text_boxes" placeholder="Transport Company" style="width:130px;" />
                    </td>
                </tr>
                <tr>
                    <td>Remarks</td>
                    <td colspan="3">
                        <input type="text" name="txt_remarks" id="txt_remarks"  class="text_boxes" style="width:375px;" maxlength="100" title="Maximum 100 Character"/>
                    </td>
                </tr>
            </table>
            <br/>
            <fieldset style="width:1070px;">
            <legend>Sub-Contract Return Details</legend>
                <div id="material_description_details_container" style="max-height:350px; overflow:auto;">          
                <table cellpadding="0" cellspacing="2" border="0" id="tbl_return_description" width="730">
                    <thead class="form_table_header">
                        <tr align="center" >
                            <th width="100" class="must_entry_caption">Order No</th>
                            <th width="100" >Item Category</th>
							<th width="60">Lot No.</th>
							<th width="60">Brand</th>
                            <th width="160"  class="must_entry_caption">Material Description</th>
                            <th width="100">Color</th>
                            <th width="100">GMTS Size</th>
                    		<th width="70">Dia</th>
                    		<th width="70">Fin Dia</th>
                            <th width="80" class="must_entry_caption">Return Qty</th>
                            <th width="60">UOM</th>
                            <th width="60">Roll/Bag</th>
                            <th>Cone</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="general" id="tr_1">
                            <td><input type="hidden" name="updateid_1" id="updateid_1">
                                <input type="hidden" name="order_no_id" id="order_no_id">
                                <input class="text_boxes" name="txt_orderno" id="txt_orderno" type="text" style="width:95px" placeholder="Browse or Write"  onDblClick= "job_search_popup()"/>	
                            </td>
                            <td>
								<?
									echo create_drop_down( "cbo_itemcategory", 100, $item_category,"", 1, "--Select Item--",$selected,"change_uom(this.value)", "","1,2,3,4,13,14,30" );
                                ?>
                            </td>
							<td>
                    			<input type="text" id="txt_lot_no" name="txt_lot_no" class="text_boxes" style="width:60px" readonly/>
                    		</td>
							<td>
                    			<input type="text" id="txt_brand" name="txt_brand" class="text_boxes txtbrand" style="width:60px" readonly/>
                    		</td>
                            <td>
                                <input type="text" name="txt_description" id="txt_description" class="text_boxes" style="width:150px"  onDblClick= "openmypage()" readonly= "readonly" placeholder="Double Click" /> 
                                <input type="hidden" name="description_id" id="description_id">
                                <input type="hidden" name="txt_rec_challan_no" id="txt_rec_challan_no">
                                <input type="hidden" name="txt_gsm" id="txt_gsm">
                                <input type="hidden" name="txt_color_id" id="txt_color_id">
                                <input type="hidden" name="txt_size_id" id="txt_size_id">
								<input type="hidden" name="txt_lot_no" id="txt_lot_no">
								<input type="hidden" name="txt_brand" id="txt_brand">
                            </td>
							
                            <td>
                        		<input name="txt_color_show" id="txt_color_show" class="text_boxes" type="text"  style="width:100px" readonly />
                    		</td>
		                    <td>
		                        <input name="txt_size_show" id="txt_size_show" class="text_boxes" type="text"  style="width:100px" readonly />
		                    </td>

                            <td>
                                <input name="txt_dia" id="txt_dia" class="text_boxes_numeric" type="text"  style="width:65px" readonly />
                            </td>
                            <td>
                            	<input type="text" name="txt_fin_dia" id="txt_fin_dia" class="text_boxes_numeric" style="width:65px" readonly>
                            </td>
                            <td>
                                 <input name="txt_quantity" id="txt_quantity" class="text_boxes_numeric" type="text"  style="width:75px" />
                            </td>
                            <td>
                                <? echo create_drop_down( "cbo_uom",60, $unit_of_measurement,"", 1, "-Select-",0,"", 1,"" );?>
                            </td>
                            <td>
                                 <input name="txt_roll" id="txt_roll" class="text_boxes_numeric" type="text"  style="width:55px" />
                            </td>
                            <td>
                                 <input name="txt_cone" id="txt_cone" class="text_boxes_numeric" type="text"  style="width:60px" disabled />
                            </td>
                        </tr>
                    </tbody>    
                </table> 
                </div>
                <table width="730" cellspacing="2" cellpadding="0" border="0">
                     <tr>
                          <td align="center" colspan="12" valign="middle" class="button_container">
							  <? echo load_submit_buttons($permission, "fnc_material_return", 0,0,"reset_form('materialreturn_1','material_return_list_view','','','')",1); ?>
                                <input type="button" name="search" id="search" value="With Gate Pass" onClick="generate_report(1)" style="width:100px" class="formbuttonplasminus" />
                                <input type="button" name="search" id="search" value="WithOut G.Pass" onClick="generate_report(2)" style="width:100px" class="formbuttonplasminus" />
                          </td>
                     </tr>           
                </table>
            </fieldset>  
            <br> 
            <fieldset style="width:750px;">
            <legend>Sub-Contract Material Return List View</legend>
                <div style="width:100%; margin-top:10px" id="material_return_list_view" align="center"></div>
            </fieldset>    
            </form>
        </fieldset> 
    </div>
</body> 
<script src="../includes/functions_bottom.js" type="text/javascript"></script>  
</html>

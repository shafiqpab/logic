<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Subcontract Material Issue					
Functionality	:	
JS Functions	:
Created by		:	Md. Abdul Hakim 
Creation date 	: 	10-04-2013
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
echo load_html_head_contents("Sub-Contract Material Issue Info", "../", 1,1, $unicode,'','');
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
	
	function openpageissue()
	{ 
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value;
		var page_link='requires/sub_contract_material_issue_controller.php?action=issue_popup&data='+data
		var title='Subcontract Issue';
		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=1000px, height=420px, center=1, resize=0, scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemail=this.contentDoc.getElementById("selected_issue");
			//alert (theemail.value);return;
			if (theemail.value!="")
			{
				freeze_window(5);
				get_php_form_data( theemail.value, "load_php_data_to_form", "requires/sub_contract_material_issue_controller" );
				show_list_view(theemail.value,'category_description_list_view','description_list_view','requires/sub_contract_material_issue_controller','setFilterGrid("list_view",-1)');
				set_button_status(0, permission, 'fnc_material_issue',1,1);	
				release_freezing();
			}
		}
	}

	

	function openmypage_item()
	{
		if ( form_validation('txtorderno_1*cboitemcategory_1','Order Name*Item Name')==false )
		{
			return;
		}
		else
		{	
			var data=document.getElementById('cboitemcategory_1').value+"_"+document.getElementById('hidden_description_id').value+"_"+document.getElementById('order_no_id').value;
			var page_link='requires/sub_contract_material_issue_controller.php?action=material_description_popup&data='+data;
			var title='Material Description Selection Form';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1110px,height=400px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{	
				var theemail=this.contentDoc.getElementById("description_id").value;
				var theemailval=this.contentDoc.getElementById("material_description").value;
				var theemaildia=this.contentDoc.getElementById("dia").value;
				var theemailchallan=this.contentDoc.getElementById("rec_challan").value;
				var theemailfindia=this.contentDoc.getElementById("fin_dia").value;
				var theemailcolor=this.contentDoc.getElementById("color").value;
				var theemailcolorName=this.contentDoc.getElementById("color_name").value;
				var theemailgsm=this.contentDoc.getElementById("gsm").value;
				var theeItemCatId=this.contentDoc.getElementById("item_category_id").value;
				var theeRecvQty=this.contentDoc.getElementById("recvQty").value;
				var theemailsize=this.contentDoc.getElementById("size").value;
				var theemailsizeName=this.contentDoc.getElementById("size_name").value;
				var theemailissue_balnce=this.contentDoc.getElementById("issue_balnce").value;
				var theemailusedYarnDtls=this.contentDoc.getElementById("usedYarnDtls").value;
				var theemailuom=this.contentDoc.getElementById("uom").value;
				var theemaillot=this.contentDoc.getElementById("lot_no").value;
				var theemailbrand=this.contentDoc.getElementById("brand").value;

				//alert (theemailusedYarnDtls);
				if(theemail!="")
				{
					$("#hidden_description_id").val(theemail);
					$("#txt_gsm").val(theemailgsm);
					if(theeItemCatId==2 || theeItemCatId==13)
					{
						if(theemailgsm!="")
							{ 
								$("#materialdescription_1").val(theemailval+','+theemailgsm); 
								$("#hidden_materialdescription").val(theemailval);
								
							} 
							else 
							{ 
								$("#materialdescription_1").val(theemailval);
								$("#hidden_materialdescription").val(theemailval);
							} 
					}
					else
					{
						$("#materialdescription_1").val(theemailval);
						$("#hidden_materialdescription").val(theemailval);
					}
					//$("#materialdescription_1").val(theemailval+','+theemailgsm);
					$("#txt_dia").val(theemaildia);
					$("#txt_rec_challan_no").val(theemailchallan);
					$("#txt_fin_dia").val(theemailfindia);
					$("#txt_fin_dia_show").val(theemailfindia);
					$("#txt_color_id").val(theemailcolor);
					$("#txt_color_show").val(theemailcolorName);
					$("#txt_size_id").val(theemailsize);
					$("#txt_size_show").val(theemailsizeName);
					$("#txt_lot_no").val(theemaillot);
					$("#txt_brand").val(theemailbrand);
					
					$("#txt_hidden_ord_rev_qty").val(theeRecvQty);
					$("#txt_hidden_checkRecvQty").val(theemailissue_balnce);
					$("#txt_used_yarn_details").val(theemailusedYarnDtls);
					$("#cbouom_1").val(theemailuom);
					
					var data_dtls=$("#order_no_id").val()+"_"+theemail;
					//get_php_form_data( data_dtls, "load_php_data_for_dtls", "requires/sub_contract_material_issue_controller" );
					freeze_window(5);
					release_freezing();
				}
			}
		}
	}

	function order_search_popup()
	{
		if ( form_validation('cbo_company_name*cbo_company_supplier','Company Name*Party Name')==false )
		{
			return;
		}
		else
		{
			var title="Order Id Popup"	
			var data=document.getElementById('txtorderno_1').value+"_"+document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('order_no_id').value;
			page_link='requires/sub_contract_material_issue_controller.php?action=order_popup&data='+data	
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=420px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("selected_order");
				//alert(theemail);
				get_php_form_data( theemail.value, "load_php_data_to_form_order", "requires/sub_contract_material_issue_controller" );
				//get_php_form_data( theemail.value, "load_php_data_ord_rev_qty", "requires/sub_contract_material_issue_controller" );
			}
		}
	}

	function fnc_material_issue( operation )
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_issue_no').val()+'*'+report_title+'*'+$('#cbo_location_name').val(), "material_issue_print", "requires/sub_contract_material_issue_controller") 
			//return;
			show_msg("3");
		}
		
		else if(operation==0 || operation==1 || operation==2)
		{
			
			if(operation==2)
			{
			var r=confirm("Press OK to Delete Or Press Cancel");
				if(r==false){
					return;
				}
		    }
			if ( form_validation('cbo_company_name*cbo_company_supplier*txt_issue_date*txtorderno_1*materialdescription_1*txtissuequantity_1','Company Name*Party Name*Date*Order No*Desciption*Quentity')==false )
			{
				return;
			}
			else
			{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_issue_no*cbo_company_name*cbo_location_name*cbo_source*cbo_company_supplier*txt_issue_date*txt_issue_challan*txt_remarks*update_id*txtorderno_1*cboitemcategory_1*materialdescription_1*txtissuequantity_1*cbouom_1*txt_roll*txt_cone*txt_dia*order_no_id*hidden_description_id*txt_rec_challan_no*txt_fin_dia*txt_color_id*txt_gsm*txt_lot_no*txt_brand*update_id_dtl*update_id*txt_size_id*hidden_materialdescription*txt_used_yarn_details',"../");
				freeze_window(operation);
				http.open("POST","requires/sub_contract_material_issue_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_material_issue_response;	
			}
		}
	}
	
	function fnc_material_issue_response()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);
			var response=trim(http.responseText).split('**');
			if(trim(response[0])=='17'){
				alert(response[1]);
				release_freezing();
				return;
			}
			else if(response[0]==0 || response[0]==1 )
			{
				document.getElementById('txt_issue_no').value = response[1];		
				document.getElementById('update_id').value = response[2];
				document.getElementById('update_id_dtl').value = response[3];
				show_msg(response[0]);
				show_list_view(response[2],'category_description_list_view','description_list_view','requires/sub_contract_material_issue_controller','setFilterGrid("list_view",-1)');		
				reset_form('','','cboitemcategory_1*materialdescription_1*hidden_description_id*txt_dia*txtissuequantity_1*cbouom_1*txt_roll*txt_cone','','');
				/*			$('#txt_dia').val('placeholder','');	
							$('#txtissuequantity_1').attr('placeholder','');
							$('#txt_roll').attr('placeholder','');
							$('#txt_cone').attr('placeholder','');
				*/			set_button_status(0, permission, 'fnc_material_issue',1,1);		
				release_freezing();
			}
			else if(response[0]==2)
			{
				document.getElementById('txt_issue_no').value = '';		
				document.getElementById('update_id').value = '';
				document.getElementById('update_id_dtl').value = '';
				show_msg(response[0]);
				show_list_view(response[2],'category_description_list_view','description_list_view','requires/sub_contract_material_issue_controller','setFilterGrid("list_view",-1)');		
				reset_form('','','cboitemcategory_1*materialdescription_1*hidden_description_id*txt_dia*txtissuequantity_1*cbouom_1*txt_roll*txt_cone','','');
				set_button_status(0, permission, 'fnc_material_issue',0,1);	
				release_freezing();
				//location.reload();
			}
			else if(response[0]==13)//Next process found.
			{
				show_msg(response[0]);
				alert(response[1]);
				release_freezing();
			}
			else if (response[0]==10)
			{
				show_msg(response[0]);
				release_freezing();
			}
		}
	}


	function change_uom(item)
	{
		if(item==1 || item==2 || item==13)
		{
			document.getElementById('cbouom_1').value= 12;
		}
		else if(item==3 || item==14)
		{
			document.getElementById('cbouom_1').value= 27;
		}
		else if(item==0)
		{
			document.getElementById('cbouom_1').value= 0;
		}
		else
		{
			document.getElementById('cbouom_1').value= 1;
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
	
	function check_revQty(inputValue)
	{
		//alert(inputValue);
		//var orderID=$('#order_no_id').val()*1;
		//get_php_form_data( orderID, "check_recvQnty", "requires/sub_contract_material_issue_controller" );

		if($("#txt_hidden_checkRecvQty").val()==""){ return;}

		var revQty=$("#txt_hidden_checkRecvQty").val()*1;
		var inputVal=inputValue*1;
		if(revQty>=inputVal)
		{
		}
		else
		{
			alert("Don't allow Issue Qnty is more than Balance Qnty. Your Balance Qnty = "+revQty);
			$('#txtissuequantity_1').val('');
			return;
		}
	}

	
</script>
</head>
<body onLoad="set_hotkey();set_auto_complete_brand('brand_return');">
    <div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",$permission);  ?>
    <fieldset style="width:1100px;">
    <legend>Sub-Contract Material Issue</legend>
    <form name="materialissue_1" id="materialissue_1" autocomplete="off"> 
        <table  width="800" cellspacing="2" cellpadding="0" border="0">
            <tr>
                <td  width="110" height="" >Issue ID</td>
                <td  width="140"><input type="hidden" name="update_id" id="update_id">
                    <input class="text_boxes"  type="text" name="txt_issue_no" id="txt_issue_no"  onDblClick="openpageissue('xx','Subcontract Issue')"  placeholder="Double Click" style="width:130px;" readonly/>
                </td>
                <td  width="120" class="must_entry_caption">Company Name</td>
                <td width="140"> 
                    <? 
                        echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sub_contract_material_issue_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/sub_contract_material_issue_controller', this.value, 'load_drop_down_issueto', 'issue_to_td' );"); ?>
                </td>
                <td width="110">Location Name</td>
                <td id="location_td" width="140">
                     <? echo create_drop_down( "cbo_location_name", 140, $blank_array,"", 1, "-- Select Location --", $selected, "" );?>
                </td>
            </tr>
            <tr>
                <td>Prod Source</td>
                <td>
                     <?
                       echo create_drop_down( "cbo_source", 140, $knitting_source,"", 1, "-- Select Source --", 1, "load_drop_down( 'requires/sub_contract_material_issue_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_company_supplier', 'issue_to_td' );",0,'1,3' );
                     ?> 
                </td>
                <td class="must_entry_caption">Issue To</td>
                <td id="issue_to_td">
                     <?
                        echo create_drop_down( "cbo_company_supplier", 140, $blank_array,"", 1, "-- Select Company --", $selected, "",0 );	
                     ?> 
                </td>
                <td class="must_entry_caption">Issue Date</td>
                <td>
                    <input type="text" name="txt_issue_date" id="txt_issue_date"  class="datepicker" style="width:130px" />           
                </td>
            </tr>
            <tr>
                <td >Issue Challan</td>
                <td>
                    <input type="text" name="txt_issue_challan" id="txt_issue_challan"  class="text_boxes" style="width:130px;" />
                </td>
                <td>Remarks</td>
                <td colspan="3">
                    <input type="text" name="txt_remarks" id="txt_remarks"  class="text_boxes" style="width:395px;" />
                </td>
            </tr>
        </table>
        <br/>
        <fieldset style="width:1020px;">
        <legend>Sub-Contract Material Issue Entry</legend>
        <div id="material_description_details_container" style="max-height:350px; overflow:auto;">          
        <table cellpadding="0" cellspacing="2" border="0" id="tbl_description" width="850">
            <thead class="form_table_header">
                <tr align="center" >
                    <th width="100" class="must_entry_caption">Order No</th>
                    <th width="120" class="must_entry_caption">Item Category</th>
					<th width="60">Lot No.</th>
					<th width="60">Brand</th>
                    <th width="150" class="must_entry_caption">Material Description</th>
                    <th width="100" >Color</th>
                    <th width="100" >GMTS Size</th>
                    <th width="60">Dia</th>
                    <th width="60" >Fin Dia</th>
                    <th width="80" class="must_entry_caption">Issue Qnty</th>
                    <th width="50">UOM</th>
                    <th width="60">Roll/Bag</th>
                    <th>Cone</th>
                </tr>
            </thead>
            <tbody>
                <tr id="tr_1">
                    <td >
                    <input class="text_boxes" name="txtorderno_1" id="txtorderno_1" type="text" style="width:95px" placeholder="Browse or Write"  autofocus onDblClick= "order_search_popup()"/>	
                    <input type="hidden" name="order_no_id" id="order_no_id">
                    </td>
                    <td>
                        <? echo create_drop_down( "cboitemcategory_1", 120, $item_category,"", 1, "--Select Item--",$selected,"change_uom(this.value); ", "","1,2,3,4,13,14,30" );?>
                    </td>
					<td>
                    	<input type="text" id="txt_lot_no" name="txt_lot_no" class="text_boxes" style="width:60px" readonly/>
                    </td>
					<td>
                    	<input type="text" id="txt_brand" name="txt_brand" class="text_boxes txtbrand" style="width:60px" readonly/>
                    </td>
                    <td>
                        <input type="text" name="materialdescription_1" id="materialdescription_1" class="text_boxes" style="width:140px"  onDblClick= "openmypage_item()" readonly= "readonly" placeholder="Double Click" />

                        <input type="hidden" name="hidden_materialdescription" id="hidden_materialdescription" class="text_boxes" style="width:140px"  onDblClick= "" readonly= "readonly" />

                        <input type="hidden" name="hidden_description_id" id="hidden_description_id">
                        <input type="hidden" name="txt_rec_challan_no" id="txt_rec_challan_no">
                        <input type="hidden" name="txt_gsm" id="txt_gsm">
                        <input type="hidden" name="txt_fin_dia" id="txt_fin_dia">
                        <input type="hidden" name="txt_color_id" id="txt_color_id">
                        <input type="hidden" name="txt_hidden_ord_rev_qty" id="txt_hidden_ord_rev_qty">
                   		<input type="hidden" name="txt_hidden_checkRecvQty" id="txt_hidden_checkRecvQty">
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
                        <input name="txt_dia" id="txt_dia" class="text_boxes_numeric" type="text"  style="width:60px" readonly />
                    </td>
                     <td>
                        <input name="txt_fin_dia_show" id="txt_fin_dia_show" class="text_boxes_numeric" type="text"  style="width:60px" readonly />
                    </td>
                    <td>
                        <input name="txtissuequantity_1" id="txtissuequantity_1" class="text_boxes_numeric" type="text"  style="width:80px" ;/>
                    </td>
                    <td>
                        <? echo create_drop_down( "cbouom_1",50, $unit_of_measurement,"", 1, "-Select-",0,"", 1,"" );?>
                    </td>
                    <td>
                        <input name="txt_roll" id="txt_roll" class="text_boxes_numeric" type="text"  style="width:60px" />
                    </td>
                    <td>
                         <input name="txt_cone" id="txt_cone" class="text_boxes_numeric" type="text"  style="width:60px" disabled />
                    </td>

                    <textarea name="txt_used_yarn_details" id="txt_used_yarn_details" class="text_boxes" type="text"  style="width:240px;display:none;"></textarea>

                </tr>
            </tbody>    
        </table> 
        </div>
        <table width="800" cellspacing="2" cellpadding="0" border="0">
             <tr>
                  <td align="center" colspan="12" valign="middle" class="button_container">
                  <? echo load_submit_buttons($permission, "fnc_material_issue", 0,1,"reset_form('materialissue_1','description_list_view','','','disable_enable_fields(\'cbo_company_name\',0)')",1); ?>
                  <input type="hidden" name="update_id_dtl" id="update_id_dtl">
                  <input type="hidden" name="hidden_selectedID" id="hidden_selectedID" readonly= "readonly" />
                  </td>
             </tr>           
        </table>
        </fieldset>  
        <br> 
        <fieldset style="width:800px;">
        <legend>Sub-Contract Order Material List View</legend>
            <div style="width:100%; margin-top:10px" id="description_list_view" align="center"></div>
        </fieldset>    
    </form>
    </fieldset> 
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
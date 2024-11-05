<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create SubCon Garments Delivery Entry
				
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	08-12-2014
Purpose			:
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
echo load_html_head_contents("Garments Delivery Info","../../", 1, 1, $unicode,'','');

?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	function openmypage_order(page_link,title)
	{
		//alert (page_link)
		var company = $("#cbo_company_id").val();
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=400px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var po_id=this.contentDoc.getElementById("hidden_order_id").value;//po id
			var item_id=this.contentDoc.getElementById("hidden_item_id").value; 
			//var po_qnty=this.contentDoc.getElementById("hidden_order_qty").value;
			//alert (po_id);
			
			if (po_id!="")
			{
				freeze_window(5);
				
				childFormReset();//child from reset
				get_php_form_data(po_id+'**'+item_id, "populate_data_from_search_popup", "requires/subcon_gmts_delivery_controller" );
				
				var variableSettings=$('#sewing_production_variable').val();
				var styleOrOrderWisw=$('#styleOrOrderWisw').val();
				var cbo_process_name=$('#cbo_process_name').val();
				if(variableSettings!=1) 
				{ 
					get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+cbo_process_name, "color_and_size_level", "requires/subcon_gmts_delivery_controller" ); 
				}
				else
				{
					$("#txt_delivery_qty").removeAttr("readonly");
				}
				//show_list_view(po_id+'**'+item_id,'show_dtls_listview','delivery_list_view','requires/subcon_gmts_delivery_controller','');
				//show_list_view(po_id,'show_country_listview','list_view_country','requires/subcon_gmts_delivery_controller','');
				set_button_status(0, permission, 'fnc_gmts_delivery',1,0);
				release_freezing();
			}
		}
	}

	function fnc_gmts_delivery(operation)
	{
		if(operation==4)
		{
			 var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_id').val()+'*'+$('#txt_sys_id').val()+'*'+report_title, "gmts_delivery_print", "requires/subcon_gmts_delivery_controller" ) 
			 return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if ( form_validation('cbo_company_id*txt_order_no*txt_delivery_qty*txt_delivery_date','Company Name*Order No*Delivery Quantity*Delivery Date')==false )
				{
				return;
				}		
			
				var sewing_production_variable = $("#sewing_production_variable").val();
				//alert(sewing_production_variable);return;
				var colorList = ($('#hidden_colorSizeID').val()).split(",");
				//alert(colorList);
				var i=0;var colorIDvalue='';
				if(sewing_production_variable==2)//color level
				{
					$("input[name=txt_color]").each(function(index, element) {
						if( $(this).val()!='' )
						{
							if(i==0)
							{
								colorIDvalue = colorList[i]+"*"+$(this).val();
							}
							else
							{
								colorIDvalue += "**"+colorList[i]+"*"+$(this).val();
							}
						}
						i++;
					});
				}
				else if(sewing_production_variable==3)//color and size level
				{	
					$("input[name=colorSize]").each(function(index, element) {
						if( $(this).val()!='' )
						{
							if(i==0)
							{
								colorIDvalue = colorList[i]+"*"+$(this).val();
							}
							else
							{
								colorIDvalue += "***"+colorList[i]+"*"+$(this).val();
							}
							//alert( $(this).val() );return;
							
						}
						i++;
					});
				}
				
				//var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+get_submitted_data_string('cbo_company_id*cbo_location',"../");
				var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+get_submitted_data_string('cbo_company_id*sewing_production_variable*cbo_location_name*cbo_party_name*cbo_process_name*cbo_item_name*hidden_po_break_down_id*hidden_colorSizeID*txt_delivery_date*txt_delivery_qty*txt_total_carton_qnty*txt_challan_no*txt_ctn_qnty*txt_transport_company*txt_vehical_no*txt_prod_quantity*txt_cumul_quantity*txt_yet_quantity*txt_update_id*txt_sys_id*cbo_forwarder*txt_mst_id*txt_dtls_id',"../../");
				// alert (data); return;
				freeze_window(operation);
				http.open("POST","requires/subcon_gmts_delivery_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_gmts_delivery_Reply;
			//}
		}
	}
  
	function fnc_gmts_delivery_Reply()
	{
		if(http.readyState == 4) 
		{
			//release_freezing();
			//alert(http.responseText);return;
			var variableSettings=$('#sewing_production_variable').val();
			var styleOrOrderWisw=$('#styleOrOrderWisw').val();
			var item_id=$('#cbo_item_name').val();
			
			var reponse=http.responseText.split('**');
			
			if(reponse[0]==50)
			{
				release_freezing();
				alert("Buyer Mixed Not Allow");return;
			}
			if(reponse[0]==15) 
			{ 
				 setTimeout('fnc_gmts_delivery('+ reponse[1]+')',8000); 
			}
			else if(reponse[0]==0)
			{
				//var po_id = reponse[1];
				show_msg(trim(reponse[0]));
				
				$("#txt_update_id").val(reponse[2]);
				$("#txt_sys_id").val(reponse[3]);
				$("#txt_challan_no").val(reponse[4]);
				
				show_list_view(reponse[2],'delivery_list_view','delivery_list_view','requires/subcon_gmts_delivery_controller','');
				setFilterGrid("details_table",-1);
				//setFilterGrid("details_table",-1);
				//show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id,'show_dtls_listview','delivery_list_view','requires/subcon_gmts_delivery_controller','');		
				reset_form('','breakdown_td_id','txt_order_no*txt_delivery_qty*hidden_break_down_html*hidden_colorSizeID*sewing_production_variable*txt_total_carton_qnty*txt_ctn_qnty*cbo_process_name*txt_prod_quantity*txt_cumul_quantity*txt_yet_quantity*txt_job_no*txt_order_qty*cbo_item_name','','');
				$('#cbo_party_name').attr('disabled','disabled');
				set_button_status(0, permission, 'fnc_gmts_delivery',1,1);
				release_freezing();
			} 
			else if(reponse[0]==1)
			{
				//var po_id = reponse[1];
				
				var po_id=$("#hidden_po_break_down_id").val();
				
				//$("#txt_sys_id").val(reponse[2]);
				//$("#txt_challan_no").val(reponse[3]);
				show_msg(trim(reponse[0]));
				
				//show_list_view(reponse[1],'show_dtls_listview_mst','delivery_list_view','requires/subcon_gmts_delivery_controller','');
				show_list_view(reponse[2],'delivery_list_view','delivery_list_view','requires/subcon_gmts_delivery_controller','');
				setFilterGrid("details_table",-1);
				//show_list_view(po_id+'**'+$("#cbo_item_name").val(),'show_dtls_listview','delivery_list_view','requires/subcon_gmts_delivery_controller','');		
				reset_form('','breakdown_td_id','txt_order_no*txt_delivery_qty*hidden_break_down_html*hidden_colorSizeID*txt_total_carton_qnty*sewing_production_variable*txt_ctn_qnty*cbo_process_name*txt_prod_quantity*txt_cumul_quantity*txt_yet_quantity*txt_job_no*txt_order_qty*cbo_item_name','','');
				$('#cbo_party_name').attr('disabled','disabled');
				set_button_status(0, permission, 'fnc_gmts_delivery',1,1);
				release_freezing();
			}
			else if(reponse[0]==2)
			{
				
				show_msg(trim(reponse[0]));
				show_list_view(reponse[2],'delivery_list_view','delivery_list_view','requires/subcon_gmts_delivery_controller','');
				setFilterGrid("details_table",-1);
				//show_list_view(po_id+'**'+$("#cbo_item_name").val()+'**'+country_id,'show_dtls_listview','delivery_list_view','requires/subcon_gmts_delivery_controller','');		
				reset_form('','breakdown_td_id','txt_order_no*txt_delivery_qty*hidden_break_down_html*hidden_colorSizeID*txt_total_carton_qnty*txt_ctn_qnty*cbo_process_name*txt_prod_quantity*txt_cumul_quantity*txt_yet_quantity*txt_job_no*txt_order_qty*cbo_item_name','','');
				$('#cbo_party_name').attr('disabled','disabled');
				set_button_status(0, permission, 'fnc_gmts_delivery',1,1);
				release_freezing();
			}
			release_freezing();
		}
	} 

	function childFormReset()
	{
		reset_form('','','hidden_break_down_html*hidden_colorSizeID*txt_total_carton_qnty*txt_ctn_qnty*txt_transport_company*txt_prod_quantity*txt_cumul_quantity*txt_yet_quantity','','');
		$('#txt_delivery_qty').attr('placeholder','');//placeholder value initilize
		$('#txt_prod_quantity').attr('placeholder','');//placeholder value initilize
		$('#txt_cumul_quantity').attr('placeholder','');//placeholder value initilize
		$('#txt_yet_quantity').attr('placeholder','');//placeholder value initilize delivery_list_view
		//$("#delivery_list_view").html('');
		$("#breakdown_td_id").html('');
	}

	function fn_qnty_per_ctn()
	{
		 var exQnty = $('#txt_delivery_qty').val();
		 var ctnQnty = $('#txt_total_carton_qnty').val();
		  
		 if(exQnty!="" && ctnQnty!="")
		 {
			 var ctn_per_qnty = parseInt( Number( exQnty/ctnQnty ) );
			 $('#txt_ctn_qnty').val(ctn_per_qnty);
		 }
	 }
  
	function fn_total(tableName,index) // for color and size level
	{
	 
		var filed_value = $("#colSize_"+tableName+index).val();
		// alert(filed_value);
		var placeholder_value = $("#colSize_"+tableName+index).attr('placeholder');
		if(filed_value*1 > placeholder_value*1)
		{
			 alert("Qnty Excceded by"+(placeholder_value-filed_value))	
				
				$("#colSize_"+tableName+index).val('');
				return;
			
		}
		
		var totalRow = $("#table_"+tableName+" tr").length;
		//alert(tableName);
		math_operation( "total_"+tableName, "colSize_"+tableName, "+", totalRow);
		if($("#total_"+tableName).val()*1!=0)
		{
			$("#total_"+tableName).html($("#total_"+tableName).val());
		}
		var totalVal = 0;
		$("input[name=colorSize]").each(function(index, element) {
			totalVal += ( $(this).val() )*1;
		});
		$("#txt_delivery_qty").val(totalVal);
	}

	function fn_colorlevel_total(index) //for color level
	{
		var filed_value = $("#colSize_"+index).val();
		var placeholder_value = $("#colSize_"+index).attr('placeholder');
		if(filed_value*1 > placeholder_value*1)
		{
			alert("Qnty Excceded by"+(placeholder_value-filed_value)) 	
			$("#colSize_"+index).val('');
			return;
		}
		
		var totalRow = $("#table_color tbody tr").length;
		//alert(totalRow);
		math_operation( "total_color", "colSize_", "+", totalRow);
		$("#txt_delivery_qty").val( $("#total_color").val() );
	} 

	function openpage_id()
	{ 
		if ( form_validation('cbo_company_id','Company Name')==false ) { return; }
		var data=document.getElementById('cbo_company_id').value;
		var page_link='requires/subcon_gmts_delivery_controller.php?action=delivery_id_popup&data='+data
		var title='Subcontract Delivery';
		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=700px, height=400px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemail=this.contentDoc.getElementById("selected_delivery_id");
			//alert (theemail.value);return;
			if (theemail.value!="")
			{
				//var ret_value=theemail.value.split("_");
				freeze_window(5);
				get_php_form_data( theemail.value, "load_php_data_to_form", "requires/subcon_gmts_delivery_controller" );
				show_list_view(theemail.value,'delivery_list_view','delivery_list_view','requires/subcon_gmts_delivery_controller','');
				setFilterGrid("details_table",-1);
				reset_form('','breakdown_td_id','txt_order_no*txt_delivery_qty*hidden_break_down_html*hidden_colorSizeID*txt_total_carton_qnty*sewing_production_variable*txt_ctn_qnty*cbo_process_name*txt_prod_quantity*txt_cumul_quantity*txt_yet_quantity*txt_job_no*txt_order_qty*cbo_item_name','','');
				set_button_status(0, permission, 'fnc_gmts_delivery',1,0);
				
				release_freezing();
			}
		}
	}
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;">
	<?  echo load_freeze_divs ("../../",$permission);  ?>
    <div style="width:930px; float:left" align="center">
        <form name="gmtsDelv_1" id="gmtsDelv_1" autocomplete="off" >     
        <fieldset style="width:830px;">
            <legend>Garments Delivery</legend>
                <fieldset>                                       
                 <table width="100%">
                    <tr>
                        <td align="right" colspan="3"><strong>System ID</strong></td>
                        <td width="140" align="justify">
                            <input type="hidden" name="txt_update_id" id="txt_update_id" />
                            <input type="text" name="txt_sys_id" id="txt_sys_id" class="text_boxes" style="width:140px" placeholder="Double Click" onDblClick="openpage_id();" readonly >
                        </td>
                    </tr>
                     <tr>
                        <td width="100" class="must_entry_caption">Company </td>
                        <td width="140">
                            <?
                                echo create_drop_down( "cbo_company_id", 152, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/subcon_gmts_delivery_controller', this.value, 'load_drop_down_location', 'location_td' ); load_drop_down( 'requires/subcon_gmts_delivery_controller', this.value, 'load_drop_down_party_name', 'party_td' );",0 );	
                            ?>
                        </td>
                        <td width="100">Location</td>
                        <td width="140" id="location_td">
                             <?
                            echo create_drop_down( "cbo_location_name", 152, $blank_array,"", 1, "-- Select Location --", $selected, "",0 );	
                             ?> 
                        </td>
                        <td width="100" class="must_entry_caption">Party</td>
                        <td id="party_td">
                            <?
                            echo create_drop_down( "cbo_party_name", 152,  $blank_array,"", 1, "-- Select Party --", $selected, "",'' ); 
                            ?>
                        </td>
                     </tr>
                    <tr>
                        <td>Challan No</td>
                        <td>
                            <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes_numeric" style="width:140px;" placeholder="Write Or Auto Create" />
                        </td>
                        <td class="must_entry_caption">Delivery Date</td>
                        <td>
                            <input type="text" name="txt_delivery_date" id="txt_delivery_date" class="datepicker" style="width:140px;" placeholder="Date" readonly />
                        </td>
                        <td>Forwarder</td>
                        <td>
                             <?
                                  echo create_drop_down( "cbo_forwarder", 152, "select b.id,b.supplier_name from lib_supplier_party_type a, lib_supplier b where b.id=a.supplier_id and a.party_type in (30,32) group by b.id,b.supplier_name","id,supplier_name", 1, "-- Select Forwarder --", $selected, "",0 );	
                             ?> 
                        </td>
                    </tr>
                    <tr>
                        <td>Transport Com.</td>
                        <td>
                            <input type="text" name="txt_transport_company" id="txt_transport_company" class="text_boxes" placeholder="Transport Company" style="width:140px;" />
                        </td>
                        <td>Vehicle No</td>
                        <td>
                        	<input type="text" name="txt_vehical_no" id="txt_vehical_no" class="text_boxes" placeholder="Vehicle No" style="width:140px;" />
                        </td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                 </table>
                </fieldset>
                <br /> 
                <table cellpadding="0" cellspacing="1" width="100%">
                    <tr>
                        <td width="30%" valign="top">
                          <fieldset>
                          <legend>New Entry</legend>
                            <table  cellpadding="0" cellspacing="2" width="100%">
                                <tr>
                                    <td width="110" class="must_entry_caption">Order No</td>
                                    <td><input name="txt_order_no" id="txt_order_no"  placeholder="Double Click" onDblClick="openmypage_order('requires/subcon_gmts_delivery_controller.php?action=order_popup&company='+document.getElementById('cbo_company_id').value+'&cbo_party_name='+document.getElementById('cbo_party_name').value,'Order Search')" class="text_boxes" style="width:100px " readonly />
                                    <input type="hidden" id="hidden_po_break_down_id" />
                                    <input type="hidden" name="txt_dtls_id" id="txt_dtls_id" readonly >
                                    </td>
                                </tr>
                                <tr>
                                    <td class="must_entry_caption">Delivery Qty</td>
                                    <td>
                                        <input name="txt_delivery_qty" id="txt_delivery_qty" class="text_boxes_numeric" type="text"  style="width:100px;" readonly />
                                        <input type="hidden" id="hidden_break_down_html"  value="" readonly />
                                        <input type="hidden" id="hidden_colorSizeID"  value="" readonly />
                                        
                                    </td>
                                </tr>
                                <tr>
                                    <td>Total Carton Qty</td>
                                    <td>
                                       <input name="txt_total_carton_qnty" id="txt_total_carton_qnty" type="text" class="text_boxes_numeric"  style="width:100px" onKeyUp="fn_qnty_per_ctn()" />
                                    </td>
                                </tr>
                                <tr>
                                    <td> Qty/Ctn(Pcs/Set)</td>
                                    <td> 
                                         <input name="txt_ctn_qnty" id="txt_ctn_qnty" class="text_boxes_numeric"  style="width:100px" readonly />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Process</td>
                                    <td> 
                                         <?
                                       		echo create_drop_down( "cbo_process_name", 112, $production_process,"", 1, "-Select Process-", $selected, "get_php_form_data(document.getElementById('cbo_company_id').value+'_'+this.value,'load_variable_settings','requires/subcon_gmts_delivery_controller');",1,0 );	
                                        ?>
                                            <input type="hidden" name="sewing_production_variable" id="sewing_production_variable" />
                            				<input type="hidden" id="styleOrOrderWisw" />
                                    </td>
                                </tr>
                           </table>
                        </fieldset>
                    </td>
                    <td width="1%" valign="top"></td>
                    <td width="28%" valign="top">
                          <fieldset>
                          <legend>Display</legend>
                              <table cellpadding="0" cellspacing="2" width="100%" >
                                <tr>
                                    <td width="110">Production Qty</td>
                                    <td>
                                    	<input name="txt_prod_quantity" id="txt_prod_quantity"  class="text_boxes_numeric" type="text" style="width:100px" readonly />
                                    </td>
                                </tr> 
                                <tr>
                                    <td>Cuml. Delv. Qty</td>
                                    <td>
                                    	<input type="text" name="txt_cumul_quantity" id="txt_cumul_quantity" class="text_boxes_numeric"  style="width:100px" readonly />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Yet to Delv. Qty</td>
                                    <td>
                                    	<input type="text" name="txt_yet_quantity" id="txt_yet_quantity" class="text_boxes_numeric"  style="width:100px" readonly />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Job No.</td>
                                    <td>	
                                    	<input name="txt_job_no" id="txt_job_no" style="width:100px;" type="text" class="text_boxes" readonly  />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Order Qty.</td>
                                    <td>
                                    	<input class="text_boxes"  name="txt_order_qty" id="txt_order_qty" type="text" style="width:100px;" readonly/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Item Name</td>   
                                    <td >
										<?
                                       		echo create_drop_down( "cbo_item_name", 112, $garments_item,"", 1, "-- Select Item --", $selected, "",1,0 );	
                                        ?>
                                    </td>
                                </tr>
                               </table>
                          </fieldset>
                      </td>
                    <td width="41%" valign="top">
                        <div style="max-height:330px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                    </td>    
                </tr>
                </table>
                <br />
                <table cellpadding="0" cellspacing="1" width="100%">
                    <tr>
                        <td align="center" colspan="6" valign="middle" class="button_container">
                             <? 
                                echo load_submit_buttons( $permission, "fnc_gmts_delivery", 0,1,"reset_form('gmtsDelv_1','delivery_list_view','','','childFormReset()')",1);
                            ?>
                             <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly >
                        </td>
                    </tr> 
                </table>
               
           </fieldset>
        </form>
         <div style="width:830px; margin-top:5px;" id="delivery_list_view" align="center"></div>
    </div>
	<div id="list_view_country" style="width:388px;float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>   
</div>
</body> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>  
</html>
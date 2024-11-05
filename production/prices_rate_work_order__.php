<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Prices Rate Work Order

Functionality	:	
JS Functions	:
Created by		:	Saidul Reza
Creation date 	: 	21.10.2014
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
echo load_html_head_contents("Finish Production Entry Info","../", 1, 1, "",'1','');
?>

<script>

	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	
	function set_exchange_rate(currence)
	{	// 1 for TK.
		if(currence==1)
		{
			$('#txt_exchange_rate').val(1);
			$('#txt_exchange_rate').attr('readonly', 1);
		}
		else
		{
			$('#txt_exchange_rate').val('');
			$('#txt_exchange_rate').removeAttr("readonly");
		}
	}
	
	
	
	function openmypage_systemid()
	{ 
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var cbo_company_id = $('#cbo_company_id').val();
			var title = 'System ID Info';
			var page_link = 'requires/piece_rate_work_order_controller.php?cbo_company_id='+cbo_company_id+'&action=systemId_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				//$('#cbo_company_id').attr('disabled','disabled');
				var theform=this.contentDoc.forms[0];
				var mst_id=this.contentDoc.getElementById("hidden_mst_id").value;
				//alert(mst_id);
				get_php_form_data(mst_id, "populate_price_rat_mst_form_data", "requires/piece_rate_work_order_controller" );
				show_list_view(mst_id,'show_price_rate_wo_listview','list_container_price_rate_wo','requires/piece_rate_work_order_controller','');
				
				show_list_view(mst_id, 'load_details_entry_single', 'details_entry_list_view', 'requires/piece_rate_work_order_controller', '');
				set_button_status(1, permission, 'fnc_prices_rate_wo',1);
				
			}
		}
	}
	
	
	
	
	
	function openmypage_service_provider()
	{
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var cbo_company_id = $('#cbo_company_id').val();
			var title = 'Service Provider Info';
			var page_link = 'requires/piece_rate_work_order_controller.php?cbo_company_id='+cbo_company_id+'&action=service_provider_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var supplier_id=this.contentDoc.getElementById("hidden_supplier_id").value;
				var sp=supplier_id.split("__");
				$('#txt_service_provider_id').val(sp[0]);
				$('#txt_service_provider_name').val(sp[1]);
			}
		}
	}
	
	
	
	
	
	function openmypage_job_no(str)
	{		
			
		if (form_validation('cbo_company_id*cbo_order_source_'+str,'Company*Order Source')==false)
		{
			return;
		}
		else
		{
			var order_source=$('#cbo_order_source_'+str).val();
			var ord_rceve_comp=$('#cbo_ord_rceve_comp_id_'+str).val();
			var txt_history=$('#txt_history').val();
			var mst_id=$('#update_id').val();
			var details_update_id=job_id=order_id=buyer_id=item_id=job_no=0;

			rowCount = $('#details_entry_list_view tr').length;
			for(i=1; i <= rowCount; i++)
			{ 
				if(i==1)
				{
					if($('#details_update_id_'+i).val())
					{
					details_update_id=$('#details_update_id_'+i).val();
					job_no=$('#txtjobno_'+i).val();
					job_id=$('#txtjobid_'+i).val();
					order_id=$('#txtorderid_'+i).val();
					buyer_id=$('#txtbuyerid_'+i).val();
					item_id=$('#txtitemid_'+i).val();
					}
					
						
				}
				else
				{
					if($('#details_update_id_'+i).val())
					{
					details_update_id+=','+$('#details_update_id_'+i).val();
					
					job_no+=','+$('#txtjobno_'+i).val();
					job_id+=','+$('#txtjobid_'+i).val();
					order_id+=','+$('#txtorderid_'+i).val();
					buyer_id+=','+$('#txtbuyerid_'+i).val();
					item_id+=','+$('#txtitemid_'+i).val();
					}
				}
				
			}
			var cbo_company_id = $('#cbo_company_id').val();
			var title = 'Job Number Info';
			var page_link = 'requires/piece_rate_work_order_controller.php?odr_source='+order_source+'&job_id='+job_no+'&order_id='+order_id+'&buyer_id='+buyer_id+'&item_id='+item_id+'&txt_history='+txt_history+'&cbo_company_id='+cbo_company_id+'&mst_id='+mst_id+'&action=job_no_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				$('#cbo_company_id').attr('disabled','disabled');
				var theform=this.contentDoc.forms[0];
				var job_data=this.contentDoc.getElementById("txt_selected_id").value;
				show_list_view( job_data+'_~_'+order_source+'_~_'+ord_rceve_comp+'_~_'+mst_id+'_~_'+details_update_id+'_~_'+job_id,'load_details_entry','details_entry_list_view', 'requires/piece_rate_work_order_controller', '');	
				if(mst_id!=""){set_button_status(1, permission, 'fnc_prices_rate_wo',1);}
			}
		}
	}
	
	
	function openmypage_wo_qty(str)
	{
			
		if (form_validation('txtjobno_'+str+'*cbo_rate_for','Cob Number*Rate For')==false)
		{
			return;
		}
		else
		{
			var order_source=$('#cbo_order_source_'+str).val();
			var txt_job_no 		= $('#txtjobno_'+str).val();
			var txt_order_no 	= $('#txtorderid_'+str).val();//txtorderno_
			var txt_order_number 	= $('#txtorderno_'+str).val();//
			var txt_buyer 		= $('#txtbuyer_'+str).val();
			var txt_item 		= $('#txtitem_'+str).val();
			var txt_item_id 	= $('#txtitemid_'+str).val();
			var txt_style 		= $('#txtstyle_'+str).val();
			
			var details_update_id = $('#details_update_id_'+str).val();
			var txtjobid 		  = $('#txtjobid_'+str).val();
			var cbo_rate_for 	  = $('#cbo_rate_for').val();
			var cbo_company_id 	  = $('#cbo_company_id').val();
			
			var search_history 	  = $('#txt_order_qty_history_'+str).val();
			
			var data=txt_job_no+'__'+txt_order_no+'__'+txt_buyer+'__'+txt_item_id+'__'+txt_item+'__'+txt_style+'__'+cbo_rate_for+'__'+txtjobid+'__'+details_update_id+'__'+cbo_company_id+'__'+txt_order_number;

			var title = 'Work Order Qty';
			var page_link = 'requires/piece_rate_work_order_controller.php?o_source='+order_source+'&search_history='+search_history+'&data='+data+'&action=wo_qty_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width=880px,height=400px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				
				var hidden_qty=this.contentDoc.getElementById("hidden_qty").value;
				var hidden_rate=this.contentDoc.getElementById("hidden_rate").value;
				var hidden_uom=this.contentDoc.getElementById("hidden_uom").value;
				var hidden_color=this.contentDoc.getElementById("hidden_color").value;
				var hidden_size=this.contentDoc.getElementById("hidden_size").value;
				var hidden_oqty=this.contentDoc.getElementById("hidden_oqty").value;
				var hidden_wo_qty_uom=this.contentDoc.getElementById("hidden_wo_qty_uom").value;
				
				var hidden_up_ids=this.contentDoc.getElementById("hidden_up_ids").value;
				
				
				var search_history=this.contentDoc.getElementById("hidden_search_history").value;
				$("#txt_order_qty_history_"+str).val(search_history+'~~'+hidden_oqty+'~~'+hidden_qty+'~~'+hidden_rate+'~~'+hidden_uom+'~~'+hidden_wo_qty_uom+'~~'+hidden_color+'~~'+hidden_size+'~~'+hidden_up_ids);

				//var spq=hidden_wo_qty_uom.split(",");
				var spq=hidden_qty.split(",");
				var spr=hidden_rate.split(",");
				var suom=hidden_uom.split(",");
				
				var total_qty=amount=uom=0;
				for(i=0;i<spq.length; i++ )
				{
				total_qty+=spq[i]*1;	
				amount+=(spq[i]*1)*(spr[i]*1);	
				uom=suom[i];
				}
				if(uom==2) var divide_by=12; else var divide_by=1;
				var avarage=(amount/total_qty);		
				avarage=avarage.toFixed(2);
				var avg_oq=Math.round(total_qty/divide_by);
				$("#txtwoqty_"+str).val(avg_oq);
				$("#txtavgrate_"+str).val(avarage);
				$("#txtdtlamount_"+str).val(avarage*avg_oq);
				$("#cbodtlsuom_"+str).val(suom[0]);
			}
		}
	}
	
	
	
	function generate_report_file(data,action)
	{
		window.open("requires/piece_rate_work_order_controller.php?data=" + data+'&action='+action, true );
	}
	
	function fnc_prices_rate_wo( operation )
	{ 
		rowCount = $('#details_entry_list_view tr').length;
		$("#tot_rows").val(rowCount);
		
		
		if(operation==4)
		{
			// var report_title=$( "div.form_caption" ).html();
			 generate_report_file($('#update_id').val(),'price_rate_wo_print');
			 return;
		}
			
		
		
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		var fill_txt='update_id*txt_system_id*cbo_company_id*txt_service_provider_id*txt_wo_date*cbo_rate_for*txt_attention*cbo_currency*txt_exchange_rate*cbo_location*txt_remarks_mst*tot_rows';
		var validation_fill_order_source='';
		var validation_fill_wo_order_qty='';
		var tot_rows=$("#tot_rows").val()*1;
		for(i=1; i<= tot_rows; i++)
		{ 
			fill_txt+="*txt_order_qty_history_"+i+"*cbo_order_source_"+i+"*cbo_ord_rceve_comp_id_"+i+"*txtjobid_"+i+"*txtorderid_"+i+"*txtbuyerid_"+i+"*txtitemid_"+i+"*txtstyle_"+i+"*colortype_"+i+"*txtwoqty_"+i+"*cbodtlsuom_"+i+"*txtavgrate_"+i+"*txtdtlamount_"+i+"*txtremarks_"+i+"*details_update_id_"+i;
			validation_fill_order_source+="*cbo_order_source_"+i+"*cbo_ord_rceve_comp_id_"+i+"*colortype_"+i;
			if(validation_fill_wo_order_qty==''){validation_fill_wo_order_qty="txtwoqty_"+i;} else {validation_fill_wo_order_qty+="*txtwoqty_"+i;}
		}
		
		// alert(validation_fill_order_source);
		if( form_validation('cbo_company_id*txt_wo_date*cbo_rate_for*cbo_currency*txt_exchange_rate'+validation_fill_order_source,'company*production date*rate for*currency*exchange rate*Order Source*Order Receiving Company*Rate Variables')==false )
		{
			return;
		}	
		else if( form_validation(validation_fill_wo_order_qty,'WO Qty')==false )
		{
			if(confirm("System will not save zero or blank wo qty.")==0)return;	
		}
		
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(fill_txt,"../");
		
		//alert (data);return;
	  freeze_window(operation);
	  http.open("POST","requires/piece_rate_work_order_controller.php",true);
	  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	  http.send(data);
	  http.onreadystatechange = fnc_prices_rate_wo_reponse;
	}
	
	
	function fnc_prices_rate_wo_reponse()
	{
		if(http.readyState == 4) 
		{
			    // release_freezing();alert(http.responseText);return;
			var reponse=trim(http.responseText).split('**');
			  //alert(reponse[0]); release_freezing();return;
			show_msg(reponse[0]);
			
			if((reponse[0]==0 || reponse[0]==1))
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_system_id').value = reponse[2];
				show_list_view(reponse[1],'show_price_rate_wo_listview','list_container_price_rate_wo','requires/piece_rate_work_order_controller','');
				show_list_view(reponse[1], 'load_details_entry_single', 'details_entry_list_view', 'requires/piece_rate_work_order_controller', '');
				set_button_status(0, permission, 'fnc_prices_rate_wo',1);
			}
			
			if(reponse[0]==1)
			{
				
				 show_list_view(reponse[1],'show_price_rate_wo_listview','list_container_price_rate_wo','requires/piece_rate_work_order_controller','');
				set_button_status(0, permission, 'fnc_prices_rate_wo',1);
			}
			
			
			
			release_freezing();
		}
	}



function open_terms_condition_popup(title)
{
	var update_id=document.getElementById('update_id').value;
	if (update_id=="")
	{
		alert("Save Work Order First")
		return;
	}	
	else
	{
		var page_link="requires/piece_rate_work_order_controller.php?action=terms_condition_popup&data="+update_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=300px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
		}
	}
}

	
/*-----------------------------------------------------------------------------------------------------------*/	
	
<?
	$location_details = return_library_array("select id,location_name from lib_location order by location_name","id","location_name");

?>	
	
	
	
function check_exchange_rate()
{
	var cbo_currercy=$('#cbo_currency').val();
	var wo_date = $('#txt_wo_date').val();
	var response=return_global_ajax_value( cbo_currercy+"**"+wo_date, 'check_conversion_rate', '', 'requires/piece_rate_work_order_controller');
	var response=response.split("_");
	
	$('#txt_exchange_rate').val(response[1]);
	$('#txt_exchange_rate').attr('disabled','disabled');
}
	
	
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">

<? echo load_freeze_divs ("../",$permission); ?>
    <form name="priceRateEntry_1" id="priceRateEntry_1" autocomplete="off" >
    <div style="width:1100px; float:left;" align="center">   
        <fieldset style="width:1100px;">
        <legend>Prices Rate Work Order</legend>
        <fieldset>
            <table cellpadding="0" cellspacing="2" width="820" border="0">
                <tr>
                    <td colspan="3" align="right"><strong>WO No.</strong><input type="hidden" name="update_id" id="update_id" /></td>
                    <td colspan="3" align="left">
                        <input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemid();" readonly />
                    </td>
                </tr>
                <tr>
                    <td colspan="6">&nbsp;</td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Company Name</td>
                    <td>
						<?
							echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "" );
                        ?>
                    </td>
                    
                    <td >Service Provider</td>
                    <td>
						<input type="text" name="txt_service_provider_name" id="txt_service_provider_name" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_service_provider();" readonly />
                        <input type="hidden" name="txt_service_provider_id" id="txt_service_provider_id" />
                    </td>
                    <td class="must_entry_caption">Work Order Date</td>
                    <td>
                        <input type="text" name="txt_wo_date" id="txt_wo_date" class="datepicker" style="width:136px;" onChange="check_exchange_rate();" readonly>
                    </td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Rate For</td>
                    <td>
						<?
							echo create_drop_down("cbo_rate_for", 150, $rate_for,"", 1,"-- Select --", 0,"","","20,30,35,40");
                        ?>
                    </td>
                    <td>Attention</td>
                    <td id="dyeingcom_td">
						<input type="text" name="txt_attention" id="txt_attention" class="text_boxes" style="width:150px;" maxlength="20" title="Maximum 20 Character" />
                    </td>
                    <td class="must_entry_caption">Currency</td>
                    <td>
						<? //set_exchange_rate(this.value)
							echo create_drop_down("cbo_currency", 150, $currency,"", 1,"-- Select Currency --", 1,"check_exchange_rate()");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Exchange Rate</td>
                    <td>
						<input type="text" name="txt_exchange_rate" id="txt_exchange_rate" class="text_boxes_numeric" style="width:136px;" maxlength="20" title="Maximum 5 Character"   readonly />
                    </td>
                    <td>Location</td>
                    <td>
					<?
                        echo create_drop_down( "cbo_location", 160, $location_details,"", 1, "--Select Location--", 0, "" );
                    ?>                    </td>
                    <td colspan="2">
                        <input type="button" id="set_button" class="image_uploader" style="width:150px;" value="Terms & Condition/Notes" onClick="open_terms_condition_popup('Terms Condition')" />                    

					</td>
                </tr>
                <tr>
                    <td>Remarks</td>
                    <td colspan="5">
						<input type="text" name="txt_remarks_mst" id="txt_remarks_mst" class="text_boxes" style="width:98%;" maxlength="100" title="Maximum 100 Character" />
                    </td>
                </tr>
            </table>
        </fieldset>
      <table cellpadding="0" cellspacing="2" width="1150" border="0">
            <tr>
                <td width="70%" valign="top">
                    <fieldset>
                    <legend>New Entry</legend>
                        <table cellpadding="0" cellspacing="2" width="1080" class="rpt_table">
                            <thead>
                                <th class="must_entry_caption" width="110">Order Source</th>
                                <th class="must_entry_caption" width="90">Ord. Recev. Comp</th>
                                <th class="must_entry_caption" width="90">Job No</th>
                                <th width="90">Order No</th>
                                <th width="90">Buyer</th>
                                <th width="90">Item</th>
                                <th width="90">Style</th>
                                <th class="must_entry_caption" width="90">Rate Variables</th>
                                <th class="must_entry_caption" width="90">WO Qty</th>
                                <th width="60">UOM</th>
                                <th width="90">Avg. Rate</th>
                                <th width="100">Amount</th>
                                <th width="90">Remarks</th>
                            </thead>
                           <tbody id="details_entry_list_view">
                            <tr>
                                <td>
                                     <? 
                                        echo create_drop_down( "cbo_order_source_1", 80, $order_source,"", 1, "-- Select --", 0, "",0 );
                                     ?>
                                </td>
                                <td>
                                    <?
                                        echo create_drop_down( "cbo_ord_rceve_comp_id_1", 90, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "" );
                                    ?>
                                </td>
                                <td>
                                     <input type="hidden" id="details_update_id_1" name="details_update_id_1" value="" />
                                     <input type="text" name="txtjobno_1" id="txtjobno_1" class="text_boxes" style="width:100px;" placeholder="Double click to search" onDblClick="openmypage_job_no(1);" />
                                     <input type="hidden" name="txtjobid_1" id="txtjobid_1">
                                </td>
                                <td>
                                     <input type="text" name="txtorderno_1" id="txtorderno_1" class="text_boxes" style="width:80px;" readonly />
                                     <input type="hidden" name="txtorderid_1" id="txtorderid_1" />
                                </td>
                                <td>
                                     <input type="text" name="txtbuyer_1" id="txtbuyer_1" class="text_boxes" style="width:80px;" readonly />
                                     <input type="hidden" name="txtbuyerid_1" id="txtbuyerid_1" value="" />
                                </td>
                                <td>
                                     <input type="text" name="txtitem_1" id="txtitem_1" class="text_boxes" style="width:80px;" readonly />
                                     <input type="hidden" name="txtitemid_1" id="txtitemid_1" value="" />
                                </td>
                                <td>
                                     <input type="text" name="txtstyle_1" id="txtstyle_1" class="text_boxes" style="width:80px;" readonly />
                                </td>
                                <td>
									<? 
                                    echo create_drop_down( "colortype_1", 90, $color_type,"",1, "--Select--", "","",0,"" ); 
									?>                                    
                                </td>
                                <td>
                                    <input type="text" name="txtwoqty_1" id="txtwoqty_1" class="text_boxes_numeric" style="width:100px;" placeholder="Double click to search" onDblClick="openmypage_wo_qty(1);" readonly />
                                </td>
                                <td>
									<? 
                                    echo create_drop_down( "cbodtlsuom_1", 80, $unit_of_measurement,"",1, "--Select--", "","",0,"1,2" ); 
									?>                                    
                                </td>
                                <td>
                                     <input type="text" name="txtavgrate_1" id="txtavgrate_1" class="text_boxes_numeric" style="width:80px;" readonly />
                                </td>
                                
                                
                                <td>
                                     <input type="text" name="txtdtlamount_1" id="txtdtlamount_1" class="text_boxes_numeric" style="width:80px;" readonly />
                                </td>
                                
                                
                                
                                <td>
                                     <input type="text" name="txtremarks_1" id="txtremarks_1" class="text_boxes" style="width:80px;" />
                                
                                
                                <input type="hidden" name="txt_order_qty_history_1" id="txt_order_qty_history_1" value="" />
                                <input type="hidden" name="txt_history" id="txt_history" value="" />
                                
                                </td>
                            </tr>
                          </tbody>
                        </table>
                    </fieldset>
                    <input type="hidden" name="tot_rows" id="tot_rows" value="1" />

                </td>
            </tr>
            <tr>
                <td align="center" colspan="9" class="button_container">
                    <?
                        echo load_submit_buttons($permission, "fnc_prices_rate_wo", 0,1,"reset_form('priceRateEntry_1','list_container_prices_rate_wo*details_entry_list_view','','cbo_production_basis,5','disable_enable_fields(\'cbo_company_id\');set_production_basis();set_auto_complete();')",1);
                    ?>
                    
                    <input type="hidden" name="save_data" id="save_data">
                </td>
            </tr>
        </table>
        <div style="width:920px;" id="list_container_price_rate_wo"></div>
		</fieldset>
    </div>
	</form>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Piece Rate Work Order

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
echo load_html_head_contents("Piece Rate Work Order Info","../", 1, 1, "",'1','');
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
				var theform=this.contentDoc.forms[0];
				var mst_id=this.contentDoc.getElementById("hidden_mst_id").value;
				
				show_list_view(mst_id, 'populate_price_rat_dtls_form_data', 'details_entry_list_view', 'requires/piece_rate_work_order_controller', '');
				
				get_php_form_data(mst_id, "populate_price_rat_mst_form_data", "requires/piece_rate_work_order_controller" );
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
			}
		}
	}
	
	
	
	function openmypage_job_no(str)
	{		
			
		if (form_validation('cbo_company_id*cboOrderSource_'+str,'Company*Order Source')==false)
		{
			return;
		}
		else
		{
			var order_source=$('#cboOrderSource_'+str).val();
			var OrdRceveCompId=$('#cboOrdRceveCompId_'+str).val();
			var cbo_company_id = $('#cbo_company_id').val();
			var qty_source = $('#qty_source').val();
			var rate_source = $('#rate_source').val();

			if (qty_source != rate_source) 
			{
				alert('Please check Piece Rate Work Order & Bill variable');
				return;
			}

			var title = 'Job Number Info';
			var page_link = 'requires/piece_rate_work_order_controller.php?order_source='+order_source+'&cbo_company_id='+cbo_company_id+'&action=job_no_popup';



			emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				
				$('#cbo_company_id').attr('disabled','disabled');
				var theform=this.contentDoc.forms[0];
				var job_data=this.contentDoc.getElementById("txt_selected_id").value;
				var row_num=$('#details_entry_list_view tr').length;
				var rate_for = $('#cbo_rate_for').val();
				
				var response=return_global_ajax_value( job_data+'**'+order_source+'**'+cbo_company_id+'**'+OrdRceveCompId+'**'+row_num+'**'+qty_source+'**'+rate_for, 'load_details_entry', '', 'requires/piece_rate_work_order_controller');
				$('#details_entry_list_view tr:last').after(response);
				
				fn_deleteRow(str);
				
				
				if(mst_id!=""){set_button_status(1, permission, 'fnc_prices_rate_wo',1);}
			}
		}
	}
	
	
	function openmypage_wo_qty(str)
	{
			
		if (form_validation('txtjobno_'+str+'*cbo_rate_for','Job Number*Rate For')==false)
		{
			return;
		}
		else
		{
			var order_source=$('#cboOrderSource_'+str).val();
			var txt_job_no 		= $('#txtjobno_'+str).val();
			var txt_order_no 	= $('#txtorderid_'+str).val();//txtorderno_
			var txt_order_number= $('#txtorderno_'+str).val();//
			var txt_buyer 		= $('#txtbuyer_'+str).val();
			var txt_item 		= $('#txtitem_'+str).val();
			var txt_item_id 	= $('#txtitemid_'+str).val();
			var txt_style 		= $('#txtstyle_'+str).val();
			var details_update_id = $('#detailsUpdateId_'+str).val();
			var txtjobid 		  = $('#txtjobid_'+str).val();
			var cbo_rate_for 	  = $('#cbo_rate_for').val();
			var cbo_company_id 	  = $('#cbo_company_id').val();
			var search_history 	  = $('#txtOrderQtyHistory_'+str).val();
			var txt_buyer 		= '';
			
			var data=txt_job_no+'__'+txt_order_no+'__'+txt_buyer+'__'+txt_item_id+'__'+txt_item+'__'+txt_style+'__'+cbo_rate_for+'__'+txtjobid+'__'+details_update_id+'__'+cbo_company_id+'__'+txt_order_number;

			var title = 'Work Order Qty';
			var page_link = 'requires/piece_rate_work_order_controller.php?order_source='+order_source+'&search_history='+search_history+'&data='+data+'&action=wo_qty_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width=880px,height=420px,center=1,resize=0,scrolling=0','')
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
				$("#txtOrderQtyHistory_"+str).val(search_history+'~~'+hidden_oqty+'~~'+hidden_qty+'~~'+hidden_rate+'~~'+hidden_uom+'~~'+hidden_wo_qty_uom+'~~'+hidden_color+'~~'+hidden_size+'~~'+hidden_up_ids);

				var spquom=hidden_wo_qty_uom.split(",");
				var spq=hidden_qty.split(",");
				var spr=hidden_rate.split(",");
				var suom=hidden_uom.split(",");
				
				var total_qty=amount=uom=0;
				for(i=0;i<spq.length; i++ )
				{
					total_qty+=spquom[i]*1;	
					amount+=(spquom[i]*1)*(spr[i]*1);	
					// total_qty+=spq[i]*1;	
					// amount+=(spq[i]*1)*(spr[i]*1);	
					uom=suom[i];
				}
				
				if(uom==2) var divide_by=12; else var divide_by=1;
				var avarage=(amount/total_qty);		
				avarage=avarage.toFixed(4);
				// alert(total_qty);
				
				$("#txtwoqty_"+str).val(total_qty.toFixed(2));
				
				$("#txtavgrate_"+str).val(avarage);
				$("#txtdtlamount_"+str).val((avarage*total_qty).toFixed(2));
				$("#cbodtlsuom_"+str).val(suom[0]);
			}
		}
	}
	
	function openmypage_avg_rate(row_id,product_dept,item)
	{
		if (form_validation('txtjobno_'+row_id+'*txtwoqty_'+row_id+'*cbodtlsuom_'+row_id,'job Number*WO Qty*UOM')==false)
		{
			return;
		}
		var title = 'Avg Rate';
		var rate_column = 'txtRate_'+row_id ;
		// console.log(rate_data);
		var page_link = 'requires/piece_rate_work_order_controller.php?action=avg_rate_popup&product_dept='+product_dept+'&item='+item+'&row_id='+row_id+'&rate_column='+rate_column+get_submitted_data_string(rate_column,"../");

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width=300px,height=280px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var total_process_qty 	= this.contentDoc.getElementById("total_process_qty").value;
			var hidden_process_str 	= this.contentDoc.getElementById("hidden_process_str").value;  
			
			
			let wo_qty = $('#txtwoqty_'+row_id).val();
			let uom = $('#cbodtlsuom_'+row_id).val();
			if (uom ==2 )  //Dzn
			{
				amount =  (wo_qty/12) * total_process_qty;
			}
			else //Pcs
			{
				amount =  wo_qty * total_process_qty;
			}

			$("#txtavgrate_"+row_id).val(total_process_qty);
			$("#txtRate_"+row_id).val(hidden_process_str);

			$('#txtdtlamount_'+row_id).val(amount); 
		}
	}
	
	function changeUom(ev) 
	{
		let id = ev.id;
		// console.log(id);
		let id_split = id.split('_');
		let row_id = id_split.pop();
		// console.log(row_id);
		let wo_qty = $('#txtwoqty_'+row_id).val();
		let avg_rate = $('#txtavgrate_'+row_id).val();

		if (wo_qty && avg_rate) 
		{
			let uom = $('#cbodtlsuom_'+row_id).val();
			if (uom ==2 )  //Dzn
			{
				amount =  (wo_qty/12) * avg_rate;
			}
			else //Pcs
			{
				amount =  wo_qty * avg_rate;
			}
  
			$('#txtdtlamount_'+row_id).val(amount); 
		}
			
	}
	
	
	function generate_report_file(data,action)
	{
		window.open("requires/piece_rate_work_order_controller.php?data=" + data+'&action='+action, true );
	}
	
	
	function fnc_prices_rate_wo( operation )
	{ 
		var selected_job_order_item=Array();
		rowCount = $('#details_entry_list_view tr').length;
		
		if(operation==4)
		{
			 generate_report_file($('#update_id').val(),'price_rate_wo_print');
			 return;
		}
			
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		var fill_txt='update_id*txt_system_id*cbo_company_id*txt_service_provider_id*txt_wo_date*cbo_rate_for*txt_attention*cbo_currency*txt_exchange_rate*cbo_location*txt_remarks_mst*cbo_line_id*qty_source*prod_reso_allo';
		var validation_fill_order_source='';
		var validation_fill_wo_order_qty='';
		for(i=1; i<= rowCount; i++)
		{ 
			//-----------------------------Validation--------------
			var str =$('#txtjobno_' + i).val()+$('#txtorderid_' + i).val()+$('#txtitemid_' + i).val();
			for( var s = 0; s < selected_job_order_item.length; s++ ) {
				if( selected_job_order_item[s] == str ){
					alert("Duplicate Job Order Item not allowed");return;
				}
			}
			selected_job_order_item.push(str);
			//--------------------------------------------
			
			fill_txt+="*txtOrderQtyHistory_"+i+"*cboOrderSource_"+i+"*cboOrdRceveCompId_"+i+"*txtjobno_"+i+"*txtjobid_"+i+"*txtorderid_"+i+"*txtbuyerid_"+i+"*txtitemid_"+i+"*txtstyle_"+i+"*colortype_"+i+"*txtwoqty_"+i+"*cbodtlsuom_"+i+"*txtavgrate_"+i+"*txtRate_"+i+"*txtdtlamount_"+i+"*txtremarks_"+i+"*detailsUpdateId_"+i;
			
			validation_fill_order_source+="*cboOrderSource_"+i+"*cboOrdRceveCompId_"+i+"*colortype_"+i;
			if(validation_fill_wo_order_qty==''){validation_fill_wo_order_qty="txtwoqty_"+i;} else {validation_fill_wo_order_qty+="*txtwoqty_"+i;}
		
		
		}
		
		
		
		if( form_validation('cbo_company_id*txt_wo_date*cbo_rate_for*cbo_currency*txt_exchange_rate'+validation_fill_order_source,'company*production date*rate for*currency*exchange rate*Order Source*Order Receiving Company*Rate Variables')==false )
		{
			return;
		}	
		else if( form_validation(validation_fill_wo_order_qty,'WO Qty')==false )
		{
			if(confirm("System will not save zero or blank wo qty.")==0)return;	
		}
		
		var data="action=save_update_delete&tot_rows="+rowCount+"&operation="+operation+get_submitted_data_string(fill_txt,"../");
		
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
			// console.table(reponse);
			  //alert(reponse[0]); release_freezing();return;
			show_msg(reponse[0]);
			
			if(reponse[0]==0 || reponse[0]==1)
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_system_id').value = reponse[2];
				show_list_view(reponse[1], 'populate_price_rat_dtls_form_data', 'details_entry_list_view', 'requires/piece_rate_work_order_controller', '');

				set_button_status(1, permission, 'fnc_prices_rate_wo',1);
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






	function fnc_prices_rate_wo_color(operation)
	{ 
		
		if(operation==12)
			{
				
				generate_report_file($('#update_id').val(),'price_rate_wo_color_print');
				return;
			}
	  freeze_window(operation);
	  http.open("POST","requires/piece_rate_work_order_controller_metro_bk_metro_bk.php",true);
	  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	  http.send(data);
	  http.onreadystatechange = fnc_prices_rate_wo_color_reponse;
	}
	
	function fnc_prices_rate_wo_color_reponse()
	{
		if(http.readyState == 4) 
		{
			
			var reponse=trim(http.responseText).split('**');
			show_msg(reponse[0]);
			
			if(reponse[0]==0 || reponse[0]==1)
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_system_id').value = reponse[2];
			}
			release_freezing();
		}
	}
	
/*-----------------------------------------------------------------------------------------------------------*/	
	
	
function check_exchange_rate()
{
	var cbo_currercy=$('#cbo_currency').val();
	var wo_date = $('#txt_wo_date').val();
	var response=return_global_ajax_value( cbo_currercy+"**"+wo_date, 'check_conversion_rate', '', 'requires/piece_rate_work_order_controller');
	var response=response.split("_");
	
	$('#txt_exchange_rate').val(response[1]);
	$('#txt_exchange_rate').attr('disabled','disabled');
}
	


    function fn_addRow(i) {
		var row_num=$('#details_entry_list_view tr').length;
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			
			i++;
			$("#details_entry_list_view tr:last").clone().find("input,select").each(function () {
				$(this).attr({
					'id': function (_, id) {var id = id.split("_");return id[0] + "_" + i},
					'name': function (_, name) {var name = name.split("_"); return name[0] + "_" + i},
					'value': function (_, value) {return value}
				});
			}).end().appendTo("#details_entry_list_view");
			
			
			$('#txtjobno_' + i).removeAttr("onDblClick").attr("onDblClick", "openmypage_job_no(" + i + ");");
			$('#txtwoqty_' + i).removeAttr("onDblClick").attr("onDblClick", "openmypage_wo_qty(" + i + ");");
			$('#increase_' + i).removeAttr("onclick").attr("onclick", "fn_addRow(" + i + ");");
			$('#decrease_' + i).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + i + ");");
			
			
			$('#txtjobno_' + i).val("");
			$('#txtjobid_' + i).val("");
			$('#detailsUpdateId_' + i).val("");
			$('#txtorderno_' + i).val("");
			$('#txtorderid_' + i).val("");
			$('#txtbuyer_' + i).val("");
			$('#txtbuyerid_' + i).val("");
			$('#txtitem_' + i).val("");
			$('#txtitemid_' + i).val("");
			$('#txtstyle_' + i).val("");
			$('#colortype_' + i).val(0);
			$('#txtwoqty_' + i).val("");
			$('#cbodtlsuom_' + i).val(0);
			$('#txtavgrate_' + i).val("");
			$('#txtdtlamount_' + i).val("");
			$('#txtremarks_' + i).val("");
			
			
			$('#cboOrdRceveCompId_' + i).attr('disabled',false);
			$('#cboOrderSource_' + i).attr('disabled',false);
			$('#txtjobno_' + i).attr('disabled',false);
		}
    }


 	function fn_deleteRow(rowNo) {  
		if(rowNo!=0)
		{
			var index=(rowNo-1);
			
			$("#details_entry_list_view tr:eq("+index+")").remove();
			let qty_rate_source = $('#qty_source').val();
			var numRow=$('#details_entry_list_view tr').length;
			for(i = rowNo;i <= numRow;i++){
				$("#details_entry_list_view tr:eq("+(i-1)+")").find("input,select").each(function() {
					$(this).attr({
					  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					  'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
					  'value': function(_, value) { return value }              
					}); 
					
					$('#txtjobno_' + i).removeAttr("onDblClick").attr("onDblClick", "openmypage_job_no(" + i + ");");
					if (qty_rate_source == 2) //PO WISE
					{
						$('#txtwoqty_' + i).removeAttr("onDblClick");
					}
					else
					{
						$('#txtwoqty_' + i).removeAttr("onDblClick").attr("onDblClick", "openmypage_wo_qty(" + i + ");");
					}
					$('#increase_' + i).removeAttr("onclick").attr("onclick", "fn_addRow(" + i + ");");
					$('#decrease_' + i).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + i + ");");
				
				
				})

			}
		}
		
 	}
	
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">

<? echo load_freeze_divs ("../",$permission); ?>
    <form name="priceRateEntry_1" id="priceRateEntry_1" autocomplete="off" >
    <div style="width:1350px; float:left;" align="center">   
        <fieldset style="width:900px;">
        <legend>Prices Rate Work Order</legend>
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
							echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down( 'requires/piece_rate_work_order_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/piece_rate_work_order_controller', this.value, 'service_provider_popup', 'service_provider_td' );get_php_form_data(this.value,'load_variable_settings','requires/piece_rate_work_order_controller' );" );
                        
						?>
						<input type="hidden" id="qty_source" name="qty_source">
						<input type="hidden" id="rate_source" name="rate_source">
						<input type="hidden" id="prod_reso_allo" name="prod_reso_allo">
                    </td>
                    
                    <td >Service Provider</td>
                    <td id="service_provider_td">
						<?
							echo create_drop_down("txt_service_provider_id", 160, $blank,"", 1,"-- Select --", 0,"","","");
                        ?>
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
                    <td id="location_td">
					<?
                        echo create_drop_down( "cbo_location", 160, $blank,"", 1, "--Select Location--", 0, "" );
                    ?>
                    </td>
					<td>Sewing Line</td>
                    <td id="sewing_line_td">
						<?
							echo create_drop_down( "cbo_line_id", 150, $blank,"", 1, "--Select Sewing Line--", 0, "" );
						?>  
					</td>
                </tr>
				<tr>
					<td colspan="5"></td>
					<td >
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
      
      	<fieldset style="width:1350px;">
        <table cellpadding="0" cellspacing="2" width="1150" border="0">
            <tr>
                <td width="70%" valign="top">
                    <fieldset>
                    <legend>New Entry</legend>
                        <table cellpadding="0" cellspacing="2" rules="all" width="1330" class="rpt_table">
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
                                <th></th>
                            </thead>
                           <tbody id="details_entry_list_view">
                           
                            <tr>
                                <td align="center">
                                     <? 
                                        echo create_drop_down( "cboOrderSource_1", 100, $order_source,"", 1, "-- Select --", 0, "",0 );
                                     ?>
                                </td>
                                <td>
                                    <?
                                        echo create_drop_down( "cboOrdRceveCompId_1", 90, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "" );
                                    ?>
                                </td>
                                <td>
                                     <input type="hidden" id="detailsUpdateId_1" name="detailsUpdateId_1" value="" />
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
                                    <input type="hidden" name="txtOrderQtyHistory_1" id="txtOrderQtyHistory_1" value="" />
                                </td>
                                <td>
									<? 
                                    echo create_drop_down( "cbodtlsuom_1", 80, $unit_of_measurement,"",1, "--Select--", "",'changeUom(this)',0,"1,2" ); 
									?>                                    
                                </td>
                                <td>
                                     <input type="text" name="txtavgrate_1" id="txtavgrate_1" class="text_boxes_numeric" style="width:80px;" readonly />
									 <input type="hidden" name="txtRate_1" id="txtRate_1"/>
                                </td>
                                
                                
                                <td>
                                     <input type="text" name="txtdtlamount_1" id="txtdtlamount_1" class="text_boxes_numeric" style="width:80px;" readonly />
                                </td>
                                <td>
                                    <input type="text" name="txtremarks_1" id="txtremarks_1" class="text_boxes" style="width:80px;" />
                                </td>
                                <td align="center">
                                    <input type="button" id="increase_1" name="increase[]" style="width:27px" class="formbuttonplasminus" value="+" onClick="fn_addRow(1)"/>
                                    <input type="button" id="decrease_1" name="decrease[]" style="width:27px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
                                </td>
                            </tr>
                          </tbody>
                        </table>
                    </fieldset>

                </td>
            </tr>
            <tr>
                <td align="center" colspan="9" class="button_container">
                    <?
                        echo load_submit_buttons($permission, "fnc_prices_rate_wo", 0,1,"reset_form('priceRateEntry_1','list_container_prices_rate_wo*details_entry_list_view','','cbo_production_basis,5','disable_enable_fields(\'cbo_company_id\');set_production_basis();set_auto_complete();')",1);
                    ?>
                   

                   <input type="button" id="print_metro" name="print_metro" style="width:100px" class="formbuttonplasminus" value="Print2" onClick="fnc_prices_rate_wo_color(12);"/>

                </td>
            </tr>
        </table>
        </fieldset>
        
        <div style="width:920px;" id="list_container_price_rate_wo"></div>
		
    </div>
	</form>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>


<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Garments Service Work Order

Functionality	:	
JS Functions	:
Created by		:	Shafiq
Creation date 	: 	04-07-2021
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
echo load_html_head_contents("Garments Service Work Order Info","../", 1, 1, "",'1','');
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
			var page_link = 'requires/garments_service_work_order_controller.php?cbo_company_id='+cbo_company_id+'&action=systemId_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var mst_id=this.contentDoc.getElementById("hidden_mst_id").value;
				get_php_form_data(mst_id, "populate_price_rat_mst_form_data", "requires/garments_service_work_order_controller" );
				show_list_view(mst_id, 'populate_price_rat_dtls_form_data', 'details_entry_list_view', 'requires/garments_service_work_order_controller', '');
				$('#cbo_working_company').attr('disabled', "disabled");
				set_button_status(1, permission, 'fnc_prices_rate_wo',1);
				
			}
		}
	}
	
	function calculate()
	{		
	    rowCount = $('#details_entry_list_view tr').length;
	    for(i=1; i<= rowCount; i++)
		{ 
			var poqty=$('#poqty_'+i).val();
			var woqty=$('#txtwoqty_'+i).val();
			var rate=$('#txtavgrate_'+i).val();
			var original=$('#original_'+i).val();
			var previous_qty=$('#previous_'+i).val();
			var balance_qty = woqty*1 + previous_qty*1;

			if(Number(poqty)<Number(balance_qty))
			{
				alert('WO qty can not be greater than remain qty');
				$('#txtwoqty_'+i).val(Number(original));
			}
			woqty=$('#txtwoqty_'+i).val();
			//console.log(woqty+'_'+rate+'_'+Number(woqty)*Number(rate));
			$('#txtdtlamount_'+i).val((Number(woqty)*Number(rate)).toFixed(2));
		}
		fn_chk_amount();	

	}

	function fn_chk_amount()
	{
		// alert('ok');
	    rowCount = $('#details_entry_list_view tr').length;
	    for(i=1; i<= rowCount; i++)
		{ 
			var poqty=$('#poqty_'+i).val()*1;
			var rate=$('#txtavgrate_'+i).val()*1;
			var original_rate=$('#original_rate_'+i).val()*1;
			var woqty=$('#txtwoqty_'+i).val()*1;
			var amount=$('#txtdtlamount_'+i).val()*1;
			var previous_qty=$('#previous_'+i).val()*1;
			var original=$('#original_'+i).val()*1;
			var origi_amount = (poqty-previous_qty)*original_rate;
			var cur_amount = woqty*rate;

			if(Number(origi_amount)<Number(cur_amount))
			{
				alert('Amout is not over than PO Qty*Rate');
				$('#txtwoqty_'+i).val(Number(original));
				$('#txtavgrate_'+i).val(Number(original_rate).toFixed(2));
				$('#txtdtlamount_'+i).val(Number(origi_amount).toFixed(2));
			}
	
		}

	}
	
	
	function openmypage_job_no(str)
	{		
			
		if (form_validation('cbo_company_id*cboOrdRceveCompId_'+str,'Company*Ord. Recev. Comp')==false)
		{
			return;
		}
		else
		{
			var order_source=1;
			var OrdRceveCompId=$('#cboOrdRceveCompId_'+str).val();
			var cbo_company_id = $('#cbo_company_id').val();
			
			var title = 'Job Number Info';
			var page_link = 'requires/garments_service_work_order_controller.php?order_source='+order_source+'&cbo_company_id='+OrdRceveCompId+'&action=job_no_popup';



			emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width=1070px,height=490px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				
				$('#cbo_company_id').attr('disabled','disabled');
				var theform=this.contentDoc.forms[0];
				var job_data=this.contentDoc.getElementById("txt_selected_id").value;
				var row_num=$('#details_entry_list_view tr').length;
				
				var response=return_global_ajax_value( job_data+'**'+order_source+'**'+cbo_company_id+'**'+OrdRceveCompId+'**'+row_num, 'load_details_entry', '', 'requires/garments_service_work_order_controller');
				//console.log(response);
				//return;
				$('#details_entry_list_view tr:last').after(response);
				
				fn_deleteRow(str,0);
				
				
				if(mst_id!=""){set_button_status(1, permission, 'fnc_prices_rate_wo',1);}
			}
		}
	}	
	
	function generate_report_file(data,action)
	{
		window.open("requires/garments_service_work_order_controller.php?data=" + data+'&action='+action, true );
	}
	
	
	function fnc_prices_rate_wo( operation )
	{ 
	  	freeze_window(operation);
		var selected_job_order_item=Array();
		rowCount = $('#details_entry_list_view tr').length;
		
		if(operation==4)
		{
			release_freezing();
			generate_report_file($('#update_id').val(),'price_rate_wo_print');
			return;
		}			
		
		
		var fill_txt='update_id*txt_system_id*cbo_company_id*cbo_location*cbo_source*cbo_working_company*txt_wo_date*txt_attention*cbo_currency*cbo_pay_mode*txt_exchange_rate*txt_remarks_mst';
		var validation_fill_order_source='';
		var validation_fill_wo_order_qty='';
		for(i=1; i<= rowCount; i++)
		{ 
			//-----------------------------Validation--------------
			var str =$('#txtjobno_' + i).val()+$('#poid_' + i).val()+$('#txtitemid_' + i).val()+$('#cboratefor_' + i).val();
			for( var s = 0; s < selected_job_order_item.length; s++ ) {
				if( selected_job_order_item[s] == str ){
					release_freezing();
					alert("Duplicate Job Order Item and Rate for not allowed");return;
				}
			}
			selected_job_order_item.push(str);
			//--------------------------------------------
			
			fill_txt+="*cboOrdRceveCompId_"+i+"*txtjobno_"+i+"*txtjobid_"+i+"*poid_"+i+"*txtbuyerid_"+i+"*txtitemid_"+i+"*txtstyle_"+i+"*colortype_"+i+"*cboratefor_"+i+"*txtwoqty_"+i+"*cbodtlsuom_"+i+"*txtavgrate_"+i+"*txtdtlamount_"+i+"*txtremarks_"+i+"*detailsUpdateId_"+i+"*poqty_"+i+"*clientid_"+i;
			
			validation_fill_order_source+="*cboOrdRceveCompId_"+i+"*txtjobno_"+i+"*colortype_"+i+"*txtavgrate_"+i+"*cbodtlsuom_"+i;
			if(validation_fill_wo_order_qty==''){validation_fill_wo_order_qty="txtwoqty_"+i;} else {validation_fill_wo_order_qty+="*txtwoqty_"+i;}
		
		
		}
		
		
		
		if( form_validation('cbo_company_id*cbo_working_company*txt_wo_date*cbo_currency*txt_exchange_rate*cbo_pay_mode'+validation_fill_order_source,'company*Working Company*production date*currency*exchange rate*Pay Mode*Order Receiving Company*Job No*Color type* Rate Variables*UOM')==false )
		{
			release_freezing();
			return;
		}	
		else if( form_validation(validation_fill_wo_order_qty,'WO Qty')==false )
		{
			release_freezing();			
			alert("System will not save zero or blank wo qty.");
			return;
		}
		
		var data="action=save_update_delete&tot_rows="+rowCount+"&operation="+operation+get_submitted_data_string(fill_txt,"../");
		
		  //alert (data);return;
	  http.open("POST","requires/garments_service_work_order_controller.php",true);
	  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	  http.send(data);
	  http.onreadystatechange = fnc_prices_rate_wo_reponse;
	}
	
	
	function fnc_prices_rate_wo_reponse()
	{
		if(http.readyState == 4) 
		{
			//release_freezing();
			console.log(http.responseText);
			//alert(http.responseText);return;
			var reponse=trim(http.responseText).split('**');
			  //alert(reponse[0]); release_freezing();return;
			if(reponse[0]==111)
			{
				release_freezing();
				alert('Delete not allowed. Data found in next process.');
				return;

			}
			else
			{
				show_msg(reponse[0]);
			
				if(reponse[0]==0 || reponse[0]==1)
				{
					document.getElementById('update_id').value = reponse[1];
					document.getElementById('txt_system_id').value = reponse[2];
					show_list_view(reponse[1], 'populate_price_rat_dtls_form_data', 'details_entry_list_view', 'requires/garments_service_work_order_controller', '');
					$('#cbo_working_company').attr('disabled', "disabled");
					set_button_status(1, permission, 'fnc_prices_rate_wo',1);
					release_freezing();
				}
				else if(reponse[0]==2)
				{
					release_freezing();
					reset_form('priceRateEntry_1','list_container_prices_rate_wo*details_entry_list_view','','cbo_production_basis,5','disable_enable_fields(\'cbo_company_id\');set_production_basis();set_auto_complete();');
				}else{
					release_freezing();
				}

				
			}
			
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
			var page_link="requires/garments_service_work_order_controller.php?action=terms_condition_popup&data="+update_id;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=300px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
			}
		}
	}

		
	/*-----------------------------------------------------------------------------------------------------------*/	
		
		
	function check_exchange_rate()
	{
		var cbo_currercy=$('#cbo_currency').val();
		var wo_date = $('#txt_wo_date').val();
		var company_id = $('#cbo_company_id').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+wo_date+"**"+company_id, 'check_conversion_rate', '', 'requires/garments_service_work_order_controller');
		var response=response.split("_");
		
		$('#txt_exchange_rate').val(response[1]);
		$('#txt_exchange_rate').attr('disabled','disabled');
	}
	


    function fn_addRow(i,wo_is_used) {
    	if(wo_is_used!=0)
 		{
 			alert('This WO No allready used. You can not add/remove row.');
 			return;
 		}

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
			$('#increase_' + i).removeAttr("onclick").attr("onclick", "fn_addRow(" + i + ",0);");
			$('#decrease_' + i).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + i + ",0);");

			const el = document.querySelector('#decrease_' + i);
			 if (el.classList.contains("formbutton_disabled")) {
			    el.classList.remove("formbutton_disabled");

			}


			
			
			$('#txtjobno_' + i).val("");
			$('#txtjobid_' + i).val("");
			$('#detailsUpdateId_' + i).val("");
			$('#client_' + i).val("");
			$('#clientid_' + i).val("");
			$('#txtbuyer_' + i).val("");
			$('#txtbuyerid_' + i).val("");
			$('#txtitem_' + i).val("");
			$('#txtitemid_' + i).val("");
			$('#txtstyle_' + i).val("");
			$('#colortype_' + i).val(0);
			$('#txtwoqty_' + i).val("");
			$('#poqty_' + i).val("");
			$('#po_' + i).val("");
			$('#poid_' + i).val("");
			$('#cbodtlsuom_' + i).val(0);
			$('#txtavgrate_' + i).val("");
			$('#txtdtlamount_' + i).val("");
			$('#txtremarks_' + i).val("");
			
			
			$('#cboOrdRceveCompId_' + i).attr('disabled',false);
			//$('#cboOrderSource_' + i).attr('disabled',false);
			$('#txtjobno_' + i).attr('disabled',false);
		}
    }


 	function fn_deleteRow(rowNo,wo_is_used) {  

 		if(wo_is_used==1)
 		{
 			alert('This WO No allready used. You can not add/remove row.');
 			return;
 		}
		if(rowNo!=0)
		{
			var index=(rowNo-1);
			
			$("#details_entry_list_view tr:eq("+index+")").remove();
			var numRow=$('#details_entry_list_view tr').length;
			for(i = rowNo;i <= numRow;i++){
				$("#details_entry_list_view tr:eq("+(i-1)+")").find("input,select").each(function() {
					$(this).attr({
					  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					  'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
					  'value': function(_, value) { return value }              
					}); 
					
					$('#txtjobno_' + i).removeAttr("onDblClick").attr("onDblClick", "openmypage_job_no(" + i + ");");
					$('#increase_' + i).removeAttr("onclick").attr("onclick", "fn_addRow(" + i + ",0);");
					$('#decrease_' + i).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + i + ",0);");
				
				
				})

			}
		}
		
 	}

 	function add_particular_rate(value,id)
    { 
		// alert(value+'='+id);
	    var id=id.split('_');
		var particular_id='txtavgrate_'+id[1];
		var wo_date = $("#txt_wo_date").val();
		var company_id = $("#cbo_company_id").val();
		var jobid = $("#txtjobid_"+id[1]).val();
		var poid = $("#poid_"+id[1]).val();
		var itemId = $("#txtitemid_"+id[1]).val();
		var woQty = $("#txtwoqty_"+id[1]).val();

		if (form_validation('txt_wo_date','Work Order Date')==false)
		{
			$("#cboratefor_"+id[1]).val(0);
			return;
		}
		else
		{
			get_php_form_data(  value+"__"+wo_date+"__"+particular_id+"__"+company_id+"__"+poid+"__"+itemId+"__"+woQty+"__"+jobid, "get_financial_parameter_data", "requires/garments_service_work_order_controller" );
			calculate();

		}		
		
    }
	
	function fn_set_rate_and_amount()
	{		
		// alert('ok');return;
	    var rowCount = $('#details_entry_list_view tr').length;

		var wo_date = $("#txt_wo_date").val();
		var company_id = $("#cbo_company_id").val();
		var currency = $("#cbo_currency").val();
	    var dataString = '';
	    for(i=1; i<= rowCount; i++)
		{ 
			if($('#cboratefor_'+i).val()=='0')
			{
				return;
			}
			var jobId=$("#txtjobid_"+i).val();
			var poId=$("#poid_"+i).val();
			var itemId=$("#txtitemid_"+i).val();
			var rateFor=$("#cboratefor_"+i).val();
			dataString+='&jobId' + i + '=' + jobId + '&poId' + i + '=' + poId+ '&itemId' + i + '=' + itemId+ '&rateFor' + i + '=' + rateFor;

		}
		// alert(dataString);
		get_php_form_data( rowCount+"__"+wo_date+"__"+company_id+"__"+currency+"__"+dataString, "set_rate_and_amount_in_details_part", "requires/garments_service_work_order_controller" );

	}
	
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">

<? echo load_freeze_divs ("../",$permission); ?>
    <form name="priceRateEntry_1" id="priceRateEntry_1" autocomplete="off" >
    <div style="width:1350px; float:left;" align="center">   
        <fieldset style="width:1100px;">
        <legend>Prices Rate Work Order</legend>
            <table cellpadding="0" cellspacing="2" width="1020" border="0">
                <tr>
                    <td colspan="4" align="right"><strong>WO No.</strong><input type="hidden" name="update_id" id="update_id" /></td>
                    <td colspan="4" align="left">
                        <input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemid();" readonly />
                    </td>
                </tr>
                <tr>
                    <td colspan="8">&nbsp;</td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Company Name</td>
                    <td>
						<?
							echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down( 'requires/garments_service_work_order_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/garments_service_work_order_controller',$('#cbo_source').val()+'**'+this.value,'load_drop_down_working_company','working_company_td' );" );
                        
						?>
                    </td>
                    <td>Location</td>
                    <td id="location_td">

					<?
                        echo create_drop_down( "cbo_location", 160, $blank,"", 1, "--Select Location--", 0, "" );
                    ?>
                    </td>
                    
                    <td >Source</td>
                    <td >

						<?
							echo create_drop_down( "cbo_source", 162, $knitting_source, "", 0, "-- Select --", 0, "load_drop_down( 'requires/garments_service_work_order_controller',this.value+'**'+$('#cbo_company_id').val(),'load_drop_down_working_company','working_company_td' );",0,"1,3" );

							?>
                    </td>
                    <td class="must_entry_caption">Working Company</td>
                     <td id="working_company_td" >
                    	

                        <?
							echo create_drop_down("cbo_working_company", 160, $blank,"", 1,"-- Select Company --", 0,"","","");
                        ?> 
                    </td>
                    
                </tr>
                <tr>
                	<td class="must_entry_caption">Work Order Date</td>
                    <td>

                        <input type="text" name="txt_wo_date" id="txt_wo_date" class="datepicker" style="width:136px;" onChange="check_exchange_rate();fn_set_rate_and_amount();" readonly>
                    </td>
                    <td>Attention</td>
                    <td id="dyeingcom_td" colspan="5">

						<input type="text" name="txt_attention" id="txt_attention" class="text_boxes" style="width:96%;" maxlength="20" title="Maximum 20 Character" />
                    </td>
                   
                </tr>
                <tr>
                	 <td class="must_entry_caption">Currency</td>
                    <td>

						<? //set_exchange_rate(this.value)
							echo create_drop_down("cbo_currency", 150, $currency,"", 1,"-- Select Currency --", 1,"check_exchange_rate();fn_set_rate_and_amount();");
                        ?>
                    </td>
                	<td class="must_entry_caption">Pay Mode</td>
                    <td>

                    	<? echo create_drop_down( "cbo_pay_mode", 160, $pay_mode,"", 1, "-- Select Pay Mode --", "", "","" ); ?> 
                    </td>
                    <td class="must_entry_caption">Exchange Rate(Tk)</td>
                    <td>

						<input type="text" name="txt_exchange_rate" id="txt_exchange_rate" class="text_boxes_numeric" style="width:150px;" maxlength="20" title="Maximum 5 Character"   readonly />
                    </td>
                   
                    <td colspan="2">

                       
                        <? 
                        include("../terms_condition/terms_condition.php");
                        terms_condition(431,'txt_system_id','../','txt_system_id');
                        ?>                   

					</td>
                </tr>
                <tr>
                    <td>Remarks</td>
                    <td colspan="5">

						<input type="text" name="txt_remarks_mst" id="txt_remarks_mst" class="text_boxes" style="width:94.6%;" maxlength="100" title="Maximum 100 Character" />
                    </td>
                </tr>
            </table>
      	</fieldset>
      
      	<fieldset style="width:1570px;">
        <table cellpadding="0" cellspacing="2" width="1570" border="0">
            <tr>
                <td  valign="top">
                    <fieldset>
                    <legend>New Entry</legend>
                        <table cellpadding="0" cellspacing="2" rules="all" width="1550" class="rpt_table">
                            <thead>
                              
                                <th class="must_entry_caption" width="100">Ord. Recev. Comp</th>
                                <th class="must_entry_caption" width="90">Job No</th>
                                <th width="90">Buyer</th>
                                <th width="90">Client </th>
                                <th width="90">Style</th>
                                <th width="90">Item</th>
                                <th width="100">PO</th>
                                <th width="70">PO Qty</th>
                                <th class="must_entry_caption" width="100">Color Type</th>
                                <th class="must_entry_caption" width="100">Rate For</th>
                                <th class="must_entry_caption" width="90">WO Qty</th>
                                <th width="60">UOM</th>
                                <th width="90">Rate</th>
                                <th width="100">Amount</th>
                                <th width="90">Remarks</th>
                                <th></th>
                            </thead>
                           <tbody id="details_entry_list_view">
                           
                            <tr>
                               
                                <td>
                                    <?
                                        echo create_drop_down( "cboOrdRceveCompId_1",100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "" );
                                    ?>
                                </td>
                                <td>
                                     <input type="hidden" id="detailsUpdateId_1" name="detailsUpdateId_1" value="" />
                                     <input type="text" name="txtjobno_1" id="txtjobno_1" class="text_boxes" style="width:100px;" placeholder="Double click to search" onDblClick="openmypage_job_no(1);" />
                                     <input type="hidden" name="txtjobid_1" id="txtjobid_1">
                                </td>
                               
                                <td>
                                     <input type="text" name="txtbuyer_1" id="txtbuyer_1" class="text_boxes" style="width:80px;" readonly />
                                     <input type="hidden" name="txtbuyerid_1" id="txtbuyerid_1" value="" />
                                </td>
                                 <td>
                                     <input type="text" name="client_1" id="client_1" class="text_boxes" style="width:80px;" readonly />
                                     <input type="hidden" name="clientid_1" id="clientid_1" />
                                </td>
                                <td>
                                     <input type="text" name="txtstyle_1" id="txtstyle_1" class="text_boxes" style="width:80px;" readonly />
                                </td>
                                <td>
                                     <input type="text" name="txtitem_1" id="txtitem_1" class="text_boxes" style="width:80px;" readonly />
                                     <input type="hidden" name="txtitemid_1" id="txtitemid_1" value="" />
                                </td>
                                 <td>
                                	<input type="text" name="po_1" id="po_1" class="text_boxes_numeric" placeholder="PO" style="width:80px;" readonly>
                                	<input type="hidden" name="poid_1" id="poid_1"  />
                                </td>
                                <td>
                                	<input type="text" name="poqty_1" id="poqty_1" class="text_boxes_numeric" placeholder="PO Qty" style="width:80px;" readonly>
                                </td>
                                <td>
									<? 
                                    echo create_drop_down( "colortype_1", 100, $color_type,"",1, "--Select--", "","",0,"" ); 
									?>                                    
                                </td>
			                    <td>
									<?
										echo create_drop_down("cboratefor_1", 100, $rate_for,"", 1,"-- Select --", 0,"","","20,30,40");
			                        ?>
			                    </td>
                               
                                <td>
                                    <input type="text" name="txtwoqty_1" id="txtwoqty_1" class="text_boxes_numeric" style="width:80px;" placeholder="WO Qty"   />
                                    
                                </td>
                                <td>
									<? 
                                    echo create_drop_down( "cbodtlsuom_1", 80, $unit_of_measurement,"",1, "--Select--", "","",0,"1,2,58" ); 
									?>                                    
                                </td>
                                <td>
                                     <input type="text" name="txtavgrate_1" id="txtavgrate_1" class="text_boxes_numeric" style="width:80px;"  />
                                </td>
                               
                                <td>
                                     <input type="text" name="txtdtlamount_1" id="txtdtlamount_1" class="text_boxes_numeric" style="width:80px;" readonly />
                                </td>
                                <td>
                                    <input type="text" name="txtremarks_1" id="txtremarks_1" class="text_boxes" style="width:80px;" />
                                </td>
                                <td align="center">
                                    <input type="button" id="increase_1" name="increase[]" style="width:27px" class="formbuttonplasminus" value="+" onClick="fn_addRow(1,0)"/>
                                    <input type="button" id="decrease_1" name="decrease[]" style="width:27px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1,0);"/>
                                </td>
                            </tr>
                          </tbody>
                        </table>
                    </fieldset>

                </td>
            </tr>
            <tr>
                <td align="center" colspan="10" class="button_container">
                    <?
                        echo load_submit_buttons($permission, "fnc_prices_rate_wo", 0,1,"reset_form('priceRateEntry_1','list_container_prices_rate_wo*details_entry_list_view','','cbo_production_basis,5','disable_enable_fields(\'cbo_company_id\');set_production_basis();set_auto_complete();')",1);
                    ?>
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

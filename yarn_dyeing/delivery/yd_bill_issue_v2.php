<?
die;

	/*--- ----------------------------------------- Comments
	Purpose         :   This form will create Yarn Dyeing Bill Entry               
	Functionality   :   
	JS Functions    :
	Created by      :   Md. MInul Hasan
	Creation date   :   02-10-2022
	Updated by      :       
	Update date     :
	Oracle Convert  :       
	Convert date    :      
	QC Performed BY :       
	QC Date         :   
	Comments        :
	*/
	session_start();
	require_once('../../includes/common.php');
	extract($_REQUEST);
	$_SESSION['page_permission']=$permission;
	//--------------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents('Yarn Dyeing Bill Entry', '../../', 1, 1, $unicode, 1, '');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../logout.php';
    var permission='<?php echo $permission; ?>';

    function exchange_rate(val)
	{
		if(form_validation('cbo_company_name*txt_bill_date', 'Company Name*Bill Date')==false )
		{
			$("#cbo_currency_id").val(0);
			return;
		}
		
		if(val==0)
		{
			$('#txt_bill_date').removeAttr('disabled','disabled');
			$('#cbo_company_name').removeAttr('disabled','disabled');
			$("#txt_exchange_rate").val("");
		}
		else if(val==1)
		{
			$("#txt_exchange_rate").val(1);
			$('#txt_bill_date').attr('disabled','disabled');
			$('#cbo_company_name').attr('disabled','disabled');
			$('#txt_exchange_rate').attr('disabled','disabled');
		}
		else
		{
			var bill_date = $('#txt_bill_date').val();
			var company_name = $('#cbo_company_name').val();
			var response=return_global_ajax_value( val+"**"+bill_date+"**"+company_name, 'check_conversion_rate', '', 'requires/yd_bill_issue_v2_controller');
			$('#txt_exchange_rate').val(response);
			$('#txt_bill_date').attr('disabled','disabled');
			$('#cbo_company_name').attr('disabled','disabled');
			$('#txt_exchange_rate').attr('disabled','disabled');
		}
	}

	function job_search_popup_job()
    {

    	if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name','Company Name*Within Group* Party Name')==false )
		{
			return;
		}

		var cbo_company_name  = $('#cbo_company_name').val();
		var cbo_within_group  = $('#cbo_within_group').val();
		var cbo_party_name    = $('#cbo_party_name').val();

		var data = '&cbo_company_name='+cbo_company_name+'&cbo_within_group='+cbo_within_group+'&cbo_party_name='+cbo_party_name;
    	
    	page_link='requires/yd_bill_issue_v2_controller.php?action=job_search_popup_job'+data;
    	title='Job Search Popup';

    	emailwindow=dhtmlmodal.open('JobBox', 'iframe', page_link, title, 'width=1150px, height=500px, center=1, resize=0, scrolling=0', '../../');
    	emailwindow.onclose=function()
        {
        	var theform=this.contentDoc.forms[0];
            var party_id=this.contentDoc.getElementById("hidden_party_id").value;

            var receive_ids=this.contentDoc.getElementById("txt_selected_id").value;

            var data =party_id+"_"+receive_ids;

            show_list_view(data,'dtls_list_view','list_view','requires/yd_bill_issue_v2_controller','');

            $('#cbo_company_name').attr('disabled','disabled');
            $('#cbo_within_group').attr('disabled','disabled');
            $('#cbo_party_name').attr('disabled','disabled');
        }
    }
    function load_delivery_data(job_no,receive_no,row_id)
    {
    	var job_no = job_no;

    	var data = job_no+'_'+receive_no;

    	$("#txt_job_no").val( job_no );
    	//$('#txt_job_no').attr('disabled','disabled');

    	$("#hidden_row_id").val(row_id);

    	get_php_form_data( job_no, "load_php_yd_job_data_to_form", "requires/yd_bill_issue_v2_controller" );

    	show_list_view(data,'delivery_dtls_list_view','delivery_details','requires/yd_bill_issue_v2_controller','');

    	set_button_status(0, permission, 'fnc_yd_bill_entry',1);
    }

    function fnc_yd_bill_entry(operation)
    {
    	if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name*txt_bill_date*cbo_currency_id*txt_exchange_rate','Company Name*Within Group* Party Name*bill Date*Bill Currency*Exchange Rate')==false )
		{
			return;
		}

		var txt_bill_no 		= $('#txt_bill_no').val();
		var hdn_update_id 		= $('#hdn_update_id').val();

		var cbo_company_name  	= $('#cbo_company_name').val();
		var cbo_within_group  	= $('#cbo_within_group').val();
		var cbo_party_name    	= $('#cbo_party_name').val();
		var txt_bill_date     	= $('#txt_bill_date').val();
		var cbo_currency_id   	= $('#cbo_currency_id').val();
		var txt_exchange_rate 	= $('#txt_exchange_rate').val();
		var cbo_location_name 	= $('#cbo_location_name').val();
		var cbo_party_location 	= $('#cbo_party_location').val();
		var txt_remarks 		= $('#txt_remarks').val();

		var txt_job_no 			= $('#txt_job_no').val();
		var txt_wo_no 			= $('#txt_wo_no').val();
		var cbo_pro_type 		= $('#cbo_pro_type').val();
		var cbo_order_type 		= $('#cbo_order_type').val();

		var data = '&txt_bill_no='+txt_bill_no+'&hdn_update_id='+hdn_update_id+'&cbo_company_name='+cbo_company_name+'&cbo_within_group='+cbo_within_group+'&cbo_party_name='+cbo_party_name+'&txt_bill_date='+txt_bill_date+'&cbo_currency_id='+cbo_currency_id+'&txt_exchange_rate='+txt_exchange_rate+'&cbo_location_name='+cbo_location_name+'&cbo_party_location='+cbo_party_location+'&txt_remarks='+txt_remarks+'&txt_job_no='+txt_job_no+'&txt_wo_no='+txt_wo_no+'&cbo_pro_type='+cbo_pro_type+'&cbo_order_type='+cbo_order_type;

		var j=0;
    	var i=0;
    	var check_field=0;
    	var data_all="";

    	$("#delivery_details tr").each(function()
    	{
    		var txtstyleRef         = $(this).find('input[name="txtstyleRef[]"]').val();
    		var txtsaleOrder        = $(this).find('input[name="txtsaleOrder[]"]').val();
    		var txtsaleOrderID      = $(this).find('input[name="txtsaleOrderID[]"]').val();
    		var buyerBuyer         	= $(this).find('input[name="buyerBuyer[]"]').val();
    		var txtlot         		= $(this).find('input[name="txtlot[]"]').val();
    		var txtGrayLot         	= $(this).find('input[name="txtGrayLot[]"]').val();
    		var txtHiddenGrayLot    = $(this).find('input[name="txtHiddenGrayLot[]"]').val();
    		var txtcountTypeId      = $(this).find('input[name="txtcountTypeId[]"]').val();
    		var txtcountType        = $(this).find('select[name="txtcountType[]"]').val();
    		var txtcountId        	= $(this).find('input[name="txtcountId[]"]').val();
    		var cboCount         	= $(this).find('select[name="cboCount[]"]').val();
    		var cboYarnTypeId       = $(this).find('input[name="cboYarnTypeId[]"]').val();
    		var cboYarnType         = $(this).find('select[name="cboYarnType[]"]').val();
    		var txtydCompositionId  = $(this).find('input[name="txtydCompositionId[]"]').val();
    		var cboComposition      = $(this).find('select[name="cboComposition[]"]').val();
    		var txtYarnColorId      = $(this).find('input[name="txtYarnColorId[]"]').val();
    		var txtYarnColor        = $(this).find('select[name="txtYarnColor[]"]').val();
    		var txtnoBag         	= $(this).find('input[name="txtnoBag[]"]').val();
    		var txtConeBag         	= $(this).find('input[name="txtConeBag[]"]').val();
    		var cboUomId         	= $(this).find('input[name="cboUomId[]"]').val();
    		var cboUom         		= $(this).find('select[name="cboUom[]"]').val();
    		var txtOrderqty         = $(this).find('input[name="txtOrderqty[]"]').val();
    		var txtHiddenOrderqty   = $(this).find('input[name="txtHiddenOrderqty[]"]').val();
    		var txtProcessLoss      = $(this).find('input[name="txtProcessLoss[]"]').val();
    		var hiddenProcessLoss	= $(this).find('input[name="txtHiddenProcessLoss[]"]').val();
    		var txtadjTypeId		= $(this).find('input[name="txtadjTypeId[]"]').val();
    		var txtadjType			= $(this).find('select[name="txtadjType[]"]').val();
    		var txtTotalqty			= $(this).find('input[name="txtTotalqty[]"]').val();
    		var txtHiddenTotalqty	= $(this).find('input[name="txtHiddenTotalqty[]"]').val();
    		var txtTotalDeliveryqty  = $(this).find('input[name="txtTotalDeliveryqty[]"]').val();
    		var txtHiddenTotalDeliveryqty = $(this).find('input[name="txtHiddenTotalDeliveryqty[]"]').val();
    		var txtPreviousBillqty = $(this).find('input[name="txtPreviousBillqty[]"]').val();
    		var txtHiddenPreviousBillqty       = $(this).find('input[name="txtHiddenPreviousBillqty[]"]').val();
    		var txtbalanceqty       = $(this).find('input[name="txtbalanceqty[]"]').val();
    		var txtHiddenbalanceqty     	= $(this).find('input[name="txtHiddenbalanceqty[]"]').val();
    		var txtReceiveQty = $(this).find('input[name="txtReceiveQty[]"]').val();;
    		var hiddenOrderQty  	= $(this).find('input[name="hiddenOrderQty[]"]').val();
    		var txtHiddenDeliveryId  = $(this).find('input[name="txtHiddenDeliveryId[]"]').val();
    		var txtHiddenDtlsId       = $(this).find('input[name="txtHiddenDtlsId[]"]').val();
    		var rate     	= $(this).find('input[name="rate[]"]').val();
    		var txtHiddenCurrencyId = $(this).find('input[name="txtHiddenCurrencyId[]"]').val();;
    		var billAmount  	= $(this).find('input[name="billAmount[]"]').val();

    		if(txtReceiveQty*1>0)
        	{
        		j++;
        		i++;

        		data_all +="&txtstyleRef_"+j+"='"+txtstyleRef+"'&txtsaleOrder_"+j+"='"+txtsaleOrder+"'&txtsaleOrderID_"+j+"='"+txtsaleOrderID+"'&buyerBuyer_"+j+"='"+buyerBuyer+"'&txtlot_"+j+"='"+txtlot+"'&txtGrayLot_"+j+"='"+txtGrayLot+"'&txtcountTypeId_"+j+"='"+txtcountTypeId+"'&txtcountId_"+j+"='"+txtcountId+"'&cboYarnTypeId_"+j+"='"+cboYarnTypeId+"'&txtydCompositionId_"+j+"='"+txtydCompositionId+"'&txtYarnColorId_"+j+"='"+txtYarnColorId+"'&txtnoBag_"+j+"='"+txtnoBag+"'&txtConeBag_"+j+"='"+txtConeBag+"'&cboUomId_"+j+"='"+cboUomId+"'&txtOrderqty_"+j+"='"+txtOrderqty+"'&txtProcessLoss_"+j+"='"+txtProcessLoss+"'&txtadjTypeId_"+j+"='"+txtadjTypeId+"'&txtHiddenTotalqty_"+j+"='"+txtHiddenTotalqty+"'&txtTotalDeliveryqty_"+j+"='"+txtTotalDeliveryqty+"'&txtHiddenTotalDeliveryqty_"+j+"='"+txtHiddenTotalDeliveryqty+"'&txtPreviousBillqty_"+j+"='"+txtPreviousBillqty+"'&txtHiddenPreviousBillqty_"+j+"='"+txtHiddenPreviousBillqty+"'&txtbalanceqty_"+j+"='"+txtbalanceqty+"'&txtHiddenbalanceqty_"+j+"='"+txtHiddenbalanceqty+"'&txtReceiveQty_"+j+"='"+txtReceiveQty+"'&hiddenOrderQty_"+j+"='"+hiddenOrderQty+"'&txtHiddenDeliveryId_"+j+"='"+txtHiddenDeliveryId+"'&txtHiddenDtlsId_"+j+"='"+txtHiddenDtlsId+"'&rate_"+j+"='"+rate+"'&txtHiddenCurrencyId_"+j+"='"+txtHiddenCurrencyId+"'&billAmount_"+j+"='"+billAmount+"'";
        	}

        	if(data_all=='')
			{
				alert("Please Insert At Least One Bill Quantity!!!");
				return;
			}

			if(check_field==0)
	        {
	        	var data="action=save_update_delete&operation="+operation+'&total_row='+i+'&txt_job_no='+txt_job_no+'&txt_bill_date='+txt_bill_date+'&cbo_company_name='+cbo_company_name+'&cbo_location_name='+cbo_location_name+'&cbo_within_group='+cbo_within_group+'&cbo_party_name='+cbo_party_name+'&cbo_party_location='+cbo_party_location+'&txt_wo_no='+txt_wo_no+'&cbo_pro_type='+cbo_pro_type+'&cbo_order_type='+cbo_order_type+'&hdn_update_id='+hdn_update_id+'&txt_bill_no='+txt_bill_no+'&txt_remarks='+txt_remarks+data_all;

	        	//alert(data);
	        	return;
	        	//freeze_window(operation);
	            http.open("POST","requires/yd_delivery_entry_v2_controller.php",true);
	            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	            http.send(data);
	            http.onreadystatechange = fnc_yd_delivery_entry_response;
	        }
	        else
	        {
	        	return;
	        }

    	});

    }

    function calculate_bill_ammount(id,value)
    {
    	if ( form_validation('txt_bill_date*cbo_currency_id*txt_exchange_rate','bill Date*Bill Currency*Exchange Rate')==false )
		{
			$("#txtReceiveQty_"+id).val(total_amount);
			$("#billAmount_"+id).val(total_amount);
			return;
		}

		$('#cbo_currency_id').attr('disabled','disabled');

		var currency = $("#cbo_currency_id").val();
		var exchange_rate = $("#txt_exchange_rate").val();

		var order_currency = $("#txtHiddenCurrencyId_"+id).val();
		var rate = $("#rate_"+id).val();
		var balance = $("#txtbalanceqty_"+id).val()*1;

		if(balance<value)
		{
			$("#txtReceiveQty_"+id).val(total_amount);
			$("#billAmount_"+id).val(total_amount);
			alert("Bill Quantity Can Not Be Greater Than Balance Quantity");
		}


		if(order_currency==currency)
		{
			var total_amount = rate*value;
		}
		else
		{
			if(currency==1 && order_currency==2)
			{
				var total_amount = exchange_rate*value*rate;
			}
			else if(currency==2 && order_currency==1)
			{
				var total_amount = value*rate/exchange_rate;
			}
		}


		$("#billAmount_"+id).val(total_amount);
		

    }
</script>
</head>
	<body onLoad="set_hotkey()">
	  	<div style="width:100%;" align="center">
	    	<? echo load_freeze_divs ("../../",$permission); ?>
			<form name="dyeingbill_1" id="dyeingbill_1" autocomplete="off">
            	<fieldset style="width:1050px;">
            		<legend>Yarn Dyeing Bill Entry</legend>
            		<table width="1030" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
            			<tr>
	                        <td colspan="4" align="right"><strong>Bill ID</strong></td>
	                        <td colspan="4">
	                            <input class="text_boxes" type="text" name="txt_bill_no" id="txt_bill_no" onDblClick="openmypage_system_id();" placeholder="Double Click" readonly style="width: 150px;" />
	                            <input type="hidden" name="hdn_update_id" id="hdn_update_id">
	                            <input type="hidden" name="hidden_row_id" id="hidden_row_id">
	                        </td>
	                    </tr>
	                    <tr>
	                    	<td align="right" class="must_entry_caption">Company Name</td>
	                        <td> 
	                            <?php echo create_drop_down('cbo_company_name', 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", 'id,company_name', 1, '-- Select Company --', $selected, "load_drop_down('requires/yd_bill_issue_v2_controller', this.value+'_'+document.getElementById('cbo_within_group').value,'load_drop_down_party', 'party_td' );load_drop_down('requires/yd_bill_issue_v2_controller', this.value,'load_drop_down_location', 'location_td' );load_drop_down('requires/yd_bill_issue_v2_controller',document.getElementById('cbo_party_name').value,'load_drop_down_party_location', 'party_location_td' );",0); ?>
	                        </td>
	                        <td align="right">Location Name</td>
	                        <td id="location_td"> 
	                            <?php echo create_drop_down('cbo_location_name', 150, $blank_array, '', 1, '-- Select Location --', $selected, "",0); ?>
	                        </td>
	                        <td align="right" class="must_entry_caption" >Within Group</td>
	                        <td> 
	                            <?php echo create_drop_down('cbo_within_group', 150, $yes_no, '', 1, '-- Select Within Group --', $selected, "load_drop_down( 'requires/yd_bill_issue_v2_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_party', 'party_td' );load_drop_down('requires/yd_bill_issue_v2_controller',document.getElementById('cbo_party_name').value,'load_drop_down_party_location', 'party_location_td' );",0); ?>
	                        </td>
	                        <td align="right" class="must_entry_caption" >Party Name</td>
	                        <td id="party_td"> 
	                            <?php echo create_drop_down('cbo_party_name', 120, $blank_array, '', 1, '-- Select Location --', $selected, "",0); ?>
	                        </td>
	                    </tr>
	                    <tr>
	                    	<td align="right">Party Location</td>
	                        <td id="party_location_td"> 
	                            <?php echo create_drop_down('cbo_party_location', 150, $blank_array, '', 1, '-- Select Location --', $selected, "",0); ?>
	                        </td>
	                    	<td align="right" class="must_entry_caption">Bill Date</td>
	                        <td> 
	                            <input class="datepicker" type="text" name="txt_bill_date" id="txt_bill_date" placeholder="Write Bill Date" style="width:140px;" value="<? echo date('d-m-Y');?>" />
	                        </td>
	                        <td align="right" class="must_entry_caption">Currency</td>
	                        <td id="currency_td">
								<? echo create_drop_down("cbo_currency_id", 150, $currency,"", 1, "-- Select Currency --",$selected,"exchange_rate(this.value)", "","","","","",7 ); ?>
	                        </td> 
	                        <td class="must_entry_caption">Exchange Rate</td>
               			 	<td>
               			 		<input type="text" name="txt_exchange_rate" id="txt_exchange_rate" style="width:110px" class="text_boxes_numeric"  value=""  readonly/>
               			 	</td>
	                    </tr>
	                    <tr>
	                    	<td align="right">Remarks</td>
	                        <td colspan="7">
	                        	<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:910px;" placeholder="Remarks Entry">
	                        </td>
	                    </tr>
            		</table>
            	</fieldset>
            	<br>
	        	<fieldset style="width: 1300px; margin: 0 auto;">
	        		<legend>Yarn Dyeing Bill Details</legend>
	        		<table cellpadding="0" cellspacing="0" border="0" width="800" id="tbl_job_dtls_delivery">
	        			<tr>
	                    	<td align="right" id="job_td" class="must_entry_caption">Job No</td>
	                        <td> 
	                            <input class="text_boxes" type="text" name="txt_job_no" id="txt_job_no" placeholder="Double Click For Job No" onDblClick="job_search_popup_job()" style="width:140px;" readonly />
	                        </td>
	                    	<td align="right">WO No</td>
	                        <td> 
	                            <input class="text_boxes" type="text" name="txt_wo_no" id="txt_wo_no" placeholder="Display" style="width:140px;" readonly />
	                        </td>
	                    	<td align="right">Prod. Type</td>
	                        <td> 
	                            <?php echo create_drop_down('cbo_pro_type', 120, $w_pro_type_arr, '', 1, '-- Select Location --', $selected, "",1); ?>
	                        </td>
	                        <td align="right">Order Type</td>
	                        <td> 
	                            <?php echo create_drop_down('cbo_order_type', 120, $w_order_type_arr, '', 1, '-- Select --', $selected, "",1); ?>
	                        </td>
	                    </tr>

	        		</table>
	        		<table cellpadding="0" cellspacing="0" border="0" width="1400" id="tbl_dtls_delivery">
	        		 	<thead class="form_table_header">
	        		 		<th width="80" id="style_no_td">Style</th>
                            <th width="60" id="buyer_no_td">Buyer Job</th>
                            <th width="60" id="cus_buyer_no_td" >Cust. Buyer</th>
                            <th width="80" id="yd_batch_lot_no_td" >YD Batch/Lot</th>
                            <th width="80" id="grey_lot_no_td" >Grey Lot</th>
                            <th width="60" id="count_type_no_td">Count Type</th>
                            <th width="60" id="count_no_td" >Count</th>
                            <th width="80" id="yarn_type_no_td" >Yarn Type</th>
                            <th width="100" id="composition_no_td" >Yarn Composition</th>
                            <th width="80" id="color_no_td">Y/D Color</th>
                            <th width="40" id="no_of_bag_no_td">No of Bag</th>
                            <th width="50" id="cone_per_bag_no_td" >Cone Per Bag</th>
                            <th width="50" id="order_uom_no_td" >Order UOM</th>
                            <th width="50" id="order_qty_no_td" >Order Qty</th>
                            <th width="50" id="process_loss_no_td" >Process Loss %</th>
                            <th width="80" id="adj_type_no_td" >Adj. Type</th>
                            <th width="50" id="grey_qty_no_td" >Grey Qty</th>
                            <th width="50" id="delivery_qty_no_td" >Delivery Qty</th>
                            <th width="50" id="previous_bill_no_td" >Previous Bill</th>
                            <th width="50" id="balance_no_td" >Balance</th>
                            <th width="50" id="yd_bill_no_td" class="must_entry_caption" >Current Bill</th>
                            <th width="50" id="rate_no_td" >Rate</th>
                            <th width="50" id="bill_amount" >Bill Amount</th>
	        		 	</thead>
	        		 	<tbody id="delivery_details"> 	
		        		</tbody>
	        		</table>
	        		<table width="1400" cellspacing="2" cellpadding="0" border="0">
	                    <tr>
	                        <td align="center" valign="middle" class="button_container">
	                        	<? echo create_drop_down( "cbo_template_id", 100, $report_template_list,'', 0, '', 0, "");?>
	                            <?php
									  echo load_submit_buttons( $permission, "fnc_yd_bill_entry", 0,1,"fnResetForm();",1); 
	                            ?>
	                        </td> 
	                    </tr>
	                </table>
	        	</fieldset>     	
            </form>
		</div>
		<br>
		<div>
			<fieldset style="width: 800px; margin: 0 auto;">
				<legend>Delivery List View:</legend>
				<div id="list_view"></div>
			</fieldset>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
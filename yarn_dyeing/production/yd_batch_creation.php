<?php
/*--- ----------------------------------------- Comments
Purpose			:	This page will display yarn dyeing batch creation
Functionality	:	
JS Functions	:
Created by		:	Sapayth
Creation date 	:	08-02-2020
Updated by 		:		
Update date		:
Oracle Convert 	:		
Convert date	:	
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$user_level=$_SESSION['logic_erp']["user_level"];
//echo $user_level;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents('Yarn Dyeing Batch Creation', '../../', 1, 1, $unicode, 0, '');
?>

<style>
	table.rpt_table input {
	    width: 90%;
	}
</style>

<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../../../logout.php';
    var permission='<?php echo $permission; ?>';

    function openBatchPopup() {
    	var data = document.getElementById('cbo_company_name').value;
    	var pageLink = 'requires/yd_batch_creation_controller.php?action=batch_no_popup&data='+data;
    	var title = 'Search Batch No';
        batchPopup = dhtmlmodal.open('Batch No', 'iframe', pageLink, title, 'width=700px, height=500px, center=1, resize=0, scrolling=0', '../../');
        batchPopup.onclose = batchPopupCloseHandler;
    }

    function batchPopupCloseHandler() {
    	freeze_window(1);

		var theform = this.contentDoc.forms[0];
        var batchMstId = this.contentDoc.getElementById('hdnBatchMstId').value;

        var reqType = 2;
        // data as 1**12345
        get_php_form_data(reqType+'**'+batchMstId, 'populate_mst_data_from_search_popup', 'requires/yd_batch_creation_controller');
        show_list_view(reqType+'**'+batchMstId, 'populate_dtls_data_from_search_popup', 'material_details','requires/yd_batch_creation_controller', '');

        document.getElementById('cbo_company_name').setAttribute('disabled', 'disabled');
        document.getElementById('txtBatchColor').setAttribute('disabled', 'disabled');
        document.getElementById('cbo_batch_against').setAttribute('disabled', 'disabled');
        document.getElementById('txtBatchNo').setAttribute('disabled', 'disabled');

        calculateBatchQty();
        set_button_status(1, permission, 'saveUpdateDelete', 1);

        release_freezing();
	}

	function openColorPopup() 
	{
		if( !form_validation('cbo_company_name', 'Company Name') ) 
		{
            return; // returning to the form if required fields is not done
        }

        var data=document.getElementById('cbo_company_name').value;
        var pageLink = 'requires/yd_batch_creation_controller.php?action=batch_color_popup&data='+data;
        var title = 'Search Batch Color';
        colorPopup = dhtmlmodal.open('Color Box', 'iframe', pageLink, title, 'width=700px, height=500px, center=1, resize=0, scrolling=0', '../../');

        colorPopup.onclose = colorPopupCloseHandler;
	}

	function colorPopupCloseHandler() 
	{
		var theform=this.contentDoc.forms[0];
        var ordMstId=this.contentDoc.getElementById('hdnYdMstId').value;
        var ordDtlsIds=this.contentDoc.getElementById('hdnYdDtlsIds').value;

        document.getElementById('cbo_company_name').setAttribute('disabled', 'disabled');
        // document.getElementById('txtBatchColor').setAttribute('disabled', 'disabled');

        var reqType = 1;
        // data as 1**12345
        get_php_form_data(reqType+'**'+ordMstId+'**'+ordDtlsIds, 'populate_mst_data_from_search_popup', 'requires/yd_batch_creation_controller');
        show_list_view(reqType+'**'+ordDtlsIds, 'populate_dtls_data_from_search_popup', 'material_details','requires/yd_batch_creation_controller', '');

        set_button_status(0, permission, 'saveUpdateDelete', 1);
	}

	function calculateBatchQty(i) 
	{
		
		var txtBatchQty=$('#txtBatchQty_'+i).val()*1;
		var txtProdQty=$('#hdnProducqty_'+i).val()*1;
		var txtPreQty=$('#hdnPrebatchqty_'+i).val()*1;
		var balance_qty = (txtProdQty*1-txtPreQty*1);
		//alert(balance_qty);
		if(txtBatchQty>balance_qty)
		{
			alert("Batch Qty exceeds Production qty not allow."); // as like as CRM
			$('#txtBatchQty_'+i).val('');
			return;
		}
		
        var rowCount = document.getElementById('batch-rows').children.length;
        math_operation('txtTotBatchQty', 'txtBatchQty_', '+', rowCount);
		var batch_wgt = document.getElementById('txtTotBatchQty').value; 
		 document.getElementById('txtBatchWeight').value=batch_wgt;
    }

    function saveUpdateDelete(operation) 
	{
		
		if($('#batch_no_creation').val()!=1)
		{
			if( form_validation('txtBatchNo','Batch Number')==false )
			{
				alert("Plesae Insert Batch No.");
				$('#txtBatchNo').focus();
				return;
			}
		}
		
        if ( !form_validation('cbo_company_name*txtBatchColor*cbo_batch_against*txtBatchWeight*txtBatchDate', 'Company Name*Batch Color*Batch Against*Batch Weight*Batch Date') ) 
		{
            return;
        }

        dataStr=get_submitted_data_string('cbo_company_name*txtBatchColor*cbo_batch_against*txtColorRange*txtBatchNo*txtBatchWeight*txtExtnNo*txtBatchDate*cbo_process*txtDurationReq*cbo_location_name*cbo_machine*txtRemarks*hdnJobMstId*hdnColorId*hdnUpdateId*hdnOrderId*txtBatchSerialNo*hdnOrderNo*hdnBookingWithoutOrd*hdnBookingType*batch_no_creation', '../../');

        var totalRow = document.getElementById('batch-rows').children.length;
        
        for (var i=1; i<=totalRow; i++) {
            var qty=$('#txtBatchQty_'+i).val();
            if(!qty) {
            	alert('Please fill Batch Quantity');
                return;
            }

            dataStr+=get_submitted_data_string('txtOrderNo_'+i+'*txtLot_'+i+'*txtCount_'+i+'*txtYarnType_'+i+'*txtYarnComp_'+i+'*txtBobbin_'+i+'*txtWindingPack_'+i+'*txtBatchQty_'+i+'*hdnDtlsId_'+i+'*hdnOrderId_'+i+'*hdnOrderNo_'+i+'*hdnSalesOrderId_'+i+'*hdnSalesOrderNo_'+i+'*hdnProductId_'+i, '../../');
        }

        // console.log(dataStr); return
        var data='action=save_update_delete&operation='+operation+'&total_row='+totalRow+dataStr;
        freeze_window(operation);
        http.open('POST', 'requires/yd_batch_creation_controller.php', true);
        http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        http.send(data);
        http.onreadystatechange = saveUpdateDeleteResponseHandler;
    }

    function saveUpdateDeleteResponseHandler() 
	{
    	if(http.readyState == 4) 
		{
            //alert(http.responseText);//return;
            var response=trim(http.responseText).split('**');
            show_msg(response[0]);
            //$('#cbo_uom').val(12);
			if(response[0]==11)
			{
				alert(response[1]);
				release_freezing();
				return;
			}
            else  if(response[0]==0 || response[0]==1)
			{
                // console.log(response[0]);
                document.getElementById('txtBatchSerialNo').value= response[1];
                document.getElementById('hdnUpdateId').value = response[2];
				document.getElementById('txtBatchNo').value = response[3];

                show_list_view('2**'+response[2], 'populate_dtls_data_from_search_popup', 'material_details','requires/yd_batch_creation_controller', '');

                calculateBatchQty();

                set_button_status(1, permission, 'saveUpdateDelete', 1);
            }

            release_freezing();
        }
    }

    function fnc_yd_batch_creation(id){

    	var Update_id = $('#hdnUpdateId').val();

    	if(Update_id==''){
    		alert("Please Save Batch First!!!");
    		return;
    	}
		var action="";
		if(id==1){
			action = "yd_batch_creation_print";
		}else if(id==2){
			action = "yd_batch_creation_print_2";
		}else if(id==3){
			action = "yd_batch_creation_print_3";
		}else{
			alert("Report Id Missing.");return;
		}
    	print_report($('#cbo_company_name').val()+'*'+$('#hdnUpdateId').val()+'*'+$('#txtBatchSerialNo').val()+'*'+id, action, "requires/yd_batch_creation_controller" );
    	return;
    }
    // function fnc_yd_batch_creation_2()
	// {
	// 	var Update_id = $('#hdnUpdateId').val();

	// 	if(Update_id==''){
	// 		alert("Please Save Batch First!!!");
	// 		return;
	// 	}

	// 	print_report($('#cbo_company_name').val()+'*'+$('#hdnUpdateId').val()+'*'+$('#txtBatchSerialNo').val(), "yd_batch_creation_print_2", "requires/yd_batch_creation_controller" );
	// 	return;
	// }
</script>

</head>
<body>
<div>
	<?php echo load_freeze_divs('../../', $permission); ?>	
		<fieldset style="width:75%; margin: 0 auto;">
			<legend>Yarn Dyeing Batch Creation </legend>
			<table style="margin:10px auto; width: 100%;">
				<form name="yarnDyingBatchCreation_1" id="yarnDyingBatchCreation_1" autocomplete="off">
					<tr>
						<td align="right" colspan="3">Batch Serial No:</td>
						<td align="left" colspan="2">
							<input class="text_boxes" type="text" placeholder="Browse" name="txtBatchSerialNo" id="txtBatchSerialNo" readonly onDblClick="openBatchPopup();" />
						</td>
					</tr>
					<tr>
						<td colspan="4">&nbsp;</td>
					</tr>
					<tr>
						<td align="right" class="must_entry_caption">Company Name:</td>
						<td>
							<?php
							 	echo create_drop_down('cbo_company_name', 150, "select id, company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", 'id,company_name', 1, '-- Select Company --', $selected, "get_php_form_data(this.value,'batch_no_creation','requires/yd_batch_creation_controller' );load_drop_down( 'requires/yd_batch_creation_controller', this.value, 'load_drop_down_location', 'location_td' );");
							?>
						</td>
						<td align="right" class="must_entry_caption">Batch Color:</td>
						<td>
							<input class="text_boxes" style="width:137px;" type="text" placeholder="Browse" name="txtBatchColor" id="txtBatchColor" onDblClick="openColorPopup();" readonly />
						</td>
                        <td align="right" class="must_entry_caption">Batch Against:</td>
						<td>
							<?php
							// create_drop_down($field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name, $additionalClass, $additionalAttributes)
							 	echo create_drop_down('cbo_batch_against', 150, $batch_against, '', 1, '-- Select --', $selected, '', '', '1,2');
							?>
						</td>
					</tr>
					<tr>  
						<td align="right">Color Range:</td>
						<td>
							<!--<input class="text_boxes" style="width:137px;" type="text" name="txtColorRange" id="txtColorRange" /-->
                             <? echo   create_drop_down( "txtColorRange", 147, $color_range,"", 1, "-- Select --",0,"",0,'','','','','','',"txtColorRange[]")   ?>
						</td> 
						<td align="right">Batch Number:</td>
						<td>
							<input class="text_boxes" style="width:137px;" type="text" name="txtBatchNo" id="txtBatchNo" />
						</td>
						<td align="right" class="must_entry_caption">Batch Weight:</td>
						<td>
							<input class="text_boxes_numeric" style="width:137px;" type="text" name="txtBatchWeight" id="txtBatchWeight" readonly />
						</td>
					</tr>
					<tr>
						<td align="right">Extention No:</td>
						<td>
							<input class="text_boxes_numeric" style="width:137px;" type="text" name="txtExtnNo" id="txtExtnNo" />
						</td>
						<td align="right" class="must_entry_caption">Batch Date:</td>
						<td>
							<input class="datepicker"  style="width:137px;"  type="text" name="txtBatchDate" id="txtBatchDate" />
						</td>
                        <td align="right">Process Name:</td>
						<td>
							<?php
							 	echo create_drop_down('cbo_process', 150, $conversion_cost_head_array, '', 1, '-- Select --', $selected, '', '', '35,133,148,137,207,84,156,209,93,220,221,230,231,232,233,234,235,236,237');
							?>
						</td>
					</tr>
					<tr> 
						<td align="right">Duration Req:</td>
						<td>
							<input class="text_boxes" style="width:137px;" type="text" name="txtDurationReq" id="txtDurationReq" />
						</td> 
						<td align="right">Location:</td>
						<td id="location_td">
							<?php
							 	echo create_drop_down('cbo_location_name', 150, $blank_array, '', 1, '-- Select Location --', $selected, '');
							 	// echo create_drop_down("cbo_location_name", 172, $blank_array,"", 1, "-- Select Location --", $selected, "" );
							?>
						</td>
						<td align="right">Machine No:</td>
						<td id="machine_td">
							<?php
							 	echo create_drop_down('cbo_machine', 150, $blank_array, '', 1, '-- Select Machine --', $selected, '');
							?>
						</td>
					</tr>
					<tr>
						<td align="right">Remarks:</td>
						<td colspan="5">
							<input class="text_boxes" style="width:93%;" type="text" name="txtRemarks" id="txtRemarks" />
						</td>
					</tr>
					<tr>
						<td colspan="2" style="padding:10px;">&nbsp;</td>
						<input type="hidden" name="hdnJobMstId" id="hdnJobMstId">
						<input type="hidden" name="hdnJobDtlsId" id="hdnJobDtlsId">
						<input type="hidden" name="hdnColorId" id="hdnColorId">
						<input type="hidden" name="hdnUpdateId" id="hdnUpdateId">
						<input type="hidden" name="hdnOrderId" id="hdnOrderId">
						<input type="hidden" name="hdnOrderNo" id="hdnOrderNo">
						<input type="hidden" name="hdnBookingWithoutOrd" id="hdnBookingWithoutOrd">
						<input type="hidden" name="hdnBookingType" id="hdnBookingType">
                        <input type="hidden" name="batch_no_creation" id="batch_no_creation" readonly>
					</tr>
				</form>	
			</table>
		</fieldset>
	
	<div style="width:85%; margin:20px auto;" id="material_details" class="material_details"></div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
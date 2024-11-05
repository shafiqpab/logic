<?php
/*--- ----------------------------------------- Comments
Purpose			:	This page will display yarn dyeing production
Functionality	:
JS Functions	:
Created by		:	Sapayth
Creation date 	:	10-02-2020
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

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents('Dyeing Production For Y/D', '../../', 1, 1, $unicode, 0, '');
?>
<script type="text/javascript">
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php"; 
	var permission="<?php echo $permission; ?>";
	function openBatchPopup() {
		if(!form_validation('cbo_company_id', 'Company Name') ) {
            return; // returning to the form if required fields is not done
        }
    	var data = document.getElementById('cbo_company_id').value;
    	var pageLink = 'requires/yd_production_controller.php?action=batch_no_popup&data='+data;
    	var title = 'Search Batch No';
        batchPopup = dhtmlmodal.open('Batch No', 'iframe', pageLink, title, 'width=700px, height=500px, center=1, resize=0, scrolling=0', '../');
        batchPopup.onclose = batchPopupCloseHandler;
    }

    function batchPopupCloseHandler()
    {
    	freeze_window(1);

		var theform = this.contentDoc.forms[0];
        var batchMstId = this.contentDoc.getElementById('hdnBatchMstId').value;

        var reqType = 1;
        // data as 1**12345
        get_php_form_data(reqType+'**'+batchMstId, 'populate_mst_data_from_search_popup', 'requires/yd_production_controller');
        show_list_view(reqType+'**'+batchMstId, 'populate_dtls_data_from_search_popup', 'material-details','requires/yd_production_controller', '');

        document.getElementById('cbo_company_id').setAttribute('disabled', 'disabled');
        document.getElementById('txtBatchNo').setAttribute('disabled', 'disabled');
        document.getElementById('txtBatchId').setAttribute('disabled', 'disabled');
        document.getElementById('txtYDColor').setAttribute('disabled', 'disabled');
        document.getElementById('txtExtNo').setAttribute('disabled', 'disabled');
        // calculateBatchQty();
        // set_button_status(1, permission, 'saveUpdateDelete', 1);

        release_freezing();
	}

	function put_data_into_dtls(ydBatchDtlsId)
	{
		var reqType = 1;
		get_php_form_data(reqType+'**'+ydBatchDtlsId, 'populate_mst_data_from_batchlist', 'requires/yd_production_controller');

        document.getElementById('txtStyle').setAttribute('disabled', 'disabled');
        document.getElementById('txtSalesOrderNo').setAttribute('disabled', 'disabled');
        document.getElementById('hdnProductionMstId').value = '';

        set_button_status(0, permission, 'saveUpdateDelete', 1);
	}

	function saveUpdateDelete(operation)
	{
        if (!form_validation('cbo_load_unload*txtLoadingDate*txtLoadingHour*txtLoadingMinute*txtProcessStartDate*txtProcessStartHour*txtProcessStartMinute', 'Load/Un-load*Loading Date*Loading Time*Process Start Date*Process Time*From List')) {
            return;
        }

        freeze_window(operation);

        // console.log(totalRow);

        dataStr=get_submitted_data_string('cbo_company_id*cbo_load_unload*cbo_service_source*cbo_service_company*cbo_process*txtProcessStartDate*txtProcessStartHour*txtProcessStartMinute*txtRemarks*cbo_floor_name*cbo_machine_id*machine_group_id*txtLoadingDate*txtLoadingHour*txtLoadingMinute*cbo_party*cbo_location_name*txtSalesOrderNo*hdnYdOrdId*hdnProductionMstId*hdnUpdateId*hdnProductionId*hdnSalesOrderId*hdnProductId*hdnBookingWithoutOrder*hdnBookingType*txtBatchId', '../');
                
        var data='action=save_update_delete&operation='+operation+dataStr;        
        http.open('POST', 'requires/yd_production_controller.php', true);
        http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        http.send(data);
        http.onreadystatechange = saveUpdateDeleteResponseHandler;
        release_freezing();
    }

    function saveUpdateDeleteResponseHandler()
    {
    	// freeze_window(operation);
		if(http.readyState == 4) {
        	var reqType = 2;
            var response=trim(http.responseText).split('**');
            show_msg(response[0]);
            if(response[0]==0 || response[0]==1) {
                // console.log(response[0]);
                document.getElementById('hdnProductionId').value= response[1];
                document.getElementById('hdnProductionMstId').value = response[2];

                show_list_view('2**'+response[2], 'create_production_list', 'production-list', 'requires/yd_production_controller', '');
                // show_list_view( data, action, div, path, extra_func, is_append )

                // calculateBatchQty();

                // set_button_status(1, permission, 'saveUpdateDelete', 1);
            }

            release_freezing();
        }
    }

    function fnc_move_cursor(val,id, field_id,lnth,max_val)
    {
		var str_length=val.length;
		if(str_length==lnth)
		{
			$('#'+field_id).select();
			$('#'+field_id).focus();
		}
		if(val>max_val) {
			document.getElementById(id).value=max_val;
		}
	}

	function populateProductionData(prodItemId)
	{
		var reqType = 2;
		document.getElementById('hdnUpdateId').value= prodItemId;
		get_php_form_data(reqType+'**'+prodItemId, 'populate_mst_data_from_productionlist', 'requires/yd_production_controller');

		set_button_status(1, permission, 'saveUpdateDelete', 1);
	}

</script>
</head>
<body>
<div>
	<?php echo load_freeze_divs('../', $permission); ?>
	<div style="width: 100%; display: inline-flex;">
		<div style="width: calc(55% - 25px); margin-right: 25px;">
			<form name="yarndyeing_1" id="yarndyeing_1" autocomplete="off">
				<fieldset style="width: 100%; position:relative;">
					<table width="100%">
						<tr width="400">
							<td>
								<!-- left table start -->
								<legend style="width:90%;">Yarn Dyeing Production</legend>							
								<table border="0">
									<tr>
										<td align="right" class="must_entry_caption">Load/Un-load</td>
										<td>
											<?php echo create_drop_down('cbo_load_unload', 140, $loading_unloading, '', 1, '-- Select --', $selected, ''); ?>											
										</td>
									</tr>
									<tr>
										<td align="right">Batch No</td>
										<td>
											<input class="text_boxes" style="width:137px" type="text"  placeholder="Double Click" name="txtBatchNo" id="txtBatchNo" ondblclick="openBatchPopup();" />
										</td>
									</tr>
									<tr>
										<td align="right">Company</td>
										<td>
											<?php
											 	echo create_drop_down('cbo_company_id', 140, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, '--Select Company--', $selected, "load_drop_down('requires/yd_production_controller', this.value, 'load_drop_down_location', 'location_td');", '', '', '', '', '',3);
											?>
										</td>
									</tr>
									<tr>
										<td align="right">Service Source</td>
										<td>
											<?php
											 	echo create_drop_down('cbo_service_source', 140, $knitting_source, '', 1, '-- Select Source --', $selected, '');
											?>
										</td>
									</tr>
									<tr>
										<td align="right">Service Company</td>
										<td>
											<?php
											 	echo create_drop_down('cbo_service_company', 140, $blank_array, '', 1, '-- Select Company --', $selected, '');
											?>
										</td>
									</tr>
									<tr>
										<td align="right">Received Challan</td>
										<td>
											<input class="text_boxes" style="width:137px"  type="text"  name="txtReceivedChallan" id="txtReceivedChallan" />
										</td>
									</tr>
									<tr>
										<td align="right">Process</td>
										<td>
											<?php
											 	echo create_drop_down('cbo_process', 140, $dyeing_sub_process, '', 1, '-- Select Process --', $selected, '');
											?>
										</td>
									</tr>
									<tr>
										<td class="must_entry_caption" align="right">Process Start Date</td>
										<td>
											<input class="datepicker" type="text"  name="txtProcessStartDate" id="txtProcessStartDate" placeholder="Click to show calendar" style="width: 137px;" />
										</td>
									</tr>
									<tr>
										<td class="must_entry_caption" align="right">Process Start Time</td>
										<td>
											<input type="text" name="txtProcessStartHour" id="txtProcessStartHour" class="text_boxes_numeric" placeholder="Hours" style="width:70px;" onKeyUp="fnc_move_cursor(this.value,'txtProcessStartHour','txtProcessStartMinute',2,23)" />
                                			<input type="text" name="txtProcessStartMinute" id="txtProcessStartMinute" class="text_boxes_numeric" placeholder="Minutes" style="width:70px;" onKeyUp="fnc_move_cursor(this.value,'txtProcessStartMinute','txt_end_date',2,59)" />
										</td>
									</tr>
									<tr>
										<td align="right">Machine Name</td>
										<td id="machine_td">
											<?php
											 	echo create_drop_down('cbo_machine_id', 140, $blank_array, '', 1, '-- Machine Name --', $selected, '');
											?>
										</td>
									</tr>
									<tr>
										<td align="right">Remarks</td>
										<td>
											<input class="text_boxes" type="text" style="width:137px"  placeholder="Remarks" name="txtRemarks" id="txtRemarks" />
										</td>
									</tr>
								</table>
								<!-- left table end -->
							</td>
							<td valign="top" align="right">
								<!-- display table start -->
								<legend style="width:90%;">Functional Batch No</legend>
								<table border="0" style="margin-left:15px;">
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td align="right">Batch ID</td>
										<td>
											<input class="text_boxes" style="width:137px" type="text" name="txtBatchId" id="txtBatchId" />
										</td>
										<td class="must_entry_caption" align="right">Loading Date</td>
										<td>
											<input class="datepicker" type="text"  name="txtLoadingDate" id="txtLoadingDate" placeholder="Click to show calendar" style="width: 137px;" />
										</td>
									</tr>
									<tr>
										<td align="right">Ext No.</td>
										<td>
											<input class="text_boxes" style="width:137px" type="text" name="txtExtNo" id="txtExtNo"/>
										</td>
										<td class="must_entry_caption" align="right">Loading Time</td>
										<td>
											<input type="text" name="txtLoadingHour" id="txtLoadingHour" class="text_boxes_numeric" placeholder="Hours" style="width:70px;" onKeyUp="fnc_move_cursor(this.value,'txtLoadingHour','txtLoadingMinute',2,23)" />
                                			<input type="text" name="txtLoadingMinute" id="txtLoadingMinute" class="text_boxes_numeric" placeholder="Minutes" style="width:70px;" onKeyUp="fnc_move_cursor(this.value,'txtLoadingMinute','txt_end_date',2,59)" />
										</td>
									</tr>
									<tr>
										
									</tr>
									<tr>
										<td align="right">Buyer/Party</td>
										<td>
											<?php
											 	echo create_drop_down( "cbo_party", 140, $blank_array, "", 1, "-- Select Party --", $selected, "");
											?>
										</td>										
										<td align="right">Location</td>
										<td id="location_td">
											<?php
												echo create_drop_down('cbo_location_name', 140, $blank_array, '', 1, '--Select Location--', $selected, '', '', '', '', '', '', 4);
		                                	?>
										</td>
									</tr>
									<tr>
										<td align="right">Style No.</td>
										<td>
											<input class="text_boxes_numeric" style="width:137px" type="text"  name="txtStyle" id="txtStyle" />
										</td>		
										<td align="right">M/C Floor</td>
										<td id="floor_td">
		                                	<?php echo create_drop_down('cbo_floor_name', 140, $blank_array, '', 1, '-- Select Floor --', 0, '',0 ); ?>
										</td>
									</tr>
									<tr>
										<td align="right">Sales Order No.</td>
										<td>
											<input class="text_boxes" style="width:137px" type="text" name="txtSalesOrderNo" id="txtSalesOrderNo" />
										</td>						
										<td align="right">M/C Group</td>
										<td id="machine_group_td">
											<?php echo create_drop_down('machine_group_id', 140, $blank_array, '', 1, '-- Select Machine --', 0, '',0 ); ?>
										</td>
									</tr>
									<tr>
										<td align="right">Y/D Color</td>
										<td>
											<input class="text_boxes" style="width:157px" type="text" name="txtYDColor" id="txtYDColor" />
										</td>
									</tr>
								</table>
								<!-- display table end -->
							</td>
						</tr>
						<tr> <!-- this row is to show blank space before the submit buttons -->
							<td colspan="2" style="padding:10px;">&nbsp;
								<input type="hidden" name="hdnBatchMstId" id="hdnBatchMstId">
								<input type="hidden" name="hdnOrderId" id="hdnOrderId">
								<input type="hidden" name="hdnYdOrdId" id="hdnYdOrdId">
								<input type="hidden" name="hdnProductionMstId" id="hdnProductionMstId">
								<input type="hidden" name="hdnProductionId" id="hdnProductionId">
								<input type="hidden" name="hdnUpdateId" id="hdnUpdateId">
								<input type="hidden" name="hdnSalesOrderId" id="hdnSalesOrderId">
								<input type="hidden" name="hdnProductId" id="hdnProductId">
								<input type="hidden" name="hdnBookingWithoutOrder" id="hdnBookingWithoutOrder">
								<input type="hidden" name="hdnBookingType" id="hdnBookingType">
							</td>
						</tr>
						<tr style="margin-top:50px;">
							<td colspan="2" align="center">								
								<?php
									echo load_submit_buttons($permission, 'saveUpdateDelete', 0, 0, "reset_form('softconning_1', '', 'txtIssueQty', '', '', '')", 0);
								?>
							</td>
						</tr>
					</table>		
				</fieldset>
			</form>
		</div>
		<div id="material-details" style="width: 45%; float: left"></div>
	</div>
	<div id="production-list" style="display: block; margin-top: 20px;"></div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
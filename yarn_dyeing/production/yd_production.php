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
	function openBatchPopup() 
	{
		
		
		var cbo_company_id = $('#cbo_company_id').val();
		var batch_no = $('#txtBatchNo').val();
		var load_unload = $('#cbo_load_unload').val();
 		if(!form_validation('cbo_company_id*cbo_load_unload', 'Company Name*Load Unload') ) 
		{
            return;  
        }
    	//var data = document.getElementById('cbo_company_id').value;
    	//var pageLink = 'requires/yd_production_controller.php?action=batch_no_popup&data='+data;
		
		pageLink='requires/yd_production_controller.php?action=batch_no_popup&cbo_company_id='+cbo_company_id+'&batch_no='+batch_no+'&load_unload='+load_unload;
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

        dataStr=get_submitted_data_string('cbo_company_id*cbo_load_unload*cbo_service_source*cbo_service_company*cbo_process*txtProcessStartDate*txtProcessStartHour*txtProcessStartMinute*txtRemarks*cbo_floor_name*cbo_machine_id*machine_group_id*txtLoadingDate*txtLoadingHour*txtLoadingMinute*cbo_party*cbo_location_name*txtSalesOrderNo*hdnYdOrdId*hdnProductionMstId*hdnUpdateId*hdnProductionId*hdnSalesOrderId*hdnProductId*hdnBookingWithoutOrder*hdnBookingType*txtBatchId*hdnBatchMstId', '../');
                
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
	
	
	
	
	function check_batch()
	{
		$('#txtBatchId').val('');
		var batch_no=$('#txtBatchNo').val();
 		var cbo_company_id = $('#cbo_company_id').val();
 		$('#cbo_company_id').removeAttr('disabled','disabled');
		if(batch_no!="")
		{
			if (form_validation('cbo_load_unload','Load Unload')==false)
			{
				return;
			}
			var response=return_global_ajax_value( cbo_company_id+"**"+batch_no, 'check_batch_no', '', 'requires/yd_production_controller');
			var response=response.split("_");
			// alert(response[1]);return;
			
			$('#cbo_company_id').val(response[2]);
			if(response[0]==0)
			{
				alert('Batch no not found.');
				$('#txtBatchNo').val('');
				$('#txtBatchId').val('');
				
			}
			else
			{
				//$('#hdnBatchMstId').val(response[1]);
				//$('#txtBatchId').val(response[1]);
 			    var reqType = 1;
				var batchMstId=response[1];
 				get_php_form_data(reqType+'**'+batchMstId, 'populate_mst_data_from_search_popup', 'requires/yd_production_controller');
				show_list_view(reqType+'**'+batchMstId, 'populate_dtls_data_from_search_popup', 'material-details','requires/yd_production_controller', '');
 				document.getElementById('cbo_company_id').setAttribute('disabled', 'disabled');
				document.getElementById('txtBatchNo').setAttribute('disabled', 'disabled');
				document.getElementById('txtBatchId').setAttribute('disabled', 'disabled');
				document.getElementById('txtYDColor').setAttribute('disabled', 'disabled');
				document.getElementById('txtExtNo').setAttribute('disabled', 'disabled');
  				$('#cbo_company_id').focus();//show_dtls_batch_list_view
				$('#cbo_company_id').attr('disabled','disabled');
 			}
		}
 	}
	
	
	$('#txtBatchNo').live('keydown', function(e) {
    if (e.keyCode === 13) {
        e.preventDefault();
		var batch_no=$('#txtBatchNo').val();
		//alert(batch_no);
		// scan_batchnumber(batch_no);
		$('#txtBatchNo').removeAttr('onChange','onChange');// This function Call Off --onChange="check_batch()--;"
		$('#cbo_company_id').focus();
		 check_batch();
    }
});

</script>
</head>
<body>
<div style="width:100%;" align="center">
<? echo load_freeze_divs ("../../",$permission); ?>
    <form name="slittingsqueezing_1" id="slittingsqueezing_1" autocomplete="off" >
    <div style="width:1300px; float:left;">
        <fieldset style="width:1250px;">
        <table cellpadding="0" cellspacing="1" width="1250" border="0" align="left" height="auto" id="master_tbl">
            <tr>
                <td width="19%" valign="top">
                     <fieldset>
                     <legend>Yarn Dyeing Production</legend>
                        <table width="235px" cellpadding="0" cellspacing="2" align="right"  >
                                  <tr>
                                    <td align="center" width="130" class="must_entry_caption">Load/Un-load</td>
                                    <td  style="float:left" width="130">
                                        <?php echo create_drop_down('cbo_load_unload', 135, $loading_unloading, '', 1, '-- Select --', $selected, ''); ?>											
                                    </td>
                                </tr> 
                                <tr>
                                    <td align="right">Batch No</td>
                                    <td>
                                    <input class="text_boxes" style="width:125px" type="text"  placeholder="Double Click" name="txtBatchNo" id="txtBatchNo" onDblClick="openBatchPopup();" onChange="check_batch();" />
                                    </td>
                                </tr>
                                <tr>
										<td align="right">Company</td>
										<td>
											<?php
											 	echo create_drop_down('cbo_company_id', 135, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, '--Select Company--', $selected, "load_drop_down('requires/yd_production_controller', this.value, 'load_drop_down_location', 'location_td');", '', '', '', '', '',3);
											?>
										</td>
									</tr>
									<tr>
										<td align="right">Service Source</td>
										<td>
											<?php
											 	//echo create_drop_down('cbo_service_source', 135, $knitting_source, '', 1, '-- Select Source --', $selected, '');
												echo create_drop_down("cbo_service_source", 135, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'requires/yd_production_controller',this.value+'**'+$('#cbo_company_id').val(),'load_drop_down_knitting_com','dyeing_company_td' );", "", "1,3");//search_populate(this.value);
											?>
										</td>
									</tr>
                                    <tr>
										<td align="right">Service Company</td>
										 <td id="dyeing_company_td">
											<?php
											 	echo create_drop_down('cbo_service_company', 135, $blank_array, '', 1, '-- Select Company --', $selected, '');
											?>
										</td>
									</tr>
									<tr>
										<td align="right">Received Challan</td>
										<td>
											<input class="text_boxes" style="width:125px"  type="text"  name="txtReceivedChallan" id="txtReceivedChallan" />
										</td>
									</tr>
									<tr>
										<td align="right">Process</td>
										<td>
											<?php
											 	echo create_drop_down('cbo_process', 135, $dyeing_sub_process, '', 1, '-- Select Process --', $selected, '');
											?>
										</td>
									</tr>
									 <tr>
										<td class="must_entry_caption" align="right">Process Start Date</td>
										<td>
											<input class="datepicker" type="text"  name="txtProcessStartDate" id="txtProcessStartDate" placeholder="Click to show calendar" style="width: 125px;" />
										</td>
									</tr>
									<tr>
										<td class="must_entry_caption" align="right">Process Start Time</td>
										<td>
											<input type="text" name="txtProcessStartHour" id="txtProcessStartHour" class="text_boxes_numeric" placeholder="Hours" style="width:55px;" onKeyUp="fnc_move_cursor(this.value,'txtProcessStartHour','txtProcessStartMinute',2,23)" />
                                			<input type="text" name="txtProcessStartMinute" id="txtProcessStartMinute" class="text_boxes_numeric" placeholder="Minutes" style="width:55px;" onKeyUp="fnc_move_cursor(this.value,'txtProcessStartMinute','txt_end_date',2,59)" />
										</td>
									</tr>
									<tr>
										<td align="right">Machine Name</td>
										<td id="machine_td">
											<?php
											 	echo create_drop_down('cbo_machine_id', 135, $blank_array, '', 1, '-- Machine Name --', $selected, '');
											?>
										</td>
									</tr>  
                        </table>
                        <div style="width:auto; float:left; min-height:40px; margin:auto" align="center" id="load_unload_container">
                        </div>
                </fieldset>
                </td>
                <td width="1%" valign="top">&nbsp;</td>
                <td width="70%" valign="top">
                    <table cellpadding="0" cellspacing="1" width="100%" border="0" align="left">
                        <tr>
                            <td colspan="3"> <center> <legend>Functional Batch No</legend></center> </td>
                        </tr>
                        <tr>
                            <td width="45%" valign="top">
                                <fieldset style="height:auto;">
                                    <table width="380" align="left" id="tbl_body1" > 
                                        <tr>
                                            <td width="70">Batch ID</td>
                                            <td width="110">
                                                <input class="text_boxes" style="width:100px" type="text" name="txtBatchId" id="txtBatchId" />
                                            </td>
                                            <td width="70">Loading Date</td>
                                            <td width="110">
                                                <input class="datepicker" type="text"  name="txtLoadingDate" id="txtLoadingDate" placeholder="Click to show calendar" style="width: 100px;" />
                                            </td>  
                                        </tr>
                                        <tr>
                                            <td>Ext. No.</td>
                                            <td>
                                               <input class="text_boxes" style="width:100px" type="text" name="txtExtNo" id="txtExtNo"/>
                                            </td>
                                            <td class="must_entry_caption">Loading Time</td>
                                            <td>   
                                                <input type="text" name="txtLoadingHour" id="txtLoadingHour" class="text_boxes_numeric" placeholder="Hours" style="width:40px;" onKeyUp="fnc_move_cursor(this.value,'txtLoadingHour','txtLoadingMinute',2,23)" />
                                			<input type="text" name="txtLoadingMinute" id="txtLoadingMinute" class="text_boxes_numeric" placeholder="Minutes" style="width:40px;" onKeyUp="fnc_move_cursor(this.value,'txtLoadingMinute','txt_end_date',2,59)" />
                                            </td>   
                                        </tr>
                                        <tr>
                                            <td align="left">Buyer/Party</td>
                                            <td>
                                            <?php
                                            	echo create_drop_down( "cbo_party", 110, $blank_array, "", 1, "-- Select Party --", $selected, "");
                                            ?>
                                            </td>										
                                            <td align="left">Location</td>
                                            <td id="location_td">
                                            <?php
                                            		echo create_drop_down('cbo_location_name', 110, $blank_array, '', 1, '--Select Location--', $selected, '', '', '', '', '', '', 4);
                                            ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left">Style No.</td>
                                            <td>
                                            <input class="text_boxes_numeric" style="width:100px" type="text"  name="txtStyle" id="txtStyle" />
                                            </td>		
                                            <td align="left">M/C Floor</td>
                                            <td id="floor_td">
                                            <?php echo create_drop_down('cbo_floor_name', 110, $blank_array, '', 1, '-- Select Floor --', 0, '',0 ); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left">Job No/Sales order no.</td>
                                            <td>
                                                <input class="text_boxes" style="width:100px" type="text" name="txtSalesOrderNo" id="txtSalesOrderNo" />
                                            </td>						
                                            <td align="left">M/C Group</td>
                                            <td id="machine_group_td">
                                                <?php echo create_drop_down('machine_group_id', 110, $blank_array, '', 1, '-- Select Machine --', 0, '',0 ); ?>
                                            </td>
                                        </tr>
                                         
                                       <tr>
										<td align="left">Y/D Color</td>
										<td>
											<input class="text_boxes" style="width:100px" type="text" name="txtYDColor" id="txtYDColor" />
										</td>
									</tr> 
                                    <tr>
                                        <td width="100">Remarks:</td>
                                        <td colspan="3">
                                            <input type="text" name="txtRemarks" id="txtRemarks" class="text_boxes" style="width:280px;"    />    
                                        </td>
                                    </tr>
                                    </table>
                                </fieldset>
                              </td>
                            <td width="1%" valign="top">&nbsp;</td>
                            <td width="54%" valign="top">
                                 
                                   <div id="material-details" style="width: 45%; float: left"></div>
                               
                            </td>
                        </tr>
                    </table>
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
             <tr>
                <td align="center" colspan="4" class="button_container">
                    <?
                        echo load_submit_buttons($permission, 'saveUpdateDelete', 0, 0, "reset_form('softconning_1', '', 'txtIssueQty', '', '', '')", 0);
                    ?>
                </td>
            </tr>
         </table>
        </fieldset>
         <br>
        <div id="production-list" style="display: block; margin-top: 20px;"></div>
        </div>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
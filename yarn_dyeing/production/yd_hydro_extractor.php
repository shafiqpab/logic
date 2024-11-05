<?php
/*--- -----------------------------------------
Purpose			:	This page will display hydro extractor
Functionality	:
JS Functions	:
Created by		:	Samiur
Creation date 	:	09-06-2020
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
echo load_html_head_contents("Hydro Extractor", "../../", 1, 1, $unicode, 0, "");
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php"; 
	var permission="<? echo $permission; ?>";

	function put_data_into_dtls(id, action, controller_path) { // function copied from gray_production_entry put_data_dtls_part
		/*alert(id);return;*/
		freeze_window();
		reset_form('','','txtTemperature*txtSpeed*txtWindingPckg*txtPrdctnQty*txtRemarks','','');
		get_php_form_data(id, 'populate_hydro_extractor_from_list', 'requires/yd_hydro_extractor_controller');
		var production_id=document.getElementById('txtProductionId').value;
		set_button_status(0, permission, 'fnc_hydro_extractor',1);
		/*alert(production_id);*/
		/*if(production_id==''){
			

		}else{
			set_button_status(1, permission, 'fnc_hydro_extractor',1);

		}*/
		
		release_freezing();
	}

	function put_data_into_form(id, action, controller_path) { // function copied from gray_production_entry put_data_dtls_part
		/*alert(id);return;*/
		freeze_window();
		$('#cbo_company_name').removeAttr('disabled');
		$('#cbo_party_name').removeAttr('disabled');
		$('#txtProductionDate').removeAttr('disabled');
		$('#txtStartDate').removeAttr('disabled');
		$('#txtEndDate').removeAttr('disabled');
		
		document.getElementById('update_dtls_id').value = id;
		reset_form('','','txtWindingPckg*txtTemperature*txtSpeed*txtPrdctnQty*txtRemarks','','');
		get_php_form_data(id, 'populate_saved_data_from_list', 'requires/yd_hydro_extractor_controller');

		set_button_status(1, permission, 'fnc_hydro_extractor',1);
		release_freezing();
	}
	function openmypage_production_id()
    { 
        var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_party_name').value;
        var page_link='requires/yd_hydro_extractor_controller.php?action=production_popup&data='+data;
        var title="Issue ID";
        
        emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=900px, height=400px, center=1, resize=0, scrolling=0','../')
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0]//("search_subcontract_frm"); //Access the form inside the modal window
            //var theemail=this.contentDoc.getElementById("selected_job");
            var theemail=this.contentDoc.getElementById("selected_production").value;
            /*alert (theemail); */
            var splt_val=theemail.split("_");

           /* alert(splt_val);*/
            if (splt_val[0]!='') {
				
				/*reset_form('','','txtSalesOrder*cbo_company_name*cbo_location_name*cbo_party_name*txtStyle*txtProductionDate*txtStartDate*txtEndDate','','');*/

				show_list_view(splt_val[1], 'create_sales_order_list', 'sales_order_list', 'requires/yd_hydro_extractor_controller', '');
				get_php_form_data( splt_val[0], "load_php_data_to_form", "requires/yd_hydro_extractor_controller" );
				show_list_view(splt_val[0], 'create_production_order_list', 'production_list', 'requires/yd_hydro_extractor_controller', '');

				set_button_status(1, permission, 'fnc_hydro_extractor',1);



				/*alert("hello");return;*/
				/*show_list_view(job_id_mst, 'create_sales_order_list', 'sales_order_list', 'requires/soft_conning_production_entry_controller', '');*/
				// show_list_view( data, action, div, path, extra_func, is_append )
				/*release_freezing();*/
				/*reset_form('softconning_1', '', 'txtIssueQty', '', '', '');*/
				/*document.getElementById('cbo_company_name').value=data;
				$('#cbo_company_name').attr('disabled','disabled');*/
				//reset_form( forms, divs, fields, default_val, extra_func, non_refresh_ids )
			}
        }
    }
    
    /*function openmypage_batch_no()
    { 
    	if( form_validation('txtSalesOrder', 'Sales Order No')==false ) {
			return;
		}
        var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_party_name').value;
        var page_link='requires/yd_hydro_extractor_controller.php?action=batchno_popup&data='+data;
        var title="Batch No Pop-Up Info";
        
        emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=900px, height=400px, center=1, resize=0, scrolling=0','')
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0]//("search_subcontract_frm"); //Access the form inside the modal window
            //var theemail=this.contentDoc.getElementById("selected_job");
            var theemail=this.contentDoc.getElementById("selected_batch_no").value;
            
            var splt_val=theemail.split("_");

            if (splt_val[0]!='') {
				
				//reset_form('','','txtSalesOrder*cbo_company_name*cbo_location_name*cbo_party_name*txtStyle*txtProductionDate*txtStartDate*txtEndDate','','');
				freeze_window();
				document.getElementById('txtBatchNo').value=splt_val[1];
				release_freezing();
			}
        }
    }*/

	function openmypage_batch_no() {
		if( form_validation('cbo_company_name', 'Company')==false ) {
			return;
		}
		var data = document.getElementById('cbo_company_name').value;

		page_link='requires/yd_hydro_extractor_controller.php?action=salesorder_popup&data='+data;
		title='Search by Sales Order Number';

		popupWindow=dhtmlmodal.open('searchBox', 'iframe', page_link, title, 'width=535px, height=350px, center=1, resize=0, scrolling=0', '../')
		popupWindow.onclose=function() {
			var theform=this.contentDoc.forms[0];
			var job_id_mst=this.contentDoc.getElementById('selected_order_id').value;
			//var batch_no=this.contentDoc.getElementById('selected_batch_no').value;
		//alert(batch_no);
			if (job_id_mst!='') {
				freeze_window(5);
				// document.getElementById('txtSalesOrder').value = job_id_mst;
				// document.getElementById('txtSalesOrder').setAttribute('disabled', 'disabled');
				// document.getElementById('cbo_company_name').setAttribute('disabled', 'disabled');
				// get_php_form_data(job_id_mst, 'populate_data_from_search_popup', 'requires/soft_conning_production_entry_controller');
				show_list_view(job_id_mst, 'create_sales_order_list', 'sales_order_list', 'requires/yd_hydro_extractor_controller', '');
				// show_list_view( data, action, div, path, extra_func, is_append )
				release_freezing();
				/*reset_form('softconning_1', '', 'txtIssueQty', '', '', '');*/
				document.getElementById('cbo_company_name').value=data;
				$('#cbo_company_name').attr('disabled','disabled');
				//reset_form( forms, divs, fields, default_val, extra_func, non_refresh_ids )
			}
		}
	}
	function fnc_hydro_extractor( operation )
    {
        if ( form_validation('txtPrdctnQty', 'Production Qty')==false )
        {
           return;
        } else {  
	        var data_str="";

	        var data_str=get_submitted_data_string('txtProductionId*update_id*sales_order_serial*update_dtls_id*job_dtls_id*order_id*job_id*within_group*txtBatchNo*txtStyle*txtSalesOrder*cbo_company_name*cbo_party_name*txtProductionDate*txtStartDate*txtEndDate*cbo_yarn_type*cbo_bobbin_type*txtTemperature*txtSpeed*txtWindingPckg*txtPrdctnQty*txtRemarks*txtLot*txtCount*txtComposition*txtYdColor*txtDyePrdcQty*txtPrdPckgQty*txtYarnHydQty*txtPckgQty*booking_without_order*booking_type*sales_order_id*product_id*batch_id',"../../");
	        //alert(data_str);
	        
	        var data="action=save_update_delete&operation="+operation+data_str;//+'&zero_val='+zero_val
	        // alert(data);
	        freeze_window(operation);
	        http.open("POST","requires/yd_hydro_extractor_controller.php",true);
	        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	        http.send(data);
	        http.onreadystatechange = fnc_hydro_extractor_response;
	    }
    }
    
    function fnc_hydro_extractor_response()
    {
        if(http.readyState == 4) 
        {
            /*alert(http.responseText);*/
            var response=trim(http.responseText).split('**');
            //if (response[0].length>3) reponse[0]=10;  
            
            show_msg(response[0]);
            //$('#cbo_uom').val(12);
            /*alert(response);return;*/

            if(response[0]==0 || response[0]==1)
            {
                //here
               /*alert(response);return;*/
                
                document.getElementById('txtProductionId').value= response[1];
                document.getElementById('update_id').value = response[2];
                /*document.getElementById('sales_order_serial').value="";*/
                if(response[0]==0 ){

                	document.getElementById('sales_order_serial').value="";
                }
                document.getElementById('txtPrdctnQty').value="";
            	document.getElementById('txtLot').value="";
            	document.getElementById('txtCount').value="";
            	document.getElementById('txtComposition').value="";
            	document.getElementById('txtYdColor').value="";
            	document.getElementById('update_dtls_id').value="";

                /*set_button_status(1, permission, 'fnc_hydro_extractor',1);*/

               var job_id_mst=response[2];
                
                
                show_list_view(job_id_mst, 'create_production_order_list', 'production_list', 'requires/yd_hydro_extractor_controller', '');
               /* if(list_view_orders!='')
                {
                    $("#rec_issue_table tr").remove();
                    $("#rec_issue_table").append(production_list);
                }*/
               /* fnc_total_calculate();*/
                 //release_freezing();
            }
            if(response[0]==2)
            {
                reset_fnc();
            }
            release_freezing();
        }
    }
    function reset_fnc()
    {
        location.reload(); 
    }
     
     function ResetForm()
    {
        reset_form('yarnissue_1','issue_list_view','','cbouom_1,1', "$('#details_tbl tbody tr:not(:first)').remove(); disable_enable_fields('cbo_company_name*cbo_within_group*cbo_party_name',0)")
    }
</script>
</head>
<body onLoad="set_hotkey();">
<div>
	<?php echo load_freeze_divs("../../", $permission); ?>
	<form name="hydroextractor_1" id="hydroextractor_1" autocomplete="off">
		<fieldset style="width:700px; margin-left:20px; position:relative; float: left;">
			<!-- calc(50% - 20px); -->
			<table width="100%">
				<tr>
					<td>
						<!-- left table start -->
						<legend style="width:90%;">Hydro Extractor</legend>							
						<table border="0">
							<tr>
								<td align="right">Production ID</td>
								<td>
									<input class="text_boxes" type="text" placeholder="Double click" onDblClick="openmypage_production_id('xx','Hydro Extractor')" name="txtProductionId" id="txtProductionId"  style="width:157px;" readonly/>
									<input type="hidden" name="update_id" id="update_id">
									<input type="hidden" name="sales_order_serial" id="sales_order_serial">
									<input type="hidden" name="update_dtls_id" id="update_dtls_id">
									<input type="hidden" name="job_dtls_id" id="job_dtls_id">
									<input type="hidden" name="order_id" id="order_id">
									<input type="hidden" name="job_id" id="job_id">
									<input type="hidden" name="within_group" id="within_group">
									<input type="hidden" name="booking_without_order" id="booking_without_order">
									<input type="hidden" name="booking_type" id="booking_type">
									<input type="hidden" name="sales_order_id" id="sales_order_id">
									<input type="hidden" name="product_id" id="product_id">
									
									
								</td>
							</tr>
							<tr>
								<td align="right" class="must_entry_caption">Batch No:</td>
								<td>
									<input class="text_boxes" type="text" placeholder="Double click" onDblClick="openmypage_batch_no('xx','Hydro Extractor')" name="txtBatchNo" id="txtBatchNo"  style="width:157px;" readonly/>
									<input type="hidden" name="batch_id" id="batch_id">
								</td>
							</tr>
							<tr>
								<td align="right">Style No</td>
								<td>
									<input class="text_boxes"  style="width:157px;" type="text" name="txtStyle" id="txtStyle" readonly/>
								</td>
							</tr>
							<tr>
								<td align="right">Job No/Sales order no.</td>
								<td>
									<input class="text_boxes"  style="width:157px;" type="text"  placeholder="Display"  name="txtSalesOrder" id="txtSalesOrder" readonly />
								</td>
							</tr>
							<tr>
								<td align="right" class="must_entry_caption">Company Name</td>
								<td id="company_td">
									<? echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/yd_hydro_extractor_controller', '', 'load_drop_down_buyer', 'party_td' );"); ?>
								</td>
							</tr>
							<tr>
								<td align="right">Party</td>
								<td id="party_td">
									<? echo create_drop_down( "cbo_party_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, ""); ?>
								</td>
							</tr>
							<tr>
								<td align="right">Production Date</td>
								<td>
									<input class="datepicker" type="text"  name="txtProductionDate" id="txtProductionDate" style="width:157px;" placeholder="Click to show calendar" />
								</td>
							</tr>
							<tr>
								<td align="right">Start Date</td>
								<td>
									<input class="datepicker" type="text"  name="txtStartDate" id="txtStartDate" style="width:157px;" placeholder="Click to show calendar" />
								</td>
							</tr>
							<tr>
								<td align="right">End Date</td>
								<td>
									<input class="datepicker" type="text"  name="txtEndDate" id="txtEndDate" style="width:157px;" placeholder="Click to show calendar" />
								</td>
							</tr>
							<tr>
								<td align="right">Yarn Type</td>
								<td>
									<?php
										$yarn_arr = array("Yarn type 1", "Yarn type 2", "Yarn type 3");
									 	echo create_drop_down( "cbo_yarn_type", 170, $yarn_arr, 1, "-- Yarn Type --", $selected, "");
									?>
								</td>
							</tr>
							<tr>
								<td align="right">Bobbin Type</td>
								<td>
									<?php
										$bobbin_arr = array("Bobbin Type 1", "Bobbin Type 2", "Bobbin Type 3");
									 	echo create_drop_down( "cbo_bobbin_type", 170, $bobbin_arr, 1, "-- Bobbin Type --", $selected, "");
									?>
								</td>
							</tr>
							<tr>
								<td align="right">Temperature:</td>
								<td>
									<input class="text_boxes" type="text"  placeholder="Write" name="txtTemperature" id="txtTemperature" style="width:157px;"  />
								</td>
							</tr>
							<tr>
								<td align="right">Speed:</td>
								<td>
									<input class="text_boxes" type="text"  placeholder="Write" name="txtSpeed" id="txtSpeed" style="width:157px;" />
								</td>
							</tr>
							<tr>
								<td align="right">Winding Package Qty:</td>
								<td>
									<input class="text_boxes_numeric" type="text"  placeholder="0" name="txtWindingPckg" id="txtWindingPckg" style="width:157px;" />
								</td>
							</tr>
							<tr>
								<td align="right" class="must_entry_caption">Production Qty</td>
								<td>
									<input class="text_boxes_numeric" type="text"  placeholder="Production Qty" name="txtPrdctnQty" id="txtPrdctnQty" style="width:157px;" />
								</td>
							</tr>
							<tr>
								<td align="right">Remarks</td>
								<td>
									<input class="text_boxes" type="text" placeholder="Remarks" name="txtRemarks" id="txtRemarks" style="width:157px;" />
								</td>
							</tr>
						</table>
						<!-- left table end -->
					</td>
					<td valign="top" align="right">
						<!-- display table start -->
						<legend style="width:90%;">Display</legend>
						<table border="0" style="margin-left:15px;">
							<tr>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<td align="right">Lot:</td>
								<td>
									<input class="text_boxes"  style="width:157px;" type="text"  placeholder="Display" name="txtLot" id="txtLot" readonly />
								</td>
							</tr>
							<tr>
								<td align="right">Count:</td>
								<td>
									<input class="text_boxes_numeric"  style="width:157px;" type="text"  placeholder="Display" name="txtCount" id="txtCount" readonly/>
								</td>
							</tr>
							<tr>
								<td align="right">Composition:</td>
								<td>
									<input class="text_boxes"  style="width:157px;" type="text"  placeholder="Display" name="txtComposition" id="txtComposition" readonly/>
								</td>
							</tr>
							<tr>
								<td align="right">Y/D Color:</td>
								<td>
									<input class="text_boxes"  style="width:157px;" type="text"  placeholder="Display" name="txtYdColor" id="txtYdColor" align="left" readonly/>
								</td>
							</tr>
							<tr>
								<td align="right" >Dyeing Production Qty:</td>
								<td>
									<input class="text_boxes_numeric"  style="width:157px;" type="text"  placeholder="Display" name="txtDyePrdcQty" id="txtDyePrdcQty" align="left" readonly/>
								</td>
							</tr>
							<tr>
								<td align="right">Production Package Qty/Pcs:</td>
								<td>
									<input class="text_boxes_numeric"  style="width:157px;" type="text" placeholder="Display"  name="txtPrdPckgQty" id="txtPrdPckgQty" readonly/>
								</td>
							</tr>
							<tr>
								<td align="right">Yarn Hydro Balance Qty:</td>
								<td>
									<input class="text_boxes_numeric"  style="width:157px;" type="text" placeholder="Display"  name="txtYarnHydQty" id="txtYarnHydQty" readonly/>
								</td>
							</tr>
							<tr>
								<td align="right">Balance Package Qty/Pcs:</td>
								<td>
									<input class="text_boxes_numeric"  style="width:157px;" type="text" placeholder="Display"  name="txtPckgQty" id="txtPckgQty" readonly/>
								</td>
							</tr>
						</table>
						<!-- display table end -->
					</td>
				</tr>
				<tr> <!-- this row is to show some blank space before the submit buttons -->
					<td colspan="2" style="padding:10px;">&nbsp;</td>
				</tr>
				<tr style="margin-top:50px;">
					<td colspan="2" align="center">
						<? echo load_submit_buttons($permission, "fnc_hydro_extractor", 0,0,"",1); ?>
					</td>
				</tr>
			</table>		
		</fieldset>
		
	</form>
	<div style="float:left; margin-left:10px; margin-top:5px; width: 500px;" id="sales_order_list">
        
    </div>
    
  <div style="width: 800px" id="production_list"></div>  
   
</div>




</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
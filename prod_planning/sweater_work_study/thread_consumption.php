<form name="gsdentry_4" id="gsdentry_4"  autocomplete="off"  >
    <fieldset style="width:870px; margin-top:5px">
    	<legend>Thread Consumption Info </legend>
        <table cellpadding="0" cellspacing="2" width="100%">
        	<tr>
                <td width="130">Body Size</td>
                <td width="160"><input type="text" name="txt_body_size" id="txt_body_size" class="text_boxes" style="width:128px" placeholder="Write" maxlength="50" title="Maximum Characters 50"/></td>
                <td width="100">Date</td>
                <td width="160"><input type="text" name="txt_cons_date" id="txt_cons_date" placeholder="Select" class="datepicker" style="width:128px;" readonly="readonly" /></td>
                <td width="100" class="must_entry_caption">Input UOM</td>
                <td>
                    <? echo create_drop_down( "cbo_uom", 140, $unit_of_measurement, "", 0, " -- Select -- ", 29, "", 0,'25,29'); ?>
                </td>
			</tr>
            <tr>
            	<td class="must_entry_caption">Breakdown System ID</td>                                              
                <td>
                    <input type="text" id="system_no_tc" name="system_no_tc" class="text_boxes" style="width:128px" placeholder="Dispaly" disabled="disabled"/>
                    <input type="hidden" id="breakdown_id3" name="breakdown_id3"/>
                    <input type="hidden" name="bl3_update_id" id="bl3_update_id" value=""/>
                </td>
                <td>Style Ref.</td>                                              
                <td>
                    <input type="text" id="txt_style_ref_tc" name="txt_style_ref_tc" class="text_boxes" style="width:128px" placeholder="Display" disabled="disabled"/>
                </td>
                <td>Buyer Name</td>                                              
                <td>
                	<? echo create_drop_down( "cbo_buyer_tc", 140, "select id,buyer_name from lib_buyer", "id,buyer_name", 1, " Display ", 0, "", 1); ?>
                </td>
            </tr> 
            <tr>
            	<td>Total Required</td>                                              
                <td>
                    <input type="text" id="txt_tot_required" name="txt_tot_required" class="text_boxes" style="width:128px" placeholder="Dispaly" disabled="disabled"/>
                </td>
                <td>Converted Into Meter</td>                                              
                <td>
                    <input type="text" id="txt_required_into_meter" name="txt_required_into_meter" class="text_boxes" style="width:128px" placeholder="Display" disabled="disabled"/>
                </td>
            </tr> 
        </table>
    </fieldset>
    <fieldset style="width:870px; margin-top:5px">
    	<legend>Operation Info </legend>
        <table cellpadding="0" cellspacing="2" width="100%">
        	<tr>
            	<td width="85" class="must_entry_caption">Operation Name</td>                                              
                <td>
                    <input type="text" id="txt_operation_name" name="txt_operation_name" class="text_boxes" style="width:138px" placeholder="Dispaly" disabled="disabled"/>
                    <input type="hidden" name="operation_id" id="operation_id" value=""/>
                    <input type="hidden" name="dtlsId_gsd" id="dtlsId_gsd" value="">
                    <input type="hidden" name="update_dtlsId" id="update_dtlsId" value="">
                    <input type="hidden" name="rowNo" id="rowNo" value="">
                </td>
                <td>Seam Length</td>
                <td><input type="text" name="txt_seam_length" id="txt_seam_length" class="text_boxes_numeric" style="width:70px;" onkeyup="calculate_thread_all();"/></td>
                <td>Resource</td>
                <td><? echo create_drop_down( "cbo_resource_tc", 130, $production_resource,'', '1',' Display ',0,'',1 ); ?></td>
                <td>Fabrication</td>
                <td>
                    <input type="text" name="txt_fabric_description" id="txt_fabric_description" class="text_boxes" style="width:200px" placeholder="Dispaly" disabled="disabled"/>
                </td>
			</tr>
        </table>
        <br />
        <table cellpadding="0" cellspacing="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_list_search_tc">
        	<thead>
                <th width="60">SL No.</th>
                <th width="140">Thread Type</th>
                <th width="200">Thread Description</th>
                <th width="120">Thread Length</th>
                <th width="120">Allowance</th>
                <th width="120" title="(Seam Length*Thread Length)+{( Seam Length*Thread Length)/100}*Allowance">Required</th>
                <th></th>
            </thead>
            <tbody id="operation_details_thread">
            	<tr id="tr_1" bgcolor="#FFFFFF" align="center">
                	<td align="left">1</td>
                    <td>
                    	<? echo create_drop_down( "cboThreadType_1", 130,$size_color_sensitive,"",1, "-- Select --", 1, "", "", "1,3", "", "", "", "", "", "cboThreadType[]" ); ?>
                    </td>
                    <td><input type="text" name="txtThreadDesc[]" id="txtThreadDesc_1" onFocus="add_auto_complete( 1 )" placeholder="Write" class="text_boxes" style="width:180px" /></td>
                    <td><input type="text" name="txtThreadLength[]" id="txtThreadLength_1" placeholder="Write" class="text_boxes_numeric" style="width:100px" onKeyUp="calculate_thread(1)" /></td>
                    <td><input type="text" name="txtAllowance[]" id="txtAllowance_1" placeholder="Write" class="text_boxes_numeric" style="width:100px" onKeyUp="calculate_thread(1)" onblur="copy_value(this.value,1);" /></td>
                    <td><input type="text" name="txtRequired[]" id="txtRequired_1" class="text_boxes_numeric" style="width:100px" placeholder="Calculative" readonly="readonly" /></td>
                    <td>
                        <input type="button" id="increaseT_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1)"/>
                        <input type="button" id="decreaseT_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
					</td>
                </tr>
                <tr id="tr_2" bgcolor="#E9F3FF" align="center">
                	<td align="left">2</td>
                    <td>
                    	<? echo create_drop_down( "cboThreadType_2", 130,$size_color_sensitive,"",1, "-- Select --", 0, "", "", "1,3", "", "", "", "", "", "cboThreadType[]" ); ?>
                    </td>
                    <td><input type="text" name="txtThreadDesc[]" id="txtThreadDesc_2" onFocus="add_auto_complete(2)" placeholder="Write" class="text_boxes" style="width:180px" /></td>
                    <td><input type="text" name="txtThreadLength[]" id="txtThreadLength_2" placeholder="Write" class="text_boxes_numeric" style="width:100px" onKeyUp="calculate_thread(2)" /></td>
                    <td><input type="text" name="txtAllowance[]" id="txtAllowance_2" placeholder="Write" class="text_boxes_numeric" style="width:100px" onKeyUp="calculate_thread(2)"  onblur="copy_value(this.value,2);" /></td>
                    <td><input type="text" name="txtRequired[]" id="txtRequired_2" class="text_boxes_numeric" style="width:100px" placeholder="Calculative" readonly="readonly" /></td>
                    <td align="center">
                        <input type="button" id="increaseT_2" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(2)"/>
                        <input type="button" id="decreaseT_2" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(2);" />
					</td>
                </tr>
                <tr id="tr_3" bgcolor="#FFFFFF" align="center">
                	<td align="left">3</td>
                    <td>
                    	<? echo create_drop_down( "cboThreadType_3", 130,$size_color_sensitive,"",1, "-- Select --", 0, "", "", "1,3", "", "", "", "", "", "cboThreadType[]" ); ?>
                    </td>
                    <td><input type="text" name="txtThreadDesc[]" id="txtThreadDesc_3" onFocus="add_auto_complete(3)" placeholder="Write" class="text_boxes" style="width:180px" /></td>
                    <td><input type="text" name="txtThreadLength[]" id="txtThreadLength_3" placeholder="Write" class="text_boxes_numeric" style="width:100px" onKeyUp="calculate_thread(3)" /></td>
                    <td><input type="text" name="txtAllowance[]" id="txtAllowance_3" placeholder="Write" class="text_boxes_numeric" style="width:100px" onKeyUp="calculate_thread(3)" onblur="copy_value(this.value,3);"/></td>
                    <td><input type="text" name="txtRequired[]" id="txtRequired_3" class="text_boxes_numeric" style="width:100px" placeholder="Calculative" readonly="readonly" /></td>
                    <td align="center">
                        <input type="button" id="increaseT_3" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(3)"/>
                        <input type="button" id="decreaseT_3" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(3);" />
					</td>
                </tr>
                <tr id="tr_4" bgcolor="#E9F3FF" align="center">
                	<td align="left">4</td>
                    <td>
                    	<? echo create_drop_down( "cboThreadType_4", 130,$size_color_sensitive,"",1, "-- Select --", 0, "", "", "1,3", "", "", "", "", "", "cboThreadType[]" ); ?>
                    </td>
                    <td><input type="text" name="txtThreadDesc[]" id="txtThreadDesc_4" onFocus="add_auto_complete( 4 )" placeholder="Write" class="text_boxes" style="width:180px" /></td>
                    <td><input type="text" name="txtThreadLength[]" id="txtThreadLength_4" placeholder="Write" class="text_boxes_numeric" style="width:100px" onKeyUp="calculate_thread(4)" /></td>
                    <td><input type="text" name="txtAllowance[]" id="txtAllowance_4" placeholder="Write" class="text_boxes_numeric" style="width:100px" onKeyUp="calculate_thread(4)"  onblur="copy_value(this.value,4);" /></td>
                    <td><input type="text" name="txtRequired[]" id="txtRequired_4" class="text_boxes_numeric" style="width:100px" placeholder="Calculative" readonly="readonly" /></td>
                    <td align="center">
                        <input type="button" id="increaseT_4" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(4)"/>
                        <input type="button" id="decreaseT_4" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(4);" />
					</td>
                </tr>
            </tbody>
            <tfoot>
            	<th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>Total</th>
                <th style="text-align:center"><input type="text" name="totReq" id="totReq" class="text_boxes_numeric" style="width:100px" readonly="readonly" /></th>
                <th>&nbsp;</th>
            </tfoot>
        </table>
        <table cellpadding="0" cellspacing="2" width="100%">
            <tr>
                <td colspan="5" align="center" class="button_container">
                    <? echo load_submit_buttons($permission,"fnc_thread_consumption_entry",0,1,"resetFormTC();",4); ?>
                </td>
            </tr>
        </table>
    </fieldset>
</form>
<div id="operation_details_tc" style="margin-top:5px"></div>
<form name="gsdentry_3" id="gsdentry_3"  autocomplete="off"  >
    <fieldset style="width:870px; margin-top:5px">
    	<legend>Operation Balancing2 Sheet Info </legend>
        <table cellpadding="0" cellspacing="2" width="100%">
             <tr>
                <td width="100" class="must_entry_caption">No. Of Worker</td>                                              
                <td width="160">
                     <input type="text" name="txt_worker" id="txt_worker" class="text_boxes_numeric" style="width:128px" placeholder="Write" onblur="list_generate()"/>
                     <input type="hidden" name="breakdown_id2" id="breakdown_id2" value=""/>
                     <input type="hidden" name="bl2_update_id" id="bl2_update_id" value=""/>
                </td>
                <td width="100" class="must_entry_caption">Efficiency</td>
                <td><input type="text" name="txt_efficiency_bl2" id="txt_efficiency_bl2" class="text_boxes_numeric" style="width:128px" placeholder="Write" onkeyup="calculate_total2()"/></td>
                <td width="100">Working Hour</td>                                              
                <td>
                    <input type="text" name="txt_working_hour_bl2" id="txt_working_hour_bl2" class="text_boxes_numeric" style="width:128px" placeholder="Dispaly" disabled="disabled"/>
                </td>
            </tr> 
            <tr>
                <td class="must_entry_caption">Max Work Load %</td>
                <td><input type="text" name="txt_max_wl" id="txt_max_wl" class="text_boxes_numeric" style="width:128px" value="" placeholder="Write"/></td>
                <td class="must_entry_caption">Min Work Load %</td>
                <td><input type="text" name="txt_min_wl" id="txt_min_wl" class="text_boxes_numeric" style="width:128px" value="" placeholder="Write"/></td>
                <td>Total SMV</td>
                <td><input type="text" name="txt_tot_smv2" id="txt_tot_smv2" class="text_boxes" style="width:128px" placeholder="Dispaly" disabled="disabled"/></td>
            </tr>
            <tr>
                <td>Target Qty. per day</td>
                <td><input type="text" name="txt_tgt_per_day" id="txt_tgt_per_day" class="text_boxes_numeric" style="width:128px" disabled="disabled"/></td>
                <td>Cycle Time/Pitch Time</td>
                <td><input type="text" name="txt_pitch_time2" id="txt_pitch_time2" class="text_boxes_numeric" style="width:128px" disabled="disabled"/></td>
                <td>Garments Item</td>
                <td><? echo create_drop_down( "cbo_gmt_item_bl2", 140, $garments_item, "", 1, " Display ", 0, "", 1); ?></td>
            </tr>
            <tr>
                <td>Learning Curve Method</td>
                <td><? echo create_drop_down( "cbo_learning_cub_method_bl2", 140, $complexity_type_tmp,"", 1, "-- Select --", 0, "", 0); ?></td>
            </tr>
        </table>
    </fieldset>
    <fieldset style="width:870px; margin-top:10px">
        <table cellpadding="0" cellspacing="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_operation_list2">
            <thead>
            	<tr id="th_1">
                    <th width="120" rowspan="4">Operation</th>
                    <th width="50" rowspan="4">Seq. No</th>
                    <th width="100" rowspan="4">Resource</th>
                    <th>Worker</th>
                </tr>
                <tr id="th_2">
                	<th>SMV</th>
                </tr>
                <tr id="th_3">
                	<th>Target</th>
                </tr>
                <tr id="th_4">
                	<th>W. Load</th>
                </tr>
            </thead>
            <tbody id="operation_details2">
            </tbody>
            <tfoot>
            	<tr id="tf_1">
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Total</th>
                    <th align="right" id="totSmv2"></th>
                </tr>
            </tfoot>
        </table>
        <table cellpadding="0" cellspacing="2" width="100%">
            <tr>
                <td colspan="5" align="center" class="button_container">
                    <? echo load_submit_buttons($permission,"fnc_balancing2_entry",0,1,"resetForm2();",3); ?>
                </td>
            </tr>
        </table>
        <div style="width:870px; margin-top:5px; height:220px; border:solid 1px">
        	<table style="margin-left:5px; font-size:12px">
            	<tr>
                	<td><b>Work Load Curve</b></td>
                    <td width="50" id="tdtest"></td>
                    <td bgcolor="#BE4B48" width="50"></td>
                    <td width="70">Max Work Load</td>
                    <td bgcolor="#98B954" width="50"></td>
                    <td width="70">Min Work Load</td>
                    <td bgcolor="#7D60A0" width="50"></td>
                    <td>Work Load</td>
                </tr>
            </table>
           <div id="canvas_container2" style="background-color:#FFF"></div>
        </div>
    </fieldset>
</form>
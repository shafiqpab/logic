<form name="gsdentry_2" id="gsdentry_2"  autocomplete="off"  >
    <fieldset style="width:870px; margin-top:5px">
    	<legend>Operation Balancing Sheet Info </legend>
        <table cellpadding="0" cellspacing="2" width="100%">
             <tr>
                <td width="100" class="must_entry_caption">Style Ref.</td>                                              
                <td width="160">
                     <input type="text" id="txt_style_ref_bl" name="txt_style_ref_bl" class="text_boxes" style="width:128px" placeholder="Display" disabled="disabled"/>
                     <input type="hidden" name="breakdown_id" id="breakdown_id" value=""/>
                     <input type="hidden" name="bl_update_id" id="bl_update_id" value=""/>
                </td>
                <td width="100">Buyer Name</td>               
                <td>
                    <? echo create_drop_down( "cbo_buyer_bl", 140, "select id,buyer_name from lib_buyer", "id,buyer_name", 1, " Display ", 0, "", 1); ?>
                </td>
                <td width="100">Garments Item</td>
                <td><? echo create_drop_down( "cbo_gmt_item_bl", 140, $garments_item, "", 1, " Display ", 0, "", 1); ?></td>
            </tr>
            <tr>
                <td>Working Hour</td>
                <td><input type="text" name="txt_working_hour_bl" id="txt_working_hour_bl" class="text_boxes_numeric" style="width:128px" value="" placeholder="Display" disabled="disabled"/></td>
                <td class="must_entry_caption">Allocated MP</td>
                <td><input type="text" name="txt_allocated_mp" id="txt_allocated_mp" class="text_boxes_numeric" style="width:128px" onkeyup="calculate_total()" placeholder="Write"/></td>
                <td>Team No. <!--Line--></td>
                <td><input type="text" name="txt_line_no" id="txt_line_no" class="text_boxes" style="width:128px" placeholder="Write"/></td>
            </tr>
            <tr>
                <td class="must_entry_caption">Efficiency (%)</td>
                <td><input type="text" name="txt_efficiency_bl" id="txt_efficiency_bl" class="text_boxes_numeric" style="width:128px" placeholder="Write" onkeyup="calculate_total()"/></td>
                <td title="Total SMV/Allocated MP">Pitch Time</td>
                <td><input type="text" name="txt_pitch_time" id="txt_pitch_time" class="text_boxes_numeric" style="width:128px" placeholder="Calculative" disabled="disabled"/></td>
                <td title="(60/Pitch Time)*Eff.%">Target</td>
                <td><input type="text" name="txt_target" id="txt_target" class="text_boxes_numeric" style="width:128px" placeholder="Calculative" disabled="disabled"/></td>
            </tr>
            <tr>
                <td>Learning Curve Method</td>
                <td><? echo create_drop_down( "cbo_learning_cub_method_bl", 140, $complexity_type_tmp,"", 1, "-- Select --", 0, "", 0); ?></td>
            </tr>
        </table>
    </fieldset>
    <fieldset style="width:870px; margin-top:10px">
        <table cellpadding="0" cellspacing="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_operation_list">
            <thead>
                <th width="50">Seq. No</th>
                <th width="80">Body Part</th>
                <th width="120">Operation</th>
                <th width="80">Resource</th>
                <th width="50">SMV</th>
                <th width="75" title="60/ Respective Operation SMV">Target (100%)</th>
                <th width="75" title="SMV*60">Cycle Time(s)</th>
                <th width="80" title="Respective Operation SMV/ Pitch Time">Theoretical MP</th>
                <th width="65">
                	<input type="checkbox" id="check_round_up" onclick="calculate_round_up()" title="Round Up on Theoretical MP" /> <span style="font-size:9px;">Round Up</span>
                 	<span style="font-size:11px;">Layout MP</span> 
                </th>
                <th width="65" title="Theoretical MP/Layout MP*100">W. Load %</th>
                <th width="65" title=" Respective operation SMV/Layout MP">Weight</th>
                <th>W. Track</th>
            </thead>
            <tbody id="operation_details">
            </tbody>
            <tfoot>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>Total</th>
                <th align="right" id="totSmv"></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th id="totTheoriticalMp" style="padding-right:3px">&nbsp;</th>
                <th id="totLayOut" style="padding-right:3px"></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tfoot>
        </table>
        <table cellpadding="2" cellspacing="0" width="100%">
        	<tr> 
            	<td width="32%" valign="top">
                	<b>SMV Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%">
                    	<tr bgcolor="#FFFFFF">
                        	<td width="100">Assistant Operator</td>
                            <td id="sh" align="right" style="padding-right:5px">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Sewing Machine</td>
                            <td id="sm" align="right" style="padding-right:5px">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Sewing QI</td>
                            <td id="sq" align="right" style="padding-right:5px">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Finishing I/M</td>
                            <td id="fim" align="right" style="padding-right:5px">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Finishing QI</td>
                            <td id="fq" align="right" style="padding-right:5px">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Poly Helper</td>
                            <td id="ph" align="right" style="padding-right:5px">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Packing</td>
                            <td id="pk" align="right" style="padding-right:5px">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Hand Tag</td>
                            <td id="ht" align="right" style="padding-right:5px">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Iron Man</td>
                            <td id="im" align="right" style="padding-right:5px">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td align="right"><b>Total SMV</b></td>
                            <td id="totSmvSumm" align="right" style="padding-right:5px">&nbsp;</td>
                        </tr>
                    </table>
                </td>
                <td width="1%" valign="top"></td>
                <td width="32%" valign="top">
                	<b>Man Power Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%">
                    	<tr bgcolor="#FFFFFF">
                        	<td width="100">Assistant Operator</td>
                            <td id="shm" align="right" style="padding-right:5px">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Sewing Machine</td>
                            <td id="smm" align="right" style="padding-right:5px">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Sewing QI</td>
                            <td id="sqm" align="right" style="padding-right:5px">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Finishing I/M</td>
                            <td id="fimm" align="right" style="padding-right:5px">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Finishing QI</td>
                            <td id="fqm" align="right" style="padding-right:5px">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Poly Helper</td>
                            <td id="phm" align="right" style="padding-right:5px">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Packing</td>
                            <td id="pkm" align="right" style="padding-right:5px">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Hand Tag</td>
                            <td id="htm" align="right" style="padding-right:5px">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Iron Man</td>
                            <td id="imm" align="right" style="padding-right:5px">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td align="right"><b>Total</b></td>
                            <td id="totMPSumm" align="right" style="padding-right:5px">&nbsp;</td>
                        </tr>
                    </table>
                </td>
                <td width="1%" valign="top"></td>
                <td valign="top">
                	<b>Machine Summary</b>
                	<table border="1" rules="all" class="rpt_table" width="100%" id="tbl_mp_summ">
                    	<!--<tr bgcolor="#FFFFFF">
                        	<td width="100">Sewing Helper</td>
                            <td id="shma" align="right" style="padding-right:5px">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Sewing Machine</td>
                            <td id="smma" align="right" style="padding-right:5px">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Sewing QI</td>
                            <td id="sqma" align="right" style="padding-right:5px">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Finishing I/M</td>
                            <td id="fimma" align="right" style="padding-right:5px">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Finishing QI</td>
                            <td id="fqma" align="right" style="padding-right:5px">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Poly Helper</td>
                            <td id="phma" align="right" style="padding-right:5px">&nbsp;</td>
                        </tr>-->
                    </table>
                </td>
            </tr>
        </table>
        <table cellpadding="0" cellspacing="2" width="100%" border="1" class="rpt_table">
            <tr>
                <td width="70%" align="center" class="button_container">
                    <? echo load_submit_buttons($permission,"fnc_balancing_entry",0,1,"resetForm();",2); ?>
                    <input type="button" class="formbutton" style="width:80px" name="print2" id="print2" value="Print2" onclick="print_balancing(2)" />
                    <input type="button" class="formbutton" style="width:80px" name="print3" id="print3" value="Print3" onclick="print_balancing(3)" />
                    <input type="button" class="formbutton" style="width:80px" name="print3" id="print3" value="Print4" onclick="print_balancing(4)" />
                    <input type="button" class="formbutton" style="width:80px" name="print3" id="print3" value="Print5" onclick="print_balancing(5)" />
                    <input type="button" class="formbutton" style="width:80px" name="print6" id="print6" value="Print6" onclick="print_balancing(6)" />
                    <input type="button" class="formbutton" style="width:80px" name="print7" id="print7" value="Print7" onclick="print_balancing(7)" />
                    <input type="button" class="formbutton" style="width:80px" name="print8" id="print8" value="Print8" onclick="print_balancing(8)" />
                    <input type="button" class="formbutton" style="width:80px" name="print9" id="print9" value="Print9" onclick="print_balancing(9)" />
                    <input type="button" class="formbutton" style="width:80px" name="print10" id="print10" value="Print10" onclick="print_balancing(10)" />
                </td>
            </tr>
        </table>
        <div style="width:870px; margin-top:5px; height:220px; border:solid 1px">
        	<table style="margin-left:5px; font-size:12px">
            	<tr>
                	<td><b>Balancing Graph</b></td>
                    <td bgcolor="#7D60A0" width="50" title="=Weight"></td>
                    <td>Weight</td>
                    <td width="50" id="tdtest"></td>
                    <td bgcolor="#BE4B48" width="50" title="=Pitch Time/0.85"></td>
                    <td width="50">UCL</td>
                     <td bgcolor="#4A7EBB" width="50" title="=Pitch Time"></td>
                    <td width="80">Pitch Time</td>
                    <td bgcolor="#98B954" width="50" title="=(Pitch Time*2)-UCL"></td>
                    <td width="50">LCL</td>
                </tr>
            </table>
            <!--<canvas id="canvas" height="200" width="860"></canvas>-->
            <div id="canvas_container" style="background-color:#FFF"></div>
        </div>
    </fieldset>
</form>
<script>
	/*var lineChartData = {
		labels : [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23],
		 datasets : [
			{
				//label: "My First dataset",
				fillColor : "rgba(220,220,220,0.2)",
				strokeColor : "#7D60A0",
				pointColor : "#7D60A0",
				pointStrokeColor : "#fff",
				pointHighlightFill : "#fff",
				pointHighlightStroke : "#7D60A0",
				data : [.18,.3,.23,.35,.35,.2,.35,.2,.19,.35,.2,.19,.24,.18,.18,.19,.3,.18,.24,.3,.3,.3,.25]
			},
			{
				//label: "My Second dataset",
				fillColor : "rgba(220,220,220,0.2)",
				strokeColor : "#BE4B48",
				pointColor : "#BE4B48",
				pointStrokeColor : "#fff",
				pointHighlightFill : "#fff",
				pointHighlightStroke : "#BE4B48",
				data : [.26,.26,.26,.26,.26,.26,.26,.26,.26,.26,.26,.26,.26,.26,.26,.26,.26,.26,.26,.26,.26,.26,.26]
			}
			,
			{
				//label: "My Second dataset",
				fillColor : "rgba(220,220,220,0.2)",
				strokeColor : "#4A7EBB",
				pointColor : "#4A7EBB",
				pointStrokeColor : "#fff",
				pointHighlightFill : "#fff",
				pointHighlightStroke : "#4A7EBB",
				data : [.22,.22,.22,.22,.22,.22,.22,.22,.22,.22,.22,.22,.22,.22,.22,.22,.22,.22,.22,.22,.22,.22,.22]
			},
			{
				//label: "My Second dataset",
				fillColor : "rgba(220,220,220,0.2)",
				strokeColor : "#98B954",
				pointColor : "#98B954",
				pointStrokeColor : "#fff",
				pointHighlightFill : "#fff",
				pointHighlightStroke : "#98B954",
				data : [.18,.18,.18,.18,.18,.18,.18,.18,.18,.18,.18,.18,.18,.18,.18,.18,.18,.18,.18,.18,.18,.18,.18]
			}
		]
	}

	function x(){ 
		var ctx = document.getElementById("canvas").getContext("2d");
		window.myLine = new Chart(ctx).Line(lineChartData, {
			responsive: true
		});
	}*/
	
</script>
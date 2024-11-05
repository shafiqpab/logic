
<form name="finishingentry_2" id="finishingentry_2"  autocomplete="off"  >
    	 
	        <fieldset style="width:750px;">
	        <legend  style=" color:#000000"> <b>Finishing Information</b></legend>
	            <table class="rpt_table" border="1" rules="all" width="100%">
                <tr>
                        <td width="100" class="must_entry_caption" bgcolor="#FFFF00"><strong>Fab Color/Code</strong> 
	                    <td width="" colspan="3" id="finishing_color_td">
	                    
	                    <?
                             
	                        echo create_drop_down( "cbo_finishing_fab_color_code",270,$sql,",", 1, "--Select--", $selected, "","","","","","",2);
	                    ?>
	                    </td>
                        <td width="100" colspan="" class="" bgcolor="#FFFF00"><strong>Batch No.</strong></td>
	                    <td width="" id="finishing_batch_no_td" colspan="3" >
	                     
	                    <?
	                        echo create_drop_down( "cbo_finishing_batch_no",270,"","", 1, "--Select--", $selected, "","","","","","");
	                    ?>
	                    </td>
	            </tr>
	                <tr>
                        <td width="110" colspan="4" style="background:#999999" align="center"><strong>Slitting</strong> 
                        <input type="hidden" id="finishing_update_id" style="width:40px;" />
                        </td>
                        <td width="110" colspan="4" style="background:#999999" align="center"><strong>Peach</strong></td>
	                </tr>
	                <tr>
                        <td width="100"><strong>Machine No</strong></td>
                        <td> <input type="text" id="txt_slitting_machine_no" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>After Dia</strong></td>
                        <td> <input type="text" id="txt_slitting_after_dia" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Machine No</strong></td>
                        <td> <input type="text" id="txt_peach_machine_no" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>After Dia</strong></td>
                        <td> <input type="text" id="txt_peach_after_dia" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
	                </tr> 
                    
                    <tr>
                        <td width="100"><strong>Slitting Process</strong></td>
                        <td> <input type="text" id="txt_slitting_process" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>Remarks</strong></td>
                        <td> <input type="text" id="txt_slitting_remarks" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Fabric Speed</strong></td>
                        <td> <input type="text" id="txt_peach_fabric_speed" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>After GSM</strong></td>
                        <td> <input type="text" id="txt_peach_after_gsm" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
	                </tr> 
                    <tr>
                        <td colspan="4" style="background:#999999" align="center" width="100"><strong>Stenter</strong></td>
                        <td width="100"><strong>Drum RPM</strong></td>
                        <td> <input type="text" id="txt_peach_drum_rpm" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Tension</strong></td>
                        <td> <input type="text" id="txt_peach_tension" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         
                       
	                </tr> 
                    <tr>
                        <td width="100"><strong>Machine No</strong></td>
                        <td> <input type="text" id="txt_stenter_machine_no" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>Dia Setting</strong></td>
                        <td> <input type="text" id="txt_stenter_dia_setting" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Remarks</strong></td>
                        <td  colspan="3" > <input type="text" id="txt_peach_remarks" class="text_boxes" style="width:270px;" placeholder="Write"  /></td>
	                </tr> 
                    <tr>
                        <td width="100"><strong>M/C Brand</strong></td>
                        <td> <input type="text" id="txt_stenter_mc_brand" class="text_boxes" style="width:70px;" rowspan="2"placeholder="Write"  /></td>
                        <td width="100"><strong>Padder Pressure (Kg)</strong></td>
                        <td> <input type="text" id="txt_stenter_paddar_pressure" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                         <td colspan="4" style="background:#999999" align="center" width="100"><strong>Brushing</strong></td>
	                </tr> 
                    <tr>
                        <td width="100"><strong>No of Chamber</strong></td>
                        <td> <input type="text" id="txt_stenter_no_chamber" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>Used Chemical</strong></td>
                        <td> <input type="text" id="txt_stenter_used_chemical" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Machine No</strong></td>
                        <td> <input type="text" id="txt_brushing_machine_no" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Pile RPM (2nd Drum)</strong></td>
                        <td> <input type="text" id="txt_brushing_pile_rpm_2nd" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
	                </tr>                     
                    <tr>
                        <td width="100"><strong>Temperature</strong></td>
                        <td> <input type="text" id="txt_stenter_temperature" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>After Dia</strong></td>
                        <td> <input type="text" id="txt_stenter_after_dia" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Fabric Speed</strong></td>
                        <td> <input type="text" id="txt_brushing_fabric_speed" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Counter Pile RPM (2nd Drum)</strong></td>
                        <td> <input type="text" id="txt_brushing_counter_pile_rpm_2nd" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
	                </tr>                     
                    <tr>
                        <td width="100"><strong>Overfeed</strong></td>
                        <td> <input type="text" id="txt_stenter_overfeed" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>After GSM</strong></td>
                        <td> <input type="text" id="txt_stenter_after_gsm" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Tension</strong></td>
                        <td> <input type="text" id="txt_brushing_tension" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>After Dia</strong></td>
                        <td> <input type="text" id="txt_brushing_after_dia" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
	                </tr>                     
                    <tr>
                        <td width="100"><strong>Speed</strong></td>
                        <td> <input type="text" id="txt_stenter_speed" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>Remarks</strong></td>
                        <td> <input type="text" id="txt_stenter_remarks" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Pile RPM (1st Drum)</strong></td>
                        <td> <input type="text" id="txt_brushing_pile_rpm_1st" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>After GSM</strong></td>
                        <td> <input type="text" id="txt_brushing_after_gsm" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
	                </tr>                     
                    <tr>
                        <td colspan="4" style="background:#999999" align="center" width="100"><strong>Dryer</strong></td>
                        <td width="100"><strong>Counter Pile RPM (1st Drum)</strong></td>
                        <td> <input type="text" id="txt_brushing_counter_pile_rpm_1st" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Remarks</strong></td>
                        <td> <input type="text" id="txt_brushing_remarks" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
	                </tr>                     
                    <tr>
                        <td width="100"><strong>Machine No</strong></td>
                        <td> <input type="text" id="txt_dryer_machine_no" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>Vibration</strong></td>
                        <td> <input type="text" id="txt_dryer_vibration" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td colspan="4" style="background:#999999" align="center" width="100" ><strong>Shearing</strong></td>
                        
	                </tr>                     
                    <tr>
                        <td width="100"><strong>Temperature</strong></td>
                        <td> <input type="text" id="txt_dryer_temperature" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>Used Chemical</strong></td>
                        <td> <input type="text" id="txt_dryer_used_chemical" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="80"><strong>Fabric Speed</strong></td>
                        <td> <input type="text" id="txt_shearing_fabric_speed" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Drum RPM</strong></td>
                        <td> <input type="text" id="txt_shearing_drum_rpm" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
	                </tr>                     
                    <tr>
                        <td width="100"><strong>Overfeed</strong></td>
                        <td> <input type="text" id="txt_dryer_overfeed" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>After Dia</strong></td>
                        <td> <input type="text" id="txt_dryer_after_dia" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                         <td width="80"><strong>Distance from Blade</strong></td>
                        <td> <input type="text" id="txt_shearing_distance_blade" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>After Dia</strong></td>
                        <td> <input type="text" id="txt_shearing_after_dia" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
	                </tr>                     
                    <tr>
                        <td width="100"><strong>Speed</strong></td>
                        <td> <input type="text" id="txt_dryer_speed" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>After GSM</strong></td>
                        <td> <input type="text" id="txt_dryer_after_gsm" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                         <td width="80"><strong>Distance from Comber</strong></td>
                        <td> <input type="text" id="txt_shearing_from_comber" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>After GSM</strong></td>
                        <td> <input type="text" id="txt_shearing_after_gsm" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
	                </tr>                     
                    <tr>
                        <td width="100"><strong>Dia Setting</strong></td>
                        <td> <input type="text" id="txt_dryer_dia_settings" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>Remarks</strong></td>
                        <td> <input type="text" id="txt_dryer_remarks" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="80"><strong>Remarks</strong></td>
                        <td colspan="3"> <input type="text" id="txt_shearing_remarks" class="text_boxes" style="width:270px;" placeholder="Write"  /></td>
	                </tr>                     
                    <tr>
                        <td colspan="4" style="background:#999999" align="center" width="100" ><strong>Stenter (After Peach/ Brush)</strong></td>
                        <td colspan="4" style="background:#999999" align="center" width="100"><strong>Compacting</strong></td>
	                </tr>                     
                    <tr>
                        <td width="100"><strong>Machine No</strong></td>
                        <td> <input type="text" id="txt_stenter_machine_no_apb" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>Dia Setting</strong></td>
                        <td> <input type="text" id="txt_stenter_dia_setting_apb" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="80"><strong>Machine No</strong></td>
                        <td> <input type="text" id="txt_compacting_machine_no" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Steam Pressure</strong></td>
                        <td> <input type="text" id="txt_compacting_steam_pressure" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
	                </tr>                     
                    <tr>
                        <td width="100"><strong>M/C Brand</strong></td>
                        <td> <input type="text" id="txt_stenter_mc_brand_apb" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>Padder Pressure (Kg)</strong></td>
                        <td> <input type="text" id="txt_stenter_padder_pressure_apb" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                         <td width="80"><strong>M/C Brand</strong></td>
                        <td> <input type="text" id="txt_compacting_mc_brand" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Blanket Pressure</strong></td>
                        <td> <input type="text" id="txt_compacting_blanket_pressure" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
	                </tr>                     
                    <tr>
                        <td width="100"><strong>No of Chamber</strong></td>
                        <td> <input type="text" id="txt_stenter_no_chamber_apb" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>Used Chemical</strong></td>
                        <td> <input type="text" id="txt_stenter_used_chemical_apb" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="80"><strong>Temperature</strong></td>
                        <td> <input type="text" id="txt_compacting_temperature" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>After Dia</strong></td>
                        <td> <input type="text" id="txt_compacting_after_dia" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
	                </tr>                     
                    <tr>
                        <td width="100"><strong>Temperature</strong></td>
                        <td> <input type="text" id="txt_stenter_temperature_apb" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>After Dia</strong></td>
                        <td> <input type="text" id="txt_stenter_after_dia_apb" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                         <td width="80"><strong>Speed</strong></td>
                        <td> <input type="text" id="txt_compacting_speed" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>After GSM</strong></td>
                        <td> <input type="text" id="txt_compacting_after_gsm" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
	                </tr>                     
                    <tr>
                        <td width="100"><strong>Overfeed</strong></td>
                        <td> <input type="text" id="txt_stenter_overfeed_apb" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>After GSM</strong></td>
                        <td> <input type="text" id="txt_stenter_after_gsm_apb" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                         <td width="80"><strong>Over Feed %</strong></td>
                        <td> <input type="text" id="txt_compacting_over_feed" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Dia Setting</strong></td>
                        <td> <input type="text" id="txt_compacting_dia_setting" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
	                </tr>                     
                    <tr>
                        <td width="100"><strong>Speed</strong></td>
                        <td> <input type="text" id="txt_stenter_speed_apb" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>Remarks</strong></td>
                        <td> <input type="text" id="txt_stenter_remarks_apb" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="80"><strong>Remarks</strong></td>
                        <td colspan="3"> <input type="text" id="txt_compacting_remarks" class="text_boxes" style="width:270px;" placeholder="Write"  /></td>
	                </tr>
                    <tr>
                        <td colspan="4" style="background:#999999" align="center" width="100" ><strong>Fabric Wash-Tumble</strong></td>
                        <td colspan="4" style="background:#999999" align="center" width="100"><strong>Remarks</strong></td><br>
	                </tr>                     
                    <tr>
                        <td width="100"><strong>Before Dia</strong></td>
                        <td> <input type="text" id="txt_fabWash_before_dia" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>Time</strong></td>
                        <td> <input type="text" id="txt_fabWash_time" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                        <td rowspan="4" colspan="4"> <input type="text" id="finish_remarks" class="text_boxes" style="width:344px;padding-right: 0px;padding-left: 0px;border-bottom-width: 0px;border-top-width: 0px;border-right-width: 0px;border-left-width: 0px;" placeholder="Write"  /></td>
	                </tr>                     
                    <tr>
                        <td width="100"><strong>Before GSM</strong></td>
                        <td> <input type="text" id="txt_fabWash_before_gsm" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>After Dia</strong></td>
                        <td> <input type="text" id="txt_fabWash_after_dia" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
	                </tr>                     
                    <tr>
                        <td width="100"><strong>Temperature</strong></td>
                        <td> <input type="text" id="txt_fabWash_temperature" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>After GSM</strong></td>
                        <td> <input type="text" id="txt_fabWash_after_gsm" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
	                </tr>                     
                    <tr>
                        <td width="100"><strong>Remarks</strong></td>
                        <td colspan="3"> <input type="text" id="txt_fabWash_remarks" class="text_boxes" style="width:270px;" placeholder="Write"  /></td>
	                </tr> 
	            </table>
	        </fieldset>
	            <br>
	            <table  style="border:none; width:735px;" cellpadding="0" cellspacing="1" border="0" id="">
	                <tr>
	                    <td align="center" class="button_container">
	                        <? 
	                            echo load_submit_buttons($permission,"fnc_finishing_entry",0,0,"reset_form('finishingentry_2','finishing_entry_info_list','','','')",7);
	                        ?>
	                    </td>
	                </tr>  
	            </table>
	           <br>
	           
        </div>
    	<div id="finishing_entry_info_list" style="width:800px;">    
     </form>
  
		
			
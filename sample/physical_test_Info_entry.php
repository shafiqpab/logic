
<form name="physicalTestentry_2" id="physicalTestentry_2"  autocomplete="off"  >
    	 
	        <fieldset style="width:800px;">
	        <legend  style=" color:#000000"> <b>Physical Test Information</b></legend>
	            <table class="rpt_table" border="1" rules="all" width="100%">
                <tr>
                        <td width="100" class="must_entry_caption" bgcolor="#FFFF00"><strong>Fab Color/Code</strong> 
	                    <td width="" colspan="3" id="physical_test_color_td">
	                    
	                    <?
                             
	                        echo create_drop_down( "cbo_physical_test_color_code",270,$sql,",", 1, "--Select--", $selected, "","","","","","",2);
	                    ?>
	                    </td>
                        <td width="100" colspan="" class="" bgcolor="#FFFF00"><strong>Batch No.</strong></td>
	                    <td width="" id="batch_physical_no_td" colspan="3" >
	                     
	                    <?
	                     
                            echo create_drop_down( "cbo_physical_batch_no",270,"","", 1, "--Select--", $selected, "","","","","","");
	                    ?>
	                    </td>
	            </tr>
	                <tr>
                        <td width="120" colspan="2" style="background:#999999" align="center"><strong>Dimensional Stability</strong> 
                        <input type="hidden" id="physicalTest_update_id" style="width:40px;" />
                        </td>
                        
                        <td width="100" class=""><strong>Dry Process</strong></td>
                          <td width="100">
                          <input type="text" id="txt_dry_process" class="text_boxes" style="width:70px;" placeholder="Write"  />
                          </td>
                           <td width="100" class=""><strong>Actual GSM</strong></td>
                          <td width="100">
                          <input type="text" id="txt_actual_gsm" class="text_boxes" style="width:70px;" placeholder="Write"  />
                          </td>
                          <td width="100" class=""><strong>Phenolic Yellowing</strong></td>
                          <td width="100">
                          <input type="text" id="txt_phenolic_yellowing" class="text_boxes" style="width:70px;" placeholder="Write"  />
                          </td>
	                    
	                </tr>
	                <tr>
                        <td width="100"><strong>Length %</strong></td>
                        <td> <input type="text" id="txt_length" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>Test Method</strong></td>
                        <td> <input type="text" id="txt_test_method" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Pilling</strong></td>
                        <td> <input type="text" id="txt_pilling" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>C/F to light</strong></td>
                        <td> <input type="text" id="txt_cf_to_light" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
	                </tr> 
                    
                    <tr>
                        <td width="100"><strong>Width %</strong></td>
                        <td> <input type="text" id="txt_width" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>Req. Dia</strong></td>
                        <td> <input type="text" id="txt_req_dia" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Bursting Strength</strong></td>
                        <td> <input type="text" id="txt_bursting_strength" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>C/F to Saliva</strong></td>
                        <td> <input type="text" id="txt_cf_to_saliva" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
	                </tr> 
                    <tr>
                        <td width="100"><strong>Twisting %</strong></td>
                        <td> <input type="text" id="txt_twisting" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>Actual Dia</strong></td>
                        <td> <input type="text" id="txt_actual_dia" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Dry Rubbing</strong></td>
                        <td> <input type="text" id="txt_dry_rubbing" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>WPI</strong></td>
                        <td> <input type="text" id="txt_wpi" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
	                </tr> 
                    <tr>
                        <td width="100"><strong>Wash Temperature</strong></td>
                        <td> <input type="text" id="txt_wash_temperature" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>Req. GSM</strong></td>
                        <td> <input type="text" id="txt_req_gsm" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Wet Rubbing</strong></td>
                        <td> <input type="text" id="txt_wet_rubbing" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>CPI</strong></td>
                        <td> <input type="text" id="txt_cpi" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
	                </tr>   
                    
                    <tr>
                        <td width="120" colspan="2" style="background:#999999" align="center"><strong>Singeing</strong></td>
                        <td width="100" colspan="" style="background:#999999" align="center"><strong>Acetate</strong></td>
                        <td width="100" colspan="" style="background:#999999" align="center"><strong>Cotton</strong></td>
                        <td width="100" colspan="" style="background:#999999" align="center"><strong>Polymide</strong></td>
                        <td width="100" colspan="" style="background:#999999" align="center"><strong>Polyester</strong></td>
                        <td width="100" colspan="" style="background:#999999" align="center"><strong>Acrylic</strong></td>
                        <td width="100" colspan="" style="background:#999999" align="center"><strong>Wool</strong></td>
	                </tr>
	                <tr>
                        <td width="100" colspan="2"><strong>C/F to Wash</strong></td>
                        <td> <input type="text" id="txt_acetate_1" class="text_boxes" style="width:70px;" placeholder="write"  /></td>
                        <td> <input type="text" id="txt_acetate_2" class="text_boxes" style="width:70px;" placeholder="write"  /></td>
                        <td> <input type="text" id="txt_acetate_3" class="text_boxes" style="width:70px;" placeholder="write"  /></td>
                        <td> <input type="text" id="txt_acetate_4" class="text_boxes" style="width:70px;" placeholder="write"  /></td>
                        <td> <input type="text" id="txt_acetate_5" class="text_boxes" style="width:70px;" placeholder="write"  /></td>
                        <td> <input type="text" id="txt_acetate_6" class="text_boxes" style="width:70px;" placeholder="write"  /></td>
	                </tr> 
                    <tr>
                        <td width="100" colspan="2"><strong>C/F to Water (Cossa Staining)</strong></td>
                        <td> <input type="text" id="txt_Water_1" class="text_boxes" style="width:70px;" placeholder="write"  /></td>
                        <td> <input type="text" id="txt_Water_2" class="text_boxes" style="width:70px;" placeholder="write"  /></td>
                        <td> <input type="text" id="txt_Water_3" class="text_boxes" style="width:70px;" placeholder="write"  /></td>
                        <td> <input type="text" id="txt_Water_4" class="text_boxes" style="width:70px;" placeholder="write"  /></td>
                        <td> <input type="text" id="txt_Water_5" class="text_boxes" style="width:70px;" placeholder="write"  /></td>
                        <td> <input type="text" id="txt_Water_6" class="text_boxes" style="width:70px;" placeholder="write"  /></td>
	                </tr> 
                    <tr>
                        <td width="100" colspan="2"><strong>C/F to Perspiration (Acid)</strong></td>
                        <td> <input type="text" id="txt_perspiration_acid_1" class="text_boxes" style="width:70px;" placeholder="write"  /></td>
                        <td> <input type="text" id="txt_perspiration_acid_2" class="text_boxes" style="width:70px;" placeholder="write"  /></td>
                        <td> <input type="text" id="txt_perspiration_acid_3" class="text_boxes" style="width:70px;" placeholder="write"  /></td>
                        <td> <input type="text" id="txt_perspiration_acid_4" class="text_boxes" style="width:70px;" placeholder="write"  /></td>
                        <td> <input type="text" id="txt_perspiration_acid_5" class="text_boxes" style="width:70px;" placeholder="write"  /></td>
                        <td> <input type="text" id="txt_perspiration_acid_6" class="text_boxes" style="width:70px;" placeholder="write"  /></td>
	                </tr> 
                    <tr>
                        <td width="100" colspan="2"><strong>C/F to Perspiration (Alkali)</strong></td>
                        <td> <input type="text" id="txt_perspiration_alkali_1" class="text_boxes" style="width:70px;" placeholder="write"  /></td>
                        <td> <input type="text" id="txt_perspiration_alkali_2" class="text_boxes" style="width:70px;" placeholder="write"  /></td>
                        <td> <input type="text" id="txt_perspiration_alkali_3" class="text_boxes" style="width:70px;" placeholder="write"  /></td>
                        <td> <input type="text" id="txt_perspiration_alkali_4" class="text_boxes" style="width:70px;" placeholder="write"  /></td>
                        <td> <input type="text" id="txt_perspiration_alkali_5" class="text_boxes" style="width:70px;" placeholder="write"  /></td>
                        <td> <input type="text" id="txt_perspiration_alkali_6" class="text_boxes" style="width:70px;" placeholder="write"  /></td>
	                </tr> 
                    <tr>
                        <td width="100" colspan="2"><strong>Remarks</strong></td>
                        <td colspan="4"> <input type="text" id="txt_physicaltest_remarks" class="text_boxes" style="width:375px;" placeholder="write"  /></td>
                        <td width="100" colspan=""><strong>Delivery Date</strong></td>
                        <td> <input type="text" id="txt_delivery_date" class="datepicker" style="width:70px;" placeholder="Date"  /></td>
	                </tr> 
                   
                   
                     
	                
	            </table>
	        </fieldset>
	            <br>
	            <table  style="border:none; width:735px;" cellpadding="0" cellspacing="1" border="0" id="">
	                <tr>
	                    <td align="center" class="button_container">
	                        <? 
	                            echo load_submit_buttons($permission,"fnc_physicalTest_entry",0,0,"reset_form('physicalTestentry_2','physicalTest_entry_info_list','','','')",8);
	                        ?>
	                    </td>
	                </tr>  
	            </table>
	           <br>
	           
        </div>
    	<div id="physicalTest_entry_info_list" style="width:800px;">    
     </form>
  
		
			

<form name="batchentry_2" id="batchentry_2"  autocomplete="off"  >
    	 
	        <fieldset style="width:800px;">
	        <legend  style=" color:#000000"> <b>Batching Information</b></legend>
	            <table class="rpt_table" border="1" rules="all" width="100%">
                <tr>
                        <td width="100" class="must_entry_caption" bgcolor="#FFFF00"><strong>Fab Color/Code</strong> 
	                    <td width="" colspan="3" id="fabric_batch_color_td">
	                    
	                    <?
                             
	                        echo create_drop_down( "cbo_fabric_batch_color_code",270,$sql,",", 1, "--Select--", $selected, "","","","","","",2);
	                    ?>
	                    </td>
                        <td width="100" class="must_entry_caption" bgcolor="#FFFF00"><strong>Batch No.</strong> 
                        <input type="hidden" id="batchting_update_id" style="width:40px;" /></td>
	                    <td width="" colspan="3" id="batch_no_td">
	                    <?
                             echo create_drop_down( "cbo_batch_no",270,"","", 1, "--Select--", $selected, "","","","","","");
	                    ?>
	                    </td>
	            </tr>
	                <tr>
                        <td width="120" colspan="4" style="background:#999999" align="center"><strong>Heat Setting</strong> 
                        </td>
	                   
                        <td width="100" class=""><strong>Dia Setting</strong></td>
                          <td width="100">
                          <input type="text" id="txt_dia_setting" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  />
                          </td>
                           <td width="100" class=""><strong>Dia Extension</strong></td>
                          <td width="100">
                          <input type="text" id="txt_dia_extension" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  />
                          </td>
	                    
	                </tr>
	                <tr>
                        <!-- <td width="100"><strong>Batch No.</strong></td>
                        <td> <input type="text" id="txt_batch" class="text_boxes" style="width:70px;" placeholder="Write"  /></td> -->
                        <td width="100"><strong>Greige Dia</strong></td>
                        <td colspan="3"> <input type="text" id="txt_batching_greige_dia" class="text_boxes_numeric" style="width:270px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Speed</strong></td>
                        <td> <input type="text" id="txt_batching_speed" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>After H/S Dia</strong></td>
                        <td> <input type="text" id="txt_hs_dia" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
	                </tr> 
                    
                    <tr>
                        <td width="100"><strong>M/C Name & Brand</strong></td>
                        <td> <input type="text" id="txt_mc_name_brand" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>Greige GSM</strong></td>
                        <td> <input type="text" id="txt_batching_greige_gsm" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Temperature</strong></td>
                        <td> <input type="text" id="txt_temperature" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>After H/S GSM</strong></td>
                        <td> <input type="text" id="txt_hs_gsm" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
	                </tr> 
                    <tr>
                        <td width="100"><strong>No of Chamber</strong></td>
                        <td> <input type="text" id="txt_no_of_chamber" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>Using Chemical</strong></td>
                        <td> <input type="text" id="txt_chemical" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Overfeed</strong></td>
                        <td> <input type="text" id="txt_overfeed" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Remarks</strong></td>
                        <td> <input type="text" id="txt_batching_remarks" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
	                </tr> 
                    
                    <tr>
                        <td width="120" colspan="4" style="background:#999999" align="center"><strong>Singeing</strong> 
                        
                        </td>
	                   
                        <td width="100" class=""><strong>No. of Burners</strong></td>
                          <td width="100">
                          <input type="text" id="txt_no_burners" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  />
                          </td>
                           <td width="100" class=""><strong>Speed(m/min)</strong></td>
                          <td width="100">
                          <input type="text" id="txt_speed_min" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  />
                          </td>
	                    
	                </tr>
	                <tr>
                        <td width="100"><strong>Intensity(Mbar)</strong></td>
                        <td> <input type="text" id="txt_intensity" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>Singeing Type</strong></td>
                        <td> <input type="text" id="txt_singeing" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Burner Distance</strong></td>
                        <td> <input type="text" id="txt_burner_distance" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Singeing Position</strong></td>
                        <td> <input type="text" id="txt_singeing_pos" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
	                </tr> 
                   
                   
                     
	                
	            </table>
	        </fieldset>
	            <br>
	            <table  style="border:none; width:735px;" cellpadding="0" cellspacing="1" border="0" id="">
	                <tr>
	                    <td align="center" class="button_container">
	                        <? 
	                            echo load_submit_buttons($permission,"fnc_batch_entry",0,0,"reset_form('batchentry_2','batching_entry_info_list','','','')",4);
	                        ?>
	                    </td>
	                </tr>  
	            </table>
	           <br>
	           
        </div>
    	<div id="batching_entry_info_list" style="width:800px;">    
     </form>
  
		
			
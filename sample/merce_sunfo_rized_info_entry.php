
<form name="merceSunfoRized_2" id="merceSunfoRized_2"  autocomplete="off"  >
    	 
	        <fieldset style="width:750px;">
	        <legend  style=" color:#000000"> <b>Mercerized and SunfoRized Information</b></legend>
	            <table class="rpt_table" border="1" rules="all" width="100%">
                <tr>
                        <td width="100" class="must_entry_caption" bgcolor="#FFFF00"><strong>Fab Color/Code</strong> 
	                    <td width="" colspan="3" id="msrce_sunfo_color_td">
	                    
	                    <?
                             
	                        echo create_drop_down( "cbo_msrce_sunfo_fab_color_code",270,$sql,",", 1, "--Select--", $selected, "","","","","","",2);
	                    ?>
	                    </td>
                        <td width="100" colspan="" class="" bgcolor="#FFFF00"><strong>Batch No.</strong></td>
	                    <td width="" id="batch_sunfo_no_td" colspan="3" >
	                     
	                    <?
	                        echo create_drop_down( "cbo_sunfo_batch_no",270,"","", 1, "--Select--", $selected, "","","","","","");
	                    ?>
	                    </td>
	            </tr>
	                <tr>
                        <td width="110" colspan="4" style="background:#999999" align="center"><strong>Mercerized</strong> 
                        <input type="hidden" id="merceSunfoRized_update_id" style="width:40px;" />
                        </td>
                        <!-- <td width="100" colspan="2" style="background:#999999" align="center"><strong>Dyeing Recipe</strong></td> -->
                        <td width="110" colspan="4" style="background:#999999" align="center"><strong>Sunforized</strong></td>
                           
	                    
	                </tr>
	                <tr>
                        <td width="100"><strong>Total Liquare</strong></td>
                        <td> <input type="text" id="txt_mer_total_liquare" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>Caustic Solution in °B</strong></td>
                        <td> <input type="text" id="txt_mer_caustic_solution" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="80"><strong>Temperature</strong></td>
                        <td> <input type="text" id="txt_sun_temperature" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Taflon Pressure /Compection</strong></td>
                        <td> <input type="text" id="txt_sun_taflon_pressureCompection" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
	                </tr> 
                    
                    <tr>
                        <td width="100"><strong>Temperature</strong></td>
                        <td> <input type="text" id="txt_mer_temperature" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>M/C speed</strong></td>
                        <td> <input type="text" id="txt_mer_mc_speed" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                         <td width="80"><strong>Over Feed</strong></td>
                        <td> <input type="text" id="txt_sun_over_feed" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Speed</strong></td>
                        <td> <input type="text" id="txt_sun_speed" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
	                </tr> 
                    <tr>
                        <td width="100"><strong>Mecerized PH</strong></td>
                        <td> <input type="text" id="txt_mer_mercerizedph" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>Normal wash</strong></td>
                        <td> <input type="text" id="txt_mer_normalWash" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="80"><strong>Steam</strong></td>
                        <td colspan="3" > <input rowspan="2" type="text" id="txt_sun_steam" class="text_boxes" style="width:270px;" placeholder="Write"  /></td>
                         
                       
	                </tr> 
                    <tr>
                        <td width="100"><strong>Acetic Acid (G/L)</strong></td>
                        <td> <input type="text" id="txt_mer_aceticAcid" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>Unloading PH</strong></td>
                        <td> <input type="text" id="txt_mer_unloadingPH" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                         <td width="80" rowspan="2"><strong>Remarks</strong></td>
                        <td rowspan="2" colspan="3" > <input type="text" id="txt_sun_remarks" class="text_boxes" style="width:270px;" placeholder="Write"  /></td>
	                </tr> 
                    <tr>
                        <td width="100"><strong>Remarks</strong></td>
                        <td colspan="3"> <input type="text" id="txt_mer_remarks" class="text_boxes" style="width:258px;" rowspan="2"placeholder="Write"  /></td>
	                </tr> 
                 
                     
	                
	            </table>
	        </fieldset>
	            <br>
	            <table  style="border:none; width:735px;" cellpadding="0" cellspacing="1" border="0" id="">
	                <tr>
	                    <td align="center" class="button_container">
	                        <? 
	                            echo load_submit_buttons($permission,"fnc_merceSunfoRized_entry",0,0,"reset_form('merceSunfoRized_2','merceSunfoRized_entry_info_list','','','')",6);
	                        ?>
	                    </td>
	                </tr>  
	            </table>
	           <br>
	           
        </div>
    	<div id="merceSunfoRized_entry_info_list" style="width:800px;">    
     </form>
  
		
			
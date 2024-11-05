
<form name="washentry_2" id="washentry_2"  autocomplete="off"  >
    	 
	        <fieldset style="width:800px;">
	        <legend  style=" color:#000000"> <b>Washing Information</b></legend>
	            <table class="rpt_table" border="1" rules="all" width="100%">
                <tr>
                        <td width="100" class="must_entry_caption" bgcolor="#FFFF00"><strong>Fab Color/Code</strong> 
	                    <td width="" colspan="3" id="fabric_wash_color_td">
	                    
	                    <?
                             
	                        echo create_drop_down( "cbo_fabric_wash_color_code",270,$sql,",", 1, "--Select--", $selected, "","","","","","",2);
	                    ?>
	                    </td>
                        <td width="100" colspan="" class="" bgcolor="#FFFF00"><strong>Batch No.</strong></td>
	                    <td width="" id="batch_no_td_wash" colspan="3" >
	                     
	                    <?
	                        echo create_drop_down( "cbo_batch_no_wash",270,"","", 1, "--Select--", $selected, "","","","","","");
	                    ?>
	                    </td>
	            </tr>
	                <tr>
                        <td width="100"><strong>M/C No</strong>
                        <td width="100">
                            <input type="text" id="txt_wash_mc_no" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /> </td>
                            <input type="hidden" id="washing_update_id" style="width:40px;" />
                        </td>
                        <td width="100" class=""><strong>RPM</strong></td>
                          <td width="100">
                            <input type="text" id="txt_wash_rpm" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  />
                          </td>
                           <td width="100" class=""><strong>Chemical Name</strong></td>
                          <td width="100">
                            <input type="text" id="txt_chemical_name" class="text_boxes" style="width:70px;" placeholder="Write"  />
                          </td>
                          <td width="100" class=""><strong>Tumble Dryer No</strong></td>
                          <td width="100">
                            <input type="text" id="txt_tumble_dryer_no" class="text_boxes" style="width:70px;" placeholder="Write"  />
                          </td>
	                    
	                </tr>
	                <tr>
                        <td width="100"><strong>Process Name</strong></td>
                        <td> <input type="text" id="txt_process_name" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>Pre-Treatment</strong></td>
                        <td> <input type="text" id="txt_pre_treatment" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Dyes Name</strong></td>
                        <td> <input type="text" id="txt_dyes_name" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Extra Process</strong></td>
                        <td> <input type="text" id="txt_extra_process" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
	                </tr> 
                    
                    <tr>
                        <td width="100"><strong>Temperature</strong></td>
                        <td> <input type="text" id="txt_wash_info_temperature" class="text_boxes_numeric" style="width:70px;" placeholder="Write"/></td>
                        <td width="100"><strong>Recepi No.</strong></td>
                        <td> <input type="text" id="txt_recepi_no" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Hydro No</strong></td>
                        <td> <input type="text" id="txt_hydro_no" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="100"><strong>Dry Process</strong></td>
                        <td> <input type="text" id="txt_wash_dry_process" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
	                </tr> 
                    <tr>
                        <td width="100"><strong>Remarks</strong></td>
                        <td colspan="7"> <input type="text" id="txt_wash_remarks" class="text_boxes" style="width:675px;" placeholder="write"  /></td>
	                </tr> 
                   
                   
                     
	                
	            </table>
	        </fieldset>
	            <br>
	            <table  style="border:none; width:735px;" cellpadding="0" cellspacing="1" border="0" id="">
	                <tr>
	                    <td align="center" class="button_container">
	                        <? 
	                            echo load_submit_buttons($permission,"fnc_washing_entry",0,0,"reset_form('washentry_2','washing_entry_info_list','','','')",9);
	                        ?>
	                    </td>
	                </tr>  
	            </table>
	           <br>
	           
        </div>
    	<div id="washing_entry_info_list" style="width:800px;">    
     </form>
  
		
			
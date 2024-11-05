
<form name="dyeingentry_2" id="dyeingentry_2"  autocomplete="off"  >
    	 
	        <fieldset style="width:750px;">
	        <legend  style=" color:#000000"> <b>Dyeing Information</b></legend>
	            <table class="rpt_table" border="1" rules="all" width="100%">
                <tr>
                        <td width="100" class="must_entry_caption" bgcolor="#FFFF00"><strong>Fab Color/Code</strong> 
	                    <td width="" colspan="3" id="dying_color_td">
	                    
	                    <?
                             
	                        echo create_drop_down( "cbo_dying_fab_color_code",270,$sql,",", 1, "--Select--", $selected, "","","","","","",2);
	                    ?>
	                    </td>
                        <td width="100" colspan="" class="" bgcolor="#FFFF00"><strong>Batch No.</strong></td>
	                    <td width="" id="dying_batch_no_td" colspan="3" >
	                     
	                    <?
	                        echo create_drop_down( "cbo_dying_batch_no",270,"","", 1, "--Select--", $selected, "","","","","","");
	                    ?>
	                    </td>
	            </tr>
	                <tr>
                        <td width="120" colspan="2" style="background:#999999" align="center"><strong>Pre-Treatment</strong> 
                        <input type="hidden" id="dyeing_update_id" style="width:40px;" />
                        </td>
	                   
                        <td width="100" colspan="2" style="background:#999999" align="center"><strong>Dyeing Recipe</strong></td>
                         
                        <td width="100" colspan="4" style="background:#999999" align="center"><strong>Dyeing Type</strong></td>
                           
	                    
	                </tr>
	                <tr>
                        <td width="100"><strong>Scouring Type</strong></td>
                        <td> <input type="text" id="txt_dyeing_scouring" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>Recipe No.</strong></td>
                        <td> 
                            <input type="text" id="txt_dyeing_recipe_num" class="text_boxes_numeric" style="width:70px;" placeholder="Display" readonly/>
                            <input type="hidden" id="txt_dyeing_recipe_id" class="text_boxes_numeric" style="width:70;" placeholder="Display" readonly/>
                        </td>
                        </td>
                         <td width="80"><strong>Reactive</strong></td>
                        <td id="txt_dyeing_reactive"><?
	                        echo create_drop_down( "cbo_dyeing_reactive",80,$yes_no,"", 1, "-Select-",$selected, "","","","","","");
	                    ?> </td>
                         <td width="100"><strong>Both Part</strong></td>
                         <td id="txt_dyeing_both_part"><?
	                        echo create_drop_down( "cbo_dyeing_both_part",80,$yes_no,"", 1, "-Select-",$selected, "","","","","","");
	                    ?> </td>
	                </tr> 
                    <tr>
                        <td width="100"><strong>Enzyme %</strong></td>
                        <td> <input type="text" id="txt_dyeing_enzyme_per" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>Dyes (Original)</strong></td>
                        <td> <input type="text" id="txt_dyeing_dyes_orginal" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="80"><strong>Direct</strong></td>
                         <td id="txt_dyeing_direct"><?
	                        echo create_drop_down( "cbo_dyeing_direct",80,$yes_no,"", 1, "-Select-",$selected, "","","","","","");
	                    ?> </td>
                         <td width="100"><strong>Only Wash</strong></td>
                         <td id="txt_dyeing_wash"><?
	                        echo create_drop_down( "cbo_dyeing_wash",80,$yes_no,"", 1, "-Select-",$selected, "","","","","","");
	                    ?> </td>
	                </tr> 
                    <tr>
                        <td width="100"><strong>Remarks</strong></td>
                        <td> <input type="text" id="txt_dyeing_remarks" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                        <td width="100"><strong>Dyes (Add/Top)</strong></td>
                        <td> <input type="text" id="txt_dyeing_dyes_add" class="text_boxes" style="width:70px;" placeholder="Write"  /></td>
                         <td width="80"><strong>Disperse</strong></td>
                         <td id="txt_dyeing_disperse"><?
	                        echo create_drop_down( "cbo_dyeing_disperse",80,$yes_no,"", 1, "-Select-",$selected, "","","","","","");
	                    ?> </td>
                         <td width="100"><strong>White</strong></td>
                         <td id="txt_dyeing_white"><?
	                        echo create_drop_down( "cbo_dyeing_white",80,$yes_no,"", 1, "-Select-",$selected, "","","","","","");
	                    ?> </td>
	                </tr> 
                 
                     
	                
	            </table>
	        </fieldset>
	            <br>
	            <table  style="border:none; width:735px;" cellpadding="0" cellspacing="1" border="0" id="">
	                <tr>
	                    <td align="center" class="button_container">
	                        <? 
	                            echo load_submit_buttons($permission,"fnc_dyeing_entry",0,0,"reset_form('dyeingentry_2','Dyeing_entry_info_list','','','')",5);
	                        ?>
	                    </td>
	                </tr>  
	            </table>
	           <br>
	           
        </div>
    	<div id="dyeing_entry_info_list" style="width:800px;">    
     </form>
  
		
			
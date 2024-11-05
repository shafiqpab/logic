
<script>

</script>
<form name="yarnentry_2" id="yarnentry_2"  autocomplete="off"  >
    	 
	        <fieldset style="width:735px;">
	        <legend  style=" color:#000000"> <b>Yarn Information</b></legend>
	            <table class="rpt_table" border="1" rules="all" width="100%">
	                <tr>
                        <td width="120" class="must_entry_caption"><strong>Fab Color/Code</strong> 
                        <input type="hidden" id="yarn_update_id" style="width:140px;" /></td>
	                    <td width="150" id="yarn_color_td">
	                    
	                    <?
                             
	                        echo create_drop_down( "cbo_yarn_fab_color_code",150,$sql,",", 1, "--Select--", $selected, "","","","","","",2);
	                    ?>
	                    </td>
                        <td width="120" class=""><strong>Fabric Description</strong></td>
	                    <td width="150" id="yarn_fabric_td" colspan="2">
	                     
	                    <?
                            
	                        echo create_drop_down( "cbo_yarn_fabrication",150,"","", 1, "--Select--", $selected, "","","","","","");
	                    ?>
	                    </td>
	                </tr>
	                <tr>
                        <td><strong>Color Type</strong></td>
	                    <td width="150" id="txt_yarn_color_type_td"> 
						<?
	                        echo create_drop_down( "txt_yarn_color_type",150,"","", 1, "--Select--", $selected, "","","","","","");
	                    ?>
                        </td>                 
	                    <td width="" colspan="3">&nbsp; 
	                    </td>
	                </tr> 
                   
                     <tr bgcolor="#CCCCCC">
                    	<td width="120">&nbsp; </td> 
                        <td width="100"><b>  1st Yarn (Face)</b></td> 
                        <td width="100">  <b> 2nd Yarn (Binding)</b></td>
                        <td width="100"> <b> 3rd Yarn (Back)</b></td>
                        <td width="100"> <b>4th Yarn (Others)</b></td>
                    </tr>
                     <tr>
                    	<td width="120"> Yarn Composition</td> 
                        <td width="100" id="yarn_composition_td1">
						<?
	                        echo create_drop_down( "cbo_yarn_composition_1",110,"","", 1, "--Select--", $selected, "","","","","","");
	                    ?></td> 
                        <td width="100" id="yarn_composition_td2">
						<?
	                        echo create_drop_down( "cbo_yarn_composition_2",110,"","", 1, "--Select--", $selected, "","","","","","");
	                    ?></td> 
                          <td width="100" id="yarn_composition_td3">
						<?
	                        echo create_drop_down( "cbo_yarn_composition_3",110,"","", 1, "--Select--", $selected, "","","","","","");
	                    ?></td>
                          <td width="100" id="yarn_composition_td4">
						<?
	                        echo create_drop_down( "cbo_yarn_composition_4",110,"","", 1, "--Select--", $selected, "","","","","","");
	                    ?></td>
                    </tr>
                     <tr>
                    	<td width="120"> Count</td> 
                        <td width="100" id="yarn_count_td1">
						<?
	                        echo create_drop_down( "cbo_yarn_count_1",110,"","", 1, "--Select--", $selected, "","","","","","");
	                    ?></td> 
                        <td width="100" id="yarn_count_td2">
						<?
	                        echo create_drop_down( "cbo_yarn_count_2",110,"","", 1, "--Select--", $selected, "","","","","","");
	                    ?></td> 
                          <td width="100" id="yarn_count_td3">
						<?
	                        echo create_drop_down( "cbo_yarn_count_3",110,"","", 1, "--Select--", $selected, "","","","","","");
	                    ?></td>
                          <td width="100" id="yarn_count_td4">
						<?
	                        echo create_drop_down( "cbo_yarn_count_4",110,"","", 1, "--Select--", $selected, "","","","","","");
	                    ?></td>
                    </tr>
                     <tr>
                    	<td width="120"> Brand</td> 
                        <td width="100" id="yarn_brand_td1">
						<input type="text" id="cbo_yarn_brand_1" class="text_boxes_numeric" style="width:100px;" placeholder="Display" readonly /></td> 
                        <td width="100" id="yarn_brand_td2">
						<input type="text" id="cbo_yarn_brand_2" class="text_boxes_numeric" style="width:100px;" placeholder="Display" readonly /></td>
                          <td width="100" id="yarn_brand_td3">
						  <input type="text" id="cbo_yarn_brand_3" class="text_boxes_numeric" style="width:100px;" placeholder="Display" readonly /></td>
                          <td width="100" id="yarn_brand_td4">
						  <input type="text" id="cbo_yarn_brand_4" class="text_boxes_numeric" style="width:100px;" placeholder="Display" readonly /></td>
                    </tr>
                     <tr>
                    	<td width="120"> Lot</td> 
                        <td width="100" id="yarn_lot_td1">
						 
                         <input type="text" id="cbo_lot_1" class="text_boxes_numeric" style="width:100px;" placeholder="Display" readonly /> 
                        </td> 
                        <td width="100" id="yarn_lot_td2">
						  <input type="text" id="cbo_lot_2" class="text_boxes_numeric" style="width:100px;" placeholder="Display" readonly />
                         </td> 
                          <td width="100" id="yarn_lot_td3">
						  <input type="text" id="cbo_lot_3" class="text_boxes_numeric" style="width:100px;" placeholder="Display" readonly />
                         </td>
                          <td width="100" id="yarn_lot_td4">
							 <input type="text" id="cbo_lot_4" class="text_boxes_numeric" style="width:100px;" placeholder="Display" readonly />
                        </td>
                    </tr>
                    <tr>
                    	<td width="120"> Ratio(%)</td> 
                        <td width="100" id="yarn_ratio_td1">
						  <input type="text" id="txt_ratio_1" class="text_boxes_numeric" style="width:100px;" placeholder="Write"  /> 
                         </td> 
                        <td width="100" id="yarn_ratio_td2">
						    <input type="text" id="txt_ratio_2" class="text_boxes_numeric" style="width:100px;" placeholder="Write"  /> 
                          </td> 
                          <td width="100" id="yarn_ratio_td3">
                           <input type="text" id="txt_ratio_3" class="text_boxes_numeric" style="width:100px;" placeholder="Write"  /> 
						 </td>
                          <td width="100" id="yarn_ratio_td4">
						 	 <input type="text" id="txt_ratio_4" class="text_boxes_numeric" style="width:100px;" placeholder="Write"  /> 
                         </td>
                    </tr>
                    <tr>
                    	<td width="120"> Actual Count</td> 
                        <td width="100" id="yarn_actual_td1">
						  <input type="text" id="txt_act_count_1" class="text_boxes_numeric" style="width:100px;" placeholder="Write"  /> 
                         </td> 
                        <td width="100" id="yarn_actual_td2">
						    <input type="text" id="txt_act_count_2" class="text_boxes_numeric" style="width:100px;" placeholder="Write"  /> 
                          </td> 
                          <td width="100" id="yarn_actual_td3">
                           <input type="text" id="txt_act_count_3" class="text_boxes_numeric" style="width:100px;" placeholder="Write"  /> 
						 </td>
                          <td width="100" id="yarn_actual_td4">
						 	 <input type="text" id="txt_act_count_4" class="text_boxes_numeric" style="width:100px;" placeholder="Write"  /> 
                         </td>
                    </tr>
	                
	            </table>
	        </fieldset>
	            <br>
	            <table  style="border:none; width:735px;" cellpadding="0" cellspacing="1" border="0" id="">
	                <tr>
	                    <td align="center" class="button_container">
	                        <? 
	                            echo load_submit_buttons($permission,"fnc_yarn_entry",0,0,"reset_form('yarnentry_2','yarn_entry_info_list','','','')",2);
	                        ?>
	                    </td>
	                </tr>  
	            </table>
	           <br>
	           
        </div>
    	<div id="yarn_entry_info_list" style="width:800px;">    
     </form>
  
		
			
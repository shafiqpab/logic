
<script>

</script>
<form name="knitentry_2" id="knitentry_2"  autocomplete="off"  >
    	 
	        <fieldset style="width:850px;">
	        <legend  style=" color:#000000"> <b>Knitting Information</b></legend>
	            <table class="rpt_table" border="1" rules="all" width="100%">
	                <tr>
                        <td width="120" class="must_entry_caption"><strong>Fab Color/Code</strong> 
                        <input type="hidden" id="knitting_update_id" style="width:40px;" /></td>
	                    <td width="200" colspan="2" id="knit_color_td">
	                    
	                    <?
                             
	                        echo create_drop_down( "cbo_knit_fab_color_code",200,$sql,",", 1, "--Select--", $selected, "","","","","","",2);
	                    ?>
	                    </td>
                        <td width="200" colspan="2" class=""><strong>Program No.</strong></td>
	                    <td width="400" id="prog_no_td" colspan="3">
	                     
	                    <?
                            
	                        echo create_drop_down( "cbo_prog_no",400,"","", 1, "--Select--", $selected, "","","","","","");
	                    ?>
	                    </td>
	                </tr>
	                <tr>
                        <td width="120"><strong>Construction</strong></td>
	                    <td align="left" colspan="2" id="construction_td" width="200">
                       <?
                            
	                        echo create_drop_down( "cbo_construction",200,"","", 1, "--Select--", $selected, "","","","","","");
	                    ?>
	                                                        
	                     <td width="120" colspan="2"><strong>Dia : <input type="text" id="txt_dia" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></strong></td>
                        
                           <td width="100"><strong> </strong></td>
                           <td width="70"><strong>Greige Dia</strong></td>
                         <td> <input type="text" id="txt_greige_dia" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
	                </tr> 
                   
                     <tr bgcolor="#CCCCCC">
                    	<td width="120">M/C No </td> 
                        <td width="70">    <input type="text" id="txt_mc_no" class="text_boxes_numeric" style="width:70px;" placeholder="Display" readonly /> </td> 
                        <td width="100">  <b> M/C Dia</b></td>
                        <td width="70"> <input type="text" id="txt_mc_dia" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                        <td width="120"> <b>Dia Type</td>
                         <td width="70"> <input type="text" id="txt_dia_type" class="text_boxes_numeric" style="width:70px;" placeholder="Display" readonly /></td>
                         <td width="100"> <b>Required GSM</td>
                         <td width="70"> <input type="text" id="txt_required_gsm" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  /></td>
                          
                    </tr>
                     <tr>
                    	<td width="120"> M/C Gauge</td> 
                        <td width="70">
						 
                         <input type="text" id="txt_mc_gauge" class="text_boxes_numeric" style="width:70px;" placeholder="Display" readonly /> 
                        </td> 
                       <td width="100"> M/C Type</td> 
                        <td width="70" id="">
						  <input type="text" id="txt_mc_type" class="text_boxes" style="width:70px;" placeholder="Write"  />
                         </td> 
                          <td width="120">Lycra Feeding</td> 
                          <td width="70" id="">
						  <input type="text" id="txt_lycra_feeding" class="text_boxes" style="width:70px;" placeholder="Write"  />
                         </td>
                        
                         <td width="100">Greige GSM</td> 
                          <td width="70" id="">
							 <input type="text" id="txt_greige_gsm" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  />
                        </td>
                    </tr>
                    <tr>
                    	<td width="120"> M/C Brand</td> 
                        <td width="70" id="">
						  <input type="text" id="txt_mc_brand" class="text_boxes" style="width:70px;" placeholder="Write"  /> 
                         </td> 
                        
                         <td width="100"> Dia Type</td> 
                        <td width="70" id="">
						    <input type="text" id="txt_brand_dia_type" class="text_boxes_numeric" style="width:70px;" placeholder="Display"  readonly/> 
                          </td> 
                           <td width="120"> Remarks</td> 
                          <td width="70" colspan="3" id="">
                           <input type="text" id="txt_remarks" class="text_boxes" style="width:70px;" placeholder="Write"  /> 
						 </td>
                          
                    </tr>
                    <tr>
                    	<td> Fabric Composition</td> 
                        <td  colspan="7" id="">
						  <table width="100%"  cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                          <tr align="center">
                          <td><b> Cotton </b> <td>
                          <td><b> Polyester </b> <td>
                          <td><b> Modal </b> <td>
                          <td><b> Viscose </b> <td>
                          <td><b> Nylon </b> <td>
                          <td><b> Elastane </b> <td>
                          <td><b> Others </b> <td>
                          </tr>
                          
                          <tr>
                              <td> <input type="text" id="txt_cotton" class="text_boxes" style="width:70px;" placeholder="Write"  />  <td>
                              <td> <input type="text" id="txt_polyester" class="text_boxes" style="width:70px;" placeholder="Write"  />  <td>
                              <td> <input type="text" id="txt_modal" class="text_boxes" style="width:70px;" placeholder="Write"  />  <td>
                              <td> <input type="text" id="txt_viscose" class="text_boxes" style="width:70px;" placeholder="Write"  />  <td>
                              <td> <input type="text" id="txt_nylon" class="text_boxes" style="width:70px;" placeholder="Write"  />  <td>
                              <td> <input type="text" id="txt_elastane" class="text_boxes" style="width:70px;" placeholder="Write"  />  <td>
                              <td> <input type="text" id="txt_others" class="text_boxes" style="width:70px;" placeholder="Write"  />  <td>
                          </tr>
                          </table>
                         </td> 
                    </tr>
                    
                     <tr>
                    	<td> Stitch length</td> 
                        <td  colspan="7" id="">
						  <table width="100%"  cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                          <tr align="center">
                          <td><b> Knit </b> <td>
                          <td><b> Binding </b> <td>
                          <td><b> Loop </b> <td>
                          <td><b> Yarn Dyed <br>Stripe Measurement</b> <td>
                          <td><b> No. of Color </b> <td>
                          <td><b> Repeat Size </b> <td>
                          <td><b> No of Feeder </b> <td>
                          </tr>
                          
                          <tr>
                              <td> <input type="text" id="txt_knit" class="text_boxes" style="width:70px;" placeholder="Write"  />  <td>
                              <td> <input type="text" id="txt_binding" class="text_boxes" style="width:70px;" placeholder="Write"  />  <td>
                              <td> <input type="text" id="txt_loop" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  />  <td>
                              <td> <input type="text" id="txt_yarn_dyed" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  />  <td>
                              <td> <input type="text" id="txt_no_of_color" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  />  <td>
                              <td> <input type="text" id="txt_repeat" class="text_boxes" style="width:70px;" placeholder="Write"  />  <td>
                              <td> <input type="text" id="txt_no_of_feeder" class="text_boxes_numeric" style="width:70px;" placeholder="Write"  />  <td>
                          </tr>
                          </table>
                         </td> 
                    </tr>
	                
	            </table>
	        </fieldset>
	            <br>
	            <table  style="border:none; width:735px;" cellpadding="0" cellspacing="1" border="0" id="">
	                <tr>
	                    <td align="center" class="button_container">
	                        <? 
	                            echo load_submit_buttons($permission,"fnc_kntting_entry",0,0,"reset_form('knitentry_2','knit_entry_info_list','','','')",3);
	                        ?>
	                    </td>
	                </tr>  
	            </table>
	           <br>
	           
        </div>
    	<div id="knit_entry_info_list" style="width:800px;">    
     </form>
  
		
			
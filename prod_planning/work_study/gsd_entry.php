<form name="gsdentry_1" id="gsdentry_1"  autocomplete="off"> 
    <fieldset style="width:870px; margin-top:5px">
        <legend>Breakdown Entry Info </legend>
        <table cellpadding="0" cellspacing="2" width="100%">
            <tr>
                <td colspan="6" align="center">
                    <strong>System ID: </strong>
                    <input type="text" id="system_no" class="text_boxes_numeric" style="width:128px;" placeholder="Browse" onDblClick="openmypage_sysnum();" readonly />
                    <input type="hidden" id="update_id" readonly />
                    <input type="hidden" name="hidden_quotation_id" id="hidden_quotation_id">
                 </td>
            </tr>
            <tr>
                <td width="150" align="left">Company Name</td> 
                <td>
                    <?
                    echo create_drop_down( "cbo_company_id", 140,"select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name", 1, "-- All company --", 0, "variable_setting_work_study()" );
                    ?>
                </td>
                 <td width="100"><strong>Extention No.</strong></td> 
                 <td align="left"><input type="text" id="txt_ext_no" class="text_boxes_numeric" style="width:128px;" placeholder="Display" readonly /></td>
                 <td width="100"><strong>Copy</strong></td>
                 <td align="left"><? echo create_drop_down( "cbo_bulletin_copy", 140, $bulletin_copy_arr, "", 1, "--  Select --", 0, "fnc_itemChange(this.value);", 1); ?></td>
            </tr>
            <tr>
                <td class="must_entry_caption">Style Ref</td>                                              
                <td>
                    <input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:128px" placeholder="Write/Browse" onDblClick="openmypage_style_ref()" title=""/>
                    <input type="hidden" id="txt_style_id" name="txt_style_id" value="">
                </td>
                
                <td>Internal Ref</td>
                <td><input type="text" name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:128px" placeholder="Write" /></td>
                
                <td>Custom Style</td>
                <td><input type="text" name="txt_custom_style" id="txt_custom_style" class="text_boxes" style="width:128px" placeholder="Write" /></td>
            </tr> 
            <tr>
                <td class="must_entry_caption">Buyer Name</td>                                              
                <td>
                    <? echo create_drop_down( "cbo_buyer", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name", "id,buyer_name", 1, "--  Select --", 0, "", 0); ?>
                </td>

                <td class="must_entry_caption">Process</td>
                <td>
                    <?= create_drop_down("cbo_process_id", 140, $machine_category, "", 1, "-- Select --", 1, "load_drop_down( 'requires/ws_gsd_controller',this.value, 'load_drop_down_resource', 'resource_td' );load_operation();load_drop_down( 'requires/ws_gsd_controller',this.value, 'load_drop_down_resource_tc', 'resource_tc_td' );", '', "4,7,8");?>
                </td>
                <td>Job No</td>
                <td>
                    <input type="text" id="txt_job_no" class="text_boxes" style="width:128px;" placeholder="Browse" onDblClick="openmypage_job();" readonly />
                    <input type="hidden" id="txt_job_id" readonly />
                </td>
            </tr>
            <tr>
                <td class="must_entry_caption">Working Hour</td>
                <td><input type="text" name="txt_working_hour" id="txt_working_hour" class="text_boxes_numeric" style="width:128px" value="1" onKeyUp="fnc_move_cursor(this.value,'txt_working_hour','cbo_action',2,23)" disabled="disabled" readonly="readonly"/></td>
                <td class="must_entry_caption">Garments Item</td>
                <td id="gmt_item_td"><? asort($garments_item); echo create_drop_down( "cbo_gmt_item", 140, $garments_item, "", 1, "--  Select --", 0, "load_operation();load_product_code(this.value);", 0); ?><span style="background-color: white;" id="show_product_code"></span></td>
                <td>Prod. Dept</td>
                <td><? echo create_drop_down( "cbo_product_department", 140, $product_dept, "", 1, "-Select-", $selected, "load_operation()", 0, "" ); ?></td>
            </tr>
            <tr>
                <td>Fabric Type</td>
                <td><input type="text" name="txt_fabric_type" id="txt_fabric_type" class="text_boxes" style="width:128px" placeholder="Write Fabric Type" /></td>
                 <td>Bulletin Type</td>
                <td><? echo create_drop_down( "cbo_bulletin_type",138, $bulletin_type_arr,"", 1, "--Select Bulletin Type--", 0, "if(this.value==4){document.getElementById('txt_applicable_period').disabled = false;}else if(this.value==3){document.getElementById('txt_applicable_period').disabled = true; document.getElementById('txt_applicable_period').value = '".date('d-m-Y')."';}else{document.getElementById('txt_applicable_period').disabled = true; document.getElementById('txt_applicable_period').value = '';}fnc_smv_active()","","","","","",""); ?></td>
                <td>Color Type</td>
                <td><? echo create_drop_down( "cbo_colortype", 140, $color_type, "", 1, "-Select-", $selected, "", 0, "" ); ?></td>
            </tr>
            <tr>
                <td>Applicable Period</td>
                <td><input type="text" name="txt_applicable_period" id="txt_applicable_period" class="datepicker" style="width:128px" readonly="readonly" disabled="disabled" /></td> 
                <td >Product Description</td>
                <td><input type="text" name="txt_product_description" id="txt_product_description" class="text_boxes" style="width:128px"  /></td>
                <td>Approved</td>
                <td><? echo create_drop_down( "cbo_approved_status", 140, $yes_no,"", 0, "", 2, "",1,"" ); ?></td>
            </tr>
            <tr>
                <td>Remarks </td>
                <td><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:128px" placeholder="Write Remarks" /></td>
                <td>Complexity Level</td>
                <td><? echo create_drop_down( "complexity_level", 140, $complexity_level, "",1," -- Select --", 0, "",'','' ); ?></td>
                <td>Action</td>
                <td><? echo create_drop_down( "cbo_action",138, $row_status,"", 1, "--Select Action--", 1, "","","","","","",""); ?></td>
            </tr>
            <tr>
                <td>File</td>
                <td>
                    <input type="button" class="image_uploader" style="width:140px;" value="Click Add/View IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'gsd_entry', 0 ,1)">
                </td>
                <td>
                <u><a id="pending_style_btn" type="button" onclick="fn_pending_style_popup()" style="width:120px; cursor:pointer; background-color:yellow">Pending style</a></u>
                <!-- <input id="pending_style_btn"  style="width:120px;" onclick="fn_pending_style_popup()" value="Pending style" type="button"> -->
                </td>
                <td>
					<input type="button" class="image_uploader" style="width:140px" value="ADD FILE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'gsd_entry', 2 ,1)">	
                </td>
                <td>Offer Qnty</td>
                <td><input type="text" name="txt_offer_qnty" id="txt_offer_qnty" class="text_boxes_numeric" style="width:128px" readonly/></td>
            </tr>
            <tr>
                <td>Sample Req.</td>
                <td>
                    <input type="text" id="txt_req_no" name="txt_req_no" class="text_boxes" style="width:128px;" placeholder="Browse" onDblClick="openmypage_sampleReq();" readonly/>
                </td>
            </tr>
            <tr height="5"><td colspan="6">&nbsp;</td></tr>
        </table>
    </fieldset>
    <fieldset style="width:870px; margin-top:10px">
        <table cellpadding="0" cellspacing="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead class="form_table_header">
                <th width="30" class="must_entry_caption">Seq. No</th>
                <th width="110" class="must_entry_caption">Body Part</th>
                <th class="must_entry_caption">Operation</th>
                <th width="60" class="must_entry_caption">Seam Length</th>
                <th width="100" class="must_entry_caption">Resource</th>
                <th width="55">Attach</th>
                <th width="55" class="must_entry_caption">Machine SMV</th>
                <th width="55" class="must_entry_caption">Manual SMV</th>
                <th width="50">Eff%</th>
                <th width="50" title="60/ Respective Operation SMV">Tgt 100%</th>
                <th width="50" title="60/Respective Operation SMV*Eff %">Tgt (eff.)</th>
            </thead>
            <tr class="general">
                <td>
                    <input type="text" name="txt_seqNo" id="txt_seqNo" value="1" class="text_boxes_numeric" style="width:25px" readonly />
                    <input type="hidden" name="txt_dtls_id" id="txt_dtls_id" />
                </td>
                <td>
                    <?
                    $sql_bpart="select a.id, a.body_part_full_name, b.entry_page_id from lib_body_part_tag_entry_page b, lib_body_part a where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0";
                    $sql_result=sql_select($sql_bpart);
                    foreach ($sql_result as $value) 
                    {
                        if($value[csf("entry_page_id")]==149)
                        {
                            $tag_body_part_arr[$value[csf("id")]]=$value[csf("body_part_full_name")];
                        }
                        $all_body_part_arr[$value[csf("id")]]=$value[csf("body_part_full_name")];
                    }
					// print_r();
                    $body_partArr=array();
                    if(count($tag_body_part_arr)>0)
                    {
                        $body_partArr=$tag_body_part_arr;   
                    }
                    else
                    {
                        $body_partArr=$all_body_part_arr;     
                    }
                    asort($body_partArr);
                    echo create_drop_down( "cbo_body_part",110,$body_partArr,"", 1, "--Select--", 0, "load_operation()","","","","","");
                    //2,3,6,7,9,10,11,26,28,40,53,59,60,63,79,92,106,196,197
                    ?>
                </td>
                <td>
                    <input type="text" name="txt_operation" id="txt_operation"  class="text_boxes" style="width:95%" placeholder="Display" readonly />
                    <input type="hidden" id="hidden_operation" > <!--onDblClick="openmypage_operation();"-->
                </td>
                <td>
                    <input type="text" name="txt_seam_length" id="txt_seam_length"  class="text_boxes_numeric" style="width:60px" readonly="readonly"/>
                </td>
                <td id="resource_td">
                    <?
                    echo create_drop_down( "cbo_resource",100,array(),"", 1, "--Select--", 0, "","0","","","","","");
                    ?>
                </td>
                <td>
                    <input type="text" name="txt_attachment" id="txt_attachment"  class="text_boxes" style="width:45px" placeholder="Browse" onDblClick="openmypage_attachment();" readonly />
                    <input type="hidden" name="txt_attachment_id" id="txt_attachment_id" />
                </td>
                <td>
                    <input type="text" name="txt_operator" id="txt_operator" class="text_boxes_numeric" style="width:45px" onkeyup="calculate_target()" readonly   />
                </td>
                <td>
                    <input type="text" name="txt_helper" id="txt_helper" class="text_boxes_numeric" style="width:45px" onkeyup="calculate_target()" readonly />
                </td>
                <td>
                    <input type="text" name="txt_efficiency" id="txt_efficiency"  class="text_boxes_numeric" style="width:35px" onkeyup="calculate_target();" />
                </td>
                <td>
                    <input type="text" name="txt_tgt_perc" id="txt_tgt_perc"  class="text_boxes_numeric" style="width:35px" readonly="readonly" />
                </td>
                <td>
                    <input type="text" name="txt_tgt_eff" id="txt_tgt_eff"  class="text_boxes_numeric" style="width:35px" readonly="readonly" />
                </td>
            </tr>
            <tr><td colspan="8"><input type="button" name="dlt" id="dlt" class="formbuttonplasminus" style="width:150px" value="Delete Operation" onclick="dlt_operation();" /></td></tr>
        </table>
        <table cellpadding="0" cellspacing="2" width="100%">
            <tr>
                <td>
                    <strong>All Operation Count </strong>
                </td>
                <td>   
                    <input type="text" name="txt_operation_count" id="txt_operation_count"  class="text_boxes_numeric" style="width:80px" readonly />
                </td>
                <td>
                    <strong>M/C Operation Count </strong>
                </td>
                <td>
                    <input type="text" name="txt_mcOperationCount" id="txt_mcOperationCount"  class="text_boxes_numeric" style="width:80px" readonly />
                </td>
                <td>
                    <strong>Total SMV </strong>
                </td>
                <td>
                    <input type="text" name="txt_tot_smv" id="txt_tot_smv"  class="text_boxes_numeric" style="width:80px" readonly />
                </td>
                <td>
                    <strong>Total MC SMV</strong>
                </td>
                <td>
                    <input type="text" name="txt_mc_smv" id="txt_mc_smv"  class="text_boxes_numeric" style="width:80px" readonly />
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Total Manual SMV </strong>
                </td>
                <td>
                    <input type="text" name="txt_manual_smv" id="txt_manual_smv"  class="text_boxes_numeric" style="width:80px" readonly />
                </td>
                <td>
                    <strong>Total Finishing SMV</strong>
                </td>
                <td>
                    <input type="text" name="txt_finishing_smv" id="txt_finishing_smv"  class="text_boxes_numeric" style="width:80px" readonly />
                </td>
                <td>
                    <strong>SPI</strong>
                </td>
                <td>
                    <?php
                    $spiArr = array(1=>"6/7", 2=>"7/8", 3=>"8/9", 4=>"9/10", 5=>"10/11", 6=>"11/12", 7=>"12/13", 8=>"13/14", 9=>"14/15", 10=>"15/16", 11=>"16/17");
                    ?>
                    <?= create_drop_down("cbo_spi", 91, $spiArr, "", 1, "--Select--", 0, "", "0", "", "", "", "", "");?>
                </td>
                <td>
                    <strong>Needle Size</strong>
                </td>
                <td>
                    <?php 
                    $needle_sizeArr = array(1=>"6", 2=>"7", 3=>"8", 4=>"9", 5=>"10", 6=>"11");
                    ?>
                    <?= create_drop_down("cbo_needle_size", 91, $needle_sizeArr, "", 1, "--Select--", 0, "", "0", "", "", "", "", "");?>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Risk Factor</strong>
                </td>
                <td>
                    <?php 
                    $risk_factor_sizeArr = array(1=>"Critical", 2=>"Semi Critical", 3=>"Normal");
                    ?>
                    <?= create_drop_down("cbo_risk_factor", 91 , $risk_factor_sizeArr, "", 1, "--Select--", 0, "", "0", "", "", "", "", "");?>
                </td>
                <td>
                    <strong>Remarks</strong>
                </td>
                <td> 
                    <input type="text" name="txt_dlts_remarks" id="txt_dlts_remarks"  class="text_boxes" style="width:120px" placeholder="Write Remarks"/>
                </td>
            </tr>
            <tr>
                <td colspan="8">
                    <div id="worningMessage" style="font-size:18px; color:#F00; font-weight:bold; text-align:center"></div>
                    <input type="hidden" value="1" name="update_parmission" id="update_parmission" />
                </td>
            </tr>
            <tr>
                <td colspan="4" align="right" class="button_container" valign="top">
                    <?= load_submit_buttons($permission,"fnc_gsd_entry", 0, 0, "reset_form('gsdentry_1', 'gsd_entry_info_list', '','txt_working_hour,1*txt_seqNo, 1', '')", 1, 1);?>
                </td>
                <td colspan="4" class="button_container" valign="top">
                    <input type="button" name="button" class="formbutton btn_copy_extension" value="Copy Bulletin"  onClick="fnc_copy_bulletin();" />&nbsp;
                    <input type="button" name="button" class="formbutton" value="Re-arrange Seq."  onClick="re_arrange_seq();accordion_menu( 'accordion_h1','list_operation_container', '');" />
                    
                    <input type="button" name="button" class="formbutton" value="Operation Sticker"  onClick="generate_operation_sticker();" /><br>
                    <input type="button" class="formbutton" style="width:80px" name="print" id="print" value="Print" onclick="print_breakdown(2)" />
                    <input type="button" class="formbutton" style="width:80px" name="print2" id="print2" value="Print 2" onclick="print_breakdown(3)" />
                </td>
            </tr>
        </table>
    </fieldset>
</form>
<div id="gsd_entry_info_list" style="width:880px; margin-top:5px" align="center"></div>
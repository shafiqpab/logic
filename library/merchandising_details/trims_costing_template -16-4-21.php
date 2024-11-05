<?
	session_start();
	if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
	require_once('../../includes/common.php');
	extract($_REQUEST);
	$_SESSION['page_permission']=$permission;
	//----------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents(" Trim Costing Template", "../../", 1, 1, $unicode,1,'');
?>
 
<script type="text/javascript">
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	function fnc_trim_cost_temp( operation )
	{
		if (form_validation('cbo_rel_buyer*cbo_trims_group*cbo_cons_uom*txt_cons_dzn_gmts*txt_purchase_rate','Related Buyer','Trims Group','Cons. UOM','Cons/Dzn Gmts','Purchase Rate')==false)
		{
			return;
		}
		else // Save Here
		{
			eval(get_submitted_variables('cbo_rel_buyer*txt_user_code*cbo_trims_group*cbo_cons_uom*txt_cons_dzn_gmts*txt_purchase_rate*txt_amount*cbo_apvl_req*cbo_supplyer*cbo_status*update_id'));
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_rel_buyer*txt_user_code*cbo_trims_group*cbo_cons_uom*txt_cons_dzn_gmts*txt_purchase_rate*txt_amount*cbo_apvl_req*cbo_supplyer*cbo_status*update_id*txt_sub_ref*txt_desc',"../../");
			freeze_window(operation);
			http.open("POST","requires/trims_cost_template_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_trim_cost_temp_reponse;
		}
	}
	
	function fnc_trim_cost_temp_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert (http.responseText)
			var reponse=http.responseText.split('**');
			show_msg(trim(reponse[0]));
			show_list_view(reponse[2],'on_change_data','trim_cost_container','../merchandising_details/requires/trims_cost_template_controller','setFilterGrid("list_view",-1)');
			reset_form('','','txt_user_code*cbo_trims_group*cbo_cons_uom*txt_cons_dzn_gmts*txt_purchase_rate*txt_amount*cbo_apvl_req*cbo_supplyer*update_id*txt_sub_ref','','','');
			set_button_status(0, permission, 'fnc_trimcosttemp_1');
			release_freezing();
		}
	}
</script>
</head>	
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%; position:relative; margin-bottom:5px; margin-top:5px">
		<? echo load_freeze_divs ("../../",$permission);  ?>	     
        <form name="trimcosttemp_1" id="trimcosttemp_1" autocomplete="off">
        <fieldset style="width:700px;">
        <legend>Trim Costing Template</legend>
            <table width="100%" border="0" cellpadding="0" cellspacing="2">
                <tr>
                    <td width="80" class="must_entry_caption">Related Buyer</td>
                    <td width="90">
                        <? 
                            echo create_drop_down( "cbo_rel_buyer",220, "select buyer_name,id from  lib_buyer where is_deleted=0 and status_active=1 order by buyer_name", "id,buyer_name", 1, '--Select--','', "show_list_view(this.value,'on_change_data','trim_cost_container','../merchandising_details/requires/trims_cost_template_controller','')",''); 
                        ?>
                    </td>
                    <td align="right" width="300">Status</td>
                    <td width="70"> <? echo create_drop_down( "cbo_status", 160, $row_status,'', $is_select, $select_text, 1, $onchange_func ); ?></td>
                </tr>
                <tr>
                    <table width="1000" cellpadding="0" cellspacing="0"  class="rpt_table" border="1" rules="all">
                        <thead>
                            <th align="center" width="40">User Code</th>
                            <th align="center" width="40" class="must_entry_caption">Trims Group</th>
                            <th align="center" width="100" >Item Desc.</th>
                            <th align="center" width="40" class="must_entry_caption">Cons. UOM</th>
                            <th align="center" width="100">Brand/Sup Ref.</th>
                            <th align="center" width="40" class="must_entry_caption">Cons/Dzn Gmts</th>
                            <th align="center" width="40" class="must_entry_caption">Purchase Rate</th>
                            <th align="center" width="40">Amount</th>
                            <th align="center" width="40">Approval Required</th>
                            <th align="center" width="40">Supplier</th>
                        </thead>
                        <tr>
                            <td align="center"><input type="text" name="txt_user_code" id="txt_user_code" class="text_boxes" style="width:80px" maxlength="50" title="Maximum 50 Character"></td>
                            <td align="center">
								<? 
                                	echo create_drop_down( "cbo_trims_group", 180, "select item_name,id from lib_item_group where item_category=4 and is_deleted=0  and 
                                status_active=1 order by item_name", "id,item_name", 1, '--Select--', 0,"load_drop_down( 'requires/trims_cost_template_controller', this.value, 'set_cons_uom', 'cons_uom_td' )" );
                                ?></td>
                            <td>
                            	<input type="text" name="txt_desc" id="txt_desc" class="text_boxes" style="width:100px"  />
                            </td>
                            <td align="center" id="cons_uom_td"><? echo create_drop_down( "cbo_cons_uom", 70, $unit_of_measurement,"", "", "", 0, "",1,"" ); ?></td>
                            <td>
                            	<input type="text" name="txt_sub_ref" id="txt_sub_ref" class="text_boxes" style="width:80px"  maxlength="50" title="Maximum 50 Character" />
                            </td>
                            <td align="center">
                            	<input type="text" name="txt_cons_dzn_gmts" id="txt_cons_dzn_gmts" class="text_boxes_numeric" style="width:80px" onBlur="math_operation('txt_amount','txt_cons_dzn_gmts*txt_purchase_rate','*','',{dec_type:1,comma:0,currency:2})">						
                            </td>
                            <td align="center">
                            	<input type="text" name="txt_purchase_rate" id="txt_purchase_rate" class="text_boxes_numeric" style="width:80px" maxlength="50" onBlur="math_operation('txt_amount','txt_cons_dzn_gmts*txt_purchase_rate','*','' ,{dec_type:1,comma:0,currency:2})">
                            </td>
                            <td align="center"><input type="text" name="txt_amount" id="txt_amount" class="text_boxes_numeric" style="width:80px" readonly /></td>
                            <td align="center"><? echo create_drop_down( "cbo_apvl_req", 70, $yes_no,"", "", "", 2, "" ); ?></td>
                            <td align="center">
								<? 
                                	echo create_drop_down( "cbo_supplyer",250, "select a.supplier_name,a.id from  lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(4,5) and a.is_deleted=0  and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, '--Select--', 0, $onchange_func  );
                                ?>
                            </td>
                        </tr> 
                    </table>
                </tr>
                <tr>
                    <td colspan="4" align="center" height="15">
                    <input type="hidden" name="update_id" id="update_id">	
                    </td>		 
                </tr>
                <tr>
                    <td colspan="4"  height="40" valign="bottom" align="center" class="button_container" >
                    <? 
                    echo load_submit_buttons( $permission, "fnc_trim_cost_temp", 0,0 ,"reset_form('trimcosttemp_1','trim_cost_container','')",1);
                    ?>						
                    </td>
                </tr>
                <tr>
                    <fieldset>
                        <td width="80"><b>Buyer</b></td>
                        <td width="90">
                            <? 
                                echo create_drop_down( "cbo_buyer",220, "select buyer_name,id from  lib_buyer where is_deleted=0 and  status_active=1 order by buyer_name", "id,buyer_name", 1, '--Select--','', "show_list_view(this.value,'on_change_data','trim_cost_container','../merchandising_details/requires/trims_cost_template_controller','')",''); 
                            ?>
                        </td>
                    </fieldset>
                </tr>
                <tr>
                    <div style="width:895px; float:left; min-height:40px; margin:auto" align="center" id="trim_cost_container"></div>
                </tr>
            </table>
        </fieldset>
        </form>	
    </div>
</body>
<script>set_multiselect('cbo_rel_buyer','0','0','','');</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

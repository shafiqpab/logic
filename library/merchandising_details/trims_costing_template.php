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
		//alert(operation);
		if (form_validation('cbo_rel_buyer*template_name*cbo_trims_group*cbo_cons_uom*txt_cons_dzn_gmts*txt_purchase_rate','Related Buyer','Trims Group','Cons. UOM','Cons/Dzn Gmts','Purchase Rate')==false)
		{
			return;
		}
		else // Save Here  
		{
			eval(get_submitted_variables('cbo_rel_buyer*txt_user_code*template_name*cbo_trims_group*cbo_cons_uom*txt_cons_dzn_gmts*txt_tot_cons*txt_ex_per*txt_purchase_rate*txt_amount*cbo_apvl_req*cbo_supplyer*cbo_status*update_id*template_name*cbo_brand_name'));
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_rel_buyer*txt_user_code*cbo_trims_group*cbo_cons_uom*txt_cons_dzn_gmts*txt_tot_cons*txt_ex_per*txt_purchase_rate*txt_amount*cbo_apvl_req*cbo_supplyer*cbo_status*update_id*txt_sub_ref*txt_desc*template_name*hidden_template_name*cbo_brand_name',"../../");
			//alert(data);
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
			 var data =reponse[3]+'_'+'0';
			show_list_view(data,'on_change_data','trim_cost_container','../merchandising_details/requires/trims_cost_template_controller','setFilterGrid("list_view",-1)');
			reset_form('','','txt_user_code*cbo_trims_group*cbo_cons_uom*txt_cons_dzn_gmts*txt_ex_per*txt_tot_cons*txt_purchase_rate*txt_amount*cbo_apvl_req*cbo_supplyer*update_id*txt_sub_ref','','','');
			set_button_status(0, permission, 'fnc_trimcosttemp_1');
			release_freezing();
		}
	}
	function fnc_ex_calculation(type)
	{
		//alert(val);
		
		if(type==1)
		{
				var tot_cons_dzn_gmts= $("#txt_cons_dzn_gmts").val()*1;
				 //$("#txt_tot_cons").val(txt_cons_dzn_gmts);
				 
				 var txt_ex_per= $("#txt_ex_per").val()*1;
				 if(txt_ex_per>0)
				 {
					 var txt_cons_dzn_gmts= $("#txt_cons_dzn_gmts").val()*1;
					var txt_ex_per= $("#txt_ex_per").val()*1;
					var tot_cons_dzn_gmts= txt_cons_dzn_gmts+(txt_cons_dzn_gmts*txt_ex_per)/100;
				 }
		}
		else
		{
			var txt_cons_dzn_gmts= $("#txt_cons_dzn_gmts").val()*1;
			var txt_ex_per= $("#txt_ex_per").val()*1;
			var tot_cons_dzn_gmts= txt_cons_dzn_gmts+(txt_cons_dzn_gmts*txt_ex_per)/100;
		}
			 $("#txt_tot_cons").val(tot_cons_dzn_gmts)
	}
    function getBrandId() 
	{//load_drop_down( 'requires/trims_cost_template_controller',$('#cbo_rel_buyer').val(), 'load_drop_down_brand', 'brand_td' );
	    var rel_buyer = document.getElementById('cbo_rel_buyer').value;
	    //var search_type = document.getElementById('cbo_search_by').value;
	    if(rel_buyer !='') {
		  var data="action=load_drop_down_brand&data="+rel_buyer;
		  //alert(data);die;
		  http.open("POST","requires/trims_cost_template_controller.php",true);
		  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		  http.send(data); 
		  http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#brand_td').html(response);
	              set_multiselect('cbo_brand_name','0','0','','0');
	              // setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location_name,'0');getLocationId();") ,3000)]; 
	              //========================
	              // load_drop_down( 'requires/order_wise_emb_report_controller_v2', company_id, 'load_drop_down_buyer', 'buyer_td' );
                  //load_drop_down( 'requires/trims_cost_template_controller',$('#cbo_rel_buyer').val(), 'load_drop_down_brand', 'brand_td' );
	          }			 
	      };
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
                    <td width="90" colspan="" id="rel_buyer_td">
                        <? 
                            echo create_drop_down( "cbo_rel_buyer",220, "select buyer_name,id from  lib_buyer where is_deleted=0 and status_active=1 order by buyer_name", "id,buyer_name", 1, '--Select--','', "show_list_view(this.value,'on_change_data','trim_cost_container','../merchandising_details/requires/trims_cost_template_controller','')",''); 
                        ?>
                    </td>
                     <td width="100">Brand<td>
                     <td width="90" id="brand_td">
                        <? 
                            echo create_drop_down( "cbo_brand_name", 150, $blank_array,"", 1, "- Select- ", "", "" );
                        ?>
                    </td>
                     <td width="100" class="must_entry_caption">Template Name<td>
                    <td width="180" colspan="" align="left">
                        <input class="text_boxes" type="text" name="template_name" id="template_name" >
                        <input type="hidden" name="hidden_template_name" id="hidden_template_name">
                        <td>
                        
                    <td align="right" width="300">Status</td>
                    <td width="70"> <? echo create_drop_down( "cbo_status", 160, $row_status,'', $is_select, $select_text, 1, $onchange_func ); ?></td>
                </tr>
                <tr>
                    <table width="1110" cellpadding="0" cellspacing="0"  class="rpt_table" border="1" rules="all">
                        <thead>
                            <th align="center" width="40">User Code</th>
                            <th align="center" width="40" class="must_entry_caption">Trims Group</th>
                            <th align="center" width="100" >Item Desc.</th>
                            <th align="center" width="40" class="must_entry_caption">Cons. UOM</th>
                            <th align="center" width="100">Brand/Sup Ref.</th>
                            <th align="center" width="40" class="must_entry_caption">Cons/Dzn Gmts</th>
                            <th align="center" width="50" class="">Ex.%</th>
                            <th align="center" width="60" class="">Total Cons.</th>
                             
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
                            	<input type="text" name="txt_cons_dzn_gmts" id="txt_cons_dzn_gmts" class="text_boxes_numeric" style="width:80px" onBlur="fnc_ex_calculation(1)">						
                            </td>
                            <td align="center">
                            	<input type="text" name="txt_ex_per" id="txt_ex_per" class="text_boxes_numeric" style="width:50px" onBlur="fnc_ex_calculation(2);math_operation('txt_amount','txt_tot_cons*txt_purchase_rate','*','' ,{dec_type:1,comma:0,currency:2})">						
                            </td>
                            <td align="center">
                            	<input type="text" name="txt_tot_cons" id="txt_tot_cons" class="text_boxes_numeric" style="width:60px" readonly > 						
                            </td>
                            
                            <td align="center">
                            	<input type="text" name="txt_purchase_rate" id="txt_purchase_rate" class="text_boxes_numeric" style="width:80px" maxlength="50" onBlur="math_operation('txt_amount','txt_tot_cons*txt_purchase_rate','*','' ,{dec_type:1,comma:0,currency:2})">
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
                                //show_list_view(this.value,'on_change_data','trim_cost_container','../merchandising_details/requires/trims_cost_template_controller','')
								echo create_drop_down( "cbo_buyer",220, "select buyer_name,id from  lib_buyer where is_deleted=0 and  status_active=1 order by buyer_name", "id,buyer_name", 1, '--Select--','', "load_drop_down( 'requires/trims_cost_template_controller', this.value, 'load_drop_down_template', 'td_template' );",''); 
                            //
							?>
                        </td>
                         <td><b  style="color: blue">Template Name</b></td>
                        <td> <span id="td_template"><? 
                            echo create_drop_down("cbo_template_name",150,"select template_name from lib_trim_costing_temp where is_deleted=0 group by template_name ORDER BY template_name ASC","template_name,template_name",1,'--Select--',0,'');
                         ?></span></td>
                         <td><input style="width: 60px" type="button" class="formbutton" name="button" value="show" onClick="show_temp_list()"></td>
                         <td><input style="width: 100px" type="button" class="formbutton" name="button" value="Copy Template" onClick="openmypage_template_copy('Copy Template')">
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
     <script type="text/javascript">
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
    var permission='<? echo $permission; ?>';
    function show_temp_list()
    {
        var buyer = document.getElementById('cbo_buyer').value;
        var templateName = document.getElementById('cbo_template_name').value;
        if(buyer != 0){
         set_multiselect('cbo_rel_buyer','0','1',buyer,'0');   
        }
        document.getElementById('template_name').value = templateName;
        if (form_validation('cbo_template_name','Template Name')==false)
        {
            return;
        }
        else
        {
        show_list_view(document.getElementById('cbo_template_name').value+'_'+document.getElementById('cbo_buyer').value,'on_change_data','trim_cost_container','requires/trims_cost_template_controller','setFilterGrid(\'list_view\',-1)');
        
            getBrandId();
        }

    }
    function fnc_trim_cost_temp( operation )
    {
        if (form_validation('cbo_rel_buyer*template_name*cbo_trims_group*cbo_cons_uom','Related Buyer','Template Name','Trims Group','Cons. UOM')==false)
        {
            return;
        }
        else
        {
            eval(get_submitted_variables('cbo_rel_buyer*txt_user_code*cbo_trims_group*cbo_cons_uom*txt_cons_dzn_gmts*txt_tot_cons*txt_ex_per*txt_purchase_rate*txt_amount*cbo_apvl_req*cbo_supplyer*cbo_status*update_id*template_name*cbo_brand_name'));
            var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_rel_buyer*txt_user_code*cbo_trims_group*cbo_cons_uom*txt_cons_dzn_gmts*txt_tot_cons*txt_ex_per*txt_purchase_rate*txt_amount*cbo_apvl_req*cbo_supplyer*cbo_status*update_id*txt_sub_ref*txt_desc*template_name*hidden_template_name*cbo_brand_name',"../../");
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
            var data =reponse[3]+'_'+'0';
            load_drop_down('requires/trims_cost_template_controller','', 'get_template_dropdown', 'td_template' );
             $('#cbo_template_name').val(reponse[3]);
             $('#hidden_template_name').val(reponse[3]);
            
            show_list_view(data,'on_change_data','trim_cost_container','requires/trims_cost_template_controller','setFilterGrid("list_view",-1)');
            reset_form('','','txt_user_code*txt_desc*cbo_trims_group*cbo_cons_uom*txt_cons_dzn_gmts*txt_ex_per*txt_tot_cons*txt_purchase_rate*txt_amount*cbo_apvl_req*cbo_supplyer*update_id*txt_sub_ref','','','');
            set_button_status(0, permission, 'fnc_trim_cost_temp',1);
            release_freezing();
        }
    }
    function openmypage_template_copy(title)
    {        
        var page_link='requires/trims_cost_template_controller.php?action=copy_template_popup';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=320px,height=150px,center=1,resize=1,scrolling=0','../')
            emailwindow.onclose = function()
            {   var theform = this.contentDoc.forms[0];
                var hidden_template_name = this.contentDoc.getElementById('hidden_template_name').value;
                if(hidden_template_name != '0'){
                   load_drop_down('requires/trims_cost_template_controller','', 'get_template_dropdown', 'td_template' );
                $('#cbo_template_name').val(this.contentDoc.getElementById('hidden_buyer_name').value);
                show_list_view(hidden_template_name,'on_change_data','trim_cost_container','requires/trims_cost_template_controller','setFilterGrid("list_view",-1)'); 
                $('#messagebox_main', window.parent.document).fadeTo(100,1,function(){ 
                    $(this).html('Copy Template Successful.').removeClass('messagebox').addClass('messagebox_success').fadeOut(2500);
                    });
                }
                else{
                    $('#messagebox_main', window.parent.document).fadeTo(100,1,function(){ 
                    $(this).html('Copy Template Faield.').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
                    });
                }
                
            }
    }

</script>
</body>

<script>set_multiselect('cbo_rel_buyer','0','0','','','');
setTimeout[($("#rel_buyer_td a").attr("onclick","disappear_list(cbo_rel_buyer,'0');getBrandId();") ,3000)]; 
set_multiselect('cbo_brand_name','0','0','','0');</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
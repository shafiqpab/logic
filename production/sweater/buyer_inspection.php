<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knit Garments Order Entry
				
Functionality	:	
JS Functions	:
Created by		:	Bilas 
Creation date 	: 	20-02-2013
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//$inpLevelArray = [1=>'In-line Inspection',2=>'Mid-line Inspection',3=>'Final Inspection'];
$inpLevelArray = array(1=>'In-line Inspection',2=>'Mid-line Inspection',3=>'Final Inspection');
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Final Inspection Info","../../", 1, 1, $unicode,'','');

?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	 
	<?php
	if($_SESSION['logic_erp']['data_arr'][723]){
		echo "var field_level_data= " . json_encode($_SESSION['logic_erp']['data_arr'][723]). ";\n";
	}
	?>

	function openmypage_order(page_link,title)
	{
		page_link=page_link+get_submitted_data_string('cbo_company_name*garments_nature','../../');
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1153px,height=470px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];;
			var job=this.contentDoc.getElementById("selected_job");
			if (job.value!="")
			{
				freeze_window(5);
				document.getElementById('txt_job_no').value=job.value;
				get_php_form_data( job.value, "populate_order_data_from_search_popup", "requires/buyer_inspection_controller" );
				//load_drop_down( 'requires/buyer_inspection_controller', job.value, 'load_drop_down_po_number', 'order_drop_down_td' );
				load_drop_down( 'requires/buyer_inspection_controller', job.value, 'load_drop_down_week_no', 'week_drop_down_td' );
				load_drop_down( 'requires/buyer_inspection_controller', job.value, 'load_drop_down_country_id', 'country_drop_down_td' );
				
				show_list_view(job.value,'show_active_listview','inspection_production_list_view','requires/buyer_inspection_controller','setFilterGrid(\'tbl_list_search\',-1)');
					
				reset_form('','','cbo_order_id*txt_po_quantity*txt_pub_shipment_date*txt_inspection_qnty*txt_cum_inspection_qnty*cbo_inspection_status*cbo_cause*txt_comments*txt_mst_id*cbo_week_no*cbo_country_id','','');
				release_freezing();
			}
		}
	}

	function open_set_popup(unit_id)
	{
		var txt_quotation_id=document.getElementById('txt_job_no').value;
		var set_breck_down=document.getElementById('set_breck_down').value;
		var tot_set_qnty=document.getElementById('tot_set_qnty').value;
		var page_link="requires/buyer_inspection_controller.php?txt_quotation_id="+trim(txt_quotation_id)+"&action=open_set_list_view&set_breck_down="+set_breck_down+"&tot_set_qnty="+tot_set_qnty;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Set Details", 'width=400px,height=250px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var set_breck_down=this.contentDoc.getElementById("set_breck_down") //Access form field with id="emailfield"
			var item_id=this.contentDoc.getElementById("item_id") //Access form field with id="emailfield"
			var tot_set_qnty=this.contentDoc.getElementById("tot_set_qnty") //Access form field with id="emailfield"
			document.getElementById('set_breck_down').value=set_breck_down.value;
			document.getElementById('item_id').value=item_id.value;
			document.getElementById('tot_set_qnty').value=tot_set_qnty.value;

		}		
	}
	
	function openOrderPoup()
	{
		if (form_validation('txt_job_no','Job No')==false)
		{
			return;   
		}
		else
		{	
			var txt_job_no=document.getElementById('txt_job_no').value;
			 
			var page_link="requires/buyer_inspection_controller.php?action=open_order_popup&txt_job_no="+txt_job_no;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Order Popup", 'width=600px,height=350px,center=1,resize=1,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				 var hidden_order_val=this.contentDoc.getElementById("hidden_order_val").value ;
				 var hidden_order_id=this.contentDoc.getElementById("hidden_order_id").value ;
				 var hidden_actual_order_id=this.contentDoc.getElementById("hidden_actual_order_id").value ;
				 
				 if(hidden_order_id)
				 {	

				 	$("#cbo_order_val").val(hidden_order_val);
				 	$("#cbo_order_id").val(hidden_order_id);
				 	// $("#cbo_actual_order_id").val(hidden_actual_order_id);
				 	load_drop_down( 'requires/buyer_inspection_controller', '__'+hidden_order_id, 'load_drop_down_week_no', 'week_drop_down_td' );
				 	load_drop_down( 'requires/buyer_inspection_controller', '__'+hidden_order_id, 'load_drop_down_country_id', 'country_drop_down_td' );
				 	get_php_form_data( hidden_order_id+','+document.getElementById('cbo_week_no').value+','+document.getElementById('cbo_country_id').value+','+document.getElementById('cbo_inspection_level').value, 'set_po_qnty_ship_date', 'requires/buyer_inspection_controller');
				 }
			}		
		}
	}

	function openActualPoPoup()
	{
		if (form_validation('cbo_order_val','PO No')==false)
		{
			return;   
		}
		else
		{	
			var txt_po_id=document.getElementById('cbo_order_id').value;
			 
			var page_link="requires/buyer_inspection_controller.php?action=open_actual_order_popup&txt_po_no="+txt_po_id;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Order Popup", 'width=600px,height=350px,center=1,resize=1,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				 var hidden_actual_order_no=this.contentDoc.getElementById("hidden_actual_order_no").value ;
				 var hidden_actual_order_id=this.contentDoc.getElementById("hidden_actual_order_id").value ;
				 
				 if(hidden_actual_order_id)
				 {	
				 	$("#actual_order_id").val(hidden_actual_order_no);
				 	$("#cbo_actual_order_id").val(hidden_actual_order_id);				 	 
				 }
			}		
		}
	}

	function openInspPoup()
	{	
		// alert('ok');return;		
		if (form_validation('txt_job_no*cbo_order_id','Job No*PO number')==false)
		{
			return;   
		}
		else
		{	
			var txt_job_no=document.getElementById('txt_job_no').value;
			var hidden_ins_data=$("#hidden_ins_data").val();
			var cbo_order_id=document.getElementById('cbo_order_id').value;
			var cbo_country_id=document.getElementById('cbo_country_id').value;
			var garments_nature=document.getElementById('garments_nature').value;
			var company_name=document.getElementById('cbo_company_name').value;
			var data = txt_job_no+"_____"+cbo_order_id+"_____"+cbo_country_id+"_____"+hidden_ins_data+"_____"+garments_nature+"_____"+company_name;
			// alert(data);
			var page_link="requires/buyer_inspection_controller.php?action=open_insp_qty_popup&data="+data;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Inspection Quantity Popup", 'width=600px,height=350px,center=1,resize=1,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				 var hidden_all_data=this.contentDoc.getElementById("hidden_all_data").value ;
				 var hidden_qnty_data=this.contentDoc.getElementById("hidden_qnty_data").value ;
				 $("#cbo_country_id").attr("disabled",false);
				 if(hidden_qnty_data)
				 {
				 	$("#cbo_country_id").attr("disabled",true);
				 	$("#txt_inspection_qnty").val(hidden_qnty_data);
				 	$("#hidden_ins_data").val(hidden_all_data);
				 }
			}		
		}
	}
	 
	function fnc_buyer_inspection_entry( operation )
	{
		if('<?php echo implode('*',$_SESSION['logic_erp']['mandatory_field'][470]);?>'){
			if (form_validation('<?php echo implode('*',$_SESSION['logic_erp']['mandatory_field'][470]);?>','<?php echo implode('*',$_SESSION['logic_erp']['field_message'][470]);?>')==false)
			{
				
				return;
			}
		}

		if (form_validation('txt_job_no*cbo_source*cbo_inspection_company*txt_inp_date*cbo_order_id*txt_inspection_qnty*cbo_inspection_status*cbo_inspection_by','Job No*Source*Insp. Company*Inspection Date*PO number*Inspection Qnty*Inspection Status*Inspection By')==false)
		{
			return;   
		}	
		else
		{
			
			
			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_inp_date').val(), current_date)==false)
			{
				alert("Inspection Date Can not Be Greater Than Current Date");
				return;
			}	
			var insfaction=document.getElementById('cbo_inspection_status').value;
			if(insfaction!=1)
			{
				if (form_validation('cbo_cause','Inspection Cause')==false)
				{
					return;
				}
			}
			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_job_no*cbo_inspection_company*txt_inp_date*cbo_order_id*cbo_actual_order_id*txt_inspection_qnty*hidden_ins_data*cbo_source*cbo_working_company*cbo_working_location*cbo_working_floor*cbo_inspection_status*txt_ins_reason*cbo_inspection_level*cbo_cause*txt_comments*txt_mst_id*cbo_inspection_by*cbo_week_no*cbo_country_id*cbo_company_name',"../../");
			freeze_window(operation);
			http.open("POST","requires/buyer_inspection_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_buyer_inspection_entry_reponse;
		}
	}
		 
	function fnc_buyer_inspection_entry_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=http.responseText.split('**');
			
			if(reponse[0]==25)
			{
				$("#txt_inspection_qnty").val("");
				show_msg('29');
			}
			else if(reponse[0]==35)
			{
				alert(reponse[1]);
				$("#txt_inspection_qnty").val("");
				$("#txt_inspection_qnty").focus();
				show_msg('29');
				release_freezing();return;
			}
			else
			{
				show_msg(trim(reponse[0]));
				set_button_status(0, permission, 'fnc_buyer_inspection_entry',1); 
				show_list_view(document.getElementById('txt_job_no').value,'show_active_listview','inspection_production_list_view','requires/buyer_inspection_controller','setFilterGrid(\'tbl_list_search\',-1)');
				reset_form('','','cbo_order_id*cbo_order_val*cbo_actual_order_id*actual_order_id*txt_po_quantity*hidden_ins_data*txt_pub_shipment_date*txt_finishing_qnty*txt_inspection_qnty*txt_cum_inspection_qnty*txt_ins_reason*cbo_inspection_status*cbo_cause*txt_comments*txt_mst_id*cbo_week_no*cbo_country_id','','');
			}
			release_freezing();
		}
	}
	
	function openmypage_image(job)
	{
		//alert(job);//var issue_num=$('#sysid_'+i).val();
		//var dev_id=$('#updateid_'+i).val();
		//alert(issue_num);
		var page_link='requires/buyer_inspection_controller.php?action=show_image&job='+job; 
		var title="Image View";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=0,scrolling=0','../../')
	}

	function fn_qnty_check()
	{
		var txt_user_lebel=$('#txt_user_lebel').val();
		var variable_is_controll=$('#variable_is_controll').val();
		
		if(variable_is_controll==1 && txt_user_lebel!=2)
		{
			if(($('#txt_inspection_qnty').val()*1)>($('#txt_finishing_qnty').val()*1))
			{
				alert("Inspection Not Over Finishing Quantity");
				$('#txt_inspection_qnty').val("");
				$('#txt_inspection_qnty').focus();
				return;
			}
		}
		
	}

</script>
</head>
<body onLoad="set_hotkey()">

    <div style="width:100%;" align="center">
        <div style="width:850px;" align="center">
             <? echo load_freeze_divs ("../../",$permission);  ?>
        </div>
         
        <fieldset style="width:1120px"> 
        <legend>Production Module</legend>
        <form name="inspectionentry_1" id="inspectionentry_1" action=""  autocomplete="off">
                <fieldset>
                    <table width="100%">
                         <tr>
                         	<td width="130" class="must_entry_caption" align="right">Job No</td>
                                <td width="170" >
									<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:200px" placeholder="Double Click to Search" onDblClick="openmypage_order('requires/buyer_inspection_controller.php?action=order_search_popup','Order Search')"/>
                                </td>
                                <td width="130" class="must_entry_caption" align="right">Company </td>
                                <td width="">
                                	<?
										echo create_drop_down( "cbo_company_name", 210, "select id,company_name from lib_company where is_deleted=0 and status_active=1 order by company_name","id,company_name", 1, "-- Select Company --", $selected,"", 1,0 );	
									?> 
										<input type="hidden" id="sewing_production_variable" />
										<input type="hidden" id="styleOrOrderWisw" />
                                    	<input type="hidden" id="variable_is_controll" />
                                		<input type="hidden" id="txt_user_lebel" value="<? echo $_SESSION['logic_erp']['user_level']; ?>" />                                  		
				                        <input type="hidden" name="hidden_variable_cntl" id="hidden_variable_cntl" value="0">
				                        <input type="hidden" name="hidden_preceding_process" id="hidden_preceding_process" value="0">
                                	</td>

                                <td width="130" class="must_entry_caption" align="right">Source </td>
                                <td width="">
                                	<?
                    					echo create_drop_down( "cbo_source", 210, $knitting_source,"", 1, "-- Select Source --", $selected, "load_drop_down( 'requires/buyer_inspection_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_working_company', 'working_company_td' );", 0, '1,3' );
                    				?> 
                                </td>
                          </tr>
                          <tr>  
                                <td width="130" align="right">Working Company</td>
                                <td width="170" id="working_company_td">
									<?
			                       		echo create_drop_down( "cbo_working_company", 210, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Working Company --", '', "load_drop_down( 'requires/buyer_inspection_controller', this.value, 'load_drop_down_working_location', 'working_location_td' );",0 ); 
								   ?> 
                                 </td>
                                <td width="130" align="right">Location</td>
                                <td width="170" id="working_location_td">
                                    <? echo create_drop_down( "cbo_working_location", 210, $blank_array,"", 1, "-- Select Working Location --", $selected, "",1,0 );?>
                                </td>  
                                 <td width="130" align="right">Floor</td>
                                 <td width="170" id="working_floor_td">
                                 	<? echo create_drop_down( "cbo_working_floor", 210, $blank_array,"", 1, "-- Select Working Floor --", $selected, "",1,0 );?>
                                 </td>
                          </tr>
                          <tr>  
                                <td width="130" align="right">Buyer Name</td>
                                <td width="170" id="buyer_td">
									<?
                                        echo create_drop_down( "cbo_buyer_name", 210, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,0 );	
                                    ?> 
                                 </td>
                                <td width="130" align="right">Style No</td>
                                <td width="170">
                                    <input name="txt_style_no"   id="txt_style_no" class="text_boxes" style="width:200px " disabled readonly />
                                </td>  
                                 <td width="130" align="right">Style Des</td>
                                 <td width="170">
                                 	<input type="text" name="txt_style_des" id="txt_style_des" class="text_boxes" style="width:200px" disabled readonly />
                                 </td>
                          </tr>
                          <tr>  
                                 <td width="130" align="right">Job Qnty</td>
                                 <td width="170">
                                 	<input name="txt_order_qty" id="txt_order_qty" class="text_boxes_numeric"  style="width:200px" disabled readonly  />
                                 </td>
                                 
                                 <td width="130" align="right">Order UOM </td> 
                                 <td width="170">
                                 <? 
                                 echo create_drop_down( "cbo_order_uom",60, $unit_of_measurement, "",0, "", 1, "change_caption_cost_dtls( this.value, 'change_caption_pcs' )",1,"1,58" );
                                 ?>
                                 <input type="button" id="set_button" class="image_uploader"  value="Item Details" onClick="open_set_popup(document.getElementById('cbo_order_uom').value)" />
                                 <input type="button" class="image_uploader"  value="Image" onClick="openmypage_image(document.getElementById('txt_job_no').value)" />
                                 <input type="hidden" id="set_breck_down" />     
                                 <input type="hidden" id="item_id" /> 
                                 <input type="hidden" id="tot_set_qnty" />    
                                 </td>
                                 <td width="130" class="must_entry_caption" align="right">Inspected By</td>
                                 <td width="170">
									 <? 
										echo create_drop_down( "cbo_inspection_by", 210, $inspected_by_arr,"", 1, "--- Select ---", $selected, "load_drop_down( 'requires/buyer_inspection_controller', this.value+','+document.getElementById('cbo_buyer_name').value+','+document.getElementById('cbo_company_name').value, 'load_drop_down_buyer_party_company', 'cutt_company_td' );" );     	 
                                     ?> 
                                 </td>
                          </tr>
                          <tr> 
                             
                                 <td width="130" class="must_entry_caption" align="right">Inp. Company</td>
                                 <td width="170" id="cutt_company_td">
									 <? //echo "select distinct a.id, a.supplier_name from lib_supplier a,lib_supplier_party_type b  where a.id=b.supplier_id and  b.party_type=41 and a.status_active=1 and a.is_deleted=0 order by a.supplier_name";
										echo create_drop_down( "cbo_inspection_company", 210, $blank_array,"", 1, "--- Select ---", $selected, "",1,0 );     	 
                                     ?> 
                                 </td>
                             
                             <!--<td width="130" class="must_entry_caption" align="right">Week No</td>
                             <td width="170">
                             	<?
									//echo create_drop_down( "cbo_week_no", 210, $yes_no,"", 1, "--- Select ---", $selected, "" );     	 
								?>
                             </td>
                             <td width="130" class="must_entry_caption" align="right">Country</td>
                             <td width="170">
                             	<?
									//echo create_drop_down( "cbo_country", 210, $yes_no,"", 1, "--- Select ---", $selected, "" );     	 
								?>
                             </td>-->
                             <td width="130" class="must_entry_caption" align="right">Inp. Date</td>
                             <td width="170">
                                <input type="text" value="<? echo date('d-m-Y');?>" name="txt_inp_date" id="txt_inp_date" class="datepicker" style="width:195px"   />
                             </td>

                          	<td width="130" class="must_entry_caption" align="right">Inspection Level</td>
                             <td width="170">
                                <? 
									echo create_drop_down( "cbo_inspection_level", 210, $inpLevelArray,"", 0, "--- Select ---", 3, "" );     	 
                                     ?> 
                             </td>
                          
                          </tr>
                    </table>
                    </fieldset>
                    <table><tr><td colspan="6" height="5"></td></tr></table>
                    <!-- ============================this is blank===================== -->
                    <table cellpadding="0" cellspacing="1" width="100%" class="rpt_table" rules="all">
                        <thead>
                            <th width="100" class="must_entry_caption">Order No</th>
                            <th width="100">Actual PO</th>
                            <th width="100">Week No</th>
                            <th width="100" >Country</th>
                            <th width="80" class="must_entry_caption">PO Qty</th>
                            <th width="90">Ship. Date </th>
                            <th width="80">Finishing Qty</th>
                            <th width="80">Inspec. Qty</th>
                            <th width="80">Cuml. Insp. Qty</th>
                            <th width="80" class="must_entry_caption">Inspec. Status</th>
                            <th width="100">Cause </th>
                            <th width="">Inspec.Reason </th>
                        </thead>
                        <tr>
                            <td id="order_drop_down_td"><?  //echo create_drop_down( "cbo_order_id",100, $blank_array, 0, "", $selected, "" ); ?>
                               <input name="cbo_order_val" placeholder="Browse" id="cbo_order_val"  class="text_boxes" type="text" readonly  style="width:80px"   ondblclick="openOrderPoup()" />
                               <input type="hidden" name="cbo_order_id" id="cbo_order_id" value="">

                            	
                            </td>
                            <td id="order_drop_down_td"><?  //echo create_drop_down( "cbo_order_id",100, $blank_array, 0, "", $selected, "" ); ?>
                               <input name="actual_order_id" placeholder="Browse" id="actual_order_id"  class="text_boxes" type="text" readonly  style="width:80px"   ondblclick="openActualPoPoup()" />
                               <input type="hidden" name="cbo_actual_order_id" id="cbo_actual_order_id" value="">

                            	
                            </td>
                            <td id="week_drop_down_td"><?  echo create_drop_down( "cbo_week_no",100, $blank_array, 0, "", $selected, "" ); ?></td>
                            <td id="country_drop_down_td"><?  echo create_drop_down( "cbo_country_id",100, $blank_array, 0, "", $selected, "" ); ?></td>
                            <td ><input name="txt_po_quantity" id="txt_po_quantity"  class="text_boxes_numeric" type="text"  style="width:80px"  disabled /></td>
                            <td ><input name="txt_pub_shipment_date" id="txt_pub_shipment_date" class="datepicker" type="text" value="" style="width:70px;" disabled  /></td>
                            <td ><input name="txt_finishing_qnty" id="txt_finishing_qnty"  class="text_boxes_numeric" type="text"  style="width:80px" readonly /></td>
                            <td >
                            <input type="hidden" name="hidden_ins_data" id="hidden_ins_data" value="">
                            <input name="txt_inspection_qnty" placeholder="Write or Browse" id="txt_inspection_qnty"  class="text_boxes_numeric" type="text" readonly  style="width:80px" onKeyUp="fn_qnty_check()" onDblClick="openInspPoup();" /></td>
                            <td ><input name="txt_cum_inspection_qnty" id="txt_cum_inspection_qnty"  class="text_boxes_numeric" type="text"  style="width:80px" readonly /></td>
                            <td><?  echo create_drop_down( "cbo_inspection_status",80, $inspection_status,"", 1, "-- Select --", $selected, "" ); //change_cause_validation( this.value ) ?></td>
                            <td><?  echo create_drop_down( "cbo_cause",100, $inspection_cause,"", 1, "-- Select --", $selected, "" ); ?></td>
                            <td ><input name="txt_ins_reason" id="txt_ins_reason"  class="text_boxes" type="text"  style="width:80px"  /></td>

                        </tr>
                        <tr>
                            <td align="right"><b> Comments</b></td>
                            <td colspan="12" ><input name="txt_comments" id="txt_comments"  class="text_boxes" type="text"  style="width:622px"/></td>
                        </tr>
                         <tr>
                            <td align="center" colspan="12" valign="middle" class="button_container">
                                <?
                                echo load_submit_buttons( $permission, "fnc_buyer_inspection_entry", 0,0 ,"reset_form('inspectionentry_1','inspection_production_list_view','','','')",1); 
                                ?>
                                <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly >	
                            </td>
                        </tr>
                </table>
                <div style="width:1120px; margin-top:5px;"  id="inspection_production_list_view" align="center"></div>
          </form>
        </fieldset>
    </div>
</body> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>  
</html>
<?

/*-------------------------------------------- Comments
Purpose			: 	This form will create  Inspection Entry
				
Functionality	:	
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor
Creation date 	: 	22-09-2022
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

$inpLevelArray = array(1=>'Pre-Final',2=>'Final');
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Cutting Info","../../", 1, 1, $unicode,'','');

?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	 
	function openmypage_order(page_link,title)
	{
			page_link=page_link+get_submitted_data_string('cbo_company_name*garments_nature','../../');
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1153px,height=470px,center=1,resize=1,scrolling=0','')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];;
				var job=this.contentDoc.getElementById("selected_job");
				if (job.value!="")
				{
					freeze_window(5);
					document.getElementById('txt_job_no').value=job.value;
					get_php_form_data( job.value, "populate_order_data_from_search_popup", "requires/inspection_for_actual_po_entry_controller" );
					//load_drop_down( 'requires/inspection_for_actual_po_entry_controller', job.value, 'load_drop_down_po_number', 'order_drop_down_td' );
					load_drop_down( 'requires/inspection_for_actual_po_entry_controller', job.value, 'load_drop_down_week_no', 'week_drop_down_td' );
					load_drop_down( 'requires/inspection_for_actual_po_entry_controller', job.value, 'load_drop_down_country_id', 'country_drop_down_td' );
					

			     
					show_list_view(job.value,'show_active_listview','inspection_production_list_view','requires/inspection_for_actual_po_entry_controller','setFilterGrid(\'tbl_list_search\',-1)');

			
				reset_form('','','cbo_actual_order_id*actual_order_id*txt_mst_id*po_list_view','','');
			
					
					release_freezing();
				}
			}
		
	}

	function open_set_popup(unit_id)
	{
				var txt_quotation_id=document.getElementById('txt_job_no').value;
				var set_breck_down=document.getElementById('set_breck_down').value;
				var tot_set_qnty=document.getElementById('tot_set_qnty').value;
				var page_link="requires/inspection_for_actual_po_entry_controller.php?txt_quotation_id="+trim(txt_quotation_id)+"&action=open_set_list_view&set_breck_down="+set_breck_down+"&tot_set_qnty="+tot_set_qnty;
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



	function openActualPoPoup()
	{
		
		if (form_validation('txt_job_no*cbo_inspection_level','Job No*Inspection Level')==false)
		{
			return;   
		}	
		else
		{
			var job_no=document.getElementById('txt_job_no').value;
			var inspection_level=document.getElementById('cbo_inspection_level').value;
			 
			var page_link="requires/inspection_for_actual_po_entry_controller.php?action=open_actual_order_popup&job_no="+job_no+"&inspection_level="+inspection_level;

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Order Popup", 'width=500px,height=350px,center=1,resize=1,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				 var hidden_actual_order_no=this.contentDoc.getElementById("txt_po_val").value ;
				 var hidden_actual_order_id=this.contentDoc.getElementById("txt_po_id").value ;
				 

				 if(hidden_actual_order_id)
				 {	
				 	$("#actual_order_id").val(hidden_actual_order_no);
				 	$("#cbo_actual_order_id").val(hidden_actual_order_id);				 	 
				 }

				 show_list_view(hidden_actual_order_no+'_'+hidden_actual_order_id+'_'+inspection_level,'actual_po_list_view','po_list_view','requires/inspection_for_actual_po_entry_controller','setFilterGrid(\'list_view\',-1)');
				 set_button_status(0, '<?=$_SESSION['page_permission'];?>', 'fnc_buyer_inspection_entry',1);

			}	
			
		}
		

	}


	 
	function fnc_buyer_inspection_entry( operation )
	{
		
		
		var data_all="";
		if (form_validation('txt_job_no*cbo_source*cbo_inspection_company*txt_inp_date*cbo_inspection_by*cbo_inspection_level*actual_order_id','Job No*Source*Insp. Company*Inspection Date*Inspection By*Inspection Level*PO number')==false)
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
			


		   var row_num=$('#table_list_view tbody tr').length;

			for (var i=1; i<=row_num; i++)
			{
				
				
				if (form_validation('cbo_insp_status_'+i+'*txt_insp_qnty_'+i,'Inspection Status*Inspection Qnty')==false)
				{
					alert("plz fillup Inspection Status and Inspection Qnty.");
						return;   
				}
				var finishing_qnty=$("#txt_finishing_qnty_"+i).val()*1;
				var insp_qnty=$("#txt_insp_qnty_"+i).val()*1;
				var bal_qnty=$("#txt_bal_qnty_"+i).val()*1;
				var prev_qnty=$("#txt_prev_qnty_"+i).val()*1;
				var cuml_insp_qnty=$("#txt_cuml_insp_qnty_"+i).val()*1;
				var total=insp_qnty+cuml_insp_qnty;
				
				// alert(`${finishing_qnty} == ${insp_qnty} == ${cuml_insp_qnty} == ${total}`);

				if(operation==1){

						if(prev_qnty < insp_qnty){
							var cur_bal = insp_qnty-prev_qnty;
						}

					if(bal_qnty < cur_bal){
						alert("Not allow Inspection Qnty more than Finishing Qty");
						return
					}
				}else if(operation==0){

					if(finishing_qnty < total){
						alert("Not allow Inspection Qnty more than Finishing Qty");
						return
					}
				}
				
			

				data_all+=get_submitted_data_string('actual_order_no_'+i+'*actual_order_id_'+i+'*txt_shipment_date_'+i+'*cbo_country_id_'+i+'*txt_po_quantity_'+i+'*txt_finishing_qnty_'+i+'*txt_cuml_insp_qnty_'+i+'*txt_insp_qnty_'+i+'*txt_minor_qnty_'+i+'*txt_major_qnty_'+i+'*txt_critical_qnty_'+i+'*txt_acceptable_qnty_'+i+'*cbo_insp_status_'+i+'*txt_comment_'+i+'*update_dtls_id_'+i,"../../",i);	
				
			}
		
	

			
			var data="action=save_update_delete&operation="+operation+data_all+'&row_num='+row_num+get_submitted_data_string('txt_job_no*cbo_inspection_company*txt_inp_date*cbo_actual_order_id*cbo_source*cbo_working_company*cbo_working_location*cbo_working_floor*cbo_inspection_level*txt_mst_id*cbo_inspection_by*cbo_company_name',"../../");
			
			freeze_window(operation);
			http.open("POST","requires/inspection_for_actual_po_entry_controller.php",true);
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
			
			if(reponse[0]==10)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}

			if(reponse[0]==25)
			{
				
				show_msg('29');
			}
			else if(reponse[0]==35)
			{
				alert(reponse[1]);
			
				show_msg('29');
				release_freezing();return;
			}
			else
			{
				show_msg(trim(reponse[0]));
				set_button_status(0, permission, 'fnc_buyer_inspection_entry',1); 
				show_list_view(document.getElementById('txt_job_no').value,'show_active_listview','inspection_production_list_view','requires/inspection_for_actual_po_entry_controller','setFilterGrid(\'tbl_list_search\',-1)');

				
				reset_form('','','cbo_actual_order_id*actual_order_id*txt_mst_id*po_list_view','','');

				for (var i=1; i<=trim(reponse[2]); i++)
				{
					reset_form('','','actual_order_no_'+i+'*txt_shipment_date_'+i+'*cbo_country_id_'+i+'*txt_po_quantity_'+i+'*txt_finishing_qnty_'+i+'*txt_cuml_insp_qnty_'+i+'*txt_insp_qnty_'+i+'*txt_minor_qnty_'+i+'*txt_major_qnty_'+i+'*txt_acceptable_qnty_'+i+'*cbo_insp_status_'+i+'*txt_comment_'+i+'*txt_bal_qnty_'+i+'*txt_prev_qnty_'+i,'','');
					$("#txt_insp_qnty_"+i).attr("placeholder","");
				}      
			}
			release_freezing();
		}
	}
	function openmypage_image(job)
	{
		//alert(job);//var issue_num=$('#sysid_'+i).val();
		//var dev_id=$('#updateid_'+i).val();
		//alert(issue_num);
		var page_link='requires/inspection_for_actual_po_entry_controller.php?action=show_image&job='+job; 
		var title="Image View";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=0,scrolling=0','../../')
	}

	

</script>
</head>
<body onLoad="set_hotkey()">

 <div style="width:100%;" align="center">
        <div style="width:1000px;" align="center">
             <? echo load_freeze_divs ("../../",$permission);  ?>
        </div>
         
        <fieldset style="width:1200px"> 
        <legend>Inspection Module</legend>
        <form name="inspectionentry_1" id="inspectionentry_1" action="" autocomplete="off">
                <fieldset>
                    <table width="1200">
                         <tr>
                         	<td  class="must_entry_caption" align="left">Job No</td>
                                <td  >
									<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:140px" placeholder="Double Click to Search" onDblClick="openmypage_order('requires/inspection_for_actual_po_entry_controller.php?action=order_search_popup','Order Search')"/>
                                </td>
                                <td  class="must_entry_caption" align="left">Company </td>
                                <td>
                                	<?
										echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company where is_deleted=0 and status_active=1 order by company_name","id,company_name", 1, "-- Select Company --", $selected,"", 1,0 );	
									?> 
										<input type="hidden" id="sewing_production_variable" />
										<input type="hidden" id="styleOrOrderWisw" />
                                    	<input type="hidden" id="variable_is_controll" />
                                		<input type="hidden" id="txt_user_lebel" value="<? echo $_SESSION['logic_erp']['user_level']; ?>" />                                  		
				                        <input type="hidden" name="hidden_variable_cntl" id="hidden_variable_cntl" value="0">
				                        <input type="hidden" name="hidden_preceding_process" id="hidden_preceding_process" value="0">
                                	</td>

                                <td  class="must_entry_caption" align="left">Source </td>
                                <td>
                                	<?
                    					echo create_drop_down( "cbo_source",210, $knitting_source,"", 0, "-- Select Source --", $selected, "load_drop_down( 'requires/inspection_for_actual_po_entry_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_working_company', 'working_company_td' );", 0, '1,3' );
                    				?> 
                                </td>
								<td  align="left">Working Company</td>
                                <td id="working_company_td">
									<?
			                       		echo create_drop_down( "cbo_working_company", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Working Company --", '', "load_drop_down( 'requires/inspection_for_actual_po_entry_controller', this.value, 'load_drop_down_working_location', 'working_location_td' );",0 ); 
								   ?> 
                                 </td>
                          </tr>
                          <tr>  
                                
                                <td  align="left">Location</td>
                                <td  id="working_location_td">
                                    <? echo create_drop_down( "cbo_working_location", 150, $blank_array,"", 1, "-- Select Working Location --", $selected, "",1,0 );?>
                                </td>  
                                 <td  align="left">Floor/Unit</td>
                                 <td  id="working_floor_td">
                                 	<? echo create_drop_down( "cbo_working_floor", 150, $blank_array,"", 1, "-- Select Working Floor --", $selected, "",1,0 );?>
                                 </td>
								 <td  align="left">Buyer Name</td>
                                <td  id="buyer_td">
									<?
                                        echo create_drop_down( "cbo_buyer_name",210, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,0 );	
                                    ?> 
                                 </td>
                                <td  align="left">Style No</td>
                                <td >
                                    <input name="txt_style_no"   id="txt_style_no" class="text_boxes" style="width:140px " disabled readonly />
                                </td>  
                          </tr>
                          <tr>  
                               
                                 <td  align="left">Style Des</td>
                                 <td >
                                 	<input type="text" name="txt_style_des" id="txt_style_des" class="text_boxes" style="width:140px" disabled readonly />
                                 </td>
								 <td  align="left">Job Qnty</td>
                                 <td >
                                 	<input name="txt_order_qty" id="txt_order_qty" class="text_boxes_numeric"  style="width:140px" disabled readonly  />
                                 </td>
                                 
                                 <td  align="left">Order UOM </td> 
                                 <td>
                                 <? 
                                 echo create_drop_down( "cbo_order_uom",60, $unit_of_measurement, "",0, "", 1, "change_caption_cost_dtls( this.value, 'change_caption_pcs' )",1,"1,58" );
                                 ?>
                                 <input type="button" id="set_button" class="image_uploader"  value="Item Details" style="width:70px" onClick="open_set_popup(document.getElementById('cbo_order_uom').value)" />
                                 <input type="button" class="image_uploader"  value="Image" style="width:70px" onClick="openmypage_image(document.getElementById('txt_job_no').value)" />
                                 <input type="hidden" id="set_breck_down" />     
                                 <input type="hidden" id="item_id" /> 
                                 <input type="hidden" id="tot_set_qnty" />    
                                 </td>

								 <td  class="must_entry_caption" align="left">Inspected By</td>
                                 <td>
									 <? 
										echo create_drop_down( "cbo_inspection_by", 150, $inspected_by_arr,"", 1, "--- Select ---", $selected, "load_drop_down( 'requires/inspection_for_actual_po_entry_controller', this.value+','+document.getElementById('cbo_buyer_name').value+','+document.getElementById('cbo_company_name').value, 'load_drop_down_buyer_party_company', 'cutt_company_td' );" );     	 
                                     ?> 
                                 </td>
                          </tr>
                          <tr>  
                                
								 <td  class="must_entry_caption" align="left">Inspection Company</td>
                                 <td  id="cutt_company_td">
									 <? //echo "select distinct a.id, a.supplier_name from lib_supplier a,lib_supplier_party_type b  where a.id=b.supplier_id and  b.party_type=41 and a.status_active=1 and a.is_deleted=0 order by a.supplier_name";
										echo create_drop_down( "cbo_inspection_company", 150, $blank_array,"", 1, "--- Select ---", $selected, "",1,0 );     	 
                                     ?> 
                                 </td>
                             
                             
                             <td  class="must_entry_caption" align="left">Inspection Date</td>
                             <td>
                                <input type="text" value="<? echo date('d-m-Y');?>" name="txt_inp_date" id="txt_inp_date" class="datepicker" style="width:140px"   />
                             </td>

                          	<td  class="must_entry_caption" align="left">Inspection Level</td>
                             <td >
                                <? 
									echo create_drop_down( "cbo_inspection_level", 210, $inpLevelArray,"", 1, "--- Select ---", 0, "" );     	 
                                     ?> 
                             </td>
                          
                          </tr>
                         
						  <tr> 
                             
						  <td class="must_entry_caption"  align="center" colspan="8"><b>Actual Po No</b>
							
                               <input name="actual_order_id" placeholder="Browse" id="actual_order_id"  class="text_boxes" type="text" readonly  style="width:120px;height:20px"   ondblclick="openActualPoPoup()" />
                               <input type="hidden" name="cbo_actual_order_id" id="cbo_actual_order_id" value="">
                            </td>
                                 
                          
                          </tr>
                    </table>
                    </fieldset>
                    <table><tr><td colspan="6" height="5"></td></tr></table>
                    <!-- ============================this is blank===================== -->
                   
				<div style="width:1200px; margin-top:5px;"  id="po_list_view" align="left">
					
				</div>
				<div style="width:1220px; margin-top:5px;"   align="center">
					<tr>
                            <td align="center" colspan="12" valign="middle" class="button_container">
                                <?
                                echo load_submit_buttons( $permission, "fnc_buyer_inspection_entry", 0,0 ,"reset_form('inspectionentry_1','inspection_production_list_view','','','')",1); 
                                ?>
                                <input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly >	
                            </td>
                        </tr>
				</div>
                <div style="width:1205px; margin-top:5px;"  id="inspection_production_list_view" align="left"></div>
          </form>
        </fieldset>
    </div>
</body> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>  
</html>
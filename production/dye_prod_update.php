<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Dyeing Production Entry

Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	19-05-2013
Updated by 		: 	Fuad Shahriar	
Update date		: 	25-05-2013	   
QC Performed BY	:		
QC Date			:	
Comments		:
*/ 

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Dyeing Production Entry Info","../", 1, 1, "",'1','');
?>

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name", "color_name" ), 0, -1); ?>];

	$(document).ready(function(e)
	 {
            $("#txt_color").autocomplete({
			 source: str_color
		  });
     });

	function set_production_besis()
	{
		var production_basis = $('#cbo_production_basis').val();
		$('#list_fabric_desc_container').html('');
		
		$('#buyer_id').val('');
		$('#buyer_name').val('');
		$('#txt_production_qty').val('');
		$('#all_po_id').val('');
		$('#save_data').val('');
		$('#batch_booking_without_order').val('');
		$('#txt_batch_qnty').val('');
		$('#txt_total_production').val('');
		$('#txt_yet_production').val('');
		
		$('#txt_production_qty').attr('readonly','readonly');
		$('#txt_production_qty').attr('onClick','openmypage_po();');	
		$('#txt_production_qty').attr('placeholder','Single Click to Search');
		
        if(production_basis == 4)
        {
			$('#cbo_body_part').val(0);
			$('#cbo_body_part').removeAttr('disabled','disabled');
			$('#txt_cons_comp').val('');
			$('#txt_cons_comp').removeAttr('disabled','disabled');
			$('#txt_color').val('');
			$('#txt_color').removeAttr('disabled','disabled');
			$("#txt_batch_no").val('');
			$("#txt_batch_id").val('');
			$("#txt_batch_no").removeAttr('readonly','readonly');
			$('#txt_batch_no').removeAttr("onDblClick");
			$('#txt_batch_no').removeAttr("placeholder");
			$('#txt_cons_comp').attr("onDblClick","openmypage_fabricDescription();");
			$('#txt_cons_comp').attr("placeholder","Double Click For Search");
			
        }
        else if(production_basis == 5)
        {
			$('#cbo_body_part').val(0);
			$('#cbo_body_part').attr('disabled','disabled');
			$('#txt_cons_comp').val('');
			$('#txt_cons_comp').attr('disabled','disabled');
			$('#txt_color').val('');
			$('#txt_color').attr('disabled','disabled');
			$("#txt_batch_no").val('');
			$("#txt_batch_id").val('');
			$("#txt_batch_no").attr('readonly','readonly');
			$('#txt_batch_no').attr("onDblClick","openmypage_batchnum();");
			$('#txt_batch_no').attr("placeholder","Double Click For Search");
			$('#txt_cons_comp').removeAttr("onDblClick");
			$('#txt_cons_comp').removeAttr("placeholder");
        }
	 }

	function openmypage_systemid()
	{
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var cbo_company_id = $('#cbo_company_id').val();
			var title = 'System ID Info';
			var page_link = 'requires/dye_prod_update_controller.php?cbo_company_id='+cbo_company_id+'&action=systemId_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var dye_prod_id=this.contentDoc.getElementById("hidden_sys_id").value;
				
				reset_form('dyingproductionentry_1','list_container_dyeing*list_fabric_desc_container','','','','roll_maintained');
				get_php_form_data(dye_prod_id, "populate_data_from_dye_update", "requires/dye_prod_update_controller" );
				show_list_view(dye_prod_id,'show_dye_prod_listview','list_container_dyeing','requires/dye_prod_update_controller','');
			}
		}
	}

	function openmypage_batchnum()
	{
		var cbo_company_id = $('#cbo_company_id').val();

		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var page_link='requires/dye_prod_update_controller.php?cbo_company_id='+cbo_company_id+'&action=batch_number_popup';
			var title='Batch Number Popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=820px,height=390px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;
				if(batch_id!="")
				{
					freeze_window(5);
					set_production_besis();
					//Batch Based Means Inhouse. Inhouse Id=1
					$('#cbo_dyeing_source').val(1);
					load_drop_down( 'requires/dye_prod_update_controller','1_'+document.getElementById('cbo_company_id').value, 'load_drop_down_dyeing_com','dyeingcom_td');
					
					get_php_form_data(batch_id, "populate_data_from_batch", "requires/dye_prod_update_controller" );
					show_list_view(batch_id,'show_fabric_desc_listview','list_fabric_desc_container','requires/dye_prod_update_controller','');
					release_freezing();
				}
			}
		}
	}
	
	function openmypage_fabricDescription()
	{
		var title = 'Fabric Description Info';	
		var page_link = 'requires/dye_prod_update_controller.php?action=fabricDescription_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=370px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theename=this.contentDoc.getElementById("hidden_desc_no").value; //Access form field with id="emailfield"
			var theegsm=this.contentDoc.getElementById("hidden_gsm").value; //Access form field with id="emailfield"
			var theeDiaWith=this.contentDoc.getElementById("hidden_dia_width").value; //Access form field with id="emailfield"
			
			$('#txt_cons_comp').val(theename);
			$('#txt_gsm').val(theegsm);
			$('#txt_dia_width').val(theeDiaWith);
		}
	}
	

	function openmypage_po()
	{
		var production_basis=$('#cbo_production_basis').val();
		var cbo_company_id = $('#cbo_company_id').val();
		var txt_batch_no = $('#txt_batch_no').val();
		var txt_batch_id = $('#txt_batch_id').val();
		var dtls_id = $('#update_dtls_id').val();
		var roll_maintained = $('#roll_maintained').val();
		var save_data = $('#save_data').val();
		var all_po_id = $('#all_po_id').val();
		var txt_production_qty = $('#txt_production_qty').val(); 
		var distribution_method = $('#distribution_method_id').val();
		
		if(production_basis==4 && cbo_company_id==0)
		{
			alert("Please Select Company.");
			$('#cbo_company_id').focus();
			return false;
		}
		else if(production_basis==5 && txt_batch_no=="")
		{
			alert("Please Select Batch No.");
			$('#txt_batch_no').focus();
			return false;
		}

		var title = 'PO Info';
		var page_link = 'requires/dye_prod_update_controller.php?production_basis='+production_basis+'&cbo_company_id='+cbo_company_id+'&txt_batch_id='+txt_batch_id+'&dtls_id='+dtls_id+'&all_po_id='+all_po_id+'&roll_maintained='+roll_maintained+'&save_data='+save_data+'&txt_production_qty='+txt_production_qty+'&prev_distribution_method='+distribution_method+'&action=po_popup';
		 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=410px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var save_string=this.contentDoc.getElementById("save_string").value;	 //Access form field with id="emailfield"
			var tot_dye_qnty=this.contentDoc.getElementById("tot_dye_qnty").value; //Access form field with id="emailfield"
			var all_po_id=this.contentDoc.getElementById("all_po_id").value; //Access form field with id="emailfield"
			var buyer_name=this.contentDoc.getElementById("buyer_name").value; //Access form field with id="emailfield"
			var buyer_id=this.contentDoc.getElementById("buyer_id").value; //Access form field with id="emailfield"
			var distribution_method=this.contentDoc.getElementById("distribution_method").value;
			
			$('#save_data').val(save_string);
			$('#txt_production_qty').val(tot_dye_qnty);
			$('#all_po_id').val(all_po_id);
			$('#buyer_name').val(buyer_name);
			$('#buyer_id').val(buyer_id);
			$('#distribution_method_id').val(distribution_method);
		}
	}
		
	function fnc_dye_production( operation )
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		if( form_validation('cbo_company_id*txt_production_date*cbo_dyeing_source*cbo_dyeing_company*txt_challan_no*txt_batch_no*cbo_body_part*txt_cons_comp*txt_production_qty','Company*Production Date*Dyeing Source*Dyeing Company*Challan No.*Body Part*Const. Comp.*Production Qnty')==false )
		{
			return;
		}	
		
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_production_basis*cbo_company_id*txt_system_id*txt_production_date*cbo_dyeing_source*cbo_dyeing_company*txt_challan_no*cbo_location*txt_remarks*txt_batch_no*txt_batch_id*cbo_body_part*txt_cons_comp*txt_color*txt_gsm*txt_dia_width*txt_production_qty*buyer_id*cbo_machine_name*txt_start_hours*txt_start_minuties*txt_start_date*txt_end_hours*txt_end_minutes*txt_end_date*update_id*update_dtls_id*save_data*all_po_id*roll_maintained*batch_booking_without_order',"../");
		//alert (data);return;
	  freeze_window(operation);
	  http.open("POST","requires/dye_prod_update_controller.php",true);
	  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	  http.send(data);
	  http.onreadystatechange = fnc_dye_production_reponse;
	}
	
	function fnc_dye_production_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);return;
			var reponse=trim(http.responseText).split('**');
			show_msg(reponse[0]);
			
			if((reponse[0]==0 || reponse[0]==1))
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_system_id').value = reponse[2];
				$('#cbo_company_id').attr('disabled','disabled');
				show_list_view(reponse[1],'show_dye_prod_listview','list_container_dyeing','requires/dye_prod_update_controller','');
			}

			var cbo_production_basis=$('#cbo_production_basis').val();
			if(cbo_production_basis==4)
			{
				reset_form('dyingproductionentry_1','','','','','update_id*txt_system_id*cbo_production_basis*cbo_company_id*txt_production_date*cbo_dyeing_source*cbo_dyeing_company*txt_challan_no*cbo_location*txt_remarks*roll_maintained');
			}
			else
			{
				reset_form('dyingproductionentry_1','','','','','update_id*txt_system_id*cbo_production_basis*cbo_company_id*txt_production_date*cbo_dyeing_source*cbo_dyeing_company*txt_challan_no*cbo_location*txt_remarks*roll_maintained*txt_batch_no*txt_batch_id*batch_booking_without_order*txt_color*cbo_machine_name*txt_start_hours*txt_start_minuties*txt_start_date*txt_end_hours*txt_end_minutes*txt_end_date');
			}
			
			set_button_status(reponse[3], permission, 'fnc_dye_production',1);	
			release_freezing();
		}
	}

	function fnc_move_cursor(val,id, field_id,lnth,max_val)
	{
		var str_length=val.length;
		if(str_length==lnth)
		{
			$('#'+field_id).select();
			$('#'+field_id).focus();
		}
		
		if(val>max_val)
		{
			document.getElementById(id).value=max_val;
		}
	}
	
	function set_form_data(data)
	{
		var data=data.split("**");
		$('#cbo_body_part').val(data[0]);
		$('#txt_cons_comp').val(data[1]);
		$('#txt_gsm').val(data[2]);
		$('#txt_dia_width').val(data[3]);
	}
	
	function put_data_dtls_part(id,type,page_path)
	{
		get_php_form_data(id+"**"+$('#roll_maintained').val(), type, page_path );
	}
	
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">

<? echo load_freeze_divs ("../",$permission); ?>
    <form name="dyingproductionentry_1" id="dyingproductionentry_1" autocomplete="off" >
    <div style="width:830px; float:left;" align="center">   
        <fieldset style="width:820px;">
        <legend>Dyeing Production Entry</legend>
        <fieldset>
            <table cellpadding="0" cellspacing="2" width="810" border="0">
                <tr>
                    <td colspan="3" align="right"><strong>System ID</strong><input type="hidden" name="update_id" id="update_id" /></td>
                    <td colspan="3" align="left">
                        <input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemid();" readonly />
                    </td>
                </tr>
                <tr>
                    <td colspan="6">&nbsp;</td>
                </tr>
                <tr>
                    <td>Production Basis</td>
                    <td>
						<?
							echo create_drop_down("cbo_production_basis", 150, $receive_basis_arr,"", 0,"", 5,"set_production_besis();","","4,5","","","");
                        ?>
                    </td>
                    <td class="must_entry_caption">Company Name</td>
                    <td>
						<?
							echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down('requires/dye_prod_update_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down('requires/dye_prod_update_controller', this.value, 'load_drop_machine', 'machine_td' );get_php_form_data(this.value,'roll_maintained','requires/dye_prod_update_controller' );" );
                        ?>
                    </td>
                    <td class="must_entry_caption">Production Date</td>
                    <td>
                        <input type="date" name="txt_production_date" id="txt_production_date" class="datepicker" style="width:150px;" readonly>
                    </td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Dyeing Source</td>
                    <td>
						<?
							echo create_drop_down("cbo_dyeing_source", 150, $knitting_source,"", 1,"-- Select Source --", 0,"load_drop_down( 'requires/dye_prod_update_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_dyeing_com','dyeingcom_td');","","","","","2");
                        ?>
                    </td>
                    <td class="must_entry_caption">Dyeing Company</td>
                    <td id="dyeingcom_td">
						<?
							echo create_drop_down("cbo_dyeing_company", 160, $blank_array,"", 1,"-- Select Dyeing Company --", 0,"");
                        ?>
                    </td>
                    <td class="must_entry_caption">Challan No.</td>
                    <td>
                        <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:150px;" maxlength="20" title="Maximum 20 Character" />
                    </td>
                </tr>
                <tr>
                    <td>Location</td>
                    <td id="location_td">
						<?
							echo create_drop_down("cbo_location", 150, $blank_array,"", 1,"-- Select Location --", 0,"");
                        ?>
                    </td>
                    <td>Remarks</td>
                    <td colspan="3">
                        <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:425px;" maxlength="400" title="Maximum 400 Character" />
                    </td>
                </tr>
            </table>
        </fieldset>
        <table cellpadding="0" cellspacing="1" width="810" border="0">
            <tr>
                <td width="60%" valign="top">
                    <fieldset>
                    <legend>New Entry</legend>
                    <table cellpadding="0" cellspacing="2" width="100%">
                        <tr> 
                            <td width="110" class="must_entry_caption">Batch No.</td>
                            <td>
                                <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:170px;" placeholder="Double click to search" maxlength="20" title="Maximum 20 Character" onDblClick="openmypage_batchnum();" readonly />
                                <input type="hidden" name="txt_batch_id" id="txt_batch_id" readonly />
                                <input type="hidden" name="batch_booking_without_order" id="batch_booking_without_order"/>
                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Body Part</td>
                            <td>
                                 <?
                                    echo create_drop_down( "cbo_body_part", 182, $body_part,"", 1, "-- Select Body Part --", 0, "",1 );
                                 ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Const. Comp.</td>
                            <td>
                                <input type="text" name="txt_cons_comp" id="txt_cons_comp" class="text_boxes" style="width:170px;" readonly/>
                            </td>
                        </tr>
                        <tr>
                            <td>Color</td>
                            <td>
                                <input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:170px;" maxlength="20" title="Maximum 20 Character" disabled />
                            </td>
                        </tr>
                        <tr>
                            <td>GSM</td>
                            <td>
                                <input type="text" name="txt_gsm" id="txt_gsm" class="text_boxes_numeric" style="width:170px;" maxlength="10" title="Maximum 10 Character" />
                            </td>
                        </tr>
                        <tr>
                            <td>Dia/Width</td>
                            <td>
                                <input type="text" name="txt_dia_width" id="txt_dia_width" class="text_boxes_numeric" style="width:170px;" maxlength="10" title="Maximum 10 Character" />
                            </td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Production Qnty</td>
                            <td>
                                <input type="text" name="txt_production_qty" id="txt_production_qty" class="text_boxes_numeric" placeholder="Single Click to Search" style="width:170px;" onClick="openmypage_po()" readonly />
                            </td>
                        </tr>
                        <tr>
                            <td>Buyer</td>
                            <td>
                                <input type="text" name="buyer_name" id="buyer_name" class="text_boxes" placeholder="Display" style="width:170px;" disabled="disabled" />
                                <input type="hidden" name="buyer_id" id="buyer_id" class="text_boxes" disabled="disabled" />
                            </td>
                        </tr>
                        <tr>
                            <td>Machine Name</td>
                            <td id="machine_td">
                            	<? echo create_drop_down( "cbo_machine_name", 182, $blank_array,"", 1, "-- Select Machine --", 0, "",0 ); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Start Time</td>
                            <td>
                                <input type="text" name="txt_start_hours" id="txt_start_hours" class="text_boxes_numeric" placeholder="Hours" style="width:30px;" onKeyUp="fnc_move_cursor(this.value,'txt_start_hours','txt_start_minuties',2,23);" />
                                
                                <input type="text" name="txt_start_minuties" id="txt_start_minuties" class="text_boxes_numeric" placeholder="Minutes" style="width:30px;" onKeyUp="fnc_move_cursor(this.value,'txt_start_minuties','txt_start_date',2,59)" />
                                
                                <input type="date" name="txt_start_date" id="txt_start_date" placeholder="Date" class="datepicker" style="width:78px;" readonly />
                            </td>
                        </tr>
                        <tr>
                            <td>End Time</td>
                            <td>
                                <input type="text" name="txt_end_hours" id="txt_end_hours" class="text_boxes_numeric" placeholder="Hours" style="width:30px;" onKeyUp="fnc_move_cursor(this.value,'txt_end_hours','txt_end_minutes',2,23)" />
                                
                                <input type="text" name="txt_end_minutes" id="txt_end_minutes" class="text_boxes_numeric" placeholder="Minutes" style="width:30px;" onKeyUp="fnc_move_cursor(this.value,'txt_end_minutes','txt_end_date',2,59)" />
                               
                                <input type="date" name="txt_end_date" id="txt_end_date" class="datepicker" placeholder="Date" style="width:78px;" readonly />
                            </td>
                        </tr>
                    </table>
                    </fieldset>
                </td>
                <td width="2%" valign="top">&nbsp;</td>
                <td width="38%" valign="top">
                    <fieldset>
                    <legend>Display</legend>
                        <table>
                            <tr>
                                <td width="110">Batch Quantity</td>
                                <td>
                                    <input type="text" name="txt_batch_qnty" id="txt_batch_qnty" class="text_boxes" style="width:150px;" disabled />
                                </td>
                            </tr>
                            <tr>
                                <td>Total Production</td>
                                <td>
                                    <input type="text" name="txt_total_production" id="txt_total_production" class="text_boxes" style="width:150px;" disabled />
                                </td>
                            </tr>
                            <tr>
                                <td>Yet to Production</td>
                                <td>
                                    <input type="text" name="txt_yet_production" id="txt_yet_production" class="text_boxes" style="width:150px;" disabled />
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td align="center" colspan="4" class="button_container">
                    <?
                        echo load_submit_buttons($permission, "fnc_dye_production", 0,0,"reset_form('dyingproductionentry_1','list_container_dyeing*list_fabric_desc_container','','cbo_production_basis,5','disable_enable_fields(\'cbo_company_id\');set_production_besis();')",1);
                    ?>
                     <input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
                     <input type="hidden" name="all_po_id" id="all_po_id" readonly>
                     <input type="hidden" name="save_data" id="save_data" readonly>
                     <input type="hidden" name="roll_maintained" id="roll_maintained" readonly>
                     <input type="hidden" name="distribution_method_id" id="distribution_method_id" readonly />
                </td>
            </tr>
        </table>
        <div style="width:820px;" id="list_container_dyeing"></div>
		</fieldset>
        </div>
        <div style="width:10px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
        <div id="list_fabric_desc_container" style="max-height:500px; width:340px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
	</form>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
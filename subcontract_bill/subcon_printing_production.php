<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create subcontract printing production
Functionality	:	
JS Functions	:
Created by		:	Hakim
Creation date 	: 	23-07-2013
Updated by 		: 		
Update date		: 
Oracle Convert 	:	Kausar		
Convert date	: 	24-05-2014	   
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
echo load_html_head_contents("Dyeing Production Entry", "../",1, 1,$unicode,1,'');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';

	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name ", "color_name" ), 0, -1); ?> ];
	function set_auto_complete(type)
	{
		if(type=='color_return')
		{
			$("#txt_color").autocomplete({
			source: str_color
			});
		}
	}

	function openmypage_batchno()
	{ 
		if ( form_validation('cbo_company_id*cbo_party_name','Company Name*Party Name')==false )
		{
			return;
		}
		else
		{
			var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_process').value;
			emailwindow=dhtmlmodal.open('EmailBox','iframe', 'requires/subcon_printing_production_controller.php?action=batch_no_popup&data='+data,'Batch no Popup', 'width=750px, height=400px, center=1, resize=0, scrolling=0','')
			emailwindow.onclose=function()
			{
				if(document.getElementById('txt_batch_no').value!="")
				{
					confirm("Are you sure you want to delete Existing Data?");
					document.getElementById('update_check').value="1";
				}
				var theemail=this.contentDoc.getElementById("selected_batch_id").value;
				//alert (theemail);
				if (theemail!="")
				{
					freeze_window(5);
					get_php_form_data( theemail, "load_php_data_to_form_batch", "requires/subcon_printing_production_controller" );
					show_list_view(document.getElementById('order_no_id').value+"_"+document.getElementById('process_id').value,'show_fabric_desc_listview','list_fabric_desc_container','requires/subcon_printing_production_controller','');

					document.getElementById('txt_receive_qnty').value="";
					document.getElementById('txt_product_qnty').value="";
					release_freezing();
				}
			}
		}
	}

	function openmypage_order_numbers()
	{ 
		if ( form_validation('cbo_company_id*cbo_party_name*txt_batch_no','Company Name*Party Name*batch no')==false )
		{
			return;
		}
		else
		{
			var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_process').value;
			emailwindow=dhtmlmodal.open('EmailBox','iframe','requires/subcon_printing_production_controller.php?action=order_numbers_popup&data='+data,'Order Numbers Popup', 'width=750px, height=400px, center=1, resize=0, scrolling=0','')
			emailwindow.onclose=function()
			{
				if(document.getElementById('order_no_id').value!="")
				{
					var r=confirm("Are you sure you want to delete Existing Data?");
					if(r==true)
					{
						document.getElementById('update_check').value="1";
						var theemail=this.contentDoc.getElementById("selected_order_id");
						var theemail_name=this.contentDoc.getElementById("selected_order_name");
						if (theemail.value!="")
						{
							freeze_window(5);
							document.getElementById('order_no_id').value=theemail.value;
							document.getElementById('txt_order_numbers').value=theemail_name.value;
							document.getElementById('txt_receive_qnty').value="";
							document.getElementById('txt_product_qnty').value="";
							release_freezing();
						}
					}
				}
				else
				{
					var theemail=this.contentDoc.getElementById("selected_order_id");
					var theemail_name=this.contentDoc.getElementById("selected_order_name");
					//alert (theemail.value+theemail_name.value);return; 
					if (theemail.value!="")
					{
						freeze_window(5);
						document.getElementById('order_no_id').value=theemail.value;
						document.getElementById('txt_order_numbers').value=theemail_name.value;
						document.getElementById('txt_receive_qnty').value="";
						document.getElementById('txt_product_qnty').value="";
						release_freezing();
					}	
				}				
			}
		}
	}

	function openmypage_composition()
	{
		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('hidden_color_id').value;
		var page_link = 'requires/subcon_printing_production_controller.php?action=cons_comp_popup&data='+data;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link,'composition Info', 'width=820px,height=400px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("cons_comp_id").value;
			//alert (theemail);
			if (theemail!="")
			{
				freeze_window(5);
				get_php_form_data( theemail+"_"+document.getElementById('hidden_color_id').value, "load_php_data_to_form_cons_comp", "requires/subcon_printing_production_controller" );
				release_freezing();
			}
		}
	}

	function openmypage_qnty()
	{
		if ( form_validation('txt_order_numbers','Order Numbers')==false )
		{
			return;
		}
		else 
		{		
			var data=document.getElementById('order_no_id').value+"_"+document.getElementById('update_id_dtl').value+"_"+document.getElementById('update_check').value+"_"+document.getElementById('txt_receive_qnty').value;
			//var data = $('#order_no_id').val();
			var title = 'Order Qnty Info';	
			var page_link = 'requires/subcon_printing_production_controller.php?action=order_qnty_popup&data='+data;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=370px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var receive_qnty_tot=this.contentDoc.getElementById("hidden_qnty_tot"); 
				var receive_qnty=this.contentDoc.getElementById("hidden_qnty"); 
				var receive_tbl_id=this.contentDoc.getElementById("hidd_qnty_tbl_id"); 
				//alert (receive_tbl_id.value);return;
				$('#txt_product_qnty').val(receive_qnty_tot.value);
				$('#txt_receive_qnty').val(receive_qnty.value);
				$('#update_id_qnty').val(receive_tbl_id.value);
				if(document.getElementById('update_check').value==1)
				{
					document.getElementById('update_id_qnty').value="";
				}
			}
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

	function subcon_printing_production(operation)
	{ 
		if ( form_validation('cbo_company_id*cbo_location_name*cbo_party_name*txt_production_date*txt_batch_no*txt_order_numbers*cbo_process*txt_composition*txt_product_qnty','Company Name*location name*Party Name*production date*batch no*order numbers*process*composition*product quantity')==false )
		{
			return;
		}
		else
		{
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_production_id*cbo_receive_basis*cbo_company_id*cbo_location_name*cbo_party_name*txt_production_date*txt_chal_no*txt_remarks*txt_batch_no*txt_batch_ext_no*txt_order_numbers*order_no_id*cbo_process*txt_composition*hidd_comp_id*txt_color*txt_gsm*txt_dia_width*txt_product_qnty*txt_receive_qnty*cbo_floor_id*cbo_machine_name*txt_start_hour*txt_start_minutes*txt_start_date*txt_end_hour*txt_end_minutes*txt_end_date*update_id_dtl*update_id*update_id_qnty',"../");
			//alert (data);return;
			freeze_window(operation);
			http.open("POST","requires/subcon_printing_production_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = subcon_printing_production_reponse;
		}
	}

	function subcon_printing_production_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert (http.responseText);//return;
			var reponse=trim(http.responseText).split('**');
			show_msg(reponse[0]);
			document.getElementById('update_id').value = reponse[1];
			document.getElementById('update_id_dtl').value = reponse[2];
			document.getElementById('txt_production_id').value = reponse[3];
			show_list_view(reponse[1],'printing_production_list_view','printing_production_list_view','requires/subcon_printing_production_controller','setFilterGrid("list_view",-1)');
			reset_form('','','txt_batch_no*txt_batch_ext_no*txt_order_numbers*order_no_id*txt_composition*txt_color*txt_gsm*txt_dia_width*txt_product_qnty*txt_receive_qnty*cbo_floor_id*cbo_machine_name*txt_start_hour*txt_start_minutes*txt_start_date*txt_end_hour*txt_end_minutes*txt_end_date*update_id_dtl');
			// document.getElementById('txt_batch_no').disabled=true;
			$('#txt_composition').focus();
			$('#list_fabric_desc_container').html('');
			set_button_status(0, permission, 'subcon_printing_production',1);
			release_freezing();	
		}
	}

	function openmypage_production_id()
	{ 
		var data=$('#cbo_company_id').val()+"_"+$('#cbo_party_name').val();
		emailwindow=dhtmlmodal.open('EmailBox','iframe', 'requires/subcon_printing_production_controller.php?action=production_id_popup&data='+data,'Dyeing Production Popup', 'width=750px, height=400px, center=1, resize=0, scrolling=0','')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("product_id");
			if (theemail.value!="")
			{
				freeze_window(5);
				get_php_form_data(theemail.value, "load_php_data_to_form_mst", "requires/subcon_printing_production_controller" );
				show_list_view(theemail.value,'printing_production_list_view','printing_production_list_view','requires/subcon_printing_production_controller','setFilterGrid("list_view",-1)');
				document.getElementById('cbo_receive_basis').disabled=true;
				document.getElementById('cbo_company_id').disabled=true;
				document.getElementById('cbo_party_name').disabled=true;
				$('#txt_batch_no').focus();
				reset_form('','','txt_batch_no*txt_batch_ext_no*txt_order_numbers*order_no_id*cbo_process*txt_composition*txt_color*txt_gsm*txt_dia_width*txt_product_qnty*cbo_floor_id*cbo_machine_name*txt_start_hour*txt_start_minutes*txt_start_date*txt_end_hour*txt_end_minutes*txt_end_date');
				release_freezing();
			}
		}
	}
	
	function set_form_data(data)
	{
		var data=data.split("**");
		$('#hidd_comp_id').val(data[0]);
		$('#txt_composition').val(data[1]);
		$('#txt_gsm').val(data[2]);
		openmypage_qnty();
	}

	function check_hour(str,field_id)
	{
		if(str>23)
		{
			alert("Allowed up to 23")
			document.getElementById(field_id).value='';
		}
	}

	function check_minutes(str,field_id)
	{
		if(str>59)
		{
			alert("Allowed up to 59")
			document.getElementById(field_id).value='';
		}
	}

</script>
<body onLoad="set_hotkey();set_auto_complete('color_return');">
<div style="width:100%;">   
	<? echo load_freeze_divs ("../",$permission);  ?>
    <fieldset style="width:800px ">
    <legend>Printing Production</legend>
        <form name="printingproduction_1" id="printingproduction_1">
        <table cellpadding="0" cellspacing="1" width="100%">
            <tr>
                <td colspan="3">
                    <fieldset>
                        <table cellpadding="0" cellspacing="2" width="100%">
                            <tr>
                                <td align="right" colspan="3"><strong>Production ID </strong></td>
                                <td width="140" align="justify">
                                    <input type="hidden" name="update_id" id="update_id" />
                                    <input type="text" name="txt_production_id" id="txt_production_id" class="text_boxes" style="width:140px" placeholder="Double Click to Search" onDblClick="openmypage_production_id();" readonly tabindex="1" >
                                </td>
                            </tr>
                            <tr>
                                <td width="120">Receive Basis</td>                                              
                                <td width="140">
									<? 
										echo create_drop_down( "cbo_receive_basis", 140, $receive_basis_arr,"", "","", 5,"","",5,"","","",2);
                                    ?>
                                </td>
                                <td width="120" class="must_entry_caption">Company Name</td>
                                <td width="140">
									<?php 
										echo create_drop_down( "cbo_company_id",140,"select id,company_name from lib_company comp where is_deleted=0 and status_active=1 and comp.core_business not in(3) $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/subcon_printing_production_controller', this.value, 'load_drop_down_location', 'location_td');load_drop_down( 'requires/subcon_printing_production_controller', this.value, 'load_drop_down_floor', 'floor_td' ); load_drop_down( 'requires/subcon_printing_production_controller', this.value, 'load_drop_down_party_name', 'party_td' );load_drop_down( 'requires/subcon_printing_production_controller', this.value, 'load_drop_machine', 'machine_td' );","","","","","",3);	
                                    ?>
                                </td>
                                <td width="100" class="must_entry_caption">Location </td>                                              
                                <td width="140" id="location_td">
									<? 
										echo create_drop_down( "cbo_location_name", 140, $blank_array,"", 1, "--Select Location--", $selected,"","","","","","",4);
                                    ?>
                                </td>
                            </tr> 
                            <tr>
                                <td class="must_entry_caption">Party Name</td>
                                <td id="party_td">
									<?
										echo create_drop_down( "cbo_party_name", 140, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"","","","",5);
                                    ?> 
                                </td>
                                <td class="must_entry_caption">Production Date</td>
                                <td>
                                    <input class="datepicker" type="text" style="width:130px" name="txt_production_date" id="txt_production_date"  tabindex="6"/>
                                </td>
                                <td>Challan No </td>
                                <td>
                                    <input type="text" name="txt_chal_no" id="txt_chal_no" class="text_boxes" style="width:130px"  tabindex="7">
                                </td>
                            </tr>
                            <tr>
                                <td >Remarks </td>                                              
                                <td colspan="3"> 
                                    <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:397px" maxlength="150" title="Maximum 150 Character" tabindex="8" />
                                </td> 
                            </tr>                                      
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr align="center">
                <td width="85%" valign="top" style="margin-left:10px;">
                    <fieldset style="width:700px">
                    <legend>New Entry</legend>
                        <table  cellpadding="0" cellspacing="2" width="100%" align="center">
                            <tr>
                                <td  style="width:120px ;" class="must_entry_caption">Process</td>
                                <td>
									<? 
										echo create_drop_down("cbo_process", 140, $conversion_cost_head_array,"", 0,"",35,"", "","35","","","",9);								 
                                    ?>
                                </td>
                                <td style="width:120px ;">Dia/Width</td>
                                <td>
                                    <input type="text" name="txt_dia_width" id="txt_dia_width" class="text_boxes" style="width:130px;" tabindex="15" />	
                                </td> 
                            </tr>
                            <tr>
                                <td class="must_entry_caption">Batch No</td>
                                <td id="batch_no">
                                    <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:60px" placeholder="Browse" onDblClick="openmypage_batchno();" readonly  tabindex="10"/>
                                    <input type="text" name="txt_batch_ext_no" id="txt_batch_ext_no" class="text_boxes" style="width:55px" placeholder="Ext." readonly />
                                </td>
                                <td class="must_entry_caption">Product Qty</td>
                                <td>
                                    <input type="hidden" name="txt_receive_qnty" id="txt_receive_qnty" />
                                    <input type="text" name="txt_product_qnty" id="txt_product_qnty" class="text_boxes_numeric" style="width:130px;" onClick="openmypage_qnty()" placeholder="Single Click" readonly tabindex="16" />	
                                </td>
                            </tr>
                            <tr>
                                <td class="must_entry_caption">Order Number </td>
                                <td id="order_numbers">
                                    <input type="text" name="txt_order_numbers" id="txt_order_numbers" class="text_boxes" style="width:130px" readonly tabindex="11"/>
                                </td>
                                <td>Prod. Floor</td>
                                <td id="floor_td">
                                    <? echo create_drop_down( "cbo_floor_id", 140, $blank_array,"", 1, "-- Select Floor --", 0, "",0 ); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="must_entry_caption">Const. and Compo.</td>
                                <td> <input type="hidden" name="hidd_comp_id" id="hidd_comp_id" />
                                    <input type="text" name="txt_composition" id="txt_composition" class="text_boxes" style="width:130px;" tabindex="12" readonly />	
                                </td>
                                <td>Machine Name</td>
                                <td id="machine_td">
                                    <? echo create_drop_down( "cbo_machine_name", 140, $blank_array,"", 1, "-- Select Machine --", 0, "",0 ); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Color</td>
                                <td>
                                    <input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:130px;"  tabindex="13"/>
                                    <input type="hidden" name="hidden_color_id" id="hidden_color_id">
                                </td>
                                <td>Start Time</td>
                                <td>
                                    <input type="text" name="txt_start_hour" id="txt_start_hour" class="text_boxes_numeric" style="width:25px" placeholder="Hour" onKeyUp="fnc_move_cursor(this.value,'txt_start_hour','txt_start_minutes',2,23);" tabindex="18"/>	
                                    <input type="text" name="txt_start_minutes" id="txt_start_minutes" class="text_boxes_numeric"  style="width:25px" placeholder="Minutes" onKeyUp="fnc_move_cursor(this.value,'txt_start_minutes','txt_start_date',2,59)" tabindex="19" />
                                    <input type="text" name="txt_start_date" id="txt_start_date" class="datepicker" style="width:60px"  placeholder="Start Date" readonly  tabindex="20"/>
                                </td>
                            </tr>
                            <tr>
                                <td>GSM</td>
                                <td>
                                    <input type="text" name="txt_gsm" id="txt_gsm" class="text_boxes_numeric" style="width:130px ;text-align:right" tabindex="14" />
                                </td>
                                <td>End Time</td>
                                <td>
                                    <input type="text" name="txt_end_hour" id="txt_end_hour" class="text_boxes_numeric" style="width:25px"  placeholder="Hour" onKeyUp="fnc_move_cursor(this.value,'txt_end_hour','txt_end_minutes',2,23)" tabindex="21"/>
                                    <input type="text" name="txt_end_minutes" id="txt_end_minutes" class="text_boxes_numeric" style="width:25px"  placeholder="Minutes" onKeyUp="fnc_move_cursor(this.value,'txt_end_minutes','txt_end_date',2,59)"  tabindex="22"/>		
                                    <input type="text" name="txt_end_date" id="txt_end_date" class="datepicker" style="width:60px"  placeholder="End Date" readonly  tabindex="23"/>
                                     
                                     <input type="hidden" name="update_id_dtl" id="update_id_dtl" />
                                     <input type="hidden" name="order_no_id" id="order_no_id" />
                                     <input type="hidden" name="update_id_qnty" id="update_id_qnty" />
                                     <input type="hidden" name="update_check" id="update_check" />
                                     <input type="hidden" name="process_id" id="process_id" />
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td colspan="6" align="center" class="button_container">
					<? 
						echo load_submit_buttons($permission, "subcon_printing_production", 0,0,"reset_form('printingproduction_1','printing_production_list_view','','','disable_enable_fields(\'cbo_receive_basis*cbo_company_id*cbo_party_name\',0)')",1);
                    ?> 
                </td>	  
            </tr>
        </table> 
        </form>
    </fieldset>
      <div style="width:800px; margin-top:10px" id="printing_production_list_view" align="center"></div>
      <div id="list_fabric_desc_container" style="max-height:500px; width:350px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
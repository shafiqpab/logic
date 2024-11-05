<?
/*--------------------------- Comments -------------------------------
Purpose			: 	This form will create Sub-contract Cutting Bill Issue
Functionality	:	
JS Functions	:
Created by		:	Hakim 
Creation date 	: 	02-07-2013
Updated by 		: 		
Update date		: 
Oracle Convert 	:	Kausar		
Convert date	: 	31-05-2014	   
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
echo load_html_head_contents("Cutting bill issue", "../", 1,1, $unicode,1,'');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';

	function openmypage_bill()
	{ 
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_name').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/subcon_cutting_bill_issue_controller.php?data='+data+'&action=bill_no_popup','Bill Popup', 'width=700px,height=400px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{			
			var theemail=this.contentDoc.getElementById("issue_id") //Access form field with id="emailfield"
			if (theemail.value!="")
			{
				freeze_window(5);
				//$('#issue_id_all').val('');
				//$('#selected_order_id').val('');			
				get_php_form_data( theemail.value, "load_php_data_to_form_issue", "requires/subcon_cutting_bill_issue_controller" );				
				window_close( theemail.value);					
				//show_list_view(document.getElementById('cbo_party_source').value+'***'+document.getElementById('cbo_party_name').value+'***'+document.getElementById('update_id').value+'***'+document.getElementById('issue_id_all').value,'cutting_delivery_list_view','cutting_info_list','requires/subcon_cutting_bill_issue_controller','set_all();');
				accounting_integration_check($('#hidden_acc_integ').val(),$('#hidden_integ_unlock').val());
				fnc_list_search(theemail.value);
				set_button_status(1, permission, 'fnc_cutting_bill_issue',1);
				set_all_onclick();
				release_freezing();
			}			
		}
	}

	function set_all()
	{
		selected_id = new Array();
		var old=document.getElementById('issue_id_all').value;
		var party_source=document.getElementById('cbo_party_source').value;
		if(party_source==2)
		{
			if(old!="")
			{   
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{   
					js_set_value( old[i]+"***"+document.getElementById('currid'+old[i]).value);
				}
			}
		}
		else if(party_source==1)
		{
			if(old!="")
			{   
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{   
					js_set_value( old[i]+"***"+'1');
				}
			}
		}
	}

	function window_close( frm )
	{	
		if ( !frm ) var frm='';
		
		if ($('#update_id').val()!=frm)
			var issue_id=document.getElementById('issue_id_all').value;
		else
			var issue_id='';
		
		//var data=document.getElementById('selected_order_id').value+"_"+issue_id+"_"+frm;
		var data=document.getElementById('selected_order_id').value+"_"+issue_id+"_"+frm+"_"+document.getElementById('cbo_party_source').value;
		var list_view_orders = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/subcon_cutting_bill_issue_controller');
		
		if(list_view_orders!='')
		{
			$("#bill_issue_table tr").remove();
			$("#bill_issue_table").append(list_view_orders);
		}
		var tot_row=$('#bill_issue_table tr').length;
		math_operation( "total_qnty", "deliveryqnty_", "+", tot_row );
		math_operation( "total_amount", "amount_", "+", tot_row );
		set_all_onclick();
	}

	function fnc_cutting_bill_issue( operation )
	{
		
		
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_bill_no').val()+'*'+report_title, "cutting_bill_print", "requires/subcon_cutting_bill_issue_controller") 
			//return;
			show_msg("3");
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if(operation==2)
			{
				if($('#hidden_acc_integ').val()==1)
				{
					show_msg('13');
					return;
				}
			}
		
			if ( form_validation('cbo_company_id*txt_bill_date*cbo_party_name*cbo_party_source*challenno_1','Company Name*Bill Date*Party Name*Party Source*Challan')==false )
			{
				return;
			}
			else
			{
				var tot_row=$('#bill_issue_table tr').length;
				var data1="action=save_update_delete&operation="+operation+"&tot_row="+tot_row+get_submitted_data_string('txt_bill_no*cbo_company_id*cbo_location_name*txt_bill_date*cbo_party_name*cbo_party_source*cbo_bill_for*update_id',"../");
				var data2='';
				for(var i=1; i<=tot_row; i++)
				{
					if(trim($("#amount_"+i).val())!="")
					{
						data2+=get_submitted_data_string('deleverydate_'+i+'*challenno_'+i+'*ordernoid_'+i+'*stylename_'+i+'*buyername_'+i+'*itemid_'+i+'*numberroll_'+i+'*deliveryqnty_'+i+'*txtrate_'+i+'*amount_'+i+'*remarks_'+i+'*curanci_'+i+'*deliveryid_'+i+'*updateiddtls_'+i+'*cbo_curanci_'+i+'*delete_id',"../");
					}
				}
				var data=data1+data2;
				//alert (data); return;
				freeze_window(operation);
				http.open("POST","requires/subcon_cutting_bill_issue_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_cutting_save_update_delete_response;
			}
		}
	}
	
	function fnc_cutting_save_update_delete_response()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
			if(response[0]*1==14*1)
			{
				release_freezing();
				alert(response[1]);
				return;
			}
			else if(response[0]==0 || response[0]==1)
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_bill_no').value = response[2];
				window_close(response[1]);
				set_button_status(1, permission, 'fnc_cutting_bill_issue',1);
			}
			release_freezing();
		}
	}

	var selected_id = new Array(); var selected_currency_id = new Array();
	function check_all_data()
	{
		var tbl_row_count = document.getElementById( 'list_view_issue' ).rows.length;
		tbl_row_count = tbl_row_count - 1;
		
		for( var i = 1; i <= tbl_row_count; i++ ) {
			eval($('#tr_'+i).attr("onclick"));  
		}
	}
	
	function toggle( x, origColor )
	{
		var newColor = 'yellow';
		if ( x.style ) {
		x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}
	
	function js_set_value(val)
	{
		//alert (val);
		var str=val.split("***");
		if( jQuery.inArray( str[1], selected_currency_id ) != -1  || selected_currency_id.length<1 )
		{
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			if( jQuery.inArray(  str[0] , selected_id ) == -1) {
				
				selected_id.push( str[0] );
				selected_currency_id.push( str[1] );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str[0]  ) break;
			}
				selected_id.splice( i, 1 );
				selected_currency_id.splice( i, 1 );
			}
			var id = ''; var currency = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				currency += selected_currency_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			currency = currency.substr( 0, currency.length - 1 );
			//alert (id);
			$('#selected_order_id').val( id );
			$('#selected_currency_no').val( currency );
		}
		else
		{
			$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
			 { 
				$(this).html('Currency Mix Not Allowed').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
			 });
		}
	}
	
	function accounting_integration_check(val,unlock)
	{
		var tot_row=$('#bill_issue_table tr').length;
		//alert (val);*****
		if(val==1 && unlock==0)
		{
			$('#cbo_company_id').attr('disabled','disabled');
			$('#cbo_location_name').attr('disabled','disabled');
			$('#txt_bill_date').attr('disabled','disabled');
			$('#cbo_party_source').attr('disabled','disabled');
			$('#cbo_party_name').attr('disabled','disabled');
			$('#cbo_bill_for').attr('disabled','disabled');
			for(var i=1; i<=tot_row; i++)
			{
				$('#numberroll_'+i).attr('disabled','disabled');
				$('#deliveryqnty_'+i).attr('disabled','disabled');
				$('#txtrate_'+i).attr('disabled','disabled');
			}
		}
		else
		{
			$('#cbo_company_id').removeAttr('disabled','disabled');
			$('#cbo_location_name').removeAttr('disabled','disabled');
			$('#txt_bill_date').removeAttr('disabled','disabled');
			$('#cbo_party_source').removeAttr('disabled','disabled');
			$('#cbo_party_name').removeAttr('disabled','disabled');
			$('#cbo_bill_for').removeAttr('disabled','disabled');
			for(var i=1; i<=tot_row; i++)
			{
				$('#numberroll_'+i).removeAttr('disabled','disabled');
				$('#deliveryqnty_'+i).removeAttr('disabled','disabled');
				$('#txtrate_'+i).removeAttr('disabled','disabled');
			}
		}
	}
	
	
	function fnc_bill_for(val)
	{
		if(val==1)
		{
			//$('#cbo_bill_for').removeAttr('disabled','disabled');
			$('#txt_bill_form_date').removeAttr('disabled','disabled');
			$('#txt_bill_to_date').removeAttr('disabled','disabled');
			//$('#txt_manual_challan').removeAttr('disabled','disabled');
			//$('#cbo_party_location').removeAttr('disabled','disabled');
		}
		else
		{
			//$('#cbo_bill_for').attr('disabled','disabled');
			

			//$('#txt_bill_form_date').attr('disabled','disabled'); // remove by issue 4444
			//$('#txt_bill_to_date').attr('disabled','disabled'); // remove by issue 4444

			$('#txt_bill_form_date').removeAttr('disabled','disabled'); // adding by issue 4444
			$('#txt_bill_to_date').removeAttr('disabled','disabled');  // adding by issue 4444


			//$('#txt_manual_challan').attr('disabled','disabled');
			//$('#cbo_party_location').attr('disabled','disabled');
		}
	}
	
	function fnc_list_search(type)
	{
		if($('#cbo_party_source').val()==1)
		{
			if( form_validation('cbo_company_id*cbo_party_source*cbo_party_name*cbo_location_name','Company Name*Party Name*Party Source*Party Location')==false)
			{
				return;
			}
			
			if( form_validation('txt_bill_form_date*txt_bill_to_date','From Date*To Date')==false)
			{
					return;
			}
			var location_cond=document.getElementById('cbo_location_name').value;
		}
		else
		{
			if( form_validation('cbo_company_id*cbo_location_name*cbo_party_source*cbo_party_name','Company Name*Location*Party Source*Party Name')==false)
			{
				return;
			}
			var location_cond=document.getElementById('cbo_location_name').value;
		}
		
		$('#cbo_company_id').attr('disabled',true);
		$('#cbo_location_name').attr('disabled',true);
		$('#cbo_party_source').attr('disabled',true);
		$('#cbo_party_name').attr('disabled',true);
		$('#cbo_bill_for').attr('disabled',true);
		//$('#cbo_party_location').attr('disabled',true);
			
		if($('#cbo_party_source').val()==1)
		{
			$('#txt_bill_form_date').removeAttr('disabled','disabled');
			$('#txt_bill_to_date').removeAttr('disabled','disabled');
			//$('#txt_manual_challan').removeAttr('disabled','disabled');
		}
		else
		{
			$('#txt_bill_form_date').attr('disabled','disabled');
			$('#txt_bill_to_date').attr('disabled','disabled');
			//$('#txt_manual_challan').attr('disabled','disabled');
		}
		
		if (type==0 && ($('#update_id').val()*1)==0)
		{
		
			show_list_view($('#cbo_party_source').val()+'***'+$('#cbo_party_name').val()+'***'+$('#update_id').val()+'***'+location_cond+'***'+$('#cbo_bill_for').val()+'***'+$('#txt_bill_form_date').val()+'***'+$('#txt_bill_to_date').val()+'***'+$('#cbo_company_id').val(),'cutting_delivery_list_view','cutting_info_list','requires/subcon_cutting_bill_issue_controller', 'setFilterGrid("list_view_issue",-1)','','');
		}
		else
		{
			var tot_row=$('#bill_issue_table tr').length;
				//alert(tot_row)
				
			var all_value="";
			if($('#cbo_party_source').val()==1)
			{
				for (var n=1; n<=tot_row; n++)
				{
					//if(all_value=="") all_value+=$('#txtChallenno_'+n).val()+'_'+$('#ordernoid_'+n).val()+'_'+$('#itemid_'+n).val()+'_'+$('#bodypartid_'+n).val()+'_'+$('#compoid_'+n).val()+'_'+$('#diaType_'+n).val()+'_'+$('#batchid_'+n).val(); 
					
				//	else all_value+='!!!!'+$('#txtChallenno_'+n).val()+'_'+$('#ordernoid_'+n).val()+'_'+$('#itemid_'+n).val()+'_'+$('#bodypartid_'+n).val()+'_'+$('#compoid_'+n).val()+'_'+$('#diaType_'+n).val()+'_'+$('#batchid_'+n).val();
				}
			}
			else
			{
				for (var n=1; n<=tot_row; n++)
				{
					if(all_value=="") all_value+=$('#deliveryid_'+n).val(); else all_value+='!!!!'+$('#deliveryid_'+n).val();
				}
			}
			//alert(all_value);
			
			
			show_list_view($('#cbo_party_source').val()+'***'+$('#cbo_party_name').val()+'***'+$('#update_id').val()+'***'+$('#issue_id_all').val()+'***'+$('#cbo_company_id').val()+'***'+$('#txt_bill_form_date').val()+'***'+$('#txt_bill_to_date').val()+'***'+location_cond+'***'+$('#cbo_bill_for').val()+'***'+type+'***'+all_value,'cutting_delivery_list_view','cutting_info_list','requires/subcon_cutting_bill_issue_controller','set_all();setFilterGrid("tbl_list_search",-1)','','');
			
			
		}
	}
	
</script>
</head>
<body onLoad="set_hotkey()">
<div align="center" style="width:100%;">
<? echo load_freeze_divs ("../",$permission);  ?>
    <form id="cuttingbillissue_1" name="cuttingbillissue_1" autocomplete="off">
        <fieldset style="width:800px;">
        <legend>Cutting Bill Info </legend>
            <table cellpadding="790" cellspacing="2" width="100%">
                <tr>
                    <td align="right" colspan="3"><strong>Bill No </strong></td>
                    <td width="140" align="justify">
                        <input type="hidden" name="hidden_acc_integ" id="hidden_acc_integ" />
                        <input type="hidden" name="hidden_integ_unlock" id="hidden_integ_unlock" />
                        <input type="hidden" name="selected_order_id" id="selected_order_id" />
                        <input type="hidden" name="selected_currency_no" id="selected_currency_no" />
                        <input type="hidden" name="update_id" id="update_id" />
                        <input type="text" name="txt_bill_no" id="txt_bill_no" class="text_boxes" style="width:140px" placeholder="Double Click to Search" onDblClick="openmypage_bill();" readonly tabindex="1" >
                    </td>
                </tr>
                <tr>
                    <td width="110" class="must_entry_caption">Company Name</td>
                    <td width="150">
                        <?php 
                            echo create_drop_down( "cbo_company_id",150,"select id,company_name from lib_company comp where is_deleted=0 and status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/subcon_cutting_bill_issue_controller', this.value, 'load_drop_down_location', 'location_td');","","","","","",2);	
                        ?>
                    </td>
                    <td width="110">Location Name</td>                                              
                    <td width="150" id="location_td">
                        <? 
                            echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "--Select Location--", $selected,"","","","","","",3);
                        ?>
                    </td>
                    <td width="110" class="must_entry_caption">Bill Date</td>                                              
                    <td width="150">
                        <input class="datepicker" type="text" style="width:140px" name="txt_bill_date" id="txt_bill_date" tabindex="4" />
                    </td>
                </tr> 
                <tr>
                    <td class="must_entry_caption">Party Source</td>
                    <td>
                        <?
                            echo create_drop_down( "cbo_party_source", 150, $knitting_source,"", 1, "-- Select Party --", $selected, "load_drop_down( 'requires/subcon_cutting_bill_issue_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_party_name', 'party_td' );fnc_bill_for(this.value)",0,"1,2","","","",4);
                        ?> 
                    </td>
                    <td class="must_entry_caption">Party Name</td>
                    <td id="party_td">
                        <?
                            echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"","","","",5);
                        ?> 
                    </td>
                    
                    <td>Bill For</td>
                    <td>
                        <?
                            echo create_drop_down( "cbo_bill_for", 150, $bill_for,"", 1, "-- Select Party --", $selected, "",0,"","","",4,7);
                        ?> 
                    </td>
                </tr> 
                <tr>
                            <td class="must_entry_caption">Trns. Date Range</td>                                              
                            <td><input class="datepicker" type="text" style="width:55px" name="txt_bill_form_date" id="txt_bill_form_date" placeholder="Form Date" disabled />&nbsp;<input class="datepicker" type="text" style="width:55px" name="txt_bill_to_date" id="txt_bill_to_date" placeholder="To Date" disabled />
                            </td>
                            <td>&nbsp;</td>                                              
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>                                              
                            <td><input class="formbutton" type="button" onClick="fnc_list_search(0);" style="width:130px" name="btn_populate" value="Populate" id="btn_populate" /></td>
              </tr>
            </table>
        </fieldset>
        <br>
        <fieldset style="width:870px;">
        <legend>Cutting Bill Info</legend>
            <table  style="border:none; width:860px;" cellpadding="0" cellspacing="1" border="0" id="">
                <thead class="form_table_header">
                    <th width="70" align="center">Delivery Date </th>
                    <th width="70" align="center" class="must_entry_caption">Challan No.</th>
                    <th width="80" align="center">Order No.</th>
                    <th width="80" align="center">Cust.Style</th>
                    <th width="80" align="center">Cust.Buyer</th>
                    <th width="50" align="center">No Carton</th>
                    <th width="130" align="center">Garments Item</th>                                      
                    <th width="40" align="center">UOM</th>
                    <th width="60" align="center">Delv. Qty</th>
                    <th width="40" align="center">Rate</th>
                    <th width="60" align="center">Amount</th>
                    <th width="60">Currency</th>
                    <th width="" align="center">Remarks</th>
                </thead>
                <tbody id="bill_issue_table">
                    <tr align="center">				
                        <td>
                            <input type="hidden" name="updateiddtls_1" id="updateiddtls_1">
                            <input type="text" name="deleverydate_1" id="deleverydate_1"  class="datepicker" style="width:65px" readonly />									
                        </td>
                        <td>
                            <input type="text" name="challenno_1" id="challenno_1"  class="text_boxes" style="width:65px" readonly />							 
                        </td>
                        <td>
                            <input type="hidden" name="ordernoid_1" id="ordernoid_1" value="">
                            <input type="text" name="orderno_1" id="orderno_1"  class="text_boxes" style="width:80px" readonly />										
                        </td>
                        <td>
                            <input type="text" name="stylename_1" id="stylename_1"  class="text_boxes" style="width:80px;" />
                        </td>
                        <td>
                            <input type="text" name="buyername_1" id="buyername_1"  class="text_boxes" style="width:80px" />								
                        </td>
                        <td>			
                            <input name="numberroll_1" id="numberroll_1" type="text" class="text_boxes" style="width:45px" readonly />							
                        </td>  
                        <td>
                            <input type="text" name="yarndesc_1" id="yarndesc_1"  class="text_boxes" style="width:125px" readonly/>
                        </td>
                        <td>
                            <? echo create_drop_down( "cbouom_1", 40, $unit_of_measurement,"", 0, "--Select UOM--",1,"",1,"" );?>
                        </td>
                        <td>
                            <input type="text" name="deliveryqnty_1" id="deliveryqnty_1"  class="text_boxes_numeric" style="width:60px" readonly />
                        </td>
                        <td>
                            <input type="text" name="txtrate_1" id="txtrate_1"  class="text_boxes_numeric" style="width:40px;" readonly />
                        </td>
                        <td>
                            <input type="text" name="amount_1" id="amount_1" style="width:60px" class="text_boxes_numeric" readonly />
                        </td>
                        <td>
							<? echo create_drop_down( "cbo_curanci_1", 60, $currency,"", 1, "-Select Currency-",1,"",0,"" );?>
                        </td>
                        <td>
                            <input type="text" name="remarks_1" id="remarks_1"  class="text_boxes" style="width:80px" />
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td width="70px">&nbsp;</td>								
                        <td width="70px">&nbsp;</td>
                        <td width="80px">&nbsp;</td>
                        <td width="80px">&nbsp;</td>
                        <td width="80px">&nbsp;</td>
                        <td width="50px">&nbsp;</td>
                        <td width="170px" colspan="2" align="right">Total Qty &nbsp;</td>
                        <td width="60px">
                            <input type="text" name="total_qnty" id="total_qnty"  class="text_boxes_numeric" style="width:60px" value="" readonly disabled />
                        </td>
                        <td width="40px">&nbsp;</td>
                        <td width="60px">
                            <input type="text" name="total_amount" id="total_amount"  class="text_boxes_numeric" style="width:60px" value="" readonly disabled />
                        </td>
                        <td width="">&nbsp;</td>
                        <td width="">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="13" height="15" align="center"><div id="accounting_integration_div" style="float:center; font-size:18px; color:#FF0000;"></div></td>
                    </tr>
                    <tr>
                        <td colspan="13" align="center" class="button_container">
                            <? 
                                echo load_submit_buttons($permission,"fnc_cutting_bill_issue",0,1,"reset_form('cuttingbillissue_1','cutting_info_list','','','$(\'#bill_issue_table tr:not(:first)\').remove();')",1);
                            ?> 
                        </td>
                    </tr>
                    <tr>
                        <td colspan="13" id="list_view" align="center"></td>
                    </tr> 
                 </tfoot>                                                              
            </table>
        </fieldset> 
    </form>
    <br>
    <div id="cutting_info_list"></div></div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
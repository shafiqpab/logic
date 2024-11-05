<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Sub-contract Sewing Bill Issue				
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	11-07-2013 
Updated by 		: 		
Update date		:
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
echo load_html_head_contents("Sewing Bill Issue", "../", 1,1, $unicode,1,'');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';

	function openmypage_bill()
	{ 
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_name').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/sewing_bill_issue_controller.php?data='+data+'&action=bill_no_popup','Bill Popup', 'width=680px,height=400px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("issue_id"); //Access form field with id="emailfield"
			
			//alert(theemail);
			if (theemail.value!="")
			{
				//freeze_window(5);
				get_php_form_data( theemail.value, "load_php_data_to_form_issue", "requires/sewing_bill_issue_controller" );
				selected_id_listed = new Array();
				window_close(theemail.value );
				accounting_integration_check($('#hidden_acc_integ').val(),$('#hidden_integ_unlock').val());
				fnc_list_search(theemail.value);
				set_button_status(1, permission, 'fnc_sewing_bill_issue',1);
				
				
				$('#tbl_list_search tbody tr').each(function(index, element) {
					
					if( $('#'+this.id).attr('bgcolor')=='yellow' )
					{
						var nid=this.id;//.replace( 'tr_', "");  
						var nid=nid.replace( 'tr_', "");  
						if( jQuery.inArray(  nid , selected_id_listed ) == -1) 
						{
							selected_id_listed.push( nid );
						}
						else
						{
							for( var i = 0; i < selected_id_listed.length; i++ ) {
								if( selected_id_listed[i] == nid  ) break;
							}
							selected_id_listed.splice( i, 1 );
						}
					}
				});
				
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
					js_set_value( old[i]+"_"+document.getElementById('currid'+old[i]).value);
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
					js_set_value( old[i]+"_"+'1');
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
			
		var data=document.getElementById('selected_order_id').value+"_"+issue_id+"_"+frm+"_"+document.getElementById('cbo_party_source').value;
		var list_view_orders = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/sewing_bill_issue_controller');
		
		if(list_view_orders!='')
		{
			$("#bill_issue_table tr").remove();
			$("#bill_issue_table").append(list_view_orders);
		}
		var tot_row=$('#bill_issue_table tr').length;
		math_operation( "txt_tot_qnty", "txt_deliveryqnty_", "+", tot_row );
		math_operation( "txt_tot_amount", "txt_amount_", "+", tot_row );
		set_all_onclick();
	}

	function fnc_sewing_bill_issue( operation )
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_bill_no').val()+'*'+report_title, "sewing_bill_print", "requires/sewing_bill_issue_controller") 
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
			
			if ( form_validation('cbo_company_id*txt_bill_date*cbo_party_name*cbo_party_source','Company Name*Bill Date*Party Name*Party Source')==false )
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
					if (form_validation('txt_challenno_'+i+'*txt_rate_'+i,'Challan*Rate')==false)
					{
						return;
					}
					else if($('#txt_rate_'+i).val()==0)
					{
						alert ("Rate Not Blank or Zero.");
						$('#txt_rate_'+i).focus();
						return;
					}
					data2+=get_submitted_data_string('txt_deleverydate_'+i+'*txt_challenno_'+i+'*ordernoid_'+i+'*color_process_'+i+'*itemid_'+i+'*txt_numberroll_'+i+'*txt_deliveryqnty_'+i+'*txt_rate_'+i+'*txt_amount_'+i+'*txt_remarks_'+i+'*txt_stylename_'+i+'*txt_buyername_'+i+'*deliveryid_'+i+'*curanci_'+i+'*updateiddtls_'+i+'*cbo_curanci_'+i+'*delete_id',"../");
				}
				var data=data1+data2;
				// alert (data2); return; 
				freeze_window(operation);
				http.open("POST","requires/sewing_bill_issue_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_sewing_bill_issue_reponse;
			}
		}
	}

	function fnc_sewing_bill_issue_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
			//alert (response[2]);return;
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
				set_button_status(1, permission, 'fnc_sewing_bill_issue',1);
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

	function toggle( x, origColor ) {
		var newColor = 'yellow';
		//alert (x);
		if ( x.style ) {
		x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}

	function js_set_value(id)
	{
		var str=id.split("_");
		// if( jQuery.inArray( str[1], selected_currency_id ) != -1  || selected_currency_id.length<1 )
		// {
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
			
			//alert(id);
			
			$('#selected_order_id').val( id );
			$('#selected_currency_no').val( currency );
		// }
		// else
		// {
		// 	$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
		// 	 { 
		// 		$(this).html('Currency Mix Not Allowed').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
		// 	 });
		// }
	}
	
	function qnty_caluculation(id)
	{
		$("#txt_amount_"+id).val(($("#txt_deliveryqnty_"+id).val()*1)*($("#txt_rate_"+id).val()*1));
		var tot_row=$('#bill_issue_table tr').length;
		math_operation( "txt_tot_qnty", "txt_deliveryqnty_", "+", tot_row );
		math_operation( "txt_tot_amount", "txt_amount_", "+", tot_row );
	
		
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
				$('#txt_numberroll_'+i).attr('disabled','disabled');
				$('#txt_deliveryqnty_'+i).attr('disabled','disabled');
				$('#txt_rate_'+i).attr('disabled','disabled');
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
				$('#txt_numberroll_'+i).removeAttr('disabled','disabled');
				$('#txt_deliveryqnty_'+i).removeAttr('disabled','disabled');
				$('#txt_rate_'+i).removeAttr('disabled','disabled');
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


			//$('#txt_bill_form_date').attr('disabled','disabled'); //remove by issue no 4445
			//$('#txt_bill_to_date').attr('disabled','disabled'); //remove by issue no 4445

			$('#txt_bill_form_date').removeAttr('disabled','disabled'); // adding by issue no 4445
			$('#txt_bill_to_date').removeAttr('disabled','disabled'); // adding by issue no 4445


			//$('#txt_manual_challan').attr('disabled','disabled');
			//$('#cbo_party_location').attr('disabled','disabled');
		}
	}
	function checkall_data()
	{
		if(document.getElementById('checkall').checked==true)
		{
			document.getElementById('checkall').value=1;
		}
		else if(document.getElementById('checkall').checked==false)
		{
			document.getElementById('checkall').value=2;
		}


		var tbl_row_count = document.getElementById( 'list_view_issue' ).rows.length;
		tbl_row_count = tbl_row_count - 1;
		
		for( var i = 1; i <= tbl_row_count; i++ ) 
		{
			
			var issue_id=$('#pro_gmts_mst_id_'+i).val();

			if($("#tr_"+issue_id).css("display") != "none")
			{
				js_set_value(issue_id+'_'+i)
				if($('#checkall').val()==1)
				{
					document.getElementById('checkid'+i).checked=true;
					document.getElementById('checkid'+i).value=1;
				}
				else if($('#checkall').val()==2) 
				{
					document.getElementById('checkid'+i).checked=false;
					document.getElementById('checkid'+i).value=2;
				}
			}
		
			
		}
	}

	function fnc_check(inc_id)
	{
		if(document.getElementById('checkid'+inc_id).checked==true)
		{
			document.getElementById('checkid'+inc_id).value=1;
		}
		else if(document.getElementById('checkid'+inc_id).checked==false)
		{
			document.getElementById('checkid'+inc_id).value=2;
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
			show_list_view($('#cbo_party_source').val()+'***'+$('#cbo_party_name').val()+'***'+$('#update_id').val()+'***'+location_cond+'***'+$('#cbo_bill_for').val()+'***'+$('#txt_bill_form_date').val()+'***'+$('#txt_bill_to_date').val()+'***'+$('#cbo_company_id').val(),'sewing_delivery_list_view','sewing_info_list','requires/sewing_bill_issue_controller', 'setFilterGrid("list_view_issue",-1)','','');
		}
		else
		{
			var tot_row=$('#bill_issue_table tr').length;
				//alert(tot_row)
				
			var all_value="";
			
			var tot_row=$('#bill_issue_table tr').length;
				//alert(tot_row)
			var all_value="";
			
			if($('#cbo_party_source').val()==2)
			{
				for (var n=1; n<=tot_row; n++)
				{
					if(all_value=="") all_value+=$('#deliveryid_'+n).val(); else all_value+='!!!!'+$('#deliveryid_'+n).val();
				}
			}
			
			
			show_list_view($('#cbo_party_source').val()+'***'+$('#cbo_party_name').val()+'***'+$('#update_id').val()+'***'+$('#issue_id_all').val()+'***'+$('#cbo_company_id').val()+'***'+$('#txt_bill_form_date').val()+'***'+$('#txt_bill_to_date').val()+'***'+location_cond+'***'+$('#cbo_bill_for').val()+'***'+type+'***'+all_value,'sewing_delivery_list_view','sewing_info_list','requires/sewing_bill_issue_controller','set_all();setFilterGrid("tbl_list_search",-1)','','');
		}
	}
	
	function copy_rate(id){ $('#copy_val').val(id);}

	function copy_rate_currency(id){

		
		var copy=$("#copy_val").val();	
		var tbl_row_count = document.getElementById( 'bill_issue_table' ).rows.length;
		
		// tbl_row_count = tbl_row_count - 1;		
		if(copy==1){
			var rate=$("#txt_rate_"+id).val();
			var currency=$("#cbo_curanci_"+id).val();
			
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				
				$("#txt_rate_"+i).val(rate);
				$("#cbo_curanci_"+i).val(currency);
				var amount=($("#txt_deliveryqnty_"+i).val()*1)*($("#txt_rate_"+i).val()*1);
				$("#txt_amount_"+i).val(amount);
				var total_amount =amount;
			}
			math_operation( "txt_tot_amount", "txt_amount_", "+", tbl_row_count );
	   }
	
	}

</script>
</head>
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
    <? echo load_freeze_divs ("../",$permission);  ?>
    <form name="sewingbillissue_1" id="sewingbillissue_1"  autocomplete="off"  >
    <fieldset style="width:800px;">
    <legend>Sewing Bill Info </legend>
        <table cellpadding="0" cellspacing="2" width="790">
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
                        echo create_drop_down( "cbo_company_id",150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/sewing_bill_issue_controller', this.value, 'load_drop_down_location', 'location_td');","","","","","",2);	
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
                        echo create_drop_down( "cbo_party_source", 150, $knitting_source,"", 1, "-- Select Party --", $selected, "load_drop_down( 'requires/sewing_bill_issue_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_party_name', 'party_td' );fnc_bill_for(this.value)",0,"1,2","","","",4);
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
                        echo create_drop_down( "cbo_bill_for", 150, $bill_for,"", 1, "-- Select Party --", $selected, "",0,"","","","",7);
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
    <fieldset style="width:950px;">
    <legend>Sewing Bill Info </legend><span><input type="radio" id="copy_id" name="copy_id" onclick="copy_rate(1)" checked>Copy Rate/Currency <input type="radio" id="copy_id"  name="copy_id" onclick="copy_rate(2)">Un-Copy Rate/Currency<input type="hidden"id="copy_val" name="copy_val" value="1"></span>
    <table  style="border:none; width:940px;" cellpadding="0" cellspacing="1" border="0" id="">
        <thead class="form_table_header">
            <th width="65">Delivery Date </th>
            <th width="60" class="must_entry_caption">Challan No.</th>
            <th width="70">Order No.</th>
            <th width="80">Cust.Style</th>
            <th width="80">Cust.Buyer</th>
            <th width="40">No. Roll</th>
            <th width="110">Item Description</th>
            <th width="85">Color/Process</th>
            <th width="120">Additional Process</th>
            <th width="60">Qty(Pcs)</th>
            <th width="40" class="must_entry_caption">Rate</th>
            <th width="60">Amount</th>
            <th width="60">Currency</th>
            <th>Remarks</th>
        </thead>
        <tbody id="bill_issue_table">
            <tr align="center">				
                <td>
                    <input type="hidden" name="updateiddtls_1" id="updateiddtls_1" style="width:40px">
                    <input type="text" name="txt_deleverydate_1" id="txt_deleverydate_1"  class="datepicker" style="width:60px" readonly />									
                </td>
                <td>
                    <input type="text" name="txt_challenno_1" id="txt_challenno_1"  class="text_boxes" style="width:55px" readonly />							 
                </td>
                <td>
                    <input type="hidden" name="ordernoid_1" id="ordernoid_1" value="" style="width:40px">
                    <input type="text" name="txt_orderno_1" id="txt_orderno_1"  class="text_boxes" style="width:65px" readonly />										
                </td>
                <td>
                    <input type="text" name="txt_stylename_1" id="txt_stylename_1"  class="text_boxes" style="width:80px;" />
                </td>
                <td>
                    <input type="text" name="txt_buyername_1" id="txt_buyername_1"  class="text_boxes" style="width:80px" />								
                </td>
                <td>			
                    <input type="text" name="txt_numberroll_1" id="txt_numberroll_1" class="text_boxes" style="width:40px" readonly />							
                </td>  
                <td>
                    <input type="text" name="text_febricdesc_1" id="text_febricdesc_1"  class="text_boxes_numeric" style="width:105px" readonly/>
                </td>
                <td>
                    <input type="text" name="txt_color_process_1" id="txt_color_process_1"  class="text_boxes" style="width:80px" readonly/>
                </td>
                <td>
                    <input type="text" name="txt_add_process_1" id="txt_add_process_1"  class="text_boxes" style="width:115px" readonly/>
                </td>
                <td>
                    <input type="text" name="txt_qnty_1" id="txt_qnty_1"  class="text_boxes_numeric" style="width:60px" readonly />
                </td>
                <td>
                    <input type="text" name="txt_rate_1" id="txt_rate_1"  class="text_boxes" style="width:40px"  onBlur="qnty_caluculation(1);" readonly />
                </td>
                <td>
                    <input type="text" name="txt_amount_1" id="txt_amount_1" class="text_boxes" style="width:60px"  readonly />
                </td>
                <td>
					<? echo create_drop_down( "cbo_curanci_1", 60, $currency,"", 1, "-Select Currency-",1,"",0,"" );?>
                </td>
                <td>
                    <input type="text" name="txt_remarks_1" id="txt_remarks_1"  class="text_boxes" style="width:70px" />
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td width="65px">&nbsp;</td>
                <td width="60px">&nbsp;</td>
                <td width="70px">&nbsp;</td>
                <td width="80px">&nbsp;</td>
                <td width="80px">&nbsp;</td>
                <td width="40px">&nbsp;</td>
                <td width="110px">&nbsp;</td>
                <td width="85px">&nbsp;</td>
                <td width="120px" align="right">Total: &nbsp;</td>
                <td width="60px">
                    <input type="text" name="txt_tot_qnty" id="txt_tot_qnty"  class="text_boxes_numeric" style="width:60px" disabled />
                </td>
                <td width="40px">&nbsp;</td>
                <td width="60px">
                    <input type="text" name="txt_tot_amount" id="txt_tot_amount"  class="text_boxes_numeric" style="width:60px" disabled/>
                </td>
                <td width="70px">&nbsp;</td>
                <td width="70px">&nbsp;</td>
            </tr> 
            <tr>
                <td colspan="13" height="15" align="center"><div id="accounting_integration_div" style="float:center; font-size:18px; color:#FF0000;"></div></td>
            </tr>               
            <tr>
                <td colspan="13" align="center" class="button_container">
                    <? 
                        echo load_submit_buttons($permission,"fnc_sewing_bill_issue",0,1,"reset_form('sewingbillissue_1','sewing_info_list','','','$(\'#bill_issue_table tr:not(:first)\').remove();')",1);
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
    &nbsp;
    <div id="sewing_info_list"></div>                           
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
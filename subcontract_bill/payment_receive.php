<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Sub-contract Payment Receive
Functionality	:	
					
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	24-6-2013
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
echo load_html_head_contents("Payment Receive", "../", 1,1, $unicode,1,'');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';

	function disable_field(str)
	{
		if($('#cbo_payment_type').val()==1)
		{
			if(str==4)	 	
			{
				document.getElementById("text_net_amount").value = '';
				document.getElementById("text_net_amount").disabled = true;
				document.getElementById("text_bank_name").disabled = true;
				document.getElementById("text_instrument_date").disabled = true;
				document.getElementById("text_instrument_no").disabled = true;
				document.getElementById("cbo_adjustment_type").disabled = false;
				document.getElementById("text_adjust_amount").disabled = false;
			}
			else if(str==5)	 	
			{
				document.getElementById("text_net_amount").value = '';
				document.getElementById("text_net_amount").disabled = true;
				document.getElementById("text_bank_name").disabled = true;
				document.getElementById("text_instrument_date").disabled = true;
				document.getElementById("text_instrument_no").disabled = true;
				document.getElementById("cbo_adjustment_type").disabled = false;
				document.getElementById("text_adjust_amount").disabled = false;
			}
			else if(str==2)      
			{
				document.getElementById("cbo_adjustment_type").disabled = true;
				document.getElementById("text_adjust_amount").disabled = true;
				document.getElementById("text_bank_name").disabled = false;
				document.getElementById("text_instrument_date").disabled = false;
				document.getElementById("text_instrument_no").disabled = false;
				document.getElementById("text_net_amount").disabled = false;
			}
			else if(str==3)      
			{
				document.getElementById("cbo_adjustment_type").disabled = true;
				document.getElementById("text_adjust_amount").disabled = true;
				document.getElementById("text_bank_name").disabled = false;
				document.getElementById("text_instrument_date").disabled = false;
				document.getElementById("text_instrument_no").disabled = false;
				document.getElementById("text_net_amount").disabled = false;
			}
			else if(str==1)  
			{ 
				document.getElementById("cbo_adjustment_type").disabled = true;
				document.getElementById("text_adjust_amount").disabled = true;
				document.getElementById("text_bank_name").disabled = true;
				document.getElementById("text_instrument_date").disabled = true;
				document.getElementById("text_instrument_no").disabled = true;
				document.getElementById("text_net_amount").disabled = false;
			}
		}
		else if ($('#cbo_payment_type').val()==3)
		{
			if(str==5)
			{
				document.getElementById("text_net_amount").value = '';
				document.getElementById("text_net_amount").disabled = true;
				document.getElementById("text_bank_name").disabled = true;
				document.getElementById("text_instrument_date").disabled = true;
				document.getElementById("text_instrument_no").disabled = true;
				document.getElementById("cbo_adjustment_type").disabled = false;
				document.getElementById("text_adjust_amount").disabled = false;
			}
		}
	}

	function advance_disable(val)
	{
		if(val==2)	 	
		{
			document.getElementById("text_net_amount").value = '';
			document.getElementById("text_net_amount").disabled = true;
			document.getElementById("cbo_clearance_method").disabled = true;
			document.getElementById("cbo_adjustment_type").disabled = true;
			document.getElementById("text_advance_amount").disabled = false;
		}
		else if(val==1)	 
		{
			//document.getElementById("text_net_amount").value = '';
			document.getElementById("text_net_amount").disabled = false;
			document.getElementById("cbo_clearance_method").disabled = false;
			document.getElementById("cbo_adjustment_type").disabled = true;
			document.getElementById("text_advance_amount").disabled = false;
		}
		else if(val==3)
		{
			document.getElementById("text_net_amount").value = '';
			document.getElementById("text_net_amount").disabled = true;
			document.getElementById("cbo_clearance_method").disabled = false;
			document.getElementById("cbo_adjustment_type").disabled = false;
			document.getElementById("text_advance_amount").disabled = true;
		}
		else
		{
			document.getElementById("text_net_amount").value = '';
			document.getElementById("text_net_amount").disabled = false;
			document.getElementById("cbo_clearance_method").disabled = false;
			document.getElementById("cbo_adjustment_type").disabled = true;
			document.getElementById("text_advance_amount").disabled = false;
		}
	}

	function openmypage_system_id()
	{ 
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_name').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/payment_receive_controller.php?data='+data+'&action=receive_no_popup','Receive Popup', 'width=750px,height=400px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("payment_id") 
			if (theemail.value!="")
			{
				freeze_window(5);
				get_php_form_data( theemail.value, "load_php_data_to_form_payment", "requires/payment_receive_controller" );
				//show_list_view(document.getElementById('cbo_party_name').value+'_'+document.getElementById('update_id').value+'_'+document.getElementById('cbo_currency').value+'_'+document.getElementById('cbo_bill_type').value,'payment_receive_list_view','payment_receive_list_view','requires/payment_receive_controller','');
				show_list_view(document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_bill_type').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('update_id').value+'_'+document.getElementById('cbo_payment_type').value,'payment_receive_list_view','payment_receive_list_view','requires/payment_receive_controller','');
				//setFilterGrid('list_view_payment',-1);
				
				disable_field(document.getElementById('cbo_instrument').value);
				accounting_integration_check($('#hidden_acc_integ').val());
				set_button_status(1, permission, 'fnc_payment_receive',1,1);
				release_freezing();
			}
		}
	}

	function amount_distebute( val,curency,intrument_type )
	{
		if(intrument_type==1 || intrument_type==2 || intrument_type==3)
		{
			var amount=document.getElementById('text_net_amount').value;
		}
		else if(intrument_type==4 || intrument_type==5 || intrument_type==6)
		{
			var amount=document.getElementById('text_adjust_amount').value;
		}
		var remain=amount;
		var tot_row=$('#list_view_payment tbody tr').length-1;
		
		if((document.getElementById('total_bill').value*1)<remain)
		{
			//alert (remain);
			alert ('Over Payment Not Allowed');
			return;
		}
		
		var total=0;
		for(var k=1; k<=tot_row; k++)
		{
			if (val==1)
			{
				$('#currentpayment_'+k).val('');
				$('#latestbalance_'+k).val('');
				$('#exchengerate_'+k).val('');
				//$('#billamount_'+k).val('');
				$('#total_payment').val('');
				$('#currentpayment_'+k).attr('readOnly','readOnly');
				//$('#exchengerate_'+k).attr('readOnly','readOnly');
				
				if((document.getElementById('billamount_'+k).value*1)>=remain)
				{
					document.getElementById('currentpayment_'+k).value=remain;
					document.getElementById('latestbalance_'+k).value=document.getElementById('billamount_'+k).value-remain;
					//total=number_format((total*1)+(remain*1));
					total=(total*1)+(remain*1);
					total=number_format (total, 2,'.' , "") ;
					//alert (total);
					document.getElementById('total_payment').value=total;
					if(curency==$('#currencyid_'+k).val())
					{
						document.getElementById('exchengerate_'+k).value=1;
						document.getElementById('exchengamount_'+k).value=(document.getElementById('currentpayment_'+k).value*1)*(document.getElementById('exchengerate_'+k).value*1);
						document.getElementById('exchengerate_'+k).disabled = true;
					}
					return;
				}
				else
				{
					document.getElementById('currentpayment_'+k).value=document.getElementById('billamount_'+k).value;
					document.getElementById('latestbalance_'+k).value=0;
					total=(total*1)+(document.getElementById('billamount_'+k).value*1);
					//alert (total+'ggggg');
					remain=remain-document.getElementById('billamount_'+k).value;
					if(curency==$('#currencyid_'+k).val())
					{
						document.getElementById('exchengerate_'+k).value=1;
						document.getElementById('exchengamount_'+k).value=(document.getElementById('currentpayment_'+k).value*1)*(document.getElementById('exchengerate_'+k).value*1);
						document.getElementById('exchengerate_'+k).disabled = true;
					}
				}
			}
			else if(val==2)
			{
				$('#currentpayment_'+k).val('');
				$('#latestbalance_'+k).val('');
				$('#exchengerate_'+k).val('');
				//$('#billamount_'+k).val('');
				$('#total_payment').val('');
				if(curency==$('#currencyid_'+k).val())
					{
						document.getElementById('exchengerate_'+k).value=1;
						document.getElementById('exchengamount_'+k).value=(document.getElementById('currentpayment_'+k).value*1)*(document.getElementById('exchengerate_'+k).value*1);
						document.getElementById('exchengerate_'+k).disabled = true;
					}
					

				$('#currentpayment_'+k).removeAttr('readOnly','readOnly');
				$('#exchengerate_'+k).removeAttr('readOnly','readOnly');
				$('#currentpayment_'+k).removeAttr("onBlur").attr("onBlur","latest_balance_calculate("+k+");");
				//document.getElementById('currentpayment_'+k).readonly = true;
				//document.getElementById('exchengerate_'+k).readonly = true;
			}
		}
	}

	function latest_balance_calculate(id)
	{
		document.getElementById('latestbalance_'+id).value=document.getElementById('billamount_'+id).value-document.getElementById('currentpayment_'+id).value;
		var tot_row=$('#list_view_payment tbody tr').length-1;
		var amount_total=0;
		for(var k=1; k<=tot_row; k++)
		{
			var amount_total=(amount_total*1)+((document.getElementById('currentpayment_'+k).value)*1);
		}
		document.getElementById('total_payment').value=amount_total;
	}
	
	function exchenge_rate_val(rate)
	{
		var tot_row=$('#list_view_payment tbody tr').length-1;
		var amount_total=0;
		for(var k=1; k<=tot_row; k++)
		{
			amount_total=(document.getElementById('currentpayment_'+k).value*1)*(rate*1);
			document.getElementById('exchengamount_'+k).value=amount_total;
		}
	}

	function fnc_payment_receive( operation )
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val()+'*'+report_title, "money_receipt_print", "requires/payment_receive_controller") 
			//return;
			show_msg("3");
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if( form_validation('cbo_company_id*cbo_payment_type','Company Name*Payment Type')==false)
			{
				return;
			}
			else
			{
				if($('#cbo_payment_type').val()==1 || $('#cbo_payment_type').val()==3)
				{
					if( form_validation('cbo_party_name*txt_receipt_date*cbo_currency*cbo_clearance_method*cbo_instrument','Party Name*Receipt Date*Currency*Clearance Method*Instrument')==false)
					{
						return;
					}
					var instrument_id=$('#cbo_instrument').val();
					var total_payment=$('#total_payment').val()*1;
					if(instrument_id==1 || instrument_id==2 || instrument_id==3)
					{
						var net_amount=$('#text_net_amount').val()*1;
					}
					else if(instrument_id==4 || instrument_id==5)
					{
						var net_amount=$('#text_adjust_amount').val()*1;
					}
					//alert (net_amount);return;
					if(net_amount!=total_payment)
					{
						alert("Net Amount & Total Distribute Amount Not Same.");
						return;
					}
					else
					{
						var tot_row=$('#list_view_payment tbody tr').length-1;
						var data1="action=save_update_delete&operation="+operation+"&tot_row="+tot_row+get_submitted_data_string('txt_system_id*cbo_company_id*cbo_payment_type*cbo_party_name*cbo_bill_type*txt_receipt_date*cbo_instrument*cbo_currency*text_net_amount*cbo_adjustment_type*text_adjust_amount*text_bank_name*text_instrument_date*text_instrument_no*cbo_clearance_method*text_advance_amount*text_remarks*update_id',"../");
						//alert (total_payment);return;
						var data2='';
						for(var i=1; i<=tot_row; i++)
						{
							if(trim($("#currentpayment_"+i).val())!="")
							{
								if( form_validation('exchengerate_'+i,'Exchange Rate')==false)
								{
									return;
								}
								
								data2+=get_submitted_data_string('processid_'+i+'*billid_'+i+'*billno_'+i+'*billdate_'+i+'*currencyid_'+i+'*billamount_'+i+'*currentpayment_'+i+'*latestbalance_'+i+'*exchengerate_'+i+'*exchengamount_'+i+'*updateiddtls_'+i,"../",2);
							}
						}
						var data=data1+data2;
						//alert (data);return;
						freeze_window(operation);
						http.open("POST","requires/payment_receive_controller.php",true);
						http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
						http.send(data);
						http.onreadystatechange = fnc_payment_receive_response;
					}
				}
				else if($('#cbo_payment_type').val()==2)
				{
					if( form_validation('cbo_party_name*txt_receipt_date*cbo_currency','Party Name*Receipt Date*Currency')==false)
					{
						return;
					}
					/*var instrument_id=$('#cbo_instrument').val();
					var total_payment=$('#total_payment').val()*1;
					if(instrument_id==1 || instrument_id==2 || instrument_id==3)
					{
						var net_amount=$('#text_net_amount').val()*1;
					}
					else if(instrument_id==4 || instrument_id==5)
					{
						var net_amount=$('#text_adjust_amount').val()*1;
					}
					//alert (net_amount);return;
					if(net_amount!=total_payment)
					{
						alert("Net Amount & Total Distribute Amount Not Same.");
						return;
					}*/
					else
					{
						//var tot_row=$('#list_view_payment tbody tr').length-1;
						var data1="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_system_id*cbo_company_id*cbo_payment_type*cbo_party_name*cbo_bill_type*txt_receipt_date*cbo_instrument*cbo_currency*text_net_amount*cbo_adjustment_type*text_adjust_amount*text_bank_name*text_instrument_date*text_instrument_no*cbo_clearance_method*text_advance_amount*text_remarks*update_id',"../");
						//alert (total_payment);return;
						var data=data1;
						//alert (data);//return;
						freeze_window(operation);
						http.open("POST","requires/payment_receive_controller.php",true);
						http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
						http.send(data);
						http.onreadystatechange = fnc_payment_receive_response;
					}
				}
			}
		}
	}

	function fnc_payment_receive_response()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
			//if (response[0].length>2) reponse[0]=10;
			if(response[0]*1==14*1)
			{
				release_freezing();
				alert(response[1]);
				return;
			}
			else if(response[0]==0 || response[0]==1)
			{
				show_msg(response[0]);
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_system_id').value = response[2];
				set_button_status(1, permission, 'fnc_payment_receive',1,1);
				show_list_view(document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_bill_type').value+'_'+ document.getElementById('cbo_party_name').value+'_'+document.getElementById('update_id').value+'_'+document.getElementById('cbo_payment_type').value,'payment_receive_list_view','payment_receive_list_view','requires/payment_receive_controller','');
			}
			release_freezing();
		}
	}
	
	function load_unadjusted_amount(val)
	{
		var company_id=$('#cbo_company_id').val();
		var bill_type=$('#cbo_bill_type').val();
		var party_id=$('#cbo_party_name').val();
		var payment_type=$('#cbo_payment_type').val();
		var instrument_id=$('#cbo_instrument').val();
		//alert(val);
		//var net_amount=$('#text_adjust_amount').val()*1;
		if(payment_type==3 && val==6)
		{
			if(instrument_id==4  || instrument_id==5)
			{
				get_php_form_data( company_id+'_'+bill_type+'_'+party_id+'_'+payment_type+'_'+instrument_id+'_'+val, "load_unadjustmed_amount", "requires/payment_receive_controller" );
			}
		}
		
	}
	
	function unadjusted_amount_chack(val)
	{
		var payment_type=$('#cbo_payment_type').val();
		var instrument_id=$('#cbo_instrument').val();
		var adjustment_type=$('#cbo_adjustment_type').val();
		var unadjusted_amount=$('#text_unadj_advance').val()*1;
		if(payment_type==3 && adjustment_type==6)
		{
			if(instrument_id==4  || instrument_id==5)
			{
				if(val*1>unadjusted_amount)
				{
					alert("Adjusted Amount Greater Then Unadjusted Advance.");
					$('#text_adjust_amount').val('');
					return;
				}
			}
		}
		
	}
	
	function disable_flds(val)
	{
		if (val!=0)
		{
			document.getElementById("cbo_company_id").disabled = true;
			document.getElementById("cbo_bill_type").disabled = true;
			document.getElementById("cbo_payment_type").disabled = true;
		}
		else
		{
			document.getElementById("cbo_company_id").disabled = false;
			document.getElementById("cbo_bill_type").disabled = false;
			document.getElementById("cbo_payment_type").disabled = false;
		}
	}
	
	function accounting_integration_check(val)
	{
		var tot_row=$('#list_view_payment tbody tr').length-1;
		var payment_type=$('#cbo_payment_type').val();
		//alert (val);
		if(val==1)
		{
			$('#cbo_company_id').attr('disabled','disabled');
			$('#cbo_bill_type').attr('disabled','disabled');
			$('#cbo_payment_type').attr('disabled','disabled');
			$('#cbo_party_name').attr('disabled','disabled');
			$('#txt_receipt_date').attr('disabled','disabled');
			$('#cbo_instrument').attr('disabled','disabled');
			$('#cbo_currency').attr('disabled','disabled');
			$('#text_net_amount').attr('disabled','disabled');
			$('#cbo_adjustment_type').attr('disabled','disabled');
			$('#text_adjust_amount').attr('disabled','disabled');
			$('#text_instrument_date').attr('disabled','disabled');
			$('#cbo_clearance_method').attr('disabled','disabled');
			$('#text_advance_amount').attr('disabled','disabled');
			if(payment_type!=2)
			{
				for(var i=1; i<=tot_row; i++)
				{
					$('#currentpayment_'+i).attr('disabled','disabled');
					$('#exchengerate_'+i).attr('disabled','disabled');
				}
			}
		}
		else
		{
			$('#cbo_company_id').removeAttr('disabled','disabled');
			$('#cbo_bill_type').removeAttr('disabled','disabled');
			$('#cbo_payment_type').removeAttr('disabled','disabled');
			$('#cbo_party_name').removeAttr('disabled','disabled');
			$('#txt_receipt_date').removeAttr('disabled','disabled');
			$('#cbo_party_name').removeAttr('disabled','disabled');
			$('#cbo_currency').removeAttr('disabled','disabled');
			$('#text_net_amount').removeAttr('disabled','disabled');
			$('#cbo_adjustment_type').removeAttr('disabled','disabled');
			$('#text_adjust_amount').removeAttr('disabled','disabled');
			$('#text_instrument_date').removeAttr('disabled','disabled');
			$('#cbo_clearance_method').removeAttr('disabled','disabled');
			$('#text_advance_amount').removeAttr('disabled','disabled');
			if(payment_type!=2)
			{
				for(var i=1; i<=tot_row; i++)
				{
					$('#currentpayment_'+i).removeAttr('disabled','disabled');
					$('#exchengerate_'+i).removeAttr('disabled','disabled');
				}
			}
		}
	}
	
</script>
</head>
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
    <? echo load_freeze_divs ("../",$permission);  ?>
    <form id="paymentreceive_1" name="paymentreceive_1" autocomplete="off">
        <fieldset style="width:800px;">
        <legend>Payment Receive info </legend>
            <table cellpadding="2" cellspacing="2" width="790">
                <tr>
                    <td align="right" colspan="3"><strong>System ID</strong></td>
                    <td width="140" align="justify">
                    	<input type="hidden" name="hidden_acc_integ" id="hidden_acc_integ" />
                        <input type="hidden" name="update_id" id="update_id" />
                        <input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:140px" placeholder="Double Click to Search" onDblClick="openmypage_system_id();" readonly tabindex="1" >
                    </td>
                </tr>
                <tr>
                    <td width="110" class="must_entry_caption" >Company Name</td>
                    <td width="150">
						<? 
							echo create_drop_down( "cbo_company_id",150,"select id,company_name from lib_company comp where is_deleted=0 and status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/payment_receive_controller',this.value, 'load_drop_down_party_name', 'party_td' );","","","","","",2);	
                        ?>
                    </td>
                    <td width="110">Bill Type</td>                                              
                    <td width="150">
						<?
							echo create_drop_down( "cbo_bill_type", 150, $production_process,"", 1, "-- Select Party --", $selected, "",0,"","","","",4);
                        ?> 
                    </td>
                    <td width="110" class="must_entry_caption">Payment Type</td>
                    <td width="140">
						<?
							echo create_drop_down( "cbo_payment_type", 150, $payment_type,"", 0, "-- Select Type --", $selected, "advance_disable(this.value)",0,"","","","","");
                        ?> 
                    </td>
                </tr> 
                <tr>
                	<td width="110" class="must_entry_caption">Party Name</td>
                    <td width="140" id="party_td">
						<?
							echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "-- Select Party --", $selected, "",1,"","","","",3);
                        ?>
                    </td>
                    <td class="must_entry_caption">Receipt Date</td>                                              
                    <td>
                        <input class="datepicker" type="text" style="width:140px" name="txt_receipt_date" id="txt_receipt_date" tabindex="5" readonly />
                    </td>
                    <td class="must_entry_caption">Instrument</td>
                    <td>
						<?
							echo create_drop_down( "cbo_instrument", 150, $instrument_payment,"", 1, "-- Select Instrument --",1, "disable_field(this.value)",0,"","","","",6);
                        ?> 
                    </td>
                </tr>
                <tr>
                	<td class="must_entry_caption">Currency</td>                                              
                    <td id="currency_td">
						<?
							echo create_drop_down("cbo_currency", 150, $currency,"", 1, "-- Select Currency --",$selected,"", "","","","","",7 );//show_list_view(document.getElementById('cbo_party_name').value+'_'+document.getElementById('update_id').value+'_'+ this.value+'_'+document.getElementById('cbo_bill_type').value,'payment_receive_list_view','payment_receive_list_view','requires/payment_receive_controller','');
                        ?>
                    </td>
                    <td>Net Amount</td>                                              
                    <td id="amount_td">
                        <input class="text_boxes_numeric" style="width:140px"  name="text_net_amount" id="text_net_amount" type="text" tabindex="8" /><!--onBlur="amount_distebute(this.value,document.getElementById('cbo_currency').value)"-->
                    </td>
                    <td>Adjustment Type</td>
                    <td id="adjustment_td">
						<?
							echo create_drop_down( "cbo_adjustment_type", 150, $adjustment_type,"", 1, "-- Select Instrument --", $selected, "load_unadjusted_amount(this.value);",1,"","","","",9);
                        ?> 
                    </td>
                </tr>
                <tr>
                	<td>Adjusted Amount</td>                                              
                    <td>
                        <input class="text_boxes_numeric" style="width:140px"  name="text_adjust_amount" id="text_adjust_amount" type="text" tabindex="10" onBlur="unadjusted_amount_chack(this.value);" disabled />
                    </td>

                    <td>Bank Name</td>                                              
                    <td id="bank_td">
                        <input class="text_boxes" style="width:140px"  name="text_bank_name" id="text_bank_name" type="text" tabindex="11" disabled />
                    </td>
                    <td>Instrument Date</td>                                              
                    <td>
                        <input class="datepicker" type="text" style="width:140px" name="text_instrument_date " id="text_instrument_date" tabindex="12" disabled />
                    </td>
                </tr>
                <tr>
                	<td>Instrument No</td>                                              
                    <td id="instrument_td">
                        <input class="text_boxes" style="width:140px"  name="text_instrument_no" id="text_instrument_no" type="text" tabindex="13" disabled />
                    </td>
                    <td  class="must_entry_caption">Clearance Method</td>                                              
                    <td>
						<?
							//$clearance_method=array(1=>"First Come First Adjust",2=>"Manual Adjustment");
							echo create_drop_down( "cbo_clearance_method", 150, $clearance_method,"", 1, "--Select Clearance--", $selected, "amount_distebute(this.value,document.getElementById('cbo_currency').value,document.getElementById('cbo_instrument').value)",'',"","","","",14);
                        ?> 
                    </td>
                    <td>Advance Rec.</td>                                              
                    <td>
                    	<input class="text_boxes_numeric" style="width:140px"  name="text_advance_amount" id="text_advance_amount" type="text" />
                    </td>
                </tr>
                <tr>
                    <td>Remarks</td>                                              
                    <td colspan="3">
						 <input class="text_boxes" style="width:405px"  name="text_remarks" id="text_remarks" type="text" tabindex="14" />
                    </td>
                    <td>Unadjusted Advance</td>                                              
                    <td>
                    	<input class="text_boxes_numeric" style="width:140px"  name="text_unadj_advance" id="text_unadj_advance" placeholder="Display" type="text" readonly />
                    </td>
                </tr>
            </table>
            <br>
            <div id="payment_receive_list_view"></div>
            <table width="840px">
            	<tr>
                    <td colspan="10" height="15" align="center"><div id="accounting_integration_div" style="float:center; font-size:18px; color:#FF0000;"></div></td>
                </tr> 
                <tr>
                    <td colspan="10" align="center" class="button_container">
						<? 
                        	echo load_submit_buttons($permission,"fnc_payment_receive",0,1,"reset_form('paymentreceive_1','payment_receive_list_view','','','disable_enable_fields(\'cbo_company_id*cbo_bill_type*cbo_payment_type\',0)');",1);
                        ?>
                    </td>
                </tr>  
            </table>
        </fieldset> 
    </form>
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
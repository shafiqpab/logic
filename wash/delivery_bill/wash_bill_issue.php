<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Wash Bill Issue 
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	31-03-2019
Updated by 		: 		
Update date		: 
Oracle Convert 	:		
Convert date	: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//-----------------------------------------------------------------------------------------------------------------

echo load_html_head_contents("Wash Bill Issue","../../", 1, 1, $unicode,'','');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	
function chk_qty_level_variabe(company)
{
   var status = return_global_ajax_value(company, 'chk_qty_level_variable', '', 'requires/wash_bill_issue_controller').trim();
   status = status.split("**");
   $('#txt_variable_status').val(status[0]);
}

	
	function fnc_load_party(type,within_group)
	{
		if(form_validation('cbo_company_name','Company')==false )
		{
			$('#cbo_within_group').val(1);
			return;
		}
		var company = $('#cbo_company_name').val();
		var party_name = $('#cbo_party_name').val();
		
		if(within_group==1 && type==1)
		{
			load_drop_down( 'requires/wash_bill_issue_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
		}
		else if(within_group==2 && type==1)
		{
			load_drop_down( 'requires/wash_bill_issue_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td' );
		}
		if(within_group==1)
		{
			load_drop_down( 'requires/wash_bill_issue_controller', party_name, 'load_drop_down_party_location', 'partylocation_td' );
		}
		//fnc_party_location(within_group);
	}
	
	function fnc_job_no()
	{
		if ( form_validation('cbo_company_name*cbo_party_name*txt_exchange_rate','Company Name*Party Name*exchange rate')==false )
		{
			return;
		}
		else
		{
			var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value+"_"+document.getElementById('txt_variable_status').value;
			var title = 'Order Search'
			var page_link='requires/wash_bill_issue_controller.php?action=job_popup&data='+data
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				freeze_window(5);
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("selected_order").value;
				var selected_multi_delevery_id=this.contentDoc.getElementById("txt_selected_id").value;
				var ex_data=theemail.split('***')
				
				$("#txtJob_no").val( ex_data[0] );
				$("#txt_wo_no").val( ex_data[1] );
				$("#txtStyleRef").val( ex_data[2] );
				$("#txtBuyerName").val( ex_data[3] );
				var exchange_rate = $('#txt_exchange_rate').val();
				
				if(ex_data[5]==6){var search_type=ex_data[5];}else{var search_type=0;}
				if(ex_data[6]!=''){var delivery_id=ex_data[6];}else{var delivery_id=0;}
				//$('#txt_delivery_id').val(delivery_id);
				if(delivery_id!="")
				{
				 	$('#txt_delivery_id').val(delivery_id);
				}
				
				var bill_variable_status=ex_data[9]*1;
				if(bill_variable_status==2)
				{
					var job_wise_delivery_id=selected_multi_delevery_id;
				}
				
				//alert(job_wise_delivery_id);
				var list_view_orders = return_global_ajax_value( 0+'**'+ex_data[0]+'**'+1+'**'+exchange_rate+'**'+search_type+'**'+delivery_id+'**'+document.getElementById('cbo_company_name').value+'**'+job_wise_delivery_id, 'load_php_dtls_form', '', 'requires/wash_bill_issue_controller');
				
				//alert(list_view_orders);
				if(list_view_orders!='')
				{
					$("#dtls_tbody tr").remove();
					$("#dtls_tbody").append(list_view_orders);
					fnc_total_calculate();
				}
				else
				{
					
					$("#dtls_tbody tr").remove();
				}
				$('#cbo_company_name').attr('disabled','disabled');
				$('#cbo_within_group').attr('disabled','disabled');
				$('#cbo_party_name').attr('disabled','disabled');
				$('#cbo_currency').attr('disabled','disabled');
				// $('#txt_exchange_rate').attr('disabled','disabled');
				release_freezing();
			}
		}
	}
	
	function fnc_embl_bill_issue( operation )
	{
		
		
		 
		
		 var variable_status = $('#txt_variable_status').val();
		 var tot_row1=$('#dtls_tbody tr').length;
		
		
		if(variable_status==2)
		{
			var messageData=''; var messageData1=''; var messageData2='';
			
			    var check_idarr=new Array();
				var totalOrderQuantity=0;
				var prevCumbillqty=0; totalbillqty=0;
			
			
			for (var jj=1; jj<=tot_row1; jj++)
			{
				var txtpoid = $('#txtpoid_'+jj).val();//$('#txtpoid_'+k).val();
				if(check_idarr[txtpoid]!=txtpoid)
				{
					 check_idarr[txtpoid]=txtpoid;
					 totalOrderQuantity += $('#txtOrderQuantity_'+jj).html()*1;
					 prevCumbillqty += $('#txtdeliveryqty_'+jj).attr('title')*1;
				}
				totalbillqty+=$('#txtbillqty_'+jj).val()*1;
				var balnceQty=totalOrderQuantity-prevCumbillqty;
				
				
				//var balnceQty=txtOrderQuantity-CumBillQty;
				//alert(balnceQty);
				
				/*if(totalbillqty>balnceQty)
				{
					alert("Bill Qty Over Order Balance");
					$('#txtbillqty_'+jj).val('');
					return;
				}*/
				
				
				
				
				
				    /*var tot_row=$('#dtls_tbody tr').length;
					var check_idarr=new Array();
					var totalOrderQuantity=0;
					var prevCumbillqty=0; totalbillqty=0;
					for(var k=1; k<=tot_row; k++)
					{ 
						var txtpoid = $('#txtpoid_'+k).val();//$('#txtpoid_'+k).val();
						if(check_idarr[txtpoid]!=txtpoid)
						{
							 check_idarr[txtpoid]=txtpoid;
							 totalOrderQuantity += $('#txtOrderQuantity_'+k).html()*1;
							 prevCumbillqty += $('#txtdeliveryqty_'+k).attr('title')*1;
						}
						totalbillqty+=$('#txtbillqty_'+k).val()*1;
					}
				var balnceQty=totalOrderQuantity-prevCumbillqty;
				//var balnceQty=txtOrderQuantity-CumBillQty;
				if(totalbillqty>balnceQty)
				{
					alert("Bill Qty Over Order Balance");
					$('#txtbillqty_'+jj).val('');
					return;
				}
				*/
				
				
				
					var txtOrderQuantity = $('#txtOrderQuantity_'+jj).html()*1;
					var txtdeliveryqty 	= $('#txtdeliveryqty_'+jj).attr('title')*1;
					var hiddenbilqty 		= $('#hiddenbilqty_'+jj).attr('title')*1;
					var totaldeleveryidwiseQuantity		= $('#txtdeleveryidwiseQuantity_'+jj).attr('title')*1;
					var prvTotalBillQuantity		= $('#txtTotalBillQuantity_'+jj).attr('title')*1;
					
					var deleveryidwise_balnceQty=totaldeleveryidwiseQuantity-prvTotalBillQuantity;
					
					var CumBill=(txtOrderQuantity-txtdeliveryqty);
					var billQuantity = $('#txtbillqty_'+jj).val()*1;
					
					//alert(txtdeleveryidwiseQuantity);
					//alert(txtTotalBillQuantity);
					if(balnceQty<totalbillqty)
					{
						messageData= 'Bill Qty : ' +totalbillqty + '  Over Order Balance  : '+balnceQty+"; \n";
						$('#txtbillqty_'+jj).focus();
					}
					
					/*if(hiddenbilqty<billQuantity)
					{
						messageData1+= 'Bill Qty : ' +billQuantity + '  Should Not More The Delivery Qty  : '+hiddenbilqty+"; \n";
						$('#txtbillqty_'+jj).focus();
					}*/
					
					if(deleveryidwise_balnceQty<billQuantity)
					{
						messageData1+= 'Bill Qty : ' +billQuantity + '  Should Not More The Delivery Qty  : '+deleveryidwise_balnceQty+"; \n";
						$('#txtbillqty_'+jj).focus();
					}
					
			}
			
			if(messageData!=''){alert(messageData);return;}
			if(messageData1!=''){alert(messageData1);return;}
			
		}
		if(operation==2)
		{
			alert("Delete Restricted.");
			return;
		}
		if ( form_validation('cbo_company_name*cbo_location_name*cbo_party_name*txt_bill_date*txtJob_no*cbo_currency*txt_exchange_rate', 'Company Name*Location*Party*Bill Date*Job No*currency*exchange rate')==false )
		{
			return;
		}
		else
		{
			if(operation==4)
			{
				if ( $('#txt_delv_no').val()=='')
				{
					alert ('Bill ID Not Save.');
					return;
				}
				var report_title=$( "div.form_caption" ).html(); 
				print_report( $('#cbo_company_name').val()+'*'+$('#txt_update_id').val()+'*'+report_title+'*'+$('#txtJob_no').val()+'*'+$('#cbo_template_id').val(), "embl_bill_issue_print", "requires/wash_bill_issue_controller") 
				//return;
				show_msg("3");
			}
			else
			{
				var data_str="";
				 
				var data_str=get_submitted_data_string('txt_bill_no*cbo_company_name*cbo_location_name*cbo_floor_name*cbo_within_group*cbo_party_name*cbo_party_location*txt_bill_date*txt_remarks*txtJob_no*txt_update_id*is_posted_account*txt_exchange_rate*cbo_currency*txt_delivery_id*txt_variable_status*txt_upcharge*txt_discount*txt_net_Amount*txt_up_remarks*txt_discount_remarks',"../../");
				var tot_row=$('#dtls_tbody tr').length;
				 var k=0;
				 
				for (var i=1; i<=tot_row; i++)
				{
					var rate=$('#txtbillrate_'+i).val();
					var billqty=$('#txtbillqty_'+i).val();
					
					var totaldeleveryidwiseQuantity		= $('#txtdeleveryidwiseQuantity_'+i).attr('title');
					var prvTotalBillQuantity		= $('#txtTotalBillQuantity_'+i).attr('title');
					
					if(billqty*1>0 && rate*1>0)
					{
						k++;
						data_str+="&txtbuyerPoId_" + k + "='" + $('#txtbuyerPoId_'+i).val()+"'"+"&txtdeliveryid_" + k + "='" + $('#txtdeliveryid_'+i).val()+"'"+"&sysnotd_" + k + "='" + $('#sysnotd_'+i).text()+"'"+"&deliverydatetd_" + k + "='" + $('#deliverydatetd_'+i).text()+"'"+"&txtbillqty_" + k + "='" + $('#txtbillqty_'+i).val()+"'"+"&txtbillrate_" + k + "='" + $('#txtbillrate_'+i).val()+"'"+"&txtbillamount_" + k + "='" + $('#txtbillamount_'+i).val()+"'"+"&txtRemarks_" + k + "='" + $('#txtRemarks_'+i).val()+"'"+"&txtpoid_" + k + "='" + $('#txtpoid_'+i).val()+"'"+"&txtColorSizeid_" + k + "='" + $('#txtColorSizeid_'+i).val()+"'"+"&txtdomisticamount_" + k + "='" + $('#txtdomisticamount_'+i).val()+"'"+"&txtDtlsUpdateId_" + k + "='" + $('#txtDtlsUpdateId_'+i).val()+"'"+"&totaldeleveryidwiseQuantity" + k + "='" + totaldeleveryidwiseQuantity+"'";
					}
				}
				if(k==0)
				{
					alert("Please input Current Rate.");
					return;
				}
				var data="action=save_update_delete&operation="+operation+'&total_row='+k+data_str;//+'&zero_val='+zero_val
				//alert (data); return;
				freeze_window(operation);
				http.open("POST","requires/wash_bill_issue_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_embl_bill_issue_response;
			}
		}
	}
	
	function fnc_embl_bill_issue_response()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
			
			/*if(trim(response[0])=='emblIssue')
			{
				alert("Issue Found :"+trim(response[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}*/
			show_msg(response[0]);
			if(response[0]*1==14*1)
			{
				release_freezing();
				alert(response[1]);
				return;
			}
			
			if(response[0]==0 || response[0]==1)
			{
				document.getElementById('txt_update_id').value= response[1];
				document.getElementById('txt_bill_no').value = response[2];
				document.getElementById('txt_delivery_id').value = response[4];
				var exchange_rate = response[3];
				var delivery_id = response[4];
				if(delivery_id!="")
				{
					var search_type=6;
				}
				
				//alert(delivery_id); return;
					
				var list_view_orders = return_global_ajax_value( response[1]+'**'+$('#txtJob_no').val()+'**'+2+'**'+exchange_rate+'**'+search_type+'**'+delivery_id+'**'+response[5], 'load_php_dtls_form', '', 'requires/wash_bill_issue_controller');
				if(list_view_orders!='')
				{
					$("#dtls_tbody tr").remove();
					$("#dtls_tbody").append(list_view_orders);
					fnc_total_calculate();
					$('#txtJob_no').attr('disabled','disabled');
					set_button_status(1, permission, 'fnc_embl_bill_issue',1);
				}
			}
			if(response[0]==2)
			{
				location.reload(); 
			}
			release_freezing();
		}
	}
	
/*function fnc_total_calculate()
{
	var rowCount = $('#dtls_tbody tr').length-1;
	//alert(rowCount);
	math_operation( "txtTotbillqty", "txtbillqty_", "+", rowCount );
	math_operation( "txtTotbillamount", "txtbillamount_", "+", rowCount );
	math_operation( "txtTotdomisticamount", "txtdomisticamount_", "+", rowCount );
} */	
function calculate_amount(i)
{
	//alert(i);
	// return;
	var txtOrderQuantity 		= $('#txtOrderQuantity_'+i).html()*1;
	var txtdeliveryqty 		    = $('#txtdeliveryqty_'+i).html()*1;
	var hiddenbilqty 		     = $('#hiddenbilqty_'+i).attr('title')*1;
	//alert(hiddenbilqty);
	var txtbillqty=$("#txtbillqty_"+i).val()*1;
	
	
	var balance=txtOrderQuantity-txtdeliveryqty;
	var txtInitialRate=$("#txtInitialRate_"+i).val()*1;
	var txtbillrate=$("#txtbillrate_"+i).val()*1;
	var exchange_rate=$("#txt_exchange_rate").val()*1;	
	//var order_domistic_amount=order_amount*converstion_factor;
	var amount=txtbillqty*txtbillrate;
	var domisticamount=amount*exchange_rate;
	$("#txtbillamount_"+i).val( number_format (amount, 4,'.' , ""));
	$("#txtdomisticamount_"+i).val( number_format (domisticamount, 4,'.' , ""));
	fnc_total_calculate();
	
	
	
	
	var breakdownid = $('#txtpoid_'+i).val();
	var Curbillqty=$('#txtbillqty_'+i).val();
	var CumBillQty=$('#txtdeliveryqty_'+i).html()*1;
	var tot_row=$('#dtls_tbody tr').length;
	
	//alert(tot_row);
	
	var totalbillqty=0; var balnceQty=0;//var CumBillQty=0;
	
	//var ActualQtyArr=new Array();
	var check_idarr=new Array();
	var totalOrderQuantity=0;
	var prevCumbillqty=0;
	
	for(var k=1; k<=tot_row; k++)
	{ 
	    var txtpoid = $('#txtpoid_'+k).val();
		if(check_idarr[txtpoid]!=txtpoid)
		{
	  		 check_idarr[txtpoid]=txtpoid;
			 totalOrderQuantity += $('#txtOrderQuantity_'+k).html()*1;
			 prevCumbillqty += $('#txtdeliveryqty_'+k).attr('title')*1;
		}
		totalbillqty+=$('#txtbillqty_'+k).val()*1;
	}
	
	//alert(totalOrderQuantity);
	//alert(prevCumbillqty);
	//alert(totalbillqty);
	
	
	//alert(totalOrderQuantity);return;
	
	
	
	/*for(var j=1; j<tot_row; j++)
	{ 
	   var breakdownidCur = $('#txtColorSizeid_'+j).val();
	    if(breakdownid==breakdownidCur)
		{ 
			totalbillqty+=$('#txtbillqty_'+j).val()*1;
			//CumBillQty+=$('#txtdeliveryqty_'+j).html()*1;
		}
	}*/
	//var balnceQty=txtOrderQuantity-CumBillQty;
	var balnceQty=totalOrderQuantity-prevCumbillqty;
	
	//alert(totalbillqty);
	//alert(txtOrderQuantity);
	//alert(totalbillqty);
	
	
	/*if(totalbillqty>balnceQty)
	{
		alert("Bill Qty Over Order Balance");
		$('#txtbillqty_'+i).val('');
		return;
	}*/
	
	
	
	
	/*var txtpoid=$("#txtpoid_"+i).val()*1;
	var txtColorSizeid=$("#txtColorSizeid_"+i).val()*1;
	var description=txtpoid+"**"+txtColorSizeid;	
	var tot_row=$('#dtls_tbody tr').length;
	//alert(tot_row);
	var ActualQtyArr=new Array();
	for(var j=1; j<tot_row; j++)
	{ 
		var txtpoidCur=$("#txtpoid_"+j).val()*1;
		var txtColorSizeidCur=$("#txtColorSizeid_"+j).val()*1;
		var NewDescription=txtpoidCur+"**"+txtColorSizeidCur;	
		var Curbillqty=$('#txtbillqty_'+j).val();
		if(description==NewDescription)
		{ 
			ActualQtyArr.push(parseInt(Curbillqty));
		}
	}
	var sum = 0;
	for (var k = 0; k < ActualQtyArr.length; k++) 
	{
		sum += ActualQtyArr[k]
	}
	if(sum>balance)
	{
		alert("Bill Qty Over Order Balance");
		//$('#NoofBag_'+i).val('');
		$('#txtbillqty_'+i).val('');
		return;
	}*/
		
		
	
	/*if(hiddenbilqty<txtbillqty)
	{
		alert("Bill Qty Should Not More The Delivery Qty ");
		$("#txtbillqty_"+i).val('') ;
		return;
	}
	
	if(balance<txtbillqty)
	{
		alert("Bill Qty Should Not More The Order Qty ");
		$("#txtbillqty_"+i).val('') ;
		return;
	}
	else
	{
		//$('#update1').removeAttr("disabled");	
		$("#txtbillamount_"+i).val( number_format (amount, 2,'.' , ""));
		$("#txtdomisticamount_"+i).val( number_format (domisticamount, 2,'.' , ""));
	}*/
	
}
	
	
	function calculate_total_amount()
	{
		var txt_total_amount=$('#txtTotbillamount').val()*1;
		var txt_upcharge=$('#txt_upcharge').val();
		var txt_discount=$('#txt_discount').val();
		if(txt_total_amount>0)
		{
			var net_tot_amnt=txt_total_amount+txt_upcharge*1-txt_discount*1;
			$('#txt_net_Amount').val(net_tot_amnt.toFixed(4));
		}
	}
	
	function openmypage_bill_no()
	{ 
		if(form_validation('cbo_company_name', 'Company Name')==false )
		{
			return;
		}
		else
		{
			var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
			var page_link='requires/wash_bill_issue_controller.php?action=bill_popup&data='+data;
			var title="Bill Popup";	
			
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=900px, height=400px, center=1, resize=0, scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_subcontract_frm"); //Access the form inside the modal window
				//var theemail=this.contentDoc.getElementById("selected_job");
				var theemail=this.contentDoc.getElementById("selected_job").value;
				//alert (theemail); 
				
				var bill_data=theemail.split("***");
				if (bill_data[0]!="")
				{
					freeze_window(5);
					reset_form('','','txt_bill_no*cbo_location_name*cbo_within_group*cbo_party_name*cbo_party_location*txt_bill_date*txt_remarks*txtJob_no*txt_update_id','','');
					$('#txt_update_id').val(bill_data[0]);
					$('#txt_bill_no').val(bill_data[1]);
					$('#cbo_location_name').val(bill_data[2]);
					load_drop_down( 'requires/wash_bill_issue_controller', bill_data[2]+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_floor', 'floor_td');
					$('#cbo_floor_name').val(bill_data[17]);
					$('#cbo_within_group').val(bill_data[3]);
					$('#cbo_party_name').val(bill_data[4]);
					fnc_load_party(2,document.getElementById('cbo_within_group').value);
					$('#cbo_party_location').val(bill_data[5]);
					$('#txt_bill_date').val(bill_data[6]);
					$('#txt_remarks').val(bill_data[7]);
					
					$('#txtJob_no').val(bill_data[8]);
					$('#txt_wo_no').val(bill_data[9]);
					$('#txtStyleRef').val(bill_data[10]);
					$('#txtBuyerName').val(bill_data[12]);
					$('#is_posted_account').val(bill_data[13]);
					 if (bill_data[13] == 1) document.getElementById("accounting_posted_status").innerHTML = "Already Posted In Accounting."; 
					 else 
					 document.getElementById("accounting_posted_status").innerHTML = "";
					
					$('#cbo_currency').val(bill_data[14]);
					$('#txt_exchange_rate').val(bill_data[15]);
					$('#txt_delivery_id').val(bill_data[16]);
					
					$('#txt_upcharge').val(bill_data[18]);
					$('#txt_discount').val(bill_data[19]);
					$('#txt_net_Amount').val(bill_data[20]);
					$('#txt_up_remarks').val(bill_data[21]);
					$('#txt_discount_remarks').val(bill_data[22]);
					
					
						//a.upcharge, a.discount, a.net_bill_amount, a.upcharge_remarks, a.discount_remarks
					$('#cbo_currency').attr('disabled','disabled');
				    // $('#txt_exchange_rate').attr('disabled','disabled');
					
					
					$('#cbo_company_name').attr('disabled','disabled');
					$('#cbo_within_group').attr('disabled','disabled');
					$('#cbo_party_name').attr('disabled','disabled');
					$('#txtJob_no').attr('disabled','disabled');
					
					//get_php_form_data( splt_val[0], "load_php_data_to_form", "requires/wash_bill_issue_controller" );
					
					var exchange_rate = bill_data[15];
					var delivery_id =bill_data[16];
					if(delivery_id!="")
					{
						var search_type=6;
					}
					
					var list_view_orders = return_global_ajax_value( bill_data[0]+'**'+bill_data[8]+'**'+1+'**'+exchange_rate+'**'+search_type+'**'+delivery_id+'**'+document.getElementById('cbo_company_name').value, 'load_php_dtls_form', '', 'requires/wash_bill_issue_controller');
					if(list_view_orders!='')
					{
						$("#dtls_tbody tr").remove();
						$("#dtls_tbody").append(list_view_orders);
					}
					fnc_total_calculate();
					set_button_status(1, permission, 'fnc_embl_bill_issue',1);
					release_freezing();
				}
			}
		}
	}
	
	function fnc_amount_calculation(val,inc)
	{
		var qty=$("#txtbillqty_"+inc).val()*1;
		var exchange_rate=$("#txt_exchange_rate").val()*1;
		var currency=$("#cbo_currency").val()*1;
		var amount=qty*val;
		var domisticamount=amount*exchange_rate;
		$("#txtbillamount_"+inc).val( number_format(amount,4,'.','' ));
		$("#txtdomisticamount_"+inc).val( number_format(domisticamount,4,'.','' ));
	}

		function fnc_domestic_calculation(exchangeRate) 
		{
			var rowCount = $('#dtls_tbody tr').length;
			exchangeRate=exchangeRate*1;
	
			for (var i = 0; i <= rowCount; i++) {
				var qty=$("#txtbillqty_"+i).val()*1;
				var billRate=$("#txtbillrate_"+i).val()*1;
				var amount=qty*billRate;
				var domisticamount=amount*exchangeRate;
				$("#txtbillamount_"+i).val( number_format(amount,4,'.','' ));
				$("#txtdomisticamount_"+i).val( number_format(domisticamount,4,'.','' ));
			}
		}
	
	function fnc_party_location(val)
	{
		if(val==1) $('#cbo_party_location').removeAttr('disabled','disabled');
		else $('#cbo_party_location').attr('disabled','disabled');
	}
	
	function fnc_total_calculate()
	{
		/*var rowCount = $('#dtls_tbody tr').length;
		//alert(rowCount)
		math_operation( "txtTotbillqty", "txtbillqty_", "+", rowCount );
		math_operation( "txtTotbillamount", "txtbillamount_", "+", rowCount );
		math_operation( "txtTotdomisticamount", "txtdomisticamount_", "+", rowCount );*/
		
		var ddd={ dec_type:5, comma:0, currency:''}
 		//var rowCount = $('#dtls_tbody tr').length-1;
		var rowCount = $('#dtls_tbody tr').length;
	
		//alert(rowCount);
		
		math_operation( "txtTotbillqty", "txtbillqty_", "+", rowCount,ddd );
		math_operation( "txtTotbillamount", "txtbillamount_", "+", rowCount,ddd );
		math_operation( "txtTotdomisticamount", "txtdomisticamount_", "+", rowCount,ddd );
		
		calculate_total_amount();
	} 
	
	function exchange_rate(val)
	{
		if(form_validation('cbo_company_name*txt_bill_date', 'Company Name*Bill Date')==false )
		{
			$("#cbo_currency").val(0);
			return;
		}
		
		if(val==0)
		{
			$('#txt_bill_date').removeAttr('disabled','disabled');
			$('#cbo_company_name').removeAttr('disabled','disabled');
			$("#txt_exchange_rate").val("");
		}
		else if(val==1)
		{
			$("#txt_exchange_rate").val(1);
			$('#txt_bill_date').attr('disabled','disabled');
			$('#cbo_company_name').attr('disabled','disabled');
			// $('#txt_exchange_rate').attr('disabled','disabled');
		}
		else
		{
			var bill_date = $('#txt_bill_date').val();
			var company_name = $('#cbo_company_name').val();
			var response=return_global_ajax_value( val+"**"+bill_date+"**"+company_name, 'check_conversion_rate', '', 'requires/wash_bill_issue_controller');
			$('#txt_exchange_rate').val(response);
			$('#txt_bill_date').attr('disabled','disabled');
			$('#cbo_company_name').attr('disabled','disabled');
			// $('#txt_exchange_rate').attr('disabled','disabled');
		}
	}
	
</script>

</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission); ?>
        <form name="embbillissue_1" id="embbillissue_1" autocomplete="off"> 
			<fieldset style="width:1100px;">
			<legend>Wash Bill Issue</legend>
                <table width="990" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                    <tr>
                        <td colspan="4" align="right"><strong>Bill ID
                          <input class="text_boxes"  type="text" name="txt_bill_no" id="txt_bill_no" onDblClick="openmypage_bill_no();" placeholder="Double Click" style="width:140px;" readonly />
                       </strong></td>
                        <td colspan="4"><input type="hidden" name="txt_update_id" id="txt_update_id" style="width:90px" class="text_boxes" value="" />
                        <input type="hidden" id="is_posted_account" name="is_posted_account" value=""/>
                        <input type="hidden" id="txt_variable_status" name="txt_variable_status" value="" />
                        </td>
                    </tr>
                    <tr>
                        <td width="100" class="must_entry_caption">Company</td>
                        <td width="160"><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/wash_bill_issue_controller', this.value, 'load_drop_down_location', 'location_td');fnc_load_party(1,document.getElementById('cbo_within_group').value);get_php_form_data( this.value, 'company_wise_report_button_setting','requires/wash_bill_issue_controller');chk_qty_level_variabe(this.value);"); ?>
                        </td>
                        <td width="100" class="must_entry_caption">Location</td>
                        <td width="160" id="location_td"><? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "" ); ?></td>
                        <td width="100" >Floor/Unit</td>
						<td width="150" id="floor_td"><? echo create_drop_down( "cbo_floor_name", 150, $blank_array,"", 1, "-- Select Floor --", $selected, "" ); ?></td>
                        <td width="80" class="must_entry_caption">Within Group</td>
                        <td width="130"><?php echo create_drop_down( "cbo_within_group", 120, $yes_no,"", 0, "--  --", 0, "fnc_load_party(1,this.value); fnc_party_location(this.value);" ); ?></td>

                        

                    </tr>
                    <tr>
                    	<td width="100" class="must_entry_caption">Party</td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $selected, "fnc_load_party(2,document.getElementById('cbo_within_group').value);"); ?></td>

                    	<td>Party Location</td>
                        <td id="partylocation_td"><? echo create_drop_down( "cbo_party_location", 150, $blank_array,"", 1, "--Party Location--", $selected, "" ); ?></td>
                    	<td class="must_entry_caption">Bill Date</td>
                        <td><input type="text" name="txt_bill_date" id="txt_bill_date" style="width:135px" class="datepicker" value="" /></td>
                        <td class="must_entry_caption">Currency</td>                                              
                        <td id="currency_td">
							<?
                            echo create_drop_down("cbo_currency", 120, $currency,"", 1, "-- Select Currency --",$selected,"exchange_rate(this.value)", "","","","","",7 ); 
                            ?>
                        </td>
                        
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">Exchange Rate</td>
               			 <td><input type="text" name="txt_exchange_rate" id="txt_exchange_rate" style="width:140px" class="text_boxes_numeric" onBlur="fnc_domestic_calculation(this.value); fnc_total_calculate();" /></td>
                        <td>Remarks</td>
                        <td colspan="5"><input type="text" name="txt_remarks" id="txt_remarks"  class="text_boxes" style="width:610px;" /></td>
                    </tr>
                    <tr style="display:none">
                        <td class="must_entry_caption">Delivery Date</td>                                              
                        <td>
                            <input class="datepicker" type="text" style="width:55px" name="txt_bill_form_date" id="txt_bill_form_date" placeholder="From Date" disabled />&nbsp;
                            <input class="datepicker" type="text" style="width:55px" name="txt_bill_to_date" id="txt_bill_to_date" placeholder="To Date" disabled />
                        </td>
                        <td>Sys. No</td>                                              
                        <td>
                            <input class="text_boxes" type="text" style="width:135px" name="txt_manual_challan" id="txt_manual_challan" disabled />
                        </td>
                        <td class="must_entry_caption">&nbsp;</td>                                              
                        <td>
                            <input class="formbutton" type="button" onClick="fnc_list_search(0);" style="width:130px" name="btn_populate" value="Populate" id="btn_populate" />
                        </td>
                    </tr> 
                </table>
            </fieldset>
            <br>
            <fieldset style="width:1100px;">
            <legend>Wash Bill Details</legend>
                <table style="width:1100px;" cellpadding="0" cellspacing="2" border="1" class="rpt_table" rules="all">
                    <thead class="form_table_header">
                    	<tr>
	                		<th colspan="4" class="must_entry_caption">Job No &nbsp;&nbsp;&nbsp;
	                			<input type="text" name="txtJob_no" id="txtJob_no" value="" class="text_boxes"  style="width:120px" placeholder="Browse" onDblClick="fnc_job_no();" readonly/>
                                <input type="hidden" name="txt_order_id" id="txt_order_id" class="text_boxes" style="width:70px;" />
                                <input type="hidden" name="txt_delivery_id" id="txt_delivery_id" style="width:90px" class="text_boxes" value="" />
                            </th>
 	                		<th>Work Order No</th>
	                		<th colspan="2"><input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:120px;" placeholder="Display" readonly /></th>
                            <th style="display:none">Buyer Style Ref.</th>
	                		<th colspan="2" style="display:none"><input type="text" name="txtStyleRef" id="txtStyleRef" value="" class="text_boxes"  style="width:110px" placeholder="Display" readonly/></th>
                            <th>Buyer</th>
                            <th><input type="text" name="txtBuyerName" id="txtBuyerName" value="" class="text_boxes"  style="width:90px" placeholder="Display" readonly/></th>
                            <th></th>
	                		<th colspan="5"></th>
                	    </tr>
                	    <tr>
	                        <th width="30">SL</th>
                            <th width="60">Delivery ID</th>
                            <th width="60">Delivery Date</th>
                            <th width="120">Buyer PO</th>
                            <th  width="120">Buyer Style Ref.</th>
	                        <th width="100">Gmts Item</th>
	                        <th width="100">Process Name</th>
	                        <th width="110">Wash Type</th>
	                        <th width="100">Color</th>
                            <th width="80">Order Qty</th>
                            <th width="60">Qty</th>
                            <th width="60" class="must_entry_caption">Rate</th>
                            <th width="60">Amount</th>
                            <th width="100">Domestic Amount</th>
	                        <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="dtls_tbody">
                    	<tr bgcolor="#FFFFFF">
                    		<td align="center">1<input name="txtbuyerPoId_1" id="txtbuyerPoId_1" type="hidden" class="text_boxes" style="width:70px" value="" /></td>
                            <td>&nbsp;</td>
                    		<td>&nbsp;</td>
                            <td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                            <td>&nbsp;</td>
                    		<td align="center"><input type="text" name="txtbillqty_1" id="txtbillqty_1" class="text_boxes_numeric" style="width:50px;" disabled /></td>
                            <td align="center"><input type="text" name="txtbillrate_1" id="txtbillrate_1" class="text_boxes_numeric" style="width:50px;" onBlur="fnc_amount_calculation(this.value,1); fnc_total_calculate();" /></td>
                            <td align="center"><input type="text" name="txtbillamount_1" id="txtbillamount_1" class="text_boxes_numeric" style="width:50px;" readonly /></td>
                            <td align="center"><input type="text" name="txtdomisticamount_1" id="txtdomisticamount_1" class="text_boxes_numeric" style="width:90px;" readonly /></td>
                    		<td align="center"><input type="text" name="txtRemarks_1" id="txtRemarks_1" class="text_boxes" style="width:80px;" />
                            	<input type="hidden" name="txtDtlsUpdateId_1" id="txtDtlsUpdateId_1" style="width:50px" class="text_boxes" value="" />
                                <input type="hidden" name="txtColorSizeid_1" id="txtColorSizeid_1" style="width:50px" class="text_boxes" value="" />
                                <input type="hidden" name="txtpoid_1" id="txtpoid_1" style="width:50px" class="text_boxes" value="" />
                            </td> 
                    	</tr>
                    </tbody> 
                    <tfoot>
                    	<tr class="tbl_bottom" name="tr_btm" id="tr_btm">
                    		<td align="center">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                            <td>&nbsp;</td>
                    		<td>Total:</td>
                    		<td align="center"><input type="text" name="txtTotbillqty" id="txtTotbillqty" class="text_boxes_numeric" style="width:50px;" readonly /></td>
                            <td>&nbsp;</td>
                            <td align="center"><input type="text" name="txtTotbillamount" id="txtTotbillamount" class="text_boxes_numeric" style="width:55px;" readonly /></td>
                            <td align="center"><input type="text" name="txtTotdomisticamount" id="txtTotdomisticamount" class="text_boxes_numeric" style="width:90px;" readonly /></td>
                    		<td>&nbsp;</td> 
                    	</tr>
                        <tr class="tbl_bottom" >
							<td align="right" colspan="6">Upcharge Remarks:</td>
                            <td colspan="5" align="center"><input type="text" id="txt_up_remarks" name="txt_up_remarks" class="text_boxes" style="width:370px;" maxlength="100" placeholder="Maximum 100 Character" /></td>
                    		<td>Upcharge: </td>
                            <td align="center"><input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" style="width:55px;" onKeyUp="calculate_total_amount()" /></td> 
                            <td>&nbsp;</td> 
                            <td>&nbsp;</td> 
                    	</tr>
                    	<tr class="tbl_bottom" >
							<td align="right" colspan="6">Discount Remarks:</td>
                            <td colspan="5" align="center"><input type="text" id="txt_discount_remarks" name="txt_discount_remarks" class="text_boxes" style="width:370px;" maxlength="100" placeholder="Maximum 100 Character" /></td>
                    		<td>Discount: </td>
                            <td align="center"><input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" style="width:55px;" onKeyUp="calculate_total_amount()" /></td> 
                            <td>&nbsp;</td> 
                            <td>&nbsp;</td> 
                    	</tr>
                    	<tr class="tbl_bottom" >
                            <td colspan="11">&nbsp;</td>
                    		<td>Net Total: </td>
                            <td align="center"><input type="text" name="txt_net_Amount" id="txt_net_Amount" class="text_boxes_numeric" style="width:55px;" readonly /></td> 
                            <td>&nbsp;</td> 
                            <td>&nbsp;</td> 
                    	</tr>
                        <tr>
                        <td colspan="16" height="15" align="center"><div id="accounting_posted_status" style="float:center; font-size:18px; color:#FF0000;"></div></td>
                        </tr>
                    </tfoot>                   
                </table>            
                <table width="830" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td align="center" valign="middle" class="button_container">
                         <? echo create_drop_down( "cbo_template_id", 100, $report_template_list,'', 0, '', 0, "");?>&nbsp;
                        	<? echo load_submit_buttons($permission,"fnc_embl_bill_issue",0,0,"reset_form('embbillissue_1', '','','','')",1); ?>
                        	<input type="button" name="print" id="print" value="Print" onClick="fnc_embl_bill_issue(4)" style="width:100px;display:none;" class="formbuttonplasminus" />
                        </td>
                    </tr>   
                </table>
            </fieldset>          
        </form>                         
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
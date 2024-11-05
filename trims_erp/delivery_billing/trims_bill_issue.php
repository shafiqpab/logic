<?
/*--- ----------------------------------------- Comments
Purpose			: 						
Functionality	:	
JS Functions	:
Created by		:	K.M Nazim Uddin
Creation date 	: 	01-02-2018
Updated by 		: 		
Update date		:
Oracle Convert 	:		
Convert date	: 	   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start(); 
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Trims Order Receive Info", "../../", 1,1, $unicode,1,'');
$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][276] );
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	<? echo "var field_level_data= ". $data_arr . ";\n";?>
	
	function fnc_job_order_entry( operation )
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#cbo_within_group').val()+'*'+report_title+'*'+$('#txt_order_no').val()+'*'+$('#txt_bill_no').val()+'*'+$('#txt_bill_date').val(), "challan_print", "requires/trims_bill_issue_controller") 
			//return;
			show_msg("3");
		}
		else
		{
			
			if (operation==2) 
			{
			
				var show_item='';
				var r=confirm("Are you sure you want to delete");
				if (r==true)
				{
					show_item="1";
				}
				else
				{
					return;
				}
			}
			
			
			
			var delete_master_info=0; var i=0;
			var cbo_within_group = $("#cbo_within_group").val();
			if ( form_validation('cbo_company_name*cbo_location_name*cbo_within_group*cbo_party_name*txt_bill_date*cbo_currency','Company*location name*Within Group*Party*bill date*Bill Currency')==false )
				{
					return;
				}
			//var txt_dalivery_no 	= $('#txt_dalivery_no').val();
			var cbo_company_name 	= $('#cbo_company_name').val();
			var cbo_location_name 	= $('#cbo_location_name').val();
			var cbo_within_group 	= $('#cbo_within_group').val();
			var cbo_party_name 		= $('#cbo_party_name').val();
			var cbo_party_location 	= $('#cbo_party_location').val();
			var cbo_currency 		= $('#cbo_currency').val();
			var txt_challan_no 		= $('#txt_challan_no').val();
			var txt_bill_date 		= $('#txt_bill_date').val();
			var txt_order_no 		= $('#txt_order_no').val();
			var hid_order_id 		= $('#hid_order_id').val();
			var update_id 			= $('#update_id').val();
			var received_id 		= $('#received_id').val();
			var bill_no_manual 		= $('#txt_bill_no_manual').val();
			var txt_bill_no 		= $('#txt_bill_no').val();
			var cbo_Wo_Currency 	= $('#cbo_Wo_Currency').val();
			var txt_exchange_rate 	= $('#txt_exchange_rate').val();
			var txt_remarks 		= $('#txt_remarks').val();
			var txtBillAmount 		= $('#txtBillAmount').val();
			var txt_upcharge 		= $('#txt_upcharge').val();
			var txt_discount 		= $('#txt_discount').val();
			var txt_net_Amount 		= $('#txt_net_Amount').val();
			var txt_up_remarks 		= $('#txt_up_remarks').val();
			var txt_discount_remarks= $('#txt_discount_remarks').val();
			var j=0; var check_field=0; data_all="";
			$("#tbl_dtls_emb tbody tr").each(function()
			{
				var txtWorkOrder 		= $(this).find('input[name="txtWorkOrder[]"]').val();
				var txtWorkOrderID 		= $(this).find('input[name="txtWorkOrderID[]"]').val();
				var cboSection 			= $(this).find('select[name="cboSection[]"]').val();
				//var cboItemGroup 		= $(this).find('select[name="cboItemGroup[]"]').val();
				//var txtdescription 		= $(this).find('input[name="txtdescription[]"]').val();
				var txtdescription 		= encodeURIComponent($(this).find('input[name="txtdescription[]"]').val());
				var txtChallan 			= $(this).find('input[name="txtChallan[]"]').val();
				var txtgmtscolorId		= $(this).find('input[name="txtgmtscolorId[]"]').val();
				var txtgmtssizeId		= $(this).find('input[name="txtgmtssizeId[]"]').val();
				var txtcolorID 			= $(this).find('input[name="txtcolorID[]"]').val();
				var txtsizeID 			= $(this).find('input[name="txtsizeID[]"]').val();
				var cboUom 				= $(this).find('select[name="cboUom[]"]').val();
				var txtTotDelQuantity 	= $(this).find('input[name="txtTotDelQuantity[]"]').val();
				var txtPrevQty 			= $(this).find('input[name="txtPrevQty[]"]').val();
				var txtQty 				= $(this).find('input[name="txtQty[]"]').val();
				var txtWoRate 			= $(this).find('input[name="txtWoRate[]"]').val();
				var txtBillRate 		= $(this).find('input[name="txtBillRate[]"]').val();
				var txtBillAmount 		= $(this).find('input[name="txtBillAmount[]"]').val();

				var hdnDtlsUpdateId 	= $(this).find('input[name="hdnDtlsUpdateId[]"]').val();
				var hdnbookingDtlsId 	= $(this).find('input[name="hdnbookingDtlsId[]"]').val();
				var hdnReceiveDtlsId 	= $(this).find('input[name="hdnReceiveDtlsId[]"]').val();
				var hdnJobDtlsId 		= $(this).find('input[name="hdnJobDtlsId[]"]').val();
				var hdnProductionDtlsId = $(this).find('input[name="hdnProductionDtlsId[]"]').val();
				var hdnDeleveryDtlsId 	= $(this).find('input[name="hdnDeleveryDtlsId[]"]').val();
				var txtWorkOrderQuantity 	= $(this).find('input[name="txtWorkOrderQuantity[]"]').val();
				//txt_total_amount 		+= $(this).find('input[name="amount[]"]').val()*1;
				//alert(cboSection);
				j++;	
				if(txtQty==0 || txtQty=='' )
				{	 				
					alert('Please Fill up Current Bill Qty');
					check_field=1 ; return;
				}
				i++;
				data_all += "&txtWorkOrder_" + j + "='" + txtWorkOrder + "'&txtWorkOrderID_" + j + "='" + txtWorkOrderID + "'&cboSection_" + j + "='" + cboSection + "'&txtdescription_" + j + "='" + txtdescription + "'&txtChallan_" + j + "='" + txtChallan  + "'&txtgmtscolorId_" + j + "='" + txtgmtscolorId + "'&txtgmtssizeId_" + j + "='" + txtgmtssizeId + "'&txtcolorID_" + j + "='" + txtcolorID  + "'&txtsizeID_" + j + "='" + txtsizeID  + "'&cboUom_" + j + "='" + cboUom  + "'&txtTotDelQuantity_" + j + "='" + txtTotDelQuantity + "'&txtPrevQty_" + j + "='" + txtPrevQty + "'&txtQty_" + j + "='" + txtQty +"'&txtWoRate_" + j + "='" + txtWoRate +"'&txtBillRate_" + j + "='" + txtBillRate +"'&txtBillAmount_" + j + "='" + txtBillAmount +"'&hdnDtlsUpdateId_" + j + "='" + hdnDtlsUpdateId +"'&hdnbookingDtlsId_" + j + "='" + hdnbookingDtlsId +"'&hdnReceiveDtlsId_" + j + "='" + hdnReceiveDtlsId+"'&hdnJobDtlsId_" + j + "='" + hdnJobDtlsId+"'&hdnProductionDtlsId_" + j + "='" + hdnProductionDtlsId+"'&hdnDeleveryDtlsId_" + j + "='" + hdnDeleveryDtlsId+"'&txtWorkOrderQuantity_" + j + "='" + txtWorkOrderQuantity + "'";
			});	
		}
		if(check_field==0)
		{
			var data="action=save_update_delete&operation="+operation+'&total_row='+i+'&txt_bill_no='+txt_bill_no+'&cbo_company_name='+cbo_company_name+'&cbo_location_name='+cbo_location_name+'&cbo_within_group='+cbo_within_group+'&cbo_party_name='+cbo_party_name+'&cbo_party_location='+cbo_party_location+'&cbo_currency='+cbo_currency+'&txt_challan_no='+txt_challan_no+'&txt_bill_date='+txt_bill_date+'&received_id='+received_id+'&txt_order_no='+txt_order_no+'&hid_order_id='+hid_order_id+'&bill_no_manual='+bill_no_manual+'&cbo_Wo_Currency='+cbo_Wo_Currency+'&txt_exchange_rate='+txt_exchange_rate+'&txt_remarks='+txt_remarks+'&txt_bill_no='+txt_bill_no+'&txtBillAmount='+txtBillAmount+'&txt_upcharge='+txt_upcharge+'&txt_discount='+txt_discount+'&txt_net_Amount='+txt_net_Amount+'&txt_up_remarks='+txt_up_remarks+'&txt_discount_remarks='+txt_discount_remarks+'&update_id='+update_id+data_all;
		
			//alert (data); return; 
			freeze_window(operation);
			http.open("POST","requires/trims_bill_issue_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_job_order_entry_response;
		
		}
		else
		{
			return;
		}	
	}
	function fnc_job_order_entry_response()
	{
		if(http.readyState == 4) 
		{
			//alert (http.responseText);//return;
			var response=trim(http.responseText).split('**');
			if(response[0]*1==40*1)
			{
				alert(response[1]);
				release_freezing();
				return;	
			}
			
			if(response[0]*1==14*1)
			{
				release_freezing();
				alert(response[1]);
				return;
			}
			
			if(response[0]==0 || response[0]==1)
			{
				document.getElementById('txt_bill_no').value = response[1];
				document.getElementById('update_id').value = response[2];
				document.getElementById('txt_bill_no_manual').value = response[3];
				var within_group = $('#cbo_within_group').val();
				/*if(within_group==2)
				{
					document.getElementById('txt_order_no').value = response[3];
				}*/
				$('#txt_order_no').attr('disabled',true);
				$('#cbo_within_group').attr('disabled',true);
				
				//show_list_view(2+'_'+response[1]+'_'+within_group+'_'+response[2],'order_dtls_list_view','emb_details_container','requires/trims_bill_issue_controller','setFilterGrid(\'list_view\',-1)');
				var txt_bill_date = $('#txt_bill_date').val();
				var cbo_currency = $('#cbo_currency').val();
				// $('#txt_exchange_rate').attr('disabled',true);
				var exchange_rate = $('#txt_exchange_rate').val();
				var Wo_Currency = $('#cbo_Wo_Currency').val();
				$('#cbo_Wo_Currency').attr('disabled',true);
				$('#cbo_currency').attr('disabled',true);
				show_list_view(2+'**'+response[2]+'**'+within_group+'**'+txt_bill_date+'**'+exchange_rate+'**'+Wo_Currency+'**'+cbo_currency,'order_dtls_list_view','emb_details_container','requires/trims_bill_issue_controller','setFilterGrid(\'list_view\',-1)');	
				
				
				set_button_status(1, permission, 'fnc_job_order_entry',1);

			}
			else if(response[0]==2)
			{
				location.reload();
			}
			show_msg(response[0]);
			release_freezing();
		}
	}
	function show_print_report()
	{
		if($('#update_id').val()=="")
		{
			alert("Please Save Data First.");
			return;
		}
		else
		{
			//var rate_cond=confirm("Press  \"OK\"  to open with Rate value\nPress  \"Cancel\"  to open without Rate value");
			//if (rate_cond==true) allow_rate="1"; else allow_rate="0";
			var report_title=$( "div.form_caption" ).html();
			//print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_within_group').val(), "trims_order_receive_print_2", "requires/trims_order_receive_controller") 
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#cbo_within_group').val()+'*'+report_title+'*'+$('#txt_order_no').val()+'*'+$('#txt_bill_no').val()+'*'+$('#txt_bill_date').val(), "challan_print2", "requires/trims_bill_issue_controller") 
			return;
		}
	}

	function show_print_report3()
	{
		if($('#update_id').val()=="")
		{
			alert("Please Save Data First.");
			return;
		}
		else
		{
			//var rate_cond=confirm("Press  \"OK\"  to open with Rate value\nPress  \"Cancel\"  to open without Rate value");
			//if (rate_cond==true) allow_rate="1"; else allow_rate="0";
			var report_title=$( "div.form_caption" ).html();
			//print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_within_group').val(), "trims_order_receive_print_2", "requires/trims_order_receive_controller") 
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#cbo_within_group').val()+'*'+report_title+'*'+$('#txt_order_no').val()+'*'+$('#txt_bill_no').val()+'*'+$('#txt_bill_date').val(), "challan_print3", "requires/trims_bill_issue_controller") 
			return;
		}
	}

	function show_print_report4()
	{
		if($('#update_id').val()=="")
		{
			alert("Please Save Data First.");
			return;
		}
		else
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#cbo_within_group').val()+'*'+report_title+'*'+$('#txt_order_no').val()+'*'+$('#txt_bill_no').val()+'*'+$('#txt_bill_date').val()+'*'+$('#txt_remarks').val()+'*'+$('#txt_challan_no').val(), "challan_print4", "requires/trims_bill_issue_controller") 
			return;
		}
	}

	function calculate_amount(i)
	{
		
		
		var balance='';
		var txtCurQty 		= $('#txtQty_'+i).val()*1;
		var txtdeleveryQuantity 	= $('#txtTotDelQuantity_'+i).val()*1;
		var txtPrevQty 	= $('#txtPrevQty_'+i).val()*1;
		var totaldoqnty=(txtCurQty+txtPrevQty);

		balance=(txtdeleveryQuantity-totaldoqnty);
		//alert(totaldoqnty);
		if(balance<0)
		{
			alert("Delv. Qty not more then Current Bill Qty");
			$('#txtQty_'+i).val('');
			//$('#txtDelvBalance_'+rowNo).val('');
			return;
		}
		var ddd={ dec_type:5, comma:0, currency:''};
		math_operation( 'txtBillAmount_'+i, 'txtQty_'+i+'*txtBillRate_'+i, '*','',ddd);
		var billAmount=$('#txtBillAmount_'+i).val()*1;
		var exRate=$('#txtExRate_'+i).val()*1;
		var domAmount=billAmount*exRate;
		$('#txtDomBillAmount_'+i).val(domAmount);
		//var rate=$('#txtBillAmount_'+i).val();

	}

function calculate_total(i)
{
	
	var Uom=$('#cboUom_1').val()*1;
	var uomArr=Array();
	
	var tblRow = $("#tbl_dtls_emb tbody tr").length;
	//alert(tblRow);
	var totalTotalDelvQty=0; var totalCumBillQty=0; var totalCurrentBillQty=0; var totalBillAmount=0;
	for(var i=1;i<=tblRow;i++)
	{
		var TotalDelvQty=$('#txtTotDelQuantity_'+i).val()*1;
		var CumBillQty=$('#txtPrevQty_'+i).val()*1;
		var CurrentBillQty=$('#txtQty_'+i).val()*1;
		var BillAmount=$('#txtBillAmount_'+i).val()*1;
		totalTotalDelvQty +=TotalDelvQty*1;
		totalCumBillQty +=CumBillQty*1;
		totalCurrentBillQty +=CurrentBillQty*1;
		totalBillAmount +=BillAmount*1;
		var TotalcboUom=$('#cboUom_'+i).val()*1;
		
		if(Uom==TotalcboUom){
			uomArr.push(TotalcboUom);
		}
		 
	}
	//alert(uomArr.length);
	if(uomArr.length==tblRow){
	$('#txtTotalDelvQty').val(totalTotalDelvQty.toFixed(2));
	$('#txtCumBillQty').val(totalCumBillQty.toFixed(2));
	$('#txtCurrentBillQty').val(totalCurrentBillQty.toFixed(2));
	$('#txtBillAmount').val(totalBillAmount.toFixed(4));
	}
	calculate_total_amount()
	
}

//alert(unique_uom);




	function change_caption_n_uom(inc,process)
	{
		if(process == 2 || process == 3 || process == 4)
		{
			//$("#cbo_uom").val(12);
		}else{
			//$("#cbo_uom").val(2);
		}
		$('#cboUom_'+inc).attr('disabled',true);
		load_drop_down( 'requires/trims_bill_issue_controller', process+'_'+inc, 'load_drop_down_embl_type', 'embltype_td_'+inc );
		
	}

	function fnc_load_party(type,within_group)
	{
		//alert();
		if ( form_validation('cbo_company_name','Company')==false )
		{
			$('#cbo_within_group').val(1);
			return;
		}
		//load_drop_down( 'requires/trims_bill_issue_controller', company+'_'+1, 'load_drop_down_group', 'group_td' );
		var company = $('#cbo_company_name').val();
		var party_name = $('#cbo_party_name').val();
		var location_name = $('#cbo_location_name').val();
		
		if(within_group==1 && type==1)
		{
			load_drop_down( 'requires/trims_bill_issue_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
			$('#txt_order_no').attr('readonly',true);
			$('#txt_order_no').attr('placeholder','Browse');
			
			$("#cbo_party_location").val(0);
			$('#cbo_party_location').attr('disabled',false);
			
		}
		else if(within_group==2 && type==1)
		{
			load_drop_down( 'requires/trims_bill_issue_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td' );
			$('#txt_order_no').removeAttr('onDblClick','onDblClick');
			
			$('#txt_order_no').attr('readonly',false);
			$('#txt_order_no').attr('placeholder','Write');
			
			$("#cbo_party_location").val(0); 
			$('#cbo_party_location').attr('disabled',true);
		}
		else if(within_group==1 && type==2)
		{
			load_drop_down( 'requires/trims_bill_issue_controller', party_name+'_'+2, 'load_drop_down_location', 'party_location_td' ); 
		} 
	}

	
	function openmypage_job()
	{
		if ( form_validation('cbo_company_name','Company')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
		page_link='requires/trims_bill_issue_controller.php?action=job_popup&data='+data;
		title='Trims Order Receive';
		

		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=990px, height=420px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemaildata=this.contentDoc.getElementById("selected_job").value;
			var ex_data=theemaildata.split('_');
			if (ex_data[0]!="")
			{//alert(theemail.value);
				freeze_window(5);
				get_php_form_data( ex_data[0], "load_mst_php_data_to_form", "requires/trims_bill_issue_controller" );
				var txt_bill_date = $('#txt_bill_date').val();
				var cbo_currency = $('#cbo_currency').val();
				// $('#txt_exchange_rate').attr('disabled',true);
				var exchange_rate = $('#txt_exchange_rate').val();
				var Wo_Currency = $('#cbo_Wo_Currency').val();
				$('#cbo_Wo_Currency').attr('disabled',true);
				$('#cbo_currency').attr('disabled',true);
				var within_group = $('#cbo_within_group').val();
				show_list_view(2+'**'+ex_data[0]+'**'+within_group+'**'+txt_bill_date+'**'+exchange_rate+'**'+Wo_Currency+'**'+cbo_currency,'order_dtls_list_view','emb_details_container','requires/trims_bill_issue_controller','setFilterGrid(\'list_view\',-1)');	
				
				$('#is_posted_account').val(ex_data[2]);
			// if (ex_data[2] == 1) document.getElementById("accounting_posted_status").innerHTML = "Already Posted In Accounting.";
			// else
			// document.getElementById("accounting_posted_status").innerHTML = "";
				set_button_status(1, permission, 'fnc_job_order_entry',1);
				fnc_exchange_rate();
				//calculate_bill_rate();
				release_freezing();
				calculate_total();
			}
		}
	}
	
	function fnc_exchange_rate()
	{
		//var rcv_date=$('#txt_rcv_date').val();
		var currency_id=$('#cbo_currency').val()*1;
		var Wo_Currency=$('#cbo_Wo_Currency').val()*1;
		var exchange_rate=$('#txt_exchange_rate').val()*1;
		
		if(Wo_Currency==1)// taka 
		{
			if(currency_id==3 || currency_id==4|| currency_id==5|| currency_id==6|| currency_id==7 || currency_id==0)
			{
				alert("Not Allow Other Currency");
				$('#cbo_currency').val(Wo_Currency);
				return;
			}
		}
		else if(Wo_Currency==2)//usd
		{
			if(currency_id==3 || currency_id==4|| currency_id==5|| currency_id==6|| currency_id==7)
			{
				alert("Not Allow Other Currency");
				$('#cbo_currency').val(0);
				return;
			}
		}
		else if(Wo_Currency==3)// EURO 
		{
			if(currency_id==2 || currency_id==4|| currency_id==5|| currency_id==6|| currency_id==7)
			{
				alert("Not Allow Other Currency");
				$('#cbo_currency').val(0);
				return;
			}
		}
		else if(Wo_Currency==4)// CHF 
		{
			if(currency_id==2 || currency_id==3|| currency_id==5|| currency_id==6|| currency_id==7)
			{
				alert("Not Allow Other Currency");
				$('#cbo_currency').val(0);
				return;
			}
			
		}
		else if(Wo_Currency==5)// SGD 
		{
			if(currency_id==2 || currency_id==3|| currency_id==4|| currency_id==6|| currency_id==7)
			{
				alert("Not Allow Other Currency");
				$('#cbo_currency').val(0);
				return;
			}
			
		}
		else if(Wo_Currency==6)// Pound 
		{
			if(currency_id==2 || currency_id==3|| currency_id==4|| currency_id==5|| currency_id==7)
			{
				alert("Not Allow Other Currency");
				$('#cbo_currency').val(0);
				return;
			}
			
		}
		else if(Wo_Currency==7)// YEN 
		{
			if(currency_id==2 || currency_id==3|| currency_id==4|| currency_id==5|| currency_id==6)
			{
				alert("Not Allow Other Currency");
				$('#cbo_currency').val(0);
				return;
			}
  		}
		
 		var con_factor='';
		//alert(Wo_Currency);
 		if(currency_id==2  && Wo_Currency==2)
		{
 			con_factor=1;
 		}
		else if(currency_id==1  && Wo_Currency==1)
		{
 			$('#txt_exchange_rate').val(1);
			con_factor=1;
		}
		else if (Wo_Currency==2   &&  currency_id==1)
		{
 			con_factor=exchange_rate;
 		}
		else if (Wo_Currency==1   &&  currency_id==2)
		{
 			con_factor=exchange_rate;
		}
		calculate_bill_rate(2,con_factor);
	}

	function calculate_bill_rate(type,con_factor)
	{
 		var numRow = $('table#tbl_dtls_emb tbody tr').length;
		var billRate=0; var billAmount=0;
		if(type==2)
		{
			for (var i=1;i<=numRow; i++)
			{
				var woRate=$('#txtWoRate_'+i).val()*1;
				var qty=$('#txtQty_'+i).val()*1;
				billRate=con_factor*woRate;
				billAmount=billRate*qty;
				$('#txtBillRate_'+i).val(billRate.toFixed(6));
				$('#txtBillAmount_'+i).val(billAmount.toFixed(4));
				calculate_total(i);
			}
		}
		/*else 
		{
			for (var i=1;i<=numRow; i++)
			{
				
				//alert(woRate);
				var woRate=$('#txtWoRate_'+i).val()*1;
				var qty=$('#txtQty_'+i).val()*1;
				billRate=woRate/con_factor;
				billAmount=billRate*qty;
				$('#txtBillRate_'+i).val(billRate.toFixed(6));
				$('#txtBillAmount_'+i).val(billAmount.toFixed(4));
				calculate_total(i)
			}
		}*/
	}
	function load_uom(i)
	{
		var itemGroup=$('#cboItemGroup_'+i).val();
		var response=return_global_ajax_value(itemGroup, 'check_uom', '', 'requires/trims_bill_issue_controller');
		$('#cboUom_'+i).val(response);

	}
	
	
	
	
	function fnResetForm() 
	{
        set_button_status(0, permission, 'fnc_job_order_entry', 1);
		//reset_form('emborderentry_1','','','cbo_within_group,1*cbo_currency,1*cboUom_1*2',"disable_enable_fields('txt_booking_no*txt_batch_color*cboPoNo_1*cboItemDesc_1*cboDiaWidthType_1*txtRollNo_1*hideRollNo_1*txtBatchQnty_1*hide_job_no',0)'); $('#txt_ext_no').val(''); $('#txt_ext_no').attr('disabled','disabled');$('#txt_batch_number').removeAttr('readOnly','readOnly');$('#tbl_item_details tbody tr:not(:first)').remove();
		reset_form('emborderentry_1','','','cbo_within_group,1*cbo_currency,1*cboUom_1,2','','');
		$('#tbl_dtls_emb tbody tr:not(:first)').remove();
		$('#cbo_company_name').attr('disabled',false);
		$('#cbo_within_group').attr('disabled',false);
		$('#cbo_party_name').attr('disabled',false);
		$('#txt_order_no').attr('disabled',false);
		
		$('#cboGmtsItem_1').attr('disabled',false);
		$('#cboProcessName_1').attr('disabled',false);
		$('#cboembtype_1').attr('disabled',false);
		$('#cboBodyPart_1').attr('disabled',false);
    }

	
	
	function openmypage_devivery_workorder()
	{
		if ( form_validation('cbo_company_name*txt_bill_date','Company*Bill Date')==false )
		{
			return;
		}
        var update_id = $('#update_id').val();
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
		page_link='requires/trims_bill_issue_controller.php?action=devivery_workorder_popup&data='+data+'&selected_id='+$("#challan_selected_id").val()+'&updated_id='+update_id;
		title='Trims Delivery info';
		
		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=990px, height=420px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemailDelv=this.contentDoc.getElementById("all_delivery_ids").value;
			 //alert(theemailDelv); return;
			if (theemailDelv!="")
			{

				freeze_window(5);
				get_php_form_data( theemailDelv+"**"+$('#cbo_currency').val()+"**"+$('#txt_bill_date').val()+"**"+$('#cbo_company_name').val(), "load_delivery_data_to_form", "requires/trims_bill_issue_controller" );
				// $('#cbo_currency').attr('disabled',true);
				//$('#txt_exchange_rate').attr('disabled',true);
				var Wo_Currency = $('#cbo_Wo_Currency').val()*1;
 				var cbo_currency = $('#cbo_currency').val()*1;
				if(Wo_Currency==1)
				{
 					$('#cbo_currency').val(Wo_Currency);
					$('#cbo_currency').attr('disabled',true);
				}
				 
                $('#challan_selected_id').val(theemailDelv);
				var within_group = $('#cbo_within_group').val();
				var txt_bill_date = $('#txt_bill_date').val();
				
				// $('#txt_exchange_rate').attr('disabled',true);
				var exchange_rate = $('#txt_exchange_rate').val();
				
				var company_name = $('#cbo_company_name').val();
				show_list_view(1+'**'+theemailDelv+'**'+within_group+'**'+txt_bill_date+'**'+exchange_rate+'**'+Wo_Currency+'**'+cbo_currency+'**'+company_name+'**'+update_id,'order_dtls_list_view','emb_details_container','requires/trims_bill_issue_controller','setFilterGrid(\'list_view\',-1)');
				calculate_bill_rate(2,exchange_rate)
				if(update_id != "")
                    set_button_status(1, permission, 'fnc_job_order_entry',1);
                else
                    set_button_status(0, permission, 'fnc_job_order_entry',1);
				release_freezing();
				
			}
			
			var numRow = $('table#tbl_dtls_emb tbody tr').length;
			for (var i=1;i<=numRow; i++)
			{
				calculate_amount(i);
				calculate_total(i);
			}
		}
		
	}

	function calculate_total_amount()
	{
		var txt_total_amount=$('#txtBillAmount').val()*1;
		var txt_upcharge=$('#txt_upcharge').val();
		var txt_discount=$('#txt_discount').val();
		if(txt_total_amount>0)
		{
			var net_tot_amnt=txt_total_amount+txt_upcharge*1-txt_discount*1;
			$('#txt_net_Amount').val(net_tot_amnt.toFixed(4));
		}
	}

	function exchange_rate(val)
	{
		if(form_validation('cbo_company_name*txt_bill_date', 'Company Name*Bill Date')==false )
		{
			$("#cbo_currency_id").val(0);
			return;
		}
		
			var bill_date = $('#txt_bill_date').val();
			var company_name = $('#cbo_company_name').val();
			var response=return_global_ajax_value( val+"**"+bill_date+"**"+company_name, 'check_conversion_rate', '', 'requires/trims_bill_issue_controller');
			$('#txt_exchange_rate').val(response);
			$('#txt_bill_date').attr('disabled','disabled');
			$('#cbo_company_name').attr('disabled','disabled'); 
			calculate_bill_rate(2,response);//txt_exchange_rate
			
			//$('#txt_exchange_rate').attr('disabled','disabled');
		
	}

</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission); ?>
        <form name="emborderentry_1" id="emborderentry_1" autocomplete="off"> 
			<fieldset style="width:950px;">
			<legend>Trims Bill Issue</legend>
                <table width="900" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                    <tr>
                        <td colspan="3" align="right"><strong>System ID</strong></td>
                        <td colspan="3">
                            <input type="hidden" name="txt_deleted_id[]" id="txt_deleted_id" class="text_boxes_numeric" style="width:90px" readonly />
                            <input type="hidden" id="is_posted_account" name="is_posted_account" value=""/>
                            <input class="text_boxes"  type="text" name="txt_bill_no" id="txt_bill_no" onDblClick="openmypage_job();" placeholder="Double Click" style="width:140px;" readonly /></td>
                    </tr>
                    <tr>
                        <td width="110" class="must_entry_caption">Company Name </td>
                        <td width="160"><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/trims_bill_issue_controller', this.value+'_'+1, 'load_drop_down_location', 'location_td');fnc_load_party(1,document.getElementById('cbo_within_group').value);get_php_form_data( this.value, 'company_wise_report_button_setting','requires/trims_bill_issue_controller');"); ?>
                        </td>
                        <td width="110"  class="must_entry_caption">Location Name</td>
                        <td width="160" id="location_td"><? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "" ); ?></td>
                        <td width="110" class="must_entry_caption">Within Group</td>
                        <td><?php echo create_drop_down( "cbo_within_group", 150, $yes_no,"", 0, "--  --", 0, "fnc_load_party(1,this.value);" ); ?></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Party</td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_load_party(2,document.getElementById('cbo_within_group').value);"); ?></td>
                        <td id="td_party_location">Party Location</td>
                        <td id="party_location_td"><? echo create_drop_down( "cbo_party_location", 150, $blank_array,"", 1, "-- Select Location --", $selected, "",1 ); ?></td>
                        <td class="must_entry_caption">Bill Date</td>
                        <td><input type="text" name="txt_bill_date" id="txt_bill_date"  style="width:140px"  class="datepicker" value="<? echo Date('d-m-Y'); ?>"  /></td>
                    </tr> 
                    <tr>
                        <td><strong>Sys Challan No</strong></td>
                        <td>
                            <input name="txt_challan_no" id="txt_challan_no" type="text"  class="text_boxes" style="width:140px" placeholder="Browse" onDblClick="openmypage_devivery_workorder();" readonly />
                            <input type="hidden" id="challan_selected_id" value="">
                        </td>
                        <td class="must_entry_caption">Wo Currency</td>
                        <td><? echo create_drop_down( "cbo_Wo_Currency",150, $currency,"", 1, "-- Select --","","fnc_exchange_rate();exchange_rate(this.value)", 1,'','','','','','',"cbo_Wo_Currency[]"); ?></td>
                        <td class="must_entry_caption">Bill Currency</td>
                        <td><? echo create_drop_down( "cbo_currency", 150, $currency,"", 1, "-- Select Currency --",0,"fnc_exchange_rate();","","" ); ?></td>
                    </tr>
                    <tr>
                        <td>Bill No</td>
                        <td ><input name="txt_bill_no_manual" id="txt_bill_no_manual" type="text" class="text_boxes" style="width:140px"/>
                        <td class="must_entry_caption">WO Ex. Rate</td>
                        <td ><input name="txt_exchange_rate" id="txt_exchange_rate" type="text" class="text_boxes_numeric" onKeyUp="calculate_bill_rate(2,this.value)" style="width:140px" />
                        <td>Remarks</td>
                        <td ><input name="txt_remarks" id="txt_remarks" type="text" class="text_boxes" style="width:140px"/>
                    </tr>
                    <tr>
                    	<td colspan="2">&nbsp;</td>
                    	<td>Terms and Condition</td>
                        <td>
	                        <? 
	                        include("../../terms_condition/terms_condition.php");
	                        terms_condition(276,'update_id','../../');
	                        ?>
                        </td>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                </table>
        </fieldset> 			
        <fieldset style="width:1470px;">
           <legend>Trims Bill Issue <span style="text-align: center;display: block;color: red;font-size: 17px;margin-top: -20px;" id="bill_posted_msg"></span></legend>
                <table cellpadding="0" cellspacing="2" border="0" class="rpt_table" rules="all" id="tbl_dtls_emb">
                    <thead class="form_table_header">
                    	<th width="90">Work Order</th>
                    	<th width="100">Buyer PO</th>
                        <th width="90">Section</th>
                        <th width="100">Trims Group</th>
                        <th width="100">Style</th>
                        <th width="90">Item Description</th>
                        <th width="150">Challan No</th>
						<th width="90">Gmts Color</th>
                        <th width="90">Gmts Size</th>
                        <th width="90">Item Color</th>
                        <th width="90">Item Size</th>
                        <th width="60">UOM</th>
                        <th width="70">WO Qty</th>
                        <th width="70">Total Delv. Qty</th>
                        <th width="70">Cum. Bill Qty</th>
                        <th width="80" class="must_entry_caption">Current Bill Qty</th>
                        <th width="70" style="display:none">WO Currency</th>
                        <th width="70" style="display:none">Exchange Rate</th>
                        <th width="70">WO Rate</th>
                        <th width="70">Bill Rate</th>
                        <th>Bill Amount</th>
                        <th style="display:none">Domestic Bill Amount</th>
                    </thead>
                    <tbody id="emb_details_container">
                        <tr id="row_1">
                        	<td><input id="txtWorkOrder_1" name="txtWorkOrder[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/>
                        		<input id="txtWorkOrderID_1" name="txtWorkOrderID[]" type="hidden" class="text_boxes" style="width:100px" placeholder="Display"/>
                        	</td>
                        	<td><input id="txtbuyerPO_1" name="txtbuyerPO[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/>
                        		<input id="txtbuyerPOID_1" name="txtbuyerPOID[]" type="hidden" class="text_boxes" style="width:100px" placeholder="Display"/>
                        	</td>
                            <td><? echo create_drop_down( "cboSection_1", 90, $trims_section,"", 1, "-- Select Section --","",'',0,'','','','','','',"cboSection[]"); ?></td>
                            <td><? echo create_drop_down( "cboItemGroup_1", 100, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- Select --",$selected, "",1,'','','','','','',"cboItemGroup[]"); ?>	</td>
                            <td><input id="txtStyle_1" name="txtStyle[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/></td>
                            <td><input id="txtdescription_1" name="txtdescription[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/></td>
                            <td><input id="txtChallan_1" name="txtChallan[]" type="text" class="text_boxes" style="width:150px" placeholder="Display"/></td>
							<td>
								<input id="txtgmtscolor_1" name="txtgmtscolor[]" type="text" class="text_boxes" style="width:90px" placeholder="Display"/>
                            	<input id="txtgmtscolorId_1" name="txtgmtscolorId[]" type="hidden" class="text_boxes" style="width:90px" placeholder="Display"/>
							</td>
                            <td>
								<input id="txtgmtssize_1" name="txtgmtssize[]" type="text" class="text_boxes" style="width:90px" placeholder="Display"/>
								<input id="txtgmtssizeId_1" name="txtgmtssizeId[]" type="hidden" class="text_boxes" style="width:90px" placeholder="Display"/>
							</td>
                            <td>
								<input id="txtcolor_1" name="txtcolor[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/>
                            	<input id="txtcolorID_1" name="txtcolorID[]" type="hidden" class="text_boxes" style="width:100px" placeholder="Display"/>
							</td>
                            <td>
								<input id="txtsize_1" name="txtsize[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/>
								<input id="txtsizeID_1" name="txtsizeID[]" type="hidden" class="text_boxes" style="width:100px" placeholder="Display"/>
							</td>
                            <td><? echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 1, "-- Select --",2,1, 1,'','','','','','',"cboUom[]"); ?>	</td>
                            <td><input id="txtWorkOrderQuantity_1" name="txtWorkOrderQuantity[]" class="text_boxes_numeric" type="text"  style="width:60px" placeholder="" readonly /></td>
                            <td><input id="txtTotDelQuantity_1" name="txtTotDelQuantity[]" class="text_boxes_numeric" type="text"  style="width:60px" onClick="openmypage_order_qnty2(1,'0',1)" placeholder="" readonly /></td>
                            <td><input id="txtPrevQty_1" name="txtPrevQty[]" type="text"  class="text_boxes_numeric" style="width:60px" readonly /></td>
                            <td><input id="txtQty_1" name="txtQty[]" type="text"  class="text_boxes_numeric" style="width:70px" readonly /></td>
                            <td style="display:none"><? echo create_drop_down( "cboCurrency_1", 80, $currency,"", 1, "-- Select --",2,1, 1,'','','','','','',"cboCurrency[]"); ?>
                           	</td>
                            <td style="display:none"><input id="txtExRate_1" name="txtExRate[]" type="text"  class="text_boxes_numeric" style="width:60px" readonly /></td>
                            <td><input id="txtWoRate_1" name="txtWoRate[]" type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td> 
                            <td><input id="txtBillRate_1" name="txtBillRate[]" type="text"  class="text_boxes_numeric" style="width:70px"  /></td> 
                            <td><input id="txtBillAmount_1" name="txtBillAmount[]" type="text"  class="text_boxes_numeric" style="width:77px" readonly />
                            </td>
                            <td style="display:none"><input id="txtDomBillAmount_1" name="txtDomBillAmount[]" type="text"  class="text_boxes_numeric" style="width:77px" readonly />
                            	<input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_1">
                                <input type="hidden" name="hdnbookingDtlsId[]" id="hdnbookingDtlsId_1">
                                <input type="hidden" name="hdnReceiveDtlsId[]" id="hdnReceiveDtlsId_1">
                                <input type="hidden" name="hdnJobDtlsId[]" id="hdnJobDtlsId_1">
                                <input type="hidden" name="hdnProductionDtlsId[]" id="hdnProductionDtlsId_1">
                                <input type="hidden" name="hdnDeleveryDtlsId[]" id="hdnDeleveryDtlsId_1">
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
							<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>Total:</td>
                            <td align="center"><input type="text" name="txtTotalWorkOrderqty" id="txtTotalWorkOrderqty" class="text_boxes_numeric" style="width:60px;" readonly /></td>
                            <td align="center"><input type="text" name="txtTotalDelvQty" id="txtTotalDelvQty" class="text_boxes_numeric" style="width:60px;" readonly /></td>
                    		<td align="center"><input type="text" name="txtCumBillQty" id="txtCumBillQty" class="text_boxes_numeric" style="width:60px;" readonly /></td>
                    		<td align="center"><input type="text" name="txtCurrentBillQty" id="txtCurrentBillQty" class="text_boxes_numeric" style="width:70px;" readonly /></td>
                            <td>&nbsp;</td>
                    		<td>&nbsp;</td>
                             <td align="center"><input type="text" name="txtBillAmount" id="txtBillAmount" class="text_boxes_numeric" style="width:77px;" readonly /></td> 
                    	</tr>
                    	<tr class="tbl_bottom" >
							<td align="right" colspan="12">Upcharge Remarks:</td>
                            <td colspan="5" align="center"><input type="text" id="txt_up_remarks" name="txt_up_remarks" class="text_boxes" style="width:370px;" maxlength="100" placeholder="Maximum 100 Character" /></td>
                    		<td>Upcharge: </td>
                            <td align="center"><input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" style="width:77px;" onKeyUp="calculate_total_amount()" /></td> 
                    	</tr>
                    	<tr class="tbl_bottom" >
							<td align="right" colspan="12">Discount Remarks:</td>
                            <td colspan="5" align="center"><input type="text" id="txt_discount_remarks" name="txt_discount_remarks" class="text_boxes" style="width:370px;" maxlength="100" placeholder="Maximum 100 Character" /></td>
                    		<td>Discount: </td>
                            <td align="center"><input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" style="width:77px;" onKeyUp="calculate_total_amount()" /></td> 
                    	</tr>
                    	<tr class="tbl_bottom" >
                            <td colspan="17">&nbsp;</td>
                    		<td>Net Total: </td>
                            <td align="center"><input type="text" name="txt_net_Amount" id="txt_net_Amount" class="text_boxes_numeric" style="width:77px;" readonly /></td> 
                    	</tr>
                        <tr>
<!--                        <td colspan="19" height="15" align="center"><div id="accounting_posted_status" style="float:center; font-size:20px; color:#FF0000;"></div></td>-->
                        </tr>
                    </tfoot> 
                </table>
                <table width="1210" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td align="center" colspan="19" valign="middle" class="button_container">
                        	<? echo load_submit_buttons( $permission, "fnc_job_order_entry", 0,1,"fnResetForm();",1); ?>
                        	<input type="hidden" name="hid_order_id" id="hid_order_id">
                            <input type="hidden" name="update_id" id="update_id">
                            <input type="hidden" name="received_id" id="received_id">
                            <input type="hidden" name="delivery_id" id="delivery_id">
                            <input type="button" id="btn_print2" value="Print2" class="formbutton" style="width:100px;" onClick="show_print_report();" >
                            <input type="button" id="btn_print3" value="Print3" class="formbutton" style="width:100px;" onClick="show_print_report3();" >
                            <input type="button" id="btn_print4" value="Print 4" class="formbutton" style="width:100px;" onClick="show_print_report4();" >
                        </td>
                    </tr>   
                </table>
            </fieldset> 
        </form>                         
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
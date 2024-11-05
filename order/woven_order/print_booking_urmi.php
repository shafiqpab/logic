<?
/*-------------------------------------------- Comments
Version          :  V1
Purpose			 : This form will create print Booking
Functionality	 :	
JS Functions	 :
Created by		 : MONZU 
Creation date 	 : 
Requirment Client: 
Requirment By    : 
Requirment type  : 
Requirment       : 
Affected page    : 
Affected Code    :              
DB Script        : 
Updated by 		 : 
Update date		 : 
Report Created BY: Aziz
QC Performed BY	 :		
QC Date			 :	
Comments		 : From this version oracle conversion is start
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Woven Fabric Booking", "../../", 1, 1,$unicode,1,'');
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	
<? $data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][161] );
echo "var field_level_data= ". $data_arr . ";\n";
?>
	
	function openmypage(page_link,title)
	{
		var d = new Date();
		var date=d.yyyymmdd();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("selected_job") //Access form field with id="emailfield"
			if (theemail.value!="")
			{
				freeze_window(5);
				reset_form('printbooking_1','booking_list_view*booking_list_view2','','cbo_pay_mode,3*cbo_currency,2*cbo_ready_to_approved,2*cbo_pay_mode,1*cbo_source,3*cbo_level,2*cbo_is_short,2');
				get_php_form_data( theemail.value, "populate_order_data_from_search_popup", "requires/print_booking_urmi_controller" );
				
				
				
				document.getElementById('txt_booking_date').value=date
				
				single_select()
				
				release_freezing();
			}
		}
	}

	function single_select()
	{
		if($('#txt_order_no_id option').length==2)
		{
			if($('#txt_order_no_id option:first').val()==0)
			{
				$('#txt_order_no_id').val($('#txt_order_no_id option:last').val());
				eval($('#txt_order_no_id').attr('onchange')); 
			}
		}
		else if($('#txt_order_no_id option').length==1)
		{
			$('#txt_order_no_id').val($('#txt_order_no_id option:last').val());
			eval($('#txt_order_no_id').attr('onchange'));
		}	
		
		if($('#cbo_gmt_item option').length==2)
		{
			if($('#cbo_gmt_item option:first').val()==0)
			{
				$('#cbo_gmt_item').val($('#cbo_gmt_item option:last').val());
				eval($('#cbo_gmt_item').attr('onchange')); 
			}
		}
		else if($('#cbo_gmt_item option').length==1)
		{
			$('#cbo_gmt_item').val($('#cbo_gmt_item option:last').val());
			eval($('#cbo_gmt_item').attr('onchange'));
		}	
	}

	Date.prototype.yyyymmdd = function()
	{         
		var yyyy = this.getFullYear().toString();                                    
		var mm = (this.getMonth()+1).toString(); // getMonth() is zero-based         
		var dd  = this.getDate().toString();             
		//return yyyy + '-' + (mm[1]?mm:"0"+mm[0]) + '-' + (dd[1]?dd:"0"+dd[0]);
		return (dd[1]?dd:"0"+dd[0])+ '-' + (mm[1]?mm:"0"+mm[0])+ '-' + yyyy ;
	};  

	function fnc_generate_booking()
	{
		var txt_booking_no=document.getElementById('txt_booking_no').value;
		var txt_job_no=document.getElementById('txt_job_no').value;
		var txt_order_no_id=document.getElementById('txt_order_no_id').value;
		var cbo_gmt_item=document.getElementById('cbo_gmt_item').value
		var cbo_booking_natu=document.getElementById('cbo_booking_natu').value;
		var txt_booking_date=document.getElementById('txt_booking_date').value;
		var booking=return_global_ajax_value(txt_booking_no+"_"+txt_job_no+"_"+txt_order_no_id+"_"+cbo_gmt_item+"_"+cbo_booking_natu, 'delete_row_fabric_cost', '', 'requires/print_booking_urmi_controller');
		if (form_validation('cbo_booking_natu*cbo_gmt_item','Order No*Fabric Nature*Gmt Item')==false)
		{
			return;
		}
		else
		{
			var data="action=generate_print_booking"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*txt_order_no_id*cbo_booking_natu*cbo_gmt_item*txt_booking_no*txt_booking_date*txt_delivery_date*calculation_basis*cbo_level*cbo_is_short*cbo_currency',"../../");
			http.open("POST","requires/print_booking_urmi_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_generate_booking_reponse;
		}
	}

	function fnc_generate_booking_reponse()
	{
		if(http.readyState == 4) 
		{
			document.getElementById('booking_list_view').innerHTML=http.responseText;
			set_all_onclick();
			//set_button_status(0, permission, 'fnc_fabric_booking_dtls',2);
		}
	}

	function fn_empty_dtls()
	{
		document.getElementById('booking_list_view').innerHTML="";
		set_all_onclick();
	}

	function fnc_show_booking()
	{
		if (form_validation('txt_order_no_id*cbo_booking_natu*cbo_gmt_item','Order No*Fabric Nature*Gmt Item')==false)
		{
			return;
		}
		else
		{
			var data="action=show_print_booking"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*txt_order_no_id*cbo_booking_natu*cbo_gmt_item*txt_booking_no*cbo_is_short',"../../");
			http.open("POST","requires/print_booking_urmi_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_generate_booking_reponse;
		}
	}

	function fnc_generate_booking_reponse()
	{
		if(http.readyState == 4) 
		{
			document.getElementById('booking_list_view').innerHTML=http.responseText;
			set_all_onclick();
			//set_button_status(1, permission, 'fnc_fabric_booking_dtls',2);
		}
	}

	function openmypage_booking(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_booking");
			if (theemail.value!="")
			{
				freeze_window(5);
				reset_form('printbooking_1','booking_list_view*booking_list_view2','','cbo_pay_mode,3*cbo_currency,2*cbo_ready_to_approved,2*cbo_pay_mode,1*cbo_source,3*cbo_is_short,2');
				get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/print_booking_urmi_controller" );
				
				get_php_form_data( document.getElementById('txt_job_no').value, "populate_order_data_from_search_popup", "requires/print_booking_urmi_controller" );
				show_list_view(theemail.value,'print_booking_list_view','booking_list_view2','requires/print_booking_urmi_controller','setFilterGrid(\'list_view\',-1)');
				set_button_status(1, permission, 'fnc_fabric_booking',1);
				release_freezing();
			}
		}
	}

	function fn_change_emb_name()
	{
		get_php_form_data(document.getElementById('txt_job_no').value, 'change_emb_name', 'requires/print_booking_urmi_controller')	
	}

	function fnc_fabric_booking( operation )
	{
		var delete_cause='';
		if(operation==2)
		{
			delete_cause = prompt("Please enter your delete cause", "");
			if(delete_cause=="")
			{
				alert("You have to enter a delete cause");
				release_freezing();
				return;
			}
			if(delete_cause==null)
			{
				release_freezing();
				return;
			}
			var r=confirm("Press OK to Delete Or Press Cancel");
			if(r==false)
			{
				release_freezing();
				return;
			}
		}
		
		if(document.getElementById('id_approved_id').value==1)
		{
			alert("This booking is approved")
			return;
		}
		if (form_validation('txt_job_no*txt_booking_date*txt_delivery_date*cbo_pay_mode*cbo_supplier_name','Job No*Booking Date*Delivery Date*Pay Mode*Supplier')==false)
		{
			return;
		}	
		else
		{
			var data="action=save_update_delete&operation="+operation+"&delete_cause="+delete_cause+get_submitted_data_string('txt_booking_no*id_approved_id*txt_job_no*cbo_company_name*cbo_buyer_name*txt_booking_date*txt_delivery_date*cbo_currency*txt_exchange_rate*cbo_supplier_name*hidden_supplier_id*cbo_pay_mode*cbo_source*cbo_ready_to_approved*txt_attention*txt_tenor*calculation_basis*cbo_level*cbo_is_short*update_id',"../../");
			freeze_window(operation);
			http.open("POST","requires/print_booking_urmi_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_fabric_booking_reponse;
		}
	}

	function fnc_fabric_booking_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
			{
				show_msg(trim(reponse[0]));
				document.getElementById('txt_booking_no').value=reponse[1];
				if(reponse[0]==0) 
				{
					document.getElementById('update_id').value=reponse[2];
				}
				set_button_status(1, permission, 'fnc_fabric_booking',1);
			}
			if(reponse[0]==2)
			{
				reset_form('printbooking_1','booking_list_view*booking_list_view2','','cbo_pay_mode,3*cbo_currency,2*cbo_ready_to_approved,2*cbo_pay_mode,1*cbo_source,3*cbo_is_short,2');
	
			}
			if(trim(reponse[0])=='approved'){
				alert("This booking is approved");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='sal1'){
				alert("Sales Order  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='pi1'){
				alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}
			release_freezing();
		}
	}


	function fnc_fabric_booking_dtls( operation )
	{
		var delete_cause='';
		if(operation==2)
		{
			delete_cause = prompt("Please enter your delete cause", "");
			if(delete_cause=="")
			{
				alert("You have to enter a delete cause");
				release_freezing();
				return;
			}
			if(delete_cause==null)
			{
				release_freezing();
				return;
			}
			var r=confirm("Press OK to Delete Or Press Cancel");
			if(r==false)
			{
				release_freezing();
				return;
			}
		}
		
		if(document.getElementById('id_approved_id').value==1)
		{
			alert("This booking is approved")
			return;
		}
		if (form_validation('txt_booking_no*cbo_gmt_item*cbo_booking_natu','Booking No*Order No*Garments Item*Emblishment Name')==false)
		{
			return;
		}	
		var data_all=get_submitted_data_string('txt_booking_no*txt_job_no*txt_order_no_id*cbo_gmt_item*cbo_booking_natu*cbo_pay_mode*cbo_is_short*cbo_supplier_name*hidden_supplier_id*update_id',"../../");
		var row_num=$('#tbl_list_search tr').length;
		
		for (var i=1; i<=row_num; i++)
		{
			var amount=$('#txtamount_'+i).val()*1;
			var amount_precost=$('#txtamount_precost_'+i).val()*1;
			if(amount_precost<amount)
			{
				//alert("Amount Exceeds Pre Cost Amount");return;   // Validation stopped by CTO on request of Beeresh Vai for Group on 03-08-2015
			}
			var txtreqqty=$('#txtreqqty_'+i).val()*1;
			var txtcuwoq=$('#txtcuwoq_'+i).val()*1;
			var txtwoq=$('#txtwoq_'+i).val()*1;
			var txtbalqty=$('#txtbalqty_'+i).val()*1;
			var pre_woqty=$('#txt_bal_qty_precost_'+i).val()*1;
			
			var cbo_is_short=$('#cbo_is_short').val()*1;
			txtreqqty=number_format(txtreqqty,2,'.','' );
			txtcuwoq=number_format(txtcuwoq,2,'.','' );
			txtwoq=number_format(txtwoq,2,'.','' );
			txtbalqty=number_format(txtbalqty,2,'.','' );
			pre_woqty=number_format(pre_woqty,2,'.','' );
			if(cbo_is_short==2)
			{
				if(operation!=2)
				{
					var total_curr_wo_woqnty=0;
					if(operation==0) //CRM Issue=6809
					{
						total_curr_wo_woqnty=(txtcuwoq*1)+(txtwoq*1);
					}
					else
					{
						total_curr_wo_woqnty=((txtcuwoq*1)-(pre_woqty*1))+(txtwoq*1);
					}
					//alert(total_curr_wo_woqnty+'='+txtcuwoq+'='+txtwoq);
					total_curr_wo_woqnty=number_format(total_curr_wo_woqnty,2,'.','' );
					var req_qty=txtreqqty.split(".");
					var reqqty=req_qty[0];
					//alert(total_curr_wo_woqnty);
					var curr_wo_woqnty=total_curr_wo_woqnty.split(".");
					var curr_woqnty=curr_wo_woqnty[0];
					
					var woqnty=txtwoq.split(".");
					var wo_qnty=woqnty[0];
					//alert(curr_woqnty+'='+reqqty); release_freezing(); return;
					
					if(operation==0)
					{
						if((curr_woqnty*1)>(reqqty*1))
						{
							var wo_msg="Exceed qty is not allowed.";
							alert(wo_msg);
							release_freezing();
							return;
						}
					}
					else
					{
						//if((wo_qnty*1)>(curr_woqnty*1))
						if((curr_woqnty*1)>(reqqty*1))
						{
							var wo_msg="Exceed qty is not allowed, CurrentQty="+curr_woqnty;
							alert(wo_msg);
							release_freezing();
							return;
						}
					}
					var total_req_amount=number_format($('#txtamount_'+i).attr('reqamount')*1,2,'.','' );
					var total_amount=number_format($('#txtamount_'+i).val()*1,2,'.','' );
					var exc_amount=total_amount-total_req_amount;
					if(total_amount>total_req_amount)
					{
						/*var wo_msg="Exceed Amount then BOM is not allow.\n Exceed Amount is : "+exc_amount;
						alert(wo_msg);
						release_freezing();
						return;*/
					}
				}
			}
			
			data_all=data_all+get_submitted_data_string('txtbookingdtlasid_'+i+'*txtpoid_'+i+'*txtitemnumberid_'+i+'*txtembcostid_'+i+'*txtcolorid_'+i+'*txtwoq_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtddate_'+i+'*description_'+i+'*txtreqqty_'+i+'*txtcountryid_'+i+'*txtuomid_'+i,"../../",i);
		}
		
		var cbo_level=document.getElementById('cbo_level').value;
		var jason_data=document.getElementById('jason_data').value;
		if(cbo_level==1){
			var data="action=save_update_delete_dtls&operation="+operation+'&total_row='+row_num+data_all+"&delete_cause="+delete_cause;
		}
		if(cbo_level==2){
			var data="action=save_update_delete_dtls_job_level&operation="+operation+'&total_row='+row_num+data_all+'&jason_data='+jason_data+"&delete_cause="+delete_cause;
		}
		//alert(data); return;
		freeze_window(operation);
		http.open("POST","requires/print_booking_urmi_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_dtls_reponse;
	}

	function fnc_fabric_booking_dtls_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=http.responseText.split('**');
			show_msg(trim(reponse[0]));
			
			if(reponse[0]==0 || reponse[0]==1)
			{
				show_list_view(trim(reponse[1]),'print_booking_list_view','booking_list_view2','requires/print_booking_urmi_controller','setFilterGrid(\'list_view\',-1)');
				fnc_generate_booking();
				release_freezing();
			}
			if(reponse[0]==2)
			{
				show_list_view(trim(reponse[1]),'print_booking_list_view','booking_list_view2','requires/print_booking_urmi_controller','setFilterGrid(\'list_view\',-1)');
				document.getElementById('booking_list_view').innerHTML="";
	
				set_all_onclick();
				release_freezing();
			}
			if(trim(reponse[0])=='notfoundemblish')
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			
			if(trim(reponse[0])=='approved'){
				alert("This booking is approved");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='sal1'){
				alert("Sales Order  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='pi1'){
				alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}
			release_freezing();
		}
	}
	
	function auto_completesupplier() // Auto Complite Party/Transport Com
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		//cbo_supplier_name hidden_supplier_id
		var company_id=document.getElementById('cbo_company_name').value;
		var pay_mode=document.getElementById('cbo_pay_mode').value;
		
		var supplier = return_global_ajax_value( company_id+'_'+pay_mode, 'supplier_company_action', '', 'requires/print_booking_urmi_controller');
		supplierInfo = eval(supplier);
		$("#cbo_supplier_name").autocomplete({
		 source: supplierInfo,	
		 search: function( event, ui ) {
			$("#hidden_supplier_id").val("");
			$("#hidden_supplier_name").val("");
		},	 
		select: function (e, ui) {
				$(this).val(ui.item.label);
				$("#hidden_supplier_name").val(ui.item.label);
				$("#hidden_supplier_id").val(ui.item.id);
			}
		});
		 
		$(".supplier_name").live("blur",function(){
			  if($(this).siblings(".hdn_supplier_name").val() == ""){
				  $(this).val("");
			 }
		});
	}
	
	function supplier_empty_check()
	{
		$("#cbo_supplier_name").val('');
		$("#hidden_supplier_id").val('');	
		$('#cbo_supplier_name').removeAttr('disabled','disabled');
	}
	
	function update_booking_data(data)
	{
		var data=data.split("_");
		document.getElementById('txt_order_no_id').value=data[0];
		document.getElementById('cbo_gmt_item').value=data[1];
		document.getElementById('cbo_booking_natu').value=data[2];
		fnc_generate_booking()
	}

	function open_terms_condition_popup(page_link,title)
	{
		var txt_booking_no=document.getElementById('txt_booking_no').value;
		if (txt_booking_no=="")
		{
			alert("Save The Booking First")
			return;
		}	
		else
		{
			page_link=page_link+get_submitted_data_string('txt_booking_no','../../');
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=470px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
			}
		}
	}

	function calculate_amount(i)
	{
		var txtrate_precost=(document.getElementById('txtrate_precost_'+i).value)*1
		var txtrate=(document.getElementById('txtrate_'+i).value)*1
		var txtwoq=(document.getElementById('txtwoq_'+i).value)*1
		if(txtrate>txtrate_precost)
		{
			alert("Rate Exceeds Of Pre-Cost Rate,Budget Rate="+txtrate_precost);
			$('#txtrate_'+i).val(txtrate_precost);	
			document.getElementById('txtamount_'+i).value=number_format_common((txtrate_precost*txtwoq),5,0);
			return;
		}
		//alert(txtrate*txtwoq)
		document.getElementById('txtamount_'+i).value=number_format_common((txtrate*txtwoq),5,0);
	}

	function calculate_amount2(i)
	{
		var txtrate_precost=(document.getElementById('txtrate_precost_'+i).value)*1
		var txtrate=(document.getElementById('txtrate_'+i).value)*1
		
		var txtamount_precost=(document.getElementById('txtamount_precost_'+i).value)*1
		var txtamount=(document.getElementById('txtamount_'+i).value)*1
		var txtwoq=(document.getElementById('txtwoq_'+i).value)*1
		var bal_qty_precost=(document.getElementById('txt_bal_qty_precost_'+i).value)*1
		if(txtamount>txtamount_precost)
		{
			//alert("Amount Exceeds Pre-Cost Amount");
			//document.getElementById('txtamount_'+i).value=number_format_common(txtamount_precost,5,0)
			//	document.getElementById('txtwoq_'+i).value=number_format_common(bal_qty_precost,5,0)
			//document.getElementById('txtamount_'+i).value=number_format_common((txtrate_precost*txtamount_precost),5,0)
	
			//return
		}
		//alert(txtrate*txtwoq)
		//document.getElementById('txtamount_'+i).value=number_format_common((txtamount_precost),5,0);
	}

	function generate_fabric_report(type)
	{
		if (form_validation('txt_booking_no','Booking No')==false)
		{
			return;
		}
		else
		{
			$report_title=$( "div.form_caption" ).html();
			var data="action="+type+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*cbo_booking_natu*cbo_gmt_item*id_approved_id*txt_job_no*cbo_is_short',"../../")+'&report_title='+$report_title;
			//freeze_window(5);
			http.open("POST","requires/print_booking_urmi_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_fabric_report_reponse;
		}	
	}

	function generate_fabric_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var file_data=http.responseText.split('****');
			$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0] );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
		}
	}

	function generate_trim_report(action)// Report here
	{ 
		if (form_validation('txt_booking_no','Booking No')==false)
		{
			return;
		}
		else
		{
		
			var show_comment='';
			var r=confirm("Press  \"Cancel\"  to hide  comments\nPress  \"OK\"  to Show comments");
			if (r==true) show_comment="1"; else show_comment="0";
			
			var data="action="+action+get_submitted_data_string('txt_booking_no*txt_job_no*cbo_company_name*cbo_buyer_name*txt_booking_date*txt_delivery_date*cbo_currency*cbo_supplier_name*hidden_supplier_id*cbo_pay_mode*txt_exchange_rate*cbo_source*cbo_booking_natu*calculation_basis*cbo_is_short*cbo_template_id*txt_season*cbo_level',"../../")+'&show_comment='+show_comment;
			http.open("POST","requires/print_booking_urmi_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_trim_report_reponse;
		}	
	}

	function generate_trim_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var file_data=http.responseText.split('****');
			$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0] );
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
		}
	}

	function setdata(data)
	{
		document.getElementById('ccc').innerHTML=data;
		document.getElementById('myBtn').click();
	}
	
	function copy_value(i,field)
	{
		var copy_val=document.getElementById('copy_val').checked;
		 var rowCount=$('#tbl_list_search tbody tr').length;
		 if(copy_val==true)
		 {
			 for(var j=i; j<=rowCount; j++){
				 if(field=='description_'){
					  var description=document.getElementById('description_'+i).value;
					  document.getElementById('description_'+j).value=description;
				 }
				 if(field=='txtwoq_'){
					  var txtwoq=document.getElementById('txtwoq_'+i).value;
					  document.getElementById('txtwoq_'+j).value=txtwoq;
					  calculate_amount(j);
					  calculate_amount2(j);
				 }
			 }
		 }
	}
	//for print button
	function print_report_button_setting(report_ids)
	{
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==86){
				$("#print_booking").show();
			}
			else if(report_id[k]==87){
				$("#print_booking_act").show();
			}
			else if(report_id[k]==88){
				$("#print_booking3").show();
			}
			if(report_id[k]==89){
				$("#print_booking4").show();
			}
		}
	}
	function check_exchange_rate()
	{
		var cbo_currercy=$('#cbo_currency').val();
		var booking_date = $('#txt_booking_date').val();
		var cbo_company_name = $('#cbo_company_name').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+booking_date+"**"+cbo_company_name, 'check_conversion_rate', '', 'requires/print_booking_urmi_controller');
		var response=response.split("_");
		$('#txt_exchange_rate').val(response[1]);
	}
</script>
<style>
	 /* The Modal (background) */
	.modal {
		display: none; /* Hidden by default */
		position: fixed; /* Stay in place */
		z-index: 1; /* Sit on top */
		left: 0;
		top: 0;
		width: 100%; /* Full width */
		height: 100%; /* Full height */
		overflow: auto; /* Enable scroll if needed */
		background-color: rgb(0,0,0); /* Fallback color */
		background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
	}
	
	 /* Modal Header */
	.modal-header {
		padding: 2px 16px;
		background-color: #999;
		color: white;
	}
	
	/* Modal Body */
	.modal-body {padding: 2px 16px;}
	
	/* Modal Footer */
	.modal-footer {
		padding: 2px 16px;
		background-color: #999;
		color: white;
	}
	
	/* Modal Content */
	.modal-content {
		position: relative;
		background-color: #fefefe;
		margin: auto;
		padding: 0;
		border: 1px solid #888;
		width: 80%;
		box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
		-webkit-animation-name: animatetop;
		-webkit-animation-duration: 0.4s;
		animation-name: animatetop;
		animation-duration: 0.4s
	}
	
	/* Add Animation */
	@-webkit-keyframes animatetop {
		from {top: 300px; opacity: 0}
		to {top: 0; opacity: 1}
	}
	
	@keyframes animatetop {
		from {top: 300px; opacity: 0}
		to {top: 0; opacity: 1}
	}
	
	/* The Close Button */
	.close {
		color: #aaa;
		float: right;
		font-size: 28px;
		font-weight: bold;
	}
	
	.close:hover,
	.close:focus {
		color: black;
		text-decoration: none;
		cursor: pointer;
	}
</style>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
<? echo load_freeze_divs ("../../",$permission);  ?>
<form name="printbooking_1"  autocomplete="off" id="printbooking_1">
    <fieldset style="width:1000px;">
    <legend>Embellishment Work Order</legend>
        <table width="1000" cellspacing="2" cellpadding="0" border="0">
            <tr>
                <td colspan="4" align="right" class="must_entry_caption"> Wo No </td>              
                <td colspan="4">
                    <input class="text_boxes" type="text" style="width:120px" onDblClick="openmypage_booking('requires/print_booking_urmi_controller.php?action=fabric_booking_popup','Print Booking Search')" readonly placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no"/>
                    <input type="hidden" id="id_approved_id">
                    <input style="width:50px;" type="hidden"  class="text_boxes"  name="report_ids" id="report_ids" /> 
                </td>
            </tr>
            <tr>
                <td width="110">Job No.</td>
                <td width="140"><input style="width:120px;" type="text" class="text_boxes"  name="txt_job_no" id="txt_job_no" onDblClick="openmypage('requires/print_booking_urmi_controller.php?action=order_popup','Job/Order Selection Form');" placeholder="Double Click" readonly/></td>
                <td width="110">Company Name</td>
                <td width="140"><?=create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company where status_active=1 and is_deleted=0 and core_business not in(3) order by company_name", "id,company_name",1, "-- Select Company --", $selected, "load_drop_down( 'requires/print_booking_urmi_controller', this.value, 'load_drop_down_buyer', 'buyer_td' )",1,"" ); ?></td>
                <td width="110">Buyer Name</td>   
                <td width="140" id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 130, "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,"" ); ?> </td>
                <td width="110" class="must_entry_caption">WO Date</td>
                <td><input class="datepicker" type="text" style="width:120px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<?=date("d-m-Y");?>"  disabled/></td>
            </tr>
            <tr>
                <td class="must_entry_caption">Delivery Date</td>
                <td><input class="datepicker" type="text" style="width:120px" name="txt_delivery_date" id="txt_delivery_date"/></td>
                <td>Currency</td>
                <td><?=create_drop_down( "cbo_currency", 130, $currency,"", 1, "-- Select --", 2, "check_exchange_rate()",0 ); ?></td>
                <td>Exchange Rate</td>
                <td><input style="width:120px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate" readonly  /></td>
                <td class="must_entry_caption">Pay Mode</td>
                <td><?=create_drop_down( "cbo_pay_mode", 130, $pay_mode,"", 1, "-- Select Pay Mode --", 1, "supplier_empty_check()","" ); ?></td>
            </tr>
            <tr>
            	<td>Source</td>              <!-- 11-00030  -->
                <td><?=create_drop_down( "cbo_source", 130, $source,"", 1, "-- Select Source --", 3, "","" ); ?></td>
                <td class="must_entry_caption">Supplier Name</td>
                <td>
                    <input type="text" name="cbo_supplier_name" id="cbo_supplier_name" class="text_boxes supplier_name" onFocus="auto_completesupplier()" style="width:120px;" placeholder="Write"  />
                    
                    <input type="hidden" class="hdn_supplier_name" id="hidden_supplier_name" name="hidden_supplier_name" />
                    <input type="hidden" id="hidden_supplier_id" name="hidden_supplier_id" style="width:60px;" class="text_boxes"  >
                </td>
                <td>Attention</td>   
                <td colspan="3"><input class="text_boxes" type="text" style="width:370px;"  name="txt_attention" id="txt_attention" /></td>
            </tr>
            <tr>
            	<td>Tenor</td>
                <td><input style="width:120px;" type="text" class="text_boxes_numeric" name="txt_tenor" id="txt_tenor" /></td>
            	<td>Season</td>
            	<td><input style="width:120px;" type="text" class="text_boxes"  name="txt_season" id="txt_season" disabled /></td>
                <td>Calculation Basis</td>   
                <td><?=create_drop_down( "calculation_basis", 130, $calculation_basis,"", 0, "", "", "","","" ); ?></td>
                
                <td>Is Short</td>
                <td><?=create_drop_down( "cbo_is_short", 130, $yes_no,'', 0, '',2,"");?></td> 
            </tr>
            <tr>
            	<td>Level</td>  
                <td>
                <?
                	$level_arr=array(1=>"PO Level",2=>"Job Level");
                    echo create_drop_down( "cbo_level", 130, $level_arr,"", 0, "", 2, "","","" );
                ?>
                </td>
            	<td>Ready To Approve</td>  
                <td><?=create_drop_down( "cbo_ready_to_approved", 130, $yes_no,"", 1, "-- Select--", 2, "","","" ); ?></td> 
            	<td>&nbsp;</td>   
                <td>
					<? 
                        include("../../terms_condition/terms_condition.php");
                        terms_condition(161,'txt_booking_no','../../');
                    ?>
                     <input type="hidden" id="update_id" >
                     <input type="hidden" id="dtls_update_id" >
                </td>
            	<td>&nbsp;</td> 
                <td>&nbsp;</td> 
            </tr>
            <tr>
                <td align="center" colspan="8" valign="top" id="app_sms2" style="font-size:18px; color:#F00">
                </td>
            </tr>
            <tr>
                <td align="center" colspan="8" valign="middle" class="button_container">
                <? 
				$date=date("d-m-Y");
				echo load_submit_buttons( $permission, "fnc_fabric_booking", 0,0 ,"reset_form('printbooking_1','booking_list_view','','cbo_pay_mode,3*cbo_currency,2*cbo_ready_to_approved,2*cbo_pay_mode,1*cbo_source,3*txt_booking_date,$date*cbo_is_short,2')",1) ; 
				?>
                </td>
            </tr>
            <tr>
                <td align="center"  valign="middle" colspan="8">
                    <div id="pdf_file_name"></div>
                    <?=create_drop_down( "cbo_template_id", 100, $report_template_list,'', 0, '', 0, ""); ?>
                    
                    <input type="button" value="Print" onClick="generate_trim_report('show_trim_booking_report')"  style="width:80px; display:none" name="print_booking" id="print_booking" class="formbutton" /> 
                    
                    <input type="button" value="Print Actual" onClick="generate_trim_report('show_trim_booking_report1')"  style="width:80px;display:none" name="print_booking_act" id="print_booking_act" class="formbutton" /> 
                    
                    <input type="button" value="Print 3" onClick="generate_trim_report('show_trim_booking_report2')"  style="width:80px;display:none" name="print_booking3" id="print_booking3" class="formbutton" /> 
                    <input type="button" value="Print 4" onClick="generate_trim_report('show_trim_booking_report_urmi')"  style="width:80px;display:none" name="print_booking4" id="print_booking4" class="formbutton" />    
                </td>
  		   </tr>
        </table>
        <input type="button" id="myBtn" value="OPen" style="display:none"/>

    <!-- The Modal -->
    <div id="myModal" class="modal">
      <div class="modal-content">
          <div class="modal-header">
            <span class="close">×</span>
            <h2>Po Number</h2>
          </div>
          <div class="modal-body">
            <p id="ccc">Some text in the Modal Body</p>
          </div>
          <div class="modal-footer">
            <h3></h3>
          </div>
    	</div>
    </div>
    </fieldset>
</form>


    <form id="printbooking_2" name="printbooking_2" autocomplete="off">
        <fieldset style="width:1247px;">
        <legend>Details</legend>
                <table style="border:none" cellpadding="0" cellspacing="2" border="0">
                    <tr>
                        <td height="" align="right" class="must_entry_caption" style="display:none">Order No</td>   
                        <td id="po_id_td" style="display:none">
                        <? echo create_drop_down( "txt_order_no_id", 172, $blank_array,"", 1, "-- Select PO --", "", "fn_empty_dtls()","",""); ?>
                        </td> 
                        <td align="right" width="130" class="must_entry_caption">
                        Gmt Item
                        </td>
                        <td id="gmt_item_td">	
                        <? 
                        echo create_drop_down( "cbo_gmt_item", 172, $blank_array,"", 1, "-- Select --", "","", "", "");		
                        ?>
                        </td>
                        <td align="right" class="must_entry_caption">Embl Name</td>
                        <td id="booking_natu_td">
                        <? 
                        echo create_drop_down( "cbo_booking_natu", 172, $blank_array,"", 1, "-- Select --", "","","","");		
                        ?>	
                                                

                        </td>
                    </tr>
                     <tr align="center">
                        <td colspan="6"><b>Copy</b> :<input type="checkbox" id="copy_val" name="copy_val"/> </td>	
                    </tr>
                    <tr align="center">
                        <td colspan="6" id="booking_list_view"></td>	
                    </tr>
                    <tr align="center">
                        <td colspan="6" id="booking_list_view2"></td>	
                    </tr>
                </table>
        </fieldset>
    </form>


</div>
<div style="display:none" id="data_panel"></div>


</body>
<script>
var modal = document.getElementById('myModal');

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on the button, open the modal
btn.onclick = function() {
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
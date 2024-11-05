<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Dry Production
Functionality	:	
JS Functions	:
Created by		:	K.M Nazim Uddin
Creation date 	: 	31-19-2019
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
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Dry Production Info", "../../", 1, 1,'','','');

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	var str_supervisor = [<? echo substr(return_library_autocomplete( "select distinct(operator_name) as supervisor from subcon_embel_production_dtls", "operator_name"  ), 0, -1); ?>];
	
	function fnc_embel_entry(operation)
	{
		if(operation==4)
		{
			if ( $('#txt_production_id').val()=='')
			{
				alert ('Production ID Not Save.');
				return;
			}
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "wash_production_entry_print", "requires/dry_production_controller") 
			//return;
			show_msg("3");
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if( form_validation('cbo_company_id*cbo_location*txt_job_no*txt_prod_date*txt_reporting_hour','Company*Location*Job No.*Production Date*Reporting Hour')==false )
			{
				return;
			}
			
			var j=0; var dataString='';  var check_field=0;//var all_barcodes=''; 
			$("#wash_details_container").find('tr').each(function()
			{
				
				
				
				//var colorSizeId=$(this).find('input[name="colorSizeId[]"]').val();
				var updateIdDtls=$(this).find('input[name="updateIdDtls[]"]').val();
				var txtbuyerPoId=$(this).find('input[name="txtbuyerPoId[]"]').val();
				var txtPoId=$(this).find('input[name="txtPoId[]"]').val();
				
				var txtProdQty=$(this).find('input[name="txtProdQty[]"]').val();
				var txtRejQty=$(this).find('input[name="txtRejQty[]"]').val();
				var hidOrderQty=$(this).find('input[name="hidOrderQty[]"]').val();
				var txtProcesId=$(this).find('select[name="txtProcesId[]"]').val();
				var txtWashType=$(this).find('select[name="txtWashType[]"]').val();
				//var txtReWashQty=$(this).find('input[name="txtReWashQty[]"]').val();
				var txtRemarks=$(this).find('input[name="txtRemarks[]"]').val();
				
				
				if( txtWashType ==0  || txtProdQty==0 || txtProcesId==0)
				{	 				
				if(txtWashType==0){
					alert('Please Select WashType');
					check_field=1 ; return;
				}else if(txtProdQty==0){
					alert('Please Write ProdQty');
					check_field=1 ; return;
				}else if(txtProcesId==0){
					alert('Please Select ProcesId ');
					check_field=1 ; return;
				}
				return;
			}
			if(check_field==0)
			{
				if( txtProdQty*1>0)
				{
					j++;
					
					dataString += '&txtbuyerPoId_' + j + '=' + txtbuyerPoId + '&txtPoId_' + j + '=' + txtPoId  + '&txtProcesId_' + j + '=' + txtProcesId  + '&txtWashType_' + j + '=' + txtWashType  + '&updateIdDtls_' + j + '=' + updateIdDtls + '&txtProdQty_' + j + '=' + txtProdQty + '&hidOrderQty_' + j + '=' + hidOrderQty+ '&txtRejQty_' + j + '=' + txtRejQty+ '&txtRemarks_' + j + '=' + txtRemarks;
				}
			}
				
				
			});
			if(j<1)
			{
				alert('Please Insert Qty At Least One Row.');
				return;
			}
			//alert(dataString);//return;
			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_production_id*update_id*cbo_company_id*cbo_location*txt_job_id*hid_order_id*txt_job_no*txt_prod_date*txt_reporting_hour*txt_super_visor*cboShift*cbo_floor_id*cbo_machine_id',"../../")+dataString+'&total_row='+j;
			//alert (data);return;
			freeze_window(operation);
			
			http.open("POST","requires/dry_production_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_embel_entry_response;
		}
	}	 
	 
	function fnc_embel_entry_response()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');	
			if(trim(response[0])=='emblQc'){
				alert("QC Found :"+trim(response[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			 }
			show_msg(response[0]);
			
			if( response[0]==0 || response[0]==1 )
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_production_id').value = response[2];
				show_list_view(response[1]+'_'+response[3]+'_'+response[4],'dry_production_list_view','dry_production_list_conainer','requires/dry_production_controller','setFilterGrid("list_view",-1)');
				
				show_list_view(response[3]+'_'+response[4],'show_fabric_desc_listview','list_fabric_desc_container','requires/dry_production_controller','setFilterGrid("list_view",-1)');
				//var batch_id = $('#txt_batch_id').val();
				//fnc_dtls_data_load(batch_id,response[1]);
				//reset_form( '', 'tbl_item_details', '', '' )
				fnc_reset_dtls_part(response[0]);
				set_button_status(0, permission, 'fnc_embel_entry',1,1);
			}
			if( response[0]==2 )
			{
				location.reload();
			}
			release_freezing();	
		}
	}
	 
	function fnc_reset_dtls_part(save_update)
	{
		/*$('#txt_prod_date').val('');
		$('#txt_reporting_hour').val('');
		$('#txt_super_visor').val('');
		$('#cbo_sub_operation').val('');
		$('#cboShift').val('');*/
		$('#txtBuyerStyle_1').val('');
		$('#txtBuyerPO_1').val('');
		$('#txtGmtsItem_1').val('');
		$('#txtGmtsColor_1').val('');
		$('#txtProcesId_1').val(0);
		type_select(0);
		$('#txtWashType_1').val(0);
		$('#txtProdQty_1').val('');
		$('#txtRejQty_1').val('');
		$('#txtRemarks_1').val('');
		$('#updateIdDtls_1').val('');
		$('#txtBuyerPoId_1').val('');
		$('#txtPoId_1').val('');
	}

	function set_form_data_update(data)
	{
		//alert(data);
		var data=data.split("**");
		$('#txtPoId_1').val(data[0]);
		$('#txtBuyerStyle_1').val(data[1]);
		$('#txtBuyerPO_1').val(data[2]);
		//alert(data[3]);
	    $('#txtBuyerPoId_1').val(data[3]);
	  	//alert( $('#txtBuyerPoId_1').val());
		$('#txtPO_1').val(data[4]);
		$('#txtGmtsItem_1').val(data[5]);
		$('#txtGmtsColor_1').val(data[6]);
		$('#txtProdQty_1').val(data[7]);
		$('#hidOrderQty_1').val(data[7]);
		//alert(trim(data[8]));
		$('#txtProcesId_1').val(trim(data[8]));
		//alert( $('#txtProcesId_1').val());
		type_select(data[8]);
		$('#txtWashType_1').val(data[9]);

		$('#txtProdQty_1').val(data[10]);
		$('#txtRejQty_1').val(data[11]);
		$('#txtRemarks_1').val(data[12]);
		$('#updateIdDtls_1').val(data[13]);
		$('#prevdryqty_1').val(data[14]);
		$('#prevrejectQty_1').val(data[15]);
		$('#OrderQty_1').val(data[16]);
		$('#totalprevdryqty_1').val(data[17]-data[10]);
		$('#prevreturnQty_1').val(data[18]);
		$('#issueQty_1').val(data[19]);
		
		$('#yourElementId').prop('title','Delivery Return Qty  '+ data[18]);
	    loadStock(data[9]);
		
		set_button_status(1, permission, 'fnc_embel_entry',1,0);
		//openmypage_qnty(data[6]);	
	}
	function openmypage_batch()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Batch Pop-up';	
			var page_link = 'requires/dry_production_controller.php?cbo_company_id='+cbo_company_id+'&action=batch_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=780px,height=450px,center=1,resize=1,scrolling=0','../');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var str_data=this.contentDoc.getElementById("selected_str_data").value;	 //Access form field with id="emailfield"
				
				if(update_id!="")
				{
					freeze_window(5);
					var estr_data=str_data.split("___");
					
					$('#txt_batch_id').val(estr_data[0]);
					$('#txt_batch_no').val( estr_data[1] );
					
					$('#txt_job_no').val(estr_data[2]);
					$('#txt_order_id').val(estr_data[3]);
					$('#txt_order').val(estr_data[4]);
					
					//$('#txtbuyerPoId').val(estr_data[8]);
					$('#txtbuyerPo').val(estr_data[9]);
					$('#txtstyleRef').val(estr_data[10]);
					/*$('#cbo_operation').val(estr_data[11]);
					$('#cbo_sub_operation').val(estr_data[12]);
					$('#cbo_sub_operation').attr('readonly',true);
					$('#cbo_operation').attr('disabled',true);*/
					
					load_drop_down( 'requires/dry_production_controller', cbo_company_id+'_'+estr_data[6]+'_'+estr_data[5], 'load_drop_down_buyer', 'party_td');
					$('#txt_order_qty').val(estr_data[7]);
					
					fnc_dtls_data_load(estr_data[0],0);
					
					release_freezing();
				} 
			}
		}
	}
	
	function fnc_dtls_data_load(batch_id,uid)
	{
		//alert(batch_id+'_'+uid); return;
		var cbo_company_id = $('#cbo_company_id').val();
		var list_view_orders = return_global_ajax_value( cbo_company_id+'***'+batch_id+'***'+uid, 'order_details', '', 'requires/dry_production_controller');
		if(list_view_orders!='')
		{
			$("#wash_details_container").html(list_view_orders);
		}
		fnc_total_calculate();
	}
	 
	function fnc_embel_prod_id()
	{
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 
			var company_id = $('#cbo_company_id').val();
			var title = 'Production ID Selection Form';	
			var page_link = 'requires/dry_production_controller.php?cbo_company_id='+company_id+'&action=embel_production_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=450px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//Access the form inside the modal window
				var emblishment_data=this.contentDoc.getElementById("hidden_production_data").value;
				//alert(emblishment_id_data);return;
				var emb_data = emblishment_data.split("***");
				if(emb_data[0]!="")
				{
					freeze_window(5);
					$('#update_id').val(emb_data[0]);
					$('#txt_production_id').val(emb_data[1]);
					$('#cbo_location').val(emb_data[2]);
					$('#txt_job_no').val(emb_data[3]);
					$('#txt_order_id').val(emb_data[4]);
					$('#txt_order_no').val(emb_data[5]);
					load_drop_down( 'requires/dry_production_controller', company_id+'_'+emb_data[6]+'_'+emb_data[7], 'load_drop_down_buyer', 'buyer_td');
					$('#txt_order_qty').val(emb_data[8]);
					$('#txt_prod_date').val(emb_data[9]);
					$('#txt_reporting_hour').val(emb_data[10]);
					$('#txt_super_visor').val(emb_data[11]);
					$('#cboShift').val(emb_data[12]);
					load_drop_down( 'requires/dry_production_controller', company_id+'__'+emb_data[2], 'load_drop_down_floor', 'floor_td');
					load_drop_down( 'requires/dry_production_controller',company_id+'_'+emb_data[13], 'load_drop_down_machine', 'machine_td' );
					$('#cbo_floor_id').val(emb_data[13]);
					$('#cbo_machine_id').val(emb_data[14]);
					$('#txt_job_id').val(emb_data[15]);
					
					show_list_view(emb_data[15]+'_'+emb_data[3],'show_fabric_desc_listview','list_fabric_desc_container','requires/dry_production_controller','setFilterGrid("list_view",-1)');
					show_list_view(emb_data[0]+'_'+emb_data[15]+'_'+emb_data[3],'dry_production_list_view','dry_production_list_conainer','requires/dry_production_controller','setFilterGrid("list_view",-1)');

					/*$('#cbo_sub_operation').attr('readonly',true);
					$('#cbo_operation').attr('disabled',true);*/
					//alert(3);
					//fnc_dtls_data_load(emb_data[3],emb_data[0]);
					set_button_status(1, permission, 'fnc_embel_entry',1,1);
					release_freezing();
				}
			}
		}
	}

	function fnc_valid_time(val,field_id)
	{
		var val_length=val.length;
		if(val_length==2)
		{
			document.getElementById(field_id).value=val+":";
		}
	
		var colon_contains=val.includes(":");
		if(colon_contains==false)
		{
			if(val>23)
			{
				document.getElementById(field_id).value='23:';
			}
		}
		else
		{
			var data=val.split(":");
			var minutes=data[1];
			var str_length=minutes.length;
			var hour=data[0]*1;
	
			if(hour>23)
			{
				hour=23;
			}
	
			if(str_length>=2)
			{
				minutes= minutes.substr(0, 2);
				if(minutes*1>59)
				{
					minutes=59;
				}
			}
	
			var valid_time=hour+":"+minutes;
			document.getElementById(field_id).value=valid_time;
		}
	}
	
	function numOnly(myfield, e, field_id)
	{
		var key;
		var keychar;
		if (window.event)
			key = window.event.keyCode;
		else if (e)
			key = e.which;
		else
			return true;
		keychar = String.fromCharCode(key);
	
		// control keys
		if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;
		// numbers
		else if ((("0123456789:").indexOf(keychar) > -1))
		{
			var dotposl=document.getElementById(field_id).value.lastIndexOf(":");
			if(keychar==":" && dotposl!=-1)
			{
				return false;
			}
			return true;
		}
		else
			return false;
	}
	
	function fn_autocomplete()
	{
		 $("#txt_super_visor").autocomplete({
			 source: str_supervisor
		  });
	}
	
	function load_machine()
	{
		//var cbo_company_id = $('#cbo_company_id').val();
		var cbo_source =1; //$('#cbo_knitting_source').val();
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_floor_id = $('#cbo_floor_id').val();
		if(cbo_source==1)
		{
			load_drop_down( 'requires/dry_production_controller',cbo_company_id+'_'+cbo_floor_id, 'load_drop_down_machine', 'machine_td' );
		}
		else
		{
			load_drop_down( 'requires/dry_production_controller',0+'_'+0, 'load_drop_down_machine', 'machine_td' );
		}
	}

	function type_select(process_id)
	{
		var PoId=$('#txtPoId_1').val();
		var WashType=$('#txtWashType_1').val();
		load_drop_down( 'requires/dry_production_controller',process_id+'*'+$('#txtPoId_1').val(), 'load_drop_down_type', 'wash_type_td' );
		
	}
	
	function location_select()
	{
		if($('#cbo_location option').length==2)
		{
			if($('#cbo_location option:first').val()==0)
			{
				$('#cbo_location').val($('#cbo_location option:last').val());
				//eval($('#cbo_location').attr('onchange')); 
			}
		}
		else if($('#cbo_location option').length==1)
		{
			$('#cbo_location').val($('#cbo_location option:last').val());
			//eval($('#cbo_location').attr('onchange'));
		}	
		load_drop_down('requires/dry_production_controller', document.getElementById('cbo_company_id').value+'__'+document.getElementById('cbo_location').value, 'load_drop_down_floor', 'floor_td');
	}
	
	function fnc_total_calculate(rowNo)
	{
		//alert(rowNo);
		
		var OrderQty=$('#OrderQty_'+rowNo).val()*1;
		var issueQty=$('#issueQty_'+rowNo).val()*1;
		var txtProdQty=$('#txtProdQty_'+rowNo).val()*1;
		var txtRejQty=$('#txtRejQty_'+rowNo).val()*1;
		var update_id=$('#update_id').val();
		//alert(update_id);
		var prevdryqty=$('#prevdryqty_'+rowNo).val()*1;
		var prevrejectQty=$('#prevrejectQty_'+rowNo).val()*1;
		var totalprevdryqty=$('#totalprevdryqty_'+rowNo).val()*1;
		var prevreturnQty=$('#prevreturnQty_'+rowNo).val()*1;
		//var totalprevdryqty=prevdryqty+prevrejectQty;
		var totalcurentprodqty=txtProdQty+txtRejQty;
		var totalPrevBalanceQty=totalprevdryqty-prevreturnQty;
		//var blance_dry_qty=OrderQty-totalprevdryqty;
		//var blance_dry_qty=OrderQty-totalPrevBalanceQty; previous code
		var blance_dry_qty=issueQty-totalPrevBalanceQty;
		
				/*$totalprevdryqty=$prv_dry_qty+$reje_qty;
				$totalPrevBalanceQty=$totalprevdryqty-$delv_return_qty;
				$balance_qty=$qty_pcs-$totalPrevBalanceQty;*/
		if(totalcurentprodqty>blance_dry_qty)
		{
			alert ("Production Quantity Can't Over Than Issue Quantity");
			$('#txtProdQty_'+rowNo).val("");
			$('#txtRejQty_'+rowNo).val("")
			return;
		}
		var rowCount = $('#wash_details_container tr').length;
		//alert(rowCount)
		math_operation( "txtTotProdQty", "txtProdQty_", "+", rowCount );
		math_operation( "txtTotRejQty", "txtRejQty_", "+", rowCount );
	}

	function openmypage_job()
	{
		if ( form_validation('cbo_company_id','Company')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_location').value+"_"+document.getElementById('cbo_buyer_name').value;
		//var data=document.getElementById('cbo_company_id').value;
		page_link='requires/dry_production_controller.php?action=job_popup&data='+data;
		title='JOB Search';
		

		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=990px, height=420px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemaildata=this.contentDoc.getElementById("selected_job").value;
			var ex_data=theemaildata.split('_');
			if (ex_data[1]!="")
			{//alert(theemail.value);

				freeze_window(5);
				get_php_form_data( ex_data[0], "load_php_data_to_form", "requires/dry_production_controller" );
				var within_group = $('#cbo_within_group').val();
				show_list_view(ex_data[0]+'_'+ex_data[1],'show_fabric_desc_listview','list_fabric_desc_container','requires/dry_production_controller','setFilterGrid("list_view",-1)');
				//load_drop_down( 'requires/dry_production_controller', company_id+'__'+emb_data[2], 'load_drop_down_floor', 'floor_td');
				//load_drop_down( 'requires/dry_production_controller',company_id+'_'+emb_data[18], 'load_drop_down_machine', 'machine_td' );
				//show_list_view(theemail.value,'subcontract_dtls_list_view','order_list_view','requires/dry_production_controller','setFilterGrid("list_view",-1)');
				//show_list_view(2+'_'+ex_data[1]+'_'+within_group+'_'+$("#update_id").val()+'_'+$("#txt_ex_rate").val(),'order_dtls_list_view','wash_details_container','requires/dry_production_controller','setFilterGrid(\'list_view\',-1)');	
				//set_button_status(1, permission, 'fnc_job_order_entry',1);
				release_freezing();
			}
		}
	} 


function loadStock(values)
 {
		 
		var txtPoId = document.getElementById('txtPoId_1').value;
		var txtProcesId = document.getElementById('txtProcesId_1').value;
		var txtWashType = document.getElementById('txtWashType_1').value;
		var OrderQty = document.getElementById('OrderQty_1').value;
		var updateIdDtls = document.getElementById('updateIdDtls_1').value;
		var txtProdQty = document.getElementById('txtProdQty_1').value;
		var txt_job_no = document.getElementById('txt_job_no').value;
		var args = txtPoId+'**'+txtProcesId+'**'+txtWashType+'**'+OrderQty+'**'+updateIdDtls+'**'+txtProdQty+'**'+txt_job_no;
		get_php_form_data(args, 'load_stock_by_prodQty', 'requires/dry_production_controller' );
	}

	function set_form_data(data)
	{
		
	    $('#txtBuyerStyle_1').val('');
		$('#txtBuyerPO_1').val('');
		$('#txtGmtsItem_1').val('');
		$('#txtGmtsColor_1').val('');
		$('#txtProcesId_1').val(0);
		type_select(0);
		$('#txtWashType_1').val(0);
		$('#txtProdQty_1').val('');
		$('#txtRejQty_1').val('');
		$('#txtRemarks_1').val('');
		$('#updateIdDtls_1').val('');
		$('#txtBuyerPoId_1').val('');
		$('#txtPoId_1').val('');
		
		var data=data.split("**");
		$('#txtPoId_1').val(data[0]);
		$('#txtBuyerStyle_1').val(data[1]);
		$('#txtBuyerPO_1').val(data[2]);
		//alert(data[3]);
	    $('#txtBuyerPoId_1').val(data[3]);
	   // alert( $('#txtBuyerPoId_1').val());
		$('#txtPO_1').val(data[4]);
		$('#txtGmtsItem_1').val(data[5]);
		$('#txtGmtsColor_1').val(data[6]);
		$('#txtProdQty_1').val(data[12]);
		$('#hidOrderQty_1').val(data[7]);
		$('#OrderQty_1').val(data[7]);
		$('#prevdryqty_1').val(data[9]);
		$('#prevrejectQty_1').val(data[10]);
		$('#totalprevdryqty_1').val(data[11]);
		$('#prevreturnQty_1').val(data[13]);
		$('#issueQty_1').val(data[14]);
		$('#yourElementId').prop('title','Delivery Return Qty  '+data[13]);
		$('#txtProcesId_1').attr('disabled',false);
		$('#txtWashType_1').attr('disabled',false);
		loadStock();
		//$('#totalprevdryqty_1').attr(title,valu,data[11]);
		set_button_status(0, permission, 'fnc_embel_entry',1,0);
		//openmypage_qnty(data[6]);	
	}
	/*function fnc_load_party(type,within_group)
	{
		if ( form_validation('cbo_company_name','Company')==false )
		{
			$('#cbo_within_group').val(1);
			return;
		}
		//$('#txtOrderDeliveryDate_1').val($('#txt_delivery_date').val());
		var company = $('#cbo_company_name').val();
		var party_name = $('#cbo_party_name').val();
		var location_name = $('#cbo_location_name').val();
		
		if(within_group==1 && type==1)
		{
			load_drop_down( 'requires/wash_order_entry_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
			
			$('#txt_order_no').removeAttr("onDblClick").attr("onDblClick","openmypage_order();");
			
			$('#txt_order_no').attr('readonly',true);
			$('#txt_order_no').attr('placeholder','Browse');
			
			$("#cbo_party_location").val(0);
			$('#cbo_party_location').attr('disabled',false);
			$('#cbo_currency').attr('disabled',true);
			$("#cboUom_1").val(2);
			$('#cboUom_1').attr('disabled',true);
			
			$('#td_party_location').css('color','blue');
			$('#buyerpo_td').css('color','blue');
			$('#buyerstyle_td').css('color','blue');
			
			$('#txtpartybuyername_1').attr('readonly',true);
			$('#txtbuyerPo_1').attr('readonly',true);
			$('#txtstyleRef_1').attr('readonly',true);

			$('#txtColor_1').attr('readonly',true);
			$('#txtColor_1').attr('placeholder','Display');
			
			$('#txtOrderQuantity_1').attr('readonly',true);
			$('#txtOrderQuantity_1').attr('placeholder','Display');
			$('#txtbuyerPo_1').attr('placeholder','Display');
			$('#txtpartybuyername_1').attr('placeholder','Display');
			$('#txtstyleRef_1').attr('placeholder','Display');
		}
		else if(within_group==2 && type==1)
		{
			load_drop_down( 'requires/wash_order_entry_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td' );
			$('#txt_order_no').removeAttr('onDblClick','onDblClick');
			
			$('#txt_order_no').attr('readonly',false);
			$('#txt_order_no').attr('placeholder','Write');
			
			$("#cbo_party_location").val(0); 
			$('#cbo_party_location').attr('disabled',true);
			$('#cbo_currency').attr('disabled',false);
			$('#cboUom_1').attr('disabled',false);
			
			$('#td_party_location').css('color','black');
			$('#buyerpo_td').css('color','black');
			$('#buyerstyle_td').css('color','black');
			$('#txtbuyerPo_1').attr('readonly',false);
			$('#txtpartybuyername_1').attr('readonly',false);
			$('#txtstyleRef_1').attr('readonly',false);
			
			$('#txtColor_1').attr('readonly',false);
			$('#txtColor_1').attr('placeholder','Write');
			
			$('#txtOrderQuantity_1').attr('readonly',false);
			$('#txtOrderQuantity_1').attr('placeholder','Write');
			$('#txtbuyerPo_1').attr('placeholder','Write');
			$('#txtpartybuyername_1').attr('placeholder','Write');
			$('#txtstyleRef_1').attr('placeholder','Write');
		}
		else if(within_group==1 && type==2)
		{
			load_drop_down( 'requires/wash_order_entry_controller', party_name+'_'+2, 'load_drop_down_location', 'party_location_td' ); 
			$('#td_party_location').css('color','blue');
			$('#cbo_currency').attr('disabled',true);
			//$('#cboUom_1').val(2);
			$('#cboUom_1').attr('disabled',true);
			$('#txtbuyerPo_1').attr('readonly',true);
			$('#txtpartybuyername_1').attr('readonly',true);
			$('#txtstyleRef_1').attr('readonly',true);
		} 
	}*/
	
	
	function val_roundup()
	 {
        if($('#round_down').is(':checked')){
            $( "input[name='txtProdQty[]']" ).each(function (index){
                var cur_bal = $(this).val();
                $(this).attr('title', cur_bal);
                var octal = cur_bal.toString().split(".");
                $(this).val(octal[0]);
            });
        }
		else
		{
            $( "input[name='txtProdQty[]']" ).each(function (index){
                var prev_bal = $(this).attr('title');
                if(prev_bal === undefined){
                    prev_bal = 0.0000;
                }
                $(this).attr('title', '');
                $(this).val(prev_bal);
            });
        }
    }


    function remove_del_val()
    {
    	if($('#remove_del_value').is(':checked'))
    	{
            $( "input[name='txtProdQty[]']" ).each(function (index)
            {
                var cur_bal = $(this).val();
                $(this).attr('title', cur_bal);
                var octal = cur_bal.toString().split(".");
                $(this).val('');
            });
        }
        else
        {
            $( "input[name='txtProdQty[]']" ).each(function (index)
            {
                var prev_bal = $(this).attr('title');
                if(prev_bal === undefined){
                    prev_bal = 0.0000;
                }
                $(this).attr('title', '');
                $(this).val(prev_bal);
            });
        }
    }
	
	
	 function val_roundup()
	 {
        if($('#round_down').is(':checked')){
            $( "input[name='txtProdQty[]']" ).each(function (index){
                var cur_bal = $(this).val();
                $(this).attr('title', cur_bal);
                var octal = cur_bal.toString().split(".");
                $(this).val(octal[0]);
            });
        }
		else
		{
            $( "input[name='txtProdQty[]']" ).each(function (index){
                var prev_bal = $(this).attr('title');
                if(prev_bal === undefined){
                    prev_bal = 0.0000;
                }
                $(this).attr('title', '');
                $(this).val(prev_bal);
            });
        }
    }


  function remove_del_val()
    {
    	if($('#remove_del_value').is(':checked'))
    	{
            $( "input[name='txtProdQty[]']" ).each(function (index)
            {
                var cur_bal = $(this).val();
                $(this).attr('title', cur_bal);
                var octal = cur_bal.toString().split(".");
                $(this).val('');
            });
        }
        else
        {
            $( "input[name='txtProdQty[]']" ).each(function (index)
            {
                var prev_bal = $(this).attr('title');
                if(prev_bal === undefined){
                    prev_bal = 0.0000;
                }
                $(this).attr('title', '');
                $(this).val(prev_bal);
            });
        }
    }
 </script>
</head>
<body onLoad="set_hotkey();">
<div align="left" style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />
    <form name="dryProduction_1" id="dryProduction_1" autocomplete="off" >
    <div style="width:900px;">
        <fieldset style="width:800px; float:left;">
        <legend>Dry Production</legend>
        <fieldset style="width:800px;">
            <table width="100%" cellpadding="1" cellspacing="1" border="0" > 
        <tr>
            <td colspan="3" align="right"><strong>Production ID</strong></td>
            <td colspan="3">
                <input type="text" name="txt_production_id" id="txt_production_id" class="text_boxes" style="width:140px;" placeholder="Browse" onDblClick="fnc_embel_prod_id();" />
                <input type="hidden" name="update_id" id="update_id"/>
            </td>
        </tr>
        <tr>
            <td width="100" class="must_entry_caption">Company Name</td>
            <td width="160"><? echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Select Company-", 0, "load_drop_down('requires/dry_production_controller', this.value, 'load_drop_down_location', 'location_td'); location_select();get_php_form_data( this.value, 'company_wise_report_button_setting','requires/dry_production_controller');"); ?>
                <input type="hidden" name="cbo_within_group" id="cbo_within_group" class="text_boxes" value="0" style="width:40px;" />
            </td>
            <td width="100" class="must_entry_caption">Location</td>
            <td width="160" id="location_td"><? echo create_drop_down("cbo_location", 150, $blank_array,"", 1,"-Select Location-", 0,""); ?></td>
             <td>Floor/Unit</td>
            <td id="floor_td"><? echo create_drop_down( "cbo_floor_id", 150, $blank_array,"", 1, "--Select Floor--", $selected, "",1 ); ?></td>
            
            <td style="display: none;">
                <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:140px;"  onDblClick="openmypage_batch();" readonly />
                <input type="hidden" name="txt_batch_id" id="txt_batch_id" class="text_boxes" value="0" style="width:40px;" />
            </td>
        </tr>
        <tr>
        	<td>Party Name</td>
            <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "--Select Party--", $selected, "",1 ); ?></td>

            <td  class="must_entry_caption" >Job No.</td>
            <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:140px;" onDblClick="openmypage_job();" placeholder="Browse"  readonly/>
                <input type="hidden" name="txt_job_id" id="txt_job_id" class="text_boxes" value="" style="width:60px;" /></td>
            <td>Order No.</td>
            <td>
                <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" value="" style="width:140px;" disabled placeholder="Display" />
                <input type="hidden" name="hid_order_id" id="hid_order_id" class="text_boxes" value="" style="width:60px;" />
            </td>
            
        </tr>
        <tr>
            <td>Order Qty</td>
            <td><input type="text" name="txt_order_qty" id="txt_order_qty" class="text_boxes_numeric" style="width:140px;" disabled placeholder="Display"/></td>
           
            <td>Machine</td>
            <td id="machine_td"><? echo create_drop_down( "cbo_machine_id", 150, $blank_array,"", 1, "--Select Machine--", $selected, "",1 ); ?></td>
        </tr>
    </table>
         </fieldset>
        <br />
        <fieldset style="width:850px;">
           <legend>Dry Production Details Info</legend>
             <table cellpadding="0" cellspacing="0" width="830" class="rpt_table" border="1" rules="all" id="tbl_item_details">
        	<thead>
            <tr>
                <th class="must_entry_caption">Date</th>
                <th><input type="text" name="txt_prod_date" id="txt_prod_date" class="datepicker" style="width:80px;" placeholder="Write" value="<?php
$currentDate = date('d-m-Y'); // Format: YYYY-MM-DD
echo $currentDate;
?>
" readonly/></th>
                <th class="must_entry_caption">Reporting Hour</th>
                <th>
                    <input name="txt_reporting_hour" id="txt_reporting_hour" class="text_boxes" style="width:90px" placeholder="24 Hour Format" onBlur="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyUp="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyPress="return numOnly(this,event,this.id);"  value="<?php $currentTime = date('H:i'); echo $currentTime;?>
" /></th>
                <th colspan="2">Operator / Superviser</th>
                <th colspan="2"><input type="text" name="txt_super_visor" id="txt_super_visor" class="text_boxes" onKeyUp="fn_autocomplete();" style="width:160px">
                </th>
                
                <th>Shift</th>
                <th> <? echo create_drop_down( "cboShift", 100, $shift_name,"", 1, 'Select', 0,"",'','','','','','','',''); ?></th>
            </tr>
            <tr>
                <th width="30">SL</th>
                <th width="100">Buyer Style</th>
                <th width="100">Buyer Po</th>
                <th width="100">Gmts Item</th>
                <th width="100">Gmts Color</th>
                <th width="80" class="must_entry_caption">Process Name</th>
                <th width="90" class="must_entry_caption">Wash Type</th>
                <th width="90">Order Qty</th>
                <th width="90">Actual Issue Qty</th>
                <th width="90">Prev Prod Qty.</th>
                <th class="must_entry_caption" width="80">
                  <input type="checkbox" name="round_down" onClick="val_roundup();" id="round_down" style="font-size: 11px;border-radius: 5px;line-height: 15px; cursor: pointer;"  />
                            <input type="checkbox" name="remove_del_value" onClick="remove_del_val();" id="remove_del_value" style="font-size: 11px;border-radius: 5px;line-height: 15px; cursor: pointer;"  />
                            <hr style="padding: 2px 0px;">
                Production Qty. (Pcs)</th>
                <th width="80">Reject Qty. (Pcs)</th>
                <th>Remarks</th>
            </tr>
        </thead> 
        <tbody id="wash_details_container">
            <tr name="tr[]" id="tr_1">
                <td align="center">1</td>
                <td><input type="text" name="txtBuyerStyle[]" id="txtBuyerStyle_1" class="text_boxes" style="width:90px" placeholder="Display" readonly /></td>
                <td><input type="text" name="txtBuyerPO[]" id="txtBuyerPO_1" class="text_boxes" style="width:90px" placeholder="Display" readonly/>
                <td><input type="text" name="txtGmtsItem[]" id="txtGmtsItem_1" class="text_boxes" style="width:100px" placeholder="Display" readonly/></td>
                <td><input type="text" name="txtGmtsColor[]" id="txtGmtsColor_1" class="text_boxes" style="width:90px" placeholder="Display" readonly/></td>
                <td><? 	
				echo create_drop_down( "txtProcesId_1", 80, $wash_type,"", 1, " Select Process", $selected, "type_select(this.value);" ,1,'2,3','','','','','',"txtProcesId[]"); ?></td
                ><td id="wash_type_td"><? echo create_drop_down( "txtWashType_1", 92, $blank_array,"", 1, " Select Process", $selected, "loadStock(this.value);" ,1,1,'','','','','',"txtWashType[]"); ?>
                </td>
                
                
                <td align="right"><input type="text" name="OrderQty[]" id="OrderQty_1" class="text_boxes_numeric" style="width:80px" readonly />
               
                <input type="hidden" name="prevdryqty[]" id="prevdryqty_1" class="text_boxes_numeric" style="width:80px" readonly />
                <input type="hidden" name="prevrejectQty[]" id="prevrejectQty_1" class="text_boxes_numeric" style="width:80px" readonly />
                <input type="hidden" name="prevreturnQty[]" id="prevreturnQty_1" class="text_boxes_numeric" style="width:80px" readonly />
                </td>
                
                 <td align="right"><input type="text" name="issueQty[]" id="issueQty_1" class="text_boxes_numeric" style="width:80px" readonly /></td>
                <td align="right" id="yourElementId"><input type="text" name="totalprevdryqty[]" id="totalprevdryqty_1" class="text_boxes_numeric" style="width:80px" readonly />
                </td>
                <td align="right"><input type="text" name="txtProdQty[]" id="txtProdQty_1" class="text_boxes_numeric" style="width:80px" placeholder="Write" onBlur="fnc_total_calculate(1);" /></td>
                <td align="right">
                    <input type="text" name="txtRejQty[]" id="txtRejQty_1" class="text_boxes_numeric" style="width:70px" placeholder="Write" onBlur="fnc_total_calculate(1);" />
                    <input type="hidden" name="hidOrderQty[]" id="hidOrderQty_1" class="text_boxes_numeric" style="width:70px" />
                </td>
                <td>
                    <input type="text" name="txtRemarks[]" id="txtRemarks_1" class="text_boxes" style="width:110px" placeholder="Write" />
                    <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_1" style="width:50px" />
                    <input type="hidden" name="txtbuyerPoId[]" id="txtBuyerPoId_1" style="width:50px" />
                    <input type="hidden" name="txtPoId[]" id="txtPoId_1" style="width:50px" />
                    <input type="hidden" name="colorSizeId[]" id="colorSizeId_1" style="width:50px" />
                </td>
            </tr>
        </tbody>
        <tfoot style="display: none">
            <tr class="tbl_bottom" name="tr_btm" id="tr_btm">
                <td colspan="7">Total:</td>
                <td align="center"><input type="text" name="txtTotProdQty" id="txtTotProdQty" class="text_boxes_numeric" style="width:80px" placeholder="Display" readonly /></td>
                <td align="center"><input type="text" name="txtTotRejQty" id="txtTotRejQty" class="text_boxes_numeric" style="width:70px" placeholder="Display" readonly /></td>
                <td>&nbsp;</td>
            </tr>
        </tfoot>
    </table>
    <table cellpadding="0" cellspacing="1" width="830">
            <tr>
                <td align="center" colspan="12" valign="middle" class="button_container">
                    <? echo load_submit_buttons($permission, "fnc_embel_entry", 0,0,"reset_form('dryProduction_1','list_fabric_desc_container*dry_production_list_conainer','','','');",1); ?>
                    <input type="button" name="print" id="print" value="Print" onClick="fnc_embel_entry(4)" style="width:100px;display:none;" class="formbuttonplasminus" /> 
                </td>	  
            </tr>
        </table>
         </fieldset>
          <br />
    </div>
    <div id="list_fabric_desc_container" style="max-height:500px; width:350px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
    <div style="clear:both"></div>
     <br />
    <div style="width:600px;" id="dry_production_list_conainer"></div>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
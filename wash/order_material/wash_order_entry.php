<?
/*--- ----------------------------------------- Comments
Purpose			: 	Wash Order Entry					
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
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Wash Order Entry Info", "../../", 1,1, $unicode,1,'');

//print_r($_SESSION['logic_erp']['data_arr'][295]);
//print_r($_SESSION['logic_erp']['data_arr'][296]);
//die;
 
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	<?
        $data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][295] );
        echo "var field_level_data= ". $data_arr . ";\n";

       echo  $mandatory_arr= $_SESSION['logic_erp']['mandatory_field'][295] ;
       // echo "var mandatory_data= ". $mandatory_arr . ";\n";
    ?>
   // alert(mandatory_data);

    
	function openmypage_job()
	{
		if ( form_validation('cbo_company_name','Company')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
		page_link='requires/wash_order_entry_controller.php?action=job_popup&data='+data;
		title='Embellishment Order Entry';
		

		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=990px, height=420px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemaildata=this.contentDoc.getElementById("selected_job").value;
			var ex_data=theemaildata.split('_');
			if (ex_data[1]!="")
			{//alert(theemail.value);

				freeze_window(5);
				get_php_form_data( ex_data[1], "load_php_data_to_form", "requires/wash_order_entry_controller" );
				var within_group = $('#cbo_within_group').val();
				if(within_group==2)
				{
					$('#txt_order_no').attr('disabled',false);
				}
				else if(within_group==1)
				{
					$('#txt_order_no').attr('disabled',true);
				}
				show_list_view(2+'_'+ex_data[1]+'_'+within_group+'_'+$("#update_id").val()+'_'+$("#txt_ex_rate").val(),'order_dtls_list_view','wash_details_container','requires/wash_order_entry_controller','setFilterGrid(\'list_view\',-1)');	
				
				if(within_group==2)
				{
					var uom = $('#cboUom_1').val();
					fnc_load_uom(2,uom);
				}
				/*else if(within_group==1)
				{
					var uom = $('#cboUom_1').val();
					fnc_load_uom(1,uom);
					//$('#txtOrderQuantity_1').attr('disabled',true);
				}*/
				$('#cbo_company_name').attr('disabled',true);
				$('#cbo_party_name').attr('disabled',true);		
				set_button_status(1, permission, 'fnc_job_order_entry',1);
				release_freezing();
			}
		}
	}

	function fnc_job_order_entry( operation )
	{
		var delete_master_info=0;
		//var process = $("#cbo_process_name").val();
		var cbo_within_group = $("#cbo_within_group").val();
		var rate_mandatory = $("#rate_mandatory").val();
		if(cbo_within_group==1)
		{	
			if ( form_validation('cbo_company_name*cbo_within_group*cbo_location_name*cbo_party_name*cbo_currency*txt_order_receive_date*cbo_gmts_type*txt_order_no*cbo_party_location','Company*Within Group*Location*Party*Currency*Order No*Order Receive Date*Gmts Type*Order No.*Party Location')==false )
			{
				return;
			}
		}
		else
		{
			if ( form_validation('cbo_company_name*cbo_within_group*cbo_location_name*cbo_party_name*cbo_currency*txt_order_receive_date*cbo_gmts_type','Company*Within Group*Location*Party*Currency*Order Receive Date*Gmts Type')==false )
			{
				return;
			}
		}
		if(operation==0)
		{
			var r=confirm("Are you sure?");	
			if(r==true)
			{
			}
			else
			{
				release_freezing();	
				return;
			}	
		}
		var row_num=$('#tbl_dtls_wash tbody tr').length;
		var data_all=""; var i=0; var selected_row=0;
		var data_delete="";  var a=0;
		// alert (data);

		for (var j=1; j<=row_num; j++)
		{
		   if(cbo_within_group==1)
			{
				if(rate_mandatory==1)
				{
					if (form_validation('txtbuyerPo_'+j+'*txtstyleRef_'+j+'*txtOrderDeliveryDate_'+j+'*cboGmtsItem_'+j+'*txtColor_'+j+'*txtOrderQuantity_'+j+'*cboUom_'+j+'*txtRate_'+j+'*txtAmount_'+j+'*txtDomAmount_'+j+'*hdnDtlsdata_'+j,'Buyer PO*Buyer Style Ref.*Delivery Date*Gmts. Item*Color*Order Qty*Order UOM*Rate*Amount*Dom. Amount*pop up data')==false)
					{
						return;
					}
				}
				else
				{
					if (form_validation('txtbuyerPo_'+j+'*txtstyleRef_'+j+'*txtOrderDeliveryDate_'+j+'*cboGmtsItem_'+j+'*txtColor_'+j+'*txtOrderQuantity_'+j+'*cboUom_'+j+'*hdnDtlsdata_'+j,'Buyer PO*Buyer Style Ref.*Delivery Date*Gmts. Item*Color*Order Qty*Order UOM*pop up data')==false)
					{
						return;
					}
				}
			}
			else
			{
				if(rate_mandatory==1)
				{
					if (form_validation('cboGmtsItem_'+j+'*txtColor_'+j+'*txtOrderQuantity_'+j+'*cboUom_'+j+'*txtRate_'+j+'*txtAmount_'+j+'*txtDomAmount_'+j+'*txtOrderDeliveryDate_'+j,'Gmts. Item*Color*Order Qty*Order UOM*Rate*Amount*Dom. Amount*Delivery Date')==false)
					{
						return;
					}
				}else{
					if (form_validation('cboGmtsItem_'+j+'*txtColor_'+j+'*txtOrderQuantity_'+j+'*cboUom_'+j+'*txtOrderDeliveryDate_'+j,'Gmts. Item*Color*Order Qty*Order UOM*Delivery Date')==false)
					{
						return;
					}
				}
				
			}  
			i++; 
			 //alert (data);
			var txtpartybuyername=encodeURIComponent(""+$('#txtpartybuyername_'+j).val()+"");
			data_all+="&txtbuyerPoId_" + i + "='" + $('#txtbuyerPoId_'+j).val()+"'"+"&txtbuyerPo_" + i + "='" + $('#txtbuyerPo_'+j).val()+"'"+"&txtstyleRef_" + i + "='" + $('#txtstyleRef_'+j).val()+"'"+"&cboGmtsItem_" + i + "='" + $('#cboGmtsItem_'+j).val()+"'"+"&txtColor_" + i + "='" + $('#txtColor_'+j).val()+"'"+"&txtColorId_" + i + "='" + $('#txtColorId_'+j).val()+"'"+"&txtSize_" + i + "='" + $('#txtSize_'+j).val()+"'"+"&txtSizeId_" + i + "='" + $('#txtSizeId_'+j).val()+"'"+"&txtOrderQuantity_" + i + "='" + $('#txtOrderQuantity_'+j).val()+"'"+"&hiddenOrderQuantity_" + i + "='" + $('#hiddenOrderQuantity_'+j).val()+"'"+"&cboUom_" + i + "='" + $('#cboUom_'+j).val()+"'"+"&txtRate_" + i + "='" + $('#txtRate_'+j).val()+"'"+"&txtAmount_" + i + "='" + $('#txtAmount_'+j).val()+"'"+"&txtDomAmount_" + i + "='" + $('#txtDomAmount_'+j).val()+"'"+"&txtSmv_" + i + "='" + $('#txtSmv_'+j).val()+"'"+"&txtOrderDeliveryDate_" + i + "='" + $('#txtOrderDeliveryDate_'+j).val()+"'"+"&txtWastage_" + i + "='" + $('#txtWastage_'+j).val()+"'"+"&txtremarks_" + i + "='" + $('#txtremarks_'+j).val()+"'"+"&hdnDtlsdata_" + i + "='" + $('#hdnDtlsdata_'+j).val()+"'"+"&hdnDtlsdataIds_" + i + "='" + $('#hdnDtlsdataIds_'+j).val()+"'"+"&hdnDtlsUpdateId_" + i + "='" + $('#hdnDtlsUpdateId_'+j).val()+"'"+"&txtpartybuyername_" + i + "='" +txtpartybuyername +"'"+"&hdnbookingDtlsId_" + i + "='" + $('#hdnbookingDtlsId_'+j).val()+"'";
		}
		var data="action=save_update_delete&operation="+operation+data_all+'&total_row='+i+get_submitted_data_string('txt_job_no*cbo_company_name*cbo_location_name*cbo_floor_name*cbo_within_group*cbo_party_name*cbo_party_location*cbo_currency*txt_ex_rate*txt_order_receive_date*txt_delivery_date*txt_rec_start_date*txt_rec_end_date*txt_order_no*hid_order_id*txt_converstion_factor*update_id*cbo_gmts_type*txt_deleted_id_dtls',"../../");
		 //alert (data); //return;
		freeze_window(operation);
		http.open("POST","requires/wash_order_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_job_order_entry_response;
	}

	function fnc_job_order_entry_response()
	{
		if(http.readyState == 4) 
		{
			//alert (http.responseText);//return;
			var response=trim(http.responseText).split('**');
			
			/*if(trim(response[0])=='washRec'){
				alert("Receive Found :"+trim(response[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			 }*/
			 
			 if(trim(response[0])=='pinumber'){
				alert("Export PI Found :"+trim(response[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			 }
			 
			 if(trim(response[0])=='washRecipe'){
				alert("Recipe Found :"+trim(response[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			 }
			if(response[0]==0 || response[0]==1)
			{
				document.getElementById('txt_job_no').value = response[1];
				document.getElementById('update_id').value = response[2];
				document.getElementById('txt_order_no').value = response[3];
				
				var within_group = $('#cbo_within_group').val();
				if(within_group==2)
				{
					$('#txt_order_no').attr('disabled',false);
				}
				else if(within_group==1)
				{
					$('#txt_order_no').attr('disabled',true);
				}
				//$('#txt_order_no').attr('disabled',true);
				$('#cbo_within_group').attr('disabled',true);
				$('#cbo_company_name').attr('disabled',true);
				$('#cbo_party_name').attr('disabled',true);
				
				show_list_view(2+'_'+response[1]+'_'+within_group+'_'+response[2]+'_'+$("#txt_ex_rate").val(),'order_dtls_list_view','wash_details_container','requires/wash_order_entry_controller','setFilterGrid(\'list_view\',-1)');
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

	function change_caption_n_uom(inc,process)
	{
		if(process == 2 || process == 3 || process == 4)
		{
			//$("#cbo_uom").val(12);
		}else{
			//$("#cbo_uom").val(2);
		}
		$('#cboUom_'+inc).attr('disabled',true);
		load_drop_down( 'requires/wash_order_entry_controller', process+'_'+inc, 'load_drop_down_embl_type', 'embltype_td_'+inc );
	}
	
	
	
	function fnc_load_uom(type,uom)
	{
		$('#order_uom_td').css('color','blue');
		/*var within_group=$('#cbo_within_group').val();
		if(within_group==2){
			if(uom==1){
				$('#order_uom_td').text('Rate/Pcs');
				$('#order_uom_td').css('color','blue');
			}if(uom==2){
				$('#order_uom_td').text('Rate');
				$('#order_uom_td').css('color','blue');
			}
		}*/
		/*else{
			var cboUom=$('#cboUom_1').val();
			if(cboUom==1){
				$('#order_uom_td').text('Rate/Pcs');
				$('#order_uom_td').css('color','blue');
			}if(cboUom==2){
				$('#order_uom_td').text('Rate');
				$('#order_uom_td').css('color','blue');
			}
		}*/
	}

	function fnc_load_party(type,within_group)
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
			$('#txt_ex_rate').attr('readonly',true);
			$('#txt_order_no').attr('readonly',true);
			$('#txt_order_no').attr('placeholder','Browse');
			
			$("#cbo_party_location").val(0);
			$('#cbo_party_location').attr('disabled',false);
			
			$("#cbo_currency").val(1);
			$("#txt_ex_rate").val(1);
			
			$('#cbo_currency').attr('disabled',true);
			$("#cboUom_1").val(2);
			$('#cboUom_1').attr('disabled',true);
			
			$('#td_party_location').css('color','blue');
			$('#buyerpo_td').css('color','blue');
			$('#buyerstyle_td').css('color','blue');
			$('#work_order_td').css('color','blue');
			//$('#color_td').css('color','blue');
			
			$('#txtpartybuyername_1').attr('readonly',true);
			$('#txtbuyerPo_1').attr('readonly',true);
			$('#txtstyleRef_1').attr('readonly',true);

			$('#txtColor_1').attr('readonly',true);
			$('#txtColor_1').attr('placeholder','Display');
			
			$('#txtSize_1').attr('readonly',true);
			$('#txtSize_1').attr('placeholder','Display');
			
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
			$('#txt_ex_rate').attr('disabled',true);
			$('#txt_ex_rate').attr('readonly',true);
			
			$('#cboUom_1').attr('disabled',false);
			
			$('#td_party_location').css('color','black');
			$('#buyerpo_td').css('color','black');
			$('#buyerstyle_td').css('color','black');
			$('#work_order_td').css('color','black');
			//$('#color_td').css('color','black');
			$('#txtbuyerPo_1').attr('readonly',false);
			$('#txtpartybuyername_1').attr('readonly',false);
			$('#txtstyleRef_1').attr('readonly',false);
			
			$('#txtColor_1').attr('readonly',false);
			$('#txtColor_1').attr('placeholder','Write');
			
			$('#txtSize_1').attr('readonly',false);
			$('#txtSize_1').attr('placeholder','Write');
			
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
		
		if(within_group==2)
		{
			var uom = $('#cboUom_1').val();
			fnc_load_uom(2,uom);
		}
		/*else if(within_group==1)
		{
			var uom = $('#cboUom_1').val();
			fnc_load_uom(1,uom);
			
		}*/
	}

	function openmypage_order()
	{
		var company = $('#cbo_company_name').val();
		var party_name = $('#cbo_party_name').val();
		var cbo_within_group = $('#cbo_within_group').val();
		if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name','Company*Within Group*Party')==false )
		{
			return;
		}
		else
		{
			var title = 'Order No. Pop-up';
			var page_link = 'requires/wash_order_entry_controller.php?company='+company+'&party_name='+party_name+'&cbo_within_group='+cbo_within_group+'&action=order_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=970px,height=420px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var theemaildata=this.contentDoc.getElementById("hidd_booking_data").value;
				//alert(theemaildata);
				var ex_data=theemaildata.split('_');
				if (theemaildata!="")
				{
					freeze_window(5);
					//14303_OG-EB-19-00065_2_
					$('#txt_order_no').val(ex_data[1]);
					$('#hid_order_id').val(ex_data[0]);
					$('#cbo_currency').val(ex_data[2]);
					$('#txt_ex_rate').val(ex_data[3]);
					
					$('#cbo_company_name').attr('disabled',true);
					$('#cbo_within_group').attr('disabled',true);
					$('#cbo_party_name').attr('disabled',true);
					$('#cbo_currency').attr('disabled',true);
					//get_php_form_data( theemail, "populate_data_from_search_popup", "requires/wash_order_entry_controller" );
					show_list_view(1+'_'+ex_data[1]+'_'+1+'_'+$("#txt_ex_rate").val(),'order_dtls_list_view','wash_details_container','requires/wash_order_entry_controller','setFilterGrid(\'list_view\',-1)');
					$('#txt_delivery_date').val($('#txtOrderDeliveryDate_1').val());
					//$('#txtOrderQuantity_1').attr('disabled',true);
					var cboUom=$('#cboUom_1').val();
					fnc_load_uom(2,cboUom);
					fnc_dom_amount_cal();
					release_freezing();
				}
			}
		}
	}
	
	function fnc_dom_amount_cal()
	{
		var row_num=$('#tbl_dtls_wash tbody tr').length;
		var exchange_rate=$('#txt_ex_rate').val()*1;
		for (var j=1; j<=row_num; j++)
		{
			var domAmount= ($('#txtAmount_'+j).val()*1)*exchange_rate;
			$('#txtDomAmount_'+j).val( number_format(domAmount,4,'.','' ) );
		}
	}

	function openmypage_order_rate(type,booking_dtls_id,row)
	{
		var withinGroup 	= $('#cbo_within_group').val();
		var rate 			= $('#txtRate_'+row).val()*1;
		var booking_po_id  	= $('#txtbuyerPoId_'+row).val();
		var buyerstyleRef  	= $('#txtstyleRef_'+row).val();
		var data_break 		=$('#hdnDtlsdata_'+row).val();
		var hdnDtlsUpdateId =$('#hdnDtlsUpdateId_'+row).val();
		var txtColorId 		=$('#txtColorId_'+row).val();
		var txtSizeId 		=$('#txtSizeId_'+row).val();
		var is_rate_mandatory=$('#rate_mandatory').val();
		var is_rate_disabled=$('#rate_disabled').val();
		
		var page_link = 'requires/wash_order_entry_controller.php?booking_dtls_id='+booking_dtls_id+'&data_break='+data_break+'&hdnDtlsUpdateId='+hdnDtlsUpdateId+'&booking_po_id='+booking_po_id+'&withinGroup='+withinGroup+'&is_rate_mandatory='+is_rate_mandatory+'&is_rate_disabled='+is_rate_disabled+'&buyerstyleRef='+buyerstyleRef+'&action=order_rate_popup';
		
		emailwindow=dhtmlmodal.open('EmailBox','iframe',page_link,'Quantity Details Popup', 'width=570px, height=400px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			$('#hdnDtlsdata_'+row).val('');
			var break_data=this.contentDoc.getElementById("hidden_break_tot_row").value; 
			var break_data_ids=this.contentDoc.getElementById("hidden_break_tot_row_ids").value; 
			var copy_for_all_po=this.contentDoc.getElementById("txt_copy_for_all_po").value; 
			var tot_rate=this.contentDoc.getElementById("txt_tot_rate").value;
			var amount=0;
			if(copy_for_all_po !=0 && copy_for_all_po !='' )
			{
				var row_num = $('#tbl_dtls_wash tbody tr').length;
				//var wastage=$('#txtWastage_1').val();
				if(copy_for_all_po==1)
				{
					for(i=row;i<=row_num;i++)
					{
						$('#hdnDtlsdata_'+i).val(break_data);
						$('#txtRate_'+i).val(tot_rate);
						amount=($('#txtOrderQuantity_'+i).val()*1)*tot_rate;
						$('#txtAmount_'+i).val( number_format(amount,4,'.','' ) );
					}
				}
				else if(copy_for_all_po==2)
				{
					for(i=row;i<=row_num;i++)
					{
						var colorId =$('#txtColorId_'+i).val();
						if(colorId==txtColorId){
							$('#hdnDtlsdata_'+i).val(break_data);
							$('#txtRate_'+i).val(tot_rate);
							amount=($('#txtOrderQuantity_'+i).val()*1)*tot_rate;
							$('#txtAmount_'+i).val( number_format(amount,4,'.','' ) );
						}
					}
				}
				else if(copy_for_all_po==3)
				{
					for(i=row;i<=row_num;i++)
					{
						var sizeId 	=$('#txtSizeId_'+i).val();
						if(sizeId==txtSizeId){
							$('#hdnDtlsdata_'+i).val(break_data);
							$('#txtRate_'+i).val(tot_rate);
							amount=($('#txtOrderQuantity_'+i).val()*1)*tot_rate;
							$('#txtAmount_'+i).val( number_format(amount,4,'.','' ) );
						}
					}
				}
				else if(copy_for_all_po==4)
				{
					for(i=row;i<=row_num;i++)
					{
						
						var styleRef 	=$('#txtstyleRef_'+i).val();
						
						//alert(styleRef);
						//alert(buyerstyleRef);
						
						
						if(styleRef==buyerstyleRef)
						{
							$('#hdnDtlsdata_'+i).val(break_data);
							$('#txtRate_'+i).val(tot_rate);
							amount=($('#txtOrderQuantity_'+i).val()*1)*tot_rate;
							$('#txtAmount_'+i).val( number_format(amount,4,'.','' ) );
						}
					}
				}
				else
				{

				}
			}
			else
			{
				$('#hdnDtlsdata_'+row).val(break_data);
				$('#txtRate_'+row).val(tot_rate);
				amount=($('#txtOrderQuantity_'+row).val()*1)*tot_rate;
				$('#txtAmount_'+row).val( number_format(amount,4,'.','' ) );
			}
			$('#hdnDtlsdataIds_'+row).val(break_data_ids);
			
			/*if(withinGroup==2)
			{
				var cboUom = $('#cboUom_'+row).val();
				var OrderQuantity = $('#txtOrderQuantity_'+row).val();
				if(cboUom==2)
				{
					var qty_pcs=(OrderQuantity*1)*12;
				}
				else if(cboUom==1)
				{
					var qty_pcs=OrderQuantity*1;
				}else
				{
					var qty_pcs=0;
				}
				$("#txtQtyPcs_"+row).val(qty_pcs);
 				$('#cboUom_'+row).attr('disabled',true);
				$('#cbo_currency').attr('disabled',true);
			}*/
			fnc_dom_amount_cal();
		}		
	}
	
	function fnResetForm() 
	{
        set_button_status(0, permission, 'fnc_job_order_entry', 1);
		//reset_form('washorderentry_1','','','cbo_within_group,1*cbo_currency,1*cboUom_1*2',"disable_enable_fields('txt_booking_no*txt_batch_color*cboPoNo_1*cboItemDesc_1*cboDiaWidthType_1*txtRollNo_1*hideRollNo_1*txtBatchQnty_1*hide_job_no',0)'); $('#txt_ext_no').val(''); $('#txt_ext_no').attr('disabled','disabled');$('#txt_batch_number').removeAttr('readOnly','readOnly');$('#tbl_item_details tbody tr:not(:first)').remove();
		reset_form('washorderentry_1','','','cbo_within_group,1*cbo_currency,1*cboUom_1,2','','');
		$('#tbl_dtls_emb tbody tr:not(:first)').remove();
		$('#cbo_company_name').attr('disabled',false);
		$('#cbo_within_group').attr('disabled',false);
		$('#cbo_party_name').attr('disabled',false);
		$('#txt_order_no').attr('disabled',false);
		
		$('#cboGmtsItem_1').attr('disabled',false);
		$('#cboProcessName_1').attr('disabled',false);
		$('#cbotype_1').attr('disabled',false);
		$('#cboBodyPart_1').attr('disabled',false);
    }
	
	function location_select()
	{
		if($('#cbo_location_name option').length==2)
		{
			if($('#cbo_location_name option:first').val()==0)
			{
				$('#cbo_location_name').val($('#cbo_location_name option:last').val());
				//eval($('#cbo_location_name').attr('onchange')); 
			}
		}
		else if($('#cbo_location_name option').length==1)
		{
			$('#cbo_location_name').val($('#cbo_location_name option:last').val());
			//eval($('#cbo_location_name').attr('onchange'));
			
		}	
		load_drop_down( 'requires/wash_order_entry_controller', document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_floor', 'floor_td');
	}
	/*function date_delevery()
	{
		$('#txt_delivery_date').val($('#txtOrderDeliveryDate_1').val());
	}*/
	function openmypage_remarks(id)
	{
		var data=document.getElementById('txtremarks_'+id).value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/wash_order_entry_controller.php?data='+data+'&action=remarks_popup','Remarks Popup', 'width=450px,height=320px,center=1,resize=1,scrolling=0','../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("text_new_remarks").value;//Access form field with id="emailfield"
			if (theemail!="")
			{
				$('#txtremarks_'+id).val(theemail);
			}
		}
	}

	

	function add_dtls_tr(i) 
	{
		var within_group = $('#cbo_within_group').val();
		if(within_group==1)
		{
			alert('This feature is use for Within Group "No" only '); return;
		}
		else
		{
			var row_num=$('#tbl_dtls_wash tbody tr').length;
			if (row_num!=i)
			{
				return false;
			}
			else
			{
				i++;
				$("#tbl_dtls_wash tbody tr:last").clone().find("input,select").each(function() 
				{
					$(this).attr(
					{
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						'name': function(_, name) { return name },
						'value': function(_, value) { return value } 
					}); 
				}).end().appendTo("#tbl_dtls_wash tbody");

				var gmtItem_id=$('#cboGmtsItem_'+Number(i-1)).val();
				var uom_id=$('#cboUom_'+Number(i-1)).val();
				var hdnDtlsdata=$('#hdnDtlsdata_'+Number(i-1)).val();
				//console.log(uom_id);
				$('#txtSize_'+i).val('');
				$('#cboGmtsItem_'+i).val(gmtItem_id);
				$('#cboUom_'+i).val(uom_id);
				$('#hdnDtlsdata_'+i).val(hdnDtlsdata);

				$("#tbl_dtls_wash tbody tr:last").removeAttr('id').attr('id','row_'+i);
				$('#increase_'+i).removeAttr("onClick").attr("onClick","add_dtls_tr("+i+");");
				$('#decrease_'+i).removeAttr("onClick").attr("onClick","fnc_delet_dtls_tr("+i+");");
				//$('#txtRate_' + i).removeAttr("onClick").attr("onClick", "openmypage_order_rate(1,0,"+i+");");
				$('#txtRate_'+i).removeAttr("onClick").attr("onClick","openmypage_order_rate("+1+","+'0'+","+i+")"); 
				$('#txtOrderQuantity_'+i).removeAttr("onBlur").attr("onBlur","calculate_amount("+i+")");
				$('#btnremarks_'+i).attr("onClick","openmypage_remarks("+i+");");
				$('#txtOrderDeliveryDate_'+i).removeAttr("class").attr("class","datepicker");
				//$('#countName_' + i).removeAttr("onchange").attr("onchange", "check_validation(" + i + ",this.id);");
 				$('#txtOrderDeliveryDate_' + i).removeAttr("onchange").attr("onchange", "calculate_date("+i+")"); // this.value
				$('#txtRate_' + i).val( '' );
				$('#txtAmount_' + i).val( '' );
				$('#txtDomAmount_' + i).val( '' );
				$('#hdnDtlsUpdateId_' + i).val( '' );
				$('#hdnbookingDtlsId_' + i).val( '' );
				$('#hiddenOrderQuantity_' + i).val( '' );
				$('#txtOrderQuantity_' + i).val( '' );
				$('#receiveQuantity_' + i).val( '' );
 				load_wastage();
				set_all_onclick();
			}
		}
	}
	
	function fnc_delet_dtls_tr(i)
	{ 
		var selected_delete_id = new Array();
		var templatedata=$('#txt_deleted_id_dtls').val();
		var within_group = $('#cbo_within_group').val();
		var details_update_id = $('#hdnDtlsUpdateId_'+i).val();
		if(within_group==1)
		{
			alert('This feature is use for Within Group "No" only '); return;
		}
		else
		{
			var numRow = $('#tbl_dtls_wash tbody tr').length;
			if(numRow==i && i!=1)
			{
				if(details_update_id!='')
				{
					if(templatedata=='') templatedata=details_update_id; else templatedata=templatedata+','+details_update_id;
					$('#txt_deleted_id_dtls').val( templatedata );
				}				
				$('#tbl_dtls_wash tbody tr:last').remove();
			}			
		}
		/*
		for( var i = 0; i < selected_delete_id.length; i++ ) {
			templatedata += selected_delete_id[i] + ',';
		}
		templatedata = templatedata.substr( 0, templatedata.length - 1 );*/
		//$('#txt_deleted_id_dtls').val( templatedata );
	}
	
function calculate_date(i)
{
	var DeliveryDate=$("#txtOrderDeliveryDate_"+i).val();
	var order_receive_date=$("#txt_order_receive_date").val();
	
	var datediff = date_compare(order_receive_date,DeliveryDate);//date_diff('d', txt_po_received_date, txt_pub_shipment_date);
		//alert(datediff);
		if(datediff==false)
		{
			alert("Delivery date can not be less than Order received date.");
			$("#txtOrderDeliveryDate_"+i).val("");
			return;
		}

}



function calculate_amount(i)
{
	//alert(i); return;
	var txtOrderQuantity=$("#txtOrderQuantity_"+i).val()*1;
	var hiddenOrderQuantity=$("#hiddenOrderQuantity_"+i).val()*1;
	var receive_Quantity=$("#receiveQuantity_"+i).val()*1;
	var txtRate=$("#txtRate_"+i).val()*1;
	var converstion_factor=$("#txt_converstion_factor").val()*1;	var converstion_factor=$("#txt_converstion_factor").val()*1;
	var cboUom=$("#cboUom_"+i).val()*1;
	
	
	
	 
	if(cboUom==1)
	{
		var receiveQuantity=receive_Quantity*1;
		var order_amount=(txtOrderQuantity*txtRate)*1;
		var order_domistic_amount=order_amount*converstion_factor;
		var qty_pcs=(txtOrderQuantity*1);
		$("#txtQtyPcs_"+i).val(qty_pcs);
	}
	else if(cboUom==2)
	{
		var receiveQuantity=receive_Quantity/12;
		var order_amount=(txtOrderQuantity*txtRate)*1;
		var order_domistic_amount=order_amount*converstion_factor;
		var qty_pcs=(txtOrderQuantity*1)*12;
		$("#txtQtyPcs_"+i).val(qty_pcs);
	}
	
	
	if(txtOrderQuantity<receiveQuantity)
	{
		alert("Order Qty  Not Allow Less Then Receive Quantity");
		$("#txtOrderQuantity_"+i).val(hiddenOrderQuantity) ;
		var order_amount=(hiddenOrderQuantity*txtRate)*1;
		var order_domistic_amount=order_amount*converstion_factor;
		$("#txtAmount_"+i).val( number_format (order_amount, 4,'.' , ""));
		$("#txtDomAmount_"+i).val( number_format (order_domistic_amount, 4,'.' , ""));
		//$("#update1").attr("disabled","disabled");	
		return;
	}
	else
	{
		//$('#update1').removeAttr("disabled");	
		$("#txtAmount_"+i).val( number_format (order_amount, 4,'.' , ""));
		$("#txtDomAmount_"+i).val( number_format (order_domistic_amount, 4,'.' , ""));
	}	
	
}

function check_exchange_rate()
{
	
	var cbo_currency=$('#cbo_currency').val();
	var txt_order_receive_date = $('#txt_order_receive_date').val();
	var cbo_company_name = $('#cbo_company_name').val();
	var response=return_global_ajax_value( cbo_currency+"**"+txt_order_receive_date+"**"+cbo_company_name, 'check_conversion_rate', '', 'requires/wash_order_entry_controller');
	//console.log(response);
	var response=response.split("_");
	$('#txt_ex_rate').val(response[1]);
}

function check_rate_activity()
{
	var cbo_company_name = $('#cbo_company_name').val();
	var response=return_global_ajax_value( cbo_company_name, 'check_popup_rate_mandatory', '', 'requires/wash_order_entry_controller');
	var disabled_response=return_global_ajax_value( cbo_company_name, 'check_popup_rate_disabled', '', 'requires/wash_order_entry_controller');
	//console.log(response);
	//var response=response.split("_");
	if(response!=''){
		$('#rate_mandatory').val(response);
	}

	if(disabled_response!=''){
		$('#rate_disabled').val(disabled_response);
	}
}


function load_wastage()
{
	//alert(val);
	var row_num = $('#tbl_dtls_wash tbody tr').length;
	if(document.getElementById('is_copy').checked==true){
		var wastage=$('#txtWastage_1').val();
		for(i=1;i<=row_num;i++)
		{
			$('#txtWastage_'+i).val(wastage);
		}
	}else{
		for(i=1;i<=row_num;i++)
		{
			$('#txtWastage_'+i).val('');
		}
	}
}


</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission); ?>
        <form name="washorderentry_1" id="washorderentry_1" autocomplete="off"> 
			<fieldset style="width:1250px;">
			<legend>Wash Order Entry</legend>
                <table width="1230" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                    <tr>
                        <td colspan="3" align="right"><strong>Job No</strong></td>
                        <td colspan="2">
                            <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes_numeric" style="width:90px" readonly />
                            <input type="hidden" name="txt_deleted_id_dtls" id="txt_deleted_id_dtls" class="text_boxes_numeric" style="width:90px" readonly />
                            <input class="text_boxes"  type="text" name="txt_job_no" id="txt_job_no" onDblClick="openmypage_job();" placeholder="Double Click" style="width:140px;" readonly />
                        </td>
                        <td id="image_1"><input type="button" class="image_uploader" name="txtFile_1" id="txtFile_1" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'aoporderentry_1', 0 ,1)" style="width:150px;" value="ADD / VIEW IMAGE">
                        </td>
                    </tr>
                    <tr>
                        <td width="110" class="must_entry_caption">Company Name </td>
                        <td width="160"><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/wash_order_entry_controller', this.value+'_'+1, 'load_drop_down_location', 'location_td'); location_select(); check_rate_activity(); fnc_load_party(1,document.getElementById('cbo_within_group').value);setFieldLevelAccess(this.value)"); ?>
                        </td>
                        <td width="110" class="must_entry_caption">Location Name</td>
                        <td width="160" id="location_td"><? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "" ); ?></td>

                        <td width="110" >Floor/Unit</td>
                        <td width="160" id="floor_td"><? echo create_drop_down( "cbo_floor_name", 150, $blank_array,"", 1, "-- Select Floor --", $selected, "" ); ?></td>
                        
                    </tr>
                    <tr>
                    	<td width="110" class="must_entry_caption">Within Group</td>
                        <td><?php echo create_drop_down( "cbo_within_group", 150, $yes_no,"", 0, "--  --", 0, "fnc_load_party(1,this.value); " ); ?></td>
                        <td class="must_entry_caption">Party</td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_load_party(2,document.getElementById('cbo_within_group').value);"); ?></td>
                        <td id="td_party_location">Party Location</td>
                        <td id="party_location_td"><? echo create_drop_down( "cbo_party_location", 150, $blank_array,"", 1, "-- Select Location --", $selected, "",1 ); ?></td>
                        
                    </tr> 
                    <tr>
                    	<td class="must_entry_caption">Ord. Receive Date</td>
                        <td><input type="text" name="txt_order_receive_date"  style="width:140px"  id="txt_order_receive_date" class="datepicker"  value="<? echo date("d-m-Y")?>" readonly/></td>
                    	<td class="must_entry_caption">Gmts Type</td>
                        <td><? 
							echo create_drop_down( "cbo_gmts_type", 150, $wash_gmts_type_array,"", 1, "-- Select Type --", $selected, "" ); ?></td>
                        <td  style="display:none" class="must_entry_caption">Delivery Date</td>
                        <td style="display:none"><input type="text" name="txt_delivery_date" id="txt_delivery_date"  style="width:140px"  class="datepicker" value="" readonly/> </td>

                        <td  class="must_entry_caption">Currency / E. Rate</td>
                        <td><? echo create_drop_down( "cbo_currency", 70, $currency,"", 1, "-- Select Currency --",1,"check_exchange_rate();", 1,"" ); ?> / <input type="text" name="txt_ex_rate" id="txt_ex_rate" style="width:55px" class="text_boxes_numeric" value="1" readonly /></td>
                       
                    </tr>
                    <tr>
                    	<td id="work_order_td" class="must_entry_caption"><strong>Work Order</strong></td>
                        <td><input name="txt_order_no" id="txt_order_no" type="text"  class="text_boxes" style="width:140px" placeholder="Browse" onDblClick="openmypage_order();" readonly />
                            <input type="hidden" name="hid_order_id" id="hid_order_id">
                            <input type="hidden" name="update_id" id="update_id">
                            <input type="hidden" name="rate_mandatory" id="rate_mandatory" value="0">
                            <input type="hidden" name="rate_disabled" id="rate_disabled" value="0">
                        </td>
                    	 <td>Rcv. Start Date</td>
                        <td><input type="text" name="txt_rec_start_date" id="txt_rec_start_date" style="width:150px" class="datepicker" value="" placeholder="Material Rcv. Start Date" readonly/></td>
                    	<td>Rcv. End Date</td>
                        <td><input type="text" name="txt_rec_end_date" id="txt_rec_end_date" style="width:140px" class="datepicker" value="" placeholder="Material Rcv. End Date" readonly/></td>
                        
                        <td style="display: none;"><input type="text" name="txt_converstion_factor" id="txt_converstion_factor"  style="width:140px"  class="text_boxes_numeric" value="1" readonly /></td>
                    </tr> 
                </table>
        </fieldset> 
        <fieldset style="width:1280px;">
           <legend>Wash Order Entry Details</legend>
                <table cellpadding="0" cellspacing="2" border="0" class="rpt_table" rules="all" id="tbl_dtls_wash">
                    <thead class="form_table_header">
                    	<th width="90" id="party_td">Party Buyer Name</th>
                        <th width="90" id="buyerpo_td">Buyer PO</th>
                        <th width="90" id="buyerstyle_td">Buyer Style Ref.</th>
                        <th width="90" class="must_entry_caption">Gmts. Item</th>
                        <th width="90" id="color_td" class="must_entry_caption" >Color</th>
                        <th width="70" >Size</th>
                        <th width="70" class="must_entry_caption">Order Qty</th>
                        <th width="60" class="must_entry_caption" >Order UOM</th>
                        <th width="60" class="must_entry_caption" id="order_uom_td">Rate</th>
                        <th width="80" class="must_entry_caption">Amount</th>
                        <th width="80" class="must_entry_caption">Dom. Amount</th>
                        <th width="80" class="must_entry_caption">Quantity (Pcs)</th>
                        <th width="50">SMV</th>
                        <th width="60" class="must_entry_caption">Delivery Date</th>
                        <th width="50">Wastage %<input type="checkbox" name="is_copy" id="is_copy" onClick="load_wastage()" /></th>
                        <th>RMK</th>
                        <th></th>
                    </thead>
                    <tbody id="wash_details_container">
                        <tr>
                        	<td><input name="txtpartybuyername_1" id="txtpartybuyername_1" type="text" class="text_boxes" style="width:80px" placeholder="Display" readonly /></td>
                            <td><input name="txtbuyerPo_1" id="txtbuyerPo_1" type="text" class="text_boxes" style="width:80px" placeholder="Display" readonly />
                            	<input name="txtbuyerPoId_1" id="txtbuyerPoId_1" type="hidden" class="text_boxes" style="width:70px" />
                            </td>
                            <td><input name="txtstyleRef_1" id="txtstyleRef_1" type="text" class="text_boxes" style="width:80px" placeholder="Display" readonly /></td>
                            <td><? 
						
							echo create_drop_down( "cboGmtsItem_1", 90, $garments_item,"", 1, "-- Select --",$selected, "",0,"" ); ?></td>
                            <td>
                            	<input name="txtColor_1" id="txtColor_1" type="text" class="text_boxes" style="width:80px" readonly placeholder="Display" />
                            	<input name="txtColorId_1" id="txtColorId_1" type="hidden" class="text_boxes" style="width:50px" />
                            </td>
                            <td>
                            	<input name="txtSize_1" id="txtSize_1" type="text" class="text_boxes" style="width:60px" readonly placeholder="Display" />
                            	<input name="txtSizeId_1" id="txtSizeId_1" type="hidden" class="text_boxes" style="width:50px" />
                            </td>
                            <td><input name="txtOrderQuantity_1" id="txtOrderQuantity_1" class="text_boxes_numeric" type="text"  style="width:60px" onBlur="calculate_amount(1)"  readonly/></td>
                            <td><? echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 0, "-- Select --",$selected,"fnc_load_uom(2,this.value);", 1,"2,1" ); ?></td>
                            <td><input name="txtRate_1" id="txtRate_1" type="text"  class="text_boxes_numeric" style="width:50px" onClick="openmypage_order_rate(1,'0',1)" placeholder="Browse" readonly /></td>
                            <td><input name="txtAmount_1" id="txtAmount_1" type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td> 
                            <td><input name="txtDomAmount_1" id="txtDomAmount_1" type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td>
                            <td><input name="txtQtyPcs_1" id="txtQtyPcs_1" type="text"  class="text_boxes_numeric" style="width:67px" readonly /></td>  
                            <td><input name="txtSmv_1" id="txtSmv_1" type="text"  class="text_boxes_numeric" style="width:40px" /></td> 
                            <td><input type="text" name="txtOrderDeliveryDate_1" id="txtOrderDeliveryDate_1" class="datepicker" style="width:50px" onChange="calculate_date(1)" readonly /></td>
                            <td>
                                <input name="txtWastage_1" id="txtWastage_1" type="text"  class="text_boxes_numeric" style="width:40px" />
                                <input type="hidden" name="hdnDtlsUpdateId_1" id="hdnDtlsUpdateId_1">
                                <input type="hidden" name="hdnDtlsdata_1" id="hdnDtlsdata_1">
                                <input type="hidden" name="hdnbookingDtlsId_1" id="hdnbookingDtlsId_1">
                            </td>
                            <td><input type="button" name="btnremarks_1" id="btnremarks_1" class="formbuttonplasminus" value="RMK" onClick="openmypage_remarks(1);" />
                            	<input type="hidden" name="txtremarks_1" id="txtremarks_1" class="text_boxes" />
                            </td>
                            <td width="65">
								<input type="button" id="increase_1" name="increase[]" style="width:18px" class="formbuttonplasminus" value="+" onClick="add_dtls_tr(1)" />
								<input type="button" id="decrease_1" name="decrease[]" style="width:18px" class="formbuttonplasminus" value="-" onClick="fnc_delet_dtls_tr(1);" />
							</td>
                        </tr>                     
                    </tbody>
                </table>
                <table width="1280" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td align="center" colspan="11" class="button_container">
                        	<? echo load_submit_buttons( $permission, "fnc_job_order_entry", 0,0,"fnResetForm();",1); ?>
                        </td>
                    </tr>   
                </table>
            </fieldset> 
        </form>                         
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Embellishment Material Issue					
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	17-09-2018
Updated by 		: 		
Update date		:
Oracle Convert 	:		
Convert date	: 	
Delivery Performed BY	:		
Delivery Date			:	
Comments		:
*/
session_start(); 
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Embellishment Material Delivery [Bundle] Info", "../../", 1,1, $unicode,1,'');

?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	var tableFilters = 
	{
		col_0: "none",
	}

	/*var str_color = [<? //echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name", "color_name" ), 0, -1); ?>];

	$(document).ready(function(e)
	 {
            $("#txt_color").autocomplete({
			 source: str_color
		  });

            $("#cbo_gmts_material_description").hide();
     });

	var str_material_description = [<? //echo substr(return_library_autocomplete( "select material_description from sub_material_dtls group by material_description ", "material_description" ), 0, -1); ?> ];

	function set_auto_complete(type)
	{
		if(type=='subcon_material_receive')
		{
			$("#txt_material_description").autocomplete({
			source: str_material_description
			});
		}
	}*/

	var str_size = [<? echo substr(return_library_autocomplete( "select size_name from lib_size group by size_name ", "size_name" ), 0, -1); ?> ];

	function set_auto_complete_size(type)
	{
		if(type=='size_return')
		{
			$(".txt_size").autocomplete({
			source: str_size
			});
		}
	}

	function openmypage_production_id()
	{ 
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
		var page_link='requires/embellishment_delivery_bundle_controller.php?action=production_popup&data='+data;
		var title="Delivery ID Popup Info";
		
		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=900px, height=400px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_subcontract_frm"); //Access the form inside the modal window
			//var theemail=this.contentDoc.getElementById("selected_job");
			var theemail=this.contentDoc.getElementById("selected_job").value;
			//alert (theemail); 
			var splt_val=theemail.split("_");
			if (splt_val[0]!="")
			{
				freeze_window(5);
				reset_form('','','txt_delivery_no*cbo_company_name*cbo_location_name*cbo_party_name*txt_issue_challan*txt_delivery_date*cbo_within_group*txt_job_no*update_id','','');
				get_php_form_data( splt_val[0], "load_php_data_to_form", "requires/embellishment_delivery_bundle_controller" );
				
				var list_view_orders = return_global_ajax_value( splt_val[0]+'**'+splt_val[1]+'**'+1, 'load_php_dtls_form', '', 'requires/embellishment_delivery_bundle_controller');
				if(list_view_orders!='')
				{
					$("#rec_issue_table tr").remove();
					$("#rec_issue_table").append(list_view_orders);
				}
				fnc_total_calculate();
				set_button_status(1, permission, 'fnc_material_delivery',1);
				release_freezing();
			}
		}
	}

	function job_search_popup()
	{
		if ( form_validation('cbo_company_name*cbo_party_name','Company Name*Party Name')==false )
		{
			return;
		}
		else
		{
			var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
			var page_link='requires/embellishment_delivery_bundle_controller.php?action=job_popup&data='+data;
			var title='Job No Popup Info';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				freeze_window(5);
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("selected_order").value;
				$("#txt_job_no").val( theemail );
				
				var list_view_orders = return_global_ajax_value( 0+'**'+theemail+'**'+1, 'load_php_dtls_form', '', 'requires/embellishment_delivery_bundle_controller');
				if(list_view_orders!='')
				{
					$("#rec_issue_table tr").remove();
					$("#rec_issue_table").append(list_view_orders);
				}
				fnc_total_calculate();
				$('#cbo_company_name').attr('disabled','disabled');
				$('#cbo_party_name').attr('disabled','disabled');
				release_freezing();
			}
		}
	}

	function fnc_material_delivery( operation )
	{
		if ( form_validation('cbo_company_name*txt_delivery_date', 'Company Name*delivery Date')==false )
		{
			return;
		}
		else
		{
			var dammy_row = document.getElementById('dammy_row');
			if (typeof(dammy_row) != 'undefined' && dammy_row != null) {
				alert("No data found.");
				return;
			}
		        var data_str="";
				
				var data_str=get_submitted_data_string('txt_delivery_no*cbo_company_name*txt_delivery_date*update_id*txt_remarks*cbo_within_group*txt_delivery_point*cbo_floor',"../../");
				var tot_row=$('#table_body tbody tr:not(.fltrow)').length;
				var k=0;
				var data_all=""; var i=0; var selected_row=0;var data_delete="";
				//var tbaleId= 'details_tbl tbody tr';
				//var j=0; var check_field=0; data_all="";
				for (var j=1; j<=tot_row; j++)
				{
					var updateIdDtls=$('#updatedtlsid_'+j).val();
					if($('#barcodeChkbox_'+j).is(':checked'))
					{
						
						if($('#txtProdQty_'+j).val()=='' || $('#txtProdQty_'+j).val()==0)
						{
							alert('Please Fill up Prod. Qty.'); return; 
						}
						else
						{
							i++;
							data_all+="&woID_" + i + "='" + $('#woID_'+j).val()+"'"+"&woDtlsID_" + i + "='" + $('#woDtlsID_'+j).val()+"'"+"&woBreakID_" + i + "='" + $('#woBreakID_'+j).val()+"'"+"&rcvID_" + i + "='" + $('#rcvID_'+j).val()+"'"+"&rcvDtlsID_" + i + "='" + $('#rcvDtlsID_'+j).val()+"'"+"&bundleMstID_" + i + "='" + $('#bundleMstID_'+j).val()+"'"+"&bundleDtlsID_" + i + "='" + $('#bundleDtlsID_'+j).val()+"'"+"&issueDtlsID_" + i + "='" + $('#issueDtlsID_'+j).val()+"'"+"&cboCompanyId_" + i + "='" + $('#cboCompanyId_'+j).val()+"'"+"&txtRcvDate_" + i + "='" + $('#txtRcvDate_'+j).val()+"'"+"&txtIssueCh_" + i + "='" + $('#txtIssueCh_'+j).val()+"'"+"&txtProdQty_" + i + "='" + $('#txtProdQty_'+j).val()+"'"+"&productionDtlsIds_" + i + "='" + $('#productionDtlsIds_'+j).val()+"'"+"&qcDtlsIds_" + i + "='" + $('#qcDtlsIds_'+j).val()+"'"+"&txtRejQty_" + i + "='" + $('#txtRejQty_'+j).val()+"'"+"&hdnDtlsdata_" + i + "='" + $('#hdnDtlsdata_'+j).val()+"'"+"&txtQcQty_" + i + "='" + $('#txtQcQty_'+j).val()+"'"+"&txtRemark_" + i + "='" + $('#txtRemark_'+j).val()+"'"+"&updatedtlsid_" + i + "='" + $('#updatedtlsid_'+j).val()+"'"+"&txtBarcodeNo_" + i + "='" + $('#txtBarcodeNo_'+j).val()+"'";
						}
						//alert(11); return;
						//productionDtlsIds txtRejQty hdnDtlsdata txtQcQty txtRemark qcDtlsIds
					}
					
					if(updateIdDtls!="" && $('#barcodeChkbox_'+j).is(':not(:checked)'))
					{
						data_delete+=$('#qcDtlsIds_'+j).val()+",";
 					}
				}
				
				//var data="action=save_update_delete&operation="+operation+'&total_row='+i+data_all+data_str;//+'&zero_val='+zero_val
				
				var data="action=save_update_delete&operation="+operation+'&total_row='+i+'&data_delete='+data_delete+data_all+data_str;//+'&zero_val='+zero_val
				//alert (data); return;
				freeze_window(operation);
				http.open("POST","requires/embellishment_delivery_bundle_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_material_qc_response;
			
		}
	}
	
	function fnc_material_qc_response()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
			if(response[0]==121) // for duplicate barcode chk
			{
				alert(response[1]);
				release_freezing();
				return;
			}
			show_msg(response[0]);
			//$('#cbo_uom').val(12);
			if(response[0]==0 || response[0]==1 || response[0]==2)
			{
				document.getElementById('txt_delivery_no').value= response[1];
				document.getElementById('update_id').value = response[2];
				//alert();

				//show_list_view(response[2],'issue_item_details_update','search_div','requires/embellishment_delivery_bundle_controller','setFilterGrid("rec_issue_table",-1)');
				//var tot_row=$('#details_tbl tbody tr').length;
				
				var within_group = document.getElementById('cbo_within_group').value;
				if(within_group==1)
				{
 				  show_list_view(response[2],'issue_item_details_update_bundle','search_div','requires/embellishment_delivery_bundle_controller','setFilterGrid("rec_issue_table",-1)');
  				}
				else
				{
 					show_list_view(response[2],'issue_item_details_update','search_div','requires/embellishment_delivery_bundle_controller','setFilterGrid("rec_issue_table",-1)');
				}
				var tot_row=$('#table_body tbody tr:not(.fltrow)').length;
				if(tot_row>0)
				{
					fnc_total_calculate();
					set_button_status(1, permission, 'fnc_material_delivery',1);
				}
				else
				{
					set_button_status(0, permission, 'fnc_material_delivery',1);
				}
			}
			 
			release_freezing();
		}
	}

	function fnc_embl_delivery(operation){
      if(operation==1){
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_location_name').val(), "embl_delivery_bundle_entry_print", "requires/embellishment_delivery_bundle_controller") 
		//return;
		show_msg("3");
	  }
	  else if(operation==2){
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_location_name').val(), "embl_delivery_bundle_entry_print_2", "requires/embellishment_delivery_bundle_controller") 
		//return;
		show_msg("3");
	  }
	  else if(operation==3){
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_location_name').val(), "embl_delivery_bundle_entry_print_3", "requires/embellishment_delivery_bundle_controller") 
		//return;
		show_msg("3");
	  }

	}

	function openmypage_production_id()
	{ 
		//var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
		var data=document.getElementById('cbo_company_name').value;
		var page_link='requires/embellishment_delivery_bundle_controller.php?action=production_popup&data='+data;
		var title="Delivery ID Popup Info";
		
		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=570px, height=400px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_subcontract_frm"); //Access the form inside the modal window
			//var theemail=this.contentDoc.getElementById("selected_job");
			var theemail=this.contentDoc.getElementById("selected_job").value;
			//alert (theemail); 
			//var splt_val=theemail.split("_");
			if (theemail!="")
			{
				freeze_window(5);
				//reset_form('','','txt_delivery_no*cbo_company_name*cbo_location_name*cbo_party_name*txt_issue_challan*txt_delivery_date*cbo_within_group*txt_job_no*update_id','','');
				get_php_form_data( theemail, "load_php_data_to_form", "requires/embellishment_delivery_bundle_controller" );
				
				var within_group = document.getElementById('cbo_within_group').value;
				if(within_group==1)
				{
 					show_list_view(theemail,'issue_item_details_update_bundle','search_div','requires/embellishment_delivery_bundle_controller','setFilterGrid("rec_issue_table",-1)');	
				}
				else
				{
					show_list_view(theemail,'issue_item_details_update','search_div','requires/embellishment_delivery_bundle_controller','setFilterGrid("rec_issue_table",-1)');
				}
				//fnc_total_calculate();
				set_button_status(1, permission, 'fnc_material_delivery',1);
				/*var list_view_orders = return_global_ajax_value( splt_val[0]+'**'+splt_val[1]+'**'+1, 'load_php_dtls_form', '', 'requires/embellishment_delivery_bundle_controller');
				if(list_view_orders!='')
				{
					$("#rec_issue_table tr").remove();
					$("#rec_issue_table").append(list_view_orders);
				}
				
				set_button_status(1, permission, 'fnc_material_delivery',1);*/
				release_freezing();
			}
		}
	}
	
	function openmypage_rec_id()
	{ 
		var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
		if(isMobile) {return false;}
		//var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
		var data=document.getElementById('cbo_company_name').value;
		var page_link='requires/embellishment_delivery_bundle_controller.php?action=receive_popup&data='+data;
		var title="Receive Id Popup Info";	
		
		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=900px, height=400px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemail=this.contentDoc.getElementById("selected_job").value;
			//alert (theemail); 
			var splt_val=theemail.split("_");
			if (splt_val[0]!="")
			{
				freeze_window(5);
				var tot_row=$('#table_body tbody tr:not(.fltrow, #dammy_row)').length;

				var list_view_orders = return_global_ajax_value( splt_val[0]+'**'+splt_val[1]+'**'+1+'**'+tot_row+'**'+splt_val[2], 'append_new_item', '', 'requires/embellishment_delivery_bundle_controller');
				if(list_view_orders=='')
				{
					alert("QC Data Not Found.");
					if($('#details_tbl tbody tr').length <1){
						$("#search_div").html("");
					}
				}
				else
				{
					//Remove dummy row and append data here
					var dammy_row = document.getElementById('dammy_row');
					if (typeof(dammy_row) != 'undefined' && dammy_row != null) {
						$("#dammy_row").remove();
					}
					$("#table_body tbody").append(list_view_orders);
					fnc_total_calculate();
				}

				release_freezing();
			}
		}
	}


	function reset_fnc()
	{
		location.reload(); 
	}
	
	function check_iss_qty_ability(value,i)
	{
		var placeholder_value = $("#txtissueqty_"+i).attr('placeholder')*1;
		var pre_iss_qty = $("#txtissueqty_"+i).attr('pre_issue_qty')*1;
		var rec_qty = $("#txtissueqty_"+i).attr('rec_qty')*1;
		//alert(placeholder_value);
		if(((value*1)+pre_iss_qty)>rec_qty)
		{
			//alert("Qnty Excceded");
			var confirm_value=confirm("Issue qty Excceded by Order qty .Press cancel to proceed otherwise press ok. ");
			if(confirm_value!=0)
			{
				$("#txtissueqty_"+i).val('');
			}			
			return;
		}
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
	}
	
	function fnc_total_calculate()
	{
		//var rowCount = $('#rec_issue_table tr').length;
		var rowCount =  $('#table_body tbody tr:not(".fltrow")').length;
		math_operation( "txtTotBundleqty", "txtBundleQty_", "+", rowCount );
		math_operation( "txtTotProdqty", "txtProdQty_", "+", rowCount );
		//math_operation( "txtTotRejqty", "txtRejQty_", "+", rowCount );
		math_operation( "txtTotQcqty", "txtQcQty_", "+", rowCount );
	}

	function search_by(val)
	{
		$('#txt_search_string').val('');
		if(val==1 || val==0) $('#search_by_td').html('Embl. Job No');
		else if(val==2) $('#search_by_td').html('W/O No');
		else if(val==3) $('#search_by_td').html('Buyer Job');
		else if(val==4) $('#search_by_td').html('Buyer Po');
		else if(val==5) $('#search_by_td').html('Buyer Style');
	}

	function fnCheckUnCheckAll(checkVal)
	{
		for (Looper=0; Looper < document.pimasterform_2.length ; Looper++ )
		{
			var strType = document.pimasterform_2.elements[Looper].type;
			if (strType=="checkbox")
			{
				document.pimasterform_2.elements[Looper].checked=checkVal;
			}
		}
	}

	function openmypage_reject_qty(type,job_dtls_id,row)
	{
		var production_id = $('#txt_production_id').val();
		//var RecipeNo= $('#txtRecipeNo_'+row).val()*1;
		//var booking_po_id = $('#txtbuyerPoId').val();
		var data_break=$('#hdnDtlsdata_'+row).val();
		var hdnDtlsUpdateId=$('#updatedtlsid_'+row).val();
		
		//var page_link = 'requires/embellishment_delivery_bundle_controller.php?job_dtls_id='+job_dtls_id+'&data_break='+data_break+'&hdnDtlsUpdateId='+hdnDtlsUpdateId+'&booking_po_id='+booking_po_id+'&RecipeNo='+RecipeNo+'&production_id='+production_id+'&action=reject_qty_popup';
		var page_link = 'requires/embellishment_delivery_bundle_controller.php?job_dtls_id='+job_dtls_id+'&data_break='+data_break+'&hdnDtlsUpdateId='+hdnDtlsUpdateId+'&production_id='+production_id+'&action=reject_qty_popup';
		
		emailwindow=dhtmlmodal.open('EmailBox','iframe',page_link,'Defect Qty Popup', 'width=470px, height=100px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			$('#hdnDtlsdata_'+row).val('');
			var break_data=this.contentDoc.getElementById("hidden_break_tot_row").value; 
			var defect_qty = break_data.split('_');
			var tot_defect_qty= (defect_qty[0])*1+(defect_qty[1])*1+(defect_qty[2])*1;
			$('#hdnDtlsdata_'+row).val(break_data);
			$('#txtRejQty_'+row).val(tot_defect_qty);
		}		
	}

	$('#txt_bundle_no').live('keyup', function(e) {
		var barcode = trim($('#txt_bundle_no').val());
        if (e.keyCode === 13 || (barcode.length==14))
        {
			if ( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
		
            e.preventDefault();
            var txt_bundle_no = trim($('#txt_bundle_no').val().toUpperCase());
		
            var flag = 1;
            $("#table_body").find('tbody tr:not(".fltrow")').each(function()
            {
            	var barcodeNo = trim($(this).find('input[name="txtBarcodeNo[]"]').val().toUpperCase());
            	if (txt_bundle_no == barcodeNo) {
                    alert("Barcode No: " + barcodeNo + " already scan, try another one.");
                    $('#txt_bundle_no').val('');
                    flag = 0;
                    return false;
                }
            });

            if (flag == 1)
            {
                fnc_duplicate_bundle(txt_bundle_no);
            }
        }
    });

    function fnc_duplicate_bundle(bundle_no)
    {
        var challan_duplicate = return_ajax_request_value(bundle_no + "__" + $('#cbo_company_name').val(), "challan_duplicate_check", "requires/embellishment_delivery_bundle_controller");

        var ex_challan_duplicate = challan_duplicate.split("**");
		
		//alert(ex_challan_duplicate); return;
		   var within_group = document.getElementById('cbo_within_group').value;
			if(within_group==1)
			{
 			       if (trim( ex_challan_duplicate[0]) != '')
					{
					   // var alt_str = ex_challan_duplicate[1].split("##");
					   // var al_msglc = "Bundle No '" + trim(alt_str[0]) + "' Found in Challan No '" + trim(alt_str[1]) + "'";
						alert(trim(ex_challan_duplicate[0]));
						$('#txt_bundle_no').val('');
						return;
					}
					else
					{
						create_row(bundle_no,'scan','');
					}
  			}
			else
			{
				 if ( trim( ex_challan_duplicate[0]) == 1)
				{
					alert(trim(ex_challan_duplicate[1]));
					$('#txt_bundle_no').val('');
					return;
				}
				else if ( trim( ex_challan_duplicate[0]) == 2)
				{
				   var dupli_wo = 0;
				   $("#table_body").find('tbody tr:not(".fltrow")').each(function()
					{
						var woID = trim($(this).find('input[name="woID[]"]').val());
						if(woID !="")
						{
							//alert(woID);alert(ex_challan_duplicate[1])
							if (woID != trim( ex_challan_duplicate[1])) {
								dupli_wo += 1;
								return false;
							}
						}
					});
		
				   if(dupli_wo >0)
				   {
						alert("Multiple job no not allowed.");
						$('#txt_bundle_no').val('');
						return;
				   }
		
					create_row(bundle_no,'scan','');
				}
   
  			}
       
		

        $('#txt_bundle_no').val('');
    }

    function create_row(bundle_nos, vscan, hidden_source_cond)
    {
        freeze_window(5);
		var row_num =  $('#table_body tbody tr:not(".fltrow, #dammy_row")').length;
		
   
   			var within_group = document.getElementById('cbo_within_group').value;
			if(within_group==1)
			{
 			  // var list_view_orders = return_global_ajax_value( bundle_nos+'**'+''+'**'+2+'**'+row_num, 'append_new_item_bundle', '', 'requires/embellishment_material_qc_bundle_controller');
			   var list_view_orders = return_global_ajax_value( bundle_nos+'**'+''+'**'+2+'**'+row_num, 'append_new_item_bundle', '', 'requires/embellishment_delivery_bundle_controller');
   
 			}
			else
			{
				//var list_view_orders = return_global_ajax_value( bundle_nos+'**'+''+'**'+2+'**'+row_num, 'append_new_item', '', 'requires/embellishment_material_qc_bundle_controller');
				
				var list_view_orders = return_global_ajax_value( bundle_nos+'**'+''+'**'+2+'**'+row_num, 'append_new_item', '', 'requires/embellishment_delivery_bundle_controller');
   
  			}
        if (trim(list_view_orders) == '')
        {
            alert("QC Data Not Found.");
			//return;
			/*if($('#table_body tbody tr:not(.fltrow, #dammy_row)').length <1){
				$("#search_div").html("");
			}*/
        }
        else
		{
			//Remove dummy row and append data here
			var dammy_row = document.getElementById('dammy_row');
			if (typeof(dammy_row) != 'undefined' && dammy_row != null) {
				$("#dammy_row").remove();
			}
        	$("#table_body tbody").append(list_view_orders);
			fnc_total_calculate();
        }
        release_freezing();
    }

	function print_report_button_setting(report_ids)
	{
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
			{
				if(report_id[k]==86)
				{
					$("#Print_1").show();	 
				}
				if(report_id[k]==110)
				{
					$("#Print_2").show();	 
				}	
				if(report_id[k]==85)
				{
					$("#Print_3").show();	 
				}	
			}
	}
</script>
</head>
<body onLoad="set_hotkey();set_auto_complete_size('size_return');">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?>
        
        <div align="center" style="width:100%;" >
            <form name="searchreceivefrm_1"  id="searchreceivefrm_1" autocomplete="off">
                <table width="870" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="11"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
                        </tr>
                    	<tr>                	 
                            <th width="140" class="must_entry_caption">Company Name</th>
                            <th width="140">Location Name</th>
                            <th width="50">Within Group</th>
                            <th width="120">Customer</th>
                            <th width="70">Receive ID</th>
                            <th width="80">Challan No</th>
                            <th width="100">Search By</th>
                    		<th width="100" id="search_by_td">Embl. Job No</th>
                            <th width="100" colspan="2">Date Range</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> <input type="hidden" id="selected_job">  <!--  echo $data;-->
							<? 
								echo create_drop_down( "cbo_company_name", 140, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --",'0', "load_drop_down( 'requires/embellishment_delivery_bundle_controller', this.value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer_pop', 'buyer_td' );load_drop_down( 'requires/embellishment_delivery_bundle_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data(this.value, 'set_print_button', 'requires/embellishment_delivery_bundle_controller');"); ?>
                            </td>
							<td id="location_td">
								<? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "" );?>
							</td>
                            <td>
							<?
								echo create_drop_down( "cbo_within_group", 50, $yes_no,"", 0, "--  --",'2', "load_drop_down( 'requires/embellishment_delivery_bundle_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer_pop', 'buyer_td' );" ); ?>
							</td>
                            <td id="buyer_td">
								<? 
								echo create_drop_down( "cbo_party_name", 120, $blank_array,"", 1, "-- Select Party --", $selected, "" );?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:60px" placeholder="Receive ID" />
                            </td>
                            <td>
                                <input type="text" name="txt_search_challan" id="txt_search_challan" class="text_boxes" style="width:70px" placeholder="Challan" />
                            </td>
                            <td>
								<?
                                    $search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                                    echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                                ?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From">
                            </td>
                            <td>
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_search_challan').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_year_selection').value, 'create_receive_search_list_view', 'search_div', 'requires/embellishment_delivery_bundle_controller', 'setFilterGrid(\'rec_issue_table\',-1)')" style="width:70px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="10" align="center" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table  width="1210" cellspacing="2" cellpadding="0" border="0">
                	<tr>
                    	<td width="120" align="right"><strong>Sys. Delivery No.</strong></td>
                    	<td width="120" align="left">
	                    	<input class="text_boxes"  type="text" name="txt_delivery_no" id="txt_delivery_no" onDblClick="openmypage_production_id();"  placeholder="Double Click" style="width:107px;" readonly/>
	                    	<input type="hidden" name="update_id" id="update_id">
	                    </td>
	                    <td width="80" align="right" class="must_entry_caption"><strong>Delivery Date</strong></td>
                    	<td width="80" align="left">
                    		<input type="text" class="datepicker" autocomplete="off"  name="txt_delivery_date" id="txt_delivery_date"  value="<? echo date("d-m-Y");?>" style="width:67px;"  />
	                    </td>
	                    <td width="100" align="right"><strong>Barcode No.</strong></td>
                    	<td width="120" align="left">
	                    	<input class="text_boxes"  type="text" name="txt_bundle_no" id="txt_bundle_no" onDblClick="openmypage_rec_id('xx','Bundle Info')" style="width:107px;" placeholder="Wirte/Browse/Scan" />
	                    </td>
                        <td width="120" align="right"><strong>Delivery to Floor</strong></td>
                    	<td width="80" align="left">
	                    	<?
                              	echo create_drop_down( "cbo_floor",120, "select id,floor_name from lib_prod_floor where production_process=1 and status_active =1 and is_deleted=0 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0,"","","","",4 );
                         	?> 
	                    </td>
	                    <td width="90" align="right"><strong>Delivery Point</strong></td>
                    	<td width="100" align="left">
	                    	<input class="text_boxes"  type="text" name="txt_delivery_point" id="txt_delivery_point" placeholder="Wirte"  style="width:95px"  />
	                    </td>
	                    <td width="50" align="right"><strong>Remerks</strong></td>
                    	<td width="120" align="left">
	                    	<input class="text_boxes"  type="text" name="txt_remarks" id="txt_remarks" placeholder="Wirte"  style="width:100px"  />
	                    </td>
					</tr>    
                </table> 
            </form>
        </div>
        <form name="pimasterform_2" id="pimasterform_2" autocomplete="off">
            <fieldset style="width:1250px; margin-top:10px;">
                <legend>Bundle Item Details</legend>
                <div id="search_div">
					<table cellpadding="0" cellspacing="2" border="1" width="1910" id="details_tbl" rules="all" align="left">
						<thead class="form_table_header">
							<tr align="center" >
								<th width="50" >SL</th>
								<th width="100" >Barcode No</th>
								<th width="100" >Comapany</th>
								<th width="100" >Location</th>
								<th width="60" >Within Group</th>
								<th width="100" >Customer</th>
								<th width="100" >Cus. Buyer</th>
								<th width="60" >Issue Date</th>
								<th width="60">Issue Ch No</th>
								<th width="140">Order No</th>
								<th width="140">Cust. Style Ref.</th>
								<th width="100">IR/IB</th>
								<th width="80">Embl. Name</th>
								<th width="60">Embl. Type</th>
								<th width="80">Body Part</th>
								<th width="130">Color</th>
								<th width="60">Size</th>
								<th width="80">Bundle NO</th>
								<th width="60">Bundle Qty.</th>
								<th width="60">Prod. Qty.</th>
								<th width="60">QC Qty.</th>
								<th width="130">RMK</th>
							</tr>
						</thead>
					</table>
					<div style="width:1930px; max-height:270px; overflow-y:scroll;" >
						<table cellpadding="0" cellspacing="2"  width="1910" id="table_body" class="rpt_table" rules="all" align="left">
							<tbody id="rec_issue_table">
							<?
								$i=1;
								
								$checkBox_check="checked";
								?>
								<tr id="dammy_row">
									<td width="50" align="center">
									<? echo $i; ?>
										<input type="checkbox" name="barcodeChkbox[]" id="barcodeChkbox_<? echo $i; ?>" value="" <? echo $checkBox_check; ?> />
										<input type="hidden" name="woID[]" id="woID_<? echo $i; ?>" value="<? echo $wo_id; ?>"  >
										<input type="hidden" name="woDtlsID[]" id="woDtlsID_<? echo $i; ?>" value="<? echo $row[csf("job_dtls_id")]; ?>"  >
										<input type="hidden" name="woBreakID[]" id="woBreakID_<? echo $i; ?>" value="<? echo $row[csf("job_break_id")]; ?>"  >
										<input type="hidden" name="rcvID[]" id="rcvID_<? echo $i; ?>" value="<? echo $row[csf("rcv_id")]; ?>"  >
										<input type="hidden" name="rcvDtlsID[]" id="rcvDtlsID_<? echo $i; ?>" value="<? echo $row[csf("rcv_dtls_id")]; ?>"  >
										<input type="hidden" name="bundleMstID[]" id="bundleMstID_<? echo $i; ?>" value="<? echo $row[csf("bundl_mst_id")]; ?>"  >
										<input type="hidden" name="bundleDtlsID[]" id="bundleDtlsID_<? echo $i; ?>" value="<? echo $row[csf("bundle_dtls_id")]; ?>"  >
										<input type="hidden" name="issueDtlsID[]" id="issueDtlsID_<? echo $i; ?>" value="<? echo $row[csf("issue_dtls_id")]; ?>"  >
										<input type="hidden" name="productionDtlsIds[]" id="productionDtlsIds_<? echo $i; ?>" value="<? echo $productionDtlsIds; ?>"  >
										<input type="hidden" name="qcDtlsIds[]" id="qcDtlsIds<? echo $i; ?>" value="<? echo $qcDtlsIds; ?>"  >
										<input type="hidden" name="updatedtlsid[]" id="updatedtlsid_<? echo $i; ?>" value=""  >
									</td>
									<td width="100">
									<? echo $row[csf("barcode_no")]; ?>
										<input name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $row[csf("barcode_no")]; ?>" style="width:90px" readonly />
									</td>
									<td width="100">
										<? 
										echo create_drop_down( "cboCompanyId_1", 90, $blank_array,"", 1, "-- Select Company --", $selected, "","","","","","","","","cboCompanyId[]" );
										?>
									</td>
									
									<td width="100">
										<? 
										echo create_drop_down( "cboLocationId_1", 90, $blank_array,"", 1, "-- Select Location --", $selected, "","","","","","","","","cboLocationId[]" );
										?>
									</td>
									<td width="60">
										<? echo create_drop_down( "cboWithinGroup_".$i, 55, $yes_no,"", 1, "-- Select --",2, "",1,'','','','','','',"cboWithinGroup[]"); 
										?>
									</td>
									<td width="100">
										<? 
										echo create_drop_down( "cboPartyId_1", 90, $blank_array,"", 1, "-- Select Customer --", $selected, "","","","","","","","","cboPartyId[]" );
										?>
									</td>
									<td width="100">
									<? echo $buyer_buyer; ?>
										<input name="txtCustBuyer[]" id="txtCustBuyer_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $buyer_buyer; ?>"  style="width:87px" readonly />
									</td>
									<td width="60">
									<? echo change_date_format($row[csf("issue_date")]); ?>
										<input name="txtIssueDate[]" id="txtIssueDate_<? echo $i; ?>" type="hidden" class="datepicker" value="<? echo change_date_format($row[csf("issue_date")]); ?>"  style="width:87px" disabled />
									</td> 
									<td width="60">
									<? echo $row[csf("challan_no")]; ?>
										<input name="txtIssueCh[]" id="txtIssueCh_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $row[csf("challan_no")]; ?>"  style="width:47px"  readonly  />
									</td>
									<td width="140">
									<? echo $order_no; ?>
										<input name="txtOrder_<? echo $i; ?>" id="txtOrder_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $order_no; ?>"  style="width:47px" readonly />
									</td>
									<td width="140">
									<? echo $style; ?>
										<input name="txtstyleRef[]" id="txtstyleRef_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $style; ?>"  style="width:47px" readonly />
									</td>
									<td width="100">
										<? //echo $style; ?>
										<!-- <input name="txtstyleRef[]" id="txtstyleRef_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $style; ?>"  style="width:47px" readonly /> -->
									</td>
									<td width="80">
										<? echo create_drop_down( "cboProcessName_<? echo $i; ?>", 70, $emblishment_name_array,"", 1, "--Select--",$main_process_id, "",1,'','','','','','',"cboEmbType[]"); ?>
									</td>
									<td width="60">
										<?
										if($main_process_id==1) $emb_type=$emblishment_print_type;
										else if($main_process_id==2) $emb_type=$emblishment_embroy_type;
										else if($main_process_id==3) $emb_type=$emblishment_wash_type;
										else if($main_process_id==4) $emb_type=$emblishment_spwork_type;
										else if($main_process_id==5) $emb_type=$emblishment_gmts_type; 
										echo create_drop_down( "cboEmbType_".$i, 55, $emb_type,"", 1, "-- Select --",$embl_type_id, "",1,'','','','','','',"cboEmbType[]"); ?>
									</td>
									<td width="80">
										<? echo create_drop_down( "cboBodyPart_".$i, 70, $body_part,"", 1, "-- Select --",$body_part_id, "",1,'','','','','','',"cboBodyPart[]"); ?>
									</td>
									<td width="130">
									<? echo $color_arr[$color_id]; ?>
										<input type="hidden" id="txtcolor_<? echo $i; ?>" name="txtcolor_<? echo $i; ?>" class="text_boxes" value="<? echo $color_arr[$color_id]; ?>"  style="width:87px" readonly/>
									</td>
									<td width="60">
									<? echo $size_arr[$size_id]; ?>
										<input type="hidden" id="txtsize_<? echo $i; ?>" name="txtsize_<? echo $i; ?>" class="text_boxes txt_size" value="<? echo $size_arr[$size_id]; ?>"  style="width:47px" readonly/>
									</td>
									<td width="80">
									<? echo $row[csf("bundle_no")]; ?>
										<input name="txtBundleNo[]" id="txtBundleNo_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $row[csf("bundle_no")]; ?>"  style="width:67px" readonly />
									</td>
									<td width="60">
										<input name="txtBundleQty[]" id="txtBundleQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $row[csf("bundle_qty")]; ?>"  style="width:47px" readonly />
									</td>
									<td width="60">
										<input name="txtProdQty[]" id="txtProdQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $prod_qty; ?>" style="width:47px" readonly />
										<input name="txtRejQty[]" id="txtRejQty_<? echo $i; ?>" type="hidden" class="text_boxes_numeric" value="<??>"  placeholder="Display"   readonly style="width:47px"  />
									</td> 
									<td width="60">
										<input name="txtQcQty[]" id="txtQcQty_<? echo $i; ?>" type="text" class="text_boxes_numeric" value="<? echo $qc_qty; ?>" placeholder="Display" style="width:47px" readonly />
									</td>
									<td width="130">
										<input name="txtRemark[]" id="txtRemark_<? echo $i; ?>" type="text" class="text_boxes" value="" placeholder="Write" style="width:120px"/>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<table cellpadding="0" cellspacing="2" border="1" width="1910" id="tbl_footer" rules="all" align="left">
						<tfoot>
							<tr class="tbl_bottom" name="tr_btm" id="tr_btm">
								<td width="50" align="center">
									All <input form="form_all" type="checkbox" name="check_all" id="check_all" value=""  onclick="fnCheckUnCheckAll(this.checked)"/>
								</td>
								<td width="100">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="60">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="60">&nbsp;</td>
								<td width="60">&nbsp;</td>
								<td width="140">&nbsp;</td>
								<td width="140">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="60">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="130">&nbsp;</td>
								<td width="60">&nbsp;</td>
								<td width="80">Total:</td>
								<td width="60">
									<input name="txtTotBundleqty" id="txtTotBundleqty" class="text_boxes_numeric" type="text" value="<? echo $totBndlQty; ?>"  style="width:47px" readonly />
								</td>
								<td width="60">
									<input name="txtTotProdqty" id="txtTotProdqty" class="text_boxes_numeric" type="text" value="<? echo $totProdQty; ?>" style="width:47px" readonly />
									<input name="txtTotRejqty" id="txtTotRejqty" class="text_boxes_numeric" type="hidden" style="width:47px"  readonly />
								</td>

								<td width="60">
									<input name="txtTotQcqty" id="txtTotQcqty" class="text_boxes_numeric" type="text" value="<? echo $totQcQty; ?>" style="width:47px"  readonly />
								</td>
								<td width="130">&nbsp;</td>
							</tr>
						</tfoot>
					</table>
				</div>
                <table width="1220" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td align="center" valign="middle" class="button_container">
                       <? echo load_submit_buttons($permission,"fnc_material_delivery",0,0,"reset_form('materialissue_1', '','','','')",1); ?>				   
                    </td>
                </tr> 
				<tr>
					<td  align="center" style="padding-top: 10px;">
						<input id="Print_1" class="formbutton" type="button" style="width:80px;display:none;" onClick="fnc_embl_delivery(1)" name="print_1" value="Print">
						<input id="Print_2" class="formbutton" type="button" style="width:80px;display:none;" onClick="fnc_embl_delivery(2)" name="print_2" value="Print 2">
						<input id="Print_3" class="formbutton" type="button" style="width:80px;display:none;" onClick="fnc_embl_delivery(3)" name="print_3" value="Print 3">
					</td>
				</tr> 
          	</table> 
            </fieldset>
        </form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$('#txt_bundle_no').focus();	
	setFilterGrid('rec_issue_table',-1,tableFilters);
</script>
</html>
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
		if ( form_validation('txt_delivery_date', 'Company Name')==false )
		{
			return;
		}
		else
		{
			/*var zero_val="0";
			if(operation==2)
			{
				var r=confirm('Press \"OK\" to delete all items of this challan.\nPress \"Cancel\" Do Not Delete.');
				if(r==true) 
				{ 
					zero_val="1";
				}
				else
				{
					zero_val="0";
					return;
				}
			}*/
			if(operation==4)
			{
				var report_title=$( "div.form_caption" ).html();
				print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_location_name').val(), "embl_delivery_bundle_entry_print", "requires/embellishment_delivery_bundle_controller") 
				//return;
				show_msg("3");
			}
			else
			{
				var data_str="";
				
				var data_str=get_submitted_data_string('txt_delivery_no*cbo_company_name*txt_delivery_date*update_id*txt_remarks*txt_delivery_point',"../../");
				var tot_row=$('#details_tbl tbody tr').length;
				var k=0;
				var data_all=""; var i=0; var selected_row=0;
				//var tbaleId= 'details_tbl tbody tr';
				//var j=0; var check_field=0; data_all="";
				for (var j=1; j<=tot_row; j++)
				{
					if($('#barcodeChkbox_'+j).is(':checked'))
					{
						
						if($('#txtProdQty_'+j).val()=='' || $('#txtProdQty_'+j).val()==0){
							alert('Please Fill up Prod. Qty.'); return; 
						}else{
							i++;
							data_all+="&woID_" + i + "='" + $('#woID_'+j).val()+"'"+"&woDtlsID_" + i + "='" + $('#woDtlsID_'+j).val()+"'"+"&woBreakID_" + i + "='" + $('#woBreakID_'+j).val()+"'"+"&rcvID_" + i + "='" + $('#rcvID_'+j).val()+"'"+"&rcvDtlsID_" + i + "='" + $('#rcvDtlsID_'+j).val()+"'"+"&bundleMstID_" + i + "='" + $('#bundleMstID_'+j).val()+"'"+"&bundleDtlsID_" + i + "='" + $('#bundleDtlsID_'+j).val()+"'"+"&issueDtlsID_" + i + "='" + $('#issueDtlsID_'+j).val()+"'"+"&cboCompanyId_" + i + "='" + $('#cboCompanyId_'+j).val()+"'"+"&txtRcvDate_" + i + "='" + $('#txtRcvDate_'+j).val()+"'"+"&txtIssueCh_" + i + "='" + $('#txtIssueCh_'+j).val()+"'"+"&txtProdQty_" + i + "='" + $('#txtProdQty_'+j).val()+"'"+"&productionDtlsIds_" + i + "='" + $('#productionDtlsIds_'+j).val()+"'"+"&qcDtlsIds_" + i + "='" + $('#qcDtlsIds_'+j).val()+"'"+"&txtRejQty_" + i + "='" + $('#txtRejQty_'+j).val()+"'"+"&hdnDtlsdata_" + i + "='" + $('#hdnDtlsdata_'+j).val()+"'"+"&txtQcQty_" + i + "='" + $('#txtQcQty_'+j).val()+"'"+"&txtRemark_" + i + "='" + $('#txtRemark_'+j).val()+"'"+"&updatedtlsid_" + i + "='" + $('#updatedtlsid_'+j).val()+"'";
						}
						//alert(11); return;
						//productionDtlsIds txtRejQty hdnDtlsdata txtQcQty txtRemark qcDtlsIds
					}
				}
				
				var data="action=save_update_delete&operation="+operation+'&total_row='+i+data_all+data_str;//+'&zero_val='+zero_val
				//alert (data); return;
				freeze_window(operation);
				http.open("POST","requires/embellishment_delivery_bundle_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_material_qc_response;
			}
		}
	}
	
	function fnc_material_qc_response()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);//return;
			var response=trim(http.responseText).split('**');


			//if (response[0].length>3) reponse[0]=10;	
			/*if(trim(response[0])=='emblProduction'){
				alert("Delivery Found :"+trim(response[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}*/
			show_msg(response[0]);
			//$('#cbo_uom').val(12);
			if(response[0]==0 || response[0]==1 || response[0]==2)
			{
				document.getElementById('txt_delivery_no').value= response[1];
				document.getElementById('update_id').value = response[2];
				//alert();

				show_list_view(response[2],'issue_item_details_update','search_div','requires/embellishment_delivery_bundle_controller','');
				var tot_row=$('#details_tbl tbody tr').length;
				if(tot_row>0){
					fnc_total_calculate();
					set_button_status(1, permission, 'fnc_material_delivery',1);
				}else{
					set_button_status(0, permission, 'fnc_material_delivery',1);
				}
		
			}
			else if(response[0]==121) // for duplicate barcode chk
			{
				alert(response[1]);
			}
			
			release_freezing();
		}
	}

	function openmypage_production_id()
	{ 
		//var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
		var data=document.getElementById('cbo_company_name').value;
		var page_link='requires/embellishment_delivery_bundle_controller.php?action=production_popup&data='+data;
		var title="Delivery ID Popup Info";
		
		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=470px, height=400px, center=1, resize=0, scrolling=0','../')
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
				show_list_view(theemail,'issue_item_details_update','search_div','requires/embellishment_delivery_bundle_controller','');
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
			//("search_subcontract_frm"); //Access the form inside the modal window
			//var theemail=this.contentDoc.getElementById("selected_job");
			var theemail=this.contentDoc.getElementById("selected_job").value;
			//alert (theemail); 
			var splt_val=theemail.split("_");
			if (splt_val[0]!="")
			{
				freeze_window(5);
				//reset_form('','','txt_receive_no*cbo_company_name*cbo_location_name*cbo_party_name*txt_receive_challan*txt_receive_date*cbo_within_group*txt_job_no*update_id*cbo_from_company_name*cbo_from_location_name*txt_receive_remarks','','');
				//get_php_form_data( splt_val[0], "load_php_data_to_form", "requires/embellishment_delivery_bundle_controller" );
				//var cbo_within_group=document.getElementById('cbo_within_group').value;
				//var bundle_variable=document.getElementById('bundle_variable').value;

				var tot_row=$('#details_tbl tbody tr').length;
				//alert(tot_row);
				if(tot_row<1){
					var new_table = return_global_ajax_value( 2+'**'+tot_row, 'create_new_table', '', 'requires/embellishment_delivery_bundle_controller');
					if (trim(new_table) != '')
			        {
			            $("#search_div").append(new_table);
			        }
				}
				//alert(tot_row);
				var list_view_orders = return_global_ajax_value( splt_val[0]+'**'+splt_val[1]+'**'+1+'**'+tot_row+'**'+splt_val[2], 'append_new_item', '', 'requires/embellishment_delivery_bundle_controller');
				if(list_view_orders!='')
				{
					//$("#rec_issue_table tr").remove();
					//$('#details_tbl tbody tr')
					//alert(list_view_orders);
					$("#details_tbl tbody").append(list_view_orders);
				}
				fnc_total_calculate();
				//set_button_status(1, permission, 'fnc_material_delivery',1);
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
		var rowCount = $('#rec_issue_table tr').length;
		//alert(rowCount);
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
            $("#details_tbl").find('tbody tr').each(function()
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
       
		if ( trim( ex_challan_duplicate[0]) == 1)
        {
            alert(trim(ex_challan_duplicate[1]));
            $('#txt_bundle_no').val('');
            return;
        }
        else if ( trim( ex_challan_duplicate[0]) == 2)
        {
           var dupli_wo = 0;
           $("#details_tbl").find('tbody tr').each(function()
            {
            	var woID = trim($(this).find('input[name="woID[]"]').val());
            	//alert(woID);alert(ex_challan_duplicate[1])
            	if (woID != trim( ex_challan_duplicate[1])) {
                    dupli_wo += 1;
                    return false;
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

        $('#txt_bundle_no').val('');
    }

    function create_row(bundle_nos, vscan, hidden_source_cond)
    {
        freeze_window(5);

        var row_num =  $('#details_tbl tbody tr').length; //$('#txt_tot_row').val();

        //var tot_row=$('#details_tbl tbody tr').length;
				//alert(tot_row);
		if(row_num<1){
			var new_table = return_global_ajax_value( 2+'**'+row_num, 'create_new_table', '', 'requires/embellishment_delivery_bundle_controller');
			if (trim(new_table) != '')
	        {
	            $("#search_div").append(new_table);
	        }
		}
		var list_view_orders = return_global_ajax_value( bundle_nos+'**'+''+'**'+2+'**'+row_num, 'append_new_item', '', 'requires/embellishment_delivery_bundle_controller');
   
        //var response_data = return_global_ajax_value(bundle_nos + "**" + row_num + "****" + $('#cbo_company_name').val() + "**" + vscan + "**" + hidden_source_cond, 'populate_bundle_data', '', 'requires/embellishment_delivery_bundle_controller');
        if (trim(list_view_orders) == '')
        {
            alert("No Data Found.");
        }
        else{
        	$("#details_tbl tbody").append(list_view_orders);
        }
        fnc_total_calculate();

        //$('#details_tbl tbody').prepend(list_view_orders);
        /*var tot_row = $('#details_tbl tbody tr').length;
        if ((tot_row * 1) > 0)
        {
            $('#cbo_company_name').attr('disabled', 'disabled');
        }
        $('#txt_tot_row').val(tot_row);*/
        release_freezing();
    }
</script>
</head>
<body onLoad="set_hotkey();set_auto_complete('subcon_material_issue');set_auto_complete_size('size_return');">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?>
        
        <div align="center" style="width:100%;" >
            <form name="searchreceivefrm_1"  id="searchreceivefrm_1" autocomplete="off">
                <table width="870" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="11"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
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
								echo create_drop_down( "cbo_company_name", 140, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --",'0', "load_drop_down( 'requires/embellishment_delivery_bundle_controller', this.value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer_pop', 'buyer_td' );load_drop_down( 'requires/embellishment_delivery_bundle_controller', this.value, 'load_drop_down_location', 'location_td' );"); ?>
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
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_search_challan').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_location_name').value, 'create_receive_search_list_view', 'search_div', 'requires/embellishment_delivery_bundle_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="10" align="center" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table  width="950" cellspacing="2" cellpadding="0" border="0">
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
                <div id="search_div"></div>
                <table width="1220" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td align="center" valign="middle" class="button_container">
                       <? echo load_submit_buttons($permission,"fnc_material_delivery",0,1,"reset_form('materialissue_1', '','','','')",1); ?>
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
</script>
</html>
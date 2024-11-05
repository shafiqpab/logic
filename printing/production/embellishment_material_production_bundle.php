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
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start(); 
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Embellishment Material Production [Bundle] Info", "../../", 1,1, $unicode,1,'');

?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	var tableFilters = 
	{
		col_0: "none",
		/* col_operation: {
		id: ["value_tot_qnty"],
		col: [18],
		operation: ["sum"],
		write_method: ["innerHTML"]
		} */
	}

	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name", "color_name" ), 0, -1); ?>];

	$(document).ready(function(e)
	 {
            $("#txt_color").autocomplete({
			 source: str_color
		  });

            $("#cbo_gmts_material_description").hide();
     });

	var str_material_description = [<? echo substr(return_library_autocomplete( "select material_description from sub_material_dtls group by material_description ", "material_description" ), 0, -1); ?> ];

	function set_auto_complete(type)
	{
		if(type=='subcon_material_receive')
		{
			$("#txt_material_description").autocomplete({
			source: str_material_description
			});
		}
	}

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
		var page_link='requires/embellishment_material_production_bundle_controller.php?action=production_popup&data='+data;
		var title="Production ID Popup Info";
		
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
				reset_form('','','txt_production_no*cbo_company_name*cbo_location_name*cbo_party_name*txt_issue_challan*txt_production_date*cbo_within_group*txt_job_no*update_id','','');
				get_php_form_data( splt_val[0], "load_php_data_to_form", "requires/embellishment_material_production_bundle_controller" );
				
				var list_view_orders = return_global_ajax_value( splt_val[0]+'**'+splt_val[1]+'**'+1, 'load_php_dtls_form', '', 'requires/embellishment_material_production_bundle_controller');
				if(list_view_orders!='')
				{
					$("#rec_issue_table tr").remove();
					$("#rec_issue_table").append(list_view_orders);
				}
				fnc_total_calculate();
				set_button_status(1, permission, 'fnc_material_production',1);
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
			var page_link='requires/embellishment_material_production_bundle_controller.php?action=job_popup&data='+data;
			var title='Job No Popup Info';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				freeze_window(5);
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("selected_order").value;
				$("#txt_job_no").val( theemail );
				
				var list_view_orders = return_global_ajax_value( 0+'**'+theemail+'**'+1, 'load_php_dtls_form', '', 'requires/embellishment_material_production_bundle_controller');
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

	function fnc_material_production( operation )
	{
		if ( form_validation('txt_production_date*cbo_shift_name*cbo_floor', 'Production Date*Shift*floor')==false )
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
			
			var data_str=get_submitted_data_string('txt_production_no*cbo_company_name*txt_production_date*cbo_floor*cbo_table*cbo_machine_name*update_id*cbo_shift_name*cbo_within_group',"../../");
			var tot_row=$('#table_body tbody tr:not(".fltrow")').length;
			var k=0;
			var data_all=""; var i=0; var selected_row=0; var data_delete="";
			//var tbaleId= 'details_tbl tbody tr';
			//var j=0; var check_field=0; data_all="";
			for (var j=1; j<=tot_row; j++)
			{
				var updateIdDtls=$('#updatedtlsid_'+j).val();
				
				if($('#barcodeChkbox_'+j).is(':checked'))
				{
					
					if($('#txtProdQty_'+j).val()=='' || $('#txtProdQty_'+j).val()==0){
						alert('Please Fill up Prod. Qty.'); return; 
					}else{
						if(operation==0){
							if($('#updatedtlsid_'+j).val()==''){
								i++;
								data_all+="&woID_" + i + "='" + $('#woID_'+j).val()+"'"+"&woDtlsID_" + i + "='" + $('#woDtlsID_'+j).val()+"'"+"&woBreakID_" + i + "='" + $('#woBreakID_'+j).val()+"'"+"&rcvID_" + i + "='" + $('#rcvID_'+j).val()+"'"+"&rcvDtlsID_" + i + "='" + $('#rcvDtlsID_'+j).val()+"'"+"&bundleMstID_" + i + "='" + $('#bundleMstID_'+j).val()+"'"+"&bundleDtlsID_" + i + "='" + $('#bundleDtlsID_'+j).val()+"'"+"&issueDtlsID_" + i + "='" + $('#issueDtlsID_'+j).val()+"'"+"&cboCompanyId_" + i + "='" + $('#cboCompanyId_'+j).val()+"'"+"&txtRcvDate_" + i + "='" + $('#txtRcvDate_'+j).val()+"'"+"&txtIssueCh_" + i + "='" + $('#txtIssueCh_'+j).val()+"'"+"&txtProdQty_" + i + "='" + $('#txtProdQty_'+j).val()+"'"+"&updatedtlsid_" + i + "='" + $('#updatedtlsid_'+j).val()+"'"+"&txtBarcodeNo_" + i + "='" + $('#txtBarcodeNo_'+j).val()+"'";	
							}
						}else{
							i++;
							data_all+="&woID_" + i + "='" + $('#woID_'+j).val()+"'"+"&woDtlsID_" + i + "='" + $('#woDtlsID_'+j).val()+"'"+"&woBreakID_" + i + "='" + $('#woBreakID_'+j).val()+"'"+"&rcvID_" + i + "='" + $('#rcvID_'+j).val()+"'"+"&rcvDtlsID_" + i + "='" + $('#rcvDtlsID_'+j).val()+"'"+"&bundleMstID_" + i + "='" + $('#bundleMstID_'+j).val()+"'"+"&bundleDtlsID_" + i + "='" + $('#bundleDtlsID_'+j).val()+"'"+"&issueDtlsID_" + i + "='" + $('#issueDtlsID_'+j).val()+"'"+"&cboCompanyId_" + i + "='" + $('#cboCompanyId_'+j).val()+"'"+"&txtRcvDate_" + i + "='" + $('#txtRcvDate_'+j).val()+"'"+"&txtIssueCh_" + i + "='" + $('#txtIssueCh_'+j).val()+"'"+"&txtProdQty_" + i + "='" + $('#txtProdQty_'+j).val()+"'"+"&updatedtlsid_" + i + "='" + $('#updatedtlsid_'+j).val()+"'"+"&txtBarcodeNo_" + i + "='" + $('#txtBarcodeNo_'+j).val()+"'";
						}
												
					}
					//alert(11); return;
					//woID_ woDtlsID_ woBreakID_ rcvID_ rcvDtlsID_ bundleMstID_ bundleDtlsID_ cboCompanyId_ txtIssueDate_ txtIssueCh_ txtProdQty_
				}
				
				if(updateIdDtls!="" && $('#barcodeChkbox_'+j).is(':not(:checked)'))
				{
					data_delete+=$('#issueDtlsID_'+j).val()+",";
					//deleteWoDtlsId+=$('#hideWoDtlsId_'+j).val()+",";
					//alert (data_delete);
				}
			}
			
			//var data="action=save_update_delete&operation="+operation+'&total_row='+i+data_all+data_str;//+'&zero_val='+zero_val
			var data="action=save_update_delete&operation="+operation+'&total_row='+i+'&data_delete='+data_delete+data_all+data_str;//+'&zero_val='+zero_val
			//alert (data); return;
			freeze_window(operation);
			http.open("POST","requires/embellishment_material_production_bundle_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_material_production_response;
		}
	}
	
	function fnc_material_production_response()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
			//$('#cbo_uom').val(12);
			if(response[0]==121) // for duplicate barcode chk
			{
				alert(response[1]);
				release_freezing();
				return;

			}
			show_msg(response[0]); 
			if(response[0]==0 || response[0]==1 || response[0]==2)
			{
				document.getElementById('txt_production_no').value= response[1];
				document.getElementById('update_id').value = response[2];
				//alert();

				
				
				var within_group = document.getElementById('cbo_within_group').value;
				if(within_group==1)
				{
					
 						show_list_view(response[2],'issue_item_details_update_bundle','search_div','requires/embellishment_material_production_bundle_controller','setFilterGrid(\'rec_issue_table\',-1)');
				
				}
				else
				{
 					show_list_view(response[2],'issue_item_details_update','search_div','requires/embellishment_material_production_bundle_controller','setFilterGrid(\'rec_issue_table\',-1)');
				 }
				fnc_total_calculate();
				set_button_status(1, permission, 'fnc_material_production',1);
			}
			release_freezing();
		}
	}

	function openmypage_production_id()
	{ 
		//var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
		var data=document.getElementById('cbo_company_name').value;
		var page_link='requires/embellishment_material_production_bundle_controller.php?action=production_popup&data='+data;
		var title="Production ID Popup Info";
		
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
				//reset_form('','','txt_production_no*cbo_company_name*cbo_location_name*cbo_party_name*txt_issue_challan*txt_production_date*cbo_within_group*txt_job_no*update_id','','');
				get_php_form_data( theemail, "load_php_data_to_form", "requires/embellishment_material_production_bundle_controller" );
				
				var within_group = document.getElementById('cbo_within_group').value;
				if(within_group==1)
				{
					
					 
					show_list_view(theemail,'issue_item_details_update_bundle','search_div','requires/embellishment_material_production_bundle_controller','setFilterGrid(\'rec_issue_table\',-1)');
				}
				else
				{
 				show_list_view(theemail,'issue_item_details_update','search_div','requires/embellishment_material_production_bundle_controller','setFilterGrid(\'rec_issue_table\',-1)');
 				}

				
				fnc_total_calculate();
				set_button_status(1, permission, 'fnc_material_production',1);
				/*var list_view_orders = return_global_ajax_value( splt_val[0]+'**'+splt_val[1]+'**'+1, 'load_php_dtls_form', '', 'requires/embellishment_material_production_bundle_controller');
				if(list_view_orders!='')
				{
					$("#rec_issue_table tr").remove();
					$("#rec_issue_table").append(list_view_orders);
				}
				set_button_status(1, permission, 'fnc_material_production',1);*/
				release_freezing();
			}
		}
	}
	
	function openmypage_rec_id()
	{ 
		var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
		if(isMobile) {return false;}
		
		var data=document.getElementById('cbo_company_name').value;
		var page_link='requires/embellishment_material_production_bundle_controller.php?action=receive_popup&data='+data;
		var title="Receive Id Popup Info";	
		
		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=900px, height=400px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemail=this.contentDoc.getElementById("selected_job").value;
			var splt_val=theemail.split("_");
			if (splt_val[0]!="")
			{
				freeze_window(5);
				var tot_row=$('#table_body tbody tr:not(.fltrow, #dammy_row)').length;

				var list_view_orders = return_global_ajax_value( splt_val[0]+'**'+splt_val[1]+'**'+1+'**'+tot_row+'**'+splt_val[2], 'append_new_item', '', 'requires/embellishment_material_production_bundle_controller');
				if(trim(list_view_orders)=='')
				{
					alert("Material Issue/Recipe Not Found.");
					if($('#table_body tbody tr:not(.fltrow, #dammy_row)').length <1){
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
		//alert(rowCount)
		var rowCount =  $('#table_body tbody tr:not(".fltrow")').length;
		math_operation( "txtTotBndlqty", "txtBundleQty_", "+", rowCount );
		math_operation( "txtTotProdqty", "txtProdQty_", "+", rowCount );
	}

	function search_by(val)
	{
		$('#txt_search_string').val('');
		if(val==1 || val==0) $('#search_by_td').html('Embl. Job No.');
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
        var challan_duplicate = return_ajax_request_value(bundle_no + "__" + $('#cbo_company_name').val(), "challan_duplicate_check", "requires/embellishment_material_production_bundle_controller");
        var ex_challan_duplicate = challan_duplicate.split("_");
       
		if ( trim( ex_challan_duplicate[0]) != '')
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
        $('#txt_bundle_no').val('');
    }

    function create_row(bundle_nos, vscan, hidden_source_cond)
    {
        freeze_window(5);

        var row_num =  $('#table_body tbody tr:not(".fltrow, #dammy_row")').length;


		 var within_group = document.getElementById('cbo_within_group').value;
		if(within_group==1)
		{
			
		 
				var list_view_orders = return_global_ajax_value( bundle_nos+'**'+''+'**'+2+'**'+row_num, 'append_new_item_bundle', '', 'requires/embellishment_material_production_bundle_controller');
   
			
			
		}
		else
		{
 		  	var list_view_orders = return_global_ajax_value( bundle_nos+'**'+''+'**'+2+'**'+row_num, 'append_new_item', '', 'requires/embellishment_material_production_bundle_controller');
   
		
		}
		

	
        if (trim(list_view_orders) == '')
        {
            alert("Material Issue/Recipe Not Found.");
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
        	fnc_material_production( 0 );
        	fnc_total_calculate();
        }

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
                    		<th width="100" id="search_by_td">Embl. Job No.</th>
                            <th width="100" colspan="2">Date Range</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> <input type="hidden" id="selected_job">  <!--  echo $data;-->
							<? 
								echo create_drop_down( "cbo_company_name", 140, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --",'0', "load_drop_down( 'requires/embellishment_material_production_bundle_controller', this.value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer_pop', 'buyer_td' );load_drop_down( 'requires/embellishment_material_production_bundle_controller', this.value, 'load_drop_down_location', 'location_td' );get_php_form_data( this.value,'get_shift_name' ,'requires/embellishment_material_production_bundle_controller');"); ?>
                            </td>
							<td id="location_td">
								<? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "" );?>
							</td>
                            <td>
							<?
								echo create_drop_down( "cbo_within_group", 50, $yes_no,"", 0, "--  --",'2', "load_drop_down( 'requires/embellishment_material_production_bundle_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer_pop', 'buyer_td' );" ); ?>
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
                                    $search_by_arr=array(1=>"Embl. Job No.",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
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
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_search_challan').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_year_selection').value, 'create_receive_search_list_view', 'search_div', 'requires/embellishment_material_production_bundle_controller', 'setFilterGrid(\'rec_issue_table\',-1)')" style="width:70px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="10" align="center" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table  width="1150" cellspacing="2" cellpadding="0" border="0">
                	<tr>
                    	<td width="120" align="right"><strong>Sys. Production No.</strong></td>
                    	<td width="120" align="left">
	                    	<input class="text_boxes"  type="text" name="txt_production_no" id="txt_production_no" onDblClick="openmypage_production_id();"  placeholder="Double Click" style="width:107px;" readonly/>
	                    	<input type="hidden" name="update_id" id="update_id">
	                    </td>
	                    <td width="100" align="right" class="must_entry_caption"><strong>Production Date</strong></td>
                    	<td width="80" align="left">
                    		<input type="text" class="datepicker" autocomplete="off"  name="txt_production_date" id="txt_production_date"  value="<? //echo date("d-m-Y");?>"  style="width:67px;"  />
	                    </td>
	                    <td width="100" align="right"><strong>Barcode No.</strong></td>
                    	<td width="120" align="left">
	                    	<input class="text_boxes"  type="text" name="txt_bundle_no" id="txt_bundle_no" onDblClick="openmypage_rec_id('xx','Bundle Info')" style="width:107px;" placeholder="Wirte/Browse/Scan"  />
	                    </td>
	                    <td width="60" align="right" class="must_entry_caption"><strong>Shift</strong></td>
                    	<td width="80" align="left" id="shift_td">
	                    	<?
                              	echo create_drop_down( "cbo_shift_name", 92, $shift_name,"", 1, "-- Select Shift --", 0, "",'' );
                         	?> 
	                    </td>
						<td width="60" align="right" class="must_entry_caption"><strong>Floor</strong></td>
                    	<td width="120" align="left">
	                    	<?
                              	echo create_drop_down( "cbo_floor",120, "select id,floor_name from lib_prod_floor where production_process=8 and status_active =1 and is_deleted=0 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/embellishment_material_production_bundle_controller',this.value, 'load_drop_down_table', 'table_td' );load_drop_down( 'requires/embellishment_material_production_bundle_controller',this.value, 'load_drop_down_machine', 'td_machine' );",0,"","","","",4 );
                         	?> 
	                    </td>
	                    <td width="60" align="right"><strong>Table</strong></td>
                    	<td id="table_td" width="100">
                            <? 
                                echo create_drop_down( "cbo_table", 100, $blank_array,"", 1, "-- Select Table --", "", "" );
                            ?>                            
                        </td>
                        <td width="60" align="right"><strong>Machine</strong></td>
						<td id="td_machine" width="100"  align="left"><?
						echo create_drop_down("cbo_machine_name", 100, $blank_array,"", 1, "-- Select Machine --", 0, "",0,"","","","");
						?></td>
					</tr>    
                </table> 
            </form>
        </div>
        <form name="pimasterform_2" id="pimasterform_2" autocomplete="off">
            <fieldset style="width:1050px; margin-top:10px;">
                <legend>Bundle Item Details</legend>
                <div id="search_div">
					<table cellpadding="0" cellspacing="2" border="1" width="1730" id="details_tbl" rules="all" align="left">
						<thead class="form_table_header">
							<tr align="center" >
								<th width="50" >SL</th>
								<th width="100" >Barcode No</th>
								<th width="100" >Comapany</th>
								<th width="100" >Location</th>
								<th width="60" >Within Group</th>
								<th width="100" >Customer</th>
								<th width="100" >Cus. Buyer</th>
								<th width="70" >Issue Date</th>
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
								<th width="60" class="must_entry_caption">Prod. Qty.</th>
							</tr>
						</thead>
					</table>
					<div style="width:1750px; max-height:270px;overflow-y:scroll;" >
						<table cellpadding="0" cellspacing="2"  width="1730" id="table_body" class="rpt_table" rules="all" align="left">
							<tbody id="rec_issue_table">
								<tr bgcolor="<? echo $bgcolor; ?>" id="dammy_row">
									<td width="50" align="center">
										1
										<input type="checkbox" name="barcodeChkbox[]" id="barcodeChkbox_1" value="" <? echo $checkBox_check; ?> />
										<input type="hidden" name="woID[]" id="woID_1" value=""  >
										<input type="hidden" name="woDtlsID[]" id="woDtlsID_1" value=""  >
										<input type="hidden" name="woBreakID[]" id="woBreakID_1" value=""  >
										<input type="hidden" name="rcvID[]" id="rcvID_1" value=""  >
										<input type="hidden" name="rcvDtlsID[]" id="rcvDtlsID_1" value=""  >
										<input type="hidden" name="bundleMstID[]" id="bundleMstID_1" value=""  >
										<input type="hidden" name="bundleDtlsID[]" id="bundleDtlsID_1" value=""  >
										<input type="hidden" name="issueDtlsID[]" id="issueDtlsID_1" value=""  >
										<input type="hidden" name="updatedtlsid[]" id="updatedtlsid_1" value=""  >
									</td>
									
									<td width="100">
										<? echo $row[csf("barcode_no")]; ?>
										<input name="txtBarcodeNo[]" id="txtBarcodeNo_1" type="hidden" class="text_boxes" value="" style="width:107px" readonly />
									</td>
									<td width="100">
										<? 
										echo create_drop_down( "cboCompanyId_1", 90, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --",$row[csf("company_id")], "",1,'','','','','','',"cboCompanyId[]"); ?></td>
									
									<td width="100">
										<? 
										echo create_drop_down( "cboLocationId_1", 90, $blank_array,"", 1, "-- Select Location --", $selected, "",1,'','','','','','',"cboLocationId[]" );
										?>
									</td>
									<td width="60">
										<? echo create_drop_down( "cboWithinGroup_1", 55, $yes_no,"", 1, "-- Select --",$within_group, "",1,'','','','','','',"cboWithinGroup[]"); ?>
									</td>
									<td width="100">
										<? 
											echo create_drop_down( "cboPartyId_1", 90, $blank_array,"", 1, "-- Select Location --", $selected, "",1,'','','','','','',"cboPartyId[]" );
										?>
									</td>
									<td width="100">
									<? //echo $buyer_buyer; ?>
										<input name="txtCustBuyer[]" id="txtCustBuyer_1" type="hidden" class="text_boxes" value=""  style="width:87px" readonly />
									</td>
									<td width="70">
										<input name="txtIssueDate[]" id="txtIssueDate_1" type="text" class="datepicker" value=""  style="width:55px" />
									</td>
									<td width="60">
										<input name="txtIssueCh[]" id="txtIssueCh_1" type="text" class="text_boxes" value="" style="width:47px"   />
									</td>
									<td width="140">
									<? //echo $order_no; ?>
										<input name="txtOrder_1" id="txtOrder_1" type="hidden" class="text_boxes" value=""  style="width:47px" readonly />
									</td>
									<td width="140">
									<? //echo $style; ?>
										<input name="txtstyleRef[]" id="txtstyleRef_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $style; ?>"  style="width:47px" readonly />
									</td>
									<td width="100">
									<? //echo $style; ?>
										<!-- <input name="txtstyleRef[]" id="txtstyleRef_<? echo $i; ?>" type="hidden" class="text_boxes" value="<? echo $style; ?>"  style="width:47px" readonly /> -->
									</td>
									<td width="80">
										<? echo create_drop_down( "cboProcessName_1", 70, $emblishment_name_array,"", 1, "--Select--",$main_process_id, "",1,'','','','','','',"cboEmbType[]"); ?>
									</td>
									<td width="60">
										<? 
										echo create_drop_down( "cboEmbType_1", 60, $emb_type,"", 1, "-- Select --",$embl_type_id, "",1,'','','','','','',"cboEmbType[]"); 
										?>
									</td>
									<td width="80">
										<? echo create_drop_down( "cboBodyPart_1", 70, $body_part,"", 1, "-- Select --",$body_part_id, "",1,'','','','','','',"cboBodyPart[]"); ?>
									</td>
									<td width="130">
									<? //echo $color_arr[$color_id]; ?>
										<input type="hidden" id="txtcolor_1" name="txtcolor_1" class="text_boxes" value=""  style="width:87px" readonly/>
									</td>
									<td width="60">
									<? //echo $size_arr[$size_id]; ?>
										<input type="hidden" id="txtsize_1" name="txtsize_1" class="text_boxes txt_size" value=""  style="width:47px" readonly/>
									</td>
									<td width="80">
									<? echo $row[csf("bundle_no")]; ?>
										<input name="txtBundleNo[]" id="txtBundleNo_1" type="hidden" class="text_boxes" value=""  style="width:67px"  title="" readonly />
									</td>
									<td width="60">
									<? //echo $row[csf("bundle_qty")]; ?>
										<input name="txtBundleQty[]" id="txtBundleQty_1" type="hidden" class="text_boxes_numeric" value=""  style="width:67px" readonly />
									</td>
									<td width="60">
									<? //echo $row[csf("bundle_qty")]; ?>
										<input name="txtProdQty[]" id="txtProdQty_1" type="hidden" class="text_boxes_numeric"  style="width:67px"  value="" readonly onKeyUp="fnc_total_calculate ();"  />
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<table cellpadding="0" cellspacing="2" border="1" width="1730" id="tbl_footer" rules="all" align="left">
						<tfoot>
							<tr class="tbl_bottom" name="tr_btm" id="tr_btm">
								<td width="50" >
								All <input form="form_all" type="checkbox" name="check_all" id="check_all" value=""  onclick="fnCheckUnCheckAll(this.checked)"/>
								</td>
								<td width="60" colspan="17">
									<input name="txtTotBndlqty" id="txtTotBndlqty" class="text_boxes_numeric" style="width:55px" type="text" value="<? echo $totBndlQty; ?>" readonly />
								</td>
								<td width="60">
									<input name="txtTotProdqty" id="txtTotProdqty" class="text_boxes_numeric" style="width:55px" type="text" readonly />
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
                <table width="1020" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td align="center" colspan="20" valign="middle" class="button_container">
                        <? echo load_submit_buttons($permission, "fnc_material_production", 0,0,"reset_form('materialissue_1','issue_list_view','','cbouom_1,1', '$(\'#details_tbl tbody tr:not(:first)\').remove(); disable_enable_fields(\'cbo_company_name*cbo_within_group*cbo_party_name*cboProcessName_1*cboReType_1*cboGmtsItem_1*cboBodyPart_1*txtmaterialdescription_1*txtcolor_1*txtsize_1\',0)')",1); ?>
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
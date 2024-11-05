<?
/*-------------------------------------------- Comments
Purpose			: This form will create Stock Yarn Allocation Entry
Functionality	:	
JS Functions	:
Created by		: Abdul Barik Tipu
Creation date 	: 17-08-2021
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
echo load_html_head_contents("Stock Yarn Allocation", "../", 1, 1,'','','');
//-----------------------------------------------------------------------------
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1)
		window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	/*
	|--------------------------------------------------------------------------
	| openmypage_pi
	|--------------------------------------------------------------------------
	|
	*/
	function openmypage_pi()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var supplierID = $("#cbo_supplier_name").val();
		var title = 'PI Search';
		var page_link = 'requires/stock_yarn_allocation_controller.php?action=pi_search_popup&companyID='+companyID+'&supplierID='+supplierID;
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform = this.contentDoc.forms[0];
			var pi_no = this.contentDoc.getElementById("hidden_pi_no").value;
			var pi_id = this.contentDoc.getElementById("hidden_pi_id").value;
			$('#hidden_pi_id').val(pi_id);
			$('#txt_pi_no').val(pi_no);
		}
	}
	
	/*
	|--------------------------------------------------------------------------
	| func_show_details
	|--------------------------------------------------------------------------
	|
	*/
	function func_show_details(type)
	{
		if(form_validation('cbo_company_name*txt_pi_no','Company*PI Number')==false)
		{
			return;
		}
		var data="action=pi_item_details"+get_submitted_data_string('cbo_company_name*cbo_supplier_name*hidden_pi_id*txt_pi_no',"../")+'&type='+type;
		
		freeze_window(5);
		http.open("POST","requires/stock_yarn_allocation_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_show_details_reponse;
	}

	/*
	|--------------------------------------------------------------------------
	| fn_show_details_reponse
	|--------------------------------------------------------------------------
	|
	*/
	function fn_show_details_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText);
			$('#list_container_pi_dtls').html(response);
			set_all_onclick();
			show_msg('18');
			release_freezing();
		}
	}

    function set_form_data(data)
	{
		var data_ref=data.split('**');
		reset_form('','','hdnPiMstId*hdnPiDtlsId*hdnSupplierId*hdnYarnDescId*hdnCountId*hdnYarnTypeId*hdn_balance_qty*txt_allocated_qty*txt_file_no*txt_remarks','','','');
		$("#hdnPiMstId").val(data_ref[0]);
		$("#hdnPiDtlsId").val(data_ref[1]);
		$("#hdnSupplierId").val(data_ref[2]);
		$("#hdnYarnDescId").val(data_ref[3]);
		$("#hdnCompPercentage").val(data_ref[4]);
		$("#hdnCountId").val(data_ref[5]);
		$("#hdnYarnTypeId").val(data_ref[6]);
		$("#hdnColorId").val(data_ref[7]);
		var balance=data_ref[8];
		$("#txt_allocated_qty").attr("placeholder", balance);
		$("#hdn_balance_qty").val(data_ref[8]);
		$("#hdnTotAllocatedQty").val(data_ref[9]);

		set_button_status(0, permission, 'fnc_allocation_entry_details',1,1);

		show_list_view(data_ref[1],'show_dtls_list_view','div_details_list_view','requires/stock_yarn_allocation_controller','');
	}
	
	/*
	|--------------------------------------------------------------------------
	| func_save_update_event
	|--------------------------------------------------------------------------
	|
	*/
	function fnc_allocation_entry_details( operation )
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}

		if(operation==4)
		{
			var print_with_vat=0;
			var report_title=$( "div.form_caption" ).html();
			print_report($('#cbo_company_name').val()+'*'+$('#hdnPiMstId').val()+'*'+$('#hdnPiDtlsId').val()+'*'+report_title,'stock_yarn_allocation_print','requires/stock_yarn_allocation_controller');
			return;
		}

		if(form_validation('cbo_buyer_name','Buyer')==false)
		{
			return;
		}

		if($("#txt_allocated_qty").val()*1<=0)
		{
			alert("Allocated Quantity Should be Greater Than Zero(0).");
			return;
		}

		if(operation==0)
		{
			if($("#txt_allocated_qty").val()*1>$("#hdn_balance_qty").val()*1)
			{
				alert("Allocated Quantity Not Over PI Balance Quantity.");
				$("#txt_allocated_qty").val('');
				return;
			}
		}
		if(operation==1)
		{
			var prevTotAllocatedQty = $("#hdnTotAllocatedQty").val()*1;
			var currentAllocatedQty = $("#hdnThisAllocatedQty").val()*1;
			var allocated_qty = $("#txt_allocated_qty").val()*1;
			var balance_qty = $("#hdn_balance_qty").val()*1;
			// alert((allocated_qty*1-currentAllocatedQty*1)+">"+(balance_qty*1));
			// if((allocated_qty*1)>(balance_qty*1+currentAllocatedQty*1))
			if((allocated_qty*1-currentAllocatedQty*1)>(balance_qty*1))
			{
				alert("Allocated Quantity Not Over PI Balance Quantity.");
				return;
			}
		}

		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('hdnPiMstId*hdnPiDtlsId*hdnSupplierId*hdnYarnDescId*hdnCompPercentage*hdnCountId*hdnYarnTypeId*hdnColorId*hdn_balance_qty*cbo_buyer_name*txt_allocated_qty*txt_file_no*txt_remarks*update_id',"../");
		// alert(data);return;
		http.open("POST","requires/stock_yarn_allocation_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_allocation_entry_reponse;
	}

	function fnc_allocation_entry_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==20)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			show_msg(reponse[0]);
			if(reponse[0]==0 || reponse[0]==1)
			{
				reset_form('palnningEntry_2','div_details_list_view','','','');
				$("#txt_allocated_qty").attr("placeholder", "");

				// $("#update_id").val(reponse[0]);

				show_list_view(reponse[3],'show_dtls_list_view','div_details_list_view','requires/stock_yarn_allocation_controller','');
				
				func_show_details(1);

				set_button_status(0, permission, 'fnc_allocation_entry_details',1,0);
				release_freezing();
			}
			release_freezing();
		}
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../",$permission); ?>
		
		
         <h3 style="width:410px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         	<div id="content_search_panel">      
             	<fieldset style="width:410px;">
                 	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Supplier</th>
                            <th class="must_entry_caption">PI No</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('palnningEntry_1','list_container_pi_dtls','','','')" class="formbutton" style="width:60px" /></th>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?php
                                    echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/stock_yarn_allocation_controller',this.value, 'load_drop_down_supplier', 'supplier_td' );load_drop_down( 'requires/stock_yarn_allocation_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                                    ?>
                                </td>
                                <td id="supplier_td">
                                    <? 
                                    echo create_drop_down( "cbo_supplier_name", 130, $blank_array,"", 1, "-- All Supplier --", $selected, "",0,"" );
                                    ?>
                                </td>
                                <td>
                                    <input type="text" name="txt_pi_no" id="txt_pi_no" class="text_boxes" style="width:100px" placeholder="Browse Or Write" onDblClick="openmypage_pi();">
                                    <input type="hidden" name="hidden_pi_id" id="hidden_pi_id">
                                </td>
                                <td>
                                	<input type="button" value="Show" name="show" id="show" class="formbutton" style="width:60px" onClick="func_show_details(1)"/>
                                </td>                	
                            </tr>
                        </tbody>
                    </table>
            	</fieldset>
            </div>
		

		<div id="list_container_pi_dtls" style="margin-left:10px;margin-top:10px;margin-bottom:10px"></div>

		<form id="palnningEntry_2" autocomplete="off">
        <fieldset style="width:415px;">
	        <legend>Allocation Entry</legend>
	        
		        <table style="border:none" width="415px" cellpadding="0" cellspacing="2" border="0">
		            <thead class="form_table_header">
		                <tr>
		                    <th width="100" class="must_entry_caption">Buyer </th>
		                    <th width="80" class="must_entry_caption">Allocated Qty</th>
		                    <th width="80">Allocate File No</th>
		                    <th>Remarks</th>
		                </tr>
		            </thead>
		            <tr>
		                <td id="buyer_td">
		                	<?
		                		echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
		                	?>
		                </td>
		                <td>
		                    <input type="text" name="txt_allocated_qty" id="txt_allocated_qty" class="text_boxes_numeric" placeholder="" style="width:80px"/>
		                     <!-- onChange="validate_allocat_qty()" -->
		                </td>
		                <td>
		                	<input type="text" name="txt_file_no" id="txt_file_no" value="" class="text_boxes" style="width:80px"/>
		                </td>                
		                <td>
		                	<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" />
		                </td>
		                <input type="hidden" id="hdnPiMstId" />
		                <input type="hidden" id="hdnPiDtlsId" />
		                <input type="hidden" id="hdnSupplierId" />
		                <input type="hidden" id="hdnYarnDescId" />
		                <input type="hidden" id="hdnCompPercentage" />
		                <input type="hidden" id="hdnCountId" />
		                <input type="hidden" id="hdnYarnTypeId" />
		                <input type="hidden" id="hdnColorId" />
		                <input type="hidden" id="hdn_balance_qty" />
		                <input type="hidden" id="hdnTotAllocatedQty" />
		                <input type="hidden" id="hdnThisAllocatedQty" />
		                <input type="hidden" id="update_id" />
		            </tr>
		            <tr>
		                <td colspan="4" valign="middle" align="center" class="button_container">
							<?
		                        echo load_submit_buttons( $permission, "fnc_allocation_entry_details", 0,1 ,"reset_form('palnningEntry_2','','','','')",1);
		                    ?>
		                </td>
		            </tr>
		        </table>	        
	        	<div style="width:415px;" id="div_details_list_view"></div>
        </fieldset>
        </form>
	</div>
    
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
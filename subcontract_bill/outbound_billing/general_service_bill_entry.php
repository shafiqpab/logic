<?php
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Out Bound Yarn Service Bill Entry
Functionality	:	
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor
Creation date 	: 	08-06-2021
Updated by 		: 		
Update date		: 
Oracle Convert 	:
Convert date	:
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == '' ) header('location:login.php');
 
require_once('../../includes/common.php');
extract($_REQUEST);

$_SESSION['page_permission']=$permission;
//-----------------------------------------------------------------------------------------
echo load_html_head_contents('Yarn Service Bill Entry', '../../', 1, 1, $unicode, 0, '');
?>

<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../logout.php';
	var permission='<?php echo $permission; ?>';
	
	var seq_arr=new Array(); 
	var uom_arr = new Array();
	var selected_id = new Array(); 
	var selected_currency_id = new Array();
	var selected_id_listed = new Array();

	function toggle( x, origColor )
	{
		//alert (x);
		var newColor = 'yellow';
		if ( x.style )
		{
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}


	function js_set_value(id)
	{
		var str=id.split("_");
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		if( jQuery.inArray(  str[0] , selected_id ) == -1) {
			
			selected_id.push( str[0] );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
			if( selected_id[i] == str[0]  ) break;
		}
			selected_id.splice( i, 1 );
		}
		var id = ''; var currency = '';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		
		$('#selected_id').val( id );
	}

	function fnc_check(inc_id)
	{		
		if(document.getElementById('checkid'+inc_id).checked==true)
		{
			//$('#checkid'+inc_id).attr("checked", false);
			document.getElementById('checkid'+inc_id).value=1;
		}
		else if(document.getElementById('checkid'+inc_id).checked==false)
		{
			//$('#checkid'+inc_id).attr("checked", true);
			document.getElementById('checkid'+inc_id).value=2;
		}
	}


	function amount_calculation(id)
	{		
		var tot_row=$('#outside_yarnservicebill_table tr').length;
		$("#txtAmount_"+id).val( ($("#txtYarnQty_"+id).val()*1)*($("#txtRate_"+id).val()*1) );
		
		math_operation( "txt_tot_qnty", "txtYarnQty_", "+", tot_row );
		math_operation( "txt_tot_amount", "txtAmount_", "+", tot_row );
		
		var curanci = $("#curanci_"+id).val()*1;
		if(curanci==1)
		{
			$("#txtDomesticAmount_"+id).val( $("#txtAmount_"+id).val()*1 );
		}
		else if(curanci>1)
		{
			get_php_form_data( id+"_"+curanci+"_"+$("#txtAmount_"+id).val()*1, "load_domestic_amount", "requires/general_service_bill_entry_controller" );
		}
		else
		{
			alert("Currency type is empty.");
		}
	}

	function openmypage_remarks(id)
	{
		var data=document.getElementById('txtRemarks_'+id).value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/general_service_bill_entry_controller.php?data='+data+'&action=remarks_popup','Remarks Popup', 'width=420px,height=320px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("text_new_remarks");//Access form field with id="emailfield"
			if (theemail.value!="")
			{
				$('#txtRemarks_'+id).val(theemail.value);
			}
		}
	}

	function fnc_saveUpdateDelete( operation ) // For Save and Update
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_bill_no').val()+'*'+$('#cbo_location_name').val()+'*'+report_title,"general_service_bill_entry_print", "requires/general_service_bill_entry_controller")
			//show_msg("3");
			
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if( form_validation('cbo_company_id*cbo_currency_id*cbo_pay_mode','Company Name*Currency*Pay Mode')==false)
			{
				return;
			}
			
			var post_account=$('#txt_is_posted_account').val();
			if(post_account==1){
				//alert("Already Posted In Accounting. Save Update Delete Restricted");return;
			}

			var j=0; var dataString=''; 
			$("#tbl_dtls_item").find('tbody tr').each(function()
			{			
				var txtProduct_id=$(this).find('input[name="txtProduct_id[]"]').val();
				var txtAssetNo=$(this).find('input[name="txtAssetNo[]"]').val();
				var txtqnty=$(this).find('input[name="txtqnty[]"]').val();
				var txtrate=$(this).find('input[name="txtrate[]"]').val();
				var txtamount=$(this).find('input[name="txtamount[]"]').val();
				var txtremarks=$(this).find('input[name="txtremarks[]"]').val();
				var txt_row_id=$(this).find('input[name="txt_row_id[]"]').val();
				var txt_dtls_id=$(this).find('input[name="txt_dtls_id[]"]').val();	
				var service_dtls_id=$(this).find('input[name="txt_service_dtls[]"]').val();	
				var check=$(this).find('input[name="check[]"]').val();		
				
				if(txtqnty>0 && txtrate>0)
				{
					j++;
					var qnty=$('#txtqnty_'+j).val()*1;
					var bln_qnty=$('#txt_balance_'+j).val()*1;
			
					if(bln_qnty < qnty){
						alert("Bill Entry Qnty  more than Work Order Qnty");
						return;
					}
					
					dataString+='&txtProduct_id_' + j + '=' + txtProduct_id + '&txtAssetNo_' + j + '=' + txtAssetNo+'&txtqnty_' + j + '=' + txtqnty  + '&txtrate_' + j + '=' + txtrate+ '&txtamount_' + j + '=' + txtamount + '&txtremarks_' + j + '=' + txtremarks + '&txt_dtls_id_' + j + '=' + txt_dtls_id + '&txt_row_id_' + j + '=' + txt_row_id + '&check_' + j + '=' + check + '&txt_service_dtls_' + j + '=' + service_dtls_id;
				}
			});
		
			var data="action=save_update_delete&operation="+operation+'&total_row='+j+get_submitted_data_string('cbo_company_id*cbo_location_name*txt_bill_date*cbo_supplier_company*cbo_pay_mode*hidden_wo_update_id*txt_party_bill*txtexchange_rate*cbo_currency_id*txt_wo_number*hidden_wo_update_id*update_id*txt_bill_no*txt_tenor*cbo_approved*txt_remarks*txt_is_posted_account',"../../")+dataString;		
			//alert (data); return; 
			freeze_window(operation);
			http.open("POST", "requires/general_service_bill_entry_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_saveUpdateDelete_response;
		}
	}

	function fnc_saveUpdateDelete_response() // For Save and Update response
	{
		if(http.readyState == 4) 
		{
			//alert (http.responseText);//return;
			var response=trim(http.responseText).split('**');
			if(response[0]==12)
			{
				alert(response[1]);
				release_freezing();
				return;
			}

			show_msg(response[0]);
			if(response[0]==0 || response[0]==1)
			{
				document.getElementById('update_id').value = response[2];
				document.getElementById('txt_bill_no').value = response[1];
				set_button_status(1, permission, 'fnc_saveUpdateDelete', 1);
			}
			if(response[0]==13)
			{
				alert(response[1]);
			}
			release_freezing();
		}
	}

	function openmypage_outside_bill()
	{ 
		if( form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
	
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_supplier_company').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/general_service_bill_entry_controller.php?data='+data+'&action=outside_bill_popup','Outside Bill Popup', 'width=700px,height=400px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById('outside_bill_id');
			if (theemail.value!="")
			{
				// freeze_window(5);
				get_php_form_data( theemail.value, 'load_php_data_to_form_outside_bill', 'requires/general_service_bill_entry_controller');
				
				set_button_status(1, permission, 'fnc_saveUpdateDelete',1);				
				// set_all_onclick();
				release_freezing();
			}
		}
	}

	function openmypage_wo()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}

		var company = $("#cbo_company_id").val();		

		var page_link = 'requires/general_service_bill_entry_controller.php?action=wo_popup&company='+company;
		var title = "WO Search";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hidden_wo_number=this.contentDoc.getElementById("hidden_wo_number").value.split("_");		
			// reset_form('outfinishingbill_1','outside_yarnservicebill_table','','','','');
			$("#txt_wo_number").val(hidden_wo_number[0]);
			$("#hidden_wo_update_id").val(hidden_wo_number[1]);
			var txt_bill_no=$("#update_id").val();
			var  company_id=$("#cbo_company_id").val();		
			
			get_php_form_data(hidden_wo_number[1], "populate_data_from_search_popup", "requires/general_service_bill_entry_controller" );
			disable_enable_fields( 'cbo_company_name*cbo_supplier_company*cbo_currency', 1, '', '' );
			show_list_view(hidden_wo_number[1]+'_'+txt_bill_no+'_'+company_id,'show_service_wo_dtls_listview','outside_yarnservicebill_table','requires/general_service_bill_entry_controller','');
			// set_button_status(1, permission, 'fnc_list_search',1);
			//release_freezing();
		}
	}

	function calculate_amount()
	{
		var tot_row=$('#tbl_dtls_item'+' tbody tr').length;  
		for(var i=1; i<=tot_row; i++)
		{
			var quantity_val=parseFloat(Number($('#txtqnty_'+i).val()));
			var rate_val=parseFloat(Number($('#txtrate_'+i).val()));
			var attached_val=quantity_val*rate_val;
			document.getElementById('txtamount_'+i).value = number_format (attached_val, 2,'.',"");
		}
	}

	function check_exchange_rate()
	{
		var cbo_currercy=$('#cbo_currency_id').val();
		var bill_date = $('#txt_bill_date').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+bill_date, 'check_conversion_rate', '', 'requires/general_service_bill_entry_controller');
		var response=response.split("_");
		$('#txtexchange_rate').val(response[1]);
		// $('#txtexchange_rate').attr('disabled','disabled');
	}

	function is_text_field()
	{
		var cbo_fixed_asset = $("#cbo_fixed_asset").val();
		if (cbo_fixed_asset == 1)
		{
			$("#asset_no_td").removeAttr("style");
			$("#asset_no_val_td").removeAttr("style");			
		}
		else
		{
			$("#asset_no_td").css("display", "none");
			$("#asset_no_val_td").css("display", "none");
		}
	}

	function search_asset(i)
	{
		emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/general_service_bill_entry_controller.php?action=search_asset_entry' + '&cbo_company_name=' + $('#cbo_company_id').val(), 'Asset Acquisition Search', 'width=1085px,height=400px,center=1,resize=0,scrolling=0', '../')
		emailwindow.onclose = function ()
		{
			var theform = this.contentDoc.forms[0];
			var data = this.contentDoc.getElementById("hidden_system_number").value;
			var v=$("#txtAssetNo_"+i).val(data);		
			//get_php_form_data(data, "populate_asset_details_form_data", "requires/asset_acquisition_controller");
			//show_list_view(data, 'show_asset_active_listview', 'asset_list_view', 'requires/asset_acquisition_controller', 'setFilterGrid(\'list_view\',-1)');
		}
	}

	function check_bill_status(id)
	{
		var status=$("#check_"+id).val();
		if(status==0){
			$("#check_"+id).val(1);
		}else{
			$("#check_"+id).val(0);
		}
	}
	$(document).ready(function(){$('#notice').hide();});
</script>
</head>
<body>
    <div align="center" style="width:100%;">
    <?php echo load_freeze_divs('../../', $permission); ?>
    <form name="general_service_bill_1" id="general_service_bill_1" autocomplete="off">
    <fieldset style="width:810px;">
    <legend>General Service Bill Info</legend>
        <table cellpadding="0" cellspacing="2" width="100%">
            <tr>
                <td align="right" colspan="3"><strong>Bill No</strong></td>
                <td width="150">
                	<input type="hidden" name="hidden_acc_integ" id="hidden_acc_integ" />
                    <input type="hidden" name="selected_id" id="selected_id" />
                    <input type="hidden" name="update_id" id="update_id" />
                    <input type="hidden" name="variable_check" id="variable_check" />
					<input type="hidden" name="txt_is_posted_account" id="txt_is_posted_account" />
                    <input type="text" name="txt_bill_no" id="txt_bill_no" class="text_boxes" style="width:140px" placeholder="Double Click to Search" onDblClick="openmypage_outside_bill();" readonly tabindex="1" >
                </td>
                <td colspan="2">&nbsp;</td>
             </tr>
             <tr>
                <td width="120" class="must_entry_caption">Company</td>
                <td width="150">
					<?
						echo create_drop_down("cbo_company_id",150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/general_service_bill_entry_controller', this.value, 'load_drop_down_location', 'location_td'); load_drop_down( 'requires/general_service_bill_entry_controller', this.value, 'load_drop_down_supplier_name', 'supplier_td');","","","","","",2);
                    ?>
                </td>
                <td width="120">Location</td>                                              
                <td width="150" id="location_td">
					<?
						echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "--Select Location--", $selected,"","","","","","",3);
                    ?>
                </td>
                <td width="90" class="must_entry_caption">Bill Date</td>                                              
                <td width="140">
                    <input class="datepicker" type="text" style="width:140px" name="txt_bill_date" id="txt_bill_date" value="<? echo date('d-m-Y'); ?>" tabindex="4" />
                </td>
            </tr> 
            <tr>
                <td class="must_entry_caption">Supplier</td>
                <td id="supplier_td">
					<?
						echo create_drop_down( "cbo_supplier_company", 150, $blank_array,"", 1, "-- Select supplier --", $selected, "",0,"","","","",5);
					?> 	
                </td>
                <td>Exchange Rate</td>               
				<td><input type="text" name="txtexchange_rate" id="txtexchange_rate" class="text_boxes" style="width:140px" placeholder=" Exchange Rate ">	</td>
                <td>Party Bill No</td>
                <td><input type="text" name="txt_party_bill" id="txt_party_bill" class="text_boxes" style="width:140px" placeholder="Party Bill" ></td>
            </tr>
            <tr>
			<td class="must_entry_caption">Currency</td>
			<td>
				<?
					echo create_drop_down( "cbo_currency_id", 150, $currency, "", 1, "-- Select Currency --", $currencyID, "check_exchange_rate();", "" );					
				?>
			</td>               
                <td>WO Num</td>                                              
				<td>
					<input type="text" name="txt_wo_number" id="txt_wo_number" class="text_boxes" style="width:140px" placeholder="Double Click to Search" onDblClick="openmypage_wo();" readonly />
					<input type="hidden" id="hidden_wo_update_id" name="hidden_wo_update_id">
                </td>              

				<td class="must_entry_caption">Pay Mode</td>               
				<td>
					<?
					echo create_drop_down( "cbo_pay_mode", 155, $pay_mode,"", 1, "-- Select --", 0, "",0 );
					?> 								
 				</td>
            </tr>
			<tr>
				<td>Tenor</td>
				<td><input type="text" id="txt_tenor"  name="txt_tenor" class="text_boxes"  style="width:140px" ></td>
				<td>Ready&nbsp;to&nbsp;Approve </td>
				<td>
					<? 
					$ready_to_approve=array(1=>"Yes",2=>"No");
					echo create_drop_down( "cbo_approved", 145,  $ready_to_approve,"", 1, "-- Select--", 0, "","","" ); 
					?>
				</td>
				<td>Remarks</td>
				<td ><input type="text" id="txt_remarks" name="txt_remarks"  class="text_boxes" style="width:140px"  ></td>
			</tr>
			<tr>
				<td colspan="7" id="notice"><h1 align="center" style="color:red;font-size: 25px;">Already Posted In Accounting.</h1> </td>
			</tr>
			<tr>
				<td colspan="2"><div id="approved" style="font-size: 18px;color: red"> </div></td>
			</tr>
        </table>
    </fieldset>
    &nbsp;
    <fieldset style="width:1400px;">
    <legend>General Service Bill Details </legend>
        <table class="rpt_table" width="1300" cellspacing="0" cellpadding="0" id="tbl_dtls_item"  border="1" rules="all">
			<thead>
				<th width="30">Check In</th>
				<th width="90">Service For</th>
				<th width="120">Service Details</th>
				<th width="120">Product ID</th>
				<th width="130">Item Description</th>
				<th width="100">Item Category</th>
				<th width="100">Item Group</th>										
				<th width="120">Asset No</th>
				<th width="80">Qnty</th>					
				<th width="60">Rate</th>
				<th width="90">Amount</th>						
				<th width="80">Pay Mode</th>
				<th width="120">Remarks</th>
				<th></th>
			</thead>
            <tbody id="outside_yarnservicebill_table">    
            </tbody>
            <tfoot>               
                <tr>
                    <td colspan="17" align="center" class="button_container">
						<?						
						echo load_submit_buttons( $permission, "fnc_saveUpdateDelete", 0,1 ,"reset_form('general_service_bill_1','','','','','');",1);
                        ?> 
                    </td>
                </tr>  
            </tfoot>
        </table>
    </fieldset>
    <br>
    <div id="outside_yarn_service_info_list"></div>  
    </form>
	<div style="width:250px; margin-top:13px; margin-left:0px; float:left;">
		<div id="wonum_list_view"></div>
	</div>
    </div>         
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
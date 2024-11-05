<?php
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Out Bound Packing And Finishing Bill Entry
Functionality	:	
JS Functions	:
Created by		:	Sapayth
Creation date 	: 	29-09-2020
Updated by 		: 		
Update date		: 
Oracle Convert 	:
Convert date	:
QC Performed BY	:		
QC Date			:	
Comments		: Mostly follows outbound > Sewing Bill Entry page
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == '' ) header('location:login.php');
 
require_once('../../includes/common.php');
extract($_REQUEST);

$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents('Packing and Finishing Bill Entry', '../../', 1, 1, $unicode, 0, '');
?>

<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<?php echo $permission; ?>';
	var selected_id = new Array();
	var selected_currency_id = new Array();

	function openmypage_bill() {
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_supplier_company').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/packing_and_finishing_bill_entry_controller.php?data='+data+'&action=bill_no_popup','Bill Popup', 'width=700px,height=400px,center=1,resize=1,scrolling=0','../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("packing_id"); //Access form field with id="emailfield"

			if (theemail.value!="")
			{
				freeze_window(5);
				get_php_form_data( theemail.value, "load_php_data_to_form_packing", "requires/packing_and_finishing_bill_entry_controller" );
				window_close( theemail.value );
				show_list_view(document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_supplier_company').value+'_'+document.getElementById('update_id').value,'packing_entry_list_view','packing_info_list','requires/packing_and_finishing_bill_entry_controller','set_all();');

				set_button_status(1, permission, 'fnc_packing_bill_entry', 1, 1);
				// set_button_status(is_update, permission, submit_func, btn_id, show_print)
				set_all_onclick();
				release_freezing();
			}
		}
	}

	function set_all()
	{
		var old=document.getElementById('issue_id_all').value;
		if(old!="")
		{   
			old=old.split(",");
			for(var i=0; i<old.length; i++)
			{   
				js_set_value( old[i]+"_"+document.getElementById('currid'+old[i]).value ) 
			}
		}
	}

	function window_close( frm )
	{
		if ( !frm ) var frm='';
		 
		if ($('#update_id').val()!=frm)
			var issue_id=document.getElementById('issue_id_all').value;
		else
			var issue_id='';
			
			var supplier_company=document.getElementById('cbo_supplier_company').value;
		var data=document.getElementById('selected_id').value+"_"+issue_id+"_"+frm+"_"+supplier_company;
		//alert (data);
		var list_view_orders = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/packing_and_finishing_bill_entry_controller');
		if(list_view_orders!='')
		{
			$("#bill_issue_table tr").remove();
			$("#bill_issue_table").append(list_view_orders);
		}
		var tot_row=$('#bill_issue_table tr').length;
		math_operation( "txt_tot_qnty", "txt_qnty_", "+", tot_row );
		math_operation( "txt_tot_amount", "txt_amount_", "+", tot_row );
		set_all_onclick();
	}

	function check_all_data()
	{
		var tbl_row_count = document.getElementById( 'list_view_issue' ).rows.length;
		tbl_row_count = tbl_row_count - 1;
		
		for( var i = 1; i <= tbl_row_count; i++ ) 
		{
			eval($('#tr_'+i).attr("onclick"));  
		}
	}

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

	function fnc_packing_bill_entry( operation )
	{
		var tot_row=$('#bill_issue_table tr').length;

		if(operation==4)
		{
			/*print_report( $('#txt_bill_no').val()+'*'+$('#cbo_company_id').val()+'*'+$('#cbo_party_name').val()+'*'+$('#issue_id_all').val()+'*'+$('#txt_bill_date').val()+'*'+$('#selected_order_id').val(), "packing_bill_entry_print", "requires/packing_and_finishing_bill_entry_controller") 
			//return;
			show_msg("3");*/  cbo_currency
			var poBreakDownIds = '';

			for(var i=1; i<=tot_row; i++)
			{
				// poBreakDownIds+=get_submitted_data_string('ordernoid_'+i, '../../');
				poBreakDownIds+=$('#ordernoid_'+i).val() + ',';
			}
			
			poBreakDownIds = poBreakDownIds.substring(0, poBreakDownIds.length - 1);

			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_bill_no').val()+'*'+report_title+'*'+poBreakDownIds,"packing_bill_entry_print", "requires/packing_and_finishing_bill_entry_controller")
			show_msg("3");
			return;
		}

		if ( form_validation('cbo_company_id*txt_bill_date*cbo_supplier_company*txt_rate_1','Company Name*Bill Date*Supplier Name*Rate')==false ) {
			return;
		}
		
		freeze_window(operation);
		var data2='';
		for(var i=1; i<=tot_row; i++)
		{
			data2+=get_submitted_data_string('reciveid_'+i+'*txtreceivedate_'+i+'*txt_challenno_'+i+'*ordernoid_'+i+'*itemid_'+i+'*text_wo_num_'+i+'*cbouom_'+i+'*txt_qnty_'+i+'*txt_rate_'+i+'*txt_amount_'+i+'*curanci_'+i+'*txt_remarks_'+i+'*updateiddtls_'+i+'*delete_id',"../../");
		}
		var data1="action=save_update_delete&operation="+operation+"&tot_row="+tot_row+get_submitted_data_string('txt_bill_no*cbo_company_id*cbo_location_name*txt_bill_date*cbo_supplier_company*cbo_currency*txt_exchange_rate*update_id', '../../');
		//alert (data1); 
		var data=data1+data2;//
		 //alert (data); return;
		
		http.open("POST", "requires/packing_and_finishing_bill_entry_controller.php", true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_packing_bill_entry_reponse;
	}

	function fnc_packing_bill_entry_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert (http.responseText);//return;
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
			document.getElementById('update_id').value = response[1];
			document.getElementById('txt_bill_no').value = response[2];
			window_close(response[1]);
			set_button_status(1, permission, 'fnc_packing_bill_entry', 1, 1);
			release_freezing();
		}
	}
	
	function amount_calculation(id)
	{
		var tot_amount='';
		tot_amount=(document.getElementById('txt_qnty_'+id).value*1)*(document.getElementById('txt_rate_'+id).value*1);
		document.getElementById('txt_amount_'+id).value=tot_amount;
		math_operation( 'txt_tot_amount', 'txt_amount_', '+', id );
	}

	function openmypage_wonum(i)
	{ 
		/*if ( form_validation('txtreceivedate_1*txt_challenno_1','Receive Date*Challen No')==false )
		{
			return;
		}*/
		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_supplier_company').value+"_"+document.getElementById("ordernoid_"+i).value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/packing_and_finishing_bill_entry_controller.php?action=wonum_popup&data='+data,'Wo Popup', 'width=950px,height=400px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("hidd_item_id") 
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				//alert (response[0]);
				var tot_row=$('#bill_issue_table tr').length;
				var txt_bill_no=document.getElementById('txt_bill_no').value;
				var update_id=document.getElementById('update_id').value;
				document.getElementById('text_wo_id_'+i).value=response[0];
				document.getElementById('text_wo_num_'+i).value=response[1];
				document.getElementById('txt_rate_'+i).value=response[2];
				
				/*if(response.length>3 )
				{
					
					if(response[3] && txt_bill_no.length==0 && update_id.length==0)
					{
						document.getElementById('curanci_'+i).value=response[3];
					}
					
				}*/
				exchenge_rate_val(response[2])
				release_freezing();
			}
		}
	}

	function exchenge_rate_val(rate)
	{
		var tot_row=$('#bill_issue_table tr').length;
		var amount_total=0;
		for(var k=1; k<=tot_row; k++)
		{
			amount_total=(document.getElementById('txt_qnty_'+k).value*1)*(rate*1);
			document.getElementById('txt_amount_'+k).value=amount_total;
		}
		ddd={dec_type:5,comma:0};
		math_operation("txt_tot_qnty", "txt_qnty_", "+", tot_row,ddd);
		math_operation("txt_tot_amount", "txt_amount_", "+", tot_row,ddd);
	}
 
	function check_exchange_rate()
	{
		var tot_row=$('#bill_issue_table tr').length;
		var cbo_currercy = $('#cbo_currency').val();
		var booking_date = $('#txt_bill_date').val();
		var company_id = $('#cbo_company_id').val();
		data = cbo_currercy+'*'+booking_date+"*"+company_id+"*"+tot_row;
		get_php_form_data(data, "check_conversion_rate", "requires/packing_and_finishing_bill_entry_controller" );
	} 
</script>
</head>
<body>
    <div align="center" style="width:100%;">
    <?php echo load_freeze_divs ('../../', $permission); ?>
    <form name="packingfinishingbill_1" id="packingfinishingbill_1" autocomplete="off"  >
    <fieldset style="width: 65%;">
    <legend>Packing and Finishing Bill Info</legend>
        <table cellpadding="0" cellspacing="2" width="100%">
            <tr>
                <td align="right" colspan="3"><strong>Bill No</strong></td>
                <td width="140" align="justify">
                    <input type="hidden" name="selected_id" id="selected_id" />
                    <input type="hidden" name="update_id" id="update_id" />
                    <input type="text" name="txt_bill_no" id="txt_bill_no" class="text_boxes" style="width:140px" placeholder="Double Click to Search" onDblClick="openmypage_bill();" readonly tabindex="1" >
                </td>
            </tr>
            <tr>
                <td width="120" class="must_entry_caption">Company Name</td>
                <td width="150">
                    <?php
                    echo create_drop_down( 'cbo_company_id', 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", 'id,company_name', 1, '--Select Company--', $selected, "load_drop_down( 'requires/packing_and_finishing_bill_entry_controller', this.value, 'load_drop_down_location', 'location_td');load_drop_down( 'requires/packing_and_finishing_bill_entry_controller', this.value, 'load_drop_down_supplier_name', 'supplier_td');", '', '', '', '', '', 2);	
                    ?>
                </td>
                <td width="120">Location Name</td>
                <td width="150" id="location_td">
                    <?php
                    echo create_drop_down( 'cbo_location_name', 150, $blank_array, '', 1, '--Select Location--', $selected, '', '', '', '', '', '', 3);
                    ?>
                </td>
                <td width="90" class="must_entry_caption">Bill Date</td>
                <td width="140">
                    <input class="datepicker" type="text" style="width:140px" name="txt_bill_date" id="txt_bill_date" tabindex="4" />
                </td>
            </tr> 
            <tr>
                <td class="must_entry_caption">Supplier Name</td>
                <td id="supplier_td">
                    <?php
                    echo create_drop_down('cbo_supplier_company', 150, $blank_array, '', 1, '-- Select Supplier --', $selected, '', 0, '', '', '', '', 5);
                    ?> 
                </td>
                <td>Currency</td>
                <td id="currency_td">
				   <? echo create_drop_down("cbo_currency", 140, $currency,"", 1, "-- Select --", 2, "check_exchange_rate(this.value);", 0); ?>
                </td> 
                <td>Exchange Rate</td>
                <td><input style="width:130px;" type="text" class="text_boxes"  name="txt_exchange_rate" id="txt_exchange_rate" readonly  /></td>
            </tr>
        </table>
    </fieldset>
    <br>
    <fieldset style="width:940px;">
    <legend>Packing and Finishing Bill Details </legend>
        <table  style="border:none; width:930px;" cellpadding="0" cellspacing="1" border="0" id="">
            <thead class="form_table_header">
                <th width="70">Receive Date </th>
                <th width="80">Challan No.</th>
                <th width="60">Order No.</th>
                <th width="80">Style</th>
                <th width="90">Buyer</th>
                <th width="130">Garments Item</th>
                <th width="60">WO Num</th>
                <th width="40">UOM</th>
                <th width="60">Garments Qty</th>
                <th width="40" class="must_entry_caption">Rate(BDT)</th>
                <th width="60">Amount</th>
				<th width="60">Currency</th>
                <th>Remarks</th>
            </thead>
            <tbody id="bill_issue_table">
                <tr align="center">
                    <td>
                        <input type="hidden" name="updateiddtls_1" id="updateiddtls_1" style="width:30px">
                        <input type="text" name="txtreceivedate_1" id="txtreceivedate_1"  class="datepicker" style="width:65px" readonly />
                    </td>
                    <td>
                        <input type="text" name="txt_challenno_1" id="txt_challenno_1"  class="text_boxes" style="width:85px" readonly />
                    </td>
                    <td>
                        <input type="hidden" name="ordernoid_1" id="ordernoid_1" value="" style="width:40px">
                        <input type="text" name="txt_orderno_1" id="txt_orderno_1"  class="text_boxes" style="width:55px" readonly />
                    </td>
                    <td>
                        <input type="text" name="txt_stylename_1" id="txt_stylename_1"  class="text_boxes" style="width:75px;" />
                    </td>
                    <td>
                        <input type="text" name="txt_partyname_1" id="txt_partyname_1"  class="text_boxes" style="width:85px" />
                    </td>
                    <td>
                        <input type="text" name="text_febricdesc_1" id="text_febricdesc_1"  class="text_boxes" style="width:125px" readonly/>
                    </td>
                    <td>
                        <input type="text" name="text_wo_num_1" id="text_wo_num_1"  class="text_boxes" style="width:60px" placeholder="Browse" onDblClick="openmypage_wonum(1);" readonly/> 
                        <input type="hidden" name="text_wo_id_1" id="text_wo_id_1">
                    </td>
                    <td>
						<?php echo create_drop_down( 'cbouom_1', 45, $unit_of_measurement, '', 0, '--Select UOM--', 1, '', '', '1,2' );?>
                    </td>
                    <td>
                        <input type="text" name="txt_qnty_1" id="txt_qnty_1"  class="text_boxes_numeric" style="width:60px"   />
                    </td>
                    <td>
                        <input type="text" name="txt_rate_1" id="txt_rate_1"  class="text_boxes_numeric" style="width:40px" />
                    </td>

                    <td>
                        <input type="text" name="txt_amount_1" id="txt_amount_1" class="text_boxes_numeric" style="width:60px" readonly />
                    </td>
					<td>
                    	<? echo create_drop_down( "curanci_1", 60, $currency,"", 1, "-Currency-",1,"","","" ); ?>
                    </td>
                    <td>
                        <input type="text" name="txt_remarks_1" id="txt_remarks_1"  class="text_boxes" style="width:95px" />
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                	<td width="70px">&nbsp;</td>
                    <td width="80px">&nbsp;</td>
                    <td width="60px">&nbsp;</td>
                    <td width="80px">&nbsp;</td>
                    <td width="90px">&nbsp;</td>
                    <td width="130px">&nbsp;</td>
                    <td width="100px" align="right" colspan="2">Total Qty:</td>
                    <td width="60px">
                    	<input type="text" name="txt_tot_qnty" id="txt_tot_qnty"  class="text_boxes_numeric" style="width:60px" readonly disabled />
                    </td>
                    <td width="40px">&nbsp;</td>
                    <td width="60px">
                    	<input type="text" name="txt_tot_amount" id="txt_tot_amount"  class="text_boxes_numeric" style="width:60px" readonly  disabled/>
                    </td>
                    <td>&nbsp;</td>
					<td>&nbsp;</td>
                </tr>                
                <tr>
                    <td colspan="12" align="center" class="button_container">
						<?php 
							echo load_submit_buttons($permission, 'fnc_packing_bill_entry', 0, 1, "reset_form('packingfinishingbill_1','packing_info_list','','','$(\'#bill_issue_table tr:not(:first)\').remove();')", 1);
							// load_submit_buttons($permission, $sub_func, $is_update, $is_show_print, $refresh_function, $btn_id, $is_show_approve)
                        ?> 
                    </td>
                </tr>
                <tr>
                    <td colspan="12" id="list_view" align="center"></td>
                </tr>
            </tfoot>
        </table>
        </fieldset> 
        </form>
        <br>
        <div id="packing_info_list"></div>
   </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>$('#cbo_currency').val(0);</script>
</html>
<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Out Side Sewing Bill Entry						
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	04-06-2014
Updated by 		: 		
Update date		: 
Oracle Convert 	:	Kausar		
Convert date	: 	04-06-2014	   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
 
require_once('../includes/common.php');
extract($_REQUEST);

$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sewing Bill Entry", "../", 1,1, $unicode,1,'');
?>

<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';

	function openmypage_bill()
	{ 
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_supplier_company').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/outside_sewing_bill_entry_controller.php?data='+data+'&action=bill_no_popup','Bill Popup', 'width=700px,height=400px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("issue_id"); //Access form field with id="emailfield"
			if (theemail.value!="")
			{
				freeze_window(5);
				get_php_form_data( theemail.value, "load_php_data_to_form_issue", "requires/outside_sewing_bill_entry_controller" );
				window_close( theemail.value );
				show_list_view(document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_supplier_company').value+'_'+document.getElementById('update_id').value,'sewing_entry_list_view','sewing_info_list','requires/outside_sewing_bill_entry_controller','set_all();');
				set_button_status(1, permission, 'fnc_sewing_bill_entry',1);
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
		var data=document.getElementById('selected_id').value+"_"+issue_id+"_"+frm;
		//alert (data);
		var list_view_orders = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/outside_sewing_bill_entry_controller');
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


	var selected_id = new Array(); var selected_currency_id = new Array();
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

	function fnc_sewing_bill_entry( operation )
	{
		 if(operation==4)
		 {
			//print_report( $('#txt_bill_no').val()+'*'+$('#cbo_company_id').val()+'*'+$('#cbo_party_name').val()+'*'+$('#issue_id_all').val()+'*'+$('#txt_bill_date').val()+'*'+$('#selected_order_id').val(), "sewing_bill_entry_print", "requires/outside_sewing_bill_entry_controller") 
			//return;

			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_bill_no').val()+'*'+report_title,"sewing_bill_entry_print", "requires/outside_sewing_bill_entry_controller")
			show_msg("3");
			
		 }
		else if(operation==0 || operation==1 || operation==2)
		{
			if ( form_validation('cbo_company_id*txt_bill_date*cbo_supplier_company*txt_challenno_1*txt_rate_1','Company Name*Bill Date*Supplier Name*Challen No*Rate')==false )
			{
				return;
			}
			else
			{	
				var tot_row=$('#bill_issue_table tr').length;
				var data2='';
				for(var i=1; i<=tot_row; i++)
				{
					data2+=get_submitted_data_string('reciveid_'+i+'*txtreceivedate_'+i+'*txt_challenno_'+i+'*ordernoid_'+i+'*itemid_'+i+'*text_wo_num_'+i+'*cbouom_'+i+'*txt_qnty_'+i+'*txt_rate_'+i+'*txt_amount_'+i+'*curanci_'+i+'*txt_remarks_'+i+'*updateiddtls_'+i+'*delete_id',"../");
				}
				var data1="action=save_update_delete&operation="+operation+"&tot_row="+tot_row+get_submitted_data_string('txt_bill_no*cbo_company_id*cbo_location_name*txt_bill_date*cbo_supplier_company*update_id',"../");
				//alert (data1); 
				var data=data1+data2;//
				 //alert (data); return;
				freeze_window(operation);
				http.open("POST","requires/outside_sewing_bill_entry_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_sewing_bill_entry_reponse;
			}
		}
	}

	function fnc_sewing_bill_entry_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert (http.responseText);//return;
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
			document.getElementById('update_id').value = response[1];
			document.getElementById('txt_bill_no').value = response[2];
			window_close(response[1]);
			set_button_status(1, permission, 'fnc_sewing_bill_entry',1);
			release_freezing();
		}
	}
	
	function amount_caculation(id)
	{
		var tot_amount='';
		tot_amount=(document.getElementById('txt_qnty_'+id).value*1)*(document.getElementById('txt_rate_'+id).value*1);
		document.getElementById('txt_amount_'+id).value=tot_amount;
		math_operation( "txt_tot_amount", "txt_amount_", "+", id );
	}

	function openmypage_wonum(i)
	{ 
		/*if ( form_validation('txtreceivedate_1*txt_challenno_1','Receive Date*Challen No')==false )
		{
			return;
		}*/
		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_supplier_company').value+"_"+document.getElementById("ordernoid_"+i).value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/outside_sewing_bill_entry_controller.php?action=wonum_popup&data='+data,'Wo Popup', 'width=950px,height=400px,center=1,resize=1,scrolling=0','')
		
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

</script>
</head>
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
    <? echo load_freeze_divs ("../",$permission);  ?>
    <form name="outsewingbill_1" id="outsewingbill_1"  autocomplete="off"  >
    <fieldset style="width:900px;">
    <legend>Sewing Bill Info </legend>
        <table cellpadding="0" cellspacing="2" width="100%">
            <tr>
                <td align="right" colspan="3"><strong>Bill No </strong></td>
                <td width="140" align="justify">
                    <input type="hidden" name="selected_id" id="selected_id" />
                    <input type="hidden" name="update_id" id="update_id" />
                    <input type="text" name="txt_bill_no" id="txt_bill_no" class="text_boxes" style="width:140px" placeholder="Double Click to Search" onDblClick="openmypage_bill();" readonly tabindex="1" >
                </td>
             </tr>
             <tr>
                <td width="120" class="must_entry_caption">Company Name</td>
                <td width="150">
                    <? 
                        echo create_drop_down( "cbo_company_id",150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/outside_sewing_bill_entry_controller', this.value, 'load_drop_down_location', 'location_td');load_drop_down( 'requires/outside_sewing_bill_entry_controller', this.value, 'load_drop_down_supplier_name', 'supplier_td');","","","","","",2);	
                    ?>
                </td>
                <td width="120">Location Name</td>                                              
                <td width="150" id="location_td">
                    <? 
                        echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "--Select Location--", $selected,"","","","","","",3);
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
                    <?
                        echo create_drop_down( "cbo_supplier_company", 150, $blank_array,"", 1, "-- Select Supplier --", $selected, "",0,"","","","",5);
                    ?> 
                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </fieldset>
    <br>
    <fieldset style="width:940px;">
    <legend>Sewing Bill Details </legend>
        <table  style="border:none; width:930px;" cellpadding="0" cellspacing="1" border="0" id="">
            <thead class="form_table_header">
                <th width="70">Receive Date </th>
                <th width="80" class="must_entry_caption">Challan No.</th>
                <th width="60">Order No.</th>
                <th width="80">Style</th>
                <th width="90">Buyer</th>
                <th width="130">Garments Item</th>
                <th width="60">WO Num</th>
                <th width="40">UOM</th>
                <th width="60">Garments Qty</th>
                <th width="40" class="must_entry_caption">Rate</th>
                <th width="60">Amount</th>
				<th width="60">Currency</th>
                <th>Remarks</th>
            </thead>
            <tbody id="bill_issue_table">
                <tr align="center">				
                    <td>
                        <input type="hidden" name="updateiddtls_1" id="updateiddtls_1" style="width:30px">
                        <input type="text" name="txt_deliverydate_1" id="txt_deliverydate_1"  class="datepicker" style="width:65px" readonly />									
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
						<? echo create_drop_down( "cbouom_1", 45, $unit_of_measurement,"", 0, "--Select UOM--",1,"","","1,2" );?>
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
                </tr>                
                <tr>
                    <td colspan="12" align="center" class="button_container">
						<? 
							echo load_submit_buttons($permission,"fnc_sewing_bill_entry",0,1,"reset_form('outsewingbill_1','sewing_info_list','','','$(\'#bill_issue_table tr:not(:first)\').remove();')",1);
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
        <div id="sewing_info_list"></div>                           
   </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
		
			
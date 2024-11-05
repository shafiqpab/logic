<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Out Side Knitting Bill Entry		
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	24-07-2013
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
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Kniting Bill Entry", "../", 1,1, $unicode,1,'');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';

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
		var newColor = 'yellow';
		if ( x.style ) {
		x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}

	function js_set_value(id)
	{
		//alert (id)
		var str=id.split("***");
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		if( jQuery.inArray(  str[0] , selected_id ) == -1) 
		{
			selected_id.push( str[0] );
		}
		else
		{
			for( var i = 0; i < selected_id.length; i++ ) {
			if( selected_id[i] == str[0]  ) break;
		}
			selected_id.splice( i, 1 );
		}
		var id = ''; var currency = '';
		for( var i = 0; i < selected_id.length; i++ ) 
		{
			id += selected_id[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		
		$('#selected_id').val( id );
	}

	function set_all()
	{
		selected_id = new Array();
		var old=document.getElementById('issue_id_all').value;
		//alert (old)
		if(old!="")
		{   
			old=old.split(",");
			for(var i=0; i<old.length; i++)
			{   
				//alert (old[i])
				//js_set_value(old[i]+"_"+document.getElementById('currid'+old[i]).value)  
				js_set_value(old[i]) 
			}
		}
	}

	function openmypage_bill()
	{ 
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_supplier_company').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/outside_knitting_bill_entry_controller.php?data='+data+'&action=bill_no_popup','Bill Popup', 'width=780px,height=400px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("outkintt_receive_id"); //Access form field with id="emailfield" 
			if (theemail.value!="")
			{
				freeze_window(5);
				get_php_form_data( theemail.value, "load_php_data_to_form_issue", "requires/outside_knitting_bill_entry_controller" );
				window_close( theemail.value );
				show_list_view(document.getElementById('cbo_company_id').value+'***'+document.getElementById('cbo_location_name').value+'***'+document.getElementById('cbo_bill_for').value+'***'+document.getElementById('cbo_supplier_company').value+'***'+document.getElementById('update_id').value+'***'+document.getElementById('issue_id_all').value,'knitting_entry_list_view','knitting_info_list','requires/outside_knitting_bill_entry_controller','set_all()','','');
		
				set_button_status(1, permission, 'fnc_knitting_bill_entry',1);
				setFilterGrid('tbl_list_search',-1);
				release_freezing();
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
		var data=document.getElementById('selected_id').value+"***"+issue_id+"***"+frm+"***"+document.getElementById('cbo_bill_for').value;
		//alert (data)
		var list_view_orders = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/outside_knitting_bill_entry_controller');
		if(list_view_orders!='')
		{
			$("#bill_issue_table tr").remove();
			$("#bill_issue_table").append(list_view_orders);
		}
		var tot_row=$('#bill_issue_table tr').length;
		math_operation( "txt_tot_qnty", "txt_qnty_", "+", tot_row );
		math_operation( "txt_tot_amount", "txt_amount_", "+", tot_row );
		//amount_caculation(tot_row)
		set_all_onclick();
	}

	function openmypage_wonum()
	{ 
		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_supplier_company').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/outside_knitting_bill_entry_controller.php?action=wo_num_popup&data='+data,'Wo Number Popup', 'width=950px,height=400px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("hidd_item_id") 
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				//alert (response[0]);
				var tot_row=$('#bill_issue_table tr').length;
				for(var k=1; k<=tot_row; k++)
				{
					document.getElementById('txtwonumid_'+k).value=response[0];
					document.getElementById('text_wo_num_'+k).value=response[1];
					document.getElementById('txt_rate_'+k).value=response[2];
				}
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
	}
	
	function fnc_knitting_bill_entry( operation )
	{
		if( form_validation('cbo_company_id*cbo_location_name*txt_bill_date*cbo_supplier_company*cbo_bill_for*txt_receive_date_1*txt_challenno_1','Company Name*Location*Bill Date*supplier company*bill for*receive date*challen no')==false)
		{ 
			return;
		}
		else
		{
			var tot_row=$('#bill_issue_table tr').length;
			var data1="action=save_update_delete&operation="+operation+"&tot_row="+tot_row+get_submitted_data_string('txt_bill_no*cbo_company_id*cbo_location_name*txt_bill_date*cbo_supplier_company*cbo_bill_for*txt_party_bill_no*update_id',"../");
			var data2='';
			for(var i=1; i<=tot_row; i++)
			{
				if (form_validation('cbouom_'+i+'*txt_rate_'+i,'Uom*Rate')==false)
				{
					return;
				}
				else if($('#txt_rate_'+i).val()==0)
				{
					alert ("Rate Not Blank or Zero.");
					$('#txt_rate_'+i).focus();
					return;
				}
				data2+=get_submitted_data_string('txt_receive_date_'+i+'*txt_challenno_'+i+'*ordernoid_'+i+'*itemid_'+i+'*bodyPartId_'+i+'*febDescId_'+i+'*txtwonumid_'+i+'*txt_numberroll_'+i+'*txt_qnty_'+i+'*cbouom_'+i+'*txt_rate_'+i+'*txt_amount_'+i+'*txt_remarks_'+i+'*txt_stylename_'+i+'*reciveid_'+i+'*cbouom_'+i+'*txt_partyname_'+i+'*updateiddtls_'+i+'*delete_id',"../",i);
			}
			var data=data1+data2;
			//alert (data); return;
			freeze_window(operation);
			http.open("POST","requires/outside_knitting_bill_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_knitting_bill_entry_reponse;
		}
	}

	function fnc_knitting_bill_entry_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert (http.responseText);
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
			document.getElementById('update_id').value = response[1];
			document.getElementById('txt_bill_no').value = response[2];
			window_close(response[1]);
			set_button_status(1, permission, 'fnc_knitting_bill_entry',1);
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
</script>
</head>
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
    <? echo load_freeze_divs ("../",$permission);  ?>
    <form name="outknittingbill_1" id="outknittingbill_1"  autocomplete="off"  >
    <fieldset style="width:900px;">
    <legend>Knitting Bill Info </legend>
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
                <td width="110" class="must_entry_caption">Company</td>
                <td width="150">
                    <?php 
                        echo create_drop_down( "cbo_company_id",145,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/outside_knitting_bill_entry_controller', this.value, 'load_drop_down_location', 'location_td');load_drop_down( 'requires/outside_knitting_bill_entry_controller', this.value, 'load_drop_down_supplier_name', 'supplier_td');","","","","","",2);	
                    ?>
                </td>
                <td width="110" class="must_entry_caption">Location</td>                                              
                <td width="150" id="location_td">
                    <? 
                        echo create_drop_down( "cbo_location_name", 145, $blank_array,"", 1, "--Select Location--", $selected,"","","","","","",3);
                    ?>
                </td>
                <td width="110" class="must_entry_caption">Bill Date</td>                                              
                <td width="140">
                    <input class="datepicker" type="text" style="width:130px" name="txt_bill_date" id="txt_bill_date" tabindex="4" />
                </td>
            </tr> 
            <tr>
            	<td class="must_entry_caption">Bill For</td>
                <td>
                    <?
                        echo create_drop_down( "cbo_bill_for", 145, $bill_for,"", 1, "-- Select --", $selected, "",0,"","","","",7);
                    ?> 
                </td>
                <td class="must_entry_caption">Supplier Name</td>
                <td id="supplier_td">
                    <?
                        echo create_drop_down( "cbo_supplier_company", 145, $blank_array,"", 1, "-- Select supplier --", $selected, "",0,"","","","",5);
                    ?> 
                </td>
                <td>Party Bill No</td>
                <td>
                    <input type="text" name="txt_party_bill_no" id="txt_party_bill_no" class="text_boxes" style="width:130px" />
                </td>
            </tr>
        </table>
    </fieldset>
    <br>
    <fieldset style="width:980px;">
    <legend>Knitting Bill Info </legend>
        <table  style="border:none; width:980px;" cellpadding="0" cellspacing="1" border="0" id="">
            <thead class="form_table_header">
                <th width="80" align="center" class="must_entry_caption">Receive Date </th>
                <th width="70" align="center" class="must_entry_caption">Sys. Challan</th>
                <th width="60" align="center">Order No.</th>
                <th width="80" align="center">Style</th>
                <th width="60" align="center">Buyer</th>
                <th width="40" align="center">N.O Roll</th>
                <th width="100" align="center">Fabric Descp</th>
                <th width="60" align="center">WO Num</th>
                <th width="60" align="center">Fabric Qty</th>
                <th width="50" align="center">UOM</th>
                <th width="40" align="center" class="must_entry_caption">Rate</th>
                <th width="60" align="center">Amount</th>
                <th width="80" align="center">Remarks</th>
            </thead>
            <tbody id="bill_issue_table">
                <tr align="center">				
                    <td>
                        <input type="hidden" name="reciveid_1" id="reciveid_1" style="width:70px">
                        <input type="date" name="txt_receive_date_1" id="txt_receive_date_1"  class="text_boxes" style="width:80px" readonly />									
                    </td>
                    <td>
                        <input type="text" name="txt_challenno_1" id="txt_challenno_1"  class="text_boxes" style="width:70px" readonly />							 
                    </td>
                    <td>
                        <input type="hidden" name="ordernoid_1" id="ordernoid_1" value="" style="width:60px">
                        <input type="text" name="txt_orderno_1" id="txt_orderno_1"  class="text_boxes" style="width:60px" readonly />										
                    </td>
                    <td>
                        <input type="text" name="txt_stylename_1" id="txt_stylename_1"  class="text_boxes" style="width:80px;" />
                    </td>
                    <td>
                        <input type="text" name="txt_partyname_1" id="txt_partyname_1"  class="text_boxes" style="width:60px" />								
                    </td>
                    <td>			
                        <input type="text" name="txt_numberroll_1" id="txt_numberroll_1" class="text_boxes" style="width:40px" readonly />							
                    </td>  
                    <td>
                        <input type="text" name="text_febricdesc_1" id="text_febricdesc_1"  class="text_boxes" style="width:100px" readonly/>
                    </td>
                    <td>
                        <input type="text" name="text_wo_num_1" id="text_wo_num_1"  class="text_boxes" style="width:60px" placeholder="Browse" onDblClick="openmypage_wonum();" readonly/>
                    </td>
                    <td>
                        <input type="text" name="txt_qnty_1" id="txt_qnty_1"  class="text_boxes_numeric" style="width:60px" readonly />
                    </td>
                    <td>
						<? echo create_drop_down( "cbouom_1", 55, $unit_of_measurement,"", 1, "--Select UOM--",12,"",1,"" );?>
                    </td>
                    <td>
                        <input type="text" name="txt_rate_1" id="txt_rate_1"  class="text_boxes_numeric" style="width:40px" readonly onBlur="amount_caculation(1);" />
                    </td>
                    <td>
                        <input type="text" name="txt_amount_1" id="txt_amount_1" class="text_boxes_numeric" style="width:60px"  readonly />
                    </td>
                    <td>
                        <input type="text" name="txt_remarks_1" id="txt_remarks_1"  class="text_boxes" style="width:80px" />
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                	<td width="80px">&nbsp;</td>
                    <td width="70px">&nbsp;</td>
                    <td width="60px">&nbsp;</td>
                    <td width="80px">&nbsp;</td>
                    <td width="60px">&nbsp;</td>
                    <td width="40px">&nbsp;</td>
                    <td width="100px">&nbsp;</td>
                    <td width="60px" align="right">Total Qty:</td>
                    <td width="60px">
                    	<input type="text" name="txt_tot_qnty" id="txt_tot_qnty"  class="text_boxes_numeric" style="width:60px" readonly disabled />
                    </td>
                    <td width="50px">&nbsp;</td>
                    <td width="40px" align="right">Total:</td>
                    <td width="60px">
                    	<input type="text" name="txt_tot_amount" id="txt_tot_amount"  class="text_boxes_numeric" style="width:60px" readonly  disabled/>
                    </td>
                    <td width="80px">&nbsp;</td>
                </tr>                
                <tr>
                    <td colspan="13" height="15" align="center"> </td>
                </tr>
                <tr>
                    <td colspan="13" align="center" class="button_container">
						<? 
							echo load_submit_buttons($permission,"fnc_knitting_bill_entry",0,0,"reset_form('outknittingbill_1','knitting_info_list','','','$(\'#bill_issue_table tr:not(:first)\').remove();')",1);
                        ?> 
                    </td>
                </tr>  
                <tr>
                    <td colspan="13" id="list_view" align="center"></td>
                </tr>
            </tfoot>                                                             
        </table>
        </fieldset>
        </form>
        <br>
        <div id="knitting_info_listss"></div>
        <div id="knitting_info_list"></div>                           
   </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
		
			
<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Out Side Embellishment Bill Entry						
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	23-03-2015	 
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
echo load_html_head_contents("Out Side Embellishment Bill Entry", "../", 1,1, $unicode,1,'');
?>

<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';

	function openmypage_bill()
	{ 
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_supplier_company').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/outside_embellishment_bill_entry_controller.php?data='+data+'&action=bill_no_popup','Bill Popup', 'width=700px,height=400px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("issue_id"); //Access form field with id="emailfield"
			if (theemail.value!="")
			{
				freeze_window(5);
				get_php_form_data( theemail.value, "load_php_data_to_form_issue", "requires/outside_embellishment_bill_entry_controller" );
				window_close( theemail.value );
				show_list_view(document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_supplier_company').value+'_'+document.getElementById('update_id').value,'embellishment_entry_list_view','embellishment_info_list','requires/outside_embellishment_bill_entry_controller','set_all();');
				setFilterGrid('tbl_list_search',-1);
				set_button_status(1, permission, 'fnc_embellishment_bill_entry',1);

				$('#tbl_list_search tbody tr').each(function(index, element) {
					
					if( $('#'+this.id).attr('bgcolor')=='yellow' )
					{
						var nid=this.id;//.replace( 'tr_', "");  
						var nid=nid.replace( 'tr_', "");  
						if( jQuery.inArray(  nid , selected_id_listed ) == -1) 
						{
							selected_id_listed.push( nid );
						}
						else
						{
							for( var i = 0; i < selected_id_listed.length; i++ ) {
								if( selected_id_listed[i] == nid  ) break;
							}
							selected_id_listed.splice( i, 1 );
						}
					}
				});
				set_all_onclick();
				release_freezing();
			}
		}
	}

	function set_all()
	{
		selected_id = new Array();
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
		var check_id='';
				
		var check_id=$('#checkid'+frm).val();
		 
		if ($('#update_id').val()!=frm)
			var issue_id=document.getElementById('issue_id_all').value;
		else
			var issue_id='';
		var data=document.getElementById('selected_id').value+"_"+issue_id+"_"+frm;
		var list_view_orders = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/outside_embellishment_bill_entry_controller');
		if(list_view_orders!='')
		{
			$("#bill_issue_table tr").remove();
			$("#bill_issue_table").append(list_view_orders);
		}
		var tot_row=$('#bill_issue_table tr').length;
		math_operation( "txt_tot_qnty", "txtQnty_", "+", tot_row );
		math_operation( "txt_tot_amount", "txtAmount_", "+", tot_row );
		set_all_onclick();
	}

	function fnc_check(inc_id)
	{
		if(inc_id=="all")
		{
			if(document.getElementById('checkall').checked==true)
			{
				document.getElementById('checkall').value=1;
			}
			else if(document.getElementById('checkall').checked==false)
			{
				document.getElementById('checkall').value=2;
			}
		}
		else if(inc_id=="rate")
		{
			if(document.getElementById('checkrate').checked==true)
			{
				document.getElementById('checkrate').value=1;
			}
			else if(document.getElementById('checkrate').checked==false)
			{
				document.getElementById('checkrate').value=2;
			}
		}else if(inc_id=="currency")
		{
			if(document.getElementById('checkCurrency').checked==true)
			{
				document.getElementById('checkCurrency').value=1;
			}
			else if(document.getElementById('checkCurrency').checked==false)
			{
				document.getElementById('checkCurrency').value=2;
			}
		}
		else
		{
			if(document.getElementById('checkid'+inc_id).checked==true)
			{
				document.getElementById('checkid'+inc_id).value=1;
			}
			else if(document.getElementById('checkid'+inc_id).checked==false)
			{
				document.getElementById('checkid'+inc_id).value=2;
			}
		}
	}

	function fnc_rate_copy( trid )
	{
		var is_ratecopy=document.getElementById('checkrate').value*1;
		var txtrate=$('#txtRate_'+trid).val()*1;
		if(is_ratecopy==1)
		{		
			var row_nums=$('#bill_issue_table tr').length;
			for(var j=trid; j<=row_nums; j++)
			{
				
				document.getElementById('txtRate_'+j).value=txtrate;
				amount_caculation(j);
			}
		}
	}
	function fnc_currency_copy( trid )
	{
		var is_copy=document.getElementById('checkCurrency').value*1;
		var checkCurrency=$('#curanci_'+trid).val()*1;
		
		if(is_copy==1)
		{		
			
			var row_nums=$('#bill_issue_table tr').length;
		 
			for(var j=trid; j<=row_nums; j++)
			{
				//alert(trid+'='+is_copy+'='+j);
				document.getElementById('curanci_'+j).value=checkCurrency;
				 
			}
		}
	}

	var selected_id = new Array(); var selected_currency_id = new Array();


		/* function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
			
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				var all_val="";
				all_val=$('#currid'+i).val();
				if($('#checkall').val()==1)
				{
					document.getElementById('checkid'+i).checked=true;
					document.getElementById('checkid'+i).value=1;
				}
				else
				{
					document.getElementById('checkid'+i).checked=false;
					document.getElementById('checkid'+i).value=2;
				}
				 
				js_set_value( all_val );
			}
		} */

		function check_all_data()
	{
		var tbl_row_count = document.getElementById( 'list_view_issue' ).rows.length;
		tbl_row_count = tbl_row_count - 1;
		
		for( var i = 1; i <= tbl_row_count; i++ ) {
		eval($('#tr_'+i).attr("onclick"));  
		}
	}

	function toggle( x, origColor ) {
		var newColor = 'yellow';
		//alert (x);
		if ( x.style ) {
		x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}

	/* function js_set_value(id)
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
	} */
	function checkall_data()
	{
		if(document.getElementById('checkall').checked==true)
		{
			document.getElementById('checkall').value=1;
		}
		else if(document.getElementById('checkall').checked==false)
		{
			document.getElementById('checkall').value=2;
		}


		var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
		tbl_row_count = tbl_row_count - 1;
		
		for( var i = 1; i <= tbl_row_count; i++ ) 
		{
			
			var issue_id=$('#pro_gmts_mst_id_'+i).val();

			if($("#tr_"+issue_id).css("display") != "none")
			{
				js_set_value(issue_id+'_'+i)
				if($('#checkall').val()==1)
				{
					document.getElementById('checkid'+i).checked=true;
					document.getElementById('checkid'+i).value=1;
				}
				else if($('#checkall').val()==2) 
				{
					document.getElementById('checkid'+i).checked=false;
					document.getElementById('checkid'+i).value=2;
				}
			}
		
			
		}
	}

	function js_set_value(id)
	{
		var str=id.split("_");
		// if( jQuery.inArray( str[1], selected_currency_id ) != -1  || selected_currency_id.length<1 )
		// {
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
			
			
			//alert(id);
			
			$('#selected_id').val( id );
			
		// }
		// else
		// {
		// 	$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
		// 	 { 
		// 		$(this).html('Currency Mix Not Allowed').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
		// 	 });
		// }
	}

	function fnc_embellishment_bill_entry( operation )
	{
		 if(operation==4)
		 {
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_bill_no').val()+'*'+report_title,"fabric_finishing_print", "requires/outside_embellishment_bill_entry_controller")
			show_msg("3");
		 }
		else if(operation==0 || operation==1 || operation==2)
		{
			if ( form_validation('cbo_company_id*txt_bill_date*cbo_supplier_company','Company Name*Bill Date*Supplier Name')==false )
			{
				return;
			}
			else
			{	
				var tot_row=$('#bill_issue_table tr').length;
				var data2='';
				for(var i=1; i<=tot_row; i++)
				{
					if (form_validation('txtChallenno_'+i+'*txtSysno_'+i+'*txtRate_'+i,'Challan*System No*Rate')==false)
					{
						return;
					}
					data2+=get_submitted_data_string('txtSysno_'+i+'*txtReceiveDate_'+i+'*txtChallenno_'+i+'*ordernoid_'+i+'*txtStylename_'+i+'*txtPartyname_'+i+'*itemid_'+i+'*embelid_'+i+'*embelTypeid_'+i+'*textWoNum_'+i+'*txtQnty_'+i+'*txtRate_'+i+'*txtAmount_'+i+'*curanci_'+i+'*hiddRemarks_'+i+'*updateiddtls_'+i+'*delete_id',"../");
				}
				var data1="action=save_update_delete&operation="+operation+"&tot_row="+tot_row+get_submitted_data_string('txt_bill_no*cbo_company_id*cbo_location_name*txt_bill_date*cbo_supplier_company*txt_remarks*update_id',"../");
				var data=data1+data2;
				
				freeze_window(operation);
				http.open("POST","requires/outside_embellishment_bill_entry_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_embellishment_bill_entry_reponse;
			}
		}
	}

	function fnc_embellishment_bill_entry_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
			document.getElementById('update_id').value = response[1];
			document.getElementById('txt_bill_no').value = response[2];
			window_close(response[1]);
			set_button_status(1, permission, 'fnc_embellishment_bill_entry',1);
			release_freezing();
		}
	}
	
	function amount_caculation(id)
	{
		var tot_row=$('#bill_issue_table tr').length;
		var tot_amount='';
		tot_amount=(document.getElementById('txtQnty_'+id).value*1)*(document.getElementById('txtRate_'+id).value*1);
		document.getElementById('txtAmount_'+id).value=tot_amount;
		math_operation( "txt_tot_amount", "txtAmount_", "+", tot_row,id );
	}
	
	function openmypage_remarks(id)
	{
		var data=document.getElementById('hiddRemarks_'+id).value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/outside_embellishment_bill_entry_controller.php?data='+data+'&action=remarks_popup','Remarks Popup', 'width=420px,height=320px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("text_new_remarks");//Access form field with id="emailfield"
			if (theemail.value!="")
			{
				$('#hiddRemarks_'+id).val(theemail.value);
			}
		}
	}

</script>
</head>
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
    <? echo load_freeze_divs ("../",$permission);  ?>
    <form name="outembellishmentbill_1" id="outembellishmentbill_1"  autocomplete="off"  >
    <fieldset style="width:900px;">
    <legend>Embellishmen Bill Info </legend>
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
                        echo create_drop_down( "cbo_company_id",150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/outside_embellishment_bill_entry_controller', this.value, 'load_drop_down_location', 'location_td');load_drop_down( 'requires/outside_embellishment_bill_entry_controller', this.value, 'load_drop_down_supplier_name', 'supplier_td');","","","","","",2);	
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
				<td align="left" width="120">Remarks</td>
            	<td align="left" width="150">
                <input class="text_boxes" type="text" style="width:97%;"  name="txt_remarks" id="txt_remarks"/>
                </td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </fieldset>
    <br>
    <fieldset style="width:940px;">
    <legend>Embellishmen Bill Details</legend>
        <table style="border:none; width:930px;" cellpadding="0" cellspacing="1" border="0" id="">
            <thead class="form_table_header">
                <th width="70">Receive Date </th>
                <th width="70" class="must_entry_caption">Challan No.</th>
                <th width="60" class="must_entry_caption">Sys. No.</th>
                <th width="70">Order No.</th>
                <th width="70">Style</th>
                <th width="60">Buyer</th>
                <th width="100">Garments Item</th>
                <th width="120">Embl. Name & Type</th>
                <th width="60">WO Num</th>
                <th width="60">Gmts Qty</th>
                <th width="40" class="must_entry_caption">Rate<input type="checkbox" name="checkrate" id="checkrate" onClick="fnc_check('rate'); " value="2" ></th>
                <th width="65">Amount</th>
				<th width="60">Currency<input type="checkbox" name="checkCurrency" id="checkCurrency" onClick="fnc_check('currency'); " value="2" ></th>
                <th>RMK</th>
            </thead>
            <tbody id="bill_issue_table">
                <tr align="center">				
                    <td>
                        <input type="hidden" name="updateiddtls_1" id="updateiddtls_1" style="width:30px">
                        <input type="date" name="txtReceiveDate_1" id="txtReceiveDate_1"  class="text_boxes" style="width:65px" readonly />									
                    </td>
                    <td>
                        <input type="text" name="txtChallenno_1" id="txtChallenno_1"  class="text_boxes" style="width:75px" readonly />							 
                    </td>
                    <td>
                        <input type="text" name="txtSysno_1" id="txtSysno_1"  class="text_boxes" style="width:55px" readonly />							 
                    </td>
                    <td>
                        <input type="hidden" name="ordernoid_1" id="ordernoid_1" value="" style="width:40px">
                        <input type="text" name="txtOrderno_1" id="txtOrderno_1"  class="text_boxes" style="width:65px" readonly />										
                    </td>
                    <td>
                        <input type="text" name="txtStylename_1" id="txtStylename_1"  class="text_boxes" style="width:65px;" />
                    </td>
                    <td>
                        <input type="text" name="txtPartyname_1" id="txtPartyname_1"  class="text_boxes" style="width:55px" />								
                    </td>
                    <td>
                    	<input type="hidden" name="itemid_1" id="itemid_1" value="" style="width:40px">
                        <input type="text" name="txtGmtsItem_1" id="txtGmtsItem_1"  class="text_boxes" style="width:95px" readonly/>
                    </td>
                    <td>
                    	<input type="hidden" name="embelid_1" id="embelid_1" value="" style="width:40px">
                        <input type="hidden" name="embelTypeid_1" id="embelTypeid_1" value="" style="width:40px">
                        <input type="text" name="textEmbelNameType_1" id="textEmbelNameType_1"  class="text_boxes" style="width:115px" readonly/>
                    </td>
                    <td>
                        <input type="text" name="textWoNum_1" id="textWoNum_1"  class="text_boxes" style="width:55px" disabled/> 
                    </td>
                    <td>
                        <input type="text" name="txtQnty_1" id="txtQnty_1"  class="text_boxes_numeric" style="width:55px"   />
                    </td>
                    <td>
                        <input type="text" name="txtRate_1" id="txtRate_1"  class="text_boxes_numeric" style="width:35px" onBlur="amount_caculation(1)"/>
                    </td>
                    <td>
                        <input type="text" name="txtAmount_1" id="txtAmount_1" class="text_boxes_numeric" style="width:60px" readonly />
                    </td>
					<td>
                    	<? echo create_drop_down( "curanci_1", 60, $currency,"", 1, "-Currency-",1,"","","" ); ?>
                    </td>
                    <td>
                    	<input type="button" name="txtRemarks_1" id="txtRemarks_1"  class="formbuttonplasminus" style="width:30px" value="R" onClick="openmypage_remarks(1);" />
                        <input type="hidden" name="hiddRemarks_1" id="hiddRemarks_1"  class="text_boxes" style="width:25px" />
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                	<td width="70px">&nbsp;</td>
                    <td width="70px">&nbsp;</td>
                    <td width="60px">&nbsp;</td>
                    <td width="70px">&nbsp;</td>
                    <td width="70px">&nbsp;</td>
                    <td width="60px">&nbsp;</td>
                    <td width="100px">&nbsp;</td>
                    <td width="180px" align="right" colspan="2">Total Qty:</td>
                    <td width="60px">
                    	<input type="text" name="txt_tot_qnty" id="txt_tot_qnty"  class="text_boxes_numeric" style="width:55px" disabled />
                    </td>
                    <td width="40px">&nbsp;</td>
                    <td width="65px">
                    	<input type="text" name="txt_tot_amount" id="txt_tot_amount"  class="text_boxes_numeric" style="width:60px" disabled/>
                    </td>
                    <td>&nbsp;</td>
                </tr>                
                <tr>
                    <td colspan="13" align="center" class="button_container">
						<? 
							echo load_submit_buttons($permission,"fnc_embellishment_bill_entry",0,1,"reset_form('outembellishmentbill_1','embellishment_info_list','','','$(\'#bill_issue_table tr:not(:first)\').remove();')",1);
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
        <div id="embellishment_info_list"></div>                           
   </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
		
			
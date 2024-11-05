<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Sub-contract Knitting bill issue
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	27-05-2014	
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
echo load_html_head_contents("Knitting bill issue", "../", 1,1, $unicode,1,'');

?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	var str_atention = [<? echo substr(return_library_autocomplete( "select attention from subcon_inbound_bill_mst group by attention", "attention" ), 0, -1); ?>];

	$(document).ready(function(e)
	 {
            $("#txt_attention").autocomplete({
			 source: str_atention
		  });
     });

	function openmypage_bill()
	{ 
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('cbo_party_source').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/knitting_bill_issue_controller.php?data='+data+'&action=bill_no_popup','Bill Popup', 'width=930px,height=400px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("issue_id") //Access form field with id="emailfield"
			if (theemail.value!="")
			{
				//freeze_window(5);
				get_php_form_data( theemail.value, "load_php_data_to_form_issue", "requires/knitting_bill_issue_controller" );
				
				window_close(theemail.value);
				//alert (document.getElementById('issue_id_all').value);
				show_list_view(document.getElementById('cbo_company_id').value+'***'+document.getElementById('cbo_location_name').value+'***'+document.getElementById('cbo_party_source').value+'***'+document.getElementById('cbo_party_name').value+'***'+document.getElementById('cbo_bill_for').value+'***'+document.getElementById('txt_bill_form_date').value+'***'+document.getElementById('txt_bill_to_date').value+'***'+document.getElementById('update_id').value+'***'+document.getElementById('issue_id_all').value,'knitting_delivery_list_view','knitting_info_list','requires/knitting_bill_issue_controller','set_all()','','');
				
				//fnc_disable_mst_field($('#cbo_party_name').val());
				//show_list_view( data, action, div, path, extra_func, is_append ) 
				accounting_integration_check($('#hidden_acc_integ').val());
	 			setFilterGrid('tbl_list_search',-1);
				set_button_status(1, permission, 'fnc_knitting_bill_issue',1);
				set_all_onclick();
				release_freezing();

			}
		}
	}

	/*function set_all()
	{
		var old=document.getElementById('issue_id_all').value;
		//alert (old);
		if(old!="")
		{   
			old=old.split(",");
			for(var i=0; i<old.length; i++)
			{   
				var cur=document.getElementById('currid'+old[i]).value;
				js_set_value( old[i]+'***'+document.getElementById('currid'+old[i]).value );
			}
		}
	}*/
	
	function set_all()
	{	
		selected_id = new Array();
		var old=document.getElementById('issue_id_all').value;
		var party_source=document.getElementById('cbo_party_source').value;
		//alert (old);
		if (party_source==2)
		{
			if(old!="")
			{   
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{   
					var cur=document.getElementById('currid'+old[i]).value;
					js_set_value( old[i]+'***'+document.getElementById('currid'+old[i]).value );
				}
			}
		}
		else if (party_source==1)
		{
			if(old!="")
			{   
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{   
					//var cur=document.getElementById('currid'+old[i]).value;
					js_set_value(old[i]+'***'+1);
				}
			}
		}
	}

	function window_close( frm )
	{
		if ( !frm ) var frm='';
		// alert (frm)
		if ($('#update_id').val()!=frm)
			var issue_id=document.getElementById('issue_id_all').value;
		else
			var issue_id='';
			
		var data=document.getElementById('selected_order_id').value+"***"+issue_id+"***"+frm+"***"+document.getElementById('cbo_party_source').value+"***"+document.getElementById('cbo_bill_for').value+"***"+document.getElementById('cbo_company_id').value;
		
		var list_view_orders = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/knitting_bill_issue_controller');
		if(list_view_orders!='')
		{
			$("#bill_issue_table tr").remove();
			$("#bill_issue_table").append(list_view_orders);
		}
		
		var tot_row=$('#bill_issue_table tr').length;
		math_operation( "total_qnty", "deliveryqnty_", "+", tot_row );
		math_operation( "total_qntyPcs", "deliveryqntypcs_", "+", tot_row );
		math_operation( "total_amount", "amount_", "+", tot_row );
		accounting_integration_check($('#hidden_acc_integ').val());
		set_all_onclick();
						$('#list_view_body').hide();
	}
	
	function check_all_data()
	{
		var tbl_row_count = document.getElementById( 'list_view_issue' ).rows.length;
		//alert (tbl_row_count)
		tbl_row_count = tbl_row_count - 1;
		
		for( var i = 1; i <= tbl_row_count; i++ ) {
			eval($('#tr_'+i).attr("onclick"));  
		}
	}
	
	function toggle( x, origColor ) {
		var newColor = 'yellow';
		if ( x.style ) {
		x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}
	var selected_id = new Array(); var selected_currency_id = new Array();
	
	function js_set_value(id)
	{
		//var source=$('#cbo_party_source').val();
		//alert (id)
		var str=id.split("***");
		
		if( jQuery.inArray( str[1], selected_currency_id ) != -1  || selected_currency_id.length<1 )
		{
			//alert (selected_id);
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			if( jQuery.inArray(  str[0] , selected_id ) == -1) {
				//alert (id);
				selected_id.push( str[0] );
				selected_currency_id.push( str[1] );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str[0]  ) break;
			}
				selected_id.splice( i, 1 );
				selected_currency_id.splice( i, 1 );
			}
			var id = ''; var currency = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				currency += selected_currency_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			currency = currency.substr( 0, currency.length - 1 );
			//alert (id);
			$('#selected_order_id').val( id );
			$('#selected_currency_no').val( currency );
		}
		else
		{
			$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
			{ 
				$(this).html('Currency Mix Not Allowed').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
			});
		}
	}
	
	function fnc_knitting_bill_issue( operation )
	{
		if(operation==4)
		{
			var source=$('#cbo_party_source').val();
			var show_val_column='';
			if(source==1)
			{
				var r=confirm("Press \"OK\" to open with Order Comments\nPress \"Cancel\" to open without Order Comments");
				if (r==true)
				{
					show_val_column="1";
				}
				else
				{
					show_val_column="0";
				}
			}
			else show_val_column="0";
			//alert (show_val_column);
			var report_title=$( "div.form_caption" ).html();
			//print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_bill_no').val()+'*'+report_title, "knitting_bill_print", "requires/knitting_bill_issue_controller") 
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_bill_no').val()+'*'+report_title+'*'+show_val_column,'knitting_bill_print','requires/knitting_bill_issue_controller');

			//return;
			show_msg("3");
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if( form_validation('cbo_company_id*cbo_location_name*txt_bill_date*cbo_party_name*cbo_party_source','Company Name*Location*Bill Date*Party Name*Party Source')==false)
			{
				return;
			}
			else
			{
				var source=$('#cbo_party_source').val();
				var tot_row=$('#bill_issue_table tr').length;
				var data1="action=save_update_delete&operation="+operation+"&tot_row="+tot_row+get_submitted_data_string('txt_bill_no*cbo_company_id*cbo_location_name*txt_bill_date*cbo_party_name*cbo_party_source*txt_attention*cbo_bill_for*update_id',"../");
				var data2='';
				for(var i=1; i<=tot_row; i++)
				{
					if(source==1)
					{
						if($('#bodypartid_'+i).val()==2 || $('#bodypartid_'+i).val()==3)
						{
							if($('#deliveryqntypcs_'+i).val()==0 || $('#deliveryqntypcs_'+i).val()=='')
							{
								alert ("Delivery Qty (Pcs) Not Blank or Zero.");
								$('#deliveryqntypcs_'+i).focus();
								return;
							}
						}
					}
					
					if (form_validation('cbouom_'+i+'*txtrate_'+i,'Uom*Rate')==false)
					{
						return;
					}
					else if($('#txtrate_'+i).val()==0)
					{
						alert ("Rate Not Blank or Zero.");
						$('#txtrate_'+i).focus();
						return;
					}
					else
					{
						if(source==2)
						{
						data2+=get_submitted_data_string('deleverydate_'+i+'*challenno_'+i+'*ordernoid_'+i+'*stylename_'+i+'*buyername_'+i+'*itemid_'+i+'*compoid_'+i+'*bodypartid_'+i+'*cbouom_'+i+'*numberroll_'+i+'*deliveryqnty_'+i+'*deliveryqntypcs_'+i+'*libRateId_'+i+'*txtrate_'+i+'*amount_'+i+'*remarksvalue_'+i+'*deliveryid_'+i+'*curanci_'+i+'*updateiddtls_'+i+'*delete_id',"../");//
						}
						else
						{
						data2+=get_submitted_data_string('deleverydate_'+i+'*challenno_'+i+'*ordernoid_'+i+'*stylename_'+i+'*buyername_'+i+'*itemid_'+i+'*compoid_'+i+'*bodypartid_'+i+'*cbouom_'+i+'*numberroll_'+i+'*deliveryqnty_'+i+'*deliveryqntypcs_'+i+'*libRateId_'+i+'*txtrate_'+i+'*amount_'+i+'*remarksvalue_'+i+'*deliveryid_'+i+'*curanci_'+i+'*updateiddtls_'+i,"../");//
						}
					}
				}
				//alert (data2);return;
				var data=data1+data2;
				freeze_window(operation);
				http.open("POST","requires/knitting_bill_issue_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_knitting_save_update_delete_response;
			}
		}
	}
	
	function generate_report_file(data,action,page)
	{
		window.open("requires/knitting_bill_issue_controller.php?data=" + data+'&action='+action, true );
	}

	function fnc_knitting_save_update_delete_response()
	{
		if(http.readyState == 4) 
		{
			//alert (http.responseText);return;
			var response=trim(http.responseText).split('**');
			//if (response[0].length>2) reponse[0]=10;
			show_msg(response[0]);
			if(response[0]*1==14*1)
			{
				release_freezing();
				alert(response[1]);
				return;
			}
			else if(response[0]==0 || response[0]==1)
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_bill_no').value = response[2];
				document.getElementById('hidden_acc_integ').value = response[3];
				window_close(response[1]);
				accounting_integration_check(response[3]);
				set_button_status(1, permission, 'fnc_knitting_bill_issue',1);
			}
			release_freezing();
		}
	}
	
	function open_terms_condition_popup(page_link,title)
	{
		var txt_bill_no=document.getElementById('txt_bill_no').value;
		if (txt_bill_no=="")
		{
			alert("Save The Knitting Bill First");
			return;
		}	
		else
		{
			page_link=page_link+get_submitted_data_string('txt_bill_no','../');
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=370px,center=1,resize=1,scrolling=0','')
			emailwindow.onclose=function(){};
		}
	}
	
	function openmypage_remarks(id)
	{
		var data=document.getElementById('remarksvalue_'+id).value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/knitting_bill_issue_controller.php?data='+data+'&action=remarks_popup','Remarks Popup', 'width=420px,height=320px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("text_new_remarks");//Access form field with id="emailfield"
			if (theemail.value!="")
			{
				$('#remarksvalue_'+id).val(theemail.value);
			}
		}
	}
	
	function qnty_caluculation(id)
	{
		var body_part=$('#bodypartid_'+id).val();
		if(body_part==2 || body_part==3)
		{
			var delv_qty=$('#deliveryqntypcs_'+id).val();
		}
		else
		{
			var delv_qty=$('#deliveryqnty_'+id).val();
		}
		$("#amount_"+id).val((delv_qty*1)*($("#txtrate_"+id).val()*1));
		var tot_row=$('#bill_issue_table tr').length;
		math_operation( "total_qnty", "deliveryqnty_", "+", tot_row );
		math_operation( "total_qntyPcs", "deliveryqntypcs_", "+", tot_row );
		math_operation( "total_amount", "amount_", "+", tot_row );
	}
	
	function accounting_integration_check(val)
	{
		var tot_row=$('#bill_issue_table tr').length;
		//alert (val);
		if(val==1)
		{
			$('#cbo_company_id').attr('disabled','disabled');
			$('#cbo_location_name').attr('disabled','disabled');
			$('#txt_bill_date').attr('disabled','disabled');
			$('#cbo_party_source').attr('disabled','disabled');
			$('#cbo_party_name').attr('disabled','disabled');
			$('#cbo_bill_for').attr('disabled','disabled');
			for(var i=1; i<=tot_row; i++)
			{
				$('#numberroll_'+i).attr('disabled','disabled');
				$('#cbouom_'+i).attr('disabled','disabled');
				$('#deliveryqnty_'+i).attr('disabled','disabled');
				$('#txtrate_'+i).attr('disabled','disabled');
			}
		}
		else
		{
			$('#cbo_company_id').removeAttr('disabled','disabled');
			$('#cbo_location_name').removeAttr('disabled','disabled');
			$('#txt_bill_date').removeAttr('disabled','disabled');
			$('#cbo_party_source').removeAttr('disabled','disabled');
			$('#cbo_party_name').removeAttr('disabled','disabled');
			$('#cbo_bill_for').removeAttr('disabled','disabled');
			for(var i=1; i<=tot_row; i++)
			{
				$('#numberroll_'+i).removeAttr('disabled','disabled');
				$('#cbouom_'+i).removeAttr('disabled','disabled');
				$('#deliveryqnty_'+i).removeAttr('disabled','disabled');
				$('#txtrate_'+i).removeAttr('disabled','disabled');
			}
		}
	}
	
	function fnc_bill_for(val)
	{
		if(val==1)
		{
			$('#cbo_bill_for').removeAttr('disabled','disabled');
		}
		else
		{
			$('#cbo_bill_for').attr('disabled','disabled');
		}
	}
	
	function fnc_disable_mst_field(val)
	{
		//alert(val)
		if(val!=0)
		{
			$('#cbo_company_id').attr('disabled','disabled');
			$('#cbo_location_name').attr('disabled','disabled');
			$('#cbo_party_source').attr('disabled','disabled');
			$('#cbo_bill_for').attr('disabled','disabled');	
		}
		else
		{
			$('#cbo_company_id').removeAttr('disabled','disabled');
			$('#cbo_location_name').removeAttr('disabled','disabled');
			$('#cbo_party_source').removeAttr('disabled','disabled');
			$('#cbo_bill_for').removeAttr('disabled','disabled');	
		}
	}
	
	function openmypage_rate(row_no)
	{
		var data=document.getElementById('cbo_company_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/knitting_bill_issue_controller.php?data='+data+'&action=kniting_rate_popup','Kniting Rate Popup', 'width=780px,height=400px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("hddn_all_data");//Access form field with id="emailfield"
			if (theemail.value!="")
			{
				var pop_data=trim(theemail.value).split('***');
				$('#libRateId_'+row_no).val(pop_data[0]);
				$('#txtrate_'+row_no).val(pop_data[1]);
				qnty_caluculation(row_no);
			}
		}
	}
	
	function fnc_without_collar_cuff(type)
	{
		if(type==2)
		{
			if ($("#txt_bill_no").val()=="")
			{
				alert ("Please Select Bill Number.");
				return;
			}
			else
			{
				var report_title=$( "div.form_caption" ).html();
				generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_bill_no').val()+'*'+report_title,'knitting_bill_without_collar_cuff_print','requires/knitting_bill_issue_controller');

			}
		}
		else 
		{
			alert("This button only for In-bound Subcontract Bill");
			return;
		}
	}
	
	
</script>
</head>
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
    <? echo load_freeze_divs ("../",$permission);  ?>
    <form id="knitigbillissue_1" name="knitigbillissue_1" autocomplete="off">
    <fieldset style="width:850px;">
    <legend>Knitting Bill Info </legend>
        <table cellpadding="0" cellspacing="2" width="850">
            <tr>
                <td align="right" colspan="3"><strong>Bill No </strong></td>
                <td colspan="3">
                    <input type="hidden" name="hidden_acc_integ" id="hidden_acc_integ" />
                    <input type="hidden" name="selected_order_id" id="selected_order_id" />
                    <input type="hidden" name="selected_currency_no" id="selected_currency_no" />
                    <input type="hidden" name="sel_order_pro_id" id="sel_order_pro_id" />
                    <input type="hidden" name="update_id" id="update_id" /><br>
                    <input type="text" name="txt_bill_no" id="txt_bill_no" class="text_boxes" style="width:140px" placeholder="Double Click to Search" onDblClick="openmypage_bill();" readonly tabindex="1" >
                </td>
            </tr>
            <tr>
                <td width="110" class="must_entry_caption">Company Name</td>
                <td width="150">
                    <?php 
                        echo create_drop_down( "cbo_company_id",150,"select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/knitting_bill_issue_controller', this.value, 'load_drop_down_location', 'location_td');","","","","","",2);	
                    ?>
                </td>
                <td width="110" class="must_entry_caption">Location Name</td>                                              
                <td width="150" id="location_td">
                    <? 
                        echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "--Select Location--", $selected,"","","","","","",3);
                    ?>
                </td>
                <td width="110" class="must_entry_caption">Bill Date</td>                                              
                <td width="150">
                    <input class="datepicker" type="text" style="width:140px" name="txt_bill_date" id="txt_bill_date" tabindex="4" value="<? echo date('d-m-Y'); ?>" />
                </td>
            </tr> 
            <tr>
                <td class="must_entry_caption">Party Source</td>
                <td>
                    <?
                        echo create_drop_down( "cbo_party_source", 150, $knitting_source,"", 1, "-- Select Party --", $selected, "load_drop_down( 'requires/knitting_bill_issue_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_party_name', 'party_td' ); fnc_bill_for(this.value);",0,"1,2","","","",5);     //setFilterGrid('list_view',-1)
                    ?> 
                </td>
                <td>Bill For</td>
                <td>
                    <?
                        echo create_drop_down( "cbo_bill_for", 150, $bill_for,"", 0, "--Select Bill--", 1, "",1,"","","","",8);
                    ?> 
                </td>
                <td class="must_entry_caption">Party Name</td>
                <td id="party_td">
                    <?
                        echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "--Select Party--", $selected, "",0,"","","","",6);
                    ?> 
                </td>
            </tr>
            <tr>
                <td>Attention</td>
                <td><input class="text_boxes" type="text" style="width:140px" name="txt_attention" id="txt_attention" tabindex="7" /></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
                              <tr>
               <td width="110" class="must_entry_caption">Form Date</td>                                              
                <td width="140">
                    <input class="datepicker" type="text" style="width:130px" name="txt_bill_form_date" id="txt_bill_form_date" tabindex="4" />
                </td>
                   <td width="110" class="must_entry_caption">To Date</td>                                              
                <td width="140">
                    <input class="datepicker" type="text" style="width:130px" name="txt_bill_to_date" id="txt_bill_to_date" tabindex="4" />
                </td>
                   <td width="110" class="must_entry_caption"></td>                                              
                <td width="140">
                    <input class="formbutton" type="button" onClick="show_list_view(document.getElementById('cbo_company_id').value+'***'+document.getElementById('cbo_location_name').value+'***'+document.getElementById('cbo_party_source').value+'***'+document.getElementById('cbo_party_name').value+'***'+document.getElementById('cbo_bill_for').value+'***'+document.getElementById('txt_bill_form_date').value+'***'+document.getElementById('txt_bill_to_date').value,'knitting_delivery_list_view','knitting_info_list','requires/knitting_bill_issue_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:130px" name="txt_bill_date" value="Populate" id="txt_bill_date" tabindex="4" />
                </td>
            </tr>
        </table>
        </fieldset>
        <br>
        <fieldset style="width:1050px;">
        <legend>Knitting Bill Info</legend>
        <table align="right" cellspacing="0" cellpadding="0" width="970"  border="1" rules="all" class="rpt_table" >
            <thead class="form_table_header">
                <th width="60" class="must_entry_caption">Delivery Date </th>
                <th width="40" class="must_entry_caption">Sys. Challan</th>
                <th width="70">Order No.</th>
                <th width="80">Cust.Style</th>
                <th width="70">Cust.Buyer</th>
                <th width="40">Roll</th>
                <th width="80">Body Part</th>                                      
                <th width="140">Fabric Des.</th>
                <th width="50" class="must_entry_caption">UOM</th>
                <th width="70">Collar Cuff Measurement</th>
                <th width="40">Delv. Qty (Wgt.)</th>
                <th width="40">Delv. Qty (Pcs.)</th>
                <th width="40" class="must_entry_caption">Rate</th>
                <th width="40">Amount</th>
                <th>RMK</th>
            </thead>
            <tbody id="bill_issue_table">
                <tr align="center">				
                    <td>
                        <input type="hidden" name="updateiddtls_1" id="updateiddtls_1">
                        <input type="text" name="deleverydate_1" id="deleverydate_1"  class="datepicker" style="width:60px" readonly />									
                    </td>
                    <td>
                        <input type="text" name="challenno_1" id="challenno_1"  class="text_boxes" style="width:40px" readonly />
                    </td>
                    <td>
                        <input type="hidden" name="ordernoid_1" id="ordernoid_1" value="">
                        <input type="text" name="orderno_1" id="orderno_1"  class="text_boxes" style="width:70px" readonly />
                    </td>
                    <td>
                        <input type="text" name="stylename_1" id="stylename_1"  class="text_boxes" style="width:80px;" />
                    </td>
                    <td>
                        <input type="text" name="buyername_1" id="buyername_1"  class="text_boxes" style="width:70px" />
                    </td>
                    <td>			
                        <input name="numberroll_1" id="numberroll_1" type="text" class="text_boxes" style="width:40px" readonly />
                    </td>  
                    <td style="display:none">
                        <input type="text" name="yarndesc_1" id="yarndesc_1"  class="text_boxes" style="width:115px" readonly/>
                    </td>
                    <td>
                        <input type="text" name="bodypart_1" id="bodypart_1"  class="text_boxes" style="width:80px" readonly/>
                    </td>
                    <td>
                        <input type="text" name="febricdesc_1" id="febricdesc_1"  class="text_boxes_numeric" style="width:135px" readonly/>
                    </td>
                    <td>
						<? echo create_drop_down( "cbouom_1", 50, $unit_of_measurement,"", 1, "-UOM-",0,"",0,"1,2,12" );?>
                    </td>
                    <td>
						<input type="text" name="collarcuff_1" id="collarcuff_1"  class="text_boxes" style="width:65px" readonly/>
                    </td>
                    <td>
                        <input type="text" name="deliveryqnty_1" id="deliveryqnty_1"  class="text_boxes_numeric" style="width:40px" />
                    </td>
                    <td>
                        <input type="text" name="deliveryqntypcs_1" id="deliveryqntypcs_1" class="text_boxes_numeric" style="width:40px" />
                    </td>
                    <td>
                        <input type="text" name="txtrate_1" id="txtrate_1"  class="text_boxes_numeric" style="width:40px" />
                        <input type="hidden" name="libRateId_1" id="libRateId_1" value="">
                    </td>
                    <td>
                        <input type="text" name="amount_1" id="amount_1" style="width:40px"  class="text_boxes"  readonly />
                    </td>
                    <td>
                        <input type="button" name="remarks_1" id="remarks_1"  class="formbuttonplasminus" value="R" onClick="openmypage_remarks(1);" />
                        <input type="hidden" name="remarksvalue_1" id="remarksvalue_1" class="text_boxes" />
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td width="60px">&nbsp;</td>								
                    <td width="40px">&nbsp;</td>
                    <td width="70px">&nbsp;</td>
                    <td width="80px">&nbsp;</td>
                    <td width="70px">&nbsp;</td>
                    <td width="40px">&nbsp;</td>
                    <td width="220px" colspan="2" align="center"><input type="button" id="set_button" class="image_uploader" style="width:140px; margin-left:5px; margin-top:2px;" value="Terms Condition" onClick="open_terms_condition_popup('requires/knitting_bill_issue_controller.php?action=terms_condition_popup','Terms Condition')" /></td>
                    <td width="50px">&nbsp;</td>
                    <td width="70px">Total Qty</td>
                    <td width="40px">
                        <input type="text" name="total_qnty" id="total_qnty"  class="text_boxes_numeric" style="width:40px" value="" readonly disabled />
                    </td>
                    <td width="40px">
                        <input type="text" name="total_qntyPcs" id="total_qntyPcs"  class="text_boxes_numeric" style="width:40px" value="" readonly disabled />
                    </td>
                    <td width="40px">Total</td>
                    <td width="40px">
                        <input type="text" name="total_amount" id="total_amount"  class="text_boxes_numeric" style="width:40px" value="" readonly disabled />
                    </td>
                    <td width="">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="15" height="15" align="center"><div id="accounting_integration_div" style="float:center; font-size:18px; color:#FF0000;"></div></td>
                </tr>
                <tr>
                    <td colspan="15" align="center" class="button_container">
                    <? 
					$date=date('d-m-Y');
                    echo load_submit_buttons($permission,"fnc_knitting_bill_issue",0,1,"reset_form('knitigbillissue_1','knitting_info_list','','txt_bill_date,".$date."','$(\'#bill_issue_table tr:not(:first)\').remove();')",1); ?>&nbsp;
                            <input type="button" name="search" id="search" value="Without Collar Cuff" onClick="fnc_without_collar_cuff(document.getElementById('cbo_party_source').value)" style="width:130px" class="formbutton" />
                    
                    </td>
                </tr>  
                <tr>
                    <td colspan="15" id="list_view" align="center"></td>
                </tr>
            </tfoot>                                                            
        </table>
        </fieldset> 
        </form>
        <br>
        <div id="knitting_info_list"></div>                           
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>

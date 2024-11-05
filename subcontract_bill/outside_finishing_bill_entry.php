<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Out-Side Finishing Bill Entry						
Functionality	:	
JS Functions	:
Created by		:	Sohel 
Creation date 	: 	04-08-2013
Updated by 		: 		
Update date		: 	
Oracle Convert 	:	Kausar		
Convert date	: 	03-06-2014	   
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
echo load_html_head_contents("Out-Side Finishing Bill Entry", "../", 1,1, $unicode,1,'');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';

	var selected_id = new Array(); var selected_currency_id = new Array();
	var selected_id_listed = new Array();
	var selected_id_removed = new Array(); 
	function check_all_data()
	{
		var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
		tbl_row_count = tbl_row_count - 1;
		
		for( var i = 1; i <= tbl_row_count; i++ ) 
		{
			var all_val="";
			all_val=$('#currid'+i).val();
			//fnc_check('all');
			//fnc_check(i);
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
			if(selected_id[i] == str[0]  ) break;
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

	function fnc_outside_finishing_bill_entry( operation )
	{   
		var isFileMandatory = "";
		<?php 
			
			if(!empty($_SESSION['logic_erp']['mandatory_field'][623][1])) echo " isFileMandatory = ". $_SESSION['logic_erp']['mandatory_field'][623][1] . ";\n";
		?>
		// alert(isFileMandatory); return;
		if($("#multiple_file_field")[0].files.length==0 && isFileMandatory!="" && $('#update_id').val()==''){

			document.getElementById("multiple_file_field").focus();
			var bgcolor='-moz-linear-gradient(bottom, rgb(254,151,174) 0%, rgb(255,255,255) 10%, rgb(254,151,174) 96%)';
			document.getElementById("multiple_file_field").style.backgroundImage=bgcolor;
			alert("Please Add File in Master Part");
			return;	
		}

		if( form_validation('cbo_company_id*txt_bill_date*cbo_supplier_company*cbo_bill_for*txtreceivedate_1*txtchallenno_1','Company Name*Bill Date*supplier company*bill for*receive date*challen no')==false)
		{
			return;
		}
		
		/* if(operation==4){
			var report_title=$( "div.form_caption" ).html();
			var data=$('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#hidsyschallan').val();
			window.open("requires/outside_finishing_bill_entry_controller.php?data=" + data+'&action=fabric_finishing_print', true );
			return;
		} */
		if(operation== 0 || operation== 1 || operation == 2 ){
			var integration_check = $('#hidden_acc_integ').val()*1;
				
			if(integration_check==1){
				alert('Already Posted In Accounts. Save Update Delete Restricted.'); return;
			}
		}
		var mandatory_check = $('#mandatory_check').val()*1;
		var tot_row=$('#outside_finishingbill_table tr').length;
		var data1="action=save_update_delete&operation="+operation+"&tot_row="+tot_row+get_submitted_data_string('txt_bill_no*cbo_company_id*cbo_location_name*txt_bill_date*cbo_supplier_company*cbo_bill_for*txt_upcharge*txt_discount*txt_party_bill*update_id',"../");
		//alert (data1);
		var selected_currecny=new Array();
		for(var i=1; i<=tot_row; i++)
		{
			var currecny=$('#curanci_'+i).val();
		//	currency_chk_arr+=currecny+',';	
			
			var str =$('#curanci_' + i).val();
			
			for( var s = 0; s < selected_currecny.length; s++ ) {
				if( selected_currecny[s]!= str ){
					//release_freezing();
					alert("Duplicate Currency not allowed");
					return;
				}
			}
			selected_currecny.push(str);
			 
		}
		
		//alert(currency_chk_arr);
		//currency_chk_arr
		var data2='';
		for(var i=1; i<=tot_row; i++)
		{
			
			if(mandatory_check==1)
			{
				if (form_validation('textwonum_'+i,'WO NO')==false)
				{
					$('#textwonum_'+i).focus();
					return;
				}
			}
			
			if($('#cbouom_'+i).val()==0)
			{
				alert ("UOM Blank.");
				$('#cbouom_'+i).focus();
				return;
			}
			else if($('#txtfabqnty_'+i).val()==0)
			{
				alert ("Qty Not Blank or Zero.");
				$('#txtfabqnty_'+i).focus();
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
				data2+=get_submitted_data_string('txtreceivedate_'+i+'*txtchallenno_'+i+'*ordernoid_'+i+'*txtnumberroll_'+i+'*itemid_'+i+'*colorid_'+i+'*bodypartid_'+i+'*textwonumid_'+i+'*txtfabqnty_'+i+'*cbouom_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtremarks_'+i+'*reciveid_'+i+'*compoid_'+i+'*batchid_'+i+'*diatype_'+i+'*updateiddtls_'+i+'*curanci_'+i+'*subprocessId_'+i+'*serviceSource_'+i+'*txtstylename_'+i+'*txtpartyname_'+i+'*txtamountusd_'+i+'*txtexRate_'+i,"../");
			}//txtamountusd_1*txtexRate_1
		}
		var data=data1+data2;
		//alert (data); return;
		freeze_window(operation);
		http.open("POST","requires/outside_finishing_bill_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_outside_finishing_bill_entry_response;
	}

	function fnc_outside_finishing_bill_entry_response()
	{
		if(http.readyState == 4) 
		{
			//alert (http.responseText);//return;
			var response=trim(http.responseText).split('**');
			//if (response[0].length>2) reponse[0]=10;
			show_msg(response[0]);
			if(response[0]==0 || response[0]==1)
			{
				var check_system_id=$("#update_id").val();
				document.getElementById('update_id').value = response[1];
				if (check_system_id=="") uploadFile( $("#update_id").val());
				document.getElementById('txt_bill_no').value = response[2];
				window_close(response[1]);
				set_button_status(1, permission, 'fnc_outside_finishing_bill_entry',1);
			}
			release_freezing();
		}
	}

	function uploadFile(mst_id)
	{
		$(document).ready(function() { 
			 
			var suc=0;
			var fail=0;
			for( var i = 0 ; i < $("#multiple_file_field")[0].files.length ; i++)
			{
				var fd = new FormData();
				console.log($("#multiple_file_field")[0].files[i]);
				var files = $("#multiple_file_field")[0].files[i]; 
				fd.append('file', files); 
				$.ajax({
					url: 'requires/outside_finishing_bill_entry_controller.php?action=file_upload&mst_id='+ mst_id, 
					type: 'post', 
					data:fd, 
					contentType: false, 
					processData: false, 
					success: function(response){
						var res=response.split('**');
						if(res[0] == 0){ 
							
							suc++;
						}
						else if(fail==0)
						{
							alert('file not uploaded');
							fail++;
						}
					}, 
				}); 
			}

			if(suc > 0 )
			{
				 document.getElementById('multiple_file_field').value='';
			}
		}); 
	}

	function openmypage_wonum(i)
	{ 
		if ( form_validation('txtreceivedate_1*txtchallenno_1','Receive Date*Challen No')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_supplier_company').value+"_"+document.getElementById("ordernoid_"+i).value+"_"+document.getElementById("cbo_bill_for").value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/outside_finishing_bill_entry_controller.php?action=wonum_popup&data='+data,'Wo Popup', 'width=950px,height=400px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("hidd_item_id") 
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				//alert (response[0]);
				var tot_row=$('#outside_finishingbill_table tr').length;
				var txt_bill_no=document.getElementById('txt_bill_no').value;
				var update_id=document.getElementById('update_id').value;
				document.getElementById('textwonumid_'+i).value=response[0];
				document.getElementById('textwonum_'+i).value=response[1];
				document.getElementById('txtrate_'+i).value=response[2];
				document.getElementById('txthiddenrate_'+i).value=response[2];
				
				if(response.length>3 )
				{
					
					if(response[3] && txt_bill_no.length==0 && update_id.length==0)
					{
						document.getElementById('curanci_'+i).value=response[3];
					}
					
				}
				// for(var k=1; k<=tot_row; k++)
				// {
				// 	document.getElementById('textwonumid_'+k).value=response[0];
				// 	document.getElementById('textwonum_'+k).value=response[1];
				// 	document.getElementById('txtrate_'+k).value=response[2];
					
				// 	if(response.length>3 )
				// 	{
						
				// 		if(response[3] && txt_bill_no.length==0 && update_id.length==0)
				// 		{
				// 			document.getElementById('curanci_'+k).value=response[3];
				// 		}
						
				// 	}
				// }
				exchenge_rate_val(response[2])
				release_freezing();
			}
		}
	}

	function exchenge_rate_val(rate)
	{
		var tot_row=$('#outside_finishingbill_table tr').length;
		var amount_total=0;
		for(var k=1; k<=tot_row; k++)
		{
			amount_total=(document.getElementById('txtfabqnty_'+k).value*1)*(rate*1);
			document.getElementById('txtamount_'+k).value=amount_total;
		}
		ddd={dec_type:5,comma:0};
		math_operation("txt_tot_qnty", "txtfabqnty_", "+", tot_row,ddd);
		math_operation("txt_tot_amount", "txtamount_", "+", tot_row,ddd);
	}

	function openmypage_outside_bill()
	{ 
		if( form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
	
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_supplier_company').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/outside_finishing_bill_entry_controller.php?data='+data+'&action=outside_bill_popup','Outside Bill Popup', 'width=700px,height=400px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("outside_bill_id") 
			if (theemail.value!="")
			{
				freeze_window(5);
				get_php_form_data( theemail.value, "load_php_data_to_form_outside_bill", "requires/outside_finishing_bill_entry_controller" );
				//$("#outside_finishingbill_table tr").remove();
				selected_id_listed = new Array();
				
				window_close(theemail.value);
				
				fnc_list_search(theemail.value);
				
				set_button_status(1, permission, 'fnc_outside_finishing_bill_entry',1);
				//var selected_id_listed = new Array();
				//var selected_id_removed = new Array(); 
				
				
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
				//alert(selected_id_listed);
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
	
	var seq_arr=new Array(); var uom_arr = new Array();
	function window_close( uid )
	{
		var html="";
		var is_readonly=""; var isreadonly='';
		var grey_or_fin_qty=$('#variable_check').val()*1;
		if(grey_or_fin_qty==1) is_readonly=""; else is_readonly="readonly";
		
		if(uid==0)
		{
			var list_tot_row=$('#tbl_list_search tr').length-1;
			//alert(list_tot_row)
			var i=0; if(seq_arr!=0) i=seq_arr; else i=i;
			
			for (var k=1; k<=list_tot_row; k++)
			{
				var check_id=''; var strval=''; var trid="";
				var check_id=$('#checkid'+k).val();
				var strval=$('#strid'+k).val();
				var split_str=strval.split("_");
				// alert(split_str[23])
				//var trid=split_str[2]+"_"+split_str[3]+"_"+split_str[12]+"_"+split_str[9]+"_"+split_str[11]+"_"+split_str[19]+"_"+split_str[14];
				var trid=split_str[0];
				
				if( check_id!=1) 
				{  
					$("#trent_"+trid).remove();
					for( var v = 0; v < selected_id_listed.length; v++ ) {
						
						if( selected_id_listed[v] == trid  ) break;
					}
					selected_id_listed.splice( v, 1 );
					//alert(selected_id_listed);
				}
				
				if(check_id==1)
				{
					//alert(strval)
					if(selected_id_listed.length==0)
					{
						$("#outside_finishingbill_table tr").remove();
					}
					//alert(selected_id_listed);
					if( jQuery.inArray(  trid , selected_id_listed ) == -1) 
					{
						i++;
						selected_id_listed.push( trid );
						//alert(selected_id_listed)
						var rec_id=split_str[0];
						var recdate=split_str[1];
						var rec_challan=split_str[2];
						var po_id=split_str[3];
						var po_no=split_str[4];
						var style_ref=split_str[5];
						var buyer=split_str[6];
						var job=split_str[7];
						var roll_no=split_str[8];
						var body_part_id=split_str[9];
						var body_part_name=split_str[10];
						var fab_des_id=split_str[11];
						var prod_id=split_str[12];
						var prod_name=split_str[13];
						var batch_id=split_str[14];
						var color_id=split_str[15];
						var color_name=split_str[16];
						var sub_process_id=split_str[17];
						var process_name=split_str[18];
						var dia_width_id=split_str[19];
						var dia_width_name=split_str[20];
						
						var rec_qty=(split_str[21]*1);
						var challan_no=split_str[22];
						var service_process=split_str[23];
						var service_process_id=split_str[24];
						var service_source=split_str[25];
						var booking_rate=split_str[26];
						var sub_service_process=split_str[27];
					    var ex_rate=(split_str[27]*1);
						var amount_usd=rec_qty*ex_rate;
						//alert(ex_rate+'='+rec_qty+'='+amount_usd);
						
						var order_uom=0;
						//var uom=split_str[13];
						//var rate=split_str[15];
						//var amount=split_str[16];
						//var remarks=split_str[16];
						//alert(challan_no);
						
						var is_disable=""; var selected_uom="";
						if (body_part_id==2 || body_part_id==3 || body_part_id==40 || body_part_id==172 || body_part_id==203 || body_part_id==214)
						{
							if(order_uom==0)
							{
								selected_uom=1;
							}
							is_disable="";
						}
						else
						{
							if(order_uom== 0)
							{
								selected_uom=12;
							}
							
							is_disable="disabled";
						}
						
						html+='<tr align="center" id="trent_'+trid+'"><td><input type="hidden" name="updateiddtls_'+i+'" id="updateiddtls_'+i+'" value=""><input type="hidden" name="subprocessId_'+i+'" id="subprocessId_'+i+'" value="'+service_process_id+'"><input type="hidden" name="serviceSource_'+i+'" id="serviceSource_'+i+'" value="'+service_source+'"><input type="hidden" name="reciveid_'+i+'" id="reciveid_'+i+'" value="'+rec_id+'"><input type="text" name="txtreceivedate_'+i+'" id="txtreceivedate_'+i+'" class="datepicker" style="width:60px" value="'+recdate+'" disabled /></td><td><input type="text" name="txtchallenno_'+i+'" id="txtchallenno_'+i+'" class="text_boxes" style="width:70px" value="'+challan_no+'" readonly /></td><td><input type="text" name="txtProcess_'+i+'" id="txtProcess_'+i+'" value="'+process_name+'" class="text_boxes" style="width:90px" readonly /></td><td><input type="text" name="txtSubProcess_'+i+'" id="txtSubProcess_'+i+'" value="'+service_process+'" class="text_boxes" style="width:90px" readonly /></td><td><input type="hidden" name="ordernoid_'+i+'" id="ordernoid_'+i+'" value="'+po_id+'" style="width:40px" /><input type="text" name="txtorderno_'+i+'" id="txtorderno_'+i+'" class="text_boxes" style="width:60px" value="'+po_no+'" readonly /></td><td><input type="text" name="txtstylename_'+i+'" id="txtstylename_'+i+'" class="text_boxes" style="width:70px;" value="'+style_ref+'" readonly /></td><td><input type="text" name="txtpartyname_'+i+'" id="txtpartyname_'+i+'" class="text_boxes" style="width:60px" value="'+buyer+'" readonly /></td><td><input name="txtnumberroll_'+i+'" id="txtnumberroll_'+i+'" type="text" class="text_boxes" style="width:40px" value="'+roll_no+'" readonly /></td><td><input type="hidden" name="diatype_'+i+'" id="diatype_'+i+'" value="'+dia_width_id+'"><input type="hidden" name="compoid_'+i+'" id="compoid_'+i+'" value="'+fab_des_id+'"><input type="hidden" name="itemid_'+i+'" id="itemid_'+i+'" value="'+prod_id+'"><input type="hidden" name="batchid_'+i+'" id="batchid_'+i+'" value="'+batch_id+'"><input type="text" name="textfebricdesc_'+i+'" id="textfebricdesc_'+i+'" class="text_boxes" style="width:100px" value="'+prod_name+'" readonly/></td><td><input type="hidden" name="colorid_'+i+'" id="colorid_'+i+'" value="'+color_id+'"><input type="text" name="textcolor_'+i+'" id="textcolor_'+i+'" class="text_boxes" style="width:60px" value="'+color_name+'" readonly/></td><td><input type="hidden" name="bodypartid_'+i+'" id="bodypartid_'+i+'" value="'+body_part_id+'"><input type="text" name="textbodypart_'+i+'" id="textbodypart_'+i+'" class="text_boxes" style="width:60px" value="'+body_part_name+'" readonly /></td><td><input type="hidden" name="textwonumid_'+i+'" id="textwonumid_'+i+'" value=""><input type="text" name="textwonum_'+i+'" id="textwonum_'+i+'" class="text_boxes" style="width:60px" value="" placeholder="Browse" onDblClick="openmypage_wonum('+i+');" /></td><td><select name="cbouom_'+i+'" id="cbouom_'+i+'" class="text_boxes" style="width:55px"><option value="0">-UOM-</option><option value="1">Pcs</option><option value="2">Dzn</option><option value="12">Kg</option></select></td><td><input type="text" name="txtfabqnty_'+i+'" id="txtfabqnty_'+i+'" class="text_boxes_numeric" style="width:50px" value="'+rec_qty+'" disabled /></td><td><input type="text" name="rate_'+i+'" id="rate_'+i+'" class="text_boxes_numeric" style="width:40px" value="'+booking_rate+'" onBlur="amount_caculation('+i+'); fnc_rate_copy('+i+');" disabled/></td><td><input type="text" name="txtrate_'+i+'" id="txtrate_'+i+'" class="text_boxes_numeric" style="width:40px" value="" onBlur="amount_caculation('+i+'); fnc_rate_copy('+i+');" /><input type="hidden" name="txthiddenrate_'+i+'" id="txthiddenrate_'+i+'" class="text_boxes_numeric" style="width:40px" value="" /></td><td><input type="text" name="txtamountusd_'+i+'" id="txtamountusd_'+i+'" style="width:60px" class="text_boxes_numeric" value="'+amount_usd+'" readonly /></td><td><input type="text" name="txtexRate_'+i+'" id="txtexRate_'+i+'" style="width:60px" class="text_boxes_numeric" value="'+ex_rate+'" readonly /></td><td><input type="text" name="txtamount_'+i+'" id="txtamount_'+i+'" style="width:60px" class="text_boxes_numeric" value="" readonly /></td><td><? echo create_drop_down( "curanci_'+i+'", 60, $currency,"", 1, "-Select Currency-",1,"",0,"" ); ?></td><td><input type="button" name="remarks_'+i+'" id="remarks_'+i+'" class="formbuttonplasminus" style="width:25px" value="R" onClick="openmypage_remarks('+i+');" /><input type="hidden" name="txtremarks_'+i+'" id="txtremarks_'+i+'" class="text_boxes" value="" /></td></tr>';
						//alert(i)
						uom_arr[i]=selected_uom;
					}
				}
			}
			seq_arr=i;
		}
		else
		{
			$("#outside_finishingbill_table tr").remove();
			var list_view_str = return_global_ajax_value( uid+'__'+$('#cbo_bill_for').val(), 'load_dtls_data', '', 'requires/outside_finishing_bill_entry_controller');
			//alert(list_view_str)
			var split_list_view=list_view_str.split('###');
			var m=1; var mn=0;
			//
			for (var n=1; n<=split_list_view.length; n++)
			{
				//alert(split_list_view.length)
				var split_list_str=split_list_view[mn].split('_');
				//var trid=split_list_str[2]+"_"+split_list_str[3]+"_"+split_list_str[12]+"_"+split_list_str[9]+"_"+split_list_str[11]+"_"+split_list_str[19]+"_"+split_list_str[14];
				var trid=split_list_str[0];
				var rec_id=trim(split_list_str[0]);
				var recdate=split_list_str[1];
				var rec_challan=split_list_str[2];
				var po_id=split_list_str[3];
				var po_no=split_list_str[4];
				var style_ref=split_list_str[5];
				var buyer=split_list_str[6];
				var job=split_list_str[7];
				var roll_no=split_list_str[8];
				var body_part_id=split_list_str[9];
				var body_part_name=split_list_str[10];
				var fab_des_id=split_list_str[11];
				var prod_id=split_list_str[12];
				var prod_name=split_list_str[13];
				var batch_id=split_list_str[14];
				var color_id=split_list_str[15];
				var color_name=split_list_str[16];
				var sub_process_id=split_list_str[17];
				var process_name=split_list_str[18];
				var dia_width_id=split_list_str[19];
				var dia_width_name=split_list_str[20];
				var rec_qty=split_list_str[21];
				var challan_no=split_list_str[22];
				
				var rate=split_list_str[23];
				
				var amount=split_list_str[24];
				var upd_id=split_list_str[25];
				var remarks=split_list_str[26];
				var uom=split_list_str[27];
				var service_process=split_list_str[28];
				var service_process_id=split_list_str[29];
				var service_source=split_list_str[30];
				var booking_rate=split_list_str[34];
				var sub_service_process=split_list_str[35];
				var amount_usd=split_list_str[36];
				var ex_rate=split_list_str[37];
				
				var currency_id=1;
				if(split_list_str.length>30)
				{

					currency_id=trim(split_list_str[31]);

				}
				var wo_no=split_list_str[32];
				var wo_id=split_list_str[33];
				<? 
				
				//$dropdown= create_drop_down( "curanci_'+m+'", 60, $currency,"", 1, "-Select Currency-",1,"",1,"" );
				  $js_array = json_encode($currency);
				  echo "var javascript_currency_arr = ". $js_array . ";\n";
				 ?>
				var dropdown='<input type="hidden" name="curanci_'+m+'" id="curanci_'+m+'" style="width:50px" class="text_boxes_numeric" value="'+currency_id+'" readonly /><input type="text" name="curan_'+m+'" id="curan_'+m+'" style="width:50px" class="text_boxes" value="'+javascript_currency_arr[currency_id]+'" readonly disabled />';
				//var strval=$('#strid'+trid).val();
				//var split_str=strval.split("_");
				
				
			//	var process=split_str[28];
				var process='';
				
				//listed_id[]=listed_id;
				trid=trim(trid);
				html+='<tr align="center" id="trent_'+trid+'"><td><input type="hidden" name="updateiddtls_'+m+'" id="updateiddtls_'+m+'" value="'+upd_id+'"><input type="hidden" name="subprocessId_'+i+'" id="subprocessId_'+i+'" value="'+service_process_id+'"><input type="hidden" name="serviceSource_'+i+'" id="serviceSource_'+i+'" value="'+service_source+'"><input type="hidden" name="reciveid_'+m+'" id="reciveid_'+m+'" value="'+rec_id+'"><input type="text" name="txtreceivedate_'+m+'" id="txtreceivedate_'+m+'" class="datepicker" style="width:60px" value="'+recdate+'" disabled /></td><td><input type="text" name="txtchallenno_'+m+'" id="txtchallenno_'+m+'" class="text_boxes" style="width:70px" value="'+challan_no+'" readonly /></td><td><input type="text" name="txtProcess_'+i+'" id="txtProcess_'+i+'" value="'+service_process+'" class="text_boxes" style="width:90px" readonly /></td><td><input type="text" name="txtSubProcess_'+i+'" id="txtSubProcess_'+i+'" value="'+sub_service_process+'" class="text_boxes" style="width:90px" readonly /></td><td><input type="hidden" name="ordernoid_'+m+'" id="ordernoid_'+m+'" value="'+po_id+'" style="width:40px" /><input type="text" name="txtorderno_'+m+'" id="txtorderno_'+m+'" class="text_boxes" style="width:60px" value="'+po_no+'" readonly /></td><td><input type="text" name="txtstylename_'+m+'" id="txtstylename_'+m+'" class="text_boxes" style="width:70px;" value="'+style_ref+'" readonly /></td><td><input type="text" name="txtpartyname_'+m+'" id="txtpartyname_'+m+'" class="text_boxes" style="width:60px" value="'+buyer+'" readonly /></td><td><input name="txtnumberroll_'+m+'" id="txtnumberroll_'+m+'" type="text" class="text_boxes" style="width:40px" value="'+roll_no+'" readonly /></td><td><input type="hidden" name="diatype_'+m+'" id="diatype_'+m+'" value="'+dia_width_id+'"><input type="hidden" name="compoid_'+m+'" id="compoid_'+m+'" value="'+fab_des_id+'"><input type="hidden" name="itemid_'+m+'" id="itemid_'+m+'" value="'+prod_id+'"><input type="hidden" name="batchid_'+m+'" id="batchid_'+m+'" value="'+batch_id+'"><input type="text" name="textfebricdesc_'+m+'" id="textfebricdesc_'+m+'" class="text_boxes" style="width:100px" value="'+prod_name+'" readonly/></td><td><input type="hidden" name="colorid_'+m+'" id="colorid_'+m+'" value="'+color_id+'"><input type="text" name="textcolor_'+m+'" id="textcolor_'+m+'" class="text_boxes" style="width:60px" value="'+color_name+'" readonly/></td><td><input type="hidden" name="bodypartid_'+m+'" id="bodypartid_'+m+'" value="'+body_part_id+'"><input type="text" name="textbodypart_'+m+'" id="textbodypart_'+m+'" class="text_boxes" style="width:60px" value="'+body_part_name+'" readonly /></td><td><input type="hidden" name="textwonumid_'+m+'" id="textwonumid_'+m+'" value="'+wo_id+'"><input type="text" name="textwonum_'+m+'" id="textwonum_'+m+'" class="text_boxes" style="width:60px" value="'+wo_no+'" placeholder="Browse" onDblClick="openmypage_wonum('+m+');" /></td><td><select name="cbouom_'+m+'" id="cbouom_'+m+'" class="text_boxes" style="width:55px"><option value="0">-UOM-</option><option value="1">Pcs</option><option value="2">Dzn</option><option value="12">Kg</option></select></td><td><input type="text" name="txtfabqnty_'+m+'" id="txtfabqnty_'+m+'" class="text_boxes_numeric" style="width:50px" value="'+rec_qty+'" disabled /></td><td><input type="text" name="rate_'+m+'" id="rate_'+m+'" class="text_boxes_numeric" style="width:40px" value="'+booking_rate+'" onBlur="amount_caculation('+m+'); fnc_rate_copy('+m+');" disabled/></td><td><input type="text" name="txtrate_'+m+'" id="txtrate_'+m+'" class="text_boxes_numeric" style="width:40px" value="'+rate+'" onBlur="amount_caculation('+m+'); fnc_rate_copy('+m+');" /><input type="hidden" name="txthiddenrate_'+m+'" id="txthiddenrate_'+m+'" class="text_boxes_numeric" style="width:40px" value="'+rate+'" /></td><td><input type="text" name="txtamountusd_'+m+'" id="txtamountusd_'+m+'" style="width:60px" class="text_boxes_numeric" value="'+amount_usd+'" readonly /></td><td><input type="text" name="txtexRate_'+m+'" id="txtexRate_'+m+'" style="width:60px" class="text_boxes_numeric" value="'+ex_rate+'" readonly /></td><td><input type="text" name="txtamount_'+m+'" id="txtamount_'+m+'" style="width:60px" class="text_boxes_numeric" value="'+amount+'" readonly /></td><td>'+dropdown+'</td><td><input type="button" name="remarks_'+m+'" id="remarks_'+m+'" class="formbuttonplasminus" style="width:25px" value="R" onClick="openmypage_remarks('+m+');" /><input type="hidden" name="txtremarks_'+m+'" id="txtremarks_'+m+'" class="text_boxes" value="'+remarks+'" /></td></tr>';
				//alert(html)
				uom_arr[m]=uom;
				mn++;
				m++;
			}
			seq_arr=m;
		}
		
		$("#outside_finishingbill_table").append( html );
		
		var counter =$('#outside_finishingbill_table tr').length; 
		for(var q=1; q<=counter; q++)
		{
			var index=q-1;
			$("#outside_finishingbill_table tr:eq("+index+")").find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ q },
					'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ q },
				}); 
			})
			$('#textwonum_'+q).removeAttr("onDblClick").attr("onDblClick","openmypage_wonum("+q+");");
			$('#txtrate_'+q).removeAttr("onBlur").attr("onBlur","amount_caculation("+q+"); fnc_rate_copy("+q+");");
			//$('#txtrate_'+q).removeAttr("onBlur").attr("onBlur","fnc_rate_copy("+q+");");
			//$('#txtRate_'+q).removeAttr("onDblClick").attr("onDblClick","openmypage_rate("+q+");");
			//$('#txtAddRate_'+q).removeAttr("onBlur").attr("onBlur","qnty_caluculation("+q+");");
			$('#remarks_'+q).removeAttr("onClick").attr("onClick","openmypage_remarks("+q+");");
			
			$("#cbouom_"+q).val( uom_arr[q] );
		}
		
		var tot_row=$('#outside_finishingbill_table tr').length;
		ddd={dec_type:5,comma:0};
		math_operation( "txt_tot_qnty", "txtfabqnty_", "+", tot_row,ddd );
		math_operation( "txt_tot_amount", "txtamount_", "+", tot_row,ddd );
		set_all_onclick();
	}
	
	function amount_caculation(id)
	{
		var tot_row=$('#outside_finishingbill_table tr').length;
		var amount = ($("#txtfabqnty_"+id).val()*1)*($("#txtrate_"+id).val()*1);
		var amount_chk = ($("#txtfabqnty_"+id).val()*1)*($("#txthiddenrate_"+id).val()*1);
		var txtrate=$("#txtrate_"+id).val()*1;
		var wo_no=$("#textwonum_"+id).val();
		var hidden_rate=$("#txthiddenrate_"+id).val()*1;
		if(wo_no!="")
		{
			if(txtrate>hidden_rate)
			{
				$("#txtrate_"+id).val(hidden_rate);
				var msg='Bill Rate is over than WO Rate';
				//$("#txtamount_"+id).val(amount_chk);
				alert(msg);
				 return;
			}
		}
		
		$("#txtamount_"+id).val( amount.toFixed(4) );
		ddd={dec_type:5,comma:0};
		math_operation( "txt_tot_qnty", "txtfabqnty_", "+", tot_row,ddd );
		math_operation( "txt_tot_amount", "txtamount_", "+", tot_row,ddd );
	}

	function openmypage_remarks(id)
	{
		var data=document.getElementById('txtremarks_'+id).value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/outside_finishing_bill_entry_controller.php?data='+data+'&action=remarks_popup','Remarks Popup', 'width=420px,height=320px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("text_new_remarks");//Access form field with id="emailfield"
			if (theemail.value!="")
			{
				$('#txtremarks_'+id).val(theemail.value);
			}
		}
	}
	
	function fnc_check(inc_id)
	{
		//alert(inc_id)
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
		var is_ratecopy=document.getElementById('checkrate').value;
		var txtrate=$('#txtrate_'+trid).val();
		if(is_ratecopy==1)
		{		
			var row_nums=$('#outside_finishingbill_table tr').length;
			for(var j=trid; j<=row_nums; j++)
			{
				
				document.getElementById('txtrate_'+j).value=txtrate;
				amount_caculation(j);
			}
		}
	}
	
	function fnc_list_search(type)
	{
		/*if( form_validation('cbo_company_id*cbo_party_source*cbo_party_name','Company Name*Party Name*Party Source')==false)
		{
			return;
		}
		
		if( form_validation('txt_bill_form_date*txt_bill_to_date','From Date*To Date')==false)
		{
			if( form_validation('txt_manual_challan','Manual Challan')==false)
			{
				return;
			}
		}*/
		
		$('#cbo_company_id').attr('disabled','disabled');
		$('#cbo_location_name').attr('disabled','disabled');
		$('#cbo_party_source').attr('disabled','disabled');
		$('#cbo_party_name').attr('disabled','disabled');
		$('#cbo_bill_for').attr('disabled','disabled');
			
		$('#txt_bill_form_date').removeAttr('disabled','disabled');
		$('#txt_bill_to_date').removeAttr('disabled','disabled');
		$('#txt_manual_challan').removeAttr('disabled','disabled');
		
		if (type==0 && ($('#update_id').val()*1)==0)
		{
			show_list_view(document.getElementById('cbo_company_id').value+'***'+document.getElementById('cbo_supplier_company').value+'***'+document.getElementById('cbo_bill_for').value+'***'+document.getElementById('txt_bill_form_date').value+'***'+document.getElementById('txt_bill_to_date').value+'***'+document.getElementById('txt_manual_challan').value+'***'+$('#variable_check').val()+'***'+''+'***'+''+'***'+$('#txt_bill_date').val(),'outside_finishing_info_list_view','outside_finishing_info_list','requires/outside_finishing_bill_entry_controller', 'setFilterGrid("tbl_list_search",-1)','','');
		}
		else
		{
			var tot_row=$('#outside_finishingbill_table tr').length;
			//alert(tot_row)
				
			var all_value="";
			for (var n=1; n<=tot_row; n++)
			{
				if(all_value=="") all_value+=$('#reciveid_'+n).val(); 
				
				else all_value+='!!!!'+$('#reciveid_'+n).val();
			}
			//alert(all_value);
			show_list_view(document.getElementById('cbo_company_id').value+'***'+document.getElementById('cbo_supplier_company').value+'***'+document.getElementById('cbo_bill_for').value+'***'+document.getElementById('txt_bill_form_date').value+'***'+document.getElementById('txt_bill_to_date').value+'***'+document.getElementById('txt_manual_challan').value+'***'+$('#variable_check').val()+'***'+type+'***'+all_value+'***'+$('#txt_bill_date').val(),'outside_finishing_info_list_view','outside_finishing_info_list','requires/outside_finishing_bill_entry_controller','setFilterGrid("tbl_list_search",-1)','','');
		}
	}
	function fnc_print_report(report_type)
	{
		if(report_type==0)
		{
			var report_title=$( "div.form_caption" ).html();
			var data=$('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#hidsyschallan').val();
			window.open("requires/outside_finishing_bill_entry_controller.php?data=" + data+'&action=fabric_finishing_print', true );
			show_msg("3");
			return;
			//return;
			
		}
		else if(report_type==1)
		{
			var report_title=$( "div.form_caption" ).html();
			var data=$('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#hidsyschallan').val();
			window.open("requires/outside_finishing_bill_entry_controller.php?data=" + data+'&action=fabric_dyeing_finishing_print', true );
			show_msg("3");
			return;
			//return;
			
		}
	}

	function print_button_setting()
	{
	//  console.log('hello');
		//$('#button_data_panel').html('');
		get_php_form_data($('#cbo_company_id').val(),'print_button_variable_setting','requires/outside_finishing_bill_entry_controller');
	}
	function fnc_net_calculation()//here
	{
		//var tot_row=$('#bill_issue_table tr').length;
			var tot_amount=$('#txt_tot_amount').val()*1;
			var txt_upcharge=$('#txt_upcharge').val()*1;
			var txt_discount=$('#txt_discount').val()*1;
			var totamount=tot_amount+txt_upcharge;
			var tot_amount_cal=totamount-txt_discount;
			
			
			//$('#txt_tot_qnty').val(number_format(totqty,2,'.','' ));
			$('#txt_net_total').val(number_format(tot_amount_cal,2,'.','' ));
	}
	function fnc_head_change(type)
	{
		var bill_for_id=$('#cbo_company_id').val();
		if(type ==4)
		{
			document.getElementById('order_head').innerHTML='FSO No';
		}
		else{
			document.getElementById('order_head').innerHTML='Order No';

		}
		
	}

</script>
</head>
<body onLoad="set_hotkey()">
   <div align="center" style="width:100%;">
   <? echo load_freeze_divs ("../",$permission);  ?>
    <form name="outfinishingbill_1" id="outfinishingbill_1"  autocomplete="off"  >
    <fieldset style="width:810px;">
    <legend>Finishing Bill Info </legend>
        <table cellpadding="0" cellspacing="2" width="100%">
            <tr>
                <td align="right" colspan="3"><strong>Bill No </strong></td>
                <td width="150">
                	<input type="hidden" name="hidden_acc_integ" id="hidden_acc_integ" />
                    <input type="hidden" name="selected_id" id="selected_id" />
                    <input type="hidden" name="update_id" id="update_id" value="" />
                    <input type="hidden" name="variable_check" id="variable_check" />
                    <input type="hidden" name="mandatory_check" id="mandatory_check" />
                    <input type="text" name="txt_bill_no" id="txt_bill_no" class="text_boxes" style="width:140px" placeholder="Double Click to Search" value="" onDblClick="openmypage_outside_bill();" readonly tabindex="1" >
                </td>
                <td colspan="2">&nbsp;</td>
             </tr>
             <tr>
                <td width="120" class="must_entry_caption">Company Name</td>
                <td width="150">
					<?php 
						echo create_drop_down( "cbo_company_id",150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "print_button_setting();load_drop_down( 'requires/outside_finishing_bill_entry_controller', this.value, 'load_drop_down_location', 'location_td'); load_drop_down( 'requires/outside_finishing_bill_entry_controller', this.value, 'load_drop_down_supplier_name', 'supplier_td'); get_php_form_data(this.value,'load_variable_settings','requires/outside_finishing_bill_entry_controller');","","","","","",2);	
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
                    <input class="datepicker" type="text" style="width:140px" name="txt_bill_date" id="txt_bill_date" value="<? echo date('d-m-Y'); ?>" tabindex="4" />
                </td>
            </tr> 
            <tr>
                <td class="must_entry_caption">Supplier Name</td>
                <td id="supplier_td">
					<?
						echo create_drop_down( "cbo_supplier_company", 150, $blank_array,"", 1, "-- Select supplier --", $selected, "",0,"","","","",5);
					?> 	
                </td>
                <td class="must_entry_caption">Bill For</td>
                <td>
					<?
						echo create_drop_down( "cbo_bill_for", 150, $bill_for,"", 1, "-- Select --", 1, "fnc_head_change(this.value)",0,"","","","",7);
                    ?> 
                </td>
                <td>Party Bill No</td>
                <td><input type="text" name="txt_party_bill" id="txt_party_bill" class="text_boxes" style="width:140px" placeholder="Party Bill" ></td>
            </tr>
            <tr>
                <td class="must_entry_caption">Trns. Date Range</td>                                              
                <td><input class="datepicker" type="text" style="width:60px" name="txt_bill_form_date" id="txt_bill_form_date" placeholder="From Date" value="<? //echo date("06-08-2017");?>"/>&nbsp;<input class="datepicker" type="text" style="width:60px" name="txt_bill_to_date" id="txt_bill_to_date" placeholder="To Date" value="<? //echo  date("06-08-2017");?>" />
                </td>
                <td class="must_entry_caption">Manual Challan No</td>                                              
                <td><input class="text_boxes" type="text" style="width:130px" name="txt_manual_challan" id="txt_manual_challan" /></td>
                <td>&nbsp;</td>                                              
                <td><input class="formbutton" type="button" onClick="fnc_list_search(0);" style="width:130px" name="btn_populate" value="Populate" id="btn_populate" /></td>
               <!-- <td>
                <input class="formbutton" type="button" onClick="show_list_view(document.getElementById('cbo_company_id').value+'***'+document.getElementById('cbo_supplier_company').value+'***'+document.getElementById('cbo_bill_for').value+'***'+document.getElementById('cbo_supplier_company').value+'***'+document.getElementById('txt_bill_form_date').value+'***'+document.getElementById('txt_bill_to_date').value+'***'+document.getElementById('variable_check').value+'***'+document.getElementById('update_id').value+'***'+document.getElementById('issue_id_all').value,'outside_finishing_info_list_view','outside_finishing_info_list','requires/outside_finishing_bill_entry_controller','setFilterGrid(\'list_view_issue\',-1)')" style="width:130px" name="txt_bill_date" value="Populate" id="txt_bill_date" tabindex="4" />
                </td>-->
            </tr>
			<tr>
				<td><input type="button" id="file_uploaded" class="image_uploader" style="width:130px" value="ADD FILE" onClick="file_uploader ( '../', document.getElementById('update_id').value,'', 'outside_finishing_bill_entry', 2 ,1)"></td>

				<td><input type="file" class="image_uploader" id="multiple_file_field" name="multiple_file_field" multiple style="width:130px"></td>
			</tr>
        </table>
        </fieldset>
        &nbsp;
        <fieldset style="width:1110px;">
    	<legend>Finishing Bill Details </legend>
        <table  style="border:none; width:1110px;" cellpadding="0" cellspacing="1" border="0" id="">
            <thead class="form_table_header">
                <th width="60" class="must_entry_caption">Receive Date </th>
                <th width="70" class="must_entry_caption">Challan No.</th>
                <th width="100">Process</th>
				<th width="100">Sub Process</th>
                <th width="60" id="order_head">Order No.</th>
                <th width="70">Style</th>
                <th width="60">Buyer</th>
                <th width="40">N.O Roll</th>
                <th width="100">Fabric Des.</th>
                <th width="60">Color</th>
                <th width="60">Body Part</th>
                <th width="60">WO Num</th>
                <th width="50" class="must_entry_caption">UOM</th>
                <th width="50" class="must_entry_caption">Fabric Qty</th>
				<th width="40">Rate</th>
                <th width="40" class="must_entry_caption">Rate (Main)<input type="checkbox" name="checkrate" id="checkrate" onClick="fnc_check('rate'); " value="2" ></th>
               <th width="60">Amount(USD)</th>
               <th width="60">Ex. Rate</th>
                <th width="60">Amount</th>
                
                <th width="60">Currency</th>
                <th>RMK</th>
            </thead>
            <tbody id="outside_finishingbill_table">
                <tr align="center">				
                    <td>
                        <input type="hidden" name="updateiddtls_1" id="updateiddtls_1" style="width:50px">
                        <input type="text" name="txtreceivedate_1" id="txtreceivedate_1"  class="text_boxes" style="width:60px" disabled />									
                    </td>
                    <td>
                        <input type="text" name="txtchallenno_1" id="txtchallenno_1"  class="text_boxes" style="width:70px" readonly />							 
                    </td>
                    <td>
                        <input type="text" name="txtProcess_1" id="txtProcess_1"  class="text_boxes" style="width:90px" readonly />							 
                    </td>
					<td>
                        <input type="text" name="txtSubProcess_1" id="txtSubProcess_1"  class="text_boxes" style="width:90px" readonly />							 
                    </td>
                    <td>
                        <input type="hidden" name="ordernoid_1" id="ordernoid_1" value="" style="width:50px">
                        <input type="text" name="txtorderno_1" id="txtorderno_1"  class="text_boxes" style="width:60px" readonly />										
                    </td>
                    <td>
                        <input type="text" name="txtstylename_1" id="txtstylename_1"  class="text_boxes" style="width:70px;" />
                    </td>
                    <td>
                        <input type="text" name="txtpartyname_1" id="txtpartyname_1"  class="text_boxes" style="width:60px" />								
                    </td>
                    <td>			
                        <input type="text" name="txtnumberroll_1" id="txtnumberroll_1" class="text_boxes" style="width:40px" readonly />							
                    </td>  
                    <td>
                        <input type="text" name="textfebricdesc_1" id="textfebricdesc_1"  class="text_boxes" style="width:100px" readonly/>
                    </td>
                    <td>
                    	<input type="hidden" name="colorid_1" id="colorid_1" value="" style="width:50px">
                        <input type="text" name="textcolor_1" id="textcolor_1"  class="text_boxes" style="width:60px" readonly/>
                    </td>
                    <td>
                    	<input type="hidden" name="bodypartid_1" id="bodypartid_1" value="" style="width:50px">
                        <input type="text" name="textbodypart_1" id="textbodypart_1"  class="text_boxes" style="width:60px" readonly/>
                    </td>
                    <td>
						<input type="hidden" name="textwonumid_1" id="textwonumid_1" value="" style="width:50px">
                        <input type="text" name="textwonum_1" id="textwonum_1" class="text_boxes" style="width:60px" placeholder="Browse" onDblClick="openmypage_wonum(1);" readonly/>
                    </td>
                    <td>
						<? echo create_drop_down( "cbouom_1", 50, $unit_of_measurement,"", 0, "--Select UOM--",12,"",1,"" );?>
                    </td>
                    <td>
                        <input type="text" name="txtfabqnty_1" id="txtfabqnty_1"  class="text_boxes_numeric" style="width:50px" readonly />
                    </td>
					<td>
                        <input type="text" name="rate_1" id="rate_1"  class="text_boxes_numeric" style="width:40px" readonly />
                    </td>
                    <td>
                        <input type="text" name="txtrate_1" id="txtrate_1"  class="text_boxes_numeric" style="width:40px" readonly />
                    </td>
 					<td>
                        <input type="text" name="txtamountusd_1" id="txtamountusd_1" class="text_boxes_numeric" style="width:60px"  readonly />
                    </td>
                     <td>
                        <input type="text" name="txtexRate_1" id="txtexRate_1" class="text_boxes_numeric" style="width:60px"  readonly />
                    </td>
                    
                    <td>
                        <input type="text" name="txtamount_1" id="txtamount_1" class="text_boxes_numeric" style="width:60px"  readonly />
                    </td>

                    <td>
                    	<? echo create_drop_down( "curanci_1", 60, $currency,"", 1, "-Currency-",1,"",1,"" ); ?>
                    </td>

                    <td>
                        <input type="button" name="remarks_1" id="remarks_1"  class="formbuttonplasminus" style="width:25px" value="R" onClick="openmypage_remarks(1);" />
                     	<input type="hidden" name="txtremarks_1" id="txtremarks_1" class="text_boxes" value="" />
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                	<td width="60px">&nbsp;</td>
                    <td width="70px">&nbsp;</td>
                    <td width="100px">&nbsp;</td>
					<td width="100px">&nbsp;</td>
                    <td width="60px">&nbsp;</td>
                    <td width="70px">&nbsp;</td>
                    <td width="60px">&nbsp;</td>
                    <td width="40px">&nbsp;</td>
                    <td width="100px" align="right"></td>
                    <td width="60px">&nbsp;</td>
                    <td width="60px">&nbsp;</td>
                    <td width="60px" align="right">&nbsp;</td>
                    <td width="50px" align="right">Total:</td>
                    
                    <td width="50px">
                    	<input type="text" name="txt_tot_qnty" id="txt_tot_qnty"  class="text_boxes_numeric" style="width:50px" disabled/>
                    </td>
					<td width="40px">&nbsp;</td>
                    <td width="40px">&nbsp;</td>
                      <td width="60px">
                    	<input type="text" name="txt_tot_amount_usd" id="txt_tot_amount_usd"  class="text_boxes_numeric" style="width:60px" disabled/>
                    </td>
                      <td width="60px">
                    	 
                    </td>
                    <td width="60px">
                    	<input type="text" name="txt_tot_amount" id="txt_tot_amount" class="text_boxes_numeric" style="width:60px"  disabled/>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
				<tr>
                    <td colspan="18" height="15" align="center"><div id="bill_on" style="float:left; font-size:18px; color:#FF0000;"></div><div id="accounting_integration_div" style="float:center; font-size:18px; color:#FF0000;"></div></td>
                    <td>
                     <input type="text" title="Upcharge" name="txt_upcharge" id="txt_upcharge" onBlur="fnc_net_calculation()"  class="text_boxes_numeric" style="width:60px" placeholder="Upcharge"/> 
                      <input type="text" title="Discount" name="txt_discount" id="txt_discount"  class="text_boxes_numeric"  onBlur="fnc_net_calculation()" style="width:60px" placeholder="Discount"/> 
                     <input type="text" title="Net Total" name="txt_net_total" id="txt_net_total"  class="text_boxes_numeric" style="width:60px" placeholder="Net Total"/> 
                     </td>
                </tr>                 
                <tr>
                    <td colspan="15" height="15" align="center"><div id="bill_on" style="float:left; font-size:18px; color:#FF0000;"></div><div id="accounting_integration_div" style="float:center; font-size:18px; color:#FF0000;"></div></td>
                </tr>
                <tr>
                    <td colspan="17" align="center" class="button_container">
						<? 
						echo load_submit_buttons($permission,"fnc_outside_finishing_bill_entry",0,0,"reset_form('outfinishingbill_1','outside_finishing_info_list','','','$(\'#bill_issue_table tr:not(:first)\').remove();')",1);
                        ?> 
                       <input type="button" name="printb" id="printb" value="Print" onClick="fnc_print_report(0);" style="width:80px" class="formbutton" />
                        <input type="button" name="printb1" id="printb1" value="Print 2" onClick="fnc_print_report(1);" style="width:80px" class="formbutton" />
                    </td>
                </tr>
            </tfoot>                                                
        </table>
        </fieldset>
        <br>
        <div id="outside_finishing_info_list"></div>  
        </form>
         <div style="width:250px; margin-top:13px; margin-left:0px; float:left;">
        <div id="wonum_list_view"></div>
        </div>
     </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
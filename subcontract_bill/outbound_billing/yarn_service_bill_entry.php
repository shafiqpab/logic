<?php
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Out Bound Yarn Service Bill Entry
Functionality	:	
JS Functions	:
Created by		:	Sapayth
Creation date 	: 	05-10-2020
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
//--------------------------------------------------------------------------------------------------------------------
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

	function fnc_list_search(type) // for populating data
	{
		if( form_validation('cbo_company_id*cbo_supplier_company*txt_bill_from_date*txt_bill_to_date*cbo_bill_for','Company Name*Supplier*From Date*To Date*Bill For')==false)
		{
			return;
		}
		
		$('#cbo_company_id').attr('disabled','disabled');
		$('#cbo_location_name').attr('disabled','disabled');
		$('#cbo_bill_for').attr('disabled','disabled');			
		$('#txt_bill_from_date').removeAttr('disabled','disabled');
		$('#txt_bill_to_date').removeAttr('disabled','disabled');
		//$('#txt_manual_challan').removeAttr('disabled','disabled');
		
		if (type==0 && ($('#update_id').val()*1)==0)
		{
			//alert("Ok")
			show_list_view(document.getElementById('cbo_company_id').value+'***'+document.getElementById('cbo_supplier_company').value+'***'+document.getElementById('cbo_bill_for').value+'***'+document.getElementById('txt_bill_from_date').value+'***'+document.getElementById('txt_bill_to_date').value+'***'+document.getElementById('txt_manual_challan').value+'***'+$('#variable_check').val(),'outside_yarn_service_info_list_view','outside_yarn_service_info_list','requires/yarn_service_bill_entry_controller', 'setFilterGrid("tbl_list_search",-1)','','');
		}
		else
		{
			var tot_row=$('#outside_yarnservicebill_table tr').length;
			//alert(tot_row)
			var all_value="";
			for (var n=1; n<=tot_row; n++)
			{
				if(all_value=="") all_value+=$('#reciveId_'+n).val()*1; 
				else all_value+='!!!!'+$('#reciveId_'+n).val()*1;
			}
			//alert(all_value);
			show_list_view(document.getElementById('cbo_company_id').value+'***'+document.getElementById('cbo_supplier_company').value+'***'+document.getElementById('cbo_bill_for').value+'***'+document.getElementById('txt_bill_from_date').value+'***'+document.getElementById('txt_bill_to_date').value+'***'+document.getElementById('txt_manual_challan').value+'***'+$('#variable_check').val()+'***'+type+'***'+all_value,'outside_yarn_service_info_list_view','outside_yarn_service_info_list','requires/yarn_service_bill_entry_controller','setFilterGrid("tbl_list_search",-1)','','');
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

	function window_close( uid )
	{
		var html="";
		var is_readonly=""; var isreadonly='';

		if(uid==0)
		{
			var list_tot_row=$('#tbl_list_search tr').length-1;
			//alert(list_tot_row);
			var i=0; if(seq_arr!=0) i=seq_arr; else i=i;
			
			for (var k=1; k<=list_tot_row; k++)
			{
				var check_id=''; var strval=''; var trid="";
				var check_id=$('#checkid'+k).val();
				var strval=$('#strid'+k).val();
				var split_str=strval.split("_");
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
				//alert(check_id);
				if(check_id==1)
				{
					//alert(selected_id_listed.length)
					if(selected_id_listed.length==0)
					{
						$("#outside_yarnservicebill_table tr").remove();
					}
					//alert(selected_id_listed);
					if( jQuery.inArray(  trid , selected_id_listed ) == -1) 
					{
						i++;
						selected_id_listed.push( trid );
						//alert(selected_id_listed)
						var rec_id		=split_str[0];
						var recdate		=split_str[1];
						var rec_number	=split_str[2];
						var rec_challan	=split_str[3];
						var job_no		=split_str[4];
						var style_ref	=split_str[5];
						var buyer		=split_str[6];
						var no_of_bags	=split_str[7];
						var cone_per_bag=split_str[8];
						var prod_id		=split_str[9];
						var prod_name	=split_str[10];
						
						var color_id	=split_str[11];
						var color_name	=split_str[12];
						var lot			=split_str[13];
						var booking_id	=split_str[14];
						var booking_no	=split_str[15];
						var order_uom	=split_str[16];
						
						var avilable_qty=split_str[17];
						var dye_charge	=split_str[18];
						var amount		=split_str[19];
						var domestic_currency=split_str[20];
						var currency_id	=split_str[21];
						var remarks		=split_str[22];
						var upd_id		=split_str[23];
						//alert(service_source+"_"+remarks+"_"+upd_id);
						var is_disable=""; 
						var selected_uom="";

						selected_uom=12;
						html+='<tr align="center" id="trent_'+trid+'"><td><input type="hidden" name="updateIdDtls_'+i+'" id="updateIdDtls_'+i+'" value="'+upd_id+'"><input type="hidden" name="reciveId_'+i+'" id="reciveId_'+i+'" value="'+rec_id
						+'"><input type="text" name="txtReceiveDate_'+i+'" id="txtReceiveDate_'+i+'" class="datepicker" style="width:60px" value="'+recdate
						+'" readonly /></td><td><input type="text" name="txtMrrNo_'+i+'" id="txtMrrNo_'+i+'" value="'+rec_number
						+'" class="text_boxes" style="width:100px" readonly /></td><td><input type="text" name="txtChallenNo_'+i+'" id="txtChallenNo_'+i+'" class="text_boxes" style="width:70px" value="'+rec_challan
						+'" readonly /></td><td><input type="text" name="txtOrderNo_'+i+'" id="txtOrderNo_'+i+'" class="text_boxes" style="width:80px" value="'+job_no
						+'" readonly /></td><td><input type="text" name="txtStyleName_'+i+'" id="txtStyleName_'+i+'" class="text_boxes" style="width:70px;" value="'+style_ref
						+'" readonly /></td><td><input type="text" name="txtPartyName_'+i+'" id="txtPartyName_'+i+'" class="text_boxes" style="width:60px" value="'+buyer
						+'" readonly /></td><td><input type="text" name="txtNumberBag_'+i+'" id="txtNumberBag_'+i+'" class="text_boxes" style="width:40px" value="'+no_of_bags
						+'" readonly /></td><td><input type="text" name="txtNumberCone_'+i+'" id="txtNumberCone_'+i+'" class="text_boxes" style="width:40px" value="'+cone_per_bag
						+'" readonly /></td><td><input type="hidden" name="diatype_'+i+'" id="diatype_'+i+'" value=""><input type="hidden" name="compoid_'+i+'" id="compoid_'+i+'" value=""><input type="hidden" name="itemid_'+i+'" id="itemid_'+i+'" value="'+prod_id
						+'"><input type="hidden" name="batchid_'+i+'" id="batchid_'+i+'" value=""><input type="text" name="textYarnDesc_'+i+'" id="textYarnDesc_'+i+'" class="text_boxes" style="width:100px" value="'+prod_name
						+'" readonly/></td><td><input type="hidden" name="bodyPartId_'+i+'" id="bodyPartId_'+i+'" value=""><input type="text" name="texLotBatch_'+i+'" id="texLotBatch_'+i+'" class="text_boxes" style="width:60px" value="'+lot
						+'" readonly /></td><td><input type="hidden" name="textWoNumId_'+i+'" id="textWoNumId_'+i+'"  value="'+booking_id
						+'"><input type="text" name="textWoNum_'+i+'" id="textWoNum_'+i+'" class="text_boxes" style="width:60px" placeholder="Display"  value="'+booking_no+'" disabled/></td><td><select name="cboUom_'+i+'" id="cboUom_'+i+'" class="text_boxes" style="width:55px"><option value="'+order_uom+'">-UOM-</option><option value="1">Pcs</option><option value="2">Dzn</option><option value="12">Kg</option></select></td><td><input type="text" name="txtYarnQty_'+i+'" id="txtYarnQty_'+i+'" class="text_boxes_numeric" style="width:50px" value="'+avilable_qty
						+'" /></td><td><input type="text" name="txtRate_'+i+'" id="txtRate_'+i+'" class="text_boxes_numeric" style="width:40px" value="'+dye_charge
						+'" onBlur="amount_calculation('+i+');" /></td><td><input type="text" name="txtAmount_'+i+'" id="txtAmount_'+i+'" style="width:70px" class="text_boxes_numeric" value="'+amount
						+'" readonly /></td><td style="display:none;"><input type="text" name="txtDomesticAmount_'+i+'" id="txtDomesticAmount_'+i+'" style="width:70px" class="text_boxes_numeric" value="'+domestic_currency
						+'" readonly /></td><td><? echo create_drop_down( "curanci_'+i+'", 70, $currency,"", 1, "-Select Currency-",1,"",1,"" ); ?></td><td><input type="button" name="remarks_'+i+'" id="remarks_'+i+'" class="formbuttonplasminus" style="width:25px" value="R" onClick="openmypage_remarks('+i+');" /><input type="hidden" name="txtRemarks_'+i+'" id="txtRemarks_'+i+'" class="text_boxes" value="'+remarks+'" /></td></tr>';
						//alert(i)
						// fnc_rate_copy('+i+');
						uom_arr[i]=selected_uom;
					}
				}
			}
			seq_arr=i;
		}
		else
		{
			$("#outside_yarnservicebill_table tr").remove();
			var list_view_str = return_global_ajax_value( uid, 'load_dtls_data', '', 'requires/yarn_service_bill_entry_controller');
			//alert(list_view_str);
			var split_list_view=list_view_str.split('###');
			var m=1; 
			var mn=0;			
			for (var n=0; n<=split_list_view.length-1; n++)
			{
				var i=n+1;
					//alert(split_list_view.length)
				var split_list_str=split_list_view[mn].split('_');
				var trid		=split_list_str[0];
				var rec_id		=split_list_str[0];
				var recdate		=split_list_str[1];
				var rec_number	=split_list_str[2];
				var rec_challan	=split_list_str[3];
				var job_no		=split_list_str[4];
				var style_ref	=split_list_str[5];
				var buyer		=split_list_str[6];
				var no_of_bags	=split_list_str[7];
				var cone_per_bag=split_list_str[8];
				var prod_id		=split_list_str[9];
				var prod_name	=split_list_str[10];
				
				var color_id	=split_list_str[11];
				var color_name	=split_list_str[12];
				var lot			=split_list_str[13];
				var booking_id	=split_list_str[14];
				var booking_no	=split_list_str[15];
				var order_uom	=split_list_str[16];
				
				var avilable_qty=split_list_str[17];
				var dye_charge	=split_list_str[18];
				var amount		=split_list_str[19];
				var domestic_currency=split_list_str[20];
				var currency_id	=split_list_str[21];
				var remarks		=split_list_str[22];
				var upd_id		=split_list_str[23];
				var process='';
					
				//listed_id[]=listed_id;
				trid=trim(trid);
				html+='<tr align="center" id="trent_'+trid+'"><td><input type="hidden" name="updateIdDtls_'+i+'" id="updateIdDtls_'+i+'" value="'+upd_id+'"><input type="hidden" name="reciveId_'+i+'" id="reciveId_'+i+'" value="'+rec_id
						+'"><input type="text" name="txtReceiveDate_'+i+'" id="txtReceiveDate_'+i+'" class="datepicker" style="width:60px" value="'+recdate
						+'" readonly /></td><td><input type="text" name="txtMrrNo_'+i+'" id="txtMrrNo_'+i+'" value="'+rec_number
						+'" class="text_boxes" style="width:100px" readonly /></td><td><input type="text" name="txtChallenNo_'+i+'" id="txtChallenNo_'+i+'" class="text_boxes" style="width:70px" value="'+rec_challan
						+'" readonly /></td><td><input type="text" name="txtOrderNo_'+i+'" id="txtOrderNo_'+i+'" class="text_boxes" style="width:80px" value="'+job_no
						+'" readonly /></td><td><input type="text" name="txtStyleName_'+i+'" id="txtStyleName_'+i+'" class="text_boxes" style="width:70px;" value="'+style_ref
						+'" readonly /></td><td><input type="text" name="txtPartyName_'+i+'" id="txtPartyName_'+i+'" class="text_boxes" style="width:60px" value="'+buyer
						+'" readonly /></td><td><input type="text" name="txtNumberBag_'+i+'" id="txtNumberBag_'+i+'" class="text_boxes" style="width:40px" value="'+no_of_bags
						+'" readonly /></td><td><input type="text" name="txtNumberCone_'+i+'" id="txtNumberCone_'+i+'" class="text_boxes" style="width:40px" value="'+cone_per_bag
						+'" readonly /></td><td><input type="hidden" name="diatype_'+i+'" id="diatype_'+i+'" value=""><input type="hidden" name="compoid_'+i+'" id="compoid_'+i+'" value=""><input type="hidden" name="itemid_'+i+'" id="itemid_'+i+'" value="'+prod_id
						+'"><input type="hidden" name="batchid_'+i+'" id="batchid_'+i+'" value=""><input type="text" name="textYarnDesc_'+i+'" id="textYarnDesc_'+i+'" class="text_boxes" style="width:100px" value="'+prod_name
						+'" readonly/></td><td><input type="hidden" name="bodyPartId_'+i+'" id="bodyPartId_'+i+'" value=""><input type="text" name="texLotBatch_'+i+'" id="texLotBatch_'+i+'" class="text_boxes" style="width:60px" value="'+lot
						+'" readonly /></td><td><input type="hidden" name="textWoNumId_'+i+'" id="textWoNumId_'+i+'"  value="'+booking_id
						+'"><input type="text" name="textWoNum_'+i+'" id="textWoNum_'+i+'" class="text_boxes" style="width:60px" placeholder="Display"  value="'+booking_no+'" disabled/></td><td><select name="cboUom_'+i+'" id="cboUom_'+i+'" class="text_boxes" style="width:55px"><option value="0">-UOM-</option><option value="1">Pcs</option><option value="2">Dzn</option><option value="12">Kg</option></select></td><td><input type="text" name="txtYarnQty_'+i+'" id="txtYarnQty_'+i+'" class="text_boxes_numeric" style="width:50px" value="'+avilable_qty
						+'" /></td><td><input type="text" name="txtRate_'+i+'" id="txtRate_'+i+'" class="text_boxes_numeric" style="width:40px" value="'+dye_charge
						+'" onBlur="amount_calculation('+i+');" /></td><td><input type="text" name="txtAmount_'+i+'" id="txtAmount_'+i+'" style="width:70px" class="text_boxes_numeric" value="'+amount
						+'" readonly /></td><td style="display:none;"><input type="text" name="txtDomesticAmount_'+i+'" id="txtDomesticAmount_'+i+'" style="width:70px" class="text_boxes_numeric" value="'+domestic_currency
						+'" readonly /></td><td><? echo create_drop_down( "curanci_'+i+'", 70, $currency,"", 1, "-Select Currency-",1,"",1,"" ); ?></td><td><input type="button" name="remarks_'+i+'" id="remarks_'+i+'" class="formbuttonplasminus" style="width:25px" value="R" onClick="openmypage_remarks('+i+');" /><input type="hidden" name="txtRemarks_'+i+'" id="txtRemarks_'+i+'" class="text_boxes" value="'+remarks+'" /></td></tr>';
				//alert(html)
				// fnc_rate_copy('+i+');
				uom_arr[m]=order_uom;
				mn++;
				m++;
			}
			seq_arr=m;
		}
		
		$("#outside_yarnservicebill_table").append( html );
		
		var counter =$('#outside_yarnservicebill_table tr').length; 
		for(var q=1; q<=counter; q++)
		{
			var index=q-1;
			$("#outside_yarnservicebill_table tr:eq("+index+")").find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ q },
					'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ q },
				}); 
			})
			$('#txtRate_'+q).removeAttr("onBlur").attr("onBlur","amount_calculation("+q+");");
			// fnc_rate_copy("+q+");
			//$('#txtRate_'+q).removeAttr("onBlur").attr("onBlur","fnc_rate_copy("+q+");");
			//$('#txtRate_'+q).removeAttr("onDblClick").attr("onDblClick","openmypage_rate("+q+");");
			//$('#txtAddRate_'+q).removeAttr("onBlur").attr("onBlur","qnty_caluculation("+q+");");
			$('#remarks_'+q).removeAttr("onClick").attr("onClick","openmypage_remarks("+q+");");
			
			$("#cboUom_"+q).val( uom_arr[q]).attr('disabled','disabled');
		}
		
		var tot_row=$('#outside_yarnservicebill_table tr').length;
		math_operation( "txt_tot_qnty", "txtYarnQty_", "+", tot_row );
		math_operation( "txt_tot_amount", "txtAmount_", "+", tot_row );
		set_all_onclick();
	}

	function amount_calculation(id)
	{
		
		var tot_row=$('#outside_yarnservicebill_table tr').length;
		$("#txtAmount_"+id).val( ($("#txtYarnQty_"+id).val()*1)*($("#txtRate_"+id).val()*1) );
		
		math_operation( "txt_tot_qnty", "txtYarnQty_", "+", tot_row );
		math_operation( "txt_tot_amount", "txtAmount_", "+", tot_row );
		
		var curanci = $("#curanci_"+id).val()*1;
		if(curanci==1){
			$("#txtDomesticAmount_"+id).val( $("#txtAmount_"+id).val()*1 );
		}
		else if(curanci>1){
			get_php_form_data( id+"_"+curanci+"_"+$("#txtAmount_"+id).val()*1, "load_domestic_amount", "requires/yarn_service_bill_entry_controller" );
		}else{
			alert("Currency type is empty.");
		}
	}

	function openmypage_remarks(id)
	{
		var data=document.getElementById('txtRemarks_'+id).value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/yarn_service_bill_entry_controller.php?data='+data+'&action=remarks_popup','Remarks Popup', 'width=420px,height=320px,center=1,resize=1,scrolling=0','')
		
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
		if( form_validation('cbo_company_id*txt_bill_date*cbo_supplier_company*cbo_bill_for*txtReceiveDate_1*txtChallenNo_1','Company Name*Bill Date*supplier company*bill for*receive date*challen no')==false)
		{
			return;
		}
		
		
		if(operation== 0 || operation== 1 || operation == 2 ){
			var integration_check = $('#hidden_acc_integ').val()*1;	
			if(integration_check==1){
				alert('Already Posted In Accounts. Save Update Delete Restricted.'); return;
			}
		}

		var tot_row=$('#outside_yarnservicebill_table tr').length;
		var data1='action=save_update_delete&operation='+operation+'&tot_row='+tot_row+get_submitted_data_string('txt_bill_no*cbo_company_id*cbo_location_name*txt_bill_date*cbo_supplier_company*cbo_bill_for*txt_party_bill*txt_manual_challan*update_id', '../../../');
		//alert (data1);
		var data2='';
		for(var i=1; i<=tot_row; i++)
		{
			if($('#cboUom_'+i).val()==0)
			{
				alert ("UOM Blank.");
				$('#cboUom_'+i).focus();
				return;
			}
			else if($('#txtYarnQty_'+i).val()==0)
			{
				alert ("Qty Not Blank or Zero.");
				$('#txtYarnQty_'+i).focus();
				return;
			}
			else if($('#txtRate_'+i).val()==0)
			{
				alert ("Rate Not Blank or Zero.");
				$('#txtRate_'+i).focus();
				return;
			}
			else
			{
				data2+=get_submitted_data_string('txtReceiveDate_'+i+'*txtMrrNo_'+i+'*txtChallenNo_'+i+'*txtOrderNo_'+i+'*txtNumberBag_'+i+'*txtNumberCone_'+i+'*itemid_'+i+'*bodyPartId_'+i+'*textWoNumId_'+i+'*cboUom_'+i+'*txtYarnQty_'+i+'*txtRate_'+i+'*txtAmount_'+i+'*txtRemarks_'+i+'*reciveId_'+i+'*updateIdDtls_'+i+'*curanci_'+i+'*txtDomesticAmount_'+i,"../");+'*txtStyleName_'+i+'*txtPartyName_'+i
				//+'*compoid_'+i+'*batchid_'+i+'*diatype_'+i+'*subprocessId_'+i+'*serviceSource_'+i
			}
		}
		var data=data1+data2;
		//alert (data); return;
		freeze_window(operation);
		http.open("POST", "requires/yarn_service_bill_entry_controller.php", true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_saveUpdateDelete_response;
	}

	function fnc_saveUpdateDelete_response() // For Save and Update response
	{
		if(http.readyState == 4) 
		{
			//alert (http.responseText);//return;
			var response=trim(http.responseText).split('**');
			//if (response[0].length>2) reponse[0]=10;
			show_msg(response[0]);
			if(response[0]==0 || response[0]==1)
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_bill_no').value = response[2];
				//alert(response[1]);
				window_close(response[1]);
				set_button_status(1, permission, 'fnc_saveUpdateDelete', 1);
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
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/yarn_service_bill_entry_controller.php?data='+data+'&action=outside_bill_popup','Outside Bill Popup', 'width=700px,height=400px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById('outside_bill_id');
			if (theemail.value!="")
			{
				freeze_window(5);
				get_php_form_data( theemail.value, 'load_php_data_to_form_outside_bill', 'requires/yarn_service_bill_entry_controller');
				//$("#outside_yarnservicebill_table tr").remove();
				selected_id_listed = new Array();
				//alert(theemail.value);
				window_close(theemail.value);
				
				fnc_list_search(theemail.value);
				
				set_button_status(1, permission, 'fnc_saveUpdateDelete',1);
				//var selected_id_listed = new Array();
				//var selected_id_removed = new Array(); 
				
				
				$('#tbl_list_search tbody tr').each(function(index, element) {					
					if( $('#'+this.id).attr('bgcolor')=='yellow' )
					{
						var nid=this.id;//.replace( 'tr_', "");  
						//alert(nid);
						var nid=nid.replace( 'tr_', "");  
						//alert(nid);
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

	function generate_report_file(data,action,page)
	{
		window.open("requires/yarn_service_bill_entry_controller.php?data=" + data+'&action='+action, true );
	}

	function yarn_service_bill_print(print_type)
	{
		var update_id = $("#update_id").val();
		var company = $("#cbo_company_id").val();
		var txt_bill_no = $("#txt_bill_no").val();

		if(print_type ==1)
		{
			var report_title='Yarn Service Bill Entry';
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_bill_no').val()+'*'+report_title,'yarn_service_bill_print','requires/yarn_service_bill_entry_controller');
			//return;
			show_msg("3");
		}
	}

</script>
</head>
<body>
   <div align="center" style="width:100%;">
   <?php echo load_freeze_divs('../../', $permission); ?>
    <form name="outfinishingbill_1" id="outfinishingbill_1" autocomplete="off">
    <fieldset style="width:810px;">
    <legend>Yarn Service Bill Info</legend>
        <table cellpadding="0" cellspacing="2" width="100%">
            <tr>
                <td align="right" colspan="3"><strong>Bill No</strong></td>
                <td width="150">
                	<input type="hidden" name="hidden_acc_integ" id="hidden_acc_integ" />
                    <input type="hidden" name="selected_id" id="selected_id" />
                    <input type="hidden" name="update_id" id="update_id" />
                    <input type="hidden" name="variable_check" id="variable_check" />
                    <input type="text" name="txt_bill_no" id="txt_bill_no" class="text_boxes" style="width:140px" placeholder="Double Click to Search" onDblClick="openmypage_outside_bill();" readonly tabindex="1" >
                </td>
                <td colspan="2">&nbsp;</td>
             </tr>
             <tr>
                <td width="120" class="must_entry_caption">Company</td>
                <td width="150">
					<?php 
						echo create_drop_down("cbo_company_id",150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/yarn_service_bill_entry_controller', this.value, 'load_drop_down_location', 'location_td'); load_drop_down( 'requires/yarn_service_bill_entry_controller', this.value, 'load_drop_down_supplier_name', 'supplier_td');","","","","","",2);
                    ?>
                </td>
                <td width="120">Location</td>                                              
                <td width="150" id="location_td">
					<?php 
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
                <td class="must_entry_caption">Bill For</td>
                <td>
					<?php
						$bill_for_yarn=array(2=>'Yarn Dyeing with Order',3=>'Yarn Dyeing without Order');
						echo create_drop_down('cbo_bill_for', 150, $yarn_issue_purpose, '', 1, '-- Select --', 1, '', 0, '7,12,15,38,46,50,51', '', '', '', 7);
						// create_drop_down($field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name, $additionalClass, $additionalAttributes)
                    ?> 
                </td>
                <td>Party Bill No</td>
                <td><input type="text" name="txt_party_bill" id="txt_party_bill" class="text_boxes" style="width:140px" placeholder="Party Bill" ></td>
            </tr>
            <tr>
                <td class="must_entry_caption">Trns. Date Range</td>                                              
                <td><input class="datepicker" type="text" style="width:60px" name="txt_bill_from_date" id="txt_bill_from_date" placeholder="From Date" value="<? //echo date("06-08-2017");?>"/>&nbsp;<input class="datepicker" type="text" style="width:60px" name="txt_bill_to_date" id="txt_bill_to_date" placeholder="To Date" value="<? //echo  date("06-08-2017");?>" />
                </td>
                <td>Manual Challan No</td>                                              
                <td><input class="text_boxes" type="text" style="width:130px" name="txt_manual_challan" id="txt_manual_challan" /></td>
                <td>&nbsp;</td>                                              
                <td><input class="formbutton" type="button" onClick="fnc_list_search(0);" style="width:130px" name="btn_populate" value="Populate" id="btn_populate" /></td>
            </tr>
        </table>
        </fieldset>
        &nbsp;
        <fieldset style="width:880px;">
    	<legend>Yarn Dyeing Bill Details </legend>
        <table  style="border:none; width:950px;" cellpadding="0" cellspacing="1" border="0" id="">
            <thead class="form_table_header">
                <th width="60" class="">Receive Date </th>
                <th width="70">MRR No.</th>
                <th width="70" class="">Challan No.</th>
                <th width="80">Job No.</th>
                <th width="70">Style</th>
                <th width="60">Buyer</th>
                <th width="40">N.O Bag</th>
                <th width="40">N.O Cone</th>
                <th width="100">Yarn Des.</th>
                <th width="60">Lot</th>
                <th width="60">WO Num</th>
                <th width="50" class="">UOM</th>
                <th width="50" class="">Yarn Qty</th>
                <th width="40" class="must_entry_caption">Rate<!--<input type="checkbox" name="checkrate" id="checkrate" onClick="fnc_check('rate'); " value="2" >--></th>
                <th width="70">Amount</th>
                <th width="70" style="display:none;">Domestic Currency</th>
                <th width="60">Currency</th>
                <th>RMK</th>
            </thead>
            <tbody id="outside_yarnservicebill_table">
                <tr align="center">				
                    <td>
                        <input type="text" name="txtReceiveDate_1" id="txtReceiveDate_1"  class="text_boxes" style="width:60px" readonly placeholder="Display"/>	
                        <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" style="width:50px">								
                    </td>
                    <td>
                        <input type="text" name="txtMrrNo_1" id="txtMrrNo_1"  class="text_boxes" style="width:90px" readonly placeholder="Display" />							 
                    </td>
                    <td>
                        <input type="text" name="txtChallenNo_1" id="txtChallenNo_1"  class="text_boxes" style="width:70px" readonly placeholder="Display" />							 
                    </td>
                    
                    <td>
                        <input type="hidden" name="orderNoId_1" id="orderNoId_1" value="" style="width:50px">
                        <input type="text" name="txtOrderNo_1" id="txtOrderNo_1"  class="text_boxes" style="width:80px" readonly placeholder="Display" />										
                    </td>
                    <td>
                        <input type="text" name="txtStyleName_1" id="txtStyleName_1"  class="text_boxes" style="width:70px;" readonly  placeholder="Display"/>
                    </td>
                    <td>
                        <input type="text" name="txtPartyName_1" id="txtPartyName_1"  class="text_boxes" style="width:60px" readonly  placeholder="Display"/>								
                    </td>
                    <td>			
                        <input type="text" name="txtNumberBag_1" id="txtNumberBag_1" class="text_boxes" style="width:40px" readonly  placeholder="Display"/>							
                    </td>  
                    <td>			
                        <input type="text" name="txtNumberCone_1" id="txtNumberCone_1" class="text_boxes" style="width:40px" readonly  placeholder="Display"/>							
                    </td>
                    <td>
                        <input type="text" name="textYarnDesc_1" id="textYarnDesc_1"  class="text_boxes" style="width:100px" readonly  placeholder="Display"/>
                    </td>
                    <td>
                    	<input type="hidden" name="bodyPartId_1" id="bodyPartId_1" value="" style="width:50px">
                        <input type="text" name="texLotBatch_1" id="texLotBatch_1"  class="text_boxes" style="width:60px" readonly  placeholder="Display"/>
                    </td>
                    <td>
                        <input type="text" name="textWoNum_1" id="textWoNum_1" class="text_boxes" style="width:60px" readonly  placeholder="Display"/>
                    </td>
                    <td>
						<? echo create_drop_down( "cboUom_1", 50, $unit_of_measurement,"", 0, "--Select UOM--",12,"",1,"" );?>
                    </td>
                    <td>
                        <input type="text" name="txtYarnQty_1" id="txtYarnQty_1"  class="text_boxes_numeric" style="width:50px" placeholder="Display"/>
                    </td>
                    <td>
                        <input type="text" name="txtRate_1" id="txtRate_1"  class="text_boxes_numeric" style="width:40px"  />
                    </td>

                    <td>
                        <input type="text" name="txtAmount_1" id="txtAmount_1" class="text_boxes_numeric" style="width:70px"  readonly  placeholder="Calculative"/>
                    </td>
                    <td style="display:none;">
                        <input type="text" name="txtDomesticAmount_1" id="txtDomesticAmount_1" class="text_boxes_numeric" style="width:70px"  readonly  placeholder="Calculative"/>
                    </td>
                    <td>
                    	<? echo create_drop_down( "curanci_1", 70, $currency,"", 1, "-Currency-",1,"",1,"" ); ?>
                    </td>

                    <td>
                        <input type="button" name="remarks_1" id="remarks_1"  class="formbuttonplasminus" style="width:25px" value="R" onClick="openmypage_remarks(1);" />
                     	<input type="hidden" name="txtRemarks_1" id="txtRemarks_1" class="text_boxes" value="" />
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                	<td width="60px">&nbsp;</td>
                    <td width="70px">&nbsp;</td>
                    <td width="100px">&nbsp;</td>
                    <td width="60px">&nbsp;</td>
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
                    	<input type="text" name="txt_tot_qnty" id="txt_tot_qnty"  class="text_boxes_numeric" style="width:50px" disabled />
                    </td>
                    <td width="60px">
                    	<input type="text" name="txt_tot_amount" id="txt_tot_amount"  class="text_boxes_numeric" style="width:70px"  disabled/>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>                
                <tr>
                    <td colspan="15" height="15" align="center"><div id="bill_on" style="float:left; font-size:18px; color:#FF0000;"></div><div id="accounting_integration_div" style="float:center; font-size:18px; color:#FF0000;"></div></td>
                </tr>
                <tr>
                    <td colspan="13" align="center" class="button_container">
						<?php 
							echo load_submit_buttons($permission,"fnc_saveUpdateDelete",0,0,"reset_form('outfinishingbill_1','outside_yarn_service_info_list','','','enableDisableFields();$(\'#bill_issue_table tr:not(:first)\').remove();')",1);
                        ?> 
						
                    </td>
					<td>
						<input type="button" name="print" id="Printt1" value="Print" onClick="yarn_service_bill_print(1)" style="width: 80px;" class="formbutton">
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
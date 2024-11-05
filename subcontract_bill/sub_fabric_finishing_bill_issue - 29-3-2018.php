<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Sub-contract Dyeing & Finishing Bill Issue
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	02-07-2013
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
echo load_html_head_contents("Dyeing & Finishing Bill Issue", "../", 1,1, $unicode,1,'');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';
	var selected_id = new Array(); var selected_currency_id = new Array();
	var selected_id_listed = new Array();
	var selected_id_removed = new Array(); 

	function openmypage_bill()
	{ 
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_source').value+'_'+document.getElementById('cbo_party_name').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/sub_fabric_finishing_bill_issue_controller.php?data='+data+'&action=bill_no_popup','Bill Popup', 'width=780px,height=400px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("issue_id"); //Access form field with id="emailfield"
			if (theemail.value!="")
			{
				//freeze_window(5);
				get_php_form_data( theemail.value, "load_php_data_to_form_issue", "requires/sub_fabric_finishing_bill_issue_controller" );
				selected_id_listed = new Array();
				window_close( theemail.value );
				fnc_list_search(theemail.value);
				
				accounting_integration_check($('#hidden_acc_integ').val(),$('#hidden_integ_unlock').val());
				set_button_status(1, permission, 'fnc_dyeing_finishing_bill_issue',1);
				
				$('#tbl_list_search tr').each(function(index, element) {
					if( $('#'+this.id).attr('bgcolor')=='Yellow' )
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
				//alert(selected_id_listed)
				set_all_onclick();
				release_freezing();
			}
		}
	}
	
	var seq_arr=new Array(); var uom_arr = new Array();
	function window_close( uid )
	{
		var html="";
		var source=$('#cbo_party_source').val();
		var is_readonly=""; var isreadonly='';
		var grey_or_fin_qty=$('#variable_check').val()*1;
		
		if(source==1)
		{
			if(grey_or_fin_qty==1) is_readonly=""; else is_readonly="readonly";
		}
		else if(source==2)
		{
			is_readonly="readonly";
		}
		//alert(is_readonly);
		if(uid==0)
		{
			var list_tot_row=$('#tbl_list_search tr').length-1;
			//alert(list_tot_row)
			var i=0; if(seq_arr!=0) i=seq_arr; else i=i;
			
			for (var k=1; k<=list_tot_row; k++)
			{
				var check_id=2; var strval='';
				var check_id=$('#checkid'+k).val();
				var strval=$('#strid'+k).val();
				var split_str=""; var trid="";
				if(source==1)
				{
					split_str=strval.split("_");
					trid=split_str[2]+"_"+split_str[3]+"_"+split_str[12]+"_"+split_str[9]+"_"+split_str[11]+"_"+split_str[19]+"_"+split_str[14];
				}
				else 
				{
					split_str=strval.split("**");
					trid=trim(split_str[0]);//+"_"+split_str[3]+"_"+split_str[12]+"_"+split_str[9]+"_"+split_str[11]+"_"+split_str[19]+"_"+split_str[14];
				}
				
				if( check_id!=1) 
				{  
					$("#trent_"+trid).remove();
					for( var g = 0; g < selected_id_listed.length; g++ ) {
						
						if( selected_id_listed[g] == trid  ) break;
						//alert(selected_id_listed)
					}
					selected_id_listed.splice( g, 1 );
				}
				
				/*if( check_id!=1) 
				{  
					$("#trent_"+trid).remove();
					for( var q = 0; q < selected_id_listed.length; q++ ) {
						if( selected_id_listed[q] == trid  ) break;
					}
					selected_id_listed.splice( q, 1 );
				}*/
				//alert(selected_id_listed);
				if(check_id==1)
				{
					if(selected_id_listed.length==0)
					{
						$("#bill_issue_table tr").remove();
					}
					//alert(jQuery.inArray(  trid , selected_id_listed ))
					if( jQuery.inArray(  trid , selected_id_listed ) == -1) 
					{
						i++;
						selected_id_listed.push(trid);
					    //alert(selected_id_listed)
						
						var rec_id=split_str[0];
						var rec_date=split_str[1];
						var challan_no=split_str[2];
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
						
						var rec_qty=split_str[21];
						var colorProcess_id=split_str[29];
						//var uom=split_str[13];
						//var rate=split_str[15];
						//var amount=split_str[16];
						//var remarks=split_str[16];
						//alert(po_no);
						//<input type="hidden" name="curanci_'+i+'" id="curanci_'+i+'" style="width:50px" value="1" />colorProcess_id

						html+='<tr align="center" id="trent_'+trid+'"><td><input type="hidden" name="updateiddtls_'+i+'" id="updateiddtls_'+i+'" value=""><input type="hidden" name="deliveryid_'+i+'" id="deliveryid_'+i+'" value="'+rec_id+'"><input type="text" name="txtDeleverydate_'+i+'" id="txtDeleverydate_'+i+'" class="datepicker" style="width:60px" value="'+rec_date+'" disabled /></td><td><input type="text" name="txtChallenno_'+i+'" id="txtChallenno_'+i+'" class="text_boxes" style="width:45px" value="'+challan_no+'" readonly /></td><td><input type="hidden" name="ordernoid_'+i+'" id="ordernoid_'+i+'" value="'+po_id+'" style="width:40px" /><input type="text" name="txtOrderno_'+i+'" id="txtOrderno_'+i+'" class="text_boxes" style="width:65px" value="'+po_no+'" readonly /></td><td><input type="text" name="txtStylename_'+i+'" id="txtStylename_'+i+'" class="text_boxes" style="width:75px;" value="'+style_ref+'" readonly /></td><td><input type="text" name="txtBuyername_'+i+'" id="txtBuyername_'+i+'" class="text_boxes" style="width:65px" value="'+buyer+'" readonly /></td><td><input name="txtNumberroll_'+i+'" id="txtNumberroll_'+i+'" type="text" class="text_boxes" style="width:40px" value="'+roll_no+'" readonly /></td><td><input type="hidden" name="bodypartid_'+i+'" id="bodypartid_'+i+'" value="'+body_part_id+'"><input type="hidden" name="compoid_'+i+'" id="compoid_'+i+'" value="'+fab_des_id+'"><input type="hidden" name="itemid_'+i+'" id="itemid_'+i+'" value="'+prod_id+'"><input type="hidden" name="batchid_'+i+'" id="batchid_'+i+'" value="'+batch_id+'"><input type="text" name="textFebricdesc_'+i+'" id="textFebricdesc_'+i+'" class="text_boxes" style="width:120px" value="'+prod_name+'" readonly/></td><td><input type="hidden" name="colorProcess_'+i+'" id="colorProcess_'+i+'" value="'+colorProcess_id+'"><input type="hidden" name="colorId_'+i+'" id="colorId_'+i+'" value="'+color_id+'"><input type="text" name="txtColorProcess_'+i+'" id="txtColorProcess_'+i+'" class="text_boxes" style="width:80px" value="'+color_name+'" readonly/></td><td><input type="hidden" name="addProcess_'+i+'" id="addProcess_'+i+'" value="'+sub_process_id+'"><input type="text" name="txtAddProcess_'+i+'" id="txtAddProcess_'+i+'" class="text_boxes" style="width:100px" value="'+process_name+'" readonly /></td><td><input type="hidden" name="diaType_'+i+'" id="diaType_'+i+'" value="'+dia_width_id+'"><input type="text" name="txtDiaType_'+i+'" id="txtDiaType_'+i+'" class="text_boxes" style="width:55px" value="'+dia_width_name+'" readonly /></td><td><input type="text" name="txtDeliveryqnty_'+i+'" id="txtDeliveryqnty_'+i+'" onBlur="qnty_caluculation('+i+');" class="text_boxes_numeric" style="width:50px" value="'+rec_qty+'" '+is_readonly+' /></td><td><input type="text" name="txtRate_'+i+'" id="txtRate_'+i+'" onBlur="qnty_caluculation('+i+');"  class="text_boxes_numeric" style="width:40px" value="" placeholder="Browse" onDblClick="openmypage_rate('+i+')" /><input type="hidden" name="libRateId_'+i+'" id="libRateId_'+i+'" value=""></td><td><input type="text" name="txtAddRate_'+i+'" id="txtAddRate_'+i+'" onBlur="qnty_caluculation('+i+');" class="text_boxes_numeric" style="width:40px" value="" placeholder="Browse" onDblClick="openmypage_addRate('+i+')" /><input type="hidden" name="libAddRateId_'+i+'" id="libAddRateId_'+i+'" value=""></td><td><input type="text" name="txtAmount_'+i+'" id="txtAmount_'+i+'" style="width:55px" class="text_boxes_numeric" value="" readonly /></td><td><? echo create_drop_down( "curanci_'+i+'", 60, $currency,"", 1, "-Select Currency-",1,"",1,"" ); ?></td><td><input type="button" name="remarks_'+i+'" id="remarks_'+i+'" class="formbuttonplasminus" style="width:20px" value="R" onClick="openmypage_remarks('+i+');" /><input type="hidden" name="remarksvalue_'+i+'" id="remarksvalue_'+i+'" class="text_boxes" value="" /></td></tr>';
						//alert(i)
					}
				}
			}
			seq_arr=i;
		}
		else
		{
			$("#bill_issue_table tr").remove();
			var list_view_str = return_global_ajax_value( uid+"!^!"+source, 'load_dtls_data', '', 'requires/sub_fabric_finishing_bill_issue_controller');
			//alert(list_view_str)
			var split_list_view=list_view_str.split('###');
			var m=1; var mn=0;
			//
			for (var n=1; n<=split_list_view.length; n++)
			{
				var split_list_str=''; var trid='';
				if(source==1)
				{
					split_list_str=split_list_view[mn].split('_');
					trid=split_list_str[2]+"_"+split_list_str[3]+"_"+split_list_str[12]+"_"+split_list_str[9]+"_"+split_list_str[11]+"_"+split_list_str[19]+"_"+split_list_str[14];
				}
				else 
				{
					split_list_str=split_list_view[mn].split('**');
					trid=trim(split_list_str[0]);//+"_"+split_list_str[3]+"_"+split_list_str[12]+"_"+split_list_str[9]+"_"+split_list_str[11]+"_"+split_list_str[19]+"_"+split_list_str[14];
				}

				var rec_id=trim(split_list_str[0]);
				var rec_date=split_list_str[1];
				var challan_no=split_list_str[2];
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
				
				var lib_rate_id=split_list_str[22];
				var rate=split_list_str[23];
				var add_rate_id=split_list_str[24];
				var add_rate=split_list_str[25];
				var amount=split_list_str[26];
				var upd_id=split_list_str[27];
				var remarks=split_list_str[28];
				
				//listed_id[]=listed_id;
				//<input type="hidden" name="curanci_'+m+'" id="curanci_'+m+'" style="width:50px" value="1" />
				html+='<tr align="center" id="trent_'+trid+'"><td><input type="hidden" name="updateiddtls_'+m+'" id="updateiddtls_'+m+'" value="'+upd_id+'"><input type="hidden" name="deliveryid_'+m+'" id="deliveryid_'+m+'" value="'+rec_id+'"><input type="text" name="txtDeleverydate_'+m+'" id="txtDeleverydate_'+m+'" class="datepicker" style="width:60px" value="'+rec_date+'" disabled /></td><td><input type="text" name="txtChallenno_'+m+'" id="txtChallenno_'+m+'" class="text_boxes" style="width:45px" value="'+challan_no+'" readonly /></td><td><input type="hidden" name="ordernoid_'+m+'" id="ordernoid_'+m+'" value="'+po_id+'" style="width:40px" /><input type="text" name="txtOrderno_'+m+'" id="txtOrderno_'+m+'" class="text_boxes" style="width:65px" value="'+po_no+'" readonly /></td><td><input type="text" name="txtStylename_'+m+'" id="txtStylename_'+m+'" class="text_boxes" style="width:75px;" value="'+style_ref+'" readonly /></td><td><input type="text" name="txtBuyername_'+m+'" id="txtBuyername_'+m+'" class="text_boxes" style="width:65px" value="'+buyer+'" readonly /></td><td><input name="txtNumberroll_'+m+'" id="txtNumberroll_'+m+'" type="text" class="text_boxes" style="width:40px" value="'+roll_no+'" readonly /></td><td><input type="hidden" name="bodypartid_'+m+'" id="bodypartid_'+m+'" value="'+body_part_id+'"><input type="hidden" name="compoid_'+m+'" id="compoid_'+m+'" value="'+fab_des_id+'"><input type="hidden" name="itemid_'+m+'" id="itemid_'+m+'" value="'+prod_id+'"><input type="hidden" name="batchid_'+m+'" id="batchid_'+m+'" value="'+batch_id+'"><input type="text" name="textFebricdesc_'+m+'" id="textFebricdesc_'+m+'" class="text_boxes" style="width:120px" value="'+prod_name+'" readonly/></td><td><input type="hidden" name="colorProcess_'+m+'" id="colorProcess_'+m+'" value=""><input type="hidden" name="colorId_'+m+'" id="colorId_'+m+'" value="'+color_id+'"><input type="text" name="txtColorProcess_'+m+'" id="txtColorProcess_'+m+'" class="text_boxes" style="width:80px" value="'+color_name+'" readonly/></td><td><input type="hidden" name="addProcess_'+m+'" id="addProcess_'+m+'" value="'+sub_process_id+'"><input type="text" name="txtAddProcess_'+m+'" id="txtAddProcess_'+m+'" class="text_boxes" style="width:100px" value="'+process_name+'" readonly /></td><td><input type="hidden" name="diaType_'+m+'" id="diaType_'+m+'" value="'+dia_width_id+'"><input type="text" name="txtDiaType_'+m+'" id="txtDiaType_'+m+'" class="text_boxes" style="width:55px" value="'+dia_width_name+'" readonly /></td><td><input type="text" name="txtDeliveryqnty_'+m+'" id="txtDeliveryqnty_'+m+'" onBlur="qnty_caluculation('+m+');" class="text_boxes_numeric" style="width:50px" value="'+rec_qty+'" '+is_readonly+' /></td><td><input type="text" name="txtRate_'+m+'" id="txtRate_'+m+'" onBlur="qnty_caluculation('+m+');" class="text_boxes_numeric" style="width:40px" value="'+rate+'" placeholder="Browse" onDblClick="openmypage_rate('+m+')" /><input type="hidden" name="libRateId_'+m+'" id="libRateId_'+m+'" value="'+lib_rate_id+'"></td><td><input type="text" name="txtAddRate_'+m+'" id="txtAddRate_'+m+'" onBlur="qnty_caluculation('+m+');" class="text_boxes_numeric" style="width:40px" value="'+add_rate+'" placeholder="Browse" onDblClick="openmypage_addRate('+m+')" /><input type="hidden" name="libAddRateId_'+m+'" id="libAddRateId_'+m+'" value="'+add_rate_id+'"></td><td><input type="text" name="txtAmount_'+m+'" id="txtAmount_'+m+'" style="width:55px" class="text_boxes_numeric" value="'+amount+'" readonly /></td><td><? echo create_drop_down( "curanci_'+m+'", 60, $currency,"", 1, "-Select Currency-",1,"",1,"" ); ?></td><td><input type="button" name="remarks_'+m+'" id="remarks_'+m+'" class="formbuttonplasminus" style="width:20px" value="R" onClick="openmypage_remarks('+m+');" /><input type="hidden" name="remarksvalue_'+m+'" id="remarksvalue_'+m+'" class="text_boxes" value="'+remarks+'" /></td></tr>';
				//alert(html)
				mn++;
				m++;
			}
			seq_arr=m;
		}
		//alert(html)
		$("#bill_issue_table").append( html );
		
		var counter =$('#bill_issue_table tr').length; 
		for(var q=1; q<=counter; q++)
		{
			var index=q-1;
			$("#bill_issue_table tr:eq("+index+")").find("input").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ q },
				}); 
			})
			$('#txtDeliveryqnty_'+q).removeAttr("onBlur").attr("onBlur","qnty_caluculation("+q+");");
			$('#txtRate_'+q).removeAttr("onBlur").attr("onBlur","qnty_caluculation("+q+");");
			$('#txtRate_'+q).removeAttr("onDblClick").attr("onDblClick","openmypage_rate("+q+");");
			$('#txtAddRate_'+q).removeAttr("onBlur").attr("onBlur","qnty_caluculation("+q+");");
			$('#txtAddRate_'+q).removeAttr("onDblClick").attr("onDblClick","openmypage_addRate("+q+");");
		}
		
		var tot_row=$('#bill_issue_table tr').length;
		math_operation( "txt_tot_qnty", "txtDeliveryqnty_", "+", tot_row );
		math_operation( "txt_tot_amount", "txtAmount_", "+", tot_row );
		set_all_onclick();
	}
	
	function fnc_dyeing_finishing_bill_issue( operation )
	{
		
		if(operation==2)
		{
			if($('#hidden_acc_integ').val()==1)
			{
				show_msg('13');
				return;
			}
		}
		
		if ( form_validation('cbo_company_id*cbo_location_name*txt_bill_date*cbo_party_name*cbo_party_source','Company Name*Location*Bill Date*Party Name*Party Source')==false )
		{
			return;
		}
		else
		{
			var source=$('#cbo_party_source').val();
			var tot_row=$('#bill_issue_table tr').length;
			var bill_for=$('#cbo_bill_for').val();
			if(bill_for!=3)
			{
				if(source==1)
				{
					var orderIds=''; var bill_amount=0;
					for(var j=1; j<=tot_row; j++)
					{
						if(j>1)
						{
							orderIds +=",";
						}
						
						orderIds += $("#ordernoid_"+j).val();
						bill_amount+= $("#txtAmount_"+j).val()*1;
					}
					var bill_amount_status = return_ajax_request_value(orderIds+"_"+bill_amount+"_"+$('#update_id').val(), 'bill_amount_check', 'requires/sub_fabric_finishing_bill_issue_controller')
					var ex_bill_amount_status=bill_amount_status.split("_");
					if (ex_bill_amount_status[0]==1)
					{
						var prev_msg="";
						if(ex_bill_amount_status[3]<0)
						{
							prev_msg="Excess Bill Amount";
						}
						else
						{
							prev_msg="Availble Bill Amount";
						}
						alert(ex_bill_amount_status[4] +"\n "+"Total Budget Amount"+"="+number_format(ex_bill_amount_status[2],2,'.','' )+"\n Previous Bill Amount"+"="+number_format(ex_bill_amount_status[1],2,'.','' ) +"\n "+ prev_msg +"="+number_format(ex_bill_amount_status[3],2,'.','' ));
						
						
						//alert(ex_bill_amount_status[4] +"\n "+"Total Budget Amount"+"="+number_format(ex_bill_amount_status[2],2,'.','' )+"\n Previous Bill Amount"+"="+number_format(ex_bill_amount_status[1],2,'.','' ) +"\n Availble Bill Amount"+"="+number_format(ex_bill_amount_status[3],2,'.','' ));
						release_freezing();
						return;
					}
					//alert(orderIds);
				}
			}
			
			
			var data1="action=save_update_delete&operation="+operation+"&tot_row="+tot_row+get_submitted_data_string('txt_bill_no*cbo_company_id*cbo_location_name*txt_bill_date*cbo_party_name*cbo_party_source*cbo_bill_for*update_id',"../");
			var data2='';
			for(var i=1; i<=tot_row; i++)
			{
				if ( form_validation('txtChallenno_'+i+'*txtDeliveryqnty_'+i+'*txtRate_'+i,'Challen No*Delivery Qty*Rate')==false )
				{
					return;
				}
				else
				{
					data2+=get_submitted_data_string('txtDeleverydate_'+i+'*txtChallenno_'+i+'*ordernoid_'+i+'*colorProcess_'+i+'*colorId_'+i+'*addProcess_'+i+'*itemid_'+i+'*compoid_'+i+'*bodypartid_'+i+'*txtNumberroll_'+i+'*txtAddProcess_'+i+'*diaType_'+i+'*batchid_'+i+'*txtDeliveryqnty_'+i+'*libRateId_'+i+'*txtRate_'+i+'*libAddRateId_'+i+'*txtAddRate_'+i+'*txtAmount_'+i+'*remarksvalue_'+i+'*txtStylename_'+i+'*txtBuyername_'+i+'*deliveryid_'+i+'*curanci_'+i+'*updateiddtls_'+i,"../");
				}
			}
			var data=data1+data2;
			//alert (data);return;
			freeze_window(operation);
			http.open("POST","requires/sub_fabric_finishing_bill_issue_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_dyeing_finishing_bill_issue_reponse;
		}
	}

	function fnc_dyeing_finishing_bill_issue_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert (http.responseText);
			var response=trim(http.responseText).split('**');
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
				window_close(response[1]);
				set_button_status(1, permission, 'fnc_dyeing_finishing_bill_issue',1);
			}
			release_freezing();
		}
	}

	var selected_id = new Array(); var selected_currency_id = new Array();

	function toggle( x, origColor ) {
		//alert (x);
		var newColor = 'yellow';
		if ( x.style ) {
		x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}

	function js_set_value(id)
	{
		//alert (id);
		var str=id.split("***");
		if( jQuery.inArray( str[1], selected_currency_id ) != -1  || selected_currency_id.length<1 )
		{
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			if( jQuery.inArray(  str[0] , selected_id ) == -1) {
				
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
	
	function open_terms_condition_popup(page_link,title)
	{
		var txt_bill_no=document.getElementById('txt_bill_no').value;
		if (txt_bill_no=="")
		{
			alert("Save The Finishing Bill First.");
			return;
		}	
		else
		{
			page_link=page_link+get_submitted_data_string('txt_bill_no','../');
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=370px,center=1,resize=1,scrolling=0','')
			emailwindow.onclose=function(){};
		}
	}
	
	function qnty_caluculation(id)
	{
		var rate=($("#txtRate_"+id).val()*1)+($("#txtAddRate_"+id).val()*1);
		
		$("#txtAmount_"+id).val(($("#txtDeliveryqnty_"+id).val()*1)*rate);
		var tot_row=$('#bill_issue_table tr').length;
		math_operation( "txt_tot_qnty", "txtDeliveryqnty_", "+", tot_row );
		math_operation( "txt_tot_amount", "txtAmount_", "+", tot_row );
	}
	
	function generate_report(type)
	{
		if ( $('#txt_bill_no').val()=='')
		{
			alert ('Bill Not Save.');
			return;
		}
		else
		{		
			var report_title=$( "div.form_caption" ).html();
			//print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_bill_no').val()+'*'+report_title+'*'+type, "fabric_finishing_print", "requires/sub_fabric_finishing_bill_issue_controller") 
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_bill_no').val()+'*'+report_title+'*'+type, "fabric_finishing_print", "requires/sub_fabric_finishing_bill_issue_controller");
			//return;
			show_msg("3");
		}
	}
	
	function generate_report_file(data,action,page)
	{
		window.open("requires/sub_fabric_finishing_bill_issue_controller.php?data=" + data+'&action='+action, true );
	}
	
	function openmypage_remarks(id)
	{
		var data=document.getElementById('remarksvalue_'+id).value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/sub_fabric_finishing_bill_issue_controller.php?data='+data+'&action=remarks_popup','Remarks Popup', 'width=420px,height=320px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("text_new_remarks");//Access form field with id="emailfield"
			if (theemail.value!="")
			{
				$('#remarksvalue_'+id).val(theemail.value);
			}
		}
	}
	
	function accounting_integration_check(val,unlock)
	{
		var tot_row=$('#bill_issue_table tr').length;
		//alert (val);
		if(val==1 && unlock==0)
		{
			$('#cbo_company_id').attr('disabled','disabled');
			$('#cbo_location_name').attr('disabled','disabled');
			$('#txt_bill_date').attr('disabled','disabled');
			$('#cbo_party_source').attr('disabled','disabled');
			$('#cbo_party_name').attr('disabled','disabled');
			$('#cbo_bill_for').attr('disabled','disabled');
			for(var i=1; i<=tot_row; i++)
			{
				$('#txtNumberroll_'+i).attr('disabled','disabled');
				$('#txtDeliveryqnty_'+i).attr('disabled','disabled');
				$('#txtRate_'+i).attr('disabled','disabled');
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
				$('#txtNumberroll_'+i).removeAttr('disabled','disabled');
				$('#txtDeliveryqnty_'+i).removeAttr('disabled','disabled');
				$('#txtRate_'+i).removeAttr('disabled','disabled');
			}
		}
	}
	
	function fnc_bill_for(val)
	{
		if(val==1)
		{
			$('#cbo_bill_for').removeAttr('disabled','disabled');
			$('#txt_bill_form_date').removeAttr('disabled','disabled');
			$('#txt_bill_to_date').removeAttr('disabled','disabled');
			$('#txt_manual_challan').removeAttr('disabled','disabled');
		}
		else
		{
			$('#cbo_bill_for').attr('disabled','disabled');
			$('#txt_bill_form_date').attr('disabled','disabled');
			$('#txt_bill_to_date').attr('disabled','disabled');
			$('#txt_manual_challan').attr('disabled','disabled');
		}
	}
	
	function openmypage_rate(row_no)
	{
		var data=document.getElementById('cbo_company_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/sub_fabric_finishing_bill_issue_controller.php?data='+data+'&action=dyeing_rate_popup','Dyeing Rate Popup', 'width=780px,height=400px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("hddn_all_data");//Access form field with id="emailfield"
			if (theemail.value!="")
			{
				var pop_data=trim(theemail.value).split('***');
				$('#libRateId_'+row_no).val(pop_data[0]);
				$('#txtRate_'+row_no).val(pop_data[1]);
				qnty_caluculation(row_no);
			}
		}
	}
	
	function openmypage_addRate(row_no)
	{
		var data=document.getElementById('cbo_company_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/sub_fabric_finishing_bill_issue_controller.php?data='+data+'&action=dyeing_addRate_popup','Dyeing Additional Rate Popup', 'width=780px,height=400px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("hddn_all_data");//Access form field with id="emailfield"
			if (theemail.value!="")
			{
				var pop_data=trim(theemail.value).split('***');
				$('#libAddRateId_'+row_no).val(pop_data[0]);
				$('#txtAddRate_'+row_no).val(pop_data[1]);
				qnty_caluculation(row_no);
			}
		}
	}
	
	function fnc_check(inc_id)
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
	
	function fnc_list_search(type)
	{
		if($('#cbo_party_source').val()==1)
		{
			if( form_validation('cbo_company_id*cbo_location_name*cbo_party_source*cbo_party_name','Company Name*Location*Party Name*Party Source')==false)
			{
				return;
			}
			
			if( form_validation('txt_bill_form_date*txt_bill_to_date','From Date*To Date')==false)
			{
				if( form_validation('txt_manual_challan','Manual Challan')==false)
				{
					return;
				}
			}
		}
		
		$('#cbo_company_id').attr('disabled','disabled');
		$('#cbo_location_name').attr('disabled','disabled');
		$('#cbo_party_source').attr('disabled','disabled');
		$('#cbo_party_name').attr('disabled','disabled');
		$('#cbo_bill_for').attr('disabled','disabled');
			
		if($('#cbo_party_source').val()==1)
		{
			$('#txt_bill_form_date').removeAttr('disabled','disabled');
			$('#txt_bill_to_date').removeAttr('disabled','disabled');
			$('#txt_manual_challan').removeAttr('disabled','disabled');
		}
		
		if (type==0 && ($('#update_id').val()*1)==0)
		{
			show_list_view(document.getElementById('cbo_company_id').value+'***'+document.getElementById('cbo_location_name').value+'***'+document.getElementById('cbo_party_source').value+'***'+document.getElementById('cbo_party_name').value+'***'+document.getElementById('cbo_bill_for').value+'***'+document.getElementById('txt_bill_form_date').value+'***'+document.getElementById('txt_bill_to_date').value+'***'+document.getElementById('txt_manual_challan').value+'***'+$('#variable_check').val(),'dyingfinishing_delivery_list_view','dyeingfinishing_info_list','requires/sub_fabric_finishing_bill_issue_controller', 'setFilterGrid("tbl_list_search",-1)','','');
		}
		else
		{
			var tot_row=$('#bill_issue_table tr').length;
				//alert(tot_row)
				
			var all_value="";
			if($('#cbo_party_source').val()==1)
			{
				for (var n=1; n<=tot_row; n++)
				{
					if(all_value=="") all_value+=$('#txtChallenno_'+n).val()+'_'+$('#ordernoid_'+n).val()+'_'+$('#itemid_'+n).val()+'_'+$('#bodypartid_'+n).val()+'_'+$('#compoid_'+n).val()+'_'+$('#diaType_'+n).val()+'_'+$('#batchid_'+n).val(); 
					
					else all_value+='!!!!'+$('#txtChallenno_'+n).val()+'_'+$('#ordernoid_'+n).val()+'_'+$('#itemid_'+n).val()+'_'+$('#bodypartid_'+n).val()+'_'+$('#compoid_'+n).val()+'_'+$('#diaType_'+n).val()+'_'+$('#batchid_'+n).val();
				}
			}
			else
			{
				for (var n=1; n<=tot_row; n++)
				{
					if(all_value=="") all_value+=$('#deliveryid_'+n).val(); else all_value+='!!!!'+$('#deliveryid_'+n).val();
				}
			}
			//alert(all_value);
			show_list_view(document.getElementById('cbo_company_id').value+'***'+document.getElementById('cbo_location_name').value+'***'+document.getElementById('cbo_party_source').value+'***'+document.getElementById('cbo_party_name').value+'***'+document.getElementById('cbo_bill_for').value+'***'+document.getElementById('txt_bill_form_date').value+'***'+document.getElementById('txt_bill_to_date').value+'***'+document.getElementById('txt_manual_challan').value+'***'+$('#variable_check').val()+'***'+type+'***'+all_value,'dyingfinishing_delivery_list_view','dyeingfinishing_info_list','requires/sub_fabric_finishing_bill_issue_controller','setFilterGrid("tbl_list_search",-1)','','');
		}
	}
	
</script>
</head>
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
    <? echo load_freeze_divs ("../",$permission);  ?>
    <form name="dyinfinishbillissue_1" id="dyinfinishbillissue_1"  autocomplete="off"  >
    <fieldset style="width:1000px;">
    <legend>Dyeing & Finishing Bill Info </legend>
        <table width="1000"  cellspacing="1" cellpadding="0" border="0" >
            <tr>
                <td width="800">
                <fieldset>
                    <table cellpadding="0" cellspacing="2" width="100%">
                        <tr>
                            <td align="right" colspan="3"><strong>Bill No </strong></td>
                            <td width="140" align="justify">
                            	<input type="hidden" name="hidden_acc_integ" id="hidden_acc_integ" />
                                <input type="hidden" name="hidden_integ_unlock" id="hidden_integ_unlock" />
                                <input type="hidden" name="selected_order_id" id="selected_order_id" />
                                <input type="hidden" name="selected_currency_no" id="selected_currency_no" />
                                <input type="hidden" name="update_id" id="update_id" />
                                <input type="hidden" name="variable_check" id="variable_check" />
                                <input type="text" name="txt_bill_no" id="txt_bill_no" class="text_boxes" style="width:140px" placeholder="Double Click to Search" onDblClick="openmypage_bill();" readonly tabindex="1" >
                            </td>
                         </tr>
                         <tr>
                            <td width="110" class="must_entry_caption">Company</td>
                            <td width="150">
                                <? 
                                    echo create_drop_down( "cbo_company_id",150,"select id, company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/sub_fabric_finishing_bill_issue_controller', this.value, 'load_drop_down_location', 'location_td'); get_php_form_data(this.value,'load_variable_settings','requires/sub_fabric_finishing_bill_issue_controller');","","","","","",2);// fnc_qty_condition(document.getElementById('variable_check').value);
                                ?>
                            </td>
                            <td width="110" class="must_entry_caption">Location</td>                                              
                            <td width="150" id="location_td">
                                <? 
                                    echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "--Select Location--", $selected,"","","","","","",3);
                                ?>
                            </td>
                            <td width="110" class="must_entry_caption">Bill Date</td>                                              
                            <td width="150">
                                <input class="datepicker" type="text" style="width:140px" name="txt_bill_date" id="txt_bill_date" tabindex="4" value="<? echo date('d-m-Y'); ?>"/>
                            </td>
                        </tr> 
                        <tr>
                            <td class="must_entry_caption">Party Source</td>
                            <td><? echo create_drop_down( "cbo_party_source", 150, $knitting_source,"", 1, "-- Select Party --", $selected, "load_drop_down( 'requires/sub_fabric_finishing_bill_issue_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_party_name', 'party_td' );fnc_bill_for(this.value)",0,"1,2","","","",4); ?></td>
							<td>Bill For</td>
                            <td><? echo create_drop_down( "cbo_bill_for", 150, $bill_for,"", 0, "-- Bill Type --", 1, "",1,"","","","",7); ?></td>                            
                            <td class="must_entry_caption">Party Name</td>
                            <td id="party_td"><? echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"","","","",5); ?></td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Trns. Date Range</td>                                              
                            <td><input class="datepicker" type="text" style="width:55px" name="txt_bill_form_date" id="txt_bill_form_date" placeholder="Form Date" disabled />&nbsp;<input class="datepicker" type="text" style="width:55px" name="txt_bill_to_date" id="txt_bill_to_date" placeholder="To Date" disabled />
                            </td>
                            <td class="must_entry_caption">Manual Challan No</td>                                              
                            <td><input class="text_boxes" type="text" style="width:130px" name="txt_manual_challan" id="txt_manual_challan" disabled /></td>
                            <td>&nbsp;</td>                                              
                            <td><input class="formbutton" type="button" onClick="fnc_list_search(0);" style="width:130px" name="btn_populate" value="Populate" id="btn_populate" /></td>
                        </tr>
                    </table>
                </fieldset>
                </td>
            </tr>
            <tr>
                <td align="center"> 
                </td> 
            </tr>   
        </table>
        </fieldset>
        <br>
        <fieldset style="width:900px;">
    	<legend>Dyeing & Finishing Bill Info </legend>
        <table  style="border:none; width:900px;" cellpadding="0" cellspacing="1" border="0" id="">
            <thead class="form_table_header">
                <th width="65">Delivery Date </th>
                <th width="50" class="must_entry_caption">Sys. Challan</th>
                <th width="70">Order </th>
                <th width="80">Style</th>
                <th width="70">Buyer</th>
                <th width="40">No. Roll</th>
                <th width="120">Fabric Des.</th>
                <th width="80">Color/Process</th>
                <th width="100">Additional Process</th>
                <th width="60">D/W Type</th>
                <th width="50" class="must_entry_caption">Qty(Kg)</th>
                <th width="40" class="must_entry_caption">Rate (Main)</th>
                <th width="40" class="must_entry_caption">Rate (Add.)</th>
                <th width="50">Amount</th>
                <th width="60">Currency</th>
                <th>RMK</th>
            </thead>
            <tbody id="bill_issue_table">
                <tr align="center">				
                    <td>
                    	<input type="hidden" name="issue_id_all" id="issue_id_all"  style="width:50px" />
                        <input type="hidden" name="updateiddtls_1" id="updateiddtls_1" style="width:50px">
                        <input type="text" name="txt_deleverydate_1" id="txt_deleverydate_1"  class="datepicker" style="width:60px" readonly />									
                    </td>
                    <td>
                        <input type="text" name="txt_challenno_1" id="txt_challenno_1"  class="text_boxes" style="width:45px" readonly />							 
                    </td>
                    <td>
                        <input type="hidden" name="ordernoid_1" id="ordernoid_1" value="" style="width:50px">
                        <input type="text" name="txt_orderno_1" id="txt_orderno_1"  class="text_boxes" style="width:65px" readonly />										
                    </td>
                    <td>
                        <input type="text" name="txt_stylename_1" id="txt_stylename_1"  class="text_boxes" style="width:75px;" />
                    </td>
                    <td>
                        <input type="text" name="txt_buyername_1" id="txt_buyername_1"  class="text_boxes" style="width:65px" />								
                    </td>
                    <td>			
                        <input type="text" name="txt_numberroll_1" id="txt_numberroll_1" class="text_boxes" style="width:40px" readonly />							
                    </td>  
                    <td>
                        <input type="text" name="text_febricdesc_1" id="text_febricdesc_1"  class="text_boxes_numeric" style="width:120px" readonly/>
                    </td>
                    <td>
                        <input type="text" name="txt_color_process_1" id="txt_color_process_1"  class="text_boxes" style="width:80px" readonly/>
                    </td>
                    <td>
                        <input type="hidden" name="add_process_1" id="add_process_1" value="">
                        <input type="text" name="txt_add_process_1" id="txt_add_process_1" class="text_boxes" style="width:100px" readonly/>
                    </td>
                    <td>
                        <input type="hidden" name="diaType_1" id="diaType_1" value="">
                        <input type="text" name="txt_diaType_1" id="txt_diaType_1" class="text_boxes" style="width:55px" readonly/>
                    </td>
                    <td>
                        <input type="text" name="txt_deliveryqnty_1" id="txt_deliveryqnty_1"  class="text_boxes_numeric" style="width:50px" onBlur="qnty_caluculation(1);" />
                    </td>
                    <td>
                        <input type="text" name="txt_rate_1" id="txt_rate_1"  class="text_boxes_numeric" style="width:40px" onBlur="qnty_caluculation(1);" />
                        <input type="hidden" name="libRateId_1" id="libRateId_1" value="">
                    </td>
                    <td>
                        <input type="text" name="txt_addRate_1" id="txt_addRate_1"  class="text_boxes_numeric" style="width:40px" onBlur="qnty_caluculation(1);" />
                        <input type="hidden" name="libAddRateId_1" id="libAddRateId_1" value="">
                    </td>
                    <td>
                        <input type="text" name="txt_amount_1" id="txt_amount_1" class="text_boxes_numeric" style="width:55px" readonly />
                    </td>

                    <td>
                    	<? echo create_drop_down( "curanci_1", 60, $currency,"", 1, "-Currency-",1,"",1,"" ); ?>
                    </td>

                    <td>
                        <input type="button" name="remarks_1" id="remarks_1"  class="formbuttonplasminus" value="R" onClick="openmypage_remarks(1);" />
                        <input type="hidden" name="remarksvalue_1" id="remarksvalue_1" class="text_boxes" />
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                	<td width="65px">&nbsp;</td>
                    <td width="50px">&nbsp;</td>
                    <td width="70px">&nbsp;</td>
                    <td width="80px">&nbsp;</td>
                    <td width="70px">&nbsp;</td>
                    <td width="40px">&nbsp;</td>
                    <td width="120px"><input type="button" id="set_button" class="image_uploader" style="width:120px" value="Terms & Condition" onClick="open_terms_condition_popup('requires/sub_fabric_finishing_bill_issue_controller.php?action=terms_condition_popup','Terms Condition')" /></td>
                    <td width="80px">&nbsp;</td>
                    <td width="60px">&nbsp;</td>
                    <td width="100px" align="right">Total Qty:</td>
                    <td width="50px">
                    	<input type="text" name="txt_tot_qnty" id="txt_tot_qnty"  class="text_boxes_numeric" style="width:50px" disabled />
                    </td>
                    <td width="40px" align="right"></td>
                    <td width="40px" align="right">Total:</td>
                    <td width="55px">
                    	<input type="text" name="txt_tot_amount" id="txt_tot_amount"  class="text_boxes_numeric" style="width:55px" disabled/>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>                
                <tr>
                    <td colspan="15" height="15" align="center"><div id="bill_on" style="float:left; font-size:18px; color:#FF0000;"></div><div id="accounting_integration_div" style="float:center; font-size:18px; color:#FF0000;"></div></td>
                </tr>
                <tr>
                    <td colspan="15" align="center" class="button_container">
						<? 
							$date=date('d-m-Y');
							echo load_submit_buttons($permission,"fnc_dyeing_finishing_bill_issue",0,0,"reset_form('dyinfinishbillissue_1','dyeingfinishing_info_list','','txt_bill_date,".$date."','$(\'#bill_issue_table tr:not(:first)\').remove();')",1);
                        ?> 
                        <input type="button" name="search" id="search" value="Print" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                        <input type="button" name="search" id="search" value="Short Bill" onClick="generate_report(2)" style="width:100px" class="formbutton" />
                        
                         <input type="button" name="search" id="search" value="Print USD" onClick="generate_report(3)" style="width:70px" class="formbutton" />
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
        <div id="dyeingfinishing_info_list"></div>                           
   </div>
</body>
<script>
//$('#cbo_company_id').val(2);
//load_drop_down( 'requires/sub_fabric_finishing_bill_issue_controller', $('#cbo_company_id').val(), 'load_drop_down_location', 'location_td');
//load_drop_down( 'requires/sub_fabric_finishing_bill_issue_controller',document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_source').value, 'load_drop_down_party_name', 'party_td' );
</script>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
		
			
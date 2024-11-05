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
	var ddd={ dec_type:1, comma:0, currency:1};
	var selected_id = new Array(); var selected_currency_id = new Array();
	var selected_id_listed = new Array();
	var selected_id_removed = new Array(); 
	var exist_data = [] ;
	var str_atention = [<? echo substr(return_library_autocomplete( "select attention from subcon_inbound_bill_mst group by attention", "attention" ), 0, -1); ?>];

	$(document).ready(function(e)
	 {
            $("#txt_attention").autocomplete({
			 source: str_atention
		  });
     });

	function openmypage_bill()
	{ 
		if(form_validation('cbo_company_id','Company Name')==false){ return; }
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('cbo_party_source').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/knitting_bill_issue_controller.php?data='+data+'&action=bill_no_popup','Bill Popup', 'width=1010px,height=420px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("issue_id")
			if (theemail.value!="")
			{
				get_php_form_data( theemail.value, "load_php_data_to_form_issue", "requires/knitting_bill_issue_controller" );
				selected_id_listed = new Array();
				window_close(theemail.value);
				accounting_integration_check($('#hidden_acc_integ').val());
				fnc_list_search(theemail.value);
				
				set_button_status(1, permission, 'fnc_knitting_bill_issue',1);
				
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
	
	var seq_arr=new Array(); var uom_arr = new Array(); var currency_arr = new Array();
	
	function window_close( uid )
	{
		var html="";
		//$('#bill_issue_table tr').remove();
		//var tbllen= $('#bill_issue_table tr').length; 
		//alert(uid);
		var bill_source= $('#cbo_party_source').val(); 
		var hidd_rate_from=$('#hidd_rate_from').val()*1;
		var incid=0; var mainrate_variable_data="";
		if(uid==0) incid=i; else incid=m;
		if(uid==0)
		{
			var list_tot_row=$('#tbl_list_search tr').length-1;
			var counter =$('#bill_issue_table tr').length; 
			//alert(counter)
			var i=0; var p=0;
			
			if(seq_arr!=0) i=counter; else i=i;
			
			for (var k=1; k<=list_tot_row; k++)
			{
				var check_id=''; var strval=''; var trid="";
				var check_id= $('#checkid'+k).val()*1; 
				var strval=$('#strid'+k).val();
				//alert(strval);
				var split_str=strval.split("_");
				if(bill_source==1)
				{
					trid=split_str[2]+"_"+split_str[3]+"_"+split_str[12]+"_"+split_str[10]+"_"+split_str[8]+"_"+split_str[24]+"_"+split_str[20]+"_"+split_str[25]+"_"+split_str[28];
					//alert(trid);
				}
				else if(bill_source==2)
				{
					trid=split_str[0];
				}
			
				if( check_id!=1) 
				{  
					
					$("#trent_"+trid).remove();
					for( var g = 0; g < selected_id_listed.length; g++ ) {
						
							//alert(selected_id_listed[g]+'='+trid);
						if( selected_id_listed[g] == trid  ) break;
					}
					selected_id_listed.splice( g, 1 );
				}
				
				if(check_id==1)
				{
					if(selected_id_listed.length==0)
					{
						$("#bill_issue_table tr").remove();
					}
					//alert(check_id);
					if( jQuery.inArray(  trid , selected_id_listed ) == -1) 
					{
						i++;
						
						if(hidd_rate_from==3)
						{
							var mainrate_variable_data='placeholder="Browse" readonly';//onDblClick="openmypage_rate('+i+');" readonly';
						}
						else
						{
							var mainrate_variable_data='placeholder="Write"';
						}

						selected_id_listed.push( trid );
						//alert(selected_id_listed)
						var rec_id=trim(split_str[0]);
						var rec_date=split_str[1];
						var challan_no=split_str[2];
						var po_id=split_str[3];
						var po_no=split_str[4];
						var style_ref=split_str[5];
						var buyer=split_str[6];
						var roll_no=split_str[7];
						var fab_des_id=split_str[8];
						var body_part_id=split_str[10];
						var body_part_name=split_str[11];
						var prod_id=split_str[12];
						var prod_name=split_str[13];
						var rec_qty=split_str[14];
						//var uom=split_str[13];
						var rate=split_str[15];
						var amount=split_str[16];
						var order_uom=split_str[17]*1;
						var delivery_qty_pcs=split_str[18];
						var subprocess_uom=split_str[19];
						var coller_cuff_measurement=split_str[20];
						var currency_id=split_str[21];
						var body_part_type=split_str[22];
						var rate_from=split_str[23];
						
						var rec_challan=split_str[24];
						var internal_ref=split_str[26];
						var is_sales=split_str[27];
						var yarn_count=split_str[28];
						var mcdia=split_str[29];
						//alert(rec_challan);
						if(yarn_count=='0') yarn_count="";
						if(mcdia=='0') mcdia="";
				
						delivery_qty_pcs=sub_delivery_qty_pcs='';
						if(bill_source==1)
						{
						var delivery_qty_pcs=split_str[23];
						}
						var sub_delivery_qty_pcs=split_str[18];
						//	alert(hidd_rate_from+'='+sub_delivery_qty_pcs+'='+delivery_qty_pcs);
						if(delivery_qty_pcs=="" || delivery_qty_pcs==0)
						{
							delivery_qty_pcs=sub_delivery_qty_pcs;
						}
						
						if(hidd_rate_from==4)//SubCon Order
						{
							rate=0;
							rate=rate_from;
							delivery_qty_pcs='';
						}
						var is_disable=""; var selected_uom=order_uom; var selected_currency=currency_id;
						
						if(bill_source==2)
						{
							selected_uom = subprocess_uom;
							is_disable="";
							//body_part_name="";
							//body_part_id="";
							
							if (body_part_type==40 || body_part_type==50)
							{
								is_disable="";
								//delivery_qty_pcs="";
								//coller_cuff_measurement="";
							}
							else
							{
								is_disable="disabled";
								//delivery_qty_pcs="";
								coller_cuff_measurement="";
							}
						}
						else
						{
							if (body_part_type==40 || body_part_type==50)
							{
								if(order_uom==0) selected_uom=1;
								//is_disable="";
								is_disable="disabled";
								//delivery_qty_pcs="";
								//coller_cuff_measurement="";
							}
							else
							{
								if(order_uom== 0) selected_uom=12;
								
								is_disable="disabled";
								//delivery_qty_pcs="";
								coller_cuff_measurement="";
							}
						}
						//alert(selected_uom);yarncount_

						//var remarks=split_str[16];
						//alert(selected_uom+'-'+seq_arr);  
						//<input type="hidden" name="curanci_'+i+'" id="curanci_'+i+'"  style="width:80px" value="1" />internalref_1

						html+='<tr align="center" id="trent_'+trid+'"><td><input type="hidden" name="updateiddtls_'+i+'" id="updateiddtls_'+i+'" value=""><input type="hidden" name="deliveryid_'+i+'" id="deliveryid_'+i+'" style="width:45px" value="'+rec_id+'"><input type="hidden" name="recchallanno_'+i+'" id="recchallanno_'+i+'" style="width:45px" value="'+rec_challan+'"><input type="text" name="deleverydate_'+i+'" id="deleverydate_'+i+'"  class="datepicker" style="width:60px" value="'+rec_date+'" disabled/></td><td><input type="text" name="challenno_'+i+'" id="challenno_'+i+'"  class="text_boxes" style="width:40px" value="'+challan_no+'" readonly /></td><td><input type="hidden" name="ordernoid_'+i+'" id="ordernoid_'+i+'" value="'+po_id+'"><input type="text" name="orderno_'+i+'" id="orderno_'+i+'" class="text_boxes" style="width:70px" value="'+po_no+'" readonly /></td><td><input type="text" name="internalref_'+i+'" id="internalref_'+i+'"  class="text_boxes" style="width:80px;" value="'+internal_ref+'" readonly /></td><td><input type="text" name="stylename_'+i+'" id="stylename_'+i+'"  class="text_boxes" style="width:80px;" value="'+style_ref+'" readonly /></td><td><input type="text" name="buyername_'+i+'" id="buyername_'+i+'"  class="text_boxes" style="width:70px" value="'+buyer+'" readonly /></td><td><input name="numberroll_'+i+'" id="numberroll_'+i+'" type="text" class="text_boxes_numeric" style="width:40px" value="'+roll_no+'" /></td><td style="display:none"><input type="hidden" name="compoid_'+i+'" id="compoid_'+i+'" value="'+fab_des_id+'"><input type="text" name="yarndesc_'+i+'" id="yarndesc_'+i+'"  class="text_boxes" style="width:115px" value="" readonly/></td><td><input type="hidden" name="bodypartid_'+i+'" id="bodypartid_'+i+'" value="'+body_part_id+'"><input type="text" name="bodypartdesc_'+i+'" id="bodypartdesc_'+i+'" bodyparttype="'+body_part_type+'" class="text_boxes" style="width:80px" value="'+body_part_name+'" readonly/></td><td><input type="hidden" name="itemid_'+i+'" id="itemid_'+i+'" value="'+prod_id+'"><input type="text" name="febricdesc_'+i+'" id="febricdesc_'+i+'"  class="text_boxes" style="width:135px" title="'+prod_name+'" value="'+prod_name+'" readonly/></td><td><input type="text" name="yarncount_'+i+'" id="yarncount_'+i+'"  class="text_boxes" style="width:70px"   value="'+yarn_count+'" readonly/></td><td><input type="text" name="mcdia_'+i+'" id="mcdia_'+i+'"  class="text_boxes" style="width:70px"  value="'+mcdia+'" readonly/></td><td><select name="cbouom_'+i+'" id="cbouom_'+i+'" class="text_boxes"><option value="0">-UOM-</option><option value="1">Pcs</option><option value="2">Dzn</option><option value="12">Kg</option><option value="27">Yds</option></select><td><select name="curanci_'+i+'" id="curanci_'+i+'" class="text_boxes" style="width:60px"><option value="0">-Select Currency-</option><option value="1" selected>Taka</option><option value="2">USD</option><option value="3">EURO</option><option value="4">CHF</option><option value="5">SGD</option><option value="6">Pound</option><option value="7">YEN</option></select></td><td><input type="text" name="collarcuff_'+i+'" id="collarcuff_'+i+'" class="text_boxes" style="width:65px" value="'+coller_cuff_measurement+'" '+is_disable+' /></td><td><input type="text" name="deliveryqnty_'+i+'" id="deliveryqnty_'+i+'"  class="text_boxes_numeric" style="width:40px" value="'+rec_qty+'" onBlur="qnty_caluculation('+i+');" readonly /></td><td><input type="text" name="deliveryqntypcs_'+i+'" id="deliveryqntypcs_'+i+'" class="text_boxes_numeric" style="width:40px" value="'+delivery_qty_pcs+'" onBlur="qnty_caluculation('+i+');" '+is_disable+' /></td><td><input type="text" name="txtrate_'+i+'" id="txtrate_'+i+'"  class="text_boxes_numeric" style="width:40px" value="'+rate+'" onBlur="qnty_caluculation('+i+'); fnc_rate_copy('+i+');" '+mainrate_variable_data+' /><input type="hidden" name="libRateId_'+i+'" id="libRateId_'+i+'" value=""></td><td><input type="text" name="amount_'+i+'" id="amount_'+i+'" style="width:40px"  class="text_boxes_numeric" value="'+amount+'" readonly /></td><td><input type="button" name="remarks_'+i+'" id="remarks_'+i+'"  class="formbuttonplasminus" style="width:20px" value="R" onClick="openmypage_remarks('+i+');" /><input type="hidden" name="remarksvalue_'+i+'" id="remarksvalue_'+i+'" class="text_boxes" value="" /></td></tr>';
						//alert(trid)
					
						// uom_arr[i]=selected_uom;
						uom_arr[i]=selected_uom;
						currency_arr[i]=selected_currency;
						p++;
					}
				}
			}
			seq_arr=p;
		}
		else //+$('#cbo_bill_for').val()
		{
			$("#bill_issue_table tr").remove();
			var list_view_str = return_global_ajax_value( uid+"!^!"+bill_source+"!^!"+$('#cbo_bill_for').val(), 'load_dtls_data', '', 'requires/knitting_bill_issue_controller');
			var split_list_view=list_view_str.split('###');
			var m=1; var mn=0;
			for (var n=1; n<=split_list_view.length; n++)
			{
				if(hidd_rate_from==3)
				{
					var mainrate_variable_data='placeholder="Browse" readonly';// onDblClick="openmypage_rate('+m+');" readonly';
				}
				else
				{
					var mainrate_variable_data='placeholder="Write"';
				}
				var split_list_str=split_list_view[mn].split('_');
				var trid="";
				if(bill_source==1)
				{
					trid=split_list_str[2]+"_"+split_list_str[3]+"_"+split_list_str[12]+"_"+split_list_str[10]+"_"+split_list_str[8]+"_"+split_list_str[25];
					//alert(trid+'='+split_list_str[25]);
				}
				else if(bill_source==2)
				{
					trid=split_list_str[0];
				}
				//alert(trid+'='+2);
				var rec_id=trim(split_list_str[0]);
				var rec_date=split_list_str[1];
				var challan_no=split_list_str[2];
				var po_id=split_list_str[3];
				var po_no=split_list_str[4];
				var style_ref=split_list_str[5];
				var buyer=split_list_str[6];
				var roll_no=split_list_str[7];
				var fab_des_id=split_list_str[8];
				var body_part_id=split_list_str[10];
				var body_part_name=split_list_str[11];
				var prod_id=split_list_str[12];
				var prod_name=split_list_str[13];
				var rec_qty=split_list_str[14];
				var rec_qty_pcs=split_list_str[15];
				var rate=split_list_str[16];
				var amount=split_list_str[17];
				var lib_rate_id=split_list_str[18];
				var upd_id=split_list_str[19];
				var remarks=split_list_str[20];
				var uom=split_list_str[21];
				var coller_cuff_measurement=split_list_str[22];
				var currency_id=split_list_str[23];
				var body_part_type=split_list_str[24];
				var rec_challan=split_list_str[25];
				var internal_ref=split_list_str[26];
				var is_sales=split_list_str[27];
				
				var yarn_count=split_list_str[28];
				var mcdia=split_list_str[29];
				if(mcdia=='0') mcdia="";
				if(yarn_count=='0') yarn_count="";
				//alert(rec_challan);
				
				var is_disable="";
				if (body_part_type==40 || body_part_type==50)
				{
					if(uom==0) uom=1;
					//is_disable="";
					is_disable="disabled";
				}
				else
				{
					if(uom== 0) uom=12;
					is_disable="disabled";
				}
				//listed_id[]=listed_id;internalref_1
				html+='<tr align="center" id="trent_'+trid+'"><td><input type="hidden" name="updateiddtls_'+m+'" id="updateiddtls_'+m+'" value="'+upd_id+'"><input type="hidden" name="deliveryid_'+m+'" id="deliveryid_'+m+'" style="width:45px" value="'+rec_id+'"><input type="hidden" name="recchallanno_'+m+'" id="recchallanno_'+m+'" style="width:45px" value="'+rec_challan+'"><input type="text" name="deleverydate_'+m+'" id="deleverydate_'+m+'"  class="datepicker" style="width:60px" value="'+rec_date+'" disabled/></td><td><input type="text" name="challenno_'+m+'" id="challenno_'+m+'"  class="text_boxes" style="width:40px" value="'+challan_no+'" readonly /></td><td><input type="hidden" name="ordernoid_'+m+'" id="ordernoid_'+m+'" value="'+po_id+'"><input type="text" name="orderno_'+m+'" id="orderno_'+m+'" class="text_boxes" style="width:70px" value="'+po_no+'" readonly /></td><td><input type="text" name="internalref_'+m+'" id="internalref_'+m+'"  class="text_boxes" style="width:80px;" value="'+internal_ref+'" readonly /></td><td><input type="text" name="stylename_'+m+'" id="stylename_'+m+'"  class="text_boxes" style="width:80px;" value="'+style_ref+'" readonly /></td><td><input type="text" name="buyername_'+m+'" id="buyername_'+m+'"  class="text_boxes" style="width:70px" value="'+buyer+'" readonly /></td><td><input name="numberroll_'+m+'" id="numberroll_'+m+'" type="text" class="text_boxes_numeric" style="width:40px" value="'+roll_no+'" /></td><td style="display:none"><input type="hidden" name="compoid_'+m+'" id="compoid_'+m+'" value="'+fab_des_id+'"><input type="text" name="yarndesc_'+m+'" id="yarndesc_'+m+'"  class="text_boxes" style="width:115px" value="" readonly/></td><td><input type="hidden" name="bodypartid_'+m+'" id="bodypartid_'+m+'" value="'+body_part_id+'"><input type="text" name="bodypartdesc_'+m+'" id="bodypartdesc_'+m+'" bodyparttype="'+body_part_type+'" class="text_boxes" style="width:80px" value="'+body_part_name+'" readonly/></td><td><input type="hidden" name="itemid_'+m+'" id="itemid_'+m+'" value="'+prod_id+'"><input type="text" name="febricdesc_'+m+'" id="febricdesc_'+m+'"  class="text_boxes" style="width:135px" title="'+prod_name+'" value="'+prod_name+'" readonly/></td><td><input type="text" name="yarncount_'+m+'" id="yarncount_'+m+'"  class="text_boxes" style="width:70px"   value="'+yarn_count+'" readonly/></td><td><input type="text" name="mcdia_'+m+'" id="mcdia_'+m+'"  class="text_boxes" style="width:70px"  value="'+mcdia+'" readonly/></td><td><select name="cbouom_'+m+'" id="cbouom_'+m+'" class="text_boxes"><option value="0">-UOM-</option><option value="1">Pcs</option><option value="2">Dzn</option><option value="12">Kg</option><option value="27">Yds</option></select></td><td><select name="curanci_'+m+'" id="curanci_'+m+'" class="text_boxes" style="width:60px"><option value="0">-Select Currency-</option><option value="1">Taka</option><option value="2">USD</option><option value="3">EURO</option><option value="4">CHF</option><option value="5">SGD</option><option value="6">Pound</option><option value="7">YEN</option></select></td><td><input type="text" name="collarcuff_'+m+'" id="collarcuff_'+m+'" class="text_boxes" style="width:65px" value="'+coller_cuff_measurement+'" '+is_disable+' /></td><td><input type="text" name="deliveryqnty_'+m+'" id="deliveryqnty_'+m+'" class="text_boxes_numeric" style="width:40px" value="'+rec_qty+'" onBlur="qnty_caluculation('+m+');" readonly /></td><td><input type="text" name="deliveryqntypcs_'+m+'" id="deliveryqntypcs_'+m+'" class="text_boxes_numeric" style="width:40px" value="'+rec_qty_pcs+'" onBlur="qnty_caluculation('+m+');" '+is_disable+' /></td><td><input type="text" name="txtrate_'+m+'" id="txtrate_'+m+'"  class="text_boxes_numeric" style="width:40px" value="'+rate+'" onBlur="qnty_caluculation('+m+'); fnc_rate_copy('+m+');" '+mainrate_variable_data+' /><input type="hidden" name="libRateId_'+m+'" id="libRateId_'+m+'" value="'+lib_rate_id+'"></td><td><input type="text" name="amount_'+m+'" id="amount_'+m+'" style="width:40px" class="text_boxes_numeric" value="'+amount+'" readonly /></td><td><input type="button" name="remarks_'+m+'" id="remarks_'+m+'" class="formbuttonplasminus" style="width:20px" value="R" onClick="openmypage_remarks('+m+');" /><input type="hidden" name="remarksvalue_'+m+'" id="remarksvalue_'+m+'" class="text_boxes" value="'+remarks+'" /></td></tr>';
				
				//alert(html)
				uom_arr[m]=uom;
				currency_arr[m]=currency_id;
				mn++;
				m++;
			}
			seq_arr=mn;
		}
		
		$("#bill_issue_table").append( html );
		
		var counter =$('#bill_issue_table tr').length;
		for(var q=1; q<=counter; q++)
		{
			var index=q-1;
			$("#bill_issue_table tr:eq("+index+")").find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ q },
					'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ q },
				});
				//$('#txtRate_'+q).removeAttr("onBlur").attr("onBlur","amount_caculation("+q+"); fnc_rate_copy("+q+");");
				$('#deliveryqnty_'+q).removeAttr("onBlur").attr("onBlur","qnty_caluculation("+q+");");
				$('#deliveryqntypcs_'+q).removeAttr("onBlur").attr("onBlur","qnty_caluculation("+q+");");
				$('#txtrate_'+q).removeAttr('onBlur').attr('onBlur',"qnty_caluculation("+q+"); fnc_rate_copy("+q+");");
				if(hidd_rate_from==3) { $('#txtrate_'+q).removeAttr("onBlur").attr("onBlur","openmypage_rate("+q+");"); }
				$('#remarks_'+q).removeAttr("onclick").attr("onclick","openmypage_remarks("+q+");");
				$("#cbouom_"+q).val( uom_arr[q] );
				$("#curanci_"+q).val( currency_arr[q] );
				qnty_caluculation_amount(q);
			})
			//alert(uom_arr[q]);
		}
		
		var tot_row=$('#bill_issue_table tr').length;
		
		math_operation( "total_qnty", "deliveryqnty_", "+", tot_row );
		math_operation( "total_qntyPcs", "deliveryqntypcs_", "+", tot_row );
		math_operation( "total_amount", "amount_", "+", tot_row,ddd );
		accounting_integration_check($('#hidden_acc_integ').val(),$('#hidden_integ_unlock').val());
		set_all_onclick();
	}
	function qnty_caluculation(id)
	{
		var tot_row=$('#bill_issue_table tr').length;
		var amount = ($("#deliveryqntypcs_"+id).val()*1)*($("#txtrate_"+id).val()*1);
		var amount_chk = ($("#deliveryqntypcs_"+id).val()*1)*($("#libRateId_"+id).val()*1);
		var txtrate=$("#txtrate_"+id).val()*1;
		//var wo_no=$("#textwonum_"+id).val();
		//var hidden_rate=$("#txthiddenrate_"+id).val()*1;
		// if(wo_no!="")
		// {
		// 	if(txtrate>hidden_rate)
		// 	{
		// 		$("#txtrate_"+id).val(hidden_rate);
		// 		var msg='Bill Rate is over than WO Rate';
		// 		//$("#txtamount_"+id).val(amount_chk);
		// 		alert(msg);
		// 		 return;
		// 	}
		// }
		
		$("#txtamount_"+id).val( amount.toFixed(4) );
		ddd={dec_type:5,comma:0};
		math_operation( "total_qntyPcs", "deliveryqntypcs_", "+", tot_row,ddd );
		math_operation( "total_amount", "amount_", "+", tot_row,ddd );
	}
	function fnc_rate_copy( trid )
	{
		var is_ratecopy=document.getElementById('checkrate').value;
		var txtrate=$('#txtrate_'+trid).val();
		if(is_ratecopy==1)
		{		
			var row_nums=$('#bill_issue_table tr').length;
			for(var j=trid; j<=row_nums; j++)
			{
				
				document.getElementById('txtrate_'+j).value=txtrate;
				qnty_caluculation(j);
			}
		}
	}
	function toggle( x, origColor , tr_id = '') {
		//var ex = tr_id.split("_");
		console.log(x+'=>'+tr_id);
		//alert(x+'='+tr_id);
		//if(document.getElementById('checkid'+ex[ex.length-1]).value==1)
		if(document.getElementById('checkid'+tr_id).value==1)
		{
			//alert(ex[ex.length-1]);
			var newColor = 'yellow';
		}
		else
		{
			var newColor = '#FFFFFF';
		}
		
		if ( x.style )
		{
			//x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			x.style.backgroundColor = newColor;
		}
	}
	var selected_id = new Array(); var selected_currency_id = new Array();
	
	function check_all_data()
	{
		if(document.getElementById('checkall').checked==true)
		{
			document.getElementById('checkall').value=1;
		}
		else if(document.getElementById('checkall').checked==false)
		{
			document.getElementById('checkall').value=2;
		}
		
		var list_tot_row=$('#tbl_list_search tr').length-1;
		var source=$('#cbo_party_source').val();
		
		//tot_row = tbl_row_count-1;
		for( var i = 1; i <= list_tot_row; i++ )
		{
			var strval=$('#strid'+i).val();
			var trid="";
			var split_str="";
			
			if(source==1) 
			{
				var split_str=strval.split("_");
				trid=split_str[2]+"_"+split_str[3]+"_"+split_str[12]+"_"+split_str[10]+"_"+split_str[8]+"_"+split_str[25];
				
			}
			else 
			{
				var split_str=strval.split("_");
				trid=trim(split_str[0]);
				
			}
			var tr_id = $("#unique_id"+i).val();
			//console.log(trid+'=>'+$("#tr_"+trid).css("display"));
			//console.log(tr_id+'=>'+$("#tr_"+tr_id).css("display"));
			if($("#tr_"+tr_id).css("display") != "none")
			{
				// alert(trid);
				
				//console.log(tr_id);
				//js_set_value( trid );
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
				js_set_value( tr_id,i );

			}
		}
	}
	
	function js_set_value(id,i)
	{
		//var source=$('#cbo_party_source').val();
		// alert (id)
		
		var str=id.split("***");
		// alert (str)
		if(exist_data.includes(id))
		{
			$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
			{ 
				$(this).html('Currency Mix Not Allowed').removeClass('messagebox').addClass('messagebox_error').fadeOut(2500);
			});
		}
		else
		{
			// alert( document.getElementById( 'tr_' + str[0] )+'='+i);
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' ,i);
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
		/*
		if( jQuery.inArray( str[1], selected_currency_id ) != -1  || selected_currency_id.length<1 )
		{
			
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
		*/
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
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_bill_no').val()+'*'+report_title+'*'+show_val_column,'knitting_bill_print','requires/knitting_bill_issue_controller');

			show_msg("3");
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if(operation==2)
			{
				if($('#hidden_acc_integ').val()==1)
				{
					show_msg('13');
					return;
				}
			}

			var isFileMandatory = "";
			<?php 
				
				if(!empty($_SESSION['logic_erp']['mandatory_field'][186][2])) echo " isFileMandatory = ". $_SESSION['logic_erp']['mandatory_field'][186][2] . ";\n";
			?>
			// alert(isFileMandatory); return;
			if($("#multiple_file_field")[0].files.length==0 && isFileMandatory!="" && $('#update_id').val()==''){

				document.getElementById("multiple_file_field").focus();
				var bgcolor='-moz-linear-gradient(bottom, rgb(254,151,174) 0%, rgb(255,255,255) 10%, rgb(254,151,174) 96%)';
				document.getElementById("multiple_file_field").style.backgroundImage=bgcolor;
				alert("Please Add File in Master Part");
				return;	
			}
			
			if( form_validation('cbo_company_id*cbo_location_name*txt_bill_date*cbo_party_name*cbo_party_source','Company Name*Location*Bill Date*Party Name*Party Source')==false)
			{
				return;
			}
			else
			{
				var source=$('#cbo_party_source').val();
				var bill_for=$('#cbo_bill_for').val();
				var tot_row=$('#bill_issue_table tr').length;
				var control_with=$('#hddn_control_with').val();
				if(control_with==1)
				{
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
								bill_amount+= $("#amount_"+j).val()*1;
							}
							//var bill_amount_status = return_ajax_request_value(orderIds+"_"+bill_amount+"_"+$('#update_id').val(), 'bill_amount_check', 'requires/knitting_bill_issue_controller')
							/*var ex_bill_amount_status=bill_amount_status.split("_");
							if (ex_bill_amount_status[0]==1)
							{
								var prev_msg="";
								if(ex_bill_amount_status[3]<0)
								{
									prev_msg="Excess Bill Amount (TK)";
								}
								else
								{
									prev_msg="Availble Bill Amount (TK)";
								}
								alert(ex_bill_amount_status[4] +"\n "+"Total Budget Amount (TK)"+"="+number_format(ex_bill_amount_status[2],2,'.','' )+"\n Previous Bill Amount (TK)"+"="+number_format(ex_bill_amount_status[1],2,'.','' ) +"\n "+ prev_msg +"="+number_format(ex_bill_amount_status[3],2,'.','' ));
								
								
								//alert(ex_bill_amount_status[4] +"\n "+"Total Budget Amount"+"="+number_format(ex_bill_amount_status[2],2,'.','' )+"\n Previous Bill Amount"+"="+number_format(ex_bill_amount_status[1],2,'.','' ) +"\n Availble Bill Amount"+"="+number_format(ex_bill_amount_status[3],2,'.','' ));
								release_freezing();
								return;
							}*/
							//alert(orderIds);
						}
					}
				}
				//
				//return; orderIds
				var data1="action=save_update_delete&operation="+operation+"&tot_row="+tot_row+get_submitted_data_string('txt_bill_no*cbo_company_id*cbo_location_name*cbo_party_location*txt_bill_date*cbo_party_name*cbo_party_source*txt_attention*cbo_bill_for*cbo_bill_section*update_id*hddn_control_with*txt_upcharge*txt_discount',"../")+"&orderIds="+orderIds;
				//alert(data1);
				//return
				
				var data2='';var z=1;
				for(var i=1; i<=tot_row; i++)
				{
					if(source==1)
					{
						var bodyPartType=$('#bodypartdesc_'+i).attr('bodyparttype');
						
						if(bodyPartType==40 || bodyPartType==50)
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
							//data2+=get_submitted_data_string('deleverydate_'+i+'*challenno_'+i+'*ordernoid_'+i+'*stylename_'+i+'*buyername_'+i+'*itemid_'+i+'*compoid_'+i+'*bodypartid_'+i+'*cbouom_'+i+'*numberroll_'+i+'*deliveryqnty_'+i+'*deliveryqntypcs_'+i+'*libRateId_'+i+'*txtrate_'+i+'*amount_'+i+'*remarksvalue_'+i+'*deliveryid_'+i+'*curanci_'+i+'*updateiddtls_'+i+'*collarcuff_'+i+'*recchallanno_'+i,"../");//
							data2+="&deleverydate_" + z + "='" + $('#deleverydate_'+i).val()+"'"+"&challenno_" + z + "='" + $('#challenno_'+i).val()+"'"+"&ordernoid_" + z + "='" + $('#ordernoid_'+i).val()+"'"+"&internalref_" + z + "='" + $('#internalref_'+i).val()+"'"+"&&stylename_" + z + "='" + $('#stylename_'+i).val()+"'"+"&buyername_" + z + "='" + $('#buyername_'+i).val()+"'"+"&itemid_" + z + "='" + $('#itemid_'+i).val()+"'"+"&compoid_" + z + "='" + $('#compoid_'+i).val()+"'"+"&bodypartid_" + z + "='" + $('#bodypartid_'+i).val()+"'"+"&cbouom_" + z + "='" + $('#cbouom_'+i).val()+"'"+"&numberroll_" + z + "='" + $('#numberroll_'+i).val()+"'"+"&deliveryqnty_" + z + "='" + $('#deliveryqnty_'+i).val()+"'"+"&deliveryqntypcs_" + z + "='" + $('#deliveryqntypcs_'+i).val()+"'"+"&libRateId_" + z + "='" + $('#libRateId_'+i).val()+"'"+"&txtrate_" + z + "='" + $('#txtrate_'+i).val()+"'"+"&amount_" + z + "='" + $('#amount_'+i).val()+"'"+"&remarksvalue_" + z + "='" + $('#remarksvalue_'+i).val()+"'"+"&deliveryid_" + z + "='" + $('#deliveryid_'+i).val()+"'"+"&curanci_" + z + "='" + $('#curanci_'+i).val()+"'"+"&updateiddtls_" + z + "='" + $('#updateiddtls_'+i).val()+"'"+"&collarcuff_" + z + "='" + $('#collarcuff_'+i).val()+"'"+"&recchallanno_" + z + "='" + $('#recchallanno_'+i).val()+"'"+"&yarncount_" + z + "='" + $('#yarncount_'+i).val()+"'"+"&mcdia_" + z + "='" + $('#mcdia_'+i).val()+"'";
						}
						else
						{
								data2+="&deleverydate_" + z + "='" + $('#deleverydate_'+i).val()+"'"+"&challenno_" + z + "='" + $('#challenno_'+i).val()+"'"+"&ordernoid_" + z + "='" + $('#ordernoid_'+i).val()+"'"+"&internalref_" + z + "='" + $('#internalref_'+i).val()+"'"+"&stylename_" + z + "='" + $('#stylename_'+i).val()+"'"+"&buyername_" + z + "='" + $('#buyername_'+i).val()+"'"+"&itemid_" + z + "='" + $('#itemid_'+i).val()+"'"+"&compoid_" + z + "='" + $('#compoid_'+i).val()+"'"+"&bodypartid_" + z + "='" + $('#bodypartid_'+i).val()+"'"+"&cbouom_" + z + "='" + $('#cbouom_'+i).val()+"'"+"&numberroll_" + z + "='" + $('#numberroll_'+i).val()+"'"+"&deliveryqnty_" + z + "='" + $('#deliveryqnty_'+i).val()+"'"+"&deliveryqntypcs_" + z + "='" + $('#deliveryqntypcs_'+i).val()+"'"+"&libRateId_" + z + "='" + $('#libRateId_'+i).val()+"'"+"&txtrate_" + z + "='" + $('#txtrate_'+i).val()+"'"+"&amount_" + z + "='" + $('#amount_'+i).val()+"'"+"&remarksvalue_" + z + "='" + $('#remarksvalue_'+i).val()+"'"+"&deliveryid_" + z + "='" + $('#deliveryid_'+i).val()+"'"+"&curanci_" + z + "='" + $('#curanci_'+i).val()+"'"+"&updateiddtls_" + z + "='" + $('#updateiddtls_'+i).val()+"'"+"&collarcuff_" + z + "='" + $('#collarcuff_'+i).val()+"'"+"&recchallanno_" + z + "='" + $('#recchallanno_'+i).val()+"'"+"&yarncount_" + z + "='" + $('#yarncount_'+i).val()+"'"+"&mcdia_" + z + "='" + $('#mcdia_'+i).val()+"'";
							//data2+=get_submitted_data_string('deleverydate_'+i+'*challenno_'+i+'*ordernoid_'+i+'*stylename_'+i+'*buyername_'+i+'*itemid_'+i+'*compoid_'+i+'*bodypartid_'+i+'*cbouom_'+i+'*numberroll_'+i+'*deliveryqnty_'+i+'*deliveryqntypcs_'+i+'*libRateId_'+i+'*txtrate_'+i+'*amount_'+i+'*remarksvalue_'+i+'*deliveryid_'+i+'*curanci_'+i+'*updateiddtls_'+i+'*collarcuff_'+i+'*recchallanno_'+i,"../");//
						}
					}
					z++;
				}
				
				var data=data1+data2;//
				// alert (data); 
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
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
			if(response[0]*1==14*1)
			{
				release_freezing();
				alert(response[1]);
				return;
			}
			else if(response[0]*1==17*1)
			{
				var prev_msg="";
				if(response[3]<0)
				{
					prev_msg="Excess Bill Amount (TK)";
				}
				else
				{
					prev_msg="Availble Bill Amount (TK)";
				}
				var validate_msg=(response[4] +"\n "+"Total Budget Amount (TK)"+"="+number_format(response[2],2,'.','' )+"\n Previous Bill Amount (TK)"+"="+number_format(response[1],2,'.','' ) +"\n "+ prev_msg +"="+number_format(response[3],2,'.','' ));
				
				release_freezing();
				alert(validate_msg);
				return;
			}
			else if(response[0]==0 || response[0]==1)
			{
				var check_system_id=$("#update_id").val();
				document.getElementById('update_id').value = response[1];
				if (check_system_id=="") uploadFile( $("#update_id").val());
				document.getElementById('txt_bill_no').value = response[2];
				document.getElementById('hidden_acc_integ').value = response[3];
				window_close(response[1]);
				accounting_integration_check(response[3]);
				set_button_status(1, permission, 'fnc_knitting_bill_issue',1);
			}
			else if(response[0]==2)
			{
				location.reload(); 
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
					url: 'requires/knitting_bill_issue_controller.php?action=file_upload&mst_id='+ mst_id, 
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
		var bodyparttype=$('#bodypartdesc_'+id).attr('bodyparttype')*1;
		var uom = $('#cbouom_'+id).val();
		if(bodyparttype==40 || bodyparttype==50)
		{
			if(uom==12)
			{
				var delv_qty=$('#deliveryqnty_'+id).val();
			}
			else
			{
				var delv_qty=$('#deliveryqntypcs_'+id).val();
			}
		}
		else
		{
			var delv_qty=$('#deliveryqnty_'+id).val();
		}

		
		if(uom == 1)
		{
			var delv_qty=$('#deliveryqntypcs_'+id).val();
		}

		$("#amount_"+id).val((delv_qty*1)*($("#txtrate_"+id).val()*1));
		var tot_row=$('#bill_issue_table tr').length;
		math_operation( "total_qnty", "deliveryqnty_", "+", tot_row );
		math_operation( "total_qntyPcs", "deliveryqntypcs_", "+", tot_row );
		math_operation( "total_amount", "amount_", "+", tot_row,ddd );
	}
	function qnty_caluculation_amount(id)	{
		var bodyparttype=$('#bodypartdesc_'+id).attr('bodyparttype')*1;
		var uom = $('#cbouom_'+id).val();
		if(bodyparttype==40 || bodyparttype==50)
		{
			if(uom==12)
			{
				var delv_qty=$('#deliveryqnty_'+id).val();
			}
			else
			{
				var delv_qty=$('#deliveryqntypcs_'+id).val();
			}
		}
		else
		{
			var delv_qty=$('#deliveryqnty_'+id).val();
		}

		
		if(uom == 1)
		{
			var delv_qty=$('#deliveryqntypcs_'+id).val();
		}

		$("#amount_"+id).val((delv_qty*1)*($("#txtrate_"+id).val()*1));
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
			//alert($('#cbo_company_id').size());
			$('#cbo_bill_for').removeAttr('disabled','disabled');
			$('#txt_bill_form_date').removeAttr('disabled','disabled');
			$('#txt_bill_to_date').removeAttr('disabled','disabled');
			$('#txt_manual_challan').removeAttr('disabled','disabled');
			$('#txt_job_no').removeAttr('disabled','disabled');
		}
		else
		{
			$('#cbo_bill_for').attr('disabled','disabled');
			$('#txt_bill_form_date').attr('disabled','disabled');
			$('#txt_bill_to_date').attr('disabled','disabled');
			$('#txt_manual_challan').attr('disabled','disabled');
			$('#txt_job_no').attr('disabled','disabled');
		}
		if(val!=0){
			load_drop_down( 'requires/knitting_bill_issue_controller', val, 'load_drop_down_party_location', 'party_location_td');
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
				freeze_window(5);
				var pop_data=trim(theemail.value).split('***');
				$('#libRateId_'+row_no).val(pop_data[0]);
				$('#txtrate_'+row_no).val(pop_data[1]);
				qnty_caluculation(row_no);
				//$('#interVeil').unlock('false');
				release_freezing();
			}
		}
	}

	function print_button_setting()
	{
	//  console.log('hello');
		//$('#button_data_panel').html('');
		get_php_form_data($('#cbo_company_id').val(),'print_button_variable_setting','requires/knitting_bill_issue_controller' );
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
	// print_report 2
	function fnc_print_report_2(report_type)
	{   
	    // For Print Button 2   For Print Button 6//alert(report_type);return;  
		if(report_type==2)
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
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_bill_no').val()+'*'+report_title+'*'+show_val_column,'knitting_bill_print_2','requires/knitting_bill_issue_controller');

			//return;
			show_msg("3");
		}
		else if(report_type==6)
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
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_bill_no').val()+'*'+report_title+'*'+show_val_column,'knitting_bill_print_6','requires/knitting_bill_issue_controller');

			//return;
			show_msg("3");
		}

		else if(report_type==7)
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
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_bill_no').val()+'*'+report_title+'*'+show_val_column,'knitting_bill_print_7','requires/knitting_bill_issue_controller');
			//return;

			show_msg("3");
		}
		// For Print Button Without Collar Cuff
		else if(report_type==3)
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
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_bill_no').val()+'*'+report_title+'*'+show_val_column,'knitting_bill_print_3','requires/knitting_bill_issue_controller');

			show_msg("3");
		
		}
        // For Print Button 3 
        else if(report_type==4)
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
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_bill_no').val()+'*'+report_title+'*'+show_val_column,'knitting_bill_print_4','requires/knitting_bill_issue_controller');

			show_msg("3");
		
		}
		else if(report_type==5){
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
			var report_title=$( "div.form_caption" ).html();
			//generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_bill_no').val()+'*'+report_title+'*'+show_val_column,'knitting_bill_print_5','requires/knitting_bill_issue_controller');

			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_bill_no').val()+'*'+report_title+'*'+show_val_column,'knitting_bill_print_5','requires/knitting_bill_issue_controller');

			show_msg("3");
		}

		else 
		{
			alert("This button only for In-bound Subcontract Bill");
			return;
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
	
	function fnc_list_search(type)
	{
		var txt_bill_form_date=$('#txt_bill_form_date').val();
		var txt_bill_to_date=$('#txt_bill_to_date').val();
		
		var txt_manual_challan=$('#txt_manual_challan').val();
		var txt_sys_challan=$('#txt_sys_challan').val();
		var txt_job_id=$('#txt_job_id').val();
		
		if($('#cbo_party_source').val()==1)
		{
			if( form_validation('cbo_company_id*cbo_party_location*cbo_party_source*cbo_party_name','Company Name*Location*Party Source*Party Name')==false)
			{
				return;
			}
			
			var divData=""; var msgData="";
			if(txt_manual_challan=="" && txt_sys_challan=="" && txt_job_id=="" ){
				var divData="txt_manual_challan*txt_sys_challan*txt_job_id";	
				var msgData="Manual Challan No*Sys. Challan No*Job No";	
			}
			if(divData!="")
			{
				if(txt_bill_form_date=="" || txt_bill_to_date=="")
				{
					if(form_validation(divData,msgData)==false){
						return;
					}
				}
			}
			var location_cond=document.getElementById('cbo_party_location').value;
		}
		else
		{
			if( form_validation('cbo_company_id*cbo_location_name*cbo_party_source*cbo_party_name','Company Name*Location*Party Source*Party Name')==false)
			{
				return;
			}
			var location_cond=document.getElementById('cbo_location_name').value;
		}
		
		$('#cbo_company_id').attr('disabled','disabled');
		//$('#cbo_location_name').attr('disabled','disabled');
		$('#cbo_party_location').attr('disabled','disabled');
		$('#cbo_party_source').attr('disabled','disabled');
		$('#cbo_party_name').attr('disabled','disabled');
		$('#cbo_bill_for').attr('disabled','disabled');
			
		if($('#cbo_party_source').val()==1)
		{
			$('#txt_bill_form_date').removeAttr('disabled','disabled');
			$('#txt_bill_to_date').removeAttr('disabled','disabled');
			$('#txt_manual_challan').removeAttr('disabled','disabled');
		}
		else
		{
			$('#txt_bill_form_date').attr('disabled','disabled');
			$('#txt_bill_to_date').attr('disabled','disabled');
			$('#txt_manual_challan').attr('disabled','disabled');
		}
		if (type==0 && ($('#update_id').val()*1)==0)
		{
			show_list_view(document.getElementById('cbo_company_id').value+'***'+location_cond+'***'+document.getElementById('cbo_party_source').value+'***'+document.getElementById('cbo_party_name').value+'***'+document.getElementById('cbo_bill_for').value+'***'+document.getElementById('txt_bill_form_date').value+'***'+document.getElementById('txt_bill_to_date').value+'***'+document.getElementById('txt_manual_challan').value+'***'+document.getElementById('txt_sys_challan').value+'***'+document.getElementById('update_id').value+'******'+document.getElementById('txt_job_id').value,'knitting_delivery_list_view','knitting_info_list','requires/knitting_bill_issue_controller', 'setFilterGrid("tbl_list_search",-1)');
		}
		else
		{
			var tot_row=$('#bill_issue_table tr').length;
				//alert(tot_row)
			var all_value="";
			
			if($('#cbo_party_source').val()==2)
			{
				for (var n=1; n<=tot_row; n++)
				{
					if(all_value=="") all_value+=$('#deliveryid_'+n).val(); else all_value+='!!!!'+$('#deliveryid_'+n).val();
				}
			}
			//alert(all_value);
			show_list_view(document.getElementById('cbo_company_id').value+'***'+location_cond+'***'+document.getElementById('cbo_party_source').value+'***'+document.getElementById('cbo_party_name').value+'***'+document.getElementById('cbo_bill_for').value+'***'+document.getElementById('txt_bill_form_date').value+'***'+document.getElementById('txt_bill_to_date').value+'***'+document.getElementById('txt_manual_challan').value+'***'+document.getElementById('txt_sys_challan').value+'***'+document.getElementById('update_id').value+'***'+all_value,'knitting_delivery_list_view','knitting_info_list','requires/knitting_bill_issue_controller','setFilterGrid("tbl_list_search",-1)','','');
		}
	}

	function openmypage_job()
	{ 
		if(form_validation('cbo_company_id*cbo_location_name*cbo_party_name','Company Name*Location*Party Name')==false){ return; }

		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('txt_manual_challan').value;

		page_link='requires/knitting_bill_issue_controller.php?action=job_popup&data='+data;
		title='Job No.';
		
		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=790px, height=420px, center=1, resize=0, scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemail=this.contentDoc.getElementById("selected_job");
			var theemaildata=theemail.value; 
			var new_data=theemaildata.split('_');
			$("#txt_job_no").val(new_data[0]);
			$("#txt_job_id").val(new_data[1]);
			fnc_list_search(0);
		}
	}
	function fnc_net_calculation()//here
	{
		//var tot_row=$('#bill_issue_table tr').length;
			var tot_amount=$('#total_amount').val()*1;//txt_tot_amount
			var txt_upcharge=$('#txt_upcharge').val()*1;
			var txt_discount=$('#txt_discount').val()*1;
			var totamount=tot_amount+txt_upcharge;
			var tot_amount_cal=totamount-txt_discount;
			
			
			//$('#txt_tot_qnty').val(number_format(totqty,2,'.','' ));
			$('#txt_net_total').val(number_format(tot_amount_cal,4,'.','' ));
	}
	//td_style
	function fnc_head_change(type)//here
	{
		//alert(type);
		if(type==4)
		{
			document.getElementById('td_order').innerHTML='Sales No';
			document.getElementById('td_style').innerHTML='Sales Style';
		}
		else{
			document.getElementById('td_order').innerHTML='Order No';
			document.getElementById('td_style').innerHTML='Cust.Style';
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
                    <input type="hidden" name="hidden_integ_unlock" id="hidden_integ_unlock" />
                    <input type="hidden" name="selected_order_id" id="selected_order_id" />
                    <input type="hidden" name="selected_currency_no" id="selected_currency_no" />
                    <input type="hidden" name="sel_order_pro_id" id="sel_order_pro_id" />
                    <input type="hidden" name="hddn_control_with" id="hddn_control_with" />
                    <input type="hidden" name="hidd_rate_from" id="hidd_rate_from" />
                    <input type="hidden" name="update_id" id="update_id" /><br>
                    <input type="text" name="txt_bill_no" id="txt_bill_no" class="text_boxes" style="width:140px" placeholder="Double Click to Search" onDblClick="openmypage_bill();" readonly tabindex="1" >
                </td>
            </tr>
            <tr>
                <td width="110" class="must_entry_caption">Knitting Company</td>
                <td width="150"><?php echo create_drop_down( "cbo_company_id",150,"select id,company_name from lib_company comp where is_deleted=0 and status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "print_button_setting();load_drop_down( 'requires/knitting_bill_issue_controller', this.value, 'load_drop_down_location', 'location_td'); get_php_form_data( this.value, 'load_field_level_check','requires/knitting_bill_issue_controller' );","","","","","",2); ?></td>
                <td width="110" class="must_entry_caption">Party Source</td>
                <td width="150"><? echo create_drop_down( "cbo_party_source", 150, $knitting_source,"", 1, "-- Select Party --", $selected, "load_drop_down( 'requires/knitting_bill_issue_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_party_name', 'party_td' ); fnc_bill_for(this.value);",0,"1,2","","","",5); ?></td>
                <td width="110" class="must_entry_caption">Party Name</td>
                <td width="150" id="party_td"><? echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "--Select Party--", $selected, "",0,"","","","",6); ?></td>
            </tr> 
            <tr>
                <td class="must_entry_caption">Location Name</td>                                              
                <td id="location_td"><? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "--Select Location--", $selected,"","","","","","",3); ?></td>
                <td>Bill For</td>
                <td><? echo create_drop_down( "cbo_bill_for", 150, $bill_for,"", 0, "--Select Bill--", 1, "fnc_head_change(this.value)",1,"","","","",8); ?></td>
				<td>Party Location</td>                                              
                <td id="party_location_td"><? echo create_drop_down( "cbo_party_location", 150, $blank_array,"", 1, "--Select Party Location--", $selected,"","","","","","",3); ?></td>
            </tr>
            <tr>
                <td>Attention</td>
                <td><input class="text_boxes" type="text" style="width:140px" name="txt_attention" id="txt_attention" tabindex="7" /></td>
                <td>Job No.</td>
                <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:140px" placeholder="Double Click to Search" onDblClick="openmypage_job();" readonly disabled>
                	<input type="hidden" name="txt_job_id" value="" class="txt_job_id" id="txt_job_id">
                </td>
                <td class="must_entry_caption">Bill Date</td>                                              
                <td><input class="datepicker" type="text" style="width:140px" name="txt_bill_date" id="txt_bill_date" tabindex="4" value="<? echo date('d-m-Y'); ?>" /></td>
            </tr>
             <tr>
            	<td>Bill Section</td>
                <td><? echo create_drop_down( "cbo_bill_section", 150, $bill_section,"", 1, "--Select Section--", 0, "",0,"","","",""); ?></td>
            	<td>&nbsp;</td>                                              
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td class="must_entry_caption">Trns. Date Range</td>                                              
                <td>
                    <input class="datepicker" type="text" style="width:60px" name="txt_bill_form_date" id="txt_bill_form_date" placeholder="From Date" disabled />&nbsp;
                    <input class="datepicker" type="text" style="width:60px" name="txt_bill_to_date" id="txt_bill_to_date" placeholder="To Date" disabled />
                </td>
                <td>Manual Challan No</td>                                              
                <td><input class="text_boxes" type="text" style="width:130px" name="txt_manual_challan" id="txt_manual_challan" disabled /> </td>
                <td>Sys. Challan No</td>                                              
                <td><input class="text_boxes_numeric" type="text" style="width:140px" name="txt_sys_challan" id="txt_sys_challan" /> </td>
            </tr>
            <tr>
			 <td><input type="button" id="file_uploaded" class="image_uploader" style="width:130px" value="ADD FILE" onClick="file_uploader ( '../', document.getElementById('update_id').value,'', 'knitting_bill_issue', 2 ,1)"></td>
                <td><input type="file" class="image_uploader" id="multiple_file_field" name="multiple_file_field" multiple style="width:130px"></td>
            	<td>&nbsp;</td>                                              
                <td><input class="formbutton" type="button" onClick="fnc_list_search(0);" style="width:130px" name="btn_populate" value="Populate" id="btn_populate" /></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
        </fieldset>
        <br>
        <fieldset style="width:1120px;">
        <legend>Knitting Bill Info</legend>
        <table align="right" cellspacing="0" cellpadding="0" width="1110"  border="1" rules="all" class="rpt_table" >
            <thead class="form_table_header">
                <th width="60" class="must_entry_caption">Delivery Date </th>
                <th width="40" class="must_entry_caption">Sys. Challan</th>
                <th width="70" id="td_order">Order No.</th>
				<th width="70">Inter.Ref</th>
                <th width="80" id="td_style">Cust.Style</th>
                <th width="70">Cust.Buyer</th>
                <th width="40">Roll</th>
                <th width="80">Body Part</th>                                      
                <th width="140">Fabric Des.</th>
				<th width="70">Yarn Count</th> 
				<th width="70">M/C Dia</th> 
                <th width="50" class="must_entry_caption">UOM</th>
                <th width="60" class="">Currency</th>
                <th width="70">Collar Cuff Measurement</th>
                <th width="40">Delv. Qty (Wgt.)</th>
                <th width="40">Delv. Qty (Pcs.)</th>
                <th width="40" class="must_entry_caption">Rate<input type="checkbox" name="checkrate" id="checkrate" onClick="fnc_check('rate'); " value="2" ></th>
                <th width="40">Amount</th>
                <th>RMK</th>
            </thead>
            <tbody id="bill_issue_table">
                <tr align="center">				
                    <td>
                        <input type="hidden" name="updateiddtls_1" id="updateiddtls_1">
                        <input type="text" name="deleverydate_1" id="deleverydate_1" class="datepicker" style="width:60px" readonly />									
                    </td>
                    <td><input type="text" name="challenno_1" id="challenno_1" class="text_boxes" style="width:40px" readonly />
                    <input type="hidden" name="recchallanno_1" id="recchallanno_1" class="text_boxes" style="width:70px" readonly />
                    </td>
                    <td>
                        <input type="hidden" name="ordernoid_1" id="ordernoid_1" value="">
                        <input type="text" name="orderno_1" id="orderno_1" class="text_boxes" style="width:70px" readonly />
                    </td>
					<td>
                        <input type="text" name="internalref_1" id="internalref_1" class="text_boxes" style="width:70px" readonly />
                    </td>
                    <td><input type="text" name="stylename_1" id="stylename_1" class="text_boxes" style="width:80px;" /></td>
                    <td><input type="text" name="buyername_1" id="buyername_1" class="text_boxes" style="width:70px" /></td>
                    <td><input name="numberroll_1" id="numberroll_1" type="text" class="text_boxes" style="width:40px" readonly /></td>  
                    <td style="display:none"><input type="text" name="yarndesc_1" id="yarndesc_1"  class="text_boxes" style="width:115px" readonly/></td>
                    <td><input type="text" name="bodypart_1" id="bodypart_1" class="text_boxes" style="width:80px" readonly/></td>
                    <td><input type="text" name="febricdesc_1" id="febricdesc_1" class="text_boxes_numeric" style="width:135px" readonly/></td>
					<td><input type="text" name="yarncount_1" id="yarncount_1" class="text_boxes_numeric" style="width:70px" readonly/></td>
					<td><input type="text" name="mcdia_1" id="mcdia_1" class="text_boxes_numeric" style="width:70px" readonly/></td>

                    <td><? echo create_drop_down( "cbouom_1", 50, $unit_of_measurement,"", 1, "-UOM-",0,"",0,"1,2,12,27" );?></td>
                    <td><? echo create_drop_down( "curanci_1", 60, $currency,"", 1, "-Select Currency-","","","","" );?></td>
                    <td><input type="text" name="collarcuff_1" id="collarcuff_1" class="text_boxes" style="width:65px" readonly/></td>
                    <td><input type="text" name="deliveryqnty_1" id="deliveryqnty_1"  class="text_boxes_numeric" style="width:40px" /></td>
                    <td><input type="text" name="deliveryqntypcs_1" id="deliveryqntypcs_1" class="text_boxes_numeric" style="width:40px" /></td>
                    <td>
                        <input type="text" name="txtrate_1" id="txtrate_1" class="text_boxes_numeric" style="width:40px" />
                        <input type="hidden" name="libRateId_1" id="libRateId_1" value="">
                    </td>
                    <td><input type="text" name="amount_1" id="amount_1" style="width:40px"  class="text_boxes" readonly /></td>
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
					<td width="70px">&nbsp;</td>
                    <td width="80px">&nbsp;</td>
                    <td width="70px">&nbsp;</td>
                    <td width="40px">&nbsp;</td>
                    <td width="220px" colspan="2" align="center"><input type="button" id="set_button" class="image_uploader" style="width:140px; margin-left:5px; margin-top:2px;" value="Terms Condition" onClick="open_terms_condition_popup('requires/knitting_bill_issue_controller.php?action=terms_condition_popup','Terms Condition')" /></td>
					<td width="70px">&nbsp;</td>
					<td width="70px">&nbsp;</td>

                    <td width="50px">&nbsp;</td>
                    <td width="60px">&nbsp;</td>
                    <td width="70px">Total Qty</td>
                    <td width="40px"><input type="text" name="total_qnty" id="total_qnty" class="text_boxes_numeric" style="width:40px" value="" readonly disabled /></td>
                    <td width="40px"><input type="text" name="total_qntyPcs" id="total_qntyPcs" class="text_boxes_numeric" style="width:40px" value="" readonly disabled /></td>
                    <td width="40px">Total</td>
                    <td width="40px" align="center"><input type="text" name="total_amount" id="total_amount"  class="text_boxes_numeric" style="width:40px" value="" readonly disabled /></td>
                    <td>&nbsp;</td>
                </tr>
				<tr>
                    <td colspan="17" height="15" align="center"><div id="bill_on" style="float:left; font-size:18px; color:#FF0000;"></div><div id="accounting_integration_div" style="float:center; font-size:18px; color:#FF0000;"></div></td>
                    <td>
                     <input type="text" title="Upcharge" name="txt_upcharge" id="txt_upcharge" onBlur="fnc_net_calculation()"  class="text_boxes_numeric" style="width:50px" placeholder="Upcharge"/> 
                      <input type="text" title="Discount" name="txt_discount" id="txt_discount"  class="text_boxes_numeric"  onBlur="fnc_net_calculation()" style="width:50px" placeholder="Discount"/> 
                     <input type="text" title="Net Total" name="txt_net_total" id="txt_net_total"  class="text_boxes_numeric" style="width:50px" placeholder="Net Total"/> 
                     </td>
                </tr>
                <tr>
                    <td colspan="19" height="15" align="center"><div id="accounting_integration_div" style="float:center; font-size:18px; color:#FF0000;"></div></td>
                </tr>
                <tr>
                    <td colspan="19" align="center" class="button_container">
                    <? 
					$date=date('d-m-Y');
					echo load_submit_buttons($permission,"fnc_knitting_bill_issue",0,1,"reset_form('knitigbillissue_1','knitting_info_list','','txt_bill_date,".$date."','$(\'#bill_issue_table tr:not(:first)\').remove();')",1); ?>&nbsp;
					&nbsp;
                    
					<input type="button" name="search_2" id="search_2" value="Print 2" onClick="fnc_print_report_2(2)" style="width:80px;display:none;" class="formbutton" />
					<input type="button" name="search_3" id="search_3" value="Print 3" onClick="fnc_print_report_2(4)" style="width:80px;display:none;" class="formbutton" />
					<input type="button" name="search_5" id="search_5" value="Print 5" onClick="fnc_print_report_2(5)" style="width:80px;display:none;" class="formbutton" />
					<input type="button" name="search_6" id="search_6" value="Print 6" onClick="fnc_print_report_2(6)" style="width:80px;display:none;" class="formbutton" />
					<input type="button" name="search_2" id="search_2" value="Print 7" onClick="fnc_print_report_2(7)" style="width:80px" class="formbutton" />
					
					<input type="button" name="search" id="search" value="Without Collar Cuff" onClick="fnc_without_collar_cuff(document.getElementById('cbo_party_source').value)" style="width:130px;display:none;" class="formbutton" />



                    </td>
                </tr>  
                <tr>
                    <td colspan="17" id="list_view" align="center"></td>
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
<script>
	if( $('#cbo_company_id option').length==2 && $('#cbo_party_source').val()==1 )
	{
		load_drop_down( 'requires/knitting_bill_issue_controller', $('#cbo_company_id').val(), 'load_drop_down_party_location', 'party_location_td');
	}
</script>
</html>
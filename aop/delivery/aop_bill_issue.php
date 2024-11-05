<? 
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create AOP Bill Issue				
Functionality	:	
JS Functions	:
Created by		:	Md Mahbubur Rahman 
Creation date 	: 	23-12-2019
Updated by 		: 		
Update date		:
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
 
require_once('../../includes/common.php');
extract($_REQUEST);

$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("AOP Bill Issue", "../../", 1,1, $unicode,1,'');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	var selected_id_listed = new Array();
	
	function fnc_load_party(type,within_group)
	{
		if (form_validation('cbo_company_name','Company')==false )
		{
			$('#cbo_within_group').val(1);
			return;
		}
		var company = $('#cbo_company_name').val();
		var party_name = $('#cbo_party_name').val();
		var location_name = $('#cbo_location_name').val();
		
		if(within_group==1 && type==1)
		{
			load_drop_down( 'requires/aop_bill_issue_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
		}
		else if(within_group==2 && type==1)
		{
			load_drop_down( 'requires/aop_bill_issue_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td' );
		}
		else if(within_group==1 && type==2)
		{
			load_drop_down( 'requires/aop_bill_issue_controller', party_name+'_'+2, 'load_drop_down_location', 'party_location_td' ); 
		} 
	}
	
	
	function openmypage_bill()
	{ 
		if(form_validation('cbo_company_name*cbo_within_group','Company Name*Within Group')==false){ return; }
		
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_within_group').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/aop_bill_issue_controller.php?data='+data+'&action=bill_no_popup','Bill Popup', 'width=810px,height=420px,center=1,resize=1,scrolling=0','../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("issue_id")
			//alert(theemail.value);
			if (theemail.value!="")
			{
				get_php_form_data( theemail.value+"_"+document.getElementById('cbo_within_group').value, "load_php_data_to_form_issue", "requires/aop_bill_issue_controller" );
				selected_id_listed = new Array();
				window_close(theemail.value);
				fnc_list_search(theemail.value);
				set_button_status(1, permission, 'fnc_aop_bill_issue',1);
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
				
				$('#cbo_currency_id').attr('disabled','disabled');
				$('#txt_exchange_rate').attr('disabled','disabled');
				$('#cbo_company_name').attr('disabled','disabled');
				$('#cbo_within_group').attr('disabled','disabled');
				$('#cbo_party_name').attr('disabled','disabled');
					
				
				var posted_account=document.getElementById('is_posted_account').value;
				 if (posted_account == 1) document.getElementById("accounting_posted_status").innerHTML = "Already Posted In Accounting."; 
				 else 
				 document.getElementById("accounting_posted_status").innerHTML = "";
				set_all_onclick();
				release_freezing();
			}
			
		}
	}
	
	
	
	
	function openmypage_job()
	{ 
		if (form_validation('cbo_company_name','Company')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
		page_link='requires/aop_bill_issue_controller.php?action=job_popup&data='+data;
		title='Job No.';
		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=790px, height=420px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemail=this.contentDoc.getElementById("selected_job");
			var theemaildata=theemail.value; 
			var new_data=theemaildata.split('_');
			$("#hidden_production_id").val(new_data[1]);
			$("#hidden_job_id").val(new_data[2]);
			$("#txt_job_no").val(new_data[0]);
		}
	}
	
	

	function fnc_list_search(type)
	{
		
		var txt_job_id=$('#hidden_job_id').val();  
		if( form_validation('cbo_company_name*cbo_party_name*txt_del_date_from*txt_del_date_to','Company Name*Party Name*date_from*date_to')==false)
		{
			return;
		}
		//alert($('#update_id').val());
		if (($('#update_id').val()*1)==0)
		{
			show_list_view(document.getElementById('cbo_company_name').value+'***'+document.getElementById('cbo_location_name').value+'***'+document.getElementById('cbo_within_group').value+'***'+document.getElementById('cbo_party_name').value+'***'+document.getElementById('cbo_party_location').value+'***'+document.getElementById('txt_bill_date').value+'***'+document.getElementById('txt_del_date_from').value+'***'+document.getElementById('txt_del_date_to').value+'***'+document.getElementById('txt_job_no').value+'***'+document.getElementById('hidden_job_id').value+'***'+document.getElementById('hidden_production_id').value+'***'+document.getElementById('update_id').value,'aop_bill_list_view','aop_info_list','requires/aop_bill_issue_controller', 'setFilterGrid("tbl_list_search",-1)');
		}
		else
		{
			var tot_row=$('#aop_issue_table tr').length;
				
			var all_value="";
			
			for (var n=1; n<=tot_row; n++)
			{
				if(all_value=="") all_value+=$('#deliveryid_'+n).val(); else all_value+='!!!!'+$('#deliveryid_'+n).val();
			}
			
			//alert(all_value);
			show_list_view(document.getElementById('cbo_company_name').value+'***'+document.getElementById('cbo_location_name').value+'***'+document.getElementById('cbo_within_group').value+'***'+document.getElementById('cbo_party_name').value+'***'+document.getElementById('cbo_party_location').value+'***'+document.getElementById('txt_bill_date').value+'***'+document.getElementById('txt_del_date_from').value+'***'+document.getElementById('txt_del_date_to').value+'***'+document.getElementById('txt_job_no').value+'***'+document.getElementById('hidden_job_id').value+'***'+document.getElementById('hidden_production_id').value+'***'+document.getElementById('update_id').value+'***'+type+'***'+all_value,'aop_bill_list_view','aop_info_list','requires/aop_bill_issue_controller','setFilterGrid("tbl_list_search",-1)','','');
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
	
	var seq_arr=new Array();  var currency_arr = new Array();
	function window_close( uid )
	{
		var html="";
		var incid=0;
		if(uid==0) incid=i; else incid=m;
		if(uid==0)
		{
			var list_tot_row=$('#tbl_list_search tr').length-1;
			var counter =$('#aop_issue_table tr').length; 
			//alert(counter); return;
			var i=0; var p=0;
			if(seq_arr!=0) i=counter; else i=i;
			
			for (var k=1; k<=list_tot_row; k++)
			{
				var check_id=''; var strval=''; var trid="";
				var check_id= $('#checkid'+k).val(); 
				var strval=$('#strid'+k).val();
				var split_str=strval.split("_");
				//alert(split_str)
				trid=split_str[0];
				if( check_id!=1) 
				{  
					$("#trent_"+trid).remove();
					for( var g = 0; g < selected_id_listed.length; g++ ) {
						
						if( selected_id_listed[g] == trid  ) break;
					}
					selected_id_listed.splice( g, 1 );
				}
				if(check_id==1)
				{
					if(selected_id_listed.length==0)
					{
						$("#aop_issue_table tr").remove();
					}
					//alert(check_id);
					if( jQuery.inArray(  trid , selected_id_listed ) == -1) 
					{
						i++;
						selected_id_listed.push( trid );
						var delev_dtls_id=trim(split_str[0]);
						var delev_no=split_str[1];
						var delev_date=split_str[2];
						var batch_id=split_str[3];
						var order_id=split_str[4];
						var process=split_str[5];
						var product_qnty=split_str[6];
						var order_no=split_str[7];
						var subcon_job=split_str[8];
						var order_buyer_po_id=split_str[9];
						var buyer_po_no=split_str[10];
						var buyer_style_ref=split_str[11];
						var work_order_id=split_str[12];
						var booking_dtls_id=split_str[13];
						var fabric_description=split_str[14];
						var no_of_roll=split_str[15];
						var process_id=split_str[16];
						var batch_name=split_str[17];
						var DiaWidthType=split_str[18];
						var rate=split_str[19];
						var currency_id=split_str[20];
						var amount=product_qnty*rate;
						var exchange_rate = $('#txt_exchange_rate').val();
						var domistic_amount=amount*exchange_rate;
						//alert(exchange_rate);
						
						//var selected_currency=currency_id;
						//var DiaWidthType="";
						html+='<tr align="center" id="trent_'+trid+'"><td><input type="hidden" name="updateiddtls_'+i+'" id="updateiddtls_'+i+'" value=""><input type="hidden" name="deliveryid_'+i+'" id="deliveryid_'+i+'" style="width:45px" value="'+delev_dtls_id+'"><input type="text" name="deleverydate_'+i+'" id="deleverydate_'+i+'"  class="datepicker" style="width:60px" value="'+delev_date+'" disabled/></td><td><input type="text" name="challenno_'+i+'" id="challenno_'+i+'"  class="text_boxes" style="width:60px" value="'+delev_no+'" readonly /></td><td><input type="hidden" name="ordernoid_'+i+'" id="ordernoid_'+i+'" value="'+order_id+'"><input type="text" name="orderno_'+i+'" id="orderno_'+i+'" class="text_boxes" style="width:90px" value="'+order_no+'" readonly /></td><td><input type="text" name="txtJobNo_'+i+'" id="txtJobNo_'+i+'"  class="text_boxes" style="width:90px;" value="'+subcon_job+'" readonly /></td><td><input type="text" name="txtBuyPO_'+i+'" id="txtBuyPO_'+i+'"  class="text_boxes" style="width:80px;" value="'+buyer_po_no+'" readonly /></td><td><input type="text" name="txtBuyStyle_'+i+'" id="txtBuyStyle_'+i+'"  class="text_boxes" style="width:80px;" value="'+buyer_style_ref+'" readonly /></td><td><input type="hidden" name="txtBatchid_'+i+'" id="txtBatchid_'+i+'"  class="text_boxes" style="width:80px;" value="'+batch_id+'" readonly /><input type="text" name="txtBatchNo_'+i+'" id="txtBatchNo_'+i+'"  class="text_boxes" style="width:80px;" value="'+batch_name+'" readonly /></td><td><input type="text" name="textFebricDesc_'+i+'" id="textFebricDesc_'+i+'"  class="text_boxes" style="width:80px;" value="'+fabric_description+'" readonly /></td><td><input type="text" name="textProcess_'+i+'" id="textProcess_'+i+'"  class="text_boxes" style="width:80px;" value="'+process+'" readonly /></td><td><input type="text" name="txtDia_'+i+'" id="txtDia_'+i+'"  class="text_boxes" style="width:80px;" value="'+DiaWidthType+'" readonly /></td><td><input type="text" name="txtNoRoll_'+i+'" id="txtNoRoll_'+i+'"  class="text_boxes" style="width:60px;text-align:right;" value="'+no_of_roll+'" readonly /></td><td><input type="text" name="deliveryqnty_'+i+'" id="deliveryqnty_'+i+'"  class="text_boxes" style="width:60px;text-align:right;" value="'+product_qnty+'" onBlur="qnty_caluculation('+i+');" readonly /></td><td><input type="text" name="txtrate_'+i+'" id="txtrate_'+i+'"  class="text_boxes" style="width:40px;text-align:right;" onBlur="qnty_caluculation('+i+');"  value="'+rate+'" /></td><td><input type="text" name="amount_'+i+'" id="amount_'+i+'"  class="text_boxes" style="width:60px;text-align:right;" value="'+amount+'" readonly /></td><td><input type="text" name="txtdomisticamount_'+i+'" id="txtdomisticamount_'+i+'"  class="text_boxes" style="width:60px;text-align:right;" value="'+domistic_amount+'" readonly /></td><td style="display:none"><select name="cboCurrency_'+i+'" id="cboCurrency_'+i+'" class="text_boxes" style="width:60px"><option value="0">-Select Currency-</option><option value="1" selected>Taka</option><option value="2">USD</option><option value="3">EURO</option><option value="4">CHF</option><option value="5">SGD</option><option value="6">Pound</option><option value="7">YEN</option></select></td><td><input type="button" name="remarks_'+i+'" id="remarks_'+i+'"  class="formbuttonplasminus" style="width:20px" value="R" onClick="openmypage_remarks('+i+');" /><input type="hidden" name="remarksvalue_'+i+'" id="remarksvalue_'+i+'" class="text_boxes" value="" /></td></tr>';
						currency_arr[i]=currency_id;
						//alert(currency_id+'='+currency_arr);
						p++;
					}
				}
			}
			seq_arr=p;
		}
		else
		{
			$("#aop_issue_table tr").remove();
			var list_view_str = return_global_ajax_value( uid+"!^!"+1, 'load_dtls_data', '', 'requires/aop_bill_issue_controller');
			var split_list_view=list_view_str.split('###');
			//alert(split_list_view);
			var m=1; var mn=0;
			for (var n=1; n<=split_list_view.length; n++)
			{
				var split_list_str=split_list_view[mn].split('_');
				var trid="";
				trid=split_list_str[0];
				var delev_dtls_id=trim(split_list_str[0]);
				var delev_no=split_list_str[1];
				var delev_date=split_list_str[2];
				var batch_id=split_list_str[3];
				var order_id=split_list_str[4];
				var process=split_list_str[5];
				var product_qnty=split_list_str[6];
				var order_no=split_list_str[7];
				var subcon_job=split_list_str[8];
				var order_buyer_po_id=split_list_str[9];
				var buyer_po_no=split_list_str[10];
				var buyer_style_ref=split_list_str[11];
				var work_order_id=split_list_str[12];
				var booking_dtls_id=split_list_str[13];
				var fabric_description=split_list_str[14];
				var no_of_roll=split_list_str[15];
				var process_id=split_list_str[16];
				var batch_name=split_list_str[17];
				var upd_id=split_list_str[18];
				var rate=split_list_str[19];
				var amount=split_list_str[20];
				var remarks=split_list_str[21];
				var DiaWidthType=split_list_str[22];
				var amount=product_qnty*rate;
				var currency_id=split_list_str[23];
				var exchange_rate = $('#txt_exchange_rate').val();
				var domistic_amount=amount*exchange_rate;
				//alert(currency_id);
				//	var DiaWidthType="";
			
				html+='<tr align="center" id="trent_'+trid+'"><td><input type="hidden" name="updateiddtls_'+m+'" id="updateiddtls_'+m+'" value="'+upd_id+'"><input type="hidden" name="deliveryid_'+m+'" id="deliveryid_'+m+'" style="width:45px" value="'+delev_dtls_id+'"><input type="text" name="deleverydate_'+m+'" id="deleverydate_'+m+'"  class="datepicker" style="width:60px" value="'+delev_date+'" disabled/></td><td><input type="text" name="challenno_'+m+'" id="challenno_'+m+'"  class="text_boxes" style="width:60px" value="'+delev_no+'" readonly /></td><td><input type="hidden" name="ordernoid_'+m+'" id="ordernoid_'+m+'" value="'+order_id+'"><input type="text" name="orderno_'+m+'" id="orderno_'+m+'" class="text_boxes" style="width:90px" value="'+order_no+'" readonly /></td><td><input type="text" name="txtJobNo_'+m+'" id="txtJobNo_'+m+'"  class="text_boxes" style="width:90px;" value="'+subcon_job+'" readonly /></td><td><input type="text" name="txtBuyPO_'+m+'" id="txtBuyPO_'+m+'"  class="text_boxes" style="width:80px;" value="'+buyer_po_no+'" readonly /></td><td><input type="text" name="txtBuyStyle_'+m+'" id="txtBuyStyle_'+m+'"  class="text_boxes" style="width:80px;" value="'+buyer_style_ref+'" readonly /></td><td><input type="hidden" name="txtBatchid_'+m+'" id="txtBatchid_'+m+'"  class="text_boxes" style="width:80px;" value="'+batch_id+'" readonly /><input type="text" name="txtBatchNo_'+m+'" id="txtBatchNo_'+m+'"  class="text_boxes" style="width:80px;" value="'+batch_name+'" readonly /></td><td><input type="text" name="textFebricDesc_'+m+'" id="textFebricDesc_'+m+'"  class="text_boxes" style="width:80px;" value="'+fabric_description+'" readonly /></td><td><input type="text" name="textProcess_'+m+'" id="textProcess_'+m+'"  class="text_boxes" style="width:80px;" value="'+process+'" readonly /></td><td><input type="text" name="txtDia_'+m+'" id="txtDia_'+m+'"  class="text_boxes" style="width:80px;" value="'+DiaWidthType+'" readonly /></td><td><input type="text" name="txtNoRoll_'+m+'" id="txtNoRoll_'+m+'"  class="text_boxes" style="width:60px;text-align:right" value="'+no_of_roll+'" readonly /></td><td><input type="text" name="deliveryqnty_'+m+'" id="deliveryqnty_'+m+'"  class="text_boxes" style="width:60px;text-align:right;" value="'+product_qnty+'" onBlur="qnty_caluculation('+m+');" readonly /></td><td><input type="text" name="txtrate_'+m+'" id="txtrate_'+m+'"  class="text_boxes" style="width:40px;text-align:right;" onBlur="qnty_caluculation('+m+');"  value="'+rate+'" /></td><td><input type="text" name="amount_'+m+'" id="amount_'+m+'"  class="text_boxes" style="width:60px;text-align:right;" value="'+amount+'" readonly /></td><td><input type="text" name="txtdomisticamount_'+m+'" id="txtdomisticamount_'+m+'"  class="text_boxes" style="width:60px;text-align:right;" value="'+domistic_amount+'" readonly /></td><td style="display:none"><select name="cboCurrency_'+m+'" id="cboCurrency_'+m+'" class="text_boxes" style="width:60px"><option value="0">-Select Currency-</option><option value="1" selected>Taka</option><option value="2">USD</option><option value="3">EURO</option><option value="4">CHF</option><option value="5">SGD</option><option value="6">Pound</option><option value="7">YEN</option></select></td><td><input type="button" name="remarks_'+m+'" id="remarks_'+m+'"  class="formbuttonplasminus" style="width:20px" value="R" onClick="openmypage_remarks('+m+');" /><input type="hidden" name="remarksvalue_'+m+'" id="remarksvalue_'+m+'" class="text_boxes" value="" /></td></tr>';
				
				//alert(currency_id)
				currency_arr[m]=currency_id;
				
				mn++;
				m++;
			}
			seq_arr=mn;
		}
		
		$("#aop_issue_table").append( html );
		var counter =$('#aop_issue_table tr').length; 
		
		//alert(currency_arr);
		
		for(var q=1; q<=counter; q++)
		{
			var index=q-1;
			$("#aop_issue_table tr:eq("+index+")").find("input,select").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ q },
					'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ q },
				}); 
				$("#cboCurrency_"+q).val( currency_arr[q] );
				
			})
		}
		
		var tot_row=$('#aop_issue_table tr').length;
		math_operation( "total_qnty", "deliveryqnty_", "+", tot_row );
		math_operation( "total_amount", "amount_", "+", tot_row );
		math_operation( "txtTotdomisticamount", "txtdomisticamount_", "+", tot_row );
		set_all_onclick();
		//currency_arr = [];
	}
	
	
	function qnty_caluculation(id)
	{
		var delv_qty=$('#deliveryqnty_'+id).val()*1;
		var rate=$('#txtrate_'+id).val()*1;
		$("#amount_"+id).val((delv_qty*1)*($("#txtrate_"+id).val()*1));
		var exchange_rate = $('#txt_exchange_rate').val();
		var amounts =(delv_qty*1)*($("#txtrate_"+id).val()*1)
		$("#txtdomisticamount_"+id).val((amounts*1)*(exchange_rate*1));
		var tot_row=$('#aop_issue_table tr').length;
		math_operation( "total_qnty", "deliveryqnty_", "+", tot_row );
		math_operation( "total_amount", "amount_", "+", tot_row );
		math_operation( "txtTotdomisticamount", "txtdomisticamount_", "+", tot_row );
	}
	
	
	function toggle( x, origColor ) 
	{
		var newColor = 'yellow';
		if ( x.style ) {
		x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}
	
	
	
	var selected_id = new Array();
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
		for( var i = 1; i <= list_tot_row; i++ )
		{
			var strval=$('#strid'+i).val();
			var trid="";
			var split_str=strval.split("_");
			trid=trim(split_str[0]);
			if($("#tr_"+trid).css("display") != "none")
			{
				js_set_value( trid );
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
		   var str=id.split("***");
			//alert (selected_id);
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			if( jQuery.inArray(  str[0] , selected_id ) == -1) {
				//alert (id);
				selected_id.push( str[0] );
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ ) 
				{
				if( selected_id[i] == str[0]  ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = ''; 
			for( var i = 0; i < selected_id.length; i++ ) 
			{
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			$('#selected_order_id').val( id );
	}
	
	
	
	function openmypage_remarks(id)
	{
		var data=document.getElementById('remarksvalue_'+id).value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/aop_bill_issue_controller.php?data='+data+'&action=remarks_popup','Remarks Popup', 'width=420px,height=320px,center=1,resize=1,scrolling=0','../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("text_new_remarks");//Access form field with id="emailfield"
			if (theemail.value!="")
			{
				$('#remarksvalue_'+id).val(theemail.value);
			}
		}
	}
	
	
	
	
	function fnc_aop_bill_issue( operation )
	{
		if(operation==4)
		{
			if ( $('#txt_bill_no').val()=='')
			{
				alert ('Bill ID Not Save.');
				return;
			}
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_within_group').val(), "aop_bill_issue_print", "requires/aop_bill_issue_controller") 
			//return;
			show_msg("3");
		}
		if(operation==0 || operation==1 || operation==2)
		{
			if( form_validation('cbo_company_name*cbo_location_name*txt_bill_date*cbo_party_name','Company Name*Location*Bill Date*Party Name')==false)
			{
				return;
			}
			else
			{
				var tot_row=$('#aop_issue_table tr').length;
				var data1="action=save_update_delete&operation="+operation+"&tot_row="+tot_row+get_submitted_data_string('txt_bill_no*cbo_company_name*cbo_location_name*cbo_within_group*cbo_party_name*cbo_party_location*txt_bill_date*txt_del_date_from*txt_del_date_to*update_id*txt_job_no*hidden_job_id*hidden_production_id*is_posted_account*txt_exchange_rate*cbo_currency_id',"../");
				//alert(data1)
				var data2=''; var prev_curr_id='';  var curr_id=''; 
				for(var i=1; i<=tot_row; i++)
				{
					curr_id= $('#cboCurrency_'+i).val();
					if(i==1){
						prev_curr_id = curr_id;
					}
					if(prev_curr_id != curr_id){
						alert ("Currency Mix Not Allow"); return;
					}
					data2+=get_submitted_data_string('deleverydate_'+i+'*challenno_'+i+'*ordernoid_'+i+'*orderno_'+i+'*txtJobNo_'+i+'*txtBuyPO_'+i+'*txtBuyStyle_'+i+'*txtBatchNo_'+i+'*textFebricDesc_'+i+'*textProcess_'+i+'*txtDia_'+i+'*txtNoRoll_'+i+'*deliveryqnty_'+i+'*txtrate_'+i+'*amount_'+i+'*cboCurrency_'+i+'*remarks_'+i+'*remarksvalue_'+i+'*updateiddtls_'+i+'*deliveryid_'+i+'*txtBatchid_'+i,"../");//
				}
				
				var data=data1+data2;
				//alert (data);return;
				freeze_window(operation);
				http.open("POST","requires/aop_bill_issue_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_aop_bill_issue_response;
			}
		}
	}
	
	
	
	function fnc_aop_bill_issue_response()
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
			
			if(response[0]==0 || response[0]==1)
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_bill_no').value = response[2];
				//document.getElementById('hidden_acc_integ').value = response[3];
				window_close(response[1]);
				//accounting_integration_check(response[3]);
				set_button_status(1, permission, 'fnc_aop_bill_issue',1);
			}
			
			release_freezing();
		}
	}
	
	
	function exchange_rate(val)
	{
		if(form_validation('cbo_company_name*txt_bill_date', 'Company Name*Bill Date')==false )
		{
			$("#cbo_currency_id").val(0);
			return;
		}
		
		if(val==0)
		{
			$('#txt_bill_date').removeAttr('disabled','disabled');
			$('#cbo_company_name').removeAttr('disabled','disabled');
			$("#txt_exchange_rate").val("");
		}
		else if(val==1)
		{
			$("#txt_exchange_rate").val(1);
			$('#txt_bill_date').attr('disabled','disabled');
			$('#cbo_company_name').attr('disabled','disabled');
			$('#txt_exchange_rate').attr('disabled','disabled');
		}
		else
		{
			var bill_date = $('#txt_bill_date').val();
			var company_name = $('#cbo_company_name').val();
			var response=return_global_ajax_value( val+"**"+bill_date+"**"+company_name, 'check_conversion_rate', '', 'requires/aop_bill_issue_controller');
			$('#txt_exchange_rate').val(response);
			$('#txt_bill_date').attr('disabled','disabled');
			$('#cbo_company_name').attr('disabled','disabled');
			$('#txt_exchange_rate').attr('disabled','disabled');
		}
	}
</script>
</head>
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
    <? echo load_freeze_divs ("../../",$permission);  ?>
	<form name="aopbillissue_1" id="aopbillissue_1"  autocomplete="off"  >
    <fieldset style="width:800px;">
    <legend>AOP Bill Info </legend>
        <table cellpadding="0" cellspacing="2" width="790">
            <tr>
                <td align="right" colspan="3"><strong>Bill No </strong></td>
                <td width="140" align="justify">
                    <input type="hidden" name="selected_order_id" id="selected_order_id" />
                    <input type="hidden" name="selected_currency_no" id="selected_currency_no" />
                    <input type="hidden" name="update_id" id="update_id" />
                    <input type="hidden" name="hidden_production_id" id="hidden_production_id" />
                     <input type="hidden" name="hidden_job_id" id="hidden_job_id" />
                     <input type="hidden" id="is_posted_account" name="is_posted_account" value=""/>
                   <input type="text" name="txt_bill_no" id="txt_bill_no" class="text_boxes" style="width:140px" placeholder="Double Click to Search" onDblClick="openmypage_bill();" readonly tabindex="1" >
                </td>
             </tr>
             <tr>
                <td width="110" class="must_entry_caption">Company Name</td>
                <td width="150">
                    <?php 
                       echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/aop_bill_issue_controller', this.value+'_'+1, 'load_drop_down_location', 'location_td'); fnc_load_party(1,document.getElementById('cbo_within_group').value);exchange_rate(document.getElementById('cbo_currency_id').value);");	
                    ?>
                </td>
                <td width="110">Location Name</td>                                              
                <td width="150" id="location_td">
                    <? 
                        echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "" );
                    ?>
                </td>
                <td class="must_entry_caption">Within Group</td>
                <td>
                    <?
                    	echo create_drop_down( "cbo_within_group", 150, $yes_no,"", 0, "--  --", 0, "fnc_load_party(1,this.value);" );
                    ?> 
                </td>
            </tr> 
            <tr>

                <td class="must_entry_caption">Party Name</td>
                <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_load_party(2,document.getElementById('cbo_within_group').value);"); ?></td>
                <td id="td_party_location">Party Location</td>
                        <td id="party_location_td"><? echo create_drop_down( "cbo_party_location", 150, $blank_array,"", 1, "-- Select Location --", $selected, "",1 ); ?></td>
                <td width="110" class="must_entry_caption">Bill Date</td>                                              
                <td width="150">
                    <input class="datepicker" type="text" style="width:140px" name="txt_bill_date" id="txt_bill_date" tabindex="4" onChange="exchange_rate(document.getElementById('cbo_currency_id').value);"  />
                </td>
            </tr>
            <tr>
                <td class="must_entry_caption">Currency</td>
                <td id="currency_td">
                <?
               	 echo create_drop_down("cbo_currency_id", 150, $currency,"", 1, "-- Select Currency --",$selected,"exchange_rate(this.value)", "","","","","",7 ); 
                ?>
                </td>
                <td class="must_entry_caption">Exchange Rate</td>
                <td><input type="text" name="txt_exchange_rate" id="txt_exchange_rate" style="width:140px" class="text_boxes_numeric"  value=""  readonly/></td>
                <td class="must_entry_caption">Delivery Date Range</td>
                <td>
                    <input class="datepicker" type="text" style="width:52px" name="txt_del_date_from" id="txt_del_date_from" tabindex="4" />  To  <input class="datepicker" type="text" style="width:52px" name="txt_del_date_to" id="txt_del_date_to" tabindex="4" />
                </td>
            </tr>
            <tr>
                <td><strong>AOP Job No.</strong></td>
                <td>
                    <input class="text_boxes"  type="text" name="txt_job_no" id="txt_job_no" onDblClick="openmypage_job();" placeholder="Double Click" style="width:140px;" readonly /></td>
                <td width="110">&nbsp;</td>                                              
                <td><input class="formbutton" type="button" onClick="fnc_list_search(0);" style="width:153px" name="btn_populate" value="Populate" id="btn_populate" /></td>
            </tr>
        </table>
    </fieldset>
    <br>
    <fieldset style="width:960px;">
    <legend>AOP Bill Info </legend>
    <table  style="border:none; width:950px;" cellpadding="0" cellspacing="1" border="0" id="">
        <thead class="form_table_header">
            <th width="60">Delivery Date </th>
            <th width="60" class="must_entry_caption">Delivery ID</th>
            <th width="80">Order No.</th>
            <th width="80">Job No.</th>
            <th width="70">Buyer PO</th>
            <th width="70">Buyer.Style</th>
            <th width="70">Batch</th>
            <th width="80">Item Description</th>
            <th width="70">Process</th>
            <th width="70">Dia Width Type</th>
            <th width="40">No. Roll</th>
            <th width="60">Qty(Kg)</th>
            <th width="40">Rate</th>
            <th width="60">Amount</th>
             <th width="60">Domestic Amount</th>
            <th width="60" style="display:none">Currency</th>
            <th>Remarks</th>
        </thead>
        <tbody id="aop_issue_table">
            <tr align="center">				
                <td>
                    <input type="hidden" name="updateiddtls_1" id="updateiddtls_1" style="width:40px">
                    <input type="text" name="txtDeleverydate_1" id="txtDeleverydate_1"  class="datepicker" style="width:60px" readonly /></td>
                <td>
                    <input type="text" name="txtDelSysId_1" id="txtDelSysId_1"  class="text_boxes" style="width:55px" readonly /></td>
                <td>
                    <input type="hidden" name="ordernoid_1" id="ordernoid_1" value="" style="width:40px">
                    <input type="text" name="txtOrderno_1" id="txtOrderno_1"  class="text_boxes" style="width:65px" readonly /></td>
                <td>
                    <input type="text" name="txtJobNo_1" id="txtJobNo_1"  class="text_boxes" style="width:65px" readonly /></td>
                <td>
                    <input type="text" name="txtBuyPO_1" id="txtBuyPO_1"  class="text_boxes" style="width:65px" readonly /></td>
                <td>
                    <input type="text" name="txtBuyStyle_1" id="txtBuyStyle_1"  class="text_boxes" style="width:80px;" /></td>
                <td>
                    <input type="text" name="txtBatchNo_1" id="txtBatchNo_1"  class="text_boxes" style="width:80px" />
                    <input type="hidden" name="txtBatchid_1" id="txtBatchid_1"  class="text_boxes" style="width:80px" /></td>
                <td>
                    <input type="text" name="textFebricDesc_1" id="textFebricDesc_1"  class="text_boxes" style="width:105px" readonly/></td>
                <td>
                    <input type="text" name="textProcess_1" id="textProcess_1"  class="text_boxes" style="width:105px" readonly/></td>
                <td>			
                    <input type="text" name="txtDia_1" id="txtDia_1" class="text_boxes" style="width:100px" readonly /></td>  
                <td>
                    <input type="text" name="txtNoRoll_1" id="txtNoRoll_1"  class="text_boxes" style="width:80px" readonly/></td>
                <td>
                    <input type="text" name="deliveryqnty_1" id="deliveryqnty_1"  class="text_boxes_numeric" style="width:60px" readonly /></td>
                <td>
                    <input type="text" name="txtrate_1" id="txtrate_1"  class="text_boxes_numeric" style="width:40px"/></td>
                <td><input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" style="width:60px"  readonly /></td>
                <td align="center"><input type="text" name="txtdomisticamount_1" id="txtdomisticamount_1" class="text_boxes_numeric" style="width:60px;" readonly /></td>
                <td style="display:none">
					<? echo create_drop_down( "cboCurrency_1", 60, $currency,"", 1, "-Select Currency-",1,"",0,"" );?></td>
                <td>
                    <input type="text" name="remarks_1" id="remarks_1"  class="text_boxes" style="width:80px" /></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right">Total: &nbsp;</td>
                <td>
                    <input type="text" name="total_qnty" id="total_qnty"  class="text_boxes_numeric" style="width:60px" readonly disabled />
                </td>
                <td >&nbsp;</td>
                <td>
                    <input type="text" name="total_amount" id="total_amount"  class="text_boxes_numeric" style="width:60px" readonly  disabled/>
                </td>
                <td>
                    <input type="text" name="txtTotdomisticamount" id="txtTotdomisticamount"  class="text_boxes_numeric" style="width:60px" readonly  disabled/>
                </td>
                <td>&nbsp;</td>
                <td style="display:none">&nbsp;</td>
            </tr> 
            <tr>
               <td colspan="19" height="15" align="center"><div id="accounting_posted_status" style="float:center; font-size:18px; color:#FF0000;"></div></td>
            </tr>              
            <tr>
                <td colspan="19" align="center" class="button_container">
                <? 
                    echo load_submit_buttons($permission,"fnc_aop_bill_issue",0,1,"reset_form('aopbillissue_1','aop_info_list','','','$(\'#aopl_issue_table tr:not(:first)\').remove();')",1);
                ?> 
                </td>
            </tr>  
            <tr>
                <td colspan="19" id="list_view" align="center"></td>
            </tr>
        </tfoot>                                                             
    </table>
    </fieldset> 
    </form>
    <br>
    <div id="aop_info_list"></div>                           
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
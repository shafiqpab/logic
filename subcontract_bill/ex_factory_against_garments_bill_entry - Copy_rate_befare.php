<? 
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Sub-contract Knitting bill issue  
Functionality	:	
JS Functions	:
Created by		:	Md Mahbubur Rahman 
Creation date 	: 	07-04-2020	
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
echo load_html_head_contents("Ex-Factory against Garments Bill Entry", "../", 1,1, $unicode,1,'');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';
	var selected_id_listed = new Array();
	
	
	
	function openmypage_bill()
	{ 
		if(form_validation('cbo_company_name','Company Name')==false){ return; }
		
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_source').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/ex_factory_against_garments_bill_entry_controller.php?data='+data+'&action=bill_no_popup','Bill Popup', 'width=810px,height=420px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("issue_id");
			//alert(theemail.value);
			
			if (theemail.value!="")
			{
				get_php_form_data( theemail.value+"_"+document.getElementById('cbo_party_source').value, "load_php_data_to_form_issue", "requires/ex_factory_against_garments_bill_entry_controller" );
				selected_id_listed = new Array();
				window_close(theemail.value);
				//alert(theemail.value);
				fnc_list_search(theemail.value);
				
				//$('#is_posted_account').val(bill_data[15]);
				var bill_data=document.getElementById('is_posted_account').value;
				
				if (bill_data == 1) document.getElementById("accounting_posted_status").innerHTML = "Already Posted In Accounting."; 
				else 
				document.getElementById("accounting_posted_status").innerHTML = "";
				
				set_button_status(1, permission, 'fnc_garments_bill_issue',1);
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
				
				
					
				
				$('#cbo_company_name').attr('disabled','disabled');
				$('#cbo_location_name').attr('disabled','disabled');
				$('#txt_bill_date').attr('disabled','disabled');
				$('#cbo_party_name').attr('disabled','disabled');
				$('#cbo_party_source').attr('disabled','disabled');
				$('#txt_exchange_rate').attr('disabled','disabled');
				$('#txt_del_date_from').attr('disabled','disabled');
				$('#txt_del_date_to').attr('disabled','disabled');
				$('#cbo_currency').attr('disabled','disabled');
				
				
				set_all_onclick();
				release_freezing();
			}
			
		}
	}
	
	
	function fnc_list_search(type)
	{
		//alert(type);
		if( form_validation('cbo_company_name*cbo_party_name*txt_del_date_from*txt_del_date_to','Company Name*Party Name*date_from*date_to')==false)
		{
			return;
		}
		//alert($('#update_id').val());
		if (($('#update_id').val()*1)==0)
		{
			show_list_view(document.getElementById('cbo_company_name').value+'***'+document.getElementById('cbo_location_name').value+'***'+document.getElementById('cbo_party_source').value+'***'+document.getElementById('cbo_party_name').value+'***'+document.getElementById('txt_exchange_rate').value+'***'+document.getElementById('txt_bill_date').value+'***'+document.getElementById('txt_del_date_from').value+'***'+document.getElementById('txt_del_date_to').value+'***'+document.getElementById('cbo_currency').value+'***'+document.getElementById('update_id').value,'garments_bill_list_view','garments_info_list','requires/ex_factory_against_garments_bill_entry_controller', 'setFilterGrid("tbl_list_search",-1)');
		}
		else
		{
			var tot_row=$('#bill_issue_table tr').length;
			var all_value=""; var updateiddtls="";
			for (var n=1; n<=tot_row; n++)
			{
				
				if(all_value=="") all_value+=$('#deliveryid_'+n).val(); else all_value+='!!!!'+$('#deliveryid_'+n).val();
				if(updateiddtls=="") updateiddtls+=$('#updateiddtls_'+n).val(); else updateiddtls+='!!!!'+$('#updateiddtls_'+n).val();
			}
			
			show_list_view(document.getElementById('cbo_company_name').value+'***'+document.getElementById('cbo_location_name').value+'***'+document.getElementById('cbo_party_source').value+'***'+document.getElementById('cbo_party_name').value+'***'+document.getElementById('txt_exchange_rate').value+'***'+document.getElementById('txt_bill_date').value+'***'+document.getElementById('txt_del_date_from').value+'***'+document.getElementById('txt_del_date_to').value+'***'+document.getElementById('cbo_currency').value+'***'+document.getElementById('update_id').value+'***'+type+'***'+all_value+'***'+updateiddtls,'garments_bill_list_view','garments_info_list','requires/ex_factory_against_garments_bill_entry_controller','setFilterGrid("tbl_list_search",-1)','','');
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
	
	var seq_arr=new Array();
	
	function window_close( uid )
	{
		//alert(uid);
		var html="";
		var incid=0;
		if(uid==0) incid=i; else incid=m;
		if(uid==0)
		{
			var list_tot_row=$('#tbl_list_search tr').length-1;
			var counter =$('#bill_issue_table tr').length; 
			//alert(counter+'=='+list_tot_row); //return;
			var i=0; var p=0;
			if(seq_arr!=0) i=counter; else i=i;
			
			for (var k=1; k<=list_tot_row; k++)
			{
				var check_id=''; var strval=''; var trid="";
				var check_id= $('#checkid'+k).val(); 
				var strval=$('#strid'+k).val();
				var split_str=strval.split("_");
				trid=split_str[1]+"_"+split_str[3]+"_"+split_str[4]+"_"+split_str[9]+"_"+split_str[10]+"_"+split_str[11]+"_"+split_str[22];
				//alert(check_id+'=='+strval)
				//trid=split_str[0];
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
						$("#bill_issue_table tr").remove();
					}
					//alert(check_id);
					if( jQuery.inArray(  trid , selected_id_listed ) == -1) 
					{
						i++;
						selected_id_listed.push( trid );
						var delev_mst_id=trim(split_str[1]);
						var delev_no=split_str[2];
						var delev_date=split_str[3];
						var po_break_down_id=split_str[4];
						var po_number=split_str[5];
						var job_no_mst=split_str[6];
						var style_ref_no=split_str[7];
						var buyer_id=split_str[8];
						var item_number_id=split_str[9];
						var color_number_id=split_str[10];
						var country_id=split_str[11];
						var company_id=split_str[12];
						var delivery_company_id=split_str[13];
						var buyer_name=split_str[14];
						var garments_name=split_str[15];
						var color_name=split_str[16];
						var country_name=split_str[17];
						var delivery_company_name=split_str[18];
						var lc_company_name=split_str[19];
						var plan_cut_qnty=split_str[20];
						var color_mst_id=split_str[21];
						var job_id=split_str[22];
						var internal_no=split_str[23];
					
					
						html+='<tr align="center" id="trent_'+trid+'"><td><input type="hidden" name="updateiddtls_'+i+'" id="updateiddtls_'+i+'" value=""><input type="hidden" name="colormstid_'+i+'" id="colormstid_'+i+'" value="'+color_mst_id+'"><input type="hidden" name="jobid_'+i+'" id="jobid_'+i+'" value="'+job_id+'"><input type="hidden" name="deliveryid_'+i+'" id="deliveryid_'+i+'" value="'+delev_mst_id+'"><input type="hidden" name="ordernoid_'+i+'" id="ordernoid_'+i+'" value="'+po_break_down_id+'"><input type="hidden" name="cboitemname_'+i+'" id="cboitemname_'+i+'" value="'+item_number_id+'"><input type="hidden" name="txtColorId_'+i+'" id="txtColorId_'+i+'" value="'+color_number_id+'"><input type="hidden" name="cbocountry_'+i+'" id="cbocountry_'+i+'" value="'+country_id+'"><input type="text" name="deleverydate_'+i+'" id="deleverydate_'+i+'"  class="datepicker" style="width:80px" value="'+delev_date+'" disabled/></td><td>'+delev_no+'</td><td>'+po_number+'</td><td>'+internal_no+'</td><td>'+style_ref_no+'</td><td>'+buyer_name+'</td><td>'+garments_name+'</td><td>'+country_name+'</td><td>'+color_name+'</td><td><input type="text" name="deliveryqnty_'+i+'" id="deliveryqnty_'+i+'"  class="text_boxes" style="width:70px;" value="'+plan_cut_qnty+'" onBlur="qnty_caluculation('+i+');" readonly /></td><td><input type="text" name="txtrate_'+i+'" id="txtrate_'+i+'"  class="text_boxes" style="width:60px;" onBlur="qnty_caluculation('+i+');"  value="" /></td><td><input type="text" name="amount_'+i+'" id="amount_'+i+'"  class="text_boxes" style="width:70px;" value="" readonly /></td><td><input type="text" name="remarks_'+i+'" id="remarks_'+i+'"  class="text_boxes" value="" /></td></tr>';
						p++;
					}
				}
			}
			seq_arr=p;
		}
		else
		{
			$("#bill_issue_table tr").remove();
			var list_view_str = return_global_ajax_value( uid+"!^!"+1, 'load_dtls_data', '', 'requires/ex_factory_against_garments_bill_entry_controller');
			var split_list_view=list_view_str.split('###');
		//alert(split_list_view);
			var m=1; var mn=0;
			for (var n=1; n<=split_list_view.length; n++)
			{
				var split_list_str=split_list_view[mn].split('_');
					var trid="";
				trid=split_list_str[1]+"_"+split_list_str[3]+"_"+split_list_str[4]+"_"+split_list_str[9]+"_"+split_list_str[10]+"_"+split_list_str[11]+"_"+split_list_str[22];
				
				//alert(split_list_str);
				//trid=split_list_str[0];
				var delev_mst_id=trim(split_list_str[1]);
				var delev_no=split_list_str[2];
				var delev_date=split_list_str[3];
				var po_break_down_id=split_list_str[4];
				var po_number=split_list_str[5];
				var job_no_mst=split_list_str[6];
				var style_ref_no=split_list_str[7];
				var buyer_id=split_list_str[8];
				var item_number_id=split_list_str[9];
				var color_number_id=split_list_str[10];
				var country_id=split_list_str[11];
				var company_id=split_list_str[12];
				var delivery_company_id=split_list_str[13];
				var buyer_name=split_list_str[14];
				var garments_name=split_list_str[15];
				var color_name=split_list_str[16];
				var country_name=split_list_str[17];
				var delivery_company_name=split_list_str[18];
				var lc_company_name=split_list_str[19];
				var plan_cut_qnty=split_list_str[20];
				var color_mst_id=split_list_str[21];
				var job_id=split_list_str[22];
				var rate=split_list_str[23];
				var amount=split_list_str[24];
				var remarks=split_list_str[25];
				var upd_id=split_list_str[26];
				var internal_no=split_list_str[27];
			//	alert(upd_id);
				html+='<tr align="center" id="trent_'+trid+'"><td><input type="hidden" name="updateiddtls_'+m+'" id="updateiddtls_'+m+'" value="'+upd_id+'"><input type="hidden" name="colormstid_'+m+'" id="colormstid_'+m+'" value="'+color_mst_id+'"><input type="hidden" name="jobid_'+i+'" id="jobid_'+i+'" value="'+job_id+'"><input type="hidden" name="deliveryid_'+m+'" id="deliveryid_'+m+'" value="'+delev_mst_id+'"><input type="hidden" name="ordernoid_'+m+'" id="ordernoid_'+m+'" value="'+po_break_down_id+'"><input type="hidden" name="cboitemname_'+m+'" id="cboitemname_'+m+'" value="'+item_number_id+'"><input type="hidden" name="txtColorId_'+m+'" id="txtColorId_'+m+'" value="'+color_number_id+'"><input type="hidden" name="cbocountry_'+m+'" id="cbocountry_'+m+'" value="'+country_id+'"><input type="text" name="deleverydate_'+m+'" id="deleverydate_'+m+'"  class="datepicker" style="width:80px" value="'+delev_date+'" disabled/></td><td>'+delev_no+'</td><td>'+po_number+'</td><td>'+internal_no+'</td><td>'+style_ref_no+'</td><td>'+buyer_name+'</td><td>'+garments_name+'</td><td>'+country_name+'</td><td>'+color_name+'</td><td><input type="text" name="deliveryqnty_'+m+'" id="deliveryqnty_'+m+'"  class="text_boxes" style="width:70px;" value="'+plan_cut_qnty+'" onBlur="qnty_caluculation('+m+');" readonly /></td><td><input type="text" name="txtrate_'+m+'" id="txtrate_'+m+'"  class="text_boxes" style="width:60px;" onBlur="qnty_caluculation('+m+');"  value="'+rate+'" /></td><td><input type="text" name="amount_'+m+'" id="amount_'+m+'"  class="text_boxes" style="width:70px;" value="'+amount+'" readonly /></td><td><input type="text" name="remarks_'+m+'" id="remarks_'+m+'"  class="text_boxes" value="'+remarks+'" /></td></tr>';
				
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
				
				$('#deliveryqnty_'+q).removeAttr("onBlur").attr("onBlur","qnty_caluculation("+q+");");
				$('#txtrate_'+q).removeAttr('onBlur').attr('onBlur',"qnty_caluculation("+q+");");
			})
			//alert(uom_arr[q]);
		}
		
		
		
		
		var tot_row=$('#bill_issue_table tr').length;
		//alert(tot_row);
		//math_operation( "total_qnty", "deliveryqnty_", "+", tot_row );
		//math_operation( "total_amount", "amount_", "+", tot_row );
		fnc_qty_amount();
		set_all_onclick();
	}
	
	
	function qnty_caluculation(id)
	{
		//var delv_qty=$('#deliveryqnty_'+id).val()*1;
		//var rate=$('#txtrate_'+id).val()*1;
		//$("#amount_"+id).val((delv_qty*1)*($("#txtrate_"+id).val()*1));
		//var tot_row=$('#bill_issue_table tr').length;
		
		
		var rate=($("#txtrate_"+id).val()*1);
		var amt=($("#deliveryqnty_"+id).val()*1)*rate;
		$("#amount_"+id).val(number_format(amt,2,'.','' ));
		//alert(tot_row);
		//math_operation( "total_qnty", "deliveryqnty_", "+", tot_row );
		//math_operation( "total_amount", "amount_", "+", tot_row );
		fnc_qty_amount();
	}
	
	
	
	function fnc_qty_amount()
	{
		var tot_row=$('#bill_issue_table tr').length;
		var totqty=0; var totamt=0;
		for(var i=1; i<=tot_row; i++)
		{
			var qty=0; var amt=0;
			qty=$('#deliveryqnty_'+i).val()*1;
			amt=$('#amount_'+i).val()*1;
			totqty+=qty;
			totamt+=amt;
		}
		$('#total_qnty').val(number_format(totqty,2,'.','' ));
		$('#total_amount').val(number_format(totamt,2,'.','' ));
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
			//trid=split_str[1]+"_"+split_str[3]+"_"+split_str[4]+"_"+split_str[5]+"_"+split_str[7]+"_"+split_str[8]+"_"+split_str[9]+"_"+split_str[10]+"_"+split_str[11]+"_"+split_str[21]+"_"+split_str[22]+"_"+split_str[23];
			
			trid=split_str[1]+"_"+split_str[3]+"_"+split_str[4]+"_"+split_str[9]+"_"+split_str[10]+"_"+split_str[11]+"_"+split_str[22];
			
			//trid=trim(split_str[0]);
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
	
	function fnc_garments_bill_issue(operation)
	{
		
		
		if(operation==4)
		{
			if ( $('#txt_bill_no').val()=='')
			{
				alert ('Bill ID Not Save.');
				return;
			}
			
			var tot_row=$('#bill_issue_table tr').length;
			var all_value="";
			for (var n=1; n<=tot_row; n++)
			{
				if(all_value=="") all_value+=$('#deliveryid_'+n).val(); else all_value+='!!!!'+$('#deliveryid_'+n).val();
			}
			
			
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+all_value, "garments_bill_issue_print", "requires/ex_factory_against_garments_bill_entry_controller") 
			//return;
			show_msg("3");
		}
		
		if(operation==0 || operation==1 || operation==2)
		{
			if( form_validation('cbo_company_name*txt_bill_date*cbo_party_name*cbo_party_source*cbo_currency','Company Name*Bill Date*Party Name*Party Source*currency')==false)
			{
				return;
			}
			else
			{
				var source=$('#cbo_party_source').val();
				var tot_row=$('#bill_issue_table tr').length;
				var data1="action=save_update_delete&operation="+operation+"&tot_row="+tot_row+get_submitted_data_string('txt_bill_no*cbo_company_name*cbo_location_name*txt_bill_date*cbo_party_name*cbo_party_source*txt_exchange_rate*txt_del_date_from*txt_del_date_to*update_id*cbo_currency*txt_remarks',"../");
				//alert(data1); return;
				var data2=''; 
				for(var i=1; i<=tot_row; i++)
				{
					if(source==1)
					{
							if($('#deliveryqnty_'+i).val()==0 || $('#deliveryqnty_'+i).val()=='')
							{
								alert ("Cha. Qty (Pcs) Not Blank or Zero.");
								$('#deliveryqnty_'+i).focus();
								return;
							}
					}
					if (form_validation('deliveryid_'+i+'*txtrate_'+i,'challen no*Rate')==false)
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
						data2+=get_submitted_data_string('deleverydate_'+i+'*ordernoid_'+i+'*cboitemname_'+i+'*deliveryqnty_'+i+'*txtrate_'+i+'*amount_'+i+'*remarks_'+i+'*updateiddtls_'+i+'*deliveryid_'+i+'*txtColorId_'+i+'*cbocountry_'+i+'*colormstid_'+i+'*jobid_'+i,"../");//	
					}
					
				}
				var data=data1+data2;
				//alert (data);return;
				freeze_window(operation);
				http.open("POST","requires/ex_factory_against_garments_bill_entry_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_garments_bill_issue_response;
			}
		}
	}
	function fnc_garments_bill_issue_response()
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
				$('#cbo_company_name').attr('disabled','disabled');
				$('#cbo_location_name').attr('disabled','disabled');
				$('#txt_bill_date').attr('disabled','disabled');
				$('#cbo_party_name').attr('disabled','disabled');
				$('#cbo_party_source').attr('disabled','disabled');
				$('#txt_exchange_rate').attr('disabled','disabled');
				$('#txt_del_date_from').attr('disabled','disabled');
				$('#txt_del_date_to').attr('disabled','disabled');
				$('#cbo_currency').attr('disabled','disabled');
				set_button_status(1, permission, 'fnc_garments_bill_issue',1);
			}
			
			release_freezing();
		}
	}
	function exchange_rate(val)
	{
		if(val==1)
		{
			$("#txt_exchange_rate").val(1);
		}
		else
		{
			var bill_date = $('#txt_bill_date').val();
			var company_name = $('#cbo_company_name').val();
			var response=return_global_ajax_value( val+"**"+bill_date+"**"+company_name, 'check_conversion_rate', '', 'requires/ex_factory_against_garments_bill_entry_controller');
			$('#txt_exchange_rate').val(response);
		}
	}
	
</script>
</head>
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
    <? echo load_freeze_divs ("../",$permission);  ?>
	<form name="garmentsbillissue_1" id="garmentsbillissue_1"  autocomplete="off"  >
    <fieldset style="width:800px;">
    <legend>Garments Derivery For Bill Info</legend>
        <table cellpadding="0" cellspacing="2" width="790">
            <tr>
                <td align="right" colspan="3"><strong>Bill No </strong></td>
                <td width="140" align="justify">
                   <input type="text" name="txt_bill_no" id="txt_bill_no" class="text_boxes" style="width:140px" placeholder="Double Click to Search" onDblClick="openmypage_bill();" readonly tabindex="1" >
                   <input type="hidden" name="update_id" id="update_id" />
                    <input type="hidden" name="selected_order_id" id="selected_order_id" />
                    <input type="hidden" id="is_posted_account" name="is_posted_account" value=""/>
                </td>
             </tr>
             <tr>
                <td>&nbsp;</td>  
                <td>&nbsp;</td>  
                <td>&nbsp;</td>                                              
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
             <tr>
                <td width="110" class="must_entry_caption">Del.Com.Name</td>
                <td width="150">
                    <?php 
					   echo create_drop_down( "cbo_company_name",150,"select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/ex_factory_against_garments_bill_entry_controller', this.value, 'load_drop_down_location', 'location_td');exchange_rate(document.getElementById('cbo_currency').value);","","","","","",2);
					   
                    ?>
                </td>
                <td width="110">Location</td>                                              
                <td width="150" id="location_td">
                    <? 
						echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "--Select Location--", $selected,"","","","","","",3);
                    ?>
                </td>
                <td class="must_entry_caption">Bill Date</td>
                <td>
                    <input class="datepicker" type="text" style="width:140px" name="txt_bill_date" id="txt_bill_date" tabindex="4" onChange="exchange_rate(document.getElementById('cbo_currency').value)"  value="<? echo date('d-m-Y');?>" /> 
                </td>
            </tr> 
            <tr>
                <td>Party Source</td>
                <td>
					<? echo create_drop_down( "cbo_party_source", 150, $knitting_source,"", 1, "-- Select Party --", $selected, "load_drop_down( 'requires/ex_factory_against_garments_bill_entry_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_party_name', 'party_td' );",1,"1","","","",5); ?>
				</td>
                 <td  class="must_entry_caption">Party Name</td>
                <td id="party_td"><? echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "--Select Party--", $selected, "",0,"","","","",6); ?></td>
                <td class="must_entry_caption">Currency</td>                                              
                <td id="currency_td">
                <?
                echo create_drop_down("cbo_currency", 150, $currency,"", 1, "-- Select Currency --",$selected,"exchange_rate(this.value)", "","","","","",7 ); 
                ?>
                </td>
            </tr>
            <tr>
                <td class="must_entry_caption">Challan Date Range</td>
                <td>
                    <input class="datepicker" type="text" style="width:52px" name="txt_del_date_from" id="txt_del_date_from" tabindex="4" />  To  <input class="datepicker" type="text" style="width:52px" name="txt_del_date_to" id="txt_del_date_to" tabindex="4" />
                </td>
                 <td class="must_entry_caption">Conversion Rate</td>
                <td><input type="text" name="txt_exchange_rate" id="txt_exchange_rate" style="width:140px" class="text_boxes_numeric"  value="" /></td>
                <td width="110">&nbsp;</td>                                              
                <td><input class="formbutton" type="button" onClick="fnc_list_search(0);" style="width:153px" name="btn_populate" value="Populate" id="btn_populate" /></td>
            </tr>
             <tr>
                <td>Remarks</td>  
                <td colspan="5">
                   <input type="text" name="txt_remarks" id="txt_remarks" style="width:400px" class="text_boxes"/>
                </td>
                
            </tr>
             <tr>
                <td>&nbsp;</td>  
                <td>&nbsp;</td>  
                <td>&nbsp;</td>                                              
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </fieldset>
    <br>
    <fieldset style="width:1160px;">
    <legend>Garments Derivery For Bill Info</legend>
     <table  style="width:1150px;" cellpadding="0" cellspacing="2" border="0" class="rpt_table" rules="all" id="tbl_dtls_emb">
        <thead class="form_table_header">
            <th width="80">Delivery Date </th>
            <th width="90" class="must_entry_caption">Challan No.</th>
            <th width="90">Order No.</th>
            <th width="90">Internal Ref.</th>
            <th width="90">Style Ref.</th>
            <th width="90">Buyer Name</th>
            <th width="90">Garments Item</th>
            <th width="80">Country</th>
            <th width="80">Color</th>
            <th width="70">Challan Qty(Pcs)</th>
            <th width="60">Rate / Pcs</th>
            <th width="70">Amount</th>
            <th>Remarks</th>
        </thead>
        <tbody id="bill_issue_table">
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
                <td align="right">Total: &nbsp;</td>
                <td>
                    <input type="text" name="total_qnty" id="total_qnty"  class="text_boxes_numeric" style="width:70px" readonly disabled />
                </td>
                <td >&nbsp;</td>
                <td>
                    <input type="text" name="total_amount" id="total_amount"  class="text_boxes_numeric" style="width:70px" readonly  disabled/>
                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="16" height="15" align="center"><div id="accounting_posted_status" style="float:center; font-size:18px; color:#FF0000;"></div></td>
            </tr> 
            <tr>
                <td colspan="14" align="center" class="button_container">
                <? 
                    echo load_submit_buttons($permission,"fnc_garments_bill_issue",0,1,"reset_form('garmentsbillissue_1','garments_info_list','','','$(\'#bill_issue_table tr:not(:first)\').remove();')",1);
                ?> 
                </td>
            </tr>  
            <tr>
                <td colspan="14" id="list_view" align="center"></td>
            </tr>
        </tfoot>                                                             
    </table>
    </fieldset> 
    </form>
    <br>
    <div id="garments_info_list"></div>                           
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Actual Cost Entry
					
Functionality	:				

JS Functions	:

Created by		:	Fuad 
Creation date 	: 	17-03-2015
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
echo load_html_head_contents("Actual Cost Entry", "../../", 1, 1,'','','');
?>	
 
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission='<? echo $permission; ?>';

function calculate_date()
{	
	var cbo_company_id = $('#cbo_company_id').val();
	var cost_head = $('#cbo_cost_head').val();
	
	if (form_validation('cbo_company_id*cbo_cost_head','Company*Cost Head')==false)
	{
		$('#txt_incurred_date').val('');
		$('#txt_incurred_to_date').val('');
		return;
	}
			
	var thisDate=($('#txt_incurred_date').val()).split('-');
	var last=new Date( (new Date(thisDate[2], thisDate[1],1))-1 );
	
	var last_date = last.getDate();
	var month = last.getMonth()+1;
	var year = last.getFullYear();
	
	if(month<10)
	{
		var months='0'+month;
	}
	else
	{
		var months=month;
	}
	
	var last_full_date=last_date+'-'+months+'-'+year;
	var first_full_date='01'+'-'+months+'-'+year;
	
	$('#txt_incurred_date').val(first_full_date);
	$('#txt_incurred_to_date').val(last_full_date);

	if(cost_head==1 || cost_head==2 || cost_head==3 || cost_head==4 || cost_head==7 || cost_head==10)
	{
		get_php_form_data(cbo_company_id+"**"+cost_head+"**"+first_full_date+"**"+last_full_date, "populate_data_from_actual_cost", "requires/actual_cost_entry_controller" );
		
		var txt_applying_period_date=$('#txt_applying_period_date').val();
		var txt_applying_period_to_date=$('#txt_applying_period_to_date').val();
		if(txt_applying_period_date!="" && txt_applying_period_to_date!="")
		{
			show_list_view(cbo_company_id+"**"+cost_head+"**"+txt_applying_period_date+"**"+txt_applying_period_to_date+"**"+first_full_date+"**"+last_full_date+"**0******",'show_po_listview','list_view','requires/actual_cost_entry_controller','setFilterGrid("table_body",-1);');
		}
	}
	else
	{	
		$('#txt_applying_period_date').val(first_full_date);
		$('#txt_applying_period_to_date').val(last_full_date);
		
		var val=2;//USD
		var hdn_variable_ac_cost=$("#hdn_variable_ac_cost").val();
		var response_data=return_global_ajax_value(val+"**"+last_full_date+"**"+cbo_company_id+"**"+first_full_date+"**"+cost_head+"**"+hdn_variable_ac_cost, 'check_conversion_rate', '', 'requires/actual_cost_entry_controller');
		//alert(response_data);return;
		var response_lib_data=response_data.split("**");
		$('#txt_exchange_rate_order').val(response_lib_data[0]);
		if(cost_head==5 && hdn_variable_ac_cost==1)
		{
			$('#txt_amount').val(response_lib_data[1]).attr("disabled",true).attr("readonly","readonly");
		}
		else
		{
			$('#txt_amount').val("").attr("disabled",false).removeAttr("readonly");;
		}
	}
}

function show_po_list()
{
	var cbo_company_id = $('#cbo_company_id').val();
	var cost_head = $('#cbo_cost_head').val();
	
	if(form_validation('cbo_company_id*cbo_cost_head*txt_incurred_date','Company*Cost Head*Incurred Date')==false)
	{
		$('#txt_applying_period_date').val('');
		$('#txt_applying_period_to_date').val('');
		return;
	}
	
	if(cost_head==1 || cost_head==2 || cost_head==3 || cost_head==4 || cost_head==7|| cost_head==10)
	{
		var txt_incurred_date=$('#txt_incurred_date').val();
		var txt_incurred_to_date=$('#txt_incurred_to_date').val();
		var txt_applying_period_date=$('#txt_applying_period_date').val();
		var txt_applying_period_to_date=$('#txt_applying_period_to_date').val();
		
		if(txt_applying_period_date!="" && txt_applying_period_to_date!="")
		{
			show_list_view(cbo_company_id+"**"+cost_head+"**"+txt_applying_period_date+"**"+txt_applying_period_to_date+"**"+txt_incurred_date+"**"+txt_incurred_to_date+"**0******",'show_po_listview','list_view','requires/actual_cost_entry_controller','setFilterGrid("table_body",-1);');
			calculate_balance(1);
		}
	}
}


function fnc_actual_cost_entry( operation )
{
	var cost_head = $('#cbo_cost_head').val();
	if(cost_head==17 || cost_head==18 || cost_head==19 || cost_head==1 || cost_head==9)
	{
		if (form_validation('cbo_company_id*cbo_cost_head*txt_amount','Company*Cost Head*Amount (TK.)')==false)
		{
			return;
		}
		
		var txt_job_id=trim($('#txt_job_id').val());
		if(txt_job_id=="")
		{
			alert("Please Select Style Or Job");return;
		}
		
		var dataString=''; var j=0; 
		var txt_amount=$('#txt_amount').val()*1;
		var tot_amount=$('#tot_amount').val()*1;
		if(txt_amount.toFixed(2)!=tot_amount.toFixed(2))
		{
			alert('Amount Distribution Not Equal.');
			return;
		}

		var tot_row=$('table#table_body tbody tr').length-1;
		
		for(i=1; i<=tot_row; i++)
		{
			var txt_amount=$('#txt_amount_'+i).val()*1;
			var po_id=$('#po_id_'+i).val();
			var jobNo=$('#job_no_'+i).text();
			var txt_dtls_id=$('#txt_dtls_id_'+i).val()*1;
			
			if(txt_amount>0)
			{
				j++;
				dataString+='&txt_amount' + j + '=' + txt_amount + '&po_id' + j + '=' + po_id + '&jobNo' + j + '=' + jobNo+ '&txt_dtls_id' + j + '=' + txt_dtls_id;
			}
		}
		
		if(j<1)
		{
			alert("Please Insert At Least One PO Amount");
			return;
		}
	
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_id*cbo_cost_head*cbo_buyer_name*txt_style_no*txt_job_no*txt_job_id*txt_amount',"../../")+dataString+'&tot_row='+j;
	}
	else
	{
		if (form_validation('cbo_company_id*cbo_cost_head*txt_incurred_date*txt_applying_period_date*txt_exchange_rate_order*txt_amount','Company*Cost Head*Incurred Date*Applying Period*Exchange Rate*Amount (TK.)')==false)
		{
			return;
		}
		
		 var dataString=''; var j=0; 
		if( cost_head==2 || cost_head==3 || cost_head==4 || cost_head==7 || cost_head==10 )
		{
			var txt_amount=$('#txt_amount').val()*1;
			var tot_amount=$('#tot_amount').val()*1;
			if(txt_amount.toFixed(2)!=tot_amount.toFixed(2))
			{
				alert('Amount Distribution Not Equal.');
				return;
			}
	
			var tot_row=$('table#table_body tbody tr').length-1;
			
			for(i=1; i<=tot_row; i++)
			{
				var txt_amount=$('#txt_amount_'+i).val()*1;
				var po_id=$('#po_id_'+i).val();
				var jobNo=$('#job_no_'+i).text();
				var txt_dtls_id=$('#txt_dtls_id_'+i).val()*1;
				
				if(txt_amount>0)
				{
					j++;
					dataString+='&txt_amount' + j + '=' + txt_amount + '&po_id' + j + '=' + po_id + '&jobNo' + j + '=' + jobNo+ '&txt_dtls_id' + j + '=' + txt_dtls_id;
				}
			}
			
			if(j<1)
			{
				alert("Please Insert At Least One PO Amount");
				return;
			}
		}
		else
		{
			if (form_validation('cbo_based_on','Based On')==false)
			{
				return;
			}
		}
	
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_id*cbo_cost_head*txt_incurred_date*txt_incurred_to_date*txt_exchange_rate_order*txt_amount*txt_applying_period_date*txt_applying_period_to_date*cbo_based_on',"../../")+dataString+'&tot_row='+j;
	}
	
	
	//alert(data);return;
	
	freeze_window(operation);
	http.open("POST","requires/actual_cost_entry_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_actual_cost_entry_reponse;
}
	 
function fnc_actual_cost_entry_reponse()
{
	if(http.readyState == 4) 
	{
		//release_freezing();return;
		var response=trim(http.responseText).split('**');
		show_msg(response[0]);
		if((response[0]==0 || response[0]==1))
		{
			set_button_status(1, permission, 'fnc_actual_cost_entry',1);
			var cost_head = $('#cbo_cost_head').val();
			if(cost_head==5 || cost_head==6 || cost_head==8  || cost_head==11 || cost_head==12 || cost_head==13 || cost_head==14 || cost_head==15 || cost_head==16)
			{
				show_list_view('','show_details_listview','cm_commercial_list_view','requires/actual_cost_entry_controller','setFilterGrid("table_body",-1);');
			}
		}
		else if(response[0]==2)
		{
			reset_form('actualCostEntry_1','list_view','','','','cbo_company_id*cbo_cost_head*hdn_variable_ac_cost');
		}
		release_freezing();
	}
}

function calculate_balance(i)
{
	var numRow = $('table#table_body tbody tr').length; 
	var tot_amount=0;
	for(var i=1; i<numRow; i++)
	{
		var amnt=$('#txt_amount_'+i).val();
		tot_amount=tot_amount*1+amnt*1;
	}

	$('#tot_amount').val(tot_amount);
	var tot_remain=$('#txt_amount').val()*1-tot_amount;
	$('#tot_remain').text(tot_remain);
}

function show_hide(cost_head)
{
	if(cost_head==5 || cost_head==6 || cost_head==8 || cost_head==9 || cost_head==11 || cost_head==12 || cost_head==13 || cost_head==14 || cost_head==15 || cost_head==16)
	{
		//if(cost_head==5) var width="1030px"; else var width="780px";
		var cost_head_name="";
		if(cost_head==5) cost_head_name="CM";
		else if(cost_head==6) cost_head_name="Commercial";
		else if(cost_head==8) cost_head_name="Opt. Exp.";
		else if(cost_head==9) cost_head_name="Other Cost";
		//else if(cost_head==10) cost_head_name="Commission";
		else if(cost_head==11) cost_head_name="Admin Cost";
		else if(cost_head==12) cost_head_name="Marketing"; 
		else if(cost_head==13) cost_head_name="Financial Expense";
		else if(cost_head==14) cost_head_name="Depreciation";
		
		var width="1030px";
		var html='<div style="width:780px;" align="center"><fieldset style="width:100%; margin-top:5px"><legend>'+cost_head_name+' Cost Info</legend><b>Company Name</b><input type="text" style="width:140px" placeholder="Write" name="seacrh_string" id="seacrh_string" class="text_boxes"/><input type="button" class="formbutton" style="width:100px" value="Search" onclick="show_details();"><div style="margin-top:10px" id="cm_commercial_list_view" align="left"></div></fieldset></div><div style="width:'+width+';" align="center"><fieldset style="width:100%; margin-top:5px"><legend>'+cost_head_name+' Cost Details Info</legend><div style="margin-top:10px" id="cm_commercial_list_view_details" align="left"></div></fieldset></div>';
		$('#list_view').html(html);
	}
	else if(cost_head==17 || cost_head==18 || cost_head==19)
	{
		var cost_head_name="";
		if(cost_head==17) cost_head_name="Discount Allowed";
		else if(cost_head==18) cost_head_name="Short Realized";
		else if(cost_head==18) cost_head_name="Incentives Missing";
		
		var width="1000px";
		var html='<div style="width:1000px;" align="center"><div style="width:'+width+';" align="left"><fieldset style="width:100%; margin-top:5px"><legend>'+cost_head_name+' Cost Details Info</legend><div style="margin-top:10px" id="cm_commercial_list_view_details" align="left"></div></fieldset></div>';
		$('#list_view').html(html);
	}
}

function show_details()
{
	show_list_view($('#seacrh_string').val(),'show_details_listview','cm_commercial_list_view','requires/actual_cost_entry_controller','setFilterGrid("table_body",-1);');
}

function show_list_view_details()
{
	/*var isDisbled=$('#update1').attr('class');
	alert(isDisbled);return;*/
	if($('#update1').hasClass('formbutton_disabled'))
	{
		alert("Please Save First.");
		return;
	}
	else
	{
		show_list_view($('#cbo_company_id').val()+"**"+$('#cbo_cost_head').val()+"**"+$('#cbo_based_on').val()+"**"+$('#txt_incurred_date').val()+"**"+$('#txt_incurred_to_date').val(),'show_details_listview_po','cm_commercial_list_view_details','requires/actual_cost_entry_controller','setFilterGrid("table_body_dtls",-1);');
	}
}

function generate_report(type)
{
	var cbo_company_id = $('#cbo_company_id').val();
	var cost_head = $('#cbo_cost_head').val();
	var cbo_buyer_id = $('#cbo_buyer_id').val();
	var txt_style = $('#txt_style').val();
	var txt_job = $('#txt_job').val();
	var txt_order = $('#txt_order').val();
	
	if(cbo_buyer_id==0 && txt_style=="" && txt_job=="" && txt_order=="" )
	{
		alert("Select At Least One Search Criteria");return;
	}
	
	if(form_validation('cbo_company_id*cbo_cost_head*txt_incurred_date','Company*Cost Head*Incurred Date')==false)
	{
		$('#txt_applying_period_date').val('');
		$('#txt_applying_period_to_date').val('');
		return;
	}
	
	if(cost_head==1 || cost_head==2 || cost_head==3 || cost_head==4 || cost_head==7 || cost_head==10)
	{
		var txt_incurred_date=$('#txt_incurred_date').val();
		var txt_incurred_to_date=$('#txt_incurred_to_date').val();
		var txt_applying_period_date=$('#txt_applying_period_date').val();
		var txt_applying_period_to_date=$('#txt_applying_period_to_date').val();
		
		if(txt_applying_period_date!="" && txt_applying_period_to_date!="")
		{
			show_list_view(cbo_company_id+"**"+cost_head+"**"+txt_applying_period_date+"**"+txt_applying_period_to_date+"**"+txt_incurred_date+"**"+txt_incurred_to_date+"**"+cbo_buyer_id+"**"+txt_style+"**"+txt_job+"**"+txt_order,'show_po_listview','list_view','requires/actual_cost_entry_controller','setFilterGrid("table_body",-1);');
			calculate_balance(1);
		}
	}
}
function fnCheckUnCheckAll(checkVal)
	{
		
		var tal_length=$("#table_body tbody tr").length*1;
	
		for(var i=1; i<=tal_length;i++)
		{
			if($("#row_"+i).is(':visible'))
			{
				if(checkVal)
				{
					$("#workOrderChkbox_"+i).prop('checked', true);
				}
				else
				{
					$("#workOrderChkbox_"+i).prop('checked', false);
				}
			}
		}
	}

function fn_amount_propotionate()
{	
	// $("input[name='workOrderChkbox[]']").each(function () {  
	// 	if ($(this).is(":checked")) {
	// 	var checkboxId = $(this).attr("id");
	// 	var rowNumber = checkboxId.split("_")[1];
	// 	}
	// });
	  var cost_head=$('#cbo_cost_head').val()*1;
	 if(cost_head ==1 || cost_head ==2 || cost_head ==3 || cost_head ==4 || cost_head ==7 || cost_head ==10 || cost_head ==9 )
	 {	
		var txt_amount=$('#txt_amount').val()*1;
		var tblRow = $("#table_body tbody tr").length-1;
		var tot_po_qnty=$('#td_tot_po_qnty').text()*1;
		//alert(txt_amount+"="+tblRow+"="+tot_po_qnty);return;
		var tot_distribute_amt=0; var rest_amt=0;
		for(var i=1; i<=tblRow; i++)
		{
			var po_qnty=$('#txt_amount_'+i).attr('title')*1;
			var issue_qnty=(txt_amount/tot_po_qnty)*po_qnty;
			if(i==tblRow)
			{				
				rest_amt=txt_amount-tot_distribute_amt;
				//alert(rest_amt+"="+txt_amount+"="+tot_distribute_amt);//return;
				$('#txt_amount_'+i).val(rest_amt);
			}
			else
			{
				tot_distribute_amt += number_format (issue_qnty, 4, '.', "")*1 ;
				//$('#txt_amount_'+i).val(issue_qnty.toFixed(4));
				$('#txt_amount_'+i).val(number_format (issue_qnty, 4, '.', ""));
			}
			
		}
		calculate_balance(tblRow);
	}
	else if(cost_head ==17 || cost_head ==18 || cost_head ==19)
	{
		
		var txt_amount=$('#txt_amount').val()*1;
		var tblRow = $("#table_body tbody tr").length-1;
		var tot_inv_qnty=0;var tot_po_qnty=0;//$('#td_tot_invoice_qnty').text()*1;
		
		var tot_distribute_amt=0; var rest_amt=0;
		for(var i=1; i<=tblRow; i++)
		{
			if($(`#workOrderChkbox_${i}`).is(":checked"))
			{
			//	console.log(i,'checked');
				tot_inv_qnty  = tot_inv_qnty * 1 + $(`#invoiceqnty_${i}`).text() * 1;
				tot_po_qnty   = tot_po_qnty * 1 + $(`#poqty_${i}`).text() * 1;
			}
			else
			{
				//console.log(i,'unchecked');
			}
		}
		// console.log('tot_po_qnty','=',tot_po_qnty);

		$("#td_tot_po_qnty").text(tot_po_qnty)
		$("#td_tot_invoice_qnty").text(tot_inv_qnty)

		
		for(var i=1; i<=tblRow; i++)
		{
			if($(`#workOrderChkbox_${i}`).is(":checked"))
			{
				console.log(i,'checked');
				var po_qnty=$('#txt_amount_'+i).attr('title')*1;
				var issue_qnty=(txt_amount/tot_inv_qnty)*po_qnty;
				// alert(issue_qnty+"_"+txt_amount+"_"+tot_inv_qnty+"_"+po_qnty+"_"+i)			
				if(i==tblRow)
				{
					rest_amt=txt_amount-tot_distribute_amt;
					$('#txt_amount_'+i).val(number_format (rest_amt, 4, '.', ""));
				}
				else
				{
					tot_distribute_amt += number_format (issue_qnty, 4, '.', "")*1 ;
					$('#txt_amount_'+i).val(number_format (issue_qnty, 4, '.', ""));
				}
			}else{
				$('#txt_amount_'+i).val('');
			}
			
			
		}
		$('#tot_amount').val(txt_amount);
	}
	else
	{
		alert("This Head Not Allow");return;
	}
}

function fnc_com_data()
{
	var com_id=$("#cbo_company_id").val();
	var com_variable_data=return_global_ajax_value(com_id, 'check_com_variable_data', '', 'requires/actual_cost_entry_controller');
	$("#hdn_variable_ac_cost").val(trim(com_variable_data));
}

function openmypage_style()
{		
	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
	}
		var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_name").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/actual_cost_entry_controller.php?data='+data+'&action=style_popup', 'style Search', 'width=480px,height=420px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theemailid=this.contentDoc.getElementById("txt_job_id");
			var theemailval=this.contentDoc.getElementById("txt_style_no");
			var theemailvalJob=this.contentDoc.getElementById("txt_job_no");
			if (theemailid.value!="" || theemailval.value!="")
			{
				//alert (theemailid.value);
				freeze_window(5);
				$("#txt_job_id").val(theemailid.value);
				$("#txt_style_no").val(theemailval.value);
				$("#txt_job_no").val(theemailvalJob.value);
				
				var cost_head=$('#cbo_cost_head').val()*1;
				var cbo_company_id=$('#cbo_company_id').val();
				var cbo_buyer_name=$('#cbo_buyer_name').val();
				var txt_job_id=trim(theemailid.value);
				var txt_job_no=trim(theemailvalJob.value);
				if(txt_job_id=="")
				{
					alert("Please Select Job/Style");return;
				}
				show_list_view(cbo_company_id+"**"+cost_head+"**"+txt_job_id+"**"+txt_job_no+"**"+cbo_buyer_name,'show_job_listview','list_view','requires/actual_cost_entry_controller','setFilterGrid("table_body",-1);');
				
				var response_data=return_global_ajax_value(txt_job_no+"**"+cost_head, 'job_previous_entry_check', '', 'requires/actual_cost_entry_controller');
				response_data=response_data.split("_");
				//alert(response_data);
				if(response_data[0]>0)
				{
					set_button_status(1, permission, 'fnc_actual_cost_entry',1);
				}
				else
				{
					set_button_status(0, permission, 'fnc_actual_cost_entry',1);
				}
				
				$('#txt_amount').val(response_data[1]);
				
				release_freezing();
			}
		}
}

</script>
 
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
     <? echo load_freeze_divs ("../../",$permission); ?>
    <form id="actualCostEntry_1">
        <fieldset style="width:650px;">
            <legend>Actual Cost Entry</legend>
            <table width="550" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td width="100" align="center" class="must_entry_caption">Company</td>
                    <td width="180">
                        <? 
                            echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name",'id,company_name', 1, '--- Select Company ---', 0, "reset_form('actualCostEntry_1','load_page','','','','cbo_company_id*hdn_variable_ac_cost');fnc_com_data();" );
                        ?>
                        <input type="hidden" id="hdn_variable_ac_cost" name="hdn_variable_ac_cost" value="0" />
                    </td> 
                    <td width="100" align="center" class="must_entry_caption">Cost Head</td>
                    <td>
                         <? 
						 	//function reset_form( forms, divs, fields, default_val, extra_func, non_refresh_ids )
						 	//reset_form('actualCostEntry_1','list_view*load_page','','','','cbo_company_id*cbo_cost_head');
                            echo create_drop_down( "cbo_cost_head", 152, $actual_cost_heads,'', '1', '---- Select ----', '',"reset_form('actualCostEntry_1','list_view*load_page','','','','cbo_company_id*cbo_cost_head*hdn_variable_ac_cost');show_list_view(this.value+'**'+$('#cbo_company_id').val(),'on_change_load_page','load_page','requires/actual_cost_entry_controller','');show_hide(this.value);",''); 
                        ?>
                    </td>
                </tr>
            </table>
            <div style="float:left; margin:auto" align="center" id="load_page"></div>
            <table>
            	<tr>
            		<td width="550" align="center" colspan="4" valign="middle" class="button_container">
                    	<? echo load_submit_buttons($permission, "fnc_actual_cost_entry", 0,0 ,"reset_form('actualCostEntry_1','load_page*list_view','','','disable_enable_fields(\'cbo_company_id\');','hdn_variable_ac_cost');",1) ; ?>
                    </td>
            	</tr>
            </table>
		</fieldset>
        <div style="margin-top:5px;" align="center" id="list_view"></div>
	</form>        
</div>
</body>
           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
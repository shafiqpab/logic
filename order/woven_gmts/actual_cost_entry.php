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

	if(cost_head==1 || cost_head==2 || cost_head==3 || cost_head==4)
	{
		get_php_form_data(cbo_company_id+"**"+cost_head+"**"+first_full_date+"**"+last_full_date, "populate_data_from_actual_cost", "requires/actual_cost_entry_controller" );
		
		var txt_applying_period_date=$('#txt_applying_period_date').val();
		var txt_applying_period_to_date=$('#txt_applying_period_to_date').val();
		if(txt_applying_period_date!="" && txt_applying_period_to_date!="")
		{
			show_list_view(cbo_company_id+"**"+cost_head+"**"+txt_applying_period_date+"**"+txt_applying_period_to_date+"**"+first_full_date+"**"+last_full_date,'show_po_listview','list_view','requires/actual_cost_entry_controller','setFilterGrid("table_body",-1);');
		}
	}
	else
	{	
		$('#txt_applying_period_date').val(first_full_date);
		$('#txt_applying_period_to_date').val(last_full_date);
		
		var val=2;//USD
		var response=return_global_ajax_value(val+"**"+last_full_date, 'check_conversion_rate', '', 'requires/actual_cost_entry_controller');
		$('#txt_exchange_rate_order').val(response);
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
	
	if(cost_head==1 || cost_head==2 || cost_head==3 || cost_head==4)
	{
		var txt_incurred_date=$('#txt_incurred_date').val();
		var txt_incurred_to_date=$('#txt_incurred_to_date').val();
		var txt_applying_period_date=$('#txt_applying_period_date').val();
		var txt_applying_period_to_date=$('#txt_applying_period_to_date').val();
		
		if(txt_applying_period_date!="" && txt_applying_period_to_date!="")
		{
			show_list_view(cbo_company_id+"**"+cost_head+"**"+txt_applying_period_date+"**"+txt_applying_period_to_date+"**"+txt_incurred_date+"**"+txt_incurred_to_date,'show_po_listview','list_view','requires/actual_cost_entry_controller','setFilterGrid("table_body",-1);');
			calculate_balance(1);
		}
	}
}


function fnc_actual_cost_entry( operation )
{
	if (form_validation('cbo_company_id*cbo_cost_head*txt_incurred_date*txt_applying_period_date*txt_exchange_rate_order*txt_amount','Company*Cost Head*Incurred Date*Applying Period*Exchange Rate*Amount (TK.)')==false)
	{
		return;
	}
	
	var cost_head = $('#cbo_cost_head').val(); var dataString=''; var j=0; 
	if(cost_head==1 || cost_head==2 || cost_head==3 || cost_head==4)
	{
		if($('#txt_amount').val()*1!=$('#tot_amount').val()*1)
		{
			alert('Amount Distribution Not Equal.');
			return;
		}

		var tot_row=$('table#table_body tbody tr').length; 
		for(i=1; i<=tot_row; i++)
		{
			var txt_amount=$('#txt_amount_'+i).val()*1;
			var po_id=$('#po_id_'+i).val();
			var jobNo=$('#job_no_'+i).text();
			
			if(txt_amount>0)
			{
				j++;
				dataString+='&txt_amount' + j + '=' + txt_amount + '&po_id' + j + '=' + po_id + '&jobNo' + j + '=' + jobNo;
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
			if(cost_head==5 || cost_head==6)
			{
				show_list_view('','show_details_listview','cm_commercial_list_view','requires/actual_cost_entry_controller','setFilterGrid("table_body",-1);');
			}
		}
		else if(response[0]==2)
		{
			reset_form('actualCostEntry_1','list_view','','','','cbo_company_id*cbo_cost_head');
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
	if(cost_head==5 || cost_head==6)
	{
		//if(cost_head==5) var width="1030px"; else var width="780px";
		
		var width="1030px";
		var html='<div style="width:780px;" align="center"><fieldset style="width:100%; margin-top:5px"><legend>CM and Commercial Cost Info</legend><b>Company Name</b><input type="text" style="width:140px" placeholder="Write" name="seacrh_string" id="seacrh_string" class="text_boxes"/><input type="button" class="formbutton" style="width:100px" value="Search" onclick="show_details();"><div style="margin-top:10px" id="cm_commercial_list_view" align="left"></div></fieldset></div><div style="width:'+width+';" align="center"><fieldset style="width:100%; margin-top:5px"><legend>CM and Commercial Cost Details Info</legend><div style="margin-top:10px" id="cm_commercial_list_view_details" align="left"></div></fieldset></div>';
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
                    <td width="80" align="center" class="must_entry_caption">Company</td>
                    <td>
                        <? 
                            echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name",'id,company_name', 1, '--- Select Company ---', 0, "reset_form('actualCostEntry_1','load_page','','','','cbo_company_id')" );
                        ?>
                    </td> 
                    <td width="90" align="center" class="must_entry_caption">Cost Head</td>
                    <td>
                         <? 
                            echo create_drop_down( "cbo_cost_head", 152, $actual_cost_heads,'', '1', '---- Select ----', '',"reset_form('actualCostEntry_1','list_view','','','','cbo_company_id*cbo_cost_head');show_list_view(this.value,'on_change_load_page','load_page','requires/actual_cost_entry_controller','');show_hide(this.value);",''); 
                        ?>
                    </td>
                </tr>
            </table>
            <div style="float:left; margin:auto" align="center" id="load_page"></div>
            <table>
            	<tr>
            		<td width="550" align="center" colspan="4" valign="middle" class="button_container">
                    	<? echo load_submit_buttons($permission, "fnc_actual_cost_entry", 0,0 ,"reset_form('actualCostEntry_1','load_page*list_view','','','disable_enable_fields(\'cbo_company_id\');');",1) ; ?>
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
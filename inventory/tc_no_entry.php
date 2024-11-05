<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create PI Wise Yarn Receive
				
Functionality	:	
JS Functions	:
Created by		:	MD Didarul Alam
Creation date 	: 	20-06-2022
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
echo load_html_head_contents("PI Wise Yarn Receive","../", 1, 1, $unicode,1,1); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function generate_report(type)
{

	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	else 
	{
		if( $('#cbo_transaction_type').val() ==1 ||  $('#cbo_transaction_type').val() ==3 )
		{
			if( $('#txt_pi_no').val() =="" && $('#btbLc_id').val() =="" && $('#txt_mrr_no').val() =="" && $('#txt_lot_no').val() =="" && $('#txt_tc_no').val() =="")
			{
				if( form_validation('txt_date_from*txt_date_to','Trans From Date*Trans To Date')==false )
				{
					return;
				}
			}
		}
		else if( $('#cbo_transaction_type').val() == 2 )
		{
			if( $('#txt_mrr_no').val() =="" && $('#txt_lot_no').val() =="" && $('#txt_int_ref').val() =="" && $('#txt_tc_no').val() =="" )
			{
				if( form_validation('txt_date_from*txt_date_to','Trans From Date*Trans To Date')==false )
				{
					return;
				}
			}
		}
		else
		{
			if( form_validation('txt_date_from*txt_date_to','Trans From Date*Trans To Date')==false )
			{
				return;
			}
		}				
	}

	var report_title=$( "div.form_caption" ).html();
	var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_location*cbo_store_name*txt_pi_no*pi_id*btbLc_id*txt_mrr_no*txt_lot_no*txt_int_ref*cbo_tc_no_type*cbo_transaction_type*txt_tc_no*txt_date_from*txt_date_to',"../")+'&report_title='+report_title+ "&type=" + type;
	freeze_window(3);
	http.open("POST","requires/tc_no_entry_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_report_reponse;  
}

function generate_report_reponse()
{	
	if(http.readyState == 4) 
	{	 
 		var reponse=trim(http.responseText).split("####");
		$("#report_container2").html(reponse[0]);  
		setFilterGrid("table_body",-1);
		show_msg('3');
		release_freezing();
	}
} 

function fnc_yarn_tc_no_entry( operation )
{
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	else 
	{
	    var chkArray = [];
		/* look for all checkboes that have a parent id called 'checkboxlist' attached to it and check if it was checked */
		 
		var checkedTcData="";
		var qcs_submit_date = "";  
 
		$('#table_body tbody tr input:checked').each(function() 
		{
			chkArray.push($(this).val());

			checked_data_arr = $(this).val().split("**");
			
			var tcDatas=$(this).val()+"**"+$("#tc_no_"+checked_data_arr[0]).val()+"**"+$("#qcs_submit_date_"+checked_data_arr[0]).val()+"**"+$("#tc_remarks_"+checked_data_arr[0]).val()+"**"+$("#qcs_no_"+checked_data_arr[0]).val()+"**"+$("#tc_used_data_"+checked_data_arr[0]).val();			
			
			if(checkedTcData!="")
			{
				checkedTcData+="___"+tcDatas;
				
			}
			else
			{
				checkedTcData=tcDatas;	
			}

		});

		var selected_detailsRow = chkArray.length;

		if(selected_detailsRow<1)
		{
			alert('Select at least one row first');
			return;
		}
	
		var data="action=save_update_delete&operation="+operation+'&tcdatastr='+checkedTcData;

		freeze_window(operation);

		http.open("POST","requires/tc_no_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_yarn_tc_no_entry_reponse;
	}		
}

function fnc_yarn_tc_no_entry_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');

		if(reponse[0]==0)
		{			
			show_msg(reponse[0]);	
			set_button_status(1, permission, 'fnc_yarn_tc_no_entry',1,1);
			//generate_report();
			release_freezing();
		}
		else if(reponse[0]==10)
		{
			show_msg(reponse[10]);
			release_freezing();
			return;
		}

		release_freezing();		
	}	
}

function openmypage_pinumber()
{
	var companyID = $('#cbo_company_name').val();

	if (form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	
	var page_link='requires/tc_no_entry_controller.php?action=pinumber_popup&companyID='+companyID;
	var title='PI Number Info';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var pi_id=this.contentDoc.getElementById("pi_id").value;
		var pi_no=this.contentDoc.getElementById("pi_no").value;
		
		$('#pi_id').val(pi_id);
		$('#txt_pi_no').val(pi_no);
	}
}
 
function openmypage_btbLc()
{
	var companyID = $('#cbo_company_name').val();

	if (form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	
	var page_link='requires/tc_no_entry_controller.php?action=btbLc_popup&companyID='+companyID;
	var title='BTB LC NO';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var btbLc_id=this.contentDoc.getElementById("btbLc_id").value;
		var btbLc_no=this.contentDoc.getElementById("btbLc_no").value;
		
		$('#btbLc_id').val(btbLc_id);
		$('#txt_btbLc_no').val(btbLc_no);
	}
}

function openmypage_tc_no(prod_id,update_id)
{
	var companyID = $('#cbo_company_name').val();

	if (form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	
	var page_link='requires/tc_no_entry_controller.php?action=tc_no_popup&companyID='+companyID+'&prod_id='+prod_id+'&issue_trans_id='+update_id;
	var title='TC Number Info';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=290px,center=1,resize=1,scrolling=0','');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var selected_tc_numbers=this.contentDoc.getElementById("selected_name").value;
		var selected_tc_data=this.contentDoc.getElementById("selected_tc_data").value;

		$('#tc_no_'+update_id).val(selected_tc_numbers);
		$('#tc_used_data_'+update_id).val(selected_tc_data);

	}
}

function check_all(tot_check_box_id)
{
    if ($('#'+tot_check_box_id).is(":checked"))
    { 
        $('#table_body tbody tr').each(function() {
            $('#table_body tbody tr input:checkbox').attr('checked', true);
        });
    }
    else
    { 
        $('#table_body tbody tr').each(function() {
            $('#table_body tbody tr input:checkbox').attr('checked', false);
        });
    } 
}

function tc_no_disabled_enabled()
{
	if ( $('#cbo_tc_no_type').val()*1 == 1 )
	{
		$('#txt_tc_no').prop({'disabled':false,'placeholder':'Write'});
	}else{
		$('#txt_tc_no').prop({'disabled':true,'placeholder':''});
	}
}
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../",$permission); ?><br />    		 
    <form name="tc_no_entry_1" id="tc_no_entry_1" autocomplete="off" > 
        <div style="width:100%;" align="center">
            <fieldset style="width:1590px;">
            <legend>Search Panel</legend> 
                <table class="rpt_table" width="1560" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="150" class="must_entry_caption">Company</th> 
                            <th width="140">Location</th>                               
                            <th width="140">Store</th>
                            <th width="100">PI Number</th>
                            <th width="100">BTB LC No</th>
                            <th width="100">MRR No</th>
                            <th width="100">Lot No</th>
                            <th width="100">Internal Ref</th>
                            <th width="100">TC No Type</th>
                            <th width="100">TC No</th>
                            <th width="100">Transaction Type</th>
							<th width="150" class="must_entry_caption">Trans Date</th>
                            <th width="70">
                            	<input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('tc_no_entry_1','report_container*report_container2','','','','');" />
                            </th>
                        </tr>
                    </thead>
                    <tr align="center">
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "reset_form('','','cbo_location*cbo_store_name*txt_pi_no*pi_id*txt_btbLc_no*btbLc_id','','',''); load_drop_down( 'requires/tc_no_entry_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                            ?>                            
                        </td>
                        <td id="location_td"> 
                            <?
                                echo create_drop_down( "cbo_location", 150, $blank_array,"", 1, "-- Select Location --", 0, "");
                            ?>
                        </td>
                        <td id="store_td"> 
                            <?
                                echo create_drop_down( "cbo_store_name", 150, $blank_array,"", 1, "-- Select Store --", 0, "" );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_pi_no" name="txt_pi_no" class="text_boxes" style="width:100px" placeholder="Write Or Browse" onDblClick="openmypage_pinumber()"  />
                            <input type="hidden" id="pi_id" readonly /> 
                        </td>
                        <td>
                            <input type="text" id="txt_btbLc_no" name="txt_btbLc_no" class="text_boxes" style="width:100px" placeholder="Double Click To Search" onDblClick="openmypage_btbLc()" readonly />
                            <input type="hidden" id="btbLc_id" readonly /> 
                        </td>
                        <td>
                            <input type="text" id="txt_mrr_no" name="txt_mrr_no" class="text_boxes" style="width:100px" placeholder="Write" />
                        </td>
                        <td>
                            <input type="text" id="txt_lot_no" name="txt_lot_no" class="text_boxes" style="width:100px" placeholder="Write"/>
                        </td>
                        <td>
                            <input type="text" id="txt_int_ref" name="txt_int_ref" class="text_boxes" style="width:100px" placeholder="Write"/>
                        </td>
                        <td>
                        	<?
                        	echo create_drop_down("cbo_tc_no_type", 100, $yes_no, "", 0, "-- Select--", 2, "tc_no_disabled_enabled();", "", "");
                        	?>
                        </td>
                        <td>
                            <input type="text" id="txt_tc_no" name="txt_tc_no" class="text_boxes" style="width:100px" placeholder="Write" disabled />
                        </td>
                        <td>
                        	<?
                        	echo create_drop_down("cbo_transaction_type", 100, $transaction_type, "", 0, "-- Select--", 1, "", "", "1,2,3");
                        	?>
                        </td>
						<td align="center" width="150">
							<input type="text" name="txt_date_from" id="txt_date_from" value="" placeholder="From Date" class="datepicker" style="width:50px"/>
							To
							<input type="text" name="txt_date_to" id="txt_date_to" value="" placeholder="To Date" class="datepicker" style="width:50px"/>
						</td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                        </td>
                    </tr>
					<tr>
						<td colspan="13"><? echo load_month_buttons(1);  ?></td>
					</tr>
                </table> 
            </fieldset>  
            
            <div id="report_container" align="center"></div>
        	<div id="report_container2"></div>
            
        </div>
    </form>    
</div>    
</body>  
<script>
	set_multiselect('cbo_store_name','0','0','','0');
	function load_multistore()
	{  
		set_multiselect('cbo_store_name','0','0','','0');
	}
</script>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>

<?
/*-------------------------------------------- Comments
Purpose			: 	Process and A/C head wise stranded cost set-up
Functionality	:	
JS Functions	:
Created by		:	Shafiq
Creation date 	: 	26-11-2023
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
echo load_html_head_contents("Process Wise Finishing Charge Set up", "../../", 1, 1,$unicode,'','',1,'');

?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission='<? echo $permission; ?>';
	


	function fnc_dyeing_charge( operation )
	{
		if(operation==2)
		{
			alert("Delete Restricted");return;
		}
		
		if (form_validation('cbo_company_id*cbo_from_year*cbo_from_month','Company Name*Year*Month')==false)
		{
			return;
		}
		else
		{   			
			var i=1; var dataString='';
			var total_row = 0;
			$("#list_data").find('tbody tr').each(function()
            {
                var txt_rate=$("#rate_"+i).val()*1;   
                var txt_prev_data=$("#prev_data_"+i).val();   
                var process=$("#process_"+i).val();   
                var update_id=$("#update_id_"+i).val();

				if(txt_prev_data !="")
				{
					dataString+='&txt_rate' + i + '=' + txt_rate  + '&txt_prev_data' + i + '=' + txt_prev_data+ '&process' + i + '=' + process+ '&update_id' + i + '=' + update_id;
					i++;
					total_row++;
				}
            });
			//alert(dataString);return;
			var data="action=save_update_delete&operation="+operation+"&total_row="+total_row+get_submitted_data_string('cbo_company_id*cbo_from_year*cbo_from_month',"../../")+dataString;
			//alert(total_row);
			// return;
			freeze_window(operation);
			http.open("POST","requires/account_head_wise_prices_rate_basic_standard_setup_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_dyeing_charge_reponse;
		}
	}

	function fnc_dyeing_charge_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==3)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
			{
				show_msg(reponse[0]);
				$("#txt_update_id").val(reponse[1]);
				// show_list_view(reponse[1],'ac_head_list_view','list_view_charge','requires/account_head_wise_prices_rate_basic_standard_setup_controller','setFilterGrid("list_view",-1)');
				set_button_status(1, permission, 'fnc_dyeing_charge',1);
			}
			release_freezing();
		}
	}

	function fnc_status_change()
	{
		var update_id=$("#txt_update_id").val();
		if(update_id!="" && update_id>0)
		{
			show_list_view(update_id,'ac_head_list_view','list_view_charge','requires/account_head_wise_prices_rate_basic_standard_setup_controller','setFilterGrid("list_view",-1)');
			set_button_status(1, permission, 'fnc_dyeing_charge',1);
		}else
		{
			$("#list_view_charge").html("");
			set_button_status(0, permission, 'fnc_dyeing_charge',1);
		}
	}

	function fnc_clc(i){
		var total_cap_min=0;
		var total_cap_pcs=0;
		var txt_capacity_month_min=$("#txt_capacity_month_min").val();
		var txt_capacity_month_pcs=$("#txt_capacity_month_pcs").val();
		//alert(1);
		var txt_ammounts=$('#txt_amount_'+i).val()*1;

		total_cap_min=txt_ammounts/txt_capacity_month_min;
		total_cap_pcs=txt_ammounts/txt_capacity_month_pcs;
		
		//alert(txt_ammounts+"_"+txt_capacity_month_min+"_"+txt_capacity_month_pcs+"_min_"+total_cap_min+"_pcs_"+total_cap_pcs);
		
		//  alert(total_cap_min)
		$('#txt_capacity_min_'+i).val(total_cap_min.toFixed(12));
		$('#txt_capacity_pcs_'+i).val(total_cap_pcs.toFixed(12));
	}

	function fnShowAcHeadPopup(type,sl)
	{
		if (form_validation('cbo_company_id*cbo_from_year*cbo_from_month*rate_'+sl,'Company*Year*Month*Rate')==false)
		{
			return;
		}
		else
		{
			let cbo_company_id = $("#cbo_company_id").val();
			let cbo_from_year = $("#cbo_from_year").val();
			let cbo_from_month = $("#cbo_from_month").val();
			let rate = $("#rate_"+sl).val();
			let prev_data = $("#prev_data_"+sl).val();

			var page_link='requires/account_head_wise_prices_rate_basic_standard_setup_controller.php?company_id='+cbo_company_id+'&cbo_from_year='+cbo_from_year+'&cbo_from_month='+cbo_from_month+'&process='+type+'&rate='+rate+'&prev_data='+prev_data+'&action=account_head_popup';
			var title='Account Head Popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=520px,height=380px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];				
				var theemail=this.contentDoc.getElementById("acc_head_data");
				if (theemail.value!="")
  				{	  				
					let acc_head_data=theemail.value;
					$("#prev_data_"+sl).val(acc_head_data);
					
  				}
				
			}
		}
	}

	
</script>
</head>
<body onLoad="set_hotkey()">
	<div align="center">
		<? echo load_freeze_divs ("../../", $permission); ?>
			<style>
				* {
					box-sizing: border-box;
					}

					.row {
					display: flex;
					}

					.column {
					flex: 50%;
					padding: 5px;
					}
			</style>
			<form name="dyeingfinishincharge_1" id="dyeingfinishincharge_1">
		      <fieldset style="width:1200px;">
				<table cellpadding="0" cellspacing="2" width="1190">
					<tr align="left">
						<td class="must_entry_caption" width="90">Company Name<input type="hidden" name="update_id" id="update_id" value=""/>
						<!-- <input type="hidden" id="txt_update_id"> -->
					</td>
						<td>
							<?
							echo create_drop_down( "cbo_company_id", 120, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "");
							?>
						</td>
						<td class="must_entry_caption" width="60">YEAR</td>
						<td>
                          <? 
                           echo create_drop_down( "cbo_from_year",60, $year, "", 0, "--All--", date('Y'), "", "", "");
						  ?>
						</td>
                        <td class="must_entry_caption" width="70">Month</td>
						<td>
                          <? 
                           echo create_drop_down( "cbo_from_month",70, $months, "", 1, "-- Select --", "", "load_drop_down( 'requires/account_head_wise_prices_rate_basic_standard_setup_controller',$('#cbo_company_id').val()+'__'+$('#cbo_from_year').val()+'__'+$('#cbo_from_month').val(), 'show_over_head_list_view', 'ac_head_tbl' );fnc_status_change();", "", "");
						  ?>
						</td>
										
					</tr>						
					<tr>
						<td colspan="8" align="center" class="button_container">
							<!-- <input type="button" id="copy_btn" class="formbutton" value="Copy for next month" onclick="fnc_dyeing_charge(3);"> -->
							<?
							echo load_submit_buttons( $permission, "fnc_dyeing_charge", 0,0,"reset_form('dyeingfinishincharge_1','list_view_charge*ac_head_tbl','','')",1);
							?>
						</td>
					</tr>
				
			    </table>	
				<div class="row">
				    <div class="column">
						<table>
						<tr>
						<div id="list_view_charge"></div>
						</tr>				
						</table>
					</div>
				</div>

				<div class="row">
						<div class="column">
							<table>
								<tr>
								<div id="ac_head_tbl"></div>
								</tr>
							</table>
						</div>
				</div>						
		</fieldset>			
	</form>
	
	
</div>

</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
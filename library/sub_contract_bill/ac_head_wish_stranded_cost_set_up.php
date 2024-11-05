<?
/*-------------------------------------------- Comments
Purpose			: 	Process and A/C head wise stranded cost set-up
Functionality	:	
JS Functions	:
Created by		:	Wayasel Ahmed
Creation date 	: 	14/11/2022
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
		
		if (form_validation('cbo_company_id*cbo_process_type','Company Name*Process')==false)
		{
			return;
		}
		else
		{   			
			var i=1; var dataString='';
			var total_row = 0;
			//var tbl_length=$("#table_list_dtls tbody tr").length*1;
			//alert(tbl_length);return;
			$("#table_list_dtls").find('tbody tr').each(function()
            {
                var txt_id=$(this).find('input[name="txt_id[]"]').val();                
                var txt_acount_code=$(this).find('input[name="txt_acount_code[]"]').val();                
                var txt_ac_description=$(this).find('input[name="txt_ac_description[]"]').val();                
                var txt_amount=$(this).find('input[name="txt_amount[]"]').val()*1;  

                var txt_capacity_min=$(this).find('input[name="txt_capacity_min[]"]').val();   
                var txt_capacity_pcs=$(this).find('input[name="txt_capacity_pcs[]"]').val();   

				if(txt_amount > 0)
				{
					dataString+='&txt_id_' + i + '=' + txt_id  + '&txt_acount_code_' + i + '=' + txt_acount_code + '&txt_ac_description_' + i + '=' + txt_ac_description + '&txt_amount_' + i + '=' + txt_amount + '&txt_capacity_min_' + i + '=' + txt_capacity_min + '&txt_capacity_pcs_' + i + '=' + txt_capacity_pcs;
					i++;
					total_row++;
				}
            });
			//alert(dataString);return;
			var data="action=save_update_delete&operation="+operation+"&total_row="+total_row+get_submitted_data_string('cbo_company_id*cbo_from_year*cbo_from_month*cbo_process_type*txt_update_id',"../../")+dataString;
			//alert(total_row);
			// return;
			freeze_window(operation);
			http.open("POST","requires/ac_head_wish_stranded_cost_set_up_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_dyeing_charge_reponse;
			release_freezing();
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
				show_list_view(reponse[1],'ac_head_list_view','list_view_charge','requires/ac_head_wish_stranded_cost_set_up_controller','setFilterGrid("list_view",-1)');
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
			show_list_view(update_id,'ac_head_list_view','list_view_charge','requires/ac_head_wish_stranded_cost_set_up_controller','setFilterGrid("list_view",-1)');
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
                           echo create_drop_down( "cbo_from_month",70, $months, "", 0, "--All--", date('m'), "", "", "");
						  ?>
						</td>
                        <td class="must_entry_caption">Process</td>
                        <td>
                            <?
							$cbo_type_arr=array(1=>"Cutting",2=>"Sewing");//,3=>"Finishing"
							echo create_drop_down( "cbo_process_type",80,$cbo_type_arr,'',1,'--Select--',0,"load_drop_down( 'requires/ac_head_wish_stranded_cost_set_up_controller',$('#cbo_process_type').val()+'__'+$('#cbo_company_id').val()+'__'+$('#cbo_from_year').val()+'__'+$('#cbo_from_month').val(), 'load_ac_head_tbl', 'ac_head_tbl' );fnc_status_change();",0); ?>
                        </td>
										
					</tr>						
					<tr>
						<td colspan="8" align="center" class="button_container">
							<input type="button" id="copy_btn" class="formbutton" value="Copy for next month" onclick="fnc_dyeing_charge(3);">
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
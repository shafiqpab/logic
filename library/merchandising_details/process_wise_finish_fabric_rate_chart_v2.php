<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Process Wise Finish Fabric Rate Chart
Functionality	:
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor
Creation date 	: 	10-03-2022
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
echo load_html_head_contents("Yarn Count Determination", "../../", 1, 1,$unicode,'','');
?>
<script type="text/javascript">

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	function fnc_fabric_count_determination( operation )
	{
		freeze_window(operation);
		var cbo_company_name=$('#cbo_company_name').val();
		var cbo_main_process=$('#cbo_main_process').val();

	
		if (form_validation('cbo_company_name*cbo_main_process','Company Name*Main Process')==false)
		{
			release_freezing();
			return;
		}

		if(cbo_main_process ==1)
		{
			if (form_validation('cbo_fabric_type*cbo_count_range_from*cbo_count_range_to','Fabric type*Count from*Count to')==false)
			{
				release_freezing();
				return;
			}
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_main_process*cbo_fabric_type*cbo_count_range_from*cbo_count_range_to*cbo_status*update_id',"../../");
		}
		else if(cbo_main_process ==30)
		{
			if (form_validation('cbo_yarn_dyeing_part*cbo_yarn_color_range','Yarn Dyeing Part*Color Range')==false)
			{
				release_freezing();
				return;
			}
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_main_process*cbo_yarn_dyeing_part*cbo_yarn_color_range*cbo_status*update_id',"../../");
		}
		else if(cbo_main_process ==31)
		{
			if (form_validation('cbo_dyeing_part*cbo_diawidthtype*cbo_color_range*cbo_dyeing_upto','Dyeing Part*Width/Dia type*Color Range*Dyeing Upto')==false)
			{
				release_freezing();
				return;
			}
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_main_process*cbo_dyeing_part*cbo_diawidthtype*cbo_color_range*cbo_dyeing_upto*cbo_status*update_id',"../../");
		}
		else if(cbo_main_process ==35)
		{
			if (form_validation('cbo_aop_type*cbo_no_color*txt_coverage_from*txt_coverage_to*cbo_aop_upto','AOP Type*No. Of Color*Coverage from*Coverage to*AOP Upto')==false)
			{
				release_freezing();
				return;
			}
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_main_process*cbo_aop_type*cbo_no_color*txt_coverage_from*txt_coverage_to*cbo_aop_upto*cbo_status*update_id',"../../");
		}
		else if(cbo_main_process ==1000)
		{
			if (form_validation('cbo_additional_process','Additional process')==false)
			{
				release_freezing();
				return;
			}
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_main_process*cbo_additional_process*cbo_status*update_id',"../../");
		}

			
		http.open("POST","requires/process_wise_finish_fabric_rate_chart_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_count_determination_reponse;
	}
	
	function fnc_fabric_count_determination_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==50)
			{
				alert(reponse[1]);
				release_freezing();
				return;	
			}
			if(reponse[0]==15) 
			{ 
				 setTimeout('fnc_fabric_count_determination('+ reponse[1]+')',8000); 
			}
			else if(reponse[0]==10 || reponse[0]==11)
			{
				show_msg(trim(reponse[0]));
				release_freezing();
			}
			else
			{
				//alert(reponse[0]);
				show_msg(trim(reponse[0]));
				show_list_view($('#cbo_company_name').val()+'_'+$('#cbo_main_process').val(),'search_list_view','yarn_count_container','requires/process_wise_finish_fabric_rate_chart_controller_v2','setFilterGrid("list_view",-1)');
				//reset_form('yarncountdetermination_1','','');
				fn_reset_form();
				
				set_button_status(0, permission, 'fnc_fabric_count_determination',1);
				release_freezing();
			}
		}
	}

	function set_form_data(data)
	{
		var data = data.split("**");
     	var dtls_id = data[0];
     	var main_process = data[1];

		get_php_form_data(dtls_id+'_'+main_process, "load_php_data_to_form", "requires/process_wise_finish_fabric_rate_chart_controller_v2");
	}
	
	function fn_reset_form()
	{
		reset_form('yarncountdetermination_1','','','','','cbo_company_name*cbo_main_process');
		set_button_status(0, permission, 'fnc_fabric_count_determination',1);
	}	
		
	function openmypage_comp(inc)
	{
		var page_link="requires/process_wise_finish_fabric_rate_chart_controller_v2.php?action=composition_popup&inc="+inc;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Composition Popup", 'width=650px,height=350px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
		var hidcompid=this.contentDoc.getElementById("hidcompid").value;
		var hidcompname=this.contentDoc.getElementById("hidcompname").value;
		$('#cbocompone').val(hidcompid);
		$('#txtcompone').val(hidcompname);
		}
	}	

function check_exchange_rate()
{
	var txt_rate_bdt=$('#txt_rate_bdt').val();
	var txt_effective_date = $('#txt_effective_date').val();
	var cbo_party_name = $('#cbo_party_name').val();
	var cbo_party_type = $('#cbo_party_type').val();
	var response=return_global_ajax_value( 2+"**"+txt_effective_date+"**"+cbo_party_name+"**"+txt_rate_bdt+"**"+cbo_party_type, 'check_conversion_rate', '', 'requires/process_wise_finish_fabric_rate_chart_controller_v2');
	var response=response.split("_");
	$('#txt_rate_usd').val(response[1]);
}


	function fnc_process_rate(upid)
	{
	 	if(upid=="")
		{
			alert("Save Data First");
			return;
		}
		
		var page_link="requires/process_wise_finish_fabric_rate_chart_controller_v2.php?action=process_wise_rate_popup&mst_id="+trim(upid);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Process Wise Rate Pop Up", 'width=450px,height=390px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var tot_rate=this.contentDoc.getElementById("tot_rate").value;
			document.getElementById('txt_rate_bdt').value=tot_rate;
			check_exchange_rate();
			show_list_view($('#cbo_company_name').val(),'search_list_view','yarn_count_container','requires/process_wise_finish_fabric_rate_chart_controller_v2','setFilterGrid("list_view",-1)');
		}
	}

	function openmypage_rate(main_process) 
	{
		var update_id=$('#update_id').val();

		if(update_id =="")
		{
			alert("Save data first");
			return;
		}

		var cbo_company_name=$('#cbo_company_name').val();

		var page_link="requires/process_wise_finish_fabric_rate_chart_controller_v2.php?action=process_wise_rate_popup&dtls_id="+trim(update_id)+"&cbo_company_name="+cbo_company_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Process Wise Rate Pop Up", 'width=650px,height=350px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var tot_rate=this.contentDoc.getElementById("tot_rate").value;
			document.getElementById('txt_rate_bdt').value=tot_rate;
			check_exchange_rate();
			show_list_view($('#cbo_company_name').val(),'search_list_view','yarn_count_container','requires/process_wise_finish_fabric_rate_chart_controller_v2','setFilterGrid("list_view",-1)');
		}
		
	}
	function fnc_dtls_list_show()
	{
		show_list_view($('#cbo_company_name').val()+'_'+$('#cbo_main_process').val(),'search_list_view','yarn_count_container','requires/process_wise_finish_fabric_rate_chart_controller_v2','setFilterGrid("list_view",-1)');
		fn_reset_form();
	}
</script>

</head>	
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%; position:relative; margin-bottom:5px; margin-top:5px">
		<? echo load_freeze_divs ("../../",$permission);  ?>
        <form name="yarncountdetermination_1" id="yarncountdetermination_1" autocomplete="off">
            <fieldset style="width:800px;">
                <legend>Finish Fabric Rate Chart Marter Part</legend>
                <table width="100%" border="0" cellpadding="0" cellspacing="2">
                    <tr>
						<td class="must_entry_caption">Company Name</td>
						<td><?= create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_dtls_list_show()" ); ?>
						<input type="hidden" id="update_id" >
						</td>
                        <td class="must_entry_caption">Main Process</td>
						<td><?

							$main_process_arr = array(1=>"Knitting",30=>"Yarn Dyeing",31=>"Fabric Dyeing",35=>"All Over Printing",1000=>"Additional");

							echo create_drop_down( "cbo_main_process",150, $main_process_arr,"", 0, "-- Select --", '1', "show_list_view(document.getElementById('cbo_main_process').value+'_'+this.value, 'show_details_part', 'details_container', 'requires/process_wise_finish_fabric_rate_chart_controller_v2', '');fnc_dtls_list_show();",$disabled,"" ); 
						?></td>

                    </tr>
                    <!-- 
                   
					<tr>
						<td class="must_entry_caption">Rate (BDT)</td>
                        <td><input class="text_boxes_numeric" type="text" onChange="check_exchange_rate();" style="width:120px;" name="txt_rate_bdt" id="txt_rate_bdt" value="" onClick="fnc_process_rate(document.getElementById('update_id').value);" readonly placeholder="Browse"/></td>
                    	<td class="must_entry_caption">Effective Date</td>
                        <td><input class="datepicker" type="text"  style="width:120px;" name="txt_effective_date" id="txt_effective_date" onChange="check_exchange_rate()" placeholder="Date"/></td>
						<td class="must_entry_caption">Rate (USD)</td>
                        <td><input class="text_boxes_numeric" type="text" style="width:120px;"  name="txt_rate_usd" id="txt_rate_usd" disabled /></td>
                    </tr> -->
                </table>
				
                
            </fieldset>
			<br>
			
			<fieldset style="width:1000px;" >
				<div id="details_container">
					<legend>Finish Fabric Rate Chart Details Part</legend>
					<table width="100%" border="0" cellpadding="0" cellspacing="2" align="left" class="rtp_table">
						<thead>
							<tr align="left">
								<th>Fabric type</th>
								<th>Count Range</th>
								<th>Rate Popup</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							<tr align="left">
								<td>
									<? echo create_drop_down( "cbo_fabric_type", 150, "select id, fabric_construction_name from lib_fabric_construction where status_active =1 and is_deleted=0  order by fabric_construction_name","id,fabric_construction_name", 1, "-- Select --", $selected, "" ); 
									?>
								</td>
								<td>
									<?
										echo create_drop_down( "cbo_count_range_from", 100, "select id, yarn_count from lib_yarn_count where status_active =1 and is_deleted=0  order by yarn_count","id,yarn_count", 1, "-- Select --", $selected, "" );
										echo create_drop_down( "cbo_count_range_to", 100, "select id, yarn_count from lib_yarn_count where status_active =1 and is_deleted=0  order by yarn_count","id,yarn_count", 1, "-- Select --", $selected, "" );
									?>
									<!-- <input type="text" id="txt_count_range_from"  name="txt_count_range_from" class="text_boxes" style="width:52px" value="" placeholder="From"/>&nbsp;<input type="text" id="txt_count_range_to"  name="txt_count_range_to" class="text_boxes" style="width:52px" value=""  placeholder="To"/> -->
								</td>
								<td>
									<input type="text" id="txt_rate_popup"  name="txt_rate_popup"  class="text_boxes" style="width:120px" value="" readonly placeholder="Browse" onDblClick="openmypage_rate(document.getElementById('cbo_main_process').value);" />
								</td>
								<td>
									<? echo create_drop_down( "cbo_status", 130, $row_status, "", "", "-- Select --", "1", "","","" );?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<br>
				<table width="100%" border="" cellpadding="0" cellspacing="0"  rules="all">
					<tr>
						<td colspan="6" align="center" class="button_container"><?=load_submit_buttons( $permission, "fnc_fabric_count_determination", 0,0 ,"fn_reset_form()",1); ?> 
						</td>		
					</tr>	
				</table>
			</fieldset>
        </form>	
		<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
        
        <div id="yarn_count_container">
			<?
				
            ?>
        </div>
    </div>
</body>
</html>

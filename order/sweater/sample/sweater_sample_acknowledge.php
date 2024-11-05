<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Sweater Sample Acknowledge
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam Reza [Cell: +880 151 1100004]
Creation date 	: 	01-10-2019
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:

*/

session_start(); 
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$menu_id=$_SESSION['menu_id'];
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sweater Sample Acknowledge", "../../../", 1, 1,'','','');

?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php"; 
	var permission='<? echo $permission; ?>';

	function fn_report_generated()
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_season*txt_style_ref*txt_req_no*txt_date_from*txt_date_to*cbo_acknowledge_type',"../../../");
		
		freeze_window(3);
		http.open("POST","requires/sweater_sample_acknowledge_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			$('#report_container').html(response[0]);
			var tableFilters = { col_0: "none" }
			setFilterGrid("tbl_list_search",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}
	
	function check_all(tot_check_box_id)
	{
		if ($('#'+tot_check_box_id).is(":checked"))
		{ 
			$('#tbl_list_search tbody tr').each(function() {
				$('#tbl_list_search tbody tr input:checkbox').attr('checked', true);
			});
		}
		else
		{ 
			$('#tbl_list_search tbody tr').each(function() {
				$('#tbl_list_search tbody tr input:checkbox').attr('checked', false);
			});
		} 
	}
	

		
	function submit_approved(total_tr,type,operation)
	{	 
		
		var dataStr='';
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				var confirm_del_end_date = $('#txt_confirm_del_end_date_'+i).val()*1;
				
				if(confirm_del_end_date=="")
				{
					if (form_validation('txt_confirm_del_end_date_'+i,'Confirm Delivery End Date')==false)
					{
						return;
					}
				}
			
				dataStr+='*tbl_'+i;
				
				
				if($('#cbo_acknowledge_type').val()==1){
					dataStr+='*update_id_'+i+'*sample_req_id_'+i+'*sample_req_no_'+i+'*req_date_'+i+'*company_id_'+i+'*buyer_id_'+i+'*season_'+i+'*style_ref_'+i+'*sample_qty_'+i+'*required_qty_'+i+'*embellishment_status_id_'+i+'*delv_start_date_'+i+'*delv_end_date_'+i+'*team_leader_'+i+'*txt_confirm_del_end_date_'+i+'*txt_refusing_cause_'+i+'*dealing_marchant_'+i;
				}

			
			
			}
			
			if($('#cbo_acknowledge_type').val()==2){
			dataStr+='*update_id_'+i+'*sample_req_id_'+i+'*sample_req_no_'+i+'*req_date_'+i+'*company_id_'+i+'*buyer_id_'+i+'*season_'+i+'*style_ref_'+i+'*sample_qty_'+i+'*required_qty_'+i+'*embellishment_status_id_'+i+'*delv_start_date_'+i+'*delv_end_date_'+i+'*team_leader_'+i+'*txt_confirm_del_end_date_'+i+'*txt_refusing_cause_'+i+'*dealing_marchant_'+i;
			}
			
			
			
		}
		var data="action=approve&operation="+operation+'&total_row='+total_tr+get_submitted_data_string('cbo_acknowledge_type'+dataStr,"../../../");
		
		//alert(data);
		
		freeze_window(operation);
		http.open("POST","requires/sweater_sample_acknowledge_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=submit_approved_response_info;
	}
	
	
	function submit_approved_response_info()
	{
		if(http.readyState == 4) 
		{ 
			var reponse=trim(http.responseText).split('**');	
			show_msg(reponse[0]);
			if(reponse[2]=='mail_send'){
				var acId=reponse[1].split(',');
				var returnValue='';
				for(var i=0;i<acId.length;i++){
					 if(returnValue !='' ){returnValue+=', ';}
					 returnValue += return_global_ajax_value(acId[i], 'sweater_sample_acknowledgement_mail_notification', '', '../../../auto_mail/sweater_sample_acknowledgement_mail_notification');
				}
				alert(returnValue);
			}
			
			fn_report_generated();			
			release_freezing();			
		}
	}
	


	function fn_check_acknowledge(i)
	{
		var dataStr='team_leader_'+i+'*txt_confirm_del_end_date_'+i+'*company_id_'+i;
		var data="action=acknowledge_capacity&row_no="+i+get_submitted_data_string(dataStr,"../../../");
		//freeze_window(3);
		http.open("POST","requires/sweater_sample_acknowledge_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_check_acknowledge_reponse;
			
	}
	function fn_check_acknowledge_reponse(){
		if(http.readyState == 4) 
		{ 
			var reponse=trim(http.responseText).split(',');	
			//show_msg(reponse[0]);
			var selected_confirm_del_end_date = $('#txt_confirm_del_end_date_'+reponse[2]).val();
			
			var totalRows = $('#tbl_list_search tbody tr').length;
			
			var booked=0;
			for(var i=1;i <= totalRows;i++){
				if( selected_confirm_del_end_date == $('#txt_confirm_del_end_date_'+i).val() ){
					booked++;
				}
				
			}
			
			var capacityBalance=((reponse[0]-reponse[1])+booked)-1;
			if(capacityBalance <=0 ){
				if(confirm("Capacity :"+reponse[0]+"\n Booked :"+reponse[1]+"\n Balance :"+capacityBalance)==false){return false;}
			}
		}
	}
	
	
</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs("../../../",''); ?>
		 <form name="sample_acknowledgement_1" id="sample_acknowledgement_1"> 
         <h3 style="width:1000px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:1000px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <tr>
                                <th class="must_entry_caption">Company Name</th>
                                <th>Buyer</th>
                                <th>Season</th>
                                <th>Style Ref</th>
                                <th>Req. No</th>
                                <th>Delv St Date</th>
                                <th>Delv End Date</th>
                                <th>Acknowledge Type</th>
                                <th>
                                    <input type="reset" name="res" id="res" value="Reset" onClick="reset_form('sample_acknowledgement_1','report_container','','','')" class="formbutton" style="width:80px" />
                                </th>
                        	</tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sweater_sample_acknowledge_controller',this.value, 'load_drop_down_buyer', 'td_buyer' );" );
                                    ?>
                                </td>
                                <td id="td_buyer"> 
									<?
                                       echo create_drop_down( "cbo_buyer_name", 152, $blank_array,"", 1, "-- All Buyer --", 0, "" );
                                    ?>
                                </td>
                                <td id="td_season"><? echo create_drop_down( "cbo_season", 100, $season,"", 1, "-- Select --", 0, "" ); ?></td>
                                <td><input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:95px"></td>
                      			<td><input name="txt_req_no" id="txt_req_no" class="text_boxes" style="width:65px"></td>
                              
                                <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" readonly style="width:80px"/></td>
                                <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" readonly style="width:80px"/></td>
                                <td> 
                                    <?
									  $acknowledge_type=array(2=>"Un-Acknowledge",1=>"Acknowledge");
                                        echo create_drop_down( "cbo_acknowledge_type", 130, $acknowledge_type,"", 0, "", $selected,"","", "" );
                                    ?>
                                </td>
                                <td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:80px" onClick="fn_report_generated()"/></td>
                            </tr>
                            <tr>
                                <td colspan="9" align="center" valign="middle">
									<? echo load_month_buttons(1); ?>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

<script>
$('#cbo_approval_type').val(0);
</script>
</html>
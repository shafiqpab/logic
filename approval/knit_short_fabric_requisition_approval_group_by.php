<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yern Work Woder Approval
Functionality	:
JS Functions	:
Created by		:	Shajib Jaman
Creation date 	: 	1-16-2024
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
$menu_id=$_SESSION['menu_id'];
$approval_setup=is_duplicate_field( "entry_form", "electronic_approval_setup", "entry_form=92 and is_deleted=0" );
//------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yarn Work Order App Group By", "../", 1, 1,'','','');

?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	var permission='<? echo $permission; ?>';


	function fn_report_generated()
	{
		var approval_setup =<? echo $approval_setup; ?>;
		freeze_window(3);
		if(approval_setup!=1)
		{
			alert("Electronic Approval Setting First.");
			release_freezing();
			return;
		}

		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			release_freezing();
			return;
		}
		var previous_approved=0;
		if($('#previous_approved').is(":checked")) previous_approved=1;
		var data="action=report_generate&previous_approved="+previous_approved+get_submitted_data_string('cbo_company_name*cbo_approval_type*txt_alter_user_id*cbo_buyer_name*cbo_season_id*txt_style_ref*cbo_year*txt_req_no*txt_date',"../");

		// alert(data);return;
		http.open("POST","requires/knit_short_fabric_requisition_approval_group_by_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = () =>{
			if(http.readyState == 4)
			{
				var response=trim(http.responseText).split("####");
				$('#report_container').html(response[0]);
				var tableFilters = { col_0: "none"}
				setFilterGrid("tbl_list_search",-1,tableFilters);
				show_msg('3');
				release_freezing();
			}
		}
	}


	function check_all(tot_check_box_id)
	{
		$('#tbl_list_search tbody tr').each(function() {
			$('#tbl_list_search tbody tr input:checkbox').attr('checked', $('#'+tot_check_box_id).is(":checked"));
		});
	}

	function check_on_scan(scan_no)
	{
		var row_no=$('#'+scan_no).val();
		var tbl_len=$("#tbl_list_search tbody tr").length;
		$('#tbl_'+row_no).attr('checked', true);

		//new
		if($('#tbl_'+row_no).is(":checked")==false)
		{
			alert("No data found");
			$('#txt_bar_code').val('');
			$('#txt_bar_code').focus();
			return;
		}
		else
		{
			submit_approved(tbl_len, $('#cbo_approval_type').val());
		}
	}

	function submit_approved(total_tr,type)
	{
		freeze_window(0);
		// Confirm Message  *************************************
		if($('#cbo_approval_type').val()==1)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to UnApproved All Work Order No");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnApproved All Work Order No");
					if(second_confirmation==false)
					{
						release_freezing();
						return;
					}
				}
			}

		}
		else if($('#cbo_approval_type').val()==0)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to Approved All Work Order No");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to Approved All Work Order No");
					if(second_confirmation==false)
					{
						release_freezing();
						return;
					}
				}
			}
		}
		// Confirm Message End ******************************************************************
        var req_id_arr = Array(); var appv_cause_arr = Array();
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				req_id_arr.push($('#req_id_'+i).val());
                appv_cause_arr.push($('#txt_appv_cause'+i).val());
			}
		}

        var req_ids = req_id_arr.join(",");
        var appv_causes = appv_cause_arr.join("**");

		
		
		if(type==5){
			$('#txt_selected_id').val(req_ids);
			fnSendMail('../','',1,0,0,1);
		}
		

		
		var alterUserID = $('#txt_alter_user_id').val();
		var cbo_company_name = $('#cbo_company_name').val();
		var data="action=approve&operation="+operation+'&txt_alter_user_id='+alterUserID+'&approval_type='+type+'&req_ids='+req_ids+'&appv_causes='+appv_causes+'&cbo_company_name='+cbo_company_name;
        //alert(data);die;
		http.open("POST","requires/knit_short_fabric_requisition_approval_group_by_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = () =>{
			if(http.readyState == 4)
			{
				var reponse=trim(http.responseText).split('**');
				show_msg(reponse[0]);
				if((reponse[0]==19 || reponse[0]==20 || reponse[0]==50))
				{
					fnc_remove_tr();
				}
				$('#txt_bar_code').val('');
				$('#txt_bar_code').focus();
				release_freezing();
			}
		}

	}

 

	function fnc_remove_tr()
	{
		var tot_row=$('#tbl_list_search tbody tr').length;
		for(var i=1;i<=tot_row;i++)
		{
			if($('#tbl_'+i).is(':checked'))
			{
				$('#tr_'+i).remove();
			}
		}
	}

	//barcode scan
	$('#txt_bar_code').live('keydown', function(e) {
		if  (e.keyCode === 13)
		{
			e.preventDefault();
			var bar_code=$('#txt_bar_code').val();
			check_on_scan(bar_code);
		}
	});

	function change_user()
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		var title = 'Yarn Work Order Approval New';
		var page_link = 'requires/knit_short_fabric_requisition_approval_group_by_controller.php?action=user_popup'+get_submitted_data_string('cbo_company_name',"../");

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=390px,center=1,resize=1,scrolling=0','');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var data=this.contentDoc.getElementById("selected_id").value;
			var data_arr=data.split("_");
			$("#txt_alter_user_id").val(data_arr[0]);
			$("#txt_alter_user").val(data_arr[1]);
			/* load_drop_down( 'requires/knit_short_fabric_requisition_approval_group_by_controller',$("#cbo_company_name").val()+"_"+data_arr[0], 'load_drop_down_buyer_new_user', 'buyer_td_id' ); */
			$("#report_container").html('');
		}
	}

	function change_approval_type(value)
	{
		if(value==0)
		{
			$("#previous_approved").val(1);
			$("#cbo_approval_type").val(1);
			$("#cbo_approval_type").attr("disabled",true);
		}
		else
		{
			$("#previous_approved").val(0);
			$("#cbo_approval_type").val(0);
			$("#cbo_approval_type").attr("disabled",false);
		}
	}
	
	
	function call_print_button_for_mail(mail){
		 
		var booking_id=$('#txt_selected_id').val();
		var sysIdArr=booking_id.split(',');
		
		var mail=mail.split(',');
		var ret_data=return_global_ajax_value(sysIdArr.join('*')+'__'+mail.join('*'), 'yarn_work_order_app', '', '../auto_mail/approval/yarn_work_order_approval_auto_mail');
		alert(ret_data);
	}
	
	
</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",''); ?>
		 <form name="requisitionApproval_1" id="requisitionApproval_1">
         <h3 style="width:1090px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
         <div id="content_search_panel">
             <fieldset style="width:980px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                        	<tr>
                            	<th colspan="2">Barcode Scan: <input type="text" id="txt_bar_code" name="txt_bar_code" class="text_boxes"  /></th>
                                 <th align="center" colspan="2">
									<input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes" style="display:none"  />
								</th>
                                <th colspan="5">
                                <?php
									$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
									if( $user_lavel==2)
									{
								?>
                                		<!--<input type="button" class="image_uploader" style="width:100px" value="CHANGE USER" onClick="change_user()">-->
                                        Alter User:
                                        <input type="text" id="txt_alter_user" name="txt_alter_user" class="text_boxes"  onDblClick="change_user()"/ placeholder="Browse " readonly>
                                        <input type="hidden" id="txt_selected_id" name="txt_selected_id" />
                                <?php
									}

								?>
                                <input type="hidden" id="txt_alter_user_id" name="txt_alter_user_id" />
                                </th>
                            </tr>
                            <tr>
                                <th class="must_entry_caption">Company Name</th>
                                <th>Buyer</th>
                                <th>Season</th>
                                <th>Year</th>
                                <th>Style Ref</th>
                                <th>Req Date</th>
                                <th>Req No.</th>
                                <th>Approval Type</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                        	</tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td>
                                    <?
                                        echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0  $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/knit_short_fabric_requisition_approval_group_by_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );" );
                                    ?>
                                </td>
                                <td id="buyer_td_id"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- All Buyer --", 0, "" ); ?></td>

                                <td id="season_td"><?=create_drop_down("cbo_season_id", 80, $blank_array,'', 1, "-Select Season-",$selected, "" ); ?></td>

                                <td> <? echo create_drop_down( "cbo_year", 100, $year,"", 1, "-- Select --", 0, "" ); ?></td>

                                <td><input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:100px"></td>

                                <td><input type="text" name="txt_date" id="txt_date" class="datepicker" style="width:80px"/></td>
                                <td width=""><input type="text" name="txt_req_no"  id="txt_req_no"  style="width:100px" class="text_boxes" placeholder="WO No" />
								</td>
                                <td>
                                    <?
                                        echo create_drop_down( "cbo_approval_type", 140, $approval_type_arr,"", 0, "", $selected,"","", "" );
                                    ?>
                                </td>
                                <td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:100px" onClick="fn_report_generated()"/></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container" align="center"></div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$('#cbo_approval_type').val(0);
</script>
</html>

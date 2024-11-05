<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Trims Order Rcv Approval
Functionality	:	
JS Functions	:
Created by		:	Nayem
Creation date 	: 	12-10-2021
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
//----------------------------------------------------------------------------------------------
echo load_html_head_contents("Trims Order Rcv Approval", "../", 1, 1,'','','');
$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "page_id=$menu_id and is_deleted=0" );
// $approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "entry_form=51 and is_deleted=0" );
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<?=$permission; ?>';

	function fn_report_generated()
	{
		var approval_setup=<?=$approval_setup; ?>;
		//freeze_window(3);
		if(approval_setup!=1)
		{
			alert("Electronic Approval Setting First.");
			release_freezing();	
			return;
		}
	
		var previous_approved=0;
		if ($('#previous_approved').is(":checked")) previous_approved=1;

		var data="action=report_generate&previous_approved="+previous_approved+get_submitted_data_string('cbo_company_name*cbo_within_group*cbo_party_name*txt_sys_id*txt_date_from*txt_date_to*cbo_approval_type*txt_alter_user_id',"../");

		freeze_window(3);
		http.open("POST","requires/trims_order_rcv_approval_controller.php",true);
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

	function submit_approved(total_tr,type)
	{ 
		freeze_window(3);
		var rcv_ids = ""; var approval_ids = ""; 
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				rcv_id = $('#rcv_id_'+i).val();
				if(rcv_ids=="") rcv_ids= rcv_id; else rcv_ids +=','+rcv_id;
			
				approval_id = $('#approval_id_'+i).val();
				if(approval_ids=="") approval_ids= approval_id; else approval_ids +=','+approval_id;
			}
		}
		var alterUserID = $('#txt_alter_user_id').val();
		var data="action=approve&txt_alter_user_id="+alterUserID+'&approval_type='+type+'&rcv_ids='+rcv_ids+'&approval_ids='+approval_ids+get_submitted_data_string('cbo_company_name',"../");
	 	// alert(data);return;
		
		http.open("POST","requires/trims_order_rcv_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_cs_approval_Reply_info;
	}	
	
	function fnc_cs_approval_Reply_info()
	{
		if(http.readyState == 4) 
		{ 
			var reponse=trim(http.responseText).split('**');	
			//show_msg(reponse[0]);
			if((reponse[0]==19 || reponse[0]==20))
			{
				fnc_remove_tr();
				show_msg(reponse[0]);
			}				
			release_freezing();	
		}
		release_freezing();
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

	function change_user()
	{
		var title = 'Trims Order Rcv Approval Info';	
		var page_link = 'requires/trims_order_rcv_approval_controller.php?action=user_popup'+get_submitted_data_string('cbo_company_name',"../");
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var data=this.contentDoc.getElementById("selected_id").value; //Access form field with id="emailfield"
			
			var data_arr=data.split("_");
			$("#txt_alter_user_id").val(data_arr[0]);
			$("#txt_alter_user").val(data_arr[1]);
			$("#cbo_approval_type").val(2);
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
			$("#cbo_approval_type").val(2);
			$("#cbo_approval_type").attr("disabled",false);
		}		
	}

	function fnc_load_party(within_group)
	{
		var company = $('#cbo_company_name').val();
		load_drop_down( 'requires/trims_order_rcv_approval_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
	}

	function print_report(company_name,id,report_title,within_group,action_type)
	{
		var action='';
		var data='';
		if(action_type==86)
		{
			var data=company_name+'*'+id+'*'+report_title+'*'+within_group;
			action="trims_order_receive_print";
		}
		else if(action_type==84) 
		{
			var rate_cond=confirm("Press  \"OK\"  to open with Rate value\nPress  \"Cancel\"  to open without Rate value");
			if (rate_cond==true) allow_rate="1"; else allow_rate="0";
			var data=company_name+'*'+id+'*'+report_title+'*'+within_group+'*'+allow_rate;
			action="trims_order_receive_print_2";
		}
		else if(action_type==377) 
		{
			var rate_cond=confirm("Press  \"OK\"  to open with Rate value\nPress  \"Cancel\"  to open without Rate value");
			if (rate_cond==true) allow_rate="1"; else allow_rate="0";
			var data=company_name+'*'+id+'*'+report_title+'*'+within_group+'*'+allow_rate;
			action="trims_order_receive_print_3";
		}

		freeze_window(5);
		http.open("POST","../trims_erp/marketing/requires/trims_order_receive_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4) 
		    {
		    	//alert(action+"**"+data);
				window.open("../trims_erp/marketing/requires/trims_order_receive_controller.php?action="+action+'&data='+data, "_blank");
				release_freezing();
		   }	
		}
	}

</script>
</head>
<body>
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../",''); ?>
		<form name="csApproval_1" id="csApproval_1"> 
         	<h3 style="width:1000px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         	<div id="content_search_panel"> 
            	<fieldset style="width:1000px;">
                	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead> 
                        	<tr> 
                                <th colspan="4" align="center">
									<?
										$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
										if( $user_lavel==2)
										{
											?><span style="vertical-align: 5px;">Previous Approved:&nbsp;</span><span><input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes"  value="0"  onChange="change_approval_type(this.value);" /></span>
											<?
										}
										else
										{
											?>
											<input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes" style="display:none" />
											<?
										}
									?> 
                                </th>
                                <th colspan="4">
									<?
										$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
										if( $user_lavel==2)
										{
											?>Alter User:<input type="text" id="txt_alter_user" name="txt_alter_user" class="text_boxes"  onDblClick="change_user();" placeholder="Browse " style="width:200px" readonly>
											<?
										}
									?>
                                <input type="hidden" id="txt_alter_user_id" name="txt_alter_user_id" /> 
                                </th>
                            </tr>                    	
                            <tr>
                                <th>Company Name</th>
                                <th>Within Group</th>
                                <th>Party Name</th>
                                <th>System ID</th>
                                <th>From Date</th>
                                <th>To Date</th>
                                <th>Approval Type</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','');" class="formbutton" style="width:70px" /> <input style="width:50px;" type="hidden" name="txt_cm_compulsory" id="txt_cm_compulsory"/></th>
                        	</tr>
                        </thead>
                        <tbody>
                        	<tr class="general">                                
                                <td>
                                    <? 
                                        // echo create_drop_down( "cbo_item_category_id", 160, $item_category,"", 1, "-- All Category --", $selected,"",1,4,"","","1,2,3,12,13,14");
                                    ?>
									<? echo create_drop_down( "cbo_company_name", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, ""); ?>
                                </td>
                                <td>
									<?php echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 0, "--  --", 0, "fnc_load_party(this.value);" ); ?>
								</td>
                                <td id="buyer_td">
									<? echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, ""); ?>
									<!-- <? echo create_drop_down( "cbo_cs_year", 110, $year, "", 1, "-- Select --", date("Y", time()), "" ); ?> -->
								</td>
                                <td>
									<input name="txt_sys_id" id="txt_sys_id" style="width:80px" class="text_boxes" placeholder="Write">
								</td>
                                <td>
									<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date">
								</td>
								<td>
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date">
								</td>
                                <td> 
                                    <?
                                       echo create_drop_down( "cbo_approval_type", 140, $approval_type_arr,"", 0, "", $selected,"","", "" );
                                    ?>
                                </td>
                                <td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:70px" onClick="fn_report_generated()"/></td>                	
                            </tr>                            
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>$('#cbo_approval_type').val(0);</script>
</html>
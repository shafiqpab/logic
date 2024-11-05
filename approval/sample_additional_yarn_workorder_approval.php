<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yern Work Woder Approval
Functionality	:
JS Functions	:
Created by		:	Rakib
Creation date 	: 	22/11/2021
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
$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "page_id=$menu_id and is_deleted=0" );
//------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sample Or Additional Yarn WO Approval", "../", 1, 1,'','','');

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
		var data="action=report_generate&previous_approved="+previous_approved+get_submitted_data_string('cbo_company_name*cbo_approval_type*txt_alter_user_id*cbo_supplier_id*cbo_get_upto*txt_date*txt_wo_no',"../");
		//alert(data);return;		
		http.open("POST","requires/sample_additional_yarn_workorder_approval_controller.php",true);
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

			var tableFilters = { col_0: "none"}
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
		//var operation=4;
		var req_nos = "";
		freeze_window(0);
		// Confirm Message  ****************************************************
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
		// Confirm Message End **********************************************************

		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				req_id = $('#req_id_'+i).val();
				if(req_nos=="") req_nos= req_id; else req_nos +=','+req_id;
			}

		}
		var alterUserID = $('#txt_alter_user_id').val();
		var cbo_company_name = $('#cbo_company_name').val();
		var data="action=approve&operation="+operation+'&txt_alter_user_id='+alterUserID+'&approval_type='+type+'&req_nos='+req_nos+'&cbo_company_name='+cbo_company_name; //alert(data);

		http.open("POST","requires/sample_additional_yarn_workorder_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_purchase_requisition_approval_Reply_info;
	}

	function fnc_purchase_requisition_approval_Reply_info()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');

			show_msg(reponse[0]);

			if((reponse[0]==19 || reponse[0]==20))
			{
				fnc_remove_tr();
			}
			 // alert(http.responseText);
			$('#txt_bar_code').val('');
			$('#txt_bar_code').focus();
			release_freezing();
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
		var page_link = 'requires/sample_additional_yarn_workorder_approval_controller.php?action=user_popup'+get_submitted_data_string('cbo_company_name',"../");

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0','');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var data=this.contentDoc.getElementById("selected_id").value; //Access form field with id="emailfield"

			var data_arr=data.split("_");
			$("#txt_alter_user_id").val(data_arr[0]);
			$("#txt_alter_user").val(data_arr[1]);
			//load_drop_down( 'requires/pre_costing_approval_controller',$("#cbo_company_name").val()+"_"+data_arr[0], 'load_drop_down_buyer_new_user', 'buyer_td_id' );
			load_drop_down( 'requires/sample_additional_yarn_workorder_approval_controller',$("#cbo_company_name").val()+"_"+data_arr[0], 'load_drop_down_buyer_new_user', 'buyer_td_id' );
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

	function print_button_link(company,mst_id) {
		var type=4;
		var report_title='Sample Or Additional Yarn WO';
		var ref_closed_sts='';
		var rate_check=confirm("Generate Rate With Amount");
		if(rate_check==true) var rate_amt=1;
		else var rate_amt=0;
		window.open("../commercial/work_order_sweater/requires/sample_yarn_work_order_controller.php?data="+company+'*'+mst_id+'*'+report_title+'*'+type+'*'+ref_closed_sts+'*'+rate_amt+'&action='+"print_to_html_report4", true ); 
	}
</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",''); ?>
		<form name="requisitionApproval_1" id="requisitionApproval_1">
        <h3 style="width:900px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
        <div id="content_search_panel">
            <fieldset style="width:900px;">
                <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                    <thead>
                    	<tr>
                        	<th colspan="2">Barcode Scan: <input type="text" id="txt_bar_code" name="txt_bar_code" class="text_boxes"  /></th>
                            <th align="center" colspan="2">
                            	<?
								$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
								if( $user_lavel==2)
								{
									?>
                            		Previous Approved: <input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes" value="0"  onChange="change_approval_type(this.value)" />
                            		<?
								}
								else
								{
									?>
                            		<input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes" style="display:none"  />
                            		<?
								}
								?>
                            </th>
                            <th colspan="3">
                            	<?
								$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
								if( $user_lavel==2)
								{
									?>
                            		<!--<input type="button" class="image_uploader" style="width:100px" value="CHANGE USER" onClick="change_user()">-->
                                    Alter User:
                                    <input type="text" id="txt_alter_user" name="txt_alter_user" class="text_boxes"  onDblClick="change_user()"/ placeholder="Browse " readonly>
                            		<?
								}
								?>
                            	<input type="hidden" id="txt_alter_user_id" name="txt_alter_user_id" />
                            </th>
                        </tr>
                        <tr>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Supplier</th>
                            <th>Get Upto</th>
                            <th>WO Date</th>
                            <th>WO No</th>
                            <th>Approval Type</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:80px" /></th>
                    	</tr>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td>
                                <?
                                    echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0  $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sample_additional_yarn_workorder_approval_controller',this.value, 'load_supplier_dropdown', 'supplier_td_id' );" );
                                ?>
                            </td>
                            <td id="supplier_td_id"> 
								<?
                                   echo create_drop_down( "cbo_supplier_id", 152, $blank_array,"", 1, "-- All Supplier --", 0, "" );
                                ?>
                            </td>
                            <td> 
								<?
									$get_upto=array(1=>"After This Date",2=>"As On Today",3=>"This Date");
                                    echo create_drop_down( "cbo_get_upto", 130, $get_upto,"", 1, "-- Select --", 0, "" );
                                ?>
                            </td>
                            <td><input type="text" name="txt_date" id="txt_date" class="datepicker" style="width:80px" placeholder="WO Date"/></td>
                            <td><input type="text" name="txt_wo_no"  id="txt_wo_no"  style="width:100px" class="text_boxes" placeholder="WO No" />
							</td>
                            <td>
                                <?
                                    echo create_drop_down( "cbo_approval_type", 120, $approval_type_arr,"", 0, "", $selected,"","", "" );
                                ?>
                            </td>
                            <td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:80px" onClick="fn_report_generated()"/></td>
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

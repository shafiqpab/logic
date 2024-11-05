<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Dyes Chemical Work Woder Approval
Functionality	:
JS Functions	:
Created by		:	Aziz
Creation date 	: 	30-04-2015
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
$user_cred_item_cat_cond = "";$user_cred_company_id_cond = "";
if($id)
{
	if($item_cate_id != "")
	{
		$user_cred_item_cat_cond = $item_cate_id;
	}
	if($company_id != "")
	{
		$user_cred_company_id_cond = " and id in ($company_id) ";
	}
}
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Other Purchase WO Approval", "../", 1, 1,'','','');

$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "page_id=$menu_id and is_deleted=0" );
?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	var permission='<? echo $permission; ?>';


	function openmypage_refusing_cause(page_link,title,quo_id)
	{
		var page_link = page_link + "&quo_id="+quo_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=280px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var cause=this.contentDoc.getElementById("txt_refusing_cause").value;
			if (cause!="")
			{
				fn_report_generated();
			}
		}
	 }


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
		/*if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			release_freezing();
			return;
		}*/

		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*txt_wo_no*txt_date_from*txt_date_to*cbo_approval_type*txt_alter_user_id*cbo_supplier_id',"../");
		
		http.open("POST","requires/other_purchase_work_order_approval_controller.php",true);
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

			var tableFilters = { col_0: "none"}//,col_3: "select", display_all_text: " --- All Category ---" }
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
		var req_nos = "";var approval_ids = ""; 
		freeze_window(0);
		// Confirm Message  *********************************************************************************************************
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
		// Confirm Message End ***************************************************************************************************
		var mst_id_company_ids='';
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				req_id = $('#req_id_'+i).val();
				if(req_nos=="") req_nos= req_id; else req_nos +=','+req_id;

				requisition_id = $('#requisition_id_'+i).val();
				if(approval_ids=="") approval_ids= requisition_id; else approval_ids +=','+requisition_id;
			
			
				
				var mst_id_company_id = $('#mst_id_company_id_'+i).val();
				if(mst_id_company_ids==""){mst_id_company_ids = mst_id_company_id;}
				else{mst_id_company_ids +=','+mst_id_company_id;}
			
			
			
			}
		}

		var data="action=approve&operation="+operation+'&approval_type='+type+'&req_nos='+req_nos+'&approval_ids='+approval_ids+'&mst_id_company_ids='+mst_id_company_ids+get_submitted_data_string('cbo_company_name*txt_alter_user_id*cbo_supplier_id',"../"); 
		// alert(data);

		http.open("POST","requires/other_purchase_work_order_approval_controller.php",true);
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

		
		function generate_worder_report_print(wo_no,company_id,update_id,action) 
		{
			var approval_setup =<? echo $approval_setup; ?>;
	
			if(approval_setup!=1)
			{
				alert("Electronic Approval Setting First.");
				return;
			}
	
	
			var show_comment='';
			var r=confirm("Press  \"Cancel\"  to hide  Comment\nPress  \"OK\"  to Show Comment");
			if (r==true)
			{
				show_comment="1";
			}
			else
			{
				show_comment="0";
			}
			var form_name="Others Purchase Order";
	//alert(action);
			//var data="action=spare_parts_work_print&form_name="+form_name+"&wo_no="+wo_no+"&company_id="+company_id+"&update_id="+update_id+"&show_comment="+show_comment+'&path=../../';
			var operation = 4; var location = ''; //var action = action;
			var path = "../commercial/work_order/requires/spare_parts_work_order_controller";
			var perameters=company_id+"*"+update_id+"*"+form_name+"*"+location+"*"+operation;
		//	var data="action=spare_parts_work_print&data="+perameters;
			var data="action="+action+'&perameters='+perameters;
			//alert(action);
			print_report(perameters, action, path);
		}
		
		
		function generate_worder_report_print_2(wo_no,company_id,update_id,action,operation) 
		{
			/*var approval_setup =<? //echo $approval_setup; ?>;
	
			if(approval_setup!=1)
			{
				alert("Electronic Approval Setting First.");
				return;
			}*/
	
	
			var show_comment='';
			var r=confirm("Press  \"Cancel\"  to hide  Comment\nPress  \"OK\"  to Show Comment");
			if (r==true)
			{
				show_comment="1";
			}
			else
			{
				show_comment="0";
			}
			var form_name="Others Purchase Order";
	
			//var data="action=spare_parts_work_print&form_name="+form_name+"&wo_no="+wo_no+"&company_id="+company_id+"&update_id="+update_id+"&show_comment="+show_comment+'&path=../../';
			//var operation = 8; 
			var location = ''; 
			//var action = "spare_parts_work_order_print8";
			var path = "../commercial/work_order/requires/spare_parts_work_order_controller";
			var perameters=company_id+"*"+update_id+"*"+form_name+"*"+location+"*"+operation;
			//var data="action=spare_parts_work_print&data="+perameters;
			var data="action="+action+'&perameters='+perameters;
	
			print_report(perameters, action, path);
		}
		
		function generate_worder_report_print_3(wo_no,company_id,update_id) 
		{
			var approval_setup =<? echo $approval_setup; ?>;
	
			if(approval_setup!=1)
			{
				alert("Electronic Approval Setting First.");
				return;
			}
	
	
			var show_comment='';
			var r=confirm("Press  \"Cancel\"  to hide  Comment\nPress  \"OK\"  to Show Comment");
			if (r==true)
			{
				show_comment="1";
			}
			else
			{
				show_comment="0";
			}
			var form_name="Others Purchase Order";
	
			//var data="action=spare_parts_work_print&form_name="+form_name+"&wo_no="+wo_no+"&company_id="+company_id+"&update_id="+update_id+"&show_comment="+show_comment+'&path=../../';
			var operation = 4; var location = ''; var action = "spare_parts_work_order_print3";
			var path = "../commercial/work_order/requires/spare_parts_work_order_controller";
			var perameters=company_id+"*"+update_id+"*"+form_name+"*"+location+"*"+operation;
			var data="action=spare_parts_work_print&data="+perameters;
	
			print_report(perameters, action, path);
		}
		
		
	function change_user()
	{
		
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		
		
		var title = 'Approval User Info';	
		var page_link = 'requires/other_purchase_work_order_approval_controller.php?action=user_popup&company_id='+$("#cbo_company_name").val();
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var data=this.contentDoc.getElementById("selected_id").value; //Access form field with id="emailfield"
			var data_arr=data.split("_");
			$("#txt_alter_user_id").val(data_arr[0]);
			$("#txt_alter_user").val(data_arr[1]);
			$("#report_container").html('');
		}
	}
		
</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",''); ?>
		 <form name="requisitionApproval_1" id="requisitionApproval_1">
         <h3 style="width:780px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
         <div id="content_search_panel">
             <fieldset style="width:780px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                        	<tr>
                            	<th colspan="2">Barcode Scan: <input type="text" id="txt_bar_code" name="txt_bar_code" class="text_boxes"  /></th>
                                <th colspan="2"></th>
                                <th colspan="3">
                                   <?
                                   $user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
									if( $user_lavel==2)
									{?>
                                    Alter User: 
                                    <input id="txt_alter_user" name="txt_alter_user" type="text" onDblClick="change_user();" class="text_boxes" style="width:150px" placeholder="Browse">
                                    
                                    <? } ?>
                                    <input id="txt_alter_user_id" name="txt_alter_user_id" type="hidden">
                                </th>
                            </tr>
                            <tr>
                                <th width="160">Company Name</th>
                                <th>Supplier</th>
								<th>Item Category</th>
                                <th width="100">WO No</th>
                                <th width="130" colspan="2">WO Date Range</th>
                                <th width="140">Approval Type</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                        	</tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td><?=create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0  $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/other_purchase_work_order_approval_controller',this.value, 'load_supplier_dropdown', 'supplier_td_id' );" ); ?></td>
                                <td id="supplier_td_id"> 
									<?
                                       echo create_drop_down( "cbo_supplier_id", 152, $blank_array,"", 1, "-- All Supplier --", 0, "" );
                                    ?>
                                </td>
								<td>
                                    <? 
                                        echo create_drop_down( "cbo_item_category_id", 160, $item_category,"", 1, "-- All Category --", $selected,"",0,"$user_cred_item_cat_cond","","","");
                                    ?>
                                </td>
                                <td><input type="text" name="txt_wo_no"  id="txt_wo_no"  style="width:90px" class="text_boxes" placeholder="WO No" /></td>
                                <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px;" placeholder="From Date"/></td>					
                        		<td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px;" placeholder="To Date" /></td>
                                <td><?=create_drop_down( "cbo_approval_type", 140, $approval_type_arr,"", 1, "", $selected,"","", "" ); ?></td>
                                <td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:100px" onClick="fn_report_generated()"/></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container" align="center"></div>
    <div id="data_panel" align="center"></div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>  
</html>

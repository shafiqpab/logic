<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Dyes & Chemical WO Approval
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	19-01-2021
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
$user_id = $_SESSION['logic_erp']['user_id'];
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Dyes & Chemical WO Approval", "../", 1, 1,'','','');
$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "page_id=$menu_id and is_deleted=0" );


$userCredential = sql_select("SELECT unit_id as company_id, item_cate_id FROM user_passwd where id=$user_id");
$item_cate_id = $userCredential[0][csf('item_cate_id')];

if($item_cate_id!='') {
	$cre_cat_arr=explode(",",$item_cate_id);
	$selected_category=array( '5', '6', '7', '23' );
	$filteredArr = array_intersect( $cre_cat_arr, $selected_category );
    $item_cate_credential_cond = implode(",",$filteredArr);
}
else $item_cate_credential_cond="5,6,7,23";
?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<?=$permission; ?>';

	function fn_report_generated()
	{
		var approval_setup =<?=$approval_setup; ?>;
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
		
		var data="action=report_generate&previous_approved="+previous_approved+get_submitted_data_string('cbo_company_name*cbo_supplier*cbo_item_category_id*cbo_year*txt_wo_no*cbo_get_upto*txt_date*cbo_approval_type*txt_alter_user_id',"../");
		
		http.open("POST","requires/dyes_chemical_wo_approval_controller.php",true);
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
	
	function check_on_scan(scan_no)
	{
		var row_no=$('#'+scan_no).val();
		var tbl_len=$("#tbl_list_search tbody tr").length;
		$('#tbl_'+row_no).attr('checked', true);
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
		var booking_nos = "";  var booking_ids = ""; var approval_ids = ""; 
		freeze_window(0);
		// confirm message  *********************************************************************************************************
		if($('#cbo_approval_type').val()==1)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to UnApproved All WO.");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnApproved All WO.");
					if(second_confirmation==false)
					{
						release_freezing();
						return;					
					}
				}
			}
		}
		// confirm message finish ***************************************************************************************************

		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				booking_id = $('#booking_id_'+i).val();
				if(booking_ids=="") booking_ids= booking_id; else booking_ids +=','+booking_id;
				
				booking_no = $('#booking_no_'+i).val();
				if(booking_nos=="") booking_nos="'"+booking_no+"'"; else booking_nos +=",'"+booking_no+"'";
				
				approval_id = parseInt($('#approval_id_'+i).val());
				if(approval_id>0)
				{
					if(approval_ids=="") approval_ids= approval_id; else approval_ids +=','+approval_id;
				}
			}
		}
		
		if(booking_nos=="")
		{
			alert("Please Select At Least One WO.");
			release_freezing();
			return;
		}
		
		
		if(type==5){
			$('#txt_selected_id').val(booking_ids);
			fnSendMail('../','',1,0,0,1);
		}
		
		
		
		var data="action=approve&operation="+operation+'&approval_type='+type+'&booking_nos='+booking_nos+'&booking_ids='+booking_ids+'&approval_ids='+approval_ids+get_submitted_data_string('cbo_company_name*txt_alter_user_id',"../");
	   //alert(data);
		
		http.open("POST","requires/dyes_chemical_wo_approval_controller.php",true);
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
			if((reponse[0]==19 || reponse[0]==20 || reponse[0]==50))
			{
				fnc_remove_tr();
			}
			if(reponse[0]==25)
			{
				fnc_remove_tr();
				alert("You Have No Authority To Approved this.");
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
	
	function generate_worder_report(action,upid,company_id)
	{
		var form_caption="Dyes & Chemical Work Order";
		print_report( company_id+'*'+upid+'*'+form_caption+'*'+1, action, "../commercial/work_order/requires/dyes_and_chemical_work_order_controller");
	}
	
	function openImgFile(id,action)
	{
		var page_link='requires/dyes_chemical_wo_approval_controller.php?action='+action+'&id='+id;
		if(action=='img') var title='Image View'; else var title='File View';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','');
	}

	//barcode scan
	$('#txt_bar_code').live('keydown', function(e) {
		if  (e.keyCode === 13) 
		{
			//alert("su..re"); return;
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
		var title = 'Alter User Info';	
		var page_link = 'requires/dyes_chemical_wo_approval_controller.php?action=user_popup'+get_submitted_data_string('cbo_company_name',"../");
		  
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
	
	function call_print_button_for_mail(mail){
		 
		var booking_id=$('#txt_selected_id').val();
		var sysIdArr=booking_id.split(',');
		
		var mail=mail.split(',');
		var ret_data=return_global_ajax_value(sysIdArr.join('*')+'__'+mail.join('*'), 'yarn_work_order_app', '', '../auto_mail/approval/dyes_chemical_wo_approval_auto_mail');
		alert(ret_data);
	}
	
	
</script>
</head>
<body>
	<div style="width:100%;" align="center">
	<?=load_freeze_divs ("../",''); ?>
     <form name="dyeChemWoApproval_1" id="dyeChemWoApproval_1">
         <h3 style="width:950px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:950px;">
                <table class="rpt_table" width="950px" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                    <thead>
                        <tr> 
                            <th colspan="2">Barcode Scan:<input type="text" id="txt_bar_code" name="txt_bar_code" class="text_boxes" style="width:90px" /></th>
                            <th colspan="3">
                            <?php 
                                $user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
                                if($user_lavel==2)
                                {
                                    ?>Previous Approved:<input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes" value="0" onChange="change_approval_type(this.value);" style="width:30px" /><?php
                                }
                                else
                                {
                                    ?><input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes" style="display:none;width:30px" /><?php	
                                }
                            ?> 
                             </th>
                            <th colspan="4">
                            <?php 
                                $user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
                                if( $user_lavel==2)
                                {
                                    ?>Alter User:<input type="text" id="txt_alter_user" name="txt_alter_user" class="text_boxes"  onDblClick="change_user();" placeholder="Browse" readonly /><?php 
                                }
                            ?>
                            <input type="hidden" id="txt_alter_user_id" name="txt_alter_user_id" /> 
                             <input type="hidden" id="txt_selected_id" name="txt_selected_id" />
                            </th>
                        </tr>
                        <tr>
                            <th width="140" class="must_entry_caption">Company Name</th>
							<th width="120">Supplier</th>
                            <th width="120">Item Category</th>
                            <th width="60">WO Year</th>
                            <th width="80">WO No.</th>
                            <th width="100">Get Upto</th>
                            <th width="70">WO Date</th>
                            <th width="100">Approval Type</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dyeChemWoApproval_1','report_container','','','')" class="formbutton" style="width:70px" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td><?=create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/dyes_chemical_wo_approval_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );" ); ?></td>
							<td id="supplier_td">
								<?
									echo create_drop_down( "cbo_supplier", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id  and b.party_type in(3) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
								?>
							</td>
                            <td><?=create_drop_down( "cbo_item_category_id", 120, $item_category,"", 1, "-All-", $selected, "",0,$item_cate_credential_cond ); ?></td>
                            <td><?=create_drop_down( "cbo_year", 60, $year,"", 1, "-All-", 0, "" ); ?></td>
                            <td><input name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:70px"></td>
                            <td> 
                                <?
                                    $get_upto=array(1=>"After This Date",2=>"As On Today",3=>"This Date");
                                    echo create_drop_down( "cbo_get_upto", 100, $get_upto,"", 1, "-- Select --", 0, "" );
                                ?>
                            </td>
                            <td><input type="text" name="txt_date" id="txt_date" class="datepicker" readonly style="width:60px"/></td>
                            <td> 
                                <?
                                    $pre_cost_approval_type=array(2=>"Un-Approved",1=>"Approved");
                                    echo create_drop_down( "cbo_approval_type", 100, $pre_cost_approval_type,"", 0, "", $selected,"","", "" );
                                ?>
                            </td>
                            <td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:70px" onClick="fn_report_generated();"/></td>
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
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$('#cbo_approval_type').val(2);
</script>
</html>
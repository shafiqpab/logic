<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create TNA Approval Group By
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam Reza
Creation date 	: 	01-01-2024
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
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("TNA Approval Group By", "../", 1, 1,'','','');

$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "entry_form=41 and is_deleted=0" );
?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';

	function fn_report_generated()
	{
		var approval_setup =<? echo $approval_setup; ?>;

		if(approval_setup!=1)
		{
			alert("Electronic Approval Setting First.");	
			return;
		}
		else if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_id*cbo_get_upto*txt_date*cbo_approval_type*cbo_template_id*txt_job_no*txt_alter_user_id',"../");
		freeze_window(3);
		http.open("POST","requires/tna_approval_group_by_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = () =>{
			if(http.readyState == 4) 
			{
				$('#report_container').html(http.responseText);
				show_msg('3');
				release_freezing();
			}
		}
	}
	
 
	
	function submit_approved(total_tr,type)
	{ 
		var job_nos = "";  var po_ids = "";  var appv_instras="";
		
		// Confirm Message  ----------------
		if($('#cbo_approval_type').val()==1)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to UnApproved All PO Number");
				if(first_confirmation==false)
				{
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnApproved All PO Number");
					if(second_confirmation==false)
					{
						return;					
					}
				}
			}
			
		}
		else if($('#cbo_approval_type').val()==0)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to Approved All PO Number");
				if(first_confirmation==false)
				{
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to Approved All PO Number");
					if(second_confirmation==false)
					{
						return;					
					}
				}
			}
		}
		//---------------- Confirm Message End;
		var po_id_arr = Array();var job_no_arr = Array();var appv_instra_arr = Array();
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				var po_id = $('#po_id_'+i).val();
				po_id_arr.push(po_id);
				var job_no = $('#job_no_'+i).val();
				job_no_arr.push(job_no);
				var appv_instra = $('#txt_appv_instra_'+i).val();
				appv_instra_arr.push(appv_instra);
			}
		}
		
		job_nos = job_no_arr.join(',');  po_ids = po_id_arr.join(','); appv_instras = appv_instra_arr.join(',');

		if(job_nos=="")
		{
			alert("Please Select At Least One PO Number");
			return;
		}
		
		var alterUserID = $('#txt_alter_user_id').val();

		var data="action=approve&operation="+operation+'&approval_type='+type+'&job_nos='+job_nos+'&po_ids='+po_ids+'&txt_alter_user_id='+alterUserID+'&appv_instras='+appv_instras+get_submitted_data_string('cbo_company_name',"../");
	
		freeze_window(operation);
		
		http.open("POST","requires/tna_approval_group_by_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = () =>{
			if(http.readyState == 4) 
			{ 
				var reponse=trim(http.responseText).split('**');	
				show_msg(trim(reponse[0]));
				if(reponse[0]==1)
				{
					fnc_remove_tr();
				}
				$('#txt_bar_code').val('');
				$('#txt_bar_code').focus();
				release_freezing();	
			}
		}
	}	
	
 
	
	
	function check_all(tot_check_box_id)
	{
		if ($('#'+tot_check_box_id).is(":checked"))
		{ 
			$("#tbl_list_search tbody").find('tr').each(function()
			{
				try 
				{
					var dealing_merchant=$(this).find("td:eq(8)").text();
					var job_no=$(this).find('input[name="job_no[]"]').val();
					var hide_approval_type=parseInt($('#hide_approval_type').val());
					
					if(!(hide_approval_type==1))
					{
						$(this).find('input[name="tbl[]"]').attr('checked', true);
					}
					else
					{
						$(this).find('input[name="tbl[]"]').attr('checked', true);
					}
				}
				catch(e) 
				{
					//got error no operation
				}
				
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
		var dealing_merchant=$('#dealing_merchant_'+row_no).html();
		var hide_approval_type=parseInt($('#hide_approval_type').val());
		var tbl_len=$("#tbl_list_search tbody tr").length;
		if(!(hide_approval_type==1))
		{
			var last_update=return_global_ajax_value( trim(scan_no), 'check_job_last_update', '', 'requires/tna_approval_group_by_controller');
			if(last_update==2)
			{
				alert("Booking ("+trim(scan_no)+") Info not synchronized with order entry and pre-costing. Contact To "+dealing_merchant+".");
				$('#tbl_'+row_no).attr('checked', false);
			}
			else
			{
				$('#tbl_'+row_no).attr('checked', true);
			}
		}
		else
		{
			$('#tbl_'+row_no).attr('checked', true);
		}
		
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
		$('#all_check').attr('checked',false);
	}
	
	function generate_worder_report(txt_job_no,cbo_company_name,txt_order_no_id,cbo_fabric_natu,cbo_fabric_source,txt_job_no,id_approved_id,type,i)
	{
		//var report_title='Budget Wise Fabric Booking';
		var data="action="+type+
		'&txt_job_no='+"'"+txt_job_no+"'"+
		'&cbo_company_name='+"'"+cbo_company_name+"'"+
		'&txt_order_no_id='+"'"+txt_order_no_id+"'"+
		'&cbo_fabric_natu='+"'"+cbo_fabric_natu+"'"+
		'&cbo_fabric_source='+"'"+cbo_fabric_source+"'"+
		'&id_approved_id='+"'"+id_approved_id+"'"+
		'&report_title='+"Budget Wise Fabric Booking"+
		'&txt_job_no='+"'"+txt_job_no+"'"+
		
		'&path=../';
		//alert(data);return;
			//var data="action="+show_fabric_job_report_gr+get_submitted_data_string('txt_job_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*id_approved_id*txt_job_no',"../../")+'&report_title='+$report_title+'&show_yarn_rate='+show_yarn_rate+'&path=../../';
			
			//$report_title=$( "div.form_caption" ).html();
			
			//var data="action="+type+get_submitted_data_string('txt_job_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*id_approved_id*txt_job_no*i',"../../")+'&path=../../';
			
			//freeze_window(5);
			//http.open("POST","requires/fabric_job_controller.php",true);
			http.open("POST","../order/woven_order/requires/fabric_job_controller.php",true);
						
					
		/*if(type==1)	
		{			
			http.open("POST","../order/woven_order/requires/short_fabric_job_controller.php",true);
		}
		else if(action=='show_fabric_job_report_gr')
		{
			http.open("POST","../order/woven_order/requires/fabric_job_controller.php",true);
		}
		else
		{
			http.open("POST","../order/woven_order/requires/sample_job_controller.php",true);
		}*/
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4) 
		    {
				var w = window.open("Surprise", "_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				d.close();
		   }
			
		}
	}
	
	function generate_worder_report2(type,job_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved,action)
	{
		var data="action="+action+
					'&txt_job_no='+"'"+job_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&txt_order_no_id='+"'"+order_id+"'"+
					'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
					'&cbo_fabric_source='+"'"+fabric_source+"'"+
					'&id_approved_id='+"'"+approved+"'"+
					'&txt_job_no='+"'"+job_no+"'"+
					'&path=../';
					
		if(type==1)	
		{			
			http.open("POST","../order/woven_order/requires/short_fabric_job_controller.php",true);
		}
		else if(type==2)
		{
			http.open("POST","../order/woven_order/requires/fabric_job_controller.php",true);
		}
		else
		{
			http.open("POST","../order/woven_order/requires/sample_job_controller.php",true);
		}
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4) 
		    {
				var w = window.open("Surprise", "_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				d.close();
		   }
		}
	}
		
	function generate_fabric_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
			d.close();
		}
	}
	
	
	function generate_mkt_report(job_no,job_no,order_id,fab_nature,fab_source,action)
	{
		var page_link='requires/tna_approval_group_by_controller.php?action='+action+'&job_no='+job_no+'&job_no='+job_no+'&order_id='+order_id+'&fab_nature='+fab_nature+'&fab_source='+fab_source;
		var title='Comments View';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=370px,center=1,resize=1,scrolling=0','');
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
		var title = 'TNA Approval';	
		var page_link = 'requires/tna_approval_group_by_controller.php?action=user_popup'+get_submitted_data_string('cbo_company_name',"../");
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=680px,height=390px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var data=this.contentDoc.getElementById("selected_id").value; //Access form field with id="emailfield"
			
			var data_arr=data.split("_");
			$("#txt_alter_user_id").val(data_arr[0]);
			$("#txt_alter_user").val(data_arr[1]);
			load_drop_down( 'requires/tna_approval_group_by_controller',$("#cbo_company_name").val()+"_"+data_arr[0], 'load_drop_down_buyer_new_user', 'buyer_td_id' );
			$("#report_container").html('');
		}
	}
	
	
	
	function get_template()
	{
		
		var title = 'TNA Template';	
		var page_link = 'requires/tna_approval_group_by_controller.php?action=get_template'+get_submitted_data_string('cbo_company_name*cbo_buyer_id',"../");
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var data=this.contentDoc.getElementById("selected_id").value; //Access form field with id="emailfield"
			
			var data_arr=data.split("_");
			$("#cbo_template_id").val(data_arr[0]);
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
	
	
</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",''); ?>
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:1000px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:1000px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                        	<tr> 
                            	<th colspan="2">Barcode Scan: <input type="text" id="txt_bar_code" name="txt_bar_code" class="text_boxes"  /></th>
                                 <th colspan="4" align="center" valign="top">
                                 Unapproved Request: <input type="checkbox" id="unapproved_request" name="unapproved_request" class="text_boxes"  />
                                <?php 
									$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
									if( $user_lavel==2)
									{
										?>
                                		Previous Approved: <input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes"  value="0"  onChange="change_approval_type(this.value)" />

                                <?php
									}
									else
									{
								?>
                                		<input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes" style="display:none"  />
                                <?php	
									}
								?> 
                                 
                                 </th>
                                <th colspan="2">
                                <?php 
									$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
									if( $user_lavel==2)
									{
								?>
                                        Alter User:
                                        <input type="text" id="txt_alter_user" name="txt_alter_user" class="text_boxes"  onDblClick="change_user()" placeholder="Browse" readonly>
                                <?php 
									}
									
								?>
                                <input type="hidden" id="txt_alter_user_id" name="txt_alter_user_id" /> 
                                </th>
                            </tr>
                            <tr>
                                <th class="must_entry_caption">Company</th>
                                <th>Buyer</th>
                                <th>Get Upto</th>
                                <th>Ship Date</th>
                                <th>Job No</th>
                                <th>TNA Template</th>
                                <th>Approval Type</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/tna_approval_group_by_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                                    ?>
                                </td>
                                <td id="buyer_td"> 
									<?
                                       echo create_drop_down( "cbo_buyer_id", 152, $blank_array,"", 1, "-- All Supplier --", 0, "" );
                                    ?>
                                </td>
                                <td> 
									<?
										$get_upto=array(1=>"After This Date",2=>"As On Today",3=>"This Date");
                                       echo create_drop_down( "cbo_get_upto", 130, $get_upto,"", 1, "-- Select --", 0, "" );
                                    ?>
                                </td>
                                <td><input type="text" name="txt_date" id="txt_date" class="datepicker" readonly style="width:80px"/></td>
                                
                                <td>
                                	<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes_numeric" 
                                	style="width:100px;">
                                </td>
                                <td>
                                	<input type="text" id="cbo_template_id" name="cbo_template_id" class="text_boxes"  onDblClick="get_template()" placeholder="Browse" readonly style="width:150px">
                                </td>
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_approval_type", 130, $approval_type_arr,"", 0, "", $selected,"","", "" );
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
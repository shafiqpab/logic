<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Import Document Acceptance Approval v2
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam Reza
Creation date 	: 	20-06-2023
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
$approval_setup=is_duplicate_field( "entry_form", "electronic_approval_setup", "entry_form=38 and is_deleted=0" );

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Import Document Acceptance Approval v2", "../", 1, 1,'','','');
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
		var data="action=report_generate&previous_approved="+previous_approved+get_submitted_data_string('cbo_company_name*cbo_supplier_id*cbo_get_upto*txt_date*cbo_approval_type*txt_alter_user_id*txt_invoice_no',"../");
		//alert(data);
		
		http.open("POST","requires/import_document_acceptance_approval_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			$('#report_container2').html(response[0]);
			
			var tableFilters = { col_0: "none" }
			setFilterGrid("tbl_list_search",-1,tableFilters);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
			show_msg('3');
			$('#txt_bar_code').focus();
			release_freezing();
		}
	}
	
	function new_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close(); 
	}
	

	function submit_approved(total_tr,type,permission)
	{ 
		var invoice_ids = "";
		freeze_window(3);
        if (permission ==2) {            
            alert('You Have No Authority for signing Invoice Approval');
			release_freezing(); 
            return false;	    
        }
		// Confirm Message  **********************************************
		if($('#cbo_approval_type').val()==1)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to UnApproved All Booking No");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnApproved All Booking No");
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
				first_confirmation=confirm("Are You Want to Approved All Invoice");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to Approved All Invoice");
					if(second_confirmation==false)
					{
						release_freezing();
						return;					
					}
				}
			}
		}
		// Confirm Message End ************************************************
		
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				var invoice_id = parseInt($('#invoice_id_'+i).val());
				if(invoice_id>0)
				{
					if(invoice_ids=="") invoice_ids= invoice_id; else invoice_ids +=','+invoice_id;
				}

				if ($('#is_posted_account_'+i).val()==1)
				{
					var invoiceNO=$('#invoice_no_'+i).val();
					alert("This invoice no "+invoiceNO+ " is already posted in account!!");
					release_freezing();
					return;
				}
			}				
		}

		if(invoice_ids=="")
		{
			alert("Please Select At Least One");
			release_freezing();
			return;
		}
		
		// if($('#cbo_approval_type').val()==1)
		// {
		// 	var import_payment_data_res=return_global_ajax_value( invoice_ids, 'check_import_payment', '', 'requires/import_document_acceptance_approval_controller_v2');
		// 	if(import_payment_data_res!==''){
		// 		alert("Unapprove Not Allow. Import Payment Found Below System Number "+import_payment_data_res);
		// 		release_freezing();return;	
		// 	}
		// }
		
		
		//return;
		var alterUserID = $('#txt_alter_user_id').val();
		var alterUserID = $('#txt_alter_user_id').val();
		var data="action=approve&operation="+operation+'&approval_type='+type+'&invoice_ids='+invoice_ids+'&txt_alter_user_id='+alterUserID+get_submitted_data_string('cbo_company_name',"../");
	
		http.open("POST","requires/import_document_acceptance_approval_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_pi_approval_Reply_info;
	}	
	
	function fnc_pi_approval_Reply_info()
	{
		if(http.readyState == 4) 
		{ 
			var reponse=trim(http.responseText).split('**');	

			show_msg(trim(reponse[0]));
			
			if((reponse[0]==19 || reponse[0]==20))
			{
				fnc_remove_tr();
			}
			else if(reponse[0]==50){alert("Import Payment For At Sight Invoice No found: [ "+reponse[1]+" ]");}
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
		
		$('#all_check').attr('checked',false);
	}
	
	
	function check_all(tot_check_box_id)
	{
		if ($('#'+tot_check_box_id).is(":checked"))
		{ 
			$("#tbl_list_search tbody").find('tr').each(function()
			{
				try 
				{
					$(this).find('input[name="tbl[]"]').attr('checked', true);
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
	
	
	function fn_check_uncheck(i){
		if ($('#tbl_'+i).is(":checked"))
		{
			$('#tbl_'+i).attr('checked', false);
		}
		else
		{
			$('#tbl_'+i).attr('checked', true);	
		}
	}
	
	function change_user()
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		var title = 'PI Approval New';	
		var page_link = 'requires/import_document_acceptance_approval_controller_v2.php?action=user_popup'+get_submitted_data_string('cbo_company_name',"../");
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var data=this.contentDoc.getElementById("selected_id").value; //Access form field with id="emailfield"
			
			var data_arr=data.split("_");
			$("#txt_alter_user_id").val(data_arr[0]);
			$("#txt_alter_user").val(data_arr[1]);
			//load_drop_down( 'requires/import_document_acceptance_approval_controller_v2',$("#cbo_company_name").val()+"_"+data_arr[0], 'load_drop_down_buyer_new_user', 'buyer_td_id' );
			$("#report_container").html('');
		}
	}
	
	function downloiadFile(id,company_name)
	{
		var title = 'PI Approval New File Download';	
		var page_link = 'requires/import_document_acceptance_approval_controller_v2.php?action=get_user_pi_file&id='+id+'&company_name='+company_name;
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			
		}
	}
	
	// .....................................................................
	function check_on_scan_____________(scan_no)
	{
		var row_no=$('#'+scan_no).val();
		var dealing_merchant=$('#dealing_merchant_'+row_no).html();
		var hide_approval_type=parseInt($('#hide_approval_type').val());
		var tbl_len=$("#tbl_list_search tbody tr").length;
		if(!(hide_approval_type==1))
		{
			var last_update=return_global_ajax_value( trim(scan_no), 'check_booking_last_update', '', 'requires/import_document_acceptance_approval_controller_v2');
			if(last_update==2)
			{
				alert("Booking ("+trim(scan_no)+") Info not synchronized with order entry and pre-costing. Contact To "+dealing_merchant+".");
				$('#tbl_'+row_no).attr('checked', false);
			}
			else
			{
				$('#tbl_'+row_no).attr('checked', true);
				//submit_approved(tbl_len,$('#cbo_approval_type').val());
			}
		}
		else
		{
			$('#tbl_'+row_no).attr('checked', true);
			//submit_approved(tbl_len, $('#cbo_approval_type').val());
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
	
	function check_last_update_____________(rowNo)
	{
		var isChecked=$('#tbl_'+rowNo).is(":checked");
		var dealing_merchant=$('#dealing_merchant_'+rowNo).text();
		var booking_no=$('#booking_no_'+rowNo).val();
		var booking_id=$('#booking_id_'+rowNo).val();
		var hide_approval_type=$('#hide_approval_type').val();
		
		if(isChecked==true && hide_approval_type!=1)
		{
			var last_update=return_global_ajax_value( trim(booking_id), 'check_booking_last_update', '', 'requires/import_document_acceptance_approval_controller_v2');
			if(last_update==2)
			{
				alert("Booking ("+trim(booking_no)+") Info not synchronized with order entry and pre-costing. Contact To "+dealing_merchant+".");
				$('#tbl_'+rowNo).attr('checked',false);
			}
			//return;
		}
	}
	// ==============================================
	
	
	function generate_worder_report_____________(txt_booking_no,cbo_company_name,txt_order_no_id,cbo_fabric_natu,cbo_fabric_source,txt_job_no,id_approved_id,type,i)
	{
		var data="action="+type+
		'&txt_booking_no='+"'"+txt_booking_no+"'"+
		'&cbo_company_name='+"'"+cbo_company_name+"'"+
		'&txt_order_no_id='+"'"+txt_order_no_id+"'"+
		'&cbo_fabric_natu='+"'"+cbo_fabric_natu+"'"+
		'&cbo_fabric_source='+"'"+cbo_fabric_source+"'"+
		'&id_approved_id='+"'"+id_approved_id+"'"+
		'&report_title='+"Budget Wise Fabric Booking"+
		'&txt_job_no='+"'"+txt_job_no+"'"+
		
		'&path=../';
			http.open("POST","../order/woven_order/requires/fabric_booking_controller.php",true);
						
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4) 
		    {
				var w = window.open("Surprise", "_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+http.responseText+'</body</html>');
				d.close();
		   }
			
		}
	}
	
	$('#txt_bar_code').live('keydown', function(e) {
		if  (e.keyCode === 13) 
		{
			e.preventDefault();
			var bar_code=$('#txt_bar_code').val();
			check_on_scan(bar_code);
		}
	});
	
	
	function change_approval_type_____________(value)
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

	function fnc_pi_cross_check_____________(pi_id,company_id) {
		
		var title = 'Cross Check Details';
		var page_link = 'requires/import_document_acceptance_approval_controller_v2.php?item_category=1&importer_id='+company_id+'&pi_id='+pi_id+'&action=cross_check_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=320px,height=250px,center=1,resize=1,scrolling=0','../');
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
                            	<th colspan="2"><!--Barcode Scan: <input type="text" id="txt_bar_code" name="txt_bar_code" class="text_boxes"  />--></th>
                                 <th colspan="3" align="center">&nbsp;</th>
                                <th colspan="3">
                                <?php 
									$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
									if( $user_lavel==2)
									{
								?>
                                        Alter User:
                                        <input type="text" id="txt_alter_user" name="txt_alter_user" class="text_boxes"  onDblClick="change_user()"/ placeholder="Browse " readonly>
                                <?php 
									}
									
								?>
                                <input type="hidden" id="txt_alter_user_id" name="txt_alter_user_id" /> 
                                <? echo get_help_button('../','import_document_acceptance_approval',1);?>
                                </th>
                            </tr>
                            <tr>
                                <th class="must_entry_caption">Company</th>
                                <th>Supplier</th>
                                <th>Invoice No</th>
                                <th>Get Upto</th>
                                <th>Invoice Date</th>
                                <th>Approval Type</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/import_document_acceptance_approval_controller_v2',this.value, 'load_supplier_dropdown', 'supplier_td_id' );" );
                                    ?>
                                </td>
                                <td id="supplier_td_id"> 
									<?
                                       echo create_drop_down( "cbo_supplier_id", 152, $blank_array,"", 1, "-- All Supplier --", 0, "" );
                                    ?>
                                </td>
                                <td><input type="text" name="txt_invoice_no" id="txt_invoice_no" class="text_boxes" style="width:100px"/></td>
                                <td> 
									<?
										$get_upto=array(1=>"After This Date",2=>"As On Today",3=>"This Date");
                                       echo create_drop_down( "cbo_get_upto", 130, $get_upto,"", 1, "-- Select --", 0, "" );
                                    ?>
                                </td>
                                <td><input type="text" name="txt_date" id="txt_date" class="datepicker" readonly style="width:80px"/></td>
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
	<div id="report_container2" align="center"></div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$('#cbo_approval_type').val(0);
</script>
</html>
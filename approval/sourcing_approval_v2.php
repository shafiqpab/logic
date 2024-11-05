<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Sourcing Post Cost Approval
Functionality	:	
JS Functions	:
Created by		:	Shajib Jaman 
Creation date 	: 	25-08-2013
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
echo load_html_head_contents("Sourcing Post Cost Approval", "../", 1, 1,'','','');
$approval_setup=is_duplicate_field( "ENTRY_FORM", "electronic_approval_setup", "ENTRY_FORM=47 and is_deleted=0" );
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
		
		/*if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			release_freezing();
			return;
		}*/
		
		var previous_approved=0;
		if($('#previous_approved').is(":checked")) previous_approved=1;
		
		var data="action=report_generate&previous_approved="+previous_approved+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_style_ref*cbo_get_upto*txt_date*cbo_approval_type*txt_job_no*cbo_year*txt_alter_user_id',"../");
		
		http.open("POST","requires/sourcing_approval_v2_controller.php",true);
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
		//var operation=4;
		var booking_nos = "";  var booking_ids = ""; var approval_ids = ""; var mst_id_company_ids = ""; 
		freeze_window(0);
		// confirm message  *********************************************************************************************************
		if($('#cbo_approval_type').val()==1)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to UnApproved All Job");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnApproved All Job");
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
				
				
				if($('#cbo_approval_type').val()==1)
				{
					unapprov_msg = $('#unapprov_msg_'+i).val();
					RefuseCause = $('#txtCause_'+i).val();
					if(unapprov_msg=="")
					{
						alert('Un-approve request empty not allowed');
						release_freezing();
						return;
					}
					
				}
				
				booking_id = $('#booking_id_'+i).val();
				if(booking_ids=="") booking_ids= booking_id; else booking_ids +=','+booking_id;
				
				booking_no = $('#booking_no_'+i).val();
				if(booking_nos=="") booking_nos="'"+booking_no+"'"; else booking_nos +=",'"+booking_no+"'";
				
				approval_id = parseInt($('#approval_id_'+i).val());
				if(approval_id>0)
				{
					if(approval_ids=="") approval_ids= approval_id; else approval_ids +=','+approval_id;
				}
				
				
				var mst_id_company_id = $('#mst_id_company_id_'+i).val();
				if(mst_id_company_ids==""){mst_id_company_ids= mst_id_company_id;}
				else{mst_id_company_ids +=','+mst_id_company_id;}
				
				
				
			}
		}
		
		if(booking_nos=="")
		{
			alert("Please Select At Least One Job");
			release_freezing();
			return;
		}
		
		var data="action=approve&operation="+operation+'&approval_type='+type+'&booking_nos='+booking_nos+'&booking_ids='+booking_ids+'&approval_ids='+approval_ids+'&mst_id_company_ids='+mst_id_company_ids+get_submitted_data_string('cbo_company_name*txt_alter_user_id',"../");
	   //alert(data);
		
		http.open("POST","requires/sourcing_approval_v2_controller.php",true);
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
			if(reponse[0]==25)
			{
				fnc_remove_tr();
				alert("You Have No Authority To Approved this.");
			}
			if((reponse[0]==19 || reponse[0]==20 || reponse[0]==50))
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
	
	function generate_worder_report(type,job_no,company_id,buyer_id,style_ref,txt_costing_date,entry_form,quotation_id)
	{
		freeze_window(3);
		$("#txt_style_ref").val(style_ref);
		var zero_val='';
		var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		if (r==true) zero_val="1"; else zero_val="0";
		var path="../";
		var rate_amt=2;
		var data="action="+type+
			'&zero_value='+zero_val+
			'&rate_amt='+rate_amt+
			'&txt_job_no='+"'"+job_no+"'"+
			'&cbo_company_name='+"'"+company_id+"'"+
			'&cbo_buyer_name='+"'"+buyer_id+"'"+
			'&txt_sourcing_date='+"'"+txt_costing_date+"'"+
			'&path='+"'"+path+"'"+
			'&txt_po_breack_down_id='+"''"+
			'&cbo_costing_per='+"'0'"+get_submitted_data_string('txt_style_ref',"../");
			//alert(data)
			http.open("POST","../order/sourcing/requires/pre_cost_entry_controller_v2.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_fabric_report_reponse;
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
			release_freezing();
		}
	}

	function history_budget_sheet(company_id,job_no,buyer_id,style_id,cost_date,type,entry_from,garments_nature,revised_no)
	{
		var zero_val='';
		freeze_window(3);
		var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		if (r==true) zero_val="1"; else zero_val="0";
		
		var rate_amt=2;
		var data="action="+type+"&zero_value="+zero_val+
				'&rate_amt='+rate_amt+
				'&cbo_company_name='+"'"+company_id+"'"+
				'&cbo_buyer_name='+"'"+buyer_id+"'"+
				'&txt_style_ref='+"'"+style_id+"'"+
				'&txt_sourcing_date='+"'"+cost_date+"'"+
				'&revised_no='+revised_no+
				'&txt_job_no='+"'"+job_no+"'";
				
		if(entry_from==425 )
		{
			if(garments_nature==3)
			{
				http.open("POST","../order/sourcing/requires/pre_cost_entry_controller_v2.php",true);
			}		
		}

		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_report_reponse;
	}

	function fnc_generate_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+http.responseText+'</body</html>');
			d.close();
			release_freezing();
		}
	}
	
	function openImgFile(id,action)
	{
		var page_link='requires/sourcing_approval_v2_controller.php?action='+action+'&id='+id;
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
	
	/*function fnc_load_cm_compulsory(data)
	{
		$('#txt_cm_compulsory').val('');
		var cm_compulsory = return_global_ajax_value( data, 'populate_cm_compulsory', '', 'requires/sourcing_approval_v2_controller');
		$('#txt_cm_compulsory').val(cm_compulsory);
	}*/
	
	function change_user()
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		var title = 'Alter User Info';	
		var page_link = 'requires/sourcing_approval_v2_controller.php?action=user_popup'+get_submitted_data_string('cbo_company_name',"../");
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var data=this.contentDoc.getElementById("selected_id").value; //Access form field with id="emailfield"
			
			var data_arr=data.split("_");
			$("#txt_alter_user_id").val(data_arr[0]);
			$("#txt_alter_user").val(data_arr[1]);
			$("#cbo_approval_type").val(2);
			//load_drop_down( 'requires/sourcing_approval_v2_controller',$("#cbo_company_name").val()+"_"+data_arr[0], 'load_drop_down_buyer_new_user', 'buyer_td_id' );
			load_drop_down( 'requires/sourcing_approval_v2_controller',$("#cbo_company_name").val()+"_"+data_arr[0], 'load_drop_down_buyer_new_user', 'buyer_td_id' );
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
	
	function generat_print_report(type,company_name,buyer_name,date_from,date_to,job_no,job_id,order_id,order_no,year,order_status,search_date,season,season_id,file_no,internal_ref)
	{
		var data="action=report_generate"+
			'&reporttype='+type+
			'&cbo_company_name='+"'"+company_name+"'"+
			'&cbo_buyer_name='+"'"+buyer_name+"'"+
			'&txt_date_from='+"'"+date_from+"'"+
			'&txt_date_to='+"'"+date_to+"'"+
			'&txt_job_no='+"'"+job_no+"'"+
			'&txt_job_id='+"'"+job_id+"'"+
			'&txt_order_id='+"'"+order_id+"'"+
			'&txt_order_no='+"'"+order_no+"'"+
			'&cbo_year='+"'"+year+"'"+
			'&cbo_order_status='+"'"+order_status+"'"+
			'&cbo_search_date='+"'"+search_date+"'"+
			'&txt_season='+"'"+season+"'"+
			'&txt_season_id='+"'"+season_id+"'"+
			'&txt_file_no='+"'"+file_no+"'"+
			'&txt_internal_ref='+"'"+internal_ref+"'";
					
		freeze_window(3);
		if(type==1 || type==2 || type==3 || type==4 || type==7)
		{
			http.open("POST","../reports/management_report/merchandising_report/requires/order_wise_budget_report_controller.php",true);
		}
		else if (type==5 || type==6)
		{
			http.open("POST","../reports/management_report/merchandising_report/requires/order_wise_budget_report2_controller.php",true);
		}
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generat_print_report_reponse;
	}
	
	function generat_print_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			$('#report_container2').html(reponse[0]); 	
			
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'<link rel="stylesheet" href="../css/style_common.css" type="text/css" /></body</html>');//
			d.close();
			
			$('#report_container2').html(''); 	
			release_freezing();
			show_msg('3');
		}
	}
	
	function openmypage_refusing_cause(page_link,title,quo_id,sourcinng_refusing_cause)
	{
		var page_link = page_link + "&quo_id="+quo_id+ "&sourcinng_refusing_cause="+sourcinng_refusing_cause;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=280px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var cause=this.contentDoc.getElementById("txt_refusing_cause").value;
			if(cause!="")
			{
				fn_report_generated();
			}
		}
	}
</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",''); ?>
		 <form name="sourcingApproval_1" id="sourcingApproval_1">
         <h3 style="width:930px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:930px;">
                 	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                        	<tr> 
                            	<th colspan="4">Barcode Scan: <input type="text" id="txt_bar_code" name="txt_bar_code" class="text_boxes" /></th>
                                <th colspan="2">
                                <?php 
									$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
									if( $user_lavel==2)
									{
										?>Previous Approved: <input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes"  value="0"  onChange="change_approval_type(this.value)" /><?php
									}
									else
									{
										?><input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes" style="display:none" /><?php	
									}
								?> 
                                 </th>
                                <th colspan="3">
                                <?php 
									$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
									if( $user_lavel==2)
									{
										?>Alter User:<input type="text" id="txt_alter_user" name="txt_alter_user" class="text_boxes"  onDblClick="change_user()"/ placeholder="Browse " readonly><?php 
									}
								?>
                                <input type="hidden" id="txt_alter_user_id" name="txt_alter_user_id" /> 
                                </th>
                            </tr>
                            <tr>
                                <th width="150" >Company Name</th>
                                <th width="150">Buyer</th>
                                <th width="70">Year</th>
                                <th width="75">Job No.</th>
                                <th width="75">Style Ref.</th>
                                <th width="120">Get Upto</th>
                                <th width="80">Sourcing Date</th>
                                <th width="120">Approval Type</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('sourcingApproval_1','report_container','','','')" class="formbutton" style="width:80px" /> <input style="width:50px;" type="hidden" name="txt_cm_compulsory" id="txt_cm_compulsory"/></th>
                        	</tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td><? echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- All --", $selected, "load_drop_down( 'requires/sourcing_approval_v2_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );" ); ?></td>
                                <td id="buyer_td_id"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- All Buyer --", 0, "" ); ?></td>
                                <td> <? echo create_drop_down( "cbo_year", 70, $year,"", 1, "-- Select --", 0, "" ); ?></td>
                                <td><input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:65px"></td>
                                <td><input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:65px"></td>
                                <td> 
									<?
										$get_upto=array(1=>"After This Date",2=>"As On Today",3=>"This Date");
                                        echo create_drop_down( "cbo_get_upto", 120, $get_upto,"", 1, "-- Select --", 0, "" );
                                    ?>
                                </td>
                                <td><input type="text" name="txt_date" id="txt_date" class="datepicker" readonly style="width:70px"/></td>
                                <td> 
                                    <?
									  	$pre_cost_approval_type=array(2=>"Un-Approved",1=>"Approved");
                                        echo create_drop_down( "cbo_approval_type", 120, $pre_cost_approval_type,"", 0, "", $selected,"","", "" );
                                    ?>
                                </td>
                                <td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:80px" onClick="fn_report_generated();"/></td>
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
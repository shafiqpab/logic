<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Service Booking For Kniting Approval
					
Functionality	:	

JS Functions	:

Created by		:	Md:Saidul Islam
Creation date 	: 	28-02-2018
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
echo load_html_head_contents("Service Booking For Kniting Approval", "../", 1, 1,'','','');

$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "page_id=$menu_id and is_deleted=0" );

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';
    
    // show button function 
	function fn_report_generated()
	{
		
		
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			release_freezing();
			return;
		}
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_approval_type*txt_booking_no*txt_date',"../");
		freeze_window(3);
		http.open("POST","requires/service_booking_for_knitting_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
    
	// show button response function 
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			$('#report_container').html(response[0]);
			
			var tableFilters = { col_0: "none" }
			setFilterGrid("tbl_list_search",-1,tableFilters);
				
			show_msg('3');
			$('#txt_bar_code').focus();
			release_freezing();
		}
	}
	
    // check_all check box function 
	function check_all(tot_check_box_id)
	{
        if ($('#'+tot_check_box_id).is(":checked"))
		{ 
			$("#tbl_list_search tbody").find('tr').each(function()
			{
				try 
				{                    
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
	
	function check_last_update(rowNo)
	{				       
        var isChecked = $('#tbl_'+rowNo).is(":checked");		
	}
	
    // Approve Button function 
	function submit_approved(total_tr,type)
	{ 
		//var operation=4;
		 var target_ids = ""; var approval_ids = ""; 
        freeze_window(0);
		// Confirm Message  ***************************************************************************************
		if($('#cbo_approval_type').val()==1)
		{
            if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to UnApproved All");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnApproved All");
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
				first_confirmation=confirm("Are You Want to Approved All");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to Approved All");
					if(second_confirmation==false)
					{
						release_freezing();
						return;					
					}
				}
			}
		}
		// Confirm Message End *******************************************************************
		
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				target_id = $('#target_id_'+i).val();
				if(target_ids=="") target_ids= target_id; else target_ids +=','+target_id;
				// alert(target_ids);
				approval_id = $('#approval_id_'+i).val();
				if(approval_ids=="") approval_ids= approval_id; else approval_ids +=','+approval_id;
				// alert(approval_ids);
			}
			
		}
		
		var data="action=approve&operation="+operation+'&approval_type='+type+'&approval_ids='+approval_ids + '&target_ids='+target_ids+get_submitted_data_string('cbo_company_name',"../");
	
		http.open("POST","requires/service_booking_for_knitting_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_purchase_requisition_approval_Reply_info;
	}	
	
    // Approve Button responds function 
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
	
	
	
function generate_trim_report(action,data_string)
{
	var data_arr=data_string.split('**');
		var show_comments='';
		if(action=='show_trim_booking_report')
		{
			var r=confirm("Press  \"Ok\"  to Hide  Comments\nPress  \"Cancel\"  to Show Comments");
			//alert(r)
			if (r==true)
			{
				show_comments="1";
			}
			else
			{
				show_comments="0";
			} 
		}
		
		
		if(action == "show_trim_booking_report3")
		{
			show_comments="1";
		}
		
var data="action="+action+'&show_comments='+show_comments+"&txt_booking_no='"+data_arr[0]+"'&cbo_company_name="+data_arr[1]+"'&id_approved_id="+data_arr[2]+"'&path=../";
		http.open("POST","../order/woven_order/requires/service_booking_knitting_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_trim_report_reponse;
		
}

function generate_trim_report_reponse()
{
	if(http.readyState == 4) 
	{
		var file_data=http.responseText.split('****');
		//$('#pdf_file_name').html(file_data[1]);
		$('#data_panel2').html(file_data[0] );
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel2').innerHTML+'</body</html>');
		d.close();
		$('#data_panel2').html('');

	}
}	
	
	function generate_fabric_report(type,job_no)
	{
		
			var show_yarn_rate='';
			var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
			if (r==true)
			{
				show_yarn_rate="1";
			}
			else
			{
				show_yarn_rate="0";
			} 
			$report_title='Main Fabric Booking';
			
			var response_data=return_global_ajax_value(job_no, 'populate_data_booking', '', 'requires/service_booking_for_knitting_approval_controller');
			var response_data_arr=trim(response_data).split("#");
			//var data_string='&txt_booking_no='+response_data_arr[0]+'&cbo_company_name='+response_data_arr[4]+'&txt_order_no_id='+response_data_arr[3]+'&cbo_fabric_natu='+response_data_arr[5]+'&cbo_fabric_source='+response_data_arr[2]+'&id_approved_id='+response_data_arr[1]+'&txt_job_no='+job_no+;
		//	alert(response_data);
			var data="action="+type+"&txt_booking_no='"+response_data_arr[0]+"'&cbo_company_name="+response_data_arr[4]+'&txt_order_no_id='+response_data_arr[3]+'&cbo_fabric_natu='+response_data_arr[5]+'&cbo_fabric_source='+response_data_arr[2]+'&id_approved_id='+response_data_arr[1]+"&txt_job_no='"+job_no+"'&report_title="+$report_title+'&show_yarn_rate='+show_yarn_rate+'&path=../';
			
			// alert(data);return;
			 //copyToClipboard( "asdasdasd_asdasdasd", 1 ) ; 
			//alert( fname )
			freeze_window(5);
			http.open("POST","../order/woven_order/requires/fabric_booking_urmi_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_fabric_report_reponse;
			
	}
	
	function generate_fabric_report_reponse()
	{
	//return;
		if(http.readyState == 4) 
		{
			var file_data=http.responseText.split('****');
			//$('#pdf_file_name').html(file_data[1]);
		//	$('#data_panel').html(file_data[0] );
			
			
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+file_data[0]+'</body></html>');
			d.close();
		//	var content=document.getElementById('data_panel').innerHTML;
			
			release_freezing();
			 
			
			
			//$.post("requires/fabric_booking_urmi_controller.php", { action: "create_file", data: content } );
		}
	}	
</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",'');?>
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:850px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:850px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                    <thead>                        	
                        <tr>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Buyer</th>
                            <th>Booking</th>
                            <th>Date</th>
                            <th>Approval Type</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> 
                                <?
                                    echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/service_booking_for_knitting_approval_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );" );
                                ?>
                            </td>
                            <td id="buyer_td_id"> 
                                <?
                                   echo create_drop_down( "cbo_buyer_name", 152, $blank_array,"", 1, "-- All Buyer --", 0, "" );
                                ?>
                            </td>                                
                            <td>
                            	<input type="text" name="txt_booking_no" id="txt_booking_no" value="" class="text_boxes" style="width:100px" /> 
                            </td>                                
                            <td> 
                            <input type="text" name="txt_date" id="txt_date" value="" class="datepicker" style="width:100px" /> 
                                
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
    <div id="data_panel2" align="center"></div>
	<div id="data_panel" align="center"></div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script> $('#cbo_approval_type').val(0); </script>
</html>
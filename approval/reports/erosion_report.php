<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Work Order for AOP Approval				
Functionality	:	
JS Functions	:
Created by		: Shajib Jaman
Creation date 	: 	03-07-2022
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start(); 
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');

extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$menu_id=$_SESSION['menu_id'];
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Erosion Approval", "../../", 1, 1,'','','');

$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "page_id=$menu_id and is_deleted=0" );

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
    
    // show button function 
	function fn_report_generated()
	{  
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			release_freezing();
			return;
		}
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_approval_type*cbo_search_type*txt_search_data*cbo_erosion_type*txt_erosion_date_from*txt_erosion_date_to',"../../");
	    
		http.open("POST","requires/erosion_report_controller.php",true);
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
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;&nbsp;<input type="button" onClick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
			var tableFilters = { col_0: "none" }
			setFilterGrid("tbl_list_search",-1,tableFilters);
				
			show_msg('3');
			$('#txt_bar_code').focus();
			release_freezing();
		}
	}
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$("#tbl_list_search tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'+
	   '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="330px";
		
		$("#tbl_list_search tr:first").show();
	}
	
	function open_popup(data,action) 
	{ //alert(cbo_company_name);
		var action='full_approved_popup';
		
		page_link='requires/erosion_report_controller.php?action='+action+'&data='+data;
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Approval Popup', 'width=600px, height=350px, center=1, resize=0, scrolling=0','../');
		emailwindow.onclose=function(){}
	}

	function pending_popup(data,action) 
	{ //alert(cbo_company_name);
		var action='pending_popup';
		
		page_link='requires/erosion_report_controller.php?action='+action+'&data='+data;
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Approval Popup', 'width=600px, height=350px, center=1, resize=0, scrolling=0','../');
		emailwindow.onclose=function(){}
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
		var approval_ids = ""; var target_ids = ""; 
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
		var target_ids="";var approval_ids="";
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{								
                target_id = parseInt($('#target_id_'+i).val());                            
                
				var comments =$('#comments_'+target_id).text();
				if(target_id && comments=='' && (type==0 || type==5)){
					alert("Please Write Comments to Approved or Deny");
					release_freezing();
					return;
				}
				
				if(target_id>0)
				{
					if(target_ids=="") target_ids= target_id; else target_ids +=','+target_id;
				
                }
                approval_id = parseInt($('#approval_id_'+i).val());
                if(approval_id>0)
				{
					if(approval_ids=="") approval_ids= approval_id; else approval_ids +=','+approval_id;
				}

				//if(type==2){
					$('#txt_selected_id').val(target_ids);
					fnSendMail('../../','',1,0,0,1,type);
				//}



			}
		}
		
		var data="action=approve&operation="+operation+'&approval_type='+type+'&approval_ids='+approval_ids + '&target_ids='+target_ids+get_submitted_data_string('cbo_company_name*txt_alter_user_id',"../../");
	
		http.open("POST","requires/erosion_report_controller.php",true);
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
			if((reponse[0]==19 || reponse[0]==20 || reponse[0]==50))
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
	
	
	
	 
function fn_generate_print(update_id,cbo_company_id){
		///alert(cbo_company_id);
	var data = "action=generate_print&operation=" + operation+"&update_id=" + update_id+"&cbo_company_id=" + cbo_company_id;
		
		freeze_window(operation);
		http.open("POST", "../../order/erosion/requires/erosion_entry_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_generate_print_res;
	
}

function fn_generate_print_res(){
	
		if(http.readyState == 4) 
		{
			release_freezing();
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+http.responseText+'</body</html>');
			d.close();
			
		}
}
	
	
	function change_user()
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		var title = 'PI Approval New';	
		var page_link = 'requires/erosion_report_controller.php?action=user_popup'+get_submitted_data_string('cbo_company_name',"../../");
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var data=this.contentDoc.getElementById("selected_id").value;
			var data_arr=data.split("_");
			$("#txt_alter_user_id").val(data_arr[0]);
			$("#txt_alter_user").val(data_arr[1]);
			$("#report_container").html('');
		}
	}
	
	
	 	
	
	function openmypage_refusing_cause(page_link,title,quo_id)
	{
		var cause=$("#comments_"+quo_id).text();
		var txt_alter_user_id=$("#txt_alter_user_id").val();
		var page_link = page_link + "&quo_id="+quo_id + "&cause="+cause + "&txt_alter_user_id="+txt_alter_user_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=280px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var cause=this.contentDoc.getElementById("txt_refusing_cause").value;
			document.getElementById("comments_"+quo_id).innerHTML=cause;
		}
	 }	


</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",'');?>
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:1000px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:1000px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                    <thead>                        	
					
                        <tr>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Buyer</th>
							<th>Search Type</th>
                            <th id="search_type_cap">Erosion No</th>
                            <th>Erosion Type</th>
                            <th colspan="2">Erosion Date</th>
                            <th>Approval Type</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container*report_container2','','','')" class="formbutton" style="width:100px" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> 
                                <?
                                    echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/erosion_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );" );
                                ?>
                            </td>
                            <td id="buyer_td_id"> 
                                <?
                                   echo create_drop_down( "cbo_buyer_name", 152, $blank_array,"", 1, "-- All Buyer --", 0, "" );
                                ?>
                            </td>                                
							<td > 
                                <?  
								  	$erosion_type=array(1=>"Erosion No",2=>"Job No",3=>"PO No"); 
                                   	echo create_drop_down( "cbo_search_type", 152, $erosion_type,"", 0, "-- Select --", 0, "$('#search_type_cap').text($('#cbo_search_type option:selected' ).text())" );
                                ?>
                            </td> 
							<td>
                            	<input type="text" name="txt_search_data" id="txt_search_data" value="" class="text_boxes" style="width:100px" /> 
                            </td>   
							<td > 
                                <?  
								   $erosion_type=array(1=>"Discount Shipment",2=>"Sea-Air Shipment",3=>"Air Shipment",4=>"Re-Inspection"); 
                                   echo create_drop_down( "cbo_erosion_type", 152, $erosion_type,"", 1, "-- Select --", 0, "" );
                                ?>
                            </td>     
                            <td> 
                            <input type="text" name="txt_erosion_date_from" id="txt_erosion_date_from" value="" class="datepicker" style="width:80px" /> 
                            </td> 
							<td> 
                            <input type="text" name="txt_erosion_date_to" id="txt_erosion_date_to" value="" class="datepicker" style="width:80px" /> 
                            </td>                                 
                            <td> 
                                <?
                                   $search_by_arr=array(2=>"Pending",3=>"Partial Approved",1=>"Full Approved",0=>"All");
								   echo create_drop_down("cbo_approval_type", 100, $search_by_arr, "", 0, "", "", "", 0);
                                ?>
                            </td>
                            <td>
								<input type="button" value="Show" name="show" id="show" class="formbutton" style="width:100px" onClick="fn_report_generated()"/>
								<input type="hidden" id="txt_selected_id" name="txt_selected_id" />
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
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script> $('#cbo_approval_type').val(0); </script>
</html>
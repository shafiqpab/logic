<?
/*-------------------------------------------- Comments
Purpose			: 	This page created for Garments Service Work Order Approval Report				
Functionality	:	
JS Functions	:
Created by		:	Shajib Jaman
Creation date 	: 	29-09-2022
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
	   if (form_validation('cbo_company_id','Comapny Name')==false)
		{
			return;
		}

		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_search_type*txt_search_data*txt_date_from*txt_date_to*cbo_approval_type',"../../");
		
		freeze_window(3);
		http.open("POST","requires/garments_service_work_order_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
    
	// show button response function 
	function fn_report_generated_reponse()
	{  //alert();
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;&nbsp;<input type="button" onClick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
			var tableFilters = { col_0: "none" }
			setFilterGrid("tbl_list_search",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}

	function open_popup(data,action) 
	{ //alert(data);
		var action='full_approved_popup';
		
		page_link='requires/garments_service_work_order_report_controller.php?action='+action+'&data='+data;
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Approval Popup', 'width=600px, height=350px, center=1, resize=0, scrolling=0','../');
		emailwindow.onclose=function(){}
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
		var target_id_arr=Array();
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
					target_id_arr.push(target_id);
                }
			}
		}

		if(target_id_arr.length==0){
			alert('Please select one for approve or unapprove');
			return;
		}


		

		var target_ids = target_id_arr.join(',');

		$('#txt_selected_id').val(target_ids);
		fnSendMail('../../','',1,0,0,1,type);


		
		var data="action=approve&operation="+operation+"&target_ids="+target_ids+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_search_type*txt_search_data*txt_date_from*txt_date_to*cbo_approval_type*txt_alter_user_id',"../../");
		freeze_window(0);
		http.open("POST","requires/garments_service_work_order_report_controller.php",true);
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
	
	let openImgFile=(id,action)=>{
		var page_link='requires/garments_service_work_order_report_controller.php?action='+action+'&id='+id;
		if(action=='garments_service_work_order') var title='Image View'; else var title='File View';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','');
	}	
	
	 
function fn_generate_print(update_id,cbo_company_id){
		
		if(confirm('Do you want to see subcontract reason, if yes press ok!')){
			update_id = update_id+'_1';
		}
		else{
			update_id = update_id+'_0';
		}
	
		var data = "action=price_rate_wo_print&operation=" + operation+"&data=" + update_id+"&cbo_company_id=" + cbo_company_id;
		
		freeze_window(operation);
		http.open("POST", "../../production/requires/garments_service_work_order_controller.php", true);
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
		if (form_validation('cbo_company_id','Comapny Name')==false)
		{
			return;
		}
		var title = 'PI Approval New';	
		var page_link = 'requires/garments_service_work_order_report_controller.php?action=user_popup'+get_submitted_data_string('cbo_company_id',"../../");
		  
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
	 
	 
	 function call_print_button_for_mail(mail,mail_body,type){
		 var booking_id=$('#txt_selected_id').val();
		 var txt_alter_user_id=$('#txt_alter_user_id').val();
		 var cbo_company_id=$('#cbo_company_id').val();
		 var sysIdArr=booking_id.split(',');
		 var mail=mail.split(',');
		 var ret_data=return_global_ajax_value(sysIdArr.join(',')+'__'+mail.join(',')+'__'+txt_alter_user_id+'__'+cbo_company_id+'__'+type, 'app_mail_notification', '', 'requires/garments_service_work_order_report_controller');
		 //alert(ret_data);
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
                            <th>Year</th>
                            <th>Search Type</th>
                            <th id="search_type_cap">Work Order</th>
                            <th colspan="2">Workorder  Date</th>
                            <th>Approval Type</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container*report_container2','','','')" class="formbutton" style="width:100px" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> 
                                <?
                                    echo create_drop_down( "cbo_company_id", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/garments_service_work_order_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );" );
                                ?>
                            </td>
                            <td id="buyer_td_id"> 
                                <?
                                   echo create_drop_down( "cbo_buyer_id", 152, $blank_array,"", 1, "-- All Buyer --", 0, "" );
                                ?>
                            </td>  
							<td>
                                <?
                                echo create_drop_down( "cbo_year", 110, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                                ?>
                            </td>                              
							<td > 
                                <?  
								  	$erosion_type=array(1=>"Work Order",2=>"Job No",3=>"PO No"); 
                                   	echo create_drop_down( "cbo_search_type", 152, $erosion_type,"", 0, "-- Select --", 0, "$('#search_type_cap').text($('#cbo_search_type option:selected' ).text())" );
                                ?>
                            </td> 
							<td>
                            	<input type="text" name="txt_search_data" id="txt_search_data" value="" class="text_boxes" style="width:100px" /> 
                            </td>      
                            <td> 
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:80px" /> 
                            </td> 
							<td> 
                            <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:80px" /> 
                            </td>                                 
                            <td> 
                                <?
                                    $search_by_arr=array(2=>"Pending",3=>"Partial Approved",1=>"Full Approved");
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
    <div id="data_panel2" align="center"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script> $('#cbo_approval_type').val(0); </script>
</html>
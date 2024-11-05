<?
/*-------------------------------------------- Comments
Purpose			: 	This page created for Garments Service Work Order Approval Report				
Functionality	:	
JS Functions	:
Created by		:	Shajib Jaman
Creation date 	: 	9-5-2023
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

		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_basis*txt_gate_pass_id*txt_job_no*txt_style_number*txt_date_from*txt_date_to*cbo_approval_type',"../../");
		//alert(data);
		freeze_window(3);
		http.open("POST","requires/gate_pass_approval_status_report_controller.php",true);
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
		
		page_link='requires/gate_pass_approval_status_report_controller.php?action='+action+'&data='+data;
		
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

	let openImgFile=(id,action)=>{
		var page_link='requires/gate_pass_approval_status_report_controller.php?action='+action+'&id='+id;
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
		var page_link = 'requires/gate_pass_approval_status_report_controller.php?action=user_popup'+get_submitted_data_string('cbo_company_id',"../../");
		  
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
	
	 
	 
	 function call_print_button_for_mail(mail,mail_body,type){
		 var booking_id=$('#txt_selected_id').val();
		 var txt_alter_user_id=$('#txt_alter_user_id').val();
		 var cbo_company_id=$('#cbo_company_id').val();
		 var sysIdArr=booking_id.split(',');
		 var mail=mail.split(',');
		 var ret_data=return_global_ajax_value(sysIdArr.join(',')+'__'+mail.join(',')+'__'+txt_alter_user_id+'__'+cbo_company_id+'__'+type, 'app_mail_notification', '', 'requires/gate_pass_approval_status_report_controller');
		 //alert(ret_data);
	 }

	 function generate_trims_print_report(company_id,sys_number,print_btn,location_id,emb_issue_ids,basis,returnable)
	{ 
		
		  var report_title="Gate Pass Entry";
				if(print_btn==116)
				{

					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else  
					{
						show_item="0";
					}
					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+show_item+'&action=print_to_html_report&template_id=1', true );
				}
				else if(print_btn==136)
				{
					if(basis==13){

						window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+report_title+'*'+emb_issue_ids+'&action=get_out_entry_emb_issue_print&template_id=1', true );
					}
				}
				else if(print_btn==137)
				{
				   var show_item=0;	
                   window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+show_item+'&action=print_to_html_report5&template_id=1', true );		        	
				}
				else if(print_btn==129)
				{
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}

					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+report_title+'*'+show_item+'*'+basis+'*'+location_id+'&action=get_out_entry_print12&template_id=1', true );	
					
					// return;
				}
				else if(print_btn==191)
				{
					
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+show_item+'&action=print_to_html_report_13&template_id=1', true );	

				}
				else if(print_btn==196)
				{
				
					if($("#cbo_basis").val()!=14)
					{
						alert('Report Generate only for Challan[Cutting Delivery] Basis');
					}
					else
					{
						var show_item=0;	
						window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+show_item+'*'+emb_issue_ids+'*'+location_id+'&action=print_to_html_report6&template_id=1', true );	

					}
				}
				else if(print_btn==199)
				{
					

					if(basis!=4 && basis!=3)
					{
						alert('Report Generate only for Challan[Grey Fabric] and Challan[Finish Fabric] Basis');
					}
					else
					{
						var show_item=0;	

						window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+show_item+'*'+emb_issue_ids+'&action=print_to_html_report7&template_id=1', true );	

					}
				}
				else if(print_btn==207)
				{
					if(basis==12)
					{
						var show_item='';			
 						window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+report_title+'*'+show_item+'*'+basis+'*'+location_id+'&action=print_to_html_report9&template_id=1', true );	
					}
					else
					{
						alert("This is for Garments Delivery Basis");
					}
				}
				else if(print_btn==208)
				{
					
					if(basis==28)
					{
						var show_item='';	

						window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+report_title+'*'+show_item+'*'+basis+'*'+location_id+'&action=print_to_html_report10&template_id=1', true );	
					}
					else
					{
						alert("This is for Sample Delivery Basis");
					}
				}
				else if(print_btn==212)
				{
					
					if(basis==2)
					{				
						window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'&action=print_to_html_report11&template_id=1', true );
					}
					else
					{
						alert("This is for Yarn Basis Only");
					}
				}
				else if(print_btn==271)
				{
					if(basis==11)
					{			
						window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'&action=print_to_html_report14&template_id=1', true );
					}
					else
					{
						alert("This is for Finish Fabric Delivery to Store Basis");
					}
				}
				else if(print_btn==707)
				{
					
					if (basis != 8){
					alert("This Button Only For Subcon Knitting Delevery Basis");
					return;
					}			
					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+basis+'*'+emb_issue_ids+'&action=print_to_html_report17&template_id=1', true );
				}
				else if(print_btn==115)
				{
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+report_title+'*'+show_item+'*'+basis+'*'+location_id+'&action=get_out_entry_print&template_id=1', true );
				}
				else if(print_btn==161)
				{
									
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&action=get_out_entry_print6&template_id=1', true );
				}
				else if(print_btn==206)
				{
					var show_item="0";
					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+report_title+'*'+show_item+'*'+basis+'*'+location_id+'&action=get_out_entry_print8_fashion&template_id=1', true );	
					return;
				}
				else if(print_btn==235)
				{
					
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&action=get_out_entry_print9&template_id=1', true );	
					return;
				}
				else if(print_btn==274)
				{
				
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+report_title+'*'+show_item+'*'+basis+'*'+location_id+'*'+1+'&action=get_out_entry_print10&template_id=1', true );	
				}
				else if(print_btn==738)
				{
					if(basis==13){

					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&action=get_out_entry_printamt&template_id=1', true );
					}
					else{
						alert("This is for Embellishment Issue Entry");
					}
				}
				else if(print_btn==747)
				{
					
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+report_title+'*'+show_item+'*'+basis+'*'+location_id+'&action=get_out_entry_print14&template_id=1', true );
				
				}
				else if(print_btn==241)
				{
					
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&action=get_pass_entry_print11&template_id=1', true );
					return;
				}
				else if(print_btn==427)
				{
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&action=get_out_entry_print20&template_id=1', true );
					return;
				}
				else if(print_btn==28)
				{
					
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&action=get_out_entry_print21&template_id=1', true );
					return;
				}
				else if(print_btn==437)
				{
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}
					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&action=get_out_entry_print22&template_id=1', true );
					return;
				}
				else if(print_btn==719)
				{
					var show_item='';
					var r=confirm("Press  \"Cancel\"  to hide  Rate &  Amount\nPress  \"OK\"  to Show Rate &  Amount");
					if (r==true)
					{
						show_item="1";
					}
					else
					{
						show_item="0";
					}		
					window.open("../../inventory/requires/get_pass_entry_controller.php?data=" + company_id+'*'+sys_number+'*'+location_id+'*'+report_title+'*'+show_item+'*'+basis+'*'+returnable+'&action=get_out_entry_print16&template_id=1', true );
					return;
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
                            <th>Basis</th>
                            <th>Gate Pass ID</th>
                            <th >Job No</th>
                            <th >Style Number</th>
                            <th colspan="2">Date Range</th>
                            <th>Approval Type</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container*report_container2','','','')" class="formbutton" style="width:100px" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> 
                                <?
                                    echo create_drop_down( "cbo_company_id", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/gate_pass_approval_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );" );
                                ?>
                            </td>
                            <td id="buyer_td_id"> 
                                <?
                                   echo create_drop_down( "cbo_buyer_id", 152, $blank_array,"", 1, "-- All Buyer --", 0, "" );
                                ?>
                            </td>  
							 <td> 
								<?= create_drop_down("cbo_basis", 150,$get_pass_basis,"", 1,"-- All --","0","","",""); ?>
							 </td>                           
							<td>
                            	<input type="text" name="txt_gate_pass_id" id="txt_gate_pass_id" value="" class="text_boxes" style="width:100px" /> 
                            </td>  
							<td>
                            	<input type="text" name="txt_job_no" id="txt_job_no" value="" class="text_boxes" style="width:100px" /> 
                            </td>  
                            <td>
                            	<input type="text" name="txt_style_number" id="txt_style_number" value="" class="text_boxes" style="width:100px" /> 
                            </td>     
                            <td> 
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:80px" /> 
                            </td> 
							<td> 
                            <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:80px" /> 
                            </td>                                 
                            <td> 
                                <?
                                    $search_by_arr=array(0=>"Pending",1=>"Partial Approved",2=>"Full Approved");
                                    echo create_drop_down("cbo_approval_type", 100, $search_by_arr, "", 0, "", "", "", 0);
                                ?>
                            </td>
                            <td>
								<input type="button" value="Show" name="show" id="show" class="formbutton" style="width:100px" onClick="fn_report_generated()"/>
								<input type="hidden" id="txt_selected_id" name="txt_selected_id" />
							</td>
                        </tr>
                    </tbody>
                    <tr>
                        <td colspan="9" align="center"><?=load_month_buttons(1); ?></td>
                    </tr>
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
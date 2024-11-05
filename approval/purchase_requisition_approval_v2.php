<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create PI Approval			
Functionality	:				
JS Functions	:
Created by		:	Md: Rakibul Islam
Creation date 	: 	01-09-2021
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
//echo load_html_head_contents("PI Approval", "../", 1, 1,'','','');
echo load_html_head_contents("Purchase Requisition Approval", "../", 1, 1,'','','');

$permitted_item_category=return_field_value("item_cate_id","user_passwd","id='".$_SESSION['logic_erp']['user_id']."'");

$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "page_id=$menu_id and is_deleted=0" );

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';
    
    // show button function 
	function fn_report_generated()
	{
		var approval_setup =<? echo $approval_setup; ?>;
		
		if(approval_setup!=1)
		{
			alert("Electronic Approval Setting First.");	
			return;
		}
		
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_store_id*cbo_req_year*cbo_item_category_id*txt_req_no*txt_date_from*txt_date_to*cbo_approval_type*txt_alter_user_id',"../");
		freeze_window(3);
		http.open("POST","requires/purchase_requisition_approval_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
    
	// show button response function 
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			//var response=http.responseText.split("####");
			$('#report_container').html(http.responseText);
			//setFilterGrid("tbl_list_search",-1);
			show_msg('18');
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
	
 
    // Approve Button function 
	function submit_approved(total_tr,type,approved_user_id)
	{ 
		var target_id_arr = Array(); var txt_appv_cause_arr = Array();
        
		// Confirm Message  ***************************************************************************************
		if($('#cbo_approval_type').val()==1)
		{
            if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to UnApproved All");
				if(first_confirmation==false)
				{
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnApproved All");
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
				first_confirmation=confirm("Are You Want to Approved All");
				if(first_confirmation==false)
				{
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to Approved All");
					if(second_confirmation==false)
					{
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
                target_id = parseInt($('#target_id_'+i).val());                            
                txt_appv_cause = $('#txt_appv_cause_'+i).val();   
				
				if(type==5 && txt_appv_cause==''){
					
					alert('Without Refusing Cause Deny Not Allowed');
					release_freezing();	
					return;
				}
                
                if(target_id>0)
				{
					target_id_arr.push(target_id);
					txt_appv_cause_arr.push(txt_appv_cause);
                }
                
			}
		}
		var target_ids = target_id_arr.join(',');
		var txt_appv_causes = txt_appv_cause_arr.join('**');


		
		if(confirm('Mail Send! Sure?')==true){
			var company_id=$('#cbo_company_name').val();
			return_global_ajax_value(target_ids+'__'+approved_user_id+'__'+company_id+'__'+type, 'send_requisition_app_mail', '', 'requires/purchase_requisition_approval_controller_v2');
			//return;
		}
		var alterUserID = $('#txt_alter_user_id').val();
		
		var data="action=approve&operation="+operation+'&approval_type='+type+'&target_ids='+target_ids+'&txt_appv_causes='+txt_appv_causes+'&txt_alter_user_id='+alterUserID+get_submitted_data_string('cbo_company_name',"../");
	
		freeze_window(operation);
		
		http.open("POST","requires/purchase_requisition_approval_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=submit_approved_response;
	}	
	
    // Approve Button responds function 
	function submit_approved_response()
	{
		if(http.readyState == 4) 
		{ 
			var reponse=http.responseText.split('**');
			fnc_remove_tr();
			//show_msg(reponse[1]);
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
	
	
	
	function change_user()
	{
		
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		
		
		var title = 'CS Approval Accessories Info';	
		var page_link = 'requires/purchase_requisition_approval_controller_v2.php?action=user_popup&company_id='+$("#cbo_company_name").val();
		  
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
	
	
	function print_report(company_name,id,Purchase_Requisition,is_approved,remarks,action_type,show_item,cbo_template_id)
	{
		var report_title='';
		var approved_id='';

		var data='';
		var action='';
		if(action_type==3) {
			action="purchase_requisition_print_2";
			data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'***'+'../../';
		}
		else if(action_type==5) {
			action="purchase_requisition_print_3";
			data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'****'+is_approved+'../../';
		}
		else if(action_type==6) {
			action="purchase_requisition_print_4";
			data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'***'+'../../';
		}
		else if(action_type==7) {
			action="purchase_requisition_print_5";
			data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'***'+'../../';
		}
		else if(action_type==8) {
			action="purchase_requisition_print_8";
			data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'***'+'../../';
		}
		else if(action_type==9) {
			action="purchase_requisition_print_9";
			data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'***'+'../../';
		}		
		else if(action_type==10) 
		{			
			show_item="";
			r=confirm("Press  \"Cancel\"  to hide  Last Rate & Req Value \nPress  \"OK\"  to Show Last Rate & Req Value");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
			action="purchase_requisition_print_10";
			data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'*'+show_item+'**'+'../../';

		}
		else if(action_type==11) {
			data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'***'+'../../';
			action="purchase_requisition_print_11";
		}
		else if(action_type==12) {
			data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'***'+'../../';
			action="purchase_requisition_print_4_akh";
		}
		else if(action_type==11) {
			data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'***'+'../../';
			action="purchase_requisition_print_13";
		}
		else if(action_type==14) {
			data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'***'+'../../';
			action="purchase_requisition_print_14";
		}
		else if(action_type==15) {
			data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'***'+'../../';
			action="purchase_requisition_print_15";
		}
		else if(action_type==16) {
			data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'***'+'../../';
			action="purchase_requisition_print_16";
		}
		else if(action_type==17) {
			data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'***'+'../../';
			action="purchase_requisition_category_wise_print";
		}
		else if(action_type==18) {
			data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'***'+'../../';
			action="purchase_requisition_print_18";
		}
		else if(action_type==19) {
			data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'*'+show_item+'*'+cbo_template_id+'***'+'../../';
			action="purchase_requisition_print_19";
		}
		else if(action_type==19) {
			data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'***'+'../../';
			action="purchase_requisition_print_20";
		}
		else if(action_type==21) {
			data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'***'+'../../';
			action="purchase_requisition_print_21";
		}
		else if(action_type==22) {
			data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'***'+'../../';
			action="purchase_requisition_print_22";
		}
		else if(action_type==23) {
			data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'***'+'../../';
			action="purchase_requisition_print_23";
		}
		else if(action_type==24) {
			data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'***'+'../../';
			action="purchase_requisition_print_24";
		}
		else if(action_type==25) {
			data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'****'+'../../';
			action="purchase_requisition_print_25";
		}
		else if(action_type==26) {
			data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+remarks+'*'+action_type+'****'+'../../';
			action="purchase_requisition_print_27";
		}
		else // action_type 1,2,4
		{
			data=company_name+'*'+id+'*'+Purchase_Requisition+'*'+approved_id+'*'+action_type+'****'+'../../';
			action="purchase_requisition_print";
		}

		freeze_window(5);
		http.open("POST","../inventory/requires/purchase_requisition_controller.php",true);
			
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4) 
		    {
		    	//alert(action+"**"+action_type);
				window.open("../inventory/requires/purchase_requisition_controller.php?action="+action+'&data='+data, "_blank");
				release_freezing();
		   }	
		}
	}

	function openmypage(req_id)
	{
		
		var cbo_company_name=document.getElementById('cbo_company_name').value;
		
		
		var data=cbo_company_name+"_"+req_id;
		
		var title = 'Un Approval Request';	
		var page_link = 'requires/purchase_requisition_approval_controller_v2.php?data='+data+'&action=unapprove_request_action';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
			var unappv_request=this.contentDoc.getElementById("hidden_appv_cause");
			
			$('#txt_un_appv_request').val(unappv_request.value);
		}
	}


	
</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",'');?>
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:1150px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:1150px;">
                 <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>                        	
                            <tr>
                            	<td colspan="9" align="right"> 
                                <?php
									$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
									if( $user_lavel==2)
									{
									?>
                                    Alter User: 
                                    <input id="txt_alter_user" name="txt_alter_user" type="text" onDblClick="change_user();" class="text_boxes" style="width:150px" placeholder="Browse" readonly >
                                    <?php } ?>
                                    <input id="txt_alter_user_id" name="txt_alter_user_id" type="hidden">
                                </td>
                            </tr>
                            <tr>
                                <th class="must_entry_caption">Company Name</th>
                                <th>Store</th>
                                <th>Item Category</th>
                                <th>Requisition Year</th>
                                <th>Requisition No</th>
                                <th colspan="2">Date Range</th>
                                <th>Approval Type.</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/purchase_requisition_approval_controller_v2', this.value, 'load_drop_down_store', 'store_td' )" );
                                    ?>
                                </td>
                                <td id="store_td">
                                    <? 
                                        echo create_drop_down( "cbo_store_id", 130, array(),"", 1, "-- All --",0,"",0,0,"","","");
                                    ?>
                                </td>
                                <td id="category_id">
                                    <? 
									 echo create_drop_down( "cbo_item_category_id", 130, array(),"", 1, "-- All --","","",0,0,"","","");
                                        // echo create_drop_down( "cbo_item_category_id", 130, $item_category,"", 1, "-- All Category --", $selected,"",0,$permitted_item_category,"","","1,2,3,12,13,14");
                                    ?>
                                </td>
                                <td>
									<? echo create_drop_down( "cbo_req_year", 110, $year, "", 1, "-- Select --", date("Y", time()), "" ); ?>
								</td>
                                <td>
									<input name="txt_req_no" id="txt_req_no" style="width:80px" class="text_boxes" placeholder="Write">
								</td>
                                <td>
									<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date">
								</td>
								<td>
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date">
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
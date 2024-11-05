<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Work Order for AOP Approval				
Functionality	:	
JS Functions	:
Created by		: Shajib Jaman
Creation date 	: 	03-07-2018
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
echo load_html_head_contents("GRN Approval", "../", 1, 1,'','','');

$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "page_id=$menu_id and is_deleted=0" );
 
$is_app_need_arr=return_library_array( "select a.COMPANY_ID,b.APPROVAL_NEED from APPROVAL_SETUP_MST a,APPROVAL_SETUP_DTLS b where a.id=b.mst_id and b.PAGE_ID=52 order by a.id", "COMPANY_ID", "APPROVAL_NEED"  );

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';
    
    // show button function 
	let fn_report_generated=()=>{
		var approval_setup =<? echo $approval_setup; ?>;
		var is_app_need_arr =<? echo json_encode($is_app_need_arr); ?>;

		if(is_app_need_arr[$('#cbo_company_name').val()*1]!=1){alert('Approval Necessity Setup Need This Approval');return;}
		else if(approval_setup!=1){alert('Please Electronic Approval Setup');return;}

		freeze_window(3);
		
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			release_freezing();return;
		}
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_item_category*txt_grn_no*cbo_store_name*cbo_approval_type*txt_grn_date_from*txt_grn_date_to*txt_alter_user_id',"../");
		//alert(data);
		http.open("POST","requires/grn_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =()=>{
			if(http.readyState == 4) 
			{	show_msg('3');
				$('#report_container').html(http.responseText);
				var tableFilters = { col_0: "none" }
				setFilterGrid("tbl_list_search",-1,tableFilters);
				release_freezing();
			}
		}
	}
    

 
	let submit_approved=(total_tr,type)=>{ 
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
			}
		}
		// Confirm Message End *******************************************************************

		var target_id_arr=Array();
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{								
                target_id = parseInt($('#target_id_'+i).val());                            
				if(target_id>0)
				{
					target_id_arr.push(target_id);
                }
			}
		}
		let target_ids=target_id_arr.join(',');


		var data="action=approve&operation="+operation+'&approval_type='+type+'&target_ids='+target_ids+get_submitted_data_string('cbo_company_name*cbo_item_category*txt_grn_no*cbo_store_name*cbo_approval_type*txt_grn_date_from*txt_grn_date_to*txt_alter_user_id',"../");
	
		http.open("POST","requires/grn_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=()=>{
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
	}	
	
 
	
	let change_user=()=>{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		var title = 'PI Approval New';	
		var page_link = 'requires/grn_approval_controller.php?action=user_popup'+get_submitted_data_string('cbo_company_name',"../");
		  
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


	let fnc_remove_tr=()=>{
		var tot_row=$('#tbl_list_search tbody tr').length-1;
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
		var page_link='requires/grn_approval_controller.php?action='+action+'&id='+id;
		if(action=='yarn_grn_receive_image') var title='Image View'; else var title='File View';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','');
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


	
	
	
		
	function fn_generate_print(update_id,cbo_company_id){
			
		var data = "action=generate_print&operation=" + operation+"&update_id=" + update_id+"&cbo_company_id=" + cbo_company_id;
			
			freeze_window(operation);
			http.open("POST", "requires/grn_approval_controller.php", true);
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
	<? echo load_freeze_divs ("../",'');?>
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:1000px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:1000px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                    <thead>                        	
					<tr> 
                            <th colspan="4"></th>
                            <th colspan="4">
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
                            </th>
                        </tr>
                        <tr>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Item Category</th>
                            <th>GRN No</th>
                            <th>Store Name</th>
                            <th colspan="2">Transaction Date</th>
                            <th>Approval Type</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> 
                                <?
                                    echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/grn_approval_controller',this.value, 'load_drop_down_buyer', 'store_td' );" );
                                ?>
                            </td>
                            <td > 
                            <?  
								   $erosion_type=array(1=>"Yarn"); 
                                   echo create_drop_down( "cbo_item_category", 152, $erosion_type,"", 1, "-- Select --", 0, "" );
                                ?>
                            </td>                                
                            <td>
                            	<input type="text" name="txt_grn_no" id="txt_grn_no" value="" class="text_boxes" style="width:100px" /> 
                            </td>  
							<td  > 
                                <? 
                                
                                $sql_store = "select id,store_name from lib_store_location  group by id,store_name order by store_name";
	                       echo create_drop_down("cbo_store_name", 142, $sql_store, "id,store_name", 1, "--Select store--", 0, "");
								 
                                //  echo create_drop_down( "cbo_store_name", 142, $blank_array,"", 1, "-- Select Store --", 0, "" );
                                 ?>
                                
                            </td>     
                            <td> 
                            <input type="text" name="txt_grn_date_from" id="txt_grn_date_from" value="" class="datepicker" style="width:80px" /> 
                            </td> 
							<td> 
                            <input type="text" name="txt_grn_date_to" id="txt_grn_date_to" value="" class="datepicker" style="width:80px" /> 
                            </td>                                 
                            <td> 
                                <?
                                    echo create_drop_down( "cbo_approval_type", 130, $approval_type_arr,"", 0, "", $selected,"","", "" );
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
    <div id="data_panel2" align="center"></div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script> $('#cbo_approval_type').val(0); </script>
</html>
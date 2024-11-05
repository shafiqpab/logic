<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Buyer Inquiry for Woven Textile Acknowledge
Functionality	:	
JS Functions	:
Created by		:	Md. Mamun Ahmed Sagor
Creation date 	: 	28-08-2023
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
//-------------------------------------------------------------------------------------------
echo load_html_head_contents("Buyer Inquiry for Woven Textile Acknowledge", "../", 1, 1,'','','');

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';

	function fn_report_generated()
	{		
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*txt_system_id*txt_date_from*txt_date_to*cbo_approval_type*cbo_buyer_name',"../");
		// alert(data);return;
		freeze_window(3);
		http.open("POST","requires/buyer_inquery_for_woven_textile_acknowledge_controller.php",true);
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
			
			setFilterGrid("tbl_list_search",-1);
				
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
		
	function submit_approved(total_tr,type)
	{
		// alert(type);return;
		var workOrder_nos = "";  var workOrder_ids = ""; var approval_ids = ""; 
		freeze_window(0);
		// Confirm Message  ******************************************************
		if($('#cbo_approval_type').val()==1)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to UnApproved All System No");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnApproved All System No");
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
				first_confirmation=confirm("Are You Want to Approved All System No");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to Approved All System No");
					if(second_confirmation==false)
					{
						release_freezing();
						return;					
					}
				}
			}
		}
		// Confirm Message End *******************************************************************
		
		var data_all="";
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				work_order_id = $('#work_order_id_'+i).val();
				if(workOrder_ids=="") workOrder_ids= work_order_id; else workOrder_ids +=','+work_order_id;
				
     			approval_id = parseInt($('#approval_id_'+i).val());
				if(approval_id>0)
				{
					if(approval_ids=="") approval_ids= approval_id; else approval_ids +=','+approval_id;
				}
				var hiddDtlsId = $('#hiddDtlsId_'+i).val();

				data_all=data_all+get_submitted_data_string('fabConstructionId_'+hiddDtlsId+'*fabConstruction_'+hiddDtlsId+'*yarnCountDeterminationId_'+hiddDtlsId+'*txtconstruction_'+hiddDtlsId+'*hiddDtlsId_'+i,"../");

			}
		}

		if(workOrder_ids=="")
		{
			alert("Please Select At Least One Requisition No");
			release_freezing();
			return;
		}
		 
		var data="action=approve&operation="+operation+'&approval_type='+type+'&workOrder_ids='+workOrder_ids+'&approval_ids='+approval_ids+'&total_row='+total_tr+get_submitted_data_string('cbo_company_name',"../")+data_all;
		//alert(data);return;
		
		http.open("POST","requires/buyer_inquery_for_woven_textile_acknowledge_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_purchase_requisition_approval_Reply_info;
	}	
	
	function fnc_purchase_requisition_approval_Reply_info()
	{		
		if(http.readyState == 4) 
		{ 
			// alert(http.responseText);
			var reponse=trim(http.responseText).split('**');
			//alert(http.responseText);release_freezing();return;	
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
	}	
	
	 

	function openImgFile(id,action)
	{
		var page_link='requires/buyer_inquery_for_woven_textile_acknowledge_controller.php?action='+action+'&id='+id;
		if(action=='img') var title='Image View'; else var title='File View';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','');
	}

	 
	 
	function generate_report(company_name,wo_no,wo_id,title)
	{
		print_report( company_name+'**'+wo_no+'**'+wo_id+'**'+title, "inquery_entry_print", "../order/woven_gmts/requires/sample_requisition_woven_textile_controller" );
			return;
			
	}	

	function openmypage_fabric_cons(fab_construction_id,dtls_id)
	{
		
			
		
		
		var page_link="requires/buyer_inquery_for_woven_textile_acknowledge_controller.php?action=fabric_construction_popup&fab_construction_id="+fab_construction_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Material Construction Popup", 'width=1000px,height=350px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			
			var hidfabconspid=this.contentDoc.getElementById("hidfabconspid").value;
			var hidfabconsname=this.contentDoc.getElementById("hidfabconsname").value;
			var fab_construction=this.contentDoc.getElementById("fab_construction").value;
			
			console.log(hidfabconsname)
			
				$('#fabConstructionId_'+dtls_id).val(hidfabconspid);
				$('#txtconstruction_'+dtls_id).val(hidfabconsname);
				$('#fabConstruction_'+dtls_id).val(fab_construction);
				
		}
	}
	 

</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",''); ?>
		<form name="requisitionApproval_1" id="requisitionApproval_1"> 
        <h3 style="width:900px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
        <div id="content_search_panel">      
            <fieldset style="width:900px;">
                <table class="rpt_table" width="900" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                    <thead>
                                    
                        <tr>
                            <th class="must_entry_caption">Company Name</th>
							<th>Buyer Name</th>
							<th>Requisition No</th>
                            <th colspan="2" >Date Range</th>                         
                            <th>Approval Type</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                        </tr>
                    </thead>
                	<tbody>
                    	<tr class="general">
							<td> 
								<?
								echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected,"load_drop_down( 'requires/buyer_inquery_for_woven_textile_acknowledge_controller',this.value, 'load_drop_down_buyer', 'buyer_td');");
								?>
							</td>
							<td id="buyer_td">
                        	<? echo create_drop_down( "cbo_buyer_name", 140, "select id,buyer_name from lib_buyer  where status_active =1 and is_deleted=0  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ,0); ?>
                        		
                       		 </td>
							<td><input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes_numeric" style="width:120px"/>
							
							</td>
							<td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px;" placeholder="From Date"/></td>					
							<td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px;" placeholder="To Date" /></td>
							
							<td> 


								<?
								$approval_type_arr = array(0=>'Un-Acknowledge',1=>'Acknowledge');
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
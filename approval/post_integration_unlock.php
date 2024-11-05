<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Pre Costing Approval
					
Functionality	:	
				

JS Functions	:

Created by		:	Ashraful
Creation date 	: 	14-02-2018
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
echo load_html_head_contents("Fabric Booking Approval", "../", 1, 1,'','','');

$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "page_id=$menu_id and is_deleted=0" );


$integration_point		= array(1=>"Ex-factory", 2=>"Import Doc Acceptance", 3=>"Pre-Export Finance", 4=>"Export Invoice", 5=>"Export Doc Negotiated", 6=>"Export Proceeds Realization", 7=>"Import Payment", 8=>"Material Receive", 9=>"Receive Return", 10=>"Material Issue", 11=>"Issue Return", 12=>"Item Transfer Issue", 13=>"Depreciation", 14=>"Assets Disposal", 15=>"Subcontract Bill Issue", 16=>"Subcontract Payment Adjustment", 17=>"Subcontract Bill Outside", 18=>"Commission Bill Issue", 19=>"Commission Receive", 20=>"Monthly Salary Bill", 21=>"Trims Bill Issue", 22=>"Trims Payment Receive", 28=>"Fixed Assets Acquisition", 33=>"LC Opening Charge", 34=>"Garments Production", 35=>"Fabric Service Receive", 36=>"Garments Service Receive", 37=>"LC Amendments Charge", 38=>"LC Acceptance Charge", 39=>"Receive Against Cash PI", 40=>"Order to Order Items Transfer", 41=>"Scrap Sale", 42=>"Insurance Expenses", 43=>"Consignment Clearing Expenses", 44=>"Adjustment Expenses", 45=>"Transport cost", 46=>"Inspection Expenses", 47=>"Waste Cotton Bill", 48=>"Item Transfer Receive", 49=>"Short/Gain Weight Claim", 50=>"Printing Bill Issue", 51=>"AOP Bill Issue", 52=>"Wash Bill Issue", 53=>"Embroidery Bill Issue"); //asort($integration_point);

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';

	function fn_report_generated()
	{
		freeze_window(3);
		if (form_validation('cbo_company_name*cbo_integration_point*integration_point_search','Comapny Name*Integration Point*Integration Point')==false)
		{
			release_freezing();
			return;
		}
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_integration_point*integration_point_search',"../");
		
		http.open("POST","requires/post_integration_unlock_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			//alert(response[0]);
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
	
	
	function submit_approved(total_tr)
	{ 
		//var operation=4; 
		var approved_data = ""; 
		freeze_window(0);
		// confirm message  *********************************************************************************************************
		/*if($('#cbo_approval_type').val()==1)
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
		}*/
		// confirm message finish ***************************************************************************************************

		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				var	mst_id = $('#mstId_'+i).val();
				var unlock_id = $('#unlockId_'+i).val();
				var	user_id = $('#cbo_requested_user_'+i).val();
				var remarks = $('#txt_remarks_'+i).val();
				if(approved_data=="") approved_data= mst_id+"_"+unlock_id+"_"+user_id+"_"+remarks;
				else approved_data +=','+mst_id+"_"+unlock_id+"_"+user_id+"_"+remarks;
			}
		}
		
		if(approved_data=="")
		{
			alert("Please Select At Least One Row");
			release_freezing();	
			return;
		}
		
		var data="action=approve&operation="+operation+'&approved_data='+approved_data+get_submitted_data_string('cbo_company_name*cbo_integration_point',"../");
	 //alert(data);return;
		
		http.open("POST","requires/post_integration_unlock_controller.php",true);
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
			if(reponse[0]==19)
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
	
	function generate_worder_report(type,job_no,company_id,buyer_id,style_ref,txt_costing_date)
	{
		var zero_val='';
		var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		if (r==true)
		{
			zero_val="1";
		}
		else
		{
			zero_val="0";
		} 
		var data="action="+type+
					'&zero_value='+zero_val+
					'&txt_job_no='+"'"+job_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&cbo_buyer_name='+"'"+buyer_id+"'"+
					'&txt_style_ref='+"'"+style_ref+"'"+
					'&txt_costing_date='+"'"+txt_costing_date+"'";
			        http.open("POST","../order/woven_order/requires/pre_cost_entry_controller_v2.php",true);
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = generate_fabric_report_reponse;
	}
		
	function generate_fabric_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
			d.close();
		}
	}
	
	
function fan_integration_point(intID)
{
	var voucherArray= {
		1:"Delivery ID",
		2:"Invoice Number",
		3:"Loan No",
		4:"Invoice No",
		5:"Bill No",
		6:"Bill No",
		7:"Bank Ref. No",
		8:"MRR No",
		9:"System ID",
		10:"Issue No",
		11:"System ID",
		12:"System ID",
		13:"",
		14:"",
		15:"Bill No",
		16:"Bill No",
		17:"Bill No",
		18:"",
		19:"",
		20:"",
		21:"",
		22:"",
		28:"",
		33:"LC Number",
		34:"",
		35:"Work Order",
		36:"Work Order"
	};
	
	var placeHolderArray= {
		1:"",
		2:"",
		3:"",
		4:"",
		5:"",
		6:"",
		7:"",
		8:"",
		9:"",
		10:"FAL-ABC-11-00001",
		11:"",
		12:"",
		13:"",
		14:"",
		15:"",
		16:"",
		17:"",
		18:"",
		19:"",
		20:"",
		21:"",
		22:"",
		28:"",
		33:"",
		34:"",
		35:"",
		36:""
	};
	
  	$("#interName").addClass("must_entry_caption");
	$("#interName").html(voucherArray[intID]);
	
	$("#integration_point_search").val('');
	$("#integration_point_search").attr("placeholder",placeHolderArray[intID]);
	
}


</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",''); ?>
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:600px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:600px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <tr>
                                <th class="must_entry_caption">Company Name</th>
                                <th>Integration Point</th>
                                <th id="interName" class="">Point Number</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                        	</tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                                    ?>
                                </td>
                                <td> 
									<?
										echo create_drop_down( "cbo_integration_point", 160,$integration_point,"", 1, "-- Select --", $selected, "fan_integration_point(this.value);","","15","","",""); 
				                 ?> 
                                   
                                </td>
                              	 <td width="" id="">
								 <input type="text" name="integration_point_search" id="integration_point_search" value="" style="width:160px" class="text_boxes" />
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
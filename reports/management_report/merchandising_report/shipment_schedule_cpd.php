 <?
/*-------------------------------------------- Comments
Version                  :   V1
Purpose			         : 	This form will create Shipment Schedule for CPD
Functionality	         :
JS Functions	         :
Created by		         :	Md Zakaria
Creation date 	         :	20-11-2021
Requirment Client        :
Requirment By            :
Requirment type          :
Requirment               :
Affected page            :
Affected Code            :
DB Script                :
Updated by 		         : 	
Update date		         : 	
QC Performed BY	         :
QC Date			         :
Comments		         : 
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$user_id=$_SESSION['logic_erp']['user_id'];
echo load_html_head_contents("Data entry followup report","../../../", 1, 1, $unicode,1,1);
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";
	function fn_on_change()
	{
		var cbo_company_name = $("#cbo_company_name").val();
		load_drop_down( 'requires/shipment_schedule_cpd_controller', cbo_company_name, 'load_drop_down_buyer', 'buyer_td' );
		set_multiselect('cbo_buyer_name','0','0','','0','fn_on_change2()');
	}
	function fn_on_change_leader()
	{
		var team_id = $("#cbo_team_name").val();
		load_drop_down( 'requires/shipment_schedule_cpd_controller', team_id, 'load_drop_down_team_leader', 'team_lead_td' );		
		set_multiselect('cbo_team_leader','0','0','','0','fn_on_change_dealing()');
	}
	function fn_on_change_dealing()
	{		
		var team_id = $("#cbo_team_leader").val();
		load_drop_down( 'requires/shipment_schedule_cpd_controller', team_id, 'load_drop_down_dealing_merchant', 'dealing_merchant_td' );		
		set_multiselect('cbo_dealing_merchant','0','0','','0');
	}
		function generate_report_main(rpt_type)
	{
		var cbo_company=document.getElementById('cbo_company_name').value;
		var date_from=document.getElementById('txt_date_from').value;
		var txt_job_no=document.getElementById('txt_job_no').value;
		var date_to=document.getElementById('txt_date_to').value;

		if(txt_job_no=="" && date_from ==""){			
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*form date*To date')==false)
				{
					return;
				}		
		}			
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_working_company*cbo_buyer_name*cbo_team_name*cbo_team_leader*cbo_dealing_merchant*txt_style_ref*cbo_dept*cbo_item*txt_date_from*txt_date_to*txt_job_no*txt_job_no_id',"../../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;
		freeze_window(3);
		http.open("POST","requires/shipment_schedule_cpd_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}
	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("****");
			$("#report_container2").html(reponse[0]);
			
			 document.getElementById('report_container').innerHTML=report_convert_button('../../../');
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body",-1);
			show_msg('3');
			release_freezing();

		}
	}
	function new_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
	}


	function generate_popup(job_no,booking_no,po_id,item_id,action)
	{
		//alert(job_no);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/shipment_schedule_cpd_controller.php?action='+action+'&booking_id='+booking_no+'&job_id='+job_no+'&po_id='+po_id+'&item_id='+item_id, 'Trims Rcv POp Up', 'width=500,height=400px,center=1,resize=0,scrolling=0','../../');
	}



	function generate_worder_report(txt_booking_no,cbo_company_name,txt_order_no_id,cbo_fabric_natu,cbo_fabric_source,txt_job_no,id_approved_id,type,report_type)
	{
		//var report_title='Budget Wise Fabric Booking';
		var data="action="+type+
		'&txt_booking_no='+"'"+txt_booking_no+"'"+
		'&cbo_company_name='+"'"+cbo_company_name+"'"+
		'&txt_order_no_id='+"'"+txt_order_no_id+"'"+
		'&cbo_fabric_natu='+"'"+cbo_fabric_natu+"'"+
		'&cbo_fabric_source='+"'"+cbo_fabric_source+"'"+
		'&id_approved_id='+"'"+id_approved_id+"'"+
		'&report_title='+"Budget Wise Fabric Booking"+
		'&txt_job_no='+"'"+txt_job_no+"'"+
		'&path=../../../';
		
		
	
		if(report_type=="fabric_booking"){
		http.open("POST","../../../order/woven_order/requires/partial_fabric_booking_controller.php",true);		
		}else{
			http.open("POST","../../../order/woven_order/requires/trims_booking_multi_job_controllerurmi.php",true);		
		}
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4) 
		    {
				var w = window.open("Surprise", "_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				d.close();
		   }
			
		}
	}

	
function openmypage_job()
{
	// alert('ok');
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}

	var companyID = $("#cbo_company_name").val();
	var buyer_name = $("#cbo_buyer_name").val();
	var cbo_season_year = $("#cbo_season_year").val();

	var page_link='requires/shipment_schedule_cpd_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year='+cbo_season_year;

	var title='Job No Search';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=770px,height=370px,center=1,resize=1,scrolling=0','../../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var data_arr=this.contentDoc.getElementById("hide_job_no").value.split("_");
		$('#txt_job_no').val(data_arr[1]);
		$('#txt_job_no_id').val(data_arr[0]);
	}
}


	function precost_bom_pop(type,data)
	{
			var reponse=trim(data).split("_");
			var  company=reponse[0];
			var  buyer=reponse[1];
			var  style_ref=reponse[2];
			var  job_no=reponse[3];
			var  job_id=reponse[4];
			var  quatation_id=reponse[5];
			var  costing_date=reponse[6];
			var  po_id=reponse[7];
			var  costing_per=reponse[8];
	
		// alert(reponse);
			// freeze_window(3);
			if(type=="summary" || type=="budget3_details")
			{
				if(type=='summary')
				{
					var rpt_type=3;var comments_head=0;
				}
				else if(type=='budget3_details')
				{
					var rpt_type=4;var comments_head=1;
				}

				var report_title="Budget/Cost Sheet";
				//	var comments_head=0;
				var cbo_company_name=$('#cbo_company_name').val();
				var cbo_buyer_name=$('#cbo_buyer_name').val();
				var txt_style_ref=$('#txt_style_ref').val();
				var txt_style_ref_id=$('#hidd_job_id').val();
				var txt_quotation_id=$('#txt_quotation_id').val();
				var sign=0;
		
				var txt_order=""; var txt_order_id="";  var txt_season_id=""; var txt_season=""; var txt_file_no="";
				var data="action=report_generate&reporttype="+rpt_type+
				'&cbo_company_name='+"'"+cbo_company_name+"'"+
				'&cbo_buyer_name='+"'"+cbo_buyer_name+"'"+
				'&txt_style_ref='+"'"+txt_style_ref+"'"+
				'&txt_style_ref_id='+"'"+txt_style_ref_id+"'"+
				'&txt_order='+"'"+txt_order+"'"+
				'&txt_order_id='+"'"+txt_order_id+"'"+
				'&txt_season='+"'"+txt_season+"'"+

				'&txt_season_id='+"'"+txt_season_id+"'"+
				'&txt_file_no='+"'"+txt_file_no+"'"+
				'&txt_quotation_id='+"'"+txt_quotation_id+"'"+
				'&txt_hidden_quot_id='+"'"+txt_quotation_id+"'"+
				'&comments_head='+"'"+comments_head+"'"+
				'&sign='+"'"+sign+"'"+
				'&report_title='+"'"+report_title+"'"+
				'&path=../../../';

				http.open("POST","requires/cost_breakup_report2_controller.php",true);

				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = function()
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
			}
			else
			{
				var rate_amt=2; var zero_val='';
				if(type!='mo_sheet' && type != 'budgetsheet' && type != 'materialSheet')
				{
					var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
				}
				if(type=='materialSheet')
				{
					var r=confirm("Press \"OK\" to show Qty  Excluding Allowance.\nPress \"Cancel\" to show Qty Including Allowance.");
				}
				var excess_per_val="";
				if(type=='mo_sheet')
				{
					excess_per_val = prompt("Please enter your Excess %", "0");
					if(excess_per_val==null) excess_per_val=0;else excess_per_val=excess_per_val;
				}
				if(type == 'budgetsheet')
				{
					var r=confirm("Press  \"OK\" to Show Budget, \nPress  \"Cancel\"  to Show Management Budget");
				}

				if (r==true) zero_val="1"; else zero_val="0";
				var print_option_id="''";
				//eval(get_submitted_variables('txt_job_no*cbo_company_name*cbo_buyer_name*txt_style_ref*txt_costing_date'));
			
				var data="action="+type+"&zero_value="+zero_val+"&rate_amt="+rate_amt+"&excess_per_val="+excess_per_val+"&txt_job_no='"+job_no+"'&cbo_company_name="+company+"&cbo_buyer_name='"+buyer+"'&txt_style_ref='"+style_ref+"'&txt_costing_date='"+costing_date+"'&txt_po_breack_down_id="+po_id+"&cbo_costing_per="+costing_per+"&print_option_id="+print_option_id+"&path=../../../";
				http.open("POST","../../../order/woven_order/requires/pre_cost_entry_controller_v3.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = function()
				{
					if(http.readyState == 4) 
					{
						var w = window.open("Surprise", "_blank");
						var d = w.document.open();
						d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
						d.close();
				  }
				
				 
				}
			}
			
	}
</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../");  ?>
    <form name="shipmentschedule_1" id="shipmentschedule_1" autocomplete="off" >
        <h3 style="width:1300px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" >
            <fieldset style="width:1220px;">
            <table class="rpt_table" width="1280" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="120" class="must_entry_caption">Company</th>
                        <th width="120">Working Company</th>
						<th width="120">Buyer</th>		
						<th width="120">Team Name</th>				
                        <th width="120">Team Leader</th>
                        <th width="120">Dealing Merchant</th>
						<th width="120">Job No</th>
						<th width="120">Style Ref</th>
						<th width="120">Prod. Dept</th>
						<th width="120">GMT Item</th>                      
                        <th width="110" colspan="2" class="must_entry_caption">Shipment Date</th>                      
                        <th><input type="reset" name="reset" id="reset" value="Reset" style="width:60px" class="formbutton" /></th>
                    </tr>
                </thead>
                <tr class="general">
                    <td><? echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "" );  ?></td>
                    <td><?=create_drop_down( "cbo_working_company", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" ); ?></td>
					<td id="buyer_td">
						<? echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select --", $selected, "" );?>	 
                    </td>	
				
					<td><? echo create_drop_down( "cbo_team_name", 120, "select id,team_name from lib_marketing_team  where status_active =1 and is_deleted=0  order by team_name","id,team_name", 1, "-Team Name-", $selected, "" ); ?></td>

                    <td id="team_lead_td"><? 
						echo create_drop_down( "cbo_team_leader", 120, "select id,team_leader_name from lib_marketing_team where project_type=1 and status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $teamId, "" );
					 ?></td> 
                    <td id="dealing_merchant_td"><? echo create_drop_down( "cbo_dealing_merchant", 120, "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0  order by id desc","id,team_member_name", 1, "-Team Member-", $selected, "" ); ?></td>
					<td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:120px"placeholder="Browse"  onDblClick="openmypage_job();" readonly  />
					<input type="hidden" name="txt_job_no_id" id="txt_job_no_id"/></td>
			    	</td>
					<td><input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:120px" placeholder="Write" /></td>
					<td><? echo create_drop_down( "cbo_dept", 120, $product_dept,"", 1, "-- Select Dept. --", $selected, "" ); ?>
					</td>
					<td><? echo create_drop_down( "cbo_item", 120, $garments_item,"", 1, "-- Select Item --", $selected, "" ); ?>
					</td>
					            
                               
                    <td><input name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:50px" placeholder="From Date"></td>
                    <td><input name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:50px" placeholder="To Date"></td>
                   
                   
                    <td><input type="button" name="search" id="search" value="show" onClick="generate_report_main(1)" style="width:60px" class="formbutton" /></td>
                </tr>
                <tr>
                    <td colspan="12" align="center">
						<? echo load_month_buttons(1); ?>
                    </td>                    
                </tr>
            </table>
        </fieldset>
        </div>
        </form>
           <div id="report_container" align="center"></div>
           <div id="report_container2"></div>
    </div>
</body>


<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

<script>
	set_multiselect('cbo_company_name','0','0','','0','fn_on_change()');
	set_multiselect('cbo_buyer_name','0','0','0','0');
	set_multiselect('cbo_team_leader','0','0','0','fn_on_change_dealing()');
	set_multiselect('cbo_team_name','0','0','','0','fn_on_change_leader()');	
	set_multiselect('cbo_dealing_merchant','0','0','','0');
	set_multiselect('cbo_dept','0','0','','0');
	set_multiselect('cbo_item','0','0','','0');
	
</script>
</html>
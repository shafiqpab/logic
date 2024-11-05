<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Unit Wise Purchase Requisition Follow-Up Report
				
Functionality	:	
JS Functions	:
Created by		:	Nayem
Creation date 	: 	6-12-2021
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
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Unit Wise Purchase Requisition Follow-Up Report","../../", 1, 1, $unicode,1,0); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function  generate_report(type)
	{
		if( form_validation('txt_date_from*txt_date_to','Date From*Date To')==false )
		{
			return;
		} 
			
		var cbo_company_name	= $("#cbo_company_name").val();
		var cbo_location_id 	= $("#cbo_location_id").val();
		var cbo_item_cat_id 	= $("#cbo_item_cat_id").val();
		var cbo_item_group_id 	= $("#cbo_item_group_id").val();
		var txt_req_no 			= $("#txt_req_no").val();
		var txt_wo_no 			= $("#txt_wo_no").val();
		var cbo_report_criteria	= $("#cbo_report_criteria").val();
		var cbo_store_id 		= $("#cbo_store_id").val();
		var txt_date_from 		= $("#txt_date_from").val();
		var txt_date_to 		= $("#txt_date_to").val();
				
		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_location_id="+cbo_location_id+"&cbo_item_cat_id="+cbo_item_cat_id+"&cbo_item_group_id="+cbo_item_group_id+"&txt_req_no="+txt_req_no+"&txt_wo_no="+txt_wo_no+"&cbo_report_criteria="+cbo_report_criteria+"&cbo_store_id="+cbo_store_id+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&type="+type;

		var data="action=generate_report"+dataString;
		freeze_window(3);
		http.open("POST","requires/unit_wise_purchase_requisition_follow_up_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse; 
		
	}

	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
		var reponse=trim(http.responseText).split("####");
		$("#report_container2").html(reponse[0]);  
		document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		setFilterGrid("table_body_1",-1);
		show_msg('3');
		release_freezing();
		}
	} 

	function openmypage_rcv(rcv_id,trans_id) 
	{
		page_link='requires/unit_wise_purchase_requisition_follow_up_report_controller.php?action=rcv_popup&rcv_id='+rcv_id+'&trans_id='+trans_id;
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Receive Qty Popup', 'width=300px, height=350px, center=1, resize=0, scrolling=0','');
		emailwindow.onclose=function(){}
	}
	function fn_wo_chk(type)
	{
		if(type==2)
		{
			$("#txt_wo_no").attr("disabled",true).val(null);
		}
		else
		{
			$("#txt_wo_no").attr("disabled",false)
		}
	}
</script>
</head>

<body onLoad="set_hotkey()">
 <form name="item_receive_issue_1" id="item_receive_issue_1" autocomplete="off" >
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
		<div style="width:1250px;" align="center">
			<h3 align="center" id="accordion_h1" style="width:1250px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
		</div>
		<div style="width:1250px;" align="center" id="content_search_panel">
			<fieldset style="width:1250px;">
				<table class="rpt_table" width="1245" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
					<thead>
						<tr>                            
							<th width="120">Company</th>                                
							<th width="130">Location</th>
							<th width="130">Item Category</th>   
							<th width="120">Item Group</th>
							<th width="100">Reqsn No</th>
							<th width="100">WO No</th>
							<th width="100">Report Criteria</th>
							<th width="120">Store</th>
							<th width="170" colspan="2" class="must_entry_caption">Date Range</th>
							<th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_field()" /></th>
						</tr>
					</thead>
					<tr class="general">
						<td>
							<?
							echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
							?>                          
						</td>
						<td id="location_td" >
							<? echo create_drop_down( "cbo_location_id", 120, $blank_array,"", 1, "-- All  --", 0, "",0 );?>
						</td>  
						<td>
							<?	echo create_drop_down( "cbo_item_cat_id", 120, $general_item_category,"", 1, "--- Select ---", $selected, "","","",0 ); ?>
						</td>                   
						<td>
							<?	echo create_drop_down( "cbo_item_group_id", 110, "select id,item_name from lib_item_group where status_active=1 ","id,item_name", 1, "-- Select --", "", "",0 );?>
						</td>                                     
						<td>
							<input type="text" name="txt_req_no"  id="txt_req_no" style="width:100px " class="text_boxes" placeholder="Write"/>
						</td>                                     
						<td>
							<input type="text" name="txt_wo_no"  id="txt_wo_no" style="width:100px " class="text_boxes" placeholder="Write"/>
						</td>   
						<td>
							<?	$report_criteria=array(1=>"All",2=>"Pending",);
								echo create_drop_down( "cbo_report_criteria", 100, $report_criteria,"", 0, "--- Select ---", $selected, "fn_wo_chk(this.value);","","",0 ); 
							?>
						</td> 
						<td id="store_td">
							<? echo create_drop_down( "cbo_store_id", 120, $blank_array,"", 1, "-- All  --", 0, "",0 );?>
						</td>                                   
						<td colspan="2">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:65px;" readonly/> 
							To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px;" readonly/>
						</td>
						<td>
						<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:80px" class="formbutton" />   
						</td>
					</tr>
					<tr>
						<td colspan="11" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
					</tr>
				</table>  
			</fieldset>    
		</div>
		<div id="report_container" align="center"></div>
		<div id="report_container2"></div> 
	</div> 
 </form>    
</body> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 

<script type="text/javascript">
	set_multiselect('cbo_company_name','0','0','0','0');
	set_multiselect('cbo_location_id','0','0','0','0');
	set_multiselect('cbo_item_cat_id','0','0','0','0');
	set_multiselect('cbo_item_group_id','0','0','0','0');
	$("#multi_select_cbo_company_name a").click(function(){
		getLocationName();
 	});
	function getLocationName()
	{
		var company=$("#cbo_company_name").val();
		load_drop_down( 'requires/unit_wise_purchase_requisition_follow_up_report_controller', company, 'load_drop_down_location', 'location_td' );
		load_drop_down( 'requires/unit_wise_purchase_requisition_follow_up_report_controller', company, 'load_drop_down_store', 'store_td' );
		set_multiselect('cbo_location_id','0','0','0','0');
	}
</script>
</html>

<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create AOP Production Status Report.
Functionality	:	
JS Functions	:
Created by		:	Md Mahbubur Rahman 
Creation date 	: 	07-01-2020
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
echo load_html_head_contents("AOP Printing Materials Receive And Issue Report", "../../", 1, 1,$unicode,1,1);
?>	

<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	var tableFilters = 
	{
		col_operation: {
			id: ["value_tdinqty","value_tdindeliqty","value_tdinbalqty","value_tdallinbalqty","value_receiveBalTotal"],
			col: [12,14,15,16,18],
			operation: ["sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	var tableFiltersAll = 
	{
		col_operation: {
			id: ["value_tdinqty","value_tdindeliqty","value_tdinbalqty","value_tdallinbalqty","value_receiveBalTotal"],
			col: [12,14,15,16,22],
			operation: ["sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
	
	function fn_report_generated(type)
	{
		if (form_validation('cbo_company_id*txt_date_from*txt_date_to','Comapny Name*From Date*To Date')==false)
		{
			return;
		}
		else
		{
			var report_title=$("div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_within_group*cbo_party_name*txt_job_no*txt_order_no*txt_order_id*txt_reference_no*txt_buyer_buyer_no*txt_buyer_po*txt_buyer_style*cbo_report_type*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title+'&type='+type;
			freeze_window(3);
			http.open("POST","requires/aop_printing_materials_receive_and_issue_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****"); 
			$('#report_container2').html(reponse[0]);
			var type = reponse[3];
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			 
			if(type == 3) {
				setFilterGrid("table_body",-1,tableFiltersAll);
			} else {
				setFilterGrid("table_body",-1,tableFilters);
			}
			
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tbody').find('tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$('#table_body tbody').find('tr:first').show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="300px";
	}
	
	function show_progress_report_details(action,order_id,width)
	{ 
		 emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/aop_printing_materials_receive_and_issue_report_controller.php?action='+action+'&order_id='+order_id, 'AOP Production Status Report', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../');
		
	} 
	
	function fnc_load_party(type)
	{
		if ( form_validation('cbo_company_id','Company')==false )
		{
			$('#cbo_within_group').val(1);
			return;
		}
		var company = $('#cbo_company_id').val();
		var cbo_within_group = $('#cbo_within_group').val();
		load_drop_down( 'requires/aop_printing_materials_receive_and_issue_report_controller', company+'_'+cbo_within_group, 'load_drop_down_buyer', 'buyer_td' );
		load_drop_down( 'requires/aop_printing_materials_receive_and_issue_report_controller', company+'_'+cbo_within_group, 'load_drop_down_buyer_buyer', 'buyer_buye_td' );
	}
	
	function openmypage_job()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_id').value;
		var cbo_buyer_name=document.getElementById('cbo_party_name').value;
		var page_link="requires/aop_printing_materials_receive_and_issue_report_controller.php?action=job_no_popup&company_id="+company_name+"&cbo_buyer_name="+cbo_buyer_name;
		var title="Job Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			//var job=theemail.split("_");
			document.getElementById('txt_job_no').value=theemail;
			release_freezing();
		}
	}	
	
	function openmypage_order()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_id').value;
		var cbo_buyer_name=document.getElementById('cbo_party_name').value;
		var job_no=document.getElementById('txt_job_no').value;
		var page_link="requires/aop_printing_materials_receive_and_issue_report_controller.php?action=order_no_popup&company_id="+company_name+"&cbo_buyer_name="+cbo_buyer_name+"&job_no="+job_no;
		var title="Order Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			var job=theemail.split("_");
			document.getElementById('txt_order_id').value=job[0];
			document.getElementById('txt_order_no').value=job[1];
			release_freezing();
		}
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
	<form id="aopOrderDtlsReport_1">
   	 	<div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1300px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1300px;">
            <table class="rpt_table" width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="130" class="must_entry_caption">Company</th>
                    <th width="60">Within Group</th>
                    <th width="120">Party</th>
                    <th width="80">AOP Job No</th>
                    <th width="80">AOP Order No</th> 
                    <th width="80">Aop Reference</th>
                    <th width="120">Buyer Name</th>
                    <th width="80">Buyer PO</th>
                    <th width="80">Buyer Style</th> 
                    <th width="80" style="display:none">Report Type</th>
                    <th width="130" colspan="2" class="must_entry_caption">Date</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:220px" value="Reset" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><? echo create_drop_down( "cbo_company_id", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_load_party(1);" ); ?></td>
                        <td><? echo create_drop_down( "cbo_within_group",60, $yes_no,"", 1, "-- All --", 0, "fnc_load_party(1);" ); ?></td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 120, $blank_array,"", 1, "-- Select Party --", $selected, "",1,"" ); ?></td>
                        <td><input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" placeholder="Wr/Br Job" onDblClick="openmypage_job();" ></td>
                        <td>
                            <input name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:70px"  placeholder="Wr/Br Order" onDblClick="openmypage_order();" >
                            <input type="hidden" name="txt_order_id" id="txt_order_id" class="text_boxes" style="width:60px">
                        </td>
                        <td><input name="txt_reference_no" id="txt_reference_no" class="text_boxes" style="width:70px"  placeholder="Write"></td>
                        <td id="buyer_buye_td"><? echo create_drop_down( "txt_buyer_buyer_no", 120, $blank_array,"", 1, "-- Select buyer --", $selected, "",1,"" ); ?></td>
                        <td><input name="txt_buyer_po" id="txt_buyer_po" class="text_boxes" style="width:70px" placeholder="Write"></td>
                        <td><input name="txt_buyer_style" id="txt_buyer_style" class="text_boxes" style="width:70px" placeholder="Write"></td>
                        <td style="display:none">
							<? 
								$reportType_arr = array(1=>'Inhouse',2=>'Sample',3=>'SubContract');
								echo create_drop_down( "cbo_report_type", 80, $reportType_arr,"", 1, "-- All --", 0, "","","1,3" ); 
							?>
                         </td>
                        <td><input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:50px" placeholder="From Date"/></td>
                        <td><input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:50px" placeholder="To Date"/></td>
                        <td>
                        	<input type="button" id="show_button" class="formbutton" style="width:90px" value="Receive" onClick="fn_report_generated(1)" />
                            <input type="button" id="show_button" class="formbutton" style="width:90px" value="Issue" onClick="fn_report_generated(2)" />
                            <input type="button" id="show_button" class="formbutton" style="width:90px" value="All" onClick="fn_report_generated(3)" />
                        
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="15" align="center"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
           </table> 
        </fieldset>
    </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 	</form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knitting Bill Report
				
Functionality	:	
JS Functions	:
Created by		:	Helal Uddin
Creation date 	: 	15-11-2020
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
echo load_html_head_contents("Knitting Bill Report","../../", 1, 1, $unicode,0,0); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	
	function generate_wo_order_report(company_id,knitting_wo_id)
	{
		print_report( company_id+'**'+knitting_wo_id,"work_order_print", "requires/knitting_and_dyeing_bill_details_report_controller");
	}
	
	

	function generate_report(type)
	{
		var job_no = $("#txt_job_id").val();
		var order_no = $("#txt_order_no").val();
		var bill_no = $("#txt_bill_no").val();
		var challan_no = $("#txt_challan_no").val();
		if(job_no!="" || order_no!="" || bill_no!="" || challan_no!="")
		{
			if(form_validation('cbo_company_id','Company Name')==false)
			{
				return;
			}
		}
		else
		{
			if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*Date Form*Date To')==false )
			{
				return;
			}
		}
		
		
		var report_title=$( "div.form_caption" ).html();
		if(type==1)
		{
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_party_source*cbo_party_name*report_type*txt_bill_no*txt_challan_no*txt_date_from*txt_date_to*txt_job_no*txt_job_id*txt_order_no*txt_order_id*cbo_job_order_type*cbo_year',"../../")+'&report_title='+report_title+'&type='+type;
		}
		else
		{
			var data="action=report_generate2"+get_submitted_data_string('cbo_company_id*cbo_party_source*cbo_party_name*report_type*txt_bill_no*txt_challan_no*txt_date_from*txt_date_to*txt_job_no*txt_job_id*txt_order_no*txt_order_id*cbo_job_order_type*cbo_year',"../../")+'&report_title='+report_title+'&type='+type;
		}
		
		freeze_window(3);
		http.open("POST","requires/knitting_and_dyeing_bill_details_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			// alert(http.responseText);
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window();" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
			if(reponse[2]==1)
			{
			var tableFilters = 
			{
				col_operation: {
				id: ["total_bill_qnty","total_bill","total_amount"],
				col: [14,15,18],
				operation: ["sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML"]
				}
			} 
			setFilterGrid("table_body",-1,tableFilters);
			}
			else
			{
				setFilterGrid("table_body",-1);
			}
			
			show_msg('3');
			release_freezing();
		}
	} 
	function generate_bill_report(company_id,id,bill_no,process_id,action)
	{
		//alert(data);
		var company_name=company_id;
		var update_id=id;
		var txt_bill_no=bill_no;
		var report_title=$( "div.form_caption" ).html();
		var data=company_name+'*'+update_id+'*'+txt_bill_no+'*'+report_title;
		if(process_id=4) //Inbound
		{
			if(action=='dyeing_finishin_bill_print')
			{
				var report_title=$( "div.form_caption" ).html();

				generate_report_file(company_name+'*'+update_id+'*'+txt_bill_no+'*'+report_title+'*'+show_val_column,action, '../requires/sub_fabric_finishing_bill_issue_controller',4);			
			}
			else if(action=='dyeing_finishin_bill_print3')
			{
				var report_title=$( "div.form_caption" ).html();

				generate_report_file(company_name+'*'+update_id+'*'+txt_bill_no+'*'+report_title,action, '../requires/sub_fabric_finishing_bill_issue_controller',4);			
			}	
			else if(action=='dyeing_finishin_bill_print4')
			{
				var report_title=$( "div.form_caption" ).html();

				generate_report_file(company_name+'*'+update_id+'*'+txt_bill_no+'*'+report_title,action, '../requires/sub_fabric_finishing_bill_issue_controller',4);			
			}	
			else if(action=='dyeingFinishinBillPrint5')
			{
				var report_title=$( "div.form_caption" ).html();

				generate_report_file(company_name+'*'+update_id+'*'+txt_bill_no+'*'+report_title,action, '../requires/sub_fabric_finishing_bill_issue_controller',4);			
			}	
			else if(action=='fabric_finishing_print')
			{
				var report_title=$( "div.form_caption" ).html();

				generate_report_file(company_name+'*'+update_id+'*'+txt_bill_no+'*'+report_title,action, '../requires/sub_fabric_finishing_bill_issue_controller',4);			
			}						
		}
		else //Kntting
		{
			generate_report_file(company_name+'*'+update_id+'*'+txt_bill_no+'*'+report_title,'knitting_bill_print_4', '../requires/knitting_bill_issue_controller.php',process_id);
			
		}

	}
	function generate_bill_outBound_report(company_id,id,bill_no,process_id,action)
	{
		var show_val_column='';
		var r=confirm("Press \"OK\" to open with Order Comments\nPress \"Cancel\" to open without Order Comments");
		if (r==true) show_val_column="1"; else show_val_column="0";

		var cbo_company_id=company_id;
		var update_id=id;
		var txt_bill_no=bill_no;
		var report_title=$( "div.form_caption" ).html();
		var data=cbo_company_id+'*'+update_id+'*'+txt_bill_no+'*'+report_title;

		if(process_id=4) //Outbound
		{
			if(action=='fabric_finishing_print')
			{
				var report_title=$( "div.form_caption" ).html();

				generate_report_file(cbo_company_id+'*'+update_id+'*'+txt_bill_no+'*'+report_title+'*'+show_val_column,action, '../requires/outside_finishing_bill_entry_controller',5);			
			}
			else if(action=='fabric_dyeing_finishing_print')
			{
				var report_title=$( "div.form_caption" ).html();

				generate_report_file(cbo_company_id+'*'+update_id+'*'+txt_bill_no+'*'+report_title+'*'+show_val_column,action, '../requires/outside_finishing_bill_entry_controller',5);			
			}						
		}
		else
		{
			generate_report_file(cbo_company_id+'*'+update_id+'*'+txt_bill_no+'*'+report_title+'*'+show_val_column,'outbound_knitting_bill_print', '../requires/outside_knitting_bill_entry_controller',3);
		}
	}

	function generate_report_file(data,action,path,type)
	{
		if(type==2) //Kntting
		{
		window.open("../requires/knitting_bill_issue_controller.php?data=" +data+ '&action='+action, true );
		}
		else if(type==3) //Outbound
		{
		window.open("../requires/outside_knitting_bill_entry_controller.php?data=" +data+ '&action='+action, true );
		}
		else if(type==4) //Outbound
		{
		window.open("../requires/sub_fabric_finishing_bill_issue_controller.php?data=" +data+ '&action='+action, true );
		}
		else if(type==5) //Outbound
		{
		window.open("../requires/outside_finishing_bill_entry_controller.php?data=" +data+ '&action='+action, true );
		}

	}
	
	function new_window()
	{
		var filter;
		if ($("#table_body tr:first").attr('class')=='fltrow')
		{
			filter=1;
			$("#table_body tr:first").hide();
		}
		var w = window.open("Surprise", "#");
		var d = w.document.open();

		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		if (filter==1)
		{
			$("#table_body tr:first").show();
		}
	}
	
	function openmypage_job()
	{
		if( form_validation('cbo_company_id*cbo_job_order_type*cbo_year','Company Name*job / Order field select* Job year')==false )
		{
			return;
		}
		var company = $("#cbo_company_id").val();	
		var party = $("#cbo_party_name").val();
		var type = $("#cbo_job_order_type").val();
		var job_year = $("#cbo_year").val();

		
		var page_link='requires/knitting_and_dyeing_bill_details_report_controller.php?action=job_popup&company='+company+'&party='+party+'&type='+type+'&job_year='+job_year;
		var title="Job Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=740px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			var year=this.contentDoc.getElementById("txt_year").value; // product Description
		//	alert(year);
			$("#txt_job_no").val(style_des);
			$("#txt_job_id").val(style_id); 
		
			
		}
	}
	function openmypage_order()
	{
		if( form_validation('cbo_company_id*cbo_job_order_type*cbo_year','Company Name*job / Order field select*Job year')==false )
		{
			return;
		}
		
		var company = $("#cbo_company_id").val();	
		var party = $("#cbo_party_name").val();
		var type = $("#cbo_job_order_type").val();
		var job_year = $("#cbo_year").val();

		
		var page_link='requires/knitting_and_dyeing_bill_details_report_controller.php?action=order_popup&company='+company+'&party='+party+'&type='+type+'&job_year='+job_year;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=740px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			var year=this.contentDoc.getElementById("txt_year").value; // product Description
		//	alert(year);
			
			$("#txt_order_no").val(style_des); 
			$("#txt_order_id").val(style_id); 
			
		}
	}
	function fnc_com_buy_supp_load()
	{
		var cbo_company_id=document.getElementById('cbo_company_id').value;
		var cbo_party_source=document.getElementById('cbo_party_source').value;
		var report_type=document.getElementById('report_type').value;
		load_drop_down( 'requires/knitting_and_dyeing_bill_details_report_controller',cbo_company_id+'_'+cbo_party_source+'_'+report_type, 'load_drop_down_party_name', 'party_td' );
	}
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission); ?>   		 
    <form name="knitting_bill_report_1" id="knitting_bill_report_1" autocomplete="off" > 
    <h3 style="width:1490px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1490px;">
                <table class="rpt_table" width="1470" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 
                        	<th width="140">Source</th>	 	
                            <th width="140" class="must_entry_caption">Company</th>    
                            <th width="140">Report Type</th>                           
                            <th width="140">Party Name</th>
							<th width="100">Buyer Name</th>
                            <th width="170">Bill No</th>	
							<th width="100">Challan No</th>
							<th width="80">For Job/order</th>
							<th width="50">Job Year</th>					
							<th width="100">Job No</th>
							<th width="100">Order No</th>
                            <th colspan="2" width="160" class="must_entry_caption">Bill Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:150px" class="formbutton" onClick="reset_form('knitting_bill_report_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                    	<td><? echo create_drop_down( "cbo_party_source", 130, $knitting_source,"", 1, "-- Select Party --", $selected, "fnc_com_buy_supp_load();load_drop_down( 'requires/knitting_and_dyeing_bill_details_report_controller',this.value +'_'+ document.getElementById('cbo_company_id').value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/knitting_and_dyeing_bill_details_report_controller', document.getElementById('location_id').value+'_'+this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_floor', 'floor_td' ); ",0,"1,2,3","","","",5); ?></td>
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3)  order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_com_buy_supp_load();load_drop_down( 'requires/knitting_and_dyeing_bill_details_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>                            
                        </td>
                        <td width="140" >
                        	<?php $bill_type_arr = array(2=>"Knitting",4=>"Dyeing Finishing"); ?>
                        	<?php echo create_drop_down( "report_type", 130, $bill_type_arr,"", 0, "-- Select --", $selected, "fnc_com_buy_supp_load()",0,"","","","",5); ?>
                        </td>
                        
                        
                       
                        <td width="140" id="party_td">
                        	<? echo create_drop_down( "cbo_party_name", 130, $blank_array,"", 1, "--Select Party--", $selected, "",0,"","","","",6); ?>
                   		 </td>
						<td id="buyer_td">
                        <? 
                            echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                        ?>
                       </td>
                        <td align="center" >
								<input type="text" style="width:130px" class="text_boxes" name="txt_bill_no" id="txt_bill_no"/>
						</td>
						<td align="center" >
								<input type="text" style="width:90px" class="text_boxes" name="txt_challan_no" id="txt_challan_no"/>
						</td>
						<td >
                        	
                        	<? $type_arr = array(1=>"In-house",2=>"Sub-Contact");
							echo create_drop_down( "cbo_job_order_type", 80, $type_arr,"", 0, "-- Select --",1, "",0,"","","","",5); ?>
                        </td>
						<td><? echo create_drop_down( "cbo_year", 50, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
						<td align="center" >
								<input type="text" style="width:130px" class="text_boxes" name="txt_job_no" onDblClick="openmypage_job()" id="txt_job_no" placeholder="Browse" readonly/>
								<input type="hidden" style="width:130px" class="text_boxes" name="txt_job_id" id="txt_job_id" value=""/>
						</td>
						<td align="center" >
								<input type="text" style="width:130px" class="text_boxes" name="txt_order_no" onDblClick="openmypage_order()" id="txt_order_no" placeholder="Browse" readonly/>
								<input type="hidden" style="width:130px" class="text_boxes" name="txt_order_id" id="txt_order_id" value=""/>
						</td>
                        
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:60px" placeholder="From Date"/>
                        </td>
                        <td>
                             <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:60px" placeholder="To Date"/>
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1);" style="width:70px" class="formbutton" />
                            <input type="button" name="search" id="search" value="Show2" onClick="generate_report(2);" style="width:70px" class="formbutton" />
                        </td>
                    </tr>
                    <tr>
						<td colspan="9" align="center" width="100%"><? echo load_month_buttons(1); ?></td>                    
					</tr>
                </table> 
            </fieldset> 
        </div>
            <div id="report_container" align="center"></div>
            <div id="report_container2"></div>   
    </form>    
</div>    
</body>  

<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>

<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Count Wise Yarn Requirement Report
				
Functionality	:	
JS Functions	:
Created by		:	Helal Uddin
Creation date 	: 	04-05-2020
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		: 
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Count Wise Yarn Requirement Report","../../../", 1, 1, $unicode,0,0); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	var tableFilters = 
	 {
		//col_0: "none",
		col_operation: {
			id: ["value_td_req","value_td_all","value_td_iss","value_td_ret", "value_td_bal"],
			col: [12,13,14,15,16],
			operation: ["sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}	
	}
	
	function openmypage_job()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();
		var page_link='requires/count_wise_yarn_requirement_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_job_no').val(job_no);
			$('#txt_job_id').val(job_id);	 
		}
	}
	function openmypage(order_id,type,yarn_count,yarn_comp_type1st,yarn_comp_percent1st,yarn_comp_type2nd,yarn_comp_percent2nd,yarn_type,trans_id)
	{
		var popup_width='';
		popup_width='1000px'; 
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/count_wise_yarn_requirement_report_controller.php?order_id='+order_id+'&action='+type+'&yarn_count='+yarn_count+'&yarn_comp_type1st='+yarn_comp_type1st+'&yarn_comp_percent1st='+yarn_comp_percent1st+'&yarn_comp_type2nd='+yarn_comp_type2nd+'&yarn_comp_percent2nd='+yarn_comp_percent2nd+'&yarn_type_id='+yarn_type+'&trans_id='+trans_id, 'Detail Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	
	
	function openmypage_booking()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();
		var txt_job_no = $("#txt_job_no").val();
		var page_link='requires/count_wise_yarn_requirement_report_controller.php?action=booking_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id+'&txt_job_no='+txt_job_no;
		var title='Booking No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=430px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var booking_no=this.contentDoc.getElementById("hide_job_no").value;
			var booking_id=this.contentDoc.getElementById("hide_job_id").value;
			//alert(booking_no);
			$('#txt_booking_no').val(booking_no);
			$('#txt_booking_id').val(booking_id);	 
		}
	}

	function generate_report()
	{
		var txt_job_no = $("#txt_job_no").val();
		var txt_booking_no = $("#txt_booking_no").val();
		if(txt_job_no == "" && txt_booking_no == "")
		{
			if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*Date Form*Date To')==false )
			{
				return;
			}
		}
		else
		{
			if( form_validation('cbo_company_id','Company Name')==false )
			{
				return;
			}
		}
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate2"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_year*txt_job_no*txt_booking_no*txt_booking_id*cbo_search_by*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
		//alert (data);return;
		freeze_window(3);
		http.open("POST","requires/count_wise_yarn_requirement_report_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window();" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
			//var batch_type = document.getElementById('cbo_batch_type').value;
			
			setFilterGrid("table_body",-1,tableFilters);
			
			show_msg('3');
			release_freezing();
		}
	} 
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="400px";
		
		$("#table_body tr:first").show();
	}
	
	function fnc_yarn_req_entry(operation,cbo_company_name,update_id,is_approved)
	{
		var report_title="Yarn Purchase Requisition";
		if(operation==4)
		 {
			
			print_report( cbo_company_name+'*'+update_id+'*'+report_title+'*'+is_approved,"yarn_requisition_print", "requires/count_wise_yarn_requirement_report_controller")
			return;
		 }

		 else if(operation==6)
		 {
			
			print_report( cbo_company_name+'*'+update_id+'*'+report_title+'*'+is_approved,"yarn_requisition_print_2", "requires/count_wise_yarn_requirement_report_controller")
			return;
		 }

		 else if(operation==7)
		 {

			
			print_report( cbo_company_name+'*'+update_id+'*'+report_title+'*'+is_approved,"yarn_requisition_print_3", "requires/count_wise_yarn_requirement_report_controller")
			return;
		 }

		else if(operation==8)
		{
			
			print_report(cbo_company_name+'*'+update_id+'*'+report_title+'*'+is_approved,"yarn_requisition_print_4", "requires/count_wise_yarn_requirement_report_controller")
			return;
		}
		else if(operation==9)
		{
			
			print_report(cbo_company_name+'*'+update_id+'*'+report_title+'*'+is_approved,"yarn_requisition_print_5", "requires/count_wise_yarn_requirement_report_controller")
			return;
		}

		
	}
	
	function generate_worder_report(type,booking_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved,action,page)
	{
		var show_yarn_rate='';
		var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
		
		if (r==true) show_yarn_rate="1"; else show_yarn_rate="0";
		
		var data="action="+action+
					'&txt_booking_no='+"'"+booking_no+"'"+
					'&cbo_company_name='+"'"+company_id+"'"+
					'&txt_order_no_id='+"'"+order_id+"'"+
					'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
					'&cbo_fabric_source='+"'"+fabric_source+"'"+
					'&id_approved_id='+"'"+approved+"'"+
					'&txt_job_no='+"'"+job_no+"'"+
					'&show_yarn_rate='+show_yarn_rate;
					
					//var data="action="+type+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*id_approved_id*txt_job_no',"../../")+'&report_title='+$report_title+'&show_yarn_rate='+show_yarn_rate+'&path=../../';
		if(type==1)	
		{			
			http.open("POST","../../../order/woven_order/requires/short_fabric_booking_controller.php",true);
		}
		else if(type==2)
		{
			if(page==154) http.open("POST","../../../order/woven_order/requires/fabric_booking_urmi_controller.php",true);
			else http.open("POST","../../../order/woven_order/requires/fabric_booking_controller.php",true);
		}
		else
		{
			http.open("POST","../../../order/woven_order/requires/sample_booking_controller.php",true);
		}
		
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
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>   		 
    <form name="jobordewiseyarnissuereport_1" id="jobordewiseyarnissuereport_1" autocomplete="off" > 
    <h3 style="width:970px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:970px;">
                <table class="rpt_table" width="950" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="140" class="must_entry_caption">Company</th>                                
                            <th width="140">Buyer</th>
                            <th width="110">Job No</th>
                            <th width="80">Year</th>
                            <th width="110">Fab. Booking No.</th>
                            <th width="100">Search By</th>
                            <th width="160" class="must_entry_caption">Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('jobordewiseyarnissuereport_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/count_wise_yarn_requirement_report_controller',this.value+'_'+1+'_'+4, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>                            
                        </td>

                        <td id="buyer_td"><?=create_drop_down( "cbo_buyer_id", 140, $blank_array,"", 1, "--Select Buyer--", 0, "",0 ); ?></td>
                       
                        <td><input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes_numeric" style="width:100px"  placeholder="Write" /></td>
                        <td><?=create_drop_down( "cbo_year", 80, $year,"", 1, "--Select Year--", $selected, "",0 ); ?></td>
                        <td>
                            <input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes" style="width:100px" onDblClick="openmypage_booking();" placeholder="Browse/Write" />
                            <input type="hidden" id="txt_booking_id" name="txt_booking_id"/>
                        </td>
                        <td> 
                            <?
								$search_by=array(1=>'Po Ship Date',2=>'Publish Date');
                                echo create_drop_down( "cbo_search_by", 100, $search_by,"", 0, "--Select--", 1, "",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:60px" placeholder="From Date"/>
                             To
                             <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:60px" placeholder="To Date"/>
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report();" style="width:70px" class="formbutton" />
                        </td>
                    </tr>
                    <tr>
						<td colspan="8" align="center" width="100%"><? echo load_month_buttons(1); ?></td>                    
					</tr>
                </table> 
            </fieldset> 
        </div>
            <div id="report_container" align="center"></div>
            <div id="report_container2"></div>   
    </form>    
</div>    
</body>  
<script>
	//set_multiselect('cbo_yarn_type*cbo_yarn_count','0*0','0*0','','0*0');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>

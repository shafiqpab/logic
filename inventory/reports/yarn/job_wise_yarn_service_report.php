<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Job Wise Yarn Service Report
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	01-12-2019
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
echo load_html_head_contents("Job Wise Yarn Service Report","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	function generate_report(type)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_supplier_name*cbo_year*txt_job_no*hide_job_id*txt_process_loss*cbo_date_type*txt_date_from*txt_date_to*cbo_search_by*txt_search_comm*cbo_wo_type',"../../../")+'&report_title='+report_title+'&type='+type;
		//alert (data);
		freeze_window(3);
		http.open("POST","requires/job_wise_yarn_service_report_controller.php",true);
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
			
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
	}

	function openmypage_job()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year = $("#cbo_year").val();
		var page_link='requires/job_wise_yarn_service_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year='+cbo_year;
		var title='Job No Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			//alert (job_no);
			$('#txt_job_no').val(job_no);
			$('#hide_job_id').val(job_id);	 
		}
	}

	function fn_date_type(type_id)
	{
		if(type_id==2) $('#td_date').text("Trans. Date");
		else $('#td_date').text("Booking Date");
	}

	function openmypage(job_no,booking_id,color,lot,action,trans_type)
	{ 
		var companyID = $("#cbo_company_name").val();
		var popup_width='600px';
		var data_ref='requires/job_wise_yarn_service_report_controller.php?companyID='+companyID+'&job_no='+job_no+'&booking_id='+booking_id+'&color='+color+'&lot='+lot+'&action='+action+'&trans_type='+trans_type;
		//alert(data_ref);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', data_ref, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}

	function generate_trim_report(action,booking_no,company_name,update_id,entry_form)
	{
		var show_comment='';
		var r=confirm("Press  \"Cancel\"  to hide  Comment\nPress  \"OK\"  to Show Comment");
		if (r==true) show_comment="1"; else show_comment="0";
		
		var form_name="yarn_dyeing_wo_booking";
		var data="action="+action+"&form_name="+form_name+"&txt_booking_no="+booking_no+"&cbo_company_name="+company_name+"&update_id="+update_id+"&show_comment="+show_comment;
		if(entry_form==94)
			http.open("POST","../../../order/woven_order/requires/yarn_service_work_order_controller.php",true);
		else
			http.open("POST","../../../order/woven_order/requires/yarn_dyeing_charge_booking_controller.php",true);
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_trim_report_reponse;
	}

	function generate_trim_report_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert( http.responseText);return;
			var file_data=http.responseText.split('****');
			//$('#pdf_file_name').html(file_data[1]);
			$('#data_panel').html(file_data[0] );
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
		}
	}
	
	function change_caption(type)
	{
		if(type==1) $('#td_search').html('W/O No');
		else if(type==2) $('#td_search').html('Booking No');
	}
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>		 
    <form name="DyedYarnReport_1" id="DyedYarnReport_1" autocomplete="off" > 
    <h3 align="left" id="accordion_h1" style="width:1100px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel">
            <fieldset style="width:1100px;">
                <table class="rpt_table" width="1100" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="130" class="must_entry_caption">Company Name</th>
                            <th width="60">WO Type</th>
                            <th width="130">Buyer Name</th>
                            <th width="130">Party Name</th>
                            <th width="60">Year</th>
                            <th width="80">Job No</th>
                            <th width="50">Process Loss %</th>
                            <th width="100">Search By</th>
                            <th width="80" id="td_search">W/O No</th>
                            <th width="60">Date Type</th>
                            <th width="130" colspan="2" id="td_date">Booking Date</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('DyedYarnReport_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td><? echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/job_wise_yarn_service_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' ); load_drop_down( 'requires/job_wise_yarn_service_report_controller',this.value, 'load_drop_down_supplier', 'supplier_td' );" );?> </td>
                        <td id="work_order_type_td"><? 
								$wo_type_arr = array(0=>'All',1=>'Main',2=>'Short');
                                echo create_drop_down( "cbo_wo_type", 60, $wo_type_arr,"", 0, "-- Select Type --", $selected, "",0,"" );?>
                        </td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"" ); ?></td>
                        <td id="supplier_td"><? echo create_drop_down( "cbo_supplier_name", 130, $blank_array,"", 1, "--Select Supplier--", $selected, "",0,"" ); ?></td>
                        <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
                        <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" placeholder="Write/Browse" onDblClick="openmypage_job();" >
                             <input type="hidden" name="hide_job_id" id="hide_job_id" readonly>
                        </td>
                        <td><input type="text" name="txt_process_loss" id="txt_process_loss" class="text_boxes" style="width:40px" value="4"></td>
                        <td> 
                            <?
								$search_by=array(1=>'W/O No');//,2=>'Booking No'
                                echo create_drop_down( "cbo_search_by", 100, $search_by,"", 0, "--Select--", 1, "change_caption(this.value);",0 );
                            ?>
                        </td>
                        <td><input type="text" id="txt_search_comm" name="txt_search_comm" class="text_boxes" style="width:70px" placeholder="Write" /></td>
                        <td>
                             <? 
							 $search_type_arr=array(1=>"Booking Date",2=>"Trans. Date");
							 echo create_drop_down( "cbo_date_type", 60, $search_type_arr,"", 0,"-- All --", 1, "fn_date_type(this.value);",0,"" ); ?>
                        </td>
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" ></td>
                        <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td>
                        <td><input type="button" name="search" id="search" value="Job Wise" onClick="generate_report(1)" style="width:70px" class="formbutton" /></td>
                    </tr>
                    <tfoot>
                        <tr align="center">
                            <td colspan="13" align="center"><? echo load_month_buttons(1);  ?></td>
                        </tr>
                    </tfoot>
                </table> 
            </fieldset> 
        </div>
    </form>    
</div> 
<div id="report_container" align="center"></div>
<div id="report_container2"></div>
<div style="display:none" id="data_panel"></div>   
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
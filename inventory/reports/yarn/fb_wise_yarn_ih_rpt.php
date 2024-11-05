<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create FB Wise Yarn IH Schedule Report
				
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	06-11-2018
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
echo load_html_head_contents("FB Wise Yarn IH Schedule Report","../../../", 1, 1, $unicode,0,0); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	
	/*
	|--------------------------------------------------------------------------
	| for openmypage_company
	|--------------------------------------------------------------------------
	|
	*/
	function openmypage_company()
	{
		var cbo_company = $("#cbo_company_id").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/fb_wise_yarn_ih_rpt_controller.php?action=company_popup&cbo_company='+cbo_company, 'Company Details', 'width=325px,height=250px,center=1,resize=0,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var selected_name=this.contentDoc.getElementById("selected_name").value;
			var selected_id=this.contentDoc.getElementById("selected_id").value;
			
			$("#txt_company").val(selected_name);
			$("#cbo_company_id").val(selected_id);
			
			//for buyer
			load_drop_down( 'requires/fb_wise_yarn_ih_rpt_controller',selected_id+'_'+1+'_'+4, 'load_drop_down_buyer', 'buyer_td' );			
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
		var page_link='requires/fb_wise_yarn_ih_rpt_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
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
		var page_link='requires/fb_wise_yarn_ih_rpt_controller.php?action=booking_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id+'&txt_job_no='+txt_job_no;
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

	function generate_report(report_type)
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
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_year*txt_job_no*txt_job_id*txt_booking_no*txt_booking_id*cbo_search_by*cbo_allocation_balance_status*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title+'&report_type='+report_type;
		//alert (data);return;
		freeze_window(3);
		http.open("POST","requires/fb_wise_yarn_ih_rpt_controller.php",true);
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
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>   		 
    <form name="jobordewiseyarnissuereport_1" id="jobordewiseyarnissuereport_1" autocomplete="off" > 
    <h3 style="width:1070px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1070px;">
                <table class="rpt_table" width="1050" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="140" class="must_entry_caption">Company</th>                                
                            <th width="140">Buyer</th>
                            <th width="80">Year</th>
                            <th width="110">Job No</th>
                            <th width="110">Fab. Booking No.</th>
                            <th width="100">Allocation Balance Status</th>
                            <th width="100">Search By</th>
                            <th width="160" class="must_entry_caption">Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('jobordewiseyarnissuereport_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
                            <input type="text" id="txt_company" name="txt_company" class="text_boxes" style="width:130px" value="" onDblClick="openmypage_company();" placeholder="Browse" readonly />
                            <input type="hidden" id="cbo_company_id" name="cbo_company_id" class="text_boxes" style="width:110px" />
                        </td>
                        <td id="buyer_td"> 
                            <?
                                echo create_drop_down( "cbo_buyer_id", 140, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                            ?>
                        </td>
                        <td> 
                            <?
								$selected_year=date("Y");
                                echo create_drop_down( "cbo_year", 80, $year,"", 1, "--Select Year--", $selected_year, "",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes_numeric" style="width:100px" onDblClick="openmypage_job();" placeholder="Browse" readonly />
                            <input type="hidden" id="txt_job_id" name="txt_job_id"/>
                        </td>
                        <td>
                            <input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes" style="width:100px" onDblClick="openmypage_booking();" placeholder="Browse/Write" readonly />
                            <input type="hidden" id="txt_booking_id" name="txt_booking_id"/>
                        </td>
                        <td> 
                            <?
								$allocation_balance_status=array(1=>'All',2=>'Full Pending',3=>'Partial Balance',4=>'No Balance',5=>'Full Pending And Partial Balance');
                                echo create_drop_down( "cbo_allocation_balance_status", 100, $allocation_balance_status,"", 0, "--Select--", 1, "",0 );
                            ?>
                        </td>
                        <td> 
                            <?
                                $search_by=array(1=>'Ship Date',2=>'Booking Date',3=>'BK Insert Date',4=>'TNA Date');
                                echo create_drop_down( "cbo_search_by", 100, $search_by,"", 0, "--Select--", 1, "",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:60px" placeholder="From Date"/>
                             To
                             <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:60px" placeholder="To Date"/>
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                        </td>
                    </tr>
                    <tr>
						<td colspan="8" align="center"><? echo load_month_buttons(1); ?></td>    
						<td>
                            <input type="button" name="search" id="search" value="TNA Wise" onClick="generate_report(2)" style="width:70px; margin-left:12px;" class="formbutton" />
                        </td>                
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

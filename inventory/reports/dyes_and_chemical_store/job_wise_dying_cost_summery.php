<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Job Wise Dying Cost Report
				
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	26-02-2020
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
echo load_html_head_contents("Job Wise Dying Cost Report","../../../", 1, 1, $unicode,'',''); 
?>	
	<script>
    var permission='<? echo $permission; ?>';
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	function openmypage_job(type)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year_id = $("#cbo_year").val();
		var page_link='requires/job_wise_dying_cost_summery_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id+'&type='+type;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var ref_data=this.contentDoc.getElementById("hide_job_no").value.split("_");
			//var job_id=this.contentDoc.getElementById("hide_job_id").value;
			//alert(type);return;
			if(type==1)
			{
				$('#txt_job_no').val(ref_data[1]);
				$('#txt_job_id').val(ref_data[0]);
			}
			else if(type==2)
			{
				$('#txt_po_no').val(ref_data[1]);
				$('#txt_po_id').val(ref_data[0]);
			}
			else
			{
				$('#txt_style_no').val(ref_data[2]);
				$('#txt_job_id').val(ref_data[0]);
			}
			
		}
	}
	
	function openmypage_booking()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year_id = $("#cbo_year").val();
		var txt_job_no = $("#txt_job_no").val();
		var page_link='requires/job_wise_dying_cost_summery_controller.php?action=booking_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id+'&txt_job_no='+txt_job_no;
		var title='Booking No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=430px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var booking_no=this.contentDoc.getElementById("hide_booking_no").value.split("_");
			//alert(booking_no);return;
			$('#txt_booking_id').val(booking_no[0]);
			$('#txt_booking_no').val(booking_no[1]);
		}
	}
	
	
	function generate_report(operation)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		if($("#txt_job_no").val() =='' && $("#txt_po_no").val()=='' && $("#txt_style_no").val()=='' && $("#txt_booking_no").val()=='')
		{
			alert("Please Select Job No");return;
		}
		
		var report_title=$( "div.form_caption" ).html(); 
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year*txt_job_no*txt_job_id*txt_po_no*txt_po_id*txt_style_no*txt_booking_no*txt_booking_id',"../../../")+'&report_title='+report_title;
		//alert (data);return;
		freeze_window(operation);
		http.open("POST","requires/job_wise_dying_cost_summery_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);  
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//setFilterGrid("table_body_id",-1,tableFilters3);
			//setFilterGrid("table_body_multibatch_id",-1,tableFilters2);
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
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
	}
	
	function fn_1st_batch(batch_id,action)
	{
		var batch_type=$('#cbo_batch_type').val();
		var width=350;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/job_wise_dying_cost_summery_controller.php?action='+action+'&batch_id='+batch_id, 'Batch Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}
  
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?><br />    		 
        <form name="closingstock_1" id="closingstock_1" autocomplete="off" > 
         <h3 style="width:1100px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:1100px" >      
            <fieldset>  
                <table class="rpt_table" width="1080" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                       <th class="must_entry_caption" width="150">Company</th>
                       <th width="150">Buyer</th>
                       <th width="80">Job Year</th>
                       <th width="150">Job No</th> 
                       <th width="150">Order No</th> 
                       <th width="150">Style</th> 
                       <th width="150">Booking</th>
                       <th width="100"><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('closingstock_1','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                           	<td>
							<? 
                            echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/job_wise_dying_cost_summery_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );//load_drop_down( 'requires/job_wise_dying_cost_summery_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );
                            ?>                            
                            </td>
                            <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "- All Buyer -", $selected, "",0,"" );?></td>
                            <td align="center"><? echo create_drop_down( "cbo_year", 70, $year,"", 0,"", date("Y",time()), "",0,"" );?></td>
                            <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:130px" onDblClick="openmypage_job(1)" placeholder="Browse" readonly />
                            <input type="hidden" id="txt_job_id" name="txt_job_id"/>
                            </td>
                         	<td>
                            <input type="text" id="txt_po_no" name="txt_po_no" class="text_boxes" style="width:130px" onDblClick="openmypage_job(2)" placeholder="Browse" readonly />
                            <input type="hidden" id="txt_po_id" name="txt_po_id"/>
                            </td>
                            <td>
                            <input type="text" style="width:130px;" name="txt_style_no" id="txt_style_no" class="text_boxes" onDblClick="openmypage_job(3)" placeholder="Browse" readonly />
                            </td>
                            <td>
                            <input type="text" style="width:130px;" name="txt_booking_no" id="txt_booking_no" class="text_boxes"  onDblClick="openmypage_booking()" placeholder="Browse" readonly />
                            <input type="hidden" id="txt_booking_id" name="txt_booking_id"/>
                            </td>
                            <td><input type="button" name="search" id="search" value="Job Wise" onClick="generate_report(5)" style="width:80px;" class="formbutton" /></td>
                        </tr>
                    </tbody>
                </table> 
            </fieldset> 
            </div>
        </form>    
    </div>
    <br /> 
    <div id="report_container" align="center"></div>
   <div id="report_container2"></div> 
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>

<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create ERP Rate & Dyeing Actual Bill Rate Variance Report.
Functionality	:	
JS Functions	:
Created by		:	Md. Mamun Ahmed Sagor
Creation date 	: 	06-10-2021
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
echo load_html_head_contents("ERP Rate & Dyeing Actual Bill Rate Variance Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
			
	function fn_report_generated(type)
	{

		var company=$('#cbo_company_id').val();
		var job_no=$('#txt_job_no').val();
		var style_ref=$('#txt_style_ref').val();
		var order_no=$('#txt_order_no').val();
		var date_from=$('#txt_date_from').val();
		//  alert(job_no+'=>'+style_ref+'=>'+order_no);

		 if (form_validation('cbo_company_id','Comapny Name')==false){return;}

		if(job_no=="" && style_ref=="" && order_no=="" &&  date_from==""){
			// if (form_validation('cbo_company_id*txt_job_no*txt_style_ref*txt_order_no','Comapny Name*Job no*style ref*order no')==false)
			// 	{
			// 		return;
			// 	}
			if (form_validation('cbo_company_id*txt_job_no','Comapny Name*Job No')==false)
				{
					return;
				}
		}
		
		

		
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_job_no*txt_style_ref*txt_order_no*txt_date_from*txt_date_to',"../../")+'&type='+type;
			freeze_window(3);
			http.open("POST","requires/erp_rate_and_dyeing_actual_bill_rate_variance_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		
	}

	function fn_report_generated_reponse()
	{	
		if(http.readyState == 4) 
		{
			//alert (http.responseText);
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			//alert(reponse[1]);
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
	    '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
	}
	
	function openmypage_job()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_id').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_id').value;
		var year=document.getElementById('cbo_year').value;
		var page_link="requires/erp_rate_and_dyeing_actual_bill_rate_variance_report_controller.php?action=job_no_popup&company_id="+company_name+"&cbo_buyer_name="+cbo_buyer_name+"&year="+year;
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
	
	function openmypage_style()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_id').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_id').value;
		var year=document.getElementById('cbo_year').value;
		var page_link="requires/erp_rate_and_dyeing_actual_bill_rate_variance_report_controller.php?action=style_no_popup&company_id="+company_name+"&cbo_buyer_name="+cbo_buyer_name+"&year="+year;
		var title="Style Ref.";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			//var job=theemail.split("_");
			document.getElementById('txt_style_ref').value=theemail;
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
		var cbo_buyer_name=document.getElementById('cbo_buyer_id').value;
		var year=document.getElementById('cbo_year').value;
		var job_no=document.getElementById('txt_job_no').value;
		var page_link="requires/erp_rate_and_dyeing_actual_bill_rate_variance_report_controller.php?action=order_no_popup&company_id="+company_name+"&cbo_buyer_name="+cbo_buyer_name+"&year="+year+"&job_no="+job_no;
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
<form id="workProgressReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:940px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:940px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="150" class="must_entry_caption">Company</th>
                    <th width="150">Buyer</th>
                                 
                    <th width="60">Job No</th>
                    <th width="80">Style Ref.</th>
                    <th width="80">Order No</th>
					
                    <th width="100" class="must_entry_caption" colspan="2">Bill Rate Range</th>
                    <th width="150">
                    <input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" />
                    </th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td  align="center"> 
                            <?
                                echo create_drop_down( "cbo_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/erp_rate_and_dyeing_actual_bill_rate_variance_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_id", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                            ?>
                        </td>
                        
                        <td>
                            <input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:55px" placeholder="Write Job"  >
                        </td>
                        <td>
                            <input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:75px" placeholder="Write Style"  >
                        </td>
                        <td>
                            <input name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:75px"  placeholder="Write Order"  >
                            <input type="hidden" name="txt_order_id" id="txt_order_id" class="text_boxes" style="width:70px">
                        </td>
						
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" >
                        </td>
						<td>
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date" >
                        </td>
                        <td align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated(1)" />
                        </td>
                    </tr>
					<tr>
						<td align="center" colspan="8"><?=load_month_buttons(1); ?></td>
					</tr>
                </tbody>
           </table> 
           <br />
        </fieldset>
    </div>
    </div>
    <div id="report_container" align="center" style="padding: 10px;"></div>
    <div id="report_container2" align="left"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

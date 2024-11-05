<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Batch Wise Recipe Requisition Monitoring Report.
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	02-11-2019
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
echo load_html_head_contents("Batch Wise Recipe Requisition Monitoring Report", "../../", 1, 1,'',1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	var tableFilters = 
		 {
			//col_0: "none",
			col_operation: {
				id: ["value_td_batch","value_td_recipe","value_td_req","value_td_dyeIss", "value_td_dye","value_td_prod","value_td_rec","value_td_finIss"],
				col: [12,13,14,15,16,17,18,19],
				operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}	
		}
		
	function fn_report_generated(type)
	{
		var company_name=document.getElementById('cbo_company_name').value;
		var working_company_id=document.getElementById('cbo_working_company').value;
		var job_no=document.getElementById('txt_job_no').value;
		var booking_no=document.getElementById('txt_booking_no').value;
		var order_no=document.getElementById('txt_order_no').value;
		var batch_no=document.getElementById('txt_batch_no').value;
		var txt_recipe_no=document.getElementById('txt_recipe_no').value;
		var txt_req_no=document.getElementById('txt_req_no').value;
		var txt_date_from=document.getElementById('txt_date_from').value;
		var txt_date_to=document.getElementById('txt_date_to').value;
		
		if(job_no!=""  || order_no!="" || batch_no!="" || booking_no!="" || txt_recipe_no!="" || txt_req_no!="")
		{
			if(company_name == 0 && working_company_id ==0) 
			{			
				alert("Please Select either a company or a working company");
				return;			
			}
		}
		else
		{
			if(company_name == 0 && working_company_id ==0) 
			{			
				alert("Please Select either a company or a working company");
				return;			
			}
			else if (txt_date_from=='' && txt_date_to=='') 
			{
				if( form_validation('txt_ref_no*txt_date_from*txt_date_to','Ref. No*Form Date*To Date')==false )
				{
					return;
				}
			}
		}
		
		var report_title=$("div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_batch_type*cbo_company_name*cbo_buyer_name*cbo_working_company*cbo_year*txt_job_no*txt_job_id*txt_booking_no*txt_hide_booking_id*txt_order_no*hide_order_id*txt_batch_no*hide_batch_id*txt_recipe_no*hide_recipe_id*txt_req_no*hide_req_id*cbo_search_date*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/batch_wise_recipe_req_monitoring_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			$('#report_container2').html(response[0]);
			//var path = '../../';
			//document.getElementById('report_container').innerHTML=report_convert_button(path); 
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window();" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
			//var batch_type = document.getElementById('cbo_batch_type').value;
			
			setFilterGrid("table_body",-1,tableFilters);
			
			show_msg('3');
			release_freezing();
		}
	}
	
	function openmypage_job()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year_id = $("#cbo_year").val();
		var cbo_batch_type = $("#cbo_batch_type").val();
		//alert(cbo_year_id);
		var page_link='requires/batch_wise_recipe_req_monitoring_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id+'&cbo_batch_type='+cbo_batch_type;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../');
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
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year_id = $("#cbo_year").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/batch_wise_recipe_req_monitoring_report_controller.php?action=booking_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Booking No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1010px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var booking_no=this.contentDoc.getElementById("hide_booking_no").value;
			var booking_id=this.contentDoc.getElementById("hide_booking_id").value;
			$('#txt_booking_no').val(booking_no);
			$('#txt_hide_booking_id').val(booking_id);	 
		}
	}
	
	function openmypage_order()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year_id = $("#cbo_year").val();
		var cbo_batch_type = $("#cbo_batch_type").val();
		var page_link='requires/batch_wise_recipe_req_monitoring_report_controller.php?action=order_no_search_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id+'&cbo_batch_type='+cbo_batch_type;
		var title='Order No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_no=this.contentDoc.getElementById("hide_order_no").value;
			var order_id=this.contentDoc.getElementById("hide_order_id").value;
			$('#txt_order_no').val(order_no);
			$('#hide_order_id').val(order_id);	 
		}
	}
	
	function openmypage_batch()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var page_link='requires/batch_wise_recipe_req_monitoring_report_controller.php?action=batch_no_search_popup&companyID='+companyID+'&buyer_name='+buyer_name;
		var title='Order No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_no=this.contentDoc.getElementById("hide_order_no").value;
			var order_id=this.contentDoc.getElementById("hide_order_id").value;
			$('#txt_batch_no').val(order_no);
			$('#hide_batch_id').val(order_id);	 
		}
	}
	
	function openmypage_recipe()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var page_link='requires/batch_wise_recipe_req_monitoring_report_controller.php?action=recipe_popup&companyID='+companyID+'&buyer_name='+buyer_name;
		var title='Recipe No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var recipe_no=this.contentDoc.getElementById("hide_recipe_no").value;
			var recipe_id=this.contentDoc.getElementById("hide_recipe_id").value;
			$('#txt_recipe_no').val(recipe_no);
			$('#hide_recipe_id').val(recipe_no);	 
		}
	}
	
	function openmypage_requisition()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var page_link='requires/batch_wise_recipe_req_monitoring_report_controller.php?action=requisition_popup&companyID='+companyID+'&buyer_name='+buyer_name;
		var title='Requisition No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var req_no=this.contentDoc.getElementById("hide_req_no").value;
			var req_id=this.contentDoc.getElementById("hide_req_id").value;
			$('#txt_req_no').val(req_no);
			$('#hide_req_id').val(req_id);	 
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
		document.getElementById('scroll_body').style.maxHeight="450px";
		
		$("#table_body tr:first").show();
	}
	
	function generate_report(companyid,batchid,action)
	{
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/batch_wise_recipe_req_monitoring_report_controller.php?companyid='+companyid+'&batchid='+batchid+'&action='+action, 'Details View', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../');
	}

	function search_populate(str)
	{
		if(str==1)
		{
			document.getElementById('search_by_th_up').innerHTML="Batch Date";
			$('#search_by_th_up').css('color','blue');
		}
		else if(str==2)
		{
			document.getElementById('search_by_th_up').innerHTML="Recipe Date";
			$('#search_by_th_up').css('color','blue');
		}
		else if(str==4)
		{
			document.getElementById('search_by_th_up').innerHTML="Issue Date";
			$('#search_by_th_up').css('color','blue');
		}
		else if(str==5)
		{
			document.getElementById('search_by_th_up').innerHTML="Dyeing Date";
			$('#search_by_th_up').css('color','blue');
		}
		else
		{
			document.getElementById('search_by_th_up').innerHTML="Requisition Date";
			$('#search_by_th_up').css('color','blue');
		}
	}
	
	
</script>
</head>
<body onLoad="set_hotkey();">
<form id="batchRecipeReqReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1200px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1200px;">
            <table class="rpt_table" width="1200px" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>
                    <th width="70">Batch Type</th>
                    <th width="120" class="must_entry_caption">Company </th>
                    <th width="100">W. Company</th>
                    <th width="120">Buyer Name</th>
                    <th width="60">Job Year</th>
                    <th width="70">Job No</th>
                    <th width="70">Booking No</th>
                    <th width="70">Order No</th>
                    <th width="70">Batch No</th>
                    <th width="70">Recipe No</th>
                    <th width="70">Requisition No</th>
                    <th width="70">Search By</th>
                    <th width="130" colspan="2" id="search_by_th_up" class="must_entry_caption">Requisition Date</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('batchRecipeReqReport_1','report_container*report_container2','','','')" class="formbutton" style="width:50px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><? $batch_type_arr=array(1=>"Self Batch",2=>"SubCon Batch",3=>"Sample Batch");
                        	echo create_drop_down( "cbo_batch_type",70, $batch_type_arr,"",1, "--All--", 0,"",0 ); ?>
                        </td>
                        <td><? echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Select Company-", $selected, "load_drop_down( 'requires/batch_wise_recipe_req_monitoring_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                        <td><? echo create_drop_down("cbo_working_company", 100, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name", "id,company_name", 1, "-Working Company-", 0, ""); ?></td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "- All Buyer -", $selected, "",0,"" ); ?></td>
                        <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", "", "",0,"" ); //date("Y",time())?></td>
                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:60px" onDblClick="openmypage_job();" onChange="$('#txt_job_id').val('');" placeholder="Wr/Br Job" />
                            <input type="hidden" id="txt_job_id" name="txt_job_id"/>
                        </td>
                        <td>
                            <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:60px" placeholder="Wr/Br" onDblClick="openmypage_booking();"onChange="$('#txt_hide_booking_id').val('');" autocomplete="off">
                            <input type="hidden" name="txt_hide_booking_id" id="txt_hide_booking_id" readonly>
                        </td>
                        <td>
                            <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:60px" placeholder="Wr/Br" onDblClick="openmypage_order();" onChange="$('#hide_order_id').val('');" autocomplete="off">
                            <input type="hidden" name="hide_order_id" id="hide_order_id" readonly>
                        </td>
                        <td>
                            <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:60px" placeholder="Wr/Br" onDblClick="openmypage_batch();" onChange="$('#hide_batch_id').val('');" autocomplete="off">
                            <input type="hidden" name="hide_batch_id" id="hide_batch_id" readonly>
                        </td>
                        <td>
                            <input type="text" name="txt_recipe_no" id="txt_recipe_no" class="text_boxes" style="width:60px" placeholder="Wr/Br" onDblClick="openmypage_recipe();" onChange="$('#hide_recipe_id').val('');" autocomplete="off">
                            <input type="hidden" name="hide_recipe_id" id="hide_recipe_id" readonly>
                        </td>
                        <td>
                            <input type="text" name="txt_req_no" id="txt_req_no" class="text_boxes" style="width:60px" placeholder="Wr/Br" onDblClick="openmypage_requisition();" onChange="$('#hide_req_id').val('');" autocomplete="off">
                            <input type="hidden" name="hide_req_id" id="hide_req_id" readonly>
                        </td>
                        <td>
							<?  
                            $search_by = array(1=>'Batch',2=>'Recipe',3=>'Requisition',4=>'Chemical Issue',5=>'Dyeing Production'); $dd="search_populate(this.value)";
                            echo create_drop_down( "cbo_search_date", 70, $search_by,"",0, "--Select--", 3,$dd,0 );
                            ?>
                        </td>
                        <td><input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:55px" placeholder="From Date"/></td>
                        <td><input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:55px" placeholder="To Date"/></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated(1);" /></td>
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

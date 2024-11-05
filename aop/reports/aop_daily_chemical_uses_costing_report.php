<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Daily Chemical Uses and Costing Report.
Functionality	:	
JS Functions	:
Created by		:	Shakil Ahmed Setu
Creation date 	: 	05-09-2021
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
echo load_html_head_contents("AOP Batch Wise Dyes and Chemical Uses and Costing Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';

	function fn_report_generated(type)
	{
		if (form_validation('cbo_company_id','Comapny Name')==false)//*txt_date_from*txt_date_to----*From Date*To Date
		{
			return;
		}
		else
		{
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_within_group*cbo_buyer_id*txt_job_no*txt_order_no*cbo_work_order_type*txt_aop_ref*txt_batch_no*txt_batch_id*txt_buyer_po*txt_style_ref*txt_date_from*txt_date_to',"../../")+'&type='+type;
			//alert(data);
			freeze_window(3);
			http.open("POST","requires/aop_daily_chemical_uses_costing_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';


			for (var i = 1; i < reponse[2]; i++) 
			{
				setFilterGrid("table_body"+i,-1);
			}
		show_msg('3');
		release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#scroll_body tr:first').show();
	}
	
	function show_progress_report_details(action,order_id,width)
	{ 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/aop_daily_chemical_uses_costing_report_controller.php?action='+action+'&order_id='+order_id, 'Work Progress Report Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../');
		
	} 

	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}

	function openImageWindow(id)
	{
		var title = 'Image View';	
		var page_link = 'requires/aop_daily_chemical_uses_costing_report_controller.php?&action=image_view_popup&id='+id;
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
		}
	}
	

	function openmypage_batch()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_id').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_id').value;
		var job_no=document.getElementById('txt_job_no').value;
		var aop_ref=document.getElementById('txt_aop_ref').value;
		var page_link="requires/aop_daily_chemical_uses_costing_report_controller.php?action=batch_no_popup&company_id="+company_name+"&cbo_buyer_name="+cbo_buyer_name+"&aop_ref="+aop_ref+"&job_no="+job_no;
		txt_aop_ref
		var title="Batch Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			var batch=theemail.split("_");
			document.getElementById('txt_batch_id').value=batch[0];
			document.getElementById('txt_batch_no').value=batch[1];
			release_freezing();
		}
	}

	

	function openmypage_qty(issue_dtls_ids,action)
	{
		//alert(issue_dtls_ids);
		var type="1"
		var popup_width='';
		if(type=="1") popup_width='1150px'; else popup_width='890px';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/aop_daily_chemical_uses_costing_report_controller.php?issue_dtls_ids='+issue_dtls_ids+'&action='+action, 'Detail Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}

</script>
</head>
<body onLoad="set_hotkey();">
<form id="workProgressReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1420px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >
         <fieldset style="width:1420px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>
                    <th width="135" class="must_entry_caption">Company</th>
                    <th width="80">Within Group </th>
                    <th width="125">Party Name</th>
                    <th width="120">Aop Job No</th>
                    <th width="80">Work Order No</th>
                    <th width="100">Wo Type</th>
                    <th width="100">Aop Ref.</th>
                    <th width="80">Buyer PO</th>
                    <th width="80">Buyer Style</th>
                    <th width="80">Batch No.</th>
                    <th width="160">Date Range</th>
                    <th width=""><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                </thead>
                <tbody>
                    <tr >
                        <td> 
                        <? echo create_drop_down( "cbo_company_id", 135, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/aop_daily_chemical_uses_costing_report_controller', this.value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer', 'buyer_td' );"); ?>
                    	</td>
                    	<td>
							<?php echo create_drop_down( "cbo_within_group", 80, $yes_no,"", 1, "All", $selected, "load_drop_down( 'requires/aop_daily_chemical_uses_costing_report_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?>
						</td>
                        <td id="buyer_td">
                        	<? 
                        		echo create_drop_down( "cbo_buyer_id", 125, $blank_array,"", 1, "-- Select Party --", $selected, "" );
                        	?>
                    	</td>
                    	
                        <td>
                    		<input class="text_boxes"  type="text" name="txt_job_no" id="txt_job_no"  placeholder="Write" style="width:107px;"/>
                    		
                    	</td>
                    	<td>
                            <input name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:75px"  placeholder="Write" >
                            
                        </td>
                        <td>
                            <? echo create_drop_down( "cbo_work_order_type", 150, $aop_work_order_type,"", 1, "--Select--",$selected,"","","" ); ?>
                        </td>

						<td>
                            <input name="txt_aop_ref" id="txt_aop_ref" class="text_boxes" style="width:100px" placeholder="Write">
                           
                        </td>
                        <td>
                            <input name="txt_buyer_po" id="txt_buyer_po" class="text_boxes" style="width:75px" placeholder="Write" >
                        </td>
                        <td>
                            <input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:75px" placeholder="Write" >
                        </td>
                        <td>
                            <input name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:75px" placeholder="Browse" onDblClick="openmypage_batch();" >
                            <input type="hidden" name="txt_batch_id" id="txt_batch_id" class="text_boxes" style="width:70px">
                        </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" >&nbsp; To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date"  >
                        </td>
                        <td align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:90px" value="Show" onClick="fn_report_generated(1)" />
                           
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="12" align="center">
							<? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </tfoot>
           </table> 
           <br />
        </fieldset>
    </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

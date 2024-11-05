<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create PI Variance Report.
Functionality	:	
JS Functions	:
Created by		:	Nayem
Creation date 	: 	28-09-2021
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
echo load_html_head_contents("PI Variance Report", "../../", 1, 1,'',1,'');
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
 
	var permission = '<? echo $permission; ?>';
	
	function generate_report(rpt_type)
	{
		var txt_job_no=$("#txt_job_no").val();
		var txt_style_no=$("#txt_style_no").val();
		var txt_po_no=$("#txt_po_no").val();
		var cbo_item_category_id=$("#cbo_item_category_id").val();
		var txt_wo_no=$("#txt_wo_no").val();
		var txt_pi_no=$("#txt_pi_no").val();
				
		if(txt_job_no =="" && txt_style_no =="" && txt_po_no =="" && cbo_item_category_id =="" && txt_wo_no =="" && txt_pi_no =="")
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name**From Date*To Date')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_name','Company Name*Buyer*Job')==false)
			{
				return;
			}
		}

		var action='';
		if(rpt_type==1){
			action="report_generate";
		}
		
		var report_title=$( "div.form_caption" ).html();	
		var data="action="+action+get_submitted_data_string('cbo_company_name*cbo_buyer_id*txt_job_no*txt_job_id*txt_style_no*txt_po_no*txt_po_id*cbo_item_category_id*txt_wo_no*txt_wo_id*txt_pi_no*txt_pi_id*cbo_date_type*txt_date_from*txt_date_to*cbo_approval_status',"../../")+'&report_title='+report_title;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/pi_variance_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("****");
			$('#report_container2').html(response[0]);

			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';			
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');// media="print"
		d.close();
	}
	
	function change_date_caption(id)
	{
		if (id==1) { $("#dynamic_caption").html("PI Date"); }
		else if (id==2) { $("#dynamic_caption").html("Work Order Date"); }
	}
		
	function fn_req_wo(str)
	{
		var txt_wo_po_no=$('#txt_wo_no').val();
		var txt_pi_no=$('#txt_pi_no').val();
        if(str==2 && txt_wo_po_no)
		{
			$('#txt_wo_no').attr("disabled",false);
			$('#txt_pi_no').val("").attr("disabled",true);	
		}
		else  if(str==3 && txt_pi_no)
		{
			$('#txt_wo_po_no').val("").attr("disabled",true);
			$('#txt_pi_no').attr("disabled",false);
		}
		else
		{	
			$('#txt_wo_no').val("").attr("disabled",false);
			$('#txt_pi_no').val("").attr("disabled",false);
			$('#txt_date_from').val("").attr("disabled",false);
			$('#txt_date_to').val("").attr("disabled",false);
		}
	}

	function openmypage_wo()
	{
		if( form_validation('cbo_company_name','Company Name')==false ){
			return;
		}

		var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('cbo_item_category_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/pi_variance_report_controller.php?action=wo_no_popup&data='+data,'WO No Popup', 'width=430px,height=420px,center=1,resize=0','../');
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("hdn_wo_info");
			if (theemail.value!="")
			{
				freeze_window(5);
                var response=theemail.value.split('_');
			    document.getElementById("txt_wo_id").value=response[0];
				document.getElementById("txt_wo_no").value=response[1];
                disable_enable_fields('txt_date_from*txt_date_to*txt_wo_no',1);
				release_freezing();
			}
		}
	}

	function openmypage_pi()
	{
		if( form_validation('cbo_company_name','Company Name')==false ){
			return;
		}
		var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_item_category_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/pi_variance_report_controller.php?action=pi_no_popup&data='+data,'PI No Popup', 'width=430px,height=420px,center=1,resize=0','../');

		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("hdn_pi_info");
			if (theemail.value!="")
			{
				freeze_window(5);
                var response=theemail.value.split('_');
			    document.getElementById("txt_pi_id").value=response[0];
				document.getElementById("txt_pi_no").value=response[1];
                disable_enable_fields('txt_date_from*txt_date_to*txt_wo_no',1);
				release_freezing();
			}
		}
	}

	function openmypage_pi_qty_hyfer_link(txt_job_no,txt_po_no,item_group,txt_style_no,item_catagory,action)
	{  //alert(type)
		var companyID = $("#cbo_company_name").val();
		var popup_width='1610px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/pi_variance_report_controller.php?companyID='+companyID+'&txt_job_no='+txt_job_no+'&txt_po_no='+txt_po_no+'&item_group='+item_group+'&txt_style_no='+txt_style_no+'&item_catagory='+item_catagory+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}

    function openmypage_job()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_job_no').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/pi_variance_report_controller.php?action=job_no_popup&data='+data,'Order No Popup', 'width=630px,height=380px,center=1,resize=0','../')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("hdn_job_info");
			if (theemail.value!="")
			{
				freeze_window(5);
                var response=theemail.value.split('_');
				document.getElementById("txt_job_id").value=response[0];
			    document.getElementById("txt_job_no").value=response[1];
			    document.getElementById("txt_style_no").value=response[2];
                disable_enable_fields('txt_job_no*txt_style_no',1);
				release_freezing();
			}
		}
	}

    function openmypage_po()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_job_no').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/pi_variance_report_controller.php?action=po_no_popup&data='+data,'Order No Popup', 'width=630px,height=380px,center=1,resize=0','../')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("hdn_po_info");
			if (theemail.value!="")
			{
				freeze_window(5);
                var response=theemail.value.split('_');
				document.getElementById("txt_job_id").value=response[0];
			    document.getElementById("txt_job_no").value=response[1];
			    document.getElementById("txt_style_no").value=response[2];
			    document.getElementById("txt_po_no").value=response[3];
			    document.getElementById("txt_po_id").value=response[4];
			    document.getElementById("cbo_buyer_id").value=response[5];
                disable_enable_fields('txt_job_no*txt_style_no*cbo_buyer_id',1);
				release_freezing();
			}
		}
	}
	
	function fnc_chk_category(id)
	{
		$('#txt_req_no').val("").attr("disabled",false);
		$('#txt_pi_no').val("").attr("disabled",false);
	}

</script>
</head>

<body onLoad="set_hotkey();">
<form name="piVarianceReport">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
        <h3 align="left" id="accordion_h1" style="width:1300px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <fieldset style="width:1300px;">
                <table class="rpt_table" width="1290" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                   		<tr>                    
                            <th width="130" class="must_entry_caption">Company Name</th>
                            <th width="130">Buyer</th>
                            <th width="90">Job</th>
                            <th width="90" >Style Ref.</th>
                            <th width="90">PO NO</th>
                            <th width="130">Item Category</th>
                            <th width="90">WO No</th>
                            <th width="90">PI No</th>
                            <th width="90" class="must_entry_caption">Data Type</th>
                            <th width="170" class="must_entry_caption" id="dynamic_caption">PI Date</th>
							<th width="90">Approval Status</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('piVarianceReport','report_container*report_container2','','','disable_enable_fields(\'txt_job_no*txt_style_no\',0);')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                        <tr class="general">
                            <td align="center"> 
								<?
                                    echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/pi_variance_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                                ?>
                            </td>
                            <td align="center" id="buyer_td">
                            	<? 
                            		echo create_drop_down( "cbo_buyer_id", 130, $blank_array,"", 1,"-- Select --",0,"" );
                            	?>
                            </td>
                            <td align="center">
                            	<input type="text" name="txt_job_no" id="txt_job_no" value="" class="text_boxes" style="width:80px" placeholder="Write/Browse" onDblClick="openmypage_job();" />
                            	<input type="hidden" name="txt_job_id" id="txt_job_id">
                            </td>
                            <td align="center">
                            	<input type="text" name="txt_style_no" id="txt_style_no" value="" class="text_boxes" style="width:80px" placeholder="Write/Browse" onDblClick="openmypage_job();"/>
                            </td>
                            <td align="center">
                            	<input type="text" name="txt_po_no" id="txt_po_no" value="" class="text_boxes" style="width:80px" placeholder="Write/Browse" onDblClick="openmypage_po();"/>
                            	<input type="hidden" name="txt_po_id" id="txt_po_id">
                            </td>
                            <td align="center">
                            	<? 
                            		echo create_drop_down( "cbo_item_category_id", 130, $item_category,'', 1, '-- Select --',0,"fnc_chk_category(this.value);",0,'1,2,3,4,24,25'); 
                            	?>
                            </td>
                            <td align="center">
                            	<input type="text" name="txt_wo_no" id="txt_wo_no" value="" class="text_boxes" style="width:80px" placeholder="Write/Browse" onDblClick="openmypage_wo();" onBlur="fn_req_wo(2);"/>
                            	<input type="hidden" name="txt_wo_id" id="txt_wo_id">
                            </td>
                            <td align="center">
                            	<input type="text" name="txt_pi_no" id="txt_pi_no" value="" class="text_boxes" style="width:80px" placeholder="Write/Browse" onDblClick="openmypage_pi();" onBlur="fn_req_wo(3);" />
                            	<input type="hidden" name="txt_pi" id="txt_pi">
                            	<input type="hidden" name="txt_pi_id" id="txt_pi_id">
                            </td>
                            <td>
                            	<?
									$date_type_arr=array(1=>"PI",2=>"Work Order");
									echo create_drop_down( "cbo_date_type", 80, $date_type_arr,"", 0, "--Select Date--", 1,"change_date_caption(this.value)" );
                            	?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:60px" placeholder="From Date" readonly />
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:60px" placeholder="To Date" readonly />
                            </td>
							<td>
								<?
									echo create_drop_down( "cbo_approval_status", 80, $yes_no,"", 1, "--Select--", 0,"" );
								?>
							</td>
                            <td>
                            	<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                            </td>
							
                        </tr>
                        <tr>
                        	<td colspan="11" align="center"><? echo load_month_buttons(1); ?></td>							
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
    
    <div style="margin-top:10px" id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
<script>
	set_multiselect('cbo_item_category_id','0','0','0','0');	
</script> 
</html>

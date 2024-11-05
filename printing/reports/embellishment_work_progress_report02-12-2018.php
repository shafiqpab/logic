<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Party Wise Grey Stock Report.
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam Reza 
Creation date 	: 	07-11-2018
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
echo load_html_head_contents("Embellishment Work Progress Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
			
	function fn_report_generated()
	{
		if (form_validation('cbo_company_id*txt_date_from*txt_date_to','Comapny Name*Date From*Date To')==false)
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_within_group*cbo_party_id*cbo_party_location_id*cbo_year*txt_job_no*txt_wo_order_no*cbo_buyer_id*txt_buyer_po*txt_buyer_po_id*txt_style_ref*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		
		
		freeze_window(3);
		http.open("POST","requires/embellishment_work_progress_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**"); 
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			//append_report_checkbox('table_header_1',1);
			//setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
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
	
	//----------------
	function job_search_popup(page_link,title)
	{
		if ( form_validation('cbo_company_id*cbo_party_id','Company Name*Party Name')==false )
		{
			return;
		}
		else
		{
			var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_party_id').value+"_"+document.getElementById('cbo_within_group').value;
			page_link='requires/embellishment_work_progress_report_controller.php?action=job_popup&data='+data
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				freeze_window(5);
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("selected_order").value;
				$("#txt_job_no").val( theemail );
				
				var list_view_orders = return_global_ajax_value( 0+'**'+theemail+'**'+1, 'load_php_dtls_form', '', 'requires/emb_order_details_report_controller');
				if(list_view_orders!='')
				{
					$("#rec_issue_table tr").remove();
					$("#rec_issue_table").append(list_view_orders);
				}
				$('#cbo_company_name').attr('disabled','disabled');
				$('#cbo_party_name').attr('disabled','disabled');
				release_freezing();
			}
		}
	}
	
	function order_search_popup()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_party_id').value+"_"+document.getElementById('cbo_within_group').value+"_"+document.getElementById('cbo_buyer_id').value+"_"+document.getElementById('txt_job_no').value+"_"+document.getElementById('cbo_year').value;
		
		var page_link="requires/embellishment_work_progress_report_controller.php?action=order_no_popup&data="+data;
		var title="Order Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			var job=theemail.split("_");
			document.getElementById('txt_buyer_po_id').value=job[0];
			document.getElementById('txt_buyer_po').value=job[1];
			release_freezing();
		}
	}
	
	
	function openmypage_style()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		
		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_party_id').value+"_"+document.getElementById('cbo_within_group').value+"_"+document.getElementById('cbo_buyer_id').value+"_"+document.getElementById('txt_job_no').value+"_"+document.getElementById('cbo_year').value;
		
		
		
		var page_link="requires/embellishment_work_progress_report_controller.php?action=style_no_popup&data="+data;
		var title="Style Ref.";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			//var job=theemail.split("_");
			document.getElementById('txt_style_ref').value=theemail;
			release_freezing();
		}
	}
	

	

	
	
	

</script>
</head>
<body onLoad="set_hotkey();">
<form>
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1450px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1450px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="140" class="must_entry_caption">Company</th>
                    <th width="125">Location</th>
                    <th width="85">Within Group </th>
                    <th width="130">Party </th>
                    <th width="105">Party Location</th>
                    <th width="65">Year</th>                     
                    <th width="70">Job No</th>
                    <th width="90">Work Order</th>
                    <th width="90">Buyer</th>
                    <th width="90">Buyer PO</th>
                    <th width="100">Style Ref.</th>
                    <th width="70">Status</th>
                    <th width="160">Order Rcvd Date</th>
                    <th width=""><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                </thead>
                <tbody>
                    <tr >
                        <td> 
                        <? echo create_drop_down( "cbo_company_id", 135, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/embellishment_work_progress_report_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/embellishment_work_progress_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );"); ?>
                    	</td>
                        
                        <td id="location_td">
							<? 
								echo create_drop_down( "cbo_location_id", 120, $blank_array,"", 1, "-- Select Location--", $selected, "",1,"" );
                            ?>
                        </td>
                        
                        
                        
                    	<td>
							<?php echo create_drop_down( "cbo_within_group", 80, $yes_no,"", 0, "--  --", 0, "load_drop_down( 'requires/embellishment_work_progress_report_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_party', 'party_td' );$('#cbo_party_location_id').val(0);$('#cbo_party_location_id').prop('disabled','disabled');if(this.value==2){ $('#txt_buyer_po').prop('disabled','disabled');$('#txt_style_ref').prop('disabled','disabled');}else{ $('#txt_buyer_po').prop('disabled','');$('#txt_style_ref').prop('disabled','');}" ); ?>
						</td>
                        <td id="party_td">
                        	<? 
                        		echo create_drop_down( "cbo_party_id", 135, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, ""); 
							?>
                    	</td>
                        
                        <td id="party_location_td">
							<? 
								echo create_drop_down( "cbo_party_location_id", 100, $blank_array,"", 1, "-- Select Location--", $selected, "",1,"" );
                            ?>
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_year", 60, $year,"", 1, "--Select Year--",date("Y"), "",0 );
                            ?>
                        </td>
                        <td>
                    		<input class="text_boxes"  type="text" name="txt_job_no" id="txt_job_no" onDblClick="job_search_popup();" placeholder="Double Click" style="width:55px;" readonly/>
                    	</td>
                        
                        <td>
                            <input name="txt_wo_order_no" id="txt_wo_order_no" class="text_boxes" style="width:75px"  placeholder="Write">
                        </td>
                        
                        <td id="buyer_td">
                        	<? 
                        		echo create_drop_down( "cbo_buyer_id", 125, $blank_array,"", 1, "-- Select Party --", $selected, "" );
                        	?>
                    	</td>
                        
                        <td>
                            <input name="txt_buyer_po" id="txt_buyer_po" class="text_boxes" style="width:75px"  placeholder="Wr/Br Order" onDblClick="order_search_popup();" >
                            <input type="hidden" name="txt_buyer_po_id" id="txt_buyer_po_id" class="text_boxes" style="width:70px">
                        </td>
                        
                        <td>
                            <input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:75px" placeholder="Wr/Br Style" onDblClick="openmypage_style();" >
                        </td>
                        <td></td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" >&nbsp; To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date"  >
                        </td>
                        <td align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1)" />
                           
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="14" align="center">
							<? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </tfoot>
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

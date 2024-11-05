<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Bundle Status Report.
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam Reza 
Creation date 	: 	16-05-2016
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
echo load_html_head_contents("Bundle Status Report","../../", 1, 1, $unicode,1,1);
?>	
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	
	

	
	
	function fnc_report_generated()
	{
		
		if($('#txt_job_no').val()!='' || $('#txt_order_no').val()!='' || $('#txt_internal_ref_no').val()!='' || $('#txt_date_from').val()!=''){
			validation='cbo_company_id';	
			msg='Company Name';	
		}
		else
		{
			validation='cbo_company_id*cbo_buyer_name*txt_date_from*txt_date_to';	
			msg='Company Name*Buyer Name*From Date*End Date';	
		}
		
		if(form_validation(validation,msg)==false)
		{
			return;
		}
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_name*txt_date_from*txt_date_to*cbo_job_year_id*txt_job_no*txt_order_no*cbo_location_id*txt_internal_ref_no*cbo_floor_group*cbo_ship_status*cbo_date_category*cbo_agent',"../../")+'&report_title='+report_title;
			freeze_window(3);
			http.open("POST","requires/bundle_status_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
	
	}
		
		
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);
			
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("tbl_list_summary",-1,'');
			var tableFilters = 
			 {
				col_38: "none",
				col_operation: {
				id: ["tot_order_qty","tot_plan_cut_qty","tot_number_of_cut","tot_fin_qty","tot_rej_qty","tot_ship_out"],
				col: [11,13,14,35,36,37],
				// col: [12,14,15,36,37,38],
				operation: ["sum","sum","sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
				}
			 }
			setFilterGrid("tbl_list_search",-1,tableFilters);
	 		show_msg('3');
			release_freezing();
		}
	}

	
	
	function fn_popup(type,cutting,sewing_out,iron,emb,wash,sp_work,printed,job_info,bundle)
	{
		//large bundle_id data for this reason I used http request;
		var data="action=set_session_data&bundle_id="+cutting;
		http.open("POST","requires/bundle_status_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function fn_report_generated_reponse(){
			if(http.readyState == 4) 
			{
		
				if(type==1){
					var page_link='requires/bundle_status_report_controller.php?action=reject_popup&cutting='+cutting+'&sewing_out='+sewing_out+'&iron='+iron+'&emb='+emb+'&wash='+wash+'&sp_work='+sp_work+'&printed='+printed+'&job_info='+job_info+'&bundle='+bundle;
					var title='Bundle Reject Info';
					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=0,scrolling=0','../');
				}
				else if(type==2){
					var page_link='requires/bundle_status_report_controller.php?action=cutting_dtls_popup&job_info='+job_info+'&bundle='+bundle;
					var title='Bundle Info';
					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=560px,height=370px,center=1,resize=0,scrolling=0','../');
				}
				else if(type==3){
					var page_link='requires/bundle_status_report_controller.php?action=cutting_qc_dtls_popup&job_info='+job_info+'&bundle='+bundle;
					var title='Cutting QC Info';
					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=660px,height=370px,center=1,resize=0,scrolling=0','../');
				}
				else if(type==4){
					var page_link='requires/bundle_status_report_controller.php?action=cutting_blance_popup&job_info='+job_info+'&bundle='+bundle;
					var title='Cutting Balance Info';
					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=560px,height=370px,center=1,resize=0,scrolling=0','../');
				}
				else if(type==5){
					var page_link='requires/bundle_status_report_controller.php?action=print_issue_popup&job_info='+job_info+'&bundle='+bundle;
					var title='Print Issue Info';
					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=660px,height=370px,center=1,resize=0,scrolling=0','../');
				}
				else if(type==6){
					var page_link='requires/bundle_status_report_controller.php?action=print_rec_popup&job_info='+job_info+'&bundle='+bundle;
					var title='Print Receive Info';
					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=660px,height=370px,center=1,resize=0,scrolling=0','../');
				}
				else if(type==7){
					var page_link='requires/bundle_status_report_controller.php?action=print_blance_popup&job_info='+job_info+'&bundle='+bundle;
					var title='Print Balance Info';
					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=660px,height=370px,center=1,resize=0,scrolling=0','../');
				}
				
				else if(type==8){
					var page_link='requires/bundle_status_report_controller.php?action=sewing_in_popup&job_info='+job_info+'&bundle='+bundle;
					var title='Sewing Output Info';
					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=760px,height=370px,center=1,resize=0,scrolling=0','../');
				}
				
				else if(type==9){
					var page_link='requires/bundle_status_report_controller.php?action=sewing_out_popup&job_info='+job_info+'&bundle='+bundle;
					var title='Sewing Input Info';
					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=760px,height=370px,center=1,resize=0,scrolling=0','../');
				}
				else if(type==19){
					var page_link='requires/bundle_status_report_controller.php?action=sewing_blance&job_info='+job_info+'&bundle='+bundle;
					var title='Sewing Blance Info';
					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=760px,height=370px,center=1,resize=0,scrolling=0','../');
				}
				else if(type==10){
					var page_link='requires/bundle_status_report_controller.php?action=embroidery_issue_popup&job_info='+job_info+'&bundle='+bundle;
					var title='Embroidery Issue Info';
					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=700px,height=370px,center=1,resize=0,scrolling=0','../');
				}
				
				else if(type==11){
					var page_link='requires/bundle_status_report_controller.php?action=embroidery_rec_popup&job_info='+job_info+'&bundle='+bundle;
					var title='Embroidery Receive Info';
					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=700px,height=370px,center=1,resize=0,scrolling=0','../');
				}
				
				else if(type==12){
					var page_link='requires/bundle_status_report_controller.php?action=embroidery_blance_popup&job_info='+job_info+'&bundle='+bundle;
					var title='Embroidery Blance Info';
					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=700px,height=370px,center=1,resize=0,scrolling=0','../');
				}
				else if(type==13){
					var page_link='requires/bundle_status_report_controller.php?action=spacial_work_issue_popup&job_info='+job_info+'&bundle='+bundle;
					var title='Spacial Work Issue Info';
					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=760px,height=370px,center=1,resize=0,scrolling=0','../');
				}
				
				else if(type==14){
					var page_link='requires/bundle_status_report_controller.php?action=spacial_work_rec_popup&job_info='+job_info+'&bundle='+bundle;
					var title='Spacial Work Receive Info';
					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=760px,height=370px,center=1,resize=0,scrolling=0','../');
				}
				
				else if(type==15){
					var page_link='requires/bundle_status_report_controller.php?action=spacial_work_blance_popup&job_info='+job_info+'&bundle='+bundle;
					var title='Spacial Work Blance Info';
					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=760px,height=370px,center=1,resize=0,scrolling=0','../');
				}
				else if(type==16){
					var page_link='requires/bundle_status_report_controller.php?action=wash_issue_popup&job_info='+job_info+'&bundle='+bundle;
					var title='Wash Work Issue Info';
					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=760px,height=370px,center=1,resize=0,scrolling=0','../');
				}
				
				else if(type==17){
					var page_link='requires/bundle_status_report_controller.php?action=wash_rec_popup&job_info='+job_info+'&bundle='+bundle;
					var title='Wash Work Receive Info';
					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=760px,height=370px,center=1,resize=0,scrolling=0','../');
				}
				
				else if(type==18){
					var page_link='requires/bundle_status_report_controller.php?action=wash_blance_popup&job_info='+job_info+'&bundle='+bundle;
					var title='Wash Work Blance Info';
					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=760px,height=370px,center=1,resize=0,scrolling=0','../');
				}
				else if(type==20){
					var page_link='requires/bundle_status_report_controller.php?action=iron_input_popup&job_info='+job_info+'&bundle='+bundle;
					var title='Iron Input Info';
					emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=760px,height=370px,center=1,resize=0,scrolling=0','../');
				}
				
				emailwindow.onclose=function()
				{
				}

			}
			
		}
		
		
	}
	
	//=====================================================
	
	function openmypage_booking()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value;
		//alert (data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/bundle_status_report_controller.php?action=booking_no_popup&data='+data,'Order No Popup', 'width=630px,height=420px,center=1,resize=0','../')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("txt_wo_id");
			var theemailv=this.contentDoc.getElementById("txt_wo_no");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_booking_id").value=theemail.value;
			    document.getElementById("txt_booking_no").value=theemailv.value;
				release_freezing();
			}
		}
	}
	function openmypage_job()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value;
		//alert (data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/bundle_status_report_controller.php?action=job_search_popup&data='+data,'Job No Popup', 'width=630px,height=420px,center=1,resize=0')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("txt_selected_no");
			
			// var theemailv=this.contentDoc.getElementById("txt_wo_no");
			var response=theemail.value.split('_');
			//alert(response.join('**'));return;
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_job_no").value=response[1];
				release_freezing();
			}
		}
	}
	
	
	function new_window()
	{
		document.getElementById('scroll_body_summary').style.overflow="auto";
		document.getElementById('scroll_body_summary').style.maxHeight="none";
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#scroll_body_summary tr:first').hide();
		$('#scroll_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body_summary').style.overflowY="scroll";
		document.getElementById('scroll_body_summary').style.maxHeight="380px";
		$('#scroll_body_summary tr:first').show();
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#scroll_body tr:first').show();
		
		
		//document.getElementById('scroll_body').style.maxWidth="120px";
	}
	
	
</script>
</head>
 
<body onLoad="set_hotkey();">
		 
<form id="date_wise_yarn_allocation">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:1467px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:1420px;">
                <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                	<thead>
                    	<tr>                   
                            <th class="must_entry_caption">Company Name</th>
                            <th>Location</th>
                            <th>Floor Group</th>
                            <th class="must_entry_caption">Buyer Name</th>
                            <th>Agent</th>
                            <th>Job Year</th>
                            <th>Job No</th>
                            <th>Order No</th>
							<th>Internal Ref</th>
                            <th>Ship Status</th>
                            <th>Date Category</th>
                            <th colspan="3" class="must_entry_caption">Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td> 
                           <?
								echo create_drop_down( "cbo_company_id", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down('requires/bundle_status_report_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/bundle_status_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/bundle_status_report_controller', this.value, 'load_drop_down_agent', 'agent_td' );" );
                            ?>
                        </td>
                       
                          <td id="location_td">
							<? 
								echo create_drop_down( "cbo_location_id", 120, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                            ?>
                        </td>
						<td id="group_td">
							<?
								$arr=array();
								echo create_drop_down("cbo_floor_group", 130, $arr, "", 1, "-- Select Group --", 0, "", 1);
							?>
                        </td>
                          <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                          <td id="agent_td">
                            <? 
                                echo create_drop_down( "cbo_agent", 120, $blank_array,"", 1, "-- All agent --", $selected, "",0,"" );
                            ?>
                        </td>
						<td>
							<? 
								$selected_year=date("Y");
								echo create_drop_down( "cbo_job_year_id", 60, $year,"", 1, "--All--", 0, "",0,"","" );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" placeholder="Write/Browse" 
							onDblClick="openmypage_job();" style="width:80px" />
                        </td>
                        <td>
                      		 <input type="text" id="txt_order_no" name="txt_order_no" class="text_boxes" style="width:80px" />
                        </td>
						<td>
                            <input type="text" name="txt_internal_ref_no" id="txt_internal_ref_no" class="text_boxes" style="width:130px" placeholder="Write" autocomplete="off" >
                        </td>
                        <td>
							<? 
								echo create_drop_down( "cbo_ship_status", 100, $shipment_status,"", 1, "--All--", "", "",0,"1,2,3","" );
                            ?>
                        </td>
                        <td>
							<? 
								$date_type_arr=array(1=>'Ship Date',2=>'Country Ship Date',3=>'Cut Plan Date',4=>'PO Insert Date');
								echo create_drop_down( "cbo_date_category", 100, $date_type_arr,"", 1, "--All--", 1, "",0,"","" );
                            ?>
                        </td>
                       
                       
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" ></td>
                        <td>To</td>
                        <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" ></td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fnc_report_generated()" />
                        </td>
                    </tr>
                    </tbody>
                </table>
                <table>
                    <tr>
                        <td>
                            <? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </table> 
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div align="center" id="report_container2"></div>
   
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

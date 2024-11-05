<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Knitting Program Wise Grey Fab Report.
Functionality	:	
JS Functions	:
Created by		:	Aziz
Creation date 	: 	22-11-2014
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
echo load_html_head_contents("Batch Progress Report", "../../", 1, 1,'',1,1);
?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';
var tableFilters = 
			 {
				col_0: "none",
				col_operation: {
					id: ["total_trims_weight_gt","total_batch_qty","total_finish_qty","total_delivery_qty","total_balance_qty"],
			   //col: [14,26,27],
			   col: [19,20,31,34,35],
			   operation: ["sum","sum","sum","sum","sum"],
			   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
				}	
			}
			var tableFilters2 = 
	 {
		col_0: "none",
		col_operation: {
			id: ["total_trims_weight_gt2","total_batch_qty2","total_finish_qty2","total_delivery_qty2","total_balance_qty2"],
			//col: [14,26,27],
			col: [19,20,31,34,35],
			operation: ["sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}	
	}
	var tableFilters3 = 
	 {
		col_0: "none",
		col_operation: {
			id: ["total_trims_weight_gt3","total_batch_qty3","total_finish_qty3","total_delivery_qty3","total_balance_qty3"],
			//col: [14,26,27],
			col: [19,20,31,34,35],
			operation: ["sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}	
	}
function fn_report_generated(type)
{
	
	var job_no=document.getElementById('txt_job_no').value;
	var order_no=document.getElementById('txt_order_no').value;
	var batch_no=document.getElementById('txt_batch_no').value;
	var txt_file_no=document.getElementById('txt_file_no').value;
	var working_company_id=document.getElementById('cbo_working_company').value;
	var company_name=document.getElementById('cbo_company_name').value;
	var booking_no=document.getElementById('txt_booking_no').value;
	var floor_no=document.getElementById('cbo_floor').value;
	var txt_ref_no=document.getElementById('txt_ref_no').value;
	var txt_date_from=document.getElementById('txt_date_from').value;
	var txt_date_to=document.getElementById('txt_date_to').value;
	
	if(job_no!=""  || order_no!="" || batch_no!="" || booking_no!="")
	{
		/*if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}*/
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
		else if (txt_ref_no=='' && txt_date_from=='') 
		{
			if( form_validation('txt_ref_no*txt_date_from*txt_date_to','Ref. No*Form Date*To Date')==false )
			{
				return;
			}
		}
		
	}
	
	
	var report_title=$( "div.form_caption" ).html();
	var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*txt_job_id*txt_order_no*txt_file_no*txt_ref_no*hide_order_id*txt_date_from*txt_date_to*cbo_year*txt_batch_no*cbo_search_date*txt_hide_booking_id*txt_booking_no*cbo_working_company*cbo_batch_type*cbo_floor',"../../")+'&report_title='+report_title;
	freeze_window(3);
	http.open("POST","requires/batch_progress_report_controller.php",true);
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
		var path = '../../';
		document.getElementById('report_container').innerHTML=report_convert_button(path); 
		//document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+reponse[2]+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
		 var batch_type = document.getElementById('cbo_batch_type').value;
		
		if (batch_type == 1)
        {
		setFilterGrid("table_body",-1,tableFilters);
		}
		else  if (batch_type == 2)
        {
			setFilterGrid("table_body2",-1,tableFilters2);
		}
		else  if (batch_type == 3)
        {
			setFilterGrid("table_body3",-1,tableFilters3);
		}
		else  if (batch_type == 4)
        {
			setFilterGrid("table_body4",-1,tableFilters3);
		}
		else
		{
			setFilterGrid("table_body",-1,tableFilters);
			setFilterGrid("table_body2",-1,tableFilters2);
			setFilterGrid("table_body3",-1,tableFilters3);
			setFilterGrid("table_body4",-1,tableFilters3);
		}


		append_report_checkbox('table_header_1',1);
		document.getElementById("check_uncheck_tr").style.display="table";
		if($("#check_uncheck").is(":checked")==false)
		{
			$("#check_uncheck").attr("checked","checked");
		}
		/*else
		{
			$("#check_uncheck").rmoveAttr("checked");
		}*/
		//setFilterGrid("tbl_list_search",-1,tableFilters);
		show_msg('3');
		release_freezing();
 	}
}

function fn_check_uncheck(){
	var lengths = $("[type=checkbox]").length;
	if($("#check_uncheck").is(":checked") != true){		
		for(var i=0; i<=lengths; i++){
			
			$("[type=checkbox]").prop('checked', false);
			$("[type=checkbox]").removeClass('rpt_check');
			$("[type=checkbox]").removeAttr('checked');
		}
	}else{
		$("[type=checkbox]").prop('checked', true);
		for(var i=0; i<=lengths; i++){
			
			$("[type=checkbox]").not("#check_uncheck").addClass('rpt_check');
			$("[type=checkbox]").attr('checked',"checked");
		}
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
		var page_link='requires/batch_progress_report_controller.php?action=order_no_search_popup&companyID='+companyID+'&buyer_name='+buyer_name;
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
		var page_link='requires/batch_progress_report_controller.php?action=batch_no_search_popup&companyID='+companyID+'&buyer_name='+buyer_name;
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
function openmypage_job()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year_id = $("#cbo_year").val();
		//var cbo_month_id = $("#cbo_month").val();
		//alert(cbo_year_id);
		var page_link='requires/batch_progress_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
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
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
	}
	
	function openmypage_receive(po_id,prog_no,booking_no,action)
	{ //alert(des_prod)
		var companyID = $("#cbo_company_name").val();
		var popup_width='580px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/batch_progress_report_controller.php?companyID='+companyID+'&po_id='+po_id+'&prog_no='+prog_no+'&booking_no='+booking_no+'&prog_no='+prog_no+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../');
	}
	function openmypage_issue(po_id,prog_no,booking_no,action)
	{ //alert(des_prod)
		var companyID = $("#cbo_company_name").val();
		var popup_width='580px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/batch_progress_report_controller.php?companyID='+companyID+'&po_id='+po_id+'&prog_no='+prog_no+'&booking_no='+booking_no+'&prog_no='+prog_no+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../');
	}
function js_set_value( str ) {
	toggle( document.getElementById( 'tr_' + str), '#FFF' );
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
			document.getElementById('search_by_th_up').innerHTML="Dyeing Date";
			$('#search_by_th_up').css('color','blue');
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
			var page_link='requires/batch_progress_report_controller.php?action=booking_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
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
</script>
</head>
<body onLoad="set_hotkey();">
<form id="knittingStatusReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1350px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1450px;">
             <table class="rpt_table" width="1450px" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th>Batch Type</th>
					<th class="must_entry_caption">Company </th>
					<th class="">W. Company</th>
                    <th>Floor No</th>
                    <th>Buyer Name</th>
                    <th>Year</th>
                    <th>Job No</th>
					<th>Booking No</th>
                    <th>Order No</th>
                    <th>File No</th>
                    <th>Ref. No</th>
                    <th>Batch No</th>
                     <th>Search By</th>
                    <th id="search_by_th_up" class="must_entry_caption">Batch Date</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('knittingStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:50px" /></th>
                </thead>
                <tbody>
                    <tr align="center">
					 <td>
                         <? 
                         $batch_type_arr=array(1=>"Self Batch",2=>"SubCon Batch",3=>"Sample Batch" ,4=>"Fabric Sales");
                          echo create_drop_down( "cbo_batch_type",70, $batch_type_arr,"",1, "--All--", 0,"",0 );
                          ?>
                       </td>
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "- Select Company -", $selected, "load_drop_down( 'requires/batch_progress_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
						  <td> 
                                    <?
                                        echo create_drop_down( "cbo_working_company", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '--Select Working--', 0,"load_drop_down('requires/batch_progress_report_controller',this.value, 'load_drop_down_floor', 'td_floor' );",'','','','','',3);
                                    ?>
                                </td>
                        <td id="td_floor">
									<?
									echo create_drop_down("cbo_floor", 100, $blank_array,"", 1, "-- Select Floor--", 0, "",0,"","","","");
									?></td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "- All Buyer -", $selected, "",0,"" );
                            ?>
                        </td>
                        <td>
                            <?
                              echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
							?>
                        </td>
                         <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:70px" onDblClick="openmypage_job();" placeholder="Write/Browse Job" />
                            <input type="hidden" id="txt_job_id" name="txt_job_id"/>
                    	</td>
							<td>
                                <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:70px" placeholder="Browse Or Write" onDblClick="openmypage_booking();"  autocomplete="off">
                                <input type="hidden" name="txt_hide_booking_id" id="txt_hide_booking_id" readonly>
                         </td>
                    	  <td>
                                <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:70px" placeholder="Browse Or Write" onDblClick="openmypage_order();" onChange="$('#hide_order_id').val('');" autocomplete="off">
                                <input type="hidden" name="hide_order_id" id="hide_order_id" readonly>
                            </td>
                            
                            <td>
                               <input type="text"  name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:60px;" placeholder="Write">  
                            </td>
                            <td>
                               <input type="text"  name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:60px;"  placeholder="Write">  
                            </td>
                            <td>
                                <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:70px" placeholder="Browse Or Write" onDblClick="openmypage_batch();" onChange="$('#hide_batch_id').val('');" autocomplete="off">
                                <input type="hidden" name="hide_batch_id" id="hide_batch_id" readonly>
                            </td>
							
                            <td width="" align="center">
                        	<?  
                            $search_by = array(1=>'Batch Date',2=>'Dyeing Date');
							$dd="search_populate(this.value)";
							echo create_drop_down( "cbo_search_date", 80, $search_by,"",0, "--Select--", $selected,$dd,0 );
                       		 ?>
                     	</td>
                       
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:55px" placeholder="From Date"/>
                            To
                            <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:55px" placeholder="To Date"/>
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated(1)" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="14" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
                        
                    </tr>
                    <tr id="check_uncheck_tr"  width="95%" style="display:none;">
                        <td colspan="14"><input type="checkbox" id="check_uncheck" name="check_uncheck" onClick="fn_check_uncheck()"/> <strong style="color:#176aaa; font-size:14px; font-weight:bold;">Check/Uncheck All</strong>
						</td>
						
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

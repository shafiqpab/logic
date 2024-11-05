<?
/*-------------------------------------------- Comments
Version                  :   V2
Purpose			         : 	This form will create  Shipment Schedule for Management Report
Functionality	         :	
JS Functions	         :
Created by		         :	Monzu 
Creation date 	         : 
Requirment Client        : 
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                   
DB Script                : 
Updated by 		         : 		
Update date		         : 		   
QC Performed BY	         :		
QC Date			         :	
Comments		         : From this version oracle conversion is start
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order Info","../../../", 1, 1, $unicode);
?>	

<script>
var permission='<? echo $permission; ?>';


	function fn_get_order_dtls(po_id)
	{

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/shipment_schedule_management_controller.php?action=po_break_down&data='+po_id,'PO Info', 'width=600px,height=350px,center=1,resize=0','../../')
		
		emailwindow.onclose=function()
		{
		}
	}

	function open_exfact_popup(po_id)
	{

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/shipment_schedule_management_controller.php?action=exfact_popup&data='+po_id,'PO Info', 'width=600px,height=350px,center=1,resize=0','../../')
		
		emailwindow.onclose=function()
		{
		}
	}




	function openmypage_img(path)
	{

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/shipment_schedule_management_controller.php?action=img_popup&data='+path,'Image Popup', 'width=300px,height=300px,center=1,resize=0','../../')
		
		emailwindow.onclose=function()
		{
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
		var page_link='requires/shipment_schedule_management_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_job_no').val(job_no);
			$('#txt_job_id').val(job_id);
		}
	}

	function openmypage_order()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/shipment_schedule_management_controller.php?action=order_no_popup&data='+data,'Order No Popup', 'width=650px,height=420px,center=1,resize=0','../../')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("order_no_id");
			var theemailv=this.contentDoc.getElementById("order_no_val");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_order_id").value=theemail.value;
			    document.getElementById("txt_order_no").value=theemailv.value;
				release_freezing();
			}
		}
	}






 	var tableFilters = {
		ex_function:{fn_name:generate_report1}
	}	
	function generate_report_main(e)
	{
			if (e!=13) {var unicode=e.keyCode? e.keyCode : e.charCode } else {unicode=13;}
			
			var inn=document.getElementById('fillter_check').value;
			if(inn=='')
			{
				generate_report('report_container2',1)
			}
			if(inn==1)
			{
				show_inner_filter(unicode);
			}
	}
		
	function generate_report(div,stype)
	{
			
			if(document.getElementById('txt_job_no').value=='' && document.getElementById('txt_order_no').value==''){
				if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false )
				{
					return;
				}
			}
			
			var txt_date_from=document.getElementById('txt_date_from').value;
			var txt_date_to=document.getElementById('txt_date_to').value;
			if (stype==1) // main call
	        {
			//alert(stype);
				document.getElementById(div).innerHTML="";
				var cbo_company_name=document.getElementById('cbo_company_name').value;
				var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
				var cbo_order_status=2;
				var cbo_team_name=document.getElementById('cbo_team_name').value;
				var cbo_team_member=document.getElementById('cbo_team_member').value;
				var cbo_category_by=document.getElementById('cbo_category_by').value;
				var cbo_order_status=document.getElementById('cbo_order_status').value;
				var txt_job_no=document.getElementById('txt_job_no').value;
				var txt_job_id=document.getElementById('txt_job_id').value;
				var txt_order_no=document.getElementById('txt_order_no').value;
				var txt_order_id=document.getElementById('txt_order_id').value;
				var data=cbo_company_name+"_"+cbo_buyer_name+"_"+txt_date_from+"_"+txt_date_to+"_"+cbo_order_status+"_"+cbo_team_name+"_"+cbo_team_member+"_"+cbo_category_by+"______"+"_"+cbo_order_status+"_"+txt_job_no+"_"+txt_job_id+"_"+txt_order_no+"_"+txt_order_id;
			}
			
			
			
			if (window.XMLHttpRequest)
			{// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp=new XMLHttpRequest();
			}
			else
			{// code for IE6, IE5
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			
			freeze_window();
			xmlhttp.onreadystatechange=function()
			{
				if (xmlhttp.readyState==4 && xmlhttp.status==200)
				{
					release_freezing();
					var response=(xmlhttp.responseText).split('####');	
					document.getElementById(div).innerHTML=response[0];
					//document.getElementById('report_container').innerHTML=report_convert_button('../../../'+'response[1]/'); 

					document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		
					append_report_checkbox('table_header_1',1);
					setFilterGrid("table_body",-1,tableFilters);
					
					document.getElementById('content_summary3_panel').innerHTML=document.getElementById('shipment_performance').innerHTML
					percent_set();
				
				
				}
			}
			
			xmlhttp.open("GET","requires/shipment_schedule_management_controller.php?data="+data+"&type=report_generate",true);
			xmlhttp.send();
			
	}
	
	function generate_report1()
	{
			var stype=1;
			var myColValues=TF_GetColValues("table_body",2);
			myColValues="'"+myColValues.join()+"'";
			//alert(myColValues);
			var txt_date_from=document.getElementById('txt_date_from').value;
			var txt_date_to=document.getElementById('txt_date_to').value;
			if (stype==1) // main call
	        {
				document.getElementById('report_container2').innerHTML="";
				var cbo_company_name=document.getElementById('cbo_company_name').value;
				var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
				var cbo_order_status=2;
				var cbo_team_name=document.getElementById('cbo_team_name').value;
				var cbo_team_member=document.getElementById('cbo_team_member').value;
				var cbo_category_by=document.getElementById('cbo_category_by').value;
				var txt_job_no=document.getElementById('txt_job_no').value;
				var txt_job_id=document.getElementById('txt_job_id').value;
				var txt_order_no=document.getElementById('txt_order_no').value;
				var txt_order_id=document.getElementById('txt_order_id').value;
				if(txt_job_no!="")
				{
					 txt_job_no=txt_job_no;
				}
				else txt_job_no=myColValues;
				 
		 
		 
				
				//var data=cbo_company_name+"_"+cbo_buyer_name+"_"+txt_date_from+"_"+txt_date_to+"_"+cbo_order_status+"_"+cbo_team_name+"_"+cbo_team_member+"_"+cbo_category_by+"______"+"_"+myColValues+"_"+txt_job_no+"_"+txt_job_id+"_"+txt_order_no+"_"+txt_order_id;
				var data=cbo_company_name+"_"+cbo_buyer_name+"_"+txt_date_from+"_"+txt_date_to+"_"+cbo_order_status+"_"+cbo_team_name+"_"+cbo_team_member+"_"+cbo_category_by+"_______"+"_"+txt_job_no+"_"+txt_job_id+"_"+txt_order_no+"_"+txt_order_id;
				 // alert(data);
			}
			
			if (window.XMLHttpRequest)
			{// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
			}
			else
			{// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange=function()
			{
			if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			var response=(xmlhttp.responseText).split('####');	
			document.getElementById('report_container2').innerHTML=response[0];
			document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 

			append_report_checkbox('table_header_1',1);
				
				setFilterGrid("table_body",-1,tableFilters);
				document.getElementById('content_summary3_panel').innerHTML=document.getElementById('shipment_performance').innerHTML
							percent_set()

			}
			}
			xmlhttp.open("GET","requires/shipment_schedule_management_controller.php?data="+data+"&type=report_generate",true);
			xmlhttp.send();
	}
	
	function percent_set()
	{
		//alert("monzu");
		var tot_row=document.getElementById('tot_row').value;
		var tot_value_js=document.getElementById('total_value').value;
		
			for(var i=1;i<tot_row;i++)
		{
			var value_js=document.getElementById('value_'+i).value;
			var percent_value_js=((value_js*1)/(tot_value_js*1))*100
			document.getElementById('value_percent_'+i).innerHTML=percent_value_js.toFixed(2);
		}
	}


	function fn_generate_report(type)
	{
		
		if(document.getElementById('txt_job_no').value=='' && document.getElementById('txt_order_no').value==''){
			
			if (form_validation('cbo_company_name*txt_date_from*txt_date_to','Comapny*From Date*To Date')==false)
			{
				return;
			}
		
		}
		
		if(type==1)
		{
			var data="action=report_generate_2"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_team_name*cbo_team_member*cbo_category_by*txt_job_no*txt_job_id*txt_order_no*txt_order_id*txt_date_from*txt_date_to*cbo_order_status',"../../../");
		}
		else if(type==2)
		{
			var data="action=report_generate_3"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_team_name*cbo_team_member*cbo_category_by*txt_job_no*txt_job_id*txt_order_no*txt_order_id*txt_date_from*txt_date_to*cbo_order_status',"../../../");
		}
		

		console.log(data);
		//alert(data);return;
		freeze_window();
		http.open("POST","requires/shipment_schedule_management_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_generate_report_reponse;
	}




	function fn_generate_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			console.log(http.responseText);
			$('#report_container').html( '<br><a href="requires/' + reponse[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window_summary()" value="HTML Preview(Summary)" name="Print" class="formbutton" style="width:180px"/>' );
			$('#report_container2').html(reponse[0]);

			show_msg('3');
			release_freezing();
		}
	}


	function new_window(type)
	{
		 $('#scroll_body').css('overflow','auto');
		 $('#scroll_body').css('maxHeight','none');
		 $("#table_body_accss tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$('#scroll_body').css('overflow','scroll');
		$('#scroll_body').css('maxHeight','400px');
		
		$("#table_body_accss tr:first").show();
	}


	function new_window_summary(type)
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('summary_td').innerHTML+'</body</html>');
		d.close(); 
	}

	
	function order_dtls_popup(job_no,po_id,template_id,tna_process_type)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/capacity_and_order_booking_status_controller.php?action=work_progress_report_details&job_no='+job_no+'&po_id='+po_id+'&template_id='+template_id+'&tna_process_type='+tna_process_type, 'Work Progress Report Details PO Wise', 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
		}
	}	
	
	
</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../");  ?>
   <h3 style="width:1400px" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
       <div id="content_search_panel" > 
            <form>
                <fieldset style="width:1400px;">
                    <div  style="width:1400px" align="center">
                            <table class="rpt_table" width="1380" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <thead>
                                    <tr>
                                        <th colspan="11"><font size="3">Shipment Schedule Report</font></th>
                                    </tr>
                                    <tr>
                                        <th class="must_entry_caption">Company</th>
                                        <th>Buyer</th>
                                        <th>Team</th>
                                        <th>Team Member</th>
                                        <th>Job No</th>
                                        <th>Order No</th>
                                        <th colspan="2" class="must_entry_caption">Date</th>
                                        <th>Date Category</th>
                                        <th>Order Status</th>
                                        <th><input type="reset" name="reset" id="reset" value="Reset" style="width:80px" class="formbutton" /></th>
                                    </tr>
                                </thead>
                                <tr>
                                    <td>
                                           <?
                                           echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, " load_drop_down( '../merchandising_report/requires/shipment_schedule_management_controller', this.value, 'load_drop_down_buyer', 'buyer_td' )" );
                                            ?> 
                                    </td>
                                    <td id="buyer_td">
                                     <? 
                                        echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- Select Buyer --", $selected, "" );
                                     ?>	
                                    </td>
                                    <td >                
                                    
                                    <?
                                           echo create_drop_down( "cbo_team_name", 130, "select id,team_name from lib_marketing_team  where status_active =1 and is_deleted=0  order by team_name","id,team_name", 1, "-- Select Team --", $selected, " load_drop_down( '../merchandising_report/requires/shipment_schedule_management_controller', this.value, 'load_drop_down_team_member', 'team_td' )" );
                                            ?>
                                    </td>
                                    <td id="team_td">
                                    <div id="div_team">
                                    <? 
                                        echo create_drop_down( "cbo_team_member", 130, $blank_array,"", 1, "- Select Team Member- ", $selected, "" );
                                     ?>	
                                    </div>
                                    <td>
                                    <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:80px" onDblClick="openmypage_job();" placeholder="Wr./Br. Job" />
                                    <input type="hidden" id="txt_job_id" name="txt_job_id"/>
                                    </td>
                                    
                                    <td>
                                        <input type="text" id="txt_order_no" name="txt_order_no" class="text_boxes" style="width:80px" onDblClick="openmypage_order();" placeholder="Wr./Br. Order"  />
                                        <input type="hidden" id="txt_order_id" name="txt_order_id"/>
                                    </td>
                                    
                                    
                                    </td>
                                    <td><input name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:75px">
                                    </td>
                                    <td><input name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:75px">
                                    </td>
                                    <td>
                                    <select name="cbo_category_by" id="cbo_category_by"  style="width:130px" class="combo_boxes">
                                    <option value="1">Ship Date Wise </option>
                                    <option value="2">PO Rec. Date Wise </option>
                                    <option value="3">Actual Ship Date </option>
                                    </select>
                                    </td>
                                    <td><? echo create_drop_down( "cbo_order_status", 80, $order_status,"", 1, "--All--", 1,"po_recevied_date( this.value )", "" ); ?></td>
                                    <td align="center">
                                    <input type="button" name="search" id="search" value="Show" onClick="generate_report_main(13)" style="width:70px" class="formbutton" />
                                    <input type="button" name="search" id="search" value="Show 2" onClick="fn_generate_report(2)" style="width:70px" class="formbutton" />
                                    
                                    <input type="button" name="search" id="search" value="File Wise" onClick="fn_generate_report(1)" style="width:70px" class="formbutton" />
                                    <input name="fillter_check" id="fillter_check" type="hidden" >
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="11" align="center">
                                        <? echo load_month_buttons(1); ?>
                                    </td>
                                </tr>
                            </table>
                    </div>
                </fieldset>
            </form>
        </div>
       <div id="report_container" align="center"></div>
       <div id="report_container2"> 
        </div>
    </div>
    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
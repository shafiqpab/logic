<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Weekly Shipment Schedule With Production Report.
Functionality	:	
JS Functions	:
Created by		:	Md. Imrul Kayesh
Creation date 	: 	16-10-2021
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
echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
$yr = date("Y");
?>	

<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	var permission = '<? echo $permission; ?>';
 	function open_style_ref()
 	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer=$("#cbo_buyer_name").val();
		var job_no=$("#txt_job_no").val();
		var job_id=$("#hidden_job_id").val();
		var cbo_year=$("#cbo_year").val();
		var page_link='requires/weekly_shipment_schedule_with_production_report_controller.php?action=style_wise_search&company='+company+'&buyer='+buyer+'&job_no='+job_no+'&job_id='+job_id+'&cbo_year='+cbo_year; 
		var title="Search Style Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var style_data=this.contentDoc.getElementById("selected_id").value;
			var style_data=style_data.split("_");
			var style_hidden_id=style_data[0];
			var style_no=style_data[1];
				
			$("#txt_style_no").val(style_no);
			$("#hidden_style_id").val(style_hidden_id); 
		}
 	}	
 	function open_order_no()
	{
		if( form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var job_no=$("#txt_job_no").val();
		var job_id=$("#hidden_job_id").val();
		var company = $("#cbo_company_name").val();	
		var buyer=$("#cbo_buyer_name").val();
		var style_no=$('#txt_style_no').val();
		var style_id=$('#hidden_style_id').val();
		var cbo_year=$("#cbo_year").val();
	    var page_link='requires/weekly_shipment_schedule_with_production_report_controller.php?action=order_wise_search&company='+company+'&buyer='+buyer+'&style_id='+style_id+'&job_id='+job_id+'&job_no='+job_no+'&cbo_year='+cbo_year; 
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=580px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			//alert(prodID); // product ID
			var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#txt_order_no").val(prodDescription);
			$("#hidden_order_id").val(prodID); 
		}
	}
		 
	function open_job_no()
	{
		if( form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer=$("#cbo_buyer_name").val();
		var cbo_year=$("#cbo_year").val();
	    var page_link='requires/weekly_shipment_schedule_with_production_report_controller.php?action=job_wise_search&company='+company+'&buyer='+buyer+'&cbo_year='+cbo_year;
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var job_data=this.contentDoc.getElementById("selected_id").value;
			//alert(job_data); // Jov ID
			var job_data=job_data.split("_");
			var job_hidden_id=job_data[0];
			var job_no=job_data[1];
			//var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#txt_job_no").val(job_no);
			$("#hidden_job_id").val(job_hidden_id); 
			//alert($("#hidden_job_id").val())
		}
	}

	function generate_report(type)
	{
		freeze_window(3);
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			release_freezing();
			return;
		}

		var report_title=$( "div.form_caption" ).html();
		var data="action=generate_report"+get_submitted_data_string('cbo_company_name*cbo_working_company_name*cbo_buyer_name*cbo_brand_name*cbo_year*txt_job_no*txt_job_id*cbo_order_status*cbo_date_type*txt_date_from*txt_date_to*cbo_ship_status*cbo_week*txt_merch_style_ref*txt_master_style_ref',"../../../")+'&report_title='+report_title+'&type='+type;
		
		http.open("POST","requires/weekly_shipment_schedule_with_production_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			//alert (http.responseText);
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
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
	}
	
	function reset_form()
	{
		$("#hidden_style_id").val("");
		$("#hidden_order_id").val("");
		$("#hidden_job_id").val("");
		
	}

	function openmypage_embl(company_id,order_id,order_number,insert_date,type,action,width,height,embl_type,color_id,item_id,today_total)
	{
		var popup_width=width;
		var popup_height=height;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/weekly_shipment_schedule_with_production_report_controller.php?company_id='+company_id+'&action='+action+'&insert_date='+insert_date+'&order_id='+order_id+'&order_number='+order_number+'&type='+type+'&embl_type='+embl_type+'&color_id='+color_id+'&item_id='+item_id+'&today_total='+today_total, 'Detail Veiw', 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');
		
	} 	
	
  	function openmypage_swing(company_id,order_id,order_number,insert_date,type,action,width,height,resource,color_id,item_id,today_total)
	{
		var popup_width=width;
		var popup_height=height;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/weekly_shipment_schedule_with_production_report_controller.php?company_id='+company_id+'&action='+action+'&insert_date='+insert_date+'&order_id='+order_id+'&order_number='+order_number+'&type='+type+'&resource='+resource+'&color_id='+color_id+'&item_id='+item_id+'&today_total='+today_total, 'Details View', 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');
		
	} 
		 
	$(function()
	{
		var yr = '<? echo $yr;?>';
		$("#cbo_year").val(yr);
	});	 
	function openmypage_job()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();
		var cbo_year = $("#cbo_year").val();

		var page_link='requires/weekly_shipment_schedule_with_production_report_controller.php?action=job_search&companyID='+company+'&buyer_name='+buyer+'&cbo_year_id='+cbo_year;
		var title="Search Job Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=580px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_id=this.contentDoc.getElementById("hide_job_id").value; 
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			$("#txt_job_id").val(job_id); 
			$("#txt_job_no").val(job_no); 
		}
	}		

	function fnc_date_clear(type)
	{
		if(type==1)
		{
			$("#cbo_week").val(0);
			disable_enable_fields('cbo_week',1);
		}
		if(type==2)
		{
			$("#txt_date_from").val('');
			$("#txt_date_to").val('');
			disable_enable_fields('txt_date_from*txt_date_to',1);
		}
	} 	 
</script>

</head>
 
<body onLoad="set_hotkey();">
  	<div style="width:100%;" align="center"> 
   	<? echo load_freeze_divs ("../../../",'');  ?>
    <h3 style="width:1660px;  margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
       <div style="width:100%;" align="center" id="content_search_panel">
    <form id="dateWiseProductionReport_1">    
      <fieldset style="width:1660px;">
            <table class="rpt_table" width="1650" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
               	<thead>                    
                    <tr>
                        <th class="must_entry_caption" width="130">Company Name</th>
						<th width="130">Working Company</th>
                        <th width="120" >Buyer Name</th>
						<th width="120" >Brand</th>
                        <th width="80" >Job Year</th>
                        <th width="100">Job No</th>
						<th width="100" >Order Status</th>
						<th width="100" >Date Type</th>
                        <th width="170" class="must_entry_caption">Date Range</th>
						<th width="100" >Ship Status</th>
						<th width="100" >Week</th>
						<th width="100" >Merch Style Ref.</th>
						<th width="100" >Master Style  Ref.</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:60px" value="Reset" onClick="reset_form()"/></th>
                    </tr>   
              	</thead>
                <tbody>
                <tr class="general">
                    <td> 
                        <?
                            echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/weekly_shipment_schedule_with_production_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                        ?>
                    </td>
                    <td> 
                        <?
                            echo create_drop_down( "cbo_working_company_name", 130, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                        ?>
                    </td>
                    <td id="buyer_td">
                        <?  echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,""); ?>
                    </td>
					<td id="brand_td">
                        <? echo create_drop_down( "cbo_brand_name", 120, $blank_array,"", 1, "-- Select Band --", $selected, "",1,"" ); ?>
                    </td>
                    <td><?=create_drop_down( "cbo_year", 80, $year,"", 1, "--Year--",0, "load_drop_down( 'requires/weekly_shipment_schedule_with_production_report_controller',this.value, 'load_drop_down_week', 'week_td' );",0 ); ?></td>
                    <td>
						<input type="text" id="txt_job_no" name="txt_job_no" onDblClick="openmypage_job()" style="width:100px" class="text_boxes" placeholder="Browse or Write" />
						<input type="hidden" name="txt_job_id" id="txt_job_id"/>    
                    </td>
					<td><?=create_drop_down( "cbo_order_status", 80, $order_status,"", 1, "--Select--", 0, "",0 ); ?> </td>
					<td>
						<?
							$date_type=array(1=>"Ship Date", 2=>"PO Receive date", 3=>"Po insert Date");
							echo create_drop_down( "cbo_date_type", 80, $date_type,"", 1, "--Select--", 0, "",0 ); 
						?> 
					</td>
                    <td>
						<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" onclick="fnc_date_clear(1)">&nbsp; To
                    	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date" onclick="fnc_date_clear(1)" >
					</td>
					<td>
						<?
							$ship_status=array( 1=>"Full shipped/closed", 2=>"Partial + Full Pending" );
							echo create_drop_down( "cbo_ship_status", 80, $ship_status,"", 1, "--Select--", 0, "",0 ); 
						?> 
					</td>
					<td id="week_td"><?=create_drop_down( "cbo_week", 80, "select week from week_of_year where year=".date('Y')." group by week order by week","week,week", 1, "--Select--", 0, "fnc_date_clear(2);",0 ); ?> </td>
					<td>
                     <input type="text" id="txt_merch_style_ref"  name="txt_merch_style_ref"  style="width:100px" class="text_boxes" placeholder="Write" />
                    </td>
					<td>
                     	<input type="text" id="txt_master_style_ref"  name="txt_master_style_ref"  style="width:100px" class="text_boxes" placeholder="Write" />
                    </td>
                    <td>
                        <input type="button" id="show_button_po" class="formbutton" style="width:60px" value="Show" onClick="generate_report(1)" />
                        <input type="button" id="show_button" class="formbutton" style="width:60px" value="Show 2" onClick="generate_report(2)" />                        
                    </td>                    
                </tr>
                </tbody>
            </table>
            <table>
            	<tr>
                	<td colspan="14">
 						<? echo load_month_buttons(1); ?>
                   	</td>
                </tr>
            </table> 
      </fieldset>    
 </form> 
 </div>  
    <div id="report_container" style="padding: 10px;"></div>
    <div id="report_container2"></div>  
 </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

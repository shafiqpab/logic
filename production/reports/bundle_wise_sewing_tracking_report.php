<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Bundle Wise Sewing Tracking Report
				
Functionality	:	
JS Functions	:
Created by		:	Shafiq
Creation date 	: 	02-08-2021
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
echo load_html_head_contents(" Bundle Wise Sewing Tracking Report","../../", 1, 1, $unicode,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
var tableFilters = {}	
var tableFilters2 = {}	
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{
		var job_no 		= $("#txt_job_no").val();
		var int_ref 	= $("#txt_int_ref").val();
		var cutting_no 	= $("#txt_cutting_no").val();
		var bundle_no 	= $("#txt_bunle_no").val();
		var job_no 		= $("#txt_job_no").val();
		var wo_company	= $("#cbo_working_company_id").val();
		var lc_company	= $("#cbo_lc_company_id").val();

		if(wo_company==0 && lc_company==0)
		{
			alert('please select lc company or working company');return
		}

		/*if( form_validation('cbo_lc_company_id*txt_date_from*txt_date_to','Company*Date from*Date To')==false )
		{
			return;
		}*/		
		
		var data="action=report_generate"+get_submitted_data_string('cbo_lc_company_id*cbo_working_company_id*cbo_location_id*cbo_floor_id*cbo_buyer_id*txt_job_no*txt_file_no*txt_int_ref*color_id*txt_cutting_no*txt_bunle_no*txt_date_from*txt_date_to*txt_job_id*txt_color_name',"../../")+'&type='+type;
		
		//alert(data); return;
		
		freeze_window(3);
		http.open("POST","requires/bundle_wise_sewing_tracking_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{	
			if(http.readyState == 4) 
			{
				//alert (http.responseText);
				var reponse=trim(http.responseText).split("####");
				$("#report_container2").html(reponse[0]);  
				
				release_freezing();
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';							
				setFilterGrid("table_body",-1,tableFilters);
				show_msg('3');
				release_freezing();
			}
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

	function type_wise_fnc(data)
	{
		if(data==1)
		{
			$("#txt_search_val").val('');
			$("#type_wise_name").text("Job No");
			$("#txt_search_val").attr("placeholder","          Job No");
			
		}
		else if(data==2)
		{
			$("#txt_search_val").val('');
			$("#type_wise_name").text("Style");
			$("#txt_search_val").attr("placeholder","            Style");

		}
		else if(data==3)
		{
			$("#txt_search_val").val('');
			$("#type_wise_name").text("PO");
			$("#txt_search_val").attr("placeholder","               PO");
		}
		else
		{
			$("#txt_search_val").val('');
			$("#type_wise_name").text("");
			$("#txt_search_val").attr("placeholder","");
		}
	}
	
	function openProdPopup(param)
	{		
		var page_link='requires/bundle_wise_sewing_tracking_report_controller.php?action=open_prod_popup&data='+param;  
		var title="Production Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
	}

	function openProdPopup2(param)
	{		
		var page_link='requires/bundle_wise_sewing_tracking_report_controller.php?action=open_prod_popup2&data='+param;  
		var title="Production Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
	}

	function openProdPopupWIP(param)
	{		
		var page_link='requires/bundle_wise_sewing_tracking_report_controller.php?action=open_prod_popup_wip&data='+param;  
		var title="Production WIP Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
	}

	function openProdPopupWIP2(param)
	{		
		var page_link='requires/bundle_wise_sewing_tracking_report_controller.php?action=open_prod_popup_wip2&data='+param;  
		var title="Production WIP Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
	}

	function openmypage_jobno() // For job no
	{
		if( form_validation('cbo_lc_company_id','Company Name')==false )
		{
			return;
		}
		var lc_company = $("#cbo_lc_company_id").val();	
		var buyer = $("#cbo_buyer_id").val();
		var page_link='requires/bundle_wise_sewing_tracking_report_controller.php?action=search_by_action&lc_company='+lc_company+'&buyer='+buyer;  
		var title="Search By Job No";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=710px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var style_id=this.contentDoc.getElementById("hide_job_id").value; // product ID
			var style_des=this.contentDoc.getElementById("hide_job_no").value; // product Description

			$("#txt_job_id").val(style_id); 
			$("#txt_job_no").val(style_des);
		}
	}

	function openmypage_color() // For color
	{
		if( form_validation('txt_job_no','Search By')==false )
		{
			return;
		}
		var txt_job_no = $("#txt_job_no").val();	
		var txt_job_id = $("#txt_job_id").val();
		var page_link='requires/bundle_wise_sewing_tracking_report_controller.php?action=color_popup&txt_job_no='+txt_job_no+'&txt_job_id='+txt_job_id;  
		var title="Color Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var color_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			// var color_name=this.contentDoc.getElementById("txt_selected").value; // product Description
			// var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			// $("#txt_order").val(style_des);
			$("#color_id").val(color_id); 
			$("#txt_color_name").val(color_id);
		}
	}

	function openmypage_cutting_no() // For cutting no
	{
		/* if( form_validation('txt_job_no','Search By')==false )
		{
			return;
		} */
		var txt_job_no = $("#txt_job_no").val();	
		var txt_job_id = $("#txt_job_id").val();
		var color_id = $("#color_id").val();
		var page_link='requires/bundle_wise_sewing_tracking_report_controller.php?action=cutting_no_popup&txt_job_no='+txt_job_no+'&txt_job_id='+txt_job_id+'&color_id='+color_id;  
		var title="Cutting no Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var txt_cutting_no=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			$("#txt_cutting_no").val(txt_cutting_no);
		}
	}

	function getWorkingCompanyId() 
	{
	    var working_company_id = document.getElementById('cbo_working_company_id').value;
	    //var search_type = document.getElementById('cbo_search_by').value;
	    if(working_company_id !='') {
		  var data="action=load_drop_down_location&data="+working_company_id;
		  //alert(data);die;
		  http.open("POST","requires/bundle_wise_sewing_tracking_report_controller.php",true);
		  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		  http.send(data); 
		  http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#location_td').html(response);
	              set_multiselect('cbo_location_id','0','0','','0');
	              setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location_id,'0');getLocationId();") ,3000)]; 
	              //========================
	              // load_drop_down( 'requires/bundle_wise_sewing_tracking_report_controller', working_company_id, 'load_drop_down_buyer', 'buyer_td' );
	          }			 
	      };
	    }         
	}

	function getLocationId() 
	{
	    var working_company_id = document.getElementById('cbo_working_company_id').value;
	    var location_id = document.getElementById('cbo_location_id').value;
	    if(working_company_id !='') {
		  var data="action=load_drop_down_floor&data="+working_company_id+'_'+location_id;
		  //alert(data);die;
		  http.open("POST","requires/bundle_wise_sewing_tracking_report_controller.php",true);
		  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		  http.send(data); 
		  http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#floor_td').html(response);
	              set_multiselect('cbo_floor_id','0','0','','0'); 
				  setTimeout[($("#floor_td a").attr("onclick","disappear_list(cbo_floor_id,'0');getFloorId();") ,3000)]; 
	          }			 
	      };
	    }         
	}

	function getFloorId() 
	{
	    var working_company_id = document.getElementById('cbo_working_company_id').value;
	    var location_id = document.getElementById('cbo_location_id').value;
	    var floor_id = document.getElementById('cbo_floor_id').value;
	    if(working_company_id !='') {
		  var data="action=load_drop_down_line&data="+working_company_id+'_'+location_id+'_'+floor_id;
		  //alert(data);die;
		  http.open("POST","requires/bundle_wise_sewing_tracking_report_controller.php",true);
		  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		  http.send(data); 
		  http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#line_td').html(response);
	              set_multiselect('cbo_line_id','0','0','','0');
	          }			 
	      };
	    }         
	}
	
</script>

</head>
<body onLoad="set_hotkey();">

<form id="StyleandLineWiseProductionReport_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
         
         <h3 style="width:1420px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1420px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="120" class="must_entry_caption">LC Company</th>
                    <th width="120" class="must_entry_caption">Working Company</th>
                    <th width="120">Location</th>
                    <th width="120">Floor</th>
                    <th width="120" class="">Buyer</th>
                    <th width="80">Job No</th>
                    <th width="80">File No</th>
                    <th width="80">Int. Ref. No</th>
                    <th width="100">Color</th>
                    <th width="80">Cutting No</th>
                    <th width="80">Bundle No</th>
                    <th width="160" class="must_entry_caption">Production Date</th>
                    <th width="70">
                    	<input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('StyleandLineWiseProductionReport_1','report_container','','','')" />
                    </th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td id="cbo_lc_company_td">
							<? 
                                echo create_drop_down( "cbo_lc_company_id", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/bundle_wise_sewing_tracking_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>                            
                        </td>
                        <td id="working_company_td">
							<? 
                                echo create_drop_down( "cbo_working_company_id", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/bundle_wise_sewing_tracking_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                            ?>                            
                        </td>
                        <td id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location_id", 120, $blank_array,"", 1, "-- Select Location --", "", "" );
                            ?>                            
                        </td> 
                        <td id="floor_td">
                            <? 
                                echo create_drop_down( "cbo_floor_id", 120, $blank_array,"", 1, "-- Select Floor --", "", "" );
                            ?>                            
                        </td> 
                        <td id="buyer_td">
							<? 
                                echo create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "-- Select Buyer --", 0, "" );
                            ?>                            
                        </td>                      
                        <td>
                         <input type="text" id="txt_job_no"  name="txt_job_no"  style="width:80px" class="text_boxes" placeholder="Browse" ondblclick="openmypage_jobno()" readonly="" />
                         <input type="hidden" name="txt_job_id" id="txt_job_id" value="">
                        </td>                     
                        <td>
                         	<input type="text" id="txt_file_no"  name="txt_file_no"  style="width:80px" class="text_boxes" />
                        </td>                    
                        <td>
                         	<input type="text" id="txt_int_ref"  name="txt_int_ref"  style="width:80px" class="text_boxes" />
                        </td>
                        <td>                          
                         <input type="text" id="txt_color_name"  name="txt_color_name"  style="width:100px" class="text_boxes"  placeholder="Browse" ondblclick="openmypage_color()" readonly="" />
                         <input type="hidden" name="color_id" id="color_id" value="">                        	
                        </td>                     
                        <td>
                         <input type="text" id="txt_cutting_no"  name="txt_cutting_no"  style="width:80px" class="text_boxes" placeholder="Browse" ondblclick="openmypage_cutting_no()" readonly="" />
                        </td>                     
                        <td>
                         	<input type="text" id="txt_bunle_no"  name="txt_bunle_no"  style="width:80px" class="text_boxes" />
                        </td>                         
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" >
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date"  >
                        </td>                        
                        <td>
                            <input type="button" name="search1" id="search1" value="Show" onClick="generate_report(1)" style="width:60px" class="formbutton" />
                        </td>
                        
                    </tr>
                    <tr>
                        <td colspan="12" align="center" ><? echo load_month_buttons(1); ?></td>
						<td align="center">
							<input type="button" name="search2" id="search2" value="Show2" onClick="generate_report(2)" style="width:60px" title="WO Company disabled" class="formbutton" />
						</td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>
    
    <div id="report_container" align="center" style="padding: 10px 0;"></div>
    
    <div id="report_container2" align="left">
    	<div style="float:left; " id="report_container3"></div>
    </div>
 </form>   
</body>
<script>
	/*set_multiselect('cbo_working_company_id','0','0','0','0');	
	set_multiselect('cbo_location_id','0','0','','0');
	set_multiselect('cbo_floor_id','0','0','','0');
	set_multiselect('cbo_line_id','0','0','','0');

	setTimeout[($("#working_company_td a").attr("onclick","disappear_list(cbo_working_company_id,'0');getWorkingCompanyId();") ,3000)]; 
	setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location_id,'0');getLocationId();") ,3000)]; 
	setTimeout[($("#floor_td a").attr("onclick","disappear_list(cbo_floor_id,'0');getFloorId();") ,3000)]; */
	// $('#cbo_location').val(0);
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>

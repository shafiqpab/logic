<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Daily Line wise Production Report
				
Functionality	:	
JS Functions	:
Created by		:	Shafiq
Creation date 	: 	13-03-2019
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
echo load_html_head_contents("Daily Line wise Production Report","../../", 1, 1, $unicode,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
var tableFilters = {}	
var tableFilters2 = {}	
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{
		
		if( form_validation('cbo_working_company_id*txt_date_from*txt_date_to','Working Company*Date from*Date To')==false )
		{
			return;
		}
		

		var report_title=$( "div.form_caption" ).html();
		
		var data="action=report_generate"+get_submitted_data_string('cbo_working_company_id*cbo_location_id*cbo_floor_id*cbo_line_id*cbo_lc_company_id*cbo_buyer_id*txt_search_by*cbo_job_year*color_id*cbo_shipping_status*txt_date_from*txt_date_to*txt_order_id*txt_color_name*txt_internal_ref',"../../")+'&report_title='+report_title+'&type='+type;
		
		//alert(data); return;
		
		freeze_window(3);
		http.open("POST","requires/style_and_line_wise_production_report_controller.php",true);
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
				//document.getElementById('factory_efficiency').innerHTML=document.getElementById('total_factory_effi').innerHTML;
				//document.getElementById('factory_parfomance').innerHTML=document.getElementById('total_factory_per').innerHTML;
				//alert(reponse[1]);
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				if(type==1)
				{
					// setFilterGrid("html_search",-1,tableFilters);
				}
				else
				{
					// setFilterGrid("table_body",-1,tableFilters2);
				}				
				
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
		var page_link='requires/style_and_line_wise_production_report_controller.php?action=open_prod_popup&data='+param;  
		var title="Production Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
	}

	function openProdPopup2(param)
	{		
		var page_link='requires/style_and_line_wise_production_report_controller.php?action=open_prod_popup2&data='+param;  
		var title="Production Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
	}

	function openProdPopupWIP(param)
	{		
		var page_link='requires/style_and_line_wise_production_report_controller.php?action=open_prod_popup_wip&data='+param;  
		var title="Production WIP Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
	}

	function openProdPopupWIP2(param)
	{		
		var page_link='requires/style_and_line_wise_production_report_controller.php?action=open_prod_popup_wip2&data='+param;  
		var title="Production WIP Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
	}

	function openmypage_searchby() // For Line number
	{
		if( form_validation('cbo_working_company_id','Working Company Name')==false )
		{
			return;
		}
		var w_company = $("#cbo_working_company_id").val();	
		var lc_company = $("#cbo_lc_company_id").val();	
		var buyer = $("#cbo_buyer_id").val();	
		var job_year = $("#cbo_job_year").val();
		var page_link='requires/style_and_line_wise_production_report_controller.php?action=search_by_action&w_company='+w_company+'&lc_company='+lc_company+'&buyer='+buyer+'&job_year='+job_year;  
		var title="Search By Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=680px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			// $("#txt_order").val(style_des);
			var order_id_arr = style_id.split(',');
			var unique_ord_id_arr = Array.from(new Set(order_id_arr));
			var orderIds = unique_ord_id_arr.join(',');

			var order_des_arr = style_des.split(',');
			var unique_ord_des_arr = Array.from(new Set(order_des_arr));
			var orderDes = unique_ord_des_arr.join(',');

			$("#txt_order_id").val(orderIds); 
			$("#txt_search_by").val(orderDes);
		}
	}

	function openmypage_color() // For Line number
	{
		if( form_validation('txt_search_by','Search By')==false )
		{
			return;
		}
		var txt_search_by = $("#txt_search_by").val();	
		var txt_order_id = $("#txt_order_id").val();
		var page_link='requires/style_and_line_wise_production_report_controller.php?action=color_popup&txt_search_by='+txt_search_by+'&txt_order_id='+txt_order_id;  
		var title="Color Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var color_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var color_name=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			// $("#txt_order").val(style_des);
			$("#color_id").val(color_name); 
			$("#txt_color_name").val(color_name);
		}
	}

	function getWorkingCompanyId() 
	{
	    var working_company_id = document.getElementById('cbo_working_company_id').value;
	    //var search_type = document.getElementById('cbo_search_by').value;
	    if(working_company_id !='') {
		  var data="action=load_drop_down_location&data="+working_company_id;
		  //alert(data);die;
		  http.open("POST","requires/style_and_line_wise_production_report_controller.php",true);
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
	              // load_drop_down( 'requires/style_and_line_wise_production_report_controller', working_company_id, 'load_drop_down_buyer', 'buyer_td' );
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
		  http.open("POST","requires/style_and_line_wise_production_report_controller.php",true);
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
		  http.open("POST","requires/style_and_line_wise_production_report_controller.php",true);
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
 
	function print_button_setting()
	{
		get_php_form_data($('#cbo_working_company_id').val(),'print_button_variable_setting','requires/style_and_line_wise_production_report_controller' ); 
	}
</script>

</head>
<body onLoad="set_hotkey();">

<form id="StyleandLineWiseProductionReport_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
         
         <h3 style="width:1610px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1610px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="140" class="must_entry_caption">Working Company</th>
                    <th width="100">Location</th>
                    <th width="100">Floor</th>
                    <th width="100">Line No</th>
                    <th width="140" class="must_entry_caption">LC Company</th>
                    <th width="100" class="">Buyer</th>
                    <th width="80" class="">Job Year</th>
                    <th width="80">Search By</th>
                    <th width="100">Color</th>
                    <th width="80">IR/IB</th>
                    <th width="100">Shipping Status</th>
                    <th width="180" class="must_entry_caption">Production Date</th>
                    <th width="130">
                    	<input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('StyleandLineWiseProductionReport_1','report_container','','','')" />
                    </th>
                </thead>
                <tbody>
                    <tr class="general">
                       <td id="working_company_td">
							<?  
							    // get_php_form_data( this.value, 'report_button_setting','requires/style_and_line_wise_production_report_controller');
                                echo create_drop_down( "cbo_working_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 0, "-- Select Company --", 0, "get_php_form_data( this.value, 'report_button_setting','requires/style_and_line_wise_production_report_controller');load_drop_down( 'requires/style_and_line_wise_production_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                            ?>                            
                        </td>
                        <td id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location_id", 100, $blank_array,"", 1, "-- Select Location --", "", "" );
                            ?>                            
                        </td>                        
                        <td id="floor_td">
                            <? 
                                echo create_drop_down( "cbo_floor_id", 100, $blank_array,"", 1, "-- Select Floor --", "", "" );
                            ?>                            
                        </td>                         
                        <td id="line_td">
                            <? 
                                echo create_drop_down( "cbo_line_id", 100, $blank_array,"", "", "-- Select Line --", "", "" );
                            ?> 
                        </td>
                        <td id="cbo_lc_company_td">
							<? 
                                echo create_drop_down( "cbo_lc_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/style_and_line_wise_production_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>                            
                        </td>
                        <td id="buyer_td">
							<? 
                                echo create_drop_down( "cbo_buyer_id", 100, $blank_array,"", 1, "-- Select Buyer --", 0, "" );
                            ?>                            
                        </td>                        
                        <td>
                        	<? 
                        	  // $selected_year= date('Y');
                              echo create_drop_down( "cbo_job_year", 80, $year,"", 1, "All Year", "", "" );
                            ?>                            
                        </td>                        
                        <td>
                         <input type="text" id="txt_search_by"  name="txt_search_by"  style="width:80px" class="text_boxes" placeholder="Browse" ondblclick="openmypage_searchby()" readonly="" />
                         <input type="hidden" name="txt_order_id" id="txt_order_id" value="">
                        </td>
                        <td>                          
                         <input type="text" id="txt_color_name"  name="txt_color_name"  style="width:100px" class="text_boxes"  placeholder="Browse" ondblclick="openmypage_color()" readonly="" />
                         <input type="hidden" name="color_id" id="color_id" value="">                        	
                        </td>
                        <td>                          
                         <input type="text" id="txt_internal_ref"  name="txt_internal_ref"  style="width:80px" class="text_boxes"  placeholder="IR/IB" />                      	
                        </td>
                        <td>
                          	<?                         	  
                              	echo create_drop_down( "cbo_shipping_status", 100, $shipment_status,"", 1, "-- All --", "", "type_wise_fnc(this.value);" );
                            ?>                         	
                        </td>                         
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" >
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date"  >
                        </td>                        
                        <td>
                            <input type="button" name="search1" id="search1" value="Show" onClick="generate_report(1)" style="width:60px;display:none" class="formbutton" />
                           
                        </td>
                    </tr>
                    <tr>
                        <td colspan="12" align="center" width="100%"><? echo load_month_buttons(1); ?>
					    <input type="button" name="search2" id="search2" value="Show2" onClick="generate_report(2)" style="width:60px;display:none" class="formbutton" />
					    <input type="button" name="search3" id="search3" value="Show3" onClick="generate_report(3)" style="width:60px;display:none" class="formbutton" />
					
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
	set_multiselect('cbo_working_company_id','0','0','','0','print_button_setting()');
	// set_multiselect('cbo_working_company_id','0','0','0','0');	
	set_multiselect('cbo_location_id','0','0','','0');
	set_multiselect('cbo_floor_id','0','0','','0');
	set_multiselect('cbo_line_id','0','0','','0');

	setTimeout[($("#working_company_td a").attr("onclick","disappear_list(cbo_working_company_id,'0');getWorkingCompanyId();") ,3000)]; 
	setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location_id,'0');getLocationId();") ,3000)]; 
	setTimeout[($("#floor_td a").attr("onclick","disappear_list(cbo_floor_id,'0');getFloorId();") ,3000)]; 
	// $('#cbo_location').val(0);
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>

<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Daily Line wise Production Report
				
Functionality	:	
JS Functions	:
Created by		:	Md. Shafiqul Islam Shafiq
Creation date 	: 	28-04-2019
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
echo load_html_head_contents("Buyer Inspection Report","../../", 1, 1, $unicode,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
var tableFilters = {}	
var tableFilters2 = {}	
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{
		var date_type = $("#cbo_date_type").val();
		var job_no = $("#txt_job_no").val();
		var style_ref_no = $("#txt_style_ref_no").val();

		if(job_no !="" || style_ref_no !="")
		{
			if( form_validation('cbo_working_company_id','Working Company')==false )
			{
				return;
			}
		}
		else
		{
			if(date_type==3)
			{
				if( form_validation('cbo_working_company_id*txt_date_from*txt_date_to','Working Company*Date from*Date To')==false )
				{
					return;
				}
			}
			else
			{
				if( form_validation('cbo_working_company_id*cbo_lc_company_id*txt_date_from*txt_date_to','Working Company*LC Company*Date from*Date To')==false )
				{
					return;
				}
			}
		}
			

		var report_title=$( "div.form_caption" ).html();
		
		var data="action=report_generate"+get_submitted_data_string('cbo_working_company_id*cbo_location_id*cbo_floor_id*cbo_lc_company_id*cbo_buyer_id*cbo_job_year*txt_job_no*txt_style_ref_no*cbo_date_type*cbo_inspection_result*txt_date_from*txt_date_to*hiden_order_id',"../../")+'&report_title='+report_title+'&type='+type;
		
		// alert(data); return;
		
		freeze_window(3);
		http.open("POST","requires/buyer_inspection_report_controller.php",true);
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
		var page_link='requires/buyer_inspection_report_controller.php?action=open_prod_popup&data='+param;  
		var title="Production Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
	}

	function openProdPopup2(param)
	{		
		var page_link='requires/buyer_inspection_report_controller.php?action=open_prod_popup2&data='+param;  
		var title="Production Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
	}

	function openProdPopupWIP(param)
	{		
		var page_link='requires/buyer_inspection_report_controller.php?action=open_prod_popup_wip&data='+param;  
		var title="Production WIP Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
	}

	function openProdPopupWIP2(param)
	{		
		var page_link='requires/buyer_inspection_report_controller.php?action=open_prod_popup_wip2&data='+param;  
		var title="Production WIP Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
	}

	function openmypage_job_no() // For Line number
	{
		if( form_validation('cbo_working_company_id','Working Company Name')==false )
		{
			return;
		}
		var w_company = $("#cbo_working_company_id").val();	
		var lc_company = $("#cbo_lc_company_id").val();	
		var buyer = $("#cbo_buyer_id").val();	
		var job_year = $("#cbo_job_year").val();
		var page_link='requires/buyer_inspection_report_controller.php?action=openJobNoPopup&w_company='+w_company+'&lc_company='+lc_company+'&buyer='+buyer+'&job_year='+job_year;  
		var title="Job No Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=520px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var po_id=this.contentDoc.getElementById("txt_selected_po").value; // product ID
			var job_no=this.contentDoc.getElementById("txt_selected_job").value; // product Description
			var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			// $("#txt_order").val(style_des);
			var order_id_arr = po_id.split(',');
			var unique_ord_id_arr = Array.from(new Set(order_id_arr));
			var orderIds = unique_ord_id_arr.join(',');

			var job_no_arr = job_no.split(',');
			var unique_job_arr = Array.from(new Set(job_no_arr));
			var jobNo = unique_job_arr.join(',');

			$("#hiden_order_id").val(orderIds); 
			$("#txt_job_no").val(jobNo);
		}
	}

	function openmypage_style_ref() // For Line number
	{
		if( form_validation('cbo_working_company_id','Working Company Name')==false )
		{
			return;
		}
		var w_company = $("#cbo_working_company_id").val();	
		var lc_company = $("#cbo_lc_company_id").val();	
		var buyer = $("#cbo_buyer_id").val();	
		var job_year = $("#cbo_job_year").val();
		var page_link='requires/buyer_inspection_report_controller.php?action=style_ref_no_popup&w_company='+w_company+'&lc_company='+lc_company+'&buyer='+buyer+'&job_year='+job_year;  
		var title="Job No Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=520px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var po_id=this.contentDoc.getElementById("txt_selected_po").value; // product ID
			var style_no=this.contentDoc.getElementById("txt_selected_style").value; // product Description
			var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			// $("#txt_order").val(style_des);
			var order_id_arr = po_id.split(',');
			var unique_ord_id_arr = Array.from(new Set(order_id_arr));
			var orderIds = unique_ord_id_arr.join(',');

			var style_no_arr = style_no.split(',');
			var unique_style_arr = Array.from(new Set(style_no_arr));
			var styleNo = unique_style_arr.join(',');

			$("#hiden_order_id").val(orderIds); 
			$("#txt_style_ref_no").val(styleNo);
		}
	}

	function getWorkingCompanyId() 
	{
	    var working_company_id = document.getElementById('cbo_working_company_id').value;
	    //var search_type = document.getElementById('cbo_search_by').value;
	    if(working_company_id !='') {
		  var data="action=load_drop_down_location&data="+working_company_id;
		  //alert(data);die;
		  http.open("POST","requires/buyer_inspection_report_controller.php",true);
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
	              // load_drop_down( 'requires/buyer_inspection_report_controller', working_company_id, 'load_drop_down_buyer', 'buyer_td' );
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
		  http.open("POST","requires/buyer_inspection_report_controller.php",true);
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

	function search_populate(str)
	{
		if(str==1)
		{
			document.getElementById('search_by_th_up').innerHTML="Country Ship Date";
			$('#search_by_th_up').css('color','blue');
		}
		else if(str==2)
		{
			document.getElementById('search_by_th_up').innerHTML="Publish Ship Date";
			$('#search_by_th_up').css('color','blue');
		}
		else if(str==3)
		{
			document.getElementById('search_by_th_up').innerHTML="Inspection date";
			$('#search_by_th_up').css('color','blue');
		}
	}

	function hs_chart(gtype,orderVal,passVal,balanceVal,extraVal, Month)
	{
		
		passVal=passVal*1;
		orderVal=orderVal*1;
		balanceVal=balanceVal*1;
		extraVal=extraVal*1;

		//alert(balanceVal +'=='+extraVal)

		$('#container'+gtype).highcharts({
	        chart: {
	            type: 'column'
	        },
	        title: {
	            text: ''
	        },
	        subtitle: {
	            text: ''
	        },
	        xAxis: {
	            categories:[Month],
	            title: {
	                text: null
	            }
	        },
	        yAxis: {
	            min: 0,
	            title: {
	                align: 'high'
	            },
	            labels: {
	                overflow: 'justify'
	            }
	        },
	        tooltip: {
	            valueSuffix: '',
				backgroundColor: 'rgba(219,219,216,0.8)',
				borderWidth: 0
	        },
			
	        plotOptions: {
	            bar: {
	                dataLabels: {
	                    enabled: true
	                }
	            }
	        },
	        credits: {
	            enabled: false
	        },
	        series: [{
	            name: 'Order Qty',
	            data: [orderVal]
			
			}, {
	            name: 'Pass Qty',
	            data: [passVal]
	        }, {
	            name: 'Balance',
	            data: [balanceVal]
	        }, {
	            name: 'Extra',
	            data: [extraVal]
	        }]
	    });
			
	}
	
</script>
<script src="../../ext_resource/hschart/hschart.js"></script>
</head>
<body onLoad="set_hotkey();">

<form id="StyleandLineWiseProductionReport_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
         
         <h3 style="width:1550px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1550px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="160" class="must_entry_caption">Working Company</th>
                    <th width="120">Location</th>
                    <th width="120">Floor</th>
                    <th width="160" class="must_entry_caption">LC Company</th>
                    <th width="120" class="">Buyer</th>
                    <th width="80" class="">Job Year</th>
                    <th width="80">Job No</th>
                    <th width="100">Style Ref. No.</th>
                    <th width="120">Date Type</th>
                    <th width="100">Result</th>
                    <th width="200"  id="search_by_th_up" class="must_entry_caption">Inspection Date</th>
                    <th width="130">
                    	<input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('StyleandLineWiseProductionReport_1','report_container','','','')" />
                    </th>
                </thead>
                <tbody>
                    <tr class="general">
                       <td id="working_company_td">
							<? 
                                echo create_drop_down( "cbo_working_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "-- Select Company --", 0, "load_drop_down( 'requires/buyer_inspection_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
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
                        <td id="cbo_lc_company_td">
							<? 
                                echo create_drop_down( "cbo_lc_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/buyer_inspection_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>                            
                        </td>
                        <td id="buyer_td">
							<? 
                                echo create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "-- Select Buyer --", 0, "" );
                            ?>                            
                        </td>                        
                        <td>
                        	<? 
                        	  $selected_year= date('Y');
                              echo create_drop_down( "cbo_job_year", 80, $year,"", 1, "All Year", $selected_year, "" );
                            ?>                            
                        </td>                        
                        <td>
                         <input type="text" id="txt_job_no"  name="txt_job_no"  style="width:100px" class="text_boxes" placeholder="Write or Browse" onDblClick="openmypage_job_no()"/>
                         <input type="hidden" name="hiden_order_id" id="hiden_order_id" value="">
                        </td>
                        <td>                          
                         <input type="text" id="txt_style_ref_no"  name="txt_style_ref_no"  style="width:100px" class="text_boxes"  placeholder="Write or Browse" onDblClick="openmypage_style_ref()"/>                       	
                        </td>                         
                        <td id="line_td">
                            <? 
                            	$date_type_arr=array("1"=>"Country Ship Date","2"=>"Publish Ship Date","3"=>" Inspection Date");
                            	$dd="search_populate(this.value)";
                                echo create_drop_down( "cbo_date_type", 120, $date_type_arr,"", "", "-- Select Date Type --", 3, $dd );
                            ?> 
                        </td>

                        <td>
                          	<?                         	  
                              	echo create_drop_down( "cbo_inspection_result", 100, $inspection_status,"", 1, "-Select Result-", "", "" );
                            ?>                         	
                        </td>                         
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" >
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date"  >
                        </td>                        
                        <td>
                            <input type="button" name="search1" id="search1" value="Show" onClick="generate_report(1)" style="width:60px" class="formbutton" />
                        </td>
                        
                    </tr>
                    <tr>
                        <td colspan="12" align="center" width="100%"><? echo load_month_buttons(1); ?></td>
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
	set_multiselect('cbo_working_company_id','0','0','0','0');	
	set_multiselect('cbo_location_id','0','0','','0');
	set_multiselect('cbo_floor_id','0','0','','0');
	// set_multiselect('cbo_line_id','0','0','','0');

	setTimeout[($("#working_company_td a").attr("onclick","disappear_list(cbo_working_company_id,'0');getWorkingCompanyId();") ,3000)]; 
	setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location_id,'0');getLocationId();") ,3000)]; 
	// setTimeout[($("#floor_td a").attr("onclick","disappear_list(cbo_floor_id,'0');getFloorId();") ,3000)]; 
	// $('#cbo_location').val(0);
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>

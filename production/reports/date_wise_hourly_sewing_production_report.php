<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Date Wise Hourly Sewing Production Report
				
Functionality	:	
JS Functions	:
Created by		:	Shafiq
Creation date 	: 	31-12-2022
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
echo load_html_head_contents("Date Wise Hourly Sewing Production Report","../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{
 
		/* if ($('#hidden_order_id').val() || $('#hidden_job_id').val() ) 
		{ 
			if( form_validation('cbo_company_id','Company Name')==false )
			{
				return;
			}
		}
		else
		{ */
			if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*Production Date From*Production Date To')==false )
			{
				return;
			}
		// }
		var report_title=$( "div.form_caption" ).html();
	
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_floor_id*cbo_line*hidden_line_id*cbo_buyer_name*txt_date_from*txt_date_to*cbo_line_status*hidden_order_id*hidden_job_id',"../../")+'&report_title='+report_title+'&rptType='+type;		
		
		freeze_window(3);
		http.open("POST","requires/date_wise_hourly_sewing_production_report_controller.php",true);
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
			//alert(reponse[1]);
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
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
	}
	
/*	function openmypage(company_id,order_id,item_id,location,floor_id,sewing_line,prod_date,action,prod_type,prod_reso_allo)
	{
		var popup_width='';
		if(action=="today_prod") popup_width='850px'; else popup_width='500px';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_hourly_sewing_production_report_controller.php?po_id='+order_id+'&company_id='+company_id+'&action='+action+'&item_id='+item_id+'&location='+location+'&floor_id='+floor_id+'&sewing_line='+sewing_line+'&prod_date='+prod_date+'&prod_type='+prod_type+'&prod_reso_allo='+prod_reso_allo, 'Detail Veiw', 'width='+popup_width+', height=370px,center=1,resize=0,scrolling=0','../');
	}
	*/
	
		
 	function open_line_popup()
	{
		if( form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company = $("#cbo_company_id").val();	
		var location=$("#cbo_location_id").val();
		var floor_id=$("#cbo_floor_id").val();
		var txt_date_from=$("#txt_date_from").val();
		var txt_date_to=$("#txt_date_to").val();
	    var page_link='requires/date_wise_hourly_sewing_production_report_controller.php?action=line_popup&company='+company+'&location='+location+'&floor_id='+floor_id+'&txt_date_from='+txt_date_from+'&txt_date_to='+txt_date_to; 
		
		var title="Search line Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=310px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]; 
				var prodID=this.contentDoc.getElementById("txt_selected_id").value;
		
				var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
				$("#cbo_line").val(prodDescription);
				$("#hidden_line_id").val(prodID); 
			}
	}
	 
	function openmypage(company_id,order_id,floor_id,line_no,action,item_smv,prod_date)
	{
			popup_width='550px'; 

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_hourly_sewing_production_report_controller.php?po_id='+order_id+'&company_id='+company_id+'&action='+action+'&floor_id='+floor_id+'&sewing_line='+line_no+'&prod_date='+prod_date+'&item_smv='+item_smv, 'Detail Veiw', 'width='+popup_width+', height=270px,center=1,resize=0,scrolling=0','../');
	}
	 
	 

	function getCompanyId() 
	{	 
	    var company_id = document.getElementById('cbo_company_id').value;

	    if(company_id !='') {
	      var data="action=load_drop_down_location&choosenCompany="+company_id;
	      http.open("POST","requires/date_wise_hourly_sewing_production_report_controller.php",true);
	      http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	      http.send(data); 
	      http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#location_td').html(response);
	              set_multiselect('cbo_location_id','0','0','','0');
				  setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location_id,'0');getLocationId();") ,3000)];

			  	load_drop_down( 'requires/date_wise_hourly_sewing_production_report_controller', company_id, 'load_drop_down_buyer', 'buyer_td_id' );
				  set_multiselect('cbo_buyer_name','0','0','','0');
	          }
	      };
	    }         
	}

	function getLocationId() 
	{	 
	    var location_id = document.getElementById('cbo_location_id').value;

	    if(location_id !='') {
	      var data="action=load_drop_down_floor&choosenLocation="+location_id;
	      http.open("POST","requires/date_wise_hourly_sewing_production_report_controller.php",true);
	      http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	      http.send(data); 
	      http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#floor_td').html(response);
	              set_multiselect('cbo_floor_id','0','0','','0');
	          }			 
	      };
	    }         
	}
	 
	function open_order_no()
	{
		if( form_validation('cbo_company_id*cbo_year','Company Name* Job Year')==false)
		{
			return;
		}
		var job_no=$("#txt_job_no").val();
		var job_id=$("#hidden_job_id").val();
		var company = $("#cbo_company_id").val();	
		var buyer=$("#cbo_buyer_name").val();  
		var cbo_year=$("#cbo_year").val();
		var txt_date_from=$("#txt_date_from").val();
		var txt_date_to=$("#txt_date_to").val();
	    var page_link='requires/date_wise_hourly_sewing_production_report_controller.php?action=order_wise_search&company='+company+'&buyer='+buyer+'&job_id='+job_id+'&job_no='+job_no+'&cbo_year='+cbo_year+'&txt_date_from='+txt_date_from+'&txt_date_to='+txt_date_to; 
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
		if( form_validation('cbo_company_id*cbo_year','Company Name*Job Year')==false)
		{
			return;
		}
		var company = $("#cbo_company_id").val();	
		var buyer=$("#cbo_buyer_name").val();
		var cbo_year=$("#cbo_year").val();
		var txt_date_from=$("#txt_date_from").val();
		var txt_date_to=$("#txt_date_to").val();
	    var page_link='requires/date_wise_hourly_sewing_production_report_controller.php?action=job_wise_search&company='+company+'&buyer='+buyer+'&cbo_year='+cbo_year+'&txt_date_from='+txt_date_from+'&txt_date_to='+txt_date_to;
		var title="Search Job Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=730px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			var job_no=this.contentDoc.getElementById("hide_job_no").value;

			$("#txt_job_no").val(job_no);
			$("#hidden_job_id").val(job_id); 
		}
	}	
	 
	function open_avg_cm_popup(date,line_id,job,action)
	{
		popup_width='400px'; 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_hourly_sewing_production_report_controller.php?job='+job+'&line_id='+line_id+'&date='+date+'&action='+action, 'AVG CM Popup', 'width='+popup_width+', height=270px,center=1,resize=0,scrolling=0','../');
	}
</script>

</head>
<body onLoad="set_hotkey();">

<form id="LineWiseProductivityAnalysis_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
         
         <h3 style="width:1460px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1460px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="160" class="must_entry_caption">WO Company</th>
                    <th width="160">Location</th>
                    <th width="140">Floor</th>
                    <th width="140">Line No</th>
                    <th width="140">Line Status</th>
                    <th width="150">Buyer</th>
					<th width="50">Job Year</th>
					<th width="100">Job No</th>
					<th width="100">Order No </th>
                    <th class="must_entry_caption">Production Date</th>
                    <th width="140"><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('LineWiseProductivityAnalysis_1','report_container','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                       <td id="td_company">
							<? 
                                echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "-- Select Company --", 0, "" );
                            ?>                            
                        </td>
                        <td id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location_id", 150, $blank_array,"", 1, "-- Select Location --", "", "" );
                            ?>                            
                        </td>
                        <td id="floor_td">
                            <? 
                                echo create_drop_down( "cbo_floor_id", 130, $blank_array,"", 1, "-- Select Floor --", "", "" );
                            ?>                            
                        </td>
                        <td id="line_td">
                            <input type="text" id="cbo_line"  name="cbo_line"  style="width:140px" class="text_boxes" onDblClick="open_line_popup()" placeholder="Browse"  readonly/>                                    <input type="hidden" id="hidden_line_id" name="hidden_line_id" />
                        </td>
                        <td> 
                            <?
							$line_status_arr = array(1=>"All Line",2=>"Only Prod Line");
                            echo create_drop_down( "cbo_line_status", 140, $line_status_arr,"", 1, "-- Select --", 0, "",0 );
                            ?>
                        </td>
                        <td id="buyer_td_id"> 
                            <?
                               echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- Select Buyer --", 0, "",0 );
                            ?>
                        </td>
						<td>
							<? 
								echo create_drop_down( "cbo_year", 50, $year,"", 1, "Year--",date('Y'), "",0 );
							?>
                    	</td>
						<td>
                       		<input type="text" id="txt_job_no"  name="txt_job_no"  style="width:90px" class="text_boxes" onDblClick="open_job_no()" placeholder="Browse/Write" />
							<input type="hidden" id="hidden_order_id"  name="hidden_order_id" />
                        	<input type="hidden" id="hidden_job_id"  name="hidden_job_id" />
                    	</td>
 						<td>
                    		<input type="text" id="txt_order_no"  name="txt_order_no"  style="width:90px" class="text_boxes" onDblClick="open_order_no()" placeholder="Browse/Write" />
                    	</td>
                         <td>
                            <input type="text" name="txt_from_date" id="txt_date_from" class="datepicker" style="width:60px;" readonly/>
							<input type="text" name="txt_to_date" id="txt_date_to" class="datepicker" style="width:60px;" readonly/>
                        </td>
                        <td>
                            <input type="button" name="Show" id="Show_" value="Floor Wise" onClick="generate_report(1)" class="formbutton" />
                            <input type="button" name="Show2" id="Show2_" value="Order Wise" onClick="generate_report(2)" class="formbutton"/>
                        </td>
                    </tr>
					<tr>
                	<td colspan="11" align="center">
 						<? echo load_month_buttons(1); ?>
                   	</td>
                </tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>
    <div id="report_container" align="center" style="margin:5px 0;"></div>
    <div id="report_container2" align="left"></div>
 </form>   
<script> 
	set_multiselect('cbo_company_id','0','0','','0'); 
	set_multiselect('cbo_location_id','0','0','','0'); 
	set_multiselect('cbo_floor_id','0','0','','0'); 
	set_multiselect('cbo_buyer_name','0','0','','0'); 

	setTimeout[($("#td_company a").attr("onclick","disappear_list(cbo_company_id,'0');getCompanyId();") ,3000)];
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</body>
</html>

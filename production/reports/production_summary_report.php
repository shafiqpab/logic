<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Production Summary Report
				
Functionality	:	
JS Functions	:
Created by		:	Rakib Hasan Mondal
Creation date 	: 	08-08-2023
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
echo load_html_head_contents("Day wise target vs achivement Report","../../", 1, 1, $unicode,1,1); 
?>	
<script>
let permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{
		if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*Production Date*Production Date')==false )
		{
			return;
		}

		let action= "report_generate";
		let data='action='+action+'&type='+type+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_floor_id*txt_date_from*txt_date_to',"../../");
 
		freeze_window(3);
		http.open("POST","requires/production_summary_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			//alert (http.responseText);
			let reponse=trim(http.responseText).split("####");
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
		
		let w = window.open("Surprise", "#");
		let d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
	}
    function getLocationId() 
	{	 
		let company_id = document.getElementById('cbo_company_id').value; 

	    if(company_id !='') 
		{
	      let data="action=load_drop_down_location&choosenCompany="+company_id;
	      http.open("POST","requires/production_summary_report_controller.php",true);
	      http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	      http.send(data); 
	      http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              let response = trim(http.responseText);
	              $('#location_td').html(response);
	              set_multiselect('cbo_location_id','0','0','','0');
				  setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location_id,'0');getFloorId();") ,3000)];
	          }			 
	      };
	    }     
	}

	function getFloorId() 
	{	 
	    let location_id = document.getElementById('cbo_location_id').value;

	    if(location_id !='') 
		{
	      let data="action=load_drop_down_floor&choosenLocation="+location_id;
	      http.open("POST","requires/production_summary_report_controller.php",true);
	      http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	      http.send(data); 
	      http.onreadystatechange = function()
		  {
	          if(http.readyState == 4) 
	          {
	              let response = trim(http.responseText);
	              $('#floor_td').html(response);
	              set_multiselect('cbo_floor_id','0','0','','0');
	          }			 
	      };
	    }         
	}

	function target_value_popup(company_id,floor_id)
	{
		popup_width='700px'; 
		txt_date_from = $('#txt_date_from').val();
		txt_date_to   = $('#txt_date_to').val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/production_summary_report_controller.php?action=target_value_popup'+'&company_id='+company_id+'&floor_id='+floor_id+'&txt_date_from='+txt_date_from+'&txt_date_to='+txt_date_to, 'Target Value Breakdown Popup', 'width='+popup_width+', height=290px,center=1,resize=0,scrolling=0','../');
	}
</script>

</head>
<body onLoad="set_hotkey();">

<form id="productionSummeryReport">
    <div style="width:100%;" align="center">    
    
        <? 
            $width = 750;
            echo load_freeze_divs ("../../",'');  
        ?>
         
         <h3 style="width:<?= $width+5 ?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:<?= $width ?>px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="160" class="must_entry_caption">Company</th>
                    <th width="160">Location</th>
                    <th width="140">Floor</th>
                    <th width="180" class="must_entry_caption">Date Range</th>
                    <th width="90">
                        <input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('productionSummeryReport','report_container','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general"> 
                        <td align="center" id="td_company"> 
                            <?
                                echo create_drop_down( "cbo_company_id", 150, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 0, "-- Select Company --", $selected, "" );
                            ?>
                        </td>
                        <td align="center" id="location_td"> 
                            <?
                                echo create_drop_down( "cbo_location_id", 150, $blank_array,"","", "-- Select location --", "", 1 ); 
                            ?>
                        </td>

                        <td align="center" id="floor_td"> 
                            <?
                                echo create_drop_down( "cbo_floor_id", 130, $blank_array,"","", "-- Select floor --", "", 1 ); 
                            ?>
                        </td>             
	                     <td>
	                     	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" >&nbsp; To
	                    	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date"  >
	                    </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:60px" class="formbutton" />
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
    <div id="report_container" align="center" style="padding:5px 0"></div>
    <div id="report_container2" align="left"></div>
 </form>   
</body>
<script>
    set_multiselect('cbo_company_id','0','0','','0'); 
	set_multiselect('cbo_location_id','0','0','','0'); 
	set_multiselect('cbo_floor_id','0','0','','0');  
	setTimeout[($("#td_company a").attr("onclick","disappear_list(cbo_company_id,'0');getLocationId();") ,3000)];
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>

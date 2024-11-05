<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create for Weekly summary report 
				
Functionality	:	
JS Functions	:
Created by		:	Rakib Hasan Mondal
Creation date 	: 	17-01-2024
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
		let data='action='+action+'&type='+type+get_submitted_data_string('cbo_company_id*cbo_buyer_name*txt_date_from*txt_date_to',"../../");
 
		freeze_window(3);
		http.open("POST","requires/weekly_summary_report_controller.php",true);
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
 
 
</script>

</head>
<body onLoad="set_hotkey();">

<form id="weeklySummeryReport">
    <div style="width:100%;" align="center">    
    
        <? 
            $width = 650;
            echo load_freeze_divs ("../../",'');  
        ?>
         
         <h3 style="width:<?= $width+5 ?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:<?= $width ?>px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="150" class="must_entry_caption">Company</th>
                    <th width="150">Buyer</th> 
                    <th width="180" class="must_entry_caption">Date Range</th>
                    <th width="110">
                        <input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('weeklySummeryReport','report_container','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general"> 
                        <td align="center" id="td_company"> 
                            <?
                                echo create_drop_down( "cbo_company_id", 150, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/weekly_summary_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );"  );
                            ?>
                        </td>
                        <td align="center" id="buyer_td"> 
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                            ?>
                        </td>             
	                     <td>
	                     	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" >&nbsp; To
	                    	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date"  >
	                    </td>
                        <td>
                            <input type="button" name="search" id="search" value="Yearly Summary" onClick="generate_report(1)" style="width:100px" class="formbutton" />
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
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>

<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Mushok Report				
Functionality	:	
JS Functions	:
Created by		:	Nayem
Creation date 	: 	6-2-2022
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
echo load_html_head_contents("Mushok Report","../../", 1, 1, $unicode,1,0); 

?>
<script>
    var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
			
	function  generate_report(type)
	{
		if( form_validation('cbo_company_name*cbo_item_cat*txt_date_from*txt_date_to','Company Name*Item Category*Date From*Date To')==false )
		{
			return;
		} 
			
		var cbo_company_name= $("#cbo_company_name").val();
		var cbo_item_cat 	 = $("#cbo_item_cat").val();
		var txt_date_from 	= $("#txt_date_from").val();
		var txt_date_to 	= $("#txt_date_to").val();
				
		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_item_cat="+cbo_item_cat+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&type="+type;

		var data="action=generate_report"+dataString;
		freeze_window(3);
		http.open("POST","requires/mushok_report_contorller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse; 
		
	}

	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
		var reponse=trim(http.responseText).split("####");
		$("#report_container2").html(reponse[0]);  
		document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		setFilterGrid("table_body_1",-1);
		show_msg('3');
		release_freezing();
		}
	} 

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		$('#scroll_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	    '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="350px";
        $('#scroll_body tr:first').show();
	}

	function fnc_rcv_details(prod_id,date_from,date_to,action)
	{
		var page_link='requires/mushok_report_contorller.php?action='+action+'&prod_id='+prod_id+'&date_from='+date_from+'&date_to='+date_to;  
		var title="Receive Details"
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1110'+',height=370px,center=1,resize=1,scrolling=1','../');
	}
    
</script>


<body onLoad="set_hotkey()">
<form name="mushokReport_1" id="mushokReport_1" autocomplete="off" >
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
   <div style="width:800px;" align="center">
    <h3 align="center" id="accordion_h1" style="width:800px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    </div>
    <div style="width:800px;" align="center" id="content_search_panel">
        <fieldset style="width:800px;">
             <table class="rpt_table" width="800" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                <thead>
                    <tr>
                        <th width="220" class="must_entry_caption">Company</th>  
						<th width="200" class="must_entry_caption">Item Category</th>                                    
                        <th width="150" colspan="2" class="must_entry_caption">Date Range</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('mushokReport_1','report_container*report_container2','','','','')" /></th>
                    </tr>
                </thead>
				<tbody>
					<tr class="general">
						<td>
							<?
							echo create_drop_down( "cbo_company_name", 180, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "",0 );
							?>                          
						</td>
						<td>
							<?	echo create_drop_down( "cbo_item_cat", 150, $item_category,"", 1, "--- Select ---", 0, "","","1,2,3,4,5,6,7,8,9,10,11,13,15,16,17,18,19,20,21,22,23,32,34,35,36,37,38,39,33,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,89,81,90,91,92,93,94,101",0 ); ?>
						</td>                    
						<td colspan="2">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:65px;" readonly/> 
							To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px;" readonly/>
						</td>
						<td>
						<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:100px" class="formbutton" />   
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
					</tr>
				</tbody>
            </table>  
        </fieldset> 
    </div>
    	<div id="report_container" align="center"></div>
        <div id="report_container2"></div> 
</div> 
</form>    
</body> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
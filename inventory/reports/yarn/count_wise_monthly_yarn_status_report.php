<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create mrr wise yarn stock report
				
Functionality	:	
JS Functions	:
Created by		:	jahid 
Creation date 	: 	08-06-2015
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
echo load_html_head_contents("MRR Wise Yarn Stock","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(operation)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		} 
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_store = $("#cbo_store").val();
		var cbo_yarn_count = $("#cbo_yarn_count").val();
		var to_date = $("#txt_date_to").val();
		var title=$("div.form_caption" ).html();
		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_store="+cbo_store+"&cbo_yarn_count="+cbo_yarn_count+"&to_date="+to_date+"&title="+title;
		var data="action=generate_report"+dataString;
		freeze_window(operation);
		http.open("POST","requires/count_wise_monthly_yarn_status_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}

	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
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
			document.getElementById('scroll_body').style.overflow="auto"; 
			document.getElementById('scroll_body').style.maxHeight="250px";
	}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission);  ?>   		 
    <form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" > 
	    <h3 style="width:600px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
	    <div id="content_search_panel" style="width:100%;" align="center">
	        <fieldset style="width:600px;">
		        <legend>Search Panel</legend> 
				<table class="rpt_table" width="600" cellpadding="0" cellspacing="0" border="1" rules="all">
	                <thead>
	                    <tr>
	                        <th width="150" class="must_entry_caption">Company</th>                                
	                        <th width="140">Store Name</th>
	                        <th width="130">Count</th>
	                        <th class="must_entry_caption">As on Date</th>
	                        <th width="70"><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_field()" /></th>
	                    </tr>
	                </thead>
	                <tr class="general">
	                    <td align="center">
	                        <? 
	                           echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/count_wise_monthly_yarn_status_report_controller', this.value, 'load_drop_down_store', 'store_td' );" );
	                        ?>                            
	                    </td>                    
	                    <td align="center" id="store_td">
							<?
								echo create_drop_down( "cbo_store", 120, $blank_array,"",1, "-- Select --", $selected, "",0);
							?>
	                    </td>
	                    <td align="center">
	                    	<?
	                            echo create_drop_down( "cbo_yarn_count", 130, "select id,yarn_count from lib_yarn_count where is_deleted = 0 AND status_active = 1 ORDER BY yarn_count ASC","id,yarn_count", 1, "--Select--", 0, "",0 );
	                        ?>
	                    <td align="center">
	                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" value="<? echo date('d-m-Y');?>"/> 
	                    </td>
	                    <td>
	                        <input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:70px" class="formbutton" />
	                    </td>
	                </tr>
	            </table> 
		    </fieldset> 
	    </div>
	    <br /> 
        <!-- Result Contain Start -->
        	<div id="report_container" align="center"></div>
            <div id="report_container2"></div>         
        <!-- Result Contain END -->
    </form>    
</div>    
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>

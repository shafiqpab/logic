<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Party Wise Reconcilation Report.
Functionality	:	
JS Functions	:
Created by		:	Tajik 
Creation date 	: 	29-01-2018
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
echo load_html_head_contents("Date Wise Sub Contract Report", "../../", 1, 1,$unicode,'','');
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
			
	function fnc_report_generated()
	{
		if (form_validation('cbo_company_id*txt_date_from*txt_date_to','Comapny Name*From Date*To Date')==false) 
		{
		    return; 
		}
        else
        {
        	var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_item_category*cbo_party_id*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
			//alert(data);
			freeze_window(3);
			http.open("POST","requires/date_wise_subcon_status_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
        }		
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**"); 
			$('#report_container2').html(reponse[0]);
			setFilterGrid('list_views',-1);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body_1').style.overflow="auto";
		document.getElementById('scroll_body_1').style.maxHeight="none"; 
		//$(".flt").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body_1').style.overflowY="scroll"; 
		document.getElementById('scroll_body_1').style.maxHeight="400px";
		//$(".flt").show();
	}

	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}

	function show_popup_report_details(action,datas,width)
	{ 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_subcon_status_report_controller.php?action='+action+'&datas='+datas, 'Details', 'width='+width+',height=320px,center=1,resize=0,scrolling=0','../');
		
	}
	function show_popup_report_details(action,datas,width)
	{ 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_subcon_status_report_controller.php?action='+action+'&datas='+datas, 'Details', 'width='+width+',height=320px,center=1,resize=0,scrolling=0','../');
		
	}


</script>
</head>
<body onLoad="set_hotkey();">
<form id="greyStock_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
        <h3 style="width:750px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div id="content_search_panel" >      
        <fieldset style="width:750px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="150" class="must_entry_caption">Company</th>
                    <th width="150">Item Category</th>                    
                    <th width="150">Party</th>
                    <th width="100" class="must_entry_caption">From Date</th>
                    <th width="100" class="must_entry_caption">To Date</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                </thead>
                <tbody>
                    <tr class="general" >
                        <td>
                        	<? 
                        		echo create_drop_down( "cbo_company_id", 150, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/date_wise_subcon_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); 
                        	?>
                        </td>
                        <td>
                        	<? 
                        		echo create_drop_down( "cbo_item_category", 140, $item_category,"", 1, "--Select Item--",13,"", "","1,2,3,4,13,14,30" );
                        	?>
                        </td>
                        <td id="buyer_td">
                        	<? 
                                echo create_drop_down( "cbo_party_id", 150, $blank_array,"", 1, "--Select Party--", $selected, "",1,"" );
                            ?>
                        </td>

                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" ></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" ></td>

                        <td><input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fnc_report_generated();" /></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" align="center">
							<? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </tfoot>
           </table> 
           <br />
        </fieldset>
    	</div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
</form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

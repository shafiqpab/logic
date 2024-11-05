<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Woven Garments Price Quotation Entry.
Functionality	:	
JS Functions	:
Created by		:	Bilas 
Creation date 	: 	27-01-2013
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
//echo load_html_head_contents("Sample Info","../../", 1, 1, $unicode);
echo load_html_head_contents("Sample Info", "../../", 1, 1,$unicode,'','');
?>	

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  

var permission = '<? echo $permission; ?>';
var rel_path = '../../';	
function fn_report_generated()
{
	if (form_validation('cbo_company_name','Plsease Select Comapny')==false)//*txt_date_from*txt_date_to*Please Select From Date*Please Select To Date
	{
		return;
	}
	else
	{
		eval(get_submitted_variables('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to'));
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to',"../../");
		freeze_window(3);
		http.open("POST","requires/delete_order_list_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
}
	

function fn_report_generated_reponse()
{
 	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split("****");
		$('#report_container1').html(reponse[0]);
 		
		 var tableFilters = 
		 {
			col_0: "none",			
			col_18: "none",
			col_9: "select",
			//col_10: "select",
			display_all_text: " -- All --",
  			 
		}							
		setFilterGrid("table-body",-1,tableFilters);	
		
		show_msg('3');
		release_freezing();
		
 	}
}

function show_comment_info(job_no)
	{
		if(job_no)
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'po_comments.php?job_no='+job_no, 'Comment Details', 'width=500px,height=300px,center=1,resize=0,scrolling=0',' ../')	
		}	
	}

function new_window(type)
	{
		var report_div='';
		var scroll_div='';
		if(type==1)
		{
			report_div="print_report_samp";
			//scroll_div='scroll_body';
		}
		else if(type==2)
		{ 
			report_div="print_report_pp";
			//scroll_div='scroll_body2';
		}
 		
 		//document.getElementById(scroll_div).style.overflow="auto";
		//document.getElementById(scroll_div).style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById(report_div).innerHTML+'</body</html>');
		d.close();
		
		//document.getElementById(scroll_div).style.overflowY="scroll";
		//document.getElementById(scroll_div).style.maxHeight="380px";
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
	
	function submit_update(total_tr)
	{ 
		 var po_ids = "";  
		for(i=1; i<=total_tr; i++)
		{
			if ($('#pocheck_'+i).is(":checked"))
			{
				po_id = $('#pocheck_'+i).val();
				if(po_ids=="") po_ids= po_id; else po_ids +=','+po_id;
			}
		}
		//alert(po_ids)
		//return;
		if(po_ids=="")
		{
			alert("Please Select At Least One Po");
			return;
		}
		
		var data="action=update&po_ids="+po_ids;
		//freeze_window(1);
		http.open("POST","requires/delete_order_list_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=submit_update_Reply_info;
	}	
	
	function submit_update_Reply_info()
	{
		if(http.readyState == 4) 
		{ 
			var reponse=trim(http.responseText).split('**');	

			//show_msg(reponse[0]);
			
			if(reponse[0]==1)
			{
				fn_report_generated()
			}
			
			//release_freezing();	
		}
	}
	
	 
</script>

</head>
 
<body onLoad="set_hotkey();">
<form id="sample_approval_rpt">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",$permission);  ?>
       
         <fieldset style="width:800px;">
        	<legend>Search Panel</legend>
            <table class="rpt_table" width="700px" cellpadding="0" cellspacing="0" align="center">
               <thead>                    
                    <th>Company Name</th>
                    <th>Buyer Name</th>
                     <th id="search_text_td">Date</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                 </thead>
                <tbody>
                <tr class="general">
                    <td> 
                        <?
                            echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/delete_order_list_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                        ?>
                    </td>
                    <td id="buyer_td">
                        <? 
                            echo create_drop_down( "cbo_buyer_name", 160, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                        ?>
                    </td> 
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" >&nbsp; To
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date"  ></td>
                    <td>
                        <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated()" />
                    </td>
                </tr>
                </tbody>
            </table>
            <table>
            	<tr>
                	<td>
						<?
                    	$c_year= date("Y");
                    	$p_year=$c_year-15;
 						?>
						<select name="cbo_year_selection" id="cbo_year_selection"  style="width:80px" class="combo_boxes">
 							<? for($i=0;$i<21;$i++){ ?>
 								<option value=<? echo $p_year+$i;
								if ($c_year==$p_year+$i){?> selected <?php }?>><? echo $p_year+$i; ?> </option>
							<? } ?>
 						</select>
 						<? echo load_month_buttons(); ?>
                   	</td>
                </tr>
            </table> 
            <br />
        </fieldset>
    </div>
    
    <div id="report_container1" align="center"></div>
    <div id="report_container2" align="center"></div>
     <!--<div id="" align="center"><? echo load_submit_buttons( $permission, "fnc_order_entry_details", 0,0 ,"",1) ;?></div>-->
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

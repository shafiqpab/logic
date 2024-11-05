<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Production QC Report.
Functionality	:	
JS Functions	:
Created by		:	REZA 
Creation date 	: 	3-3-2017
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
echo load_html_head_contents("Production QC Report", "../../", 1, 1,$unicode,'','');

?>	
<script src="../../Chart.js-master/Chart.js"></script>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
			
	function fn_report_generated(type)
	{
		
		if($('#cbo_company_name').val()==0){
			var data='cbo_working_company_id*cbo_production_type*txt_date_from*txt_date_to';	
			var filed='Working Company Name*Production Type*From Date*To Date';	
		}
		else
		{
			var data='cbo_company_name*cbo_production_type*txt_date_from*txt_date_to';	
			var filed='Company Name*Production Type*From Date*To Date';	
		}
		
		
		
		if( form_validation(data,filed)==false )
		{
			return;
		}
		else
		{
			var report_title=$( "div.form_caption" ).html();
			var from_date = $('#txt_date_from').val();
			var to_date = $('#txt_date_to').val();
			var datediff = date_diff( 'd', from_date, to_date )+1;
			
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_working_company_id*cbo_production_type*cbo_buyer_name*cbo_location*cbo_floor*txt_date_from*txt_date_to',"../../")+"&report_title="+report_title+"&type="+type+"&datediff="+datediff;
			freeze_window(3);
			http.open("POST","requires/daily_qc_analysis_report_controller.php",true);
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
			/*if(reponse[2]==1)
			{
				document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			}
			else
			{*/
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
			//}
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		var canvas1  = document.getElementById("canvas"); 
		var dataUrl1 = canvas1.toDataURL("image/png");
		$("#canvas_div").css("display","block");
		$("#canvas_div").html('<img src="'+dataUrl1+'"/>');

		var canvas2  = document.getElementById("canvas2"); 
		var dataUrl2 = canvas2.toDataURL("image/png");
		$("#canvas2_div").css("display","block");
		$("#canvas2_div").html('<img src="'+dataUrl2+'"/>');
 		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="none";
		$("#canvas_div").css("display","none");
		$("#canvas2_div").css("display","none");
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
	
	function fn_disable_com(str){
		if(str==2){$("#cbo_company_name").attr('disabled','disabled');}
		else{ $('#cbo_company_name').removeAttr("disabled");}
		if(str==1){$("#cbo_working_company_id").attr('disabled','disabled');}
		else{ $('#cbo_working_company_id').removeAttr("disabled");}
	}
	 
</script>
</head>
<body onLoad="set_hotkey();">
<form id="sewingQcReport_1">
    <div style="width:1050px; margin:1px auto;">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1050px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:950px" >    
         <fieldset style="width:1050px;">
            <table class="rpt_table" width="1050" cellpadding="0" cellspacing="0" align="center" rules="all" border="1">
               <thead>                    
                    <tr>
                        <th class="must_entry_caption">Company Name</th>
                        <th class="must_entry_caption">Working Comapny</th>
                        <th class="must_entry_caption">Production Type</th>
                        <th>Buyer Name</th>
                        <th>Location</th>
                        <th>Floor</th>
                        <th class="must_entry_caption">Production Date</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:90px" value="Reset" onClick="fn_disable_com(0)"/></th>
                    </tr>    
                 </thead>
                <tbody>
                <tr class="general">
                    <td width="150" align="center"> 
                        <?
                            echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/daily_qc_analysis_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/daily_qc_analysis_report_controller', this.value, 'load_drop_down_location', 'location_td' );fn_disable_com(1)" );
                        ?>
                    </td>
                    <td width="150" align="center"> 
                        <?
                            echo create_drop_down( "cbo_working_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/daily_qc_analysis_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/daily_qc_analysis_report_controller', this.value, 'load_drop_down_location', 'location_td' );fn_disable_com(2)" );
                        ?>
                    </td>
                    <td width="100" align="center">
                        <? 
                            echo create_drop_down( "cbo_production_type", 100, $production_type,"", 1, "--Select Type--", $selected, "","","1,5,8,11" );
                        ?>
                    </td>
                    <td width="110" id="buyer_td" align="center">
                        <? 
                            echo create_drop_down( "cbo_buyer_name", 110, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td width="110" id="location_td" align="center">
                    	<? 
                            echo create_drop_down( "cbo_location", 110, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td width="110" id="floor_td" align="center">
                    	<? 
                            echo create_drop_down( "cbo_floor", 110, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td>
                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" value="<? echo date("d-m-Y",time());?>" >&nbsp; To
                    	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" value="<? echo date("d-m-Y",time());?>"  >
                    </td>
                    <td width="100">
                        <input type="button" id="show_button" class="formbutton" style="width:90px" value="Show" onClick="fn_report_generated(1)" />
                    </td>
                </tr>
                </tbody>
            </table>
            <table width="1050">
            	<tr>
                	<td colspan="8" width="800" align="center">
 						<? echo load_month_buttons(1); ?>
                   	</td>
                </tr>
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
<script>
	$('#cbo_location').val(0);
</script>
</html>

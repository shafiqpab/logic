<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Production QC Report.
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	21-09-2015
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
		
		var job_no = $('#txt_job_no').val();
		var int_ref = $('#txt_int_ref').val();

		if($('#cbo_company_name').val()==0)
		{
			if(job_no!="" || int_ref!="")
			{
				var data='cbo_working_company_id*cbo_production_type';	
				var filed='Working Company Name*Production Type';	
			}
			else
			{
				var data='cbo_working_company_id*cbo_production_type*txt_date_from*txt_date_to';	
				var filed='Working Company Name*Production Type*From Date*To Date';
			}	
		}
		else
		{	
			if(job_no!="" || int_ref!="")
			{
				var data='cbo_company_name*cbo_production_type';	
				var filed='Company Name*Production Type';	
			}
			else
			{
				var data='cbo_company_name*cbo_production_type*txt_date_from*txt_date_to';	
				var filed='Company Name*Production Type*From Date*To Date';	
			}
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

			if(type=='2')
			{
				if(from_date=="" || to_date=="")
				{
					alert('Please enter production date.'); 
					$('#txt_date_from').focus();
					return;
				}
			}
			
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_working_company_id*cbo_production_type*cbo_buyer_name*cbo_location*cbo_floor*txt_job_no*txt_int_ref*txt_date_from*txt_date_to*cbo_line_id*txt_order_no*txt_style_ref',"../../")+"&report_title="+report_title+"&type="+type+"&datediff="+datediff;
			freeze_window(3);
			http.open("POST","requires/production_qc_report_controller.php",true);
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
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="none";
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
    <div style="width:1260px; margin:1px auto;">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1260px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:950px" >    
         <fieldset style="width:1260px;">
            <table class="rpt_table" width="1260" cellpadding="0" cellspacing="0" align="center" rules="all" border="1">
               <thead>                    
                    <tr>
                        <th width="120" class="must_entry_caption">Company Name</th>
                        <th width="120" class="must_entry_caption">Working Comapny</th>
                        <th width="80">Location</th>
                        <th width="70">Floor</th>
                        <th width="90" class="must_entry_caption">Production Type</th>
                        <th width="70">Line No</th>
                        <th width="80">Buyer Name</th>
                        <th width="70">Job No</th>
                        <th width="70">Order No</th>
                        <th width="70">Style Ref.</th>
                        <th width="70">Int. Ref.</th>
                        <th width="150" class="must_entry_caption">Production Date</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:90px" value="Reset" onClick="fn_disable_com(0)"/></th>
                    </tr>    
                 </thead>
                <tbody>
                <tr class="general">
                    <td width="120" align="center"> 
                        <?
                            echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/production_qc_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );fn_disable_com(1)" );
                        ?>
                    </td>
                    <td width="120" align="center"> 
                        <?
                            echo create_drop_down( "cbo_working_company_id", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/production_qc_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );fn_disable_com(2)" );
                        ?>
                    </td>
                    <td width="80" id="location_td" align="center">
                    	<? 
                            echo create_drop_down( "cbo_location", 80, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td width="70" id="floor_td" align="center">
                    	<? 
                            echo create_drop_down( "cbo_floor", 70, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td width="90" align="center">
                        <? 
                            echo create_drop_down( "cbo_production_type", 90, $production_process,"", 1, "--Select Type--", $selected, "load_drop_down( 'requires/production_qc_report_controller', this.value+'_'+$('#cbo_working_company_id').val(), 'load_drop_down_location', 'location_td' );","","1,5,11,13" );
                        ?>
                    </td>
					<td id="line_td">
                        <? 
                            echo create_drop_down( "cbo_line_id", 70, $blank_array,"", 1, "-- Select Line --",  $selected, "",1,"" );
                        ?>                            
                    </td>
                    <td width="80" id="buyer_td" align="center">
                        <? 
                            echo create_drop_down( "cbo_buyer_name", 80, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td width="70">
                    	<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:80px" placeholder="Job No">
                    </td>
                    <td width="70">
                    	<input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:80px" placeholder="Order No">
                    </td>
                    <td width="70">
                    	<input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:80px" placeholder="Style Ref">
                    </td>
                    <td width="70">
                    	<input type="text" name="txt_int_ref" id="txt_int_ref" class="text_boxes" style="width:80px" placeholder="Int Ref">
                    </td>
                    <td>
                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date">To
                    	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date">
                    </td>
                    <td width="120">
                        <input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated(1)" />
                        <input type="button" id="show_button" class="formbutton" style="width:50px" value="WVN" onClick="fn_report_generated(3)" />
                    </td>
                </tr>
                </tbody>
            </table>
            <table width="1260">
            	<tr>
                	<td colspan="9" width="800" align="center">
 						<? echo load_month_buttons(1); ?>
                   	</td>
                    <td width="100" align="center">
                    	<input type="button" id="show_button" class="formbutton" style="width:90px" value="Monthly" onClick="fn_report_generated(2)" />
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

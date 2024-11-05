<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Team Wise Order Status Report.
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam Reza
Creation date 	: 	02-11-2020
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
extract($_REQUEST);

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Team Wise Order Status","../../../", 1, 1, $unicode,1,1);
?>	
<script>
 	if( $('#index_page', window.parent.document).val()!=1){window.location.href = "../../../logout.php"; } 
	
	function fn_generate_report(type)
	{
		if(form_validation('txt_start_date*txt_end_date','Start Date*End Date')==false)
		{
			return;
		}
		else
		{	
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_team_leader*cbo_buyer_name*cbo_category_by*txt_start_date*txt_end_date*cbo_status',"../../../")+'&type='+type;
			freeze_window(3);
			http.open("POST","requires/team_wise_order_status_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_generate_report_reponse;
		}
	}
		
	function fn_generate_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####");
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

		let filter = 0;
		if ($("#scroll_body table tr:first").attr('class')=='fltrow')
		{
			filter = 1;
			$("#scroll_body table tr:first").hide();
		} 

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		if(filter == 1)
		{
			$("#scroll_body table tr:first").show();
		}
		// $('#scroll_body tr:first').show();
		//document.getElementById('scroll_body').style.maxWidth="120px";
	}
	
	function getBuyerId() 
	{	 
	    let company_id = $('#cbo_company_id').val();
		// console.log(company_id); 
	    if(company_id != '')
	    {
	        var data="action=load_drop_down_buyer&company_id="+company_id;
	        http.open("POST","requires/team_wise_order_status_controller.php",true);
	        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	        http.send(data); 
	        http.onreadystatechange = function(){
	            if(http.readyState == 4) {
	                var response = trim(http.responseText);
	                $('#buyer_td').html(response);
	                set_multiselect('cbo_buyer_name','0','0','','0');
	            }			 
	        };
	    }         
	}
    
	
</script>
</head>
 
<body onLoad="set_hotkey();">
		 
<form id="cost_breakdown_rpt">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 style="width:1060px;" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel"> 
            <fieldset style="width:1060px;">
                <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                	<thead>
                    	<tr>                   
                            <th>Company</th>
                            <th>Team Leader</th>
                            <th>Buyer</th>
                            <th class="must_entry_caption">Date Category</th>
                            <th colspan="2" class="date_caption must_entry_caption">PHD/PCD Date</th>
                            <th>Order Status</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr>
                        <td align="center" id="company_td"> 
                           <?
								echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "-- Select Company --", $selected, "" );
                            ?>
                        </td>
                       
                        <td align="center">
							<? 
							echo create_drop_down( "cbo_team_leader", 150, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 0, "-- Select Team --", $selected, "",0);// project_type=2 and 
	                		?>
                        </td>
						<td id="buyer_td">
							<? 
                            echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "--All Buyer--", $selected, "",0,"" );
                            ?>
                        </td>
						<td align="center">
							<? 
							$date_catogory = ['1'=>'PHD/PCD Date','2'=>'Pub Ship Date','3'=>'Shipment Date','4'=>'Country Ship Date'];
							echo create_drop_down( "cbo_category_by", 100, $date_catogory,"", 0, "-- Select --", date('m'), "$('.date_caption').text($('#cbo_category_by option:selected').text()).css('color','blue')",0,"" );
                            ?>
                        </td>
                        <td align="center">
							<input type="text" name="txt_start_date" id="txt_start_date" style="width:80px"
								value="" class="datepicker"  />
                        </td>
						<td align="center">
						<input type="text" name="txt_end_date" id="txt_end_date" style="width:80px"
								value="" class="datepicker"  />
                        </td>
                        <td align="center">
                            <? 
							echo create_drop_down( "cbo_status", 100, array(1=>'Confirm',2=>'Projection',3=>'Sub-Con'),"", 0, "- All -", $selected, "" );  
                            ?>
                        </td>
                         <td >
                           <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_generate_report(1)" />
                           <input type="button" id="show_button" class="formbutton" style="width:70px" value="Team Wise" onClick="fn_generate_report(2)" />
                           <input type="button" id="show_button" class="formbutton" style="width:70px" value="Buyer Wise" onClick="fn_generate_report(3)" />
                        </td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
   
 </form>    
</body>
<script>
	set_multiselect('cbo_company_id','0','0','','0');
	set_multiselect('cbo_team_leader','0','0','','0');
	set_multiselect('cbo_buyer_name','0','0','','');
	set_multiselect('cbo_status','0','0','','');
	setTimeout[($("#company_td a").attr("onclick","disappear_list(cbo_company_id,'0');getBuyerId();") ,3000)];
</script>

<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

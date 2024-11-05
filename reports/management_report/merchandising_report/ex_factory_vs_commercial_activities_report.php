<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	Knitting Production QC Report.
Functionality	:	
JS Functions	:
Created by		:	Kaiyum
Creation date 	: 	04-11-2017
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
echo load_html_head_contents("Knitting Production QC Report","../../../", 1, 1, $unicode,0,0);
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	
	
	

	function fn_report_generated(type)
	{
		var company=$('#cbo_company_name').val();
		var del_company=$('#cbo_knitting_company').val();
		
		if(company==0 && del_company==0){
			if (form_validation('cbo_company_name','Comapny Name')==false)
			{
				release_freezing();
				return;
			}
		}
		
			
			
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_knitting_company*cbo_location_name*cbo_year*cbo_del_floor',"../../../");
			freeze_window(3);
			http.open("POST","requires/ex_factory_vs_commercial_activities_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
		
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			release_freezing();
			var reponse=trim(http.responseText).split("####");
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>';
	 		show_msg('3');
	 		//&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>
		}
	}

	
	function new_window()
	{
		
		//$('#table_body tr:first').hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body><script src="../../../Chart.js-master/Chart.js"><\/script></html>');
		d.close(); 
		
		//$('#table_body tr:first').show();
	}
	

	

</script>

</head>
 
<body onLoad="set_hotkey();">
 <div style="width:100%;" align="center">
<form id="monthly_ex_factory" name="monthly_ex_factory">
    <div style="width:800px;" align="center">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:800px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:800px;">
                <table class="rpt_table" width="780" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                    	<tr>                   
                            <th width="150">Company Name</th>
                            <th width="150">Working Company</th>
                            <th width="150">Location</th>
                            <th width="100">Floor</th>
                            <th width="60">Year</th>
                            <th><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:100px" onClick="reset_form('','report_container*report_container2','','','')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td align="center"> 
							<?
                            echo create_drop_down("cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/ex_factory_vs_commercial_activities_report_controller', this.value, 'load_drop_down_location', 'location' );$('#cbo_knitting_company').val(0);");
                            ?>
                         </td>
                        <td id="knitting_com">
                            <?
                                echo create_drop_down( "cbo_knitting_company", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Delivery Company --", $selected,"load_drop_down( 'requires/ex_factory_vs_commercial_activities_report_controller', this.value, 'load_drop_down_location', 'location' );$('#cbo_company_name').val(0);" );
                            ?>
                        </td>
                        <td id="location">
							 <? 
	                            echo create_drop_down( "cbo_location_name", 172, $blank_array,"", 1, "-- Select --", $selected, "" );
	                         ?>	  
	                    </td>
                       
                        <td id="del_floor_td">
                        <? echo create_drop_down( "cbo_del_floor", 120, $blank_array,"", 1, "-- Select Delivery Floor --", $selected, "" );?>
                      </td>
                        <td>
                        	<?
								echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
							?>
                        </td>
                      <td>
                        <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated(1);" />
                      </td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
     </form>

    <!--<div id="report_container" align="center"></div>-->
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
    <div style="display:none" id="data_panel"></div>  
 </div>    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

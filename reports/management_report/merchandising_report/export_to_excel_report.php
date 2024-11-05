<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Export To Excel Report
				
Functionality	:	
JS Functions	:
Created by		:	Aziz 
Creation date 	: 	29/11/2015
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
echo load_html_head_contents("Export to Excel Info","../../../", 1, 1, $unicode);
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	function fn_report_generated1(rptid)
	{
		var from_date=$('#txt_date_from').val();
		var to_date=$('#txt_date_to').val();
		//alert(rptid);return;
		var type=rptid;
		if(type==3)
		{
			document.getElementById('ship_date_th').innerHTML='Ex Factory Date';
			$('#ship_date_th').css('color','blue');
		}
		else
		{
			document.getElementById('ship_date_th').innerHTML='Shipment Date';
			$('#ship_date_th').css('color','blue');	
		}
		if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		//var report_id=rptid.split(",");
		
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title+'&type='+type;
			//alert(data);return;
			freeze_window(3);
			http.open("POST","requires/export_to_excel_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_report_reponse1;  
		
	}
	
	function generate_report_reponse1()
	{	
		if(http.readyState == 4) 
		{	 
			var response=trim(http.responseText).split("####");
			//$("#report_container2").html(response[0]);
			//alert(response[0]);
			if(response!='')
			{
			$('#aa1').removeAttr('href').attr('href','requires/'+response[0]);
			//$('#aa1')[0].click();
			 document.getElementById('aa1').click();
			}
			show_msg('3');
			release_freezing();
			var rptid='';
			//fn_report_generated2(rptid);
		}
	}
	
	/*function fn_report_generated2(rptid)
	{
		var type=''
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		//var report_id=rptid.split(",");
		
			var data="action=report_generate2"+get_submitted_data_string('cbo_company_id',"../../../")+'&report_title='+report_title+'&type='+type;
			http.open("POST","requires/export_to_excel_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_report_reponse2;  
		
	}
	
	function generate_report_reponse2()
	{	
		if(http.readyState == 4) 
		{	 
			var response=trim(http.responseText).split("####");
			$("#report_container3").html(response[0]);
			$('#aa2').removeAttr('href').attr('href','requires/'+response[1]);
			show_msg('3');
		}
	}*/
	
		
	function print_button_setting()
	{
		/*if( form_validation('txt_date_from*txt_date_to','From Date*To Date')==false )
			{
				//alert('select date Range');
				return;
			}*/
		
		$('#button_container').html('');
		get_php_form_data($('#cbo_company_id').val(),'print_button_variable_setting','requires/export_to_excel_report_controller' );
		///fn_report_generated(1); 
	}
	
	function print_report_button_setting(report_ids) 
	{
		//alert(report_ids);
		if(report_ids=='')
		{
		 alert('No Found any Excel Button,Please Go to (Variable Settings) Report Setting');
		 return;	
		}
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==30) //Projection Temp MM
			{
				//alert(report_id[k]);
				$('#button_container').append( '<input type="button"  id="project_temp" name="project_temp" onClick="fn_report_generated1(1)" class="formbutton" style="width:140px;" value="Projection Temp MM" /><a   id="aa1" href="" style="text-decoration:none" download hidden>BB</a>&nbsp;&nbsp;&nbsp;' );
				
			}
			if(report_id[k]==31) // Plan Vs Ex-Factory
			{
				
					$('#button_container').append( '<input type="button"  id="project_temp2" name="project_temp2" onClick="fn_report_generated1(2)" class="formbutton" style="width:140px;" value="Plan Vs Ex-Factory" /><a   id="aa2" href="" style="text-decoration:none" download hidden>CC</a>&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==32) //  Ex-Factory Vs Plan
			{
					$('#button_container').append( '<input type="button"  id="project_temp3" name="project_temp3" onClick="fn_report_generated1(3)" class="formbutton" style="width:140px;" value="Ex-Factory Vs Plan" /><a   id="aa2" href="" style="text-decoration:none" download hidden>DD</a>&nbsp;&nbsp;&nbsp;' );
			
			}
			if(report_id[k]==33) //  Both Plan Vs Ex-Factory 
			{
					$('#button_container').append( '<input type="button"  id="project_temp4" name="project_temp4" onClick="fn_report_generated1(4)" class="formbutton" style="width:140px;" value="Both Plan Vs Ex-Fact" /><a   id="aa2" href="" style="text-decoration:none" download hidden>EE</a>&nbsp;&nbsp;&nbsp;' );
			
			}
			if(report_id[k]==49) //  Fabrics
			{
					$('#button_container').append( '<input type="button"  id="project_temp5" name="project_temp4" onClick="fn_report_generated1(5)" class="formbutton" style="width:140px;" value="Fabric" /><a   id="aa2" href="" style="text-decoration:none" download hidden>EE</a>&nbsp;&nbsp;&nbsp;' );
			
			}
		}
			

	}
</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../");  ?>
    <form name="shipmentpending_1" id="shipmentpending_1" autocomplete="off" > 
        <h3 style="width:440px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
        <fieldset style="width:440px" >
            <table class="rpt_table" width="440" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                	
                	<th class="must_entry_caption">Company Name</th>
                    <th id="ship_date_th" class="must_entry_caption">Shipment Date</th>
                    <!--<th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" /></th>-->
                </thead>
                <tr class="general">
                 	<td>
						<?
							echo create_drop_down( "cbo_company_id", 172, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "print_button_setting();" );
                        ?> 
                        <input type="hidden" id="report_ids" name="report_ids"/>                                    
                    </td>
                    <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" >&nbsp; To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date"  >
                     </td>
                   
                   <!-- <td>
                    	<input type="button" name="show" id="show" onClick="generate_report(1);" class="formbutton" style="width:80px" value="Show" />
                    </td>-->
                </tr>
            </table>
        </fieldset>
        </div>
    </form>
    <br/><br/><br/><br/>
    <div id="button_container" align="center"></div>
	<div id="report_container" align="center"></div>
    <div id="report_container4" align="center"></div>
    
    <div id="report_container2"></div>
    <div id="report_container3"></div>      
    </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
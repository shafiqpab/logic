<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Order Forecast Report.
Functionality	:	
JS Functions	:
Created by		:	Aziz
Creation date 	: 	03-01-2015
Updated by 		:   Md. Saidul Islam Reza		
Update date		: 	25-03-2015	   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order  Forcast Report","../../", 1, 1, $unicode,1,1);
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	
	var tableFilters = 
	 {
		col_30: "none",
		col_operation: {
		id: ["total_buyer_po_quantity","value_total_buyer_po_value","parcentages","total_current_ex_Fact_Qty","value_total_current_ex_fact_value","mt_total_ex_fact_qty","value_mt_total_ex_fact_value"],
	    col: [2,3,4,5,6,7,8],
	    operation: ["sum","sum","sum","sum","sum","sum","sum"],
	    write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 } 
	 
	function fn_report_generated(type)
	{		


		
		if(form_validation('cbo_company_name*cbo_month_from*cbo_year_from*cbo_month_to*cbo_year_to','Company Name*From Month*From Year*To Month*To Year')==false)
		{
			return;
		}
		else
		{
			if(type==1)
			{
				var is_checked = document.getElementById("million").checked;
				if (is_checked) var is_checked=1; else var is_checked = 0;
				var data="action=report_generate&reportType="+type+"&is_checked="+is_checked+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_agent*cbo_team_leader*cbo_month_from*cbo_year_from*cbo_month_to*cbo_year_to*cbo_date_cat_id',"../../");
			}
			else if(type==2)
			{
				var data="action=report_generate_2&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_agent*cbo_team_leader*cbo_month_from*cbo_year_from*cbo_month_to*cbo_year_to*cbo_date_cat_id',"../../");
			}
			else if(type==3)
			{
				var data="action=report_generate_3&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_agent*cbo_team_leader*cbo_month_from*cbo_year_from*cbo_month_to*cbo_year_to*cbo_date_cat_id',"../../");
			}
			else if(type==4)
			{
				var data="action=report_generate_4&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_agent*cbo_team_leader*cbo_month_from*cbo_year_from*cbo_month_to*cbo_year_to*cbo_date_cat_id',"../../");
			}
			//alert(data);return;
			freeze_window(3);
			http.open("POST","requires/order_forecast_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");

			$("#report_container2").html(response[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		setFilterGrid("table_body",-1);
			
	 		show_msg('3');
			release_freezing();
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	    '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="330px";
		$("#table_body tr:first").show();
	}	
	
	function change_colors(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
			document.getElementById(v_id+'1').bgColor=e_color;
			document.getElementById(v_id+'2').bgColor=e_color;
			document.getElementById(v_id+'3').bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
			document.getElementById(v_id+'1').bgColor="#33CC00";
			document.getElementById(v_id+'2').bgColor="#33CC00";
			document.getElementById(v_id+'3').bgColor="#33CC00";
		}
	}

	function print_report_button_setting(report_ids)
	{
		$('#show_button1').hide();
		$('#show_button2').hide();
		$('#show_button3').hide();
		$('#show_button4').hide();
	
		var report_id=report_ids.split(",");
		report_id.forEach(function(items)
		{
			if(items==108){$('#show_button1').show();}
			else if(items==195){$('#show_button2').show();}
			else if(items==242){$('#show_button3').show();}
			else if(items==359){$('#show_button4').show();}
		});
	}

	// function print_report_button_setting(report_ids) 
	// {
	// 	$('#show_button1').hide();
	// 	$('#show_button2').hide();
	// 	$('#show_button3').hide();
	// 	$('#show_button4').hide();
	// 	alert(report_ids);
	// 	var report_id=report_ids.split(",");
	// 	report_id.forEach(function(items){
	// 		if(items==108){$('#show_button1').show();}
	// 		else if(items==195){$('#show_button2').show();}
	// 		else if(items==242){$('#show_button3').show();}
	// 		else if(items==359){$('#show_button4').show();}
	// 	});
	// }

	// function fnc_load_report_format()
    // {
    //     var data=$('#cbo_company_name').val();
    //     var report_ids = return_global_ajax_value( data, 'load_report_format', '', 'requires/order_forecast_report_controller');
    //     print_report_button_setting(report_ids);
    // }

	// function print_report_button_setting(report_ids)
    // {
    //     if(trim(report_ids)=="")
    //     {
    //         $("#show_button1").show();
    //         $("#show_button2").show();
    //         $("#show_button3").show();
    //         $("#show_button4").show();
    //      }
    //     else
    //     {
    //         var report_id=report_ids.split(",");
    //         $("#show_button1").hide();
    //         $("#show_button2").hide();
    //         $("#show_button3").hide();
    //         $("#show_button4").hide();
    //          for (var k=0; k<report_id.length; k++)
    //         {
    //             if(report_id[k]==108)
    //             {
    //                 $("#show_button1").show();
    //             }
    //             else if(report_id[k]==195)
    //             {
    //                 $("#show_button2").show();
    //             }
    //             else if(report_id[k]==242)
    //             {
    //                 $("#show_button3").show();
    //             }
    //             if(report_id[k]==359)
    //             {
    //                 $("#show_button4").show();
    //             }

    //         }
    //     }


    // }

	// function print_button_setting()
	// {
	// 	//$('#data_panel').html('');
	// 	get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/order_forecast_report_controller' ); 
	// }
	// function print_report_button_setting(report_ids) 
	// {
	// 	$('#show_button1').hide();
	// 	$('#show_button2').hide();
	// 	$('#show_button3').hide();
	// 	$('#show_button4').hide();
	// 	var report_id=report_ids.split(",");
	// 	for (var k=0; k<report_id.length; k++)
	// 	{
	// 		if(report_id[k]==108)
	// 		{
	// 			$('#show_button1').show();
	// 		}
	// 		if(report_id[k]==195)
	// 		{
	// 			$('#show_button2').show();
	// 		}
	// 		if(report_id[k]==242) //Size wise
	// 		{
	// 			$('#show_button3').show();
	// 		}
	// 		if(report_id[k]==359)
	// 		{
	// 			$('#show_button4').show();
	// 		}
	// 	}
	// }
	
	function getBuyerId() 
	{
	    var company_name = document.getElementById('cbo_company_name').value;
		//alert(company_name)
	    if(company_name !='') {
		  var data="action=load_drop_down_buyer&data="+company_name;
		  //alert(data);die;
		  http.open("POST","requires/order_forecast_report_controller.php",true);
		  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		  http.send(data); 
		  http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#buyer_td').html(response);
				  
				  set_multiselect('cbo_buyer_name','0','0','','0');
	          }			 
	      };
	    }    
		get_php_form_data(company_name,'print_button_variable_setting','requires/order_forecast_report_controller' );     
	}	
	
</script>

</head>
 
<body onLoad="set_hotkey();">
<div style="width:1160px" align="left">
<form id="monthly_ex_factory" name="monthly_ex_factory">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:1110px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:1110px;">
                <table class="rpt_table" width="1100" cellpadding="1" cellspacing="2" align="center" rules="all">
                	<thead>
                    	<tr>                   
                            <th width="100" class="must_entry_caption">Company Name</th>
                             <th width="100">Buyer Name</th>
                             <th width="125">Agent Name</th>
                             <th width="125">Team Leader</th>
                             <th width="110">Date Category</th>
                            <th width="250" colspan="5" class="must_entry_caption">Month Range</th>
                            <th>
                            	Million<input type="checkbox" name="million" id="million" class="formbutton" style="width:20px"/>
                            	<input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:80px" onClick="reset_form('','report_container*report_container2','','','')" />
                            </th>
                        </tr>
                     </thead>
                     <tbody>
                         <tr>
                            <td id="lccompany_td"><? echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "get_php_form_data(this.value,'print_button_variable_setting','requires/order_forecast_report_controller' );" ); ?></td>
                            <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
                            <td id="agent_td"><? echo create_drop_down( "cbo_agent", 120, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id  and a.id in (select  buyer_id from lib_buyer_party_type where party_type in (20,21)) group by a.id,a.buyer_name order by a.buyer_name","id,buyer_name", 1, "-- Select --", 0, "",0 ); ?></td>
                            <td><? echo create_drop_down( "cbo_team_leader", 120, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select --", 0, "",0 ); ?></td>
                            <td>
									<? 
									$date_category_arr=array(1=>'Pub Ship Date',2=>'Country Ship Date',3=>'Actual Ship Date'); //
									echo create_drop_down( "cbo_date_cat_id", 100, $date_category_arr,"", 0, "-- Select --", 1, "",0,"" ); ?>
                            </td>
                            <td> 
                                <?
                                    $selected_month=date("m");
                                    echo create_drop_down( "cbo_month_from", 80, $months,"", 1, "--Month--", 0, "",0 );
                                ?>
                            </td>
                            <td> 
                                <?
                                    $selected_year=date("Y");
                                    echo create_drop_down( "cbo_year_from", 60, $year,"", 1, "--Year--", $selected_year, "",0 );
                                ?>
                            </td>
                            <td> 
                                <?
                                    $selected_month=date("m");
                                    echo create_drop_down( "cbo_month_to", 80, $months,"", 1, "--Month--", 0, "",0 );
                                ?>
                            </td>
                            <td>
                                 <?
                                    $selected_year=date("Y");
                                    echo create_drop_down( "cbo_year_to", 60, $year,"", 1, "--Year--", $selected_year, "",0 );
                                ?>
                            </td>
                            <td colspan="2">
                                <input type="button" id="show_button1" name="show_button1" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated(1);" />
                                <input type="button" id="show_button2" name="show_button2" class="formbutton" style="width:50px" value="Show 2" onClick="fn_report_generated(2);" />
                                <input type="button" id="show_button3" name="show_button3" class="formbutton" style="width:50px" value="Show 3" onClick="fn_report_generated(3);" />
								<input type="button" id="show_button4" name="show_button4" class="formbutton" style="width:50px" value="Show 4" onClick="fn_report_generated(4);" />
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
<script>
	set_multiselect('cbo_company_name','0','0','0','0');
	setTimeout[($("#lccompany_td a").attr("onclick","disappear_list(cbo_company_name,'0'); getBuyerId();"),3000)]; 
	set_multiselect('cbo_buyer_name','0','0','','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>



</html>

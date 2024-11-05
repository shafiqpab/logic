<?
/*-------------------------------------------- Comments
Version                  :   V1
Purpose			         : 	This form will create  order In Hand Report
Functionality	         :
JS Functions	         :
Created by		         :	Aziz
Creation date 	         :  18 Sep,2023
Requirment Client        :
Requirment By            :
Requirment type          :
Requirment               :
Affected page            :
Affected Code            :
DB Script                :
Updated by 		         :
Update date		         :
QC Performed BY	         :
QC Date			         :
Comments		         : From this version oracle conversion is start
						    
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------

echo load_html_head_contents("Order In Hand Capacity","../../../", 1, 1, $unicode,1,1);
 

?>
 

<!--For Graph end-->
<script src="../../../ext_resource/hschart/hschart.js"></script>
<script type="text/javascript">
  var permission='<? echo $permission; ?>';
 if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";
 function fn_report_generated(type)
	{

		var company_name = $("#cbo_company_name").val();
		var cbo_year = $("#cbo_year").val();
        var cbo_buyer = $("#cbo_buyer").val();


			if(company_name==0)
			{
				if(form_validation('cbo_company_name','Company Name')==false)
				{
					return;
				}
			}

		if(form_validation('cbo_year_name*cbo_month*cbo_end_year_name*cbo_month_end','Start Year*Start Month*End Year*End Month')==false)
		{
			return;
		}
		else
		{
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_year_name*cbo_month*cbo_month_end*cbo_end_year_name*cbo_buyer*cbo_year',"../../../")+'&report_title='+report_title+'&type='+type;
			freeze_window(3);
			http.open("POST","requires/order_in_hand_summary_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("####");
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);


		document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';

			show_msg('3');
			release_freezing();
            hs_chart("["+reponse[2]+"]","["+reponse[3]+"]");
            hs_chart_min("["+reponse[2]+"]","["+reponse[3]+"]","["+reponse[4]+"]");
		}
	}

	function fnc_load_report_format()
    {
       // var data=$('#cbo_company_name').val();
       // var report_ids = return_global_ajax_value( data, 'load_report_format', '', 'requires/order_in_hand_summary_controller');
       // print_report_button_setting(report_ids);
    }




    function print_report_button_setting(report_ids)
    {
        if(trim(report_ids)=="")
        {
            $("#search1").show();
           
         }
        else
        {
            var report_id=report_ids.split(",");
            $("#search1").hide();
           
             for (var k=0; k<report_id.length; k++)
            {
                if(report_id[k]==108)
                {
                    $("#search1").show();
                }
                 

            }
        }


    }

function new_window()
{
	//document.getElementById('scroll_body').style.overflow='auto';
	//document.getElementById('scroll_body').style.maxHeight='none';
	//$("#table_body tr:first").hide();
	//$("#table_body1 tr:first").hide();
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close();

	//document.getElementById('scroll_body').style.overflowY='scroll';
	//document.getElementById('scroll_body').style.maxHeight='300px';
	//$("#table_body tr:first").show();
}

function fn_buyer_com(){
        var cbo_company_name = $("#cbo_company_name").val();
		load_drop_down( 'requires/order_in_hand_summary_controller', cbo_company_name, 'load_drop_down_buyer', 'buyer_td' );
		set_multiselect('cbo_buyer','0','0','','0','');
	}

//Loading %, In Minutes


    function hs_chart(capacityVal,Month){
	
	$('#container').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Loading %, In Minutes'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories:eval(Month),
            title: {
                text: null
            }
        },
        yAxis: {
            min: 0,
            title: {
                align: 'high'
            },
            labels: {
                overflow: 'justify'
            }
        },
        tooltip: {
            valueSuffix: '',
			backgroundColor: 'rgba(219,219,216,0.8)',
			borderWidth: 0
        },
		
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true
                }
            }
        },
        credits: {
            enabled: false
        },
        series: [{
            name: 'In Hand Minutes%',
            data: eval(capacityVal)
        }]
    });
		
}	
 	

function hs_chart_min(capacityVal,Month,orderInhand){
	var cur="M";	
	var msg="Total Capacity Min"
    $('#container_min').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Orde In Hand Minutes'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories:eval(Month),
            title: {
                text: null
            }
        },
        yAxis: {
            min: 0,
            title: {
                align: 'high'
            },
            labels: {
                overflow: 'justify'
            }
        },
        tooltip: {
            valueSuffix: '',
			backgroundColor: 'rgba(219,219,216,0.8)',
			borderWidth: 0
        },
		
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true
                }
            }
        },
        credits: {
            enabled: false
        },
        series: [{
            name: 'Order In Hand Minutes',
            data: eval(orderInhand),
				tooltip: {
                valueSuffix: ' M'
            	}
        },{
				type: 'spline',
				name: 'Capacity',
                data: eval(capacityVal),
				marker: {
					lineWidth: 2,
					lineColor: Highcharts.getOptions().colors[8],
					fillColor: 'white'
				},
				tooltip: {
                valueSuffix: ' '+cur
            	}
			}]
    });
		
}	
//fnc_load_report_format()
</script>




</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../../");  ?>
        <h3 align="left" id="accordion_h1" class="accordion_h" style="width:1060px;" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" >
            <form id="monthly_capacity_order_qnty" name="monthly_capacity_order_qnty">
                <div style="width:1060px">
                    <fieldset>
                    <legend>Monthly Capacity Allocation</legend>
                        <table cellpadding="0" cellspacing="2" width="980" class="rpt_table" border="1" rules="all">
                          <thead>
                            <tr>
                                <th width="160">Company</th>
                                <th width="150">Buyer</th>
                                <th width="120">Job Year</th>
                                <th width="100">Start Year</th>
                                <th width="100">Start Month</th>
                                <th width="100">End Year</th>
                                <th width="100">End Month</th>
                                <th rowspan="2">
                                 <input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:80px" onClick="reset_form('monthly_capacity_order_qnty','report_container*report_container2','','','');" />&nbsp;<input type="button" name="search" id="search1" value="Show" onClick="fn_report_generated(1)" style="width:80px" class="formbutton" />&nbsp;</th>
                                 
                            </tr>
                            <tr class="general">
                                <td>
                                <?
                                echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name", 0, "-- Select Company --", $selected,"" );
                                ?>
                                </td>
                                 <td id="buyer_td" >
							<?
                           		 echo create_drop_down( "cbo_buyer", 150, "","", 1, "--Buyer--", $selected, ""); ?>

                        		</td>
                                <td >
                                <?
                                 echo create_drop_down( "cbo_year", 120, $year,"", 1, "All", $selected, ""); ?>

                                </td>
                                <td>
                                <?
                                echo create_drop_down( "cbo_year_name", 100,$year,"id,year", 1, "-- Select Year --", date('Y'),"" );
                                ?>
                                </td>
                                <td>
                                <?
                                echo create_drop_down( "cbo_month", 100,$months,"", 1, "-- Select --", "","" );
                                ?>
                                </td>
                                <td>
                                <?
                                echo create_drop_down( "cbo_end_year_name", 100,$year,"id,year", 1, "-- Select Year --", date('Y'),"" );
                                ?>
                                </td>
                                <td>
                                <?
                                echo create_drop_down( "cbo_month_end", 100,$months,"", 1, "-- Select --", "","" );
                                ?>
                                </td>
                            </tr>
                            <!-- <tr>
                                <th colspan="8">
                                 <input type="button" name="search" id="search1" value="Show" onClick="fn_report_generated(1)" style="width:80px" class="formbutton" />&nbsp;
                                
                             </th> -->
                            </tr>
                           </thead>
                        </table>
                    </fieldset>
                </div>
            </form>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2"></div>
    </div>
</body>

<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
    set_multiselect('cbo_company_name','0','0','','0',"fn_buyer_com();");
	set_multiselect('cbo_buyer','0','0','0','0');
	 
</script>
</html>
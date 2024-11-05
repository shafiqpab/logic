<?
/*-------------------------------------------- Comments
Version                  :   V1
Purpose			         : 	This form will create  Monthly Capacity and order qty Report
Functionality	         :
JS Functions	         :
Created by		         :	Saidul Islam
Creation date 	         :  29 June,2015
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
						   Update According to sujon vai requirment issue id 3793
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
//echo load_html_head_contents("Order Info","../../../", 1, 1, $unicode);
//echo load_html_head_contents("Graph", "../../../", "", $popup, 1,1);

echo load_html_head_contents("Graph","../../../", 1, 1, $popup,1,1);

?>
<!--For Graph start-->
<script type="text/javascript">

function hs_chart(gtype,cData,dataTitle){
	var cData=eval(cData);
    $('#container'+gtype).highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie',
			animation:true,
			borderColor: "#4572A7"
        },
        title: {
            text: 'Chart for '+dataTitle,
			style: {
				 fontSize: '16px',
				 fontWeight: 'bold'
			  }
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>',
			backgroundColor: 'rgba(219,219,216,0.8)',
			borderWidth:2
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            name: dataTitle,
            colorByPoint: true,
				data: cData
        		}]
    });



}




</script>

<script src="../../../ext_resource/hschart/hschart.js"></script>

<!--For Graph end-->

<script>
var permission='<? echo $permission; ?>';


 function fn_report_generated(type)
	{

		var company_name = $("#cbo_company_name").val();
		var style_owner = $("#cbo_style_owner").val();


			if(company_name==0  &&  style_owner==0)
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
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_year_name*cbo_month*cbo_month_end*cbo_end_year_name*cbo_style_owner*cbo_order_status',"../../../")+'&report_title='+report_title+'&type='+type;
			freeze_window(3);
			http.open("POST","requires/monthly_buyer_wise_order_summary_controller.php",true);
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
		}
	}

	function fnc_load_report_format()
    {
        var data=$('#cbo_company_name').val();
        var report_ids = return_global_ajax_value( data, 'load_report_format', '', 'requires/monthly_buyer_wise_order_summary_controller');
        print_report_button_setting(report_ids);
    }




    function print_report_button_setting(report_ids)
    {
        if(trim(report_ids)=="")
        {
            $("#search1").show();
            $("#search2").show();
            $("#search3").show();
            $("#search4").show();
            $("#search5").show();
            $("#search6").show();
         }
        else
        {
            var report_id=report_ids.split(",");
            $("#search1").hide();
            $("#search2").hide();
            $("#search3").hide();
            $("#search4").hide();
            $("#search5").hide();
            $("#search6").hide();
             for (var k=0; k<report_id.length; k++)
            {
                if(report_id[k]==108)
                {
                    $("#search1").show();
                }
                else if(report_id[k]==96)
                {
                    $("#search2").show();
                }
                else if(report_id[k]==140)
                {
                    $("#search3").show();
                }
                if(report_id[k]==141)
                {
                    $("#search4").show();
                }
                if(report_id[k]==492)
                {
                    $("#search5").show();
                }
                if(report_id[k]==195)
                {
                    $("#search6").show();
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

function fn_disable_com(str){
		if(str==2){$("#cbo_company_name").attr('disabled','disabled');}
		else{ $('#cbo_company_name').removeAttr("disabled");}
		if(str==1){$("#cbo_style_owner").attr('disabled','disabled');}
		else{ $('#cbo_style_owner').removeAttr("disabled");}
	}


</script>




</head>
<body onLoad="set_hotkey();fnc_load_report_format()">
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
                                <th width="150">Style Owner</th>
                                <th width="120">Order Status</th>
                                <th width="100">Start Year</th>
                                <th width="100">Start Month</th>
                                <th width="100">End Year</th>
                                <th width="100">End Month</th>
                                <th rowspan="2">
                                 <input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:80px" onClick="reset_form('monthly_capacity_order_qnty','report_container*report_container2','','','');" /></th>
                            </tr>
                            <tr class="general">
                                <td>
                                <?
                                echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name", 0, "-- Select Company --", $selected,"" );
                                ?>
                                </td>
                                 <td >
							<?
                           		 echo create_drop_down( "cbo_style_owner", 150, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Style Owner--", $selected, "fn_disable_com(2)"); ?>

                        		</td>
                                <td >
                                <?
                                 echo create_drop_down( "cbo_order_status", 120, $order_status,"", 1, "All", $selected, ""); ?>

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
                            <tr>
                                <th colspan="8">
                                 <input type="button" name="search" id="search1" value="Show" onClick="fn_report_generated(1)" style="width:80px" class="formbutton" />&nbsp;
                                <input type="button" name="search" id="search6" value="Show 2" onClick="fn_report_generated(6)" style="width:80px" class="formbutton" />&nbsp;
                                 <input type="button" name="search" id="search2" value="Summary" onClick="fn_report_generated(2)" style="width:80px" class="formbutton" />&nbsp;
                                <input type="button" name="search" id="search3" value="Country Ship" onClick="fn_report_generated(3)" style="width:80px" class="formbutton" />&nbsp;
                                <input type="button" name="search" id="search4" value="Summary Country Ship" onClick="fn_report_generated(4)" style="width:130px" class="formbutton" />
                                <input type="button" name="search" id="search5" value="Org. Ship. Date" onClick="fn_report_generated(5)" style="width:130px" class="formbutton" />
                             </th>
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
    set_multiselect('cbo_company_name','0','0','','0',"fn_disable_com(1);fnc_load_report_format();");
    //set_multiselect('cbo_company_name','0','0','','0',"fn_disable_com(1);get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/monthly_buyer_wise_order_summary_controller' );");
</script>
<?
		$sql=sql_select("select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name");
		$company_id='';
		$is_single_select=0;
		if(count($sql)==1){
			$company_id=$sql[0][csf('id')];
			$is_single_select=1;
			?>
			<script>
			console.log('shariar');
			set_multiselect('cbo_company_name','0','<? echo $is_single_select?>','<? echo $company_id?>','0');
			
			</script>
			
			<?
		}
		
		?>
</html>
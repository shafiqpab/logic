<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Avergae Rate Bar Graph
				
Functionality	:	
JS Functions	:
Created by		:	Akter Hossain, FAL 
Creation date 	: 	28-03-2015
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Daily Yarn Stock","../", 1, 1, $unicode,1,1); 
//  echo load_html_head_contents("Graph", "", "", $popup, $unicode, $multi_select, 1);
?>	
<script src="../ext_resource/hschart/hschart.js"></script>
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

Highcharts.theme = {
   colors: ["#7cb5ec", "#f7a35c", "#90ee7e", "#7798BF", "#aaeeee", "#ff0066", "#eeaaee",
      "#55BF3B", "#DF5353", "#7798BF", "#aaeeee"],
   chart: {
      backgroundColor: null, //null
      style: {
         fontFamily: "Dosis, sans-serif"
      }
   },
   title: {
      style: {
         fontSize: '16px',
         fontWeight: 'bold',
         textTransform: 'uppercase'
      }
   },
   tooltip: {
      borderWidth: 0,
      backgroundColor: 'rgba(219,219,216,0.8)',
      shadow: false
   },
   legend: {
      itemStyle: {
         fontWeight: 'bold',
         fontSize: '13px'
      }
   },
   xAxis: {
      gridLineWidth: 1,
	  
      labels: {
         style: {
            fontSize: '12px'
         }
      }
   },
   yAxis: {
      minorTickInterval: 'auto',
	  
      title: {
         style: {
            textTransform: 'uppercase'
         }
      },
      labels: {
         style: {
            fontSize: '12px'
         }
      }
   },
   plotOptions: {
      candlestick: {
         lineColor: '#404048'
      }
   },


   // General
   background2: '#FF0000'
   
};

// Apply the theme
Highcharts.setOptions(Highcharts.theme);

function generate_report(type)
{
	if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false )
	{
		return;
	}
	
	var cbo_company_name = $("#cbo_company_name").val();
	
	var cbo_yarn_type = $("#cbo_yarn_type").val();
	var txt_count 	= $("#cbo_yarn_count").val();
	
	var from_date 	= $("#txt_date_from").val();
	var to_date 	= $("#txt_date_to").val();
	var cbo_supplier = $("#cbo_supplier").val();
	
	
	
	
	
	
	var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_yarn_type="+cbo_yarn_type+"&txt_count="+txt_count+"&from_date="+from_date+"&to_date="+to_date+"&cbo_supplier="+cbo_supplier+"&type="+type;
 	var data="action=generate_report"+dataString;
	freeze_window(3);
	http.open("POST","requires/planning_board_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_report_reponse;  
	
}

function generate_report_reponse()
{	
	if(http.readyState == 4) 
	{	 
 		var reponse=trim(http.responseText).split("**");
		//alert(reponse[1]);
		hs_homegraph_stack( 1,reponse[0],reponse[1],reponse[2] );
		///$("#report_container2").html(reponse[0]);  
		//report_container2
		
		show_msg('3');
		
		release_freezing();
	//	alert(1);
	}
} 
  
function hs_homegraph_stack( gtype,gdata_val_stck,gcapacity,gcatg )
	{
		//gtype: 1=Value column chart,  2=Qnty  column chart,  3=Stack value column chart, 4=stack qnty column chart
		 
		 if(gtype==1)
		 {
			 var datas=gdata_val_stck;
			 var msg="Avg";
		 }
		 
		 
		$('#report_container2').highcharts({

			chart: {
				type: 'column'
			},
	
			title: {
				text: gcapacity
			},
	
			xAxis: {
				categories: gcatg
			},
	
			yAxis: {
				allowDecimals: false,
				min: 0,
				title: {
					text: msg
				}
			},
	
			tooltip: {
				formatter: function () {
					return '<b>' + this.x + '</b><br/>' +
						this.series.name + ': ' + this.y + '<br/>' ;
				}
			},
	
			plotOptions: {
				column: {
					stacking: 'normal'
				}
			},
		
			series: datas
		});
		
		
	}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../../",$permission);  ?>    		 
    <form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" > 
    <div style="width:100%;" align="center">
        <h3 style="width:950px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div style="width:100%;" id="content_search_panel">
            <fieldset style="width:950px;">
                <table class="rpt_table" width="1150" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th class="must_entry_caption">Company</th> 
                            <th>Supplier</th>                               
                          
                            <th>Yarn Type</th>
                            <th>Count</th>
                          
                            <th class="must_entry_caption">Date</th>
                           
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
							<? 
                            //   echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/requires/yarn_avg_rate_graph_controller', this.value, 'load_drop_down_supplier', 'supplier' );load_drop_down( 'requires/requires/yarn_avg_rate_graph_controller', this.value+'**'+document.getElementById('cbo_store_wise').value, 'load_drop_down_store', 'store_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/yarn_avg_rate_graph_controller' );" );
							   
							    echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/yarn_avg_rate_graph_controller', this.value, 'load_drop_down_supplier', 'supplier' );get_php_form_data( this.value, 'eval_multi_select', 'requires/yarn_avg_rate_graph_controller' );" );
								
                            ?>                            
                        </td>
                        <td id="supplier"> 
							<?
                            	echo create_drop_down( "cbo_supplier", 140, $blank_array,"",0, "--- Select Supplier ---", $selected, "",0);
                            ?>
                           </td>


                       
                        <td> 
                            <?
                                echo create_drop_down( "cbo_yarn_type", 80, $yarn_type,"", 1, "--Select--", 0, "",0 );
                            ?>
                        </td>
                        <td>
                            <?
                                echo create_drop_down("cbo_yarn_count",120,"select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count","id,yarn_count",0, "-- Select --", $selected, "");
                            ?>
                        </td>
                       
                        <td align="center">
                             <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:60px" readonly/>
                            
                             <input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y",time()- 86400);?>" class="datepicker" style="width:60px" readonly/>
                        </td>
                       
                       
                      
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:60px" class="formbutton" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="14" align="center"><? echo load_month_buttons(1); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    </tr>
                </table> 
            </fieldset> 
		</div>
    </div>
    <br /> 
    
        <!-- Result Contain Start-------------------------------------------------------------------->
         
        	<div id="report_container2" style="margin-left:5px"></div> 
            <table>
             <tr>
                    
                    <td align="center" height="400" width="764">
                      <div id="report_container2" style="width:1050px; height:400px; background-color:#FFFFFF"></div>
                    </td>
                    
             </tr>
             </table>
             
 
        
        <!-- Result Contain END-------------------------------------------------------------------->
    
    
    </form>    
</div>    
</body> 
<script>
	set_multiselect('cbo_yarn_count*cbo_supplier','0*0','0*0','','0*0');
</script> 
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

</html>

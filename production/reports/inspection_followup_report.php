<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Buyer Inspection followup Report.
Functionality	:	
JS Functions	:
Created by		:	Saidul Reza
Creation date 	: 	01-9-2015
Updated by 		: 	Abdullah Al Foysal	
Update date		: 	13-03-2017	   
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
echo load_html_head_contents("Order Wise Wages Bill Statement", "../../", 1, 1,$unicode,'','');

?>	

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';
 
 function open_order_no()
	 {
		 if( form_validation('cbo_company_name','Company Name')==false)
				{
					return;
				}
		var job_no=$("#txt_job_no").val();
		var job_id=$("#hidden_job_id").val();
		var company = $("#cbo_company_name").val();	
		var buyer=$("#cbo_buyer_name").val();
		var style_no=$('#txt_style_no').val();
		var style_id=$('#hidden_style_id').val();
	    var page_link='requires/inspection_followup_report_controller.php?action=order_wise_search&company='+company+'&buyer='+buyer+'&style_id='+style_id+'&job_id='+job_id+'&job_no='+job_no; 
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=560px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]; 
				var prodID=this.contentDoc.getElementById("txt_selected_id").value;
				//alert(prodID); // product ID
				var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
				$("#txt_order_no").val(prodDescription);
				$("#hidden_order_id").val(prodID); 
			}
	 }

	 //................new........................

	 function open_style_ref()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer=$("#cbo_buyer_name").val();
		var job_no=$("#txt_job_no").val();
		var job_id=$("#hidden_job_id").val();
		var cbo_year = $("#cbo_year").val();
		var page_link='requires/inspection_followup_report_controller.php?action=style_wise_search&company='+company+'&buyer='+buyer+'&job_no='+job_no+'&job_id='+job_id+'&cbo_year='+cbo_year; 
		var title="Search Style Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var styleID=this.contentDoc.getElementById("txt_selected_id").value;
			var styleDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#txt_style_no").val(styleDescription);
			$("#hidden_style_id").val(styleID); 
		}
	}

	//........new end..............
	 
	 
	 
function generate_report()
	{
		
		if( form_validation('cbo_company_name','Company Name')==false ){return;}
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=generate_report"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_order_no*hidden_order_id*txt_style_no*hidden_style_id*cbo_date_type*cbo_status*cbo_result*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/inspection_followup_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####");
			show_msg('3');
			release_freezing();
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			var tableFilters = 
			{
				/*col_17: "none",*/
				// col_operation: {
				// 	id: ["total_po_qnty","total_ins_qty","total_pass_qnty","total_yet_ins_qty","total_re_check","total_fail","total_ex_qnty"],
				// 	col: [9,10,11,12,15,16,18],
				// 	operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
				// 	write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
				// }
				col_operation: {
					id: ["total_po_qnty","total_ins_qty","total_pass_qnty","total_yet_ins_qty","total_re_check","total_fail","total_ex_qnty"],
					col: [10,11,12,13,16,17,19],
					operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
				}
			}
			setFilterGrid("table_body",-1,tableFilters);
			
			
			
		}
	} 
	 
	
	
	
	
	function openmypage_image(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
		}
	}
	
	
	function open_popup(order_id,title)
	{
		
		var page_link='requires/inspection_followup_report_controller.php?action=order_inspection_details&order_id='+order_id; 

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
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
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$("#table_body tr:first").show();
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="300px";
	}
	
	function openmypage_bill_info(company,po_id,item_id,type)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/inspection_followup_report_controller.php?company='+company+'&po_id='+po_id+'&item_id='+item_id+'&action='+type, 'Bill Qnty Info', 'width=700px,height=350px,center=1,resize=0,scrolling=0','../');
	
	}
	 
	 
function hs_chart(gtype,orderVal,passVal,balanceVal,Month){
	
	passVal=passVal*1;
	orderVal=orderVal*1;
	balanceVal=balanceVal*1;

	$('#container'+gtype).highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: ''
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories:[Month],
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
            name: 'Order Qty',
            data: [orderVal]
		
		}, {
            name: 'Pass Qty',
            data: [passVal]
        }, {
            name: 'Balance',
            data: [balanceVal]
        }]
    });
		
}

function search_populate(str)
	{
		if(str==1)
		{
			document.getElementById('search_by_th_up').innerHTML="Country Ship Date";
			//$('#search_by_th_up').css('color','blue');
		}
		else if(str==2)
		{
			document.getElementById('search_by_th_up').innerHTML="Publish Ship Date";
			//$('#search_by_th_up').css('color','blue');
		}
		else if(str==3)
		{
			document.getElementById('search_by_th_up').innerHTML="Inspection date";
			//$('#search_by_th_up').css('color','blue');
		}
	}

	
</script>
<script src="../../ext_resource/hschart/hschart.js"></script>

</head>
 
<body onLoad="set_hotkey();">
  <div style="width:100%;" align="center"> 
   <? echo load_freeze_divs ("../../",'');  ?>
    <h3 style="width:1000px;  margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
       <div style="width:100%;" align="center" id="content_search_panel">
    <form id="dateWiseProductionReport_1">    
      <fieldset style="width:1150px;">
            <table class="rpt_table" width="1150px" cellpadding="0" cellspacing="0" align="center">
               <thead>                    
                       <tr>
                        <th class="must_entry_caption" width="150">Company Name</th>
                        <th>Buyer Name</th>
                        <th>Style Reff. No </th>
                        <th>Order No </th>
                        <th>Date Type</th>
                        <th id="search_by_th_up" > Country Ship Date </th>
                        <th>Status</th>
                        <th>Result</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" onClick="reset_form()"/></th>
                        
                    </tr>   
              </thead>
                <tbody>
                <tr class="general">
                    <td> 
                        <?
                            echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/inspection_followup_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' )" );
                        ?>
                    </td>
                    <td id="buyer_td">
                        <? 
                            echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                        ?>
                    </td>

                    <td width="130" id="location_td">
                        <input type="text" id="txt_style_no"  name="txt_style_no"  style="width:120px" class="text_boxes" onDblClick="open_style_ref()" placeholder="Browse/Write"  />
                        <input type="hidden" id="hidden_style_id"  name="hidden_style_id" />
                        <input type="hidden" id="hidden_order_id"  name="hidden_order_id" />
                        <input type="hidden" id="hidden_job_id"  name="hidden_job_id" />
                    </td>


                    <td id="floor_td">
                     <input type="text" id="txt_order_no"  name="txt_order_no"  style="width:120px" class="text_boxes" onDblClick="open_order_no()" placeholder="Browse/Write" />
                     <input type="hidden" id="hidden_order_id"  name="hidden_order_id" />
                    </td>
                               
                  
                    <td>
                        <? 
							$date_type_arr=array("1"=>"Country Ship Date","2"=>"Publish Ship Date","3"=>" Inspection date");
                           // echo create_drop_down( "cbo_date_type", 100, $date_type_arr,"", 1, "-- Select --", 2, "","","" );
							
							//$search_by = array(1=>'Shipment Date',2=>'Po Received Date',3=>'Po Insert Date');
								$dd="search_populate(this.value)";
								echo create_drop_down( "cbo_date_type", 100, $date_type_arr,"",0, "--Select--", $selected,$dd,0 );
                        ?>
                    </td>

                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" >&nbsp; To
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date"  ></td>


                    <td> 
                        <? 
							$ins_status_arr=array(1=>"Not Started",2=>"Started",3=>"Partial",4=>"Complete");
                            echo create_drop_down( "cbo_status", 100, $ins_status_arr,"", 1, "-- Select --", $selected, "","","" );
                        ?>
                    </td>
                    <td>
                        <? 
                            echo create_drop_down( "cbo_result", 100, $inspection_status,"", 1, "-- Select --", $selected, "","","" );
                        ?>
                    </td>
                    
                    <td>
                        <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="generate_report()" />
                    </td>
                    
                    
                </tr>
                </tbody>
            </table>
            <table>
            	<tr>
                	<td>
 						<? echo load_month_buttons(1); ?>
                   	</td>
                </tr>
            </table> 
      </fieldset>
      <div id="report_container" align="center"></div>
      <div id="report_container2"></div>  
 </form> 
 </div>
 </div>  
 <script>
	window.onload = function(){
		document.getElementById("cbo_date_type").value = 3;
		var event = new Event('change');
		document.getElementById('cbo_date_type').dispatchEvent(event);
	}
 </script> 
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

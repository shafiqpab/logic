<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Multi Company Wise Daily Yarn Receive Report
				
Functionality	:	
JS Functions	:
Created by		:	Abu Sayed 
Creation date 	: 	30/05/2023
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
echo load_html_head_contents("Multi Company Wise Daily Yarn Receive Report","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	
function reset_field()
{
	reset_form('item_receive_issue_1','report_container2','','','','');
}

function  generate_report(rptType)
{
	var cbo_item_cat = 1;
	var cbo_company_name 	= $("#cbo_company_name").val();
	var txt_date_from 		= $("#txt_date_from").val(); 
	var txt_date_to 		= $("#txt_date_to").val();
	var cbo_dyed_type 		= $("#cbo_dyed_type").val();
	var cbo_yarn_count 		= $("#cbo_yarn_count").val();	
	var cbo_store_name 		= $("#cbo_store_name").val();
	var cbo_receive_purpose = $("#cbo_receive_purpose").val();
	var cbo_source 			= $("#cbo_source").val();
	//var fso_id = $("#fso_id").val();
	
	if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Form Date*To Date')==false )
	{			
		return;
	}
	
	var dataString = "&cbo_item_cat="+cbo_item_cat+"&cbo_company_name="+cbo_company_name+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&cbo_dyed_type="+cbo_dyed_type+"&cbo_yarn_count="+cbo_yarn_count+"&rptType="+rptType+"&cbo_store_name="+cbo_store_name+"&cbo_receive_purpose="+cbo_receive_purpose+"&cbo_source="+cbo_source;
	var data="action=generate_report"+dataString;
	//alert(data);return;
	
	freeze_window(5);
	http.open("POST","requires/multi_company_wise_daily_yarn_receive_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_report_reponse; 
}

function generate_report_reponse()
{	
	if(http.readyState == 4) 
	{
		//alert(http.responseText);	 
		var reponse=trim(http.responseText).split("**");
		 //alert(reponse[2]);
		$("#report_container2").html(reponse[0]);
		document.getElementById('report_container').innerHTML=print_preview_button('../../../');
		document.getElementById('report_container3').innerHTML=excel_preview_button(reponse[1]);
		
		if(reponse[2]!=2)
        {
			append_report_checkbox('table_header_1',1);
        }
		
		var tableFilters4 = 
		 {
			col_40: "none",
			col_operation: {
			id: ["value_total_receive","value_tot_receive_ret_qty","value_total_order_amt","value_total_amount"],
		   	col: [21,22,23,25],
		   	operation: ["sum","sum","sum","sum"],
		   	write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		 }
		
		setFilterGrid("table_body",-1,tableFilters4);
		release_freezing();
		show_msg('3');
		//document.getElementById('report_container').innerHTML=report_convert_button('../../');
	}
} 


function print_preview_button(url)
{
	return '<input type="button" onclick="print_priview_html( \'report_container2\', \'scroll_body\',\'table_header_1\',\'report_table_footer\', 3, \'0\',\''+url+'\' )" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
}
function excel_preview_button(url)
{
	return '<a href="requires/'+url+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/>';
}

function new_window()
{
	 
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none";
	$('#table_body tr:first').hide(); 
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close(); 
	document.getElementById('scroll_body').style.overflow="auto"; 
	document.getElementById('scroll_body').style.maxHeight="250px";
	$('#table_body tr:first').show();
}


</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission);  ?><br />    		 
    <form name="item_receive_issue_1" id="item_receive_issue_1" autocomplete="off" >
    <h3 align="left" id="accordion_h1" style="width:1120px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div style="width:1110px;" align="center" id="content_search_panel">
        <fieldset style="width:1100px;">
                <table class="rpt_table" width="1100" cellpadding="0" cellspacing="0" rules="all">
                <thead>
                    <tr>                    	
                        <th width="200" class="must_entry_caption">Company</th>
                        <th width="200">Store Name</th>
                        <th width="100">Dyed Type</th>
                        <th width="120">Count</th>
						<th width="100">Receive Purpose</th>
						<th width="100">Receive Source</th>
                        <th width="170" class="must_entry_caption">Date Range</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_field()" /></th>
                    </tr>
                </thead>
                <tr class="general">                	
                    <td>
                        <?
						//   echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "getStore(this.value);load_drop_down('requires/multi_company_wise_daily_yarn_issue_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data(this.value,'print_button_variable_setting','requires/multi_company_wise_daily_yarn_issue_report_controller' );" );

                        // 	echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/multi_company_wise_daily_yarn_receive_report_controller',this.value+'**1', 'load_drop_down_store', 'store_td' );" );

						echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                        ?>                          
                    </td>
                    <td id="store_td"><? 
                        	echo create_drop_down( "cbo_store_name", 170, $blank_array,"", 1, "-- All Store --", $selected, "",0,"" );
                        ?>
                    </td>
                     <td align="center">
						<?   
                        $dyedType=array(0=>'All',1=>'Dyed Yarn',2=>'Non Dyed Yarn');
                        echo create_drop_down( "cbo_dyed_type", 80, $dyedType,"", 0, "--Select--", $selected, "", "","");
                        ?>              
                     </td>
                    <td>  
						<?
                        echo create_drop_down( "cbo_yarn_count", 100, "select id,yarn_count from lib_yarn_count where is_deleted = 0 AND status_active = 1 ORDER BY yarn_count ASC","id,yarn_count", 1, "--Select--", 0, "",0 );
                        ?>
                    </td>
					<td>
						<?

							echo create_drop_down( "cbo_receive_purpose", 100, $yarn_issue_purpose,"", 0, "--Select Purpose--",$selected,"","","2,5,6,7,12,15,16,38,43,46,50,51","","","" );
						?>
					</td>
					<td>
						<?
						 	echo create_drop_down( "cbo_source", 100, $source,"", 1, "-- Select --", $selected, "","" );
						?>
					</td>
                    <td>
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px;" placeholder="From Date" readonly/>&nbsp; TO &nbsp;
                    	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:55px;" readonly/>
                    </td>
                    <td>
                        <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:80px" class="formbutton" />
                    </td>
                </tr>
                <tr>
                	<td colspan="8" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                </tr>
                
            </table> 
        </fieldset> 
           
    </div>
        <!-- Result Contain Start-------------------------------------------------------------------->
        	<div style="margin-top:10px" id=""><span id="report_container"></span><span id="report_container3"></span></div>
            <div id="report_container2"></div> 
        <!-- Result Contain END-------------------------------------------------------------------->
    
    
    </form>    
</div>    
</body>
<script>
	set_multiselect('cbo_company_name','0','0','0','0');	
	$("#multi_select_cbo_company_name a").click(function(){load_getStore();});


    function load_getStore()
	{  
		var company=$("#cbo_company_name").val(); 		 
		
        if(company !='') {
            var data="action=load_drop_down_store&choosenCompany="+company;
            http.open("POST","requires/multi_company_wise_daily_yarn_receive_report_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = function(){
                if(http.readyState == 4)
                {
                    var response = trim(http.responseText);
                    $('#store_td').html(response);
                    set_multiselect('cbo_store_name','0','0','','0');
                }
            };
        }
	}
	set_multiselect('cbo_receive_purpose*cbo_store_name','0*0','0*0','','0*0');
</script>  

<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>

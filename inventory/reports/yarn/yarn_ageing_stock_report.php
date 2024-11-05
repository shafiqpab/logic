<?
/*-------------------------------------------- Comments
Purpose			: 	This file will create fro Yarn Ageing Stock Report
				
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam Reza 
Creation date 	: 	13-06-2016
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
echo load_html_head_contents("Yarn Ageing Stock Report","../../../", 1, 1, $unicode,1,1); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function generate_report(type)
{

	if( form_validation('txt_no_col*txt_range','Number of Colum*Range')==false )
	{
		return;
	}
	var report_title=$( "div.form_caption" ).html();
	var data="action=generate_report"+get_submitted_data_string('cbo_company_name*cbo_supplier*cbo_dyed_type*cbo_yarn_type*cbo_yarn_count*txt_lot_no*txt_date_from*cbo_store_wise*cbo_store_name*txt_no_col*txt_range*txt_composition',"../../../")+'&report_title='+report_title+"&type="+type;//*txt_job_no
	//alert (data); 
	freeze_window(3);
	http.open("POST","requires/yarn_ageing_stock_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_report_reponse;  
}

function generate_report_reponse()
{	
	if(http.readyState == 4) 
	{	 
 		var reponse=trim(http.responseText).split("**");
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
	
	//$("#table_body tr:first").hide();
	
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><style></style></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close(); 
	document.getElementById('scroll_body').style.overflow="auto"; 
	document.getElementById('scroll_body').style.maxHeight="310px";
	
	//$("#table_body tr:first").show();
}

 
/*
function openmypage(prod_id,action)
{
	var companyID = $("#cbo_company_id").val();
	var popup_width='900px';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/yarn_ageing_stock_report_controller.php?companyID='+companyID+'&prod_id='+prod_id+'&action='+action, 'Yarn Allocation Statement', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
}

function openmypage_stock(prod_id,action)
{
	var popup_width='750px';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/yarn_ageing_stock_report_controller.php?prod_id='+prod_id+'&action='+action, 'Yarn Stock Details', 'width='+popup_width+', height=400px,center=1,resize=0,scrolling=0','../../');
}

function openmypage_trans(prod_id,trans_type,store_name,from_date,to_date,action)
{
	var popup_width='450px';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/yarn_ageing_stock_report_controller.php?prod_id='+prod_id+'&trans_type='+trans_type+'&store_name='+store_name+'&from_date='+from_date+'&to_date='+to_date+'&action='+action, 'Yarn Transfer Details', 'width='+popup_width+', height=200px,center=1,resize=0,scrolling=0','../../');
}

function validate(e)
{
	var key;
	var keychar;
	if (window.event)
		key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;
	keychar = String.fromCharCode(key);
	// control keys
	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
	return true;
	// numbers
	else if ((("%").indexOf(keychar) > -1))
		return false;
	else
		return true;
}

$(document).ready(function() 
{
	$('#txt_composition').bind('copy paste cut',function(e) {
		e.preventDefault(); //disable cut,copy,paste
	});
});
*/


</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../../",$permission); ?> 		 
    <form name="dailyYarnIssueReport_1" id="dailyYarnIssueReport_1" autocomplete="off" > 
        <h3 style="width:1020px; margin:5px auto 0 auto; " id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div style="width:100%;" align="center">
            <fieldset id="content_search_panel" style="width:960px;">
            <legend>Search Panel</legend> 
                <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th>Company</th> 
                            <th>Supplier</th>                               
                            <th>Dyed Type</th>
                            <th>Yarn Type</th>
                            <th>Count</th>
                            <th>Composition</th>
                            <th>Lot</th>
                            <th>Date</th>
                            <th>Store Wise</th>
                            <th>Store Name</th>
                            <th>No. of Col.</th>
                            <th>Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" /></th>
                        </tr>
                    </thead>
                    <tr>
                        <td>
							<? 
                               echo create_drop_down( "cbo_company_name", 110, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- All Company --", $selected, "load_drop_down( 'requires/yarn_ageing_stock_report_controller', this.value, 'load_drop_down_supplier', 'supplier' );load_drop_down( 'requires/yarn_ageing_stock_report_controller', this.value+'**'+document.getElementById('cbo_store_wise').value, 'load_drop_down_store', 'store_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/yarn_ageing_stock_report_controller' );" );
                            ?>                            
                        </td>
                        <td id="supplier"> 
							<?
                            	echo create_drop_down( "cbo_supplier", 120, $blank_array,"",0, "--- Select Supplier ---", $selected, "",0);
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
                                echo create_drop_down("cbo_yarn_type",100,$yarn_type,"",0, "-- Select --", $selected, "");
							?>
                        </td>
                        <td>
                            <?
                                echo create_drop_down("cbo_yarn_count",90,"select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count","id,yarn_count",0, "-- Select --", $selected, "");
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_composition" name="txt_composition" class="text_boxes" style="width:70px" value="" onKeyPress="return validate(event);" />
                        </td>
                        <td>
                            <input type="text" id="txt_lot_no" name="txt_lot_no" class="text_boxes" style="width:45px" value="" />
                        </td>
                        <td align="center">
                             <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y");?>" class="datepicker" style="width:55px" readonly/>
                          </td>
                        <td> 
                            <?
                                echo create_drop_down( "cbo_store_wise", 50, $yes_no,"", 0, "--Select--", 2, "load_drop_down( 'requires/yarn_ageing_stock_report_controller', document.getElementById('cbo_company_name').value+'**'+this.value, 'load_drop_down_store', 'store_td' );",0 );
                            ?>
                        </td>
                        <td id="store_td">
                            <? 
                                echo create_drop_down( "cbo_store_name", 100, $blank_array,"", 1, "-- All Store --", $storeName, "",1 );
                            ?>
                        </td>
                        <td align="center">
                            <input type="text" id="txt_no_col" name="txt_no_col" class="text_boxes_numeric" style="width:30px" value="" />
                        </td>
                        <td>
                            <input type="text" id="txt_range" name="txt_range" class="text_boxes_numeric" style="width:30px" value="" />
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:60px" class="formbutton" />
                        </td>
                    </tr>
                </table> 
            </fieldset> 
            
        	<div id="report_container" align="center"></div>
            <div id="report_container2" style="margin-left:5px"></div> 
           
        </div>
    </form>    
</div>    
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_multiselect('cbo_yarn_count*cbo_supplier*cbo_yarn_type','0*0*0','0*0*0','','0*0*0');
</script> 
</html>

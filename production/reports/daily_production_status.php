<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Production Incentive Payment Report
				
Functionality	:	
JS Functions	:
Created by		:	Ashraful
Creation date 	: 	14-06-2017
Updated by 		: 		
Update date		: 		   
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
echo load_html_head_contents("Line Wise Productivity Analysis","../../", 1, 1, $unicode,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report()
	{
		if( form_validation('cbo_company_id*txt_date','Company Name*Production Date')==false )
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();
		
		var data="action=report_generate2"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_floor_id*cbo_line*hidden_line_id*cbo_buyer_name*txt_date*txt_parcentage*txt_file_no*txt_ref_no*cbo_no_prod_type*txt_item_catgory',"../../")+'&report_title='+report_title;
	
		freeze_window(3);
		http.open("POST","requires/daily_production_status_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			//alert (http.responseText);
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			
			release_freezing();
			document.getElementById('factory_efficiency').innerHTML=document.getElementById('total_factory_effi').innerHTML;
			document.getElementById('factory_parfomance').innerHTML=document.getElementById('total_factory_per').innerHTML;
			//alert(reponse[1]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			show_msg('3');
			release_freezing();
		}
	} 

	function generate_report_month_data()
	{
		if( form_validation('cbo_company_id*txt_date','Company Name*Production Date')==false )
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();
		
		var data="action=report_generate_month"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_floor_id*cbo_line*hidden_line_id*cbo_buyer_name*txt_date*txt_parcentage*txt_file_no*txt_ref_no*cbo_no_prod_type*txt_item_catgory',"../../")+'&report_title='+report_title;
	
		freeze_window(3);
		http.open("POST","requires/daily_production_status_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_month_data_reponse;  
	}

	function generate_report_month_data_reponse()
	{	
		if(http.readyState == 4) 
		{
			//alert (http.responseText);
			var reponse=trim(http.responseText).split("####");
			$("#report_container3").html(reponse[0]);  
			release_freezing();
		
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><link rel="stylesheet" href="../../amchart/plugins/export.css" type="text/css" media="all" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
	}
		
	function openmypage_line()
	{
		if( form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company = $("#cbo_company_id").val();	
		var location=$("#cbo_location_id").val();
		var floor_id=$("#cbo_floor_id").val();
		var txt_date=$("#txt_date").val();
		var page_link='requires/daily_production_status_controller.php?action=line_search_popup&company='+company+'&location='+location+'&floor_id='+floor_id+'&txt_date='+txt_date; 
		
		var title="Search line Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=310px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			
			var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#cbo_line").val(prodDescription);
			$("#hidden_line_id").val(prodID); 
		}
	}
	 
	 function openmypage(company_id,order_id,subcon_order,floor_id,line_no,action,item_smv,actual_time,line_date,prod_date)
		{
			 popup_width='550px'; 

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_production_status_controller.php?po_id='+order_id+'&company_id='+company_id+'&action='+action+'&floor_id='+floor_id+'&sewing_line='+line_no+'&prod_date='+prod_date+'&item_smv='+item_smv+'&subcon_order='+subcon_order+'&line_date='+line_date+'&actual_time='+actual_time, 'Detail Veiw', 'width='+popup_width+', height=270px,center=1,resize=0,scrolling=0','../');
		}
		
		function openmypage2(company_id,order_id,subcon_order,floor_id,line_no,action,item_smv,actual_time,line_date,prod_date)
		{
			 popup_width='550px'; 

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_production_status_controller.php?po_id='+order_id+'&company_id='+company_id+'&action='+action+'&floor_id='+floor_id+'&sewing_line='+line_no+'&prod_date='+prod_date+'&item_smv='+item_smv+'&subcon_order='+subcon_order+'&line_date='+line_date+'&actual_time='+actual_time, 'Detail Veiw', 'width='+popup_width+', height=270px,center=1,resize=0,scrolling=0','../');
		}
	 
	  function generate_style_popup(style,po_id,subcon_order,res_mst_id,floor_id,item_id,prod_reso_allo,prod_date,action,i)
		{
			 popup_width='1120px'; 
			var company_id = $("#cbo_company_id").val();	
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_production_status_controller.php?po_id='+po_id+'&company_id='+company_id+'&action='+action+'&floor_id='+floor_id+'&item_id='+item_id+'&style='+style+'&prod_reso_allo='+prod_reso_allo+'&sewing_line='+res_mst_id+'&prod_date='+prod_date+'&subcon_order='+subcon_order, 'Detail Veiw', 'width='+popup_width+', height=390px,center=1,resize=0,scrolling=0','../');
		}
	 
	 
</script>
	<script src="../../Chart.js-master/amcharts/amcharts.js"></script>
	<script src="../../Chart.js-master/amcharts/serial.js"></script>
    <script src="../../Chart.js-master/amcharts/light.js"></script>

<!-- 	<script src="../../amchart/amcharts.js"></script>
	<script src="../../amchart/serial.js"></script>
    <script src="../../amchart/plugins/export.min.js"></script>
    <link rel="stylesheet" href="../../amchart/plugins/export.css" type="text/css" media="all" />
    <script src="../../amchart/themes/light.js"></script>-->
</head>
<body onLoad="set_hotkey();">

<form id="LineWiseProductivityAnalysis_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
         
         <h3 style="width:1130px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1400px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="160" class="must_entry_caption">Company</th>
                    <th class="must_entry_caption">Production Date</th>
                    <th width="150">Location</th>
                    <th width="70">File No</th>
                    <th width="70">Ref. No</th>
                    <th width="120">Floor</th>
                    <th width="80">Include No Prod.Line</th>
                    <th width="120">Line No</th>
                    <th width="100">Product Category</th>
                    <th width="150">Buyer</th>
                    
                    
                    <th width="70">Efficiency %</th>
                    <th width="180" colspan="2"><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('LineWiseProductivityAnalysis_1','report_container','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                       <td>
							<? 
							
                                echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/daily_production_status_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );load_drop_down( 'requires/daily_production_status_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                            ?>                            
                        </td>
                         <td>
                            <input type="text" name="txt_date" id="txt_date" class="datepicker" style="width:70px;" readonly/>
                        </td>
                        <td id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location_id", 140, $blank_array,"", 1, "-- Select Location --", "", "" );
                            ?>                            
                        </td>
                         <td>
                               <input type="text" id="txt_file_no"  name="txt_file_no"  style="width:70px" class="text_boxes"  placeholder="Write"  />
                        </td>
                         <td>
                               <input type="text" id="txt_ref_no"  name="txt_ref_no"  style="width:70px" class="text_boxes"  placeholder="Write"  />
                        </td>
                        <td id="floor_td">
                            <? 
                                echo create_drop_down( "cbo_floor_id", 120, $blank_array,"", 1, "-- Select Floor --", "", "" );
                            ?>                            
                        </td>
                         <td>
                            <? 
                                echo create_drop_down( "cbo_no_prod_type", 80, $yes_no,"", 0, "-- Select --", "", "" );
                            ?>                            
                        </td>
                        <td id="line_td">
                               <input type="text" id="cbo_line"  name="cbo_line"  style="width:120px" class="text_boxes" onDblClick="openmypage_line()" placeholder="Browse"  readonly/>
                               <input type="hidden" id="hidden_line_id" name="hidden_line_id" />
                        </td>
                        <td>
                        	<?
                              echo create_drop_down( "txt_item_catgory", 100, $product_category,"", 1, "-Product Category-", "", "","","" );
							 ?>
                         </td>
                        <td id="buyer_td_id"> 
                            <?
                               echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- Select Buyer --", 0, "",0 );
                            ?>
                        </td>
                        
                            <?
								//$get_upto=array(1=>"Greater Than",2=>"Less Than",3=>"Greater/Equal",4=>"Less/Equal");
                               // echo create_drop_down( "cbo_get_upto", 70, $get_upto,"", 1, "- All -", 0, "",0 );
                            ?>
                      
                        <td align="left">
                            <input type="text" id="txt_parcentage" name="txt_parcentage" class="text_boxes_numeric" style="width:50px; text-align:left" value="60" />
                        </td>
                        <td>
                            <!--Not Use --hidden button> --> 
                            <input type="button" name="search2" id="search2" value="Show" onClick="generate_report()" style="width:40px" class="formbutton" />
                        </td>
                        <td>
                        	<input type="button" name="search1" id="search1" value="Month data" onClick="generate_report_month_data()" style="width:80px" class="formbutton" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left">
    	<div style="float:left; " id="report_container3">
           
        </div>
    </div>
 </form>   
</body>
<script>
	set_multiselect('cbo_floor_id','0','0','','0');
</script> 

<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
<script>
$("#cbo_location_id").val(0);

</script>
</html>

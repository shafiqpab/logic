<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Multiple Factory Monthly Production Report
				
Functionality	:	
JS Functions	:
Created by		:	Kaiyum
Creation date 	: 	10-02-2018
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
echo load_html_head_contents("Multiple Factory Monthly Production Report","../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function fn_report_generated(type)
	{
		/*if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*Date From* Date To')==false )
		{
			return;
		}*/
		
		if($('#cbo_company_id').val()==0){
			var data='cbo_working_company_id*txt_date_from*txt_date_to';	
			var filed='Working Company Name*From Date*Date To';	
		}
		else
		{
			var data='cbo_company_id*txt_date_from*txt_date_to';	
			var filed='Company Name*From Date*Date To';	
		}
		
		
		
		if( form_validation(data,filed)==false )
		{
			return;
		}
		else
		{	
		
		var from_date = $('#txt_date_from').val();
		var to_date = $('#txt_date_to').val();
		var datediff = date_diff( 'd', from_date, to_date )+1;

		var report_title=$( "div.form_caption" ).html();
		
		var data="action=report_generate"+get_submitted_data_string('cbo_product_cat*cbo_company_id*cbo_location*txt_date_from*txt_date_to*cbo_working_company_id',"../../")+'&report_title='+report_title+'&datediff='+datediff+'&type='+type;
		//alert(data);
		freeze_window(3);
		http.open("POST","requires/multiple_factory_production_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;  
	}
  	}	
	function fn_report_generated_reponse()
	{	
		if(http.readyState == 4) 
		{
			//alert (http.responseText);
			var reponse=trim(http.responseText).split("####");
			//alert(reponse[1]); 
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
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
	}
	
	function openmypage2(date,company_id,po_id,location,action,source)
	{
		var popupWidth = "width=800px,height=350px,";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/multiple_factory_production_report_controller.php?date='+date+'&company_id='+company_id+'&po_id='+po_id+'&location_id='+location+'&action='+action+'&sewing_source='+source, 'Production Quantity Details', popupWidth+'center=1,resize=0,scrolling=0','../');
			
	}
function fn_disable_com(str){
		if(str==2){$("#cbo_company_id").attr('disabled','disabled');}
		else{ $('#cbo_company_id').removeAttr("disabled");}
		if(str==1){$("#cbo_working_company_id").attr('disabled','disabled');}
		else{ $('#cbo_working_company_id').removeAttr("disabled");}
	}
	
function getCompanyId() 
{
    var company_id = document.getElementById('cbo_company_id').value;
    //var search_type = document.getElementById('cbo_search_by').value;
    if(company_id !='') {
      var data="action=load_drop_down_location&data="+company_id;
      http.open("POST","requires/multiple_factory_production_report_controller.php",true);
      http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
      http.send(data); 
      http.onreadystatechange = function(){
          if(http.readyState == 4) 
          {
              var response = trim(http.responseText);
              //$('#location_td').html(response);
              $('#location_td').html(response);
             // set_multiselect('cbo_location','0','0','','0');
              //set_multiselect('cbo_buyer_name','0','0','','0');
             // fn_buyer_visibility(search_type);
          }			 
      };
    }         
}	
	
</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission); ?>   		 
        <form name="factorymonthlyproduction_1" id="factorymonthlyproduction_1" autocomplete="off" > 
        <h3 style="width:860px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel','')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:860px" align="center" >      
            <fieldset>  
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th class="must_entry_caption">Company Name</th>
                    <th class="must_entry_caption">Working Company</th>
                    <th>Location</th>
                    <th>Product Category</th>
                    <th class="must_entry_caption">Production Date</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" onClick="reset_form('factorymonthlyproduction_1','report_container*report_container2','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td id="cbo_company_id_td"> 
							<?
								echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "-- Select Company --", $selected, "fn_disable_com(1)" );
                            ?>
                        </td>
                        
                         <td width="150" align="center"> 
			                        <?
			                            echo create_drop_down( "cbo_working_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 0, "-- Select Company --", $selected, "load_drop_down( 'requires/multiple_factory_production_report_controller', this.value, 'load_drop_down_location', 'location_td'); fn_disable_com(2)" );
			                        ?>
                      	</td>
                        
                        <td align="center" id="location_td">
                    	<? 
                            echo create_drop_down( "cbo_location", 130, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                        ?>
                    	</td>
                        <td align="center" id="product_td">
                    	<? 
                            echo create_drop_down( "cbo_product_cat", 130, $product_category,"", 1, "-- Select --", $selected, "",0,"" );
                        ?>
                    	</td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date" readonly > To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date" readonly >
                        </td>
                        <td align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="Gmt Prod Sew" onClick="fn_report_generated(3)" />
                        </td>
                    </tr>
                </tbody>
                <tr>
                    <td colspan="6">
                        <? echo load_month_buttons(1); ?>
                    </td>
                </tr>
            </table> 
        </fieldset>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2" align="left"></div>
    </form> 
    </div>
</body>
<script>
//set_multiselect('cbo_company_name*cbo_buyer_name','0*0','0','','0*0');
set_multiselect('cbo_company_id*cbo_working_company_id','0','0','','0');
	setTimeout[($("#cbo_company_id_td a").attr("onclick","disappear_list(cbo_company_id,'0');getCompanyId();") ,3000)];
</script>
<script>$('#cbo_location').val(0); </script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

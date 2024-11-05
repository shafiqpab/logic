<?
/*-------------------------------------------- Comments -----------------------
Purpose			    : 	Report.
Functionality	  :	
JS Functions	  :
Created by		  :	  Md. Shafiqul Islam Shafiq 
Creation date   : 	09-10-2018
Updated by 		  : 		
Update date		  : 	 
QC Performed BY	:		
QC Date			    :	
Comments		    :
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Date Wise Production Report", "../../", 1, 1,$unicode,1,1);

?>
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';
 
var tableFilters = 
		{
			col_0: "none", 
		} 
		   		
var tableFilters1 = 
		{
			col_0: "none", 
		} 
					
function fn_report_generated()
{
	if (form_validation('txt_date','From Date')==false)
	{
		return;
	}
	else
	{		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_location*cbo_buyer*txt_date',"../../");
		freeze_window(3);
		http.open("POST","requires/sewing_input_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
}
	

function fn_report_generated_reponse()
{
 	if(http.readyState == 4) 
	{
	 	show_msg('3'); 
  		var reponse=trim(http.responseText).split("####"); 
        
		$('#report_container2').html(reponse[0]);
		// alert(reponse[2]);
		document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
		//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
		//append_report_checkbox('table_header_1',1);		
		//setFilterGrid("table_body",-1,tableFilters);
		
		//setFilterGrid("table_body1",-1,tableFilters);		
		release_freezing();
 	}
	
}

function new_window()
{
	document.getElementById('scroll_body').style.overflow='auto';
	document.getElementById('scroll_body').style.maxHeight='none'; 
	$("#table_body tr:first").hide();
	$("#table_body1 tr:first").hide();
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><style type="text/css">.block_div { width:auto;height:auto;text-wrap:normal;vertical-align:bottom;display: block;position: !important;-webkit-transform: rotate(-90deg);} </style><body>'+document.getElementById('report_container2').innerHTML+'</body</html>'); 
	d.close();
	
	document.getElementById('scroll_body').style.overflowY='scroll';
	document.getElementById('scroll_body').style.maxHeight='425px';
	$("#table_body tr:first").show();
}	 


 function show_line_remarks(company_id,order_id,floor_id,line_no,prod_date,action)
	{
		
		popup_width='550px'; 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sewing_input_report_controller.php?order_id='+order_id+'&company_id='+company_id+'&floor_id='+floor_id+'&sewing_line='+line_no+'&prod_date='+prod_date+'&action='+action, 'Detail Veiw','width='+popup_width+', height=270px,center=1,resize=0,scrolling=0','../');
	}
  		

function getCompanyId() 
{
    var company_id = document.getElementById('cbo_company_name').value;
    var location_id = document.getElementById('cbo_location').value;
    var floor_id = document.getElementById('cbo_floor').value;
    //var search_type = document.getElementById('cbo_search_by').value;
    if(company_id !='') {
      var data="action=load_drop_down_line&company_id="+company_id+'&location_id='+location_id+'&floor_id='+floor_id;
      http.open("POST","requires/sewing_input_report_controller.php",true);
      http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
      http.send(data); 
      http.onreadystatechange = function(){
          if(http.readyState == 4) 
          {
              var response = trim(http.responseText);
              //$('#location_td').html(response);
              $('#line_td').html(response);
             // set_multiselect('cbo_location','0','0','','0');
              //set_multiselect('cbo_buyer_name','0','0','','0');
             // fn_buyer_visibility(search_type);
          }          
      };
    }     
}

</script>
  <style type="text/css">
    #chart_container {
      height: 400px;
      min-width: 400px;
      max-width: 800px;
      margin: 0 auto;
    }
 </style>
                     
                          
</head>
 
<body onLoad="set_hotkey();">

<form id="lineWiseProductionReport_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
               
         <fieldset style="width:500px;">
        	<legend>Search Panel</legend>
            <table class="rpt_table" width="500px" cellpadding="0" cellspacing="0" border="1" align="center">
               <thead>                    
                    <tr>
                        <th>Working Company Name</th>
                        <th>Location</th>
                        <th>Buyer</th>
                        <th id="search_text_td" class="must_entry_caption">Input Date</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                    </tr>    
                 </thead>
                <tbody>
                <tr >
                    <td width="140"> 
                        <?
                            echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sewing_input_report_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/sewing_input_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                        ?>
                    </td>                   
                    <td width="110" id="location_td">
                    	<? 
                            echo create_drop_down( "cbo_location", 110, $blank_array,"", 1, "-- Select --", $selected, " load_drop_down( 'requires/sewing_input_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );", 1, "" );
                        ?>
                    </td>
                    <td width="110" id="buyer_td">
                    	<? 
                            echo create_drop_down( "cbo_buyer", 110, $blank_array,"", 1, "-- Select --", $selected, "", 1, "" );
                        ?>
                    </td>
                    <td width="">
                        <input name="txt_date" id="txt_date" class="datepicker" style="width:75px" onChange="load_drop_down( 'requires/sewing_input_report_controller',document.getElementById('cbo_floor').value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_line', 'line_td' );" readonly >
                    </td>   
                                      
                    <td width="70">
                        <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(0)" />
                    </td>
                </tr>
                </tbody>
            </table>
        </fieldset>
    </div>
    
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>    
</body>
<script>
	set_multiselect('cbo_floor','0','0','','0');
    // setTimeout[($("#floor_td a").attr("onclick","disappear_list(cbo_floor,'0');getCompanyId();") ,3000)];   

</script> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

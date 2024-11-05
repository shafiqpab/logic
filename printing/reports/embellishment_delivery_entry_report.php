<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Date Wise Production Report
				
Functionality	:	
JS Functions	:
Created by		:	Md.Mahbubur Rahman
Creation date 	: 	22-07-2019
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
echo load_html_head_contents("Printing Delivery Entry Report","../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	
	
	function generate_report(report_type)
	{
		if(document.getElementById('cbo_company_id').value==0 || document.getElementById('cbo_customer_source').value==0 || document.getElementById('cbo_customer_name').value==0)
		{
			if( form_validation('cbo_company_id*cbo_customer_source*cbo_customer_name*txt_date_from*txt_date_to','Company Name*Within Group*Party Name*From date*To date')==false )
			{
				return;
			}
		}
		
		
		var data="action=generate_report&report_type="+report_type+"&report_title="+$( "div.form_caption" ).html()+get_submitted_data_string('cbo_company_id*cbo_location_name*cbo_customer_source*cbo_customer_name*txt_date_from*txt_date_to',"../../");
		
		//alert(data);
		freeze_window(3);
		http.open("POST","requires/embellishment_delivery_entry_report_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body_id",-1,'');
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window(type)
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body_id tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$('#table_body_id tr:first').show();
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="250px";
	}


	/*function check_all()
	{
		$("input[name=chkPrint]").each(function(index, element) { 
					
			if( $('#all_check').prop('checked')==true) 
				$(this).attr('checked','true');
			else
				$(this).removeAttr('checked');
		});
	}*/
	
	function print_formet(){
		var data='';
		var error=1;
		$("input[name=chkPrint]").each(function(index, element) {
		if( $(this).prop('checked')==true)
		{
			
			error=0;
			var idd=$(this).attr('id').split("_");
			if(data=="") data=$('#hiddenid_'+idd[1] ).val(); else data=data+","+$('#hiddenid_'+idd[1] ).val();
		}
		});
		//alert(data); 
		if (error==1) {
			alert("Data Does Not Select"); return;
		}

		var response = return_global_ajax_value(data, "report_voucher_print", "", "requires/embellishment_delivery_entry_report_controller");
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write(response);
        d.close();
        //alert(response)
	}


	
</script>
</head>
<body onLoad="set_hotkey();">
  <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
     <h3 style="width:820px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:820px">  
             <form name="date_wise_production_1" id="date_wise_production_1" autocomplete="off" >    
                    <fieldset> 
                        <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                            <thead>
                                <th width="120" class="must_entry_caption">Company</th>
                                <th width="120">Location</th>
                                <th width="100" class="must_entry_caption">Within Group</th>
                                <th width="100" class="must_entry_caption">Party</th>
                                <th colspan="2" id="th_date_caption">Deli. Date</th>
                                <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('date_wise_production_1','','','','')" /></th>
                            </thead>
                            <tbody>
                            	<tr>
                                    <td>
										 <? 
                                       		 echo create_drop_down( "cbo_company_id",120,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/embellishment_delivery_entry_report_controller', this.value, 'load_drop_down_location', 'location_td');","","","","","",2);  ?>                            
                                    </td>
                                    <td id="location_td">
		                                <?php 
		                                    echo create_drop_down( 'cbo_location_name', 100, $blank_array, '', 1, '-- Select Location --', $selected, '', '', '', '', '', '', 2);
		                                ?>
		                            </td>
                                    
                                    <td>
										<? 
                                       		 echo create_drop_down( "cbo_customer_source",100,$yes_no,"", 1, "--All--",0, "load_drop_down( 'requires/embellishment_delivery_entry_report_controller', (document.getElementById('cbo_company_id').value+'_'+this.value), 'load_drop_down_buyer', 'buyer_td' );","","","","","",2);
                                        ?>
                                    </td>
                                    
                                    <td id="buyer_td">
										<? 
                                        	echo create_drop_down( "cbo_customer_name",100,$blank_array,"", 1, "--All--", $selected, "","","","","","",2);
                                        ?>
                                    </td>
                                    
                                     <td width="90">
                                    	<input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:80px;"/>                    				</td>
                                    <td width="90">
                                    	<input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:80px;"/>                        		    </td>
                                    <td>
                                    <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:80px" class="formbutton" /> 					</td>
                                </tr>
                                <tr>
                                    <td colspan="9" align="center"><? echo load_month_buttons(1);  ?></td>
                                </tr>
                        	<tbody>
                        </table> 
                    </fieldset>
              </form> 
          </div>
          <div id="report_container" align="center"></div>
          <div id="report_container2"></div> 
   </div>
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>

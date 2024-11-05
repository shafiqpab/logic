<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Production Incentive Payment Report
				
Functionality	:	
JS Functions	:
Created by		:	Ashraful
Creation date 	: 	08-05-2017
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

echo load_html_head_contents("Batch Creation Info", "../", 1, 1,'','','');
?>	
<script>

//var str=" this is ( this is ( this is (";
//var strin=str.replace("/(/"+g, "[");

//alert(strin);

//return;


var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{
		if( form_validation('cbo_company_id*txt_date','Company Name*Production Date')==false )
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*txt_date*cbo_location_id',"../")+'&report_title='+report_title;
		
	
		freeze_window(3);
		http.open("POST","requires/order_wise_avaliable_minutes_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			release_freezing();
			//alert (http.responseText);
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			show_msg('3');
			document.getElementById('report_container').innerHTML='<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
			
		}
	} 


	function openmypage(po_information,available_minit,id,line_name)
	{
		popup_width='1050px'; 
		var pre_po_available_min=$("#poWiseAvailableMinutes"+id).val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_avaliable_minutes_controller.php?po_iinformation='+po_information+'&available_minit='+available_minit+'&pre_po_available_min='+pre_po_available_min+'&action=distribute_available_minit', 'Distribute Used Minutes of line '+line_name, 'width='+popup_width+', height=270px,center=1,resize=0,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var available_minute_info=this.contentDoc.getElementById("po_available_minutes").value;
			$("#poWiseAvailableMinutes"+id).val(available_minute_info);
			$("#tr_"+id).css('background-color','#FFD6C1')
		}
	}
	
	

	function open_mypage_remarks(id)
	{
		var txt_appv_instra = $("#txt_remarks_"+id).val();	
		
		var data=txt_appv_instra;
		
		var title = 'Remarks ';	
		var page_link = 'requires/order_wise_avaliable_minutes_controller.php?data='+data+'&action=remarks_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
			$('#txt_remarks_'+id).val(appv_cause.value);
		}
	}

	
	function fnc_order_wise_available_minutes( operation )
	{
		if(operation==4)
		{ 
			 return;
		}
		
	/*	if(operation==2)
		{
			show_msg('13');
			return;
		}*/
		
		var j=0; var dataString=''; var all_barcodes=''; var error=0; var all_po_ids='';
		$("#resource_allocation_tbody").find('tbody tr').each(function()
		{
			//var poWiseAvailableMinutes=$(this).find('input[name="poWiseAvailableMinutes[]"]').val();
			if(error==0)
			{
				var location_id=$(this).find('input[name="locationId[]"]').val();
				var floor_id=$(this).find('input[name="floorId[]"]').val();
				var resource_id=$(this).find('input[name="resourceId[]"]').val();
				var buyer_ids=$(this).find('input[name="buyerIds[]"]').val();
				var orderIds=$(this).find('input[name="poIds[]"]').val();
				var gmtItemIds=$(this).find('input[name="gmtItemIds[]"]').val();
				var poDetails=$(this).find('input[name="poDetails[]"]').val();
				var poWiseAvailableMinutes=$(this).find('input[name="poWiseAvailableMinutes[]"]').val();
				if(all_po_ids!='') 	all_po_ids=all_po_ids+","+orderIds;
				else 				all_po_ids=orderIds;
				if(poWiseAvailableMinutes=='')
				{
					error=1;
					return;
				}
				var locationName=$(this).find("td:eq(1)").text().replace("(", "[").replace(")", "]");
				var floorName=$(this).find("td:eq(2)").text().replace("(", "[").replace(")", "]");
				var lineName=$(this).find("td:eq(3)").text().replace("(", "[").replace(")", "]");
				var buyerNames=$(this).find("td:eq(4)").text();
				var poNumbers=$(this).find('input[name="poNumbers[]"]').val().replace("(", "[").replace(")", "]");
				var fileNos=$(this).find("td:eq(6)").text();
				var referenceNos=$(this).find("td:eq(7)").text();
				var garmentItems=$(this).find("td:eq(8)").text().replace("(", "[").replace(")", "]");
				var smv=$(this).find("td:eq(9)").text();
				var operator=$(this).find("td:eq(15)").text();
				var helper=$(this).find("td:eq(16)").text();
				var manPower=$(this).find("td:eq(17)").text();
				var hourlyTarget=$(this).find("td:eq(18)").text();
				var capacity=$(this).find("td:eq(19)").text();
				var workingHour=$(this).find("td:eq(20)").text();
				
				var totalTarget=$(this).find("td:eq(12)").text();
				var totalProduced=$(this).find("td:eq(13)").text();
				var variancePeces=$(this).find("td:eq(14)").text();
				var availableMinutes=trim($(this).find("td:eq(10)").text());
				var producdMinutes=$(this).find("td:eq(11)").text();
				var remarks=$(this).find('input[name="txt_remarks[]"]').val();
	
				j++;
				dataString+='&locationId_' + j + '=' + location_id + '&floorId_' + j + '=' + floor_id + '&resourceId_' + j + '=' + resource_id + '&buyerIds_' + j + '=' + buyer_ids + '&orderIds_' + j + '=' + orderIds + '&gmtItemIds_' + j + '=' + gmtItemIds + '&poDetails_' + j + '=' + poDetails + '&poWiseAvailableMinutes_' + j + '=' + poWiseAvailableMinutes + '&locationName_' + j + '=' + locationName + '&floorName_' + j + '=' + floorName + '&lineName_' + j + '=' + lineName + '&buyerNames_' + j + '=' + buyerNames + '&poNumbers_' + j + '=' + poNumbers + '&fileNos_' + j + '=' + fileNos + '&referenceNos_' + j + '=' + referenceNos + '&garmentItems_' + j + '=' + garmentItems + '&smv_' + j + '=' + smv + '&operator_' + j + '=' + operator + '&helper_' + j + '=' + helper + '&manPower_' + j + '=' + manPower+ '&hourlyTarget_' + j + '=' + hourlyTarget+ '&capacity_' + j + '=' + capacity+ '&workingHour_' + j + '=' + workingHour+'&totalTarget_' + j + '=' + totalTarget+ '&totalProduced_' + j + '=' + totalProduced+ '&variancePeces_' + j + '=' + variancePeces+ '&availableMinutes_' + j + '=' + availableMinutes+'&producdMinutes_' + j + '=' + producdMinutes+'&remarks_' + j + '=' + remarks;
				
			}
		
			
		});
		if(error==1)
		{
			alert('Please distribute available minutes order wise Which line color is red.');
			return;
		}
		//alert(dataString);return;
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+'&all_po_ids='+all_po_ids+get_submitted_data_string('cbo_company_id*txt_date*cbo_location_id',"../")+dataString;
		freeze_window(operation);
		http.open("POST","requires/order_wise_avaliable_minutes_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_order_wise_available_minutes_reponse;
	}
	
	function fnc_order_wise_available_minutes_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
		//release_freezing();return;
			show_msg(trim(reponse[0]));
			
			if((reponse[0]==0 || reponse[0]==1))
			{
				set_button_status(1, permission, 'fnc_order_wise_available_minutes',1);
			}
			else if(reponse[0]==2)
			{
				reset_form('OrderWiseAvailableMinutes_1','report_container2','','',"");
			}
				
			release_freezing();
		}
	}
	
	function check_date_format(current_date,selected_date)
	{
		var fdate=current_date.split('-');
		var new_date_from=fdate[2]+'-'+fdate[1]+'-'+fdate[0];
		
		var tdate=selected_date.split('-');
		var new_date_to=tdate[2]+'-'+tdate[1]+'-'+tdate[0];
		
		var fromDate=new Date(new_date_from);
		
		var toDate=new Date(new_date_to);
		if(fromDate.getTime() <= toDate.getTime())
		{
			$("#txt_date").val("");
			alert("Production Date Must Be Less Than Current Date");
		} 

	}
	 
	function new_window()
	{
		
		//document.getElementById('scroll_body').style.overflow='auto';
		//document.getElementById('scroll_body').style.maxHeight='none'; 
		$("#tab_save_button").hide();
		//$("#table_body1 tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>'); 
		d.close();
		//../css/style_common.css
		//document.getElementById('scroll_body').style.overflowY='scroll';
		//document.getElementById('scroll_body').style.maxHeight='300px';
		$("#tab_save_button").show();
	}	 
	 
	 
</script>

</head>
<body onLoad="set_hotkey();">

<form id="OrderWiseAvailableMinutes_1">
    <div style="width:100%;" align="center">    
    
       <? echo load_freeze_divs ("../",$permission);  ?>
         
         <div id="content_search_panel" >      
         <fieldset style="width:500px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="200" class="must_entry_caption">Company</th>
                    <th width="200">Location</th>
                    <th class="must_entry_caption">Production Date</th>
            
                    <th width="110"><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('LineWiseProductivityAnalysis_1','report_container','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general"  bgcolor="">
                       <td>
							<? 
							
                                echo create_drop_down( "cbo_company_id", 200, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/order_wise_avaliable_minutes_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                            ?>                            
                        </td>
                        <td id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location_id", 140, $blank_array,"", 1, "-- Select Location --", "", "" );
                            ?>                            
                        </td>
                         <td>
                            <input type="text" name="txt_date" id="txt_date" class="datepicker" style="width:90px;" onChange="check_date_format('<?php echo date("d-m-Y"); ?>',this.value)" readonly/>
                        </td>
                       
                        <td>
                            <!--Not Use --hidden button> --> 
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:100px" class="formbutton" />
                  
                        </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>
    <div id="report_container2" align="left"></div>
    <br/> <br/>
    
 </form>   
</body>


<script src="../includes/functions_bottom.js" type="text/javascript"></script> 
<script>

</html>

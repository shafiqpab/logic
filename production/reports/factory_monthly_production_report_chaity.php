<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Production  Report.
Functionality	:	
JS Functions	:
Created by		:	Rehan 
Creation date 	: 	30-3-2019
Updated by 		: 	Shafiq	
Update date		: 	17-06-2019	   
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
echo load_html_head_contents("Production  Report", "../../", 1, 1,$unicode,1,1);
 
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	var tableFilters = 
		{
			
		} 

	function job_no_popup(type)
	{
		if( form_validation('cbo_working_company_id','Company Name')==false)
		{
			return;
		}
		var company = $("#cbo_working_company_id").val();	
		var buyer=$("#cbo_buyer_name").val();
		var cbo_year = $("#cbo_year").val();
		var txt_job_no = $("#txt_job_no").val();	
		var page_link='requires/factory_monthly_production_report_controller_chaity.php?action=job_wise_search&company='+company+'&buyer='+buyer+'&cbo_year='+cbo_year+'&type='+type+'&txt_job_no='+txt_job_no; 
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=780px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			//alert(type);
			if(type==1)
			{
				$('#txt_job_no').val(job_no);
				$('#txt_job_id').val(job_id);
			}
			else if(type==2)
			{
				$('#txt_style_ref').val(job_no);
				$('#txt_style_hidden').val(job_id);
			}
			else if(type==3)
			{
				$('#txt_po_no').val(job_no);
				$('#txt_po_no_hidden').val(job_id);
			}
		}
	}

	
	function fn_report_generated(type)
	{
		if(type==1)
		{
			if($('#cbo_working_company_id').val()==0){
				var field_id='cbo_working_company_id*cbo_production_process*txt_date_from*txt_date_to';	
				var field='Working Company*Production Process*From Date*To Date';	
			}
			else
			{
				var field_id='cbo_working_company_id*cbo_production_process*txt_date_from*txt_date_to';	
				var field='Working Company*Production Process*From Date*To Date';	
			}
		}
		else //Summary....
		{
			if($('#cbo_working_company_id').val()==0){
				var field_id='cbo_working_company_id*txt_date_from*txt_date_to';	
				var field='Working Company*From Date*To Date';	
			}
			else
			{
				var field_id='cbo_working_company_id*txt_date_from*txt_date_to';	
				var field='Working Company*From Date*To Date';	
			}
		}
		
		
		
		if( form_validation(field_id,field)==false )
		{
			return;
		}
		else
		{
			var report_title=$( "div.form_caption" ).html();
			var from_date = $('#txt_date_from').val();
			var to_date = $('#txt_date_to').val();
			var datediff = date_diff( 'd', from_date, to_date )+1;
			//alert(datediff);
			var data="action=report_generate"+get_submitted_data_string('cbo_working_company_id*cbo_location*cbo_production_process*cbo_buyer_name*cbo_floor_group_name*cbo_floor*cbo_year*txt_job_no*txt_style_ref*txt_po_no*txt_date_from*txt_date_to',"../../")+"&report_title="+report_title+"&type="+type+"&datediff="+datediff;
			freeze_window(3);
			http.open("POST","requires/factory_monthly_production_report_controller_chaity.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("****"); 
			var rpt_type = response[4]*1;
			$('#report_container2').html(response[0]);
			
			if(rpt_type==2 || rpt_type==4)
			{
				document.getElementById('report_container').innerHTML='<input type="button" onclick="new_window(1,'+rpt_type+')" value="Print Preview(summary)" name="Print" class="formbutton" style="width:150px">&nbsp;&nbsp;<a href="requires/'+response[3]+'" style="text-decoration:none"><input type="button" value="Excel Preview(summary)" name="Print" class="formbutton" style="width:150px"></a>&nbsp;&nbsp;<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(3,'+rpt_type+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			}
			else
			{
				document.getElementById('report_container').innerHTML='<input type="button" onclick="new_window(1,'+rpt_type+')" value="Print Preview(summary)" name="Print" class="formbutton" style="width:150px">&nbsp;&nbsp;<a href="requires/'+response[3]+'" style="text-decoration:none"><input type="button" value="Excel Preview(summary)" name="Print" class="formbutton" style="width:150px"></a>';
			}			

			setFilterGrid('tableBody',-1,tableFilters); 
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window(type,rpt_type)
	{
		if(rpt_type==2 || rpt_type==4)
		{		
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			document.getElementById('scroll_body_summary').style.overflow="auto";
			document.getElementById('scroll_body_summary').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			if(type==1)
			{
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><title></title><link rel="stylesheet" href="../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('buyer_wise_summary').innerHTML+'</body</html>');
			}
			else
			{
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><title></title><link rel="stylesheet" href="../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			}		

			d.close(); 
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="300px";
			
			document.getElementById('scroll_body_summary').style.overflowY="scroll";
			document.getElementById('scroll_body_summary').style.maxHeight="300px";
		}
		else
		{
			document.getElementById('scroll_body_summary').style.overflow="auto";
			document.getElementById('scroll_body_summary').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><title></title><link rel="stylesheet" href="../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('buyer_wise_summary').innerHTML+'</body</html>');					

			d.close(); 			
			document.getElementById('scroll_body_summary').style.overflowY="scroll";
			document.getElementById('scroll_body_summary').style.maxHeight="300px";
		}
	}

	function fn_report_excel_generated(excel_type) //Excel Convert Only
	{
		if (form_validation('cbo_working_company_id*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false)
        {
            return;
        }
		else
		{
			var report_title=$( "div.form_caption" ).html();
			var from_date = $('#txt_date_from').val();
			var to_date = $('#txt_date_to').val();
			var datediff = date_diff( 'd', from_date, to_date )+1;
			//alert(datediff);
			var data="action=report_generate_in_excel"+get_submitted_data_string('cbo_working_company_id*cbo_location*cbo_production_process*cbo_buyer_name*cbo_floor_group_name*cbo_floor*cbo_year*txt_job_no*txt_style_ref*txt_po_no*txt_date_from*txt_date_to',"../../")+"&report_title="+report_title+"&type="+excel_type+"&datediff="+datediff;
			freeze_window(3);
			http.open("POST","requires/factory_monthly_production_report_controller_chaity.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse2;
		}
	}

	function fn_report_generated_reponse2()
	{
		if(http.readyState == 4) 
		{
			//alert(2333);
			var response=trim(http.responseText).split("****"); 
			// alert(response[0]);
			$('#report_container2').html(response[0]);
			$('#report_container2').css('display','none');
			if(response!='')
			{
				$('#aa1').removeAttr('href').attr('href','requires/'+response[1]);
				document.getElementById('aa1').click();
			}
			//$('#report_container2').css('display','block');
			show_msg('3');
			release_freezing();
		}
	}

	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}
	
	function fn_disable_com(str){
		if(str==2){$("#cbo_company_name").attr('disabled','disabled');}
		else{ $('#cbo_company_name').removeAttr("disabled");}
		if(str==1){$("#cbo_working_company_id").attr('disabled','disabled');}
		else{ $('#cbo_working_company_id').removeAttr("disabled");}
	}
	
	 
function generate_po_report_popup(po_id,country_id,company_name,process_rpt_type,from_date,to_date,action,type)
{
	//var garments_nature = $("#cbo_garments_nature").val();
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/factory_monthly_production_report_controller_chaity.php?po_id='+po_id+'&company_name='+company_name+'&country_id='+country_id+'&type='+type+'&process_rpt_type='+process_rpt_type+'&from_date='+from_date+'&to_date='+to_date+'&action='+action, 'PO Qty with Color Size Breakdown', 'width=750px,height=350px,center=1,resize=0,scrolling=0','../');
}

function fnc_date_check(todate)
{
	var from_date = $('#txt_date_from').val();
	var to_date = $('#txt_date_to').val();
	to_dates=to_date.split('-');
	from_dates=from_date.split('-');
	to_mon_year=to_dates[1]+'-'+to_dates[2];
	from_mon_year=from_dates[1]+'-'+from_dates[2];
	//alert(from_mon_year);
	if(from_mon_year==to_mon_year)
	{
		$('#txt_date_from').val(from_date);
		$('#txt_date_to').val(to_date);
	}
	else
	{
		//alert('Month Mixed Not Allow');
		//$('#txt_date_to').val('');
	}
}

function getCompanyId(company_id) 
{
    var company_id = document.getElementById('cbo_working_company_id').value;
    //var search_type = document.getElementById('cbo_search_by').value;
    if(company_id !='') {
	  var data="action=load_drop_down_location&data="+company_id;
	  //alert(data);die;
	  http.open("POST","requires/factory_monthly_production_report_controller_chaity.php",true);
	  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	  http.send(data); 
	  http.onreadystatechange = function(){
          if(http.readyState == 4) 
          {
              var response = trim(http.responseText);
              //$('#location_td').html(response);
              $('#location_td').html(response);
              set_multiselect('cbo_location','0','0','','0');
              setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location,'0');getFloorGroup();") ,3000)]; 
              
              //set_multiselect('cbo_buyer_name','0','0','','0');
             // fn_buyer_visibility(search_type);
          }			 
      };
    }         
}

function getFloorGroup() 
{
	var company_id = document.getElementById('cbo_working_company_id').value;
    var loc_id = document.getElementById('cbo_location').value;
   
    if(loc_id !='') {
	  var data="action=load_drop_down_floor_group&data="+company_id+'_'+loc_id;
	  //alert(data);die;
	  http.open("POST","requires/factory_monthly_production_report_controller_chaity.php",true);
	  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	  http.send(data); 
	  http.onreadystatechange = function(){
          if(http.readyState == 4) 
          {
              var response = trim(http.responseText);
              $('#floor_group_td').html(response);
              getFloor();
              // set_multiselect('cbo_floor_group_name','0','0','','0');
              // setTimeout[($("#floor_group_td a").attr("onclick","disappear_list(cbo_floor_group_name,'0');getFloor();") ,3000)]; 
              // set_multiselect('cbo_floor','0','0','0','0');	
             // fn_buyer_visibility(search_type);
          }			 
      };
    }         
}

function getFloor() 
{
	var company_id = document.getElementById('cbo_working_company_id').value;
    var loc_id = document.getElementById('cbo_location').value;
    // var floor_group = document.getElementById('cbo_floor_group_name').value;
   
    if(loc_id !='') 
    {
	  var data="action=load_drop_down_floor&data="+company_id+'_'+loc_id;
	  http.open("POST","requires/factory_monthly_production_report_controller_chaity.php",true);
	  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	  http.send(data); 
	  http.onreadystatechange = function(){
          if(http.readyState == 4) 
          {
              var response = trim(http.responseText);
              $('#floor_td').html(response);
              set_multiselect('cbo_floor','0','0','','0');
          }			 
      };
    }         
}

</script>
</head>
<body onLoad="set_hotkey();">
<form id="sewingQcReport_1">
    <div style="width:1190px; margin:1px auto;">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1190px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:870px" >    
         <fieldset style="width:890px;">
            <table class="rpt_table" width="1190" cellpadding="0" cellspacing="0" align="center" rules="all" border="1">
               <thead>                    
                    <tr>
                        <th class="must_entry_caption">Working Comapny</th>
                        <th>Wor. Com. Location</th>
                        <th>Buyer</th>
                       <!--	<th>Job Year</th>
                        <th>Job Number</th>
                        <th>Style Number</th>
                        <th>PO Number</th> 
                        -->
                        
                        <th>Floor Group</th>
                        <th>Floor</th>
                        <th class="must_entry_caption">Production Date</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:50px" value="Reset" onClick="fn_disable_com(0)"/></th>
                    </tr>    
                 </thead>
                <tbody>
                <tr class="general">
                 <td width="150" align="center" id="working_company_td"> 
                        <?
						//load_drop_down( 'requires/factory_monthly_production_report_controller_chaity',this.value, 'load_drop_down_buyer', 'buyer_td' )
                            echo create_drop_down( "cbo_working_company_id", 170, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "getCompanyId(this.value); fn_disable_com(2)" );
                        ?>
                    </td><td width="110" id="location_td" align="center">
                        <? 
                            echo create_drop_down( "cbo_location", 130, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                        ?>
                    </td>
                     <td width="110" id="buyer_td" align="center">
                        <? 
                            //echo create_drop_down( "cbo_buyer_name", 110, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
							echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by  buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );
                        ?>
                    </td>
                     <td style="display:none">
						<?
                           echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", "", "",0,"" );
                        ?>
                    </td>
                     <td style="display:none">
                       <input type="text"  name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px;" tabindex="1" placeholder="Write/Browse" onDblClick="job_no_popup(1);"> 
                 	 </td>
                      <td style="display:none">
                       <input type="text"  name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:70px;" tabindex="1" placeholder="Write/Browse" onDblClick="job_no_popup(2);"> 
                 	 </td>
                      <td style="display:none">
                       <input type="text"  name="txt_po_no" id="txt_po_no" class="text_boxes" style="width:70px;" tabindex="1" placeholder="Write/Browse" onDblClick="job_no_popup(3);"> 
                      </td>
                                 
                    <td width="110" id="floor_group_td" align="center">
                    	<? 
                        	echo create_drop_down( "cbo_floor_group_name", 130, $blank_array,"", 1, "-- Select --", $selected, "",0,"" );
                        ?>
                    </td>
                    <td width="110" id="floor_td" align="center">
                    	<? 
                            echo create_drop_down( "cbo_floor", 130, $blank_array,"", 1, "-- Select --", $selected, "",0,"" );
                        ?>
                    </td>
                    <td>
                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" onChange="fnc_date_check()" placeholder="From Date" value="<? echo date("d-m-Y",time());?>" >&nbsp; To
                    	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" onChange="fnc_date_check(this.value)"  placeholder="To Date" value="<? echo date("d-m-Y",time());?>"  >
                    </td>
                    <td width="330">
                        <input type="button" style="display: none;" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated(1)" /> &nbsp; 
                        <input type="button" id="show_button" class="formbutton" style="width:60px" value="Show" onClick="fn_report_generated(2)" />
						<input type="button" id="show_button" class="formbutton" style="width:60px" value="Show 2" onClick="fn_report_generated(4)" />
                        <input type="button" id="show_button" class="formbutton" style="width:60px" value="Summary" onClick="fn_report_generated(3)" />
                        <input type="hidden" id="cbo_production_process" value="0" name="">
                    </td>
                </tr>
                </tbody>
            </table>
            <table width="1190">
            	<tr>
                	<td colspan="6" width="820" align="center">
 						<? echo load_month_buttons(1); ?>
                   	</td>
                   	<td align="center">
                   		<input type="button" id="show_button" class="formbutton" style="width:100px;" value="Convert to Excel" onClick="fn_report_excel_generated(10)" /><a   id="aa1" href="" style="text-decoration:none" download hidden></a>
                   	</td>
                </tr>
            </table> 
            <br />
        </fieldset>
    </div>
    </div>
    
    <div id="report_container" align="center" style="padding: 10px;">
    	
    </div>
    <div id="report_container2" align="center"></div>
 </form>    
</body>
<script>
	set_multiselect('cbo_working_company_id','0','0','0','0');	
	 
	setTimeout[($("#working_company_td a").attr("onclick","disappear_list(cbo_working_company_id,'0');getCompanyId();") ,3000)]; 
	
	// $('#cbo_location').val(0);
	set_multiselect('cbo_location','0','0','0','0');	
	// set_multiselect('cbo_floor_group_name','0','0','0','0');	
	set_multiselect('cbo_floor','0','0','0','0');	
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>

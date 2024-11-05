<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Date wise Production Reconciliation Report
Functionality	:	
JS Functions	:
Created by		:	Md. Rakib Hasan Mondal
Creation date 	: 	19-06-2023
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
//----------------------------------------------------------------------------------------
echo load_html_head_contents("Date Wise Production Report", "../../", 1, 1, $unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';  

	function fn_report_generated(type)
	{ 

		if ($('#txt_style_ref').val() =='') 
		{
			if (!$('#cbo_company_name').val()) 
			{
				if (form_validation('cbo_com_fac_name*txt_date_from*txt_date_to','Gmts. Del. Company*From Date*To Date')==false)
				{
					return;
				}
			}
			
		}
		else
		{
			if (!$('#cbo_company_name').val()) 
			{
				if (form_validation('cbo_com_fac_name','Gmts. Del. Company')==false)
				{
					return;
				}
			}
		}
		
		var data="action=report_generate"+"&type="+type+get_submitted_data_string('cbo_company_name*cbo_com_fac_name*cbo_location*cbo_buyer_name*cbo_brand*cbo_season*cbo_season_year*cbo_search_by*txt_style_ref*txt_date_from*txt_date_to*delivery_status',"../../");
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/date_wise_production_reconciliation_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
		
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####"); 
			show_msg('3');
			release_freezing();
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../');  

 			append_report_checkbox('table_header_1',1);  
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
		document.getElementById('hide_scroll').style.maxHeight='1000px';
		$("#table_body tr:first").show();
		$("#sub_list_view tr:first").hide();
	}

	function reset_company_val()
	{
		 var working_factory_val = $("#cbo_com_fac_name").val();
		 if (working_factory_val) $("#cbo_company_name").val();
	}	

	function getCompanyId() 
	{	 
	    var company_id = document.getElementById('cbo_com_fac_name').value;
	    //var search_type = document.getElementById('cbo_search_by').value;
	    if(company_id !='') {
	      var data="action=load_drop_down_location&choosenCompany="+company_id;
	      http.open("POST","requires/date_wise_production_reconciliation_report_controller.php",true);
	      http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	      http.send(data); 
	      http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText).split("**");
	              $('#location_td').html(response[0]); 
	              set_multiselect('cbo_location','0','0','','0'); 
				 	setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location,'0');") ,3000)];	
	          }			 
	      };
	    }         
	}
	function getBuyerId() 
	{	 
	    var company_id = document.getElementById('cbo_company_name').value;
	    //var search_type = document.getElementById('cbo_search_by').value;
		var data="action=load_drop_down_buyer&company_id="+company_id;
		http.open("POST","requires/date_wise_production_reconciliation_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data); 
		http.onreadystatechange = function(){
			if(http.readyState == 4) 
			{
				var response = trim(http.responseText).split("**");
				$('#buyer_td').html(response[0]);   
			}			 
		};       
	}
	
	/* function getLocationId() 
	{	 
	    var location_id = document.getElementById('cbo_location').value; 
	    if(location_id !='') {
	      var data="action=load_drop_down_floor&choosenLocation="+location_id;
	      http.open("POST","requires/date_wise_production_reconciliation_report_controller.php",true);
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
	} */
	function browseJobStyle(popupFor)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}

		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		  
        let title = (popupFor == 1) ? 'Job No Search' : 'Style Search' ; 
		var page_link='requires/date_wise_production_reconciliation_report_controller.php?action=job_style_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&popupFor='+popupFor;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			var popupFor=this.contentDoc.getElementById("hide_popup_for").value;
            $('#txt_job_id').val(job_id);
            if(popupFor == 1)
            {
                $('#txt_job_no').val(job_no);
            }
            else
            { 
                $('#txt_style_ref').val(job_no);
            }
			
		}
	} 
 
	 
</script>
</head>
<body onLoad="set_hotkey();">
<form>
    <div style="width:100%;" align="center">
    <? 
		echo load_freeze_divs ("../../",''); 
		$search_width = 1300;
	?>
    <h3 style="width:<?= $search_width+20; ?>px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
    <div id="content_search_panel" style="width:<?= $search_width+20; ?>px" align="center" >      
        <fieldset>  
            <table class="rpt_table" width="<?= $search_width; ?>px" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
                <thead>                    
                    <tr>
                        <th class="must_entry_caption">Company Name</th>
                        <th>Gmts. Del. Company</th>
                        <th>Location</th>
                        <th>Buyer Name</th>
                        <th>Brand</th>
                        <th>Season</th>
                        <th>Season Year</th>  
                        <th>Style Ref.</th>  
						<th>Date Type</th>
                        <th id="search_by_td_up" style="color:blue" colspan="2">Please Enter Org Shipment Date</th> 
                        <th>Gmts Del. Status </th> 
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                    </tr>    

					
                </thead>
                <tbody>
                    <tr class="general">
                        <td width="130" id="lc_company_td"> 
                            <?
                                echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 0, "-- Select Company --", $selected, "" );
                            ?>
                        </td>
                        <td width="110" id="factory_td">
                            <? 
                                echo create_drop_down( "cbo_com_fac_name", 110, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 0, "-- Select Factory --", $selected, "","","" );
                            ?>
                        </td>
                        <td width="110" id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location", 110, $blank_array,"", 0, "-- All --", $selected, "",1,"" );
                            ?>
                        </td>
                        <td width="110" id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 110, $blank_array,"", 1, "-- All --", $selected, "",1,"" );
                            ?>
                        </td>
                        <td width="110" id="brand_td">
                            <? 
                                echo create_drop_down( "cbo_brand", 110, $blank_array,"", 1, "-- All --", $selected, "",1,"" );
                            ?>
                        </td>
                        <td width="110" id="season_td">
                            <? 
                                echo create_drop_down( "cbo_season", 110, $blank_array,"", 1, "-- All --", $selected, "",1,"" );
                            ?>
                        </td>
                        <td width="60" id="season_year_td">
                            <? 
                                echo create_drop_down( "cbo_season_year", 60, $year,"", 1, "-- All --", $selected, "",0,"" );
                            ?>
                        </td>  
                         <td width="140">
                        	 <!-- <input type="text"  name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:70px;"   placeholder="Write"> -->
							 <input style="width:140px;" type="text"  onDblClick="browseJobStyle(2)" class="text_boxes" autocomplete="off" placeholder="Browse/Write" name="txt_style_ref" id="txt_style_ref" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).ondblclick()" />
                        </td> 
						<td width="90">
							<?
                                $search_by_arr=array(1=>"Org Shipment Date",2=>"Gmts delivery Date");
                                $dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
                                echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                            ?>
                        </td>
                        <td width="70">
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" onChange="fn_add_date_field()" >
                        </td> 
                        <td width="70">
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date"  >
							 
                        </td>
						<td width="70">
							<?
                                $search_by_arr=array(2=>"Partial Delivery",3=>"Full Delivery/Closed ");					
                                echo create_drop_down( "delivery_status", 110, $search_by_arr,"",0, "--Select--", 3,$selected,0 ); 
                            ?>
                        </td>
                        <td >
                            <input type="button" id="show_button" class="formbutton" style="width:70px;" value="Show" onClick="fn_report_generated(1)" /> 
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="general">
                        <td colspan="17" align="center">
							<? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </tfoot>
            </table> 
        </fieldset>
    </div>
    <div id="report_container" style="margin: 30px 0;" align="center"></div>
    <div id="report_container2" align="center"></div>
    </div>
</form>    
</body>
<script>
	set_multiselect('cbo_company_name*cbo_com_fac_name*cbo_location','0*0*0','0','','0*0*0');
	setTimeout[($("#factory_td a").attr("onclick","disappear_list(cbo_com_fac_name,'0');getCompanyId();") ,3000)];
	setTimeout[($("#lc_company_td a").attr("onclick","disappear_list(cbo_company_name,'0');getBuyerId();") ,3000)];

	$("#multi_select_cbo_com_fac_name a").click(function(){
		var company=$("#cbo_com_fac_name").val();
		if (company) 
		{
			$('#cbo_search_by').val(2);
			$('#cbo_search_by').attr("disabled","disabled");
		}else{
			$('#cbo_search_by').val(1);
			$('#cbo_search_by').removeAttr("disabled");
		}
		// alert(company);
		
		reset_company_val();
 	});
		
</script> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>$('#cbo_location').val(0); </script>
</html>

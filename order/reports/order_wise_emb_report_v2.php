<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Order Wise Embellishment Report v2.
Functionality	:	
JS Functions	:
Created by		:	Md. Mamun Ahmed Sagor
Creation date 	: 	11-12-2022
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
echo load_html_head_contents("Order Wise Embellishment Report v2", "../../", 1, 1,$unicode,'1','');

?>	

<script>
	var tableFilters = {}	
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
 
	var permission = '<? echo $permission; ?>';
	
	function fn_report_generated(type)
	{
		var search_by = $("#cbo_search_by").val();
		var txt_date_from = $("#txt_date_from").val();
		var txt_date_to = $("#txt_date_to").val();
		if(search_by !=0)
		{
			if(form_validation('cbo_working_company*cbo_search_by*txt_search_type','Working Company Name*Search By*Search Type')==false)
			{			
				return; 
			}
		}
		else if(txt_date_from !="" && txt_date_to !="")
		{
			if(form_validation('cbo_working_company*txt_date_from*txt_date_to*cbo_source','Working Company Name*Date from*Date To*Source')==false)
			{			
				return;
			}
		}
		else
		{
			if(form_validation('cbo_working_company*cbo_source*cbo_search_by*txt_search_type*txt_date_from*txt_date_to','Working Company Name*Source*Search By*Search Type*Date from*Date To')==false)
			{			
				return;
			}
			
		}

		var data="action=report_generate&reportType="+type+get_submitted_data_string('cbo_working_company*cbo_location_name*cbo_emb_type*cbo_source*cbo_party_name*cbo_job_year*cbo_search_by*cbo_buyer_name*txt_search_type*txt_date_from*txt_date_to',"../../");
		freeze_window(3);
		http.open("POST","requires/order_wise_emb_report_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
		
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
					
				
			// setFilterGrid("table_body",-1,tableFilters);		
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
	

	function new_window(html_filter_print,type)
	{
		if(type==1)
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			document.getElementById('approval_div').style.overflow="auto";
			document.getElementById('approval_div').style.maxHeight="none";
			
			$("#data_panel2").hide();
			
			if(html_filter_print*1>1) $("#table_body tr:first").hide();
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close();
			
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="400px";
			document.getElementById('approval_div').style.overflowY="scroll";
			document.getElementById('approval_div').style.maxHeight="380px";
			
			$("#data_panel2").show();
			
			if(html_filter_print*1>1) $("#table_body tr:first").show();
		}
		else if(type==2)
		{
			document.getElementById('approval_div').style.overflow="auto";
			document.getElementById('approval_div').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('embell_approval_div').innerHTML+'</body</html>');
			d.close();
			
			document.getElementById('approval_div').style.overflowY="scroll";
			document.getElementById('approval_div').style.maxHeight="380px";
		}
	}	
	
	function getCompanyId() 
	{
	    var company_id = document.getElementById('cbo_working_company').value;
	    //var search_type = document.getElementById('cbo_search_by').value;
	    if(company_id !='') {
		  var data="action=load_drop_down_location&data="+company_id;
		  //alert(data);die;
		  http.open("POST","requires/order_wise_emb_report_controller_v2.php",true);
		  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		  http.send(data); 
		  http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#location_td').html(response);
	              set_multiselect('cbo_location_name','0','0','','0');
	              // setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location_name,'0');getLocationId();") ,3000)]; 
	              //========================
	              // load_drop_down( 'requires/order_wise_emb_report_controller_v2', company_id, 'load_drop_down_buyer', 'buyer_td' );
	          }			 
	      };
	    }         
	}

	function getLocationId() 
	{
	    var company_id = document.getElementById('cbo_working_company').value;
	    var location_id = document.getElementById('cbo_location_name').value;
	    if(company_id !='') {
		  var data="action=load_drop_down_floor&data="+company_id+'_'+location_id;
		  //alert(data);die;
		  http.open("POST","requires/order_wise_emb_report_controller_v2.php",true);
		  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		  http.send(data); 
		  http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#floor_td').html(response);
	              set_multiselect('cbo_floor_name','0','0','','0');
	          }			 
	      };
	    }         
	}


	$(function(){
		var txt = "Company";
		$("#search_by_txt").text('Job').css('color','blue');
		$("#txt_search_type").attr("placeholder", "Browse/Write Job");
		$("#cbo_search_by").change(function()
		{
			var type = parseInt($(this).val());
			switch(type) {				
				case 1 :
					$("#search_by_txt").text('Job').css('color','blue');
					$("#txt_search_type").attr("placeholder", "Browse/Enter Job");
					$("#txt_search_type").val('');
					break;
				case 2 :
					$("#search_by_txt").text('Style').css('color','blue');
					$("#txt_search_type").attr("placeholder", "Browse/Enter Style");
					$("#txt_search_type").val('');
					break;
				case 3 :
					$("#search_by_txt").text('PO').css('color','blue');
					$("#txt_search_type").attr("placeholder", "Browse/Enter PO");
					$("#txt_search_type").val('');
					break;
				case 4 :
					$("#search_by_txt").text('Cutting No').css('color','blue');
					$("#txt_search_type").attr("placeholder", "Browse/Enter Cutting");
					$("#txt_search_type").val('');
					break;
				case 5 :
					$("#search_by_txt").text('Buyer').css('color','blue');
					// $("#txt_search_type").attr("placeholder", "Enter Buyer");
					var company_id = document.getElementById('cbo_working_company').value;
					load_drop_down( 'requires/order_wise_emb_report_controller_v2', company_id, 'load_drop_down_buyer', 'buyer_td' );
					$("#txt_search_type").val('');
					break;	
				default :
					$("#search_by_txt").text('Job').css('color','blue');
					$("#txt_search_type").attr("placeholder", "Browse/Write Job");
					$("#txt_search_type").val('');
					break;

			}
		});
	});

	function openmypage_searchby()
	{
		if( form_validation('cbo_working_company','Working Company Name')==false )
			{
				return;
			}
			var company = $("#cbo_working_company").val();	
			var location = $("#cbo_location_name").val();
			var floor = $("#cbo_floor_name").val();
			var embel_type = $("#cbo_emb_type").val();
			var source = $("#cbo_source").val();
			var page_link='requires/order_wise_emb_report_controller_v2.php?action=search_by_popup&company='+company+'&location='+location+'&floor='+floor+'&embel_type='+embel_type+'&source='+source;
			var title="Search By Popup";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=540px,height=370px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				//var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
				//var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
				var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
				//alert(style_no);
				data=style_no.split("_");
				//$("#txt_style_ref").val(data[3]);
				//
				
				// $('#txt_style_ref_id').val(data[0]);
				// $('#txt_job_no').val(data[1]);
				// $("#txt_job_no_hidden").val(data[1]); 
		  // 		$('#txt_ref_no').val(data[2]);
		  // 		$('#txt_job_no').attr('disabled','true'); 
			}
	}

	function open_emb_popup(param) //po,country,item,color,cutting,source,date from, date to , production type
	{	
		
		var page_link='requires/order_wise_emb_report_controller_v2.php?action=open_emb_popup&data='+param;
		var title="Emblishment By Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=640px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			
		}
	}

	function openmypage_job()
	{
		 
		 
		var cbo_search_by=$("#cbo_search_by").val();
		var page_link='requires/order_wise_emb_report_controller_v2.php?action=job_popup&cbo_search_by='+cbo_search_by;
		var title="Search Job Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_id=this.contentDoc.getElementById("selected_id").value;  
			var job_no=this.contentDoc.getElementById("selected_name").value; 
 			$("#hidden_job_id").val(job_id);
			$("#txt_search_type").val(job_no);
			  
		}
	}
	

</script>

</head>

<body onLoad="set_hotkey();">
<form id="order_wise_embell_approval_rpt">
<input type="hidden" id="hidden_job_id" name="hidden_job_id" value="0">
 
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
        <h3 align="left" id="accordion_h1" style="width:1530px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
			<fieldset style="width:1530px;">
        	<legend>Search Panel</legend>
                <table class="rpt_table" width="1510" cellpadding="1" cellspacing="2">
                   <thead>                    
                        <th width="150" class="must_entry_caption">Working Company Name</th>
                        <th width="130">Location</th>
                        <th width="80">Year</th>
                        <th width="130">Emb. Type</th>
                        <th width="130" class="must_entry_caption">Source</th>
                        <th width="130">Party</th>
                        <th width="130">Buyer</th>
                        <th width="130" class="must_entry_caption">Search By</th>
                        <th width="150" id="search_by_txt"></th>
                        <th width="210" class="must_entry_caption">Date</th>
                        <th width=""><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td id="working_company_td"> 
                            <?
                               echo create_drop_down( "cbo_working_company", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/order_wise_emb_report_controller_v2',this.value, 'load_drop_down_location', 'location_td' );" );
                            ?>
                        </td>
                        <td id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location_name", 130, $blank_array,"", 1, "-- Select Location --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td id="year_td">
	                        <?
	                        	// $year_current=date("Y");
	                        	echo create_drop_down( "cbo_job_year", 80, $year,"", 1, "All",$year_current,'','');
	                    	?>
                        </td>
                        <td id="emb_td">
							 <? 
                                echo create_drop_down( "cbo_emb_type", 130, $emblishment_name_array,"", 1, "-Select Emb Type - ", $selected, "" );
                             ?>	
                        </td>
                        <td>
                            <? 
                                // echo create_drop_down( "cbo_buyer_name", 130, $knitting_source,"", 1, "-- Select Source --", $selected, "",0,"" );
                            ?>
                            <?
								echo create_drop_down("cbo_source",130,$knitting_source,"", 1, "-- Select --", 0,"load_drop_down( 'requires/order_wise_emb_report_controller_v2', this.value+'_'+document.getElementById('cbo_working_company').value, 'load_drop_down_party','party_td');",0,'1,3');
							?>
                          </td>
                          <td id="party_td">
                            <? 
                                echo create_drop_down( "cbo_party_name", 130, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"" );
                            ?>	
                          
                          </td>
                        <td id="buyer_td">
                             <? 
                             echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by  buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" ); 
                            ?>
                        </td>
                          <td>
                           <? 
                           		$search_by = array(1=>"Job",2=>"Style",3=>"PO",4=>"Cutting No");//,5=>"Buyer"
                                echo create_drop_down( "cbo_search_by", 130, $search_by,"", 1, "-- Select --", $selected, "",0,"" );
                            ?>
                           <!--  <input type="text" name="cbo_search_by" id="cbo_search_by" class="text_boxes" placeholder="Browse........" readonly="" onDblClick="openmypage_searchby();">
                            <input type="hidden" name="search_by_id" id="search_by_id" value=""> -->
                          </td>
                           <td>
                        	<input type="text" name="txt_search_type" id="txt_search_type" class="text_boxes" placeholder="Browse/Write"  ondblclick="openmypage_job();">
                    	</td>
                        <td>
                        	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" >&nbsp; To
                        	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date" >
                    	</td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(2)" />
                        </td>
                    </tr>
                    </tbody>
                </table>
                <table cellpadding="1" cellspacing="2">
                    <tr>
                        <td>
                            <? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </table> 
        	</fieldset>
        </div>
    </div>
    <div id="report_container" align="center" style="padding: 10px 0"></div>
    <div id="report_container2"></div>
 </form>
 <script>
	set_multiselect('cbo_working_company','0','0','0','0');	
	set_multiselect('cbo_location_name','0','0','','0');
	
	setTimeout[($("#working_company_td a").attr("onclick","disappear_list(cbo_working_company,'0');getCompanyId();") ,3000)]; 
	// setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_working_company,'0');getLocationId();") ,3000)]; 
	// $('#cbo_location').val(0);
</script>    
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</body>
</html>

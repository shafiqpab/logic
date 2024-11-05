<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Floor wise Daily RMG Production Report.
Functionality	:	
JS Functions	:
Created by		:	
Creation date 	: 	10-04-2019
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
echo load_html_head_contents("Date Wise Production Report", "../../", 1, 1,$unicode,1,1);
?>	

<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	var tableFilters = 
	{} 
	
 	function open_order_no()
	{
		var job_no=$("#txt_job_no").val();
		var job_id=$("#hidden_job_id").val();
		var buyer=$("#cbo_buyer_name").val();
		var cbo_year=$("#cbo_year").val();
	    var page_link='requires/floor_wise_daily_rmg_production_controller.php?action=order_wise_search&buyer='+buyer+'&job_id='+job_id+'&job_no='+job_no+'&cbo_year='+cbo_year;
		//alert(page_link);return; 
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=580px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]; 
				var prodID=this.contentDoc.getElementById("txt_selected_id").value;
				//alert(prodID); // product ID
				var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
				$("#txt_order_no").val(prodDescription);
				$("#hidden_order_id").val(prodID); 
			}
	} 
	
 	function open_int_ref_popup()
	{
		var job_no=$("#txt_job_no").val();
		var job_id=$("#hidden_job_id").val();
		var buyer=$("#cbo_buyer_name").val();
		var cbo_year=$("#cbo_year").val();
	    var page_link='requires/floor_wise_daily_rmg_production_controller.php?action=int_ref_wise_search&buyer='+buyer+'&job_id='+job_id+'&job_no='+job_no+'&cbo_year='+cbo_year;
		//alert(page_link);return; 
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=580px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]; 
				var prodID=this.contentDoc.getElementById("txt_selected_id").value;
				//alert(prodID); // product ID
				var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
				$("#txt_int_ref").val(prodDescription);
				$("#hidden_order_id").val(prodID); 
			}
	}
	 
	function open_job_no()
	{
		var buyer=$("#cbo_buyer_name").val();
		var cbo_year=$("#cbo_year").val();
	    var page_link='requires/floor_wise_daily_rmg_production_controller.php?action=job_popup&buyer='+buyer+'&cbo_year='+cbo_year;
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var job_data=this.contentDoc.getElementById("selected_id").value;
			//alert(job_data); // Jov ID
			var job_data=job_data.split("_");
			var job_hidden_id=job_data[0];
			var job_no=job_data[1];
			//var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#txt_job_no").val(job_no);
			$("#hidden_job_id").val(job_hidden_id); 
			//alert($("#hidden_job_id").val())
		}
	}
	 
	function fn_generate_report(type)
	{
		var job_no = $("#txt_job_no").val();
		var order_no = $("#txt_order_no").val();
		
		if( form_validation('cbo_work_company_name*txt_production_date','Working Company Name*Production Date')==false )
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();

		var data="action=generate_report"+get_submitted_data_string('cbo_work_company_name*cbo_floor_name*cbo_location_name*cbo_group_name*cbo_buyer_name*cbo_year*txt_job_no*hidden_job_id*txt_order_no*hidden_order_id*txt_int_ref*cbo_shipping_status*txt_production_date',"../../")+'&type='+type+'&report_title='+report_title;

		// var data="action=report_generate"+"&type="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_location*cbo_floor*cbo_job_year*txt_job_no*hidden_job_id*txt_production_date',"../../");

		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/floor_wise_daily_rmg_production_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[2]+'" style="text-decoration:none"><input type="button" value="Excel Preview (Summary)" name="excel" id="excel" class="formbutton" style="width:165px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview (Summary)" name="Print" class="formbutton" style="width:165px"/>&nbsp;&nbsp;<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(2)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window(type)
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		$(".flt").css("display","none");
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		if(type==1)
		{
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="all" /><title></title></head><body>'+document.getElementById('summary_part').innerHTML+'</body</html>');
		}
		else
		{
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="all" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		}
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
		$(".flt").css("display","block");
	}
		
	function openmypage_ex_fac_total(arg,action)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/floor_wise_daily_rmg_production_controller.php?data='+arg+'&action='+action, 'Ex-factory Popup', 'width=450px,height=250px,center=1,resize=0,scrolling=0','../');
	}
	
	function openmypage_fab_popup(po_id,color,type,action)
	{
		var title="";
		switch(type) {
			case 1 :
				title = "Fabric Receive";
				break;
			case 2 :
				title = "Fabric Issue to Cutting";
				break;
		}
		var data=po_id+'_'+color+'_'+type+'_'+title;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/floor_wise_daily_rmg_production_controller.php?action='+action+'&data='+data, title, 'width=680px,height=250px,center=1,resize=0,scrolling=0','../');
	}

	function reset_form()
	{
		$("#hidden_style_id").val("");
		$("#hidden_order_id").val("");
		$("#hidden_job_id").val("");
		
	}

	function openmypage_production_popup(po,item,color,type,day,action,title,popup_width,popup_height) 	 		 	 
	{

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/floor_wise_daily_rmg_production_controller.php?po='+po+'&action='+action+'&item='+item+'&color='+color+'&type='+type+'&day='+day, title, 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');

	}

	function getCompanyId() 
	{	 
	    var company_id = document.getElementById('cbo_work_company_name').value;

	    if(company_id !='') {
	      var data="action=load_drop_down_location&choosenCompany="+company_id;
	      http.open("POST","requires/floor_wise_daily_rmg_production_controller.php",true);
	      http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	      http.send(data); 
	      http.onreadystatechange = function(){
	          if(http.readyState == 4) 
	          {
	              var response = trim(http.responseText);
	              $('#location_td').html(response);
	              set_multiselect('cbo_location_name','0','0','','0');
				  setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location_name,'0');getLocationId();") ,3000)];
	          }			 
	      };
	    }         
	}

	function getLocationId() 
	{	 
	    var location_id = document.getElementById('cbo_location_name').value;

	    if(location_id !='') {
	      var data="action=load_drop_down_floor&choosenLocation="+location_id;
	      http.open("POST","requires/floor_wise_daily_rmg_production_controller.php",true);
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
	
	function openmypage_image(page_link,title)
	{
		// alert(page_link);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=350px,height=300px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
		}
	}
</script>

</head>
 
<body onLoad="set_hotkey();">
  <div style="width:100%;" align="center"> 
   <? echo load_freeze_divs ("../../",'');  ?>
    <h3 style="width:1320px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
       <div style="width:100%;" align="center" id="content_search_panel">
    <form id="dateWiseProductionReport_1">    
      <fieldset style="width:1320px;">
            <table class="rpt_table" width="1300" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
               <thead>                    
                       <tr>
                        <th class="must_entry_caption" width="150" >Working Company</th>
                        <th class="" width="100" >Location</th>
                        <th class="" width="100" >Floor</th>
                        <th width="100" >Floor Group</th>
                        <th width="100" >Buyer</th>
                        <th width="80">Job Year</th>
                        <th width="80">Job No</th>
                        <th width="80">Order No </th>
                        <th width="80">Int. Ref. </th>
                        <th width="100">Shiping Status </th>
                        <th width="80" class="must_entry_caption">Production Date </th>
                        <th colspan="2"><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form()"/></th>
                    </tr>   
              </thead>
                <tbody>
                <tr class="general">
                    <td align="center" id="td_company"> 
                        <?
                            echo create_drop_down( "cbo_work_company_name", 150, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 0, "Select Company", $selected, "" );
                        ?>
                    </td>

                    <td align="center" id="location_td"> 
                        <?
                            echo create_drop_down( "cbo_location_name", 100, $blank_array,"","", "Select location", "", "" );
                            // echo create_drop_down( "cbo_location_name", 200, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0  group by id,location_name  order by location_name","id,location_name", 0, "-- Select location --", $selected, "" );
                        ?>
                    </td>

                    <td align="center" id="floor_td"> 
                        <?
                        	echo create_drop_down( "cbo_floor_name", 100, $blank_array,"","", "Select floor", "", "" );
                            // echo create_drop_down( "cbo_floor_name", 200, "SELECT id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 group by id,floor_name order by floor_name","id,floor_name", 0, "-- Select Floor --", $selected, "" );
                        ?>
                    </td>
                    <td align="center">
                        <? 
                        echo create_drop_down( "cbo_group_name", 100, "SELECT a.group_name from lib_prod_floor a where a.status_active=1 and a.is_deleted=0 and a.group_name is not null group by a.group_name  order by a.group_name","group_name,group_name", 1, "Select Group", $selected, "",0,"" );
                        ?>
                    </td>
                    <td align="center">
                        <? 
                        // echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "Select Buyer", 0, "",0 );
                        echo create_drop_down( "cbo_buyer_name", 100, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by  buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
                        ?>
                    </td>
                    <td>
                        <?
						
						echo create_drop_down( "cbo_year", 80, $year,"", 1, "All Year", 0, "",0 );
						?>
                    </td>
                    <td>
                       <input type="text" id="txt_job_no"  name="txt_job_no"  style="width:80px" class="text_boxes" onDblClick="open_job_no()" placeholder="Browse" readonly />
                       <input type="hidden" id="hidden_job_id"  name="hidden_job_id" />
                    </td>
                    <td>
                     <input type="text" id="txt_order_no"  name="txt_order_no"  style="width:80px" class="text_boxes" onDblClick="open_order_no()" placeholder="Browse" readonly />
                     <input type="hidden" id="hidden_order_id"  name="hidden_order_id" />
                    </td>
                    <td>
                     <input type="text" id="txt_int_ref"  name="txt_int_ref"  style="width:80px" class="text_boxes" onDblClick="open_int_ref_popup()" placeholder="Browse" readonly />
                     <!-- <input type="hidden" id="hidden_order_id"  name="hidden_order_id" /> -->
                    </td>
                    <td>
                        <?
                        $shipment_status = array(1 => "Full Pending", 2 => "Partial Shipment", 3 => "Full Shipment/Closed");
							echo create_drop_down( "cbo_shipping_status", 150, $shipment_status,"", 0, "-- Shipping Status --", 0, "",0 );
						?>
                    </td>
                    <td><input type="text" name="txt_production_date" id="txt_production_date" class="datepicker" style="width:80px"  placeholder="To Date" value="<? echo date("d-m-Y"); ?>"  ></td>
                    <td>
                        <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_generate_report(1)" />
					</td>
					<td>
                        <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show2" onClick="fn_generate_report(2)" />
                    </td>
                    
                </tr>
                </tbody>
            </table>
      </fieldset>
    
 </form> 
 </div>  
    <div style="padding: 10px;" id="report_container" ></div>
    <div id="report_container2"></div>  
 </div>
</body>
<script> 
	set_multiselect('cbo_work_company_name','0','0','','0'); 
	set_multiselect('cbo_location_name','0','0','','0'); 
	set_multiselect('cbo_floor_name','0','0','','0'); 
	set_multiselect('cbo_shipping_status','0','0','','0'); 

	setTimeout[($("#td_company a").attr("onclick","disappear_list(cbo_work_company_name,'0');getCompanyId();") ,3000)];
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

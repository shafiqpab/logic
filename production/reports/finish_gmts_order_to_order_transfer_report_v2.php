<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Finish Garments Order to Order Transfer Report.
Functionality	:	
JS Functions	:
Created by		: Kamrul Hasan
Creation date 	: 	14-02-2023
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
echo load_html_head_contents("Finish Garments Order to Order Transfer Report V2", "../../", 1, 1,$unicode,1,1);
?>	

<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	var tableFilters = 
	{} 
	
 	function open_order_no()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		
		var buyer=$("#cbo_buyer_name").val();
		var cbo_year=$("#cbo_year").val();
		var copmany=$("#cbo_company_name").val();
	    var page_link='requires/finish_gmts_order_to_order_transfer_report_v2_controller.php?action=order_popup&buyer='+buyer+'&cbo_year='+cbo_year+'&copmany='+copmany;
		//alert(page_link);return; 
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]; 
				var order_data=this.contentDoc.getElementById("selected_id").value;
				//alert(order_data); // Jov ID
				var order_data=order_data.split("_");
				var hidden_order_id=order_data[0];
				var order_no=order_data[1];
				
				$("#txt_order_no").val(order_no);
				$("#hidden_order_id").val(hidden_order_id); 
				//alert($("#hidden_order_id").val())
				
			}
	} 
	
 	function open_int_ref_popup()
	{
		var job_no=$("#txt_job_no").val();
		var job_id=$("#hidden_job_id").val();
		var buyer=$("#cbo_buyer_name").val();
		var cbo_year=$("#cbo_year").val();
	    var page_link='requires/finish_gmts_order_to_order_transfer_report_v2_controller.php?action=int_ref_wise_search&buyer='+buyer+'&job_id='+job_id+'&job_no='+job_no+'&cbo_year='+cbo_year;
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
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}

		var buyer=$("#cbo_buyer_name").val();
		var cbo_year=$("#cbo_year").val();
		var copmany=$("#cbo_company_name").val();
	    var page_link='requires/finish_gmts_order_to_order_transfer_report_v2_controller.php?action=job_popup&buyer='+buyer+'&cbo_year='+cbo_year+'&copmany='+copmany;
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
	function openmypage_reqNo()
{
	var cbo_company_id = $('#cbo_company_name').val();
	//var transfer_criteria = $('#cbo_transfer_criteria').val();
	if (form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	
	var title = 'Sample Requision Info';	
	var page_link = 'requires/finish_gmts_order_to_order_transfer_report_v2_controller.php?cbo_company_id='+cbo_company_id+'&action=requisition_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=990px,height=370px,center=1,resize=1,scrolling=0','');
	emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var requisition_data=this.contentDoc.getElementById("selected_id").value;
			
			var requisition_data=requisition_data.split("_");
			var hidden_requisition_id=requisition_data[0];
			var requisition_no=requisition_data[1];
			
			$("#txt_requisition_no").val(requisition_no);
			$("#hidden_requisition_id").val(hidden_requisition_id); 
			//alert($("#hidden_job_id").val())
		}
}
	
	
	function fn_generate_report(type)
	{
		if( form_validation('cbo_company_name','Working Company Name')==false )
			{
				return;
			}
			//alert(job_no);
		let job_no = $("#txt_job_no").val();
		let order_no = $("#txt_order_no").val();
		let requisition_no = $("#txt_requisition_no").val();
		//let transfer_criteria = $('#cbo_transfer_criteria').val();
		let cbo_date_category = $('#cbo_date_category').val();
		if(job_no=="" && order_no==""&& requisition_no==""  && cbo_date_category=="0")
		{		
			alert('Please Enter Job No or Order No  or Requisition No  or Date Category ') ;
			die;
			
		}
	  
		
		var report_title=$( "div.form_caption" ).html();
		//console.log('sssssssssssss');return;
		var data="action=generate_report"+get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_year*txt_job_no*hidden_job_id*txt_order_no*hidden_order_id*txt_requisition_no*hidden_requisition_id*cbo_transfer_criteria*cbo_date_category*txt_date_from*txt_date_to',"../../")+'&type='+type+'&report_title='+report_title;

       // console.log(`data = ${data}`);
		//return;//
		

		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/finish_gmts_order_to_order_transfer_report_v2_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		$(".flt").css("display","none");
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="all" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
		$(".flt").css("display","block");
	}
		
	function openmypage_ex_fac_total(arg,action)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/finish_gmts_order_to_order_transfer_report_v2_controller.php?data='+arg+'&action='+action, 'Ex-factory Popup', 'width=450px,height=250px,center=1,resize=0,scrolling=0','../');
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
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/finish_gmts_order_to_order_transfer_report_v2_controller.php?action='+action+'&data='+data, title, 'width=680px,height=250px,center=1,resize=0,scrolling=0','../');
	}

	function reset_form()
	{
		$("#hidden_style_id").val("");
		$("#hidden_order_id").val("");
		$("#hidden_job_id").val("");
		
	}

	function openmypage_production_popup(po,item,color,type,day,action,title,popup_width,popup_height) 	 		 	 
	{

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/finish_gmts_order_to_order_transfer_report_v2_controller.php?po='+po+'&action='+action+'&item='+item+'&color='+color+'&type='+type+'&day='+day, title, 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');

	}

	function getCompanyId() 
	{	 
	    var company_id = document.getElementById('cbo_company_name').value;

	    if(company_id !='') {
	      var data="action=load_drop_down_location&choosenCompany="+company_id;
	      http.open("POST","requires/finish_gmts_order_to_order_transfer_report_v2_controller.php",true);
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
	      http.open("POST","requires/finish_gmts_order_to_order_transfer_report_v2_controller.php",true);
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

	function change_title(id)
	{
		// alert(id);
		var text = '';
		switch(id)
		{
			case '1' :
				text = "Transfer Date";
				break;
			case '2' :
				text = "Shipment Date";
				break;
			case '3' :
				text = "Insert Date";
				break;
		}
		// alert(text);
		$("#date_category").text(text);
	}
</script>

</head>
 
<body onLoad="set_hotkey();">
  <div style="width:100%;" align="center"> 
   <? echo load_freeze_divs ("../../",'');  ?>
    <h3 style="width:1100px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
       <div style="width:100%;" align="center" id="content_search_panel">
    <form id="dateWiseProductionReport_1">    
      <fieldset style="width:1080px;">
            <table class="rpt_table" width="920" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
               <thead>                    
                       <tr>
                        <th class="must_entry_caption" width="150" >Company</th>
                        <th class="" width="100" >Location</th>
                        <th width="100">Buyer</th>
                        <th width="80">Job Year</th>
                        <th width="80">Job No</th>
                        <th width="80">Order No </th>
						<th width="80">Requisition No</th>
                        <th width="100">Transfer Criteria</th>
						<th width="100">Date Category</th>
                        <th width="80" id="date_category" class="must_entry_caption" colspan="2">Transfer Date </th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form()"/></th>
                    </tr>   
              </thead>
                <tbody>
                <tr class="general">
                    <td align="center" id="td_company"> 
                        <?
                            echo create_drop_down( "cbo_company_name", 150, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/finish_gmts_order_to_order_transfer_report_v2_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );load_drop_down( 'requires/finish_gmts_order_to_order_transfer_report_v2_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                        ?>
                    </td>

                    <td align="center" id="location_td"> 
                        <?
                            echo create_drop_down( "cbo_location_name", 120, $blank_array,"",1, "-- Select location --", "", "" );
                        ?>
                    </td>
                    <td align="center" id="buyer_td_id">
                        <? 
                        echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", 0, "",0 );
                        ?>
                    </td>
                    <td>
                        <?
						
						echo create_drop_down( "cbo_year", 80, $year,"", 1, "-- All Year --", date("Y"), "",0 );
						?>
                    </td>
                    <td>
                       <input type="text" id="txt_job_no"  name="txt_job_no"  style="width:80px" class="text_boxes" onDblClick="open_job_no()" placeholder="Browse" readonly value="" />
                       <input type="hidden" id="hidden_job_id"  name="hidden_job_id"  />
                    </td>
                    <td>
                     <input type="text" id="txt_order_no"  name="txt_order_no"  style="width:80px" class="text_boxes" onDblClick="open_order_no()" placeholder="Browse" value="" readonly />
                     <input type="hidden" id="hidden_order_id"  name="hidden_order_id"  />
                    </td>
					
					<td> 
					<input type="text" name="txt_requisition_no" id="txt_requisition_no" class="text_boxes" style="width: 80px;" placeholder="Browse" value="" readonly onDblClick="openmypage_reqNo();" >
					<input type="hidden" id="hidden_requisition_id"  name="hidden_requisition_id"  />
				 </td>
                    <td>
                        <?
                        
							echo create_drop_down( "cbo_transfer_criteria", 100, $fin_gmts_transfer_criteria_array,"", 1, "-- All --", 0, "",0 );
						?>
                    </td>
					  <td>
                        <?
                        $date_cat = array(1 => "Transfer Date", 2 => "Shipment Date", 3 => "Insert Date");
							echo create_drop_down( "cbo_date_category", 150, $date_cat,"", 1, "-- Select --", 0, "change_title(this.value)",0 );
						?>
                    </td>
                    <td><input name="txt_date_from" value="" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" readonly>
                    </td>
                    <td><input name="txt_date_to" value="" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date"  readonly>
                    </td>
                    <td>
                        <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_generate_report(1)" />
                    </td>
                    
                </tr>
                </tbody>
                <tr>
                    <td colspan="10" align="center"><?=load_month_buttons(1); ?></td>
                </tr>
            </table>
      </fieldset>
    
 </form> 
 </div>  
    <div style="padding: 10px;" id="report_container" ></div>
    <div id="report_container2"></div>  
 </div>
</body>
<script> 
	/*set_multiselect('cbo_company_name','0','0','','0'); 
	set_multiselect('cbo_location_name','0','0','','0'); 
	set_multiselect('cbo_floor_name','0','0','','0'); 
	set_multiselect('cbo_shipping_status','0','0','','0'); 

	setTimeout[($("#td_company a").attr("onclick","disappear_list(cbo_company_name,'0');getCompanyId();") ,3000)];*/
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

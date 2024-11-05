<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Finish Fabric Delivery Status Report.
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	04-07-2017
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
echo load_html_head_contents("Finish Fabric Delivery Status Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
			
	function fnc_report_generated()
	{
		var company = document.getElementById('cbo_company_id').value;
        var workingCompany = document.getElementById('cbo_deli_company_id').value;
		var txt_date_from = document.getElementById('txt_date_from').value;
        var txt_date_to = document.getElementById('txt_date_to').value;

        var txt_job_no = document.getElementById('txt_job_no').value;
		var txt_internal_ref = document.getElementById('txt_internal_ref').value;
        var txt_style_ref = document.getElementById('txt_style_ref').value;
        var txt_order_no = document.getElementById('txt_order_no').value;
        var txt_batch_no = document.getElementById('txt_batch_no').value;

        if (txt_job_no=="" && txt_style_ref=="" && txt_order_no=="" && txt_batch_no=="" && txt_internal_ref=="") 
        {
        	if (form_validation('cbo_company_id*txt_date_from*txt_date_to','Comapny Name*Date From*Date To')==false)
			{
				return;
			}
        }
        else
        {    
        	if (form_validation('cbo_company_id','Comapny Name')==false)
			{
				return;
			}
        }

		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_deli_company_id*cbo_deli_location_id*txt_buyer_id*txt_party_id*cbo_prod_category*txt_date_from*txt_date_to*txt_job_no*txt_internal_ref*txt_style_ref*txt_order_no*txt_batch_no*cbo_type_id',"../../")+'&report_title='+report_title;
		//alert(data);
		freeze_window(3);
		http.open("POST","requires/finish_fabric_delivery_status_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**"); 
			$('#report_container2').html(reponse[0]);
			setFilterGrid('list_views',-1);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body_1').style.overflow="auto";
		document.getElementById('scroll_body_2').style.overflow="auto";
		document.getElementById('scroll_body_3').style.overflow="auto";
		document.getElementById('scroll_body_1').style.maxHeight="none"; 
		document.getElementById('scroll_body_2').style.maxHeight="none"; 
		document.getElementById('scroll_body_3').style.maxHeight="none"; 
		$(".flt").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body_1').style.overflowY="scroll"; 
		document.getElementById('scroll_body_2').style.overflowY="scroll"; 
		document.getElementById('scroll_body_3').style.overflowY="scroll"; 
		document.getElementById('scroll_body_1').style.maxHeight="200px";
		document.getElementById('scroll_body_2').style.maxHeight="200px";
		document.getElementById('scroll_body_3').style.maxHeight="400px";
		$(".flt").show();
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
	
	function fnc_buyer_party_popup(type)
	{
		if (form_validation('cbo_company_id','Comapny Name')==false)
		{
			return;
		}
		var data=$('#cbo_company_id').val()+'__'+type;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/finish_fabric_delivery_status_report_controller.php?data='+data+'&action=buyer_party_popup','Buyer Party Popup', 'width=420px,height=320px,center=1,resize=1,scrolling=0','../')
		
		emailwindow.onclose=function()
		{
			var hide_party_id=this.contentDoc.getElementById("hide_party_id").value;//Access form field with id="emailfield"
			var hide_party_name=this.contentDoc.getElementById("hide_party_name").value;
			if (hide_party_name!="")
			{
				if(type==1)
				{
					$('#txt_buyer_id').val(hide_party_id);
					$('#txt_buyer_name').val(hide_party_name);
				}
				else if (type==2)
				{
					$('#txt_party_id').val(hide_party_id);
					$('#txt_party_name').val(hide_party_name);
				}
			}
		}
	}


	function openmypage_job()
	{ 
		
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		
		var company_name=document.getElementById('cbo_company_id').value;
		var page_link="requires/finish_fabric_delivery_status_report_controller.php?action=job_no_popup&company_id="+company_name;
		var title="Job Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			//var job=theemail.split("_");
			document.getElementById('txt_job_no').value=theemail;
			release_freezing();
		}
	}	
	
	function openmypage_style()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_id').value;
	
		var page_link="requires/finish_fabric_delivery_status_report_controller.php?action=style_no_popup&company_id="+company_name;
		var title="Style Ref.";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			//var job=theemail.split("_");
			document.getElementById('txt_style_ref').value=theemail;
			release_freezing();
		}
	}

	function openmypage_order()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_id').value;
	
		var page_link="requires/finish_fabric_delivery_status_report_controller.php?action=order_no_popup&company_id="+company_name;
		var title="Order Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			var job=theemail.split("_");
			document.getElementById('txt_order_id').value=job[0];
			document.getElementById('txt_order_no').value=job[1];
			release_freezing();
		}
	}

	function openmypage_batch_num()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_id').value;
		var hid_order_id=document.getElementById('txt_order_id').value;
		var page_link="requires/finish_fabric_delivery_status_report_controller.php?action=batch_no_popup&company_name="+company_name+"&hid_order_id="+hid_order_id;
		var title="Batch Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=400px,center=1,resize=0,scrolling=0','../')

		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			var batch=theemail.split("_");
			document.getElementById('hid_batch_id').value=batch[0];
			document.getElementById('txt_batch_no').value=batch[1];
			document.getElementById('txt_ext_no').value=batch[2];
			release_freezing();
		}
	}

</script>
</head>
<body onLoad="set_hotkey();">
<form id="greyStock_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1430px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1430px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="140" class="must_entry_caption">Company</th>
                    <th width="140" >Delivery Company</th>
                    <th width="140" >Delivery Location</th>
					<th width="100">Type</th>
                    <th width="130">Self Order Buyer</th>
                    <th width="130">Subcontract Party</th>
					<th width="60">Job No</th>
					<th width="80">Internal Ref.</th>
                    <th width="80">Style Ref.</th>
                    <th width="80">Order No</th>
					<th width="80">Batch No</th>
                    <th width="100">Product Category</th>
                    <th width="170" class="must_entry_caption" colspan="2">Transaction Date</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                </thead>
                <tbody>
                    <tr class="general" >
                        <td><? echo create_drop_down( "cbo_company_id", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-Select Company-", $selected, "" ); ?></td>
						<td>
							<?
								echo create_drop_down( "cbo_deli_company_id", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected , "load_drop_down( 'requires/finish_fabric_delivery_status_report_controller', this.value, 'load_drop_down_location_deli', 'deli_location_td' );" );
							?>
						</td>
						<td id="deli_location_td">
							<?
								echo create_drop_down( "cbo_deli_location_id",140,$blank_array,"", 1, "--Select Location--", $selected, "","","","","","",2);
							?>
						</td>
						<? $type_arr=array("1"=>"Self Order","2"=>"Subcontract Order","3"=>"Sample Order");?>
						<td><? echo create_drop_down( "cbo_type_id", 100, $type_arr,0, 1, "-Select type-", $selected, "" ); ?></td>
                        <td><input class="text_boxes" type="text" style="width:120px" name="txt_buyer_name" id="txt_buyer_name" placeholder="Browse Buyer" onDblClick="fnc_buyer_party_popup(1);" readonly /><input class="text_boxes" type="hidden" style="width:70px" name="txt_buyer_id" id="txt_buyer_id" readonly /></td>
                        <td><input class="text_boxes" type="text" style="width:120px" name="txt_party_name" id="txt_party_name" placeholder="Browse Party" onDblClick="fnc_buyer_party_popup(2);" readonly /><input class="text_boxes" type="hidden" style="width:70px" name="txt_party_id" id="txt_party_id" readonly /></td>
                        <td>
                            <input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:55px" placeholder="Write" >
                        </td>
						<td>
                            <input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:75px" placeholder="Write">
                        </td>
                        <td>
                            <input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:75px" placeholder="Write">
                        </td>
                        <td>
                            <input name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:75px"  placeholder="Write">
                            <input type="hidden" name="txt_order_id" id="txt_order_id" class="text_boxes" style="width:70px">
                        </td>
						<td>
                                <input type="text"  name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:80px;" placeholder="Write" >
                                <input type="hidden" name="hid_batch_id" id="hid_batch_id" style="width:50px;">
                        </td>
					    <td><? echo create_drop_down( "cbo_prod_category", 100, $product_category,"", 1, "-Prod Category-", 0, "","","","","","",""); ?></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" ></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" ></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fnc_report_generated();" /></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7" align="center">
							<? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </tfoot>
           </table> 
           <br />
        </fieldset>
    </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

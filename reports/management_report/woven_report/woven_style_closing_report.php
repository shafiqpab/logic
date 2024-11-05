<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create For Woven Style Report
Functionality	:
JS Functions	:
Created by		:	Aziz 
Creation date 	: 	17-10-2020
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------

echo load_html_head_contents("Woven Style Report","../../../", 1, 1, $unicode,1,1);
?>
<script>
    var permission='<? echo $permission; ?>';
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";

    function fn_report_generated(type)
    {
       // var job_no=document.getElementById('txt_job_no').value;
      //  var order_no=document.getElementById('txt_order_no').value;
        var txt_style_ref=document.getElementById('txt_style_ref').value;
        var txt_style_id=document.getElementById('txt_style_id').value;
        var txt_ref_no=document.getElementById('txt_ref_no').value;
		var hide_order_id=document.getElementById('hide_order_id').value;
		var txt_job_no=document.getElementById('txt_job_no').value;
		var txt_job_id=document.getElementById('txt_job_id').value;
		var style_owner=document.getElementById('cbo_style_owner').value;
		var company_name=document.getElementById('cbo_company_name').value;
       var cbo_brand_id=document.getElementById('cbo_brand_id').value;
	   var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
	   var cbo_season_id=document.getElementById('cbo_season_id').value;
	   var cbo_season_year=document.getElementById('cbo_season_year').value;
	  if(cbo_brand_id==0 && cbo_season_id==0 && cbo_season_year==0) 
	  {
		  if(txt_ref_no=="" && txt_job_no=="" && txt_style_ref=="" )
			{
				alert("Please Write Job No/Master Style/March Style");
				return;
			}
	  }
	  else  if(cbo_buyer_name>0 && cbo_brand_id==0 && cbo_season_id==0 && cbo_season_year==0) 
	  {
		  if(txt_ref_no=="" && txt_job_no=="" && txt_style_ref=="" )
			{
				alert("Please Write Job No/Master Style/March Style");
				return;
			}
	  }
		

		if(company_name==0)
		{
			if(style_owner==0)
			{
				if(form_validation('cbo_company_name','Company')==false)
					{
						return;
					}
			}
		}
		if(style_owner==0)
		{
			if(company_name==0)
			{
				if(form_validation('cbo_style_owner','Style Owner')==false)
				{
					return;
				}
			}
		}

       

        var data="action=report_generate&reporttype="+type+get_submitted_data_string('cbo_company_name*cbo_style_owner*cbo_buyer_name*txt_job_no*txt_job_id*hide_order_id*txt_ref_no*cbo_year*cbo_shipping_status*txt_style_id*txt_style_ref*cbo_brand_id*cbo_season_year*cbo_season_id',"../../../");
        freeze_window(3);
       
        http.open("POST","requires/woven_style_closing_report_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_reponse;
    }
	 function fn_report_generated_reponse()
    {
        if(http.readyState == 4)
        {
            var reponse=trim(http.responseText).split("****");


            if(reponse[2]==1 || reponse[2]==5 || reponse[2]==4)
            {
                $('#report_container2').html(reponse[0]);
                document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            }
            else
            {
                $('#report_container2').html(reponse[0]);
                document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            }

             setFilterGrid("table_body",-1);
            release_freezing();
            show_msg('3');
        }
    }

    function new_window()
    {
        document.getElementById('scroll_body').style.overflow="auto";
        document.getElementById('scroll_body').style.maxHeight="none";

        $("#table_body tr:first").hide();

        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
            '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close();

        document.getElementById('scroll_body').style.overflow="scroll";
        document.getElementById('scroll_body').style.maxHeight="350px";

        $("#table_body tr:first").show();
    }

 function openmypage_style()
	{
		 var style_owner = $("#cbo_style_owner").val();
		var company_name = $("#cbo_company_name").val();
		  if(company_name==0)
		{
			if(style_owner==0)
			{
				if(form_validation('cbo_company_name','Company')==false)
					{
						return;
					}
			}
		}
		if(style_owner==0)
		{
			if(company_name==0)
			{
				if(form_validation('cbo_style_owner','Style Owner')==false)
				{
					return;
				}
			}
		}
		//var company = $("#cbo_company_name").val();
		//var cbo_style_owner = $("#cbo_style_owner").val();
		var buyer = $("#cbo_buyer_name").val();
		var cbo_year = $("#cbo_year").val();
		var txt_job_no = $("#txt_job_no").val();
		var txt_job_id = $("#txt_job_id").val();
		//var txt_style_ref = $("#txt_style_ref").val();
		var page_link='requires/woven_style_closing_report_controller.php?action=style_ref_popup&companyID='+company_name+'&style_owner='+style_owner+'&buyer_name='+buyer+'&txt_job_no='+txt_job_no+'&txt_job_id='+txt_job_id;+'&cbo_year_id='+cbo_year
		var title="Search Job Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var job_no=this.contentDoc.getElementById("txt_selected").value; // product Description
		//	var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			$("#txt_job_no").val(job_no);
			$("#txt_job_id").val(job_id); 
			//$("#txt_style_ref_no").val(style_no); 
		}
	}

    function openmypage_order(type)
    {
         
        var style_owner = $("#cbo_style_owner").val();
		var company_name = $("#cbo_company_name").val();
		  if(company_name==0)
		{
			if(style_owner==0)
			{
				if(form_validation('cbo_company_name','Company')==false)
					{
						return;
					}
			}
		}
		if(style_owner==0)
		{
			if(company_name==0)
			{
				if(form_validation('cbo_style_owner','Style Owner')==false)
				{
					return;
				}
			}
		}
		
      //  var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value;
			var companyID = $("#cbo_company_name").val();
			var buyerID = $("#cbo_buyer_name").val();
			var txt_job_id = $("#txt_job_id").val();
			if(type==1)
			{
				var title='Master Style';
			}
			else
			{
				var title='Merch Style';
			}
		var page_link='requires/woven_style_closing_report_controller.php?action=order_no_search_popup&companyID='+companyID+'&style_owner='+style_owner+'&txt_job_id='+txt_job_id+'&type='+type;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=880px,height=420px,center=1,resize=1,scrolling=0','../../');
        emailwindow.onclose=function()
        {
            var theemail=this.contentDoc.getElementById("hide_order_id"); 
            var theemailv=this.contentDoc.getElementById("hide_ref_no");
            var response=theemail.value.split('_');
            if (theemail.value!="")
            {
               // freeze_window(5);
			  // alert(type);
				if(type==1)
				{
                document.getElementById("hide_order_id").value=theemail.value;
                document.getElementById("txt_ref_no").value=theemailv.value;//
				}
				else
				{
				document.getElementById("txt_style_id").value=theemail.value;
               	 document.getElementById("txt_style_ref").value=theemailv.value;//
				}
               // release_freezing();
            }
        }
    }

    function generate_pre_cost_report(po_id,job_no,company_id,buyer_id,style_ref,action)
    {
        //var budget_version = $("#cbo_budget_version").val();
        var popup_width='900px';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_style_closing_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
    }

    function generate_precost_fab_purchase_detail(po_id,job_no,company_id,buyer_id,fabric_source,action)
    {
        var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'woven_order/requires/woven_style_closing_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&fabric_source='+fabric_source+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
    }

    function generate_pre_cost_knit_popup(po_id,job_no,company_id,buyer_id,style_ref,action)
    {
        var popup_width='700px';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_style_closing_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style_ref='+style_ref+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
    }

    function generate_precost_fab_dyeing_detail(po_id,job_no,company_id,buyer_id,fab_source,action)
    {
        var popup_width='750px';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_style_closing_report_controller.php?company_id='+company_id+'&po_id='+po_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&fab_source='+fab_source+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');

    }

   

//generate_style_report_with_graph sew_defect
 function generate_sew_defect_popup2(po_ids,action,type)
    {
       var style_owner = $("#cbo_style_owner").val();
	    var company_id = $("#cbo_company_name").val();
		var buyer_id = $("#cbo_buyer_name").val();
	    var popup_width='750px';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_style_closing_report_controller.php?company_id='+company_id+'&po_ids='+po_ids+'&buyer_id='+buyer_id+'&style_owner='+style_owner+'&type='+type+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
    }
	
  
    function generate_popup(po_ids,action,type)
    {
       var style_owner = $("#cbo_style_owner").val();
	    var company_id = $("#cbo_company_name").val();
		var buyer_id = $("#cbo_buyer_name").val();
	    var popup_width='750px';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_style_closing_report_controller.php?company_id='+company_id+'&po_ids='+po_ids+'&buyer_id='+buyer_id+'&style_owner='+style_owner+'&type='+type+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
    }
	function generate_line_popup(po_ids,job_no,style_ref,action,type)
    {
       var style_owner = $("#cbo_style_owner").val();
	    var company_id = $("#cbo_company_name").val();
		var buyer_id = $("#cbo_buyer_name").val();
	    var popup_width='3280px';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_style_closing_report_controller.php?company_id='+company_id+'&style_owner='+style_owner+'&style_ref='+style_ref+'&job_no='+job_no+'&po_ids='+po_ids+'&buyer_id='+buyer_id+'&type='+type+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
    }
	function generate_line_defect_popup(po_ids,job_no,style_ref,action,type)
    {
       var style_owner = $("#cbo_style_owner").val();
	    var company_id = $("#cbo_company_name").val();
		var buyer_id = $("#cbo_buyer_name").val();
	    var popup_width='3080px';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_style_closing_report_controller.php?company_id='+company_id+'&style_owner='+style_owner+'&style_ref='+style_ref+'&job_no='+job_no+'&po_ids='+po_ids+'&buyer_id='+buyer_id+'&type='+type+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
    }

    

    function country_order_dtls(po_id,country_date,buyer_id,job_no,action)
    {
        if (action=="country_trims_dtls_popup")
        {
            var popup_width='850px';
        }
        else
        {
            var popup_width='750px';
        }
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_style_closing_report_controller.php?po_id='+po_id+'&country_date='+country_date+'&buyer_id='+buyer_id+'&job_no='+job_no+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
    }
    function country_order_dtls_trim(po_id,country_id,buyer_id,job_no,action)
    {

        var popup_width='850px';
        //country_trims_dtls_popup
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_style_closing_report_controller.php?po_id='+po_id+'&country_id='+country_id+'&buyer_id='+buyer_id+'&job_no='+job_no+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
    }

    function new_window1(type)
    {
        var report_div='';
        var scroll_div='';
        if(type==1)
        {
            report_div="yarn_summary";
            //scroll_div='scroll_body';
        }
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
            '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById(report_div).innerHTML+'</body</html>');
        d.close();
    }

    function report_generate_pop(company_id,style_owner,job_no,txt_style_ref_no,buyer_id,po_id,action,type)
    {
       //if(style_owner>0)  company_id=style_owner;//else company_id=company_id;
	   // var data="&action=generate_style_report_with_graph"+
	
	   if(type==1) //cbo_order_status
	   { 
	   		//var job_no="";
		   


		   var cbo_shipment_status=0;var cbo_order_status=0;var cbo_search_type=1;
	    var data="&action="+action+
            '&style_owner='+"'"+style_owner+"'"+
            '&txt_job_no='+"'"+job_no+"'"+
            '&cbo_company_name='+"'"+company_id+"'"+
            '&cbo_buyer_name='+"'"+buyer_id+"'"+
			 '&cbo_shipment_status='+"'"+cbo_shipment_status+"'"+
			  '&cbo_search_type='+"'"+cbo_search_type+"'"+
			  '&cbo_order_status='+"'"+cbo_order_status+"'"+
			'&txt_style_ref='+"'"+txt_style_ref_no+"'"+
		   '&hide_order_id='+"'"+po_id+"'";
	   }
	   if(type==2)
	   {
		     var type_cut=1;var job_no='';var order_no='q';
			 var data="&action=report_generate"+
            '&cbo_working_company_name='+"'"+style_owner+"'"+
            '&txt_job_no='+"'"+job_no+"'"+
            '&cbo_company_name='+"'"+company_id+"'"+
            '&cbo_buyer_name='+"'"+buyer_id+"'"+
			'&txt_style_ref_no='+"'"+txt_style_ref_no+"'"+
		   '&hide_order_id='+"'"+po_id+"'"+
		   '&txt_order_no='+"'"+order_no+"'"+
		   '&type='+"'"+type_cut+"'";
	   }
	    if(type==3)//Master Style
	   {
		   var cbo_budget_version=2;
			 var cbo_year=buyer_id; var cbo_costcontrol_source=1;
			  var g_exchange_rate = style_owner;
			//   alert(g_exchange_rate);
			// var type_cut=1;var job_no='';var order_no='q';
			 var data="&action=report_generate"+
            '&cbo_company_name='+"'"+style_owner+"'"+
            '&txt_job_no='+"'"+job_no+"'"+
            '&cbo_company_name='+"'"+company_id+"'"+
            '&cbo_year='+"'"+cbo_year+"'"+
			'&txt_style_ref_no='+"'"+txt_style_ref_no+"'"+
		   '&cbo_budget_version='+"'"+cbo_budget_version+"'"+
		   '&g_exchange_rate='+"'"+g_exchange_rate+"'"+
		   '&cbo_costcontrol_source='+"'"+cbo_costcontrol_source+"'";
	   }
	 
       // alert(data);
		 if(type==1)
		 {
       	//  http.open("POST","../../../tna/report/requires/tna_report_controller.php",true);
		   http.open("POST","../../../order/woven_gmts/requires/pre_cost_entry_controller_v2.php",true);
		 }
		 if(type==2)
		 {
       	 http.open("POST","../../../prod_planning/reports/requires/cutting_status_report_controller2.php",true);
		 }
		 if(type==3)
		{
			http.open("POST","../../../reports/management_report/woven_report/requires/style_wise_cost_comparison_report_controller2.php",true);
		}
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = function()
        {
            if(http.readyState == 4)
            {
               	var w = window.open();
                var d = w.document.open();
               // d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
                  //  '<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				  d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><body><link rel="stylesheet" href="../../../../css/style_print.css" type="text/css" /><title></title></head>'+http.responseText+'</body</html>');
                d.close();
            }
        }
    }
	
	function report_generate_popup(company_id,style_owner,txt_wo_po_no,action,type)
    {
       if(style_owner>0)  company_id=style_owner;//else company_id=company_id;
	   // var data="&action=generate_style_report_with_graph"+
	  
	 var  cbo_item_category_id=3; txt_pi_no='';
	   if(type==3)
	   {
		     var type_cut=1; 
			 var data="&action=report_generate_woven"+
            '&txt_pi_no='+"'"+txt_pi_no+"'"+
            '&cbo_company_name='+"'"+company_id+"'"+
            '&txt_wo_po_no='+"'"+txt_wo_po_no+"'"+
		   '&cbo_item_category_id='+"'"+cbo_item_category_id+"'"+
		   '&type='+"'"+type_cut+"'";
	   }
       // alert(data); http://localhost/platform-v3.5/commercial/reports/requires/purchase_recap_report3_controller.php
		 
		 if(type==3)
		 {
       	 http.open("POST","../../../commercial/reports/requires/purchase_recap_report3_controller.php",true);
		 }
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = function()
        {
            if(http.readyState == 4)
            {
               	var w = window.open();
                var d = w.document.open();
               // d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
                  //  '<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				  d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><body><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="all" /><title></title></head>'+http.responseText+'</body</html>');
                d.close();
            }
        }
    }
	
	function generate_sew_defect_popup(company_id,po_id,style_ref,action,type)
    {
     //  if(style_owner>0)  company_id=style_owner;//else company_id=company_id;
	   // var data="&action=generate_style_report_with_graph"+
	  
	// var  cbo_item_category_id=3; txt_pi_no='';
	   if(type==1)
	   {
		     var type_cut=1; 
			 var data="&action=sewing_defect_popup"+
            '&po_id='+"'"+po_id+"'"+
            '&cbo_company_name='+"'"+company_id+"'"+
            '&style_ref='+"'"+style_ref+"'"+
		   '&type='+"'"+type+"'";
	   }
	   if(type==2)
	   {
		     var type_cut=1; 
			 var data="&action=fin_defect_popup"+
            '&po_id='+"'"+po_id+"'"+
            '&cbo_company_name='+"'"+company_id+"'"+
            '&style_ref='+"'"+style_ref+"'"+
		   '&type='+"'"+type+"'";
	   }
       // alert(data); http://localhost/platform-v3.5/commercial/reports/requires/purchase_recap_report3_controller.php
		 
		 if(type==1 || type==2)
		 {
       	 http.open("POST","requires/woven_style_closing_report_controller.php",true);
		 }
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = function()
        {
            if(http.readyState == 4)
            {
               	var w = window.open();
                var d = w.document.open();
               // d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
                  //  '<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				  d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><body><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="all" /><title></title></head>'+http.responseText+'</body</html>');
                d.close();
            }
        }
    }
	
    function search_populate(str)
    {
        if(str==1)
        {
            document.getElementById('search_by_th_up').innerHTML="Shipment Date";
            $('#search_by_th_up').css('color','blue');
        }
        else if(str==2)
        {
            document.getElementById('search_by_th_up').innerHTML="PO Received Date";
            $('#search_by_th_up').css('color','blue');
        }
        else if(str==3)
        {
            document.getElementById('search_by_th_up').innerHTML="PO Insert Date";
            $('#search_by_th_up').css('color','blue');
        }
    }

    function openmypage_season()
    {
        if(form_validation('cbo_company_name','Company Name')==false)
        {
            return;
        }
        var companyID = $("#cbo_company_name").val();
        var buyerID = $("#cbo_buyer_name").val();
        var job_no = $("#txt_job_no").val();
        var page_link='requires/woven_style_closing_report_controller.php?action=search_popup&companyID='+companyID+'&buyerID='+buyerID+'&job_no='+job_no;
        var title='Season Search';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=350px,center=1,resize=1,scrolling=0','../../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var hide_season=this.contentDoc.getElementById("hide_season").value;
            var hide_season_id=this.contentDoc.getElementById("hide_season_id").value;

            $('#txt_season').val(hide_season);
            $('#txt_season_id').val(hide_season_id);
        }
    }
    //for print button
    function print_button_setting()
    {
        $('#data_panel').html('');
        get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/woven_style_closing_report_controller' );
    }

    

    function generate_ex_factory_popup(action,job,id,width)
    {
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_style_closing_report_controller.php?action='+action+'&job='+job+'&id='+id, 'Ex-Factory Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
    }
	</script>
	</head>
	<body onLoad="set_hotkey();">
	<form id="budgetReport_1">
	    <div style="width:100%;" align="center">
	        <? echo load_freeze_divs ("../../../",$permission);  ?>
	        <h3 align="left" id="accordion_h1" style="width:1150px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
	        <fieldset style="width:1020px;" id="content_search_panel">
	            <table class="rpt_table" width="1120" cellpadding="1" cellspacing="2" align="center" border="1" rules="all">
	                <thead>
	                <th class="must_entry_caption">Company Name</th>
                    <th class="must_entry_caption">Style Owner</th>
	                <th>Buyer Name</th>
                     <th>Brand</th>
                     <th>Season</th>
                     <th>Season Year</th>
	                <th>Job Year</th>
	                <th>Job No.</th>
	                <th>Master Style </th>
	                <th>Merch Style</th>
	                <th>Ship Status</th>
	                </thead>
	                <tbody>
	                <tr class="general">
	                    <td>
	                        <?
	                        echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected,"load_drop_down( 'requires/woven_style_closing_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
	                        ?>
	                    </td>
                         <td>
	                        <?
	                        echo create_drop_down( "cbo_style_owner", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected,"" );
	                        ?>
	                    </td>
	                    <td id="buyer_td">
	                        <?
	                        echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "--All Buyer--", $selected, "",0,"" );
	                        ?>
	                    </td>
                         <td id="brand_td">
                        <?
                        echo create_drop_down( "cbo_brand_id", 100, $blank_array,"", 1, "-- All Brand --", $selected, "",0,"" );
                        ?>
                    </td>
                       <td id="season_td"><? echo create_drop_down( "cbo_season_id", 100, $blank_array,'', 1, "--Season--",$selected, "" ); ?>    </td> 
                        <td><? echo create_drop_down( "cbo_season_year", 100, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>   
                        
                        
	                    <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
	                    <td>
                        <input style="width:100px;" name="txt_job_no" id="txt_job_no" onDblClick="openmypage_style(1)" class="text_boxes" placeholder="Browse/Write"/>
                        <input type="hidden" name="txt_job_id" id="txt_job_id"/> 
	                    </td>
                        <td>
	                        
                            
                              <input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:80px" placeholder="Browse Or Write" onDblClick="openmypage_order(1);" onChange="$('#hide_order_id').val('');" autocomplete="off">
                                <input type="hidden" name="hide_order_id" id="hide_order_id" readonly/>

	                    </td>
	                   
	                    <td>
	                  <input type="text" id="txt_style_ref" name="txt_style_ref" class="text_boxes" style="width:80px" onDblClick="openmypage_order(2);"
                       placeholder="Wr./Br. Order"  />    <input type="hidden" name="txt_style_id" id="txt_style_id" readonly>
                        </td>
	                    
	                     <td>
                        	<?
								echo create_drop_down( "cbo_shipping_status", 100, $shipment_status,"", 0, "-- Select --", $selected, "",0,'','','','','' );
								// echo create_drop_down( "cbo_brand_id", 100, $blank_array,"", 1, "-- All Brand --", $selected, "",0,"" );
							?>
                        </td>
  	                     
	                </tr>
	              <!--  <tr align="center"  class="general">
	                    <td colspan="13"><? //echo load_month_buttons(1); ?></td>
	                </tr>-->
	                <tr>
	                    <td colspan="13" align="center" id="data_panel">&nbsp;</td>
	                </tr>
	                <tr>
	                     
                      
                        <td colspan="13" align="right">
                          <input type="button"  id="print_1" class="formbutton" style="width:90px;" value="Show2"  name="print_1"  onClick="fn_report_generated(2)" />&nbsp;
                           <input type="button"  id="print_1" class="formbutton" style="width:90px;" value="Show"  name="print_1"  onClick="fn_report_generated(1)" />&nbsp;
                        <input type="reset" id="reset_btn" class="formbutton" style="width:60px" value="Reset" onClick="reset_form('budgetReport_1','report_container*report_container2','','','')" />
                        </td>
	                </tr>
	            </table>
	        </fieldset>
	    </div>
	    <div id="report_container" align="center"></div>
	    <div id="report_container2"></div>
	</form>
	
    </body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
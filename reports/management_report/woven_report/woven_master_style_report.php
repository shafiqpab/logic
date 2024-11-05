<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create For Woven Master Style Report
Functionality	:
JS Functions	:
Created by		:	Aziz 
Creation date 	: 	13-12-2020
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
	    // var txt_job_no=document.getElementById('txt_job_no').value;
	    // var txt_job_id=document.getElementById('txt_job_id').value;
		var cbo_season_year=document.getElementById('cbo_season_year').value;
		var company_name=document.getElementById('cbo_company_name').value;
        //var budget_version=document.getElementById('cbo_budget_version').value;
		/*if(txt_ref_no=="" &&  txt_style_ref=="" )
		{
			alert("Please Write Job No/Master Style/March Style");
			return;
		}*/

		if(company_name==0)
		{
			
				if(form_validation('cbo_company_name','Company')==false)
					{
						return;
					}
		}
		
		if(type==1){

			var data="action=report_generate&reporttype="+type+get_submitted_data_string('cbo_company_name*cbo_season_id*cbo_season_year*cbo_brand_id*cbo_buyer_name*hide_order_id*txt_ref_no*txt_date_from_rec*txt_date_to_rec*txt_date_from_target*txt_date_to_target*txt_style_id*txt_style_ref*cbo_team_leader*cbo_dealing_merchant',"../../../");
		}else if(type==2){
       		 var data="action=report_generate2&reporttype="+type+get_submitted_data_string('cbo_company_name*cbo_season_id*cbo_season_year*cbo_brand_id*cbo_buyer_name*hide_order_id*txt_ref_no*txt_date_from_rec*txt_date_to_rec*txt_date_from_target*txt_date_to_target*txt_style_id*txt_style_ref*cbo_team_leader*cbo_dealing_merchant',"../../../");
		}else if(type==3){
       		 var data="action=report_generate3&reporttype="+type+get_submitted_data_string('cbo_company_name*cbo_season_id*cbo_season_year*cbo_brand_id*cbo_buyer_name*hide_order_id*txt_ref_no*txt_date_from_rec*txt_date_to_rec*txt_date_from_target*txt_date_to_target*txt_date_from_bulk*txt_date_to_bulk*txt_style_id*txt_style_ref*cbo_team_leader*cbo_dealing_merchant',"../../../");
		}
		else if(type==4){
       		 var data="action=report_generate4&reporttype="+type+get_submitted_data_string('cbo_company_name*cbo_season_id*cbo_season_year*cbo_brand_id*cbo_buyer_name*hide_order_id*txt_ref_no*txt_date_from_rec*txt_date_to_rec*txt_date_from_target*txt_date_to_target*txt_date_from_bulk*txt_date_to_bulk*txt_style_id*txt_style_ref*cbo_team_leader*cbo_dealing_merchant',"../../../");
		}
        freeze_window(3);
       
        http.open("POST","requires/woven_master_style_report_controller.php",true);
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
			var tableFilters = {
                    col_operation: {
                        id: ["po_qty_td"],
                        //col: [11,12,15,16,17,19,20,22,24,25,27,29,30,31,32,33,34,36,37,38,39,40,41,42,44,45,47,48,49,51,52,53,54],
                        col: [43],
                        operation: ["sum"],
                        write_method: ["innerHTML"]
                    }
                }
				
             setFilterGrid("table_body",-1,tableFilters);
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
		var page_link='requires/woven_master_style_report_controller.php?action=style_ref_popup&companyID='+company_name+'&style_owner='+style_owner+'&buyer_name='+buyer+'&txt_job_no='+txt_job_no+'&txt_job_id='+txt_job_id;+'&cbo_year_id='+cbo_year
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
        // var style_owner = $("#cbo_style_owner").val();
	    var company_name = $("#cbo_company_name").val();
	    if(company_name==0)
		{
			if(form_validation('cbo_company_name','Company')==false)
			{
				return;
			}
		}
        // var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value;
		var companyID = $("#cbo_company_name").val();
		var buyerID = $("#cbo_buyer_name").val();
		//var txt_job_id = $("#txt_job_id").val();
		if(type==1)
		{
			var title='Master Style';
		}
		else
		{
			var title='Merch Style';
		}
		var page_link='requires/woven_master_style_report_controller.php?action=order_no_search_popup&companyID='+companyID+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name+'&type='+type;
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


   

//generate_style_report_with_graph

  
    function report_meeting_popup(cbo_company_name,inquery_id,action,type)
    {
     
	  //  var company_id = $("#cbo_company_name").val();
	//	var buyer_id = $("#cbo_buyer_name").val();
	    var popup_width='1250px';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_master_style_report_controller.php?cbo_company_name='+cbo_company_name+'&inquery_id='+inquery_id+'&type='+type+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
    }
	
	function report_po_popup(cbo_company_name,master_style_ref,inquire_id,action,type)
    {
     
	  //  var company_id = $("#cbo_company_name").val();
	//	var buyer_id = $("#cbo_buyer_name").val();
	//alert(master_style_ref);
	if(type==4)
	{
		 var popup_width='550px';
	}
	else
	{
	    var popup_width='1550px';
	}
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_master_style_report_controller.php?cbo_company_name='+cbo_company_name+'&master_style_ref='+master_style_ref+'&type='+type+'&inquire_id='+inquire_id+'&action='+action, 'Style Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
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
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_master_style_report_controller.php?po_id='+po_id+'&country_date='+country_date+'&buyer_id='+buyer_id+'&job_no='+job_no+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
    }
    function country_order_dtls_trim(po_id,country_id,buyer_id,job_no,action)
    {

        var popup_width='850px';
        //country_trims_dtls_popup
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_master_style_report_controller.php?po_id='+po_id+'&country_id='+country_id+'&buyer_id='+buyer_id+'&job_no='+job_no+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
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
	 function report_generate_inquery_pop(cbo_company_name,sys_no,update_id)//
	 {
		 

		 var report_title='';
		 print_report( cbo_company_name+'**'+sys_no+'**'+update_id+'**'+report_title, "inquery_entry_print", "../../../order/woven_gmts/requires/quotation_inquery_controller" )
		 return;
	 }
	 function report_generate_samp_req_pop(cbo_company_name,update_id,txt_booking_no)//report_generate_samp_req_pop
	 {
		 
		var action="sample_requisition_print1";
		 var cbo_template_id=1; 
		// var report_title='';
		 print_report( cbo_company_name+'*'+update_id+'**'+txt_booking_no+'**'+cbo_template_id, "sample_requisition_print1", "../../../order/woven_gmts/requires/sample_requisition_with_booking_controller" )
		 return;
	 }
	 
	 
	 function report_la_costing_popup(cbo_company_name,update_id,action,type)//report_generate_samp_req_pop
    {
         if(update_id=="")
		 {
			 alert('System no not found');
			 return;
		 }
		 var update_id=update_id;
        //freeze_window(5);
        var report_title="Consumption Entry [CAD] For LA Costing";
        var data="action=consumption_report"+'&report_title='+report_title+'&update_id='+update_id;
        http.open("POST","../../../order/woven_gmts/requires/consumption_la_costing_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = generate_report_reponse;
    }
    function generate_report_reponse(){
        if(http.readyState == 4){
           /* var file_data=http.responseText.split("****");
            //$('#pdf_file_name').html(file_data[1]);
            $('#data_panel').html(file_data[0]);
            var w = window.open("Surprise", "_blank");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
            '<html><head><link rel="stylesheet" href="../../css/prt.css" type="text/css" media=print /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
            d.close();*/
				var w = window.open("Surprise", "#");
                var d = w.document.open();
               // d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
                  //  '<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				  d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><body><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="all" /><title></title></head>'+http.responseText+'</body</html>');
                d.close(); 
            //release_freezing();
        }
        else{
          // release_freezing();
        }
    }
	
	 function fnc_quick_costing_print(cost_sheet_no,qc_no,action,type)//report_generate_samp_req_pop
	 {
		 
		var action="quick_costing_print";
		var report_title='BASIC COST SHEET';
		// var report_title='';
		 print_report( qc_no+'*'+cost_sheet_no+'*'+report_title, "quick_costing_print", "../../../order/spot_costing/requires/quick_costing_woven_controller" )
		 return;
	 }
	 function openmypage_file(comp,update_id,i,type)
	{
		 
		 
			var page_link='requires/woven_master_style_report_controller.php?action=show_file&update_id='+update_id; 
		 
		var title="File Download View";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=250px,center=1,resize=0,scrolling=0','../../')
	}
	
		 

    function report_generate_pop(company_id,style_owner,job_no,inquire_id,buyer_id,booking_no,action,type)
    {
       //if(style_owner>0)  company_id=style_owner;//else company_id=company_id;
	   // var data="&action=generate_style_report_with_graph"+
	  
	   if(type==1) //cbo_order_status
	   { 
	   		//var job_no="";
		   
		 var po_id=booking_no;
		   var cbo_shipment_status=0;var cbo_order_status=0;var cbo_search_type=1;
	    var data="&action=basic_cost"+
            '&style_owner='+"'"+style_owner+"'"+
            '&txt_job_no='+"'"+job_no+"'"+
            '&cbo_company_name='+"'"+company_id+"'"+
            '&cbo_buyer_name='+"'"+buyer_id+"'"+
			'&inquire_id='+"'"+inquire_id+"'"+
		   '&hide_order_id='+"'"+po_id+"'";
	   }
	   if(type==2) //main Fabric
	   {
		  //  alert(inquire_id);
			var data="&action=fabric_booking_report"+
            '&style_owner='+"'"+style_owner+"'"+
            '&txt_job_no='+"'"+job_no+"'"+
            '&cbo_company_name='+"'"+company_id+"'"+
            '&cbo_buyer_name='+"'"+buyer_id+"'"+
			'&inquire_ids='+"'"+inquire_id+"'"+
		   '&txt_booking_no='+"'"+booking_no+"'";
	   }
	   if(type==4)//Sample With Order
	   {
		    var data="&action=show_fabric_booking_report"+
            '&style_owner='+"'"+style_owner+"'"+
            '&txt_job_no='+"'"+job_no+"'"+
            '&cbo_company_name='+"'"+company_id+"'"+
            '&cbo_buyer_name='+"'"+buyer_id+"'"+
			'&inquire_id='+"'"+inquire_id+"'"+
		   '&txt_booking_no='+"'"+booking_no+"'";
	   }
	   if(type==3)//Short  Booking
	   {
		    var data="&action=fabric_booking_report"+
            '&style_owner='+"'"+style_owner+"'"+
            '&txt_job_no='+"'"+job_no+"'"+
            '&cbo_company_name='+"'"+company_id+"'"+
            '&cbo_buyer_name='+"'"+buyer_id+"'"+
			'&inquire_id='+"'"+inquire_id+"'"+
		   '&txt_booking_no='+"'"+booking_no+"'";
		  // alert(type+'='+data);
	   }
	   
	    if(type==5)//Trim  Booking//Work Progress Report
	   {
		   
		    var data="&action=trims_rec_popup"+
            '&style_owner='+"'"+style_owner+"'"+
            '&txt_job_no='+"'"+job_no+"'"+
            '&cbo_company_name='+"'"+company_id+"'"+
            '&cbo_buyer_name='+"'"+buyer_id+"'"+
			'&po_id='+"'"+inquire_id+"'"+
		   '&txt_booking_no='+"'"+booking_no+"'";
	   }
	    if(type==6)//Wash  Wo //Work Progress Report
	   {
		   
		   var data_str=job_no+'_'+inquire_id;
		    var data="&action=wash_booked_style_popup"+
            '&style_owner='+"'"+style_owner+"'"+
            '&job_number='+"'"+job_no+"'"+
            '&cbo_company_name='+"'"+company_id+"'"+
            '&cbo_buyer_name='+"'"+buyer_id+"'"+
			'&po_id='+"'"+inquire_id+"'"+
			'&data_str='+"'"+data_str+"'"+
		   '&txt_booking_no='+"'"+booking_no+"'";
	   }
	    if(type==7)//Fab Pi   Wo  //Trim  report_generate_trims
	   {
		   
		 	 var  cbo_item_category_id=3; //booking_no='';
			  var type_cut=1; 
			  
		    var data="&action=report_generate_woven"+
            '&style_owner='+style_owner+
            '&job_number='+job_no+
            '&cbo_company_name='+company_id+
            '&cbo_buyer_name='+buyer_id+
			'&txt_pi_no='+inquire_id+
			 '&cbo_item_category_id='+cbo_item_category_id+ // '&type='+"'"+type_cut+"'";
			 '&type='+type_cut+
		   '&txt_wo_po_no='+booking_no;
	   }
	    if(type==8)//Trim  
	   {
		   
		 	 var  cbo_item_category_id=4; //txt_pi_no=''; 
			  var type_cut=1; 
			var  cbo_date_type=3;
		    var data="&action=report_generate_trims"+
            '&style_owner='+style_owner+
            '&job_number='+job_no+
            '&cbo_company_name='+company_id+
            '&cbo_buyer_name='+buyer_id+
			'&txt_pi_no='+inquire_id+ 
			'&txt_pi_id='+style_owner+
			 '&cbo_date_type='+cbo_date_type+ 
			 '&cbo_item_category_id='+cbo_item_category_id+ // '&type='+"'"+type_cut+"'";
			 '&type='+type_cut+
		   '&txt_wo_po_no='+booking_no;
	   }
	    if(type==9)//Wash PI  
	   {
			 var  cbo_item_category_id=25; //txt_pi_no='';
			 var entry_form = "170";
			  var data=cbo_importer_id+'*'+booking_no+'*'+entry_form+'*'+cbo_item_category_id;
			  
		    var data="&action=print"+
            '&data='+data+
		   '&update_id='+booking_no;
	   }
	   if(type==10)//SC...LC  
	   {
		   
		 	 var  cbo_item_category_id=4; txt_pi_no='';
			  var cbo_date_type=1; 
			  
		    var data="&action=report_generate"+
            '&txt_lc_sc_no='+booking_no+
            '&cbo_date_type='+cbo_date_type+
            '&cbo_company_name='+company_id+
            '&cbo_buyer_name='+buyer_id+
			'&txt_pi_no='+inquire_id+
			 '&cbo_item_category_id='+cbo_item_category_id+ // '&type='+"'"+type_cut+"'";
			 '&type='+type_cut+
		   '&txt_wo_po_no='+booking_no;
	   }
       // alert(data);
		 if(type==1)
		 {
       	 http.open("POST","../../../order/woven_gmts/requires/pre_cost_entry_controller_v2.php",true);
		 }
		 if(type==2)
		 {
       	 	 //http.open("POST","../../../order/woven_gmts/requires/woven_partial_fabric_booking_controller.php",true);
			 http.open("POST","requires/woven_master_style_report_controller.php",true);
		 } 
		 if(type==3)
		 {
       	 	 http.open("POST","../../../order/woven_gmts/requires/short_fabric_booking_controller.php",true);
			 //http.open("POST","requires/woven_master_style_report_controller.php",true);
		 }
		 if(type==4)
		 {
       	 	 http.open("POST","../../../order/woven_gmts/requires/sample_booking_controller.php",true);
		 }
		 if(type==5 || type==6)
		 {
       	 	 http.open("POST","../../../reports/management_report/merchandising_report/requires/shipment_date_wise_wp_report_woven_controller2.php",true);
		 }
		 if(type==7 || type==8) //Fab Pi No//Trim Pi
		 {
       	 http.open("POST","../../../commercial/reports/requires/purchase_recap_report3_controller.php",true);
		 }
		 if(type==9) //Wash Pi No//
		 {
       	 http.open("POST","../../../commercial/import_details/requires/pi_print_urmi.php",true);
		 }
		 if(type==10) //SC\LC Pi No//
		 {
       	 http.open("POST","../../../commercial/reports/requires/export_lc_and_sales_contract_report_controller.php",true);
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
               	var w = window.open("Surprise", "#");
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
        var page_link='requires/woven_master_style_report_controller.php?action=search_popup&companyID='+companyID+'&buyerID='+buyerID+'&job_no='+job_no;
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
        $('#button_data_panel').html('');
        get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/woven_master_style_report_controller' );
    }

    function print_report_button_setting(report_ids)
    {
        var report_id=report_ids.split(",");
        for (var k=0; k<report_id.length; k++)
        {
            if(report_id[k]==108)
            {

                $('#button_data_panel').append( '<input type="button"  id="print_1" class="formbutton" style="width:80px;" value="Show"  name="print_1"  onClick="fn_report_generated(1)" />&nbsp;&nbsp;' );
            }
            if(report_id[k]==195)
            {
                $('#button_data_panel').append( '<input type="button"  id="print_2" class="formbutton" style="width:80px;" value="Show 2"  name="print_2"  onClick="fn_report_generated(2)" />&nbsp;&nbsp;' );
            }
			if(report_id[k]==242)
            {
                $('#button_data_panel').append( '<input type="button"  id="print_3" class="formbutton" style="width:80px;" value="Show 3"  name="print_3"  onClick="fn_report_generated(3)" />&nbsp;&nbsp;' );
            }
			if(report_id[k]==359)
            {
                $('#button_data_panel').append( '<input type="button"  id="print_4" class="formbutton" style="width:80px;" value="Show 4"  name="print_4"  onClick="fn_report_generated(4)" />&nbsp;&nbsp;' );
            }
        }
    }

    function generate_popup(job,action,type)
    {
        if(type==1)
		{
		var width=500;
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_master_style_report_controller.php?action='+action+'&job='+job+'&type='+type, 'Remarks Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
    }
	function fn_on_change()
	{
		var cbo_company_name = $("#cbo_company_name").val();
		load_drop_down( 'requires/woven_master_style_report_controller', cbo_company_name, 'load_drop_down_buyer', 'buyer_td' );
		set_multiselect('cbo_buyer_name','0','0','','0','fn_on_change2()');
		
		//set_multiselect('cbo_company_name','0','0','','0','fn_on_change()');
	}
	function fn_on_change2()
	{
		
		//var buyer_id = 1;
		var buyer_id = $("#cbo_buyer_name").val();
		//var cbo_working_company_name = $("#cbo_working_company_name").val();
		load_drop_down( 'requires/woven_master_style_report_controller', buyer_id, 'load_drop_down_brand', 'brand_td' );
		load_drop_down( 'requires/woven_master_style_report_controller', buyer_id, 'load_drop_down_season', 'season_td' );
		set_multiselect('cbo_season_id','0','0','','0');
		set_multiselect('cbo_brand_id','0','0','','0');
	}
	function fn_on_change_dealing()
	{
		
		//var buyer_id = 1;
		var team_id = $("#cbo_team_leader").val();
		//var cbo_working_company_name = $("#cbo_working_company_name").val();
	//	load_drop_down( 'requires/woven_master_style_report_controller', buyer_id, 'load_drop_down_brand', 'brand_td' );
		load_drop_down( 'requires/woven_master_style_report_controller', team_id, 'load_drop_down_dealing', 'dealmarchant_td' );
		//set_multiselect('cbo_season_id','0','0','','0');
		set_multiselect('cbo_dealing_merchant','0','0','','0');
	}
	</script>
	</head>
	<body onLoad="set_hotkey();">
	<form id="budgetReport_1">
	    <div style="width:100%;" align="center">
	        <? echo load_freeze_divs ("../../../",$permission);  ?>
	        <h3 align="left" id="accordion_h1" style="width:1420px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
	        <fieldset style="width:1420px;" id="content_search_panel">
	            <table class="rpt_table" width="1420" cellpadding="1" cellspacing="2" align="center" border="1" rules="all">
	                <thead>
	                <th class="must_entry_caption">Company Name</th>
	                <th>Buyer Name</th>
                    <th>Season</th>
                    <th>Season Year</th>
                     <th>Brand</th>
	              	<th>Team Leader</th>
                    <th>Dealing Merchant</th>
	                <th>Master Style </th>
	                <th>Merch  Style</th>
	                <th>Inq.Rcvd Date</th>
                    <th>Cons. Rec.Tgt. Date Range</th>
					<th>Bulk Est. Ship Date</th>
	                </thead>
	                <tbody>
	                <tr class="general">
	                    <td>
	                        <?
	                        echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "-- Select Company --", $selected,"" );
	                        ?>
	                    </td>
	                     <td id="buyer_td">
	                        <?
	                        echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "--All Buyer--", $selected, "",0,"" );
	                        ?>
	                     </td>
                         <td id="season_td"><? echo create_drop_down( "cbo_season_id", 120, $blank_array,'', 1, "--Season--",$selected, "" ); ?>    </td>
                         <td><? echo create_drop_down( "cbo_season_year", 120, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
                         <td id="brand_td">
                            <?
                            echo create_drop_down( "cbo_brand_id", 100, $blank_array,"", 1, "-- All Brand --", $selected, "",0,"" );
                            ?>
                  		 </td>
                         <td id="leader_td">
                            <?
                            echo create_drop_down( "cbo_team_leader", 140, "select id,team_leader_name from lib_marketing_team  where  status_active=1 and is_deleted=0 and project_type=2 order by team_leader_name","id,team_leader_name", 0, "-Team Leader-", $selected, "" );
                            ?>
                  		 </td>
                         <td id="dealmarchant_td">
                            <?
                            echo create_drop_down( "cbo_dealing_merchant", 140, "select id,team_member_name from lib_mkt_team_member_info where   status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 0, "-- Select Team Member --", $selected, "" );
                            ?>
                  		</td>
                        <td>
                             <input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:80px" placeholder="Browse Or Write" onDblClick="openmypage_order(1);" onChange="$('#hide_order_id').val('');" autocomplete="off">
                             <input type="hidden" name="hide_order_id" id="hide_order_id" readonly/>
	                    </td>
	                    <td>
	                        <input type="text" id="txt_style_ref" name="txt_style_ref" class="text_boxes" style="width:80px" onDblClick="openmypage_order(2);"  onChange="$('#txt_style_id').val('');" autocomplete="off"
                       placeholder="Wr./Br. Order"  />
                            <input type="hidden" name="txt_style_id" id="txt_style_id" readonly>
                        </td>
	                    
	                     <td>
                            <input name="txt_date_from_rec" id="txt_date_from_rec" title="01-jul-2022"  class="datepicker" style="width:45px" placeholder="From Date" >
                            <input name="txt_date_to_rec" id="txt_date_to_rec"  class="datepicker" style="width:45px" placeholder="To Date">
                        </td>
                        <td>
                            <input name="txt_date_from_target" id="txt_date_from_target"  class="datepicker" style="width:45px" placeholder="From Date" >
                            <input name="txt_date_to_target" id="txt_date_to_target"  class="datepicker" style="width:45px" placeholder="To Date">
                        </td>
						<td>
                            <input name="txt_date_from_bulk" id="txt_date_from_bulk"  class="datepicker" style="width:45px" placeholder="From Date" >
                            <input name="txt_date_to_bulk" id="txt_date_to_bulk"  class="datepicker" style="width:45px" placeholder="To Date">
                        </td>
  	                     
	                </tr>
	                <tr>
                        <td colspan="14" align="right" style="padding-top: 4px;padding-bottom: 4px;">
                            <span id="button_data_panel"></span>
                            <input type="reset" id="reset_btn" class="formbutton" style="width:60px" value="Reset" onClick="reset_form('budgetReport_1','report_container*report_container2','','','')" />&nbsp;&nbsp;
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
    <script type="text/javascript">
	set_multiselect('cbo_company_name','0','0','','0','print_button_setting();fn_on_change();');
	set_multiselect('cbo_buyer_name','0','0','','0');
	set_multiselect('cbo_season_id','0','0','','0');
	set_multiselect('cbo_brand_id','0','0','','0');
	set_multiselect('cbo_team_leader','0','0','','0','fn_on_change_dealing()');
	set_multiselect('cbo_dealing_merchant','0','0','','0');
	
    </script>
</html>
<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Pre Costing Approval
					
Functionality	:	
				
JS Functions	:

Created by		:	Md.Didarul Alam
Creation date 	: 	14-10-2014
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
$menu_id=$_SESSION['menu_id'];
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Fabric Booking Approval", "../", 1, 1,'','','');
$approval_setup = is_duplicate_field( "page_id", "electronic_approval_setup", "page_id=$menu_id and is_deleted=0" );
?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';
    
	function fn_report_generated()
	{        
        var approval_setup =<? echo $approval_setup; ?>;
		freeze_window(3);
		if(approval_setup!=1)
		{
			alert("Electronic Approval Setting First.");
			release_freezing(); 	
			return;
		}
    
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			release_freezing(); 
			return;
		}
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*cbo_get_upto*txt_date*cbo_approval_type',"../");
		
		http.open("POST","requires/pre_costing_approval_new_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{          
			var response=trim(http.responseText).split("####");
			$('#report_container').html(response[0]);
			
			var tableFilters = { col_0: "none" }
			setFilterGrid("tbl_list_search",-1,tableFilters);				
            show_msg('3');
            release_freezing();    
		}
	}
	
    // if any job number of cost component is checked then job no would be checked automatically  
    function specificJobCheck(job) 
	{       
        jobNoId = $('#check'+job).attr("id");
        clickValue = 'check'+job;       
        if (jobNoId===clickValue) { 
            $("#"+jobNoId).attr('checked', true); 
        }  
    }
	
	// ============ check un-approve job no of cost component whether booked or not start =====================
	function specificJobCostUnCheck(approvalType,jobcostRowId,costKey,jobNo)
	{
		//alert(costKey+"---"+jobNo);
		if ( (approvalType == 1) && ($("#cost_com_"+jobcostRowId).prop('checked') == false) && (costKey == 1 || costKey == 2 || costKey==3 || costKey==4 || costKey==7) )
		{
			var data="action=check_cost_wise_job_booking"+"&job_cost_row_id="+jobcostRowId+"&cost_component_key="+costKey+"&job_no="+jobNo;
			
			http.open("POST","requires/pre_costing_approval_new_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
	
			http.onreadystatechange = fn_job_booking_reponse;
		}
	}
	
	function fn_job_booking_reponse()
	{
		if(http.readyState == 4) 
		{  
			var reponse=trim(http.responseText).split('**');
			
			if( reponse[1] !='' )
			{
				
				decision = confirm("Following Booking Found :\n\n"+reponse[1]);
				
				if (decision == true)
				{
					//alert(reponse[0]);
					$("#cost_com_"+reponse[0]).prop('checked',false);
				}
				else
				{
					$("#cost_com_"+reponse[0]).prop('checked',true);
				}
				//$("#cost_com_"+jobNoId).attr('checked', true); 
				//$("#"+jobNoId).attr('checked', true); 
			}
		}
	}
	// ============ check un-approve job no of cost component whether booked or not end =====================

    function check_all_cost_component(trSl,jobNo)
	{       
        if($('#check'+trSl).is(':checked')) {          
            $("#tbl_list_search tbody tr #tbl_"+trSl+" input:checkbox").attr('checked',true);
            $("#tbl_list_search tbody tr #cost_component_"+jobNo+"").each(function() {                          
				$('#tbl_list_search tbody tr #cost_component_'+jobNo+' input:checkbox').attr('checked', true);               
            });                      
        } else {           
            $("#tbl_list_search tbody tr #cost_component_"+jobNo+"").each(function() {
				$('#tbl_list_search tbody tr #cost_component_'+jobNo+' input:checkbox').attr('checked', false);
			}); 
        }  
	}
      
	function check_all(tot_check_box_id)
	{
		if ($('#'+tot_check_box_id).is(":checked"))
		{ 
			$('#tbl_list_search tbody tr').each(function() {
				/*if($('#tbl_list_search tbody tr').css("display") != "none")
				{*/
					$('#tbl_list_search tbody tr input:checkbox').attr('checked', true);
				//}
			});
		}
		else
		{ 
			$('#tbl_list_search tbody tr').each(function() {
				/*if($('#tbl_list_search tbody tr').css("display") != "none")
				{*/
					$('#tbl_list_search tbody tr input:checkbox').attr('checked', false);
				//}
			});
		} 
	}
	
	function check_all2()
	{
		if(document.getElementById('all_check').checked==true)
		{
			document.getElementById('all_check').value=1;
		}
		else if(document.getElementById('all_check').checked==false)
		{
			document.getElementById('all_check').value=2;
		}
		
		var list_tot_row=$('#tbl_list_search tbody tr').length-1;
		//tot_row = tbl_row_count-1;
		for( var i = 1; i <= list_tot_row; i++ )
		{
			if($("#tr_"+i).css("display") != "none")
			{
				js_set_value( trid );
				if($('#all_check').val()==1)
				{
					document.getElementById('checkid'+i).checked=true;
					document.getElementById('checkid'+i).value=1;
				}
				else if($('#all_check').val()==2) 
				{
					document.getElementById('checkid'+i).checked=false;
					document.getElementById('checkid'+i).value=2;
				}
			}
		}
	}
    		
	function submit_approved(total_tr,type,permission)
	{        
        //var operation=4;
		var booking_nos = "";  var booking_ids = "";
		freeze_window(0);
        if(permission!=1)
		{
			alert("You are unauthorized to sign the Pre Costing.");	
			release_freezing(); 
			return false;
		}
		
        // confirm message  *********************************************************************************************************
		if($('#cbo_approval_type').val()==1)
		{
			if($("#all_check").is(":checked"))
			{
				first_confirmation=confirm("Are You Want to UnApproved All Job");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnApproved All Job");
					if(second_confirmation==false)
					{
						release_freezing();
						return;					
					}
				}
			}
		}
		// confirm message finish ***************************************************************************************************
        
        costComponents = '';

		for (i=1; i<total_tr; i++)
		{              
            if($('#check'+i).is(':checked'))
			{   
                
                booking_id = $('#booking_id_'+i).val();
				if(booking_ids=="") booking_ids= booking_id; else booking_ids +=','+booking_id;
                
                booking_no = $('#booking_no_'+i).val();
                if(booking_nos=="") booking_nos="'"+booking_no+"'"; else booking_nos +=",'"+booking_no+"'";

                if (costComponents !='') costComponents +=",";          
                
                costTblTrLength = $('#cost_component_tbl_'+i+ ' tr').length; 

                checkedUncheckedCostComponents = '';
               
                for(k=1;k<=costTblTrLength;k++) 
                {  
                    if (checkedUncheckedCostComponents !='') checkedUncheckedCostComponents +=",";

                    if($('#cost_com_'+i+'_'+k).is(':checked')) 
                    {                          
                        checkedUncheckedCostComponents+=$('#cost_com_hidden_'+i+'_'+k).val()+'*'+1; 
                    } 
                    else 
                    {                          
                       checkedUncheckedCostComponents +=$('#cost_com_hidden_'+i+'_'+k).val()+'*'+0;
                    }
                }
                costComponents +=checkedUncheckedCostComponents;              
			}
		}
        
        if(booking_nos=="")
		{
			alert("Please Select At Least One Job");
			release_freezing();
			return;
		}
        
		var data="action=approve&operation="+operation+'&approval_type='+type+'&booking_ids='+booking_ids+'&costComponents='+costComponents+get_submitted_data_string('cbo_company_name',"../");
		
		http.open("POST","requires/pre_costing_approval_new_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_precosting_approval_Reply_info;
	}	
	
	function fnc_precosting_approval_Reply_info()
	{
		if(http.readyState == 4) 
		{ 
			var reponse=trim(http.responseText).split('**');
                      
            if (reponse[0]==100) { 
                fn_report_generated();
            }
            else if(reponse[0]==101)
            {  
                fn_report_generated();
            }
			else if(reponse[0]==40)
            { 
				alert("Job No : '"+trim(reponse[1])+"' Ready To Approved Change Found.\n Please Check again. Sincerely sorry for the mistake.");
                release_freezing();
            }
             
			if((reponse[0]==19 || reponse[0]==20))
			{ 
				fnc_remove_tr();
                fn_report_generated();
                show_msg(reponse[0]);   
                release_freezing();    
			}
			release_freezing(); 
		}
	}
	
	function fnc_remove_tr()
	{
		var tot_row=$('#tbl_list_search tbody tr').length;
		for(var i=1;i<=tot_row;i++)
		{
			if($('#check'+i).is(':checked'))
			{
				$('#tr_'+i).remove();
			}
		}
	}
	
    function report_part(type,job_no,company_id,buyer_id,style_ref,txt_costing_date,po_break_down_id,pre_cost_version) {
        if(type=='preCostRpt')
        {
            var print_option = $("#print_option").val();
            var print_option_id = $("#print_option_id").val();
            var print_option_no = $("#print_option_no").val();
            
            
            var page_link='requires/pre_costing_approval_new_controller.php?action=report_part_select_view&print_option='+print_option+'&print_option_id='+print_option_id+'&print_option_no='+print_option_no;  
            
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Pre-Costing Print Option", 'width=460px,height=270px,center=1,resize=0,scrolling=0','')
            emailwindow.onclose=function()
            {
                var theform=this.contentDoc.forms[0]; 
                var option_des=this.contentDoc.getElementById("txt_selected").value; 
                var option_id=this.contentDoc.getElementById("txt_selected_id").value; 
                var serial_no=this.contentDoc.getElementById("txt_selected_no").value; 
                $("#print_option").val(option_des);
                $("#print_option_id").val(option_id); 
                $("#print_option_no").val(serial_no);
                generate_report(type,job_no,company_id,buyer_id,style_ref,txt_costing_date,po_break_down_id,option_id,pre_cost_version);               
            }		
        }			
    }
    
    function generate_report(type,job_no,company_id,buyer_id,style_ref,txt_costing_date,po_break_down_id,option_id,pre_cost_version)
    {       
       
	    var zero_val='';
        var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
        if (r==true)
        {
            zero_val="1";            
        }
        else
        {
            zero_val="0";
        } 
                
        var data="action="+type+"&txt_job_no="+"'"+job_no+"'"+"&cbo_company_name="+"'"+company_id+"'"+"&cbo_buyer_name="+"'"+buyer_id+"'"+"&txt_style_ref="+"'"+style_ref+"'"+"&txt_costing_date="+"'"+txt_costing_date+"'"+"&txt_po_breack_down_id="+"'"+po_break_down_id+"'"+"&print_option_id="+"'"+option_id+"'"+"&zero_value="+zero_val+"&img_path=../"
		if(pre_cost_version==1)
		{
       		 http.open("POST","../order/woven_order/requires/pre_cost_entry_controller.php",true);
		}
		else
		{
			 http.open("POST","../order/woven_order/requires/pre_cost_entry_controller_v2.php",true);
		}
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_generate_report_reponse;
    }
    
    function fnc_generate_report_reponse()
    {
        if(http.readyState == 4) 
        {
            $('#data_panel').html( http.responseText );
            var w = window.open("Surprise", "_blank");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
    '<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
            d.close();
        }
    }
    
    
	function openImgFile(id,action)
	{
		var page_link='requires/pre_costing_approval_new_controller.php?action='+action+'&id='+id;
		if(action=='img') var title='Image View'; else var title='File View';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','');
	}



	function generate_worder_report(type,job_no,company_id,buyer_id,style_ref,txt_costing_date,entry_form,quotation_id)
	{
		var zero_val='';
		var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		if (r==true)
		{
			zero_val="1";
		}
		else
		{
			zero_val="0";
		}
		if(type=="summary")
		{
			alert("i am here.");
			var report_title="Budget/Cost Sheet";
			var comments_head=0;
			var txt_style_ref_id='';

			var txt_order=""; var txt_order_id="";  var txt_season_id=""; var txt_season=""; var txt_file_no="";
			var data="action=report_generate&reporttype="+3+
			'&cbo_company_name='+"'"+company_id+"'"+
			'&cbo_buyer_name='+"'"+buyer_id+"'"+
			'&txt_style_ref='+"'"+style_ref+"'"+
			'&txt_style_ref_id='+"'"+txt_style_ref_id+"'"+
			'&txt_order='+"'"+txt_order+"'"+
			'&txt_order_id='+"'"+txt_order_id+"'"+
			'&txt_season='+"'"+txt_season+"'"+
			
			'&txt_season_id='+"'"+txt_season_id+"'"+
			'&txt_file_no='+"'"+txt_file_no+"'"+
			'&txt_quotation_id='+quotation_id+
			'&txt_hidden_quot_id='+quotation_id+
			'&comments_head='+"'"+comments_head+"'"+
			'&report_title='+"'"+report_title+"'"+
			'&path=../../../';
		//	alert(data)
			http.open("POST","../reports/management_report/merchandising_report/requires/cost_breakup_report2_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_fabric_report_reponse;
		}
		else
		{
			if(entry_form==111)
			{
				var data="action="+type+
						'&zero_value='+zero_val+
						'&txt_job_no='+"'"+job_no+"'"+
						'&cbo_company_name='+"'"+company_id+"'"+
						'&cbo_buyer_name='+"'"+buyer_id+"'"+
						'&txt_style_ref='+"'"+style_ref+"'"+
						'&print_option_id='+'1,2,3,4,5,6,7,8'+
						'&txt_po_breack_down_id='+''+
						'&txt_costing_date='+"'"+txt_costing_date+"'";
						http.open("POST","../order/woven_order/requires/pre_cost_entry_controller.php",true);
						http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
						http.send(data);
						http.onreadystatechange = generate_fabric_report_reponse;
			}
			else
			{
				var data="action="+type+
						'&zero_value='+zero_val+
						'&txt_job_no='+"'"+job_no+"'"+
						'&cbo_company_name='+"'"+company_id+"'"+
						'&cbo_buyer_name='+"'"+buyer_id+"'"+
						'&txt_style_ref='+"'"+style_ref+"'"+
						'&txt_costing_date='+"'"+txt_costing_date+"'";
						http.open("POST","../order/woven_order/requires/pre_cost_entry_controller_v2.php",true);
						http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
						http.send(data);
						http.onreadystatechange = generate_fabric_report_reponse;
			}
		}
	}
		
	function generate_fabric_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
			d.close();
		}
	}

	function generat_print_report(type,company_name,buyer_name,date_from,date_to,job_no,job_id,order_id,order_no,year,order_status,search_date,season,season_id,file_no,internal_ref)
	{
	
	var data="action=report_generate"+
			'&reporttype='+type+
			'&cbo_company_name='+"'"+company_name+"'"+
			'&cbo_buyer_name='+"'"+buyer_name+"'"+
			'&txt_date_from='+"'"+date_from+"'"+
			'&txt_date_to='+"'"+date_to+"'"+
			'&txt_job_no='+"'"+job_no+"'"+
			'&txt_job_id='+"'"+job_id+"'"+
			'&txt_order_id='+"'"+order_id+"'"+
			'&txt_order_no='+"'"+order_no+"'"+
			'&cbo_year='+"'"+year+"'"+
			'&cbo_order_status='+"'"+order_status+"'"+
			'&cbo_search_date='+"'"+search_date+"'"+
			'&txt_season='+"'"+season+"'"+
			'&txt_season_id='+"'"+season_id+"'"+
			'&txt_file_no='+"'"+file_no+"'"+
			'&txt_internal_ref='+"'"+internal_ref+"'";
					
		freeze_window(3);
		if(type==1 || type==2 || type==3 || type==4 || type==7)
		{
			http.open("POST","../reports/management_report/merchandising_report/requires/order_wise_budget_report_controller.php",true);
		}
		else if(type==8)
		{
			http.open("POST","../reports/management_report/merchandising_report/requires/order_wise_budget_report_controller2.php",true);
		}
		else if (type==5 || type==6)
		{
			http.open("POST","../reports/management_report/merchandising_report/requires/order_wise_budget_report2_controller.php",true);
		}
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generat_print_report_reponse;
	}
	
	function generat_print_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			$('#report_container2').html(reponse[0]); 	
			
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'<link rel="stylesheet" href="../css/style_common.css" type="text/css" /></body</html>');//
			d.close();
			
			$('#report_container2').html(''); 	
			

			release_freezing();
			show_msg('3');
		}
	}


	function openPopup(param,title,action)
	{
		
		var page_link='requires/pre_costing_approval_new_controller.php?action='+action+'&data='+param;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=1,scrolling=0','');
	}
</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",''); ?>
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:800px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:800px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>                        	
                            <tr>
                                <th class="must_entry_caption">Company Name</th>
                                <th>Buyer</th>
                                <th>Job No</th>
                                <th>Get Upto</th>
                                <th>Costing  Date</th>
                                <th>Approval Type</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                        	</tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/pre_costing_approval_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );" );
                                    ?>
                                </td>
                                <td id="buyer_td_id"> 
									<?
                                       echo create_drop_down( "cbo_buyer_name", 152, $blank_array,"", 1, "-- All Buyer --", 0, "" );
                                    ?>
                                </td>
                                <td><input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:65px"></td>
                                <td> 
									<?
										$get_upto=array(1=>"After This Date",2=>"As On Today",3=>"This Date");
                                       echo create_drop_down( "cbo_get_upto", 130, $get_upto,"", 1, "-- Select --", 0, "" );
                                    ?>
                                </td>
                                <td><input type="text" name="txt_date" id="txt_date" class="datepicker" readonly style="width:80px"/></td>
                                <td> 
                                    <?
                                        $pre_cost_approval_type=array(2=>"Un-Approved",1=>"Approved");
                                        echo create_drop_down( "cbo_approval_type", 130, $pre_cost_approval_type,"", 0, "", $selected,"","", "" );
                                    ?> 
                                    <input type="hidden" id="print_option" name="print_option" />
                                    <input type="hidden" id="print_option_no" name="print_option_no" />
                                    <input type="hidden" id="print_option_id" name="print_option_id" />     
                                </td>
                                <td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:100px" onClick="fn_report_generated()"/></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container" align="center"></div>
    <div style="display:none" id="data_panel"></div>
    <div id="report_container2" align="center"></div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$('#cbo_approval_type').val(0);
</script>
</html>
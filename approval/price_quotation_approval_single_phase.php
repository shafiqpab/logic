<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Price Quotation Approval Single Phase Approval
					
Functionality	:	
				

JS Functions	:

Created by		:	Md : Didarul Alam
Creation date 	: 	08-08-2016
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
echo load_html_head_contents("Price Quotation Approval Single Phase", "../", 1, 1,'','','');

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';


	function fn_report_generated()
	{
		freeze_window(3);
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			release_freezing();
			return;
		}
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_get_upto*txt_date*cbo_approval_type',"../");
		
		http.open("POST","requires/price_quotation_approval_single_phase_controller.php",true);
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
	
	function check_all(tot_check_box_id)
	{
		if ($('#'+tot_check_box_id).is(":checked"))
		{ 
			$('#tbl_list_search tbody tr').each(function() {
				$('#tbl_list_search tbody tr input:checkbox').attr('checked', true);
			});
		}
		else
		{ 
			$('#tbl_list_search tbody tr').each(function() {
				$('#tbl_list_search tbody tr input:checkbox').attr('checked', false);
			});
		} 
	}
	
	function check_on_scan(scan_no)
	{
		var row_no=$('#'+scan_no).val();
		var tbl_len=$("#tbl_list_search tbody tr").length;
		$('#tbl_'+row_no).attr('checked', true);
		
		//new
		if($('#tbl_'+row_no).is(":checked")==false)
		{
			alert("No data found");
			$('#txt_bar_code').val('');
			$('#txt_bar_code').focus();
			return;
		} 
		else
		{
			submit_approved(tbl_len, $('#cbo_approval_type').val());
		}
	}
		
	function submit_approved(total_tr,type,permission)
	{ 
		//var operation=4;
		var booking_nos = "";  var booking_ids = ""; var approval_ids = ""; 
		freeze_window(0);
        if (permission ==2) {            
            alert('You Have No Authority To Price Quotation Approval Single Phase'); 
			release_freezing();
            return false;	    
        }
		
		// Confirm Message  *********************************************************************************************************
		if($('#cbo_approval_type').val()==1)
		{
			if($("#all_check").is(":checked"))
			{
				//var cm_cost_id = $('#cm_cost_id_'+i).val();
				//var cm_compulsory=$('#txt_cm_compulsory').val();
				first_confirmation=confirm("Are You Want to UnApproved All Quatation No");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to UnApproved All Quatation No");
					if(second_confirmation==false)
					{
						release_freezing();
						return;					
					}
				}
			}
		}
		else if($('#cbo_approval_type').val()==0)
		{
			if($("#all_check").is(":checked"))
			{
			
				var cm_cost_id = $('#cm_cost_id_'+i).val()*1;
				var cm_compulsory=$('#txt_cm_compulsory').val()*1;
					//alert('DD');
				if($('#cbo_approval_type').val()==0)
				{
					if(cm_compulsory==1)
					{
						if(cm_cost_id<0 || cm_cost_id==0)
						{
							alert('Without CM Cost Approving not allowed');	
							release_freezing();
							return;
						}
					}
				}
				first_confirmation=confirm("Are You Want to Approved All Quatation No");
				if(first_confirmation==false)
				{
					release_freezing();
				 	return;	
				}
				else
				{
					second_confirmation=confirm("Are You Sure Want to Approved All Quatation No");
					if(second_confirmation==false)
					{
						release_freezing();
						return;					
					}
				}
			}
		}
		// Confirm Message finish ***************************************************************************************************
		
		
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				 booking_id = $('#booking_id_'+i).val();
				var cm_cost_id = $('#cm_cost_id_'+i).val()*1;
				var cm_compulsory=$('#txt_cm_compulsory').val()*1;
					//alert('MM');
				if($('#cbo_approval_type').val()==0)
				{
					if(cm_compulsory==1)
					{
						if(cm_cost_id<0 || cm_cost_id==0)
						{
							alert('Without CM Cost Approving not allowed');	
							return;
						}
					}
				}
				if(booking_ids=="") booking_ids= booking_id; else booking_ids +=','+booking_id;
				
				booking_no = $('#booking_no_'+i).val();
				if(booking_nos=="") booking_nos="'"+booking_no+"'"; else booking_nos +=",'"+booking_no+"'";
				
				approval_id = parseInt($('#approval_id_'+i).val());
				if(approval_id>0)
				{
					if(approval_ids=="") approval_ids= approval_id; else approval_ids +=','+approval_id;
				}
			}
		}
		
		if(booking_nos=="")
		{
			alert("Please Select At Least One Booking");
			release_freezing();
			return;
		}
		
		var data="action=approve&operation="+operation+'&approval_type='+type+'&booking_nos='+booking_nos+'&booking_ids='+booking_ids+'&approval_ids='+approval_ids+get_submitted_data_string('cbo_company_name',"../");
	
		http.open("POST","requires/price_quotation_approval_single_phase_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_price_quotation_approval_Reply_info;
	}	
	
	function fnc_price_quotation_approval_Reply_info()
	{
		if(http.readyState == 4) 
		{ 
			//release_freezing();	return;
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]=='approvedPre')
			{
				alert("Budget is approved againts this quotation");
				release_freezing();
				return;
			}	
			show_msg(reponse[0]);
			if((reponse[0]==19 || reponse[0]==20))
			{
				fnc_remove_tr();
			}
			$('#txt_bar_code').val('');
			$('#txt_bar_code').focus();
			release_freezing();	
		}
	}
	
	function fnc_remove_tr()
	{
		var tot_row=$('#tbl_list_search tbody tr').length;
		
		for(var i=1;i<=tot_row;i++)
		{
			if($('#tbl_'+i).is(':checked'))
			{
				$('#tr_'+i).remove();
			}
		}
	}
	
	function generate_worder_report(type,quatation_id,company_id,buyer_id,style_ref,quot_date)
	{
		var data="action=generate_report&type="+type+
		            
        '&txt_quotation_id='+"'"+quatation_id+"'"+
        '&cbo_company_name='+"'"+company_id+"'"+
        '&cbo_buyer_name='+"'"+buyer_id+"'"+
        '&txt_style_ref='+"'"+style_ref+"'"+
        '&txt_quotation_date='+"'"+quot_date+"'";
        
        http.open("POST","../order/woven_order/requires/quotation_entry_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = generate_pricequotation_report_reponse;
	}
		
		
	function generate_pricequotation_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
			d.close();
		}
	}
	
	function openImgFile(id,action)
	{
		var page_link='requires/price_quotation_approval_single_phase_controller.php?action='+action+'&id='+id;
		if(action=='img') var title='Image View'; else var title='File View';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','');
		
	}
	
	//barcode scan
	$('#txt_bar_code').live('keydown', function(e) {
		if  (e.keyCode === 13) 
		{
			e.preventDefault();
			var bar_code=$('#txt_bar_code').val();
			check_on_scan(bar_code);
		}
	});
	
	function fnc_load_cm_compulsory(data)
	{
	$('#txt_cm_compulsory').val('');
	var cm_compulsory = return_global_ajax_value( data, 'populate_cm_compulsory', '', 'requires/price_quotation_approval_single_phase_controller');
	$('#txt_cm_compulsory').val(cm_compulsory);
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
                            	<th colspan="6">Barcode Scan: <input type="text" id="txt_bar_code" name="txt_bar_code" class="text_boxes"  /></th>
                            </tr>
                            <tr>
                                <th class="must_entry_caption">Company Name</th>
                                <th>Buyer</th>
                                <th>Get Upto</th>
                                <th>Booking Date</th>
                                <th>Approval Type</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" />
                                <input style="width:50px;" type="hidden" name="txt_cm_compulsory" id="txt_cm_compulsory"/>
                                </th>
                        	</tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/price_quotation_approval_single_phase_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );fnc_load_cm_compulsory(this.value);" );
                                    ?>
                                </td>
                                <td id="buyer_td_id"> 
									<?
                                       echo create_drop_down( "cbo_buyer_name", 152, $blank_array,"", 1, "-- All Buyer --", 0, "" );
                                    ?>
                                </td>
                                <td> 
									<?
										$get_upto=array(1=>"After This Date",2=>"As On Today",3=>"This Date");
                                       echo create_drop_down( "cbo_get_upto", 130, $get_upto,"", 1, "-- Select --", 0, "" );
                                    ?>
                                </td>
                                <td><input type="text" name="txt_date" id="txt_date" class="datepicker" readonly style="width:80px"/></td>
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_approval_type", 130, $approval_type_arr,"", 0, "", $selected,"","", "" );
                                    ?>
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
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$('#cbo_approval_type').val(0);
</script>
</html>
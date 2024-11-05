<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Pre Costing Approval
Functionality	:	
JS Functions	:
Created by		:	Ashraful
Creation date 	: 	14-10-2019
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
$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "page_id=$menu_id and is_deleted=0" );
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
	
		var previous_approved=0;
		if($('#previous_approved').is(":checked")) previous_approved=1;
		
		var data="action=report_generate&previous_approved="+previous_approved+get_submitted_data_string('cbo_buyer_name*txt_costshit_no*txt_style_ref*cbo_get_upto*txt_date*cbo_approval_type*cbo_year*txt_alter_user_id',"../");
		
		http.open("POST","requires/quick_costing_approval_controller.php",true);
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
		
	function submit_approved(total_tr,type)
	{ 
		//var operation=4;
		var booking_nos = "";  var booking_ids = ""; var approval_ids = "";  var confirm_ids="";
		freeze_window(0);
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

		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
				var styleavlmin =$("#booking_no_"+i).attr('styleavlmin')*1;
				var balanmin=$('#tdbalanmin_'+i).text()*1;
					
				if($('#cbo_approval_type').val()==2)
				{
					if((styleavlmin*1)==0)
					{
						alert('Please Margin Entry First.');
						release_freezing();
						return;
					}
					
					if((styleavlmin*1)>(balanmin*1))
					{
						alert('Style avl min is more than Balance Min.');	
						release_freezing();
						return;
					}
				}
				
				booking_id = $('#booking_id_'+i).val();
				confirm_id = $('#confirm_id_'+i).val();
				if(booking_ids=="") booking_ids= booking_id; else booking_ids +=','+booking_id;
				if(confirm_ids=="") confirm_ids= confirm_id; else confirm_ids +=','+confirm_id;
				
				booking_no = $('#booking_no_'+i).val();
				if(booking_nos=="") booking_nos=booking_no; else booking_nos +=","+booking_no;
				
				approval_id = parseInt($('#approval_id_'+i).val());
				if(approval_id>0)
				{
					if(approval_ids=="") approval_ids= approval_id; else approval_ids +=','+approval_id;
				}
			}
		}
		
		if(booking_nos=="")
		{
			alert("Please Select At Least One Style Reff.");
			release_freezing();
			return;
		}
		
		var data="action=approve&operation="+operation+'&approval_type='+type+'&booking_nos='+booking_nos+'&booking_ids='+booking_ids+'&approval_ids='+approval_ids+'&confirm_ids='+confirm_ids+get_submitted_data_string('txt_alter_user_id',"../");
	   //alert(data);
		
		http.open("POST","requires/quick_costing_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_purchase_requisition_approval_Reply_info;
	}	
	
	function fnc_purchase_requisition_approval_Reply_info()
	{
		if(http.readyState == 4) 
		{ 
			var reponse=trim(http.responseText).split('**');	
			show_msg(reponse[0]);
			if((reponse[0]==19 || reponse[0]==20))
			{
				fnc_remove_tr();
			}			
			//$('#txt_bar_code').val('');
			//$('#txt_bar_code').focus();
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
	
	function openImgFile(id,action)
	{
		var page_link='requires/quick_costing_approval_controller.php?action='+action+'&id='+id;
		if(action=='img') var title='Image View'; else var title='File View';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','');
	}

	//barcode scan
	$('#txt_bar_code').live('keydown', function(e) {
		if  (e.keyCode === 13) 
		{
			//alert("su..re"); return;
			e.preventDefault();
			var bar_code=$('#txt_bar_code').val();
			check_on_scan(bar_code);
		}
	});
	
	function fnc_load_cm_compulsory(data)
	{
		$('#txt_cm_compulsory').val('');
		var cm_compulsory = return_global_ajax_value( data, 'populate_cm_compulsory', '', 'requires/quick_costing_approval_controller');
		$('#txt_cm_compulsory').val(cm_compulsory);
	}
	
	function change_user()
	{
	
		var title = 'Alter User Info';	
		var page_link = 'requires/quick_costing_approval_controller.php?action=user_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var data=this.contentDoc.getElementById("selected_id").value; //Access form field with id="emailfield"
			
			var data_arr=data.split("_");
			$("#txt_alter_user_id").val(data_arr[0]);
			$("#txt_alter_user").val(data_arr[1]);
			$("#cbo_approval_type").val(2);
			$("#report_container").html('');
		}
	}
	
	function change_approval_type(value)
	{
		if(value==0)
		{
			$("#previous_approved").val(1);
			$("#cbo_approval_type").val(1);
			$("#cbo_approval_type").attr("disabled",true);	
		}
		else
		{
			$("#previous_approved").val(0);
			$("#cbo_approval_type").val(2);
			$("#cbo_approval_type").attr("disabled",false);
		}		
	}
	
	function fnc_print_report(qc_no,cost_sheet_no,action)
	{
		var report_title=$( "div.form_caption" ).html();
		var data=qc_no+'*'+cost_sheet_no+'*'+report_title;
		window.open("../order/spot_costing/requires/quick_costing_controller.php?data=" + data+'&action='+action, true );
		return;
	}

	function fnc_confirm_style(qc_no,update_id)
	{
		var data=qc_no+'__'+update_id;
		var page_link='requires/quick_costing_approval_controller.php?action=confirmStyle_popup';
		var title="Confirm Style Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&data='+data, title, 'width=950px,height=450px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
	
		}
	}
	
	function fn_capAllPop()
	{
		page_link='../order/woven_order/sales_target.php?action=daily_task_entry';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Detail View', 'width=1050px, height=450px, center=1, resize=0, scrolling=0','../');
		emailwindow.onclose=function()
		{
			var menu_info=this.contentDoc.getElementById("txt_menu_info").value; //alert(menu_info);
			var dataArr=menu_info.split('**');
			var dataStr="'"+dataArr.join("','")+"'";
			//localStorage['visited'] = menu_info;
			
			window.location.href = window.location.origin+"/platform-v3.5/index.php?module_id="+dataArr[5];
			//callurl.load(dataArr[0],dataArr[1],dataArr[2],'','');
		}
	}

	function fnc_costing_details(qc_no,buyer,costing_date,ex_rate,offer_qty,action)
    {
        //alert(buyer)
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/quick_costing_approval_controller.php?qc_no='+qc_no+'&buyer='+buyer+'&costing_date='+costing_date+'&ex_rate='+ex_rate+'&offer_qty='+offer_qty+'&action='+action,'Costing Popup', 'width=958px,height=450px,center=1,resize=0','../');
        emailwindow.onclose=function()
        {
            
        }
    }
</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",''); ?>
		 <form name="requisitionApproval_1" id="requisitionApproval_1"> 
         <h3 style="width:1000px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel"> 
             
             <fieldset style="width:1000px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                        	<tr> 
                            	<th colspan="3" id="allcation"><input type="button" value="Sales Forecast" name="show" id="show" class="formbutton" style="width:120px" onClick="fn_capAllPop();"/></th>
                                <th colspan="2" align="center">
                                <?php 
									$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
									if( $user_lavel==2)
									{
										?>
                                		Previous Approved: <input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes"  value="0"  onChange="change_approval_type(this.value)" />

                                <?php
									}
									else
									{
								?>
                                		<input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes" style="display:none"  />
                                <?php	
									}
								?> 
                                 
                                 </th>
                                <th colspan="3">
                                <?php 
									$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
									if( $user_lavel==2)
									{
								?>
                                		<!--<input type="button" class="image_uploader" style="width:100px" value="CHANGE USER" onClick="change_user()">-->
                                        Alter User:
                                        <input type="text" id="txt_alter_user" name="txt_alter_user" class="text_boxes"  onDblClick="change_user()"/ placeholder="Browse " style="width:200px" readonly>
                                <?php 
									}
									
								?>
                                
                                <input type="hidden" id="txt_alter_user_id" name="txt_alter_user_id" /> 
                                </th>
                            </tr>
                            <tr>
                                <th>Buyer</th>
                                <th>Year</th>
                                <th>Style Ref.</th>
                                <th>Cost Sheet No</th>
                                <th>Get Upto</th>
                                <th>Costing  Date</th>
                                <th>Approval Type</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" /> <input style="width:50px;" type="hidden" name="txt_cm_compulsory" id="txt_cm_compulsory"/></th>
                        	</tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                              
          
                                <td id="buyer_td_id"> 
									<?
                                       echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );
										
                                    ?>
                                </td>
                                <td> <? echo create_drop_down( "cbo_year", 130, $year,"", 1, "-- Select --", 0, "" ); ?></td>
                          		<td><input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:90px"></td>
                                <td><input name="txt_costshit_no" id="txt_costshit_no" class="text_boxes" style="width:100px"></td>
                                <td> 
									<?
										$get_upto=array(1=>"After This Date",2=>"As On Today",3=>"This Date");
                                       echo create_drop_down( "cbo_get_upto", 130, $get_upto,"", 1, "-- Select --", 0, "" );
                                    ?>
                                </td>
                                <td><input type="text" name="txt_date" id="txt_date" class="datepicker" readonly style="width:70px"/></td>
                                <td> 
                                    <?
									  $pre_cost_approval_type=array(2=>"Un-Approved",1=>"Approved");
                                        echo create_drop_down( "cbo_approval_type", 130, $pre_cost_approval_type,"", 0, "", $selected,"","", "" );
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
    <div id="report_container2" align="center"></div>
    <div id="gsd_entry"><?php include('../../library/merchandising_details/capacity_allocation.php'); ?></div> 
    
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>$('#cbo_approval_type').val(0);</script>
</html>
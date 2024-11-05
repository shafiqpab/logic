<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Price Quotation Approval
Functionality	:
JS Functions	:
Created by		:	Fuad Shahriar
Creation date 	: 	22-12-2013
Updated by 		: 	zakaria joy
Update date		: 	13-05-2018
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

	function openmypage_refusing_cause(page_link,title,quo_id)
	{
		var page_link = page_link + "&quo_id="+quo_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=280px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var cause=this.contentDoc.getElementById("txt_refusing_cause").value;
			if (cause!="")
			{
				fn_report_generated();
			}
		}
	 }
	 
	function fn_report_generated()
	{      //alert(); Fa
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

		var previous_approved=0;
		if($('#previous_approved').is(":checked")) previous_approved=1;
		var unapproved_request=0;
		if($('#unapproved_request').is(":checked")) unapproved_request=1;

		var data="action=report_generate&previous_approved="+previous_approved+"&unapproved_request="+unapproved_request+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_get_upto*txt_date*cbo_approval_type*txt_alter_user_id*txt_quotation_no*txt_mkt_no',"../");
		
		http.open("POST","requires/price_quatation_approval_controller.php",true);
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

	function submit_approved(total_tr,type)
	{
		//var operation=4;
		var booking_nos = "";  var booking_ids = ""; var approval_ids = ""; var unapprove_reasons ="" ;
		freeze_window(0);
		// Confirm Message  *********************************************************************************************************
		if($('#cbo_approval_type').val()==1)
		{
			if($("#all_check").is(":checked"))
			{
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
				if(booking_ids=="") booking_ids= booking_id; else booking_ids +=','+booking_id;

				booking_no = $('#booking_no_'+i).val();
				if(booking_nos=="") booking_nos="'"+booking_no+"'"; else booking_nos +=",'"+booking_no+"'";

				approval_id = parseInt($('#approval_id_'+i).val());
				if(approval_id>0)
				{
					if(approval_ids=="") approval_ids= approval_id; else approval_ids +=','+approval_id;
				}

				if(type==1)
				{
					if($('#unapprove_reason_'+i).val() != ""){
						unapprove_reason = $('#unapprove_reason_'+i).val();
						if(unapprove_reasons=="") unapprove_reasons="'"+unapprove_reason+"'"; else unapprove_reasons +=",'"+unapprove_reason+"'";
					}
					else
					{
						alert("Please write unapproved reasaon.");
						$('#unapprove_reason_'+i).css({'border-color':'red'});
						$('#unapprove_reason_'+i).focus();
						release_freezing();
						return;
					}
				}
			}
		}

		if(booking_nos=="")
		{
			alert("Please Select At Least One Booking");
			release_freezing();
			return;
		}

		var data="action=approve&operation="+operation+'&approval_type='+type+'&booking_nos='+booking_nos+'&unapprove_reasons='+unapprove_reasons+'&booking_ids='+booking_ids+'&approval_ids='+approval_ids+get_submitted_data_string('cbo_company_name*txt_alter_user_id',"../");

		

		http.open("POST","requires/price_quatation_approval_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_purchase_requisition_approval_Reply_info;
	}

	function fnc_purchase_requisition_approval_Reply_info()
	{
		if(http.readyState == 4)
		{
			//release_freezing();	return;
			var reponse=trim(http.responseText).split('**');
			show_msg(reponse[0]);
			if((reponse[0]==19 || reponse[0]==20))
			{
				fnc_remove_tr();
			}
			else if(reponse[0]==21)
			{
				release_freezing();
				alert("Ready to approved No not allow when submit to approved");
			}
			else if(reponse[0]==23)
			{
				release_freezing();
				alert("Pre-costing approval found below job ["+reponse[1]+'], Please unapproved first.');
			}
			else if(reponse[0]==16){alert('Quatation id ['+reponse[1]+'] is approved by pre-cost;');}
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

	function generate_worder_report(type,quatation_id,company_id,buyer_id,style_ref,quot_date,gmt_nature)
	{

		if(type=='summary2' || type=='lc_cost_details')
		{
			var report_title="Budget/Cost Sheet";
			//var comments_head=0;

			if(type=='summary2')
			{
				var rpt_type=5;var comments_head=0;
			}
			else if(type=='lc_cost_details')
			{
				var rpt_type=6;var comments_head=1;
			}




			var txt_style_ref_id=$('#hidd_job_id').val();

			var txt_order=""; var txt_order_id="";  var txt_season_id=""; var txt_season=""; var txt_file_no="";
			var data="action=report_generate&reporttype="+rpt_type+
			'&cbo_company_name='+"'"+company_id+"'"+
			'&cbo_buyer_name='+"'"+buyer_id+"'"+
			'&txt_style_ref='+"'"+style_ref+"'"+
			'&txt_style_ref_id='+"'"+txt_style_ref_id+"'"+
			'&txt_order='+"'"+txt_order+"'"+
			'&txt_order_id='+"'"+txt_order_id+"'"+
			'&txt_season='+"'"+txt_season+"'"+

			'&txt_season_id='+"'"+txt_season_id+"'"+
			'&txt_file_no='+"'"+txt_file_no+"'"+
			'&txt_quotation_id='+"'"+quatation_id+"'"+
			'&txt_hidden_quot_id='+"'"+quatation_id+"'"+
			'&comments_head='+"'"+comments_head+"'"+
			'&report_title='+"'"+report_title+"'"+
			'&path=../../../';

			http.open("POST","../reports/management_report/merchandising_report/requires/cost_breakup_report2_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_fabric_report_reponse;

		}else{
			var data="action=generate_report&type="+type+
						'&txt_quotation_id='+"'"+quatation_id+"'"+
						'&cbo_company_name='+"'"+company_id+"'"+
						'&cbo_buyer_name='+"'"+buyer_id+"'"+
						'&txt_style_ref='+"'"+style_ref+"'"+
						'&txt_quotation_date='+"'"+quot_date+"'"+
						'&path='+'../';
						freeze_window(3);
						if(gmt_nature==2) // Knit (woven_order) type == 'preCostRpt4' &&
						{
							//alert('Knit');
							http.open("POST","../order/woven_order/requires/quotation_entry_controller.php",true);
							http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
							http.send(data);
							http.onreadystatechange = generate_fabric_report_reponse;
						}
						if(gmt_nature==3) // Woven (woven_gmts) type == 'preCostRpt4' &&
						{
							//alert('Woven');
							http.open("POST","../order/woven_gmts/requires/quotation_entry_controller.php",true);
							http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
							http.send(data);
							http.onreadystatechange = generate_fabric_report_reponse;
						}
					/*	else
						{
							http.open("POST","../order/woven_order/requires/quotation_entry_controller.php",true);
							http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
							http.send(data);
							http.onreadystatechange = generate_fabric_report_reponse;
						}*/
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
			release_freezing();
		}
	}

	function openImgFile(id,action)
	{
		var page_link='requires/price_quatation_approval_controller.php?action='+action+'&id='+id;
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


	function change_user()
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		var title = 'Approval user list';
		var page_link = 'requires/price_quatation_approval_controller.php?action=user_popup'+get_submitted_data_string('cbo_company_name',"../");

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0','');

		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var data=this.contentDoc.getElementById("selected_id").value; //Access form field with id="emailfield"

			var data_arr=data.split("_");
			$("#txt_alter_user_id").val(data_arr[0]);
			$("#txt_alter_user").val(data_arr[1]);
			load_drop_down( 'requires/price_quatation_approval_controller',$("#cbo_company_name").val()+"_"+data_arr[0], 'load_drop_down_buyer_new_user', 'buyer_td_id' );
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
			$("#cbo_approval_type").val(0);
			$("#cbo_approval_type").attr("disabled",false);
		}
	}
	function change_unapproved_type(value)
	{
		if(value==0)
		{
			$("#unapproved_request").val(1);
			$("#cbo_approval_type").val(1);
			$("#cbo_approval_type").attr("disabled",true);
		}
		else
		{
			$("#unapproved_request").val(0);
			$("#cbo_approval_type").val(0);
			$("#cbo_approval_type").attr("disabled",false);
		}
	}

	// Un-approved Reason copy_value
    function copy_value(value,field_id,i)
    {
      var rowCount = $('#tbl_list_search tr').length-1;
      var is_checked = $('#copy_basis').is(':checked');
      for(var j=i; j<=rowCount; j++)
      {
        if(field_id=='unapprove_reason_')
        {
          if(is_checked==true)
          {
            document.getElementById(field_id+j).value=value;
          }
        }
      }
    }
    // Un-approved Reason copy_value
</script>
</head>

<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",''); ?>
		 <form name="requisitionApproval_1" id="requisitionApproval_1">
         <h3 style="width:960px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
         <div id="content_search_panel">
             <fieldset style="width:960px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                        	<tr>
                            	<th colspan="2" align="right">Barcode Scan: <input type="text" id="txt_bar_code" name="txt_bar_code" class="text_boxes"  /></th>
                            	<th align="right" colspan="2">Unapproved Request: <input type="checkbox" id="unapproved_request" name="unapproved_request" class="text_boxes"  value="0" onChange="change_unapproved_type(this.value)"/></th>
                                <th colspan="2" align="center">

                                <?php
									$user_lavel = return_field_value("user_level", "user_passwd", "id=" . $_SESSION['logic_erp']['user_id'] . "");
									if ($user_lavel == 2) 
									{
										?>
                                		Previous Approved: <input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes"  value="0"  onChange="change_approval_type(this.value)"/>
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
                                <th colspan="4">
                                <?php
								if ($user_lavel == 2)
								{
									?>
                                        Alter User:
                                        <input type="text" id="txt_alter_user" name="txt_alter_user" class="text_boxes"  onDblClick="change_user()"/ placeholder="Browse " readonly>
                                		<?php
								}

								?>

                                <input type="hidden" id="txt_alter_user_id" name="txt_alter_user_id" />
                                </th>
                            </tr>
                            <tr>
                                <th class="must_entry_caption">Company Name</th>
                                <th>Buyer</th>
                                <th>Quotation No</th>
                                <th>Mkt.No</th>
                                <th>Get Upto</th>
                                <th>Quotation Date</th>
                                <th>Approval Type</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                        	</tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td>
                                    <?
                                        echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/price_quatation_approval_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );" );
                                    ?>
                                </td>
                                <td id="buyer_td_id">
									<?
                                       echo create_drop_down( "cbo_buyer_name", 150, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
                                    ?>
                                </td>
                                <td>
                                	<input type="text" name="txt_quotation_no" id="txt_quotation_no" class="text_boxes" style="width:80px"/>
                                </td>
                                <td>
                                	<input type="text" name="txt_mkt_no" id="txt_mkt_no" class="text_boxes" style="width:80px"/>
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
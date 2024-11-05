<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create Price Quotation Approval Group By
Functionality	:			
JS Functions	:
Created by		:	Md. Saidul Islam Reza 
Creation date 	: 	27-08-2023
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
echo load_html_head_contents("Fabric Booking Approval Group By", "../", 1, 1,'','','');

$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "entry_form=10 and is_deleted=0" );

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

		if($('#cbo_approval_type').val() == 1){
			if (form_validation('cbo_buyer_name','Buyer Name')==false && form_validation('txt_price_quotation_id','Price Quotation')==false && form_validation('txt_mkt_no','MKT No')==false){
				if (form_validation('txt_date_from*txt_date_to','Quotation From Date*Quotation To Date')==false)
				{
					release_freezing();
					return;
				}
				else{
					$("#cbo_buyer_name").css({ 'background-image': '' });
					$("#txt_price_quotation_id").css({ 'background-image': '' });
					$("#txt_mkt_no").css({ 'background-image': '' });
				}	
			}
		}
		
		var previous_approved=0;
		if($('#previous_approved').is(":checked")) previous_approved=1;
		
		var data="action=report_generate&previous_approved="+previous_approved+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to*txt_team_member*txt_price_quotation_id*cbo_approval_type*txt_alter_user_id*txt_mkt_no',"../");
		
		http.open("POST","requires/price_quatation_approval_group_by_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = () =>{
            if(http.readyState == 4) 
		    {
                var response=trim(http.responseText).split("####");
                $('#report_container').html(response[0]);
                
                var tableFilters = { col_0: "none" }
                setFilterGrid("tbl_list_search",-1,tableFilters);
                    
                show_msg('3');
                release_freezing();
            }
        };
	}
	

	
	function check_all(tot_check_box_id)
	{
        $('#tbl_list_search tbody tr').each(function() {
            $('#tbl_list_search tbody tr input:checkbox').attr('checked', $('#'+tot_check_box_id).is(":checked"));
        });
	}
	

	
		
	function submit_approved(total_tr,type)
	{ 
		//var operation=4;
		var unapprove_reasons ="" ;
		freeze_window(0);
		// Confirm Message  *****************************************************************************************
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
	
		// Confirm Message finish *********************************************************************************
		
		var booking_id_arr = Array(); var booking_no_arr = Array(); var approval_id_arr = Array(); var unapprove_reason_arr = Array();
		for(i=1; i<total_tr; i++)
		{
			if ($('#tbl_'+i).is(":checked"))
			{
	
                booking_id_arr.push($('#booking_id_'+i).val());
                booking_no_arr.push($('#booking_no_'+i).val());
                approval_id_arr.push(parseInt($('#approval_id_'+i).val()));

				if(type==1)
				{
					if($('#unapprove_reason_'+i).val() != ""){
                        unapprove_reason_arr.push($('#unapprove_reason_'+i).val());
					}
					else
					{
						release_freezing();
                        alert("Please write unapproved reasaon.");
						$('#unapprove_reason_'+i).focus().css({'border-color':'red'});
						return;
					}
				}
			}
		}


		if(type  == 5){
			var flag = 1; 
			booking_id_arr.forEach((quo_id) => {
				if(document.querySelector('#txtCause_'+quo_id).value == ''){
					document.querySelector('#txtCause_'+quo_id).style.backgroundColor ="red";
					flag = 0;
				}
			});
			
			if(flag == 0) { 
				alert("Please write denay reasaon.");
				release_freezing();	
				return false;
			}
		}


        var booking_ids = booking_id_arr.join(','); 
        var booking_nos = booking_no_arr.join("','");  
        var approval_ids = approval_id_arr.join(',');
        var unapprove_reasons = unapprove_reason_arr.join("','");  
		
		if(booking_nos=="")
		{
			alert("Please Select At Least One Booking");
			release_freezing();
			return;
		}
		
		var data="action=approve&operation="+operation+'&approval_type='+type+"&booking_nos='"+booking_nos+"'&unapprove_reasons='"+unapprove_reasons+"'&booking_ids="+booking_ids+'&approval_ids='+approval_ids+get_submitted_data_string('cbo_company_name*txt_alter_user_id',"../");
		
		http.open("POST","requires/price_quatation_approval_group_by_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = () =>{
            if(http.readyState == 4) 
            { 
                //release_freezing();	return;
                var reponse=trim(http.responseText).split('**');	
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
	
	function generate_worder_report(dataStr)
	{
		var [type,quatation_id,company_id,buyer_id,style_ref,quot_date] = dataStr.split('**');
		
		var data="action=generate_report&type="+type+ '&txt_quotation_id='+"'"+quatation_id+"'"+ '&cbo_company_name='+"'"+company_id+"'"+ '&cbo_buyer_name='+"'"+buyer_id+"'"+ '&txt_style_ref='+"'"+style_ref+"'"+ '&txt_quotation_date='+"'"+quot_date+"'"+'&path=../../'+"&zero_value=0";
		$path_location = "../order/woven_order/requires/quotation_entry_controller.php";
     
        http.open("POST",$path_location,true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = () =>{
            if(http.readyState == 4) 
            {
                var w = window.open("Surprise", "_blank");
                var d = w.document.open();
                d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
                d.close();
            }           
        }
        
	}
		

	function openImgFile(id,action)
	{
		var page_link='requires/price_quatation_approval_group_by_controller.php?action='+action+'&id='+id;
		if(action=='img') var title='Image View'; else var title='File View';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','');
	}
	

	
	function change_user()
	{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			return;
		}
		var title = 'Approval user list';	
		var page_link = 'requires/price_quatation_approval_group_by_controller.php?action=user_popup'+get_submitted_data_string('cbo_company_name',"../");
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=390px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var data=this.contentDoc.getElementById("selected_id").value; //Access form field with id="emailfield"
			
			var data_arr=data.split("_");
			$("#txt_alter_user_id").val(data_arr[0]);
			$("#txt_alter_user").val(data_arr[1]);
			// load_drop_down( 'requires/price_quatation_approval_group_by_controller',$("#cbo_company_name").val(), 'load_drop_down_buyer', 'buyer_td_id' );
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

    	//barcode scan
	$('#txt_bar_code').live('keydown', function(e) {
		if  (e.keyCode === 13) 
		{
			e.preventDefault();
			var bar_code=$('#txt_bar_code').val();
			check_on_scan(bar_code);
		}
	});


	function openmypage_refusing_cause(page_link,title,quo_id)
	{
		var page_link = page_link + "&quo_id="+quo_id + "&refusing_cause="+document.querySelector('#txtCause_'+quo_id).value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=280px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var cause=this.contentDoc.getElementById("txt_refusing_cause").value;
			if (cause!="")
			{
				document.querySelector('#txtCause_'+quo_id).value = cause;
				//fn_report_generated();
			}
		}
	}

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
         <h3 style="width:1180px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
             <fieldset style="width:1180px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                        	<tr> 
                            	<th colspan="2" align="right">Barcode Scan: <input type="text" id="txt_bar_code" name="txt_bar_code" class="text_boxes"  /></th>
                                <th colspan="3" align="center">
                                
	                                <?php 
										$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
										if( $user_lavel==2)
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
										if( $user_lavel==2)
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
                                <th>Price Quotation ID</th>
								<th>Mkt.No</th>
                                <th colspan="2">Quotation Date</th>
                                <th>Team Member</th>
                                <th>Approval Type</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                        	</tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/price_quatation_approval_group_by_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );" );
                                    ?>
                                </td>
                                <td id="buyer_td_id"> 
									<?
                                       //echo create_drop_down( "cbo_buyer_name", 152, $blank_array,"", 1, "-- All Buyer --", 0, "" );
                                       echo create_drop_down( "cbo_buyer_name", 150, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" ); 
                                    ?>
                                </td>
                                <td><input type="text" name="txt_price_quotation_id" placeholder="Write" id="txt_price_quotation_id" class="text_boxes" style="width:100px"/></td>
								<td>
                                	<input type="text" name="txt_mkt_no" id="txt_mkt_no" class="text_boxes" style="width:80px"/>
                                </td>
                                <td><input type="text" placeholder="Select From Date" name="txt_date_from" id="txt_date_from" class="datepicker" readonly style="width:80px"/></td>
                                <td><input type="text" placeholder="Select To Date" name="txt_date_to" id="txt_date_to" class="datepicker" readonly style="width:80px"/></td>
                                
                                <td id="team_member_td"> 
									<?
                                       //echo create_drop_down( "cbo_buyer_name", 152, $blank_array,"", 1, "-- All Buyer --", 0, "" );
                                       // echo create_drop_down( "cbo_team_member", 150, "SELECT id ,team_member  from wo_price_quotation_v3_mst  where status_active =1 and is_deleted=0 order by team_member","id,team_member", 1, "-- Select Buyer --", 0, "" ); 
                                    ?>
                                    <input type="text" name="txt_team_member" class="text_boxes" id="txt_team_member" placeholder="Write">
                                </td>
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_approval_type", 130, $approval_type_arr,"", 0, "", $selected,"","", "" );
                                    ?>
                                </td>
                                <td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:100px" onClick="fn_report_generated()"/></td>
                            </tr>
							<tr>
								<td colspan="9" align="center"><? echo load_month_buttons(1);  ?></td>
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
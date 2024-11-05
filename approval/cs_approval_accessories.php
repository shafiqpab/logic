<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Comparative Statement Accessories
Functionality	:	
JS Functions	:
Created by		:	Rakib
Creation date 	: 	28-06-2021
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
//----------------------------------------------------------------------------------------------
echo load_html_head_contents("Comparative Statements (CS) Accessories", "../", 1, 1,'','','');
$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "page_id=$menu_id and is_deleted=0" );
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<?=$permission; ?>';

	function fn_report_generated()
	{
		var approval_setup=<?=$approval_setup; ?>;
		if(approval_setup!=1)
		{
			alert("Electronic Approval Setting First.");
			release_freezing();	
			return;
		}
		var previous_approved=0;
		if ($('#previous_approved').is(":checked")) previous_approved=1;

		var data="action=report_generate&previous_approved="+previous_approved+get_submitted_data_string('cbo_item_category_id*cbo_cs_year*txt_cs_no*txt_date_from*txt_date_to*cbo_approval_type*txt_alter_user_id',"../");

		freeze_window(3);
		http.open("POST","requires/cs_approval_accessories_controller.php",true);
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
		}

	}
	
 
	function check_all(tot_check_box_id,total_tr)
	{
		if ($('#'+tot_check_box_id).is(":checked"))
		{ 
			$('#tbl_cs_list tbody tr').each(function() {
				$('#tbl_cs_list tbody tr input:checkbox').attr('checked', true);
			});
		}
		else
		{ 
			$('#tbl_cs_list tbody tr').each(function() {
				$('#tbl_cs_list tbody tr input:checkbox').attr('checked', false);
			});
			for(i=1; i<total_tr; i++)
			{
				$('#txt_supplier_id_'+i).val('');
				$('#txt_supplier_name_'+i).val('');
			}
		} 
	}

		
	function submit_approved(total_tr,type)
	{

		var supplier_ids = "";  var booking_ids = ""; var approval_ids = "";  var booking_nos="";
		var booking_dtlsids="";
		freeze_window(0);	
		
		var data_supplier="";
		var data_company="";
		var data_dtlsid="";
		data_all="";
		var data_supplier_approval_rate="";
		var data_company_approval_rate="";
		var sl_wise_item_row="";
		var sl_str="";

		$('input[name="tbl[]"]:checked').each(function()
		{
			
			var i=$(this).attr("value");
			if (sl_str=="") sl_str= i;
			else sl_str +=","+i;

			var item_row=$(".item_class_"+i).length;
			for(j=1; j<=item_row; j++)
			{
				if (type !=1)
				{
					if (form_validation('txt_supp_comp_name_'+i+'_'+j,'Supplier Name')==false)
					{
						release_freezing();
						return;
					}
				}				

				var booking_id = $('#booking_id_'+i+'_'+j).val();
				if (booking_ids=="") booking_ids= booking_id; 
				else booking_ids +=','+booking_id;

				var booking_no = $('#booking_no_'+i+'_'+j).val();
				if (booking_nos=="") booking_nos=booking_no; 
				else booking_nos +=","+booking_no;				

				var booking_dtlsid = $('#booking_dtlsid_'+i+'_'+j).val();
				if (booking_dtlsids=="") booking_dtlsids=booking_dtlsid; 
				else booking_dtlsids +=","+booking_dtlsid;

				var supplier_id = $('#txt_supp_id_'+i+'_'+j).val();
				if (supplier_id=="") supplier_ids=supplier_id; 
				else supplier_ids +=","+supplier_id;

				var approval_id = parseInt($('#approval_id_'+i+'_'+j).val());
				if(approval_id>0)
				{
					if (approval_ids=="") approval_ids= approval_id; 
					else approval_ids +=','+approval_id;
				}

				data_supplier += '&supplier_id_'+ i+'_'+j + '=' + $('#supplier_id_'+i+'_'+j).val();
				data_company += '&company_id_' +i+'_'+j + '=' + $('#company_id_'+i+'_'+j).val();
				data_dtlsid += '&booking_dtlsid_'+ i+'_'+j + '=' + $('#booking_dtlsid_'+i+'_'+j).val();

				var supplier_ID = $('#supplier_id_'+i+'_'+j).val();
        		var supp_num_arr = supplier_ID.split(',');
        		var supp_num = supp_num_arr.length; 
		        var company_name_id = $('#company_id_'+i+'_'+j).val();
		        var company_num_arr = company_name_id.split(',');
		        if (company_name_id !='') {var company_num = company_num_arr.length}else{var company_num =0;}		        

	            for (var m=0; m<supp_num; m++)
	            {
	                var mm=supp_num_arr[m];
	                data_supplier_approval_rate += '&txtApprovedPriceSpplier_'+ i+'_'+j+'_'+ mm + '=' + $('#txtApprovedPriceSpplier_'+i+'_'+j+'_'+mm).val();
	            }

	            for (var m=0; m<company_num; m++)
	            {
	                var mm=company_num_arr[m];
	                data_company_approval_rate  += '&txtApprovedPriceCompany_'+ i+'_'+j+'_' + mm + '=' + $('#txtApprovedPriceCompany_'+i+'_'+j+'_'+mm).val();
	            }
				
	        

	            data_all += '&item_category_id_'+i+'_'+j+ '=' + $('#item_category_id_'+i+'_'+j).attr('title') + '&item_group_id_'+i+'_'+j+ '=' + $('#item_group_id_'+i+'_'+j).attr('title') + '&item_ref_'+i+'_'+j+ '=' + encodeURIComponent($('#item_ref_'+i+'_'+j).attr('title')) + '&item_description_'+i+'_'+j+ '=' + encodeURIComponent($('#item_description_'+i+'_'+j).attr('title')) + '&uom_'+i+'_'+j+ '=' + $('#uom_'+i+'_'+j).attr('title') + '&txt_supp_id_'+i+'_'+j+ '=' + $('#txt_supp_id_'+i+'_'+j).val() + '&txt_comp_id_'+i+'_'+j+ '=' + $('#txt_comp_id_'+i+'_'+j).val();

			}

			sl_wise_item_row += '&sl_wise_item_row_'+i + '=' + item_row;
		});

		if (type !=1)
		{
			if(supplier_ids=="")
			{
				alert("Please Select At Least One Check Mark");
				release_freezing();
				return;
			}
		}	
		
		var data="action=approve&approval_type="+type+'&sl_str='+sl_str+'&booking_ids='+booking_ids+'&booking_nos='+booking_nos+'&booking_dtlsids='+booking_dtlsids+'&approval_ids='+approval_ids+'&data_dtlsid='+data_dtlsid+'&data_supplier='+data_supplier+'&data_company='+data_company+"&data_supplier_approval_rate="+data_supplier_approval_rate+"&data_company_approval_rate="+data_company_approval_rate+"&data_all="+data_all+sl_wise_item_row+get_submitted_data_string('txt_alter_user_id',"../");
		//alert(data);return;
		http.open("POST","requires/cs_approval_accessories_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_cs_approval_Reply_info;
	}	
	
	function fnc_cs_approval_Reply_info()
	{
		if(http.readyState == 4) 
		{ 
			var reponse=trim(http.responseText).split('**');	
			//show_msg(reponse[0]);
			if((reponse[0]==19 || reponse[0]==20))
			{
				var previous_approved=0;
				if ($('#previous_approved').is(":checked")) previous_approved=1;
				var cbo_item_category_id=$('#cbo_item_category_id').val();
				var cbo_cs_year=$('#cbo_cs_year').val();
				var txt_cs_no=$('#txt_cs_no').val();
				var txt_date_from=$('#txt_date_from').val();
				var txt_date_to=$('#txt_date_to').val();
				var cbo_approval_type=$('#cbo_approval_type').val();
				var txt_alter_user_id=$('#txt_alter_user_id').val();

				var all_cs_ids=reponse[1];
				show_list_view(cbo_item_category_id+"**"+cbo_cs_year+"**"+txt_cs_no+"**"+txt_date_from+"**"+txt_date_to+"**"+cbo_approval_type+"**"+txt_alter_user_id+"**"+all_cs_ids,'report_generate_after_approve_unapprove','report_container','requires/cs_approval_accessories_controller','');
				show_msg(reponse[0]);
			}				
			release_freezing();	
		}
		release_freezing();
	}

	function change_user()
	{
		var title = 'CS Approval Accessories Info';	
		var page_link = 'requires/cs_approval_accessories_controller.php?action=user_popup';
		  
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

</script>
</head>
<body>
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",''); ?>
		 <form name="csApproval_1" id="csApproval_1"> 
         <h3 style="width:1000px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel"> 
             
             <fieldset style="width:1000px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead> 
                        	<tr> 
                                <th colspan="4" align="center">
                                <?
								$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
								if( $user_lavel==2)
								{
									?><span style="vertical-align: 5px;">Previous Approved:&nbsp;</span><span><input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes"  value="0"  onChange="change_approval_type(this.value);" /></span>
									<?
								}
								else
								{
									?>
									<input type="checkbox" id="previous_approved" name="previous_approved" class="text_boxes" style="display:none" />
									<?
								}
								?> 
                                 </th>
                                <th colspan="3">
                                <?
								$user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
								if( $user_lavel==2)
								{
									?>Alter User:<input type="text" id="txt_alter_user" name="txt_alter_user" class="text_boxes"  onDblClick="change_user();"/ placeholder="Browse " style="width:200px" readonly>
									<?
								}
								?>
                                <input type="hidden" id="txt_alter_user_id" name="txt_alter_user_id" /> 
                                </th>
                            </tr>                    	
                            <tr>
                                <th>Item Category</th>
                                <th>CS Year</th>
                                <th>CS No</th>
                                <th  colspan="2">Date Range</th>
                                <th>Approval Type</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','');" class="formbutton" style="width:70px" /> <input style="width:50px;" type="hidden" name="txt_cm_compulsory" id="txt_cm_compulsory"/></th>
                        	</tr>
                        </thead>
                        <tbody>
                        	<tr class="general">                                
                                <td>
                                    <? 
                                        echo create_drop_down( "cbo_item_category_id", 160, $item_category,"", 1, "-- All Category --", $selected,"",1,4,"","","1,2,3,12,13,14");
                                    ?>
                                </td>
                                <td>
									<? echo create_drop_down( "cbo_cs_year", 110, $year, "", 1, "-- Select --", date("Y", time()), "" ); ?>
								</td>
                                <td>
									<input name="txt_cs_no" id="txt_cs_no" style="width:80px" class="text_boxes" placeholder="Write">
								</td>
                                <td>
									<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date">
								</td>
								<td>
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date">
								</td>
                                <td> 
                                    <?//create_drop_down( "cbo_add_confirm_id",165,$yes_no,'',0,'',2,0,0);
                                        //$approval_type_arr=array(1 => "Approved", 2 => "Un-Approved");
                                        //echo create_drop_down( "cbo_approval_type", 140, $approval_type_arr,"", 0, "", 2,0,0, "" );
                                       echo create_drop_down( "cbo_approval_type", 140, $approval_type_arr,"", 0, "", $selected,"","", "" );
                                    ?>
                                </td>
                                <td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:70px" onClick="fn_report_generated()"/></td>                	
                            </tr>                            
                        </tbody>
                    </table>
                </fieldset>
            </div>
		</form>
	</div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>$('#cbo_approval_type').val(0);</script>
</html>
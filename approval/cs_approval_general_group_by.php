<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Comparative Statement General
Functionality	:	
JS Functions	:
Created by		:	MD. Saidul Islam REZA
Creation date 	: 	27-06-2021
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
$user_id=$_SESSION['logic_erp']['user_id'];
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("CS Approval [General]", "../", 1, 1,'','','');
$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "entry_form=49 and is_deleted=0" );

$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd WHERE id=$user_id");
$item_cate_id = $userCredential[0][csf('item_cate_id')];
$permitted_item_category ="";
if($item_cate_id != "") $permitted_item_category=$item_cate_id;
//echo $permitted_item_category;
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

		/*var all_cs_ids="";
		if (ids == 1) {
			freeze_window(3);
			all_cs_ids="";			
		} else {
			all_cs_ids=ids;
		}*/

		var previous_approved=0;
		if ($('#previous_approved').is(":checked")) previous_approved=1;


		var data="action=report_generate&previous_approved="+previous_approved+get_submitted_data_string('cbo_item_category_id*cbo_cs_year*txt_cs_no*txt_date_from*txt_date_to*cbo_approval_type*txt_alter_user_id',"../");
		freeze_window(3);
		http.open("POST","requires/cs_approval_general_group_by_controller.php",true);
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
			/*for(i=1; i<total_tr; i++)
			{
				$('#txt_supplier_id_'+i).val('');
				$('#txt_supplier_name_'+i).val('');
			}*/
		} 
	}

		
	function submit_approved(sl, type)
	{

		var supplier_ids = "";  var booking_ids = ""; var approval_ids = "";  var booking_nos="";
		var booking_dtlsids="";
		freeze_window(0);	
		var data_supplier="";
		var data_dtlsid="";
		var data_all="";
		var sl_wise_item_row="";
		var data_supplier_approval_rate="";
		var refuse_case_arr = Array();
		//var row_num = $('input[name="tbl[]"]:checked').length;
		var sl_str="";
		//var sl="";
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
				refuse = $('#txtCause_'+booking_id).val();
				refuse_case_arr.push(refuse);

				
				
				
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

				data_supplier += '&supplier_id_'+i+'_'+j + '=' + $('#supplier_id_'+i+'_'+j).val();
				data_dtlsid += '&booking_dtlsid_'+i+'_'+j + '=' + $('#booking_dtlsid_'+i+'_'+j).val();

				var supplier_ID = $('#supplier_id_'+i+'_'+j).val();
        		var supp_num_arr = supplier_ID.split(',');
        		var supp_num = supp_num_arr.length;       

	            //var neg_validationSupplier=0;
	            //var neg_validationCompany=0;

	            for (var m=0; m<supp_num; m++)
	            {
	                var mm=supp_num_arr[m];
	                data_supplier_approval_rate += '&txtApprovedPriceSpplier_'+i+'_'+j+'_'+mm+ '=' + $('#txtApprovedPriceSpplier_'+i+'_'+j+'_'+mm).val();
	                //neg_validationSupplier += +$('#txtApprovedPriceSpplier_'+i+'_'+j+'_'+mm).val();
	            }

			
	            /*if(neg_validationSupplier==0)
				{
	                alert("Please Fill the Price");
	                release_freezing();
	                return;
	            }*/

	            data_all += '&item_category_id_'+i+'_'+j+ '=' + $('#item_category_id_'+i+'_'+j).attr('title') + '&item_group_id_'+i+'_'+j+ '=' + $('#item_group_id_'+i+'_'+j).attr('title') + '&item_code_'+i+'_'+j+ '=' + encodeURIComponent($('#item_code_'+i+'_'+j).attr('title')) + '&item_description_'+i+'_'+j+ '=' + encodeURIComponent($('#item_description_'+i+'_'+j).attr('title')) + '&uom_'+i+'_'+j+ '=' + $('#uom_'+i+'_'+j).attr('title') + '&brand_name_'+i+'_'+j+ '=' + $('#brand_name_'+i+'_'+j).val() + '&model_name_'+i+'_'+j+ '=' + $('#model_name_'+i+'_'+j).val() + '&origin_name_'+i+'_'+j+ '=' + $('#origin_name_'+i+'_'+j).val() + '&txt_supp_id_'+i+'_'+j+ '=' + $('#txt_supp_id_'+i+'_'+j).val();
				
			}
			//$.unique(booking_ids.split(','));
			sl_wise_item_row += '&sl_wise_item_row_'+i + '=' + item_row;

		});	
		//alert( booking_ids);return;
		//return;

		if (type !=1)
		{
			if(supplier_ids=="")
			{
				alert("Please Select At Least One Check Mark");
				release_freezing();
				return;
			}
		}

		if(refuse=="" && type==5){
						alert('Please Entry Refusing Cause.');
						release_freezing();	
						return;
				}
		
		
			//alert(target_ids);release_freezing();	return;
			
			$('#txt_selected_id').val(booking_ids);
			fnSendMail('../','',1,0,0,1)
			
			var refuse_case_str = refuse_case_arr.join(',');
		
		var data="action=approve&approval_type="+type+'&sl_str='+sl_str+'&booking_ids='+booking_ids+'&booking_nos='+booking_nos+'&booking_dtlsids='+booking_dtlsids+'&refuse_case_str='+refuse_case_str+'&approval_ids='+approval_ids+'&data_dtlsid='+data_dtlsid+'&data_supplier='+data_supplier+"&data_supplier_approval_rate="+data_supplier_approval_rate+"&data_all="+data_all+sl_wise_item_row+get_submitted_data_string('txt_alter_user_id',"../");
		//alert(data);return;
		http.open("POST","requires/cs_approval_general_group_by_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange=fnc_cs_approval_Reply_info;
	}	
	
	function fnc_cs_approval_Reply_info()
	{
		if(http.readyState == 4) 
		{ 
			var reponse=trim(http.responseText).split('**');			
			if((reponse[0]==19 || reponse[0]==20 ||reponse[0]==50 ))
			{
				//fn_report_generated(reponse[1]);

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
				show_list_view(cbo_item_category_id+"**"+cbo_cs_year+"**"+txt_cs_no+"**"+txt_date_from+"**"+txt_date_to+"**"+cbo_approval_type+"**"+txt_alter_user_id+"**"+all_cs_ids,'report_generate_after_approve_unapprove','report_container','requires/cs_approval_general_group_by_controller','');
				show_msg(reponse[0]);
			}				
			release_freezing();
		}
		release_freezing();
	}

	function change_user()
	{
		var title = 'CS Approval Accessories Info';	
		var page_link = 'requires/cs_approval_general_group_by_controller.php?action=user_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=715px,height=390px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
			var data=this.contentDoc.getElementById("selected_id").value; //Access form field with id="emailfield"
			
			var data_arr=data.split("_");
			$("#txt_alter_user_id").val(data_arr[0]);
			$("#txt_alter_user").val(data_arr[1]);
			load_drop_down( 'requires/cs_approval_general_group_by_controller',data_arr[0], 'load_drop_down_item_category_new_user', 'item_category_td' );
			$("#cbo_approval_type").val(2);
			$("#report_container").html('');
		}
	}

	function openmypage_refusing_cause(page_link,title,quo_id)
	 {
		var cause=document.getElementById("txtCause_"+quo_id).value;
		
		var page_link = page_link + "&quo_id="+quo_id + "&cause="+cause;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=280px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var cause=this.contentDoc.getElementById("txt_refusing_cause").value;
			document.getElementById("txtCause_"+quo_id).value=cause;
			/*if (cause!="")
			{
				fn_report_generated();
			}*/
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


	function fnc_remove_tr()
	{
		//var tot_row=$('#tbl_cs_list tbody tr').length;
		var row_num = $('input[name="tbl[]"]:checked').length;
		//alert(row_num);
		for(var i=1;i<=row_num;i++)
		{
			if($('#tbl_'+i).is(':checked'))
			{
				var item_row=$(".item_class_"+i).length;
				alert(item_row);
				for(var j=1;j<=item_row;j++)
				{					
					$('#trbandrow_'+i+'_'+j).remove();
					$('#trqutrow_'+i+'_'+j).remove();
					$('#trqutvalrow_'+i+'_'+j).remove();
				}
				$('#tr_'+i).remove();
			}
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
								
								<input type="hidden" id="txt_selected_id" name="txt_selected_id" />
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
                                <td id="item_category_td">
                                    <? 
                                        //echo create_drop_down( "cbo_item_category_id", 160, $general_item_category,"", 1, "-- All Category --", $selected,"",0,$permitted_item_category,"","","");
                                        echo create_drop_down( "cbo_item_category_id", 160, $item_category,"", 1, "-- Select Category --", $selected,"",0,$permitted_item_category,"","","1,2,3,12,13,14");
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
                                    <?
                                        echo create_drop_down( "cbo_approval_type", 140, $approval_type_arr,"", 0, "", $selected,"","", "" );
                                    ?>
                                </td>
                                <td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:70px" onClick="fn_report_generated(1)"/></td>                	
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
<script>$('#cbo_approval_type').val(0);</script>
</html>
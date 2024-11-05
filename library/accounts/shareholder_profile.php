<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create shareholder profile
Functionality	:	
JS Functions	:
Created by		:	CTO 
Creation date 	: 	09.03.2013
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
echo load_html_head_contents("Shareholder Profile", "../../", 1, 1,$unicode,'','');


?>	
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
var permission='<? echo $permission; ?>';

function search_shareholder_profile(page_link,title)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=400px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0] //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("hidden_mst_id").value;
			if (theemail!="")
			{
			freeze_window(5);
			get_php_form_data(theemail, "load_php_data_to_form", "requires/shareholder_profile_controller" );
			release_freezing();
			}
		}
}

function fnc_shareholder_profile( operation )
{
	if ( form_validation('txt_id_no*txt_name*txt_bo_ac_id','Id*Name*BO Account')==false )
	{
		return;
	}
	else
	{
	//eval(get_submitted_variables('txt_id_no*txt_name*txt_bo_ac_id*txt_father_name*txt_mother_name*txt_profession*txt_organization*txt_designation*txt_national_id*txt_tin*txt_vat*txt_email*txt_phone*cbo_status*update_id*txt_present_plot_no*txt_present_level_no*txt_present_road_no*txt_present_block_no*cbo_present_country*txt_present_province*cbo_present_state*txt_present_zip_code*txt_permanent_plot_no*txt_permanent_level_no*txt_permanent_road_no*txt_permanent_block_no*cbo_permanent_country*txt_permanent_province*cbo_permanent_state*txt_permanent_zip_code'));
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_id_no*txt_name*txt_bo_ac_id*txt_father_name*txt_mother_name*txt_profession*txt_organization*txt_designation*txt_national_id*txt_tin*txt_vat*txt_email*txt_phone*cbo_status*update_id*txt_present_plot_no*txt_present_level_no*txt_present_road_no*txt_present_block_no*cbo_present_country*txt_present_province*cbo_present_state*txt_present_zip_code*txt_permanent_plot_no*txt_permanent_level_no*txt_permanent_road_no*txt_permanent_block_no*cbo_permanent_country*txt_permanent_province*cbo_permanent_state*txt_permanent_zip_code',"../../");
		freeze_window(operation);
		http.open("POST","requires/shareholder_profile_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_shareholder_reponse;
	}
}

function fnc_shareholder_reponse()
{
	if(http.readyState == 4) 
	{
		
		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		document.getElementById('update_id').value = reponse[1];
		show_msg(reponse[0]);
		//show_list_view(reponse[1],'search_list_view','subgroup_list_view','../accounts/requires/account_group_controller','setFilterGrid("list_view",-1)');
		reset_form('shareholderprofile_1','','');
		set_button_status(0, permission, 'fnc_shareholder_profile',1);
		release_freezing();
	}
}



function fnc_accounts_group( operation )
{ 
	if(form_validation('cbocompanynameshare_1','Company Name Share')==false )
	{
		return;
	}
	else
	{
		var tot_row=$('#tbl_share_details_entry'+' tbody tr').length;
		var data="action=save_update_delete_dtl&operation="+operation +"&tot_row="+tot_row;
		var data1='';
		for(i=1; i<=tot_row; i++)
		{
			data1+=get_submitted_data_string('cbocompanynameshare_'+i+'*txtnoofshare_'+i+'*txtfacevalue_'+i+'*txtpremium_'+i+'*txtsharevalue_'+i,"../../");
		}
		var tot_row1=$('#tbl_nominee_entry'+' tbody tr').length;
		//var data2="action=save_update_delete_dtl&operation="+operation +"&tot_row1="+tot_row1;
		var data2='&tot_row1='+tot_row1;
		var data3='';
		for(i=1; i<=tot_row1; i++)
		{
		data3+=get_submitted_data_string('cbocompanynamenominee_'+i+'*txtnomineename_'+i+'*txtnomineerelation_'+i+'*txtnomineeratio_'+i+'*txtnomineeamount_'+i,"../../");
		}
		data=data+data1+data2+data3;
		freeze_window(operation);
		http.open("POST","requires/shareholder_profile_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_shareholder_group_reponse;
	}
}


function fnc_shareholder_group_reponse()
{
	if(http.readyState == 4) 
	{
		alert(http.responseText)
		var reponse=trim(http.responseText).split('**');
		if(reponse[0]==15) 
			{ 
				 setTimeout('fnc_accounts_group('+ reponse[1]+')',8000); 
			}
			else
			{
			show_msg(reponse[0]);
			//reset_form('yarncountdetermination_1','','');
			//set_button_status(0, permission, 'fnc_yarn_count_determination');
			release_freezing();
			}
	}
}



function add_share_row( i ) 
{
	var row_num=$('#tbl_share_details_entry tr').length-1;
	if (row_num!=i)
	{
		return false;
	}
	/*if (form_validation('cbogmtsitem_'+i+'*txtcolor_'+i+'*txtsize_'+i+'*txtorderquantity_'+i+'*txtorderrate_'+i+'*txtorderexcesscut_'+i+'','Company Name*Location Name*Buyer Name*Style Ref*Product Department*Item Catagory*Dealing Merchant*Packing')==false)
	{
		return;
	}*/
		i++;
		 $("#tbl_share_details_entry tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }              
			});
		  }).end().appendTo("#tbl_share_details_entry");
		  $('#increaseconversion_'+i).removeAttr("onClick").attr("onClick","add_share_row("+i+");");
		  $('#decreaseconversion_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+','+'"tbl_share_details_entry"'+");");
}



function add_nominee_row( i ) 
{	
	var row_num=$('#tbl_nominee_entry tr').length-1;
	if (row_num!=i)
	{
		return false;
	}
	/*if (form_validation('cbogmtsitem_'+i+'*txtcolor_'+i+'*txtsize_'+i+'*txtorderquantity_'+i+'*txtorderrate_'+i+'*txtorderexcesscut_'+i+'','Company Name*Location Name*Buyer Name*Style Ref*Product Department*Item Catagory*Dealing Merchant*Packing')==false)
	{
		return;
	}*/
		i++;
		 $("#tbl_nominee_entry tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }              
			});
		  }).end().appendTo("#tbl_nominee_entry");
		  $('#incrementnominee_'+i).removeAttr("onClick").attr("onClick","add_nominee_row("+i+");");
		  $('#decrementnominee_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+','+'"tbl_nominee_entry"'+");");
}


function fn_deletebreak_down_tr(rowNo,table_id ) 
{
	
		var numRow = $('#'+table_id+' tbody tr').length;
			if(numRow==rowNo && rowNo!=1)
			{
				$('#'+table_id+' tbody tr:last').remove();
			}
			else
				return false;
	
}


</script>
</head>
<body onLoad="set_hotkey()">
<div align="center" style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?>

<form name="shareholderprofile_1" id="shareholderprofile_1" autocomplete="off"> 
 	<fieldset style="width:1020px;height:auto;">
     	<legend>New Account Entry</legend>    	
        <table align="center" width="1000">
                    <tr>
                        <td>
                         <table width="1000">
                                <tr>
                                    <td width="100" class="must_entry_caption">Id No</td>
                                    <td width="150">
                                    <input type="text" id="txt_id_no" name="txt_id_no"  class="text_boxes_numeric" style="width:140px" placeholder="Double Click for Update"  ondblclick="search_shareholder_profile('requires/shareholder_profile_controller.php?action=shareholder_profile','ID Search')" />
                                    <input type="hidden" name="txt_id" id="txt_id" value="" />
                                    </td>
                                    <td width="100" class="must_entry_caption">Name</td>
                                    <td width="150"><input  type="text" id="txt_name" name="txt_name"  class="text_boxes"  style="width:140px"/></td>
                                    <td width="100" class="must_entry_caption">BO Account ID</td>
                                    <td width="150"><input  type="text" id="txt_bo_ac_id" name="txt_bo_ac_id"  class="text_boxes_numeric" style="width:140px" /></td>
                                    <td width="100">Father/Husband Name</td>
                                    <td width="150"><input  type="text" id="txt_father_name" name="txt_father_name"  class="text_boxes" style="width:140px"/></td>
                                </tr>  <!-- 01711360529 munir  -->
                                <tr>
                                    <td>Mother's Name</td>
                                    <td><input  type="text" id="txt_mother_name" name="txt_mother_name"  class="text_boxes" style="width:140px" /></td>
                                    <td>Profession</td>
                                    <td><input  type="text" id="txt_profession" name="txt_profession"  class="text_boxes" style="width:140px" /></td>
                                    <td>Organization</td>
                                    <td><input  type="text" id="txt_organization" name="txt_organization"  class="text_boxes" style="width:140px" /></td>
                                    <td>Designation</td>
                                    <td><input  type="text" id="txt_designation" name="txt_designation"  class="text_boxes" style="width:140px" /></td>
                                </tr>   
                                <tr>
                                    <td>National ID</td>
                                    <td><input  type="text" id="txt_national_id" name="txt_national_id"  class="text_boxes" style="width:140px" /></td>
                                    <td>TIN</td>
                                    <td><input  type="text" id="txt_tin" name="txt_tin"  class="text_boxes" style="width:140px" /></td>
                                    <td>VAT</td>
                                    <td><input  type="text" id="txt_vat" name="txt_vat"  class="text_boxes" style="width:140px" /></td>
                                    <td>E-mail</td>
                                    <td><input  type="email" id="txt_email" name="txt_email"  class="text_boxes" style="width:140px" /></td>
                                </tr>
                                <tr>
                                    <td>Phone</td>
                                    <td><input  type="text" id="txt_phone" name="txt_phone"  class="text_boxes" style="width:140px" /></td>
                                    <td>Status</td>
                                    <td><?php echo create_drop_down( "cbo_status", 100, $row_status,'', 0, '',1,0); ?> </td>
                                    <td><input type="hidden" name="update_id" id="update_id"></td>
                                </tr>
                                <tr height="10"></tr>
                            </table>
                        </td>
                    </tr>
    		   </table>    	
   </fieldset>
   
   
   <fieldset style="width:1000px;">
		<legend>Contact Address</legend>
        <div style="width:1020px;">
            <!--<div style="width:490px; float:left;" >-->
            <fieldset style="width:490px; float:left;">
                <legend>Present Address</legend>
                <table align="center" width="490" >
                    <tr>
                         <td width="80">Plot No</td>
                         <td width="150"><input  type="text" id="txt_present_plot_no" name="txt_present_plot_no"  class="text_boxes" style="width:140px" /></td>
                         <td width="80">Level No</td>
                         <td width="150"><input  type="text" id="txt_present_level_no" name="txt_present_level_no"  class="text_boxes" style="width:140px" /></td>
                    </tr>
                    <tr>
                         <td>Road No</td>
                         <td><input  type="text" id="txt_present_road_no" name="txt_present_road_no"  class="text_boxes" style="width:140px" /></td>
                         <td>Block #</td>
                         <td><input  type="text" id="txt_present_block_no" name="txt_present_block_no"  class="text_boxes" style="width:140px" /></td>
                    </tr>
                    <tr>
                         <td>Country</td>
                         <td width="150"> 
                         <?php 
						 echo create_drop_down( "cbo_present_country", 150,"select id,country_name from  lib_country where status_active=1 and is_deleted=0","id,country_name", 1, "-- Select --", $selected, "" );
						 ?>   
                         </td>
                         <td>Province</td>
                         <td>
                         <input  type="text" id="txt_present_province" name="txt_present_province"  class="text_boxes" style="width:140px" />
                         </td>
                    </tr>
                    <tr>
                         <td>City/ Town</td>
                         <td> <?php echo create_drop_down( "cbo_present_state", 150,$blank_array,'', 1, '',0,0,0); ?> </td>
                         <td>Zip Code</td>
                         <td><input  type="text" id="txt_present_zip_code" name="txt_present_zip_code"  class="text_boxes" style="width:140px" /></td>                 
                    </tr>
                </table>
           <!-- </div>-->
           </fieldset>
            <!--<div style="width:480px;float:right" >-->
             <fieldset style="width:490px; float:left;">
               <legend>Permanent Address</legend>
                <table align="center" width="480">
                     <tr>
                         <td width="80">Plot No</td>
                         <td width="150"><input  type="text" id="txt_permanent_plot_no" name="txt_permanent_plot_no"  class="text_boxes" style="width:140px" /></td>
                         <td width="80">Level No</td>
                         <td width="150"><input  type="text" id="txt_permanent_level_no" name="txt_permanent_level_no"  class="text_boxes" style="width:140px" /></td>
                    </tr>
                    <tr>
                         <td>Road No</td>
                         <td><input  type="text" id="txt_permanent_road_no" name="txt_permanent_road_no"  class="text_boxes" style="width:140px" /></td>
                         <td>Block #</td>
                         <td><input  type="text" id="txt_permanent_block_no" name="txt_permanent_block_no"  class="text_boxes" style="width:140px" /></td>
                    </tr>
                    <tr>
                         <td>Country</td>
                         <td> 
                         <?php 
					 	 echo create_drop_down( "cbo_permanent_country", 150, "select id,country_name from  lib_country where status_active=1 and is_deleted=0","id,country_name", 1, "-- Select --", $selected, "" );
						 ?>       
                         </td>
                         <td>Province</td>
                         <td><input  type="text" id="txt_permanent_province" name="txt_permanent_province"  class="text_boxes" style="width:140px" /></td>
                    </tr>
                    <tr>
                        <td>City/ Town</td>
                        <td><?php echo create_drop_down( "cbo_permanent_state", 150, $blank_array,"", 1, "-- Select --", $selected, "" ); ?>  </td>
                        <td>Zip Code</td>
                        <td><input  type="text" id="txt_permanent_zip_code" name="txt_permanent_zip_code"  class="text_boxes" style="width:140px" /></td>                 
                    </tr>
                </table>            
           <!-- </div>-->
           </fieldset>
            <div style="width:1000px;float:left;">
                <table align="center" width="100%">	
                    <tr style="height:10px;"></tr>
                       <tr>
                            <td colspan="6" align="center" class="button_container">
                              <? 
                              echo load_submit_buttons( $permission, "fnc_shareholder_profile", 0,0 ,"reset_form('shareholderprofile_1','','')",1);
                              ?> 
                            </td>				
                        </tr>
                </table>
            </div>
        </div>
 	</fieldset>	  
</form>
<!-----------------------------------------------------------------------------------end first form----------------------------------------->
<!-----------------------------------------------------------------------------------end first form----------------------------------------->

<form name="shareholdershare_2" id="shareholdershare_2" autocomplete="off">   
  	<fieldset style="width:1210px;height:auto;">
  		<legend>Share Details</legend>
        <div>
            <div style="width:600px;float:left;"> 
             	<legend>Share Details:</legend>
                <table align="center" width="100%"  class="rpt_table" id="tbl_share_details_entry">
                	<thead>
                    	<th width="120" class="must_entry_caption">Comapny</th>
                        <th width="100">No. of Share</th>
                        <th width="80">Face Value</th>
                        <th width="80">Premium</th>
                        <th width="100">Share Value</th>
                        <th width="100">I/D</th>
                    </thead>
                    <tbody>
                    	<td width="120">
                        <? 
						echo create_drop_down( "cbocompanynameshare_1", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond","id,company_name", 1, "-- Select Company --", $selected, "" );
						?>	 
                        </td>
                        <td width="100">
                        <input type="text" id="txtnoofshare_1" name="txtnoofshare_1" class="text_boxes" style="width:90px" />
                        </td>
                        <td width="80">
                        <input type="text" id="txtfacevalue_1" name="txtfacevalue_1" class="text_boxes"  style="width:80px"/>
                        </td>
                        <td width="80">
                        <input type="text" id="txtpremium_1" name="txtpremium_1" class="text_boxes"   style="width:80px"/>
                        </td>
                        <td width="100">
                        <input type="text" id="txtsharevalue_1" name="txtsharevalue_1" class="text_boxes" style="width:90px" />
                        </td> 
                        <td>
                        <input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="+" onClick="add_share_row(1)"/>
        <input type="button" id="decreaseconversion_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1,'tbl_share_details_entry')"/>
                        </td> 
                    </tbody> 
                </table>
            </div>
            <div style="width:600px;float:right;"> 
            	<legend>Nominee</legend>
                <table align="center" width="100%"  class="rpt_table" id="tbl_nominee_entry">
                	<thead>
                    	<th width="120">Comapny</th>
                        <th width="100">Name</th>
                        <th width="80">Relation</th>
                        <th width="80">Ratio</th>
                        <th width="100">Amount</th>
                        <th width="100">I/D</th>
                    </thead>
                    <tbody>
                    	<td>
                        <? 
						echo create_drop_down( "cbocompanynamenominee_1", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond","id,company_name", 1, "-- Select Company --", $selected, "" );
						?>	 
                        <input type="hidden" name="update_id" id="update_id" value="">
                        </td>
                        <td>
                        <input type="text" id="txtnomineename_1" name="txtnomineename_1" class="text_boxes" style="width:90px"/>
                        </td>
                        <td>
                        <input type="text" id="txtnomineerelation_1" name="txtnomineerelation_1" class="text_boxes" style="width:80px" />
                        </td>
                        <td>
                        <input type="text"  id="txtnomineeratio_1" name="txtnomineeratio_1" class="text_boxes"   style="width:80px"/>
                        </td>
                        <td>
                        <input type="text" id="txtnomineeamount_1" name="txtnomineeamount_1" class="text_boxes" style="width:90px" />
                        </td>
                        <td>
                        <input type="button" id="incrementnominee_1" style="width:30px" class="formbutton" value="+" onClick="add_nominee_row(1)" />
                 <input type="button" id="decrementnominee_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1,'tbl_nominee_entry')" />
                        </td>
                   </tbody>
                </table>
            </div>
        </div>
        <div style="width:1000px;float:left;">
        	<table align="center" width="100%">	
            	<tr style="height:10px;"></tr>
                <tr>
                    <td colspan="8" align="center" class="button_container">
                     <? 
                     echo load_submit_buttons( $permission, "fnc_accounts_group", 0,0 ,"reset_form('shareholdershare_2','','')",2);
                     ?> 
                    </td> 
                </tr>
            </table>
        </div>
  	</fieldset>
 </form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
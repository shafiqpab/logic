<?
/*-------------------------------------------- Comments
Purpose			         :  This Form Will Create Sewater Sample Rating Entry.
Functionality	         :
JS Functions	         :
Created by		         : Md. Mamun Ahmed Sagor
Creation date 	         : 14-05-2022
Requirment Client        :
Requirment By            :
Requirment type          :
Requirment               :
Affected page            :
Affected Code            :
DB Script                :
Updated by 		         :
Update date		         :
QC Performed BY	         :
QC Date			         :
Comments		         :
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sewater Sample Rating", "../../../", 1, 1,$unicode,'','');


?>
<style>
.bgColor{
	background-color: lightblue;
	}

</style>
<script>

  	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";
  	var permission='<? echo $permission; ?>';
	var season_mandatory_arr=new Array();

	<?
	$lib_season_mandatory_arr=array();
	$season_mandatory_sql=sql_select( "select company_name, season_mandatory from variable_order_tracking where variable_list=44 and status_active=1");
	foreach($season_mandatory_sql as $key=>$value)
	{
		$lib_season_mandatory_arr[$value[csf("company_name")]]=$value[csf("season_mandatory")];
	}
	$lib_season_mandatory_arr=json_encode($lib_season_mandatory_arr); 
	echo "season_mandatory_arr = ". $lib_season_mandatory_arr . ";\n";
	?>

	function fnc_sweater_sample_rating( operation )
		{
		
		

			var row_num=$('#tbl_sample_details tr').length-1;
			var data_all="";

			for (var i=1; i<=row_num-1; i++){
			
				
				data_all+="&txtMarks_" + i + "='" + $('#txtMarks_'+i).val()+"'"+"&txtRemarks_" + i + "='" + $('#txtRemarks_'+i).val()+"'"+"&txtSampleParticularId_" + i + "='" + $('#txtSampleParticularId_'+i).val()+"'"+"&txtScaleId_" + i + "='" + $('#txtScaleId_'+i).val()+"'"+"&txtScaleValue_" + i + "='" + $('#txtScaleValue_'+i).val()+"'";
	
			}
		

			
			var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+data_all+get_submitted_data_string('txt_requisition_id*cbo_complete_status*txt_complete_date*cbo_sample_name*cbo_sample_team*cbo_sample_status*cbo_buyer_name*txt_style_name*cbo_season_name*txt_sample_qty*cbo_dealing_merchant*cbo_gmts_item*txt_remarks*txt_requisition_update_id*txt_company_id',"../../../");
			freeze_window(operation);
			http.open("POST","requires/sweater_sample_rating_controller.php", true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_sweater_sample_rating_reponse;
		
	}

	function fnc_sweater_sample_rating_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==0 )
			{
			   show_msg(reponse[0]);
			   $("#txt_rating_id").val(reponse[1]);
			   $("#update_id").val(reponse[2]);
			   set_button_status(1, permission, 'fnc_sweater_sample_requisition_mst_info',1);
			   
			}

			if(reponse[0]==1 )
			{
				show_msg(reponse[0]);
			}
			if(reponse[0]==10 )
			{
				show_msg(reponse[0]);
			}
			if(reponse[0]==11 )
			{
				show_msg(reponse[0]);
			}

			
			release_freezing();
			
		
		}
	}
	
	function openmypage_requisition()
	{
		hide_left_menu("Button1");
		var title = 'Requisition ID Search';
		var page_link = 'requires/sweater_sample_rating_controller.php?&action=requisition_id_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var mst_tbl_id=this.contentDoc.getElementById("selected_job").value;//mst id

			if (mst_tbl_id!="")
			{
				freeze_window(5);
				get_php_form_data(mst_tbl_id, "populate_data_from_requisition", "requires/sweater_sample_rating_controller" );
				
				$("#cbo_company_name").attr('disabled','disabled');
				$("#cbo_buyer_name").attr('disabled','disabled');
 				//set_button_status(1, permission, 'fnc_sweater_sample_requisition_mst_info',1,0);
 				release_freezing();
 				
 			}
 		}
 	}

	 function openmypage_rating()
	{
		hide_left_menu("Button1");
		var title = 'Requisition ID Search';
		var page_link = 'requires/sweater_sample_rating_controller.php?&action=sample_rating_id_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var mst_tbl_id=this.contentDoc.getElementById("selected_job").value;//mst id

			if (mst_tbl_id!="")
			{
				freeze_window(5);
				get_php_form_data(mst_tbl_id, "populate_data_from_rating", "requires/sweater_sample_rating_controller" );
				
				$("#cbo_company_name").attr('disabled','disabled');
				$("#cbo_buyer_name").attr('disabled','disabled');
 				//set_button_status(1, permission, 'fnc_sweater_sample_requisition_mst_info',1,0);
 				release_freezing();
 				
 			}
 		}
 	}


	function fnc_point_scale(row_id,desc_id){			
			var point_val=$('#txt_point_scale_'+row_id+'_'+desc_id).val()*1;
			var point_weight=$('#txt_point_weight_'+desc_id).val()*1;
			var marks=point_weight*point_val;
			 $('#txtMarks_'+desc_id).val(marks);
			 $('#txtScaleId_'+desc_id).val(row_id);
			 $('#txtScaleValue_'+desc_id).val(point_val);

		
	
			  var row_num=$('#tbl_sample_details tr').length-1;
			 
			  
			  document.getElementById('txt_point_scale_'+row_id+'_'+desc_id).style.backgroundColor = 'green';

			//   $('#txt_point_scale_'+row_id+'_'+desc_id).addClass("bgColor");
			 var total_val="";
		
			 for (var i=1; i<=row_num-2; i++){
			 
				total_val=total_val*1+$('#txtMarks_'+i).val()*1;
	    	}
		
			document.getElementById('txt_total_marks').value=total_val;
		
			

			
	}
	
	

</script>

  
</head>
<body onLoad="set_hotkey(); ">
	<div style="width:100%;">
		<? echo load_freeze_divs ("../../../",$permission);  ?>
		<form name="sweater_sample_requisition_1" id="sweater_sample_requisition_1" enctype="multipart/form-data" method="post">
			<fieldset style="width:1200px;">
				<legend>Sample Requisition</legend>
                <div style="width:1200px; float:left;" align="center">
				<table cellpadding="2" cellspacing="2">
					<tr>
						<td colspan="5" align="right">Rating Id</td>
						<td colspan="3"> <input type="text" name="txt_rating_id" id="txt_rating_id" class="text_boxes" style="width: 140px;margin-right: 38px;" placeholder="Rating Id" readonly onDblClick="openmypage_rating();" > </td>
					</tr>
					<tr>
						<td class="must_entry_caption" align="right" width="100">Requisition</td>
						<td ><input type="text" name="txt_requisition_id" id="txt_requisition_id" class="text_boxes" style="width: 120px;margin-right: 38px;" placeholder="Requisition Id" readonly onDblClick="openmypage_requisition();" >
						</td>
						<td  align="right" width="100">Comp. Status</td>
						<td ><?
						$comp_status_arr=array(1=>"Pending",2=>"Complete");
						echo create_drop_down( "cbo_complete_status", 120, $comp_status_arr, "", 1, "-- Select Stage --", $selected, "", "", "" ); ?>
						</td>
						<td align="right" width="100">Compl. date</td>
						<td><input name="txt_complete_date" id="txt_complete_date" class="datepicker" type="text" value="" style="width:120px;" disabled /></td>
						<td  align="right" width="100">Sample Name</td>
						<td><? 
						$lib_sample_name=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name");
						echo create_drop_down( "cbo_sample_name", 120, $lib_sample_name,"", 1, "select Sample", $selected, "");?></td>
						<td  align="right" width="100">Team Name</td>
						<td><?=create_drop_down( "cbo_sample_team", 120, "select id,team_name from  lib_sample_production_team where product_category=6 and is_deleted=0","id,team_name", 1, "-- Select --", $selected, "" ); ?></td>
					</tr>
					<tr>
						<td align="right" width="100">Sample stage</td>
						<td><?=create_drop_down( "cbo_sample_status", 120, $sample_stage, "", 1, "-- Select Stage --", $selected, "", "", "" ); ?></td>
						<td  align="right" width="100">Buyer Name</td>
						<td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", 0, "" ); ?></td>
						<td  align="right" width="100">Style Ref</td>
						<td> <input name="txt_style_name" id="txt_style_name" class="text_boxes" type="text" value="" style="width:120px;"  disabled/> </td>
						<td  align="right" width="100">Season</td>
						<td id="season_td"><?=create_drop_down( "cbo_season_name", 120, $blank_array,"", 1, "-- Select Season --", $selected, "" ); ?></td>
						<td  align="right" width="100">Sample Qty</td>
						<td><input name="txt_sample_qty" id="txt_sample_qty" class="text_boxes" type="text" value="" style="width:120px;"  disabled/></td>
					</tr>
					<tr>
                    	<td  align="right" width="100">Dealing Merchant </td>
						<td><?=create_drop_down( "cbo_dealing_merchant", 120, "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Dealing Merchant --", $selected, "" ); ?></td>
						<td class="must_entry_caption" align="right" width="100">Gmts. Item</td>
						<td><?=create_drop_down( "cbo_gmts_item", 120,get_garments_item_array(100) ,'', 1, '--- Select Gmts. Item ---', 0, ""  ); ?></td>
						<td align="right" width="100">Remarks</td>
						<td colspan="4"><input name="txt_remarks" class="text_boxes" id="txt_remarks" style="width:340px" maxlength="500" title="Maximum 500 Character">
						<input  name="txt_requisition_update_id" id="txt_requisition_update_id" class="text_boxes" type="hidden" value=""  />
						<input  name="txt_company_id" id="txt_company_id" class="text_boxes" type="hidden" value=""  />
						<input  name="update_id" id="update_id" class="text_boxes" type="hidden" value=""  />
						
					</td>
					</tr>
					<br>
				</table>
				<table  border="1" style="width:1100px;" id="tbl_sample_details">
					<tr>
						<th width="30" >SL</th>
						<th width="170" >Particular</th>
						<th width="100" >Points Weight</th>
						<th colspan="5" width="100" >Point Scale</th>					
						<th width="100" >Marks</th>
						<th width="100" >Remarks</th>						
					</tr>
					<?php

						$sample_rating_particular_arr=array(1=>"On time Sample Submission",2=>"Sample Quality/Get Up",3=>"Handfeel",4=>"Measurements",5=>"Technical Risk Points");


						foreach($sample_rating_particular_arr as $key=>$val){?>


						<tr>
							<td><?=$key;?></td>
							<td><p><?=$val;?></p></td>
							<td><input name="txt_point_weight_<?=$key;?>" id="txt_point_weight_<?=$key;?>" style="width:100px;" class="text_boxes" type="text" value="5" disabled /></td>
							<td onClick="fnc_point_scale(1,<?=$key;?>)"><input name="txt_point_scale_1_<?=$key;?>" id="txt_point_scale_1_<?=$key;?>" style="width:100px;" class="text_boxes" type="text" value="4"  readonly /></td>
							<td onClick="fnc_point_scale(2,<?=$key;?>)"><input name="txt_point_scale_2_<?=$key;?>" id="txt_point_scale_2_<?=$key;?>" style="width:100px;" class="text_boxes" type="text" value="3" readonly /></td>
							<td onClick="fnc_point_scale(3,<?=$key;?>)"><input name="txt_point_scale_3_<?=$key;?>" id="txt_point_scale_3_<?=$key;?>" style="width:100px;" class="text_boxes" type="text" value="2"  readonly/></td>
							<td onClick="fnc_point_scale(4,<?=$key;?>)"><input name="txt_point_scale_4_<?=$key;?>" id="txt_point_scale_4_<?=$key;?>" style="width:100px;" class="text_boxes" type="text" value="1"  readonly/></td>
							<td onClick="fnc_point_scale(5,<?=$key;?>)"><input name="txt_point_scale_5_<?=$key;?>" id="txt_point_scale_5_<?=$key;?>" style="width:100px;" class="text_boxes" type="text" value="0"  readonly/></td>
							<td><input name="txtMarks_<?=$key;?>" id="txtMarks_<?=$key;?>" class="text_boxes" style="width:100px;" type="text" value="" disabled /></td>
							<td><input name="txtRemarks_<?=$key;?>" id="txtRemarks_<?=$key;?>" class="text_boxes" style="width:100px;" type="text" value=""  />
							<input  name="txtSampleParticularId_<?=$key;?>" id="txtSampleParticularId_<?=$key;?>" class="text_boxes" type="hidden" value="<?=$key;?>"  />
							<input  name="txtScaleId_<?=$key;?>" id="txtScaleId_<?=$key;?>" class="text_boxes" type="hidden" value="<?=$key;?>"  />
							<input  name="txtScaleValue_<?=$key;?>" id="txtScaleValue_<?=$key;?>" class="text_boxes" type="hidden" value="<?=$key;?>"  />
						   </td>
					</tr>
					
					<? } 
						?>
						<tr>
							
							<td colspan="8" align="right"><b>Total</b></td>
							<td><input name="txt_total_marks" id="txt_total_marks" class="text_boxes" style="width:100px;" type="text" value="" disabled /></td>
							<td> </td>
					</tr>

					
					<tr>
						<td colspan="10" height="40" valign="bottom" align="center" class="button_container">
							<?
							echo load_submit_buttons( $permission, "fnc_sweater_sample_rating", 0,1,"",1);
							?>
						
						</td>
					</tr>
				</table>
                        
				
                </div>
                <div style="width:5px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
                <div id="list_acknowledge" style="max-height:300px; width:290px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">$("#cbo_buyer_name").val(0);</script>
</html>

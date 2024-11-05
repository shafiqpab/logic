<?php
/******************************************************************
|	Purpose			:	This form will create Recipe base color Range Slap Entry
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Aziz
|	Creation date 	:	28.07.2022
|	Updated by 		: 		
|	Update date		:    
|	QC Performed BY	:		
|	QC Date			:	
|	Comments		:
********************************************************************/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------

echo load_html_head_contents("Recipe base color Range","../../", 1, 1, "",'','');

?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	function fnc_recipe_base_slap( operation )
	{
		
		if (form_validation('txt_date_upto','Applicable Date')==false)
		{
			return;
		}
		
		var row_num=$('#cut_slap_dtls  tr').length;
		//alert(row_num);
		var data2='';//txt_date_upto +'&txt_deleted_id='+txt_deleted_id
		var data1="action=save_update_delete&operation="+operation+"&row_num="+row_num+get_submitted_data_string('txt_date_upto*txt_deleted_id',"../../");
		for(var i=1; i<=row_num; i++)
		{
			if (form_validation('cbocolorrangeid_'+i,'Color Range')==false)
			{
				return;
			}
			//data2+=get_submitted_data_string('updateid_'+i+'*txtupperid_'+i+'*txtlowerid_'+i+'*txtslapid_'+i+'*txtpercentid_'+i,"../../",i);
			data2+=get_submitted_data_string('updateid_'+i+'*txtupperid_'+i+'*txtlowerid_'+i+'*cbocolorrangeid_'+i,"../../",i);
		}
		var data=data1+data2;
		//alert(data);//return;
		freeze_window(operation);
		http.open("POST","requires/recipe_base_color_range_slap_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_recipe_base_slap_reponse;
		
	}
	
	function fnc_recipe_base_slap_reponse()
	{
		if(http.readyState == 4) 
		{  
			var reponse=trim(http.responseText).split('**');
			//alert(reponse[0])
			//reset_form('cuting_list_cont','','');
			if(reponse[0]==10)
			{
				show_msg(trim(reponse[0]));
				release_freezing();
				return;
			}
			if(reponse[0]==0 || reponse[0]==1)
			{
				show_msg(trim(reponse[0]));
				release_freezing();
				set_button_status(1, permission, 'fnc_recipe_base_slap',1,1);
			}
			if(reponse[0]==11)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			if(reponse[0]==14)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			//alert (reponse[4]);
			var return_id_arr=reponse[4].split(',');
			var k=0;
			for(var j=1;j<=return_id_arr.length;j++)
			{ 
				$("#updateid_"+j).val(return_id_arr[k]);
				k++;
			}
		}
	}
	
	function append_cut_slap_row(val,id)
	{
		//alert(val+'='+id);
			if((val*1)==0)
			{
				fnc_remove_row( id );
				return 0;
			}
			else
			{
				var counter =$('#tbl_cut_details tbody tr').length;
				if(id!=counter) return;
				
				var counter =$('#tbl_cut_details tbody tr').length; 
				
				var upper_val=$("#txtupperid_"+counter).val()*1;
				var lower_val=$("#txtlowerid_"+counter).val()*1;
				//alert(upper_val);
				if(lower_val>upper_val)
				{
					$("#txtupperid_"+counter).val('');
					$('#txtupperid_'+counter).focus();
					return;
				}
				
				if(counter>=1) counter++;
				else if (counter<1) counter=1;
				var z=1;
				for(var i=1;i<=counter;i++)
				{	
					if($("#txtupperid_"+i).val()=="")
					{
						
						z++;
						
					}
					
				}
				//alert(z)
				
				if(z==1)
				{ 
					
					$('#tbl_cut_details tbody').append(
						'<tr id="trCut_'+counter+'">' +
						'<td><input type="text" name="txtslapid[]" class="text_boxes_numeric" id="txtslapid_'+counter+'"  value="'+counter+'" style="width:50px;" readonly/></td><td><input type="text"  name="txtlowerid[]" class="text_boxes_numeric" id="txtlowerid_'+counter+'"  style="width:100px;" readonly/></td><td><input type="text" name="txtupperid[]" align="right" class="text_boxes_numeric" id="txtupperid_'+counter+'"  style="width:100px;" onBlur="append_cut_slap_row(this.value,'+counter+')" /></td><td><input type="text" name="txtpercentid[]" class="text_boxes_numeric" id="txtpercentid_'+counter+'"  style="width:70px;" /><input type="hidden" name="updateid[]" class="text_boxes_numeric" id="updateid_'+counter+'"  style="width:20px;" /></td>'+ '</tr>'
					);
					//fnc_add_val_row(val,counter);
					
					
				}
				//fnc_remove_row();
				
				var data_id='';
				data_id=id+1;
				//alert(data_id);
				var lower_val='';
				lower_val=(val*1)+1;
				$("#txtlowerid_"+data_id).val(lower_val);
				
			}
			
		
		
	}
	function fnc_remove_row( id )
	{
		//alert(id)
			 var id=(id*1)+1;
			$('#trCut_'+id).remove();
				
			//alert(counter);
			
	}
	
	
	function fnResetForm()
	{
		reset_form('tbl_cut_details','variable_settings_container','','','','');
	}
	function add_break_down_tr(i)
	{
		var upper_value=$("#txtupperid_"+i).val();
		if(upper_value == '')
		{
			alert("Upper Limit(Qty) can not be null");
			return;
		}
		var row_num=$('#cut_slap_dtls tr').length;
		var j=i;
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
			$("#cut_slap_dtls tr:last").clone().find("input,select").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }
				});
			}).end().appendTo("#cut_slap_dtls");
			//$("#cut_slap_dtls tr:last").removeAttr('id').attr('id','trCut_'+i);
			$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+");");
			$('#txtupperid_'+i).removeAttr("onChange").attr("onChange","check_qty_limit("+i+");");

			//alert($("#txtupperid_"+j).val()*1);
			var uppler_add_to_lower="";
			var uppler_add_to_lower=$("#txtupperid_"+j).val()*1;
			var uppler_add_to_lower=uppler_add_to_lower+1;
			 		//alert(uppler_add_to_lower);
			$("#txtlowerid_"+i).val(uppler_add_to_lower);
			$("#txtupperid_"+i).val("")
			
			
			 
			$('#updateid_'+i).val("");
			$("#txtupperid_"+j).attr('disabled','disabled');
		}
	}
	function fn_deletebreak_down_tr(rowNo)
	{
		//var  tnumRow= $('#cut_slap_dtls tr').length;		
		//var j=rowNo-1;
		//var k=0;
		if(rowNo!=1){
			var index=rowNo-1 //cut_slap_dtls
			
			var updateIdDtls=$('#updateid_'+rowNo).val();
				var txt_deleted_id=$('#txt_deleted_id').val();
				var selected_id='';

				if(updateIdDtls!='')
				{
					if(txt_deleted_id=='') selected_id=updateIdDtls; else selected_id=txt_deleted_id+','+updateIdDtls;
					$('#txt_deleted_id').val( selected_id );
				}
			
			//$("#tbl_cut_details tr:eq("+index+")").remove()
			$("table#tbl_cut_details tbody tr:eq("+index+")").remove()
		//	var numRow = $('#tbl_cut_details tbody tr').length;
			var numRow = $('#tbl_cut_details  tr').length;
			//alert(rowNo+'='+numRow);
			for(i = rowNo;i <= numRow;i++){
				$("#tbl_cut_details tr:eq("+i+")").find("input,select").each(function() {
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						'value': function(_, value) { return value }
					});
					$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
					$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+");");
					
					$('#txtupperid_'+i).removeAttr("onChange").attr("onChange","check_qty_limit("+i+");");
					//$('#txtslapid_'+i).val( i );
					//k=i;					
				})				
			}
			var last_id=numRow-1;
			$('#txtupperid_'+last_id).removeAttr('disabled');
			
		}
	}
	function calculate_total_per(i)
	{
		//var total_per=$('#txtprint_'+i).val()*1+$('#txtembro_'+i).val()*1+$('#txtgmtwash_'+i).val()*1+$('#txtspworks_'+i).val()*1+$('#txtcutting_'+i).val()*1+$('#txtsewing_'+i).val()*1+$('#txtfinishing_'+i).val()*1;
		//$('#txttotalper_'+i).val(total_per);
	}
	function check_qty_limit(i)
	{
		var lower_val=$('#txtlowerid_'+i).val()*1;
		var upper_val=$('#txtupperid_'+i).val()*1;
		if(lower_val>upper_val)
		{
			alert("Upper Limit can not less then Lower Limit");
			$('#txtupperid_'+i).val("");
			return;
		}
	}
	function fnc_button_status_chk(data)
	{
		//alert(data);
		data_mst_id=data.split("_");
		$('#txt_date_upto').val(data_mst_id[0]);
		if(data_mst_id[1]!="")
		{
		//	alert(data_mst_id[1]);
		set_button_status(1, permission, 'fnc_recipe_base_slap',1,1);
		}
	}
	//fnc_button_status
</script>
</head>
<body  onload="set_hotkey()">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <div align="center" style="width:100%;" id="cuting_list_cont">	
		<form name="excess_cut_1" id="excess_cut_1"  autocomplete="off">
            <fieldset style="width:500px;"><legend>Recipe Base Color Range</legend>
            <!--<tr>
                	 <th>&nbsp;  </th> 
                     <th colspan="2">Ratio For Dyes </th>  
                     <th>&nbsp;  </th>
                </tr>-->
                
			<table  align="center" border="1" cellpadding="0" cellspacing="0" width="500px" id="tbl_cut_details" class="rpt_table" rules="all">
            <caption> Applicable Date Upto:<input type="text" name="txt_date_upto" id="txt_date_upto" class="datepicker" style="width:70px;" readonly />
            <input type="hidden" name="txt_deleted_id" id="txt_deleted_id"  style="width:70px;" readonly />
            <br>
            <b> Ratio For Dyes</b> </caption>
            
            	<thead>
                    <th> Color Range </th>
                    <th> Lower Value </th>
                    <th> Upper Value </th>
                    <th>&nbsp;</th>
                </thead>
                
                <tbody id="cut_slap_dtls">
			 	<tr>
                   <td width="120"> 
					<?
                        echo create_drop_down( "cbocolorrangeid_1", 120, $color_range,"", 1, "-- Select --", $selected, "","","1,2,9,10" );
                    ?>
                   </td>
                   <td> 
                        <input  type="text" name="txtlowerid[]" id="txtlowerid_1" class="text_boxes_numeric" style="width:100px;" align="right" value="0" />
                   </td>
                    <td> 
                        <input  type="text" name="txtupperid[]" id="txtupperid_1" class="text_boxes_numeric" style="width:100px;"  align="right" onChange="check_qty_limit(1)" /> 
                   </td>
                   <td> 
                        <input  type="hidden" name="updateid[]" id="updateid_1"/>
                        <input type="button" id="increase_1" style="width:40px" class="formbutton" value="+" onClick="add_break_down_tr(1);" />
                        <input type="button" id="decrease_1" style="width:40px" class="formbutton" value="-" onClick="fn_deletebreak_down_tr(1);" />
                   </td>
             </tr>
             </tbody>
		   </table>
            <div style="width:500px; height:auto;" id="slap_list" align="center">
			<table cellspacing="0" width="100%" >
						<tr> 
							<td align="center" width="320">&nbsp;</td>						
						</tr>						 
						<tr>
						   <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
								<?=load_submit_buttons( $permission, "fnc_recipe_base_slap", $is_update,0 ,"reset_form('excess_cut_1','','')",1); ?>
							</td>					
						</tr>
			 </table>
		</div>
        
		
	 </fieldset>
    </form>
	</div>
     <div style="width:300px; min-height:40px; margin:auto" align="center" id="variable_settings_container">
         <?
        // $sql="select app_date_upto from  lib_recipe_base_color_range where is_deleted=0 group by app_date_upto order by app_date_upto desc";
         //  echo create_list_view("list_view", "Applicable", "200", "250", "200", 1,$sql,"get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0", $arr, "app_date_upto", "requires/body_part_entry_controller", 'setFilterGrid("list_view",-1);', '0,0');
		 ?>
        	<table  align="center" border="1" cellspacing="0" width="300px"  class="rpt_table" rules="all" >
            <thead>
            	<th> SL</th>
                <th> Applicable Date</th>
            </thead>
		 <?
         $date_sql="select ID,APP_DATE_UPTO from  lib_recipe_base_color_range where is_deleted=0 order by app_date_upto desc";
		  $date_sql_res=sql_select($date_sql);
		  foreach($date_sql_res as $row)
		  {
			  $app_date_arr[$row['APP_DATE_UPTO']].=$row['ID'].',';
		  }
		  $k=1;
		 // print_r($app_date_arr);
		  foreach($app_date_arr as $date_key=>$mst_id)
		  {
			  if ($k % 2 == 0) $bgcolor = "#E9F3FF";
			 else $bgcolor = "#FFFFFF";
			  // echo  $mst_id.'d';;
			 $mst_ids=rtrim($mst_id,',');
			 $mst_ids=array_unique(explode(",",$mst_ids));
			  $mst_idss=implode(",",$mst_ids);
			
		 ?>			 
            <tr bgcolor="<? echo $bgcolor; ?>"  onClick='show_list_view("<? echo $date_key.'_'. $mst_idss; ?>","load_php_data_to_form","cut_slap_dtls","requires/recipe_base_color_range_slap_controller");fnc_button_status_chk("<? echo change_date_format($date_key).'_'.$mst_idss; ?>");' style="cursor:pointer">
               <td><?=$k;?>	</td>	
               <td><?=change_date_format($date_key);?>	</td>					
            </tr>
                        
			<?
            $k++;
		  }
			?>
				 </table>
             
         </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

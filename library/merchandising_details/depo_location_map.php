<?php
/******************************************************************
|	Purpose			:	This form will create Depo Location Mapping
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Kausar
|	Creation date 	:	23.01.2016
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
//echo load_html_head_contents("Excess Cut Entry", "../../", 1, 1,$unicode,1,'');
echo load_html_head_contents("Depo Location Mapping","../../", 1, 1, "",'1','');

?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	function fnc_depo_mapping( operation )
	{
		if (form_validation('cbo_country_id*cbo_ultimate_id','Country Name*Depo Name')==false)
		{
			return;
		}
		var row_num=$('#depo_tbl tbody tr').length;
		var data2='';
		var data1="action=save_update_delete&operation="+operation+"&row_num="+row_num+get_submitted_data_string('cbo_country_id*cbo_ultimate_id',"../../");
		for(var i=1; i<=row_num; i++)
		{
			data2+=get_submitted_data_string('updateid_'+i+'*txtDepoDtls_'+i,"../../",i);
		}
		var data=data1+data2;
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/depo_location_map_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_depo_mapping_reponse;
	}
	
	function fnc_depo_mapping_reponse()
	{
		if(http.readyState == 4) 
		{  
			var reponse=trim(http.responseText).split('**');
			show_msg(trim(reponse[0]));
			if(reponse[0]==0 || reponse[0]==1)
			{
				release_freezing();
				//$("#update_id").val(response[2]);
				reset_form('ultimatecountry_1','depo_location_mapping','')
				set_button_status(0, permission, 'fnc_depo_mapping',1);
			}
			if(reponse[0]==14)
			{
				alert('Not Allow');
				return;
			}
			//alert (reponse[4]);
			/*var return_id_arr=reponse[4].split(',');
			var k=0;
			for(var j=1;j<=return_id_arr.length;j++)
			{ 
				$("#updateid_"+j).val(return_id_arr[k]);
				k++;
			}*/
		}
	}
	
	function append_depo_mapping_row(val,id)
	{
		//alert(val+'='+id);
		if(val=='')
		{
			fnc_remove_row( id );
			return 0;
		}
		else
		{
			var counter =$('#depo_tbl tbody tr').length;
			if(id!=counter) return;
			
			var counter =$('#depo_tbl tbody tr').length; 
			if(counter>=1) counter++;
			else if (counter<1) counter=1;
			var z=1;
			for(var i=1;i<=counter;i++)
			{	
				if($("#txtDepoDtls_"+i).val()=="")
				{
					z++;
				}
			}
			//alert(z);
			if(z==1)
			{ 
				$('#depo_tbl tbody').append(
				'<tr id="trUltimate_'+counter+'">' +
				'<td width="40">'+counter+'</td><td><input type="text" name="txtDepoDtls_'+counter+'" id="txtDepoDtls_'+counter+'" class="text_boxes" style="width:300px;" onBlur="append_depo_mapping_row(this.value,'+counter+');" /><input type="hidden" name="updateid_'+counter+'" id="updateid_'+counter+'" class="text_boxes" style="width:20px;" /></td>'+ '</tr>'
				);
			}
			//fnc_remove_row();
		}
	}
	
	function fnc_remove_row(id)
	{
		var id=(id*1)+1;
		$('#trUltimate_'+id).remove();
	}
</script>
</head>
<body  onload="set_hotkey()">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <div align="center" style="width:100%;">	
		<form name="ultimatecountry_1" id="ultimatecountry_1"  autocomplete="off">
            <fieldset style="width:400px;"><legend></legend>
            <table cellpadding="0" cellspacing="2" width="520px">
                <tr>
                    <td width="110" class="must_entry_caption">Country Name</td>
                    <td><? echo create_drop_down( "cbo_country_id", 150, "Select id, country_name from lib_country where is_deleted=0 order by country_name ASC","id,country_name", 1, "-- Select Country --", $selected, "load_drop_down( 'requires/depo_location_map_controller',this.value, 'load_drop_down_ultimate_country', 'ultimate_td' );" ); ?>
                    </td>
                    <td width="110" class="must_entry_caption">Ultimate Country</td>
                    <td id="ultimate_td"><? echo create_drop_down( "cbo_ultimate_id", 150, $blank_array,"", 1, "-- Select--", $selected, "" ); ?>
                    </td>
                </tr>
            </table>
            <br>
            <div style="width:400px; float:left; min-height:40px; margin:auto" align="center" id="depo_location_mapping"></div>
	 </fieldset>
    </form>
	</div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

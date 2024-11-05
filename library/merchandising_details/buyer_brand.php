<?php
/******************************************************************
|	Purpose			:	This form will create Buyer Wise Brand Entry
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Kausar
|	Creation date 	:	14.08.2020
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
echo load_html_head_contents("Buyer Wise Brand Entry","../../", 1, 1, "",'1','');

?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	function fnc_buyer_brand( operation )
	{
		if (form_validation('cbo_buyer_id','Buyer Name')==false)
		{
			return;
		}
		var row_num=$('#brand_tbl tbody tr').length;
		
		var data2='';
		var data1="action=save_update_delete&operation="+operation+"&row_num="+row_num+get_submitted_data_string('cbo_buyer_id',"../../");
		//alert (data1)
		for(var i=1; i<=row_num; i++)
		{
			data2+=get_submitted_data_string('updateid_'+i+'*txtBrandName_'+i,"../../",i);
		}
		var data=data1+data2;
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/buyer_brand_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_buyer_brand_reponse;
	}
	
	function fnc_buyer_brand_reponse()
	{
		if(http.readyState == 4) 
		{  
			var reponse=trim(http.responseText).split('**');
			show_msg(trim(reponse[0]));
			if(reponse[0]==0 || reponse[0]==1)
			{
				release_freezing();
				//$("#update_id").val(response[2]);
				reset_form('buyerbrand_1','buyer_brand_name','')
				set_button_status(0, permission, 'fnc_buyer_brand',1);
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
	
	function append_brandName_row(val,id)
	{
		//alert(val+'='+id);
		if(val=='')
		{
			fnc_remove_row( id );
			return 0;
		}
		else
		{
			var counter =$('#brand_tbl tbody tr').length;
			if(id!=counter) return;
			
			var counter =$('#brand_tbl tbody tr').length; 
			if(counter>=1) counter++;
			else if (counter<1) counter=1;
			var z=1;
			for(var i=1;i<=counter;i++)
			{	
				if($("#txtBrandName_"+i).val()=="")
				{
					z++;
				}
			}
			//alert(z);
			if(z==1)
			{ 
				$('#brand_tbl tbody').append(
				'<tr id="trBrand_'+counter+'">' +
				'<td width="40">'+counter+'</td><td><input type="text" name="txtBrandName_'+counter+'" id="txtBrandName_'+counter+'" class="text_boxes" style="width:300px;" onBlur="append_brandName_row(this.value,'+counter+');" /><input type="hidden" name="updateid_'+counter+'" id="updateid_'+counter+'" class="text_boxes" style="width:20px;" /></td>'+ '</tr>'
				);
			}
			//fnc_remove_row();
		}
	}

	function fnc_remove_row(id)
	{
		var id=(id*1)+1;
		$('#trBrand_'+id).remove();
	}
</script>
</head>
<body  onload="set_hotkey()">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <div align="center" style="width:100%;">	
		<form name="buyerbrand_1" id="buyerbrand_1"  autocomplete="off">
            <fieldset style="width:400px;"><legend></legend>
            <table cellpadding="0" cellspacing="2" width="320px" align="center">
                <tr>
                    <td width="110" class="must_entry_caption">Buyer Name</td>
                    <td><? echo create_drop_down( "cbo_buyer_id", 150, "select id, buyer_name from lib_buyer where status_active =1 and is_deleted=0 order by buyer_name ASC","id,buyer_name", 1, "-- Select Buyer --", $selected, "show_list_view(this.value,'on_change_data','buyer_brand_name','requires/buyer_brand_controller','');" ); ?>
                    </td>
                </tr>
            </table>
            <br>
            <div style="width:400px; float:left; min-height:40px; margin:auto" align="center" id="buyer_brand_name"></div>
	 </fieldset>
    </form>
	</div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

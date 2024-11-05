<?
	/*-------------------------------------------- Comments
	Purpose			: 	This form will create Buyer wise shade % Entry
	Functionality	:	Must fill Company, Variable List
	JS Functions	:
	Created by		:	Md. Minul Hasan
	Creation date 	: 	26-09-2022
	Updated by 		:
	Update date		:
	QC Performed BY	:
	QC Date			:
	Comments		: 
	*/
	session_start();
	if( $_SESSION['logic_erp']['user_id'] == "" ) header(":login.php");
	require_once('../../../includes/common.php');
	extract($_REQUEST);
	$_SESSION['page_permission']=$permission;
	//--------------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents("Buyer wise shade % Entry", "../../../", 1, 1,$unicode,'','');
?>
<script language="javascript">
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";
	var permission='<? echo $permission; ?>';

	function fnc_load_party(company_id, within_group) {
        if ( form_validation('cbo_company_name','Company')==false ) {
            
            $('#party_td').html('<select name="cbo_party_name" id="cbo_party_name" class="combo_boxes " style="width:150px" onchange="">\n<option data-attr="" value="0">-- Select Party --</option>\n</select>');
            return;
        }
        var company_id = company_id;
        var within_group = within_group;

        load_drop_down( 'requires/buyer_wise_shade_entry_controller', company_id+'_'+within_group, 'load_drop_down_party', 'party_td' );
    }

    function fnc_addRow( i, table_id, tr_id )
    {

        var prefix=tr_id.substr(0, tr_id.length-1);
        var row_num = $('#tbl_dtls_buyer_wise_shade_entry tbody tr').length;
        var row_num1 = $('#tbl_dtls_buyer_wise_shade_entry tbody tr').length;

        for(var k =1; k<=row_num;k++)
        {
            var shadeLowerLimit     = $('#shadeLowerLimit_'+k).val();
	    	var shadeUperLimit      = $('#shadeUperLimit_'+k).val();


	    	if(shadeLowerLimit=='' || shadeUperLimit=='')
	    	{
	    		alert("Please Fill Up Lower Limit And Uper Limt, Then Add New Row!!!");
	    		return;
	    	}
        }

        row_num++;
        var clone= $("#"+tr_id+i).clone();
        clone.attr({
            id: tr_id + row_num,
        });

        clone.find("input,select").each(function(){

            $(this).attr({ 
                'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
                'name': function(_, name) { return name },
                'value': function(_, value) { return value }
            });
        }).end();
        $("#"+tr_id+row_num1).after(clone);

        for(var i =1; i<=row_num;i++)
        {
            $('#tbl_dtls_buyer_wise_shade_entry tr:eq(' + i + ') td:eq(0)').html(i);
        }

        $('#txtItemColor_'+row_num).removeAttr("value").attr("value",0);
        $('#shadeLowerLimit_'+row_num).removeAttr("value").attr("value",'');
        $('#shadeUperLimit_'+row_num).removeAttr("value").attr("value",'');
        $('#shadePrice_'+row_num).removeAttr("value").attr("value",'');
        $('#hdnDtlsUpdateId_'+row_num).removeAttr("value").attr("value",'');

        $('#txtItemColor_'+row_num).removeAttr("disabled");
        $('#shadeLowerLimit_'+row_num).removeAttr("disabled");
        $('#shadeUperLimit_'+row_num).removeAttr("disabled");
        $('#shadePrice_'+row_num).removeAttr("disabled");
        $('#hdnDtlsUpdateId_'+row_num).removeAttr("disabled");

        $('#increase_'+row_num).removeAttr("value").attr("value","+");
        $('#decrease_'+row_num).removeAttr("value").attr("value","-");
        $('#increase_'+row_num).removeAttr("disabled");
        $('#decrease_'+row_num).removeAttr("disabled");
        $('#increase_'+row_num).removeAttr("onclick").attr("onclick","fnc_addRow("+row_num+",'"+table_id+"','"+tr_id+"');");
        $('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fnc_deleteRow("+row_num+",'"+table_id+"','"+tr_id+"');");
        set_all_onclick();
    }

    function fnc_deleteRow(rowNo,table_id,tr_id) 
    { 
        var numRow = $('#'+table_id+' tbody tr').length; 

        if(numRow!=1)
        {
            var updateIdDtls=$('#hdnDtlsUpdateId_'+rowNo).val();
            var txt_deleted_id=$('#txt_deleted_id').val();
            var selected_id='';
            if(updateIdDtls!='')
            {
                if(txt_deleted_id=='') selected_id=updateIdDtls; else selected_id=txt_deleted_id+','+updateIdDtls;
                $('#txt_deleted_id').val( selected_id );
            }
            $("#"+tr_id+rowNo).remove();

            for(var i =1; i<=numRow;i++)
	        {
 
				//Remove attribute
				$('#tbl_dtls_buyer_wise_shade_entry tr:eq(' + i + ')').removeAttr('id');
				$('#tbl_dtls_buyer_wise_shade_entry tr:eq(' + i + ') td:eq(1) select:eq(0)').removeAttr('id');
				$('#tbl_dtls_buyer_wise_shade_entry tr:eq(' + i + ') td:eq(2) input:eq(0)').removeAttr('id');
				$('#tbl_dtls_buyer_wise_shade_entry tr:eq(' + i + ') td:eq(3) input:eq(0)').removeAttr('id');
				$('#tbl_dtls_buyer_wise_shade_entry tr:eq(' + i + ') td:eq(4) input:eq(0)').removeAttr('id');
				$('#tbl_dtls_buyer_wise_shade_entry tr:eq(' + i + ') td:eq(5) input:eq(0)').removeAttr('id');
				$('#tbl_dtls_buyer_wise_shade_entry tr:eq(' + i + ') td:eq(6) input:eq(0)').removeAttr('id');

				//add attribute
				$('#tbl_dtls_buyer_wise_shade_entry tr:eq(' + i + ')').attr('id','row_'+i);
				$('#tbl_dtls_buyer_wise_shade_entry tr:eq(' + i + ') td:eq(1) select:eq(0)').attr('id','txtItemColor_3'+i);
				$('#tbl_dtls_buyer_wise_shade_entry tr:eq(' + i + ') td:eq(2) input:eq(0)').attr('id','shadeLowerLimit_'+i);
				$('#tbl_dtls_buyer_wise_shade_entry tr:eq(' + i + ') td:eq(3) input:eq(0)').attr('id','shadeUperLimit_'+i);
				$('#tbl_dtls_buyer_wise_shade_entry tr:eq(' + i + ') td:eq(4) input:eq(0)').attr('id','shadePrice_'+i);
				$('#tbl_dtls_buyer_wise_shade_entry tr:eq(' + i + ') td:eq(5) input:eq(0)').attr('id','increase_'+i);
				$('#tbl_dtls_buyer_wise_shade_entry tr:eq(' + i + ') td:eq(6) input:eq(0)').attr('id','decrease_'+i);

	            $('#tbl_dtls_buyer_wise_shade_entry tr:eq(' + i + ') td:eq(0)').html(i);
	        }
        }
        else
        {
            return false;
        }
    }

    function fnc_check_duplicate(value,id)
    {
    	var value = value;
    	var id  = id.split("_");
    	var id = id[1]*1;

    	var row_num = $('#tbl_dtls_buyer_wise_shade_entry tbody tr').length;

    	for(var i =1; i<=row_num;i++)
        {
        	var row_value   = $('#txtItemColor_'+i).val();
        	
        	if(value==row_value && i!=id)
        	{
        		alert("Duplicate Color Range Not Allow!!!");

        		$('#txtItemColor_'+id).val(0);
        	}
           
        }
    }

    function check_shade_limit(id)
    {
    	var id  = id.split("_");
    	var id = id[1]*1;

    	var numRow = $('#tbl_dtls_buyer_wise_shade_entry tbody tr').length; 

    	var shadeLowerLimit     = $('#shadeLowerLimit_'+id).val()*1;
	    var shadeUperLimit      = $('#shadeUperLimit_'+id).val()*1;

	    if(numRow>1)
	    {
	    	var pre_row_num = id-1;
	    	var pre_row_uperlimit = $('#shadeUperLimit_'+pre_row_num).val();

	    	if(pre_row_uperlimit=='')
	    	{
	    		$('#shadeLowerLimit_'+id).val('');
	    		alert("Please Fill Up Uper Row Uper Limt!!!");
	    		return;
	    	}
	    }

	    if(shadeLowerLimit>=shadeUperLimit && shadeLowerLimit!='' && shadeUperLimit!='')
	    {
	    	alert("Shade Lower Limit Can Not Be Greater Than Or Equale Shade Uper Limit!!!");

	    	$('#shadeLowerLimit_'+id).val('');
	    	$('#shadeUperLimit_'+id).val('');
	    	return;
	    }

	    if(numRow>1)
	    {
	    	var pre_row_num = id-1;
	    	var pre_row_num1 = id+1;
	    	
	    	var pre_row_uperlimit = $('#shadeUperLimit_'+pre_row_num).val();
	    	var row_lowerlimit = $('#shadeLowerLimit_'+id).val();
	    	var row_uperlimit = $('#shadeUperLimit_'+id).val();
	    	var next_row_lowerlimit = $('#shadeLowerLimit_'+pre_row_num1).val();

	    	if(pre_row_uperlimit*1>=row_lowerlimit*1)
	    	{
	    		alert("Previous Row Uper Limt Can Not Greater Than Or Equale This Row Lower Limit!!!");
	    		$('#shadeLowerLimit_'+id).val('');
	    		return;
	    	}

	    	if(row_uperlimit*1>=next_row_lowerlimit*1)
	    	{
	    		if(numRow!=pre_row_num1)
	    		{
		    		alert("Next Row Lower Limt Can Not Less Than Or Equale This Row Uper Limit!!!");
		    		$('#shadeUperLimit_'+id).val('');
		    		return;
		    	}
		    	else
		    	{
		    		if(next_row_lowerlimit*1>0)
		    		{
		    			alert("Next Row Lower Limt Can Not Less Than Or Equale This Row Uper Limit!!!");
		    			$('#shadeUperLimit_'+id).val('');
		    			return;
		    		}
		    	}
	    	}
	    }
    }

    function fnc_buyer_wise_shade_entry(operation)
    {

    	if(operation==0 || operation==1 || operation==2)
    	{
    		if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name*txt_applicable_up_to_date','Company*Within Group*Party*Appplicable Up To Date')==false )
			{
				return;
			}

			var cbo_company_name    		= $('#cbo_company_name').val();
	        var cbo_within_group   			= $('#cbo_within_group').val();
	        var cbo_party_name   			= $('#cbo_party_name').val();
	        var txt_applicable_up_to_date 	= $('#txt_applicable_up_to_date').val();
	        var txt_remarks    				= $('#txt_remarks').val();
	        var txt_update_id    			= $('#txt_update_id').val();
	        var txt_deleted_id    			= $('#txt_deleted_id').val();

	        var j=0;
	        var i=0;
	        var check_field=0;
	        var data_all="";

	        $("#tbl_dtls_buyer_wise_shade_entry tbody tr").each(function()
	        {
	        	var txtItemColor         = $(this).find('select[name="txtItemColor[]"]').val();
	            var shadeLowerLimit        = $(this).find('input[name="shadeLowerLimit[]"]').val();
	            var shadeUperLimit      = $(this).find('input[name="shadeUperLimit[]"]').val();
	            var shadePrice        = $(this).find('input[name="shadePrice[]"]').val();
	            var hdnDtlsUpdateId          = $(this).find('input[name="hdnDtlsUpdateId[]"]').val();

	            if( (txtItemColor==0 || shadeLowerLimit=='' || shadeUperLimit=='' || shadePrice==''))
	            {
	            	if(txtItemColor==0)
					{
	                    alert('Please Fill up Color Range');
	                    check_field=1 ; return;
	                }
					else if(shadeLowerLimit=='')
					{
						alert('Please Select Shade Lower Limit ');
						check_field=1 ; return;
					}
					else if(shadeUperLimit=='')
					{
						alert('Please Fill up Shade Uper Limit ');
						check_field=1 ; return;
					}
					else if(shadePrice=='')
					{
						alert('Please Fill up Shade Price ');
						check_field=1 ; return;
					}
	            }
	            j++;
	            i++;

	            data_all +="&txtItemColor_"+j+"='"+txtItemColor+"'&shadeLowerLimit_"+j+"='"+shadeLowerLimit+"'&shadeUperLimit_"+j+"='"+shadeUperLimit+"'&shadePrice_"+j+"='"+shadePrice+"'&hdnDtlsUpdateId_"+j+"='"+hdnDtlsUpdateId+"'";
	        });

	        if(check_field==0)
	        {
	        	var data="action=save_update_delete&operation="+operation+'&total_row='+i+'&cbo_company_name='+cbo_company_name+'&cbo_within_group='+cbo_within_group+'&cbo_party_name='+cbo_party_name+'&txt_applicable_up_to_date='+txt_applicable_up_to_date+'&txt_remarks='+txt_remarks+'&txt_update_id='+txt_update_id+'&txt_deleted_id='+txt_deleted_id+data_all;

	        	//alert(data);
	        	freeze_window(operation);
	            http.open("POST","requires/buyer_wise_shade_entry_controller.php",true);
	            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	            http.send(data);
	            http.onreadystatechange = fnc_buyer_wise_shade_entry_response;
	        }
	        else
	        {
	        	return;
	        }
    	}
    }

    function fnc_buyer_wise_shade_entry_response()
    {
    	if(http.readyState == 4) 
        {
        	var response=trim(http.responseText).split('**');

        	if(response[0]==0 || response[0]==1)
        	{
        		show_list_view(response[1],'buyer_wise_shade_list_view','buyer_wise_shade_entry_details_container','requires/buyer_wise_shade_entry_controller','');

        		document.getElementById('txt_update_id').value= response[1];
        		document.getElementById('txt_deleted_id').value= '';

        		if(response[0]==1){
        			fnResetForm();
        		}
        		if(response[0]==0){
        			fnResetForm();
        			//$('#cbo_company_name').attr('disabled','true');
        			//$('#cbo_within_group').attr('disabled','true');
        			//$('#cbo_party_name').attr('disabled','true');
        			//$('#txt_applicable_up_to_date').attr('disabled','true');
        			//$('#txt_remarks').attr('disabled','true');    			
        		}
        	}

        	if (response[0]==2)
        	{
        		show_list_view(response[1],'buyer_wise_shade_list_view','buyer_wise_shade_entry_details_container','requires/buyer_wise_shade_entry_controller','');
        		set_button_status(0, permission, 'fnc_buyer_wise_shade_entry',1);

        		document.getElementById('txt_update_id').value= '';
        		document.getElementById('txt_deleted_id').value= '';

        		fnResetForm();

        	}

        	if (response[0]==20)
        	{
        		alert(response[1]);
        		release_freezing();
        		return;
        	}

        	if (response[0]==14)
        	{
        		alert(response[1]);
        		release_freezing();
        		return;
        	}

        	show_list_view(response[1],'buyer_wise_shade_details_list_view','list_view','requires/buyer_wise_shade_entry_controller','');

        	release_freezing();
        	set_all_onclick();
        	setFilterGrid("list_view")

        	show_msg(response[0]);
        	
        }
    }

	function set_max_lenght(value, id)
	{
		var tot_value = value*1;
		var value = value.split(".");

		val = value[1];


		var length = val.length;
		if(length>4)
		{
			alert("Not Allow Greater Four Digit After Decimal!!!");
			document.getElementById(id).value = tot_value.toFixed(4);
		}
	}

	function fnResetForm()
 	{
		set_button_status(0, permission, 'fnc_buyer_wise_shade_entry',1);
		reset_form('buyer_wise_shade_entry_1','','','','','');

		var numRow = $('#tbl_dtls_buyer_wise_shade_entry tbody tr').length; 

        if(numRow!=1)
        {
            for(var i =1; i<=numRow;i++)
	        {
	        	if(i!=1)
        		{
	           		$("#row_"+i).remove();
	           	}
	           	else
	           	{
	           		$('#txtItemColor_1').attr('disabled',false);
		        	$('#shadeLowerLimit_1').attr('disabled',false);
		        	$('#shadeUperLimit_1').attr('disabled',false);
		        	$('#shadePrice_1').attr('disabled',false);
		        	$('#increase_1').attr('disabled',false);
		        	$('#decrease_1').attr('disabled',false);
	           	}
	        }
        }
        else
        {
        	$('#txtItemColor_1').attr('disabled',false);
        	$('#shadeLowerLimit_1').attr('disabled',false);
        	$('#shadeUperLimit_1').attr('disabled',false);
        	$('#shadePrice_1').attr('disabled',false);
        	$('#increase_1').attr('disabled',false);
        	$('#decrease_1').attr('disabled',false);
        }
 	}

</script>
<body  onLoad="set_hotkey();">
	<div align="center" style="width:100%;">
		<? echo load_freeze_divs ("../../../",$permission);  ?>
		<form name="buyer_wise_shade_entry_1" id="buyer_wise_shade_entry_1" autocomplete="off">
			<fieldset style="width:850px;">
            	<legend>Buyer wise shade % Entry</legend>
            	<table width="850" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
            		<tr>
            			<td width="110" class="must_entry_caption">Company Name </td>
                        <td width="160"><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_load_party(this.value,document.getElementById('cbo_within_group').value);"); ?>
                        </td>
                        <td width="110" class="must_entry_caption">Within Group</td>
                        <td width="160"><?php echo create_drop_down( "cbo_within_group", 150, $yes_no,"", 0, "--  --", 0, "fnc_load_party(document.getElementById('cbo_company_name').value,this.value);" ); ?> &nbsp;
                        </td>
                        <td width="110" class="must_entry_caption">Party Name</td>
                        <td id="party_td">
                        	<? echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "-- Select Party --", $selected, "" ); ?>
                        </td>
            		</tr>
            		<tr>
            			<td class="must_entry_caption">Applicable Date Upto</td>
                        <td> <input type="text" name="txt_applicable_up_to_date" id="txt_applicable_up_to_date"  style="width:140px"  class="datepicker" value="" /> 
						 </td>
						 <td >Remarks</td>
                        <td colspan="3"><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:415px"  placeholder="Write" />
                        </td>
            		</tr>
            	</table>
            </fieldset>
            <fieldset style="width:850px;">
            	<legend>Buyer wise shade % Entry Details</legend>
            	<table width="850px" cellpadding="0" cellspacing="2" border="0" class="rpt_table" rules="all" id="tbl_dtls_buyer_wise_shade_entry">
            		<thead class="form_table_header">
            			<th width="30">SL</th>
                        <th width="100">Color Range</th>
                        <th width="100">Lower Limit(Shade %)</th>
                        <th width="100">Upper Limit(Shade %)</th>
                        <th width="100">Price [USD]</th>
                        <th width="100">&nbsp;</th>
            		</thead>
            		<tbody id="buyer_wise_shade_entry_details_container">
            			<tr id="row_1">
            				<td align="center">
            					1
            				</td>
            				<td align="center">
                            	<? echo   create_drop_down( "txtItemColor_1", 170, $color_range,"", 1, "-- Select --",0,"fnc_check_duplicate(this.value, this.id)",0,'','','','','','',"txtItemColor[]")   ?>
                            </td>
                            <td align="center">
                            	<input  style="width:150px;" onchange="check_shade_limit(this.id);" type="text" id="shadeLowerLimit_1" name="shadeLowerLimit[]" style="width:30px" class="text_boxes_numeric" value="" />
                            </td>
                            <td align="center">
                            	<input style="width:150px;" onchange="check_shade_limit(this.id);" type="text" id="shadeUperLimit_1" name="shadeUperLimit[]" class="text_boxes_numeric" value="" />
                            </td>
                            <td align="center">
                            	<input onkeyup="set_max_lenght(this.value,this.id);"  style="width:150px;" type="text" id="shadePrice_1" name="shadePrice[]" class="text_boxes_numeric" value="" />
                            </td>
            				<td align="center" width="50">
            					<input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_1" class="text_boxes_numeric" style="width:50px"  readonly />

	                            <input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(1,'tbl_dtls_buyer_wise_shade_entry','row_')" />
	                            <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(1,'tbl_dtls_buyer_wise_shade_entry','row_');" />
                            </td>
            			</tr>
            		</tbody>
            	</table>
            	<table width="850px" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td align="center" colspan="11" class="button_container">
                        	<input type="hidden" name="txt_deleted_id" id="txt_deleted_id" readonly />
                        	<input type="hidden" name="txt_update_id" id="txt_update_id" readonly />
                        	<? echo load_submit_buttons( $permission, "fnc_buyer_wise_shade_entry", 0,0,"fnResetForm();",1); ?>
                        </td>
                    </tr>   
                </table>
            </fieldset>
		</form>
	</div>
	<div align="center" style="width:100%;" id="buyer_wise_shade_entry_list">         
		<table width="850px" cellpadding="0" border="0" class="rpt_table" rules="all">
			 <thead>
			 	<th align="center" width="35">Sl</th>
			 	<th align="center" width="150">Company Name</th>
			 	<th align="center" width="150">Within Group</th>
			 	<th align="center" width="150">Party Name</th>
			 	<th align="center" width="150">Applicable Date</th>
			 	<th align="center" width="150">Remarks</th>
			 </thead>
			 <tbody id="list_view">
			 	<?php
			 		$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
			 		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

			 		$sql = "select a.id, a.company_id,  a.within_group,  a.party_id,  a.applicable_upto_date,  a.remarks from shade_entry_mst a where a.status_active=1 and a.is_deleted=0";

			 		$result = sql_select($sql);
			 		$i=1;
			 		foreach($result as $data)
			 		{
			 			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

			 			if($data[csf('within_group')]==1)
			 			{
			 				$party_name = $company_library[$data[csf('party_id')]];
			 			}
			 			else
			 			{
			 				$party_name = $party_arr[$data[csf('party_id')]];
			 			}
			 	?>
				 	<tr style="cursor: pointer;" bgcolor="<? echo $bgcolor; ?>" onclick="get_php_form_data(<?php echo $data[csf('id')];?>,'load_php_data_to_form','requires/buyer_wise_shade_entry_controller')">
				 		<td align="center" width="35" ><?php echo $i;?></td>
				 		<td align="center" width="150" ><?php echo $company_library[$data[csf('company_id')]];?></td>
				 		<td align="center" width="150" ><?php echo $yes_no[$data[csf('within_group')]];?></td>
				 		<td align="center" width="150" ><?php echo $party_name;?></td>
				 		<td align="center" width="150" ><?php echo $data[csf('applicable_upto_date')];?></td>
				 		<td align="center" width="" ><?php echo $data[csf('remarks')];?></td>
				 	</tr>
				<?php

					$i++;
					}
				?>
			 </tbody>
		</table>             
	</div>
</body>
<script type="text/javascript">
	setFilterGrid('list_view',-1);
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
/*--- ----------------------------------------- Comments
Purpose         :   This form will create Efficiency Percentage Slab Entry              
Functionality   :   
JS Functions    :
Created by      :   Mirza Tahmid Tajik
Creation date   :   27-05-2017
Updated by      :       
Update date     :          
QC Performed BY :       
QC Date         :
Comments        :
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Efficiency Percentage Slab", "../../", 1,1, $unicode,1,'');
?>

<script type="text/javascript">

var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";


function fnc_LoadCompanyData(data)
{               

                var response_data = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/efficiency_percentage_slab_controller_v2');
                var is_exists = return_global_ajax_value( data, 'check_data_is_exis', '', 'requires/efficiency_percentage_slab_controller_v2');

                if(response_data!='')
                {
                    $("#tbl_efficiency_percentage tbody tr").remove();
                    $("#tbl_efficiency_percentage tbody").append(response_data);
                    if( is_exists.trim()=='yes')
                    {
                        set_button_status(1, permission, 'fnc_efficiency_percentage',2);
                    }
                    else
                    {
                       set_button_status(0, permission, 'fnc_efficiency_percentage',2);
                    }
                    set_all_onclick();
                    return;
                }
            return;
}

function fnc_efficiency_percentage( operation )
    {   
            if (form_validation('cbo_company_name','Company Name')==false)
            {
              return;
            }
            var company_name = $("#cbo_company_name").val();
            var row_num=$('#tbl_efficiency_percentage tbody tr').length;
            var data_all="";
            for (var i=1; i<=row_num; i++) 
            {
                /*if (form_validation('txtSmvLowerLimit_'+i+'*txtSmvUpperLimit_'+i+'*txtOrderQtyLowerLimit_'+i+'*txtOrderQtyUpperLimit_'+i,'SMV Lower Limit*SMV Upper Limit*Order Quantity Lower Limit*Order Quantity Upper Limit')==false)
                    {
                    return; 
                    }*/
           data_all=data_all+get_submitted_data_string('txtSmvLowerLimit_'+i+'*txtSmvUpperLimit_'+i+'*txtOrderQtyLowerLimit_'+i+'*txtOrderQtyUpperLimit_'+i+'*txtNewOrder_'+i+'*txtRepeatOrder_'+i+'*txtLearningCubPercentage_'+i+'*updateDtls_'+i,"../../");
            }
            
            var data="action=save_update_delete_efficiency_percentage&operation="+operation+'&total_row='+row_num+data_all+'&company_name='+company_name;
           // alert(data);return;
            freeze_window(operation);
            http.open("POST","requires/efficiency_percentage_slab_controller_v2.php", true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_efficiency_percentage_reponse;
    }
    
    function fnc_efficiency_percentage_reponse()
    {
        if(http.readyState == 4) 
        {
            var reponse=trim(http.responseText).split('**');
            
			if(reponse[0]==0)
			{
				show_msg(reponse[0]);
				fnc_LoadCompanyData(reponse[1]);
			}
			else if(reponse[0]==1 )
			{
				show_msg(reponse[0]);
			}
			else if(reponse[0]==10 )
			{
				show_msg(reponse[0]);
			}
            release_freezing();
        }
    }

    function add_rf_tr(i)
    { 
        var row_num=$('#tbl_efficiency_percentage tbody tr').length;
        if (row_num!=i)
        {
            return false;
        }

        else
        { 
            i++;
            var k=i-1;
            $("#tbl_efficiency_percentage tbody tr:last").clone().find("input,select").each(function(){
            $(this).attr({ 
              'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
              'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
              'value': function(_, value) { return value }              
            });
            }).end().appendTo("#tbl_efficiency_percentage");
           
            $('#slabNo_'+i).val(i); 
            $('#txtSmvLowerLimit_'+i).val('');
            $('#txtSmvUpperLimit_'+i).val('');
            $('#txtOrderQtyLowerLimit_'+i).val('');
            $('#txtOrderQtyUpperLimit_'+i).val('');
            $('#txtNewOrder_'+i).val('');
            $('#txtRepeatOrder_'+i).val('');
            $('#txtLearningCubPercentage_'+i).val('');
            $('#updateDtls_'+i).val('');
            

            $('#increaserf_'+i).removeAttr("value").attr("value","+");
            $('#decreaserf_'+i).removeAttr("value").attr("value","-");
            $('#increaserf_'+i).removeAttr("onclick").attr("onclick","add_rf_tr("+i+");");
            $('#decreaserf_'+i).removeAttr("onclick").attr("onclick","fn_rf_deleteRow("+i+");");
            
            
        }
    }
       
    function fn_rf_deleteRow(rowNo) 
    { 
            var k=rowNo-1;
            if(rowNo!=1)
            {
              var numRow = $('#tbl_efficiency_percentage tbody tr').length; 
              $("table#tbl_efficiency_percentage tbody tr:eq("+k+")").remove();
                
				for(i = rowNo;i <= numRow;i++)
                {
					$("#tbl_efficiency_percentage tr:eq("+i+")").find("input").each(function() {
						$(this).attr({
							'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+(i-1)},
							'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ (i-1) },
							'value': function(_, value) { return value }             
						}); 
						
						$('#slabNo_'+(i-1)).val(i-1); 
						$('#increaserf_'+i).removeAttr("value").attr("value","+");
						$('#decreaserf_'+i).removeAttr("value").attr("value","-");
						$('#increaserf_'+i).removeAttr("onclick").attr("onclick","add_rf_tr("+(i-1)+");");
						$('#decreaserf_'+i).removeAttr("onclick").attr("onclick","fn_rf_deleteRow("+(i-1)+");");
					
					})
					
					
						
						
						/*$('#slabNo_'+s).val(i); 
						$('#txtSmvLowerLimit_'+i).val();
						$('#txtSmvUpperLimit_'+i).val();
						$('#txtOrderQtyLowerLimit_'+i).val();
						$('#txtOrderQtyUpperLimit_'+i).val();
						$('#txtNewOrder_'+i).val();
						$('#txtRepeatOrder_'+i).val();
						*/
						
					
					
					
                }
            }
            else
            {
                return false;
            }
    }



  



</script>


</head>
<body onLoad="set_hotkey();">
        <? echo load_freeze_divs ("../../",$permission);  ?>
        <br>
<center>

<div style="width:700px;">
<form name="efficiencyDtls_1" id="efficiencyDtls_1" class="fetch_results" style="margin-bottom:10px; float:left">
   <fieldset style="width:250px; margin-left:-350px;" id="sample_dtls">
    <table width="300" cellspacing="2" cellpadding="0" align="center">
        <tr>
            <td width="150" class="must_entry_caption"> <strong>Company Name</strong> </td>
            <td>
                <?
                             echo create_drop_down( "cbo_company_name", 157, "select comp.id,comp.company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_LoadCompanyData(this.value);" );
                ?>
            </td>
        </tr>   
    </table>
   </fieldset>

<br>

   <fieldset style="width:700px;" id="sample_dtls">
        <table id="tbl_efficiency_percentage" class="rpt_table" rules="all" width="100%" cellspacing="0" cellpadding="0" border="1">
            <thead>
                <tr>
                    <th rowspan="2" >Slab No</th> 
                    <th colspan="2" width="120">SMV Range</th>
                    <th colspan="2" width="120">Order Qty</th>
                    <th colspan="2" width="120">Efficiency %</th>
                    <th rowspan="2" width="100">Learning Curve %</th>
                    <th rowspan="2" width="70"></th>
                </tr>
                <tr>
                    <th>Lower Limit</th>
                    <th>Upper Limit</th>

                    <th>Lower Limit</th>
                    <th>Upper Limit</th>

                    <th>New Order</th>
                    <th>Repeat Order</th>
                </tr>
            </thead>

            <tbody>
                <tr id="tr_1" class="general">
                    <td align="center">
                        <input type="button" id="slabNo_1" name="slabNo_1" value="1" style="background-color:#B0B0B0" disabled>
                    </td>
                    <td align="center">
                        <input type="text" name="txtSmvLowerLimit_1" id="txtSmvLowerLimit_1" class="text_boxes_numeric" style="width:60px">
                    </td>
                    <td align="center">
                        <input type="text" name="txtSmvUpperLimit_1" id="txtSmvUpperLimit_1" class="text_boxes_numeric" style="width:60px">
                    </td>
                    <td align="center">
                        <input type="text" name="txtOrderQtyLowerLimit_1" id="txtOrderQtyLowerLimit_1" class="text_boxes_numeric" style="width:60px">
                    </td>
                    <td align="center">
                        <input type="text" name="txtOrderQtyUpperLimit_1" id="txtOrderQtyUpperLimit_1" class="text_boxes_numeric" style="width:60px">
                    </td>
                    <td align="center">
                        <input type="text" name="txtNewOrder_1" id="txtNewOrder_1" class="text_boxes_numeric" style="width:60px">
                    </td>
                    <td align="center">
                        <input type="text" name="txtRepeatOrder_1" id="txtRepeatOrder_1" class="text_boxes_numeric" style="width:60px">
                    
                        <input type="hidden" id="updateDtls_1" name="updateDtls_1" class="abc">
                    </td>
                    
                    <td align="center">
                        <input type="text" name="txtLearningCubPercentage_1" id="txtLearningCubPercentage_1" class="text_boxes" style="width:60px" placeholder="0,0,0">
                    </td>
                    
                    
                    
                    <td>
                        <input type="button" id="increaserf_1" name="increaserf_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_rf_tr(1)" />
                        <input type="button" id="decreaserf_1" name="decreaserf_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_rf_deleteRow(1);" />
                    </td>
                </tr>   

                                    
            </tbody>    
        </table> 
    

    <center>
    <table>
    <tr>
        <td colspan="15" height="40" valign="bottom" align="center" class="">
				<? 
                echo load_submit_buttons( $permission, "fnc_efficiency_percentage", 0,0 ,"reset_form('efficiencyDtls_1');",2);
                ?>
         </td>  
     </tr>
    </table>
    </center>

</fieldset>
</form>

</div>
</center>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html> 
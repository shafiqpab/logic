<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create KNit Garments Order Entry
					
Functionality	:	

JS Functions	:
Created by		:	Md. Rabiul islam
Creation date 	: 	18-10-2012
Updated by 		: 		
Update date		: 		   

QC Performed BY	:		

QC Date			:	

Comments		:

-----------------------------------------------------*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:../../login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sales Terget Info", "../../", 1, 1,$unicode,'','');
?>	

<script>

//if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  

var permission='<? echo $permission; ?>';
 
	function fnc_sales_target_entry( operation )
	{			
	if (form_validation('cbo_company_id*cbo_buyer_name*cbo_team_leader*cbo_starting_month*cbo_starting_year','Company Name*Buyer*Team Leader*text designation*starting month*starting year')==false)
		{
			return;
		}	
		else
		{
		var target_month=$("#cbo_starting_month").val()*1;
		var target_year=$("#cbo_starting_year").val()*1;
			
         var k=1;
         var month_data='';
            for (var i=0; i<12; i++)
            {
                if (k<13)
                {
                    var month=target_month+i;
                    var yy=target_year;
					
					month_data+="*month_"+String(month)+String(yy)+"*qty_"+String(month)+String(yy)+"*val_"+String(month)+String(yy)+"*mint_"+String(month)+String(yy);
                    if (month==12)
                        { target_month=0; i=0; target_year=target_year+1; }
                    
                }
                k++;
            }	
			var data="action=save_update_delete_mst&operation="+operation+get_submitted_data_string('update_id*cbo_company_id*cbo_buyer_name*cbo_agent*cbo_team_leader*cbo_starting_month*cbo_starting_year*txt_total_qty*txt_total_val*txt_total_alo_prcnt'+month_data,"../../");
			//alert(data);
		freeze_window(operation);
		 
		http.open("POST","requires/sales_target_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_sales_target_entry_reponse;
		 
	 	}			
	}	
	
	 
function fnc_sales_target_entry_reponse()
{
	
	if(http.readyState == 4) 
	{
		var reponse=http.responseText.split('**');
		 
		show_msg(trim(reponse[0]));
		$("#update_id").val(reponse[1]);
		
		permission='1_1_2_1';
		if(reponse[0]==0)
		{
			set_button_status(1, permission, 'fnc_sales_target_entry',1);
		}
		/*else if(reponse[1]==1)
		{
			set_button_status(1, permission, 'fnc_sales_target_entry',1);
		}*/
		else{set_button_status(0, permission, 'fnc_sales_target_entry',1);}
		//new development
		//if(reponse[0]==1 && reponse[1]==1){set_button_status(0, permission, 'fnc_sales_target_entry',1); reset_form('salesTargetForm','','');}
		release_freezing();
		
	}
	
}	

function fn_calculate()
{ 
	var target_month=$("#cbo_starting_month").val()*1;
	var target_year=$("#cbo_starting_year").val()*1;

	var tot_qty=tot_val=tot_alo_prcnt=0;
 	var k=1;
	for (var i=0; i<12; i++)
	{
		if (k<13)
		{
			var month=target_month+i;
			var yy=target_year;
			
			tot_qty+=$("#qty_"+String(month)+String(yy)).val()*1;
			tot_val+=$("#val_"+String(month)+String(yy)).val()*1;
			tot_alo_prcnt+=$("#mint_"+String(month)+String(yy)).val()*1;
			
			if (month==12)
				{ target_month=0; i=0; target_year=target_year+1; }
			
		}
		k++;
	}	
		
	$("#txt_total_qty").val(tot_qty);
	$("#txt_total_val").val(tot_val);
	$("#txt_total_alo_prcnt").val(tot_alo_prcnt);

	

}





function fnc_load_sales_target_data()
{   
	if (form_validation('cbo_company_id*cbo_buyer_name*cbo_team_leader*cbo_starting_month*cbo_starting_year','Company Name*Buyer*Team Leader*text designation*starting month*starting year')==false)
    {
        return;
    }	
    else
    {
        var fill_data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_agent').value+'_'+document.getElementById('cbo_team_leader').value+'_'+document.getElementById('cbo_starting_month').value+'_'+document.getElementById('cbo_starting_year').value;

        var data="action=generate_list_view&operation="+operation+'&data='+fill_data;
		//alert(data);
        freeze_window(operation);
        http.open("POST","requires/sales_target_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_load_sales_target_data_reponse;     
    }				
}

function fnc_load_sales_target_data_reponse()
{
	
	if(http.readyState == 4) 
	{
		
        var reponse=http.responseText.split('*_*');
		$("#list_view").html(reponse[0]);
		
		permission='1_1_2_1';
		if(reponse[1]){set_button_status(1, permission, 'fnc_sales_target_entry',1);}
		else{set_button_status(0, permission, 'fnc_sales_target_entry',1);}
		set_all_onclick();
		release_freezing();
		
	}
	
}	

function fuc_select_month(comp_id)
{
	get_php_form_data(comp_id, "select_month_from_variable", "requires/sales_target_controller");
}

</script>	
    	
</head>
<body onLoad="set_hotkey()">
    <div align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <fieldset style=" width:860px;"><legend>Sales Target</legend>
    <form name="salesTargetForm" id="salesTargetForm" autocomplete="off">
        <table cellpadding="0" cellspacing="">
            <tr>
                <td width="850" align="center">
                    <fieldset style="width:850px;">
                        <table  width="800" cellspacing="2" cellpadding="0" border="0">
                                <tr>
                                    <td width="100" align="right" class="must_entry_caption">Company Name</td>
                                    <td width="80">
                                    <? 
                                    echo create_drop_down( "cbo_company_id", 172, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "-- Select --",0,"fuc_select_month(this.value)", 0 );
                                    ?>
                                    </td>
                                    <td width="80" align="right" class="must_entry_caption">Buyer Name</td>
                                    <td width="80" id="buyer_td">
                                    <? 
                                    echo create_drop_down( "cbo_buyer_name", 172, "select buy.buyer_name, buy.id from lib_buyer buy where status_active =1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "fnc_load_sales_target_data();" );
                                    ?>
                                    </td>
                                    <td align="right">Agent</td>
                                    <td>
                                    <?	
                                    echo create_drop_down( "cbo_agent", 172, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id  and a.id in (select  buyer_id from lib_buyer_party_type where party_type in (20,21)) group by a.id,a.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select --", 0, "fnc_load_sales_target_data();",0 );  
                                    ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="100" align="right" class="must_entry_caption">Team Leader</td>
                                    <td width="86">
                                    <?  
                                    echo create_drop_down( "cbo_team_leader", 172, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", 0, "fnc_load_sales_target_data();" );
                                    ?>
                                    </td>
                                    <td height="" align="right">Designation </td>
                                    <td >                                            
                                    <input style="width:160px;" type="text"  class="text_boxes"  name="text_designation_value" id="text_designation_value"/>
                                    </td>
                                    <td align="right" class="must_entry_caption"> Starting Month </td>
                                    <td>	
                                    <? 
                                    echo create_drop_down( "cbo_starting_month", 172, $months,"", 1, "-- Select Month --", 0, "fnc_load_sales_target_data()" ,1);
                                    ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td  align="right" class="must_entry_caption">Year</td>			
                                    <td >
                                    <?  
                                    $c_year=date("Y",time());
                                    $year_array=array();
                                    for ($i=-10; $i<11; $i++)
                                    {
                                    $opt_yr=$c_year+$i;
                                    $year_array[$opt_yr]=$opt_yr;
                                    }
                                    echo create_drop_down( "cbo_starting_year", 172, $year_array, "", 1, "-- Select Year --",0, "fnc_load_sales_target_data();");
									 
                                    ?>
                                    </td>     
                                </tr>		
                            </table>                            
                        </fieldset> 
              <tr>
              	<td align="center">                                        
					<?
                    echo load_submit_buttons( $permission, "fnc_sales_target_entry",0,0,"reset_form('orderentry_1','','')",1);
                    ?> 
            	</td>
            </tr>                      
            <tr><td id="list_view"></td></tr> 
        </table>
    </form>
    </fieldset>
    </div> 
</body>
     
           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
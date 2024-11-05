<?
/*-------------------------------------------- Comments
Version                  :  V1
Purpose			         : 	This form will create Woven Garments Price Quotation Entry Entry
Functionality	         :	
JS Functions	         :
Created by		         :	Rashed 
Creation date 	         : 	18-10-2012
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
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$type=$_REQUEST['type'];
/*if ($_SESSION['logic_erp']["data_level_secured"]==1)
{
	if($_SESSION['logic_erp']["buyer_id"]!=0) $buyer_cond=" and a.id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
	if($_SESSION['logic_erp']["company_id"]!=0) $company_cond=" and id in (".$_SESSION['logic_erp']["company_id"].")"; else $company_cond="";
}
else
{
	$buyer_cond="";	$company_cond="";
}*/
$permission=$_SESSION['page_permission'];
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );

//---------------------------------------------------- Start---------------------------------------------------------------
//Master Table=============================================================================================================
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 160, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/quotation_entry_simple_final_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_season_buyer', 'season_td');" );     	 

} 
if($action=="only_for_all_season")
{
	echo create_drop_down( "cbo_season_name", 165, "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ","id,season_name", 1, "-- Select Season --", $selected,"" );
}
if ($action=="load_drop_down_season_buyer")
{
	$datas=explode('_',$data);
	echo create_drop_down( "cbo_season_name", 165, "select a.id,a.season_name from LIB_BUYER_SEASON a,variable_order_tracking b where a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.buyer_id='$datas[0]' and b.company_name='$datas[1]' and b.season_mandatory=1 and b.variable_list=44","id,season_name", 1, "-- Select Season --", $selected, "" );
}
if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent", 160, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21))  order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" );   
	 	 
}

if ($action=="cm_cost_predefined_method")
{
	$cm_cost_method=return_field_value("cm_cost_method", "variable_order_tracking", "company_name=$data  and variable_list=22 and status_active=1 and is_deleted=0");
	if($cm_cost_method=="")
	{
		$cm_cost_method=0;
	}
	echo $cm_cost_method;
	die;
	 	 
}
if($action=="check_conversion_rate")
{ 
	$data=explode("**",$data);
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate( $data[0], $conversion_date );
	echo "1"."_".$currency_rate;
	exit();	
}

if ($action=="asking_profit_percent")
{
	$asking_profit=return_field_value("asking_profit", "lib_standard_cm_entry", "company_id=$data  and status_active=1 and is_deleted=0");
	if($asking_profit=="")
	{
		$asking_profit=0;
	}
	echo $asking_profit;
	die;
	 	 
}

if ($action=="cost_per_minute")
{
	$monthly_cm_expense=0;
	$no_factory_machine=0;
	$working_hour=0;
	$cost_per_minute=0;
	$sql="select monthly_cm_expense,no_factory_machine,working_hour,cost_per_minute from lib_standard_cm_entry where company_id=$data  and status_active=1 and is_deleted=0 LIMIT 1";
	$data_array=sql_select($sql);
	foreach ($data_array as $row)
	{
		if($row[csf("monthly_cm_expense")] !="")
		{
		  $monthly_cm_expense=$row[csf("monthly_cm_expense")];
		}
		if($row[csf("no_factory_machine")] !="")
		{
		  $no_factory_machine=$row[csf("no_factory_machine")];
		}
		if($row[csf("working_hour")] !="")
		{
		  $working_hour=$row[csf("working_hour")];
		}
		if($row[csf("cost_per_minute")] !="")
		{
		  $cost_per_minute=$row[csf("cost_per_minute")];
		}
		
	}
	$data=$monthly_cm_expense."_".$no_factory_machine."_".$working_hour."_".$cost_per_minute;
	echo $data;

	 	 
}



if($action=="lead_time_calculate")
{
	$data=explode("_",$data);
	$txt_est_ship_date=gmdate('Y-m-d',strtotime( $data[0]));
	$txt_op_date=gmdate('Y-m-d',strtotime( $data[1]));
	$dayes=datediff('d',$txt_op_date,$txt_est_ship_date);
	if($dayes >= 7)
	{
	$day=$dayes%7;
	$week=($dayes-$day)/7;
	    if($week>1)
		{
			$week_string="W";
		}
		else
		{
			$week_string="W";
		}
		if($day>1)
		{
			$day_string="D";
		}
		else
		{
			$day_string="D";
		}
		if($day != 0)
	    {
		echo $week." ".$week_string." ".$day." ".$day_string;
		}
		else
		{
		echo $week." ".$week_string;
		}
	}
	else
	{
	if($dayes>1)
		{
			$day_string="Days";
		}
		else
		{
			$dayes="Day";
		}	
		echo $dayes." ".$day_string;
	}
	
}

if ($action=="quotation_id_popup")
{
  	echo load_html_head_contents("Quotation Entry","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);

?>
     
	<script>
	function js_set_value( quotation_id )
	{
		document.getElementById('selected_id').value=quotation_id;
		parent.emailwindow.hide();
	}
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="750" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
                    <thead>                	 
                        <th width="150">Company Name</th><th width="150">Buyer Name</th><th width="200">Date Range</th><th></th>           
                    </thead>
        			<tr>
                    	<td> <input type="hidden" id="selected_id">
							<? 
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'quotation_entry_simple_final_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
                        </td>
                   	<td id="buyer_td">
                     <? 
						echo create_drop_down( "cbo_buyer_name", 172, $blank_array,'', 1, "-- Select Buyer --" );
					?>	</td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_quotation_id_list_view', 'search_div', 'quotation_entry_simple_final_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        		</tr>
             </table>
          </td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle">
            <? 
			echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
			?>
			<? echo load_month_buttons();  ?>
            </td>
            </tr>
        <tr>
            <td align="center"valign="top" id="search_div"> 
	
            </td>
        </tr>
    </table>    
     
    </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_quotation_id_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and buyer_id='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0)
	{
	if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and est_ship_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $est_ship_date ="";
	}
	if($db_type==2)
	{
	if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and est_ship_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $est_ship_date ="";
	}
	 
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (1=>$comp,2=>$buyer_arr,5=>$pord_dept);
	$sql= "select id,company_id, buyer_id, style_ref,style_desc,pord_dept,offer_qnty,est_ship_date from  wo_pri_sim_mst a where status_active=1  and is_deleted=0 $company $buyer $est_ship_date order by id";
	echo  create_list_view("list_view", "Quotation ID,Company,Buyer Name,Style Ref,Style Desc.,Prod. Dept., Offer Qnty, Est Ship Date", "90,120,100,100,200,100,100","1000","320",0, $sql , "js_set_value", "id", "", 1, "0,company_id,buyer_id,0,0,pord_dept,0,0", $arr , "id,company_id,buyer_id,style_ref,style_desc,pord_dept,offer_qnty,est_ship_date", "",'','0,0,0,0,0,0,2,3') ;
} 

if ($action=="populate_data_from_search_popup")
{
	$cbo_approved_status=="";
	$data_array=sql_select("select id, company_id, buyer_id,m_list,bh_merchant,quotation_status,style_ref, revised_no, pord_dept,product_code, style_desc, currency, agent, offer_qnty, region, color_range, incoterm, incoterm_place, machine_line, prod_line_hr, fabric_source, costing_per, quot_date, est_ship_date,op_date, factory, remarks, garments_nature,order_uom,gmts_item_id,set_break_down,total_set_qnty ,cm_cost_predefined_method_id,exchange_rate,sew_smv,cut_smv,sew_effi_percent,cut_effi_percent,efficiency_wastage_percent,season_buyer_wise, approved,inserted_by, insert_date, status_active, is_deleted from wo_pri_sim_mst where id='$data'");
	foreach ($data_array as $row)
	{
		//
		echo "load_drop_down( 'requires/quotation_entry_simple_final_controller', '".$row[csf("company_id")]."', 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/quotation_entry_simple_final_controller', '".$row[csf("season_buyer_wise")]."', 'only_for_all_season', 'season_td' ); load_drop_down( 'requires/quotation_entry_simple_final_controller', '".$row[csf("company_id")]."', 'load_drop_down_agent', 'agent_td' );cm_cost_predefined_method('".$row[csf("company_id")]."') ;\n";
		echo "document.getElementById('txt_quotation_id').value = '".$row[csf("id")]."';\n";  
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";  
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref")]."';\n";  
		echo "document.getElementById('txt_revised_no').value = '".$row[csf("revised_no")]."';\n";  
		echo "document.getElementById('cbo_pord_dept').value = '".$row[csf("pord_dept")]."';\n";
		echo "document.getElementById('txt_product_code').value = '".$row[csf("product_code")]."';\n";
		echo "document.getElementById('txt_style_desc').value = '".$row[csf("style_desc")]."';\n";  
		echo "document.getElementById('cbo_currercy').value = '".$row[csf("currency")]."';\n";  
		echo "document.getElementById('cbo_agent').value = '".$row[csf("agent")]."';\n";  
		echo "document.getElementById('txt_offer_qnty').value = '".$row[csf("offer_qnty")]."';\n";  
		echo "document.getElementById('cbo_region').value = '".$row[csf("region")]."';\n";  
		echo "document.getElementById('cbo_color_range').value = '".$row[csf("color_range")]."';\n";  
		echo "document.getElementById('cbo_inco_term').value = '".$row[csf("incoterm")]."';\n";  
		echo "document.getElementById('txt_incoterm_place').value = '".$row[csf("incoterm_place")]."';\n";  
		echo "document.getElementById('txt_machine_line').value = '".$row[csf("machine_line")]."';\n";  
		echo "document.getElementById('txt_prod_line_hr').value = '".$row[csf("prod_line_hr")]."';\n";  
		echo "document.getElementById('cbo_costing_per').value = '".$row[csf("costing_per")]."';\n";  
		echo "document.getElementById('txt_quotation_date').value = '".change_date_format($row[csf("quot_date")],'dd-mm-yyyy','-')."';\n";  
		echo "document.getElementById('txt_est_ship_date').value = '".change_date_format($row[csf("est_ship_date")],'dd-mm-yyyy','-')."';\n"; 
		echo "document.getElementById('txt_op_date').value = '".change_date_format($row[csf("op_date")],'dd-mm-yyyy','-')."';\n"; 
		echo "document.getElementById('txt_factory').value = '".$row[csf("factory")]."';\n";  
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n"; 
		echo "document.getElementById('garments_nature').value = '".$row[csf("garments_nature")]."';\n";  
		echo "document.getElementById('cbo_order_uom').value = '".$row[csf("order_uom")]."';\n";  
		echo "document.getElementById('item_id').value = '".$row[csf("gmts_item_id")]."';\n";  
		echo "document.getElementById('set_breck_down').value = '".$row[csf("set_break_down")]."';\n";  
		echo "document.getElementById('tot_set_qnty').value = '".$row[csf("total_set_qnty")]."';\n";  
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_approved_status').value = '".$row[csf("approved")]."';\n"; 
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('txt_sew_smv').value = '".$row[csf("sew_smv")]."';\n";
		echo "document.getElementById('txt_cut_smv').value = '".$row[csf("cut_smv")]."';\n";
		echo "document.getElementById('txt_sew_efficiency_per').value = '".$row[csf("sew_effi_percent")]."';\n";
		echo "document.getElementById('txt_cut_efficiency_per').value = '".$row[csf("cut_effi_percent")]."';\n";
		echo "document.getElementById('txt_efficiency_wastage').value = '".$row[csf("efficiency_wastage_percent")]."';\n";
		echo "document.getElementById('cbo_season_name').value = '".$row[csf("season_buyer_wise")]."';\n";
		
		echo "document.getElementById('txt_m_list').value = '".$row[csf("m_list")]."';\n";
		echo "document.getElementById('txt_bh_merchant').value = '".$row[csf("bh_merchant")]."';\n";
		echo "document.getElementById('cbo_quotation_status').value = '".$row[csf("quotation_status")]."';\n";

		echo "calculate_lead_time();\n";
		
		
		$cbo_approved_status= $row[csf("approved")];
		if($cbo_approved_status==1)
		{
		echo "document.getElementById('approve1').value = 'Un-Approved';\n"; 
	    echo "$('#cbo_company_name').attr('disabled','true')".";\n";
	    echo "$('#cbo_buyer_name').attr('disabled','true')".";\n";
	    echo "$('#txt_style_ref').attr('disabled','true')".";\n";
	    echo "$('#txt_revised_no').attr('disabled','true')".";\n";
		
		echo "$('#cbo_pord_dept').attr('disabled','true')".";\n";
	    echo "$('#txt_style_desc').attr('disabled','true')".";\n";
	    echo "$('#cbo_currercy').attr('disabled','true')".";\n";
	    echo "$('#cbo_agent').attr('disabled','true')".";\n";
	    echo "$('#txt_offer_qnty').attr('disabled','true')".";\n";
		
		echo "$('#cbo_region').attr('disabled','true')".";\n";
	    echo "$('#cbo_color_range').attr('disabled','true')".";\n";
	    echo "$('#cbo_inco_term').attr('disabled','true')".";\n";
	    echo "$('#txt_incoterm_place').attr('disabled','true')".";\n";
	    echo "$('#txt_machine_line').attr('disabled','true')".";\n";
		
		echo "$('#txt_prod_line_hr').attr('disabled','true')".";\n";
	    echo "$('#cbo_costing_per').attr('disabled','true')".";\n";
	    echo "$('#txt_quotation_date').attr('disabled','true')".";\n";
	    echo "$('#txt_est_ship_date').attr('disabled','true')".";\n";
		
		echo "$('#txt_factory').attr('disabled','true')".";\n";
	    echo "$('#txt_remarks').attr('disabled','true')".";\n";
	    echo "$('#cbo_costing_per').attr('disabled','true')".";\n";
	    echo "$('#garments_nature').attr('disabled','true')".";\n";
	    echo "$('#cbo_order_uom').attr('disabled','true')".";\n";
	    echo "$('#image_button').attr('disabled','true')".";\n";
	    echo "$('#set_button').attr('disabled','true')".";\n";
	    echo "$('#save1').attr('disabled','true')".";\n";
	    echo "$('#update1').attr('disabled','true')".";\n";
	    echo "$('#Delete1').attr('disabled','true')".";\n";
		}
		else
		{
		echo "document.getElementById('approve1').value = 'Approved';\n";
		echo "$('#txt_quotation_id').removeAttr('disabled')".";\n";
	    echo "$('#cbo_company_name').removeAttr('disabled')".";\n";
	    echo "$('#cbo_buyer_name').removeAttr('disabled')".";\n";
	    echo "$('#txt_style_ref').removeAttr('disabled')".";\n";
	    echo "$('#txt_revised_no').removeAttr('disabled')".";\n";
		echo "$('#cbo_pord_dept').removeAttr('disabled')".";\n";
	    echo "$('#txt_style_desc').removeAttr('disabled')".";\n";
	    echo "$('#cbo_currercy').removeAttr('disabled')".";\n";
	    echo "$('#cbo_agent').removeAttr('disabled')".";\n";
	    echo "$('#txt_offer_qnty').removeAttr('disabled')".";\n";
		echo "$('#cbo_region').removeAttr('disabled')".";\n";
	    echo "$('#cbo_color_range').removeAttr('disabled')".";\n";
	    echo "$('#cbo_inco_term').removeAttr('disabled')".";\n";
	    echo "$('#txt_incoterm_place').removeAttr('disabled')".";\n";
	    echo "$('#txt_machine_line').removeAttr('disabled')".";\n";
		echo "$('#txt_prod_line_hr').removeAttr('disabled')".";\n";
	    echo "$('#cbo_costing_per').removeAttr('disabled')".";\n";
	    echo "$('#txt_quotation_date').removeAttr('disabled')".";\n";
	    echo "$('#txt_est_ship_date').removeAttr('disabled')".";\n";
		echo "$('#txt_factory').removeAttr('disabled')".";\n";
	    echo "$('#txt_remarks').removeAttr('disabled')".";\n";
	    echo "$('#cbo_costing_per').removeAttr('disabled')".";\n";
	    echo "$('#garments_nature').removeAttr('disabled')".";\n";
	    echo "$('#cbo_order_uom').removeAttr('disabled')".";\n";
		echo "$('#image_button').removeAttr('disabled')".";\n";
	    echo "$('#set_button').removeAttr('disabled')".";\n";
	    echo "$('#save1').removeAttr('disabled')".";\n";
	    echo "$('#update1').removeAttr('disabled')".";\n";
	    echo "$('#Delete1').removeAttr('disabled')".";\n";

		}
	}
}
if($action=="inquery_mrr_popup")//Quotation Inquery
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);  
?>
     
<script>
	function js_set_value(mrr)
	{
 		$("#hidden_issue_number").val(mrr); // mrr number
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="500" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>
                <tr> 
                   <th width="150">Company Name</th>               	 
                    <th width="150">Buyer Name</th>
                    <th width="150" align="center" id="search_by_td_up">Style Reff.</th>
                    <th width="150">Inquery Date </th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr>
                <td>
                    <?
                        echo create_drop_down( "cbo_company_name", 172, "select comp.id,comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "",1);
                     ?> 
                
                
                </td>
                    <td>
                        <?  
							echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
                        ?>
                    </td>
                    <td width="" align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_style" id="txt_style" />	
                    </td>    
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:120px" placeholder="From Date" />
                        
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_date_from').value+'_'+<? echo $company; ?>, 'create_inquery_mrr_search_list_view', 'search_div', 'quotation_entry_simple_final_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="5">
				
                    <!-- Hidden field here-------->
                     <input type="hidden" id="hidden_issue_number" value="" />
                    <!-- ---------END------------->
                </td>
            </tr>    
            </tbody>
         </tr>         
        </table>    
        <div align="center" valign="top" id="search_div"> </div> 
        </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}
if($action=="create_inquery_mrr_search_list_view")
{
	
	$ex_data = explode("_",$data);
	$txt_buyer = $ex_data[0];
	$txt_style = $ex_data[1];
	$inq_date = $ex_data[2];
	$company = $ex_data[3];
    if($company==0) $company_name=""; else $company_name=" and company_id=$company";
	if($txt_buyer==0) $buyer_name=""; else $buyer_name="and buyer_id=$txt_buyer";
	if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce='".$txt_style."'";
	if( $inq_date!="" )  $sql_cond.= " and inquery_date='".change_date_format($inq_date,'yyyy-mm-dd',"-",1)."'";
	
	$buyer_arr = return_library_array("select id,buyer_name from  lib_buyer ","id","buyer_name");
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$arr=array(0=>$company_arr,1=>$buyer_arr,7=>$row_status);
	$sql = "select system_number_prefix_num,system_number, company_id,buyer_id,season_buyer_wise,inquery_date,style_refernce,status_active,extract(year from insert_date) as year ,id from wo_quotation_inquery where is_deleted=0 $company_name $buyer_name $sql_cond";
	//echo $sql;
	echo create_list_view("list_view", "Company Name,Buyer Name,System Num,Style Reff., Inquery Date,Season,Year,Status","120,120,80,120,120,120,60,100","900","260",0, $sql , "js_set_value", "system_number,id", "", 1, "company_id,buyer_id,0,0,0,0,0,status_active", $arr, "company_id,buyer_id,system_number_prefix_num,style_refernce,inquery_date,season_buyer_wise,year,status_active", "",'','0') ;
	?>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}
if($action=="populate_data_from_data_inquery")
{
	$data=explode("**",$data);
	
	$sql = sql_select("select  id,company_id,buyer_id,season_buyer_wise,inquery_date,style_refernce,status_active,remarks from wo_quotation_inquery where system_number='$data[0]'");
	//echo "select  id,company_id,buyer_id,season,inquery_date,style_refernce,status_active,remarks from wo_quotation_inquery where system_number='$data[0]'";
	foreach($sql as $row)
	{
		
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n"; 
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n"; 
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_refernce")]."';\n";
		echo "document.getElementById('cbo_season_name').value = '".$row[csf("season_buyer_wise")]."';\n";
		echo "document.getElementById('inquery_id').value = '".$row[csf("id")]."';\n";
	}
}


if($action=="open_set_list_view")
{
echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode,'','');
extract($_REQUEST);

?>
<script>
function add_break_down_set_tr( i )
{
	var unit_id= document.getElementById('unit_id').value;
	if(unit_id==1)
	{
		alert('Only One Item');
		return false;	
	}
	var row_num=$('#tbl_set_details tr').length-1;
	if (row_num!=i)
	{
		return false;
	}
	
	if (form_validation('cboitem_'+i+'*txtsetitemratio_'+i,'Gmts Items*Set Ratio')==false)
	{
		return;
	}
	else
	{
		i++;
	 
		 $("#tbl_set_details tr:last").clone().find("input,select,a").each(function() {
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }              
			});
		  }).end().appendTo("#tbl_set_details");
		  
		  $('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_set_tr("+i+")");
		  $('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_delete_down_tr("+i+",'tbl_set_details')");
		  $('#cboitem_'+i).val(''); 
		  set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );

	}
		 
		  
}


function fn_delete_down_tr(rowNo,table_id) 
{   
	
	if(table_id=='tbl_set_details')
	{
		var numRow = $('table#tbl_set_details tbody tr').length; 
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_set_details tbody tr:last').remove();
		}
		/*else
		{
																																																																																																																																																																																																																																																																																																																																									reset_form('','','txtordernumber_'+rowNo+'*txtorderqnty_'+rowNo+'*txtordervalue_'+rowNo+'*txtattachedqnty_'+rowNo+'*txtattachedvalue_'+rowNo+'*txtstyleref_'+rowNo+'*txtitemname_'+rowNo+'*txtjobno_'+rowNo+'*hiddenwopobreakdownid_'+rowNo+'*hiddenunitprice_'+rowNo+'*totalOrderqnty*totalOrdervalue*totalAttachedqnty*totalAttachedvalue');
		} */
		 //set_all_onclick();
		 set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
		  //set_sum_value( 'cons_sum', 'cons_'  );
		  //set_sum_value( 'processloss_sum', 'processloss_'  );
		  //set_sum_value( 'requirement_sum', 'requirement_');
          //set_sum_value( 'pcs_sum', 'pcs_');
	}
	
	
}


function set_sum_value_set(des_fil_id,field_id)
{
	var rowCount = $('#tbl_set_details tr').length-1;
	math_operation( des_fil_id, field_id, '+', rowCount );
}

function js_set_value_set()
{
	var rowCount = $('#tbl_set_details tr').length-1;
	var set_breck_down="";
	var item_id=""
	for(var i=1; i<=rowCount; i++)
	{
		if (form_validation('cboitem_'+i+'*txtsetitemratio_'+i,'Gmts Items*Set Ratio')==false)
		{
			return;
		}
			
		
		if(set_breck_down=="")
		{
			set_breck_down+=$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val();
			item_id+=$('#cboitem_'+i).val();
		  
		}
		else
		{
			set_breck_down+="__"+$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val();
			item_id+=","+$('#cboitem_'+i).val();

		}
		
	}
	document.getElementById('set_breck_down').value=set_breck_down;
	document.getElementById('item_id').value=item_id;

	parent.emailwindow.hide();
}
</script>
</head>
<body>
       <div id="set_details"  align="center">   
    	<fieldset>
        	<form id="setdetails_1" autocomplete="off">
            <input type="hidden" id="set_breck_down" />     
            <input type="hidden" id="item_id" /> 
            <input type="hidden" id="unit_id" value="<? echo $unit_id;  ?>" />        	
       	
            <table width="800" cellspacing="0" class="rpt_table" border="0" id="tbl_set_details" rules="all">
                	<thead>
                    	<tr>
                        	<th width="250">Item</th><th  width="200">Set Item Ratio</th><th width=""></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					
					$data_array=explode("__",$set_breck_down);
					if($data_array[0]=="")
					{
						$data_array=array();
					}
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							$data=explode('_',$row);
							?>
                            	<tr id="settr_1" align="center">
                                    <td>
									<? 
									echo create_drop_down( "cboitem_".$i, 250, $garments_item, "",1," -- Select Item --", $data[0], "",'','' ); 
									?>
                                    
                                    </td>
                                    <td>
                                    <input type="text" id="txtsetitemratio_<? echo $i;?>"   name="txtsetitemratio_<? echo $i;?>" style="width:190px"  class="text_boxes_numeric" onChange="set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' )"  value="<? echo $data[1] ?>" <? if ($unit_id==1){echo "readonly";} else{echo "";}?> /> 
                                    </td>
                                   
                                  
                                    <td>
                                    <input type="button" id="increaseset_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decreaseset_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(<? echo $i; ?> ,'tbl_set_details' );" />
                                     </td> 
                                </tr>
                            
                            <?
							 
						}
					}
					else
					{
						
					?>
                    <tr id="settr_1" align="center">
                                   <td>
									<? 
									echo create_drop_down( "cboitem_1", 240, $garments_item, "",1,"--Select--", 0, '','','' ); 
									?>
                                    </td>
                                     <td>
                                    <input type="text" id="txtsetitemratio_1" name="txtsetitemratio_1" style="width:190px" class="text_boxes_numeric" onChange="set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' )" value="<? if ($unit_id==1) {echo "1";} else{echo "";}?>"  <? if ($unit_id==1){echo "readonly";} else{echo "";}?>  /> 
                                     </td>
                                   
                                    <td>
                                    <input type="button" id="increaseset_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(1)" />
                                    <input type="button" id="decreaseset_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(1 ,'tbl_set_details' );" />
                                    </td> 
                                </tr>
                    <? 
					
					} 
					?>
                </tbody>
                </table>
                <table width="800" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                            <th width="250">Total</th>
                            <th  width="200"><input type="text" id="tot_set_qnty" name="tot_set_qnty"  class="text_boxes_numeric" style="width:190px"  value="<? if($tot_set_qnty !=''){ echo $tot_set_qnty;} else{ echo 1;} ?>" readonly  /></th>
                            <th width=""></th>
                        </tr>
                    </tfoot>
                </table>
                           
                <table width="800" cellspacing="0" class="" border="0">
                
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
                        
						        <input type="button" class="formbutton" value="Close" onClick="js_set_value_set()"/> 

                        </td> 
                    </tr>
                </table>
               
            </form>
        </fieldset>
        </div>
 </body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

<?
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			$id=return_next_id( "id", "wo_pri_sim_mst", 1 ) ;
			$field_array="id,inquery_sys_id,inquery_id,company_id, buyer_id, style_ref, revised_no, pord_dept,product_code, style_desc, currency, agent, offer_qnty, region, color_range, incoterm, incoterm_place, machine_line, prod_line_hr,  costing_per, quot_date, est_ship_date,op_date, factory, remarks, garments_nature,order_uom,gmts_item_id,set_break_down,total_set_qnty,cm_cost_predefined_method_id,exchange_rate,sew_smv,cut_smv,sew_effi_percent,cut_effi_percent,efficiency_wastage_percent,season_buyer_wise,m_list,bh_merchant,quotation_status, inserted_by, insert_date, status_active, is_deleted";
			$data_array="(".$id.",".$txt_quotation_inquery_sys_id.",".$inquery_id.",".$cbo_company_name.",".$cbo_buyer_name.",".$txt_style_ref.",".$txt_revised_no.",".$cbo_pord_dept.",".$txt_product_code.",".$txt_style_desc.",".$cbo_currercy.",".$cbo_agent.",".$txt_offer_qnty.",".$cbo_region.",".$cbo_color_range.",".$cbo_inco_term.",".$txt_incoterm_place.",".$txt_machine_line.",".$txt_prod_line_hr.",".$cbo_costing_per.",".$txt_quotation_date.",".$txt_est_ship_date.",".$txt_op_date.",".$txt_factory.",".$txt_remarks.",".$garments_nature.",".$cbo_order_uom.",".$item_id.",".$set_breck_down.",".$tot_set_qnty.",".$cm_cost_predefined_method_id.",".$txt_exchange_rate.",".$txt_sew_smv.",".$txt_cut_smv.",".$txt_sew_efficiency_per.",".$txt_cut_efficiency_per.",".$txt_efficiency_wastage.",".$cbo_season_name.",".$txt_m_list.",".$txt_bh_merchant.",".$cbo_quotation_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
				$field_array1="id,quotation_id, gmts_item_id,set_item_ratio";
				$add_comma=0;
				$id1=return_next_id( "id", "wo_pri_sim_mst_set_dtls", 1 ) ;
				$set_breck_down_array=explode('__',str_replace("'",'',$set_breck_down));
				for($c=0;$c < count($set_breck_down_array);$c++)
				{
					$set_breck_down_arr=explode('_',$set_breck_down_array[$c]);
					if ($add_comma!=0) $data_array1 .=",";
					$data_array1 .="(".$id1.",".$id.",'".$set_breck_down_arr[0]."','".$set_breck_down_arr[1]."')";
					$add_comma++;
					$id1=$id1+1;
				}
			$rID=sql_insert("wo_pri_sim_mst",$field_array,$data_array,0);
			$rID1=sql_insert("wo_pri_sim_mst_set_dtls",$field_array1,$data_array1,1);

			if($db_type==0)
			{
			if(str_replace("'","",$cbo_order_uom)==58 || str_replace("'","",$cbo_order_uom)==57 )
			{
				if($rID==1 && $rID1==1){
					mysql_query("COMMIT");  
					echo "0**".$id;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$id;
				}
			}
			if(str_replace("'","",$cbo_order_uom)==1)
			{
				if($rID==1){
					mysql_query("COMMIT");  
					echo "0**".$id;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$id;
				}
			}
			}
			
			if($db_type==2 || $db_type==1 )
			{
				if(str_replace("'","",$cbo_order_uom)==58 || str_replace("'","",$cbo_order_uom)==57 )
				{
					if($rID==1 && $rID1==1){
						oci_commit($con);
						echo "0**".$id;
					}
					else{
						oci_rollback($con); 
						echo "10**".$id;
					}
				}
				if(str_replace("'","",$cbo_order_uom)==1)
				{
					if($rID==1){
						oci_commit($con);
						echo "0**".$id;
					}
					else{
						oci_rollback($con); 
						echo "10**".$id;
					}
				}
			}
			disconnect($con);
			die;
	}
	
	
	else if ($operation==1)   
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			 	
			$field_array="inquery_sys_id*inquery_id*company_id*buyer_id*style_ref*revised_no*pord_dept*product_code*style_desc*currency*agent*offer_qnty*region*color_range*incoterm*incoterm_place*machine_line* prod_line_hr*costing_per*quot_date*est_ship_date*op_date*factory*remarks*garments_nature*order_uom*gmts_item_id*set_break_down*total_set_qnty*cm_cost_predefined_method_id*exchange_rate*sew_smv*cut_smv*sew_effi_percent*cut_effi_percent*efficiency_wastage_percent*season_buyer_wise*m_list*bh_merchant*quotation_status*updated_by*update_date*status_active* is_deleted";
			$data_array="".$txt_quotation_inquery_sys_id."*".$inquery_id."*".$cbo_company_name."*".$cbo_buyer_name."*".$txt_style_ref."*".$txt_revised_no."*".$cbo_pord_dept."*".$txt_product_code."*".$txt_style_desc."*".$cbo_currercy."*".$cbo_agent."*".$txt_offer_qnty."*".$cbo_region."*".$cbo_color_range."*".$cbo_inco_term."*".$txt_incoterm_place."*".$txt_machine_line."*".$txt_prod_line_hr."*".$cbo_costing_per."*".$txt_quotation_date."*".$txt_est_ship_date."*".$txt_op_date."*".$txt_factory."*".$txt_remarks."*".$garments_nature."*".$cbo_order_uom."*".$item_id."*".$set_breck_down."*".$tot_set_qnty."*".$cm_cost_predefined_method_id."*".$txt_exchange_rate."*".$txt_sew_smv."*".$txt_cut_smv."*".$txt_sew_efficiency_per."*".$txt_cut_efficiency_per."*".$txt_efficiency_wastage."*".$cbo_season_name."*".$txt_m_list."*".$txt_bh_merchant."*".$cbo_quotation_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'1'*'0'";
				$field_array1="id,quotation_id, gmts_item_id,set_item_ratio";
				$add_comma=0;
				$id1=return_next_id( "id", "wo_pri_sim_mst_set_dtls", 1 ) ;
				$set_breck_down_array=explode('__',str_replace("'",'',$set_breck_down));
				for($c=0;$c < count($set_breck_down_array);$c++)
				{
					$set_breck_down_arr=explode('_',$set_breck_down_array[$c]);
					if ($add_comma!=0) $data_array1 .=",";
					$data_array1 .="(".$id1.",".$update_id.",'".$set_breck_down_arr[0]."','".$set_breck_down_arr[1]."')";
					$add_comma++;
					$id1=$id1+1;
				}
			$rID=sql_update("wo_pri_sim_mst",$field_array,$data_array,"id","".$update_id."",0);
			$rID1=execute_query( "delete from wo_pri_sim_mst_set_dtls where  quotation_id =".$update_id."",0);
			$rID2=sql_insert("wo_pri_sim_mst_set_dtls",$field_array1,$data_array1,1);
			if($db_type==0)
			{
				if(str_replace("'","",$cbo_order_uom)==58 || str_replace("'","",$cbo_order_uom)==57)
				{
					if($rID==1 && $rID1==1 && $rID2==1  ){
						mysql_query("COMMIT");  
						echo "1**".str_replace("'","",$update_id);
					}
					else{
						mysql_query("ROLLBACK"); 
						echo "10**".str_replace("'","",$update_id);
					}
				}
				
				if(str_replace("'","",$cbo_order_uom)==1)
				{
					if($rID ){
						mysql_query("COMMIT");  
						echo "1**".str_replace("'","",$update_id);
					}
					else{
						mysql_query("ROLLBACK"); 
						echo "10**".str_replace("'","",$update_id);
					}
				}
			}
			if($db_type==2 || $db_type==1 )
			{
			    if(str_replace("'","",$cbo_order_uom)==58 || str_replace("'","",$cbo_order_uom)==57)
				{
					if($rID==1 && $rID1==1 && $rID2==1  ){
						oci_commit($con);
						echo "1**".str_replace("'","",$update_id);
					}
					else{
						oci_rollback($con); 
						echo "10**".str_replace("'","",$update_id);
					}
				}
				
				if(str_replace("'","",$cbo_order_uom)==1)
				{
					if($rID ){
						oci_commit($con);
						echo "1**".str_replace("'","",$update_id);
					}
					else{
						oci_rollback($con); 
						echo "10**".str_replace("'","",$update_id);
					}
				}
			}
			disconnect($con);
			die;
	}
	
	else if ($operation==2)  
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$rID=execute_query( "delete from wo_pri_sim_mst where  id =".$update_id."",0);
			$rID=execute_query( "delete from wo_pri_sim_mst_set_dtls where  quotation_id =".$update_id."",0);
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "2**".str_replace("'","",$update_id);
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".str_replace("'","",$update_id);
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				if($rID && $rID1 ){
					oci_commit($con);
					echo "2**".str_replace("'","",$update_id);
				}
				else{
					oci_rollback($con);
					echo "10**".str_replace("'","",$update_id);
				}
			}
			disconnect($con);
			die;
		
	}
	
	
	else if ($operation==3)   // Update Here
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			$field_array="approved*approved_by*approved_date*updated_by*update_date";
			if(trim(str_replace("'","",$cbo_approved_status))==2) 
			{
				$data_array="'1'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
			}
			else 
			{
				$data_array="'0'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
				
			}						
		    $rID=sql_update("wo_pri_sim_mst",$field_array,$data_array,"id",$update_id,1); 
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "2**".str_replace("'","",$update_id)."**".trim(str_replace("'","",$cbo_approved_status));
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".str_replace("'","",$update_id)."**".trim(str_replace("'","",$cbo_approved_status));
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				if($rID ){
					oci_commit($con); 
					echo "2**".str_replace("'","",$update_id)."**".trim(str_replace("'","",$cbo_approved_status));
				}
				else{
					oci_rollback($con);
					echo "10**".str_replace("'","",$update_id)."**".trim(str_replace("'","",$cbo_approved_status));
				}
			}
			disconnect($con);
			die;
	}
}
//Master Table End ====================================================================================================================================================
if($action=="generate_table")
{
	$header_array=array(1=>"B",2=>"C",3=>"D",4=>"E",5=>"F",6=>"G",7=>"H",8=>"I",9=>"J",10=>"K",11=>"L",12=>"M",13=>"N",14=>"O",15=>"P",16=>"Q",17=>"R",18=>"S",19=>"T",20=>"U",21=>"V",22=>"W",23=>"X",24=>"Y",25=>"Z");
	$data=explode("_",$data);
	$num_row=$data[0];
	$num_col=$data[1];
	//$td_with=floor((1100-($num_col*15))/$num_col);
	$td_with=100;
	$table_width=($num_col*$td_with)+200;
	?>
    <strong>
    FABRIC & YARN DETAILS
    </strong>
    <input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr('table_1')" />
    <input type="button" id="increaseconversion_2" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr('table_1')"/>
     Click Last Column Header To Remove Last Column

	<table class="rpt_table" border="0" cellpadding="0" cellspacing="0" style="width:<? echo $table_width; ?>px;text-align:center;" rules="all" id="table_1">
    <thead>	
    <tr>
    <th style="width:200px;">
     A
    </th>
     <?
	for($col=1;$col <= $num_col; $col++)
	{
	?>
    <th style="width:<? echo $td_with; ?>px;" onClick="delete_column(this)">
    <? echo $header_array[$col];?>
    </th>
    <?
	}
    ?>
    </tr>
    </thead>
    <tbody>
    <?
	for($row=1;$row <= $num_row; $row++)
	{
		if($row==1)
		{
			$value="Particulars";
			$class="text_boxes";
		}
		else
		{
			$value="";
			$class="text_boxes_numeric";
		}
	?>
    <tr class="mythingy">
    <td style="width:200px;">
    <input class="text_boxes"  style="width:200px;"  id="A_<? echo $row?>" onClick="id_detection(this.id)" value="<? echo $value;  ?>"/> 
    </td>
     <?
	for($col=1;$col <= $num_col; $col++)
	{
	?>
    <td style="width:<? echo $td_with; ?>px;">
    <input class="<? echo $class; ?>"  style="width:<? echo $td_with; ?>px;"  id="<? echo $header_array[$col]."_".$row; ?>" onChange="sum_value(this.id,'table_1')" onClick="id_detection(this.id)"/> 
    </td>
    <?
	}
    ?>
    </tr>
    <?
	}
	?>
    </tbody>
    <tfoot>	
    <tr>
    <th style="width:200px;">
     Total fabric Cons
    </th>
     <?
	for($col=1;$col <= $num_col; $col++)
	{
	?>
    <th style="width:<? echo $td_with; ?>px;">
     <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "sum1_".$header_array[$col];?>" /> 
    </th>
    <?
	}
    ?>
    </tr>
    <tr>
    <th style="width:200px;">
     Fabric Cons <input class="text_boxes_numeric"  style="width:30px;" id="Fper_1" onChange="claculate_percent(1)" />
    </th>
     <?
	for($col=1;$col <= $num_col; $col++)
	{
	?>
    <th style="width:<? echo $td_with; ?>px;">
     <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;" id="<? echo "sum2_".$header_array[$col];?>" /> 
    </th>
    <?
	}
    ?>
    </tr>
    <tr>
    <th style="width:200px;">
    Yarn Cons <input class="text_boxes_numeric"  style="width:30px;" id="Yper_1" onChange="claculate_percent(2)"  />
    </th>
     <?
	for($col=1;$col <= $num_col; $col++)
	{
	?>
    <th style="width:<? echo $td_with; ?>px;">
     <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "sum3_".$header_array[$col];?>"/> 
    </th>
    <?
	}
    ?>
    </tr>
    </tfoot>
    </table>
    
    
    <strong>Per Kg Fabric Cost Details</strong>
    <input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr('table_2')" />
    <input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr('table_2')"/>
    <table class="rpt_table" border="0" cellpadding="0" cellspacing="0" style="width:<? echo $table_width; ?>px;text-align:center;" rules="all" id="table_2">
    <tbody>
    <?
	for($row=1;$row <= $num_row; $row++)
	{
		if($row==1)
		{
			$class="text_boxes";
		}
		else
		{
			$class="text_boxes_numeric";
		}
	?>
    <tr class="mythingy">
    <td style="width:200px;">
    <input class="text_boxes"  style="width:200px;"  id="AY_<? echo $row?>" onClick="id_detection(this.id)"//> 
    </td>
     <?
	for($col=1;$col <= $num_col; $col++)
	{
	?>
    <td style="width:<? echo $td_with; ?>px;">
    <input class="<? echo $class; ?>"  style="width:<? echo $td_with; ?>px;"  id="<? echo $header_array[$col]."Y_".$row; ?>" onChange="sum_value(this.id,'table_2')" onClick="id_detection(this.id)"/> 
    </td>
    <?
	}
    ?>
    </tr>
    <?
	}
	?>
    </tbody>
    <tfoot>	
    <tr>
    <th style="width:200px;">
     Fabric Price/KG
    </th>
     <?
	for($col=1;$col <= $num_col; $col++)
	{
	?>
    <th style="width:<? echo $td_with; ?>px;">
     <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "sum4_".$header_array[$col]."Y";?>" /> 
    </th>
    <?
	}
    ?>
    </tr>
    <tr>
    <th style="width:200px;">
     Total Fabric Cost/Dzn
    </th>
     <?
	for($col=1;$col <= $num_col; $col++)
	{
	?>
    <th style="width:<? echo $td_with; ?>px;">
     <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;" id="<? echo "sum5_".$header_array[$col]."Y";?>" /> 
    </th>
    <?
	}
    ?>
    </tr>
    <tr>
    <th style="width:200px;">
     CM Cost/Dzn
    </th>
     <?
	for($col=1;$col <= $num_col; $col++)
	{
	?>
    <th style="width:<? echo $td_with; ?>px;">
     <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;" id="<? echo "CM_".$header_array[$col]."Y";?>" onChange="total_garments_cost_dzn()" /> 
    </th>
    <?
	}
    ?>
    </tr>
    </tfoot>
    </table>
    <strong>Trims & Other Fabric Cost Details</strong>
    <input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr('table_3')" />
    <input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr('table_3')"/>
    <table class="rpt_table" border="0" cellpadding="0" cellspacing="0" style="width:<? echo $table_width; ?>px;text-align:center;" rules="all" id="table_3">
    <tbody>
    <?
	for($row=1;$row <= $num_row; $row++)
	{
	?>
    <tr class="mythingy">
    <td style="width:200px;">
    <input class="text_boxes"  style="width:200px;"  id="AT_<? echo $row?>" onClick="id_detection(this.id)"//> 
    </td>
     <?
	for($col=1;$col <= $num_col; $col++)
	{
	?>
    <td style="width:<? echo $td_with; ?>px;">
    <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo $header_array[$col]."T_".$row; ?>" onChange="sum_value(this.id,'table_3')" onClick="id_detection(this.id)"/> 
    </td>
    <?
	}
    ?>
    </tr>
    <?
	}
	?>
    </tbody>
    <tfoot>	
    <tr>
    <th style="width:200px;">
     Total 
    </th>
     <?
	for($col=1;$col <= $num_col; $col++)
	{
	?>
    <th style="width:<? echo $td_with; ?>px;">
     <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "sum6_".$header_array[$col]."T";?>" /> 
    </th>
    <?
	}
    ?>
    </tr>
    
    <tr>
    <th style="width:200px;">
     Total Garments Cost/Dzn
    </th>
     <?
	for($col=1;$col <= $num_col; $col++)
	{
	?>
    <th style="width:<? echo $td_with; ?>px;">
     <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "sum7_".$header_array[$col]."T";?>" /> 
    </th>
    <?
	}
    ?>
    </tr>
    <tr>
    <th style="width:200px;">
     Total Garments Cost/Pcs
    </th>
     <?
	for($col=1;$col <= $num_col; $col++)
	{
	?>
    <th style="width:<? echo $td_with; ?>px;">
     <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;" id="<? echo "sum8_".$header_array[$col]."T";?>" /> 
    </th>
    <?
	}
    ?>
    </tr>
    <tr>
    <th style="width:200px;">
    Commision <input class="text_boxes_numeric"  style="width:30px;" id="Cper_1" onChange="claculate_percent(3)"  />
    </th>
     <?
	for($col=1;$col <= $num_col; $col++)
	{
	?>
    <th style="width:<? echo $td_with; ?>px;">
     <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "sum9_".$header_array[$col]."T";?>"/> 
    </th>
    <?
	}
    ?>
    </tr>
    <tr>
    <th style="width:200px;">
    Terget Price
    </th>
     <?
	for($col=1;$col <= $num_col; $col++)
	{
	?>
    <th style="width:<? echo $td_with; ?>px;">
     <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "sum10_".$header_array[$col]."T";?>"/> 
    </th>
    <?
	}
    ?>
    </tr>
    </tfoot>
    </table>
    
    
    <br/>
    <div>
    <div style="width:433px; float:left;">
    <table width="433" cellspacing="0" cellpadding=""  border="0" class="rpt_table" rules="all" id="fabrication_table">
    <thead>
    <tr>
    <th width="170" style="text-align:left">Fabrication</th>
    <th width="170">
    </th>
    <th width="80">
    </th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="txtfabricationA_1" id="txtfabricationA_1" maxlength="100" title="Maximum 100 Character" value="Color"/></td>
    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="txtfabricationB_1" id="txtfabricationB_1" maxlength="100" title="Maximum 100 Character"/></td>
    <td width="80">
    <input type="button" id="increasefabrication_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'fabrication_table')" />
    <input type="button" id="decreasefabrication_1" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'fabrication_table')"/>
    </td>
    </tr>
    <tr>
    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="txtfabricationA_2" id="txtfabricationA_2" maxlength="100" title="Maximum 100 Character" value="Fabrication"/></td>
    
    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="txtfabricationB_2" id="txtfabricationB_2" maxlength="100" title="Maximum 100 Character"/></td>
    <td width="80">
    <input type="button" id="increasefabrication_2" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'fabrication_table')" />
    <input type="button" id="decreasefabrication_2" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'fabrication_table')"/>
    </td>
    </tr>
     <tr>
    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="txtfabricationA_3" id="txtfabricationA_3" maxlength="100" title="Maximum 100 Character" value="Composition"/></td>
    
    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="txtfabricationB_3" id="txtfabricationB_3" maxlength="100" title="Maximum 100 Character"/></td>
    <td width="80">
    <input type="button" id="increasefabrication_3" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'fabrication_table')" />
    <input type="button" id="decreasefabrication_3" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'fabrication_table')"/>
    </td>
    </tr>
    <tr>
    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="txtfabricationA_4" id="txtfabricationA_4" maxlength="100" title="Maximum 100 Character" value="Fabric Weight"/></td>
    
    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="txtfabricationB_4" id="txtfabricationB_4" maxlength="100" title="Maximum 100 Character"/></td>
    <td width="80">
    <input type="button" id="increasefabrication_4" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'fabrication_table')" />
    <input type="button" id="decreasefabrication_4" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'fabrication_table')"/>
    </td>
    </tr>
    </tbody>
    </table>
    </div>
    <div style="width:433px;  float:right;">
    <table width="433" cellspacing="0" cellpadding=""  border="0" class="rpt_table" rules="all" id="measurment_table">
    <thead>
    <tr>
    <th width="170" style="text-align:left">Measurment</th>
    <th width="170">
    </th>
    <th width="80">
    </th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="measurmentA_1" id="measurmentA_1" maxlength="100" title="Maximum 100 Character" value="HSP LENGHT"/></td>
    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="measurmentB_1" id="measurmentB_1" maxlength="100" title="Maximum 100 Character"/></td>
    <td width="80">
    <input type="button" id="increasemeasurment_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'measurment_table')" />
    <input type="button" id="decreasemeasurment_1" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'measurment_table')"/>
    </td>
    </tr>
    <tr>
    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="measurmentA_2" id="measurmentA_2" maxlength="100" title="Maximum 100 Character" value="1/2 CHEST"/></td>
    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="measurmentB_2" id="measurmentB_2" maxlength="100" title="Maximum 100 Character"/></td>
    <td width="80">
    <input type="button" id="increasemeasurment_2" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'measurment_table')" />
    <input type="button" id="decreasemeasurment_2" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'measurment_table')"/>
    </td>
    </tr>
    <tr>
    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="measurmentA_3" id="measurmentA_3" maxlength="100" title="Maximum 100 Character" value="1/2 Bottom"/></td>
    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="measurmentB_3" id="measurmentB_3" maxlength="100" title="Maximum 100 Character"/></td>
    <td width="80">
    <input type="button" id="increasemeasurment_3" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'measurment_table')" />
    <input type="button" id="decreasemeasurment_3" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'measurment_table')"/>
    </td>
    </tr>
    <tr>
    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="measurmentA_4" id="measurmentA_4" maxlength="100" title="Maximum 100 Character" value="1/2 HIP"/></td>
    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="measurmentB_4" id="measurmentB_4" maxlength="100" title="Maximum 100 Character"/></td>
    <td width="80">
    <input type="button" id="increasemeasurment_4" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'measurment_table')" />
    <input type="button" id="decreasemeasurment_4" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'measurment_table')"/>
    </td>
    </tr>
    
    </tbody>
    </table>
    </div>
    <div style="overflow:hidden;">
    <table width="433" cellspacing="0" cellpadding=""  border="0" class="rpt_table" rules="all" id="marker_table">
   <thead>
    <tr>
    <th width="170" style="text-align:left">Marker</th>
    <th width="170">
    </th>
    <th width="80">
    </th>
    </tr>
    </thead>
    <tbody>
    <tr>
    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="markerA_1" id="markerA_1" maxlength="100" title="Maximum 500 Character" value="Width"/></td>
    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="markerB_1" id="markerB_1" maxlength="100" title="Maximum 500 Character"/></td>
    <td width="80">
    <input type="button" id="increasemarker_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'marker_table')" />
    <input type="button" id="decreasemarker_1" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'marker_table')"/>
    </td>
    </tr>
    <tr>
    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="markerA_2" id="markerA_2" maxlength="100" title="Maximum 500 Character" value="Length"/></td>
    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="markerB_2" id="markerB_2" maxlength="100" title="Maximum 500 Character"/></td>
    <td width="80">
    <input type="button" id="increasemarker_2" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'marker_table')" />
    <input type="button" id="decreasemarker_2" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'marker_table')"/>
    </td>
    </tr>
    <tr>
    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="markerA_3" id="markerA_3" maxlength="100" title="Maximum 500 Character" value="Pcs"/></td>
    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="markerB_3" id="markerB_3" maxlength="100" title="Maximum 500 Character"/></td>
    <td width="80">
    <input type="button" id="increasemarker_3" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'marker_table')" />
    <input type="button" id="decreasemarker_3" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'marker_table')"/>
    </td>
    </tr>
    </tbody>
    </table>
    </div>
    </div>
    
    <br/>
    <table class="rpt_table" border="0" cellpadding="0" cellspacing="0" style="width:1320px;text-align:center;" rules="all">
    <tr>
    <td>
    <?
    echo load_submit_buttons( $permission, "fnc_simple_pri_dtls", 0,0 ,"",1,1) ;
    ?>
    </td>
    </tr>
    </table>
<?
}



if($action=="save_update_delet_simple_pri")
{
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//echo 0;
	//die;
	if($operation==0)
	{
			$con = connect();
			if($db_type==0)
			{
			mysql_query("BEGIN");
			}
			
			//,col_f,col_g,col_h,col_i,col_j,col_k,col_l,col_m,col_n,col_o,col_p,col_q,col_r,col_s,col_t,col_u,col_v,col_w, col_x,col_y,col_z;
			$field_array1="id,mst_id,type,col_a,col_b,col_c,col_d,col_e,inserted_by,insert_date";
			
			$id=return_next_id( "id", "wo_pri_fabric_yarn_details", 1 ) ;
			$row_table_1_tbody=explode("__",$table_1_tbody);
			for($i=0;$i<count($row_table_1_tbody); $i++)
			{
				$col_table_1_tbody=explode("_",$row_table_1_tbody[$i]);
				$data_table_1_tbody .="(".$id.",".$mst_id.",'tbody','".$col_table_1_tbody[0]."','".$col_table_1_tbody[1]."','".$col_table_1_tbody[2]."','".$col_table_1_tbody[3]."','".$col_table_1_tbody[4]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'),";
				$id++;
			}
			
			
			
			$row_table_1_tfoot=explode("__",$table_1_tfoot);
			for($i=0;$i<count($row_table_1_tfoot); $i++)
			{
				$col_table_1_tfoot=explode("_",$row_table_1_tfoot[$i]);
				$data_table_1_tfoot .="(".$id.",".$mst_id.",'tfoot','".$col_table_1_tfoot[0]."','".$col_table_1_tfoot[1]."','".$col_table_1_tfoot[2]."','".$col_table_1_tfoot[3]."','".$col_table_1_tfoot[4]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'),";
				$id++;
			}
			
			$data_table_1_tbody=rtrim($data_table_1_tbody,",");
			$data_table_1_tfoot=rtrim($data_table_1_tfoot,",");
			
			$rID=execute_query( "delete from wo_pri_fabric_yarn_details where  mst_id =".$mst_id."",1);
			$rID1=sql_insert("wo_pri_fabric_yarn_details",$field_array1,$data_table_1_tbody,1); 
			$rID1=sql_insert("wo_pri_fabric_yarn_details",$field_array1,$data_table_1_tfoot,1); 
			
			
			//echo "INSERT INTO wo_pri_fabric_yarn_details(".$field_array1.") VALUES ".$data_array1."";  die;
			//mysql_query("COMMIT");  
			//echo "0"; die;
			//==================================================================
			
			$id=return_next_id( "id", "wo_pri_body_fabric_cost", 1 ) ;
			$row_table_2_tbody=explode("__",$table_2_tbody);
			for($i=0;$i<count($row_table_2_tbody); $i++)
			{
				$col_table_2_tbody=explode("_",$row_table_2_tbody[$i]);
				$data_table_2_tbody .="(".$id.",".$mst_id.",'tbody','".$col_table_2_tbody[0]."','".$col_table_2_tbody[1]."','".$col_table_2_tbody[2]."','".$col_table_2_tbody[3]."','".$col_table_2_tbody[4]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'),";
				$id++;
			}
			
			
			
			$row_table_2_tfoot=explode("__",$table_2_tfoot);
			for($i=0;$i<count($row_table_2_tfoot); $i++)
			{
				$col_table_2_tfoot=explode("_",$row_table_2_tfoot[$i]);
				$data_table_2_tfoot .="(".$id.",".$mst_id.",'tfoot','".$col_table_2_tfoot[0]."','".$col_table_2_tfoot[1]."','".$col_table_2_tfoot[2]."','".$col_table_2_tfoot[3]."','".$col_table_2_tfoot[4]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'),";
				$id++;
			}
			
			$data_table_2_tbody=rtrim($data_table_2_tbody,",");
			$data_table_2_tfoot=rtrim($data_table_2_tfoot,",");
			
			$rID=execute_query( "delete from wo_pri_body_fabric_cost where  mst_id =".$mst_id."",1);
			$rID1=sql_insert("wo_pri_body_fabric_cost",$field_array1,$data_table_2_tbody,1); 
			$rID1=sql_insert("wo_pri_body_fabric_cost",$field_array1,$data_table_2_tfoot,1); 
			
			
			//echo "INSERT INTO wo_pri_fabric_yarn_details(".$field_array1.") VALUES ".$data_array1."";  die;
			//mysql_query("COMMIT");  
			//echo "0"; die;
			//==================================================================
			
			//==================================================================
			
			$id=return_next_id( "id", " wo_pri_other_fabric_cost", 1 ) ;
			$row_table_3_tbody=explode("__",$table_3_tbody);
			for($i=0;$i<count($row_table_3_tbody); $i++)
			{
				$col_table_3_tbody=explode("_",$row_table_3_tbody[$i]);
				$data_table_3_tbody .="(".$id.",".$mst_id.",'tbody','".$col_table_3_tbody[0]."','".$col_table_3_tbody[1]."','".$col_table_3_tbody[2]."','".$col_table_3_tbody[3]."','".$col_table_3_tbody[4]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'),";
				$id++;
			}
			
			
			
			$row_table_3_tfoot=explode("__",$table_3_tfoot);
			for($i=0;$i<count($row_table_3_tfoot); $i++)
			{
				$col_table_3_tfoot=explode("_",$row_table_3_tfoot[$i]);
				$data_table_3_tfoot .="(".$id.",".$mst_id.",'tfoot','".$col_table_3_tfoot[0]."','".$col_table_3_tfoot[1]."','".$col_table_3_tfoot[2]."','".$col_table_3_tfoot[3]."','".$col_table_3_tfoot[4]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'),";
				$id++;
			}
			
			$data_table_3_tbody=rtrim($data_table_3_tbody,",");
			$data_table_3_tfoot=rtrim($data_table_3_tfoot,",");
			
			$rID=execute_query( "delete from  wo_pri_other_fabric_cost where  mst_id =".$mst_id."",1);
			$rID1=sql_insert(" wo_pri_other_fabric_cost",$field_array1,$data_table_3_tbody,1); 
			$rID1=sql_insert(" wo_pri_other_fabric_cost",$field_array1,$data_table_3_tfoot,1); 
			
			//echo "INSERT INTO wo_pri_fabric_yarn_details(".$field_array1.") VALUES ".$data_array1."";  die;
			//mysql_query("COMMIT");  
			//echo "0"; die;
			//==================================================================
			
			$id=return_next_id( "id", " wo_pri_ebellishment_cost", 1 ) ;
			$row_table_4_tbody=explode("__",$table_4_tbody);
			for($i=0;$i<count($row_table_4_tbody); $i++)
			{
				$col_table_4_tbody=explode("_",$row_table_4_tbody[$i]);
				$data_table_4_tbody .="(".$id.",".$mst_id.",'tbody','".$col_table_4_tbody[0]."','".$col_table_4_tbody[1]."','".$col_table_4_tbody[2]."','".$col_table_4_tbody[3]."','".$col_table_4_tbody[4]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'),";
				$id++;
			}
			
			
			
			$row_table_4_tfoot=explode("__",$table_4_tfoot);
			for($i=0;$i<count($row_table_4_tfoot); $i++)
			{
				$col_table_4_tfoot=explode("_",$row_table_4_tfoot[$i]);
				$data_table_4_tfoot .="(".$id.",".$mst_id.",'tfoot','".$col_table_4_tfoot[0]."','".$col_table_4_tfoot[1]."','".$col_table_4_tfoot[2]."','".$col_table_4_tfoot[3]."','".$col_table_4_tfoot[4]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'),";
				$id++;
			}
			
			$data_table_4_tbody=rtrim($data_table_4_tbody,",");
			$data_table_4_tfoot=rtrim($data_table_4_tfoot,",");
			
			$rID=execute_query( "delete from  wo_pri_ebellishment_cost where  mst_id =".$mst_id."",1);
			$rID1=sql_insert("wo_pri_ebellishment_cost",$field_array1,$data_table_4_tbody,1); 
			$rID1=sql_insert("wo_pri_ebellishment_cost",$field_array1,$data_table_4_tfoot,1); 
			
			//echo "INSERT INTO wo_pri_fabric_yarn_details(".$field_array1.") VALUES ".$data_table_4_tbody."";  die;
			//mysql_query("COMMIT");  
			//echo "0"; die;
			//==================================================================
			
			//==================================================================
			
			$id=return_next_id( "id", " wo_pri_trim_cost", 1 ) ;
			$row_table_5_tbody=explode("__",$table_5_tbody);
			for($i=0;$i<count($row_table_5_tbody); $i++)
			{
				$col_table_5_tbody=explode("_",$row_table_5_tbody[$i]);
				$data_table_5_tbody .="(".$id.",".$mst_id.",'tbody','".$col_table_5_tbody[0]."','".$col_table_5_tbody[1]."','".$col_table_5_tbody[2]."','".$col_table_5_tbody[3]."','".$col_table_5_tbody[4]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'),";
				$id++;
			}
			
			
			
			$row_table_5_tfoot=explode("__",$table_5_tfoot);
			for($i=0;$i<count($row_table_5_tfoot); $i++)
			{
				$col_table_5_tfoot=explode("_",$row_table_5_tfoot[$i]);
				$data_table_5_tfoot .="(".$id.",".$mst_id.",'tfoot','".$col_table_5_tfoot[0]."','".$col_table_5_tfoot[1]."','".$col_table_5_tfoot[2]."','".$col_table_5_tfoot[3]."','".$col_table_5_tfoot[4]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'),";
				$id++;
			}
			
			$data_table_5_tbody=rtrim($data_table_5_tbody,",");
			$data_table_5_tfoot=rtrim($data_table_5_tfoot,",");
			
			$rID=execute_query( "delete from  wo_pri_trim_cost where  mst_id =".$mst_id."",1);
			$rID1=sql_insert("wo_pri_trim_cost",$field_array1,$data_table_5_tbody,1); 
			$rID1=sql_insert("wo_pri_trim_cost",$field_array1,$data_table_5_tfoot,1); 
			//echo "INSERT INTO wo_pri_fabric_yarn_details(".$field_array1.") VALUES ".$data_table_5_tbody."";  die;
			//mysql_query("COMMIT");  
			//echo "0"; die;
		
			//==================================================================
			
			$id=return_next_id( "id", "wo_pri_other_cost", 1 ) ;
			$row_table_6_tbody=explode("__",$table_6_tbody);
			for($i=0;$i<count($row_table_6_tbody); $i++)
			{
				$col_table_6_tbody=explode("_",$row_table_6_tbody[$i]);
				$data_table_6_tbody .="(".$id.",".$mst_id.",'tbody','".$col_table_6_tbody[0]."','".$col_table_6_tbody[1]."','".$col_table_6_tbody[2]."','".$col_table_6_tbody[3]."','".$col_table_6_tbody[4]."'),";
				$id++;
			}
			
			
			
			$row_table_6_tfoot=explode("__",$table_6_tfoot);
			for($i=0;$i<count($row_table_6_tfoot); $i++)
			{
				$col_table_6_tfoot=explode("_",$row_table_6_tfoot[$i]);
				$data_table_6_tfoot .="(".$id.",".$mst_id.",'tfoot','".$col_table_6_tfoot[0]."','".$col_table_6_tfoot[1]."','".$col_table_6_tfoot[2]."','".$col_table_6_tfoot[3]."','".$col_table_6_tfoot[4]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'),";
				$id++;
			}
			
			$data_table_6_tbody=rtrim($data_table_6_tbody,",");
			$data_table_6_tfoot=rtrim($data_table_6_tfoot,",");
			
			$rID=execute_query( "delete from  wo_pri_other_cost where  mst_id =".$mst_id."",1);
			$rID1=sql_insert("wo_pri_other_cost",$field_array1,$data_table_6_tbody,1); 
			$rID1=sql_insert("wo_pri_other_cost",$field_array1,$data_table_6_tfoot,1); 
			//echo "INSERT INTO wo_pri_fabric_yarn_details(".$field_array1.") VALUES ".$data_table_6_tbody."";  die;
			//mysql_query("COMMIT");  
			//echo "0"; die;
			//==================================================================
			
			//==================================================================
			
			$id=return_next_id( "id", "wo_pri_price_summary", 1 ) ;
			/*$row_table_7_tbody=explode("__",$table_7_tbody);
			for($i=0;$i<count($row_table_7_tbody); $i++)
			{
				$col_table_7_tbody=explode("_",$row_table_7_tbody[$i]);
				$data_table_7_tbody .="(".$id.",".$mst_id.",'tbody','".$col_table_7_tbody[0]."','".$col_table_7_tbody[1]."','".$col_table_7_tbody[2]."','".$col_table_7_tbody[3]."','".$col_table_7_tbody[4]."'),";
				$id++;
			}*/
			
			
			
			$row_table_7_tfoot=explode("__",$table_7_tfoot);
			for($i=0;$i<count($row_table_7_tfoot); $i++)
			{
				$col_table_7_tfoot=explode("_",$row_table_7_tfoot[$i]);
				$data_table_7_tfoot .="(".$id.",".$mst_id.",'tfoot','".$col_table_7_tfoot[0]."','".$col_table_7_tfoot[1]."','".$col_table_7_tfoot[2]."','".$col_table_7_tfoot[3]."','".$col_table_7_tfoot[4]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'),";
				$id++;
			}
			
			$data_table_7_tbody=rtrim($data_table_7_tbody,",");
			$data_table_7_tfoot=rtrim($data_table_7_tfoot,",");
			
			$rID=execute_query( "delete from   wo_pri_price_summary where  mst_id =".$mst_id."",1);
			//$rID1=sql_insert(" wo_pri_price_summary",$field_array1,$data_table_7_tbody,1); 
			$rID1=sql_insert(" wo_pri_price_summary",$field_array1,$data_table_7_tfoot,1); 
			//echo "INSERT INTO wo_pri_fabric_yarn_details(".$field_array1.") VALUES ".$data_table_7_tbody."";  die;
			//mysql_query("COMMIT");  
			//echo "0"; die;
			//================================================================================================
			$tot_row_fab=explode("__",$tot_row_fab);
	       // $head=explode("_",$data[0]);
			$id=return_next_id( "id", "wo_pri_sim_fab", 1 ) ;
			//$mst_id=1;
			$field_array4="id,mst_id,fab_des,fab_val";

			for($i=0;$i<count($tot_row_fab); $i++)
			{
				$data_value=explode("_",$tot_row_fab[$i]);
				
				$data_array4 .="(".$id.",".$mst_id.",'".$data_value[0]."','".$data_value[1]."'),";
				$id++;
			}
			$data_array4=rtrim($data_array4,",");
			$rID6=execute_query( "delete from wo_pri_sim_fab where  mst_id =".$mst_id."",1);
			$rID7=sql_insert("wo_pri_sim_fab",$field_array4,$data_array4,1);
			//================================================================================================
			$tot_row_mes=explode("__",$tot_row_mes);
	       // $head=explode("_",$data[0]);
			$id=return_next_id( "id", "wo_pri_sim_mes", 1 ) ;
			//$mst_id=1;
			$field_array5="id,mst_id,mes_des,mes_val";

			for($i=0;$i<count($tot_row_mes); $i++)
			{
				$data_value=explode("_",$tot_row_mes[$i]);
				
				$data_array5 .="(".$id.",".$mst_id.",'".$data_value[0]."','".$data_value[1]."'),";
				$id++;
			}
			$data_array5=rtrim($data_array5,",");
			$rID8=execute_query( "delete from wo_pri_sim_mes where  mst_id =".$mst_id."",1);
			$rID9=sql_insert("wo_pri_sim_mes",$field_array5,$data_array5,1);
			//================================================================================================
			$tot_row_mar=explode("__",$tot_row_mar);
	       // $head=explode("_",$data[0]);
			$id=return_next_id( "id", "wo_pri_sim_mar", 1 ) ;
			//$mst_id=1;
			$field_array6="id,mst_id,mar_des,mar_val";

			for($i=0;$i<count($tot_row_mar); $i++)
			{
				$data_value=explode("_",$tot_row_mar[$i]);
				
				$data_array6 .="(".$id.",".$mst_id.",'".$data_value[0]."','".$data_value[1]."'),";
				$id++;
			}
			$data_array6=rtrim($data_array6,",");
			
			$rID10=execute_query( "delete from wo_pri_sim_mar where  mst_id =".$mst_id."",1);
			$rID11=sql_insert("wo_pri_sim_mar",$field_array6,$data_array6,1);
			//=======================
			/*$rID=execute_query( "delete from wo_pri_sim_dtls_fyd where  mst_id =".$mst_id."",1);
			$rID1=sql_insert("wo_pri_sim_dtls_fyd",$field_array1,$data_array1,1);
			$rID2=execute_query( "delete from wo_pri_sim_dtls_fcd where  mst_id =".$mst_id."",1);
			$rID3=sql_insert("wo_pri_sim_dtls_fcd",$field_array2,$data_array2,1);
			$rID4=execute_query( "delete from wo_pri_sim_dtls_tfcd where  mst_id =".$mst_id."",1);
			$rID5=sql_insert("wo_pri_sim_dtls_tfcd",$field_array3,$data_array3,1);
			$rID6=execute_query( "delete from wo_pri_sim_fab where  mst_id =".$mst_id."",1);
			$rID7=sql_insert("wo_pri_sim_fab",$field_array4,$data_array4,1);
			$rID8=execute_query( "delete from wo_pri_sim_mes where  mst_id =".$mst_id."",1);
			$rID9=sql_insert("wo_pri_sim_mes",$field_array5,$data_array5,1);
			$rID10=execute_query( "delete from wo_pri_sim_mar where  mst_id =".$mst_id."",1);
			$rID11=sql_insert("wo_pri_sim_mar",$field_array6,$data_array6,1);
			$rID12=execute_query( "delete from wo_pri_sim_cm where  mst_id =".$mst_id."",1);
			$rID13=sql_insert("wo_pri_sim_cm",$field_array7,$data_array7,1);
			$rID14=execute_query( "delete from wo_pri_sim_tp where  mst_id =".$mst_id."",1);
			$rID15=sql_insert("wo_pri_sim_tp",$field_array8,$data_array8,1);
			$percent_data_string=explode("_",$percent_data_string);
			$rID16=execute_query( "update wo_pri_sim_mst set fper='$percent_data_string[0]', yper='$percent_data_string[1]', cper='$percent_data_string[2]' where id =".$mst_id."",1);*/
			//================================================================================================
			//check_table_status( $_SESSION['menu_id'],0);
		//=======================sum End =================
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10";
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0";
			}
			else{
				oci_rollback($con); 
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
}


if($action=="show_table_data")
{
		$data=explode("_",$data);
		$mst_id=$data[0];
		$extra_col=$data[1];
		?>
        <form>
         <div class="left" style="float: left;width:433px;background: white;">
                      <div style="">
                      Add <input class="text_boxes_numeric"  style="width:50px;" name="coll_add" id="coll_add"/>  after last Column  
                    <input type="button" id="coll_addb" style="width:50px" class="formbutton" value="ADD" onClick="add_column()"/>
                    <input type="hidden" class="text_boxes"  style="width:70px;" name="num_cel" id="num_cel"/> 
                    <table width="433" cellspacing="0" cellpadding=""  border="0" class="rpt_table" rules="all" id="fabrication_table">
                    <thead>
                    <tr>
                    <th width="170" style="text-align:left">Fabrication</th>
                    <th width="170">
                    </th>
                    <th width="80">
                    </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?
                    $i=1;
                    $sql_fabrication=sql_select("select fab_des,fab_val from wo_pri_sim_fab where mst_id=$mst_id");
                    if(count($sql_fabrication)>0)
                    {
                    foreach($sql_fabrication as $sql_fabrication_row)
                    {
                    ?>
                    <tr>
                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="txtfabricationA_<? echo $i;?>" id="txtfabricationA_<? echo $i;?>" maxlength="100" title="Maximum 100 Character" value="<? echo $sql_fabrication_row[csf('fab_des')]; ?>"/></td>
                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="txtfabricationB_<? echo $i;?>" id="txtfabricationB_<? echo $i;?>" maxlength="100" title="Maximum 100 Character" value="<? echo $sql_fabrication_row[csf('fab_val')]; ?>"/></td>
                    <td width="80">
                    <input type="button" id="increasefabrication_<? echo $i;?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'fabrication_table')" />
                    <input type="button" id="decreasefabrication_<? echo $i;?>" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'fabrication_table')"/>
                    </td>
                    </tr>
                   <?
                   $i++;
                    }
                    }
                    else
                    {
                        ?>
                        <tr>
                                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="txtfabricationA_1" id="txtfabricationA_1" maxlength="100" title="Maximum 100 Character" value="Color"/></td>
                                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="txtfabricationB_1" id="txtfabricationB_1" maxlength="100" title="Maximum 100 Character"/></td>
                                    <td width="80">
                                    <input type="button" id="increasefabrication_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'fabrication_table')" />
                                    <input type="button" id="decreasefabrication_1" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'fabrication_table')"/>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="txtfabricationA_2" id="txtfabricationA_2" maxlength="100" title="Maximum 100 Character" value="Fabrication"/></td>
                                    
                                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="txtfabricationB_2" id="txtfabricationB_2" maxlength="100" title="Maximum 100 Character"/></td>
                                    <td width="80">
                                    <input type="button" id="increasefabrication_2" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'fabrication_table')" />
                                    <input type="button" id="decreasefabrication_2" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'fabrication_table')"/>
                                    </td>
                                    </tr>
                                     <tr>
                                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="txtfabricationA_3" id="txtfabricationA_3" maxlength="100" title="Maximum 100 Character" value="Composition"/></td>
                                    
                                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="txtfabricationB_3" id="txtfabricationB_3" maxlength="100" title="Maximum 100 Character"/></td>
                                    <td width="80">
                                    <input type="button" id="increasefabrication_3" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'fabrication_table')" />
                                    <input type="button" id="decreasefabrication_3" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'fabrication_table')"/>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="txtfabricationA_4" id="txtfabricationA_4" maxlength="100" title="Maximum 100 Character" value="Fabric Weight"/></td>
                                    
                                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="txtfabricationB_4" id="txtfabricationB_4" maxlength="100" title="Maximum 100 Character"/></td>
                                    <td width="80">
                                    <input type="button" id="increasefabrication_4" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'fabrication_table')" />
                                    <input type="button" id="decreasefabrication_4" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'fabrication_table')"/>
                                    </td>
                                    </tr>
                        <?
                    }
                   ?>
                    </tbody>
                    </table>
                    <br/>
                    <table width="433" cellspacing="0" cellpadding=""  border="0" class="rpt_table" rules="all" id="measurment_table">
                    <thead>
                    <tr>
                    <th width="170" style="text-align:left">Measurment</th>
                    <th width="170">
                    </th>
                    <th width="80">
                    </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?
                    $i=1;
                    $sql_measurment=Sql_select("select mes_des,mes_val from wo_pri_sim_mes where mst_id=$mst_id");
                    if(count($sql_measurment)>0)
                    {
                    foreach($sql_measurment as $sql_measurment_row)
                    {
                    ?>
                    <tr>
                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="measurmentA_<? echo $i; ?>" id="measurmentA_<? echo $i; ?>" maxlength="100" title="Maximum 100 Character" value="<? echo $sql_measurment_row[csf('mes_des')];  ?>"/></td>
                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="measurmentB_<? echo $i; ?>" id="measurmentB_<? echo $i; ?>" maxlength="100" title="Maximum 100 Character" value="<? echo $sql_measurment_row[csf('mes_val')];  ?>"/></td>
                    <td width="80">
                    <input type="button" id="increasemeasurment_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'measurment_table')" />
                    <input type="button" id="decreasemeasurment_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'measurment_table')"/>
                    </td>
                    </tr>
                    <?
                    $i++;
                    }
                    }
                    else
                    {
                        
                        ?>
                        <tr>
                                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="measurmentA_1" id="measurmentA_1" maxlength="100" title="Maximum 100 Character" value="HSP LENGHT"/></td>
                                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="measurmentB_1" id="measurmentB_1" maxlength="100" title="Maximum 100 Character"/></td>
                                    <td width="80">
                                    <input type="button" id="increasemeasurment_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'measurment_table')" />
                                    <input type="button" id="decreasemeasurment_1" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'measurment_table')"/>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="measurmentA_2" id="measurmentA_2" maxlength="100" title="Maximum 100 Character" value="1/2 CHEST"/></td>
                                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="measurmentB_2" id="measurmentB_2" maxlength="100" title="Maximum 100 Character"/></td>
                                    <td width="80">
                                    <input type="button" id="increasemeasurment_2" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'measurment_table')" />
                                    <input type="button" id="decreasemeasurment_2" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'measurment_table')"/>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="measurmentA_3" id="measurmentA_3" maxlength="100" title="Maximum 100 Character" value="1/2 Bottom"/></td>
                                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="measurmentB_3" id="measurmentB_3" maxlength="100" title="Maximum 100 Character"/></td>
                                    <td width="80">
                                    <input type="button" id="increasemeasurment_3" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'measurment_table')" />
                                    <input type="button" id="decreasemeasurment_3" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'measurment_table')"/>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="measurmentA_4" id="measurmentA_4" maxlength="100" title="Maximum 100 Character" value="1/2 HIP"/></td>
                                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="measurmentB_4" id="measurmentB_4" maxlength="100" title="Maximum 100 Character"/></td>
                                    <td width="80">
                                    <input type="button" id="increasemeasurment_4" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'measurment_table')" />
                                    <input type="button" id="decreasemeasurment_4" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'measurment_table')"/>
                                    </td>
                                    </tr>
                        <?
                    }
                    ?>
                    </tbody>
                    </table>
                    <br/>
                    <table width="433" cellspacing="0" cellpadding=""  border="0" class="rpt_table" rules="all" id="marker_table">
                   <thead>
                    <tr>
                    <th width="170" style="text-align:left">Marker</th>
                    <th width="170">
                    </th>
                    <th width="80">
                    </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?
                    $i=1;
                    $sql_marker=Sql_select("select mar_des,mar_val from wo_pri_sim_mar where mst_id=$mst_id");
                    if(count($sql_marker)>0)
                    {
                    foreach($sql_marker as $sql_marker_row)
                    {
                    ?>
                    <tr>
                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="markerA_<? echo $i; ?>" id="markerA_<? echo $i; ?>" maxlength="100" title="Maximum 500 Character" value="<? echo $sql_marker_row[csf('mar_des')] ?>"/></td>
                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="markerB_<? echo $i; ?>" id="markerB_<? echo $i; ?>" maxlength="100" title="Maximum 500 Character" value="<? echo $sql_marker_row[csf('mar_val')] ?>"//></td>
                    <td width="80">
                    <input type="button" id="increasemarker_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'marker_table')" />
                    <input type="button" id="decreasemarker_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'marker_table')"/>
                    </td>
                    </tr>
                   <?
                   $i++;
                    }
                    }
                    else
                    {
                        ?>
                        <tr>
                                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="markerA_1" id="markerA_1" maxlength="100" title="Maximum 500 Character" value="Width"/></td>
                                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="markerB_1" id="markerB_1" maxlength="100" title="Maximum 500 Character"/></td>
                                    <td width="80">
                                    <input type="button" id="increasemarker_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'marker_table')" />
                                    <input type="button" id="decreasemarker_1" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'marker_table')"/>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="markerA_2" id="markerA_2" maxlength="100" title="Maximum 500 Character" value="Length"/></td>
                                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="markerB_2" id="markerB_2" maxlength="100" title="Maximum 500 Character"/></td>
                                    <td width="80">
                                    <input type="button" id="increasemarker_2" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'marker_table')" />
                                    <input type="button" id="decreasemarker_2" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'marker_table')"/>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="markerA_3" id="markerA_3" maxlength="100" title="Maximum 500 Character" value="Pcs"/></td>
                                    <td width="170"><input class="text_boxes" type="text" style="width:170px;" name="markerB_3" id="markerB_3" maxlength="100" title="Maximum 500 Character"/></td>
                                    <td width="80">
                                    <input type="button" id="increasemarker_3" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr1(this.id,'marker_table')" />
                                    <input type="button" id="decreasemarker_3" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr1(this.id,'marker_table')"/>
                                    </td>
                                    </tr>
                        <?
                        
                    }
                   ?>
                    </tbody>
                    </table>
                    </div>
                    </div>
        
        <?
		
		$header_array=array(1=>"B",2=>"C",3=>"D",4=>"E",5=>"F",6=>"G",7=>"H",8=>"I",9=>"J",10=>"K",11=>"L",12=>"M",13=>"N",14=>"O",15=>"P",16=>"Q",17=>"R",18=>"S",19=>"T",20=>"U",21=>"V",22=>"W",23=>"X",24=>"Y",25=>"Z");
		$data=explode("_",$data);
		$num_row=3;
		$num_col=4;
		//$td_with=floor((1100-($num_col*15))/$num_col);
		$td_with=100;
		$table_width=($num_col*$td_with)+200;
		?>
        <div class="right" style="float: left;width:882px;background: white;" align="left">
        <div style="margin-left:10px" id="data_container">
		<strong>
		FABRIC & YARN DETAILS
		</strong>
		<input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr('table_1')" />
		<input type="button" id="increaseconversion_2" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr('table_1')"/>
		Click Last Column Header To Remove Last Column
		<table class="rpt_table" border="0" cellpadding="0" cellspacing="0" style="width:<? echo $table_width; ?>px;text-align:center" rules="all" id="table_1">
		<thead>	
		<tr>
		<th style="width:200px;">
		 A
		</th>
		 <?
		for($col=1;$col <= $num_col; $col++)
		{
		?>
		<th style="width:<? echo $td_with; ?>px;" onClick="delete_column(this)">
		<? echo $header_array[$col];?>
		</th>
		<?
		}
		?>
		</tr>
		</thead>
		<tbody>
		<?
		$wo_pri_fabric_yarn_details=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from wo_pri_fabric_yarn_details where mst_id=$mst_id and type='tbody'");
		$num_row=count($wo_pri_fabric_yarn_details);
		if($num_row<=0)
		{
			$num_row=3;
		}
		$datarow=0;
		for($row=1;$row <= $num_row; $row++)
		{
			if($row==1)
			{
				$value="Particulars";
				$class="text_boxes";
			}
			else
			{
				$value="";
				$class="text_boxes_numeric";
			}
			$wo_pri_fabric_yarn_details_data=$wo_pri_fabric_yarn_details[$datarow];
			//print_r( $wo_pri_fabric_yarn_details_data);
		?>
		<tr class="mythingy">
		<td style="width:200px;">
		<input class="text_boxes"  style="width:200px;"  id="A_<? echo $row?>" onClick="id_detection(this.id)" value="<? echo $wo_pri_fabric_yarn_details_data[csf('col_a')]  ?>"/> 
		</td>
		 <?
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			
			$index="col_".lcfirst($header_array[$col]);
		?>
		<td style="width:<? echo $td_with; ?>px;">
		<input class="<? echo $class; ?>"  style="width:<? echo $td_with; ?>px;"  id="<? echo $header_array[$col]."_".$row; ?>" onChange="sum_value(this.id,'table_1')" onClick="id_detection(this.id)" value="<? echo $wo_pri_fabric_yarn_details_data[csf($index)]; ?>" <? echo $readonly; ?>/> 
		</td>
		<?
		}
		?>
		</tr>
		<?
		$datarow++;
		}
		?>
		</tbody>
		<tfoot>	
		<tr>
		<th style="width:200px;">
		Total Finish Fabric Cons
	   <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "sum1_A"?>" value="Total Finish Fabric Cons" /> 
		 
		</th>
		 <?
		 $wo_pri_fabric_yarn_details_tfoot1=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from wo_pri_fabric_yarn_details where mst_id=$mst_id and type='tfoot' and col_a='Total Finish Fabric Cons'");
		 list($wo_pri_fabric_yarn_details_tfoot1_data)=$wo_pri_fabric_yarn_details_tfoot1;
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;">
		 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "sum1_".$header_array[$col];?>" value="<? echo $wo_pri_fabric_yarn_details_tfoot1_data[csf($index)]; ?>" <? echo $readonly; ?> /> 
		</th>
		<?
		}
		?>
		</tr>
		<tr>
		<th style="width:200px;">
		 Garments Process Loss%
         
		 <input type="hidden" class="text_boxes_numeric"  style="width:200px;"  id="<? echo "Fper_A"?>" value="Garments Process Loss"/> 
		</th>
		 <?
		 $wo_pri_fabric_yarn_details_tfoot2=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from wo_pri_fabric_yarn_details where mst_id=$mst_id and type='tfoot' and col_a='Garments Process Loss'");
		 list($wo_pri_fabric_yarn_details_tfoot2_data)=$wo_pri_fabric_yarn_details_tfoot2;
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
		$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;">
		 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "Fper_".$header_array[$col];?>" onChange="claculate_percent(1)" value="<? echo $wo_pri_fabric_yarn_details_tfoot2_data[csf($index)]; ?>"  <? echo $readonly; ?>  /> 
		</th>
		<?
		}
		?>
		</tr>
		<tr>
		<th style="width:200px;">
		 Finish Fabric Cons
		 <input type="hidden" class="text_boxes"  style="width:200px;" id="<? echo "sum2_A"?>" value="Finish Fabric Cons" />  
		</th>
		 <?
		  $wo_pri_fabric_yarn_details_tfoot3=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from wo_pri_fabric_yarn_details where mst_id=$mst_id and type='tfoot' and col_a='Finish Fabric Cons'");
		 list($wo_pri_fabric_yarn_details_tfoot3_data)=$wo_pri_fabric_yarn_details_tfoot3;
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;">
		 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;" id="<? echo "sum2_".$header_array[$col];?>" value="<? echo $wo_pri_fabric_yarn_details_tfoot3_data[csf($index)]; ?>"  <? echo $readonly; ?>/> 
		</th>
		<?
		}
		?>
		</tr>
		<tr>
		<th style="width:200px;">
		 Dyeing and Knitting ProcessLoss %
		 <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "Yper_A";?>" value="Dyeing and Knitting ProcessLoss" />
		</th>
		 <?
		 $wo_pri_fabric_yarn_details_tfoot4=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from wo_pri_fabric_yarn_details where mst_id=$mst_id and type='tfoot' and col_a='Dyeing and Knitting ProcessLoss'");
		 list($wo_pri_fabric_yarn_details_tfoot4_data)=$wo_pri_fabric_yarn_details_tfoot4;
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;">
		 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "Yper_".$header_array[$col];?>" onChange="claculate_percent(2)" value="<? echo $wo_pri_fabric_yarn_details_tfoot4_data[csf($index)]; ?>" <? echo $readonly; ?> /> 
		</th>
		<?
		}
		?>
		</tr>
		<tr>
		<th style="width:200px;">
		Grey Fabric and Yarn Cons 
		<input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "sum3_A";?>" value="Grey Fabric and Yarn Cons"/> 
		</th>
		 <?
		  $wo_pri_fabric_yarn_details_tfoot5=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from wo_pri_fabric_yarn_details where mst_id=$mst_id and type='tfoot' and col_a='Grey Fabric and Yarn Cons'");
		 list($wo_pri_fabric_yarn_details_tfoot5_data)=$wo_pri_fabric_yarn_details_tfoot5;
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
			//echo $index; 
		?>
		<th style="width:<? echo $td_with; ?>px;">
		 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "sum3_".$header_array[$col];?>" value="<? echo $wo_pri_fabric_yarn_details_tfoot5_data[csf($index)]; ?>" <? echo $readonly; ?>/> 
		</th>
		<?
		}
		?>
		</tr>
		<tr>
		<th style="width:200px;">
		 Yarn Count 
		 <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "Ycount_A";?>" value="Yarn Count"/> 
		</th>
		 <?
		 $wo_pri_fabric_yarn_details_tfoot6=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from wo_pri_fabric_yarn_details where mst_id=$mst_id and type='tfoot' and col_a='Yarn Count'");
		 list($wo_pri_fabric_yarn_details_tfoot6_data)=$wo_pri_fabric_yarn_details_tfoot6;
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;">
		 <input class="text_boxes"  style="width:<? echo $td_with; ?>px;"  id="<? echo "Ycount_".$header_array[$col];?>" value="<? echo $wo_pri_fabric_yarn_details_tfoot6_data[csf($index)]; ?>" <? echo $readonly; ?>/> 
		</th>
		<?
		}
		?>
		</tr>
		</tfoot>
		</table>
		
		
		<strong>Body Fabric Cost </strong>
		<input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr('table_2')" />
		<input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr('table_2')"/>
		<table class="rpt_table" border="0" cellpadding="0" cellspacing="0" style="width:<? echo $table_width; ?>px;text-align:center;" rules="all" id="table_2">
		<tbody>
		<?
		$wo_pri_body_fabric_cost=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from wo_pri_body_fabric_cost where mst_id=$mst_id and type='tbody'");
		$num_row=count($wo_pri_body_fabric_cost);
		if($num_row<=0)
		{
			$num_row=3;
		}
		$datarow=0;
		for($row=1;$row <= $num_row; $row++)
		{
			if($row==1)
			{
				$class="text_boxes_numeric";
			}
			else
			{
				$class="text_boxes_numeric";
			}
			$wo_pri_body_fabric_cost_data=$wo_pri_body_fabric_cost[$datarow];
		?>
		<tr class="mythingy">
		<td style="width:200px;">
		<input class="text_boxes"  style="width:200px;"  id="AY_<? echo $row?>" onClick="id_detection(this.id)" value="<? echo $wo_pri_body_fabric_cost_data[csf('col_a')]; ?>"/> 
		</td>
		 <?
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<td style="width:<? echo $td_with; ?>px;">
		<input class="<? echo $class; ?>"  style="width:<? echo $td_with; ?>px;"  id="<? echo $header_array[$col]."Y_".$row; ?>" onChange="sum_value(this.id,'table_2')" onClick="id_detection(this.id)" value="<? echo $wo_pri_body_fabric_cost_data[csf($index)] ;?>" <? echo $readonly; ?>/> 
		</td>
		<?
		}
		?>
		</tr>
		<?
		$datarow++;
		}
		?>
		</tbody>
		<tfoot>	
		<tr>
		<th style="width:200px;">
		 Fabric Price/KG
		 <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "sum4_AY";?>" value="Fabric Price/KG" />
		</th>
        
		 <?
		 
		 $wo_pri_body_fabric_cost_tfoot1=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from wo_pri_body_fabric_cost where mst_id=$mst_id and type='tfoot' and col_a='Fabric Price/KG'");
		list($wo_pri_body_fabric_cost_tfoot1_data)=$wo_pri_body_fabric_cost_tfoot1;
		
		
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
		$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;">
		 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "sum4_".$header_array[$col]."Y";?>" value="<? echo $wo_pri_body_fabric_cost_tfoot1_data[csf($index)] ;?>" <? echo $readonly; ?>/> 
		</th>
		<?
		}
		?>
		</tr>
		<tr>
		<th style="width:200px;">
		 Total Fabric Cost/Dzn
		  <input type="hidden" class="text_boxes"  style="width:200px;" id="<? echo "sum5_AY";?>" value="Total Fabric Cost/Dzn" /> 
		</th>
		 <?
		 $wo_pri_body_fabric_cost_tfoot2=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from wo_pri_body_fabric_cost where mst_id=$mst_id and type='tfoot' and col_a='Total Fabric Cost/Dzn'");
		list($wo_pri_body_fabric_cost_tfoot2_data)=$wo_pri_body_fabric_cost_tfoot2;
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;">
		 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;" id="<? echo "sum5_".$header_array[$col]."Y";?>" value="<? echo $wo_pri_body_fabric_cost_tfoot2_data[csf($index)] ;?>" <? echo $readonly; ?> /> 
		</th>
		<?
		}
		?>
		</tr>
		<tr>
		<th style="width:200px;">
		 CM Cost/Dzn
		 <input type="hidden" class="text_boxes"  style="width:200px;" id="<? echo "CM_AY";?>"  value="CM Cost/Dzn"/>
		</th>
		 <?
		 $wo_pri_body_fabric_cost_tfoot3=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from wo_pri_body_fabric_cost where mst_id=$mst_id and type='tfoot' and col_a='CM Cost/Dzn'");
		list($wo_pri_body_fabric_cost_tfoot3_data)=$wo_pri_body_fabric_cost_tfoot3;
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
		$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;">
		 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;" id="<? echo "CM_".$header_array[$col]."Y";?>" onChange="total_garments_cost_dzn()" value="<? echo $wo_pri_body_fabric_cost_tfoot3_data[csf($index)] ;?>" <? echo $readonly; ?> /> 
		</th>
		<?
		}
		?>
		</tr>
		</tfoot>
		</table>
		<strong>Other Fabric Cost</strong>
		<input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr('table_3')" />
		<input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr('table_3')"/>
		<table class="rpt_table" border="0" cellpadding="0" cellspacing="0" style="width:<? echo $table_width; ?>px;text-align:center;" rules="all" id="table_3">
		<tbody>
		<?
		$wo_pri_other_fabric_cost=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from wo_pri_other_fabric_cost where mst_id=$mst_id and type='tbody'");
		$num_row=count($wo_pri_other_fabric_cost);
		if($num_row<=0)
		{
			$num_row=3;
		}
		$datarow=0;
		for($row=1;$row <= $num_row; $row++)
		{
		$wo_pri_other_fabric_cost_data=$wo_pri_other_fabric_cost[$datarow];	
		?>
		<tr class="mythingy">
		<td style="width:200px;">
		<input class="text_boxes"  style="width:200px;"  id="AT_<? echo $row?>" onClick="id_detection(this.id)" value="<? echo $wo_pri_other_fabric_cost_data[csf('col_a')]; ?>"/> 
		</td>
		 <?
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<td style="width:<? echo $td_with; ?>px;">
		<input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo $header_array[$col]."T_".$row; ?>" onChange="sum_value(this.id,'table_3')" onClick="id_detection(this.id)"  value="<? echo $wo_pri_other_fabric_cost_data[csf($index)] ?>" <? echo $readonly; ?>/> 
		</td>
		<?
		}
		?>
		</tr>
		<?
		$datarow++;
		}
		?>
		</tbody>
		<tfoot>	
		<tr>
		<th style="width:200px;">
		 Total 
		 <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "sum6_AT";?>" value="Total" /> 
		</th>
		 <?
		$wo_pri_other_fabric_cost_tfoot=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from  wo_pri_other_fabric_cost where mst_id=$mst_id and type='tfoot'");
		list($wo_pri_other_fabric_cost_tfoot_data)=$wo_pri_other_fabric_cost_tfoot;
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
		$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;">
		 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "sum6_".$header_array[$col]."T";?>" value="<? echo $wo_pri_other_fabric_cost_tfoot_data[csf($index)] ?>" <? echo $readonly; ?> /> 
		</th>
		<?
		}
		?>
		</tr>
		
		
		</tfoot>
		</table>
		
		<strong>Ebellishment Cost</strong>
		<input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr('table_4')" />
		<input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr('table_4')"/>
		<table class="rpt_table" border="0" cellpadding="0" cellspacing="0" style="width:<? echo $table_width; ?>px;text-align:center;" rules="all" id="table_4">
		<tbody>
		<?
		$wo_pri_ebellishment_cost=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from  wo_pri_ebellishment_cost where mst_id=$mst_id and type='tbody'");
		$num_row=count($wo_pri_ebellishment_cost);
		if($num_row<=0)
		{
			$num_row=3;
		}
		$datarow=0;
		for($row=1;$row <= $num_row; $row++)
		{
			$wo_pri_ebellishment_cost_data=$wo_pri_ebellishment_cost[$datarow];
		?>
		<tr class="mythingy">
		<td style="width:200px;">
		<input class="text_boxes"  style="width:200px;"  id="AE_<? echo $row?>" onClick="id_detection(this.id)" value="<? echo $wo_pri_ebellishment_cost_data[csf('col_a')]; ?>"/> 
		</td>
		 <?
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<td style="width:<? echo $td_with; ?>px;">
		<input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo $header_array[$col]."E_".$row; ?>" onChange="sum_value(this.id,'table_4')" onClick="id_detection(this.id)" value="<? echo $wo_pri_ebellishment_cost_data[csf($index)]; ?>" <? echo $readonly; ?>/> 
		</td>
		<?
		}
		?>
		</tr>
		<?
		$datarow++;
		}
		?>
		</tbody>
		<tfoot>	
		<tr>
		<th style="width:200px;">
		 Total 
		 <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "Esum1_AE";?>"  value="Total"/> 
		</th>
		 <?
		$wo_pri_ebellishment_cost_tfoot=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from  wo_pri_ebellishment_cost where mst_id=$mst_id and type='tfoot'");
		list($wo_pri_ebellishment_cost_tfoot_data)=$wo_pri_ebellishment_cost_tfoot;
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
		$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;">
		 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "Esum1_".$header_array[$col]."E";?>" value="<? echo $wo_pri_ebellishment_cost_tfoot_data[csf($index)]; ?>" <? echo $readonly; ?> /> 
		</th>
		<?
		}
		?>
		</tr>
		
		
		</tfoot>
		</table>
		
		
		<strong>Trim Cost</strong>
		<input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr('table_5')" />
		<input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr('table_5')"/>
		<table class="rpt_table" border="0" cellpadding="0" cellspacing="0" style="width:<? echo $table_width; ?>px;text-align:center;" rules="all" id="table_5">
        <thead>	
					<tr>
					<th style="width:200px;">
					 Trim Group
					</th>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
					?>
					<th style="width:<? echo $td_with; ?>px;" onClick="delete_column(this)">
					<? 
					if($col==1)
					{
						echo "Rate/Unit";
					}
					else if($col==2)
					{
						echo "Cons";
					}
					else
					{
					echo "";
					}
					?>
					</th>
					<?
					}
					?>
					</tr>
					</thead>
		<tbody>
		<?
		$wo_pri_trim_cost=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from  wo_pri_trim_cost where mst_id=$mst_id and type='tbody'");
		$num_row=count($wo_pri_trim_cost);
		if($num_row<=0)
		{
			$num_row=3;
		}
		$datarow=0;
		for($row=1;$row <= $num_row; $row++)
		{
			$wo_pri_trim_cost_data=$wo_pri_trim_cost[$datarow];
		?>
		<tr class="mythingy">
		<td style="width:200px;">
		<input class="text_boxes"  style="width:200px;"  id="ATR_<? echo $row?>" onClick="id_detection(this.id)" value="<? echo $wo_pri_trim_cost_data[csf('col_a')]; ?>"/> 
		</td>
		 <?
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$function="calculate_trim_cost(this.id,'table_5')";
			}
			else
			{
			  $function="sum_value(this.id,'table_5')";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<td style="width:<? echo $td_with; ?>px;">
		<input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo $header_array[$col]."TR_".$row; ?>" onChange="<? echo $function; ?>" onClick="id_detection(this.id)" value="<? echo $wo_pri_trim_cost_data[csf($index)]; ?>"/> 
		</td>
		<?
		}
		?>
		</tr>
		<?
		$datarow++;
		}
		?>
		</tbody>
		<tfoot>	
		<tr>
		<th style="width:200px;">
		 Total 
		  <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "TRsum1_ATR";?>" value="Total" /> 
		</th>
		 <?
		$wo_pri_trim_cost_tfoot=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from  wo_pri_trim_cost where mst_id=$mst_id and type='tfoot'");
		list($wo_pri_trim_cost_tfoot_data)=$wo_pri_trim_cost_tfoot;
		for($col=1;$col <= $num_col; $col++)
		{
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;">
		 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "TRsum1_".$header_array[$col]."TR";?>" value="<? echo $wo_pri_trim_cost_tfoot_data[csf($index)]; ?>" /> 
		</th>
		<?
		}
		?>
		</tr>
		</tfoot>
		</table>
		
		<strong>Other Cost</strong>
		<input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr('table_6')" />
		<input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr('table_6')"/>
		<table class="rpt_table" border="0" cellpadding="0" cellspacing="0" style="width:<? echo $table_width; ?>px;text-align:center;" rules="all" id="table_6">
		<tbody>
		<?
		$wo_pri_other_cost=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from  wo_pri_other_cost where mst_id=$mst_id and type='tbody'");
		//print_r($wo_pri_other_cost);
		$num_row=count($wo_pri_other_cost);
		if($num_row<=0)
		{
			$num_row=3;
		}
		$datarow=0;
		for($row=1;$row <= $num_row; $row++)
		{
			$wo_pri_other_cost_data=$wo_pri_other_cost[$datarow];
		?>
		<tr class="mythingy">
		<td style="width:200px;">
		<input class="text_boxes"  style="width:200px;"  id="AOC_<? echo $row?>" onClick="id_detection(this.id)" value="<? echo $wo_pri_other_cost_data[csf('col_a')]; ?>"/> 
		</td>
		 <?
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<td style="width:<? echo $td_with; ?>px;">
		<input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo $header_array[$col]."OC_".$row; ?>" onChange="sum_value(this.id,'table_6')" onClick="id_detection(this.id)" value="<? echo $wo_pri_other_cost_data[csf($index)]; ?>" <? echo $readonly; ?>/> 
		</td>
		<?
		}
		?>
		</tr>
		<?
		$datarow++;
		}
		?>
		</tbody>
		<tfoot>	
		<tr>
		<th style="width:200px;">
		   Total 
		   <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "OCsum1_AOC";?>" value="Total" /> 
		</th>
		 <?
		$wo_pri_other_cost_tfoot=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from  wo_pri_other_cost where mst_id=$mst_id and type='tfoot'");
		list($wo_pri_other_cost_tfoot_data)=$wo_pri_other_cost_tfoot;
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;">
		 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "OCsum1_".$header_array[$col]."OC";?>" value="<? echo $wo_pri_other_cost_tfoot_data[csf($index)];?>" <? echo $readonly; ?> /> 
		</th>
		<?
		}
		?>
		</tr>
		</tfoot>
		</table>
		
		
		
		
		
		
		<strong>Price Summary</strong>
		<input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr('table_7')" />
		<input type="button" id="increaseconversion_1" style="width:30px" class="formbutton" value="-" onClick="delete_break_down_tr('table_7')"/>
		<table class="rpt_table" border="0" cellpadding="0" cellspacing="0" style="width:<? echo $table_width+65; ?>px;text-align:center;" rules="all" id="table_7">
		
		<tfoot>	
		<tr>
		<th width="300">
		 Total Garments Cost/Dzn
		 <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "sum7_AT";?>" value="Total Garments Cost Dzn" /> 
		</th>
		 <?
		 $wo_pri_price_summary_tfoot=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from  wo_pri_price_summary where mst_id=$mst_id and type='tfoot' order by id");
		 $TotalGarmentsCostDzn=$wo_pri_price_summary_tfoot[0];
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;">
		 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "sum7_".$header_array[$col]."T";?>" value="<? echo $TotalGarmentsCostDzn[csf($index)];?>" <? echo $readonly; ?> /> 
		</th>
		<?
		}
		?>
		</tr>
		
		<tr>
		<th width="300">
		 Total Garments Cost/Pcs
		 <input type="hidden" class="text_boxes"  style="width:200px;" id="<? echo "sum8_AT";?>"  value="Total Garments Cost Pcs"/> 
		</th>
		 <?
		 $TotalGarmentsCostPcs=$wo_pri_price_summary_tfoot[1];
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
				$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;">
		 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;" id="<? echo "sum8_".$header_array[$col]."T";?>" <? echo $readonly; ?> value="<? echo $TotalGarmentsCostPcs[csf($index)];?>" /> 
		</th>
		<?
		}
		?>
		</tr>
		<tr>
		<th width="300">
		 Commercial Cost/Pcs
		  <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "Commercialsum_A";?>" value="Commercial Cost Dzn" /> 
		</th>
		 <?
		 $CommercialCostDzn=$wo_pri_price_summary_tfoot[2];
		for($col=1;$col <= $num_col; $col++)
		{
			if($col==1)
			{
				$readonly="disabled";
			}
			else if($col==2)
			{
				$readonly="";
			}
			else
			{
			  $readonly="readonly";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;">
		 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "Commercialsum_".$header_array[$col];?>" onChange="claculate_percent(3)" value="<? echo $CommercialCostDzn[csf($index)];?>" <? echo $readonly; ?>/> 
		</th>
		<?
		}
		?>
		</tr>
		<tr>
		<th width="300">
		 Price After Commercial Cost/Pcs
		 <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "Commercialsum1_A";?>" value="Price After Commercial Cost Pcs" /> 
		</th>
		 <?
		 $PriceAfterCommercialCostPcs=$wo_pri_price_summary_tfoot[3];
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;">
		 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "Commercialsum1_".$header_array[$col];?>" value="<? echo $PriceAfterCommercialCostPcs[csf($index)];?>"  <? echo $readonly; ?> /> 
		</th>
		<?
		}
		?>
		</tr>
		
		<tr>
		<th width="300">
		Price After Profit 
		<input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "profit_A";?>" value="Price After Profit" /> 
		</th>
		 <?
		  $PriceAfterProfit =$wo_pri_price_summary_tfoot[4];
		for($col=1;$col <= $num_col; $col++)
		{
			if($col==1)
			{
				$readonly="disabled";
			}
			else if($col==2)
			{
				$readonly="";
			}
			else
			{
			  $readonly="readonly";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;">
		 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "profit_".$header_array[$col];?>" onChange="claculate_percent(4)" value="<? echo $PriceAfterProfit[csf($index)];?>" <? echo $readonly; ?>  /> 
		</th>
		<?
		}
		?>
		</tr>
		<tr>
		<th width="300">
		Buying Commision
		 <input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "buyingcommision_A";?>" value="Buying Commision"/> 
		 
		</th>
		 <?
		 $BuyingCommision =$wo_pri_price_summary_tfoot[5];
		for($col=1;$col <= $num_col; $col++)
		{
			if($col==1)
			{
				$readonly="disabled";
			}
			else if($col==2)
			{
				$readonly="";
			}
			else
			{
			  $readonly="readonly";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;">
		 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "buyingcommision_".$header_array[$col];?>" onChange="claculate_percent(5)" value="<? echo $BuyingCommision[csf($index)];?>" <? echo $readonly; ?>/> 
		</th>
		<?
		}
		?>
		</tr>
		<tr>
		<th width="300">
		Quoted Price 
		<input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "quotedPrice_A";?>" value="Quoted Price"/>
		</th>
		 <?
		 $QuotedPrice =$wo_pri_price_summary_tfoot[6];
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;">
		 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "quotedPrice_".$header_array[$col];?>" value="<? echo $QuotedPrice[csf($index)];?>" <? echo $readonly; ?>/> 
		</th>
		<?
		}
		?>
		</tr>
		
		<tr>
		<th width="300">
		Make Combine 
		<input  type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "makecombine_A";?>" value="Make Combine"/> 
		</th>
		 <?
		 $MakeCombine  =$wo_pri_price_summary_tfoot[7];
		for($col=1;$col <= $num_col; $col++)
		{
		$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;">
		<?
		if($col==1)
		{
			?>
			<input type="checkbox" id="combine" onClick="make_combine()"/>
			<?
		
		}
		else
		{
		?>
		 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "makecombine_".$header_array[$col];?>" value="<? echo $MakeCombine[csf($index)];?>" readonly/> 
		 <?
		}
		 ?>
		</th>
		<?
		}
		?>
		</tr>
		<tr>
		<th width="300">
		Make Average
		<input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "makeaverage_A";?>" value="Make Average"/>   
		</th>
		 <?
		  $MakeAverage  =$wo_pri_price_summary_tfoot[8];
		for($col=1;$col <= $num_col; $col++)
		{
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;">
		<?
		if($col==1)
		{
			?>
			<input type="checkbox" id="average" onChange="make_average()"/>
			<?
		
		}
		else
		{
		?>
		 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "makeaverage_".$header_array[$col];?>" value="<? echo $MakeAverage[csf($index)];?>" readonly/> 
		 <?
		}
		 ?>
		</th>
		<?
		}
		?>
		</tr>
		<tr>
		<th width="300">
		Terget Price
		<input type="hidden" class="text_boxes"  style="width:200px;"  id="<? echo "sum10_AT";?>" value="Terget Price"/> 
		</th>
		 <?
		 $TergetPrice  =$wo_pri_price_summary_tfoot[9];
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;">
		 <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "sum10_".$header_array[$col]."T";?>" value="<? echo $TergetPrice[csf($index)];?>"  <? echo $readonly; ?>/> 
		</th>
		<?
		}
		?>
		</tr>
		</tfoot>
		</table>
        <br/>
        <table class="rpt_table" border="0" cellpadding="0" cellspacing="0" style="width:<? echo $table_width+65; ?>px;text-align:center;" rules="all">
        <tr>
        <td>
        <?
        echo load_submit_buttons( $permission, "fnc_simple_pri_dtls", 0,0 ,"",1,1) ;
        ?>
        </td>
        </tr>
        </table>
        </div>
        </div>
        </form>
        
<?
}

if($action=="generate_report" && $type=="preCostRpt")
{
	extract($_REQUEST);
 	$txt_quotation_date=change_date_format(str_replace("'","",$txt_quotation_date),'yyyy-mm-dd','-');
	if($txt_quotation_id=="") $quotation_id=''; else $quotation_id=" and a.id=".$txt_quotation_id."";
	$mst_id=$txt_quotation_id;
	?>
    
    <?
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($db_type==0)
	{
	 $sql = "select a.id,a.company_id ,a.buyer_id,a.style_ref,a.style_desc,a.season_buyer_wise,a.gmts_item_id ,a.order_uom ,a.offer_qnty,a.	est_ship_date,a.op_date,DATEDIFF(est_ship_date,op_date) as date_diff,a.costing_per from wo_pri_sim_mst a where a.id=$mst_id and a.status_active=1  order by a.id";
	}
	if($db_type==2)
	{
	 $sql = "select a.id,a.company_id ,a.buyer_id,a.style_ref,a.style_desc,a.season_buyer_wise,a.gmts_item_id ,a.order_uom ,a.offer_qnty,a.	est_ship_date,a.op_date,(est_ship_date-op_date) as date_diff,a.costing_per from wo_pri_sim_mst a where a.id=$mst_id and a.status_active=1  order by a.id";
	}
	$data_array=sql_select($sql);
	
	
	?>
    <div style="width:850px; font-size:20px; font-weight:bold" align="center"><? echo $comp[str_replace("'","",$cbo_company_name)]; ?></div>
     <div style="width:850px; font-size:14px;" align="center">
	 <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
                            foreach ($nameArray as $result)
                            { 
                            ?>
                                Plot No: <? echo $result[csf('plot_no')]; ?> 
                                Level No: <? echo $result[csf('level_no')]?>
                                Road No: <? echo $result[csf('road_no')]; ?> 
                                Block No: <? echo $result[csf('block_no')];?> 
                                City No: <? echo $result[csf('city')];?> 
                                Zip Code: <? echo $result[csf('zip_code')]; ?> 
                                Province No: <?php echo $result[csf('province')];?> 
                                Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
                                Email Address: <? echo $result[csf('email')];?> 
                                Website No: <? echo $result[csf('website')];
                            }
                            ?>   
     </div>
    <div style="width:850px; font-size:14px; font-weight:bold" align="center">Price Quotation</div>
	<?
	foreach ($data_array as $row)
	{	
		

		
		
		?>
        
            	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px" rules="all">
                	<tr>
                    	<td width="80">Quotation ID</td>
                        <td width="80"><b><? echo $row[csf("id")]; ?></b></td>
                        <td width="90">Buyer</td>
                        <td width="100"><b><? echo $buyer_arr[$row[csf("buyer_id")]]; ?></b></td>
                        <td width="80">Garments Item</td>
                        <? 
							
							if($row[csf("order_uom")]==1)
							{
							  $grmnt_items=$garments_item[$row[csf("gmts_item_id")]];	
							}
							else
							{
								$gmt_item=explode(',',$row[csf("gmts_item_id")]);
								foreach($gmt_item as $key=>$val)
								{
									$grmnt_items .=$garments_item[$val].", ";
								}
								
							}
							
						?>
                        <td width="100"><b><? echo $grmnt_items; ?></b></td>
                    </tr>
                    <tr>
                    	<td>Style Ref. No </td>
                        <td><b><? echo $row[csf("style_ref")]; ?></b></td>
                        <td>Order UOM </td>
                        <td><b><? echo $unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
                        <td>Offer Qnty</td>
                        <td><b><? echo $row[csf("offer_qnty")]; ?></b></td>
                    </tr>
                   
                   
                    <tr>
                        
                        
                    </tr>
                     <tr>
                    	<td>Costing Per</td>
                        <td><b><? echo $costing_per[$row[csf("costing_per")]]; ?></b></td>
                        <td>Style Desc</td>
                        <td><b><? echo $row[csf("style_desc")]; ?></b></td>
                        <td>Season</td>
                        <td><b><? echo $row[csf("season_buyer_wise")]; ?></b></td>
                        
                    </tr>
                    <tr>
                    	<td> OP Date </td>
                        <td><b><? echo change_date_format($row[csf("op_date")]); ?></b></td>
                    	<td> Est.Ship Date </td>
                        <td><b><? echo change_date_format($row[csf("est_ship_date")]); ?></b></td>
                        <td> Lead Time </td>
                        <td>
						<? 
						
						$dayes=$row[csf("date_diff")]+1;
						if($dayes >= 7)
						{
						$day=$dayes%7;
						$week=($dayes-$day)/7;
							if($week>1)
							{
								$week_string="Weeks";
							}
							else
							{
								$week_string="Week";
							}
							if($day>1)
							{
								$day_string="Days";
							}
							else
							{
								$day_string="Day";
							}
							if($day != 0)
							{
							echo $week." ".$week_string." ".$day." ".$day_string;
							}
							else
							{
							echo $week." ".$week_string;
							}
						}
						else
						{
						if($dayes>1)
							{
								$day_string="Days";
							}
							else
							{
								$dayes="Day";
							}	
							echo $dayes." ".$day_string;
						}
						 
						?>
                        </td>
                        
                        
                    </tr>
                </table>
                <?
	}
				?>
	
	
	
	<br/>
	
	
	 <div class="container" style="width:1315px; overflow: hidden;" id="container">
	 <div class="left" style="float: left;width:433px;background: white;">
                      <div style="">
                     
                    <table width="433" cellspacing="0" cellpadding=""  border="1" class="rpt_table" rules="all" id="">
                    <thead>
                    <tr>
                    <th width="170" style="text-align:left">Fabrication</th>
                    <th width="170">
                    </th>
                    <th width="80">
                    </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?
                    $i=1;
                    $sql_fabrication=Sql_select("select fab_des,fab_val from wo_pri_sim_fab where mst_id=$mst_id");
                   
                    foreach($sql_fabrication as $sql_fabrication_row)
                    {
                    ?>
                    <tr>
                    <td width="170"><? echo $sql_fabrication_row[csf('fab_des')]; ?></td>
                    <td width="170"><? echo $sql_fabrication_row[csf('fab_val')]; ?></td>
                    <td width="80">
                    
                    </td>
                    </tr>
                   <?
                   $i++;
                    }
                    ?>
                    </tbody>
                    </table>
                    <br/>
                    <table width="433" cellspacing="0" cellpadding=""  border="1" class="rpt_table" rules="all" id="">
                    <thead>
                    <tr>
                    <th width="170" style="text-align:left">Measurment</th>
                    <th width="170">
                    </th>
                    <th width="80">
                    </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?
                    $i=1;
                    $sql_measurment=Sql_select("select mes_des,mes_val from wo_pri_sim_mes where mst_id=$mst_id");
                    
                    foreach($sql_measurment as $sql_measurment_row)
                    {
                    ?>
                    <tr>
                    <td width="170"><? echo $sql_measurment_row[csf('mes_des')];  ?></td>
                    <td width="170"><? echo $sql_measurment_row[csf('mes_val')];  ?></td>
                    <td width="80">
                  
                    </td>
                    </tr>
                    <?
                    $i++;
                    }
                    ?>
                    </tbody>
                    </table>
                    <br/>
                    <table width="433" cellspacing="0" cellpadding=""  border="1" class="rpt_table" rules="all" id="">
                   <thead>
                    <tr>
                    <th width="170" style="text-align:left">Marker</th>
                    <th width="170">
                    </th>
                    <th width="80">
                    </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?
                    $i=1;
                    $sql_marker=Sql_select("select mar_des,mar_val from wo_pri_sim_mar where mst_id=$mst_id");
                  
                    foreach($sql_marker as $sql_marker_row)
                    {
                    ?>
                    <tr>
                    <td width="170"><? echo $sql_marker_row[csf('mar_des')] ?></td>
                    <td width="170"><? echo $sql_marker_row[csf('mar_val')] ?></td>
                    <td width="80">
                   
                    </td>
                    </tr>
                   <?
                   $i++;
                    }
                   ?>
                    </tbody>
                  </table>
                  </div>
                    </div>
        
        <?
		
		$header_array=array(1=>"B",2=>"C",3=>"D",4=>"E",5=>"F",6=>"G",7=>"H",8=>"I",9=>"J",10=>"K",11=>"L",12=>"M",13=>"N",14=>"O",15=>"P",16=>"Q",17=>"R",18=>"S",19=>"T",20=>"U",21=>"V",22=>"W",23=>"X",24=>"Y",25=>"Z");
		$data=explode("_",$data);
		$num_row=3;
		$num_col=4;
		//$td_with=floor((1100-($num_col*15))/$num_col);
		$td_with=100;
		$table_width=($num_col*$td_with)+200;
		?>
        <div class="right" style="float: left;width:882px;background: white;" align="left">
        <div style="margin-left:10px" id="data_container">
		<strong>
		FABRIC & YARN DETAILS
		</strong>
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<? echo $table_width; ?>px;text-align:center" rules="all" id="">
		<thead>	
		<tr>
		<th style="width:200px;">
		 A
		</th>
		 <?
		for($col=1;$col <= $num_col; $col++)
		{
		?>
		<th style="width:<? echo $td_with; ?>px;" onClick="delete_column(this)">
		<? echo $header_array[$col];?>
		</th>
		<?
		}
		?>
		</tr>
		</thead>
		<tbody>
		<?
		$wo_pri_fabric_yarn_details=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from wo_pri_fabric_yarn_details where mst_id=$mst_id and type='tbody'");
		$num_row=count($wo_pri_fabric_yarn_details);
		if($num_row<=0)
		{
			$num_row=3;
		}
		$datarow=0;
		for($row=1;$row <= $num_row; $row++)
		{
			if($row==1)
			{
				$value="Particulars";
				$class="text_boxes";
			}
			else
			{
				$value="";
				$class="text_boxes_numeric";
			}
			$wo_pri_fabric_yarn_details_data=$wo_pri_fabric_yarn_details[$datarow];
		?>
		<tr class="mythingy">
		<td style="width:200px;" align="left">
	    	<?  echo $wo_pri_fabric_yarn_details_data[col_a]  ?>
		</td>
		 <?
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			
			$index="col_".lcfirst($header_array[$col]);
		?>
		<td style="width:<? echo $td_with; ?>px;" align="right">
	    	<? if($wo_pri_fabric_yarn_details_data[$index]!=0) echo $wo_pri_fabric_yarn_details_data[$index]; ?>
		</td>
		<?
		}
		?>
		</tr>
		<?
		$datarow++;
		}
		?>
		</tbody>
		<tfoot>	
		<tr>
		<th style="width:200px;" align="left">
		Total Finish Fabric Cons
		 
		</th>
		 <?
		 $wo_pri_fabric_yarn_details_tfoot1=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from wo_pri_fabric_yarn_details where mst_id=$mst_id and type='tfoot' and col_a='Total Finish Fabric Cons'");
		 list($wo_pri_fabric_yarn_details_tfoot1_data)=$wo_pri_fabric_yarn_details_tfoot1;
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;" align="right">
	     <? if($wo_pri_fabric_yarn_details_tfoot1_data[$index]!=0) echo $wo_pri_fabric_yarn_details_tfoot1_data[$index]; ?>
		</th>
		<?
		}
		?>
		</tr>
		<tr>
		<th style="width:200px;" align="left">
		 Garments Process Loss%
         
		</th>
		 <?
		 $wo_pri_fabric_yarn_details_tfoot2=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from wo_pri_fabric_yarn_details where mst_id=$mst_id and type='tfoot' and col_a='Garments Process Loss'");
		 list($wo_pri_fabric_yarn_details_tfoot2_data)=$wo_pri_fabric_yarn_details_tfoot2;
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
		$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;" align="right">
		<?  if($wo_pri_fabric_yarn_details_tfoot2_data[$index]!=0) echo $wo_pri_fabric_yarn_details_tfoot2_data[$index]; ?>
		</th>
		<?
		}
		?>
		</tr>
		<tr>
		<th style="width:200px;" align="left">
		 Finish Fabric Cons
		</th>
		 <?
		  $wo_pri_fabric_yarn_details_tfoot3=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from wo_pri_fabric_yarn_details where mst_id=$mst_id and type='tfoot' and col_a='Finish Fabric Cons'");
		 list($wo_pri_fabric_yarn_details_tfoot3_data)=$wo_pri_fabric_yarn_details_tfoot3;
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;" align="right">
		<? if($wo_pri_fabric_yarn_details_tfoot3_data[$index]!=0) echo $wo_pri_fabric_yarn_details_tfoot3_data[$index]; ?>
		</th>
		<?
		}
		?>
		</tr>
		<tr>
		<th style="width:200px;" align="left">
		 Dyeing and Knitting ProcessLoss %
		</th>
		 <?
		 $wo_pri_fabric_yarn_details_tfoot4=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from wo_pri_fabric_yarn_details where mst_id=$mst_id and type='tfoot' and col_a='Dyeing and Knitting ProcessLoss'");
		 list($wo_pri_fabric_yarn_details_tfoot4_data)=$wo_pri_fabric_yarn_details_tfoot4;
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;" align="right">
		 <? if($wo_pri_fabric_yarn_details_tfoot4_data[$index]!=0) echo $wo_pri_fabric_yarn_details_tfoot4_data[$index]; ?> 
		</th>
		<?
		}
		?>
		</tr>
		<tr>
		<th style="width:200px;" align="left">
		Grey Fabric and Yarn Cons 
		</th>
		 <?
		  $wo_pri_fabric_yarn_details_tfoot5=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from wo_pri_fabric_yarn_details where mst_id=$mst_id and type='tfoot' and col_a='Grey Fabric and Yarn Cons'");
		 list($wo_pri_fabric_yarn_details_tfoot5_data)=$wo_pri_fabric_yarn_details_tfoot5;
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;" align="right">
		<? if($wo_pri_fabric_yarn_details_tfoot5_data[$index]!=0) echo $wo_pri_fabric_yarn_details_tfoot5_data[$index]; ?>
		</th>
		<?
		}
		?>
		</tr>
		<tr>
		<th style="width:200px;" align="left">
		 Yarn Count 
		</th>
		 <?
		 $wo_pri_fabric_yarn_details_tfoot6=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from wo_pri_fabric_yarn_details where mst_id=$mst_id and type='tfoot' and col_a='Yarn Count'");
		 list($wo_pri_fabric_yarn_details_tfoot6_data)=$wo_pri_fabric_yarn_details_tfoot6;
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;" align="right">
		 <? if($wo_pri_fabric_yarn_details_tfoot6_data[$index]!=0) echo $wo_pri_fabric_yarn_details_tfoot6_data[$index]; ?>
		</th>
		<?
		}
		?>
		</tr>
		</tfoot>
		</table>
		
		
		<strong>Body Fabric Cost </strong>
		
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<? echo $table_width; ?>px;text-align:center;" rules="all" id="">
		<tbody>
		<?
		$wo_pri_body_fabric_cost=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from wo_pri_body_fabric_cost where mst_id=$mst_id and type='tbody'");
		$num_row=count($wo_pri_body_fabric_cost);
		if($num_row<=0)
		{
			$num_row=3;
		}
		$datarow=0;
		for($row=1;$row <= $num_row; $row++)
		{
			if($row==1)
			{
				$class="text_boxes_numeric";
			}
			else
			{
				$class="text_boxes_numeric";
			}
			$wo_pri_body_fabric_cost_data=$wo_pri_body_fabric_cost[$datarow];
		?>
		<tr class="mythingy">
		<td style="width:200px;"  align="left">
		<? echo $wo_pri_body_fabric_cost_data[col_a]; ?>
		</td>
		 <?
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<td style="width:<? echo $td_with; ?>px;" align="right">
		<?  if($wo_pri_body_fabric_cost_data[$index]!=0) echo $wo_pri_body_fabric_cost_data[$index] ;?>
		</td>
		<?
		}
		?>
		</tr>
		<?
		$datarow++;
		}
		?>
		</tbody>
		<tfoot>	
		<tr>
		<th style="width:200px;"  align="left">
		 Fabric Price/KG
		</th>
        
		 <?
		 
		 $wo_pri_body_fabric_cost_tfoot1=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from wo_pri_body_fabric_cost where mst_id=$mst_id and type='tfoot' and col_a='Fabric Price/KG'");
		list($wo_pri_body_fabric_cost_tfoot1_data)=$wo_pri_body_fabric_cost_tfoot1;
		
		
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
		$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;" align="right">
		<? if($wo_pri_body_fabric_cost_tfoot1_data[$index]!=0) echo $wo_pri_body_fabric_cost_tfoot1_data[$index] ;?>
		</th>
		<?
		}
		?>
		</tr>
		<tr>
		<th style="width:200px;"  align="left">
		 Total Fabric Cost/Dzn
		</th>
		 <?
		 $wo_pri_body_fabric_cost_tfoot2=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from wo_pri_body_fabric_cost where mst_id=$mst_id and type='tfoot' and col_a='Total Fabric Cost/Dzn'");
		list($wo_pri_body_fabric_cost_tfoot2_data)=$wo_pri_body_fabric_cost_tfoot2;
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;" align="right">
		 <? if($wo_pri_body_fabric_cost_tfoot2_data[$index]!=0) echo $wo_pri_body_fabric_cost_tfoot2_data[$index] ;?>
		</th>
		<?
		}
		?>
		</tr>
		<tr>
		<th style="width:200px;"  align="left">
		 CM Cost/Dzn
		</th>
		 <?
		 $wo_pri_body_fabric_cost_tfoot3=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from wo_pri_body_fabric_cost where mst_id=$mst_id and type='tfoot' and col_a='CM Cost/Dzn'");
		list($wo_pri_body_fabric_cost_tfoot3_data)=$wo_pri_body_fabric_cost_tfoot3;
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
		$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;" align="right">
		<?  if($wo_pri_body_fabric_cost_tfoot3_data[$index]!=0) echo $wo_pri_body_fabric_cost_tfoot3_data[$index] ;?>
		</th>
		<?
		}
		?>
		</tr>
		</tfoot>
		</table>
		<strong>Other Fabric Cost</strong>
		
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<? echo $table_width; ?>px;text-align:center;" rules="all" id="">
		<tbody>
		<?
		$wo_pri_other_fabric_cost=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from wo_pri_other_fabric_cost where mst_id=$mst_id and type='tbody'");
		$num_row=count($wo_pri_other_fabric_cost);
		if($num_row<=0)
		{
			$num_row=3;
		}
		$datarow=0;
		for($row=1;$row <= $num_row; $row++)
		{
		$wo_pri_other_fabric_cost_data=$wo_pri_other_fabric_cost[$datarow];	
		?>
		<tr class="mythingy">
		<td style="width:200px;" align="left">
		<? echo $wo_pri_other_fabric_cost_data[col_a]; ?>
		</td>
		 <?
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<td style="width:<? echo $td_with; ?>px;"  align="right">
		<? if($wo_pri_other_fabric_cost_data[$index]!=0)  echo $wo_pri_other_fabric_cost_data[$index]; ?>
		</td>
		<?
		}
		?>
		</tr>
		<?
		$datarow++;
		}
		?>
		</tbody>
		<tfoot>	
		<tr>
		<th style="width:200px;" align="left" >
		 Total 
		</th>
		 <?
		$wo_pri_other_fabric_cost_tfoot=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from  wo_pri_other_fabric_cost where mst_id=$mst_id and type='tfoot'");
		list($wo_pri_other_fabric_cost_tfoot_data)=$wo_pri_other_fabric_cost_tfoot;
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
		$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;" align="right">
		 <? if($wo_pri_other_fabric_cost_tfoot_data[$index]!=0) echo $wo_pri_other_fabric_cost_tfoot_data[$index]; ?>
		</th>
		<?
		}
		?>
		</tr>
		
		
		</tfoot>
		</table>
		
		<strong>Ebellishment Cost</strong>
		
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<? echo $table_width; ?>px;text-align:center;" rules="all" id="">
		<tbody>
		<?
		$wo_pri_ebellishment_cost=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from  wo_pri_ebellishment_cost where mst_id=$mst_id and type='tbody'");
		$num_row=count($wo_pri_ebellishment_cost);
		if($num_row<=0)
		{
			$num_row=3;
		}
		$datarow=0;
		for($row=1;$row <= $num_row; $row++)
		{
			$wo_pri_ebellishment_cost_data=$wo_pri_ebellishment_cost[$datarow];
		?>
		<tr class="mythingy">
		<td style="width:200px;"  align="left">
		<? echo $wo_pri_ebellishment_cost_data[col_a]; ?>
		</td>
		 <?
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<td style="width:<? echo $td_with; ?>px;"  align="right">
		<? if($wo_pri_ebellishment_cost_data[$index]!=0) echo $wo_pri_ebellishment_cost_data[$index]; ?>
		</td>
		<?
		}
		?>
		</tr>
		<?
		$datarow++;
		}
		?>
		</tbody>
		<tfoot>	
		<tr>
		<th style="width:200px;"  align="left">
		 Total 
		</th>
		 <?
		$wo_pri_ebellishment_cost_tfoot=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from  wo_pri_ebellishment_cost where mst_id=$mst_id and type='tfoot'");
		list($wo_pri_ebellishment_cost_tfoot_data)=$wo_pri_ebellishment_cost_tfoot;
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
		$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;" align="right">
		<? if($wo_pri_ebellishment_cost_tfoot_data[$index]!=0) echo $wo_pri_ebellishment_cost_tfoot_data[$index]; ?>
		</th>
		<?
		}
		?>
		</tr>
		
		
		</tfoot>
		</table>
		
		
		<strong>Trim Cost</strong>
		
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<? echo $table_width; ?>px;text-align:center;" rules="all" id="">
        <thead>	
					<tr>
					<th style="width:200px;">
					 Trim Group
					</th>
					 <?
					for($col=1;$col <= $num_col; $col++)
					{
					?>
					<th style="width:<? echo $td_with; ?>px;" onClick="delete_column(this)">
					<? 
					if($col==1)
					{
						echo "Rate/Unit";
					}
					else if($col==2)
					{
						echo "Cons";
					}
					else
					{
					echo "";
					}
					?>
					</th>
					<?
					}
					?>
					</tr>
					</thead>
		<tbody>
		<?
		$wo_pri_trim_cost=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from  wo_pri_trim_cost where mst_id=$mst_id and type='tbody'");
		$num_row=count($wo_pri_trim_cost);
		if($num_row<=0)
		{
			$num_row=3;
		}
		$datarow=0;
		for($row=1;$row <= $num_row; $row++)
		{
			$wo_pri_trim_cost_data=$wo_pri_trim_cost[$datarow];
		?>
		<tr class="mythingy">
		<td style="width:200px;"  align="left">
		<? echo $wo_pri_trim_cost_data[col_a]; ?>
		</td>
		 <?
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$function="calculate_trim_cost(this.id,'table_5')";
			}
			else
			{
			  $function="sum_value(this.id,'table_5')";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<td style="width:<? echo $td_with; ?>px;" align="right">
		   <? if($wo_pri_trim_cost_data[$index]!=0) echo $wo_pri_trim_cost_data[$index]; ?> 
		</td>
		<?
		}
		?>
		</tr>
		<?
		$datarow++;
		}
		?>
		</tbody>
		<tfoot>	
		<tr>
		<th style="width:200px;"  align="left">
		 Total 
		</th>
		 <?
		$wo_pri_trim_cost_tfoot=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from  wo_pri_trim_cost where mst_id=$mst_id and type='tfoot'");
		list($wo_pri_trim_cost_tfoot_data)=$wo_pri_trim_cost_tfoot;
		for($col=1;$col <= $num_col; $col++)
		{
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;" align="right">
		<? if($wo_pri_trim_cost_tfoot_data[$index]!=0) echo $wo_pri_trim_cost_tfoot_data[$index]; ?>
		</th>
		<?
		}
		?>
		</tr>
		</tfoot>
		</table>
		
		<strong>Other Cost</strong>
	
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<? echo $table_width; ?>px;text-align:center;" rules="all" id="">
		<tbody>
		<?
		$wo_pri_other_cost=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from  wo_pri_other_cost where mst_id=$mst_id and type='tbody'");
		//print_r($wo_pri_other_cost);
		$num_row=count($wo_pri_other_cost);
		if($num_row<=0)
		{
			$num_row=3;
		}
		$datarow=0;
		for($row=1;$row <= $num_row; $row++)
		{
			$wo_pri_other_cost_data=$wo_pri_other_cost[$datarow];
		?>
		<tr class="mythingy">
		<td style="width:200px;"  align="left">
		<? echo $wo_pri_other_cost_data[col_a]; ?>
		</td>
		 <?
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<td style="width:<? echo $td_with; ?>px;" align="right">
		<? if($wo_pri_other_cost_data[$index]!=0) echo $wo_pri_other_cost_data[$index]; ?>
		</td>
		<?
		}
		?>
		</tr>
		<?
		$datarow++;
		}
		?>
		</tbody>
		<tfoot>	
		<tr>
		<th style="width:200px;"  align="left">
		   Total 
		</th>
		 <?
		$wo_pri_other_cost_tfoot=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from  wo_pri_other_cost where mst_id=$mst_id and type='tfoot'");
		list($wo_pri_other_cost_tfoot_data)=$wo_pri_other_cost_tfoot;
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;" align="right">
		<? if($wo_pri_other_cost_tfoot_data[$index]!=0) echo $wo_pri_other_cost_tfoot_data[$index];?>
		</th>
		<?
		}
		?>
		</tr>
		</tfoot>
		</table>
		
		
		
		
		
		
		<strong>Price Summary</strong>
		
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<? echo $table_width ?>px;text-align:center;" rules="all" id="">
		
		<tfoot>	
		<tr>
		<th style="width:200px;"  align="left">
		 Total Garments Cost/Dzn
		</th>
		 <?
		 $wo_pri_price_summary_tfoot=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e from  wo_pri_price_summary where mst_id=$mst_id and type='tfoot' order by id");
		 $TotalGarmentsCostDzn=$wo_pri_price_summary_tfoot[0];
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;"  align="right">
		<? if($TotalGarmentsCostDzn[$index]!=0) echo $TotalGarmentsCostDzn[$index];?>
		</th>
		<?
		}
		?>
		</tr>
		
		<tr>
		<th style="width:200px;" align="left">
		 Total Garments Cost/Pcs
		</th>
		 <?
		 $TotalGarmentsCostPcs=$wo_pri_price_summary_tfoot[1];
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
				$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;" align="right">
		<? if($TotalGarmentsCostPcs[$index]!=0) echo $TotalGarmentsCostPcs[$index];?> 
		</th>
		<?
		}
		?>
		</tr>
		<tr>
		<th style="width:200px;"  align="left">
		 Commercial Cost/Dzn
		</th>
		 <?
		 $CommercialCostDzn=$wo_pri_price_summary_tfoot[2];
		for($col=1;$col <= $num_col; $col++)
		{
			if($col==1)
			{
				$readonly="disabled";
			}
			else if($col==2)
			{
				$readonly="";
			}
			else
			{
			  $readonly="readonly";	

			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;" align="right">
		 <? if($CommercialCostDzn[$index]!=0) echo $CommercialCostDzn[$index];?>
		</th>
		<?
		}
		?>
		</tr>
		<tr>
		<th style="width:200px;"  align="left" style="word">
		 Price After Comm. Cost/Pcs
		</th>
		 <?
		 $PriceAfterCommercialCostPcs=$wo_pri_price_summary_tfoot[3];
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;" align="right">
		 <? if($PriceAfterCommercialCostPcs[$index]!=0) echo $PriceAfterCommercialCostPcs[$index];?> 
		</th>
		<?
		}
		?>
		</tr>
		
		<tr>
		<th style="width:200px;"  align="left">
		Price After Profit 
		</th>
		 <?
		  $PriceAfterProfit =$wo_pri_price_summary_tfoot[4];
		for($col=1;$col <= $num_col; $col++)
		{
			if($col==1)
			{
				$readonly="disabled";
			}
			else if($col==2)
			{
				$readonly="";
			}
			else
			{
			  $readonly="readonly";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;" align="right">
		 <? if($PriceAfterProfit[$index]!=0) echo $PriceAfterProfit[$index];?>
		</th>
		<?
		}
		?>
		</tr>
		<tr>
		<th style="width:200px;"  align="left">
		Buying Commision
		 
		</th>
		 <?
		 $BuyingCommision =$wo_pri_price_summary_tfoot[5];
		for($col=1;$col <= $num_col; $col++)
		{
			if($col==1)
			{
				$readonly="disabled";
			}
			else if($col==2)
			{
				$readonly="";
			}
			else
			{
			  $readonly="readonly";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;" align="right">
		<? if($BuyingCommision[$index]!=0) echo $BuyingCommision[$index];?>
		</th>
		<?
		}
		?>
		</tr>
		<tr>
		<th style="width:200px;"  align="left">
		Quoted Price 
		</th>
		 <?
		 $QuotedPrice =$wo_pri_price_summary_tfoot[6];
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;" align="right">
		 <? if($QuotedPrice[$index]!=0) echo $QuotedPrice[$index];?>
		</th>
		<?
		}
		?>
		</tr>
		
		<tr>
		<th style="width:200px;"  align="left">
		Make Combine 
		</th>
		 <?
		 $MakeCombine  =$wo_pri_price_summary_tfoot[7];
		for($col=1;$col <= $num_col; $col++)
		{
		$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;" align="right">
		  <?
		if($col==1)
		{
			?>
			<?
		
		}
		else
		{
		?>
		<? if ($MakeCombine[$index]!=0) echo $MakeCombine[$index];?>
		 <?
		}
		 ?>
		</th>
		<?
		}
		?>
		</tr>
		<tr>
		<th style="width:200px;"  align="left">
		Make Average
		</th>
		 <?
		  $MakeAverage  =$wo_pri_price_summary_tfoot[8];
		for($col=1;$col <= $num_col; $col++)
		{
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;" align="right">
		 <?
		if($col==1)
		{
			?>
			<?
		
		}
		else
		{
		?>
		 <? if($MakeAverage[$index]!=0) echo $MakeAverage[$index];?>
		 <?
		}
		 ?>
		</th>
		<?
		}
		?>
		</tr>
		<tr>
		<th style="width:200px;"  align="left">
		Terget Price
		</th>
		 <?
		 $TergetPrice  =$wo_pri_price_summary_tfoot[9];
		for($col=1;$col <= $num_col; $col++)
		{
			if($col<=2)
			{
				$readonly="disabled";
			}
			else
			{
			  $readonly="";	
			}
			$index="col_".lcfirst($header_array[$col]);
		?>
		<th style="width:<? echo $td_with; ?>px;" align="right">
		<? if($TergetPrice[$index]!=0) echo $TergetPrice[$index];?> 
		</th>
		<?
		}
		?>
		</tr>
		</tfoot>
		</table>
        </div>
        </div>
        
        <table class="rpt_table" border="0" cellpadding="1" cellspacing="1" style="width:1315px;text-align:center;" rules="all">
        <tr style="alignment-baseline:baseline;">
        	<td height="100" width="33%" style="text-decoration:overline; border:none"></td>
            <td width="33%" style="text-decoration:overline; border:none"></td>
            <td width="33%" style="text-decoration:overline; border:none"></td>
        </tr>	
		<tr style="alignment-baseline:baseline;">
        	<td height="100" width="33%" style="text-decoration:overline; border:none">Prepared By</td>
            <td width="33%" style="text-decoration:overline; border:none">Checked By</td>
            <td width="33%" style="text-decoration:overline; border:none">Approved By</td>
        </tr>
    </table>
        </div>
<?
}//end master if condition-------------------------------------------------------

if ($action=="copy_quatation")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==5)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$id=return_next_id( "id", "wo_pri_sim_mst", 1 ) ;
		$field_array="id,inquery_sys_id,inquery_id,company_id, buyer_id, style_ref, revised_no, pord_dept,product_code, style_desc, currency, agent, offer_qnty, region, color_range, incoterm, incoterm_place, machine_line, prod_line_hr,  costing_per, quot_date, est_ship_date,op_date, factory, remarks, garments_nature,order_uom,gmts_item_id,set_break_down,total_set_qnty,cm_cost_predefined_method_id,exchange_rate,sew_smv,cut_smv,sew_effi_percent,cut_effi_percent,efficiency_wastage_percent,season_buyer_wise,m_list,bh_merchant,quotation_status, inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$id.",".$txt_quotation_inquery_sys_id.",".$inquery_id.",".$cbo_company_name.",".$cbo_buyer_name.",".$txt_style_ref.",".$txt_revised_no.",".$cbo_pord_dept.",".$txt_product_code.",".$txt_style_desc.",".$cbo_currercy.",".$cbo_agent.",".$txt_offer_qnty.",".$cbo_region.",".$cbo_color_range.",".$cbo_inco_term.",".$txt_incoterm_place.",".$txt_machine_line.",".$txt_prod_line_hr.",".$cbo_costing_per.",".$txt_quotation_date.",".$txt_est_ship_date.",'".$txt_op_date."',".$txt_factory.",".$txt_remarks.",".$garments_nature.",".$cbo_order_uom.",".$item_id.",".$set_breck_down.",".$tot_set_qnty.",".$cm_cost_predefined_method_id.",".$txt_exchange_rate.",".$txt_sew_smv.",".$txt_cut_smv.",".$txt_sew_efficiency_per.",".$txt_cut_efficiency_per.",".$txt_efficiency_wastage.",".$cbo_season_name.",".$txt_m_list.",".$txt_bh_merchant.",".$cbo_quotation_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
		$field_array1="id,quotation_id, gmts_item_id,set_item_ratio";
		$add_comma=0;
		$id1=return_next_id( "id", "wo_pri_sim_mst_set_dtls", 1 ) ;
		$set_breck_down_array=explode('__',str_replace("'",'',$set_breck_down));
		for($c=0;$c < count($set_breck_down_array);$c++)
		{
			$set_breck_down_arr=explode('_',$set_breck_down_array[$c]);
			if ($add_comma!=0) $data_array1 .=",";
			$data_array1 .="(".$id1.",".$id.",'".$set_breck_down_arr[0]."','".$set_breck_down_arr[1]."')";
			$add_comma++;
			$id1=$id1+1;
		}
		$rID=sql_insert("wo_pri_sim_mst",$field_array,$data_array,0);
		//echo "0**"."INSERT INTO wo_pri_sim_mst (".$field_array.") VALUES ".$data_array;die;
		
		$rID1=sql_insert("wo_pri_sim_mst_set_dtls",$field_array1,$data_array1,1);
		//echo "0**"."INSERT INTO wo_pri_sim_mst_set_dtls (".$field_array1.") VALUES ".$data_array1;die;
		//echo $id.'M'.$txt_quotation_id;die;
		$id_costing_mst=save_fabric_cost($id,$txt_quotation_id);
		
		check_table_status( $_SESSION['menu_id'],0);
		
		if($db_type==0)
		{
			if(str_replace("'","",$cbo_order_uom)==58 || str_replace("'","",$cbo_order_uom)==57 )
			{
				if($rID==1 && $rID1==1){
					mysql_query("COMMIT");  
					echo "0**".$rID."**".$id;//$id_costing_mst;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID."**".$id;//$id_costing_mst;
				}
			}
			if(str_replace("'","",$cbo_order_uom)==1)
			{
				if($rID==1){
					mysql_query("COMMIT");  
					echo "0**".$rID."**".$id;//.$id_costing_mst;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID."**".$id;//"**".$id_costing_mst;
				}
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			echo '0**0'."**".$id;
		}
		disconnect($con);
		die;
	}
}
function save_fabric_cost($newid,$txt_quotation_id_old)
{
	    global $pc_date_time;
		//echo $newid."PPPP".$txt_quotation_id_old;die;
		$conversion_cost_headarr=array();
		$id=return_next_id( "id", "wo_pri_fabric_yarn_details", 1 ) ;
		$id2=return_next_id( "id", "wo_pri_body_fabric_cost", 1 ) ;
		$id3=return_next_id( "id", "wo_pri_other_cost", 1 ) ;
		$id4=return_next_id( "id", "wo_pri_ebellishment_cost", 1 ) ;
		$id5=return_next_id( "id", "wo_pri_trim_cost", 1 ) ;
		$id6=return_next_id( "id", "wo_pri_other_fabric_cost", 1 ) ;	
		$id7=return_next_id( "id", "wo_pri_price_summary", 1 ) ;
		$id8=return_next_id( "id", "wo_pri_sim_fab", 1 ) ;
		$id9=return_next_id( "id", "wo_pri_sim_mes", 1 ) ;
		$id10=return_next_id( "id", "wo_pri_sim_mar", 1 ) ;
		$field_array="id,mst_id,type,col_a,col_b,col_c,col_d,col_e,col_f,col_g,col_h,col_i,col_j,col_k,col_l,col_m,	col_n,col_o,col_q,col_r,col_s,col_t,col_u,col_v,col_w,col_x,col_y,col_z,inserted_by,insert_date,status_active,is_deleted";
		/*$field_array2="id,mst_id,type,col_a,col_b,col_c,col_d,col_e,col_f,col_g,col_h,col_i,col_j,col_k,col_l,col_m,	col_n,col_o,col_q,col_r,col_s,col_t,col_u,col_v,col_w,col_x,col_y,col_z,inserted_by,insert_date,status_active,is_deleted";
		$field_array3="id,mst_id,type,col_a,col_b,col_c,col_d,col_e,col_f,col_g,col_h,col_i,col_j,col_k,col_l,col_m,	col_n,col_o,col_q,col_r,col_s,col_t,col_u,col_v,col_w,col_x,col_y,col_z,inserted_by,insert_date,status_active,is_deleted";
		$field_array4="id,mst_id,type,col_a,col_b,col_c,col_d,col_e,col_f,col_g,col_h,col_i,col_j,col_k,col_l,col_m,	col_n,col_o,col_q,col_r,col_s,col_t,col_u,col_v,col_w,col_x,col_y,col_z,inserted_by,insert_date,status_active,is_deleted";
		$field_array5="id,mst_id,type,col_a,col_b,col_c,col_d,col_e,col_f,col_g,col_h,col_i,col_j,col_k,col_l,col_m,	col_n,col_o,col_q,col_r,col_s,col_t,col_u,col_v,col_w,col_x,col_y,col_z,inserted_by,insert_date,status_active,is_deleted";
		$field_array6="id,mst_id,type,col_a,col_b,col_c,col_d,col_e,col_f,col_g,col_h,col_i,col_j,col_k,col_l,col_m,	col_n,col_o,col_q,col_r,col_s,col_t,col_u,col_v,col_w,col_x,col_y,col_z,inserted_by,insert_date,status_active,is_deleted";
		$field_array7="id,mst_id,type,col_a,col_b,col_c,col_d,col_e,col_f,col_g,col_h,col_i,col_j,col_k,col_l,col_m,	col_n,col_o,col_q,col_r,col_s,col_t,col_u,col_v,col_w,col_x,col_y,col_z,inserted_by,insert_date,status_active,is_deleted";*/
		$field_array8="id,mst_id,fab_des,fab_val";
		$field_array9="id,mst_id,mes_des,mes_val";
		$field_array10="id,mst_id,mar_des,mar_val";
		
		
	   $sql_data=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e,col_f,col_g,col_h,col_i,col_j,col_k,col_l,col_m,	col_n,col_o,col_p,col_q,col_r,col_s,col_t,col_u,col_v,col_w,col_x,col_y,col_z,status_active,is_deleted from wo_pri_fabric_yarn_details where mst_id=$txt_quotation_id_old and status_active=1 and is_deleted=0 ");
	   $sql_data2=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e,col_f,col_g,col_h,col_i,col_j,col_k,col_l,col_m,	col_n,col_o,col_p,col_q,col_r,col_s,col_t,col_u,col_v,col_w,col_x,col_y,col_z,status_active,is_deleted from wo_pri_body_fabric_cost where mst_id=$txt_quotation_id_old and status_active=1 and is_deleted=0 ");
	  $sql_data3=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e,col_f,col_g,col_h,col_i,col_j,col_k,col_l,col_m,	col_n,col_o,col_p,col_q,col_r,col_s,col_t,col_u,col_v,col_w,col_x,col_y,col_z,status_active,is_deleted from wo_pri_other_cost where mst_id=$txt_quotation_id_old and status_active=1 and is_deleted=0 ");
	   $sql_data4=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e,col_f,col_g,col_h,col_i,col_j,col_k,col_l,col_m,	col_n,col_o,col_p,col_q,col_r,col_s,col_t,col_u,col_v,col_w,col_x,col_y,col_z,status_active,is_deleted from wo_pri_ebellishment_cost where mst_id=$txt_quotation_id_old and status_active=1 and is_deleted=0 ");
	    $sql_data5=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e,col_f,col_g,col_h,col_i,col_j,col_k,col_l,col_m,	col_n,col_o,col_p,col_q,col_r,col_s,col_t,col_u,col_v,col_w,col_x,col_y,col_z,status_active,is_deleted from wo_pri_trim_cost where mst_id=$txt_quotation_id_old and status_active=1 and is_deleted=0 ");
		$sql_data6=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e,col_f,col_g,col_h,col_i,col_j,col_k,col_l,col_m,	col_n,col_o,col_p,col_q,col_r,col_s,col_t,col_u,col_v,col_w,col_x,col_y,col_z,status_active,is_deleted from wo_pri_other_fabric_cost where mst_id=$txt_quotation_id_old and status_active=1 and is_deleted=0 ");
		$sql_data7=sql_select("select id,mst_id,type,col_a,col_b,col_c,col_d,col_e,col_f,col_g,col_h,col_i,col_j,col_k,col_l,col_m,	col_n,col_o,col_p,col_q,col_r,col_s,col_t,col_u,col_v,col_w,col_x,col_y,col_z,status_active,is_deleted from wo_pri_price_summary where mst_id=$txt_quotation_id_old and status_active=1 and is_deleted=0 ");
		$sql_data8=sql_select("select id,mst_id,fab_des,fab_val from wo_pri_sim_fab where mst_id=$txt_quotation_id_old");
		$sql_data9=sql_select("select id,mst_id,mes_des,mes_val from wo_pri_sim_mes where mst_id=$txt_quotation_id_old");
		$sql_data10=sql_select("select id,mst_id,mar_des,mar_val from wo_pri_sim_mar where mst_id=$txt_quotation_id_old");
	  
	   $i=1;
	   $data_array="";
	foreach($sql_data as $row)
	{
		    if ($i!=1) $data_array .=",";
			 $data_array .="(".$id.",'".$newid."','".$row[csf("type")]."','".$row[csf("col_a")]."','".$row[csf("col_b")]."','".$row[csf("col_c")]."','".$row[csf("col_d")]."','".$row[csf("col_e")]."','".$row[csf("col_f")]."','".$row[csf("col_g")]."','".$row[csf("col_h")]."','".$row[csf("col_i")]."','".$row[csf("col_j")]."','".$row[csf("col_k")]."','".$row[csf("col_l")]."','".$row[csf("col_m")]."','".$row[csf("col_n")]."','".$row[csf("col_o")]."','".$row[csf("col_p")]."','".$row[csf("col_q")]."','".$row[csf("col_r")]."','".$row[csf("col_s")]."','".$row[csf("col_t")]."','".$row[csf("col_v")]."','".$row[csf("col_w")]."','".$row[csf("col_x")]."','".$row[csf("col_y")]."','".$row[csf("col_x")]."','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."','".$row[csf("status_active")]."',0)";
			
		/*	$sql_data_cons=sql_select("Select id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs, body_length, body_sewing_margin,	body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin,front_rise_length,	front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin,total,marker_dia,marker_yds,marker_inch,gmts_pcs,marker_length,net_fab_cons from wo_pri_quo_fab_co_avg_con_dtls where quotation_id=$txt_quotation_id_old and wo_pri_quo_fab_co_dtls_id='".$row[csf("id")]."'");
			
			foreach($sql_data_cons as $row_cons)
	        {
			if ($add_comma!=0) $data_array1 .=",";
			$data_array1 .="(".$id1.",".$id.",".$newid.",'".$row_cons[csf("gmts_sizes")]."','".$row_cons[csf("dia_width")]."','".$row_cons[csf("cons")]."','".$row_cons[csf("process_loss_percent")]."','".$row_cons[csf("requirment")]."','".$row_cons[csf("pcs")]."','".$row_cons[csf("body_length")]."','".$row_cons[csf("body_sewing_margin")]."','".$row_cons[csf("body_hem_margin")]."','".$row_cons[csf("sleeve_length")]."','".$row_cons[csf("sleeve_sewing_margin")]."','".$row_cons[csf("sleeve_hem_margin")]."','".$row_cons[csf("half_chest_length")]."','".$row_cons[csf("half_chest_sewing_margin")]."','".$row_cons[csf("front_rise_length")]."','".$row_cons[csf("front_rise_sewing_margin")]."','".$row_cons[csf("west_band_length")]."','".$row_cons[csf("west_band_sewing_margin")]."','".$row_cons[csf("in_seam_length")]."','".$row_cons[csf("in_seam_sewing_margin")]."','".$row_cons[csf("in_seam_hem_margin")]."','".$row_cons[csf("half_thai_length")]."','".$row_cons[csf("half_thai_sewing_margin")]."','".$row_cons[csf("total")]."','".$row_cons[csf("marker_dia")]."','".$row_cons[csf("marker_yds")]."','".$row_cons[csf("marker_inch")]."','".$row_cons[csf("gmts_pcs")]."','".$row_cons[csf("marker_length")]."','".$row_cons[csf("net_fab_cons")]."')";
			$id1=$id1+1;
			$add_comma++;
	        }*/
			//print_r($data_array."PP".$field_array);die;
			$conversion_cost_headarr[$row[csf("id")]]=$id;
			$id=$id+1;
			$i++;
	}
	
	
	 $i2=1;
	 $data_array2="";
	foreach($sql_data2 as $row2)
	{
		    if ($i2!=1) $data_array2 .=",";
			 $data_array2 .="(".$id2.",'".$newid."','".$row2[csf("type")]."','".$row2[csf("col_a")]."','".$row2[csf("col_b")]."','".$row2[csf("col_c")]."','".$row2[csf("col_d")]."','".$row2[csf("col_e")]."','".$row2[csf("col_f")]."','".$row2[csf("col_g")]."','".$row2[csf("col_h")]."','".$row2[csf("col_i")]."','".$row2[csf("col_j")]."','".$row2[csf("col_k")]."','".$row2[csf("col_l")]."','".$row2[csf("col_m")]."','".$row2[csf("col_n")]."','".$row2[csf("col_o")]."','".$row2[csf("col_p")]."','".$row2[csf("col_q")]."','".$row2[csf("col_r")]."','".$row2[csf("col_s")]."','".$row2[csf("col_t")]."','".$row2[csf("col_v")]."','".$row2[csf("col_w")]."','".$row2[csf("col_x")]."','".$row2[csf("col_y")]."','".$row2[csf("col_x")]."','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."','".$row2[csf("status_active")]."',0)";
			
		/*	$sql_data_cons=sql_select("Select id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs, body_length, body_sewing_margin,	body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin,front_rise_length,	front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin,total,marker_dia,marker_yds,marker_inch,gmts_pcs,marker_length,net_fab_cons from wo_pri_quo_fab_co_avg_con_dtls where quotation_id=$txt_quotation_id_old and wo_pri_quo_fab_co_dtls_id='".$row[csf("id")]."'");
			
			foreach($sql_data_cons as $row_cons)
	        {
			if ($add_comma!=0) $data_array1 .=",";
			$data_array1 .="(".$id1.",".$id.",".$newid.",'".$row_cons[csf("gmts_sizes")]."','".$row_cons[csf("dia_width")]."','".$row_cons[csf("cons")]."','".$row_cons[csf("process_loss_percent")]."','".$row_cons[csf("requirment")]."','".$row_cons[csf("pcs")]."','".$row_cons[csf("body_length")]."','".$row_cons[csf("body_sewing_margin")]."','".$row_cons[csf("body_hem_margin")]."','".$row_cons[csf("sleeve_length")]."','".$row_cons[csf("sleeve_sewing_margin")]."','".$row_cons[csf("sleeve_hem_margin")]."','".$row_cons[csf("half_chest_length")]."','".$row_cons[csf("half_chest_sewing_margin")]."','".$row_cons[csf("front_rise_length")]."','".$row_cons[csf("front_rise_sewing_margin")]."','".$row_cons[csf("west_band_length")]."','".$row_cons[csf("west_band_sewing_margin")]."','".$row_cons[csf("in_seam_length")]."','".$row_cons[csf("in_seam_sewing_margin")]."','".$row_cons[csf("in_seam_hem_margin")]."','".$row_cons[csf("half_thai_length")]."','".$row_cons[csf("half_thai_sewing_margin")]."','".$row_cons[csf("total")]."','".$row_cons[csf("marker_dia")]."','".$row_cons[csf("marker_yds")]."','".$row_cons[csf("marker_inch")]."','".$row_cons[csf("gmts_pcs")]."','".$row_cons[csf("marker_length")]."','".$row_cons[csf("net_fab_cons")]."')";
			$id1=$id1+1;
			$add_comma++;
	        }*/
			//print_r($data_array."PP".$field_array);die;
			$conversion_cost_headarr[$row2[csf("id")]]=$id2;
			$id2=$id2+1;
			$i2++;
	}
	
	   $i3=1;
	   $data_array3="";
	foreach($sql_data3 as $row3)
	{
		    if ($i3!=1) $data_array3 .=",";
			 $data_array3 .="(".$id3.",'".$newid."','".$row3[csf("type")]."','".$row3[csf("col_a")]."','".$row3[csf("col_b")]."','".$row3[csf("col_c")]."','".$row3[csf("col_d")]."','".$row3[csf("col_e")]."','".$row3[csf("col_f")]."','".$row3[csf("col_g")]."','".$row3[csf("col_h")]."','".$row3[csf("col_i")]."','".$row3[csf("col_j")]."','".$row3[csf("col_k")]."','".$row3[csf("col_l")]."','".$row3[csf("col_m")]."','".$row3[csf("col_n")]."','".$row3[csf("col_o")]."','".$row3[csf("col_p")]."','".$row3[csf("col_q")]."','".$row3[csf("col_r")]."','".$row3[csf("col_s")]."','".$row3[csf("col_t")]."','".$row3[csf("col_v")]."','".$row3[csf("col_w")]."','".$row3[csf("col_x")]."','".$row3[csf("col_y")]."','".$row3[csf("col_x")]."','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."','".$row3[csf("status_active")]."',0)";
			
		/*	$sql_data_cons=sql_select("Select id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs, body_length, body_sewing_margin,	body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin,front_rise_length,	front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin,total,marker_dia,marker_yds,marker_inch,gmts_pcs,marker_length,net_fab_cons from wo_pri_quo_fab_co_avg_con_dtls where quotation_id=$txt_quotation_id_old and wo_pri_quo_fab_co_dtls_id='".$row[csf("id")]."'");
			
			foreach($sql_data_cons as $row_cons)
	        {
			if ($add_comma!=0) $data_array1 .=",";
			$data_array1 .="(".$id1.",".$id.",".$newid.",'".$row_cons[csf("gmts_sizes")]."','".$row_cons[csf("dia_width")]."','".$row_cons[csf("cons")]."','".$row_cons[csf("process_loss_percent")]."','".$row_cons[csf("requirment")]."','".$row_cons[csf("pcs")]."','".$row_cons[csf("body_length")]."','".$row_cons[csf("body_sewing_margin")]."','".$row_cons[csf("body_hem_margin")]."','".$row_cons[csf("sleeve_length")]."','".$row_cons[csf("sleeve_sewing_margin")]."','".$row_cons[csf("sleeve_hem_margin")]."','".$row_cons[csf("half_chest_length")]."','".$row_cons[csf("half_chest_sewing_margin")]."','".$row_cons[csf("front_rise_length")]."','".$row_cons[csf("front_rise_sewing_margin")]."','".$row_cons[csf("west_band_length")]."','".$row_cons[csf("west_band_sewing_margin")]."','".$row_cons[csf("in_seam_length")]."','".$row_cons[csf("in_seam_sewing_margin")]."','".$row_cons[csf("in_seam_hem_margin")]."','".$row_cons[csf("half_thai_length")]."','".$row_cons[csf("half_thai_sewing_margin")]."','".$row_cons[csf("total")]."','".$row_cons[csf("marker_dia")]."','".$row_cons[csf("marker_yds")]."','".$row_cons[csf("marker_inch")]."','".$row_cons[csf("gmts_pcs")]."','".$row_cons[csf("marker_length")]."','".$row_cons[csf("net_fab_cons")]."')";
			$id1=$id1+1;
			$add_comma++;
	        }*/
			//print_r($data_array."PP".$field_array);die;
			$conversion_cost_headarr[$row3[csf("id")]]=$id3;
			$id3=$id3+1;
			$i3++;
	}
	 
	   $i4=1;
	   $data_array4="";
	foreach($sql_data4 as $row4)
	{
		    if ($i4!=1) $data_array4 .=",";
			 $data_array4 .="(".$id4.",'".$newid."','".$row4[csf("type")]."','".$row4[csf("col_a")]."','".$row4[csf("col_b")]."','".$row4[csf("col_c")]."','".$row4[csf("col_d")]."','".$row4[csf("col_e")]."','".$row4[csf("col_f")]."','".$row4[csf("col_g")]."','".$row4[csf("col_h")]."','".$row4[csf("col_i")]."','".$row4[csf("col_j")]."','".$row4[csf("col_k")]."','".$row4[csf("col_l")]."','".$row4[csf("col_m")]."','".$row4[csf("col_n")]."','".$row4[csf("col_o")]."','".$row4[csf("col_p")]."','".$row4[csf("col_q")]."','".$row4[csf("col_r")]."','".$row4[csf("col_s")]."','".$row4[csf("col_t")]."','".$row4[csf("col_v")]."','".$row4[csf("col_w")]."','".$row4[csf("col_x")]."','".$row4[csf("col_y")]."','".$row4[csf("col_x")]."','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."','".$row4[csf("status_active")]."',0)";
			
		/*	$sql_data_cons=sql_select("Select id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs, body_length, body_sewing_margin,	body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin,front_rise_length,	front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin,total,marker_dia,marker_yds,marker_inch,gmts_pcs,marker_length,net_fab_cons from wo_pri_quo_fab_co_avg_con_dtls where quotation_id=$txt_quotation_id_old and wo_pri_quo_fab_co_dtls_id='".$row[csf("id")]."'");
			
			foreach($sql_data_cons as $row_cons)
	        {
			if ($add_comma!=0) $data_array1 .=",";
			$data_array1 .="(".$id1.",".$id.",".$newid.",'".$row_cons[csf("gmts_sizes")]."','".$row_cons[csf("dia_width")]."','".$row_cons[csf("cons")]."','".$row_cons[csf("process_loss_percent")]."','".$row_cons[csf("requirment")]."','".$row_cons[csf("pcs")]."','".$row_cons[csf("body_length")]."','".$row_cons[csf("body_sewing_margin")]."','".$row_cons[csf("body_hem_margin")]."','".$row_cons[csf("sleeve_length")]."','".$row_cons[csf("sleeve_sewing_margin")]."','".$row_cons[csf("sleeve_hem_margin")]."','".$row_cons[csf("half_chest_length")]."','".$row_cons[csf("half_chest_sewing_margin")]."','".$row_cons[csf("front_rise_length")]."','".$row_cons[csf("front_rise_sewing_margin")]."','".$row_cons[csf("west_band_length")]."','".$row_cons[csf("west_band_sewing_margin")]."','".$row_cons[csf("in_seam_length")]."','".$row_cons[csf("in_seam_sewing_margin")]."','".$row_cons[csf("in_seam_hem_margin")]."','".$row_cons[csf("half_thai_length")]."','".$row_cons[csf("half_thai_sewing_margin")]."','".$row_cons[csf("total")]."','".$row_cons[csf("marker_dia")]."','".$row_cons[csf("marker_yds")]."','".$row_cons[csf("marker_inch")]."','".$row_cons[csf("gmts_pcs")]."','".$row_cons[csf("marker_length")]."','".$row_cons[csf("net_fab_cons")]."')";
			$id1=$id1+1;
			$add_comma++;
	        }*/
			//print_r($data_array."PP".$field_array);die;
			$conversion_cost_headarr[$row4[csf("id")]]=$id4;
			$id4=$id4+1;
			$i4++;
	}
	
	   $i5=1;
	   $data_array5="";
	foreach($sql_data5 as $row5)
	{
		    if ($i5!=1) $data_array5 .=",";
			 $data_array5 .="(".$id5.",'".$newid."','".$row5[csf("type")]."','".$row5[csf("col_a")]."','".$row5[csf("col_b")]."','".$row5[csf("col_c")]."','".$row5[csf("col_d")]."','".$row5[csf("col_e")]."','".$row5[csf("col_f")]."','".$row5[csf("col_g")]."','".$row5[csf("col_h")]."','".$row5[csf("col_i")]."','".$row5[csf("col_j")]."','".$row5[csf("col_k")]."','".$row5[csf("col_l")]."','".$row5[csf("col_m")]."','".$row5[csf("col_n")]."','".$row5[csf("col_o")]."','".$row5[csf("col_p")]."','".$row5[csf("col_q")]."','".$row5[csf("col_r")]."','".$row5[csf("col_s")]."','".$row5[csf("col_t")]."','".$row5[csf("col_v")]."','".$row5[csf("col_w")]."','".$row5[csf("col_x")]."','".$row5[csf("col_y")]."','".$row5[csf("col_x")]."','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."','".$row5[csf("status_active")]."',0)";
			
		/*	$sql_data_cons=sql_select("Select id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs, body_length, body_sewing_margin,	body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin,front_rise_length,	front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin,total,marker_dia,marker_yds,marker_inch,gmts_pcs,marker_length,net_fab_cons from wo_pri_quo_fab_co_avg_con_dtls where quotation_id=$txt_quotation_id_old and wo_pri_quo_fab_co_dtls_id='".$row[csf("id")]."'");
			
			foreach($sql_data_cons as $row_cons)
	        {
			if ($add_comma!=0) $data_array1 .=",";
			$data_array1 .="(".$id1.",".$id.",".$newid.",'".$row_cons[csf("gmts_sizes")]."','".$row_cons[csf("dia_width")]."','".$row_cons[csf("cons")]."','".$row_cons[csf("process_loss_percent")]."','".$row_cons[csf("requirment")]."','".$row_cons[csf("pcs")]."','".$row_cons[csf("body_length")]."','".$row_cons[csf("body_sewing_margin")]."','".$row_cons[csf("body_hem_margin")]."','".$row_cons[csf("sleeve_length")]."','".$row_cons[csf("sleeve_sewing_margin")]."','".$row_cons[csf("sleeve_hem_margin")]."','".$row_cons[csf("half_chest_length")]."','".$row_cons[csf("half_chest_sewing_margin")]."','".$row_cons[csf("front_rise_length")]."','".$row_cons[csf("front_rise_sewing_margin")]."','".$row_cons[csf("west_band_length")]."','".$row_cons[csf("west_band_sewing_margin")]."','".$row_cons[csf("in_seam_length")]."','".$row_cons[csf("in_seam_sewing_margin")]."','".$row_cons[csf("in_seam_hem_margin")]."','".$row_cons[csf("half_thai_length")]."','".$row_cons[csf("half_thai_sewing_margin")]."','".$row_cons[csf("total")]."','".$row_cons[csf("marker_dia")]."','".$row_cons[csf("marker_yds")]."','".$row_cons[csf("marker_inch")]."','".$row_cons[csf("gmts_pcs")]."','".$row_cons[csf("marker_length")]."','".$row_cons[csf("net_fab_cons")]."')";
			$id1=$id1+1;
			$add_comma++;
	        }*/
			//print_r($data_array."PP".$field_array);die;
			$conversion_cost_headarr[$row5[csf("id")]]=$id5;
			$id5=$id5+1;
			$i5++;
	}
	 
	   $i6=1;
	   $data_array6="";
	foreach($sql_data6 as $row6)
	{
		    if ($i6!=1) $data_array6 .=",";
			 $data_array6 .="(".$id6.",'".$newid."','".$row6[csf("type")]."','".$row6[csf("col_a")]."','".$row6[csf("col_b")]."','".$row6[csf("col_c")]."','".$row6[csf("col_d")]."','".$row6[csf("col_e")]."','".$row6[csf("col_f")]."','".$row6[csf("col_g")]."','".$row6[csf("col_h")]."','".$row6[csf("col_i")]."','".$row6[csf("col_j")]."','".$row6[csf("col_k")]."','".$row6[csf("col_l")]."','".$row6[csf("col_m")]."','".$row6[csf("col_n")]."','".$row6[csf("col_o")]."','".$row6[csf("col_p")]."','".$row6[csf("col_q")]."','".$row6[csf("col_r")]."','".$row6[csf("col_s")]."','".$row6[csf("col_t")]."','".$row6[csf("col_v")]."','".$row6[csf("col_w")]."','".$row6[csf("col_x")]."','".$row6[csf("col_y")]."','".$row6[csf("col_x")]."','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."','".$row6[csf("status_active")]."',0)";
			
		/*	$sql_data_cons=sql_select("Select id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs, body_length, body_sewing_margin,	body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin,front_rise_length,	front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin,total,marker_dia,marker_yds,marker_inch,gmts_pcs,marker_length,net_fab_cons from wo_pri_quo_fab_co_avg_con_dtls where quotation_id=$txt_quotation_id_old and wo_pri_quo_fab_co_dtls_id='".$row[csf("id")]."'");
			
			foreach($sql_data_cons as $row_cons)
	        {
			if ($add_comma!=0) $data_array1 .=",";
			$data_array1 .="(".$id1.",".$id.",".$newid.",'".$row_cons[csf("gmts_sizes")]."','".$row_cons[csf("dia_width")]."','".$row_cons[csf("cons")]."','".$row_cons[csf("process_loss_percent")]."','".$row_cons[csf("requirment")]."','".$row_cons[csf("pcs")]."','".$row_cons[csf("body_length")]."','".$row_cons[csf("body_sewing_margin")]."','".$row_cons[csf("body_hem_margin")]."','".$row_cons[csf("sleeve_length")]."','".$row_cons[csf("sleeve_sewing_margin")]."','".$row_cons[csf("sleeve_hem_margin")]."','".$row_cons[csf("half_chest_length")]."','".$row_cons[csf("half_chest_sewing_margin")]."','".$row_cons[csf("front_rise_length")]."','".$row_cons[csf("front_rise_sewing_margin")]."','".$row_cons[csf("west_band_length")]."','".$row_cons[csf("west_band_sewing_margin")]."','".$row_cons[csf("in_seam_length")]."','".$row_cons[csf("in_seam_sewing_margin")]."','".$row_cons[csf("in_seam_hem_margin")]."','".$row_cons[csf("half_thai_length")]."','".$row_cons[csf("half_thai_sewing_margin")]."','".$row_cons[csf("total")]."','".$row_cons[csf("marker_dia")]."','".$row_cons[csf("marker_yds")]."','".$row_cons[csf("marker_inch")]."','".$row_cons[csf("gmts_pcs")]."','".$row_cons[csf("marker_length")]."','".$row_cons[csf("net_fab_cons")]."')";
			$id1=$id1+1;
			$add_comma++;
	        }*/
			//print_r($data_array."PP".$field_array);die;
			$conversion_cost_headarr[$row6[csf("id")]]=$id6;
			$id6=$id6+1;
			$i6++;
	}
	 	$i7=1;
	   $data_array7="";
	foreach($sql_data7 as $row7)
	{
		    if ($i7!=1) $data_array7 .=",";
			 $data_array7 .="(".$id7.",'".$newid."','".$row7[csf("type")]."','".$row7[csf("col_a")]."','".$row7[csf("col_b")]."','".$row7[csf("col_c")]."','".$row7[csf("col_d")]."','".$row7[csf("col_e")]."','".$row7[csf("col_f")]."','".$row7[csf("col_g")]."','".$row7[csf("col_h")]."','".$row7[csf("col_i")]."','".$row7[csf("col_j")]."','".$row7[csf("col_k")]."','".$row7[csf("col_l")]."','".$row7[csf("col_m")]."','".$row7[csf("col_n")]."','".$row7[csf("col_o")]."','".$row7[csf("col_p")]."','".$row7[csf("col_q")]."','".$row7[csf("col_r")]."','".$row7[csf("col_s")]."','".$row7[csf("col_t")]."','".$row7[csf("col_v")]."','".$row7[csf("col_w")]."','".$row7[csf("col_x")]."','".$row7[csf("col_y")]."','".$row7[csf("col_x")]."','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."','".$row7[csf("status_active")]."',0)";
			
		/*	$sql_data_cons=sql_select("Select id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs, body_length, body_sewing_margin,	body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin,front_rise_length,	front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin,total,marker_dia,marker_yds,marker_inch,gmts_pcs,marker_length,net_fab_cons from wo_pri_quo_fab_co_avg_con_dtls where quotation_id=$txt_quotation_id_old and wo_pri_quo_fab_co_dtls_id='".$row[csf("id")]."'");
			
			foreach($sql_data_cons as $row_cons)
	        {
			if ($add_comma!=0) $data_array1 .=",";
			$data_array1 .="(".$id1.",".$id.",".$newid.",'".$row_cons[csf("gmts_sizes")]."','".$row_cons[csf("dia_width")]."','".$row_cons[csf("cons")]."','".$row_cons[csf("process_loss_percent")]."','".$row_cons[csf("requirment")]."','".$row_cons[csf("pcs")]."','".$row_cons[csf("body_length")]."','".$row_cons[csf("body_sewing_margin")]."','".$row_cons[csf("body_hem_margin")]."','".$row_cons[csf("sleeve_length")]."','".$row_cons[csf("sleeve_sewing_margin")]."','".$row_cons[csf("sleeve_hem_margin")]."','".$row_cons[csf("half_chest_length")]."','".$row_cons[csf("half_chest_sewing_margin")]."','".$row_cons[csf("front_rise_length")]."','".$row_cons[csf("front_rise_sewing_margin")]."','".$row_cons[csf("west_band_length")]."','".$row_cons[csf("west_band_sewing_margin")]."','".$row_cons[csf("in_seam_length")]."','".$row_cons[csf("in_seam_sewing_margin")]."','".$row_cons[csf("in_seam_hem_margin")]."','".$row_cons[csf("half_thai_length")]."','".$row_cons[csf("half_thai_sewing_margin")]."','".$row_cons[csf("total")]."','".$row_cons[csf("marker_dia")]."','".$row_cons[csf("marker_yds")]."','".$row_cons[csf("marker_inch")]."','".$row_cons[csf("gmts_pcs")]."','".$row_cons[csf("marker_length")]."','".$row_cons[csf("net_fab_cons")]."')";
			$id1=$id1+1;
			$add_comma++;
	        }*/
			//print_r($data_array."PP".$field_array);die;
			$conversion_cost_headarr[$row7[csf("id")]]=$id7;
			$id7=$id7+1;
			$i7++;
	}
		$i8=1;
	   $data_array8="";
	foreach($sql_data8 as $row8)
	{
		    if ($i8!=1) $data_array8 .=",";
			 $data_array8 .="(".$id8.",'".$newid."','".$row8[csf("fab_des")]."','".$row8[csf("fab_val")]."')";
			
		/*	$sql_data_cons=sql_select("Select id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs, body_length, body_sewing_margin,	body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin,front_rise_length,	front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin,total,marker_dia,marker_yds,marker_inch,gmts_pcs,marker_length,net_fab_cons from wo_pri_quo_fab_co_avg_con_dtls where quotation_id=$txt_quotation_id_old and wo_pri_quo_fab_co_dtls_id='".$row[csf("id")]."'");
			
			foreach($sql_data_cons as $row_cons)
	        {
			if ($add_comma!=0) $data_array1 .=",";
			$data_array1 .="(".$id1.",".$id.",".$newid.",'".$row_cons[csf("gmts_sizes")]."','".$row_cons[csf("dia_width")]."','".$row_cons[csf("cons")]."','".$row_cons[csf("process_loss_percent")]."','".$row_cons[csf("requirment")]."','".$row_cons[csf("pcs")]."','".$row_cons[csf("body_length")]."','".$row_cons[csf("body_sewing_margin")]."','".$row_cons[csf("body_hem_margin")]."','".$row_cons[csf("sleeve_length")]."','".$row_cons[csf("sleeve_sewing_margin")]."','".$row_cons[csf("sleeve_hem_margin")]."','".$row_cons[csf("half_chest_length")]."','".$row_cons[csf("half_chest_sewing_margin")]."','".$row_cons[csf("front_rise_length")]."','".$row_cons[csf("front_rise_sewing_margin")]."','".$row_cons[csf("west_band_length")]."','".$row_cons[csf("west_band_sewing_margin")]."','".$row_cons[csf("in_seam_length")]."','".$row_cons[csf("in_seam_sewing_margin")]."','".$row_cons[csf("in_seam_hem_margin")]."','".$row_cons[csf("half_thai_length")]."','".$row_cons[csf("half_thai_sewing_margin")]."','".$row_cons[csf("total")]."','".$row_cons[csf("marker_dia")]."','".$row_cons[csf("marker_yds")]."','".$row_cons[csf("marker_inch")]."','".$row_cons[csf("gmts_pcs")]."','".$row_cons[csf("marker_length")]."','".$row_cons[csf("net_fab_cons")]."')";
			$id1=$id1+1;
			$add_comma++;
	        }*/
			//print_r($data_array."PP".$field_array);die;
			$conversion_cost_headarr[$row8[csf("id")]]=$id8;
			$id8=$id8+1;
			$i8++;
	}
	$i9=1;
	$data_array9="";
	foreach($sql_data9 as $row9)
	{
		    if ($i9!=1) $data_array9 .=",";
			 $data_array9 .="(".$id9.",'".$newid."','".$row9[csf("mes_des")]."','".$row9[csf("mes_val")]."')";
			
		/*	$sql_data_cons=sql_select("Select id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs, body_length, body_sewing_margin,	body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin,front_rise_length,	front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin,total,marker_dia,marker_yds,marker_inch,gmts_pcs,marker_length,net_fab_cons from wo_pri_quo_fab_co_avg_con_dtls where quotation_id=$txt_quotation_id_old and wo_pri_quo_fab_co_dtls_id='".$row[csf("id")]."'");
			
			foreach($sql_data_cons as $row_cons)
	        {
			if ($add_comma!=0) $data_array1 .=",";
			$data_array1 .="(".$id1.",".$id.",".$newid.",'".$row_cons[csf("gmts_sizes")]."','".$row_cons[csf("dia_width")]."','".$row_cons[csf("cons")]."','".$row_cons[csf("process_loss_percent")]."','".$row_cons[csf("requirment")]."','".$row_cons[csf("pcs")]."','".$row_cons[csf("body_length")]."','".$row_cons[csf("body_sewing_margin")]."','".$row_cons[csf("body_hem_margin")]."','".$row_cons[csf("sleeve_length")]."','".$row_cons[csf("sleeve_sewing_margin")]."','".$row_cons[csf("sleeve_hem_margin")]."','".$row_cons[csf("half_chest_length")]."','".$row_cons[csf("half_chest_sewing_margin")]."','".$row_cons[csf("front_rise_length")]."','".$row_cons[csf("front_rise_sewing_margin")]."','".$row_cons[csf("west_band_length")]."','".$row_cons[csf("west_band_sewing_margin")]."','".$row_cons[csf("in_seam_length")]."','".$row_cons[csf("in_seam_sewing_margin")]."','".$row_cons[csf("in_seam_hem_margin")]."','".$row_cons[csf("half_thai_length")]."','".$row_cons[csf("half_thai_sewing_margin")]."','".$row_cons[csf("total")]."','".$row_cons[csf("marker_dia")]."','".$row_cons[csf("marker_yds")]."','".$row_cons[csf("marker_inch")]."','".$row_cons[csf("gmts_pcs")]."','".$row_cons[csf("marker_length")]."','".$row_cons[csf("net_fab_cons")]."')";
			$id1=$id1+1;
			$add_comma++;
	        }*/
			//print_r($data_array."PP".$field_array);die;
			$conversion_cost_headarr[$row9[csf("id")]]=$id9;
			$id9=$id9+1;
			$i9++;
	}
	$i10=1;
	$data_array10="";
	foreach($sql_data10 as $row10)
	{
		    if ($i10!=1) $data_array10 .=",";
			 $data_array10 .="(".$id10.",'".$newid."','".$row10[csf("mar_des")]."','".$row10[csf("mar_val")]."')";
			
		/*	$sql_data_cons=sql_select("Select id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs, body_length, body_sewing_margin,	body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin,front_rise_length,	front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin,total,marker_dia,marker_yds,marker_inch,gmts_pcs,marker_length,net_fab_cons from wo_pri_quo_fab_co_avg_con_dtls where quotation_id=$txt_quotation_id_old and wo_pri_quo_fab_co_dtls_id='".$row[csf("id")]."'");
			
			foreach($sql_data_cons as $row_cons)
	        {
			if ($add_comma!=0) $data_array1 .=",";
			$data_array1 .="(".$id1.",".$id.",".$newid.",'".$row_cons[csf("gmts_sizes")]."','".$row_cons[csf("dia_width")]."','".$row_cons[csf("cons")]."','".$row_cons[csf("process_loss_percent")]."','".$row_cons[csf("requirment")]."','".$row_cons[csf("pcs")]."','".$row_cons[csf("body_length")]."','".$row_cons[csf("body_sewing_margin")]."','".$row_cons[csf("body_hem_margin")]."','".$row_cons[csf("sleeve_length")]."','".$row_cons[csf("sleeve_sewing_margin")]."','".$row_cons[csf("sleeve_hem_margin")]."','".$row_cons[csf("half_chest_length")]."','".$row_cons[csf("half_chest_sewing_margin")]."','".$row_cons[csf("front_rise_length")]."','".$row_cons[csf("front_rise_sewing_margin")]."','".$row_cons[csf("west_band_length")]."','".$row_cons[csf("west_band_sewing_margin")]."','".$row_cons[csf("in_seam_length")]."','".$row_cons[csf("in_seam_sewing_margin")]."','".$row_cons[csf("in_seam_hem_margin")]."','".$row_cons[csf("half_thai_length")]."','".$row_cons[csf("half_thai_sewing_margin")]."','".$row_cons[csf("total")]."','".$row_cons[csf("marker_dia")]."','".$row_cons[csf("marker_yds")]."','".$row_cons[csf("marker_inch")]."','".$row_cons[csf("gmts_pcs")]."','".$row_cons[csf("marker_length")]."','".$row_cons[csf("net_fab_cons")]."')";
			$id1=$id1+1;
			$add_comma++;
	        }*/
			//print_r($data_array."PP".$field_array);die;
			$conversion_cost_headarr[$row10[csf("id")]]=$id10;
			$id10=$id10+1;
			$i10++;
	}
	
	 //echo "0**"."INSERT INTO wo_pri_body_fabric_cost (".$field_array2.") VALUES ".$data_array2;die;
	   $rID=sql_insert("wo_pri_fabric_yarn_details",$field_array,$data_array,0);
	   $rID2=sql_insert("wo_pri_body_fabric_cost",$field_array,$data_array2,0);
	   $rID3=sql_insert("wo_pri_other_cost",$field_array,$data_array3,0);
		$rID4=sql_insert("wo_pri_ebellishment_cost",$field_array,$data_array4,0);
		$rID5=sql_insert("wo_pri_trim_cost",$field_array,$data_array5,0); 
		$rID6=sql_insert("wo_pri_other_fabric_cost",$field_array,$data_array6,0); 
		$rID7=sql_insert("wo_pri_price_summary",$field_array,$data_array7,0); 
		$rID8=sql_insert("wo_pri_sim_fab",$field_array8,$data_array8,0); 
		$rID9=sql_insert("wo_pri_sim_mes",$field_array9,$data_array9,0); 
		$rID10=sql_insert("wo_pri_sim_mar",$field_array10,$data_array10,0); 
	  // echo $rID;die;
		//$rID=sql_insert("wo_pri_quo_fab_co_avg_con_dtls",$field_array1,$data_array1,1);
		
		//---Yarn Cost--------------
		/* $iy=1;
		 $id_yarn=return_next_id( "id", "wo_pri_quo_fab_yarn_cost_dtls", 1 ) ;
		 $field_array_yarn="id,quotation_id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, status_active, is_deleted";
		 $sql_data_yarn=sql_select("Select id,quotation_id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, status_active, is_deleted from wo_pri_quo_fab_yarn_cost_dtls where quotation_id=$txt_quotation_id_old and status_active=1 and  is_deleted =0");
		 foreach($sql_data_yarn as $row_yarn)
	     {
			if ($iy!=1) $data_array_yarn .=",";
			$data_array_yarn .="(".$id_yarn.",".$newid.",'".$row_yarn[csf("count_id")]."','".$row_yarn[csf("copm_one_id")]."','".$row_yarn[csf("percent_one")]."','".$row_yarn[csf("copm_two_id")]."','".$row_yarn[csf("percent_two")]."','".$row_yarn[csf("type_id")]."','".$row_yarn[csf("cons_ratio")]."','".$row_yarn[csf("cons_qnty")]."','".$row_yarn[csf("rate")]."','".$row_yarn[csf("amount")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$row_yarn[csf("status_active")]."',0)";
			$id_yarn=$id_yarn+1;
			$iy++;
		 }
		 $rID=sql_insert("wo_pri_quo_fab_yarn_cost_dtls",$field_array_yarn,$data_array_yarn,0);
		 //---Yarn Cost End --------------
		 
		 //---Conversion Cost--------------
		 $ifc=1;
		/* $idfc=return_next_id( "id", "wo_pri_quo_fab_conv_cost_dtls", 1 ) ;
		 $field_array_fc="id,quotation_id,cost_head,cons_type,req_qnty,charge_unit,amount,charge_lib_id,inserted_by,insert_date,status_active,is_deleted";
		 $sql_data_con=sql_select("Select id,quotation_id,cost_head,cons_type,req_qnty,charge_unit,amount,charge_lib_id,inserted_by,insert_date,status_active,is_deleted from wo_pri_quo_fab_conv_cost_dtls where quotation_id=$txt_quotation_id_old and status_active=1 and  is_deleted =0");
		 foreach($sql_data_con as $row_con)
	     {
			if ($ifc!=1) $data_array_fc .=",";
			$data_array_fc .="(".$idfc.",".$newid.",'".$conversion_cost_headarr[$row_con[csf("cost_head")]]."','".$row_con[csf("cons_type")]."','".$row_con[csf("req_qnty")]."','".$row_con[csf("charge_unit")]."','".$row_con[csf("amount")]."','".$row_con[csf("charge_lib_id")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$row_con[csf("status_active")]."',0)";
			$idfc=$idfc+1;
			$ifc++;
		 }*/
		 $rID=sql_insert("wo_pri_quo_fab_conv_cost_dtls",$field_array_fc,$data_array_fc,0);
		 //---Conversion Cost End --------------
		 
		 //---Trim Cost--------------
		 $it=1;
		 $idt=return_next_id( "id", "wo_pri_quo_trim_cost_dtls", 1 ) ;
		 $field_array_t="id,quotation_id, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, inserted_by, insert_date, status_active,is_deleted";
		 $sql_data_t=sql_select("Select id,quotation_id, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, inserted_by, insert_date, status_active,is_deleted from wo_pri_quo_trim_cost_dtls where quotation_id=$txt_quotation_id_old and status_active=1 and  is_deleted =0");
		 foreach($sql_data_t as $row_t)
	     {
			if ($it!=1) $data_array_t .=",";
			$data_array_t .="(".$idt.",".$newid.",'".$row_t[csf("trim_group")]."','".$row_t[csf("cons_uom")]."','".$row_t[csf("cons_dzn_gmts")]."','".$row_t[csf("rate")]."','".$row_t[csf("amount")]."','".$row_t[csf("apvl_req")]."','".$row_t[csf("nominated_supp")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$row_t[csf("status_active")]."',0)";
			$idt=$idt+1;
			$it++;
		 }
		 $rID=sql_insert("wo_pri_quo_trim_cost_dtls",$field_array_t,$data_array_t,0);
		 //---Trim Cost End --------------
		 
		 
		 //---Embelishment And Wash Cost--------------
		 $iem=1;
		 $idem=return_next_id( "id", "wo_pri_quo_embe_cost_dtls", 1 ) ;
		 $field_array_em="id,quotation_id,emb_name,emb_type,cons_dzn_gmts,rate,amount,charge_lib_id,inserted_by,insert_date,status_active,is_deleted";
		 
		 $sql_data_em=sql_select("Select id,quotation_id,emb_name,emb_type,cons_dzn_gmts,rate,amount,charge_lib_id,inserted_by,insert_date,status_active,is_deleted from wo_pri_quo_embe_cost_dtls where quotation_id=$txt_quotation_id_old  and status_active=1 and  is_deleted =0");
		 foreach($sql_data_em as $row_em)
	     {
			if ($iem!=1) $data_array_em .=",";
			$data_array_em .="(".$idem.",".$newid.",'".$row_em[csf("emb_name")]."','".$row_em[csf("emb_type")]."','".$row_em[csf("cons_dzn_gmts")]."','".$row_em[csf("rate")]."','".$row_em[csf("amount")]."','".$row_em[csf("charge_lib_id")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$row_em[csf("status_active")]."',0)";
			$idem=$idem+1;
			$iem++;
		 }
		 $rID=sql_insert("wo_pri_quo_embe_cost_dtls",$field_array_em,$data_array_em,0);
		 //---Embelishment And Wash Cost End --------------
		 
		 //---Commercial Cost--------------
		 $icmr=1;
		 $idcmr=return_next_id( "id", "wo_pri_quo_comarcial_cost_dtls", 1 ) ;
		 $field_array_cmr="id,quotation_id,item_id,rate,amount,inserted_by,insert_date,status_active,is_deleted ";
		 
		 $sql_data_cmr=sql_select("Select id,quotation_id,item_id,rate,amount,inserted_by,insert_date,status_active,is_deleted  from wo_pri_quo_comarcial_cost_dtls where quotation_id=$txt_quotation_id_old  and status_active=1 and  is_deleted =0");
		 foreach($sql_data_cmr as $row_cmr)
	     {
			if ($icmr!=1) $data_array_cmr .=",";
			$data_array_cmr .="(".$idcmr.",".$newid.",'".$row_cmr[csf("item_id")]."','".$row_cmr[csf("rate")]."','".$row_cmr[csf("amount")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$row_cmr[csf("status_active")]."',0)";
			$idcmr=$idcmr+1;
			$icmr++;
		 }
		 $rID=sql_insert("wo_pri_quo_comarcial_cost_dtls",$field_array_cmr,$data_array_cmr,0);
		 //---Commercial Cost End --------------
		 
		 //---Commision Cost--------------
		 $icms=1;
		 $idcms=return_next_id( "id", "wo_pri_quo_commiss_cost_dtls", 1 ) ;
		 $field_array_cms="id,quotation_id,particulars_id,commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,status_active,is_deleted ";
		 
		 $sql_data_cms=sql_select("Select id,quotation_id,particulars_id,commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,status_active,is_deleted   from wo_pri_quo_commiss_cost_dtls where quotation_id=$txt_quotation_id_old  and status_active=1 and  is_deleted =0");
		 foreach($sql_data_cms as $row_cms)
	     {
			if ($icms!=1) $data_array_cms .=",";
			$data_array_cms .="(".$idcms.",".$newid.",'".$row_cms[csf("particulars_id")]."','".$row_cms[csf("commission_base_id")]."','".$row_cms[csf("commision_rate")]."','".$row_cms[csf("commission_amount")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$row_cms[csf("status_active")]."',0)";
			$idcms=$idcms+1;
			$icms++;
		 }
		 $rID=sql_insert("wo_pri_quo_commiss_cost_dtls",$field_array_cms,$data_array_cms,0);
		 //---Commision Cost End --------------
		 
		 
		 
		 //---wo_price_quotation_costing_mst Table--------------
		 $id_costing_mst=return_next_id( "id", "wo_price_quotation_costing_mst", 1 ) ;
		$field_array_costing_mst="id,quotation_id,costing_per_id,order_uom_id,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,lab_test,lab_test_percent,inspection, 	inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent,certificate_pre_cost,certificate_percent, common_oh,common_oh_percent,total_cost,total_cost_percent,commission,commission_percent,final_cost_dzn,final_cost_dzn_percent,final_cost_pcs,final_cost_set_pcs_rate,1st_quoted_price,1st_quoted_price_percent,1st_quoted_price_date,revised_price,revised_price_date,confirm_price,confirm_price_set_pcs_rate,confirm_price_dzn, confirm_price_dzn_percent, margin_dzn,margin_dzn_percent,price_with_commn_dzn,price_with_commn_percent_dzn,price_with_commn_pcs,price_with_commn_percent_pcs, confirm_date,asking_quoted_price,asking_quoted_price_percent, inserted_by, insert_date, status_active, is_deleted ";
		 
		 $sql_data_costing_mst=sql_select("Select id,quotation_id,costing_per_id,order_uom_id,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,lab_test,lab_test_percent,inspection, 	inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent,certificate_pre_cost,certificate_percent,common_oh,common_oh_percent,total_cost,total_cost_percent,commission,commission_percent,final_cost_dzn,final_cost_dzn_percent,final_cost_pcs,final_cost_set_pcs_rate,1st_quoted_price,1st_quoted_price_percent,1st_quoted_price_date,revised_price,revised_price_date,confirm_price,confirm_price_set_pcs_rate,confirm_price_dzn, confirm_price_dzn_percent, margin_dzn,margin_dzn_percent,price_with_commn_dzn,price_with_commn_percent_dzn,price_with_commn_pcs,price_with_commn_percent_pcs, confirm_date,asking_quoted_price,asking_quoted_price_percent, inserted_by, insert_date, status_active, is_deleted    from wo_price_quotation_costing_mst where quotation_id=$txt_quotation_id_old  and status_active=1 and  is_deleted =0");
		 foreach($sql_data_costing_mst as $row_costing_mst)
	     {
			$data_array_costing_mst="(".$id_costing_mst.",".$newid.",'".$row_costing_mst[csf("costing_per_id")]."','".$row_costing_mst[csf("order_uom_id")]."','".$row_costing_mst[csf("fabric_cost")]."','".$row_costing_mst[csf("fabric_cost_percent")]."','".$row_costing_mst[csf("trims_cost")]."','".$row_costing_mst[csf("trims_cost_percent")]."','".$row_costing_mst[csf("embel_cost")]."','".$row_costing_mst[csf("embel_cost_percent")]."','".$row_costing_mst[csf("wash_cost")]."','".$row_costing_mst[csf("wash_cost_percent")]."','".$row_costing_mst[csf("comm_cost")]."','".$row_costing_mst[csf("comm_cost_percent")]."','".$row_costing_mst[csf("lab_test")]."','".$row_costing_mst[csf("lab_test_percent")]."','".$row_costing_mst[csf("inspection")]."','".$row_costing_mst[csf("inspection_percent")]."','".$row_costing_mst[csf("cm_cost")]."','".$row_costing_mst[csf("cm_cost_percent")]."','".$row_costing_mst[csf("freight")]."','".$row_costing_mst[csf("freight_percent")]."','".$row_costing_mst[csf("currier_pre_cost")]."','".$row_costing_mst[csf("currier_percent")]."','".$row_costing_mst[csf("certificate_pre_cost")]."','".$row_costing_mst[csf("certificate_percent")]."','".$row_costing_mst[csf("common_oh")]."','".$row_costing_mst[csf("common_oh_percent")]."','".$row_costing_mst[csf("total_cost")]."','".$row_costing_mst[csf("total_cost_percent")]."','".$row_costing_mst[csf("commission")]."','".$row_costing_mst[csf("commission_percent")]."','".$row_costing_mst[csf("final_cost_dzn")]."','".$row_costing_mst[csf("final_cost_dzn_percent")]."','".$row_costing_mst[csf("final_cost_pcs")]."','".$row_costing_mst[csf("final_cost_set_pcs_rate")]."','".$row_costing_mst[csf("1st_quoted_price")]."','".$row_costing_mst[csf("1st_quoted_price_percent")]."','".$row_costing_mst[csf("1st_quoted_price_date")]."','".$row_costing_mst[csf("revised_price")]."','".$row_costing_mst[csf("revised_price_date")]."','".$row_costing_mst[csf("confirm_price")]."','".$row_costing_mst[csf("confirm_price_set_pcs_rate")]."','".$row_costing_mst[csf("confirm_price_dzn")]."','".$row_costing_mst[csf("confirm_price_dzn_percent")]."','".$row_costing_mst[csf("margin_dzn")]."','".$row_costing_mst[csf("margin_dzn_percent")]."','".$row_costing_mst[csf("price_with_commn_dzn")]."','".$row_costing_mst[csf("price_with_commn_percent_dzn")]."','".$row_costing_mst[csf("price_with_commn_pcs")]."','".$row_costing_mst[csf("price_with_commn_percent_pcs")]."','".$row_costing_mst[csf("confirm_date")]."','".$row_costing_mst[csf("asking_quoted_price")]."','".$row_costing_mst[csf("asking_quoted_price_percent")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
		 }
		 $rID=sql_insert("wo_price_quotation_costing_mst",$field_array_costing_mst,$data_array_costing_mst,0);
		 //---wo_price_quotation_costing_mst Cost End --------------
		 
		 
		 
		 //---wo_pri_quo_sum_dtls Table--------------
		 $id_sum=return_next_id( "id", "wo_pri_quo_sum_dtls", 1 ) ;
		$field_array_sum="id,quotation_id,fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_amount,yarn_cons_qnty,yarn_amount,conv_req_qnty,conv_charge_unit,conv_amount, 	trim_cons,trim_rate,trim_amount,emb_amount,wash_amount,comar_rate,comar_amount,commis_rate,commis_amount,inserted_by,insert_date,status_active,is_deleted";
		 
		 $sql_data_sum=sql_select("Select id,quotation_id,fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_amount,yarn_cons_qnty,yarn_amount,conv_req_qnty,conv_charge_unit,conv_amount, 	trim_cons,trim_rate,trim_amount,emb_amount,wash_amount,comar_rate,comar_amount,commis_rate,commis_amount,inserted_by,insert_date,status_active,is_deleted   from wo_pri_quo_sum_dtls where quotation_id=$txt_quotation_id_old  and status_active=1 and  is_deleted =0");
		 foreach($sql_data_sum as $row_sum)
	     {
			$data_array_sum="(".$id_sum.",".$newid.",'".$row_sum[csf("fab_yarn_req_kg")]."','".$row_sum[csf("fab_woven_req_yds")]."','".$row_sum[csf("fab_knit_req_kg")]."','".$row_sum[csf("fab_amount")]."','".$row_sum[csf("yarn_cons_qnty")]."','".$row_sum[csf("yarn_amount")]."','".$row_sum[csf("conv_req_qnty")]."','".$row_sum[csf("conv_charge_unit")]."','".$row_sum[csf("conv_amount")]."','".$row_sum[csf("trim_cons")]."','".$row_sum[csf("trim_rate")]."','".$row_sum[csf("trim_amount")]."','".$row_sum[csf("emb_amount")]."','".$row_sum[csf("wash_amount")]."','".$row_sum[csf("comar_rate")]."','".$row_sum[csf("comar_amount")]."','".$row_sum[csf("commis_rate")]."','".$row_sum[csf("commis_amount")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
		 }
		 $rID=sql_insert("wo_pri_quo_sum_dtls",$field_array_sum,$data_array_sum,0);
		 
		 return $id_costing_mst;
		 //---wo_pri_quo_sum_dtls Cost End --------------*/
		 if($db_type==0)
			{
				if($rID==1 && $rID2==1 && $rID3==1 && $rID4==1 && $rID5==1 &&   $rID6==1 &&  $rID7==1 && $rID8==1 && $rID9==1 && $rID10==1)
				{
					mysql_query("COMMIT");  
					echo "0**".$newid;//."**".$id;.$id_costing_mst;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$newid;//."**".$id;"**".$id_costing_mst;
				}
			}
}

/*function sql_insert2($strTable,$arrNames,$arrValues, $commit,$contain_lob )
{
	global $con ;
	
	$strQuery= "INSERT INTO ".$strTable." (".$arrNames.") VALUES ".$arrValues.""; 
	 //return $strQuery; die;
	 
	mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
	
	$result=mysql_query($strQuery); 
	
	$_SESSION['last_query']=$_SESSION['last_query'].";;".$strQuery;
	
	if ($commit==1)
	{
		$pc_time= add_time(date("H:i:s",time()),360);  
		$pc_date = date("Y-m-d",strtotime(add_time(date("H:i:s",time()),360)));
		
		$strQuery= "INSERT INTO activities_history ( session_id,user_id,ip_address,entry_time,entry_date,module_name,form_name,query_details,query_type) VALUES ('".$_SESSION['logic_erp']["history_id"]."','".$_SESSION['logic_erp']["user_id"]."','".$_SESSION['logic_erp']["pc_local_ip"]."','".$pc_time."','".$pc_date."','".$_SESSION["module_id"]."','".$_SESSION['menu_id']."','".encrypt($_SESSION['last_query'])."','0')"; 

		mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
		 
		$result111=mysql_query($strQuery); 
		$_SESSION['last_query']="";
	}
	  
		return $result;
	die;
}*/
?>

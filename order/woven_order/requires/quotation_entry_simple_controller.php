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
	echo create_drop_down( "cbo_buyer_name", 160, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );     	 
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
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'quotation_entry_simple_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
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
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_quotation_id_list_view', 'search_div', 'quotation_entry_simple_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
	if ($data[1]!=0) $buyer=" and buyer_id='$data[1]'"; else { echo "Please Select Buyer First."; die; }
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
	$data_array=sql_select("select id, company_id, buyer_id, style_ref, revised_no, pord_dept,product_code, style_desc, currency, agent, offer_qnty, region, color_range, incoterm, incoterm_place, machine_line, prod_line_hr, fabric_source, costing_per, quot_date, est_ship_date,op_date, factory, remarks, garments_nature,order_uom,gmts_item_id,set_break_down,total_set_qnty ,cm_cost_predefined_method_id,exchange_rate,sew_smv,cut_smv,sew_effi_percent,cut_effi_percent,efficiency_wastage_percent,season, approved,inserted_by, insert_date, status_active, is_deleted from wo_pri_sim_mst where id='$data'");
	foreach ($data_array as $row)
	{
		echo "load_drop_down( 'requires/quotation_entry_simple_controller', '".$row[csf("company_id")]."', 'load_drop_down_buyer', 'buyer_td' ); load_drop_down( 'requires/quotation_entry_simple_controller', '".$row[csf("company_id")]."', 'load_drop_down_agent', 'agent_td' );cm_cost_predefined_method('".$row[csf("company_id")]."') ;\n";
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
		echo "document.getElementById('txt_season').value = '".$row[csf("season")]."';\n";
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
			$field_array="id, company_id, buyer_id, style_ref, revised_no, pord_dept,product_code, style_desc, currency, agent, offer_qnty, region, color_range, incoterm, incoterm_place, machine_line, prod_line_hr,  costing_per, quot_date, est_ship_date,op_date, factory, remarks, garments_nature,order_uom,gmts_item_id,set_break_down,total_set_qnty,cm_cost_predefined_method_id,exchange_rate,sew_smv,cut_smv,sew_effi_percent,cut_effi_percent,efficiency_wastage_percent,season, inserted_by, insert_date, status_active, is_deleted";
			$data_array="(".$id.",".$cbo_company_name.",".$cbo_buyer_name.",".$txt_style_ref.",".$txt_revised_no.",".$cbo_pord_dept.",".$txt_product_code.",".$txt_style_desc.",".$cbo_currercy.",".$cbo_agent.",".$txt_offer_qnty.",".$cbo_region.",".$cbo_color_range.",".$cbo_inco_term.",".$txt_incoterm_place.",".$txt_machine_line.",".$txt_prod_line_hr.",".$cbo_costing_per.",".$txt_quotation_date.",".$txt_est_ship_date.",".$txt_op_date.",".$txt_factory.",".$txt_remarks.",".$garments_nature.",".$cbo_order_uom.",".$item_id.",".$set_breck_down.",".$tot_set_qnty.",".$cm_cost_predefined_method_id.",".$txt_exchange_rate.",".$txt_sew_smv.",".$txt_cut_smv.",".$txt_sew_efficiency_per.",".$txt_cut_efficiency_per.",".$txt_efficiency_wastage.",".$txt_season.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
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
			 	
			$field_array="company_id*buyer_id*style_ref*revised_no*pord_dept*product_code*style_desc*currency*agent*offer_qnty*region*color_range*incoterm*incoterm_place*machine_line* prod_line_hr*costing_per*quot_date*est_ship_date*op_date*factory*remarks*garments_nature*order_uom*gmts_item_id*set_break_down*total_set_qnty*cm_cost_predefined_method_id*exchange_rate*sew_smv*cut_smv*sew_effi_percent*cut_effi_percent*efficiency_wastage_percent*season*updated_by*update_date*status_active* is_deleted";
			$data_array="".$cbo_company_name."*".$cbo_buyer_name."*".$txt_style_ref."*".$txt_revised_no."*".$cbo_pord_dept."*".$txt_product_code."*".$txt_style_desc."*".$cbo_currercy."*".$cbo_agent."*".$txt_offer_qnty."*".$cbo_region."*".$cbo_color_range."*".$cbo_inco_term."*".$txt_incoterm_place."*".$txt_machine_line."*".$txt_prod_line_hr."*".$cbo_costing_per."*".$txt_quotation_date."*".$txt_est_ship_date."*".$txt_op_date."*".$txt_factory."*".$txt_remarks."*".$garments_nature."*".$cbo_order_uom."*".$item_id."*".$set_breck_down."*".$tot_set_qnty."*".$cm_cost_predefined_method_id."*".$txt_exchange_rate."*".$txt_sew_smv."*".$txt_cut_smv."*".$txt_sew_efficiency_per."*".$txt_cut_efficiency_per."*".$txt_efficiency_wastage."*".$txt_season."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'1'*'0'";
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
			if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
			$data=explode("__",$tot_row_val);
	        $head=explode("_",$data[0]);
			$id=return_next_id( "id", "wo_pri_sim_dtls_fyd", 1 ) ;
			//$mst_id=1;
			$field_array1="id,mst_id,row_no,col_no,fab_des,fab_type,qty";

			for($i=1;$i<count($data); $i++)
			{
				$data_value=explode("_",$data[$i]);
				for($j=1;$j<count($head); $j++)
				{
				//$data_array[$data_value[0]][$head[$j]]=$data_value[$j];
				$data_array1 .="(".$id.",".$mst_id.",".$i.",".$j.",'".$data_value[0]."','".$head[$j]."','".$data_value[$j]."'),";
				$id++;
			    }
			}
			$data_array1=rtrim($data_array1,",");
			//$rID=execute_query( "delete from wo_pri_sim_dtls_fyd where  mst_id =".$mst_id."",1);
			//$rID1=sql_insert("wo_pri_sim_dtls_fyd",$field_array1,$data_array1,1);
			//==================================================================
			
			$tot_row_val2=explode("__",$tot_row_val2);
	       // $head=explode("_",$data[0]);
			$id=return_next_id( "id", "wo_pri_sim_dtls_fcd", 1 ) ;
			//$mst_id=1;
			$field_array2="id,mst_id,row_no,col_no,fab_des,fab_type,qty";

			for($i=0;$i<count($tot_row_val2); $i++)
			{
				$row=$i+1;
				$data_value=explode("_",$tot_row_val2[$i]);
				for($j=1;$j<count($head); $j++)
				{
				//$data_array[$data_value[0]][$head[$j]]=$data_value[$j];
				$data_array2 .="(".$id.",".$mst_id.",".$row.",".$j.",'".$data_value[0]."','".$head[$j]."','".$data_value[$j]."'),";
				$id++;
			    }
			}
			$data_array2=rtrim($data_array2,",");
			//$rID2=execute_query( "delete from wo_pri_sim_dtls_fcd where  mst_id =".$mst_id."",1);
			//$rID3=sql_insert("wo_pri_sim_dtls_fcd",$field_array2,$data_array2,1);
			//================================================================================================
			$tot_row_val3=explode("__",$tot_row_val3);
	       // $head=explode("_",$data[0]);
			$id=return_next_id( "id", "wo_pri_sim_dtls_tfcd", 1 ) ;
			//$mst_id=1;
			$field_array3="id,mst_id,row_no,col_no,fab_des,fab_type,qty";

			for($i=0;$i<count($tot_row_val3); $i++)
			{
				$row=$i+1;
				$data_value=explode("_",$tot_row_val3[$i]);
				for($j=1;$j<count($head); $j++)
				{
				//$data_array[$data_value[0]][$head[$j]]=$data_value[$j];
				$data_array3 .="(".$id.",".$mst_id.",".$row.",".$j.",'".$data_value[0]."','".$head[$j]."','".$data_value[$j]."'),";
				$id++;
			    }
			}
			$data_array3=rtrim($data_array3,",");
			//$rID4=execute_query( "delete from wo_pri_sim_dtls_tfcd where  mst_id =".$mst_id."",1);
			//$rID5=sql_insert("wo_pri_sim_dtls_tfcd",$field_array3,$data_array3,1);
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
			//$rID6=execute_query( "delete from wo_pri_sim_fab where  mst_id =".$mst_id."",1);
			//$rID7=sql_insert("wo_pri_sim_fab",$field_array4,$data_array4,1);
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
			//$rID8=execute_query( "delete from wo_pri_sim_mes where  mst_id =".$mst_id."",1);
			//$rID9=sql_insert("wo_pri_sim_mes",$field_array5,$data_array5,1);
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
			//$rID10=execute_query( "delete from wo_pri_sim_mar where  mst_id =".$mst_id."",1);
			//$rID11=sql_insert("wo_pri_sim_mar",$field_array6,$data_array6,1);
			//================================================================================================
			
			$valuecm=explode("_",$valuecm);
	       // $head=explode("_",$data[0]);
			$id=return_next_id( "id", "wo_pri_sim_cm", 1 ) ;
			//$mst_id=1;
			$field_array7="id,mst_id,col_no,qty";

			for($i=0;$i<count($valuecm); $i++)
			{
				$col=$i+1;
				$data_array7 .="(".$id.",".$mst_id.",'".$col."','".$valuecm[$i]."'),";
				$id++;
			}
			$data_array7=rtrim($data_array7,",");
			//$rID12=execute_query( "delete from wo_pri_sim_cm where  mst_id =".$mst_id."",1);
			//$rID13=sql_insert("wo_pri_sim_cm",$field_array7,$data_array7,1);
			//================================================================================================
			$valueTP=explode("_",$valueTP);
	       // $head=explode("_",$data[0]);
			$id=return_next_id( "id", "wo_pri_sim_tp", 1 ) ;
			//$mst_id=1;
			$field_array8="id,mst_id,col_no,qty";

			for($i=0;$i<count($valueTP); $i++)
			{
				$col=$i+1;
				$data_array8 .="(".$id.",".$mst_id.",'".$col."','".$valueTP[$i]."'),";
				$id++;
			}
			$data_array8=rtrim($data_array8,",");
			//$rID14=execute_query( "delete from wo_pri_sim_tp where  mst_id =".$mst_id."",1);
			//$rID15=sql_insert("wo_pri_sim_tp",$field_array8,$data_array8,1);
			//================================================================================================
			$rID=execute_query( "delete from wo_pri_sim_dtls_fyd where  mst_id =".$mst_id."",1);
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
			$rID16=execute_query( "update wo_pri_sim_mst set fper='$percent_data_string[0]', yper='$percent_data_string[1]', cper='$percent_data_string[2]' where id =".$mst_id."",1);
			//================================================================================================
			check_table_status( $_SESSION['menu_id'],0);
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
	$header_array=array(1=>"B",2=>"C",3=>"D",4=>"E",5=>"F",6=>"G",7=>"H",8=>"I",9=>"J",10=>"K",11=>"L",12=>"M",13=>"N",14=>"O",15=>"P",16=>"Q",17=>"R",18=>"S",19=>"T",20=>"U",21=>"V",22=>"W",23=>"X",24=>"Y",25=>"Z");
	$row_library=array();
	$col_library=array();
	$value_library=array();
	$sql_data=sql_select("select row_no,col_no,fab_des,fab_type,qty from wo_pri_sim_dtls_fyd where mst_id=$mst_id");
	foreach($sql_data as $sql_row)
	{
		$row_library[$sql_row[csf('row_no')]]=$sql_row[csf('fab_des')];
		$col_library[$sql_row[csf('col_no')]]=$sql_row[csf('fab_type')];
		$value_library[$sql_row[csf('row_no')]][$sql_row[csf('col_no')]]=$sql_row[csf('qty')];
	}
	$library_data_sum=return_library_array("select col_no,sum(qty) as qty from wo_pri_sim_dtls_fyd where mst_id=$mst_id group by col_no","col_no", "qty");
	
	$sql_data_percent=sql_select("select fper,yper,cper from wo_pri_sim_mst where id=$mst_id");
	$FabricConsPercent=0;
	$YarnConsPercent=0;
	$Commision_percent=0;
    foreach($sql_data_percent as $sql_data_percent_row)
	{
		$FabricConsPercent=	$sql_data_percent_row[csf('fper')];
		$YarnConsPercent=$sql_data_percent_row[csf('yper')];
		$Commision_percent=$sql_data_percent_row[csf('cper')];
	}

	$num_row=count($row_library);
	$num_col=count($col_library)+$extra_col;
	
	if($num_row==0)
	{
		$num_row=2;
	}
	if($num_col==0)
	{
		$num_col=3;
	}
	$td_with=100;
	$table_width=($num_col*$td_with)+200;
	?>
    <strong>FABRIC & YARN DETAILS</strong>
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
    <th style="width:<? echo $td_with; ?>px;" id="<? echo "th_".$col; ?>" onClick="delete_column(this)">
    <? echo $header_array[$col];?>
    </th>
    <?
	}
    ?>
    </tr>
    </thead>
    <tbody>
    <?
	for($row=0;$row <= $num_row; $row++)
	{
		if($row==0)
		{
			$value="Particulars";
			$class="text_boxes";
		}
		else
		{
			$value=$row_library[$row];
			$class="text_boxes_numeric";
		}
		$row_id=$row+1;
	?>
    <tr class="mythingy">
    <td style="width:200px;">
    <input class="text_boxes"  style="width:200px;"  id="A_<? echo $row_id?>" onClick="id_detection(this.id)" value="<? echo $value;  ?>"/> 
    </td>
     <?
	for($col=1;$col <= $num_col; $col++)
	{
		if($row==0)
		{
			$value=$col_library[$col];
			//$class="text_boxes";
		}
		else
		{
			$value=def_number_format($value_library[$row][$col],2,"");
			//$class="text_boxes_numeric";
		}
	?>
    <td style="width:<? echo $td_with; ?>px;">
    <input class="<? echo $class; ?>"  style="width:<? echo $td_with; ?>px;"  id="<? echo $header_array[$col]."_".$row_id; ?>" onChange="sum_value(this.id,'table_1')" onClick="id_detection(this.id)" value="<? echo $value; ?>"/> 
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
     <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "sum1_".$header_array[$col];?>" value="<? echo def_number_format($library_data_sum[$col],2,""); ?>" /> 
    </th>
    <?
	}
    ?>
    </tr>
    <tr>
    <th style="width:200px;">
     Fabric Cons <input class="text_boxes"  style="width:30px;" id="Fper_1" onChange="claculate_percent(1)" value="<? echo $FabricConsPercent; ?>" />
    </th>
     <?
	for($col=1;$col <= $num_col; $col++)
	{
	$FabricCons=(($FabricConsPercent*$library_data_sum[$col])/100)+$library_data_sum[$col];
	$FabricCons=def_number_format($FabricCons,2,"");
	?>
    <th style="width:<? echo $td_with; ?>px;">
     <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;" id="<? echo "sum2_".$header_array[$col];?>" value="<? echo $FabricCons; ?>" /> 
    </th>
    <?
	}
    ?>
    </tr>
    <tr>
    <th style="width:200px;">
    Yarn Cons <input class="text_boxes"  style="width:30px;" id="Yper_1" onChange="claculate_percent(2)" value="<? echo $YarnConsPercent;  ?>"  />
    </th>
     <?
	$YarnCons_arr=array();
   
	for($col=1;$col <= $num_col; $col++)
	{
	$FabricCons=(($FabricConsPercent*$library_data_sum[$col])/100)+$library_data_sum[$col];
	$FabricCons=def_number_format($FabricCons,2,"");
	$YarnCons=(($YarnConsPercent*$FabricCons)/100)+$FabricCons;
	$YarnCons=def_number_format($YarnCons,2,"");
	$YarnCons_arr[$col]=$YarnCons;
	?>
    <th style="width:<? echo $td_with; ?>px;">
     <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "sum3_".$header_array[$col];?>" value="<? echo $YarnCons;  ?>"/> 
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
	$row_library=array();
	$col_library=array();
	$value_library=array();
	
	$sql_data=sql_select("select row_no,col_no,fab_des,fab_type,qty from wo_pri_sim_dtls_fcd where mst_id=$mst_id");
	foreach($sql_data as $sql_row)
	{
		$row_library[$sql_row[csf('row_no')]]=$sql_row[csf('fab_des')];
		$col_library[$sql_row[csf('col_no')]]=$sql_row[csf('fab_type')];
		$value_library[$sql_row[csf('row_no')]][$sql_row[csf('col_no')]]=$sql_row[csf('qty')];
	}
	$library_data_sum=return_library_array("select col_no,sum(qty) as qty from wo_pri_sim_dtls_fcd where mst_id=$mst_id and row_no !=1 group by col_no","col_no", "qty");

	$num_row=count($row_library);
	$num_col=count($col_library)+$extra_col;
	if($num_row==0)
	{
		$num_row=3;
	}
	if($num_col==0)
	{
		$num_col=3;
	}
	
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
    <input class="text_boxes"  style="width:200px;"  id="AY_<? echo $row?>" onClick="id_detection(this.id)" value="<? echo $row_library[$row];  ?>"/> 
    </td>
     <?
	for($col=1;$col <= $num_col; $col++)
	{
		if($row==1)
		{
			$value=$value_library[$row][$col];
		}
		else
		{
			$value=def_number_format($value_library[$row][$col],2,"");
		}
	?>
    <td style="width:<? echo $td_with; ?>px;">
    <input class="<? echo $class; ?>"  style="width:<? echo $td_with; ?>px;"  id="<? echo $header_array[$col]."Y_".$row; ?>" onChange="sum_value(this.id,'table_2')" onClick="id_detection(this.id)" value="<? echo $value;?>"/> 
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
     <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "sum4_".$header_array[$col]."Y";?>"  value="<? echo def_number_format($library_data_sum[$col],2,""); ?>"/> 
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
	$FabricCostDzn_arr=array();
	for($col=1;$col <= $num_col; $col++)
	{
		$FabricCostDzn=$YarnCons_arr[$col]*$library_data_sum[$col];
		$FabricCostDzn=def_number_format($FabricCostDzn,2,"");
		$FabricCostDzn_arr[$col]=$FabricCostDzn;
	?>
    <th style="width:<? echo $td_with; ?>px;">
     <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;" id="<? echo "sum5_".$header_array[$col]."Y";?>" value="<? echo $FabricCostDzn;  ?>" /> 
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
    $library_data_cm=return_library_array("select col_no,qty  from wo_pri_sim_cm where mst_id=$mst_id","col_no", "qty");

	for($col=1;$col <= $num_col; $col++)
	{
	?>
    <th style="width:<? echo $td_with; ?>px;">
     <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;" id="<? echo "CM_".$header_array[$col]."Y";?>" onChange="total_garments_cost_dzn()" value="<? echo def_number_format($library_data_cm[$col],2,"");  ?>" /> 
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
	$row_library=array();
	$col_library=array();
	$value_library=array();
	
	$sql_data=sql_select("select row_no,col_no,fab_des,fab_type,qty from wo_pri_sim_dtls_tfcd where mst_id=$mst_id");
	foreach($sql_data as $sql_row)
	{
		$row_library[$sql_row[csf('row_no')]]=$sql_row[csf('fab_des')];
		$col_library[$sql_row[csf('col_no')]]=$sql_row[csf('fab_type')];
		$value_library[$sql_row[csf('row_no')]][$sql_row[csf('col_no')]]=$sql_row[csf('qty')];
	}
	$library_data_sum=return_library_array("select col_no,sum(qty) as qty from wo_pri_sim_dtls_tfcd where mst_id=$mst_id group by col_no","col_no", "qty");
	$num_row=count($row_library);
	$num_col=count($col_library)+$extra_col;
	if($num_row==0)
	{
		$num_row=3;
	}
	if($num_col==0)
	{
		$num_col=3;
	}
	
	for($row=1;$row <= $num_row; $row++)
	{
	?>
    <tr class="mythingy">
    <td style="width:200px;">
    <input class="text_boxes"  style="width:200px;"  id="AT_<? echo $row?>" onClick="id_detection(this.id)" value="<? echo $row_library[$row]; ?>"/> 
    </td>
     <?
	for($col=1;$col <= $num_col; $col++)
	{
	?>
    <td style="width:<? echo $td_with; ?>px;">
    <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo $header_array[$col]."T_".$row; ?>" onChange="sum_value(this.id,'table_3')" onClick="id_detection(this.id)" value="<? echo def_number_format($value_library[$row][$col],2,"");  ?>"/> 
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
     <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "sum6_".$header_array[$col]."T";?>"  value="<? echo def_number_format($library_data_sum[$col],2,"") ;?>"/> 
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
	 $TotalGarmentsCostDzn_arr=array();
	for($col=1;$col <= $num_col; $col++)
	{
	$TotalGarmentsCostDzn=$FabricCostDzn_arr[$col]+$library_data_cm[$col]+$library_data_sum[$col];
	$TotalGarmentsCostDzn=def_number_format($TotalGarmentsCostDzn,2,"");
	$TotalGarmentsCostDzn_arr[$col]=$TotalGarmentsCostDzn;
	?>
    <th style="width:<? echo $td_with; ?>px;">
     <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "sum7_".$header_array[$col]."T";?>" value="<? echo $TotalGarmentsCostDzn; ?>" /> 
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
		$TotalGarmentsCostPcs=$TotalGarmentsCostDzn_arr[$col]/12;
		$TotalGarmentsCostPcs=def_number_format($TotalGarmentsCostPcs,2,"");
	?>
    <th style="width:<? echo $td_with; ?>px;">
     <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;" id="<? echo "sum8_".$header_array[$col]."T";?>" value="<? echo $TotalGarmentsCostPcs;?>" /> 
    </th>
    <?
	}
    ?>
    </tr>
    <tr>
    <th style="width:200px;">
    Commision <input class="text_boxes_numeric"  style="width:30px;" id="Cper_1" onChange="claculate_percent(3)" value="<? echo $Commision_percent;  ?>"  />
    </th>
     <?
	for($col=1;$col <= $num_col; $col++)
	{
		$Commision=((($TotalGarmentsCostDzn_arr[$col]*$Commision_percent)/100)+$TotalGarmentsCostDzn_arr[$col])/12;
		$Commision=def_number_format($Commision,2,"");
	?>
    <th style="width:<? echo $td_with; ?>px;">
     <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "sum9_".$header_array[$col]."T";?>" value=" <? echo $Commision; ?>"/> 
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
	$library_data_tp=return_library_array("select col_no,qty  from wo_pri_sim_tp where mst_id=$mst_id","col_no", "qty");

	for($col=1;$col <= $num_col; $col++)
	{
	?>
    <th style="width:<? echo $td_with; ?>px;">
     <input class="text_boxes_numeric"  style="width:<? echo $td_with; ?>px;"  id="<? echo "sum10_".$header_array[$col]."T";?>" value="<? echo def_number_format($library_data_tp[$col],2,""); ?>"/> 
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
    <?
	$i=1;
	$sql_fabrication=Sql_select("select fab_des,fab_val from wo_pri_sim_fab where mst_id=$mst_id");
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
	 $sql = "select a.id,a.company_id ,a.buyer_id,a.style_ref,a.style_desc,a.season,a.gmts_item_id ,a.order_uom ,a.offer_qnty,a.	est_ship_date,a.op_date,DATEDIFF(est_ship_date,op_date) as date_diff,a.costing_per from wo_pri_sim_mst a where a.id=$mst_id and a.status_active=1  order by a.id";
	}
	if($db_type==2)
	{
	 $sql = "select a.id,a.company_id ,a.buyer_id,a.style_ref,a.style_desc,a.season,a.gmts_item_id ,a.order_uom ,a.offer_qnty,a.	est_ship_date,a.op_date,(est_ship_date-op_date) as date_diff,a.costing_per from wo_pri_sim_mst a where a.id=$mst_id and a.status_active=1  order by a.id";
	}
	$data_array=sql_select($sql);
	
	
	?>
    <div style="width:850px; font-size:20px; font-weight:bold" align="center"><? //echo $comp[str_replace("'","",$cbo_company_name)]; ?></div>
    <div style="width:850px; font-size:14px; font-weight:bold" align="center">Quotation</div>
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
                        <td><b><? echo $row[csf("season")]; ?></b></td>
                        
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
	
	
	
	
	
	
	
	<?
	$header_array=array(1=>"B",2=>"C",3=>"D",4=>"E",5=>"F",6=>"G",7=>"H",8=>"I",9=>"J",10=>"K",11=>"L",12=>"M",13=>"N",14=>"O",15=>"P",16=>"Q",17=>"R",18=>"S",19=>"T",20=>"U",21=>"V",22=>"W",23=>"X",24=>"Y",25=>"Z");
	/*$row_library=return_library_array( "select row_no,fab_des from wo_pri_sim_dtls_fyd", "row_no", "fab_des"  );
	$col_library=return_library_array( "select col_no,fab_type from wo_pri_sim_dtls_fyd", "col_no", "fab_type"  );*/
	$row_library=array();
	$col_library=array();
	$value_library=array();
	
	$sql_data=sql_select("select row_no,col_no,fab_des,fab_type,qty from wo_pri_sim_dtls_fyd where mst_id=$mst_id");
	foreach($sql_data as $sql_row)
	{
		$row_library[$sql_row[csf('row_no')]]=$sql_row[csf('fab_des')];
		$col_library[$sql_row[csf('col_no')]]=$sql_row[csf('fab_type')];
		$value_library[$sql_row[csf('row_no')]][$sql_row[csf('col_no')]]=$sql_row[csf('qty')];
	}
	$library_data_sum=return_library_array("select col_no,sum(qty) as qty from wo_pri_sim_dtls_fyd where mst_id=$mst_id group by col_no","col_no", "qty");
	
	$sql_data_percent=sql_select("select fper,yper,cper from wo_pri_sim_mst where id=$mst_id");
	$FabricConsPercent=0;
	$YarnConsPercent=0;
	$Commision_percent=0;
    foreach($sql_data_percent as $sql_data_percent_row)
	{
		$FabricConsPercent=	$sql_data_percent_row[csf('fper')];
		$YarnConsPercent=$sql_data_percent_row[csf('yper')];
		$Commision_percent=$sql_data_percent_row[csf('cper')];
	}
	
	$num_row=count($row_library);
	$num_col=count($col_library);
	//$td_with=floor((1100-($num_col*15))/$num_col);
	$td_with=100;
	$table_width=($num_col*$td_with)+200;
	?>
    <table>
    <tr>
    <td valign="top">
   <strong> Fabrication,Measurment & Marker</strong>
                <div style="width:433px;">
                <table width="433" cellspacing="0" cellpadding=""  border="1" class="rpt_table" rules="all" id="">
                <thead>
                <tr>
                <th width="80">
                Sl
                </th>
                <th width="170" style="text-align:left">Fabrication</th>
                <th width="170">
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
                <td width="80">
                <? echo $i; ?>
                </td>
                <td width="170"><? echo $sql_fabrication_row[csf('fab_des')]; ?></td>
                <td width="170"><? echo $sql_fabrication_row[csf('fab_val')]; ?></td>
               
                </tr>
               <?
               $i++;
                }
               ?>
                </tbody>
                </table>
                </div>
                <br/>
                <div style="width:433px;">
                <table width="433" cellspacing="0" cellpadding=""  border="1" class="rpt_table" rules="all" id="">
                <thead>
                <tr>
                <th width="80">
                Sl
                </th>
                <th width="170" style="text-align:left">Measurment</th>
                <th width="170">
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
                <td width="80">
                 <? echo $i; ?>
                </td>
                <td width="170"><? echo $sql_measurment_row[csf('mes_des')];  ?></td>
                <td width="170"><? echo $sql_measurment_row[csf('mes_val')];  ?></td>
                </tr>
                <?
                $i++;
                }
                ?>
                </tbody>
                </table>
                </div>
                <br/>
                <div style="overflow:hidden;">
                <table width="433" cellspacing="0" cellpadding=""  border="1" class="rpt_table" rules="all" id="">
                <thead>
                <tr>
                <th width="80">
                Sl
                </th>
                <th width="170" style="text-align:left">Marker</th>
                <th width="170">
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
                <td width="80">
                <? echo $i; ?>
                </td>
                <td width="170"><? echo $sql_marker_row[csf('mar_des')] ?></td>
                <td width="170"><? echo $sql_marker_row[csf('mar_val')] ?></td>
                </tr>
               <?
               $i++;
                }
               ?>
                </tbody>
                </table>
                </div>
   </td>
    <td valign="top"> 
            <strong>FABRIC & YARN DETAILS</strong>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<? echo $table_width; ?>px;text-align:center;" rules="all" id="">
            <thead>	
            <tr>
            <th style="width:200px;">
             A
            </th>
             <?
            for($col=1;$col <= $num_col; $col++)
            {
            ?>
            <th style="width:<? echo $td_with; ?>px;">
            <? echo $header_array[$col];?>
            </th>
            <?
            }
            ?>
            </tr>
            </thead>
            <tbody>
            <?
            for($row=0;$row <= $num_row; $row++)
            {
                if($row==0)
                {
                    $value="Particulars";
                    $align="left";
                }
                else
                {
                    $value=$row_library[$row];
                    $align="right";
                }
                $row_id=$row+1;
            ?>
            <tr class="mythingy">
            <td style="width:200px;" align="left">
            <? echo $value;  ?> 
            </td>
             <?
            for($col=1;$col <= $num_col; $col++)
            {
                if($row==0)
                {
                    $value=$col_library[$col];
                    //$class="text_boxes";
                }
                else
                {
                    $value=def_number_format($value_library[$row][$col],2,"");
                    //$class="text_boxes_numeric";
                }
            ?>
            <td style="width:<? echo $td_with; ?>px;" align="<? echo $align; ?>">
            <? if($value !=0){echo $value;} ?>
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
            <th style="width:200px;" align="left">
             Total fabric Cons
            </th>
             <?
            for($col=1;$col <= $num_col; $col++)
            {
            ?>
            <th style="width:<? echo $td_with; ?>px;" align="right">
            <? echo def_number_format($library_data_sum[$col],2,""); ?>    
            </th>
            <?
            }
            ?>
            </tr>
            <tr>
            <th style="width:200px;" align="left">
             Fabric Cons &nbsp;&nbsp;&nbsp;&nbsp; <? echo $FabricConsPercent  ?>%
            </th>
             <?
            for($col=1;$col <= $num_col; $col++)
            {
            $FabricCons=(($FabricConsPercent*$library_data_sum[$col])/100)+$library_data_sum[$col];
            $FabricCons=def_number_format($FabricCons,2,"");
            ?>
            <th style="width:<? echo $td_with; ?>px;" align="right">
            <? echo $FabricCons; ?>    
            </th>
            <?
            }
            ?>
            </tr>
            <tr>
            <th style="width:200px;" align="left">
            Yarn Cons  &nbsp;&nbsp;&nbsp;&nbsp; <? echo $YarnConsPercent  ?>%
            </th>
             <?
            $YarnCons_arr=array();
            for($col=1;$col <= $num_col; $col++)
            {
            $FabricCons=(($FabricConsPercent*$library_data_sum[$col])/100)+$library_data_sum[$col];
            $FabricCons=def_number_format($FabricCons,2,"");
            $YarnCons=(($YarnConsPercent*$FabricCons)/100)+$FabricCons;
            $YarnCons=def_number_format($YarnCons,2,"");
            $YarnCons_arr[$col]=$YarnCons;
            ?>
            <th style="width:<? echo $td_with; ?>px;" align="right">
            <? echo $YarnCons;  ?>
            </th>
            <?
            }
            ?>
            </tr>
            </tfoot>
            </table>
            
             <strong>Per Kg Fabric Cost Details</strong>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<? echo $table_width; ?>px;text-align:center;" rules="all" id="">
            <tbody>
            <?
            $row_library=array();
            $col_library=array();
            $value_library=array();
            
            $sql_data=sql_select("select row_no,col_no,fab_des,fab_type,qty from wo_pri_sim_dtls_fcd where mst_id=$mst_id");
            foreach($sql_data as $sql_row)
            {
                $row_library[$sql_row[csf('row_no')]]=$sql_row[csf('fab_des')];
                $col_library[$sql_row[csf('col_no')]]=$sql_row[csf('fab_type')];
                $value_library[$sql_row[csf('row_no')]][$sql_row[csf('col_no')]]=$sql_row[csf('qty')];
            }
            $library_data_sum=return_library_array("select col_no,sum(qty) as qty from wo_pri_sim_dtls_fcd where mst_id=$mst_id and row_no !=1 group by col_no","col_no", "qty");
            
            $num_row=count($row_library);
            $num_col=count($col_library);
            for($row=1;$row <= $num_row; $row++)
            {
            if($row==1)
            {
                $align="left";
            }
            else
            {
                $align="right";
            }
            ?>
            <tr class="mythingy">
            <td style="width:200px;" align="left">
            <? echo $row_library[$row];  ?>
            </td>
             <?
            for($col=1;$col <= $num_col; $col++)
            {
                if($row==1)
                {
                    $value=$value_library[$row][$col];
                }
                else
                {
                    $value=def_number_format($value_library[$row][$col],2,"");
                }
            ?>
            <td style="width:<? echo $td_with; ?>px;" align="<? echo $align;  ?>">
            <? if($value !=0){echo $value;}?>
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
            <th style="width:200px;" align="left">
             Fabric Price/KG
            </th>
             <?
            for($col=1;$col <= $num_col; $col++)
            {
                
            ?>
            <th style="width:<? echo $td_with; ?>px;" align="right">
             <? echo def_number_format($library_data_sum[$col],2,""); ?>
            </th>
            <?
            }
            ?>
            </tr>
            <tr>
            <th style="width:200px;" align="left">
             Total Fabric Cost/Dzn
            </th>
             <?
            $FabricCostDzn_arr=array();
            for($col=1;$col <= $num_col; $col++)
            {
                $FabricCostDzn=$YarnCons_arr[$col]*$library_data_sum[$col];
                $FabricCostDzn=def_number_format($FabricCostDzn,2,"");
                $FabricCostDzn_arr[$col]=$FabricCostDzn;
            ?>
            <th style="width:<? echo $td_with; ?>px;" align="right">
            <? echo $FabricCostDzn;  ?>    
            </th>
            <?
            }
            ?>
            </tr>
            <tr>
            <th style="width:200px;" align="left">
             CM Cost/Dzn
            </th>
            <?
            $library_data_cm=return_library_array("select col_no,qty  from wo_pri_sim_cm where mst_id=$mst_id","col_no", "qty");
            for($col=1;$col <= $num_col; $col++)
            {
            ?>
            <th style="width:<? echo $td_with; ?>px;" align="right">
            <? echo def_number_format($library_data_cm[$col],2,"");  ?>    
            </th>
            <?
            }
            ?>
            </tr>
            </tfoot>
            </table>
            <strong>Trims & Other Fabric Cost Details</strong>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:<? echo $table_width; ?>px;text-align:center;" rules="all" id="">
            <tbody>
            <?
            $row_library=array();
            $col_library=array();
            $value_library=array();
            
            $sql_data=sql_select("select row_no,col_no,fab_des,fab_type,qty from wo_pri_sim_dtls_tfcd where mst_id=$mst_id");
            foreach($sql_data as $sql_row)
            {
                $row_library[$sql_row[csf('row_no')]]=$sql_row[csf('fab_des')];
                $col_library[$sql_row[csf('col_no')]]=$sql_row[csf('fab_type')];
                $value_library[$sql_row[csf('row_no')]][$sql_row[csf('col_no')]]=$sql_row[csf('qty')];
            }
            $library_data_sum=return_library_array("select col_no,sum(qty) as qty from wo_pri_sim_dtls_tfcd where mst_id=$mst_id group by col_no","col_no", "qty");
            $num_row=count($row_library);
            $num_col=count($col_library);
            for($row=1;$row <= $num_row; $row++)
            {
            ?>
            <tr class="mythingy">
            <td style="width:200px;" align="left">
            <? echo $row_library[$row]; ?>
            </td>
             <?
            for($col=1;$col <= $num_col; $col++)
            {
            ?>
            <td style="width:<? echo $td_with; ?>px;" align="right">
            <? if($value_library[$row][$col] !=0) echo def_number_format($value_library[$row][$col],2,"");  ?>
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
            <th style="width:200px;" align="left">
             Total 
            </th>
             <?
            for($col=1;$col <= $num_col; $col++)
            {
            ?>
            <th style="width:<? echo $td_with; ?>px;" align="right">
             <? if($library_data_sum[$col] !=0){echo def_number_format($library_data_sum[$col],2,"") ;}?>
            </th>
            <?
            }
            ?>
            </tr>
            
            <tr>
            <th style="width:200px;" align="left">
             Total Garments Cost/Dzn
            </th>
             <?
             $TotalGarmentsCostDzn_arr=array();
            for($col=1;$col <= $num_col; $col++)
            {
            $TotalGarmentsCostDzn=$FabricCostDzn_arr[$col]+$library_data_cm[$col]+$library_data_sum[$col];
            $TotalGarmentsCostDzn=def_number_format($TotalGarmentsCostDzn,2,"");
            $TotalGarmentsCostDzn_arr[$col]=$TotalGarmentsCostDzn;
            ?>
            <th style="width:<? echo $td_with; ?>px;" align="right">
             <? echo $TotalGarmentsCostDzn; ?>
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
            for($col=1;$col <= $num_col; $col++)
            {
                $TotalGarmentsCostPcs=$TotalGarmentsCostDzn_arr[$col]/12;
                $TotalGarmentsCostPcs=def_number_format($TotalGarmentsCostPcs,2,"");
            ?>
            <th style="width:<? echo $td_with; ?>px;" align="right">
             <? echo $TotalGarmentsCostPcs;?>
            </th>
            <?
            }
            ?>
            </tr>
            <tr>
            <th style="width:200px;" align="left">
            Commision &nbsp;&nbsp;&nbsp;&nbsp; <? echo $Commision_percent  ?>% 
            </th>
             <?
            for($col=1;$col <= $num_col; $col++)
            {
                $Commision=((($TotalGarmentsCostDzn_arr[$col]*$Commision_percent)/100)+$TotalGarmentsCostDzn_arr[$col])/12;
                $Commision=def_number_format($Commision,2,"");
            ?>
            <th style="width:<? echo $td_with; ?>px;" align="right">
             <? echo $Commision; ?>
            </th>
            <?
            }
            ?>
            </tr>
            <tr>
            <th style="width:200px;" align="left">
            Terget Price
            </th>
             <?
            $library_data_tp=return_library_array("select col_no,qty  from wo_pri_sim_tp where mst_id=$mst_id","col_no", "qty");
            
            for($col=1;$col <= $num_col; $col++)
            {
            ?>
            <th style="width:<? echo $td_with; ?>px;" align="right">
            <? echo def_number_format($library_data_tp[$col],2,""); ?>
            </th>
            <?
            }
            ?>
            </tr>
            </tfoot>
            </table>
   </td>
   </tr>
   </table>
<?
}//end master if condition-------------------------------------------------------
?>

﻿<?
/*--------------------------------------------Comments----------------
Version (MySql)          :  V2
Version (Oracle)         :  V1
Converted by             :  MONZU
Converted Date           :  24-05-2014
Purpose			         : 	This form will create Knit Garments Order Entry
Functionality	         :	
JS Functions	         :
Created by		         :	Monzu 
Creation date 	         : 	13-10-2012
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
Comments		         :From this version oracle conversion is start
----------------------------------------------------------------------*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

//************************************ Start*************************************************
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );

// Master Form*************************************Master Form*************************
if ($action=="cbo_factory_merchant")
{
	echo create_drop_down( "cbo_factory_merchant", 172, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
}

if ($action=="order_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);

?>
     
	<script>
	function set_checkvalue()
	{
		if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
		else document.getElementById('chk_job_wo_po').value=0;
	}
	
	function js_set_value( job_no )
	{
		document.getElementById('selected_job').value=job_no;
		parent.emailwindow.hide();
	}
	
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="1000" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                        <thead>
                         <th width="150" colspan="3"> </th>
                        	<th>
                              <?
                               echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" );
                              ?>
                            </th>
                          <th width="150" colspan="3"> </th>
                        </thead>
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="80">Job No</th>
                        <th width="100">Style Ref </th>
                        <th width="120">Order No</th>
                        <th width="200">Ship Date Range</th>
                        <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job Without PO</th>           
                    </thead>
        			<tr>
                    	<td> 
                        <input type="hidden" id="selected_job">
                        <input type="hidden" id="garments_nature" value="<? echo $garments_nature; ?>">
							<? 
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'woven_order_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
                    </td>
                   	<td id="buyer_td">
                     <? 
						echo create_drop_down( "cbo_buyer_name", 172, $blank_array,'', 1, "-- Select Buyer --" );
					?>	</td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:80px"></td>
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:120px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('garments_nature').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value, 'create_po_search_list_view', 'search_div', 'woven_order_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
            <td align="center" valign="top" id="search_div"> 
	
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


if($action=="create_po_search_list_view")
{
	//echo $data;die;
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	
	
	
	if($db_type==0)
	{
	$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[7]";
	if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	if($db_type==2)
	{
	$year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";
	if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	$order_cond="";
	$job_cond=""; 
	$style_cond="";
	if($data[8]==1)
		{
		  if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num='$data[6]'  $year_cond";
		  if (trim($data[9])!="") $order_cond=" and b.po_number='$data[9]'  "; //else  $order_cond=""; 
		  if (trim($data[10])!="") $style_cond=" and a.style_ref_no='$data[10]'  "; //else  $style_cond=""; 
		}
	
	if($data[8]==4 || $data[8]==0)
		{
		  if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]%'  $year_cond"; //else  $job_cond=""; 
		  if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]%'  ";
		  if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '%$data[10]%'  "; //else  $style_cond=""; 

		}
	
	if($data[8]==2)
		{
		  if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '$data[6]%'  $year_cond"; //else  $job_cond=""; 
		  if (trim($data[9])!="") $order_cond=" and b.po_number like '$data[9]%'  ";
		  if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '$data[10]%'  "; //else  $style_cond=""; 

		}
	
	if($data[8]==3)
		{
		  if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]'  $year_cond"; //else  $job_cond=""; 
		  if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]'  ";
		  if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '%$data[10]'  "; //else  $style_cond=""; 

		}
	
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (2=>$comp,3=>$buyer_arr,9=>$item_category);
	if ($data[2]==0)
	{
	if($db_type==0)
		{
	 	$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.shipment_date,a.garments_nature,SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer $job_cond $order_cond $style_cond order by a.job_no";
		}
	 if($db_type==2)
		{
	 	$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.shipment_date,a.garments_nature,to_char(a.insert_date,'YYYY') as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer $job_cond $order_cond $style_cond order by a.job_no";
		}
		//echo $sql;
		 echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date,Gmts Nature", "50,60,120,100,100,90,90,90,80,80","1000","320",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0,garments_nature", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date,garments_nature", "",'','0,0,0,0,0,1,0,1,3,0');
	}
	else
	{
		$arr=array (2=>$comp,3=>$buyer_arr,5=>$item_category);
		if($db_type==0)
		{
		$sql= "select a.job_no_prefix_num,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.garments_nature,SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a where a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.garments_nature=$data[5] and a.status_active=1   and a.is_deleted=0 $company $buyer order by a.job_no";
		}
		if($db_type==2)
		{
		$sql= "select a.job_no_prefix_num,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.garments_nature,to_char(a.insert_date,'YYYY') as year from wo_po_details_master a where a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.garments_nature=$data[5] and a.status_active=1   and a.is_deleted=0 $company $buyer order by a.job_no";
		}
		echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Gmts Nature", "90,60,120,100,100,90","1000","320",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_name,buyer_name,0,garments_nature", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,garments_nature", "",'','0,0,0,0,0,0');
	}
} 

if ($action=="populate_data_from_search_popup")
{
	$data_array=sql_select("select id, garments_nature, job_no, job_no_prefix, job_no_prefix_num, company_name, buyer_name, location_name, style_ref_no, style_description, product_dept, product_code, pro_sub_dep, currency_id, agent_name, client_id, order_repeat_no, region, product_category, team_leader, dealing_marchant, bh_merchant, packing, remarks, ship_mode, order_uom, set_break_down, gmts_item_id, total_set_qnty, set_smv, season_buyer_wise, quotation_id, job_quantity, order_uom, avg_unit_price, currency_id, total_price, factory_marchant from wo_po_details_master where job_no='$data'");
	foreach ($data_array as $row)
	{
		echo "load_drop_down( 'requires/woven_order_entry_controller_update', '".$row[csf("company_name")]."', 'load_drop_down_location', 'location' ); load_drop_down( 'requires/woven_order_entry_controller', '".$row[csf("company_name")]."', 'load_drop_down_buyer', 'buyer_td' ); load_drop_down( 'requires/woven_order_entry_controller_update', '".$row[csf("company_name")]."', 'load_drop_down_agent', 'agent_td' );load_drop_down( 'requires/woven_order_entry_controller_update', '".$row[csf("company_name")]."', 'load_drop_down_party_type', 'party_type_td' );sub_dept_load('".$row[csf("buyer_name")]."','".$row[csf("product_dept")]."');\n";
		echo "load_drop_down( 'requires/woven_order_entry_controller_update', '".$row[csf("team_leader")]."', 'cbo_dealing_merchant', 'div_marchant' ) ;\n";
		echo "load_drop_down( 'requires/woven_order_entry_controller_update', '".$row[csf("team_leader")]."', 'cbo_factory_merchant', 'div_marchant_factory' ) ;\n";
		
		echo "publish_shipment_date('".$row[csf("company_name")]."');\n";
		echo "document.getElementById('garments_nature').value = '".$row[csf("garments_nature")]."';\n";  
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";  
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";  
		echo "document.getElementById('cbo_location_name').value = '".$row[csf("location_name")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";  
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref_no")]."';\n";  
		echo "document.getElementById('txt_style_description').value = '".$row[csf("style_description")]."';\n";  
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("product_dept")]."';\n";
		echo "document.getElementById('txt_product_code').value = '".$row[csf("product_code")]."';\n";
		echo "document.getElementById('txt_bhmerchant').value = '".$row[csf("bh_merchant")]."';\n";
		echo "document.getElementById('cbo_sub_dept').value = '".$row[csf("pro_sub_dep")]."';\n";
		echo "document.getElementById('cbo_currercy').value = '".$row[csf("currency_id")]."';\n";  
		echo "document.getElementById('cbo_agent').value = '".$row[csf("agent_name")]."';\n"; 
		echo "document.getElementById('cbo_client').value = '".$row[csf("client_id")]."';\n"; 
		
		 
		echo "document.getElementById('txt_repeat_no').value = '".$row[csf("order_repeat_no")]."';\n";  
		echo "document.getElementById('cbo_region').value = '".$row[csf("region")]."';\n";  
		echo "document.getElementById('txt_item_catgory').value = '".$row[csf("product_category")]."';\n";  
		echo "document.getElementById('cbo_team_leader').value = '".$row[csf("team_leader")]."';\n";  
		echo "document.getElementById('cbo_dealing_merchant').value = '".$row[csf("dealing_marchant")]."';\n";  
		echo "document.getElementById('cbo_packing').value = '".$row[csf("packing")]."';\n";  
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";  
		echo "document.getElementById('cbo_ship_mode').value = '".$row[csf("ship_mode")]."';\n";
		echo "document.getElementById('cbo_order_uom').value = '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('set_breck_down').value = '".$row[csf("set_break_down")]."';\n";
		echo "document.getElementById('item_id').value = '".$row[csf("gmts_item_id")]."';\n";
		echo "document.getElementById('tot_set_qnty').value = '".$row[csf("total_set_qnty")]."';\n";
		echo "document.getElementById('tot_smv_qnty').value = '".$row[csf("set_smv")]."';\n";
		echo "document.getElementById('txt_quotation_id').value = '".$row[csf("quotation_id")]."';\n";
		echo "document.getElementById('txt_total_job_quantity').value = '".$row[csf("job_quantity")]."';\n";
		//echo "document.getElementById('set_pcs').value = '".$unit_of_measurement[$row[csf("order_uom")]]."';\n";
		echo "document.getElementById('set_pcs').value = '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('pojected_set_pcs').value = '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('txt_avg_unit_price').value = '".$row[csf("avg_unit_price")]."';\n";
		//echo "document.getElementById('set_unit').value = '".$currency[$row[csf("currency_id")]]."';\n";
		echo "document.getElementById('cbo_factory_merchant').value = '".$row[csf("factory_marchant")]."';\n";
		echo "document.getElementById('set_unit').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('projected_set_unit').value = '".$row[csf("currency_id")]."';\n";  
		echo "document.getElementById('txt_job_total_price').value = '".$row[csf("total_price")]."';\n";
		
		 if($_SESSION['logic_erp']['data_arr'][122][$cbo_company_name][txt_excess_cut][is_disable]==1)
		 {
			 echo "$('#txt_excess_cut').attr('disabled','true')".";\n";
		 }
		 else
		 {
			 echo "$('#txt_excess_cut').removeAttr('disabled')".";\n";
		 }
		
		echo "load_drop_down( 'requires/woven_order_entry_controller_update','".$row[csf("buyer_name")].'_'.$row[csf("company_name")]."', 'load_drop_down_season_buyer', 'season_td') ;";
		echo "document.getElementById('cbo_season_name').value = '".$row[csf("season_buyer_wise")]."';\n";
		//echo "document.getElementById('cbo_season_name').value = '".$row[csf("season_buyer_wise")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("job_no")]."';\n";  
	}
	
	$projected_data_array=sql_select("select sum(original_po_qty) as projected_qty,sum(original_po_qty*original_avg_price) as projected_amount  ,(sum(original_po_qty*original_avg_price)/sum(original_po_qty)) as projected_rate 
 from wo_po_break_down where job_no_mst='$data' and is_confirmed=2");
		foreach ($projected_data_array as $row_val)
	{
	    echo "document.getElementById('txt_projected_job_quantity').value = '".$row_val[csf("projected_qty")]."';\n";
		echo "document.getElementById('txt_projected_price').value = '".number_format($row_val[csf("projected_rate")],4)."';\n";
		echo "document.getElementById('txt_project_total_price').value = '".$row_val[csf("projected_amount")]."';\n";
	}
}
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 172, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select --", $selected, "" );		 
}
if ($action=="load_drop_down_season_buyer")
{
	$datas=explode('_',$data);
	echo create_drop_down( "cbo_season_name", 172, "select a.id,a.season_name from lib_buyer_season a,variable_order_tracking b where a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.buyer_id='$datas[0]' and b.company_name='$datas[1]' and b.season_mandatory=1 and b.variable_list=44","id,season_name", 1, "-- Select Season --", $selected, "" );
}
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/woven_order_entry_controller_update', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_season_buyer', 'season_td');sub_dept_load(this.value,document.getElementById('cbo_product_department').value)" );   	 
} 

if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent", 172, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21))  order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" );  
	 	 
} 

if ($action=="load_drop_down_party_type")
{
	echo create_drop_down( "cbo_client", 172, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (7))  order by buyer_name","id,buyer_name", 1, "-- Select Client --", $selected, "" );  
	 	 
}

if ($action=="cbo_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 172, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
}

if ($action=="load_drop_down_sub_dep")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_sub_dept", 172, "select id,sub_department_name from lib_pro_sub_deparatment where buyer_id=$data[0] and	department_id='$data[1]' and status_active =1 and is_deleted=0 order by sub_department_name","id,sub_department_name", 1, "-- Select Sub Dep --", $selected, "" );
}

if ($action=="load_drop_down_projected_po")
{
	echo create_drop_down( "cbo_projected_po", 100, "select id,po_number from  wo_po_break_down where job_no_mst='$data'   and is_confirmed=2 and status_active =1 and is_deleted=0 order by po_number","id,po_number", 1, "-- Select --", "", "" );
}

if ($action=="load_drop_down_tna_task")
{
	$sql_task = "SELECT a.id,task_template_id,lead_time,material_source,total_task,tna_task_id,deadline,execution_days,notice_before,a.sequence_no,for_specific,b.task_catagory,b.task_name FROM  tna_task_template_details a, lib_tna_task b WHERE  a.is_deleted = 0 and a.status_active=1 and a.tna_task_id=b.id order by for_specific,lead_time";
	$result = sql_select( $sql_task ) ;
	$tna_template = array();
	$i=0;
	$k=0;
	$j=0;
	foreach( $result as $row ) 
	{
		if (!in_array($row[csf("task_template_id")],$template))
		{
			$template[]=$row[csf("task_template_id")];
			if ( $row[csf("for_specific")]==0 )
			{
				$tna_template[$i]['lead']=$row[csf('lead_time')];
				$tna_template[$i]['id']=$row[csf('task_template_id')];
				$i++;
			}
			else
			{
				if(!in_array($row[csf('for_specific')],$tna_template_spc)) { $j=0; $tna_template_spc[]=$row[csf("for_specific")]; }
				$tna_template_buyer[$row[csf('for_specific')]][$j]['lead']=$row[csf('lead_time')];
				$tna_template_buyer[$row[csf('for_specific')]][$j]['id']=$row[csf('task_template_id')];
				$j++;
			}
			$k++;
		}
        }
	$data=explode("_",$data);
	$remain_days=datediff( "d", $data[0], $data[1] );
	$template_id=get_tna_template($remain_days,$tna_template,$data[2]);
	//echo $template_id; 
	echo create_drop_down( "cbo_tna_task", 90, "select a.id, concat(a.sequence_no,'-',b.task_short_name) as task_short_name ,a.tna_task_id from  tna_task_template_details a,lib_tna_task b where a.tna_task_id=b.id and task_template_id='$template_id'  and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 order by a.sequence_no","id,task_short_name", 1, "-- Select --", "", "" );
}

if($action=="publish_shipment_date")
{
	$publish_shipment_date=return_field_value("publish_shipment_date", "variable_order_tracking", "company_name=$data  and variable_list=25  and status_active=1 and is_deleted=0");
	if($publish_shipment_date !="")
	{
	  echo trim($publish_shipment_date);	
	}
	else
	{
		echo 1;
	}
	
	die;

}

if ($action=="show_po_active_listview")
{
	 
	$arr=array (0=>$order_status,11=>$row_status);
	
	if($db_type==0)
	{
 	 $sql= "select is_confirmed,po_number,po_received_date,pub_shipment_date,shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,DATEDIFF(pub_shipment_date,po_received_date) as  date_diff,status_active,id from  wo_po_break_down  where   status_active=1 and is_deleted=0 and job_no_mst='$data'"; 
	}
	
	if($db_type==2)
	{
 	 $sql= "select is_confirmed,po_number,po_received_date,pub_shipment_date,shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,(pub_shipment_date-po_received_date) as  date_diff,status_active,id from  wo_po_break_down  where   status_active=1 and is_deleted=0 and job_no_mst='$data'"; 
	}
	 
	 
	echo  create_list_view("list_view", "Order Status,PO No,PO Recv. Date,Ship Date,Orgn. Ship Date,PO Qnty,Avg. Rate,Amount, Excess Cut %,Plan Cut Qnty,Lead Time,Status", "90,130,80,80,80,80,80,80,80,80,50","1050","220",0, $sql , "get_details_form_data", "id", "'populate_order_details_form_data'", 1, "is_confirmed,0,0,0,0,0,0,0,0,0,0,status_active", $arr , "is_confirmed,po_number,po_received_date,pub_shipment_date,shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,date_diff,status_active", "../woven_order/requires/woven_order_entry_controller_update",'','0,0,3,3,3,1,2,2,2,2,1') ;
	
}

if ($action=="show_deleted_po_active_listview")
{
	 
	  $arr=array (0=>$order_status,11=>$row_status);
	  if($db_type==0)
	  {
 	  $sql= "select is_confirmed,po_number,po_received_date,shipment_date,pub_shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,DATEDIFF(shipment_date,po_received_date) as date_diff,status_active,id from  wo_po_break_down  where   status_active !=1  and job_no_mst='$data'"; 
	  }
	  if($db_type==2)
	  {
 	  $sql= "select is_confirmed,po_number,po_received_date,shipment_date,pub_shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,(shipment_date-po_received_date) as  date_diff,status_active,id from  wo_po_break_down  where   status_active !=1  and job_no_mst='$data'"; 
	  }
	echo  create_list_view("list_view", "Order Status,PO No,PO Recv. Date,Ship Date,Orgn. Ship Date,PO Qnty,Avg. Rate,Amount, Excess Cut %,Plan Cut Qnty,Lead Time,Status", "90,130,80,80,80,80,80,80,80,80,50,70","1050","220",0, $sql , "get_details_form_data", "id", "'populate_order_details_form_data'", 1, "is_confirmed,0,0,0,0,0,0,0,0,0,0,status_active", $arr , "is_confirmed,po_number,po_received_date,shipment_date,pub_shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,date_diff,status_active", "../woven_order/requires/woven_order_entry_controller_update",'','0,0,3,3,3,1,2,2,2,2,1') ;
	
}

if ($action=="quotation_id_popup")
{
  	echo load_html_head_contents("Woven Order Entry","../../../", 1, 1, $unicode);
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
	<table width="900" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
                    
                    <thead> 
                         <tr>
                        	<th  colspan="6">
                              <?
                               echo create_drop_down( "cbo_string_search_type", 140, $string_search_type,'', 1, "-- Searching Type --" );
                              ?>
                            </th>
                     </tr>
                       <tr>               	 
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="100">Quotation ID</th>
                        <th width="100">Style Reff.</th>
                        <th width="200">Date Range</th>
                        <th></th> 
                       </tr>          
                    </thead>
        			<tr class="general">
                    	<td> <input type="hidden" id="selected_id">
							<? 
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down('woven_order_entry_controller_update', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
                        </td>
                   	<td id="buyer_td">
                     <? 
						echo create_drop_down( "cbo_buyer_name", 172, $blank_array,'', 1, "-- Select Buyer --" );
					?>	
                    </td>
                     <td >  
                        <input type="text" style="width:70px" class="text_boxes"  name="txt_quotation_no" id="txt_quotation_no"  />	
                    </td>
                     <td  align="center">				
                        <input type="text" style="width:100px" class="text_boxes"  name="txt_style" id="txt_style"  />			
                    </td>
                    <td>
                      <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_quotation_no').value+'_'+document.getElementById('txt_style').value, 'create_quotation_id_list_view', 'search_div', 'woven_order_entry_controller_update', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
	if ($data[1]!=0) $buyer=" and buyer_id='$data[1]'"; $buyer="";// else { echo "Please Select Buyer First."; die; }
	
	if($db_type==0)
	{
	if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and est_ship_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $est_ship_date ="";
	}
	
	if($db_type==2)
	{
	if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and est_ship_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $est_ship_date ="";
	}
	
	$style_cond="";
	$quotation_id_cond="";
	if($data[4]==1)
		{
		   if (trim($data[5])!="") $quotation_id_cond=" and id='$data[5]'";
		   if (trim($data[6])!="") $style_cond=" and style_ref='$data[6]'";
		}
	
	if($data[4]==4 || $data[4]==0)
		{
		  if (trim($data[5])!="") $quotation_id_cond=" and id like '%$data[5]%' ";
		  if (trim($data[6])!="") $style_cond=" and style_ref like '%$data[6]%' ";
		}
	
	if($data[4]==2)
		{
		  if (trim($data[5])!="") $quotation_id_cond=" and id like '$data[5]%' "; 
		  if (trim($data[6])!="") $style_cond=" and style_ref like '$data[6]%' ";
		}
	
	if($data[4]==3)
		{
		  if (trim($data[5])!="") $quotation_id_cond=" and id like '%$data[5]' ";
		  if (trim($data[6])!="") $style_cond=" and style_ref like '%$data[6]' "; 
		}
		
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (1=>$comp,2=>$buyer_arr,5=>$pord_dept);
	$sql= "select id,company_id, buyer_id, style_ref,style_desc,pord_dept,offer_qnty,est_ship_date from  wo_price_quotation a where status_active=1  and is_deleted=0 $company $buyer $style_cond $quotation_id_cond order by id";
	echo  create_list_view("list_view", "Quotation ID,Company,Buyer Name,Style Ref,Style Desc.,Prod. Dept., Offer Qnty, Est Ship Date", "90,120,100,100,200,100,100","1000","320",0, $sql , "js_set_value", "id", "", 1, "0,company_id,buyer_id,0,0,pord_dept,0,0", $arr , "id,company_id,buyer_id,style_ref,style_desc,pord_dept,offer_qnty,est_ship_date", "",'','0,0,0,0,0,0,2,3') ;
}

if ($action=="populate_data_from_search_popup_quotation")
{
	$data_array=sql_select("select a.id, a.company_id, a.buyer_id, a.style_ref, a.revised_no, a.pord_dept,a.product_code, a.style_desc, a.currency, a.agent, a.offer_qnty, a.region, a.color_range, a.incoterm, a.incoterm_place, a.machine_line, a.prod_line_hr, a.fabric_source, a.costing_per, a.quot_date, a.est_ship_date, a.factory,a.season_buyer_wise, a.remarks, a.garments_nature,a.order_uom,a.gmts_item_id,a.set_break_down,a.total_set_qnty,b.price_with_commn_pcs from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.id='$data'");
	foreach ($data_array as $row)
	{
		echo "load_drop_down( 'requires/woven_order_entry_controller_update', '".$row[csf("company_id")]."', 'load_drop_down_buyer', 'buyer_td' ); load_drop_down( 'requires/quotation_entry_controller', '".$row[csf("company_id")]."', 'load_drop_down_agent', 'agent_td' ); load_drop_down( 'requires/woven_order_entry_controller_update', '".$row[csf("company_id")]."', 'load_drop_down_party_type', 'party_type_td' );sub_dept_load('".$row[csf("buyer_id")]."','".$row[csf("pord_dept")]."');\n";
		
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";  
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref")]."';\n";
		echo "document.getElementById('txt_style_description').value = '".$row[csf("style_desc")]."';\n";
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("pord_dept")]."';\n"; 
		echo "document.getElementById('txt_product_code').value = '".$row[csf("product_code")]."';\n";
		//echo "document.getElementById('cbo_sub_dept').value = '".$row[csf("pro_sub_dep")]."';\n";  
		echo "document.getElementById('cbo_currercy').value = '".$row[csf("currency")]."';\n";  
		echo "document.getElementById('cbo_agent').value = '".$row[csf("agent")]."';\n";  
		echo "document.getElementById('cbo_region').value = '".$row[csf("region")]."';\n";  
		echo "document.getElementById('cbo_order_uom').value = '".$row[csf("order_uom")]."';\n";  
		echo "document.getElementById('item_id').value = '".$row[csf("gmts_item_id")]."';\n";  
		echo "document.getElementById('set_breck_down').value = '".$row[csf("set_break_down")]."';\n";  
		echo "document.getElementById('tot_set_qnty').value = '".$row[csf("total_set_qnty")]."';\n";  
		echo "document.getElementById('txt_quotation_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_avg_price').value = '".$row[csf("price_with_commn_pcs")]."';\n";
		echo "document.getElementById('cbo_season_name').value = '".$row[csf("season_buyer_wise")]."';\n";
		//echo "location_select();\n";
	}
}
if($action=="open_set_list_view")
{
echo load_html_head_contents("Set Entry","../../../", 1, 1, $unicode);
extract($_REQUEST);


 
?>
<script>

//else{$("#smv_1").removeAttr('disabled');}

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
		  $('#txtsetitemratio_'+i).removeAttr("onChange").attr("onChange","calculate_set_smv("+i+")");
		  $('#smv_'+i).removeAttr("onChange").attr("onChange","calculate_set_smv("+i+")");
		  $('#cutsmv_'+i).removeAttr("onChange").attr("onChange","calculate_set_cutsmv("+i+")");
		  $('#finsmv_'+i).removeAttr("onChange").attr("onChange","calculate_set_finsmv("+i+")");
		  $('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_set_tr("+i+")");
		  $('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_delete_down_tr("+i+",'tbl_set_details')");
		  $('#cboitem_'+i).val(''); 
		  set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
		  set_sum_value_set( 'tot_smv_qnty', 'smvset_' );
		  set_sum_value_set( 'tot_cutsmv_qnty', 'cutsmvset_' );
		  set_sum_value_set( 'tot_finsmv_qnty', 'finsmvset_' );
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
		 
		 set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
		 set_sum_value_set( 'tot_smv_qnty', 'smvset_' );
		 set_sum_value_set( 'tot_cutsmv_qnty', 'cutsmvset_' );
		 set_sum_value_set( 'tot_finsmv_qnty', 'finsmvset_' );
	}
}

function check_duplicate(id,td)
{
	var item_id=(document.getElementById('cboitem_'+id).value);
	var row_num=$('#tbl_set_details tr').length-1;
	for (var k=1;k<=row_num; k++)
	{
		if(k==id)
		{
			continue;
		}
		else
		{
			if(item_id==document.getElementById('cboitem_'+k).value)
			{
				alert("Same Gmts Item Duplication Not Allowed.");
				document.getElementById(td).value="0";
				document.getElementById(td).focus();
			}
		}
	}
}

function calculate_set_smv(i){
	var txtsetitemratio=document.getElementById('txtsetitemratio_'+i).value;
	var smv=document.getElementById('smv_'+i).value;
	var set_smv=txtsetitemratio*smv;
	document.getElementById('smvset_'+i).value=set_smv;
	
	set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
	set_sum_value_set( 'tot_smv_qnty', 'smvset_' );
	
	calculate_set_cutsmv(i);
	calculate_set_finsmv(i);
}

function calculate_set_cutsmv(i){
	var txtsetitemratio=document.getElementById('txtsetitemratio_'+i).value;
	var smv=document.getElementById('cutsmv_'+i).value;
	var set_smv=txtsetitemratio*smv;
	document.getElementById('cutsmvset_'+i).value=set_smv;
	set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
	set_sum_value_set( 'tot_cutsmv_qnty', 'cutsmvset_' );
}
function calculate_set_finsmv(i){
	var txtsetitemratio=document.getElementById('txtsetitemratio_'+i).value;
	var smv=document.getElementById('finsmv_'+i).value;
	var set_smv=txtsetitemratio*smv;
	document.getElementById('finsmvset_'+i).value=set_smv;
	set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
	set_sum_value_set( 'tot_finsmv_qnty', 'finsmvset_' );
}

function set_sum_value_set(des_fil_id,field_id)
{
	var rowCount = $('#tbl_set_details tr').length-1;
	if(des_fil_id=="tot_set_qnty")
	{
	math_operation( des_fil_id, field_id, '+', rowCount );
	}
	if(des_fil_id=="tot_smv_qnty")
	{
	var ddd={ dec_type:1, comma:0, currency:1}

	math_operation( des_fil_id, field_id, '+', rowCount,ddd );
	}
	if(des_fil_id=="tot_cutsmv_qnty")
	{
	var ddd={ dec_type:1, comma:0, currency:1}
	math_operation( des_fil_id, field_id, '+', rowCount,ddd );
	}
	if(des_fil_id=="tot_finsmv_qnty")
	{
	var ddd={ dec_type:1, comma:0, currency:1}
	math_operation( des_fil_id, field_id, '+', rowCount,ddd );
	}
}

function js_set_value_set()
{
	var rowCount = $('#tbl_set_details tr').length-1;
	var set_breck_down="";
	var item_id=""
	for(var i=1; i<=rowCount; i++)
	{
		if (form_validation('cboitem_'+i+'*txtsetitemratio_'+i+'*smv_'+i,'Gmts Items*Set Ratio')==false)
		{
			return;
		}
		var smv =document.getElementById('smv_'+i).value;
		if(smv==0)
		{
			alert("Smv 0 not accepted");
			return;
		}
		if($('#cutsmv_'+i).val()==''){
		$('#cutsmv_'+i).val(0)
		}
		if($('#cutsmvset_'+i).val()==''){
		$('#cutsmvset_'+i).val(0)
		}
		if($('#finsmv_'+i).val()==''){
		$('#finsmv_'+i).val(0)
		}
		if($('#finsmvset_'+i).val()==''){
		$('#finsmvset_'+i).val(0)
		}
		if(set_breck_down=="")
		{
			set_breck_down+=$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val()+'_'+$('#smv_'+i).val()+'_'+$('#smvset_'+i).val()+'_'+$('#complexity_'+i).val()+'_'+$('#emblish_'+i).val()+'_'+$('#cutsmv_'+i).val()+'_'+$('#cutsmvset_'+i).val()+'_'+$('#finsmv_'+i).val()+'_'+$('#finsmvset_'+i).val();
			item_id+=$('#cboitem_'+i).val();
		}
		else
		{
			set_breck_down+="__"+$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val()+'_'+$('#smv_'+i).val()+'_'+$('#smvset_'+i).val()+'_'+$('#complexity_'+i).val()+'_'+$('#emblish_'+i).val()+'_'+$('#cutsmv_'+i).val()+'_'+$('#cutsmvset_'+i).val()+'_'+$('#finsmv_'+i).val()+'_'+$('#finsmvset_'+i).val();
			item_id+=","+$('#cboitem_'+i).val();
		}
	}
	document.getElementById('set_breck_down').value=set_breck_down;
	document.getElementById('item_id').value=item_id;
	parent.emailwindow.hide();
}

function open_emblishment_pop_up(i){ 
	var page_link="woven_order_entry_controller.php?action=open_emblishment_list";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, pcs_or_set, 'width=620px,height=300px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var set_breck_down=this.contentDoc.getElementById("set_breck_down");
		var item_id=this.contentDoc.getElementById("item_id");
		var tot_set_qnty=this.contentDoc.getElementById("tot_set_qnty");
		var tot_smv_qnty=this.contentDoc.getElementById("tot_smv_qnty");
		document.getElementById('set_breck_down').value=set_breck_down.value;
		document.getElementById('item_id').value=item_id.value;
		document.getElementById('tot_set_qnty').value=tot_set_qnty.value;
		document.getElementById('tot_smv_qnty').value=tot_smv_qnty.value;
	}		
}
</script>
</head>
<body>
       <div id="set_details"  align="center">            
    	<fieldset>
         <?  
		 $disabled=0;
		 if($precostfound >0 ){ 
			 //echo "Pre Cost Found, Any Change will be not allowed";
			 $disabled=1;
		 }
		 else{
			 $disabled=0;
		 }
		 ?>
        	<form id="setdetails_1" autocomplete="off">
            <input type="hidden" id="set_breck_down" />     
            <input type="hidden" id="item_id" />  
            <input type="hidden" id="unit_id" value="<? echo $unit_id;  ?>" />        	
            <table width="640" cellspacing="0" class="rpt_table" border="0" id="tbl_set_details" rules="all">
                	<thead>
                    	<tr>
                        	<th width="230">Item</th><th width="40">Set Ratio</th><th width="40">Sew SMV/ Pcs</th><th width="40">Cut SMV/ Pcs</th><th width="40">Fin SMV/ Pcs</th><th width="80">Complexity</th><th width="80">Embellishment</th><th width=""></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$smv_arr=array();
					$sql_d=sql_select("Select gmts_item_id,set_item_ratio,smv_pcs,smv_set,complexity,embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set from wo_po_details_mas_set_details where job_no='$txt_job_no' order by id");
					foreach($sql_d as $sql_r){
						if($sql_r[csf('gmts_item_id')]==""){
							$sql_r[csf('gmts_item_id')]=0;
						}
						if($sql_r[csf('set_item_ratio')]==""){
							$sql_r[csf('set_item_ratio')]=0;
						}
						if($sql_r[csf('smv_pcs')]==""){
							$sql_r[csf('smv_pcs')]=0;
							$sql_r[csf('smv_set')]=0;
						}
						if($sql_r[csf('complexity')]==""){
							$sql_r[csf('complexity')]=0;
						}
						if($sql_r[csf('embelishment')]==""){
							$sql_r[csf('embelishment')]=0;
						}
						if($sql_r[csf('cutsmv_pcs')]==""){
							$sql_r[csf('cutsmv_pcs')]=0;
							$sql_r[csf('cutsmv_set')]=0;
						}
						if($sql_r[csf('finsmv_pcs')]==""){
							$sql_r[csf('finsmv_pcs')]=0;
							$sql_r[csf('finsmv_set')]=0;
						}
						$sql_r=removenumeric($sql_r);
						$smv_arr[]=implode("_",$sql_r);
					}
					$smv_srt=rtrim(implode("__",$smv_arr),"__");
					if(count($sql_d)){
						$set_breck_down=$smv_srt;
					}
					//echo $set_breck_down;
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
							$tot_cutsmv_qnty+=$data[6];
							$tot_finsmv_qnty+=$data[8];
							?>
                            	<tr id="settr_1" align="center">
                                    <td>
									<? 
										echo create_drop_down( "cboitem_".$i, 230, $garments_item, "",1," -- Select Item --", $data[0], "check_duplicate(".$i.",this.id )",$disabled,'' ); 
									?>
                                    </td>
                                    <td>
                                    <input type="text" id="txtsetitemratio_<? echo $i;?>"   name="txtsetitemratio_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric" onChange="calculate_set_smv(<? echo $i;?>)"  value="<? echo $data[1] ?>" <? if ($unit_id==1){echo "readonly";} else{echo "";}?> <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>  /> 
                                    </td>
                                    <td>
                                    <input type="text" id="smv_<? echo $i;?>"   name="smv_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric" onChange="calculate_set_smv(<? echo $i;?>)"  value="<? echo $data[2] ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?> /> 
                                    <input type="hidden" id="smvset_<? echo $i;?>"   name="smvset_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric"  value="<? echo $data[3] ?>" readonly/> 
                                    </td>
                                    <td>
                                    <input type="text" id="cutsmv_<? echo $i;?>"   name="cutsmv_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric" onChange="calculate_set_cutsmv(<? echo $i;?>)"  value="<? echo $data[6] ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?> /> 
                                    <input type="hidden" id="cutsmvset_<? echo $i;?>"   name="cutsmvset_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric"  value="<? echo $data[7] ?>" readonly/> 
                                    </td>
                                    <td>
                                    <input type="text" id="finsmv_<? echo $i;?>"   name="finsmv_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric" onChange="calculate_set_finsmv(<? echo $i;?>)"  value="<? echo $data[8] ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?> /> 
                                    <input type="hidden" id="finsmvset_<? echo $i;?>"   name="finsmvset_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric"  value="<? echo $data[9] ?>" readonly/> 
                                    </td>
                                    <td>
                                     <? 
										echo create_drop_down( "complexity_".$i, 80, $complexity_level, "",1," -- Select --", $data[4], "",$disabled,'' ); 
									 ?>
                                    </td>
                                    <td>
                                     <? 
										echo create_drop_down( "emblish_".$i, 80, $yes_no, "",1," -- Select--", $data[5], "",$disabled,'' ); 
									 ?>
                                   
                                    </td>
                                    <td>
                                    <input type="button" id="increaseset_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(<? echo $i; ?> )" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/>
                                    <input type="button" id="decreaseset_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(<? echo $i; ?> ,'tbl_set_details' );" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/>
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
									echo create_drop_down( "cboitem_1", 230, $garments_item, "",1,"--Select--", 0, "check_duplicate(1,this.id )",'','' ); 
									?>
                                    </td>
                                    <td>
                                    <input type="text" id="txtsetitemratio_1" name="txtsetitemratio_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(1)"  value="<?  if ($unit_id==1){echo "1";} else{echo "";}?>" <? if ($unit_id==1){echo "readonly";} else{echo "";}?> /> 
                                    </td>
                                    <td>
                                    <input type="text" id="smv_1" name="smv_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(1)"  value="0"  /> 
                                    <input type="hidden" id="smvset_1" name="smvset_1" style="width:30px" class="text_boxes_numeric"   value="0"  /> 
                                    </td>
                                    <td>
                                    <input type="text" id="cutsmv_1" name="cutsmv_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_cutsmv(1)"  value="0"  /> 
                                    <input type="hidden" id="cutsmvset_1" name="cutsmvset_1" style="width:30px" class="text_boxes_numeric"   value="0"  /> 
                                    </td>
                                     <td>
                                    <input type="text" id="finsmv_1" name="finsmv_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_finsmv(1)"  value="0"  /> 
                                    <input type="hidden" id="finsmvset_1" name="finsmvset_1" style="width:30px" class="text_boxes_numeric"   value="0"  /> 
                                    </td>
                                    <td>
                                   
                                    <? 
										echo create_drop_down( "complexity_1", 80, $complexity_level, "",1," -- Select --", 0, "",'','' ); 
									?>
                                    </td>
                                    <td>
                                     <? 
										echo create_drop_down( "emblish_1", 80, $yes_no, "",1," -- Select --", 0, "",'','' ); 
									?>
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
                <table width="640" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                            <th width="230">Total</th>
                            <th  width="40">
                                <input type="text" id="tot_set_qnty" name="tot_set_qnty" class="text_boxes_numeric" style="width:30px"  value="<? if($tot_set_qnty !=''){ echo $tot_set_qnty;} else{ echo 1;} ?>" readonly />
                            </th>
                             <th  width="40">
                                <input type="text" id="tot_smv_qnty" name="tot_smv_qnty" class="text_boxes_numeric" style="width:30px"  value="<? if($tot_smv_qnty !=''){ echo $tot_smv_qnty;} else{ echo 0;} ?>" readonly />
                            </th>
                             <th  width="40">
                                <input type="text" id="tot_cutsmv_qnty" name="tot_cutsmv_qnty" class="text_boxes_numeric" style="width:30px"  value="<? if($tot_cutsmv_qnty !=''){ echo $tot_cutsmv_qnty;} else{ echo 0;} ?>" readonly />
                            </th>
                            <th  width="40">
                                <input type="text" id="tot_finsmv_qnty" name="tot_finsmv_qnty" class="text_boxes_numeric" style="width:30px"  value="<? if($tot_finsmv_qnty !=''){ echo $tot_finsmv_qnty;} else{ echo 0;} ?>" readonly />
                            </th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
                <table width="560" cellspacing="0" class="" border="0">
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
 <script>
set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
set_sum_value_set( 'tot_smv_qnty', 'smvset_' );
set_sum_value_set( 'tot_cutsmv_qnty', 'cutsmvset_' );
set_sum_value_set( 'tot_finsmv_qnty', 'finsmvset_' );  

var fileParmission1='<? echo $_SESSION['logic_erp']['data_arr'][122][$cbo_company_name][txt_Sewing_SMV][is_disable];?>';
var fileParmission2='<? echo $_SESSION['logic_erp']['data_arr'][122][$cbo_company_name][txt_Cutting_SMV][is_disable];?>';
var fileParmission3='<? echo $_SESSION['logic_erp']['data_arr'][122][$cbo_company_name][txt_Finish_SMV][is_disable];?>';
var rowCount = $('#tbl_set_details tr').length-1;


for(var i=1;i<=rowCount;i++){
	if(fileParmission1==1){document.getElementById('smv_'+i).disabled =true;}
	else{document.getElementById('smv_'+i).disabled =false;}
	
	if(fileParmission2==1){document.getElementById('cutsmv_'+i).disabled =true;}
	else{document.getElementById('cutsmv_'+i).disabled =false;}
	
	if(fileParmission3==1){document.getElementById('finsmv_'+i).disabled =true;}
	else{document.getElementById('finsmv_'+i).disabled =false;}
	
	
	
}

</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

<?

}
/*
if($action=="booking_no_with_approved_status")
{
	
	$data=explode("_",$data);
	//echo $data[0];
	//echo $data[1];
	if($data[1]=="")
	{
		$sql="select booking_no,is_approved from wo_booking_mst where job_no='$data[0]' and booking_type=1 and is_short=2 and is_deleted=0 and status_active=1";
	}
	else
	{
		$sql="select a.booking_no,a.is_approved from wo_booking_mst a, wo_booking_dtls b where a.job_no=b.job_no and  a.job_no='$data[0]' and a.booking_type=1 and a.is_short=2 and b.po_break_down_id=$data[1] and a.is_deleted=0 and a.status_active=1 group by a.booking_no,is_approved";
	}
	$approved_booking="";
	$un_approved_booking="";
	$sql_booking=sql_select($sql);
	foreach($sql_booking as $row)
	{
		if($row[csf('is_approved')]==1)
		{
		  $approved_booking.=$row[csf('booking_no')].", ";	
		}
		else
		{
		  $un_approved_booking.=$row[csf('booking_no')].", ";	
		}
	}
	echo rtrim($approved_booking ,", ")."_".rtrim($un_approved_booking , ", ");
}
*/
 
// Dtls Form ************************************************Dtls Form************************************************
if ($action=="get_excess_cut_percent")
{
	$data=explode("_",$data);
	 $qry_result=sql_select( "select slab_rang_start,slab_rang_end,excess_percent from  var_prod_excess_cutting_slab where company_name='$data[1]' and variable_list=2 and status_active=1 and is_deleted=0");
	 foreach ($qry_result as $row)
	 {
		 if ( $data[0]>=$row[csf("slab_rang_start")] && $data[0]<=$row[csf("slab_rang_end")] )
		 {
			 echo $row[csf("excess_percent")]; die;
		 }
	 }
	 echo "0"; die;
}

if ($action=="populate_order_details_form_data")
{
	
	$data_array=sql_select("select id,is_confirmed,po_number,po_received_date,pub_shipment_date,shipment_date,factory_received_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,country_name,details_remarks,delay_for,status_active,packing,grouping,projected_po_id,tna_task_from_upto,file_no,sc_lc from wo_po_break_down where id='$data'");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('cbo_order_status').value = '".$row[csf("is_confirmed")]."';\n";  
		echo "document.getElementById('txt_po_no').value = '".$row[csf("po_number")]."';\n";  
		echo "document.getElementById('txt_po_received_date').value = '".change_date_format($row[csf("po_received_date")], "dd-mm-yyyy", "-")."';\n";  
		echo "document.getElementById('txt_pub_shipment_date').value = '".change_date_format($row[csf("pub_shipment_date")], "dd-mm-yyyy", "-")."';\n";  
		echo "document.getElementById('txt_org_shipment_date').value = '".change_date_format($row[csf("shipment_date")], "dd-mm-yyyy", "-")."';\n";  
		echo "document.getElementById('txt_factory_rec_date').value = '".change_date_format($row[csf("factory_received_date")], "dd-mm-yyyy", "-")."';\n";  
		echo "document.getElementById('txt_po_quantity').value = '".$row[csf("po_quantity")]."';\n";  
		echo "document.getElementById('txt_avg_price').value = '".$row[csf("unit_price")]."';\n";  
		echo "document.getElementById('txt_amount').value = '".$row[csf("po_total_price")]."';\n";  
		echo "document.getElementById('txt_excess_cut').value = '".$row[csf("excess_cut")]."';\n";  
		echo "document.getElementById('txt_plan_cut').value = '".$row[csf("plan_cut")]."';\n";  
		//echo "document.getElementById('cbo_po_country').value = '".$row[csf("country_name")]."';\n";  
		echo "document.getElementById('txt_details_remark').value = '".$row[csf("details_remarks")]."';\n";  
		echo "document.getElementById('cbo_status').value = '".$row[csf("status_active")]."';\n";  
		echo "document.getElementById('update_id_details').value = '".$row[csf("id")]."';\n"; 
		echo "set_multiselect('cbo_delay_for','0','1','".($row[csf("delay_for")])."','0');\n"; 
		//echo "load_drop_down( 'requires/woven_order_entry_controller', '".$row[csf('po_received_date')]."'_'".$row[csf('pub_shipment_date')]."'_'.cbo_buyer_name, 'load_drop_down_tna_task', 'tna_task_td' );\n";
		//echo "set_tna_task();\n"; 
		if($row[csf("is_confirmed")]==1)
		{
			echo "$('#cbo_order_status').attr('disabled','true')".";\n";
		}
		else echo "$('#cbo_order_status').removeAttr('disabled')".";\n";
		echo "$('#txt_po_received_date').attr('disabled','true')".";\n";
		echo "$('#txt_org_shipment_date').attr('disabled','true')".";\n";
		echo "$('#cbo_delay_for').attr('disabled','true')".";\n";
		echo "document.getElementById('cbo_packing_po_level').value = '".$row[csf("packing")]."';\n";  
		echo "document.getElementById('txt_grouping').value = '".$row[csf("grouping")]."';\n"; 
		echo "document.getElementById('cbo_projected_po').value = '".$row[csf("projected_po_id")]."';\n";  
		echo "document.getElementById('cbo_tna_task').value = '".$row[csf("tna_task_from_upto")]."';\n"; 
                echo "document.getElementById('txt_file_no').value = '".$row[csf("file_no")]."';\n"; 
                echo "document.getElementById('txt_sc_lc').value = '".$row[csf("sc_lc")]."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_order_entry_details',2);\n";  
	}
	 $qry_result=sql_select( "select id from  wo_po_color_size_breakdown where po_break_down_id='$data' and  status_active=1 and is_deleted=0");
	 $row=count($qry_result);
	 if($row>0)
	 {
				echo "$('#txt_avg_price').attr('disabled','true')".";\n";
				echo "$('#txt_avg_price').attr('title','Change It From Color Size Break Down')".";\n";

	 }
	 else
	 {
				echo "$('#txt_avg_price').removeAttr('disabled')".";\n";
				echo "$('#txt_avg_price').removeAttr('title')".";\n";
	 }
	 
	
}



if ($action=="save_update_delete_mst")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
// Insert Here----------------------------------------------------------
	if ($operation==0) 
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=return_next_id( "id", "wo_po_details_master", 1 ) ;
		//echo "select job_no_prefix,job_no_prefix_num from wo_po_details_master where company_name=$cbo_company_name and YEAR('Y',insert_date)=".date('Y',time())." order by job_no_prefix_num desc"; die; 
		if($db_type==0)
		{
		$new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select job_no_prefix,job_no_prefix_num from wo_po_details_master where company_name=$cbo_company_name and YEAR(insert_date)=".date('Y',time())." order by job_no_prefix_num desc ", "job_no_prefix", "job_no_prefix_num" ));
		}
		if($db_type==2)
		{
		$new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select job_no_prefix,job_no_prefix_num from wo_po_details_master where company_name=$cbo_company_name and to_char(insert_date,'YYYY')=".date('Y',time())." order by job_no_prefix_num desc ", "job_no_prefix", "job_no_prefix_num" ));
		}
		
		$field_array="id,garments_nature,quotation_id,job_no,job_no_prefix,job_no_prefix_num,company_name,buyer_name,location_name,style_ref_no,style_description,product_dept,product_code,pro_sub_dep,currency_id,agent_name,client_id,order_repeat_no,region,product_category,team_leader,dealing_marchant,bh_merchant,factory_marchant,packing,remarks,ship_mode,order_uom,gmts_item_id,set_break_down, total_set_qnty,set_smv,season_buyer_wise,is_deleted,status_active,inserted_by,insert_date";
		
		$data_array="(".$id.",".$garments_nature.",".$txt_quotation_id.",'".$new_job_no[0]."','".$new_job_no[1]."','".$new_job_no[2]."',".$cbo_company_name.",".$cbo_buyer_name.",".$cbo_location_name.",".$txt_style_ref.",".$txt_style_description.",".$cbo_product_department.",".$txt_product_code.",".$cbo_sub_dept.",".$cbo_currercy.",".$cbo_agent.",".$cbo_client.",".$txt_repeat_no.",".$cbo_region.",".$txt_item_catgory.",".$cbo_team_leader.",".$cbo_dealing_merchant.",".$txt_bhmerchant.",".$cbo_factory_merchant.",".$cbo_packing.",".$txt_remarks.",".$cbo_ship_mode.",".$cbo_order_uom.",".$item_id.",".$set_breck_down.",".$tot_set_qnty.",".$tot_smv_qnty.",".$cbo_season_name.",0,1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		$field_array1="id,job_no,gmts_item_id,set_item_ratio,smv_pcs,smv_set,complexity,embelishment,cutsmv_pcs,cutsmv_set,finsmv_pcs,finsmv_set";
		$add_comma=0;
		$id1=return_next_id( "id", "wo_po_details_mas_set_details", 1 ) ;
		$set_breck_down_array=explode('__',str_replace("'",'',$set_breck_down));
		for($c=0;$c < count($set_breck_down_array);$c++)
		{
			$set_breck_down_arr=explode('_',$set_breck_down_array[$c]);
			if ($add_comma!=0) $data_array1 .=",";
			if($set_breck_down_arr[2]==0 || $set_breck_down_arr[2]==''){
				echo "SMV**";
				disconnect($con);die;
			}
			$data_array1 .="(".$id1.",'".$new_job_no[0]."','".$set_breck_down_arr[0]."','".$set_breck_down_arr[1]."','".$set_breck_down_arr[2]."','".$set_breck_down_arr[3]."','".$set_breck_down_arr[4]."','".$set_breck_down_arr[5]."','".$set_breck_down_arr[6]."','".$set_breck_down_arr[7]."','".$set_breck_down_arr[8]."','".$set_breck_down_arr[9]."')";
			$add_comma++;
			$id1=$id1+1;
		}
		$rID=sql_insert("wo_po_details_master",$field_array,$data_array,0);
		$rID1=sql_insert("wo_po_details_mas_set_details",$field_array1,$data_array1,1);
		if($db_type==0)
		{
			if($rID && $rID1)
			{
				mysql_query("COMMIT");  
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1)
			{
				oci_commit($con);  
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
// Insert Here End------------------------------------------------------
// Update Here----------------------------------------------------------
	else if ($operation==1) 
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$PrevData=sql_select("select style_ref_no,gmts_item_id from wo_po_details_master where job_no=$txt_job_no");
		$PrevStyleRefNo=$PrevData[0][csf('style_ref_no')];
		$PrevGmtsItemId=$PrevData[0][csf('gmts_item_id')];
		$field_array="quotation_id*buyer_name*location_name*style_ref_no*style_description*product_dept*product_code*pro_sub_dep*currency_id*agent_name*client_id*order_repeat_no*region*product_category*team_leader*dealing_marchant*bh_merchant*factory_marchant*packing*remarks*ship_mode*order_uom*gmts_item_id*set_break_down*total_set_qnty*set_smv*season_buyer_wise*style_ref_no_prev*gmts_item_id_prev*is_deleted*status_active*updated_by*update_date";
		$data_array="".$txt_quotation_id."*".$cbo_buyer_name."*".$cbo_location_name."*".$txt_style_ref."*".$txt_style_description."*".$cbo_product_department."*".$txt_product_code."*".$cbo_sub_dept."*".$cbo_currercy."*".$cbo_agent."*".$cbo_client."*".$txt_repeat_no."*".$cbo_region."*".$txt_item_catgory."*".$cbo_team_leader."*".$cbo_dealing_merchant."*".$txt_bhmerchant."*".$cbo_factory_merchant."*".$cbo_packing."*".$txt_remarks."*".$cbo_ship_mode."*".$cbo_order_uom."*".$item_id."*".$set_breck_down."*".$tot_set_qnty."*".$tot_smv_qnty."*".$cbo_season_name."*'".$PrevStyleRefNo."'*'".$PrevGmtsItemId."'*0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$field_array1="id,job_no, gmts_item_id,set_item_ratio,smv_pcs,smv_set,complexity,embelishment,cutsmv_pcs,cutsmv_set,finsmv_pcs,finsmv_set";
		$add_comma=0;
		$id1=return_next_id( "id", "wo_po_details_mas_set_details", 1 ) ;
		$set_breck_down_array=explode('__',str_replace("'",'',$set_breck_down));
		for($c=0;$c < count($set_breck_down_array);$c++)
		{
			$set_breck_down_arr=explode('_',$set_breck_down_array[$c]);
			if ($add_comma!=0) $data_array1 .=",";
			if($set_breck_down_arr[2]==0 || $set_breck_down_arr[2]==''){
				echo "SMV**";
				disconnect($con);die;
			}
			$data_array1 .="(".$id1.",".$txt_job_no.",'".$set_breck_down_arr[0]."','".$set_breck_down_arr[1]."','".$set_breck_down_arr[2]."','".$set_breck_down_arr[3]."','".$set_breck_down_arr[4]."','".$set_breck_down_arr[5]."','".$set_breck_down_arr[6]."','".$set_breck_down_arr[7]."','".$set_breck_down_arr[8]."','".$set_breck_down_arr[9]."')";
			$add_comma++;
			$id1=$id1+1;
		}
		//print_r($data_array);
		$rID=sql_update("wo_po_details_master",$field_array,$data_array,"job_no","".$txt_job_no."",0);
		//echo "10**".$rID; die;
		$rID1=execute_query("delete from wo_po_details_mas_set_details where  job_no =".$txt_job_no."",0);
		$rID2=sql_insert("wo_po_details_mas_set_details",$field_array1,$data_array1,0);
		//$rID3=execute_query( "update  wo_booking_mst set is_apply_last_update=2  where  job_no =".$txt_job_no." and booking_type=1 and is_short=2 ",1);
		$txt_job_no=str_replace("'","",$txt_job_no);
		if($db_type==0)
		{
			if($rID && $rID1 && $rID2 )
			{
				mysql_query("COMMIT");  
				echo "1**".$txt_job_no."**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1 && $rID2  )
			{
				oci_commit($con); 
				echo "1**".$txt_job_no."**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
// Update Here End ----------------------------------------------------------
// Delete Here----------------------------------------------------------
	else if ($operation==2)   
	{
		$con = connect();
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="'0'*'1'".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_delete("wo_po_details_master",$field_array,$data_array,"job_no","".$txt_job_no."",1);
		$rID1=sql_delete("wo_po_break_down",$field_array,$data_array,"job_no_mst","".$txt_job_no."",1);
		$txt_job_no=str_replace("'","",$txt_job_no);
		if($db_type==0)
		{
			if($rID && $rID1)
			{
				mysql_query("COMMIT");  
				echo "2**".$txt_job_no."**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1)
			{
				oci_commit($con); 
				echo "2**".$txt_job_no."**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
	}
// Delete Here End ----------------------------------------------------------
}




if ($action=="save_update_delete_dtls")
{
	
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$packing ="";
	if(str_replace("'","",$cbo_packing_po_level)==0)
	{
		$packing = $cbo_packing;
	}
	else
	{
		$packing = $cbo_packing_po_level;
	}
	if (file_exists('dateretriction.php'))
	{
		require('dateretriction.php');
	}
	if ($operation==0)  // Insert Here
	{	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		/*if (is_duplicate_field( "po_number", "wo_po_break_down", "po_number=$txt_po_no and job_no_mst=$update_id and pub_shipment_date=$txt_pub_shipment_date and po_quantity= $txt_po_quantity   and is_deleted=0" ) == 1)
		{
			echo "11**0"; 
			die;
		}*/
		$data_shipDate_vari="";
		$sql_shipDate_vari=sql_select("select duplicate_ship_date from variable_order_tracking where company_name=$cbo_company_name and variable_list=29");
		foreach($sql_shipDate_vari as $row_shipDate_vari)
		{
		$data_shipDate_vari=$row_shipDate_vari[csf('duplicate_ship_date')];	
		}
		if($data_shipDate_vari==1)
		{
		$txt_pub_shipment_date_cond="and pub_shipment_date=$txt_pub_shipment_date";	
		}
		else
		{
		$txt_pub_shipment_date_cond="";	
		}
		if (is_duplicate_field( "po_number", "wo_po_break_down", "po_number=$txt_po_no and job_no_mst=$update_id  $txt_pub_shipment_date_cond and is_deleted=0" ) == 1)
		{
			echo "11**0"; 
			disconnect($con);die;
		}
		$org_shipment_date=$txt_org_shipment_date;
		if(trim($org_shipment_date,"'")=="")
		{
			$org_shipment_date=$txt_pub_shipment_date;
		}
		
		$txt_pub_shipment_date=$txt_pub_shipment_date;
		if(trim($txt_pub_shipment_date,"'")=="")
		{
			$txt_pub_shipment_date=$txt_org_shipment_date;
		}
		
		$id=return_next_id( "id", "wo_po_break_down", 1 ) ;
		$field_array="id,job_no_mst,is_confirmed,po_number,po_received_date,pub_shipment_date,shipment_date,factory_received_date,po_quantity,unit_price,original_avg_price,po_total_price,excess_cut,plan_cut,details_remarks,delay_for,packing,grouping,projected_po_id,tna_task_from_upto,t_year,t_month,original_po_qty,file_no,sc_lc,is_deleted,status_active,inserted_by,insert_date";
		$data_array="(".$id.",".$update_id.",".$cbo_order_status.",".$txt_po_no.",".$txt_po_received_date.",".$txt_pub_shipment_date.",".$org_shipment_date.",".$txt_factory_rec_date.",".$txt_po_quantity.",".$txt_avg_price.",".$txt_avg_price.",".$txt_amount.",".$txt_excess_cut.",".$txt_plan_cut.",".$txt_details_remark.",".$cbo_delay_for.",".$packing.",".$txt_grouping.",".$cbo_projected_po.",".$cbo_tna_task.",'".date("Y",strtotime(str_replace("'","",$txt_org_shipment_date)))."','".date("m",strtotime(str_replace("'","",$txt_org_shipment_date)))."',".$txt_po_quantity.",".$txt_file_no.",".$txt_sc_lc.",0,".$cbo_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		$rID=sql_insert("wo_po_break_down",$field_array,$data_array,0);		
//====================================================================================
		/*$add_comma=0;
		$id1=return_next_id( "id", "wo_po_color_size_breakdown", 1 ) ;
		$field_array1="id,po_break_down_id,job_no_mst,color_mst_id,	size_mst_id,item_mst_id, article_number, item_number_id,size_number_id,color_number_id, order_quantity, order_rate,order_total ,excess_cut_perc, plan_cut_qnty,is_deleted,status_active,inserted_by,insert_date";
		$new_array_size=array();
		$new_array_color=array();
		$color_mst=array();
		$size_mst=array();
		$item_mst=array();
		$color_size_break_down_array=explode('__',str_replace("'",'',$color_size_break_down));
		if($color_size_break_down_array[0]=="")
		{
			$color_size_break_down_array=array();
		}
		if ( count($color_size_break_down_array)>0)
		{
			for($c=0;$c < count($color_size_break_down_array);$c++)
			{
				 $color_size_break_down_arr=explode('_',$color_size_break_down_array[$c]);
				 $cbogmtsitem=$color_size_break_down_arr[6];
				 $txtarticleno=$color_size_break_down_arr[7];
				 $txtcolor=$color_size_break_down_arr[8];
				 $txtsize=$color_size_break_down_arr[9];
				 $txtorderquantity=$color_size_break_down_arr[10];
				 $txtorderrate=$color_size_break_down_arr[11];
				 $txtorderamount=$color_size_break_down_arr[12];
				 $txtorderexcesscut=$color_size_break_down_arr[13];
				 $txtorderplancut=$color_size_break_down_arr[14];
				 $cbostatus=$color_size_break_down_arr[15];
				 
				 if (!in_array(str_replace("'","",$txtcolor),$new_array_color))
				 {
					  $color_id = return_id( str_replace("'","",$txtcolor), $color_library, "lib_color", "id,color_name");  
					  $new_array_color[$color_id]=str_replace("'","",$txtcolor);
				 }
				 else 
				 {
					 $color_id =  array_search(str_replace("'","",$txtcolor), $new_array_color);
				 }
				 
				 
				 
				 if (!in_array(str_replace("'","",$txtsize),$new_array_size))
				 {
					  $size_id = return_id( str_replace("'","",$txtsize), $size_library, "lib_size", "id,size_name");   
					  $new_array_size[$size_id]=str_replace("'","",$txtsize);
				 }
				 else 
				 {
					$size_id =  array_search(str_replace("'","",$txtsize), $new_array_size); 
				 }
				 
				 
				 if(!in_array($cbogmtsitem,$item_mst))
				 {
					 $item_mst[$id1] = $cbogmtsitem;
					 $item_mst_id=$id1;
					 $color_mst= array();
					 $size_mst=array();
				 }
				 else
				 {
					 $item_mst_id=0;	 
				 }


				 if(!in_array($color_id,$color_mst))
				 {
					 $color_mst[$id1]=$color_id;
					 $color_mst_id=$id1;
				 }
				 else
				 {
				   $color_mst_id=0;	 
				 }
				 
				 if(!in_array($size_id,$size_mst))
				 {
					 $size_mst[$id1]=$size_id;
					 $size_mst_id=$id1;
				 }
				 else
				 {
				   $size_mst_id=0;	 
				 }
				 
				 if(!in_array($cbogmtsitem,$item_mst))
				 {
					 $item_mst[$id1] = $cbogmtsitem;
					 $item_mst_id=$id1;
				 }
				 else
				 {
					 $item_mst_id=0;	 
				 }
				 
				 if ($add_comma!=0) $data_array1 .=",";
				 $data_array1 .="(".$id1.",".$id.",".$update_id.",".$color_mst_id.",".$size_mst_id.",".$item_mst_id.",'".$txtarticleno."','".$cbogmtsitem."','".$size_id."','".$color_id."','".$txtorderquantity."','".$txtorderrate."','".$txtorderamount."','".$txtorderexcesscut."','".$txtorderplancut."',0,'".$cbostatus."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				 $id1=$id1+1;
				 $add_comma++;
			}
		//$rID1=sql_insert("wo_po_color_size_breakdown",$field_array1,$data_array1,0);
		}*/
//=================================================
 		
		/*if($data_array1 !='')
		{
				 $rID1=sql_insert("wo_po_color_size_breakdown",$field_array1,$data_array1,0);

				 $sam=1;
				 $id_sm=return_next_id( "id", "wo_po_sample_approval_info", 1 ) ;
				 $cbosampletype=return_field_value( 'id', 'lib_sample', 'sample_type=2 and status_active=1 and is_deleted=0' );
				 $field_array_sm="id,job_no_mst,po_break_down_id,color_number_id,sample_type_id,status_active,is_deleted";
				 $data_array_sample=sql_select("select a.id as po_id,a.po_number, b.id as color_size_table_id, b.color_mst_id, b.color_number_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.job_no_mst=$update_id and b.color_mst_id !=0 and a.id=b.po_break_down_id and  b.po_break_down_id='$id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.po_break_down_id,b.color_mst_id order by a.id");
				 foreach ( $data_array_sample as $row_sam1 )
				 {
					  if ($sam!=1) $data_array_sm .=",";
					  $data_array_sm .="(".$id_sm.",".$update_id.",".$row_sam1[csf('po_id')].",".$row_sam1[csf('color_size_table_id')].",".$cbosampletype.",1,0)";
					  $id_sm=$id_sm+1;
					  $sam=$sam+1;
				 }
				 $rID=sql_insert("wo_po_sample_approval_info",$field_array_sm,$data_array_sm,0); 
		}*/
//============================================================================================
		$return_data=update_job_mast($update_id);//define in common_functions.php
		update_cost_sheet($update_id);
//=============================================================================================
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "0**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);  
				echo "0**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
			else
			{
				oci_rollback($con); 
				echo "10**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
		}
		disconnect($con);
		die;
	}
	
	
	else if ($operation==1)   // Update Here
	{
		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		/*if (is_duplicate_field( "po_number", "wo_po_break_down", "po_number=$txt_po_no and job_no_mst=$update_id and pub_shipment_date=$txt_pub_shipment_date and po_quantity= $txt_po_quantity   and id!=$update_id_details and is_deleted=0" ) == 1)
		{
			echo "11**0"; 
			die;
		}*/
		$data_shipDate_vari="";
		$sql_shipDate_vari=sql_select("select duplicate_ship_date from variable_order_tracking where company_name=$cbo_company_name and variable_list=29");
		foreach($sql_shipDate_vari as $row_shipDate_vari)
		{
		$data_shipDate_vari=$row_shipDate_vari[csf('duplicate_ship_date')];	
		}
		if($data_shipDate_vari==1)
		{
		$txt_pub_shipment_date_cond="and pub_shipment_date=$txt_pub_shipment_date";	
		}
		else
		{
		$txt_pub_shipment_date_cond="";	
		}
		//echo "select duplicate_ship_date from variable_order_tracking where company_name=$cbo_company_name and variable_list=29";
		if (is_duplicate_field( "po_number", "wo_po_break_down", "po_number=$txt_po_no and job_no_mst=$update_id $txt_pub_shipment_date_cond and id!=$update_id_details and is_deleted=0" ) == 1)
		{
			echo "11**0"; 
			disconnect($con);die;
		}
		$org_shipment_date=$txt_org_shipment_date;
		if(trim($org_shipment_date,"'")=="")
		{
			$org_shipment_date=$txt_pub_shipment_date;
		}
		
		$txt_pub_shipment_date=$txt_pub_shipment_date;
		if(trim($txt_pub_shipment_date,"'")=="")
		{
			$txt_pub_shipment_date=$txt_org_shipment_date;
		}
		$prev_data=sql_select("SELECT is_confirmed,po_number,po_received_date,pub_shipment_date,shipment_date,factory_received_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,details_remarks,delay_for,packing,grouping,projected_po_id,tna_task_from_upto,t_year,t_month,file_no,is_deleted,status_active,updated_by,update_date FROM wo_po_break_down WHERE id=$update_id_details");
		foreach($prev_data as $rows)
		{
			$prev_po_no=$rows[csf('po_number')];
			$prev_order_status=$rows[csf('is_confirmed')];
			$prev_po_received_date=$rows[csf('po_received_date')];
			$prev_po_qty=$rows[csf('po_quantity')];
			$prev_pub_shipment_date=$rows[csf('pub_shipment_date')];
			$prev_status=$rows[csf('status_active')];
			$prev_org_shipment_date=$rows[csf('shipment_date')];
			$prev_factory_rec_date=$rows[csf('factory_received_date')];
			$prev_projected_po=$rows[csf('projected_po_id')];
			$prev_packing=$rows[csf('packing')];
			$prev_details_remark=$rows[csf('details_remarks')];
			$prev_file_no=$rows[csf('file_no')];
			$prev_avg_price=$rows[csf('unit_price')];
			$prev_excess_cut=$rows[csf('excess_cut')];
			$prev_plan_cut=$rows[csf('plan_cut')];
			$prev_status=$rows[csf('status_active')];
			$prev_updated_by=$rows[csf('updated_by')];
			$prev_update_date=$rows[csf('update_date')];
		}
                $field_array="is_confirmed*po_number*po_received_date*pub_shipment_date*shipment_date*factory_received_date*po_quantity*unit_price*po_total_price*excess_cut*plan_cut*details_remarks*delay_for*packing*grouping*projected_po_id*tna_task_from_upto*t_year*t_month*file_no*sc_lc*po_number_prev*pub_shipment_date_prev*is_deleted*status_active*updated_by*update_date";
				
		$data_array ="".$cbo_order_status."*".$txt_po_no."*".$txt_po_received_date."*".$txt_pub_shipment_date."*".$org_shipment_date."*".$txt_factory_rec_date."*".$txt_po_quantity."*".$txt_avg_price."*".$txt_amount."*".$txt_excess_cut."*".$txt_plan_cut."*".$txt_details_remark."*".$cbo_delay_for."*".$packing."*".$txt_grouping."*".$cbo_projected_po."*".$cbo_tna_task."*".date("Y",strtotime(str_replace("'","",$txt_org_shipment_date)))."*".date("m",strtotime(str_replace("'","",$txt_org_shipment_date)))."*".$txt_file_no."*".$txt_sc_lc."*'".$prev_po_no."'*'".$prev_pub_shipment_date."'*0*".$cbo_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		
		//History Code....shajjad
		
		$log_id_mst = return_next_id( "id", "wo_po_update_log", 1 ) ;
		
		if($db_type==0)
		{
			$current_date = $pc_date_time;
		}
		else
		{
			$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
		}
		
		$previous_po_qty=return_field_value("po_quantity","wo_po_break_down","job_no_mst=".$update_id." and id=".$update_id_details."");
		
		$log_update_date=return_field_value("update_date","wo_po_update_log","job_no=".$update_id." and po_id=".$update_id_details." order by id DESC");
		
		$log_update=date("Y-m-d", strtotime($log_update_date));
		$curr_date=date("Y-m-d", strtotime($current_date));
		
		if($log_update=="" || $log_update!=$curr_date)
		{
			$field_array_history="id,entry_form,job_no,po_no,po_id,order_status,po_received_date,previous_po_qty,shipment_date,org_ship_date,po_status,t_year,t_month,update_date,update_by";
			
			$data_array_history="(".$log_id_mst.",2,".$update_id.",".$txt_po_no.",".$update_id_details.",".$cbo_order_status.",".$txt_po_received_date.",".$previous_po_qty.",".$txt_pub_shipment_date.",".$txt_org_shipment_date.",".$cbo_status.",'".date("Y",strtotime(str_replace("'","",$txt_org_shipment_date)))."','".date("m",strtotime(str_replace("'","",$txt_org_shipment_date)))."','".$current_date."',".$_SESSION['logic_erp']['user_id'].")";
			
			$rID3=sql_insert("wo_po_update_log",$field_array_history,$data_array_history,1);	
			
		}
		else if( $log_update==$curr_date)
		{
			
			$field_array_history="job_no*po_no*po_id*order_status*po_received_date*previous_po_qty*shipment_date*org_ship_date*po_status*update_date*update_by";
			
			$data_array_history="".$update_id."*".$txt_po_no."*".$update_id_details."*".$cbo_order_status."*".$txt_po_received_date."*".$txt_po_quantity."*".$txt_pub_shipment_date."*".$txt_org_shipment_date."*".$cbo_status."*'".$current_date."'*".$_SESSION['logic_erp']['user_id']."";
			
			$rID3=sql_update("wo_po_update_log",$field_array_history,$data_array_history,"po_id*update_date","".$update_id_details."*'".$log_update_date."'",1);
		}
		
		//History Code....shajjad
		
		
		$rID=sql_update("wo_po_break_down",$field_array,$data_array,"id","".$update_id_details."",1);
		//$rID2=execute_query( "update  wo_booking_mst set is_apply_last_update=2  where  job_no =".$update_id." and booking_type=1 and is_short=2 ",1);
		
		
		
		
//======================================================
		/*$new_array_size=array();
		$new_array_color=array();
		$color_mst=array();
		$size_mst=array();
		$item_mst=array();
		$color_size_break_down_array=explode('__',str_replace("'",'',$color_size_break_down));
		if($color_size_break_down_array[0]=="")
		{
			$color_size_break_down_array=array();
		}
		if ( count($color_size_break_down_array)>0)
		{
			for($c=0;$c < count($color_size_break_down_array);$c++)
			{
				 $color_size_break_down_arr=explode('_',$color_size_break_down_array[$c]);
				 $color_size_table_id=$color_size_break_down_arr[5];
				 $cbogmtsitem=$color_size_break_down_arr[6];
				 $txtarticleno=$color_size_break_down_arr[7];
				 $txtcolor=$color_size_break_down_arr[8];
				 $txtsize=$color_size_break_down_arr[9];
				 $txtorderquantity=$color_size_break_down_arr[10];
				 $txtorderrate=$color_size_break_down_arr[11];
				 $txtorderamount=$color_size_break_down_arr[12];
				 $txtorderexcesscut=$color_size_break_down_arr[13];
				 $txtorderplancut=$color_size_break_down_arr[14];
				 $cbostatus=$color_size_break_down_arr[15];
				 if (!in_array(str_replace("'","",$txtcolor),$new_array_color))
				 {
					  $color_id = return_id( str_replace("'","",$txtcolor), $color_library, "lib_color", "id,color_name");  
					  $new_array_color[$color_id]=str_replace("'","",$txtcolor);
				 }
				 else
				 {
					 $color_id =  array_search(str_replace("'","",$txtcolor), $new_array_color); 
				 }
				
				 if (!in_array(str_replace("'","",$txtsize),$new_array_size))
				 {
					  $size_id = return_id( str_replace("'","",$txtsize), $size_library, "lib_size", "id,size_name");   
					  $new_array_size[$size_id]=str_replace("'","",$txtsize);
				 }
				 else
				 {
					$size_id =  array_search(str_replace("'","",$txtsize), $new_array_size); 
				 }
				if($color_size_table_id!=0)
				{
					if(!in_array($cbogmtsitem,$item_mst))
					 {
						 $item_mst[$color_size_table_id]=$cbogmtsitem;
						 $item_mst_id=$color_size_table_id;
						 $color_mst= array();
					     $size_mst=array();
					 }
					 else
					 {
					   $item_mst_id=0;	 
					 }
					if(!in_array($color_id,$color_mst))
					 {
						 $color_mst[$color_size_table_id]=$color_id;
						 $color_mst_id=$color_size_table_id;
					 }
					 else
					 {
					   $color_mst_id=0;	 
					 }
					 
					 if(!in_array($size_id,$size_mst))
					 {
						 $size_mst[$color_size_table_id]=$size_id;
						 $size_mst_id=$color_size_table_id;
					 }
					 else
					 {
					   $size_mst_id=0;	 
					 }
					 
					 
					$field_array1="color_mst_id*size_mst_id*item_mst_id*article_number*item_number_id*size_number_id*color_number_id*order_quantity*order_rate*order_total*excess_cut_perc* plan_cut_qnty*is_deleted*status_active*inserted_by*insert_date";
					$data_array1="'".$color_mst_id."'*'".$size_mst_id."'*'".$item_mst_id."'*'".$txtarticleno."'*'".$cbogmtsitem."'*'".$size_id."'*'".$color_id."'*'".$txtorderquantity."'*'".$txtorderrate."'*'".$txtorderamount."'*'".$txtorderexcesscut."'*'".$txtorderplancut."'*0*'".$cbostatus."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
					$rID1=sql_update("wo_po_color_size_breakdown",$field_array1,$data_array1,"id","".$color_size_table_id."",1);
				 }
				
				if($color_size_table_id==0)
				{
					$id1=return_next_id( "id", "wo_po_color_size_breakdown", 1 ) ;
					 if(!in_array($color_id,$color_mst))
					 {
						 $color_mst[$id1]=$color_id;
						 $color_mst_id=$id1;
					 }
					 else
					 {
					   $color_mst_id=0;	 
					 }
					 if(!in_array($size_id,$size_mst))
					 {
						 $size_mst[$id1]=$size_id;
						 $size_mst_id=$id1;
					 }
					 else
					 {
					   $size_mst_id=0;	 
					 }
					 
					 if(!in_array($cbogmtsitem,$item_mst))
					 {
						 $item_mst[$id1] = $cbogmtsitem;
						 $item_mst_id=$id1;
					 }
					 else
					 {
					   $item_mst_id=0;	 
					 }
					 
					$field_array1="id,po_break_down_id,job_no_mst,color_mst_id,size_mst_id,item_mst_id,article_number, item_number_id,size_number_id,color_number_id, order_quantity, order_rate,order_total ,excess_cut_perc, plan_cut_qnty,is_deleted,status_active,inserted_by,insert_date";
					$data_array1 ="(".$id1.",".$update_id_details.",".$update_id.",".$color_mst_id.",".$size_mst_id.",".$item_mst_id.",'".$txtarticleno."','".$cbogmtsitem."','".$size_id."','".$color_id."','".$txtorderquantity."','".$txtorderrate."','".$txtorderamount."','".$txtorderexcesscut."','".$txtorderplancut."',0,'".$cbostatus."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$rID1=sql_insert("wo_po_color_size_breakdown",$field_array1,$data_array1,1);
				 }
			}
			
		}*/
//=================================================
//=================================================
		$return_data= update_job_mast($update_id);//define in common_functions.php
		update_cost_sheet($update_id);
//==================================================
		$txt_job_no=str_replace("'","",$txt_job_no);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con); 
				echo "0**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
			else{
				oci_rollback($con); 
				echo "10**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
		}
		disconnect($con);
		die;
	}
	
	
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_delete("wo_po_break_down",$field_array,$data_array,"id","".$update_id_details."",1);
		$return_data=update_job_mast($update_id);//define in common_functions.php
		update_cost_sheet($update_id);
		$txt_job_no=str_replace("'","",$txt_job_no);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con); 
				echo "0**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
			else{
				oci_rollback($con); 
				echo "10**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
		}
		//echo "2**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3];
		disconnect($con);
		die;
	}
}

// function================

function get_tna_template( $remain_days, $tna_template, $buyer )
{
	global $tna_template_buyer;
	if(count($tna_template_buyer[$buyer])>0)
	{ 
		$n=count($tna_template_buyer[$buyer]); 
		for($i=0;$i<$n;$i++)
		{ 
			if($remain_days<=$tna_template_buyer[$buyer][$i]['lead']) 
			{
				if ($i!=0)
				{
					$up_day=$tna_template_buyer[$buyer][$i]['lead']-$remain_days;
					$low_day=$remain_days-$tna_template_buyer[$buyer][$i-1]['lead'];
					if ($up_day>=$low_day)
						return $tna_template_buyer[$buyer][$i-1]['id'];
					else
						return $tna_template_buyer[$buyer][$i]['id'];
				}
				else
				{
					return $tna_template_buyer[$buyer][$i]['id'];
				}
			}
		}
	}
	else
	{
		$n=count($tna_template); 
		for($i=0;$i<$n;$i++)
		{
			if($remain_days<=$tna_template[$i]['lead']) 
			{
				if ($i!=0)
				{
					$up_day=$tna_template[$i]['lead']-$remain_days;
					$low_day=$remain_days-$tna_template[$i-1]['lead'];
					if ($up_day>=$low_day)
						return $tna_template[$i-1]['id'];
					else

						return $tna_template[$i]['id'];
				}
				else
				{
					return $tna_template[$i]['id'];
				}
			}
		}
	}
}

if ($action=="actual_po_info_popup")
{
	echo load_html_head_contents("Actual PO Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);

?> 
	<script>
	var permission='<? echo $permission; ?>';
		
function add_break_down_tr(i) 
 {
	var row_num=$('#tbl_list_search tbody tr').length;
	if (row_num!=i)
	{
		return false;
	}
	else
	{
		i++;
	 
		 $("#tbl_list_search tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }              
			});  
		  }).end().appendTo("#tbl_list_search");
		 $('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
		 $('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
		 $('#poNo_'+i).val("");
		 $('#poQnty_'+i).val("");
		 $('#rowid_'+i).val("");

	}
		  
}
function fn_deletebreak_down_tr(rowNo) 
{   
	
	
		var numRow = $('table#tbl_list_search tbody tr').length; 
		if(rowNo!=1)
		{
			    var permission_array=permission.split("_");
				var rowid=$('#rowid_'+rowNo).val();
				if(rowid !="" && permission_array[2]==1)
				{
				var booking=return_global_ajax_value(rowid, 'delete_row', '', 'woven_order_entry_controller_update');
				}
				var index=rowNo-1
			    $('#tbl_list_search tbody tr:eq('+index+')').remove();
				var numRow = $('table#tbl_list_search tbody tr').length; 
				for(i = rowNo;i <= numRow;i++)
				{
					$("#size_color_break_down_list tr:eq("+i+")").find("input,select").each(function() {
							$(this).attr({
								'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
								//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
								'value': function(_, value) { return value }             
							}); 
					})
				}
		}
}
		
function fnc_acc_po_info( operation )
{
	   var row_num = $('table#tbl_list_search tbody tr').length; 
		var data_all='&poid='+document.getElementById('hid_po_id').value+'&txt_job_no='+document.getElementById('txt_job_no').value;
		for (var i=1; i<=row_num; i++)
		{
			
			if (form_validation('poNo_'+i+'*poQnty_'+i,'PO No*PO Qty')==false)
			{
				return;
			}
			
		data_all=data_all+get_submitted_data_string('poNo_'+i+'*poQnty_'+i+'*rowid_'+i,"../../../",i);
		}
		
		var data="action=save_update_delete_accpoinfo&operation="+operation+'&total_row='+row_num+data_all;
		//alert(data);
		//return;
		freeze_window(operation);
		http.open("POST","woven_order_entry_controller_update.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_acc_po_info_reponse;
}

function fnc_acc_po_info_reponse()
{
	
	if(http.readyState == 4) 
	{
	    var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			release_freezing();
			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
			{
				parent.emailwindow.hide();
			}
	}
}
    </script>

</head>

<body>
<div align="center">
 <? echo load_freeze_divs ("../../../",$permission);  ?>

	<fieldset style="width:360px">
    <form id="accpoinfo_1" autocomplete="off">
        <table width="360" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
            <thead>
                <th>PO Number</th>
                <th>PO Quantity</th>
                <th></th>
            </thead>
            <tbody>
            <?
			$data_array=sql_select("select id,acc_po_no,acc_po_qty from wo_po_acc_po_info where po_break_down_id=$po_id and job_no='$txt_job_no' and status_active=1 and is_deleted=0");
			if(count($data_array)>0)
			{
				$i=1;
				foreach( $data_array as $row)
				{
			?>
                <tr class="general" id="tr_1">
                    <td align="center">
                    <input type="hidden" id="rowid_<? echo $i;?>" name="rowid_<? echo $i;?>" class="text_boxes" style="width:130px" value="<? echo $row[csf('id')] ; ?>" />
                    <input type="text" id="poNo_<? echo $i;?>" name="poNo_<? echo $i;?>" class="text_boxes" style="width:130px" value="<? echo $row[csf('acc_po_no')] ; ?>" readonly />
                    </td>
                    <td align="center"><input type="text" id="poQnty_<? echo $i;?>" name="poQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:120px" value="<? echo $row[csf('acc_po_qty')] ; ?>" readonly/></td>
                    <td width="70">
                        <input type="button" id="increase_<? echo $i;?>" name="increase_<? echo $i;?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i;?>)" />
                        <input type="button" id="decrease_<? echo $i;?>" name="decrease_<? echo $i;?>" style="width:30px" class="formbutton" value="-" onClick="fn_deletebreak_down_tr(<? echo $i;?>);" />
                    </td>
                </tr>
                <?
				$i++;
				}
			}
			else
			{
				?>
                 <tr class="general" id="tr_1">
                    <td align="center">
                    <input type="hidden" id="rowid_1" name="rowid_1" class="text_boxes" style="width:130px" value="" />
                    <input type="text" id="poNo_1" name="poNo_1" class="text_boxes" style="width:130px" value="" readonly />
                    </td>
                    <td align="center"><input type="text" id="poQnty_1" name="poQnty_1" class="text_boxes_numeric" style="width:120px" value="" readonly/></td>
                    <td width="70">
                        <input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(1)" />
                        <input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbutton" value="-" onClick="fn_deletebreak_down_tr(1);" />
                    </td>
                </tr>
                <?
			}
				?>
            </tbody>
        </table>
        <div align="center" style="margin-top:10px">
           <?
		   if(count($data_array)>0)
			{
			echo load_submit_buttons( $permission, "fnc_acc_po_info", 1,0 ,"reset_form('accpoinfo_1','','','','')",1) ; 
			}
			else
			{
			echo load_submit_buttons( $permission, "fnc_acc_po_info", 0,0 ,"reset_form('accpoinfo_1','','','','')",1) ; 
			}
		   ?>
            <input type="hidden" id="hid_po_id" value="<? echo $po_id; ?>" />
             <input type="hidden" id="txt_job_no" value="<? echo $txt_job_no; ?>" />
        </div>
        </form>
	</fieldset>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

// --------------------------------------------pp meeting popup-------------------------------------------------------------------------------

if ($action=="all_po_ppMeeting")
{
	echo load_html_head_contents("Actual PO Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);

?> 
	<script>
	var permission='<? echo $permission; ?>';
	function cope_pp_date(id)
	{
	 if (document.getElementById('cbx_cope_date').checked==true)
	 {	
	 var row_num = $('#tbl_list_pp_date tbody tr').length; 
	 var initial=id+1;
	 
	 for( j=initial; j<=row_num; j++)
	   $("#pp_meeting_date_"+j).val($("#pp_meeting_date_"+id).val())
	 }
		
	}
	
	
			
function fnc_pp_metting( operation )
{
	    var row_num = $('#tbl_list_pp_date tbody tr').length;  
	
		var data1='';
		for( var i=1; i<=row_num; i++)
		{
			data1+=get_submitted_data_string('pp_meeting_date_'+i+'*pp_order_id_'+i, "../../../",i);
		}
		
		
		var data="action=save_update_delete_pp_meeting&operation="+operation+'&total_row='+row_num+data1;
		
		freeze_window(operation);
		http.open("POST","woven_order_entry_controller_update.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_pp_metting_reponse;
}

function fnc_pp_metting_reponse()
{
	
	if(http.readyState == 4) 
	{
		//alert(http.responseText)
	    var reponse=trim(http.responseText).split('**');
		
			if (reponse[0].length>2) reponse[0]=10;
			release_freezing();
			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
			{
				parent.emailwindow.hide();
			}
	}
}
  
  function reset_pp_metting()
  {
	 var row_num = $('#tbl_list_pp_date tbody tr').length;  
	 
	   for( j=1; j<=row_num; j++)
	   $("#pp_meeting_date_"+j).val('');
  }
    </script>

</head>

<body>
<div align="center">
 <? echo load_freeze_divs ("../../../",$permission);  ?>

	<fieldset style="width:820px">
    <form id="accpoinfo_1" autocomplete="off">
        
        <table width="800" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table" id="">
         <tr>
               <td width="150" align="right" colspan="8" style=" padding-right:20px;">Copy <input type="checkbox" id="cbx_cope_date" name="cbx_cope_date" checked /></td>
         </tr>
        </table>
        <table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_pp_date">
            <thead>
                <th>SL No</th>
                <th>Order Status</th>
                <th>PO Number</th>
                <th>PO Recv. Date</th>
                <th>Ship Date</th>
                <th>Orgin. Ship Date</th>
                <th>PO Qnty</th>
                <th>PP Meeting Date</th>
            </thead>
            <tbody>
            <?
			 $sql= "select is_confirmed,po_number,po_received_date,pub_shipment_date,shipment_date,po_quantity,id,pp_meeting_date from  wo_po_break_down  where   status_active=1 and is_deleted=0 and job_no_mst='$update_id'  and is_confirmed=1"; //and is_confirmed=1
			$pp_metting_data=sql_select($sql);
			$pp_meeting_date="";
			  
				$i=1;
				foreach( $pp_metting_data as $row)
				{
				  $pp_meeting_date.=$row[csf('pp_meeting_date')];	
			?>
            <tr class="" id="tr_1">
              <td width="40" align="center"><? echo $i; ?>
              <input type="hidden" id="pp_order_id_<? echo $i;?>" name="pp_order_id_<? echo $i;?>"  value="<? echo $row[csf('id')]; ?>" />
              </td>
              <td width="100" align="center"><? echo $order_status[$row[csf('is_confirmed')]]; ?></td>
              <td width="150" align="center"><? echo $row[csf('po_number')]; ?></td>
              <td width="100" align="center"><? echo change_date_format($row[csf('po_received_date')]); ?></td>
              <td width="100" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
              <td width="100" align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
              <td width="100" align="right"><? echo $row[csf('po_quantity')]; ?></td>
              <td align="center" >
              <input type="text" id="pp_meeting_date_<? echo $i;?>" name="pp_meeting_date_<? echo $i;?>" style="width:80px"     onChange="cope_pp_date(<? echo $i; ?>)"class="datepicker" value="<? echo change_date_format($row[csf('pp_meeting_date')]); ?>" /></td>
            </tr>
                <?
				$i++;
				}
			
				?>
            </tbody>
        </table>
        <div align="center" style="margin-top:10px">
           <?
		   if($pp_meeting_date!="")
			   {
				echo load_submit_buttons( $permission, "fnc_pp_metting", 1,0 ,"reset_pp_metting()",1) ; 
			   }
			 else
			    {
				echo load_submit_buttons( $permission, "fnc_pp_metting", 0,0 ,"reset_pp_metting()",1) ; 
			   }
		   ?>
            <input type="hidden" id="hid_po_id" value="<? echo $po_id; ?>" />
             <input type="hidden" id="txt_job_no" value="<? echo $txt_job_no; ?>" />
        </div>
        </form>
	</fieldset>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}


if ($action=="bookingMeetingDate")
{
	echo load_html_head_contents("Actual PO Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

?> 
	<script>
	var permission='<? echo $permission; ?>';
			
function fnc_booking_metting( operation )
{
		
		var data="action=save_update_delete_booking_meeting&operation="+operation+get_submitted_data_string('booking_meeting_date*txt_job_no',"../../../");
		freeze_window(operation);
		http.open("POST","woven_order_entry_controller_update.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_booking_metting_reponse;
}

function fnc_booking_metting_reponse()
{
	
	if(http.readyState == 4) 
	{
		//alert(http.responseText)
	    var reponse=trim(http.responseText).split('**');
		
			if (reponse[0].length>2) reponse[0]=10;
			release_freezing();
			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
			{
				parent.emailwindow.hide();
			}
	}
}
  
  function reset_pp_metting()
  {
	   $("#booking_meeting_date").val('');
  }
   
    </script>

</head>

<body>
<div align="center">
 <? echo load_freeze_divs ("../../../",$permission);  ?>

	<fieldset style="width:820px">
    <form id="accpoinfo_1" autocomplete="off">
        
        <table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_pp_date">
            <thead>
                <th>SL No</th>
                <th>Company Name</th>
                <th>Job No</th>
                <th>Buyer Name</th>
                <th>Style Ref.</th>
                <th>Order Uom</th>
                <th>Job Qty</th>
                <th>Booking Meeting Date</th>
            </thead>
            <tbody>
            <?
			 //ALTER TABLE `wo_po_details_master`  ADD `booking_meeting_date` DATE NOT NULL AFTER `style_owner`
			 //ALTER TABLE LOGIC3RDVERSION.WO_PO_DETAILS_MASTER ADD (booking_meeting_date  DATE);
			 $sql= "select job_no,company_name,buyer_name,style_ref_no,job_quantity,order_uom,booking_meeting_date from  wo_po_details_master  where status_active=1 and is_deleted=0 and job_no='$update_id'"; //and is_confirmed=1
			$booking_metting_data=sql_select($sql);
			$pp_meeting_date="";
			  
				$i=1;
				foreach($booking_metting_data as $row)
				{
				  $booking_meeting_date=$row[csf('booking_meeting_date')];	
			?>
            <tr class="" id="tr_1">
              <td width="40" align="center"><? echo $i; ?></td>
              <td width="100" align="center"><? echo $comp[$row[csf('company_name')]]; ?></td>
              <td width="150" align="center"><? echo $row[csf('job_no')]; ?></td>
              <td width="100" align="center"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
              <td width="100" align="center"><? echo $row[csf('style_ref_no')]; ?></td>
              <td width="100" align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
              <td width="100" align="right"><? echo $row[csf('job_quantity')]; ?></td>
              <td align="center" >
              <input type="text" id="booking_meeting_date" name="booking_meeting_date" style="width:80px" class="datepicker" value="<? echo change_date_format($row[csf('booking_meeting_date')]); ?>" /></td>
            </tr>
                <?
				$i++;
				}
			
				?>
            </tbody>
        </table>
        <div align="center" style="margin-top:10px">
           <?
		   if($booking_meeting_date!="")
			   {
				echo load_submit_buttons( $permission, "fnc_booking_metting", 1,0 ,"reset_pp_metting()",1) ; 
			   }
			 else
			    {
				echo load_submit_buttons( $permission, "fnc_booking_metting", 0,0 ,"reset_pp_metting()",1) ; 
			   }
		   ?>
            <input type="hidden" id="txt_job_no" value="<? echo $update_id; ?>" />
        </div>
        </form>
	</fieldset>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}




if($action=="save_update_delete_pp_meeting")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		 $add_comma=0;
		 $field_array="pp_meeting_date*updated_by*update_date";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $metting_date="pp_meeting_date_".$i;
			 $order_id="pp_order_id_".$i;
            if(str_replace("'",'',$$metting_date)!="")
			 {
				 $id_arr[]=str_replace("'",'',$$order_id);
				 $data_array_up[str_replace("'",'',$$order_id)] =explode("*",("".$$metting_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			 }
		 }
		//echo "10**".bulk_update_sql_statement( "wo_po_break_down", "id", $field_array, $data_array_up, $id_arr );die;
		 $rID=execute_query(bulk_update_sql_statement( "wo_po_break_down", "id", $field_array, $data_array_up, $id_arr ));
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
	if ($operation==1)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 $add_comma=0;
		 $field_array="pp_meeting_date*updated_by*update_date";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $metting_date="pp_meeting_date_".$i;
			 $order_id="pp_order_id_".$i;
            if(str_replace("'",'',$$metting_date)!="")
			 {
				 $id_arr[]=str_replace("'",'',$$order_id);
				 $data_array_up[str_replace("'",'',$$order_id)] =explode("*",("".$$metting_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			 }
		 }
		//echo "10**".bulk_update_sql_statement( "wo_po_break_down", "id", $field_array, $data_array_up, $id_arr );die;
		 $rID=execute_query(bulk_update_sql_statement( "wo_po_break_down", "id", $field_array, $data_array_up, $id_arr ));
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1";
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
				echo "1";
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


if($action=="save_update_delete_booking_meeting")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$rID=execute_query("update wo_po_details_master set booking_meeting_date=$booking_meeting_date, updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where job_no=$txt_job_no");

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
	else if ($operation==1)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$rID=execute_query("update wo_po_details_master set booking_meeting_date=$booking_meeting_date, updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where job_no=$txt_job_no");

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




/*
if($action=="save_update_delete_pp_meeting")
{
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		echo "10**100";
		 $add_comma=0;
		 $field_array="pp_meeting_date,updated_by,update_date";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $metting_date="pp_meeting_date_".$i;
			 $order_id="pp_order_id_".$i;
			
            if(str_replace("'",'',$$metting_date)!="")
			 {
				 $id_arr[]=str_replace("'",'',$$order_id);
				 $data_array_up[str_replace("'",'',$$order_id)] =explode("*",("".$$metting_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			 }
		 }
		echo "10**".bulk_update_sql_statement( "wo_po_break_down", "id", $field_array, $data_array_up, $id_arr );die;
		 $rID=execute_query(bulk_update_sql_statement( "wo_po_break_down", "id", $field_array, $data_array_up, $id_arr ));
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
			if($rID){
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
	if ($operation==1)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}

		 $add_comma=0;
		 $field_array="pp_meeting_date,updated_by,update_date";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $poNo="poNo_".$i;
			 $poQnty="poQnty_".$i;
			 $rowid="rowid_".$i;
             if(str_replace("'",'',$$rowid)=="")
			 {
				if ($add_comma!=0) $data_array .=",";
				$data_array .="(".$id.",'".$txt_job_no."','".$poid."',".$$poNo.",".$$poQnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$add_comma++;
				$id=$id+1;
			 }
		 }
		// echo bulk_update_sql_statement( "wo_po_acc_po_info", "id", $field_array_up, $data_array_up, $id_arr );
		 $rID=execute_query(bulk_update_sql_statement( "wo_po_acc_po_info", "id", $field_array_up, $data_array_up, $id_arr ));
		 if($data_array !="")
		 {
			$rID=sql_insert("wo_po_acc_po_info",$field_array,$data_array,1);
		 }
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1";
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
				echo "1";
			}
			else{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
	if ($operation==2)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}

		 $field_array_up="status_active*is_deleted*updated_by*update_date";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $poNo="poNo_".$i;
			 $poQnty="poQnty_".$i;
			 $rowid="rowid_".$i;
			 if(str_replace("'",'',$$rowid)!="")
			 {
				 $id_arr[]=str_replace("'",'',$$rowid);
				 $data_array_up[str_replace("'",'',$$rowid)] =explode("*",("0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			 }
             
		 }
		// echo bulk_update_sql_statement( "wo_po_acc_po_info", "id", $field_array_up, $data_array_up, $id_arr );
		$rID=execute_query(bulk_update_sql_statement( "wo_po_acc_po_info", "id", $field_array_up, $data_array_up, $id_arr ));
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "2";
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
				echo "2";
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
*/





if($action=="save_update_delete_accpoinfo")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}		
		 $id=return_next_id( "id", "wo_po_acc_po_info", 1 ) ;
		 $field_array="id,job_no,po_break_down_id,acc_po_no,acc_po_qty,inserted_by,insert_date";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $poNo="poNo_".$i;
			 $poQnty="poQnty_".$i;
			 $rowid="rowid_".$i;

		    $id_arr[]=str_replace("'",'',$$rowid);
			$data_array_up[str_replace("'",'',$$rowid)] =explode("*",("".$$poNo."*".$$poQnty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		 }
		$rID=sql_insert("wo_po_acc_po_info",$field_array,$data_array,1);
		check_table_status( $_SESSION['menu_id'],0);
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
	if ($operation==1)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}

		 $add_comma=0;
		 $id=return_next_id( "id", "wo_po_acc_po_info", 1 ) ;
		 $field_array="id,job_no,po_break_down_id,acc_po_no,acc_po_qty,inserted_by,insert_date";
		 $field_array_up="acc_po_no*acc_po_qty*updated_by*update_date";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $poNo="poNo_".$i;
			 $poQnty="poQnty_".$i;
			 $rowid="rowid_".$i;
			 if(str_replace("'",'',$$rowid)!="")
			 {
				 $id_arr[]=str_replace("'",'',$$rowid);
				 $data_array_up[str_replace("'",'',$$rowid)] =explode("*",("".$$poNo."*".$$poQnty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			 }
             if(str_replace("'",'',$$rowid)=="")
			 {
				if ($add_comma!=0) $data_array .=",";
				$data_array .="(".$id.",'".$txt_job_no."','".$poid."',".$$poNo.",".$$poQnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$add_comma++;
				$id=$id+1;
			 }
		 }
		// echo bulk_update_sql_statement( "wo_po_acc_po_info", "id", $field_array_up, $data_array_up, $id_arr );
		 $rID=execute_query(bulk_update_sql_statement( "wo_po_acc_po_info", "id", $field_array_up, $data_array_up, $id_arr ));
		 if($data_array !="")
		 {
			$rID=sql_insert("wo_po_acc_po_info",$field_array,$data_array,1);
		 }
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1";
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
				echo "1";
			}
			else{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
	if ($operation==2)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}

		 $field_array_up="status_active*is_deleted*updated_by*update_date";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $poNo="poNo_".$i;
			 $poQnty="poQnty_".$i;
			 $rowid="rowid_".$i;
			 if(str_replace("'",'',$$rowid)!="")
			 {
				 $id_arr[]=str_replace("'",'',$$rowid);
				 $data_array_up[str_replace("'",'',$$rowid)] =explode("*",("0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			 }
             
		 }
		// echo bulk_update_sql_statement( "wo_po_acc_po_info", "id", $field_array_up, $data_array_up, $id_arr );
		$rID=execute_query(bulk_update_sql_statement( "wo_po_acc_po_info", "id", $field_array_up, $data_array_up, $id_arr ));
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "2";
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
				echo "2";
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



if($action=="delete_row")
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	$rID=execute_query("update wo_po_acc_po_info set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where id=$data");
	if($db_type==0)
	{
		if($rID ){
		mysql_query("COMMIT");  
		echo "2";
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
		echo "2";
		}
		else{
		oci_rollback($con);
		echo "10";
		}
	}
	disconnect($con);
	die;
}
?>
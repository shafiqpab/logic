<? 
/*-------------------------------------------- Comments
Version (MySql)          :  V2
Version (Oracle)         :  V1
Converted by             :  MONZU
Purpose			         :  This form will create Trims Booking
Functionality	         :	
JS Functions	         :
Created by		         : MONZU 
Creation date 	         : 27-12-2012
Requirment Client        : Fakir Apperels
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :              
DB Script                : 
Updated by 		         : Aziz (TNA Information in Print Booking3 ) 
Update date		         : 28-8-15
QC Performed BY	         :		
QC Date			         :	
Comments		         : 
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
require_once('../../../includes/class3/class.conditions.php');
require_once('../../../includes/class3/class.reports.php');
require_once('../../../includes/class3/class.trims.php');
//require_once('../../../includes/class3/class.yarns.php');
/*require_once('../../../includes/class3/class.conversions.php');
require_once('../../../includes/class3/class.emblishments.php');
require_once('../../../includes/class3/class.commisions.php');
require_once('../../../includes/class3/class.commercials.php');
require_once('../../../includes/class3/class.others.php');
require_once('../../../includes/class3/class.trims.php');
require_once('../../../includes/class3/class.fabrics.php');
require_once('../../../includes/class3/class.washes.php');*/
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

//---------------------------------------------------- Start---------------------------------------------------------------------------
$po_number=return_library_array("select id,po_number from wo_po_break_down", "id", "po_number");
$color_library=return_library_array("select id,color_name from lib_color", "id", "color_name");
$size_library=return_library_array("select id,size_name from lib_size", "id", "size_name");
$company_library=return_library_array("select id,company_name from lib_company", "id", "company_name");
$buyer_arr=return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');
$trim_group= return_library_array("select id, item_name from lib_item_group",'id','item_name');
$country_library=return_library_array("select id,country_name from lib_country", "id", "country_name");
$country_library_short=return_library_array("select id,short_name from lib_country", "id", "short_name");

function load_drop_down_buyer($data)
{
	global $buyer_cond;	
	$drop_down_buyer= create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected,"","");	
return $drop_down_buyer;
}

if ($action=="load_drop_down_buyer")
{
	echo $action($data);
	/*echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected,"","");*/
	
}

if ($action=="load_drop_down_supplier")
{ 
	//echo "dsdsd";
	
	if($data==5 || $data==3){
	   echo create_drop_down( "cbo_supplier_name", 172, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Company --", "", "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/short_fabric_booking_controller');",0,"" );
	}
	else{
	   echo create_drop_down( "cbo_supplier_name", 172, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and b.party_type=4 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value, 'load_drop_down_attention', 'requires/trims_booking_controller');","");

	}
	
	exit();
}

if($action=="load_drop_down_attention")
{
	$supplier_name=return_field_value("contact_person","lib_supplier","id ='".$data."' and is_deleted=0 and status_active=1");
	echo "document.getElementById('txt_attention').value = '".$supplier_name."';\n";
	exit();	
}


if ($action=="load_drop_down_buyer_pop")
{ 
	$data_buyer=explode('_',$data);
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data_buyer[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --",$data_buyer[1],"",1);
	
}

function populate_variable_setting_data($data){
	$data_array=sql_select("select exeed_budge_qty,exeed_budge_amount,amount_exceed_level from variable_order_tracking where company_name='$data' and item_category_id=4 and variable_list=26 and status_active=1 and is_deleted=0");
	foreach ($data_array as $row){
		echo "document.getElementById('exeed_budge_qty').value = '".$row[csf("exeed_budge_qty")]."';\n";  
		echo "document.getElementById('exeed_budge_amount').value = '".$row[csf("exeed_budge_amount")]."';\n"; 
		echo "document.getElementById('amount_exceed_level').value = '".$row[csf("amount_exceed_level")]."';\n"; 
	}
	$buyer_dropdown=load_drop_down_buyer($data);
	echo "document.getElementById('buyer_td').innerHTML = '".$buyer_dropdown."';\n";
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id in(5,6) and is_deleted=0 and status_active=1");
	echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
	echo "print_report_button_setting('".$print_report_format."');\n";	
	$sql_result=sql_select("select tna_integrated from variable_order_tracking where company_name='$data' and variable_list=14 and status_active=1 and is_deleted=0");
	$maintain_setting=$sql_result[0][csf('tna_integrated')];
	if($maintain_setting==1) {
		echo "document.getElementById('lib_tna_intregrate').value = '1';\n"; 
	}
	else {
		echo "document.getElementById('lib_tna_intregrate').value = '0';\n"; 
	}
}

if ($action=="populate_variable_setting_data")
{
	$action($data);
	/*$data_array=sql_select("select exeed_budge_qty,exeed_budge_amount,amount_exceed_level from variable_order_tracking where company_name='$data' and item_category_id=4 and variable_list=26 and status_active=1 and is_deleted=0");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('exeed_budge_qty').value = '".$row[csf("exeed_budge_qty")]."';\n";  
		echo "document.getElementById('exeed_budge_amount').value = '".$row[csf("exeed_budge_amount")]."';\n"; 
		echo "document.getElementById('amount_exceed_level').value = '".$row[csf("amount_exceed_level")]."';\n"; 
	}*/
}

function load_drop_down_trim_precost_id($data)
{
	    $data=explode("_",$data);
		$trim_group= return_library_array("select id, item_name from lib_item_group",'id','item_name');
		$trim_group_array=array();
		if($data[1]>0) $nominated_sup_cond=" and (nominated_supp='$data[1]' or nominated_supp = 0)"; else $nominated_sup_cond="";
		$sql_data=sql_select("select id,trim_group,cons_dzn_gmts from wo_pre_cost_trim_cost_dtls where job_no='$data[0]' $nominated_sup_cond  and is_deleted=0  and status_active=1");
		foreach($sql_data as $row)
		{
			$trim_group_array[$row[csf('id')]]=$trim_group[$row[csf('trim_group')]]." (".$row[csf('cons_dzn_gmts')]." )";
		}
		$trim_precost_id= create_drop_down( "cbo_trim_precost_id", 155, $trim_group_array, "",1," -- Select Item --", "", "set_precost_data(this.value,1);fnc_generate_booking()","","" );
		return  $trim_precost_id;
}

if ($action=="load_drop_down_trim_precost_id")
{
	echo  $action($data);
	/*$trim_group_array=array();
	$sql_data=sql_select("select id,trim_group,cons_dzn_gmts from wo_pre_cost_trim_cost_dtls where job_no='$data' and is_deleted=0  and status_active=1");
	foreach($sql_data as $row)
	{
		$trim_group_array[$row[csf('id')]]=$trim_group[$row[csf('trim_group')]]." (".$row[csf('cons_dzn_gmts')]." )";
	}
	echo create_drop_down( "cbo_trim_precost_id", 155, $trim_group_array, "",1," -- Select Item --", "", "set_precost_data(this.value,1);fnc_generate_booking()","","" );*/
}

function load_drop_down_gmt_item($data,$garments_item)
{
    $gmts_item_id="";
	$drop_down_gmt_item="";
	$sql_data=sql_select("select gmts_item_id from wo_po_details_master where job_no='$data' and is_deleted=0  and status_active=1");
	foreach($sql_data as $row)
	{
		$gmts_item_id=$row[csf('gmts_item_id')];
	}
	$gmts_item_arr=explode($gmts_item_id);
	if(count($gmts_item_arr)>1)
	{
		$drop_down_gmt_item= create_drop_down( "cbo_gmt_item_id", 150, $garments_item,"", 1, "-- Select Item --", $selected, "" ,"",$gmts_item_id); 
	}
	else
	{
		$drop_down_gmt_item= create_drop_down( "cbo_gmt_item_id", 150, $garments_item,"", "", "", $selected, "" ,"",$gmts_item_id); 
	}	
	return $drop_down_gmt_item;
}

if ($action=="load_drop_down_gmt_item")
{
	echo $action($data,$garments_item);
	/*$gmts_item_id="";
	$sql_data=sql_select("select gmts_item_id from wo_po_details_master where job_no='$data' and is_deleted=0  and status_active=1");
	foreach($sql_data as $row)
	{
		$gmts_item_id=$row[csf('gmts_item_id')];
	}
	$gmts_item_arr=explode($gmts_item_id);
	if(count($gmts_item_arr)>1)
	{
		echo create_drop_down( "cbo_gmt_item_id", 150, $garments_item,"", 1, "-- Select Item --", $selected, "" ,"",$gmts_item_id); 
	}
	else
	{
		echo create_drop_down( "cbo_gmt_item_id", 150, $garments_item,"", "", "", $selected, "" ,"",$gmts_item_id); 
	}*/
}


if ($action=="order_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $cbo_buyer_name;
?>
	<script>
	function set_checkvalue()
	{
		if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
		else document.getElementById('chk_job_wo_po').value=0;
	}
	
	function js_set_value( data )
	{
		var data=data.split("_");
		document.getElementById('selected_job').value=data[0];
		document.getElementById('po_id').value=data[1];
		document.getElementById('po_no').value=data[2];
		parent.emailwindow.hide();
	}
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="1260" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                    <thead> 
                    <tr>
                     <th width="150" colspan="4"> </th>
                            <th>
                              <?
                               echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" );
                              ?>
                            </th>
                          <th width="150" colspan="4"> </th>
                    </tr>  
                    <tr>              	 
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="80">File No</th>
                        <th width="80">Ref. No</th>
                        <th width="100">Job No</th>
                        <th width="100">Style Ref </th>
                        <th width="150">Order No</th>
                        <th width="200">Date Range</th>
                        <th>
                        <input type="hidden" value="0"  id="chk_job_wo_po"><!--Job Without PO onClick="set_checkvalue()"-->
                        </th> 
                        </tr>          
                    </thead>
        			<tr>
                    	<td> 
                        <input type="hidden" id="selected_job">
                        <input type="hidden" id="po_id">
                        <input type="hidden" id="buyer_id" value="<? echo $cbo_buyer_name ?>">
                        <input type="hidden" id="po_no">
                        <input type="hidden" id="garments_nature" value="<? echo $garments_nature; ?>">
							<? 
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down( 'trims_booking_controller', this.value+'_'+document.getElementById('buyer_id').value, 'load_drop_down_buyer_pop', 'buyer_td' );",1,"" );
							?>
                    </td>
                   	<td id="buyer_td">
                     <? 
						echo create_drop_down( "cbo_buyer_name", 172, $buyer_arr,'', 1, "-- Select Buyer --", $cbo_buyer_name,"",1);
					?>	</td>
                     <td><input name="txt_file" id="txt_file" class="text_boxes" style="width:80px"></td>
                     <td><input name="txt_ref" id="txt_ref" class="text_boxes" style="width:80px"></td>
                     <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:100px"></td>
                     <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px"></td>
                     <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:150px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('garments_nature').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_file').value+'_'+document.getElementById('txt_ref').value, 'create_po_search_list_view', 'search_div', 'trims_booking_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        		</tr>
             </table>
          </td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle">
            <? 
			echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
			?>
			<? 
			echo load_month_buttons();  
			?>
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
	
	$data=explode('_',$data);
	
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else { echo "Please Select Buyer First."; die; }
	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[7]";
	if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";
	$job_cond="";
	$order_cond=""; 
	$style_cond="";
	$file_cond="";
	$ref_cond="";
	if($data[10]==1)
	{
	if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num='$data[6]' $year_cond"; //else  $job_cond=""; 
	if (trim($data[8])!="") $style_cond=" and a.style_ref_no ='$data[8]'"; //else  $style_cond=""; 
	if (str_replace("'","",$data[9])!="") $order_cond=" and b.po_number = '$data[9]'  "; //else  $order_cond="";
	if (str_replace("'","",$data[11])!="") $file_cond=" and b.file_no in($data[11])  ";
	if (str_replace("'","",$data[12])!="") $ref_cond=" and b.grouping ='$data[12]'  "; 
	}
	//echo $file_cond.'='.$ref_cond;
	if($data[10]==2)
	{
	if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '$data[6]%' $year_cond"; //else  $job_cond=""; 
	if (trim($data[8])!="") $style_cond=" and a.style_ref_no like '$data[8]%'  "; //else  $style_cond=""; 
	if (str_replace("'","",$data[9])!="") $order_cond=" and b.po_number like '$data[9]%'  "; //else  $order_cond=""; 
	if (str_replace("'","",$data[11])!="") $file_cond=" and b.file_no in($data[11])  ";
	if (str_replace("'","",$data[12])!="") $ref_cond=" and b.grouping ='$data[12]'  "; 
	}
	if($data[10]==3)
	{
	if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]' $year_cond"; //else  $job_cond=""; 
	if (trim($data[8])!="") $style_cond=" and a.style_ref_no like '%$data[8]'"; //else  $style_cond=""; 
	if (str_replace("'","",$data[9])!="") $order_cond=" and b.po_number like '%$data[9]'  "; //else  $order_cond="";
	if (str_replace("'","",$data[11])!="") $file_cond=" and b.file_no in($data[11])  ";
	if (str_replace("'","",$data[12])!="") $ref_cond=" and b.grouping ='$data[12]'  ";  
	}
	if($data[10]==4 || $data[10]==0)
	{
	if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]%' $year_cond "; //else  $job_cond=""; 
	if (trim($data[8])!="") $style_cond=" and a.style_ref_no like '%$data[8]%'"; //else  $style_cond=""; 
	if (str_replace("'","",$data[9])!="") $order_cond=" and b.po_number like '%$data[9]%'  "; //else  $order_cond="";
	if (str_replace("'","",$data[11])!="") $file_cond=" and b.file_no in($data[11])  ";
	if (str_replace("'","",$data[12])!="") $ref_cond=" and b.grouping ='$data[12]'  ";  
	}
	
	if($db_type==0)
	{
	if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	if($db_type==2)
	{
	if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (4=>$comp,5=>$buyer_arr,11=>$item_category,12=>$yes_no);
	if ($data[2]==0)
	{
		if($db_type==0)
		{
	 	$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.file_no,b.grouping,b.po_quantity,b.shipment_date,a.garments_nature,SUBSTRING_INDEX(a.insert_date, '-', 1) as year,b.id,c.approved from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.approved=1 and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer $job_cond $file_cond $ref_cond $order_cond $style_cond order  by a.job_no";
		}
		if($db_type==2)
		{
	 	 $sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.file_no,b.grouping,b.po_quantity,b.shipment_date,a.garments_nature,to_char(a.insert_date,'YYYY') as year,b.id,c.approved from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.approved=1 and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer $job_cond $file_cond $ref_cond $order_cond $style_cond order by a.job_no";
		}
		
		 echo  create_list_view("list_view", "Job No,File No,Ref. No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date,Gmts Nature,Approved", "50,80,80,60,120,100,100,90,90,90,80,80,50","1160","320",0, $sql , "js_set_value", "job_no,id,po_number", "", 1, "0,0,0,0,company_name,buyer_name,0,0,0,0,0,garments_nature,approved", $arr , "job_no_prefix_num,file_no,grouping,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date,garments_nature,approved", "",'','0,0,0,0,0,0,0,1,0,1,3,0,0');
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
		echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Gmts Nature", "90,80,120,100,100,90","1000","320",0, $sql , "", "", "", 1, "0,0,company_name,buyer_name,0,garments_nature", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,garments_nature", "",'','0,0,0,0,0,0');
	}
} 


/*if ($action=="populate_data_from_search_popup")
{
	$data_array=sql_select("select job_no,company_name,buyer_name from wo_po_details_master where job_no='$data'");
	foreach ($data_array as $row)
	{
		echo "load_drop_down( 'requires/trims_booking_controller', '".$row[csf("company_name")]."', 'load_drop_down_buyer', 'buyer_td' );\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";  
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n"; 
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n"; 
		echo " $('#cbo_company_name').attr('disabled',true);\n"; 
		echo " $('#cbo_buyer_name').attr('disabled',true);\n"; 
	}
}*/


function set_precost_data($data)
{
	$data=explode("_",$data);
	if($data[2]=="") $txt_country_cond=""; else $txt_country_cond ="and c.country_id in ($data[2])";
	$selected_country_array=explode(',',$data[2]);
	// With Gmt Item
	/*$sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$data[1]' and c.item_number_id=$data[3] $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty,c.item_number_id ");*/
	$sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$data[1]' $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty ");//and c.item_number_id=$data[3]
	
	list($sql_po_qty_row)=$sql_po_qty;
	$po_qty=$sql_po_qty_row[csf('order_quantity_set')];
	
	$sql_data=sql_select("select a.id, a.job_no, a.trim_group, a.cons_dzn_gmts, a.rate, a.amount, a.description, a.brand_sup_ref, b.cons, b.country_id from wo_pre_cost_trim_cost_dtls a , wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id and  a.id='$data[0]' and b.po_break_down_id='$data[1]' and a.is_deleted=0  and a.status_active=1");
	foreach($sql_data as $row)
	{
		if($row[csf('country_id')]==0) $txt_country_cond1="";
		else $txt_country_cond1 ="and c.country_id in (".$row[csf('country_id')].")";
		
		$sql_po_qty1=sql_select("select sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$data[1]' $txt_country_cond1  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty");//and c.item_number_id=$data[3]
		
		list($sql_po_qty_row1)=$sql_po_qty1;
		$po_qty1=$sql_po_qty_row1[csf('order_quantity_set')];
		
		$cons_uom=return_field_value("order_uom", "lib_item_group", "id=".$row[csf('trim_group')]."");
		$conversion_factor=return_field_value("conversion_factor", "lib_item_group", "id=".$row[csf('trim_group')]."");
		$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$row[csf('job_no')]."'");
		$exchange_rate=return_field_value("exchange_rate", "wo_pre_cost_mst", "job_no='".$row[csf('job_no')]."'");
		$currency_id_pre_cost=return_field_value("currency_id", "wo_po_details_master", "job_no='".$row[csf('job_no')]."'");
		$currency_id_this_booking=$data[4];
		
		if($currency_id_pre_cost==$currency_id_this_booking) $exchange_rate=1;	
		
		if($costing_per==1) $costing_per_qty=12;
		else if($costing_per==2) $costing_per_qty=1;
		else if($costing_per==3) $costing_per_qty=24;
		else if($costing_per==4) $costing_per_qty=36;
		else if($costing_per==5) $costing_per_qty=48;
		
		if($data[5]==1)
		{
			if($row[csf('country_id')] !=0)
			{
				$country_array=explode(",",$row[csf('country_id')]);
				$result=array_diff($selected_country_array,$country_array);
				if(count($result)>0)
				{
					$join_result="";
					for($i=0;$i<count($result);$i++)
					{
						$join_result.=$country_library[$result[$i]].",";
					}
					echo 'alert("This Item is not applicable for  '.$join_result.'\n Please review your country selection");';
					echo "document.getElementById('txt_req_quantity').value = '';\n";
					echo "document.getElementById('txt_req_amt').value = '';\n";
					echo "document.getElementById('cbo_trim_precost_id').value = '0';\n";  
					echo "document.getElementById('txt_trim_group_id').value = '';\n";
					echo "document.getElementById('txt_exchange_rate_dtls').value = '';\n";
					die;
				}
			}
			
			$req_qnty_cons_uom=def_number_format(($row[csf('cons')]*($po_qty1/$costing_per_qty)),5,"");
			$req_amount_cons_uom=def_number_format($req_qnty_cons_uom*$row[csf("rate")],5,"");
			$req_qnty=def_number_format(($row[csf('cons')]*($po_qty/$costing_per_qty))/$conversion_factor,5,"");
			$txt_avg_price=def_number_format(($row[csf("rate")]*$conversion_factor)*$exchange_rate,5,"");
			
			echo "document.getElementById('txt_trim_group_id').value = '".$row[csf("trim_group")]."';\n";  
			echo "document.getElementById('cbo_uom').value = '".$cons_uom."';\n"; 
			echo "document.getElementById('txt_req_quantity').value = '".$req_qnty."';\n";
			echo "document.getElementById('txt_req_amt').value = '".$row[csf("amount")]."';\n";
			echo "document.getElementById('txt_avg_price').value = '".$txt_avg_price."';\n";
			echo "document.getElementById('txt_pre_des').value = '".$row[csf("description")]."';\n";  
			echo "document.getElementById('txt_pre_brand_sup').value = '".$row[csf("brand_sup_ref")]."';\n";  
			echo "document.getElementById('txt_exchange_rate_dtls').value = '".$exchange_rate."';\n"; 
			echo "$('#txt_req_quantity').attr('req_qnty_cons_uom', ".$req_qnty_cons_uom.");\n";
			echo "$('#txt_req_quantity').attr('conversion_factor', ".$conversion_factor.");\n";
			echo "$('#txt_req_quantity').attr('req_amount_cons_uom', ".$req_amount_cons_uom.");\n";
		}
		if($data[5]==2)
		{
			$req_qnty_cons_uom=def_number_format(($row[csf('cons')]*($po_qty1/$costing_per_qty)),5,"");
			$req_amount_cons_uom=def_number_format($req_qnty_cons_uom*$row[csf("rate")],5,"");
			$req_qnty=def_number_format(($row[csf('cons')]*($po_qty/$costing_per_qty))/$conversion_factor,5,"");
			$txt_avg_price=def_number_format(($row[csf("rate")]*$conversion_factor)*$exchange_rate,5,"");
			echo "document.getElementById('txt_req_amt').value = '".$row[csf("amount")]."';\n";
			echo "document.getElementById('txt_trim_group_id').value = '".$row[csf("trim_group")]."';\n"; 
			echo "$('#txt_req_quantity').attr('req_qnty_cons_uom', ".$req_qnty_cons_uom.");\n";
			echo "$('#txt_req_quantity').attr('conversion_factor', ".$conversion_factor.");\n";
			echo "$('#txt_req_quantity').attr('req_amount_cons_uom', ".$req_amount_cons_uom.");\n";	
		}
	}
}


if ($action=="set_precost_data")
{
	$data=explode("_",$data);
	if($data[6]==2)
	{
		$cons_uom=return_field_value("order_uom", "lib_item_group", "id=".$data[0]."");
		$conversion_factor=return_field_value("conversion_factor", "lib_item_group", "id=".$data[0]."");
		$sql_data=sql_select("select a.job_no, c.exchange_rate from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no  and  b.id='$data[1]'  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by a.job_no,c.exchange_rate");
		foreach($sql_data as $row_data)
		{
			$exchange_rate=$row_data[csf('exchange_rate')];
			$currency_id_pre_cost=return_field_value("currency_id", "wo_po_details_master", "job_no='".$row_data[csf('job_no')]."'");
			$currency_id_this_booking=$data[4];
			if($currency_id_pre_cost==$currency_id_this_booking) $exchange_rate=1;	
			echo "document.getElementById('txt_trim_group_id').value = '".$data[0]."';\n";  
			echo "document.getElementById('cbo_uom').value = '".$cons_uom."';\n"; 
			//echo "document.getElementById('txt_req_quantity').value = '".$req_qnty."';\n";
			//echo "document.getElementById('txt_avg_price').value = '".$txt_avg_price."';\n";
			//echo "document.getElementById('txt_pre_des').value = '".$row[csf("description")]."';\n";  
			//echo "document.getElementById('txt_pre_brand_sup').value = '".$row[csf("brand_sup_ref")]."';\n";  
			echo "document.getElementById('txt_exchange_rate_dtls').value = '".$exchange_rate."';\n"; 
			echo "$('#txt_req_quantity').attr('conversion_factor', ".$conversion_factor.");\n";
		}
	}
	if($data[6]==1)
	{
		if($data[2]=="") $txt_country_cond="";
		else $txt_country_cond ="and c.country_id in ($data[2])";
		$selected_country_array=explode(',',$data[2]);
		// With Gmt Item
		/*$sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$data[1]' and c.item_number_id=$data[3] $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty,c.item_number_id ");*/
		$sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$data[1]' $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty ");//and c.item_number_id=$data[3]
		
		list($sql_po_qty_row)=$sql_po_qty;
		$po_qty=$sql_po_qty_row[csf('order_quantity_set')];
		
		$sql_data=sql_select("select a.id, a.job_no, a.trim_group, a.cons_dzn_gmts, a.rate, a.amount, a.description, a.brand_sup_ref, b.cons, b.country_id from wo_pre_cost_trim_cost_dtls a , wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id and  a.id='$data[0]' and b.po_break_down_id='$data[1]' and a.is_deleted=0  and a.status_active=1");
		foreach($sql_data as $row)
		{
			if($row[csf('country_id')]==0) $txt_country_cond1="";
			else $txt_country_cond1 ="and c.country_id in (".$row[csf('country_id')].")";
			
			$sql_po_qty1=sql_select("select sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$data[1]' $txt_country_cond1  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty ");//and c.item_number_id=$data[3]
			
			list($sql_po_qty_row1)=$sql_po_qty1;
			$po_qty1=$sql_po_qty_row1[csf('order_quantity_set')];
			
			$cons_uom=return_field_value("order_uom", "lib_item_group", "id=".$row[csf('trim_group')]."");
			$conversion_factor=return_field_value("conversion_factor", "lib_item_group", "id=".$row[csf('trim_group')]."");
			$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$row[csf('job_no')]."'");
			$exchange_rate=return_field_value("exchange_rate", "wo_pre_cost_mst", "job_no='".$row[csf('job_no')]."'");
			$currency_id_pre_cost=return_field_value("currency_id", "wo_po_details_master", "job_no='".$row[csf('job_no')]."'");
			$currency_id_this_booking=$data[4];
			if($currency_id_pre_cost==$currency_id_this_booking) $exchange_rate=1;	
			
			if($costing_per==1) $costing_per_qty=12;
			else if($costing_per==2) $costing_per_qty=1;
			else if($costing_per==3) $costing_per_qty=24;
			else if($costing_per==4) $costing_per_qty=36;
			else if($costing_per==5) $costing_per_qty=48;
			
			if($data[5]==1)
			{
				if($row[csf('country_id')] !=0)
				{
					$country_array=explode(",",$row[csf('country_id')]);
					$result=array_diff($selected_country_array,$country_array);
					if(count($result)>0)
					{
						$join_result="";
						for($i=0;$i<count($result);$i++)
						{
							$join_result.=$country_library[$result[$i]].",";
						}
						echo 'alert("This Item is not applicable for  '.$join_result.'\n Please review your country selection");';
						echo "document.getElementById('txt_req_quantity').value = '';\n";
						echo "document.getElementById('txt_req_amt').value = '';\n";
						echo "document.getElementById('cbo_trim_precost_id').value = '0';\n";  
						echo "document.getElementById('txt_trim_group_id').value = '';\n";
						echo "document.getElementById('txt_exchange_rate_dtls').value = '';\n";
						die;
					}
				}
				$req_qnty_cons_uom=def_number_format(($row[csf('cons')]*($po_qty1/$costing_per_qty)),5,"");
				$req_amount_cons_uom=def_number_format($req_qnty_cons_uom*$row[csf("rate")],5,"");
				
				$req_qnty=def_number_format(($row[csf('cons')]*($po_qty/$costing_per_qty))/$conversion_factor,5,"");
				$txt_avg_price=def_number_format(($row[csf("rate")]*$conversion_factor)*$exchange_rate,5,"");
				
				echo "document.getElementById('txt_trim_group_id').value = '".$row[csf("trim_group")]."';\n";  
				echo "document.getElementById('cbo_uom').value = '".$cons_uom."';\n"; 
				echo "document.getElementById('txt_req_quantity').value = '".$req_qnty."';\n";
				echo "document.getElementById('txt_req_amt').value = '".$row[csf("amount")]."';\n";
				echo "document.getElementById('txt_avg_price').value = '".$txt_avg_price."';\n";
				echo "document.getElementById('txt_pre_des').value = '".$row[csf("description")]."';\n";  
				echo "document.getElementById('txt_pre_brand_sup').value = '".$row[csf("brand_sup_ref")]."';\n";  
				echo "document.getElementById('txt_exchange_rate_dtls').value = '".$exchange_rate."';\n"; 
				
				echo "$('#txt_req_quantity').attr('req_qnty_cons_uom', ".$req_qnty_cons_uom.");\n";
				echo "$('#txt_req_quantity').attr('conversion_factor', ".$conversion_factor.");\n";
				echo "$('#txt_req_quantity').attr('req_amount_cons_uom', ".$req_amount_cons_uom.");\n";
			}
			if($data[5]==2)
			{
				$req_qnty_cons_uom=def_number_format(($row[csf('cons')]*($po_qty1/$costing_per_qty)),5,"");
				$req_amount_cons_uom=def_number_format($req_qnty_cons_uom*$row[csf("rate")],5,"");
				
				$req_qnty=def_number_format(($row[csf('cons')]*($po_qty/$costing_per_qty))/$conversion_factor,5,"");
				$txt_avg_price=def_number_format(($row[csf("rate")]*$conversion_factor)*$exchange_rate,5,"");
				echo "document.getElementById('txt_req_amt').value = '".$row[csf("amount")]."';\n";
				echo "$('#txt_req_quantity').attr('req_qnty_cons_uom', ".$req_qnty_cons_uom.");\n";
				echo "$('#txt_req_quantity').attr('conversion_factor', ".$conversion_factor.");\n";
				echo "$('#txt_req_quantity').attr('req_amount_cons_uom', ".$req_amount_cons_uom.");\n";	
			}
		}
	}
}


function set_delivery_date_from_tna($poid, $save_update_mode, $company_id, $pay_mode, $trim_type)
{
	if($trim_type==1) $task_num='70'; else $task_num='71';
	$task_finish_date='';
	$tnasql=sql_select("select task_finish_date from tna_process_mst where task_number=$task_num and po_number_id=$poid and is_deleted= 0 and status_active=1");
	foreach($tnasql as $tnarow){
		$task_finish_date=$tnarow[csf('task_finish_date')];
	}
	
	$sql_tna_lib=sql_select("select b.date_calc, b.day_status from lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id=$company_id and b.date_calc='$task_finish_date' and a.status_active=1 and a.is_deleted=0");
	
	$date_calc=$sql_tna_lib[0][csf("date_calc")];
	$day_status=$sql_tna_lib[0][csf("day_status")];
	
	if($day_status==2)
	{
		$task_finish_date=return_field_value("max(b.date_calc) as  date_calc ", " lib_capacity_calc_mst a, lib_capacity_calc_dtls b "," a.id=b.mst_id and a.comapny_id=$company_id and b.date_calc<'$task_finish_date' and a.status_active=1 and a.is_deleted=0 and b.day_status=1","date_calc");
	}
	else
	{
		$task_finish_date=$task_finish_date;
	}
	
	
	if($save_update_mode=='save')
	{
		if($task_finish_date !='')
		{
			echo "document.getElementById('txt_delevary_date').value = '".change_date_format($task_finish_date,'dd-mm-yyyy','-')."';\n";
			echo "document.getElementById('txt_tna_date').value = '".change_date_format($task_finish_date,'dd-mm-yyyy','-')."';\n";
		}
		else
		{
			echo "document.getElementById('txt_delevary_date').value = '';\n";
			echo "document.getElementById('txt_tna_date').value = '';\n";
		}
	}
	if($save_update_mode=='update')
	{
		if($task_finish_date !='')
		{
			echo "document.getElementById('txt_tna_date').value = '".change_date_format($task_finish_date,'dd-mm-yyyy','-')."';\n";
		}
		else
		{
			echo "document.getElementById('txt_tna_date').value = '';\n";
		}
	}
	
	if($company_id<0) $company_id=0;
	$tna_integrated=return_field_value("tna_integrated","variable_order_tracking","company_name='$company_id' and variable_list=14 and status_active=1 and is_deleted=0","tna_integrated");
	//echo $tna_integrated."**".$pay_mode;die;
	if($tna_integrated==1 && ($pay_mode==3 || $pay_mode==5) )
	{
		echo " $('#txt_delevary_date').attr('disabled',true);\n"; 
	}
	else
	{
		echo "$('#txt_delevary_date').attr('disabled',false);\n";
	}
}

if($action=="set_delivery_date_from_tna"){
	$data=explode("_",$data);
	set_delivery_date_from_tna($data[0], $data[1], $data[2], $data[3], $data[4]);
}

if($action=="booking_qnty_and_amount_trim_group")
{
   $data=explode("_",$data);
   $txt_po_id=$data[0];
   $txt_trim_group_id=$data[1];
   $cbo_trim_precost_id=$data[2];
   $woqty="";
   $amount="";
   $data_sql=sql_select("select sum(wo_qnty) as wo_qnty, sum(amount) as amount from wo_booking_dtls where po_break_down_id=$txt_po_id and trim_group=$txt_trim_group_id and pre_cost_fabric_cost_dtls_id=$cbo_trim_precost_id and booking_type=2 and status_active=1 and is_deleted=0");
  foreach($data_sql as $row)
  {
	$woqty=$row[csf('wo_qnty')];
    $amount=$row[csf('amount')];  
  }
  echo  $woqty."_".$amount;
}


if($action=="booking_amount_and_budget_amount_po_level")
{
   $data=explode("_",$data);
   $txt_po_id=$data[0];
   $txt_trim_group_id=$data[1];
   $cbo_trim_precost_id=$data[2];
   
  $condition= new condition();
  $condition->po_id("=$txt_po_id");
  $condition->init();
  $trims= new trims($condition);
  $trims_costing_arr=$trims->getAmountArray_by_order();
  $budget_amount_po_level=def_number_format($trims_costing_arr[$txt_po_id],5,0);	
  
  $booking_amount_po_level=0;
  $data_sql_polevel=sql_select("select  sum(amount/exchange_rate) as amount from wo_booking_dtls where po_break_down_id=$txt_po_id  and booking_type=2 and status_active=1 and is_deleted=0");
  
  foreach($data_sql_polevel as $row_polevel)
  {
    //$booking_amount_po_level=def_number_format($row_polevel[csf('amount')],5,0);  
	$booking_amount_po_level=number_format($row_polevel[csf('amount')],5,'.',''); 
  }
  
  echo  $budget_amount_po_level."_".$booking_amount_po_level;
}



if($action=="open_country_popup")
{
echo load_html_head_contents("Country","../../../", 1, 1, $unicode);
extract($_REQUEST);
?>
<script> 
/*function js_set_value(data)
{
	document.getElementById('country_name').value=data;
    parent.emailwindow.hide();
}*/

	var selected_id = new Array, selected_name = new Array();	
	 function check_all_data() {
			/*var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}*/
			$("#tbl_list_search tr").each(function() {
			    var valTP=$(this).attr("id");
				$("#"+valTP).click();
				
																				  
		});
		}
		
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual_name' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = '';
			var name='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			//alert(id)
			$('#txt_selected_id').val( id );
			$('#txt_selected_name').val( name );
		}
</script> 
</head>
<body>
<div align="center">
<form>
<input type="hidden" id="txt_selected_id" name="txt_selected_id" value="<? echo $txt_country;?> " />
<input type="hidden" id="txt_selected_name" name="txt_selected_name" value="<? echo $txt_country_name;?> " />
<?
if($txt_po_id!="") $po_cond=" and po_break_down_id=$txt_po_id";else $po_cond="";
//po_break_down_id=$txt_po_id 
$sql_data=sql_select("select country_id  from wo_po_color_size_breakdown   WHERE   status_active=1 and is_deleted=0 $po_cond group by country_id"); 
?>
<table width="450" cellspacing="0" class="rpt_table" border="0" id="tbl_list_search" rules="all">
	<thead>
    	<tr>
        	<th width="50"></th>
            <th width="200">Country Name</th>
            <th>Country Short Name</th>
        </tr>
    </thead>
    <tbody>
	<?
    $i=1;
    foreach($sql_data as $row)
    {
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
		?>
		<tr bgcolor="<? echo $bgcolor;  ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $row[csf('country_id')];?>" onClick="js_set_value(<? echo $row[csf('country_id')];?>)"> 
            <td width="50" align="center"><? echo $i ?></td>
            <td width="100">
            <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $row[csf('country_id')]; ?>" value="<? echo $row[csf('country_id')]; ?>"/>
            <input type="hidden" name="txt_individual_name" id="txt_individual_name<?php echo $row[csf('country_id')]; ?>" value="<? echo $country_library[$row[csf('country_id')]]; ?>"/>		
            <? echo $country_library[$row[csf('country_id')]]; ?>
            </td>
            <td width="100"><? echo $country_library_short[$row[csf('country_id')]]; ?></td>
		</tr>
		<?
		$i++;
    }
    ?> 	
    </tbody>
</table>
<br>
<table width="450" cellspacing="0" cellpadding="0" style="border:none" align="center">
    <tr>
        <td align="center" height="30" valign="bottom">
        <div style="width:100%"> 
        <div style="width:50%; float:left" align="left">
        <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
        </div>
        <div style="width:50%; float:left" align="left">
        <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
        </div>
        </div>
        </td>
    </tr>
</table>
</form>
</div>
</body>
<script>
var txt_country='<? echo $txt_country; ?>'
var txt_country_name='<? echo $txt_country_name; ?>'
if(txt_country !="")
{
selected_id=txt_country.split(",");
selected_name=txt_country_name.split(",");
}

for(var i=0; i<selected_id.length;i++)
{
   if(selected_id[i] !="")
   {
	toggle( document.getElementById( 'search' + selected_id[i] ), '#FFFFCC' );
   }
}

</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
    <?
}

if ($action=="generate_fabric_booking")
{
	extract($_REQUEST);
	if($txt_job_no=="")
	{
		$txt_job_no_cond="";
		$txt_job_no_cond1="";
	}
	else
	{
		$txt_job_no_cond ="and a.job_no='$txt_job_no'";
		$txt_job_no_cond1 ="and job_no='$txt_job_no'";
	}
	
	if($cbo_buyer_name==0)
	{
		$cbo_buyer_name_cond="";
		$cbo_buyer_name_cond1="";
	}
	else
	{
		$cbo_buyer_name_cond =" and a.buyer_name='$cbo_buyer_name'";
		$cbo_buyer_name_cond1 ="and buyer_name='$cbo_buyer_name'";
	}
	
	if($txt_country=="") $txt_country_cond=""; else $txt_country_cond ="and c.country_id in ($txt_country)";
	?>
	<div align="center" style="width:1050px;" >
        <fieldset>
            <form id="consumptionform_1" autocomplete="off">
            <?
            $process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=18 and item_category_id=4 and status_active=1 and is_deleted=0");
            
            // With Gmt Item
            /*$sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$txt_po_id' and c.item_number_id='$cbo_gmt_item_id' $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty,c.item_number_id");*/
            $sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$txt_po_id'  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty");//and c.item_number_id='$cbo_gmt_item_id'
            
            
            list($sql_po_qty_row)=$sql_po_qty;
            $po_qty=$sql_po_qty_row[csf('order_quantity_set')];
            //$po_qty=return_field_value("po_quantity", "wo_po_break_down", "id='$txt_po_id'");
            ?>
            
            <table width="1050" cellspacing="0" class="rpt_table" border="0" id="tbl_consmption_cost" rules="all">
                <thead>
                    <tr>
                        <th width="40" colspan="14">
                            <input type="hidden" id="txtwoq" value="<? echo $txt_req_quantity;?>"/>
                            Wo Qty:<input type="text" id="txtwoq_qty" class="text_boxes_numeric" onBlur="poportionate_qty(this.value)"/>
                            <b>Copy</b> : <input type="checkbox" id="copy_val" name="copy_val" checked/>
                            <input type="hidden" id="process_loss_method_id" name="process_loss_method_id" value="<? echo $process_loss_method; ?>"/>
                            <input type="hidden" id="po_qty" name="po_qty" value="<? echo $po_qty; ?>"/>
                        </th>
                    </tr>
                    <tr>
                        <th width="40">SL</th><th width="100">Gmts. Color</th><th width="70">Gmts. sizes</th><th width="100">Description</th><th width="100">Brand/Sup Ref</th><th  width="100">Item Color</th><th width="80">Item Sizes</th><th width="70"> Wo Qty</th><th width="40">Excess %</th><th width="70">WO Qty.</th><th width="40">Rate</th><th width="50">Amount</th><th width="50">RMG Qnty</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                <?
                $booking_data_arr=array();
                $booking_data=sql_select("select id, wo_trim_booking_dtls_id, description, brand_supplier, item_color, item_size, cons, process_loss_percent, requirment, rate, 	amount, pcs, color_size_table_id from wo_trim_book_con_dtls where wo_trim_booking_dtls_id='$txt_update_dtls_id'");
                foreach($booking_data as $booking_data_row)
                {
					$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][id]=$booking_data_row[csf('id')];
					$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][description]=$booking_data_row[csf('description')];
					$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][brand_supplier]=$booking_data_row[csf('brand_supplier')];
					$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][item_color]=$booking_data_row[csf('item_color')];
					$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][item_size]=$booking_data_row[csf('item_size')];
					$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][cons]=$booking_data_row[csf('cons')];
					$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][process_loss_percent]=$booking_data_row[csf('process_loss_percent')];
					$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][requirment]=$booking_data_row[csf('requirment')];
					$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][rate]=$booking_data_row[csf('rate')];
					$booking_data_arr[$booking_data_row[csf('color_size_table_id')]][amount]=$booking_data_row[csf('amount')];
                }
				unset($booking_data);
                //print_r($booking_data_arr);
                $gmt_color_edb=""; $item_color_edb=""; $gmt_size_edb=""; $item_size_edb="";
                if($cbo_colorsizesensitive==1)
                {
					// With Gmts Item	
					/* $sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$txt_po_id' and c.item_number_id='$cbo_gmt_item_id' $txt_country_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.item_number_id  order by b.id, c.color_number_id";*/
					$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$txt_po_id'  $txt_country_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id order by b.id, c.color_number_id";//and c.item_number_id='$cbo_gmt_item_id'
					
					$gmt_size_edb="disabled"; $item_size_edb="disabled";
                }
                else if($cbo_colorsizesensitive==2)
                {
					// With Gmts Item		
					/*$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.size_number_id,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$txt_po_id' and c.item_number_id='$cbo_gmt_item_id' $txt_country_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.size_number_id,c.item_number_id  order by b.id,c.size_number_id";*/
					$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.size_number_id,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$txt_po_id' $txt_country_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.size_number_id  order by b.id,c.size_number_id";//and c.item_number_id='$cbo_gmt_item_id'
					
					$gmt_color_edb="disabled"; $item_color_edb="disabled";
                }
                else if($cbo_colorsizesensitive==3)
                {
					// With Gmts Item	
					/*$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$txt_po_id' and c.item_number_id='$cbo_gmt_item_id' $txt_country_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.item_number_id  order by b.id, c.color_number_id";*/
					$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$txt_po_id'  $txt_country_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id order by b.id, c.color_number_id";//and c.item_number_id='$cbo_gmt_item_id'
					$gmt_size_edb="disabled"; $item_size_edb="disabled";
                }
                else if($cbo_colorsizesensitive==4)
                {
					// With Gmts Item
					/*$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$txt_po_id' and c.item_number_id='$cbo_gmt_item_id' $txt_country_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.size_number_id,c.item_number_id  order by b.id, c.color_number_id,c.size_number_id";*/
					$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$txt_po_id'  $txt_country_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.size_number_id  order by b.id, c.color_number_id,c.size_number_id";//and c.item_number_id='$cbo_gmt_item_id'
                }
                else
                {
					// With Gmts Item
					/*$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$txt_po_id' and c.item_number_id='$cbo_gmt_item_id' $txt_country_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.item_number_id order by b.id";*/
					$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$txt_po_id' $txt_country_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty order by b.id";//and c.item_number_id='$cbo_gmt_item_id'
                }
                $data_array=sql_select($sql); 
                if ( count($data_array)>0)
				{
					$i=0;
					foreach( $data_array as $row )
					{
						$data=explode('_',$data_array_cons[$i]);
						$i++;
						
						$txtwoq_cal =def_number_format(($txt_req_quantity/$po_qty) * ($row[csf('order_quantity_set')]),5,"");
						$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
						if($item_color==0 || $item_color=="" ) $item_color = $row[csf('color_number_id')];
						
						$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
						if($item_size==0 || $item_size == "") $item_size=$size_library[$row[csf('size_number_id')]];
						
						$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
						if($rate==0 || $rate=="") $rate=$txt_avg_price;
						
						$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];
						//echo $description."mmm";
						if($description=="") $description=$txt_pre_des;
						//echo $description."mmm";
						$brand_supplier=$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
						if($brand_supplier=="") $brand_supplier=$txt_pre_brand_sup;
						?>
						<tr id="break_1" align="center">
                            <td><? echo $i; ?></td>
                            <td>
                                <input type="text" id="pocolor_<? echo $i;?>" name="pocolor_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $color_library[$row[csf('color_number_id')]]; ?>" <? echo  $gmt_color_edb; ?>/>
                                <input type="hidden" id="pocolorid_<? echo $i;?>" name="pocolorid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('color_number_id')]; ?>" />
                            </td>
                            <td>
                                <input type="text" id="gmtssizes_<? echo $i;?>" name="gmtssizes_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $size_library[$row[csf('size_number_id')]]; ?>"<?  echo $gmt_size_edb; ?>/>
                                <input type="hidden" id="gmtssizesid_<? echo $i;?>" name="gmtssizesid_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $row[csf('size_number_id')]; ?>">
                            </td>
                            <td><input type="text" id="des_<? echo $i;?>" name="des_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $description;?>" onChange="copy_value(this.value,'des_',<? echo $i;?>)" /></td>
                            <td><input type="text" id="brndsup_<? echo $i;?>" name="brndsup_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $brand_supplier; ?>" onChange="copy_value(this.value,'brndsup_',<? echo $i;?>)" /></td>
                            <td><input type="text" id="itemcolor_<? echo $i;?>"  value="<? echo $color_library[$item_color]; ?>" name="itemcolor_<? echo $i;?>"  class="text_boxes" style="width:95px" onChange="copy_value(this.value,'itemcolor_',<? echo $i;?>)" <? echo $item_color_edb; ?> /></td>
                            <td><input type="text" id="itemsizes_<? echo $i;?>" name="itemsizes_<? echo $i;?>" class="text_boxes" style="width:70px" onChange="copy_value(this.value,'itemsizes_',<? echo $i;?>)" value="<? echo $item_size; ?>" <? echo $item_size_edb; ?>/></td>
                            <td>
                            	<input type="text" id="qty_<? echo $i;?>" onBlur="validate_sum( <? echo $i; ?> )" onChange="set_sum_value( 'qty_sum', 'qty_' );set_sum_value( 'woqty_sum', 'woqny_' );calculate_requirement(<? echo $i;?>);copy_value(this.value,'qty_',<? echo $i;?>)"  name="qty_<? echo $i;?>" class="text_boxes_numeric" style="width:70px"   placeholder="<? echo $txtwoq_cal; ?>" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][cons]; ?>"/> 
                            </td>
                            <td>
                            	<input type="text" id="excess_<? echo $i;?>" onBlur="set_sum_value( 'excess_sum', 'excess_' ) "  name="excess_<? echo $i;?>" class="text_boxes_numeric" style="width:40px" onChange="calculate_requirement(<? echo $i;?>);set_sum_value( 'excess_sum', 'excess_' );set_sum_value( 'woqty_sum', 'woqny_' );copy_value(this.value,'excess_',<? echo $i;?>) " value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]; ?>" disabled/> 
                            </td>
                            <td><input type="text" id="woqny_<? echo $i;?>" onBlur="set_sum_value( 'woqty_sum', 'woqny_' ) " onChange="set_sum_value( 'woqty_sum', 'woqny_' )"  name="woqny_<? echo $i;?>" class="text_boxes_numeric" style="width:70px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][requirment]; ?>" readonly /></td>
                            <td><input type="text" id="rate_<? echo $i;?>" name="rate_<? echo $i;?>" class="text_boxes_numeric" style="width:40px" onChange="calculate_amount(<? echo $i;?>);set_sum_value( 'amount_sum', 'amount_' );copy_value(this.value,'rate_',<? echo $i;?>) " value="<? echo $rate; ?>" /></td>
                            <td><input type="text" id="amount_<? echo $i;?>"  name="amount_<? echo $i;?>"  onBlur="set_sum_value( 'amount_sum', 'amount_' ) " class="text_boxes_numeric" style="width:50px"  value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][amount]; ?>" readonly>
                            </td>
                            <td>
                                <input type="text" id="pcs_<? echo $i;?>"  name="pcs_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:50px"  value="<? echo $row[csf('order_quantity')]; ?>" readonly>
                                <input type="hidden" id="pcsset_<? echo $i;?>"  name="pcsset_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:50px"  value="<? echo $row[csf('order_quantity_set')]; ?>" readonly>
                            </td>
                            <td id="add_1">
                                <input type="hidden" id="colorsizetableid_<? echo $i;?>"  name="colorsizetableid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('color_size_table_id')]; ?>" />
                                <input type="hidden" id="updateid_<? echo $i;?>"  name="updateid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][id]; ?>" />
                                <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(<? echo $i;?>,'tbl_consmption_cost');" />
                            </td>
						</tr>
						<?
					}
				}
                ?>
                </tbody>
            </table>
            <table width="1050" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    <tr>
                        <th style="width:534px;">SUM</th>
                        <th width="82">&nbsp;</th>
                        <th width="70"><input type="text" id="qty_sum" name="qty_sum" class="text_boxes_numeric" style="width:70px"  readonly></th>
                        <th width="40"><input type="text" id="excess_sum" name="excess_sum" class="text_boxes_numeric" style="width:40px" readonly></th>
                        <th width="70"><input type="text" id="woqty_sum" name="woqty_sum" class="text_boxes_numeric" style="width:70px" readonly></th>
                        <th width="40"><input type="text" id="rate_sum" name="rate_sum" class="text_boxes_numeric" style="width:40px" readonly></th>
                        <th width="50"><input type="text" id="amount_sum" name="amount_sum" class="text_boxes_numeric" style="width:50px" readonly></th>
                        <th width="50"><input type="text" id="pcs_sum" name="pcs_sum" class="text_boxes_numeric" style="width:50px" readonly></th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            </form>
        </fieldset>
	</div>
	<?
	exit();
}

if ($action=="show_trim_booking")
{
	extract($_REQUEST);
	if($txt_job_no=="")
	{
		$txt_job_no_cond="";
		$txt_job_no_cond1="";
	}
	else
	{
		$txt_job_no_cond ="and a.job_no='$txt_job_no'";
		$txt_job_no_cond1 ="and job_no='$txt_job_no'";
	}
	
	if($cbo_buyer_name==0)
	{
		$cbo_buyer_name_cond="";
		$cbo_buyer_name_cond1="";
	}
	else
	{
		$cbo_buyer_name_cond =" and a.buyer_name='$cbo_buyer_name'";
		$cbo_buyer_name_cond1 ="and buyer_name='$cbo_buyer_name'";
	}
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1360" class="rpt_table" >
        <thead>
            <th width="40">SL</th>
            <th width="100">Job No</th>
            <th width="80">File No</th>
            <th width="80">Ref. No</th>
            <th width="100">Ord. No</th>
            <th width="80">Country</th>
            <th width="100">Trims Group</th>
            <th width="100">Sensitivity</th>
            <th width="60">UOM</th>
            <th width="80">Del. Date</th>
            <th width="70">Req. Qnty</th>
            <th width="70">CU WOQ</th>
            <th width="70">Bal WOQ</th>
            <th width="70">WOQ</th>
            <th width="50">Rate</th>
            <th width="60">Exchange Rate</th>
            <th>Amount</th>
        </thead>
	</table>
	<div style="width:1360px; overflow-y:scroll; max-height:350px;" id="buyer_list_view" align="center">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1343" class="rpt_table" id="tbl_list_search" >
            <tbody>
            <?
            $sql_lib_item_group_array=array();
            $sql_lib_item_group=sql_select("select id, item_name, conversion_factor, order_uom as cons_uom from lib_item_group");
            foreach($sql_lib_item_group as $row_sql_lib_item_group)
            {
				$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
				$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
				$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
            }
            $sql="select id as booking_id, job_no, po_break_down_id, pre_cost_fabric_cost_dtls_id, country_id_string, gmt_item, booking_no, trim_group, sensitivity, uom, delivery_date, wo_qnty, rate, exchange_rate, amount from wo_booking_dtls where booking_no='$txt_booking_no' and booking_type=2 and status_active=1 and is_deleted=0 order by id asc";
            $list_view_data_arr=array(); $country_id_array=array(); $po_id_array=array(); $job_array=array(); $pre_cost_fabric_cost_dtls_id_array=array();
            $i=1; $total_amount=0;
            $nameArray=sql_select( $sql );
            foreach ($nameArray as $selectResult)
            {
				$list_view_data_arr[$i][csf('booking_id')]=$selectResult[csf('booking_id')];
				$list_view_data_arr[$i][csf('job_no')]=$selectResult[csf('job_no')];
				$list_view_data_arr[$i][csf('po_break_down_id')]=$selectResult[csf('po_break_down_id')];
				$list_view_data_arr[$i][csf('pre_cost_fabric_cost_dtls_id')]=$selectResult[csf('pre_cost_fabric_cost_dtls_id')];
				$list_view_data_arr[$i][csf('country_id_string')]=$selectResult[csf('country_id_string')];
				$list_view_data_arr[$i][csf('gmt_item')]=$selectResult[csf('gmt_item')];
				$list_view_data_arr[$i][csf('booking_no')]=$selectResult[csf('booking_no')];
				$list_view_data_arr[$i][csf('trim_group')]=$selectResult[csf('trim_group')];
				$list_view_data_arr[$i][csf('sensitivity')]=$selectResult[csf('sensitivity')];
				$list_view_data_arr[$i][csf('uom')]=$selectResult[csf('uom')];
				$list_view_data_arr[$i][csf('delivery_date')]=$selectResult[csf('delivery_date')];
				$list_view_data_arr[$i][csf('wo_qnty')]=$selectResult[csf('wo_qnty')];
				$list_view_data_arr[$i][csf('rate')]=$selectResult[csf('rate')];
				$list_view_data_arr[$i][csf('exchange_rate')]=$selectResult[csf('exchange_rate')];
				$list_view_data_arr[$i][csf('amount')]=$selectResult[csf('amount')];
				$country_id_array[$i]=$selectResult[csf('country_id_string')];
				$po_id_array[$i]=$selectResult[csf('po_break_down_id')];
				$job_array[$i]="'".$selectResult[csf('job_no')]."'";
				$pre_cost_fabric_cost_dtls_id_array[$i]=$selectResult[csf('pre_cost_fabric_cost_dtls_id')];
				$i++;
            }
            
            $country_ids=implode(",",array_unique(explode(",",implode(",",$country_id_array))));
            $po_ids=implode(",",array_unique(explode(",",implode(",",$po_id_array))));
            $jobs=implode(",",array_unique(explode(",",implode(",",$job_array))));
            $pre_cost_fabric_cost_dtls_ids=implode(",",array_unique(explode(",",implode(",",$pre_cost_fabric_cost_dtls_id_array))));
            
            if($country_ids=="") $txt_country_cond=""; else $txt_country_cond ="and c.country_id in (".$country_ids.")";
            if($po_ids=="" || $po_ids==0) $po_ids=0;else $po_ids=$po_ids; 
            $array_po_qty=array();
            $sql_po_qty=sql_select("select b.id,c.country_id, sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id in(".$po_ids.")  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty,c.country_id");
            foreach($sql_po_qty as $row_po_qty)
            {
            	$array_po_qty[$row_po_qty[csf('id')]][$row_po_qty[csf('country_id')]]=$row_po_qty[csf('order_quantity_set')];	
            }
            
            $i=1;
            foreach ($list_view_data_arr as $selectResult)
            {
				if($selectResult[csf('country_id_string')]=="") $txt_country_cond="";
				else $txt_country_cond ="and c.country_id in (".$selectResult[csf('country_id_string')].")";
				//With Gmt Item
				/*$sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='".$selectResult[csf('po_break_down_id')]."' and c.item_number_id='".$selectResult[csf('gmt_item')]."' $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty,c.item_number_id ");*/
				$sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='".$selectResult[csf('po_break_down_id')]."'  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty");
				
				list($sql_po_qty_row)=$sql_po_qty;
				$po_qty=$sql_po_qty_row[csf('order_quantity_set')];
				
				$sql_cons_data=sql_select("select b.cons from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id and  a.id='".$selectResult[csf('pre_cost_fabric_cost_dtls_id')]."' and b.po_break_down_id='".$selectResult[csf('po_break_down_id')]."' and a.is_deleted=0  and a.status_active=1");
				list($sql_cons_data_row)=$sql_cons_data;
				$cons=$sql_cons_data_row[csf('cons')];
				
				$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$selectResult[csf('job_no')]."'");
				$sql_file=sql_select("select file_no, grouping from wo_po_break_down where id=".$selectResult[csf('po_break_down_id')]."");
				//echo "select file_no,grouping from wo_po_break_down where id=".$selectResult[csf('po_break_down_id')]."";
				list($sql_po_data)=$sql_file;
				$file_no=$sql_po_data[csf('file_no')];
				$ref_no=$sql_po_data[csf('grouping')];
				
				//echo  $file_no.'=='. $ref_no;
				if($costing_per==1) $costing_per_qty=12;
				else if($costing_per==2) $costing_per_qty=1;
				else if($costing_per==3) $costing_per_qty=24;
				else if($costing_per==4) $costing_per_qty=36;
				else if($costing_per==5) $costing_per_qty=48;
				
				$req_qnty=def_number_format(($cons*($po_qty/$costing_per_qty))/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],5,"");
				
				$sql_cu_woq=sql_select("select sum(wo_qnty) as cu_woq  from wo_booking_dtls where po_break_down_id='".$selectResult[csf('po_break_down_id')]."' and pre_cost_fabric_cost_dtls_id='".$selectResult[csf('pre_cost_fabric_cost_dtls_id')]."' and country_id_string='".$selectResult[csf('country_id_string')]."' and gmt_item='".$selectResult[csf('gmt_item')]."' and trim_group='".$selectResult[csf('trim_group')]."' and  booking_type=2 and status_active=1 and is_deleted=0");
				
				list($sql_cu_woq_row)=$sql_cu_woq;
				$cu_woq=$sql_cu_woq_row[csf('cu_woq')];
				$bal_woq=def_number_format($req_qnty-$cu_woq,5,"");
				$rate=def_number_format(($selectResult[csf('rate')]*$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor]),5,"");
				
				if($selectResult[csf("country_id_string")]!="")
				{
					$country_name_string="";
					$country_id_arr=explode(",",$selectResult[csf("country_id_string")]);
					for($k=0; $k<count($country_id_arr); $k++)
					{
						$country_name_string.=$country_library[$country_id_arr[$k]].",";
					}
					$country_name_string=rtrim($country_name_string,",");
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="load_dtls_data(<? echo $selectResult[csf('booking_id')]; ?>,<? echo $req_qnty;  ?>)">
                    <td width="40"><? echo $i;?></td>
                    <td width="100"><? echo $selectResult[csf('job_no')];?></td>
                    <td width="80"><? echo $file_no;?></td>
                    <td width="80"><?  echo $ref_no;?></td>
                    <td width="100"><? echo $po_number[$selectResult[csf('po_break_down_id')]];?></td>
                    <td width="80" title="<? echo $country_name_string ?>">
						<?
                            $country=explode(",",$country_name_string);
                            echo $country[0];
                            if($country[1]!="") echo "...";
                        ?>
                    </td>
                    <td width="100"><? echo $sql_lib_item_group_array[$selectResult[csf('trim_group')]][item_name];?></td>
                    <td width="100"><? echo $size_color_sensitive[$selectResult[csf('sensitivity')]];?></td>
                    <td width="60"><? echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];?></td>
                    <td width="80"><? echo change_date_format($selectResult[csf('delivery_date')],"dd-mm-yyyy","-"); ?></td>
                    <td width="70" align="right"><? echo def_number_format($req_qnty,2,""); ?></td>
                    <td width="70" align="right"><? echo def_number_format($cu_woq,2,"");?></td>
                    <td width="70" align="right"><? echo def_number_format($bal_woq,2,"");?></td>
                    <td width="70" align="right"><? echo $selectResult[csf('wo_qnty')];?></td>
                    <td width="50" align="right"><? echo number_format($selectResult[csf('rate')],8);?></td>
                    <td width="60" align="right"><? echo number_format($selectResult[csf('exchange_rate')],2);?></td>
                    <td align="right"><? echo def_number_format($selectResult[csf('amount')],2,""); $total_amount+=$selectResult[csf('amount')];?></td>
				</tr>
				<?
				$i++;
            }
            //print_r($list_view_data_arr);
            ?>
            </tbody>
        </table>
	</div>
	<table width="1360" class="rpt_table" border="0" rules="all">
        <tfoot>
            <tr>
                <th width="40"></th>
                <th width="100"></th>
                <th width="80"></th>
                <th width="80"></th>
                <th width="100"></th>
                <th width="80"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="60"></th>
                <th width="80"></th>
                <th width="70"></th>
                <th width="70"></th>
                <th width="70"></th>
                <th width="70"></th>
                <th width="50"></th>
                <th width="60"></th>
                <th><? echo number_format($total_amount,2);?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
            </tr>
        </tfoot>
	</table>
	<?
	exit();
}

if($action=="populate_data_dtls_from")
{
$sql="select b.id, b.job_no, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.trim_group, b.sensitivity, b.uom, b.delivery_date, b.wo_qnty, b.rate, b.amount, b.pre_req_amt, b.exchange_rate, b.country_id_string, b.gmt_item, a.company_id, a.pay_mode, a.trime_type,a.item_from_precost from wo_booking_mst a, wo_booking_dtls b  where a.booking_no=b.booking_no and b.id='$data'";     
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
		//echo "load_drop_down( 'requires/trims_booking_controller', '".$row[csf("job_no")]."', 'load_drop_down_trim_precost_id', 'trim_group_td' );\n";
		
		//echo "load_drop_down( 'requires/trims_booking_controller', '".$row[csf("job_no")]."', 'load_drop_down_gmt_item', 'gmt_item_td' );\n";
		$drop_down_gmt_item=load_drop_down_gmt_item($row[csf("job_no")],$garments_item);
		echo "document.getElementById('gmt_item_td').innerHTML = '".$drop_down_gmt_item."';\n";
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n"; 
		echo "document.getElementById('txt_po_no').value = '".$po_number[$row[csf("po_break_down_id")]]."';\n";  
		echo "document.getElementById('txt_po_id').value = '".$row[csf("po_break_down_id")]."';\n"; 
		echo "document.getElementById('cbo_gmt_item_id').value = '".$row[csf("gmt_item")]."';\n";  
		
		echo "document.getElementById('txt_trim_group_id').value = '".$row[csf("trim_group")]."';\n";  
		echo "document.getElementById('cbo_colorsizesensitive').value = '".$row[csf("sensitivity")]."';\n"; 
		echo "document.getElementById('cbo_uom').value = '".$row[csf("uom")]."';\n";
		echo "document.getElementById('txt_delevary_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n"; 
	    echo "document.getElementById('txt_quantity').value = '".$row[csf("wo_qnty")]."';\n";
		echo "$('#txt_quantity').attr('saved_quantity', '".$row[csf("wo_qnty")]."');\n";
	    echo "document.getElementById('txt_avg_price').value = '".$row[csf("rate")]."';\n";
		echo "document.getElementById('txt_exchange_rate_dtls').value = '".$row[csf("exchange_rate")]."';\n"; 
	    echo "document.getElementById('txt_amount').value = '".$row[csf("amount")]."';\n";
		echo "document.getElementById('txt_req_amt').value = '".$row[csf("pre_req_amt")]."';\n";
		
		echo "$('#txt_amount').attr('saved_amount', '".$row[csf("amount")]."');\n";

		echo "document.getElementById('txt_update_dtls_id').value = '".$row[csf("id")]."';\n";
		
		if($row[csf("country_id_string")]!="")
		{
			$country_name_string="";
			$country_id_arr=explode(",",$row[csf("country_id_string")]);
			for($i=0; $i<count($country_id_arr); $i++)
			{
				$country_name_string.=$country_library[$country_id_arr[$i]].",";
			}
			$country_name_string=rtrim($country_name_string,",");
			echo "document.getElementById('txt_country').value = '".$row[csf("country_id_string")]."';\n"; 
			echo "document.getElementById('txt_country_name').value = '".$country_name_string."';\n"; 
		}
		if($row[csf("item_from_precost")]==1){
			$drop_down_trim_precost_id=load_drop_down_trim_precost_id($row[csf("job_no")]);
			echo "document.getElementById('trim_group_td').innerHTML = '".$drop_down_trim_precost_id."';\n";
			echo "document.getElementById('cbo_trim_precost_id').value = '".$row[csf("pre_cost_fabric_cost_dtls_id")]."';\n";
			echo "set_precost_data(".$row[csf("pre_cost_fabric_cost_dtls_id")].",2);\n";
		}
		if($row[csf("item_from_precost")]==2){
			echo "document.getElementById('cbo_trim_precost_id').value = '".$row[csf("trim_group")]."';\n";
			echo "set_precost_data(".$row[csf("trim_group")].",2);\n";
		}
		//set_precost_data($row[csf("pre_cost_fabric_cost_dtls_id")]."_".$row[csf("po_break_down_id")]."_".$row[csf("country_id_string")]."_".$row[csf("gmt_item")]."_".$row[csf("gmt_item")],2); trime_type
		set_delivery_date_from_tna($row[csf("po_break_down_id")], "update", $row[csf("company_id")], $row[csf("pay_mode")], $row[csf("trime_type")]);
	 }	
}
if ($action=="save_update_delete")
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
		if($db_type==0)
		{
			$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'TB', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=2 and YEAR(insert_date)=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		}
		if($db_type==2)
		{
		$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'TB', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and booking_type=2 and to_char(insert_date,'YYYY')=".date('Y',time())." order by booking_no_prefix_num desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		}
		$id=return_next_id( "id", "wo_booking_mst", 1 ) ;
		$field_array="id,booking_type,is_short,booking_no_prefix,booking_no_prefix_num,booking_no,company_id,buyer_id,job_no, 	item_category,supplier_id,currency_id,exchange_rate,booking_date,delivery_date,pay_mode,source,attention,trime_type,ready_to_approved,remarks,entry_form,item_from_precost,inserted_by,insert_date";
		$data_array ="(".$id.",2,".$cbo_isshort.",'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",".$txt_job_no.",4,".$cbo_supplier_name.",".$cbo_currency.",".$txt_exchange_rate.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_pay_mode.",".$cbo_source.",".$txt_attention.",".$cbo_trim_type.",".$cbo_ready_to_approved.",".$txt_remarks.",43,".$cbo_item_from_precost.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		$rID=sql_insert("wo_booking_mst",$field_array,$data_array,0);
		//cbo_trim_type 
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");  
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_booking_no[0];
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);  
				echo "0**".$new_booking_no[0];
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0];
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
		 
		//if(str_replace("'","",$cbo_pay_mode)==2){
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				disconnect($con);die;
			}
	/*	}else{*/
			$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no  and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($recv_number){
				echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
				disconnect($con);die;
			}
		//}
		 
		 $field_array_up="is_short*job_no*item_category*supplier_id*currency_id*exchange_rate*booking_date*delivery_date*pay_mode*source*attention*trime_type*ready_to_approved*remarks*entry_form*item_from_precost*updated_by*update_date"; 
		 //cbo_trim_type
		 $data_array_up ="".$cbo_isshort."*".$txt_job_no."*4*".$cbo_supplier_name."*".$cbo_currency."*".$txt_exchange_rate."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_pay_mode."*".$cbo_source."*".$txt_attention."*".$cbo_trim_type."*".$cbo_ready_to_approved."*".$txt_remarks."*43*".$cbo_item_from_precost."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_update("wo_booking_mst",$field_array_up,$data_array_up,"booking_no","".$txt_booking_no."",0);
		
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);  
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con);  
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
	
	
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//if(str_replace("'","",$cbo_pay_mode)==2){
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			disconnect($con);	die;
			}
	/*	}else{*/
			$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no  and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($recv_number){
				echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
				disconnect($con);die;
			}
		//}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("wo_booking_mst",$field_array,$data_array,"booking_no","".$txt_booking_no."",1);
		$rID1=sql_delete("wo_booking_dtls",$field_array,$data_array,"booking_no","".$txt_booking_no."",1);
		if($db_type==0)
		{
			if($rID &&  $rID1){
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID &&  $rID1){
				oci_commit($con);  
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con);  
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
		 
		
	}
}



if ($action=="save_update_delete_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	//Trims Received for booking Validation................................................................
	$trims_rec_arr=array();
	if ($operation!=0)
	{	
		$sql_rec="select b.booking_id, b.booking_no, b.item_group_id, b.item_description, b.order_id, b.receive_qnty, b.reject_receive_qnty, b.cons_qnty, b.gmts_color_id, b.item_color, b.gmts_size_id, b.item_size from inv_receive_master a,inv_trims_entry_dtls b where a.booking_no=b.booking_no and a.id=b.mst_id and a. booking_no=$txt_booking_no";
		$rec_data=sql_select($sql_rec);
		foreach($rec_data as $row)
		{
			if($row[csf('gmts_color_id')]==0){$gmts_color_id=0;}else{$gmts_color_id=$row[csf('gmts_color_id')];}
			if($row[csf('gmts_size_id')]==0){$gmts_size_id=0;}else{$gmts_size_id=$row[csf('gmts_size_id')];}
			if($row[csf('item_color')]==0){$item_color=0;}else{$item_color=$row[csf('item_color')];}
			if($row[csf('item_size')]==""){$item_size=0;}else{$item_size=$row[csf('item_size')];}
			$trims_rec_arr[$row[csf('booking_no')]][$row[csf('item_group_id')]][$row[csf('item_description')]][$gmts_color_id][$gmts_size_id][$item_color][$item_size]+=$row[csf('receive_qnty')];

		}
		
	}
	//end Trims Received for booking Validation................................................................
	
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		 $id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		 $field_array1="id, pre_cost_fabric_cost_dtls_id, po_break_down_id, job_no, booking_no, booking_type, is_short, trim_group, uom, sensitivity, wo_qnty, rate, exchange_rate, amount, pre_req_amt, delivery_date, country_id_string, gmt_item, inserted_by, insert_date";
		 $field_array2="id, wo_trim_booking_dtls_id, booking_no, job_no, po_break_down_id, color_number_id, gmts_sizes, description, brand_supplier, item_color, item_size, cons, process_loss_percent, requirment, rate, amount, pcs, color_size_table_id";
		 if(str_replace("'","",$cbo_item_from_precost)==2){
			 $cbo_trim_precost_id=0;
		 }

		$data_array1 .="(".$id_dtls.",".$cbo_trim_precost_id.",".$txt_po_id.",".$txt_job_no.",".$txt_booking_no.",2,".$cbo_isshort.",".$txt_trim_group_id.",".$cbo_uom.",".$cbo_colorsizesensitive.",".$txt_quantity.",".$txt_avg_price.",".$txt_exchange_rate_dtls.",".$txt_amount.",".$txt_req_amt.",".$txt_delevary_date.",".$txt_country.",".$cbo_gmt_item_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//echo $data_array1;
		//==============================
		 $id1=return_next_id( "id", "wo_trim_book_con_dtls", 1 );
		 $new_array_color=array();
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $pocolorid="pocolorid_".$i;
			 $gmtssizesid="gmtssizesid_".$i;
			 $des="des_".$i;
			 $brndsup="brndsup_".$i;
			 $itemcolor="itemcolor_".$i;
			 $itemsizes="itemsizes_".$i;
			 $qty="qty_".$i;
			 $excess="excess_".$i;
			 $woqny="woqny_".$i;
			 $rate="rate_".$i;
			 $amount="amount_".$i;
			 $pcs="pcs_".$i;
			 $updateid="updateid_".$i;
			 $colorsizetableid="colorsizetableid_".$i;
			 
			 /*if (!in_array(str_replace("'","",$$itemcolor),$new_array_color))
			 {
				  $color_id = return_id( str_replace("'","",$$itemcolor), $color_library, "lib_color", "id,color_name");  
				  $new_array_color[$color_id]=str_replace("'","",$$itemcolor);
			 }
			 else 
			 {
				 $color_id =  array_search(str_replace("'","",$$itemcolor), $new_array_color);
			 }*/
			if(str_replace("'","",$$itemcolor) !="")
			{
			    if (!in_array(str_replace("'","",$$itemcolor),$new_array_color))
			    {
			        $color_id = return_id( str_replace("'","",$$itemcolor), $color_library, "lib_color", "id,color_name","43");
			        $new_array_color[$color_id]=str_replace("'","",$$itemcolor);
			    }
			    else $color_id =  array_search(str_replace("'","",$$itemcolor), $new_array_color);
			}
			else
			{
			    $color_id=0;
			}
			 if(str_replace("'","",$cbo_colorsizesensitive)==2){
				 $color_id='';
				 $pocolorid='';
			 }
			 else{
				 $color_id=str_replace("'","",$color_id);
				 $pocolorid=str_replace("'","",$$pocolorid);
			 }
			 if(str_replace("'","",$cbo_colorsizesensitive)==1 || str_replace("'","",$cbo_colorsizesensitive)==3){
				 $gmtssizesid='';
				 $itemsizes='';
			 }
			 else{
				 $gmtssizesid=str_replace("'","",$$gmtssizesid);
				 $itemsizes=str_replace("'","",$$itemsizes);
			 }

			 if ($i!=1) $data_array2 .=",";
			 $data_array2 .="(".$id1.",".$id_dtls.",".$txt_booking_no.",".$txt_job_no.",".$txt_po_id.",'".$pocolorid."','".$gmtssizesid."',".$$des.",".$$brndsup.",'".$color_id."','".$itemsizes."',".$$qty.",".$$excess.",".$$woqny.",".$$rate.",".$$amount.",".$$pcs.",".$$colorsizetableid.")";
			 //echo $data_array2;
			 $id1=$id1+1;
		 }
		 
		
		 //=============================
		 $rID=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
		 $txt_delivery_date=return_field_value( "max(delivery_date)", "wo_booking_dtls","booking_no = $txt_booking_no");
		 
		 //echo "10**update  wo_booking_mst set supplier_id=$cbo_supplier_name,delivery_date='$txt_delivery_date' where booking_no=$txt_booking_no";die;
		 
		 $rID_1=execute_query("update  wo_booking_mst set supplier_id=$cbo_supplier_name,delivery_date='$txt_delivery_date' where booking_no=$txt_booking_no");
		 
		 
		//echo "insert into wo_booking_dtls(".$field_array1.") values".$data_array2; die;
		 $rID1=sql_insert("wo_trim_book_con_dtls",$field_array2,$data_array2,0);
		if($db_type==0)
		{
			if($rID && $rID1){
				mysql_query("COMMIT");  
				echo "0**".$txt_booking_no;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$txt_booking_no;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1){
				oci_commit($con);  
				echo "0**".$txt_booking_no;
			}
			else{
				oci_rollback($con);
				echo "10**".$txt_booking_no;
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
		 
		 //if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");//and b.item_group=".$txt_trim_group_id."
			    if($pi_number){
				    echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
					check_table_status( $_SESSION['menu_id'],0);
				   disconnect($con); die;
			    }
			/*}else{*/
				$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no  and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");//and b.item_group_id=".$txt_trim_group_id."
			    if($recv_number){
				    echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
					check_table_status( $_SESSION['menu_id'],0);
				    disconnect($con);die;
			    }
			//}
		 
		 $field_array_up1="pre_cost_fabric_cost_dtls_id*po_break_down_id*job_no*booking_no*is_short*trim_group*uom*sensitivity*wo_qnty*rate*amount*pre_req_amt*exchange_rate*delivery_date*country_id_string*gmt_item*updated_by*update_date";
		 $field_array_up2="color_number_id*gmts_sizes*description*brand_supplier*item_color*item_size*cons* process_loss_percent*requirment*rate*amount*pcs*color_size_table_id";

         if(str_replace("'","",$cbo_item_from_precost)==2){
			 $cbo_trim_precost_id=0;
		 }
		$data_array_up1 .="".$cbo_trim_precost_id."*".$txt_po_id."*".$txt_job_no."*".$txt_booking_no."*".$cbo_isshort."*".$txt_trim_group_id."*".$cbo_uom."*".$cbo_colorsizesensitive."*".$txt_quantity."*".$txt_avg_price."*".$txt_amount."*".$txt_req_amt."*".$txt_exchange_rate_dtls."*".$txt_delevary_date."*".$txt_country."*".$cbo_gmt_item_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$field_array_up2="color_number_id*gmts_sizes*description*brand_supplier*item_color*item_size*cons* process_loss_percent*requirment*rate*amount*pcs*color_size_table_id";
		
		//==============================
		 $id1=return_next_id( "id", "wo_trim_book_con_dtls", 1 );
		 $new_array_color=array();
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $pocolorid="pocolorid_".$i;
			 $gmtssizesid="gmtssizesid_".$i;
			 $des="des_".$i;
			 $brndsup="brndsup_".$i;
			 $itemcolor="itemcolor_".$i;
			 $itemsizes="itemsizes_".$i;
			 $qty="qty_".$i;
			 $excess="excess_".$i;
			 $woqny="woqny_".$i;
			 $rate="rate_".$i;
			 $amount="amount_".$i;
			 $pcs="pcs_".$i;
			 $updateid="updateid_".$i;
			 $colorsizetableid="colorsizetableid_".$i;
			 
			 /*if (!in_array(str_replace("'","",$$itemcolor),$new_array_color))
			 {
				  $color_id = return_id( str_replace("'","",$$itemcolor), $color_library, "lib_color", "id,color_name");  
				  $new_array_color[$color_id]=str_replace("'","",$$itemcolor);
			 }
			 else 
			 {
				 $color_id =  array_search(str_replace("'","",$$itemcolor), $new_array_color);
			 }*/
			if(str_replace("'","",$$itemcolor) !="")
			{
			    if (!in_array(str_replace("'","",$$itemcolor),$new_array_color))
			    {
			        $color_id = return_id( str_replace("'","",$$itemcolor), $color_library, "lib_color", "id,color_name","43");
			        $new_array_color[$color_id]=str_replace("'","",$$itemcolor);
			    }
			    else $color_id =  array_search(str_replace("'","",$$itemcolor), $new_array_color);
			}
			else
			{
			    $color_id=0;
			}
			if(str_replace("'","",$cbo_colorsizesensitive)==2){
				 $color_id='';
				 $pocolorid='';
			 }
			 else{
				 $color_id=str_replace("'","",$color_id);
				 $pocolorid=str_replace("'","",$$pocolorid);
			 }
			 if(str_replace("'","",$cbo_colorsizesensitive)==1 || str_replace("'","",$cbo_colorsizesensitive)==3){
				 $gmtssizesid='';
				 $itemsizes='';
			 }
			 else{
				 $gmtssizesid=str_replace("'","",$$gmtssizesid);
				 $itemsizes=str_replace("'","",$$itemsizes);
			 }
		    if(str_replace("'",'',$$updateid)!="")
			{
				$item_des=str_replace("'","",$$des);
				$gmt_color=($pocolorid=="")?0:$pocolorid;
				$gmt_size=($gmtssizesid=="")?0:$gmtssizesid;
				$item_color=($color_id=="")?0:$color_id;
				$item_size=($itemsizes=="")?0:$itemsizes;
				
				if($trims_rec_arr[str_replace("'","",$txt_booking_no)][str_replace("'","",$txt_trim_group_id)][$item_des][$gmt_color][$gmt_size][$item_color][$item_size]>str_replace("'","",$$qty))
				{
					echo "14**".str_replace("'","",$txt_booking_no);
					break;die;	
				}
				
				
		        $id_arr[]=str_replace("'",'',$$updateid);
				$data_array_up2[str_replace("'",'',$$updateid)] =explode("*",("'".$pocolorid."'*'".$gmtssizesid."'*".$$des."*".$$brndsup."*'".$color_id."'*'".$itemsizes."'*".$$qty."*".$$excess."*".$$woqny."*".$$rate."*".$$amount."*".$$pcs."*".$$colorsizetableid.""));
			}
		 }
		 //=============================
		 
		 
		
		//echo "10 ** $data_array_up2";die;
		 
		$rID=sql_update("wo_booking_dtls",$field_array_up1,$data_array_up1,"id","".$txt_update_dtls_id."",0);
		$txt_delivery_date=return_field_value( "max(delivery_date)", "wo_booking_dtls","booking_no = $txt_booking_no");
		$rID_1=execute_query("update  wo_booking_mst set supplier_id=$cbo_supplier_name,delivery_date='$txt_delivery_date' where booking_no=$txt_booking_no");
		//rID_1=execute_query("update  wo_booking_mst set supplier_id=$cbo_supplier_name,delivery_date='$txt_delivery_date' where booking_no=$txt_booking_no");
	    $rID_1=execute_query(bulk_update_sql_statement( "wo_trim_book_con_dtls", "id", $field_array_up2, $data_array_up2, $id_arr ));

		
		if($db_type==0)
		{
			if($rID && $rID_1){
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_1){
				oci_commit($con);  
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con);  
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
	
	
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if(count($trims_rec_arr[str_replace("'","",$txt_booking_no)])!=0)
		{
			echo "13**".str_replace("'","",$txt_booking_no);
			disconnect($con);die;	
		}
		
		//if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");//and b.item_group=".$txt_trim_group_id."
			    if($pi_number){
				    echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
					check_table_status( $_SESSION['menu_id'],0);
				  disconnect($con);  die;
			    }
			/*}else{*/
				$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");// and b.item_group_id=".$txt_trim_group_id."
			    if($recv_number){
				    echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
					check_table_status( $_SESSION['menu_id'],0);
				  disconnect($con);  die;
			    }
			//}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		//$rID=sql_delete("wo_booking_dtls",$field_array,$data_array,"booking_no","".$txt_booking_no."",1);
		$rID=execute_query("delete from  wo_booking_dtls where id=$txt_update_dtls_id and booking_no=$txt_booking_no");
		$rID1=execute_query("delete from  wo_trim_book_con_dtls where wo_trim_booking_dtls_id=$txt_update_dtls_id and booking_no=$txt_booking_no");
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);  
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con);  
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
		 
		
	}
}

if ($action=="trims_booking_popup")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
		function set_checkvalue()
		{
			if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
			else document.getElementById('chk_job_wo_po').value=0;
		}
		
		function js_set_value( str_data ) 
		{
			document.getElementById('txt_booking').value=str_data;
			parent.emailwindow.hide();
		}
    </script>

</head>
<body>
    <div align="center" style="width:100%;" >
    <input type="hidden" id="txt_booking" value="" />
    
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="1130" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                     <thead>
                           <th colspan="4"> </th>
                        	<th  >
                              <?
                               echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
                              ?>
                            </th>
                            <th colspan="5"></th>
                    </thead>
                    <thead>                	 
                        <th width="120" class="must_entry_caption">Company Name</th> 
                        <th width="150" >Buyer Name</th>
                        <th width="130">Supplier Name</th>
                        <th width="100">Booking No</th>
                        <th width="80">Job No</th>
                        <th width="100">File No</th>
                        <th width="100">Ref. No</th>
                        <th width="200"> Booking Date Range</th>
                         <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">WO Without PO</th>                
                    </thead>
        			<tr>
                    	<td> 
							<? 
								echo create_drop_down( "cbo_company_mst", 120, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'trims_booking_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
							?>
                        </td>
                         <td id="buyer_td">
                              <? 
                                echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --", $selected, "","" );
                               ?>	  
                    </td>
                   	<td>
                     <?
					 echo create_drop_down( "cbo_supplier_name", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=4 and   a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );
						//echo create_drop_down( "cbo_supplier_name", 172, "select id,supplier_name from lib_supplier where find_in_set(4,party_type) and status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );
					?>	
                     </td>
                     <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
                     <td><input name="txt_job" id="txt_job" class="text_boxes" style="width:100px"></td>
                     <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:80px"></td>
                    <td><input name="txt_ref" id="txt_ref" class="text_boxes" style="width:80px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
					 </td> 
            		 <td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_ref').value+'_'+document.getElementById('txt_job').value, 'create_booking_search_list_view', 'search_div', 'trims_booking_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
<?
     

?>
	
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
  exit();
}
if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if ($data[2]!=0) $supplier_id=" and a.supplier_id='$data[2]'"; else $supplier_id ="";
	//echo $data[11];
	if($db_type==0)
	{
	$booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[5]";	
	if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
	$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[5]";	
	if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	
	if($data[7]==4 || $data[7]==0)
		{
			if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]%'  $booking_year_cond  "; else $booking_cond="";
			if (str_replace("'","",$data[8])!="") $file_cond=" and c.file_no like '%$data[8]%'"; else $file_cond="";
			if (str_replace("'","",$data[10])!="") $ref_cond=" and c.grouping='$data[10]'"; else $ref_cond="";
			if (str_replace("'","",$data[11])!="") $job_cond=" and a.job_no like '%$data[11]%' "; else $job_cond="";
		}
    if($data[7]==1)
		{
			if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num ='$data[6]'   "; else $booking_cond="";
			if (str_replace("'","",$data[8])!="") $file_cond=" and c.file_no ='$data[8]'   "; else $file_cond="";
			if (str_replace("'","",$data[10])!="") $ref_cond=" and c.grouping='$data[10]'"; else $ref_cond="";
			if (str_replace("'","",$data[11])!="") $job_cond=" and a.job_no like '%$data[11]%' "; else $job_cond="";
		}
   if($data[7]==2)
		{
			if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[6]%'  $booking_year_cond  "; else $booking_cond="";
			if (str_replace("'","",$data[8])!="") $file_cond=" and c.file_no ='$data[8]'   "; else $file_cond="";
			if (str_replace("'","",$data[10])!="") $ref_cond=" and c.grouping='$data[10]'"; else $ref_cond="";
			if (str_replace("'","",$data[11])!="") $job_cond=" and a.job_no like '%$data[11]%' "; else $job_cond="";
		}
	if($data[7]==3)
		{
			if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]'  $booking_year_cond  "; else $booking_cond="";
			if (str_replace("'","",$data[8])!="") $file_cond=" and c.file_no ='$data[8]'   "; else $file_cond="";
			if (str_replace("'","",$data[10])!="") $ref_cond=" and c.grouping='$data[10]'"; else $ref_cond="";
			if (str_replace("'","",$data[11])!="") $job_cond=" and a.job_no like '%$data[11]%' "; else $job_cond="";
		}
	
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (1=>$comp,2=>$suplier);
	
	$sql_Data="select a.id,a.booking_no from  wo_booking_mst a , wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no  and b.po_break_down_id=c.id and  a.booking_type=2 and a.entry_form=43   $company  $buyer  $supplier_id $booking_date $booking_cond $job_cond $file_cond $ref_cond group by a.id,a.booking_no_prefix_num, a.booking_no,company_id,a.supplier_id,a.booking_date,a.delivery_date";
	$ResultData=sql_select($sql_Data);
	$all_book_id="";
	foreach($ResultData as $row)
	{
		if($all_book_id=="") $all_book_id=$row[csf("id")]; else  $all_book_id.=",".$row[csf("id")];
	}
	
		if($all_book_id!="")
		{
		$boIds=chop($all_book_id,','); $bo_cond_for_in="";
		$bo_ids=count(array_unique(explode(",",$all_book_id)));
			if($db_type==2 && $bo_ids>1000)
			{
				$bo_cond_for_in=" and (";
				$boIdsArr=array_chunk(explode(",",$boIds),999);
				foreach($boIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$bo_cond_for_in.=" a.id in($ids) or"; 
				}
				$bo_cond_for_in=chop($bo_cond_for_in,'or ');
				$bo_cond_for_in.=")";
			}
			else
			{
				$bo_cond_for_in=" and a.id in($boIds)";
			}
		}
						
	//if($booking_id_con!="") $booking_nos="and a.id in($all_book_id)";else $booking_nos="";
	
	if($data[9]==0)
	{
		$sql="select min(a.id) as id,a.booking_no_prefix_num, a.booking_no,company_id,a.job_no,a.supplier_id,a.booking_date,a.delivery_date,c.file_no,c.grouping from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no  and b.po_break_down_id=c.id and  a.booking_type=2 and a.entry_form=43 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $company  $buyer ".set_user_lavel_filtering(' and a.buyer_id','buyer_id')."  $supplier_id $booking_date $booking_cond $job_cond $file_cond $ref_cond group by a.booking_no_prefix_num,a.job_no, a.booking_no,company_id,a.supplier_id,a.booking_date,a.delivery_date,c.file_no,c.grouping order by id";
		echo  create_list_view("list_view", "Booking No,Company,Supplier,Booking Date,Delivery Date,Job No,File No,Ref. No", "120,100,100,100,100,100,100","900","420",0, $sql , "js_set_value", "booking_no", "", 1, "0,company_id,supplier_id,0,0,0,0,0", $arr , "booking_no_prefix_num,company_id,supplier_id,booking_date,delivery_date,job_no,file_no,grouping", '','','0,0,0,3,3,0','','');

	}
	else
	{
		 /*$sql="select min(a.id) as id,a.job_no,a.booking_no_prefix_num, a.booking_no,company_id,a.supplier_id,a.booking_date,a.delivery_date from wo_booking_mst a  where  a.booking_no not in ( select a.booking_no from  wo_booking_mst a , wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no  and b.po_break_down_id=c.id and  a.booking_type=2 and a.entry_form=43   $company  $buyer  $supplier_id $booking_date $booking_cond $job_cond $file_cond $ref_cond group by a.booking_no_prefix_num, a.booking_no,company_id,a.supplier_id,a.booking_date,a.delivery_date  ) and a.booking_type=2 and a.entry_form=43   $company  $buyer ".set_user_lavel_filtering(' and a.buyer_id','buyer_id')."  $supplier_id $booking_date $booking_cond group by a.booking_no_prefix_num, a.booking_no,a.job_no,company_id,a.supplier_id,a.booking_date,a.delivery_date order by id";*/
		 $sql="select min(a.id) as id,a.job_no,a.booking_no_prefix_num, a.booking_no,company_id,a.supplier_id,a.booking_date,a.delivery_date from wo_booking_mst a  where   a.booking_type=2 and a.entry_form=43 and a.status_active=1 and a.is_deleted=0  $company  $buyer ".set_user_lavel_filtering(' and a.buyer_id','buyer_id')."  $supplier_id $booking_date $booking_cond $bo_cond_for_in group by a.booking_no_prefix_num, a.booking_no,a.job_no,company_id,a.supplier_id,a.booking_date,a.delivery_date order by id";
		
		
		echo  create_list_view("list_view", "Booking No,Company,Supplier,Booking Date,Delivery Date,Job No", "120,100,100,100,100","700","420",0, $sql , "js_set_value", "booking_no", "", 1, "0,company_id,supplier_id,0,0,0,0", $arr , "booking_no_prefix_num,company_id,supplier_id,booking_date,delivery_date,job_no", '','','0,0,3,3,0,0','','');
	}
}

if($action=="terms_condition_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
    $_SESSION['page_permission']=$permission;
?>
	<script>
	var permission='<? echo $permission; ?>';
	
	function add_break_down_tr(i) 
	{
		var row_num=$('#tbl_termcondi_details tr').length-1;
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
		 
			 $("#tbl_termcondi_details tr:last").clone().find("input,select").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }              
				});  
			  }).end().appendTo("#tbl_termcondi_details");
			 $('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
			  $('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
			  $('#termscondition_'+i).val("");
			  $('#sltd_'+i).val(i);
			  //$('#sl_td').i
			  //alert(i)
			  //document.getElementById('sltd_'+i).innerHTML=i;
		}
	}

	function fn_deletebreak_down_tr(rowNo) 
	{   
		var numRow = $('table#tbl_termcondi_details tbody tr').length; 
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_termcondi_details tbody tr:last').remove();
		}
	}

	function fnc_fabric_booking_terms_condition( operation )
	{
		var row_num=$('#tbl_termcondi_details tr').length-1;
		var data_all="";
		for (var i=1; i<=row_num; i++)
		{
			
			if (form_validation('termscondition_'+i,'Term Condition')==false)
			{
				return;
			}
			
			data_all=data_all+get_submitted_data_string('txt_booking_no*termscondition_'+i,"../../../",i);
		}
		var data="action=save_update_delete_fabric_booking_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
		//freeze_window(operation);
		http.open("POST","trims_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_terms_condition_reponse;
	}

	function fnc_fabric_booking_terms_condition_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			if(reponse[0]==0 || reponse[0]==1)
			{
				parent.emailwindow.hide();
			}
		}
	}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<? echo load_freeze_divs ("../../../",$permission);  ?>
<fieldset>
<input type="hidden" id="txt_booking_no" name="txt_booking_no" value="<? echo str_replace("'","",$txt_booking_no) ?>"/>
        	<form id="termscondi_1" autocomplete="off">
           
            
            
            <table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
                	<thead>
                    	<tr>
                        	<th width="50">Sl</th><th width="530">Terms</th><th ></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							if ($i%2==0)  
							$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							$i++;
							?>
                            	<tr id="settr_1" align="center" bgcolor="<? echo $bgcolor;  ?>">
                                    <td >
                                    <? //echo $i;?>
                                    <input type="text" id="sltd_<? echo $i;?>"   name="sltd_<? echo $i;?>" style="width:100%;background-color:<? echo $bgcolor;  ?>"  value="<? echo $i; ?>"   /> 
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  /> 
                                    </td>
                                    <td> 
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
                                    </td>
                                </tr>
                            <?
						}
					}
					else
					{
					$data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1");// quotation_id='$data'
					foreach( $data_array as $row )
						{
							if ($i%2==0)  
							$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							$i++;
					?>
                    <tr id="settr_1" align="center" bgcolor="<? echo $bgcolor;  ?>">
                                    <td >
                                    <? // echo $i;?>
                                    <input type="text" id="sltd_<? echo $i;?>"   name="sltd_<? echo $i;?>" style="width:100%; background-color:<? echo $bgcolor;  ?>"  value="<? echo $i; ?>"   /> 
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  /> 
                                    </td>
                                    <td>
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> );" />
                                    </td>
                                </tr>
                    <? 
						}
					} 
					?>
                </tbody>
                </table>
                
                <table width="650" cellspacing="0" class="" border="0">
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
						        <?
									echo load_submit_buttons( $permission, "fnc_fabric_booking_terms_condition", 0,0 ,"reset_form('termscondi_1','','','','')",1) ; 
									?>
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

if($action=="show_trim_booking_report")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$order_uom_arr=return_library_array("select id,order_uom  from lib_item_group","id","order_uom");
	//$po_qnty_tot=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$order_file_no_arr=return_library_array("select po_number,file_no  from wo_po_break_down","po_number","file_no");
	$order_ref_no_arr=return_library_array("select po_number,grouping  from wo_po_break_down","po_number","grouping");
	?>
	<div style="width:1333px" align="center">       
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100"> 
              <? if($link==1){?>
               <img  src='../../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               <? }else{?>
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               <? }?>
               </td>
               <td width="1000">                                     
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php      
                                    echo $company_library[$cbo_company_name];
                              ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
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
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                                <strong>
								<? 
								if(str_replace("'","",$cbo_isshort)==2)
								{
								$isshort="";	
								}
								if(str_replace("'","",$cbo_isshort)==1)
								{
								$isshort="[Short]";	
								}
								if ($report_title !="")
								{
									echo $report_title." ".$isshort;
								} 
								else 
								{
									echo "Main Trims Booking ".$isshort;
								}  
								?> 
                                &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <font style="color:#F00">
								<? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> 
                                </font>
                                </strong>
                             </td> 
                            </tr>
                      </table>
                </td>
                 <td width="250" id="barcode_img_id"> 
                 
               </td>       
            </tr>
       </table>
		<?
		$booking_grand_total=0;
		$job_no="";
		$currency_id="";
		$nameArray_job=sql_select( "select distinct b.job_no  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no=$txt_booking_no"); 
        foreach ($nameArray_job as $result_job)
        {
			$job_no.=$result_job[csf('job_no')].", ";
		}
		$buyer_string="";
		
		$nameArray_buyer=sql_select( "select distinct a.buyer_name  from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no"); 
        foreach ($nameArray_buyer as $result_buy)
        {
			$buyer_string.=$buyer_name_arr[$result_buy[csf('buyer_name')]].",";
		}
		
		$po_no="";
		$po_qty="";
		$nameArray_job=sql_select( "select  b.po_number, sum(distinct b.po_quantity) as po_quantity   from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and b.status_active=1 and b.is_deleted=0  group by b.po_number order by b.po_number"); 
        foreach ($nameArray_job as $result_job)
        {
			$po_no.=$result_job[csf('po_number')].", ";
			$po_qty.=$result_job[csf('po_quantity')].", ";
		}
		
	
		
		$style_ref="";
		$nameArray_style=sql_select( "select distinct a.style_ref_no  from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no"); 
        foreach ($nameArray_style as $result_style)
        {
			$style_ref.=$result_style[csf('style_ref_no')].", ";
		}
       
	   
	    $nameArray=sql_select( "select a.booking_no,a.booking_date,a.pay_mode,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source,a.insert_date,a.update_date,a.remarks  from wo_booking_mst a where  a.booking_no=$txt_booking_no"); 
        foreach ($nameArray as $result)
        {
       
	   
			$sql_po= "select b.po_number,MIN(b.pub_shipment_date) pub_shipment_date, MIN(b.insert_date) as insert_date,b.shiping_status from wo_booking_dtls a,wo_po_break_down b  where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no group by b.po_number, b.shiping_status"; 
			$data_array_po=sql_select($sql_po);
			foreach ($data_array_po as $rows)
			{
				
				$daysInHand.=(datediff('d',date('d-m-Y',time()),$rows[csf('pub_shipment_date')])-1).",";
				$booking_date=$result[csf('update_date')];
				if($booking_date=="" || $booking_date=="0000-00-00 00:00:00")
				{
					$booking_date=$result[csf('insert_date')];
				}
				$WOPreparedAfter.=(datediff('d',$rows[csf('insert_date')],$booking_date)-1).",";
			
				if($rows[csf('shiping_status')]==1)
				{
				$shiping_status.= "FP".",";
				}
				else if($rows[csf('shiping_status')]==2)
				{
				$shiping_status.= "PS".",";
				}
				else if($rows[csf('shiping_status')]==3)
				{
				$shiping_status.= "FS".",";
				}
			
			}
	
	   $varcode_booking_no=$result[csf('booking_no')];
	   
	    ?>
       <table width="100%" style="border:1px solid black">                    	
            <tr>
                <td colspan="6" valign="top"></td>                             
            </tr>                                                
            <tr>
                <td width="110" style="font-size:12px"><b>Booking No </b>   </td>
                <td width="300">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="300">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100"><span style="font-size:16px"><b>Delivery Date</b></span></td>
                <td>:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>	
            </tr>
            <tr>
                <td width="110" style="font-size:12px"><b>Currency</b></td>
                <td width="300">:&nbsp;<? $currency_id=$result[csf('currency_id')]; echo $currency[$result[csf('currency_id')]]; ?></td>
                <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="300" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Source</b></td>
                <td>:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>
            </tr> 
            <tr>
                <td width="110" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="300">:&nbsp;<? 
				if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
					echo $company_library[$result[csf('supplier_id')]];
					}
					else{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
					}
				//echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td colspan="3">:&nbsp;<? echo $supplier_address_arr[$result[csf('supplier_id')]];?></td>
               	
            </tr> 
            <tr>
                <td width="110" style="font-size:12px"><b>Buyer</b>   </td>
                <td width="300">:&nbsp;
				<? 
				echo rtrim($buyer_string,", ");
				?> 
                </td>
                <td width="110" style="font-size:12px"><b>Style</b> </td>
                <td style="font-size:12px" colspan="3">:&nbsp;<? echo rtrim($style_ref,", "); ?> </td>
                 
               	
            </tr> 
            <tr>
                <td width="110" style="font-size:12px"><b>Job No</b>   </td>
                <td width="300">:&nbsp;
				<? 
				echo rtrim($job_no,", ");
				?> 
                </td>
                 
               	<td width="110" style="font-size:12px"><b>PO No</b> </td>
                <td style="font-size:12px" colspan="3">:&nbsp;<? echo rtrim($po_no,", "); ?> </td>
            </tr> 
              <tr>
               	<td width="110" style="font-size:12px"><b>PO Qty</b> </td>
                <td style="font-size:12px" colspan="5">:&nbsp;<? echo rtrim($po_qty,", "); ?> </td>
            </tr>
            <tr>
               <td width="110" style="font-size:12px"><b>WO Prepared After</b></td>
               <td width="300"> :&nbsp;<? echo rtrim($WOPreparedAfter,',').' Days' ?></td>
                
               <td width="100" style="font-size:12px"><b>Ship.days in Hand</b></td>
               <td width="300"> :&nbsp;<? echo rtrim($daysInHand,',').' Days'?></td>
                
               <td width="100" style="font-size:12px"><b>Ex-factory status</b></td>
               <td> :&nbsp;<? echo rtrim($shiping_status,','); ?></td>
            </tr> 
             <tr>
                
                <td width="110" style="font-size:12px"><b>File No</b>   </td>
                 <td>:&nbsp;
				<? 
				$po_data=explode(",",$po_no);
				$file_no='';$ref_no='';
				foreach($po_data as $po_id)
				{
					if($file_no=='')	$file_no=$order_file_no_arr[$po_id];else $file_no.=','.$order_file_no_arr[$po_id];
					if($ref_no=='')	$ref_no=$order_ref_no_arr[$po_id];else $ref_no.=','.$order_ref_no_arr[$po_id];
				}
				//$order_file_no_arr
				echo rtrim($file_no,',');
				?> 
                </td>
                <td width="110" style="font-size:12px"><b>Ref. No</b>   </td>
                 <td width="110">:&nbsp;
                  <?
                	echo rtrim($ref_no,',');
					?> 
                  </td>
                
                <td width="110" style="font-size:12px"><b>Remarks</b>   </td>
                <td>:&nbsp;
				<? 
				echo $result[csf('remarks')];
				?> 
                </td>
            </tr> 
             
            
        </table>  
        <br/>
		<?
        }
        ?>
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 order by trim_group "); 
        $nameArray_color=sql_select( "select  b.color_number_id , min(b.color_size_table_id) as  color_size_table_id from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.sensitivity=1 group by b.color_number_id order by color_size_table_id"); 
		if(count($nameArray_color)>0)
		{
        ?>
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="<? echo count($nameArray_color)+8; ?>" align="">
                <strong>As Per Garments Color</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand Supplier</strong> </td>
                <?  				
                foreach($nameArray_color  as $result_color)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('color_number_id')]];?></strong></td>
                <?	}    ?>				
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            $nameArray_item_description=sql_select( "select b.description, b.brand_supplier,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.sensitivity=1 and a.trim_group=".$result_item[csf('trim_group')]." group by b.description, b.brand_supplier order by trim_group "); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $trim_group[$result_item[csf('trim_group')]]; ?>
                </td>
                <? 
                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('description')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('brand_supplier')]; ?> </td>
                <?
                foreach($nameArray_color  as $result_color)
                {
					if($db_type==0)
					{
					
                $nameArray_color_size_qnty=sql_select( "select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.sensitivity=1 and a.trim_group=". $result_item[csf('trim_group')]." and b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='". $result_itemdescription[csf('brand_supplier')]."' and b.color_number_id=".$result_color[csf('color_number_id')].""); 
					}
					if($db_type==2)
					{
					
                $nameArray_color_size_qnty=sql_select( "select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.sensitivity=1 and a.trim_group=". $result_item[csf('trim_group')]." and nvl(b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('". $result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.color_number_id,0)=nvl(".$result_color[csf('color_number_id')].",0)"); 
					}
					
                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <? 
                if($result_color_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_color_size_qnty[csf('cons')],2);
                $item_desctiption_total += round($result_color_size_qnty[csf('cons')],2) ;
                if (array_key_exists($result_color[csf('color_number_id')], $color_tatal))
                {
                $color_tatal[$result_color[csf('color_number_id')]]+=$result_color_size_qnty[csf('cons')];
                }
                else
                {
                $color_tatal[$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('cons')]; 
                }
                }
                else echo "";
                ?>
                </td>
                <?   
                }
                }
                ?>
                <? 
                $amount_as_per_gmts_color =round($result_itemdescription[csf('amount')],4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($amount_as_per_gmts_color/$item_desctiption_total,8); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
				echo number_format($amount_as_per_gmts_color,4);
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="2"><strong> Item Total</strong></td>
                <?
                foreach($nameArray_color  as $result_color)
                {
                
                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_color[csf('color_number_id')]] !='')
                {
                echo number_format($color_tatal[$result_color[csf('color_number_id')]],2);  
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+7; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER GMTS COLOR END=========================================  -->
        
        <!--==============================================AS PER GMTS SIZE START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 order by trim_group "); 
        $nameArray_size=sql_select( "select  b.item_size  as gmts_sizes, min(b.color_size_table_id) as color_size_table_id from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.sensitivity=2 group by b.item_size order by color_size_table_id");
		if(count($nameArray_size)>0)
		{
        ?>
        
        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="<? echo count($nameArray_size)+8; ?>" align="">
                <strong>As Per Garments Size </strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand Supplier</strong> </td>
                <?  				
                foreach($nameArray_size  as $result_size)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $result_size[csf('gmts_sizes')];?></strong></td>
                <?	}    ?>				
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
            //$nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and trim_group=".$result_item[csf('trim_group')]." order by trim_group "); 
			$nameArray_item_description=sql_select( "select b.description, b.brand_supplier,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.sensitivity=2 and a.trim_group=".$result_item[csf('trim_group')]." group by b.description, b.brand_supplier"); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $trim_group[$result_item[csf('trim_group')]]; ?>
                </td>
                <? 
                $size_tatal=array();
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('description')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('brand_supplier')]; ?> </td>
                <?
                foreach($nameArray_size  as $result_size)
                {
					if($db_type==0)
					{
                $nameArray_size_size_qnty=sql_select( "select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.sensitivity=2 and a.trim_group=". $result_item[csf('trim_group')]." and b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='". $result_itemdescription[csf('brand_supplier')]."' and b.item_size='".$result_size[csf('gmts_sizes')]."'");
					}
					if($db_type==2)
					{
                $nameArray_size_size_qnty=sql_select( "select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.sensitivity=2 and a.trim_group=". $result_item[csf('trim_group')]." and nvl(b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('". $result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_size,0)=nvl('".$result_size[csf('gmts_sizes')]."',0)");
					}
                foreach($nameArray_size_size_qnty as $result_size_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <? 
                if($result_size_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_size_size_qnty[csf('cons')],2);
                $item_desctiption_total += $result_size_size_qnty[csf('cons')] ;
                if (array_key_exists($result_size[csf('gmts_sizes')], $size_tatal))
                {
                $size_tatal[$result_size[csf('gmts_sizes')]]+=$result_size_size_qnty[csf('cons')];
                }
                else
                {
                $size_tatal[$result_size[csf('gmts_sizes')]]=$result_size_size_qnty[csf('cons')]; 
                }
                }
                else echo "";
                ?>
                </td>
                <?   
                }
                }
                ?>
                
                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],8); ?> </td>
                <td style="border:1px solid black; text-align:right">

                <? 
                $amount_as_per_gmts_size = $item_desctiption_total*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_size,2);
                $total_amount_as_per_gmts_size+=$amount_as_per_gmts_size;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="2"><strong> Item Total</strong></td>
                <?
                foreach($nameArray_size  as $result_size)
                {
                
                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($size_tatal[$result_size[csf('gmts_sizes')]] !='')
                {
                echo number_format($size_tatal[$result_size[csf('gmts_sizes')]],2);  
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($size_tatal),2);  ?></td>
                 <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_size,2);
                $grand_total_as_per_gmts_size+=$total_amount_as_per_gmts_size;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+7; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_size,2); $booking_grand_total+=$grand_total_as_per_gmts_size; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER SIZE  END=========================================  -->
        
         <!--==============================================AS PER CONTRAST COLOR START=========================================  -->
		<?
		$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.sensitivity=3", "item_color", "color_number_id"  );
		
        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 order by trim_group "); 
		//echo "select  b.item_color as color_number_id,b.color_number_id as gmts_color, min(b.color_size_table_id) as  color_size_table_id  from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.sensitivity=3 group by b.item_color,b.color_number_id order by color_size_table_id ";
        $nameArray_color=sql_select( "select  b.item_color as color_number_id,b.color_number_id as gmts_color, min(b.color_size_table_id) as  color_size_table_id  from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.sensitivity=3 and b.cons > 0 group by b.item_color,b.color_number_id order by color_size_table_id "); 
		if(count($nameArray_color)>0)
		{
        ?>
        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="<? echo count($nameArray_color)+9; ?>" align="">
                <strong>Contrast Color</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black" rowspan="2"><strong>Sl</strong> </td>
                <td style="border:1px solid black" rowspan="2"><strong>Item Group</strong> </td>
                <td style="border:1px solid black" rowspan="2"><strong>Item Description</strong> </td>
                <td style="border:1px solid black" rowspan="2"><strong>Brand Supplier</strong> </td>
                <td style="border:1px solid black"><strong>Gmts Color</strong> </td>
                <?  				
                foreach($nameArray_color  as $result_color)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('gmts_color')]];//echo $color_library[$gmtcolor_library[$result_color[csf('color_number_id')]]];?></strong></td>
                <?	}    ?>				
                <td style="border:1px solid black" align="center" rowspan="2"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center" rowspan="2"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center" rowspan="2"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center" rowspan="2"><strong>Amount</strong></td>
            </tr>
            
            <tr>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <?  				
                foreach($nameArray_color  as $result_color)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('color_number_id')]];?></strong></td>
                <?	}    ?>				
                
            </tr>
            
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            //$nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and trim_group=".$result_item[csf('trim_group')]." order by trim_group "); 
			$nameArray_item_description=sql_select( "select b.description, b.brand_supplier,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.sensitivity=3 and a.trim_group=".$result_item[csf('trim_group')]." group by b.description, b.brand_supplier order by trim_group "); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $trim_group[$result_item[csf('trim_group')]]; ?>
                </td>
                <? 
                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('description')]; ?> </td>
                <td style="border:1px solid black" colspan="2"><? echo $result_itemdescription[csf('brand_supplier')]; ?> </td>
                <?
                foreach($nameArray_color  as $result_color)
                {
					if($db_type==0)
				    {
                $nameArray_color_size_qnty=sql_select( "select sum(b.cons) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.sensitivity=3 and a.trim_group=". $result_item[csf('trim_group')]." and b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='". $result_itemdescription[csf('brand_supplier')]."' and b.item_color=".$result_color[csf('color_number_id')]." and b.color_number_id=".$result_color[csf('gmts_color')]."");
					}
					if($db_type==2)
				    {
                $nameArray_color_size_qnty=sql_select( "select sum(b.cons) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.sensitivity=3 and a.trim_group=". $result_item[csf('trim_group')]." and nvl(b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('". $result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_color,0)=nvl(".$result_color[csf('color_number_id')].",0) and nvl(b.color_number_id,0)=nvl(".$result_color[csf('gmts_color')].",0)");
					}
                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <? 
                if($result_color_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_color_size_qnty[csf('cons')],2);
                $item_desctiption_total += $result_color_size_qnty[csf('cons')] ;
				
                if (array_key_exists($result_color[csf('color_number_id')], $color_tatal))
                {
                $color_tatal[$result_color[csf('color_number_id')]][$result_color[csf('gmts_color')]]+=$result_color_size_qnty[csf('cons')];
                }
                else
                {
                $color_tatal[$result_color[csf('color_number_id')]][$result_color[csf('gmts_color')]]=$result_color_size_qnty[csf('cons')]; 
                }
                }
                else echo "";
                ?>
                </td>
                <?   
                }
                }
                ?>
                
                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                 <td style="border:1px solid black; text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],8); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,2);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="3"><strong> Item Total</strong></td>
                <?
				$item_total=0;
                foreach($nameArray_color  as $result_color)
                {
                
                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_color[csf('color_number_id')]][$result_color[csf('gmts_color')]] !='')
                {
                echo number_format($color_tatal[$result_color[csf('color_number_id')]][$result_color[csf('gmts_color')]],2);  
				$item_total+=$color_tatal[$result_color[csf('color_number_id')]][$result_color[csf('gmts_color')]];
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format($item_total,2);  ?></td>
                <td style="border:1px solid black;text-align:center"></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+8; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER CONTRAST COLOR END=========================================  -->
        
        <!--==============================================AS PER GMTS Color & SIZE START=========================================  -->
		<?
		
		
        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 order by trim_group "); 
        $nameArray_size=sql_select( "select  b.item_size  as gmts_sizes, min(b.color_size_table_id) as color_size_table_id from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.sensitivity=4 group by b.item_size order by color_size_table_id");
	    //$nameArray_color=sql_select( "select distinct b.item_color as color_number_id,b.description, b.brand_supplier from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=4"); 

		if(count($nameArray_size)>0)
		{
        ?>
        
        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="<? echo count($nameArray_size)+10; ?>" align="">
                <strong>Color & size sensitive </strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td style="border:1px solid black"><strong>Gmts Color</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand Supplier</strong> </td>
                <?  				
                foreach($nameArray_size  as $result_size)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $result_size[csf('gmts_sizes')];?></strong></td>
                <?	}    ?>				
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
			
			$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.trim_group=".$result_item[csf('trim_group')]." and a.sensitivity=4", "item_color", "color_number_id"  );
			
			$nameArray_color=sql_select( "select  b.item_color,b.color_number_id,b.description, b.brand_supplier, min(b.color_size_table_id) as  color_size_table_id, avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.trim_group=".$result_item[csf('trim_group')]." and a.sensitivity=4 group by b.item_color,b.color_number_id,b.description, b.brand_supplier order by color_size_table_id");
			
            $nameArray_item_description=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and trim_group=".$result_item[csf('trim_group')]." order by trim_group "); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo   (count($nameArray_item_description)*count($nameArray_color)); ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo (count($nameArray_item_description)*count($nameArray_color)); ?>">
                <? echo $trim_group[$result_item[csf('trim_group')]]; ?>
                </td>
                <? 
				

                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
				foreach($nameArray_color as $result_color)
                {
					$item_desctiption_total=0;
					?>
					<td style="border:1px solid black"><? echo $color_library[$result_color[csf('item_color')]]; ?> </td>
                    <td style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; ?> </td>
					<td style="border:1px solid black" ><? echo $result_color[csf('description')]; ?> </td>
					<td style="border:1px solid black" ><? echo $result_color[csf('brand_supplier')]; ?> </td>
					<?
					foreach($nameArray_size  as $result_size)
					{
						if($db_type==0)
				        {
						$nameArray_size_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.sensitivity=4 and a.trim_group=". $result_item[csf('trim_group')]." and  b.description='". $result_color[csf('description')]."' and b.brand_supplier='".$result_color[csf('brand_supplier')]."'  and b.item_size='".$result_size[csf('gmts_sizes')]."' and b.item_color=".$result_color[csf('item_color')]." and b.color_number_id=".$result_color[csf('color_number_id')]."");
						}
						if($db_type==2)
				        {
						$nameArray_size_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.sensitivity=4 and a.trim_group=". $result_item[csf('trim_group')]." and nvl( b.description,0)=nvl('". $result_color[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$result_color[csf('brand_supplier')]."',0)  and nvl(b.item_size,0)=nvl('".$result_size[csf('gmts_sizes')]."',0) and nvl(b.item_color,0)=nvl(".$result_color[csf('item_color')].",0) and nvl(b.color_number_id,0)=nvl(".$result_color[csf('color_number_id')].",0)");
						}
						foreach($nameArray_size_size_qnty as $result_size_size_qnty)
						{
							?>
							<td style="border:1px solid black; text-align:right">
							<? 
							if($result_size_size_qnty[csf('cons')]!= "")
							{
							echo number_format($result_size_size_qnty[csf('cons')],2);
							$item_desctiption_total += $result_size_size_qnty[csf('cons')] ;
							
							if (array_key_exists($result_size[csf('gmts_sizes')], $color_tatal))
							{
							$color_tatal[$result_size[csf('gmts_sizes')]]+=$result_size_size_qnty[csf('cons')];
							}
							else
							{
							$color_tatal[$result_size[csf('gmts_sizes')]]=$result_size_size_qnty[csf('cons')]; 
							}
							}
							else echo "";
							?>
							</td>
							<?   
						}
					}
					?>
					<td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
					<td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
					<td style="border:1px solid black; text-align:right">
					<? 
					$rate =$result_color[csf('amount')]/$item_desctiption_total;;
					echo number_format($rate,2); 
					?>
                     </td>
					<td style="border:1px solid black; text-align:right">
					<? 
					//$amount_as_per_gmts_color = $item_desctiption_total*  $result_color[csf('rate')];
					$amount_as_per_gmts_color =$result_color[csf('amount')];
					echo number_format($amount_as_per_gmts_color,2);
					$total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
					?>
					</td>
				</tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="6"><strong> Item Total</strong></td>
                <?
                foreach($nameArray_size  as $result_size)
                {
                
                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_size[csf('gmts_sizes')]] !='')
                {
                echo number_format($color_tatal[$result_size[csf('gmts_sizes')]],2);  
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
			
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+9; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER Color & SIZE  END=========================================  -->
        
        
         <!--==============================================NO NENSITIBITY START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 order by trim_group "); 
        //$nameArray_color=sql_select( "select distinct b.color_number_id from wo_trims_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=1"); 
		$nameArray_color= array();
		if(count($nameArray_item)>0)
		{
        ?>
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="8" align="">
                <strong>No Sensitivity</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand Supplier</strong> </td>
                 <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td align="center" style="border:1px solid black"><strong> Qnty</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
				
				$nameArray_item_description=sql_select( "select  b.description, b.brand_supplier,b.item_color,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.trim_group=".$result_item[csf('trim_group')]." and a.sensitivity=0 group by b.description, b.brand_supplier,b.item_color"); 
				
            //$nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and trim_group=".$result_item[csf('trim_group')]." order by trim_group "); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">

                <? echo $trim_group[$result_item[csf('trim_group')]]; ?>
                </td>
                <? 
                $color_tatal=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('description')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('brand_supplier')]; ?> </td>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('item_color')]]; ?> </td>
                <?
                //$nameArray_color_size_qnty=sql_select( "select sum(b.cons) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=0 and a.trim_group=". $result_item['trim_group']." and a.description='". $result_itemdescription['description']."'");
				/*if($db_type==0)
				{
				$nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls     where    booking_no=$txt_booking_no and sensitivity=0 and trim_group=". $result_item[csf('trim_group')]." and description='". $result_itemdescription[csf('description')]."'"); 
				}
                  
				if($db_type==2)
				{
					echo "select sum(wo_qnty) as cons from wo_booking_dtls     where    booking_no=$txt_booking_no and sensitivity=0 and trim_group=". $result_item[csf('trim_group')]." and nvl(description,0)=nvl('". $result_itemdescription[csf('description')]."',0)";
				$nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls     where    booking_no=$txt_booking_no and sensitivity=0 and trim_group=". $result_item[csf('trim_group')]." and nvl(description,0)=nvl('". $result_itemdescription[csf('description')]."',0)"); 
				}*/
				
				if($db_type==0)
				        {
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.sensitivity=0 and a.trim_group=". $result_item[csf('trim_group')]." and  b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='".$result_itemdescription[csf('brand_supplier')]."' and b.item_color='".$result_itemdescription[csf('item_color')]."'");
						}
						if($db_type==2)
				        {
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.sensitivity=0 and a.trim_group=". $result_item[csf('trim_group')]." and nvl( b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_color,0)=nvl('".$result_itemdescription[csf('item_color')]."',0)");
						}
                          
                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <? 
                if($result_color_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_color_size_qnty[csf('cons')],2);
                $item_desctiption_total += $result_color_size_qnty[csf('cons')] ;
                $color_tatal+=$result_color_size_qnty[csf('cons')];
                }
                else echo "";
                ?>
                </td>
                <?   
                }
                ?>
                
                <td style="border:1px solid black; text-align:center "><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],8); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,2);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="3"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal !='')
                {
                echo number_format($color_tatal,2);  
                }
                ?>
                </td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="8"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <?
		//print_r($color_tatal);
		}
		?>
        <!--==============================================NO NENSITIBITY END=========================================  -->
       &nbsp;
       <?
	   $mcurrency="";
	   $dcurrency="";
	   if($currency_id==1)
	   {
		$mcurrency='Taka';
		$dcurrency='Paisa'; 
	   }
	   if($currency_id==2)

	   {
		$mcurrency='USD';
		$dcurrency='CENTS'; 
	   }
	   if($currency_id==3)
	   {
		$mcurrency='EURO';
		$dcurrency='CENTS'; 
	   }
	   ?>
       <table  width="100%" class="rpt_table"  border="1" cellpadding="0" cellspacing="0" rules="all">
       <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount</th><td width="30%" style="border:1px solid black; text-align:right"><? echo number_format($booking_grand_total,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</th><td width="30%" style="border:1px solid black;"><? echo number_to_words(def_number_format($booking_grand_total,2,""),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
          &nbsp;
        <table width="100%">
        <tr>
        <td width="49%">
        <? echo get_spacial_instruction($txt_booking_no);?>
    </td>
    <td width="2%"></td>
    
    <td width="49%">
    <?
	//str_replace("'","",$show_comment);
	
	if(str_replace("'","",$show_comment)==1)
	{
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" >
                <thead>
                    <th width="40">SL</th>
                    <th width="150">Item</th>
                    <th width="150">PO No</th>
                    <th width="150">Pre-Cost Value</th>
                    <th width="">WO Value</th>
                  
                </thead>
       <tbody>
       <?
					$sql_lib_item_group_array=array();
					$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
					foreach($sql_lib_item_group as $row_sql_lib_item_group)
					{
						$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
						$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
						$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
					}
					
					
					$sql="select job_no,po_break_down_id,pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls where booking_no=$txt_booking_no and status_active=1 and is_deleted=0 group by job_no,po_break_down_id,pre_cost_fabric_cost_dtls_id,trim_group";
					$exchange_rate=return_field_value("exchange_rate", " wo_booking_mst", "booking_no=".$txt_booking_no."");
					$i=1;
					$total_amount=0;
                    $nameArray=sql_select( $sql );
                    foreach ($nameArray as $selectResult)
                    {
						$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$selectResult[csf('job_no')]."'");
						
						if($costing_per==1)
						{
							$costing_per_qty=12;
						}
						else if($costing_per==2)
						{
							$costing_per_qty=1;
						}
						else if($costing_per==3)
						{
							$costing_per_qty=24;
						}
						else if($costing_per==4)
						{
							$costing_per_qty=36;
						}
						else if($costing_per==5)
						{
							$costing_per_qty=48;
						}
						
						$sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='".$selectResult[csf('po_break_down_id')]."'  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty");
						
						list($sql_po_qty_row)=$sql_po_qty;
						$po_qty=$sql_po_qty_row[csf('order_quantity_set')];
						
						$sql_cons_data=sql_select("select a.rate,b.cons from wo_pre_cost_trim_cost_dtls a , wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id and  a.id='".$selectResult[csf('pre_cost_fabric_cost_dtls_id')]."' and b.po_break_down_id='".$selectResult[csf('po_break_down_id')]."' and a.is_deleted=0  and a.status_active=1");
						list($sql_cons_data_row)=$sql_cons_data;
						$pre_cons=$sql_cons_data_row[csf('cons')];
						$pre_rate=$sql_cons_data_row[csf('rate')];
						$pre_req_qnty=def_number_format(($pre_cons*($po_qty/$costing_per_qty))/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],5,"");
						$pre_amount=$pre_req_qnty*$pre_rate;
						
						$sql_cu_woq=sql_select("select sum(amount) as amount  from wo_booking_dtls where po_break_down_id='".$selectResult[csf('po_break_down_id')]."' and pre_cost_fabric_cost_dtls_id='".$selectResult[csf('pre_cost_fabric_cost_dtls_id')]."'  and  booking_type=2 and status_active=1 and is_deleted=0");
						list($sql_cu_woq_row)=$sql_cu_woq;
						$cu_woq_amount=$sql_cu_woq_row[csf('amount')];
	   ?>
                    <tr>
                    <td width="40"><? echo $i;?></td>
                    <td width="150">
					<? echo $trim_group[$selectResult[csf('trim_group')]];?> 
                    </td>
                    <td width="150">
					<? echo $po_number[$selectResult[csf('po_break_down_id')]];?> 
                    </td>
                    <td width="150" align="right">
                     <? echo $pre_amount; ?>
                    </td>
                    <td width="" align="right">
                    <? echo number_format($cu_woq_amount/$exchange_rate,5);?>
                    </td>
                    
                    </tr>
	   <?
	   $i++;
					}
       ?>
	</tbody>
    </table>
    <?
	}
	?>
    </td>
    </tr>
    </table>
                
    </div>
    <div>
		<?
        	echo signature_table(2, $cbo_company_name, "1330px");
        ?>
    </div>
    
	<? if($link==1){?>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
        <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
    <? 
		}
		else
		{
    ?>
		<script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <? }?>
    
    <script>
		fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
	</script>
  
<?
exit();
}

if($action=="show_trim_booking_report1")
{
	
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$order_uom_arr=return_library_array("select id,order_uom  from lib_item_group","id","order_uom");
	$order_file_no_arr=return_library_array("select po_number,file_no  from wo_po_break_down","po_number","file_no");
	$order_ref_no_arr=return_library_array("select po_number,grouping  from wo_po_break_down","po_number","grouping");
	//$po_qnty_tot=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<div style="width:1333px" align="center">       
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100"> 
              <? if($link==1){?>
               <img  src='../../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               <? }else{?>
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               <? }?>
               </td>
               <td width="1000">                                     
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php      
                                    echo $company_library[$cbo_company_name];
                              ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
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
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                                 <strong>
								<? 
								if(str_replace("'","",$cbo_isshort)==2)
								{
								$isshort="";	
								}
								if(str_replace("'","",$cbo_isshort)==1)
								{
								$isshort="[Short]";	
								}
								if ($report_title !="")
								{
									echo $report_title." ".$isshort;
								} 
								else 
								{
									echo "Main Trims Booking ".$isshort;
								}  
								?> 
                                &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <font style="color:#F00">
								<? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> 
                                </font>
                                </strong>
                             </td> 
                            </tr>
                      </table>
                </td> 
                 <td width="250" id="barcode_img_id"> 
                 
               </td>         
            </tr>
       </table>
		<?
		
		$booking_grand_total=0;
		$job_no="";
		$currency_id="";
		$nameArray_job=sql_select( "select distinct b.job_no  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no=$txt_booking_no"); 
        foreach ($nameArray_job as $result_job)
        {

			$job_no.=$result_job[csf('job_no')].", ";
		}
		$buyer_string="";
		
		$nameArray_buyer=sql_select( "select distinct a.buyer_name  from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no"); 
        foreach ($nameArray_buyer as $result_buy)
        {
			$buyer_string.=$buyer_name_arr[$result_buy[csf('buyer_name')]].",";
		}
		
		$po_no="";
		$nameArray_job=sql_select( "select distinct b.po_number  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no"); 
        foreach ($nameArray_job as $result_job)
        {
			$po_no.=$result_job[csf('po_number')].", ";
		}
		$style_ref="";
		$nameArray_style=sql_select( "select distinct a.style_ref_no  from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no"); 
        foreach ($nameArray_style as $result_style)
        {
			$style_ref.=$result_style[csf('style_ref_no')].", ";
		}
        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source,a.remarks,a.pay_mode  from wo_booking_mst a where  a.booking_no=$txt_booking_no"); 
        foreach ($nameArray as $result)
        {
			$varcode_booking_no=$result[csf('booking_no')];
        ?>
       <table width="100%" style="border:1px solid black">                    	
            <tr>
                <td colspan="6" valign="top"></td>                             
            </tr>                                                
            <tr>
                <td width="100" style="font-size:12px"><b>Booking No </b>   </td>
                <td width="110">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100"><span style="font-size:16px"><b>Delivery Date</b></span></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>	
            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Currency</b></td>
                <td width="110">:&nbsp;<? $currency_id=$result[csf('currency_id')]; echo $currency[$result[csf('currency_id')]]; ?></td>
                <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Source</b></td>
                <td  width="110" >:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>
            </tr> 
            <tr>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<? 
				if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
					echo $company_library[$result[csf('supplier_id')]];
					}
					else{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
					}
				//echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td  colspan="3">:&nbsp;<? echo $supplier_address_arr[$result[csf('supplier_id')]];?></td>
               	
            </tr> 
            <tr>
                <td width="100" style="font-size:12px"><b>Buyer</b>   </td>
                <td width="110">:&nbsp;
				<? 
				echo rtrim($buyer_string,", ");
				?> 
                </td>
                <td width="110" style="font-size:12px"><b>Style</b> </td>
                <td  width="100" style="font-size:12px" colspan="3">:&nbsp;<? echo rtrim($style_ref,", "); ?> </td>
                 
               	
            </tr> 
            <tr>
                <td width="100" style="font-size:12px"><b>Job No</b>   </td>
                <td width="110">:&nbsp;
				<? 
				echo rtrim($job_no,", ");
				?> 
                </td>
                 
               	<td width="110" style="font-size:12px"><b>PO No</b> </td>
                <td  width="100" style="font-size:12px" colspan="3">:&nbsp;<? echo rtrim($po_no,", "); ?> </td>
            </tr> 
             <tr>
                <td width="100" style="font-size:12px"><b>File No</b> </td>
                 <td>:&nbsp;
				<? 
				$po_data=explode(",",$po_no);
				$file_no='';$ref_no='';
				foreach($po_data as $po_id)
				{
					if($file_no=='')	$file_no=$order_file_no_arr[$po_id];else $file_no.=','.$order_file_no_arr[$po_id];
					if($ref_no=='')	$ref_no=$order_ref_no_arr[$po_id];else $ref_no.=','.$order_ref_no_arr[$po_id];
				}
				//$order_file_no_arr
				echo rtrim($file_no,',');
				?> 
                </td>
                
                <td width="100" style="font-size:12px"><b>Remarks</b>   </td>
                <td width="110">:&nbsp;
				<? 
				echo $result[csf('remarks')];
				?> 
                </td>
                <td width="110" style="font-size:12px"><b>Pay Mode</b> </td>
                <td  width="100" style="font-size:12px" >:&nbsp;<? echo $pay_mode[$result[csf('pay_mode')]]; ?> </td> 
               	
            </tr> 
            <tr>
            <td>Ref. No </td> <td colspan="5">:&nbsp; <? 
				echo rtrim($ref_no,',');
				?> </td>
            </tr>
        </table>  
        <br/>
		<?
        }
        ?>
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?
		
		$nameArray_job_po=sql_select( "select a.job_no,a.po_break_down_id,sum(distinct b.po_quantity) as po_quantity from wo_booking_dtls a, wo_po_break_down b  where a.booking_no=$txt_booking_no and a.po_break_down_id=b.id and b.status_active=1 and b.is_deleted=0  group by a.job_no,a.po_break_down_id order by a.job_no,a.po_break_down_id "); 
		foreach($nameArray_job_po as $nameArray_job_po_row)
		{
		
        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]."  and sensitivity=1 order by trim_group "); 
        $nameArray_color=sql_select( "select  b.color_number_id, min(b.color_size_table_id) as  color_size_table_id from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=1 group by b.color_number_id order by color_size_table_id"); 
		if(count($nameArray_color)>0)
		{
        ?>
        &nbsp;
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
           <tr>
                <td colspan="<? echo count($nameArray_color)+8; ?>" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO Qty:" .$nameArray_job_po_row[csf('po_quantity')];?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="<? echo count($nameArray_color)+8; ?>" align="">
                <strong>As Per Garments Color</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand Supplier</strong> </td>
                <?  				
                foreach($nameArray_color  as $result_color)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('color_number_id')]];?></strong></td>
                <?	}    ?>				
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            $nameArray_item_description=sql_select( "select b.description, b.brand_supplier,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=1 and a.trim_group=".$result_item[csf('trim_group')]." group by b.description, b.brand_supplier order by trim_group "); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $trim_group[$result_item[csf('trim_group')]]; ?>
                </td>
                <? 
                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('description')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('brand_supplier')]; ?> </td>
                <?
                foreach($nameArray_color  as $result_color)
                {
					if($db_type==0)
					{
					
                $nameArray_color_size_qnty=sql_select( "select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=1 and a.trim_group=". $result_item[csf('trim_group')]." and b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='". $result_itemdescription[csf('brand_supplier')]."' and b.color_number_id=".$result_color[csf('color_number_id')].""); 
					}
					if($db_type==2)
					{
					
              $nameArray_color_size_qnty=sql_select( "select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=1 and a.trim_group=". $result_item[csf('trim_group')]." and nvl(b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('". $result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.color_number_id,0)=nvl(".$result_color[csf('color_number_id')].",0)"); 
					}
					
                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <? 
                if($result_color_size_qnty[csf('cons')]!= "")
                {
					echo number_format($result_color_size_qnty[csf('cons')],2);
					$item_desctiption_total += $result_color_size_qnty[csf('cons')] ;
					if (array_key_exists($result_color[csf('color_number_id')], $color_tatal))
					{
						$color_tatal[$result_color[csf('color_number_id')]]+=$result_color_size_qnty[csf('cons')];
					}
					else
					{
						$color_tatal[$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('cons')]; 
					}
                }
                else echo "";
                ?>
                </td>
                <?   
                }
                }
                ?>
                
                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],8); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,2);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="2"><strong> Item Total</strong></td>
                <?
                foreach($nameArray_color  as $result_color)
                {
                
                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_color[csf('color_number_id')]] !='')
                {
                echo number_format($color_tatal[$result_color[csf('color_number_id')]],2);  
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+7; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		
		}
		?>
        <!--==============================================AS PER GMTS COLOR END=========================================  -->
        
        <!--==============================================AS PER GMTS SIZE START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=2 order by trim_group "); 
        $nameArray_size=sql_select( "select  b.item_size  as gmts_sizes, min(b.color_size_table_id) as color_size_table_id from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.sensitivity=2 group by b.item_size order by color_size_table_id");
		if(count($nameArray_item)>0)
		{
        ?>
        
        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="<? echo count($nameArray_size)+8; ?>" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO Qty:" .$nameArray_job_po_row[csf('po_quantity')];?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="<? echo count($nameArray_size)+8; ?>" align="">
                <strong>As Per Garments Size </strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand Supplier</strong> </td>
                <?  				
                foreach($nameArray_size  as $result_size)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $result_size[csf('gmts_sizes')];?></strong></td>
                <?	}    ?>				
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_size=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
            //$nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and trim_group=".$result_item[csf('trim_group')]." order by trim_group "); 
			$nameArray_item_description=sql_select( "select b.description, b.brand_supplier,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=2 and a.trim_group=".$result_item[csf('trim_group')]." group by b.description, b.brand_supplier"); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $trim_group[$result_item[csf('trim_group')]]; ?>
                </td>
                <? 
                $size_tatal=array();
               $total_amount_as_per_gmts_color=0;$total_amount_as_per_gmts_size=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0; 
                ?>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('description')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('brand_supplier')]; ?> </td>
                <?
                foreach($nameArray_size  as $result_size)
                {
					if($db_type==0)
					{
						//echo "select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=2 and a.trim_group=". $result_item[csf('trim_group')]." and b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='". $result_itemdescription[csf('brand_supplier')]."' and b.item_size='".$result_size[csf('gmts_sizes')]."'";
                $nameArray_size_size_qnty=sql_select( "select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=2 and a.trim_group=". $result_item[csf('trim_group')]." and b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='". $result_itemdescription[csf('brand_supplier')]."' and b.item_size='".$result_size[csf('gmts_sizes')]."'");
					}
					if($db_type==2)
					{
                $nameArray_size_size_qnty=sql_select( "select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=2 and a.trim_group=". $result_item[csf('trim_group')]." and nvl(b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('". $result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_size,0)=nvl('".$result_size[csf('gmts_sizes')]."',0)");
					}
                foreach($nameArray_size_size_qnty as $result_size_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <? 
                if($result_size_size_qnty[csf('cons')]!= "")
                {
					echo number_format($result_size_size_qnty[csf('cons')],2);
					$item_desctiption_total += $result_size_size_qnty[csf('cons')] ;
					if (array_key_exists($result_size[csf('gmts_sizes')], $size_tatal))
					{
						$size_tatal[$result_size[csf('gmts_sizes')]]+=$result_size_size_qnty[csf('cons')];
					}
					else
					{
						$size_tatal[$result_size[csf('gmts_sizes')]]=$result_size_size_qnty[csf('cons')]; 
					}
                }
                else echo "";
                ?>
                </td>
                <?   
                }
                }
                ?>
                
                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],8); ?> </td>
                <td style="border:1px solid black; text-align:right">

                <? 
                $amount_as_per_gmts_size = $item_desctiption_total*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_size,2);
                $total_amount_as_per_gmts_size+=$amount_as_per_gmts_size;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="2"><strong> Item Total</strong></td>
                <?
                foreach($nameArray_size  as $result_size)
                {
                
                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($size_tatal[$result_size[csf('gmts_sizes')]] !='')
                {
                	echo number_format($size_tatal[$result_size[csf('gmts_sizes')]],2);  
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($size_tatal),2);  ?></td>
                 <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_size,2);
                $grand_total_as_per_gmts_size+=$total_amount_as_per_gmts_size;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+7; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_size,2); $booking_grand_total+=$grand_total_as_per_gmts_size; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER SIZE  END=========================================  -->
        
         <!--==============================================AS PER CONTRAST COLOR START=========================================  -->
		<?
		$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.sensitivity=3", "item_color", "color_number_id"  );
		
        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=3 order by trim_group "); 
		
        $nameArray_color=sql_select( "select  b.item_color as color_number_id, b.color_number_id as gmts_color, min(b.color_size_table_id) as  color_size_table_id from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=3 and b.cons >0 group by b.item_color ,b.color_number_id order by color_size_table_id "); 
		if(count($nameArray_color)>0)
		{
        ?>
        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
           <tr>
                <td colspan="<? echo count($nameArray_color)+9; ?>" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO Qty:" .$nameArray_job_po_row[csf('po_quantity')];?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="<? echo count($nameArray_color)+9; ?>" align="">
                <strong>Contrast Color</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black" rowspan="2"><strong>Sl</strong> </td>
                <td style="border:1px solid black" rowspan="2"><strong>Item Group</strong> </td>
                <td style="border:1px solid black" rowspan="2"><strong>Item Description</strong> </td>
                <td style="border:1px solid black" rowspan="2"><strong>Brand Supplier</strong> </td>
                <td style="border:1px solid black"><strong>Gmts Color</strong> </td>
                <?  				
                foreach($nameArray_color  as $result_color)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('gmts_color')]];?></strong></td>
                <?	}    ?>				
                <td style="border:1px solid black" align="center" rowspan="2"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center" rowspan="2"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center" rowspan="2"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center" rowspan="2"><strong>Amount</strong></td>
            </tr>
             <tr>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <?  				
                foreach($nameArray_color  as $result_color)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('color_number_id')]];?></strong></td>
                <?	}    ?>				
               
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            //$nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and trim_group=".$result_item[csf('trim_group')]." order by trim_group "); 
			$nameArray_item_description=sql_select( "select b.description, b.brand_supplier,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=3 and a.trim_group=".$result_item[csf('trim_group')]." group by b.description, b.brand_supplier order by trim_group "); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $trim_group[$result_item[csf('trim_group')]]; ?>
                </td>
                <? 
                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('description')]; ?> </td>
                <td style="border:1px solid black" colspan="2"><? echo $result_itemdescription[csf('brand_supplier')]; ?> </td>
                <?
                foreach($nameArray_color  as $result_color)
                {
					if($db_type==0)
				    {
                $nameArray_color_size_qnty=sql_select( "select sum(b.cons) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=3 and a.trim_group=". $result_item[csf('trim_group')]." and b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='". $result_itemdescription[csf('brand_supplier')]."' and b.item_color=".$result_color[csf('color_number_id')]." and b.color_number_id=".$result_color[csf('gmts_color')]."");
					}
					if($db_type==2)
				    {
                $nameArray_color_size_qnty=sql_select( "select sum(b.cons) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=3 and a.trim_group=". $result_item[csf('trim_group')]." and nvl(b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('". $result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_color,0)=nvl(".$result_color[csf('color_number_id')].",0) and nvl(b.color_number_id,0)=nvl(".$result_color[csf('gmts_color')].",0)");
					}
                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <? 
                if($result_color_size_qnty[csf('cons')]!= "")
                {
					echo number_format($result_color_size_qnty[csf('cons')],2);
					$item_desctiption_total += $result_color_size_qnty[csf('cons')] ;
					
					if (array_key_exists($result_color[csf('color_number_id')], $color_tatal))
					{
						$color_tatal[$result_color[csf('color_number_id')]][$result_color[csf('gmts_color')]]+=$result_color_size_qnty[csf('cons')];
					}
					else
					{
						$color_tatal[$result_color[csf('color_number_id')]][$result_color[csf('gmts_color')]]=$result_color_size_qnty[csf('cons')]; 
					}
                }
                else echo "";
                ?>
                </td>
                <?   
                }
                }
                ?>
                
                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
                 <td style="border:1px solid black; text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],8); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,2);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="3"><strong> Item Total</strong></td>
                <?
				$item_total=0;
                foreach($nameArray_color  as $result_color)
                {
                
                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_color[csf('color_number_id')]][$result_color[csf('gmts_color')]] !='')
                {
                echo number_format($color_tatal[$result_color[csf('color_number_id')]][$result_color[csf('gmts_color')]],2);  
				$item_total+=$color_tatal[$result_color[csf('color_number_id')]][$result_color[csf('gmts_color')]];
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format($item_total,2);  ?></td>
                <td style="border:1px solid black;text-align:center"></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+8; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER CONTRAST COLOR END=========================================  -->
        
        <!--==============================================AS PER GMTS Color & SIZE START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=4 order by trim_group "); 
        $nameArray_size=sql_select( "select  b.item_size  as gmts_sizes, min(b.color_size_table_id) as color_size_table_id  from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=4 group by b.item_size order by color_size_table_id");
	   

		if(count($nameArray_size)>0)
		{
        ?>
        
        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
             <tr>
                <td colspan="<? echo count($nameArray_size)+10;?>" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO Qty:" .$nameArray_job_po_row[csf('po_quantity')];?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="<? echo count($nameArray_size)+10; ?>" align="">
                <strong>Color & size sensitive </strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                 <td style="border:1px solid black"><strong>Gmts Color</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand Supplier</strong> </td>
                <?  				
                foreach($nameArray_size  as $result_size)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $result_size[csf('gmts_sizes')];?></strong></td>
                <?	}    ?>				
                <td style="border:1px solid black" align="center"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
			$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.trim_group=".$result_item[csf('trim_group')]." and a.sensitivity=4", "item_color", "color_number_id"  );
			 /*$nameArray_color=sql_select( "select distinct b.item_color as color_number_id,b.description, b.brand_supplier from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.trim_group=".$result_item[csf('trim_group')]." and a.sensitivity=4"); */
			 $nameArray_color=sql_select( "select  b.item_color,b.color_number_id,b.description, b.brand_supplier, min(b.color_size_table_id) as  color_size_table_id,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.trim_group=".$result_item[csf('trim_group')]." and a.sensitivity=4 group by b.item_color,b.color_number_id,b.description, b.brand_supplier order by color_size_table_id"); 
			 
            $nameArray_item_description=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=4 and trim_group=".$result_item[csf('trim_group')]." order by trim_group "); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo   (count($nameArray_item_description)*count($nameArray_color)); ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo (count($nameArray_item_description)*count($nameArray_color)); ?>">
                <? echo $trim_group[$result_item[csf('trim_group')]]; ?>
                </td>
                <? 
				

                $color_tatal=array();
                $total_amount_as_per_gmts_color=0;
				foreach($nameArray_color as $result_color)
                {
					$item_desctiption_total=0;
					?>
					<td style="border:1px solid black"><? echo $color_library[$result_color[csf('item_color')]]; ?> </td>
                    <td style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; ?> </td>
					<td style="border:1px solid black" ><? echo $result_color[csf('description')]; ?> </td>
					<td style="border:1px solid black" ><? echo $result_color[csf('brand_supplier')]; ?> </td>
					<?
					foreach($nameArray_size  as $result_size)
					{
						if($db_type==0)
				        {
						$nameArray_size_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=4 and a.trim_group=". $result_item[csf('trim_group')]." and  b.description='". $result_color[csf('description')]."' and b.brand_supplier='".$result_color[csf('brand_supplier')]."'  and b.item_size='".$result_size[csf('gmts_sizes')]."' and b.item_color=".$result_color[csf('item_color')]." and b.color_number_id=".$result_color[csf('color_number_id')]."");
						}
						if($db_type==2)
				        {
						$nameArray_size_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=4 and a.trim_group=". $result_item[csf('trim_group')]." and nvl( b.description,0)=nvl('". $result_color[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$result_color[csf('brand_supplier')]."',0)  and nvl(b.item_size,0)=nvl('".$result_size[csf('gmts_sizes')]."',0) and nvl(b.item_color,0)=nvl(".$result_color[csf('item_color')].",0) and nvl(b.color_number_id,0)=nvl(".$result_color[csf('color_number_id')].",0)");
						}
						foreach($nameArray_size_size_qnty as $result_size_size_qnty)
						{
							?>
							<td style="border:1px solid black; text-align:right">
							<? 
							if($result_size_size_qnty[csf('cons')]!= "")
							{
								echo number_format($result_size_size_qnty[csf('cons')],2);
								$item_desctiption_total += $result_size_size_qnty[csf('cons')] ;
								
								if (array_key_exists($result_size[csf('gmts_sizes')], $color_tatal))
								{
									$color_tatal[$result_size[csf('gmts_sizes')]]+=$result_size_size_qnty[csf('cons')];
								}
								else
								{
									$color_tatal[$result_size[csf('gmts_sizes')]]=$result_size_size_qnty[csf('cons')]; 
								}
							}
							else echo "";
							?>
							</td>
							<?   
						}
					}
					?>
					<td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,2); ?></td>
					<td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
					<td style="border:1px solid black; text-align:right">
					<? 
					$rate =$result_color[csf('amount')]/$item_desctiption_total;;
					echo number_format($rate,2); 
					?>
                     </td>
					<td style="border:1px solid black; text-align:right">
					<? 
					//$amount_as_per_gmts_color = $item_desctiption_total*  $result_color[csf('rate')];
					$amount_as_per_gmts_color =$result_color[csf('amount')];
					echo number_format($amount_as_per_gmts_color,2);
					$total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
					?>
					</td>
				</tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="6"><strong> Item Total</strong></td>
                <?
                foreach($nameArray_size  as $result_size)
                {
                
                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_size[csf('gmts_sizes')]] !='')
                {
                echo number_format($color_tatal[$result_size[csf('gmts_sizes')]],2);  
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
			
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+9; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER Color & SIZE  END=========================================  -->
        
        
         <!--==============================================NO NENSITIBITY START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=0 order by trim_group "); 
        //$nameArray_color=sql_select( "select distinct b.color_number_id from wo_trims_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=1"); 
		//$nameArray_color= array();
		if(count($nameArray_item)>0)
		{
        ?>
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="8" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]."&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO Qty:" .$nameArray_job_po_row[csf('po_quantity')];?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="8" align="">
                <strong>No Sensitivity</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand Supplier</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td align="center" style="border:1px solid black"><strong> Qnty</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
				
				$nameArray_item_description=sql_select( "select  b.description, b.brand_supplier,b.item_color,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.trim_group=".$result_item[csf('trim_group')]." and a.sensitivity=0 group by b.description, b.brand_supplier,b.item_color"); 
				
            //$nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and trim_group=".$result_item[csf('trim_group')]." order by trim_group "); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">

                <? echo $trim_group[$result_item[csf('trim_group')]]; ?>
                </td>
                <? 
                $color_tatal=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('description')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('brand_supplier')]; ?> </td>
                 <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('item_color')]]; ?> </td>
                <?
                //$nameArray_color_size_qnty=sql_select( "select sum(b.cons) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=0 and a.trim_group=". $result_item['trim_group']." and a.description='". $result_itemdescription['description']."'");
				/*if($db_type==0)
				{
				$nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls     where    booking_no=$txt_booking_no and sensitivity=0 and trim_group=". $result_item[csf('trim_group')]." and description='". $result_itemdescription[csf('description')]."'"); 
				}
                  
				if($db_type==2)
				{
					echo "select sum(wo_qnty) as cons from wo_booking_dtls     where    booking_no=$txt_booking_no and sensitivity=0 and trim_group=". $result_item[csf('trim_group')]." and nvl(description,0)=nvl('". $result_itemdescription[csf('description')]."',0)";
				$nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls     where    booking_no=$txt_booking_no and sensitivity=0 and trim_group=". $result_item[csf('trim_group')]." and nvl(description,0)=nvl('". $result_itemdescription[csf('description')]."',0)"); 
				}*/
				
				if($db_type==0)
				{
				$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=0 and a.trim_group=". $result_item[csf('trim_group')]." and  b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='".$result_itemdescription[csf('brand_supplier')]."' and b.item_color='".$result_itemdescription[csf('item_color')]."'");
				}
				if($db_type==2)
				{
				$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=0 and a.trim_group=". $result_item[csf('trim_group')]." and nvl( b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_color,0)=nvl('".$result_itemdescription[csf('item_color')]."',0)");
				}
                          
                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <? 
                if($result_color_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_color_size_qnty[csf('cons')],2);
                $item_desctiption_total += $result_color_size_qnty[csf('cons')] ;
                $color_tatal+=$result_color_size_qnty[csf('cons')];
                }
                else echo "";
                ?>
                </td>
                <?   
                }
                ?>
                
                <td style="border:1px solid black; text-align:center "><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],8); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,2);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="3"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal !='')
                {
                echo number_format($color_tatal,2);  
                }
                ?>
                </td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="8"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <?
		//print_r($color_tatal);
		}
		}

		?>
        <!--==============================================NO NENSITIBITY END=========================================  -->
       &nbsp;
       <?
       $mcurrency="";
	   $dcurrency="";
	   if($currency_id==1)
	   {
		$mcurrency='Taka';
		$dcurrency='Paisa'; 
	   }
	   if($currency_id==2)
	   {
		$mcurrency='USD';
		$dcurrency='CENTS'; 
	   }
	   if($currency_id==3)
	   {
		$mcurrency='EURO';
		$dcurrency='CENTS'; 
	   }
	   ?>
       <table  width="100%" class="rpt_table"  border="1" cellpadding="0" cellspacing="0" rules="all">
       <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount</th><td width="30%" style="border:1px solid black; text-align:right"><? echo number_format($booking_grand_total,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</th><td width="30%" style="border:1px solid black;"><? echo number_to_words(def_number_format($booking_grand_total,2,""),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
          &nbsp;
        <table width="100%">
        <tr>
        <td width="49%">
        
         <? echo get_spacial_instruction($txt_booking_no);?>
    </td>
    <td width="2%"></td>
    
    <td width="49%">
    <?
	//if($show_comment==1)
	if(str_replace("'","",$show_comment)==1)
	{
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" >
                <thead>
                    <th width="40">SL</th>
                    <th width="150">Item</th>
                    <th width="150">PO No</th>
                    <th width="150">Pre-Cost Value</th>
                    <th width="">WO Value </th>
                  
                </thead>
       <tbody>
       <?
					$sql_lib_item_group_array=array();
					$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
					foreach($sql_lib_item_group as $row_sql_lib_item_group)
					{
						$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
						$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
						$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
					}
					
					$sql="select job_no,po_break_down_id,pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls where booking_no=$txt_booking_no and status_active=1 and is_deleted=0 group by job_no,po_break_down_id,pre_cost_fabric_cost_dtls_id,trim_group";
					$exchange_rate=return_field_value("exchange_rate", " wo_booking_mst", "booking_no=".$txt_booking_no."");
					$i=1;
					$total_amount=0;
                    $nameArray=sql_select( $sql );
                    foreach ($nameArray as $selectResult)
                    {
						$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$selectResult[csf('job_no')]."'");
						
						if($costing_per==1)
						{
							$costing_per_qty=12;
						}
						else if($costing_per==2)
						{
							$costing_per_qty=1;
						}
						else if($costing_per==3)
						{
							$costing_per_qty=24;
						}
						else if($costing_per==4)
						{
							$costing_per_qty=36;
						}
						else if($costing_per==5)
						{
							$costing_per_qty=48;
						}
						
						$sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='".$selectResult[csf('po_break_down_id')]."'  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty");
						list($sql_po_qty_row)=$sql_po_qty;
						$po_qty=$sql_po_qty_row[csf('order_quantity_set')];
						
						$sql_cons_data=sql_select("select a.rate,b.cons from wo_pre_cost_trim_cost_dtls a , wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id and  a.id='".$selectResult[csf('pre_cost_fabric_cost_dtls_id')]."' and b.po_break_down_id='".$selectResult[csf('po_break_down_id')]."' and a.is_deleted=0  and a.status_active=1");
						list($sql_cons_data_row)=$sql_cons_data;
						$pre_cons=$sql_cons_data_row[csf('cons')];
						$pre_rate=$sql_cons_data_row[csf('rate')];
						$pre_req_qnty=def_number_format(($pre_cons*($po_qty/$costing_per_qty))/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],5,"");
						$pre_amount=$pre_req_qnty*$pre_rate;
						
						$sql_cu_woq=sql_select("select sum(amount) as amount  from wo_booking_dtls where po_break_down_id='".$selectResult[csf('po_break_down_id')]."' and pre_cost_fabric_cost_dtls_id='".$selectResult[csf('pre_cost_fabric_cost_dtls_id')]."'  and  booking_type=2 and status_active=1 and is_deleted=0");
						list($sql_cu_woq_row)=$sql_cu_woq;
						$cu_woq_amount=$sql_cu_woq_row[csf('amount')];
	   ?>
                    <tr>
                    <td width="40"><? echo $i;?></td>
                    <td width="150">
					<? echo $trim_group[$selectResult[csf('trim_group')]];?> 
                    </td>
                    <td width="150">
					<? echo $po_number[$selectResult[csf('po_break_down_id')]];?> 
                    </td>
                    <td width="150" align="right">
                     <? echo $pre_amount; ?>
                    </td>
                    <td width="" align="right">
                    <? echo number_format($cu_woq_amount/$exchange_rate,5);?>
                    </td>
                    
                    </tr>
	   <?
	   $i++;
					}
       ?>
	</tbody>
    </table>
    <?
	}
	?>
    </td>
    </tr>
    </table>
                
    </div>
    <div>
		<?
        	echo signature_table(2, $cbo_company_name, "1330px");
        ?>
    </div>
<?
	
	if($link==1)
	{?>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<? 
	}
	else
	{
	?>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<? }?>
	
	<script>
	fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
	</script>
	<?
	exit();
}

if($action=="show_trim_booking_report2")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$order_uom_arr=return_library_array("select id,order_uom  from lib_item_group","id","order_uom");
	$team_member_arr=return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");
	//print_r($team_member_arr);die;
	//$po_qnty_tot=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<div style="width:1333px" align="center">       
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100"> 
              <? if($link==1){?>
               <img  src='../../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               <? }else{?>
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               <? }?>
               </td>
               <td width="1000">                                     
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php      
                                    echo $company_library[$cbo_company_name];
                              ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
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
                               </td> 
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">  
                                <strong>
								<? 
								if(str_replace("'","",$cbo_isshort)==2)
								{
								$isshort="";	
								}
								if(str_replace("'","",$cbo_isshort)==1)
								{
								$isshort="[Short]";	
								}
								if ($report_title !="")
								{
									echo $report_title." ".$isshort;
								} 
								else 
								{
									echo "Main Trims Booking ".$isshort;
								}  
								?> 
                                &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <font style="color:#F00">
								<? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> 
                                </font>
                                </strong>
                             </td> 
                            </tr>
                      </table>
                </td> 
                  <td width="250" id="barcode_img_id"> 
                 
               </td>      
            </tr>
       </table>
		<?
		
		$booking_grand_total=0;
		$job_no="";
		$currency_id="";
		$nameArray_job=sql_select( "select distinct b.job_no  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no=$txt_booking_no"); 
        foreach ($nameArray_job as $result_job)
        {

			$job_no.=$result_job[csf('job_no')].", ";
		}
		$buyer_string="";
		
		$nameArray_buyer=sql_select( "select distinct a.buyer_name  from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no"); 
        foreach ($nameArray_buyer as $result_buy)
        {
			$buyer_string.=$buyer_name_arr[$result_buy[csf('buyer_name')]].",";
		}
		
		$po_no="";$file_no="";$ref_no="";
		$nameArray_job=sql_select( "select distinct b.po_number,b.file_no,b.grouping  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no"); 
        foreach ($nameArray_job as $result_job)
        {
			$po_no.=$result_job[csf('po_number')].", ";
			$file_no.=$result_job[csf('file_no')].", ";
			$ref_no.=$result_job[csf('grouping')].", ";
		}
		$style_ref="";
		$deling_marchent="";
		$nameArray_style=sql_select( "select distinct a.style_ref_no,a.dealing_marchant  from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no"); 
        foreach ($nameArray_style as $result_style)
        {
			$style_ref.=$result_style[csf('style_ref_no')].", ";
			$deling_marchent.=$team_member_arr[$result_style[csf('dealing_marchant')]].", ";
		}
        $nameArray=sql_select("select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source,a.remarks,a.pay_mode  from wo_booking_mst a where  a.booking_no=$txt_booking_no"); 
        foreach ($nameArray as $result)
        {
			$varcode_booking_no=$result[csf('booking_no')];
        ?>
       <table width="100%" style="border:1px solid black">                    	
            <tr>
                <td colspan="6" valign="top"></td>                             
            </tr> 
            
             <tr>
                <td width="100" style="font-size:12px"><b>Attention </b>   </td>
                <td width="110">:&nbsp;<?  echo $result[csf('attention')];?> </td>
                <td width="100" style="font-size:12px"><b>Buyer Name</b></td>
                <td width="110">:&nbsp;<? echo   rtrim($buyer_string,", "); ?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100"><span style="font-size:12px"><b>Booking No</b></span></td>
                <td width="110">:&nbsp;<? echo $result[csf('booking_no')];?></td>	
            </tr>
               <tr>
                <td width="100" style="font-size:12px"><b>Supplier Name </b>   </td>
                <td width="110">:&nbsp;<? 
				if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
					echo $company_library[$result[csf('supplier_id')]];
					}
					else{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
					}
				//echo $supplier_name_arr[$result[csf('supplier_id')]];?> </td>
                <td width="100" style="font-size:12px"><b>Order No</b></td>
                <td width="110">:&nbsp;<? echo rtrim($po_no,", "); ?> </td>
                <td width="100"><span style="font-size:12px"><b>Source</b></span></td>
                <td width="110">:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>	
            </tr>
            
               <tr>
                <td width="100" style="font-size:12px"><b>Supplier Address </b>   </td>
                <td width="110">:&nbsp;<? echo $supplier_address_arr[$result[csf('supplier_id')]];?> </td>
                <td width="100" style="font-size:12px"><b>Job No</b></td>
                <td width="110">:&nbsp;<? echo $job_no=rtrim($job_no,", ");?> </td>
                <td width="100"><span style="font-size:12px"><b>Currency</b></span></td>
                <td width="110">:&nbsp;<? $currency_id=$result[csf('currency_id')]; echo $currency[$result[csf('currency_id')]]; ?></td>	
            </tr>
            
             <tr>
                <td width="100" style="font-size:12px"><b>Booking Date </b>   </td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp; </td>
                <td width="100" style="font-size:12px"><b>Style Name</b></td>
                <td width="110">:&nbsp;<? echo $style_sting=rtrim($style_ref,", "); ?> </td>
                <td width="100"><span style="font-size:12px"><b>Exchange Rate</b></span></td>
                <td width="110">:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>	
            </tr>
            
              <tr>
                <td width="100" style="font-size:16px"><b>Delivery Date </b>   </td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?> </td>
                <td width="100" style="font-size:12px"><b>Merd Name</b></td>
                <td width="110">:&nbsp;<? echo rtrim($deling_marchent,", "); ?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100"><span style="font-size:12px"><b>Pay Mode</b></span></td>
                <td width="110">:&nbsp;<? echo $pay_mode[$result[csf('pay_mode')]];?></td>	
            </tr>
             <tr>
                <td width="100" style="font-size:16px"><b>File no </b>   </td>
                <td width="110">:&nbsp;<? echo rtrim($file_no,", ");?> </td>
                <td width="100" style="font-size:16px"><b>Ref. no</b></td>
                <td width="110">:&nbsp;<? echo rtrim($ref_no,", "); ?>&nbsp;&nbsp;&nbsp;</td>
               	
            </tr>
            
            
        </table>  
        <br/>
		<?
        }
        ?>
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?
		$nameArray_job_po=sql_select( "select a.job_no,a.po_break_down_id, b.po_quantity as po_quantity from wo_booking_dtls a,wo_po_break_down b  where a.booking_no=$txt_booking_no and a.po_break_down_id=b.id and b.status_active=1 and b.is_deleted=0  group by a.job_no,a.po_break_down_id order by a.job_no,a.po_break_down_id "); 
		foreach($nameArray_job_po as $nameArray_job_po_row)
		{
		
       // $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]."  and sensitivity=1 order by trim_group ");
		 $nameArray_item=sql_select( "select  id,trim_group,country_id_string from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]."  and sensitivity=1 order by trim_group ");
		
		
       // $nameArray_color=sql_select( "select distinct b.color_number_id from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=1");
	   if(count($nameArray_item)>0)
		{
        ?>
        &nbsp;
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
           <tr>
                <td colspan="9" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO Qty:" .$nameArray_job_po_row[csf('po_quantity')];?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="9" align="">
                <strong>As Per Garments Color</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
                <td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            $nameArray_item_description=sql_select( "select b.description, b.brand_supplier,b.item_color,min(b.color_size_table_id) as color_size_table_id,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and  a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=1 and   a.id= ".$result_item[csf('id')]." and a.trim_group=".$result_item[csf('trim_group')]." and b.requirment !=0 group by b.description, b.brand_supplier,b.item_color order by color_size_table_id "); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center"  style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? 
				echo $trim_group[$result_item[csf('trim_group')]]."<br/>";
				$country_arr=explode(",",$result_item[csf('country_id_string')]);
				$country_name_string="";
				for($co=0; $co <count($country_arr);$co++)
				{
				 $country_name_string.=$country_library[$country_arr[$co]].",";
				}
				echo rtrim($country_name_string,',');
				?>
                </td>
                <? 
				$item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                
                ?>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('description')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('brand_supplier')]; ?> </td>
                <td style="border:1px solid black; text-align:right">
               <? echo $color_library[$result_itemdescription[csf('item_color')]]; ?> 
                </td>
                <td style="border:1px solid black; text-align:right">
				<?
				echo number_format($result_itemdescription[csf('cons')],4);
				$item_desctiption_total += $result_itemdescription[csf('cons')] ;
				?>
                </td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],8); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="3"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right"><? echo number_format($item_desctiption_total ,4);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="8"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER GMTS COLOR END=========================================  -->
        
        
        
        
        
        
        
        
        
        
        <!--==============================================Size Sensitive START=========================================  -->
		<?
        //$nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=2 order by trim_group "); 
		
		$nameArray_item=sql_select( "select  id,trim_group,country_id_string from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=2 order by trim_group "); 
        //$nameArray_size=sql_select( "select distinct b.item_size  as gmts_sizes from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=2");
		if(count($nameArray_item)>0)
		{
        ?>
        
        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="10" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO Qty:" .$nameArray_job_po_row[csf('po_quantity')];?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="10" align="">
                <strong>Size Sensitive </strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
                <td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
                <td align="center" style="border:1px solid black"><strong>Gmts Size</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty.</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
            //$nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and trim_group=".$result_item[csf('trim_group')]." order by trim_group "); 
			$nameArray_item_description=sql_select( "select b.description, b.brand_supplier,b.item_size,b.gmts_sizes,min(b.color_size_table_id) as color_size_table_id, sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id=b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=2 and  a.id= ".$result_item[csf('id')]." and a.trim_group=".$result_item[csf('trim_group')]." and b.requirment !=0 group by b.description, b.brand_supplier,b.item_size, b.gmts_sizes order by color_size_table_id"); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? 
				echo $trim_group[$result_item[csf('trim_group')]]."<br/>"; 
				$country_arr=explode(",",$result_item[csf('country_id_string')]);
				//print_r($country_arr);
				$country_name_string="";
				for($co=0; $co <count($country_arr);$co++)
				{
				 $country_name_string.=$country_library[$country_arr[$co]].",";
				}
				echo rtrim($country_name_string,',');
				?>
                </td>
                <? 
                $item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                
                ?>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('description')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('brand_supplier')]; ?> </td>
                <td style="border:1px solid black; text-align:right">
              <? echo $result_itemdescription[csf('item_size')];?>
                </td>
                <td style="border:1px solid black; text-align:right">
              <? echo $size_library[$result_itemdescription[csf('gmts_sizes')]];?>
                </td>
                
                
                <td style="border:1px solid black; text-align:right">
				<? 
				 echo number_format($result_itemdescription[csf('cons')],4);
                $item_desctiption_total += $result_itemdescription[csf('cons')] ;
				?>
                </td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],8); ?> </td>
                <td style="border:1px solid black; text-align:right">

                <? 
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="4"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right"><? echo number_format($item_desctiption_total,4);  ?></td>
                 <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="9"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        
        <!--==============================================Size Sensitive END=========================================  -->
        
         <!--==============================================AS PER CONTRAST COLOR START=========================================  -->
		<?
       // $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=3 order by trim_group "); 
		

		 $nameArray_item=sql_select( "select  id,trim_group,country_id_string from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=3 order by trim_group "); 
        //$nameArray_color=sql_select( "select distinct b.item_color as color_number_id from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=3"); 
		if(count($nameArray_item)>0)
		{
        ?>
        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
           <tr>
                <td colspan="10" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO Qty:" .$nameArray_job_po_row[csf('po_quantity')];?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="10" align="">
                <strong>Contrast Color</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
                <td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
                  <td align="center" style="border:1px solid black"><strong>Gmts Color</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty.</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=3 and  a.id= ".$result_item[csf('id')]." and a.trim_group=".$result_item[csf('trim_group')]." and b.requirment !=0  order by trim_group ", "item_color", "color_number_id"  );
			$nameArray_item_description=sql_select( "select b.description, b.brand_supplier,b.item_color,b.color_number_id,sum(b.requirment) as cons,min(b.color_size_table_id) as color_size_table_id,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=3 and  a.id= ".$result_item[csf('id')]." and a.trim_group=".$result_item[csf('trim_group')]." and b.requirment !=0 group by b.description, b.brand_supplier,b.item_color,b.color_number_id order by color_size_table_id "); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? 
				echo $trim_group[$result_item[csf('trim_group')]]."<br/>"; 
				$country_arr=explode(",",$result_item[csf('country_id_string')]);
				//print_r($country_arr);
				$country_name_string="";
				for($co=0; $co <count($country_arr);$co++)
				{
				 $country_name_string.=$country_library[$country_arr[$co]].",";
				}
				echo rtrim($country_name_string,',');
				?>
                </td>
                <? 
                $item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                
                ?>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('description')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('brand_supplier')]; ?> </td>
                <td style="border:1px solid black; text-align:right">
               <? echo $color_library[$result_itemdescription[csf('item_color')]]; ?>
                </td>
                <td style="border:1px solid black; text-align:right">
               <? echo $color_library[$result_itemdescription[csf('color_number_id')]]; ?>
                </td>
               
                
                <td style="border:1px solid black; text-align:right">
				<? 
				echo number_format($result_itemdescription[csf('cons')],4);
                $item_desctiption_total += $result_itemdescription[csf('cons')] ;
				?>
                </td>
                 <td style="border:1px solid black; text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],8); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="4"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right"><? echo number_format($item_desctiption_total,4);  ?></td>
                <td style="border:1px solid black;text-align:center"></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="9"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        
        <!--==============================================AS PER CONTRAST COLOR END=========================================  -->
        
        <!--==============================================AS PER GMTS Color & SIZE START=========================================  -->
		<?
       // $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=4 order by trim_group ");
		
		$nameArray_item=sql_select( "select  id,trim_group,country_id_string from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=4 order by trim_group ");
       // $nameArray_size=sql_select( "select distinct b.item_size  as gmts_sizes from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=4");
	   if(count($nameArray_item)>0)
		{
        ?>
        
        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
             <tr>
                <td colspan="12" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO Qty:" .$nameArray_job_po_row[csf('po_quantity')];?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="12" align="">
                <strong>Color & size sensitive </strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td style="border:1px solid black"><strong>Gmts Color</strong> </td>
                
                <td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
                <td align="center" style="border:1px solid black"><strong>Gmts Size</strong></td>
                			
                <td style="border:1px solid black" align="center"><strong>WO Qty.</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
			//$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no  and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=4 and  a.id= ".$result_item[csf('id')]." and a.trim_group=".$result_item[csf('trim_group')]." and b.requirment !=0  order by trim_group ", "item_color", "color_number_id"  );
			
			
			 $nameArray_color=sql_select( "select  b.item_color,b.color_number_id,b.item_size,b.gmts_sizes,min(b.color_size_table_id) as color_size_table_id,b.description, b.brand_supplier,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id   and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and  a.id= ".$result_item[csf('id')]." and a.trim_group=".$result_item[csf('trim_group')]." and a.sensitivity=4 and b.requirment !=0 group by b.item_color,b.color_number_id,b.item_size,b.gmts_sizes,b.description, b.brand_supplier order by color_size_table_id "); 
			 
            $nameArray_item_description=sql_select( "select distinct uom from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=4   and trim_group=".$result_item[csf('trim_group')]." order by trim_group "); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo   (count($nameArray_color)); ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo (count($nameArray_color)); ?>">
                <? 
				echo $trim_group[$result_item[csf('trim_group')]]."<br/>"; 
				$country_arr=explode(",",$result_item[csf('country_id_string')]);
				//print_r($country_arr);
				$country_name_string="";
				for($co=0; $co <count($country_arr);$co++)
				{
				 $country_name_string.=$country_library[$country_arr[$co]].",";
				}
				echo rtrim($country_name_string,','); 
				?>
                </td>
                <? 
				

                
				$item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
				foreach($nameArray_color as $result_color)
                {
					
					?>
					
					<td style="border:1px solid black" ><? echo $result_color[csf('description')]; ?> </td>
					<td style="border:1px solid black" ><? echo $result_color[csf('brand_supplier')]; ?> </td>
                    <td style="border:1px solid black"><? echo $color_library[$result_color[csf('item_color')]]; ?> </td>
                    <td style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; ?> </td>
					<td style="border:1px solid black; text-align:right">
					<? echo $result_color[csf('item_size')]; ?> 
					</td>
                    <td style="border:1px solid black; text-align:right">
					<? echo $size_library[$result_color[csf('gmts_sizes')]]; ?> 
					</td>
					<td style="border:1px solid black; text-align:right">
					<?
					echo number_format($result_color[csf('cons')],4);
					$item_desctiption_total += $result_color[csf('cons')] ;
					//echo number_format($item_desctiption_total,2); 
					?>
                    </td>
					<td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
					<td style="border:1px solid black; text-align:right">
					<?
					echo number_format($result_color[csf('rate')],4);
					/*$rate =$result_color[csf('amount')]/$result_color[csf('cons')];
					echo number_format($rate,4); */
					?>
                     </td>
					<td style="border:1px solid black; text-align:right">
					<? 
					//$amount_as_per_gmts_color = $item_desctiption_total*  $result_color[csf('rate')];
					$amount_as_per_gmts_color =$result_color[csf('amount')];
					echo number_format($amount_as_per_gmts_color,4);
					$total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
					?>
					</td>
				</tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="8"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right"><? echo number_format($item_desctiption_total,4);  ?></td>
                <td style="border:1px solid black;  text-align:right"><? //echo number_format(array_sum($color_tatal),2);  ?></td>
                <td style="border:1px solid black; text-align:right"><? //echo number_format(round($color_total),0); ?></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
			
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="11"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
       <?
		}
	   ?>
        <!--==============================================AS PER Color & SIZE  END=========================================  -->
        
        
         <!--==============================================NO NENSITIBITY START=========================================  -->
		<?
       //$nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=0 order by trim_group ");
		$nameArray_item=sql_select( "select  id,trim_group,country_id_string from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=0 order by trim_group ");
        //$nameArray_color=sql_select( "select distinct b.color_number_id from wo_trims_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=1"); 
		//$nameArray_color= array();
		if(count($nameArray_item)>0)
		{
        ?>
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="8" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO Qty:" .$nameArray_job_po_row[csf('po_quantity')];?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="8" align="">
                <strong>No Sensitivity</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td align="center" style="border:1px solid black"><strong> Qnty</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
				
				$nameArray_item_description=sql_select( "select  b.description, b.brand_supplier,b.item_color,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and  a.id= ".$result_item[csf('id')]." and a.trim_group=".$result_item[csf('trim_group')]." and a.sensitivity=0 group by b.description, b.brand_supplier,b.item_color"); 
				
            //$nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and trim_group=".$result_item[csf('trim_group')]." order by trim_group "); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">

                <?
				echo $trim_group[$result_item[csf('trim_group')]]."<br/>"; 
				$country_arr=explode(",",$result_item[csf('country_id_string')]);
				//print_r($country_arr);
				$country_name_string="";
				for($co=0; $co <count($country_arr);$co++)
				{
				 $country_name_string.=$country_library[$country_arr[$co]].",";
				}
				echo rtrim($country_name_string,',');  
				?>
                </td>
                <? 
                $color_tatal=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
                ?>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('description')]; ?> </td>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('brand_supplier')]; ?> </td>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('item_color')]]; ?> </td>
                <?
                //$nameArray_color_size_qnty=sql_select( "select sum(b.cons) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=0 and a.trim_group=". $result_item['trim_group']." and a.description='". $result_itemdescription['description']."'");
				/*if($db_type==0)
				{
				$nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls     where    booking_no=$txt_booking_no and sensitivity=0 and trim_group=". $result_item[csf('trim_group')]." and description='". $result_itemdescription[csf('description')]."'"); 
				}
                  
				if($db_type==2)
				{
					echo "select sum(wo_qnty) as cons from wo_booking_dtls     where    booking_no=$txt_booking_no and sensitivity=0 and trim_group=". $result_item[csf('trim_group')]." and nvl(description,0)=nvl('". $result_itemdescription[csf('description')]."',0)";
				$nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls     where    booking_no=$txt_booking_no and sensitivity=0 and trim_group=". $result_item[csf('trim_group')]." and nvl(description,0)=nvl('". $result_itemdescription[csf('description')]."',0)"); 
				}*/
				
				if($db_type==0)
				        {
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=0 and  a.id= ".$result_item[csf('id')]." and a.trim_group=". $result_item[csf('trim_group')]." and  b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='".$result_itemdescription[csf('brand_supplier')]."' and b.item_color='".$result_itemdescription[csf('item_color')]."'");
						}
						if($db_type==2)
				        {
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=0 and  a.id= ".$result_item[csf('id')]." and a.trim_group=". $result_item[csf('trim_group')]." and nvl( b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_color,0)=nvl('".$result_itemdescription[csf('item_color')]."',0)");
						}
                          
                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <? 
                if($result_color_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_color_size_qnty[csf('cons')],4);
                $item_desctiption_total += $result_color_size_qnty[csf('cons')] ;
                $color_tatal+=$result_color_size_qnty[csf('cons')];
                }
                else echo "";
                ?>
                </td>
                <?   
                }
                ?>
                
                <td style="border:1px solid black; text-align:center "><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],8); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="3"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal !='')
                {
                echo number_format($color_tatal,4);  
                }
                ?>
                </td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right">
                <? 
                echo number_format($total_amount_as_per_gmts_color,4);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td align="right" style="border:1px solid black"  colspan="8"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <?
		//print_r($color_tatal);
		}
		}
		?>
        <!--==============================================NO NENSITIBITY END=========================================  -->
       &nbsp;
       <?
       $mcurrency="";
	   $dcurrency="";
	   if($currency_id==1)
	   {
		$mcurrency='Taka';
		$dcurrency='Paisa'; 
	   }
	   if($currency_id==2)
	   {
		$mcurrency='USD';
		$dcurrency='CENTS'; 
	   }
	   if($currency_id==3)
	   {
		$mcurrency='EURO';
		$dcurrency='CENTS'; 
	   }
	   ?>
       <table  width="100%" class="rpt_table"  border="1" cellpadding="0" cellspacing="0" rules="all">
       <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount</th><td width="30%" style="border:1px solid black; text-align:right"><? echo number_format($booking_grand_total,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <th width="70%" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</th><td width="30%" style="border:1px solid black;"><? echo number_to_words(def_number_format($booking_grand_total,2,""),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
          &nbsp;
        <table width="100%">
        <tr>
        <td width="49%">
         <? echo get_spacial_instruction($txt_booking_no);?>
    </td>
    <td width="2%"></td>
    
    <td width="49%">
    <?
	if(str_replace("'","",$show_comment)==1)
	{
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" >
                <thead>
                    <th width="40">SL</th>
                    <th width="150">Item</th>
                    <th width="150">PO No</th>
                    <th width="100">Pre-Cost Value</th>
                    <th width="100">WO Value </th>
                    <th width="">Comments </th>
                  
                </thead>
       <tbody>
       <?
					$sql_lib_item_group_array=array();
					$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
					foreach($sql_lib_item_group as $row_sql_lib_item_group)
					{
						$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
						$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
						$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
					}
					
					$sql="select job_no,po_break_down_id,pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls where booking_no=$txt_booking_no and status_active=1 and is_deleted=0 group by job_no,po_break_down_id,pre_cost_fabric_cost_dtls_id,trim_group";
					$exchange_rate=return_field_value("exchange_rate", " wo_booking_mst", "booking_no=".$txt_booking_no."");

					$i=1;
					$total_amount=0;
                    $nameArray=sql_select( $sql );
                    foreach ($nameArray as $selectResult)
                    {
						$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$selectResult[csf('job_no')]."'");
						if($costing_per==1)
						{
							$costing_per_qty=12;
						}
						else if($costing_per==2)
						{
							$costing_per_qty=1;
						}
						else if($costing_per==3)
						{
							$costing_per_qty=24;
						}
						else if($costing_per==4)
						{
							$costing_per_qty=36;
						}
						else if($costing_per==5)
						{
							$costing_per_qty=48;
						}
						
						$sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='".$selectResult[csf('po_break_down_id')]."'  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty");
						list($sql_po_qty_row)=$sql_po_qty;
						$po_qty=$sql_po_qty_row[csf('order_quantity_set')];
						
						$sql_cons_data=sql_select("select a.rate,b.cons from wo_pre_cost_trim_cost_dtls a , wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id and  a.id='".$selectResult[csf('pre_cost_fabric_cost_dtls_id')]."' and b.po_break_down_id='".$selectResult[csf('po_break_down_id')]."' and a.is_deleted=0  and a.status_active=1");
						list($sql_cons_data_row)=$sql_cons_data;
						$pre_cons=$sql_cons_data_row[csf('cons')];
						$pre_rate=$sql_cons_data_row[csf('rate')];
						
						$pre_req_qnty=def_number_format(($pre_cons*($po_qty/$costing_per_qty))/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],5,"");
						$pre_amount=$pre_req_qnty*$pre_rate;
						
						
						$sql_cu_woq=sql_select("select sum(amount) as amount  from wo_booking_dtls where po_break_down_id='".$selectResult[csf('po_break_down_id')]."' and pre_cost_fabric_cost_dtls_id='".$selectResult[csf('pre_cost_fabric_cost_dtls_id')]."'  and  booking_type=2 and status_active=1 and is_deleted=0");
						list($sql_cu_woq_row)=$sql_cu_woq;
						$cu_woq_amount=$sql_cu_woq_row[csf('amount')];
	   ?>
                    <tr>
                    <td width="40"><? echo $i;?></td>
                    <td width="150">
					<? echo $trim_group[$selectResult[csf('trim_group')]];?> 
                    </td>
                    <td width="150">
					<? echo $po_number[$selectResult[csf('po_break_down_id')]];?> 
                    </td>
                    <td width="100" align="right">
                     <? echo $pre_amount; ?>
                    </td>
                    <td width="100" align="right">
                    <? echo number_format($cu_woq_amount/$exchange_rate,5);?>
                    </td>
                    <td width="" align="right">
                    <?
					if($pre_amount==$cu_woq_amount)
					{
					echo "At Per";
					}
					if($pre_amount>$cu_woq_amount)
					{
					echo "Less Booking";
					}
					if($pre_amount<$cu_woq_amount)
					{
					echo "Over Booking";
					}
					?>
                    </td>
                    
                    </tr>
	   <?
	   $i++;
					}
       ?>
	</tbody>
    </table>
    <?
	}
	?>
    </td>
    </tr>
    </table>
                
    </div>
    <div>
		<?
        	echo signature_table(2, $cbo_company_name, "1330px");
			echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
        ?>
    </div>
<?
	if($link==1)
	{
		?>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
		<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
		<? 
	}
	else
	{
		?>
		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		<? 
	}
	?>
	
	<script>
	fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
	</script>
	<?
	exit();
}

if($action=="show_trim_booking_report3"){
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$order_uom_arr=return_library_array("select id,order_uom  from lib_item_group","id","order_uom");
	$team_member_arr=return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$location_name_arr=return_library_array( "select company_id,location_name from lib_location where status_active=1 and is_deleted=0",'company_id','location_name');
	?>
	<div style="width:1033px" align="center">       
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black; margin-bottom:20px;" align="left">
            <tr>
                <td width="100"> 
					<? if($link==1){?>
                    <img  src='../../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
                    <? }else{?>
                    <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
                    <? }?>
                </td>
                <td width="1000">                                     
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                            <?php      
                            echo $company_library[$cbo_company_name];
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
                            <?
                            $nameArray=sql_select( "select id, plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
                            foreach ($nameArray as $result){ 
								if($result[csf('plot_no')])echo $result[csf('plot_no')].', ';  
								if($result[csf('level_no')])echo $result[csf('level_no')].', ';
								if($result[csf('road_no')])echo $result[csf('road_no')].', ';  
								if($result[csf('block_no')])echo $result[csf('block_no')].', '; 
								if($result[csf('city')])echo $result[csf('city')].', ';
								if($result[csf('zip_code')])echo $result[csf('zip_code')].', '; 
								if($result[csf('province')])echo $result[csf('province')].', ';
								echo $country_arr[$result[csf('country_id')]];
                            }
                            ?>   
                            </td> 
                        </tr>
                        <tr>
                            <td align="center" style="font-size:20px">  
                            <strong>
                            <? 
                            if(str_replace("'","",$cbo_isshort)==2){
								$isshort="";	
                            }
                            else if(str_replace("'","",$cbo_isshort)==1){
								$isshort="[Short]";	
                            }
                            
                            if ($report_title !=""){
								echo $report_title." ".$isshort;
                            } 
                            else {
								echo "Main Trims Booking ".$isshort;
                            }  
                            ?> 
                            &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <font style="color:#F00">
                            <? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> 
                            </font>
                            </strong>
                            </td> 
                        </tr>
                    </table>
                </td> 
                <td width="250" id="barcode_img_id"> 
                </td>        
            </tr>
        </table>
		<?
		$booking_grand_total=0;
		$grand_total_as_per_gmts_color=0;
		$grand_item_desctiption_total=0;
		$uom_arr_check=array();
		$group_arr_check=array();
		$currency_id="";
		
		/*$job_no="";
		$nameArray_job=sql_select( "select distinct b.job_no  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.booking_type=2"); 
        foreach ($nameArray_job as $result_job)
        {
			if($job_no!="") { $job_no.=",".$result_job[csf('job_no')]; }
			else $job_no=$result_job[csf('job_no')];
		}*/
		
		/*$style_ref="";
		$deling_marchent="";
		$nameArray_style=sql_select( "select distinct a.style_ref_no,a.dealing_marchant  from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no"); 
        foreach ($nameArray_style as $result_style)
        {
			$style_ref.=$result_style[csf('style_ref_no')].", ";
			$deling_marchent.=$team_member_arr[$result_style[csf('dealing_marchant')]].", ";
		}*/
		
		
		/*$po_id="";
		$nameArray_job=sql_select( "select  b.id  as po_id,b.shiping_status from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.booking_type=2"); 
        foreach ($nameArray_job as $result_job){
			if($po_id!=""){
				$po_id.=",".$result_job[csf('po_id')]; 
			}
			else{
				$po_id=$result_job[csf('po_id')];
			}
			$po_status_arr[]=$result_job[csf('shiping_status')];
		}*/
		
		/*$nameArray_job=sql_select( "select  min(b.shipment_date) as ship_date  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.booking_type=2"); 
        foreach ($nameArray_job as $result_job){
			$min_shipdate=$result_job[csf('ship_date')];
		}*/
		//echo "select  b.job_no,a.buyer_name,a.style_ref_no,a.dealing_marchant,c.id  as po_id,c.po_number,c.shiping_status,c.shipment_date,d.costing_per  from wo_po_details_master a, wo_booking_dtls b, wo_po_break_down c,wo_pre_cost_mst d where a.job_no=b.job_no and a.job_no=c.job_no_mst and a.job_no=d.job_no and b.po_break_down_id=c.id and b.booking_no=$txt_booking_no and b.booking_type=2 order by c.shipment_date desc";
				//$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$nameArray_job_po_row[csf('job_no')]."'");

		$po_num_arr=array();
		$po_id_arr=array();
		$po_status_arr=array();
		$job_no_string_arr=array();
		$buyer_string_arr=array();
		$style_ref_no_string_arr=array();
		$dealing_marchant_string_arr=array();
		$job_style_arr=array();
		$costing_per_arr=array();
		$nameArray_buyer=sql_select( "select  b.job_no,a.buyer_name,a.style_ref_no,a.dealing_marchant,c.id  as po_id,c.po_number,c.shiping_status,c.shipment_date,c.pub_shipment_date,d.costing_per   from wo_po_details_master a, wo_booking_dtls b, wo_po_break_down c ,wo_pre_cost_mst d where a.job_no=b.job_no and a.job_no=c.job_no_mst and a.job_no=d.job_no and b.po_break_down_id=c.id and b.booking_no=$txt_booking_no and b.booking_type=2 order by c.shipment_date desc"); 
        foreach ($nameArray_buyer as $result_buy)
		{
			$job_no_string_arr[$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
			$buyer_string_arr[$result_buy[csf('buyer_name')]]=$buyer_name_arr[$result_buy[csf('buyer_name')]];
			$style_ref_no_string_arr[$result_buy[csf('style_ref_no')]]=$result_buy[csf('style_ref_no')];
			$job_style_arr[$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
			$dealing_marchant_string_arr[$result_buy[csf('dealing_marchant')]]=$team_member_arr[$result_buy[csf('dealing_marchant')]];
			$po_num_arr[$result_buy[csf('po_id')]]=$result_buy[csf('po_number')];
			$po_id_arr[$result_buy[csf('po_id')]]=$result_buy[csf('po_id')];
			$po_status_arr[]=$result_buy[csf('shiping_status')];
			$min_shipdate=$result_buy[csf('shipment_date')];
			$costing_per_arr[$result_buy[csf('job_no')]]=$result_buy[csf('costing_per')];
		}
		$job_no=implode(",",$job_no_string_arr);
		$buyer_string=implode(",",$buyer_string_arr);
		$style_ref=implode(",",$style_ref_no_string_arr);
		$deling_marchent=implode(",",$dealing_marchant_string_arr);
		$po_id=implode(",",$po_id_arr);
		
		$po_status_arr=array_unique($po_status_arr);
		$shipping_status=0;
		if(count($po_status_arr)==1){
			$shipping_status=$po_status_arr[0];
		}
		else if(count($po_status_arr)>1){
			$shipping_status=2;
		}
		
		//TNA Information
		$tna_start_sql=sql_select( "select id,po_number_id, 
								(case when task_number=31 then task_start_date else null end) as fab_booking_start_date,
								(case when task_number=31 then task_finish_date else null end) as fab_booking_end_date,
								(case when task_number=32 then task_start_date else null end) as trims_booking_start_date,
								(case when task_number=32 then task_finish_date else null end) as trims_booking_end_date,
								(case when task_number=70 then task_start_date else null end) as Sew_Trims_rcv_start_date,
								(case when task_number=70 then task_finish_date else null end) as Sew_Trims_rcv_end_date,								
								(case when task_number=84 then task_start_date else null end) as cutting_start_date,
								(case when task_number=84 then task_finish_date else null end) as cutting_end_date,
								(case when task_number=71 then task_start_date else null end) as fin_Trims_rcv_start_date,
								(case when task_number=71 then task_finish_date else null end) as fin_Trims_rcv_end_date,
								(case when task_number=86 then task_start_date else null end) as sewing_start_date,
								(case when task_number=86 then task_finish_date else null end) as sewing_end_date,
								(case when task_number=110 then task_start_date else null end) as exfact_start_date,
								(case when task_number=110 then task_finish_date else null end) as exfact_end_date
		from tna_process_mst
		where status_active=1 and po_number_id in($po_id)");
		$tna_fab_start=$tna_knit_start=$tna_dyeing_start=$tna_fin_start=$tna_cut_start=$tna_sewin_start=$tna_exfact_start="";
		$tna_date_task_arr=array();
		foreach($tna_start_sql as $row){
			if($row[csf("fab_booking_start_date")]!="" && $row[csf("fab_booking_start_date")]!="0000-00-00"){
				if($tna_fab_start==""){
					$tna_fab_start=$row[csf("fab_booking_start_date")];
				}
			}
			if($row[csf("trims_booking_start_date")]!="" && $row[csf("trims_booking_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['trims_booking_start_date']=$row[csf("trims_booking_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['trims_booking_end_date']=$row[csf("trims_booking_end_date")];
					}
					if($row[csf("Sew_Trims_rcv_start_date")]!="" && $row[csf("Sew_Trims_rcv_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['Sew_Trims_rcv_start_date']=$row[csf("Sew_Trims_rcv_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['Sew_Trims_rcv_end_date']=$row[csf("Sew_Trims_rcv_end_date")];
					}					
					if($row[csf("cutting_start_date")]!="" && $row[csf("cutting_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_start_date']=$row[csf("cutting_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_end_date']=$row[csf("cutting_end_date")];
					}
					if($row[csf("fin_Trims_rcv_start_date")]!="" && $row[csf("fin_Trims_rcv_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['fin_Trims_rcv_start_date']=$row[csf("fin_Trims_rcv_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['fin_Trims_rcv_end_date']=$row[csf("fin_Trims_rcv_end_date")];
					}
					if($row[csf("sewing_start_date")]!="" && $row[csf("sewing_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_start_date']=$row[csf("sewing_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_end_date']=$row[csf("sewing_end_date")];
					}
					if($row[csf("exfact_start_date")]!="" && $row[csf("exfact_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_start_date']=$row[csf("exfact_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_end_date']=$row[csf("exfact_end_date")];
					}
		}
				
		$tna_start_date="";
		$min_tna_date=return_field_value(" min(a.task_start_date) as min_start_date","tna_process_mst a, lib_tna_task b"," b.task_name=a.task_number and po_number_id in ($po_id) and task_name=32","min_start_date");
		$sql_order=sql_select("select order_quantity,color_number_id,size_number_id,po_break_down_id,country_id from wo_po_color_size_breakdown  where po_break_down_id in($po_id ) and status_active=1");
		$po_qty_po_level=array();
		$order_size_qty=array();
		$order_color_size_qty=array();
		$order_color_qty=array();
		$order_qty_arr=array();
		foreach($sql_order as $val){
			$order_color_qty[$val[csf('po_break_down_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]+=$val[csf('order_quantity')];
			$order_size_qty[$val[csf('po_break_down_id')]][$val[csf('country_id')]][$val[csf('size_number_id')]]+=$val[csf('order_quantity')];	
			$order_color_size_qty[$val[csf('po_break_down_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('order_quantity')];
			$order_qty_arr[$val[csf('po_break_down_id')]][$val[csf('country_id')]]+=$val[csf('order_quantity')];
			$po_qty_po_level[$val[csf('po_break_down_id')]]+=$val[csf('order_quantity')];
		}
		
		//'MM-DD-YYYY'
		if($db_type==0) $select_update_date="max(date( b.update_date)) as update_date"; else $select_update_date="max(to_char( b.update_date,'DD-MM-YYYY')) as update_date";
		$nameArray=sql_select("select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.pay_mode,a.source,a.remarks, $select_update_date  
		from wo_booking_mst a , wo_booking_dtls b  
		where a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.booking_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.pay_mode,a.source,a.remarks");
        foreach ($nameArray as $result){
			$varcode_booking_no=$result[csf('booking_no')];
        ?>
       <table width="100%" style="border:1px solid black" align="left">                    	
            <tr>
                <td colspan="6" valign="top"></td>                             
            </tr> 
            <tr>
                <td width="100" style="font-size:16px"><b>Buyer Name</b></td>
                <td width="200" >:&nbsp;<? echo   rtrim($buyer_string,", "); ?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100" style="font-size:16px"><b>Attention :</b></td>
                <td width="200"><?  echo $result[csf('attention')];?></td>
                <td width="100"><span style="font-size:16px"><b>Booking No</b></span></td>
                <td>:&nbsp;<? echo $result[csf('booking_no')];?></td>	
            </tr>
            <tr>
                <td style="font-size:16px"><b>Supplier Name </b>   </td>
                <td>:&nbsp;<? 
				if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
					echo $company_library[$result[csf('supplier_id')]];
					}
					else{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
					}
				//echo $supplier_name_arr[$result[csf('supplier_id')]];?> </td>
                <td style="font-size:16px"><b>Supplier Add. </b>   </td>
                <td>:&nbsp; <? echo $supplier_address_arr[$result[csf('supplier_id')]];?></td>
                <td><span style="font-size:16px"><b>Source</b></span></td>
                <td>:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>		
            </tr>
            <tr>
                <td style="font-size:16px"><b>Pay Mode</b>   </td>
                <td>:&nbsp; <? echo $pay_mode[$result[csf('pay_mode')]]; ?></td>
                <td style="font-size:16px"><b>Currency</b></td>
                <td>:&nbsp; <? $currency_id=$result[csf('currency_id')]; echo $currency[$result[csf('currency_id')]]; ?></td>
               	<td><span style="font-size:16px"><b>Exch. Rate</b></span></td>
                <td>:&nbsp;<? echo $exchange_rate=$result[csf('exchange_rate')]; ?></td>	
            </tr>
           </table>
           
           <table width="100%" style="border:1px solid black; margin:20px 0px 20px 0px" align="left">                    	
            <tr>
                <td colspan="6" valign="top"></td>                             
            </tr> 
             <tr>
                <td width="100" style="font-size:16px"><b>Booking Date </b>   </td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp; </td>
                <td width="100" style="font-size:16px"><b>TNA Start Date</b></td>
                <td width="110">:&nbsp;<? if($min_tna_date!="") echo change_date_format($min_tna_date,'dd-mm-yyyy','-'); ?> </td>
                <?
				$wo_tna_datediff=datediff( "d", $result[csf('booking_date')], $min_tna_date);
				if($wo_tna_datediff<1) $wo_caption="After";  else $wo_caption="Before";
				?>
                <td width="100"><span style="font-size:16px"><b>WO Prepared  <? echo $wo_caption; ?></b></span></td>
                <td width="110">:&nbsp;<? echo abs($wo_tna_datediff); ?></td>	
            </tr>
            
              <tr>
                <td style="font-size:16px"><b>Delivery Date </b>   </td>
                <td>:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?> </td>
                <td style="font-size:16px"><b>Shipment Date</b></td>
                <td>:&nbsp;<? if($min_shipdate!="") echo change_date_format($min_shipdate,'dd-mm-yyyy','-'); ?>&nbsp;&nbsp;&nbsp;</td>
                <td><span style="font-size:16px"><b>Ship.days in Hand</b></span></td>
                <td>:&nbsp;<? echo datediff( "d",date("d-m-Y"),$min_shipdate);?></td>	
            </tr>
            <tr>
                <td style="font-size:16px"><b>Last Update</b> </td>
                <td style="font-size:12px">:&nbsp;<? if($result[csf('update_date')]!="" && $result[csf('update_date')]!="0000-00-00") echo change_date_format($result[csf('update_date')]);?></td>
                <td style="font-size:16px"></td>
                <td ></td>
                <td ><span style="font-size:16px"><b>Ex-factory Status</b></span></td>
                <td style="font-size:16px">:&nbsp;<? echo $shipment_status[$shipping_status];?></td>	
            </tr>
            <tr>
                <td style="font-size:16px"><b>Remarks</b> </td>
                <td style="font-size:16px">:&nbsp;<? echo $result[csf('remarks')];?></td>
                <td style="font-size:12px"></td>
                <td ></td>
                
            </tr>
        </table>  
        <br/>
		<?
        }
        ?>
        
        
        
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?
		$total_order_qty=0;
		$nameArray_job_po=sql_select( "select a.job_no,a.po_break_down_id, max(b.po_quantity) as po_quantity from wo_booking_dtls a,wo_po_break_down b  where a.booking_no=$txt_booking_no and a.booking_type=2 and a.po_break_down_id=b.id and b.status_active=1 and b.is_deleted=0  group by a.job_no,a.po_break_down_id order by a.job_no,a.po_break_down_id "); 
		
		foreach($nameArray_job_po as $nameArray_job_po_row){
		//$all_po_qty=sql_select("select sum(order_quantity) as po_total,po_break_down_id from wo_po_color_size_breakdown where po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and status_active=1 and is_deleted=0 group by po_break_down_id");
		//$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$nameArray_job_po_row[csf('job_no')]."'");
		//$style_ref=return_field_value("style_ref_no", "wo_po_details_master", "job_no='".$nameArray_job_po_row[csf('job_no')]."'");
		$total_order_qty+=$po_qty_po_level[$nameArray_job_po_row[csf('po_break_down_id')]];
		$style_ref=$job_style_arr[$nameArray_job_po_row[csf('job_no')]];
		$costing_per=$costing_per_arr[$nameArray_job_po_row[csf('job_no')]];
		if($costing_per==1){
			$costing_per_qty=12;
		}
		else if($costing_per==2){
			$costing_per_qty=1;
		}
		else if($costing_per==3){
			$costing_per_qty=24;
		}
		else if($costing_per==4){
			$costing_per_qty=36;
		}
		else if($costing_per==5){
			$costing_per_qty=48;
		}
		
		$nameArray_item=sql_select( "select  id,trim_group,country_id_string from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]."  and sensitivity=1 and status_active=1 and is_deleted=0 and wo_qnty>0 order by trim_group ");
	   if(count($nameArray_item)>0){
        ?>
        &nbsp;
        <table style="border:1px solid black" align="left" class=""  cellpadding="0" width="100%" cellspacing="0" rules="all" >
           <tr>
                <td colspan="10" align="">
                <strong><? 
				$style_sting=$style_ref;
				
				echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; Style Ref:".$style_ref." &nbsp;&nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO Qty:" .$po_qty_po_level[$nameArray_job_po_row[csf('po_break_down_id')]];?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="10" align="">
                <strong>As Per Garments Color</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Order Qty.</strong> </td>
                <td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                <td style="border:1px solid black" align="center"><strong>Cost/Dzn(USD)</strong></td>
            </tr>
            <?
			$i=0;
			$trim_amount_arr=array();
			$sql_trim_amount=sql_select("select  cons_uom,trim_group,job_no,description, cons_dzn_gmts, rate, amount from  wo_pre_cost_trim_cost_dtls where job_no='".$nameArray_job_po_row[csf('job_no')]."' and  status_active=1 and is_deleted=0 ");
			foreach($sql_trim_amount as $item){
				$trim_amount_arr[$item[csf("job_no")]][$item[csf("trim_group")]][$item[csf("description")]]['amount']=$item[csf("amount")];
			}
			
            foreach($nameArray_item as $result_item){
				$i++;
				
				$nameArray_item_description=sql_select( "select b.description, b.brand_supplier,b.item_color, b.color_number_id ,min(b.color_size_table_id) as color_size_table_id,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and  a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=1 and   a.id= ".$result_item[csf('id')]." and a.trim_group=".$result_item[csf('trim_group')]." and b.requirment !=0 and b.requirment>0 group by b.description, b.brand_supplier,b.item_color, b.color_number_id order by color_size_table_id "); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center"  style="border:1px solid black; font-weight:bold;" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? 
				$group_arr_check[$result_item[csf('trim_group')]]=$result_item[csf('trim_group')];
				echo $trim_group[$result_item[csf('trim_group')]]."<br/>";
				
				$country_arr=explode(",",$result_item[csf('country_id_string')]);
				$country_name_string="";
				for($co=0; $co <count($country_arr);$co++){
				$country_name_string.=$country_library[$country_arr[$co]].",";
				}
				echo rtrim($country_name_string,',');
				?>
                </td>
                <? 
				$item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription){
					$country_order_qty=0;
					for($coa=0; $coa <count($country_arr);$coa++){
						$country_order_qty+=$order_color_qty[$nameArray_job_po_row[csf('po_break_down_id')]][$country_arr[$coa]][$result_itemdescription[csf('color_number_id')]];
					}	
                ?>
                <td style="border:1px solid black"><?  echo $result_itemdescription[csf('description')]; ?> </td>
                <td style="border:1px solid black" align="right"><? echo $country_order_qty; ?> </td>
                <td style="border:1px solid black;">
               <? echo $color_library[$result_itemdescription[csf('color_number_id')]]; ?> 
                </td>
                <td style="border:1px solid black; text-align:right">
				<?
			    echo number_format($result_itemdescription[csf('cons')],4);
				$item_desctiption_total += $result_itemdescription[csf('cons')] ;
				?>
                </td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_arr_check[$order_uom_arr[$result_item[csf('trim_group')]]]=$order_uom_arr[$result_item[csf('trim_group')]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],6); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_as_per_gmts_color,2);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
                <td style="border:1px solid black; text-align:right">
				<? 
				$trim_amount= $trim_amount_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf("trim_group")]][$result_itemdescription[csf('description')]]['amount'];
				echo ($trim_amount*$costing_per_qty)/12; 
				?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="3"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><? echo number_format($item_desctiption_total ,2); $grand_item_desctiption_total+=$item_desctiption_total;  ?></td>
                <td style="border:1px solid black;  text-align:right"></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right; font-weight:bold;">
                <? 
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER GMTS COLOR END=========================================  -->
        
        
        
        <!--==============================================Size Sensitive START=========================================  -->
		<?
		$nameArray_item=sql_select( "select  id,trim_group,country_id_string from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=2 and status_active=1 and is_deleted=0 and wo_qnty>0 order by trim_group "); 
		if(count($nameArray_item)>0){
        ?>
        
        <table style="border:1px solid black" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="11" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; Style Ref:&nbsp; ".$style_ref."  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO Qty:" .$po_qty_po_level[$nameArray_job_po_row[csf('po_break_down_id')]];?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="11" align="">
                <strong>Size Sensitive </strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Order Qty.</strong> </td>
                <td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
                <td align="center" style="border:1px solid black"><strong>Gmts Size</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty.</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                <td style="border:1px solid black" align="center"><strong>Cost/Dzn(USD)</strong></td>
            </tr>
            <?
			$i=0;
			$trim_amount_arr=array();
			$uom_arr_check=array();
			$sql_trim_amount=sql_select("select  cons_uom,trim_group,job_no,description, cons_dzn_gmts, rate, amount from  wo_pre_cost_trim_cost_dtls where job_no='".$nameArray_job_po_row[csf('job_no')]."' and  status_active=1 and is_deleted=0 ");
			
			foreach($sql_trim_amount as $item){
				$trim_amount_arr[$item[csf("job_no")]][$item[csf("trim_group")]][$item[csf("description")]]['amount']=$item[csf("amount")];
			}

            foreach($nameArray_item as $result_item){
			$i++;
			$nameArray_item_description=sql_select( "select b.description, b.brand_supplier,b.item_size,b.gmts_sizes,min(b.color_size_table_id) as color_size_table_id, sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id=b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=2 and  a.id= ".$result_item[csf('id')]." and a.trim_group=".$result_item[csf('trim_group')]." and b.requirment>0 group by b.description, b.brand_supplier,b.item_size, b.gmts_sizes order by color_size_table_id");
			$sql_trim_amount=sql_select("select  cons_uom, cons_dzn_gmts, rate, amount from  wo_pre_cost_trim_cost_dtls where job_no='".$nameArray_job_po_row[csf('job_no')]."'  and trim_group=".$result_item[csf('trim_group')]." ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black; font-weight:bold;" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? 
				$group_arr_check[$result_item[csf('trim_group')]]=$result_item[csf('trim_group')];
				echo $trim_group[$result_item[csf('trim_group')]]."<br/>"; 
				$country_arr=explode(",",$result_item[csf('country_id_string')]);
				$country_name_string="";
				for($co=0; $co <count($country_arr);$co++){
					$country_name_string.=$country_library[$country_arr[$co]].",";
				}
				echo rtrim($country_name_string,',');
				?>
                </td>
                <? 
                $item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
					$country_order_qty=0;
					for($coa=0; $coa <count($country_arr);$coa++){
					 $country_order_qty+=$order_size_qty[$nameArray_job_po_row[csf('po_break_down_id')]][$country_arr[$coa]][$result_itemdescription[csf('gmts_sizes')]];
					}	
                ?>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('description')]; ?> </td>
                <td style="border:1px solid black" align="right"><? echo $country_order_qty;?> </td>
                <td style="border:1px solid black; text-align:right">
              <? echo $result_itemdescription[csf('item_size')];?>
                </td>
                <td style="border:1px solid black; text-align:right">
              <? echo $size_library[$result_itemdescription[csf('gmts_sizes')]];?>
                </td>
                <td style="border:1px solid black; text-align:right">
				<? 
				 echo number_format($result_itemdescription[csf('cons')],2);
                $item_desctiption_total += $result_itemdescription[csf('cons')] ;
				?>
                </td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_arr_check[$order_uom_arr[$result_item[csf('trim_group')]]]=$order_uom_arr[$result_item[csf('trim_group')]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],6); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_as_per_gmts_color,2);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
                <td style="border:1px solid black; text-align:right"><? $trim_amount= $trim_amount_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf("trim_group")]][$result_itemdescription[csf('description')]]['amount'];echo ($trim_amount*$costing_per_qty)/12;  ?></td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="4"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><? echo number_format($item_desctiption_total,2); $grand_item_desctiption_total+=$item_desctiption_total;  ?></td>
                 <td style="border:1px solid black;  text-align:right"></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right; font-weight:bold;">
                <? 
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
        </table>
        <br/>
        <?
		}
		?>
        
        <!--==============================================Size Sensitive END=========================================  -->
        
         <!--==============================================AS PER CONTRAST COLOR START=========================================  -->
		<?
		 $nameArray_item=sql_select( "select  id,trim_group,country_id_string from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=3 and status_active=1 and is_deleted=0 and wo_qnty>0 order by trim_group "); 
		if(count($nameArray_item)>0){
        ?>
        <table style="border:1px solid black" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
           <tr>
                <td colspan="11" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; Style Ref:&nbsp; ".$style_ref." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO Qty:" .$po_qty_po_level[$nameArray_job_po_row[csf('po_break_down_id')]];?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="11" align="">
                <strong>Contrast Color</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Order Qty.</strong> </td>
              
                <td align="center" style="border:1px solid black"><strong>Gmts Color</strong></td>
                <td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty.</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                <td style="border:1px solid black" align="center"><strong>Cost/Dzn(USD)</strong></td>
            </tr>
            <?
			$i=0;
			$trim_amount_arr=array();$uom_arr_check=array();
			$sql_trim_amount=sql_select("select  cons_uom,trim_group,job_no,description, cons_dzn_gmts, rate, amount from  wo_pre_cost_trim_cost_dtls where job_no='".$nameArray_job_po_row[csf('job_no')]."' and  status_active=1 and is_deleted=0 ");
			
			foreach($sql_trim_amount as $item){
				$trim_amount_arr[$item[csf("job_no")]][$item[csf("trim_group")]][$item[csf("description")]]['amount']=$item[csf("amount")];
			}
            foreach($nameArray_item as $result_item){
				$i++;
				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=3 and  a.id= ".$result_item[csf('id')]." and a.trim_group=".$result_item[csf('trim_group')]." and b.requirment !=0  order by trim_group ", "item_color", "color_number_id"  );
				$nameArray_item_description=sql_select( "select b.description, b.brand_supplier,b.item_color,b.color_number_id,sum(b.requirment) as cons,min(b.color_size_table_id) as color_size_table_id,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=3 and  a.id= ".$result_item[csf('id')]." and a.trim_group=".$result_item[csf('trim_group')]." and b.requirment !=0 group by b.description, b.brand_supplier,b.item_color,b.color_number_id order by color_size_table_id "); 
				$sql_trim_amount=sql_select("select  cons_uom, cons_dzn_gmts, rate, amount from  wo_pre_cost_trim_cost_dtls where job_no='".$nameArray_job_po_row[csf('job_no')]."'  and trim_group=".$result_item[csf('trim_group')]." ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black; font-weight:bold;" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? 
				$group_arr_check[$result_item[csf('trim_group')]]=$result_item[csf('trim_group')];
				echo $trim_group[$result_item[csf('trim_group')]]."<br/>"; 
				$country_arr=explode(",",$result_item[csf('country_id_string')]);
				$country_name_string="";
				for($co=0; $co <count($country_arr);$co++){
					$country_name_string.=$country_library[$country_arr[$co]].",";
				}
				echo rtrim($country_name_string,',');
				?>
                </td>
                <? 
                $item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription){
					$country_order_qty=0;
					for($coa=0; $coa <count($country_arr);$coa++){
					 $country_order_qty+=$order_color_qty[$nameArray_job_po_row[csf('po_break_down_id')]][$country_arr[$coa]][$gmtcolor_library[$result_itemdescription[csf('item_color')]]];
					}
                ?>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('description')]; ?> </td>
                <td style="border:1px solid black" align="right"><? echo $country_order_qty; ?> </td>
                <td style="border:1px solid black">
               <? echo $color_library[$result_itemdescription[csf('color_number_id')]]; ?>
                </td>
                <td style="border:1px solid black">
               <? echo $color_library[$result_itemdescription[csf('item_color')]]; ?>
                </td>
                <td style="border:1px solid black; text-align:right">
				<? 
				echo number_format($result_itemdescription[csf('cons')],2);
                $item_desctiption_total += $result_itemdescription[csf('cons')] ;
				?>
                </td>
                 <td style="border:1px solid black; text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_arr_check[$order_uom_arr[$result_item[csf('trim_group')]]]=$order_uom_arr[$result_item[csf('trim_group')]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],6); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,2);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
                <td style="border:1px solid black; text-align:right"><? $trim_amount= $trim_amount_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf("trim_group")]][$result_itemdescription[csf('description')]]['amount'];echo ($trim_amount*$costing_per_qty)/12;  ?></td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="4"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><? echo number_format($item_desctiption_total,2); $grand_item_desctiption_total+=$item_desctiption_total;  ?></td>
                <td style="border:1px solid black;text-align:center"></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right; font-weight:bold;">
                <? 
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
        </table>
        <br/>
        <?
		}
		?>
        
        <!--==============================================AS PER CONTRAST COLOR END=========================================  -->
        
        <!--==============================================AS PER GMTS Color & SIZE START=========================================  -->
		<?
		
		$nameArray_item=sql_select( "select  id,trim_group,country_id_string from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=4 and status_active=1 and is_deleted=0 and wo_qnty>0 order by trim_group ");
	   if(count($nameArray_item)>0)
		{
        ?>
        
        <table style="border:1px solid black" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
             <tr>
                <td colspan="13" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; Style Ref:&nbsp; ".$style_ref." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO Qty:" .$po_qty_po_level[$nameArray_job_po_row[csf('po_break_down_id')]];?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="13" align="">
                <strong>Color & size sensitive </strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Order Qty.</strong> </td>
                
                <td style="border:1px solid black"><strong>Gmts Color</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
                <td align="center" style="border:1px solid black"><strong>Gmts Size</strong></td>
                			
                <td style="border:1px solid black" align="center"><strong>WO Qty.</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                <td style="border:1px solid black" align="center"><strong>Cost/Dzn(USD)</strong></td>
            </tr>
            <?
			$i=0;
			$trim_amount_arr=array();
			$sql_trim_amount=sql_select("select  cons_uom,trim_group,job_no,description, cons_dzn_gmts, rate, amount from  wo_pre_cost_trim_cost_dtls where job_no='".$nameArray_job_po_row[csf('job_no')]."' and  status_active=1 and is_deleted=0 ");
			
			foreach($sql_trim_amount as $item)
            {
				$trim_amount_arr[$item[csf("job_no")]][$item[csf("trim_group")]][$item[csf("description")]]['amount']=$item[csf("amount")];
			}
            foreach($nameArray_item as $result_item)
            {
			$i++;
			$nameArray_color=sql_select( "select  b.item_color,b.color_number_id,b.item_size,b.gmts_sizes,min(b.color_size_table_id) as color_size_table_id,b.description, b.brand_supplier,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id   and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and  a.id= ".$result_item[csf('id')]." and a.trim_group=".$result_item[csf('trim_group')]." and a.sensitivity=4 and b.requirment !=0 group by b.item_color,b.color_number_id,b.item_size,b.gmts_sizes,b.description, b.brand_supplier order by color_size_table_id "); 
            $nameArray_item_description=sql_select( "select distinct uom from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=4   and trim_group=".$result_item[csf('trim_group')]." order by trim_group "); 
			$sql_trim_amount=sql_select("select  cons_uom, cons_dzn_gmts, rate, amount from  wo_pre_cost_trim_cost_dtls where job_no='".$nameArray_job_po_row[csf('job_no')]."'  and trim_group=".$result_item[csf('trim_group')]." ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo   (count($nameArray_color)); ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black; font-weight:bold;" rowspan="<? echo (count($nameArray_color)); ?>">
                <? 
				$group_arr_check[$result_item[csf('trim_group')]]=$result_item[csf('trim_group')];
				echo $trim_group[$result_item[csf('trim_group')]]."<br/>"; 
				$country_arr=explode(",",$result_item[csf('country_id_string')]);
				$country_name_string="";
				for($co=0; $co <count($country_arr);$co++)
				{
				 $country_name_string.=$country_library[$country_arr[$co]].",";
				}
				echo rtrim($country_name_string,','); 
				?>
                </td>
                <? 
				$item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
				$uom_arr_check=array();
				foreach($nameArray_color as $result_color)
                {
					$country_order_qty=0;
					for($coa=0; $coa <count($country_arr);$coa++)
					{
					 $country_order_qty+=$order_color_size_qty[$nameArray_job_po_row[csf('po_break_down_id')]][$country_arr[$coa]][$result_color[csf('color_number_id')]][$result_color[csf('gmts_sizes')]];
					}
					?>
					<td style="border:1px solid black" ><? echo $result_color[csf('description')]; ?> </td>
					<td style="border:1px solid black" align="right" ><? echo $country_order_qty; ?> </td>
                    <td style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; ?> </td>
                    <td style="border:1px solid black"><? echo $color_library[$result_color[csf('item_color')]]; ?> </td>
					<td style="border:1px solid black; text-align:right">
					<? echo $result_color[csf('item_size')]; ?> 
					</td>
                    <td style="border:1px solid black; text-align:right">
					<? echo $size_library[$result_color[csf('gmts_sizes')]]; ?> 
					</td>
					<td style="border:1px solid black; text-align:right">
					<?
					echo number_format($result_color[csf('cons')],2);
					$item_desctiption_total += $result_color[csf('cons')] ;
					?>
                    </td>
					<td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_arr_check[$order_uom_arr[$result_item[csf('trim_group')]]]= $order_uom_arr[$result_item[csf('trim_group')]];?></td>
					<td style="border:1px solid black; text-align:right">
					<?
					echo number_format($result_color[csf('rate')],6);
					?>
                     </td>
					<td style="border:1px solid black; text-align:right">
					<? 
					$amount_as_per_gmts_color =$result_color[csf('amount')];
					echo number_format($amount_as_per_gmts_color,2);
					$total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
					?>
					</td>
                    <td style="border:1px solid black; text-align:right"><? $trim_amount= $trim_amount_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf("trim_group")]][$result_color[csf('description')]]['amount']; echo ($trim_amount*$costing_per_qty)/12;  ?></td>
				</tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="8"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><? echo number_format($item_desctiption_total,2); $grand_item_desctiption_total+=$item_desctiption_total; ?></td>
                <td style="border:1px solid black;  text-align:right"></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right; font-weight:bold;">
                <? 
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
			
            ?>
        </table>
        <br/>
       <?
		}
	   ?>
        <!--==============================================AS PER Color & SIZE  END=========================================  -->
        
        
         <!--==============================================NO NENSITIBITY START=========================================  -->
		<?
		$nameArray_item=sql_select( "select  id,trim_group,country_id_string from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=0 and status_active=1 and is_deleted=0 and wo_qnty>0 order by trim_group ");
		if(count($nameArray_item)>0)
		{
        ?>
        <table  style="border:1px solid black" align="left" class=""  cellpadding="0" width="100%" cellspacing="0" rules="all" >
        
            <tr>
                <td colspan="810" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; Style Ref:&nbsp; ".$style_ref." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO Qty:" .$po_qty_po_level[$nameArray_job_po_row[csf('po_break_down_id')]];?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="10" align="">
                <strong>No Sensitivity</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Order Qty.</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td align="center" style="border:1px solid black"><strong> Qnty</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                <td style="border:1px solid black" align="center"><strong>Cost/Dzn(USD)</strong></td>
            </tr>
            <?
			$i=0;
			$trim_amount_arr=array();
			$sql_trim_amount=sql_select("select  cons_uom,trim_group,job_no,description, cons_dzn_gmts, rate, amount from  wo_pre_cost_trim_cost_dtls where job_no='".$nameArray_job_po_row[csf('job_no')]."' and  status_active=1 and is_deleted=0 ");
			
			foreach($sql_trim_amount as $item)
            {
				$trim_amount_arr[$item[csf("job_no")]][$item[csf("trim_group")]][$item[csf("description")]]['amount']=$item[csf("amount")];
			}
			$grand_color_total=0;$uom_arr_check=array();
            foreach($nameArray_item as $result_item)
            {
				$i++;
				
				$nameArray_item_description=sql_select( "select  b.description, b.brand_supplier,b.item_color,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and  a.id= ".$result_item[csf('id')]." and a.trim_group=".$result_item[csf('trim_group')]." and a.sensitivity=0 group by b.description, b.brand_supplier,b.item_color"); 
				
            $sql_trim_amount=sql_select("select  cons_uom, cons_dzn_gmts, rate, amount from  wo_pre_cost_trim_cost_dtls where job_no='".$nameArray_job_po_row[csf('job_no')]."'  and trim_group=".$result_item[csf('trim_group')]." ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black; font-weight:bold;" rowspan="<? echo count($nameArray_item_description)+1; ?>">

                <?
				$group_arr_check[$result_item[csf('trim_group')]]=$result_item[csf('trim_group')];
				echo $trim_group[$result_item[csf('trim_group')]]."<br/>"; 
				$country_arr=explode(",",$result_item[csf('country_id_string')]);
				$country_name_string="";
				for($co=0; $co <count($country_arr);$co++)
				{
				 $country_name_string.=$country_library[$country_arr[$co]].",";
				}
				echo rtrim($country_name_string,',');  
				?>
                </td>
                <? 
                $color_tatal=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
				$country_order_qty=0;
				for($coa=0; $coa <count($country_arr);$coa++)
				{
				 $country_order_qty+=$order_qty_arr[$nameArray_job_po_row[csf('po_break_down_id')]][$country_arr[$coa]];
				}
				
                ?>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('description')]; ?> </td>
                <td style="border:1px solid black" align="right"><? echo $country_order_qty; ?> </td>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('item_color')]]; ?> </td>
                <?
				if($db_type==0)
				{
				$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=0 and  a.id= ".$result_item[csf('id')]." and a.trim_group=". $result_item[csf('trim_group')]." and  b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='".$result_itemdescription[csf('brand_supplier')]."' and b.item_color='".$result_itemdescription[csf('item_color')]."'");
				}
				if($db_type==2)
				{
				$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=0 and  a.id= ".$result_item[csf('id')]." and a.trim_group=". $result_item[csf('trim_group')]." and nvl( b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_color,0)=nvl('".$result_itemdescription[csf('item_color')]."',0)");
				}
                          
                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right;">
                <? 
                if($result_color_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_color_size_qnty[csf('cons')],2);
                $item_desctiption_total += $result_color_size_qnty[csf('cons')] ;
                $color_tatal+=$result_color_size_qnty[csf('cons')];
                }
                else echo "";
                ?>
                </td>
                <?   
                }
                ?>
                
                <td style="border:1px solid black; text-align:center "><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; if($order_uom_arr[$result_item[csf('trim_group')]]>0) $uom_arr_check[]=$order_uom_arr[$result_item[csf('trim_group')]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],6); ?> </td>
                <td style="border:1px solid black; text-align:right;">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,2);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
                <td style="border:1px solid black; text-align:right;"><? $trim_amount= $trim_amount_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf("trim_group")]][$result_itemdescription[csf('description')]]['amount']; echo ($trim_amount*$costing_per_qty)/12;  ?></td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="3"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;">
                <?
                if($color_tatal !='')
                {
                echo number_format($color_tatal,2);
				$grand_item_desctiption_total+= $color_tatal; 
                }
                ?>
                </td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right; font-weight:bold;">
                <? 
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
        </table>
        <?
		}
		}
		?>
        <!--==============================================NO NENSITIBITY END=========================================  -->
       &nbsp;
       <?
       $mcurrency="";
	   $dcurrency="";
	   if($currency_id==1){
		$mcurrency='Taka';
		$dcurrency='Paisa'; 
	   }
	   if($currency_id==2){
		$mcurrency='USD';
		$dcurrency='CENTS'; 
	   }
	   if($currency_id==3){
		$mcurrency='EURO';
		$dcurrency='CENTS'; 
	   }
	   ?>
       <table  width="100%" class=""  style="border:1px solid black" cellpadding="0" cellspacing="0" rules="all" align="left">
            <tr style="border:1px solid black;">
                <td width="14%"  style="border:1px solid black; text-align:right; font-weight:bold; ">Total Order Qty:</td>
				<td width="17%"  style="border:1px solid black; text-align:right; font-weight:bold; "><? echo number_format($total_order_qty,2);?></td>
                <td width="22%" style="border:1px solid black;  text-align:right; font-weight:bold; "> Grand Total:</td> 
                <td width="9%" style="border:1px solid black;  text-align:right; font-weight:bold; ">
				<?
				if(count($uom_arr_check)<2 && count($group_arr_check)<2){
					echo number_format($grand_item_desctiption_total,2);
				}
				?>
                </td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold; padding-right:160px;"><? echo number_format($grand_total_as_per_gmts_color,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <td colspan="4" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</td>
                <td style="border:1px solid black;"><? echo number_to_words(def_number_format($grand_total_as_per_gmts_color,2,""),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
       &nbsp;
          
         <? echo get_spacial_instruction($txt_booking_no);?>
    
    <br>
    <?
	if(str_replace("'","",$show_comment)==1){
	?>
    <table cellspacing="0" cellpadding="0" style="border:1px solid black" rules="all" width="100%" class=""  align="left">
            <thead>
            	<tr>
                	<th colspan="9" style="font-size:18px; font-weight:bold;">Comments</th>
                </tr>
                <tr style="border:1px solid black">
                    <th width="40" style="border:1px solid black">SL</th>
                    <th width="135" style="border:1px solid black">Item</th>
                    <th width="135" style="border:1px solid black">PO No</th>
                    <th width="90" style="border:1px solid black">Pre-Cost Value</th>
                    <th width="90" style="border:1px solid black">Main Trims Value</th>
                    <th width="90" style="border:1px solid black">Short Trims Value</th>
                    <th width="90" style="border:1px solid black">Sample Trims Value</th>
                    <th width="90" style="border:1px solid black">Total</th>
                    <th width="90" style="border:1px solid black">Balance</th>
                    <th width="" style="border:1px solid black">Comments </th>
                </tr>
            </thead>
           <tbody>
           <?
					$sql_lib_item_group_array=array();
					$main_booking_array=array();
					$main_booking_array2=array();
					$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
					foreach($sql_lib_item_group as $row_sql_lib_item_group){
						$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
						$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
						$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
					}
					
					$sql="select job_no,po_break_down_id,pre_cost_fabric_cost_dtls_id,trim_group,country_id_string from wo_booking_dtls where booking_no=$txt_booking_no and status_active=1 and is_deleted=0 group by job_no,po_break_down_id,pre_cost_fabric_cost_dtls_id,trim_group,country_id_string";
					
					$i=1;
					$total_amount=0;
					$pre_amount=0; 
					$pre_amount_total=0;
					$cu_woq_amount_total=0;
					$cu_woq_amount_short_total=0;
					$cu_woq_amount_sample_total=0;
					$total_value_total=0;
					$trims_balance_total=0;
                    $nameArray=sql_select( $sql );
                    foreach ($nameArray as $selectResult){
						$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$selectResult[csf('job_no')]."'");
						if($costing_per==1){
							$costing_per_qty=12;
						}
						else if($costing_per==2){
							$costing_per_qty=1;
						}
						else if($costing_per==3){
							$costing_per_qty=24;
						}
						else if($costing_per==4){
							$costing_per_qty=36;
						}
						else if($costing_per==5){
							$costing_per_qty=48;
						}
						if($selectResult[csf('country_id_string')]!="") $contry_cond=" and c.country_id in(".$selectResult[csf('country_id_string')].")"; else $contry_cond="";
						$sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='".$selectResult[csf('po_break_down_id')]."' $contry_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty");
						list($sql_po_qty_row)=$sql_po_qty;
						$po_qty=$sql_po_qty_row[csf('order_quantity_set')];
						
						$sql_cons_data=sql_select("select a.rate,b.cons from wo_pre_cost_trim_cost_dtls a , wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id and  a.id='".$selectResult[csf('pre_cost_fabric_cost_dtls_id')]."' and b.po_break_down_id='".$selectResult[csf('po_break_down_id')]."' and a.is_deleted=0  and a.status_active=1");
						list($sql_cons_data_row)=$sql_cons_data;
						$pre_cons=$sql_cons_data_row[csf('cons')];
						$pre_rate=$sql_cons_data_row[csf('rate')];
						
						$pre_req_qnty=def_number_format(($pre_cons*($po_qty/$costing_per_qty))/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],2,"");
						$pre_amount=$pre_req_qnty*$pre_rate;
						
						$booking_no=str_replace("'","",$txt_booking_no);
						$booking_id=return_field_value("id","wo_booking_mst","booking_type=2 and booking_no='$booking_no'","id");
						$prev_book_cond="";
						if($booking_id>0) $prev_book_cond=" and a.id<$booking_id";
						
						
						$prev_wo_amt=sql_select("select sum(b.amount) as prev_amount, a.exchange_rate from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.booking_no!=$txt_booking_no and b.po_break_down_id='".$selectResult[csf('po_break_down_id')]."' and b.pre_cost_fabric_cost_dtls_id='".$selectResult[csf('pre_cost_fabric_cost_dtls_id')]."' $prev_book_cond and  b.booking_type=2 and b.is_short=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by a.exchange_rate");
						$prev_wo_amount=0;
						foreach($prev_wo_amt as $row)
						{
							$prev_wo_amount+=$row[csf("prev_amount")]/$row[csf("exchange_rate")];
						}
						
						
						$sql_cu_woq=sql_select("select sum(b.amount) as amount, a.exchange_rate from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.booking_no=$txt_booking_no and b.po_break_down_id='".$selectResult[csf('po_break_down_id')]."' and b.pre_cost_fabric_cost_dtls_id='".$selectResult[csf('pre_cost_fabric_cost_dtls_id')]."'  and  b.booking_type=2 and  b.is_short=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.exchange_rate");
						$cureent_wo_amt=0*1;
						foreach($sql_cu_woq as $row)
						{
							$cureent_wo_amt+=$row[csf("amount")]/$row[csf("exchange_rate")];
						}
						
						$cu_woq_amount=($cureent_wo_amt+$prev_wo_amount);
						
						$sql_cu_woq_short=sql_select("select sum(amount) as amount  from wo_booking_dtls where po_break_down_id='".$selectResult[csf('po_break_down_id')]."' and pre_cost_fabric_cost_dtls_id='".$selectResult[csf('pre_cost_fabric_cost_dtls_id')]."'  and  booking_type=2 and is_short=1 and status_active=1 and is_deleted=0");
						list($sql_cu_woq_row_short)=$sql_cu_woq_short;
						$cu_woq_amount_short=$sql_cu_woq_row_short[csf('amount')];
						
						$sql_cu_woq_sample=sql_select("select sum(amount) as amount  from wo_booking_dtls where po_break_down_id='".$selectResult[csf('po_break_down_id')]."' and pre_cost_fabric_cost_dtls_id='".$selectResult[csf('pre_cost_fabric_cost_dtls_id')]."'  and  booking_type=5 and status_active=1 and is_deleted=0");
						list($sql_cu_woq_row_sample)=$sql_cu_woq_sample;
						$cu_woq_amount_sample=$sql_cu_woq_row_sample[csf('amount')];
						$total_value=($cu_woq_amount)+($cu_woq_amount_short/$exchange_rate)+($cu_woq_amount_sample/$exchange_rate);
						$trims_balance=$pre_amount-$total_value;
						?>
						<tr style="border:1px solid black">
                            <td style="border:1px solid black"><? echo $i;?></td>
                            <td style="border:1px solid black"><? echo $trim_group[$selectResult[csf('trim_group')]];?></td>
                            <td style="border:1px solid black"><? echo $po_number[$selectResult[csf('po_break_down_id')]];?>  </td>
                            <td align="right" style="border:1px solid black"><? echo number_format($pre_amount,2);$pre_amount_total+=$pre_amount; ?></td>
                            <td align="right" style="border:1px solid black"><? echo number_format($cu_woq_amount,2);$cu_woq_amount_total+=$cu_woq_amount;?></td>
                            <td align="right" style="border:1px solid black"><? echo number_format($cu_woq_amount_short/$exchange_rate,2);$cu_woq_amount_short_total+=($cu_woq_amount_short/$exchange_rate);?></td>
                            <td align="right" style="border:1px solid black"><? echo number_format($cu_woq_amount_sample/$exchange_rate,2);$cu_woq_amount_sample_total+=($cu_woq_amount_sample/$exchange_rate);?></td>
                            <td align="right" style="border:1px solid black"><? echo number_format($total_value,2);$total_value_total+=$total_value?></td>
                            <td align="right" style="border:1px solid black"><? echo number_format($trims_balance,2);$trims_balance_total+=$trims_balance;?></td>
                            <td align="right" style="border:1px solid black">
                            <?
                            if($pre_amount==($total_value)){
                            	echo "At Per";
                            }
                            if($pre_amount>($total_value)){
                            	echo "Less Booking";
                            }
                            if($pre_amount<($total_value)){
                            	echo "Over Booking";
                            }
                            ?>
                            </td>
						</tr>
						<?
                        $i++;
                    }
					?>
		</tbody>
        <tfoot>
         <tr align="right">
                    <th  colspan="3"width="135" style="border:1px solid black">Total:</th>
                    <th width="90" style="border:1px solid black"><? echo number_format($pre_amount_total,2); ?></th>
                    <th width="90" style="border:1px solid black"><? echo number_format($cu_woq_amount_total,2); ?></th>
                    <th width="90" style="border:1px solid black"><? echo number_format($cu_woq_amount_short_total,2); ?></th>
                    <th width="90" style="border:1px solid black"><? echo number_format($cu_woq_amount_sample_total,2); ?></th>
                    <th width="90" style="border:1px solid black"><? echo number_format($total_value_total,2); ?></th>
                    <th width="90" style="border:1px solid black"><? echo number_format($trims_balance_total,2); ?></th>
                    <th width="" style="border:1px solid black"></th>
         </tr>
         </tfoot>
    </table>
    <?
	}
	?>
           <br>
        <!--<div style="max-width:1000;">-->
      	<style>
			.rpt_ntable{
				border:1px solid black;
			}
			.rpt_ntable tr{
				border:1px solid black;
			}
			.rpt_ntable tr td{
				border:1px solid black;
			}
		</style>
        <table width="100%"  class="rpt_ntable"  cellpadding="0" cellspacing="0" rules="all"  align="left"> 
          <tr><td colspan="14" align="center"><b>TNA Information</b></td></tr>                   	
            <tr>
            	<td rowspan="2" align="center" valign="top">SL</td>
            	<td width="180" rowspan="2"  align="center" valign="top"><b>Order No</b></td>
                <td colspan="2" align="center" valign="top"><b>Trims Book[SnF]</b></td>
                <td colspan="2" align="center" valign="top"><b>Sew Trims Rcv.</b></td>
                <td colspan="2" align="center" valign="top"><b>Cutting</b></td>
                <td colspan="2" align="center" valign="top"><b>Fin Trims Rcv. </b></td>
                <td colspan="2" align="center" valign="top"><b>Sewing </b></td>
                <td colspan="2"  align="center" valign="top"><b>Ex-factory </b></td>
            </tr> 
            <tr>
            	<td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                
            </tr>
            <?
			$i=1;
			foreach($tna_date_task_arr as $order_id=>$row){
				?>
                <tr>
                	<td><? echo $i; ?></td>
                    <td><? echo $po_num_arr[$order_id]; ?></td>
                	<td align="center"><? echo change_date_format($row['trims_booking_start_date']); ?></td>
                    <td  align="center"><? echo change_date_format($row['trims_booking_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['Sew_Trims_rcv_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['Sew_Trims_rcv_end_date']); ?></td>                    
                    <td align="center"><? echo change_date_format($row['cutting_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['cutting_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['fin_Trims_rcv_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['fin_Trims_rcv_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['sewing_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['sewing_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['exfact_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['exfact_end_date']); ?></td>
                </tr>
                <?
				$i++;
			}
			?> 
            
            <tr>
            
            </tr>
           
        </table>
        
        <br>
        
        <table cellspacing="0" cellpadding="0" rules="all" width="100%" class=""  align="left">
        	<tr>
            	<td>
				<? 
					echo signature_table(2, $cbo_company_name, "1030px"); 
					echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
				?>
                </td>
            </tr>
        </table>
        <!--</div>  --> 
   
    </div>
	<?	
	if($link==1){
		?>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
		<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
		<? 
	}
	else{
		?>
		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		<? 
	}
	?>
	<script>
	fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
	</script>
	<?
   exit(); 
}

if($action=="show_trim_booking_report4"){
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$order_uom_arr=return_library_array("select id,order_uom  from lib_item_group","id","order_uom");
	$team_member_arr=return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$location_name_arr=return_library_array( "select company_id,location_name from lib_location where status_active=1 and is_deleted=0",'company_id','location_name');
	?>
    <style>
@media print {
    .gg {page-break-after: always;}
}
</style>
	<div style="width:1033px" align="center">       
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black; margin-bottom:20px;" align="left">
            <tr>
                <td width="100"> 
					<? if($link==1){?>
                    <img  src='../../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
                    <? }else{?>
                    <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
                    <? }?>
                </td>
                <td width="1000">                                     
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                            <?php      
                            echo $company_library[$cbo_company_name];
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
                            <?
                            $nameArray=sql_select( "select id, plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
                            foreach ($nameArray as $result){ 
								if($result[csf('plot_no')])echo $result[csf('plot_no')].', ';  
								if($result[csf('level_no')])echo $result[csf('level_no')].', ';
								if($result[csf('road_no')])echo $result[csf('road_no')].', ';  
								if($result[csf('block_no')])echo $result[csf('block_no')].', '; 
								if($result[csf('city')])echo $result[csf('city')].', ';
								if($result[csf('zip_code')])echo $result[csf('zip_code')].', '; 
								if($result[csf('province')])echo $result[csf('province')].', ';
								echo $country_arr[$result[csf('country_id')]];
                            }
                            ?>   
                            </td> 
                        </tr>
                        <tr>
                            <td align="center" style="font-size:20px">  
                            <strong>
                            <? 
                            if(str_replace("'","",$cbo_isshort)==2){
								$isshort="";	
                            }
                            else if(str_replace("'","",$cbo_isshort)==1){
								$isshort="[Short]";	
                            }
                            
                            if ($report_title !=""){
								echo $report_title." ".$isshort;
                            } 
                            else {
								echo "Main Trims Booking ".$isshort;
                            }  
                            ?> 
                            &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <font style="color:#F00">
                            <? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> 
                            </font>
                            </strong>
                            </td> 
                        </tr>
                    </table>
                </td> 
                <td width="250" id="barcode_img_id"> 
                </td>        
            </tr>
        </table>
		<?
		$booking_grand_total=0;
		$grand_total_as_per_gmts_color=0;
		$grand_item_desctiption_total=0;
		$uom_arr_check=array();
		$group_arr_check=array();
		$currency_id="";
		
		/*$job_no="";
		$nameArray_job=sql_select( "select distinct b.job_no  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.booking_type=2"); 
        foreach ($nameArray_job as $result_job)
        {
			if($job_no!="") { $job_no.=",".$result_job[csf('job_no')]; }
			else $job_no=$result_job[csf('job_no')];
		}*/
		
		/*$style_ref="";
		$deling_marchent="";
		$nameArray_style=sql_select( "select distinct a.style_ref_no,a.dealing_marchant  from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no"); 
        foreach ($nameArray_style as $result_style)
        {
			$style_ref.=$result_style[csf('style_ref_no')].", ";
			$deling_marchent.=$team_member_arr[$result_style[csf('dealing_marchant')]].", ";
		}*/
		
		
		/*$po_id="";
		$nameArray_job=sql_select( "select  b.id  as po_id,b.shiping_status from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.booking_type=2"); 
        foreach ($nameArray_job as $result_job){
			if($po_id!=""){
				$po_id.=",".$result_job[csf('po_id')]; 
			}
			else{
				$po_id=$result_job[csf('po_id')];
			}
			$po_status_arr[]=$result_job[csf('shiping_status')];
		}*/
		
		/*$nameArray_job=sql_select( "select  min(b.shipment_date) as ship_date  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.booking_type=2"); 
        foreach ($nameArray_job as $result_job){
			$min_shipdate=$result_job[csf('ship_date')];
		}*/
		//echo "select  b.job_no,a.buyer_name,a.style_ref_no,a.dealing_marchant,c.id  as po_id,c.po_number,c.shiping_status,c.shipment_date,d.costing_per  from wo_po_details_master a, wo_booking_dtls b, wo_po_break_down c,wo_pre_cost_mst d where a.job_no=b.job_no and a.job_no=c.job_no_mst and a.job_no=d.job_no and b.po_break_down_id=c.id and b.booking_no=$txt_booking_no and b.booking_type=2 order by c.shipment_date desc";
				//$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$nameArray_job_po_row[csf('job_no')]."'");

		$po_num_arr=array();
		$po_id_arr=array();
		$po_status_arr=array();
		$job_no_string_arr=array();
		$buyer_string_arr=array();
		$style_ref_no_string_arr=array();
		$dealing_marchant_string_arr=array();
		$job_style_arr=array();
		$costing_per_arr=array();
		$nameArray_buyer=sql_select( "select  b.job_no,a.buyer_name,a.style_ref_no,a.dealing_marchant,c.id  as po_id,c.po_number,c.shiping_status,c.shipment_date,c.pub_shipment_date,d.costing_per   from wo_po_details_master a, wo_booking_dtls b, wo_po_break_down c ,wo_pre_cost_mst d where a.job_no=b.job_no and a.job_no=c.job_no_mst and a.job_no=d.job_no and b.po_break_down_id=c.id and b.booking_no=$txt_booking_no and b.booking_type=2 order by c.shipment_date desc"); 
        foreach ($nameArray_buyer as $result_buy)
		{
			$job_no_string_arr[$result_buy[csf('job_no')]]=$result_buy[csf('job_no')];
			$buyer_string_arr[$result_buy[csf('buyer_name')]]=$buyer_name_arr[$result_buy[csf('buyer_name')]];
			$style_ref_no_string_arr[$result_buy[csf('style_ref_no')]]=$result_buy[csf('style_ref_no')];
			$job_style_arr[$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
			$dealing_marchant_string_arr[$result_buy[csf('dealing_marchant')]]=$team_member_arr[$result_buy[csf('dealing_marchant')]];
			$po_num_arr[$result_buy[csf('po_id')]]=$result_buy[csf('po_number')];
			$po_id_arr[$result_buy[csf('po_id')]]=$result_buy[csf('po_id')];
			$po_status_arr[]=$result_buy[csf('shiping_status')];
			$min_shipdate=$result_buy[csf('shipment_date')];
			$costing_per_arr[$result_buy[csf('job_no')]]=$result_buy[csf('costing_per')];
		}
		$job_no=implode(",",$job_no_string_arr);
		$buyer_string=implode(",",$buyer_string_arr);
		$style_ref=implode(",",$style_ref_no_string_arr);
		$deling_marchent=implode(",",$dealing_marchant_string_arr);
		$po_id=implode(",",$po_id_arr);
		
		$po_status_arr=array_unique($po_status_arr);
		$shipping_status=0;
		if(count($po_status_arr)==1){
			$shipping_status=$po_status_arr[0];
		}
		else if(count($po_status_arr)>1){
			$shipping_status=2;
		}
		
		//TNA Information
		$tna_start_sql=sql_select( "select id,po_number_id, 
								(case when task_number=31 then task_start_date else null end) as fab_booking_start_date,
								(case when task_number=31 then task_finish_date else null end) as fab_booking_end_date,
								(case when task_number=32 then task_start_date else null end) as trims_booking_start_date,
								(case when task_number=32 then task_finish_date else null end) as trims_booking_end_date,
								(case when task_number=70 then task_start_date else null end) as Sew_Trims_rcv_start_date,
								(case when task_number=70 then task_finish_date else null end) as Sew_Trims_rcv_end_date,								
								(case when task_number=84 then task_start_date else null end) as cutting_start_date,
								(case when task_number=84 then task_finish_date else null end) as cutting_end_date,
								(case when task_number=71 then task_start_date else null end) as fin_Trims_rcv_start_date,
								(case when task_number=71 then task_finish_date else null end) as fin_Trims_rcv_end_date,
								(case when task_number=86 then task_start_date else null end) as sewing_start_date,
								(case when task_number=86 then task_finish_date else null end) as sewing_end_date,
								(case when task_number=110 then task_start_date else null end) as exfact_start_date,
								(case when task_number=110 then task_finish_date else null end) as exfact_end_date
		from tna_process_mst
		where status_active=1 and po_number_id in($po_id)");
		$tna_fab_start=$tna_knit_start=$tna_dyeing_start=$tna_fin_start=$tna_cut_start=$tna_sewin_start=$tna_exfact_start="";
		$tna_date_task_arr=array();
		foreach($tna_start_sql as $row){
			if($row[csf("fab_booking_start_date")]!="" && $row[csf("fab_booking_start_date")]!="0000-00-00"){
				if($tna_fab_start==""){
					$tna_fab_start=$row[csf("fab_booking_start_date")];
				}
			}
			if($row[csf("trims_booking_start_date")]!="" && $row[csf("trims_booking_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['trims_booking_start_date']=$row[csf("trims_booking_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['trims_booking_end_date']=$row[csf("trims_booking_end_date")];
					}
					if($row[csf("Sew_Trims_rcv_start_date")]!="" && $row[csf("Sew_Trims_rcv_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['Sew_Trims_rcv_start_date']=$row[csf("Sew_Trims_rcv_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['Sew_Trims_rcv_end_date']=$row[csf("Sew_Trims_rcv_end_date")];
					}					
					if($row[csf("cutting_start_date")]!="" && $row[csf("cutting_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_start_date']=$row[csf("cutting_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_end_date']=$row[csf("cutting_end_date")];
					}
					if($row[csf("fin_Trims_rcv_start_date")]!="" && $row[csf("fin_Trims_rcv_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['fin_Trims_rcv_start_date']=$row[csf("fin_Trims_rcv_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['fin_Trims_rcv_end_date']=$row[csf("fin_Trims_rcv_end_date")];
					}
					if($row[csf("sewing_start_date")]!="" && $row[csf("sewing_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_start_date']=$row[csf("sewing_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_end_date']=$row[csf("sewing_end_date")];
					}
					if($row[csf("exfact_start_date")]!="" && $row[csf("exfact_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_start_date']=$row[csf("exfact_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_end_date']=$row[csf("exfact_end_date")];
					}
		}
				
		$tna_start_date="";
		$min_tna_date=return_field_value(" min(a.task_start_date) as min_start_date","tna_process_mst a, lib_tna_task b"," b.task_name=a.task_number and po_number_id in ($po_id) and task_name=32","min_start_date");
		$sql_order=sql_select("select order_quantity,color_number_id,size_number_id,po_break_down_id,country_id from wo_po_color_size_breakdown  where po_break_down_id in($po_id ) and status_active=1");
		$po_qty_po_level=array();
		$order_size_qty=array();
		$order_color_size_qty=array();
		$order_color_qty=array();
		$order_qty_arr=array();
		foreach($sql_order as $val){
			$order_color_qty[$val[csf('po_break_down_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]+=$val[csf('order_quantity')];
			$order_size_qty[$val[csf('po_break_down_id')]][$val[csf('country_id')]][$val[csf('size_number_id')]]+=$val[csf('order_quantity')];	
			$order_color_size_qty[$val[csf('po_break_down_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]][$val[csf('size_number_id')]]+=$val[csf('order_quantity')];
			$order_qty_arr[$val[csf('po_break_down_id')]][$val[csf('country_id')]]+=$val[csf('order_quantity')];
			$po_qty_po_level[$val[csf('po_break_down_id')]]+=$val[csf('order_quantity')];
		}
		
		//'MM-DD-YYYY'
		if($db_type==0) $select_update_date="max(date( b.update_date)) as update_date"; else $select_update_date="max(to_char( b.update_date,'DD-MM-YYYY')) as update_date";
		$nameArray=sql_select("select a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.pay_mode,a.source,a.remarks, $select_update_date  
		from wo_booking_mst a , wo_booking_dtls b  
		where a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.booking_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.pay_mode,a.source,a.remarks");
        foreach ($nameArray as $result){
			$varcode_booking_no=$result[csf('booking_no')];
        ?>
       <table width="100%" style="border:1px solid black" align="left">                    	
            <tr>
                <td colspan="6" valign="top"></td>                             
            </tr> 
            <tr>
                <td width="100" style="font-size:16px"><b>Buyer Name</b></td>
                <td width="200" >:&nbsp;<? echo   rtrim($buyer_string,", "); ?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100" style="font-size:16px"><b>Attention :</b></td>
                <td width="200"><?  echo $result[csf('attention')];?></td>
                <td width="100"><span style="font-size:16px"><b>Booking No</b></span></td>
                <td>:&nbsp;<? echo $result[csf('booking_no')];?></td>	
            </tr>
            <tr>
                <td style="font-size:16px"><b>Supplier Name </b>   </td>
                <td>:&nbsp;<? 
				if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
					echo $company_library[$result[csf('supplier_id')]];
					}
					else{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
					}
				//echo $supplier_name_arr[$result[csf('supplier_id')]];?> </td>
                <td style="font-size:16px"><b>Supplier Add. </b>   </td>
                <td>:&nbsp; <? echo $supplier_address_arr[$result[csf('supplier_id')]];?></td>
                <td><span style="font-size:16px"><b>Source</b></span></td>
                <td>:&nbsp;<? echo $source[$result[csf('source')]]; ?></td>		
            </tr>
            <tr>
                <td style="font-size:16px"><b>Pay Mode</b>   </td>
                <td>:&nbsp; <? echo $pay_mode[$result[csf('pay_mode')]]; ?></td>
                <td style="font-size:16px"><b>Currency</b></td>
                <td>:&nbsp; <? $currency_id=$result[csf('currency_id')]; echo $currency[$result[csf('currency_id')]]; ?></td>
               	<td><span style="font-size:16px"><b>Exch. Rate</b></span></td>
                <td>:&nbsp;<? echo $exchange_rate=$result[csf('exchange_rate')]; ?></td>	
            </tr>
           </table>
           
           <table width="100%" style="border:1px solid black; margin:20px 0px 20px 0px" align="left">                    	
            <tr>
                <td colspan="6" valign="top"></td>                             
            </tr> 
             <tr>
                <td width="100" style="font-size:16px"><b>Booking Date </b>   </td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp; </td>
                <td width="100" style="font-size:16px"><b>TNA Start Date</b></td>
                <td width="110">:&nbsp;<? if($min_tna_date!="") echo change_date_format($min_tna_date,'dd-mm-yyyy','-'); ?> </td>
                <?
				$wo_tna_datediff=datediff( "d", $result[csf('booking_date')], $min_tna_date);
				if($wo_tna_datediff<1) $wo_caption="After";  else $wo_caption="Before";
				?>
                <td width="100"><span style="font-size:16px"><b>WO Prepared  <? echo $wo_caption; ?></b></span></td>
                <td width="110">:&nbsp;<? echo abs($wo_tna_datediff); ?></td>	
            </tr>
            
              <tr>
                <td style="font-size:16px"><b>Delivery Date </b>   </td>
                <td>:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?> </td>
                <td style="font-size:16px"><b>Shipment Date</b></td>
                <td>:&nbsp;<? if($min_shipdate!="") echo change_date_format($min_shipdate,'dd-mm-yyyy','-'); ?>&nbsp;&nbsp;&nbsp;</td>
                <td><span style="font-size:16px"><b>Ship.days in Hand</b></span></td>
                <td>:&nbsp;<? echo datediff( "d",date("d-m-Y"),$min_shipdate);?></td>	
            </tr>
            <tr>
                <td style="font-size:16px"><b>Last Update</b> </td>
                <td style="font-size:12px">:&nbsp;<? if($result[csf('update_date')]!="" && $result[csf('update_date')]!="0000-00-00") echo change_date_format($result[csf('update_date')]);?></td>
                <td style="font-size:16px"></td>
                <td ></td>
                <td ><span style="font-size:16px"><b>Ex-factory Status</b></span></td>
                <td style="font-size:16px">:&nbsp;<? echo $shipment_status[$shipping_status];?></td>	
            </tr>
            <tr>
                <td style="font-size:16px"><b>Remarks</b> </td>
                <td style="font-size:16px">:&nbsp;<? echo $result[csf('remarks')];?></td>
                <td style="font-size:12px"></td>
                <td ></td>
                
            </tr>
        </table>  
        <br/>
		<?
        }
        ?>
        
        
        
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?
		$total_order_qty=0;
		$nameArray_job_po=sql_select( "select a.job_no,a.po_break_down_id, max(b.po_quantity) as po_quantity from wo_booking_dtls a,wo_po_break_down b  where a.booking_no=$txt_booking_no and a.booking_type=2 and a.po_break_down_id=b.id and b.status_active=1 and b.is_deleted=0  group by a.job_no,a.po_break_down_id order by a.job_no,a.po_break_down_id "); 
		
		foreach($nameArray_job_po as $nameArray_job_po_row){
		//$all_po_qty=sql_select("select sum(order_quantity) as po_total,po_break_down_id from wo_po_color_size_breakdown where po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and status_active=1 and is_deleted=0 group by po_break_down_id");
		//$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$nameArray_job_po_row[csf('job_no')]."'");
		//$style_ref=return_field_value("style_ref_no", "wo_po_details_master", "job_no='".$nameArray_job_po_row[csf('job_no')]."'");
		$total_order_qty+=$po_qty_po_level[$nameArray_job_po_row[csf('po_break_down_id')]];
		$style_ref=$job_style_arr[$nameArray_job_po_row[csf('job_no')]];
		$costing_per=$costing_per_arr[$nameArray_job_po_row[csf('job_no')]];
		if($costing_per==1){
			$costing_per_qty=12;
		}
		else if($costing_per==2){
			$costing_per_qty=1;
		}
		else if($costing_per==3){
			$costing_per_qty=24;
		}
		else if($costing_per==4){
			$costing_per_qty=36;
		}
		else if($costing_per==5){
			$costing_per_qty=48;
		}
		
		$nameArray_item=sql_select( "select  id,trim_group,country_id_string from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]."  and sensitivity=1 and status_active=1 and is_deleted=0 and wo_qnty>0 order by trim_group ");
	   if(count($nameArray_item)>0){
        ?>
        &nbsp;
        <table style="border:1px solid black" align="left" class=""  cellpadding="0" width="100%" cellspacing="0" rules="all" >
           <tr>
                <td colspan="10" align="">
                <strong><? 
				$style_sting=$style_ref;
				
				echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; Style Ref:".$style_ref." &nbsp;&nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO Qty:" .$po_qty_po_level[$nameArray_job_po_row[csf('po_break_down_id')]];?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="10" align="">
                <strong>As Per Garments Color</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Order Qty.</strong> </td>
                <td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                <td style="border:1px solid black" align="center"><strong>Cost/Dzn(USD)</strong></td>
            </tr>
            <?
			$i=0;
			$trim_amount_arr=array();
			$sql_trim_amount=sql_select("select  cons_uom,trim_group,job_no,description, cons_dzn_gmts, rate, amount from  wo_pre_cost_trim_cost_dtls where job_no='".$nameArray_job_po_row[csf('job_no')]."' and  status_active=1 and is_deleted=0 ");
			foreach($sql_trim_amount as $item){
				$trim_amount_arr[$item[csf("job_no")]][$item[csf("trim_group")]][$item[csf("description")]]['amount']=$item[csf("amount")];
			}
			
            foreach($nameArray_item as $result_item){
				$i++;
				
				$nameArray_item_description=sql_select( "select b.description, b.brand_supplier,b.item_color, b.color_number_id ,min(b.color_size_table_id) as color_size_table_id,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and  a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=1 and   a.id= ".$result_item[csf('id')]." and a.trim_group=".$result_item[csf('trim_group')]." and b.requirment !=0 and b.requirment>0 group by b.description, b.brand_supplier,b.item_color, b.color_number_id order by color_size_table_id "); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center"  style="border:1px solid black; font-weight:bold;" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? 
				$group_arr_check[$result_item[csf('trim_group')]]=$result_item[csf('trim_group')];
				echo $trim_group[$result_item[csf('trim_group')]]."<br/>";
				
				$country_arr=explode(",",$result_item[csf('country_id_string')]);
				$country_name_string="";
				for($co=0; $co <count($country_arr);$co++){
				$country_name_string.=$country_library[$country_arr[$co]].",";
				}
				echo rtrim($country_name_string,',');
				?>
                </td>
                <? 
				$item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription){
					$country_order_qty=0;
					for($coa=0; $coa <count($country_arr);$coa++){
						$country_order_qty+=$order_color_qty[$nameArray_job_po_row[csf('po_break_down_id')]][$country_arr[$coa]][$result_itemdescription[csf('color_number_id')]];
					}	
                ?>
                <td style="border:1px solid black"><?  echo $result_itemdescription[csf('description')]; ?> </td>
                <td style="border:1px solid black" align="right"><? echo $country_order_qty; ?> </td>
                <td style="border:1px solid black;">
               <? echo $color_library[$result_itemdescription[csf('color_number_id')]]; ?> 
                </td>
                <td style="border:1px solid black; text-align:right">
				<?
			    echo number_format($result_itemdescription[csf('cons')],4);
				$item_desctiption_total += $result_itemdescription[csf('cons')] ;
				?>
                </td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_arr_check[$order_uom_arr[$result_item[csf('trim_group')]]]=$order_uom_arr[$result_item[csf('trim_group')]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],6); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_as_per_gmts_color,2);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
                <td style="border:1px solid black; text-align:right">
				<? 
				$trim_amount= $trim_amount_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf("trim_group")]][$result_itemdescription[csf('description')]]['amount'];
				echo ($trim_amount*$costing_per_qty)/12; 
				?>
                </td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="3"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><? echo number_format($item_desctiption_total ,2); $grand_item_desctiption_total+=$item_desctiption_total;  ?></td>
                <td style="border:1px solid black;  text-align:right"></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right; font-weight:bold;">
                <? 
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER GMTS COLOR END=========================================  -->
        
        
        
        <!--==============================================Size Sensitive START=========================================  -->
		<?
		$nameArray_item=sql_select( "select  id,trim_group,country_id_string from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=2 and status_active=1 and is_deleted=0 and wo_qnty>0 order by trim_group "); 
		if(count($nameArray_item)>0){
        ?>
        
        <table style="border:1px solid black" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="11" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; Style Ref:&nbsp; ".$style_ref."  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO Qty:" .$po_qty_po_level[$nameArray_job_po_row[csf('po_break_down_id')]];?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="11" align="">
                <strong>Size Sensitive </strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Order Qty.</strong> </td>
                <td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
                <td align="center" style="border:1px solid black"><strong>Gmts Size</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty.</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                <td style="border:1px solid black" align="center"><strong>Cost/Dzn(USD)</strong></td>
            </tr>
            <?
			$i=0;
			$trim_amount_arr=array();
			$uom_arr_check=array();
			$sql_trim_amount=sql_select("select  cons_uom,trim_group,job_no,description, cons_dzn_gmts, rate, amount from  wo_pre_cost_trim_cost_dtls where job_no='".$nameArray_job_po_row[csf('job_no')]."' and  status_active=1 and is_deleted=0 ");
			
			foreach($sql_trim_amount as $item){
				$trim_amount_arr[$item[csf("job_no")]][$item[csf("trim_group")]][$item[csf("description")]]['amount']=$item[csf("amount")];
			}

            foreach($nameArray_item as $result_item){
			$i++;
			$nameArray_item_description=sql_select( "select b.description, b.brand_supplier,b.item_size,b.gmts_sizes,min(b.color_size_table_id) as color_size_table_id, sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id=b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=2 and  a.id= ".$result_item[csf('id')]." and a.trim_group=".$result_item[csf('trim_group')]." and b.requirment>0 group by b.description, b.brand_supplier,b.item_size, b.gmts_sizes order by color_size_table_id");
			$sql_trim_amount=sql_select("select  cons_uom, cons_dzn_gmts, rate, amount from  wo_pre_cost_trim_cost_dtls where job_no='".$nameArray_job_po_row[csf('job_no')]."'  and trim_group=".$result_item[csf('trim_group')]." ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black; font-weight:bold;" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? 
				$group_arr_check[$result_item[csf('trim_group')]]=$result_item[csf('trim_group')];
				echo $trim_group[$result_item[csf('trim_group')]]."<br/>"; 
				$country_arr=explode(",",$result_item[csf('country_id_string')]);
				$country_name_string="";
				for($co=0; $co <count($country_arr);$co++){
					$country_name_string.=$country_library[$country_arr[$co]].",";
				}
				echo rtrim($country_name_string,',');
				?>
                </td>
                <? 
                $item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
					$country_order_qty=0;
					for($coa=0; $coa <count($country_arr);$coa++){
					 $country_order_qty+=$order_size_qty[$nameArray_job_po_row[csf('po_break_down_id')]][$country_arr[$coa]][$result_itemdescription[csf('gmts_sizes')]];
					}	
                ?>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('description')]; ?> </td>
                <td style="border:1px solid black" align="right"><? echo $country_order_qty;?> </td>
                <td style="border:1px solid black; text-align:right">
              <? echo $result_itemdescription[csf('item_size')];?>
                </td>
                <td style="border:1px solid black; text-align:right">
              <? echo $size_library[$result_itemdescription[csf('gmts_sizes')]];?>
                </td>
                <td style="border:1px solid black; text-align:right">
				<? 
				 echo number_format($result_itemdescription[csf('cons')],2);
                $item_desctiption_total += $result_itemdescription[csf('cons')] ;
				?>
                </td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_arr_check[$order_uom_arr[$result_item[csf('trim_group')]]]=$order_uom_arr[$result_item[csf('trim_group')]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],6); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				echo number_format($amount_as_per_gmts_color,2);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
                <td style="border:1px solid black; text-align:right"><? $trim_amount= $trim_amount_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf("trim_group")]][$result_itemdescription[csf('description')]]['amount'];echo ($trim_amount*$costing_per_qty)/12;  ?></td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="4"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><? echo number_format($item_desctiption_total,2); $grand_item_desctiption_total+=$item_desctiption_total;  ?></td>
                 <td style="border:1px solid black;  text-align:right"></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right; font-weight:bold;">
                <? 
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
        </table>
        <br/>
        <?
		}
		?>
        
        <!--==============================================Size Sensitive END=========================================  -->
        
         <!--==============================================AS PER CONTRAST COLOR START=========================================  -->
		<?
		 $nameArray_item=sql_select( "select  id,trim_group,country_id_string from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=3 and status_active=1 and is_deleted=0 and wo_qnty>0 order by trim_group "); 
		if(count($nameArray_item)>0){
        ?>
        <table style="border:1px solid black" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
           <tr>
                <td colspan="11" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; Style Ref:&nbsp; ".$style_ref." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO Qty:" .$po_qty_po_level[$nameArray_job_po_row[csf('po_break_down_id')]];?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="11" align="">
                <strong>Contrast Color</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Order Qty.</strong> </td>
              
                <td align="center" style="border:1px solid black"><strong>Gmts Color</strong></td>
                <td align="center" style="border:1px solid black"><strong>Item Color</strong></td>
                <td style="border:1px solid black" align="center"><strong>WO Qty.</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                <td style="border:1px solid black" align="center"><strong>Cost/Dzn(USD)</strong></td>
            </tr>
            <?
			$i=0;
			$trim_amount_arr=array();$uom_arr_check=array();
			$sql_trim_amount=sql_select("select  cons_uom,trim_group,job_no,description, cons_dzn_gmts, rate, amount from  wo_pre_cost_trim_cost_dtls where job_no='".$nameArray_job_po_row[csf('job_no')]."' and  status_active=1 and is_deleted=0 ");
			
			foreach($sql_trim_amount as $item){
				$trim_amount_arr[$item[csf("job_no")]][$item[csf("trim_group")]][$item[csf("description")]]['amount']=$item[csf("amount")];
			}
            foreach($nameArray_item as $result_item){
				$i++;
				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=3 and  a.id= ".$result_item[csf('id')]." and a.trim_group=".$result_item[csf('trim_group')]." and b.requirment !=0  order by trim_group ", "item_color", "color_number_id"  );
				$nameArray_item_description=sql_select( "select b.description, b.brand_supplier,b.item_color,b.color_number_id,sum(b.requirment) as cons,min(b.color_size_table_id) as color_size_table_id,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=3 and  a.id= ".$result_item[csf('id')]." and a.trim_group=".$result_item[csf('trim_group')]." and b.requirment !=0 group by b.description, b.brand_supplier,b.item_color,b.color_number_id order by color_size_table_id "); 
				$sql_trim_amount=sql_select("select  cons_uom, cons_dzn_gmts, rate, amount from  wo_pre_cost_trim_cost_dtls where job_no='".$nameArray_job_po_row[csf('job_no')]."'  and trim_group=".$result_item[csf('trim_group')]." ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black; font-weight:bold;" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? 
				$group_arr_check[$result_item[csf('trim_group')]]=$result_item[csf('trim_group')];
				echo $trim_group[$result_item[csf('trim_group')]]."<br/>"; 
				$country_arr=explode(",",$result_item[csf('country_id_string')]);
				$country_name_string="";
				for($co=0; $co <count($country_arr);$co++){
					$country_name_string.=$country_library[$country_arr[$co]].",";
				}
				echo rtrim($country_name_string,',');
				?>
                </td>
                <? 
                $item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription){
					$country_order_qty=0;
					for($coa=0; $coa <count($country_arr);$coa++){
					 $country_order_qty+=$order_color_qty[$nameArray_job_po_row[csf('po_break_down_id')]][$country_arr[$coa]][$gmtcolor_library[$result_itemdescription[csf('item_color')]]];
					}
                ?>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('description')]; ?> </td>
                <td style="border:1px solid black" align="right"><? echo $country_order_qty; ?> </td>
                <td style="border:1px solid black">
               <? echo $color_library[$result_itemdescription[csf('color_number_id')]]; ?>
                </td>
                <td style="border:1px solid black">
               <? echo $color_library[$result_itemdescription[csf('item_color')]]; ?>
                </td>
                <td style="border:1px solid black; text-align:right">
				<? 
				echo number_format($result_itemdescription[csf('cons')],2);
                $item_desctiption_total += $result_itemdescription[csf('cons')] ;
				?>
                </td>
                 <td style="border:1px solid black; text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_arr_check[$order_uom_arr[$result_item[csf('trim_group')]]]=$order_uom_arr[$result_item[csf('trim_group')]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],6); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? 
                $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,2);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
                <td style="border:1px solid black; text-align:right"><? $trim_amount= $trim_amount_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf("trim_group")]][$result_itemdescription[csf('description')]]['amount'];echo ($trim_amount*$costing_per_qty)/12;  ?></td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="4"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><? echo number_format($item_desctiption_total,2); $grand_item_desctiption_total+=$item_desctiption_total;  ?></td>
                <td style="border:1px solid black;text-align:center"></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right; font-weight:bold;">
                <? 
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
        </table>
        <br/>
        <?
		}
		?>
        
        <!--==============================================AS PER CONTRAST COLOR END=========================================  -->
        
        <!--==============================================AS PER GMTS Color & SIZE START=========================================  -->
		<?
		
		$nameArray_item=sql_select( "select  id,trim_group,country_id_string from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=4 and status_active=1 and is_deleted=0 and wo_qnty>0 order by trim_group ");
	   if(count($nameArray_item)>0)
		{
        ?>
        
        <table style="border:1px solid black" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
             <tr>
                <td colspan="13" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; Style Ref:&nbsp; ".$style_ref." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO Qty:" .$po_qty_po_level[$nameArray_job_po_row[csf('po_break_down_id')]];?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="13" align="">
                <strong>Color & size sensitive </strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Order Qty.</strong> </td>
                
                <td style="border:1px solid black"><strong>Gmts Color</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
                <td align="center" style="border:1px solid black"><strong>Gmts Size</strong></td>
                			
                <td style="border:1px solid black" align="center"><strong>WO Qty.</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                <td style="border:1px solid black" align="center"><strong>Cost/Dzn(USD)</strong></td>
            </tr>
            <?
			$i=0;
			$trim_amount_arr=array();
			$sql_trim_amount=sql_select("select  cons_uom,trim_group,job_no,description, cons_dzn_gmts, rate, amount from  wo_pre_cost_trim_cost_dtls where job_no='".$nameArray_job_po_row[csf('job_no')]."' and  status_active=1 and is_deleted=0 ");
			
			foreach($sql_trim_amount as $item)
            {
				$trim_amount_arr[$item[csf("job_no")]][$item[csf("trim_group")]][$item[csf("description")]]['amount']=$item[csf("amount")];
			}
            foreach($nameArray_item as $result_item)
            {
			$i++;
			$nameArray_color=sql_select( "select  b.item_color,b.color_number_id,b.item_size,b.gmts_sizes,min(b.color_size_table_id) as color_size_table_id,b.description, b.brand_supplier,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id   and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and  a.id= ".$result_item[csf('id')]." and a.trim_group=".$result_item[csf('trim_group')]." and a.sensitivity=4 and b.requirment !=0 group by b.item_color,b.color_number_id,b.item_size,b.gmts_sizes,b.description, b.brand_supplier order by color_size_table_id "); 
            $nameArray_item_description=sql_select( "select distinct uom from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=4   and trim_group=".$result_item[csf('trim_group')]." order by trim_group "); 
			$sql_trim_amount=sql_select("select  cons_uom, cons_dzn_gmts, rate, amount from  wo_pre_cost_trim_cost_dtls where job_no='".$nameArray_job_po_row[csf('job_no')]."'  and trim_group=".$result_item[csf('trim_group')]." ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo   (count($nameArray_color)); ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black; font-weight:bold;" rowspan="<? echo (count($nameArray_color)); ?>">
                <? 
				$group_arr_check[$result_item[csf('trim_group')]]=$result_item[csf('trim_group')];
				echo $trim_group[$result_item[csf('trim_group')]]."<br/>"; 
				$country_arr=explode(",",$result_item[csf('country_id_string')]);
				$country_name_string="";
				for($co=0; $co <count($country_arr);$co++)
				{
				 $country_name_string.=$country_library[$country_arr[$co]].",";
				}
				echo rtrim($country_name_string,','); 
				?>
                </td>
                <? 
				$item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
				$uom_arr_check=array();
				foreach($nameArray_color as $result_color)
                {
					$country_order_qty=0;
					for($coa=0; $coa <count($country_arr);$coa++)
					{
					 $country_order_qty+=$order_color_size_qty[$nameArray_job_po_row[csf('po_break_down_id')]][$country_arr[$coa]][$result_color[csf('color_number_id')]][$result_color[csf('gmts_sizes')]];
					}
					?>
					<td style="border:1px solid black" ><? echo $result_color[csf('description')]; ?> </td>
					<td style="border:1px solid black" align="right" ><? echo $country_order_qty; ?> </td>
                    <td style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; ?> </td>
                    <td style="border:1px solid black"><? echo $color_library[$result_color[csf('item_color')]]; ?> </td>
					<td style="border:1px solid black; text-align:right">
					<? echo $result_color[csf('item_size')]; ?> 
					</td>
                    <td style="border:1px solid black; text-align:right">
					<? echo $size_library[$result_color[csf('gmts_sizes')]]; ?> 
					</td>
					<td style="border:1px solid black; text-align:right">
					<?
					echo number_format($result_color[csf('cons')],2);
					$item_desctiption_total += $result_color[csf('cons')] ;
					?>
                    </td>
					<td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_arr_check[$order_uom_arr[$result_item[csf('trim_group')]]]= $order_uom_arr[$result_item[csf('trim_group')]];?></td>
					<td style="border:1px solid black; text-align:right">
					<?
					echo number_format($result_color[csf('rate')],6);
					?>
                     </td>
					<td style="border:1px solid black; text-align:right">
					<? 
					$amount_as_per_gmts_color =$result_color[csf('amount')];
					echo number_format($amount_as_per_gmts_color,2);
					$total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
					?>
					</td>
                    <td style="border:1px solid black; text-align:right"><? $trim_amount= $trim_amount_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf("trim_group")]][$result_color[csf('description')]]['amount']; echo ($trim_amount*$costing_per_qty)/12;  ?></td>
				</tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="8"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;"><? echo number_format($item_desctiption_total,2); $grand_item_desctiption_total+=$item_desctiption_total; ?></td>
                <td style="border:1px solid black;  text-align:right"></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right; font-weight:bold;">
                <? 
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
			
            ?>
        </table>
        <br/>
       <?
		}
	   ?>
        <!--==============================================AS PER Color & SIZE  END=========================================  -->
        
        
         <!--==============================================NO NENSITIBITY START=========================================  -->
		<?
		$nameArray_item=sql_select( "select  id,trim_group,country_id_string from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=0 and status_active=1 and is_deleted=0 and wo_qnty>0 order by trim_group ");
		if(count($nameArray_item)>0)
		{
        ?>
        <table  style="border:1px solid black" align="left" class=""  cellpadding="0" width="100%" cellspacing="0" rules="all" >
        
            <tr>
                <td colspan="810" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; Style Ref:&nbsp; ".$style_ref." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO Qty:" .$po_qty_po_level[$nameArray_job_po_row[csf('po_break_down_id')]];?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="10" align="">
                <strong>No Sensitivity</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Order Qty.</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
                <td align="center" style="border:1px solid black"><strong> Qnty</strong></td>
                <td style="border:1px solid black" align="center"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center"><strong>Amount</strong></td>
                <td style="border:1px solid black" align="center"><strong>Cost/Dzn(USD)</strong></td>
            </tr>
            <?
			$i=0;
			$trim_amount_arr=array();
			$sql_trim_amount=sql_select("select  cons_uom,trim_group,job_no,description, cons_dzn_gmts, rate, amount from  wo_pre_cost_trim_cost_dtls where job_no='".$nameArray_job_po_row[csf('job_no')]."' and  status_active=1 and is_deleted=0 ");
			
			foreach($sql_trim_amount as $item)
            {
				$trim_amount_arr[$item[csf("job_no")]][$item[csf("trim_group")]][$item[csf("description")]]['amount']=$item[csf("amount")];
			}
			$grand_color_total=0;$uom_arr_check=array();
            foreach($nameArray_item as $result_item)
            {
				$i++;
				
				$nameArray_item_description=sql_select( "select  b.description, b.brand_supplier,b.item_color,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and  a.id= ".$result_item[csf('id')]." and a.trim_group=".$result_item[csf('trim_group')]." and a.sensitivity=0 group by b.description, b.brand_supplier,b.item_color"); 
				
            $sql_trim_amount=sql_select("select  cons_uom, cons_dzn_gmts, rate, amount from  wo_pre_cost_trim_cost_dtls where job_no='".$nameArray_job_po_row[csf('job_no')]."'  and trim_group=".$result_item[csf('trim_group')]." ");
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center" style="border:1px solid black; font-weight:bold;" rowspan="<? echo count($nameArray_item_description)+1; ?>">

                <?
				$group_arr_check[$result_item[csf('trim_group')]]=$result_item[csf('trim_group')];
				echo $trim_group[$result_item[csf('trim_group')]]."<br/>"; 
				$country_arr=explode(",",$result_item[csf('country_id_string')]);
				$country_name_string="";
				for($co=0; $co <count($country_arr);$co++)
				{
				 $country_name_string.=$country_library[$country_arr[$co]].",";
				}
				echo rtrim($country_name_string,',');  
				?>
                </td>
                <? 
                $color_tatal=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription)
                {
                $item_desctiption_total=0;
				$country_order_qty=0;
				for($coa=0; $coa <count($country_arr);$coa++)
				{
				 $country_order_qty+=$order_qty_arr[$nameArray_job_po_row[csf('po_break_down_id')]][$country_arr[$coa]];
				}
				
                ?>
                <td style="border:1px solid black"><? echo $result_itemdescription[csf('description')]; ?> </td>
                <td style="border:1px solid black" align="right"><? echo $country_order_qty; ?> </td>
                <td style="border:1px solid black"><? echo $color_library[$result_itemdescription[csf('item_color')]]; ?> </td>
                <?
				if($db_type==0)
				{
				$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=0 and  a.id= ".$result_item[csf('id')]." and a.trim_group=". $result_item[csf('trim_group')]." and  b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='".$result_itemdescription[csf('brand_supplier')]."' and b.item_color='".$result_itemdescription[csf('item_color')]."'");
				}
				if($db_type==2)
				{
				$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=0 and  a.id= ".$result_item[csf('id')]." and a.trim_group=". $result_item[csf('trim_group')]." and nvl( b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_color,0)=nvl('".$result_itemdescription[csf('item_color')]."',0)");
				}
                          
                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right;">
                <? 
                if($result_color_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_color_size_qnty[csf('cons')],2);
                $item_desctiption_total += $result_color_size_qnty[csf('cons')] ;
                $color_tatal+=$result_color_size_qnty[csf('cons')];
                }
                else echo "";
                ?>
                </td>
                <?   
                }
                ?>
                
                <td style="border:1px solid black; text-align:center "><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; if($order_uom_arr[$result_item[csf('trim_group')]]>0) $uom_arr_check[]=$order_uom_arr[$result_item[csf('trim_group')]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],6); ?> </td>
                <td style="border:1px solid black; text-align:right;">
                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,2);
                $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
                ?>
                </td>
                <td style="border:1px solid black; text-align:right;"><? $trim_amount= $trim_amount_arr[$nameArray_job_po_row[csf('job_no')]][$result_item[csf("trim_group")]][$result_itemdescription[csf('description')]]['amount']; echo ($trim_amount*$costing_per_qty)/12;  ?></td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="3"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold;">
                <?
                if($color_tatal !='')
                {
                echo number_format($color_tatal,2);
				$grand_item_desctiption_total+= $color_tatal; 
                }
                ?>
                </td>
                <td style="border:1px solid black;"></td>
                <td style="border:1px solid black; text-align:right"></td>
                <td style="border:1px solid black; text-align:right; font-weight:bold;">
                <? 
                echo number_format($total_amount_as_per_gmts_color,2);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>
            </tr>
            <?
            }
            ?>
        </table>
        <?
		}
		}
		?>
        <!--==============================================NO NENSITIBITY END=========================================  -->
       &nbsp;
       <?
       $mcurrency="";
	   $dcurrency="";
	   if($currency_id==1){
		$mcurrency='Taka';
		$dcurrency='Paisa'; 
	   }
	   if($currency_id==2){
		$mcurrency='USD';
		$dcurrency='CENTS'; 
	   }
	   if($currency_id==3){
		$mcurrency='EURO';
		$dcurrency='CENTS'; 
	   }
	   ?>
       <table  width="100%" class=""  style="border:1px solid black" cellpadding="0" cellspacing="0" rules="all" align="left">
            <tr style="border:1px solid black;">
                <td width="14%"  style="border:1px solid black; text-align:right; font-weight:bold; ">Total Order Qty:</td>
				<td width="17%"  style="border:1px solid black; text-align:right; font-weight:bold; "><? echo number_format($total_order_qty,2);?></td>
                <td width="22%" style="border:1px solid black;  text-align:right; font-weight:bold; "> Grand Total:</td> 
                <td width="9%" style="border:1px solid black;  text-align:right; font-weight:bold; ">
				<?
				if(count($uom_arr_check)<2 && count($group_arr_check)<2){
					echo number_format($grand_item_desctiption_total,2);
				}
				?>
                </td>
                <td style="border:1px solid black;  text-align:right; font-weight:bold; padding-right:160px;"><? echo number_format($grand_total_as_per_gmts_color,2);?></td>
            </tr>
            <tr style="border:1px solid black;">
                <td colspan="4" style="border:1px solid black; text-align:right">Total Booking Amount (in word)</td>
                <td style="border:1px solid black;"><? echo number_to_words(def_number_format($grand_total_as_per_gmts_color,2,""),$mcurrency, $dcurrency);?></td>
            </tr>
       </table>
       &nbsp;
          
         <? echo get_spacial_instruction($txt_booking_no);?>
    <br>
         <table cellspacing="0" cellpadding="0" rules="all" width="100%" class=""  align="left" style="margin-top:3px">
        	<tr>
            	<td>
				<? 
					echo signature_table(2, $cbo_company_name, "1030px"); 
				?>
                </td>
            </tr>
        </table>  
  
    <p class="gg" style="background-color:#F00; opacity: 0.09"></p>
    <?
	if(str_replace("'","",$show_comment)==1){
	?>
    <table cellspacing="0" cellpadding="0" style="border:1px solid black" rules="all" width="100%" class=""  align="left">
            <thead>
            	<tr>
                	<th colspan="9" style="font-size:18px; font-weight:bold;">Comments</th>
                </tr>
                <tr style="border:1px solid black">
                    <th width="40" style="border:1px solid black" rowspan="2">SL</th>
                    <th width="135" style="border:1px solid black" rowspan="2">Job No</th>
                    <th width="135" style="border:1px solid black" rowspan="2">PO No</th>
                    <th width="90" style="border:1px solid black" rowspan="2">Pre-Cost Value</th>
                    <th width="90" style="border:1px solid black" rowspan="2">Current Trims Value</th>
                    <th width="90" style="border:1px solid black" colspan="3">Prev Trims Value</th>
                    
                    <th width="90" style="border:1px solid black" rowspan="2">Total</th>
                    <th width="90" style="border:1px solid black" rowspan="2">Balance</th>
                    <th width="" style="border:1px solid black" rowspan="2">Comments </th>
                </tr>
                 <tr style="border:1px solid black">
                   
                    <th width="90" style="border:1px solid black">Main Trims Value</th>
                    <th width="90" style="border:1px solid black">Short Trims Value</th>
                    <th width="90" style="border:1px solid black">Sample Trims Value</th>
                    
                </tr>
            </thead>
           <tbody>
           <?
					$sql_lib_item_group_array=array();
					$main_booking_array=array();
					$main_booking_array2=array();
					$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
					foreach($sql_lib_item_group as $row_sql_lib_item_group){
						$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
						$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
						$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
					}
					
					$sql="select job_no,po_break_down_id,country_id_string from wo_booking_dtls where booking_no=$txt_booking_no and status_active=1 and is_deleted=0 group by job_no,po_break_down_id,country_id_string";
					$poWiseArr=array();
					$i=1;
					$total_amount=0;
					$pre_amount=0; 
					$pre_amount_total=0;
					$cu_woq_amount_total=0;
					$cu_woq_amount_short_total=0;
					$cu_woq_amount_sample_total=0;
					$total_value_total=0;
					$trims_balance_total=0;
                    $nameArray=sql_select( $sql );
                    foreach ($nameArray as $selectResult){
						$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$selectResult[csf('job_no')]."'");
						if($costing_per==1){
							$costing_per_qty=12;
						}
						else if($costing_per==2){
							$costing_per_qty=1;
						}
						else if($costing_per==3){
							$costing_per_qty=24;
						}
						else if($costing_per==4){
							$costing_per_qty=36;
						}
						else if($costing_per==5){
							$costing_per_qty=48;
						}
						if($selectResult[csf('country_id_string')]!="") $contry_cond=" and c.country_id in(".$selectResult[csf('country_id_string')].")"; else $contry_cond="";
						if($selectResult[csf('country_id_string')]!="") $contry_cond1=" and b.country_id_string ='".$selectResult[csf('country_id_string')]."'"; else $contry_cond1="";
						$sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='".$selectResult[csf('po_break_down_id')]."' $contry_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty");
						list($sql_po_qty_row)=$sql_po_qty;
						$po_qty=$sql_po_qty_row[csf('order_quantity_set')];
						
						$sql_cons_data=sql_select("select a.rate,b.cons from wo_pre_cost_trim_cost_dtls a , wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id  and b.po_break_down_id='".$selectResult[csf('po_break_down_id')]."' and a.is_deleted=0  and a.status_active=1");
						list($sql_cons_data_row)=$sql_cons_data;
						$pre_cons=$sql_cons_data_row[csf('cons')];
						$pre_rate=$sql_cons_data_row[csf('rate')];
						$pre_req_qnty=def_number_format(($pre_cons*($po_qty/$costing_per_qty)),2,"");
						$pre_amount=$pre_req_qnty*$pre_rate;
					
						
						$booking_no=str_replace("'","",$txt_booking_no);
						$booking_id=return_field_value("id","wo_booking_mst","booking_type=2 and booking_no='$booking_no'","id");
						$prev_book_cond="";
						if($booking_id>0) $prev_book_cond=" and a.id<$booking_id";
						
						$sql_cu_woq=sql_select("select sum(b.amount) as amount, a.exchange_rate from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.booking_no=$txt_booking_no and b.po_break_down_id='".$selectResult[csf('po_break_down_id')]."'  $contry_cond1   and  b.booking_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.exchange_rate");
						$cureent_wo_amt=0*1;
						foreach($sql_cu_woq as $row)
						{
							$cureent_wo_amt+=$row[csf("amount")]/$row[csf("exchange_rate")];
						}
						$poWiseArr[$selectResult[csf("job_no")]][$selectResult[csf("po_break_down_id")]]['pre_amount']+=$pre_amount;
						$poWiseArr[$selectResult[csf("job_no")]][$selectResult[csf("po_break_down_id")]]['cureent_wo_amt']+=$cureent_wo_amt;
						$poWiseArr[$selectResult[csf("job_no")]][$selectResult[csf("po_break_down_id")]]['job_no']=$selectResult[csf("job_no")];
						$poWiseArr[$selectResult[csf("job_no")]][$selectResult[csf("po_break_down_id")]]['po_break_down_id']=$selectResult[csf("po_break_down_id")];
					}
				//echo	$sql="select job_no,po_break_down_id from wo_booking_dtls where booking_no=$txt_booking_no and status_active=1 and is_deleted=0 group by job_no,po_break_down_id";
                       $nameArray=sql_select( $sql );
						foreach ($nameArray as $selectResult){
							//echo "select sum(b.amount) as prev_amount, a.exchange_rate from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.booking_no!=$txt_booking_no and b.po_break_down_id='".$selectResult[csf('po_break_down_id')]."'   $prev_book_cond and  b.booking_type=2 and b.is_short=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by a.exchange_rate";
						$booking_no=str_replace("'","",$txt_booking_no);
						$booking_id=return_field_value("id","wo_booking_mst","booking_type=2 and booking_no='$booking_no'","id");
						$prev_book_cond="";
						if($booking_id>0) $prev_book_cond=" and a.id<$booking_id";
						
						$prev_wo_amt=sql_select("select sum(b.amount) as prev_amount, a.exchange_rate from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.booking_no!=$txt_booking_no and b.po_break_down_id='".$selectResult[csf('po_break_down_id')]."'   $prev_book_cond and  b.booking_type=2 and b.is_short=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by a.exchange_rate");
						$prev_wo_amount=0;
						foreach($prev_wo_amt as $row)
						{
							$prev_wo_amount+=$row[csf("prev_amount")]/$row[csf("exchange_rate")];
						}
						
						$sql_cu_woq_short=sql_select("select sum(amount) as amount  from wo_booking_dtls where po_break_down_id='".$selectResult[csf('po_break_down_id')]."'   and  booking_type=2 and booking_no!=$txt_booking_no and is_short=1 and status_active=1 and is_deleted=0");
						list($sql_cu_woq_row_short)=$sql_cu_woq_short;
						$cu_woq_amount_short=$sql_cu_woq_row_short[csf('amount')];
						
						$sql_cu_woq_sample=sql_select("select sum(amount) as amount  from wo_booking_dtls where po_break_down_id='".$selectResult[csf('po_break_down_id')]."'  and  booking_type=5 and status_active=1 and is_deleted=0");
						list($sql_cu_woq_row_sample)=$sql_cu_woq_sample;
						$cu_woq_amount_sample=$sql_cu_woq_row_sample[csf('amount')];
						
						$poWiseArr[$selectResult[csf("job_no")]][$selectResult[csf("po_break_down_id")]]['prev_amount']+=$prev_wo_amount;
						$poWiseArr[$selectResult[csf("job_no")]][$selectResult[csf("po_break_down_id")]]['cu_woq_amount_short']+=$cu_woq_amount_short;
						$poWiseArr[$selectResult[csf("job_no")]][$selectResult[csf("po_break_down_id")]]['cu_woq_amount_sample']+=$cu_woq_amount_sample;
						$poWiseArr[$selectResult[csf("job_no")]][$selectResult[csf("po_break_down_id")]]['job_no']=$selectResult[csf("job_no")];
						$poWiseArr[$selectResult[csf("job_no")]][$selectResult[csf("po_break_down_id")]]['po_break_down_id']=$selectResult[csf("po_break_down_id")];


						//$total_value=$cureent_wo_amt+$prev_wo_amount+($cu_woq_amount_short/$exchange_rate)+($cu_woq_amount_sample/$exchange_rate);
						//$trims_balance=$pre_amount-$total_value;
						}
						//print_r($poWiseArr);
						foreach ($poWiseArr as $job=>$po){
							foreach ($po as $key=>$value){
								$total_value=$value['cureent_wo_amt']+$value['prev_amount']+($value['cu_woq_amount_short']/$exchange_rate)+($value['cu_woq_amount_sample']/$exchange_rate);
						        $trims_balance=$value['pre_amount']-$total_value;
						?>
						<tr style="border:1px solid black">
                            <td style="border:1px solid black"><? echo $i;?></td>
                            <td style="border:1px solid black"><? echo $value['job_no'];?></td>
                            <td style="border:1px solid black"><? echo $po_number[$value['po_break_down_id']];?>  </td>
                            <td align="right" style="border:1px solid black">
							<? 
							echo number_format($value['pre_amount'],2);
							$pre_amount_total+=$value['pre_amount']; 
							?>
                            </td>
                             <td align="right" style="border:1px solid black">
							 <?  echo number_format($value['cureent_wo_amt'],2);
							 $cur_amount_total+=$value['cureent_wo_amt']; 
							 ?>
                             </td>
                            <td align="right" style="border:1px solid black">
							<? 
							echo number_format($value['prev_amount'],2);
							$prev_woq_amount_total+=$value['prev_amount'];
							?>
                            </td>
                            <td align="right" style="border:1px solid black">
							<? 
							echo number_format($value['cu_woq_amount_short']/$exchange_rate,2);
							$cu_woq_amount_short_total+=($value['cu_woq_amount_short']/$exchange_rate);
							?>
                            </td>
                            <td align="right" style="border:1px solid black">
							<? 
							echo number_format($value['cu_woq_amount_sample']/$exchange_rate,2);
							$cu_woq_amount_sample_total+=($value['cu_woq_amount_sample']/$exchange_rate);
							?>
                            </td>
                            <td align="right" style="border:1px solid black"><? echo number_format($total_value,2);$total_value_total+=$total_value?></td>
                            <td align="right" style="border:1px solid black"><? echo number_format($trims_balance,2);$trims_balance_total+=$trims_balance;?></td>
                            <td align="right" style="border:1px solid black">
                            <?
                            if($value['pre_amount']==($total_value)){
                            	echo "At Per";
                            }
                            if($value['pre_amount']>($total_value)){
                            	echo "Less Booking";
                            }
                            if($value['pre_amount']<($total_value)){
                            	echo "Over Booking";
                            }
                            ?>
                            </td>
						</tr>
						<?
                        $i++;
                    }
						}
					?>
		</tbody>
        <tfoot>
         <tr align="right">
                    <th  colspan="3"width="135" style="border:1px solid black">Total:</th>
                    <th width="90" style="border:1px solid black"><? echo number_format($pre_amount_total,2); ?></th>
                    <th width="90" style="border:1px solid black"><? echo number_format($cur_amount_total,2); ?></th>
                    <th width="90" style="border:1px solid black"><? echo number_format($prev_woq_amount_total,2); ?></th>
                    <th width="90" style="border:1px solid black"><? echo number_format($cu_woq_amount_short_total,2); ?></th>
                    <th width="90" style="border:1px solid black"><? echo number_format($cu_woq_amount_sample_total,2); ?></th>
                    <th width="90" style="border:1px solid black"><? echo number_format($total_value_total,2); ?></th>
                    <th width="90" style="border:1px solid black"><? echo number_format($trims_balance_total,2); ?></th>
                    <th width="" style="border:1px solid black"></th>
         </tr>
         </tfoot>
    </table>
    <?
	}
	?>
           <br>
        <!--<div style="max-width:1000;">-->
      	<style>
			.rpt_ntable{
				border:1px solid black;
			}
			.rpt_ntable tr{
				border:1px solid black;
			}
			.rpt_ntable tr td{
				border:1px solid black;
			}
		</style>
        <table width="100%"  class="rpt_ntable"  cellpadding="0" cellspacing="0" rules="all"  align="left"> 
          <tr><td colspan="14" align="center"><b>TNA Information</b></td></tr>                   	
            <tr>
            	<td rowspan="2" align="center" valign="top">SL</td>
            	<td width="180" rowspan="2"  align="center" valign="top"><b>Order No</b></td>
                <td colspan="2" align="center" valign="top"><b>Trims Book[SnF]</b></td>
                <td colspan="2" align="center" valign="top"><b>Sew Trims Rcv.</b></td>
                <td colspan="2" align="center" valign="top"><b>Cutting</b></td>
                <td colspan="2" align="center" valign="top"><b>Fin Trims Rcv. </b></td>
                <td colspan="2" align="center" valign="top"><b>Sewing </b></td>
                <td colspan="2"  align="center" valign="top"><b>Ex-factory </b></td>
            </tr> 
            <tr>
            	<td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                
            </tr>
            <?
			$i=1;
			foreach($tna_date_task_arr as $order_id=>$row){
				?>
                <tr>
                	<td><? echo $i; ?></td>
                    <td><? echo $po_num_arr[$order_id]; ?></td>
                	<td align="center"><? echo change_date_format($row['trims_booking_start_date']); ?></td>
                    <td  align="center"><? echo change_date_format($row['trims_booking_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['Sew_Trims_rcv_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['Sew_Trims_rcv_end_date']); ?></td>                    
                    <td align="center"><? echo change_date_format($row['cutting_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['cutting_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['fin_Trims_rcv_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['fin_Trims_rcv_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['sewing_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['sewing_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['exfact_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['exfact_end_date']); ?></td>
                </tr>
                <?
				$i++;
			}
			?> 
            
            <tr>
            
            </tr>
           
        </table>
        
        <br>
        
        <table cellspacing="0" cellpadding="0" rules="all" width="100%" class=""  align="left" style="margin-top:3px">
        	<tr>
            	<td>
				<? 
					//echo signature_table(2, $cbo_company_name, "1030px"); 
					echo "****".custom_file_name($txt_booking_no,$style_sting,$job_no);
				?>
                </td>
            </tr>
        </table>
        <!--</div>       --> 
   
    </div>
	<?	
	if($link==1){
		?>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
		<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
		<? 
	}
	else{
		?>
		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		<? 
	}
	?>
	<script>
	fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
	</script>
	<?
   exit(); 
}


if($action=="save_update_delete_fabric_booking_terms_condition")
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
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con);die;}		
		 $id=return_next_id( "id", "wo_booking_terms_condition", 1 ) ;
		 $field_array="id,booking_no,terms";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $termscondition="termscondition_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_booking_no.",".$$termscondition.")";
			$id=$id+1;
		 }
		// echo  $data_array;
		$rID_de3=execute_query( "delete from wo_booking_terms_condition where  booking_no =".$txt_booking_no."",0);

		 $rID=sql_insert("wo_booking_terms_condition",$field_array,$data_array,1);
		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_booking_no[0];
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);  
				echo "0**".$new_booking_no[0];
			}
			else{
				oci_rollback($con);  
				echo "10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}	
}

if ($action=="populate_data_from_search_popup")
{
	 $sql= "select booking_no,booking_date,company_id,ready_to_approved,buyer_id,job_no,currency_id,exchange_rate,pay_mode,supplier_id,attention,delivery_date,source,is_approved,is_short,trime_type,remarks,item_from_precost from wo_booking_mst  where booking_no='$data'";
	 //echo $sql;die;
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
		//$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$row[csf("company_id")]."'   and module_id=2 and report_id=5 and is_deleted=0 and status_active=1");
		//$buyer_dropdown=load_drop_down_buyer($row[csf("company_id")]);
		
		//echo "document.getElementById('buyer_td').innerHTML = '".$buyer_dropdown."';\n";
		//echo "get_php_form_data(".$row[csf("company_id")].", 'populate_variable_setting_data', 'requires/trims_booking_controller' );\n";
		populate_variable_setting_data($row[csf("company_id")]);
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n"; 
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n"; 
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";  
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "load_drop_down( 'requires/trims_booking_controller', '".$row[csf("pay_mode")]."', 'load_drop_down_supplier', 'supplier_td' );\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
	    echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
		echo "document.getElementById('id_approved_id').value = '".$row[csf("is_approved")]."';\n";
		echo "document.getElementById('cbo_isshort').value = '".$row[csf("is_short")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('cbo_trim_type').value = ".$row[csf("trime_type")].";\n";
		echo "document.getElementById('cbo_item_from_precost').value = ".$row[csf("item_from_precost")].";\n";
		echo " $('#cbo_company_name').attr('disabled',true);\n"; 
		echo " $('#cbo_buyer_name').attr('disabled',true);\n"; 
		echo " $('#cbo_supplier_name').attr('disabled',true);\n";
		echo " $('#cbo_pay_mode').attr('disabled',true);\n";
		echo " $('#cbo_item_from_precost').attr('disabled',true);\n";
		if($row[csf("is_approved")]==1)
		{
			echo "document.getElementById('app_sms').innerHTML = 'This booking is approved';\n";
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is approved';\n";
		}
		else
		{
			echo "document.getElementById('app_sms').innerHTML = '';\n";
			echo "document.getElementById('app_sms2').innerHTML = '';\n";
		}
	 }
}
?>
<? 
/*-------------------------------------------- Comments
Version (MySql)          :  V2
Version (Oracle)         :  V1
Converted by             :  MONZU
Purpose			         :  This form will create Trims Booking
Functionality	         :	
JS Functions	         :
Created by		         :  MONZU 
Creation date 	         :  27-12-2012
Requirment Client        :  Fakir Apperels
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
$permission=$_SESSION['page_permission'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

//---------------------------------------------------- Start---------------------------------------------------------------------------
$po_number=return_library_array("select id, po_number from wo_po_break_down", "id", "po_number");
$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
$size_library=return_library_array("select id, size_name from lib_size", "id", "size_name");
$company_library=return_library_array("select id, company_name from lib_company", "id", "company_name");
$buyer_arr=return_library_array("select id, short_name from lib_buyer",'id','short_name');
$trim_group= return_library_array("select id, item_name from lib_item_group",'id','item_name');
$supplier_library= return_library_array("select id, supplier_name from lib_supplier",'id','supplier_name');

function load_drop_down_supplier($data){
	if($data==5 || $data==3){
	   echo create_drop_down( "cbo_supplier_name", 172, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Company --", "", "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/trims_booking_controller_v2');",0,"" );
	}
	else
	{
		$cbo_supplier_name= create_drop_down( "cbo_supplier_name", 172, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and b.party_type=4 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value, 'load_drop_down_attention', 'requires/trims_booking_controller_v2');","");
	}
	
	return $cbo_supplier_name;
	exit();
}

if ($action=="load_drop_down_buyer")
{
	$ex_data=explode('__',$data);
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$ex_data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $ex_data[1], "","" );
	exit();
} 
if ($action=="load_drop_down_supplier")
{
	echo $action($data);
	//echo create_drop_down( "cbo_supplier_name", 172, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type=4 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value, 'load_drop_down_attention', 'requires/trims_booking_controller');","");
	exit();
}
if ($action=="populate_variable_setting_data")
{
	$data_array=sql_select("select exeed_budge_qty,exeed_budge_amount,amount_exceed_level from variable_order_tracking where company_name='$data' and item_category_id=4 and variable_list=26 and status_active=1 and is_deleted=0");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('exeed_budge_qty').value = '".$row[csf("exeed_budge_qty")]."';\n";  
		echo "document.getElementById('exeed_budge_amount').value = '".$row[csf("exeed_budge_amount")]."';\n"; 
		echo "document.getElementById('amount_exceed_level').value = '".$row[csf("amount_exceed_level")]."';\n"; 
	}
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
                     
                        <th width="100">Job No</th>
                        <th width="80">File no</th>
                        <th width="80">Ref. No</th>
                        
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
                        <input type="hidden" id="garments_nature" value="<? echo $garments_nature; ?>">
							<? 
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'trims_booking_controller_v2', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
                    </td>
                   	<td id="buyer_td">
                     <? 
						echo create_drop_down( "cbo_buyer_name", 172, $blank_array,'', 1, "-- Select Buyer --" );
					?>	</td>
                     <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:100px"></td>
                     <td><input name="txt_file" id="txt_file" class="text_boxes" style="width:80px"></td>
                     <td><input name="txt_ref" id="txt_ref" class="text_boxes" style="width:80px"></td>
                    
                      <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px"></td>
                     <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:150px"></td>
                     <td>
                     <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					 <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('garments_nature').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_file').value+'_'+document.getElementById('txt_ref').value, 'create_po_search_list_view', 'search_div', 'trims_booking_controller_v2', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                     </td>
        		</tr>
             </table>
          </td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle">
            <? 
			echo create_drop_down("cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
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
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	
	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[7]";
	if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";
	
	//if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num='$data[6]'  $year_cond"; else  $job_cond=""; 
	//if (str_replace("'","",$data[8])!="") $order_cond=" and b.po_number like '%$data[8]%'  "; else  $order_cond=""; 
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
	 	$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.grouping,b.file_no,b.po_quantity,b.shipment_date,a.garments_nature,SUBSTRING_INDEX(a.insert_date, '-', 1) as year,c.approved from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.approved=1 and a.garments_nature=$data[5] and a.status_active=1 ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." and b.status_active=1 $shipment_date $company $buyer $job_cond $style_cond $ref_cond $file_cond $order_cond order by a.job_no";
		}
		if($db_type==2)
		{
	 	 $sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.grouping,b.file_no,b.po_quantity,b.shipment_date,a.garments_nature,to_char(a.insert_date,'YYYY') as year,c.approved from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.approved=1 and a.garments_nature=$data[5] and a.status_active=1".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." and b.status_active=1 $shipment_date $company $buyer $job_cond $style_cond $ref_cond $file_cond $order_cond order by a.job_no";
		}
		 echo  create_list_view("list_view", "Job No,File No,Ref. No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date,Gmts Nature,Approved", "50,80,80,60,120,100,100,90,90,90,80,80,50","1160","320",0, $sql , "js_set_value", "job_no", "", 1, "0,0,0,0,company_name,buyer_name,0,0,0,0,0,garments_nature,approved", $arr , "job_no_prefix_num,file_no,grouping,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date,garments_nature,approved", "",'','0,0,0,0,0,0,0,1,0,1,3,0,0');
	}
	else
	{
		$arr=array (2=>$comp,3=>$buyer_arr,5=>$item_category);
		if($db_type==0)
		{
		$sql= "select a.job_no_prefix_num,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.garments_nature,SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a where a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.garments_nature=$data[5] and a.status_active=1".set_user_lavel_filtering(' and a.buyer_name','buyer_id')."   and a.is_deleted=0 $company $buyer order by a.job_no";
		}
		if($db_type==2)
		{
		$sql= "select a.job_no_prefix_num,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.garments_nature,to_char(a.insert_date,'YYYY') as year from wo_po_details_master a where a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.garments_nature=$data[5] and a.status_active=1".set_user_lavel_filtering(' and a.buyer_name','buyer_id')."   and a.is_deleted=0 $company $buyer order by a.job_no";
		}
		echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Gmts Nature", "90,80,120,100,100,90","1000","320",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_name,buyer_name,0,garments_nature", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,garments_nature", "",'','0,0,0,0,0,0');
	}
} 


if ($action=="populate_data_from_search_popup")
{
	$data_array=sql_select("select job_no,company_name,buyer_name,currency_id from wo_po_details_master where job_no='$data'");
	foreach ($data_array as $row)
	{
		$data_req=total_cu_data($row[csf("job_no")]);
		$data_req_arr=explode("_",$data_req);
		echo "document.getElementById('txt_tot_req_amount').value = '".$data_req_arr[0]."';\n";
		echo "document.getElementById('txt_tot_cu_amount').value = '".$data_req_arr[1]."';\n";
		
		echo "load_drop_down( 'requires/trims_booking_controller_v2', '".$row[csf("company_name")].'__'.$row[csf("buyer_name")]."', 'load_drop_down_buyer', 'buyer_td' );\n";
		echo "load_drop_down( 'requires/trims_booking_controller_v2', '".$row[csf("company_name")]."', 'load_drop_down_supplier', 'supplier_td' );\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";  
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n"; 
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";
		echo "document.getElementById('cbo_currency_job').value = '".$row[csf("currency_id")]."';\n";
		echo " $('#cbo_company_name').attr('disabled',true);\n"; 
		echo " $('#cbo_buyer_name').attr('disabled',true);\n"; 
		
		
		echo "get_php_form_data(".$row[csf("company_name")].", 'populate_variable_setting_data', 'requires/trims_booking_controller_v2' );\n";
		
	}
	/*$sql_delevary=sql_select("select task_number,max(task_finish_date) as task_finish_date from tna_process_mst where job_no ='$data' and task_number in(71) and is_deleted = 0 and 	status_active=1 group by task_number");
		foreach($sql_delevary as $row_delevary)
		{
		   //echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row_delevary[csf("task_finish_date")],'dd-mm-yyyy','-')."';\n";  	
		}*/
}

function total_cu_data($job_no)
{
		$sql_lib_item_group_array=array();
		$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
		foreach($sql_lib_item_group as $row_sql_lib_item_group)
		{
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
		}
		
		$sql="select
		a.job_no_prefix_num,
		a.job_no,
		a.company_name,
		a.buyer_name,
		a.style_ref_no,
		b.costing_per,
		c.id as wo_pre_cost_trim_cost_dtls,
		c.trim_group,
		c.description,
		c.brand_sup_ref,
		c.rate,
		d.id as po_id,
		d.po_number,
		d.po_quantity as plan_cut,
		e.id as id,
		e.po_break_down_id,
		e.cons,
		sum(f.wo_qnty) as cu_woq,
		sum(f.amount) as cu_amount
		from 
		wo_po_details_master a,
		wo_pre_cost_mst b,
		wo_pre_cost_trim_cost_dtls c,
		wo_po_break_down d,
		wo_pre_cost_trim_co_cons_dtls e  
		left join wo_booking_dtls f
		on 
		f.job_no=e.job_no and 
		f.pre_cost_fabric_cost_dtls_id=e.wo_pre_cost_trim_cost_dtls_id and 
		f.po_break_down_id=e.po_break_down_id and 
		f.booking_type=2
		where
		a.job_no=b.job_no and 
		a.job_no=c.job_no and 
		a.job_no=d.job_no_mst and 
		a.job_no=e.job_no and
		c.id=e.wo_pre_cost_trim_cost_dtls_id and
		d.id=e.po_break_down_id and 
		a.job_no='$job_no'  and
		d.is_deleted=0 and 
		d.status_active=1
		group by e.id,
		a.job_no_prefix_num,
		a.job_no,
		a.company_name,
		a.buyer_name,
		a.style_ref_no,
		b.costing_per,
		c.id,
		c.trim_group,
		c.description,
		c.brand_sup_ref,
		c.rate,
		d.id,
		d.po_number,
		d.po_quantity,
		e.po_break_down_id,
		e.cons
		order by d.id,c.id";
		$i=1;
		$total_req=0;
		$total_amount=0;
		$nameArray=sql_select( $sql );
		
		foreach ($nameArray as $selectResult)
		{
		if($selectResult[csf('costing_per')]==1)
		{
		$costing_per_qty=12;
		}
		else if($selectResult[csf('costing_per')]==2)
		{
		$costing_per_qty=1;
		}
		else if($selectResult[csf('costing_per')]==3)
		{
		$costing_per_qty=24;
		}
		else if($selectResult[csf('costing_per')]==4)
		{
		$costing_per_qty=36;
		}
		else if($selectResult[csf('costing_per')]==5)
		{
		$costing_per_qty=48;
		}
		$req_qnty=def_number_format(($selectResult[csf('cons')]*($selectResult[csf('plan_cut')]/12))/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],5,"");
		//$bal_woq=def_number_format($req_qnty-$selectResult[csf('cu_woq')],5,"");
		$rate=def_number_format(($selectResult[csf('rate')]*$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor]),5,"");
		
		$req_amount=def_number_format($req_qnty*$rate,5,"");
		$total_req_amount+=$req_amount;
		$total_cu_amount+=$selectResult[csf('cu_amount')];
		}
		
		$data_req=$total_req_amount."_".$total_cu_amount;
		return $data_req;
	
}
if ($action=="fnc_process_data")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
?>
	<script>
	
	    var selected_id = new Array(); 
	    var selected_name = new Array();	
		
		//var first_item_group=0;
	 
	    function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			if($("#search"+str).css("display") !='none'){
				toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
				if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
					selected_id.push( $('#txt_individual_id' + str).val() );
				}
				else{
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
					}
					selected_id.splice( i, 1 );
				}
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			$('#txt_selected_id').val( id );
		}
		
		
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<? 
extract($_REQUEST);
//++++++++++++DO NOT DELETE THIS PART++++++++++++++++++++
/*$view_sql="CREATE or REPLACE  VIEW wo_trim_booking_data_park AS select  d.id as id,a.job_no,a.company_name,b.pub_shipment_date,c.id as wo_pre_cost_trim_cost_dtls,d.cons,e.costing_per,a.buyer_name,a.style_ref_no,b.id as po_id,b.po_number,c.trim_group,c.description,c.brand_sup_ref,
CASE e.costing_per WHEN 1 THEN round((d.cons/12)*b.po_quantity,4) WHEN 2 THEN round((d.cons/1)*b.po_quantity,4)  WHEN 3 THEN round((d.cons/24)*b.po_quantity,4) WHEN 4 THEN round((d.cons/36)*b.po_quantity,4) WHEN 5 THEN round((d.cons/48)*b.po_quantity,4) ELSE 0 END as req_qnty,c.cons_uom,IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0)  as cu_woq, CASE e.costing_per WHEN 1 THEN round(((d.cons/12)*b.po_quantity)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 2 THEN round(((d.cons/2)*b.po_quantity) - IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4)  WHEN 3 THEN round(((d.cons/24)*b.po_quantity)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 4 THEN round(((d.cons/36)*b.po_quantity)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 5 THEN round(((d.cons/48)*b.po_quantity)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) ELSE 0 END as bal_woq,c.rate from wo_po_details_master a, wo_po_break_down b ,wo_pre_cost_mst e,wo_pre_cost_trim_cost_dtls c , wo_pre_cost_trim_co_cons_dtls d left join wo_booking_dtls f on f.job_no=d.job_no and f.pre_cost_fabric_cost_dtls_id=d.wo_pre_cost_trim_cost_dtls_id and f.po_break_down_id=d.po_break_down_id  where a.job_no=b.job_no_mst and  a.job_no=c.job_no and a.job_no=e.job_no and  a.job_no=d.job_no   and c.id=d.wo_pre_cost_trim_cost_dtls_id and b.id=d.po_break_down_id group by d.id order by b.id
";
$rID_up=execute_query($view_sql);*/
///new view======================================
/*$view_sql="CREATE or REPLACE  VIEW wo_trim_booking_data_park AS select  d.id as id,a.job_no,a.company_name,b.pub_shipment_date,b.grouping,c.id as wo_pre_cost_trim_cost_dtls,d.cons,e.costing_per,a.buyer_name,a.style_ref_no,b.id as po_id,b.po_number,c.trim_group,c.description,c.brand_sup_ref,
CASE e.costing_per WHEN 1 THEN round(((d.cons/12)*b.po_quantity)/cc.conversion_factor,4) WHEN 2 THEN round(((d.cons/1)*b.po_quantity)/cc.conversion_factor,4)  WHEN 3 THEN round(((d.cons/24)*b.po_quantity)/cc.conversion_factor,4) WHEN 4 THEN round(((d.cons/36)*b.po_quantity)/cc.conversion_factor,4) WHEN 5 THEN round(((d.cons/48)*b.po_quantity)/cc.conversion_factor,4) ELSE 0 END as req_qnty,cc.order_uom as cons_uom,IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0)  as cu_woq, CASE e.costing_per WHEN 1 THEN round((((d.cons/12)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 2 THEN round((((d.cons/1)*b.po_quantity)/cc.conversion_factor) - IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4)  WHEN 3 THEN round((((d.cons/24)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 4 THEN round((((d.cons/36)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 5 THEN round((((d.cons/48)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) ELSE 0 END as bal_woq, round((c.rate*cc.conversion_factor),8) as rate from wo_po_details_master a, wo_po_break_down b ,wo_pre_cost_mst e,wo_pre_cost_trim_cost_dtls c ,lib_item_group cc, wo_pre_cost_trim_co_cons_dtls d left join wo_booking_dtls f on f.job_no=d.job_no and f.pre_cost_fabric_cost_dtls_id=d.wo_pre_cost_trim_cost_dtls_id and f.po_break_down_id=d.po_break_down_id and f.booking_type=2   where a.job_no=b.job_no_mst and  a.job_no=c.job_no and a.job_no=e.job_no and  a.job_no=d.job_no   and c.id=d.wo_pre_cost_trim_cost_dtls_id and b.id=d.po_break_down_id and cc.id=c.trim_group group by d.id order by b.id";
$rID_up=execute_query($view_sql);*/
//if($data[4]==2) $cbo_order_status="%%"; else $cbo_order_status= "$data[4]";

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

	 $booking_month=0;
	 if($cbo_booking_month<10)
	 {
		 $booking_month.=$cbo_booking_month;
	 }
	 else
	 {
		$booking_month=$cbo_booking_month; 
	 }
	 
	$start_date=$cbo_booking_year."-".$booking_month."-01";
	$end_date=$cbo_booking_year."-".$booking_month."-".cal_days_in_month(CAL_GREGORIAN, $booking_month, $cbo_booking_year);
?>
<input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
<input type="hidden" name="itemGroup" id="itemGroup" value="" />

        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1530" class="rpt_table" >
                <thead>
                    <th width="40">SL</th>
                    <th width="50">Buyer</th>
                    <th width="100">Job No</th>
                    <th width="80">File No</th>
                    <th width="80">Ref. No</th>
                    <th width="80">Style No</th>
                    <th width="100">Ord. No</th>
                    
                    <th width="100">Trims Group</th>
                    <th width="130">Description</th>
                    <th width="100">Brand/Supp.Ref</th>
                    <th width="100">Nominated Supp</th>
                    <th width="80">Req. Qnty</th>
                    <th width="50">UOM</th>
                    <th width="80">CU WOQ</th>
                    <th width="80">Bal WOQ</th>
                    <th width="80">Exch.Rate</th>
                    <th width="80">Rate</th>
                    <th width="">Amount</th>
                </thead>
            </table>
            <div style="width:1530px; overflow-y:scroll; max-height:350px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1510" class="rpt_table" id="tbl_list_search" >
            <?
			/* $sql="select id,buyer_name,job_no_prefix_num,job_no,style_ref_no,po_number,grouping,trim_group,description,brand_sup_ref,req_qnty,cons_uom,cu_woq,bal_woq,rate from (select  d.id as id,a.job_no_prefix_num,a.job_no,a.company_name,b.pub_shipment_date,b.grouping,c.id as wo_pre_cost_trim_cost_dtls,d.cons,e.costing_per,a.buyer_name,a.style_ref_no,b.id as po_id,b.po_number,c.trim_group,c.description,c.brand_sup_ref,
CASE e.costing_per WHEN 1 THEN round(((d.cons/12)*b.po_quantity)/cc.conversion_factor,4) WHEN 2 THEN round(((d.cons/1)*b.po_quantity)/cc.conversion_factor,4)  WHEN 3 THEN round(((d.cons/24)*b.po_quantity)/cc.conversion_factor,4) WHEN 4 THEN round(((d.cons/36)*b.po_quantity)/cc.conversion_factor,4) WHEN 5 THEN round(((d.cons/48)*b.po_quantity)/cc.conversion_factor,4) ELSE 0 END as req_qnty,cc.order_uom as cons_uom,IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0)  as cu_woq, CASE e.costing_per WHEN 1 THEN round((((d.cons/12)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 2 THEN round((((d.cons/1)*b.po_quantity)/cc.conversion_factor) - IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4)  WHEN 3 THEN round((((d.cons/24)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 4 THEN round((((d.cons/36)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 5 THEN round((((d.cons/48)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) ELSE 0 END as bal_woq, round((c.rate*cc.conversion_factor),8) as rate from wo_po_details_master a, wo_po_break_down b ,wo_pre_cost_mst e,wo_pre_cost_trim_cost_dtls c ,lib_item_group cc, wo_pre_cost_trim_co_cons_dtls d left join wo_booking_dtls f on f.job_no=d.job_no and f.pre_cost_fabric_cost_dtls_id=d.wo_pre_cost_trim_cost_dtls_id and f.po_break_down_id=d.po_break_down_id and f.booking_type=2   where a.job_no=b.job_no_mst and  a.job_no=c.job_no and a.job_no=e.job_no and  a.job_no=d.job_no   and c.id=d.wo_pre_cost_trim_cost_dtls_id and b.id=d.po_break_down_id and cc.id=c.trim_group and b.pub_shipment_date between '$start_date' and '$end_date'  and a.company_name=$company_id group by d.id order by b.id) m  where pub_shipment_date between '$start_date' and '$end_date'  and company_name=$company_id and bal_woq >0 order by trim_group";*/
			  /*$sql="select id,buyer_name,job_no_prefix_num,job_no,style_ref_no,po_number,grouping,trim_group,description,brand_sup_ref,req_qnty,cons_uom,cu_woq,bal_woq,rate from (select  d.id as id,a.job_no_prefix_num,a.job_no,a.company_name,a.garments_nature,b.pub_shipment_date,b.grouping,c.id as wo_pre_cost_trim_cost_dtls,d.cons,e.costing_per,a.buyer_name,a.style_ref_no,b.id as po_id,b.po_number,c.trim_group,c.description,c.brand_sup_ref,
CASE e.costing_per WHEN 1 THEN round(((d.cons/12)*b.po_quantity)/cc.conversion_factor,4) WHEN 2 THEN round(((d.cons/1)*b.po_quantity)/cc.conversion_factor,4)  WHEN 3 THEN round(((d.cons/24)*b.po_quantity)/cc.conversion_factor,4) WHEN 4 THEN round(((d.cons/36)*b.po_quantity)/cc.conversion_factor,4) WHEN 5 THEN round(((d.cons/48)*b.po_quantity)/cc.conversion_factor,4) ELSE 0 END as req_qnty,cc.order_uom as cons_uom,IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0)  as cu_woq, CASE e.costing_per WHEN 1 THEN round((((d.cons/12)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 2 THEN round((((d.cons/1)*b.po_quantity)/cc.conversion_factor) - IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4)  WHEN 3 THEN round((((d.cons/24)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 4 THEN round((((d.cons/36)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 5 THEN round((((d.cons/48)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) ELSE 0 END as bal_woq, round((c.rate*cc.conversion_factor),8) as rate from wo_po_details_master a, wo_po_break_down b ,wo_pre_cost_mst e,wo_pre_cost_trim_cost_dtls c ,lib_item_group cc, wo_pre_cost_trim_co_cons_dtls d left join wo_booking_dtls f on f.job_no=d.job_no and f.pre_cost_fabric_cost_dtls_id=d.wo_pre_cost_trim_cost_dtls_id and f.po_break_down_id=d.po_break_down_id and f.booking_type=2   where a.job_no=b.job_no_mst and  a.job_no=c.job_no and a.job_no=e.job_no and  a.job_no=d.job_no   and c.id=d.wo_pre_cost_trim_cost_dtls_id and b.id=d.po_break_down_id and cc.id=c.trim_group   and a.company_name=$company_id $txt_job_no_cond $cbo_buyer_name_cond and a.garments_nature=$garments_nature  group by d.id order by b.id) m  where  company_name=$company_id $txt_job_no_cond1 $cbo_buyer_name_cond1  and garments_nature=$garments_nature   and bal_woq >0 order by trim_group";//and b.pub_shipment_date between '$start_date' and '$end_date'*/
					$sql_lib_item_group_array=array();
					$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
					foreach($sql_lib_item_group as $row_sql_lib_item_group)
					{
					$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
					$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
					$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
					}
					 $sql="select
					a.job_no_prefix_num,
					a.job_no,
					a.company_name,
					a.buyer_name,
					a.style_ref_no,
					b.costing_per,
					b.exchange_rate,
					c.id as wo_pre_cost_trim_cost_dtls,
					c.trim_group,
					c.description,
					c.brand_sup_ref,
					c.nominated_supp,
					c.rate,
					d.id as po_id,
					d.po_number,
					d.file_no,
					d.grouping,
					d.po_quantity as plan_cut,
					e.id as id,
					e.po_break_down_id,
					e.cons,
					sum(f.wo_qnty) as cu_woq,
					sum(f.amount) as cu_amount
					from 
					wo_po_details_master a,
					wo_pre_cost_mst b,
					wo_pre_cost_trim_cost_dtls c,
					wo_po_break_down d,
					wo_pre_cost_trim_co_cons_dtls e  
					left join wo_booking_dtls f
					on 
					f.job_no=e.job_no and 
					f.pre_cost_fabric_cost_dtls_id=e.wo_pre_cost_trim_cost_dtls_id and 
					f.po_break_down_id=e.po_break_down_id and 
					f.booking_type=2
					where
					a.job_no=b.job_no and 
					a.job_no=c.job_no and 
					a.job_no=d.job_no_mst and 
					a.job_no=e.job_no and
					c.id=e.wo_pre_cost_trim_cost_dtls_id and
					d.id=e.po_break_down_id and 
					a.company_name=$company_id $txt_job_no_cond $cbo_buyer_name_cond and
					(c.nominated_supp = $cbo_supplier_name or c.nominated_supp= 0) and
					a.garments_nature=$garments_nature and 
					d.is_deleted=0 and 
					d.status_active=1
					group by e.id,
					a.job_no_prefix_num,
					a.job_no,
					a.company_name,
					a.buyer_name,
					a.style_ref_no,
					b.costing_per,
					b.exchange_rate,
					c.id,
					c.trim_group,
					c.description,
					c.brand_sup_ref,
					c.nominated_supp,
					c.rate,
					d.id,
					d.po_number,
					d.file_no,
					d.grouping,
					d.po_quantity,
					e.po_break_down_id,
					e.cons
					order by d.id,c.id";
					$i=1;
					$total_req=0;
					$total_amount=0;
                    $nameArray=sql_select( $sql );
                    foreach ($nameArray as $selectResult)
                    {
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
							
						if($selectResult[csf('costing_per')]==1)
						{
							$costing_per_qty=12;
						}
						else if($selectResult[csf('costing_per')]==2)
						{
							$costing_per_qty=1;
						}
						else if($selectResult[csf('costing_per')]==3)
						{
							$costing_per_qty=24;
						}
						else if($selectResult[csf('costing_per')]==4)
						{
							$costing_per_qty=36;
						}
						else if($selectResult[csf('costing_per')]==5)
						{
							$costing_per_qty=48;
						}
						
						$exchange_rate=$selectResult[csf('exchange_rate')];
						
						if($cbo_currency==$cbo_currency_job)
						{
						 $exchange_rate=1;	
						}
						
						$req_qnty=def_number_format(($selectResult[csf('cons')]*($selectResult[csf('plan_cut')]/12))/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],5,"");
						$bal_woq=def_number_format($req_qnty-$selectResult[csf('cu_woq')],5,"");
						$rate=def_number_format(($selectResult[csf('rate')]*$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor])*$exchange_rate,5,"");
						
						$req_amount=def_number_format($req_qnty*$rate,5,"");
					    $total_req_amount+=$req_amount;
						$total_cu_amount+=$selectResult[csf('cu_amount')];
						if($bal_woq>0)
						{
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
                    <td width="40"><? echo $i;?>
                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>	
                    <input type="hidden" name="txt_trim_group_id" id="txt_trim_group_id<?php echo $i ?>" value="<? echo $selectResult[csf('trim_group')]; ?>"/>	
                    </td>
                    <td width="50"><? echo $buyer_arr [$selectResult[csf('buyer_name')]];?></td>
                    <td width="100"><? echo $selectResult[csf('job_no_prefix_num')];?></td>
                    <td width="80"><? echo $selectResult[csf('file_no')];?></td>
                    <td width="80"><p><? echo $selectResult[csf('grouping')];?></p></td>
                    <td width="80"><p><? echo $selectResult[csf('style_ref_no')];?></p></td>
                    <td width="100"><? echo $selectResult[csf('po_number')];?></td>
                   
                    <td width="100"><p><? echo $trim_group[$selectResult[csf('trim_group')]];?></p></td>
                    <td width="130"><p><? echo $selectResult[csf('description')];?></p></td>
                    <td width="100"><? echo $selectResult[csf('brand_sup_ref')];?></td>
                    <td width="100"><? echo $supplier_library[$selectResult[csf('nominated_supp')]];?></td>
                    <td width="80" align="right">
					<? 
						echo $req_qnty; 
						$total_req+=$req_qnty;
					?>
                    </td>
                    <td width="50"><? echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];?></td>
                    <td width="80" align="right"><? echo def_number_format($selectResult[csf('cu_woq')],5,"");?></td>
                    <td width="80" align="right">
					<?
					echo $bal_woq;
					?>
                    </td>
                    <td width="80" align="right">
					<? 
					echo $exchange_rate;
					?>
                    </td>
                    <td width="80" align="right">
					<? 
					echo $rate;
					?>
                    </td>
                    <td width="" align="right">
                    <?
					$amount=def_number_format($rate*$bal_woq,5,"");
					echo $amount; 
					$total_amount+=$amount;
					?>
                    </td>
                    </tr>
                    <?
					$i++;
						}
					}
					?>
             </table>
             <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1510" class="rpt_table" >
                <tfoot>
                    <th width="40"></th>
                    <th width="50"></th>
                    <th width="100"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="100"></th>
                   
                    <th width="100"></th>
                    <th width="130"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="80" id="value_total_req"><? echo number_format($total_req,2); ?></th>
                    <th width="50"><input type="text" style="width:40px"  id="txt_tot_req_amount" value="<? echo number_format($total_req_amount,2); ?>" /></th>
                    <th width="80"><input type="text" style="width:40px" id="txt_tot_cu_amount" value="<? echo number_format($total_cu_amount,2); ?>" /></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="" id="value_total_amount"><? echo number_format($total_amount,2); ?></th>
                </tfoot>
            </table>
            </div>
            <table width="790" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
            <script>
			var tableFilters = {
					col_operation: {
									   id: ["value_total_req","value_total_amount"],
									   col: [10,16],
									   operation: ["sum","sum"],
									   write_method: ["innerHTML","innerHTML"]
								   } 
							   }
			setFilterGrid('tbl_list_search',-1,tableFilters)
            </script>
        </div>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if ($action=="fnc_po_select_data")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
?>
	<script>
	  var selected_id = new Array();
	  var selected_name = new Array();	
	 function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
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
			var name = "";
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#txt_selected_id').val( id );
			$('#txt_selected_name').val( name );
			
		}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<? 
extract($_REQUEST);
//++++++++++++DO NOT DELETE THIS PART++++++++++++++++++++
/*$view_sql="CREATE or REPLACE  VIEW wo_trim_booking_data_park AS select  d.id as id,a.job_no,a.company_name,b.pub_shipment_date,c.id as wo_pre_cost_trim_cost_dtls,d.cons,e.costing_per,a.buyer_name,a.style_ref_no,b.id as po_id,b.po_number,c.trim_group,c.description,c.brand_sup_ref,
CASE e.costing_per WHEN 1 THEN round((d.cons/12)*b.po_quantity,4) WHEN 2 THEN round((d.cons/1)*b.po_quantity,4)  WHEN 3 THEN round((d.cons/24)*b.po_quantity,4) WHEN 4 THEN round((d.cons/36)*b.po_quantity,4) WHEN 5 THEN round((d.cons/48)*b.po_quantity,4) ELSE 0 END as req_qnty,c.cons_uom,IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0)  as cu_woq, CASE e.costing_per WHEN 1 THEN round(((d.cons/12)*b.po_quantity)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 2 THEN round(((d.cons/2)*b.po_quantity) - IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4)  WHEN 3 THEN round(((d.cons/24)*b.po_quantity)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 4 THEN round(((d.cons/36)*b.po_quantity)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 5 THEN round(((d.cons/48)*b.po_quantity)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) ELSE 0 END as bal_woq,c.rate from wo_po_details_master a, wo_po_break_down b ,wo_pre_cost_mst e,wo_pre_cost_trim_cost_dtls c , wo_pre_cost_trim_co_cons_dtls d left join wo_booking_dtls f on f.job_no=d.job_no and f.pre_cost_fabric_cost_dtls_id=d.wo_pre_cost_trim_cost_dtls_id and f.po_break_down_id=d.po_break_down_id  where a.job_no=b.job_no_mst and  a.job_no=c.job_no and a.job_no=e.job_no and  a.job_no=d.job_no   and c.id=d.wo_pre_cost_trim_cost_dtls_id and b.id=d.po_break_down_id group by d.id order by b.id
";
$rID_up=execute_query($view_sql);*/
///new view======================================
/*$view_sql="CREATE or REPLACE  VIEW wo_trim_booking_data_park AS select  d.id as id,a.job_no,a.company_name,b.pub_shipment_date,b.grouping,c.id as wo_pre_cost_trim_cost_dtls,d.cons,e.costing_per,a.buyer_name,a.style_ref_no,b.id as po_id,b.po_number,c.trim_group,c.description,c.brand_sup_ref,
CASE e.costing_per WHEN 1 THEN round(((d.cons/12)*b.po_quantity)/cc.conversion_factor,4) WHEN 2 THEN round(((d.cons/1)*b.po_quantity)/cc.conversion_factor,4)  WHEN 3 THEN round(((d.cons/24)*b.po_quantity)/cc.conversion_factor,4) WHEN 4 THEN round(((d.cons/36)*b.po_quantity)/cc.conversion_factor,4) WHEN 5 THEN round(((d.cons/48)*b.po_quantity)/cc.conversion_factor,4) ELSE 0 END as req_qnty,cc.order_uom as cons_uom,IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0)  as cu_woq, CASE e.costing_per WHEN 1 THEN round((((d.cons/12)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 2 THEN round((((d.cons/1)*b.po_quantity)/cc.conversion_factor) - IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4)  WHEN 3 THEN round((((d.cons/24)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 4 THEN round((((d.cons/36)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 5 THEN round((((d.cons/48)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) ELSE 0 END as bal_woq, round((c.rate*cc.conversion_factor),8) as rate from wo_po_details_master a, wo_po_break_down b ,wo_pre_cost_mst e,wo_pre_cost_trim_cost_dtls c ,lib_item_group cc, wo_pre_cost_trim_co_cons_dtls d left join wo_booking_dtls f on f.job_no=d.job_no and f.pre_cost_fabric_cost_dtls_id=d.wo_pre_cost_trim_cost_dtls_id and f.po_break_down_id=d.po_break_down_id and f.booking_type=2   where a.job_no=b.job_no_mst and  a.job_no=c.job_no and a.job_no=e.job_no and  a.job_no=d.job_no   and c.id=d.wo_pre_cost_trim_cost_dtls_id and b.id=d.po_break_down_id and cc.id=c.trim_group group by d.id order by b.id";
$rID_up=execute_query($view_sql);*/
//if($data[4]==2) $cbo_order_status="%%"; else $cbo_order_status= "$data[4]";

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

	 $booking_month=0;
	 if($cbo_booking_month<10)
	 {
		 $booking_month.=$cbo_booking_month;
	 }
	 else
	 {
		$booking_month=$cbo_booking_month; 
	 }
	 
	$start_date=$cbo_booking_year."-".$booking_month."-01";
	$end_date=$cbo_booking_year."-".$booking_month."-".cal_days_in_month(CAL_GREGORIAN, $booking_month, $cbo_booking_year);
?>
<input type="text" name="txt_selected_id" id="txt_selected_id" value="" />
<input type="text" name="txt_selected_name" id="txt_selected_name" value="" />

        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="550" class="rpt_table" >
                <thead>
                    <th width="40">SL</th>
                    <th width="50">Buyer</th>
                    <th width="100">Job No</th>
                    <th width="80">Style No</th>
                    <th width="100">Ord. No</th>
                    <th width="100">Ord. Qty</th>
                    <th width="">Pub Sipment Date</th>
                </thead>
         </table>
            <div style="width:550px; overflow-y:scroll; max-height:350px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="530" class="rpt_table" id="tbl_list_search" >
            <?
					$sql="select
					a.job_no_prefix_num,
					a.job_no,
					a.company_name,
					a.buyer_name,
					a.style_ref_no,
					d.id as po_id,
					d.po_number,
					d.po_quantity,
					d.pub_shipment_date
					from 
					wo_po_details_master a,
					wo_po_break_down d
					where
					a.job_no=d.job_no_mst and 
					a.company_name=$company_id $txt_job_no_cond $cbo_buyer_name_cond and
					a.garments_nature=$garments_nature and 
					d.is_deleted=0 and 
					d.status_active=1
					order by d.id";
					$i=1;
					$total_req=0;
					$total_amount=0;
                    $nameArray=sql_select( $sql );
                    foreach ($nameArray as $selectResult)
                    {
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
					
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
                    <td width="40">
					<? echo $i;?>
                    </td>
                    <td width="50"><? echo $buyer_arr [$selectResult[csf('buyer_name')]];?></td>
                    <td width="100"><? echo $selectResult[csf('job_no_prefix_num')];?></td>
                    <td width="80"><? echo $selectResult[csf('style_ref_no')];?></td>
                    <td width="100">
					<? echo $selectResult[csf('po_number')];?>
                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('po_id')]; ?>"/>
                     <input type="hidden" name="txt_individual_name" id="txt_individual_name<?php echo $i ?>" value="<? echo $selectResult[csf('po_number')]; ?>"/>
                    </td>
                    <td width="100">
					<? echo $selectResult[csf('po_quantity')];?>
                    </td>
                    <td width=""><? echo change_date_format($selectResult[csf('pub_shipment_date')],'dd-mm-yyyy','-');?></td>
                    </tr>
                    <?
					$i++;
					}
					?>
             </table>
             <table cellspacing="0" cellpadding="0" border="1" rules="all" width="530" class="rpt_table" >
                <tfoot>
                    <th width="40"></th>
                    <th width="50"></th>
                    <th width="100"></th>
                    <th width="80"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width=""></th>
                    
                </tfoot>
            </table>
            </div>
            <table width="500" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
            <script>
			var tableFilters = {
				col_operation: {
								   id: ["value_total_req","value_total_amount"],
								   col: [9,14],
								   operation: ["sum","sum"],
								   write_method: ["innerHTML","innerHTML"]
								} 
								}
			setFilterGrid('tbl_list_search',-1,tableFilters)
            </script>
        </div>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}


if ($action=="fnc_process_data_item_from_library")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
?>
	<script>
	  var selected_id = new Array();
	  var selected_po_id = new Array();	
	 function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
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
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			$('#txt_selected_id').val( id );
		}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<? 
extract($_REQUEST);
//++++++++++++DO NOT DELETE THIS PART++++++++++++++++++++
/*$view_sql="CREATE or REPLACE  VIEW wo_trim_booking_data_park AS select  d.id as id,a.job_no,a.company_name,b.pub_shipment_date,c.id as wo_pre_cost_trim_cost_dtls,d.cons,e.costing_per,a.buyer_name,a.style_ref_no,b.id as po_id,b.po_number,c.trim_group,c.description,c.brand_sup_ref,
CASE e.costing_per WHEN 1 THEN round((d.cons/12)*b.po_quantity,4) WHEN 2 THEN round((d.cons/1)*b.po_quantity,4)  WHEN 3 THEN round((d.cons/24)*b.po_quantity,4) WHEN 4 THEN round((d.cons/36)*b.po_quantity,4) WHEN 5 THEN round((d.cons/48)*b.po_quantity,4) ELSE 0 END as req_qnty,c.cons_uom,IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0)  as cu_woq, CASE e.costing_per WHEN 1 THEN round(((d.cons/12)*b.po_quantity)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 2 THEN round(((d.cons/2)*b.po_quantity) - IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4)  WHEN 3 THEN round(((d.cons/24)*b.po_quantity)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 4 THEN round(((d.cons/36)*b.po_quantity)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 5 THEN round(((d.cons/48)*b.po_quantity)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) ELSE 0 END as bal_woq,c.rate from wo_po_details_master a, wo_po_break_down b ,wo_pre_cost_mst e,wo_pre_cost_trim_cost_dtls c , wo_pre_cost_trim_co_cons_dtls d left join wo_booking_dtls f on f.job_no=d.job_no and f.pre_cost_fabric_cost_dtls_id=d.wo_pre_cost_trim_cost_dtls_id and f.po_break_down_id=d.po_break_down_id  where a.job_no=b.job_no_mst and  a.job_no=c.job_no and a.job_no=e.job_no and  a.job_no=d.job_no   and c.id=d.wo_pre_cost_trim_cost_dtls_id and b.id=d.po_break_down_id group by d.id order by b.id
";
$rID_up=execute_query($view_sql);*/
///new view======================================
/*$view_sql="CREATE or REPLACE  VIEW wo_trim_booking_data_park AS select  d.id as id,a.job_no,a.company_name,b.pub_shipment_date,b.grouping,c.id as wo_pre_cost_trim_cost_dtls,d.cons,e.costing_per,a.buyer_name,a.style_ref_no,b.id as po_id,b.po_number,c.trim_group,c.description,c.brand_sup_ref,
CASE e.costing_per WHEN 1 THEN round(((d.cons/12)*b.po_quantity)/cc.conversion_factor,4) WHEN 2 THEN round(((d.cons/1)*b.po_quantity)/cc.conversion_factor,4)  WHEN 3 THEN round(((d.cons/24)*b.po_quantity)/cc.conversion_factor,4) WHEN 4 THEN round(((d.cons/36)*b.po_quantity)/cc.conversion_factor,4) WHEN 5 THEN round(((d.cons/48)*b.po_quantity)/cc.conversion_factor,4) ELSE 0 END as req_qnty,cc.order_uom as cons_uom,IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0)  as cu_woq, CASE e.costing_per WHEN 1 THEN round((((d.cons/12)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 2 THEN round((((d.cons/1)*b.po_quantity)/cc.conversion_factor) - IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4)  WHEN 3 THEN round((((d.cons/24)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 4 THEN round((((d.cons/36)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 5 THEN round((((d.cons/48)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) ELSE 0 END as bal_woq, round((c.rate*cc.conversion_factor),8) as rate from wo_po_details_master a, wo_po_break_down b ,wo_pre_cost_mst e,wo_pre_cost_trim_cost_dtls c ,lib_item_group cc, wo_pre_cost_trim_co_cons_dtls d left join wo_booking_dtls f on f.job_no=d.job_no and f.pre_cost_fabric_cost_dtls_id=d.wo_pre_cost_trim_cost_dtls_id and f.po_break_down_id=d.po_break_down_id and f.booking_type=2   where a.job_no=b.job_no_mst and  a.job_no=c.job_no and a.job_no=e.job_no and  a.job_no=d.job_no   and c.id=d.wo_pre_cost_trim_cost_dtls_id and b.id=d.po_break_down_id and cc.id=c.trim_group group by d.id order by b.id";
$rID_up=execute_query($view_sql);*/
//if($data[4]==2) $cbo_order_status="%%"; else $cbo_order_status= "$data[4]";

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

	 $booking_month=0;
	 if($cbo_booking_month<10)
	 {
		 $booking_month.=$cbo_booking_month;
	 }
	 else
	 {
		$booking_month=$cbo_booking_month; 
	 }
	 
	$start_date=$cbo_booking_year."-".$booking_month."-01";
	$end_date=$cbo_booking_year."-".$booking_month."-".cal_days_in_month(CAL_GREGORIAN, $booking_month, $cbo_booking_year);
?>
<input type="text" name="txt_selected_id" id="txt_selected_id" value="" />
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="300" class="rpt_table" >
                <thead>
                    <th width="40">SL</th>
                    <th width="100">Trims Group</th>
                    <th width="">UOM</th>
                </thead>
            </table>
            <div style="width:300px; overflow-y:scroll; max-height:350px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="280" class="rpt_table" id="tbl_list_search" >
            <?
					
						
							$i=1;
					$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group where item_category=4 and status_active=1 and	is_deleted=0");
					foreach($sql_lib_item_group as $row_sql_lib_item_group)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
                    <td width="40">
					<? echo $i;?>
                    </td>
                    <td width="100">
					<? echo $row_sql_lib_item_group[csf('item_name')];?>
                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row_sql_lib_item_group[csf('id')]; ?>"/>
                    </td>
                    <td width=""><? echo $unit_of_measurement[$row_sql_lib_item_group[csf('cons_uom')]];?></td>
                    </tr>
                    <?
					$i++;
					}
					?>
             </table>
             <table cellspacing="0" cellpadding="0" border="1" rules="all" width="280" class="rpt_table" >
                <tfoot>
                    <th width="40"></th>
                    <th width="100"></th>
                    <th width=""></th>
                    
                </tfoot>
            </table>
            </div>
            <table width="300" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
            <script>
			var tableFilters = {
				col_operation: {
								   id: ["value_total_req","value_total_amount"],
								   col: [9,14],
								   operation: ["sum","sum"],
								   write_method: ["innerHTML","innerHTML"]
								} 
								}
			setFilterGrid('tbl_list_search',-1,tableFilters)
            </script>
        </div>
</div>
</body>           
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
		 $cbo_buyer_name_cond ="and a.buyer_name='$cbo_buyer_name'";
		 $cbo_buyer_name_cond1 ="and buyer_name='$cbo_buyer_name'";
	 }
	 
	 $booking_month=0;
	 if($cbo_booking_month<10)
	 {
		 $booking_month.=$cbo_booking_month;
	 }
	 else
	 {
		$booking_month=$cbo_booking_month; 
	 }
	$start_date=$cbo_booking_year."-".$booking_month."-01";
	$end_date=$cbo_booking_year."-".$booking_month."-".cal_days_in_month(CAL_GREGORIAN, $booking_month, $cbo_booking_year);
	$data=str_replace("'","",$data);
	?>
   <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1130" class="rpt_table" >
                <thead>
                    <th width="40">SL</th>
                    <th width="100">Ord. No</th>
                    <th width="100">Trims Group</th>
                    <th width="80">Req. Qnty</th>
                    <th width="80">UOM</th>
                    <th width="80">CU WOQ</th>
                    <th width="80">Bal WOQ</th>
                    <th width="100">Sensitivity</th>
                    <th width="80">WOQ</th>
                    <th width="80">Exch.Rate</th>
                    <th width="80">Rate</th>
                    <th width="80">Amount</th>
                    <th width="">Delv. Date</th>
                </thead>
            </table>
            <div style="width:1130px; overflow-y:scroll; max-height:350px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1113" class="rpt_table" id="tbl_list_search" >
       <tbody>
       <?
	    //$sql="select id,po_id,wo_pre_cost_trim_cost_dtls,buyer_name,job_no,style_ref_no,po_number,trim_group,description,brand_sup_ref,req_qnty,cons_uom,cu_woq,bal_woq,rate from wo_trim_booking_data_park where id in($data) order by trim_group";
		 /*$sql="select id,po_id,wo_pre_cost_trim_cost_dtls,buyer_name,job_no_prefix_num,job_no,style_ref_no,po_number,trim_group,description,brand_sup_ref,req_qnty,cons_uom,cu_woq,bal_woq,rate from ( select  d.id as id,a.job_no_prefix_num,a.job_no,a.company_name,b.pub_shipment_date,b.grouping,c.id as wo_pre_cost_trim_cost_dtls,d.cons,e.costing_per,a.buyer_name,a.style_ref_no,b.id as po_id,b.po_number,c.trim_group,c.description,c.brand_sup_ref,
CASE e.costing_per WHEN 1 THEN round(((d.cons/12)*b.po_quantity)/cc.conversion_factor,4) WHEN 2 THEN round(((d.cons/1)*b.po_quantity)/cc.conversion_factor,4)  WHEN 3 THEN round(((d.cons/24)*b.po_quantity)/cc.conversion_factor,4) WHEN 4 THEN round(((d.cons/36)*b.po_quantity)/cc.conversion_factor,4) WHEN 5 THEN round(((d.cons/48)*b.po_quantity)/cc.conversion_factor,4) ELSE 0 END as req_qnty,cc.order_uom as cons_uom,IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0)  as cu_woq, CASE e.costing_per WHEN 1 THEN round((((d.cons/12)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 2 THEN round((((d.cons/1)*b.po_quantity)/cc.conversion_factor) - IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4)  WHEN 3 THEN round((((d.cons/24)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 4 THEN round((((d.cons/36)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 5 THEN round((((d.cons/48)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) ELSE 0 END as bal_woq, round((c.rate*cc.conversion_factor),8) as rate from wo_po_details_master a, wo_po_break_down b ,wo_pre_cost_mst e,wo_pre_cost_trim_cost_dtls c ,lib_item_group cc, wo_pre_cost_trim_co_cons_dtls d left join wo_booking_dtls f on f.job_no=d.job_no and f.pre_cost_fabric_cost_dtls_id=d.wo_pre_cost_trim_cost_dtls_id and f.po_break_down_id=d.po_break_down_id and f.booking_type=2   where a.job_no=b.job_no_mst and  a.job_no=c.job_no and a.job_no=e.job_no and  a.job_no=d.job_no   and c.id=d.wo_pre_cost_trim_cost_dtls_id and b.id=d.po_break_down_id and cc.id=c.trim_group and b.pub_shipment_date between '$start_date' and '$end_date'  and a.company_name=$cbo_company_name group by d.id order by b.id) m   where id in($data) order by trim_group";*/
		 
		   /*$sql="select id,po_id,wo_pre_cost_trim_cost_dtls,buyer_name,job_no_prefix_num,job_no,style_ref_no,po_number,trim_group,description,brand_sup_ref,req_qnty,cons_uom,cu_woq,bal_woq,rate from ( select  d.id as id,a.job_no_prefix_num,a.job_no,a.company_name,b.pub_shipment_date,b.grouping,c.id as wo_pre_cost_trim_cost_dtls,d.cons,e.costing_per,a.buyer_name,a.style_ref_no,a.garments_nature,b.id as po_id,b.po_number,c.trim_group,c.description,c.brand_sup_ref,
CASE e.costing_per WHEN 1 THEN round(((d.cons/12)*b.po_quantity)/cc.conversion_factor,4) WHEN 2 THEN round(((d.cons/1)*b.po_quantity)/cc.conversion_factor,4)  WHEN 3 THEN round(((d.cons/24)*b.po_quantity)/cc.conversion_factor,4) WHEN 4 THEN round(((d.cons/36)*b.po_quantity)/cc.conversion_factor,4) WHEN 5 THEN round(((d.cons/48)*b.po_quantity)/cc.conversion_factor,4) ELSE 0 END as req_qnty,cc.order_uom as cons_uom,IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0)  as cu_woq, CASE e.costing_per WHEN 1 THEN round((((d.cons/12)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 2 THEN round((((d.cons/1)*b.po_quantity)/cc.conversion_factor) - IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4)  WHEN 3 THEN round((((d.cons/24)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 4 THEN round((((d.cons/36)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 5 THEN round((((d.cons/48)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) ELSE 0 END as bal_woq, round((c.rate*cc.conversion_factor),8) as rate from wo_po_details_master a, wo_po_break_down b ,wo_pre_cost_mst e,wo_pre_cost_trim_cost_dtls c ,lib_item_group cc, wo_pre_cost_trim_co_cons_dtls d left join wo_booking_dtls f on f.job_no=d.job_no and f.pre_cost_fabric_cost_dtls_id=d.wo_pre_cost_trim_cost_dtls_id and f.po_break_down_id=d.po_break_down_id and f.booking_type=2   where a.job_no=b.job_no_mst and  a.job_no=c.job_no and a.job_no=e.job_no and  a.job_no=d.job_no   and c.id=d.wo_pre_cost_trim_cost_dtls_id and b.id=d.po_break_down_id and cc.id=c.trim_group  and a.company_name=$cbo_company_name $txt_job_no_cond $cbo_buyer_name_cond and a.garments_nature=$garments_nature group by d.id order by b.id) m   where id in($data) $txt_job_no_cond1  $cbo_buyer_name_cond1 and garments_nature=$garments_nature order by trim_group";//and b.pub_shipment_date between '$start_date' and '$end_date'*/ 
		   
					$sql_lib_item_group_array=array();
					$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
					foreach($sql_lib_item_group as $row_sql_lib_item_group)
					{
					$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
					$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
					$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
					}	
					
					
					
					$sql="select
					a.job_no_prefix_num,
					a.job_no,
					a.company_name,
					a.buyer_name,
					a.style_ref_no,
					b.costing_per,
					b.exchange_rate,
					c.id as wo_pre_cost_trim_cost_dtls,
					c.trim_group,
					c.description,
					c.brand_sup_ref,
					c.rate,
					c.amount as pre_amt,
					d.id as po_id,
					d.po_number,
					d.po_quantity as plan_cut,
					e.id as id,
					e.po_break_down_id,
					e.cons,
					sum(f.wo_qnty) as cu_woq,
					sum(f.amount) as cu_amount
					from 
					wo_po_details_master a,
					wo_pre_cost_mst b,
					wo_pre_cost_trim_cost_dtls c,
					wo_po_break_down d,
					wo_pre_cost_trim_co_cons_dtls e  
					left join wo_booking_dtls f
					on 
					f.job_no=e.job_no and 
					f.pre_cost_fabric_cost_dtls_id=e.wo_pre_cost_trim_cost_dtls_id and 
					f.po_break_down_id=e.po_break_down_id and 
					f.booking_type=2
					where
					a.job_no=b.job_no and 
					a.job_no=c.job_no and 
					a.job_no=d.job_no_mst and 
					a.job_no=e.job_no and
					c.id=e.wo_pre_cost_trim_cost_dtls_id and
					d.id=e.po_break_down_id and 
					a.company_name=$cbo_company_name $txt_job_no_cond $cbo_buyer_name_cond and
					a.garments_nature=$garments_nature and 
					e.id in($data) and
					d.is_deleted=0 and 
					d.status_active=1
					group by 
					e.id,
					a.job_no_prefix_num,
					a.job_no,
					a.company_name,
					a.buyer_name,
					a.style_ref_no,
					b.costing_per,
					b.exchange_rate,
					c.id,
					c.trim_group,
					c.description,
					c.brand_sup_ref,
					c.rate,
					c.amount,
					d.id,
					d.po_number,
					d.po_quantity,
					e.po_break_down_id,
					e.cons
					order by d.id,c.id";		 
					$i=1;
                    $nameArray=sql_select( $sql );
                    foreach ($nameArray as $selectResult)
                    {
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
					    if($selectResult[csf('costing_per')]==1)
						{
							$costing_per_qty=12;
						}
						else if($selectResult[csf('costing_per')]==2)
						{
							$costing_per_qty=1;
						}
						else if($selectResult[csf('costing_per')]==3)
						{
							$costing_per_qty=24;
						}
						else if($selectResult[csf('costing_per')]==4)
						{
							$costing_per_qty=36;
						}
						else if($selectResult[csf('costing_per')]==5)
						{
							$costing_per_qty=48;
						}
						$exchange_rate=$selectResult[csf('exchange_rate')];
						
						if($cbo_currency==$cbo_currency_job)
						{
						 $exchange_rate=1;	
						}
						
						$req_qnty=def_number_format( ( $selectResult[csf('cons')]*($selectResult[csf('plan_cut')]/12) )/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor] ,5,"");
						$bal_woq=def_number_format($req_qnty-$selectResult[csf('cu_woq')],5,"");
						
						$rate=def_number_format(($selectResult[csf('rate')]*$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor])*$exchange_rate,5,"");
						$country_arr=return_library_array("select country_id, country_id from wo_po_color_size_breakdown   WHERE po_break_down_id=".$selectResult[csf('po_id')]." and  status_active=1 and is_deleted=0 group by country_id","country_id","country_id");
						$country_id_string=implode(",", $country_arr);
	   ?>
       <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                    <td width="40"><? echo $i;?></td>
                    <td width="100">
					<? echo $selectResult[csf('po_number')];?> 
                    <input type="hidden" id="txtbookingid_<? echo $i;?>" value="" readonly/>
                    <input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $selectResult[csf('po_id')];?>" readonly/>
                    <input type="hidden" id="txtcountry_<? echo $i;?>"  value="<? echo $country_id_string ?>" readonly />
                    <input type="hidden" id="txtdesc_<? echo $i;?>"  value="<? echo $selectResult[csf('description')];?>" readonly />
                    <input type="hidden" id="txtbrandsup_<? echo $i;?>"  value="<? echo $selectResult[csf('brand_sup_ref')];?>" readonly />
                    </td>
                    <td width="100"  title="<? echo $sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor];  ?>">
					<? echo $trim_group[$selectResult[csf('trim_group')]];?> 
                    <input type="hidden" id="txttrimcostid_<? echo $i;?>" value="<? echo $selectResult[csf('wo_pre_cost_trim_cost_dtls')];?>" readonly/> 
                    <input type="hidden" id="txttrimgroup_<? echo $i;?>" value="<? echo $selectResult[csf('trim_group')];?>" readonly/>
                    </td>
                    <td width="80" align="right">
                    <?
					$req_amount=$req_qnty*$rate;
					$tot_req_qty+=$req_qnty;
					$tot_req_amount+=$req_amount;
					?>
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i;?>" value="<? echo $req_qnty;?>"  readonly  />
                    <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<? echo $i;?>" value="<? echo $req_amount;?>"  readonly  />
                    <input type="hidden" id="preconsamt_<? echo $i;?>"  value="<? echo $selectResult[csf('pre_amt')];?>" readonly />
                    </td>
                    <td width="80">
					<? echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];?> 
                    <input type="hidden" id="txtuom_<? echo $i;?>" value="<? echo $sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom];?>" readonly />
                    </td>
                    <td width="80" align="right">
					<?
					$tot_cu_woq+=$selectResult[csf('cu_woq')];
					$tot_cu_amount+=$selectResult[csf('cu_amount')];
					?>
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i;?>" value="<? echo $selectResult[csf('cu_woq')];?>"  readonly  />
                     <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuamount_<? echo $i;?>" value="<? echo $selectResult[csf('cu_amount')];?>"  readonly  />
                    </td>
                    <td width="80" align="right">
					<?
					$tot_bal_woq+=$bal_woq;
					?>
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i;?>" value="<? echo $bal_woq; ?>"  readonly  />
                    </td>
                    <td width="100" align="right">
					<?  echo create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", "", "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)","","" ); ?>
                    </td>
                    <td width="80" align="right">
					
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo $bal_woq;?>" onClick="open_consumption_popup('requires/trims_booking_controller_v2.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<? echo $i;?>',<? echo $i;?>)" />
                    </td>
                    <td width="80" align="right">
                     <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtexchrate_<? echo $i;?>" value="<? echo $exchange_rate;?>" readonly />
                     
                    </td>
                    <td width="80" align="right">
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo $rate;?>" onChange="calculate_amount(<? echo $i; ?>)" />
                    
                     <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i;?>" value="<? echo $rate;?>" readonly />
                     
                    </td>
                    
                    <td width="80" align="right">
					<?
					$amount=def_number_format($rate*$bal_woq,5,"");
					$total_amount+=$amount;
					?>
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo $amount;?>"  readonly  />
                    </td>
                    <td width="" align="right">
                    <input type="text"   style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i;?>"  class="datepicker" value="<? echo $txt_delivery_date; ?>"  readonly  />
                    <input type="hidden" id="consbreckdown_<? echo $i;?>"  value=""/>
                    </td>
                    </tr>
	   <?
	   $i++;
					}
       ?>
	</tbody>
    </table>
    </div>
 
    <table width="1130" class="rpt_table" border="0" rules="all">
    <tfoot>
    <tr>
	   
                    <th width="40">SL</th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="80"><? echo $tot_req_qty; ?></th>
                    <th width="80"><? //echo $tot_req_amount; ?></th>
                    <th width="80"><? echo $tot_cu_woq; ?></th>
                    <th width="80"><? echo $tot_bal_woq; ?></th>
                    <th width="100"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="80"><input type="text" id="tot_amount" value="<? echo  $total_amount; ?>" style="width:80px"/><? //echo  $total_amount; ?></th>
                    <th width=""><input type="hidden" id="saved_tot_amount" value="0" style="width:80px; text-align:right" readonly/></th>
   </tr>
   </tfoot>
   </table>
       
   
<?
}


if ($action=="generate_fabric_booking_without_precost")
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
		 $cbo_buyer_name_cond ="and a.buyer_name='$cbo_buyer_name'";
		 $cbo_buyer_name_cond1 ="and buyer_name='$cbo_buyer_name'";
	 }
	 
	 if($txt_select_po=="")
	 {
		 $po_id_cond="";
	 }
	 else
	 {
		 $po_id_cond ="and d.id in($txt_select_po)";
	 }
	 
	 
	 
	 $booking_month=0;
	 if($cbo_booking_month<10)
	 {
		 $booking_month.=$cbo_booking_month;
	 }
	 else
	 {
		$booking_month=$cbo_booking_month; 
	 }
	$start_date=$cbo_booking_year."-".$booking_month."-01";
	$end_date=$cbo_booking_year."-".$booking_month."-".cal_days_in_month(CAL_GREGORIAN, $booking_month, $cbo_booking_year);
	$data=str_replace("'","",$data);
	?>
   <table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table" >
                <thead>
                    <th width="40">SL</th>
                    <th width="100">Ord. No</th>
                    <th width="100">Trims Group</th>
                    <th width="80">UOM</th>
                    <th width="100">Sensitivity</th>
                    <th width="80">WOQ</th>
                    <th width="80">Exch.Rate</th>
                    <th width="80">Rate</th>
                    <th width="80">Amount</th>
                    <th width="">Delv. Date</th>
                </thead>
            </table>
            <div style="width:880px; overflow-y:scroll; max-height:350px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="863" class="rpt_table" id="tbl_list_search" >
       <tbody>
       <?
	    //$sql="select id,po_id,wo_pre_cost_trim_cost_dtls,buyer_name,job_no,style_ref_no,po_number,trim_group,description,brand_sup_ref,req_qnty,cons_uom,cu_woq,bal_woq,rate from wo_trim_booking_data_park where id in($data) order by trim_group";
		 /*$sql="select id,po_id,wo_pre_cost_trim_cost_dtls,buyer_name,job_no_prefix_num,job_no,style_ref_no,po_number,trim_group,description,brand_sup_ref,req_qnty,cons_uom,cu_woq,bal_woq,rate from ( select  d.id as id,a.job_no_prefix_num,a.job_no,a.company_name,b.pub_shipment_date,b.grouping,c.id as wo_pre_cost_trim_cost_dtls,d.cons,e.costing_per,a.buyer_name,a.style_ref_no,b.id as po_id,b.po_number,c.trim_group,c.description,c.brand_sup_ref,
CASE e.costing_per WHEN 1 THEN round(((d.cons/12)*b.po_quantity)/cc.conversion_factor,4) WHEN 2 THEN round(((d.cons/1)*b.po_quantity)/cc.conversion_factor,4)  WHEN 3 THEN round(((d.cons/24)*b.po_quantity)/cc.conversion_factor,4) WHEN 4 THEN round(((d.cons/36)*b.po_quantity)/cc.conversion_factor,4) WHEN 5 THEN round(((d.cons/48)*b.po_quantity)/cc.conversion_factor,4) ELSE 0 END as req_qnty,cc.order_uom as cons_uom,IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0)  as cu_woq, CASE e.costing_per WHEN 1 THEN round((((d.cons/12)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 2 THEN round((((d.cons/1)*b.po_quantity)/cc.conversion_factor) - IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4)  WHEN 3 THEN round((((d.cons/24)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 4 THEN round((((d.cons/36)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 5 THEN round((((d.cons/48)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) ELSE 0 END as bal_woq, round((c.rate*cc.conversion_factor),8) as rate from wo_po_details_master a, wo_po_break_down b ,wo_pre_cost_mst e,wo_pre_cost_trim_cost_dtls c ,lib_item_group cc, wo_pre_cost_trim_co_cons_dtls d left join wo_booking_dtls f on f.job_no=d.job_no and f.pre_cost_fabric_cost_dtls_id=d.wo_pre_cost_trim_cost_dtls_id and f.po_break_down_id=d.po_break_down_id and f.booking_type=2   where a.job_no=b.job_no_mst and  a.job_no=c.job_no and a.job_no=e.job_no and  a.job_no=d.job_no   and c.id=d.wo_pre_cost_trim_cost_dtls_id and b.id=d.po_break_down_id and cc.id=c.trim_group and b.pub_shipment_date between '$start_date' and '$end_date'  and a.company_name=$cbo_company_name group by d.id order by b.id) m   where id in($data) order by trim_group";*/
		 
		   /*$sql="select id,po_id,wo_pre_cost_trim_cost_dtls,buyer_name,job_no_prefix_num,job_no,style_ref_no,po_number,trim_group,description,brand_sup_ref,req_qnty,cons_uom,cu_woq,bal_woq,rate from ( select  d.id as id,a.job_no_prefix_num,a.job_no,a.company_name,b.pub_shipment_date,b.grouping,c.id as wo_pre_cost_trim_cost_dtls,d.cons,e.costing_per,a.buyer_name,a.style_ref_no,a.garments_nature,b.id as po_id,b.po_number,c.trim_group,c.description,c.brand_sup_ref,
CASE e.costing_per WHEN 1 THEN round(((d.cons/12)*b.po_quantity)/cc.conversion_factor,4) WHEN 2 THEN round(((d.cons/1)*b.po_quantity)/cc.conversion_factor,4)  WHEN 3 THEN round(((d.cons/24)*b.po_quantity)/cc.conversion_factor,4) WHEN 4 THEN round(((d.cons/36)*b.po_quantity)/cc.conversion_factor,4) WHEN 5 THEN round(((d.cons/48)*b.po_quantity)/cc.conversion_factor,4) ELSE 0 END as req_qnty,cc.order_uom as cons_uom,IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0)  as cu_woq, CASE e.costing_per WHEN 1 THEN round((((d.cons/12)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 2 THEN round((((d.cons/1)*b.po_quantity)/cc.conversion_factor) - IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4)  WHEN 3 THEN round((((d.cons/24)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 4 THEN round((((d.cons/36)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 5 THEN round((((d.cons/48)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) ELSE 0 END as bal_woq, round((c.rate*cc.conversion_factor),8) as rate from wo_po_details_master a, wo_po_break_down b ,wo_pre_cost_mst e,wo_pre_cost_trim_cost_dtls c ,lib_item_group cc, wo_pre_cost_trim_co_cons_dtls d left join wo_booking_dtls f on f.job_no=d.job_no and f.pre_cost_fabric_cost_dtls_id=d.wo_pre_cost_trim_cost_dtls_id and f.po_break_down_id=d.po_break_down_id and f.booking_type=2   where a.job_no=b.job_no_mst and  a.job_no=c.job_no and a.job_no=e.job_no and  a.job_no=d.job_no   and c.id=d.wo_pre_cost_trim_cost_dtls_id and b.id=d.po_break_down_id and cc.id=c.trim_group  and a.company_name=$cbo_company_name $txt_job_no_cond $cbo_buyer_name_cond and a.garments_nature=$garments_nature group by d.id order by b.id) m   where id in($data) $txt_job_no_cond1  $cbo_buyer_name_cond1 and garments_nature=$garments_nature order by trim_group";//and b.pub_shipment_date between '$start_date' and '$end_date'*/ 
		   
				/*	$sql_lib_item_group_array=array();
					$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
					foreach($sql_lib_item_group as $row_sql_lib_item_group)
					{
					$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
					$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
					$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
					}*/	
					$sql="select
					a.job_no_prefix_num,
					a.job_no,
					a.company_name,
					a.buyer_name,
					a.style_ref_no,
					d.id as po_id,
					d.po_number,
					d.po_quantity as plan_cut,
					e.exchange_rate
					from 
					wo_po_details_master a,
					wo_po_break_down d,
					wo_pre_cost_mst e
					where
					a.job_no=d.job_no_mst and 
					a.job_no=e.job_no and 
					a.company_name=$cbo_company_name $txt_job_no_cond $cbo_buyer_name_cond $po_id_cond and
					a.garments_nature=$garments_nature and 
					d.is_deleted=0 and 
					d.status_active=1
					order by d.id";		 
					$i=1;
                    $nameArray=sql_select( $sql );
                    foreach ($nameArray as $selectResult)
                    {
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
							
						$exchange_rate=$selectResult[csf('exchange_rate')];
						if($cbo_currency==$cbo_currency_job)
						{
						 $exchange_rate=1;	
						}
					    /*if($selectResult[csf('costing_per')]==1)
						{
							$costing_per_qty=12;
						}
						else if($selectResult[csf('costing_per')]==2)
						{
							$costing_per_qty=1;
						}
						else if($selectResult[csf('costing_per')]==3)
						{
							$costing_per_qty=24;
						}
						else if($selectResult[csf('costing_per')]==4)
						{
							$costing_per_qty=36;
						}
						else if($selectResult[csf('costing_per')]==5)
						{
							$costing_per_qty=48;
						}
						$req_qnty=def_number_format(($selectResult[csf('cons')]*($selectResult[csf('plan_cut')]/12))/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],5,"");
						$bal_woq=def_number_format($req_qnty-$selectResult[csf('cu_woq')],5,"");
						
						$rate=def_number_format(($selectResult[csf('rate')]*$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor]),5,"");*/
						$country_arr=return_library_array("select country_id, country_id from wo_po_color_size_breakdown   WHERE po_break_down_id=".$selectResult[csf('po_id')]." and  status_active=1 and is_deleted=0 group by country_id","country_id","country_id");
						$country_id_string=implode(",", $country_arr);
						
						$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group where item_category=4 and id in($data)  and status_active=1 and	is_deleted=0");
					foreach($sql_lib_item_group as $row_sql_lib_item_group)
					{
	   ?>
       <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                    <td width="40"><? echo $i;?></td>
                    <td width="100">
					<? echo $selectResult[csf('po_number')];?> 
                    <input type="hidden" id="txtbookingid_<? echo $i;?>" value="" readonly/>
                    <input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $selectResult[csf('po_id')];?>" readonly/>
                    <input type="hidden" id="txtcountry_<? echo $i;?>"  value="<? echo $country_id_string ?>" readonly />
                     <input type="hidden" id="txtdesc_<? echo $i;?>"  value="" readonly />
                     <input type="hidden" id="txtbrandsup_<? echo $i;?>"  value="" readonly />
                    </td>
                    <td width="100">
					<? echo $row_sql_lib_item_group[csf('item_name')];?> 
                    <input type="hidden" id="txttrimcostid_<? echo $i;?>" value="" readonly/> 
                    <input type="hidden" id="txttrimgroup_<? echo $i;?>" value="<? echo $row_sql_lib_item_group[csf('id')];?>" readonly/>
                    <input type="hidden"  id="txtreqqnty_<? echo $i;?>" value=""  readonly  />
                    <input type="hidden" id="preconsamt_<? echo $i;?>"  value="" readonly />
                    <input type="hidden" id="txtreqamount_<? echo $i;?>" value=""  readonly  />
                    <input type="hidden"  id="txtcuwoq_<? echo $i;?>" value=""  readonly  />
                    <input type="hidden"  id="txtcuamount_<? echo $i;?>" value=""  readonly  />
                    <input type="hidden"  id="txtbalwoq_<? echo $i;?>" value=""  readonly  />
                    </td>
                    <td width="80">
					<? echo $unit_of_measurement[$row_sql_lib_item_group[csf('cons_uom')]];?> 
                    <input type="hidden" id="txtuom_<? echo $i;?>" value="<? echo $row_sql_lib_item_group[csf('cons_uom')];?>" readonly />
                    </td>
                    <td width="100" align="right">
					<?  echo create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", "", "set_cons_break_down($i)","","" ); ?>
                    </td>
                    <td width="80" align="right">
					
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="" onClick="open_consumption_popup('requires/trims_booking_controller_v2.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<? echo $i;?>',<? echo $i;?>)"     />
                    </td>
                    <td width="80" align="right">
                     <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtexchrate_<? echo $i;?>" value="<? echo $exchange_rate;?>" readonly />
                    
                     
                    </td>
                     <td width="80" align="right">
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo $rate;?>" onChange="calculate_amount(<? echo $i; ?>)" />
                    
                     <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i;?>" value="" readonly />
                     
                    </td>
                    <td width="80" align="right">
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value=""  readonly  />
                    </td>
                    <td width="" align="right">
                    <input type="text"   style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i;?>"  class="datepicker" value="<? echo $txt_delivery_date; ?>"  readonly  />
                    <input type="hidden" id="consbreckdown_<? echo $i;?>"  value=""/>
                    </td>
                    </tr>
	   <?
	   $i++;
					}
					}
       ?>
	</tbody>
    </table>
    </div>
 
    <table width="880" class="rpt_table" border="0" rules="all">
    <tfoot>
    <tr>
	   
                    <th width="40">SL</th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="80"></th>
                    <th width="100"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="80"><input type="hidden" id="tot_amount" value="<? echo  $total_amount; ?>" style="width:80px"/></th>
                    <th width="80"><input type="hidden" id="saved_tot_amount" value="0" style="width:80px; text-align:right" readonly/></th>
                    <th width=""></th>
   </tr>
   </tfoot>
   </table>
       
   
<?
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
	 
	 $booking_month=0;
	 if($cbo_booking_month<10)
	 {
		 $booking_month.=$cbo_booking_month;
	 }
	 else
	 {
		$booking_month=$cbo_booking_month; 
	 }
	$start_date=$cbo_booking_year."-".$booking_month."-01";
	$end_date=$cbo_booking_year."-".$booking_month."-".cal_days_in_month(CAL_GREGORIAN, $booking_month, $cbo_booking_year);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1290" class="rpt_table" >
                <thead>
                    <th width="40">SL</th>
                    <th width="100">Ord. No</th>
                    <th width="80">File No</th>
                    <th width="80">Ref. No</th>
                    <th width="100">Trims Group</th>
                    <th width="80">Req. Qnty</th>
                    <th width="80">UOM</th>
                    <th width="80">CU WOQ</th>
                    <th width="80">Bal WOQ</th>
                    <th width="100">Sensitivity</th>
                    <th width="80">WOQ</th>
                    <th width="80">Exch.Rate</th>
                    <th width="80">Rate</th>
                    <th width="80">Amount</th>
                    <th width="">Delv. Date</th>
                </thead>
            </table>
            <div style="width:1290px; overflow-y:scroll; max-height:350px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1273" class="rpt_table" id="tbl_list_search" >
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
					 $sql="select
					m.job_no_prefix_num,
					m.job_no,
					m.company_name,
					m.buyer_name,
					m.style_ref_no,
					m.costing_per,
					m.wo_pre_cost_trim_cost_dtls,
					m.trim_group,
					m.rate as precost_rate,
					m.po_id,
					m.po_number,
					m.plan_cut,
					m.id,
					m.po_break_down_id,
					m.cons,
					m.cu_woq,
					m.cu_amount,
					m.description,
					m.brand_sup_ref,
					n.id as booking_id,
					n.booking_no,
					n.sensitivity,
					n.wo_qnty,
					n.rate,
					n.exchange_rate,
					n.amount,
					n.delivery_date,
					n.cons_break_down 
					
					from (select
					a.job_no_prefix_num,
					a.job_no,
					a.company_name,
					a.buyer_name,
					a.style_ref_no,
					b.costing_per,
					c.id as wo_pre_cost_trim_cost_dtls,
					c.trim_group,
					c.description,
					c.brand_sup_ref,
					c.rate,
					c.amount as pre_amt,
					d.id as po_id,
					d.po_number,
					d.file_no,
					d.grouping,
					d.po_quantity as plan_cut,
					e.id as id,
					e.po_break_down_id,
					e.cons,
					sum(f.wo_qnty) as cu_woq,
					sum(f.amount) as cu_amount
					from 
					wo_po_details_master a,
					wo_pre_cost_mst b,
					wo_pre_cost_trim_cost_dtls c,
					wo_po_break_down d,
					wo_pre_cost_trim_co_cons_dtls e  
					left join wo_booking_dtls f
					on 
					f.job_no=e.job_no and 
					f.pre_cost_fabric_cost_dtls_id=e.wo_pre_cost_trim_cost_dtls_id and 
					f.po_break_down_id=e.po_break_down_id and 
					f.booking_type=2
					where
					a.job_no=b.job_no and 
					a.job_no=c.job_no and 
					a.job_no=d.job_no_mst and 
					a.job_no=e.job_no and
					c.id=e.wo_pre_cost_trim_cost_dtls_id and
					d.id=e.po_break_down_id and 
					a.company_name=$cbo_company_name $txt_job_no_cond $cbo_buyer_name_cond and
					a.garments_nature=$garments_nature and 
					d.is_deleted=0 and 
					d.status_active=1
					group by 
					e.id,
					a.job_no_prefix_num,
					a.job_no,
					a.company_name,
					a.buyer_name,
					a.style_ref_no,
					b.costing_per,
					c.id,
					c.trim_group,
					c.description,
					c.brand_sup_ref,
					c.rate,
					c.amount,
					d.id,
					d.po_number,
					d.file_no,
					d.grouping,
					d.po_quantity,
					e.po_break_down_id,
					e.cons
					order by d.id,c.id) m, wo_booking_dtls n where m.job_no=n.job_no and m.po_id=n.po_break_down_id and m.wo_pre_cost_trim_cost_dtls=n.pre_cost_fabric_cost_dtls_id  and n.booking_no=$txt_booking_no and n.is_deleted=0 and 
					n.status_active=1 order by m.po_id,m.wo_pre_cost_trim_cost_dtls ";

					$i=1;
					$total_amount=0;
                    $nameArray=sql_select( $sql );
                    foreach ($nameArray as $selectResult)
                    {
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
							
						if($selectResult[csf('costing_per')]==1)
						{
							$costing_per_qty=12;
						}
						else if($selectResult[csf('costing_per')]==2)
						{
							$costing_per_qty=1;
						}
						else if($selectResult[csf('costing_per')]==3)
						{
							$costing_per_qty=24;
						}
						else if($selectResult[csf('costing_per')]==4)
						{
							$costing_per_qty=36;
						}
						else if($selectResult[csf('costing_per')]==5)
						{
							$costing_per_qty=48;
						}
						$req_qnty=def_number_format(($selectResult[csf('cons')]*($selectResult[csf('plan_cut')]/12))/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],5,"");
						$bal_woq=def_number_format($req_qnty-$selectResult[csf('cu_woq')],5,"");
						
						//$rate=def_number_format(($selectResult[csf('rate')]*$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor]),5,"");
						$rate=def_number_format(($selectResult[csf('rate')]),5,"");

						$precost_rate=def_number_format(($selectResult[csf('precost_rate')]*$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor]*$selectResult[csf('exchange_rate')]),5,"");
						
						$country_arr=return_library_array("select country_id, country_id from wo_po_color_size_breakdown   WHERE po_break_down_id=".$selectResult[csf('po_id')]." and  status_active=1 and is_deleted=0 group by country_id","country_id","country_id");
						$country_id_string=implode(",", $country_arr);
						$sql_file=sql_select("select file_no,grouping from wo_po_break_down where id=".$selectResult[csf('po_id')]."");
				//echo "select file_no,grouping from wo_po_break_down where id=".$selectResult[csf('po_id')]."";
				 list($sql_po_data)=$sql_file;
                $file_no=$sql_po_data[csf('file_no')];
				 $ref_no=$sql_po_data[csf('grouping')];
				
				
						
	   ?>
       <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                    <td width="40"><? echo $i;?></td>
                    <td width="100">
					<? echo $selectResult[csf('po_number')];?> 
                    <input type="hidden" id="txtbookingid_<? echo $i;?>" value="<? echo $selectResult[csf('booking_id')];?>" readonly/>
                    <input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $selectResult[csf('po_id')];?>" readonly/>
                    <input type="hidden" id="txtcountry_<? echo $i;?>"  value="<? echo $country_id_string ?>" readonly />
                    <input type="hidden" id="txtdesc_<? echo $i;?>"  value="<? echo $selectResult[csf('description')];?>" readonly />
                     <input type="hidden" id="txtbrandsup_<? echo $i;?>"  value="<? echo $selectResult[csf('brand_sup_ref')];?>" readonly />
                    </td>
                     <td width="80" align="right"> <? echo $file_no;//$trim_group[$selectResult[csf('file_no')]];?> </td>
                      <td width="80" align="right"> <? echo $ref_no;//$trim_group[$selectResult[csf('grouping')]];?> </td>
                    <td width="100">
					<? echo $trim_group[$selectResult[csf('trim_group')]];?> 
                    <input type="hidden" id="txttrimcostid_<? echo $i;?>" value="<? echo $selectResult[csf('wo_pre_cost_trim_cost_dtls')];?>" readonly/> 
                    <input type="hidden" id="txttrimgroup_<? echo $i;?>" value="<? echo $selectResult[csf('trim_group')];?>" readonly/>
                    </td>
                  
                    <td width="80" align="right">
                    <?
					$tot_req_qty+=$req_qnty;
					$req_amount=$req_qnty*$precost_rate;
					?>
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i;?>" value="<? echo $req_qnty;?>"  readonly  />
                    <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqamount_<? echo $i;?>" value="<? echo $req_amount;?>"  readonly  />
                    <input type="hidden" id="preconsamt_<? echo $i;?>"  value="<? echo $selectResult[csf('pre_amt')];?>" readonly />
                    </td>
                    <td width="80">
                    <?
					echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];
					?>
                    <input type="hidden" id="txtuom_<? echo $i;?>" value="<? echo $sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]; ?>" readonly />
                    </td>
                    <td width="80" align="right">
					<? 
					$cu_woq=$selectResult[csf('cu_woq')]-$selectResult[csf('wo_qnty')];
					$tot_cu_woq+=$cu_woq;
					$cu_amount=$selectResult[csf('cu_amount')]-$selectResult[csf('amount')];
					?>
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i;?>" value="<? echo $cu_woq;?>"  readonly  />
                     <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuamount_<? echo $i;?>" value="<? echo $cu_amount;?>"  readonly  />
                    
                    </td>
                    <td width="80" align="right">
					<? 
					$bal_woq=def_number_format($req_qnty-$cu_woq,5,"");
					$tot_bal_woq+=$bal_woq;
					?>
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i;?>" value="<? echo $bal_woq;?>"  readonly  />
                    </td>
                    <td width="100" align="right">
					<?  echo create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", $selectResult[csf("sensitivity")], "set_cons_break_down($i),copy_value(this.value,'cbocolorsizesensitive_',$i)","","" ); ?>
                    </td>
                    <td width="80" align="right">
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo $selectResult[csf('wo_qnty')];?>" onClick="open_consumption_popup('requires/trims_booking_controller_v2.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<? echo $i;?>',<? echo $i;?>)"     />
                    </td>
                     <td width="80" align="right">
                     <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtexchrate_<? echo $i;?>" value="<?  echo $selectResult[csf('exchange_rate')];?>" readonly />
                     
                    </td>
                    <td width="80" align="right">
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo number_format($selectResult[csf('rate')],4);?>"   onChange="calculate_amount(<? echo $i; ?>)" />
                    <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i;?>" value="<? echo number_format($precost_rate,4);?>" readonly />

                    </td>
                    <td width="80" align="right">
                  <? $total_amount+=$selectResult[csf('amount')];?>
                   <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo def_number_format($selectResult[csf('amount')],5,"");?>"  readonly  />
                    </td>
                    <td width="" align="right">
                    <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo def_number_format($selectResult[csf('amount')],5,"");?>"  readonly  />
                    <input type="text"   style="width:70%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i;?>"  class="datepicker" value="<? echo change_date_format($selectResult[csf('delivery_date')],"dd-mm-yyyy","-"); ?>"  readonly  /> 
                    <?
					/*if($db_type==0)
					{
					$cons_break_down=$selectResult[csf('cons_break_down')]; 
					}
					if($db_type==2)
					{
					$cons_break_down=$selectResult[csf('cons_break_down')]->load();
					}*/
					?>
                    <input type="hidden" id="consbreckdown_<? echo $i;?>"  value="<? echo $selectResult[csf('cons_break_down')];  ?>"/> 
                    </td>
                    </tr>
	   <?
	   $i++;
					}
       ?>
	</tbody>
    </table>
    </div>
    <table width="1290" class="rpt_table" border="0" rules="all">
    <tfoot>
                    <th width="40"></th>
                    <th width="100"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="100"></th>
                    <th width="80"><? echo number_format($tot_req_qty,4) ?></th>
                    <th width="80"></th>
                    <th width="80"><? echo number_format($tot_cu_woq,4)?></th>
                    <th width="80"><? echo number_format($tot_bal_woq,4)?></th>
                    <th width="100"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="80" id="value_total_amount"><input type="text" id="tot_amount" value="<? echo  number_format($total_amount,2); ?>" style="width:80px; text-align:right" readonly/><? // echo number_format($total_amount,2);?></th>
                    <th width=""><input type="hidden" id="saved_tot_amount" value="<? echo  number_format($total_amount,2); ?>" style="width:80px; text-align:right" readonly/></th>               
   </tfoot>
   </table>
   
<?
}


if ($action=="show_trim_booking_without_precost")
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
	 
	 $booking_month=0;
	 if($cbo_booking_month<10)
	 {
		 $booking_month.=$cbo_booking_month;
	 }
	 else
	 {
		$booking_month=$cbo_booking_month; 
	 }
	$start_date=$cbo_booking_year."-".$booking_month."-01";
	$end_date=$cbo_booking_year."-".$booking_month."-".cal_days_in_month(CAL_GREGORIAN, $booking_month, $cbo_booking_year);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table" >
                <thead>
                    <th width="40">SL</th>
                    <th width="100">Ord. No</th>
                    <th width="100">Trims Group</th>
                    <th width="80">UOM</th>
                    
                    <th width="100">Sensitivity</th>
                    <th width="80">WOQ</th>
                    <th width="80">Exch.Rate</th>
                    <th width="80">Rate</th>
                    <th width="80">Amount</th>
                    <th width="">Delv. Date</th>
                </thead>
            </table>
            <div style="width:880px; overflow-y:scroll; max-height:350px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="863" class="rpt_table" id="tbl_list_search" >
       <tbody>
       <?
					$sql="select
					id,
					job_no,
					po_break_down_id,
					booking_no ,	
					booking_type,
					trim_group,
					uom,
					sensitivity,
					wo_qnty,
					exchange_rate,
					rate,
					amount,
					cons_break_down,
					delivery_date
					from  wo_booking_dtls where booking_no=$txt_booking_no
					";

					$i=1;
					$total_amount=0;
                    $nameArray=sql_select( $sql );
                    foreach ($nameArray as $selectResult)
                    {
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						$country_arr=return_library_array("select country_id, country_id from wo_po_color_size_breakdown   WHERE po_break_down_id=".$selectResult[csf('po_break_down_id')]." and  status_active=1 and is_deleted=0 group by country_id","country_id","country_id");
						$country_id_string=implode(",", $country_arr);
						
	   ?>
       <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                    <td width="40"><? echo $i;?></td>
                    <td width="100">
					<? echo $po_number[$selectResult[csf('po_break_down_id')]];?> 
                    <input type="hidden" id="txtbookingid_<? echo $i;?>" value="<? echo $selectResult[csf('id')];?>" readonly/>
                    <input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $selectResult[csf('po_break_down_id')];?>" readonly/>
                    <input type="hidden" id="txtcountry_<? echo $i;?>"  value="<? echo $country_id_string ?>" readonly />
                    <input type="hidden" id="txtdesc_<? echo $i;?>"  value="" readonly />
                     <input type="hidden" id="txtbrandsup_<? echo $i;?>"  value="" readonly />
                    </td>
                    <td width="100">
					<? echo $trim_group[$selectResult[csf('trim_group')]];?> 
                    <input type="hidden" id="txttrimcostid_<? echo $i;?>" value="" readonly/> 
                    <input type="hidden" id="txttrimgroup_<? echo $i;?>" value="<? echo $selectResult[csf('trim_group')];?>" readonly/>
                    <input type="hidden"id="txtreqqnty_<? echo $i;?>" value=""  readonly  />
                    <input type="hidden" id="txtreqamount_<? echo $i;?>" value=""  readonly  />
                    <input type="hidden" id="txtcuwoq_<? echo $i;?>" value=""  readonly  />
                    <input type="hidden"  id="txtcuamount_<? echo $i;?>" value=""  readonly  />
                    <input type="hidden" id="txtbalwoq_<? echo $i;?>" value=""  readonly  />
                    </td>
                    <td width="80">
                    <?
					echo $unit_of_measurement[$selectResult[csf('uom')]];
					?>
                    <input type="hidden" id="txtuom_<? echo $i;?>" value="<? echo $selectResult[csf('uom')]; ?>" readonly />
                    </td>
                    
                    <td width="100" align="right">
					<?  echo create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", $selectResult[csf("sensitivity")], "set_cons_break_down($i)","","" ); ?>
                    </td>
                    <td width="80" align="right">
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo $selectResult[csf('wo_qnty')];?>" onClick="open_consumption_popup('requires/trims_booking_controller_v2.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<? echo $i;?>',<? echo $i;?>)"     />
                    </td>
                     <td width="80" align="right">
                     <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtexchrate_<? echo $i;?>" value="<? echo $selectResult[csf('exchange_rate')];?>" readonly />
                     
                    </td>
                    <td width="80" align="right">
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo number_format($selectResult[csf('rate')],4);?>"   onChange="calculate_amount(<? echo $i; ?>)" />
                    <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i;?>" value="<? echo number_format($selectResult[csf('precost_rate')],4);?>" readonly />

                    </td>
                    <td width="80" align="right">
                  <? $total_amount+=$selectResult[csf('amount')];?>
                   <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo def_number_format($selectResult[csf('amount')],5,"");?>"  readonly  />
                    </td>
                    <td width="" align="right">
                    <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo def_number_format($selectResult[csf('amount')],5,"");?>"  readonly  />
                    <input type="text"   style="width:70%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i;?>"  class="datepicker" value="<? echo change_date_format($selectResult[csf('delivery_date')],"dd-mm-yyyy","-"); ?>"  readonly  /> 
                    <input type="hidden" id="consbreckdown_<? echo $i;?>"  value="<? echo $selectResult[csf('cons_break_down')];  ?>"/> 
                    </td>
                    </tr>
	   <?
	   $i++;
					}
       ?>
	</tbody>
    </table>
    </div>
    <table width="880" class="rpt_table" border="0" rules="all">
    <tfoot>
                    <th width="40">SL</th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="80"></th>
                    <th width="100"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="80"> <input type="hidden" id="saved_tot_amount" value="0" style="width:80px; text-align:right" readonly/></th>
                    <th width="80" id="value_total_amount"><input type="text" id="tot_amount" value="<? echo  $total_amount; ?>" style="width:80px"/><? //echo number_format($total_amount,2);?></th>
                    <th width=""></th>  
                    
                           
   </tfoot>
   </table>
   
<?
}

if ($action == "consumption_popup")
{
  echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode,'','');
?>
     
<script>
var str_gmtssizes = [<? echo substr(return_library_autocomplete( "select size_name from  lib_size", "size_name"  ), 0, -1); ?>];
var str_diawidth = [<? echo substr(return_library_autocomplete( "select color_name from lib_color", "color_name"  ), 0, -1); ?>];
function poportionate_qty(qty)
{
	var po_qty=document.getElementById('po_qty').value;
	var txtwoq_qty=document.getElementById('txtwoq_qty').value;
    var rowCount = $('#tbl_consmption_cost tr').length-2;
	for(var i=1; i<=rowCount; i++)
	{
	// var pcs=$('#pcs_'+i).val();
	 var pcs=$('#pcsset_'+i).val();
	 var txtwoq_cal =number_format_common((txtwoq_qty/po_qty) * (pcs),5,0);
	 $('#qty_'+i).val(txtwoq_cal);
	 calculate_requirement(i)
	}
	set_sum_value( 'qty_sum', 'qty_' )
}

function calculate_requirement(i)
{
	var process_loss_method_id=document.getElementById('process_loss_method_id').value;
	var cons=(document.getElementById('qty_'+i).value)*1;
	var processloss=(document.getElementById('excess_'+i).value)*1;
	    var WastageQty='';
		if(process_loss_method_id==1)
		{
			WastageQty=cons+cons*(processloss/100);
		}
		else if(process_loss_method_id==2)
		{
			var devided_val = 1-(processloss/100);
			var WastageQty=parseFloat(cons/devided_val);
		}
		else
		{
			WastageQty=0;
		}
		WastageQty= number_format_common( WastageQty, 5, 0) ;	
		document.getElementById('woqny_'+i).value= WastageQty;
		set_sum_value( 'woqty_sum', 'woqny_' )
		calculate_amount(i)
		
		
}

function set_sum_value(des_fil_id,field_id)
{
	if(des_fil_id=='qty_sum')
	{
	var ddd={dec_type:5,comma:0,currency:0};
	}
	
	if(des_fil_id=='excess_sum')
	{
	var ddd={dec_type:5,comma:0,currency:0};
	}
	
	if(des_fil_id=='woqty_sum')
	{
	var ddd={dec_type:5,comma:0,currency:0};
	}
	
	
	if(des_fil_id=='amount_sum')
	{
	var ddd={dec_type:5,comma:0,currency:0};
	}
	
	if(des_fil_id=='pcs_sum')
	{
	var ddd={dec_type:6,comma:0};
	}
	var rowCount = $('#tbl_consmption_cost tr').length-2;
	math_operation( des_fil_id, field_id, '+', rowCount,ddd );
}

function copy_value(value,field_id,i)
{
	  var copy_val=document.getElementById('copy_val').checked;
	  var gmtssizesid=document.getElementById('gmtssizesid_'+i).value;
	  var pocolorid=document.getElementById('pocolorid_'+i).value;
	  var rowCount = $('#tbl_consmption_cost tr').length-2;
	  if(copy_val==true)
	  {
	  for(var j=i; j<=rowCount; j++)
		{
		  
		  if(field_id=='des_')
		  {
			if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
			{
			document.getElementById(field_id+j).value=value;
			//calculate_requirement(j) 
			}
		  }
		  if(field_id=='brndsup_')
		  {
			if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
			{
			document.getElementById(field_id+j).value=value;
			//calculate_requirement(j) 
			}
		  }
		  if(field_id=='itemcolor_')
		  {
			if( pocolorid==document.getElementById('pocolorid_'+j).value)
			{
			document.getElementById(field_id+j).value=value;
			//calculate_requirement(j) 
			}
		  }
		  
		  if(field_id=='itemsizes_')
		  {
			if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
			{
			document.getElementById(field_id+j).value=value;
			//calculate_requirement(j) 
			}
		  }
		  if(field_id=='qty_')
		  {
			document.getElementById(field_id+j).value=value;
			calculate_requirement(j) 
			set_sum_value( 'qty_sum', 'qty_'  );
		  }
		  if(field_id=='excess_')
		  {
			document.getElementById(field_id+j).value=value;
			calculate_requirement(j)  
		  }
		  if(field_id=='rate_')
		  {
			document.getElementById(field_id+j).value=value;
			calculate_amount(j)  
		  }
		}
	  }
}

function calculate_amount(i) 
{
	var rate=(document.getElementById('rate_'+i).value)*1;
	var woqny=(document.getElementById('woqny_'+i).value)*1;
	var amount=number_format_common((rate*woqny),5,0);
	document.getElementById('amount_'+i).value=amount;
	set_sum_value( 'amount_sum', 'amount_' );
	calculate_avg_rate()
	
}
function calculate_avg_rate()
{
	var woqty_sum=document.getElementById('woqty_sum').value;
	var amount_sum=document.getElementById('amount_sum').value;
	var avg_rate=number_format_common((amount_sum/woqty_sum),5,0);
	//alert(avg_rate);
	document.getElementById('rate_sum').value=avg_rate;
	//document.getElementById('txt_quantity').value=woqty_sum;
	//document.getElementById('txt_avg_price').value=avg_rate;
	//document.getElementById('txt_amount').value=amount_sum;
}


function js_set_value()
{
	var row_num=$('#tbl_consmption_cost tbody tr').length;
	var cons_breck_down="";
	for(var i=1; i<=row_num; i++)
	{
		var pocolorid=$('#pocolorid_'+i).val()
			if(pocolorid=='')
			{
				 pocolorid=0;
			}
		var gmtssizesid=$('#gmtssizesid_'+i).val()
			if(gmtssizesid=='')
			{
				 gmtssizesid=0;
			}
			
		var des=$('#des_'+i).val()
			if(des=='')
			{
				 des=0;
			}
		var brndsup=$('#brndsup_'+i).val();
			if(brndsup=='')
			{
				 brndsup=0;
			}
			
		var itemcolor=$('#itemcolor_'+i).val()
			if(itemcolor=='')
			{
				 itemcolor=0;
			}
		var itemsizes=$('#itemsizes_'+i).val()
			if(itemsizes=='')
			{
				 itemsizes=0;
			}
			
		var qty=$('#qty_'+i).val()
			if(qty=='')
			{
				 qty=0;
			}
	    var excess=$('#excess_'+i).val()
			if(excess=='')
			{
				 excess=0;
			}
			
	    var woqny=$('#woqny_'+i).val()
			if(woqny=='')
			{
				 woqny=0;
			}
			
		var rate=$('#rate_'+i).val()
			if(rate=='')
			{
				 rate=0;
			}
			
		var amount=$('#amount_'+i).val()
			if(amount=='')
			{
				 amount=0;
			}
			
			var pcs=$('#pcs_'+i).val()
			if(pcs=='')
			{
				 pcs=0;
			}
			
			var colorsizetableid=$('#colorsizetableid_'+i).val()
			if(colorsizetableid=='')
			{
				 colorsizetableid=0;
			}
			
			var updateid=$('#updateid_'+i).val()
			if(updateid=='')
			{
				 updateid=0;
			}
			
			
			
			
			if(cons_breck_down=="")
			{
				cons_breck_down+=pocolorid+'_'+gmtssizesid+'_'+des+'_'+brndsup+'_'+itemcolor+'_'+itemsizes+'_'+qty+'_'+excess+'_'+woqny+'_'+rate+'_'+amount+'_'+pcs+'_'+colorsizetableid;
			}
			else
			{
				cons_breck_down+="__"+pocolorid+'_'+gmtssizesid+'_'+des+'_'+brndsup+'_'+itemcolor+'_'+itemsizes+'_'+qty+'_'+excess+'_'+woqny+'_'+rate+'_'+amount+'_'+pcs+'_'+colorsizetableid;
			}
	}
    document.getElementById('cons_breck_down').value=cons_breck_down;
	parent.emailwindow.hide();
}
</script>
</head>
<body>
<?
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
	 
	  if($txt_country=="")
	 {
		 $txt_country_cond="";
	 }
	 else
	 {
		 $txt_country_cond ="and c.country_id in ($txt_country)";
	 }
	?>
   <div align="center" style="width:1050px;" >
<fieldset>
        	<form id="consumptionform_1" autocomplete="off">
            
			<?
			/*$pcs_value=0;
			$set_item_ratio=return_field_value("set_item_ratio", "wo_po_details_mas_set_details", "id='$txt_po_id'  and gmts_item_id='$cbogmtsitem'");
			if($set_item_ratio==0 || $set_item_ratio=="")
			{
				$set_item_ratio=1;
			}*/
			$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=18 and item_category_id=4 and status_active=1 and is_deleted=0");
			
			//$sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$txt_po_id'  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty,c.item_number_id");
			$sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$txt_po_id'  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty");//,c.item_number_id
			
			//echo "select sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$txt_po_id'  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty";
			
			
	        list($sql_po_qty_row)=$sql_po_qty;
	        $po_qty=$sql_po_qty_row[csf('order_quantity_set')];
		   //$po_qty=return_field_value("po_quantity", "wo_po_break_down", "id='$txt_po_id'");
            ?>
               
            	<table width="1050" cellspacing="0" class="rpt_table" border="0" id="tbl_consmption_cost" rules="all">
                	<thead>
                         <tr>
                        	<th width="40" colspan="14">
                             <input type="hidden" id="cons_breck_down" name="cons_breck_down" value="<? echo $po_qty; ?>"/>
                            <input type="hidden" id="txtwoq" value="<? echo $txt_req_quantity;?>"/>
                            Wo Qty:<input type="text" id="txtwoq_qty" class="text_boxes_numeric" onBlur="poportionate_qty(this.value)"/>
                            <b>Copy</b> : <input type="checkbox" id="copy_val" name="copy_val" checked/>
                            <input type="hidden" id="process_loss_method_id" name="process_loss_method_id" value="<? echo $process_loss_method; ?>"/>
                            <input type="hidden" id="po_qty" name="po_qty" value="<? echo $po_qty; ?>"/>
                           </th>
                        </tr>
                    	<tr>
                        	<th width="40">SL</th><th  width="100">Gmts. Color</th><th  width="70">Gmts. sizes</th><th  width="100">Description</th><th  width="100">Brand/Sup Ref</th><th  width="100">Item Color</th><th width="80">Item Sizes</th><th width="70"> Wo Qty</th><th width="40">Excess %</th><th width="70">WO Qty.</th><th width="40">Rate</th><th width="50">Amount</th><th width="50">RMG Qnty</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					
					$booking_data_arr=array();
					
					$booking_data=sql_select("select id,wo_trim_booking_dtls_id,description,brand_supplier,item_color,item_size,cons,process_loss_percent,requirment,rate, 	amount,pcs,color_size_table_id  from wo_trim_book_con_dtls where wo_trim_booking_dtls_id='$txt_update_dtls_id'");
					//echo "select id,wo_trim_booking_dtls_id,description,brand_supplier,item_color,item_size,cons,process_loss_percent,requirment,rate, 	amount,pcs,color_size_table_id  from wo_trim_book_con_dtls where wo_trim_booking_dtls_id='$txt_update_dtls_id'";
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
					//print_r($booking_data_arr);
					$gmt_color_edb="";
					$item_color_edb="";
					$gmt_size_edb="";
					$item_size_edb="";
					if($cbo_colorsizesensitive==1)
					{
						
					 $sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$txt_po_id'  $txt_country_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id order by b.id, c.color_number_id";
						
					 $gmt_size_edb="disabled";
					 $item_size_edb="disabled";
					}
					else if($cbo_colorsizesensitive==2)
					{
						
					$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.size_number_id,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$txt_po_id'  $txt_country_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.size_number_id order by b.id,c.size_number_id";
						
					 $gmt_color_edb="disabled";
					 $item_color_edb="disabled";
					}
					else if($cbo_colorsizesensitive==3)
					{
					$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$txt_po_id'  $txt_country_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id order by b.id, c.color_number_id";
					 $gmt_size_edb="disabled";
					 $item_size_edb="disabled";
					}
					else if($cbo_colorsizesensitive==4)
					{
					$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$txt_po_id'  $txt_country_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.size_number_id  order by b.id, c.color_number_id,c.size_number_id";
					}
					else
					{
						 $sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$txt_po_id'  $txt_country_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty order by b.id";
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
								//echo $txt_req_quantity."/".$po_qty."*".$row[csf('order_quantity_set')];
								$item_color = $booking_data_arr[$row[csf('color_size_table_id')]][item_color];
								if($item_color==0 || $item_color=="" )
								{
									$item_color = $row[csf('color_number_id')];
								}
								$item_size=$booking_data_arr[$row[csf('color_size_table_id')]][item_size];
								if($item_size==0 || $item_size == "")
								{
								  $item_size=$size_library[$row[csf('size_number_id')]];
								}
								$rate=$booking_data_arr[$row[csf('color_size_table_id')]][rate];
								if($rate==0 || $rate=="")
								{
									$rate=$txt_avg_price;
								}
								$description=$booking_data_arr[$row[csf('color_size_table_id')]][description];
								if($description=="")
								{
									$description=$txt_pre_des;
								}
								$brand_supplier=$booking_data_arr[$row[csf('color_size_table_id')]][brand_supplier];
								if($brand_supplier=="")
								{
									$brand_supplier=$txt_pre_brand_sup;
								}
					?>
                    <tr id="break_1" align="center">
                                    <td>
                                      <? echo $i;?>
                                    </td>
                                    
                                    <td>
                                    <input type="text" id="pocolor_<? echo $i;?>"  name="pocolor_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $color_library[$row[csf('color_number_id')]]; ?>" <? echo  $gmt_color_edb; ?>/>
                                    <input type="hidden" id="pocolorid_<? echo $i;?>"  name="pocolorid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('color_number_id')]; ?>" />
                                    </td>
                                     <td>
                                    <input type="text" id="gmtssizes_<? echo $i;?>"  name="gmtssizes_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $size_library[$row[csf('size_number_id')]]; ?>"<?  echo $gmt_size_edb; ?>/>
                                    <input type="hidden" id="gmtssizesid_<? echo $i;?>"  name="gmtssizesid_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $row[csf('size_number_id')]; ?>">
                                    </td>
                                    <td>
                                    <input type="text" id="des_<? echo $i;?>"  name="des_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $description;?>" onChange="copy_value(this.value,'des_',<? echo $i;?>)" />
                                    </td>
                                    <td>
                                    <input type="text" id="brndsup_<? echo $i;?>"  name="brndsup_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $brand_supplier; ?>" onChange="copy_value(this.value,'brndsup_',<? echo $i;?>)" />

                                    </td>
                                    <td>
                                    <input type="text" id="itemcolor_<? echo $i;?>"  value="<? echo $color_library[$item_color]; ?>"  name="itemcolor_<? echo $i;?>"  class="text_boxes" style="width:95px" onChange="copy_value(this.value,'itemcolor_',<? echo $i;?>)" <? echo $item_color_edb; ?> />    
                                    </td>
                                    <td>
                                    <input type="text" id="itemsizes_<? echo $i;?>"  name="itemsizes_<? echo $i;?>"    class="text_boxes" style="width:70px" onChange="copy_value(this.value,'itemsizes_',<? echo $i;?>)" value="<? echo $item_size; ?>" <? echo $item_size_edb; ?>/>
                                    </td>
                                    <td>
                                    <input type="text" id="qty_<? echo $i;?>" onBlur="validate_sum( <? echo $i; ?> )" onChange="set_sum_value( 'qty_sum', 'qty_' );set_sum_value( 'woqty_sum', 'woqny_' );calculate_requirement(<? echo $i;?>);copy_value(this.value,'qty_',<? echo $i;?>)"  name="qty_<? echo $i;?>" class="text_boxes_numeric" style="width:70px"   placeholder="<? echo $txtwoq_cal; ?>" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][cons]; ?>"/> 
                                    </td>
                                    <td>
                                    <input type="text" id="excess_<? echo $i;?>" onBlur="set_sum_value( 'excess_sum', 'excess_' ) "  name="excess_<? echo $i;?>" class="text_boxes_numeric" style="width:40px" onChange="calculate_requirement(<? echo $i;?>);set_sum_value( 'excess_sum', 'excess_' );set_sum_value( 'woqty_sum', 'woqny_' );copy_value(this.value,'excess_',<? echo $i;?>) " value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]; ?>" disabled/> 
                                    </td>
                                    <td>
                                    <input type="text" id="woqny_<? echo $i;?>" onBlur="set_sum_value( 'woqty_sum', 'woqny_' ) " onChange="set_sum_value( 'woqty_sum', 'woqny_' )"  name="woqny_<? echo $i;?>" class="text_boxes_numeric" style="width:70px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][requirment]; ?>" readonly /> 
                                    </td>
                                    
                                    
                                     <td>
                                    <input type="text" id="rate_<? echo $i;?>"  name="rate_<? echo $i;?>" class="text_boxes_numeric" style="width:40px" onChange="calculate_amount(<? echo $i;?>);set_sum_value( 'amount_sum', 'amount_' );copy_value(this.value,'rate_',<? echo $i;?>) " value="<? echo $rate; ?>" /> 
                                    </td>
                                    <td>
                                    <input type="text" id="amount_<? echo $i;?>"  name="amount_<? echo $i;?>"  onBlur="set_sum_value( 'amount_sum', 'amount_' ) " class="text_boxes_numeric" style="width:50px"  value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][amount]; ?>">
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
                            <th width="82"></th>
                            <th width="70"><input type="text" id="qty_sum" name="qty_sum" class="text_boxes_numeric" style="width:70px"  readonly></th>
                            <th width="40"><input type="text" id="excess_sum"  name="excess_sum" class="text_boxes_numeric" style="width:40px" readonly></th>
                            <th width="70"><input type="text" id="woqty_sum"  name="woqty_sum" class="text_boxes_numeric" style="width:70px" readonly></th>
                            <th width="40"><input type="text" id="rate_sum"  name="rate_sum" class="text_boxes_numeric" style="width:40px" readonly></th>
                            <th width="50"><input type="text" id="amount_sum" name="amount_sum" class="text_boxes_numeric" style="width:50px" readonly></th>
                            <th width="50"><input type="text" id="pcs_sum"    name="pcs_sum" class="text_boxes_numeric" style="width:50px" readonly></th>
                            <th width=""></th>
                        </tr>
                        
                    </tfoot>
                </table>
                 <table width="810" cellspacing="0" class="" border="0" rules="all">
                	 <tr>
                        <td align="center" width="100%"> <input type="button" class="formbutton" value="Close" onClick="js_set_value()"/> </td> 
                    </tr>
                </table>
            </form>
        </fieldset>
        </div>


</body>
<script>
set_sum_value( 'qty_sum', 'qty_' );
set_sum_value( 'woqty_sum', 'woqny_' );
set_sum_value( 'amount_sum', 'amount_' );
set_sum_value( 'pcs_sum', 'pcs_' ); 
calculate_avg_rate();
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if ($action=="set_cons_break_down")
{
	$data=explode("_",$data);
	$garments_nature=$data[0];
	$cbo_company_name=$data[1];
	$txt_job_no=$data[2];
	$cbo_buyer_name=$data[3];
	$txt_po_id=$data[4];
	$cbo_trim_precost_id=$data[5];
	$txt_trim_group_id=$data[6];
	$txt_update_dtls_id=$data[7];
	$cbo_colorsizesensitive=$data[8];
	$txt_req_quantity=$data[9];
	$txt_avg_price=$data[10];
	$txt_country=$data[11];
	$txt_pre_des=$data[12];
	$txt_pre_brand_sup=$data[13];
	$txtbalwoq=$data[14];
	$txt_req_quantity=$txtbalwoq;
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
	
	if($txt_country=="")
	{
		$txt_country_cond="";
	}
	else
	{
		$txt_country_cond ="and c.country_id in ($txt_country)";
	}
	
	$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=18 and item_category_id=4 and status_active=1 and is_deleted=0");
	
	$sql_po_qty=sql_select("select sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$txt_po_id'  $txt_country_cond  and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty");//c.item_number_id;
	list($sql_po_qty_row)=$sql_po_qty;
	$po_qty=$sql_po_qty_row[csf('order_quantity_set')];
	
	$booking_data_arr=array();
	$booking_data=sql_select("select id,wo_trim_booking_dtls_id,description,brand_supplier,item_color,item_size,cons,process_loss_percent,requirment,rate, 	amount,pcs,color_size_table_id  from wo_trim_book_con_dtls where wo_trim_booking_dtls_id='$txt_update_dtls_id'");
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
	
	$gmt_color_edb="";
	$item_color_edb="";
	$gmt_size_edb="";
	$item_size_edb="";
	if($cbo_colorsizesensitive==1)
	{
		$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$txt_po_id'  $txt_country_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id order by b.id, c.color_number_id";
		$gmt_size_edb="disabled";
		$item_size_edb="disabled";
	}
	else if($cbo_colorsizesensitive==2)
	{
	
		$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.size_number_id,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$txt_po_id'  $txt_country_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.size_number_id order by b.id,c.size_number_id";
		$gmt_color_edb="disabled";
		$item_color_edb="disabled";
	}
	else if($cbo_colorsizesensitive==3)
	{
		$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,sum(c.order_quantity) as order_quantity ,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$txt_po_id'  $txt_country_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id order by b.id, c.color_number_id";
		$gmt_size_edb="disabled";
		$item_size_edb="disabled";
	}
	else if($cbo_colorsizesensitive==4)
	{
		$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$txt_po_id'  $txt_country_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty,c.color_number_id,c.size_number_id  order by b.id, c.color_number_id,c.size_number_id";
	}
	else
	{
		$sql="select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$txt_po_id'  $txt_country_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by  b.id, b.po_number,b.po_quantity,a.total_set_qnty order by b.id";
	}
	$cons_breck_down="";
	$data_array=sql_select($sql); 
	if ( count($data_array)>0)
	{
		$i=0;
		foreach( $data_array as $row )
		{
			$color_number_id=$row[csf('color_number_id')];
			if($color_number_id=="")
			{
				$color_number_id=0;
			}
			
			$size_number_id=$row[csf('size_number_id')];
			if($size_number_id=="")
			{
				$size_number_id=0;
			}
			
			$description=$txt_pre_des;
			if($description=="")
			{
				$description=0;
			}
			
			$brand_supplier=$txt_pre_brand_sup;
			if($brand_supplier=="")
			{
				$brand_supplier=0;
			}
			
			$item_color=$color_library[$row[csf('color_number_id')]];
			if($item_color=="")
			{
				$item_color=0;
			}
			
			$item_size=$size_library[$row[csf('size_number_id')]];
			if($item_size=="")
			{
				$item_size=0;
			}
			$excess=0;
			$txtwoq_cal =def_number_format(($txt_req_quantity/$po_qty) * ($row[csf('order_quantity_set')]),5,"");
			$amount=def_number_format($txtwoq_cal*$txt_avg_price,5,"");
			
			$pcs=$row[csf('order_quantity_set')];
			if($pcs=="")
			{
				$pcs=0;
			}
			
			$colorsizetableid=$row[csf('color_size_table_id')];
			if($colorsizetableid=="")
			{
				$colorsizetableid=0;
			}
			
			if($cons_breck_down=="")
			{
				$cons_breck_down.=$color_number_id.'_'.$size_number_id.'_'.$description.'_'.$brand_supplier.'_'.$item_color.'_'.$item_size.'_'.$txtwoq_cal.'_'.$excess.'_'.$txtwoq_cal.'_'.$txt_avg_price.'_'.$amount.'_'.$pcs.'_'.$colorsizetableid;
			}
			else
			{
				$cons_breck_down.="__".$color_number_id.'_'.$size_number_id.'_'.$description.'_'.$brand_supplier.'_'.$item_color.'_'.$item_size.'_'.$txtwoq_cal.'_'.$excess.'_'.$txtwoq_cal.'_'.$txt_avg_price.'_'.$amount.'_'.$pcs.'_'.$colorsizetableid;
			}
			
		}
		echo $cons_breck_down;
	}
}




if ($action=="consumption_popup1")
{
  echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode,'','');
  extract($_REQUEST);
?>
     
<script>
var str_gmtssizes = [<? echo substr(return_library_autocomplete( "select size_name from  lib_size", "size_name"  ), 0, -1); ?>];
var str_diawidth = [<? echo substr(return_library_autocomplete( "select color_name from lib_color", "color_name"  ), 0, -1); ?>];
function copy_value(value,field_id,i)
{
	  var copy_val=document.getElementById('copy_val').checked;
	  var gmtssizesid=document.getElementById('gmtssizesid_'+i).value;
	  var pocolorid=document.getElementById('pocolorid_'+i).value;
	  var rowCount = $('#tbl_consmption_cost tr').length-1;
	  if(copy_val==true)
	  {
	  for(var j=i; j<=rowCount; j++)
		{
		  if(field_id=='diawidth_')
		  {
			if( pocolorid==document.getElementById('pocolorid_'+j).value)
			{
			document.getElementById(field_id+j).value=value;
			calculate_requirement(j) 
			}
		  }
		  if(field_id=='itemsizes_')
		  {
			if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value)
			{
			document.getElementById(field_id+j).value=value;
			calculate_requirement(j) 
			}
		  }
		  if(field_id=='cons_')
		  {
			document.getElementById(field_id+j).value=value;
			calculate_requirement(j) 
			set_sum_value( 'cons_sum', 'cons_'  );
		  }
		  if(field_id=='processloss_')
		  {
			document.getElementById(field_id+j).value=value;
			calculate_requirement(j)  
		  }
		}
	  }
}
function fn_delete_down_tr(rowNo,table_id) 
{   
	if(table_id=='tbl_consmption_cost')
	{
		var numRow = $('table#tbl_consmption_cost tbody tr').length; 
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_consmption_cost tbody tr:last').remove();
			$('#tbl_msmnt_cost tbody tr:last').remove();
		}
		/*else
		{
																																																																																																																																																																																																																																																																																																																																									reset_form('','','txtordernumber_'+rowNo+'*txtorderqnty_'+rowNo+'*txtordervalue_'+rowNo+'*txtattachedqnty_'+rowNo+'*txtattachedvalue_'+rowNo+'*txtstyleref_'+rowNo+'*txtitemname_'+rowNo+'*txtjobno_'+rowNo+'*hiddenwopobreakdownid_'+rowNo+'*hiddenunitprice_'+rowNo+'*totalOrderqnty*totalOrdervalue*totalAttachedqnty*totalAttachedvalue');
		} */
		 //set_all_onclick();
		  set_sum_value( 'cons_sum', 'cons_'  );
		  set_sum_value( 'processloss_sum', 'processloss_'  );
		  set_sum_value( 'requirement_sum', 'requirement_');
          set_sum_value( 'pcs_sum', 'pcs_');
	}
}

function set_sum_value(des_fil_id,field_id)
{
	if(des_fil_id=='cons_sum')
	{
	var ddd={dec_type:5,comma:0,currency:0};
	}
	if(des_fil_id=='processloss_sum')
	{
	var ddd={dec_type:1,comma:0,currency:1};
	}
	
	if(des_fil_id=='requirement_sum')
	{
	var ddd={dec_type:1,comma:0,currency:1};
	}
	if(des_fil_id=='pcs_sum')
	{
	var ddd={dec_type:6,comma:0};
	}
	var rowCount = $('#tbl_consmption_cost tr').length-1;
	math_operation( des_fil_id, field_id, '+', rowCount,ddd );
}

function validate_sum(i)
{
	var cons_sum= (document.getElementById('cons_sum').value)*1;
	var txtwoq= (document.getElementById('txtwoq').value)*1;
	if(cons_sum > txtwoq)
	{
		alert("Breakdown Qnty Exceeds The WO.Qnty")	;
		document.getElementById('cons_'+i).value="";
	}
}
	
function js_set_value()
{
	var rowCount = $('#tbl_consmption_cost tr').length-1;
	var cons_breck_down="";
	for(var i=1; i<=rowCount; i++)
	{
		var ponoid=$('#ponoid_'+i).val()
			if(ponoid=='')
			{
				 ponoid=0;
			}
		var pocolorid=$('#pocolorid_'+i).val()
			if(pocolorid=='')
			{
				 pocolorid=0;
			}
		var gmtssizesid=$('#gmtssizesid_'+i).val()
			if(gmtssizesid=='')
			{
				 gmtssizesid=0;
			}
		var diawidth=$('#diawidth_'+i).val()
			if(diawidth=='')
			{
				 diawidth=0;
			}
		var itemsizes=$('#itemsizes_'+i).val();
			if(itemsizes=='')
			{
				 itemsizes=0;
			}
		var cons=$('#cons_'+i).val()
			if(cons=='')
			{
				 cons=0;
			}
		var processloss=$('#processloss_'+i).val()
			if(processloss=='')
			{
				 processloss=0;
			}
		var requirement=$('#requirement_'+i).val()
			if(requirement=='')
			{
				 requirement=0;
			}
	    var pcs=$('#pcs_'+i).val()
			if(pcs=='')
			{
				 pcs=0;
			}
	    var colorsizetableid=$('#colorsizetableid_'+i).val()
			if(colorsizetableid=='')
			{
				 colorsizetableid=0;
			}
			
			if(cons_breck_down=="")
			{
				cons_breck_down+=ponoid+'_'+pocolorid+'_'+gmtssizesid+'_'+diawidth+'_'+itemsizes+'_'+cons+'_'+processloss+'_'+requirement+'_'+pcs+'_'+colorsizetableid;
			}
			else
			{
				cons_breck_down+="__"+ponoid+'_'+pocolorid+'_'+gmtssizesid+'_'+diawidth+'_'+itemsizes+'_'+cons+'_'+processloss+'_'+requirement+'_'+pcs+'_'+colorsizetableid;
			}
	}

    document.getElementById('cons_breck_down').value=cons_breck_down;
	parent.emailwindow.hide();
}





function calculate_requirement(i)
{
	var process_loss_method_id=document.getElementById('process_loss_method_id').value;
	var cons=(document.getElementById('cons_'+i).value)*1;
	var processloss=(document.getElementById('processloss_'+i).value)*1;
	    var WastageQty='';
		if(process_loss_method_id==1)
		{
			WastageQty=cons+cons*(processloss/100);
		}
		else if(process_loss_method_id==2)
		{
			var devided_val = 1-(processloss/100);
			var WastageQty=parseFloat(cons/devided_val);
		}
		else
		{
			WastageQty=0;
		}
		WastageQty= number_format_common( WastageQty, 1, 0) ;	
		document.getElementById('requirement_'+i).value= WastageQty;
		set_sum_value( 'requirement_sum', 'requirement_' )
		
}

function poportionate_qty(qty)
{
	var po_qty=document.getElementById('po_qty').value;
	var txtwoq_qty=document.getElementById('txtwoq_qty').value;
	var txtwoq= (document.getElementById('txtwoq').value)*1;
	if(txtwoq_qty > txtwoq)
	{
		alert("Breakdown Qnty Exceeds The WO.Qnty")	;
		return;
	}

    var rowCount = $('#tbl_consmption_cost tr').length-1;
	for(var i=1; i<=rowCount; i++)
	{
	 var pcs=$('#pcs_'+i).val();
	 var txtwoq_cal =number_format_common((txtwoq_qty/po_qty) * (pcs),5,0);
	 $('#cons_'+i).val(txtwoq_cal);
	}
	set_sum_value( 'cons_sum', 'cons_' )
}
</script>
</head>
<body>
<div align="center" style="width:100%;" >
<fieldset>
            <legend><? //echo $body_part_id.'.'.$body_part[$body_part_id].'   Costing '.$costing_per[$cbo_costing_per] ;?></legend>
        	<form id="consumptionform_1" autocomplete="off">
            <input type="text" id="txtwoq" value="<? echo $txtwoq;?>"/>
            Wo Qty:<input type="text" id="txtwoq_qty" class="text_boxes_numeric" onBlur="poportionate_qty(this.value)"/>
            <input type="hidden" id="cons_breck_down" value=""/>
            <b>Copy</b> : <input type="checkbox" id="copy_val" name="copy_val" checked/>
			<?
			/*$pcs_value=0;
			$set_item_ratio=return_field_value("set_item_ratio", "wo_po_details_mas_set_details", "id='$po_id'  and gmts_item_id='$cbogmtsitem'");
			if($set_item_ratio==0 || $set_item_ratio=="")
			{
				$set_item_ratio=1;
			}*/
			$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_id  and variable_list=18 and item_category_id=4 and status_active=1 and is_deleted=0");
		    $po_qty=return_field_value("po_quantity", "wo_po_break_down", "id='$po_id'");

            ?>

           <input type="hidden" id="process_loss_method_id" name="process_loss_method_id" value="<? echo $process_loss_method; ?>"/>
                      <input type="hidden" id="po_qty" name="po_qty" value="<? echo $po_qty; ?>"/>

            	<table width="1050" cellspacing="0" class="rpt_table" border="0" id="tbl_consmption_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="40">SL</th><th  width="100">Gmts. Color</th><th  width="70">Gmts. sizes</th><th  width="100">Description</th><th  width="100">Brand/Supplier Ref.</th><th  width="110">Item Color</th><th width="80">Item Sizes</th><th width="70"> WOQ </th><th width="40">Excess %</th><th width="70">Requirment </th><th width="40">Rate</th><th width="50">Amount</th><th width="50">RMG Qnty</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					//$po_no_library=return_library_array( "select id,po_number from wo_po_break_down where id ='$po_id'", "id", "po_number"  );
					$data_array=explode("__",$cons_breck_downn);
					if($data_array[0]=="")
					{
						$data_array=array();
					}
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$data=explode('_',$row);
							$i++;
							?>
                             <tr id="break_1" align="center">
                                    <td>
                                      <? echo $i;?>
                                    </td>
                                    
                                    <td>
                                    <input type="text" id="pocolor_<? echo $i;?>"  name="pocolor_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $color_library[$row[csf('color_number_id')]]; ?>" <? echo  $gmt_color_edb; ?>/>
                                    <input type="hidden" id="pocolorid_<? echo $i;?>"  name="pocolorid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('color_number_id')]; ?>" />
                                    </td>
                                     <td>
                                    <input type="text" id="gmtssizes_<? echo $i;?>"  name="gmtssizes_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $size_library[$row[csf('size_number_id')]]; ?>"<?  echo $gmt_size_edb; ?>/>
                                    <input type="hidden" id="gmtssizesid_<? echo $i;?>"  name="gmtssizesid_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $row[csf('size_number_id')]; ?>">
                                    </td>
                                    <td>
                                    <input type="text" id="des_<? echo $i;?>"  name="des_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $description;?>" onChange="copy_value(this.value,'des_',<? echo $i;?>)" />
                                    </td>
                                    <td>
                                    <input type="text" id="brndsup_<? echo $i;?>"  name="brndsup_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $brand_supplier; ?>" onChange="copy_value(this.value,'brndsup_',<? echo $i;?>)" />

                                    </td>
                                    <td>
                                    <input type="text" id="itemcolor_<? echo $i;?>"  value="<? echo $color_library[$item_color]; ?>"  name="itemcolor_<? echo $i;?>"  class="text_boxes" style="width:95px" onChange="copy_value(this.value,'itemcolor_',<? echo $i;?>)" <? echo $item_color_edb; ?> />    
                                    </td>
                                    <td>
                                    <input type="text" id="itemsizes_<? echo $i;?>"  name="itemsizes_<? echo $i;?>"    class="text_boxes" style="width:70px" onChange="copy_value(this.value,'itemsizes_',<? echo $i;?>)" value="<? echo $item_size; ?>" <? echo $item_size_edb; ?>/>
                                    </td>
                                    <td>
                                    <input type="text" id="qty_<? echo $i;?>" onBlur="validate_sum( <? echo $i; ?> )" onChange="set_sum_value( 'qty_sum', 'qty_' );set_sum_value( 'woqty_sum', 'woqny_' );calculate_requirement(<? echo $i;?>);copy_value(this.value,'qty_',<? echo $i;?>)"  name="qty_<? echo $i;?>" class="text_boxes_numeric" style="width:70px"   placeholder="<? echo $txtwoq_cal; ?>" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][cons]; ?>"/> 
                                    </td>
                                    <td>
                                    <input type="text" id="excess_<? echo $i;?>" onBlur="set_sum_value( 'excess_sum', 'excess_' ) "  name="excess_<? echo $i;?>" class="text_boxes_numeric" style="width:40px" onChange="calculate_requirement(<? echo $i;?>);set_sum_value( 'excess_sum', 'excess_' );set_sum_value( 'woqty_sum', 'woqny_' );copy_value(this.value,'excess_',<? echo $i;?>) " value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]; ?>" /> 
                                    </td>
                                    <td>
                                    <input type="text" id="woqny_<? echo $i;?>" onBlur="set_sum_value( 'woqty_sum', 'woqny_' ) " onChange="set_sum_value( 'woqty_sum', 'woqny_' )"  name="woqny_<? echo $i;?>" class="text_boxes_numeric" style="width:70px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][requirment]; ?>" readonly /> 
                                    </td>
                                    
                                    
                                     <td>
                                    <input type="text" id="rate_<? echo $i;?>"  name="rate_<? echo $i;?>" class="text_boxes_numeric" style="width:40px" onChange="calculate_amount(<? echo $i;?>);set_sum_value( 'amount_sum', 'amount_' );copy_value(this.value,'rate_',<? echo $i;?>) " value="<? echo $rate; ?>" /> 
                                    </td>
                                    <td>
                                    <input type="text" id="amount_<? echo $i;?>"  name="amount_<? echo $i;?>"  onBlur="set_sum_value( 'amount_sum', 'amount_' ) " class="text_boxes_numeric" style="width:50px"  value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][amount]; ?>">
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
					else
					{
						$data_array=sql_select("select b.id, b.po_number,b.po_quantity,min(c.id) as color_size_table_id,c.color_number_id,c.size_number_id,sum(c.order_quantity) as order_quantity  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and  b.id='$po_id' and a.status_active=1 and b.status_active=1 and c.status_active=1 group by  b.id, b.po_number,b.po_quantity,c.color_number_id,c.size_number_id  order by b.id, c.color_number_id,c.size_number_id"); 
						$data_array_cons=explode("__",$cons_breck_downn);
						if ( count($data_array)>0)
						{
							$i=0;
							foreach( $data_array as $row )
							{
								$data=explode('_',$data_array_cons[$i]);
								$i++;
								$txtwoq_cal =def_number_format(($txtwoq/$row[csf('po_quantity')]) * ($row[csf('order_quantity')]),5,"");
					?>
                     <tr id="break_1" align="center">
                                    <td>
                                      <? echo $i;?>
                                    </td>
                                    
                                    <td>
                                    <input type="text" id="pocolor_<? echo $i;?>"  name="pocolor_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $color_library[$row[csf('color_number_id')]]; ?>" <? echo  $gmt_color_edb; ?>/>
                                    <input type="hidden" id="pocolorid_<? echo $i;?>"  name="pocolorid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('color_number_id')]; ?>" />
                                    </td>
                                     <td>
                                    <input type="text" id="gmtssizes_<? echo $i;?>"  name="gmtssizes_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $size_library[$row[csf('size_number_id')]]; ?>"<?  echo $gmt_size_edb; ?>/>
                                    <input type="hidden" id="gmtssizesid_<? echo $i;?>"  name="gmtssizesid_<? echo $i;?>" class="text_boxes" style="width:70px" value="<? echo $row[csf('size_number_id')]; ?>">
                                    </td>
                                    <td>
                                    <input type="text" id="des_<? echo $i;?>"  name="des_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $description;?>" onChange="copy_value(this.value,'des_',<? echo $i;?>)" />
                                    </td>
                                    <td>
                                    <input type="text" id="brndsup_<? echo $i;?>"  name="brndsup_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $brand_supplier; ?>" onChange="copy_value(this.value,'brndsup_',<? echo $i;?>)" />

                                    </td>
                                    <td>
                                    <input type="text" id="itemcolor_<? echo $i;?>"  value="<? echo $color_library[$item_color]; ?>"  name="itemcolor_<? echo $i;?>"  class="text_boxes" style="width:95px" onChange="copy_value(this.value,'itemcolor_',<? echo $i;?>)" <? echo $item_color_edb; ?> />    
                                    </td>
                                    <td>
                                    <input type="text" id="itemsizes_<? echo $i;?>"  name="itemsizes_<? echo $i;?>"    class="text_boxes" style="width:70px" onChange="copy_value(this.value,'itemsizes_',<? echo $i;?>)" value="<? echo $item_size; ?>" <? echo $item_size_edb; ?>/>
                                    </td>
                                    <td>
                                    <input type="text" id="qty_<? echo $i;?>" onBlur="validate_sum( <? echo $i; ?> )" onChange="set_sum_value( 'qty_sum', 'qty_' );set_sum_value( 'woqty_sum', 'woqny_' );calculate_requirement(<? echo $i;?>);copy_value(this.value,'qty_',<? echo $i;?>)"  name="qty_<? echo $i;?>" class="text_boxes_numeric" style="width:70px"   placeholder="<? echo $txtwoq_cal; ?>" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][cons]; ?>"/> 
                                    </td>
                                    <td>
                                    <input type="text" id="excess_<? echo $i;?>" onBlur="set_sum_value( 'excess_sum', 'excess_' ) "  name="excess_<? echo $i;?>" class="text_boxes_numeric" style="width:40px" onChange="calculate_requirement(<? echo $i;?>);set_sum_value( 'excess_sum', 'excess_' );set_sum_value( 'woqty_sum', 'woqny_' );copy_value(this.value,'excess_',<? echo $i;?>) " value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][process_loss_percent]; ?>" /> 
                                    </td>
                                    <td>
                                    <input type="text" id="woqny_<? echo $i;?>" onBlur="set_sum_value( 'woqty_sum', 'woqny_' ) " onChange="set_sum_value( 'woqty_sum', 'woqny_' )"  name="woqny_<? echo $i;?>" class="text_boxes_numeric" style="width:70px" value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][requirment]; ?>" readonly /> 
                                    </td>
                                    
                                    
                                     <td>
                                    <input type="text" id="rate_<? echo $i;?>"  name="rate_<? echo $i;?>" class="text_boxes_numeric" style="width:40px" onChange="calculate_amount(<? echo $i;?>);set_sum_value( 'amount_sum', 'amount_' );copy_value(this.value,'rate_',<? echo $i;?>) " value="<? echo $rate; ?>" /> 
                                    </td>
                                    <td>
                                    <input type="text" id="amount_<? echo $i;?>"  name="amount_<? echo $i;?>"  onBlur="set_sum_value( 'amount_sum', 'amount_' ) " class="text_boxes_numeric" style="width:50px"  value="<? echo $booking_data_arr[$row[csf('color_size_table_id')]][amount]; ?>">
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
					} 
					?>
                </tbody>
                </table>
               
                <table width="1050" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                        	<th style="width:534px;">SUM</th>
                            <th width="82"></th>
                            <th width="70"><input type="text" id="qty_sum"     name="qty_sum"    class="text_boxes_numeric" style="width:70px" readonly></th>
                            <th width="40"><input type="text" id="excess_sum"  name="excess_sum" class="text_boxes_numeric" style="width:40px" readonly></th>
                            <th width="70"><input type="text" id="woqty_sum"   name="woqty_sum"  class="text_boxes_numeric" style="width:70px" readonly></th>
                            <th width="40"><input type="text" id="rate_sum"    name="rate_sum"   class="text_boxes_numeric" style="width:40px" readonly></th>
                            <th width="50"><input type="text" id="amount_sum"  name="amount_sum" class="text_boxes_numeric" style="width:50px" readonly></th>
                            <th width="50"><input type="text" id="pcs_sum"     name="pcs_sum"    class="text_boxes_numeric" style="width:50px" readonly></th>
                            <th width=""></th>
                        </tr>
                        
                    </tfoot>
                </table>
				<script>
                set_sum_value( 'cons_sum', 'cons_'  );
                set_sum_value( 'processloss_sum', 'processloss_'  );
                set_sum_value( 'requirement_sum', 'requirement_'); 
                set_sum_value( 'pcs_sum', 'pcs_');
                </script>
            </form>
        </fieldset>
   </div>

<div align="center" style="width:100%;" >
<fieldset>
                <table width="810" cellspacing="0" class="" border="0" rules="all">
                	 <tr>
                        <td align="center" width="100%" class="button_container"> <input type="button" class="formbutton" value="Close" onClick="js_set_value()"/> </td> 
                    </tr>
                </table>
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
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";disconnect($con); die;}	
		
		$id=return_next_id( "id", "wo_booking_mst", 1 ) ;
		$field_array="id,booking_type,is_short,booking_month,booking_year,booking_no_prefix,booking_no_prefix_num,booking_no,company_id,buyer_id,job_no, 	item_category,supplier_id,currency_id,exchange_rate,booking_date,delivery_date,pay_mode,source,attention,ready_to_approved,remarks,item_from_precost,entry_form,inserted_by,insert_date";
		
		 $data_array ="(".$id.",2,2,".$cbo_booking_month.",".$cbo_booking_year.",'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",".$txt_job_no.",4,".$cbo_supplier_name.",".$cbo_currency.",".$txt_exchange_rate.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_pay_mode.",".$cbo_source.",".$txt_attention.",".$cbo_ready_to_approved.",".$txt_remarks.",".$cbo_item_from_precost.",44,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		 
		 $rID=sql_insert("wo_booking_mst",$field_array,$data_array,0);
		 	
		 $id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		 $field_array1="id, pre_cost_fabric_cost_dtls_id, po_break_down_id, job_no, booking_no, booking_type, is_short, trim_group, uom, sensitivity, wo_qnty, exchange_rate, pre_req_amt, rate, amount, delivery_date, cons_break_down, country_id_string, inserted_by, insert_date";

	
		$field_array2="id, wo_trim_booking_dtls_id, booking_no, job_no, po_break_down_id, color_number_id, gmts_sizes, description, brand_supplier, item_color, item_size, cons, process_loss_percent, requirment, rate, amount, pcs, color_size_table_id";
		
		 $add_comma=0;
		 $id1=return_next_id( "id", "wo_trim_book_con_dtls", 1 );
		 $new_array_color=array();
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $txttrimcostid="txttrimcostid_".$i;
			 $txtpoid="txtpoid_".$i;
			 $txttrimgroup="txttrimgroup_".$i;
			 $txtuom="txtuom_".$i;
			 //$txtreqqnty="txtreqqnty_".$i;
			// $txtcuwoq="txtcuwoq_".$i;
			 //$txtbalwoq="txtbalwoq_".$i;
			 $cbocolorsizesensitive="cbocolorsizesensitive_".$i;
			 $txtwoq="txtwoq_".$i;
			 $txtexchrate="txtexchrate_".$i;
			 $txtrate="txtrate_".$i;
			 $txtamount="txtamount_".$i;
			 $txtddate="txtddate_".$i;
			 $consbreckdown="consbreckdown_".$i;
			 $txtbookingid="txtbookingid_".$i;
			 $txtcountry="txtcountry_".$i;
			 $preconsamt="preconsamt_".$i;
			 //if ($i!=1) $data_array1 .=",";
			 $data_array1 ="(".$id_dtls.",".$$txttrimcostid.",".$$txtpoid.",".$txt_job_no.",'".$new_booking_no[0]."',2,2,".$$txttrimgroup.",".$$txtuom.",".$$cbocolorsizesensitive.",".$$txtwoq.",".$$txtexchrate.",".$$preconsamt.",".$$txtrate.",".$$txtamount.",".$$txtddate.",".$$consbreckdown.",".$$txtcountry.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			 
			 //	CONS break down===============================================================================================	
				if(str_replace("'",'',$$consbreckdown) !='')
				{
					$data_array2="";
					$rID_de1=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id =".$$txtbookingid."",0);
					$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
					for($c=0;$c < count($consbreckdown_array);$c++)
					{
						
						$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
						 /*if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color))
						 {
							  $color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name");  
							  $new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[4]);
						 }
						 else 
						 {
							 $color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
						 }*/
						if(str_replace("'","",$consbreckdownarr[4]) !="")
						{
						    if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color))
						    {
						        $color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name","44");
						        $new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[4]);
						    }
						    else $color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
						}
						else
						{
						    $color_id=0;
						}
						 if(str_replace("'","",$$cbocolorsizesensitive)==2){
							 $color_id='';
							 $consbreckdownarr[0]='';
						 }
						 if(str_replace("'","",$$cbocolorsizesensitive)==1 || str_replace("'","",$$cbocolorsizesensitive)==3){
							 $consbreckdownarr[1]='';
							 $consbreckdownarr[5]='';
						 }		
//"id,wo_trim_booking_dtls_id,booking_no,job_no,po_break_down_id,color_number_id,gmts_sizes,description,brand_supplier,item_color,item_size,cons, process_loss_percent,requirment,rate,amount,pcs,color_size_table_id"
						if ($c!=0) $data_array2 .=",";
						$data_array2 .="(".$id1.",".$id_dtls.",'".$new_booking_no[0]."',".$txt_job_no.",".$$txtpoid.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$color_id."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."','".$consbreckdownarr[10]."','".$consbreckdownarr[11]."','".$consbreckdownarr[12]."')";
						$id1=$id1+1;
						$add_comma++;
					}
				}
          //CONS break down end===============================================================================================
		  
		   $rID1=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
		   $rID2=1;
		   if($data_array2 !="")
		   {
		   $rID2=sql_insert("wo_trim_book_con_dtls",$field_array2,$data_array2,1);
		   }
		   $id_dtls=$id_dtls+1;
			
		 }
		 
		 
		
		 //echo $data_array2;
		 check_table_status( $_SESSION['menu_id'],0);
		 //echo $rID."".$rID1."".$rID2;die;
		if($db_type==0)
		{
			if($rID && $rID1 && $rID2){
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
			if($rID && $rID1 && $rID2){
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
		 $field_array_up="booking_month*booking_year*company_id*buyer_id*job_no*item_category*supplier_id*currency_id*exchange_rate*booking_date*delivery_date*pay_mode*source*attention*ready_to_approved*remarks*item_from_precost*entry_form*updated_by*update_date"; 
		 
		 $data_array_up ="".$cbo_booking_month."*".$cbo_booking_year."*".$cbo_company_name."*".$cbo_buyer_name."*".$txt_job_no."*4*".$cbo_supplier_name."*".$cbo_currency."*".$txt_exchange_rate."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_pay_mode."*".$cbo_source."*".$txt_attention."*".$cbo_ready_to_approved."*".$txt_remarks."*".$cbo_item_from_precost."*44*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		 $rID=sql_update("wo_booking_mst",$field_array_up,$data_array_up,"booking_no","".$txt_booking_no."",0);
		
		// if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}		
		 $field_array_up1="pre_cost_fabric_cost_dtls_id*po_break_down_id*job_no*booking_no*trim_group*uom*sensitivity*wo_qnty*exchange_rate*pre_req_amt*rate*amount*delivery_date*cons_break_down*country_id_string*updated_by*update_date";
		  $id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		  $field_array1="id, pre_cost_fabric_cost_dtls_id, po_break_down_id, job_no, booking_no, booking_type, is_short, trim_group, uom, sensitivity, wo_qnty, exchange_rate, pre_req_amt, rate, amount, delivery_date, cons_break_down, country_id_string, inserted_by, insert_date";
		 //$field_array2="id,wo_trim_booking_dtls_id,booking_no,job_no,po_break_down_id,color_number_id,gmts_sizes,description,brand_supplier,item_color,item_size,cons, process_loss_percent,requirment,rate,amount,pcs,color_size_table_id";
		 $field_array_up2="id,wo_trim_booking_dtls_id,booking_no,job_no,po_break_down_id,color_number_id,gmts_sizes,description,brand_supplier,item_color,item_size,cons, process_loss_percent,requirment,rate,amount,pcs,color_size_table_id";
		 
		  $add_comma=0;
		  $id1=return_next_id( "id", "wo_trim_book_con_dtls", 1 );
		  $new_array_color=array();
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $txttrimcostid="txttrimcostid_".$i;
			 $txtpoid="txtpoid_".$i;
			 $txttrimgroup="txttrimgroup_".$i;
			 $txtuom="txtuom_".$i;
			// $txtreqqnty="txtreqqnty_".$i;
			 //$txtcuwoq="txtcuwoq_".$i;
			 //$txtbalwoq="txtbalwoq_".$i;
			 $cbocolorsizesensitive="cbocolorsizesensitive_".$i;
			 $txtwoq="txtwoq_".$i;
			 $txtexchrate="txtexchrate_".$i;
			 $txtrate="txtrate_".$i;
			 $txtamount="txtamount_".$i;
			 $txtddate="txtddate_".$i;
			 $consbreckdown="consbreckdown_".$i;
			 $txtbookingid="txtbookingid_".$i;
			 $txtcountry="txtcountry_".$i;
			 $preconsamt="preconsamt_".$i;
			 
			if(str_replace("'",'',$$txtbookingid)!="")
			{
				$id_arr=array();
				$data_array_up1=array();
				
				$id_arr[]=str_replace("'",'',$$txtbookingid);
				$data_array_up1[str_replace("'",'',$$txtbookingid)] =explode("*",("".$$txttrimcostid."*".$$txtpoid."*".$txt_job_no."*".$txt_booking_no."*".$$txttrimgroup."*".$$txtuom."*".$$cbocolorsizesensitive."*".$$txtwoq."*".$$txtexchrate."*".$$preconsamt."*".$$txtrate."*".$$txtamount."*".$$txtddate."*".$$consbreckdown."*".$$txtcountry."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
				//	CONS break down===============================================================================================	
				if(str_replace("'",'',$$consbreckdown) !='')
				{
					$data_array_up2="";
					$rID_de1=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id =".$$txtbookingid."",0);
					$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
					for($c=0;$c < count($consbreckdown_array);$c++)
					{
						$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
						/* if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color))
						 {
							  $color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name");  
							  $new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[4]);
						 }
						 else 
						 {
							 $color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
						 }*/
						if(str_replace("'","",$consbreckdownarr[4]) !="")
						{
						    if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color))
						    {
						        $color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name","44");
						        $new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[4]);
						    }
						    else $color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
						}
						else
						{
						    $color_id=0;
						}
						 if(str_replace("'","",$$cbocolorsizesensitive)==2){
							 $color_id='';
							 $consbreckdownarr[0]='';
						 }
						 if(str_replace("'","",$$cbocolorsizesensitive)==1 || str_replace("'","",$$cbocolorsizesensitive)==3){
							 $consbreckdownarr[1]='';
							 $consbreckdownarr[5]='';
						 }	
						if ($c!=0) $data_array_up2 .=",";
						$data_array_up2 .="(".$id1.",".$$txtbookingid.",".$txt_booking_no.",".$txt_job_no.",".$$txtpoid.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$color_id."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."','".$consbreckdownarr[10]."','".$consbreckdownarr[11]."','".$consbreckdownarr[12]."')";
						$id1=$id1+1;
						$add_comma++;
					}
				}
                //CONS break down end===============================================================================================
				if($data_array_up1 !="")
				{
				$rID1=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ));
				}
			}
			
			if(str_replace("'",'',$$txtbookingid)=="")
			{
				 //if ($i!=1) $data_array1 .=",";
				 $data_array1 ="(".$id_dtls.",".$$txttrimcostid.",".$$txtpoid.",".$txt_job_no.",".$txt_booking_no.",2,2,".$$txttrimgroup.",".$$txtuom.",".$$cbocolorsizesensitive.",".$$txtwoq.",".$$txtexchrate.",".$$preconsamt.",".$$txtrate.",".$$txtamount.",".$$txtddate.",".$$consbreckdown.",".$$txtcountry.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				//	CONS break down===============================================================================================	
					if(str_replace("'",'',$$consbreckdown) !='')
					{
						$data_array_up2="";
						$rID_de1=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id =".$$txtbookingid."",0);
						$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
						for($c=0;$c < count($consbreckdown_array);$c++)
						{
							$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
							 /*if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color))
							 {
								  $color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name");  
								  $new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[4]);
							 }
							 else 
							 {
								 $color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
							 }*/
							if(str_replace("'","",$consbreckdownarr[4]) !="")
							{
							    if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color))
							    {
							        $color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name","44");
							        $new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[4]);
							    }
							    else $color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
							}
							else
							{
							    $color_id=0;
							}
							if ($c!=0) $data_array_up2 .=",";
							$data_array_up2 .="(".$id1.",".$id_dtls.",".$txt_booking_no.",".$txt_job_no.",".$$txtpoid.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$color_id."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."','".$consbreckdownarr[10]."','".$consbreckdownarr[11]."','".$consbreckdownarr[12]."')";
							$id1=$id1+1;
							$add_comma++;
						}
					}
					//CONS break down end===============================================================================================
					
				if($data_array1 !="")
				{
				$rID1=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
				}
				$id_dtls=$id_dtls+1;
			}
			
			
		  
			$rID2=1;
			if($data_array_up2 !="")
			{
			$rID2=sql_insert("wo_trim_book_con_dtls",$field_array_up2,$data_array_up2,1);
			}
		 }
		 
		
		
		
		
        //check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $rID1 &&  $rID2){
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
			if($rID && $rID1 &&  $rID2){
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





if ($action=="save_update_delete_old")
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
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}	
		
		$id=return_next_id( "id", "wo_booking_mst", 1 ) ;
		$field_array="id,booking_type,is_short,booking_month,booking_year,booking_no_prefix,booking_no_prefix_num,booking_no,company_id,buyer_id,job_no, 	item_category,supplier_id,currency_id,exchange_rate,booking_date,delivery_date,pay_mode,source,attention,remarks,item_from_precost,entry_form,inserted_by,insert_date";
		
		 $data_array ="(".$id.",2,2,".$cbo_booking_month.",".$cbo_booking_year.",'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",".$txt_job_no.",4,".$cbo_supplier_name.",".$cbo_currency.",".$txt_exchange_rate.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_pay_mode.",".$cbo_source.",".$txt_attention.",".$txt_remarks.",".$cbo_item_from_precost.",44,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		 
		 
		 	
		 $id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		 $field_array1="id,pre_cost_fabric_cost_dtls_id,po_break_down_id,job_no,booking_no,booking_type,is_short,trim_group,uom,sensitivity,wo_qnty,rate,amount,delivery_date,cons_break_down,country_id_string,inserted_by,insert_date";

	
		$field_array2="id,wo_trim_booking_dtls_id,booking_no,job_no,po_break_down_id,color_number_id,gmts_sizes,description,brand_supplier,item_color,item_size,cons, process_loss_percent,requirment,rate,amount,pcs,color_size_table_id";
		
		 $add_comma=0;
		 $id1=return_next_id( "id", "wo_trim_book_con_dtls", 1 );
		 $new_array_color=array();
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $txttrimcostid="txttrimcostid_".$i;
			 $txtpoid="txtpoid_".$i;
			 $txttrimgroup="txttrimgroup_".$i;
			 $txtuom="txtuom_".$i;
			 //$txtreqqnty="txtreqqnty_".$i;
			// $txtcuwoq="txtcuwoq_".$i;
			 //$txtbalwoq="txtbalwoq_".$i;
			 $cbocolorsizesensitive="cbocolorsizesensitive_".$i;
			 $txtwoq="txtwoq_".$i;
			 $txtrate="txtrate_".$i;
			 $txtamount="txtamount_".$i;
			 $txtddate="txtddate_".$i;
			 $consbreckdown="consbreckdown_".$i;
			 $txtbookingid="txtbookingid_".$i;
			 $txtcountry="txtcountry_".$i;
			 if ($i!=1) $data_array1 .=",";
			 $data_array1 .="(".$id_dtls.",".$$txttrimcostid.",".$$txtpoid.",".$txt_job_no.",'".$new_booking_no[0]."',2,2,".$$txttrimgroup.",".$$txtuom.",".$$cbocolorsizesensitive.",".$$txtwoq.",".$$txtrate.",".$$txtamount.",".$$txtddate.",".$$consbreckdown.",".$$txtcountry.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			 
			 //	CONS break down===============================================================================================	
				if(str_replace("'",'',$$consbreckdown) !='')
				{
					$rID_de1=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id =".$$txtbookingid."",0);
					$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
					for($c=0;$c < count($consbreckdown_array);$c++)
					{
						$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
						 /*if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color))
						 {
							  $color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name");  
							  $new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[4]);
						 }
						 else 
						 {
							 $color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
						 }*/
						if(str_replace("'","",$consbreckdownarr[4]) !="")
						{
						    if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color))
						    {
						        $color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name","44");
						        $new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[4]);
						    }
						    else $color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
						}
						else
						{
						    $color_id=0;
						}
						 		

						if ($add_comma!=0) $data_array2 .=",";
						$data_array2 .="(".$id1.",".$id_dtls.",'".$new_booking_no[0]."',".$txt_job_no.",".$$txtpoid.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$color_id."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."','".$consbreckdownarr[10]."','".$consbreckdownarr[11]."','".$consbreckdownarr[12]."')";
						$id1=$id1+1;
						$add_comma++;
					}
				}
          //CONS break down end===============================================================================================
		   $id_dtls=$id_dtls+1;
			
		 }
		 
		 $rID=sql_insert("wo_booking_mst",$field_array,$data_array,0);
		 $rID1=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
		 $rID2=1;
		 if($data_array2 !="")
		 {
		 $rID2=sql_insert("wo_trim_book_con_dtls",$field_array2,$data_array2,1);
		 }
		 //echo $data_array2;
		 check_table_status( $_SESSION['menu_id'],0);
		 //echo $rID."".$rID1."".$rID2;die;
		if($db_type==0)
		{
			if($rID && $rID1 && $rID2){
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
			if($rID && $rID1 && $rID2){
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
		 $field_array_up="booking_month*booking_year*company_id*buyer_id*job_no*item_category*supplier_id*currency_id*exchange_rate*booking_date*delivery_date*pay_mode*source*attention*remarks*item_from_precost*entry_form*updated_by*update_date"; 
		 
		 $data_array_up ="".$cbo_booking_month."*".$cbo_booking_year."*".$cbo_company_name."*".$cbo_buyer_name."*".$txt_job_no."*4*".$cbo_supplier_name."*".$cbo_currency."*".$txt_exchange_rate."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_pay_mode."*".$cbo_source."*".$txt_attention."*".$txt_remarks."*".$cbo_item_from_precost."*44*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}		
		 $field_array_up1="pre_cost_fabric_cost_dtls_id*po_break_down_id*job_no*booking_no*trim_group*uom*sensitivity*wo_qnty*rate*amount*delivery_date*cons_break_down*country_id_string*updated_by*update_date";
		  $id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		  $field_array1="id,pre_cost_fabric_cost_dtls_id,po_break_down_id,job_no,booking_no,booking_type,is_short,trim_group,uom,sensitivity,wo_qnty,rate,amount,delivery_date,cons_break_down,country_id_string,inserted_by,insert_date";
		 //$field_array2="id,wo_trim_booking_dtls_id,booking_no,job_no,po_break_down_id,color_number_id,gmts_sizes,description,brand_supplier,item_color,item_size,cons, process_loss_percent,requirment,rate,amount,pcs,color_size_table_id";
		 $field_array_up2="id,wo_trim_booking_dtls_id,booking_no,job_no,po_break_down_id,color_number_id,gmts_sizes,description,brand_supplier,item_color,item_size,cons, process_loss_percent,requirment,rate,amount,pcs,color_size_table_id";
		 
		  $add_comma=0;
		  $id1=return_next_id( "id", "wo_trim_book_con_dtls", 1 );
		  $new_array_color=array();
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $txttrimcostid="txttrimcostid_".$i;
			 $txtpoid="txtpoid_".$i;
			 $txttrimgroup="txttrimgroup_".$i;
			 $txtuom="txtuom_".$i;
			// $txtreqqnty="txtreqqnty_".$i;
			 //$txtcuwoq="txtcuwoq_".$i;
			 //$txtbalwoq="txtbalwoq_".$i;
			 $cbocolorsizesensitive="cbocolorsizesensitive_".$i;
			 $txtwoq="txtwoq_".$i;
			 $txtrate="txtrate_".$i;
			 $txtamount="txtamount_".$i;
			 $txtddate="txtddate_".$i;
			 $consbreckdown="consbreckdown_".$i;
			 $txtbookingid="txtbookingid_".$i;
			 $txtcountry="txtcountry_".$i;
			 
			if(str_replace("'",'',$$txtbookingid)!="")
			{
				$id_arr[]=str_replace("'",'',$$txtbookingid);
				$data_array_up1[str_replace("'",'',$$txtbookingid)] =explode("*",("".$$txttrimcostid."*".$$txtpoid."*".$txt_job_no."*".$txt_booking_no."*".$$txttrimgroup."*".$$txtuom."*".$$cbocolorsizesensitive."*".$$txtwoq."*".$$txtrate."*".$$txtamount."*".$$txtddate."*".$$consbreckdown."*".$$txtcountry."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
				//	CONS break down===============================================================================================	
				if(str_replace("'",'',$$consbreckdown) !='')
				{
					$rID_de1=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id =".$$txtbookingid."",0);
					$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
					for($c=0;$c < count($consbreckdown_array);$c++)
					{
						$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
						 /*if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color))
						 {
							  $color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name");  
							  $new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[4]);
						 }
						 else 
						 {
							 $color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
						 }*/
						if(str_replace("'","",$consbreckdownarr[4]) !="")
						{
						    if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color))
						    {
						        $color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name","44");
						        $new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[4]);
						    }
						    else $color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
						}
						else
						{
						    $color_id=0;
						}
						if ($add_comma!=0) $data_array_up2 .=",";
						$data_array_up2 .="(".$id1.",".$$txtbookingid.",".$txt_booking_no.",".$txt_job_no.",".$$txtpoid.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$color_id."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."','".$consbreckdownarr[10]."','".$consbreckdownarr[11]."','".$consbreckdownarr[12]."')";
						$id1=$id1+1;
						$add_comma++;
					}
				}
          //CONS break down end===============================================================================================
			}
			
			if(str_replace("'",'',$$txtbookingid)=="")
			{
				
		 
				if ($i!=1) $data_array1 .=",";
			 $data_array1 .="(".$id_dtls.",".$$txttrimcostid.",".$$txtpoid.",".$txt_job_no.",".$txt_booking_no.",2,2,".$$txttrimgroup.",".$$txtuom.",".$$cbocolorsizesensitive.",".$$txtwoq.",".$$txtrate.",".$$txtamount.",".$$txtddate.",".$$consbreckdown.",".$$txtcountry.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				//	CONS break down===============================================================================================	
				if(str_replace("'",'',$$consbreckdown) !='')
				{
					$rID_de1=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id =".$$txtbookingid."",0);
					$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
					for($c=0;$c < count($consbreckdown_array);$c++)
					{
						$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
						 /*if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color))
						 {
							  $color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name");  
							  $new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[4]);
						 }
						 else 
						 {
							 $color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
						 }*/
						if(str_replace("'","",$consbreckdownarr[4]) !="")
						{
						    if (!in_array(str_replace("'","",$consbreckdownarr[4]),$new_array_color))
						    {
						        $color_id = return_id( str_replace("'","",$consbreckdownarr[4]), $color_library, "lib_color", "id,color_name","44");
						        $new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[4]);
						    }
						    else $color_id =  array_search(str_replace("'","",$consbreckdownarr[4]), $new_array_color);
						}
						else
						{
						    $color_id=0;
						}
						if ($add_comma!=0) $data_array_up2 .=",";
						$data_array_up2 .="(".$id1.",".$id_dtls.",".$txt_booking_no.",".$txt_job_no.",".$$txtpoid.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$consbreckdownarr[3]."','".$color_id."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."','".$consbreckdownarr[10]."','".$consbreckdownarr[11]."','".$consbreckdownarr[12]."')";
						$id1=$id1+1;
						$add_comma++;
					}
				}
          //CONS break down end===============================================================================================
		   $id_dtls=$id_dtls+1;
			}
		 }
		 
		$rID=sql_update("wo_booking_mst",$field_array_up,$data_array_up,"booking_no","".$txt_booking_no."",0);
		if($data_array_up1 !="")
		{
		$rID1=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ));
		}
		if($data_array1 !="")
		{
		$rID1=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
		}
		$rID2=1;
		if($data_array_up2 !="")
		{
		$rID2=sql_insert("wo_trim_book_con_dtls",$field_array_up2,$data_array_up2,1);
		}
        check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $rID1 &&  $rID2){
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
			if($rID && $rID1 &&  $rID2){
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




if ($action=="trims_booking_popup"){
	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	function js_set_value( str_data ){
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
                                    <tr>
                                        <th colspan="4"> </th>
                                        <th>
                                        <?
                                        echo create_drop_down( "cbo_search_category", 80, $string_search_type,'', 1, "-- Search Catagory --" );
                                        ?>
                                        </th>
                                        <th colspan="4"></th>
                                    </tr>
                                    <tr>               	 
                                        <th width="150" class="must_entry_caption">Company Name</th>
                                        <th width="150" class="must_entry_caption">Buyer Name</th>
                                        <th width="100">Style Ref </th>
                                         <th width="80">Job No</th>
                                        <th width="100">Order No</th>
                                        <th width="100">Supplier Name</th>
                                        <th width="80">Booking No</th>
                                        <th width="200"> Booking Date Range</th><th></th>   
                                    </tr>        
                                </thead>
                                <tr>
                                    <td> 
                                    <? 
                                    echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'trims_booking_controller_v2', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                                    ?>
                                    </td>
                                    <td id="buyer_td">
                                    <? 
                                    echo create_drop_down( "cbo_buyer_name", 152, $blank_array,"", 1, "-- Select Buyer --", $selected, "","" );
                                    ?>	  
                                    </td>
                                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px"></td>
                                     <td><input name="txt_job" id="txt_job" class="text_boxes" style="width:80px"></td>
                                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
                                    <td>
                                    <?
                                    echo create_drop_down( "cbo_supplier_name", 102, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=4 and   a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );
                                    ?>	</td>
                                    <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:80px"></td>
                                    <td>
                                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
                                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                                    </td> 
                                    <td align="center">
                                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_job').value, 'create_booking_search_list_view', 'search_div', 'trims_booking_controller_v2','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td  align="center" height="40" valign="middle">
                        <? 
                        echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
                        echo load_month_buttons();  
                        ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" valign="top" id="search_div"></td>
                    </tr>
                </table>    
            </form>
        </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
exit();
}
if ($action=="create_booking_search_list_view"){
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";
	if ($data[2]!=0) $supplier_id=" and a.supplier_id='$data[2]'"; else $supplier_id ="";
	if($db_type==0){
	$booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[5]";	
		if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2){
	$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[5]";
		if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	
	if($data[7]==1){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num ='$data[6]'   "; else $booking_cond="";
		if (trim($data[8])!="") $style_cond=" and c.style_ref_no ='$data[8]'";
		if (str_replace("'","",$data[9])!="") $order_cond=" and d.po_number = '$data[9]'  "; //else  $order_cond="";
		if (str_replace("'","",$data[10])!="") $job_cond=" and c.job_no_prefix_num = '$data[10]' "; else $job_cond="";
	}
	if($data[7]==2){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[6]%'  $booking_year_cond  "; else $booking_cond="";
		if (trim($data[8])!="") $style_cond=" and c.style_ref_no like '$data[8]%'  "; //else  $style_cond=""; 
		if (str_replace("'","",$data[9])!="") $order_cond=" and d.po_number like '$data[9]%'  "; //else  $order_cond=""; 
		if (str_replace("'","",$data[10])!="") $job_cond=" and c.job_no_prefix_num like '$data[10]%' "; else $job_cond="";
	}
	if($data[7]==3){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]'  $booking_year_cond  "; else $booking_cond="";
		if (trim($data[8])!="") $style_cond=" and c.style_ref_no like '%$data[8]'"; //else  $style_cond=""; 
		if (str_replace("'","",$data[9])!="") $order_cond=" and d.po_number like '%$data[9]'  "; //else  $order_cond=""; 
		if (str_replace("'","",$data[10])!="") $job_cond=" and c.job_no_prefix_numlike '%$data[10]' "; else $job_cond="";
	}
	if($data[7]==4 || $data[7]==0){
		if (str_replace("'","",$data[6])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[6]%'  $booking_year_cond  "; else $booking_cond="";
		if (trim($data[8])!="") $style_cond=" and c.style_ref_no like '%$data[8]%'"; //else  $style_cond=""; 
		if (str_replace("'","",$data[9])!="") $order_cond=" and d.po_number like '%$data[9]%'  "; //else  $order_cond="";
		if (str_replace("'","",$data[10])!="") $job_cond=" and c.job_no_prefix_num like '%$data[10]%' "; else $job_cond="";
	}
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (1=>$comp,2=>$suplier);
	
	 $sql="select min(a.id) as id, a.booking_no_prefix_num, a.booking_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date,c.style_ref_no,c.job_no_prefix_num,d.po_number from wo_booking_mst a,wo_booking_dtls b, wo_po_details_master c,wo_po_break_down d  where a.booking_no=b.booking_no and b.job_no=c.job_no and b.job_no=d.job_no_mst and a.booking_type=2 and a.entry_form=44 $company  $buyer $job_cond  $supplier_id $booking_date $booking_cond $style_cond $order_cond group by a.booking_no_prefix_num, a.booking_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date,c.style_ref_no,c.job_no_prefix_num,d.po_number  order by id";
	
	echo  create_list_view("list_view", "Booking No,Company,Supplier,Booking Date,Delivery Date,Style Ref No,Job No,Po Number", "120,100,100,100,150,100,150","1000","320",0, $sql , "js_set_value", "booking_no", "", 1, "0,company_id,supplier_id,0,0,0,0,0", $arr , "booking_no_prefix_num,company_id,supplier_id,booking_date,delivery_date,style_ref_no,job_no_prefix_num,po_number", '','','0,0,0,3,3,0,0','','','');
}

if($action=="get_attention_name")
{
	$contact_person='';
	$sql=sql_select("select id,contact_person from lib_supplier  where id=$data and  status_active =1 and is_deleted=0");
	foreach($sql as $row){
		$contact_person=$row[csf('contact_person')];
	}
	echo $contact_person;
	//die;
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
			
			data_all=data_all+get_submitted_data_string('txt_booking_no*termscondition_'+i,"");
		}
		var data="action=save_update_delete_fabric_booking_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
		//freeze_window(operation);
		http.open("POST","trims_booking_controller_v2.php",true);
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
                                    <input type="text" id="sltd_<? echo $i;?>"   name="sltd_<? echo $i;?>" style="width:100%;background-color:<? echo $bgcolor;  ?>"  value="<? echo $i; ?>"  readonly /> 
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
                                    <input type="text" id="sltd_<? echo $i;?>"   name="sltd_<? echo $i;?>" style="width:100%; background-color:<? echo $bgcolor;  ?>"  value="<? echo $i; ?>"  readonly /> 
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
	?>
	<div style="width:1333px" align="center">       
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100"> 
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
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
		$nameArray_job=sql_select( "select distinct b.job_no  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.status_active=1 and b.is_deleted=0"); 
        foreach ($nameArray_job as $result_job)
        {
			$job_no.=$result_job[csf('job_no')].", ";
		}
		$buyer_string="";
		
		$nameArray_buyer=sql_select( "select distinct a.buyer_name  from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active=1 and b.is_deleted=0"); 
        foreach ($nameArray_buyer as $result_buy)
        {
			$buyer_string.=$buyer_name_arr[$result_buy[csf('buyer_name')]].",";
		}
		
		$po_no="";$file_no="";$ref_no="";$ship_date="";
		$nameArray_job=sql_select( "select distinct b.po_number,b.grouping,b.file_no,b.pub_shipment_date  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0"); 
        foreach ($nameArray_job as $result_job)
        {
			$po_no.=$result_job[csf('po_number')].", ";
			$ref_no.=$result_job[csf('grouping')].", ";
			$file_no.=$result_job[csf('file_no')].", ";
			
		if($ship_date=="")	$ship_date=change_date_format($result_job[csf('pub_shipment_date')]);else $ship_date.=",".change_date_format($result_job[csf('pub_shipment_date')]);
		}
		$ship_dates=implode(",",array_unique(explode(",",$ship_date)));
		$style_ref="";
		$nameArray_style=sql_select( "select distinct a.style_ref_no  from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active=1 and b.is_deleted=0"); 
        foreach ($nameArray_style as $result_style)
        {
			$style_ref.=$result_style[csf('style_ref_no')].", ";
		}
        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.pay_mode,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source,a.remarks  from wo_booking_mst a where  a.booking_no=$txt_booking_no"); 
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
                <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
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
               	<td>:&nbsp;<? echo $supplier_address_arr[$result[csf('supplier_id')]];?></td>
                 <td width="100" style="font-size:12px"><b>Shipment Date</b></td>
                 <td>:&nbsp;<? echo $ship_dates;?></td>
               	
            </tr> 
            <tr>
                <td width="100" style="font-size:12px"><b>Buyer</b>   </td>
                <td width="110" colspan="2">:&nbsp;
				<? 
				echo rtrim($buyer_string,", ");
				?> 
                </td>
                <td width="110" style="font-size:12px"><b>Style</b> </td>
                <td  width="100" style="font-size:12px" colspan="2">:&nbsp;<? echo rtrim($style_ref,", "); ?> </td>
                 
               	
            </tr> 
            <tr>
                <td width="100" style="font-size:12px"><b>Job No</b>   </td>
                <td width="110" colspan="2">:&nbsp;
				<? 
				echo rtrim($job_no,", ");
				?> 
                </td>
                 
               	<td width="110" style="font-size:12px"><b>PO No</b> </td>
                <td  width="100" style="font-size:12px" colspan="2">:&nbsp;<? echo rtrim($po_no,", "); ?> </td>
            </tr> 
            
             <tr>
                <td width="100" style="font-size:12px"><b>File No</b>   </td>
                <td width="110">:&nbsp;
				<? 
				echo rtrim($file_no,", ");
				?> 
                </td>
                <td width="100" style="font-size:12px"><b>Ref. No</b>   </td>
                <td width="110">:&nbsp;
				<? 
				echo rtrim($ref_no,", ");
				
				?> 
                </td>
                
                <td width="100" style="font-size:12px"><b>Remarks</b>   </td>
                <td width="110">:&nbsp;
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
        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and status_active=1 and is_deleted=0 order by trim_group "); 
        $nameArray_color=sql_select( "select distinct b.color_number_id from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and b.requirment!=0 and a.sensitivity=1 and a.status_active=1 and a.is_deleted=0"); 
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
            $nameArray_item_description=sql_select( "select b.description, b.brand_supplier,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and b.requirment!=0  and a.sensitivity=1 and a.trim_group=".$result_item[csf('trim_group')]." and a.status_active=1 and a.is_deleted=0 group by b.description, b.brand_supplier order by trim_group "); 
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
					
                $nameArray_color_size_qnty=sql_select( "select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.requirment!=0  and a.status_active=1 and a.is_deleted=0 and a.sensitivity=1 and a.trim_group=". $result_item[csf('trim_group')]." and b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='". $result_itemdescription[csf('brand_supplier')]."' and b.color_number_id=".$result_color[csf('color_number_id')].""); 
					}
					if($db_type==2)
					{
					
                $nameArray_color_size_qnty=sql_select( "select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.requirment!=0  and a.status_active=1 and a.is_deleted=0 and a.sensitivity=1 and a.trim_group=". $result_item[csf('trim_group')]." and nvl(b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('". $result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.color_number_id,0)=nvl(".$result_color[csf('color_number_id')].",0)"); 
					}
					
                foreach($nameArray_color_size_qnty as $result_color_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <? 
                if($result_color_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_color_size_qnty[csf('cons')],4);
                $item_desctiption_total += round($result_color_size_qnty[csf('cons')],4) ;
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
                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,4); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($amount_as_per_gmts_color/$item_desctiption_total,4); ?> </td>
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
                echo number_format($color_tatal[$result_color[csf('color_number_id')]],4);  
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),4);  ?></td>
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
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+7; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER GMTS COLOR END=========================================  -->
        
        <!--==============================================AS PER GMTS SIZE START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and status_active=1 and is_deleted=0 order by trim_group "); 
        $nameArray_size=sql_select( "select distinct b.item_size  as gmts_sizes from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.status_active=1 and a.is_deleted=0 and a.booking_no=$txt_booking_no and a.sensitivity=2");
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
			$nameArray_item_description=sql_select( "select b.description, b.brand_supplier,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.sensitivity=2 and a.trim_group=".$result_item[csf('trim_group')]." and a.status_active=1 and a.is_deleted=0 group by b.description, b.brand_supplier"); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i ; ?>
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
                foreach($nameArray_size  as $result_size)
                {
					if($db_type==0)
					{
                $nameArray_size_size_qnty=sql_select( "select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.sensitivity=2 and a.trim_group=". $result_item[csf('trim_group')]." and b.description='". $result_itemdescription[csf('description')]."' and a.status_active=1 and a.is_deleted=0 and b.brand_supplier='". $result_itemdescription[csf('brand_supplier')]."' and b.item_size='".$result_size[csf('gmts_sizes')]."'");
					}
					if($db_type==2)
					{
                $nameArray_size_size_qnty=sql_select( "select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.sensitivity=2 and  a.status_active=1 and a.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and nvl(b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('". $result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_size,0)=nvl('".$result_size[csf('gmts_sizes')]."',0)");
					}
                foreach($nameArray_size_size_qnty as $result_size_size_qnty)
                {
                ?>
                <td style="border:1px solid black; text-align:right">
                <? 
                if($result_size_size_qnty[csf('cons')]!= "")
                {
                echo number_format($result_size_size_qnty[csf('cons')],4);
                $item_desctiption_total += $result_size_size_qnty[csf('cons')] ;
                if (array_key_exists($result_size[csf('color_number_id')], $color_tatal))
                {
                $color_tatal[$result_size[csf('color_number_id')]]+=$result_size_size_qnty[csf('cons')];
                }
                else
                {
                $color_tatal[$result_size[csf('color_number_id')]]=$result_size_size_qnty[csf('cons')]; 
                }
                }
                else echo "";
                ?>
                </td>
                <?   
                }
                }
                ?>
                
                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,4); ?></td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">

                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,4);
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
                foreach($nameArray_size  as $result_size)

                {
                
                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_size[csf('gmts_sizes')]] !='')
                {
                echo number_format($color_tatal[$result_size[csf('gmts_sizes')]],4);  
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),4);  ?></td>
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
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+7; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER SIZE  END=========================================  -->
        
         <!--==============================================AS PER CONTRAST COLOR START=========================================  -->
		<?
		$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.sensitivity=3 and a.status_active=1 and a.is_deleted=0", "item_color", "color_number_id"  );
        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and status_active=1 and is_deleted=0 order by trim_group "); 
        $nameArray_color=sql_select( "select distinct b.item_color as color_number_id from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.sensitivity=3 and a.status_active=1 and a.is_deleted=0"); 
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
                <td style="border:1px solid black"><strong> Item Color</strong> </td>
                <?  				
                foreach($nameArray_color  as $result_color)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('color_number_id')]];?></strong></td>
                <?	}    ?>				
                <td style="border:1px solid black" align="center" rowspan="2"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center" rowspan="2"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center" rowspan="2"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center" rowspan="2"><strong>Amount</strong></td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Gmts Color</strong> </td>
                <?  				
                foreach($nameArray_color  as $result_color)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$gmtcolor_library[$result_color[csf('color_number_id')]]];?></strong></td>
                <?	}    ?>				
                
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            //$nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and trim_group=".$result_item[csf('trim_group')]." order by trim_group "); 
			$nameArray_item_description=sql_select( "select b.description, b.brand_supplier,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.sensitivity=3 and a.trim_group=".$result_item[csf('trim_group')]." and a.status_active=1 and a.is_deleted=0 group by b.description, b.brand_supplier order by trim_group "); 
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
                $nameArray_color_size_qnty=sql_select( "select sum(b.cons) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and a.sensitivity=3 and a.trim_group=". $result_item[csf('trim_group')]." and b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='". $result_itemdescription[csf('brand_supplier')]."' and b.item_color=".$result_color[csf('color_number_id')]."");
					}
					if($db_type==2)
				    {
                $nameArray_color_size_qnty=sql_select( "select sum(b.cons) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no  and a.status_active=1 and a.is_deleted=0 and a.sensitivity=3 and a.trim_group=". $result_item[csf('trim_group')]." and nvl(b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('". $result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_color,0)=nvl(".$result_color[csf('color_number_id')].",0)");
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
                
                <td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,4); ?></td>
                 <td style="border:1px solid black; text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
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
                <?
                foreach($nameArray_color  as $result_color)
                {
                
                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_color[csf('color_number_id')]] !='')
                {
                echo number_format($color_tatal[$result_color[csf('color_number_id')]],4);  
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),4);  ?></td>
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
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+8; ?>"><strong>Total</strong></td>
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
        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and status_active=1 and is_deleted=0 order by trim_group "); 
        $nameArray_size=sql_select( "select distinct b.item_size  as gmts_sizes from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.booking_no=$txt_booking_no and a.sensitivity=4");
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
			$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.trim_group=".$result_item[csf('trim_group')]." and a.status_active=1 and a.is_deleted=0 and a.sensitivity=4", "item_color", "color_number_id"  );
			
			$nameArray_color=sql_select( "select  b.item_color,b.color_number_id,b.description, b.brand_supplier,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.trim_group=".$result_item[csf('trim_group')]." and a.sensitivity=4 and a.status_active=1 and a.is_deleted=0 group by b.item_color,b.color_number_id,b.description, b.brand_supplier"); 
            $nameArray_item_description=sql_select( "select distinct uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and status_active=1 and is_deleted=0 and trim_group=".$result_item[csf('trim_group')]." order by trim_group "); 
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
						$nameArray_size_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.sensitivity=4 and a.status_active=1 and a.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and  b.description='". $result_color[csf('description')]."' and b.brand_supplier='".$result_color[csf('brand_supplier')]."'  and b.item_size='".$result_size[csf('gmts_sizes')]."' and b.item_color=".$result_color[csf('item_color')]." and b.color_number_id=".$result_color[csf('color_number_id')]."");
						}
						if($db_type==2)
				        {
						$nameArray_size_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.sensitivity=4 and a.status_active=1 and a.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and nvl( b.description,0)=nvl('". $result_color[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$result_color[csf('brand_supplier')]."',0)  and nvl(b.item_size,0)=nvl('".$result_size[csf('gmts_sizes')]."',0) and nvl(b.item_color,0)=nvl(".$result_color[csf('item_color')].",0) and nvl(b.color_number_id,0)=nvl(".$result_color[csf('color_number_id')].",0)");
						}
						foreach($nameArray_size_size_qnty as $result_size_size_qnty)
						{
							?>
							<td style="border:1px solid black; text-align:right">
							<? 
							if($result_size_size_qnty[csf('cons')]!= "")
							{
							echo number_format($result_size_size_qnty[csf('cons')],4);
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
					<td style="border:1px solid black; text-align:right"><? echo number_format($item_desctiption_total,4); ?></td>
					<td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
					<td style="border:1px solid black; text-align:right">
					<? 
					$rate =$result_color[csf('amount')]/$item_desctiption_total;;
					echo number_format($rate,4); 
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
                <td style="border:1px solid black;  text-align:right" colspan="6"><strong> Item Total</strong></td>
                <?
                foreach($nameArray_size  as $result_size)
                {
                
                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_size[csf('gmts_sizes')]] !='')
                {
                echo number_format($color_tatal[$result_size[csf('gmts_sizes')]],4);  
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right"><? echo number_format(array_sum($color_tatal),4);  ?></td>
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
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+9; ?>"><strong>Total</strong></td>
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
        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and status_active=1 and is_deleted=0 order by trim_group "); 
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
				
				$nameArray_item_description=sql_select( "select  b.description, b.brand_supplier,b.item_color,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.trim_group=".$result_item[csf('trim_group')]." and a.sensitivity=0 and a.status_active=1 and a.is_deleted=0 group by b.description, b.brand_supplier,b.item_color"); 
				
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
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.sensitivity=0 and a.trim_group=". $result_item[csf('trim_group')]." and  b.description='". $result_itemdescription[csf('description')]."' and a.status_active=1 and a.is_deleted=0 and b.brand_supplier='".$result_itemdescription[csf('brand_supplier')]."' and b.item_color='".$result_itemdescription[csf('item_color')]."'");
						}
						if($db_type==2)
				        {
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.sensitivity=0 and a.trim_group=". $result_item[csf('trim_group')]." and nvl( b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$result_itemdescription[csf('brand_supplier')]."',0) and a.status_active=1 and a.is_deleted=0 and nvl(b.item_color,0)=nvl('".$result_itemdescription[csf('item_color')]."',0)");
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
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('amount')]/$item_desctiption_total,4); ?> </td>
                <td style="border:1px solid black; text-align:right">
                <? //$item_desctiption_total*  $result_itemdescription[csf('rate')]
                $amount_as_per_gmts_color = $result_itemdescription[csf('amount')];echo number_format($amount_as_per_gmts_color,2);
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
        <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
        <thead>
            <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Spacial Instruction</th>
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
                $i++;
                ?>
                    <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;">
                        <? echo $row[csf('terms')]; ?>
                        </td>
                    </tr>
                <?
            }
        }
        else
        {
			$i=0;
        $data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1");// quotation_id='$data'
        foreach( $data_array as $row )
            {
                $i++;
        ?>
        <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;">
                        <? echo $row[csf('terms')]; ?>
                        </td>
                        
                    </tr>
        <? 
            }
        } 
        ?>
    </tbody>
    </table>
    </td>
    <td width="2%"></td>
    
    <td width="49%">
     <?
	if($show_comment==1)
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
                     <? echo number_format($pre_amount,4,".",""); ?>
                    </td>
                    <td width="" align="right">
                    <? echo number_format($cu_woq_amount,4,".","");?>
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
    
	<script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
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
	//$po_qnty_tot=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<div style="width:1333px" align="center">       
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100"> 
               <img  src='<? echo $path?$path:"../../";?><? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
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
		$nameArray_job=sql_select( "select distinct b.job_no  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.status_active=1 and b.is_deleted=0"); 
        foreach ($nameArray_job as $result_job)
        {

			$job_no.=$result_job[csf('job_no')].", ";
		}
		$buyer_string="";
		
		$nameArray_buyer=sql_select( "select distinct a.buyer_name  from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active=1 and b.is_deleted=0"); 
        foreach ($nameArray_buyer as $result_buy)
        {
			$buyer_string.=$buyer_name_arr[$result_buy[csf('buyer_name')]].",";
		}
		
		$po_no="";	$file_no="";	$ref_no="";$ship_date="";
		$nameArray_job=sql_select( "select distinct b.po_number,b.file_no,b.grouping,b.pub_shipment_date  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0"); 
        foreach ($nameArray_job as $result_job)
        {
			$po_no.=$result_job[csf('po_number')].", ";
			$file_no.=$result_job[csf('file_no')].", ";
			$ref_no.=$result_job[csf('grouping')].", ";
			if($ship_date=="") $ship_date=change_date_format($result_job[csf('pub_shipment_date')]);else $ship_date.=",".change_date_format($result_job[csf('pub_shipment_date')]);

		}
		$ship_dates=implode(",",array_unique(explode(",",$ship_date)));
		$style_ref="";
		$nameArray_style=sql_select( "select distinct a.style_ref_no  from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no"); 
        foreach ($nameArray_style as $result_style)
        {
			$style_ref.=$result_style[csf('style_ref_no')].", ";
		}
        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.pay_mode,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source,a.remarks  from wo_booking_mst a where  a.booking_no=$txt_booking_no"); 
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
                <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
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
               	<td >:&nbsp;<? echo $supplier_address_arr[$result[csf('supplier_id')]];?></td>
                <td width="100" style="font-size:12px"><b>Shipment Date</b></td>
               	<td>:&nbsp;<? echo $ship_dates;?></td>
               	
            </tr> 
            <tr>
                <td width="100" style="font-size:12px"><b>Buyer</b>   </td>
                <td width="110" colspan="2">:&nbsp;
				<? 
				echo rtrim($buyer_string,", ");
				?> 
                </td>
                <td width="110" style="font-size:12px"><b>Style</b> </td>
                <td  width="100" style="font-size:12px" colspan="2">:&nbsp;<? echo rtrim($style_ref,", "); ?> </td>
                 
               	
            </tr> 
            <tr>
                <td width="100" style="font-size:12px"><b>Job No</b>   </td>
                <td width="110" colspan="2">:&nbsp;
				<? 
				echo rtrim($job_no,", ");
				?> 
                </td>
                 
               	<td width="110" style="font-size:12px"><b>PO No</b> </td>
                <td  width="100" style="font-size:12px" colspan="2">:&nbsp;<? echo rtrim($po_no,", "); ?> </td>
            </tr> 
             <tr>
                
                 <td width="100" style="font-size:12px"><b>File No</b>   </td>
                <td width="110">:&nbsp;
				<? 
					echo rtrim($file_no,", ");
				?> 
                </td>
                <td width="100" style="font-size:12px"><b>Ref. No</b>   </td>
                <td width="110">:&nbsp;
				<? 
					echo rtrim($ref_no,", ");
				?> 
                </td>
                
                <td width="100" style="font-size:12px"><b>Remarks</b>   </td>
                <td width="110">:&nbsp;
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
		$nameArray_job_po=sql_select( "select job_no,po_break_down_id from wo_booking_dtls  where booking_no=$txt_booking_no and status_active=1 and is_deleted=0  group by job_no,po_break_down_id order by job_no,po_break_down_id "); 
		foreach($nameArray_job_po as $nameArray_job_po_row)
		{
		
        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]."  and sensitivity=1 and status_active=1 and is_deleted=0 order by trim_group "); 
        $nameArray_color=sql_select( "select distinct b.color_number_id from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.status_active=1 and a.is_deleted=0 and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=1"); 
		if(count($nameArray_color)>0)
		{
        ?>
        &nbsp;
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
           <tr>
                <td colspan="<? echo count($nameArray_color)+8; ?>" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]];?></strong>
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
            $nameArray_item_description=sql_select( "select b.description, b.brand_supplier,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=1 and a.trim_group=".$result_item[csf('trim_group')]." and a.status_active=1 and a.is_deleted=0 group by b.description, b.brand_supplier order by trim_group "); 
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
					
                $nameArray_color_size_qnty=sql_select( "select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=1 and a.trim_group=". $result_item[csf('trim_group')]." and b.description='". $result_itemdescription[csf('description')]."' and a.status_active=1 and a.is_deleted=0  and b.brand_supplier='". $result_itemdescription[csf('brand_supplier')]."' and b.color_number_id=".$result_color[csf('color_number_id')].""); 
					}
					if($db_type==2)
					{
					
                $nameArray_color_size_qnty=sql_select( "select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=1 and a.trim_group=". $result_item[csf('trim_group')]." and nvl(b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and a.status_active=1 and a.is_deleted=0 and nvl(b.brand_supplier,0)=nvl('". $result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.color_number_id,0)=nvl(".$result_color[csf('color_number_id')].",0)"); 
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
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
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
        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and status_active=1 and is_deleted=0 and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=2 order by trim_group "); 
        $nameArray_size=sql_select( "select distinct b.item_size  as gmts_sizes from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.status_active=1 and a.is_deleted=0 and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.sensitivity=2");
		if(count($nameArray_size)>0)
		{
        ?>
        
        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="<? echo count($nameArray_size)+8; ?>" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]];?></strong>
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
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
			$i++;
            //$nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and trim_group=".$result_item[csf('trim_group')]." order by trim_group "); 
			$nameArray_item_description=sql_select( "select b.description, b.brand_supplier,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=2 and a.trim_group=".$result_item[csf('trim_group')]." and a.status_active=1 and a.is_deleted=0 group by b.description, b.brand_supplier"); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i ; ?>
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
                foreach($nameArray_size  as $result_size)
                {
					if($db_type==0)
					{
                $nameArray_size_size_qnty=sql_select( "select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=2 and a.status_active=1 and a.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='". $result_itemdescription[csf('brand_supplier')]."' and b.item_size='".$result_size[csf('gmts_sizes')]."'");
					}
					if($db_type==2)
					{
                $nameArray_size_size_qnty=sql_select( "select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=2 and a.trim_group=". $result_item[csf('trim_group')]." and nvl(b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('". $result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_size,0)=nvl('".$result_size[csf('gmts_sizes')]."',0)");
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
                if (array_key_exists($result_size[csf('color_number_id')], $color_tatal))
                {
                $color_tatal[$result_size[csf('color_number_id')]]+=$result_size_size_qnty[csf('cons')];
                }
                else
                {

                $color_tatal[$result_size[csf('color_number_id')]]=$result_size_size_qnty[csf('cons')]; 
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
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
                <td style="border:1px solid black; text-align:right">

                <? 
                $amount_as_per_gmts_color = $item_desctiption_total*$result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,2);
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
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+7; ?>"><strong>Total</strong></td>
                <td  style="border:1px solid black;  text-align:right"><?  echo number_format($grand_total_as_per_gmts_color,2); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER SIZE  END=========================================  -->
        
         <!--==============================================AS PER CONTRAST COLOR START=========================================  -->
		<?
		$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.status_active=1 and a.is_deleted=0 and a.booking_no=$txt_booking_no and a.sensitivity=3", "item_color", "color_number_id"  );
		
        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=3 and status_active=1 and is_deleted=0 order by trim_group "); 
        $nameArray_color=sql_select( "select distinct b.item_color as color_number_id,b.color_number_id as gmts_color from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=3 and a.status_active=1 and a.is_deleted=0"); 
		if(count($nameArray_color)>0)
		{
        ?>
        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
           <tr>
                <td colspan="<? echo count($nameArray_color)+9; ?>" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]];?></strong>
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
                <td style="border:1px solid black"><strong> Item Color</strong> </td>
                <?  				
                foreach($nameArray_color  as $result_color)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('color_number_id')]];?></strong></td>
                <?	}    ?>				
                <td style="border:1px solid black" align="center" rowspan="2"><strong>Total</strong></td>
                <td style="border:1px solid black" align="center" rowspan="2"><strong>UOM</strong></td>
                <td style="border:1px solid black" align="center" rowspan="2"><strong>Rate</strong></td>
                <td style="border:1px solid black" align="center" rowspan="2"><strong>Amount</strong></td>
            </tr>
             <tr>
                <td style="border:1px solid black"><strong>Gmts Color</strong> </td>
                <?  				
                foreach($nameArray_color  as $result_color)
                {	     ?>
                <td align="center" style="border:1px solid black"><strong><? echo $color_library[$result_color[csf('gmts_color')]];?></strong></td>
                <?	}    ?>				
               
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
            //$nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and trim_group=".$result_item[csf('trim_group')]." order by trim_group "); 
			$nameArray_item_description=sql_select( "select b.description, b.brand_supplier,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=3 and a.trim_group=".$result_item[csf('trim_group')]." and a.status_active=1 and a.is_deleted=0 group by b.description, b.brand_supplier order by trim_group "); 
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
                $nameArray_color_size_qnty=sql_select( "select sum(b.cons) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=3 and a.trim_group=". $result_item[csf('trim_group')]." and b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='". $result_itemdescription[csf('brand_supplier')]."' and a.status_active=1 and a.is_deleted=0 and b.item_color=".$result_color[csf('color_number_id')]." and b.color_number_id=".$result_color[csf('gmts_color')]."");
					}
					if($db_type==2)
				    {
                $nameArray_color_size_qnty=sql_select( "select sum(b.cons) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=3 and a.trim_group=". $result_item[csf('trim_group')]." and nvl(b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('". $result_itemdescription[csf('brand_supplier')]."',0) and a.status_active=1 and a.is_deleted=0 and nvl(b.item_color,0)=nvl(".$result_color[csf('color_number_id')].",0) and nvl(b.color_number_id,0)=nvl(".$result_color[csf('gmts_color')].",0)");
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
				
				$color_tatal[$result_color[csf('color_number_id')]][$result_color[csf('gmts_color')]]+=$result_color_size_qnty[csf('cons')];
                /*if (array_key_exists($result_color[csf('color_number_id')], $color_tatal))
                {
                $color_tatal[$result_color[csf('color_number_id')]]+=$result_color_size_qnty[csf('cons')];
                }
                else
                {
                $color_tatal[$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('cons')]; 
                }*/
				
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
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
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
                foreach($nameArray_color  as $result_color)
                {
                
                ?>
                <td style="border:1px solid black;  text-align:right">
                <?
                if($color_tatal[$result_color[csf('color_number_id')]][$result_color[csf('gmts_color')]] !='')
                {
                echo number_format($color_tatal[$result_color[csf('color_number_id')]][$result_color[csf('gmts_color')]],2);  
                }
                ?>
                </td>
            <?
            }
            ?>
                <td style="border:1px solid black;  text-align:right">
				<? 
				$item_total_constrast_color=0;
				foreach ($color_tatal as $key => $value)
				{
                $item_total_constrast_color+= array_sum($value);
				}
				echo number_format($item_total_constrast_color,2);  
				?></td>
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
        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=4 and status_active=1 and is_deleted=0 order by trim_group "); 
        $nameArray_size=sql_select( "select distinct b.item_size  as gmts_sizes from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.status_active=1 and a.is_deleted=0 and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=4");
	   

		if(count($nameArray_size)>0)
		{
        ?>
        
        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
             <tr>
                <td colspan="<? echo count($nameArray_size)+10;?>" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]];?></strong>
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
			$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.trim_group=".$result_item[csf('trim_group')]." and a.status_active=1 and a.is_deleted=0 and a.sensitivity=4", "item_color", "color_number_id"  );
			 /*$nameArray_color=sql_select( "select distinct b.item_color as color_number_id,b.description, b.brand_supplier from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.trim_group=".$result_item[csf('trim_group')]." and a.sensitivity=4"); */
			 $nameArray_color=sql_select( "select  b.item_color as color_number_id,b.description, b.brand_supplier,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.trim_group=".$result_item[csf('trim_group')]." and a.status_active=1 and a.is_deleted=0 and a.sensitivity=4 group by b.item_color,b.description, b.brand_supplier"); 
			 
            $nameArray_item_description=sql_select( "select distinct uom from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=4 and status_active=1 and is_deleted=0 and trim_group=".$result_item[csf('trim_group')]." order by trim_group "); 
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
					<td style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; ?> </td>
                    <td style="border:1px solid black"><? echo $color_library[$gmtcolor_library[$result_color[csf('color_number_id')]]]; ?> </td>
					<td style="border:1px solid black" ><? echo $result_color[csf('description')]; ?> </td>
					<td style="border:1px solid black" ><? echo $result_color[csf('brand_supplier')]; ?> </td>
					<?
					foreach($nameArray_size  as $result_size)
					{
						if($db_type==0)
				        {
						$nameArray_size_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=4 and a.status_active=1 and a.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and  b.description='". $result_color[csf('description')]."' and b.brand_supplier='".$result_color[csf('brand_supplier')]."'  and b.item_size='".$result_size[csf('gmts_sizes')]."' and b.item_color=".$result_color[csf('color_number_id')]."");
						}
						if($db_type==2)
				        {
						$nameArray_size_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=4 and a.status_active=1 and a.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and nvl( b.description,0)=nvl('". $result_color[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$result_color[csf('brand_supplier')]."',0)  and nvl(b.item_size,0)=nvl('".$result_size[csf('gmts_sizes')]."',0) and nvl(b.item_color,0)=nvl(".$result_color[csf('color_number_id')].",0)");
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
        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=0 and status_active=1 and is_deleted=0 order by trim_group "); 
        //$nameArray_color=sql_select( "select distinct b.color_number_id from wo_trims_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=1"); 
		//$nameArray_color= array();
		if(count($nameArray_item)>0)
		{
        ?>
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="<? echo count($nameArray_color)+8; ?>" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]];?></strong>
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
				
				$nameArray_item_description=sql_select( "select  b.description, b.brand_supplier,b.item_color,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.trim_group=".$result_item[csf('trim_group')]."  and a.sensitivity=0 and a.status_active=1 and a.is_deleted=0 group by b.description, b.brand_supplier,b.item_color"); 
				
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
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=0 and a.trim_group=". $result_item[csf('trim_group')]." and  b.description='". $result_itemdescription[csf('description')]."' and a.status_active=1 and a.is_deleted=0 and b.brand_supplier='".$result_itemdescription[csf('brand_supplier')]."' and b.item_color='".$result_itemdescription[csf('item_color')]."'");
						}
						if($db_type==2)
				        {
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=0 and a.status_active=1 and a.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and nvl( b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_color,0)=nvl('".$result_itemdescription[csf('item_color')]."',0)");
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
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
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
        <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
        <thead>
            <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Spacial Instruction</th>
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
                $i++;
                ?>
                    <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;">
                        <? echo $row[csf('terms')]; ?>
                        </td>
                    </tr>
                <?
            }
        }
        else
        {
			$i=0;
        $data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1");// quotation_id='$data'
        foreach( $data_array as $row )
            {
                $i++;
        ?>
        <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;">
                        <? echo $row[csf('terms')]; ?>
                        </td>
                        
                    </tr>
        <? 
            }
        } 
        ?>
    </tbody>
    </table>
    </td>
    <td width="2%"></td>
    
    <td width="49%">
     <?
	if($show_comment==1)
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
                     <? echo number_format($pre_amount,4,".",""); ?>
                    </td>
                    <td width="" align="right">
                    <? echo number_format($cu_woq_amount,4,".","");?>
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
    
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
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
	//$po_qnty_tot=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<div style="width:1333px" align="center">       
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100"> 
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
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
		$nameArray_job=sql_select( "select distinct b.job_no  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.status_active=1 and b.is_deleted=0"); 
        foreach ($nameArray_job as $result_job)
        {

			$job_no.=$result_job[csf('job_no')].", ";
		}
		$buyer_string="";
		
		$nameArray_buyer=sql_select( "select distinct a.buyer_name  from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active=1 and b.is_deleted=0"); 
        foreach ($nameArray_buyer as $result_buy)
        {
			$buyer_string.=$buyer_name_arr[$result_buy[csf('buyer_name')]].",";
		}
		
		$po_no="";$file_no="";$ref_no="";$ship_date="";
		$nameArray_job=sql_select( "select distinct b.po_number,b.grouping,b.file_no,b.pub_shipment_date from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0"); 
        foreach ($nameArray_job as $result_job)
        {
			$po_no.=$result_job[csf('po_number')].", ";
			$file_no.=$result_job[csf('file_no')].", ";
			$ref_no.=$result_job[csf('grouping')].", ";
			if($ship_date=="") $ship_date=change_date_format($result_job[csf('pub_shipment_date')]);else $ship_date.=",".change_date_format($result_job[csf('pub_shipment_date')]);
		}
		$ship_dates=implode(",",array_unique(explode(",",$ship_date)));
		$style_ref="";
		$nameArray_style=sql_select( "select distinct a.style_ref_no  from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no and b.status_active=1 and b.is_deleted=0"); 
        foreach ($nameArray_style as $result_style)
        {
			$style_ref.=$result_style[csf('style_ref_no')].", ";
		}
        $nameArray=sql_select( "select a.booking_no,a.pay_mode,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source,a.remarks  from wo_booking_mst a where  a.booking_no=$txt_booking_no"); 
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
                <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
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
               	<td >:&nbsp;<? echo $supplier_address_arr[$result[csf('supplier_id')]];?></td>
                 <td width="100" style="font-size:12px"><b>Shipment Date</b></td>
               	<td>:&nbsp;<? echo $ship_dates;?></td>
               	
            </tr> 
            <tr>
                <td width="100" style="font-size:12px"><b>Buyer</b>   </td>
                <td width="110" colspan="2">:&nbsp;
				<? 
				echo rtrim($buyer_string,", ");
				?> 
                </td>
                <td width="110" style="font-size:12px"><b>Style</b> </td>
                <td  width="100" style="font-size:12px" colspan="2">:&nbsp;<? echo $style_sting=rtrim($style_ref,", "); ?> </td>
                 
               	
            </tr> 
            <tr>
                <td width="100" style="font-size:12px"><b>Job No</b>   </td>
                <td width="110" colspan="2">:&nbsp;
				<? 
				echo $job_no=rtrim($job_no,", ");
				?> 
                </td>
                 
               	<td width="110" style="font-size:12px"><b>PO No</b> </td>
                <td  width="100" style="font-size:12px" colspan="2">:&nbsp;<? echo rtrim($po_no,", "); ?> </td>
            </tr> 
            <tr>
                <td width="100" style="font-size:12px"><b>Remarks</b>   </td>
                <td width="110">:&nbsp;
				<? 
				echo rtrim($file_no,", ");
				?> 
                </td>
                <td width="100" style="font-size:12px"><b>Remarks</b>   </td>
                <td width="110">:&nbsp;
				<? 
				echo rtrim($ref_no,", ");
				?> 
                </td>
                
                <td width="100" style="font-size:12px"><b>Remarks</b>   </td>
                <td width="110">:&nbsp;
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
		$nameArray_job_po=sql_select( "select job_no,po_break_down_id from wo_booking_dtls  where booking_no=$txt_booking_no and status_active=1 and is_deleted=0 group by job_no,po_break_down_id order by job_no,po_break_down_id "); 
		foreach($nameArray_job_po as $nameArray_job_po_row)
		{
		
       // $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]."  and sensitivity=1 order by trim_group ");
		 $nameArray_item=sql_select( "select  id,trim_group,country_id_string from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]."  and status_active=1 and is_deleted=0 and sensitivity=1 order by trim_group ");
		
		
       // $nameArray_color=sql_select( "select distinct b.color_number_id from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=1");
	   if(count($nameArray_item)>0)
		{
        ?>
        &nbsp;
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
           <tr>
                <td colspan="9" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]];?></strong>
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
            $nameArray_item_description=sql_select( "select b.description, b.brand_supplier,b.item_color,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=1 and  a.id= ".$result_item[csf('id')]." and a.trim_group=".$result_item[csf('trim_group')]." and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 group by b.description, b.brand_supplier,b.item_color order by trim_group "); 
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
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
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
		
		$nameArray_item=sql_select( "select  id,trim_group,country_id_string from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=2 and status_active=1 and is_deleted=0 order by trim_group "); 
        //$nameArray_size=sql_select( "select distinct b.item_size  as gmts_sizes from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=2");
		if(count($nameArray_item)>0)
		{
        ?>
        
        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="9" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]];?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="9" align="">
                <strong>Size Sensitive </strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand/Supplier Ref.</strong> </td>
                <td align="center" style="border:1px solid black"><strong>Item Size</strong></td>
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
			$nameArray_item_description=sql_select( "select b.description, b.brand_supplier,b.item_size,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and   a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=2 and  a.id= ".$result_item[csf('id')]." and a.status_active=1 and a.is_deleted=0 and a.trim_group=".$result_item[csf('trim_group')]." and b.requirment !=0 group by b.description, b.brand_supplier,b.item_size order by b.item_size"); 
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
				<? 
				 echo number_format($result_itemdescription[csf('cons')],4);
                $item_desctiption_total += $result_itemdescription[csf('cons')] ;
				?>
                </td>
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
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
                <td align="right" style="border:1px solid black"  colspan="8"><strong>Total</strong></td>
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
		
		 $nameArray_item=sql_select( "select  id,trim_group,country_id_string from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and status_active=1 and is_deleted=0 and sensitivity=3 order by trim_group "); 
        //$nameArray_color=sql_select( "select distinct b.item_color as color_number_id from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=3"); 
		if(count($nameArray_item)>0)
		{
        ?>
        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
           <tr>
                <td colspan="10" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]];?></strong>
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
				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=3 and  a.id= ".$result_item[csf('id')]." and a.trim_group=".$result_item[csf('trim_group')]." and b.requirment !=0  and a.status_active=1 and a.is_deleted=0 order by trim_group ", "item_color", "color_number_id"  );

			$nameArray_item_description=sql_select( "select b.description, b.brand_supplier,b.item_color,b.color_number_id,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=3 and  a.id= ".$result_item[csf('id')]." and a.trim_group=".$result_item[csf('trim_group')]." and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 group by b.description, b.brand_supplier,b.item_color,b.color_number_id order by trim_group "); 
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
               <?
			   //echo $color_library[$gmtcolor_library[$result_itemdescription[csf('item_color')]]];
			   echo $color_library[$result_itemdescription[csf('color_number_id')]]; 
			   ?>
                </td>
               
                
                <td style="border:1px solid black; text-align:right">
				<? 
				echo number_format($result_itemdescription[csf('cons')],4);
                $item_desctiption_total += $result_itemdescription[csf('cons')] ;
				?>
                </td>
                 <td style="border:1px solid black; text-align:center"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
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
		
		$nameArray_item=sql_select( "select  id,trim_group,country_id_string from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and sensitivity=4 and status_active=1 and is_deleted=0 order by trim_group ");
       // $nameArray_size=sql_select( "select distinct b.item_size  as gmts_sizes from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=4");
	   if(count($nameArray_item)>0)
		{
        ?>
        
        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
             <tr>
                <td colspan="11" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]];?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="11" align="">
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
				$gmtcolor_library=return_library_array( "select b.item_color,b.color_number_id from wo_booking_dtls a,  wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and  a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=4 and  a.id= ".$result_item[csf('id')]." and a.trim_group=".$result_item[csf('trim_group')]." and b.requirment !=0  and a.status_active=1 and a.is_deleted=0 order by trim_group ", "item_color", "color_number_id"  );
			
			 $nameArray_color=sql_select( "select  b.item_color as color_number_id,b.item_size,b.description, b.brand_supplier,sum(b.requirment) as cons,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and  a.id= ".$result_item[csf('id')]." and a.trim_group=".$result_item[csf('trim_group')]." and a.sensitivity=4 and b.requirment !=0 and a.status_active=1 and a.is_deleted=0 group by b.item_color,b.item_size,b.description, b.brand_supplier order by b.item_color "); 
			 
            $nameArray_item_description=sql_select( "select distinct uom from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and status_active=1 and is_deleted=0 and sensitivity=4 and trim_group=".$result_item[csf('trim_group')]." order by trim_group "); 
            ?>
            <tr>
                <td style="border:1px solid black" rowspan="<? echo   (count($nameArray_item_description)*count($nameArray_color)); ?>">
                <? echo $i ; ?>
                </td>
                <td align="center" style="border:1px solid black" rowspan="<? echo (count($nameArray_item_description)*count($nameArray_color)); ?>">
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
                    <td style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; ?> </td>
                     <td style="border:1px solid black"><? echo $color_library[$gmtcolor_library[$result_color[csf('color_number_id')]]]; ?> </td>
					<td style="border:1px solid black; text-align:right">
					<? echo $result_color[csf('item_size')]; ?> 
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
                <td style="border:1px solid black;  text-align:right" colspan="7"><strong> Item Total</strong></td>
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
                <td align="right" style="border:1px solid black"  colspan="10"><strong>Total</strong></td>
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
		$nameArray_item=sql_select( "select  id,trim_group,country_id_string from wo_booking_dtls  where booking_no=$txt_booking_no and job_no='".$nameArray_job_po_row[csf('job_no')]."' and po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and status_active=1 and is_deleted=0 and sensitivity=0 order by trim_group ");
        //$nameArray_color=sql_select( "select distinct b.color_number_id from wo_trims_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=1"); 
		//$nameArray_color= array();
		if(count($nameArray_item)>0)
		{
        ?>
        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="<? echo count($nameArray_color)+8; ?>" align="">
                <strong><? echo "Job NO:".$nameArray_job_po_row[csf('job_no')]." &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;PO No:" .$po_number[$nameArray_job_po_row[csf('po_break_down_id')]];?></strong>
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
				
				$nameArray_item_description=sql_select( "select  b.description, b.brand_supplier,b.item_color,avg(b.rate) as rate, sum(b.amount) as amount from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and  a.id= ".$result_item[csf('id')]." and a.trim_group=".$result_item[csf('trim_group')]." and a.status_active=1 and a.is_deleted=0 and a.sensitivity=0 group by b.description, b.brand_supplier,b.item_color"); 
				
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
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=0 and  a.id= ".$result_item[csf('id')]." and a.trim_group=". $result_item[csf('trim_group')]." and  b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='".$result_itemdescription[csf('brand_supplier')]."' and a.status_active=1 and a.is_deleted=0 and b.item_color='".$result_itemdescription[csf('item_color')]."'");
						}
						if($db_type==2)
				        {
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.po_break_down_id=".$nameArray_job_po_row[csf('po_break_down_id')]." and a.sensitivity=0 and  a.id= ".$result_item[csf('id')]." and a.trim_group=". $result_item[csf('trim_group')]." and a.status_active=1 and a.is_deleted=0 and nvl( b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_color,0)=nvl('".$result_itemdescription[csf('item_color')]."',0)");
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
                <td style="border:1px solid black; text-align:right"><? echo number_format($result_itemdescription[csf('rate')],4); ?> </td>
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
        <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
        <thead>
            <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Spacial Instruction</th>
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
                $i++;
                ?>
                    <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;">
                        <? echo $row[csf('terms')]; ?>
                        </td>
                    </tr>
                <?
            }
        }
        else
        {
			$i=0;
        $data_array=sql_select("select id, terms from  lib_terms_condition where is_default=1");// quotation_id='$data'
        foreach( $data_array as $row )
            {
                $i++;
        ?>
        <tr id="settr_1" align="" style="border:1px solid black;">
                        <td style="border:1px solid black;">
                        <? echo $i;?>
                        </td>
                        <td style="border:1px solid black;">
                        <? echo $row[csf('terms')]; ?>
                        </td>
                        
                    </tr>
        <? 
            }
        } 
        ?>
    </tbody>
    </table>
    </td>
    <td width="2%"></td>
    
    <td width="49%">
    <?
	if($show_comment==1)
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
                     <? echo number_format($pre_amount,4,".",""); ?>
                    </td>
                    <td width="100" align="right">
                    <? echo number_format($cu_woq_amount,4,".","");?>
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
    
     <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
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
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";disconnect($con); die;}		
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

if ($action=="populate_data_from_search_popup_booking")
{
	$job_no="";
	 $sql= "select booking_no,booking_date,company_id,buyer_id, 	job_no,currency_id,exchange_rate,pay_mode,ready_to_approved,booking_month,supplier_id,attention,remarks,item_from_precost,delivery_date,source,booking_year,is_approved from wo_booking_mst  where booking_no='$data'";     
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
		$job_no=$row[csf("job_no")];
		$data_req=total_cu_data($row[csf("job_no")]);
		$data_req_arr=explode("_",$data_req);
		echo "document.getElementById('txt_tot_req_amount').value = '".$data_req_arr[0]."';\n";
		echo "document.getElementById('txt_tot_cu_amount').value = '".$data_req_arr[1]."';\n";
		echo "load_drop_down( 'requires/trims_booking_controller_v2', '".$row[csf("company_id")].'__'.$row[csf("buyer_id")]."', 'load_drop_down_buyer', 'buyer_td' );\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n"; 
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";  
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "load_drop_down( 'requires/trims_booking_controller_v2', '".$row[csf("pay_mode")]."', 'load_drop_down_supplier', 'supplier_td' );\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_booking_month').value = '".$row[csf("booking_month")]."';\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('cbo_item_from_precost').value = '".$row[csf("item_from_precost")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
	    echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
		echo "document.getElementById('cbo_booking_year').value = '".$row[csf("booking_year")]."';\n";
		echo "document.getElementById('id_approved_id').value = '".$row[csf("is_approved")]."';\n";
		echo " $('#cbo_company_name').attr('disabled',true);\n"; 
		echo " $('#cbo_buyer_name').attr('disabled',true);\n"; 
		echo " $('#cbo_item_from_precost').attr('disabled',true);\n";
		echo " $('#cbo_supplier_name').attr('disabled',true);\n"; 
		echo "get_php_form_data(".$row[csf("company_id")].", 'populate_variable_setting_data', 'requires/trims_booking_controller_v2' );\n";

		if($row[csf("item_from_precost")]==1)
		{
			echo " $('#txt_select_po_num').attr('disabled',true);\n";
			echo " $('#txt_select_po_num').removeAttr('placeholder','Double Click');\n";
			echo " $('#txt_select_po_num').attr('placeholder','No Need');\n";
		}
		if($row[csf("item_from_precost")]==2)
		{
		echo " $('#txt_select_po_num').attr('disabled',false);\n"; 
		echo " $('#txt_select_po_num').removeAttr('placeholder','No Need');\n";
		echo " $('#txt_select_po_num').attr('placeholder','Double Click');\n";
		}
		
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
	$data_array_job=sql_select("select job_no,company_name,buyer_name,currency_id from wo_po_details_master where job_no='$job_no'");
	foreach ($data_array_job as $row_job)
	{
		echo "document.getElementById('cbo_currency_job').value = '".$row_job[csf("currency_id")]."';\n";
	}
}


?>
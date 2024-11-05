<?
/*-------------------------------------------- Comments

Version (MySql)          :  V2
Version (Oracle)         :  V1
Converted by             :  MONZU
Purpose			         : This form will create Trims Booking
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
Updated by 		         : 
Update date		         : 
QC Performed BY	         :		
QC Date			         :	
Comments		         : 
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.commisions.php');
require_once('../../../includes/class4/class.trims.php');
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
$buyer_arr=return_library_array("select id, short_name from lib_buyer",'id','short_name');
$trim_group= return_library_array("select id, item_name from lib_item_group",'id','item_name');

if ($action=="load_drop_down_buyer")
{
	
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","" );
	
} 
function load_drop_down_supplier($data){
	if($data==5 || $data==3){
	   echo create_drop_down( "cbo_supplier_name", 172, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Supplier --", "", "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/trims_booking_controller_v2');",0,"" );
	}
	else
	{
		$cbo_supplier_name= create_drop_down( "cbo_supplier_name", 172, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and b.party_type=4 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value, 'load_drop_down_attention', 'requires/trims_booking_controller');","");
	}
	
	return $cbo_supplier_name;
	exit();
}
if ($action=="load_drop_down_supplier")
{
	echo $action($data);
	//echo create_drop_down( "cbo_supplier_name", 172, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type=4 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value, 'load_drop_down_attention', 'requires/trims_booking_controller');","");
	exit();
}

if($action=="load_drop_down_attention")
{
	$supplier_name=return_field_value("contact_person","lib_supplier","id ='".$data."' and is_deleted=0 and status_active=1");
	echo "document.getElementById('txt_attention').value = '".$supplier_name."';\n";
	exit();	
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
                        <th width="80">File No </th>
                        <th width="80">Ref. No </th>
                        <th width="100">Style</th>
                        <th width="150">Order No</th>
                        <th width="200">Date Range</th>
                        <th>
                        <input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job Without PO
                        </th>   
                        </tr>        
                    </thead>
        			<tr>
                    	<td> 
                        <input type="hidden" id="selected_job">
                        <input type="hidden" id="garments_nature" value="<? echo $garments_nature; ?>">
							<? 
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'short_trims_booking_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
                    </td>
                   	<td id="buyer_td">
                     <? 
						echo create_drop_down( "cbo_buyer_name", 172, $blank_array,'', 1, "-- Select Buyer --" );
					?>	</td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:100px"></td>
                    
                     <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:80px"></td>
                     <td><input name="txt_ref" id="txt_ref" class="text_boxes" style="width:80px"></td>
                      <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:150px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('garments_nature').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref').value, 'create_po_search_list_view', 'search_div', 'short_trims_booking_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
	
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";// else { echo "Please Select Buyer First."; die; }
	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(a.`insert_date`, '-', 1)=$data[7]";
	if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";
	
	//if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num='$data[6]'  $year_cond"; else  $job_cond=""; 
	//if (str_replace("'","",$data[8])!="") $order_cond=" and b.po_number like '%$data[8]%'  "; else  $order_cond=""; 
	//echo $data[11].'='.$data[12];
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
	if (str_replace("'","",$data[11])!="") $file_cond=" and b.file_no= '$data[11]'  "; 
	if (str_replace("'","",$data[12])!="") $ref_cond=" and b.grouping= '$data[12]'  "; 
	
	}
	if($data[10]==2)
	{
	if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '$data[6]%' $year_cond"; //else  $job_cond=""; 
	if (trim($data[8])!="") $style_cond=" and a.style_ref_no like '$data[8]%'  "; //else  $style_cond=""; 
	if (str_replace("'","",$data[9])!="") $order_cond=" and b.po_number like '$data[9]%'  "; //else  $order_cond="";
	if (str_replace("'","",$data[11])!="") $file_cond=" and b.file_no= '$data[11]'  "; 
	if (str_replace("'","",$data[12])!="") $ref_cond=" and b.grouping= '$data[12]'  ";  
	}
	if($data[10]==3)
	{
	if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]' $year_cond"; //else  $job_cond=""; 
	if (trim($data[8])!="") $style_cond=" and a.style_ref_no like '%$data[8]'"; //else  $style_cond=""; 
	if (str_replace("'","",$data[9])!="") $order_cond=" and b.po_number like '%$data[9]'  "; //else  $order_cond=""; 
	if (str_replace("'","",$data[11])!="") $file_cond=" and b.file_no= '$data[11]'  "; 
	if (str_replace("'","",$data[12])!="") $ref_cond=" and b.grouping= '$data[12]'  "; 
	}
	if($data[10]==4 || $data[10]==0)
	{
	if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]%' $year_cond "; //else  $job_cond=""; 
	if (trim($data[8])!="") $style_cond=" and a.style_ref_no like '%$data[8]%'"; //else  $style_cond=""; 
	if (str_replace("'","",$data[9])!="") $order_cond=" and b.po_number like '%$data[9]%'  "; //else  $order_cond="";
	if (str_replace("'","",$data[11])!="") $file_cond=" and b.file_no= '$data[11]'  "; 
	if (str_replace("'","",$data[12])!="") $ref_cond=" and b.grouping= '$data[12]'  ";  
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
	$arr=array (4=>$comp,5=>$buyer_arr,11=>$item_category);
	if ($data[2]==0)
	{
		if($db_type==0)
		{
	 	$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.file_no,b.grouping ,b.shipment_date,a.garments_nature,SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $shipment_date $company $buyer $job_cond $style_cond $order_cond $file_cond $ref_cond order by a.job_no";
		}
		if($db_type==2)
		{
	 	$sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.file_no,b.grouping ,b.po_quantity,b.shipment_date,a.garments_nature,to_char(a.insert_date,'YYYY') as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $shipment_date $company $buyer $job_cond $style_cond $order_cond $file_cond $ref_cond order by a.job_no";
		}
		
		 echo  create_list_view("list_view", "Job No,File No,Ref. No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date,Gmts Nature", "50,80,80,60,120,100,100,90,90,90,80,80","1160","320",0, $sql , "js_set_value", "job_no", "", 1, "0,0,0,0,company_name,buyer_name,0,0,0,0,0,garments_nature", $arr , "job_no_prefix_num,file_no,grouping,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date,garments_nature", "",'','0,0,0,0,0,0,0,1,0,1,3,0');
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
		$sql= "select a.job_no_prefix_num,a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.garments_nature,to_char(a.insert_date,'YYYY') as year from wo_po_details_master a where a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.garments_nature=$data[5] and a.status_active=1 ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')."  and a.is_deleted=0 $company $buyer order by a.job_no";
		}
		
		echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Gmts Nature", "90,80,120,100,100,90","1000","320",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_name,buyer_name,0,garments_nature", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,garments_nature", "",'','0,0,0,0,0,0');
	}
} 


if ($action=="populate_data_from_search_popup")
{
	$data_array=sql_select("select job_no,company_name,buyer_name from wo_po_details_master where job_no='$data'");
	foreach ($data_array as $row)
	{
		echo "load_drop_down( 'requires/short_trims_booking_controller', '".$row[csf("company_name")]."', 'load_drop_down_buyer', 'buyer_td' );\n";
		//echo "load_drop_down( 'requires/short_trims_booking_controller', '".$row[csf("company_name")]."', 'load_drop_down_supplier', 'supplier_td' );\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";  
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n"; 
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n"; 
		echo " $('#cbo_company_name').attr('disabled',true);\n"; 
		echo " $('#cbo_buyer_name').attr('disabled',true);\n"; 
	}
}


if ($action=="fnc_process_data")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
?>
	<script>
	  var selected_id = new Array, selected_name = new Array();	
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
<div align="center" style="width:1270px;" >
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
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1270" class="rpt_table" >
                <thead>
                    <th width="40">SL</th>
                    <th width="50">Buyer</th>
                    <th width="100">Job No</th>
                    <th width="80">File No</th>
                    <th width="50">Ref. no</th>
                    <th width="80">Style No</th>
                    <th width="100">Ord. No</th>
                   
                    <th width="100">Trims Group</th>
                    <th width="130">Description</th>
                    <th width="100">Brand / Supp. Ref</th>
                    <th width="80">Req. Qnty</th>
                    <th width="50">UOM</th>
                    <th width="80">CU WOQ</th>
                    <th width="80">Bal WOQ</th>
                    <th width="80">Rate</th>
                    <th width="">Amount</th>
                </thead>
            </table>
            <div style="width:1290px; overflow-y:scroll; max-height:350px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1270" class="rpt_table" id="tbl_list_search" >
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
					/*$sql="select
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
					d.file_no,
					d.grouping, 
					
					d.plan_cut,
					e.id as id,
					e.po_break_down_id,
					e.cons,
					sum(f.wo_qnty) as cu_woq
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
					d.is_deleted=0 and  f.wo_qnty>0 and
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
					d.file_no,
					d.grouping,
					d.plan_cut,
					e.po_break_down_id,
					e.cons
					order by d.id,c.id";
					*/
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
					d.file_no,
					d.grouping, 
					
					d.plan_cut,
					min(e.id) as id,
					sum(f.wo_qnty) as cu_woq
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
					d.is_deleted=0 and  f.wo_qnty>0 and
					d.status_active=1
					group by 
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
					d.file_no,
					d.grouping,
					d.plan_cut
					
					order by d.id,c.id";
					$i=1;
					$total_req=0;
					$total_amount=0;
                    $nameArray=sql_select( $sql );
					//echo $sql;
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
						$po_ids=$selectResult[csf('po_id')];
						 $condition= new condition();
                        if($po_ids!=''){ 
							$condition->po_id("in(".$po_ids.")");
                        }
                        
                        $condition->init();
                        $trims= new trims($condition);
						//echo $trims->getQuery();die;
						$trim_arr_qty=$trims->getQtyArray_by_precostdtlsid();
						//print_r($trim_arr_amount);
						
						//$req_qnty=def_number_format(($selectResult[csf('cons')]*($selectResult[csf('plan_cut')]/12))/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],5,"");
						$req_qnty=$trim_arr_qty[$selectResult[csf('wo_pre_cost_trim_cost_dtls')]];
							
						$bal_woq=def_number_format($req_qnty-$selectResult[csf('cu_woq')],5,"");
					//	echo $req_qnty.'='.$selectResult[csf('cu_woq')].'<br>';
						$rate=def_number_format(($selectResult[csf('rate')]*$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor]),5,"");
						
						if($bal_woq<=0)
						{
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
                    <td width="40"><? echo $i;?>
                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>	
                    </td>
                    <td width="50"><? echo $buyer_arr [$selectResult[csf('buyer_name')]];?></td>
                    <td width="100"><? echo $selectResult[csf('job_no_prefix_num')];?></td>
                    <td width="80"><? echo $selectResult[csf('file_no')];?></td>
                    <td width="50"><? echo $selectResult[csf('grouping')];?></td>
                    <td width="80"><? echo $selectResult[csf('style_ref_no')];?></td>
                    <td width="100"><? echo $selectResult[csf('po_number')];?></td>
                    
                    <td width="100"><? echo $trim_group[$selectResult[csf('trim_group')]];?></td>
                    <td width="130"><? echo $selectResult[csf('description')];?></td>
                    <td width="100"><? echo $selectResult[csf('brand_sup_ref')];?></td>
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
             <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1270" class="rpt_table" >
                <tfoot>
                    <th width="40"></th>
                    
                    <th width="50"></th>
                    <th width="100"></th>
                   
                    <th width="80"></th>
                    <th width="50"></th>
                    <th width="80"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="130"></th>
                    <th width="100"></th>
                    <th width="80" id="value_total_req"><? echo number_format($total_req,4); ?></th>
                    <th width="50"></th>
                   
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="" id="value_total_amount"><? echo number_format($total_amount,4); ?></th>
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
								   col: [10,15],
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
	$data=str_replace("'","",$data);
	?>
   <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1567" class="rpt_table" >
                <thead>
                    <th width="40">SL</th>
                    <th width="50">Buyer</th>
                    <th width="100">Job No</th>
                    <th width="80">Style No</th>
                    <th width="100">Ord. No</th>
                    <th width="100">Trims Group</th>
                    <th width="150" class="must_entry_caption">Description</th>
                    <th width="100">Brand/ Supp. Ref</th>
                    <th width="80">Req. Qnty</th>
                    <th width="80">UOM</th>
                    <th width="80">CU WOQ</th>
                    <th width="80">Bal WOQ</th>
                    <th width="100">Sensitivity</th>
                    <th width="80">WOQ</th>
                    <th width="80">Rate</th>
                    <th width="80">Amount</th>
                    <th width="">Del. Date</th>
                </thead>
            </table>
            <div style="width:1567px; overflow-y:scroll; max-height:350px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1550" class="rpt_table" id="tbl_list_search" >
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
					/*$sql="select
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
					d.plan_cut,
					e.id as id,
					e.po_break_down_id,
					e.cons,
					sum(f.wo_qnty) as cu_woq
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
					c.id,
					c.trim_group,
					c.description,
					c.brand_sup_ref,
					c.rate,
					d.id,
					d.po_number,
					d.plan_cut,
					e.po_break_down_id,
					e.cons
					order by d.id,c.id";
					*/	
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
					d.plan_cut,
					
					sum(f.wo_qnty) as cu_woq
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
					d.plan_cut
					
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
						$po_ids=$selectResult[csf('po_id')];
						 $condition= new condition();
                        if($po_ids!=''){ 
							$condition->po_id("in(".$po_ids.")");
                        }
                        
                        $condition->init();
                        $trims= new trims($condition);
						//echo $trims->getQuery();die;
						$trim_arr_qty=$trims->getQtyArray_by_precostdtlsid();
						//wo_pre_cost_trim_cost_dtls
						//$req_qnty=def_number_format(($selectResult[csf('cons')]*($selectResult[csf('plan_cut')]/12))/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],5,"");
						$req_qnty=$trim_arr_qty[$selectResult[csf('wo_pre_cost_trim_cost_dtls')]];
						$bal_woq=def_number_format($req_qnty-$selectResult[csf('cu_woq')],5,"");
						$rate=def_number_format(($selectResult[csf('rate')]*$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor]),8,"");
	   ?>
       <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                    <td width="40"><? echo $i;?></td>
                    <td width="50"><? echo $buyer_arr [$selectResult[csf('buyer_name')]];?></td>
                    <td width="100"> <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; background-color:<? echo $bgcolor; ?>" id="txtjob_<? echo $i;?>" value="<? echo $selectResult[csf('job_no')];?>" readonly/>  <input type="hidden" id="txtbookingid_<? echo $i;?>" value="" readonly/></td>
                    <td width="80"><? echo $selectResult[csf('style_ref_no')];?></td>
                    <td width="100"><? echo $selectResult[csf('po_number')];?> <input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $selectResult[csf('po_id')];?>" readonly/></td>
                    <td width="100"><? echo $trim_group[$selectResult[csf('trim_group')]];?> <input type="hidden" id="txttrimcostid_<? echo $i;?>" value="<? echo $selectResult[csf('wo_pre_cost_trim_cost_dtls')];?>" readonly/> <input type="hidden" id="txttrimgroup_<? echo $i;?>" value="<? echo $selectResult[csf('trim_group')];?>" readonly/></td>
                    <td width="150" ><input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; background-color:<? echo $bgcolor; ?>" id="txtdescription_<? echo $i;?>" value="<? echo $selectResult[csf('description')];?>"  onBlur="copy_value(this.value,'txtdescription_',<? echo $i;?>)"/></td>
                    <td width="100">
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; background-color:<? echo $bgcolor; ?>" id="txtbrandsupref_<? echo $i;?>" value="<? echo $selectResult[csf('brand_sup_ref')];?>"  onBlur="copy_value(this.value,'txtbrandsupref_',<? echo $i;?>)"  />
                    </td>
                    <td width="80" align="right">
					<?
					//echo number_format($selectResult[csf('req_qnty')],4);
					
					?>
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i;?>" value="<? echo $req_qnty;?>"  readonly  />
                    </td>
                    <td width="80">
					<? echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];?> 
                    <input type="hidden" id="txtuom_<? echo $i;?>" value="<? echo $sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom];?>" readonly />
                    </td>
                    <td width="80" align="right">
					<? //echo number_format($selectResult[csf('cu_woq')],4);?>
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i;?>" value="<? echo $selectResult[csf('cu_woq')];?>"  readonly  />
                    </td>
                    <td width="80" align="right">
					<? // echo number_format($selectResult[csf('bal_woq')],4);?>
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i;?>" value="<? echo $bal_woq; ?>"  readonly  />
                    </td>
                    <td width="100" align="right">
					<?  echo create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", "", "","","" ); ?>
                    </td>
                    <td width="80" align="right">
					
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo $bal_woq;?>" onClick="open_consumption_popup('requires/short_trims_booking_controller.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<? echo $i;?>',<? echo $i;?>)"     />
                    </td>
                    <td width="80" align="right"><? //echo $selectResult[csf('rate')];?> 
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo $rate;?>" onChange="calculate_amount(<? echo $i; ?>)" />
                    
                     <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i;?>" value="<? echo $rate;?>" readonly />
                     
                    </td>
                    <td width="80" align="right">
					<?
					$amount=def_number_format($rate*$bal_woq,8,"");
					$total_amount+=$amount;
					?>
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo $amount;?>"  readonly  />
                    </td>
                    <td width="" align="right"><? //echo $selectResult[csf('rate')];?> 
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
 
    <table width="1567" class="rpt_table" border="0" rules="all">
    <tfoot>
    <tr>
	   
                    <th width="40">SL</th>
                    
                    <th width="50"></th>
                    <th width="100"></th>
                    <th width="80"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="150"></th>
                    <th width="100"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="100"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="80"></th>
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
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1687" class="rpt_table" >
                <thead>
                    <th width="40">SL</th>
                    <th width="100">Booking No</th>
                    <th width="50">Buyer</th>
                    <th width="100">Job No</th>
                    <th width="80">Style No</th>
                    <th width="100">Ord. No</th>
                    <th width="100">Trims Group</th>
                    <th width="150" class="must_entry_caption">Description</th>
                    <th width="100">Brand/ Supp. Ref</th>
                    <th width="80">Req. Qnty</th>
                    <th width="80">UOM</th>
                    <th width="80">CU WOQ</th>
                    <th width="80">Bal WOQ</th>
                    <th width="100">Sensitivity</th>
                    <th width="80">WOQ</th>
                    <th width="80">Rate</th>
                    <th width="80">Amount</th>
                    <th width="">Del. Date</th>
                </thead>
            </table>
            <div style="width:1687px; overflow-y:scroll; max-height:350px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1669" class="rpt_table" id="tbl_list_search" >
       <tbody>
       <?
					//$sql="select a.id,a.buyer_name,a.job_no,a.style_ref_no,a.po_number,a.wo_pre_cost_trim_cost_dtls,a.po_id,a.trim_group,b. 	description,b.brand_supplier,a.req_qnty,a.cons_uom,a.cu_woq,a.bal_woq,b.id as booking_id,b.booking_no 	,b.rate,b.sensitivity,b.wo_qnty,b.rate,b.amount,b.delivery_date,b.cons_break_down from wo_trim_booking_data_park  a, wo_booking_dtls b where a.job_no=b.job_no and a.po_id=b.po_break_down_id and a.wo_pre_cost_trim_cost_dtls=b.pre_cost_fabric_cost_dtls_id  and b.booking_no=$txt_booking_no order by trim_group";
					/*$sql="select m.id,m.buyer_name,m.job_no_prefix_num,m.job_no,m.style_ref_no,m.po_number,m.wo_pre_cost_trim_cost_dtls,m.po_id,m.trim_group,n. 	description,n.brand_supplier,m.req_qnty,m.cons_uom,m.cu_woq,m.bal_woq,n.id as booking_id,n.booking_no,  n.rate,n.sensitivity,n.wo_qnty,n.rate,n.amount,n.delivery_date,n.cons_break_down from (select  d.id as id,a.job_no_prefix_num,a.job_no,a.company_name,b.pub_shipment_date,b.grouping,c.id as wo_pre_cost_trim_cost_dtls,d.cons,e.costing_per,a.buyer_name,a.style_ref_no,b.id as po_id,b.po_number,c.trim_group,c.description,c.brand_sup_ref,
CASE e.costing_per WHEN 1 THEN round(((d.cons/12)*b.po_quantity)/cc.conversion_factor,4) WHEN 2 THEN round(((d.cons/1)*b.po_quantity)/cc.conversion_factor,4)  WHEN 3 THEN round(((d.cons/24)*b.po_quantity)/cc.conversion_factor,4) WHEN 4 THEN round(((d.cons/36)*b.po_quantity)/cc.conversion_factor,4) WHEN 5 THEN round(((d.cons/48)*b.po_quantity)/cc.conversion_factor,4) ELSE 0 END as req_qnty,cc.order_uom as cons_uom,IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0)  as cu_woq, CASE e.costing_per WHEN 1 THEN round((((d.cons/12)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 2 THEN round((((d.cons/1)*b.po_quantity)/cc.conversion_factor) - IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4)  WHEN 3 THEN round((((d.cons/24)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 4 THEN round((((d.cons/36)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 5 THEN round((((d.cons/48)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) ELSE 0 END as bal_woq, round((c.rate*cc.conversion_factor),8) as rate from wo_po_details_master a, wo_po_break_down b ,wo_pre_cost_mst e,wo_pre_cost_trim_cost_dtls c ,lib_item_group cc, wo_pre_cost_trim_co_cons_dtls d left join wo_booking_dtls f on f.job_no=d.job_no and f.pre_cost_fabric_cost_dtls_id=d.wo_pre_cost_trim_cost_dtls_id and f.po_break_down_id=d.po_break_down_id and f.booking_type=2   where a.job_no=b.job_no_mst and  a.job_no=c.job_no and a.job_no=e.job_no and  a.job_no=d.job_no   and c.id=d.wo_pre_cost_trim_cost_dtls_id and b.id=d.po_break_down_id and cc.id=c.trim_group and b.pub_shipment_date between '$start_date' and '$end_date'  and a.company_name=$cbo_company_name group by d.id order by b.id) m, wo_booking_dtls n where m.job_no=n.job_no and m.po_id=n.po_break_down_id and m.wo_pre_cost_trim_cost_dtls=n.pre_cost_fabric_cost_dtls_id  and n.booking_no=$txt_booking_no order by trim_group";*/
					
					/*$sql="select m.id,m.buyer_name,m.job_no_prefix_num,m.job_no,m.style_ref_no,m.po_number,m.wo_pre_cost_trim_cost_dtls,m.po_id,m.trim_group,n. 	description,n.brand_supplier,m.req_qnty,m.cons_uom,m.cu_woq,m.bal_woq,n.id as booking_id,n.booking_no,  m.rate as precost_rate,n.sensitivity,n.wo_qnty,n.rate,n.amount,n.delivery_date,n.cons_break_down from (select  d.id as id,a.job_no_prefix_num,a.job_no,a.company_name,b.pub_shipment_date,b.grouping,c.id as wo_pre_cost_trim_cost_dtls,d.cons,e.costing_per,a.buyer_name,a.style_ref_no,b.id as po_id,b.po_number,c.trim_group,c.description,c.brand_sup_ref,
CASE e.costing_per WHEN 1 THEN round(((d.cons/12)*b.po_quantity)/cc.conversion_factor,4) WHEN 2 THEN round(((d.cons/1)*b.po_quantity)/cc.conversion_factor,4)  WHEN 3 THEN round(((d.cons/24)*b.po_quantity)/cc.conversion_factor,4) WHEN 4 THEN round(((d.cons/36)*b.po_quantity)/cc.conversion_factor,4) WHEN 5 THEN round(((d.cons/48)*b.po_quantity)/cc.conversion_factor,4) ELSE 0 END as req_qnty,cc.order_uom as cons_uom,IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0)  as cu_woq, CASE e.costing_per WHEN 1 THEN round((((d.cons/12)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 2 THEN round((((d.cons/1)*b.po_quantity)/cc.conversion_factor) - IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4)  WHEN 3 THEN round((((d.cons/24)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 4 THEN round((((d.cons/36)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) WHEN 5 THEN round((((d.cons/48)*b.po_quantity)/cc.conversion_factor)- IF(f.wo_qnty != '', round(sum(f.wo_qnty),4), 0),4) ELSE 0 END as bal_woq, round((c.rate*cc.conversion_factor),8) as rate from wo_po_details_master a, wo_po_break_down b ,wo_pre_cost_mst e,wo_pre_cost_trim_cost_dtls c ,lib_item_group cc, wo_pre_cost_trim_co_cons_dtls d left join wo_booking_dtls f on f.job_no=d.job_no and f.pre_cost_fabric_cost_dtls_id=d.wo_pre_cost_trim_cost_dtls_id and f.po_break_down_id=d.po_break_down_id and f.booking_type=2   where a.job_no=b.job_no_mst and  a.job_no=c.job_no and a.job_no=e.job_no and  a.job_no=d.job_no   and c.id=d.wo_pre_cost_trim_cost_dtls_id and b.id=d.po_break_down_id and cc.id=c.trim_group  and a.company_name=$cbo_company_name $txt_job_no_cond $cbo_buyer_name_cond and a.garments_nature=$garments_nature  group by d.id order by b.id) m, wo_booking_dtls n where m.job_no=n.job_no and m.po_id=n.po_break_down_id and m.wo_pre_cost_trim_cost_dtls=n.pre_cost_fabric_cost_dtls_id  and n.booking_no=$txt_booking_no order by trim_group"; //and b.pub_shipment_date between '$start_date' and '$end_date' */
					
					$sql_lib_item_group_array=array();
					$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
					foreach($sql_lib_item_group as $row_sql_lib_item_group)
					{
					$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
					$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
					$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
					}	
					/* $sql="select
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
					n.description,
					n.brand_supplier,
					n.id as booking_id,
					n.booking_no,
					n.sensitivity,
					n.wo_qnty,
					n.rate,
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
					d.id as po_id,
					d.po_number,
					d.plan_cut,
					e.id as id,
					e.po_break_down_id,
					e.cons,
					sum(f.wo_qnty) as cu_woq
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
					d.id,
					d.po_number,
					d.plan_cut,
					e.po_break_down_id,
					e.cons
					order by d.id,c.id) m, wo_booking_dtls n where m.job_no=n.job_no and m.po_id=n.po_break_down_id and m.wo_pre_cost_trim_cost_dtls=n.pre_cost_fabric_cost_dtls_id  and n.booking_no=$txt_booking_no order by m.po_id,m.wo_pre_cost_trim_cost_dtls ";*/
					
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
					m.cu_woq,
					n.description,
					n.brand_supplier,
					n.id as booking_id,
					n.booking_no,
					n.sensitivity,
					n.wo_qnty,
					n.rate,
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
					d.id as po_id,
					d.po_number,
					d.plan_cut,
					sum(f.wo_qnty) as cu_woq
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
					d.plan_cut
					
					order by d.id,c.id) m, wo_booking_dtls n where m.job_no=n.job_no and m.po_id=n.po_break_down_id and m.wo_pre_cost_trim_cost_dtls=n.pre_cost_fabric_cost_dtls_id  and n.booking_no=$txt_booking_no order by m.po_id,m.wo_pre_cost_trim_cost_dtls ";

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
						$po_ids=$selectResult[csf('po_id')];
						 $condition= new condition();
                        if($po_ids!=''){ 
							$condition->po_id("in(".$po_ids.")");
                        }
                        
                        $condition->init();
                        $trims= new trims($condition);
						//echo $trims->getQuery();die;
						$trim_arr_qty=$trims->getQtyArray_by_precostdtlsid();
						
						//$req_qnty=def_number_format(($selectResult[csf('cons')]*($selectResult[csf('plan_cut')]/12))/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],5,"");
						$req_qnty=$trim_arr_qty[$selectResult[csf('wo_pre_cost_trim_cost_dtls')]];
						$bal_woq=def_number_format($req_qnty-$selectResult[csf('cu_woq')],5,"");
						$rate=def_number_format(($selectResult[csf('rate')]*$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor]),8,"");
						
	   ?>
       <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                    <td width="40"><? echo $i;?></td>
                    <td width="100"> <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; background-color:<? echo $bgcolor; ?>" id="txtbooking_<? echo $i;?>" value="<? echo $selectResult[csf('booking_no')];?>" readonly/> <input type="hidden" id="txtbookingid_<? echo $i;?>" value="<? echo $selectResult[csf('booking_id')];?>" readonly/></td>
                    <td width="50"><? echo $buyer_arr [$selectResult[csf('buyer_name')]];?></td>
                    <td width="100"> <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; background-color:<? echo $bgcolor; ?>" id="txtjob_<? echo $i;?>" value="<? echo $selectResult[csf('job_no')];?>" readonly/></td>
                    <td width="80"><? echo $selectResult[csf('style_ref_no')];?></td>
                    <td width="100">
					<? echo $selectResult[csf('po_number')];?> 
                    <input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $selectResult[csf('po_id')];?>" readonly/>
                    </td>
                    <td width="100">
					<? echo $trim_group[$selectResult[csf('trim_group')]];?> 
                    <input type="hidden" id="txttrimcostid_<? echo $i;?>" value="<? echo $selectResult[csf('wo_pre_cost_trim_cost_dtls')];?>" readonly/> 
                    <input type="hidden" id="txttrimgroup_<? echo $i;?>" value="<? echo $selectResult[csf('trim_group')];?>" readonly/>
                    </td>
                    <td width="150" >
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; background-color:<? echo $bgcolor; ?>" id="txtdescription_<? echo $i;?>" value="<? echo $selectResult[csf('description')];?>"   onBlur="copy_value(this.value,'txtdescription_',<? echo $i;?>)"/>
                    </td>
                    <td width="100">
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; background-color:<? echo $bgcolor; ?>" id="txtbrandsupref_<? echo $i;?>" value="<? echo $selectResult[csf('brand_supplier')];?>"   onBlur="copy_value(this.value,'txtbrandsupref_',<? echo $i;?>)"  />
                    </td>
                    <td width="80" align="right">
					<?
					//echo number_format($selectResult[csf('req_qnty')],4);
					
					?>
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; text-align:right; background-color:<? echo $bgcolor; ?>" id="txtreqqnty_<? echo $i;?>" value="<? echo $req_qnty;?>"  readonly  />
                    </td>
                    <td width="80">
                    <?
					echo $unit_of_measurement[$sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]];
					?>
                    <input type="hidden" id="txtuom_<? echo $i;?>" value="<? echo $sql_lib_item_group_array[$selectResult[csf('trim_group')]][cons_uom]; ?>" readonly />
                    </td>
                    <td width="80" align="right">
					<? $cu_woq=$selectResult[csf('cu_woq')]-$selectResult[csf('wo_qnty')];?>
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i;?>" value="<? echo $cu_woq;?>"  readonly  />
                    </td>
                    <td width="80" align="right">
					<? $bal_woq=$req_qnty-$cu_woq;?>
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalwoq_<? echo $i;?>" value="<? echo $bal_woq;?>"  readonly  />
                    </td>
                    <td width="100" align="right">
					<?  echo create_drop_down( "cbocolorsizesensitive_".$i, 100, $size_color_sensitive,"", 1, "--Select--", $selectResult[csf("sensitivity")], "","","" ); ?>
                    </td>
                    <td width="80" align="right">
					<?// echo number_format($selectResult[csf('bal_woq')],4);?>
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo $selectResult[csf('wo_qnty')];?>" onClick="open_consumption_popup('requires/short_trims_booking_controller.php?action=consumption_popup', 'Consumtion Entry Form','txtpoid_<? echo $i;?>',<? echo $i;?>)"     />
                    </td>
                    <td width="80" align="right"><? //echo $selectResult[csf('rate')];?> 
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo number_format($selectResult[csf('rate')],6);?>"   onChange="calculate_amount(<? echo $i; ?>)" />
                    <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i;?>" value="<? echo number_format($selectResult[csf('precost_rate')],6);?>" readonly />

                    </td>
                    <td width="80" align="right"><? //echo $selectResult[csf('rate')];?> 
                  <? $total_amount+=$selectResult[csf('amount')];?>
                   <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo def_number_format($selectResult[csf('amount')],8,"");?>"  readonly  />
                    </td>
                    <td width="" align="right"><? //echo $selectResult[csf('rate')];?> 
                    <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo def_number_format($selectResult[csf('amount')],8,"");?>"  readonly  />
                    <input type="text"   style="width:70%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i;?>"  class="datepicker" value="<? echo change_date_format($selectResult[csf('delivery_date')],"dd-mm-yyyy","-"); ?>"  readonly  /> 
                    <?
					if($db_type==0)
					{
					$cons_break_down=$selectResult[csf('cons_break_down')]; 
					}
					if($db_type==2)
					{
					$cons_break_down=$selectResult[csf('cons_break_down')];
					}
					?>
                    <input type="hidden" id="consbreckdown_<? echo $i;?>"  value="<? echo $cons_break_down;  ?>"/> 
                    </td>
                    </tr>
	   <?
	   $i++;
					}
       ?>
	</tbody>
    </table>
    </div>
 
    <table width="1687" class="rpt_table" border="0" rules="all">
    <tfoot>
    <tr>
	   
                    <th width="40">SL</th>
                    <th width="100"></th>
                    <th width="50"></th>
                    <th width="100"></th>
                    <th width="80"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="150"></th>
                    <th width="100"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="100"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="80" id="value_total_amount"><? echo number_format($total_amount,2);?></th>
                    <th width=""></th>               
   </tr>
   </tfoot>
   </table>
   
<?
}

if ($action=="consumption_popup")
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
	/*if(txtwoq_qty > txtwoq)
	{
		alert("Breakdown Qnty Exceeds The WO.Qnty")	;
		return;
	}*/

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
			$pcs_value=0;
			$set_item_ratio=return_field_value("set_item_ratio", "wo_po_details_mas_set_details", "id='$po_id'  and gmts_item_id='$cbogmtsitem'");
			if($set_item_ratio==0 || $set_item_ratio=="")
			{
				$set_item_ratio=1;
			}
			$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_id  and variable_list=18 and item_category_id=4 and status_active=1 and is_deleted=0");
		    $po_qty=return_field_value("po_quantity", "wo_po_break_down", "id='$po_id'");

            ?>

           <input type="hidden" id="process_loss_method_id" name="process_loss_method_id" value="<? echo $process_loss_method; ?>"/>
                      <input type="hidden" id="po_qty" name="po_qty" value="<? echo $po_qty; ?>"/>

            	<table width="800" cellspacing="0" class="rpt_table" border="0" id="tbl_consmption_cost" rules="all">
                	<thead>
                    	<tr>
                        	<th width="50">SL</th><th  width="100">PO NO</th><th  width="100">Gmts. Color</th><th  width="100">Gmts. sizes</th><th  width="110">Item Color</th><th width="90">Item Sizes</th><th width="110"> WOQ </th><th width="110" style="display:none">Process Loss %</th><th width="105" style="display:none">Requirment </th><th width="90">Pln.Cut Qnty</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$po_no_library=return_library_array( "select id,po_number from wo_po_break_down where id ='$po_id'", "id", "po_number"  );
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
                                    <input type="text" id="pono_<? echo $i;?>"  name="pono_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $po_no_library[$data[0]]; ?>" readonly />
                                    <input type="hidden" id="ponoid_<? echo $i;?>"  name="ponoid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $data[0]; ?>" readonly />

                                    </td>
                                    <td>
                                    <input type="text" id="pocolor_<? echo $i;?>"  name="pocolor_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $color_library[$data[1]]; ?>" readonly />
                                    <input type="hidden" id="pocolorid_<? echo $i;?>"  name="pocolorid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $data[1]; ?>" readonly/>
                                    </td>
                                    <td>
                                    <input type="text" id="gmtssizes_<? echo $i;?>"  name="gmtssizes_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $size_library[$data[2]]; ?>" readonly />
                                    <input type="hidden" id="gmtssizesid_<? echo $i;?>"  name="gmtssizesid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $data[2]; ?>" readonly />
                                    </td>
                                    <td>
                                    <input type="text" id="diawidth_<? echo $i;?>"    name="diawidth_<? echo $i;?>"  class=" text_boxes" style="width:95px" onChange="copy_value(this.value,'diawidth_',<? echo $i;?>)" value="<? echo $data[3]; ?>"  />    
                                    </td>
                                    <td>
                                    <input type="text" id="itemsizes_<? echo $i;?>"  name="itemsizes_<? echo $i;?>"    class="text_boxes" style="width:75px" onChange="copy_value(this.value,'itemsizes_',<? echo $i;?>)" value="<? echo $data[4]; ?>"   />
                                    </td>
                                    <td>
                                    <input type="text" id="cons_<? echo $i;?>" onBlur="validate_sum( <? echo $i; ?> )" onChange="set_sum_value( 'cons_sum', 'cons_' );set_sum_value( 'requirement_sum', 'requirement_' );calculate_requirement(<? echo $i;?>);copy_value(this.value,'cons_',<? echo $i;?>)"  name="cons_<? echo $i;?>" class="text_boxes_numeric" style="width:95px" value="<? echo $data[5]; ?>"   /> 
                                    </td>
                                    <td  style="display:none">
                                    <input type="text" id="processloss_<? echo $i;?>" onBlur="set_sum_value( 'processloss_sum', 'processloss_' ) "  name="processloss_<? echo $i;?>" class="text_boxes_numeric" style="width:95px" onChange="calculate_requirement(<? echo $i;?>);set_sum_value( 'processloss_sum', 'processloss_' );set_sum_value( 'requirement_sum', 'requirement_' );copy_value(this.value,'processloss_',<? echo $i;?>)" value="<? echo $data[6]; ?>" /> 
                                    </td>
                                    <td  style="display:none">
                                    <input type="text" id="requirement_<? echo $i;?>" onBlur="set_sum_value( 'requirement_sum', 'requirement_' ) "  onChange="set_sum_value( 'requirement_sum', 'requirement_' )"  name="requirement_<? echo $i;?>" class="text_boxes_numeric" style="width:90px" readonly value="<? echo $data[7]; ?>"/> 
                                    </td>
                                    <td>
                                    <input type="text" id="pcs_<? echo $i;?>"  name="pcs_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) "  class="text_boxes_numeric" style="width:75px" value="<? echo $data[8]; ?>"   />
                                    </td>
                                     <td>
                                     <input type="hidden" id="colorsizetableid_<? echo $i;?>"  name="colorsizetableid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $data[9]; ?>" readonly />
                                    <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(<? echo $i;?> ,'tbl_consmption_cost' );" />
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
                                    <input type="text" id="pono_<? echo $i;?>"  name="pono_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('po_number')]; ?>" />
                                    <input type="hidden" id="ponoid_<? echo $i;?>"  name="ponoid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('id')]; ?>" />

                                    </td>
                                    <td>
                                    <input type="text" id="pocolor_<? echo $i;?>"  name="pocolor_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $color_library[$row[csf('color_number_id')]]; ?>" />
                                    <input type="hidden" id="pocolorid_<? echo $i;?>"  name="pocolorid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('color_number_id')]; ?>" />
                                    </td>
                                     <td>
                                    <input type="text" id="gmtssizes_<? echo $i;?>"  name="gmtssizes_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $size_library[$row[csf('size_number_id')]]; ?>">
                                    <input type="hidden" id="gmtssizesid_<? echo $i;?>"  name="gmtssizesid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('size_number_id')]; ?>">
                                    </td>
                                    <td>
                                    <input type="text" id="diawidth_<? echo $i;?>"  value="<? if($cbocolorsizesensitive==1 || $cbocolorsizesensitive==2 || $cbocolorsizesensitive==4   ) {echo $color_library[$row[csf('color_number_id')]];} ?>"  name="diawidth_<? echo $i;?>"  class="text_boxes" style="width:95px" onChange="copy_value(this.value,'diawidth_',<? echo $i;?>)" />    
                                    </td>
                                    <td>
                                    <input type="text" id="itemsizes_<? echo $i;?>"  name="itemsizes_<? echo $i;?>"    class="text_boxes" style="width:75px" onChange="copy_value(this.value,'itemsizes_',<? echo $i;?>)" value="<? echo $data[4]; ?>">
                                    </td>
                                    <td>
                                    <input type="text" id="cons_<? echo $i;?>" onBlur="validate_sum( <? echo $i; ?> )" onChange="set_sum_value( 'cons_sum', 'cons_' );set_sum_value( 'requirement_sum', 'requirement_' );calculate_requirement(<? echo $i;?>);copy_value(this.value,'cons_',<? echo $i;?>)"  name="cons_<? echo $i;?>" class="text_boxes_numeric" style="width:95px"  value="<? echo $txtwoq_cal; ?>"/> 
                                    </td>
                                    <td  style="display:none">
                                    <input type="text" id="processloss_<? echo $i;?>" onBlur="set_sum_value( 'processloss_sum', 'processloss_' ) "  name="processloss_<? echo $i;?>" class="text_boxes_numeric" style="width:95px" onChange="calculate_requirement(<? echo $i;?>);set_sum_value( 'processloss_sum', 'processloss_' );set_sum_value( 'requirement_sum', 'requirement_' );copy_value(this.value,'processloss_',<? echo $i;?>) " value="<? echo $data[6]; ?>" /> 
                                    </td>
                                    <td  style="display:none">
                                    <input type="text" id="requirement_<? echo $i;?>" onBlur="set_sum_value( 'requirement_sum', 'requirement_' ) " onChange="set_sum_value( 'requirement_sum', 'requirement_' )"  name="requirement_<? echo $i;?>" class="text_boxes_numeric" style="width:90px" value="<? echo $data[7]; ?>" readonly /> 
                                    </td>
                                    <td>
                                    <input type="text" id="pcs_<? echo $i;?>"  name="pcs_<? echo $i;?>"  onBlur="set_sum_value( 'pcs_sum', 'pcs_' ) " class="text_boxes_numeric" style="width:75px"  value="<? echo $row[csf('order_quantity')]; ?>">
                                    </td>
                                    <td id="add_1">
                                   <input type="hidden" id="colorsizetableid_<? echo $i;?>"  name="colorsizetableid_<? echo $i;?>" class="text_boxes" style="width:85px" value="<? echo $row[csf('color_size_table_id')]; ?>" />
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
               
                <table width="800" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                        	<th style="width:464px;">SUM</th>
                            <th width="90"></th>
                            <th width="110"><input type="text" id="cons_sum" name="cons_sum" class="text_boxes_numeric" style="width:95px" readonly></th>
                            <th width="110" style="display:none"><input type="text" id="processloss_sum"  name="processloss_sum" class="text_boxes_numeric" style="width:95px" readonly></th>
                            <th width="105"  style="display:none"><input type="text" id="requirement_sum"  name="requirement_sum" class="text_boxes_numeric" style="width:90px" readonly></th>
                            <th width="90"><input type="text" id="pcs_sum"    name="pcs_sum" class="text_boxes_numeric" style="width:75px" readonly></th>
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
		$id=return_next_id( "id", "wo_booking_mst", 1 ) ;
		$field_array="id,booking_type,is_short,booking_month,booking_year,booking_no_prefix,booking_no_prefix_num,booking_no,company_id,buyer_id,job_no, 	item_category,supplier_id,currency_id,exchange_rate,booking_date,delivery_date,pay_mode,source,attention,fabric_source,ready_to_approved,inserted_by,insert_date";
		//cbo_ready_to_approved
		 $data_array ="(".$id.",2,1,".$cbo_booking_month.",".$cbo_booking_year.",'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",".$txt_job_no.",4,".$cbo_supplier_name.",".$cbo_currency.",".$txt_exchange_rate.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_pay_mode.",".$cbo_source.",".$txt_attention.",".$cbo_material_source.",".$cbo_ready_to_approved.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		 
		 
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con); die;}		
		 $id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		 $field_array1="id,pre_cost_fabric_cost_dtls_id,po_break_down_id,job_no,booking_no,booking_type,is_short,trim_group,description,brand_supplier,uom,sensitivity,wo_qnty,rate,amount,delivery_date,cons_break_down,inserted_by,insert_date";
		 
		 		 //$field_array="id,job_no,po_break_down_id,pre_cost_fabric_cost_dtls_id,color_size_table_id,booking_no,booking_type ,fabric_color_id,fin_fab_qnty,grey_fab_qnty,rate,amount,color_type,construction,copmposition,gsm_weight,dia_width,process_loss_percent";

		 $field_array2="id,wo_trim_booking_dtls_id,booking_no,job_no,po_break_down_id,color_number_id,gmts_sizes,item_color,item_size,cons,process_loss_percent,requirment,pcs,color_size_table_id";
		 $add_comma=0;
		 $id1=return_next_id( "id", "wo_trim_book_con_dtls", 1 );
		 $new_array_color=array();
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $txttrimcostid="txttrimcostid_".$i;
			 $txtpoid="txtpoid_".$i;
			 $txtjob="txtjob_".$i;
			 $txttrimgroup="txttrimgroup_".$i;
			 $txtdescription="txtdescription_".$i;
			 $txtbrandsupref="txtbrandsupref_".$i;
			 $txtuom="txtuom_".$i;
			 $txtreqqnty="txtreqqnty_".$i;
			 $txtcuwoq="txtcuwoq_".$i;
			 $txtbalwoq="txtbalwoq_".$i;
			 $cbocolorsizesensitive="cbocolorsizesensitive_".$i;
			 $txtwoq="txtwoq_".$i;
			 $txtrate="txtrate_".$i;
			 $txtamount="txtamount_".$i;
			 $txtddate="txtddate_".$i;
			 $consbreckdown="consbreckdown_".$i;
			 $txtbookingid="txtbookingid_".$i;
			 if ($i!=1) $data_array1 .=",";
			 $data_array1 .="(".$id_dtls.",".$$txttrimcostid.",".$$txtpoid.",".$$txtjob.",'".$new_booking_no[0]."',2,1,".$$txttrimgroup.",".$$txtdescription.",".$$txtbrandsupref.",".$$txtuom.",".$$cbocolorsizesensitive.",".$$txtwoq.",".$$txtrate.",".$$txtamount.",".$$txtddate.",".$$consbreckdown.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			 
			 //	CONS break down===============================================================================================	
				if(str_replace("'",'',$$consbreckdown) !='')
				{
					$rID_de1=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id =".$$txtbookingid."",0);
					$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
					for($c=0;$c < count($consbreckdown_array);$c++)
					{
						$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
						 /*if (!in_array(str_replace("'","",$consbreckdownarr[3]),$new_array_color))
						 {
							  $color_id = return_id( str_replace("'","",$consbreckdownarr[3]), $color_library, "lib_color", "id,color_name");  
							  $new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[3]);
						 }
						 else 
						 {
							 $color_id =  array_search(str_replace("'","",$consbreckdownarr[3]), $new_array_color);
						 }*/
						if(str_replace("'","",$consbreckdownarr[3]) !="")
						{
						    if (!in_array(str_replace("'","",$consbreckdownarr[3]),$new_array_color))
						    {
						        $color_id = return_id( str_replace("'","",$consbreckdownarr[3]), $color_library, "lib_color", "id,color_name","178");
						        $new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[3]);
						    }
						    else $color_id =  array_search(str_replace("'","",$consbreckdownarr[3]), $new_array_color);
						}
						else
						{
						    $color_id=0;
						}
						 
						if ($add_comma!=0) $data_array2 .=",";
						$data_array2 .="(".$id1.",".$id_dtls.",'".$new_booking_no[0]."',".$$txtjob.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$color_id."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."')";
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
		 $field_array_up="booking_month*booking_year*company_id*buyer_id*job_no*item_category*supplier_id*currency_id*exchange_rate*booking_date*delivery_date*pay_mode*source*attention*fabric_source*ready_to_approved*updated_by*update_date"; 
		 
		 $data_array_up ="".$cbo_booking_month."*".$cbo_booking_year."*".$cbo_company_name."*".$cbo_buyer_name."*".$txt_job_no."*4*".$cbo_supplier_name."*".$cbo_currency."*".$txt_exchange_rate."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_pay_mode."*".$cbo_source."*".$txt_attention."*".$cbo_material_source."*".$cbo_ready_to_approved."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; disconnect($con); die;}		
		 $field_array_up1="pre_cost_fabric_cost_dtls_id*po_break_down_id*job_no*booking_no*trim_group*description*brand_supplier*uom*sensitivity*wo_qnty*rate*amount*delivery_date*cons_break_down*updated_by*update_date";
		 $field_array_up2="id,wo_trim_booking_dtls_id,booking_no,job_no,po_break_down_id,color_number_id,gmts_sizes,item_color,item_size,cons,process_loss_percent,requirment,pcs,color_size_table_id";
		 
		  $add_comma=0;
		  $id1=return_next_id( "id", "wo_trim_book_con_dtls", 1 );
		  $new_array_color=array();
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $txttrimcostid="txttrimcostid_".$i;
			 $txtpoid="txtpoid_".$i;
			 $txtjob="txtjob_".$i;
			 $txttrimgroup="txttrimgroup_".$i;
			 $txtdescription="txtdescription_".$i;
			 $txtbrandsupref="txtbrandsupref_".$i;
			 $txtuom="txtuom_".$i;
			 $txtreqqnty="txtreqqnty_".$i;
			 $txtcuwoq="txtcuwoq_".$i;
			 $txtbalwoq="txtbalwoq_".$i;
			 $cbocolorsizesensitive="cbocolorsizesensitive_".$i;
			 $txtwoq="txtwoq_".$i;
			 $txtrate="txtrate_".$i;
			 $txtamount="txtamount_".$i;
			 $txtddate="txtddate_".$i;
			 $consbreckdown="consbreckdown_".$i;
			 $txtbookingid="txtbookingid_".$i;
			if(str_replace("'",'',$$txtbookingid)!="")
			{
				$id_arr[]=str_replace("'",'',$$txtbookingid);
				$data_array_up1[str_replace("'",'',$$txtbookingid)] =explode("*",("".$$txttrimcostid."*".$$txtpoid."*".$$txtjob."*".$txt_booking_no."*".$$txttrimgroup."*".$$txtdescription."*".$$txtbrandsupref."*".$$txtuom."*".$$cbocolorsizesensitive."*".$$txtwoq."*".$$txtrate."*".$$txtamount."*".$$txtddate."*".$$consbreckdown."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
				//	CONS break down===============================================================================================	
				if(str_replace("'",'',$$consbreckdown) !='')
				{
					$rID_de1=execute_query( "delete from wo_trim_book_con_dtls where  wo_trim_booking_dtls_id =".$$txtbookingid."",0);
					$consbreckdown_array=explode('__',str_replace("'",'',$$consbreckdown));
					for($c=0;$c < count($consbreckdown_array);$c++)
					{
						$consbreckdownarr=explode('_',$consbreckdown_array[$c]);
						 /*if (!in_array(str_replace("'","",$consbreckdownarr[3]),$new_array_color))
						 {
							  $color_id = return_id( str_replace("'","",$consbreckdownarr[3]), $color_library, "lib_color", "id,color_name");  
							  $new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[3]);
						 }
						 else 
						 {
							 $color_id =  array_search(str_replace("'","",$consbreckdownarr[3]), $new_array_color);
						 }*/
						if(str_replace("'","",$consbreckdownarr[3]) !="")
						{
						    if (!in_array(str_replace("'","",$consbreckdownarr[3]),$new_array_color))
						    {
						        $color_id = return_id( str_replace("'","",$consbreckdownarr[3]), $color_library, "lib_color", "id,color_name","178");
						        $new_array_color[$color_id]=str_replace("'","",$consbreckdownarr[3]);
						    }
						    else $color_id =  array_search(str_replace("'","",$consbreckdownarr[3]), $new_array_color);
						}
						else
						{
						    $color_id=0;
						}
						if ($add_comma!=0) $data_array_up2 .=",";
						$data_array_up2 .="(".$id1.",".$$txtbookingid.",".$txt_booking_no.",".$$txtjob.",'".$consbreckdownarr[0]."','".$consbreckdownarr[1]."','".$consbreckdownarr[2]."','".$color_id."','".$consbreckdownarr[4]."','".$consbreckdownarr[5]."','".$consbreckdownarr[6]."','".$consbreckdownarr[7]."','".$consbreckdownarr[8]."','".$consbreckdownarr[9]."')";
						$id1=$id1+1;
						$add_comma++;
					}
				}
          //CONS break down end===============================================================================================
			}
		 }
		$rID=sql_update("wo_booking_mst",$field_array_up,$data_array_up,"booking_no","".$txt_booking_no."",0);
		$rID1=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ));
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













if ($action=="trims_booking_popup")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
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
	<table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                     <thead>
                           <th colspan="2"> </th>
                        	<th  >
                              <?
                               echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
                              ?>
                            </th>
                            <th colspan="3"></th>
                     </thead>
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="150">Supplier Name</th>
                        <th width="100">Booking No</th>
                        <th width="200"> Booking Date Range</th><th></th>           
                    </thead>
        			<tr>
                    	<td> 
							<? 
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "");
							?>
                        </td>
                   	<td id="buyer_td">
                     <? 
						echo create_drop_down( "cbo_supplier_name", 172, "select id,supplier_name from lib_supplier where find_in_set(4,party_type) and status_active =1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );
					?>	</td>
                    <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value, 'create_booking_search_list_view', 'search_div', 'short_trims_booking_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $supplier_id=" and supplier_id='$data[1]'"; else $supplier_id ="";

	
	if($db_type==0)
	{
	$booking_year_cond=" and SUBSTRING_INDEX(`insert_date`, '-', 1)=$data[4]";	
	if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
	 $booking_year_cond=" and to_char(insert_date,'YYYY')=$data[4]";
	if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	
	if($data[6]==4 || $data[6]==0)
		{
		   if (str_replace("'","",$data[5])!="") $booking_cond=" and booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  "; else  $booking_cond="";
		}
    if($data[6]==1)
		{
			if (str_replace("'","",$data[5])!="") $booking_cond=" and booking_no_prefix_num='$data[5]'  $booking_year_cond  "; else  $booking_cond="";
		}
   if($data[6]==2)
		{
			if (str_replace("'","",$data[5])!="") $booking_cond=" and booking_no_prefix_num like '$data[5]%'  $booking_year_cond  "; else  $booking_cond="";
		}
	if($data[6]==3)
		{
			if (str_replace("'","",$data[5])!="") $booking_cond=" and booking_no_prefix_num like '%$data[5]' $booking_year_cond  "; else  $booking_cond="";
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
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (1=>$comp,2=>$suplier);
	$sql=" select booking_no_prefix_num, booking_no,company_id,supplier_id,booking_date,delivery_date from wo_booking_mst where  booking_type=2 and is_short=1 ".set_user_lavel_filtering(' and buyer_id','buyer_id')."  $company  $supplier_id $booking_date $booking_cond";
	//echo $sql;
	 echo  create_list_view("list_view", "Booking No,Company,Supplier,Booking Date,Delivery Date", "120,100,100,100","600","320",0, $sql , "js_set_value", "booking_no", "", 1, "0,company_id,supplier_id,0,0", $arr , "booking_no_prefix_num,company_id,supplier_id,booking_date,delivery_date", '','','0,0,0,3,3','','');
	
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
		http.open("POST","short_trims_booking_controller.php",true);
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
	//$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
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
                                <strong><? if ($report_title !=""){echo $report_title;} else {echo "Main Trims Booking";}  ?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
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
		
		$po_no="";$int_ref_no="";$file_no="";
		$nameArray_job=sql_select( "select distinct b.po_number,b.grouping,b.file_no  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no"); 
        foreach ($nameArray_job as $result_job)
        {
			$po_no.=$result_job[csf('po_number')].", ";
			$int_ref_no.=$result_job[csf('grouping')].", ";
			$file_no.=$result_job[csf('file_no')].", ";
		}
		$style_ref="";
		$nameArray_style=sql_select( "select distinct a.style_ref_no  from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no=$txt_booking_no"); 
        foreach ($nameArray_style as $result_style)
        {
			$style_ref.=$result_style[csf('style_ref_no')].", ";
		}
        $nameArray=sql_select( "select a.booking_no,a.booking_date,a.pay_mode,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.source,a.insert_date,a.update_date  from wo_booking_mst a where  a.booking_no=$txt_booking_no and a.booking_type=2"); 
        foreach ($nameArray as $result)
        {
        
			$sql_po= "select b.po_number,MIN(b.pub_shipment_date) pub_shipment_date, MIN(b.insert_date) as insert_date,b.shiping_status from wo_booking_dtls a,wo_po_break_down b  where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no group by b.po_number, b.shiping_status"; 
			$data_array_po=sql_select($sql_po);
			foreach ($data_array_po as $rows)
			{
				
				$daysInHand.=(datediff('d',$result[csf('delivery_date')],$rows[csf('pub_shipment_date')])-1).",";
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
               	<td width="110">:&nbsp;<? echo $supplier_address_arr[$result[csf('supplier_id')]];?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
            </tr>  
            <tr>
                <td width="100" style="font-size:12px"><b>Job No</b>   </td>
                <td width="110">:&nbsp;
				<? 
				echo $job_no=rtrim($job_no,", ");
				
				$int_ref_no=rtrim($int_ref_no,", ");
				$file_no=rtrim($file_no,", ");
				$int_ref_no=implode(",",array_unique(explode(",",$int_ref_no)));
				$file_no=implode(",",array_unique(explode(",",$file_no)));
				?> 
                </td>
                 
               	<td width="110" style="font-size:12px" align="left"><b>PO No</b> </td>
                <td  width="100" style="font-size:12px" align="left">:&nbsp;<? echo rtrim($po_no,", "); ?> </td>
               	<td width="110" style="font-size:12px" align="left"><b>File No</b> </td>
                <td  width="100" style="font-size:12px" align="left">:&nbsp;<? echo $file_no; ?> </td>
            </tr> 
            <tr>
                <td width="100" style="font-size:12px"><b>Buyer</b>   </td>
                <td width="110">:&nbsp;
				<? 
					echo rtrim($buyer_string,", ");
				?> 
                </td>
                <td width="110" style="font-size:12px"><b>Style</b> </td>
                <td  width="100" style="font-size:12px" >:&nbsp;<? echo $style_sting=rtrim($style_ref,", "); ?> </td>
                <td width="110" style="font-size:12px" align="left"><b>Ref. No</b> </td>
                <td  width="100" style="font-size:12px" align="left">:&nbsp;<? echo $int_ref_no; ?> </td>
                 
               	
            </tr> 
             <tr>
               <td width="110" style="font-size:12px"><b>WO Prepared After</b></td>
               <td width="300"> :&nbsp;<? echo rtrim($WOPreparedAfter,',').' Days' ?></td>
                
               <td width="100" style="font-size:12px"><b>Ship.days in Hand</b></td>
               <td width="300"> :&nbsp;<? echo rtrim($daysInHand,',').' Days'?></td>
                
               <td width="100" style="font-size:12px"><b>Ex-factory status</b></td>
               <td> :&nbsp;<? echo rtrim($shiping_status,','); ?></td>
            </tr> 
            
            
        </table>  
        <br/>
		<?
        }
        ?>
          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?
        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 order by trim_group "); 
        $nameArray_color=sql_select( "select distinct b.color_number_id from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=1"); 
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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=1 and trim_group=".$result_item[csf('trim_group')]." order by trim_group "); 
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
                $nameArray_color_size_qnty=sql_select( "select sum(b.cons) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=1 and a.trim_group=". $result_item[csf('trim_group')]." and a.description='". $result_itemdescription[csf('description')]."' and b.color_number_id=".$result_color[csf('color_number_id')]."");                          
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
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
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
        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 order by trim_group "); 
        $nameArray_size=sql_select( "select distinct b.item_size  as gmts_sizes from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=2");
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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=2 and trim_group=".$result_item[csf('trim_group')]." order by trim_group "); 
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
                $nameArray_size_size_qnty=sql_select( "select sum(b.cons) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=2 and a.trim_group=". $result_item[csf('trim_group')]." and a.description='". $result_itemdescription[csf('description')]."' and b.item_size='".$result_size[csf('gmts_sizes')]."'");                          
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
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
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
        $nameArray_item=sql_select( "select distinct trim_group from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 order by trim_group "); 
        $nameArray_color=sql_select( "select distinct b.item_color as color_number_id from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=3"); 
		if(count($nameArray_color)>0)
		{
        ?>
        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="<? echo count($nameArray_color)+8; ?>" align="">
                <strong>Contrast Color</strong>
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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=3 and trim_group=".$result_item[csf('trim_group')]." order by trim_group "); 
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
                $nameArray_color_size_qnty=sql_select( "select sum(b.cons) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=3 and a.trim_group=". $result_item[csf('trim_group')]." and a.description='". $result_itemdescription[csf('description')]."' and b.item_color=".$result_color[csf('color_number_id')]."");                          
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
                 <td style="border:1px solid black; text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
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
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_color)+7; ?>"><strong>Total</strong></td>
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
        $nameArray_size=sql_select( "select distinct b.item_size  as gmts_sizes from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=4");
	    $nameArray_color=sql_select( "select distinct b.item_color as color_number_id from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=4"); 

		if(count($nameArray_size)>0)
		{
        ?>
        
        <table border="1" align="left" cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="<? echo count($nameArray_size)+8; ?>" align="">
                <strong>Color & size sensitive </strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand Supplier</strong> </td>
                <td style="border:1px solid black"><strong>Item Color</strong> </td>
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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=4 and trim_group=".$result_item[csf('trim_group')]." order by trim_group "); 
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
                foreach($nameArray_item_description as $result_itemdescription)
                {
					?>
                    <td style="border:1px solid black" rowspan="<? echo count($nameArray_color); ?>"><? echo $result_itemdescription[csf('description')]; ?> </td>
                    <td style="border:1px solid black" rowspan="<? echo count($nameArray_color); ?>"><? echo $result_itemdescription[csf('brand_supplier')]; ?> </td>
                    <?
                //$item_desctiption_total=0;
				foreach($nameArray_color as $result_color)
                {
					 $item_desctiption_total=0;
                ?>
                
                <td style="border:1px solid black"><? echo $color_library[$result_color[csf('color_number_id')]]; ?> </td>
                <?
                foreach($nameArray_size  as $result_size)
                {
                $nameArray_size_size_qnty=sql_select( "select sum(b.cons) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=4 and a.trim_group=". $result_item[csf('trim_group')]." and a.description='". $result_itemdescription[csf('description')]."' and b.item_size='".$result_size[csf('gmts_sizes')]."' and b.item_color=".$result_color[csf('color_number_id')]."");                          
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
                <td style="border:1px solid black;text-align:center"><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
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
			}
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right" colspan="5"><strong> Item Total</strong></td>
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
                <td align="right" style="border:1px solid black"  colspan="<? echo count($nameArray_size)+8; ?>"><strong>Total</strong></td>
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
                <td colspan="7" align="">
                <strong>No Sensitivity</strong>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid black"><strong>Sl</strong> </td>
                <td style="border:1px solid black"><strong>Item Group</strong> </td>
                <td style="border:1px solid black"><strong>Item Description</strong> </td>
                <td style="border:1px solid black"><strong>Brand Supplier</strong> </td>
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
            $nameArray_item_description=sql_select( "select distinct description,brand_supplier,rate,uom from wo_booking_dtls  where booking_no=$txt_booking_no and sensitivity=0 and trim_group=".$result_item[csf('trim_group')]." order by trim_group "); 
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
                <?
                //$nameArray_color_size_qnty=sql_select( "select sum(b.cons) as cons from wo_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=$txt_booking_no and a.sensitivity=0 and a.trim_group=". $result_item['trim_group']." and a.description='". $result_itemdescription['description']."'"); 
				$nameArray_color_size_qnty=sql_select( "select sum(wo_qnty) as cons from wo_booking_dtls     where    booking_no=$txt_booking_no and sensitivity=0 and trim_group=". $result_item[csf('trim_group')]." and description='". $result_itemdescription[csf('description')]."'");                          
                          
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
                
                <td style="border:1px solid black; text-align:center "><? echo $unit_of_measurement[$result_itemdescription[csf('uom')]]; ?></td>
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
                <td align="right" style="border:1px solid black"  colspan="7"><strong>Total</strong></td>
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
        <?
		   echo get_spacial_instruction($txt_booking_no);
		?>
    </td>
    <td width="2%"></td>
    
    <td width="49%" valign="top">
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
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con); die;}		
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
	 $sql= "select booking_no,booking_date,company_id,buyer_id, 	job_no,currency_id,exchange_rate,pay_mode,booking_month,ready_to_approved,supplier_id,attention,delivery_date,source,booking_year,is_approved,fabric_source from wo_booking_mst  where booking_no='$data'";     
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
		echo "load_drop_down( 'requires/short_trims_booking_controller', '".$row[csf("company_id")]."', 'load_drop_down_buyer', 'buyer_td' );\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n"; 
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";  
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "load_drop_down( 'requires/short_trims_booking_controller', '".$row[csf("pay_mode")]."', 'load_drop_down_supplier', 'supplier_td' );\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_booking_month').value = '".$row[csf("booking_month")]."';\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
                echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
                echo "document.getElementById('cbo_material_source').value = '".$row[csf("fabric_source")]."';\n";
		echo "document.getElementById('cbo_booking_year').value = '".$row[csf("booking_year")]."';\n";
		echo "document.getElementById('id_approved_id').value = '".$row[csf("is_approved")]."';\n";
		echo " $('#cbo_company_name').attr('disabled',true);\n"; 
		echo " $('#cbo_buyer_name').attr('disabled',true);\n"; 
		echo " $('#cbo_supplier_name').attr('disabled',true);\n"; 
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
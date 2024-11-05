<? 
/*-------------------------------------------- Comments
Version          :  V1
Purpose			 : This form will create print Booking
Functionality	 :	
JS Functions	 :
Created by		 : MONZU 
Creation date 	 : 
Requirment Client: 
Requirment By    : 
Requirment type  : 
Requirment       : 
Affected page    : 
Affected Code    :              
DB Script        : 
Updated by 		 : 
Update date		 : 
QC Performed BY	 :		
QC Date			 :	
Comments		 : From this version oracle conversion is start
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
//require_once('../../../includes/class.reports.php');
//include('../../../includes/class.emblishments.php');
//require_once('../../../includes/class.washes.php');
require_once('../../../includes/class3/class.conditions.php');
require_once('../../../includes/class3/class.reports.php');
require_once('../../../includes/class3/class.emblishments.php');
require_once('../../../includes/class3/class.washes.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
//---------------------------------------------------- Start---------------------------------------------------------------------------
//$po_number=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number");
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
$trim_group= return_library_array("select id, item_name from lib_item_group",'id','item_name');
$po_number_arr=return_library_array("select id,po_number from wo_po_break_down", "id", "po_number");

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
}
if($action=="check_conversion_rate")
{ 
	$data=explode("**",$data);
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "yyyy-mm-dd", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate( $data[0], $conversion_date,$data[2] );
	echo "1"."_".$currency_rate;
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
	//echo $budget_version;
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
				//document.getElementById('selected_budget_id').value=budget_id;
				parent.emailwindow.hide();
			}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="1210" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                    <thead> 
                    <tr>
                     <th colspan="4"> </th>
                            <th colspan="3">
                              <?
                               echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" );
                              ?>
                            </th>
                          <th colspan="3"> </th>
                    </tr>  
                    <tr>               	 
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                         <th width="100">Job No</th>
                         <th width="90">File No</th>
                         <th width="90">Ref. No</th>
                         <th width="100">Style Ref </th>
                        <th width="140">Order No</th>
						 <th width="100">Budget Version</th>
                        <th width="200">Date Range</th><th></th>  
                        </tr>         
                    </thead>
        			<tr>
                    	<td> 
                        <input type="hidden" id="selected_job">
						<input type="hidden" id="selected_budget_id">
                        
							<? 
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'print_booking_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
                    </td>
                   	<td id="buyer_td">
                     <? 
						echo create_drop_down( "cbo_buyer_name", 172, $blank_array,'', 1, "-- Select Buyer --" );
					?>	</td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:90px"></td>
                    <td><input name="txt_file" id="txt_file" class="text_boxes" style="width:85px"></td>
                    <td><input name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:85px"></td>
                    
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:140px"></td>
					<td>				
						<?
						$pre_cost_class_arr = array(1=>'Pre Cost 1',2=>'Pre Cost 2');
						echo create_drop_down( "cbo_budget_version", 100, $pre_cost_class_arr,"", 0, "-- Select Version --",$budget_version,"",1);
						?>	
					</td> 
					
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_file').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('cbo_budget_version').value, 'create_po_search_list_view', 'search_div', 'print_booking_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
    </table>    
       <div id="search_div"></div>
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
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else  $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[5]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[5]";}
	//if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num='$data[4]'  $year_cond "; else  $job_cond=""; 
	//if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]%'  "; else  $order_cond=""; 
	$budget_version=str_replace("'","",$data[11]);
	$job_cond="";
	$order_cond=""; 
	$style_cond="";
	if($data[8]==1)
	{
	if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num='$data[4]' $year_cond"; //else  $job_cond=""; 
	if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number = '$data[6]'  "; //else  $order_cond=""; 
	if (trim($data[7])!="") $style_cond=" and a.style_ref_no ='$data[7]'"; //else  $style_cond=""; 
	}
	if($data[8]==2)
	{
	if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '$data[4]%' $year_cond"; //else  $job_cond=""; 
	if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '$data[6]%'  "; //else  $order_cond=""; 
	if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '$data[7]%'  "; //else  $style_cond=""; 
	}
	if($data[8]==3)
	{
	if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]' $year_cond"; //else  $job_cond=""; 
	if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]'  "; //else  $order_cond=""; 
	if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '%$data[7]'"; //else  $style_cond=""; 
	}
	if($data[8]==4 || $data[8]==0)
	{
	if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]%' $year_cond"; //else  $job_cond=""; 
	if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]%'  "; //else  $order_cond=""; 
	if (trim($data[7])!="") $style_cond=" and a.style_ref_no like '%$data[7]%'"; //else  $style_cond=""; 
	}
	if($db_type==0)
	{
	if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	if($db_type==2)
	{
	if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	
	if (str_replace("'","",$data[9])!="") $file_cond=" and b.file_no ='$data[9]'   "; else $file_cond="";
	if (str_replace("'","",$data[10])!="") $ref_cond=" and b.grouping='$data[10]'"; else $ref_cond="";
	if($budget_version==1)
	{
		$entry_form_cond="and c.entry_from=111";
	}
	else
	{
		$entry_form_cond="and c.entry_from=158";
	}
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (4=>$comp,5=>$buyer_arr);
	//$sql= "select YEAR(a.insert_date) as year, a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.shipment_date,a.job_no,c.id as pre_id from wo_po_details_master  a, wo_po_break_down b left join wo_pre_cost_mst c on b.job_no_mst=c.job_no and c.status_active=1 and c.is_deleted=0 where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer order by a.job_no"; 
	if($db_type==0) $yearCond="YEAR(a.insert_date)"; else if($db_type==2) $yearCond="to_char(a.insert_date,'YYYY')";
	/*{
		$sql= "select YEAR(a.insert_date) as year, a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.file_no,b.grouping, b.po_number,b.po_quantity,b.shipment_date,a.job_no,c.id as pre_id from wo_po_details_master  a, wo_po_break_down b, wo_pre_cost_mst c,wo_pre_cost_embe_cost_dtls d   where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.job_no=d.job_no and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.approved=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $entry_form_cond $shipment_date $company $buyer  $job_cond $style_cond $order_cond $file_cond $ref_cond order by a.job_no";  
	}
	if($db_type==2)
	{*/
		 $sql= "select $yearCond as year, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.file_no, b.grouping, b.po_number, b.po_quantity, b.shipment_date, a.job_no, c.id as pre_id from wo_po_details_master  a, wo_po_break_down b, wo_pre_cost_mst c,wo_pre_cost_embe_cost_dtls d where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.job_no=d.job_no and a.status_active=1 and a.is_deleted=0 and c.approved=1  and b.shiping_status not in(3) and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $entry_form_cond $shipment_date $company $buyer  
$job_cond $style_cond $order_cond $file_cond $ref_cond group by a.insert_date, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.file_no, b.grouping, b.po_number, b.po_quantity, b.shipment_date, a.job_no, c.id order by a.job_no desc";  
	//}

		//echo $sql;
	//echo  create_list_view("list_view", "Year,Job No,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date, Precost id", "90,80,120,100,100,90,90,90,80,100","1080","320",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0", $arr , "year,job_no_prefix_num,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date,pre_id", "",'','0,0,0,0,0,1,0,1,3,0') ;
	
	echo  create_list_view("list_view", "Year,Job No,File No,Ref. No,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date, Precost id", "60,80,80,80,120,100,100,90,90,90,80,100","1130","280",0, $sql , "js_set_value", "job_no", "", 1, "0,0,0,0,company_name,buyer_name,0,0,0,0,0", $arr , "year,job_no_prefix_num,file_no,grouping,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date,pre_id", "",'','0,0,0,0,0,0,0,1,0,1,3,0') ;
	exit();
}


if ($action=="change_emb_name"){
echo "document.getElementById('cbo_booking_natu').value = 0;"; 
//exit(); 	
}


if ($action=="populate_order_data_from_search_popup")
{
	
	$po_number=return_library_array( "select id,po_number from wo_po_break_down where job_no_mst='".$data."' and shiping_status not in(3)", "id", "po_number"  );
 	$cbo_po_no=create_drop_down( "txt_order_no_id", 172, $po_number,"", 1, "-- Select PO --", "", "fn_change_emb_name();fn_empty_dtls()","",""); 
	//$po_number=return_library_array( "select id,po_number from wo_po_break_down where job_no_mst='".$data."'", "id", "po_number"  );
	//$pre_cost_ex_rate_arr=return_library_array( "select job_no,exchange_rate from wo_pre_cost_mst where status_active=1 ", "job_no", "exchange_rate"  );
    $emb_name="";
	//$data_array=sql_select("select a.job_no,a.company_name,a.buyer_name,a.gmts_item_id,b.emb_name from wo_po_details_master a, wo_pre_cost_embe_cost_dtls b where a.job_no ='".$data."' and b.cons_dzn_gmts !=0 and b.emb_type in(1,2,3) and a.job_no=b.job_no");
	  $pre_cost_arr=array();
	 $res_sql=sql_select("select a.job_no,a.exchange_rate,b.currency_id from wo_pre_cost_mst a,wo_po_details_master b where b.job_no=a.job_no and  a.status_active=1 and  b.status_active=1");
	 foreach($res_sql as $row)
	 {
		 $pre_cost_arr[$row[csf('job_no')]]['rate']=$row[csf('exchange_rate')];
		 $pre_cost_arr[$row[csf('job_no')]]['curr']=$row[csf('currency_id')]; 
	 }
	//$pre_cost_ex_rate="select id,po_number from wo_pre_cost_mst where job_no_mst='".$data."'";
	//wo_pre_cost_mst
	//echo $data.'aziz';
	$data_array=sql_select("select a.job_no,a.company_name,a.currency_id,a.buyer_name,a.gmts_item_id,b.emb_name from wo_po_details_master a, wo_pre_cost_embe_cost_dtls b where a.job_no ='".$data."' and b.cons_dzn_gmts !=0 and b.emb_name in(1,2,3,4,5) and a.job_no=b.job_no");
	
	foreach ($data_array as $row)
	{
		$emb_name.=$row[csf("emb_name")].",";
		$currency_id= $pre_cost_arr[$row[csf('job_no')]]['curr'];
		$ex_rate= $pre_cost_arr[$data]['rate'];
		$job_curr=$row[csf("currency_id")];
		if($currency_id==$job_curr)
		{
			$exchane_rate=$ex_rate;
		}
		else
		{
			$exchane_rate=1;
		}
		echo "document.getElementById('txt_exchange_rate').value = '".$exchane_rate."';\n"; 
		//echo "check_exchange_rate();\n"; 
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";  
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		$gmt_item=create_drop_down( "cbo_gmt_item", 172, $garments_item,"", 1, "-- Select Item --", "", "","",$row[csf("gmts_item_id")] ); 
		echo "document.getElementById('gmt_item_td').innerHTML = '".$gmt_item."';\n";
		//echo "set_field_level_access( ".$row[csf("company_name")]." );\n";
		$company_name=$row[csf("company_name")];

	}
	
	 $emb_name=rtrim($emb_name,",");
	 //echo $emb_name;
	 if($emb_name !="")
	 {
		$cbo_booking_natu= create_drop_down( "cbo_booking_natu", 172, $emblishment_name_array,"", 1, "-- Select --", "","fnc_generate_booking()", "", $emb_name);
	    echo "document.getElementById('booking_natu_td').innerHTML ='".$cbo_booking_natu."';\n"; 
		echo "document.getElementById('po_id_td').innerHTML = '".$cbo_po_no."';\n";

	 }
	 else
	 {
		echo "alert('No Embellishment Data found');\n";
	 }
	  echo "set_field_level_access( ".$company_name." );\n";
}


if ($action=="check_row_embl_setting")
{
	extract($_REQUEST);
	$data=explode("_",$data);
	$company_name=str_replace("'","",$data[0]);
	$emb_name=str_replace("'","",$data[1]);
	
	$variArr=array();
	$sqlvari=sql_select("select embellishment_id,embellishment_budget_id from variable_order_tracking where company_name='$company_name' and embellishment_id=$emb_name and variable_list=56 and status_active=1 and is_deleted=0");
	//echo "select embellishment_id,embellishment_budget_id from variable_order_tracking where company_name='$company_name' and embellishment_id=$emb_name and variable_list=56 and status_active=1 and is_deleted=0";
	$embellishment_budget_id=0;
	foreach($sqlvari as $row)
	{
		$embellishment_budget_id=$row[csf('embellishment_budget_id')];
		echo $embellishment_budget_id;
	}
	
	
}

if ($action=="generate_print_booking")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_order_no_id=str_replace("'","",$txt_order_no_id);
	$cbo_booking_natu=str_replace("'","",$cbo_booking_natu);
	$cbo_gmt_item=str_replace("'","",$cbo_gmt_item);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$txt_booking_date=change_date_format(str_replace("'","",$txt_booking_date),"dd-mm-yyyy","-");
	$calculation_basis=str_replace("'","",$calculation_basis);
	
	$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='$txt_job_no'");
	if($costing_per==1) $costing_per_dzn="1 Dzn";
	else if($costing_per==2) $costing_per_dzn="1 Pcs";
	else if($costing_per==3) $costing_per_dzn="2 Dzn";
	else if($costing_per==4) $costing_per_dzn="3 Dzn";
	else if($costing_per==5) $costing_per_dzn="4 Dzn";


     if($cbo_company_name==0) $cbo_company_name_cond=""; else $cbo_company_name_cond =" and a.company_name='$cbo_company_name'";
	 
	 if($cbo_buyer_name==0) $cbo_buyer_name_cond=""; else $cbo_buyer_name_cond =" and a.buyer_name='$cbo_buyer_name'";
	 
	 if($txt_job_no=="") $txt_job_no_cond="";  else $txt_job_no_cond ="and a.job_no='$txt_job_no'";
	 
	 $booking_month=0;
	 if($cbo_booking_month<10) $booking_month.=$cbo_booking_month; else $booking_month=$cbo_booking_month; 
	$start_date=$cbo_booking_year."-".$booking_month."-01";
	$end_date=$cbo_booking_year."-".$booking_month."-".cal_days_in_month(CAL_GREGORIAN, $booking_month, $cbo_booking_year);
	?>
   	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1247" class="rpt_table" >
            <thead>
                <th width="40">SL</th>
                <th width="90">Ord. No</th>
                <th width="100">Gmt Item</th>
                <th width="90">Booking Nature</th>
                <th width="100">Booking Type</th>
                <th width="80">Color</th>
                <th width="60"><? if($calculation_basis==1){echo "Order Qty (Pcs)";} else{echo "Plan Cut(Pcs)";} ?></th>
                <th width="200">Description</th>
                <th width="60">Req Qty /DZN</th>
                <th width="60">CU WOQ/ DZN</th>
                <th width="60">Bal Qty/ DZN</th>
                <th width="60">WOQ / DZN</th>
                <th width="50">Rate/ DZN</th>
                <th width="60">Amount/ DZN</th>
                <th width="">Delv. Date</th>
            </thead>
        </table>
        <div style="width:1247px; overflow-y:scroll; max-height:350px;" id="buyer_list_view" align="center">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1230" class="rpt_table" id="tbl_list_search" >
       <tbody>
       <?
				   
				/*	$po_id=$txt_order_no_id;
					$condition= new condition();
					//$emblishment= new emblishment($po_id,'po');
					 if(str_replace("'","",$po_id) !=''){
					  $condition->po_id("in ($po_id)");
				   	}
					$condition->init();
				  	$emblishment= new emblishment($condition);
				   //echo $emblishment->getQuery(); //die;
					$precost_embQty_arr=$emblishment->getQtyArray_by_orderAndEmblishmentid();*/
					
					
				   $booking_data_array_cu=array();
				   
				   $sql_booking_cu= sql_select( "select b.po_break_down_id,b.gmt_item,b.pre_cost_fabric_cost_dtls_id,b.gmts_color_id,sum(b.wo_qnty) as cu_woq,c.emb_name,c.emb_type from wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_embe_cost_dtls c where a.job_no=b.job_no and  a.job_no=c.job_no and a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.job_no='$txt_job_no' and a.booking_type=6 and a.is_short=2 and  a.status_active=1  and 	a.is_deleted=0 and  b.status_active=1  and 	b.is_deleted=0  group by b.po_break_down_id,b.gmt_item,b.pre_cost_fabric_cost_dtls_id,c.emb_name,c.emb_type,b.gmts_color_id  order by b.pre_cost_fabric_cost_dtls_id");
				   
				   foreach($sql_booking_cu as $sql_booking_row_cu )
				   {
					  $booking_data_array_cu[$sql_booking_row_cu[csf('po_break_down_id')]][$sql_booking_row_cu[csf('gmt_item')]][$sql_booking_row_cu[csf('pre_cost_fabric_cost_dtls_id')]][$sql_booking_row_cu[csf('emb_name')]][$sql_booking_row_cu[csf('emb_type')]][$sql_booking_row_cu[csf('gmts_color_id')]]=$sql_booking_row_cu[csf('cu_woq')];
				   }
				   unset($sql_booking_cu);
				   
				   $booking_data_array=array();
				   $updateid="";
				    $sql_booking= sql_select( "select id,job_no,po_break_down_id,pre_cost_fabric_cost_dtls_id,booking_no,booking_type,gmts_color_id,gmt_item,wo_qnty,rate,amount,delivery_date,description from  wo_booking_dtls where booking_no='$txt_booking_no' and  status_active=1  and 	is_deleted=0");
					
				   foreach($sql_booking as $sql_booking_row )
				   {
					   $booking_data_array[$sql_booking_row[csf('po_break_down_id')]][$sql_booking_row[csf('gmt_item')]][$sql_booking_row[csf('pre_cost_fabric_cost_dtls_id')]][$sql_booking_row[csf('gmts_color_id')]][id]=$sql_booking_row[csf('id')];
					   $booking_data_array[$sql_booking_row[csf('po_break_down_id')]][$sql_booking_row[csf('gmt_item')]][$sql_booking_row[csf('pre_cost_fabric_cost_dtls_id')]][$sql_booking_row[csf('gmts_color_id')]][wo_qnty]=$sql_booking_row[csf('wo_qnty')];
					   $booking_data_array[$sql_booking_row[csf('po_break_down_id')]][$sql_booking_row[csf('gmt_item')]][$sql_booking_row[csf('pre_cost_fabric_cost_dtls_id')]][$sql_booking_row[csf('gmts_color_id')]][rate]=$sql_booking_row[csf('rate')];
					   $booking_data_array[$sql_booking_row[csf('po_break_down_id')]][$sql_booking_row[csf('gmt_item')]][$sql_booking_row[csf('pre_cost_fabric_cost_dtls_id')]][$sql_booking_row[csf('gmts_color_id')]][amount]=$sql_booking_row[csf('amount')];
					   $booking_data_array[$sql_booking_row[csf('po_break_down_id')]][$sql_booking_row[csf('gmt_item')]][$sql_booking_row[csf('pre_cost_fabric_cost_dtls_id')]][$sql_booking_row[csf('gmts_color_id')]][delivery_date]=$sql_booking_row[csf('delivery_date')];
					   $booking_data_array[$sql_booking_row[csf('po_break_down_id')]][$sql_booking_row[csf('gmt_item')]][$sql_booking_row[csf('pre_cost_fabric_cost_dtls_id')]][$sql_booking_row[csf('gmts_color_id')]][description]=$sql_booking_row[csf('description')];

				   }
	   
				    $sql="select a.total_set_qnty, b.costing_per, c.id as wo_pre_cost_embe_cost_dtls, c.job_no, c.emb_name, c.emb_type, c.cons_dzn_gmts, c.rate, c.amount, d.id as po_id, d.po_number, d.pub_shipment_date, d.grouping, d.plan_cut, e.item_number_id, e.color_number_id, sum(e.plan_cut_qnty) as plan_cut_qnty, sum(e.order_quantity) as order_quantity
					from 
					wo_po_details_master a, wo_pre_cost_mst b, wo_pre_cost_embe_cost_dtls c, wo_po_break_down d, wo_po_color_size_breakdown e
					where
					a.job_no=b.job_no and a.job_no=c.job_no and a.job_no=d.job_no_mst and a.job_no=e.job_no_mst and d.id=e.po_break_down_id $cbo_company_name_cond $txt_job_no_cond $cbo_buyer_name_cond and c.emb_name=$cbo_booking_natu and d.id in($txt_order_no_id) and e.item_number_id=$cbo_gmt_item and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 
					group by a.total_set_qnty, b.costing_per, c.id, c.job_no, c.emb_name, c.emb_type, c.cons_dzn_gmts, c.rate, c.amount, d.id, d.po_number, d.pub_shipment_date, d.grouping, d.plan_cut, e.item_number_id, e.color_number_id order by d.id";
					//echo $sql;//die;
					$i=1;
                    $nameArray=sql_select( $sql );
					
                    foreach ($nameArray as $selectResult)
                    {
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
						if($selectResult[csf('costing_per')]==1) $costing_per_qty=12;
						else if($selectResult[csf('costing_per')]==2) $costing_per_qty=1;
						else if($selectResult[csf('costing_per')]==3) $costing_per_qty=24;
						else if($selectResult[csf('costing_per')]==4) $costing_per_qty=36;
						else if($selectResult[csf('costing_per')]==5) $costing_per_qty=48;
					
						$booking_dtls_id=$booking_data_array[$selectResult[csf('po_id')]][$selectResult[csf('item_number_id')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('color_number_id')]][id];
						$updateid.=$booking_dtls_id;

						$wo_qnty_pre=$booking_data_array[$selectResult[csf('po_id')]][$selectResult[csf('item_number_id')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('color_number_id')]][wo_qnty];
						$wo_qnty_pre=def_number_format($wo_qnty_pre,2,"");
						
						$rate_pre=$booking_data_array[$selectResult[csf('po_id')]][$selectResult[csf('item_number_id')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('color_number_id')]][rate];
						$rate_pre=def_number_format($rate_pre,5,"");
						
						$amount_pre=$booking_data_array[$selectResult[csf('po_id')]][$selectResult[csf('item_number_id')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('color_number_id')]][amount];
						$amount_pre=def_number_format($amount_pre,5,"");
						
						$delivery_date=$booking_data_array[$selectResult[csf('po_id')]][$selectResult[csf('item_number_id')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('color_number_id')]][delivery_date];
						$delivery_date=change_date_format($delivery_date,"dd-mm-yyyy","-");
						
						$description=$booking_data_array[$selectResult[csf('po_id')]][$selectResult[csf('item_number_id')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('color_number_id')]][description];
						
						$cu_woq=$booking_data_array_cu[$selectResult[csf('po_id')]][$selectResult[csf('item_number_id')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('emb_name')]][$selectResult[csf('emb_type')]][$selectResult[csf('color_number_id')]];
						$cu_woq=def_number_format($cu_woq-$wo_qnty_pre,2,"");
						//echo $cu_woq;
						if($calculation_basis==1)
						{
						  //$req_qnty=def_number_format(($selectResult[csf('cons_dzn_gmts')]*($selectResult[csf('order_quantity')]/(12*$selectResult[csf('total_set_qnty')]))) ,2,"" );
						 // $req_qnty=def_number_format(($selectResult[csf('cons_dzn_gmts')]*($selectResult[csf('order_quantity')]/12)) ,2,"" );
						   $req_qnty=def_number_format(($selectResult[csf('cons_dzn_gmts')]*($selectResult[csf('order_quantity')]/($costing_per_qty))) ,2,"" );
						}
						else
						{
						 // $req_qnty=def_number_format(($selectResult[csf('cons_dzn_gmts')]*($selectResult[csf('order_quantity')]/(12*$selectResult[csf('total_set_qnty')]))),2,"");
						  //$req_qnty=def_number_format(($selectResult[csf('cons_dzn_gmts')]*($selectResult[csf('plan_cut_qnty')]/12)),2,"");
						  $req_qnty=def_number_format(($selectResult[csf('cons_dzn_gmts')]*($selectResult[csf('plan_cut_qnty')]/($costing_per_qty))) ,2,"" );
						}
						//echo $calculation_basis;
						$bal_woq=def_number_format($req_qnty-$cu_woq,2,"");

						if($booking_dtls_id==""){
							$wo_qnty_new=$bal_woq;
							$rate=def_number_format((($selectResult[csf('rate')]/$costing_per_qty)*12),5,"");
							$amount=def_number_format($rate*$wo_qnty_new,5,"");
							$delv_date=change_date_format(str_replace("'","",$txt_delivery_date),"dd-mm-yyyy","-");;
						}
						else {
							$wo_qnty_new=$wo_qnty_pre;
							$rate=$rate_pre;
							$amount=$amount_pre;
							$booking_date=$txt_booking_date;
							$delv_date=$delivery_date;
						}
	   ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                    <td width="40">
					<? echo $i;?>
                    <input type="hidden" id="txtbookingdtlasid_<? echo $i;?>" value=" <? echo $booking_dtls_id ;?>" style="width:40px" readonly/>
                    </td>
                   
                    <td width="90"><p><? echo $selectResult[csf('po_number')];?> </p>
                    <input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $selectResult[csf('po_id')];?>" readonly/>
                    </td>
                    <td width="100">
					<? echo $garments_item[$selectResult[csf('item_number_id')]];?>
                    <input type="hidden" id="txtitemnumberid_<? echo $i;?>" value="<? echo $selectResult[csf('item_number_id')];?>" readonly />
                    </td>
                    <td width="90" >
                    <input type="hidden" id="txtembcostid_<? echo $i;?>" value="<? echo $selectResult[csf('wo_pre_cost_embe_cost_dtls')];?>" readonly/> 
                    <? echo $emblishment_name_array[$selectResult[csf('emb_name')]];?>
                    </td>
                    <td width="100">
                    <?
					if($cbo_booking_natu==1) $emb_type=$emblishment_print_type[$selectResult[csf('emb_type')]];
					else if($cbo_booking_natu==2) $emb_type=$emblishment_embroy_type[$selectResult[csf('emb_type')]];
					else if($cbo_booking_natu==3) $emb_type=$emblishment_wash_type[$selectResult[csf('emb_type')]];
					else if($cbo_booking_natu==4) $emb_type=$emblishment_spwork_type[$selectResult[csf('emb_type')]];
					
					echo $emb_type;
					?> 
                    <input type="hidden" id="txtembtype_<? echo $i;?>" value="<? echo $selectResult[csf('emb_type')];?>" readonly/>
                    </td>
                    
                    <td width="80">
                    <? echo $color_library[$selectResult[csf('color_number_id')]];?>
					<input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; background-color:<? echo $bgcolor; ?>" id="txtcolorid_<? echo $i;?>" value="<? echo $selectResult[csf('color_number_id')];?>"/>
                    </td>
                     <td width="60">
					<input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; text-align:right; font-size:11px; background-color:<? echo $bgcolor; ?>" id="txtplancut_<? echo $i;?>" value="<? if($calculation_basis==1){echo $selectResult[csf('order_quantity')];}else{ echo $selectResult[csf('plan_cut_qnty')];}?>" readonly/>
                    </td>
                    <td width="200">
					<input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; background-color:<? echo $bgcolor; ?>" id="description_<? echo $i;?>" value="<? echo $description;?>" />
                    </td>
                    <td width="60">
					<input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; text-align:right; font-size:11px; background-color:<? echo $bgcolor; ?>" id="txtreqqty_<? echo $i;?>" value="<? echo $req_qnty;?>" readonly/>
                    </td>
                    <td width="60" align="right">
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i;?>" value="<? echo $cu_woq;?>"  readonly  />
                    </td>
                    <td width="60" align="right">
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalqty_<? echo $i;?>" value="<? echo $bal_woq; ?>"  readonly  />
                    </td>
                    <td width="60" align="right">
					 <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txt_bal_qty_precost_<? echo $i;?>" value="<? echo def_number_format($wo_qnty_pre,5,"");?>" />
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo $wo_qnty_new;?>" onChange="calculate_amount(<? echo $i; ?>);calculate_amount2(<? echo $i; ?>);" />
                    </td>
                    <td width="50" align="right"> 
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo $rate;?>" onChange="calculate_amount(<? echo $i; ?>)" />
                     <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i;?>" value="<? echo def_number_format((($selectResult[csf('rate')]/$costing_per_qty)*12),5,"");?>" />
                    </td>
                    <td width="60" align="right">
                    
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>"   value="<? echo $amount; ?>"  readonly  />
                      <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_precost_<? echo $i;?>" value="<? echo def_number_format(($bal_woq*(($selectResult[csf('rate')]/$costing_per_qty)*12)),5,"");?>" />
                    </td>
                    <td width="" align="right">
                    <input type="text"   style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i;?>"  class="datepicker" value="<? echo $delv_date; ?>"  readonly  />
                    </td>
                    </tr>
	   <?
	   $i++;
					}
					
       ?>
	</tbody>
    </table>
    </div>
 
    <table width="1247" class="rpt_table" border="0" rules="all">
    <tfoot>
    <tr>
	   
                    <th width="40">SL</th>
                    <th width="90"></th>
                    <th width="100"></th>
                    <th width="90"></th>
                    <th width="100"></th>
                    <th width="80"></th>
                    <th width="60"></th>
                    <th width="200"></th>
                    <th width="60"></th>
                    <th width="60"></th>
                    <th width="60"></th>
                    <th width="60"></th>
                    <th width="50"></th>
                    <th width="60"></th>
                    <th width=""></th>               
   </tr>
   </tfoot>
   </table>
   <table width="1247" class="rpt_table" border="0" rules="all">
   <tr>
	   <td align="center" colspan="15" valign="middle" class="button_container">
                <? 
				if($updateid=="")
				{
				echo load_submit_buttons( $permission, "fnc_fabric_booking_dtls", 0,0 ,"",2) ;
				}
				else
				{
				echo load_submit_buttons( $permission, "fnc_fabric_booking_dtls", 1,0 ,"",2) ;
				}
				?>
       </td>
                                
   </tr>
   </table>
       
   
<?
}
if ($action=="show_print_booking")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_order_no_id=str_replace("'","",$txt_order_no_id);
	$cbo_booking_natu=str_replace("'","",$cbo_booking_natu);
	$cbo_gmt_item=str_replace("'","",$cbo_gmt_item);
	$txt_booking_no=str_replace("'","",$txt_booking_no);

     if($cbo_company_name==0)
	 {
		 $cbo_company_name_cond="";
	 }
	 else
	 {
		 $cbo_company_name_cond =" and a.company_name='$cbo_company_name'";
	 }
	 
	 
	 if($cbo_buyer_name==0)
	 {
		 $cbo_buyer_name_cond="";
	 }
	 else
	 {
		 $cbo_buyer_name_cond =" and a.buyer_name='$cbo_buyer_name'";
	 }
	 
	 if($txt_job_no=="")
	 {
		 $txt_job_no_cond="";
	 }
	 else
	 {
		 $txt_job_no_cond ="and a.job_no='$txt_job_no'";
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
   <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1047" class="rpt_table" >
                <thead>
                    <th width="40">SL</th>
                    <th width="90">Ord. No</th>
                    <th width="100">Gmt Item</th>
                    <th width="90">Booking Nature</th>
                    <th width="100">Booking Type</th>
                    <th width="80">Color</th>
                    <th width="60">Plan Cut</th>
                    <th width="60">Req Qty</th>
                    <th width="60">CU WOQ</th>
                    <th width="60">Bal Qty</th>
                    <th width="60">WOQ</th>
                    <th width="50">Rate</th>
                    <th width="60">Amount</th>
                    <th width="">Del. Date</th>
                </thead>
            </table>
            <div style="width:1047px; overflow-y:scroll; max-height:350px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1030" class="rpt_table" id="tbl_list_search" >
       <tbody>
       <?
				   $booking_data_array_cu=array();
				   $sql_booking_cu= sql_select( "select b.po_break_down_id,b.gmt_item,b.pre_cost_fabric_cost_dtls_id,b.gmts_color_id,b.wo_qnty,sum(b.wo_qnty) as cu_woq ,c.emb_name,c.emb_type from wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_embe_cost_dtls c where a.job_no=b.job_no and  a.job_no=c.job_no and a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.job_no='$txt_job_no' and a.booking_type=6 and a.is_short=2 and  a.status_active=1  and 	a.is_deleted=0 and  b.status_active=1  and 	b.is_deleted=0 group by b.po_break_down_id,b.gmt_item,b.pre_cost_fabric_cost_dtls_id,c.emb_name,c.emb_type,b.gmts_color_id  order by b.pre_cost_fabric_cost_dtls_id");
				   foreach($sql_booking_cu as $sql_booking_row_cu )
				   {
					  $booking_data_array_cu[$sql_booking_row_cu[csf('po_break_down_id')]][$sql_booking_row_cu[csf('gmt_item')]][$sql_booking_row_cu[csf('pre_cost_fabric_cost_dtls_id')]][$sql_booking_row_cu[csf('emb_name')]][$sql_booking_row_cu[csf('emb_type')]][$sql_booking_row_cu[csf('gmts_color_id')]][cu_woq]=$sql_booking_row_cu[csf('cu_woq')];

				   }
				  
				    $sql="select
					b.costing_per,
					c.id as wo_pre_cost_embe_cost_dtls,
					c.job_no,
					c.emb_name,
					c.emb_type,
					c.cons_dzn_gmts,
					c.rate,
					d.id as po_id,
					d.po_number,
					d.plan_cut,
					e.item_number_id,
					e.color_number_id,
					sum(e.plan_cut_qnty) as plan_cut_qnty,
					f.id as bookingdtlsid,
					f.wo_qnty as wo_qnty,
					f.rate as frate,
					f.amount as famount
					from 
					wo_po_details_master a,
					wo_pre_cost_mst b,
					wo_pre_cost_embe_cost_dtls c,
					wo_po_break_down d,
					wo_po_color_size_breakdown e,
					wo_booking_dtls f
					where
					a.job_no=b.job_no and 
					a.job_no=c.job_no and 
					a.job_no=d.job_no_mst  and
					a.job_no=e.job_no_mst  and
					a.job_no=f.job_no  and
					c.id=f.pre_cost_fabric_cost_dtls_id  and
					d.id=e.po_break_down_id and
					d.id=f.po_break_down_id and
					f.gmt_item=e.item_number_id and 
					f.gmts_color_id=e.color_number_id

					$cbo_company_name_cond $txt_job_no_cond $cbo_buyer_name_cond and
					c.emb_name=$cbo_booking_natu and
					d.id in($txt_order_no_id) and
					e.item_number_id=$cbo_gmt_item and
					f.booking_no='$txt_booking_no' and
					a.is_deleted=0 and 
					a.status_active=1 and
					b.is_deleted=0 and 
					b.status_active=1 and
					c.is_deleted=0 and 
					c.status_active=1 and
					d.is_deleted=0 and 
					d.status_active=1 and
					e.is_deleted=0 and 
					e.status_active=1
					group by 
					f.id,
					f.wo_qnty,
					f.rate,
					f.amount,
					e.item_number_id,
					e.color_number_id,
					d.id,
					d.po_number,
					d.plan_cut,
					c.id,
					c.job_no,
					c.emb_name,
					c.emb_type,
					c.cons_dzn_gmts,
					c.rate,
					b.costing_per
					order by d.id";
				   
	   
				    
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
						
						//$req_qnty=def_number_format(($selectResult[csf('cons_dzn_gmts')]*($selectResult[csf('plan_cut_qnty')]/12)),5,"");
						$req_qnty=def_number_format(($selectResult[csf('cons_dzn_gmts')]*($selectResult[csf('order_quantity')]/(12*$selectResult[csf('total_set_qnty')]))) ,2,"" );

						
						$wo_qnty=$selectResult[csf('wo_qnty')];
						$wo_qnty=def_number_format($wo_qnty,5,"");
						
						$cu_woq=$booking_data_array_cu[$selectResult[csf('po_id')]][$selectResult[csf('item_number_id')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('emb_name')]][$selectResult[csf('emb_type')]][$selectResult[csf('color_number_id')]][cu_woq];
						$cu_woq=def_number_format($cu_woq-$wo_qnty,5,"");
						
						$bal_woq=def_number_format($req_qnty-$cu_woq,5,"");
						
						$rate=$selectResult[csf('frate')];
						$rate=def_number_format($rate,5,"");
						$pre_cost_rate=$selectResult[csf('rate')];
						$pre_cost_rate=def_number_format($pre_cost_rate,5,"");

						$amount=$selectResult[csf('famount')];
						
						$amount=def_number_format($amount,5,"");
					    //$total_amount+=$amount;
	   ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
                    <td width="40">
					<? echo $i;?>
                     <input type="hidden" id="txtbookingdtlasid_<? echo $i;?>" value="<? echo $selectResult[csf('bookingdtlsid')];?>" readonly/>
                    </td>
                   
                    <td width="90"><? echo $selectResult[csf('po_number')];?> 
                    <input type="hidden" id="txtpoid_<? echo $i;?>" value="<? echo $selectResult[csf('po_id')];?>" readonly/>
                    </td>
                    <td width="100">
					<? echo $garments_item[$selectResult[csf('item_number_id')]];?>
                    <input type="hidden" id="txtitemnumberid_<? echo $i;?>" value="<? echo $selectResult[csf('item_number_id')];?>" readonly />
                    </td>
                    <td width="90" >
                    <input type="hidden" id="txtembcostid_<? echo $i;?>" value="<? echo $selectResult[csf('wo_pre_cost_embe_cost_dtls')];?>" readonly/> 
                    <? echo $emblishment_name_array[$selectResult[csf('emb_name')]];?>
                    </td>
                    <td width="100">
                    <?
					if($selectResult[csf('emb_name')]==1)
					{
						$emb_type=$emblishment_print_type[$selectResult[csf('emb_type')]];
					}
					if($selectResult[csf('emb_name')]==2)
					{
						$emb_type=$emblishment_embroy_type[$selectResult[csf('emb_type')]];
					}
					if($selectResult[csf('emb_name')]==3)
					{
						$emb_type=$emblishment_wash_type[$selectResult[csf('emb_type')]];
					}
					if($selectResult[csf('emb_name')]==4)
					{
						$emb_type=$emblishment_spwork_type[$selectResult[csf('emb_type')]];
					}
					echo $emb_type;
					?> 
                    <input type="hidden" id="txtembtype_<? echo $i;?>" value="<? echo $selectResult[csf('emb_type')];?>" readonly/>
                    </td>
                    
                    <td width="80">
                    <? echo $color_library[$selectResult[csf('color_number_id')]];?>
					<input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px; background-color:<? echo $bgcolor; ?>" id="txtcolorid_<? echo $i;?>" value="<? echo $selectResult[csf('color_number_id')];?>"/>
                    </td>
                     <td width="60">
					<input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; text-align:right; font-size:11px; background-color:<? echo $bgcolor; ?>" id="txtplancut_<? echo $i;?>" value="<? echo $selectResult[csf('plan_cut_qnty')];?>" readonly/>
                    </td>
                    <td width="60">
					<input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; text-align:right; font-size:11px; background-color:<? echo $bgcolor; ?>" id="txtreqqty_<? echo $i;?>" value="<? echo $req_qnty;?>" readonly/>
                    </td>
                    <td width="60" align="right">
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtcuwoq_<? echo $i;?>" value="<? echo $cu_woq;?>"  readonly  />
                    </td>
                    <td width="60" align="right">
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtbalqty_<? echo $i;?>" value="<? echo $bal_woq; ?>"  readonly  />
                    </td>
                    <td width="60" align="right">
					
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:#FFC" id="txtwoq_<? echo $i;?>" value="<? echo $wo_qnty;?>" onChange="calculate_amount(<? echo $i; ?>);calculate_amount2(<? echo $i; ?>);" />
                    </td>
                    <td width="50" align="right"> 
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_<? echo $i;?>" value="<? echo $rate;?>" onChange="calculate_amount(<? echo $i; ?>)" />
                    <input type="hidden"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtrate_precost_<? echo $i;?>" value="<? echo $pre_cost_rate;?>" />

                    </td>
                    <td width="60" align="right">
                    
                    <input type="text"  style="width:100%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtamount_<? echo $i;?>" value="<? echo $amount; ?>"  readonly  />
                    </td>
                    <td width="" align="right">
                    <input type="text"   style="width:90%; font-family:Verdana, Geneva, sans-serif; font-size:11px;  text-align:right; background-color:<? echo $bgcolor; ?>" id="txtddate_<? echo $i;?>"  class="datepicker" value="<? echo $txt_delivery_date; ?>"  readonly  />
                    </td>
                    </tr>
	   <?
	   $i++;
					}
       ?>
	</tbody>
    </table>
    </div>
 
    <table width="1047" class="rpt_table" border="0" rules="all">
    <tfoot>
    <tr>
	   
                    <th width="40">SL</th>
                    <th width="90"></th>
                    <th width="100"></th>
                    <th width="90"></th>
                    <th width="100"></th>
                    <th width="80"></th>
                    <th width="60"></th>
                    <th width="60"></th>
                    <th width="60"></th>
                    <th width="60"></th>
                    
                    <th width="60"></th>
                    <th width="50"></th>
                    <th width="60"></th>
                    <th width=""></th>               
   </tr>
   </tfoot>
   </table>
   <table width="1047" class="rpt_table" border="0" rules="all">
   <tr>
	   <td align="center" colspan="14" valign="middle" class="button_container">
                <? echo load_submit_buttons( $permission, "fnc_fabric_booking_dtls", 1,0 ,"",2) ; ?>
       </td>
                                
   </tr>
   </table>
<?
exit();
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
		
		$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'ESB', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_booking_mst where company_id=$cbo_company_name and entry_form=199 and booking_type=6 and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		
		$id=return_next_id( "id", "wo_booking_mst", 1 ) ;
		$field_array="id, entry_form, booking_type, is_short, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, job_no, currency_id, item_category, exchange_rate, pay_mode, source, booking_date, delivery_date, booking_month, booking_year, supplier_id, attention, tenor, ready_to_approved, budget_version, inserted_by, insert_date, calculation_basis, status_active, is_deleted"; 
		$data_array ="(".$id.",199,6,2,'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",".$txt_job_no.",".$cbo_currency.",25,".$txt_exchange_rate.",".$cbo_pay_mode.",".$cbo_source.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_booking_month.",".$cbo_booking_year.",".$cbo_supplier_name.",".$txt_attention.",".$txt_tenor.",".$cbo_ready_to_approved.",".$cbo_budget_version.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$calculation_basis.",1,0)";
		$rID=sql_insert("wo_booking_mst",$field_array,$data_array,0);
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");  
				echo "0**".$new_booking_no[0]."**".$id;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_booking_no[0]."**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);  
				echo "0**".$new_booking_no[0]."**".$id;
			}
			else{
				oci_rollback($con);  
				echo "10**".$new_booking_no[0]."**".$id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$update_id=str_replace("'", "", $update_id);
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="buyer_id*job_no*currency_id*item_category*exchange_rate*pay_mode*source*booking_date*delivery_date*booking_month*booking_year*supplier_id*attention*tenor*ready_to_approved*budget_version*updated_by*update_date*calculation_basis"; 
		 $data_array ="".$cbo_buyer_name."*".$txt_job_no."*".$cbo_currency."*25*".$txt_exchange_rate."*".$cbo_pay_mode."*".$cbo_source."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_booking_month."*".$cbo_booking_year."*".$cbo_supplier_name."*".$txt_attention."*".$txt_tenor."*".$cbo_ready_to_approved."*".$cbo_budget_version."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$calculation_basis."";
		$rID=sql_update("wo_booking_mst",$field_array,$data_array,"id",$update_id,0);
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_booking_no)."**".$update_id;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no)."**".$update_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);  
				echo "1**".str_replace("'","",$txt_booking_no)."**".$update_id;
			}
			else{
				oci_rollback($con);  
				echo "10**".str_replace("'","",$txt_booking_no)."**".$update_id;
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
		$data_array="'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*'2'*'1'";
		$rID=sql_delete("wo_booking_mst",$field_array,$data_array,"id","".$update_id."",1);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
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

if($action=="save_update_delete_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
		$bok_prev=sql_select("select b.id,b.po_break_down_id as po_id,b.gmt_item,b.emblishment_name as emb_name,b.gmts_color_id,b.booking_no,b.amount from wo_booking_dtls b where  b.job_no=$txt_job_no and b.booking_type=6 and b.status_active=1");
		foreach($bok_prev as $row){
			
			//$prev_wo_arr[$row[csf('booking_no')]]=$row[csf('booking_no')];
			$prev_wo_arr[$row[csf('po_id')]][$row[csf('gmt_item')]][$row[csf('emb_name')]][$row[csf('gmts_color_id')]]['amt']+=$row[csf('amount')];
			$prev_wo_arr2[$row[csf('id')]][$row[csf('po_id')]][$row[csf('gmt_item')]][$row[csf('emb_name')]][$row[csf('gmts_color_id')]]['prev_amt']+=$row[csf('amount')];
			$prev_wo_arr[$row[csf('po_id')]][$row[csf('gmt_item')]][$row[csf('emb_name')]][$row[csf('gmts_color_id')]]['booking_no'].=$row[csf('booking_no')].',';
		}
		
	$update_id=str_replace("'", "", $update_id);
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con);die;}		
		 $id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		 $field_array1="id, booking_mst_id, pre_cost_fabric_cost_dtls_id, entry_from, po_break_down_id, job_no, booking_no, booking_type, is_short, gmt_item, emblishment_name, gmts_color_id, wo_qnty, rate, amount, delivery_date, description, uom, inserted_by, insert_date, status_active, is_deleted";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $txtbookingdtlasid="txtbookingdtlasid_".$i;
			 $txtpoid="txtpoid_".$i;
			 $txtitemnumberid="txtitemnumberid_".$i;
			 $txtembcostid="txtembcostid_".$i;
			 $txtcolorid="txtcolorid_".$i;
			 $txtwoq="txtwoq_".$i;
			 $txtrate="txtrate_".$i;
			 $txtamount="txtamount_".$i;
			 $txtddate="txtddate_".$i;
			 $description="description_".$i;
			 $txtamount_precost="txtamount_precost_".$i;
			 $uom_id="txtuomid_".$i;
			 
			 $txtpoid=str_replace("'","",$$txtpoid);
			 $item_id=str_replace("'","",$$txtitemnumberid);
			 $emb_name=str_replace("'","",$$cbo_booking_natu);
			 $color_id=str_replace("'","",$$txtcolorid);
			 $amount=str_replace("'","",$$txtamount);
			 $pre_amount=str_replace("'","",$$txtamount_precost);
			 $booking_dtls_id=str_replace("'","",$$txtbookingdtlasid);
			 
			
			 $prev_wo_amt=$prev_wo_arr[$txtpoid][$item_id][$emb_name][$color_id]['amt'];
			 $booking_nos=rtrim($prev_wo_arr[$txtpoid][$item_id][$emb_name][$color_id]['booking_no'],',');
			 $tot_current_amt=$amount+$prev_wo_amt;
			 
			 if(($tot_current_amt>$pre_amount))
			{
				echo "budgetOver**".str_replace("'","",$txt_job_no)."**".$booking_nos."**".$pre_amount;
				disconnect($con);die;
			}
			
			  
			 if ($i!=1) $data_array1 .=",";
			// $data_array1 .="(".$id_dtls.",".$$txtembcostid.",".$$txtpoid.",".$txt_job_no.",".$txt_booking_no.",6,2,".$$txtitemnumberid.",".$$txtcolorid.",".$$txtwoq.",".$$txtrate.",".$$txtamount.",".$$txtddate.",".$$description.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			 $data_array1 .="(".$id_dtls.",".$update_id.",".$$txtembcostid.",199,".$txt_order_no_id.",".$txt_job_no.",".$txt_booking_no.",6,2,".$cbo_gmt_item.",".$cbo_booking_natu.",".$$txtcolorid.",".$$txtwoq.",".$$txtrate.",".$$txtamount.",".$$txtddate.",".$$description.",".$$uom_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			 $id_dtls=$id_dtls+1;
		 }
		 //echo "INSERT INTO wo_booking_dtls".$strTable." (".$field_array1.") VALUES ".$data_array1.""; 

		 //echo $data_array1 ; 
		 $rID=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);  
				echo "0**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$txt_booking_no);
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
	    if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1";disconnect($con); die;}		
		$field_array_up1="pre_cost_fabric_cost_dtls_id*po_break_down_id*job_no*booking_type*is_short*gmt_item*emblishment_name*gmts_color_id*wo_qnty*rate*amount*delivery_date*description*uom*updated_by*update_date*status_active*is_deleted";
		
		 $add_comma=0;
		 $id_dtls=return_next_id( "id", "wo_booking_dtls", 1 ) ;
		 $field_array1="id, booking_mst_id, pre_cost_fabric_cost_dtls_id, entry_from, po_break_down_id, job_no, booking_no, booking_type, is_short, gmt_item, emblishment_name, gmts_color_id, wo_qnty, rate, amount, delivery_date, description, uom, inserted_by, insert_date, status_active, is_deleted";
		 
		 for ($i=1;$i<=$total_row;$i++)
		 {
			$txtbookingdtlasid="txtbookingdtlasid_".$i;
			 $txtpoid="txtpoid_".$i;
			 $txtitemnumberid="txtitemnumberid_".$i;
			 $txtembcostid="txtembcostid_".$i;
			 $txtcolorid="txtcolorid_".$i;
			 $txtwoq="txtwoq_".$i;
			 $txtrate="txtrate_".$i;
			 $txtamount="txtamount_".$i;
			 $txtddate="txtddate_".$i;
			 $description="description_".$i;
			 $txtamount_precost="txtamount_precost_".$i;
			 $uom_id="txtuomid_".$i;
			 
			 $txtpoid=str_replace("'","",$$txtpoid);
			 $item_id=str_replace("'","",$$txtitemnumberid);
			 $emb_name=str_replace("'","",$$cbo_booking_natu);
			 $color_id=str_replace("'","",$$txtcolorid);
			 $amount=str_replace("'","",$$txtamount);
			 $pre_amount=str_replace("'","",$$txtamount_precost);
			 $booking_dtls_id=str_replace("'","",$$txtbookingdtlasid);
			  
			 $prev_wo_amt2=$prev_wo_arr2[$booking_dtls_id][$txtpoid][$item_id][$emb_name][$color_id]['prev_amt'];
			 $prev_wo_amt=$prev_wo_arr[$txtpoid][$item_id][$emb_name][$color_id]['amt'];
			 $booking_nos=rtrim($prev_wo_arr[$txtpoid][$item_id][$emb_name][$color_id]['booking_no'],',');
			 $tot_current_amt=($amount+$prev_wo_amt)-$prev_wo_amt2;
			 
			 if(($tot_current_amt>$pre_amount))
			{
				echo "budgetOver**".str_replace("'","",$txt_job_no)."**".$booking_nos."**".$pre_amount;
				disconnect($con);die;
			}
			
			if(str_replace("'",'',$$txtbookingdtlasid)!="")
			{
				$id_arr[]=str_replace("'",'',$$txtbookingdtlasid);
				$data_array_up1[str_replace("'",'',$$txtbookingdtlasid)] =explode("*",("".$$txtembcostid."*".$txt_order_no_id."*".$txt_job_no."*6*2*".$cbo_gmt_item."*".$cbo_booking_natu."*".$$txtcolorid."*".$$txtwoq."*".$$txtrate."*".$$txtamount."*".$$txtddate."*".$$description."*".$$uom_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0"));
			}
			if(str_replace("'",'',$$txtbookingdtlasid)=="")
			{
				if ($add_comma!=0) $data_array1 .=",";
			 //$data_array1 .="(".$id_dtls.",".$$txtembcostid.",".$$txtpoid.",".$txt_job_no.",".$txt_booking_no.",6,2,".$$txtitemnumberid.",".$$txtcolorid.",".$$txtwoq.",".$$txtrate.",".$$txtamount.",".$$txtddate.",".$$description.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			  $data_array1 .="(".$id_dtls.",".$update_id.",".$$txtembcostid.",199,".$txt_order_no_id.",".$txt_job_no.",".$txt_booking_no.",6,2,".$cbo_gmt_item.",".$cbo_booking_natu.",".$$txtcolorid.",".$$txtwoq.",".$$txtrate.",".$$txtamount.",".$$txtddate.",".$$description.",".$$uom_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$id_dtls=$id_dtls+1;
				$add_comma++;
			}
		 }
		// echo bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr );
		$rID=execute_query(bulk_update_sql_statement( "wo_booking_dtls", "id", $field_array_up1, $data_array_up1, $id_arr ));
		$rID1=1;
		if($data_array1 !="")
		 {
			$rID1=sql_insert("wo_booking_dtls",$field_array1,$data_array1,0);
		 }

        check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $rID1){
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
			if($rID && $rID1){
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
	if ($operation==2)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//echo "10**update wo_booking_dtls set updated_by='".$_SESSION['logic_erp']['user_id']."', update_date='".$pc_date_time."', status_active=0,is_deleted =1 where  booking_no =$txt_booking_no and po_break_down_id=$txt_order_no_id and gmt_item=$cbo_gmt_item and emblishment_name=$cbo_booking_natu";die;
		$rID=execute_query( "update wo_booking_dtls set updated_by='".$_SESSION['logic_erp']['user_id']."', update_date='".$pc_date_time."', status_active=0,is_deleted =1 where  booking_no =$txt_booking_no and po_break_down_id=$txt_order_no_id and gmt_item=$cbo_gmt_item and emblishment_name=$cbo_booking_natu",0);	
			
		if($db_type==0)
		{
			if($rID ){
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
			if($rID ){
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

if ($action=="print_booking_list_view")
{
	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$po_num=return_library_array( "select job_no, job_no_prefix_num from wo_po_details_master",'job_no','job_no_prefix_num');
	$po_array=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');

	$arr=array (2=>$comp,3=>$buyer_arr,4=>$po_num,5=>$po_array,6=>$garments_item,7=>$emblishment_name_array,8=>$suplier,9=>$approved,10=>$is_ready);
	
	  $sql= "select a.booking_no_prefix_num, a.booking_no,a.booking_date,company_id,a.buyer_id,a.job_no,b.po_break_down_id,b.gmt_item,c.emb_name,a.supplier_id,a.is_approved,a.ready_to_approved from wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_embe_cost_dtls c where a.job_no=b.job_no and  a.job_no=c.job_no and a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.booking_no='$data' and a.booking_type=6 and a.is_short=2 and  a.status_active=1  and 	a.is_deleted=0 and  b.status_active=1  and 	b.is_deleted=0 group by a.booking_no_prefix_num,a.booking_no,a.booking_date,company_id,a.buyer_id,a.job_no,b.po_break_down_id,b.gmt_item,c.emb_name,a.supplier_id,a.is_approved,a.ready_to_approved order by a.booking_no";
	
	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,PO number,Gmts Item,Embl Name,Supplier,Approved,Is-Ready", "80,80,70,100,90,200,80,80,50,50","1020","320",0, $sql , "update_booking_data", "po_break_down_id,gmt_item,emb_name", "", 1, "0,0,company_id,buyer_id,job_no,po_break_down_id,gmt_item,emb_name,supplier_id,is_approved,ready_to_approved", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,job_no,po_break_down_id,gmt_item,emb_name,supplier_id,is_approved,ready_to_approved", '','','0,0,0,0,0,0,0,0,0,0,0','','');
}

if ($action=="fabric_booking_popup")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	?>
	<script>
	function js_set_value(booking_no)
	{
		document.getElementById('selected_booking').value=booking_no;
		parent.emailwindow.hide();
	}
	function check_orphan( str )
	{
		if($("#chk_orphan").prop('checked')==true)
			$('#chk_orphan').val(1);
		else
			$('#chk_orphan').val(0);
	}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="970" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
        <thead>
            <th colspan="9">
              <?
               echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
              ?>
            </th>
        </thead>
        <thead>                	 
            <th width="150">Company Name</th>
            <th width="150">Buyer Name</th>
            <th width="100">Booking No</th>
            <th width="100">Job No</th>
            <th width="80">File No</th>
            <th width="80">Ref. No</th>
            <th width="130" colspan="2">Date Range</th>
            <th> <input type="checkbox" id="chk_orphan" onClick="check_orphan(this.value)" value="0"> Orphan WO</th>           
        </thead>
            <tr class="general">
                <td> <input type="hidden" id="selected_booking">
                <? 
                echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'print_booking_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                ?>
                </td>
                <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --" ); ?></td>
                <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
                <td><input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px"></td>
                <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"></td> 
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"></td> 
                <td>
                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('chk_orphan').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value, 'create_booking_search_list_view', 'search_div', 'print_booking_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                </td>
            </tr>
            <tr>
                <td align="center" colspan="9" valign="middle"><?  echo load_month_buttons(1); ?></td>
            </tr>
         </table>
    	<div id="search_div"> </div>    
    </form>
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
	if ($data[0]!=0) $company="  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0)
	{
	$booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[4]";
	if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
	$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]";	
	if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	if($data[6]==4 || $data[6]==0)
		{
		 if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  "; else $booking_cond="";
		}
    if($data[6]==1)
		{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num ='$data[5]' "; else $booking_cond="";
		}
   if($data[6]==2)
		{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[5]%'  $booking_year_cond  "; else $booking_cond="";
		}
	if($data[6]==3)
		{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]'  $booking_year_cond  "; else $booking_cond="";
		}
		
	//if (str_replace("'","",$data[9])!="") $file_cond=" and b.file_no ='$data[9]'   "; else $file_cond="";
	//if (str_replace("'","",$data[10])!="") $ref_cond=" and b.grouping='$data[10]'"; else $ref_cond="";
		
	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$po_num=return_library_array( "select job_no, job_no_prefix_num from wo_po_details_master",'job_no','job_no_prefix_num');
	$po_array=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	
	
	//echo $data[7];
	//$arr=array (2=>$comp,3=>$buyer_arr,4=>$po_num,5=>$po_array,6=>$item_category,7=>$fabric_source,8=>$suplier,9=>$approved,10=>$is_ready);
	//$sql= "select booking_no_prefix_num, booking_no,booking_date,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,supplier_id,is_approved,ready_to_approved from wo_booking_mst  where $company $buyer $booking_date and booking_type=6 and is_short=2 and  status_active=1  and 	is_deleted=0 order by booking_no"; 

	//echo  create_list_view("list_view", "WO No,WO Date,Company,Buyer,Job No.,PO number,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "80,80,70,100,90,200,80,80,50,50","1020","320",0, $sql , "js_set_value", "booking_no", "", 1, "0,0,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,job_no,po_break_down_id,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','','0,0,0,0,0,0,0,0,0,0,0','','');
	
	
	if(trim($data[9])!="") $file_cond=" and d.file_no like '%$data[9]'"; else $file_cond="";
	if(trim($data[10])!="") $ref_cond=" and d.grouping like '%$data[10]'"; else $ref_cond="";
	
	if(trim($data[8])!="") $job_cond=" and a.job_no like '%$data[8]%'"; else $job_cond="";
	$arr=array (2=>$buyer_arr,5=>$po_num,6=>$po_array,7=>$garments_item,8=>$emblishment_name_array,9=>$suplier,10=>$approved,11=>$is_ready);
	
	
	if($data[7]==0)
			  $sql= "select a.id, a.booking_no_prefix_num, a.booking_no,a.booking_date,company_id,a.buyer_id,a.job_no,b.po_break_down_id,b.gmt_item,c.emb_name,a.supplier_id,a.is_approved,a.ready_to_approved,d.file_no,d.grouping from wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_embe_cost_dtls c,wo_po_break_down d where $company $buyer $booking_date $booking_cond and d.job_no_mst=b.job_no and d.id=b.po_break_down_id and a.job_no=b.job_no and  a.job_no=c.job_no and a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id  and a.booking_type=6 and a.is_short=2 and  a.status_active=1  and a.is_deleted=0 and  b.status_active=1  and b.is_deleted=0 $job_cond $ref_cond $file_cond group by a.id, a.booking_no_prefix_num, a.booking_no,a.booking_date,company_id,a.buyer_id,a.job_no,b.po_break_down_id,b.gmt_item,c.emb_name,a.supplier_id,a.is_approved,a.ready_to_approved,d.file_no,d.grouping order by a.id DESC";
	else
		$sql= "select a.id, a.booking_no_prefix_num, a.booking_no,a.booking_date,company_id,a.buyer_id,a.job_no,c.emb_name,a.supplier_id,a.is_approved,a.ready_to_approved,1 as po_break_down_id,2 as gmt_item,'' as file_no,'' as grouping  from wo_booking_mst a,  wo_pre_cost_embe_cost_dtls c where $company $buyer $booking_date $booking_cond  and  a.job_no=c.job_no  and a.booking_type=6 and a.is_short=2 and  a.status_active=1  and 	a.is_deleted=0 $job_cond group by a.id,a.booking_no_prefix_num, a.booking_no,a.booking_date,company_id,a.buyer_id,a.job_no, c.emb_name,a.supplier_id,a.is_approved,a.ready_to_approved order by a.id DESC ";
	//echo $sql;
	 echo  create_list_view("list_view", "Booking No,Booking Date,Buyer,Job No.,File No,Ref. No,PO number,Gmts Item,Embl Name,Supplier,Approved,Is-Ready", "60,65,60,90,60,80,100,110,80,110,50","970","280",0, $sql , "js_set_value", "booking_no", "", 1, "0,0,buyer_id,0,0,0,po_break_down_id,gmt_item,emb_name,supplier_id,is_approved,ready_to_approved", $arr , "booking_no_prefix_num,booking_date,buyer_id,job_no,file_no,grouping,po_break_down_id,gmt_item,emb_name,supplier_id,is_approved,ready_to_approved", '','','0,3,0,0,0,0,0,0,0,0,0,0','','');
	 exit();
}

if ($action=="populate_data_from_search_popup")
{
	 
	 $pre_cost_arr=array();
	 $res_sql=sql_select("select a.job_no,a.exchange_rate,b.currency_id from wo_pre_cost_mst a,wo_po_details_master b where b.job_no=a.job_no and  a.status_active=1 and  b.status_active=1");
	 foreach($res_sql as $row)
	 {
		 $pre_cost_arr[$row[csf('job_no')]]['rate']=$row[csf('exchange_rate')];
		 $pre_cost_arr[$row[csf('job_no')]]['curr']=$row[csf('currency_id')]; 
	 }
	 $sql= "select booking_no,booking_date,company_id,buyer_id,budget_version,job_no,currency_id,exchange_rate,pay_mode,booking_month,supplier_id,attention,tenor,delivery_date,source,booking_year,is_approved,ready_to_approved,calculation_basis,id from wo_booking_mst  where booking_no='$data'"; 
	
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
		
		$pre_cost_curr=$pre_cost_arr[$row[csf('job_no')]]['curr'];
		$pre_cost_rate=$pre_cost_arr[$row[csf('job_no')]]['rate'];
		if($pre_cost_curr==$row[csf("currency_id")])
		{
			$ex_rate=1;	
		}
		else
		{
			$ex_rate=$pre_cost_rate;	
		}
		$budget_version=$row[csf("budget_version")];
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";  
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('cbo_budget_version').value = '".$row[csf("budget_version")]."';\n";
		if($budget_version!=0)
		{
			echo "$('#cbo_budget_version').attr('disabled',true);\n";
		}
		else
		{
			echo "$('#cbo_budget_version').attr('disabled',false);\n";
		}
		
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$ex_rate."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_booking_month').value = '".$row[csf("booking_month")]."';\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_tenor').value = '".$row[csf("tenor")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
	    echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
		echo "document.getElementById('cbo_booking_year').value = '".$row[csf("booking_year")]."';\n";
		echo "document.getElementById('id_approved_id').value = '".$row[csf("is_approved")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('calculation_basis').value = '".$row[csf("calculation_basis")]."';\n";
		//echo "check_exchange_rate();\n";
		if($row[csf("is_approved")]==1)
		{
			///echo "document.getElementById('app_sms').innerHTML = 'This booking is approved';\n";
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is approved';\n";
		}
		else
		{
			//echo "document.getElementById('app_sms').innerHTML = '';\n";
			echo "document.getElementById('app_sms2').innerHTML = '';\n";
		}
	 }
}







if($action=="terms_condition_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
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
		http.open("POST","print_booking_controller.php",true);
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
        	<form id="termscondi_1" autocomplete="off">
           <input type="hidden" id="txt_booking_no" name="txt_booking_no" value="<? echo str_replace("'","",$txt_booking_no) ?>"/>
            
            
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
							$i++;
							?>
                            	<tr id="settr_1" align="center">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  /> 
                                    </td>
                                    <td> 
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
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
							$i++;
					?>
                    <tr id="settr_1" align="center">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  /> 
                                    </td>
                                    <td>
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> );" />
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

if($action=="show_trim_booking_report")
{
	//echo "fgfgfffgf";die;
 extract($_REQUEST);
//$data=explode('*',$data);
//$txt_booking_no=$data[0];
$txt_booking_no=str_replace("'","",$txt_booking_no);
$calculation_basis=str_replace("'","",$calculation_basis);

$sql_booking=sql_select("select a.company_id, a.supplier_id, a.job_no, a.booking_date, a.delivery_date, a.buyer_id, a.currency_id, a.pay_mode, a.source, a.exchange_rate from wo_booking_mst a where a.is_deleted=0 and a.status_active=1 and a.booking_no='$txt_booking_no' and a.booking_type=6 and a.is_short=2");


$cbo_company_name=$sql_booking[0][csf("company_id")];
$cbo_supplier_name=$sql_booking[0][csf("supplier_id")];
//echo $cbo_supplier_name;die;
$txt_job_no=$sql_booking[0][csf("job_no")];
$txt_booking_date=$sql_booking[0][csf("booking_date")];
$txt_delivery_date=$sql_booking[0][csf("delivery_date")];
$cbo_buyer_name=$sql_booking[0][csf("buyer_id")];
//$txt_booking_no=str_replace("'","",$txt_booking_no);
$cbo_currency=$sql_booking[0][csf("currency_id")];
$cbo_pay_mode=$sql_booking[0][csf("pay_mode")];
$cbo_source=$sql_booking[0][csf("source")];
$txt_exchange_rate=$sql_booking[0][csf("exchange_rate")];


if($txt_exchange_rate=="" || $txt_exchange_rate==0)
{
  $txt_exchange_rate=1;	
}

$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_name= return_library_array("select id, short_name from  lib_buyer","id","short_name");
$supplier_name=return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
$color_id=return_library_array("select id,color_name from lib_color", "id", "color_name");
$job_no_qty_arr = return_library_array( "select job_no, job_quantity from wo_po_details_master",'job_no','job_quantity');
//$order_arr = return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');

$varcode_work_order_no=$txt_booking_no;

?>
<div style="width:1100px;" align="center">
 <table width="1100" cellspacing="0" align="center" border="0" >
        <tr>
            <td colspan="4" align="center" style="font-size:28px"><strong><? echo $company_library[$cbo_company_name]; ?></strong></td>
        </tr>
        <tr class="form_caption">
            <td colspan="4" align="center">
				<? 		
					
					$style_ref_no=sql_select("SELECT c.id, d.style_ref_no FROM wo_po_break_down c,wo_po_details_master d WHERE d.job_no=c.job_no_mst and d.job_no='$txt_job_no' and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by c.id,d.style_ref_no");
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
					$nameArray_sup=sql_select( "select address_1 from lib_supplier where id=$cbo_supplier_name");
                ?> 
            </td>
             <td rowspan="3" colspan="2" id="barcode_img_id" valign="top">
               
             </td>  
        </tr>
        <tr>
            <td colspan="4" align="center" style="font-size:20px"><strong>Embellishment Work Order</strong></td>
        </tr>
         <tr>
            <td colspan="6" align="center" style="font-size:20">&nbsp;</td>
        </tr>
         <tr>
            <td style="font-size:20" width="130" >Supplier Name </td> <td style="font-size:20"  width="170">: &nbsp;<? echo $supplier_name[$cbo_supplier_name]; ?></td>
            <td style="font-size:20">Work Order No</td> <td style="font-size:20">: &nbsp;<? echo $txt_booking_no; //change_date_format($txt_booking_date); ?></td>
            <td style="font-size:20">Work Order Date  </td> <td style="font-size:20">: &nbsp;<? echo change_date_format($txt_booking_date); ?></td>
        </tr>
         <tr>
            <td style="font-size:20">Supplier Address  </td> <td style="font-size:20">: &nbsp;<? echo $nameArray_sup[0][csf('address_1')]; ?></td>
            <td style="font-size:20"> Job No. </td> <td style="font-size:20">: &nbsp;<? echo $txt_job_no; ?></td>
           
            <td style="font-size:20">Delivery Date </td> <td style="font-size:20">: &nbsp;<? echo change_date_format($txt_delivery_date); ?></td>
        </tr>
        <tr>
            <td style="font-size:20"> Buyer  </td> <td style="font-size:20">: &nbsp;<? echo $buyer_name[$cbo_buyer_name]; ?></td>
            <td style="font-size:20">Currency </td> <td style="font-size:20">: &nbsp;<? echo $currency[$cbo_currency]; ?></td>
            <td style="font-size:20">Exchange Rate  </td> <td style="font-size:20">: &nbsp;<? echo $txt_exchange_rate; ?></td>
        </tr>
        <tr>
            <td style="font-size:20">Style Ref.  </td> <td style="font-size:20">: &nbsp;<? echo $style_ref_no[0][csf('style_ref_no')]; ?></td>
            <td style="font-size:20"> Pay mode </td> <td style="font-size:20">: &nbsp;<? echo $pay_mode[$cbo_pay_mode]; ?></td>
            <td style="font-size:20">Source </td>  <td style="font-size:20">: &nbsp;<? echo $source[$cbo_source]; ?></td> 
        </tr>
          <tr>
          <td style="font-size:20">Article No</td> 
          <td style="font-size:20">: &nbsp;
		  <? 
		  $txt_attention=return_field_value("attention"," wo_booking_mst","booking_no='$txt_booking_no'");
		  $nameArray_article_number=sql_select( "select article_number from  wo_po_color_size_breakdown where job_no_mst='$txt_job_no' and article_number!=0 and is_deleted=0 and status_active=1");
		  echo $nameArray_article_number[0][csf('article_number')]; 
		  ?>
          </td>
          
          <td style="font-size:20">Image:</td> 
            <td> 
			<?
			$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$txt_job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
			foreach($data_array as $row)
			{?>
				<img src='../../<? echo $row[csf('image_location')]; ?>' height='65' width='90' align="middle" />
			<? 
			}?>
           </td>
           <td style="font-size:20">Attention</td> 
          <td style="font-size:20">: &nbsp;
           <?
           echo $txt_attention; 
		  ?>
          </td>
        </tr>
        
 </table><br/>
 <div style="width:100%;">
 <?
 $costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='$txt_job_no'");
	if($costing_per==1)
	{
		$costing_per_dzn="1 Dzn";
	}
	else if($costing_per==2)
	{
		$costing_per_dzn="1 Pcs";
	}
	else if($costing_per==3)
	{
		$costing_per_dzn="2 Dzn";
	}
	else if($costing_per==4)
	{
		$costing_per_dzn="3 Dzn";
	}
	else if($costing_per==5)
	{
		$costing_per_dzn="4 Dzn";
	}
 ?>
 <table width="1100" cellspacing="0" align="center" border="1" rules="all" class="rpt_table" >
      <thead bgcolor="#dddddd" align="center">
         <th width="30">SL</th>
         <th width="100">Order No.</th>
         <th width="100">Order Qty(PCS)</th>
         <th width="80">Shipment Date</th>
         <th width="80">Emb. Name</th>
         <th width="80">Emb. type</th>
         <th width="150">Description</th>
         <th width="80">Color</th>
         <th width="80">Plan Cut(PCS)</th>
         <th width="100">Req. Qty /DZN</th>
         <th width="80">CU WOQ/DZN</th>
         <th width="100">Bal Qty/DZN</th>
         <th width="80">WQO/DZN</th>
         <th width="70">Rate/DZN</th>
         <th width="70">Amount/DZN</th>
      </thead>
      <?
	   			   $booking_data_array_cu=array();
				   $sql_booking_cu= sql_select( "select b.po_break_down_id,b.gmt_item,b.pre_cost_fabric_cost_dtls_id,b.gmts_color_id,b.wo_qnty,sum(b.wo_qnty) as cu_woq ,c.emb_name,c.emb_type from wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_embe_cost_dtls c where a.job_no=b.job_no and  a.job_no=c.job_no and a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.job_no='$txt_job_no' and a.booking_type=6 and a.is_short=2 and  a.status_active=1  and 	a.is_deleted=0 and  b.status_active=1  and 	b.is_deleted=0  group by b.po_break_down_id,b.gmt_item,b.pre_cost_fabric_cost_dtls_id,c.emb_name,c.emb_type,b.gmts_color_id,b.wo_qnty  order by b.pre_cost_fabric_cost_dtls_id");
				   foreach($sql_booking_cu as $sql_booking_row_cu )
				   {
					  $booking_data_array_cu[$sql_booking_row_cu[csf('po_break_down_id')]][$sql_booking_row_cu[csf('gmt_item')]][$sql_booking_row_cu[csf('pre_cost_fabric_cost_dtls_id')]][$sql_booking_row_cu[csf('emb_name')]][$sql_booking_row_cu[csf('emb_type')]][$sql_booking_row_cu[csf('gmts_color_id')]]['cu_woq']=$sql_booking_row_cu[csf('cu_woq')];
				   }
				  //f.id as bookingdtlsid
				 $sql="select
					a.total_set_qnty,b.costing_per,c.id as wo_pre_cost_embe_cost_dtls,c.job_no,c.emb_name,c.emb_type,c.cons_dzn_gmts,c.rate,d.id as po_id,d.po_number,d.pub_shipment_date,d.plan_cut,e.item_number_id,e.color_number_id,sum(e.plan_cut_qnty) as plan_cut_qnty,sum(e.order_quantity) as order_quantity,d.po_quantity,f.wo_qnty as wo_qnty,f.rate as frate,f.amount as famount,f.description 
					from wo_po_details_master a, wo_pre_cost_mst b,wo_pre_cost_embe_cost_dtls c,wo_po_break_down d,wo_po_color_size_breakdown e,wo_booking_dtls f 
					where a.job_no=b.job_no and a.job_no=c.job_no and a.job_no=d.job_no_mst  and a.job_no=e.job_no_mst  and a.job_no=f.job_no  and c.id=f.pre_cost_fabric_cost_dtls_id  and d.id=e.po_break_down_id and d.id=f.po_break_down_id and f.gmt_item=e.item_number_id and  f.gmts_color_id=e.color_number_id and f.booking_no='$txt_booking_no' and a.is_deleted=0 and  a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 and f.wo_qnty>0 
					group by   f.wo_qnty,f.rate,f.amount,e.item_number_id,e.color_number_id,d.id,d.po_number,d.po_quantity,d.pub_shipment_date,d.plan_cut,c.id,c.job_no,c.emb_name,c.emb_type,c.cons_dzn_gmts,c.rate,b.costing_per,a.total_set_qnty,f.description order by d.id";
					//echo $sql;
					
					$order_id_array=array();
					$i=1;
					$k=1;
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
						
						//$req_qnty=def_number_format(($selectResult[csf('cons_dzn_gmts')]*($selectResult[csf('plan_cut_qnty')]/$costing_per_qty)),5,"");
						//echo $calculation_basis.'=='.$selectResult[csf('order_quantity')].'=='.$selectResult[csf('cons_dzn_gmts')].'=='.$selectResult[csf('total_set_qnty')].'='.$costing_per_qty.',';
						if($calculation_basis==1) //Order Qty
						{
						$req_qnty=def_number_format(($selectResult[csf('cons_dzn_gmts')]*($selectResult[csf('order_quantity')]/($costing_per_qty))) ,2,"" );
						}
						else
						{
						$req_qnty=def_number_format(($selectResult[csf('cons_dzn_gmts')]*($selectResult[csf('plan_cut_qnty')]/($costing_per_qty))) ,2,"" );
						}

						$wo_qnty1=$selectResult[csf('wo_qnty')];
						//$wo_qnty=def_number_format($wo_qnty1,5,"");
						$cu_woq=$booking_data_array_cu[$selectResult[csf('po_id')]][$selectResult[csf('item_number_id')]][$selectResult[csf('wo_pre_cost_embe_cost_dtls')]][$selectResult[csf('emb_name')]][$selectResult[csf('emb_type')]][$selectResult[csf('color_number_id')]]['cu_woq'];
						$cu_woq=def_number_format($cu_woq-$wo_qnty1,5,"");
						$bal_woq=def_number_format($req_qnty-$cu_woq,5,"");
						$rate=$selectResult[csf('frate')];
						$description=$selectResult[csf('description')];
						$rate=def_number_format($rate,5,"");
						$pre_cost_rate=$selectResult[csf('rate')];
						$pre_cost_rate=def_number_format($pre_cost_rate,5,"");
						$amount=$selectResult[csf('famount')];
						$amount=def_number_format($amount,5,"");
						if (!in_array($selectResult[csf('po_id')],$order_id_array))
						{  
							if($k!=1)
							{
							
								?>
								<tr>
                                    <td colspan="12" align="right"><b>Sub Total</b></td> 
                                    <td   align="right"><b><? echo number_format($wo_qnty,2); ?></b> </td> 
                                    <td  align="right">&nbsp;</td>
                                    <td  align="right"><b><? echo number_format($amount_tot,2);?> </b></td>
								</tr>
								<?		
							
							}
							
							unset($wo_qnty);
							unset($amount_tot);
							$k++;
							$order_id_array[]=$selectResult[csf('po_id')];	
						}
						
						?>
						<tr <? echo $bgcolor; ?>>
                            <td width="30"><? echo $i; ?> </td> 
                            <td width="100"><p><? echo $selectResult[csf('po_number')];?></p> </td>
                            <td width="100" align="right"><p><? echo $selectResult[csf('po_quantity')]*$selectResult[csf('total_set_qnty')];?></p> </td>
                            <td width="80" align="center"><p><? echo change_date_format($selectResult[csf('pub_shipment_date')])?></p> </td> 
                            <td  width="80" align="center"><p><? echo $emblishment_name_array[$selectResult[csf('emb_name')]]; ?> </p></td> 
                            <td  width="80" align="center">
                            <p>
							<? 
							 
							if($selectResult[csf('emb_name')]==1)
							{
								$emb_type=$emblishment_print_type[$selectResult[csf('emb_type')]];
							}
							if($selectResult[csf('emb_name')]==2)
							{
								$emb_type=$emblishment_embroy_type[$selectResult[csf('emb_type')]];
							}
							if($selectResult[csf('emb_name')]==3)
							{
								$emb_type=$emblishment_wash_type[$selectResult[csf('emb_type')]];
							}
							if($selectResult[csf('emb_name')]==4)
							{
								$emb_type=$emblishment_spwork_type[$selectResult[csf('emb_type')]];
							}
							echo $emb_type;
							
							
							//echo $emblishment_print_type[$selectResult[csf('emb_type')]];
							?> 
                            </p>
                            </td>
                            <td  width="150"><div style="word-break:break-all; width:98px"><? echo $description; ?> &nbsp; </div> </td>
                            <td width="80"><p><? echo $color_id[$selectResult[csf('color_number_id')]]; ?> </p></td> 
                            <td width="80" align="right"><p><? echo $selectResult[csf('plan_cut_qnty')]; ?> </p></td> 
                            <td width="100" align="right"><p><? echo number_format($req_qnty,2); ?></p> </td>
                            <td  width="80" align="right"><p><? echo number_format($cu_woq,2); ?> </p></td>
                            <td width="100" align="right"><p><?  echo number_format($bal_woq,2)?></p> </td> 
                            <td width="80" align="right"><p><? $w_qty=$selectResult[csf('wo_qnty')]; echo number_format($w_qty,2); ?></p></td>
                            <td width="70" align="right"><p> <? echo number_format($rate,2); ?> </p></td> 
                            <td width="70" align="right"><p> <? echo number_format($amount,2); ?> </p></td>
						</tr>
						
						<?
						$i++;
						$wo_qnty+=$w_qty;
						$amount_tot+=$amount;
						
						$wo_qnty_r+=$w_qty;
						$amount_tot_t+=$amount;
						//end foreach	
					}
 ?> 
                      <tr>
                          <td  align="right" colspan="12"><b>Sub Total</b></td> 
                          <td  align="right"><b><? echo number_format($wo_qnty,2);//]$emblishment_name_array[; ?> </b></td> 
                          <td  align="right">&nbsp;</td>
                          <td  align="right"><b><? echo number_format($amount_tot,2);?></b> </td>
                      </tr>
                      <tr>
                          <td  align="right" colspan="12"><strong>Grand Total</strong></td> 
                          <td  align="right"><b><? echo number_format($wo_qnty_r,2);//]$emblishment_name_array[; ?> </b></td> 
                          <td  align="right">&nbsp;</td>
                          <td  align="right"><b><? echo number_format($amount_tot_t,2);?> </b></td>
                      </tr>
 </table>
    <br/>    
					<?
						echo get_spacial_instruction($txt_booking_no);
					?>
							
  <br> <br>
  <?
  if(str_replace("'","",$show_comment)==1) //Aziz
  {
  ?>
  <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table" >
                <thead>
                <tr> <td colspan="8" align="center"> <b>Comments</b> </td></tr>
                <tr>
                    <th width="40">SL</th>
                    <th width="200">Embl Name</th>
                    <th width="100">Booking Type</th>
                    <th width="200">PO No</th>
                    <th width="80">Pre-Cost/Budget Value</th>
                    <th width="80">WO Value</th>
                    <th width="80">Balance</th>
                    <th width="">Comments </th>
                </tr>
            </thead>
       <tbody>
       <?
					$sql_po_qty=sql_select("select b.id as po_id,sum(b.plan_cut) as order_quantity,(sum(b.po_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.is_deleted=0  and a.status_active=1 group by b.id,a.total_set_qnty");
					foreach( $sql_po_qty as $row)
					{
						$po_qty_arr[$row[csf("po_id")]]['order_quantity']=$row[csf("order_quantity")];
						//$po_qty_arr[$row[csf("po_id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
					}
					
					$sql_cons_data=sql_select("select a.job_no,a.emb_name,a.amount,a.rate as rate from wo_pre_cost_embe_cost_dtls a  where  a.is_deleted=0  and a.status_active=1");
									 
					foreach($sql_cons_data as $row)
					{
						$pre_cost_data_arr[$row[csf("job_no")]][$row[csf("emb_name")]]['amount']=$row[csf("amount")];
						$pre_cost_data_arr[$row[csf("job_no")]][$row[csf("emb_name")]]['rate']=$row[csf("rate")];
					}
					
					  
					  $embl_booking_array=array();$embl_booking_data=array();
					  $sql_wo=sql_select("select b.po_break_down_id as po_id,b.booking_no,a.exchange_rate,b.pre_cost_fabric_cost_dtls_id as fab_dtls_id,sum(b.amount) as amount  from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.booking_type=6 and b.is_short=2 and 
					b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0   group by  b.po_break_down_id, b.booking_no,b.pre_cost_fabric_cost_dtls_id,a.exchange_rate");
						foreach($sql_wo as $row)
						{ //pre_cost_fabric_cost_dtls_id
							$embl_booking_array[$row[csf('booking_no')]][$row[csf('fab_dtls_id')]][$row[csf('po_id')]]['amount']=$row[csf('amount')];
							$embl_booking_array[$row[csf('booking_no')]][$row[csf('fab_dtls_id')]][$row[csf('po_id')]]['exchange_rate']=$row[csf('exchange_rate')];
						}
						
						if($db_type==0) $group_concat="group_concat( distinct booking_no,',') AS booking_no";
					else if($db_type==2)  $group_concat="listagg(cast(booking_no as varchar2(4000)),',') within group (order by booking_no) AS booking_no";
					
					
					$wo_book=sql_select("select po_break_down_id,pre_cost_fabric_cost_dtls_id as fab_dtls_id,$group_concat,sum(amount) as amount  from wo_booking_dtls where 
					 booking_type=6 and is_short=2 and status_active=1 and is_deleted=0 group by po_break_down_id,pre_cost_fabric_cost_dtls_id");
					foreach($wo_book as $row)
						{ //pre_cost_fabric_cost_dtls_id
							$embl_booking_data[$row[csf('po_break_down_id')]][$row[csf('fab_dtls_id')]]['booking_no']=$row[csf('booking_no')];
						}
					  
					  
					
					 $sql_booking_cu="select b.po_break_down_id,a.job_no,c.emb_type,b.pre_cost_fabric_cost_dtls_id,sum(b.amount) as amount,c.emb_name from wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_embe_cost_dtls c where a.job_no=b.job_no and  a.job_no=c.job_no and a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.booking_no='$txt_booking_no' and a.booking_type=6 and a.is_short=2 and  a.status_active=1 and  b.status_active=1  and 	b.is_deleted=0  and 	a.is_deleted=0 and b.amount>0  group by b.po_break_down_id,a.job_no,b.pre_cost_fabric_cost_dtls_id,c.emb_name,emb_type  order by b.po_break_down_id";
					 //echo $sql_booking_cu;
				
					//$exchange_rate=return_field_value("exchange_rate", " wo_booking_mst", "booking_no='".$txt_booking_no."'");
					$i=1; $tot_pre_amount=0;$tot_embl_amount=0;  $tot_embl_amount_up=0;$tot_balance_cost=0;
					//$po_qty=0;
					$nameArray=sql_select( $sql_booking_cu );
					$po_id=array();
					foreach($nameArray as $forpoid){
						$po_id[$forpoid[csf('po_break_down_id')]]=$forpoid[csf('po_break_down_id')];
						
					}
					$po_id=implode(",",$po_id);
					$condition= new condition();
					//$emblishment= new emblishment($po_id,'po');
					 if(str_replace("'","",$po_id) !=''){
					  $condition->po_id("in ($po_id)");
				   	}
					$condition->init();
				  	$emblishment= new emblishment($condition);
				   //echo $emblishment->getQuery(); //die;
					$precostembamount_arr=$emblishment->getAmountArray_by_orderEmbnameAndEmbtype();
					//print_r($precostembamount_arr);
					//$wash= new wash($po_id,'po');
					$wash= new wash($condition);
					$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();
					
					/*$emblishment= new emblishment($po_id,'po');
					$precostembamount=$emblishment->getAmountArray_by_orderEmbnameAndEmbtype();

					$wash= new wash($po_id,'po');
					$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();*/
                    
					// var_dump($precostembamount_arr);die;
					
					
                    foreach ($nameArray as $selectResult)
                    {
						$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$selectResult[csf('job_no')]."'");
						//echo $costing_per;
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
						
						//$po_qty=$job_no_qty_arr[$selectResult[csf("job_no")]];
						$po_qty=$po_qty_arr[$selectResult[csf('po_break_down_id')]]['order_quantity'];
						//$po_ship_date=$po_qty_arr[$selectResult[csf('po_break_down_id')]]['pub_shipment_date'];
						//$embl_pre_amount=(($pre_cost_data_arr[$selectResult[csf("job_no")]][$selectResult[csf("emb_name")]]['amount']/$costing_per_qty)*$po_qty);
						//$pre_rate=$pre_cost_data_arr[$selectResult[csf("job_no")]][$selectResult[csf("emb_name")]]['rate'];
						//$pre_req_qnty=def_number_format($pre_cost*($po_qty/$costing_per_qty),5,"");
						$pre_amount2=(($pre_cost_data_arr[$selectResult[csf("job_no")]][$selectResult[csf("emb_name")]]['amount']/$costing_per_qty)*$po_qty);
						//$pre_amount2=($pre_cost_data_arr[$selectResult[csf("job_no")]][$selectResult[csf("emb_name")]]['amount']);
						//$pre_amount=$pre_amount2;
						
						$booking_data=array_unique(explode(",",$embl_booking_data[$selectResult[csf('po_break_down_id')]][$selectResult[csf('pre_cost_fabric_cost_dtls_id')]]['booking_no']));
						//print_r($booking_data);
						$booking_amount=0;
						$exchaned_rate=0;

						foreach($booking_data as $book_no)
						{
							if($book_no!=str_replace("'","",$txt_booking_no))
							{
								$booking_amount=$embl_booking_array[$book_no][$selectResult[csf('pre_cost_fabric_cost_dtls_id')]][$selectResult[csf('po_break_down_id')]]['amount'];
								$exchaned_rate=$embl_booking_array[$book_no][$selectResult[csf('pre_cost_fabric_cost_dtls_id')]][$selectResult[csf('po_break_down_id')]]['exchange_rate'];
							}
						} //echo $booking_amount;
						
						if(str_replace("'","",$cbo_currency)==2)
						{
							$embl_pre_amount=$selectResult[csf("amount")]+$booking_amount;
						}
						else
						{
						 	$embl_pre_amount=($selectResult[csf("amount")]/$txt_exchange_rate)+($booking_amount/$exchaned_rate);
						}
	   ?>
                    <tr>
                    <td width="40"><? echo $i;?></td>
                    <td width="200"><? echo $emblishment_name_array[$selectResult[csf('emb_name')]];?></td>
                    <td width="100">
					<? 
						if($selectResult[csf('emb_name')]==1)
						{
							$emb_type=$emblishment_print_type[$selectResult[csf('emb_type')]];
						}
						if($selectResult[csf('emb_name')]==2)
						{
							$emb_type=$emblishment_embroy_type[$selectResult[csf('emb_type')]];
						}
						if($selectResult[csf('emb_name')]==3)
						{
							$emb_type=$emblishment_wash_type[$selectResult[csf('emb_type')]];
						}
						if($selectResult[csf('emb_name')]==4)
						{
							$emb_type=$emblishment_spwork_type[$selectResult[csf('emb_type')]];
						}
						echo $emb_type;
					
					//echo $emblishment_print_type[$selectResult[csf('emb_type')]];
					?> 
                    </td>
                    <td width="200"><? echo $po_number_arr[$selectResult[csf('po_break_down_id')]];?></td>
                    <td width="80" align="right">
                     <? 
					 if($selectResult[csf('emb_name')]==3)
					 {
					 $wash_cost=$emblishment_costing_arr_name_wash[$selectResult[csf('po_break_down_id')]][3];
					 }
					 	$pre_amount = $precostembamount_arr[$selectResult[csf('po_break_down_id')]][$selectResult[csf('emb_name')]][$selectResult[csf('emb_type')]]+$wash_cost;
						echo number_format($pre_amount,2); 
					 ?>
					
                    </td>
                     <td width="80" align="right">
                    <? echo number_format($embl_pre_amount,2); ?>
                    </td>
                  
                    <td width="80" align="right">
                       <? 
					   $embl_amount=number_format($embl_pre_amount,2);
					   $precost_amount=number_format($pre_amount,2);
					   $tot_balance=$pre_amount-$embl_pre_amount;echo number_format($tot_balance,2); ?>
                    </td>
                    <td width="" title="<? echo $pre_amount.'='.$embl_pre_amount; ?>" >
                    <? 
					//echo $pre_amount.'='.$embl_pre_amount;
					if( $pre_amount>$embl_pre_amount)
						{
						echo "Less Booking";
						}
					else if ($pre_amount<$embl_pre_amount) 
						{
						echo "Over Booking";
						} 
					else if ($pre_amount==$embl_pre_amount) 
						{
							echo "As Per";
						} 
					else
						{
						echo "";
						}
						?>
                    </td>
                    </tr>
	   <?
				$tot_pre_amount+=$pre_amount;
			   $tot_embl_amount+=$embl_pre_amount; 
			   //$tot_embl_amount_up+=$embl_pre_amount;
				$tot_balance_cost+=$tot_balance;
			   $i++;
					}
       ?>
	</tbody>
     <tfoot>
            <tr>
                <td colspan="3" align="right">  <b>Total</b></td>
                <td align="right"> <b></b></td>
                <td  align="right"><b><? echo number_format($tot_pre_amount,2); ?> </b></td>
                 <td align="right"> <b>  <? echo number_format($tot_embl_amount,2); ?></b>  </td>
                 <td align="right"><b><? echo number_format($tot_balance_cost,2); ?></b> </td>
             </tr>
        </tfoot>
    </table>
    <?
  }
  
	$lib_designation_arr=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");
	$user_lib_designation_arr=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
	$user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	
	$mst_id=return_field_value("id as mst_id","wo_booking_mst","booking_no=$txt_booking_no","mst_id");
	//echo $mst_id.'ssD';
	//and b.un_approved_date is null
	$approve_data_array=sql_select("select b.approved_by,min(b.approved_date) as approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=32  group by  b.approved_by order by b.sequence_no asc");
	
	$unapprove_data_array=sql_select("select b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date,b.approved_no from   approval_history b where b.mst_id=$mst_id and b.entry_form=32 order by b.approved_date,b.approved_by");

    if(count($approve_data_array)>0)
	{
 		?>
       <table  width="850" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
                <tr style="border:1px solid black;">
                    <th colspan="5" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                    <th width="3%" style="border:1px solid black;">Sl</th>
                    <th width="40%" style="border:1px solid black;">Name</th>
                    <th width="30%" style="border:1px solid black;">Designation</th>
                    <th width="27%" style="border:1px solid black;">Approval Date</th>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($approve_data_array as $row)
			{
				?>
				<tr style="border:1px solid black;">
					<td width="3%" style="border:1px solid black;"><? echo $i;?></td>
					<td width="40%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
					<td width="30%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
					<td width="27%" style="border:1px solid black;text-align:center"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')]));?></td>
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
		<br>
		<?
		if(count($unapprove_data_array)>0)
		{/*
			$sql_unapproved=sql_select("select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form=7 and approval_type=2 and is_deleted=0 and status_active=1 and booking_id=$mst_id");
			$unapproved_request_arr=array();
			foreach($sql_unapproved as $rowu)
			{
				$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
			}*/
 		?>
       <table  width="850" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="5" style="border:1px solid black;">Approval/Un Approval History</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
				<th width="30%" style="border:1px solid black;">Name</th>
				<th width="20%" style="border:1px solid black;">Designation</th>
				<th width="5%" style="border:1px solid black;">Approval Status</th>
				<th width="22%" style="border:1px solid black;"> Date</th>

                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($unapprove_data_array as $row){

			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="5%" style="border:1px solid black; text-align:center"><? echo 'Yes';?></td>
				<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); else echo "";?></td>
            </tr>
                <?
				$i++;
				$un_approved_date= explode(" ",$row[csf('un_approved_date')]);
				$un_approved_date=$un_approved_date[0];
				if($db_type==0) //Mysql
				{
					if($un_approved_date=="" || $un_approved_date=="0000-00-00") $un_approved_date="";else $un_approved_date=$un_approved_date;
				}
				else
				{
					if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;
				}

				if($un_approved_date!="")
				{
					?>
                    <tr style="border:1px solid black;">
                        <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
                        <td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
                        <td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
                        <td width="5%" style="border:1px solid black;text-align:center;"><? echo 'No';?></td>
                        <td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('un_approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('un_approved_date')])); else echo "";?></td>
                    </tr>
					<?
                    $i++;
				}
			}
				?>
            </tbody>
        </table>
		<?
		}
		?>
   		<br>
		 <?
		 $print_id=str_replace("'","",$print_id);
		if($print_id!=6)
		{
            echo signature_table(57, $cbo_company_name, "1100px");
			echo "****".custom_file_name($txt_booking_no,$style_ref_no[0][csf('style_ref_no')],$txt_job_no);
		}
         ?>
 </div>

</div>	

 	<script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $varcode_work_order_no; ?>','barcode_img_id');
    </script>
	
<? 
exit(); 
}

if($action=="show_trim_booking_report1")
{
	//echo "fgfgfffgf";die;
 extract($_REQUEST);
//$data=explode('*',$data);
//$txt_booking_no=$data[0];
$txt_booking_no=str_replace("'","",$txt_booking_no);
$cbo_company_name=str_replace("'","",$cbo_company_name);
$cbo_supplier_name=str_replace("'","",$cbo_supplier_name);
//echo $cbo_supplier_name;die;
$txtJob_no=$txt_job_no;
$txt_job_no=str_replace("'","",$txt_job_no);
$txt_booking_date=str_replace("'","",$txt_booking_date);
$txt_delivery_date=str_replace("'","",$txt_delivery_date);
$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
//$txt_booking_no=str_replace("'","",$txt_booking_no);
$cbo_currency=str_replace("'","",$cbo_currency);
$cbo_pay_mode=str_replace("'","",$cbo_pay_mode);
$cbo_source=str_replace("'","",$cbo_source);
$txt_exchange_rate=str_replace("'","",$txt_exchange_rate);
if($txt_exchange_rate=='' || $txt_exchange_rate==0)
{
	$txt_exchange_rate=1;
}

$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_name= return_library_array("select id, short_name from  lib_buyer","id","short_name");
$supplier_name=return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
$color_id=return_library_array("select id,color_name from lib_color", "id", "color_name");
$order_arr = return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
$job_no_qty_arr = return_library_array( "select job_no, job_quantity from wo_po_details_master",'job_no','job_quantity');
?>
<div style="width:1100px;" align="center">

  <table width="100%" cellpadding="0" cellspacing="0">
           <tr>
               <td width="100"> 
               <?
			$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$txt_job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
			foreach($data_array as $row)
			{?>
				<img src='../../<? echo $row[csf('image_location')]; ?>' height='65' width='90' align="middle" />
			<? 
			}?>
               </td>
  <td width="1100">    
 <table width="100%" cellpadding="0" cellspacing="0"  >
        <tr>
            <td colspan="6" align="center" style="font-size:28px"><strong><? echo $company_library[$cbo_company_name]; ?></strong></td>
        </tr>
        <tr class="form_caption">
            <td colspan="6" align="center">
				<? 		
					
					$style_ref_no=sql_select("SELECT c.id, d.style_ref_no FROM wo_po_break_down c,wo_po_details_master d WHERE d.job_no=c.job_no_mst and d.job_no='$txt_job_no' and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by c.id,d.style_ref_no");
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
					$nameArray_sup=sql_select( "select address_1 from lib_supplier where id=$cbo_supplier_name");
                ?> 
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:20px"><strong>Embellishment Work Order</strong></td>
        </tr>
         <tr>
            <td colspan="6" align="center" style="font-size:20">&nbsp;</td>
        </tr>
         <tr>
            <td style="font-size:20" width="130" >Supplier Name </td> <td style="font-size:20"  width="170">: &nbsp;<? echo $supplier_name[$cbo_supplier_name]; ?></td>
            <td style="font-size:20">Work Order No</td> <td style="font-size:20">: &nbsp;<? echo $txt_booking_no; //change_date_format($txt_booking_date); ?></td>
            <td style="font-size:20">Work Order Date  </td> <td style="font-size:20">: &nbsp;<? echo change_date_format($txt_booking_date); ?></td>
        </tr>
         <tr>
            <td style="font-size:20">Supplier Address  </td> <td style="font-size:20">: &nbsp;<? echo $nameArray_sup[0][csf('address_1')]; ?></td>
            <td style="font-size:20"> Job No. </td> <td style="font-size:20">: &nbsp;<? echo $txt_job_no; ?></td>
            <td style="font-size:20">Delivery Date </td> <td style="font-size:20">: &nbsp;<? echo change_date_format($txt_delivery_date); ?></td>
        </tr>
        <tr>
            <td style="font-size:20"> Buyer  </td> <td style="font-size:20">: &nbsp;<? echo $buyer_name[$cbo_buyer_name]; ?></td>
            <td style="font-size:20">Currency </td> <td style="font-size:20">: &nbsp;<? echo $currency[$cbo_currency]; ?></td>
            <td style="font-size:20">Exchange Rate  </td> <td style="font-size:20">: &nbsp;<? echo $txt_exchange_rate; ?></td>
        </tr>
        <tr>
            <td style="font-size:20">Style Ref.  </td> <td style="font-size:20">: &nbsp;<? echo $style_ref_no[0][csf('style_ref_no')]; ?></td>
            <td style="font-size:20"> Pay mode </td> <td style="font-size:20">: &nbsp;<? echo $pay_mode[$cbo_pay_mode]; ?></td>
            <td style="font-size:20">Source </td>  <td style="font-size:20">: &nbsp;<? echo $source[$cbo_source]; ?></td> 
        </tr>
          <tr>
          <td style="font-size:20">Article No</td> 
          <td style="font-size:20">: &nbsp;
		  <? 
		  $txt_attention=return_field_value("attention"," wo_booking_mst","booking_no='$txt_booking_no'");
		  $nameArray_article_number=sql_select( "select article_number from  wo_po_color_size_breakdown where job_no_mst='$txt_job_no' and article_number!=0 and is_deleted=0 and status_active=1");
		  echo $nameArray_article_number[0][csf('article_number')]; 
		  ?>
          </td>
          
          <td style="font-size:20"> Attention</td> 
            <td colspan="3"> : &nbsp;
			<?
			echo  $txt_attention;
			 ?>
          
           </td>
        </tr>
        
 </table>
 </td>
 </tr>
 </table>
 <br/>
 <div style="width:100%;">
  <?
 $costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='$txt_job_no'");
	if($costing_per==1)
	{
		$costing_per_dzn="1 Dzn";
	}
	else if($costing_per==2)
	{
		$costing_per_dzn="1 Pcs";
	}
	else if($costing_per==3)
	{
		$costing_per_dzn="2 Dzn";
	}
	else if($costing_per==4)
	{
		$costing_per_dzn="3 Dzn";
	}
	else if($costing_per==5)
	{
		$costing_per_dzn="4 Dzn";
	}
 ?>
 <table width="1100" cellspacing="0" align="center" border="1" rules="all" class="rpt_table" >
      <thead bgcolor="#dddddd" align="center">
         <th width="30">SL</th>
         <th width="100">Order No.</th>
         <th width="80">Emb. Name</th>
         <th width="80">Emb. type</th>
         <th width="150">Description</th>
         <th width="80">Color</th>
         <th width="80">Order Qty (PCS)</th>
        
         <th width="80">WOQ/DZN</th>
         <th width="70">Rate/DZN</th>
         <th width="70">Amount/DZN</th>
      </thead>
      <?
	   			  
				  //f.id as bookingdtlsid,
				  $sql="select
					b.costing_per,c.id as wo_pre_cost_embe_cost_dtls,c.job_no,c.emb_name,c.emb_type,c.cons_dzn_gmts,c.rate,d.id as po_id,d.po_number,d.plan_cut,e.item_number_id,e.color_number_id,sum(e.order_quantity) as order_quantity,f.wo_qnty as wo_qnty,f.rate as frate,f.amount as famount,f.description 
					from wo_po_details_master a, wo_pre_cost_mst b,wo_pre_cost_embe_cost_dtls c,wo_po_break_down d,wo_po_color_size_breakdown e,wo_booking_dtls f 
					where a.job_no=b.job_no and a.job_no=c.job_no and a.job_no=d.job_no_mst  and a.job_no=e.job_no_mst  and a.job_no=f.job_no  and c.id=f.pre_cost_fabric_cost_dtls_id  and d.id=e.po_break_down_id and d.id=f.po_break_down_id and f.gmt_item=e.item_number_id and f.booking_no='$txt_booking_no' and a.is_deleted=0 and  a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1  and f.is_deleted=0 and f.status_active=1  and f.wo_qnty>0 
					group by  f.wo_qnty,f.rate,f.amount,e.item_number_id,e.color_number_id,d.id,d.po_number,d.plan_cut,c.id,c.job_no,c.emb_name,c.emb_type,c.cons_dzn_gmts,c.rate,b.costing_per,f.description order by d.id";
					$order_id_array=array();
					$i=1;
					$k=1;
                    $nameArray=sql_select( $sql );
					foreach ($nameArray as $selectResult)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
							
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
						}*/
						
						
						$rate=$selectResult[csf('frate')];
						$description=$selectResult[csf('description')];
						$rate=def_number_format($rate,5,"");
						$pre_cost_rate=$selectResult[csf('rate')];
						$pre_cost_rate=def_number_format($pre_cost_rate,5,"");
						$amount=$selectResult[csf('famount')];
						$amount=def_number_format($amount,5,"");
						if (!in_array($selectResult[csf('po_id')],$order_id_array))
						{  
							if($k!=1)
							{
							
								?>
								<tr>
                                    <td colspan="7" align="right"><b>Sub Total</b></td> 
                                    <td   align="right"><b><? echo number_format($wo_qnty,2);//]$emblishment_name_array[; ?></b> </td> 
                                    <td  align="right">&nbsp;</td>
                                    <td  align="right"><b><? echo number_format($amount_tot,2);?> </b></td>
								</tr>
								<?		
							
							}
							
							unset($wo_qnty);
							unset($amount_tot);
							$k++;
							$order_id_array[]=$selectResult[csf('po_id')];	
						}
						
						?>
						<tr <? echo $bgcolor; ?>>
                            <td width="30"><? echo $i; ?> </td> 
                            <td width="100"><p><? echo $order_arr[$selectResult[csf('po_id')]];?></p> </td> 
                            <td  width="80" align="center"><p><? echo $emblishment_name_array[$selectResult[csf('emb_name')]]; ?> </p></td> 
                            <td  width="80" align="center">
                            <p>
							<? 
							 
							if($selectResult[csf('emb_name')]==1)
							{
								$emb_type=$emblishment_print_type[$selectResult[csf('emb_type')]];
							}
							if($selectResult[csf('emb_name')]==2)
							{
								$emb_type=$emblishment_embroy_type[$selectResult[csf('emb_type')]];
							}
							if($selectResult[csf('emb_name')]==3)
							{
								$emb_type=$emblishment_wash_type[$selectResult[csf('emb_type')]];
							}
							if($selectResult[csf('emb_name')]==4)
							{
								$emb_type=$emblishment_spwork_type[$selectResult[csf('emb_type')]];
							}
							echo $emb_type;
							
							
							//echo $emblishment_print_type[$selectResult[csf('emb_type')]];
							?> 
                            </p>
                            </td>
                            <td  width="150"><div style="word-break:break-all; width:98px"><? echo $description; ?> &nbsp; </div> </td>
                            <td width="80"><p><? echo $color_id[$selectResult[csf('color_number_id')]]; ?> </p></td> 
                            <td width="80" align="right"><p><? echo $selectResult[csf('order_quantity')]; ?> </p></td> 
                            
                            <td width="80" align="right"><p><? $w_qty=$selectResult[csf('wo_qnty')]; echo number_format($w_qty,2); ?></p></td>
                            <td width="70" align="right"><p> <? echo number_format($rate,2); ?> </p></td> 
                            <td width="70" align="right"><p> <? echo number_format($amount,2); ?> </p></td>
						</tr>
						
						<?
						$i++;
						$wo_qnty+=$w_qty;
						$amount_tot+=$amount;
						
						$wo_qnty_r+=$w_qty;
						$amount_tot_t+=$amount;
						//end foreach	
					}
 ?> 
                      <tr>
                          <td  align="right" colspan="7"><b>Sub Total</b></td> 
                          <td  align="right"><b><? echo number_format($wo_qnty,2);//]$emblishment_name_array[; ?> </b></td> 
                          <td  align="right">&nbsp;</td>
                          <td  align="right"><b><? echo number_format($amount_tot,2);?></b> </td>
                      </tr>
                      <tr>
                          <td  align="right" colspan="7"><strong>Grand Total</strong></td> 
                          <td  align="right"><b><? echo number_format($wo_qnty_r,2);//]$emblishment_name_array[; ?> </b></td> 
                          <td  align="right">&nbsp;</td>
                          <td  align="right"><b><? echo number_format($amount_tot_t,2);?> </b></td>
                      </tr>
 </table>
 <br> 
    
 					<?
						echo get_spacial_instruction($txt_booking_no);
					?>
    <br> <br>
    <?
  if(str_replace("'","",$show_comment)==1)
  {
	?>
  <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table" >
                <tr> <td colspan="8" align="center"> <b>Comments</b></td></tr>
                <tr>
                    <th width="40">SL</th>
                    <th width="200">Emble Name</th> 
                    <th width="100">Booking Type</th>
                    <th width="200">PO No</th>
                    <th width="80">Pre-Cost/Budget Value</th>
                    <th width="80">WO Value</th>
                     <th width="80">Balance</th>
                    <th width="">Comments </th>
                </tr>
       <tbody>
       <?
					
					$sql_po_qty=sql_select("select b.id as po_id,sum(b.plan_cut) as order_quantity,(sum(b.po_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.is_deleted=0  and a.status_active=1 group by b.id,a.total_set_qnty");
					foreach( $sql_po_qty as $row)
					{
						$po_qty_arr[$row[csf("po_id")]]['order_quantity']=$row[csf("order_quantity")];
						//$po_qty_arr[$row[csf("po_id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
						
					}
					
					$sql_cons_data=sql_select("select a.job_no,a.emb_name,a.amount,a.rate as rate from wo_pre_cost_embe_cost_dtls a  where  a.is_deleted=0  and a.status_active=1");
									 
					foreach($sql_cons_data as $row)
					{
						$pre_cost_data_arr[$row[csf("job_no")]][$row[csf("emb_name")]]['amount']=$row[csf("amount")];
						$pre_cost_data_arr[$row[csf("job_no")]][$row[csf("emb_name")]]['rate']=$row[csf("rate")];
					}
					// Sum PO Comulative//Aziz
					$embl_booking_array=array();$embl_booking_data=array();
					  $sql_wo=sql_select("select b.po_break_down_id as po_id,b.booking_no,a.exchange_rate,b.pre_cost_fabric_cost_dtls_id as fab_dtls_id,sum(b.amount) as amount  from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.booking_type=6 and b.is_short=2 and 
					b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by  b.po_break_down_id, b.booking_no,b.pre_cost_fabric_cost_dtls_id,a.exchange_rate");
						foreach($sql_wo as $row)
						{ //pre_cost_fabric_cost_dtls_id
							$embl_booking_array[$row[csf('booking_no')]][$row[csf('fab_dtls_id')]][$row[csf('po_id')]]['amount']=$row[csf('amount')];
							$embl_booking_array[$row[csf('booking_no')]][$row[csf('fab_dtls_id')]][$row[csf('po_id')]]['exchange_rate']=$row[csf('exchange_rate')];
						}
						
						if($db_type==0) $group_concat="group_concat( distinct booking_no,',') AS booking_no";
					else if($db_type==2)  $group_concat="listagg(cast(booking_no as varchar2(4000)),',') within group (order by booking_no) AS booking_no";
					
					
					$wo_book=sql_select("select po_break_down_id,pre_cost_fabric_cost_dtls_id as fab_dtls_id,$group_concat,sum(amount) as amount  from wo_booking_dtls where 
					 booking_type=6 and is_short=2 and status_active=1 and is_deleted=0 group by po_break_down_id,pre_cost_fabric_cost_dtls_id");
					foreach($wo_book as $row)
						{ //pre_cost_fabric_cost_dtls_id
							$embl_booking_data[$row[csf('po_break_down_id')]][$row[csf('fab_dtls_id')]]['booking_no']=$row[csf('booking_no')];
						}
					
					$sql_booking_cu=( "select b.po_break_down_id,a.job_no,b.pre_cost_fabric_cost_dtls_id,sum(b.amount) as amount,c.emb_name,c.emb_type from wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_embe_cost_dtls c where a.job_no=b.job_no and  a.job_no=c.job_no and a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.booking_no='$txt_booking_no' and a.booking_type=6 and a.is_short=2 and  b.amount>0 and  a.status_active=1  and 	a.is_deleted=0 and b.status_active=1  and 	b.is_deleted=0  group by b.po_break_down_id,a.job_no,b.pre_cost_fabric_cost_dtls_id,c.emb_name,c.emb_type  order by b.po_break_down_id");
					 
					// $sql="select job_no,po_break_down_id,pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls where booking_no='$txt_booking_no' and status_active=1 and is_deleted=0 group by job_no,po_break_down_id,pre_cost_fabric_cost_dtls_id,trim_group";
					$exchange_rate=return_field_value("exchange_rate", " wo_booking_mst", "booking_no='".$txt_booking_no."'");
					
					
					
					
					$i=1;
					$tot_pre_cost=0;$tot_pre_cost2=0;
					$tot_embl_amount=0;
					$tot_embl_pre_amount_up=0;
					$tot_balance_cost=0;
                    $nameArray=sql_select( $sql_booking_cu );
					$po_id=array();
					foreach($nameArray as $forpoid){
						$po_id[$forpoid[csf('po_break_down_id')]]=$forpoid[csf('po_break_down_id')];
						
					}
					$po_id=implode(",",$po_id);
					$condition= new condition();
					$condition->init();
					//$emblishment= new emblishment($po_id,'po');
					 if(str_replace("'","",$po_id) !=''){
					  $condition->po_id("in ($po_id)");
				   	}
					/* if($txtJob_no !=''){
					  $condition->job_no("in ($txtJob_no)");
				   	}*/
					$emblishment= new emblishment($condition);
					$wash= new wash($condition);
				    //echo $emblishment->getQuery(); //die;
					$precostembamount_arr=$emblishment->getAmountArray_by_orderEmbnameAndEmbtype();
					//$wash= new wash($po_id,'po');
					$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();
					
					/*$condition= new condition();
					if(str_replace("'","",$txt_job_no) !=''){
						$condition->job_no("='$txt_job_no'");
					}
					$condition->init();
					$precostembamount=$emblishment->getAmountArray_by_orderEmbnameAndEmbtype();
					$wash= new wash($condition);
					$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();*/
					
					$pre_amount_data="";
                    foreach ($nameArray as $selectResult)
                    {
						$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$selectResult[csf('job_no')]."'");
						//echo $costing_per;
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

						//$po_qty=$job_no_qty_arr[$selectResult[csf("job_no")]];
						$po_qty=$po_qty_arr[$selectResult[csf('po_break_down_id')]]['order_quantity'];
						//$pre_amount2=(($pre_cost_data_arr[$selectResult[csf("job_no")]][$selectResult[csf("emb_name")]]['amount']/$costing_per_qty)*$po_qty);
						$pre_amount_data2=(($pre_cost_data_arr[$selectResult[csf("job_no")]][$selectResult[csf("emb_name")]]['amount']/$costing_per_qty)*$po_qty);
						//$pre_rate=$pre_cost_data_arr[$selectResult[csf("job_no")]][$selectResult[csf("emb_name")]]['rate'];
						//$pre_req_qnty=def_number_format($pre_cost*($po_qty/$costing_per_qty),5,"");
						//$pre_amount=$pre_amount2;
						//$pre_amount_data=$pre_amount_data2;
						$booking_data=array_unique(explode(",",$embl_booking_data[$selectResult[csf('po_break_down_id')]][$selectResult[csf('pre_cost_fabric_cost_dtls_id')]]['booking_no']));
						//print_r($booking_data);
						$booking_amount=0;
						$exchaned_rate=0;
						

						foreach($booking_data as $book_no)
						{
							if($book_no!=str_replace("'","",$txt_booking_no))
							{
							$booking_amount=$embl_booking_array[$book_no][$selectResult[csf('pre_cost_fabric_cost_dtls_id')]][$selectResult[csf('po_break_down_id')]]['amount'];
							$exchaned_rate=$embl_booking_array[$book_no][$selectResult[csf('pre_cost_fabric_cost_dtls_id')]][$selectResult[csf('po_break_down_id')]]['exchange_rate'];
							}
						} //echo $booking_amount;
						
						if($cbo_currency==2)
						{
						  $embl_pre_amount=$selectResult[csf("amount")]+$booking_amount;
						}
						else
						{
						  $embl_pre_amount=($selectResult[csf("amount")]/$txt_exchange_rate)+($booking_amount/$exchaned_rate);
						}
						//echo $cbo_currency.'=='.$selectResult[csf("amount")];
	   ?>
                    <tr>
                    <td width="40"><? echo $i;?></td>
                    <td width="200">
					<? echo $emblishment_name_array[$selectResult[csf('emb_name')]];?> 
                    </td>
                    <td width="100">
					<? 
					if($selectResult[csf('emb_name')]==1)
					{
						$emb_type=$emblishment_print_type[$selectResult[csf('emb_type')]];
					}
					if($selectResult[csf('emb_name')]==2)
					{
						$emb_type=$emblishment_embroy_type[$selectResult[csf('emb_type')]];
					}
					if($selectResult[csf('emb_name')]==3)
					{
						$emb_type=$emblishment_wash_type[$selectResult[csf('emb_type')]];
					}
					if($selectResult[csf('emb_name')]==4)
					{
						$emb_type=$emblishment_spwork_type[$selectResult[csf('emb_type')]];
					}
					echo $emb_type;
					
					//echo $emblishment_print_type[$selectResult[csf('emb_type')]];?> 
                    </td>
                    <td width="200">
					<? echo $po_number_arr[$selectResult[csf('po_break_down_id')]];?> 
                    </td>
                    <td width="80" align="left">
                     <? 
					 if($selectResult[csf('emb_name')]==3)
					 {
					 $wash_cost=$emblishment_costing_arr_name_wash[$selectResult[csf('po_break_down_id')]][3];
					 }
					  //echo number_format($precostembamount_arr[$selectResult[csf('po_break_down_id')]][$selectResult[csf('emb_name')]][$selectResult[csf('emb_type')]]+$wash_cost,2);
						$pre_amount_data= $precostembamount_arr[$selectResult[csf('po_break_down_id')]][$selectResult[csf('emb_name')]][$selectResult[csf('emb_type')]]+$wash_cost;
						echo number_format($pre_amount_data,2); 
						
					 ?>
                    </td>
                     <td width="80" align="right">
                    <? echo number_format($embl_pre_amount,2); ?>
                    </td>
                   
                    <td width="80" align="right">
                       <? $pre_amount=number_format($pre_amount_data,2);
					   $embl_amount=number_format($embl_pre_amount,2);
					   $tot_balance=$pre_amount_data-$embl_pre_amount;echo number_format($tot_balance,2); ?>
                    </td>
                    <td width="">
                    <? 
					if( $pre_amount_data>$embl_pre_amount)
						{
						echo "Less Booking";
						}
					else if ($pre_amount_data<$embl_pre_amount) 
						{
						echo "Over Booking";
						} 
					else if ($pre_amount_data==$embl_pre_amount) 
						{
							echo "As Per";
						} 
					else
						{
						echo "";
						}
						?>
                    </td>
                    </tr>
	   <?
	     $tot_pre_cost2+=$pre_amount_data;
	  	 $tot_embl_amount+=$embl_pre_amount;
		//$tot_embl_pre_amount_up+=$embl_pre_amount;
		$tot_balance_cost+=$tot_balance;
	   $i++;
					}
       ?>
	</tbody>
    	<tfoot>
            <tr>
                <td colspan="3" align="right"><b>Total</b></td>
                <td align="right"> <b></b></td>
                <td  align="right"><b><? echo number_format($tot_pre_cost2,2); ?></b></td>
                 
                 <td align="right"> <b>  <? echo number_format($tot_embl_amount,2); ?> </b>  </td>
                 <td align="right"><b> <? echo number_format(abs($tot_balance_cost),2); ?></b> </td>
             </tr>
        </tfoot>
    </table>
    <?
  }
	?>
   <br>
		 <?
            echo signature_table(57, $cbo_company_name, "1100px");
			echo "****".custom_file_name($txt_booking_no,$style_ref_no[0][csf('style_ref_no')],$txt_job_no);
         ?>
 </div>

</div>	
	 
<? 
exit();
}

//Without Order Qty
if($action=="show_trim_booking_report2")
{
	//echo "fgfgfffgf";die;
 extract($_REQUEST);
//$data=explode('*',$data);
//$txt_booking_no=$data[0];
$txt_booking_no=str_replace("'","",$txt_booking_no);
$cbo_company_name=str_replace("'","",$cbo_company_name);
$cbo_supplier_name=str_replace("'","",$cbo_supplier_name);
//echo $cbo_supplier_name;die;
$txt_job_no=str_replace("'","",$txt_job_no);
$txt_booking_date=str_replace("'","",$txt_booking_date);
$txt_delivery_date=str_replace("'","",$txt_delivery_date);
$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
//$txt_booking_no=str_replace("'","",$txt_booking_no);
$cbo_currency=str_replace("'","",$cbo_currency);
$cbo_pay_mode=str_replace("'","",$cbo_pay_mode);
$cbo_source=str_replace("'","",$cbo_source);
$txt_exchange_rate=str_replace("'","",$txt_exchange_rate);
if($txt_exchange_rate=='' || $txt_exchange_rate==0)
{
	$txt_exchange_rate=1;
}

$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_name= return_library_array("select id, short_name from  lib_buyer","id","short_name");
$supplier_name=return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
$color_id=return_library_array("select id,color_name from lib_color", "id", "color_name");
$order_arr = return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
$job_no_qty_arr = return_library_array( "select job_no, job_quantity from wo_po_details_master",'job_no','job_quantity');
?>
<div style="width:1100px;" align="center">

  <table width="100%" cellpadding="0" cellspacing="0">
           <tr>
               <td width="100"> 
               <?
			$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$txt_job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
			foreach($data_array as $row)
			{?>
				<img src='../../<? echo $row[csf('image_location')]; ?>' height='65' width='90' align="middle" />
			<? 
			}?>
               </td>
  <td width="1100">    
 <table width="100%" cellpadding="0" cellspacing="0"  >
        <tr>
            <td colspan="6" align="center" style="font-size:28px"><strong><? echo $company_library[$cbo_company_name]; ?></strong></td>
        </tr>
        <tr class="form_caption">
            <td colspan="6" align="center">
				<? 		
					
					$style_ref_no=sql_select("SELECT c.id, d.style_ref_no FROM wo_po_break_down c,wo_po_details_master d WHERE d.job_no=c.job_no_mst and d.job_no='$txt_job_no' and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by c.id,d.style_ref_no");
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
					$nameArray_sup=sql_select( "select address_1 from lib_supplier where id=$cbo_supplier_name");
                ?> 
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:20px"><strong>Embellishment Work Order</strong></td>
        </tr>
         <tr>
            <td colspan="6" align="center" style="font-size:20">&nbsp;</td>
        </tr>
         <tr>
            <td style="font-size:20" width="130" >Supplier Name </td> <td style="font-size:20"  width="170">: &nbsp;<? echo $supplier_name[$cbo_supplier_name]; ?></td>
            <td style="font-size:20">Work Order No</td> <td style="font-size:20">: &nbsp;<? echo $txt_booking_no; //change_date_format($txt_booking_date); ?></td>
            <td style="font-size:20">Work Order Date  </td> <td style="font-size:20">: &nbsp;<? echo change_date_format($txt_booking_date); ?></td>
        </tr>
         <tr>
            <td style="font-size:20">Supplier Address  </td> <td style="font-size:20">: &nbsp;<? echo $nameArray_sup[0][csf('address_1')]; ?></td>
            <td style="font-size:20"> Job No. </td> <td style="font-size:20">: &nbsp;<? echo $txt_job_no; ?></td>
            <td style="font-size:20">Delivery Date </td> <td style="font-size:20">: &nbsp;<? echo change_date_format($txt_delivery_date); ?></td>
        </tr>
        <tr>
            <td style="font-size:20"> Buyer  </td> <td style="font-size:20">: &nbsp;<? echo $buyer_name[$cbo_buyer_name]; ?></td>
            <td style="font-size:20">Currency </td> <td style="font-size:20">: &nbsp;<? echo $currency[$cbo_currency]; ?></td>
            <td style="font-size:20">Exchange Rate  </td> <td style="font-size:20">: &nbsp;<? echo $txt_exchange_rate; ?></td>
        </tr>
        <tr>
            <td style="font-size:20">Style Ref.  </td> <td style="font-size:20">: &nbsp;<? echo $style_ref_no[0][csf('style_ref_no')]; ?></td>
            <td style="font-size:20"> Pay mode </td> <td style="font-size:20">: &nbsp;<? echo $pay_mode[$cbo_pay_mode]; ?></td>
            <td style="font-size:20">Source </td>  <td style="font-size:20">: &nbsp;<? echo $source[$cbo_source]; ?></td> 
        </tr>
          <tr>
          <td style="font-size:20">Article No</td> 
          <td style="font-size:20">: &nbsp;
		  <? 
		  $txt_attention=return_field_value("attention"," wo_booking_mst","booking_no='$txt_booking_no'");
		  $nameArray_article_number=sql_select( "select article_number from  wo_po_color_size_breakdown where job_no_mst='$txt_job_no' and article_number!=0 and is_deleted=0 and status_active=1");
		  echo $nameArray_article_number[0][csf('article_number')]; 
		  ?>
          </td>
          
          <td style="font-size:20"> Attention</td> 
            <td colspan="3"> : &nbsp;
			<?
			echo  $txt_attention;
			 ?>
          
           </td>
        </tr>
        
 </table>
 </td>
 </tr>
 </table>
 <br/>
 <div style="width:100%;">
  <?
 $costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='$txt_job_no'");
	if($costing_per==1)
	{
		$costing_per_dzn="1 Dzn";
	}
	else if($costing_per==2)
	{
		$costing_per_dzn="1 Pcs";
	}
	else if($costing_per==3)
	{
		$costing_per_dzn="2 Dzn";
	}
	else if($costing_per==4)
	{
		$costing_per_dzn="3 Dzn";
	}
	else if($costing_per==5)
	{
		$costing_per_dzn="4 Dzn";
	}
 ?>
 <table width="1100" cellspacing="0" align="center" border="1" rules="all" class="rpt_table" >
      <thead bgcolor="#dddddd" align="center">
         <th width="30">SL</th>
         <th width="100">Order No.</th>
         <th width="80">Emb. Name</th>
         <th width="80">Emb. type</th>
         <th width="230">Description</th>
         <th width="80">Color</th>
        <!-- <th width="80">Order Qty (PCS)</th>-->
        
         <th width="80">WOQ/DZN</th>
         <th width="70">Rate/DZN</th>
         <th width="70">Amount/DZN</th>
      </thead>
      <?
	   			  
				  //f.id as bookingdtlsid,
				 $sql="select
					b.costing_per,c.id as wo_pre_cost_embe_cost_dtls,c.job_no,c.emb_name,c.emb_type,c.cons_dzn_gmts,c.rate,d.id as po_id,d.po_number,d.plan_cut,e.item_number_id,e.color_number_id,sum(e.order_quantity) as order_quantity,f.wo_qnty as wo_qnty,f.rate as frate,f.amount as famount,f.description 
					from wo_po_details_master a, wo_pre_cost_mst b,wo_pre_cost_embe_cost_dtls c,wo_po_break_down d,wo_po_color_size_breakdown e,wo_booking_dtls f 
					where a.job_no=b.job_no and a.job_no=c.job_no and a.job_no=d.job_no_mst  and a.job_no=e.job_no_mst  and a.job_no=f.job_no  and c.id=f.pre_cost_fabric_cost_dtls_id  and d.id=e.po_break_down_id and d.id=f.po_break_down_id and f.gmt_item=e.item_number_id and  f.gmts_color_id=e.color_number_id and f.booking_no='$txt_booking_no' and a.is_deleted=0 and  a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1  and f.is_deleted=0 and f.status_active=1  and f.wo_qnty>0 
					group by  f.wo_qnty,f.rate,f.amount,e.item_number_id,e.color_number_id,d.id,d.po_number,d.plan_cut,c.id,c.job_no,c.emb_name,c.emb_type,c.cons_dzn_gmts,c.rate,b.costing_per,f.description order by d.id";
					$order_id_array=array();
					$i=1;
					$k=1;
                    $nameArray=sql_select( $sql );
					foreach ($nameArray as $selectResult)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
							
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
						}*/
						
						
						$rate=$selectResult[csf('frate')];
						$description=$selectResult[csf('description')];
						$rate=def_number_format($rate,5,"");
						$pre_cost_rate=$selectResult[csf('rate')];
						$pre_cost_rate=def_number_format($pre_cost_rate,5,"");
						$amount=$selectResult[csf('famount')];
						$amount=def_number_format($amount,5,"");
						if (!in_array($selectResult[csf('po_id')],$order_id_array))
						{  
							if($k!=1)
							{
							
								?>
								<tr>
                                    <td colspan="6" align="right"><b>Sub Total</b></td> 
                                    <td   align="right"><b><? echo number_format($wo_qnty,2);//]$emblishment_name_array[; ?></b> </td> 
                                    <td  align="right">&nbsp;</td>
                                    <td  align="right"><b><? echo number_format($amount_tot,2);?> </b></td>
								</tr>
								<?		
							
							}
							
							unset($wo_qnty);
							unset($amount_tot);
							$k++;
							$order_id_array[]=$selectResult[csf('po_id')];	
						}
						
						?>
						<tr <? echo $bgcolor; ?>>
                            <td width="30"><? echo $i; ?> </td> 
                            <td width="100"><p><? echo $order_arr[$selectResult[csf('po_id')]];?></p> </td> 
                            <td  width="80" align="center"><p><? echo $emblishment_name_array[$selectResult[csf('emb_name')]]; ?> </p></td> 
                            <td  width="80" align="center">
                            <p>
							<? 
							 
							if($selectResult[csf('emb_name')]==1)
							{
								$emb_type=$emblishment_print_type[$selectResult[csf('emb_type')]];
							}
							if($selectResult[csf('emb_name')]==2)
							{
								$emb_type=$emblishment_embroy_type[$selectResult[csf('emb_type')]];
							}
							if($selectResult[csf('emb_name')]==3)
							{
								$emb_type=$emblishment_wash_type[$selectResult[csf('emb_type')]];
							}
							if($selectResult[csf('emb_name')]==4)
							{
								$emb_type=$emblishment_spwork_type[$selectResult[csf('emb_type')]];
							}
							echo $emb_type;
							
							
							//echo $emblishment_print_type[$selectResult[csf('emb_type')]];
							?> 
                            </p>
                            </td>
                            <td  width="230"><div style="word-break:break-all; width:220px"><? echo $description; ?> &nbsp; </div> </td>
                            <td width="80"><p><? echo $color_id[$selectResult[csf('color_number_id')]]; ?> </p></td> 
                           <!-- <td width="80" align="right"><p><? //echo $selectResult[csf('order_quantity')]; ?> </p></td>--> 
                            
                            <td width="80" align="right"><p><? $w_qty=$selectResult[csf('wo_qnty')]; echo number_format($w_qty,2); ?></p></td>
                            <td width="70" align="right"><p> <? echo number_format($rate,2); ?> </p></td> 
                            <td width="70" align="right"><p> <? echo number_format($amount,2); ?> </p></td>
						</tr>
						
						<?
						$i++;
						$wo_qnty+=$w_qty;
						$amount_tot+=$amount;
						
						$wo_qnty_r+=$w_qty;
						$amount_tot_t+=$amount;
						//end foreach	
					}
 ?> 
                      <tr>
                          <td  align="right" colspan="6"><b>Sub Total</b></td> 
                          <td  align="right"><b><? echo number_format($wo_qnty,2);//]$emblishment_name_array[; ?> </b></td> 
                          <td  align="right">&nbsp;</td>
                          <td  align="right"><b><? echo number_format($amount_tot,2);?></b> </td>
                      </tr>
                      <tr>
                          <td  align="right" colspan="6"><strong>Grand Total</strong></td> 
                          <td  align="right"><b><? echo number_format($wo_qnty_r,2);//]$emblishment_name_array[; ?> </b></td> 
                          <td  align="right">&nbsp;</td>
                          <td  align="right"><b><? echo number_format($amount_tot_t,2);?> </b></td>
                      </tr>
 </table>
 <br> 
    
 					<?
						echo get_spacial_instruction($txt_booking_no);
					?>
    <br> <br>
    <?
  if(str_replace("'","",$show_comment)==1)
  {
	?>
  <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table" >
                <tr> <td colspan="8" align="center"> <b>Comments</b></td></tr>
                <tr>
                    <th width="40">SL</th>
                    <th width="200">Emble Name</th> 
                    <th width="100">Booking Type</th>
                    <th width="200">PO No</th>
                    <th width="80">Pre-Cost/Budget Value</th>
                    <th width="80">WO Value</th>
                     <th width="80">Balance</th>
                    <th width="">Comments </th>
                </tr>
       <tbody>
       <?
					
					$sql_po_qty=sql_select("select b.id as po_id,sum(b.plan_cut) as order_quantity,(sum(b.po_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.is_deleted=0  and a.status_active=1 group by b.id,a.total_set_qnty");
					foreach( $sql_po_qty as $row)
					{
						$po_qty_arr[$row[csf("po_id")]]['order_quantity']=$row[csf("order_quantity")];
						//$po_qty_arr[$row[csf("po_id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
						
					}
					
					$sql_cons_data=sql_select("select a.job_no,a.emb_name,a.amount,a.rate as rate from wo_pre_cost_embe_cost_dtls a  where  a.is_deleted=0  and a.status_active=1");
									 
					foreach($sql_cons_data as $row)
					{
						$pre_cost_data_arr[$row[csf("job_no")]][$row[csf("emb_name")]]['amount']=$row[csf("amount")];
						$pre_cost_data_arr[$row[csf("job_no")]][$row[csf("emb_name")]]['rate']=$row[csf("rate")];
					}
					// Sum PO Comulative//Aziz
					$embl_booking_array=array();$embl_booking_data=array();
					  $sql_wo=sql_select("select b.po_break_down_id as po_id,b.booking_no,a.exchange_rate,b.pre_cost_fabric_cost_dtls_id as fab_dtls_id,sum(b.amount) as amount  from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.booking_type=6 and b.is_short=2 and 
					b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by  b.po_break_down_id, b.booking_no,b.pre_cost_fabric_cost_dtls_id,a.exchange_rate");
						foreach($sql_wo as $row)
						{ //pre_cost_fabric_cost_dtls_id
							$embl_booking_array[$row[csf('booking_no')]][$row[csf('fab_dtls_id')]][$row[csf('po_id')]]['amount']=$row[csf('amount')];
							$embl_booking_array[$row[csf('booking_no')]][$row[csf('fab_dtls_id')]][$row[csf('po_id')]]['exchange_rate']=$row[csf('exchange_rate')];
						}
						
						if($db_type==0) $group_concat="group_concat( distinct booking_no,',') AS booking_no";
					else if($db_type==2)  $group_concat="listagg(cast(booking_no as varchar2(4000)),',') within group (order by booking_no) AS booking_no";
					
					
					$wo_book=sql_select("select po_break_down_id,pre_cost_fabric_cost_dtls_id as fab_dtls_id,$group_concat,sum(amount) as amount  from wo_booking_dtls where 
					 booking_type=6 and is_short=2 and status_active=1 and is_deleted=0 group by po_break_down_id,pre_cost_fabric_cost_dtls_id");
					foreach($wo_book as $row)
						{ //pre_cost_fabric_cost_dtls_id
							$embl_booking_data[$row[csf('po_break_down_id')]][$row[csf('fab_dtls_id')]]['booking_no']=$row[csf('booking_no')];
						}
					
					$sql_booking_cu=( "select b.po_break_down_id,a.job_no,b.pre_cost_fabric_cost_dtls_id,sum(b.amount) as amount,c.emb_name,c.emb_type from wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_embe_cost_dtls c where a.job_no=b.job_no and  a.job_no=c.job_no and a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.booking_no='$txt_booking_no' and a.booking_type=6 and a.is_short=2 and  b.amount>0 and  a.status_active=1  and 	a.is_deleted=0 and b.status_active=1  and 	b.is_deleted=0  group by b.po_break_down_id,a.job_no,b.pre_cost_fabric_cost_dtls_id,c.emb_name,c.emb_type  order by b.po_break_down_id");
					 
					// $sql="select job_no,po_break_down_id,pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls where booking_no='$txt_booking_no' and status_active=1 and is_deleted=0 group by job_no,po_break_down_id,pre_cost_fabric_cost_dtls_id,trim_group";
					$exchange_rate=return_field_value("exchange_rate", " wo_booking_mst", "booking_no='".$txt_booking_no."'");
					
					
					
					
					$i=1;
					$tot_pre_cost=0;$tot_pre_cost2=0;
					$tot_embl_amount=0;
					$tot_embl_pre_amount_up=0;
					$tot_balance_cost=0;
                    $nameArray=sql_select( $sql_booking_cu );
					$po_id=array();
					foreach($nameArray as $forpoid){
						$po_id[$forpoid[csf('po_break_down_id')]]=$forpoid[csf('po_break_down_id')];
						
					}
					$po_id=implode(",",$po_id);
					$condition= new condition();
					$condition->init();
					//$emblishment= new emblishment($po_id,'po');
					 if(str_replace("'","",$po_id) !=''){
					  $condition->po_id("in ($po_id)");
				   	}
				  	$emblishment= new emblishment($condition);
					$wash= new wash($condition);
				   //echo $wash->getQuery(); die;
					$precostembamount_arr=$emblishment->getAmountArray_by_orderEmbnameAndEmbtype();
					//$wash= new wash($po_id,'po');
					$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();
			
                    foreach ($nameArray as $selectResult)
                    {
						$costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$selectResult[csf('job_no')]."'");
						//echo $costing_per;
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
						
						//$po_qty=$job_no_qty_arr[$selectResult[csf("job_no")]];
						$po_qty=$po_qty_arr[$selectResult[csf('po_break_down_id')]]['order_quantity'];
						//$pre_amount2=(($pre_cost_data_arr[$selectResult[csf("job_no")]][$selectResult[csf("emb_name")]]['amount']/$costing_per_qty)*$po_qty);
						$pre_amount_data2=(($pre_cost_data_arr[$selectResult[csf("job_no")]][$selectResult[csf("emb_name")]]['amount']/$costing_per_qty)*$po_qty);
						//$pre_rate=$pre_cost_data_arr[$selectResult[csf("job_no")]][$selectResult[csf("emb_name")]]['rate'];
						//$pre_req_qnty=def_number_format($pre_cost*($po_qty/$costing_per_qty),5,"");
						//$pre_amount=$pre_amount2;
						//$pre_amount_data=$pre_amount_data2;
						$booking_data=array_unique(explode(",",$embl_booking_data[$selectResult[csf('po_break_down_id')]][$selectResult[csf('pre_cost_fabric_cost_dtls_id')]]['booking_no']));
						//print_r($booking_data);
						$booking_amount=0;
						$exchaned_rate=0;
						

						foreach($booking_data as $book_no)
						{
							if($book_no!=str_replace("'","",$txt_booking_no))
							{
							$booking_amount=$embl_booking_array[$book_no][$selectResult[csf('pre_cost_fabric_cost_dtls_id')]][$selectResult[csf('po_break_down_id')]]['amount'];
							$exchaned_rate=$embl_booking_array[$book_no][$selectResult[csf('pre_cost_fabric_cost_dtls_id')]][$selectResult[csf('po_break_down_id')]]['exchange_rate'];
							}
						} //echo $booking_amount;
						
						if($cbo_currency==2)
						{
						  $embl_pre_amount=$selectResult[csf("amount")]+$booking_amount;
						}
						else
						{
						  $embl_pre_amount=($selectResult[csf("amount")]/$txt_exchange_rate)+($booking_amount/$exchaned_rate);
						}
						//echo $cbo_currency.'=='.$selectResult[csf("amount")];
	   ?>
                    <tr>
                    <td width="40"><? echo $i;?></td>
                    <td width="200">
					<? echo $emblishment_name_array[$selectResult[csf('emb_name')]];?> 
                    </td>
                    <td width="100">
					<? 
					if($selectResult[csf('emb_name')]==1)
					{
						$emb_type=$emblishment_print_type[$selectResult[csf('emb_type')]];
					}
					if($selectResult[csf('emb_name')]==2)
					{
						$emb_type=$emblishment_embroy_type[$selectResult[csf('emb_type')]];
					}
					if($selectResult[csf('emb_name')]==3)
					{
						$emb_type=$emblishment_wash_type[$selectResult[csf('emb_type')]];
					}
					if($selectResult[csf('emb_name')]==4)
					{
						$emb_type=$emblishment_spwork_type[$selectResult[csf('emb_type')]];
					}
					echo $emb_type;
					
					//echo $emblishment_print_type[$selectResult[csf('emb_type')]];?> 
                    </td>
                    <td width="200">
					<? echo $po_number_arr[$selectResult[csf('po_break_down_id')]];?> 
                    </td>
                    <td width="80" align="right">
                     <? 
					 if($selectResult[csf('emb_name')]==3)
					 {
						 $wash_cost=$emblishment_costing_arr_name_wash[$selectResult[csf('po_break_down_id')]][3];
					 }
					 
					 //echo number_format($precostembamount_arr[$selectResult[csf('po_break_down_id')]][$selectResult[csf('emb_name')]][$selectResult[csf('emb_type')]]+$wash_cost,2);
					 $pre_amount_data= $precostembamount_arr[$selectResult[csf('po_break_down_id')]][$selectResult[csf('emb_name')]][$selectResult[csf('emb_type')]]+$wash_cost;
					 echo number_format($pre_amount_data,2);
					 ?>
                    </td>
                     <td width="80" align="right">
                    <? echo number_format($embl_pre_amount,2); ?>
                    </td>
                   
                    <td width="80" align="right">
                       <? $pre_amount=number_format($pre_amount_data,2);
					   $embl_amount=number_format($embl_pre_amount,2);
					   $tot_balance=$pre_amount_data-$embl_pre_amount;echo number_format($tot_balance,2); ?>
                    </td>
                    <td width="">
                    <? 
					if( $pre_amount_data>$embl_pre_amount)
						{
						echo "Less Booking";
						}
					else if ($pre_amount_data<$embl_pre_amount) 
						{
						echo "Over Booking";
						} 
					else if ($pre_amount_data==$embl_pre_amount) 
						{
							echo "As Per";
						} 
					else
						{
						echo "";
						}
						?>
                    </td>
                    </tr>
	   <?
	     $tot_pre_cost2+=$pre_amount_data;
	  	 $tot_embl_amount+=$embl_pre_amount;
		//$tot_embl_pre_amount_up+=$embl_pre_amount;
		$tot_balance_cost+=$tot_balance;
	   $i++;
					}
       ?>
	</tbody>
    	<tfoot>
            <tr>
                <td colspan="3" align="right"><b>Total</b></td>
                <td align="right"> <b></b></td>
                <td  align="right"><b><? echo number_format($tot_pre_cost2,2); ?></b></td>
                 
                 <td align="right"> <b>  <? echo number_format($tot_embl_amount,2); ?> </b>  </td>
                 <td align="right"><b> <? echo number_format(abs($tot_balance_cost),2); ?></b> </td>
             </tr>
        </tfoot>
    </table>
    <?
  }
	?>
   <br>
		 <?
            echo signature_table(57, $cbo_company_name, "1100px");
			echo "****".custom_file_name($txt_booking_no,$style_ref_no[0][csf('style_ref_no')],$txt_job_no);
         ?>
 </div>

</div>	
	 
<? 
exit();
}
?>
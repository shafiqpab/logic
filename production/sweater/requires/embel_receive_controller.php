<?
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];
$user_level = $_SESSION['logic_erp']['user_level'];
//------------------------------------------------------------------------------------------------------
//$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	if($data[1]==1) { $dropdown_name="cbo_location"; $load_function=""; }
	else if($data[1]==2) { $dropdown_name="cbo_emb_location"; $load_function="load_drop_down( 'requires/embel_receive_controller', this.value, 'load_drop_down_floor', 'floor_td' );"; }
	echo create_drop_down( $dropdown_name, 130, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "$load_function" );	
	exit();
}

if ($action=="load_drop_down_floor")
{
 	echo create_drop_down( "cbo_floor", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (7,8,9) order by floor_name","id,floor_name", 1, "--Select Floor--", $selected, "",0 );
	exit();
}

if ($action=="load_drop_down_working_com")
{
	$data=explode("_",$data);

	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_source').value)";
	else $load_function="";
	
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_emb_company", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Embel. Company-", $data[2], "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_emb_company", 130, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-Embel. Company-", $data[2], "" );
	}	
	exit();	 
}

if($action=="load_drop_down_embl_name")
{
	if($db_type==0) $embel_name_cond="group_concat(b.emb_name) as emb_name";
	else if($db_type==2) $embel_name_cond="LISTAGG(b.emb_name,',') WITHIN GROUP ( ORDER BY b.emb_name) as emb_name";
	//echo "select $embel_name_cond from wo_po_details_master a, wo_pre_cost_embe_cost_dtls b where a.job_no=b.job_no and a.job_no='$data' and b.emb_name!=3 and b.status_active=1 and b.is_deleted=0";
	$emb_name=return_field_value("$embel_name_cond","wo_po_details_master a, wo_pre_cost_embe_cost_dtls b","a.job_no=b.job_no and a.job_no='$data' and b.emb_name!=3 and b.status_active=1 and b.is_deleted=0 ","emb_name");
	if($emb_name=="") $emb_name=0;

	echo create_drop_down( "cbo_embel_name", 130, $emblishment_name_array,"", 1, "-Select Embel.Name-", $selected, "load_drop_down( 'requires/embel_receive_controller', this.value+'**'+document.getElementById('txt_job_no').value, 'load_drop_down_embro_issue_type', 'embro_type_td');","","$emb_name" );
	exit();
}

if($action=="load_drop_down_embro_issue_type")
{
	$data=explode("**",$data);
	$emb_name=$data[0];
	$job_no=$data[1];

	if($db_type==0) $embel_name_cond="group_concat(b.emb_type) as emb_type";
	else if($db_type==2) $embel_name_cond="LISTAGG(b.emb_type,',') WITHIN GROUP ( ORDER BY b.emb_type) as emb_type";
	$embl_type=return_field_value("$embel_name_cond","wo_po_details_master a, wo_pre_cost_embe_cost_dtls b","a.job_no=b.job_no and b.emb_name!=3 and a.job_no='$job_no' and b.emb_name=$emb_name and b.status_active=1 and b.is_deleted=0 ","emb_type");

	if($emb_name==1)
		echo create_drop_down( "cbo_embel_type", 130, $emblishment_print_type,"", 1, "-Printing-", $selected, "fnc_dtls_data_load('".$job_no."','".$emb_name."',this.value,0);" ,"","$embl_type");
	elseif($emb_name==2)
		echo create_drop_down( "cbo_embel_type", 130, $emblishment_embroy_type,"", 1, "-Embroidery-", $selected ,"fnc_dtls_data_load('".$job_no."','".$emb_name."',this.value,0);","","$embl_type" );
	elseif($emb_name==4)
		echo create_drop_down( "cbo_embel_type", 130, $emblishment_spwork_type,"", 1, "-Special Works-", $selected,"fnc_dtls_data_load('".$job_no."','".$emb_name."',this.value,0);","","$embl_type" );
	elseif($emb_name==5)
		echo create_drop_down( "cbo_embel_type", 130, $emblishment_gmts_type,"", 1, "-Gmts Dyeing-", $selected,"fnc_dtls_data_load('".$job_no."','".$emb_name."',this.value,0);","","$embl_type" );
	else
		echo create_drop_down( "cbo_embel_type", 130, $blank_array,"", 1, "--Select--", $selected, "" );
	exit();
}

if ($action=="job_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
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
        <table width="830" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
            	<tr>
                	<th colspan="7" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>
                    <th>Buyer Name</th>
                    <th>Job No</th>
                    <th>Style Ref </th>
                    <th>Order No</th>
                    <th colspan="2">Pub. Ship Date Range</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" />
                        <input type="hidden" id="selected_job">
                    </th>
                </tr>
            </thead>
            <tr class="general">
        		<td><? echo create_drop_down( "cbo_buyer_id", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_name' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --" ); ?></td>
                <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes_numeric" style="width:50px"></td>
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td>
                <td align="center">
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( '<? echo $cbo_company_name; ?>'+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value, 'create_po_search_list_view', 'search_div', 'embel_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        	</tr>
            <tr>
                <td align="center" colspan="7"><? echo load_month_buttons(1); ?></td>
            </tr>
        </table>
        <div id="search_div"></div>
    </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_po_search_list_view")
{
	//echo $data;die;
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }

	if(str_replace("'","",$data[1])==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer="";
		}
		else $buyer="";
	}
	else $buyer=" and a.buyer_name='$data[1]'";

	if($db_type==0)
	{
		$insert_year="YEAR(a.insert_date)";
		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[5]";
		if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
		$ponoCond="group_concat(b.po_number)";
		$shipdateCond="group_concat(b.shipment_date)";
	}
	else if($db_type==2)
	{
		$insert_year="to_char(a.insert_date,'YYYY')";
		$year_cond=" and to_char(a.insert_date,'YYYY')=$data[5]";
		if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
		$ponoCond="rtrim(xmlagg(xmlelement(e,b.po_number,',').extract('//text()') order by b.po_number).GetClobVal(),',')";
		$shipdateCond="rtrim(xmlagg(xmlelement(e,b.shipment_date,',').extract('//text()') order by b.shipment_date).GetClobVal(),',')";
	}

	$order_cond=""; $job_cond=""; $style_cond="";
	if($data[6]==1)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num='$data[4]'  $year_cond";
		if (trim($data[7])!="") $order_cond=" and b.po_number='$data[7]'  "; //else  $order_cond="";
		if (trim($data[8])!="") $style_cond=" and a.style_ref_no='$data[8]'  "; //else  $style_cond="";
	}
	else if($data[6]==4 || $data[6]==0)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]%'  $year_cond"; //else  $job_cond="";
		if (trim($data[7])!="") $order_cond=" and b.po_number like '%$data[7]%'  ";
		if (trim($data[8])!="") $style_cond=" and a.style_ref_no like '%$data[8]%'  "; //else  $style_cond="";
	}
	else if($data[6]==2)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '$data[4]%'  $year_cond"; //else  $job_cond="";
		if (trim($data[7])!="") $order_cond=" and b.po_number like '$data[7]%'  ";
		if (trim($data[8])!="") $style_cond=" and a.style_ref_no like '$data[8]%'  "; //else  $style_cond="";
	}
	else if($data[6]==3)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]'  $year_cond"; //else  $job_cond="";
		if (trim($data[7])!="") $order_cond=" and b.po_number like '%$data[7]'  ";
		if (trim($data[8])!="") $style_cond=" and a.style_ref_no like '%$data[8]'  "; //else  $style_cond="";
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	
	$sql= "select $insert_year as year, a.quotation_id, a.id, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.job_quantity, $ponoCond as po_number, sum(b.po_quantity) as po_quantity, $shipdateCond as shipment_date from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_dtls c where a.garments_nature=100 and a.job_no=b.job_no_mst and a.job_no=c.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1 and ( c.embel_cost is not NULL ) $shipment_date $company $buyer $job_cond $style_cond $order_cond $year_cond 
	group by a.insert_date, a.quotation_id, a.id, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.job_quantity order by a.id DESC";
	//echo $sql;
	$result=sql_select($sql);
	?> 
 	<div align="left" style=" margin-left:5px;margin-top:10px"> 
    	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" align="left" class="rpt_table" >
 			<thead>
 				<th width="30">SL</th>
 				<th width="50">Year</th>
 				<th width="50">Job No</th>               
 				<th width="100">Buyer Name</th>
                <th width="100">Style Ref. No</th>
 				<th width="80">Job Qty.</th>
 				<th width="150">PO number</th>
                <th width="80">PO Quantity</th>
 				<th>Shipment Date</th>
 			</thead>
 		</table>
    	<div style="width:830px; max-height:270px; overflow-y:scroll" id="container_batch" >	 
 			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="810" class="rpt_table" id="list_view">  
 				<?
 				$i=1;
 				foreach ($result as $row)
 				{  
 					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
					if($db_type==2)
					{
						$row[csf('po_number')]= $row[csf('po_number')]->load();
						$row[csf('shipment_date')]= $row[csf('shipment_date')]->load();
					}
					
					$ex_po=implode(", ",explode(",",$row[csf('po_number')]));
					$ex_shipment_date=explode(",",$row[csf('shipment_date')]);
					$shipmentDate="";
					foreach($ex_shipment_date as $shipDate)
					{
						if($shipmentDate=="") $shipmentDate=change_date_format($shipDate); else $shipmentDate.=', '.change_date_format($shipDate);
					}
					
 					?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')].'__'.$row[csf('job_no')].'__'.$row[csf('buyer_name')].'__'.$row[csf('style_ref_no')];?>')"> 
                            <td width="30"><? echo $i; ?>  </td>  
                            <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                            <td width="50" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                            <td width="100" style="word-break:break-all"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                            <td width="100" style="word-break:break-all"><? echo $row[csf('style_ref_no')]; ?></td>
                            <td width="80" align="right"><? echo $row[csf('job_quantity')]; ?></td>
                            <td width="150" style="word-break:break-all"><? echo $ex_po; ?></td>
                            <td width="80"><? echo $row[csf('po_quantity')]; ?></td>
                            <td style="word-break:break-all"><? echo $shipmentDate; ?></td>
                        </tr> 
                        <? 
                        $i++;
 				}
 				?> 
 			</table>        
 		</div>
 	</div>

	<?
	exit();
}


if ($action=="work_order_popup")
{
	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	?>
	<script> 
		function js_set_value( strCon ) 
		{ 
			$('#txt_selected').val( strCon ); 
			parent.emailwindow.hide();
		} 
    </script>
	<?
	extract($_REQUEST); 
	//  echo "<pre>";
	//  print_r($_REQUEST); 
	//  die;
	$sql= "SELECT a.id,a.booking_no,b.rate  from wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_embe_cost_dtls c,wo_pre_cos_emb_co_avg_con_dtls d where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and c.id=d.pre_cost_emb_cost_dtls_id and b.po_break_down_id=d.po_break_down_id and  a.booking_type=6 and a.is_short=2 and  a.status_active=1  and a.is_deleted=0  and c.emb_name=".$cbo_embel_name." and a.company_id=".$company_id." and c.job_no='$txt_job_no'  group by a.id,a.booking_no,b.rate  order by a.booking_no";
	// echo $sql; die;
	?> 
		<input type='hidden' id='txt_selected_id' />
		<input type='hidden' id='txt_selected' /> 
		<div style="width:320px; max-height:300px; overflow-y:scroll" id="scroll_body" >          
			<table cellspacing="0" width="300"  border="1" rules="all" class="rpt_table" id="list_view" >
				<thead>
					<th ></th>
					<th >WO NO</th>
				</thead>
				<?  
				$i=1;
				$result = sql_select($sql);	
				// $result  =[ ['ID'=>1111, 'BOOKING_NO'=>1111],['ID'=>2222, 'BOOKING_NO'=>2222],['ID'=>3333, 'BOOKING_NO'=>3333]];
				foreach($result as $row)
				{
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";   
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('booking_no')]."_".$row[csf('rate')]; ?>');"> 
						<td ><? echo $i; ?></td>
						<td  align="center"><p><? echo $row[csf('booking_no')]; ?></p></td>  
					</tr>
				<?
				$i++;
				}
				?>
			</table>
		</div>  
	<?
 exit();
}

if($action=="get_wo_data")
{
	extract($_REQUEST); 
	$data_arr = explode("**",$data);
	$sql= "SELECT a.id,a.booking_no,b.rate  from wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_embe_cost_dtls c,wo_pre_cos_emb_co_avg_con_dtls d where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and c.id=d.pre_cost_emb_cost_dtls_id and b.po_break_down_id=d.po_break_down_id and  a.booking_type=6 and a.is_short=2 and  a.status_active=1  and a.is_deleted=0  and c.emb_name=".$data_arr[1]." and c.job_no='$data_arr[0]'  group by a.id,a.booking_no,b.rate  order by a.booking_no";
	$result = sql_select($sql);	

	echo "$('#txt_wo_no').val(".$result[0][csf("booking_no")].");\n";
	echo "$('#txt_wo_id').val(".$result[0][csf("id")].");\n";
	echo "$('#txt_wo_rate').val(".$result[0][csf("rate")].");\n";
	exit();
}
if ($action=="order_details")
{
	$exdata=explode("***",$data);
	$company_id=0; $jobno=''; $update_id=0;
	
	$company_id=$exdata[0];
	$jobno=$exdata[1];
	$embl_name=$exdata[2];
	$embl_type=$exdata[3];
	$update_id=$exdata[4];
	
	$dtlsDataArr=array(); $mstDataArr=array();
	/*if($update_id!=0)
	{*/
		$sql_prod="select a.id, a.delivery_mst_id, a.po_break_down_id, a.item_number_id, a.country_id, b.color_size_break_down_id, b.production_qnty from  pro_garments_production_mst a, pro_garments_production_dtls b, pro_gmts_delivery_mst c where c.job_no='$jobno' and a.id=b.mst_id and c.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		
		$sql_prod_result =sql_select($sql_prod);
		foreach ($sql_prod_result as $row)
		{
			if($update_id==$row[csf("delivery_mst_id")])
			{
				$dtlsDataArr[$row[csf("color_size_break_down_id")]]['up']=$row[csf("production_qnty")];
				$mstDataArr[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]]=$row[csf("id")];
			}
			else
			{
				$dtlsDataArr[$row[csf("color_size_break_down_id")]]['old']=$row[csf("production_qnty")];
			}
		}
		unset($sql_prod_result);
	//}
	
	$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	
	$sql_dtls="SELECT a.id, a.po_number, a.pub_shipment_date, b.id as cid, b.item_number_id, b.country_id, b.country_ship_date, b.color_number_id, b.size_number_id, b.order_quantity, b.plan_cut_qnty
	
	from wo_po_break_down a, wo_po_color_size_breakdown b, wo_pre_cost_embe_cost_dtls c, wo_pre_cos_emb_co_avg_con_dtls d
	where a.id=b.po_break_down_id and a.job_no_mst=c.job_no and c.id=d.pre_cost_emb_cost_dtls_id and a.id=d.po_break_down_id and d.requirment!=0 and d.item_number_id=b.item_number_id and d.color_number_id=b.color_number_id and d.size_number_id=b.size_number_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.job_no_mst='$jobno' and c.emb_name='$embl_name' and c.emb_type='$embl_type'";
	//echo $sql_dtls;
	$sql_result =sql_select($sql_dtls);
	$k=0;
	foreach ($sql_result as $row)
	{
		$k++;
		if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		$qty=0; $dtls_id=""; $placeholderQty="";
		/*if($update_id!=0)
		{*/
			$qty=$dtlsDataArr[$row[csf("cid")]]['up'];
			$old_qty=$dtlsDataArr[$row[csf("cid")]]['old'];
			$dtls_id=$mstDataArr[$row[csf("id")]][$row[csf("item_number_id")]][$row[csf("country_id")]];
			$placeholderQty=$row[csf("plan_cut_qnty")]-($old_qty);
		//}
		
		$dtlsData="";
		$dtlsData=$row[csf("item_number_id")].'*'.$row[csf("country_id")];
		?>
        <tr bgcolor="<? echo $bgcolor; ?>">
            <td width="30" align="center"><? echo $k; ?></td>
            <td width="130" style="word-break:break-all"><? echo $row[csf("po_number")]; ?></td>
            <td width="70"><? echo change_date_format($row[csf("pub_shipment_date")]); ?></td>
            <td width="120" style="word-break:break-all"><? echo $garments_item[$row[csf("item_number_id")]]; ?></td>
            <td width="120" style="word-break:break-all"><? echo $country_arr[$row[csf("country_id")]]; ?></td>
            <td width="70"><? echo change_date_format($row[csf("country_ship_date")]); ?></td>
            <td width="140" style="word-break:break-all"><? echo $color_arr[$row[csf("color_number_id")]]; ?></td>
            <td width="80" style="word-break:break-all"><? echo $size_arr[$row[csf("size_number_id")]]; ?></td>
            <td width="80" align="right" id="orderQty_<? echo $k; ?>"><? echo $row[csf("order_quantity")]; ?></td>
            <td width="80" align="right" id="planCutQty_<? echo $k; ?>"><? echo $row[csf("plan_cut_qnty")]; ?></td>
            <td align="right">
            	<input type="text" name="txtReceiveQty_<? echo $k; ?>" id="txtReceiveQty_<? echo $k; ?>" class="text_boxes_numeric" style="width:70px;" value="<? echo $qty; ?>" placeholder="<? echo $placeholderQty; ?>" pre_issue_qty="<? echo $old_qty; ?>" onBlur="fnc_total_calculate(this.value,<? echo $i; ?>);" />
                <input type="hidden" name="txtpoid_<? echo $k; ?>" id="txtpoid_<? echo $k; ?>" style="width:30px" class="text_boxes" value="<? echo $row[csf("id")]; ?>" />
                <input type="hidden" name="txtColorSizeid_<? echo $k; ?>" id="txtColorSizeid_<? echo $k; ?>" style="width:30px" class="text_boxes" value="<? echo $row[csf("cid")]; ?>" />
                <input type="hidden" name="txtDtlsData_<? echo $k; ?>" id="txtDtlsData_<? echo $k; ?>" style="width:30px" class="text_boxes" value="<? echo $dtlsData; ?>" />
                <input type="hidden" name="txtDtlsUpId_<? echo $k; ?>" id="txtDtlsUpId_<? echo $k; ?>" style="width:30px" class="text_boxes" value="<? echo $dtls_id; ?>" />
            </td>
        </tr>
		<?
	}
	exit();
}

if($action=="system_number_popup")
{
  	echo load_html_head_contents("Embellishment Receive Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value( strCon ) 
		{
			document.getElementById('hidd_str_data').value=strCon;
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%; overflow-y:hidden;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="850" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>                	 
                    <th width="130">Company name</th>
                    <th width="100">Receive No</th>
                    <th width="100">Job No</th>
                    <th width="100">Style Ref.</th>
                    <th width="100">Order No</th>
                    <th width="180">Date Range</th>
                    <th>
                    	<input name="hidd_str_data" id="hidd_str_data" class="text_boxes" style="width:100px" type="hidden"/>
                    	<input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  />
                    </th>           
                </tr>
            </thead>
            <tbody>
                <tr class="general">                    
                    <td><? echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --","", ""); ?></td>
                    <td><input name="txt_issue_no" id="txt_issue_no" class="text_boxes" style="width:90px"  placeholder="Write"/></td>
                    <td><input name="txt_job_search" id="txt_job_search" class="text_boxes" style="width:90px"  placeholder="Write"/></td>
                    <td><input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:90px"  placeholder="Write"/></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:90px" placeholder="Write" /></td>
                    <td>
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                    </td>
                    <td>
                    	<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_issue_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style_ref').value, 'create_system_search_list_view', 'search_div', 'embel_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
                </tr>
                <tr>                  
                     <td align="center" valign="middle" colspan="7"><? echo load_month_buttons(1); ?></td>
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
    exit();
}

if($action=="create_system_search_list_view")
{
    $ex_data = explode("_",$data);
	$company = $ex_data[0];	
	$issue_no = $ex_data[1];
	$job_no = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$cut_year= $ex_data[5];
	$order_no= $ex_data[6];
	$style_ref= $ex_data[7];
	
	if(str_replace("'","",$company)==0) { echo "Please select company First"; die;}
    if($db_type==2)
	{ 
		$year_cond=" and extract(year from a.insert_date)=$cut_year"; 
		$year=" extract(year from a.insert_date) as year";
	}
    else  if($db_type==0)
	{ 
		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cut_year"; 
		$year=" SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
	}
	if(str_replace("'","",$company)==0) $conpany_cond=""; else $conpany_cond="and a.company_id=".str_replace("'","",$company)."";
	if(str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and c.job_no_prefix_num='".str_replace("'","",$job_no)."'";
	if(str_replace("'","",$system_no)=="") $system_cond=""; else $system_cond="and a.sys_number_prefix_num=".trim($issue_no)." $year_cond";
	if(str_replace("'","",$order_no)=="") $order_cond=""; else $order_cond=" and d.po_number='".str_replace("'","",$order_no)."'";
	if(str_replace("'","",$style_ref)=="") $style_ref_cond=""; else $style_ref_cond="and c.style_ref_no='".str_replace("'","",$style_ref)."'";
	
	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$location_arr = return_library_array("select id,location_name from lib_location", 'id', 'location_name');
	$floor_arr = return_library_array("select id,floor_name from lib_prod_floor", 'id', 'floor_name');
	
	if( $from_date!="" && $to_date!="" )
	{
		if($db_type==0)
		{
			$sql_cond= " and a.delivery_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		}
		if($db_type==2)
		{
			$sql_cond= " and a.delivery_date between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		}
	}
	
	$sql_pop="SELECT a.id, a.sys_number_prefix_num, a.sys_number, a.company_id, a.location_id, a.production_source, a.working_company_id, a.working_location_id, a.floor_id, a.job_id, a.job_no, a.delivery_date, a.embel_name, a.embel_type, a.sending_company, a.sending_location, a.challan_no, a.remarks, c.style_ref_no, c.buyer_name, $year
    FROM pro_gmts_delivery_mst a, pro_garments_production_mst b, wo_po_details_master c, wo_po_break_down d
    where c.garments_nature=100 and a.entry_form=330 and b.entry_form=330 and a.id=b.delivery_mst_id and b.po_break_down_id=d.id and c.job_no=d.job_no_mst $conpany_cond $job_cond $sql_cond $order_cond $system_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	group by a.id, a.sys_number_prefix_num, a.sys_number, a.company_id, a.location_id, a.production_source, a.working_company_id, a.working_location_id, a.floor_id, a.job_id, a.job_no, a.delivery_date, a.embel_name, a.embel_type, a.sending_company, a.sending_location, a.challan_no, a.remarks, c.style_ref_no, c.buyer_name, a.insert_date order by a.id DESC";
	//echo $sql_order;
	
	$sql_pop_res=sql_select($sql_pop);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="985" >
        <thead>
            <th width="30">SL</th>
            <th width="100">Location</th>
            <th width="60">Receive Year</th>
            <th width="60">Receive No</th>
            <th width="120">Embel. Company</th>
            <th width="120">Embel. Location</th>
            <th width="80">Floor</th>
            <th width="70">Receive Date</th>
            <th width="110">Job No</th>
            <th>Style Ref.</th>
        </thead>
        </table>
        <div style="width:985px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="965" class="rpt_table" id="list_view">
        <tbody>
            <? 
            $i=1;
            foreach($sql_pop_res as $row)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				$cbo_embel_name = $row[csf('embel_name')];
				$company = $row[csf('company_id')];
				$txt_job_no = $row[csf('job_no')];

				$booking_sql= "SELECT a.id,a.booking_no  from wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_embe_cost_dtls c,wo_pre_cos_emb_co_avg_con_dtls d where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and c.id=d.pre_cost_emb_cost_dtls_id and b.po_break_down_id=d.po_break_down_id and  a.booking_type=6 and a.is_short=2 and  a.status_active=1  and a.is_deleted=0  and c.emb_name=".$cbo_embel_name." and a.company_id=".$company." and c.job_no='$txt_job_no' group by a.id,a.booking_no  order by a.booking_no";
				$result_booking_sql = sql_select($booking_sql);
				 
				$booking_id = '';
				foreach($result_booking_sql as $booking_no)
				{
					$booking_id.=$booking_no['BOOKING_NO'];
				}
				$wo_booking_no = rtrim($booking_id, ',');
				
				$sending_location=0;
				if($row[csf('sending_company')]!=0) $sending_location=$row[csf('sending_location')].'*'.$row[csf('sending_company')]
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf('id')].'_'.$row[csf('sys_number')].'_'.$row[csf('company_id')].'_'.$row[csf('location_id')].'_'.$row[csf('production_source')].'_'.$row[csf('working_company_id')].'_'.$row[csf('working_location_id')].'_'.$row[csf('floor_id')].'_'.$row[csf('job_id')].'_'.$row[csf('job_no')].'_'.change_date_format($row[csf('delivery_date')]).'_'.$row[csf('embel_name')].'_'.$row[csf('embel_type')].'_'.$sending_location.'_'.$row[csf('challan_no')].'_'.$row[csf('remarks')].'_'.$row[csf('style_ref_no')].'_'.$row[csf('buyer_name')].'_'.$wo_booking_no; ?>')" style="cursor:pointer" >
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $location_arr[$row[csf('location_id')]]; ?></td>
                    <td width="60" align="center"><? echo $row[csf('year')]; ?></td>
                    <td width="60" align="center"><? echo $row[csf('sys_number_prefix_num')]; ?></td>
                    <td width="120" style="word-break:break-all"><? echo $company_arr[$row[csf('working_company_id')]]; ?></td>
                    <td width="120" style="word-break:break-all"><? echo $location_arr[$row[csf('working_location_id')]]; ?></td>
                    <td width="80" style="word-break:break-all"><? echo $floor_arr[$row[csf('floor_id')]]; ?></td>
                    <td width="70" style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
                    <td width="110" style="word-break:break-all"><? echo $row[csf('job_no')]; ?></td>	
                    <td style="word-break:break-all"><? echo $row[csf('style_ref_no')]; ?></td>
                </tr>
				<? 
                $i++; 
            } 
            ?>
        </tbody>
    </table>
    </div>
	<?    
	exit();
}

//pro_garments_production_mst
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	//$is_control=return_field_value("is_control","variable_settings_production","company_name=$cbo_company_name and variable_list=33 and page_category_id=28","is_control");

	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$sending_location =$sending_company =0;
		if(str_replace("'","",$cbo_sending_location)!=0)
		{
			$cbo_sending_location = explode("*",str_replace("'","",$cbo_sending_location));
			$sending_location = $cbo_sending_location[0];
			$sending_company = $cbo_sending_location[1];
		}
		
		if (str_replace("'","",$txt_update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')"; else $year_cond="";	
			$new_sys_number = explode("*", return_next_id_by_sequence("", "pro_gmts_delivery_mst",$con,1,$cbo_company_name,'EMBR',0,date("Y",time()),0,0,64,0,0 ));
			
			$field_array_delivery = "id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, location_id, production_source, working_company_id, working_location_id, floor_id, job_id, job_no, delivery_date, embel_name, embel_type, sending_company, sending_location, challan_no, remarks, entry_form, inserted_by, insert_date, status_active, is_deleted";
			$mst_id = return_next_id_by_sequence("pro_gmts_delivery_mst_seq", "pro_gmts_delivery_mst", $con );
				
			$data_array_delivery="(".$mst_id.",'".$new_sys_number[1]."','".(int)$new_sys_number[2]."','".$new_sys_number[0]."',".$cbo_company_name.",64,".$cbo_location.",".$cbo_source.",".$cbo_emb_company.",".$cbo_emb_location.",".$cbo_floor.",".$txt_job_id.",".$txt_job_no.",".$txt_receive_date.",".$cbo_embel_name.",".$cbo_embel_type.",'".$sending_company."','".$sending_location."',".$txt_challan.",".$txt_remark.",330,".$user_id.",'".$pc_date_time."',1,0)";
		}
		else
		{
			$mst_id = str_replace("'","",$txt_update_id);
			
			$field_array_delivery = "location_id*production_source*working_company_id*working_location_id*floor_id*job_id*job_no*delivery_date*embel_name*embel_type*sending_company*sending_location*challan_no*remarks*updated_by*update_date";
			$data_array_delivery = "".$cbo_location."*".$cbo_source."*".$cbo_emb_company."*".$cbo_emb_location."*".$cbo_floor."*".$txt_job_id."*".$txt_job_no."*".$txt_receive_date."*".$cbo_embel_name."*".$cbo_embel_type."*".$sending_company."*".$sending_location."*".$txt_challan."*".$txt_remark."*".$user_id."*'".$pc_date_time."'";
		}
		
		$mstArr=array(); $dtlsArr=array();
		for($j=1;$j<=$tot_row;$j++)
		{
			$txtReceiveQty 		="txtReceiveQty_".$j;
			$txtpoid 			="txtpoid_".$j;
			$txtDtlsData	 	="txtDtlsData_".$j;
			$txtColorSizeid 	="txtColorSizeid_".$j;
			$txtDtlsUpId 		="txtDtlsUpId_".$j;
			
			$ex_item_country=explode("*",str_replace("'","",$$txtDtlsData));
			
			$issueQty=str_replace("'","",$$txtReceiveQty);
			$po_id=str_replace("'","",$$txtpoid);
			$colorSizeid=str_replace("'","",$$txtColorSizeid);
			
			$gmts_item=$ex_item_country[0];
			$country_id=$ex_item_country[1];
			
			$mstArr[$po_id][$gmts_item][$country_id]+=$issueQty;
			$dtlsArr[$colorSizeid]+=$issueQty;
			$colorSizeArr[$colorSizeid] =$po_id."**".$gmts_item."**".$country_id;
		}
		
		$field_array_mst="id, delivery_mst_id, garments_nature, company_id, location, production_source, sending_location, sending_company, floor_id, production_date, challan_no, remarks, po_break_down_id, item_number_id, country_id, serving_company, embel_name, embel_type, production_quantity, production_type, entry_break_down_type, entry_form, inserted_by, insert_date, status_active, is_deleted,wo_order_id,wo_rate";
		$data_array_mst="";
		
		foreach($mstArr as $orderId=>$orderData)
		{
			foreach($orderData as $gmtsItemId=>$gmtsItemIdData)
			{
				foreach($gmtsItemIdData as $countryId=>$qty)
				{
					$id= return_next_id_by_sequence("pro_gar_production_mst_seq", "pro_garments_production_mst", $con );
					
					if($data_array_mst!="") $data_array_mst.=",";
					$data_array_mst.="(".$id.",".$mst_id.",".$garments_nature.",".$cbo_company_name.",".$cbo_location.",".$cbo_source.",'".$sending_company."','".$sending_location."',".$cbo_floor.",".$txt_receive_date.",".$txt_challan.",".$txt_remark.",".$orderId.", ".$gmtsItemId.",".$countryId.",".$cbo_emb_company.",".$cbo_embel_name.",".$cbo_embel_type.",".$qty.",64,3,330,".$user_id.",'".$pc_date_time."',1,0,".$txt_wo_id.",".$txt_wo_rate.")";
					
					$mstIdArr[$orderId][$gmtsItemId][$countryId]=$id;
				}
			}
		}
		
		$field_array_dtls ="id, delivery_mst_id, mst_id, production_type, color_size_break_down_id, production_qnty, entry_form, status_active, is_deleted";
		
		$data_array_dtls="";
		foreach($dtlsArr as $colorSizeid=>$qty)
		{
			$colorSizedData 		=explode("**",$colorSizeArr[$colorSizeid]);
			$gmtsMstId 				=$mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
			if($data_array_dtls!="") $data_array_dtls.=",";

			$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

			$data_array_dtls.= "(".$dtls_id.",".$mst_id.",".$gmtsMstId.",64,'".$colorSizeid."','".$qty."',330,1,0)"; 
		}

		$flag=1;
		if (str_replace("'","",$txt_update_id)=="")
		{
			$rID_mst=sql_insert("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,1);
			if($rID_mst==1 && $flag==1) $flag=1; else $flag=0;
		}
		else
		{
			$rID_mst = sql_update("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, "id", $mst_id, 1);
			if($rID_mst==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		$rID=sql_insert("pro_garments_production_mst",$field_array_mst,$data_array_mst,1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);
		if($dtlsrID==1 && $flag==1) $flag=1; else $flag=0;
		
		// echo "10**insert into pro_garments_production_mst($field_array_mst)values".$data_array_mst;die;
		// echo "10**".$rID_mst."**".$rID."**".$dtlsrID."**".$flag;die;

		if($db_type==0)
		{  
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$mst_id."**".str_replace("'","",$new_sys_number[0]);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==1 || $db_type==2 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "0**".$mst_id."**".str_replace("'","",$new_sys_number[0]);
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
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
 		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$sending_location =$sending_company =0;
		if(str_replace("'","",$cbo_sending_location)!=0)
		{
			$cbo_sending_location = explode("*",str_replace("'","",$cbo_sending_location));
			$sending_location = $cbo_sending_location[0];
			$sending_company = $cbo_sending_location[1];
		}
		
		$mst_id = str_replace("'", "", $txt_update_id);
		$field_array_delivery = "location_id*production_source*working_company_id*working_location_id*floor_id*job_id*job_no*delivery_date*embel_name*embel_type*sending_company*sending_location*challan_no*remarks*updated_by*update_date";
		$data_array_delivery = "".$cbo_location."*".$cbo_source."*".$cbo_emb_company."*".$cbo_emb_location."*".$cbo_floor."*".$txt_job_id."*".$txt_job_no."*".$txt_receive_date."*".$cbo_embel_name."*".$cbo_embel_type."*".$sending_company."*".$sending_location."*".$txt_challan."*".$txt_remark."*".$user_id."*'".$pc_date_time."'";
		
		$mstArr=array(); $dtlsArr=array(); $colorSizeArr=array(); $mstIdArr=array();
		for($j=1;$j<=$tot_row;$j++)
		{
			$txtReceiveQty 		="txtReceiveQty_".$j;
			$txtpoid 			="txtpoid_".$j;
			$txtDtlsData	 	="txtDtlsData_".$j;
			$txtColorSizeid 	="txtColorSizeid_".$j;
			$txtDtlsUpId 		="txtDtlsUpId_".$j;
			
			$ex_item_country=explode("*",str_replace("'","",$$txtDtlsData));
			
			$issueQty=str_replace("'","",$$txtReceiveQty);
			$po_id=str_replace("'","",$$txtpoid);
			$colorSizeid=str_replace("'","",$$txtColorSizeid);
			
			$gmts_item=$ex_item_country[0];
			$country_id=$ex_item_country[1];
			
			$mstArr[$po_id][$gmts_item][$country_id]+=$issueQty;
			$mstIdArr[$po_id][$gmts_item][$country_id]=str_replace("'","",$$txtDtlsUpId);
			$dtlsArr[$colorSizeid]+=$issueQty;
			$colorSizeArr[$colorSizeid] =$po_id."**".$gmts_item."**".$country_id;
		}
		
		$field_array_up="location*production_source*sending_location*sending_company*floor_id*production_date*challan_no*remarks*po_break_down_id*item_number_id*country_id*serving_company*embel_name*embel_type*production_quantity*updated_by*update_date*wo_order_id*wo_rate";
		
		$field_array_mst="id, delivery_mst_id, garments_nature, company_id, location, production_source, sending_location, sending_company, floor_id, production_date, challan_no, remarks, po_break_down_id, item_number_id, country_id, serving_company, embel_name, embel_type, production_quantity, production_type, entry_break_down_type, entry_form, inserted_by, insert_date, status_active, is_deleted,wo_order_id,wo_rate";
		$data_array_mst= ""; $is_up=0;
		foreach ($mstArr as $orderId => $orderData)
		{
			if($orderId)
			{
				foreach ($orderData as $gmtsItemId => $gmtsItemIdData)
				{
					foreach ($gmtsItemIdData as $countryId => $qty) 
					{
						$gmtProdId=$mstIdArr[$orderId][$gmtsItemId][$countryId];
						
						if($gmtProdId=="")
						{
							$id= return_next_id_by_sequence( "pro_gar_production_mst_seq", "pro_garments_production_mst", $con );
							if($data_array_mst!="") $data_array_mst.=",";
							$data_array_mst.="(".$id.",".$mst_id.",".$garments_nature.",".$cbo_company_name.",".$cbo_location.",".$cbo_source.",'".$sending_company."','".$sending_location."',".$cbo_floor.",".$txt_receive_date.",".$txt_challan.",".$txt_remark.",".$orderId.", ".$gmtsItemId.",".$countryId.",".$cbo_emb_company.",".$cbo_embel_name.",".$cbo_embel_type.",".$qty.",64,3,330,".$user_id.",'".$pc_date_time."',1,0,".$txt_wo_id.",".$txt_wo_rate.")";
						}
						else
						{
							$data_array_up[$gmtProdId] =explode("*",("".$cbo_location."*".$cbo_source."*'".$sending_company."'*'".$sending_location."'*".$cbo_floor."*".$txt_receive_date."*".$txt_challan."*".$txt_remark."*".$orderId."*".$gmtsItemId."*".$countryId."*".$cbo_emb_company."*".$cbo_embel_name."*".$cbo_embel_type."*".$qty."*'".$user_id."'*'".$pc_date_time."'*".$txt_wo_id."*".$txt_wo_rate.""));
							$id=$gmtProdId;
							$is_up=1;
							$id_arr[]=$gmtProdId;
						}
						$mstIdArr[$orderId][$gmtsItemId][$countryId]=$id;
					}
				}
			}
		}
		
		$field_array_dtls ="id, delivery_mst_id, mst_id, production_type, color_size_break_down_id, production_qnty, entry_form, status_active, is_deleted";
		
		$data_array_dtls="";
		foreach($dtlsArr as $colorSizeid=>$qty)
		{
			$colorSizedData 		=explode("**",$colorSizeArr[$colorSizeid]);
			$gmtsMstId 				=$mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
			if($data_array_dtls!="") $data_array_dtls.=",";

			$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

			$data_array_dtls.= "(".$dtls_id.",".$mst_id.",".$gmtsMstId.",64,'".$colorSizeid."','".$qty."',330,1,0)"; 
		}

		$flag=1;
		$rID_mst = sql_update("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, "id", $mst_id, 1);
		if($rID_mst==1 && $flag==1) $flag=1; else $flag=0;
		
		if($data_array_up!="")
		{
			$rID1=execute_query(bulk_update_sql_statement("pro_garments_production_mst", "id",$field_array_up,$data_array_up,$id_arr ));
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		if($data_array_mst!="")
		{
			$rID=sql_insert("pro_garments_production_mst",$field_array_mst,$data_array_mst,1);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		$dtlsrDelete = execute_query("update pro_garments_production_dtls set status_active=0, is_deleted=1 WHERE delivery_mst_id=$mst_id and production_type=64 and status_active=1 and is_deleted=0");
		if($dtlsrDelete==1 && $flag==1) $flag=1; else $flag=0;
		
		$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);
		if($dtlsrID==1 && $flag==1) $flag=1; else $flag=0;
		//echo "10**insert into pro_gmts_delivery_mst($field_array_delivery)values".$data_array_delivery;die;
		//echo "10**".$rID_mst."**".$rID1."**".$rID."**".$dtlsrID."**".$flag;
		//print_r($data_array_up);
		//echo bulk_update_sql_statement("pro_garments_production_dtls", "id",$field_array_up,$data_array_up,$id_arr );
		
		//die;

		if($db_type==0)
		{  
			if($flag==1)
			{ 
				mysql_query("COMMIT");  
				echo "1**".$mst_id."**".str_replace("'","",$txt_system_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==1 || $db_type==2 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "1**".$mst_id."**".str_replace("'","",$txt_system_no);
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
	else if ($operation==2)  // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$rID = sql_delete("pro_garments_production_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id ',$txt_mst_id,1);
		$dtlsrID = sql_delete("pro_garments_production_dtls","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);

 		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$hidden_po_break_down_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$hidden_po_break_down_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="emblishment_receive_print")// 29/10/2019
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$mst_id=implode(',',explode("_",$data[1]));
	//print_r( $mst_id);die;
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$order_library=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number"  );
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
	$sel_col=($db_type!=0) ? " listagg(color_type_id,',') within group(order by color_type_id) as color_type_id  " : " group_concat(color_type_id) as color_type_id";

	$sql_color_type=sql_select("SELECT $sel_col  from pro_garments_production_dtls where status_active=1 and is_deleted=0 and delivery_mst_id=$mst_id and production_type=64");
	$color_type_id=$sql_color_type[0][csf("color_type_id")];
	$type_ids=array_unique(explode(",", $color_type_id));
	$color_tp="";
	foreach($type_ids as $key=>$val)
	{
		if($color_tp=="")
		{
			$color_tp=$color_type[$val];
		}
		else
		{
			$color_tp .=','.$color_type[$val];
		}

	}

	if($db_type==0)
	{
		$body_part_info_cond=",group_concat(body_part_info) as body_part_info";
	}
	else if($db_type==2)
	{
		$body_part_info_cond=",LISTAGG(CAST(body_part_info AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as body_part_info";
	} 



	$sql="SELECT id,company_id, sum(production_quantity) as production_quantity,sum(reject_qnty) as  reject_qnty, challan_no,    po_break_down_id, item_number_id, entry_break_down_type, break_down_type_rej,country_id,
      production_source,  serving_company, location ,
	   embel_name, embel_type,  production_date,production_type,remarks ,body_part_info from pro_garments_production_mst where production_type=64 and delivery_mst_id=$mst_id and status_active=1 and is_deleted=0 group by id,company_id,production_quantity, reject_qnty, challan_no, po_break_down_id, item_number_id, entry_break_down_type, break_down_type_rej,country_id,
     production_source,  serving_company, location ,
	   embel_name, embel_type,  production_date,production_type,remarks ,body_part_info";
	// echo $sql;die;
	$dataArray=sql_select($sql);
	$po_id_array=array();

	foreach($dataArray as $row)
	{
		if($body_part_info!='') $body_part_info.=", ".$row[csf('body_part_info')];else  $body_part_info=$row[csf('body_part_info')];
		$po_id_array[$row[csf("po_break_down_id")]]=$row[csf("po_break_down_id")];
	}
	
	
	$break_down_type_reject=$dataArray[0][csf('break_down_type_rej')];
	$entry_break_down_type=$dataArray[0][csf('entry_break_down_type')];


	$order_array=array();
	$po_id_con=where_con_using_array($po_id_array,0,"b.id");
	$order_sql="SELECT a.style_ref_no, a.job_no,a.buyer_name, b.id, b.po_number, b.po_quantity from  wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_id_con";
	//echo $order_sql;die;
	$order_sql_result=sql_select($order_sql);
	foreach ($order_sql_result as $row)
	{
		$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		// $order_array[$row[csf('id')]]['po_number'].=$row[csf('po_number')].",";
		$po_number.=$row[csf('po_number')].",";
		$order_array[$row[csf('id')]]['po_quantity']+=$row[csf('po_quantity')];
		$order_array[$row[csf('id')]]['job']=$row[csf('job_no')];
	}
	

	?>
	<div style="width:930px;">


    <table width="900" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:12px">
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')];?>
						City No: <? echo $result[csf('city')];?>
						Zip Code: <? echo $result[csf('zip_code')]; ?>
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email Address: <? echo $result[csf('email')];?>
						Website No: <? echo $result[csf('website')];
					}
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:18px"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
        </tr>
        <tr>
			<?
                $supp_add=$dataArray[0][csf('serving_company')];
                $nameArray=sql_select( "select address_1,web_site,email,country_id from lib_supplier where id=$supp_add");
                foreach ($nameArray as $result)
                {
                    $address="";
                    if($result!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                }
				//echo $address;
            ?>
        	<td width="100" rowspan="6" valign="top" colspan="2"><p><strong>Embel. Company : <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]]; else if($dataArray[0][csf('production_source')]==3) echo $supplier_library[$dataArray[0][csf('serving_company')]].'<br>'.$address;  ?></strong></p></td>
            <td width="125"><strong>Order No :</strong></td><td width="175px"><? echo chop($po_number,","); ?></td>
           
        	<td width="125"><strong>Style Ref. :</strong></td><td width="175px"><? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['style_ref_no'];; ?></td>
        </tr>
        <tr>
        	<td><strong>Order Qty :</strong></td><td><? echo  $order_array[$dataArray[0][csf('po_break_down_id')]]['po_quantity']; ?></td>
        	<td><strong>Item :</strong></td><td><? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Emb. Source :</strong></td><td><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
           <td><strong>Emb. Type :</strong></td><td ><? if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==5) echo $emblishment_gmts_type[$dataArray[0][csf('embel_type')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Embel. Name :</strong></td>
            <td><? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
            <td><strong>Receive Date :</strong></td>
            <td><? echo change_date_format($dataArray[0][csf('production_date')]); ?></td>

        </tr>
        <tr>
            <td><strong>Challan No :</strong></td>
            <td><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Buyer :</strong></td>
            <td><? echo $buyer_library[$order_array[$dataArray[0][csf('po_break_down_id')]]['buyer_name']];; ?></td>

        </tr>
        <tr>
            <td><strong>Job No :</strong></td>
            <td><? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['job']; ?></td>
            <td><strong>Color Type :</strong></td>
            <td><? echo  $color_type[$color_type_id];?></td>
        </tr>
        <tr>
        	<td colspan="4" ><strong>Remarks :  <? echo $dataArray[0][csf('remarks')]; ?></strong></td>
        </tr>

        <tr>
         <?
		if($entry_break_down_type==1)
		{
		?>

        	<td colspan="3" ><strong>Receive Qnty :  <? echo $dataArray[0][csf('production_quantity')]; ?></strong></td>
       <?
		}
		 if($break_down_type_reject==1)
		{
  ?>
            <td colspan="3" ><strong>Reject Qnty:  <? echo $dataArray[0][csf('reject_qnty')]; ?></strong></td>
       <? }
		   else
		 { ?>
			   <td colspan="6">&nbsp;</td>
	<?   }
	 ?>
        </tr>

    </table>
         <br>
        <?
		if($entry_break_down_type!=1)
		{
			//$mst_id=$dataArray[0][csf('id')];
			$po_id_con=where_con_using_array($po_id_array,0,"b.po_break_down_id");
			
			$sql="SELECT sum(a.production_qnty) as production_qnty,sum(a.reject_qty) as reject_qty,b.item_number_id,b.po_break_down_id ,b.color_number_id, b.size_number_id,b.country_id,c.body_part_info,c.challan_no from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where c.id=a.mst_id and a.delivery_mst_id=$mst_id $po_id_con and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.size_number_id,b.item_number_id,b.po_break_down_id, b.color_number_id,b.country_id,c.body_part_info,c.challan_no ";
			//echo $sql;die;
			$result=sql_select($sql);
			$size_array=array ();
			$qun_array=array ();
			$color_array=array ();
			$body_part_info=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('production_qnty')];
				//$reject_qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('reject_qty')];
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$body_part_info[$row[csf('challan_no')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('body_part_info')]] = $row[csf('body_part_info')];
				
			}

			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
			$country_id=return_field_value("b.country_id"," pro_garments_production_dtls a, wo_po_color_size_breakdown b","a.delivery_mst_id=$mst_id  and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ","country_id");
		?>

	<div style="width:100%;">
    <div style="margin-left:30px;"><strong> Goods Qty.</strong></div>
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="80" align="center">Color/Size</th>
              <th width="80" align="center">Country</th>
				<?
                foreach ($size_array as $sizid)
                {
					//$size_count=count($sizid);
                    ?>
                        <th width="150"><strong><? echo  $sizearr[$sizid];  ?></strong></th>
                    <?
                }
                ?>
            <th width="80" align="center">Total Issue Qnty.</th>
        </thead>
        <tbody>
			<?
            //$mrr_no=$dataArray[0][csf('issue_number')];
            $i=1;
            $tot_qnty=array();
                foreach($color_array as $cid)
                {
                    if ($i%2==0)
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";
					$color_count=count($cid);
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i;  ?></td>
                        <td><? echo $colorarr[$cid]; ?></td>
                         <td><? echo $country_library[$country_id]; ?></td>
                        <?
                        foreach ($size_array as $sizval)
                        {
							$size_count=count($sizval);
                            ?>
                            <td align="right"><? echo $qun_array[$cid][$sizval]; ?></td>
                            <?
                            $tot_qnty[$cid]+=$qun_array[$cid][$sizval];
							$tot_qnty_size[$sizval]+=$qun_array[$cid][$sizval];
                        }
                        ?>
                        <td align="right"><? echo $tot_qnty[$cid]; ?></td>
                    </tr>
                    <?
					$production_quantity+=$tot_qnty[$cid];
					$i++;
                }
            ?>
        </tbody>
        <tr>
            <td colspan="3" align="right"><strong>Grand Total :</strong></td>
            <?
				foreach ($size_array as $sizval)
				{
					?>
                    <td align="right"><?php echo $tot_qnty_size[$sizval]; ?></td>
                    <?
				}
			?>
            <td align="right"><?php echo $production_quantity; ?></td>
        </tr>
    </table>
      <br>

		 <?
		}
		if($break_down_type_reject!=1)
		{ 
		
			 $sql="SELECT sum(a.production_qnty) as production_qnty,sum(a.reject_qty) as reject_qty, b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.delivery_mst_id=$mst_id and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.reject_qty>0 group by  b.size_number_id, b.color_number_id ";
			//echo $sql;
			$result=sql_select($sql);
			$size_array=array ();
			$qun_array=array ();
			$reject_qun_array=array ();
			$color_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				//$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
				$reject_qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('reject_qty')];
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			}

			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");


				$country_id2=return_field_value("b.country_id"," pro_garments_production_dtls a, wo_po_color_size_breakdown b","delivery_mst_id=$mst_id and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","country_id");
		?>

	<div style="width:100%;">
    <div style="width:900px; margin-left:30px;">
   <table align="right" cellspacing="0" width="900"  border="0" rules="all" class="rpt_table">
    <tr><td><strong> Reject Qty:</strong><?=$reject_qun_array[$row[csf('reject_qty')]];?></td></tr>
    </table>
    </div>
    &nbsp;<br>
    <?
	

		?>
		<br>
		<? }
            echo signature_table(27, $data[0], "900px");
         ?>
	</div>
	</div>
	<?
	exit();
}


if($action=="file_upload")
{
	header("Content-Type: application/json");
	$filename = time().$_FILES['file']['name']; 
	$location = "../../../file_upload/".$filename; 
    //echo "0**".$filename; die;
	$uploadOk = 1;
	if(empty($txt_update_id))
	{
		$txt_update_id=$_GET['txt_update_id'];
	} 
    
	if(move_uploaded_file($_FILES['file']['tmp_name'], $location))
	{ 
		$uploadOk = 1;
	}
	else
	{ 
		$uploadOk=0; 
	} 
    // echo "0**".$uploadOk; die;
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}

	$id=return_next_id( "id","COMMON_PHOTO_LIBRARY", 1 ) ;
	$data_array .="(".$id.",'".$txt_update_id."','embel_receive','file_upload/".$filename."','2','".$filename."','".$pc_date_time."')";
	$field_array="id,master_tble_id,form_name,image_location,file_type,real_file_name,insert_date";
	$rID=sql_insert("COMMON_PHOTO_LIBRARY",$field_array,$data_array,1);

	if($db_type==0)
	{
		if($rID==1 && $uploadOk==1)
		{
			mysql_query("COMMIT");
			echo "0**".$new_system_id[0]."**".$hidden_mrr_id;
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "10**".$hidden_mrr_id;
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID==1 && $uploadOk==1)
		{
			oci_commit($con);
			echo "0**".$new_system_id[0]."**".$hidden_mrr_id;
		}
		else
		{
			oci_rollback($con);
			echo "10**".$rID."**".$uploadOk."**INSERT INTO COMMON_PHOTO_LIBRARY(".$field_array.") VALUES ".$data_array;
		}
	}
	disconnect($con);
	die;
}


if ($action=="load_drop_down_color_type")
{

	$sql="SELECT b.color_type_id from  wo_po_break_down a,wo_pre_cost_fabric_cost_dtls b,wo_pre_cos_fab_co_avg_con_dtls c where   a.job_no_mst=b.job_no  and b.id=c.pre_cost_fabric_cost_dtls_id and a.id=c.po_break_down_id and b.job_no=c.job_no  and   a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id='$data' and c.cons>0  group by b.color_type_id";
	foreach(sql_select($sql) as $key=>$vals)
	{
		$color_type_arr[$vals[csf("color_type_id")]]=$color_type[$vals[csf("color_type_id")]];
	}


	if(count(sql_select($sql))>1)
	{
		echo create_drop_down( "cbo_color_type", 110, $color_type_arr,"", 0, "Select Type", $selected,"");
	}
	else
	{
		echo create_drop_down( "cbo_color_type", 110, $color_type_arr,"", 0, "Select Type", $selected,"");
	}


	exit();
}

?>
<script type="text/javascript">
	function getActionOnEnter(event){
			if (event.keyCode == 13){
				document.getElementById('btn_show').click();
			}

	}
</script>

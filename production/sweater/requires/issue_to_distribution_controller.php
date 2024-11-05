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
	else if($data[1]==2) { $dropdown_name="cbo_distribution_location"; $load_function="load_drop_down( 'requires/issue_to_distribution_controller', this.value, 'load_drop_down_floor', 'floor_td' );"; }
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
		echo create_drop_down( "cbo_distribution_company", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-W. Company-", $data[2], "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_distribution_company", 130, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-W. Company-", $data[2], "" );
	}	
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
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( '<? echo $cbo_company_name; ?>'+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value, 'create_po_search_list_view', 'search_div', 'issue_to_distribution_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
	
	$sql= "select $insert_year as year, a.quotation_id, a.id, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.job_quantity, $ponoCond as po_number, sum(b.po_quantity) as po_quantity, $shipdateCond as shipment_date from wo_po_details_master a, wo_po_break_down b where a.garments_nature=100 and a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1  $shipment_date $company $buyer $job_cond $style_cond $order_cond $year_cond 
	group by a.insert_date, a.quotation_id, a.id, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.job_quantity order by a.id DESC";
	//echo $sql;die;
	$result=sql_select($sql);
	?> 
 	<div align="left" style=" margin-left:5px;margin-top:10px">
    	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" align="left" class="rpt_table" >
 			<thead>
 				<th width="30">SL</th>
 				<th width="50">Year</th>
 				<th width="50">Job No</th>               
 				<th width="100">Buyer Name</th>
                <th width="100">Style Ref.</th>
 				<th width="80">Job Qty.</th>
 				<th width="150">PO No</th>
                <th width="80">PO Qty</th>
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

	// echo $company_id;die; // 2
	//echo $txt_job_no;die; // SSL-23-00543

	 
	$sql= "SELECT a.id,a.booking_no  from wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_embe_cost_dtls c,wo_pre_cos_emb_co_avg_con_dtls d where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and c.id=d.pre_cost_emb_cost_dtls_id and b.po_break_down_id=d.po_break_down_id and  a.booking_type=6 and a.is_short=2 and  a.status_active=1  and a.is_deleted=0 and a.company_id=".$company_id." and c.job_no='$txt_job_no' group by a.id,a.booking_no  order by a.booking_no";
	//echo $sql; die;
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
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('booking_no')]; ?>');"> 
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

if ($action=="order_details")
{
	$exdata=explode("***",$data);
	$company_id=0; $jobno=''; $update_id=0;
	
	$company_id=$exdata[0];
	$jobno=$exdata[1];
	$update_id=$exdata[2];
	
	$dtlsDataArr=array(); $mstDataArr=array();

	$sql_prod="select a.id, a.delivery_mst_id, a.po_break_down_id, a.item_number_id, a.country_id, b.color_size_break_down_id, b.production_qnty from  pro_garments_production_mst a, pro_garments_production_dtls b, pro_gmts_cutting_qc_mst c where c.job_no='$jobno' and a.id=b.mst_id and b.delivery_mst_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	
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
	
	$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	
	$sql_dtls="SELECT a.id, a.po_number, a.pub_shipment_date, b.id as cid, b.item_number_id, b.country_id, b.country_ship_date, b.color_number_id, b.size_number_id, b.order_quantity, b.plan_cut_qnty
	
	from wo_po_break_down a, wo_po_color_size_breakdown b
	where a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no_mst='$jobno' order by b.id, b.color_order, b.size_order  ";
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
            	<input type="text" name="txtIssueQty_<? echo $k; ?>" id="txtIssueQty_<? echo $k; ?>" class="text_boxes_numeric" style="width:70px;" value="<? echo $qty; ?>" placeholder="<? echo $placeholderQty; ?>" pre_issue_qty="<? echo $old_qty; ?>" onBlur="fnc_total_calculate(this.value,<? echo $i; ?>);" />
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
  	echo load_html_head_contents("Issue To Distribution Point Info","../../../", 1, 1, '','1','');
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
                    <th width="100">Issue No</th>
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
                    	<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_issue_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style_ref').value, 'create_system_search_list_view', 'search_div', 'issue_to_distribution_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
                </tr>
                <tr>                  
                     <td align="center" valign="middle" colspan="7"><? echo load_month_buttons(1); ?></td>
                </tr>   
            </tbody>
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
	if(str_replace("'","",$system_no)=="") $system_cond=""; else $system_cond="and a.cut_qc_prefix_no=".trim($issue_no)." $year_cond";
	if(str_replace("'","",$order_no)=="") $order_cond=""; else $order_cond=" and d.po_number='".str_replace("'","",$order_no)."'";
	if(str_replace("'","",$style_ref)=="") $style_ref_cond=""; else $style_ref_cond="and c.style_ref_no='".str_replace("'","",$style_ref)."'";
	
	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$location_arr = return_library_array("select id,location_name from lib_location", 'id', 'location_name');
	$floor_arr = return_library_array("select id,floor_name from lib_prod_floor", 'id', 'floor_name');
	
	if( $from_date!="" && $to_date!="" )
	{
		if($db_type==0)
		{
			$sql_cond= " and a.cutting_qc_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		}
		if($db_type==2)
		{
			$sql_cond= " and a.cutting_qc_date between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		}
	}
	// wo_booking_mst
	$sql_pop="SELECT a.id, a.cut_qc_prefix_no, a.cutting_qc_no, a.company_id, a.location_id, a.production_source, a.serving_company, a.service_location, a.floor_id, a.job_no, a.cutting_qc_date, b.sending_location, b.sending_company, b.challan_no, a.remarks, c.style_ref_no, c.buyer_name, $year 
    FROM pro_gmts_cutting_qc_mst a, pro_garments_production_mst b, wo_po_details_master c, wo_po_break_down d
    where c.garments_nature=100 and b.entry_form=674 and a.id=b.delivery_mst_id and b.po_break_down_id=d.id and c.job_no=d.job_no_mst $conpany_cond $job_cond $sql_cond $order_cond $system_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	group by a.id, a.cut_qc_prefix_no, a.cutting_qc_no, a.company_id, a.location_id, a.production_source, a.serving_company, a.service_location, a.floor_id, a.job_no, a.cutting_qc_date, b.sending_location, b.sending_company, b.challan_no, a.remarks, c.style_ref_no, c.buyer_name, a.insert_date order by a.id DESC";
	
	/*$booking_sql= "SELECT a.id,a.booking_no  from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=6 and a.is_short=2 and  a.status_active=1  and a.is_deleted=0 and a.company_id=".$company."  group by a.id,a.booking_no  order by a.booking_no";
	//echo $booking_sql;die;
	$result_booking_sql = sql_select($booking_sql);
	 
	$booking_id = '';
	foreach($result_booking_sql as $booking_no)
	{
		$booking_id.=$booking_no['BOOKING_NO'];
	}*/
	
  // echo $sql_pop;die;
	
	$sql_pop_res=sql_select($sql_pop);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="985" >
        <thead>
            <th width="30">SL</th>
            <th width="100">Location</th>
            <th width="60">Issue Year</th>
            <th width="60">Issue No</th>
            <th width="120">W. Company</th>
            <th width="120">W. Location</th>
            <th width="80">Floor</th>
            <th width="70">Issue Date</th>
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

				$company_id = $row[csf('company_id')];
				$txt_job_no = $row[csf('job_no')];
				$wo_booking_no = rtrim($booking_id, ',');
				 
				$sending_location=0;
				if($row[csf('sending_company')]!=0) $sending_location=$row[csf('sending_location')].'*'.$row[csf('sending_company')]
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" data-id="<?=$row[csf('working_company_id')].'_'.$row[csf('job_no')] ?>" onClick="js_set_value('<? echo $row[csf('id')].'_'.$row[csf('cutting_qc_no')].'_'.$row[csf('company_id')].'_'.$row[csf('location_id')].'_'.$row[csf('production_source')].'_'.$row[csf('serving_company')].'_'.$row[csf('service_location')].'_'.$row[csf('floor_id')].'_'.$row[csf('job_no')].'_'.change_date_format($row[csf('cutting_qc_date')]).'_'.$sending_location.'_'.$row[csf('challan_no')].'_'.$row[csf('remarks')].'_'.$row[csf('style_ref_no')].'_'.$row[csf('buyer_name')].'_'.$wo_booking_no; ?>')" style="cursor:pointer" >
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $location_arr[$row[csf('location_id')]]; ?></td>
                    <td width="60" align="center"><? echo $row[csf('year')]; ?></td>
                    <td width="60" align="center"><? echo $row[csf('cut_qc_prefix_no')]; ?></td>
                    <td width="120" style="word-break:break-all"><? echo $company_arr[$row[csf('serving_company')]]; ?></td>
                    <td width="120" style="word-break:break-all"><? echo $location_arr[$row[csf('service_location')]]; ?></td>
                    <td width="80" style="word-break:break-all"><? echo $floor_arr[$row[csf('floor_id')]]; ?></td>
                    <td width="70" style="text-align:center;"><? echo change_date_format($row[csf('cutting_qc_date')]); ?></td>
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
			$new_system_id = explode("*", return_next_id_by_sequence("", "pro_gmts_cutting_qc_mst",$con,1,$cbo_company_name,'IDP',0,date("Y",time()),0,0,86,0,0 ));
			
			$field_array_qc_mst="id, garments_nature, cut_qc_prefix, cut_qc_prefix_no, cutting_qc_no, company_id, job_no, location_id, floor_id, cutting_qc_date, production_source, serving_company, service_location, production_type, remarks, inserted_by, insert_date, status_active, is_deleted";
			
			$mst_id = return_next_id_by_sequence(  "pro_gmts_cutting_qc_mst_seq", "pro_gmts_cutting_qc_mst", $con );
			
			$data_arra_cutt_mst="(".$mst_id.", 100, '".$new_system_id[1]."', ".(int)$new_system_id[2].", '".$new_system_id[0]."',".$cbo_company_name.",".$txt_job_no.",".$cbo_location.",".$cbo_floor.",".$txt_issue_date.",".$cbo_source.",".$cbo_distribution_company.",".$cbo_distribution_location.",86,".$txt_remark.",".$user_id.",'".$pc_date_time."',1,0)";
		}
		else
		{
			$mst_id = str_replace("'","",$txt_update_id);
			
			$field_array_delivery = "location_id*floor_id*cutting_qc_date*production_source*serving_company*service_location*remarks*updated_by*update_date";
			$data_array_delivery = "".$cbo_location."*".$cbo_floor."*".$txt_issue_date."*".$cbo_source."*".$cbo_distribution_company."*".$cbo_distribution_location."*".$txt_remark."*".$user_id."*'".$pc_date_time."'";
		}
		
		$mstArr=array(); $dtlsArr=array();
		for($j=1;$j<=$tot_row;$j++)
		{
			$txtIssueQty 		="txtIssueQty_".$j;
			$txtpoid 			="txtpoid_".$j;
			$txtDtlsData	 	="txtDtlsData_".$j;
			$txtColorSizeid 	="txtColorSizeid_".$j;
			$txtDtlsUpId 		="txtDtlsUpId_".$j;
			
			$ex_item_country=explode("*",str_replace("'","",$$txtDtlsData));
			
			$issueQty=str_replace("'","",$$txtIssueQty);
			$po_id=str_replace("'","",$$txtpoid);
			$colorSizeid=str_replace("'","",$$txtColorSizeid);
			
			$gmts_item=$ex_item_country[0];
			$country_id=$ex_item_country[1];
			
			$mstArr[$po_id][$gmts_item][$country_id]+=$issueQty;
			$dtlsArr[$colorSizeid]+=$issueQty;
			$colorSizeArr[$colorSizeid] =$po_id."**".$gmts_item."**".$country_id;
		}
		
		$field_array_mst="id, delivery_mst_id, garments_nature, company_id, location, production_source, sending_location, sending_company, floor_id, production_date, challan_no, remarks, po_break_down_id, item_number_id, country_id, serving_company, production_quantity, production_type, entry_break_down_type, entry_form, inserted_by, insert_date, status_active, is_deleted, wo_order_id";
		$data_array_mst="";
		
		foreach($mstArr as $orderId=>$orderData)
		{
			foreach($orderData as $gmtsItemId=>$gmtsItemIdData)
			{
				foreach($gmtsItemIdData as $countryId=>$qty)
				{
					$id= return_next_id_by_sequence("pro_gar_production_mst_seq", "pro_garments_production_mst", $con );
					
					if($data_array_mst!="") $data_array_mst.=",";
					$data_array_mst.="(".$id.",".$mst_id.",".$garments_nature.",".$cbo_company_name.",".$cbo_location.",".$cbo_source.",'".$sending_company."','".$sending_location."',".$cbo_floor.",".$txt_issue_date.",".$txt_challan.",".$txt_remark.",".$orderId.", ".$gmtsItemId.",".$countryId.",".$cbo_distribution_company.",".$qty.",86,3,674,".$user_id.",'".$pc_date_time."',1,0,".$txt_wo_id.")";
					
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

			$data_array_dtls.= "(".$dtls_id.",".$mst_id.",".$gmtsMstId.",86,'".$colorSizeid."','".$qty."',674,1,0)"; 
		}

		$flag=1;
		if (str_replace("'","",$txt_update_id)=="")
		{
			$rID_mst=sql_insert("pro_gmts_cutting_qc_mst",$field_array_qc_mst,$data_arra_cutt_mst,1);
			if($rID_mst==1 && $flag==1) $flag=1; else $flag=0;
		}
		else
		{
			$rID_mst = sql_update("pro_gmts_cutting_qc_mst", $field_array_delivery, $data_array_delivery, "id", $mst_id, 1);
			if($rID_mst==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		$rID=sql_insert("pro_garments_production_mst",$field_array_mst,$data_array_mst,1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);
		if($dtlsrID==1 && $flag==1) $flag=1; else $flag=0;
		
		//echo "10**insert into pro_gmts_delivery_mst($field_array_delivery)values".$data_array_delivery;die;
		//echo "10**".$rID_mst."**".$rID."**".$dtlsrID."**".$flag;die;

		if($db_type==0)
		{  
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$mst_id."**".str_replace("'","",$new_system_id[0]);
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
				echo "0**".$mst_id."**".str_replace("'","",$new_system_id[0]);
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
		
		$field_array_delivery = "location_id*floor_id*cutting_qc_date*production_source*serving_company*service_location*remarks*updated_by*update_date";
		$data_array_delivery = "".$cbo_location."*".$cbo_floor."*".$txt_issue_date."*".$cbo_source."*".$cbo_distribution_company."*".$cbo_distribution_location."*".$txt_remark."*".$user_id."*'".$pc_date_time."'";
		
		$mstArr=array(); $dtlsArr=array(); $colorSizeArr=array(); $mstIdArr=array();
		for($j=1;$j<=$tot_row;$j++)
		{
			$txtIssueQty 		="txtIssueQty_".$j;
			$txtpoid 			="txtpoid_".$j;
			$txtDtlsData	 	="txtDtlsData_".$j;
			$txtColorSizeid 	="txtColorSizeid_".$j;
			$txtDtlsUpId 		="txtDtlsUpId_".$j;
			
			$ex_item_country=explode("*",str_replace("'","",$$txtDtlsData));
			
			$issueQty=str_replace("'","",$$txtIssueQty);
			$po_id=str_replace("'","",$$txtpoid);
			$colorSizeid=str_replace("'","",$$txtColorSizeid);
			
			$gmts_item=$ex_item_country[0];
			$country_id=$ex_item_country[1];
			
			$mstArr[$po_id][$gmts_item][$country_id]+=$issueQty;
			$mstIdArr[$po_id][$gmts_item][$country_id]=str_replace("'","",$$txtDtlsUpId);
			$dtlsArr[$colorSizeid]+=$issueQty;
			$colorSizeArr[$colorSizeid] =$po_id."**".$gmts_item."**".$country_id;
		}
		
		$field_array_up="location*production_source*sending_location*sending_company*floor_id*production_date*challan_no*remarks*po_break_down_id*item_number_id*country_id*serving_company*production_quantity*updated_by*update_date";
		
		$field_array_mst="id, delivery_mst_id, garments_nature, company_id, location, production_source, sending_location, sending_company, floor_id, production_date, challan_no, remarks, po_break_down_id, item_number_id, country_id, serving_company, production_quantity, production_type, entry_break_down_type, entry_form, inserted_by, insert_date, status_active, is_deleted, wo_order_id";
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
							$data_array_mst.="(".$id.",".$mst_id.",".$garments_nature.",".$cbo_company_name.",".$cbo_location.",".$cbo_source.",'".$sending_company."','".$sending_location."',".$cbo_floor.",".$txt_issue_date.",".$txt_challan.",".$txt_remark.",".$orderId.", ".$gmtsItemId.",".$countryId.",".$cbo_distribution_company.",".$qty.",86,3,674,".$user_id.",'".$pc_date_time."',1,0,".$txt_wo_id.")";
						}
						else
						{
							$data_array_up[$gmtProdId] =explode("*",("".$cbo_location."*".$cbo_source."*'".$sending_company."'*'".$sending_location."'*".$cbo_floor."*".$txt_issue_date."*".$txt_challan."*".$txt_remark."*".$orderId."*".$gmtsItemId."*".$countryId."*".$cbo_distribution_company."*".$qty."*'".$user_id."'*'".$pc_date_time."'"));
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

			$data_array_dtls.= "(".$dtls_id.",".$mst_id.",".$gmtsMstId.",86,'".$colorSizeid."','".$qty."',674,1,0)"; 
		}

		$flag=1;
		$rID_mst = sql_update("pro_gmts_cutting_qc_mst", $field_array_delivery, $data_array_delivery, "id", $mst_id, 1);
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
		
		$dtlsrDelete = execute_query("update pro_garments_production_dtls set status_active=0, is_deleted=1 WHERE delivery_mst_id=$mst_id and production_type=86 and status_active=1 and is_deleted=0");
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

if($action=="issueto_distribution_print") // Print 2 Start.
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$mst_id=implode(',',explode("_",$data[1]));
	//print_r ($mst_id);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name");

	$sql="SELECT a.id, a.company_id, a.challan_no, a.po_break_down_id, a.item_number_id, a.country_id, a.production_source, a.serving_company, a.location, a.production_quantity, a.production_type, a.remarks, a.floor_id, b.cutting_qc_no, cutting_qc_date from pro_garments_production_mst a,pro_gmts_cutting_qc_mst b where a.production_type=86 and a.delivery_mst_id=$mst_id and b.id=a.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
//	echo $sql;die;
	$dataArray=sql_select($sql);
	$issue_date='';
	$po_id_array=array();
	foreach($dataArray as $row)
	{
		if($issue_date!='') $issue_date.=", ".change_date_format($row[csf('production_date')]);else  $issue_date=change_date_format($row[csf('production_date')]);
		if($body_part_info!='') $body_part_info.=", ".$row[csf('body_part_info')];else  $body_part_info=$row[csf('body_part_info')];
		$po_id_array[$row[csf("po_break_down_id")]]=$row[csf("po_break_down_id")];
		
	}
	$body_part_info=array_unique(explode(", ",$body_part_info));

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
		//$po_number=explode($row[csf("po_number")],' ');	
		//$po_number=rtrim($row[csf('po_number')]," ,");
		$order_array[$row[csf('id')]]['po_quantity']+=$row[csf('po_quantity')];
		$order_array[$row[csf('id')]]['job']=$row[csf('job_no')];
	}
	//var_dump($order_array);

	$sql_color_type=sql_select("SELECT color_type_id from pro_garments_production_dtls where status_active=1 and is_deleted=0 and delivery_mst_id=$mst_id and production_type=86");
	$color_type_id=$sql_color_type[0][csf("color_type_id")];

	//echo $issue_dates=implode(", ",array_unique(explode(", ",$issue_date)));
	?>
	<div style="width:930px;">
    <table width="900" cellspacing="0" align="right">
        <tr>

        	<?
            $data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
                ?>
            <td  align="left" rowspan="3">
                <?
                foreach($data_array as $img_row)
                {
					?>
                    <img src='../<? echo $img_row[csf('image_location')]; ?>' height='60' width='200' align="middle" />
                    <?
                }
                ?>
           </td>
            <td colspan="4" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
            <td></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="4" align="center" style="font-size:14px">
				<?
					echo show_company($data[0],'','');//Aziz
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="4" align="center" style="font-size:20px"><u><strong>Challan/Gate Pass</strong></u></td>
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
        	<td width="100" rowspan="4" valign="top" colspan="2"><p><strong>Issue To : <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]]; else if($dataArray[0][csf('production_source')]==3) echo $supplier_library[$dataArray[0][csf('serving_company')]].'<br>'.$address;  ?></strong></p>
        		<p><strong>Location : </strong> <? echo $location_library[$dataArray[0][csf('location')]]; ?></p>
        	</td>
            <td width="125"><strong>Issue ID:</strong></td> <td width="175px"><? echo $dataArray[0][csf('cutting_qc_no')]; ?></td>
            <td width="125"><strong>Buyer :</strong></td><td width="175px"><? echo $buyer_library[$order_array[$dataArray[0][csf('po_break_down_id')]]['buyer_name']]; ?></td>
        </tr>
        <tr>
        <td> <strong>Job No</strong></td> <td> <? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['job']; ?></td>
         <td ><strong>Order No :</strong></td><td><? echo chop($po_number,","); ?></td>
        </tr>
        <tr>

           <td><strong>Order Qty:</strong></td><td><? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['po_quantity']; ?></td>
        	<td><strong>Style Ref. :</strong></td><td><? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['style_ref_no']; ?></td>
        </tr>
        <tr>
        	<td><strong>Item :</strong></td>
        	<td><? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>
            <td colspan="2"><strong>Source:</strong><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Issue Date:</strong></td><td><? echo change_date_format($dataArray[0][csf('cutting_qc_date')]); ?></td>
            <td><strong>Challan No:</strong></td><td><? echo $dataArray[0][csf('challan_no')]; ?></td>
        </tr>
    </table>
         <br>
        <?
			//$mst_id=$dataArray[0][csf('id')];
			
		
			$po_id_con=where_con_using_array($po_id_array,0,"b.po_break_down_id");
			$sql="SELECT sum(a.production_qnty) as production_qnty,b.po_break_down_id, b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.delivery_mst_id=$mst_id $po_id_con and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.size_number_id, b.color_number_id,b.size_order,b.po_break_down_id order by b.size_order";
			// echo $sql; die;
			$result=sql_select($sql);
			$size_array=array ();
			$qun_array=array ();
			
			
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('production_qnty')];
			}
			//pro_garments_production_mst c, c.id=a.mst_id and
			//$po_id_array=array();

			$po_id_con=where_con_using_array($po_id_array,0,"b.po_break_down_id");

			$sql="SELECT sum(a.production_qnty) as production_qnty,c.production_date as issue_date,c.challan_no,b.po_break_down_id, b.color_number_id from pro_garments_production_mst c,pro_garments_production_dtls a, wo_po_color_size_breakdown b where c.id=a.mst_id and a.delivery_mst_id=$mst_id  and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_id_con group by b.po_break_down_id,b.color_number_id,c.production_date,c.challan_no ";

			//echo $sql;
			// and a.production_date='$production_date'
			$result=sql_select($sql);
			$color_array=array ();$issue_data_array=array ();
			foreach ( $result as $row )
			{
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$issue_data_array[$row[csf('color_number_id')]]['issue_date'].=",".$row[csf('issue_date')];
				$issue_data_array[$row[csf('color_number_id')]]['chal_no']=$row[csf('challan_no')];
			}

			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
		?>

	<div style="width:100%;">
	    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
	        <thead bgcolor="#dddddd" align="center">
	            <th width="30">SL</th>
	            <th width="70">Issue Date</th>
	            <th width="70">Chal. No</th>
	            <th width="80" align="center">Color/Size</th>
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
						$issue_date=ltrim($issue_data_array[$cid]['issue_date'],',');
						$challn_no=$issue_data_array[$cid]['chal_no'];
						$date_pro=array_unique(explode(",",$issue_date));
						$all_date='';
						foreach($date_pro as $date_val)
						{
							if($all_date=='') $all_date=change_date_format($date_val);else $all_date.=",".change_date_format($date_val);
						}
						//print_r($date_pro);
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>">
	                        <td><? echo $i;  ?></td>
	                        <td><? echo $all_date;  ?></td>
	                        <td><? echo $challn_no;  ?></td>
	                        <td><? echo $colorarr[$cid]; ?></td>
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
	            <td colspan="4" align="right"><strong>Grand Total :</strong></td>
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
	    &nbsp;<br>
	    <table align="right" cellspacing="0" width="900" >
	        <tr>
	            <td width="80"><strong>Remarks : </strong></td>
	            <td align="left"><p><? echo $dataArray[0][csf('remarks')]; ?></p></td>
	        </tr>
	    </table>
	    &nbsp;<br>
	
	        <br>
			 <?
	            echo signature_table(674, $data[0], "900px");
	         ?>
		</div>
	</div>
	<?
    exit();
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

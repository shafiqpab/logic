<?
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];
$user_level = $_SESSION['logic_erp']['user_level'];
//------------------------------------------------------------------------------------------------------
//$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );

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

if($action=="production_process_control")
{
	echo "$('#hidden_variable_cntl').val('0');\n";
	echo "$('#hidden_preceding_process').val('0');\n";
    $control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=50 and page_category_id=30 and company_name='$data'");
    if(count($control_and_preceding)>0)
    {
      echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf("is_control")]."');\n";
	  echo "$('#hidden_preceding_process').val('".$control_and_preceding[0][csf("preceding_page_id")]."');\n";
    }

	exit();
}

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	if($data[1]==1) { $dropdown_name="cbo_location"; $load_function=""; }
	else if($data[1]==2) { $dropdown_name="cbo_iron_location"; $load_function="load_drop_down( 'requires/leftover_gmts_receive_controller', this.value, 'load_drop_down_floor', 'floor_td' );"; }
	echo create_drop_down( $dropdown_name, 130, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "$load_function" );	
	exit();
}

if ($action=="load_drop_down_floor")
{
 	echo create_drop_down( "cbo_floor", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (5,11) order by floor_name","id,floor_name", 1, "--Select Floor--", $selected, "load_drop_down( 'requires/leftover_gmts_receive_controller', this.value+'_'+document.getElementById('cbo_iron_location').value+'_'+document.getElementById('cbo_iron_company').value, 'load_drop_down_table', 'table_td');",0 );
	exit();
}

if ($action=="load_drop_down_table")
{
	$data=explode("_",$data);
 	echo create_drop_down( "txt_table_no", 130, "select id,TABLE_NAME from lib_table_entry where status_active =1 and is_deleted=0 and TABLE_TYPE=2 and COMPANY_NAME=$data[2] and LOCATION_NAME=$data[1] and FLOOR_NAME=$data[0] order by TABLE_SEQUENCE","id,TABLE_NAME", 1, "--Select Floor--", $selected, "",0 );
	exit();
}

if ($action=="load_drop_down_working_com")
{
	$data=explode("_",$data);

	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_source').value)";
	else $load_function="";
	
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_iron_company", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", $data[2], "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_iron_company", 130, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", $data[2], "" );
	}	
	exit();	 
}

if ($action=="job_popup")
{
  	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
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
                    <th>IR/IB </th>
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
                <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td>
                <td align="center">
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( '<? echo $cbo_company_name; ?>'+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_internal_ref').value, 'create_po_search_list_view', 'search_div', 'leftover_gmts_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        	</tr>
            <tr>
                <td align="center" colspan="7"><? echo load_month_buttons(1); ?></td>
            </tr>
        </table>
        <div id="search_div"></div>
    </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
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

	$order_cond=""; $job_cond=""; $style_cond=""; $ir_cond="";
	if($data[6]==1)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num='$data[4]'  $year_cond";
		if (trim($data[7])!="") $order_cond=" and b.po_number='$data[7]'  "; //else  $order_cond="";
		if (trim($data[8])!="") $style_cond=" and a.style_ref_no='$data[8]'  "; //else  $style_cond="";
		if (trim($data[9])!="") $ir_cond=" and b.grouping='$data[9]'  "; //else  $ir_cond="";
	}
	else if($data[6]==4 || $data[6]==0)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]%'  $year_cond"; //else  $job_cond="";
		if (trim($data[7])!="") $order_cond=" and b.po_number like '%$data[7]%'  ";
		if (trim($data[8])!="") $style_cond=" and a.style_ref_no like '%$data[8]%'  "; //else  $style_cond="";
		if (trim($data[9])!="") $ir_cond=" and b.grouping like '%$data[9]%'  ";
	}
	else if($data[6]==2)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '$data[4]%'  $year_cond"; //else  $job_cond="";
		if (trim($data[7])!="") $order_cond=" and b.po_number like '$data[7]%'  ";
		if (trim($data[8])!="") $style_cond=" and a.style_ref_no like '$data[8]%'  "; //else  $style_cond="";
		if (trim($data[9])!="") $ir_cond=" and b.grouping like '$data[9]%'  ";
	}
	else if($data[6]==3)
	{
		if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num like '%$data[4]'  $year_cond"; //else  $job_cond="";
		if (trim($data[7])!="") $order_cond=" and b.po_number like '%$data[7]'  ";
		if (trim($data[8])!="") $style_cond=" and a.style_ref_no like '%$data[8]'  "; //else  $style_cond="";
		if (trim($data[9])!="") $ir_cond=" and b.grouping like '%$data[9]'  ";
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	
	$sql= "select $insert_year as year, a.quotation_id, a.id, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.job_quantity, $ponoCond as po_number, sum(b.po_quantity) as po_quantity, $shipdateCond as shipment_date,b.grouping from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer $job_cond $style_cond $order_cond $year_cond $ir_cond
	group by a.insert_date, a.quotation_id, a.id, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.job_quantity,b.grouping order by a.id DESC";
	// echo $sql; die;
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
                <th width="80">IR/IB</th>
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
                            <td width="80" style="word-break:break-all"><? echo $row[csf('grouping')]; ?></td>
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

if($action=="wo_no_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function set_checkvalue()
		{
			if(document.getElementById('chk_job_wo_po').value==0)
				document.getElementById('chk_job_wo_po').value=1;
			else
				document.getElementById('chk_job_wo_po').value=0;
		}
		function js_set_value(val)
		{
			$("#hidden_sys_data").val(val);
			//$("#hidden_id").val(id);
			parent.emailwindow.hide();
		}
</script>
</head>
<body>
<div style="width:850px;" align="center" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="850" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th colspan="6">
						<? echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" ); ?>
					</th>
					<th colspan="2" style="text-align:right"><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">WO Without Job</th>
				</tr>
				<tr>
					<th width="120">Buyer Name</th>
					<th width="130">Supplier Name</th>
					<th width="100">WO No</th>
					<th width="100">Job No</th>
                    <th width="100">Style Ref.</th>
					<th width="130" colspan="2"> WO Date Range</th>
					<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchorderfrm_1','search_div','','','','');"  /></th>
				</tr>
			</thead>
			<tbody>
				<tr class="general">
				<td><?=create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", "", "",0); ?></td>
				<td><?=create_drop_down( "cbo_supplier_name", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in (2,21) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select Supplier --", $service_company_id, "",0 ); 
				//echo "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.tag_company=$company_id and b.party_type in (2,21) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name";
				
				?></td>
                <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:90px"></td>
                
                <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:90px"></td>
                <td><input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:90px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"/></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" /> </td>
                <td>
                    <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_style_ref').value+'_'+'<? echo $txt_job_no; ?>', 'create_wo_search_list_view', 'search_div', 'leftover_gmts_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" />
                </td>
            </tr>
            <tr>
                <td align="center" valign="middle" colspan="8">
                    <?=load_month_buttons(1);  ?>
                    <input type="hidden" id="hidden_sys_data" value="hidden_sys_data" />
                </td>
            </tr>
        </tbody>
    </table>
    <div id="search_div"></div>
    </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_wo_search_list_view")
{
	$ex_data = explode("_",$data);
	$supplier = $ex_data[0];
	$fromDate = $ex_data[1];
	$toDate = $ex_data[2];
	$company = $ex_data[3];
	$buyer_val=$ex_data[4];
	$search_category=$ex_data[5];
	$booking_prifix=$ex_data[6];
	$job_prifix=$ex_data[7];
	$year_selection=$ex_data[8];
	$chk_job_wo_po=trim($ex_data[9]);
	$style_ref=$ex_data[10];
	$jobno=$ex_data[11];
		
	if( $supplier!=0 )  $supplier="and a.supplier_id='$supplier'"; else  $supplier="";
	if( $company!=0 )  $company=" and a.company_id='$company'"; else  $company="";
	if( $buyer_val!=0 )  $buyer_cond="and d.buyer_name='$buyer_val'"; else  $buyer_cond="";
	
	$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$ex_data[8]";
	$year_cond=" and to_char(d.insert_date,'YYYY')=$ex_data[8]";
	if( $fromDate!=0 && $toDate!=0 ) $sql_cond= "and a.booking_date  between '".change_date_format($fromDate,'mm-dd-yyyy','/',1)."' and '".change_date_format($toDate,'mm-dd-yyyy','/',1)."'";

	if($search_category==0 || $search_category==4)
	{
		if (str_replace("'","",$job_prifix)!="") $job_cond=" and d.job_no_prefix_num like '%$job_prifix%' $year_cond "; else  $job_cond="";
		if (str_replace("'","",$booking_prifix)!="") $booking_cond=" and a.subcon_wo_suffix_num like '%$booking_prifix%'  $booking_year_cond  "; else $booking_cond="";
	}
	else if($search_category==1)
	{
		if (str_replace("'","",$job_prifix)!="") $job_cond=" and d.job_no_prefix_num ='$job_prifix' "; else  $job_cond="";
		if (str_replace("'","",$booking_prifix)!="") $booking_cond=" and a.subcon_wo_suffix_num ='$booking_prifix'   "; else $booking_cond="";
	}
	else if($search_category==2)
	{
		if (str_replace("'","",$job_prifix)!="") $job_cond=" and d.job_no_prefix_num like '$job_prifix%'  $year_cond"; else  $job_cond="";
		if (str_replace("'","",$booking_prifix)!="") $booking_cond=" and a.subcon_wo_suffix_num like '$booking_prifix%'  $booking_year_cond  "; else $booking_cond="";
	}
	else if($search_category==3)
	{
		if (str_replace("'","",$job_prifix)!="") $job_cond=" and d.job_no_prefix_num like '%$job_prifix'  $year_cond"; else  $job_cond="";
		if (str_replace("'","",$booking_prifix)!="") $booking_cond=" and a.subcon_wo_suffix_num like '%$booking_prifix'  $booking_year_cond  "; else $booking_cond="";
	}

	if($db_type==0) $select_year="year(a.insert_date) as year"; else $select_year="to_char(a.insert_date,'YYYY') as year";
	if($chk_job_wo_po==1)
	{
		$sql = "select a.id, a.subcon_wo_suffix_num, a.SUCON_WO_NO, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode, a.source, a.attention, $select_year, 0 as job_no_id, null as job_no, 0 as buyer_name, null as po_number
		from subcon_wo_mst a
		where a.status_active=1 and a.is_deleted=0 and a.entry_form=643 and a.id not in(select mst_id from subcon_wo_dtls where job_no_id>0 and entry_form=643 and status_active=1 and  is_deleted=0) $company $supplier  $sql_cond  $booking_cond order by a.id DESC";
	}
	else
	{
		$sql = "select a.id, a.subcon_wo_suffix_num, a.SUCON_WO_NO, a.supplier_id, a.booking_date, a.CLOSING_DATE, a.currency, a.service_sweater, TO_CHAR(a.insert_date,'YYYY') as year, d.buyer_name, LISTAGG(CAST(b.job_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no) as job_no, LISTAGG(CAST(d.style_ref_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY d.style_ref_no) as style_ref_no from subcon_wo_mst a, subcon_wo_dtls b, wo_po_details_master d where a.id=b.mst_id and b.job_no = d.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.pay_mode in (1,2,4) and a.entry_form=643 and b.entry_form=643 and d.job_no='$jobno' $company $supplier $sql_cond $buyer_cond $job_cond $booking_cond $job_ids_cond group by a.id, a.subcon_wo_suffix_num, a.SUCON_WO_NO, a.supplier_id, a.booking_date, a.CLOSING_DATE, a.currency, a.service_sweater, a.insert_date, d.buyer_name order by a.id DESC";
	}
	//echo $sql;
	?>
	<div style="width:850px;" align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" >
			<thead>
				<th width="30">SL</th>
				<th width="100">WO no</th>
                <th width="50">WO Year</th>
                <th width="70">WO Date</th>
                <th width="140">Service Company</th>
                <th width="140">Buyer Name</th>
				<th width="100">Job No</th>
                <th width="120">Style Ref.</th>
				<th >Closing Date</th>
			</thead>
		</table>
		<div style="width:850px; overflow-y:scroll; max-height:270px;" id="buyer_list_view">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table" id="tbl_list_search" >
				<?
				$supplier_arr=return_library_array("select id, supplier_name from lib_supplier",'id','supplier_name');
				$buyer_arr=return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');
				$i=1;
				$nameArray=sql_select( $sql );
				$linkingWoArr=array();
				foreach($nameArray as $row)
				{
					$typeofservice=explode(",",$row[csf("service_sweater")]);
					if (in_array(9, $typeofservice)) {
						$linkingWoArr[$row[csf('id')]]=$row[csf('SUCON_WO_NO')];
					}
				}
				//var_dump($nameArray);die;
				foreach ($nameArray as $selectResult)
				{
					if($linkingWoArr[$selectResult[csf('id')]]!="")
					{
						$job_no=implode(",",array_unique(explode(",",$selectResult[csf("job_no")])));
						$style_ref_no=implode(",",array_unique(explode(",",$selectResult[csf("style_ref_no")])));
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$supplier=$supplier_arr[$selectResult[csf('supplier_id')]];
						
						$ref_no=implode(",",array_unique(explode(",",chop($po_ref_arr[$selectResult[csf("id")]],","))));
						?>
						<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" onClick="js_set_value('<?=$selectResult[csf('id')].'_'.$selectResult[csf('SUCON_WO_NO')]; ?>'); ">
							<td width="30" align="center"><?=$i; ?></td>
							<td width="100" align="center" style="word-break:break-all"><?=$selectResult[csf('SUCON_WO_NO')]; ?></td>
							<td width="50" align="center"><?=$selectResult[csf('year')]; ?></td>
							<td width="70"><?=change_date_format($selectResult[csf('booking_date')]); ?></td>
							<td width="140" style="word-break:break-all"><?=$supplier; ?></td>
							<td width="140" style="word-break:break-all"><?=$buyer_arr[$selectResult[csf('buyer_name')]]; ?></td>
							<td width="100" style="word-break:break-all"><?=$job_no; ?></td>
							<td width="120" style="word-break:break-all"><?=$style_ref_no; ?></td>
							<td><?=change_date_format($selectResult[csf('CLOSING_DATE')]); ?></td>
						</tr>
							<?
						$i++;
					}
				}
				?>
			</table>
		</div>
	</div>
		<?
	exit();
}

if ($action=="order_details")
{
	list($company_id,$jobno,$update_id,$order_type,$goods_type,$source) = explode("***",$data);

	/* $control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=50 and page_category_id=30 and company_name='$company_id'");    
	$preceding_process = $control_and_preceding[0][csf("preceding_page_id")]; */

	$qty_source=0;
	if($preceding_process==3) $qty_source=3; //Wash Complete
	else if($preceding_process==5) $qty_source=5; //Sewing Complete
	$qty_source=5;
	$po_id_array=return_library_array( "SELECT id,id from wo_po_break_down where status_active=1 and job_no_mst='$jobno'", "id", "id");

	$sewing_qty_arr = array();
	$po_id_cond = where_con_using_array($po_id_array,0,"a.po_break_down_id");
	$goods_type_cond = ($goods_type==1) ? "b.production_qnty" : "b.reject_qty";
	$qtySourceCond="";
	// =============================== sewing qty ===================================
	if($qty_source!=0)
	{
		$sql_prod="SELECT b.color_size_break_down_id, $goods_type_cond as production_qnty from  pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type=$qty_source $po_id_cond";
		// echo $sql_prod;die;
		$result =sql_select($sql_prod);
		foreach ($result as $val) 
		{
			$sewing_qty_arr[$val[csf("color_size_break_down_id")]] += $val[csf("production_qnty")];
		}
		unset($result);
		$qtySourceCond="and a.production_type=$qty_source";
	}	
	// =============================== ex-factory qty ===================================
	$goods_type_cond2 = ($goods_type==1) ? "b.production_qnty" : "0";
	$sql = "SELECT b.color_size_break_down_id, $goods_type_cond2 as ex_qty from PRO_EX_FACTORY_MST a,PRO_EX_FACTORY_DTLS b where a.id=b.mst_id and b.status_active=1 $po_id_cond";
	// echo $sql;die;
	$res = sql_select($sql);
	$ex_data_arr = array();
	foreach ($res as $v) 
	{
		$ex_data_arr[$v['COLOR_SIZE_BREAK_DOWN_ID']] += $v['EX_QTY'];
	}
	// =============================== receive qty ===================================
	$po_id_conds = where_con_using_array($po_id_array,0,"b.po_break_down_id");
	$sql = "SELECT c.color_size_break_down_id,c.PRODUCTION_QNTY from PRO_LEFTOVER_GMTS_RCV_MST a,PRO_LEFTOVER_GMTS_RCV_DTLS b , PRO_LEFTOVER_GMTS_RCV_CLR_SZ c where a.id=b.mst_id and b.id=c.DTLS_ID and a.id=c.mst_id and a.ORDER_TYPE=$order_type and a.GOODS_TYPE=$goods_type and b.status_active=1 $po_id_conds ";
	// echo $sql;die;
	$res = sql_select($sql);
	$rcv_qty_array = array();
	foreach ($res as $v) 
	{
		$rcv_qty_array[$v['COLOR_SIZE_BREAK_DOWN_ID']] += $v['PRODUCTION_QNTY'];
	}
	
	
	$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	
	$sql_dtls="SELECT a.job_no_mst, a.id, a.po_number, a.pub_shipment_date, b.id as cid, b.item_number_id, b.country_id, b.country_ship_date, b.color_number_id, b.size_number_id, b.order_quantity, b.plan_cut_qnty,b.order_rate from wo_po_break_down a, wo_po_color_size_breakdown b where a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no_mst='$jobno' order by b.color_order,b.size_order";
	//echo $sql_dtls;
	$sql_result =sql_select($sql_dtls);
	$k=0;
	foreach ($sql_result as $row)
	{
		$k++;
		if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
		
		$dtls_id='';//$mstDataArr[$row[csf("id")]][$row[csf("item_number_id")]][$row[csf("country_id")]];
		// $placeholderQty=$row[csf("plan_cut_qnty")]-($old_qty);
		$bal_qty = $sewing_qty_arr[$row[csf("cid")]] - $ex_data_arr[$row[csf("cid")]];
		
		$dtlsData="";
		?>
        <tr bgcolor="<? echo $bgcolor; ?>">
            <td width="30" align="center"><?= $k; ?></td>
            <td width="110" style="word-break:break-all"><?= $row[csf("po_number")]; ?></td>
            <td width="70"><?= change_date_format($row[csf("pub_shipment_date")]); ?></td>
            <td width="110" style="word-break:break-all"><?= $garments_item[$row[csf("item_number_id")]]; ?></td>
            <td width="110" style="word-break:break-all"><?= $country_arr[$row[csf("country_id")]]; ?></td>
            <td width="70"><?= change_date_format($row[csf("country_ship_date")]); ?></td>
            <td width="120" style="word-break:break-all"><?= $color_arr[$row[csf("color_number_id")]]; ?></td>
            <td width="70" style="word-break:break-all"><?= $size_arr[$row[csf("size_number_id")]]; ?></td>
            <td width="70" align="right" id="orderQty_<?= $k; ?>"><?= $row[csf("order_quantity")]; ?></td>
            <td width="70" align="right" id="sewingQty_<?= $k; ?>"><?=$sewing_qty_arr[$row[csf("cid")]]; ?></td>
            <td width="70" align="right" id="exfactQty_<?= $k; ?>"><?=$ex_data_arr[$row[csf("cid")]]; ?></td>
            <td width="70" title="Sewing Qty - Delivery Qty" align="right" id="balQty_<?= $k; ?>"><?=$bal_qty; ?></td>            
           
            <td width="70" align="center">
				<input type="text" name="txtQty_<?= $k; ?>" id="txtQty_<?= $k; ?>" class="text_boxes_numeric" style="width:53px;" value="" onBlur="fnc_total_calculate(this.value,<?= $k; ?>);" placeholder="<?=$bal_qty;?>" />
			</td>           
           

			<td width="70">
				<input type="text" disabled readonly name="orderRate_<?=$k; ?>" id="orderRate_<?=$k; ?>" value="<?= $row[csf("order_rate")]; ?>" style="width:53px;"/>
			</td>
			<td width="70" align="right">
				<input type="text" disabled readonly name="amtUSD_<?=$k; ?>" id="amtUSD_<?=$k; ?>" value="" style="width:53px;"/>
			</td>
			<td  align="right">
                <input type="text" disabled readonly name="amtBDT_<?=$k; ?>" id="amtBDT_<?=$k; ?>" value="" style="width:53px;"/>
                <input type="hidden" name="txtpoid_<?=$k; ?>" id="txtpoid_<?=$k; ?>" value="<?=$row[csf("id")]; ?>" />
                <input type="hidden" name="txtColorSizeid_<?=$k; ?>" id="txtColorSizeid_<?=$k; ?>" value="<?=$row[csf("cid")]; ?>" />
                <input type="hidden" name="txtDtlsData_<?=$k; ?>" id="txtDtlsData_<?=$k; ?>" value="<?=$dtlsData; ?>" />
                <input type="hidden" name="txtDtlsUpId_<?=$k; ?>" id="txtDtlsUpId_<?=$k; ?>" value="<?=$dtls_id; ?>" />
                <input type="hidden" name="txtRate_<?=$k; ?>" id="txtRate_<?=$k; ?>" value="<?=$row[csf("order_rate")]; ?>" />
                <input type="hidden" name="txtItemID_<?=$k; ?>" id="txtItemID_<?=$k; ?>" value="<?=$row[csf("item_number_id")]; ?>" />
                <input type="hidden" name="txtCountryID_<?=$k; ?>" id="txtCountryID_<?=$k; ?>" value="<?=$row[csf("country_id")]; ?>" />
            </td>
        </tr>
		<?
	}
	exit();
}

if ($action=="order_details_update")
{
	list($company_id,$jobno,$update_id,$order_type,$goods_type,$source) = explode("***",$data);

	/* $control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=50 and page_category_id=30 and company_name='$company_id'");    
	$preceding_process = $control_and_preceding[0][csf("preceding_page_id")]; */

	$qty_source=0;
	if($preceding_process==3) $qty_source=3; //Wash Complete
	else if($preceding_process==5) $qty_source=5; //Sewing Complete
	$qty_source=5;
	$po_id_array=return_library_array( "SELECT id,id from wo_po_break_down where status_active=1 and job_no_mst='$jobno'", "id", "id");

	$sewing_qty_arr = array();
	$po_id_cond = where_con_using_array($po_id_array,0,"a.po_break_down_id");
	$goods_type_cond = ($goods_type==1) ? "b.production_qnty" : "b.reject_qty";
	$qtySourceCond="";
	// =============================== sewing qty ===================================
	if($qty_source!=0)
	{
		$sql_prod="SELECT b.color_size_break_down_id, $goods_type_cond as production_qnty from  pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type=$qty_source $po_id_cond";
		// echo $sql_prod;die;
		$result =sql_select($sql_prod);
		foreach ($result as $val) 
		{
			$sewing_qty_arr[$val[csf("color_size_break_down_id")]] += $val[csf("production_qnty")];
		}
		unset($result);
		$qtySourceCond="and a.production_type=$qty_source";
	}	
	// =============================== ex-factory qty ===================================
	$goods_type_cond2 = ($goods_type==1) ? "b.production_qnty" : "0";
	$sql = "SELECT b.color_size_break_down_id, $goods_type_cond2 as ex_qty from PRO_EX_FACTORY_MST a,PRO_EX_FACTORY_DTLS b where a.id=b.mst_id and b.status_active=1 $po_id_cond";
	// echo $sql;die;
	$res = sql_select($sql);
	$ex_data_arr = array();
	foreach ($res as $v) 
	{
		$ex_data_arr[$v['COLOR_SIZE_BREAK_DOWN_ID']] += $v['EX_QTY'];
	}
	// =============================== receive qty ===================================
	$po_id_conds = where_con_using_array($po_id_array,0,"b.po_break_down_id");
	$sql = "SELECT a.EXCHANGE_RATE,c.color_size_break_down_id,c.PRODUCTION_QNTY from PRO_LEFTOVER_GMTS_RCV_MST a,PRO_LEFTOVER_GMTS_RCV_DTLS b , PRO_LEFTOVER_GMTS_RCV_CLR_SZ c where a.id=b.mst_id and b.id=c.DTLS_ID and a.id=c.mst_id and a.id=$update_id";
	// echo $sql;die;
	$res = sql_select($sql);
	$rcv_qty_array = array();
	foreach ($res as $v) 
	{
		$rcv_qty_array[$v['COLOR_SIZE_BREAK_DOWN_ID']] += $v['PRODUCTION_QNTY'];
		$exchange_rate = $v['EXCHANGE_RATE'];
	}
	
	
	$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	
	$sql_dtls="SELECT a.id, a.po_number, a.pub_shipment_date, b.id as cid, b.item_number_id, b.country_id, b.country_ship_date, b.color_number_id, b.size_number_id, b.order_quantity, b.plan_cut_qnty,b.order_rate from wo_po_break_down a, wo_po_color_size_breakdown b where a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no_mst='$jobno' order by b.color_order,b.size_order";
	//echo $sql_dtls;
	$sql_result =sql_select($sql_dtls);
	$k=0;
	foreach ($sql_result as $row)
	{
		$k++;
		if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
		
		$dtls_id='';//$mstDataArr[$row[csf("id")]][$row[csf("item_number_id")]][$row[csf("country_id")]];
		// $placeholderQty=$row[csf("plan_cut_qnty")]-($old_qty);
		$bal_qty = $sewing_qty_arr[$row[csf("cid")]] - $ex_data_arr[$row[csf("cid")]];
		$amt_usd = $rcv_qty_array[$row[csf("cid")]]*$row[csf("order_rate")];
		$amt_bdt = $rcv_qty_array[$row[csf("cid")]]*$row[csf("order_rate")]*$exchange_rate;
		
		$dtlsData="";
		?>
        <tr bgcolor="<? echo $bgcolor; ?>">
            <td width="30" align="center"><?= $k; ?></td>
            <td width="110" style="word-break:break-all"><?= $row[csf("po_number")]; ?></td>
            <td width="70"><?= change_date_format($row[csf("pub_shipment_date")]); ?></td>
            <td width="110" style="word-break:break-all"><?= $garments_item[$row[csf("item_number_id")]]; ?></td>
            <td width="110" style="word-break:break-all"><?= $country_arr[$row[csf("country_id")]]; ?></td>
            <td width="70"><?= change_date_format($row[csf("country_ship_date")]); ?></td>
            <td width="120" style="word-break:break-all"><?= $color_arr[$row[csf("color_number_id")]]; ?></td>
            <td width="70" style="word-break:break-all"><?= $size_arr[$row[csf("size_number_id")]]; ?></td>
            <td width="70" align="right" id="orderQty_<?= $k; ?>"><?= $row[csf("order_quantity")]; ?></td>
            <td width="70" align="right" id="sewingQty_<?= $k; ?>"><?=$sewing_qty_arr[$row[csf("cid")]]; ?></td>
            <td width="70" align="right" id="exfactQty_<?= $k; ?>" ><?=$ex_data_arr[$row[csf("cid")]]; ?></td>
            <td width="70" align="right" title="Sewing Qty - Delivery Qty"  id="balQty_<?= $k; ?>"><?=$bal_qty; ?></td>            
           
            <td width="70" align="center">
				<input type="text" name="txtQty_<?= $k; ?>" id="txtQty_<?= $k; ?>" class="text_boxes_numeric" style="width:53px;" value="<?=$rcv_qty_array[$row[csf("cid")]];?>" onBlur="fnc_total_calculate(this.value,<?= $k; ?>);" placeholder="<?=$bal_qty;?>" />
			</td>           
           

			<td width="70">
				<input type="text" disabled readonly name="orderRate_<?=$k; ?>" id="orderRate_<?=$k; ?>" value="<?= $row[csf("order_rate")]; ?>" style="width:53px;"/>
			</td>
			<td width="70" align="right">
				<input type="text" disabled readonly name="amtUSD_<?=$k; ?>" id="amtUSD_<?=$k; ?>" value="<?=$amt_usd;?>" style="width:53px;"/>
			</td>
			<td  align="right">
                <input type="text" disabled readonly name="amtBDT_<?=$k; ?>" id="amtBDT_<?=$k; ?>" value="<?=$amt_bdt;?>" style="width:53px;"/>
                <input type="hidden" name="txtpoid_<?=$k; ?>" id="txtpoid_<?=$k; ?>" value="<?=$row[csf("id")]; ?>" />
                <input type="hidden" name="txtColorSizeid_<?=$k; ?>" id="txtColorSizeid_<?=$k; ?>" value="<?=$row[csf("cid")]; ?>" />
                <input type="hidden" name="txtDtlsData_<?=$k; ?>" id="txtDtlsData_<?=$k; ?>" value="<?=$dtlsData; ?>" />
                <input type="hidden" name="txtDtlsUpId_<?=$k; ?>" id="txtDtlsUpId_<?=$k; ?>" value="<?=$dtls_id; ?>" />
                <input type="hidden" name="txtRate_<?=$k; ?>" id="txtRate_<?=$k; ?>" value="<?=$row[csf("order_rate")]; ?>" />
                <input type="hidden" name="txtItemID_<?=$k; ?>" id="txtItemID_<?=$k; ?>" value="<?=$row[csf("item_number_id")]; ?>" />
                <input type="hidden" name="txtCountryID_<?=$k; ?>" id="txtCountryID_<?=$k; ?>" value="<?=$row[csf("country_id")]; ?>" />
            </td>
        </tr>
		<?
	}
	exit();
}



if($action=="system_number_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);

	//$order_type_arr = array(1=>"Self Order",2=>"Subcontract Order");
	$goods_type_arr = array(1=>"Good GMT In Hand",2=>"Damage GMT",3=>"Leftover Sample");
	?>
	<script>
		$(document).ready(function(e) {
            //$("#txt_search_common").focus();
			load_drop_down( 'left_over_garments_receive_controller','<? echo $company; ?>', 'load_drop_down_location2', 'location_td' );
        });

		function js_set_value(id)
		{
			$("#hidden_search_data").val(id);//po id
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:950px;" >
	    <form name="searchorderfrm_1" style="width:950px;" id="searchorderfrm_1" autocomplete="off">
	        <table ellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
	            <thead>
	                <tr>
	                    <th>Company</th>
						<th>Job No</th>
	                    <th>Location</th>
	                    <th>Order Type</th>
	                    <th>Buyer</th>
	                    <th>Store Name</th>
						<th>Int ref</th>
	                    <th>System No</th>
	                    <th width="150">Date Range</th>
	                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
	                </tr>
	            </thead>
	            <tbody>
	                <tr>
	                    <td>
	                    <?
	                    echo create_drop_down( "cbo_company_name", 100, "select company_name,id from lib_company comp where is_deleted=0  and status_active=1 order by company_name",'id,company_name', 1, '--- Select Company ---',$company, "load_drop_down( 'left_over_garments_receive_controller', this.value, 'load_drop_down_location2', 'location_td' );");?>
	                    </td>
						<td>
						<input type="text" class="text_boxes" name="txt_job_no" id="txt_job_no" style="width:50px">
							

						</td>
	                    <td id="location_td">
	                    <?
	                    echo create_drop_down( "cbo_location_name", 100, $blank_array,'', 1, '--- Select Location ---', $selected, "",0,0 );
	                    ?>
	                    </td>
	                    <td>
	                    <?
	                    echo create_drop_down( "cbo_order_type", 100, $order_source, "", 1, "-- Select --", $selected, "", "", "1,2", "", "");
	                    ?>
	                    </td>
	                    <td>
	                    <?
	                    echo create_drop_down( "cbo_buyer_name", 100, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","",0 );
	                    ?>
	                    </td>
	                    <td id="store_name_td">

	                    <?
	                    echo create_drop_down( "cbo_store_name", 100, "select id,store_name from lib_store_location", "id,store_name", 1, "-- Select Store --", $selected,"",0,0);
	                    ?>
	                    </td>
						<td>
						<input type="text" class="text_boxes" name="txt_internal_ref" id="txt_internal_ref" style="width:50px">

						</td>
						
	                    <td>
	                    <input type="text" style="width:50px" class="text_boxes"  name="txt_system_no" id="txt_system_no" />
	                    </td>
	                    <td align="center">
	                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:45px"> To
	                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:45px">
	                    </td>
	                    <td align="center">

	                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_order_type').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_store_name').value+'_'+document.getElementById('txt_system_no').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_internal_ref').value, 'create_system_number_list_view', 'search_div', 'leftover_gmts_receive_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:80px;" />
	                    </td>
	                </tr>
	            </tbody>
	            <tfoot>
	                <tr>
	                    <td align="center" valign="middle" colspan="10" style="background-image: -moz-linear-gradient(bottom, rgb(136,170,214) 7%, rgb(194,220,255) 10%, rgb(136,170,214) 96%);">
	                    <? echo load_month_buttons(1);  ?>
	                    <input type="hidden" id="hidden_search_data">
	                    </td>
	                </tr>
	            </tfoot>
	        </table>
	        <div style="margin-top:10px" id="search_div"></div>
	    </form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_system_number_list_view")
{
 	$ex_data = explode("_",$data);

    $company = $ex_data[0];
    $location = $ex_data[1];
    $txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
    $order_type = $ex_data[4];
    $buyer_id = $ex_data[5];
    $store_id = $ex_data[6];
    // $floor_id = $ex_data[7];
	$system_no = $ex_data[7];
	$job_no  =$ex_data[8];
	$internal_ref =$ex_data[9];

	$goods_type_arr = array(1=>"Good GMT In Hand",2=>"Damage GMT",3=>"Leftover Sample");
	$order_type_arr = array(1=>"Self Order",2=>"Subcontract Order");
	$buyer_arr=return_library_array( "SELECT id, buyer_name from lib_buyer",'id','buyer_name');
	$garments_item=return_library_array("SELECT id,item_name from  lib_garment_item", 'id', 'item_name');
	$location_name=return_library_array("SELECT id,location_name from lib_location where status_active=1 and is_deleted=0", 'id', 'location_name');
	$country_arr=return_library_array( "SELECT id, country_name from lib_country",'id','country_name');
	$store_name_arr=return_library_array( "SELECT id,store_name from lib_store_location",'id','store_name');
	$floor_name_arr=return_library_array( "SELECT id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0",'id','floor_name');
	$sql_cond="";


	if($db_type==0)
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$sql_cond .= " and a.leftover_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else if($txt_date_from=="" && $txt_date_to!=""){
			$sql_cond .= " and a.leftover_date <= '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else if($txt_date_from!="" && $txt_date_to==""){
			$sql_cond .= " and a.leftover_date >= '".change_date_format($txt_date_from,'yyyy-mm-dd')."'";
		}
	}
	if($db_type==2 || $db_type==1)
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$sql_cond .= " and a.leftover_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
		else if($txt_date_from=="" && $txt_date_to!=""){
			$sql_cond .= " and a.leftover_date <=  '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
		else if($txt_date_from!="" && $txt_date_to==""){
			$sql_cond .= " and a.leftover_date >= '".date("j-M-Y",strtotime($txt_date_from))."'";
		}

	}

	if(trim($system_no)!="")
	{
		$sql_cond = " and a.sys_number like '%".trim($system_no)."'";
	}
	if(trim($company)!='0')
	{
		$sql_cond .= " and a.company_id='$company'";
	}
	if(trim($location)!='0')
	{
		$sql_cond .= " and a.location='$location'";
	}

	if(trim($order_type)!='0')
	{
		$sql_cond .= " and a.order_type='$order_type'";
	}
	if(trim($buyer_id)!=0)
	{
		$sql_cond .= " and a.buyer_name='$buyer_id'";
	}
	if(trim($store_id)!='0')
	{
		$sql_cond .= " and a.store_name='$store_id'";
	}

	if(trim($job_no) != '')
	{
		$sql_cond .= " and b.job_no like '%$job_no%'";
	}
	if(trim($internal_ref) != '')
	{
		$sql_cond .= " and c.grouping='$internal_ref'";
	}
	

	$sql = "SELECT a.id, a.sys_number, a.company_id, a.location, a.leftover_date, a.order_type, a.buyer_name, a.store_name, a.floor_id, a.goods_type,b.job_no, b.total_left_over_receive, c.grouping AS internal_ref,b.leftover_source, a.WORKING_COMPANY_ID, 
	a.WORKING_LOCATION_ID, a.WORKING_FLOOR_ID,a.exchange_rate,b.JOB_NO,b.STYLE_REF_NO,b.CATEGORY_ID,b.REMARKS from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b,wo_po_break_down c where a.id=b.mst_id and c.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and a.entry_form=719  $sql_cond order by a.id desc ";
	// echo $sql; die;
	$res = sql_select($sql);
	// $sql ="SELECT a.id, a.sys_number, a.company_id, a.location, a.leftover_date, a.order_type, a.buyer_name, a.store_name, a.floor_id, a.goods_type  from pro_leftover_gmts_rcv_mst a where  a.status_active=1 and a.is_deleted=0 $sql_cond order by a.id";
	//  echo $sql;
	$master_data_arr=array();
	foreach($res as $val)
	{
		$master_data_arr[$val[csf('id')]]['sys_number']=$val['SYS_NUMBER'];
		$master_data_arr[$val[csf('id')]]['company_id']=$val['COMPANY_ID'];
		$master_data_arr[$val[csf('id')]]['location']=$val['LOCATION'];
		$master_data_arr[$val[csf('id')]]['source']=$val['LEFTOVER_SOURCE'];
		$master_data_arr[$val[csf('id')]]['working_company_id']=$val['WORKING_COMPANY_ID'];
		$master_data_arr[$val[csf('id')]]['working_location_id']=$val['WORKING_LOCATION_ID'];
		$master_data_arr[$val[csf('id')]]['working_floor_id']=$val['WORKING_FLOOR_ID'];
		$master_data_arr[$val[csf('id')]]['exchange_rate']=$val['EXCHANGE_RATE'];
		$master_data_arr[$val[csf('id')]]['leftover_date']=change_date_format($val['LEFTOVER_DATE']);
		$master_data_arr[$val[csf('id')]]['received_qty']=$val['TOTAL_LEFT_OVER_RECEIVE'];
		$master_data_arr[$val[csf('id')]]['order_type']=$val['ORDER_TYPE'];
		$master_data_arr[$val[csf('id')]]['buyer_name']=$val['BUYER_NAME'];
		$master_data_arr[$val[csf('id')]]['store_name']=$val['STORE_NAME'];
		$master_data_arr[$val[csf('id')]]['floor_id']=$val['FLOOR_ID'];
		$master_data_arr[$val[csf('id')]]['goods_type']=$val['GOODS_TYPE'];
		$master_data_arr[$val[csf('id')]]['job_no']=$val['JOB_NO'];
		$master_data_arr[$val[csf('id')]]['internal_ref']=$val['INTERNAL_REF'];
		$master_data_arr[$val[csf('id')]]['style_ref_no']=$val['STYLE_REF_NO'];
		$master_data_arr[$val[csf('id')]]['category_id']=$val['CATEGORY_ID'];
		$master_data_arr[$val[csf('id')]]['remarks']=$val['REMARKS'];
	}
 	// echo "<pre>";	print_r($master_data_arr);die();
	?>
	<div style="width:940px;margin:0 auto">
		<table  class="rpt_table" id="rpt_table_list_view" rules="all" width="920" height="" cellspacing="0" cellpadding="0" border="0" align="left">
			<thead>
				<tr align="left" width="200">
					<th  width="50">SL No</th>
					<th width="80">Job No</th>
					<th width="120">System Number</th>
					<th width="80">Receive Date</th>
					<th width="110">Internal Ref</th>
					<th width="100">Order Type</th>
					<th width="100">Buyer</th>
					<th width="120">Goods Type</th>
					<th width="80">Store Name</th>
					<th width="80">Floor</th>
					<th width="80">Received Qty</th>
				</tr>
			</thead>
		</table>
		<div style="max-height:220px; width:940px; overflow-y:scroll" id="">
			<table  class="rpt_table" id="list_view" rules="all" width="920" height="" cellspacing="0" cellpadding="0"
				border="0" align="left">
				<tbody>
					<? $i=1;
					foreach($master_data_arr as $key=>$row)
					{
						$set_value = $key."_".$row['sys_number']."_".$row['company_id']."_".$row['location']."_".$row['source']."_".$row['working_company_id']."_".$row['working_location_id']."_".$row['working_floor_id']."_".$row['job_no']."_".$row['leftover_date']."_".$row['order_type']."_".$row['goods_type']."_".$row['style_ref_no']."_".$row['buyer_name']."_".$row['exchange_rate']."_".$row['category_id']."_".$row['remarks'];
						?>
						
						<tr align="right" onClick="js_set_value('<?=$set_value;?>')" style="cursor:pointer" 
							bgcolor="#FFFFFF">
							<td align="left" width="50"><?=$i;?></td>
							<td align="left"width="80"><? echo $row['job_no'];?></td>
							<td align="left" width="120"><? echo $row['sys_number']; ?></td>
							<td align="left" width="80"><? echo $row['leftover_date'];?></td>
							<td align="left" width="110"><? echo $row['internal_ref']; ?></td>
							<td align="left" width="100"><? echo $order_type_arr[$row['order_type']];?></td>
							<td align="left" width="100"><? echo $buyer_arr[$row['buyer_name']];?></td>
							<td align="left" width="120"><? echo $goods_type_arr[$row['goods_type']];?></td>
							<td align="left" width="80"><? echo $store_name_arr[$row['store_name']];?></td>
							<td align="left" width="80"><? echo $floor_name_arr[$row['floor_id']];?></td>
							<td align="right" width="80"><? echo $row['received_qty'];?></td>
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
    

	/* $sql ="SELECT a.id, a.sys_number, a.company_id, a.location, a.leftover_date, a.order_type, a.buyer_name, a.store_name, a.floor_id, a.goods_type  from pro_leftover_gmts_rcv_mst a where  a.status_active=1 and a.is_deleted=0 $sql_cond order by a.id";

	$arr=array(2=>$order_source,3=>$buyer_arr,4=>$store_name_arr,5=>$floor_name_arr);

	echo create_list_view("list_view", "System Number,Receive Date,Order Type,Buyer,Store Name,Floor","120,80,100,100,100,80","700","240",0, $sql , "js_set_value","id,sys_number,company_id", "",1, "0,0,order_type,buyer_name,store_name,floor_id", $arr,"sys_number,leftover_date,order_type,buyer_name,store_name,floor_id", "","setFilterGrid('list_view',-1)","0,3,0,0,0,0") ; */

	exit();
}

//pro_garments_production_mst
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	//$is_control=return_field_value("is_control","variable_settings_production","company_name=$cbo_company_name and variable_list=33 and page_category_id=28","is_control");
	// echo "10**";die;
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		if (str_replace("'","",$txt_update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')"; else $year_cond="";	
			$new_sys_number = explode("*", return_next_id_by_sequence("", "PRO_LEFTOVER_GMTS_RCV_MST",$con,1,$cbo_company_name,'LGR',719,date("Y",time()),0,0,0,0,0 ));
			
			$field_array_mst="id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, location, leftover_date, order_type, buyer_name, store_name, exchange_rate, goods_type,working_company_id,working_location_id,working_floor_id,remarks,entry_form, inserted_by, insert_date";
			$id=return_next_id("id", "pro_leftover_gmts_rcv_mst", 1);
				
			$data_array_mst="(".$id.",'".$new_sys_number[1]."','".(int)$new_sys_number[2]."','".$new_sys_number[0]."',".$cbo_company_name.",".$cbo_location.",".$txt_iron_date.",".$cbo_order_type.",".$cbo_buyer_name.",'',".$exchange_rate.",".$cbo_goods_type.",".$cbo_iron_company.",".$cbo_iron_location.",".$cbo_floor.",".$txt_remark.",719,".$user_id.",'".$pc_date_time."')";
		}
		
		$mstArr=array(); $dtlsArr=array();
		for($j=1;$j<=$tot_row;$j++)
		{
			$txtQty 			="txtQty_".$j;
			
			$txtpoid 			="txtpoid_".$j;
			$txtDtlsData	 	="txtDtlsData_".$j;
			$txtColorSizeid 	="txtColorSizeid_".$j;
			$txtDtlsUpId 		="txtDtlsUpId_".$j;
			$txtRate 			="txtRate_".$j;
			$txtItemID 			="txtItemID_".$j;
			$txtCountryID		="txtCountryID_".$j;

			$issueQty=str_replace("'","",$$txtQty);
			$po_id=str_replace("'","",$$txtpoid);
			$colorSizeid=str_replace("'","",$$txtColorSizeid);
			$gmts_item=str_replace("'","",$$txtItemID);
			$country_id=str_replace("'","",$$txtCountryID);			
			$fobRate=str_replace("'","",$$txtRate);			
			
			$mstArr[$po_id][$gmts_item][$country_id]['qc']+=$issueQty;			
			$mstArr[$po_id][$gmts_item][$country_id]['fobRate']=$fobRate;			
			
			$dtlsArr[$colorSizeid]['qc']+=$issueQty;
			$colorSizeArr[$colorSizeid] =$po_id."**".$gmts_item."**".$country_id;
		}
		
		if($db_type==2) 
		{
			$txt_reporting_hour=str_replace("'","",$txt_iron_date)." ".str_replace("'","",$txt_reporting_hour);
			$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
		}
		
		$dtls_id=return_next_id("id", "pro_leftover_gmts_rcv_dtls", 1);
		$field_array_dtls="id, mst_id, po_break_down_id, item_number_id, country_id, total_left_over_receive, currency_id, fob_rate, leftover_amount, bdt_amount, sewing_production_variable,leftover_source,remarks,category_id,job_no,style_ref_no, inserted_by, insert_date";
				
		$data_array_dtls = "";
		foreach($mstArr as $orderId=>$orderData)
		{
			foreach($orderData as $gmtsItemId=>$gmtsItemIdData)
			{
				foreach($gmtsItemIdData as $countryId=>$qtyData)
				{					
					$amt_usd = $qtyData['qc']*$qtyData['fobRate'];
					$amt_bdt = $qtyData['qc']*$qtyData['fobRate']*$exchange_rate;
					if($db_type==2)
					{
						$data_array_dtls.=" INTO pro_leftover_gmts_rcv_dtls (".$field_array_dtls.") VALUES(".$dtls_id.",".$id.",".$orderId.",".$gmtsItemId.",".$countryId.",".$qtyData['qc'].",2,'".$qtyData['fobRate']."',".$amt_usd.",".$amt_bdt.",3,".$cbo_source.",".$txt_remark.",".$cbo_category_id.",".$txt_job_no.",".$txt_style_ref.",".$user_id.",'".$pc_date_time."')";
					}
					else
					{
						if($data_array_dtls!="") $data_array_dtls.=",";
						$data_array_dtls.="(".$dtls_id.",".$id.",".$orderId.",".$gmtsItemId.",".$countryId.",".$qtyData['qc'].",2,'".$qtyData['fobRate']."',".$amt_usd.",".$amt_bdt.",3,".$cbo_source.",".$txt_remark.",".$cbo_category_id.",".$txt_job_no.",".$txt_style_ref.",".$user_id.",'".$pc_date_time."')";
					}
					$mstIdArr[$orderId][$gmtsItemId][$countryId]=$dtls_id;
					$dtls_id +=1;
				}
			}
		}
		
		$field_array_color_size="id, mst_id, dtls_id, color_size_break_down_id, production_qnty";
		$col_size_id=return_next_id("id", "pro_leftover_gmts_rcv_clr_sz", 1);
		$data_array_color_size="";
		foreach($dtlsArr as $colorSizeid=>$qtyStr)
		{
			
			$colorSizedData 		=explode("**",$colorSizeArr[$colorSizeid]);
			$dtlsId 				=$mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
			if($data_array_color_size!="") $data_array_color_size.=",";

			$data_array_color_size.= "(".$col_size_id.",".$id.",".$dtlsId.",'".$colorSizeid."','".$qtyStr['qc']."')"; 
			$col_size_id++;
		}

		$flag=1;
		if (str_replace("'","",$txt_update_id)=="")
		{
			$rID_mst=sql_insert("pro_leftover_gmts_rcv_mst",$field_array_mst,$data_array_mst,1);
			if($rID_mst==1 && $flag==1) $flag=1; else $flag=0;
		}
		// echo "10**insert into pro_leftover_gmts_rcv_mst($field_array_mst)values".$data_array_mst;die;
		if($db_type==2)
		{
			$query="INSERT ALL".$data_array_dtls." SELECT * FROM dual";
			$rID=execute_query($query);
		}
		else
		{
			$rID=sql_insert("pro_leftover_gmts_rcv_dtls",$field_array_dtls,$data_array_dtls,1);
		}
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$dtlsrID=sql_insert("pro_leftover_gmts_rcv_clr_sz",$field_array_color_size,$data_array_color_size,1);
		if($dtlsrID==1 && $flag==1) $flag=1; else $flag=0;
		
		// echo "10**insert into pro_leftover_gmts_rcv_clr_sz($field_array_color_size)values".$data_array_color_size;die;
		// echo "10**".$rID_mst."**".$rID."**".$dtlsrID."**".$flag;die;

		if($db_type==0)
		{  
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**".str_replace("'","",$new_sys_number[0]);
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
				echo "0**".$id."**".str_replace("'","",$new_sys_number[0]);
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
  	else if ($operation==1) // update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$txt_update_id = str_replace("'","",$txt_update_id);
					
		$field_array_mst_update="working_company_id*working_location_id*working_floor_id*remarks*updated_by*update_date";
		$data_array_mst_update="".$cbo_iron_company."*".$cbo_iron_location."*".$cbo_floor."*".$txt_remark."*".$user_id."*'".$pc_date_time."'";

		$field_array_dtls_update="total_left_over_receive*remarks*category_id*updated_by*update_date";
		
		$data_array_dtls_update="".$txt_total_left_over_receive."*".$txt_leftover_amount."*".$txt_bdt_amount."*".$cbo_room_no."*".$cbo_rack_no."*".$cbo_shelf_no."*".$cbo_bin_no."*".$txt_remark2."*".$cbo_category_id."*".$user_id."*'".$pc_date_time."'";
		
		
		$mstArr=array(); $dtlsArr=array();
		for($j=1;$j<=$tot_row;$j++)
		{
			$txtQty 			="txtQty_".$j;
			
			$txtpoid 			="txtpoid_".$j;
			$txtDtlsData	 	="txtDtlsData_".$j;
			$txtColorSizeid 	="txtColorSizeid_".$j;
			$txtDtlsUpId 		="txtDtlsUpId_".$j;
			$txtRate 			="txtRate_".$j;
			$txtItemID 			="txtItemID_".$j;
			$txtCountryID		="txtCountryID_".$j;

			$issueQty=str_replace("'","",$$txtQty);
			$po_id=str_replace("'","",$$txtpoid);
			$colorSizeid=str_replace("'","",$$txtColorSizeid);
			$gmts_item=str_replace("'","",$$txtItemID);
			$country_id=str_replace("'","",$$txtCountryID);			
			$fobRate=str_replace("'","",$$txtRate);			
			
			$mstArr[$po_id][$gmts_item][$country_id]['qc']+=$issueQty;			
			$mstArr[$po_id][$gmts_item][$country_id]['fobRate']=$fobRate;			
			
			$dtlsArr[$colorSizeid]['qc']+=$issueQty;
			$colorSizeArr[$colorSizeid] =$po_id."**".$gmts_item."**".$country_id;
		}
		
		if($db_type==2) 
		{
			$txt_reporting_hour=str_replace("'","",$txt_iron_date)." ".str_replace("'","",$txt_reporting_hour);
			$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
		}
		
		$dtls_id=return_next_id("id", "pro_leftover_gmts_rcv_dtls", 1);
		$field_array_dtls="id, mst_id, po_break_down_id, item_number_id, country_id, total_left_over_receive, currency_id, fob_rate, leftover_amount, bdt_amount, sewing_production_variable,leftover_source,remarks,category_id,job_no,style_ref_no, inserted_by, insert_date";
				
		$data_array_dtls = "";
		foreach($mstArr as $orderId=>$orderData)
		{
			foreach($orderData as $gmtsItemId=>$gmtsItemIdData)
			{
				foreach($gmtsItemIdData as $countryId=>$qtyData)
				{					
					$amt_usd = $qtyData['qc']*$qtyData['fobRate'];
					$amt_bdt = $qtyData['qc']*$qtyData['fobRate']*$exchange_rate;
					if($db_type==2)
					{
						$data_array_dtls.=" INTO pro_leftover_gmts_rcv_dtls (".$field_array_dtls.") VALUES(".$dtls_id.",".$txt_update_id.",".$orderId.",".$gmtsItemId.",".$countryId.",".$qtyData['qc'].",2,'".$qtyData['fobRate']."',".$amt_usd.",".$amt_bdt.",3,".$cbo_source.",".$txt_remark.",".$cbo_category_id.",".$txt_job_no.",".$txt_style_ref.",".$user_id.",'".$pc_date_time."')";
					}
					else
					{
						if($data_array_dtls!="") $data_array_dtls.=",";
						$data_array_dtls.="(".$dtls_id.",".$txt_update_id.",".$orderId.",".$gmtsItemId.",".$countryId.",".$qtyData['qc'].",2,'".$qtyData['fobRate']."',".$amt_usd.",".$amt_bdt.",3,".$cbo_source.",".$txt_remark.",".$cbo_category_id.",".$txt_job_no.",".$txt_style_ref.",".$user_id.",'".$pc_date_time."')";
					}
					$mstIdArr[$orderId][$gmtsItemId][$countryId]=$dtls_id;
					$dtls_id +=1;
				}
			}
		}
		
		$field_array_color_size="id, mst_id, dtls_id, color_size_break_down_id, production_qnty";
		$col_size_id=return_next_id("id", "pro_leftover_gmts_rcv_clr_sz", 1);
		$data_array_color_size="";
		foreach($dtlsArr as $colorSizeid=>$qtyStr)
		{
			$colorSizedData 		=explode("**",$colorSizeArr[$colorSizeid]);
			$dtlsId 				=$mstIdArr[$colorSizedData[0]][$colorSizedData[1]][$colorSizedData[2]];
			if($data_array_color_size!="") $data_array_color_size.=",";			

			$data_array_color_size.= "(".$col_size_id.",".$txt_update_id.",".$dtlsId.",'".$colorSizeid."','".$qtyStr['qc']."')"; 
		}

		$flag=1;
		if (str_replace("'","",$txt_update_id)!="")
		{
			$rID=sql_update("pro_leftover_gmts_rcv_mst",$field_array_mst_update,$data_array_mst_update,"id","".$txt_update_id."",1);
			$rID1=sql_update("pro_leftover_gmts_rcv_dtls",$field_array_dtls_update,$data_array_dtls_update,"id","".$hidden_dtls_id."",1);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
		}
		$dtlsrDelete = execute_query("DELETE from pro_leftover_gmts_rcv_clr_sz where mst_id=$txt_update_id",1);
		// echo "10**insert into pro_leftover_gmts_rcv_mst($field_array_mst)values".$data_array_mst;die;
		if($db_type==2)
		{
			$query="INSERT ALL".$data_array_dtls." SELECT * FROM dual";
			$rID=execute_query($query);
		}
		else
		{
			$rID=sql_insert("pro_leftover_gmts_rcv_dtls",$field_array_dtls,$data_array_dtls,1);
		}
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$dtlsrID=sql_insert("pro_leftover_gmts_rcv_clr_sz",$field_array_color_size,$data_array_color_size,1);
		if($dtlsrID==1 && $flag==1) $flag=1; else $flag=0;
		
		//echo "10**insert into pro_gmts_delivery_mst($field_array_delivery)values".$data_array_delivery;die;
		// echo "10**".$rID_mst."**".$rID."**".$dtlsrID."**".$flag;die;

		if($db_type==0)
		{  
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**".str_replace("'","",$new_sys_number[0]);
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
				echo "0**".$id."**".str_replace("'","",$new_sys_number[0]);
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
?>
<script type="text/javascript">
	function getActionOnEnter(event){
			if (event.keyCode == 13){
				document.getElementById('btn_show').click();
			}

	}
</script>

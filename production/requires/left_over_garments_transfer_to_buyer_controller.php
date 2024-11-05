<?
session_start();
include('../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//************************************ Start *************************************************

if ($action=="load_variable_settings")
{
	echo "$('#sewing_production_variable').val(0);\n";
	$sql_result = sql_select("select smv_source,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
 	foreach($sql_result as $result)
	{
		echo "$('#sewing_production_variable').val(".$result[csf("smv_source")].");\n";
		echo "$('#styleOrOrderWisw').val(".$result[csf("production_entry")].");\n";
	}
 	exit();
}
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );

// Transfer Popup
if ($action=="orderToorderTransfer_popup")
{
	echo load_html_head_contents("Order To Order Transfer Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data)
		{
			$('#transfer_data').val(data);
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:680px;">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:650px;margin-left:10px">
	        <legend>Enter search words</legend>
	            <table cellpadding="0" cellspacing="0" width="650" class="rpt_table" border="1" rules="all">
	                <thead>
	                    <th>Search By</th>
	                    <th width="240" id="search_by_td_up">Please Enter Transfer ID</th>
						<th colspan="2">Date Range</th>
	                    <th>

	                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
	                        <input type="hidden" name="transfer_data" id="transfer_data" class="text_boxes" value="">
	                    </th>
	                </thead>
	                <tr class="general">
	                    <td>
							<?
								$search_by_arr=array(1=>"Transfer ID",2=>"Challan No.",3=>"Job No.",4=>"Style Ref.",5=>"Order No");
								$dd="change_search_event(this.value, '0*0*0*0*0', '0*0*0*0*0', '../../../') ";
								echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
	                        ?>
	                    </td>
	                    <td id="search_by_td">
	                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
	                    </td>
						<td align="center">
                            <input type="text" style="width:80px;" class="datepicker"  name="txt_date_from" id="txt_date_from" readonly />
                        </td>
                        <td align="center">
                            <input type="text" style="width:80px;" class="datepicker"  name="txt_date_to" id="txt_date_to" readonly />
	                    <td>
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_transfer_search_list_view', 'search_div', 'left_over_garments_transfer_to_buyer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
	                    </td>
	                </tr>
	            </table>
				<tr>
                    <td  align="center"  valign="middle">
                        <?=load_month_buttons(1);?>
                    </td>
                </tr>
	        	<div style="margin-top:10px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
if ($action == "load_drop_down_del_location")
{
	echo create_drop_down("cbo_delivery_location", 130, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select Working Location --", $selected, "load_drop_down( 'requires/left_over_garments_transfer_to_buyer_controller', $data+'**'+this.value, 'load_drop_down_del_floor', 'del_floor_td' );");
	exit();
}
if ($action == "load_drop_down_del_floor")
{
	$data = explode('**', $data);
	echo create_drop_down("cbo_delivery_floor", 130, "select id,floor_name from lib_prod_floor where company_id='$data[0]' and location_id='$data[1]' and status_active =1 and is_deleted=0 and production_process=11 order by floor_name", "id,floor_name", 1, "-- Select Floor --", $selected, "");
	exit();
}

if ($action == "load_drop_down_store")
{
	$data = explode("_", $data);

	$sql_store = "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and
	a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0  group by a.id, a.store_name order by a.store_name";
	// echo $sql_store;die;
	/*$store_id = sql_select($sql_store);
	$selected_store = "";
	if (count($store_id) == 1) $selected_store = $store_id[0][csf('id')];*/
	// echo create_drop_down("cbo_store_name", 152, $sql_store, "id,store_name", 1, "--Select store--", 0, ";reset_room_rack_shelf('','cbo_store_name');");
	echo create_drop_down("cbo_store_name", 152, $sql_store, "id,store_name", 1, "-- Select store --", $selected, "");
	exit();
}


// Create deisgn View
if($action=='create_transfer_search_list_view')
{
	$data = explode("_",$data);

	// echo $data;

	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$txt_date_from=$data[3];
	$txt_date_to=$data[4];
	//echo $txt_date_to;

	$search_field="";
	if($search_by==1)
	{
		$search_field="a.sys_number";
	}else if($search_by==2)
	{
		$search_field="a.challan_no";

	}else if($search_by==3)
	{
		$search_field="c.job_no_prefix_num";

	}else if($search_by==4)
	{
		$search_field="c.style_ref_no";

	}else if($search_by==5)
	{
		$search_field="d.po_number";

	}



	if($db_type==0)
	{
		$year_field="YEAR(a.insert_date) as year";
		$delivery_date="DATE_FORMAT(a.delivery_date, '%d-%m-%Y')";
	}
	else if($db_type==2)
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
		$delivery_date="to_char(a.delivery_date,'DD-MM-YYYY')";
	}
	 else $year_field="";//defined Later

	 $date_con="";
	 if($txt_date_from!='' && $txt_date_to!=''){

            $txt_date_from=change_date_format($txt_date_from,'','',-1);
            $txt_date_to=change_date_format($txt_date_to,'','',-1);

        $date_con=" and a.delivery_date BETWEEN '$txt_date_from' and '$txt_date_to'";
    }
    else
    {
        $date_con="";
    }

 	$sql="SELECT a.id, a.sys_number_prefix_num, $year_field, a.sys_number, a.challan_no, a.company_id, c.job_no_prefix_num,c.style_ref_no,d.po_number, a. delivery_date,a.transfer_criteria,a.store_id,a.working_company_id,a.working_location_id,a.working_floor_id,a.purpose_id from pro_gmts_delivery_mst a,pro_garments_production_mst b,wo_po_details_master c,wo_po_break_down d  where
	a.id=b.delivery_mst_id and d.id=b.po_break_down_id and c.id=d.job_id  and a.production_type=17 and a.company_id=$company_id and $search_field like '$search_string' $date_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 order by a.id desc";
	//  echo $sql; die;
	$sql_result=sql_select($sql);
	$data_array=array();
	foreach($sql_result as $v)
	{  $data_array[$v['ID']]['sys_number']=$v['SYS_NUMBER'];
		$data_array[$v['ID']]['sys_number_prefix_num']=$v['SYS_NUMBER_PREFIX_NUM'];
		$data_array[$v['ID']]['year']=$v['YEAR'];
		$data_array[$v['ID']]['challan_no']=$v['CHALLAN_NO'];
		$data_array[$v['ID']]['company_id']=$v['COMPANY_ID'];
		$data_array[$v['ID']]['delivery_date']=$v['DELIVERY_DATE'];
		$data_array[$v['ID']]['transfer_criteria']=$v['TRANSFER_CRITERIA'];
		$data_array[$v['ID']]['store_id']=$v['STORE_ID'];
		$data_array[$v['ID']]['working_company_id']=$v['WORKING_COMPANY_ID'];
		$data_array[$v['ID']]['working_location_id']=$v['WORKING_LOCATION_ID'];
		$data_array[$v['ID']]['working_floor_id']=$v['WORKING_FLOOR_ID'];
		$data_array[$v['ID']]['purpose_id']=$v['PURPOSE_ID'];

	}
	// echo "<pre>";print_r($data_array);die;
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	?>
	<div style="width:500px;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="500" width="100%" class="rpt_table">
            <thead>
				<th width="30">SL</th>
                <th width="100">Transfer ID</th>
                <th width="70">YEAR</th>
                <th width="100">Challan No</th>
                <th width="100">Company</th>
				<th width="100">Transfer Date</th>

            </thead>
     	</table>
     </div>
	 <div style="width:500px; max-height:240px;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="500" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $data_array as $sys_id=>$row )
            {

						if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $sys_id;?>_<? echo $row["sys_number"];?>_<? echo $row["delivery_date"];?>_<? echo $row["challan_no"];?>_<? echo $row["transfer_criteria"];?>_<? echo $row["store_id"];?>_<? echo $row["purpose_id"];?>');" >
								<td width="30" align="center"><?php echo $i; ?></td>
								<td width="100" align="center"><?=$row['sys_number_prefix_num']; ?></td>
								<td width="70"><?=$row['year']?></td>
								<td width="100"><?=$row['challan_no']?></td>
								<td width="100"><?=$company_arr[$row['company_id']]?></td>
        						<td width="100"><?=$row['delivery_date']?></td>

							</tr>
						<?
						$i++;
					}
			?>
        </table>
    </div>
	 <?




	// echo create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date", "80,70,100,110","550","250",0, $sql, "js_set_value", "id,sys_number,delivery_date,challan_no,transfer_criteria", "", 1, "0,0,0,company_id,0", $arr, "sys_number_prefix_num,year,challan_no,company_id,delivery_date", '','','0,0,0,0,3');

	// exit();
}


if ($action=="order_popup")
{
	echo load_html_head_contents("Order Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo "<pre>";print_r($_REQUEST);
	?>
	<script>
		function js_set_value(data)
		{
			$('#order_data').val(data);
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:980px;">
		<form name="searchdescfrm" id="searchdescfrm">
			<fieldset style="width:970px;margin-left:3px">
	        <legend>Enter search words</legend>
	            <table cellpadding="0" cellspacing="0" width="800" class="rpt_table" border="1" rules="all">
	                <thead>
	                    <th>Buyer Name</th>
	                    <th width="130">Search By</th>
	                    <th width="180" align="center" id="search_by_td_up">Enter Order Number</th>
	                    <th width="230">Shipment Date Range</th>
	                    <th>
	                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
	                        <input type="hidden" name="order_data" id="order_data" class="text_boxes" value="">
	                    </th>
	                </thead>
	                <tr class="general">
	                    <td>
							<?
								echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
							?>
	                    </td>
	                    <td align="center">
	                    	<?
								$searchby_arr=array(1=>"Order No",2=>"Style Ref.",3=>"Job No",4=>"File No",5=>" Internal Ref.");
								$dd="change_search_event(this.value, '0*0*0*0*0', '0*0*0*0*0', '../../../') ";
								echo create_drop_down( "cbo_search_by", 130, $searchby_arr,"",0, "--Select--", 1,$dd,0 );
							?>
	                    </td>
	                    <td id="search_by_td">
	                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
	                    </td>
	                    <td>
	                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly>
	                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" readonly>
	                    </td>
	                    <td>
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $type; ?>', 'create_po_search_list_view', 'search_div', 'left_over_garments_transfer_to_buyer_controller', 'setFilterGrid(\'tbl_po_list\',-1);')" style="width:100px;" />
	                    </td>
	                </tr>
	                <tr>
	                    <td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
	                </tr>
	            </table>
	        	<div style="margin-top:10px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="show_dtls_listview")
{
	$data_ex = explode("**",$data);
	$delivery_mst_id = $data_ex[0];


	$po_arr=return_library_array( "SELECT b.id, b.po_number from pro_garments_production_mst a, wo_po_break_down b where a.po_break_down_id=b.id and a.delivery_mst_id=$delivery_mst_id and a.production_type=17 group by b.id, b.po_number",'id','po_number');

	$req_arr=return_library_array( "SELECT b.id, b.requisition_number from pro_garments_production_mst a, SAMPLE_DEVELOPMENT_MST b where a.po_break_down_id=b.id and a.delivery_mst_id=$delivery_mst_id and a.production_type=17 group by b.id, b.requisition_number",'id','requisition_number');

	?>
	<div style="width:930px">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="40">SL</th>

                <th width="120" align="center">From Order No</th>
                <th width="120" align="center">To Order No</th>
                <th width="150" align="center">Item Name</th>
                <th width="110" align="center">Country</th>
                <th width="110" align="center">Leftover Qnty</th>
            </thead>
    	</table>
    </div>
	<div style="width:930px;max-height:180px; overflow:y-scroll" id="sewing_production_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_list_search">
		<?php
			$i=1;
			$total_production_qnty=0;
			$sqlResult =sql_select("SELECT id, from_po_id, to_po_id, item_number_id, country_id, production_quantity from pro_gmts_delivery_dtls where mst_id=$delivery_mst_id and production_type=17 and status_active=1 and is_deleted=0 order by id");
 			foreach($sqlResult as $selectResult)
			{
 				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				 $_action = "populate_transfer_form_data";

 			?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'<?=$_action;?>','requires/left_over_garments_transfer_to_buyer_controller');" >
                    <td width="40"><? echo $i; ?></td>
                    <td width="120" ><p><?php echo $po_arr[$selectResult[csf('from_po_id')]]; ?>&nbsp;</p></td>
                    <td width="120" ><p><?php echo $po_arr[$selectResult[csf('to_po_id')]]; ?>&nbsp;</p></td>
                    <td width="150"><p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p></td>
                    <td width="110"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?>&nbsp;</p></td>
                    <td width="110" align="right"><p><?php echo $selectResult[csf('production_quantity')]; ?></p></td>

                </tr>
			<?php
			$i++;
			}
			?>
		</table>
	</div>
	<?
	exit();
}

if($action=="create_po_search_list_view")
{
 	$data=explode('_',$data);
	// print_r($data);

	if($data[0]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name='".$data[0]."'";
		$buyer_id_cond1=" and c.buyer_name='".$data[0]."'";


	}

	$search_by=$data[1];
	$txt_search_common=trim($data[2]);
	$company_id=$data[3];

	if ($data[4]!="" &&  $data[5]!="")
	{
		if($db_type==0)
		{
			$shipment_date = "and b.pub_shipment_date between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$shipment_date = "and b.pub_shipment_date between '".change_date_format($data[4],'','',1)."' and '".change_date_format($data[5],'','',1)."'";
		}
	}
	else
		$shipment_date ="";

	$sql_cond="";
	if($txt_search_common!="")
	{
		if($search_by==1)
			$sql_cond = " and b.po_number like '%".trim($txt_search_common)."%'";
		else if($search_by==2)
			$sql_cond = " and a.style_ref_no like '%".trim($txt_search_common)."%'";
		else if($search_by==3)
			$sql_cond = " and a.job_no like '%".trim($txt_search_common)."'";
		else if($search_by==4)
			$sql_cond = " and b.file_no like '".trim($txt_search_common)."%'";
		else if($search_by==5)
			$sql_cond = " and b.grouping like '%".trim($txt_search_common)."%'";
 	}

	$type=$data[6];
	if($type=="from") $status_cond=" and b.status_active in(1,2,3)"; else $status_cond=" and b.status_active in (1,2,3)";

	if($type=="from")
	{


		$sql_rcv="SELECT b.id,b.pub_shipment_date,a.job_no,a.style_ref_no,a.item_number_id,b.file_no,b.grouping,b.po_quantity,a.country_id,c.buyer_name,b.po_number from pro_leftover_gmts_rcv_dtls a,  wo_po_break_down b, pro_leftover_gmts_rcv_mst c WHERE c.id=a.mst_id and b.id=a.po_break_down_id and c.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $status_cond $sql_cond $shipment_date $buyer_id_cond1 order by b.pub_shipment_date, b.id";

		$tot_sql_rcv=sql_select($sql_rcv);
        $rcv_arr=array();

		foreach($tot_sql_rcv as $row)
		{
			$rcv_arr[$row[csf('id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
			$rcv_arr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
			$rcv_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
			$rcv_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$rcv_arr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$rcv_arr[$row[csf('id')]]['item_number_id']=$row[csf('item_number_id')];
			$rcv_arr[$row[csf('id')]]['file_no']=$row[csf('file_no')];
			$rcv_arr[$row[csf('id')]]['grouping']=$row[csf('grouping')];
			$rcv_arr[$row[csf('id')]]['country_id']=$row[csf('country_id')];
			$rcv_arr[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
		}

		// echo '<pre>';
        // print_r($rcv_arr);
		// echo '</pre>';

       $sql_issue="SELECT a.job_no, a.style_ref_no, c.item_number_id, b.pub_shipment_date, b.po_number, b.file_no, b.grouping, b.po_quantity,b.country_name from wo_po_details_master a, wo_po_break_down b,pro_leftover_gmts_issue_dtls c where a.id = b.job_id and b.id=c.po_break_down_id and a.company_name=$company_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $status_cond $sql_cond $shipment_date $buyer_id_cond order by b.pub_shipment_date, b.id ";

		$tot_sql_issue=sql_select($sql_issue);
        $issue_arr=array();
		foreach($tot_sql_issue as $row)
		{
			$issue_arr[$row[csf('id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
			$issue_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$issue_arr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$issue_arr[$row[csf('id')]]['item_number_id']=$row[csf('item_number_id')];
			$issue_arr[$row[csf('id')]]['file_no']=$row[csf('file_no')];
			$issue_arr[$row[csf('id')]]['grouping']=$row[csf('grouping')];
			$issue_arr[$row[csf('id')]]['country_name']=$row[csf('country_name')];
			$issue_arr[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
		}
		// echo '<pre>';
        // print_r($issue_arr);
		// echo '</pre>';
	}

	else
	{
		$sql = "SELECT b.id, a.order_uom, a.buyer_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, b.pub_shipment_date, b.po_number, b.file_no, b.grouping, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.id = b.job_id and a.company_name=$company_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $status_cond $sql_cond $shipment_date $buyer_id_cond order by b.pub_shipment_date, b.id";

	}

	// echo $sql;//die;


	$result = sql_select($sql);
	$po_id_array = array();
	foreach ($result as $val)
	{
		$po_id_array[$val['ID']] = $val['ID'];
	}

	$po_id_cond = where_con_using_array($po_id_array,0,"po_break_down_id");

 	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

	/*if($db_type==0)
	{
		$po_country_arr=return_library_array( "select po_break_down_id, group_concat(country_id) as country from wo_po_color_size_breakdown  group by po_break_down_id",'po_break_down_id','country');
	}
	else
	{
		$po_country_arr=return_library_array( "select po_break_down_id, listagg(CAST(country_id as VARCHAR(4000)),',') within group (order by country_id) as country from wo_po_color_size_breakdown  group by po_break_down_id",'po_break_down_id','country');
	}*/

	$po_country_sql=sql_select("SELECT po_break_down_id, country_id from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 $po_id_cond group by po_break_down_id,country_id");
	foreach ($po_country_sql as $key => $value)
	{
		if($po_country_arr[$value[csf("po_break_down_id")]]=="")
		{
			$po_country_arr[$value[csf("po_break_down_id")]].=$value[csf("country_id")];

		}
		else
		{
			$po_country_arr[$value[csf("po_break_down_id")]].=','.$value[csf("country_id")];
		}


	}



	$po_country_data_arr=array();
	$poCountryData=sql_select( "SELECT po_break_down_id, item_number_id, country_id, sum(order_quantity) as qnty, sum(plan_cut_qnty)    AS plan_qnty  from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 $po_id_cond group by po_break_down_id, item_number_id,plan_cut_qnty, country_id");
	// echo $poCountryData;die;

	foreach($poCountryData as $row)
	{
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['po_qnty']+=$row[csf('qnty')];
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['plan_qnty']+=$row[csf('plan_qnty')];
	}
	// echo "<pre>";print_r($poCountryData);die;
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="960" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="70">Ship. Date</th>
            <th width="100">Order No</th>
            <th width="65">Buyer</th>
            <th width="120">Style</th>
            <th width="90">Job No</th>
            <th width="70">File No</th>
            <th width="80">Internal Ref.</th>
            <th width="130">Item</th>
            <th width="90">Country</th>
            <th>Order Qty</th>
        </thead>
	</table>
	<div style="width:960px; max-height:220px;overflow-y:scroll;" >
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="940" class="rpt_table" id="tbl_po_list">
		<?
		$i=1;

		if($type=='from')
		{
             foreach($rcv_arr as $id=>$row)
			 {

				$data=$id."_".$row["po_number"]."_".$row["buyer_name"]."_".$row["style_ref_no"]."_".$row["job_no"]."_".$row['po_quantity']."_".$row["item_number_id"]."_".$row['pub_shipment_date']."_".$row['country_id'];

				?>

				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $data; ?>');" >
					<td width="40"><?php echo $i; ?></td>
					<td width="70" align="center"><?php echo $row['pub_shipment_date']; ?></td>
					<td width="100"><p><?php echo $row["po_number"]; ?></p></td>
					<td width="65"><p><?php echo $buyer_arr[$row["buyer_name"]]; ?></p></td>
					<td width="120"><p><?php echo $row["style_ref_no"]; ?></p></td>
					<td width="90"><p><?php echo $row["job_no"]; ?></p></td>
					<td width="70"><p><?php echo $row["file_no"]; ?></p></td>
					<td width="80"><p><?php echo $row["grouping"]; ?></p></td>
					<td width="130"><p><?php echo $garments_item[$row["item_number_id"]];?></p></td>
					<td width="90"><p><?php echo $country_library[$row["country_id"]]; ?></p></td>
					<td align="right"><?php echo $row['po_quantity'];?></td>
				</tr>
               <?
			   $i++;
			 }
		}


		else
		{
            foreach( $result as $row )
            {
				$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
				$numOfItem = count($exp_grmts_item);
				$set_qty=""; $grmts_item="";

				$country=array_unique(explode(",",$po_country_arr[$row[csf("id")]]));
				$numOfCountry = count($country);

				for($k=0;$k<$numOfItem;$k++)
				{
					if($row[csf("total_set_qnty")]>1)
					{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}else
					{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}

					foreach($country as $country_id)
					{
						if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$po_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id]['po_qnty'];
						$plan_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id]['plan_qnty'];

						$shipDate=change_date_format($row[csf("pub_shipment_date")]);
						$data=$row[csf("id")]."_".$row[csf("po_number")]."_".$row[csf("buyer_name")]."_".$row[csf("style_ref_no")]."_".$row[csf("job_no")]."_".$po_qnty."_".$grmts_item."_".$shipDate."_".$country_id."_".$plan_qnty;
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $data; ?>');" >
                            <td width="40"><?php echo $i; ?></td>
                            <td width="70" align="center"><?php echo $shipDate; ?></td>
                            <td width="100"><p><?php echo $row[csf("po_number")]; ?></p></td>
                            <td width="65"><p><?php echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
                            <td width="120"><p><?php echo $row[csf("style_ref_no")]; ?></p></td>
                            <td width="90"><p><?php echo $row[csf("job_no")]; ?></p></td>
                            <td width="70"><p><?php echo $row[csf("file_no")]; ?>&nbsp;</p></td>
                            <td width="80"><p><?php echo $row[csf("grouping")]; ?>&nbsp;</p></td>
                            <td width="130"><p><?php echo $garments_item[$grmts_item];?></p></td>
                            <td width="90"><p><?php echo $country_library[$country_id]; ?>&nbsp;</p></td>
                            <td align="right"><?php echo $po_qnty;?>&nbsp;</td>
                        </tr>
						<?
						$i++;
					}
				}
            }
		}
   		?>
        </table>
    </div>
	<?
	exit();
}
if($action=="populate_data_from_search_popup")
{
		$dataArr = explode("**",$data);
		$po_id = $dataArr[0];
		$item_id = $dataArr[1];

	    $recv_sql=sql_select("SELECT a.po_break_down_id,
		sum(CASE WHEN c.production_type=1 then c.production_qnty ELSE 0 END) as recv_qnty
		from  wo_po_color_size_breakdown a, pro_leftover_gmts_rcv_dtls b,pro_leftover_gmts_rcv_clr_sz c WHERE a.po_break_down_id=b.po_break_down_id and b.id=c.dtls_id and  a.id=c.color_size_break_down_id
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.po_break_down_id='$po_id' and a.item_number_id='$item_id' group by a.po_break_down_id");

		foreach($recv_sql as $row)
		{
			$color_size_qnty_array[$row[csf('po_break_down_id')]]['recv_qnty']= $row[csf('recv_qnty')];

		}
		$issue_sql=sql_select("SELECT a.po_break_down_id,
		sum(CASE WHEN c.production_type=1 then c.production_qnty  ELSE 0 END) as issue_qnty
		from  wo_po_color_size_breakdown a, pro_leftover_gmts_issue_dtls b,pro_leftover_gmts_issue_clr_sz c  WHERE a.po_break_down_id=b.po_break_down_id and b.id=c.dtls_id and  a.id=c.color_size_break_down_id
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.is_deleted=0  and a.po_break_down_id='$po_id' and a.item_number_id='$item_id' group by a.po_break_down_id");

		// echo "SELECT a.po_break_down_id,
		// sum(CASE WHEN c.production_type=1 then c.production_qnty  ELSE 0 END) as issue_qnty
		// from  wo_po_color_size_breakdown a, pro_leftover_gmts_issue_dtls b,pro_leftover_gmts_issue_clr_sz c  WHERE a.po_break_down_id=b.po_break_down_id and b.id=c.dtls_id and  a.id=c.color_size_break_down_id
		// and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.is_deleted=0  and a.po_break_down_id='$po_id' and a.item_number_id='$item_id' group by a.po_break_down_id";


		foreach($issue_sql as $row)
		{
			$color_size_qnty_array[$row[csf('po_break_down_id')]]['issue_qnty']= $row[csf('issue_qnty')];

		}

		$leftover_stock=$color_size_qnty_array[$row[csf('po_break_down_id')]]['recv_qnty']-$color_size_qnty_array[$row[csf('po_break_down_id')]]['issue_qnty'];

	echo "$('#txt_leftover_quantity').val('".$leftover_stock."');\n";

	echo "$('#txt_cumul_quantity').attr('placeholder','".$total_transfer_qnty."');\n";
	echo "$('#txt_cumul_quantity').val('".$total_transfer_qnty."');\n";

	// $yet_to_produced = ($finish_qty+$rcv_rtn_qty+$trans_in_qty)-($exfact_qty+$totalDel+$trans_out_qty);
	// $yettoTitel="Total Finish Qty:".$finish_qty."-Total Delivery Qty:".$totalDel."+( Trans In:".$trans_in_qty."- Trans Out:".$trans_out_qty."+ Rec. Return:".$rcv_rtn_qty.")";

	echo "$('#txt_yet_quantity').attr('placeholder','".$leftover_stock."');\n";
	echo "$('#txt_yet_quantity').val('".$leftover_stock."');\n";
	echo "$('#txt_yet_quantity').attr('title','".$leftover_stock."');\n";

 	exit();
}

if($action=="color_and_size_level")
{
	$dataArr = explode("**",$data);
	// print_r($dataArr);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$variableSettings = $dataArr[2];

	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');


	//#############################################################################################//

	if( $variableSettings==3 ) //color and size level
	{


       	$recv_sql=sql_select("SELECT a.po_break_down_id,a.size_number_id,
		CASE WHEN c.production_type=1 then c.production_qnty ELSE 0 END as recv_qnty
		from  wo_po_color_size_breakdown a, pro_leftover_gmts_rcv_dtls b,pro_leftover_gmts_rcv_clr_sz c WHERE a.po_break_down_id=b.po_break_down_id and b.id=c.dtls_id and  a.id=c.color_size_break_down_id
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.po_break_down_id='$po_id' and a.item_number_id='$item_id'");
		
		foreach($recv_sql as $row)
		{
			$color_size_qnty_array[$row[csf('po_break_down_id')]][$row[csf('size_number_id')]]['recv_qnty']+= $row[csf('recv_qnty')];

		}

		$issue_sql=sql_select("SELECT a.po_break_down_id,a.size_number_id,
		CASE WHEN c.production_type=1 then c.production_qnty ELSE 0 END as issue_qnty
		from  wo_po_color_size_breakdown a, pro_leftover_gmts_issue_dtls b,pro_leftover_gmts_issue_clr_sz c WHERE a.po_break_down_id=b.po_break_down_id  and b.id=c.dtls_id and  a.id=c.color_size_break_down_id
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.is_deleted=0 and a.po_break_down_id='$po_id' and a.item_number_id='$item_id'");

		foreach($issue_sql as $row)
		{
			$color_size_qnty_array[$row[csf('po_break_down_id')]][$row[csf('size_number_id')]]['issue_qnty']+= $row[csf('issue_qnty')];

		}
		$sql = "SELECT id, item_number_id, size_number_id,po_break_down_id, color_number_id, order_quantity, plan_cut_qnty
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id'  and status_active=1 and is_deleted=0   order by color_number_id, id";

			$colorResult = sql_select($sql);
			//print_r($sql);
			$colorHTML="";
			$colorID='';
			$chkColor = array();
			$i=0;$totalQnty=0;
			foreach($colorResult as $color)
			{
				if($variableSettings==3 ) //color and size level
				{
					if( !in_array( $color[csf("color_number_id")], $chkColor ) )
					{
						if( $i!=0 ) $colorHTML .= "</table></div>";
						$i=0;
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[] = $color[csf("color_number_id")];
					}
						//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
						$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
						$recv_qnty=$color_size_qnty_array[$color[csf('po_break_down_id')]][$color[csf('size_number_id')]]['recv_qnty'];
						$issue_qnty=$color_size_qnty_array[$color[csf('po_break_down_id')]][$color[csf('size_number_id')]]['issue_qnty'];

						// echo $recv_qnty;

						$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" title="'.($recv_qnty."_".$issue_qnty).'" placeholder="'.($recv_qnty-$issue_qnty).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')"></td></tr>';
				}
				$i++;
			}

			//echo $colorHTML;die;
			echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
			$colorList = substr($colorID,0,-1);
			echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
		//#############################################################################################//
	}

	exit();
}

if($action=="populate_transfer_form_data")
{
  	$sql_dtls ="SELECT a.id,a.from_po_id,a.to_po_id, a.entry_break_down_type, a.item_number_id, a.country_id, a.production_quantity, a.remarks,b.transfer_criteria,b.company_id from pro_gmts_delivery_dtls a,pro_gmts_delivery_mst b where b.id=a.mst_id and a.id='$data'";

	$sql_dtls_from_to="SELECT b.country_id,b.item_number_id,trans_type from pro_gmts_delivery_dtls a,pro_garments_production_mst b where a.id=b.dtls_id and a.id='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type=17 and b.production_type=17";
	$dtls_from_to_array=array();
  	foreach(sql_select($sql_dtls_from_to) as $keys=>$value)
  	{
  		$dtls_from_to_array[$value[csf("trans_type")]]["item"]=$value[csf("item_number_id")];
  		$dtls_from_to_array[$value[csf("trans_type")]]["country"]=$value[csf("country_id")];

  	}
	$sqlResult =sql_select($sql_dtls);
	$company_id = $sqlResult[0]["COMPANY_ID"];
	$from_po_id = $sqlResult[0]["FROM_PO_ID"];
	$item_id = $sqlResult[0]["ITEM_NUMBER_ID"];
	$country_id = $sqlResult[0]["COUNTRY_ID"];
	// $productionVariable= return_field_value("is_control","variable_settings_production","company_name=$company_id and variable_list=33 and page_category_id=32 ");

	$dtlsData = sql_select("SELECT
	sum(CASE WHEN b.production_type=17 and b.trans_type=6 then a.production_qnty ELSE 0 END) as left_qnty,
	from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.mst_id=b.id and b.production_type in (17) and b.po_break_down_id='$from_po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and b.production_type in(17) ");

    // $sql="SELECT
	// sum(CASE WHEN b.production_type=17 and b.trans_type=6 then a.production_qnty ELSE 0 END) as left_qnty
	// from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.mst_id=b.id and b.production_type in (17) and b.po_break_down_id='$from_po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and b.production_type in(17) ";
	$finish_qty=$totalDel=$total_transferin_qnty =$total_transferout_qnty =$rcv_rtn_qty=0;
	if($productionVariable==1)
	{
		//$finish_qty=$dtlsData[0][csf('finish_qnty')]+$dtlsData[0][csf('rcv_rtn_qty')] - $dtlsData[0][csf('del_qty')]+$dtlsData[0][csf('trans_in_qty')];
		$total_left_qnty = $dtlsData[0][csf('left_qnty')];
		if($total_transferout_qnty=="") $total_transferout_qnty=0;

	}
	else
	{
		$exfactData = sql_select("SELECT sum(d.production_qnty) as ex_fact_qty from pro_ex_factory_mst c, pro_ex_factory_dtls d, pro_ex_factory_delivery_mst e where c.id=d.mst_id and e.id=c.delivery_mst_id and c.po_break_down_id=$from_po_id and c.item_number_id=$item_id and c.country_id=$country_id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0");
		//$finish_qty=$dtlsData[0][csf('finish_qnty')]+$dtlsData[0][csf('rcv_rtn_qty')] - $exfactData[0][csf('ex_fact_qty')]+$dtlsData[0][csf('trans_in_qty')];

	}

	// $total_transfer_qnty = $productionData[0][csf('finish_transfer_qnty')];

	//$yet_to_produced = $finish_qty-$total_transfer_qnty;


	foreach($sqlResult as $result)
	{
			$poIds=$result[csf("from_po_id")].",".$result[csf("to_po_id")];
			$poQtyArr=return_library_array( "SELECT po_break_down_id, sum(order_quantity) as qnty, SUM (plan_cut_qnty) AS plan_qnty from wo_po_color_size_breakdown where   po_break_down_id in($poIds) and item_number_id='".$result[csf("item_number_id")]."' and country_id='".$result[csf("country_id")]."' and status_active=1 and is_deleted=0 group by po_break_down_id","po_break_down_id","qnty");
			$poArr=array();
			$poData = sql_select("SELECT a.buyer_name, a.job_no, a.style_ref_no, b.id, b.pub_shipment_date, b.po_number from wo_po_details_master a, wo_po_break_down b where a.id = b.job_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.id in($poIds)");

			// echo $poQuery="SELECT a.buyer_name, a.job_no, a.style_ref_no, b.id, b.pub_shipment_date, b.po_number from wo_po_details_master a, wo_po_break_down b where a.id = b.job_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.id in($poIds)";
			foreach($poData as $row)
			{
				$poArr[$row[csf('id')]][1]=$row[csf('buyer_name')];
				$poArr[$row[csf('id')]][2]=$row[csf('job_no')];
				$poArr[$row[csf('id')]][3]=$row[csf('style_ref_no')];
				$poArr[$row[csf('id')]][4]=$row[csf('po_number')];
				$poArr[$row[csf('id')]][5]=$row[csf('pub_shipment_date')];
			}



		//$mst_id=return_field_value("id","pro_garments_production_mst","dtls_id=$data and production_type=10 and trans_type=6");
		$update_dtls_issue_id=''; $update_dtls_recv_id='';
		$mstData=sql_select("SELECT id, trans_type from pro_garments_production_mst where dtls_id=$data and production_type=17 and status_active=1 and is_deleted=0");

		foreach($mstData as $row)
		{
			if($row[csf('trans_type')]==6)
			{
				$update_dtls_issue_id=$row[csf('id')];
			}

		}

		echo "document.getElementById('txt_from_order_id').value 			= '".$result[csf("from_po_id")]."';\n";
		echo "document.getElementById('txt_from_order_no').value 			= '".$poArr[$result[csf("from_po_id")]][4]."';\n";
		echo "document.getElementById('txt_from_po_qnty').value 			= '".$poQtyArr[$result[csf("from_po_id")]]."';\n";
		echo "document.getElementById('cbo_from_buyer_name').value 			= '".$poArr[$result[csf("from_po_id")]][1]."';\n";
		echo "document.getElementById('txt_from_style_ref').value 			= '".$poArr[$result[csf("from_po_id")]][3]."';\n";
		echo "document.getElementById('txt_from_job_no').value 				= '".$poArr[$result[csf("from_po_id")]][2]."';\n";
		echo "document.getElementById('cbo_from_gmts_item').value 			= '".$dtls_from_to_array[6]["item"]."';\n";
		echo "document.getElementById('cbo_from_country_name').value 		= '".$dtls_from_to_array[6]["country"]."';\n";
		echo "document.getElementById('txt_from_shipment_date').value 		= '".change_date_format($poArr[$result[csf("from_po_id")]]
		[5])."';\n";


			echo "document.getElementById('txt_to_order_id').value 				= '".$result[csf("to_po_id")]."';\n";
			echo "document.getElementById('txt_to_order_no').value 				= '".$poArr[$result[csf("to_po_id")]][4]."';\n";
			echo "document.getElementById('txt_to_po_qnty').value 				= '".$poQtyArr[$result[csf("to_po_id")]]."';\n";
			echo "document.getElementById('cbo_to_buyer_name').value 			= '".$poArr[$result[csf("to_po_id")]][1]."';\n";
			echo "document.getElementById('txt_to_style_ref').value 			= '".$poArr[$result[csf("to_po_id")]][3]."';\n";
			echo "document.getElementById('txt_to_job_no').value 				= '".$poArr[$result[csf("to_po_id")]][2]."';\n";
			echo "document.getElementById('cbo_to_gmts_item').value 			= '".$dtls_from_to_array[6]["item"]."';\n";
			echo "document.getElementById('cbo_to_country_name').value 			= '".$dtls_from_to_array[6]["country"]."';\n";
			echo "document.getElementById('txt_to_shipment_date').value 		= '".change_date_format($poArr[$result[csf("to_po_id")]][5])."';\n";

		echo "$('#txt_leftover_quantity').val('".$result[csf('production_quantity')]."');\n";
		echo "$('#update_dtls_id').val('".$result[csf('id')]."');\n";
		echo "$('#update_dtls_issue_id').val('".$update_dtls_issue_id."');\n";
		echo "$('#update_dtls_recv_id').val('".$update_dtls_recv_id."');\n";

		$productionData = sql_select("SELECT
		sum(case when production_type=17 then production_quantity else 0 end) as leftover_qnty
		from pro_garments_production_mst
		where po_break_down_id=".$result[csf('from_po_id')]." and item_number_id='".$result[csf('item_number_id')]."' and country_id='".$result[csf('country_id')]."' and status_active=1 and is_deleted=0");

		// $yet_to_produced = $finish_qty-$totalDel+($total_transferin_qnty-$total_transferout_qnty+$rcv_rtn_qty);
		// $yettoTitel="Total Finish Qty:".$finish_qty."-Total Delivery Qty:".$totalDel."+( Trans In:".$total_transferin_qnty."- Trans Out:".$total_transferout_qnty."+ Rec. Return:".$rcv_rtn_qty.")";

		// $finish_qty=$productionData[0][csf('finish_qnty')];
		// if($finish_qty=="") $finish_qty=0;

		// $total_transfer_qnty = $productionData[0][csf('finish_transfer_qnty')]; ;
		// if($total_transfer_qnty=="") $total_transfer_qnty=0;

		echo "$('#txt_cumul_quantity').attr('placeholder','".$total_transferout_qnty."');\n";
		echo "$('#txt_cumul_quantity').val('".$total_transferout_qnty."');\n";
		// $yet_to_produced = $finish_qty-$total_transfer_qnty;
		echo "$('#txt_yet_quantity').attr('placeholder','".$yet_to_produced."');\n";
		// echo "$('#txt_transfer_qty').attr('placeholder','".$result[csf('production_quantity')]."');\n";
		echo "$('#txt_yet_quantity').val('".$yet_to_produced."');\n";
		echo "$('#txt_yet_quantity').attr('title','".$yettoTitel."');\n";
		echo "$('#txt_transfer_qty').removeAttr('readonly')\n";
 		echo "set_button_status(1, permission, 'fnc_transfer_entry',1,1);\n";
		echo "$('#isUpdateMood').val('1');\n";

		//break down of color and size------------------------------------------
 		//#############################################################################################//
		// order wise - color level, color and size level
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

		$variableSettings = $result[csf('entry_break_down_type')];
		if($variableSettings!=1 )
		{
			$po_id = $result[csf('from_po_id')];

		    if( $variableSettings==3 ) //color and size level
			{
				$dtlsData = sql_select("SELECT a.color_size_break_down_id,


							sum(CASE WHEN a.production_type=17 and b.trans_type=6 then a.production_qnty ELSE 0 END) as leftover_qty
							from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(17) group by a.color_size_break_down_id");




				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['out']= $row[csf('leftover_qty')];

				}

			    	$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and status_active=1 and is_deleted=0 order by color_number_id, id";
			}


			$colorResult = sql_select($sql);
			//print_r($sql);
			$colorHTML=""; $colorID=''; $chkColor = array(); $i=0; $totalQnty=0;
			foreach($colorResult as $color)
			{
				if($variableSettings==3 ) //color and size level
				{
					if( !in_array( $color[csf("color_number_id")], $chkColor ) )
					{
						if( $i!=0 ) $colorHTML .= "</table></div>";
						$i=0;
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[] = $color[csf("color_number_id")];
					}
						//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
						$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
						$leftover_qnty=$color_size_qnty_array[$color[csf('po_break_down_id')]][$color[csf('size_number_id')]]['leftover_qty'];

						// echo $recv_qnty;

						$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" title="'.$leftover_qnty.'" placeholder="'.$leftover_qnty.'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')"></td></tr>';
				}
				$i++;
			}
			//echo $colorHTML;die;
			echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
			if( $variableSettings==3 )
			{
				echo "$totalFn;\n";
			}

			$colorList = substr($colorID,0,-1);
			echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
		}
		exit();
	}
}
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		if(str_replace("'","",$update_id)=="")
		{
			//$mst_id=return_next_id("id", "pro_gmts_delivery_mst", 1);
		    $mst_id = return_next_id_by_sequence(  "pro_gmts_delivery_mst_seq","pro_gmts_delivery_mst", $con );


			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later


			$new_sys_number = explode("*", return_next_id_by_sequence("", "pro_gmts_delivery_mst",$con,1,$cbo_company_id,'FGT',731,date("Y",time()),0,0,17,0,0 ));


			$field_array_delivery="id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, delivery_basis, delivery_date, challan_no, transfer_criteria,store_id,working_company_id,purpose_id,working_location_id,working_floor_id, inserted_by, insert_date";
			$data_array_delivery="(".$mst_id.",'".$new_sys_number[1]."',".$new_sys_number[2].",'".$new_sys_number[0]."',".str_replace("'","",$cbo_company_id).",17,1,".$txt_transfer_date.",".$txt_challan_no.",".$cbo_transfer_criteria.",".$cbo_store_name.",".$cbo_del_company.",".$cbo_goods_type.",".$cbo_delivery_location.",".$cbo_delivery_floor.",".$user_id.",'".$pc_date_time."')";

		    // echo "10**".$data_array_delivery;die();
		}
		else
		{
			$mst_id=str_replace("'","",$update_id);
			$field_array_delivery="delivery_date*challan_no*transfer_criteria*updated_by*update_date";
			$data_array_delivery=$txt_transfer_date."*".$txt_challan_no."*".$cbo_transfer_criteria."*".$user_id."*'".$pc_date_time."'";
		}

		if(str_replace("'","",$sewing_production_variable)=="" || str_replace("'","",$sewing_production_variable)==0)
		{
			$sewing_production_variable = 3;
		}


		//$delivery_dtlsId=return_next_id("id", "pro_gmts_delivery_dtls", 1);
		$delivery_dtlsId=return_next_id_by_sequence("PRO_GMTS_DELIVERY_DTLS_PK_SEQ", "pro_gmts_delivery_dtls", $con);

		$field_array_delivery_dtls="id,mst_id,entry_break_down_type,production_type,from_po_id,to_po_id,item_number_id,country_id,production_quantity,inserted_by,insert_date";
		$data_array_delivery_dtls="(".$delivery_dtlsId.",".$mst_id.",".str_replace("'","",$sewing_production_variable).",17,".str_replace("'","",$txt_from_order_id).",".str_replace("'","",$txt_to_order_id).",".str_replace("'","",$cbo_from_gmts_item).",".str_replace("'","",$cbo_from_country_name).",".str_replace("'","",$txt_leftover_quantity).",".$user_id.",'".$pc_date_time."')";

		//$id=return_next_id("id", "pro_garments_production_mst", 1);
		 $id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );
		 $id_recv= return_next_id_by_sequence(  "pro_gar_production_mst_seq",   "pro_garments_production_mst", $con );

		//$id_recv=$id+1;
		//  echo "10**".str_replace("'","",$sewing_production_variable); die;
		if(str_replace("'","",$sewing_production_variable)==3)
		{

				// pro_garments_production_dtls table entry here ----------------------------------///
				// $field_array_dtls="id, mst_id, production_type, trans_type, color_size_break_down_id, production_qnty,delivery_mst_id";
				
                    
                //  echo "10**".str_replace("'","",$sewing_production_variable); die;
			    if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
				{

					$color_sizeID_arr=sql_select( "SELECT id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$txt_from_order_id and item_number_id=$cbo_from_gmts_item  and status_active=1 and is_deleted=0 order by size_number_id,color_number_id" );
					// echo $sql="10**"."SELECT id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$txt_from_order_id and item_number_id=$cbo_from_gmts_item  and status_active=1 and is_deleted=0 order by size_number_id,color_number_id";die();
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val){
						$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
						$colSizeID_from_arr[$index]=$val[csf("id")];
					}

					$color_sizeID_arr=sql_select( "SELECT id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$txt_to_order_id and item_number_id=$cbo_to_gmts_item  and status_active=1 and is_deleted=0 order by size_number_id,color_number_id" );


					//  echo "10**"."SELECT id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$txt_to_order_id and item_number_id=$cbo_to_gmts_item  and status_active=1 and is_deleted=0 order by size_number_id,color_number_id";die();
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val){
						$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
						$colSizeID_arr[$index]=$val[csf("id")];
					}
					// print_r($colSizeID_arr[$index]); die;
					$rowEx = explode("***",$colorIDvalue);
					//  print_r($rowEx); die;
					//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
					$data_array_dtls=""; $j=0;
					//   echo "10**"."KL"; die();
					foreach($rowEx as $rowE=>$valE)
					{

						$colorAndSizeAndValue_arr = explode("*",$valE);
						$sizeID = $colorAndSizeAndValue_arr[0];
						$colorID = $colorAndSizeAndValue_arr[1];
						$colorSizeValue = $colorAndSizeAndValue_arr[2];
						$index = $sizeID.$colorID;
						// echo "10**".$index; die();
						//  echo $colSizeID_arr[$index]; die;
						//   echo "10**"."KL"; die();
						if($colSizeID_arr[$index]>0)
						{
							//   echo "10**"."jKL"; die();
							$txt_leftover_quantity+=$colorSizeValue;
							$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );
							// $id_recv= return_next_id_by_sequence(  "pro_gar_production_mst_seq",   "pro_garments_production_mst", $con );
							// echo "ID Receive = ".$id_recv;
							$field_array_dtls="id, mst_id, production_type, trans_type, color_size_break_down_id, production_qnty,delivery_mst_id";
							if($data_array_dtls!="") $data_array_dtls.=",";
							$data_array_dtls.= "(".str_replace("'","",$dtls_id).",".str_replace("'","",$id).",17,6,'".str_replace("'","",$colSizeID_from_arr[$index])."','".str_replace("'","",$colorSizeValue)."',".$mst_id.")";
							//$dtls_id=$dtls_id+1;
							$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );

							// $data_array_dtls.= "(".str_replace("'","",$dtls_id).",".str_replace("'","",$id).",17,6,'".str_replace("'","",$colSizeID_arr[$index])."','".str_replace("'","",$colorSizeValue)."',".$mst_id.")";
							//$dtls_id=$dtls_id+1;
						}
					}
				}//color and size wise
				// echo "10**$txt_transfer_qty";die();


		}

		$field_array1="id, delivery_mst_id, dtls_id,  company_id, challan_no, po_break_down_id, item_number_id, country_id, production_date, production_quantity, production_type, trans_type, entry_break_down_type, inserted_by, insert_date";

		$data_array1="(".$id.",".$mst_id.",".$delivery_dtlsId.",".str_replace("'","",$cbo_company_id).",".$txt_challan_no.",".str_replace("'","",$txt_from_order_id).",".str_replace("'","",$cbo_from_gmts_item).",".str_replace("'","",$cbo_from_country_name).",".$txt_transfer_date.",".str_replace("'","",$txt_leftover_quantity).",17,6,".str_replace("'","",$sewing_production_variable).",".$user_id.",'".$pc_date_time."')";

		if($id_recv != "" && str_replace("'","",$sewing_production_variable)!=1)
		{
			$data_array1="(".$id.",".$mst_id.",".$delivery_dtlsId.",".str_replace("'","",$cbo_company_id).",".$txt_challan_no.",".str_replace("'","",$txt_from_order_id).",".str_replace("'","",$cbo_from_gmts_item).",".str_replace("'","",$cbo_from_country_name).",".$txt_transfer_date.",".str_replace("'","",$txt_leftover_quantity).",17,6,".str_replace("'","",$sewing_production_variable).",".$user_id.",'".$pc_date_time."')";
		}
		else
		{
			$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );
			$data_array1="(".$id.",".$mst_id.",".$delivery_dtlsId.",".str_replace("'","",$cbo_company_id).",".$txt_challan_no.",".str_replace("'","",$txt_from_order_id).",".str_replace("'","",$cbo_from_gmts_item).",".str_replace("'","",$cbo_from_country_name).",".$txt_transfer_date.",".str_replace("'","",$txt_leftover_quantity).",17,6,".str_replace("'","",$sewing_production_variable).",".$user_id.",'".$pc_date_time."')";
		}

		if(str_replace("'","",$txt_system_id)=="")
		{
			$challanrID=sql_insert("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,1);
		}
		else
		{
			$challanrID=sql_update("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,"id",$update_id,1);
		}

		// echo $field_array_delivery."<br>";
		// echo $data_array_delivery."<br>";

		//echo $field_array_delivery_dtls."<br>";
		//echo $data_array_delivery_dtls."<br>";

		// echo $field_array1."<br>";
		// echo $data_array1."<br>";

		// echo $field_array_dtls."<br>";
		// echo $data_array_dtls."<br>";

		$rIDDtls=sql_insert("pro_gmts_delivery_dtls",$field_array_delivery_dtls,$data_array_delivery_dtls,1);
		//  echo "10**INSERT INTO pro_gmts_delivery_dtls (".$field_array_delivery_dtls.")VALUES $data_array_delivery_dtls"; die;
		$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);
        //  echo "10**INSERT INTO pro_garments_production_mst (".$field_array1.") VALUES $data_array1"; die;
		$dtlsrID=true;
		if($sewing_production_variable==3)
		{
 			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);
		    // echo "10**INSERT INTO pro_garments_production_dtls (".$field_array_dtls.") VALUES $data_array_dtls"; die;
		}
		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);
		//oci_rollback($con);
		// echo "10**INSERT INTO pro_garments_production_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls.""; die;
        //   echo "10**".$challanrID ."&&". $rIDDtls ."&&". $rID ."&&". $dtlsrID;die;
		if($db_type==0)
		{
			if($challanrID && $rIDDtls && $rID && $dtlsrID)
			{
				mysql_query("COMMIT");
				echo "0**".$mst_id."**".$new_sys_number[0]."**".str_replace("'","",$cbo_transfer_criteria);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			//echo "challanrID = ".$challanrID.", rIDDtls = ".$rIDDtls.", dtlsrID = ".$dtlsrID." rID=".$rID.";";
			if($challanrID && $rIDDtls && $rID && $dtlsrID)
			{
				oci_commit($con);
				echo "0**".$mst_id."**".$new_sys_number[0]."**".str_replace("'","",$cbo_transfer_criteria);
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
		//table lock here
		$mst_id=str_replace("'","",$update_id);
		$field_array_delivery="delivery_date*challan_no*transfer_criteria*store_id*purpose_id*working_company_id*working_location_id*working_floor_id*updated_by*update_date";
		$data_array_delivery=$txt_transfer_date."*".$txt_challan_no."*".$cbo_transfer_criteria."*".$cbo_store_name."*".$cbo_goods_type."*".$cbo_del_company."*".$cbo_delivery_location."*".$cbo_delivery_floor."*".$user_id."*'".$pc_date_time."'";

		$field_array_delivery_dtls="from_po_id*to_po_id*item_number_id*country_id*production_quantity*updated_by*update_date";
		$data_array_delivery_dtls=$txt_from_order_id."*".$txt_to_order_id."*".$cbo_from_gmts_item."*".$cbo_from_country_name."*".$txt_leftover_quantity."*".$user_id."*'".$pc_date_time."'";
		if(str_replace("'","",$sewing_production_variable)=="" || str_replace("'","",$sewing_production_variable)==0)
		{
			$sewing_production_variable = 3;
		}

		if(str_replace("'","",$sewing_production_variable)==3)
		{
				// pro_garments_production_dtls table entry here ----------------------------------///
				$field_array_dtls="id, mst_id, production_type, trans_type, color_size_break_down_id, production_qnty,delivery_mst_id";
				$txt_leftover_quantity=0;
				//color level wise
			    if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
				{
					$color_sizeID_arr=sql_select( "SELECT id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$txt_from_order_id and item_number_id=$cbo_from_gmts_item and country_id=$cbo_from_country_name and status_active=1 and is_deleted=0 order by size_number_id,color_number_id" );
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val){
						$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
						$colSizeID_from_arr[$index]=$val[csf("id")];
					}

					$color_sizeID_arr=sql_select( "SELECT id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$txt_to_order_id and item_number_id=$cbo_to_gmts_item and country_id=$cbo_to_country_name and status_active=1 and is_deleted=0 order by size_number_id,color_number_id" );
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val){
						$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
						$colSizeID_arr[$index]=$val[csf("id")];
					}

					$rowEx = explode("***",$colorIDvalue);
					//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
					$data_array_dtls=""; $j=0;
					foreach($rowEx as $rowE=>$valE)
					{
						// echo "abc";
						$colorAndSizeAndValue_arr = explode("*",$valE);
						$sizeID = $colorAndSizeAndValue_arr[0];
						$colorID = $colorAndSizeAndValue_arr[1];
						$colorSizeValue = $colorAndSizeAndValue_arr[2];
						$index = $sizeID.$colorID;

						if($colSizeID_arr[$index]>0)
						{
							$txt_leftover_quantity+=$colorSizeValue;
							$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

							if($data_array_dtls!="") $data_array_dtls.=",";
							$data_array_dtls.= "(".$dtls_id.",".$update_dtls_issue_id.",17,6,'".$colSizeID_from_arr[$index]."','".$colorSizeValue."',".$mst_id.")";
						//	$dtls_id=$dtls_id+1;
							$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );

							$data_array_dtls.= "(".$dtls_id.",".$update_dtls_recv_id.",17,5,'".$colSizeID_arr[$index]."','".$colorSizeValue."',".$mst_id.")";
							//$dtls_id=$dtls_id+1;
						}
					}
				}//color and size wise

				// if($txt_transfer_qty<=0)
				// {
				// 	echo "30**Please Select Same Color And Size.";disconnect($con);die;
				// }

		}

		$field_array_update="challan_no*po_break_down_id*item_number_id*country_id*production_date*production_quantity*updated_by*update_date";
		$updateId_array=array();
		$update_dtls_issue_id=str_replace("'","",$update_dtls_issue_id);
		$update_dtls_recv_id=str_replace("'","",$update_dtls_recv_id);

		if($update_dtls_issue_id != ''){
			$updateId_array[]=$update_dtls_issue_id;
			$updateID_data[$update_dtls_issue_id]=explode("*",("".$txt_challan_no."*".$txt_from_order_id."*".$cbo_from_gmts_item."*".$cbo_from_country_name."*".$txt_transfer_date."*".$txt_leftover_quantity."*".$user_id."*'".$pc_date_time."'"));
		}
		if($update_dtls_recv_id != ''){
			$updateId_array[]=$update_dtls_recv_id;
			$updateID_data[$update_dtls_recv_id]=explode("*",("".$txt_challan_no."*".$txt_to_order_id."*".$cbo_to_gmts_item."*".$cbo_to_country_name."*".$txt_transfer_date."*".$txt_leftover_quantity."*".$user_id."*'".$pc_date_time."'"));
		}

		$rID=sql_update("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,"id",$update_id,1);
		$rID2=sql_update("pro_gmts_delivery_dtls",$field_array_delivery_dtls,$data_array_delivery_dtls,"id",$update_dtls_id,1);
		//print_r($updateId_array); die;
		// echo "10**$sewing_production_variable ** INSERT INTO pro_gmts_delivery_dtls (".$field_array_delivery_dtls.") VALUES ".$data_array_delivery_dtls.""; die;
		$rID3=execute_query(bulk_update_sql_statement("pro_garments_production_mst","id",$field_array_update,$updateID_data,$updateId_array));

		//$mst_ids=$update_dtls_issue_id.",".$update_dtls_recv_id;
		$mst_ids = implode (", ", $updateId_array);
		$rID4=execute_query("delete from pro_garments_production_dtls where mst_id in($mst_ids)",1);

		$dtlsrID=true;
		if(str_replace("'","",$sewing_production_variable)==3)
		{
 			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);
		}
		//check_table_status( $_SESSION['menu_id'],0);
		//oci_rollback($con);
		// echo "10**$sewing_production_variable ** INSERT INTO pro_garments_production_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls.""; die;
		//  echo "10**".$rID ."&&". $rID2 ."&&". $rID3 ."&&". $rID4 ."&&". $dtlsrID;die;
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $dtlsrID)
			{
				mysql_query("COMMIT");
				echo "1**".$mst_id."**".str_replace("'","",$txt_system_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4 && $dtlsrID)
			{
				oci_commit($con);
				echo "1**".$mst_id."**".str_replace("'","",$txt_system_id);
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
		// ============== check ex-factory =======================
		$poData = sql_select("SELECT po_break_down_id as po_id, item_number_id as item_id,country_id from pro_garments_production_mst where delivery_mst_id=$update_id and status_active=1 and is_deleted=0");
		// echo $sql="SELECT po_break_down_id as po_id, item_number_id as item_id,country_id from pro_garments_production_mst where delivery_mst_id=$update_id and status_active=1 and is_deleted=0";
		$poId = $poData[0]['PO_ID'];
		$itmId = $poData[0]['ITEM_ID'];
		$countyId = $poData[0]['COUNTRY_ID'];
		$exSql = sql_select("SELECT sum(ex_factory_qnty) as qty from pro_ex_factory_mst where po_break_down_id=$poId and item_number_id=$itmId and country_id=$countyId and status_active=1 and is_deleted=0");
		$exQty = $exSql[0]['QTY'];
		if($exQty > 0)
		{
			echo "11**Ex-factory qnty found! So, you can not delete.";
			disconnect($con);
			exit();
		}

		// =========delete from delivery ==========================
		$DrID = sql_delete("pro_gmts_delivery_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id ',$update_id,1);
		$DdtlsrID = sql_delete("pro_gmts_delivery_dtls","status_active*is_deleted","0*1",'mst_id',$update_id,1);
		// =========delete from production ==========================
 		$rID = sql_delete("pro_garments_production_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'delivery_mst_id ',$update_id,1);
		$dtlsrID = sql_delete("pro_garments_production_dtls","status_active*is_deleted","0*1",'delivery_mst_id',$update_id,1);

 		if($db_type==0)
		{
			if($rID && $dtlsrID && $DrID && $DdtlsrID)
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
			if($rID && $dtlsrID && $DrID && $DdtlsrID)
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
<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
//print_r ($data[0]);
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","","","","","",3 );
	exit();	 
}

if ($action=="load_drop_down_party_name")
{
	$data=explode('_',$data);
	$selected_company = $data[0];
	if($data[1]==2)
	{
		echo create_drop_down( "cbo_party_id", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "show_list_view(document.getElementById('cbo_company_id').value+'***'+document.getElementById('cbo_location_name').value+'***'+document.getElementById('cbo_party_source').value+'***'+this.value+'***'+document.getElementById('cbo_bill_for').value,'embellishment_entry_list_view','embellishment_info_list','requires/subcon_embellisment_bill_issue_controller','setFilterGrid(\'tbl_list_search\',-1)');","","","","","",5 ); 
	}
	else if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_id", 150, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "-- Select Comapny --", $selected, "show_list_view(document.getElementById('cbo_company_id').value+'***'+document.getElementById('cbo_location_name').value+'***'+document.getElementById('cbo_party_source').value+'***'+this.value+'***'+document.getElementById('cbo_bill_for').value,'embellishment_entry_list_view','embellishment_info_list','requires/subcon_embellisment_bill_issue_controller','setFilterGrid(\'tbl_list_search\',-1)');","","","","","",5); 
	}
	else
	{
		echo create_drop_down( "cbo_party_id", 150, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"","","","",5);
	}
	exit();
}

if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_party_id", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "","","","","","",5);
	exit();
}

if ($action=="bill_no_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	$ex_data=explode('_',$data);
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('issue_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="packingbill_1"  id="packingbill_1" autocomplete="off">
                <table width="650" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="80">Bill ID</th>
                        <th width="170">Date Range</th>
                        <th>
                        <input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" />
                        </th>           
                    </thead>
                    <tbody>
                        <tr>
                            <td> 
                                <input type="hidden" id="issue_id">  
                                <?   
									echo create_drop_down( "cbo_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $ex_data[0],"load_drop_down( 'subcon_embellisment_bill_issue_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );",0 );
                                ?>
                            </td>
                            <td width="150" id="supplier_td">
								<?
									echo create_drop_down( "cbo_party_id", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$ex_data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $ex_data[1], "","","","","","",5 );
                                ?> 
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:75px" />
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value, 'embellishment_bill_list_view', 'search_div', 'subcon_embellisment_bill_issue_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center" height="40" valign="middle">
								<? echo load_month_buttons(1);  ?>
                            </td>
                        </tr>
                        <tr>
                        <td colspan="5" align="center" valign="top" id=""><div id="search_div"></div></td>
                        </tr>
                    </tbody>
                </table>    
            </form>
        </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="embellishment_bill_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company_name=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $party_name_cond=" and party_id='$data[1]'"; $party_name_cond="";
	
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $return_date = "and bill_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $return_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $return_date = "and bill_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $return_date ="";
	}
	
	if ($data[4]!='') $bill_id_cond=" and prefix_no_num='$data[4]'"; else $bill_id_cond="";
	
	$company_id=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	
	$arr=array (3=>$party_arr,5=>$knitting_source,4=>$bill_for);
	
	if($db_type==0)
	{
		$year_cond= "year(insert_date)as year";
	}
	else if($db_type==2)
	{
		$year_cond= "TO_CHAR(insert_date,'YYYY') as year";
	}
	
	$sql= "select id, bill_no, prefix_no_num, $year_cond, location_id, bill_date, party_source, party_id, bill_for from subcon_inbound_bill_mst where process_id=12 and company_id='$data[0]' and status_active=1 $party_name_cond $return_date $bill_id_cond";
	
	echo  create_list_view("list_view", "Bill No,Year,Bill Date,Party Name,Bill For", "70,70,100,120,100","600","250",0, $sql , "js_set_value", "id", "", 1, "0,0,0,party_id,bill_for", $arr , "prefix_no_num,year,bill_date,party_id,bill_for", "subcon_embellisment_bill_issue_controller","",'0,0,3,0,0') ;
	exit(); 
}

if ($action=="load_php_data_to_form_issue")
{
	$nameArray= sql_select("select id, bill_no, company_id, location_id, bill_date, party_source, party_id, bill_for from subcon_inbound_bill_mst where id='$data'");
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_bill_no').value 					= '".$row[csf("bill_no")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_party_source').value 			= '".$row[csf("party_source")]."';\n";
		echo "document.getElementById('cbo_bill_for').value 				= '".$row[csf("bill_for")]."';\n";
		
		//echo "load_drop_down('requires/subcon_embellisment_bill_issue_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_location', 'location_td' );\n";
		echo "load_drop_down('requires/subcon_embellisment_bill_issue_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_source').value, 'load_drop_down_party_name', 'party_td' );\n";
		echo "document.getElementById('cbo_party_id').value					= '".$row[csf("party_id")]."';\n"; 

		echo "document.getElementById('cbo_location_name').value			= '".$row[csf("location_id")]."';\n"; 
		echo "document.getElementById('txt_bill_date').value 				= '".change_date_format($row[csf("bill_date")])."';\n";   
	    echo "document.getElementById('update_id').value            		= '".$row[csf("id")]."';\n";
	}
	exit();
}

if ($action=="embellishment_entry_list_view")
{
	echo load_html_head_contents("Popup Info","../", 1, 1, $unicode,1,'');
	$exdata=explode('***',$data);
	if ($exdata[2]==1)
	{
	?>
		<script>
        </script>
        </head>
        <body>
            <div style="width:100%;">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="920px" class="rpt_table">
                    <thead>
                        <th width="25">SL</th>
                        <th width="70">Challan No</th>
                        <th width="65">Recive Date</th>
                        <th width="60">Sys. No</th>                    
                        <th width="110">Garments Item</th>
                        <th width="120">Embl. Name & Type</th>
                        <th width="75">Recive Qty</th>
                        <th width="100">Order No</th>
                        <th width="100">Style Ref.</th>
                        <th width="50">Job</th>
                        <th width="50">Year</th>
                        <th>Buyer</th>
                    </thead>
                </table>
            </div>
            <div style="width:920px; max-height:180px; overflow-y:scroll">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="902px" class="rpt_table" id="tbl_list_search">
                <? 
                $order_array=array();
                if($db_type==0) $year_cond= "year(a.insert_date)";
                else if($db_type==2) $year_cond= "TO_CHAR(a.insert_date,'YYYY')";
                $order_sql=sql_select( "select a.job_no_prefix_num, $year_cond as year, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
                foreach($order_sql as $row)
                {
                    $order_array[$row[csf('id')]]['job_no']=$row[csf('job_no_prefix_num')];
                    $order_array[$row[csf('id')]]['year']=$row[csf('year')];
                    $order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
                    $order_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
                    $order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
                }
				
				$delivery_idArr=array();
				$delivery_sql="SELECT b.delivery_id, sum(b.delivery_qty) as delivery_qty FROM subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.process_id=12 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 group by b.delivery_id";
				$delivery_sql_result=sql_select($delivery_sql);
				foreach($delivery_sql_result as $row)
				{
					$delivery_idArr[$row[csf('delivery_id')]]=$row[csf('delivery_qty')];
				}
				
                 
                $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
                $i=1;
                /*if(!$exdata[5]) // Insert
                {*/
                    $sql="select id, challan_no, production_date, po_break_down_id, item_number_id, production_quantity, embel_name, embel_type from pro_garments_production_mst where company_id=$exdata[0] and location=$exdata[1] and serving_company=$exdata[3] and production_source=1 and production_type=3 and status_active=1 and is_deleted=0 order by id Desc";			
					if($exdata[6]!='')
					{
						$sql_data="select id, challan_no, production_date, po_break_down_id, item_number_id, production_quantity, embel_name, embel_type from pro_garments_production_mst where company_id=$exdata[0] and location=$exdata[1] and serving_company=$exdata[3] and id in ($exdata[6]) and production_source=1 and production_type=3 and status_active=1 and is_deleted=0 order by id Desc";	
						$sql_data_result=sql_select($sql_data);
						foreach($sql_data_result as $row)
						{
							$data_all.=$row[csf('id')].'_'.$row[csf('challan_no')].'_'.$row[csf('production_date')].'_'.$row[csf('po_break_down_id')].'_'.$row[csf('item_number_id')].'_'.$row[csf('production_quantity')].'_'.$row[csf('embel_name')].'_'.$row[csf('embel_type')].'==';
						}
					}
                /*}
                else
                {
					$sql="select id, challan_no, production_date, po_break_down_id, item_number_id, production_quantity, embel_name, embel_type from pro_garments_production_mst where company_id=$exdata[0] and location=$exdata[1] and serving_company=$exdata[3] and production_source=1 and production_type=3 and status_active=1 and is_deleted=0 order by id Desc";			
                }*/
                //echo $sql;
				
				$data_all_ex=explode('==',$data_all);
				//print_r ($data_all_ex);
				if(count($data_all_ex)>0)
				{
					foreach($data_all_ex as $val)
					{
						//$all_value=$val;
						$ex_val=explode('_',$val);
						//$rec_challan_no=$save_data_arr[$ex_val[0]][$ex_val[1]][$ex_val[2]][$ex_val[3]][$ex_val[4]]['challan_no'];
						//$receive_date=$save_data_arr[$ex_val[0]][$ex_val[1]][$ex_val[2]][$ex_val[3]][$ex_val[4]]['receive_date'];
						//$roll_qty=$save_data_arr[$ex_val[0]][$ex_val[1]][$ex_val[2]][$ex_val[3]][$ex_val[4]]['roll_qty'];
						//$quantity=$save_data_arr[$ex_val[0]][$ex_val[1]][$ex_val[2]][$ex_val[3]][$ex_val[4]]['quantity'];
						if($ex_val[0]!=0)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if($ex_val[6]==1) $embel_type=' & '.$emblishment_print_type[$ex_val[7]];
							elseif($ex_val[6]==2) $embel_type=' & '.$emblishment_embroy_type[$ex_val[7]];
							elseif($ex_val[6]==3) $embel_type=' & '.$emblishment_wash_type[$ex_val[7]];	
							elseif($ex_val[6]==4) $embel_type=' & '.$emblishment_spwork_type[$ex_val[7]];
							else $embel_type='';
							?>
							<tr id="tr_<? echo $ex_val[0]; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $ex_val[0]; ?>');">
                                <td width="25"><? echo $i; ?></td>
                                <td width="70"><div style="word-wrap:break-word; width:70px"><? echo $ex_val[1]; ?></div></td>
                                <td width="65"><? echo change_date_format($ex_val[2]); ?></td>
                                <td width="60"><? echo $ex_val[0]; ?></td>
                                <td width="110"><div style="word-wrap:break-word; width:100px"><? echo $garments_item[$ex_val[4]]; ?></div></td>
                                <td width="120"><div style="word-wrap:break-word; width:120px"><? echo $emblishment_name_array[$ex_val[6]].''.$embel_type; ?></div></td>
                                <td width="75" align="right"><? echo number_format($available_qty,2); ?></td>
                                <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $order_array[$ex_val[3]]['po_number']; ?></div></td>
                                <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $order_array[$ex_val[3]]['style']; ?></div></td>
                                <td width="50"><? echo $order_array[$ex_val[3]]['job_no']; ?></td>
                                <td width="50"><? echo $order_array[$ex_val[3]]['year']; ?></td>
                                <td align="center"><div style="word-wrap:break-word; width:60px"><? echo $buyer_arr[$order_array[$ex_val[3]]['buyer_name']]; ?>
                                <input type="hidden" id="currid<? echo $ex_val[0]; ?>" value="<? echo $ex_val[0]; ?>" style="width:40px"></div></td>
                            </tr>
							<?php
							$i++;
						}
					}
				}
                $sql_result=sql_select($sql);
                foreach($sql_result as $row)
                {
                   // if($row[csf('recid')]==1) $bgcolor="yellow";
				   $delvery_qty=$delivery_idArr[$row[csf('id')]];
                    if($row[csf('embel_name')]==1) $embel_type=' & '.$emblishment_print_type[$row[csf('embel_type')]];
                    elseif($row[csf('embel_name')]==2) $embel_type=' & '.$emblishment_embroy_type[$row[csf('embel_type')]];
                    elseif($row[csf('embel_name')]==3) $embel_type=' & '.$emblishment_wash_type[$row[csf('embel_type')]];	
                    elseif($row[csf('embel_name')]==4) $embel_type=' & '.$emblishment_spwork_type[$row[csf('embel_type')]];
                    else $embel_type='';
					$available_qty=$row[csf('production_quantity')]-$delvery_qty;
					if($available_qty>0)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr id="tr_<? echo $row[csf('id')]; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>');">
							<td width="25"><? echo $i; ?></td>
							<td width="70"><div style="word-wrap:break-word; width:70px"><? echo $row[csf('challan_no')]; ?></div></td>
							<td width="65"><? echo change_date_format($row[csf('production_date')]); ?></td>
							<td width="60"><? echo $row[csf('id')]; ?></td>
							<td width="110"><div style="word-wrap:break-word; width:100px"><? echo $garments_item[$row[csf('item_number_id')]]; ?></div></td>
							<td width="120"><div style="word-wrap:break-word; width:120px"><? echo $emblishment_name_array[$row[csf('embel_name')]].''.$embel_type; ?></div></td>
							<td width="75" align="right"><? echo number_format($available_qty,2); ?></td>
							<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $order_array[$row[csf('po_break_down_id')]]['po_number']; ?></div></td>
							<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $order_array[$row[csf('po_break_down_id')]]['style']; ?></div></td>
							<td width="50"><? echo $order_array[$row[csf('po_break_down_id')]]['job_no']; ?></td>
							<td width="50"><? echo $order_array[$row[csf('po_break_down_id')]]['year']; ?></td>
							<td align="center"><div style="word-wrap:break-word; width:60px"><? echo $buyer_arr[$order_array[$row[csf('po_break_down_id')]]['buyer_name']]; ?>
							<input type="hidden" id="currid<? echo $row[csf('id')]; ?>" value="<? echo $row[csf('id')]; ?>" style="width:40px"></div></td>
						</tr>
						<?php
						$i++;
					}
				}
                ?>
            </table>
            </div>
            <table width="900">
                <tr>
                    <td colspan="12" align="center">
                        <input type="button" id="show_button" class="formbutton" style="width:100px" value="Close" onClick="window_close()" />
                    </td>
                </tr>
            </table>
        </body>           
        <script src="../includes/functions_bottom.js" type="text/javascript"></script>
        </html>
        <?
	}
	exit();
}

if ($action=="load_php_dtls_form") 
{
	$data = explode("_",$data);
	$del_id=array_diff(explode(",",$data[0]), explode(",",$data[1]));
	$bill_id=array_intersect(explode(",",$data[0]), explode(",",$data[1]));
	$delete_id=array_diff(explode(",",$data[1]), explode(",",$data[0]));
	$del_id=implode(",",$del_id); $bill_id=implode(",",$bill_id); $delete_id=implode(",",$delete_id);
	//echo $del_id.'=='.$bill_id;  
	$order_array=array();
	$sql_order="Select a.id, a.po_number, b.style_ref_no, b.buyer_name from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
	$sql_order_result=sql_select($sql_order);
	foreach ($sql_order_result as $row)
	{
		$order_array[$row[csf("id")]]['po_number']=$row[csf("po_number")];
		$order_array[$row[csf("id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$order_array[$row[csf("id")]]['buyer_name']=$row[csf("buyer_name")];
	}
	if($db_type==0)
	{
		$delivery_id_cond="group_concat(id)";
		$challan_no_cond="group_concat(challan_no)";
		$item_id_cond="group_concat(item_number_id)";
		$prod_challan_cond="group_concat(challan_no)";
	}
	else if ($db_type==2)
	{
		$delivery_id_cond="LISTAGG(id, ',') WITHIN GROUP (ORDER BY id)";
		$challan_no_cond="LISTAGG(cast(challan_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY challan_no)";
		$item_id_cond="LISTAGG(cast(item_number_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY item_number_id)";
		$prod_challan_cond="LISTAGG(cast(challan_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY challan_no)";
	}
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name'); 
	if( $data[2]!="" )//update===========
	{
		$sql="SELECT id as upd_id, delivery_id, delivery_date, challan_no, order_id, item_id, delivery_qty, embel_name, embel_type, rate, amount, remarks, currency_id FROM subcon_inbound_bill_dtls  WHERE mst_id=$data[2] and process_id=12 and status_active=1 and is_deleted=0";
	}
	else //insert=================
	{
		if($bill_id!="" && $del_id!="")
			$sql="(SELECT id as upd_id, delivery_id, delivery_date, $challan_no_cond as challan_no, order_id, item_id, delivery_qty, embel_name, embel_type, rate, amount, remarks, currency_id FROM subcon_inbound_bill_dtls WHERE delivery_id in ($bill_id) and process_id='12' and status_active=1 and is_deleted=0  group by id, delivery_id, delivery_date, challan_no, order_id, item_id, delivery_qty, embel_name, embel_type, rate, amount, remarks, currency_id)
			 union
			 (SELECT 0 as upd_id, $delivery_id_cond as delivery_id, production_date as delivery_date, $prod_challan_cond as challan_no, po_break_down_id as order_id, $item_id_cond as item_id, sum(production_quantity) as delivery_qty, embel_name, embel_type, 0 as rate, 0 as amount, null as remarks FROM pro_garments_production_mst WHERE id in ($del_id) and production_source=1 and production_type=3 and status_active=1 and is_deleted=0 group by id, production_date, challan_no, po_break_down_id, item_number_id, embel_name, embel_type) order by delivery_id DESC";
		else if($bill_id!="" && $del_id=="")
			$sql="SELECT id as upd_id, delivery_id, delivery_date, challan_no, order_id, item_id, delivery_qty, embel_name, embel_type, rate, amount, remarks, currency_id FROM  subcon_inbound_bill_dtls WHERE delivery_id in ($bill_id) and process_id='12' and status_active=1 and is_deleted=0";
		else if($bill_id=="" && $del_id!="")
			$sql="SELECT 0 as upd_id, id as delivery_id, production_date as delivery_date, challan_no, po_break_down_id as order_id, item_number_id as item_id, sum(production_quantity) as delivery_qty, embel_name, embel_type, 0 as rate, 0 as amount, null as remarks FROM pro_garments_production_mst WHERE id in ($del_id) and production_source=1 and production_type=3 and status_active=1 and is_deleted=0 group by id, production_date, challan_no, po_break_down_id, item_number_id, embel_name, embel_type order by id DESC";
	}
	//echo $sql;//die;	
	$k=0;
	$sql_result=sql_select($sql);
	$num_rowss=count($sql_result);
	foreach ($sql_result as $row)
	{
		 $k++;
		 if( $data[2]!="" )
		 {
			 if($data[1]=="") $data[1]=$row[csf("delivery_id")]; else $data[1].=",".$row[csf("delivery_id")];
		 }
	?>
       <tr align="center">				
            <td>
				<? if ($k==$num_rowss) { ?>
                    <input type="hidden" name="issue_id_all" id="issue_id_all"  style="width:65px" value="<? echo $data[1]; ?>" />
                    <input type="hidden" name="delete_id" id="delete_id"  style="width:65px" value="<? echo $delete_id; ?>" />
                 <? } ?>
                <input type="hidden" name="updateiddtls_<? echo $k; ?>" id="updateiddtls_<? echo $k; ?>" value="<? echo ($row[csf("upd_id")] != 0 ? $row[csf("upd_id")] : "") ?>">
                <input type="date" name="txtReceiveDate_<? echo $k; ?>" id="txtReceiveDate_<? echo $k; ?>"  class="datepicker" style="width:65px" value="<? echo change_date_format($row[csf("delivery_date")]); ?>" readonly />									
            </td>
            <td>
                <input type="text" name="txtChallenno_<? echo $k; ?>" id="txtChallenno_<? echo $k; ?>"  class="text_boxes" style="width:75px" value="<? echo $row[csf("challan_no")]; ?>" readonly />							 
            </td>
            <td>
                <input type="text" name="txtSysno_<? echo $k; ?>" id="txtSysno_<? echo $k; ?>" class="text_boxes" style="width:55px" value="<? echo $row[csf("delivery_id")]; ?>" readonly />							 
            </td>
            <td>
                <input type="hidden" name="ordernoid_<? echo $k; ?>" id="ordernoid_<? echo $k; ?>" value="<? echo $row[csf("order_id")]; ?>" style="width:40px" readonly /> 
                <input type="text" name="txtOrderno_<? echo $k; ?>" id="txtOrderno_<? echo $k; ?>"  class="text_boxes" style="width:65px" value="<? echo $order_array[$row[csf("order_id")]]['po_number']; ?>" readonly />										
            </td>
            <td>
                <input type="text" name="txtStylename_<? echo $k; ?>" id="txtStylename_<? echo $k; ?>"  class="text_boxes" style="width:65px;" value="<? echo $order_array[$row[csf("order_id")]]['style_ref_no']; ?>" readonly />
            </td>
            <td>
                <input type="text" name="txtPartyname_<? echo $k; ?>" id="txtPartyname_<? echo $k; ?>"  class="text_boxes" style="width:55px" value="<? echo $buyer_arr[$order_array[$row[csf("order_id")]]['buyer_name']]; ?>" readonly />								
            </td>
            <td>
                <input type="hidden" name="itemid_<? echo $k; ?>" id="itemid_<? echo $k; ?>" value="<? echo $row[csf("item_id")]; ?>">
                <input type="text" name="txtGmtsItem_<? echo $k; ?>" id="txtGmtsItem_<? echo $k; ?>"  class="text_boxes" style="width:95px" value="<? echo $garments_item[$row[csf("item_id")]]; ?>" readonly />
            </td>
            <td>
            <?
				if($row[csf('embel_name')]==1) $embel_type=' & '.$emblishment_print_type[$row[csf('embel_type')]];
				elseif($row[csf('embel_name')]==2) $embel_type=' & '.$emblishment_embroy_type[$row[csf('embel_type')]];
				elseif($row[csf('embel_name')]==3) $embel_type=' & '.$emblishment_wash_type[$row[csf('embel_type')]];	
				elseif($row[csf('embel_name')]==4) $embel_type=' & '.$emblishment_spwork_type[$row[csf('embel_type')]];
				else $embel_type='';
			?>
                <input type="hidden" name="embelid_<? echo $k; ?>" id="embelid_<? echo $k; ?>" value="<? echo $row[csf("embel_name")]; ?>">
                <input type="hidden" name="embelTypeid_<? echo $k; ?>" id="embelTypeid_<? echo $k; ?>" value="<? echo $row[csf("embel_type")]; ?>">
                <input type="text" name="textEmbelNameType_<? echo $k; ?>" id="textEmbelNameType_<? echo $k; ?>"  class="text_boxes" style="width:115px" value="<? echo  $emblishment_name_array[$row[csf('embel_name')]].''.$embel_type; ?>" readonly />
            </td>
            <td>
                <input type="text" name="textWoNum_<? echo $k; ?>" id="textWoNum_<? echo $k; ?>" class="text_boxes" style="width:55px" value="<? //echo $row[csf("")]; ?>" disabled/>
            </td>
            <td>
                <input type="text" name="txtQnty_<? echo $k; ?>" id="txtQnty_<? echo $k; ?>"  class="text_boxes_numeric" style="width:55px;" value="<? echo $row[csf("delivery_qty")]; ?>" />
            </td>
            <td>
                <input type="text" name="txtRate_<? echo $k; ?>" id="txtRate_<? echo $k; ?>"  class="text_boxes_numeric" style="width:35px;" value="<? echo $row[csf("rate")]; ?>" onBlur="amount_caculation(<? echo $k; ?>);" />
            </td>
            <td>
				<?
					//$total_amount=$row[csf("order_qnty")]*$row[csf("rate")];
                ?>
                <input type="text" name="txtAmount_<? echo $k; ?>" id="txtAmount_<? echo $k; ?>" style="width:60px;"  class="text_boxes_numeric" value="<? echo $row[csf("amount")]; ?>" readonly  />                	
            </td>
            <td>
					<? echo create_drop_down( "cbo_curanci_$k", 60, $currency,"", 1, "-Select Currency-",$row[csf("currency_id")],"",0,"" );?>
              </td>
            <td>
            	<input type="button" name="txtRemarks_<? echo $k; ?>" id="txtRemarks_<? echo $k; ?>"  class="formbuttonplasminus" style="width:30px" value="R" onClick="openmypage_remarks(<? echo $k; ?>);" />
                <input type="hidden" name="hiddRemarks_<? echo $k; ?>" id="hiddRemarks_<? echo $k; ?>"  class="text_boxes" style="width:25px" value="<? echo $row[csf("remarks")]; ?>" />
            </td>
        </tr>
	<?	
	}
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$bill_process_id="12";
	if ($operation==0)   // Insert Here 
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0)
		{
			$year_cond=" and YEAR(insert_date)";	
		}
		else if($db_type==2)
		{
			$year_cond=" and TO_CHAR(insert_date,'YYYY')";	
		}
		
		$new_bill_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'EMBL', date("Y",time()), 5, "select prefix_no, prefix_no_num from  subcon_inbound_bill_mst where company_id=$cbo_company_id and process_id=$bill_process_id $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		
		if(str_replace("'",'',$update_id)=="")
		{
			$id=return_next_id( "id", "subcon_inbound_bill_mst",1); 	
			$field_array="id, prefix_no, prefix_no_num, bill_no, company_id, location_id, bill_date, party_source, bill_for, party_id, process_id, inserted_by, insert_date";
			$data_array="(".$id.",'".$new_bill_no[1]."','".$new_bill_no[2]."','".$new_bill_no[0]."',".$cbo_company_id.",".$cbo_location_name.",".$txt_bill_date.",".$cbo_party_source.",".$cbo_bill_for.",".$cbo_party_id.",".$bill_process_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
			//echo "INSERT INTO subcon_outbound_bill_mst (".$field_array.") VALUES ".$data_array; die;
			$rID=sql_insert("subcon_inbound_bill_mst",$field_array,$data_array,0);
			$return_no=$new_bill_no[0]; 
		}
		else
		{
			/*$id=str_replace("'",'',$update_id);
			$field_array="location_id*bill_date*party_id*updated_by*update_date";
			$data_array="".$cbo_location_name."*".$txt_bill_date."*".$cbo_supplier_company."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			$rID=sql_update("subcon_inbound_bill_mst",$field_array,$data_array,"id",$update_id,0);
			$return_no=str_replace("'",'',$txt_bill_no);*/
		}
		//id as upd_id, delivery_id, delivery_date, challan_no, order_id, item_id, delivery_qty, embel_name, embel_type, rate, amount, remarks FROM subcon_inbound_bill_dtls
		$id1=return_next_id( "id", "subcon_inbound_bill_dtls",1);
		$field_array1 ="id, mst_id, delivery_id, delivery_date, challan_no, order_id, item_id, embel_name, embel_type, delivery_qty, rate, amount, remarks, process_id,currency_id, inserted_by, insert_date";
		$field_array_up ="delivery_id*delivery_date*challan_no*order_id*item_id*embel_name*embel_type*delivery_qty*rate*amount*remarks*currency_id*updated_by*update_date";
		$add_comma=0;
		for($i=1; $i<=$tot_row; $i++)
		{
			$reciveid="txtSysno_".$i;
			$receive_date="txtReceiveDate_".$i;
			$challen_no="txtChallenno_".$i;
			$orderid="ordernoid_".$i;
			$item_id="itemid_".$i;
			$embelid="embelid_".$i;
			$embelTypeid="embelTypeid_".$i;
			$wo_num="textWoNum_".$i;
			$quantity="txtQnty_".$i;
			$rate="txtRate_".$i;
			$amount="txtAmount_".$i;
			$remarks="hiddRemarks_".$i;
			$updateid_dtls="updateiddtls_".$i;
			$curanci="cbo_curanci_".$i;
			  
			if(str_replace("'",'',$$updateid_dtls)=="")  
			{
				if ($add_comma!=0) $data_array1 .=",";
				$data_array1 .="(".$id1.",".$id.",".$$reciveid.",".$$receive_date.",".$$challen_no.",".$$orderid.",".$$item_id.",".$$embelid.",".$$embelTypeid.",".$$quantity.",".$$rate.",".$$amount.",".$$remarks.",'".$bill_process_id."',".$$curanci.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id1=$id1+1;
				$add_comma++;
			}
			else
			{
				$id_arr[]=str_replace("'",'',$$updateid_dtls);
				$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$reciveid."*".$$receive_date."*".$$challen_no."*".$$orderid."*".$$item_id."*".$$embelid."*".$$embelTypeid."*".$$quantity."*".$$rate."*".$$amount."*".$$remarks."*".$$curanci."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				$id_arr_delivery[]=str_replace("'",'',$$reciveid);
				$data_array_delivery[str_replace("'",'',$$reciveid)] =explode("*",("1"));
			}
		}
			
		if($data_array1!="")
		{
			//echo "insert into subcon_outbound_bill_dtls (".$field_array1.") values ".$data_array1;die;
			$rID1=sql_insert("subcon_inbound_bill_dtls",$field_array1,$data_array1,1);
		}
	
		if($db_type==0)
		{
			if($rID && $rID1 )
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		if($db_type==2)
		{
			if($rID && $rID1 )
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}	
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here=============================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	
		$id=str_replace("'",'',$update_id);
		$field_array="location_id*bill_date*party_id*updated_by*update_date";
		$data_array="".$cbo_location_name."*".$txt_bill_date."*".$cbo_party_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$rID=sql_update("subcon_inbound_bill_mst",$field_array,$data_array,"id",$update_id,0);
		if($rID) $flag=1; else $flag=0;
		$return_no=str_replace("'",'',$txt_bill_no);
		
		$id1=return_next_id( "id","subcon_inbound_bill_dtls",1);
		$field_array1 ="id, mst_id, delivery_id, delivery_date, challan_no, order_id, item_id, embel_name, embel_type, delivery_qty, rate, amount, remarks, process_id, currency_id, inserted_by, insert_date";
		$field_array_up ="delivery_id*delivery_date*challan_no*order_id*item_id*embel_name*embel_type*delivery_qty*rate*amount*remarks*currency_id*updated_by*update_date";
		$add_comma=0;
		for($i=1; $i<=$tot_row; $i++)
		{
			$reciveid="txtSysno_".$i;
			$receive_date="txtReceiveDate_".$i;
			$challen_no="txtChallenno_".$i;
			$orderid="ordernoid_".$i;
			$item_id="itemid_".$i;
			$embelid="embelid_".$i;
			$embelTypeid="embelTypeid_".$i;
			$wo_num="textWoNum_".$i;
			$quantity="txtQnty_".$i;
			$rate="txtRate_".$i;
			$amount="txtAmount_".$i;
			$remarks="hiddRemarks_".$i;
			$updateid_dtls="updateiddtls_".$i;
			$curanci="cbo_curanci_".$i;
			  
			if(str_replace("'",'',$$updateid_dtls)=="")  
			{
				if ($add_comma!=0) $data_array1 .=",";
				$data_array1 .="(".$id1.",".$id.",".$$reciveid.",".$$receive_date.",".$$challen_no.",".$$orderid.",".$$item_id.",".$$embelid.",".$$embelTypeid.",".$$quantity.",".$$rate.",".$$amount.",".$$remarks.",'".$bill_process_id."',".$$curanci.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id1=$id1+1;
				$add_comma++;
			}
			else
			{
				$id_arr[]=str_replace("'",'',$$updateid_dtls);
				$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$reciveid."*".$$receive_date."*".$$challen_no."*".$$orderid."*".$$item_id."*".$$embelid."*".$$embelTypeid."*".$$quantity."*".$$rate."*".$$amount."*".$$remarks."*".$$curanci."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				$id_arr_delivery[]=str_replace("'",'',$$reciveid);
				$data_array_delivery[str_replace("'",'',$$reciveid)] =explode("*",("1"));
			}
		}
			  
		$rID1=execute_query(bulk_update_sql_statement("subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
		if($rID1) $flag=1; else $flag=0;
		if($data_array1!="")
		{
			//echo "insert into subcon_outbound_bill_dtls (".$field_array1.") values ".$data_array1;
			$rID1=sql_insert("subcon_inbound_bill_dtls",$field_array1,$data_array1,1);
			if($rID1) $flag=1; else $flag=0;
		}
		//echo $delete_id.'=='; die;
		$delete_id="'".implode("','",explode(',',str_replace("'",'',$delete_id)))."'";
		if(str_replace("'",'',$delete_id)!="")
		{
			$rID3=execute_query( "delete from subcon_inbound_bill_dtls where delivery_id in ($delete_id)",0);
			if($rID3) $flag=1; else $flag=0;
			$delete_id=explode(",",str_replace("'",'',$delete_id));
			for ($i=0;$i<count($delete_id);$i++)
			{
				$id_delivery[]=$delete_id[$i];
				$data_delivery[str_replace("'",'',$delete_id[$i])] =explode(",",("0"));
			}
		}
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}		
		disconnect($con);
		die;
	}
}

if($action=="remarks_popup")
{
	echo load_html_head_contents("Remarks","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>
	function js_set_value(val)
	{
		document.getElementById('text_new_remarks').value=val;
		parent.emailwindow.hide();
	}
	</script>
    </head>
<body>
<div align="center">
	<fieldset style="width:400px;margin-left:4px;">
        <form name="remarksfrm_1"  id="remarksfrm_1" autocomplete="off">
            <table cellpadding="0" cellspacing="0" width="370" >
                <tr>
                    <td align="center"><input type="hidden" name="auto_id" id="auto_id" value="<? echo $data; ?>" />
                      <textarea id="text_new_remarks" name="text_new_remarks" class="text_area" title="Maximum 1000 Character" maxlength="1000" style="width:330px; height:270px" placeholder="Remarks Here. Maximum 1000 Character." ><? echo $data; ?></textarea>
                    </td>
                </tr>
                <tr>
                	<td align="center">
                 <input type="button" id="formbuttonplasminus" align="middle" class="formbutton" style="width:100px" value="Close" onClick="js_set_value(document.getElementById('text_new_remarks').value)" />
                 	</td>
                </tr>
            </table>
        </form>
    </fieldset>
</div>    
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}
?>
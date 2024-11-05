<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');
require_once('../../includes/class3/class.conditions.php');
require_once('../../includes/class3/class.reports.php');
require_once('../../includes/class3/class.conversions.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","","","","","",3 );
	exit();	 
}

if ($action=="load_drop_down_party_name")
{ 
	$data=explode('_',$data);
	if($data[1]==2)
	{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "","","","","","",5 );
	}
	else if($data[1]==1)
	{	
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select Party --", $selected, "","","","","","",5 ); 
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"","","","",5);
	}
	exit();
}

if ($action=="load_drop_down_party_name_popup")
{ 
	$data=explode('_',$data);
	if($data[1]==2)
	{
		echo create_drop_down( "cbo_party_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "","","","","","",5 );
	}
	else if($data[1]==1)
	{	
		echo create_drop_down( "cbo_party_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select Party --", $selected, "","","","","","",5 ); 
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 130, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"","","","",5);
	}
	exit();
}

if ($action=="load_variable_settings")
{
	echo "$('#variable_check').val(0);\n";
	echo "$('#bill_on').text('');\n"; 
	$sql_result = sql_select("select dyeing_fin_bill from  variable_settings_subcon where company_id='$data' and variable_list=1 order by id");
 	foreach($sql_result as $result)
	{
		echo "$('#variable_check').val(".$result[csf("dyeing_fin_bill")].");\n";
		if ($result[csf("dyeing_fin_bill")]==1)
		{
			echo "$('#bill_on').text('Bill On Grey Qty');\n"; 
		}
		else if ($result[csf("dyeing_fin_bill")]==2)
		{
			echo "$('#bill_on').text('Bill On Delivery Qty');\n"; 
		}
		else
		{
			echo "$('#bill_on').text('');\n"; 
		}
	}
 	exit();
}

if ($action=="bill_no_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	$exdata=explode('_',$data);
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('issue_id').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_chanllan(val)
		{
			if(val==1)
			{
				$('#txt_search_challan').removeAttr('disabled','disabled');
			}
			else
			{
				$('#txt_search_challan').attr('disabled','disabled');
			}
		}
	</script>
	</head>
	<body>
        <div align="center">
            <form name="dyingfinishingbill_1"  id="dyingfinishingbill_1" autocomplete="off">
                <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>                	 
                        <th width="130">Company Name</th>
                        <th width="100">Source</th>
                        <th width="130">Party Name</th>
                        <th width="80">Issue ID</th>
                        <th width="80">Rec. Challan No</th>
                        <th width="160">Bill Date Range</th>
                        <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>           
                    </thead>
                    <tbody>
                        <tr>
                            <td> 
                                <input type="hidden" id="issue_id">  
                                <?   
                                    echo create_drop_down( "cbo_company_id", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $exdata[0],"",0 );
                                ?>
                            </td>
                            <td width="100"><? echo create_drop_down( "cbo_party_source", 100, $knitting_source,"", 1, "-- Select Party --", $selected, "load_drop_down( 'sub_fabric_finishing_bill_issue_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_party_name_popup', 'party_td' ); fnc_chanllan(this.value); ",0,"1,2","","","",4); ?></td>
                            <td id="party_td"><? echo create_drop_down( "cbo_party_name", 130, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"","","","",5); ?></td>
                            <td><input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:75px" /></td>
                            <td><input type="text" name="txt_search_challan" id="txt_search_challan" class="text_boxes" style="width:75px" placeholder="Write" disabled /></td>
                            <td align="center"><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"></td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_search_challan').value, 'dyeingfinishing_bill_list_view', 'search_div', 'sub_fabric_finishing_bill_issue_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                            </td>
                        </tr>
                        <tr><td colspan="7" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td></tr>
                        <tr><td colspan="7" align="center" valign="top" id=""><div id="search_div"></div></td></tr>
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

if ($action=="dyeingfinishing_bill_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company_name=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $party_name=" and a.party_id='$data[1]'"; else $party_name="";
	
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $return_date = "and a.bill_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $return_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $return_date = "and a.bill_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $return_date ="";
	}
	
	if ($data[4]!='') $bill_id_cond=" and a.prefix_no_num='$data[4]'"; else $bill_id_cond="";
	//if ($data[5]!='') $recChallan_cond=" and challan_no='$data[5]'"; else $recChallan_cond="";
	if ($data[5]!='') $recChallan_cond=" and challan_no='$data[5]'"; else $recChallan_cond="";
	
	$rec_man_challan_arr=array();
	$sql_rec="select id, challan_no from inv_receive_master where status_active=1 and is_deleted=0 $recChallan_cond";
	$sql_rec_result = sql_select($sql_rec); $recId=""; $tot_rows=0;
	foreach($sql_rec_result as $row)
	{
		$tot_rows++;
		$rec_man_challan_arr[$row[csf("id")]]=$row[csf("challan_no")];
		$recId.="'".$row[csf("id")]."',";
	}
	unset($sql_rec_result);
	$rec_id_cond="";
	if ($data[5]!='')
	{
		$recIds=chop($recId,','); 
		if($db_type==2 && $tot_rows>1000)
		{
			$rec_id_cond=" and (";
			$recIdsArr=array_chunk(explode(",",$recIds),999);
			foreach($recIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$rec_id_cond.=" b.delivery_id in($ids) or ";
			}
			$rec_id_cond=chop($rec_id_cond,'or ');
			$rec_id_cond.=")";
		}
		else
		{
			$rec_id_cond=" and b.delivery_id in ($recIds)";
		}
	}
	
	$sub_del_challan_arr=array();
	$sql_sub_challan="select a.challan_no, b.id from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and status_active=1 and is_deleted=0 $recChallan_cond";
	$sql_sub_challan_result = sql_select($sql_sub_challan);
	foreach ($sql_sub_challan_result as $row)
	{
		$sub_del_challan_arr[$row[csf("id")]]=$row[csf("challan_no")];
	}
	unset($sql_sub_challan_result);
	
	$company_id=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$party_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	
	$arr=array (2=>$location,4=>$party_arr,5=>$knitting_source,6=>$bill_for);
	
	if($db_type==0)
	{
		$year_cond= "year(a.insert_date)as year";
		$delivery_id_cond="group_concat(b.delivery_id)";
	}
	else if($db_type==2)
	{
		$year_cond= "TO_CHAR(a.insert_date,'YYYY') as year";
		$delivery_id_cond="LISTAGG(CAST(b.delivery_id AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY b.delivery_id)";
	}
	
	$sql= "select a.id, a.bill_no, a.prefix_no_num, $year_cond, a.location_id, a.bill_date, a.party_id, a.party_source, a.bill_for, $delivery_id_cond as delivery_id from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.process_id=4 and a.status_active=1 $company_name $party_name $return_date $bill_id_cond $rec_id_cond group by a.id, a.bill_no, a.prefix_no_num, a.insert_date, a.location_id, a.bill_date, a.party_id, a.party_source, a.bill_for order by a.id DESC";
	?>
	<div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="60">Bill No</th>
                <th width="60">Year</th>
                <th width="110">Location</th>
                <th width="90">Source</th>
                <th width="60">Bill Date</th>
                <th width="120">Party</th>
                <th width="80">Bill For</th>
                <th>Challan No</th>
            </thead>
     	</table>
     </div>
     <div style="width:750px; max-height:250px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="list_view">
			<?
			$i=1; $result = sql_select($sql);
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$challan_no=""; $bill_company="";
				if($row[csf("party_source")]==1) 
				{
					$bill_company=$company_id[$row[csf("party_id")]];
					//$challan_no=$rec_man_challan_arr[$row[csf("delivery_id")]];
					$ex_del_id=explode(",",$row[csf("delivery_id")]);
					foreach($ex_del_id as $del_id)
					{
						if ($challan_no=="") $challan_no=$rec_man_challan_arr[$del_id]; else $challan_no.=','.$rec_man_challan_arr[$del_id];
					}
				}
				else 
				{
					$bill_company=$party_arr[$row[csf("party_id")]];
					$ex_del_id=explode("_",$row[csf("delivery_id")]);
					foreach($ex_del_id as $del_id)
					{
						if ($challan_no=="") $challan_no=$sub_del_challan_arr[$del_id]; else $challan_no.=','.$sub_del_challan_arr[$del_id];
					}
				}
				$unique_challan=implode(",",array_unique(explode(',',$challan_no)));
				
				//if($row[csf("party_source")]==1) $bill_company=$company_id[$row[csf("party_id")]]; else $bill_company=$party_arr[$row[csf("party_id")]];
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>);" > 
						<td width="30"><? echo $i; ?></td>
						<td width="60"><? echo $row[csf("prefix_no_num")]; ?></td>
                        <td width="60"><? echo $row[csf("year")]; ?></td>		
						<td width="110"><? echo $location_arr[$row[csf("location_id")]];  ?></td>	
                        <td width="90"><? echo $knitting_source[$row[csf("party_source")]];  ?></td>
						<td width="60"><? echo change_date_format($row[csf("bill_date")]); ?></td>
						<td width="120"><? echo $bill_company;?> </td>	
						<td width="80"><? echo $bill_for[$row[csf("bill_for")]]; ?></td>
                        <td style="word-wrap:break-word; word-break: break-all;"><? echo $unique_challan; ?>&nbsp;</td>
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

if ($action=="load_php_data_to_form_issue")
{
	$sql="SELECT min(delivery_date) as min_date, max(delivery_date) as max_date FROM subcon_inbound_bill_dtls WHERE mst_id='$data' and status_active=1 and is_deleted=0 group by mst_id";
	
	$sql_result_arr =sql_select($sql); 
	$mindate='';  $maxdate='';
	$mindate=$sql_result_arr[0][csf('min_date')];
	$maxdate=$sql_result_arr[0][csf('max_date')];
	unset($sql_result_arr);
	
	$nameArray= sql_select("select id, bill_no, company_id, location_id, bill_date, party_id, party_source, bill_for, is_posted_account,post_integration_unlock from subcon_inbound_bill_mst where id='$data'");
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_bill_no').value 					= '".$row[csf("bill_no")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "load_drop_down( 'requires/sub_fabric_finishing_bill_issue_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_location', 'location_td');\n";
		echo "document.getElementById('cbo_location_name').value			= '".$row[csf("location_id")]."';\n"; 
		echo "document.getElementById('txt_bill_date').value 				= '".change_date_format($row[csf("bill_date")])."';\n";   
		echo "document.getElementById('cbo_party_source').value				= '".$row[csf("party_source")]."';\n"; 
		echo "load_drop_down( 'requires/sub_fabric_finishing_bill_issue_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_source').value, 'load_drop_down_party_name', 'party_td' );\n";
		echo "document.getElementById('cbo_party_name').value				= '".$row[csf("party_id")]."';\n"; 
		echo "document.getElementById('hidden_acc_integ').value				= '".$row[csf("is_posted_account")]."';\n";
		echo "document.getElementById('hidden_integ_unlock').value			= '".$row[csf("post_integration_unlock")]."';\n";
		if($row[csf("is_posted_account")]==1 && $row[csf("post_integration_unlock")]==0)
		{
			echo "$('#accounting_integration_div').text('All Ready Posted in Accounting.');\n"; 
		}
		else if($row[csf("is_posted_account")]==1 && $row[csf("post_integration_unlock")]==1)
		{
			echo "$('#accounting_integration_div').text('Deleting not allowed since posted in Accounts.Only Data changing is allowed.');\n"; 
		}
		else 
		{
			echo "$('#accounting_integration_div').text('');\n"; 
		}
		echo "disable_enable_fields('cbo_party_name',1,'','');\n";	
		echo "document.getElementById('cbo_bill_for').value					= '".$row[csf("bill_for")]."';\n"; 
		echo "document.getElementById('txt_bill_form_date').value 			= '".change_date_format($mindate)."';\n";  
		echo "document.getElementById('txt_bill_to_date').value 			= '".change_date_format($maxdate)."';\n";  
	    echo "document.getElementById('update_id').value            		= '".$row[csf("id")]."';\n";
		//echo "show_list_view(document.getElementById('cbo_party_source').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('update_id').value+'_'+document.getElementById('issue_id_all').value,'dyingfinishing_delivery_list_view','dyeingfinishing_info_list','requires/sub_fabric_finishing_bill_issue_controller','set_all()');\n";
		echo "set_button_status(1, permission, 'fnc_dyeing_finishing_bill_issue',1);\n";
	}
	exit();
}

if ($action=="dyingfinishing_delivery_list_view")
{
	echo load_html_head_contents("Popup Info","../", 1, 1, $unicode,1,'');
	//echo $data;
	$data=explode('***',$data);
	$ex_bill_for=$data[4];
	$ex_bill_for=$data[4];
	$date_from=$data[5];
	$date_to=$data[6];
	$manual_challan_no=$data[7];
	$variable_check=$data[8];
	$update_id=$data[9];
	$str_data=trim($data[10]);
	//print_r($str_data);

	if($db_type==0)
	{ 
		if ($date_from!="" &&  $date_to!="") $date_cond= "and a.receive_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $date_cond= "";
		$year_cond= "year(b.insert_date)as year";

	}
	else if ($db_type==2)
	{
		if ($date_from!="" &&  $date_to!="") $date_cond= "and a.receive_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";  else $date_cond= "";
		$year_cond= "TO_CHAR(b.insert_date,'YYYY') as year";
	}
	$ex_str_data=explode("!!!!",$str_data);
	$str_arr=array();
	foreach($ex_str_data as $str)
	{
		$str_arr[]=$str;
	}
	if($data[2]==2)
	{
		$delv_id=implode(',',explode('!!!!',$str_data));
		?>
            <div >
                <table cellspacing="0" cellpadding="0" border="1" rules="all"  align="center" width="1000px" class="rpt_table">
                    <thead>
                    	<th width="30">&nbsp;</th>
						<th width="30">SL</th>
						<th width="90">Process</th>
						<th width="80">Challan No</th>
						<th width="60">Delivery Date</th>
						<th width="100">Order No</th>
                        <th width="100">Batch No</th>
                        <th width="110">Sub-Process</th>                    
						<th width="130">Fabric Description</th>
                        <th width="70">Grey Used Qty</th>
						<th width="70">Delivery Qty</th>
						<th>Currency</th>
                    </thead>
                 </table>
            </div>
            <div style="width:1000px;max-height:180px; overflow-y:scroll" id="sewing_production_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="980px" class="rpt_table" id="tbl_list_search">
				<? 
				$color_name=return_library_array( "select id, color_name from lib_color",'id','color_name');
				$rec_febricdesc_arr=return_library_array( "select id,material_description from sub_material_dtls",'id','material_description');
				$prod_febricdesc_arr=return_library_array( "select id,fabric_description from subcon_production_dtls",'id','fabric_description');
				$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
				
				$grey_qty_array=array();
				$grey_fabric_array=array();
				$grey_sql="Select b.id, b.fabric_from, b.po_id, b.id, b.item_description, b.fin_dia, b.batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form=36 and b.status_active=1 and b.is_deleted=0";
				$grey_sql_result =sql_select($grey_sql);
				
				foreach($grey_sql_result as $row)
				{
					$item_name=explode(',',$row[csf('item_description')]);
					$grey_qty_array[$row[csf('po_id')]][$row[csf('id')]]=$row[csf('batch_qnty')];
					$grey_fabric_array[$row[csf('id')]]=$row[csf('item_description')];
				}
				unset($grey_sql_result);
				// var_dump($grey_qty_array);
				$order_arr=array();
				$sql_ord=sql_select("select a.subcon_job, a.job_no_prefix_num, a.currency_id, b.id, b.order_no, b.cust_style_ref, b.cust_buyer from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst ");
				
				foreach($sql_ord as $row)
				{
					$order_arr[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
					$order_arr[$row[csf('id')]]['currency']=$row[csf('currency_id')];
					$order_arr[$row[csf('id')]]['po']=$row[csf('order_no')];
					$order_arr[$row[csf('id')]]['style']=$row[csf('cust_style_ref')];
					$order_arr[$row[csf('id')]]['buyer']=$row[csf('cust_buyer')];
				}
				unset($sql_ord);
				
				/*return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$currency_arr=return_library_array( "select b.id, a.currency_id from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst",'id','currency_id');*/
                $i=1;
				//$old_delivery_id=implode(',',explode('_',$data[3]));
				if($db_type==0)
				{
					if(!$update_id)
					{
						$sql="select a.challan_no, b.width_dia_type, a.delivery_date, b.sub_process_id,b.color_id, b.batch_id, b.order_id, group_concat(b.id SEPARATOR '_') as id, group_concat(b.process_id SEPARATOR '_') as process_id, group_concat(b.item_id SEPARATOR '_') as item_id, sum(b.gray_qty) as gray_qty, sum(b.delivery_qty) as delivery_qty, 0 as type from  subcon_delivery_mst a, subcon_delivery_dtls b where b.bill_status=0 and a.id=b.mst_id and a.party_id='$data[3]' and b.process_id in (3,4) and a.status_active=1 and a.is_deleted=0 group by a.challan_no, b.width_dia_type, a.delivery_date, b.sub_process_id, b.color_id, b.batch_id, b.order_id"; 
					}
					else
					{
						$sql="(select a.challan_no, b.width_dia_type, a.delivery_date, b.sub_process_id, b.color_id, b.batch_id, b.order_id, group_concat(b.id SEPARATOR '_') as id, group_concat(b.process_id SEPARATOR '_') as process_id, group_concat(b.item_id SEPARATOR '_') as item_id,sum(b.gray_qty) as gray_qty, sum(b.delivery_qty) as delivery_qty, 0 as type from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.party_id='$data[3]' and b.process_id in (3,4) and a.status_active=1 and b.bill_status=0  group by a.challan_no, b.width_dia_type, a.delivery_date,b.sub_process_id, b.color_id, b.batch_id, b.order_id)
						 union 
						 (select a.challan_no, b.width_dia_type, a.delivery_date,b.sub_process_id, b.color_id, b.batch_id, b.order_id, group_concat(b.id SEPARATOR '_') as id, group_concat(b.process_id SEPARATOR '_') as process_id, group_concat(b.item_id SEPARATOR '_') as item_id,sum(b.gray_qty) as gray_qty, sum(b.delivery_qty) as delivery_qty, 1 as type from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.party_id='$data[3]' and b.process_id in (3,4) and b.id in ($delv_id) and a.status_active=1 group by a.challan_no, b.width_dia_type, a.delivery_date,b.sub_process_id, b.color_id, b.batch_id, b.order_id) order by type DESC";
					}
				}
				else if ($db_type==2)
				{
					if(!$update_id)
					{
						$sql="select a.challan_no, b.width_dia_type, a.delivery_date, b.sub_process_id, b.color_id, b.batch_id, b.order_id,
						listagg(b.id,'_') within group (order by b.id) as id,
						listagg(b.process_id,'_') within group (order by b.process_id) as process_id,
						listagg(b.item_id,'_') within group (order by b.item_id) as item_id,sum(b.gray_qty) as gray_qty,
						sum(b.delivery_qty) as delivery_qty, 0 as type
		from  subcon_delivery_mst a, subcon_delivery_dtls b where b.bill_status=0 and a.id=b.mst_id and a.party_id='$data[3]' and a.process_id=4 and b.process_id in (3,4) and a.status_active=1 and a.is_deleted=0 group by a.challan_no, b.width_dia_type, a.delivery_date, b.sub_process_id, b.color_id, b.batch_id, b.order_id order by a.challan_no"; 
					}
					else
					{
						$sql="(select a.challan_no, b.width_dia_type, a.delivery_date, b.sub_process_id, b.color_id, b.batch_id, b.order_id,
						listagg(b.id,'_') within group (order by b.id) as id,
						listagg(b.process_id,'_') within group (order by b.process_id) as process_id,
						listagg(b.item_id,'_') within group (order by b.item_id) as item_id,sum(b.gray_qty) as gray_qty,
						sum(b.delivery_qty) as delivery_qty, 0 as type 
						from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.party_id='$data[3]' and a.process_id=4 and b.process_id in (3,4) and a.status_active=1 and b.bill_status=0 group by a.challan_no, b.width_dia_type, a.delivery_date,b.sub_process_id, b.color_id, b.batch_id, b.order_id)
						 union 
						 (select a.challan_no, b.width_dia_type, a.delivery_date,b.sub_process_id, b.color_id, b.batch_id, b.order_id,
						 listagg(b.id,'_') within group (order by b.id) as id,
						listagg(b.process_id,'_') within group (order by b.process_id) as process_id,
						listagg(b.item_id,'_') within group (order by b.item_id) as item_id,sum(b.gray_qty) as gray_qty,
						sum(b.delivery_qty) as delivery_qty, 1 as type 
						  from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.party_id='$data[3]' and a.process_id=4 and b.process_id in (3,4) and b.id in ($delv_id) and a.status_active=1 and b.bill_status=1 group by a.challan_no, b.width_dia_type, a.delivery_date,b.sub_process_id, b.color_id, b.batch_id, b.order_id) order by type DESC";
					}
				}
				//echo $sql;
				$sql_result =sql_select($sql);
					
				foreach($sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$checked_val=2; $ischeck="";
					if ($row[csf('type')]==0) 
					{
						$row_color=$bgcolor; 
						$checked_val=2;
						$ischeck="";
					}
					else 
					{
						$bgcolor="Yellow";
						$checked_val=1;
						$ischeck="checked";
					}
					
					$sub_process_id=array_unique(explode(',',$row[csf('sub_process_id')]));
					$subprocess_val='';
					foreach ($sub_process_id as $val)
					{
						if($subprocess_val=='') $subprocess_val=$conversion_cost_head_array[$val]; else $subprocess_val.=" + ".$conversion_cost_head_array[$val];
					}
					
					$process_id=array_unique(explode('_',$row[csf('process_id')]));
					$process_val=''; $process_ids='';
					foreach ($process_id as $val)
					{
						if($process_val=='') $process_val=$production_process[$val]; else $process_val.=", ".$production_process[$val];
						if($process_ids=='') $process_ids=$val; else $process_ids.=", ".$val;
					}
					
					$delivery_id=array_unique(explode(',',$row[csf('id')]));
					$delivery_id_val='';
					foreach ($delivery_id as $val)
					{
						if($delivery_id_val=='') $delivery_id_val=$val; else $delivery_id_val.="_".$val;
					}
					
					$item_id=array_unique(explode('_',$row[csf('item_id')]));
					$item_name=''; $grey_qty=0;
					foreach ($item_id as $val)
					{
						if($item_name=='') $item_name=$grey_fabric_array[$val]; else $item_name.=",<br>".$grey_fabric_array[$val];
						$grey_qty+=$grey_qty_array[$row[csf('order_id')]][$val];
					}
					
					if($variable_check==1)
					{
						$on_bill_qty=$row[csf('gray_qty')];
					}
					else
					{
						$on_bill_qty=$row[csf('delivery_qty')];
					}

					$str_val=$row[csf('id')].'**'.change_date_format($row[csf('delivery_date')]).'**'.$row[csf('challan_no')].'**'.$row[csf('order_id')].'**'.$order_arr[$row[csf('order_id')]]['po'].'**'.$order_arr[$row[csf('order_id')]]['style'].'**'.$order_arr[$row[csf('order_id')]]['buyer'].'**'.$order_arr[$row[csf('order_id')]]['job'].'**********'.$row[csf('item_id')].'**'.$item_name.'**'.$row[csf('batch_id')].'**'.$row[csf('color_id')].'**'.$color_name[$row[csf('color_id')]].'**'.$row[csf('sub_process_id')].'**'.$subprocess_val.'**'.$row[csf('width_dia_type')].'**'.$fabric_typee[$row[csf('width_dia_type')]].'**'.number_format($on_bill_qty, 2, '.', '').'****************'.$process_ids;
					
					?>
					<tr id="tr_<? echo $row[csf('id')]; ?>" bgcolor="<? echo $bgcolor; ?>"  style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."***".$order_arr[$row[csf('order_id')]]['currency']; ?>');" >
                    	<td width="30" align="center"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="<? echo $checked_val; ?>" <? echo $ischeck; ?> ></td>
						<td width="30"><? echo $i; ?></td>
                        <td width="90"><? echo $process_val; ?></td>
						<td width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
						<td width="60"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
						<td width="100"><? echo $order_arr[$row[csf('order_id')]]['po']; ?></td>
                        <td width="100"><? echo $batch_arr[$row[csf('batch_id')]]; ?></td>
                        <td width="110"><? echo $subprocess_val; ?></td>
						<td width="130"><p><? echo $item_name; ?></p></td>

                        <td width="70" align="right"><? echo $row[csf('gray_qty')]; ?>&nbsp;</td>

						<td width="70" align="right"><? echo $row[csf('delivery_qty')]; ?>&nbsp;</td>
						<td><? echo $currency[$order_arr[$row[csf('order_id')]]['currency']]; ?>
                        
                        <input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
						<input type="hidden" id="currid<? echo $row[csf('id')]; ?>" style="width:50px" value="<? echo $order_arr[$row[csf('order_id')]]['currency']; ?>"></td>
					</tr>
					<?
					$i++;
				}
				?>
		   </table>
		</div>
		   <table width="933px">
				<tr align="center">
					<td align="center">
						<input type="button" id="show_button" align="middle" class="formbutton" style="width:100px" value="Close" onClick="window_close(0)" />
					</td>
				</tr>
		   </table>
	</body>           
	<script src="../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
    <?
	}
	else if($data[2]==1)
	{
		?>
        	<div id="list_view_body">
            <div >
                <table cellspacing="0" cellpadding="0" border="1" rules="all"  align="center" width="1017px" class="rpt_table">
                    <thead>
                    	<th width="30">&nbsp;</th>
                        <th width="30">SL</th>
                        <th width="50">Sys. Challan</th>
                        <th width="50">Rec. Challan</th>
                        <th width="60">Rec. Date</th>
                        <th width="70">Color</th>
                        <th width="50">Job No</th>
                        <th width="90">Style Ref.</th>
                        <th width="80">Order No</th>
                        <th width="70">Batch No</th>
                        <th width="30">Ext.</th>
                        <th width="100">Sub-Process</th>                    
                        <th width="120">Fabric Description</th>
                        <th width="70">Dia/Width Type</th>
                        <th width="60">Grey Qty</th>
                        <th>Rec. Qty</th>
                    </thead>
                 </table>
            </div>
            <div style="width:1017px;max-height:180px; overflow-y:scroll" id="sewing_production_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="998px" class="rpt_table" id="tbl_list_search">
				<? 
				$product_dtls_arr=return_library_array( "select id, product_name_details from product_details_master",'id','product_name_details');
				$color_name=return_library_array( "select id, color_name from lib_color",'id','color_name');
				$recive_basis_arr=return_library_array( "select id, receive_basis from inv_receive_master",'id','receive_basis');
				$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
				$bill_qty_array=array();
				$sql_bill="select challan_no, order_id, febric_description_id, body_part_id, item_id, batch_id, sum(packing_qnty) as roll_qty, sum(delivery_qty) as bill_qty, dia_width_type from subcon_inbound_bill_dtls where status_active=1 and is_deleted=0 group by challan_no, order_id, febric_description_id, body_part_id, item_id, batch_id, dia_width_type";
				 
				$sql_bill_result =sql_select($sql_bill);
				foreach ($sql_bill_result as $row)
				{
					$bill_qty_array[$row[csf('challan_no')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('dia_width_type')]][$row[csf('batch_id')]]['qty']=$row[csf('bill_qty')];
				}
				unset($sql_bill_result);
				//print_r($bill_qty_array);
				
				$batch_array=array();
				$grey_sql="Select a.id, a.batch_no, a.extention_no, a.process_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form!=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.extention_no, a.process_id";
				$grey_sql_result =sql_select($grey_sql);
				
				foreach($grey_sql_result as $row)
				{
					$batch_array[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
					$batch_array[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
					$batch_array[$row[csf('id')]]['process_id']=$row[csf('process_id')];
					$batch_array[$row[csf('id')]]['batch_qnty']=$row[csf('batch_qnty')];
				}
				unset($grey_sql_result);
				// var_dump($grey_qty_array);
				$po_array=array();
				$po_sql=sql_select( "select a.style_ref_no, a.job_no_prefix_num, a.buyer_name, b.id, b.po_number from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				foreach($po_sql as $row)
				{
					$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
					$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
					$po_array[$row[csf('id')]]['order']=$row[csf('po_number')];
					$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
				}
				unset($po_sql);
                $i=1;
				if($manual_challan_no!='') $manual_challan_cond=" and a.challan_no='$manual_challan_no'"; else  $manual_challan_cond="";
				if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2) $bill_for_id="SM";
				if($ex_bill_for==1) $bill_for_cond=" and d.batch_against='1'"; else if($ex_bill_for==2) $bill_for_cond=" and d.batch_against='3'"; else if($ex_bill_for==3) $bill_for_cond=" and d.booking_without_order='1'";
				//if($ex_bill_for!=3)
				if($ex_bill_for!=3) 
				{
					if($db_type==0)
					{
						$year_cond="year(a.insert_date)";
						$booking_without_order="IFNULL(d.booking_without_order,0)";
					}
					else if($db_type==2) 
					{
						$year_cond="TO_CHAR(a.insert_date,'YYYY')";
						$booking_without_order="nvl(d.booking_without_order,0)";
					}
					
					$sql="SELECT a.id, a.entry_form, a.receive_basis, a.recv_number_prefix_num, a.challan_no, a.receive_date, a.booking_id as bookingno, $year_cond as year, b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.color_id, b.dia_width_type, sum(c.quantity) as rec_qnty, sum(b.no_of_roll) as carton_roll, c.po_breakdown_id, d.booking_no_id, d.booking_no
							FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d
							WHERE a.id=b.mst_id and b.id=c.dtls_id and d.id=b.batch_id and c.trans_type=1 and c.entry_form in (7,37,66,68) and c.trans_id!=0 and a.entry_form in (7,37,66,68) AND a.knitting_source=1 AND a.company_id=$data[3] AND a.location_id=$data[1] AND a.knitting_company=$data[0] and a.receive_basis in (2,4,5,9,11) and a.item_category=2 
							and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.batch_against in (0,1,3) and $booking_without_order=0 $date_cond $manual_challan_cond
							group by a.id, a.entry_form, a.receive_basis, a.recv_number_prefix_num, a.challan_no, a.receive_date, a.booking_id, a.insert_date, b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.color_id, b.dia_width_type, c.po_breakdown_id, d.booking_no_id, d.booking_no order by a.recv_number_prefix_num DESC";
							
				}
				else
				{
					if($db_type==0)
					{
						$year_cond="year(a.insert_date)";
						$booking_without_order="IFNULL(d.booking_without_order,0)";
					}
					else if($db_type==2) 
					{
						$year_cond="TO_CHAR(a.insert_date,'YYYY')";
						$booking_without_order="nvl(d.booking_without_order,0)";
					}
					$sql="SELECT a.id, a.entry_form, a.receive_basis, a.recv_number_prefix_num, a.challan_no, a.receive_date, null as bookingno, $year_cond as year, b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.color_id, b.dia_width_type, sum(b.receive_qnty) as rec_qnty, sum(b.no_of_roll) as carton_roll, null as po_breakdown_id, null as booking_no_id, null as booking_no 
						FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst d
						WHERE a.id=b.mst_id and d.id=b.batch_id and a.entry_form in (7,37,66,68) AND a.knitting_source=1 AND a.company_id=$data[3] AND a.location_id=$data[1] AND a.knitting_company=$data[0] and a.receive_basis in(2,4,5,9,11) and d.batch_against in (3,5) and $booking_without_order=1 and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $manual_challan_cond
						group by a.id, a.entry_form, a.receive_basis, a.recv_number_prefix_num, a.challan_no, a.receive_date, a.insert_date, b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.color_id, b.dia_width_type order by a.recv_number_prefix_num DESC";

				}
				//echo $sql;
				$sql_result =sql_select($sql);
				
				foreach($sql_result as $row) // for update row
				{
					$all_value=$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$row[csf('prod_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('fabric_description_id')].'_'.$row[csf('dia_width_type')].'_'.$row[csf('batch_id')];
					if(in_array($all_value,$str_arr))
					{
						$booking_no=0; $independent=''; $bill_for_id=0; $bill_for_sb=0;
						if ($row[csf('entry_form')]==7)
						{
							if($ex_bill_for==1) { if($row[csf('receive_basis')]==4) $independent=4; } //else $independent='';
							if ($row[csf('receive_basis')]==5) $booking_no=$row[csf('booking_no')]; else $booking_no=0;
							if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2) $bill_for_id="SM"; else if($ex_bill_for==3) $bill_for_id="SMN";
						}
						else if ($row[csf('entry_form')]==37)
						{
							if ($row[csf('receive_basis')]==9) 
							{
								if($recive_basis_arr[$row[csf('bookingno')]]==4) $independent=4; //else $row[csf('receive_basis')]=5;
							}
							
							if($ex_bill_for==1) { if($row[csf('receive_basis')]==4) $independent=4; }// else $independent='';
							if($row[csf('receive_basis')]==2 || $row[csf('receive_basis')]==11) 
								$booking_no=$row[csf('booking_no')];
							else if($row[csf('receive_basis')]==9 && $recive_basis_arr[$row[csf('bookingno')]]==5) 
								$booking_no=$row[csf('booking_no')];
							else 
								$booking_no=0;
							if($ex_bill_for==1) { $bill_for_id="Fb"; $bill_for_sb="SB"; } else if($ex_bill_for==2 && $row[csf('receive_basis')]!=4) $bill_for_id="SM";  else if($ex_bill_for==3) $bill_for_id="SMN";
						}
						else if ($row[csf('entry_form')]==66)
						{
							$rec_basis=0; $bookinNo=""; $bookingId=0;// booking id is barcode, and booking no is delv id
							$rec_basis=0;
							$bookinNo=$row[csf('booking_no')];
							$bookingId=$row[csf('booking_id')];
							
							if($ex_bill_for==1) { if($rec_basis==0) $independent=4; } //else $independent='';
							if ($rec_basis==0) $booking_no=$bookinNo; else $booking_no=0;
							if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2 && $rec_basis!=0) $bill_for_id="SM"; else if($ex_bill_for==3) $bill_for_id="SMN";
						}
						else if ($row[csf('entry_form')]==68)
						{
							$rec_basis=0; $bookinNo=""; $bookingId=0;// booking id is barcode, and booking no is delv id
							$rec_basis=0;
							$bookinNo=$row[csf('booking_no')];
							$bookingId=$row[csf('booking_id')];
							
							if ($ex_bill_for==1) { if($rec_basis==0) $independent=4; } //else $independent='';
							if ($rec_basis==0) $booking_no=$bookinNo; else $booking_no=0;
							if ($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2 && $rec_basis!=0) $bill_for_id="SM"; else if($ex_bill_for==3) $bill_for_id="SMN";
						}
						
						$ex_booking="";
						if($booking_no!='') $ex_booking=explode('-',$booking_no); else $ex_booking="_";
						$process_id=array_unique(explode(',',$batch_array[$row[csf('batch_id')]]['process_id']));
						$process_name='';
						foreach ($process_id as $val)
						{
							if($process_name=='') $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
						}
						$on_bill_qty=0;
						if ($variable_check==1) $on_bill_qty=$row[csf('rec_qnty')]; //$batch_array[$row[csf('batch_id')]]['batch_qnty'];
						else $on_bill_qty=$row[csf('rec_qnty')];
						$str_val=$row[csf('id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$po_array[$row[csf('po_breakdown_id')]]['order'].'_'.$po_array[$row[csf('po_breakdown_id')]]['style'].'_'.$buyer_arr[$po_array[$row[csf('po_breakdown_id')]]['buyer']].'_'.$po_array[$row[csf('po_breakdown_id')]]['job'].'_'.$row[csf('carton_roll')].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('fabric_description_id')].'_'.$row[csf('prod_id')].'_'.$product_dtls_arr[$row[csf('prod_id')]].'_'.$row[csf('batch_id')].'_'.$row[csf('color_id')].'_'.$color_name[$row[csf("color_id")]].'_'.$batch_array[$row[csf('batch_id')]]['process_id'].'_'.$process_name.'_'.$row[csf('dia_width_type')].'_'.$fabric_typee[$row[csf('dia_width_type')]].'_'.number_format($on_bill_qty, 2, '.', '');
						
						if($independent==4)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
                            <tr id="tr_<? echo $all_value; ?>" bgcolor="yellow" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value."***".'1'; ?>');" >
                            	<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="1" checked ></td>
								<td width="30" align="center"><? echo $i; ?></td>
								<td width="50" align="center"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
								<td width="50" align="center"><? echo $row[csf('challan_no')]; ?></td>
								<td width="60"><? echo change_date_format($row[csf('receive_date')]); ?></td>
								<td width="70"><p><? echo $color_name[$row[csf('color_id')]]; ?></p></td>
								<td width="50" align="center"><? echo $po_array[$row[csf('po_breakdown_id')]]['job']; ?></td>
								<td width="90" ><p><? echo $po_array[$row[csf('po_breakdown_id')]]['style']; ?></p></td>
								<td width="80"><p><? echo $po_array[$row[csf('po_breakdown_id')]]['order']; ?></p></td>
								<td width="70"><p><? echo $batch_array[$row[csf('batch_id')]]['batch_no']; ?></p></td>
								<td width="30"><? echo $batch_array[$row[csf('batch_id')]]['extention_no']; ?></td>
								<td width="100"><p><? echo $process_name; ?></p>&nbsp;</td>
								<td width="120"><p><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></p></td>
								<td width="70"><p><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></p></td>
								<td width="60" align="right"><? echo number_format($batch_array[$row[csf('batch_id')]]['batch_qnty'],2,'.',''); ?></td>
								<td align="right"><? echo number_format($row[csf('rec_qnty')],2,'.',''); ?>
                                <input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
								<input type="hidden" id="currid<? echo $all_value; ?>" style="width:50px" value="<? echo '1'; ?>"></td>
							</tr>
							<?php
							$i++;
						}
						else
						{
							if( strtolower($ex_booking[1])==strtolower($bill_for_id) || strtolower($ex_booking[1])==strtolower($bill_for_sb)) 
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr id="tr_<? echo $all_value; ?>"  bgcolor="yellow" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value."***".'1'; ?>');" >
                                    <td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="1" checked ></td>
                                    <td width="30" align="center"><? echo $i; ?></td>
                                    <td width="50" align="center"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
                                    <td width="50" align="center"><? echo $row[csf('challan_no')]; ?></td>
                                    <td width="60"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                                    <td width="70"><p><? echo $color_name[$row[csf('color_id')]]; ?></p></td>
                                    <td width="50" align="center"><? echo $po_array[$row[csf('po_breakdown_id')]]['job']; ?></td>
                                    <td width="90" ><p><? echo $po_array[$row[csf('po_breakdown_id')]]['style']; ?></p></td>
                                    <td width="80"><p><? echo $po_array[$row[csf('po_breakdown_id')]]['order']; ?></p></td>
                                    <td width="70"><p><? echo $batch_array[$row[csf('batch_id')]]['batch_no']; ?></p></td>
                                    <td width="30"><? echo $batch_array[$row[csf('batch_id')]]['extention_no']; ?></td>
                                    <td width="100" ><p><? echo $process_name; ?></p>&nbsp;</td>
                                    <td width="120"><p><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></p></td>
                                    <td width="70"><p><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></p></td>
                                    <td width="60" align="right"><? echo number_format($batch_array[$row[csf('batch_id')]]['batch_qnty'],2,'.',''); ?></td>
                                    <td align="right"><? echo number_format($row[csf('rec_qnty')],2,'.',''); ?>
                                    <input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
                                    <input type="hidden" id="currid<? echo $all_value; ?>" style="width:50px" value="<? echo '1'; ?>"></td>
                                </tr>
								<?php
								$i++;
							}
						}
					}
				}
			
				foreach($sql_result as $row) // for new row
				{
					$booking_no=0; $independent=''; $bill_for_id=0; $bill_for_sb=0;
					if ($row[csf('entry_form')]==7)
					{
						if($ex_bill_for==1) { if($row[csf('receive_basis')]==4) $independent=4; } //else $independent='';
						if ($row[csf('receive_basis')]==5) $booking_no=$row[csf('booking_no')]; else $booking_no=0;
						if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2) $bill_for_id="SM"; else if($ex_bill_for==3) $bill_for_id="SMN";
					}
					else if ($row[csf('entry_form')]==37)
					{
						if ($row[csf('receive_basis')]==9) 
						{
							if($recive_basis_arr[$row[csf('bookingno')]]==4) $independent=4;
						}
						
						if($ex_bill_for==1) { if($row[csf('receive_basis')]==4) $independent=4; }// else $independent='';
						if($row[csf('receive_basis')]==2 || $row[csf('receive_basis')]==11) 
							$booking_no=$row[csf('booking_no')];
						else if($row[csf('receive_basis')]==9 && $recive_basis_arr[$row[csf('bookingno')]]==5) 
							$booking_no=$row[csf('booking_no')];
						else 
							$booking_no=0;
						if($ex_bill_for==1) { $bill_for_id="Fb"; $bill_for_sb="SB"; } else if($ex_bill_for==2 && $row[csf('receive_basis')]!=4) $bill_for_id="SM"; else if($ex_bill_for==3) $bill_for_id="SMN";
					}
					else if ($row[csf('entry_form')]==66)
					{
						$rec_basis=0; $bookinNo=""; $bookingId=0;// booking id is barcode, and booking no is delv id
						$rec_basis=0;
						$bookinNo=$row[csf('booking_no')];
						$bookingId=$row[csf('booking_id')];
						
						if($ex_bill_for==1) { if($rec_basis==0) $independent=4; } //else $independent='';
						if ($rec_basis==0) $booking_no=$bookinNo; else $booking_no=0;
						if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2 && $rec_basis!=0) $bill_for_id="SM"; else if($ex_bill_for==3) $bill_for_id="SMN";
					}
					else if ($row[csf('entry_form')]==68)
					{
						$rec_basis=0; $bookinNo=""; $bookingId=0;// booking id is barcode, and booking no is delv id
						$rec_basis=0;
						$bookinNo=$row[csf('booking_no')];
						$bookingId=$row[csf('booking_id')];
						
						if ($ex_bill_for==1) { if($rec_basis==0) $independent=4; } //else $independent='';
						if ($rec_basis==0) $booking_no=$bookinNo; else $booking_no=0;
						if ($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2 && $rec_basis!=0) $bill_for_id="SM"; else if($ex_bill_for==3) $bill_for_id="SMN";
					}
					$ex_booking=""; $bill_qty=0;
					if($booking_no!='') $ex_booking=explode('-',$booking_no); else $ex_booking="_";
					//echo $row[csf('booking_no')];
					//if($ex_booking[1]!='Fb') echo $ex_booking[1];
					$bill_qty=$bill_qty_array[$row[csf('recv_number_prefix_num')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('dia_width_type')]][$row[csf('batch_id')]]['qty'];
					//$bill_qty_array[$row[csf('challan_no')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('dia_width_type')]][$row[csf('batch_id')]]['qty']
					
					$avilable_qty=$row[csf('rec_qnty')]-$bill_qty;
					$avilable_roll=$roll_no_arr[$row[csf('dtls_id')]]-$roll_qty;
					$all_value=$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$row[csf('prod_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('fabric_description_id')].'_'.$row[csf('dia_width_type')].'_'.$row[csf('batch_id')];
					
					$process_id=array_unique(explode(',',$batch_array[$row[csf('batch_id')]]['process_id']));
					$process_name='';
					foreach ($process_id as $val)
					{
						if($process_name=='') $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
					}
					
					$on_bill_qty=0;
					if ($variable_check==1) $on_bill_qty=$row[csf('rec_qnty')];//$batch_array[$row[csf('batch_id')]]['batch_qnty'];
					else $on_bill_qty=$row[csf('rec_qnty')];
					
					$str_val=$row[csf('id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$po_array[$row[csf('po_breakdown_id')]]['order'].'_'.$po_array[$row[csf('po_breakdown_id')]]['style'].'_'.$buyer_arr[$po_array[$row[csf('po_breakdown_id')]]['buyer']].'_'.$po_array[$row[csf('po_breakdown_id')]]['job'].'_'.$row[csf('carton_roll')].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('fabric_description_id')].'_'.$row[csf('prod_id')].'_'.$product_dtls_arr[$row[csf('prod_id')]].'_'.$row[csf('batch_id')].'_'.$row[csf('color_id')].'_'.$color_name[$row[csf("color_id")]].'_'.$batch_array[$row[csf('batch_id')]]['process_id'].'_'.$process_name.'_'.$row[csf('dia_width_type')].'_'.$fabric_typee[$row[csf('dia_width_type')]].'_'.number_format($on_bill_qty, 2, '.', '');
					if($independent==4)
					{
						if($avilable_qty>0)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
                            <tr id="tr_<? echo $all_value; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value."***".'1'; ?>');" >
                            	<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="2" ></td>
								<td width="30" align="center"><? echo $i; ?></td>
								<td width="50" align="center"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
								<td width="50" align="center"><? echo $row[csf('challan_no')]; ?></td>
								<td width="60"><? echo change_date_format($row[csf('receive_date')]); ?></td>
								<td width="70"><p><? echo $color_name[$row[csf('color_id')]]; ?></p></td>
								<td width="50" align="center"><? echo $po_array[$row[csf('po_breakdown_id')]]['job']; ?></td>
								<td width="90"><p><? echo $po_array[$row[csf('po_breakdown_id')]]['style']; ?></p></td>
								<td width="80"><p><? echo $po_array[$row[csf('po_breakdown_id')]]['order']; ?></p></td>
								<td width="70"><p><? echo $batch_array[$row[csf('batch_id')]]['batch_no']; ?></p></td>
								<td width="30"><? echo $batch_array[$row[csf('batch_id')]]['extention_no']; ?></td>
								<td width="100"><p><? echo $process_name; ?></p>&nbsp;</td>
								<td width="120"><p><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></p></td>
								<td width="70"><p><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></p></td>
								<td width="60" align="right"><? echo number_format($batch_array[$row[csf('batch_id')]]['batch_qnty'],2,'.',''); ?></td>
								<td align="right"><? echo number_format($row[csf('rec_qnty')],2,'.',''); ?>
                                <input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
								<input type="hidden" id="currid<? echo $all_value; ?>" style="width:50px" value="<? echo '1'; ?>"></td>
							</tr>
							<?php
							$i++;
						}
					}
					else
					{
						if( strtolower($ex_booking[1])==strtolower($bill_for_id) || strtolower($ex_booking[1])==strtolower($bill_for_sb)) 
						{
							if($avilable_qty>0)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr id="tr_<? echo $all_value; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value."***".'1'; ?>');" >
                                    <td width="30" align="center"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="2" ></td>
                                    <td width="30" align="center"><? echo $i; ?></td>
                                    <td width="50" align="center"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
                                    <td width="50" align="center"><? echo $row[csf('challan_no')]; ?></td>
                                    <td width="60"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                                    <td width="70"><p><? echo $color_name[$row[csf('color_id')]]; ?></p></td>
                                    <td width="50" align="center"><? echo $po_array[$row[csf('po_breakdown_id')]]['job']; ?></td>
                                    <td width="90" ><p><? echo $po_array[$row[csf('po_breakdown_id')]]['style']; ?></p></td>
                                    <td width="80"><p><? echo $po_array[$row[csf('po_breakdown_id')]]['order']; ?></p></td>
                                    <td width="70"><p><? echo $batch_array[$row[csf('batch_id')]]['batch_no']; ?></p></td>
                                    <td width="30"><? echo $batch_array[$row[csf('batch_id')]]['extention_no']; ?></td>
                                    <td width="100" ><p><? echo $process_name; ?></p>&nbsp;</td>
                                    <td width="120"><p><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></p></td>
                                    <td width="70"><p><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></p></td>
                                    <td width="60" align="right"><? echo number_format($batch_array[$row[csf('batch_id')]]['batch_qnty'],2,'.',''); ?></td>
                                    <td align="right"><? echo number_format($row[csf('rec_qnty')],2,'.',''); ?>
                                    
                                    <input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
                                    <input type="hidden" id="currid<? echo $all_value; ?>" style="width:50px" value="<? echo '1'; ?>"></td>
                                </tr>
								<?php
								$i++;
							}
						}
					}
				}
				?>
		   </table>
		</div>
		   <table width="933px">
				<tr align="center">
					<td align="center">
						<input type="button" id="show_button" align="middle" class="formbutton" style="width:100px" value="Close" onClick="window_close(0)" />
					</td>
				</tr>
		   </table>
           </div>
	</body>           
	<script src="../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
    <?
	}
	exit();
}

if ($action=="load_dtls_data") 
{
	$ex_data=explode("!^!",$data);
	$upid=$ex_data[0];
	$source=$ex_data[1];
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$roll_no_arr=return_library_array( "select id, no_of_roll from  pro_grey_prod_entry_dtls",'id','no_of_roll');
    $product_dtls_arr=return_library_array( "select id,product_name_details from  product_details_master",'id','product_name_details');
	$color_name=return_library_array( "select id, color_name from lib_color",'id','color_name');
	if($source==1)
	{
		$job_order_arr=array();
		$sql_job="Select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0";
		$sql_job_result =sql_select($sql_job);
		foreach($sql_job_result as $row)
		{
			$job_order_arr[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$job_order_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$job_order_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$job_order_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		unset($sql_job_result);
		
		$batch_array=array();
		$grey_sql="Select a.id, a.batch_no, a.extention_no, a.process_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form!=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.extention_no, a.process_id";
		$grey_sql_result =sql_select($grey_sql);
		
		foreach($grey_sql_result as $row)
		{
			$job_order_arr[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$job_order_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$job_order_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$job_order_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		unset($sql_job_result);
		
		$batch_array=array();
		$grey_sql="Select a.id, a.batch_no, a.extention_no, a.process_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form!=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.extention_no, a.process_id";
		$grey_sql_result =sql_select($grey_sql);
		
		foreach($grey_sql_result as $row)
		{
			$batch_array[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$batch_array[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
			$batch_array[$row[csf('id')]]['process_id']=$row[csf('process_id')];
			$batch_array[$row[csf('id')]]['batch_qnty']=$row[csf('batch_qnty')];
		}
		unset($grey_sql_result);
		$determ_arr = return_library_array( "select mst_id, copmposition_id from lib_yarn_count_determina_dtls",'mst_id','copmposition_id');
		$composition_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
		$data_array=sql_select($sql_deter);
		if(count($data_array)>0)
		{
			foreach( $data_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
			}
		}
		//var_dump($order_array);
		
		$sql="select id as upd_id, delivery_id, delivery_date, challan_no, item_id, body_part_id, color_id, batch_id, febric_description_id, dia_width_type, add_process, add_process_name, packing_qnty as carton_roll, delivery_qty, lib_rate_id, rate, add_rate_id, add_rate, amount, remarks, order_id from subcon_inbound_bill_dtls where mst_id=$upid  and status_active=1 and is_deleted=0 order by id ASC";
		//echo $sql;
		$sql_result_arr =sql_select($sql); $str_val="";
		foreach ($sql_result_arr as $row)
		{
			$process_id=array_unique(explode(',',$batch_array[$row[csf('batch_id')]]['process_id']));
			$process_name='';
			foreach ($process_id as $val)
			{
				if($process_name=='') $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
			}
			
			if($str_val=="") $str_val=$row[csf('delivery_id')].'_'.change_date_format($row[csf('delivery_date')]).'_'.$row[csf('challan_no')].'_'.$row[csf('order_id')].'_'.$job_order_arr[$row[csf('order_id')]]['po'].'_'.$job_order_arr[$row[csf('order_id')]]['style'].'_'.$buyer_arr[$job_order_arr[$row[csf('order_id')]]['buyer']].'_'.$job_order_arr[$row[csf('order_id')]]['job'].'_'.$row[csf('carton_roll')].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('febric_description_id')].'_'.$row[csf('item_id')].'_'.$product_dtls_arr[$row[csf('item_id')]].'_'.$row[csf('batch_id')].'_'.$row[csf('color_id')].'_'.$color_name[$row[csf('color_id')]].'_'.$batch_array[$row[csf('batch_id')]]['process_id'].'_'.$process_name.'_'.$row[csf('dia_width_type')].'_'.$fabric_typee[$row[csf('dia_width_type')]].'_'.number_format($row[csf('delivery_qty')], 2, '.', '').'_'.$row[csf('lib_rate_id')].'_'.$row[csf('rate')].'_'.$row[csf('add_rate_id')].'_'.$row[csf('add_rate')].'_'.$row[csf('amount')].'_'.$row[csf('upd_id')].'_'.$row[csf('remarks')];
			else $str_val.="###".$row[csf('delivery_id')].'_'.change_date_format($row[csf('delivery_date')]).'_'.$row[csf('challan_no')].'_'.$row[csf('order_id')].'_'.$job_order_arr[$row[csf('order_id')]]['po'].'_'.$job_order_arr[$row[csf('order_id')]]['style'].'_'.$buyer_arr[$job_order_arr[$row[csf('order_id')]]['buyer']].'_'.$job_order_arr[$row[csf('order_id')]]['job'].'_'.$row[csf('carton_roll')].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('febric_description_id')].'_'.$row[csf('item_id')].'_'.$product_dtls_arr[$row[csf('item_id')]].'_'.$row[csf('batch_id')].'_'.$row[csf('color_id')].'_'.$color_name[$row[csf('color_id')]].'_'.$batch_array[$row[csf('batch_id')]]['process_id'].'_'.$process_name.'_'.$row[csf('dia_width_type')].'_'.$fabric_typee[$row[csf('dia_width_type')]].'_'.number_format($row[csf('delivery_qty')], 2, '.', '').'_'.$row[csf('lib_rate_id')].'_'.$row[csf('rate')].'_'.$row[csf('add_rate_id')].'_'.$row[csf('add_rate')].'_'.$row[csf('amount')].'_'.$row[csf('upd_id')].'_'.$row[csf('remarks')];
		}
	}
	else
	{

		$product_dtls_arr=return_library_array( "select id,item_description from pro_batch_create_dtls",'id','item_description');

		$job_order_arr=array();
		/*$sql_job="Select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0";*/

		$sql_job="Select a.job_no_prefix_num, a.party_id as buyer_name, b.cust_style_ref as style_ref_no, b.id, b.order_no as po_number from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0";


		//$sql_job="Select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0";

		$sql_job_result =sql_select($sql_job);
		foreach($sql_job_result as $row)
		{
			$job_order_arr[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$job_order_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$job_order_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$job_order_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		unset($sql_job_result);
		
		$batch_array=array();
		$grey_sql="Select a.id, a.batch_no, a.extention_no, a.process_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.extention_no, a.process_id";
		$grey_sql_result =sql_select($grey_sql);
		
		foreach($grey_sql_result as $row)
		{
			$batch_array[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$batch_array[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
			$batch_array[$row[csf('id')]]['process_id']=$row[csf('process_id')];
			$batch_array[$row[csf('id')]]['batch_qnty']=$row[csf('batch_qnty')];
		}
		unset($grey_sql_result);
		//$determ_arr = return_library_array( "select mst_id, copmposition_id from lib_yarn_count_determina_dtls",'mst_id','copmposition_id');
		/*$composition_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
		$data_array=sql_select($sql_deter);
		if(count($data_array)>0)
		{
			foreach( $data_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
			}
		}*/
		//var_dump($order_array);
		
		$sql="select id as upd_id, delivery_id, delivery_date, challan_no, item_id, body_part_id, color_id, batch_id, febric_description_id, dia_width_type, add_process, add_process_name, packing_qnty as carton_roll, delivery_qty, lib_rate_id, rate, add_rate_id, add_rate, amount, remarks, order_id from subcon_inbound_bill_dtls where mst_id=$upid  and status_active=1 and is_deleted=0 order by id ASC";
		//echo $sql;
		$sql_result_arr =sql_select($sql); $str_val="";
		foreach ($sql_result_arr as $row)
		{
			$process_id=array_unique(explode(',',$batch_array[$row[csf('batch_id')]]['process_id']));
			$process_name='';
			foreach ($process_id as $val)
			{
				if($process_name=='') $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
			}

			if($str_val=="") $str_val=$row[csf('delivery_id')].'**'.change_date_format($row[csf('delivery_date')]).'**'.$row[csf('challan_no')].'**'.$row[csf('order_id')].'**'.$job_order_arr[$row[csf('order_id')]]['po'].'**'.$job_order_arr[$row[csf('order_id')]]['style'].'**'.$buyer_arr[$job_order_arr[$row[csf('order_id')]]['buyer']].'**'.$job_order_arr[$row[csf('order_id')]]['job'].'**'.$row[csf('carton_roll')].'**'.$row[csf('body_part_id')].'**'.$body_part[$row[csf("body_part_id")]].'**'.$row[csf('febric_description_id')].'**'.$row[csf('item_id')].'**'.$product_dtls_arr[$row[csf('item_id')]].'**'.$row[csf('batch_id')].'**'.$row[csf('color_id')].'**'.$color_name[$row[csf('color_id')]].'**'.$row[csf('add_process')].'**'.$row[csf('add_process_name')].'**'.$row[csf('dia_width_type')].'**'.$fabric_typee[$row[csf('dia_width_type')]].'**'.number_format($row[csf('delivery_qty')], 2, '.', '').'**'.$row[csf('lib_rate_id')].'**'.$row[csf('rate')].'**'.$row[csf('add_rate_id')].'**'.$row[csf('add_rate')].'**'.$row[csf('amount')].'**'.$row[csf('upd_id')].'**'.$row[csf('remarks')];
			
			else $str_val.="###".$row[csf('delivery_id')].'**'.change_date_format($row[csf('delivery_date')]).'**'.$row[csf('challan_no')].'**'.$row[csf('order_id')].'**'.$job_order_arr[$row[csf('order_id')]]['po'].'**'.$job_order_arr[$row[csf('order_id')]]['style'].'**'.$buyer_arr[$job_order_arr[$row[csf('order_id')]]['buyer']].'**'.$job_order_arr[$row[csf('order_id')]]['job'].'**'.$row[csf('carton_roll')].'**'.$row[csf('body_part_id')].'**'.$body_part[$row[csf("body_part_id")]].'**'.$row[csf('febric_description_id')].'**'.$row[csf('item_id')].'**'.$product_dtls_arr[$row[csf('item_id')]].'**'.$row[csf('batch_id')].'**'.$row[csf('color_id')].'**'.$color_name[$row[csf('color_id')]].'**'.$row[csf('add_process')].'**'.$row[csf('add_process_name')].'**'.$row[csf('dia_width_type')].'**'.$fabric_typee[$row[csf('dia_width_type')]].'**'.number_format($row[csf('delivery_qty')], 2, '.', '').'**'.$row[csf('lib_rate_id')].'**'.$row[csf('rate')].'**'.$row[csf('add_rate_id')].'**'.$row[csf('add_rate')].'**'.$row[csf('amount')].'**'.$row[csf('upd_id')].'**'.$row[csf('remarks')];

		}
	}
	
	echo $str_val;
	exit();
}

if ($action=="load_php_dtls_form")  //new issue 
{
	//echo $data;
	$data = explode("***",$data);
	if ($data[4]==2)
	{
		$old_selected_id="'".implode("','",explode(",",$data[0]))."'";
		$old_issue_id="'".implode("','",explode(",",$data[1]))."'";
		
		$old_bill_id=array_intersect(explode(",",$old_selected_id), explode(",",$old_issue_id));
		$old_bill_id=implode(",",$old_bill_id);
		
		$data_selected=implode(',',explode('_',$data[0]));
		$data_issue=implode(',',explode('_',$data[1]));
		
		$del_id=array_diff(explode(",",$data_selected), explode(",",$data_issue));
		//$bill_id=array_intersect(explode(",",$data_selected), explode(",",$data_issue));
		//$delete_id=array_diff(explode(",",$data_issue), explode(",",$data_selected));
		$bill_id=array_intersect(explode(",",$old_selected_id), explode(",",$old_issue_id));
		$delete_id=array_diff(explode(",",$old_issue_id), explode(",",$old_selected_id));
		
		$del_id=implode(",",$del_id); $bill_id=implode(",",$bill_id);   $delete_id=implode(",",$delete_id);
		
		$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
		$rec_febricdesc_arr=return_library_array( "select id,material_description from sub_material_dtls",'id','material_description');
		$prod_febricdesc_arr=return_library_array( "select id,fabric_description from subcon_production_dtls",'id','fabric_description');
		$color_id_arr=return_library_array( "select id, color_id from subcon_delivery_dtls",'id','color_id');
		$prod_process_arr=return_library_array( "select cons_comp_id, process from subcon_production_dtls",'cons_comp_id','process');
		
		$order_array=array();
		$order_sql="Select a.currency_id, b.id, b.order_no, b.order_uom, b.cust_buyer, b.cust_style_ref, b.main_process_id, b.process_id, b.rate, b.amount, c.process_loss from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and b.id=c.order_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0";
		$order_sql_result =sql_select($order_sql);
		foreach ($order_sql_result as $row)
		{
			$order_array[$row[csf("id")]]['order_no']=$row[csf("order_no")];
			$order_array[$row[csf("id")]]['order_uom']=$row[csf("order_uom")];
			$order_array[$row[csf("id")]]['cust_buyer']=$row[csf("cust_buyer")];
			$order_array[$row[csf("id")]]['cust_style_ref']=$row[csf("cust_style_ref")];
			$order_array[$row[csf("id")]]['rate']=$row[csf("rate")];
			$order_array[$row[csf("id")]]['amount']=$row[csf("amount")];
			$order_array[$row[csf("id")]]['currency_id']=$row[csf("currency_id")];
			$order_array[$row[csf("id")]]['main_process_id']=$row[csf("main_process_id")];
			$order_array[$row[csf("id")]]['process_id']=$row[csf("process_id")];
			$order_array[$row[csf("id")]]['process_loss']=$row[csf("process_loss")];
		}
		
		$grey_qty_array=array();
		$grey_fabric_array=array();
		$grey_sql="Select a.color_id, a.batch_no, a.extention_no, b.id, b.fabric_from, b.po_id,
		 b.item_description, b.fin_dia, b.batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form=36 and b.status_active=1 and b.is_deleted=0";
		$grey_sql_result =sql_select($grey_sql);
		foreach($grey_sql_result as $row)
		{
			$item_name=explode(',',$row[csf('item_description')]);
			$grey_qty_array[$row[csf('po_id')]]=$row[csf('batch_qnty')];
			$grey_fabric_array[$row[csf('po_id')]][$row[csf('id')]]=$row[csf('item_description')];
		}	
		if($db_type==0)
		{
			if( $data[2]!="" )
			{
				$sql="select id as upd_id, delivery_id, delivery_date, challan_no, item_id, packing_qnty as carton_roll, delivery_qty, rate, amount, remarks, order_id, add_process as sub_process_id, color_id from subcon_inbound_bill_dtls where mst_id in ($data[2]) and status_active=1 and process_id  in (3,4)"; 
			}
			else
			{
				if($bill_id!="" && $del_id!="")
					$sql="(select id as upd_id, delivery_id, delivery_date, challan_no, item_id, packing_qnty as carton_roll, delivery_qty, rate, amount, remarks, order_id, add_process as sub_process_id, null as batch_id, color_id as color_id from subcon_inbound_bill_dtls where delivery_id in ($bill_id) and status_active=1 and is_deleted=0 and process_id in (3,4))
					 union
					 (select 0 as upd_id,  group_concat(b.id SEPARATOR '_') as delivery_id, a.delivery_date, a.challan_no, group_concat(b.item_id SEPARATOR '_') as item_id, sum(b.carton_roll) as carton_roll, sum(b.delivery_qty) as delivery_qty, null as rate, null as amount, null as remarks, b.order_id, b.sub_process_id, b.batch_id, b.color_id as color_id from  subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id  and a.process_id=4 and b.id in ($del_id) and a.status_active=1 and b.process_id in (3,4) group by a.challan_no, a.delivery_date, b.sub_process_id, b.batch_id, b.order_id, b.color_id)";
				else if($bill_id!="" && $del_id=="")
					$sql="select id as upd_id, delivery_id, delivery_date, challan_no, item_id, packing_qnty as carton_roll, delivery_qty, rate, amount, remarks, order_id, add_process as sub_process_id, color_id as color_id from subcon_inbound_bill_dtls where delivery_id in ($old_bill_id) and status_active=1 and is_deleted=0 and process_id in (3,4)";
				else  if($bill_id=="" && $del_id!="")
					$sql="select 0 as upd_id,  group_concat(b.id SEPARATOR '_') as delivery_id, a.delivery_date, a.challan_no, group_concat(b.item_id SEPARATOR '_') as item_id, sum(b.carton_roll) as carton_roll, sum(b.delivery_qty) as delivery_qty, null as rate, null as amount, null as remarks, b.order_id, b.sub_process_id, b.batch_id,  b.color_id as color_id
		 from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id  and a.process_id=4 and b.id in ($del_id) and a.status_active=1 and a.is_deleted=0 and b.process_id in (3,4) group by a.challan_no, a.delivery_date, b.sub_process_id, b.batch_id, b.order_id, b.color_id ";
			}
		}
		else if ($db_type==2)
		{
			if( $data[2]!="" )
			{
				$sql="select id as upd_id, delivery_id, delivery_date, challan_no, item_id, packing_qnty as carton_roll, delivery_qty, rate, amount, remarks, order_id, add_process as sub_process_id, color_id from subcon_inbound_bill_dtls where mst_id in ($data[2]) and status_active=1 and process_id  in (3,4)"; 
			}
			else
			{
				if($bill_id!="" && $del_id!="")
					$sql="(select id as upd_id, delivery_id, delivery_date, challan_no, item_id, packing_qnty as carton_roll, delivery_qty, rate, amount, remarks, order_id, add_process as sub_process_id, null as batch_id, color_id as color_id from subcon_inbound_bill_dtls where delivery_id in ($bill_id) and status_active=1 and is_deleted=0 and process_id in (3,4))
					 union
					 (select 0 as upd_id,  listagg(b.id,'_') within group (order by b.id) as delivery_id, a.delivery_date, a.challan_no, listagg(b.item_id,'_') within group (order by b.item_id) as item_id, sum(b.carton_roll) as carton_roll, sum(b.delivery_qty) as delivery_qty, null as rate, null as amount, null as remarks, b.order_id, b.sub_process_id, b.batch_id, b.color_id as color_id from  subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id  and a.process_id=4 and b.id in ($del_id) and a.status_active=1 and b.process_id in (3,4) group by a.challan_no, a.delivery_date, b.sub_process_id, b.batch_id, b.order_id, b.color_id)";
				else if($bill_id!="" && $del_id=="")
					$sql="select id as upd_id, delivery_id, delivery_date, challan_no, item_id, packing_qnty as carton_roll, delivery_qty, rate, amount, remarks, order_id, add_process as sub_process_id, color_id as color_id from subcon_inbound_bill_dtls where delivery_id in ($old_bill_id) and status_active=1 and is_deleted=0 and process_id in (3,4)";
				else  if($bill_id=="" && $del_id!="")
					$sql="select 0 as upd_id,  listagg(b.id,'_') within group (order by b.id) as delivery_id, a.delivery_date, a.challan_no, listagg(b.item_id,'_') within group (order by b.item_id) as item_id, sum(b.carton_roll) as carton_roll, sum(b.delivery_qty) as delivery_qty, null as rate, null as amount, null as remarks, b.order_id, b.sub_process_id, b.batch_id, listagg(b.color_id,',') within group (order by b.color_id) as color_id
		 from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id  and a.process_id=4 and b.id in ($del_id) and a.status_active=1 and a.is_deleted=0 and b.process_id in (3,4) group by   a.challan_no, a.delivery_date, b.sub_process_id, b.batch_id, b.order_id ";
			}
		}
		//echo $sql; //die;
		$sql_result =sql_select($sql);
		$k=0;
		
		if(count($sql_result>0))
		{
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
							<input type="hidden" name="issue_id_all" id="issue_id_all"  style="width:50px" value="<? echo $data[1]; ?>" />
							<input type="hidden" name="delete_id" id="delete_id"  style="width:50px" value="<? echo $delete_id; ?>" />
						<? } ?>
						<input type="hidden" name="curanci_<? echo $k; ?>" id="curanci_<? echo $k; ?>"  style="width:50px" value="<? echo $order_array[$row[csf("order_id")]]['currency_id']; ?>" />
						<input type="hidden" name="updateiddtls_<? echo $k; ?>" id="updateiddtls_<? echo $k; ?>" value="<? echo ($row[csf("upd_id")] != 0 ? $row[csf("upd_id")] : "") ?>">
						<input type="hidden" name="deliveryid_<? echo $k; ?>" id="deliveryid_<? echo $k; ?>" value="<? echo $row[csf("delivery_id")]; ?>">
						<input type="text" name="txt_deleverydate_<? echo $k; ?>" id="txt_deleverydate_<? echo $k; ?>"  class="datepicker" style="width:60px" value="<? echo change_date_format($row[csf("delivery_date")]); ?>" disabled />									
					</td>
					<td>
						<input type="text" name="txt_challenno_<? echo $k; ?>" id="txt_challenno_<? echo $k; ?>"  class="text_boxes" style="width:45px" value="<? echo $row[csf("challan_no")]; ?>" readonly />							 
					</td>
					<td>
						<input type="hidden" name="ordernoid_<? echo $k; ?>" id="ordernoid_<? echo $k; ?>" value="<? echo $row[csf("order_id")]; ?>" style="width:40px" > 
						<input type="text" name="txt_orderno_<? echo $k; ?>" id="txt_orderno_<? echo $k; ?>"  class="text_boxes" style="width:65px" value="<? echo $order_array[$row[csf("order_id")]]['order_no']; ?>" readonly />										
					</td>
					<td>
						<input type="text" name="txt_stylename_<? echo $k; ?>" id="txt_stylename_<? echo $k; ?>"  class="text_boxes" style="width:75px;" value="<? echo $order_array[$row[csf("order_id")]]['cust_style_ref']; ?>"  />
					</td>
					<td>
						<input type="text" name="txt_buyername_<? echo $k; ?>" id="txt_buyername_<? echo $k; ?>"  class="text_boxes" style="width:65px" value="<? echo $order_array[$row[csf("order_id")]]['cust_buyer']; ?>"  />								
					</td>
					<td>			
						<input name="txt_numberroll_<? echo $k; ?>" id="txt_numberroll_<? echo $k; ?>" type="text" class="text_boxes" style="width:40px" value="<? echo $row[csf("carton_roll")]; ?>" readonly />							
					</td> 
					<td>
                        <input type="hidden" name="bodypartid_<? echo $k; ?>" id="bodypartid_<? echo $k; ?>" value="<? echo $row[csf("body_part_id")]; ?>">
                        <input type="hidden" name="compoid_<? echo $k; ?>" id="compoid_<? echo $k; ?>" value="<? echo $row[csf("febric_description_id")]; ?>">
						<input type="hidden" name="itemid_<? echo $k; ?>" id="itemid_<? echo $k; ?>" value="<? echo $row[csf("item_id")]; ?>">
                        <input type="hidden" name="batchid_<? echo $k; ?>" id="batchid_<? echo $k; ?>" value="<? echo $row[csf("batch_id")]; ?>">
						<?
							$item_id=array_unique(explode('_',$row[csf('item_id')]));
							$item_name='';
							foreach ($item_id as $val)
							{
								if($item_name=='') $item_name=$grey_fabric_array[$row[csf('order_id')]][$val]; else $item_name.=",".$grey_fabric_array[$row[csf('order_id')]][$val];
							}
						
							//$item_name=$grey_fabric_array[$row[csf('order_id')]][$row[csf('item_id')]];
						?>
						<input type="text" name="text_febricdesc_<? echo $k; ?>" id="text_febricdesc_<? echo $k; ?>"  class="text_boxes" style="width:100px" value="<? echo $item_name; ?>" readonly/>
					</td>
					<td>
						 <input type="hidden" name="color_process_<? echo $k; ?>" id="color_process_<? echo $k; ?>" value="<? echo $order_array[$row[csf("order_id")]]['main_process_id']; ?>">
						 <input type="hidden" name="color_id_<? echo $k; ?>" id="color_id_<? echo $k; ?>" value="<? $color_id=implode(',',array_unique(explode(',',$row[csf('color_id')]))); echo $color_id; ?>">
						<input type="text" name="txt_color_process_<? echo $k; ?>" id="txt_color_process_<? echo $k; ?>"  class="text_boxes" style="width:80px" value="<? echo $color_arr[$color_id].'/'.$production_process[$order_array[$row[csf("order_id")]]['main_process_id']];//$grey_color_array[$row[csf('order_id')]][$row[csf('item_id')]] ?>" readonly/>
					</td>
					<td>
						<?
							$process=explode(',',$row[csf("sub_process_id")]);
							$add_process="";
							foreach($process as $inf)
							{
								if($add_process=="") $add_process=$conversion_cost_head_array[$inf]; else $add_process.=",".$conversion_cost_head_array[$inf];
							}
						?>
		
						<input type="hidden" name="add_process_<? echo $k; ?>" id="add_process_<? echo $k; ?>" value="<? echo $row[csf("sub_process_id")]; ?>">
						<input type="text" name="txt_add_process_<? echo $k; ?>" id="txt_add_process_<? echo $k; ?>" class="text_boxes" style="width:115px" value="<? echo $add_process; ?>" />
					</td>
					<td>
						<?
							$bill_qty='';
							if($row[csf("upd_id")]==0)
							{
								if($data[3]==1)
								{
									$gray_qty=$row[csf("delivery_qty")]/(1-($order_array[$row[csf("order_id")]]['process_loss'] /100));
									$bill_qty=$gray_qty;
									
								}
								else
								{
									$bill_qty=$row[csf("delivery_qty")];
								}
							}
							else
							{
								$bill_qty=$row[csf("delivery_qty")] ;
							}
							
							if($data[3]==1) $is_readonly="";
							else if($data[3]==2) $is_readonly="readonly";
						?>
						<input type="text" name="txt_deliveryqnty_<? echo $k; ?>" id="txt_deliveryqnty_<? echo $k; ?>" onBlur="qnty_caluculation(<? echo $k; ?>);" class="text_boxes_numeric" style="width:50px" value="<? echo number_format($bill_qty, 2, '.', ''); ?>" <? echo $is_readonly; ?> />
					</td>
					<td>
						<?
							$rate_change='';
							if($row[csf("upd_id")]!=0)
							{
								$rate_change=$row[csf("rate")] ;
							}
							else
							{
								$rate_change=$order_array[$row[csf("order_id")]]['rate'];
							}
						
						?>
						<input type="text" name="txt_rate_<? echo $k; ?>" id="txt_rate_<? echo $k; ?>" onBlur="qnty_caluculation(<? echo $k; ?>);"  class="text_boxes_numeric" style="width:40px" value="<? echo $rate_change; ?>" />
                        <input type="hidden" name="libRateId_<? echo $k; ?>" id="libRateId_<? echo $k; ?>" value="<? //echo $row[csf("lib_rate_id")]; ?>">
					</td>
                    <td>
                        <input type="text" name="txt_addRate_<? echo $k; ?>" id="txt_addRate_<? echo $k; ?>" onBlur="qnty_caluculation(<? echo $k; ?>);"  class="text_boxes_numeric" style="width:40px" value="<? echo $row[csf("add_rate")]; ?>" />
                        <input type="hidden" name="libAddRateId_<? echo $k; ?>" id="libAddRateId_<? echo $k; ?>" value="<? echo $row[csf("add_rate_id")]; ?>">
                    </td>
					<td>
						<?
							$total_amount=$bill_qty*$rate_change;
						?>
						<input type="text" name="txt_amount_<? echo $k; ?>" id="txt_amount_<? echo $k; ?>" style="width:55px"  class="text_boxes_numeric"  value="<? echo  $total_amount; ?>" readonly />
					</td>
					<td>
						<input type="text" name="txt_remarks_<? echo $k; ?>" id="txt_remarks_<? echo $k; ?>"  class="text_boxes" style="width:80px" value="<? echo $row[csf("remarks")]; ?>" />
				   </td>
				</tr>
			<?	
			}
		}
		else
		{
			?>
			<tr align="center">				
				<td>
					<input type="hidden" name="issue_id_all" id="issue_id_all"  style="width:50px" />
					<input type="hidden" name="updateiddtls_1" id="updateiddtls_1" style="width:50px">
					<input type="text" name="txt_deleverydate_1" id="txt_deleverydate_1"  class="datepicker" style="width:60px" readonly />									
				</td>
				<td>
					<input type="text" name="txt_challenno_1" id="txt_challenno_1"  class="text_boxes" style="width:45px" readonly />							 
				</td>
				<td>
					<input type="hidden" name="ordernoid_1" id="ordernoid_1" value="" style="width:50px">
					<input type="text" name="txt_orderno_1" id="txt_orderno_1"  class="text_boxes" style="width:65px" readonly />										
				</td>
				<td>
					<input type="text" name="txt_stylename_1" id="txt_stylename_1"  class="text_boxes" style="width:75px;" />
				</td>
				<td>
					<input type="text" name="txt_buyername_1" id="txt_buyername_1"  class="text_boxes" style="width:65px" />								
				</td>
				<td>			
					<input type="text" name="txt_numberroll_1" id="txt_numberroll_1" class="text_boxes" style="width:40px" readonly />							
				</td>  
				<td>
					<input type="text" name="text_febricdesc_1" id="text_febricdesc_1"  class="text_boxes_numeric" style="width:120px" readonly/>
				</td>
				<td>
					<input type="text" name="txt_color_process_1" id="txt_color_process_1"  class="text_boxes" style="width:80px" readonly/>
				</td>
				<td>
					<input type="hidden" name="add_process_1" id="add_process_1" value="">
					<input type="text" name="txt_add_process_1" id="txt_add_process_1" class="text_boxes" style="width:115px" readonly/>
				</td>
				<td>
					<input type="text" name="txt_qnty_1" id="txt_qnty_1"  class="text_boxes_numeric" style="width:50px" readonly />
				</td>
				<td>
					<input type="text" name="txt_rate_1" id="txt_rate_1"  class="text_boxes" style="width:40px" readonly />
                    <input type="hidden" name="libRateId_1" id="libRateId_1" >
				</td>
                <td>
                    <input type="text" name="txt_addRate_1" id="txt_addRate_1"  class="text_boxes_numeric" style="width:40px" onBlur="qnty_caluculation(1);" />
                    <input type="hidden" name="libAddRateId_1" id="libAddRateId_1" value="">
                </td>
				<td>
					<input type="text" name="txt_amount_1" id="txt_amount_1" class="text_boxes" style="width:55px"  readonly />
				</td>
				<td>
					<input type="text" name="txt_remarks_1" id="txt_remarks_1"  class="text_boxes" style="width:80px" />
				</td>
			</tr>
			<?
		}
	}
	else if ($data[4]==1)
	{
		$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
		$buyer_arr=return_library_array( "select id,short_name from lib_buyer",'id','short_name');
		$prod_febricdesc_arr=return_library_array( "select id,product_name_details from product_details_master",'id','product_name_details');
		$color_id_arr=return_library_array( "select id, color_id from subcon_delivery_dtls",'id','color_id');
		$prod_process_arr=return_library_array( "select cons_comp_id, process from subcon_production_dtls",'cons_comp_id','process');
		
		//echo $data[0];
		
		$old_selected_id=explode(",",$data[0]); 
		$challan=""; $po_id=""; $item_id=""; $body_part_id=""; $febric_description_id=""; $dia_width_type_id=""; $batch_id="";
		foreach($old_selected_id as $val)
		{
			//echo $val.'<br>';
			$selected_id_arr[]=$val;
			$ex_data=explode("_",$val);
			if($challan=="") $challan=$ex_data[0]; else $challan.=','.$ex_data[0];
			if($po_id=="") $po_id=$ex_data[1]; else $po_id.=','.$ex_data[1];
			if($item_id=="") $item_id=$ex_data[2]; else $item_id.=','.$ex_data[2];
			if($body_part_id=="") $body_part_id=$ex_data[3]; else $body_part_id.=','.$ex_data[3];
			if($febric_description_id=="") $febric_description_id=$ex_data[4]; else $febric_description_id.=','.$ex_data[4];
			if($dia_width_type_id=="") $dia_width_type_id=$ex_data[5]; else $dia_width_type_id.=','.$ex_data[5];
			if($batch_id=="") $batch_id=$ex_data[6]; else $batch_id.=','.$ex_data[6];
		}	
				

		$old_issue_id=explode(",",$data[1]); 
		$old_challan=""; $old_po_id=""; $old_item_id=""; $old_body_part_id=""; $old_febric_description_id=""; $old_dia_width_type_id=""; $old_batch_id="";
		foreach($old_issue_id as $value)
		{
			$old_selected_id_arr[]=$value;
			$old_data=explode("_",$value);
			if($old_challan=="") $old_challan=$old_data[0]; else $old_challan.=','.$old_data[0];
			if($old_po_id=="") $old_po_id=$old_data[1]; else $old_po_id.=','.$old_data[1];
			if($old_item_id=="") $old_item_id=$old_data[2]; else $old_item_id.=','.$old_data[2];
			if($old_body_part_id=="") $old_body_part_id=$old_data[3]; else $old_body_part_id.=','.$old_data[3];
			if($old_febric_description_id=="") $old_febric_description_id=$old_data[4]; else $old_febric_description_id.=','.$old_data[4];
			if($old_dia_width_type_id=="") $old_dia_width_type_id=$old_data[5]; else $old_dia_width_type_id.=','.$old_data[5];
			if($old_batch_id=="") $old_batch_id=$old_data[6]; else $old_batch_id.=','.$old_data[6];
		}	
		$bill_challan=implode(",",array_intersect(explode(",",$challan), explode(",",$old_challan)));
		$bill_po_id=implode(",",array_intersect(explode(",",$po_id), explode(",",$old_po_id)));
		$bill_item_id=implode(",",array_intersect(explode(",",$item_id), explode(",",$old_item_id)));
		$bill_body_part_id=implode(",",array_intersect(explode(",",$body_part_id), explode(",",$old_body_part_id)));
		$bill_febric_description_id=implode(",",array_intersect(explode(",",$febric_description_id), explode(",",$old_febric_description_id)));
		$bill_dia_width_type_id=implode(",",array_intersect(explode(",",$dia_width_type_id), explode(",",$old_dia_width_type_id)));
		$bill_batch_id=implode(",",array_intersect(explode(",",$batch_id), explode(",",$old_batch_id)));
		//echo $challan.'=='.$old_challan;
			
		$del_challan=implode(",",array_diff(explode(",",$challan), explode(",",$old_challan)));
		$del_po_id=implode(",",array_diff(explode(",",$po_id), explode(",",$old_po_id)));
		$del_item_id=implode(",",array_diff(explode(",",$item_id), explode(",",$old_item_id)));
		$del_body_part_id=implode(",",array_diff(explode(",",$body_part_id), explode(",",$old_body_part_id)));
		$del_febric_description_id=implode(",",array_diff(explode(",",$febric_description_id), explode(",",$old_febric_description_id)));
		$del_dia_width_type_id=implode(",",array_diff(explode(",",$dia_width_type_id), explode(",",$old_dia_width_type_id)));
		$del_batch_id=implode(",",array_diff(explode(",",$batch_id), explode(",",$old_batch_id)));	
		//$add_del_item_id="'".implode("','",explode(",",$del_item_id))."'";
		if($del_item_id=="") $add_del_item_id="'".implode("','",explode(",",$item_id))."'"; else $add_del_item_id="'".implode("','",explode(",",$del_item_id))."'";
	
		
		
		
		/*$bill_challan=implode(",",array_intersect(explode(",",$challan), explode(",",$old_challan)));
		$bill_po_id=implode(",",array_intersect(explode(",",$po_id), explode(",",$old_po_id)));
		$bill_item_id=implode(",",array_intersect(explode(",",$item_id), explode(",",$old_item_id)));
		$bill_body_part_id=implode(",",array_intersect(explode(",",$body_part_id), explode(",",$old_body_part_id)));
		
		$bill_febric_description_id=implode(",",array_intersect(explode(",",$febric_description_id), explode(",",$old_febric_description_id)));*/
		$dele_item_id="'".implode("','",explode(",",$bill_item_id))."'";
		
		$old_bill_id=array_intersect(explode(",",$old_selected_id), explode(",",$old_issue_id));
		$old_bill_id=implode(",",$old_bill_id);
		
		$old_selected_id="'".implode("','",explode(",",$data[0]))."'";
		$old_issue_id="'".implode("','",explode(",",$data[1]))."'";
		
		$data_selected=implode(',',explode('_',$data[0]));
		$data_issue=implode(',',explode('_',$data[1]));
		
		$del_id=array_diff(explode(",",$data_selected), explode(",",$data_issue));
		//print_r($del_id);
		$bill_id=array_intersect(explode(",",$old_selected_id), explode(",",$old_issue_id));
		//$delete_id=array_diff(explode(",",$data_issue), explode(",",$data_selected));
		//$bill_id=array_intersect(explode(",",$old_selected_id), explode(",",$old_issue_id));
		$delete_id=array_diff(explode(",",$old_issue_id), explode(",",$old_selected_id));
		
		$del_id=implode(",",$del_id); $bill_id=implode(",",$bill_id); $delete_id=implode(",",$delete_id);
		//echo $del_id.'=='.$bill_id.'<br>';
		$po_array=array();
		$po_sql=sql_select( "select a.style_ref_no, a.job_no_prefix_num, a.buyer_name, b.id, b.po_number from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach($po_sql as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['order']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		}
		
		$batch_array=array();
		$grey_sql="Select a.id, a.batch_no, a.extention_no, a.process_id as sub_process_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form!=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.extention_no, a.process_id";
		$grey_sql_result =sql_select($grey_sql);
		
		foreach($grey_sql_result as $row)
		{
			$batch_array[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			$batch_array[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
			$batch_array[$row[csf('id')]]['sub_process_id']=$row[csf('sub_process_id')];
			$batch_array[$row[csf('id')]]['batch_qnty']=$row[csf('batch_qnty')];
		}	
		
		if($del_body_part_id!="") $body_part_cond=" and b.body_part_id in ($body_part_id)"; else $body_part_cond="";
		if($challan!="") $del_challan_cond=" and a.recv_number_prefix_num in ($del_challan)"; else $del_challan_cond="";
		if($del_po_id!="") $del_po_id_cond=" and c.po_breakdown_id in ($po_id)"; else $del_po_id_cond="";
		if($add_del_item_id!="") $del_item_id_cond=" and c.prod_id in ($add_del_item_id)"; else $del_item_id_cond="";
		if($del_febric_description_id!="") $del_febric_id_cond="  and b.fabric_description_id in ($febric_description_id)"; else $del_febric_id_cond="";
		if($add_del_item_id!="") $wout_item_id_cond=" and b.prod_id in ($add_del_item_id)"; else $wout_item_id_cond="";
		if($del_dia_width_type_id!="") $del_dia_width_type_id_cond=" and b.dia_width_type in ($dia_width_type_id)"; else $del_dia_width_type_id_cond="";
		if($del_batch_id!="") $del_batch_id_cond=" and b.batch_id in ($batch_id)"; else $del_batch_id_cond="";
		
		//echo "select id as upd_id, delivery_date, challan_no, item_id, body_part_id, color_id, febric_description_id, add_process, packing_qnty as carton_roll, delivery_qty, rate, amount, remarks, order_id from subcon_inbound_bill_dtls where challan_no in ($bill_challan) and order_id in ($bill_po_id) and item_id in ($bill_item_id) and body_part_id in ($bill_body_part_id) and febric_description_id in ($bill_febric_description_id) and status_active=1 and is_deleted=0";
		if($data[5]!=3)
		{
			if($db_type==0)
			{
				if( $data[2]!="" )
				{
					$sql="select id as upd_id, delivery_id, delivery_date, challan_no, item_id, packing_qnty as carton_roll, delivery_qty, rate, amount, remarks, order_id, add_process as sub_process_id, color_id from subcon_inbound_bill_dtls where mst_id in ($data[2]) and status_active=1 and process_id  in (3,4)"; 
				}
				else
				{
					if($bill_id!="" && $del_id!="")
						$sql="(select id as upd_id, delivery_id, delivery_date, challan_no, item_id, packing_qnty as carton_roll, delivery_qty, rate, amount, remarks, order_id, add_process as sub_process_id, null as batch_id, color_id as color_id from subcon_inbound_bill_dtls where delivery_id in ($bill_id) and status_active=1 and is_deleted=0 and process_id in (3,4))
						 union
						 (select 0 as upd_id,  group_concat(b.id SEPARATOR '_') as delivery_id, a.delivery_date, a.challan_no, group_concat(b.item_id SEPARATOR '_') as item_id, sum(b.carton_roll) as carton_roll, sum(b.delivery_qty) as delivery_qty, null as rate, null as amount, null as remarks, b.order_id, b.sub_process_id, b.batch_id, b.color_id as color_id from  subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id  and a.process_id=4 and b.id in ($del_id) and a.status_active=1 and b.process_id in (3,4) group by a.challan_no, a.delivery_date, b.sub_process_id, b.batch_id, b.order_id, b.color_id)";
					else if($bill_id!="" && $del_id=="")
						$sql="select id as upd_id, delivery_id, delivery_date, challan_no, item_id, packing_qnty as carton_roll, delivery_qty, rate, amount, remarks, order_id, add_process as sub_process_id, color_id as color_id from subcon_inbound_bill_dtls where delivery_id in ($old_bill_id) and status_active=1 and is_deleted=0 and process_id in (3,4)";
					else  if($bill_id=="" && $del_id!="")
						$sql="select 0 as upd_id,  group_concat(b.id SEPARATOR '_') as delivery_id, a.delivery_date, a.challan_no, group_concat(b.item_id SEPARATOR '_') as item_id, sum(b.carton_roll) as carton_roll, sum(b.delivery_qty) as delivery_qty, null as rate, null as amount, null as remarks, b.order_id, b.sub_process_id, b.batch_id,  b.color_id as color_id
			 from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id  and a.process_id=4 and b.id in ($del_id) and a.status_active=1 and a.is_deleted=0 and b.process_id in (3,4) group by a.challan_no, a.delivery_date, b.sub_process_id, b.batch_id, b.order_id, b.color_id ";
				}
			}
			else if ($db_type==2)
			{
				if( $data[2]!="" )
				{
					$sql="select id as upd_id, delivery_date, challan_no, item_id, body_part_id, febric_description_id, color_id, batch_id, add_process, add_process_name, packing_qnty as carton_roll, delivery_qty, lib_rate_id, rate, add_rate_id, add_rate, amount, remarks, order_id, dia_width_type from subcon_inbound_bill_dtls where mst_id in ($data[2]) and status_active=1 order by challan_no";
					 
					$sql_result_arr =sql_select($sql);
					foreach ($sql_result_arr as $row)
					{
						$update_chk_str[]=$row[csf("challan_no")].'_'.$row[csf('order_id')].'_'.$row[csf('item_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')].'_'.$row[csf('dia_width_type')].'_'.$row[csf('batch_id')];
						$issue_chk_str[]=$row[csf("challan_no")].'_'.$row[csf('order_id')].'_'.$row[csf('item_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')].'_'.$row[csf('dia_width_type')].'_'.$row[csf('batch_id')];
					}
				}
				else
				{
					if($bill_id!="" && $del_id!="")
						$sql="(select id as upd_id, delivery_date, challan_no, item_id, body_part_id, color_id, batch_id, febric_description_id, dia_width_type, add_process, add_process_name, packing_qnty as carton_roll, delivery_qty, lib_rate_id, rate, add_rate_id, add_rate, amount, remarks, order_id from subcon_inbound_bill_dtls where challan_no in ($bill_challan) and order_id in ($bill_po_id) and item_id in ($dele_item_id) and body_part_id in ($bill_body_part_id) and febric_description_id in ($bill_febric_description_id) and dia_width_type in ($bill_dia_width_type_id) and batch_id in ($bill_batch_id) and status_active=1 and is_deleted=0)
						 union
						 (select 0 as upd_id, a.receive_date as delivery_date, a.recv_number_prefix_num as challan_no, listagg(b.prod_id,',') within group (order by b.prod_id) as item_id, b.body_part_id, b.color_id, b.batch_id, b.fabric_description_id as febric_description_id, b.dia_width_type, null as add_process, null as add_process_name, sum(b.no_of_roll) as carton_roll, sum(c.quantity) as delivery_qty, null as lib_rate_id, null as rate, null as add_rate_id, null as add_rate, null as amount, null as remarks, c.po_breakdown_id as order_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=1 and c.trans_type=1 and c.trans_id!=0 and a.entry_form in (7,37) and c.entry_form in (7,37) and a.item_category=2 and a.receive_basis in (4,5,9) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $del_challan_cond $del_po_id_cond $del_item_id_cond $body_part_cond $del_febric_id_cond $del_dia_width_type_id_cond $del_batch_id_cond group by a.id, a.recv_number_prefix_num, a.receive_date, b.prod_id, b.body_part_id, b.color_id, b.batch_id, b.fabric_description_id, b.dia_width_type, c.po_breakdown_id) order by challan_no";
					else if($bill_id!="" && $del_id=="")
						$sql="select id as upd_id, delivery_date, challan_no, item_id, body_part_id, color_id, batch_id, febric_description_id, dia_width_type, add_process, add_process_name, packing_qnty as carton_roll, delivery_qty, lib_rate_id, rate, add_rate_id, add_rate, amount, remarks, order_id from subcon_inbound_bill_dtls where challan_no in ($bill_challan) and order_id in ($bill_po_id) and item_id in ($dele_item_id) and body_part_id in ($bill_body_part_id) and febric_description_id in ($bill_febric_description_id) and dia_width_type in ($bill_dia_width_type_id) and batch_id in ($bill_batch_id) and status_active=1 and is_deleted=0 order by challan_no";
					else  if($bill_id=="" && $del_id!="")
						$sql="select 0 as upd_id, a.receive_date as delivery_date, a.recv_number_prefix_num as challan_no, c.prod_id as item_id, b.body_part_id, b.color_id, b.batch_id, b.fabric_description_id as febric_description_id, b.dia_width_type, null as add_process, null as add_process_name, sum(b.no_of_roll) as carton_roll, sum(c.quantity) as delivery_qty, null as lib_rate_id, null as rate, null as add_rate_id, null as add_rate, null as amount, null as remarks, c.po_breakdown_id as order_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=1 and c.trans_type=1 and c.trans_id!=0 and a.entry_form in (7,37) and c.entry_form in (7,37) and a.item_category=2 and a.receive_basis in (4,5,9) and a.recv_number_prefix_num in ($challan) and c.po_breakdown_id in ($po_id) and c.prod_id in ($item_id) and b.body_part_id in ($body_part_id) and b.fabric_description_id in ($febric_description_id) and b.dia_width_type in ($dia_width_type_id) and b.batch_id in ($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.recv_number_prefix_num, a.receive_date, c.prod_id, b.body_part_id, b.color_id, b.batch_id, b.fabric_description_id, b.dia_width_type, c.po_breakdown_id order by a.recv_number_prefix_num ";
				}
			}
		}
		else
		{
			if($add_del_item_id!="") $del_item_idCond=" and b.prod_id in ($add_del_item_id)"; else $del_item_idCond="";
			if( $data[2]!="" )
			{
				$sql="select id as upd_id, delivery_date, challan_no, item_id, body_part_id, febric_description_id, color_id, batch_id, add_process, add_process_name, packing_qnty as carton_roll, delivery_qty, lib_rate_id, rate, add_rate_id, add_rate, amount, remarks, order_id, dia_width_type from subcon_inbound_bill_dtls where mst_id in ($data[2]) and status_active=1 order by challan_no";
				 
				$sql_result_arr =sql_select($sql);
				foreach ($sql_result_arr as $row)
				{
					$update_chk_str[]=$row[csf("challan_no")].'_'.$row[csf('order_id')].'_'.$row[csf('item_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')].'_'.$row[csf('dia_width_type')].'_'.$row[csf('batch_id')];
					$issue_chk_str[]=$row[csf("challan_no")].'_'.$row[csf('order_id')].'_'.$row[csf('item_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')].'_'.$row[csf('dia_width_type')].'_'.$row[csf('batch_id')];
				}
			}
			else
			{
				if($bill_id!="" && $del_id!="")
					$sql="(select id as upd_id, delivery_date, challan_no, item_id, body_part_id, color_id, batch_id, febric_description_id, dia_width_type, add_process, add_process_name, packing_qnty as carton_roll, delivery_qty, lib_rate_id, rate, add_rate_id, add_rate, amount, remarks, order_id from subcon_inbound_bill_dtls where challan_no in ($bill_challan) and item_id in ($dele_item_id) and body_part_id in ($bill_body_part_id) and febric_description_id in ($bill_febric_description_id) and dia_width_type in ($bill_dia_width_type_id) and batch_id in ($bill_batch_id) and status_active=1 and is_deleted=0)
					 union all
					 (select 0 as upd_id, a.receive_date as delivery_date, a.recv_number_prefix_num as challan_no, listagg(b.prod_id,',') within group (order by b.prod_id) as item_id, b.body_part_id, b.color_id, b.batch_id, b.fabric_description_id as febric_description_id, b.dia_width_type, null as add_process, null as add_process_name, sum(b.no_of_roll) as carton_roll, sum(b.receive_qnty) as delivery_qty, null as lib_rate_id, null as rate, null as add_rate_id, null as add_rate, null as amount, null as remarks, null as order_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.knitting_source=1 and a.entry_form in (7,37) and a.item_category=2 and a.receive_basis in (4,5,9) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $del_challan_cond $del_item_idCond $body_part_cond $del_febric_id_cond $del_dia_width_type_id_cond $del_batch_id_cond group by a.id, a.recv_number_prefix_num, a.receive_date, b.prod_id, b.body_part_id, b.color_id, b.batch_id, b.fabric_description_id, b.dia_width_type ) order by challan_no";
				else if($bill_id!="" && $del_id=="")
					$sql="select id as upd_id, delivery_date, challan_no, item_id, body_part_id, color_id, batch_id, febric_description_id, dia_width_type, add_process, add_process_name, packing_qnty as carton_roll, delivery_qty, lib_rate_id, rate, add_rate_id, add_rate, amount, remarks, order_id from subcon_inbound_bill_dtls where challan_no in ($challan) and item_id in ($dele_item_id) and body_part_id in ($bill_body_part_id) and febric_description_id in ($bill_febric_description_id) and dia_width_type in ($bill_dia_width_type_id) and batch_id in ($bill_batch_id) and status_active=1 and is_deleted=0 order by challan_no";
				else  if($bill_id=="" && $del_id!="")
					$sql="select 0 as upd_id, a.receive_date as delivery_date, a.recv_number_prefix_num as challan_no, b.prod_id as item_id, b.body_part_id, b.color_id, b.batch_id, b.fabric_description_id as febric_description_id, b.dia_width_type, null as add_process, null as add_process_name, sum(b.no_of_roll) as carton_roll, sum(b.receive_qnty) as delivery_qty, null as lib_rate_id, null as rate, null as add_rate_id, null as add_rate, null as amount, null as remarks, null as order_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.knitting_source=1 and a.entry_form in (7,37) and a.item_category=2 and a.receive_basis in (4,5,9) and a.recv_number_prefix_num in ($challan) and b.body_part_id in ($body_part_id) and b.fabric_description_id in ($febric_description_id) and b.dia_width_type in ($dia_width_type_id) and b.batch_id in ($batch_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.recv_number_prefix_num, a.receive_date, b.prod_id, b.body_part_id, b.color_id, b.batch_id, b.fabric_description_id, b.dia_width_type order by a.recv_number_prefix_num ";
			}
		}
		// echo $sql; //die;
		$sql_result =sql_select($sql);
		$k=0; $num_rowss=count($sql_result); $previous_chk_str="";
		
		if(count($sql_result>0))
		{
			//$num_rowss=count($sql_result);
			foreach ($sql_result as $row)
			{
				//$k++;
				if( $data[2]!="" )
				{
					$data[1]='';
					//if($data[1]=="") $data[1]=$row[csf("delivery_id")]; else $data[1].=",".$row[csf("delivery_id")];
					foreach ($issue_chk_str as $val)
					{
						if($data[1]=="") $data[1]=$val; else $data[1].=",".$val;
					}
					$update_chk_str[]=$row[csf("challan_no")].'_'.$row[csf('order_id')].'_'.$row[csf('item_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')].'_'.$row[csf('dia_width_type')].'_'.$row[csf('batch_id')];
				}
				$item_id=implode(",",array_unique(explode(",",$row[csf('item_id')])));
				$chk_str=$row[csf("challan_no")].'_'.$row[csf('order_id')].'_'.$item_id.'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')].'_'.$row[csf('dia_width_type')].'_'.$row[csf('batch_id')];
				if($data[2]=="") $previous_chk_str=$selected_id_arr; else $previous_chk_str=$update_chk_str;
				//print_r ($previous_chk_str);
				$count_selected_id_arr=count($selected_id_arr);
				if(in_array($chk_str,$previous_chk_str))
				{
					$k++;
				?>
					<tr align="center">				
					   <td>
							<? if ($k==$count_selected_id_arr) { ?>
								<input type="hidden" name="issue_id_all" id="issue_id_all"  style="width:50px" value="<? echo $data[1]; ?>" />
								<input type="hidden" name="delete_id" id="delete_id" style="width:50px" value="<? echo $delete_id; ?>" />
							<? } ?>
							<input type="hidden" name="curanci_<? echo $k; ?>" id="curanci_<? echo $k; ?>"  style="width:50px" value="<? echo '1'; ?>" />
							<input type="hidden" name="updateiddtls_<? echo $k; ?>" id="updateiddtls_<? echo $k; ?>" value="<? echo ($row[csf("upd_id")] != 0 ? $row[csf("upd_id")] : "") ?>">
							<input type="hidden" name="deliveryid_<? echo $k; ?>" id="deliveryid_<? echo $k; ?>" value="<? echo $row[csf("delivery_id")]; ?>">
							<input type="text" name="txt_deleverydate_<? echo $k; ?>" id="txt_deleverydate_<? echo $k; ?>"  class="datepicker" style="width:60px" value="<? echo change_date_format($row[csf("delivery_date")]); ?>" disabled />									
						</td>
						<td>
							<input type="text" name="txt_challenno_<? echo $k; ?>" id="txt_challenno_<? echo $k; ?>"  class="text_boxes" style="width:45px" value="<? echo $row[csf("challan_no")]; ?>" readonly />							 
						</td>
						<td>
							<input type="hidden" name="ordernoid_<? echo $k; ?>" id="ordernoid_<? echo $k; ?>" value="<? echo $row[csf("order_id")]; ?>" style="width:40px" > 
							<input type="text" name="txt_orderno_<? echo $k; ?>" id="txt_orderno_<? echo $k; ?>"  class="text_boxes" style="width:65px" value="<? echo $po_array[$row[csf('order_id')]]['order'] ?>" readonly />										
						</td>
						<td>
							<input type="text" name="txt_stylename_<? echo $k; ?>" id="txt_stylename_<? echo $k; ?>"  class="text_boxes" style="width:75px;" value="<? echo $po_array[$row[csf('order_id')]]['style']; ?>" readonly />
						</td>
						<td>
							<input type="text" name="txt_buyername_<? echo $k; ?>" id="txt_buyername_<? echo $k; ?>"  class="text_boxes" style="width:65px" value="<? echo $buyer_arr[$po_array[$row[csf('order_id')]]['buyer_name']]; ?>" readonly />								
						</td>
						<td>			
							<input name="txt_numberroll_<? echo $k; ?>" id="txt_numberroll_<? echo $k; ?>" type="text" class="text_boxes" style="width:40px" value="<? echo $row[csf("carton_roll")]; ?>" readonly />							
						</td> 
						<td>
                        	<input type="hidden" name="bodypartid_<? echo $k; ?>" id="bodypartid_<? echo $k; ?>" value="<? echo $row[csf("body_part_id")]; ?>">
                            <input type="hidden" name="compoid_<? echo $k; ?>" id="compoid_<? echo $k; ?>" value="<? echo $row[csf("febric_description_id")]; ?>">
							<input type="hidden" name="itemid_<? echo $k; ?>" id="itemid_<? echo $k; ?>" value="<? echo $row[csf("item_id")]; ?>">
                            <input type="hidden" name="batchid_<? echo $k; ?>" id="batchid_<? echo $k; ?>" value="<? echo $row[csf("batch_id")]; ?>">
							<?
								$item_id=array_unique(explode('_',$row[csf('item_id')]));
								$item_name='';
								foreach ($item_id as $val)
								{
									if($item_name=='') $item_name=$prod_febricdesc_arr[$val]; else $item_name.=",".$prod_febricdesc_arr[$val];
								}
							?>
							<input type="text" name="text_febricdesc_<? echo $k; ?>" id="text_febricdesc_<? echo $k; ?>"  class="text_boxes" style="width:120px" value="<? echo $item_name; ?>" readonly/>
						</td>
						<td>
							 <input type="hidden" name="color_process_<? echo $k; ?>" id="color_process_<? echo $k; ?>" value="<? //echo $order_array[$row[csf("order_id")]]['main_process_id']; ?>">
							 <input type="hidden" name="color_id_<? echo $k; ?>" id="color_id_<? echo $k; ?>" value="<? $color_id=implode(',',array_unique(explode(',',$row[csf('color_id')]))); echo $color_id; ?>">
							<input type="text" name="txt_color_process_<? echo $k; ?>" id="txt_color_process_<? echo $k; ?>"  class="text_boxes" style="width:80px" value="<? echo $color_arr[$row[csf("color_id")]];//$grey_color_array[$row[csf('order_id')]][$row[csf('item_id')]] ?>" readonly/>
						</td>
						<td>
							<?
								if($row[csf("upd_id")]==0) { $process=explode(',',$batch_array[$row[csf('batch_id')]]['sub_process_id']); }
								else { $process=explode(',',$row[csf('add_process')]); }
								$add_process="";
								foreach($process as $inf)
								{
									if($add_process=="") $add_process=$conversion_cost_head_array[$inf]; else $add_process.=",".$conversion_cost_head_array[$inf];
								}
							?>
			
							<input type="hidden" name="add_process_<? echo $k; ?>" id="add_process_<? echo $k; ?>" value="<? echo $batch_array[$row[csf('batch_id')]]['sub_process_id']; ?>">
							<input type="text" name="txt_add_process_<? echo $k; ?>" id="txt_add_process_<? echo $k; ?>" class="text_boxes" style="width:100px" value="<? echo $row[csf('add_process_name')]; ?>" />
						</td>
                        <td>
							<input type="hidden" name="diaType_<? echo $k; ?>" id="diaType_<? echo $k; ?>" value="<? echo $row[csf('dia_width_type')]; ?>">
							<input type="text" name="txt_diaType_<? echo $k; ?>" id="txt_diaType_<? echo $k; ?>" class="text_boxes" style="width:55px" value="<? echo $fabric_typee[$row[csf('dia_width_type')]]; ?>" readonly />
						</td>
						<td>
							<?
								$bill_qty='';
								if($row[csf("upd_id")]==0)
								{
									if($data[3]==1)
									{
										$gray_qty=$row[csf("delivery_qty")]/(1-($order_array[$row[csf("order_id")]]['process_loss'] /100));
										$bill_qty=$gray_qty;
									} else { $bill_qty=$row[csf("delivery_qty")]; }
								}
								else { $bill_qty=$row[csf("delivery_qty")]; }
								
								if($data[3]==1) $is_readonly="";
 								else if($data[3]==2) $is_readonly="readonly";
							?>
							<input type="text" name="txt_deliveryqnty_<? echo $k; ?>" id="txt_deliveryqnty_<? echo $k; ?>" onBlur="qnty_caluculation(<? echo $k; ?>);" class="text_boxes_numeric" style="width:50px" value="<? echo number_format($bill_qty, 2, '.', ''); ?>" <? echo $is_readonly; ?> />
						</td>
						<td>
							<input type="text" name="txt_rate_<? echo $k; ?>" id="txt_rate_<? echo $k; ?>" onBlur="qnty_caluculation(<? echo $k; ?>);"  class="text_boxes_numeric" style="width:40px" value="<? echo $row[csf("rate")]; ?>" placeholder="Browse" onDblClick="openmypage_rate(<? echo $k; ?>)"  />
                            <input type="hidden" name="libRateId_<? echo $k; ?>" id="libRateId_<? echo $k; ?>" value="<? echo $row[csf("lib_rate_id")]; ?>">
						</td>
                        <td>
							<input type="text" name="txt_addRate_<? echo $k; ?>" id="txt_addRate_<? echo $k; ?>" onBlur="qnty_caluculation(<? echo $k; ?>);"  class="text_boxes_numeric" style="width:40px" value="<? echo $row[csf("add_rate")]; ?>"  placeholder="Browse" onDblClick="openmypage_addRate(<? echo $k; ?>)"   />
                            <input type="hidden" name="libAddRateId_<? echo $k; ?>" id="libAddRateId_<? echo $k; ?>" value="<? echo $row[csf("add_rate_id")]; ?>">
						</td>
						<td>
							<input type="text" name="txt_amount_<? echo $k; ?>" id="txt_amount_<? echo $k; ?>" style="width:55px"  class="text_boxes_numeric"  value="<? echo  $row[csf("amount")]; ?>" readonly />
						</td>
                       <td>
							 <input type="button" name="remarks_<? echo $k; ?>" id="remarks_<? echo $k; ?>"  class="formbuttonplasminus" style="width:20px" value="R" onClick="openmypage_remarks(<? echo $k; ?>);" />
							 <input type="hidden" name="remarksvalue_<? echo $k; ?>" id="remarksvalue_<? echo $k; ?>" class="text_boxes" value="<? echo $row[csf("remarks")]; ?>" />
						</td>
					</tr>
				<?
				}
			}
		}
		else
		{
			?>
			<tr align="center">				
				<td>
					<input type="hidden" name="issue_id_all" id="issue_id_all"  style="width:50px" />
					<input type="hidden" name="updateiddtls_1" id="updateiddtls_1" style="width:50px">
					<input type="text" name="txt_deleverydate_1" id="txt_deleverydate_1"  class="datepicker" style="width:60px" readonly />									
				</td>
				<td>
					<input type="text" name="txt_challenno_1" id="txt_challenno_1"  class="text_boxes" style="width:45px" readonly />							 
				</td>
				<td>
					<input type="hidden" name="ordernoid_1" id="ordernoid_1" value="" style="width:50px">
					<input type="text" name="txt_orderno_1" id="txt_orderno_1"  class="text_boxes" style="width:65px" readonly />										
				</td>
				<td>
					<input type="text" name="txt_stylename_1" id="txt_stylename_1"  class="text_boxes" style="width:75px;" />
				</td>
				<td>
					<input type="text" name="txt_buyername_1" id="txt_buyername_1"  class="text_boxes" style="width:65px" />								
				</td>
				<td>			
					<input type="text" name="txt_numberroll_1" id="txt_numberroll_1" class="text_boxes" style="width:40px" readonly />							
				</td>  
				<td>
					<input type="text" name="text_febricdesc_1" id="text_febricdesc_1"  class="text_boxes_numeric" style="width:120px" readonly/>
				</td>
				<td>
					<input type="text" name="txt_color_process_1" id="txt_color_process_1"  class="text_boxes" style="width:80px" readonly/>
				</td>
				<td>
					<input type="hidden" name="add_process_1" id="add_process_1" value="">
					<input type="text" name="txt_add_process_1" id="txt_add_process_1" class="text_boxes" style="width:115px" readonly/>
				</td>
				<td>
					<input type="text" name="txt_qnty_1" id="txt_qnty_1"  class="text_boxes_numeric" style="width:50px" readonly />
				</td>
				<td>
					<input type="text" name="txt_rate_1" id="txt_rate_1"  class="text_boxes" style="width:40px" readonly />
				</td>
                <td>
                    <input type="text" name="txt_addRate_1" id="txt_addRate_1"  class="text_boxes_numeric" style="width:40px" onBlur="qnty_caluculation(1);" />
                    <input type="hidden" name="libAddRateId_1" id="libAddRateId_1" value="">
                </td>
				<td>
					<input type="text" name="txt_amount_1" id="txt_amount_1" class="text_boxes" style="width:55px"  readonly />
				</td>
				<td>
					<input type="text" name="txt_remarks_1" id="txt_remarks_1"  class="text_boxes" style="width:80px" />
				</td>
			</tr>
			<?
		}
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0)   // Insert Here========================================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if (is_duplicate_field( "delivery_id", "subcon_inbound_bill_dtls", "mst_id=$update_id" )==1)
		{
		echo "11**0"; 
		die;			
		}
		if($db_type==0)
		{
			$year_cond=" and YEAR(insert_date)";	
		}
		else if($db_type==2)
		{
			$year_cond=" and TO_CHAR(insert_date,'YYYY')";	
		}
		
		for($i=1; $i<=$tot_row; $i++)
		{
			$color_process="color_process_".$i;  
		}
		/*if (str_replace("'",'',$$color_process)==3)
		{
			$new_bill_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'DYE', date("Y",time()), 5, "select prefix_no,prefix_no_num from  subcon_inbound_bill_mst where company_id=$cbo_company_id and process_id=".$$color_process." $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		}
		else if (str_replace("'",'',$$color_process)==4)
		{*/
			$new_bill_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'DFB', date("Y",time()), 5, "select prefix_no, prefix_no_num from  subcon_inbound_bill_mst where company_id=$cbo_company_id and process_id=4 $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		//}
			$id=return_next_id( "id", "subcon_inbound_bill_mst", 1 ) ; 	
		//if(str_replace("'",'',$update_id)=="")
		//{
			$field_array="id, prefix_no, prefix_no_num, bill_no, company_id, location_id, bill_date, party_id, party_source, bill_for, process_id, inserted_by, insert_date";
			$data_array="(".$id.",'".$new_bill_no[1]."','".$new_bill_no[2]."','".$new_bill_no[0]."',".$cbo_company_id.",".$cbo_location_name.",".$txt_bill_date.",".$cbo_party_name.",".$cbo_party_source.",".$cbo_bill_for.",4,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
			//echo "INSERT INTO subcon_inbound_bill_mst (".$field_array.") VALUES ".$data_array; //die;
			$rID=sql_insert("subcon_inbound_bill_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
			$return_no=$new_bill_no[0];
		/*}
		else
		{
			$id=str_replace("'",'',$update_id);
			$field_array="bill_no*company_id*location_id*bill_date*party_id*party_source*bill_for*updated_by*update_date";
			$data_array="".$txt_bill_no."*".$cbo_company_id."*".$cbo_location_name."*".$txt_bill_date."*".$cbo_party_name."*".$cbo_party_source."*".$cbo_bill_for."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			$rID=sql_update("subcon_inbound_bill_mst",$field_array,$data_array,"id",$update_id,0);
			$return_no=str_replace("'",'',$txt_bill_no);
		}*/
		$id1=return_next_id( "id", "subcon_inbound_bill_dtls",1);
		
		$field_array1 ="id, mst_id, delivery_id, delivery_date, challan_no, order_id, item_id, febric_description_id, dia_width_type, batch_id, body_part_id, add_process, add_process_name, packing_qnty, delivery_qty, lib_rate_id, rate, add_rate_id, add_rate, amount, remarks, currency_id, process_id, color_id, inserted_by, insert_date";
		$field_array_up ="delivery_id*delivery_date*challan_no*order_id*item_id*febric_description_id*body_part_id*add_process*add_process_name*color_id*packing_qnty*delivery_qty*lib_rate_id*rate*add_rate_id*add_rate*amount*remarks*currency_id*updated_by*update_date";
		$field_array_delivery="bill_status";
		$field_array_order="cust_buyer*cust_style_ref";
		$add_comma=0;
		for($i=1; $i<=$tot_row; $i++)
		{
			$delivery_id="deliveryid_".$i;
			$delevery_date="txtDeleverydate_".$i;
			$challen_no="txtChallenno_".$i;
			$orderid="ordernoid_".$i;
			$item_id="itemid_".$i;
			$compoid="compoid_".$i;
			$bodypartid="bodypartid_".$i;
			$style_name="txtStylename_".$i;
			$buyer_name="txt_buyername_".$i;
			$number_roll="txtNumberroll_".$i;
			$quantity="txtDeliveryqnty_".$i;
			$libRateId="libRateId_".$i;
			$rate="txtRate_".$i;
			$libAddRateId="libAddRateId_".$i;
			$addRate="txtAddRate_".$i;
			$amount="txtAmount_".$i;
			$curanci="curanci_".$i;
			$remarks="remarksvalue_".$i;
			$updateid_dtls="updateiddtls_".$i;
			$color_process="colorProcess_".$i;
			$color_id="colorId_".$i;
			$add_process="addProcess_".$i;
			$txt_add_process="txtAddProcess_".$i;
			$diaType="diaType_".$i;
			$batchid="batchid_".$i;
			  
			if(str_replace("'",'',$$updateid_dtls)=="")  
			{ 
				if ($add_comma!=0) $data_array1.=",";
				$data_array1.="(".$id1.",".$id.",".$$delivery_id.",".$$delevery_date.",".$$challen_no.",".$$orderid.",".$$item_id.",".$$compoid.",".$$diaType.",".$$batchid.",".$$bodypartid.",".$$add_process.",".$$txt_add_process.",".$$number_roll.",".$$quantity.",".$$libRateId.",".$$rate.",".$$libAddRateId.",".$$addRate.",".$$amount.",".$$remarks.",".$$curanci.",".$$color_process.",".$$color_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id1=$id1+1;
				$add_comma++;
				$id_arr_deli=implode(',',explode('_',str_replace("'",'',$$delivery_id)));
				$delv_id=explode(',',$id_arr_deli);
				//$id_arr_delivery=explode(',',$id_arr_deli);
				//$data_array_delivery[explode(',',str_replace("'",'',$$delivery_id))] =explode("*",("1"));
				foreach($delv_id as $val)
				{
					$id_arr_delivery[]=$val;
					$data_array_delivery[$val] =explode("*",("1"));
				}
			}
			else
			{
				$id_arr[]=str_replace("'",'',$$updateid_dtls);
				$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$delivery_id."*".$$delevery_date."*".$$challen_no."*".$$orderid."*".$$item_id."*".$$add_process."*".$$txt_add_process."*".$$color_id."*".$$number_roll."*".$$quantity."*".$$libRateId."*".$$rate."*".$$libAddRateId."*".$$addRate."*".$$amount."*".$$remarks."*".$$curanci."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				$id_arr_deli=implode(',',explode('_',str_replace("'",'',$$delivery_id)));
				$delv_id=explode(',',$id_arr_deli);
				//$id_arr_delivery=explode(',',$id_arr_deli);
				//$data_array_delivery[explode(',',str_replace("'",'',$$delivery_id))] =explode("*",("1"));
				foreach($delv_id as $val)
				{
					$id_arr_delivery[]=$val;
					$data_array_delivery[$val] =explode("*",("1"));
				}
			}
			// echo $data_array1;die;
			//order table insert====================================================================================================
			if(str_replace("'",'',$$style_name)=="" || str_replace("'",'',$$buyer_name)=="")  
			{
				$order_id_arr[]=str_replace("'",'',$$orderid);
				$data_array_order[str_replace("'",'',$$orderid)] =explode("*",("".$$buyer_name."*".$$style_name.""));
			}
			else
			{
				$order_id_arr[]=str_replace("'",'',$$orderid);
				$data_array_order[str_replace("'",'',$$orderid)] =explode("*",("".$$buyer_name."*".$$style_name.""));	
			}
		}
		//$rID1=execute_query(bulk_update_sql_statement("subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
			//echo bulk_update_sql_statement( "subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr );
		if (str_replace("'",'',$cbo_party_source)==2)
		{
			$rID2=execute_query(bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_array_delivery,$id_arr_delivery ));
			if($rID2) $flag=1; else $flag=0;
			$rID4=execute_query(bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array_order,$data_array_order,$order_id_arr ));
			if($rID4) $flag=1; else $flag=0;
		}
		 
		if($data_array1!="")
		{
			//echo "insert into subcon_inbound_bill_dtls (".$field_array1.") values ".$data_array1; die;
			$rID1=sql_insert("subcon_inbound_bill_dtls",$field_array1,$data_array1,1);
			if($rID1) $flag=1; else $flag=0;
		}
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			/*else if($rID)
			{
				mysql_query("ROLLBACK");  
				echo "5**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}*/
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
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			/*else if($rID)
			{
				oci_rollback($con);
				echo "5**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}*/
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
		
		$nameArray= sql_select("select is_posted_account,post_integration_unlock from subcon_inbound_bill_mst where id='$id'");
		$posted_account=$nameArray[0][csf('is_posted_account')];
		$post_integration_unlock=$nameArray[0][csf('post_integration_unlock')];
		
		if($posted_account==1 && $post_integration_unlock==0)
		{
			echo "14**All Ready Posted in Accounting.";
			exit();
		}
		$field_array="bill_no*company_id*location_id*bill_date*party_id*party_source*bill_for*updated_by*update_date";
		$data_array="".$txt_bill_no."*".$cbo_company_id."*".$cbo_location_name."*".$txt_bill_date."*".$cbo_party_name."*".$cbo_party_source."*".$cbo_bill_for."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$return_no=str_replace("'",'',$txt_bill_no);
		
		
		$sql_dtls="Select id from subcon_inbound_bill_dtls where mst_id=$update_id and status_active=1 and is_deleted=0";
		$nameArray=sql_select( $sql_dtls );
		foreach($nameArray as $row)
		{
			$dtls_update_id_array[]=$row[csf('id')];
		}
		
		$id1=return_next_id( "id", "subcon_inbound_bill_dtls",1);
		
		$field_array1 ="id, mst_id, delivery_id, delivery_date, challan_no, order_id, item_id, febric_description_id, dia_width_type, batch_id, body_part_id, add_process, add_process_name, packing_qnty, delivery_qty, lib_rate_id, rate, add_rate_id, add_rate, amount, remarks, currency_id, process_id, color_id, inserted_by, insert_date";
			
		$field_array_up ="packing_qnty*add_process_name*delivery_qty*lib_rate_id*rate*add_rate_id*add_rate*amount*remarks*currency_id*updated_by*update_date";
		
		$field_array_delivery="bill_status";
		$field_array_order="cust_buyer*cust_style_ref";
		
		$add_comma=0;
		for($i=1; $i<=$tot_row; $i++)
		{
			$delivery_id="deliveryid_".$i;
			$delevery_date="txtDeleverydate_".$i;
			$challen_no="txtChallenno_".$i;
			$orderid="ordernoid_".$i;
			$item_id="itemid_".$i;
			$compoid="compoid_".$i;
			$bodypartid="bodypartid_".$i;
			$style_name="txtStylename_".$i;
			$buyer_name="txtBuyername_".$i;
			$number_roll="txtNumberroll_".$i;
			$quantity="txtDeliveryqnty_".$i;
			$libRateId="libRateId_".$i;
			$rate="txtRate_".$i;
			$libAddRateId="libAddRateId_".$i;
			$addRate="txtAddRate_".$i;
			$amount="txtAmount_".$i;
			$curanci="curanci_".$i;
			$remarks="remarksvalue_".$i;
			$updateid_dtls="updateiddtls_".$i;
			$color_process="colorProcess_".$i;
			$color_id="colorId_".$i;
			$add_process="addProcess_".$i;
			$txt_add_process="txtAddProcess_".$i;
			$diaType="diaType_".$i;
			$batchid="batchid_".$i;
				
			if(str_replace("'",'',$$updateid_dtls)=="")  
			{ 
			  if ($add_comma!=0) $data_array1 .=",";
			  $data_array1 .="(".$id1.",".$id.",".$$delivery_id.",".$$delevery_date.",".$$challen_no.",".$$orderid.",".$$item_id.",".$$compoid.",".$$diaType.",".$$batchid.",".$$bodypartid.",".$$add_process.",".$$txt_add_process.",".$$number_roll.",".$$quantity.",".$$libRateId.",".$$rate.",".$$libAddRateId.",".$$addRate.",".$$amount.",".$$remarks.",'".str_replace("'",'',$$curanci)."',".$$color_process.",".$$color_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			  $id1=$id1+1;
			  $add_comma++;
				
				$id_arr_deli=implode(',',explode('_',str_replace("'",'',$$delivery_id)));
				$delv_id=explode(',',$id_arr_deli);
				//$id_arr_delivery=explode(',',$id_arr_deli);
				//$data_array_delivery[explode(',',str_replace("'",'',$$delivery_id))] =explode("*",("1"));
				foreach($delv_id as $val)
				{
					$id_arr_delivery[]=$val;
					$data_array_delivery[$val] =explode("*",("1"));
				}
			}
			else
			{
				$id_arr[]=str_replace("'",'',$$updateid_dtls);
				
				$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$number_roll."*".$$txt_add_process."*".$$quantity."*".$$libRateId."*".$$rate."*".$$libAddRateId."*".$$addRate."*".$$amount."*".$$remarks."*".$$curanci."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));

				$id_arr_deli=implode(',',explode('_',str_replace("'",'',$$delivery_id)));
				$delv_id=explode(',',$id_arr_deli);
				//$id_arr_delivery=explode(',',$id_arr_deli);
				//$data_array_delivery[explode(',',str_replace("'",'',$$delivery_id))] =explode("*",("1"));
				foreach($delv_id as $val)
				{
					$id_arr_delivery[]=$val;
					$data_array_delivery[$val] =explode("*",("1"));
				}
			}
			//print_r ($data_array_delivery);
		    //order table insert====================================================================================================
			if(str_replace("'",'',$$style_name)=="" || str_replace("'",'',$$buyer_name)=="")  
			{
				$order_id_arr[]=str_replace("'",'',$$orderid);
				$data_array_order[str_replace("'",'',$$orderid)] =explode("*",("".$$buyer_name."*".$$style_name.""));
			}
			else
			{
				$order_id_arr[]=str_replace("'",'',$$orderid);
				$data_array_order[str_replace("'",'',$$orderid)] =explode("*",("".$$buyer_name."*".$$style_name.""));	
			}
		//order table insert====================================================================================================
		}
		
		$rID=sql_update("subcon_inbound_bill_mst",$field_array,$data_array,"id",$id,0);
		if($rID) $flag=1; else $flag=0;
		
		$rID1=execute_query(bulk_update_sql_statement("subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
		if($rID1) $flag=1; else $flag=0;
			//echo $flag;die;
		
			
		if($data_array1!="")
		{
			//echo "insert into subcon_inbound_bill_dtls (".$field_array1.") values ".$data_array1;die;
			$rID1=sql_insert("subcon_inbound_bill_dtls",$field_array1,$data_array1,0);
			if($rID1) $flag=1; else $flag=0;
		}
		
		//echo $delete_id; die;
		/*if (str_replace("'",'',$cbo_party_source)==1)
		{*/
			if(implode(',',$id_arr)!="")
			{
				$distance_delete_id=implode(',',array_diff($dtls_update_id_array,$id_arr));
			}
			else
			{
				$distance_delete_id=implode(',',$dtls_update_id_array);
			}
			if(str_replace("'",'',$distance_delete_id)!="")
			{
				$rID3=execute_query( "delete from subcon_inbound_bill_dtls where id in ($distance_delete_id)",0);
				if($rID3) $flag=1; else $flag=0;
			}
		/*}
		else
		{
			if(implode(',',$id_arr)!="")
			{
				$distance_delete_id=implode(',',array_diff($dtls_update_id_array,$id_arr));
			}
			else
			{
				$distance_delete_id=implode(',',$dtls_update_id_array);
			}
			if(str_replace("'",'',$distance_delete_id)!="")
			{
				$rID3=execute_query( "delete from subcon_inbound_bill_dtls where id in ($distance_delete_id)",0);
				if($rID3) $flag=1; else $flag=0;
			}
		}*/
		if (str_replace("'",'',$cbo_party_source)==2)
		{
			$rID2=execute_query(bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_array_delivery,$id_arr_delivery ));
			if($rID2) $flag=1; else $flag=0;
			
			//echo bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array_order,$data_array_order,$order_id_arr );die;
			$rID4=execute_query(bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array_order,$data_array_order,$order_id_arr ));
			if($rID4) $flag=1; else $flag=0;
		}

				
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			/*else if($rID && $rID1 && $rID2 && $rID4)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}*/
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
			/*else if($rID && $rID1 && $rID2 && $rID4)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}*/
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}	
		disconnect($con);
		die;
	}
	else if ($operation==2)  //Delete here======================================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=str_replace("'",'',$update_id);
		$return_no=str_replace("'",'',$txt_bill_no);
		$field_array_delivery="bill_status";
		if(str_replace("'",'',$delete_id)!="")
		{
			$delete_id=str_replace("'",'',$delete_id);
			$rID3=execute_query( "delete from subcon_inbound_bill_dtls where delivery_id in ($delete_id)",0);
			$delete_id=explode(",",str_replace("'",'',$delete_id));
			for ($i=0;$i<count($delete_id);$i++)
			{
				$id_delivery[]=$delete_id[$i];
				$data_delivery[str_replace("'",'',$delete_id[$i])] =explode(",",("0"));
			}
		}
		//echo bulk_update_sql_statement( "subcon_delivery", "id",$field_array_delivery,$data_delivery,$id_delivery );
		$rID4=execute_query(bulk_update_sql_statement( "subcon_delivery", "id",$field_array_delivery,$data_delivery,$id_delivery ));
		
		if($db_type==0)
		{
			if($rID3 && $rID4)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		disconnect($con);
		die;
	}
}
//======================================================================Bill Print============================================================================================
if($action=="fabric_finishing_print") 
{
    extract($_REQUEST);
	$data=explode('*',$data);

	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library",'master_tble_id','image_location');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$party_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name");
	$color_library=return_library_array( "select id,color_name from  lib_color", "id","color_name");
	$color_id_arr=return_library_array( "select id, color_id from subcon_delivery_dtls",'id','color_id');
	$inv_item_arr=return_library_array( "select id,material_description from sub_material_dtls",'id','material_description');
	$prod_item_arr=return_library_array( "select id,fabric_description from subcon_production_dtls",'id','fabric_description');
	$prod_process_arr=return_library_array( "select cons_comp_id, process from subcon_production_dtls",'cons_comp_id','process');
	$prod_dtls_arr=return_library_array( "select id, product_name_details from  product_details_master",'id','product_name_details');
	$location_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
	
	$sql_mst="Select id, bill_no, bill_date, location_id, party_id, party_source, bill_for, terms_and_condition from subcon_inbound_bill_mst where company_id=$data[0] and id='$data[1]' and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql_mst);
	?>
    <div style="width:930px;">
    <table width="100%" cellpadding="0" cellspacing="0" >
        <tr>
            <td width="100" align="right"> 
            	<img  src='../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='100%' width='100%' />
            </td>
            <td>
                <table width="800" cellspacing="0" align="center">
                    <tr>
                    	<td align="center" style="font-size:20px"><strong ><? echo $company_library[$data[0]]; ?></strong></td>
                    </tr>
                    <tr>
                        <td align="center"  style="font-size:16px"><strong>Unit : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td  align="center" style="font-size:14px">  
							<?
								$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website, vat_number from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
								foreach ($nameArray as $result)
								{ 
									?>
									<? echo $result[csf('plot_no')]; ?> &nbsp; 
									<? echo $result[csf('level_no')]?> &nbsp; 
									<? echo $result[csf('road_no')]; ?> &nbsp; 
									<? echo $result[csf('block_no')];?> &nbsp; 
									<? echo $result[csf('city')];?> &nbsp; 
									<? echo $result[csf('zip_code')]; ?> &nbsp; 
									<? echo $result[csf('province')];?> &nbsp;
									<? echo $result[csf('contact_no')];?> &nbsp; 
									<? echo $result[csf('email')];?> &nbsp; <br>
									<b>Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
								}
                            ?> 
                        </td>  
                    </tr>
                    <tr>
                    	<td align="center" style="font-size:18px"><strong><? echo $data[3]; ?></strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table> 
    <table width="930" cellspacing="0" align="" border="0">   
    	  <tr><td colspan="6" align="center"><hr></hr></td></tr>
             <tr>
			 <?
			 	if($dataArray[0][csf('party_source')]==2)
				{
					$party_add=$dataArray[0][csf('party_id')];
					$nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add"); 
					foreach ($nameArray as $result)
					{ 
                    	$address="";
						if($result!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
					}
					$party_details=$party_library[$party_add].'<br>'.$address;
				}
				else if($dataArray[0][csf('party_source')]==1)
				{
					$party_details=$company_library[$dataArray[0][csf('party_id')]];
				}
			 ?>
                <td width="300" rowspan="4" valign="top" colspan="2"><strong>Party :<? echo $party_details; ?></strong></td>
                <td width="130"><strong>Bill No :</strong></td> <td width="175"><? echo $dataArray[0][csf('bill_no')]; ?></td>
                <td width="130"><strong>Bill Date: </strong></td><td width="175px"> <? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
            </tr>
             <tr>
                <td><strong>Source :</strong></td> <td><? echo $knitting_source[$dataArray[0][csf('party_source')]]; ?></td>
                <td><strong>Bill For :</strong></td> <td><? echo $bill_for[$dataArray[0][csf('bill_for')]]; ?></td>
            </tr>
        </table>
        <br>
        <?
		$batch_array=array(); $order_array=array();
		$grey_color_array=array();
		$grey_sql="Select a.color_id, b.fabric_from, b.po_id, b.id, b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0";
		$grey_sql_result =sql_select($grey_sql);
		foreach($grey_sql_result as $row)
		{
			//$batch_array[$row[csf('id')]]=$row[csf('fabric_from')];
			$batch_array[$row[csf('id')]]['color']=$row[csf('color_id')];
			$batch_array[$row[csf('id')]]['item_description']=$row[csf('item_description')];
		}	
		
		if($dataArray[0][csf('party_source')]==2)
		{
			$order_sql="select id, order_no, order_uom, cust_buyer, cust_style_ref from subcon_ord_dtls where status_active=1 and is_deleted=0";
			$order_sql_result =sql_select($order_sql);
			foreach($order_sql_result as $row)
			{
				$order_array[$row[csf('id')]]['order_no']=$row[csf('order_no')];
				$order_array[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
				$order_array[$row[csf('id')]]['cust_buyer']=$row[csf('cust_buyer')];
				$order_array[$row[csf('id')]]['cust_style_ref']=$row[csf('cust_style_ref')];
			}
		}
		else if($dataArray[0][csf('party_source')]==1)
		{
			$order_sql="select a.buyer_name, a.style_ref_no, a.order_uom, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			$order_sql_result =sql_select($order_sql);
			foreach($order_sql_result as $row)
			{
				$order_array[$row[csf('id')]]['order_no']=$row[csf('po_number')];
				$order_array[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
				$order_array[$row[csf('id')]]['cust_buyer']=$party_library[$row[csf('buyer_name')]];
				$order_array[$row[csf('id')]]['cust_style_ref']=$row[csf('style_ref_no')];
			}
			$recChallan_arr=array();
			
			$rec_challa_sql="SELECT a.recv_number_prefix_num, a.challan_no, b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.dia_width_type, c.po_breakdown_id
							FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d
							WHERE a.id=b.mst_id and b.id=c.dtls_id and d.id=b.batch_id and c.trans_type=1 and c.entry_form in (7,37) and c.trans_id!=0 and a.entry_form in (7,37) AND a.knitting_source=1 AND a.company_id='".$dataArray[0][csf('party_id')]."' AND a.location_id='".$dataArray[0][csf('location_id')]."' AND a.knitting_company=$data[0] and a.receive_basis in(4,5,9) and b.trans_id!=0  and a.item_category=2 
							and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
							group by a.id, a.recv_number_prefix_num, a.challan_no, b.batch_id, b.prod_id, b.body_part_id, b.fabric_description_id, b.dia_width_type, c.po_breakdown_id order by a.recv_number_prefix_num DESC";
			$rec_challa_sql_res=sql_select($rec_challa_sql);
			foreach($rec_challa_sql_res as $row)
			{
				$recChallan_arr[$row[csf('recv_number_prefix_num')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('dia_width_type')]]=$row[csf('challan_no')];
			}
		}
		//var_dump($recChallan_arr);
		 
		 if($data[4]==1)
		 {
		 ?>
	<div style="width:100%;">
		<table align="right" cellspacing="0" width="930"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center" style="font-size:12px"> 
                <th width="30">SL</th>
                <th width="60">Challan & <br> Delv. Date</th>
                <th width="50">Rec. Challan</th>
                <th width="80">Order</th> 
                <th width="70">Buyer  & <br> Style</th>
                <th width="120">Fabric Des.</th>
                <th width="60">D.W Type</th>
                <th width="60">Color</th>
                <th width="100">A.Process</th>
                <th width="30">Roll</th>
                <th width="60">Bill Qty</th>
                <th width="30">UOM</th>
                <th width="30">Rate (Main)</th>
                <th width="30">Rate (Add)</th>
                <th width="60">Amount</th>
                <th width="50">Currency</th>
                <th>Remarks</th>
            </thead>
		 <?
     		$i=1;
			$mst_id=$dataArray[0][csf('id')];
			$sql_result =sql_select("select id, delivery_id, delivery_date, challan_no, order_id, item_id, batch_id, body_part_id, febric_description_id, dia_width_type, color_id, packing_qnty, delivery_qty, rate, add_rate, amount, remarks, currency_id, process_id, add_process, add_process_name from subcon_inbound_bill_dtls  where mst_id='$mst_id' and status_active=1 and is_deleted=0 order by challan_no"); 
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$process=explode(',',$row[csf("add_process")]);
				$add_process="";
				foreach($process as $inf)
				{
					if($add_process=="") $add_process=$conversion_cost_head_array[$inf]; else $add_process.=", ".$conversion_cost_head_array[$inf];
				}
                
				if($dataArray[0][csf('party_source')]==2)
				{
					$item_all= explode(',',$batch_array[$row[csf('item_id')]]['item_description']);
				}
				else if($dataArray[0][csf('party_source')]==1)
				{
					$item_all= explode(',',$row[csf('item_id')]);
				}
				$item_name="";
				foreach($item_all as $inf)
				{
					if($dataArray[0][csf('party_source')]==2)
					{
						if($item_name=="") $item_name=$inf; else $item_name.=", ".$inf;
					}
					else if($dataArray[0][csf('party_source')]==1)
					{
						if($item_name=="") $item_name=$prod_dtls_arr[$inf]; else $item_name.=", ".$prod_dtls_arr[$inf];
					}
				}
				//echo $row[csf('challan_no')].'='.$row[csf('order_id')].'='.$row[csf('item_id')].'='.$row[csf('batch_id')].'='.$row[csf('body_part_id')].'='.$row[csf('febric_description_id')].'='.$row[csf('dia_width_type')].'='.change_date_format($row[csf('delivery_date')]).'<br>';
				$rec_challan="";
				$rec_challan=$recChallan_arr[$row[csf('challan_no')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('dia_width_type')]];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:12px"> 
                    <td><? echo $i; ?></td>
                    <td align="center"><p><? echo $row[csf('challan_no')].'<br>'.change_date_format($row[csf('delivery_date')]); ?></p></td>
                    <td><p><? echo $rec_challan;//$recChallan_arr[$row[csf('challan_no')]][change_date_format($row[csf('delivery_date')])]; ?></p></td>
                    <td><div style="word-wrap:break-word; width:80px"><? echo $order_array[$row[csf('order_id')]]['order_no']; ?></div></td>
                    <td align="center"><p><? echo $order_array[$row[csf('order_id')]]['cust_buyer'].'<br>'.$order_array[$row[csf('order_id')]]['cust_style_ref']; ?></p></td>
                    <td><p><? echo $item_name; ?></p></td>
                    <td><p><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></p></td>
                    <td><div style="word-wrap:break-word; width:70px"><? echo $color_library[$row[csf('color_id')]]; ?></div></td>
                    <td><p><? echo $row[csf('add_process_name')];//$add_process; ?></p></td>
                    <td align="right"><p><? echo $row[csf('packing_qnty')]; $tot_packing_qty+=$row[csf('packing_qnty')]; ?>&nbsp;</p></td>
                    <td align="right"><p><? echo number_format($row[csf('delivery_qty')],2,'.',''); $tot_delivery_qty+=$row[csf('delivery_qty')]; ?>&nbsp;</p></td>
                    <td><p><? echo $unit_of_measurement[12]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf('rate')],2,'.',''); ?>&nbsp;</p></td>
                    <td align="right"><p><? echo number_format($row[csf('add_rate')],2,'.',''); ?>&nbsp;</p></td>
                    <td align="right"><p><? echo number_format($row[csf('amount')],2,'.','');  $total_amount += $row[csf('amount')]; ?>&nbsp;</p></td>

                    <td align="center"><? echo $currency[$row[csf('currency_id')]]; ?></td>

                    <td><p><? echo $row[csf('remarks')]; ?></p></td>
                    <? 
					$carrency_id=$row[csf('currency_id')];
					if($carrency_id==1) $paysa_sent="Paisa"; else if($carrency_id==2) $paysa_sent="CENTS";
				    ?>
                </tr>
                <?php
                $i++;
			}
			?>
        	<tr style="font-size:12px"> 
                <td align="right" colspan="9"><strong>Total</strong></td>
                <td align="right"><? echo $tot_packing_qty; ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_delivery_qty,2,'.',''); ?>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><? echo $format_total_amount=number_format($total_amount,2,'.',''); ?>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
           <tr>
               <td colspan="17" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
           </tr>
        </table>
        <?
		 }
		 elseif($data[4]==2)
		 {
			 ?>
            <div style="width:111%;">
            <table align="right" cellspacing="0" width="1030"  border="1" rules="all" class="rpt_table" >
                <thead bgcolor="#dddddd" align="center">
                    <th width="30">SL</th>
                    <th width="65" align="center">Sys. Challan & <br> Delv. Date</th>
                    <th width="50">Rec. Challan</th>
                    <th width="100" align="center">Order</th> 
                    <th width="80" align="center">Buyer & <br> Style</th>
                    <th width="70" align="center">Color</th>
                    <th width="130" align="center">A.Process</th>
                    <th width="30" align="center">Roll</th>
                    <th width="70" align="center">Bill Qty</th>
                    <th width="50" align="center">Rate (Main)</th>
                    <th width="30" align="center">Rate (Add)</th>
                    <th width="70" align="center">Amount</th>
                    <th width="50" align="center">Currency</th>
                    <th width="" align="center">Remarks</th>
                </thead>
             <?
                $i=1;
                $mst_id=$dataArray[0][csf('id')];
                $sql_result =sql_select("select delivery_date, challan_no, order_id, color_id, currency_id, sum(packing_qnty) as packing_qnty, sum(delivery_qty) as delivery_qty, rate, add_rate, sum(amount) as amount, add_process, add_process_name, max(remarks) as remarks from subcon_inbound_bill_dtls  where mst_id='$mst_id' and status_active=1 and is_deleted=0 group by delivery_date, challan_no, order_id, color_id, currency_id, rate, add_rate, add_process, add_process_name order by delivery_date, challan_no, order_id, color_id, rate, add_process"); 
                foreach($sql_result as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    
					$process=explode(',',$row[csf("add_process")]);
					$add_process="";
					foreach($process as $inf)
					{
						if($add_process=="") $add_process=$conversion_cost_head_array[$inf]; else $add_process.=", ".$conversion_cost_head_array[$inf];
					}
					$rec_challan="";
					$rec_challan=$recChallan_arr[$row[csf('challan_no')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('dia_width_type')]];
				
                   ?>
                    <tr bgcolor="<? echo $bgcolor; ?>"> 
                        <td><? echo $i; ?></td>
                        <td align="center"><p><? echo $row[csf('challan_no')].'<br>'.change_date_format($row[csf('delivery_date')]);; ?></p></td>
                        <td><p><? echo $rec_challan; ?></p></td>
                        <td><div style="word-wrap:break-word; width:80px"><? echo $order_array[$row[csf('order_id')]]['order_no']; ?></div></td>
                        <td align="center"><p><? echo $order_array[$row[csf('order_id')]]['cust_buyer'].'<br>'.$order_array[$row[csf('order_id')]]['cust_style_ref']; ?></p></td>
                        <td><div style="word-wrap:break-word; width:70px"><? echo $color_library[$row[csf('color_id')]];//$color_library[$grey_color_array[$row[csf('order_id')]][$row[csf('item_id')]]]; ?></div></td>
                        <td><p><? echo $row[csf('add_process_name')]; ?></p></td>
                        <td align="right"><p><? echo $row[csf('packing_qnty')]; $tot_packing_qty+=$row[csf('packing_qnty')]; ?>&nbsp;</p></td>
                        <td align="right"><p><? echo number_format($row[csf('delivery_qty')],2,'.',''); $tot_delivery_qty+=$row[csf('delivery_qty')]; ?>&nbsp;</p></td>
                        <td align="right"><p><? echo number_format($row[csf('rate')],2,'.',''); ?>&nbsp;</p></td>
                        <td align="right"><p><? echo number_format($row[csf('add_rate')],2,'.',''); ?>&nbsp;</p></td>
                        <td align="right"><p><? echo number_format($row[csf('amount')],2,'.','');  $total_amount += $row[csf('amount')]; ?>&nbsp;</p></td>
                        <td align="center"><? echo $currency[$row[csf('currency_id')]]; ?></td>
                        <td><p><? echo $row[csf('remarks')]; ?></p></td>
                        <? 
                        $carrency_id=$row['currency_id'];
                        if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}
                       ?>
                    </tr>
                    <?
                    $i++;
                }
                ?>
                <tr> 
                    <td align="right" colspan="7"><strong>Total</strong></td>
                    <td align="right"><? echo $tot_packing_qty; ?>&nbsp;</td>
                    <td align="right"><? echo number_format($tot_delivery_qty,2,'.',''); ?>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo $format_total_amount=number_format($total_amount,2,'.',''); ?>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
               <tr>
                   <td colspan="14" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
               </tr>
            </table>
        <?			 
		 }
		 elseif($data[4]==3)
		 {
		 ?>
	<div style="width:100%;">
		<table align="right" cellspacing="0" width="930"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center" style="font-size:12px"> 
                <th width="30">SL</th>
                <th width="60">Challan & <br> Delv. Date</th>
                <th width="50">Rec. Challan</th>
                <th width="80">Order</th> 
                <th width="70">Buyer  & <br> Style</th>
                <th width="120">Fabric Des.</th>
                <th width="60">D.W Type</th>
                <th width="60">Color</th>
                <th width="100">A.Process</th>
                <th width="30">Roll</th>
                <th width="60">Bill Qty</th>
                <th width="30">UOM</th>
                <th width="30">Rate (Main)</th>
                <th width="30">Rate (Add)</th>
                <th width="60">Amount</th>
                <th width="50">Currency</th>
                <th>Remarks</th>
            </thead>
		 <?
     		$i=1;
			
			$sql_currency_result_usd=sql_select("SELECT conversion_rate from currency_conversion_rate WHERE con_date = (SELECT MAX(con_date) from currency_conversion_rate WHERE is_deleted=0 and status_active=1 and currency=2)");
			
			$mst_id=$dataArray[0][csf('id')];
			$sql_result =sql_select("select id, delivery_id, delivery_date, challan_no, order_id, item_id, batch_id, body_part_id, febric_description_id, dia_width_type, color_id, packing_qnty, delivery_qty, rate, add_rate, amount, remarks, currency_id, process_id, add_process, add_process_name from subcon_inbound_bill_dtls  where mst_id='$mst_id' and status_active=1 and is_deleted=0 order by challan_no"); 
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$process=explode(',',$row[csf("add_process")]);
				$add_process="";
				foreach($process as $inf)
				{
					if($add_process=="") $add_process=$conversion_cost_head_array[$inf]; else $add_process.=", ".$conversion_cost_head_array[$inf];
				}
                
				if($dataArray[0][csf('party_source')]==2)
				{
					$item_all= explode(',',$batch_array[$row[csf('item_id')]]['item_description']);
				}
				else if($dataArray[0][csf('party_source')]==1)
				{
					$item_all= explode(',',$row[csf('item_id')]);
				}
				$item_name="";
				foreach($item_all as $inf)
				{
					if($dataArray[0][csf('party_source')]==2)
					{
						if($item_name=="") $item_name=$inf; else $item_name.=", ".$inf;
					}
					else if($dataArray[0][csf('party_source')]==1)
					{
						if($item_name=="") $item_name=$prod_dtls_arr[$inf]; else $item_name.=", ".$prod_dtls_arr[$inf];
					}
				}
				//echo $row[csf('challan_no')].'='.$row[csf('order_id')].'='.$row[csf('item_id')].'='.$row[csf('batch_id')].'='.$row[csf('body_part_id')].'='.$row[csf('febric_description_id')].'='.$row[csf('dia_width_type')].'='.change_date_format($row[csf('delivery_date')]).'<br>';
				$rec_challan="";
				$rec_challan=$recChallan_arr[$row[csf('challan_no')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('dia_width_type')]];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:12px"> 
                    <td><? echo $i; ?></td>
                    <td align="center"><p><? echo $row[csf('challan_no')].'<br>'.change_date_format($row[csf('delivery_date')]); ?></p></td>
                    <td><p><? echo $rec_challan;//$recChallan_arr[$row[csf('challan_no')]][change_date_format($row[csf('delivery_date')])]; ?></p></td>
                    <td><div style="word-wrap:break-word; width:80px"><? echo $order_array[$row[csf('order_id')]]['order_no']; ?></div></td>
                    <td align="center"><p><? echo $order_array[$row[csf('order_id')]]['cust_buyer'].'<br>'.$order_array[$row[csf('order_id')]]['cust_style_ref']; ?></p></td>
                    <td><p><? echo $item_name; ?></p></td>
                    <td><p><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></p></td>
                    <td><div style="word-wrap:break-word; width:70px"><? echo $color_library[$row[csf('color_id')]]; ?></div></td>
                    <td><p><? echo $row[csf('add_process_name')];//$add_process; ?></p></td>
                    <td align="right"><p><? echo $row[csf('packing_qnty')]; $tot_packing_qty+=$row[csf('packing_qnty')]; ?>&nbsp;</p></td>
                    <td align="right"><p><? echo number_format($row[csf('delivery_qty')],2,'.',''); $tot_delivery_qty+=$row[csf('delivery_qty')]; ?>&nbsp;</p></td>
                    <td><p><? echo $unit_of_measurement[12]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf('rate')],2,'.',''); ?>&nbsp;</p></td>
                    <td align="right"><p><? echo number_format($row[csf('add_rate')],2,'.',''); ?>&nbsp;</p></td>
                    <td align="right"><p><? echo number_format($row[csf('amount')]/$sql_currency_result_usd[0][csf('conversion_rate')],2,'.','');  $total_amount += $row[csf('amount')]/$sql_currency_result_usd[0][csf('conversion_rate')]; ?>&nbsp;</p></td>

                    <td align="center"><? echo $currency[2]; ?></td>

                    <td><p><? echo $row[csf('remarks')]; ?></p></td>
                    <? 
					$carrency_id=$row[csf('currency_id')];
					if($carrency_id==1) $paysa_sent="Paisa"; else if($carrency_id==2) $paysa_sent="CENTS";
				    ?>
                </tr>
                <?php
                $i++;
			}
			?>
        	<tr style="font-size:12px"> 
                <td align="right" colspan="9"><strong>Total</strong></td>
                <td align="right"><? echo $tot_packing_qty; ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_delivery_qty,2,'.',''); ?>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><? echo $format_total_amount=number_format($total_amount,2,'.',''); ?>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
           <tr>
               <td colspan="17" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[2],$paysa_sent); ?></b></td>
           </tr>
        </table>
        <?
		 }
		?>
        <table width="930" align="left" > 
        	<tr><td colspan="2">&nbsp;</td> </tr>
            <tr><td colspan="2" align="center"><b>TERMS & CONDITION</b></td> </tr>
        <?
			$bill_no=$dataArray[0][csf('bill_no')];
			$sql_terms="Select id,terms from subcon_terms_condition where entry_form=2 and bill_no='$bill_no' ";
			$result_sql_terms =sql_select($sql_terms);
			$i=1;
			if(count($result_sql_terms)>0)
			{
				foreach($result_sql_terms as $rows)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
					<tr bgcolor="<? echo $bgcolor; ?>"> 
						<td width="30"><? echo $i; ?></td>
						<td><p><? echo $rows[csf('terms')]; ?></p></td>
					</tr>
				<?
				$i++;
				}
			}
			?>
        </table>
        <br>
		 <?
            echo signature_table(48, $data[0], "930px");
         ?>
   </div>
   </div>
<?
}

if($action=="terms_condition_popup")
{
	echo load_html_head_contents("Trems & Condition Search","../../", 1, 1, $unicode);
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

	function fnc_fabric_finishing_terms_condition( operation )
	{
		var row_num=$('#tbl_termcondi_details tr').length-1;
		var data_all="";
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('termscondition_'+i,'Term Condition')==false)
			{
				return;
			}
			data_all=data_all+get_submitted_data_string('txt_bill_no*termscondition_'+i,"../../");
		}
		var data="action=save_update_delete_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
		//freeze_window(operation);
		http.open("POST","sub_fabric_finishing_bill_issue_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_finishing_terms_condition_reponse;
	}

	function fnc_fabric_finishing_terms_condition_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			//if (reponse[0].length>2) reponse[0]=10;
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
<? echo load_freeze_divs ("../../",$permission);  ?>
    <fieldset>
    <input type="hidden" id="txt_bill_no" name="txt_bill_no" value="<? echo str_replace("'","",$txt_bill_no) ?>"/>
        <form id="termscondi_1" autocomplete="off">
        <table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
            <thead>
                <tr>
                    <th width="50">Sl</th><th width="530">Terms</th><th ></th>
                </tr>
            </thead>
            <tbody>
				<?
                $data_array=sql_select("select id, terms from  subcon_terms_condition where bill_no=$txt_bill_no");// quotation_id='$data'
                if(count($data_array)>0)
                {
					$i=0;
					foreach( $data_array as $row )
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$i++;
						?>
						<tr id="settr_1" align="center" bgcolor="<? echo $bgcolor;  ?>">
                            <td >
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
						if ($i%2==0) $bgcolor="#E9F3FF";  else  $bgcolor="#FFFFFF";
						$i++;
						?>
						<tr id="settr_1" align="center" bgcolor="<? echo $bgcolor;  ?>">
                            <td >
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
						echo load_submit_buttons( $permission, "fnc_fabric_finishing_terms_condition", 0,0 ,"reset_form('termscondi_1','','','','')",1) ; 
                    ?>
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

if($action=="save_update_delete_terms_condition")
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
		
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}		
		 $id=return_next_id( "id", "subcon_terms_condition", 1 ) ;
		 $field_array="id,bill_no,terms,entry_form";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			$termscondition="termscondition_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_bill_no.",".$$termscondition.",2)";
			$id=$id+1;
		 }
		// echo  $data_array;
		$rID_de3=execute_query( "delete from subcon_terms_condition where  bill_no =".$txt_bill_no."",0);

		$rID=sql_insert("subcon_terms_condition",$field_array,$data_array,1);
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$txt_bill_no;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$txt_bill_no;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);  
				echo "0**".$txt_bill_no;
			}
			else{
				oci_rollback($con);  
				echo "10**".$txt_bill_no;
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

if($action=="dyeing_rate_popup")
{
	echo load_html_head_contents("Dyeing Rate Popup","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>
	function js_set_value(val)
	{
		document.getElementById('hddn_all_data').value=val;
		parent.emailwindow.hide();
	}
	</script>
     <input type="hidden" id="hddn_all_data" />
	<?
	$buyer_arr=return_library_array("select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array("select id, color_name from lib_color",'id','color_name');
	
	//print_r($composition_arr);
	$sql="select id, const_comp, process_type_id, process_id, color_id, width_dia_id, in_house_rate, uom_id, rate_type_id, customer_rate, buyer_id, status_active from lib_subcon_charge where status_active!=0 and is_deleted=0 and rate_type_id in (3,4,7,8) and process_type_id=1 and comapny_id=$data order by id Desc";
	$result = sql_select($sql); $i=1;
	?>
    <table width="750" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
    	<thead>
        	<th width="25">SL</th>
            <th width="80">Buyer</th>
            <th width="160">Construction & Composition</th>
            <th width="80">Color</th>
            <th width="80">Rate type</th>
            <th width="110">Width/Dia type</th>
            <th width="80">In House Rate</th>
            <th width="60">UOM</th>
            <th>Customer Rate</th>
        </thead>
    </table>
    <div style="width:750; max-height:350px; overflow-y:scroll">
        <table cellpadding="0" width="750" class="rpt_table" rules="all" border="1" id="table_body">
            <tbody>
                <?
                foreach($result as $row)
                {
                    if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf("id")].'***'.$row[csf("in_house_rate")]; ?>')" id="tr_<? echo $i; ?>">
                        <td width="25"><? echo $i; ?></td>
                        <td width="80"><? echo $buyer_arr[$row[csf("buyer_id")]]; ?></td>
                        <td width="160"><? echo $row[csf("const_comp")]; ?></td>
                        <td width="80"><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
                        <td width="80"><? echo $production_process[$row[csf("rate_type_id")]]; ?></td>
                        <td width="110"><? echo $fabric_typee[$row[csf("width_dia_id")]]; ?></td>
                        <td width="80" align="right"><? echo number_format($row[csf("in_house_rate")],3); ?></td>
                        <td width="60"><? echo $unit_of_measurement[$row[csf("uom_id")]]; ?></td>
                        <td align="right"><? echo number_format($row[csf("customer_rate")],3); ?></td>
                    </tr>
					<?
                    $i++;
                }
                ?>
            </tbody>
        </table>
    </div>
    <script> setFilterGrid("table_body",-1); </script>
    <?
	exit();					
}

if($action=="dyeing_addRate_popup")
{
	echo load_html_head_contents("Dyeing Additional Rate Popup","../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>
	function js_set_value(val)
	{
		document.getElementById('hddn_all_data').value=val;
		parent.emailwindow.hide();
	}
	</script>
     <input type="hidden" id="hddn_all_data" />
	<?
	$buyer_arr=return_library_array("select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array("select id, color_name from lib_color",'id','color_name');
	
	//print_r($composition_arr);
	$sql="select id, const_comp, process_type_id, process_id, color_id, width_dia_id, in_house_rate, uom_id, rate_type_id, customer_rate, buyer_id, status_active from lib_subcon_charge where status_active!=0 and is_deleted=0 and rate_type_id in (3,4,7,8) and process_type_id=2 and comapny_id=$data order by id Desc";
	$result = sql_select($sql); $i=1;
	?>
    <table width="750" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
    	<thead>
        	<th width="25">SL</th>
            <th width="80">Buyer</th>
            <th width="160">Construction & Composition</th>
            <th width="80">Color</th>
            <th width="80">Rate type</th>
            <th width="110">Width/Dia type</th>
            <th width="80">In House Rate</th>
            <th width="60">UOM</th>
            <th>Customer Rate</th>
        </thead>
    </table>
    <div style="width:750; max-height:350px; overflow-y:scroll">
        <table cellpadding="0" width="750" class="rpt_table" rules="all" border="1" id="table_body">
            <tbody>
                <?
                foreach($result as $row)
                {
                    if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF"; 
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf("id")].'***'.$row[csf("in_house_rate")]; ?>')" id="tr_<? echo $i; ?>">
                        <td width="25"><? echo $i; ?></td>
                        <td width="80"><? echo $buyer_arr[$row[csf("buyer_id")]]; ?></td>
                        <td width="160"><? echo $row[csf("const_comp")]; ?></td>
                        <td width="80"><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
                        <td width="80"><? echo $production_process[$row[csf("rate_type_id")]]; ?></td>
                        <td width="110"><? echo $fabric_typee[$row[csf("width_dia_id")]]; ?></td>
                        <td width="80" align="right"><? echo number_format($row[csf("in_house_rate")],3); ?></td>
                        <td width="60"><? echo $unit_of_measurement[$row[csf("uom_id")]]; ?></td>
                        <td align="right"><? echo number_format($row[csf("customer_rate")],3); ?></td>
                    </tr>
					<?
                    $i++;
                }
                ?>
            </tbody>
        </table>
    </div>
    <script> setFilterGrid("table_body",-1); </script>
    <?
	exit();					
}

if($action=="bill_amount_check")
{
	$data=explode("_",$data);
	$orderIds=$data[0];
	$current_amount=$data[1];
	$update_id=$data[2];
	$job_arr=return_library_array("select id, job_no_mst from wo_po_break_down",'id','job_no_mst');
	$exc_rate_arr=return_library_array("select job_no, exchange_rate from wo_pre_cost_mst",'job_no','exchange_rate');
	
	$condition = new condition();
	if($orderIds!='' || $orderIds!=0)
	{
		$condition->po_id("in($orderIds)"); 
	}
	
	$condition->init();
	$conversion= new conversion($condition);
	//echo $conversion->getQuery(); die;
	$conversion_costing_arr=$conversion->getAmountArray_by_orderAndProcess();
	
	$budget_amount=0; $job_no='';
	$ex_po_id=array_unique(explode(",",$orderIds));
	foreach($ex_po_id as $po_id)
	{
		$budgetAmt=0;
		$budgetAmt=$conversion_costing_arr[$po_id][31]*$exc_rate_arr[$job_arr[$po_id]];
		$budget_amount+=$budgetAmt;
	}
	
	if($update_id!="") $thisbill_cond=" and a.id!='$update_id'"; else $thisbill_cond="";
	
	$previous_bill_sql=sql_select("select sum(b.amount) as amount from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.party_source=1 and a.process_id=4 and b.order_id in ($orderIds) $thisbill_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	
	$previous_bill_amount=$previous_bill_sql[0][csf('amount')];
	
	$total_bill_amount=$previous_bill_amount+$current_amount;
	$avaible_bill_amount=$budget_amount-$previous_bill_amount;
	
	$msg="Total bill amount exceeding costing amount not allowed.";
	
	if($total_bill_amount>$budget_amount)
	{
		echo "1"."_".rtrim($previous_bill_amount)."_".rtrim($budget_amount)."_".rtrim($avaible_bill_amount)."_".$msg;
		//echo $total_bill_amount."_".$budget_amount."_".$previous_bill_amount."_".$current_amount;
	}
	else
	{
		echo "0"."___";
	}
 	exit();
}

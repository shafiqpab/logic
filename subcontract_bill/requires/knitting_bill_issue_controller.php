<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');
require_once('../../includes/class4/class.conditions.php');
require_once('../../includes/class4/class.reports.php');
require_once('../../includes/class4/class.conversions.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$location_arr=return_library_array("select id,location_name from lib_location", "id","location_name");

// ================================Print button ==============================

if($action=="print_button_variable_setting")
{

    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=8 and report_id=133 and is_deleted=0 and status_active=1");
	 $printButton=explode(',',$print_report_format);
	   
	   echo "$('#search_2').hide();";
	   echo "$('#search_3').hide();";
	   echo "$('#search').hide();";
	   echo "$('#search_5').hide();";
	   echo "$('#search_6').hide();";

	foreach($printButton as $id){				
		if($id==66){echo "$('#search_2').show();";}
		else if($id==85){echo "$('#search_3').show();";}
		else if($id==706){echo "$('#search').show();";}		
		else if($id==129){echo "$('#search_5').show();";}	
		else if($id==161){echo "$('#search_6').show();";}		
	}

    exit();
}
// ======================= End Print button =================================================

if($action=="load_field_level_check")
{
	$user=$_SESSION['logic_erp']['user_id'];
	extract($_REQUEST);

	echo "$('#cbo_party_source').val(0);\n";
	echo "$('#cbo_party_source').removeAttr('disabled','disabled');\n";
	echo "load_drop_down('requires/knitting_bill_issue_controller',document.getElementById('cbo_company_id').value+'_'+0, 'load_drop_down_party_name', 'party_td');\n"; 
	echo "fnc_bill_for(0);\n";

	$sql="SELECT field_id, field_name, defalt_value, is_disable from field_level_access where page_id=186 and company_id=$data and user_id='$user' and status_active=1 and is_deleted=0";
	foreach (sql_select($sql) as $value) 
	{
		if($value[csf("field_id")]==1) //cbo_party_source
		{
			$default_value=$value[csf("defalt_value")];
			echo "$('#cbo_party_source').val($default_value);\n";
			if($value[csf("is_disable")]==1) { echo "$('#cbo_party_source').attr('disabled','disabled');\n"; }
			else{ echo "$('#cbo_party_source').removeAttr('disabled','disabled');\n"; }

			echo "load_drop_down( 'requires/knitting_bill_issue_controller',document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_source').value, 'load_drop_down_party_name', 'party_td' );\n"; 
			echo "fnc_bill_for(document.getElementById('cbo_party_source').value);\n";
		}	
	}
	$control_with=0; $rate_from=0;
	$sql_result = sql_select("select variable_list, dyeing_fin_bill from  variable_settings_subcon where company_id='$data' and variable_list in (3,9) order by id");
	foreach($sql_result as $result)
	{
		 if($result[csf("variable_list")]==9) $control_with=$result[csf("dyeing_fin_bill")];
		 if($result[csf("variable_list")]==3) $rate_from=$result[csf("dyeing_fin_bill")];
	}
	echo "$('#hddn_control_with').val(".$control_with.");\n";
	echo "$('#hidd_rate_from').val(".$rate_from.");\n";
}
	
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","","","","","",3 );
	exit();	 
}
if ($action=="load_drop_down_party_location")
{
	echo create_drop_down( "cbo_party_location", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","","","","","",3 );
	exit();	 
}

if ($action=="load_drop_down_location_pop")
{
	echo create_drop_down( "cbo_location_name", 120, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","","","","","",3 );
	exit();	 
}

if ($action=="load_drop_down_party_name")
{
	$data=explode('_',$data);
	
	if($data[1]==2)
	{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "","","","","","",5 ); 
	}
	else if($data[1]==1)
	{	
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select Party --", $selected, "load_drop_down( 'requires/knitting_bill_issue_controller', this.value, 'load_drop_down_party_location', 'party_location_td');","","","","","",5 ); 
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"","","","",5);
	}
	/* else if($data[1]==1)
	{	
		$party_arr=return_library_array("select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id","company_name");
		$value = 1;
		if(count($party_arr)==1){
			$value =0;
		}
		echo create_drop_down( "cbo_party_name", 150, $party_arr,"",1, "-- Select Party --", $value, "load_drop_down( 'requires/knitting_bill_issue_controller', this.value, 'load_drop_down_party_location', 'party_location_td');","","","","","",5 ); 
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"","","","",5);
	} */
	exit();
}

if ($action=="load_drop_down_party_name_popup")
{
	$data=explode('_',$data);
	if($data[1]==2)
	{
		echo create_drop_down( "cbo_party_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "","","","","","",5 ); 
	}
	else if($data[1]==1)
	{	
		echo create_drop_down( "cbo_party_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select Party --", $selected, "load_drop_down( 'knitting_bill_issue_controller', this.value, 'load_drop_down_location_pop', 'location_td');","","","","","",5 ); 
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 130, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"","","","",5);
	}
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
		
		function fnc_chanllan(val)
		{
			if(val==1)
			{
				$('#txt_search_challan').removeAttr('disabled','disabled');
				$('#txt_sys_challan').removeAttr('disabled','disabled');
			}
			else
			{
				$('#txt_search_challan').attr('disabled','disabled');
				$('#txt_sys_challan').attr('disabled','disabled');
			}
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="knittingbill_1"  id="knittingbill_1" autocomplete="off">
                <table width="980" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                     <tr>
                         <th colspan="10" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",'1' ); ?></th>
                    </tr>
                     <tr>
                        <th width="130">Company Name</th>
                        <th width="100">Source</th>
                        <th width="130">Party Name</th>
                        <th width="120">Location</th>
                        <th width="70">Bill ID</th>
                        <th width="80">Rec. Challan No.</th>
                        <th width="80">Rec. Sys. Challan</th>
                        <th width="170" colspan="2">Date Range</th>
                        <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>           
                     </tr>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> <input type="hidden" id="issue_id">  
								<?   
									echo create_drop_down( "cbo_company_id", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $ex_data[0],"load_drop_down( 'knitting_bill_issue_controller', this.value, 'load_drop_down_party_name_popup', 'party_td' );load_drop_down( 'knitting_bill_issue_controller', this.value, 'load_drop_down_location_pop', 'location_td');",0 );
                                ?>
                            </td>
                            <td>
								<?  
									echo create_drop_down( "cbo_party_source", 100, $knitting_source,"",1, "-Select Source-",$ex_data[2],"load_drop_down( 'knitting_bill_issue_controller', document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_party_name_popup', 'party_td' ); fnc_chanllan(this.value);",0,'1,2' );
                                ?>
                            </td>
                            <td width="130" id="party_td">
								<?
									echo create_drop_down( "cbo_party_name", 130, $blank_array,"", 1, "-- Select Party --", 0, "","","","","","",5 );
                                ?> 
                            </td>
                            <td id="location_td">
								<?
								if($ex_data[0]>0)
								{ 
								//echo "select id,location_name from lib_location where company_id=".$ex_data[0]." and status_active =1 and is_deleted=0 order by location_name";
									$blank_loc="select id,location_name from lib_location where company_id=".$ex_data[0]." and status_active =1 and is_deleted=0 order by location_name";
								}
								else
								{
									$blank_loc=$blank_array;
								}
								echo create_drop_down( "cbo_location_name", 120, $blank_loc,"", 1, "--Select Location--", $selected,"","","","","","",3);
								?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:65px" placeholder="Write" />
                            </td>
                            <td>
                                <input type="text" name="txt_search_challan" id="txt_search_challan" class="text_boxes" style="width:75px" placeholder="Write" disabled />
                            </td>
                            <td>
                                <input type="text" name="txt_sys_challan" id="txt_sys_challan" class="text_boxes" style="width:75px" placeholder="Write" disabled />
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                            </td>
                            <td>
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_party_source').value+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_search_challan').value+'_'+document.getElementById('txt_sys_challan').value, 'kniting_bill_list_view', 'search_div', 'knitting_bill_issue_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="10" align="center" valign="middle">
								<? echo load_month_buttons(1);  ?>
                            </td>
                        </tr>
                    </tbody>
                </table> 
                <div id="search_div" style="margin-top:10px;"></div>   
            </form>
        </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    <script> load_drop_down( 'knitting_bill_issue_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_source').value, 'load_drop_down_party_name_popup', 'party_td' ); fnc_chanllan(document.getElementById('cbo_party_source').value);</script>
    <script> load_drop_down( 'knitting_bill_issue_controller', document.getElementById('cbo_party_name').value, 'load_drop_down_location_pop', 'location_td'); $('#cbo_company_id').attr('disabled','disabled');</script>
	</html>
	<?
	exit();
}

if ($action=="kniting_bill_list_view")
{
	$data=explode('_',$data);
	$search_type=$data[7];
	$year_id=$data[8];
	if ($data[0]!=0) $company_name=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $party_name_cond=" and a.party_id='$data[1]'"; else $party_name_cond="";
	
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $return_date = "and a.bill_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $return_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $return_date = "and a.bill_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $return_date ="";
	}
	
	if ($data[5]!=0) $source_cond=" and a.party_source='$data[5]'"; else $source_cond="";
	if ($data[6]!=0) $location_cond=" and a.location_id='$data[6]'"; else $location_cond="";
	
	if($search_type==1)
	{
		if ($data[4]!='') $bill_id_cond=" and a.prefix_no_num='$data[4]'"; else $bill_id_cond="";
		if ($data[9]!='') $recChallan_cond=" and challan_no='$data[9]'"; else $recChallan_cond="";
	}
	else if($search_type==4 || $search_type==0) // all/Content
	{
		if ($data[4]!='') $bill_id_cond=" and a.prefix_no_num like '%$data[4]%'"; else $bill_id_cond="";
		if ($data[9]!='') $recChallan_cond=" and challan_no like '%$data[9]%'"; else $recChallan_cond="";
	}
	else if($search_type==2) //Starts With
	{
		if($db_type == 0)
        {
			if ($data[4]!='') $bill_id_cond=" and a.prefix_no_num like '$data[4]%'"; else $bill_id_cond="";
			if ($data[9]!='') $recChallan_cond=" and challan_no like '$data[9]%'"; else $recChallan_cond="";
		}
		else
		{
			if ($data[4]!='') $bill_id_cond=" and regexp_like (prefix_no_num, '^".trim($data[4])."')"; else $bill_id_cond="";
			if ($data[9]!='') $recChallan_cond=" and regexp_like(challan_no, '^".trim($data[9])."')"; else $recChallan_cond="";
		}
	}
	else if($search_type==3)
	{
		if ($data[4]!='') $bill_id_cond=" and a.prefix_no_num like '%$data[4]'"; else $bill_id_cond="";
		if ($data[9]!='') $recChallan_cond=" and challan_no like '%$data[9]'"; else $recChallan_cond="";
	}
	
	if ($data[10]!='') $recSysChallan_cond=" and recv_number_prefix_num='$data[10]'"; else $recSysChallan_cond="";	
	
	if($db_type==0) $year_cond= "year(a.insert_date)as year";
	else if($db_type==2) $year_cond= "TO_CHAR(a.insert_date,'YYYY') as year";

	$cbo_year=str_replace("'","",$year_id);
	if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_id_cond=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
		$delivery_id_cond="group_concat(b.delivery_id)";
	}
	else if($db_type==2)
	{
		if(trim($cbo_year)!=0) $year_id_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
		$delivery_id_cond="rtrim(xmlagg(xmlelement(e,b.delivery_id,',').extract('//text()') order by b.delivery_id).GetClobVal(),',')";
	}
	
	$rec_man_challan_arr=array(); $rec_sys_challan_arr=array();
	$sql_rec="select id, recv_number_prefix_num, challan_no from inv_receive_master where status_active=1 and is_deleted=0 and item_category=13 $recChallan_cond $recSysChallan_cond";
	$sql_rec_result = sql_select($sql_rec); $recId=""; $tot_rows=0;
	foreach($sql_rec_result as $row)
	{
		$tot_rows++;
		$rec_man_challan_arr[$row[csf("id")]]=$row[csf("challan_no")];
		$rec_sys_challan_arr[$row[csf("id")]]=$row[csf("recv_number_prefix_num")];
		$recId.="'".$row[csf("id")]."',";
	}
	unset($sql_rec_result);
	$rec_id_cond="";
	if ($data[9]!='' || $data[10]!='')
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
	
	$sub_del_challan_arr=array(); $sub_del_sys_challan_arr=array();
	$sql_sub_challan="select a.delivery_prefix_num, a.challan_no, b.id from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0";
	$sql_sub_challan_result = sql_select($sql_sub_challan);
	foreach ($sql_sub_challan_result as $row)
	{
		$sub_del_challan_arr[$row[csf("id")]]=$row[csf("challan_no")];
		$sub_del_sys_challan_arr[$row[csf("id")]]=$row[csf("delivery_prefix_num")];
	}
	unset($sql_sub_challan_result);
	
	$sql= "select a.id, a.bill_no, a.prefix_no_num, $year_cond, a.location_id, a.bill_date, a.party_id, a.party_source, a.bill_for, $delivery_id_cond as delivery_id from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.process_id=2 $company_name $party_name_cond $source_cond $return_date $bill_id_cond $location_cond $year_id_cond $rec_id_cond group by a.id, a.bill_no, a.prefix_no_num, a.insert_date, a.location_id, a.bill_date, a.party_id, a.party_source, a.bill_for order by a.id DESC";
	
	//echo $sql; die;
	$company_id=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$party_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	?> 
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="70">Bill No</th>
                <th width="70">Year</th>
                <th width="120">Location</th>
                <th width="120">Party Source</th>
                <th width="80">Bill Date</th>
                <th width="120">Party</th>
                <th width="80">Bill For</th>
                <th width="80">Challan No</th>
                <th>Sys.Challan</th>
            </thead>
     	</table>
     </div>
     <div style="width:950px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table" id="tbl_po_list">
			<?
			$i=1; $result = sql_select($sql);
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($db_type==2) $row[csf('delivery_id')]= $row[csf('delivery_id')]->load();
				$challan_no=""; $bill_company=""; $sys_challan="";
				if($row[csf("party_source")]==1) 
				{
					$bill_company=$company_id[$row[csf("party_id")]];
					//$challan_no=$rec_man_challan_arr[$row[csf("delivery_id")]];
					$ex_del_id=explode(",",$row[csf("delivery_id")]);
					foreach($ex_del_id as $del_id)
					{
						if ($challan_no=="") $challan_no=$rec_man_challan_arr[$del_id]; else $challan_no.=','.$rec_man_challan_arr[$del_id];
						if ($sys_challan=="") $sys_challan=$rec_sys_challan_arr[$del_id]; else $sys_challan.=','.$rec_sys_challan_arr[$del_id];
					}
				}
				else 
				{
					$bill_company=$party_arr[$row[csf("party_id")]];
					$ex_del_id=explode("_",$row[csf("delivery_id")]);
					foreach($ex_del_id as $del_id)
					{
						if ($challan_no=="") $challan_no=$sub_del_challan_arr[$del_id]; else $challan_no.=','.$sub_del_challan_arr[$del_id];
						if ($sys_challan=="") $sys_challan=$sub_del_sys_challan_arr[$del_id]; else $sys_challan.=','.$sub_del_sys_challan_arr[$del_id];
					}
				}
				$unique_challan=implode(", ",array_unique(explode(',',$challan_no)));
				$unique_sys_challan=implode(", ",array_unique(explode(',',$sys_challan)));
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")]; ?>);" > 
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="70" align="center"><? echo $row[csf("prefix_no_num")]; ?></td>
                        <td width="70" align="center"><? echo $row[csf("year")]; ?></td>		
						<td width="120"><? echo $location_arr[$row[csf("location_id")]];  ?></td>	
                        <td width="120"><? echo $knitting_source[$row[csf("party_source")]];  ?></td>
						<td width="80"><? echo change_date_format($row[csf("bill_date")]); ?></td>
						<td width="120"><? echo $bill_company;?> </td>	
						<td width="80"><? echo $bill_for[$row[csf("bill_for")]]; ?></td>
                        <td width="80" style="word-wrap:break-word; word-break: break-all;"><? echo $unique_challan; ?>&nbsp;</td>
                        <td style="word-wrap:break-word; word-break: break-all;"><? echo $unique_sys_challan; ?>&nbsp;</td>
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
	
	$nameArray= sql_select("select id, bill_no, company_id, location_id, party_location_id, bill_date, party_id, party_source, attention,upcharge,discount, bill_for, bill_section, is_posted_account, post_integration_unlock from subcon_inbound_bill_mst where id='$data'");
	foreach ($nameArray as $row)
	{
		$loc_comp=0;
		if($row[csf("party_source")]==1) $loc_comp=$row[csf("party_id")]; else if ($row[csf("party_source")]==2) $loc_comp=$row[csf("company_id")];
			
		echo "document.getElementById('txt_bill_no').value 					= '".$row[csf("bill_no")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "load_drop_down( 'requires/knitting_bill_issue_controller', '".$row[csf("company_id")]."', 'load_drop_down_location', 'location_td' );\n";
		echo "document.getElementById('cbo_location_name').value			= '".$row[csf("location_id")]."';\n";
		
		echo "load_drop_down( 'requires/knitting_bill_issue_controller', '".$row[csf("party_id")]."', 'load_drop_down_party_location', 'party_location_td' );\n";
			
		echo "document.getElementById('cbo_party_location').value			= '".$row[csf("party_location_id")]."';\n"; 
		echo "document.getElementById('txt_bill_date').value 				= '".change_date_format($row[csf("bill_date")])."';\n";   
		echo "document.getElementById('cbo_party_source').value				= '".$row[csf("party_source")]."';\n"; 
		echo "document.getElementById('cbo_bill_section').value				= '".$row[csf("bill_section")]."';\n"; 
		echo "load_drop_down( 'requires/knitting_bill_issue_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_source').value, 'load_drop_down_party_name', 'party_td' );\n";
		echo "document.getElementById('hidden_acc_integ').value				= '".$row[csf("is_posted_account")]."';\n";
		echo "document.getElementById('hidden_integ_unlock').value			= '".$row[csf("post_integration_unlock")]."';\n";
		
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		//echo "accounting_integration_check(".$row[csf("is_posted_account")].");\n";
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
		
		echo "document.getElementById('cbo_party_name').value				= '".$row[csf("party_id")]."';\n"; 
		echo "document.getElementById('txt_attention').value				= '".$row[csf("attention")]."';\n"; 
		echo "document.getElementById('cbo_bill_for').value					= '".$row[csf("bill_for")]."';\n"; 
		if($row[csf("party_source")]==1)
		{
			echo "document.getElementById('txt_bill_form_date').value 			= '".change_date_format($mindate)."';\n";  
			echo "document.getElementById('txt_bill_to_date').value 			= '".change_date_format($maxdate)."';\n";  
		}
		
	    echo "document.getElementById('update_id').value            		= '".$row[csf("id")]."';\n";
		echo "disable_enable_fields('cbo_company_id*cbo_location_name*cbo_party_source*cbo_party_name*cbo_bill_for',1);\n";
		echo "document.getElementById('txt_upcharge').value				= '".$row[csf("upcharge")]."';\n"; 
		echo "document.getElementById('txt_discount').value				= '".$row[csf("discount")]."';\n"; 
		//echo "fnc_disable_mst_field(document.getElementById('cbo_party_name').value);\n";
	}	
	exit();
}

if ($action=="knitting_delivery_list_view")
{
	//echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	//echo $data;
	$data=explode('***',$data);
	
	$ex_bill_for=$data[4];
	$date_from=$data[5];
	$date_to=$data[6];
	$challan_no=$data[7];
	$sys_challan_no=$data[8];
	$update_id=$data[9];
	$str_data=$data[10];
	$job_id=$data[11];
	if($job_id)
	{
		$po_ids="";
		$po_sql="SELECT b.id from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.id=$job_id and a.status_active!=0 and b.status_active!=0";
		foreach (sql_select($po_sql) as $value) 
		{
			$po_ids .= $value[csf("id")].",";
		}
		$po_ids=chop($po_ids, ",");
	}
	//echo $po_ids;

	if($db_type==0)
	{ 
		if ($date_from!="" &&  $date_to!="") 
		$date_cond= "and a.receive_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $date_cond= "";
		$year_cond= "year(b.insert_date) as year";
		// if($in_house_knit_bill_from!=2)   //delevery_date
	}
	else if ($db_type==2)
	{
		if ($date_from!="" &&  $date_to!="") 
		{ 
			$date_cond= "and a.receive_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";
			$date_cond2= "and a.delevery_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";
		}
		else { $date_cond= ""; $date_cond2= "";
		
		}
		$year_cond= "TO_CHAR(b.insert_date,'YYYY') as year";
	}
	 //echo $data[2].'=='.$ex_bill_for.', ';die;
	//$delv_id=implode(',',explode('_',$data[8]));
	if($data[2]==2)
	{
		?>
		</head>
		<body>
			<div style="width:100%;">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="917px" class="rpt_table">
					<thead>
                    	<th width="40">&nbsp;</th>
						<th width="30">SL</th>
						<th width="70">Challan No</th>
						<th width="70">Delivery Date</th>
						<th width="110">Order No</th>                    
						<th width="180">Fabric Description</th>
						<th width="100">Delivery Qty</th>
						<th width="100">Delivery Pcs</th>
						<th width="100">Process</th>
						<th>Currency</th>
					</thead>
			 </table>
        </div>
        <div style="width:920px;max-height:180px; overflow-y:scroll" id="sewing_production_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900px" class="rpt_table" id="tbl_list_search">
            <?
				$item_id_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
				$bodyPartTypeArr = return_library_array( "select id, body_part_type from lib_body_part",'id','body_part_type');
				$item_body_part_id=return_library_array( "select id, body_part from lib_subcon_charge",'id','body_part');
				
				$delv_id=implode(',',explode('!!!!',$str_data));
				
				$order_array=array();
				$order_sql="Select b.id, b.order_no, b.order_uom, b.process_id, b.cust_buyer, b.cust_style_ref, b.rate, b.amount, a.currency_id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0";
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
					$order_array[$row[csf("id")]]['process_id']=$row[csf("process_id")];
				}
				/*$from_rate_sql="select dyeing_fin_bill from variable_settings_subcon where  company_id='$data[0]' and variable_list=$explode_data[0] order by id";
				$from_rate_sql_result =sql_select($from_rate_sql);
				foreach ($from_rate_sql_result as $row)
				{
					$rate_from=$row[csf("dyeing_fin_bill")]; 
				}*/
				
				
				unset($order_sql_result);
				$rate_array=array();
				$rate_sql="select order_id, item_id, rate from subcon_ord_breakdown ";
				$rate_sql_result =sql_select($rate_sql);
				foreach ($rate_sql_result as $row)
				{
					$rate_array[$row[csf("order_id")]][$row[csf("item_id")]]=$row[csf("rate")];
				}
				unset($rate_sql_result);
                $i=1;
				
				if($sys_challan_no!="") $sys_challan_cond=" and a.delivery_prefix_num in ($sys_challan_no)"; else $sys_challan_cond="";
				if(!$update_id)
				{
					$sql="select b.id, a.challan_no, a.delivery_date, b.process_id, b.item_id, b.delivery_qty, b.delivery_pcs, b.collar_cuff, b.order_id, b.carton_roll as roll_qty, 0 as type from subcon_delivery_mst a, subcon_delivery_dtls b where a.company_id='$data[0]' and a.location_id='$data[1]' and b.bill_status=0 and a.id=b.mst_id and a.party_id='$data[3]' and a.process_id='2' and a.status_active=1 and a.is_deleted=0 $sys_challan_cond"; 
				}
				else
				{
					$sql="(select b.id, a.challan_no, a.delivery_date, b.process_id, b.item_id, b.delivery_qty, b.delivery_pcs, b.collar_cuff, b.order_id, b.carton_roll as roll_qty, 0 as type from subcon_delivery_mst a, subcon_delivery_dtls b where a.company_id='$data[0]' and a.location_id='$data[1]' and a.id=b.mst_id and a.party_id='$data[3]' and a.process_id=2 and a.status_active=1 and b.bill_status=0 $sys_challan_cond)
					 union 
					 	(select b.id, a.challan_no, a.delivery_date, b.process_id, b.item_id, b.delivery_qty, b.delivery_pcs, b.collar_cuff, b.order_id, b.carton_roll as roll_qty, 1 as type from subcon_delivery_mst a, subcon_delivery_dtls b where a.company_id='$data[0]' and a.location_id='$data[1]' and a.id=b.mst_id and a.party_id='$data[3]' and a.process_id=2 and b.id in ( $delv_id ) and a.status_active=1 and b.bill_status=1) order by type DESC";
				}
				//echo $sql;
				$sql_result =sql_select($sql);
				
				foreach($sql_result as $row) // for update row
				{
					$process_id_val=$row[csf('process_id')]; $item_name="";
                    if($process_id_val==1 || $process_id_val==5) $item_name=$garments_item[$row[csf('item_id')]]; else $item_name=$item_id_arr[$row[csf('item_id')]];
					$all_value=$row[csf('id')];

					//checking coller & cuff subprocess is present or not
					if(in_array(3, explode(",", $order_array[$row[csf("order_id")]]['process_id']))==false) $subprocess_uom=0; else $subprocess_uom=1;

					$str_val=$row[csf('id')].'_'.change_date_format($row[csf('delivery_date')]).'_'.$row[csf('challan_no')].'_'.$row[csf('order_id')].'_'.$order_array[$row[csf('order_id')]]['order_no'].'_'.$order_array[$row[csf('order_id')]]['cust_style_ref'].'_'.$order_array[$row[csf('order_id')]]['cust_buyer'].'_'.$row[csf('roll_qty')].'_0__'.$item_body_part_id[$row[csf('item_id')]].'_'.$body_part[$item_body_part_id[$row[csf('item_id')]]].'_'.$row[csf('item_id')].'_'.$item_name.'_'.$row[csf('delivery_qty')].'_0_0_'.$order_array[$row[csf('order_id')]]['order_uom'].'_'.$row[csf('delivery_pcs')].'_'.$subprocess_uom.'_'.$row[csf('collar_cuff')].'_1_'.$bodyPartTypeArr[$item_body_part_id[$row[csf('item_id')]]].'_'.$rate_array[$row[csf("order_id")]][$row[csf("item_id")]];
					//$order_array[$row[csf('order_id')]]['order_uom'].'_1_'.$subprocess_uom.'_'.$row[csf('collar_cuff')].'_'.$bodyPartTypeArr[$item_body_part_id[$row[csf('item_id')]]];
						
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$checked_val=2; $ischeck="";
					if ($row[csf('type')]==0) 
					{
						$row_color=$bgcolor; $checked_val=2; $ischeck="";
					}
					else 
					{
						$bgcolor="yellow"; $checked_val=1; $ischeck="checked";
					}
					?>
					<tr id="tr_<? echo $all_value; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."***".$order_array[$row[csf("order_id")]]['currency_id']; ?>','<? echo $i; ?>');" >
						<td width="40" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="<? echo $checked_val; ?>" <? echo $ischeck; ?> ></td>
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="70" style="word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
						<td width="70"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
						<td width="110" style="word-break:break-all"><? echo $order_array[$row[csf('order_id')]]['order_no']; ?></td>
						<td width="180" style="word-break:break-all"><? echo $item_name; ?></td>
						<td width="100" align="right"><? echo $row[csf('delivery_qty')]; ?>&nbsp;</td>
						<td width="100" align="right"><? echo $row[csf('delivery_pcs')]; ?>&nbsp;</td>
						<td width="100" style="word-break:break-all"><? echo $production_process[$row[csf('process_id')]]; ?></td>
						<td style="word-break:break-all"><? echo $currency[$order_array[$row[csf("order_id")]]['currency_id']]; ?>
						<!-- <input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>"> -->
						<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>"> 
						<input type="hidden" id="checkAllId<? echo $i; ?>" value="<? echo $all_value; ?>">
						<input type="hidden" id="currid<? echo $row[csf('id')]; ?>" style="width:50px" value="<? echo $order_array[$row[csf("order_id")]]['currency_id']; ?>"></td>
					</tr>
					<?php
					$i++;
				}
				?>
                </table>
         </div>
        <div>
            <table width="920">
                <tr style="border:none">
                    <td bgcolor="#7FDF00" align="center"><input type="checkbox" name="checkall" id="checkall" class="formbutton" value="2" onClick="check_all_data();"/><b>Check all</b></td>
                    <td bgcolor="#FF80FF" align="center"><input type="button" id="show_button" align="middle" class="formbutton" style="width:100px" value="Close" onClick="window_close(0);" /></td>
                </tr>
           </table>
      	</div>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	}
	else if($data[2]==1)
	{
		if($ex_bill_for==3) $tbl_wight="820"; else $tbl_wight="1270";
		?>
		</head> 
		<body>
        <div id="list_view_body">
			<div>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_wight; ?>px" class="rpt_table">
					<thead>
                    <?
					if($ex_bill_for==4)
					{
						$td_head="Sales ";
					}
					else $td_head="";
					if($ex_bill_for==3)
					{
						?>
                    	<th width="30">&nbsp;</th>
						<th width="30">SL</th>
                        <th width="100">Buyer Name</th>
						<th width="60">Sys. Challan</th>
						<th width="70">Rec. Challan</th>
						<th width="70">Receive Date</th>
                        <th width="90">Body Part</th>
						<th width="160">Fabric Description</th>
                        <th width="60">Color Type</th>
						<th width="80">Receive Qty</th>
                        <th width="80">Rec. Qty Pcs</th>
                        <th>Roll Qty</th>
                        <?
					}
					else
					{
						?>
                        <th width="30">&nbsp;</th>
						<th width="30">SL</th>
						<th width="60">Sys. Challan</th>
						<th width="70">Rec. Challan</th>
						<th width="60">Receive Date</th>
                        <th width="80">Job</th>
                        <th width="100"><?=$td_head;?>Style</th>                    
						<th width="120"><?=$td_head;?>Order No</th>
                        <th width="90">Body Part</th>
						<th width="160">Fabric Description</th>
						<th width="70">Yarn Count</th>
						<th width="70">M/C Dia</th>
                        <th width="60">Color Type</th>
						<th width="80">Receive Qty</th>
                         <th width="80">Rec. Qty Pcs</th>
                        <th>Number of Roll</th>
                        <?
					}?>
					</thead>
			 </table>
        </div>
        <div style="width:<? echo $tbl_wight; ?>px;max-height:180px; overflow-y:scroll" id="sewing_production_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_wight; ?>px" class="rpt_table" id="tbl_list_search">
            <?
				$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
				// $product_dtls_arr=return_library_array( "select id, product_name_details from  product_details_master",'id','product_name_details');
				// $currency_arr=return_library_array( "select b.id, a.currency_id from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst",'id','currency_id');
				//$roll_no_arr=return_library_array( "select id, no_of_roll from  pro_grey_prod_entry_dtls",'id','no_of_roll');
				$determ_arr = return_library_array( "select mst_id, copmposition_id from lib_yarn_count_determina_dtls",'mst_id','copmposition_id');
				$bodyPartTypeArr = return_library_array( "select id, body_part_type from lib_body_part",'id','body_part_type');
				
				$nameArray_vari= sql_select("select id, dyeing_fin_bill, allow_per,variable_list from variable_settings_subcon where company_id='$data[3]' and variable_list=7    order by id");
				//echo "select id, dyeing_fin_bill, allow_per,variable_list from variable_settings_subcon where company_id='$data[3]' and variable_list=7    order by id";
				 $in_house_knit_bill_from=0; // Subcon Variable-In House Knit Bill From
				foreach($nameArray_vari as $row)
				{
					$in_house_knit_bill_from=$row[csf('dyeing_fin_bill')];
				}
				if($in_house_knit_bill_from==0) $in_house_knit_bill_from=2;
				// echo $in_house_knit_bill_from.'daaa';

				if($ex_bill_for!=3)
				{
					$bill_qty_array=array();$str_data="";
					$sql_bill="select mst_id,challan_no,rec_challan_no, order_id, febric_description_id, body_part_id, item_id, sum(packing_qnty) as roll_qty, sum(delivery_qty) as bill_qty,coller_cuff_measurement from subcon_inbound_bill_dtls where status_active=1 and is_deleted=0 group by mst_id,rec_challan_no,challan_no, order_id, febric_description_id, body_part_id, item_id,coller_cuff_measurement";
					$sql_bill_result =sql_select($sql_bill);
					
					foreach($sql_bill_result as $row)
					{
					 //	echo $update_id.'DS';
					 if($row[csf('challan_no')]=='') $row[csf('challan_no')]=0;
					  if($row[csf('rec_challan_no')]=='') $row[csf('rec_challan_no')]=0;
					  if($row[csf('coller_cuff_measurement')]=='') $row[csf('coller_cuff_measurement')]=0;
						$bill_qty_array[$row[csf('challan_no')]][$row[csf('rec_challan_no')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('coller_cuff_measurement')]]['qty']=$row[csf('bill_qty')];
						$bill_qty_array[$row[csf('challan_no')]][$row[csf('rec_challan_no')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('coller_cuff_measurement')]]['roll']=$row[csf('roll_qty')];
						if($row[csf('mst_id')]==$update_id)
						{
							 
							// if($row[csf('rec_challan_no')]=='') $row[csf('rec_challan_no')]=0;
							 if($str_data=="") $str_data=$row[csf('challan_no')].'_'.$row[csf('order_id')].'_'.$row[csf('item_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')].'_'.$row[csf('rec_challan_no')].'_'.$row[csf('coller_cuff_measurement')]; 
							 else $str_data.='!!!!'.$row[csf('challan_no')].'_'.$row[csf('order_id')].'_'.$row[csf('item_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')].'_'.$row[csf('rec_challan_no')].'_'.$row[csf('coller_cuff_measurement')];
							// echo $str_data.'=';
						}
					}
					unset($sql_bill_result);

				}
				else if($ex_bill_for==3)
				{
					$bill_qty_array=array();$str_data="";
					$sql_bill="select mst_id,challan_no,rec_challan_no, order_id, febric_description_id, body_part_id, item_id, sum(packing_qnty) as roll_qty, sum(delivery_qty) as bill_qty from subcon_inbound_bill_dtls where status_active=1 and is_deleted=0 group by mst_id,rec_challan_no,challan_no, order_id, febric_description_id, body_part_id, item_id";
					$sql_bill_result =sql_select($sql_bill);
					
					foreach($sql_bill_result as $row)
					{
					 //	echo $update_id.'DS';
					 if($row[csf('challan_no')]=='') $row[csf('challan_no')]=0;
					  if($row[csf('rec_challan_no')]=='') $row[csf('rec_challan_no')]=0;
						$bill_qty_array[$row[csf('challan_no')]][$row[csf('rec_challan_no')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['qty']=$row[csf('bill_qty')];
						$bill_qty_array[$row[csf('challan_no')]][$row[csf('rec_challan_no')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['roll']=$row[csf('roll_qty')];
						if($row[csf('mst_id')]==$update_id)
						{
							 //echo $update_id.'DS';
							// if($row[csf('rec_challan_no')]=='') $row[csf('rec_challan_no')]=0;
							 if($str_data=="") $str_data=$row[csf('challan_no')].'_'.$row[csf('order_id')].'_'.$row[csf('item_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')].'_'.$row[csf('rec_challan_no')]; 
							 else $str_data.='!!!!'.$row[csf('challan_no')].'_'.$row[csf('order_id')].'_'.$row[csf('item_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')].'_'.$row[csf('rec_challan_no')];
						}
					}
					unset($sql_bill_result);

				}
				$yarncountArr = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');
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
				$ex_str_data=explode("!!!!",$str_data);
				$str_arr=array();
				foreach($ex_str_data as $str)
				{
					$str_arr[]=$str;
				}
				
				if($db_type==0) 
				{
					$booking_without_order="IFNULL(a.booking_without_order,0)";
					$booking_without_order_roll="IFNULL(f.booking_without_order,0)";
					$knittingCharge="IFNULL(b.kniting_charge,0)";
				}
				else if ($db_type==2)
				{
					$booking_without_order="nvl(a.booking_without_order,0)";
					$booking_without_order_roll="nvl(f.booking_without_order,0)";
					$knittingCharge="nvl(b.kniting_charge,0)";
				}
				
				if($bill_for_id==0) $bill_for_id_cond=""; else $bill_for_id_cond="and d.booking_type='$bill_for_id'";
			
				$roll_dlv_arr=array();
				$sql_dlv="select a.id, a.sys_number, b.barcode_num, c.receive_basis, c.booking_no, c.booking_id, c.buyer_id from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, inv_receive_master c where a.id=b.mst_id and b.grey_sys_id=c.id and c.knitting_source=1 and c.company_id='$data[3]' and c.knitting_company='$data[0]' and c.entry_form=2 and b.status_active=1 and b.is_deleted=0";
				$sql_dlv_result =sql_select($sql_dlv);
				foreach($sql_dlv_result as $row)
				{
					/*$roll_dlv_arr[$row[csf('sys_number')]][$row[csf('barcode_num')]]['receive_basis']=$row[csf('receive_basis')];
					$roll_dlv_arr[$row[csf('sys_number')]][$row[csf('barcode_num')]]['booking_no']=$row[csf('booking_no')];
					$roll_dlv_arr[$row[csf('sys_number')]][$row[csf('barcode_num')]]['booking_id']=$row[csf('booking_id')];*/

					$roll_dlv_arr[$row[csf('sys_number')]]['receive_basis']=$row[csf('receive_basis')];
					$roll_dlv_arr[$row[csf('sys_number')]]['booking_no']=$row[csf('booking_no')];
					$roll_dlv_arr[$row[csf('sys_number')]]['booking_id']=$row[csf('booking_id')];
					$roll_dlv_arr[$row[csf('sys_number')]]['buyer_id']=$row[csf('buyer_id')];
				}
				unset($sql_dlv_result);
				$billForArr=array(3);
                $i=1;
				if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2) $bill_for_id="SM";
				//	if($ex_bill_for!=3)
				if(!in_array($ex_bill_for,$billForAr))
				{
					$po_breakdown_id_conds2="";
					if($job_id)
					{
						$po_breakdown_id_conds2= " and c.po_breakdown_id in ($po_ids)";
					}

					// echo $in_house_knit_bill_from.'D';die;
					// and c.is_sales=0 
				 if($in_house_knit_bill_from!=2)   //delevery_date
				 {
					$man_challan_cond="";  $sys_challan_cond="";
					if($challan_no!="") $man_challan_cond="and a.challan_no='$challan_no'";
					if($sys_challan_no!="") $sys_challan_cond=" and a.recv_number_prefix_num in ($sys_challan_no)";

					$po_breakdown_id_conds="";
					if($job_id)
					{
						$po_breakdown_id_conds= " and c.po_breakdown_id in ($po_ids)";
						$date_cond="";
					}
					//grey_receive_qnty_pcs
					if($ex_bill_for==4) //==========For FSO=====================
					{
						    $sql="(select a.id, a.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, c.po_breakdown_id, sum(c.quantity) as quantity, sum(c.quantity_pcs) as quantity_pcs, avg($knittingCharge) as rate, sum(b.amount) as amount, a.receive_basis, a.booking_no, a.booking_id,e.po_buyer,e.po_job_no,e.job_no as po_number,e.sales_booking_no, e.job_no, e.style_ref_no, e.job_no_prefix_num, c.is_sales from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,fabric_sales_order_mst e
						where a.id=b.mst_id and $booking_without_order=0 and b.id=c.dtls_id  and c.po_breakdown_id=e.id  and a.knitting_source=1 and a.company_id='$data[3]' and a.knitting_company='$data[0]' and a.location_id='$data[1]' and c.trans_type=1 and a.entry_form in(2,22) and c.entry_form in(2,22) and a.receive_basis in(1,2,4) and a.item_category=13   and c.trans_id!=0 and c.quantity>0
						and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $man_challan_cond $po_breakdown_id_conds $sys_challan_cond
						group by a.id, a.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id, a.receive_basis, a.booking_no, a.booking_id,e.po_buyer,e.po_job_no,e.job_no,e.sales_booking_no, e.job_no, e.style_ref_no, e.job_no_prefix_num, c.is_sales)"; 
						//echo  $sql;die;

						// $sql = "select a.id as id, a.recv_number, a.recv_number_prefix_num, a.challan_no, a.entry_form, a.receive_date, a.booking_id as prog_id, a.booking_no as prog_no, b.body_part_id, a.buyer_id, a.receive_basis, a.knitting_source, a.booking_without_order, sum(b.no_of_roll) as no_of_roll,b.febric_description_id, max(b.gsm) as gsm, max(b.width) as dia_width, b.prod_id, LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as dtls_id, c.po_breakdown_id, sum(c.quantity) as quantity,sum(c.quantity_pcs) as grey_receive_qnty_pcs, d.po_buyer,d.po_job_no,d.job_no as po_number,d.sales_booking_no, d.job_no, d.style_ref_no, d.job_no_prefix_num, c.is_sales, to_char(d.insert_date,'YYYY') as job_year, a.booking_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, fabric_sales_order_mst d where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id  and a.entry_form=2 and c.entry_form=2 and c.trans_id>0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.trans_type=1 and c.po_breakdown_id!=0 and c.is_sales=1 and a.location_id='$data[1]' and a.company_id='$data[0]' $date_cond $po_breakdown_id_conds  group by a.id, a.recv_number, a.recv_number_prefix_num, a.challan_no, a.entry_form, b.febric_description_id, a.receive_date, a.booking_id,a.booking_no, a.buyer_id, a.receive_basis, a.knitting_source, b.body_part_id,a.booking_without_order,b.prod_id,c.po_breakdown_id ,d.po_buyer,d.po_job_no,d.job_no,d.sales_booking_no,d.style_ref_no,d.job_no_prefix_num,c.is_sales,d.insert_date, a.booking_id order by d.style_ref_no,a.id ";
					}
					else
					{
						$sql="(select a.id, d.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, c.po_breakdown_id, sum(c.quantity) as quantity, sum(c.quantity_pcs) as quantity_pcs, avg($knittingCharge) as rate, sum(b.amount) as amount, d.receive_basis, d.booking_no, d.booking_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, inv_receive_master d
						where a.id=b.mst_id and $booking_without_order=0 and b.id=c.dtls_id and d.id=a.booking_id and a.knitting_source=1 and a.company_id='$data[3]' and a.knitting_company='$data[0]' and a.location_id='$data[1]' and c.trans_type=1 and a.entry_form=22 and c.entry_form=22 and a.receive_basis=9 and a.item_category=13 and d.entry_form=2 and c.trans_id!=0 and c.quantity>0
						and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $man_challan_cond $po_breakdown_id_conds $sys_challan_cond
						group by a.id, d.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id, d.receive_basis, d.booking_no, d.booking_id)
						union all
						(select a.id, a.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, c.po_breakdown_id, sum(c.quantity) as quantity,sum(c.quantity_pcs) as quantity_pcs, avg($knittingCharge) as rate, sum(b.amount) as amount, a.receive_basis, a.booking_no, a.booking_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c
					   where a.id=b.mst_id and $booking_without_order=0 and b.id=c.dtls_id and a.knitting_source=1 and a.company_id='$data[3]' and a.knitting_company='$data[0]' and a.location_id='$data[1]' and c.trans_type=1 and a.entry_form=2 and c.entry_form=2 and a.receive_basis in (1,2) and a.item_category=13 and c.trans_id!=0 and c.quantity>0
					   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $man_challan_cond $po_breakdown_id_conds $sys_challan_cond
					   group by a.id, a.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id, a.receive_basis, a.booking_no, a.booking_id) 
					   union all
					   (select a.id, a.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, count(b.no_of_roll) as roll_qty, c.po_breakdown_id, sum(d.qnty) as quantity,sum(c.quantity_pcs) as quantity_pcs, avg($knittingCharge) as rate, sum(b.amount) as amount, a.receive_basis, a.booking_no, 0 booking_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, pro_roll_details d
					   where a.id=b.mst_id and $booking_without_order=0 and b.id=c.dtls_id and b.id=d.dtls_id and a.id=d.mst_id  and d.qnty>0
					   
					   and a.knitting_source=1 and a.company_id='$data[3]' and a.knitting_company='$data[0]' and a.location_id='$data[1]' and c.trans_type=1 and a.entry_form =58 and d.entry_form=58 and c.entry_form=58 and a.receive_basis=10 and a.item_category=13 and c.trans_id!=0
					   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $man_challan_cond $po_breakdown_id_conds $sys_challan_cond
					   group by a.id, a.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id, a.receive_basis, a.booking_no)
					   union all
					   (select a.id, a.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, c.po_breakdown_id, sum(c.quantity) as quantity,sum(c.quantity_pcs) as quantity_pcs, avg($knittingCharge) as rate, sum(b.amount) as amount, a.receive_basis, a.booking_no, a.booking_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c
					   where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=1 and a.company_id='$data[3]' and a.knitting_company='$data[0]' and a.location_id='$data[1]' and c.trans_type=1 and a.entry_form=22 and c.entry_form=22 and a.item_category=13 and a.receive_basis in (2,4,11) and c.trans_id!=0 and c.quantity>0
					   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $man_challan_cond $po_breakdown_id_conds $sys_challan_cond
					   group by a.id, a.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id, a.receive_basis, a.booking_no, a.booking_id)
					   order by recv_number_prefix_num ASC";
					}
						
					}
					else // ***=====Subcon Variable-In House Knit Bill From===***
					{
						 
						$man_challan_cond="";  $sys_challan_cond="";
						if($challan_no!="") $man_challan_cond="and b.grey_sys_number like '%$challan_no%'";
						if($sys_challan_no!="") $sys_challan_cond=" and a.sys_number_prefix_num in ($sys_challan_no)";
	
						$po_breakdown_id_conds="";
						if($job_id)
						{
							$po_breakdown_id_conds= " and b.order_id in ($po_ids)";
							$date_cond2="";
						}	
						if($ex_bill_for==4) //==========For FSO=====================
						{
						//   $sql=" select a.id, a.entry_form, a.sys_number_prefix_num as recv_number_prefix_num, b.grey_sys_number as challan_no, a.delevery_date as receive_date, b.product_id as prod_id, 0 as body_part_id, b.determination_id as febric_description_id, sum(b.roll) as roll_qty, b.order_id as po_breakdown_id, sum(b.current_delivery) as quantity,sum(b.current_delivery_qnty_in_pcs) as qnty_pcs,sum(d.qc_pass_qnty_pcs) as quantity_pcs, 0 as rate, 0 as amount, c.receive_basis, c.booking_no, c.booking_id,b.size_coller_cuff, e.po_buyer,e.po_job_no,e.job_no as po_number,e.sales_booking_no, e.job_no, e.style_ref_no,e.job_no_prefix_num, d.is_sales from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b,inv_receive_master c,pro_roll_details d,fabric_sales_order_mst e where a.id=b.mst_id and b.grey_sys_id=c.id and c.id=d.mst_id and b.roll_id=d.id and b.order_id=e.id and d.po_breakdown_id=e.id and c.knitting_source=1 and c.company_id='$data[3]' and c.knitting_company='$data[0]' and c.entry_form=2  and c.knitting_source=1 and a.company_id='$data[3]' and c.knitting_company='$data[0]' and c.location_id='$data[1]'   and a.entry_form in(56,53) and c.entry_form=2 and c.receive_basis in (2,4,11) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond2 $man_challan_cond $po_breakdown_id_conds $sys_challan_cond group by a.id, a.entry_form, a.sys_number_prefix_num, c.receive_basis,b.grey_sys_number, a.delevery_date, b.product_id, b.determination_id, b.order_id, c.booking_no, c.booking_id,b.size_coller_cuff,e.po_buyer,e.po_job_no,e.job_no,e.sales_booking_no, e.job_no, e.style_ref_no,e.job_no_prefix_num, d.is_sales order by a.sys_number_prefix_num ASC";
						    //echo $sql;die;
							/*  $sql=" select a.id, a.entry_form, a.sys_number_prefix_num as recv_number_prefix_num, b.grey_sys_number as challan_no, a.delevery_date as receive_date, b.product_id as prod_id, 0 as body_part_id, b.determination_id as febric_description_id, sum(b.roll) as roll_qty, b.order_id as po_breakdown_id, sum(b.current_delivery) as quantity,sum(b.current_delivery_qnty_in_pcs) as qnty_pcs,sum(b.current_delivery_qnty_in_pcs) as quantity_pcs, 0 as rate, 0 as amount, c.receive_basis, c.booking_no, c.booking_id,b.size_coller_cuff, e.po_buyer,e.po_job_no,e.job_no as po_number,e.sales_booking_no, e.job_no, e.style_ref_no,e.job_no_prefix_num,1  as is_sales from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b,inv_receive_master c,fabric_sales_order_mst e where a.id=b.mst_id and b.grey_sys_id=c.id and b.order_id=e.id and c.knitting_source=1 and c.company_id='$data[3]' and c.knitting_company='$data[0]' and c.entry_form in(22,2)  and c.knitting_source=1 and a.company_id='$data[3]' and c.knitting_company='$data[0]' and c.location_id='$data[1]'  and a.entry_form in(56,53)  and c.receive_basis in (9,2,4,11) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond2 $man_challan_cond $po_breakdown_id_conds $sys_challan_cond group by a.id, a.entry_form, a.sys_number_prefix_num, c.receive_basis,b.grey_sys_number, a.delevery_date, b.product_id, b.determination_id, b.order_id, c.booking_no, c.booking_id,b.size_coller_cuff,e.po_buyer,e.po_job_no,e.job_no,e.sales_booking_no, e.job_no, e.style_ref_no,e.job_no_prefix_num order by a.sys_number_prefix_num ASC"; */
							 // echo "=A=".$sql;die;
							$sql=" select a.id, a.entry_form, a.sys_number_prefix_num as recv_number_prefix_num, b.grey_sys_number as challan_no, a.delevery_date as receive_date, b.product_id as prod_id, 0 as body_part_id, b.determination_id as febric_description_id, sum(b.roll) as roll_qty, b.order_id as po_breakdown_id, sum(b.current_delivery) as quantity,sum(b.current_delivery_qnty_in_pcs) as qnty_pcs,sum(b.grey_receive_qnty_pcs) as quantity_pcs, 0 as rate, 0 as amount, c.receive_basis, c.booking_no, c.booking_id,b.size_coller_cuff, e.po_buyer,e.po_job_no,e.job_no as po_number,e.sales_booking_no, e.job_no, e.style_ref_no,e.job_no_prefix_num,1  as is_sales, p.machine_dia as machine_prd_dia,p.yarn_count as yarn_prd_count from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b,inv_receive_master c left join pro_grey_prod_entry_dtls p on c.id=p.mst_id and p.status_active=1,fabric_sales_order_mst e where a.id=b.mst_id and b.grey_sys_id=c.id and b.order_id=e.id and c.knitting_source=1 and c.company_id='$data[3]' and c.knitting_company='$data[0]' and c.entry_form in(22,2)  and c.knitting_source=1 and a.company_id='$data[3]' and c.knitting_company='$data[0]' and c.location_id='$data[1]'  and a.entry_form in(56,53)  and c.receive_basis in (9,2,4,11) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond2 $man_challan_cond $po_breakdown_id_conds $sys_challan_cond group by a.id, a.entry_form, a.sys_number_prefix_num, c.receive_basis,b.grey_sys_number, a.delevery_date, b.product_id, b.determination_id, b.order_id, c.booking_no, c.booking_id,b.size_coller_cuff,e.po_buyer,e.po_job_no,e.job_no,e.sales_booking_no, e.job_no, e.style_ref_no,e.job_no_prefix_num, p.machine_dia,p.yarn_count order by a.sys_number_prefix_num ASC";
							 //echo "=A=".$sql;die;
							 $mc_prod_yarn_arr=array();
							 $sql_prod_data =sql_select($sql);
							foreach($sql_prod_data as $row) 
							{

								$mc_prod_yarn_arr[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]][$row[csf('booking_no')]][$row[csf('booking_id')]]['mc_prod_dia']=$row[csf('machine_prd_dia')];
								$mc_prod_yarn_arr[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]][$row[csf('booking_no')]][$row[csf('booking_id')]]['yarn_prod_count']=$row[csf('yarn_prd_count')];
							}
						}
						else
						{
							$sql=" select a.id, a.entry_form, a.sys_number_prefix_num as recv_number_prefix_num, b.grey_sys_number as challan_no, a.delevery_date as receive_date, b.product_id as prod_id, 0 as body_part_id, b.determination_id as febric_description_id, sum(b.roll) as roll_qty, b.order_id as po_breakdown_id, sum(b.current_delivery) as quantity,sum(b.current_delivery_qnty_in_pcs) as qnty_pcs,sum(d.qc_pass_qnty_pcs) as quantity_pcs, 0 as rate, 0 as amount, c.receive_basis, c.booking_no, c.booking_id,b.size_coller_cuff from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b,inv_receive_master c,pro_roll_details d where a.id=b.mst_id and b.grey_sys_id=c.id and c.id=d.mst_id and b.roll_id=d.id and c.knitting_source=1 and c.company_id='$data[3]' and c.knitting_company='$data[0]' and c.entry_form=2  and c.knitting_source=1 and a.company_id='$data[3]' and c.knitting_company='$data[0]' and c.location_id='$data[1]'   and a.entry_form in(56,53) and c.entry_form=2 and c.receive_basis in (2,4,11) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond2 $man_challan_cond $po_breakdown_id_conds $sys_challan_cond group by a.id, a.entry_form, a.sys_number_prefix_num, c.receive_basis,b.grey_sys_number, a.delevery_date, b.product_id, b.determination_id, b.order_id, c.booking_no, c.booking_id,b.size_coller_cuff order by a.sys_number_prefix_num ASC";//change for issue 
						}
							$sqlRecvData=sql_select("select  b.prod_id, b.body_part_id, b.machine_dia,b.yarn_count,b.febric_description_id, c.po_breakdown_id, a.booking_no, a.booking_id,b.coller_cuff_size from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and $booking_without_order=0 and b.id=c.dtls_id and a.knitting_source=1 and a.company_id='$data[3]' and a.knitting_company='$data[0]' and a.location_id='$data[1]' and c.trans_type=1 and a.entry_form=2 and c.entry_form=2 and a.receive_basis in (1,2) and a.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $man_challan_cond $po_breakdown_id_conds2 $sys_challan_cond group by b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id, a.booking_no, b.machine_dia,b.yarn_count,a.booking_id,b.coller_cuff_size");
						
							$recvInfo_arr=array();
							foreach($sqlRecvData as $row) 
							{
								$recvInfo_arr[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]][$row[csf('booking_no')]][$row[csf('booking_id')]][$row[csf('coller_cuff_size')]]['body_part_id']=$row[csf('body_part_id')];

								$mc_yarn_arr[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]][$row[csf('booking_no')]][$row[csf('booking_id')]]['mc_dia']=$row[csf('machine_dia')];
								$mc_yarn_arr[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]][$row[csf('booking_no')]][$row[csf('booking_id')]]['yarn_count']=$row[csf('yarn_count')];
							}
							unset($sqlRecvData);
						  }
						  /*  echo '<pre>';print_r($mc_prod_yarn_arr);die;   */

						
					
				//	echo $sql;
					$sql_result=sql_select($sql);
					foreach($sql_result as $row) // for update row
					{
						$prod_idArr[$row[csf('prod_id')]]=$row[csf('prod_id')];
						$po_idArr[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];
						$job_idArr[$row[csf('po_job_no')]]=$row[csf('po_job_no')];
						if ($row[csf('receive_basis')]==2)
						{
							$prog_idArr[$row[csf('booking_no')]]=$row[csf('booking_no')];
						}
					}
					$prodId_cond=where_con_using_array($prod_idArr,0,'id');
					$poId_cond=where_con_using_array($po_idArr,0,'b.po_break_down_id');
					$poId_cond2=where_con_using_array($po_idArr,0,'b.id');
					$progId_cond=where_con_using_array($prog_idArr,0,'b.id');
					$jobId_cond=where_con_using_array($job_idArr,1,'b.job_no');
					$product_dtls_arr=return_library_array( "select id, product_name_details from  product_details_master where status_active=1 $prodId_cond",'id','product_name_details');
					//$currency_arr=return_library_array( "select b.id, a.currency_id from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst",'id','currency_id');
					
					if($ex_bill_for==1 || $ex_bill_for==2)
					{
						$color_type_array=array();
						$color_type_sql="select a.color_type_id, a.lib_yarn_count_deter_id, b.po_break_down_id from  wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id='$data[3]' $poId_cond ";
						$color_type_sql_result =sql_select($color_type_sql);
						foreach($color_type_sql_result as $row)
						{
							$color_type_array[$row[csf('po_break_down_id')]][$row[csf('lib_yarn_count_deter_id')]]['color_type']=$row[csf('color_type_id')];
						}
						unset($color_type_sql_result);
						
						$job_order_arr=array();
						$sql_job="Select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active!=0 and a.is_deleted=0 and b.status_active!=0 and b.is_deleted=0 $poId_cond2";
						$sql_job_result =sql_select($sql_job);
						foreach($sql_job_result as $row)
						{
							$job_order_arr[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
							$job_order_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
							$job_order_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
							$job_order_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
							$job_order_arr[$row[csf('id')]]['grouping']=$row[csf('grouping')];
						}
						unset($sql_job_result);
					}
					if($ex_bill_for==4)
					{
						$rate_array=array();
						$rate_sql="select a.body_part_id,a.lib_yarn_count_deter_id,b.charge_unit from wo_pre_cost_fabric_cost_dtls a,wo_pre_cost_fab_conv_cost_dtls b where a.id=b.fabric_description  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.cons_process=1 $jobId_cond ";
						$rate_sql_result =sql_select($rate_sql);
						foreach($rate_sql_result as $row)
						{
							$rate_array[$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['rate']=$row[csf('charge_unit')];
						}
						unset($rate_sql_result);
					}
					//echo '<pre>';print_r($rate_array);
					$plan_booking_arr=array();
				 	$knit_booking="select b.id,b.machine_dia, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.status_active=1 and b.is_deleted=0 $progId_cond";
					$knit_booking_result =sql_select($knit_booking);
					foreach($knit_booking_result as $row)
					{
						$plan_booking_arr[$row[csf('id')]]=$row[csf('booking_no')];
						$plan_mcDia_arr[$row[csf('id')]]['mc_dia']=$row[csf('machine_dia')];
					}
					unset($knit_booking_result);	
				
				 	 //	echo $sql.'DDS'; 
					foreach($sql_result as $row) // for update row
					{
						if($in_house_knit_bill_from!=2)   //delevery_date
				 		{
				 			$row[csf('body_part_id')]=$row[csf('body_part_id')];
							
						}
						else
						{
							$row[csf('body_part_id')]=$recvInfo_arr[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]][$row[csf('booking_no')]][$row[csf('booking_id')]][$row[csf('size_coller_cuff')]]['body_part_id'];

							//echo $row[csf('body_part_id')]."abcc"."<br/>";
						}
						$is_sales = $row[csf('is_sales')];

						 if($row[csf('challan_no')]=='') $row[csf('challan_no')]=0; if($row[csf('size_coller_cuff')]=='') $row[csf('size_coller_cuff')]=0;
				      	// if($row[csf('rec_challan_no')]=='') $row[csf('rec_challan_no')]=0;
						$all_value=$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$row[csf('prod_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')].'_'.$row[csf('challan_no')].'_'.$row[csf('size_coller_cuff')];
						$all_value2=$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$row[csf('prod_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')].'_'.$row[csf('challan_no')].'_'.$row[csf('size_coller_cuff')].'_'.$row[csf('booking_no')];
						//echo $all_value.'<br>';
						//echo "<pre>";
						//print_r($str_arr);
						if(in_array($all_value,$str_arr))
						{
							//echo $all_value.'='.$str_arr.'<br>';
							$booking_no=0; $independent=''; $bill_for_id=0; $bill_for_sb=0;
							if ($row[csf('entry_form')]==2)
							{
								if($ex_bill_for==1) { if($row[csf('receive_basis')]==0) $independent=4; } //else $independent='';
								if ($row[csf('receive_basis')]==2) $booking_no=$plan_booking_arr[$row[csf('booking_no')]]; else if ($row[csf('receive_basis')]==1) $booking_no=$row[csf('booking_no')]; else $booking_no=0;
								if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2 && $row[csf('receive_basis')]!=0) $bill_for_id="SM";
							}
							else if ($row[csf('entry_form')]==22)
							{
								if($ex_bill_for==1) { if($row[csf('receive_basis')]==4) $independent=4; }// else $independent='';
								if($row[csf('receive_basis')]==2 || $row[csf('receive_basis')]==11) $booking_no=$row[csf('booking_no')]; else $booking_no=0;
								if($ex_bill_for==1) { $bill_for_id="Fb"; $bill_for_sb="SB"; } else if($ex_bill_for==2 && $row[csf('receive_basis')]!=4) $bill_for_id="SM"; 
							}
							else if ($row[csf('entry_form')]==58 || $row[csf('entry_form')]==56)
							{
								$rec_basis=0; $bookinNo=""; $bookingId=0;// booking id is barcode, and booking no is delv id

								/*$rec_basis=$roll_dlv_arr[$row[csf('booking_no')]][$row[csf('booking_id')]]['receive_basis'];
								$bookinNo=$roll_dlv_arr[$row[csf('booking_no')]][$row[csf('booking_id')]]['booking_no'];
								$bookingId=$roll_dlv_arr[$row[csf('booking_no')]][$row[csf('booking_id')]]['booking_id'];*/

								$rec_basis=$roll_dlv_arr[$row[csf('booking_no')]]['receive_basis'];
								$bookinNo=$roll_dlv_arr[$row[csf('booking_no')]]['booking_no'];
								$bookingId=$roll_dlv_arr[$row[csf('booking_no')]]['booking_id'];
								//echo $bookinNo.'=';
								
								if($ex_bill_for==1) { if($rec_basis==0) $independent=4; } //else $independent='';
								if ($rec_basis==2) $booking_no=$plan_booking_arr[$bookinNo]; 
								else if ($rec_basis==1) $booking_no=$bookinNo; else $booking_no=0;
								 
								if($ex_bill_for==1 || $ex_bill_for==4) { $bill_for_id="Fb"; $booking_no=$row[csf('sales_booking_no')];}
								else if($ex_bill_for==2 && $rec_basis!=0) $bill_for_id="SM";
							}
							else if ($row[csf('entry_form')]==53)
							{
								if($ex_bill_for==1) { if($row[csf('receive_basis')]==0) $independent=4; } //else $independent='';
								if ($row[csf('receive_basis')]==2) $booking_no=$plan_booking_arr[$row[csf('booking_no')]]; else if ($row[csf('receive_basis')]==1) $booking_no=$row[csf('booking_no')]; else $booking_no=0;
								if($ex_bill_for==1) $bill_for_id="Fb"; 
								else if($ex_bill_for==2 && $row[csf('receive_basis')]!=0) $bill_for_id="SM";
							}
							 if (($row[csf('entry_form')] == 53 || $row[csf('entry_form')] ==56) && $is_sales == 1) //===========FSO=============
							{
								$booking_no = $row[csf('sales_booking_no')];
								if ($ex_bill_for == 4) $bill_for_id = "Fb";
								else if ($ex_bill_for == 2 && $row[csf('receive_basis')] != 0) $bill_for_id = "SM";
							} 
							if($ex_bill_for==4) //==========For FSO=====================
							{
								$mc_dia=$mc_prod_yarn_arr[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]][$row[csf('booking_no')]][$row[csf('booking_id')]]['mc_prod_dia'];
								$yarn_count_str=$mc_prod_yarn_arr[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]][$row[csf('booking_no')]][$row[csf('booking_id')]]['yarn_prod_count'];
								/* $yarn_countArr=array_unique(explode(",",$yarn_count_str));
								foreach($yarn_countArr as $yid)
								{
									if($yarncountArr[$yid]!="")
									{
										$yarncount_Arr[$yid]=$yarncountArr[$yid];
									}
								} */
								$yarn_count=$yarncountArr[$yarn_count_str];

							}else{
								$mc_dia=$mc_yarn_arr[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]][$row[csf('booking_no')]][$row[csf('booking_id')]]['mc_dia'];
								$yarn_count_str=$mc_yarn_arr[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]][$row[csf('booking_no')]][$row[csf('booking_id')]]['yarn_count'];
								$yarn_countArr=array_unique(explode(",",$yarn_count_str));
								foreach($yarn_countArr as $yid)
								{
									if($yarncountArr[$yid]!="")
									{
										$yarncount_Arr[$yid]=$yarncountArr[$yid];
									}
								}
								$yarn_count=implode(", ",$yarncount_Arr);
							}
								
						
							
							//echo $booking_no.'='.$row[csf('entry_form')].'='.$is_sales.'SSSASA';
							
							$ex_booking="";
							if($booking_no!='') $ex_booking=explode('-',$booking_no); else $ex_booking="_";
							//echo $row[csf('quantity_pcs')].'='.$independent.', ';die;
							if($row[csf('roll_qty')]=='') $row[csf('roll_qty')]=0;
							if($row[csf('body_part_id')]=='') $row[csf('body_part_id')]=0;
							if($body_part[$row[csf("body_part_id")]]=='') $body_part[$row[csf("body_part_id")]]=0;
							if($bodyPartTypeArr[$row[csf('body_part_id')]]=='') $bodyPartTypeArr[$row[csf('body_part_id')]]=0;
							if($row[csf("quantity_pcs")]=='') $row[csf("quantity_pcs")]=0;
							if ($is_sales == 1) //========If Sales ============
							{
								$po_number = $row[csf('po_number')];
								$style_ref_no = $row[csf('style_ref_no')];
								$buyerId = $row[csf('po_buyer')];
								$job_no = $row[csf('po_job_no')];
								$rate= $rate_array[$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['rate'];
								$int_ref_no="";
							} else {
								$po_number = $job_order_arr[$row[csf('po_breakdown_id')]]['po'];
								$style_ref_no = $job_order_arr[$row[csf('po_breakdown_id')]]['style'];
								$buyerId = $job_order_arr[$row[csf('po_breakdown_id')]]['buyer'];
								$job_no = $job_order_arr[$row[csf('po_breakdown_id')]]['job_no'];
								$int_ref_no=$job_order_arr[$row[csf('po_breakdown_id')]]['grouping'];
								$rate = $row[csf('rate')];
								if($int_ref_no) $int_ref_no=$int_ref_no;else $int_ref_no=0;
							}

						
							$str_val=$row[csf('id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$po_number.'_'.$style_ref_no.'_'.$buyer_arr[$buyerId].'_'.$row[csf('roll_qty')].'_'.$row[csf('febric_description_id')].'_'.$composition_arr[$determ_arr[$row[csf('febric_description_id')]]].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('prod_id')].'_'.$product_dtls_arr[$row[csf('prod_id')]].'_'.$row[csf('quantity')].'_'.$rate.'_'.$row[csf('amount')].'_0___'.$row[csf('size_coller_cuff')].'_1_'.$bodyPartTypeArr[$row[csf('body_part_id')]].'_'.$row[csf('quantity_pcs')].'_'.$row[csf('challan_no')].'_'.$row[csf('booking_no')].'_'.$int_ref_no.'_'.$is_sales.'_'.$yarn_count.'_'.$mc_dia;
							//echo $str_val.'Up';//$row[csf('rate')]
							if($independent==4)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr id="tr_<? echo $all_value2; ?>" bgcolor="yellow" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value2; ?>','<? echo $i; ?>');" >
									<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="1" checked ></td>
									
									<td width="30"><? echo $i; ?></td>
									<td width="60"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
									<td width="70" style="word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
									<td width="60"><? echo change_date_format($row[csf('receive_date')]); ?></td>
									<td width="80"><? echo $job_no; ?></td>
									<td width="100" style="word-break:break-all"><? echo $style_ref_no; ?></td>
									<td width="120" style="word-break:break-all"><? echo $po_number; ?></td>
									<td width="90" style="word-break:break-all"><? echo $body_part[$row[csf("body_part_id")]]; ?></td>
									<td width="160" style="word-break:break-all"><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></td>
									<td width="70" style="word-break:break-all"><?  echo $yarn_count; ?></td>
									<td width="70" style="word-break:break-all"><? echo $mc_dia; ?></td>
									<td width="60" style="word-break:break-all"><? echo $color_type[$color_type_array[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['color_type']]; ?></td>
									<td width="80" align="right"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
                                    <td width="80" align="right"><? echo number_format($row[csf('quantity_pcs')],2,'.',''); ?></td>
									<td align="center"><? echo $row[csf('roll_qty')]; ?>
									
									<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
									
									<input type="hidden" id="currid<? echo $all_value2; ?>" value="<? echo '1'; ?>">
									<input type="hidden" id="unique_id<? echo $i; ?>" value="<? echo $all_value2; ?>"></td>
								</tr>
								<?php
								$i++;
							}
							else
							{
								//echo strtolower($ex_booking[1]).'='.strtolower($bill_for_id).'='.strtolower($bill_for_sb);

								if( strtolower($ex_booking[1])==strtolower($bill_for_id) || strtolower($ex_booking[1])==strtolower($bill_for_sb)) 
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr id="tr_<? echo $all_value2; ?>" bgcolor="yellow" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value2; ?>','<? echo $i; ?>');" >
										<td width="30" align="center"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="1" checked ></td>
										<td width="30"><? echo $i; ?></td>
										<td width="60"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
										<td width="70" style="word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
										<td width="60"><? echo change_date_format($row[csf('receive_date')]); ?></td>
										<td width="80"><? echo $job_no; ?></td>
										<td width="100" style="word-break:break-all"><? echo $style_ref_no; ?></td>
										<td width="120" style="word-break:break-all"><? echo $po_number; ?></td>
										<td width="90" style="word-break:break-all"><? echo $body_part[$row[csf("body_part_id")]]; ?></td>
										<td width="160" style="word-break:break-all"><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></td>
										<td width="70" style="word-break:break-all"><?  echo $yarn_count; ?></td>
										<td width="70" style="word-break:break-all"><? echo $mc_dia; ?></td>

										<td width="60" style="word-break:break-all"><? echo $color_type[$color_type_array[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['color_type']]; ?></td>
										<td width="80" align="right"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
                                         <td width="80" align="right"><? echo number_format($row[csf('quantity_pcs')],2,'.',''); ?></td>
										<td align="center"><? echo $row[csf('roll_qty')]; ?>
										
										<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">									
										<input type="hidden" id="currid<? echo $all_value2; ?>" value="<? echo '1'; ?>">
										<input type="hidden" id="unique_id<? echo $i; ?>" value="<? echo $all_value2; ?>"></td>
									</tr>
									<?php
									$i++;
								}
							}
						}
					}
				
					foreach($sql_result as $row) // for new row
					{

						if($in_house_knit_bill_from!=2)   //delevery_date
				 		{
				 			$row[csf('body_part_id')]=$row[csf('body_part_id')];
							
						}
						else
						{
							$row[csf('body_part_id')]=$recvInfo_arr[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]][$row[csf('booking_no')]][$row[csf('booking_id')]][$row[csf('size_coller_cuff')]]['body_part_id'];

							//echo $row[csf('body_part_id')]."abcc"."<br/>";
						}
						$is_sales = $row[csf('is_sales')];
						 
						if($ex_bill_for==4) //==========For FSO=====================
							{
								$mc_dia=$mc_prod_yarn_arr[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]][$row[csf('booking_no')]][$row[csf('booking_id')]]['mc_prod_dia'];
								$yarn_count_str=$mc_prod_yarn_arr[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]][$row[csf('booking_no')]][$row[csf('booking_id')]]['yarn_prod_count'];
								/* $yarn_countArr=array_unique(explode(",",$yarn_count_str));
								foreach($yarn_countArr as $yid)
								{
									if($yarncountArr[$yid]!="")
									{
										$yarncount_Arr[$yid]=$yarncountArr[$yid];
									}
								} */
								$yarn_count=$yarncountArr[$yarn_count_str];

							}else{
								$mc_dia=$mc_yarn_arr[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]][$row[csf('booking_no')]][$row[csf('booking_id')]]['mc_dia'];
								$yarn_count_str=$mc_yarn_arr[$row[csf('prod_id')]][$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]][$row[csf('booking_no')]][$row[csf('booking_id')]]['yarn_count'];
								$yarn_countArr=array_unique(explode(",",$yarn_count_str));
								foreach($yarn_countArr as $yid)
								{
									if($yarncountArr[$yid]!="")
									{
										$yarncount_Arr[$yid]=$yarncountArr[$yid];
									}
								}
								$yarn_count=implode(", ",$yarncount_Arr);
							}
							
							
						//echo $mc_dia.'='.$yarn_count.'<br>';
						//print_r($yarn_count_id);
						//echo $mc_dia.'='.$yarn_count_id.'<br>';
						

						

						$booking_no=0; $independent=''; $bill_for_id=0; $bill_for_sb=0;
						if ($row[csf('entry_form')]==2)
						{
							if($ex_bill_for==1) { if($row[csf('receive_basis')]==0) $independent=4; } //else $independent='';
							if ($row[csf('receive_basis')]==2) $booking_no=$plan_booking_arr[$row[csf('booking_no')]]; else if ($row[csf('receive_basis')]==1) $booking_no=$row[csf('booking_no')]; else $booking_no=0;
							if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2 && $row[csf('receive_basis')]!=0) $bill_for_id="SM";
						}
						else if ($row[csf('entry_form')]==22)
						{
							if($ex_bill_for==1) { if($row[csf('receive_basis')]==4) $independent=4; }// else $independent='';
							if($row[csf('receive_basis')]==2 || $row[csf('receive_basis')]==11) $booking_no=$row[csf('booking_no')]; else $booking_no=0;
							if($ex_bill_for==1) { $bill_for_id="Fb"; $bill_for_sb="SB"; } else if($ex_bill_for==2 && $row[csf('receive_basis')]!=4) $bill_for_id="SM"; 
						}
						else if ($row[csf('entry_form')]==58 || $row[csf('entry_form')]==56)
						{
							$rec_basis=0; $bookinNo=""; $bookingId=0;// booking id is barcode, and booking no is delv id
							/*$rec_basis=$roll_dlv_arr[$row[csf('booking_no')]][$row[csf('booking_id')]]['receive_basis'];
							$bookinNo=$roll_dlv_arr[$row[csf('booking_no')]][$row[csf('booking_id')]]['booking_no'];
							$bookingId=$roll_dlv_arr[$row[csf('booking_no')]][$row[csf('booking_id')]]['booking_id'];*/

							$rec_basis=$roll_dlv_arr[$row[csf('booking_no')]]['receive_basis'];
							$bookinNo=$roll_dlv_arr[$row[csf('booking_no')]]['booking_no'];
							$bookingId=$roll_dlv_arr[$row[csf('booking_no')]]['booking_id'];
							//echo $is_sales.'='.$rec_basis.'<br>';
							if($ex_bill_for==1) { if($rec_basis==0) $independent=4; } //else $independent='';
							if ($rec_basis==2) $booking_no=$plan_booking_arr[$bookinNo]; 
							else if ($rec_basis==1) $booking_no=$bookinNo; else $booking_no=0;
							if($ex_bill_for==1 || $ex_bill_for==4) { $bill_for_id="Fb"; $booking_no=$row[csf('sales_booking_no')];}
							else if($ex_bill_for==2 && $rec_basis!=0) $bill_for_id="SM";
						}
						else if ($row[csf('entry_form')]==53 && $is_sales != 1)
						{
							if($ex_bill_for==1) { if($row[csf('receive_basis')]==0) $independent=4; } //else $independent='';
							if ($row[csf('receive_basis')]==2) $booking_no=$plan_booking_arr[$row[csf('booking_no')]]; else if ($row[csf('receive_basis')]==1) $booking_no=$row[csf('booking_no')]; else $booking_no=0;
							if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2 && $row[csf('receive_basis')]!=0) $bill_for_id="SM";
						}
						else if ($row[csf('entry_form')]==53 && $is_sales == 1)
						{
							$booking_no = $row[csf('sales_booking_no')];
							if($ex_bill_for==1) { if($row[csf('receive_basis')]==0) $independent=4; } //else $independent='';
							if ($row[csf('receive_basis')]==2) $booking_no=$plan_booking_arr[$row[csf('booking_no')]];
							 else if ($row[csf('receive_basis')]==1) $booking_no=$row[csf('booking_no')]; else $booking_no=0;
							if($ex_bill_for==1 || $ex_bill_for==4) $bill_for_id="Fb";
							 else if($ex_bill_for==2 && $row[csf('receive_basis')]!=0) $bill_for_id="SM";
							 // echo $row[csf('sales_booking_no')].'='.$booking_no.'='.$row[csf('entry_form')].'='.$is_sales;
						}
						 if (($row[csf('entry_form')] == 2 || $row[csf('entry_form')] ==22) && $is_sales == 1) //===========FSO=============
						{
							$booking_no = $row[csf('sales_booking_no')];
							if ($ex_bill_for == 4) $bill_for_id = "Fb";
							else if ($ex_bill_for == 2 && $row[csf('receive_basis')] != 0) $bill_for_id = "SM";
							
						} 

						$ex_booking="";
						if($booking_no!='') $ex_booking=explode('-',$booking_no); else $ex_booking="_";
						 if($row[csf('challan_no')]=='') $row[csf('challan_no')]=0;
						 if($row[csf('size_coller_cuff')]=='') $row[csf('size_coller_cuff')]=0;
						
						//if($ex_booking[1]!='Fb') echo $ex_booking[1];
						$bill_qty=$bill_qty_array[$row[csf('recv_number_prefix_num')]][$row[csf('challan_no')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]][$row[csf('size_coller_cuff')]]['qty'];
						
						$avilable_qty=$row[csf('quantity')]-$bill_qty;
						$avilable_roll=$roll_no_arr[$row[csf('dtls_id')]]-$roll_qty;
						// 	if($row[csf('challan_no')]=='') $row[csf('challan_no')]=0;
					//	echo $row[csf('quantity')].'='.$bill_qty.'<br>';
						if($row[csf("size_coller_cuff")]=='') $row[csf("size_coller_cuff")]=0;
						$all_value=$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$row[csf('prod_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')].'_'.$row[csf('challan_no')].'_'.$row[csf('size_coller_cuff')];
						$all_value2=$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$row[csf('prod_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')].'_'.$row[csf('challan_no')];
						 // echo $all_value.'<br>';
						if($row[csf('roll_qty')]=='') $row[csf('roll_qty')]=0;
						if($row[csf('body_part_id')]=='') $row[csf('body_part_id')]=0;
						if($body_part[$row[csf("body_part_id")]]=='') $body_part[$row[csf("body_part_id")]]=0;
						if($bodyPartTypeArr[$row[csf('body_part_id')]]=='') $bodyPartTypeArr[$row[csf('body_part_id')]]=0;
						if($row[csf("quantity_pcs")]=='') $row[csf("quantity_pcs")]=0;

						if ($is_sales == 1) //========If Sales ============
						{
							$po_number = $row[csf('po_number')];
							$style_ref_no = $row[csf('style_ref_no')];
							$buyerId = $row[csf('po_buyer')];
							$job_no = $row[csf('po_job_no')];
							$rate= $rate_array[$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['rate'];
							$int_ref_no="";
						} else {
							$po_number = $job_order_arr[$row[csf('po_breakdown_id')]]['po'];
							$style_ref_no = $job_order_arr[$row[csf('po_breakdown_id')]]['style'];
							$buyerId = $job_order_arr[$row[csf('po_breakdown_id')]]['buyer'];
							$job_no = $job_order_arr[$row[csf('po_breakdown_id')]]['job_no'];
							$int_ref_no=$job_order_arr[$row[csf('po_breakdown_id')]]['grouping'];
							$rate= $row[csf('rate')];
							if($int_ref_no) $int_ref_no=$int_ref_no;else $int_ref_no='0';
						}
						// echo $job_no.'='.$po_number.'='.$style_ref_no.'='.$buyerId.'<br>';

						//echo $avilable_qty.'<br>';die;
							
						$str_val=$row[csf('id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$po_number.'_'.$style_ref_no.'_'.$buyer_arr[$buyerId].'_'.$row[csf('roll_qty')].'_'.$row[csf('febric_description_id')].'_'.$composition_arr[$determ_arr[$row[csf('febric_description_id')]]].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('prod_id')].'_'.$product_dtls_arr[$row[csf('prod_id')]].'_'.$row[csf('quantity')].'_'.$rate.'_'.$row[csf('amount')].'_0___'.$row[csf('size_coller_cuff')].'_1_'.$bodyPartTypeArr[$row[csf('body_part_id')]].'_'.$row[csf('quantity_pcs')].'_'.$row[csf('challan_no')].'_'.$row[csf('booking_no')].'_'.$int_ref_no. '_' . $is_sales. '_' . $yarn_count. '_' . $mc_dia;
						//echo $str_val.'<br>';die;//$row[csf('rate')]
						if($independent==4)
						{
							if($avilable_qty>0)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr id="tr_<? echo $all_value2; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value2; ?>','<? echo $i; ?>');" >
									<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="2" ></td>
									<td width="30"><? echo $i; ?></td>
									<td width="60"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
									<td width="70" style="word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
									<td width="60"><? echo change_date_format($row[csf('receive_date')]); ?></td>
									<td width="60"><? echo $job_no; ?></td>
									<td width="100" style="word-break:break-all"><? echo $style_ref_no; ?></td>
									<td width="100" style="word-break:break-all"><? echo $po_number; ?></td>
									<td width="90" style="word-break:break-all"><? echo $body_part[$row[csf("body_part_id")]]; ?></td>
									
									<td width="160" style="word-break:break-all"><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></td>
									<td width="70" style="word-break:break-all"><?  echo $yarn_count; ?></td>
									<td width="70" style="word-break:break-all"><? echo $mc_dia; ?></td>
									<td width="60" style="word-break:break-all"><? echo $color_type[$color_type_array[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['color_type']]; ?></td>
									<td width="80" align="right"><? echo number_format($avilable_qty,2,'.',''); ?></td>
                                      <td width="80" align="right"><? echo number_format($row[csf('quantity_pcs')],2,'.',''); ?></td>
									<td align="center"><? echo $row[csf('roll_qty')]; ?>
									
									<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
								
									<input type="hidden" id="currid<? echo $all_value2; ?>" value="<? echo '1'; ?>">
									<input type="hidden" id="unique_id<? echo $i; ?>" value="<? echo $all_value2; ?>"></td>
								</tr>
								<?php
								$i++;
							}
						}
						else
						{
							
							//  echo $ex_booking[1].'='.$bill_for_id.'=X='.$ex_booking[1].'='.$bill_for_sb.'<br>';
							if( strtolower($ex_booking[1])==strtolower($bill_for_id) || strtolower($ex_booking[1])==strtolower($bill_for_sb)) 
							{
								 
								if(number_format($avilable_qty,2,'.','')>0)
								{
									 //echo $all_value."_".$i.'=';
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr id="tr_<? echo $all_value2; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value2; ?>','<? echo $i; ?>');" >
										<td width="30" align="center"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="2" ></td>
										<td width="30"><? echo $i; ?></td>
										<td width="60"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
										<td width="70" style="word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
										<td width="60"><? echo change_date_format($row[csf('receive_date')]); ?></td>
										<td width="80"><? echo $job_no; ?></td>
										<td width="100" style="word-break:break-all"><? echo $style_ref_no; ?></td>
										<td width="120" style="word-break:break-all"><? echo $po_number; ?></td>
										<td width="90" style="word-break:break-all"><? echo $body_part[$row[csf("body_part_id")]]; ?></td>
										<td width="160" style="word-break:break-all"><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></td>
										<td width="70" style="word-break:break-all"><?  echo $yarn_count; ?></td>
										<td width="70" style="word-break:break-all"><? echo $mc_dia; ?></td>
										<td width="60" style="word-break:break-all"><? echo $color_type[$color_type_array[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['color_type']]; ?></td>
										<td width="80" align="right"><? echo number_format($avilable_qty,2,'.',''); ?></td>
                                         <td width="80" align="right"><? echo number_format($row[csf('quantity_pcs')],2,'.',''); ?></td>
										<td align="center"><? echo $row[csf('roll_qty')]; ?>
										
										<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">									
										<input type="hidden" id="currid<? echo $all_value2; ?>" value="<? echo '1'; ?>">
										<input type="hidden" id="unique_id<? echo $i; ?>" value="<? echo $all_value2; ?>"></td>
									</tr>
									<?php
									$i++;
								}
							}
						}
					}
				}
				else if($ex_bill_for==3)// sample without order
				{
					$sys_challan_cond="";
					//echo $in_house_knit_bill_from;die;
					if($in_house_knit_bill_from==0) $in_house_knit_bill_from=2;
				 if($in_house_knit_bill_from!=2)   //delevery_date 
				 {
					if($sys_challan_no!="") $sys_challan_cond=" and a.recv_number_prefix_num in ($sys_challan_no)";
				 $sql="(select a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, sum(b.grey_receive_qnty) as quantity,sum(b.grey_receive_qnty_pcs) as quantity_pcs, c.entry_form, c.receive_basis, c.booking_no, c.booking_id, 0 as order_id 
					from inv_receive_master a, pro_grey_prod_entry_dtls b, inv_receive_master c 
					where a.id=b.mst_id and c.id=a.booking_id and a.booking_without_order=1 and a.knitting_source=1 and a.company_id='$data[3]' and a.knitting_company='$data[0]' and a.location_id='$data[1]' and a.entry_form=22 and a.receive_basis=9 and a.item_category=13 and c.entry_form=2 and c.receive_basis in (0,1,2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.trans_id!=0 $date_cond $sys_challan_cond
					group by a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.entry_form, c.receive_basis, c.booking_no, c.booking_id)
					union all
					(select a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, sum(b.grey_receive_qnty) as quantity,sum(b.grey_receive_qnty_pcs) as quantity_pcs, a.entry_form, a.receive_basis, a.booking_no, a.booking_id, 0 as order_id 
					from inv_receive_master a, pro_grey_prod_entry_dtls b
					where a.id=b.mst_id and a.booking_without_order=1 and a.knitting_source=1 and a.company_id='$data[3]' and b.trans_id!=0 and a.knitting_company='$data[0]' and a.location_id='$data[1]' and a.entry_form=2 and a.item_category=13 and a.receive_basis in (0,1,2)
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $sys_challan_cond
					group by a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, a.entry_form, a.receive_basis, a.booking_no, a.booking_id)
					union all
					(select a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, sum(f.qnty) as quantity,sum(b.grey_receive_qnty_pcs) as quantity_pcs, a.entry_form, a.receive_basis, a.booking_no, a.booking_id, 0 as order_id 
					from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details f
					where a.id=b.mst_id  and b.id=f.dtls_id and a.id=f.mst_id 
					and a.knitting_source=1 and a.knitting_source=1 and a.company_id='$data[3]' and a.knitting_company='$data[0]' and b.trans_id!=0 and a.location_id='$data[1]' and a.entry_form =58 and a.receive_basis=10 and a.item_category=13
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $sys_challan_cond
					group by a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, a.entry_form, a.receive_basis, a.booking_no, a.booking_id)
					union all
					(select a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, sum(b.grey_receive_qnty) as quantity,sum(b.grey_receive_qnty_pcs) as quantity_pcs, a.entry_form, a.receive_basis, a.booking_no, a.booking_id, 0 as order_id 
					from inv_receive_master a, pro_grey_prod_entry_dtls b
					where a.id=b.mst_id and a.knitting_source=1 and a.company_id='$data[3]' and a.knitting_company='$data[0]' and b.trans_id!=0 and a.location_id='$data[1]' and a.entry_form=22 and a.item_category=13 and a.receive_basis in (2,4,11)
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $sys_challan_cond
					group by a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, a.entry_form, a.receive_basis, a.booking_no, a.booking_id)
					order by recv_number_prefix_num ASC";
				 }
				 else
				 {
					 	$man_challan_cond="";  $sys_challan_cond="";
						if($challan_no!="") $man_challan_cond="and b.grey_sys_number like '%$challan_no%'";
						if($sys_challan_no!="") $sys_challan_cond=" and a.sys_number_prefix_num in ($sys_challan_no)";
	
						$po_breakdown_id_conds="";
						if($job_id)
						{
							$po_breakdown_id_conds= " and b.order_id in ($po_ids)";
							$date_cond2="";
						}	
						  $sql=" select a.id, a.entry_form, a.sys_number_prefix_num as recv_number_prefix_num, b.grey_sys_number as challan_no, a.delevery_date as receive_date, b.product_id as prod_id, 0 as body_part_id, b.determination_id as febric_description_id, sum(b.roll) as roll_qty, 0 as order_id, sum(b.current_delivery) as quantity,sum(b.current_delivery) as quantity_pcs, 0 as rate, 0 as amount, c.receive_basis, c.buyer_id,0 as booking_no, 0 as booking_id from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b,inv_receive_master c
						where a.id=b.mst_id and b.grey_sys_id=c.id and c.knitting_source=1 and c.company_id='$data[3]' and c.knitting_company='$data[0]' and c.entry_form=2  and c.knitting_source=1 and a.company_id='$data[3]' and c.knitting_company='$data[0]' and c.location_id='$data[1]'   and a.entry_form in(56,53) and c.entry_form=2 and c.receive_basis in (1,2,4,11) 
						and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond2 $man_challan_cond $po_breakdown_id_conds $sys_challan_cond
						group by a.id, a.entry_form, a.sys_number_prefix_num, c.receive_basis,b.grey_sys_number, a.delevery_date, b.product_id, b.determination_id,c.buyer_id
						order by a.sys_number_prefix_num ASC";	
				 }
					
					 // echo $sql; die;
					$sql_result =sql_select($sql);
					foreach($sql_result as $row) // for update row
					{
						//$row[csf('order_id')]='';
						if($row[csf('challan_no')]=='') $row[csf('challan_no')]=0;
						if($row[csf('order_id')]=='') $row[csf('order_id')]=0;
						
						$all_value=$row[csf('recv_number_prefix_num')].'_'.$row[csf('order_id')].'_'.$row[csf('prod_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')].'_'.$row[csf('challan_no')];
						// echo $row[csf('entry_form')].'='.$all_value.'<br>';
							//echo $row[csf('entry_form')].'d';
						if(in_array($all_value,$str_arr))
						{
							$booking_no=0; $independent=''; $bill_for_id=0; $bill_for_sb=0;
							if ($row[csf('entry_form')]==2)
							{
								if($row[csf('receive_basis')]==0) $independent=4;  //else $independent='';
								if ($row[csf('receive_basis')]==2) $booking_no=$plan_booking_arr[$row[csf('booking_no')]]; else if ($row[csf('receive_basis')]==1) $booking_no=$row[csf('booking_no')]; else $booking_no=0;
							}
							else if ($row[csf('entry_form')]==22)
							{
								if($row[csf('receive_basis')]==4) $independent=4; // else $independent='';
								if($row[csf('receive_basis')]==2 || $row[csf('receive_basis')]==11) $booking_no=$row[csf('booking_no')]; else $booking_no=0;
							}
							
							else if ($row[csf('entry_form')]==58)
							{
								$rec_basis=0; $bookinNo=""; $bookingId=0;
	
								$rec_basis=$roll_dlv_arr[$row[csf('booking_no')]]['receive_basis'];
								$bookinNo=$roll_dlv_arr[$row[csf('booking_no')]]['booking_no'];
								$bookingId=$roll_dlv_arr[$row[csf('booking_no')]]['booking_id'];
								if($row[csf('buyer_id')]==0) $row[csf('buyer_id')]=$roll_dlv_arr[$row[csf('booking_no')]]['buyer_id'];
								
								if($rec_basis==0 ) $independent=4; 
								if ($rec_basis==2) $booking_no=$plan_booking_arr[$bookinNo]; else if ($rec_basis==1) $booking_no=$bookinNo; else $booking_no=0;
							}
						
							$ex_booking="";
							if($booking_no!='') $ex_booking=explode('-',$booking_no); else $ex_booking="_";
							
							$bill_qty=$bill_qty_array[$row[csf('recv_number_prefix_num')]][$row[csf('challan_no')]][$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['qty'];
							$ref_no='';
							$avilable_qty=$row[csf('quantity')]-$bill_qty;
							$avilable_roll=$row[csf('roll_qty')];//$roll_no_arr[$row[csf('dtls_id')]]-$roll_qty;
							//$row[csf('order_id')]='';
							$po_no=$job_order_arr[$row[csf('order_id')]]['po'];$style=$job_order_arr[$row[csf('order_id')]]['style'];

							$str_val=$row[csf('id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number_prefix_num')].'_'.$row[csf('order_id')].'_'.$po_no.'_'.$style.'_'.$buyer_arr[$job_order_arr[$row[csf('order_id')]]['buyer']].'_'.$row[csf('roll_qty')].'_'.$row[csf('febric_description_id')].'_'.$composition_arr[$determ_arr[$row[csf('febric_description_id')]]].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('prod_id')].'_'.$product_dtls_arr[$row[csf('prod_id')]].'_'.$row[csf('quantity')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_0____1_'.$bodyPartTypeArr[$row[csf('body_part_id')]].'_'.$row[csf('quantity_pcs')].'_'.$row[csf('challan_no')].'_'.$ref_no;
							
							if($independent==4)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr id="tr_<?  echo $all_value; ?>" bgcolor="yellow" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>','<? echo $i; ?>');" >
									<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="1" checked ></td>
									<td width="30" align="center"><? echo $i; ?></td>
                                    <td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
                                    <td width="60"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
                                    <td width="70" style="word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
                                    <td width="70" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                                    <td width="90" style="word-break:break-all"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
                                    <td width="160" style="word-break:break-all"><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></td>
                                    <td width="60" style="word-break:break-all"><? //echo $color_type[$color_type_array[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['color_type']]; ?>&nbsp;</td>
                                    <td width="80" align="right"><? echo number_format($avilable_qty,2,'.',''); ?></td>
                                    <td width="80" align="right"><? echo number_format($row[csf('quantity_pcs')],2,'.',''); ?></td>
                                    <td align="right"><? echo number_format($avilable_roll,2,'.',''); ?>
                                    <input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
                                    <input type="hidden" style="width:40px" id="currid<? echo $all_value; ?>" value="<? echo 1; ?>">
                                    <input type="hidden" id="unique_id<? echo $i; ?>" value="<? echo $all_value; ?>"></td>
								</tr>
								<?php
								$i++;
							}
							else
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr id="tr_<?  echo $all_value; ?>" bgcolor="yellow" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>','<? echo $i; ?>');" >
									<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="1" checked ></td>
									<td width="30" align="center"><? echo $i; ?></td>
                                    <td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
                                    <td width="60"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
                                    <td width="70" style="word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
                                    <td width="70" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                                    <td width="90" style="word-break:break-all"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
                                    <td width="160" style="word-break:break-all"><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></td>
                                    <td width="60" style="word-break:break-all"><? //echo $color_type[$color_type_array[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['color_type']]; ?>&nbsp;</td>
                                    <td width="80" align="right"><? echo number_format($avilable_qty,2,'.',''); ?></td>
                                    <td width="80" align="right"><? echo number_format($row[csf('quantity_pcs')],2,'.',''); ?></td>
                                    <td align="right"><? echo number_format($avilable_roll,2,'.',''); ?>
                                    <input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
                                    <input type="hidden" style="width:40px" id="currid<? echo $all_value; ?>" value="<? echo 1; ?>">
                                    <input type="hidden" id="unique_id<? echo $i; ?>" value="<? echo $all_value; ?>"></td>
								</tr>
								<?php
								$i++;
							}
						}
					}
					
					foreach($sql_result as $row) // for new row
					{
						if($row[csf('order_id')]=='') $row[csf('order_id')]=0; 
						$independent='';
						if ($row[csf('entry_form')]==2)
						{
							if($row[csf('receive_basis')]==0) $independent=4; //else $independent='';
							$booking_no=$row[csf('booking_no')];
						}
						else if ($row[csf('entry_form')]==22)
						{
							if($row[csf('receive_basis')]==4) $independent=4; // else $independent='';
							$booking_no=$row[csf('booking_no')];
						}
						else if ($row[csf('entry_form')]==58 || $row[csf('entry_form')]==56)
						{
							$rec_basis=0; $bookinNo=""; $bookingId=0;// booking id is barcode, and booking no is delv id
							/*$rec_basis=$roll_dlv_arr[$row[csf('booking_no')]][$row[csf('booking_id')]]['receive_basis'];
							$bookinNo=$roll_dlv_arr[$row[csf('booking_no')]][$row[csf('booking_id')]]['booking_no'];
							$bookingId=$roll_dlv_arr[$row[csf('booking_no')]][$row[csf('booking_id')]]['booking_id'];*/

							$rec_basis=$roll_dlv_arr[$row[csf('booking_no')]]['receive_basis'];
							$bookinNo=$roll_dlv_arr[$row[csf('booking_no')]]['booking_no'];
							$bookingId=$roll_dlv_arr[$row[csf('booking_no')]]['booking_id'];
							if($row[csf('buyer_id')]==0) $row[csf('buyer_id')]=$roll_dlv_arr[$row[csf('booking_no')]]['buyer_id'];
							
							if($rec_basis==0 ) $independent=4; //else $independent='';
							if ($rec_basis==2) $booking_no=$plan_booking_arr[$bookinNo]; else if ($rec_basis==1) $booking_no=$bookinNo; else $booking_no=0;
						}
						else if ($row[csf('entry_form')]==53)
						{
							if($ex_bill_for==1) { if($row[csf('receive_basis')]==0) $independent=4; } //else $independent='';
							if ($row[csf('receive_basis')]==2) $booking_no=$plan_booking_arr[$row[csf('booking_no')]]; else if ($row[csf('receive_basis')]==1) $booking_no=$row[csf('booking_no')]; else $booking_no=0;
							if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2 && $row[csf('receive_basis')]!=0) $bill_for_id="SM";
							//echo $row[csf('booking_no')].'d';
						}
						$bill_for_sb="SMN";
						$ex_booking="";
						if($booking_no!='') $ex_booking=explode('-',$booking_no); else $ex_booking="_";
						//echo $row[csf('booking_no')];
						//if($ex_booking[1]!='Fb') echo $ex_booking[1];
						if($row[csf('challan_no')]=='') $row[csf('challan_no')]=0;
						$bill_qty=$bill_qty_array[$row[csf('recv_number_prefix_num')]][$row[csf('challan_no')]][$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['qty'];
						// echo $row[csf('quantity')].'==>'.$bill_qty."<br>";
						$avilable_qty=$row[csf('quantity')]-$bill_qty;
						$avilable_roll=$row[csf('roll_qty')]-$roll_qty;//$roll_no_arr[$row[csf('dtls_id')]]-$roll_qty;
						$ref_no=''; 
						$all_value=$row[csf('recv_number_prefix_num')].'_'.$row[csf('order_id')].'_'.$row[csf('prod_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')].'_'.$row[csf('challan_no')];
					//	$row[csf('order_id')]='';
							$po_no=$job_order_arr[$row[csf('order_id')]]['po'];$style=$job_order_arr[$row[csf('order_id')]]['style'];

						$str_val=$row[csf('id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number_prefix_num')].'_'.$row[csf('order_id')].'_'.$po_no.'_'.$style.'_'.$buyer_arr[$row[csf('buyer_id')]].'_'.$row[csf('roll_qty')].'_'.$row[csf('febric_description_id')].'_'.$composition_arr[$determ_arr[$row[csf('febric_description_id')]]].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('prod_id')].'_'.$product_dtls_arr[$row[csf('prod_id')]].'_'.$row[csf('quantity')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_0____1_'.$bodyPartTypeArr[$row[csf('body_part_id')]].'_'.$row[csf('quantity_pcs')].'_'.$row[csf('challan_no')].'_'.$ref_no.'_'.$ref_no.'_'.$ref_no.'_'.$ref_no.'_'.$ref_no;
						if($independent==4)
						{
							if($avilable_qty>0)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr id="tr_<?  echo $all_value; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>','<? echo $i; ?>');" > 
									<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="2" ></td>
									<td width="30" align="center"><? echo $i; ?></td>
                                    <td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
                                    <td width="60"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
                                    <td width="70" style="word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
                                    <td width="70" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                                    <td width="90" style="word-break:break-all"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
                                    <td width="160" style="word-break:break-all"><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></td>
                                    <td width="60" style="word-break:break-all"><? //echo $color_type[$color_type_array[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['color_type']]; ?>&nbsp;</td>
                                    <td width="80" align="right"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
                                    <td width="80" align="right"><? echo number_format($row[csf('quantity_pcs')],2,'.',''); ?></td>
                                    <td align="right"><? echo number_format($avilable_roll,2,'.',''); ?>
                                    <input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
                                    <input type="hidden" style="width:40px" id="currid<? echo $all_value; ?>" value="<? echo 1; ?>"><input type="hidden" id="unique_id<? echo $i; ?>" value="<? echo $all_value; ?>">
                                	</td>
								</tr>
								<?php
								$i++;
							}
						}
						else
						{
							//  echo strtolower($ex_booking[1]).'=='.strtolower($bill_for_sb).'==>'.$row[csf('recv_number_prefix_num')].'==>'.$row[csf('challan_no')].'==>'.$row[csf('quantity')].'==>'.$bill_qty."<br>";
							if( strtolower($ex_booking[1])==strtolower($bill_for_sb) ) 
							{
								// echo $avilable_qty.'sad';
								if($avilable_qty>0)
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr id="tr_<?  echo $all_value; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>','<? echo $i; ?>');" > 
										<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="2" ></td>
										<td width="30" align="center"><? echo $i; ?></td>
										<td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
										<td width="60"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
										<td width="70" style="word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
										<td width="70" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
										<td width="90" style="word-break:break-all"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
										<td width="160" style="word-break:break-all"><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></td>
										<td width="60" style="word-break:break-all"><? //echo $color_type[$color_type_array[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['color_type']]; ?>&nbsp;</td>
										<td width="80" align="right"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
                                        <td width="80" align="right"><? echo number_format($row[csf('quantity_pcs')],2,'.',''); ?></td>
										<td align="right"><? echo number_format($avilable_roll,2,'.',''); ?>
										<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
										<input type="hidden" style="width:40px" id="currid<? echo $all_value; ?>" value="<? echo 1; ?>">
										<input type="hidden" id="unique_id<? echo $i; ?>" value="<? echo $all_value; ?>">
									</td>
									</tr>
									<?php
									$i++;
								}
							}
						}
					}
				}
				?>
            </table>
        </div>
        <div>
            <table width="1000">
                <tr style="border:none">
                	<td bgcolor="#7FDF00" align="center"><input type="checkbox" name="checkall" id="checkall" class="formbutton" value="2" onClick="check_all_data();"/><b>Check all</b></td>
                    <td bgcolor="#FF80FF" align="center"><input type="button" id="show_button" align="middle" class="formbutton" style="width:100px" value="Close" onClick="window_close(0);" /></td>
                </tr>
           </table>
      </div>
      </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?	
	}
	exit();
}

if ($action=="load_dtls_data") 
{
	$ex_data=explode("!^!",$data);

	$upid=$ex_data[0];
	$billfor = $ex_data[2];
	//echo $ex_data[1].'D';

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$roll_no_arr=return_library_array( "select id, no_of_roll from  pro_grey_prod_entry_dtls",'id','no_of_roll');
	$bodyPartTypeArr = return_library_array( "select id, body_part_type from lib_body_part",'id','body_part_type');
    
	if($ex_data[1]!=2)
	{
		$product_dtls_arr=return_library_array( "select id,product_name_details from  product_details_master",'id','product_name_details');
		if ($billfor == 4) //========FSO================ subcon_inbound_bill_dtls
		{
			 $sql_order_sales = "Select b.id, b.job_no as po_number,b.po_buyer as buyer_name, b.po_job_no,b.style_ref_no from subcon_inbound_bill_dtls a, fabric_sales_order_mst b where a.order_id=b.id  and a.mst_id='$upid' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$sql_order_sales_result = sql_select($sql_order_sales);
			foreach ($sql_order_sales_result as $row) {
				$job_order_arr[$row[csf("id")]]['po'] = $row[csf("po_number")];
				$job_order_arr[$row[csf("id")]]['style'] = $row[csf("style_ref_no")];
				$job_order_arr[$row[csf("id")]]['buyer'] = $row[csf("buyer_name")];
				$job_order_arr[$row[csf('id')]]['job']=$row[csf('po_job_no')];
				$job_order_arr[$row[csf('id')]]['grouping']='';
			}
			unset($sql_order_result);
			//print_r($po_array);
		}
		if ($billfor == 2 || $billfor == 1) 
		{
		$job_order_arr=array();
		$sql_job="Select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active!=0 and a.is_deleted=0  and b.status_active!=0 and b.is_deleted=0";
		$sql_job_result =sql_select($sql_job);
		foreach($sql_job_result as $row)
		{
			$job_order_arr[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$job_order_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$job_order_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$job_order_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
			$job_order_arr[$row[csf('id')]]['grouping']=$row[csf('grouping')];
		}
		unset($sql_job_result);
		}
	}
	else
	{
		$product_dtls_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');

		$job_order_arr=array();
		$sql_job="Select a.job_no_prefix_num, a.party_id as buyer_name, b.cust_style_ref as style_ref_no, b.id, b.order_no as po_number from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0";

		$sql_job_result =sql_select($sql_job);
		foreach($sql_job_result as $row)
		{
			$job_order_arr[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$job_order_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$job_order_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$job_order_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		unset($sql_job_result);
	}

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
	/*$str_val=$row[csf('id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$job_order_arr[$row[csf('po_breakdown_id')]]['po'].'_'.$job_order_arr[$row[csf('po_breakdown_id')]]['style'].'_'.$buyer_arr[$job_order_arr[$row[csf('po_breakdown_id')]]['buyer']].'_'.$row[csf('roll_qty')].'_'.$row[csf('febric_description_id')].'_'.$composition_arr[$determ_arr[$row[csf('febric_description_id')]]].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('prod_id')].'_'.$product_dtls_arr[$row[csf('prod_id')]].'_'.$row[csf('quantity')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_0____1_'.$bodyPartTypeArr[$row[csf('body_part_id')]].'_'.$row[csf('quantity_pcs')].'_'.$row[csf('challan_no')];*/
	
	$sql="select id as upd_id, delivery_id,rec_challan_no, delivery_date, challan_no, item_id, body_part_id, febric_description_id, uom, packing_qnty as carton_roll, delivery_qty, delivery_qtypcs, lib_rate_id, rate, amount, remarks, order_id, coller_cuff_measurement,currency_id,is_sales,yarn_count,mc_dia from subcon_inbound_bill_dtls where mst_id=$upid and process_id=2 and status_active=1 and is_deleted=0 and process_id=2 order by upd_id ASC";
	
	$sql_result_arr =sql_select($sql); $str_val="";
	foreach ($sql_result_arr as $row)
	{
		
		if($row[csf('carton_roll')]=='') $row[csf('carton_roll')]=0;
		if($row[csf('body_part_id')]=='') $row[csf('body_part_id')]=0;
		if($row[csf('yarn_count')]=='') $row[csf('yarn_count')]=0;
		if($row[csf('mc_dia')]=='') $row[csf('mc_dia')]=0;
		if($row[csf('is_sales')]=='') $row[csf('is_sales')]=0;

		if($body_part[$row[csf("body_part_id")]]=='') $body_part[$row[csf("body_part_id")]]=0;
		if($bodyPartTypeArr[$row[csf('body_part_id')]]=='') $bodyPartTypeArr[$row[csf('body_part_id')]]=0;
		if($row[csf("delivery_qty")]=='') $row[csf("delivery_qty")]=0;
		if($row[csf("delivery_qtypcs")]=='') $row[csf("delivery_qtypcs")]=0;
		if($row[csf('rec_challan_no')]=='') $row[csf('rec_challan_no')]=0;
		if($job_order_arr[$row[csf('order_id')]]['grouping']=='') $job_order_arr[$row[csf('order_id')]]['grouping']='';
						
		
		if($str_val=="") $str_val=$row[csf('delivery_id')].'_'.change_date_format($row[csf('delivery_date')]).'_'.$row[csf('challan_no')].'_'.$row[csf('order_id')].'_'.$job_order_arr[$row[csf('order_id')]]['po'].'_'.$job_order_arr[$row[csf('order_id')]]['style'].'_'.$buyer_arr[$job_order_arr[$row[csf('order_id')]]['buyer']].'_'.$row[csf('carton_roll')].'_'.$row[csf('febric_description_id')].'_'.$composition_arr[$determ_arr[$row[csf('febric_description_id')]]].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('item_id')].'_'.$product_dtls_arr[$row[csf('item_id')]].'_'.$row[csf('delivery_qty')].'_'.$row[csf('delivery_qtypcs')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('lib_rate_id')].'_'.$row[csf('upd_id')].'_'.$row[csf('remarks')].'_'.$row[csf('uom')].'_'.$row[csf('coller_cuff_measurement')].'_'.$row[csf('currency_id')].'_'.$bodyPartTypeArr[$row[csf('body_part_id')]].'_'.$row[csf('rec_challan_no')].'_'.$job_order_arr[$row[csf('order_id')]]['grouping'].'_'.$is_sales.'_'.$row[csf('yarn_count')].'_'.$row[csf('mc_dia')];
		
		else $str_val.="###".$row[csf('delivery_id')].'_'.change_date_format($row[csf('delivery_date')]).'_'.$row[csf('challan_no')].'_'.$row[csf('order_id')].'_'.$job_order_arr[$row[csf('order_id')]]['po'].'_'.$job_order_arr[$row[csf('order_id')]]['style'].'_'.$buyer_arr[$job_order_arr[$row[csf('order_id')]]['buyer']].'_'.$row[csf('carton_roll')].'_'.$row[csf('febric_description_id')].'_'.$composition_arr[$determ_arr[$row[csf('febric_description_id')]]].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('item_id')].'_'.$product_dtls_arr[$row[csf('item_id')]].'_'.$row[csf('delivery_qty')].'_'.$row[csf('delivery_qtypcs')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('lib_rate_id')].'_'.$row[csf('upd_id')].'_'.$row[csf('remarks')].'_'.$row[csf('uom')].'_'.$row[csf('coller_cuff_measurement')].'_'.$row[csf('currency_id')].'_'.$bodyPartTypeArr[$row[csf('body_part_id')]].'_'.$row[csf('rec_challan_no')].'_'.$job_order_arr[$row[csf('order_id')]]['grouping'].'_'.$is_sales.'_'.$row[csf('yarn_count')].'_'.$row[csf('mc_dia')];
		
		//$row[csf('uom')].'_'.$row[csf('currency_id')].'_'.$bodyPartTypeArr[$row[csf('body_part_id')]];
		
		//$order_array[$row[csf('order_id')]]['order_uom'].'_1_'.$subprocess_uom.'_'.$row[csf('collar_cuff')].'_'.$bodyPartTypeArr[$item_body_part_id[$row[csf('item_id')]]];
	}
	echo $str_val;
	exit();
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

	function fnc_kniting_terms_condition( operation )
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
		http.open("POST","knitting_bill_issue_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_kniting_terms_condition_reponse;
	}

	function fnc_kniting_terms_condition_reponse()
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
                            <td><input type="text" id="sltd_<? echo $i;?>" name="sltd_<? echo $i;?>" style="width:100%;" value="<? echo $i; ?>" disabled/></td>
                            <td><input type="text" id="termscondition_<? echo $i;?>" name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>" /></td>
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
                            <td><input type="text" id="sltd_<? echo $i;?>" name="sltd_<? echo $i;?>" style="width:100%;" value="<? echo $i; ?>" disabled /></td>
                            <td><input type="text" id="termscondition_<? echo $i;?>" name="termscondition_<? echo $i;?>" style="width:95%" class="text_boxes" value="<? echo $row[csf('terms')]; ?>" /></td>
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
                <td align="center" width="100%" class="button_container"><? echo load_submit_buttons( $permission, "fnc_kniting_terms_condition", 0,0 ,"reset_form('termscondi_1','','','','')",1) ;?>
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
			$data_array .="(".$id.",".$txt_bill_no.",".$$termscondition.",1)";
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
		else if($db_type==2 || $db_type==1 )
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

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$bill_process_id="2";
	
	
 	$order_ids=str_replace("'","",$orderIds);
	$order_ids=implode(",",array_unique(explode(",",$order_ids)));
	$updateId=str_replace("'","",$update_id);
	$control_with=str_replace("'","",$hddn_control_with);
	$cbo_bill_sectionId=str_replace("'","",$cbo_bill_section);
	$party_sourceId=str_replace("'","",$cbo_party_source);
	$bill_forId=str_replace("'","",$cbo_bill_for);
	 
	$is_sales = 0;
	if ($bill_forId == 4) {
		$is_sales = 1;
	}

	//echo "10**=".$control_with.'='.$cbo_bill_sectionId.'='.$party_sourceId;die;
	if($bill_forId!=4 && $control_with==1 && $cbo_bill_sectionId==1)
	{
		if($bill_forId!=3)
		{
			if($party_sourceId==1)
			{
	
				$sql_po=sql_select("select b.id, b.job_no_mst,a.exchange_rate from wo_po_break_down b,wo_pre_cost_mst a where  b.job_id= a.job_id and a.status_active=1 and b.status_active=1 and  b.id in($order_ids) ");
				foreach($sql_po as $row)
				{
					$job_arr[$row[csf('id')]]=$row[csf('job_no_mst')];
					$job_exchnage_arr[$row[csf('job_no_mst')]]=$row[csf('exchange_rate')];
				}
				$condition = new condition();
				if($order_ids!='' || $order_ids!=0)
				{
					$condition->po_id_in($order_ids); 
				}
				
				$condition->init();
				$conversion= new conversion($condition);
				//echo "10**=".$conversion->getQuery(); die;
				$conversion_costing_arr=$conversion->getAmountArray_by_orderAndProcess();
				
				if($updateId!="") $thisbill_cond=" and a.id!='$updateId'"; else $thisbill_cond="";
				
				$previous_bill_sql=sql_select("select b.order_id,sum(b.amount) as amount from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.party_source=1 and a.process_id=2 and b.order_id in ($order_ids) $thisbill_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id");
			//	echo "10**=select b.order_id,sum(b.amount) as amount from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.party_source=1 and a.process_id=2 and b.order_id in ($order_ids) $thisbill_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id";die;
				foreach($previous_bill_sql as $row)
				{
					$previous_bill_amountArr[$row[csf('order_id')]]=$row[csf('amount')];
				}
				for($i=1; $i<=$tot_row; $i++)
				{
					 
					$po_id="ordernoid_".$i;
					$po_ids=str_replace("'","",$$po_id);
					//$quantity="deliveryqnty_".$i;
					//$rate="txtrate_".$i;
					$amount="amount_".$i;
					$curanci="curanci_".$i;
					$current_amount=str_replace("'","",$$amount);
					$curanciId=str_replace("'","",$$curanci);
					$previous_bill_amount=$previous_bill_amountArr[$po_ids];
					if($previous_bill_amount=='') $previous_bill_amount=0;
					$msg="Total bill amount exceeding costing amount not allowed.";
					
	
					$budget_amount=0;
					if($curanciId==1) //TK
					{
						$ex_change_rate=$job_exchnage_arr[$job_arr[$po_ids]];	
						$budget_amount=array_sum($conversion_costing_arr[$po_ids][1])*$ex_change_rate;
					}
					else
					{
						//$ex_change_rate=1;	
						$budget_amount=array_sum($conversion_costing_arr[$po_ids][1]);
					}
					
					
					$total_bill_amount=$previous_bill_amount+$current_amount;
					$avaible_bill_amount=$budget_amount-$previous_bill_amount;
				//	echo "10**=".$previous_bill_amount.'='.$current_amount.'='.$budget_amount;die;
					
					if($total_bill_amount>$budget_amount)
					{
						echo "17**".rtrim($previous_bill_amount)."**".rtrim($budget_amount)."**".rtrim($avaible_bill_amount)."**".$msg;
						disconnect($con);die;
						//echo $total_bill_amount."_".$budget_amount."_".$previous_bill_amount."_".$current_amount;
					}
				} //Loop end
				
			}
		}
    }
	//echo "10**=Aziz";die;
	
	if ($operation==0)   // Insert Here========================================================================================delivery_id
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		if (is_duplicate_field( "delivery_id", "subcon_inbound_bill_dtls", "mst_id=$update_id" )==1)
		{
			echo "11**0"; 
			disconnect($con);
			die;			
		}
		if($db_type==0)$year_cond=" and YEAR(insert_date)";	
		else if($db_type==2) $year_cond=" and TO_CHAR(insert_date,'YYYY')";	
		
		$new_bill_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'KNT', date("Y",time()), 5, "select prefix_no, prefix_no_num from  subcon_inbound_bill_mst where company_id=$cbo_company_id and process_id=$bill_process_id $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
			
		if(str_replace("'",'',$update_id)=="")
		{
			$id=return_next_id( "id", "subcon_inbound_bill_mst", 1 ) ; 	
			$field_array="id, prefix_no, prefix_no_num, bill_no, company_id, location_id,party_location_id, bill_date, party_id, party_source, attention,upcharge,discount, bill_for, process_id, bill_section, inserted_by, insert_date";
			$data_array="(".$id.",'".$new_bill_no[1]."','".$new_bill_no[2]."','".$new_bill_no[0]."',".$cbo_company_id.",".$cbo_location_name.",".$cbo_party_location.",".$txt_bill_date.",".$cbo_party_name.",".$cbo_party_source.",".$txt_attention.",".$txt_upcharge.",".$txt_discount.",".$cbo_bill_for.",'".$bill_process_id."',".$cbo_bill_section.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
			
			$return_no=$new_bill_no[0]; 
		}
		else
		{
			$id=str_replace("'",'',$update_id);
			$field_array="location_id*bill_date*party_id*party_source*attention*bill_for*bill_section*updated_by*update_date";
			$data_array="".$cbo_location_name."*".$txt_bill_date."*".$cbo_party_name."*".$cbo_party_source."*".$txt_attention."*".$txt_upcharge."*".$txt_discount."*".$cbo_bill_for."*".$cbo_bill_section."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			
			$return_no=str_replace("'",'',$txt_bill_no);
		}
		$id1=return_next_id( "id", "subcon_inbound_bill_dtls",1);
		$field_array1 ="id, mst_id, delivery_id,rec_challan_no, delivery_date, challan_no, order_id,internal_ref, item_id, febric_description_id, body_part_id, uom, packing_qnty, delivery_qty, delivery_qtypcs, lib_rate_id, rate, amount, remarks, currency_id, process_id,is_sales,yarn_count,mc_dia, inserted_by, insert_date, coller_cuff_measurement";
		$field_array_up ="delivery_id*rec_challan_no*delivery_date*challan_no*order_id*internal_ref*item_id* febric_description_id*body_part_id*uom*packing_qnty*delivery_qty*delivery_qtypcs*lib_rate_id*rate*amount*remarks*currency_id*yarn_count*mc_dia*updated_by*update_date*coller_cuff_measurement";
		$field_array_delivery="bill_status";
		$field_array_order="cust_buyer*cust_style_ref";
		$process_id=2;
		$add_comma=0;
		for($i=1; $i<=$tot_row; $i++)
		{
			$delivery_id="deliveryid_".$i;
			$deleverydate="deleverydate_".$i;
			$challen_no="challenno_".$i;
			$recchallanno="recchallanno_".$i;
			$orderid="ordernoid_".$i;
			$style_name="stylename_".$i;
			$internal_ref="internalref_".$i;
			$buyer_name="buyername_".$i;
			$item_id="itemid_".$i;
			$compoid="compoid_".$i;
			$bodypartid="bodypartid_".$i;
			$cbouom="cbouom_".$i;
			$number_roll="numberroll_".$i;
			$quantity="deliveryqnty_".$i;
			$deliveryqntypcs="deliveryqntypcs_".$i;
			$rate="txtrate_".$i;
			$libRateId="libRateId_".$i;
			$amount="amount_".$i;
			$remarks="remarksvalue_".$i;
			$curanci="curanci_".$i;
			$yarncount="yarncount_".$i;
			$mcdia="mcdia_".$i;
			$updateid_dtls="updateiddtls_".$i;
			$collarcuff="collarcuff_".$i;
			
			$delevery_date=str_replace("'",'',$$deleverydate);
			$delevery_date=date('d-M-Y',strtotime($delevery_date));
			
			if(str_replace("'",'',$$updateid_dtls)=="")  
			{
				if($$amount!="")
				{
					if ($add_comma!=0) $data_array1 .=",";
					$data_array1 .="(".$id1.",".$id.",".$$delivery_id.",".$$recchallanno.",'".$delevery_date."',".$$challen_no.",".$$orderid.",".$$internal_ref.",".$$item_id.",".$$compoid.",".$$bodypartid.",".$$cbouom.",".$$number_roll.",".$$quantity.",".$$deliveryqntypcs.",".$$libRateId.",".$$rate.",".$$amount.",".$$remarks.",".$$curanci.",'".$process_id."','".$is_sales."',".$$yarncount.",".$$mcdia.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$collarcuff.")";
					$id1=$id1+1;
					$add_comma++;
				}
				//$id_arr_delivery[]=str_replace("'",'',$$delivery_id);
				//$data_array_delivery[str_replace("'",'',$$delivery_id)] =explode("*",("1")); 
				
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
				$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$delivery_id."*".$$recchallanno."*'".$delevery_date."'*".$$challen_no."*".$$orderid."*".$$internal_ref."*".$$item_id."*".$$compoid."*".$$bodypartid."*".$$cbouom."*".$$number_roll."*".$$quantity."*".$$deliveryqntypcs."*".$$libRateId."*".$$rate."*".$$amount."*".$$remarks."*".$$curanci."*".$$yarncount."*".$$mcdia."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$$collarcuff.""));
				$id_arr_delivery[]=str_replace("'",'',$$delivery_id);
				$data_array_delivery[str_replace("'",'',$$delivery_id)] =explode("*",("1"));
			}
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
		$flag=1;
		if(str_replace("'",'',$update_id)=="")
		{
			//echo "10**INSERT INTO subcon_inbound_bill_mst (".$field_array.") VALUES ".$data_array; die; 
			$rID=sql_insert("subcon_inbound_bill_mst",$field_array,$data_array,1);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("subcon_inbound_bill_mst",$field_array,$data_array,"id",$update_id,0);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		//$rID1=execute_query(bulk_update_sql_statement("subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
		//if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		if (str_replace("'",'',$cbo_party_source)==2)
		{
			//echo bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array_order,$data_array_order,$order_id_arr );die;
			$rID2=execute_query(bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_array_delivery,$id_arr_delivery ));
			if($rID2==1 && $flag==1) $flag=1; else $flag=0;
			//$rID4=execute_query(bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array_order,$data_array_order,$order_id_arr ));
			//if($rID4==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		if($data_array1!="")
		{
			//echo "10**=insert into subcon_inbound_bill_dtls (".$field_array1.") values ".$data_array1;die;
			$rID1=sql_insert("subcon_inbound_bill_dtls",$field_array1,$data_array1,1);
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		//echo "10**".$rID.'='.$rID1.'='.$flag;die;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".'0';
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".'0';
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".'0';
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".'0';
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
			disconnect($con);
			exit();
		}
		
		$field_array="bill_date*bill_section*attention*upcharge*discount*updated_by*update_date";
		$data_array="".$txt_bill_date."*".$cbo_bill_section."*".$txt_attention."*".$txt_upcharge."*".$txt_discount."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		//$dtls_update_id_array=array();
		$sql_dtls="Select id from subcon_inbound_bill_dtls where mst_id=$update_id and status_active=1 and is_deleted=0";
		$nameArray=sql_select( $sql_dtls );
		foreach($nameArray as $row)
		{
			$dtls_update_id_array[]=$row[csf('id')];
		}
		 
		$return_no=str_replace("'",'',$txt_bill_no);
		
		$id1=return_next_id( "id", "subcon_inbound_bill_dtls",1);
		$field_array1 ="id, mst_id, delivery_id,rec_challan_no, delivery_date, challan_no, order_id,internal_ref, item_id, febric_description_id, body_part_id, uom, packing_qnty, delivery_qty, delivery_qtypcs, lib_rate_id, rate, amount, remarks, currency_id,yarn_count,mc_dia, process_id, is_sales,inserted_by, insert_date, coller_cuff_measurement";
		$field_array_up ="delivery_id*rec_challan_no*delivery_date*challan_no*order_id*internal_ref*item_id*febric_description_id*body_part_id*uom*packing_qnty*delivery_qty*delivery_qtypcs*lib_rate_id*rate*amount*remarks*currency_id*yarn_count*mc_dia*updated_by*update_date*coller_cuff_measurement";
		$field_array_delivery="bill_status";
		$field_array_order="cust_buyer*cust_style_ref";
		$process_id=2;
		$add_comma=0;
		for($i=1; $i<=$tot_row; $i++)
		{
			$delivery_id="deliveryid_".$i;
			$deleverydate="deleverydate_".$i;
			$challen_no="challenno_".$i;
			$recchallanno="recchallanno_".$i;
			$orderid="ordernoid_".$i;
			$style_name="stylename_".$i;
			$internal_ref="internalref_".$i;
			$buyer_name="buyername_".$i;
			$item_id="itemid_".$i;
			$compoid="compoid_".$i;
			$bodypartid="bodypartid_".$i;
			$cbouom="cbouom_".$i;
			$number_roll="numberroll_".$i;
			$quantity="deliveryqnty_".$i;
			$deliveryqntypcs="deliveryqntypcs_".$i;
			$rate="txtrate_".$i;
			$libRateId="libRateId_".$i;
			$amount="amount_".$i;
			$remarks="remarksvalue_".$i;
			$curanci="curanci_".$i;
			$yarncount="yarncount_".$i;
			$mcdia="mcdia_".$i;
			$updateid_dtls="updateiddtls_".$i;
			$collarcuff="collarcuff_".$i;
			//echo $up_id=str_replace("'",'',$$updateid_dtls);
			$delevery_date=str_replace("'",'',$$deleverydate);
			$delevery_date=date('d-M-Y',strtotime($delevery_date));
				
			if(str_replace("'",'',$$updateid_dtls)=="")  
			{ 
				if ($add_comma!=0) $data_array1 .=",";
				$data_array1 .="(".$id1.",".$id.",".$$delivery_id.",".$$recchallanno.",'".$delevery_date."',".$$challen_no.",".$$orderid.",".$$internal_ref.",".$$item_id.",".$$compoid.",".$$bodypartid.",".$$cbouom.",".$$number_roll.",".$$quantity.",".$$deliveryqntypcs.",".$$libRateId.",".$$rate.",".$$amount.",".$$remarks.",".$$curanci.",".$$yarncount.",".$$mcdia.",'".$process_id."','".$is_sales."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$collarcuff.")";
				$id1=$id1+1;
				$add_comma++;
				//$id_arr_delivery[]=str_replace("'",'',$$delivery_id);
				//$data_array_delivery[str_replace("'",'',$$delivery_id)] =explode("*",("1")); 
				
				$id_arr_deli=implode(',',explode('_',str_replace("'",'',$$delivery_id)));
				$delv_id=explode(',',$id_arr_deli);
				foreach($delv_id as $val)
				{
					$id_arr_delivery[]=$val;
					$data_array_delivery[$val] =explode("*",("1"));
				}
			}
			else
			{
				$id_arr[]=str_replace("'",'',$$updateid_dtls);
				$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$delivery_id."*".$$recchallanno."*'".$delevery_date."'*".$$challen_no."*".$$orderid."*".$$internal_ref."*".$$item_id."*".$$compoid."*".$$bodypartid."*".$$cbouom."*".$$number_roll."*".$$quantity."*".$$deliveryqntypcs."*".$$libRateId."*".$$rate."*".$$amount."*".$$remarks."*".$$curanci."*".$$yarncount."*".$$mcdia."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$$collarcuff.""));
				//$id_arr_delivery[]=str_replace("'",'',$$delivery_id);
				//$data_array_delivery[str_replace("'",'',$$delivery_id)] =explode("*",("1"));
				$id_arr_deli=implode(',',explode('_',str_replace("'",'',$$delivery_id)));
				$delv_id=explode(',',$id_arr_deli);
				foreach($delv_id as $val)
				{
					$id_arr_delivery[]=$val;
					$data_array_delivery[$val] =explode("*",("1"));
				}
				
			}
			//order table insert====================================================================================================
			if (str_replace("'",'',$cbo_party_source)==2)
			{
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
			//order table insert====================================================================================================
		}
		//echo $distance_delete_id; die;
		$flag=1;
		//echo "10**=".bulk_update_sql_statement2( "subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr ); die;
		//--------------Previous Data pickup---------------//
		$sql_dtls="Select id,delivery_id from subcon_inbound_bill_dtls where mst_id=$update_id and status_active=1 and is_deleted=0";
		$nameArray=sql_select( $sql_dtls );
		foreach($nameArray as $row)
		{
			$dtlsUpdate_id_array[]=$row[csf('id')];
			$DeliveryIdUpdate_array[]=$row[csf('delivery_id')];
		}
		 
		if(implode(',',$id_arr_delivery)!="")
		{
			//$delivery_delete_id=array_diff($DeliveryIdUpdate_array,$id_arr_delivery);
			$delivery_delete_id=implode(',',array_diff($DeliveryIdUpdate_array,$id_arr_delivery));
		}
		else
		{
			//$delivery_delete_id=$DeliveryIdUpdate_array;
			$delivery_delete_id=implode(',',$DeliveryIdUpdate_array);
		}
		
		$rID=sql_update("subcon_inbound_bill_mst",$field_array,$data_array,"id",$update_id,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		if($data_array_up!="")
		{
			//echo "10**=A".bulk_update_sql_statement("subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr );die;
		$rID1=execute_query(bulk_update_sql_statement2("subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		if($data_array1!="")
		{
			//echo "10**insert into subcon_inbound_bill_dtls (".$field_array1.") values ".$data_array1.'='.$flag;die;
			$rID1=sql_insert("subcon_inbound_bill_dtls",$field_array1,$data_array1,0);
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		if (str_replace("'",'',$cbo_party_source)==1)
		{
			if(implode(',',$id_arr)!="")
			{
				$distance_delete_id=implode(',',array_diff($dtlsUpdate_id_array,$id_arr));
			}
			else
			{
				$distance_delete_id=implode(',',$dtlsUpdate_id_array);
			}
			if(str_replace("'",'',$distance_delete_id)!="")
			{
				//$rID3=execute_query( "delete from subcon_inbound_bill_dtls where id in ($distance_delete_id)",0);
				$query="UPDATE subcon_inbound_bill_dtls SET status_active=0 and is_deleted=1 WHERE id in ($distance_delete_id)";
				$rID3=execute_query($query,0);
				if($rID3==1 && $flag==1) $flag=1; else $flag=0;
			}
		}
		else
		{
			if(implode(',',$id_arr)!="")
			{
				$distance_delete_id=implode(',',array_diff($dtlsUpdate_id_array,$id_arr));
			}
			else
			{
				$distance_delete_id=implode(',',$dtlsUpdate_id_array);
			}
			if(str_replace("'",'',$distance_delete_id)!="")
			{
				//$rID3=execute_query( "delete from subcon_inbound_bill_dtls where id in ($distance_delete_id)",0);
				$query="UPDATE subcon_inbound_bill_dtls SET status_active=0 , is_deleted=1 WHERE id in ($distance_delete_id)";
				$rID3=execute_query($query,0);
				if($rID3==1 && $flag==1) $flag=1; else $flag=0;
				//echo "10**=".$rID3.'='.$flag.'='.$query;die;
			}
			$new_delete_id=implode(",",explode("_",str_replace("'",'',$delete_id)));
			$all_delv_id=explode(",",$new_delete_id);
			for ($i=0;$i<count($all_delv_id);$i++)
			{
				$id_delivery[]=$all_delv_id[$i];
				$data_delivery[str_replace("'",'',$all_delv_id[$i])] =explode(",",("0"));
			}
			if($delivery_delete_id!="")
			{
				$query_delivery="UPDATE subcon_delivery_dtls SET bill_status=0  WHERE id in ($delivery_delete_id)";
				$rID2=execute_query($query_delivery,0);
				//echo "10**=".$query;die;
				if($rID2==1 && $flag==1) $flag=1; else $flag=0;
			}
		}
		//  echo "10**".$rID.'-'.$rID1.'-'.$rID2.'-'.$flag; die;
				
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$posted_account);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$posted_account);
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
	else if ($operation==2)  //Delete here======================================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=str_replace("'",'',$update_id);
		
		$id=str_replace("'",'',$update_id);
		
		$nameArray= sql_select("select is_posted_account, post_integration_unlock from subcon_inbound_bill_mst where id='$id'");
		$posted_account=$nameArray[0][csf('is_posted_account')];
		$post_integration_unlock=$nameArray[0][csf('post_integration_unlock')];
		
		if($posted_account==1 && $post_integration_unlock==0)
		{
			echo "14**All Ready Posted in Accounting.";
			disconnect($con);
			die;
		}
		
		$return_no=str_replace("'",'',$txt_bill_no);
		$field_array_delivery="bill_status";
		for($i=1; $i<=$tot_row; $i++)
		{
			$delivery_id="deliveryid_".$i;
			$updateid_dtls="updateiddtls_".$i;
			//$rID3=execute_query( "delete from subcon_inbound_bill_dtls where delivery_id in ($delete_id)",0);
			$delivery_id=explode(",",str_replace("'",'',$$delivery_id));
			for ($k=0; $k<count($delivery_id); $k++)
			{
				$id_delivery[]=$delivery_id[$k];
				$data_delivery[str_replace("'",'',$delivery_id[$k])] =explode(",",("0"));
			}
		}
		$flag=1;
		$rID=execute_query( "update subcon_inbound_bill_mst set updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."', status_active=0, is_deleted=1 where id ='$id' and status_active=1 and is_deleted=0 ",0);
		if( $rID==1 && $flag==1 ) $flag=1; else $flag=0;
		$rID1=execute_query( "update subcon_inbound_bill_dtls set updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."', status_active=0, is_deleted=1 where mst_id ='$id' and status_active=1 and is_deleted=0",0);
		if( $rID1==1 && $flag==1 ) $flag=1; else $flag=0;
		//echo bulk_update_sql_statement( "subcon_delivery", "id",$field_array_delivery,$data_delivery,$id_delivery ); 
		if (str_replace("'",'',$cbo_party_source)==2 && $flag==1)
		{
			$rID4=execute_query(bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_delivery,$id_delivery ));
			if( $rID4==1 && $flag==1 ) $flag=1; else $flag=0;
		}
		
		if($db_type==0)
		{
			if($flag==1)
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
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
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
//bulk_update_sql_statement2 for ISD-22-30168
function bulk_update_sql_statement2($table, $id_column, $update_column, $data_values, $id_count) {
	$field_array = explode("*", $update_column);
	//$id_count=explode("*",$id_count);
	//$data_values=explode("*",$data_values);
	//print_r($data_values);die;
	$idsstr=implode(",",$id_count);
	$id_countarr=count($id_count);
	$ids_cond="";
	if($id_countarr>1000)
	{
		$ids_cond=" and (";
		$idsArr=array_chunk(explode(",",$idsstr),999);
		foreach($idsArr as $ids)
		{
			$ids=implode(",",$ids);
			$ids_cond.=" $id_column in($ids) or"; 
		}
		$ids_cond=chop($ids_cond,'or ');
		$ids_cond.=")";
	}
	else
	{
		$ids_cond="and $id_column in (" . implode(",", $id_count) . ")";
	}
	
	//return $ids_cond;  die;
	$sql_up .= "UPDATE $table SET ";

	for ($len = 0; $len < count($field_array); $len++) {
		$sql_up .= " " . $field_array[$len] . " = CASE $id_column ";
		for ($id = 0; $id < count($id_count); $id++) {
			if (trim($data_values[$id_count[$id]][$len]) == "") {
				$sql_up .= " when " . $id_count[$id] . " then  '" . $data_values[$id_count[$id]][$len] . "'";
			} else {
				$sql_up .= " when " . $id_count[$id] . " then  " . $data_values[$id_count[$id]][$len] . "";
			}

		}
		if ($len != (count($field_array) - 1)) {
			$sql_up .= " END, ";
		} else {
			$sql_up .= " END ";
		}

	}
	$sql_up .= " where 1=1 $ids_cond";
	//$sql_up .= " where $id_column in (" . implode(",", $id_count) . ")";
	return $sql_up;
}

//======================================================================Bill Print============================================================================================
if($action=="knitting_bill_print") 
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//echo $data[5];die;
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$party_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name");
	$yarn_desc_arr=return_library_array( "select id,yarn_description from lib_subcon_charge",'id','yarn_description');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	
	 $sql_mst="Select id, bill_no, bill_date, party_id, party_source, location_id, attention, bill_for,party_location_id,upcharge,discount from subcon_inbound_bill_mst where company_id=$data[0] and id='$data[1]' and bill_no='$data[2]' and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql_mst);
	?>
    <div style="width:930px;margin:0px auto;">
    <table width="100%" cellpadding="0" cellspacing="0" >
        <tr>
            <td width="70" align="right"> 
            	<img  src='../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='70' width='200' />
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
                        <td align="center" style="font-size:14px"><? echo show_company($data[0],'',''); ?></td>  
                    </tr>
                    <tr>
                    	<td align="center" style="font-size:18px"><strong><? echo $data[3]; ?></strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
     <table width="915" cellspacing="0" align="" border="0">
            <tr>
                <td width="130"><strong>Bill No :</strong></td> <td width="175"><? echo $dataArray[0][csf('bill_no')]; ?></td>
                <td width="130"><strong>Bill Date: </strong></td><td width="175px"> <? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
                <td width="130"><strong>Source :</strong></td> <td width="175"><? echo $knitting_source[$dataArray[0][csf('party_source')]]; ?></td>
            </tr>
            <tr>
				<?
                    if($dataArray[0][csf('attention')]!='')  $attention='('.$dataArray[0][csf('attention')].')'; else $attention='';
                        
                    if($dataArray[0][csf('party_source')]==2)
                    {
                        $party_add=$dataArray[0][csf('party_id')];
                        $nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add"); 
                        foreach ($nameArray as $result)
                        { 
                            $address="";
                            if($result!="") $address=$result[csf('address_1')];
                        }
						$party_name=$party_library[$dataArray[0][csf('party_id')]].' :</br> Address :- '.$address.' '.$attention;
						$party_location='';
                    }
                    else
                    {
						$party_name=$company_library[$dataArray[0][csf('party_id')]].' '.$attention;
						$party_location=$location_arr[$dataArray[0][csf('party_location_id')]];
                    }
                ?>
                <td  width="150"  valign="top"><strong>Party Name: </strong></td><td width="200"  valign="top"><p> <? echo $party_name; ?></p></td>
				<td width="180"  valign="top"><strong>Party Location: </strong></td><td  width="200"  valign="top"> <? echo $party_location; ?></td>
                <td width="150"  valign="top"><strong>Bill For : </strong></td><td width="200"  valign="top"> <? echo $bill_for[$dataArray[0][csf('bill_for')]]; ?></td>
            </tr>
        </table>
         <br>
	<div style="width:100%;">
		<table align="left" cellspacing="0" width="915"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center" style="font-size:10px">
                <th width="30" style="word-wrap: break-word;word-break: break-all;">SL</th>
                <th width="50" style="word-wrap: break-word;word-break: break-all;">Sys. Challan</th>
                <th width="50" style="word-wrap: break-word;word-break: break-all;">Rec. Challan</th>
                <th width="55" style="word-wrap: break-word;word-break: break-all;">D. Date</th>
                <th width="60" style="word-wrap: break-word;word-break: break-all;">Order</th> 
                <th width="60" style="word-wrap: break-word;word-break: break-all;">Buyer</th>
                <th width="60" style="word-wrap: break-word;word-break: break-all;">Style</th>
                <th width="25" style="word-wrap: break-word;word-break: break-all;">Job</th>
                <th width="25" style="word-wrap: break-word;word-break: break-all;">Year</th>
                <th width="100" style="word-wrap: break-word;word-break: break-all;">Fabric Description</th>
                <th width="50" style="word-wrap: break-word;word-break: break-all;">Collar Cuff Measurement</th>
                <th width="25" style="word-wrap: break-word;word-break: break-all;">Roll</th>
                <th width="55" style="word-wrap: break-word;word-break: break-all;">D. Qty (W)</th>
                <th width="55" style="word-wrap: break-word;word-break: break-all;">D. Qty (P)</th>
                <th width="25" style="word-wrap: break-word;word-break: break-all;">UOM</th>
                <th width="30" style="word-wrap: break-word;word-break: break-all;">Rate</th>
                <th width="30" style="word-wrap: break-word;word-break: break-all;">Amount</th>
                <th width="45" style="word-wrap: break-word;word-break: break-all;">Currency</th>
                <th> Remarks</th>
            </thead>
		 <?
		 if($db_type==0) $job_year="YEAR(a.insert_date) as year";
		 else  $job_year="to_char(a.insert_date,'YYYY') as year";
		 	
			if($dataArray[0][csf('party_source')]==2)
			{
				$order_array=array();
				 $order_sql="select a.job_no_prefix_num,b.id,$job_year, b.order_no, b.order_uom, b.cust_buyer, b.cust_style_ref from subcon_ord_dtls b,subcon_ord_mst a where b.job_no_mst=a.subcon_job and b.status_active=1 and b.is_deleted=0";
				$order_sql_result =sql_select($order_sql);
				foreach($order_sql_result as $row)
				{
					$order_array[$row[csf('id')]]['po_number']=$row[csf('order_no')];
					$order_array[$row[csf('id')]]['buyer_name']=$row[csf('cust_buyer')];
					$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('cust_style_ref')];
					$order_array[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
					$order_array[$row[csf('id')]]['year']=$row[csf('year')];
				}
				unset($order_sql_result);
				$const_comp_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
				$collar_cuff_arr=return_library_array( "select id,collar_cuff from subcon_delivery_dtls",'id','collar_cuff');
			}
			else if($dataArray[0][csf('party_source')]==1)
			{
				$order_array=array();
				$job_sql="select a.job_no, $job_year, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.total_set_qnty as ratio, b.id, b.po_number, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active!=0 and a.is_deleted=0 and  b.status_active!=0 and b.is_deleted=0";// and a.company_name=$data[0]
				$job_sql_result =sql_select($job_sql);
				foreach($job_sql_result as $row)
				{
					$order_array[$row[csf('id')]]['buyer_name']=$party_library[$row[csf('buyer_name')]];
					$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
					$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
					$order_array[$row[csf('id')]]['job']=$row[csf('job_no')];
					$order_array[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
					$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
					$order_array[$row[csf('id')]]['ratio']=$row[csf('ratio')];
					$order_array[$row[csf('id')]]['year']=$row[csf('year')];
				}
				unset($job_sql_result);
				$const_comp_arr=return_library_array( "select id,product_name_details from product_details_master",'id','product_name_details');
				$recChallan_arr=array();
				$rec_challan_arr=return_library_array( "select id,challan_no from inv_receive_master",'id','challan_no');
				//echo "select recv_number_prefix_num, receive_date, challan_no from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]' and location_id='".$dataArray[0][csf('location_id')]."' and entry_form=22 and status_active=1 and is_deleted=0 and recv_number_prefix_num=30";
				$rec_challa_sql=sql_select("select recv_number_prefix_num, receive_date, challan_no from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]' and location_id='".$dataArray[0][csf('location_id')]."' and entry_form in (2,22)  and status_active=1 and is_deleted=0"); //and a.company_id='$data[3]' and a.knitting_company='$data[0]' and a.location_id='$data[1]'
				foreach($rec_challa_sql as $row)
				{
					$recChallan_arr[$row[csf('recv_number_prefix_num')]][change_date_format($row[csf('receive_date')])]=$row[csf('challan_no')];
				}
			}
			
			if($dataArray[0][csf('bill_for')]==3)
			{
				$buyer_id_arr=array();
				$sql_non_booking=sql_select("select recv_number_prefix_num, receive_date, buyer_id from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]' and location_id='".$dataArray[0][csf('location_id')]."' and entry_form=22  and status_active=1 and is_deleted=0 group by recv_number_prefix_num, receive_date, buyer_id");
				foreach($sql_non_booking as $row)
				{
					$buyer_id_arr[$row[csf('recv_number_prefix_num')]][change_date_format($row[csf('receive_date')])]=$party_library[$row[csf('buyer_id')]];
				}
			}
			//var_dump($recChallan_arr);
     		$i=1;
			$mst_id=$dataArray[0][csf('id')];
			$po_id="";
			$sql_result =sql_select("select id, delivery_id, delivery_date, challan_no, order_id, item_id, uom, packing_qnty, delivery_qty, delivery_qtypcs, rate, amount, remarks, currency_id, process_id from subcon_inbound_bill_dtls  where mst_id='$mst_id' and process_id='2' and status_active=1 and is_deleted=0 order by challan_no ASC"); 
			//echo "select id, delivery_id, delivery_date, challan_no, order_id, item_id, uom, packing_qnty, delivery_qty, delivery_qtypcs, rate, amount, remarks, currency_id, process_id from subcon_inbound_bill_dtls  where mst_id='$mst_id' and process_id='2' and status_active=1 and is_deleted=0 order by challan_no ASC";
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($po_id=="") $po_id=$row[csf('order_id')]; else $po_id.=','.$row[csf('order_id')];
				$buyer_id_name="";
				if($dataArray[0][csf('bill_for')]==3)
				{
					$buyer_id_name=$buyer_id_arr[$row[csf('challan_no')]][change_date_format($row[csf('delivery_date')])];
				}
				else
				{
					$buyer_id_name=$order_array[$row[csf('order_id')]]['buyer_name'];
				}
				//echo $row[csf('order_id')];
				$rec_challan="";
				if($row[csf('delivery_id')]!='' || $row[csf('delivery_id')]!=0) $rec_challan=$rec_challan_arr[$row[csf('delivery_id')]];
				else $rec_challan=$recChallan_arr[$row[csf('challan_no')]][change_date_format($row[csf('delivery_date')])];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:10px"> 
                    <td width="30" style="word-wrap: break-word;word-break: break-all;"><? echo $i; ?></td>
                    <td width="50" style="word-wrap: break-word;word-break: break-all;"><p><? echo $row[csf('challan_no')]; ?></p></td>
                    <td width="50" style="word-wrap: break-word;word-break: break-all;"><? echo $rec_challan; ?></td>
                    <td width="55" style="word-wrap: break-word;word-break: break-all;"><p><? echo change_date_format($row[csf('delivery_date')]); ?></p></td>
                    <td width="60" style="word-wrap: break-word;word-break: break-all;"><? echo $order_array[$row[csf('order_id')]]['po_number']; ?></td>
                    <td width="60" style="word-wrap: break-word;word-break: break-all;"><? echo $buyer_id_name; ?></td>
                    <td width="60" style="word-wrap: break-word;word-break: break-all;"><? echo $order_array[$row[csf('order_id')]]['style_ref_no']; ?></td>
                    <td width="25" style="word-wrap: break-word;word-break: break-all;" align="center"><? echo $order_array[$row[csf('order_id')]]['job_no_prefix_num']; ?></td>
                     <td width="25" style="word-wrap: break-word;word-break: break-all;" align="center"><? echo $order_array[$row[csf('order_id')]]['year']; ?></td>
                    <td width="100" style="word-wrap: break-word;word-break: break-all;"> <? echo $const_comp_arr[$row[csf('item_id')]]; ?></td>
                    <td width="50" style="word-wrap: break-word;word-break: break-all;"> <? echo $collar_cuff_arr[$row[csf('delivery_id')]]; ?></td>
                    <td width="25" style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo $row[csf('packing_qnty')]; $tot_packing_qty+=$row[csf('packing_qnty')]; ?>&nbsp;</p></td>
                    <td width="55" style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo number_format($row[csf('delivery_qty')],2,'.',''); $tot_delivery_qty+=$row[csf('delivery_qty')]; ?>&nbsp;</p></td>
                    <td width="55" style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo number_format($row[csf('delivery_qtypcs')],2,'.',''); $tot_delivery_qtypcs+=$row[csf('delivery_qtypcs')]; ?>&nbsp;</p></td>
                    <td width="25" style="word-wrap: break-word;word-break: break-all;"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
                    <td width="30" style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo number_format($row[csf('rate')],2,'.',''); ?>&nbsp;</p></td>
                    <td width="30" style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo number_format($row[csf('amount')],0,'.','');  $total_amount += $row[csf('amount')]; ?>&nbsp;</p></td>

                    <td width="45" style="word-wrap: break-word;word-break: break-all;" align="center"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>

                    <td ><? echo $row[csf('remarks')]; ?> </td>
                    <? 
					$carrency_id=$row['currency_id'];
					if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}
				   ?>
                </tr>
                <?
                $i++;
			}
			?>
        	<tr style="font-size:12px"> 
                <td align="right" colspan="11"><strong>Total</strong></td>
                <td align="right"><? echo $tot_packing_qty; ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_delivery_qty,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_delivery_qtypcs,2,'.',''); ?>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><? echo $format_total_amount=number_format($total_amount,0,'.',''); ?>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
			<tr style="font-size:12px"> 
                <td align="right" colspan="16"><strong>Upcharge</strong></td>
                <td align="right">
					<?
					echo $dataArray[0][csf('upcharge')]; 
					?>
					&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
			<tr style="font-size:12px"> 
                <td align="right" colspan="16"><strong>Discount</strong></td>
                <td align="right"><? echo $dataArray[0][csf('discount')]; ?>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
			<tr style="font-size:12px"> 
                <td align="right" colspan="16" style="padding-bottom: 15px"><strong>Net total</strong></td>
                <td align="right">
					<? 
						$upcharge=$dataArray[0][csf('upcharge')];
						$discount=$dataArray[0][csf('discount')];
						$tot_up=$total_amount+$upcharge;
						$net_total=$tot_up-$discount;
						echo $format_total_amount=number_format($net_total,0,'.',''); 
					?>
				&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
           <tr>
               <td colspan="18" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
           </tr>
        </table>
        <?
			$bill_no=$dataArray[0][csf('bill_no')];
			$sql_terms="Select id,terms from subcon_terms_condition where entry_form=1 and bill_no='$bill_no' ";
			$result_sql_terms =sql_select($sql_terms);

			$i=1;
			if(count($result_sql_terms)>0)
			{
				?>
                <table width="915" align="left" > 
                    <tr><td colspan="2">&nbsp;</td> </tr>
                    <tr><td colspan="2" align="center"><b>TERMS & CONDITION</b></td></tr>
					<?
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
       
        <? if($data[4]==1) 
		{ 
			if($dataArray[0][csf('bill_for')]!=3)
			{
			?>
			<table align="left" cellspacing="0" width="915"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="120" style="word-wrap: break-word;word-break: break-all;">Order No</th>
					<th width="110" style="word-wrap: break-word;word-break: break-all;">Buyer Name</th>
					<th width="100" style="word-wrap: break-word;word-break: break-all;">Grey Required (KG)</th>
					<th width="100" style="word-wrap: break-word;word-break: break-all;">Charge Required (USD)</th>
					<th width="100" style="word-wrap: break-word;word-break: break-all;">Bill Qty (KG)</th> 
					<th width="100" style="word-wrap: break-word;word-break: break-all;">Bill Amount (USD)</th>
					<th width="100" style="word-wrap: break-word;word-break: break-all;">Balance Qty (KG)</th>
					<th width="">Balance Amount (USD)</th>
				</thead>
				<tbody>
				<?
				$grey_req_arr=array();
				$grey_req_sql="select po_break_down_id, sum(requirment) as grey_req from wo_pre_cos_fab_co_avg_con_dtls group by po_break_down_id";
				$grey_req_sql_result =sql_select($grey_req_sql);
				foreach($grey_req_sql_result as $row)
				{
					$grey_req_arr[$row[csf('po_break_down_id')]]=$row[csf('grey_req')];
				}
				
				$charge_req_arr=array(); 
				$charge_req_sql="select job_no, sum(amount) as charge_req from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0 group by job_no";
				$charge_req_sql_result =sql_select($charge_req_sql);
				foreach($charge_req_sql_result as $row)
				{
					$charge_req_arr[$row[csf('job_no')]]=$row[csf('charge_req')];
				}
				
				$bill_arr=array(); 
				$bill_sql="select b.order_id, sum(b.delivery_qty) as bill_qty, sum(b.amount) as bill_amount from  subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.party_source=1 and b.process_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id";
				$bill_sql_result =sql_select($bill_sql);
				foreach($bill_sql_result as $row)
				{
					$bill_arr[$row[csf('order_id')]]['bill_qty']=$row[csf('bill_qty')];
					$bill_arr[$row[csf('order_id')]]['bill_amount']=$row[csf('bill_amount')];
				}
				
				$currency_rate=set_conversion_rate( 2, $dataArray[0][csf('bill_date')] );
				$costingper_id_arr=return_library_array( "select job_no,costing_per_id from wo_pre_cost_dtls",'job_no','costing_per_id');
				
				$ex_po=array_unique(explode(",",$po_id)); $k=1;
				foreach($ex_po as $po_id)
				{
					if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$po_quantity=$order_array[$po_id]['po_quantity'];
					
					$costing_per_id=$costingper_id_arr[$order_array[$po_id]['job']];
					$dzn_qnty=0;
					if($costing_per_id==1) $dzn_qnty=12;
					else if($costing_per_id==3) $dzn_qnty=12*2;
					else if($costing_per_id==4) $dzn_qnty=12*3;
					else if($costing_per_id==5) $dzn_qnty=12*4;
					else $dzn_qnty=1;
					$dzn_qnty_req=$dzn_qnty*$order_array[$po_id]['ratio'];
					$grey_req=($po_quantity/$dzn_qnty_req)*$grey_req_arr[$po_id];
					$charge_req=($po_quantity/$dzn_qnty_req)*$charge_req_arr[$order_array[$po_id]['job']]*$currency_rate;
					
					$bill_qty=$bill_arr[$po_id]['bill_qty'];
					$bill_amount=$bill_arr[$po_id]['bill_amount']*$currency_rate;
					$balance_qty=$grey_req-$bill_qty;
					$balance_amount=$charge_req-$bill_amount;
				?>
					<tr bgcolor="<? echo $bgcolor; ?>"> 
						<td width="120" style="word-wrap: break-word;word-break: break-all;" > <? echo $order_array[$po_id]['po_number']; ?> </td>
						<td width="110" style="word-wrap: break-word;word-break: break-all;" ><p><? echo $order_array[$po_id]['buyer_name']; ?></p></td>
						
						<td width="100" style="word-wrap: break-word;word-break: break-all;"  align="right"><p><? echo number_format($grey_req,2,'.',''); ?></p></td>
						<td width="100" style="word-wrap: break-word;word-break: break-all;"  align="right"><p><? echo number_format($charge_req,2,'.',''); ?></p></td>
						
						<td width="100"  style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo number_format($bill_qty,2,'.',''); ?></p></td>
						<td width="100" style="word-wrap: break-word;word-break: break-all;"  align="right"><p><? echo number_format($bill_amount,2,'.',''); ?></p></td>
						
						<td width="100" style="word-wrap: break-word;word-break: break-all;"  align="right"><p><? echo number_format($balance_qty,2,'.',''); ?></p></td>
						<td  align="right"><p><? echo number_format($balance_amount,2,'.',''); ?></p></td>
					</tr>
				<?
				}
				?>
				
				</tbody>
			</table>
        <? } 
		} ?>
        
        <br>
		 <? echo signature_table(220, $data[0], "930px"); ?>
   </div>
   </div>
	<?
    exit();
}
if($action=="knitting_bill_print_7") 
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r($data);
	//echo $data[5];die;
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$party_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name");
	$yarn_desc_arr=return_library_array( "select id,yarn_description from lib_subcon_charge",'id','yarn_description');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	
	$sql_mst="Select id, bill_no, bill_date, party_id, party_source, location_id, attention, bill_for,party_location_id from subcon_inbound_bill_mst where company_id=$data[0] and id='$data[1]' and bill_no='$data[2]' and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql_mst);
	$k=0;
	$copy_no=array(1,2,3);
	foreach($copy_no as $cid)
	{
		if ($cid!=1) $margin_cond="style='margin-top: 70px;'";
		else $margin_cond='';
		$k++;
		
     ?>

        <div style="width:930px;margin:0px auto;">
		<table width="100%" cellpadding="0" cellspacing="0" >
			<tr>
			
				<td width="70" align="right"> 
					<img  src='../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='70' width='200' />
				</td>
				<td>
					<table width="700" cellspacing="0" align="center">
					<tr>
							<td align="right" style="font-size:18px"><strong><? 
							if($k==1){
							echo "<b><h2>Office Copy</h2></b>";
							}
							else if($k==2){
							echo "<b><h2>Customer Copy</h2></b>";
							}
							else if($k==3){
							echo "<b><h2>Accounts Copy</h2></b>";
							}
							?></strong></td>
								</tr>
						<tr>
							<td align="center" style="font-size:20px"><strong ><? echo $company_library[$data[0]]; ?></strong>
							
						</td>
							
						</tr>
						<tr>
							<td align="center"  style="font-size:16px"><strong>Unit : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
						</tr>
						<tr class="form_caption">
							<td align="center" style="font-size:14px"><? echo show_company($data[0],'',''); ?></td>  
						</tr>
						<tr>
							<td align="center" style="font-size:18px"><strong><? echo $data[3]; ?></strong></td>
						</tr>
								
					</table>
				</td>
			</tr>
		</table>
		<table width="915" cellspacing="0" align="" border="0">
				<tr>
					<td width="130"><strong>Bill No :</strong></td> <td width="175"><? echo $dataArray[0][csf('bill_no')]; ?></td>
					<td width="130"><strong>Bill Date: </strong></td><td width="175px"> <? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
					<td width="130"><strong>Source :</strong></td> <td width="175"><? echo $knitting_source[$dataArray[0][csf('party_source')]]; ?></td>
				</tr>
				<tr>
					<?
						if($dataArray[0][csf('attention')]!='')  $attention='('.$dataArray[0][csf('attention')].')'; else $attention='';
							
						if($dataArray[0][csf('party_source')]==2)
						{
							$party_add=$dataArray[0][csf('party_id')];
							$nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add"); 
							foreach ($nameArray as $result)
							{ 
								$address="";
								if($result!="") $address=$result[csf('address_1')];
							}
							$party_name=$party_library[$dataArray[0][csf('party_id')]].' :</br> Address :- '.$address.' '.$attention;
							$party_location='';
						}
						else
						{
							$party_name=$company_library[$dataArray[0][csf('party_id')]].' '.$attention;
							$party_location=$location_arr[$dataArray[0][csf('party_location_id')]];
						}
					?>
					<td  width="150"  valign="top"><strong>Party Name: </strong></td><td width="200"  valign="top"><p> <? echo $party_name; ?></p></td>
					<td width="180"  valign="top"><strong>Party Location: </strong></td><td  width="200"  valign="top"> <? echo $party_location; ?></td>
					<td width="150"  valign="top"><strong>Bill For : </strong></td><td width="200"  valign="top"> <? echo $bill_for[$dataArray[0][csf('bill_for')]]; ?></td>
				</tr>
			</table>
			<br>
		<div style="width:100%;">
			<table align="left" cellspacing="0" width="915"  border="1" rules="all" class="rpt_table" >
				    <thead bgcolor="#dddddd" align="center" style="font-size:10px">
					<th width="30" style="word-wrap: break-word;word-break: break-all;">SL</th>
					<th width="50" style="word-wrap: break-word;word-break: break-all;">Sys. Challan</th>
					<th width="50" style="word-wrap: break-word;word-break: break-all;">Rec. Challan</th>
					<th width="55" style="word-wrap: break-word;word-break: break-all;">D. Date</th>
					<th width="60" style="word-wrap: break-word;word-break: break-all;">Order</th> 
					<th width="60" style="word-wrap: break-word;word-break: break-all;">Buyer</th>
					<th width="60" style="word-wrap: break-word;word-break: break-all;">Style</th>
					<th width="25" style="word-wrap: break-word;word-break: break-all;">Job</th>
					<th width="25" style="word-wrap: break-word;word-break: break-all;">Year</th>
					<th width="100" style="word-wrap: break-word;word-break: break-all;">Fabric Description</th>
					<th width="50" style="word-wrap: break-word;word-break: break-all;">Collar Cuff Measurement</th>
					<th width="25" style="word-wrap: break-word;word-break: break-all;">Roll</th>
					<th width="55" style="word-wrap: break-word;word-break: break-all;">D. Qty (W)</th>
					<th width="55" style="word-wrap: break-word;word-break: break-all;">D. Qty (P)</th>
					<th width="25" style="word-wrap: break-word;word-break: break-all;">UOM</th>
					<th width="30" style="word-wrap: break-word;word-break: break-all;">Rate</th>
					<th width="30" style="word-wrap: break-word;word-break: break-all;">Amount</th>
					<th width="45" style="word-wrap: break-word;word-break: break-all;">Currency</th>
					<th> Remarks</th>
				</thead>
			<?
			if($db_type==0) $job_year="YEAR(a.insert_date) as year";
			else  $job_year="to_char(a.insert_date,'YYYY') as year";
				
				if($dataArray[0][csf('party_source')]==2)
				{
					$order_array=array();
					$order_sql="select a.job_no_prefix_num,b.id,$job_year, b.order_no, b.order_uom, b.cust_buyer, b.cust_style_ref from subcon_ord_dtls b,subcon_ord_mst a where b.job_no_mst=a.subcon_job and b.status_active=1 and b.is_deleted=0";
					$order_sql_result =sql_select($order_sql);
					foreach($order_sql_result as $row)
					{
						$order_array[$row[csf('id')]]['po_number']=$row[csf('order_no')];
						$order_array[$row[csf('id')]]['buyer_name']=$row[csf('cust_buyer')];
						$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('cust_style_ref')];
						$order_array[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
						$order_array[$row[csf('id')]]['year']=$row[csf('year')];
					}
					unset($order_sql_result);
					$const_comp_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
					$collar_cuff_arr=return_library_array( "select id,collar_cuff from subcon_delivery_dtls",'id','collar_cuff');
				}
				else if($dataArray[0][csf('party_source')]==1)
				{
					$order_array=array();
					$job_sql="select a.job_no, $job_year, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.total_set_qnty as ratio, b.id, b.po_number, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active!=0 and a.is_deleted=0 and  b.status_active!=0 and b.is_deleted=0";// and a.company_name=$data[0]
					$job_sql_result =sql_select($job_sql);
					foreach($job_sql_result as $row)
					{
						$order_array[$row[csf('id')]]['buyer_name']=$party_library[$row[csf('buyer_name')]];
						$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
						$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
						$order_array[$row[csf('id')]]['job']=$row[csf('job_no')];
						$order_array[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
						$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
						$order_array[$row[csf('id')]]['ratio']=$row[csf('ratio')];
						$order_array[$row[csf('id')]]['year']=$row[csf('year')];
					}
					unset($job_sql_result);
					$const_comp_arr=return_library_array( "select id,product_name_details from product_details_master",'id','product_name_details');
					$recChallan_arr=array();
					$rec_challan_arr=return_library_array( "select id,challan_no from inv_receive_master",'id','challan_no');
					//echo "select recv_number_prefix_num, receive_date, challan_no from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]' and location_id='".$dataArray[0][csf('location_id')]."' and entry_form=22 and status_active=1 and is_deleted=0 and recv_number_prefix_num=30";
					$rec_challa_sql=sql_select("select recv_number_prefix_num, receive_date, challan_no from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]' and location_id='".$dataArray[0][csf('location_id')]."' and entry_form in (2,22)  and status_active=1 and is_deleted=0"); //and a.company_id='$data[3]' and a.knitting_company='$data[0]' and a.location_id='$data[1]'
					foreach($rec_challa_sql as $row)
					{
						$recChallan_arr[$row[csf('recv_number_prefix_num')]][change_date_format($row[csf('receive_date')])]=$row[csf('challan_no')];
					}
				}
				
				if($dataArray[0][csf('bill_for')]==3)
				{
					$buyer_id_arr=array();
					$sql_non_booking=sql_select("select recv_number_prefix_num, receive_date, buyer_id from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]' and location_id='".$dataArray[0][csf('location_id')]."' and entry_form=22  and status_active=1 and is_deleted=0 group by recv_number_prefix_num, receive_date, buyer_id");
					foreach($sql_non_booking as $row)
					{
						$buyer_id_arr[$row[csf('recv_number_prefix_num')]][change_date_format($row[csf('receive_date')])]=$party_library[$row[csf('buyer_id')]];
					}
				}
				//var_dump($recChallan_arr);
				$i=1;
				$mst_id=$dataArray[0][csf('id')];
				$po_id="";
				$tot_packing_qty="";
				$tot_delivery_qty="";
				$tot_delivery_qtypcs="";
				$total_amount="";

				$sql_result =sql_select("select id, delivery_id, delivery_date, challan_no, order_id, item_id, uom, packing_qnty, delivery_qty, delivery_qtypcs, rate, amount, remarks, currency_id, process_id from subcon_inbound_bill_dtls  where mst_id='$mst_id' and process_id='2' and status_active=1 and is_deleted=0 order by challan_no ASC"); 
				//echo "select id, delivery_id, delivery_date, challan_no, order_id, item_id, uom, packing_qnty, delivery_qty, delivery_qtypcs, rate, amount, remarks, currency_id, process_id from subcon_inbound_bill_dtls  where mst_id='$mst_id' and process_id='2' and status_active=1 and is_deleted=0 order by challan_no ASC";
				foreach($sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if($po_id=="") $po_id=$row[csf('order_id')]; else $po_id.=','.$row[csf('order_id')];
					$buyer_id_name="";
					if($dataArray[0][csf('bill_for')]==3)
					{
						$buyer_id_name=$buyer_id_arr[$row[csf('challan_no')]][change_date_format($row[csf('delivery_date')])];
					}
					else
					{
						$buyer_id_name=$order_array[$row[csf('order_id')]]['buyer_name'];
					}
					//echo $row[csf('order_id')];
					$rec_challan="";
					if($row[csf('delivery_id')]!='' || $row[csf('delivery_id')]!=0) $rec_challan=$rec_challan_arr[$row[csf('delivery_id')]];
					else $rec_challan=$recChallan_arr[$row[csf('challan_no')]][change_date_format($row[csf('delivery_date')])];
				?>
					    <tr bgcolor="<? echo $bgcolor; ?>" style="font-size:10px"> 
						<td width="30" style="word-wrap: break-word;word-break: break-all;"><? echo $i; ?></td>
						<td width="50" style="word-wrap: break-word;word-break: break-all;"><p><? echo $row[csf('challan_no')]; ?></p></td>
						<td width="50" style="word-wrap: break-word;word-break: break-all;"><? echo $rec_challan; ?></td>
						<td width="55" style="word-wrap: break-word;word-break: break-all;"><p><? echo change_date_format($row[csf('delivery_date')]); ?></p></td>
						<td width="60" style="word-wrap: break-word;word-break: break-all;"><? echo $order_array[$row[csf('order_id')]]['po_number']; ?></td>
						<td width="60" style="word-wrap: break-word;word-break: break-all;"><? echo $buyer_id_name; ?></td>
						<td width="60" style="word-wrap: break-word;word-break: break-all;"><? echo $order_array[$row[csf('order_id')]]['style_ref_no']; ?></td>
						<td width="25" style="word-wrap: break-word;word-break: break-all;" align="center"><? echo $order_array[$row[csf('order_id')]]['job_no_prefix_num']; ?></td>
						<td width="25" style="word-wrap: break-word;word-break: break-all;" align="center"><? echo $order_array[$row[csf('order_id')]]['year']; ?></td>
						<td width="100" style="word-wrap: break-word;word-break: break-all;"> <? echo $const_comp_arr[$row[csf('item_id')]]; ?></td>
						<td width="50" style="word-wrap: break-word;word-break: break-all;"> <? echo $collar_cuff_arr[$row[csf('delivery_id')]]; ?></td>
						<td width="25" style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo $row[csf('packing_qnty')]; $tot_packing_qty+=$row[csf('packing_qnty')]; ?>&nbsp;</p></td>
						<td width="55" style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo number_format($row[csf('delivery_qty')],2,'.',''); $tot_delivery_qty+=$row[csf('delivery_qty')]; ?>&nbsp;</p></td>
						<td width="55" style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo number_format($row[csf('delivery_qtypcs')],2,'.',''); $tot_delivery_qtypcs+=$row[csf('delivery_qtypcs')]; ?>&nbsp;</p></td>
						<td width="25" style="word-wrap: break-word;word-break: break-all;"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
						<td width="30" style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo number_format($row[csf('rate')],2,'.',''); ?>&nbsp;</p></td>
						<td width="30" style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo number_format($row[csf('amount')],2,'.','');  $total_amount += $row[csf('amount')]; ?>&nbsp;</p></td>

						<td width="45" style="word-wrap: break-word;word-break: break-all;" align="center"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>

						<td ><? echo $row[csf('remarks')]; ?> </td>
						<? 
						$carrency_id=$row['currency_id'];
						if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}
					?>
					</tr>
					<?
					$i++;
				}
				?>
				<tr style="font-size:12px"> 
					<td align="right" colspan="11"><strong>Total</strong></td>
					<td align="right"><? echo $tot_packing_qty; ?>&nbsp;</td>
					<td align="right"><? echo number_format($tot_delivery_qty,2,'.',''); ?>&nbsp;</td>
					<td align="right"><? echo number_format($tot_delivery_qtypcs,2,'.',''); ?>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align="right"><? echo $format_total_amount=number_format($total_amount,2,'.',''); ?>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			<tr>
				<td colspan="18" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
			</tr>
			</table>
			<?
				$bill_no=$dataArray[0][csf('bill_no')];
				$sql_terms="Select id,terms from subcon_terms_condition where entry_form=1 and bill_no='$bill_no' ";
				$result_sql_terms =sql_select($sql_terms);

				$i=1;
				if(count($result_sql_terms)>0)
				{
					?>
					<table width="915" align="left" > 
						<tr><td colspan="2">&nbsp;</td> </tr>
						<tr><td colspan="2" align="center"><b>TERMS & CONDITION</b></td></tr>
						<?
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
		
			<? if($data[4]==1) 
			{ 
				if($dataArray[0][csf('bill_for')]!=3)
				{
				?>
				<table align="left" cellspacing="0" width="915"  border="1" rules="all" class="rpt_table" >
					<thead bgcolor="#dddddd" align="center">
						<th width="120" style="word-wrap: break-word;word-break: break-all;">Order No</th>
						<th width="110" style="word-wrap: break-word;word-break: break-all;">Buyer Name</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">Grey Required (KG)</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">Charge Required (USD)</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">Bill Qty (KG)</th> 
						<th width="100" style="word-wrap: break-word;word-break: break-all;">Bill Amount (USD)</th>
						<th width="100" style="word-wrap: break-word;word-break: break-all;">Balance Qty (KG)</th>
						<th width="">Balance Amount (USD)</th>
					</thead>
					<tbody>
					<?
					$grey_req_arr=array();
					$grey_req_sql="select po_break_down_id, sum(requirment) as grey_req from wo_pre_cos_fab_co_avg_con_dtls group by po_break_down_id";
					$grey_req_sql_result =sql_select($grey_req_sql);
					foreach($grey_req_sql_result as $row)
					{
						$grey_req_arr[$row[csf('po_break_down_id')]]=$row[csf('grey_req')];
					}
					
					$charge_req_arr=array(); 
					$charge_req_sql="select job_no, sum(amount) as charge_req from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0 group by job_no";
					$charge_req_sql_result =sql_select($charge_req_sql);
					foreach($charge_req_sql_result as $row)
					{
						$charge_req_arr[$row[csf('job_no')]]=$row[csf('charge_req')];
					}
					
					$bill_arr=array(); 
					$bill_sql="select b.order_id, sum(b.delivery_qty) as bill_qty, sum(b.amount) as bill_amount from  subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.party_source=1 and b.process_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id";
					$bill_sql_result =sql_select($bill_sql);
					foreach($bill_sql_result as $row)
					{
						$bill_arr[$row[csf('order_id')]]['bill_qty']=$row[csf('bill_qty')];
						$bill_arr[$row[csf('order_id')]]['bill_amount']=$row[csf('bill_amount')];
					}
					
					$currency_rate=set_conversion_rate( 2, $dataArray[0][csf('bill_date')] );
					$costingper_id_arr=return_library_array( "select job_no,costing_per_id from wo_pre_cost_dtls",'job_no','costing_per_id');
					
					$ex_po=array_unique(explode(",",$po_id)); $k=1;
					foreach($ex_po as $po_id)
					{
						if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$po_quantity=$order_array[$po_id]['po_quantity'];
						
						$costing_per_id=$costingper_id_arr[$order_array[$po_id]['job']];
						$dzn_qnty=0;
						if($costing_per_id==1) $dzn_qnty=12;
						else if($costing_per_id==3) $dzn_qnty=12*2;
						else if($costing_per_id==4) $dzn_qnty=12*3;
						else if($costing_per_id==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;
						$dzn_qnty_req=$dzn_qnty*$order_array[$po_id]['ratio'];
						$grey_req=($po_quantity/$dzn_qnty_req)*$grey_req_arr[$po_id];
						$charge_req=($po_quantity/$dzn_qnty_req)*$charge_req_arr[$order_array[$po_id]['job']]*$currency_rate;
						
						$bill_qty=$bill_arr[$po_id]['bill_qty'];
						$bill_amount=$bill_arr[$po_id]['bill_amount']*$currency_rate;
						$balance_qty=$grey_req-$bill_qty;
						$balance_amount=$charge_req-$bill_amount;
					?>
						<tr bgcolor="<? echo $bgcolor; ?>"> 
							<td width="120" style="word-wrap: break-word;word-break: break-all;" > <? echo $order_array[$po_id]['po_number']; ?> </td>
							<td width="110" style="word-wrap: break-word;word-break: break-all;" ><p><? echo $order_array[$po_id]['buyer_name']; ?></p></td>
							
							<td width="100" style="word-wrap: break-word;word-break: break-all;"  align="right"><p><? echo number_format($grey_req,2,'.',''); ?></p></td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;"  align="right"><p><? echo number_format($charge_req,2,'.',''); ?></p></td>
							
							<td width="100"  style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo number_format($bill_qty,2,'.',''); ?></p></td>
							<td width="100" style="word-wrap: break-word;word-break: break-all;"  align="right"><p><? echo number_format($bill_amount,2,'.',''); ?></p></td>
							
							<td width="100" style="word-wrap: break-word;word-break: break-all;"  align="right"><p><? echo number_format($balance_qty,2,'.',''); ?></p></td>
							<td  align="right"><p><? echo number_format($balance_amount,2,'.',''); ?></p></td>
						</tr>
					<?
					}
					?>
					
					</tbody>
				</table>
			<? } 
			} ?>
			
			<br>
			<? echo signature_table(220, $data[0], "930px"); ?>
		</div>
		</div>
		<?
	}
    exit();
}


if($action=="knitting_bill_print_5") 
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$party_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name");
	$yarn_desc_arr=return_library_array( "select id,yarn_description from lib_subcon_charge",'id','yarn_description');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$bodyPartNameArr = return_library_array( "select id, BODY_PART_FULL_NAME from lib_body_part",'id','BODY_PART_FULL_NAME');
	
	$sql_mst="Select id, bill_no, bill_date, party_id, party_source, location_id, attention, bill_for,party_location_id from subcon_inbound_bill_mst where company_id=$data[0] and id='$data[1]' and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql_mst);
	?>
    <div style="width:930px;margin:0px auto;">
	<table width="100%" cellpadding="0" cellspacing="0" >
	    <tr>
	    	<td width="70" align="left"> 
	        	<img style="margin-bottom: -200px;"  src='../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='70' width='200' />
	        </td>
	    </tr>
	</table>
    <table width="100%" cellpadding="0" cellspacing="0" >
        <tr>
            <td>
                <table width="800" cellspacing="0" align="center">
                    <tr>
                    	<td align="center" style="font-size:20px"><strong ><? echo $company_library[$data[0]]; ?></strong></td>
                    </tr>
                    <tr>
                        <td align="center"  style="font-size:16px"><strong>Unit : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td align="center" style="font-size:14px"><? echo show_company($data[0],'',''); ?></td>  
                    </tr>
                    <tr>
                    	<td align="center" style="font-size:18px"><strong><? echo $data[3]; ?></strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
	<table width="915" cellspacing="0" align="" border="0">
        <tr>
            <td width="130"><strong>Bill No :</strong></td> <td width="175"><? echo $dataArray[0][csf('bill_no')]; ?></td>
            <td width="130"><strong>Bill Date: </strong></td><td width="175px"> <? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
            <td width="130"><strong>Source :</strong></td> <td width="175"><? echo $knitting_source[$dataArray[0][csf('party_source')]]; ?></td>
        </tr>
        <tr>
			<?
                if($dataArray[0][csf('attention')]!='')  $attention='('.$dataArray[0][csf('attention')].')'; else $attention='';
                    
                if($dataArray[0][csf('party_source')]==2)
                {
                    $party_add=$dataArray[0][csf('party_id')];
                    $nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add"); 
                    foreach ($nameArray as $result)
                    { 
                        $address="";
                        if($result!="") $address=$result[csf('address_1')];
                    }
					$party_name=$party_library[$dataArray[0][csf('party_id')]].' :</br> Address :- '.$address.' '.$attention;
					$party_location='';
                }
                else
                {
					$party_name=$company_library[$dataArray[0][csf('party_id')]].' '.$attention;
					$party_location=$location_arr[$dataArray[0][csf('party_location_id')]];
                }
            ?>
            <td  width="150"  valign="top"><strong>Party Name: </strong></td><td width="200"  valign="top"><p> <? echo $party_name; ?></p></td>
			<td width="180"  valign="top"><strong>Party Location: </strong></td><td  width="200"  valign="top"> <? echo $party_location; ?></td>
            <td width="150"  valign="top"><strong>Bill For : </strong></td><td width="200"  valign="top"> <? echo $bill_for[$dataArray[0][csf('bill_for')]]; ?></td>
        </tr>
 	</table>
 	<br>
	<div style="width:100%;">
		<table align="left" cellspacing="0" width="945"  border="1" rules="all" class="rpt_table" >
			<thead bgcolor="#dddddd" align="center" style="font-size:10px">
                <th width="30" style="word-wrap: break-word;word-break: break-all;">SL</th>
                <th width="40" style="word-wrap: break-word;word-break: break-all;">Sys. Challan</th>
                <th width="40" style="word-wrap: break-word;word-break: break-all;">Rec. Challan</th>
                <th width="55" style="word-wrap: break-word;word-break: break-all;">D. Date</th>
                <th width="55" style="word-wrap: break-word;word-break: break-all;">Order</th> 
                <th width="55" style="word-wrap: break-word;word-break: break-all;">Buyer</th>
                <th width="55" style="word-wrap: break-word;word-break: break-all;">Style</th>
                <th width="25" style="word-wrap: break-word;word-break: break-all;">Job</th>
                <th width="25" style="word-wrap: break-word;word-break: break-all;">Year</th>
                <th width="60" style="word-wrap: break-word;word-break: break-all;">Body Part</th>
                <th width="100" style="word-wrap: break-word;word-break: break-all;">Fabric Description</th>
                <th width="55" style="word-wrap: break-word;word-break: break-all;">Collar Cuff Measurement</th>
                <th width="25" style="word-wrap: break-word;word-break: break-all;">Roll</th>
                <th width="55" style="word-wrap: break-word;word-break: break-all;">D. Qty (W)</th>
                <th width="55" style="word-wrap: break-word;word-break: break-all;">D. Qty (P)</th>
                <th width="25" style="word-wrap: break-word;word-break: break-all;">UOM</th>
                <th width="30" style="word-wrap: break-word;word-break: break-all;">Rate</th>
                <th width="30" style="word-wrap: break-word;word-break: break-all;">Amount</th>
                <th width="45" style="word-wrap: break-word;word-break: break-all;">Currency</th>
                <th> Remarks</th>
            </thead>
		 <?
		if($dataArray[0][csf('bill_for')]!=4){
			if($db_type==0) $job_year="YEAR(a.insert_date) as year";
			else  $job_year="to_char(a.insert_date,'YYYY') as year";
		 	
			if($dataArray[0][csf('party_source')]==2)
			{
				$order_array=array();
				 $order_sql="select a.job_no_prefix_num,b.id,$job_year, b.order_no, b.order_uom, b.cust_buyer, b.cust_style_ref from subcon_ord_dtls b,subcon_ord_mst a where b.job_no_mst=a.subcon_job and b.status_active=1 and b.is_deleted=0";
				$order_sql_result =sql_select($order_sql);
				foreach($order_sql_result as $row)
				{
					$order_array[$row[csf('id')]]['po_number']=$row[csf('order_no')];
					$order_array[$row[csf('id')]]['buyer_name']=$row[csf('cust_buyer')];
					$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('cust_style_ref')];
					$order_array[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
					$order_array[$row[csf('id')]]['year']=$row[csf('year')];
				}
				unset($order_sql_result);
				$const_comp_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
				$collar_cuff_arr=return_library_array( "select id,collar_cuff from subcon_delivery_dtls",'id','collar_cuff');
			}
			else if($dataArray[0][csf('party_source')]==1)
			{
				$order_array=array();
				$job_sql="select a.job_no, $job_year, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.total_set_qnty as ratio, b.id, b.po_number, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active!=0 and a.is_deleted=0 and  b.status_active!=0 and b.is_deleted=0";// and a.company_name=$data[0]
				$job_sql_result =sql_select($job_sql);
				foreach($job_sql_result as $row)
				{
					$order_array[$row[csf('id')]]['buyer_name']=$party_library[$row[csf('buyer_name')]];
					$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
					$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
					$order_array[$row[csf('id')]]['job']=$row[csf('job_no')];
					$order_array[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
					$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
					$order_array[$row[csf('id')]]['ratio']=$row[csf('ratio')];
					$order_array[$row[csf('id')]]['year']=$row[csf('year')];
				}
				unset($job_sql_result);
				$const_comp_arr=return_library_array( "select id,product_name_details from product_details_master",'id','product_name_details');
				$recChallan_arr=array();
				$rec_challan_arr=return_library_array( "select id,challan_no from inv_receive_master",'id','challan_no');
				//echo "select recv_number_prefix_num, receive_date, challan_no from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]' and location_id='".$dataArray[0][csf('location_id')]."' and entry_form=22 and status_active=1 and is_deleted=0 and recv_number_prefix_num=30";
				$rec_challa_sql=sql_select("select recv_number_prefix_num, receive_date, challan_no from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]' and location_id='".$dataArray[0][csf('location_id')]."' and entry_form in (2,22)  and status_active=1 and is_deleted=0"); //and a.company_id='$data[3]' and a.knitting_company='$data[0]' and a.location_id='$data[1]'
				foreach($rec_challa_sql as $row)
				{
					$recChallan_arr[$row[csf('recv_number_prefix_num')]][change_date_format($row[csf('receive_date')])]=$row[csf('challan_no')];
				}
			}
			
			if($dataArray[0][csf('bill_for')]==3)
			{
				$buyer_id_arr=array();
				$sql_non_booking=sql_select("select recv_number_prefix_num, receive_date, buyer_id from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]' and location_id='".$dataArray[0][csf('location_id')]."' and entry_form=22  and status_active=1 and is_deleted=0 group by recv_number_prefix_num, receive_date, buyer_id");
				foreach($sql_non_booking as $row)
				{
					$buyer_id_arr[$row[csf('recv_number_prefix_num')]][change_date_format($row[csf('receive_date')])]=$party_library[$row[csf('buyer_id')]];
				}
			}
			//var_dump($recChallan_arr);
     		$i=1;
			$mst_id=$dataArray[0][csf('id')];
			$po_id="";
			$sql_result =sql_select("select id, delivery_id, delivery_date, challan_no, order_id, item_id, uom, packing_qnty, delivery_qty, delivery_qtypcs, rate, amount, remarks, currency_id, process_id, body_part_id from subcon_inbound_bill_dtls  where mst_id='$mst_id' and process_id='2' and status_active=1 and is_deleted=0 order by challan_no ASC"); 
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($po_id=="") $po_id=$row[csf('order_id')]; else $po_id.=','.$row[csf('order_id')];
				$buyer_id_name="";
				if($dataArray[0][csf('bill_for')]==3)
				{
					$buyer_id_name=$buyer_id_arr[$row[csf('challan_no')]][change_date_format($row[csf('delivery_date')])];
				}
				else
				{
					$buyer_id_name=$order_array[$row[csf('order_id')]]['buyer_name'];
				}
				//echo $row[csf('order_id')];
				$rec_challan="";
				if($row[csf('delivery_id')]!='' || $row[csf('delivery_id')]!=0) $rec_challan=$rec_challan_arr[$row[csf('delivery_id')]];
				else $rec_challan=$recChallan_arr[$row[csf('challan_no')]][change_date_format($row[csf('delivery_date')])];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:10px"> 
                    <td width="30" style="word-wrap: break-word;word-break: break-all;"><? echo $i; ?></td>
                    <td width="50" style="word-wrap: break-word;word-break: break-all;"><p><? echo $row[csf('challan_no')]; ?></p></td>
                    <td width="50" style="word-wrap: break-word;word-break: break-all;"><? echo $rec_challan; ?></td>
                    <td width="55" style="word-wrap: break-word;word-break: break-all;"><p><? echo change_date_format($row[csf('delivery_date')]); ?></p></td>
                    <td width="55" style="word-wrap: break-word;word-break: break-all;"><? echo $order_array[$row[csf('order_id')]]['po_number']; ?></td>
                    <td width="55" style="word-wrap: break-word;word-break: break-all;"><? echo $buyer_id_name; ?></td>
                    <td width="55" style="word-wrap: break-word;word-break: break-all;"><? echo $order_array[$row[csf('order_id')]]['style_ref_no']; ?></td>
                    <td width="25" style="word-wrap: break-word;word-break: break-all;" align="center"><? echo $order_array[$row[csf('order_id')]]['job_no_prefix_num']; ?></td>
                     <td width="25" style="word-wrap: break-word;word-break: break-all;" align="center"><? echo $order_array[$row[csf('order_id')]]['year']; ?></td>
					 <td width="60" style="word-wrap: break-word;word-break: break-all;" align="center"><? echo $bodyPartNameArr[$row[csf('body_part_id')]]; ?> </td>
                    <td width="100" style="word-wrap: break-word;word-break: break-all;"> <? echo $const_comp_arr[$row[csf('item_id')]]; ?></td>
                    <td width="55" style="word-wrap: break-word;word-break: break-all;"> <? echo $collar_cuff_arr[$row[csf('delivery_id')]]; ?></td>
                    <td width="25" style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo $row[csf('packing_qnty')]; $tot_packing_qty+=$row[csf('packing_qnty')]; ?>&nbsp;</p></td>
                    <td width="55" style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo number_format($row[csf('delivery_qty')],2,'.',''); $tot_delivery_qty+=$row[csf('delivery_qty')]; ?>&nbsp;</p></td>
                    <td width="55" style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo number_format($row[csf('delivery_qtypcs')],2,'.',''); $tot_delivery_qtypcs+=$row[csf('delivery_qtypcs')]; ?>&nbsp;</p></td>
                    <td width="25" style="word-wrap: break-word;word-break: break-all;"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
                    <td width="30" style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo number_format($row[csf('rate')],2,'.',''); ?>&nbsp;</p></td>
                    <td width="30" style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo number_format($row[csf('amount')],2,'.','');  $total_amount += $row[csf('amount')]; ?>&nbsp;</p></td>

                    <td width="45" style="word-wrap: break-word;word-break: break-all;" align="center"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>

                    <td ><? echo $row[csf('remarks')]; ?> </td>
                    <? 
					$carrency_id=$row['currency_id'];
					if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}
				   ?>
                </tr>
                <?
                $i++;
			}
		}else{
			$job_year="to_char(a.insert_date,'YYYY') as year"; 
			$const_comp_arr=return_library_array( "select id,product_name_details from product_details_master",'id','product_name_details');
			$collar_cuff_arr=return_library_array( "select id,collar_cuff from subcon_delivery_dtls",'id','collar_cuff');
			$buyer_arr=return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');
     		$i=1;
			$mst_id=$dataArray[0][csf('id')];
			$po_id="";
			$sql_result =sql_select("select a.id, a.delivery_id, a.delivery_date, a.challan_no, a.order_id, a.item_id, a.uom, a.packing_qnty,a.coller_cuff_measurement, a.delivery_qty, a.delivery_qtypcs, a.rate, a.amount, a.remarks, a.currency_id, a.process_id, a.body_part_id,a.rec_challan_no,b.style_ref_no,b.po_job_no,b.customer_buyer,b.job_no,$job_year from subcon_inbound_bill_dtls a,fabric_sales_order_mst b where a.mst_id='$mst_id' and a.order_id=b.id and a.process_id='2' and b.entry_form= 109 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.challan_no ASC"); 
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$rec_challan="";
				if($row[csf('delivery_id')]!='' || $row[csf('delivery_id')]!=0) $rec_challan=$rec_challan_arr[$row[csf('delivery_id')]];
				else $rec_challan=$recChallan_arr[$row[csf('challan_no')]][change_date_format($row[csf('delivery_date')])];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:10px"> 
                    <td width="30" style="word-wrap: break-word;word-break: break-all;"><? echo $i; ?></td>
                    <td width="50" style="word-wrap: break-word;word-break: break-all;"><p><? echo $row[csf('challan_no')]; ?></p></td>
                    <td width="50" style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf('rec_challan_no')]; ?></td>
                    <td width="55" style="word-wrap: break-word;word-break: break-all;"><p><? echo change_date_format($row[csf('delivery_date')]); ?></p></td>
                    <td width="55" style="word-wrap: break-word;word-break: break-all;"><p><? echo $row[csf('job_no')]; ?></p></td>
                    <td width="55" style="word-wrap: break-word;word-break: break-all;"><p><? echo $buyer_arr[$row[csf('customer_buyer')]]; ?></td>
                    <td width="55" style="word-wrap: break-word;word-break: break-all;"><p><? echo $row[csf('style_ref_no')]; ?></td>
                    <td width="25" style="word-wrap: break-word;word-break: break-all;" align="center"><p><? echo $row[csf('po_job_no')]; ?></td>
                     <td width="25" style="word-wrap: break-word;word-break: break-all;" align="center"><? echo $row[csf('year')]; ?></td>
					 <td width="60" style="word-wrap: break-word;word-break: break-all;" align="center"><? echo $bodyPartNameArr[$row[csf('body_part_id')]]; ?> </td>
                    <td width="100" style="word-wrap: break-word;word-break: break-all;"> <? echo $const_comp_arr[$row[csf('item_id')]]; ?></td>
                    <td width="55" style="word-wrap: break-word;word-break: break-all;"> <? echo $row[csf('coller_cuff_measurement')]; ?></td>
                    <td width="25" style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo $row[csf('packing_qnty')]; $tot_packing_qty+=$row[csf('packing_qnty')]; ?>&nbsp;</p></td>
                    <td width="55" style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo number_format($row[csf('delivery_qty')],2,'.',''); $tot_delivery_qty+=$row[csf('delivery_qty')]; ?>&nbsp;</p></td>
                    <td width="55" style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo number_format($row[csf('delivery_qtypcs')],2,'.',''); $tot_delivery_qtypcs+=$row[csf('delivery_qtypcs')]; ?>&nbsp;</p></td>
                    <td width="25" style="word-wrap: break-word;word-break: break-all;"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
                    <td width="30" style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo number_format($row[csf('rate')],2,'.',''); ?>&nbsp;</p></td>
                    <td width="30" style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo number_format($row[csf('amount')],2,'.','');  $total_amount += $row[csf('amount')]; ?>&nbsp;</p></td>

                    <td width="45" style="word-wrap: break-word;word-break: break-all;" align="center"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>

                    <td ><? echo $row[csf('remarks')]; ?> </td>
                    <? 
					$carrency_id=$row['currency_id'];
					if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}
				   ?>
                </tr>
                <?
                $i++;
			}
		}
			?>
        	<tr style="font-size:12px"> 
                <td align="right" colspan="12"><strong>Total</strong></td>
                <td align="right"><? echo $tot_packing_qty; ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_delivery_qty,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_delivery_qtypcs,2,'.',''); ?>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><? echo $format_total_amount=number_format($total_amount,2,'.',''); ?>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
           <tr>
               <td colspan="19" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
           </tr>
        </table>
        <?
			$bill_no=$dataArray[0][csf('bill_no')];
			$sql_terms="Select id,terms from subcon_terms_condition where entry_form=1 and bill_no='$bill_no' ";
			$result_sql_terms =sql_select($sql_terms);

			$i=1;
			if(count($result_sql_terms)>0)
			{
				?>
                <table width="915" align="left" > 
                    <tr><td colspan="2">&nbsp;</td> </tr>
                    <tr><td colspan="2" align="center"><b>TERMS & CONDITION</b></td></tr>
					<?
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
		 <? echo signature_table(220, $data[0], "930px"); ?>
   </div>
   </div>
	<?
    exit();
}


//======================================================================Bill Print 5============================================================================================
if($action=="knitting_bill_print_5_backup") 
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$party_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name");
	$yarn_desc_arr=return_library_array( "select id,yarn_description from lib_subcon_charge",'id','yarn_description');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	
	$sql_mst="Select id, bill_no, bill_date, party_id, party_source, location_id, attention, bill_for,party_location_id from subcon_inbound_bill_mst where company_id=$data[0] and id='$data[1]' and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql_mst);
	?>
    <div style="width:930px;margin:0px auto;">
    <table width="100%" cellpadding="0" cellspacing="0" >
        <tr>
            <!-- <td width="70" align="right"> 
            	<img  src='../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='70' width='200' />
            </td> -->
            <td>
                <table width="800" cellspacing="0" align="center">
                    <tr>
                    	<td align="center" style="font-size:20px"><strong ><? echo $company_library[$data[0]]; ?></strong></td>
                    </tr>
                    <!-- <tr>
                        <td align="center"  style="font-size:16px"><strong>Unit : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                    </tr> -->
                    <tr class="form_caption">
                        <td align="center" style="font-size:14px"><? echo show_company($data[0],'',''); ?></td>  
                    </tr>
					<tr>  
					<td>&nbsp;</td>
					</tr>
                    <tr>
                    	<td align="center" style="font-size:18px"><strong><? echo $data[3]; ?></strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
     <table width="915" cellspacing="0" align="" border="0">
            <tr>
                <td width="130"><strong>Bill No :</strong></td> <td width="175"><? echo $dataArray[0][csf('bill_no')]; ?></td>
                <td width="130"><strong>Bill Date: </strong></td><td width="175px"> <? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
                <td width="130"><strong>Source :</strong></td> <td width="175"><? echo $knitting_source[$dataArray[0][csf('party_source')]]; ?></td>
            </tr>
            <tr>
			
				<?
                    if($dataArray[0][csf('attention')]!='')  $attention='('.$dataArray[0][csf('attention')].')'; else $attention='';
                        
                    if($dataArray[0][csf('party_source')]==2)
                    {
                        $party_add=$dataArray[0][csf('party_id')];
                        $nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add"); 
                        foreach ($nameArray as $result)
                        { 
                            $address="";
							if($result!="") $address=$result[csf('address_1')];;
                        }
						$party_name=$party_library[$dataArray[0][csf('party_id')]].'<br/>Address:'.$address.' '.$attention;
						$party_location='';
                    }
                    else
                    {
                        $party_name=$company_library[$dataArray[0][csf('party_id')]].' '.$attention;
						$party_location=$location_arr[$dataArray[0][csf('party_location_id')]];
                    }
                ?>
				
				<td  width="150"  valign="top"><strong>Party Name: </strong></td><td width="200"  valign="top"><p> <? echo $party_name; ?></p></td>
                <td width="180"  valign="top"><strong>Party Location: </strong></td><td  width="200"  valign="top"> <? echo $party_location; ?></td>
                <td width="150"  valign="top"><strong>Bill For : </strong></td><td width="200"  valign="top"> <? echo $bill_for[$dataArray[0][csf('bill_for')]]; ?></td>
            </tr>
        </table>
         <br>
	<div style="width:100%;">
		<table align="left" cellspacing="0" width="915"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center" style="font-size:16px">
                <th width="30" style="word-wrap: break-word;word-break: break-all;">SL</th>
                <th width="100" style="word-wrap: break-word;word-break: break-all;">Sys. Challan <br> D. Date</th>
                <th width="100" style="word-wrap: break-word;word-break: break-all;">Order <br>Buyer <br>Style </th> 
                <th width="25" style="word-wrap: break-word;word-break: break-all;">Job</th>
                <th width="210" style="word-wrap: break-word;word-break: break-all;">Fabric Description <br> Collar Cuff Measurement</th>
                <th width="25" style="word-wrap: break-word;">Roll</th>
                <th width="100" style="word-wrap: break-word;word-break: break-all;">D. Qty (W)</th>
                <th width="30" style="word-wrap: break-word;">Rate</th>
                <th width="100" style="word-wrap: break-word;word-break: break-all;">Amount</th>
                <th> Remarks</th>
            </thead>
		 <?
		 if($db_type==0) $job_year="YEAR(a.insert_date) as year";
		 else  $job_year="to_char(a.insert_date,'YYYY') as year";
		 	
			if($dataArray[0][csf('party_source')]==2)
			{
				$order_array=array();
				 $order_sql="select a.job_no_prefix_num,b.id,$job_year, b.order_no, b.order_uom, b.cust_buyer, b.cust_style_ref from subcon_ord_dtls b,subcon_ord_mst a where b.job_no_mst=a.subcon_job and b.status_active=1 and b.is_deleted=0";
				$order_sql_result =sql_select($order_sql);
				foreach($order_sql_result as $row)
				{
					$order_array[$row[csf('id')]]['po_number']=$row[csf('order_no')];
					$order_array[$row[csf('id')]]['buyer_name']=$row[csf('cust_buyer')];
					$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('cust_style_ref')];
					$order_array[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
					$order_array[$row[csf('id')]]['year']=$row[csf('year')];
				}
				unset($order_sql_result);
				$const_comp_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
				$collar_cuff_arr=return_library_array( "select id,collar_cuff from subcon_delivery_dtls",'id','collar_cuff');
			}
			else if($dataArray[0][csf('party_source')]==1)
			{
				$order_array=array();
				$job_sql="select a.job_no, $job_year, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.total_set_qnty as ratio, b.id, b.po_number, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active!=0 and a.is_deleted=0 and  b.status_active!=0 and b.is_deleted=0";// and a.company_name=$data[0]
				$job_sql_result =sql_select($job_sql);
				foreach($job_sql_result as $row)
				{
					$order_array[$row[csf('id')]]['buyer_name']=$party_library[$row[csf('buyer_name')]];
					$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
					$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
					$order_array[$row[csf('id')]]['job']=$row[csf('job_no')];
					$order_array[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
					$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
					$order_array[$row[csf('id')]]['ratio']=$row[csf('ratio')];
					$order_array[$row[csf('id')]]['year']=$row[csf('year')];
				}
				unset($job_sql_result);
				$const_comp_arr=return_library_array( "select id,product_name_details from product_details_master",'id','product_name_details');
				$recChallan_arr=array();
				$rec_challan_arr=return_library_array( "select id,challan_no from inv_receive_master",'id','challan_no');
				//echo "select recv_number_prefix_num, receive_date, challan_no from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]' and location_id='".$dataArray[0][csf('location_id')]."' and entry_form=22 and status_active=1 and is_deleted=0 and recv_number_prefix_num=30";
				$rec_challa_sql=sql_select("select recv_number_prefix_num, receive_date, challan_no from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]' and location_id='".$dataArray[0][csf('location_id')]."' and entry_form in (2,22)  and status_active=1 and is_deleted=0"); //and a.company_id='$data[3]' and a.knitting_company='$data[0]' and a.location_id='$data[1]'
				foreach($rec_challa_sql as $row)
				{
					$recChallan_arr[$row[csf('recv_number_prefix_num')]][change_date_format($row[csf('receive_date')])]=$row[csf('challan_no')];
				}
			}
			
			if($dataArray[0][csf('bill_for')]==3)
			{
				$buyer_id_arr=array();
				$sql_non_booking=sql_select("select recv_number_prefix_num, receive_date, buyer_id from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]' and location_id='".$dataArray[0][csf('location_id')]."' and entry_form=22  and status_active=1 and is_deleted=0 group by recv_number_prefix_num, receive_date, buyer_id");
				foreach($sql_non_booking as $row)
				{
					$buyer_id_arr[$row[csf('recv_number_prefix_num')]][change_date_format($row[csf('receive_date')])]=$party_library[$row[csf('buyer_id')]];
				}
			}
			//var_dump($recChallan_arr);
     		$i=1;
			$mst_id=$dataArray[0][csf('id')];
			$po_id="";
			$sql_result =sql_select("select id, delivery_id, delivery_date, challan_no, order_id, item_id, uom, packing_qnty, delivery_qty, delivery_qtypcs, rate, amount, remarks, currency_id, process_id from subcon_inbound_bill_dtls  where mst_id='$mst_id' and process_id='2' and status_active=1 and is_deleted=0 order by challan_no ASC"); 
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($po_id=="") $po_id=$row[csf('order_id')]; else $po_id.=','.$row[csf('order_id')];
				$buyer_id_name="";
				if($dataArray[0][csf('bill_for')]==3)
				{
					$buyer_id_name=$buyer_id_arr[$row[csf('challan_no')]][change_date_format($row[csf('delivery_date')])];
				}
				else
				{
					$buyer_id_name=$order_array[$row[csf('order_id')]]['buyer_name'];
				}
				//echo $row[csf('order_id')];
				$rec_challan="";
				if($row[csf('delivery_id')]!='' || $row[csf('delivery_id')]!=0) $rec_challan=$rec_challan_arr[$row[csf('delivery_id')]];
				else $rec_challan=$recChallan_arr[$row[csf('challan_no')]][change_date_format($row[csf('delivery_date')])];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:16px; text-align: center;"> 
                    <td width="30" style="word-wrap: break-word;word-break: break-all; text-align: center;"><? echo $i; ?></td>
                    <td width="100" style="word-wrap: break-word;word-break: break-all;">
					<p>
							 <? $challan_no=$row[csf('challan_no')]; 
							    if ($challan_no !=""){
									echo $challan_no;
								 }else
								 echo "-----";
							 ?> 
						<br> <? 
								$delivery_date=change_date_format($row[csf('delivery_date')]); 
								if ($delivery_date !=""){
									echo $delivery_date;
								 }else
								 echo "-----";
						     ?> 
					</p>
					</td>
                    <td width="100" style="word-wrap: break-word;word-break: break-all;">
					<p>
						     <?   
							 $Order_id=$order_array[$row[csf('order_id')]]['po_number'];
							 if ($Order_id !=""){
								echo $Order_id;
							 }else
							 echo "-----"; 
					         ?> 
						<br> <? 
						      if ($buyer_id_name !=""){
								echo $buyer_id_name;
							 }else
							 echo "-----"; 
						     ?> 
						<br> <? 
							  $style_No= $order_array[$row[csf('order_id')]]['style_ref_no']; 
							  if ($style_No !=""){
								echo $style_No;
							 }else
							 echo "-----"; 
						     ?>
					</p>
					</td>
                    <td width="25" style="word-wrap: break-word;word-break: break-all;" align="center"><? echo $order_array[$row[csf('order_id')]]['job_no_prefix_num']; ?></td>
                    <td width="210" style="word-wrap: break-word;word-break: break-all;"> <? echo $const_comp_arr[$row[csf('item_id')]]; ?> <br> <? echo $collar_cuff_arr[$row[csf('delivery_id')]]; ?></td>
                    <td width="25" style="word-wrap: break-word;word-break: break-all; text-align: center;"><p><? echo $row[csf('packing_qnty')]; $tot_packing_qty+=$row[csf('packing_qnty')]; ?>&nbsp;</p></td>
                    <td width="100" style="word-wrap: break-word;word-break: break-all; text-align: center;"><p><? echo number_format($row[csf('delivery_qty')],2,'.',''); $tot_delivery_qty+=$row[csf('delivery_qty')]; ?>&nbsp;</p></td>
                    <td width="30" style="word-wrap: break-word;word-break: break-all; text-align: center;"><p><? echo number_format($row[csf('rate')],2,'.',''); ?>&nbsp;</p></td>
                    <td width="100" style="word-wrap: break-word;word-break: break-all; text-align: center;"><p><? echo number_format($row[csf('amount')],2,'.','');  $total_amount += $row[csf('amount')]; ?>&nbsp;</p></td>
                    <td ><? echo $row[csf('remarks')]; ?> </td>
                    <? 
					$carrency_id=$row['currency_id'];
					if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}
				   ?>
                </tr>
                <?
                $i++;
			}
			?>
        	<tr style="font-size:16px"> 
                <td align="right" colspan="5"><strong>Total</strong></td>
                <td align="center"><strong><? echo $tot_packing_qty; ?></strong>&nbsp;</td>
                <td align="center"><strong><? echo number_format($tot_delivery_qty,2,'.',''); ?></strong>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="center" colspan=""><strong><? echo $format_total_amount=number_format($total_amount,2,'.',''); ?></strong>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
           <tr>
               <td colspan="18" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
           </tr>
		
        </table>
        <?
			$bill_no=$dataArray[0][csf('bill_no')];
			$sql_terms="Select id,terms from subcon_terms_condition where entry_form=1 and bill_no='$bill_no' ";
			$result_sql_terms =sql_select($sql_terms);

			$i=1;
			if(count($result_sql_terms)>0)
			{
				?>
                <table width="915" align="left" > 
                    <tr><td colspan="2">&nbsp;</td> </tr>
                    <tr><td colspan="2" align="center"><b>TERMS & CONDITION</b></td></tr>
					<?
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
    	 <? echo signature_table(220, $data[0], "930px"); ?>   
   </div>
   </div>
	<?
    exit();
}

//============================================ Bill Print ( 2 ) similar print report 1 but 2 colom discard in print report=========================
if($action=="knitting_bill_print_2") 
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$party_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name");
	$yarn_desc_arr=return_library_array( "select id,yarn_description from lib_subcon_charge",'id','yarn_description');
	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$yearn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	
	$sql_mst="Select id, bill_no, bill_date, party_id, party_source, location_id,party_location_id, attention, bill_for from subcon_inbound_bill_mst where company_id=$data[0] and id='$data[1]' and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql_mst);
	$mst_id=$dataArray[0][csf('id')];
	
	$sql_result =sql_select("select id, delivery_id, delivery_date, challan_no, order_id, item_id, uom, packing_qnty, delivery_qty, delivery_qtypcs, rate, amount, remarks, currency_id, process_id, body_part_id, febric_description_id from subcon_inbound_bill_dtls where mst_id='$mst_id' and process_id='2' and status_active=1 and is_deleted=0 order by challan_no ASC"); 
	//echo "select id, delivery_id, delivery_date, challan_no, order_id, item_id, uom, packing_qnty, delivery_qty, delivery_qtypcs, rate, amount, remarks, currency_id, process_id, body_part_id, febric_description_id from subcon_inbound_bill_dtls where mst_id='$mst_id' and process_id='2' and status_active=1 and is_deleted=0 order by challan_no ASC";
	$po_id_arr=array();
	foreach($sql_result as $row)
	{
		$po_id_arr[$row[csf('order_id')]]=$row[csf('order_id')];
		$receive_id_arr[$row[csf('delivery_id')]]=$row[csf('delivery_id')];
	}
	//echo "<pre>";
	//print_r($sql_result); die;
	?>
    <div style="width:1150px; margin-left:20px;">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td  width="110" align="right"> 
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
                        <tr>
                        	<td class="form_caption"><? echo show_company($data[0],'',''); ?></td>  
                        </tr>
                        <tr>
                            <td align="center" style="font-size:18px"><strong><? echo $data[3]; ?></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table width="1150" cellspacing="0" align="" border="0">
            <tr>
                <td width="150"  valign="top"><strong>Bill No :</strong></td> <td width="200"><? echo $dataArray[0][csf('bill_no')]; ?></td>
                <td width="150"><strong>Bill Date: </strong></td><td width="200px"> <? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
                <td width="150"><strong>Source :</strong></td> <td width="200"><? echo $knitting_source[$dataArray[0][csf('party_source')]]; ?></td>
            </tr>
            <tr>
				<?
                if($dataArray[0][csf('attention')]!='')  $attention='('.$dataArray[0][csf('attention')].')';
                else $attention='';
                if($dataArray[0][csf('party_source')]==2)
                {
					$party_add=$dataArray[0][csf('party_id')];
					$nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add"); 
					foreach ($nameArray as $result)
					{ 
						$address="";
						if($result!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
					}
					$party_name=$party_library[$dataArray[0][csf('party_id')]].'<br/>Address:'.$address.' '.$attention;
					$party_location='';
                }
                else
                {
					$party_name=$company_library[$dataArray[0][csf('party_id')]].' '.$attention;
					$party_location=$location_arr[$dataArray[0][csf('party_location_id')]];
                }
                ?>
                <td  width="150"  valign="top"><strong>Party Name: </strong></td><td width="200"  valign="top"><p> <? echo $party_name; ?></p></td>
                <td width="180"  valign="top"><strong>Party Location: </strong></td><td  width="200"  valign="top"> <? echo $party_location; ?></td>
                <td width="150"  valign="top"><strong>Bill For : </strong></td><td width="200"  valign="top"> <? echo $bill_for[$dataArray[0][csf('bill_for')]]; ?></td>
            </tr>
        </table>
        <br>
        <div style="width:100%;" >
		<table cellspacing="0" width="1240"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center" style="font-size:16px">
                <th width="30">SL</th>
                <th width="40">Sys. Challan</th>
                <th width="40">Rec. Challan</th>
                <th width="55">D. Date</th>
                <th width="60">Order</th> 
                <th width="60">Buyer</th>
                <th width="60">Style</th>
                <th width="40">Job</th>
                <th width="50">Internal Ref.</th>
                <th width="35">Year</th>
                <th width="150">Fabric Description</th>
                <th width="100">Fabric Color</th>
                <th width="40">Feeder</th>
                <th width="40">Yarn Count</th>
                <th width="40">MC Dia</th>
                <th width="40">MC Gauge</th>
                <th width="25">Roll</th>
                <th width="55">D. Qty (W)</th>
                <th width="55">D. Qty (P)</th>
                <th width="30">UOM</th>
                <th width="40">Rate</th>
                <th width="70">Amount</th>
                <th>Currency</th>
            </thead>
		 	<? 
		 	if($db_type==0)
				$job_year="YEAR(a.insert_date) as year";
			else
				$job_year="to_char(a.insert_date,'YYYY') as year";
		 	
			if($dataArray[0][csf('party_source')]==2)
			{
				$order_array=array();
				 $order_sql="select a.job_no_prefix_num,b.id,$job_year, b.order_no, b.order_uom, b.cust_buyer, b.cust_style_ref from subcon_ord_dtls b,subcon_ord_mst a where b.job_no_mst=a.subcon_job and b.status_active=1 and b.is_deleted=0";
				$order_sql_result =sql_select($order_sql);
				foreach($order_sql_result as $row)
				{
					$order_array[$row[csf('id')]]['po_number']=$row[csf('order_no')];
					$order_array[$row[csf('id')]]['buyer_name']=$row[csf('cust_buyer')];
					$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('cust_style_ref')];
					$order_array[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
					$order_array[$row[csf('id')]]['year']=$row[csf('year')];
					$job_array[$row[csf('job_no')]]=$row[csf('job_no')];
				}
				unset($order_sql_result);
				$const_comp_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
				$collar_cuff_arr=return_library_array( "select id,collar_cuff from subcon_delivery_dtls",'id','collar_cuff');
				
				$prodData="select order_id, cons_comp_id, color_id, yrn_count_id, machine_dia, machine_gg from subcon_production_dtls where product_type=2 and is_deleted=0 and  status_active=1";
				$prod_sql_result =sql_select($prodData);
				$subcon_exData_arr=array();
				foreach($prod_sql_result as $row)
				{
					$subcon_exData_arr[$row[csf('order_id')]][$row[csf('cons_comp_id')]]['fabcolor'].=$row[csf('color_id')].',';
					$subcon_exData_arr[$row[csf('order_id')]][$row[csf('cons_comp_id')]]['yrnCount'].=$row[csf('yrn_count_id')].',';
					$subcon_exData_arr[$row[csf('order_id')]][$row[csf('cons_comp_id')]]['mcdia'].=$row[csf('machine_dia')].',';
					$subcon_exData_arr[$row[csf('order_id')]][$row[csf('cons_comp_id')]]['mcgg'].=$row[csf('machine_gg')].',';
				}
				unset($prod_sql_result);
			}
			
			else if($dataArray[0][csf('party_source')]==1)
			{
				//for wo_po_break_down id
				$poCondition = '';
				if(count($po_id_arr)>0)
				{
					$poCond = '';
					$allPo = implode(",", $po_id_arr);
					if($db_type==2 && count($po_id_arr)>999)
					{
						$allPoChunk=array_chunk($po_id_arr,999) ;
						foreach($allPoChunk as $chunk_arr)
						{
							$poCond.=" b.id in(".implode(",",$chunk_arr).") or ";
						}
			
						$poCondition.=" and (".chop($poCond,'or ').")";
					}
					else
					{
						$poCondition=" and b.id in(".$allPo.")";
					}
				}
				
				$order_array=array();
				$job_sql="select a.job_no, $job_year, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.total_set_qnty as ratio, b.id, b.po_number, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active!=0 and a.is_deleted=0 and  b.status_active!=0 and b.is_deleted=0 ".$poCondition."";//and a.company_name=$data[0]
				$job_sql_result =sql_select($job_sql);
				foreach($job_sql_result as $row)
				{
					$order_array[$row[csf('id')]]['buyer_name']=$party_library[$row[csf('buyer_name')]];
					$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
					$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
					$order_array[$row[csf('id')]]['job']=$row[csf('job_no')];
					$order_array[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
					$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
					$order_array[$row[csf('id')]]['ratio']=$row[csf('ratio')];
					$order_array[$row[csf('id')]]['year']=$row[csf('year')];
					$job_array[$row[csf('job_no')]]=$row[csf('job_no')];
					
				}
				unset($job_sql_result);
				
				$const_comp_arr=return_library_array( "select id,product_name_details from product_details_master",'id','product_name_details');
				$knit_plan_arr=array();
				$knit_plan="select id, feeder from ppl_planning_info_entry_dtls where status_active=1 and is_deleted=0 and feeder!=0";
				$knit_plan_res=sql_select($knit_plan);
				foreach($knit_plan_res as $row)
				{
					$knit_plan_arr[$row[csf('id')]]=$row[csf('feeder')];
				}
				unset($knit_plan_res);

				$production_arr=array();
				//echo "select recv_number_prefix_num, receive_date, challan_no from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]' and location_id='".$dataArray[0][csf('location_id')]."' and entry_form=22 and status_active=1 and is_deleted=0 and recv_number_prefix_num=30";
				$production_sql=sql_select("select id, booking_id, booking_no from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]'   and entry_form in (2) and receive_basis=2 and status_active=1 and is_deleted=0"); //and a.company_id='$data[3]' and a.knitting_company='$data[0]' and a.location_id='$data[1]'
				//echo "select id, booking_id, booking_no from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]'   and entry_form in (2) and receive_basis=2 and status_active=1 and is_deleted=0";
				foreach($production_sql as $row)
				{
					$production_arr[$row[csf('id')]]=$feeder[$knit_plan_arr[$row[csf('booking_id')]]];
				}
				unset($production_sql);
				
				//for inv_receive_master id
				$rcvIdCondition = '';
				if(count($receive_id_arr)>0)
				{
					$rcvIdCond = '';
					$allRcvId = implode(",", $receive_id_arr );
					if($db_type==2 && count($receive_id_arr)>999)
					{
						$allRcvIdChunk=array_chunk($receive_id_arr,999) ;
						foreach($allRcvIdChunk as $chunk_arr)
						{
							$rcvIdCond.=" a.id in(".implode(",",$chunk_arr).") or ";
						}
			
						$rcvIdCondition.=" and (".chop($rcvIdCond,'or ').")";
					}
					else
					{
						$rcvIdCondition=" and a.id in(".$allRcvId.")";
					}
				}
				
				$rec_data_arr=array();
				$recChallan_arr=array();
				 $res_sql="select c.barcode_no,a.id, a.recv_number_prefix_num, a.receive_date, a.entry_form, a.challan_no, a.receive_basis, a.booking_id, a.booking_no, b.prod_id, b.body_part_id, b.febric_description_id,c.booking_no as prog_no, c.po_breakdown_id as po_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.company_id='".$dataArray[0][csf('party_id')]."' and a.knitting_company='$data[0]' and a.location_id='".$dataArray[0][csf('party_location_id')]."' and a.id=b.mst_id and b.id=c.DTLS_ID and a.id=c.mst_id and b.trans_id > 0 and a.entry_form in(2,22,58) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".$rcvIdCondition."";	
				$res_sql_res=sql_select($res_sql);
				foreach($res_sql_res as $row)
				{
					$barCodeArr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
				}
				
				//for pro_roll_details barcode_no
				$barcodeCondition = '';
				if(count($barCodeArr)>0)
				{
					$barcodeCond = '';
					$all_barcode_nos = implode(",", $barCodeArr);
					if($db_type==2 && count($barCodeArr)>999)
					{
						$all_barcode_no_chunk=array_chunk($barCodeArr,999) ;
						foreach($all_barcode_no_chunk as $chunk_arr)
						{
							$barcodeCond.=" c.barcode_no in(".implode(",",$chunk_arr).") or ";
						}
			
						$barcodeCondition.=" and (".chop($barcodeCond,'or ').")";
					}
					else
					{
						$barcodeCondition=" and c.barcode_no in(".$all_barcode_nos.")";
					}
				}

				//$barCodeWiseProductionSql="select a.recv_number,c.barcode_no, c.po_breakdown_id as po_id,c.booking_no, b.id, b.body_part_id,b.prod_id,b.febric_description_id,b.yarn_count, b.color_id, b.machine_dia, b.machine_gg from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.company_id='".$dataArray[0][csf('party_id')]."' and a.knitting_company='".$data[0]."' and a.location_id='".$dataArray[0][csf('location_id')]."' and a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.entry_form in (2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.barcode_no in(".implode(',',$barCodeArr).")";
				$barCodeWiseProductionSql="select a.recv_number,c.barcode_no, c.po_breakdown_id as po_id,c.booking_no, b.id, b.body_part_id,b.prod_id,b.febric_description_id,b.yarn_count, b.color_id, b.machine_dia, b.machine_gg from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.company_id='".$dataArray[0][csf('party_id')]."' and a.knitting_company='".$data[0]."' and a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.entry_form in (2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".$barcodeCondition."";
				$barCodeWiseProductionResult=sql_select($barCodeWiseProductionSql);
				foreach($barCodeWiseProductionResult as $row)
				{
					$barCodeDataArr2[$row[csf('barcode_no')]][$row[csf('po_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['yarn_count']=$row[csf('yarn_count')];
					//echo $row[csf('recv_number')].'='.$row[csf('yarn_count')].'<br>';
					$barCodeDataArr[$row[csf('barcode_no')]]['color_id']=$row[csf('color_id')];
					$barCodeDataArr[$row[csf('barcode_no')]]['machine_dia']=$row[csf('machine_dia')];
					$barCodeDataArr[$row[csf('barcode_no')]]['machine_gg']=$row[csf('machine_gg')];
					$barCodeDataArr[$row[csf('barcode_no')]]['plan_id']=$row[csf('booking_no')];
				}
				//echo "<pre>";
				//print_r($barCodeDataArr); die;
				
				foreach($res_sql_res as $row)
				{
					$sys_challan=$row[csf('id')];
					$recChallan_arr[$sys_challan][change_date_format($row[csf('receive_date')])]=$row[csf('challan_no')];
					$yarn_count="";
					$yarn_count=$barCodeDataArr2[$row[csf('barcode_no')]][$row[csf('po_id')]][$row[csf('prog_no')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['yarn_count'];
				//	echo $sys_challan.'='. $yarn_count.'='.$row[csf('po_id')].'<br> ';
					
					$rec_data_arr[$sys_challan][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['yarn_count'].=$yarn_count.',';
					$rec_data_arr[$sys_challan][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['color_id'].=$barCodeDataArr[$row[csf('barcode_no')]]['color_id'].',';
					$rec_data_arr[$sys_challan][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['machine_dia'].=$barCodeDataArr[$row[csf('barcode_no')]]['machine_dia'].',';
					
					$rec_data_arr[$sys_challan][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['machine_gg'].=$barCodeDataArr[$row[csf('barcode_no')]]['machine_gg'].',';
					
					
					if(($row[csf('receive_basis')]==9 || $row[csf('receive_basis')]==10) && ($row[csf('entry_form')]==22 || $row[csf('entry_form')]==58))
					{
						$rec_data_arr[$sys_challan][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['feeder'].=$feeder[$knit_plan_arr[$barCodeDataArr[$row[csf('barcode_no')]]['plan_id']]].',';
					}
					else if($row[csf('receive_basis')]==2 && $row[csf('entry_form')]==2)
					{
						$rec_data_arr[$sys_challan][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['feeder'].=$production_arr[$row[csf('booking_id')]].',';
					}
					$sample_rec_data_arr[$sys_challan][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['color_id'].=$barCodeDataArr[$row[csf('barcode_no')]]['color_id'].',';
					$sample_rec_data_arr[$sys_challan][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['yarn_count'].=$yarn_count.',';
					$sample_rec_data_arr[$sys_challan][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['machine_dia'].=$barCodeDataArr[$row[csf('barcode_no')]]['machine_dia'].',';
					
					$sample_rec_data_arr[$sys_challan][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['machine_gg'].=$barCodeDataArr[$row[csf('barcode_no')]]['machine_gg'].',';
					$sample_rec_data_arr[$sys_challan][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['feeder'].=$production_arr[$row[csf('booking_id')]].',';
				}
				unset($res_sql_res);
				$internal_ref_sql="select job_no,internal_ref from wo_order_entry_internal_ref where job_no in('".implode("','",$job_array)."')";
				$internal_ref_sql_result=sql_select($internal_ref_sql);
				foreach($internal_ref_sql_result as $row)
				{
					$internal_ref_arr[$row[csf('job_no')]][$row[csf('internal_ref')]]=$row[csf('internal_ref')];
				}
			}
			if($dataArray[0][csf('bill_for')]==3)
			{
				$buyer_id_arr=array();
				$sql_non_booking=sql_select("select b.booking_no,b.booking_without_order,c.grouping,a.recv_number_prefix_num, a.receive_date, c.buyer_id,e.style_ref_no from inv_receive_master a,pro_roll_details b,wo_non_ord_samp_booking_mst c,wo_non_ord_samp_booking_dtls d,sample_development_mst e  where a.id=b.mst_id and c.booking_no=b.booking_no and c.booking_no=d.booking_no and e.id=d.style_id and b.entry_form=58 and a.company_id='".$dataArray[0][csf('party_id')]."' and a.knitting_company='$data[0]' and a.entry_form=58  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.booking_no,b.booking_without_order,a.recv_number_prefix_num, a.receive_date, c.buyer_id,c.grouping,e.style_ref_no");
				
				//echo "select recv_number_prefix_num, receive_date, buyer_id from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]' and location_id='".$dataArray[0][csf('location_id')]."' and entry_form=58  and status_active=1 and is_deleted=0 group by recv_number_prefix_num, receive_date, buyer_id";
				foreach($sql_non_booking as $row)
				{
					$buyer_id_arr[$row[csf('recv_number_prefix_num')]][change_date_format($row[csf('receive_date')])]=$party_library[$row[csf('buyer_id')]];
					$sample_dtls_ref_arr[$row[csf('recv_number_prefix_num')]][$row[csf('receive_date')]]['ref']=$row[csf('grouping')];
					$sample_dtls_ref_arr[$row[csf('recv_number_prefix_num')]][$row[csf('receive_date')]]['style']=$row[csf('style_ref_no')];
				}
			}
			
			//$order_array[$row[csf('id')]]['job']=$row[csf('job_no')];
			$po_id="";
			$i=1;
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($po_id=="") $po_id=$row[csf('order_id')]; else $po_id.=','.$row[csf('order_id')];
				$buyer_id_name="";
				if($dataArray[0][csf('bill_for')]==3)
				{
					$buyer_id_name=$buyer_id_arr[$row[csf('challan_no')]][change_date_format($row[csf('delivery_date')])];
					$internal_refs=$sample_dtls_ref_arr[$row[csf('challan_no')]][$row[csf('delivery_date')]]['ref'];
					$style_ref_no=$sample_dtls_ref_arr[$row[csf('challan_no')]][$row[csf('delivery_date')]]['style'];
					//$samole_rec_data_arr[$sys_challan][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['color_id'];
					
					//$sam_fab_color=array_filter(array_unique(explode(",",$samole_rec_data_arr[$row[csf('delivery_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['color_id'])));
					//print_r($sam_fab_color);
				}
				else
				{
					$buyer_id_name=$order_array[$row[csf('order_id')]]['buyer_name'];
					$internal_refs=implode(',',$internal_ref_arr[$order_array[$row[csf('order_id')]]['job']]);
					$style_ref_no=$order_array[$row[csf('order_id')]]['style_ref_no'];
				}
				
				$fab_color=""; $feeder_str=""; $yarn_count=""; $mc_dia=''; $mc_gg="";
				if($dataArray[0][csf('party_source')]==1)
				{
					if($dataArray[0][csf('bill_for')]==3)
					{
					$fab_color=array_filter(array_unique(explode(",",$sample_rec_data_arr[$row[csf('delivery_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['color_id'])));
					$feeder_str=implode(",",array_filter(array_unique(explode(",",$sample_rec_data_arr[$row[csf('delivery_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['feeder']))));
					 $yarn_count_id=array_filter(array_unique(explode(",",$sample_rec_data_arr[$row[csf('delivery_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['yarn_count'])));
					 $mc_dia=implode(",",array_filter(array_unique(explode(",",$sample_rec_data_arr[$row[csf('delivery_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['machine_dia']))));
					//echo $row[csf('challan_no')].'='.$row[csf('item_id')].'='.$row[csf('body_part_id')].'='.$row[csf('febric_description_id')].'<br>';
					$mc_gg=implode(",",array_filter(array_unique(explode(",",$sample_rec_data_arr[$row[csf('delivery_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['machine_gg']))));
					}
					else
					{
						$fab_color=array_filter(array_unique(explode(",",$rec_data_arr[$row[csf('delivery_id')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['color_id'])));
						$feeder_str=implode(",",array_filter(array_unique(explode(",",$rec_data_arr[$row[csf('delivery_id')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['feeder']))));
					  $yarn_count_id=array_filter(array_unique(explode(",",$rec_data_arr[$row[csf('delivery_id')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['yarn_count'])));
					  $mc_dia=implode(",",array_filter(array_unique(explode(",",$rec_data_arr[$row[csf('delivery_id')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['machine_dia']))));
					//echo $row[csf('challan_no')].'='.$row[csf('item_id')].'='.$row[csf('body_part_id')].'='.$row[csf('febric_description_id')].'<br>';
					$mc_gg=implode(",",array_filter(array_unique(explode(",",$rec_data_arr[$row[csf('delivery_id')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['machine_gg']))));
					}
					
					$color_srt_arr=array();
					foreach ($fab_color as $color_id){
						$color_srt_arr[$color_id]=$color_arr[$color_id];
					}
					$fab_color=implode(",",$color_srt_arr);
					
					
					//echo $dataArray[0][csf('party_source')].',';
					foreach($yarn_count_id as $count_id)
					{
						if($yarn_count=="") $yarn_count=$yearn_count_arr[$count_id]; else $yarn_count.=', '.$yearn_count_arr[$count_id];
					}
					$yarn_count=implode(",",explode(',',$yarn_count));
					
					
				}
				else if($dataArray[0][csf('party_source')]==2)
				{
					$fab_color=array_filter(array_unique(explode(",",$subcon_exData_arr[$row[csf('order_id')]][$row[csf('item_id')]]['fabcolor'])));
					
					$color_srt_arr=array();
					foreach ($fab_color as $color_id){
						$color_srt_arr[$color_id]=$color_arr[$color_id];
					}
					$fab_color=implode(",",$color_srt_arr);
					
					
					$yarn_count_id=array_filter(array_unique(explode(",",$subcon_exData_arr[$row[csf('order_id')]][$row[csf('item_id')]]['yrnCount'])));
					foreach($yarn_count_id as $count_id)
					{
						if($yarn_count=="") $yarn_count=$yearn_count_arr[$count_id]; else $yarn_count.=', '.$yearn_count_arr[$count_id];
					}
					$yarn_count=implode(",",explode(',',$yarn_count));
					
					$mc_dia=implode(",",array_filter(array_unique(explode(",",$subcon_exData_arr[$row[csf('order_id')]][$row[csf('item_id')]]['mcdia']))));
					$mc_gg=implode(",",array_filter(array_unique(explode(",",$subcon_exData_arr[$row[csf('order_id')]][$row[csf('item_id')]]['mcgg']))));
				}
				//$data_all=$row[csf('delivery_id')].'='.$row[csf('item_id')].'='.$row[csf('body_part_id')].'='.$row[csf('febric_description_id')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:16px"> 
                    <td><? echo $i; ?></td>
                    <td><p><? echo $row[csf('challan_no')]; ?></p></td>
                    <td><div style="word-wrap:break-word; width:40px"><? echo $recChallan_arr[$row[csf('delivery_id')]][change_date_format($row[csf('delivery_date')])]; ?></div></td>
                    <td><p><? echo change_date_format($row[csf('delivery_date')]); ?></p></td>
                    <td><div style="word-wrap:break-word; width:60px"><? echo $order_array[$row[csf('order_id')]]['po_number']; ?></div></td>
                    <td><div style="word-wrap:break-word; width:60px"><? echo $buyer_id_name; ?></div></td>
                    <td><div style="word-wrap:break-word; width:60px"><? echo $style_ref_no; ?></div></td>
                    <td align="center"><div style="word-wrap:break-word; width:40px"><? echo $order_array[$row[csf('order_id')]]['job_no_prefix_num']; ?></div></td>
                   
                    <td align="center"><p><? echo $internal_refs; ?></p></td>
                    
                    <td align="center"><div style="word-wrap:break-word; width:35px"><? echo $order_array[$row[csf('order_id')]]['year']; ?></div></td>
                    <td><div style="word-wrap:break-word; width:150px"><? echo $const_comp_arr[$row[csf('item_id')]]; ?></div></td>
                    
                    <td align="center"><div style="word-wrap:break-word; width:100px"><? echo $fab_color; ?></div></td>
                    <td><div style="word-wrap:break-word; width:60px"><? echo $feeder_str; ?></div></td>
                    <td align="center" title="<? echo $data_all;?>"><div style="word-wrap:break-word; width:40px"><? echo $yarn_count; ?></div></td>
                    <td align="center"><div style="word-wrap:break-word; width:40px"><? echo $mc_dia; ?></div></td>
                    <td align="center"><div style="word-wrap:break-word; width:40px"><? echo $mc_gg; ?></div></td>
                  
                    <td align="right"><p><? echo $row[csf('packing_qnty')]; $tot_packing_qty+=$row[csf('packing_qnty')]; ?>&nbsp;</p></td>
                    <td align="right"><p><b><? echo number_format($row[csf('delivery_qty')],2,'.',''); $tot_delivery_qty+=$row[csf('delivery_qty')]; ?>&nbsp;</b></p></td>
                    <td align="right"><p><b><? echo number_format($row[csf('delivery_qtypcs')],2,'.',''); $tot_delivery_qtypcs+=$row[csf('delivery_qtypcs')]; ?>&nbsp;</b></p></td>
                    <td><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf('rate')],4,'.',''); ?>&nbsp;</p></td>
                    <td align="right"><p><b><? echo number_format($row[csf('amount')],2,'.','');  $total_amount += $row[csf('amount')]; ?>&nbsp;</b></p></td>

                    <td align="center"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
                  
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
                <td align="right" colspan="16"><strong>Total</strong></td>
                <td align="right"><? echo $tot_packing_qty; ?>&nbsp;</td>
                <td align="right"><b><? echo number_format($tot_delivery_qty,2,'.',''); ?></b></td>
                <td align="right"><b><? echo number_format($tot_delivery_qtypcs,2,'.',''); ?></b></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><b><? echo $format_total_amount=number_format($total_amount,0,'.',''); ?></b></td>
                <td>&nbsp;</td>
			</tr>
           <tr>
               <td colspan="23" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
           </tr>
        </table>
        <?
			$bill_no=$dataArray[0][csf('bill_no')];
			$sql_terms="Select id,terms from subcon_terms_condition where entry_form=1 and bill_no='$bill_no' ";
			$result_sql_terms =sql_select($sql_terms);

			$i=1;
			if(count($result_sql_terms)>0)
			{
				?>
                <table width="930" align="left" > 
                    <tr><td colspan="2">&nbsp;</td> </tr>
                    <tr><td colspan="2" align="center"><b>TERMS & CONDITION</b></td></tr>
					<?
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
					?>
        		</table><?
			}
			?>
        <br>
        <? if($data[4]==1) 
		{ 
			if($dataArray[0][csf('bill_for')]!=3)
			{
			?>
			<table align="left"  cellspacing="0" width="980"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="120">Order No</th>
					<th width="110">Buyer Name</th>
					<th width="100">Grey Required (KG)</th>
					<th width="100">Charge Required (USD)</th>
					<th width="100">Bill Qty (KG)</th> 
					<th width="100">Bill Amount (USD)</th>
					<th width="100">Balance Qty (KG)</th>
					<th width="">Balance Amount (USD)</th>
				</thead>
				<tbody>
				<?
				$grey_req_arr=array();
				$grey_req_sql="select po_break_down_id, sum(requirment) as grey_req from wo_pre_cos_fab_co_avg_con_dtls group by po_break_down_id";
				$grey_req_sql_result =sql_select($grey_req_sql);
				foreach($grey_req_sql_result as $row)
				{
					$grey_req_arr[$row[csf('po_break_down_id')]]=$row[csf('grey_req')];
				}
				
				$charge_req_arr=array(); 
				$charge_req_sql="select job_no, sum(amount) as charge_req from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0 group by job_no";
				$charge_req_sql_result =sql_select($charge_req_sql);
				foreach($charge_req_sql_result as $row)
				{
					$charge_req_arr[$row[csf('job_no')]]=$row[csf('charge_req')];
				}
				
				$bill_arr=array(); 
				$bill_sql="select b.order_id, sum(b.delivery_qty) as bill_qty, sum(b.amount) as bill_amount from  subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.party_source=1 and b.process_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id";
				$bill_sql_result =sql_select($bill_sql);
				foreach($bill_sql_result as $row)
				{
					$bill_arr[$row[csf('order_id')]]['bill_qty']=$row[csf('bill_qty')];
					$bill_arr[$row[csf('order_id')]]['bill_amount']=$row[csf('bill_amount')];
				}
				
				$currency_rate=set_conversion_rate( 2, $dataArray[0][csf('bill_date')] );
				$costingper_id_arr=return_library_array( "select job_no,costing_per_id from wo_pre_cost_dtls",'job_no','costing_per_id');
				
				$ex_po=array_unique(explode(",",$po_id)); $k=1;
				foreach($ex_po as $po_id)
				{
					if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$po_quantity=$order_array[$po_id]['po_quantity'];
					
					$costing_per_id=$costingper_id_arr[$order_array[$po_id]['job']];
					$dzn_qnty=0;
					if($costing_per_id==1) $dzn_qnty=12;
					else if($costing_per_id==3) $dzn_qnty=12*2;
					else if($costing_per_id==4) $dzn_qnty=12*3;
					else if($costing_per_id==5) $dzn_qnty=12*4;
					else $dzn_qnty=1;
					$dzn_qnty_req=$dzn_qnty*$order_array[$po_id]['ratio'];
					$grey_req=($po_quantity/$dzn_qnty_req)*$grey_req_arr[$po_id];
					$charge_req=($po_quantity/$dzn_qnty_req)*$charge_req_arr[$order_array[$po_id]['job']]*$currency_rate;
					
					$bill_qty=$bill_arr[$po_id]['bill_qty'];
					$bill_amount=$bill_arr[$po_id]['bill_amount']*$currency_rate;
					$balance_qty=$grey_req-$bill_qty;
					$balance_amount=$charge_req-$bill_amount;
				?>
					<tr bgcolor="<? echo $bgcolor; ?>"> 
						<td><div style="word-wrap:break-word; width:120px"><? echo $order_array[$po_id]['po_number']; ?></div></td>
						<td><p><? echo $order_array[$po_id]['buyer_name']; ?></p></td>
						
						<td align="right"><p><? echo number_format($grey_req,2,'.',''); ?></p></td>
						<td align="right"><p><? echo number_format($charge_req,2,'.',''); ?></p></td>
						
						<td align="right"><p><? echo number_format($bill_qty,2,'.',''); ?></p></td>
						<td align="right"><p><? echo number_format($bill_amount,2,'.',''); ?></p></td>
						
						<td align="right"><p><? echo number_format($balance_qty,2,'.',''); ?></p></td>
						<td align="right"><p><? echo number_format($balance_amount,2,'.',''); ?></p></td>
					</tr>
				<?
				}
				?>
				</tbody>
			</table>
        <? } 
		} ?>
        <br>
		 <? echo signature_table(220, $data[0], "1400px"); ?>
   </div>
   </div>
	<?
    exit();
}

if($action=="knitting_bill_print_6") //shariar aukotex
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$party_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name");
	$yarn_desc_arr=return_library_array( "select id,yarn_description from lib_subcon_charge",'id','yarn_description');
	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$yearn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	
	$sql_mst="Select id, bill_no, bill_date, party_id, party_source, location_id,party_location_id, attention, bill_for from subcon_inbound_bill_mst where company_id=$data[0] and id='$data[1]' and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql_mst);
	$mst_id=$dataArray[0][csf('id')];
	
	$sql_result =sql_select("select id, delivery_id, delivery_date, challan_no, order_id, item_id, uom, packing_qnty, delivery_qty, delivery_qtypcs, rate, amount, remarks, currency_id, process_id, body_part_id, febric_description_id from subcon_inbound_bill_dtls where mst_id='$mst_id' and process_id='2' and status_active=1 and is_deleted=0 order by challan_no ASC"); 
	$po_id_arr=array();
	foreach($sql_result as $row)
	{
		$po_id_arr[$row[csf('order_id')]]=$row[csf('order_id')];
		$receive_id_arr[$row[csf('delivery_id')]]=$row[csf('delivery_id')];
	}
	?>
    <div style="width:1110px; margin-left:20px;">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td  width="110" align="right"> 
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
                        <!-- <tr>
                        	<td class="form_caption"><?// echo show_company($data[0],'',''); ?></td>  
                        </tr> -->
                        <tr>
                            <td align="center" style="font-size:18px"><strong><? echo $data[3]; ?></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table width="1110" cellspacing="0" align="" border="0">
            <tr>
                <td width="150"  valign="top"><strong>Bill No :</strong></td> <td width="200"><? echo $dataArray[0][csf('bill_no')]; ?></td>
                <td width="150"><strong>Bill Date: </strong></td><td width="200px"> <? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
                <td width="150"><strong>Source :</strong></td> <td width="200"><? echo $knitting_source[$dataArray[0][csf('party_source')]]; ?></td>
            </tr>
            <tr>
				<?
                if($dataArray[0][csf('attention')]!='')  $attention='('.$dataArray[0][csf('attention')].')';
                else $attention='';
                if($dataArray[0][csf('party_source')]==2)
                {
					$party_add=$dataArray[0][csf('party_id')];
					$nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add"); 
					foreach ($nameArray as $result)
					{ 
						$address="";
						if($result!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
					}
					$party_name=$party_library[$dataArray[0][csf('party_id')]].'<br/>Address:'.$address.' '.$attention;
					$party_location='';
                }
                else
                {
					$party_name=$company_library[$dataArray[0][csf('party_id')]].' '.$attention;
					$party_location=$location_arr[$dataArray[0][csf('party_location_id')]];
                }
                ?>
                <td  width="150"  valign="top"><strong>Party Name: </strong></td><td width="200"  valign="top"><p> <? echo $party_name; ?></p></td>
                <td width="180"  valign="top"><strong></strong></td><td  width="200"  valign="top"> <? //echo $party_location; ?></td> 
                <td width="150"  valign="top"><strong>Bill For : </strong></td><td width="200"  valign="top"> <? echo $bill_for[$dataArray[0][csf('bill_for')]]; ?></td>
            </tr>
        </table>
        <br>
        <div style="width:100%;" >
		<table cellspacing="0" width="1110"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center" style="font-size:16px">
                <th width="30">SL</th>
                <th width="40">Sys. Challan</th>
                <th width="55">D. Date</th>
                <th width="60">Order</th> 
                <th width="60">Buyer</th>
                <th width="60">Style</th>
                <th width="40">Job</th>
                <th width="35">Year</th>
                <th width="150">Fabric Description</th>
                <th width="100">Fabric Color</th>
                <th width="40">Yarn Count</th>
                <th width="40">MC Dia</th>
                <th width="40">MC Gauge</th>
                <th width="25">Roll</th>
                <th width="55">D. Qty (W)</th>
                <th width="55">D. Qty (P)</th>
                <th width="30">UOM</th>
                <th width="40">Rate</th>
                <th width="70">Amount</th>
                <th>Currency</th>
            </thead>
		 	<? 
		 	if($db_type==0)
				$job_year="YEAR(a.insert_date) as year";
			else
				$job_year="to_char(a.insert_date,'YYYY') as year";
		 	
			if($dataArray[0][csf('party_source')]==2)
			{
				$order_array=array();
				 $order_sql="select a.job_no_prefix_num,b.id,$job_year, b.order_no, b.order_uom, b.cust_buyer, b.cust_style_ref from subcon_ord_dtls b,subcon_ord_mst a where b.job_no_mst=a.subcon_job and b.status_active=1 and b.is_deleted=0";
				$order_sql_result =sql_select($order_sql);
				foreach($order_sql_result as $row)
				{
					$order_array[$row[csf('id')]]['po_number']=$row[csf('order_no')];
					$order_array[$row[csf('id')]]['buyer_name']=$row[csf('cust_buyer')];
					$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('cust_style_ref')];
					$order_array[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
					$order_array[$row[csf('id')]]['year']=$row[csf('year')];
					$job_array[$row[csf('job_no')]]=$row[csf('job_no')];
				}
				unset($order_sql_result);
				$const_comp_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
				$collar_cuff_arr=return_library_array( "select id,collar_cuff from subcon_delivery_dtls",'id','collar_cuff');
				
				$prodData="select order_id, cons_comp_id, color_id, yrn_count_id, machine_dia, machine_gg from subcon_production_dtls where product_type=2 and is_deleted=0 and  status_active=1";
				$prod_sql_result =sql_select($prodData);
				$subcon_exData_arr=array();
				foreach($prod_sql_result as $row)
				{
					$subcon_exData_arr[$row[csf('order_id')]][$row[csf('cons_comp_id')]]['fabcolor'].=$row[csf('color_id')].',';
					$subcon_exData_arr[$row[csf('order_id')]][$row[csf('cons_comp_id')]]['yrnCount'].=$row[csf('yrn_count_id')].',';
					$subcon_exData_arr[$row[csf('order_id')]][$row[csf('cons_comp_id')]]['mcdia'].=$row[csf('machine_dia')].',';
					$subcon_exData_arr[$row[csf('order_id')]][$row[csf('cons_comp_id')]]['mcgg'].=$row[csf('machine_gg')].',';
				}
				unset($prod_sql_result);
			}
			
			else if($dataArray[0][csf('party_source')]==1)
			{
				//for wo_po_break_down id
				$poCondition = '';
				if(count($po_id_arr)>0)
				{
					$poCond = '';
					$allPo = implode(",", $po_id_arr);
					if($db_type==2 && count($po_id_arr)>999)
					{
						$allPoChunk=array_chunk($po_id_arr,999) ;
						foreach($allPoChunk as $chunk_arr)
						{
							$poCond.=" b.id in(".implode(",",$chunk_arr).") or ";
						}
			
						$poCondition.=" and (".chop($poCond,'or ').")";
					}
					else
					{
						$poCondition=" and b.id in(".$allPo.")";
					}
				}
				
				$order_array=array();
				$job_sql="select a.job_no, $job_year, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.total_set_qnty as ratio, b.id, b.po_number, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active!=0 and a.is_deleted=0 and  b.status_active!=0 and b.is_deleted=0 ".$poCondition."";//and a.company_name=$data[0]
				$job_sql_result =sql_select($job_sql);
				foreach($job_sql_result as $row)
				{
					$order_array[$row[csf('id')]]['buyer_name']=$party_library[$row[csf('buyer_name')]];
					$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
					$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
					$order_array[$row[csf('id')]]['job']=$row[csf('job_no')];
					$order_array[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
					$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
					$order_array[$row[csf('id')]]['ratio']=$row[csf('ratio')];
					$order_array[$row[csf('id')]]['year']=$row[csf('year')];
					$job_array[$row[csf('job_no')]]=$row[csf('job_no')];
					
				}
				unset($job_sql_result);
				
				$const_comp_arr=return_library_array( "select id,product_name_details from product_details_master",'id','product_name_details');
				$knit_plan_arr=array();
				$knit_plan="select id, feeder from ppl_planning_info_entry_dtls where status_active=1 and is_deleted=0 and feeder!=0";
				$knit_plan_res=sql_select($knit_plan);
				foreach($knit_plan_res as $row)
				{
					$knit_plan_arr[$row[csf('id')]]=$row[csf('feeder')];
				}
				unset($knit_plan_res);

				$production_arr=array();
				//echo "select recv_number_prefix_num, receive_date, challan_no from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]' and location_id='".$dataArray[0][csf('location_id')]."' and entry_form=22 and status_active=1 and is_deleted=0 and recv_number_prefix_num=30";
				$production_sql=sql_select("select id, booking_id, booking_no from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]'   and entry_form in (2) and receive_basis=2 and status_active=1 and is_deleted=0"); //and a.company_id='$data[3]' and a.knitting_company='$data[0]' and a.location_id='$data[1]'
				//echo "select id, booking_id, booking_no from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]'   and entry_form in (2) and receive_basis=2 and status_active=1 and is_deleted=0";
				foreach($production_sql as $row)
				{
					$production_arr[$row[csf('id')]]=$feeder[$knit_plan_arr[$row[csf('booking_id')]]];
				}
				unset($production_sql);
				
				//for inv_receive_master id
				$rcvIdCondition = '';
				if(count($receive_id_arr)>0)
				{
					$rcvIdCond = '';
					$allRcvId = implode(",", $receive_id_arr );
					if($db_type==2 && count($receive_id_arr)>999)
					{
						$allRcvIdChunk=array_chunk($receive_id_arr,999) ;
						foreach($allRcvIdChunk as $chunk_arr)
						{
							$rcvIdCond.=" a.id in(".implode(",",$chunk_arr).") or ";
						}
			
						$rcvIdCondition.=" and (".chop($rcvIdCond,'or ').")";
					}
					else
					{
						$rcvIdCondition=" and a.id in(".$allRcvId.")";
					}
				}
				
				$rec_data_arr=array();
				$recChallan_arr=array();
				 $res_sql="select c.barcode_no,a.id, a.recv_number_prefix_num, a.receive_date, a.entry_form, a.challan_no, a.receive_basis, a.booking_id, a.booking_no, b.prod_id, b.body_part_id, b.febric_description_id,c.booking_no as prog_no, c.po_breakdown_id as po_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.company_id='".$dataArray[0][csf('party_id')]."' and a.knitting_company='$data[0]' and a.location_id='".$dataArray[0][csf('party_location_id')]."' and a.id=b.mst_id and b.id=c.DTLS_ID and a.id=c.mst_id and b.trans_id > 0 and a.entry_form in(2,22,58) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".$rcvIdCondition."";	
				$res_sql_res=sql_select($res_sql);
				foreach($res_sql_res as $row)
				{
					$barCodeArr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
				}
				
				//for pro_roll_details barcode_no
				$barcodeCondition = '';
				if(count($barCodeArr)>0)
				{
					$barcodeCond = '';
					$all_barcode_nos = implode(",", $barCodeArr);
					if($db_type==2 && count($barCodeArr)>999)
					{
						$all_barcode_no_chunk=array_chunk($barCodeArr,999) ;
						foreach($all_barcode_no_chunk as $chunk_arr)
						{
							$barcodeCond.=" c.barcode_no in(".implode(",",$chunk_arr).") or ";
						}
			
						$barcodeCondition.=" and (".chop($barcodeCond,'or ').")";
					}
					else
					{
						$barcodeCondition=" and c.barcode_no in(".$all_barcode_nos.")";
					}
				}

				$barCodeWiseProductionSql="select a.recv_number,c.barcode_no, c.po_breakdown_id as po_id,c.booking_no, b.id, b.body_part_id,b.prod_id,b.febric_description_id,b.yarn_count, b.color_id, b.machine_dia, b.machine_gg from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.company_id='".$dataArray[0][csf('party_id')]."' and a.knitting_company='".$data[0]."' and a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.entry_form in (2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".$barcodeCondition."";
				$barCodeWiseProductionResult=sql_select($barCodeWiseProductionSql);
				foreach($barCodeWiseProductionResult as $row)
				{
					$barCodeDataArr2[$row[csf('barcode_no')]][$row[csf('po_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['yarn_count']=$row[csf('yarn_count')];
					//echo $row[csf('recv_number')].'='.$row[csf('yarn_count')].'<br>';
					$barCodeDataArr[$row[csf('barcode_no')]]['color_id']=$row[csf('color_id')];
					$barCodeDataArr[$row[csf('barcode_no')]]['machine_dia']=$row[csf('machine_dia')];
					$barCodeDataArr[$row[csf('barcode_no')]]['machine_gg']=$row[csf('machine_gg')];
					$barCodeDataArr[$row[csf('barcode_no')]]['plan_id']=$row[csf('booking_no')];
				}
				//echo "<pre>";
				//print_r($barCodeDataArr); die;
				
				foreach($res_sql_res as $row)
				{
					$sys_challan=$row[csf('id')];
					$recChallan_arr[$sys_challan][change_date_format($row[csf('receive_date')])]=$row[csf('challan_no')];
					$yarn_count="";
					$yarn_count=$barCodeDataArr2[$row[csf('barcode_no')]][$row[csf('po_id')]][$row[csf('prog_no')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['yarn_count'];
				//	echo $sys_challan.'='. $yarn_count.'='.$row[csf('po_id')].'<br> ';
					
					$rec_data_arr[$sys_challan][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['yarn_count'].=$yarn_count.',';
					$rec_data_arr[$sys_challan][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['color_id'].=$barCodeDataArr[$row[csf('barcode_no')]]['color_id'].',';
					$rec_data_arr[$sys_challan][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['machine_dia'].=$barCodeDataArr[$row[csf('barcode_no')]]['machine_dia'].',';
					
					$rec_data_arr[$sys_challan][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['machine_gg'].=$barCodeDataArr[$row[csf('barcode_no')]]['machine_gg'].',';
					
					
					if(($row[csf('receive_basis')]==9 || $row[csf('receive_basis')]==10) && ($row[csf('entry_form')]==22 || $row[csf('entry_form')]==58))
					{
						$rec_data_arr[$sys_challan][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['feeder'].=$feeder[$knit_plan_arr[$barCodeDataArr[$row[csf('barcode_no')]]['plan_id']]].',';
					}
					else if($row[csf('receive_basis')]==2 && $row[csf('entry_form')]==2)
					{
						$rec_data_arr[$sys_challan][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['feeder'].=$production_arr[$row[csf('booking_id')]].',';
					}
					$sample_rec_data_arr[$sys_challan][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['color_id'].=$barCodeDataArr[$row[csf('barcode_no')]]['color_id'].',';
					$sample_rec_data_arr[$sys_challan][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['yarn_count'].=$yarn_count.',';
					$sample_rec_data_arr[$sys_challan][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['machine_dia'].=$barCodeDataArr[$row[csf('barcode_no')]]['machine_dia'].',';
					
					$sample_rec_data_arr[$sys_challan][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['machine_gg'].=$barCodeDataArr[$row[csf('barcode_no')]]['machine_gg'].',';
					$sample_rec_data_arr[$sys_challan][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['feeder'].=$production_arr[$row[csf('booking_id')]].',';
				}
				unset($res_sql_res);
				$internal_ref_sql="select job_no,internal_ref from wo_order_entry_internal_ref where job_no in('".implode("','",$job_array)."')";
				$internal_ref_sql_result=sql_select($internal_ref_sql);
				foreach($internal_ref_sql_result as $row)
				{
					$internal_ref_arr[$row[csf('job_no')]][$row[csf('internal_ref')]]=$row[csf('internal_ref')];
				}
			}
			if($dataArray[0][csf('bill_for')]==3)
			{
				$buyer_id_arr=array();
				$sql_non_booking=sql_select("select b.booking_no,b.booking_without_order,c.grouping,a.recv_number_prefix_num, a.receive_date, c.buyer_id,e.style_ref_no from inv_receive_master a,pro_roll_details b,wo_non_ord_samp_booking_mst c,wo_non_ord_samp_booking_dtls d,sample_development_mst e  where a.id=b.mst_id and c.booking_no=b.booking_no and c.booking_no=d.booking_no and e.id=d.style_id and b.entry_form=58 and a.company_id='".$dataArray[0][csf('party_id')]."' and a.knitting_company='$data[0]' and a.entry_form=58  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.booking_no,b.booking_without_order,a.recv_number_prefix_num, a.receive_date, c.buyer_id,c.grouping,e.style_ref_no");
				
				foreach($sql_non_booking as $row)
				{
					$buyer_id_arr[$row[csf('recv_number_prefix_num')]][change_date_format($row[csf('receive_date')])]=$party_library[$row[csf('buyer_id')]];
					$sample_dtls_ref_arr[$row[csf('recv_number_prefix_num')]][$row[csf('receive_date')]]['ref']=$row[csf('grouping')];
					$sample_dtls_ref_arr[$row[csf('recv_number_prefix_num')]][$row[csf('receive_date')]]['style']=$row[csf('style_ref_no')];
				}
			}
			
			//$order_array[$row[csf('id')]]['job']=$row[csf('job_no')];
			$po_id="";
			$i=1;
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($po_id=="") $po_id=$row[csf('order_id')]; else $po_id.=','.$row[csf('order_id')];
				$buyer_id_name="";
				if($dataArray[0][csf('bill_for')]==3)
				{
					$buyer_id_name=$buyer_id_arr[$row[csf('challan_no')]][change_date_format($row[csf('delivery_date')])];
					$internal_refs=$sample_dtls_ref_arr[$row[csf('challan_no')]][$row[csf('delivery_date')]]['ref'];
					$style_ref_no=$sample_dtls_ref_arr[$row[csf('challan_no')]][$row[csf('delivery_date')]]['style'];
					//$samole_rec_data_arr[$sys_challan][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['color_id'];
					
					//$sam_fab_color=array_filter(array_unique(explode(",",$samole_rec_data_arr[$row[csf('delivery_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['color_id'])));
					//print_r($sam_fab_color);
				}
				else
				{
					$buyer_id_name=$order_array[$row[csf('order_id')]]['buyer_name'];
					$internal_refs=implode(',',$internal_ref_arr[$order_array[$row[csf('order_id')]]['job']]);
					$style_ref_no=$order_array[$row[csf('order_id')]]['style_ref_no'];
				}
				
				$fab_color=""; $feeder_str=""; $yarn_count=""; $mc_dia=''; $mc_gg="";
				if($dataArray[0][csf('party_source')]==1)
				{
					if($dataArray[0][csf('bill_for')]==3)
					{
					$fab_color=array_filter(array_unique(explode(",",$sample_rec_data_arr[$row[csf('delivery_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['color_id'])));
					$feeder_str=implode(",",array_filter(array_unique(explode(",",$sample_rec_data_arr[$row[csf('delivery_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['feeder']))));
					 $yarn_count_id=array_filter(array_unique(explode(",",$sample_rec_data_arr[$row[csf('delivery_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['yarn_count'])));
					 $mc_dia=implode(",",array_filter(array_unique(explode(",",$sample_rec_data_arr[$row[csf('delivery_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['machine_dia']))));
					//echo $row[csf('challan_no')].'='.$row[csf('item_id')].'='.$row[csf('body_part_id')].'='.$row[csf('febric_description_id')].'<br>';
					$mc_gg=implode(",",array_filter(array_unique(explode(",",$sample_rec_data_arr[$row[csf('delivery_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['machine_gg']))));
					}
					else
					{
						$fab_color=array_filter(array_unique(explode(",",$rec_data_arr[$row[csf('delivery_id')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['color_id'])));
						$feeder_str=implode(",",array_filter(array_unique(explode(",",$rec_data_arr[$row[csf('delivery_id')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['feeder']))));
					  $yarn_count_id=array_filter(array_unique(explode(",",$rec_data_arr[$row[csf('delivery_id')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['yarn_count'])));
					  $mc_dia=implode(",",array_filter(array_unique(explode(",",$rec_data_arr[$row[csf('delivery_id')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['machine_dia']))));
					//echo $row[csf('challan_no')].'='.$row[csf('item_id')].'='.$row[csf('body_part_id')].'='.$row[csf('febric_description_id')].'<br>';
					$mc_gg=implode(",",array_filter(array_unique(explode(",",$rec_data_arr[$row[csf('delivery_id')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['machine_gg']))));
					}
					
					$color_srt_arr=array();
					foreach ($fab_color as $color_id){
						$color_srt_arr[$color_id]=$color_arr[$color_id];
					}
					$fab_color=implode(",",$color_srt_arr);
					
					
					//echo $dataArray[0][csf('party_source')].',';
					foreach($yarn_count_id as $count_id)
					{
						if($yarn_count=="") $yarn_count=$yearn_count_arr[$count_id]; else $yarn_count.=', '.$yearn_count_arr[$count_id];
					}
					$yarn_count=implode(",",explode(',',$yarn_count));
					
					
				}
				else if($dataArray[0][csf('party_source')]==2)
				{
					$fab_color=array_filter(array_unique(explode(",",$subcon_exData_arr[$row[csf('order_id')]][$row[csf('item_id')]]['fabcolor'])));
					
					$color_srt_arr=array();
					foreach ($fab_color as $color_id){
						$color_srt_arr[$color_id]=$color_arr[$color_id];
					}
					$fab_color=implode(",",$color_srt_arr);
					
					
					$yarn_count_id=array_filter(array_unique(explode(",",$subcon_exData_arr[$row[csf('order_id')]][$row[csf('item_id')]]['yrnCount'])));
					foreach($yarn_count_id as $count_id)
					{
						if($yarn_count=="") $yarn_count=$yearn_count_arr[$count_id]; else $yarn_count.=', '.$yearn_count_arr[$count_id];
					}
					$yarn_count=implode(",",explode(',',$yarn_count));
					
					$mc_dia=implode(",",array_filter(array_unique(explode(",",$subcon_exData_arr[$row[csf('order_id')]][$row[csf('item_id')]]['mcdia']))));
					$mc_gg=implode(",",array_filter(array_unique(explode(",",$subcon_exData_arr[$row[csf('order_id')]][$row[csf('item_id')]]['mcgg']))));
				}
				//$data_all=$row[csf('delivery_id')].'='.$row[csf('item_id')].'='.$row[csf('body_part_id')].'='.$row[csf('febric_description_id')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:16px"> 
                    <td><? echo $i; ?></td>
                    <td><p><? echo $row[csf('challan_no')]; ?></p></td>
                    <td><p><? echo change_date_format($row[csf('delivery_date')]); ?></p></td>
                    <td><div style="word-wrap:break-word; width:60px"><? echo $order_array[$row[csf('order_id')]]['po_number']; ?></div></td>
                    <td><div style="word-wrap:break-word; width:60px"><? echo $buyer_id_name; ?></div></td>
                    <td><div style="word-wrap:break-word; width:60px"><? echo $style_ref_no; ?></div></td>
                    <td align="center"><div style="word-wrap:break-word; width:40px"><? echo $order_array[$row[csf('order_id')]]['job_no_prefix_num']; ?></div></td>
                   
                    
                    <td align="center"><div style="word-wrap:break-word; width:35px"><? echo $order_array[$row[csf('order_id')]]['year']; ?></div></td>
                    <td><div style="word-wrap:break-word; width:150px"><? echo $const_comp_arr[$row[csf('item_id')]]; ?></div></td>
                    
                    <td align="center"><div style="word-wrap:break-word; width:100px"><? echo $fab_color; ?></div></td>
                    <td align="center" title="<? echo $data_all;?>"><div style="word-wrap:break-word; width:40px"><? echo $yarn_count; ?></div></td>
                    <td align="center"><div style="word-wrap:break-word; width:40px"><? echo $mc_dia; ?></div></td>
                    <td align="center"><div style="word-wrap:break-word; width:40px"><? echo $mc_gg; ?></div></td>
                  
                    <td align="right"><p><? echo $row[csf('packing_qnty')]; $tot_packing_qty+=$row[csf('packing_qnty')]; ?>&nbsp;</p></td>
                    <td align="right"><p><b><? echo number_format($row[csf('delivery_qty')],2,'.',''); $tot_delivery_qty+=$row[csf('delivery_qty')]; ?>&nbsp;</b></p></td>
                    <td align="right"><p><b><? echo number_format($row[csf('delivery_qtypcs')],2,'.',''); $tot_delivery_qtypcs+=$row[csf('delivery_qtypcs')]; ?>&nbsp;</b></p></td>
                    <td><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf('rate')],4,'.',''); ?>&nbsp;</p></td>
                    <td align="right"><p><b><? echo number_format($row[csf('amount')],2,'.','');  $total_amount += $row[csf('amount')]; ?>&nbsp;</b></p></td>

                    <td align="center"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
                  
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
                <td align="right" colspan="13"><strong>Total</strong></td>
                <td align="right"><? echo $tot_packing_qty; ?>&nbsp;</td>
                <td align="right"><b><? echo number_format($tot_delivery_qty,2,'.',''); ?></b></td>
                <td align="right"><b><? echo number_format($tot_delivery_qtypcs,2,'.',''); ?></b></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><b><? echo $format_total_amount=number_format($total_amount,0,'.',''); ?></b></td>
                <td>&nbsp;</td>
			</tr>
           <tr>
               <td colspan="23" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
           </tr>
        </table>
        <?
			$bill_no=$dataArray[0][csf('bill_no')];
			$sql_terms="Select id,terms from subcon_terms_condition where entry_form=1 and bill_no='$bill_no' ";
			$result_sql_terms =sql_select($sql_terms);

			$i=1;
			if(count($result_sql_terms)>0)
			{
				?>
                <table width="930" align="left" > 
                    <tr><td colspan="2">&nbsp;</td> </tr>
                    <tr><td colspan="2" align="center"><b>TERMS & CONDITION</b></td></tr>
					<?
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
					?>
        		</table><?
			}
			?>
        <br>
        <? if($data[4]==1) 
		{ 
			if($dataArray[0][csf('bill_for')]!=3)
			{
			?>
			<table align="left"  cellspacing="0" width="980"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="120">Order No</th>
					<th width="110">Buyer Name</th>
					<th width="100">Grey Required (KG)</th>
					<th width="100">Charge Required (USD)</th>
					<th width="100">Bill Qty (KG)</th> 
					<th width="100">Bill Amount (USD)</th>
					<th width="100">Balance Qty (KG)</th>
					<th width="">Balance Amount (USD)</th>
				</thead>
				<tbody>
				<?
				$grey_req_arr=array();
				$grey_req_sql="select po_break_down_id, sum(requirment) as grey_req from wo_pre_cos_fab_co_avg_con_dtls group by po_break_down_id";
				$grey_req_sql_result =sql_select($grey_req_sql);
				foreach($grey_req_sql_result as $row)
				{
					$grey_req_arr[$row[csf('po_break_down_id')]]=$row[csf('grey_req')];
				}
				
				$charge_req_arr=array(); 
				$charge_req_sql="select job_no, sum(amount) as charge_req from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0 group by job_no";
				$charge_req_sql_result =sql_select($charge_req_sql);
				foreach($charge_req_sql_result as $row)
				{
					$charge_req_arr[$row[csf('job_no')]]=$row[csf('charge_req')];
				}
				
				$bill_arr=array(); 
				$bill_sql="select b.order_id, sum(b.delivery_qty) as bill_qty, sum(b.amount) as bill_amount from  subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.party_source=1 and b.process_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id";
				$bill_sql_result =sql_select($bill_sql);
				foreach($bill_sql_result as $row)
				{
					$bill_arr[$row[csf('order_id')]]['bill_qty']=$row[csf('bill_qty')];
					$bill_arr[$row[csf('order_id')]]['bill_amount']=$row[csf('bill_amount')];
				}
				
				$currency_rate=set_conversion_rate( 2, $dataArray[0][csf('bill_date')] );
				$costingper_id_arr=return_library_array( "select job_no,costing_per_id from wo_pre_cost_dtls",'job_no','costing_per_id');
				
				$ex_po=array_unique(explode(",",$po_id)); $k=1;
				foreach($ex_po as $po_id)
				{
					if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$po_quantity=$order_array[$po_id]['po_quantity'];
					
					$costing_per_id=$costingper_id_arr[$order_array[$po_id]['job']];
					$dzn_qnty=0;
					if($costing_per_id==1) $dzn_qnty=12;
					else if($costing_per_id==3) $dzn_qnty=12*2;
					else if($costing_per_id==4) $dzn_qnty=12*3;
					else if($costing_per_id==5) $dzn_qnty=12*4;
					else $dzn_qnty=1;
					$dzn_qnty_req=$dzn_qnty*$order_array[$po_id]['ratio'];
					$grey_req=($po_quantity/$dzn_qnty_req)*$grey_req_arr[$po_id];
					$charge_req=($po_quantity/$dzn_qnty_req)*$charge_req_arr[$order_array[$po_id]['job']]*$currency_rate;
					
					$bill_qty=$bill_arr[$po_id]['bill_qty'];
					$bill_amount=$bill_arr[$po_id]['bill_amount']*$currency_rate;
					$balance_qty=$grey_req-$bill_qty;
					$balance_amount=$charge_req-$bill_amount;
				?>
					<tr bgcolor="<? echo $bgcolor; ?>"> 
						<td><div style="word-wrap:break-word; width:120px"><? echo $order_array[$po_id]['po_number']; ?></div></td>
						<td><p><? echo $order_array[$po_id]['buyer_name']; ?></p></td>
						
						<td align="right"><p><? echo number_format($grey_req,2,'.',''); ?></p></td>
						<td align="right"><p><? echo number_format($charge_req,2,'.',''); ?></p></td>
						
						<td align="right"><p><? echo number_format($bill_qty,2,'.',''); ?></p></td>
						<td align="right"><p><? echo number_format($bill_amount,2,'.',''); ?></p></td>
						
						<td align="right"><p><? echo number_format($balance_qty,2,'.',''); ?></p></td>
						<td align="right"><p><? echo number_format($balance_amount,2,'.',''); ?></p></td>
					</tr>
				<?
				}
				?>
				</tbody>
			</table>
        <? } 
		} ?>
        <br>
		 <? echo signature_table(220, $data[0], "1110px"); ?>
   </div>
   </div>
	<?
    exit();
}


if($action=="knitting_bill_print_2_08072020") 
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$party_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name");
	$yarn_desc_arr=return_library_array( "select id,yarn_description from lib_subcon_charge",'id','yarn_description');
	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$yearn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
	
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	
	$sql_mst="Select id, bill_no, bill_date, party_id, party_source, location_id,party_location_id, attention, bill_for from subcon_inbound_bill_mst where company_id=$data[0] and id='$data[1]' and status_active=1 and is_deleted=0";
	
	$dataArray=sql_select($sql_mst);
	
	$mst_id=$dataArray[0][csf('id')];
	$sql_result =sql_select("select id, delivery_id, delivery_date, challan_no, order_id, item_id, uom, packing_qnty, delivery_qty, delivery_qtypcs, rate, amount, remarks, currency_id, process_id, body_part_id, febric_description_id from subcon_inbound_bill_dtls  where mst_id='$mst_id' and process_id='2' and status_active=1 and is_deleted=0 order by challan_no ASC"); 
	$po_id_arr=array();
	foreach($sql_result as $row)
	{
		$po_id_arr[$row[csf('order_id')]]=$row[csf('order_id')];
		$receive_id_arr[$row[csf('delivery_id')]]=$row[csf('delivery_id')];
	}
	?>
    <div style="width:1150px; margin-left:20px;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="70" align="right"> 
            	<img  src='../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='70' width='200' />
            </td>
            <td>
                <table width="800" cellspacing="0" align="center">
                    <tr>
                    	<td align="center" style="font-size:20px"><strong ><? echo $company_library[$data[0]]; ?></strong></td>
                    </tr>
                    <tr>
                        <td align="center"  style="font-size:16px"><strong>Unit : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                    </tr>
                    <td class="form_caption"><? echo show_company($data[0],'',''); ?></td>  
                    </tr>
                    <tr>
                    	<td align="center" style="font-size:18px"><strong><? echo $data[3]; ?></strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
     <table width="1150" cellspacing="0" align="" border="0">
            <tr>
                <td width="150"  valign="top"><strong>Bill No :</strong></td> <td width="200"><? echo $dataArray[0][csf('bill_no')]; ?></td>
                <td width="150"><strong>Bill Date: </strong></td><td width="200px"> <? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
                <td width="150"><strong>Source :</strong></td> <td width="200"><? echo $knitting_source[$dataArray[0][csf('party_source')]]; ?></td>
            </tr>
            <tr>
				<?
                    if($dataArray[0][csf('attention')]!='')  $attention='('.$dataArray[0][csf('attention')].')';
                    else $attention='';
                        
                    if($dataArray[0][csf('party_source')]==2)
                    {
                        $party_add=$dataArray[0][csf('party_id')];
                        $nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add"); 
                        foreach ($nameArray as $result)
                        { 
                            $address="";
                            if($result!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                        }
                        $party_name=$party_library[$dataArray[0][csf('party_id')]].'<br/>Address:'.$address.' '.$attention;
						 $party_location='';
                    }
                    else
                    {
                        $party_name=$company_library[$dataArray[0][csf('party_id')]].' '.$attention;
						$party_location=$location_arr[$dataArray[0][csf('party_location_id')]];
                    }
                ?>
                 <td  width="150"  valign="top"><strong>Party Name: </strong></td><td width="200"  valign="top"><p> <? echo $party_name; ?></p></td>
				 <td width="180"  valign="top"><strong>Party Location: </strong></td><td  width="200"  valign="top"> <? echo $party_location; ?></td>
                <td width="150"  valign="top"><strong>Bill For : </strong></td><td width="200"  valign="top"> <? echo $bill_for[$dataArray[0][csf('bill_for')]]; ?></td>
            </tr>
        </table>
         <br>
	<div style="width:100%;" >
		<table cellspacing="0" width="1240"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center" style="font-size:16px">
                <th width="30">SL</th>
                <th width="40">Sys. Challan</th>
                <th width="40">Rec. Challan</th>
                <th width="55">D. Date</th>
                <th width="60">Order</th> 
                <th width="60">Buyer</th>
                <th width="60">Style</th>
                <th width="40">Job</th>
                <th width="50">Internal Ref.</th>
                <th width="35">Year</th>
                <th width="150">Fabric Description</th>
                
                <th width="100">Fabric Color</th>
                <th width="40">Feeder</th>
                <th width="40">Yarn Count</th>
                <th width="40">MC Dia</th>
                <th width="40">MC Gauge</th>
                
                <th width="25">Roll</th>
                <th width="55">D. Qty (W)</th>
                <th width="55">D. Qty (P)</th>
                <th width="30">UOM</th>
                <th width="40">Rate</th>
                <th width="70">Amount</th>
                <th>Currency</th>
            </thead>
		 <? 
		 if($db_type==0) $job_year="YEAR(a.insert_date) as year"; else $job_year="to_char(a.insert_date,'YYYY') as year";
		 	
			if($dataArray[0][csf('party_source')]==2)
			{
				$order_array=array();
				 $order_sql="select a.job_no_prefix_num,b.id,$job_year, b.order_no, b.order_uom, b.cust_buyer, b.cust_style_ref from subcon_ord_dtls b,subcon_ord_mst a where b.job_no_mst=a.subcon_job and b.status_active=1 and b.is_deleted=0";
				$order_sql_result =sql_select($order_sql);
				foreach($order_sql_result as $row)
				{
					$order_array[$row[csf('id')]]['po_number']=$row[csf('order_no')];
					$order_array[$row[csf('id')]]['buyer_name']=$row[csf('cust_buyer')];
					$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('cust_style_ref')];
					$order_array[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
					$order_array[$row[csf('id')]]['year']=$row[csf('year')];
					$job_array[$row[csf('job_no')]]=$row[csf('job_no')];
				}
				unset($order_sql_result);
				$const_comp_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
				$collar_cuff_arr=return_library_array( "select id,collar_cuff from subcon_delivery_dtls",'id','collar_cuff');
				
				$prodData="select order_id, cons_comp_id, color_id, yrn_count_id, machine_dia, machine_gg from subcon_production_dtls where product_type=2 and is_deleted=0 and  status_active=1";
				$prod_sql_result =sql_select($prodData);
				$subcon_exData_arr=array();
				foreach($prod_sql_result as $row)
				{
					$subcon_exData_arr[$row[csf('order_id')]][$row[csf('cons_comp_id')]]['fabcolor'].=$row[csf('color_id')].',';
					$subcon_exData_arr[$row[csf('order_id')]][$row[csf('cons_comp_id')]]['yrnCount'].=$row[csf('yrn_count_id')].',';
					$subcon_exData_arr[$row[csf('order_id')]][$row[csf('cons_comp_id')]]['mcdia'].=$row[csf('machine_dia')].',';
					$subcon_exData_arr[$row[csf('order_id')]][$row[csf('cons_comp_id')]]['mcgg'].=$row[csf('machine_gg')].',';
				}
				unset($prod_sql_result);
			}
			else if($dataArray[0][csf('party_source')]==1)
			{
				$order_array=array();
				$job_sql="select a.job_no, $job_year, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.total_set_qnty as ratio, b.id, b.po_number, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active!=0 and a.is_deleted=0 and  b.status_active!=0 and b.is_deleted=0 and b.id in(".implode(',',$po_id_arr).")";//and a.company_name=$data[0]
				$job_sql_result =sql_select($job_sql);
				foreach($job_sql_result as $row)
				{
					$order_array[$row[csf('id')]]['buyer_name']=$party_library[$row[csf('buyer_name')]];
					$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
					$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
					$order_array[$row[csf('id')]]['job']=$row[csf('job_no')];
					$order_array[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
					$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
					$order_array[$row[csf('id')]]['ratio']=$row[csf('ratio')];
					$order_array[$row[csf('id')]]['year']=$row[csf('year')];
					$job_array[$row[csf('job_no')]]=$row[csf('job_no')];
					
				}
				unset($job_sql_result);
				$const_comp_arr=return_library_array( "select id,product_name_details from product_details_master",'id','product_name_details');
				
				$knit_plan_arr=array();
				$knit_plan="select id, feeder from ppl_planning_info_entry_dtls where status_active=1 and is_deleted=0 and feeder!=0";
				$knit_plan_res=sql_select($knit_plan);
				foreach($knit_plan_res as $row)
				{
					$knit_plan_arr[$row[csf('id')]]=$row[csf('feeder')];
				}
				unset($knit_plan_res);
				
				
				$production_arr=array();
				//echo "select recv_number_prefix_num, receive_date, challan_no from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]' and location_id='".$dataArray[0][csf('location_id')]."' and entry_form=22 and status_active=1 and is_deleted=0 and recv_number_prefix_num=30";
				
				$production_sql=sql_select("select id, booking_id, booking_no from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]' and location_id='".$dataArray[0][csf('location_id')]."' and entry_form in (2) and receive_basis=2 and status_active=1 and is_deleted=0"); //and a.company_id='$data[3]' and a.knitting_company='$data[0]' and a.location_id='$data[1]'
				foreach($production_sql as $row)
				{
					$production_arr[$row[csf('id')]]=$feeder[$knit_plan_arr[$row[csf('booking_id')]]];
				}
				unset($production_sql);
				
				$rec_data_arr=array(); $recChallan_arr=array();
				$res_sql="select c.barcode_no,a.id, a.recv_number_prefix_num, a.receive_date, a.entry_form, a.challan_no, a.receive_basis, a.booking_id, a.booking_no, b.prod_id, b.body_part_id, b.febric_description_id,c.booking_no as prog_no, c.po_breakdown_id as po_id from inv_receive_master a, pro_grey_prod_entry_dtls b ,pro_roll_details c where a.company_id='".$dataArray[0][csf('party_id')]."' and a.knitting_company='$data[0]' and a.location_id='".$dataArray[0][csf('party_location_id')]."' and a.id=b.mst_id and b.id=c.DTLS_ID and a.id=c.mst_id and b.trans_id > 0 and a.entry_form in (2,22,58)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in(".implode(',',$receive_id_arr).")";	
							
				$res_sql_res=sql_select($res_sql);
				foreach($res_sql_res as $row)
				{
					$barCodeArr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
				}
					
				$barCodeWiseProductionSql="select a.recv_number,c.barcode_no, c.po_breakdown_id as po_id,c.booking_no, b.id, b.body_part_id,b.prod_id,b.febric_description_id,b.yarn_count, b.color_id, b.machine_dia, b.machine_gg from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.company_id='".$dataArray[0][csf('party_id')]."' and a.knitting_company='".$data[0]."' and a.location_id='".$dataArray[0][csf('location_id')]."' and a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.entry_form in (2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.barcode_no in(".implode(',',$barCodeArr).")";
				
				$barCodeWiseProductionResult=sql_select($barCodeWiseProductionSql);
				foreach($barCodeWiseProductionResult as $row)
				{
					$barCodeDataArr2[$row[csf('barcode_no')]][$row[csf('po_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['yarn_count']=$row[csf('yarn_count')];
					//echo $row[csf('recv_number')].'='.$row[csf('yarn_count')].'<br>';
					$barCodeDataArr[$row[csf('barcode_no')]]['color_id']=$row[csf('color_id')];
					$barCodeDataArr[$row[csf('barcode_no')]]['machine_dia']=$row[csf('machine_dia')];
					$barCodeDataArr[$row[csf('barcode_no')]]['machine_gg']=$row[csf('machine_gg')];
					$barCodeDataArr[$row[csf('barcode_no')]]['plan_id']=$row[csf('booking_no')];
				}
				//var_dump($barCodeDataArr);
				foreach($res_sql_res as $row)
				{
					$sys_challan=$row[csf('id')];
					$recChallan_arr[$sys_challan][change_date_format($row[csf('receive_date')])]=$row[csf('challan_no')];
					
					//$rec_data_arr[$sys_challan][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['yarn_count'].=$barCodeDataArr[$row[csf('barcode_no')]]['yarn_count'].',';
					$yarn_count="";
					$yarn_count=$barCodeDataArr2[$row[csf('barcode_no')]][$row[csf('po_id')]][$row[csf('prog_no')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['yarn_count'];
					$rec_data_arr[$sys_challan][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['yarn_count'].=$yarn_count.',';
					
					$rec_data_arr[$sys_challan][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['color_id'].=$barCodeDataArr[$row[csf('barcode_no')]]['color_id'].',';
					$rec_data_arr[$sys_challan][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['machine_dia'].=$barCodeDataArr[$row[csf('barcode_no')]]['machine_dia'].',';
					
					$rec_data_arr[$sys_challan][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['machine_gg'].=$barCodeDataArr[$row[csf('barcode_no')]]['machine_gg'].',';
					
					
					if(($row[csf('receive_basis')]==9 || $row[csf('receive_basis')]==10) && ($row[csf('entry_form')]==22 || $row[csf('entry_form')]==58))
					{
						$rec_data_arr[$sys_challan][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['feeder'].=$feeder[$knit_plan_arr[$barCodeDataArr[$row[csf('barcode_no')]]['plan_id']]].',';
					}
					else if($row[csf('receive_basis')]==2 && $row[csf('entry_form')]==2)
					{
						$rec_data_arr[$sys_challan][$row[csf('po_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['feeder'].=$production_arr[$row[csf('booking_id')]].',';
					}
				}
				unset($res_sql_res);
				$internal_ref_sql="select job_no,internal_ref from wo_order_entry_internal_ref where job_no in('".implode("','",$job_array)."')";
				$internal_ref_sql_result=sql_select($internal_ref_sql);
				foreach($internal_ref_sql_result as $row)
				{
					$internal_ref_arr[$row[csf('job_no')]][$row[csf('internal_ref')]]=$row[csf('internal_ref')];
				}
			}
			if($dataArray[0][csf('bill_for')]==3)
			{
				$buyer_id_arr=array();
				$sql_non_booking=sql_select("select recv_number_prefix_num, receive_date, buyer_id from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]' and location_id='".$dataArray[0][csf('location_id')]."' and entry_form=22  and status_active=1 and is_deleted=0 group by recv_number_prefix_num, receive_date, buyer_id");
				foreach($sql_non_booking as $row)
				{
					$buyer_id_arr[$row[csf('recv_number_prefix_num')]][change_date_format($row[csf('receive_date')])]=$party_library[$row[csf('buyer_id')]];
				}
			}
			
			//$order_array[$row[csf('id')]]['job']=$row[csf('job_no')];
			
				
			$po_id="";$i=1;
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($po_id=="") $po_id=$row[csf('order_id')]; else $po_id.=','.$row[csf('order_id')];
				$buyer_id_name="";
				if($dataArray[0][csf('bill_for')]==3)
				{
					$buyer_id_name=$buyer_id_arr[$row[csf('challan_no')]][change_date_format($row[csf('delivery_date')])];
				}
				else
				{
					$buyer_id_name=$order_array[$row[csf('order_id')]]['buyer_name'];
				}
				
				$fab_color=""; $feeder_str=""; $yarn_count=""; $mc_dia=''; $mc_gg="";
				if($dataArray[0][csf('party_source')]==1)
				{
					$fab_color=array_filter(array_unique(explode(",",$rec_data_arr[$row[csf('delivery_id')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['color_id'])));
					
					$color_srt_arr=array();
					foreach ($fab_color as $color_id){
						$color_srt_arr[$color_id]=$color_arr[$color_id];
					}
					$fab_color=implode(",",$color_srt_arr);
					
					$feeder_str=implode(",",array_filter(array_unique(explode(",",$rec_data_arr[$row[csf('delivery_id')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['feeder']))));
					$yarn_count_id=array_filter(array_unique(explode(",",$rec_data_arr[$row[csf('delivery_id')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['yarn_count'])));
					foreach($yarn_count_id as $count_id)
					{
						if($yarn_count=="") $yarn_count=$yearn_count_arr[$count_id]; else $yarn_count.=', '.$yearn_count_arr[$count_id];
					}
					$yarn_count=implode(",",explode(',',$yarn_count));
					
					$mc_dia=implode(",",array_filter(array_unique(explode(",",$rec_data_arr[$row[csf('delivery_id')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['machine_dia']))));
					//echo $row[csf('challan_no')].'='.$row[csf('item_id')].'='.$row[csf('body_part_id')].'='.$row[csf('febric_description_id')].'<br>';
					$mc_gg=implode(",",array_filter(array_unique(explode(",",$rec_data_arr[$row[csf('delivery_id')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['machine_gg']))));
				}
				else if($dataArray[0][csf('party_source')]==2)
				{
					$fab_color=array_filter(array_unique(explode(",",$subcon_exData_arr[$row[csf('order_id')]][$row[csf('item_id')]]['fabcolor'])));
					
					$color_srt_arr=array();
					foreach ($fab_color as $color_id){
						$color_srt_arr[$color_id]=$color_arr[$color_id];
					}
					$fab_color=implode(",",$color_srt_arr);
					
					
					$yarn_count_id=array_filter(array_unique(explode(",",$subcon_exData_arr[$row[csf('order_id')]][$row[csf('item_id')]]['yrnCount'])));
					foreach($yarn_count_id as $count_id)
					{
						if($yarn_count=="") $yarn_count=$yearn_count_arr[$count_id]; else $yarn_count.=', '.$yearn_count_arr[$count_id];
					}
					$yarn_count=implode(",",explode(',',$yarn_count));
					
					$mc_dia=implode(",",array_filter(array_unique(explode(",",$subcon_exData_arr[$row[csf('order_id')]][$row[csf('item_id')]]['mcdia']))));
					$mc_gg=implode(",",array_filter(array_unique(explode(",",$subcon_exData_arr[$row[csf('order_id')]][$row[csf('item_id')]]['mcgg']))));
				}
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:16px"> 
                    <td><? echo $i; ?></td>
                    <td><p><? echo $row[csf('challan_no')]; ?></p></td>
                    <td><div style="word-wrap:break-word; width:40px"><? echo $recChallan_arr[$row[csf('delivery_id')]][change_date_format($row[csf('delivery_date')])]; ?></div></td>
                    <td><p><? echo change_date_format($row[csf('delivery_date')]); ?></p></td>
                    <td><div style="word-wrap:break-word; width:60px"><? echo $order_array[$row[csf('order_id')]]['po_number']; ?></div></td>
                    <td><div style="word-wrap:break-word; width:60px"><? echo $buyer_id_name; ?></div></td>
                    <td><div style="word-wrap:break-word; width:60px"><? echo $order_array[$row[csf('order_id')]]['style_ref_no']; ?></div></td>
                    <td align="center"><div style="word-wrap:break-word; width:40px"><? echo $order_array[$row[csf('order_id')]]['job_no_prefix_num']; ?></div></td>
                   
                    <td align="center"><p><? echo implode(',',$internal_ref_arr[$order_array[$row[csf('order_id')]]['job']]); ?></p></td>
                    
                    <td align="center"><div style="word-wrap:break-word; width:35px"><? echo $order_array[$row[csf('order_id')]]['year']; ?></div></td>
                    <td><div style="word-wrap:break-word; width:150px"><? echo $const_comp_arr[$row[csf('item_id')]]; ?></div></td>
                    
                    <td align="center"><div style="word-wrap:break-word; width:100px"><? echo $fab_color; ?></div></td>
                    <td><div style="word-wrap:break-word; width:60px"><? echo $feeder_str; ?></div></td>
                    <td align="center"><div style="word-wrap:break-word; width:40px"><? echo $yarn_count; ?></div></td>
                    <td align="center"><div style="word-wrap:break-word; width:40px"><? echo $mc_dia; ?></div></td>
                    <td align="center"><div style="word-wrap:break-word; width:40px"><? echo $mc_gg; ?></div></td>
                  
                    <td align="right"><p><? echo $row[csf('packing_qnty')]; $tot_packing_qty+=$row[csf('packing_qnty')]; ?>&nbsp;</p></td>
                    <td align="right"><p><b><? echo number_format($row[csf('delivery_qty')],2,'.',''); $tot_delivery_qty+=$row[csf('delivery_qty')]; ?>&nbsp;</b></p></td>
                    <td align="right"><p><b><? echo number_format($row[csf('delivery_qtypcs')],2,'.',''); $tot_delivery_qtypcs+=$row[csf('delivery_qtypcs')]; ?>&nbsp;</b></p></td>
                    <td><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf('rate')],4,'.',''); ?>&nbsp;</p></td>
                    <td align="right"><p><b><? echo number_format($row[csf('amount')],2,'.','');  $total_amount += $row[csf('amount')]; ?>&nbsp;</b></p></td>

                    <td align="center"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
                  
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
                <td align="right" colspan="16"><strong>Total</strong></td>
                <td align="right"><? echo $tot_packing_qty; ?>&nbsp;</td>
                <td align="right"><b><? echo number_format($tot_delivery_qty,2,'.',''); ?></b></td>
                <td align="right"><b><? echo number_format($tot_delivery_qtypcs,2,'.',''); ?></b></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><b><? echo $format_total_amount=number_format($total_amount,0,'.',''); ?></b></td>
                <td>&nbsp;</td>
			</tr>
           <tr>
               <td colspan="23" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
           </tr>
        </table>
        <?
			$bill_no=$dataArray[0][csf('bill_no')];
			$sql_terms="Select id,terms from subcon_terms_condition where entry_form=1 and bill_no='$bill_no' ";
			$result_sql_terms =sql_select($sql_terms);

			$i=1;
			if(count($result_sql_terms)>0)
			{
				?>
                <table width="930" align="left" > 
                    <tr><td colspan="2">&nbsp;</td> </tr>
                    <tr><td colspan="2" align="center"><b>TERMS & CONDITION</b></td></tr>
					<?
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
					?>
        		</table><?
			}
			?>
        <br>
        <? if($data[4]==1) 
		{ 
			if($dataArray[0][csf('bill_for')]!=3)
			{
			?>
			<table align="left"  cellspacing="0" width="980"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="120">Order No</th>
					<th width="110">Buyer Name</th>
					<th width="100">Grey Required (KG)</th>
					<th width="100">Charge Required (USD)</th>
					<th width="100">Bill Qty (KG)</th> 
					<th width="100">Bill Amount (USD)</th>
					<th width="100">Balance Qty (KG)</th>
					<th width="">Balance Amount (USD)</th>
				</thead>
				<tbody>
				<?
				$grey_req_arr=array();
				$grey_req_sql="select po_break_down_id, sum(requirment) as grey_req from wo_pre_cos_fab_co_avg_con_dtls group by po_break_down_id";
				$grey_req_sql_result =sql_select($grey_req_sql);
				foreach($grey_req_sql_result as $row)
				{
					$grey_req_arr[$row[csf('po_break_down_id')]]=$row[csf('grey_req')];
				}
				
				$charge_req_arr=array(); 
				$charge_req_sql="select job_no, sum(amount) as charge_req from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0 group by job_no";
				$charge_req_sql_result =sql_select($charge_req_sql);
				foreach($charge_req_sql_result as $row)
				{
					$charge_req_arr[$row[csf('job_no')]]=$row[csf('charge_req')];
				}
				
				$bill_arr=array(); 
				$bill_sql="select b.order_id, sum(b.delivery_qty) as bill_qty, sum(b.amount) as bill_amount from  subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.party_source=1 and b.process_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id";
				$bill_sql_result =sql_select($bill_sql);
				foreach($bill_sql_result as $row)
				{
					$bill_arr[$row[csf('order_id')]]['bill_qty']=$row[csf('bill_qty')];
					$bill_arr[$row[csf('order_id')]]['bill_amount']=$row[csf('bill_amount')];
				}
				
				$currency_rate=set_conversion_rate( 2, $dataArray[0][csf('bill_date')] );
				$costingper_id_arr=return_library_array( "select job_no,costing_per_id from wo_pre_cost_dtls",'job_no','costing_per_id');
				
				$ex_po=array_unique(explode(",",$po_id)); $k=1;
				foreach($ex_po as $po_id)
				{
					if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$po_quantity=$order_array[$po_id]['po_quantity'];
					
					$costing_per_id=$costingper_id_arr[$order_array[$po_id]['job']];
					$dzn_qnty=0;
					if($costing_per_id==1) $dzn_qnty=12;
					else if($costing_per_id==3) $dzn_qnty=12*2;
					else if($costing_per_id==4) $dzn_qnty=12*3;
					else if($costing_per_id==5) $dzn_qnty=12*4;
					else $dzn_qnty=1;
					$dzn_qnty_req=$dzn_qnty*$order_array[$po_id]['ratio'];
					$grey_req=($po_quantity/$dzn_qnty_req)*$grey_req_arr[$po_id];
					$charge_req=($po_quantity/$dzn_qnty_req)*$charge_req_arr[$order_array[$po_id]['job']]*$currency_rate;
					
					$bill_qty=$bill_arr[$po_id]['bill_qty'];
					$bill_amount=$bill_arr[$po_id]['bill_amount']*$currency_rate;
					$balance_qty=$grey_req-$bill_qty;
					$balance_amount=$charge_req-$bill_amount;
				?>
					<tr bgcolor="<? echo $bgcolor; ?>"> 
						<td><div style="word-wrap:break-word; width:120px"><? echo $order_array[$po_id]['po_number']; ?></div></td>
						<td><p><? echo $order_array[$po_id]['buyer_name']; ?></p></td>
						
						<td align="right"><p><? echo number_format($grey_req,2,'.',''); ?></p></td>
						<td align="right"><p><? echo number_format($charge_req,2,'.',''); ?></p></td>
						
						<td align="right"><p><? echo number_format($bill_qty,2,'.',''); ?></p></td>
						<td align="right"><p><? echo number_format($bill_amount,2,'.',''); ?></p></td>
						
						<td align="right"><p><? echo number_format($balance_qty,2,'.',''); ?></p></td>
						<td align="right"><p><? echo number_format($balance_amount,2,'.',''); ?></p></td>
					</tr>
				<?
				}
				?>
				</tbody>
			</table>
        <? } 
		} ?>
        <br>
		 <? echo signature_table(220, $data[0], "980px"); ?>
   </div>
   </div>
	<?
    exit();
}

//======================================================================This button simillar to print button 1 and it changed just currency USD / Bill Print============================================================================================
if($action=="knitting_bill_print_3") 
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$party_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name");
	$yarn_desc_arr=return_library_array( "select id,yarn_description from lib_subcon_charge",'id','yarn_description');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	
	$sql_mst="Select id, bill_no, bill_date, party_id, party_source, location_id, attention, bill_for from subcon_inbound_bill_mst where company_id=$data[0] and id='$data[1]' and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql_mst);
	?>
    <div style="width:930px;margin:0px auto;">
    <table width="100%" cellpadding="0" cellspacing="0" >
        <tr>
            <td width="70" align="right"> 
            	<img  src='../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='70' width='200' />
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
                        <td  align="center" style="font-size:14px"><? echo show_company($data[0],'',''); ?></td>  
                    </tr>
                    <tr>
                    	<td align="center" style="font-size:18px"><strong><? echo $data[3]; ?></strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
     <table width="915" cellspacing="0" align="" border="0">
            <tr>
                <td width="130"><strong>Bill No :</strong></td> <td width="175"><? echo $dataArray[0][csf('bill_no')]; ?></td>
                <td width="130"><strong>Bill Date: </strong></td><td width="175px"> <? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
                <td width="130"><strong>Source :</strong></td> <td width="175"><? echo $knitting_source[$dataArray[0][csf('party_source')]]; ?></td>
            </tr>
            <tr>
				<?
                    if($dataArray[0][csf('attention')]!='')  $attention='('.$dataArray[0][csf('attention')].')'; else $attention='';
                        
                    if($dataArray[0][csf('party_source')]==2)
                    {
                        $party_add=$dataArray[0][csf('party_id')];
                        $nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add"); 
                        foreach ($nameArray as $result)
                        { 
                            $address="";
                            if($result!="") $address=$result['address_1'];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                        }
                        $party_name=$party_library[$dataArray[0][csf('party_id')]].' : Address :- '.$address.' '.$attention;
                    }
                    else
                    {
                        $party_name=$company_library[$dataArray[0][csf('party_id')]].' '.$attention;
                    }
                ?>
                <td><strong>Party Name : </strong></td><td colspan="3"> <? echo $party_name; ?></td>
                <td><strong>Bill For : </strong></td><td> <? echo $bill_for[$dataArray[0][csf('bill_for')]]; ?></td>
            </tr>
        </table>
         <br>
	<div style="width:100%;">
		<table align="left" cellspacing="0" width="915"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center" style="font-size:10px">
                <th width="30" style="word-wrap: break-word;word-break: break-all;">SL</th>
                <th width="50" style="word-wrap: break-word;word-break: break-all;">Sys. Challan</th>
                <th width="50" style="word-wrap: break-word;word-break: break-all;">Rec. Challan</th>
                <th width="55" style="word-wrap: break-word;word-break: break-all;">D. Date</th>
                <th width="60" style="word-wrap: break-word;word-break: break-all;">Order</th> 
                <th width="60" style="word-wrap: break-word;word-break: break-all;">Buyer</th>
                <th width="60" style="word-wrap: break-word;word-break: break-all;">Style</th>
                <th width="25" style="word-wrap: break-word;word-break: break-all;">Job</th>
                <th width="25" style="word-wrap: break-word;word-break: break-all;">Year</th>
                <th width="100" style="word-wrap: break-word;word-break: break-all;">Fabric Description</th>
                <th width="50" style="word-wrap: break-word;word-break: break-all;">Collar Cuff Measurement</th>
                <th width="25" style="word-wrap: break-word;word-break: break-all;">Roll</th>
                <th width="55" style="word-wrap: break-word;word-break: break-all;">D. Qty (W)</th>
                <th width="55" style="word-wrap: break-word;word-break: break-all;">D. Qty (P)</th>
                <th width="25" style="word-wrap: break-word;word-break: break-all;">UOM</th>
                <th width="30" style="word-wrap: break-word;word-break: break-all;">Rate</th>
                <th width="30" style="word-wrap: break-word;word-break: break-all;">Amount</th>
                <th width="45" style="word-wrap: break-word;word-break: break-all;">Currency</th>
                <th> Remarks</th>
            </thead>
		 <?
		 if($db_type==0) $job_year="YEAR(a.insert_date) as year"; else $job_year="to_char(a.insert_date,'YYYY') as year";
		 	
			if($dataArray[0][csf('party_source')]==2)
			{
				$order_array=array();
				 $order_sql="select a.job_no_prefix_num,b.id,$job_year, b.order_no, b.order_uom, b.cust_buyer, b.cust_style_ref from subcon_ord_dtls b,subcon_ord_mst a where b.job_no_mst=a.subcon_job and b.status_active=1 and b.is_deleted=0";
				$order_sql_result =sql_select($order_sql);
				foreach($order_sql_result as $row)
				{
					$order_array[$row[csf('id')]]['po_number']=$row[csf('order_no')];
					$order_array[$row[csf('id')]]['buyer_name']=$row[csf('cust_buyer')];
					$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('cust_style_ref')];
					$order_array[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
					$order_array[$row[csf('id')]]['year']=$row[csf('year')];
				}
				unset($order_sql_result);
				$const_comp_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
				$collar_cuff_arr=return_library_array( "select id,collar_cuff from subcon_delivery_dtls",'id','collar_cuff');
			}
			else if($dataArray[0][csf('party_source')]==1)
			{
				$order_array=array();
				$job_sql="select a.job_no, $job_year, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.total_set_qnty as ratio, b.id, b.po_number, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active!=0 and a.is_deleted=0 and  b.status_active!=0 and b.is_deleted=0";// and a.company_name=$data[0]
				$job_sql_result =sql_select($job_sql);
				foreach($job_sql_result as $row)
				{
					$order_array[$row[csf('id')]]['buyer_name']=$party_library[$row[csf('buyer_name')]];
					$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
					$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
					$order_array[$row[csf('id')]]['job']=$row[csf('job_no')];
					$order_array[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
					$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
					$order_array[$row[csf('id')]]['ratio']=$row[csf('ratio')];
					$order_array[$row[csf('id')]]['year']=$row[csf('year')];
				}
				unset($job_sql_result);
				$const_comp_arr=return_library_array( "select id,product_name_details from product_details_master",'id','product_name_details');
				$recChallan_arr=array();
				$rec_challan_arr=return_library_array( "select id,challan_no from inv_receive_master",'id','challan_no');
				//echo "select recv_number_prefix_num, receive_date, challan_no from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]' and location_id='".$dataArray[0][csf('location_id')]."' and entry_form=22 and status_active=1 and is_deleted=0 and recv_number_prefix_num=30";
				$rec_challa_sql=sql_select("select recv_number_prefix_num, receive_date, challan_no from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]' and location_id='".$dataArray[0][csf('location_id')]."' and entry_form in (2,22)  and status_active=1 and is_deleted=0"); //and a.company_id='$data[3]' and a.knitting_company='$data[0]' and a.location_id='$data[1]'
				foreach($rec_challa_sql as $row)
				{
					$recChallan_arr[$row[csf('recv_number_prefix_num')]][change_date_format($row[csf('receive_date')])]=$row[csf('challan_no')];
				}
			}
			
			if($dataArray[0][csf('bill_for')]==3)
			{
				$buyer_id_arr=array();
				$sql_non_booking=sql_select("select recv_number_prefix_num, receive_date, buyer_id from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]' and location_id='".$dataArray[0][csf('location_id')]."' and entry_form=22  and status_active=1 and is_deleted=0 group by recv_number_prefix_num, receive_date, buyer_id");
				foreach($sql_non_booking as $row)
				{
					$buyer_id_arr[$row[csf('recv_number_prefix_num')]][change_date_format($row[csf('receive_date')])]=$party_library[$row[csf('buyer_id')]];
				}
			}
			//var_dump($recChallan_arr);
     		$i=1;
			$mst_id=$dataArray[0][csf('id')];
			$po_id="";
			$sql_result =sql_select("select id, delivery_id, delivery_date, challan_no, order_id, item_id, uom, packing_qnty, delivery_qty, delivery_qtypcs, rate, amount, remarks, currency_id, process_id from subcon_inbound_bill_dtls  where mst_id='$mst_id' and process_id='2' and status_active=1 and is_deleted=0 order by challan_no ASC"); 
			
			$sql_currency_result_usd=sql_select("SELECT conversion_rate from currency_conversion_rate WHERE con_date = (SELECT MAX(con_date) from currency_conversion_rate WHERE is_deleted=0 and status_active=1 and currency=2)");
						
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($po_id=="") $po_id=$row[csf('order_id')]; else $po_id.=','.$row[csf('order_id')];
				$buyer_id_name="";
				if($dataArray[0][csf('bill_for')]==3)
				{
					$buyer_id_name=$buyer_id_arr[$row[csf('challan_no')]][change_date_format($row[csf('delivery_date')])];
				}
				else
				{
					$buyer_id_name=$order_array[$row[csf('order_id')]]['buyer_name'];
				}
				//echo $row[csf('order_id')];
				$rec_challan="";
				if($row[csf('delivery_id')]!='' || $row[csf('delivery_id')]!=0) $rec_challan=$rec_challan_arr[$row[csf('delivery_id')]];
				else $rec_challan=$recChallan_arr[$row[csf('challan_no')]][change_date_format($row[csf('delivery_date')])];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:10px"> 
                    <td width="30" style="word-wrap: break-word;word-break: break-all;"><? echo $i; ?></td>
                    <td width="50" style="word-wrap: break-word;word-break: break-all;"><p><? echo $row[csf('challan_no')]; ?></p></td>
                    <td width="50" style="word-wrap: break-word;word-break: break-all;"><? echo $rec_challan; ?></td>
                    <td width="55" style="word-wrap: break-word;word-break: break-all;"><p><? echo change_date_format($row[csf('delivery_date')]); ?></p></td>
                    <td width="60" style="word-wrap: break-word;word-break: break-all;"><? echo $order_array[$row[csf('order_id')]]['po_number']; ?></td>
                    <td width="60" style="word-wrap: break-word;word-break: break-all;"><? echo $buyer_id_name; ?></td>
                    <td width="60" style="word-wrap: break-word;word-break: break-all;"><? echo $order_array[$row[csf('order_id')]]['style_ref_no']; ?></td>
                    <td width="25" style="word-wrap: break-word;word-break: break-all;" align="center"><? echo $order_array[$row[csf('order_id')]]['job_no_prefix_num']; ?></td>
                     <td width="25" style="word-wrap: break-word;word-break: break-all;" align="center"><? echo $order_array[$row[csf('order_id')]]['year']; ?></td>
                    <td width="100" style="word-wrap: break-word;word-break: break-all;"> <? echo $const_comp_arr[$row[csf('item_id')]]; ?></td>
                    <td width="50" style="word-wrap: break-word;word-break: break-all;"> <? echo $collar_cuff_arr[$row[csf('delivery_id')]]; ?></td>
                    <td width="25" style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo $row[csf('packing_qnty')]; $tot_packing_qty+=$row[csf('packing_qnty')]; ?>&nbsp;</p></td>
                    <td width="55" style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo number_format($row[csf('delivery_qty')],2,'.',''); $tot_delivery_qty+=$row[csf('delivery_qty')]; ?>&nbsp;</p></td>
                    <td width="55" style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo number_format($row[csf('delivery_qtypcs')],2,'.',''); $tot_delivery_qtypcs+=$row[csf('delivery_qtypcs')]; ?>&nbsp;</p></td>
                    <td width="25" style="word-wrap: break-word;word-break: break-all;"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
                    <td width="30" style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo number_format($row[csf('rate')],2,'.',''); ?>&nbsp;</p></td>
                    <td width="30" style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo number_format($row[csf('amount')]/$sql_currency_result_usd[0][csf('conversion_rate')],2,'.','');  $total_amount += $row[csf('amount')]/$sql_currency_result_usd[0][csf('conversion_rate')]; ?>&nbsp;</p></td>

                    <td width="45" style="word-wrap: break-word;word-break: break-all;" align="center"><p><? echo $currency[2]; ?></p></td>
                    <td ><? echo $row[csf('remarks')]; ?> </td>
                    <? 
					$carrency_id=$row['currency_id'];
					if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}
				   ?>
                </tr>
                <?
                $i++;
			}
			?>
        	<tr style="font-size:12px"> 
                <td align="right" colspan="11"><strong>Total</strong></td>
                <td align="right"><? echo $tot_packing_qty; ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_delivery_qty,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_delivery_qtypcs,2,'.',''); ?>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><? echo $format_total_amount=number_format($total_amount,2,'.',''); ?>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
           <tr>
               <td colspan="18" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[2],$paysa_sent); ?></b></td>
           </tr>
        </table>
        <?
			$bill_no=$dataArray[0][csf('bill_no')];
			$sql_terms="Select id,terms from subcon_terms_condition where entry_form=1 and bill_no='$bill_no' ";
			$result_sql_terms =sql_select($sql_terms);

			$i=1;
			if(count($result_sql_terms)>0)
			{
				?>
                <table width="915" align="left" > 
                    <tr><td colspan="2">&nbsp;</td> </tr>
                    <tr><td colspan="2" align="center"><b>TERMS & CONDITION</b></td></tr>
					<?
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
        <? if($data[4]==1) 
		{ 
			if($dataArray[0][csf('bill_for')]!=3)
			{
			?>
			<table align="left" cellspacing="0" width="915"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="120" style="word-wrap: break-word;word-break: break-all;">Order No</th>
					<th width="110" style="word-wrap: break-word;word-break: break-all;">Buyer Name</th>
					<th width="100" style="word-wrap: break-word;word-break: break-all;">Grey Required (KG)</th>
					<th width="100" style="word-wrap: break-word;word-break: break-all;">Charge Required (USD)</th>
					<th width="100" style="word-wrap: break-word;word-break: break-all;">Bill Qty (KG)</th> 
					<th width="100" style="word-wrap: break-word;word-break: break-all;">Bill Amount (USD)</th>
					<th width="100" style="word-wrap: break-word;word-break: break-all;">Balance Qty (KG)</th>
					<th width="">Balance Amount (USD)</th>
				</thead>
				<tbody>
				<?
				$grey_req_arr=array();
				$grey_req_sql="select po_break_down_id, sum(requirment) as grey_req from wo_pre_cos_fab_co_avg_con_dtls group by po_break_down_id";
				$grey_req_sql_result =sql_select($grey_req_sql);
				foreach($grey_req_sql_result as $row)
				{
					$grey_req_arr[$row[csf('po_break_down_id')]]=$row[csf('grey_req')];
				}
				
				$charge_req_arr=array(); 
				$charge_req_sql="select job_no, sum(amount) as charge_req from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0 group by job_no";
				$charge_req_sql_result =sql_select($charge_req_sql);
				foreach($charge_req_sql_result as $row)
				{
					$charge_req_arr[$row[csf('job_no')]]=$row[csf('charge_req')];
				}
				
				$bill_arr=array(); 
				$bill_sql="select b.order_id, sum(b.delivery_qty) as bill_qty, sum(b.amount) as bill_amount from  subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.party_source=1 and b.process_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id";
				$bill_sql_result =sql_select($bill_sql);
				foreach($bill_sql_result as $row)
				{
					$bill_arr[$row[csf('order_id')]]['bill_qty']=$row[csf('bill_qty')];
					$bill_arr[$row[csf('order_id')]]['bill_amount']=$row[csf('bill_amount')];
				}
				
				$currency_rate=set_conversion_rate( 2, $dataArray[0][csf('bill_date')] );
				$costingper_id_arr=return_library_array( "select job_no,costing_per_id from wo_pre_cost_dtls",'job_no','costing_per_id');
				
				$ex_po=array_unique(explode(",",$po_id)); $k=1;
				foreach($ex_po as $po_id)
				{
					if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$po_quantity=$order_array[$po_id]['po_quantity'];
					
					$costing_per_id=$costingper_id_arr[$order_array[$po_id]['job']];
					$dzn_qnty=0;
					if($costing_per_id==1) $dzn_qnty=12;
					else if($costing_per_id==3) $dzn_qnty=12*2;
					else if($costing_per_id==4) $dzn_qnty=12*3;
					else if($costing_per_id==5) $dzn_qnty=12*4;
					else $dzn_qnty=1;
					$dzn_qnty_req=$dzn_qnty*$order_array[$po_id]['ratio'];
					$grey_req=($po_quantity/$dzn_qnty_req)*$grey_req_arr[$po_id];
					$charge_req=($po_quantity/$dzn_qnty_req)*$charge_req_arr[$order_array[$po_id]['job']]*$currency_rate;
					
					$bill_qty=$bill_arr[$po_id]['bill_qty'];
					$bill_amount=$bill_arr[$po_id]['bill_amount']*$currency_rate;
					$balance_qty=$grey_req-$bill_qty;
					$balance_amount=$charge_req-$bill_amount;
				?>
					<tr bgcolor="<? echo $bgcolor; ?>"> 
						<td width="120" style="word-wrap: break-word;word-break: break-all;" > <? echo $order_array[$po_id]['po_number']; ?> </td>
						<td width="110" style="word-wrap: break-word;word-break: break-all;" ><p><? echo $order_array[$po_id]['buyer_name']; ?></p></td>
						
						<td width="100" style="word-wrap: break-word;word-break: break-all;"  align="right"><p><? echo number_format($grey_req,2,'.',''); ?></p></td>
						<td width="100" style="word-wrap: break-word;word-break: break-all;"  align="right"><p><? echo number_format($charge_req,2,'.',''); ?></p></td>
						
						<td width="100"  style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo number_format($bill_qty,2,'.',''); ?></p></td>
						<td width="100" style="word-wrap: break-word;word-break: break-all;"  align="right"><p><? echo number_format($bill_amount,2,'.',''); ?></p></td>
						
						<td width="100" style="word-wrap: break-word;word-break: break-all;"  align="right"><p><? echo number_format($balance_qty,2,'.',''); ?></p></td>
						<td  align="right"><p><? echo number_format($balance_amount,2,'.',''); ?></p></td>
					</tr>
				<?
				}
				?>
				</tbody>
			</table>
        <? } 
		} ?>
        
        <br>
		 <? echo signature_table(220, $data[0], "930px"); ?>
   </div>
   </div>
	<?
	exit();
}

//======================================================================Bill Print 3 ============================================================================================
if($action=="knitting_bill_print_4") 
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$party_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name");
	$yarn_desc_arr=return_library_array( "select id,yarn_description from lib_subcon_charge",'id','yarn_description');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	
	$sql_mst="Select id, bill_no, bill_date, party_id, party_source, location_id, attention, bill_for,party_location_id from subcon_inbound_bill_mst where company_id=$data[0] and id='$data[1]' and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql_mst);
	?>
    <div style="width:930px;margin:0px auto;">
    <table width="100%" cellpadding="0" cellspacing="0" >
        <tr>
            <!-- <td width="70" align="right"> 
            	<img  src='../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='70' width='200' />
            </td> -->
            <td>
                <table width="800" cellspacing="0" align="center">
                    <tr>
                    	<td align="center" style="font-size:20px"><strong ><? echo $company_library[$data[0]]; ?></strong></td>
                    </tr>
                    <!-- <tr>
                        <td align="center"  style="font-size:16px"><strong>Unit : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                    </tr> -->
                    <tr class="form_caption">
                        <td align="center" style="font-size:14px"><? echo show_company($data[0],'',''); ?></td>  
                    </tr>
					<tr>  
					<td>&nbsp;</td>
					</tr>
                    <tr>
                    	<td align="center" style="font-size:18px"><strong><? echo $data[3]; ?></strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
     <table width="915" cellspacing="0" align="" border="0">
            <tr>
                <td width="130"><strong>Bill No :</strong></td> <td width="175"><? echo $dataArray[0][csf('bill_no')]; ?></td>
                <td width="130"><strong>Bill Date: </strong></td><td width="175px"> <? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
                <td width="130"><strong>Source :</strong></td> <td width="175"><? echo $knitting_source[$dataArray[0][csf('party_source')]]; ?></td>
            </tr>
            <tr>
			
				<?
                    if($dataArray[0][csf('attention')]!='')  $attention='('.$dataArray[0][csf('attention')].')'; else $attention='';
                        
                    if($dataArray[0][csf('party_source')]==2)
                    {
                        $party_add=$dataArray[0][csf('party_id')];
                        $nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add"); 
                        foreach ($nameArray as $result)
                        { 
                            $address="";
							if($result!="") $address=$result[csf('address_1')];;
                        }
						$party_name=$party_library[$dataArray[0][csf('party_id')]].'<br/>Address:'.$address.' '.$attention;
						$party_location='';
                    }
                    else
                    {
                        $party_name=$company_library[$dataArray[0][csf('party_id')]].' '.$attention;
						$party_location=$location_arr[$dataArray[0][csf('party_location_id')]];
                    }
                ?>
				
				<td  width="150"  valign="top"><strong>Party Name: </strong></td><td width="200"  valign="top"><p> <? echo $party_name; ?></p></td>
                <td width="180"  valign="top"><strong>Party Location: </strong></td><td  width="200"  valign="top"> <? echo $party_location; ?></td>
                <td width="150"  valign="top"><strong>Bill For : </strong></td><td width="200"  valign="top"> <? echo $bill_for[$dataArray[0][csf('bill_for')]]; ?></td>
            </tr>
        </table>
         <br>
	<div style="width:100%;">
		<table align="left" cellspacing="0" width="915"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center" style="font-size:10px">
                <th width="30" style="word-wrap: break-word;word-break: break-all;">SL</th>
                <th width="100" style="word-wrap: break-word;word-break: break-all;">Sys. Challan <br> D. Date</th>
                <th width="100" style="word-wrap: break-word;word-break: break-all;">Order <br>Buyer <br>Style </th> 
                <th width="25" style="word-wrap: break-word;word-break: break-all;">Job</th>
                <th width="210" style="word-wrap: break-word;word-break: break-all;">Fabric Description <br> Collar Cuff Measurement</th>
                <th width="25" style="word-wrap: break-word;word-break: break-all;">Roll</th>
                <th width="100" style="word-wrap: break-word;word-break: break-all;">D. Qty (W)</th>
                <th width="30" style="word-wrap: break-word;word-break: break-all;">Rate</th>
                <th width="100" style="word-wrap: break-word;word-break: break-all;">Amount</th>
                <th> Remarks</th>
            </thead>
		 <?
		 if($db_type==0) $job_year="YEAR(a.insert_date) as year";
		 else  $job_year="to_char(a.insert_date,'YYYY') as year";
		 	
			if($dataArray[0][csf('party_source')]==2)
			{
				$order_array=array();
				 $order_sql="select a.job_no_prefix_num,b.id,$job_year, b.order_no, b.order_uom, b.cust_buyer, b.cust_style_ref from subcon_ord_dtls b,subcon_ord_mst a where b.job_no_mst=a.subcon_job and b.status_active=1 and b.is_deleted=0";
				$order_sql_result =sql_select($order_sql);
				foreach($order_sql_result as $row)
				{
					$order_array[$row[csf('id')]]['po_number']=$row[csf('order_no')];
					$order_array[$row[csf('id')]]['buyer_name']=$row[csf('cust_buyer')];
					$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('cust_style_ref')];
					$order_array[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
					$order_array[$row[csf('id')]]['year']=$row[csf('year')];
				}
				unset($order_sql_result);
				$const_comp_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
				$collar_cuff_arr=return_library_array( "select id,collar_cuff from subcon_delivery_dtls",'id','collar_cuff');
			}
			else if($dataArray[0][csf('party_source')]==1)
			{
				$order_array=array();
				$job_sql="select a.job_no, $job_year, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.total_set_qnty as ratio, b.id, b.po_number, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active!=0 and a.is_deleted=0 and  b.status_active!=0 and b.is_deleted=0";// and a.company_name=$data[0]
				$job_sql_result =sql_select($job_sql);
				foreach($job_sql_result as $row)
				{
					$order_array[$row[csf('id')]]['buyer_name']=$party_library[$row[csf('buyer_name')]];
					$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
					$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
					$order_array[$row[csf('id')]]['job']=$row[csf('job_no')];
					$order_array[$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
					$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
					$order_array[$row[csf('id')]]['ratio']=$row[csf('ratio')];
					$order_array[$row[csf('id')]]['year']=$row[csf('year')];
				}
				unset($job_sql_result);
				$const_comp_arr=return_library_array( "select id,product_name_details from product_details_master",'id','product_name_details');
				$recChallan_arr=array();
				$rec_challan_arr=return_library_array( "select id,challan_no from inv_receive_master",'id','challan_no');
				//echo "select recv_number_prefix_num, receive_date, challan_no from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]' and location_id='".$dataArray[0][csf('location_id')]."' and entry_form=22 and status_active=1 and is_deleted=0 and recv_number_prefix_num=30";
				$rec_challa_sql=sql_select("select recv_number_prefix_num, receive_date, challan_no from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]' and location_id='".$dataArray[0][csf('location_id')]."' and entry_form in (2,22)  and status_active=1 and is_deleted=0"); //and a.company_id='$data[3]' and a.knitting_company='$data[0]' and a.location_id='$data[1]'
				foreach($rec_challa_sql as $row)
				{
					$recChallan_arr[$row[csf('recv_number_prefix_num')]][change_date_format($row[csf('receive_date')])]=$row[csf('challan_no')];
				}
			}
			
			if($dataArray[0][csf('bill_for')]==3)
			{
				$buyer_id_arr=array();
				$sql_non_booking=sql_select("select recv_number_prefix_num, receive_date, buyer_id from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]' and location_id='".$dataArray[0][csf('location_id')]."' and entry_form=22  and status_active=1 and is_deleted=0 group by recv_number_prefix_num, receive_date, buyer_id");
				foreach($sql_non_booking as $row)
				{
					$buyer_id_arr[$row[csf('recv_number_prefix_num')]][change_date_format($row[csf('receive_date')])]=$party_library[$row[csf('buyer_id')]];
				}
			}
			//var_dump($recChallan_arr);
     		$i=1;
			$mst_id=$dataArray[0][csf('id')];
			$po_id="";
			$sql_result =sql_select("select id, delivery_id, delivery_date, challan_no, order_id, item_id, uom, packing_qnty, delivery_qty, delivery_qtypcs, rate, amount, remarks, currency_id, process_id from subcon_inbound_bill_dtls  where mst_id='$mst_id' and process_id='2' and status_active=1 and is_deleted=0 order by challan_no ASC"); 
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($po_id=="") $po_id=$row[csf('order_id')]; else $po_id.=','.$row[csf('order_id')];
				$buyer_id_name="";
				if($dataArray[0][csf('bill_for')]==3)
				{
					$buyer_id_name=$buyer_id_arr[$row[csf('challan_no')]][change_date_format($row[csf('delivery_date')])];
				}
				else
				{
					$buyer_id_name=$order_array[$row[csf('order_id')]]['buyer_name'];
				}
				//echo $row[csf('order_id')];
				$rec_challan="";
				if($row[csf('delivery_id')]!='' || $row[csf('delivery_id')]!=0) $rec_challan=$rec_challan_arr[$row[csf('delivery_id')]];
				else $rec_challan=$recChallan_arr[$row[csf('challan_no')]][change_date_format($row[csf('delivery_date')])];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:10px; text-align: center;"> 
                    <td width="30" style="word-wrap: break-word;word-break: break-all; text-align: center;"><? echo $i; ?></td>
                    <td width="100" style="word-wrap: break-word;word-break: break-all;">
					<p>
							 <? $challan_no=$row[csf('challan_no')]; 
							    if ($challan_no !=""){
									echo $challan_no;
								 }else
								 echo "-----";
							 ?> 
						<br> <? 
								$delivery_date=change_date_format($row[csf('delivery_date')]); 
								if ($delivery_date !=""){
									echo $delivery_date;
								 }else
								 echo "-----";
						     ?> 
					</p>
					</td>
                    <td width="100" style="word-wrap: break-word;word-break: break-all;">
					<p>
						     <?   
							 $Order_id=$order_array[$row[csf('order_id')]]['po_number'];
							 if ($Order_id !=""){
								echo $Order_id;
							 }else
							 echo "-----"; 
					         ?> 
						<br> <? 
						      if ($buyer_id_name !=""){
								echo $buyer_id_name;
							 }else
							 echo "-----"; 
						     ?> 
						<br> <? 
							  $style_No= $order_array[$row[csf('order_id')]]['style_ref_no']; 
							  if ($style_No !=""){
								echo $style_No;
							 }else
							 echo "-----"; 
						     ?>
					</p>
					</td>
                    <td width="25" style="word-wrap: break-word;word-break: break-all;" align="center"><? echo $order_array[$row[csf('order_id')]]['job_no_prefix_num']; ?></td>
                    <td width="210" style="word-wrap: break-word;word-break: break-all;"> <? echo $const_comp_arr[$row[csf('item_id')]]; ?> <br> <? echo $collar_cuff_arr[$row[csf('delivery_id')]]; ?></td>
                    <td width="25" style="word-wrap: break-word;word-break: break-all; text-align: center;"><p><? echo $row[csf('packing_qnty')]; $tot_packing_qty+=$row[csf('packing_qnty')]; ?>&nbsp;</p></td>
                    <td width="100" style="word-wrap: break-word;word-break: break-all; text-align: center;"><p><? echo number_format($row[csf('delivery_qty')],2,'.',''); $tot_delivery_qty+=$row[csf('delivery_qty')]; ?>&nbsp;</p></td>
                    <td width="30" style="word-wrap: break-word;word-break: break-all; text-align: center;"><p><? echo number_format($row[csf('rate')],2,'.',''); ?>&nbsp;</p></td>
                    <td width="100" style="word-wrap: break-word;word-break: break-all; text-align: center;"><p><? echo number_format($row[csf('amount')],2,'.','');  $total_amount += $row[csf('amount')]; ?>&nbsp;</p></td>
                    <td ><? echo $row[csf('remarks')]; ?> </td>
                    <? 
					$carrency_id=$row['currency_id'];
					if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}
				   ?>
                </tr>
                <?
                $i++;
			}
			?>
        	<tr style="font-size:12px"> 
                <td align="right" colspan="5"><strong>Total</strong></td>
                <td align="center"><? echo $tot_packing_qty; ?>&nbsp;</td>
                <td align="center"><? echo number_format($tot_delivery_qty,2,'.',''); ?>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="center" colspan=""><? echo $format_total_amount=number_format($total_amount,2,'.',''); ?>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
           <tr>
               <td colspan="18" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
           </tr>
		
        </table>
        <?
			$bill_no=$dataArray[0][csf('bill_no')];
			$sql_terms="Select id,terms from subcon_terms_condition where entry_form=1 and bill_no='$bill_no' ";
			$result_sql_terms =sql_select($sql_terms);

			$i=1;
			if(count($result_sql_terms)>0)
			{
				?>
                <table width="915" align="left" > 
                    <tr><td colspan="2">&nbsp;</td> </tr>
                    <tr><td colspan="2" align="center"><b>TERMS & CONDITION</b></td></tr>
					<?
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
       
        <? if($data[4]==1) 
		{ 
			if($dataArray[0][csf('bill_for')]!=3)
			{
			?>
			<table align="left" cellspacing="0" width="915"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="120" style="word-wrap: break-word;word-break: break-all;">Order No</th>
					<th width="110" style="word-wrap: break-word;word-break: break-all;">Buyer Name</th>
					<th width="100" style="word-wrap: break-word;word-break: break-all;">Grey Required (KG)</th>
					<th width="100" style="word-wrap: break-word;word-break: break-all;">Charge Required (USD)</th>
					<th width="100" style="word-wrap: break-word;word-break: break-all;">Bill Qty (KG)</th> 
					<th width="100" style="word-wrap: break-word;word-break: break-all;">Bill Amount (USD)</th>
					<th width="100" style="word-wrap: break-word;word-break: break-all;">Balance Qty (KG)</th>
					<th width="">Balance Amount (USD)</th>
				</thead>
				<tbody>
				<?
				$grey_req_arr=array();
				$grey_req_sql="select po_break_down_id, sum(requirment) as grey_req from wo_pre_cos_fab_co_avg_con_dtls group by po_break_down_id";
				$grey_req_sql_result =sql_select($grey_req_sql);
				foreach($grey_req_sql_result as $row)
				{
					$grey_req_arr[$row[csf('po_break_down_id')]]=$row[csf('grey_req')];
				}
				
				$charge_req_arr=array(); 
				$charge_req_sql="select job_no, sum(amount) as charge_req from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0 group by job_no";
				$charge_req_sql_result =sql_select($charge_req_sql);
				foreach($charge_req_sql_result as $row)
				{
					$charge_req_arr[$row[csf('job_no')]]=$row[csf('charge_req')];
				}
				
				$bill_arr=array(); 
				$bill_sql="select b.order_id, sum(b.delivery_qty) as bill_qty, sum(b.amount) as bill_amount from  subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.party_source=1 and b.process_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id";
				$bill_sql_result =sql_select($bill_sql);
				foreach($bill_sql_result as $row)
				{
					$bill_arr[$row[csf('order_id')]]['bill_qty']=$row[csf('bill_qty')];
					$bill_arr[$row[csf('order_id')]]['bill_amount']=$row[csf('bill_amount')];
				}
				
				$currency_rate=set_conversion_rate( 2, $dataArray[0][csf('bill_date')] );
				$costingper_id_arr=return_library_array( "select job_no,costing_per_id from wo_pre_cost_dtls",'job_no','costing_per_id');
				
				$ex_po=array_unique(explode(",",$po_id)); $k=1;
				foreach($ex_po as $po_id)
				{
					if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$po_quantity=$order_array[$po_id]['po_quantity'];
					
					$costing_per_id=$costingper_id_arr[$order_array[$po_id]['job']];
					$dzn_qnty=0;
					if($costing_per_id==1) $dzn_qnty=12;
					else if($costing_per_id==3) $dzn_qnty=12*2;
					else if($costing_per_id==4) $dzn_qnty=12*3;
					else if($costing_per_id==5) $dzn_qnty=12*4;
					else $dzn_qnty=1;
					$dzn_qnty_req=$dzn_qnty*$order_array[$po_id]['ratio'];
					$grey_req=($po_quantity/$dzn_qnty_req)*$grey_req_arr[$po_id];
					$charge_req=($po_quantity/$dzn_qnty_req)*$charge_req_arr[$order_array[$po_id]['job']]*$currency_rate;
					
					$bill_qty=$bill_arr[$po_id]['bill_qty'];
					$bill_amount=$bill_arr[$po_id]['bill_amount']*$currency_rate;
					$balance_qty=$grey_req-$bill_qty;
					$balance_amount=$charge_req-$bill_amount;
				?>
					<tr bgcolor="<? echo $bgcolor; ?>"> 
						<td width="120" style="word-wrap: break-word;word-break: break-all;" > <? echo $order_array[$po_id]['po_number']; ?> </td>
						<td width="110" style="word-wrap: break-word;word-break: break-all;" ><p><? echo $order_array[$po_id]['buyer_name']; ?></p></td>
						
						<td width="100" style="word-wrap: break-word;word-break: break-all;"  align="right"><p><? echo number_format($grey_req,2,'.',''); ?></p></td>
						<td width="100" style="word-wrap: break-word;word-break: break-all;"  align="right"><p><? echo number_format($charge_req,2,'.',''); ?></p></td>
						
						<td width="100"  style="word-wrap: break-word;word-break: break-all;" align="right"><p><? echo number_format($bill_qty,2,'.',''); ?></p></td>
						<td width="100" style="word-wrap: break-word;word-break: break-all;"  align="right"><p><? echo number_format($bill_amount,2,'.',''); ?></p></td>
						
						<td width="100" style="word-wrap: break-word;word-break: break-all;"  align="right"><p><? echo number_format($balance_qty,2,'.',''); ?></p></td>
						<td  align="right"><p><? echo number_format($balance_amount,2,'.',''); ?></p></td>
					</tr>
				<?
				}
				?>
				
				</tbody>
			</table>
        <? } 
		} ?>
        
        <br>
    	 <? echo signature_table(220, $data[0], "930px"); ?>   
   </div>
   </div>
	<?
    exit();
}
//  Print 3 End Here

if($action=="knitting_bill_without_collar_cuff_print") 
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$party_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name");
	$yarn_desc_arr=return_library_array( "select id,yarn_description from lib_subcon_charge",'id','yarn_description');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	
	$sql_mst="Select id, bill_no, bill_date, party_id, party_source, location_id, attention, bill_for from subcon_inbound_bill_mst where company_id=$data[0] and id='$data[1]' and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql_mst);
	?>
    <div style="width:930px;">
    <table width="100%" cellpadding="0" cellspacing="0" >
        <tr>
            <td width="70" align="right"> 
            	<img  src='../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='70' width='200' />
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
                        <td align="center" style="font-size:14px"> <? echo show_company($data[0],'',''); ?></td>  
                    </tr>
                    <tr>
                    	<td align="center" style="font-size:18px"><strong><? echo $data[3]; ?></strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
     <table width="930" cellspacing="0" align="" border="0">
            <tr>
                <td width="130"><strong>Bill No :</strong></td> <td width="175"><? echo $dataArray[0][csf('bill_no')]; ?></td>
                <td width="130"><strong>Bill Date: </strong></td><td width="175px"> <? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
                <td width="130"><strong>Source :</strong></td> <td width="175"><? echo $knitting_source[$dataArray[0][csf('party_source')]]; ?></td>
            </tr>
            <tr>
				<?
                    if($dataArray[0][csf('attention')]!='')  $attention='('.$dataArray[0][csf('attention')].')'; else $attention='';
                        
                    if($dataArray[0][csf('party_source')]==2)
                    {
                        $party_add=$dataArray[0][csf('party_id')];
                        $nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add"); 
                        foreach ($nameArray as $result)
                        { 
                            $address="";
                            if($result!="") $address=$result['address_1'];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                        }
                        $party_name=$party_library[$dataArray[0][csf('party_id')]].' : Address :- '.$address.' '.$attention;
                    }
                    else
                    {
                        $party_name=$company_library[$dataArray[0][csf('party_id')]].' '.$attention;
                    }
                ?>
                <td><strong>Party Name : </strong></td><td colspan="3"> <? echo $party_name; ?></td>
                <td><strong>Bill For : </strong></td><td> <? echo $bill_for[$dataArray[0][csf('bill_for')]]; ?></td>
            </tr>
        </table>
         <br>
	<div style="width:100%;">
		<table align="right" cellspacing="0" width="930"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center" style="font-size:10px">
                <th width="30">SL</th>
                <th width="50">Sys. Challan</th>
                <th width="60">Rec. Challan</th>
                <th width="55">D. Date</th>
                <th width="60">Order</th> 
                <th width="60">Buyer</th>
                <th width="60">Style</th>
                <th width="130">F. Des.</th>
                <th width="30">Roll</th>
                <th width="55">D. Qty (W)</th>
                <th width="55">D. Qty (P)</th>
                <th width="30">UOM</th>
                <th width="45">Rate</th>
                <th width="60">Amount</th>
                <th width="40">Currency</th>
                <th>Remarks</th>
            </thead>
		 <?
			$order_array=array();
			$order_sql="select id, order_no, order_uom, cust_buyer, cust_style_ref from subcon_ord_dtls where status_active=1 and is_deleted=0";
			$order_sql_result =sql_select($order_sql);
			foreach($order_sql_result as $row)
			{
				$order_array[$row[csf('id')]]['po_number']=$row[csf('order_no')];
				$order_array[$row[csf('id')]]['buyer_name']=$row[csf('cust_buyer')];
				$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('cust_style_ref')];
			}
			$const_comp_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
			$collar_cuff_arr=return_library_array( "select id,collar_cuff from subcon_delivery_dtls",'id','collar_cuff');
			
     		$i=1;
			$mst_id=$dataArray[0][csf('id')];
			$po_id="";
			$sql_result =sql_select("select id, delivery_id, delivery_date, challan_no, order_id, item_id, uom, packing_qnty, delivery_qty, delivery_qtypcs, rate, amount, remarks, currency_id, process_id from subcon_inbound_bill_dtls  where mst_id='$mst_id' and process_id='2' and status_active=1 and is_deleted=0"); 
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($po_id=="") $po_id=$row[csf('order_id')]; else $po_id.=','.$row[csf('order_id')];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:10px"> 
                    <td><? echo $i; ?></td>
                    <td><p><? echo $row[csf('challan_no')]; ?></p></td>
                    <td><p><? echo $recChallan_arr[$row[csf('challan_no')]][change_date_format($row[csf('delivery_date')])]; ?></p></td>
                    <td><p><? echo change_date_format($row[csf('delivery_date')]); ?></p></td>
                    <td><p><? echo $order_array[$row[csf('order_id')]]['po_number']; ?></p></td>
                    <td><p><? echo $order_array[$row[csf('order_id')]]['buyer_name']; ?></p></td>
                    <td><p><? echo $order_array[$row[csf('order_id')]]['style_ref_no']; ?></p></td>
                    <td><p><? echo $const_comp_arr[$row[csf('item_id')]]; ?></p></td>
                    <td align="right"><p><? echo $row[csf('packing_qnty')]; $tot_packing_qty+=$row[csf('packing_qnty')]; ?>&nbsp;</p></td>
                    <td align="right"><p><? echo number_format($row[csf('delivery_qty')],2,'.',''); $tot_delivery_qty+=$row[csf('delivery_qty')]; ?>&nbsp;</p></td>
                    <td align="right"><p><? echo number_format($row[csf('delivery_qtypcs')],2,'.',''); $tot_delivery_qtypcs+=$row[csf('delivery_qtypcs')]; ?>&nbsp;</p></td>
                    <td><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf('rate')],4,'.',''); ?>&nbsp;</p></td>
                    <td align="right"><p><? echo number_format($row[csf('amount')],2,'.','');  $total_amount += $row[csf('amount')]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
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
        	<tr style="font-size:11px"> 
                <td align="right" colspan="8"><strong>Total</strong></td>
                <td align="right"><? echo $tot_packing_qty; ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_delivery_qty,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_delivery_qtypcs,2,'.',''); ?>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><? echo $format_total_amount=number_format($total_amount,2,'.',''); ?>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
           <tr>
               <td colspan="16" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
           </tr>
        </table>
        <?
			$bill_no=$dataArray[0][csf('bill_no')];
			$sql_terms="Select id,terms from subcon_terms_condition where entry_form=1 and bill_no='$bill_no' ";
			$result_sql_terms =sql_select($sql_terms);

			$i=1;
			if(count($result_sql_terms)>0)
			{
				?>
                <table width="930" align="left" > 
                    <tr><td colspan="2">&nbsp;</td> </tr>
                    <tr><td colspan="2" align="center"><b>TERMS & CONDITION</b></td></tr>
					<?
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
				?>
                 </table>
                <?
			}
			?>
        <br>
		 <? echo signature_table(220, $data[0], "930px"); ?>
   </div>
   </div>
	<?
    exit();
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
                	<td align="center"><input type="button" id="formbuttonplasminus" align="middle" class="formbutton" style="width:100px" value="Close" onClick="js_set_value(document.getElementById('text_new_remarks').value)" /></td>
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

if($action=="kniting_rate_popup")
{
	echo load_html_head_contents("Kniting Rate Popup","../../", 1, 1, $unicode);
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
	
	//print_r($composition_arr);
	$sql="select id, body_part, const_comp, gsm, yarn_description, uom_id, status_active, customer_rate, buyer_id, in_house_rate from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id=2 and comapny_id=$data order by id desc";
	$result = sql_select($sql); $i=1;
	?>
    <table width="750" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
    	<thead>
        	<th width="25">SL</th>
            <th width="80">Buyer</th>
            <th width="100">Body Part</th>
            <th width="160">Construction & Composition</th>
            <th width="60">GSM</th>
            <th width="110">Yarn Description</th>
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
                        <td width="100"><? echo $body_part[$row[csf("body_part")]]; ?></td>
                        <td width="160"><? echo $row[csf("const_comp")]; ?></td>
                        <td width="60"><? echo $row[csf("gsm")]; ?></td>
                        <td width="110"><? echo $row[csf("yarn_description")]; ?></td>
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
		$budgetAmt=array_sum($conversion_costing_arr[$po_id][1])*$exc_rate_arr[$job_arr[$po_id]];
		$budget_amount+=$budgetAmt;
	}
	
	if($update_id!="") $thisbill_cond=" and a.id!='$update_id'"; else $thisbill_cond="";
	
	$previous_bill_sql=sql_select("select sum(b.amount) as amount from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.party_source=1 and a.process_id=2 and b.order_id in ($orderIds) $thisbill_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	
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

if ($action=="job_popup")
{
	echo load_html_head_contents("Job Popup Info","../../", 1, 1, $unicode,'','');
	$data=explode("_",$data);
	$party_name=$data[2];
	$challan_no = ($data[3] !="") ? $data[3] : "0";
	?>
	<script>
		function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >

        	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table width="740" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead> 
                        <tr>                 
                            <th width="150">Company Name</th>
                            <th width="150">Location</th>
                            <th width="80">Job Year</th>
                            <th width="110">Job No</th>
                            <th width="110">Order No</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>           
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> 
                                <input type="hidden" id="selected_job">
                                <? echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "load_drop_down( 'sub_contract_order_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1); ?>
                            </td>
                            <td><? echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $data[1], "",1,"","","","",3 ); ?></td>
                            <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
                            <td><input type="text" name="txt_search_job" id="txt_search_job" class="text_boxes" style="width:100px" placeholder="Search Job" /></td> 
                            <td><input type="text" name="txt_search_order" id="txt_search_order" class="text_boxes" style="width:100px" placeholder="Search Order" /></td> 
                            <td>
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_location_name').value+'_'+<? echo $party_name; ?>+'_'+<? echo $challan_no; ?>+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_search_job').value+'_'+document.getElementById('txt_search_order').value,'create_job_search_list_view','search_div','knitting_bill_issue_controller','setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
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

if($action=="create_job_search_list_view")
{	
	$data=explode('_',$data);
	$company_name=str_replace("'","",$data[0]);
	$location_name=str_replace("'","",$data[1]);
	$party_name=str_replace("'","",$data[2]);
	$challan_no=str_replace("'","",$data[3]);
	$year=str_replace("'","",$data[4]);
	$search_job=str_replace("'","",$data[5]);
	$search_order=trim(str_replace("'","",$data[6]));

	if($search_job=="" && $search_order=="")
	{
		echo "<p style='text-align:center;'>Please Give Job or Order.</p>"; die;
	}
	else
	{
		if($search_job!='') $search_job_cond=" and a.job_no_prefix_num like '%$search_job%'"; else $search_job_cond="";
		if($search_order!='') $search_order_cond=" and b.po_number like '%$search_order%'"; else $search_order_cond="";
	}

	$man_challan_cond="";
	if($challan_no==0) $man_challan_cond=""; else $man_challan_cond="and a.challan_no='$challan_no'";

	if($db_type==0) 
	{
		$booking_without_order="IFNULL(a.booking_without_order,0)";
		$date_sql="YEAR(a.insert_date) as year";
		$date_cond=" and YEAR(a.insert_date)=$year";
	}
	else if ($db_type==2)
	{
		$booking_without_order="nvl(a.booking_without_order,0)";
		$date_sql="TO_CHAR(a.insert_date,'YYYY') as year";
		$date_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year";
	}

	$po_sql="(select distinct(c.po_breakdown_id) from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, inv_receive_master d where a.id=b.mst_id and $booking_without_order=0 and b.id=c.dtls_id and d.id=a.booking_id and a.knitting_source=1 and a.company_id='$data[2]' and a.knitting_company='$data[0]' and a.location_id='$data[1]' and c.trans_type=1 and a.entry_form=22 and c.entry_form=22 and a.receive_basis=9 and a.item_category=13 and d.entry_form=2 and c.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $man_challan_cond)
	union all
	(select distinct(c.po_breakdown_id) from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and $booking_without_order=0 and b.id=c.dtls_id and a.knitting_source=1 and a.company_id='$data[2]' and a.knitting_company='$data[0]' and a.location_id='$data[1]' and c.trans_type=1 and a.entry_form=2 and c.entry_form=2 and a.receive_basis in (1,2) and a.item_category=13 and c.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $man_challan_cond) 
	union all
	(select distinct(c.po_breakdown_id) from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, pro_roll_details d where a.id=b.mst_id and $booking_without_order=0 and b.id=c.dtls_id and b.id=d.dtls_id and a.id=d.mst_id and a.knitting_source=1 and a.company_id='$data[2]' and a.knitting_company='$data[0]' and a.location_id='$data[1]' and c.trans_type=1 and a.entry_form =58 and d.entry_form=58 and c.entry_form=58 and a.receive_basis=10 and a.item_category=13 and c.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $man_challan_cond)
	union all
	(select distinct(c.po_breakdown_id) from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=1 and a.company_id='$data[2]' and a.knitting_company='$data[0]' and a.location_id='$data[1]' and c.trans_type=1 and a.entry_form=22 and c.entry_form=22 and a.item_category=13 and a.receive_basis in (2,4,11) and c.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $man_challan_cond)
	order by po_breakdown_id ASC";
	//echo $po_sql;

	foreach (sql_select($po_sql) as $value) 
	{
		$po_arr[$value[csf("po_breakdown_id")]]=$value[csf("po_breakdown_id")];
	}

	if(count($po_arr)>999)
	{
		if($db_type==0)
		{
			$po_conds="and b.id in (".trim(implode(',', array_filter($po_arr)),',').")";
		}
		else if($db_type==2) 
		{
			$chunked_arr = array_chunk(array_filter($po_arr), 999);
			$po_conds=" and (";
			foreach ($chunked_arr as $po) 
			{
				$po_conds .="b.id in (".implode(',', $po).") or ";
			}
			$po_conds=chop($po_conds, " or ");
			$po_conds .=")";
		}
	}
	else
	{
		$po_conds="and b.id in (".trim(implode(',', array_filter($po_arr)),',').")";
	}

	$sql="SELECT a.id as job_id, a.job_no, a.job_no_prefix_num, $date_sql, a.company_name, a.location_name, a.buyer_name as party_id, b.id, b.job_no_mst, b.po_number as order_no, b.shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active!=0 and b.status_active!=0 $po_conds $search_job_cond $search_order_cond $date_cond order by a.id DESC";

	echo  create_list_view("list_view", "Job No,Year,Order No,Shipment Date","100,100,100,150","550","350",0,$sql, "js_set_value","job_no,job_id","",1,"0,0,0,0",$arr,"job_no_prefix_num,year,order_no,shipment_date", "",'','0,0,0,0') ;
	exit();		 
} 

if($action=="file_upload")
{
	header("Content-Type: application/json");
	$filename = time().$_FILES['file']['name']; 
	$location = "../../file_upload/".$filename; 
    //echo "0**".$filename; die;
	$uploadOk = 1;
	if(empty($mst_id))
	{
		$mst_id=$_GET['mst_id'];
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
	$data_array .="(".$id.",".$mst_id.",'knitting_bill_issue','file_upload/".$filename."','2','".$filename."','".$pc_date_time."')";
	$field_array="id,master_tble_id,form_name,image_location,file_type,real_file_name,insert_date";
	$rID=sql_insert("COMMON_PHOTO_LIBRARY",$field_array,$data_array,1);

	if($db_type==0)
	{
		if($rID==1 && $uploadOk==1)
		{
			mysql_query("COMMIT");
			echo "0**".$new_system_id[0]."**".$mst_id;
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "10**".$mst_id;
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID==1 && $uploadOk==1)
		{
			oci_commit($con);
			echo "0**".$new_system_id[0]."**".$mst_id;
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
?>
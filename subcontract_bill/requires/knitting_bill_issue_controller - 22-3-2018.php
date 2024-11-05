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

$location_arr=return_library_array("select id,location_name from lib_location", "id","location_name");

if($action=="load_field_level_check")
{
	$user=$_SESSION['logic_erp']['user_id'];
	extract($_REQUEST);

	echo "$('#cbo_party_source').val(0);\n";
	echo "$('#cbo_party_source').removeAttr('disabled','disabled');\n";
	echo "load_drop_down( 'requires/knitting_bill_issue_controller',document.getElementById('cbo_company_id').value+'_'+0, 'load_drop_down_party_name', 'party_td' );\n"; 
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
}
	
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","","","","","",3 );
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
	/*
		if($data[1]==2)
	{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "show_list_view(document.getElementById('cbo_company_id').value+'***'+document.getElementById('cbo_location_name').value+'***'+document.getElementById('cbo_party_source').value+'***'+this.value+'***'+document.getElementById('cbo_bill_for').value,'knitting_delivery_list_view','knitting_info_list','requires/knitting_bill_issue_controller','setFilterGrid(\'tbl_list_search\',-1)');","","","","","",5 ); 
	}
	else if($data[1]==1)
	{	
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select Party --", $selected, "show_list_view(document.getElementById('cbo_company_id').value+'***'+document.getElementById('cbo_location_name').value+'***'+document.getElementById('cbo_party_source').value+'***'+this.value+'***'+document.getElementById('cbo_bill_for').value,'knitting_delivery_list_view','knitting_info_list','requires/knitting_bill_issue_controller','setFilterGrid(\'tbl_list_search\',-1)'); fnc_disable_mst_field(this.value);","","","","","",5 ); 
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"","","","",5);
	}
	exit();
	*/
	if($data[1]==2)
	{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "","","","","","",5 ); 
	}
	else if($data[1]==1)
	{	
		$party_arr=return_library_array("select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id","company_name");
		$value = 1;
		if(count($party_arr)==1){
			$value =0;
		}
		
		echo create_drop_down( "cbo_party_name", 150, $party_arr,"",$value, "-- Select Party --", $selected, "","","","","","",5 ); 
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
		echo create_drop_down( "cbo_party_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "","","","","","",5 ); 
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
			}
			else
			{
				$('#txt_search_challan').attr('disabled','disabled');
			}
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="knittingbill_1"  id="knittingbill_1" autocomplete="off">
                <table width="900" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                     <tr>
                         <th colspan="9" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",'1' ); ?></th>
                    </tr>
                     <tr>
                        <th width="130">Company Name</th>
                        <th width="120">Location</th>
                        <th width="100">Source</th>
                        <th width="130">Party Name</th>
                        <th width="70">Bill ID</th>
                        <th width="80">Rec. Challan No.</th>
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
                            <td id="location_td">
								<?
								if($ex_data[0]>0)
								{ 
									$blank_array="select id,location_name from lib_location where company_id=".$ex_data[0]." and status_active =1 and is_deleted=0 order by location_name";
								}
								else
								{
									$blank_array=array();
								}
								echo create_drop_down( "cbo_location_name", 120, $blank_array,"id,location_name", 1, "--Select Location--", $selected,"","","","","","",3);
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
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:65px" placeholder="Write" />
                            </td>
                            <td>
                                <input type="text" name="txt_search_challan" id="txt_search_challan" class="text_boxes" style="width:75px" placeholder="Write" disabled />
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                            </td>
                            <td>
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_party_source').value+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_search_challan').value, 'kniting_bill_list_view', 'search_div', 'knitting_bill_issue_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="9" align="center" height="40" valign="middle">
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
	//if ($data[2]!="" &&  $data[3]!="") $return_date = "and bill_date between '".change_date_format($data[2], "mm-dd-yyyy", "/",1)."' and '".change_date_format($data[3], "mm-dd-yyyy", "/",1)."'"; else $return_date="";
	
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
		if ($data[4]!='') $bill_id_cond=" and a.prefix_no_num like '$data[4]%'"; else $bill_id_cond="";
		if ($data[9]!='') $recChallan_cond=" and challan_no like '$data[9]%'"; else $recChallan_cond="";
	}
	else if($search_type==3)
	{
		if ($data[4]!='') $bill_id_cond=" and a.prefix_no_num like '%$data[4]'"; else $bill_id_cond="";
		if ($data[9]!='') $recChallan_cond=" and challan_no like '%$data[9]'"; else $recChallan_cond="";
	}	
	
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
		$delivery_id_cond="LISTAGG(CAST(b.delivery_id AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY b.delivery_id)";
	}
	
	$rec_man_challan_arr=array();
	$sql_rec="select id, challan_no from inv_receive_master where status_active=1 and is_deleted=0 and item_category=13 $recChallan_cond";
	$sql_rec_result = sql_select($sql_rec); $recId=""; $tot_rows=0;
	foreach($sql_rec_result as $row)
	{
		$tot_rows++;
		$rec_man_challan_arr[$row[csf("id")]]=$row[csf("challan_no")];
		$recId.="'".$row[csf("id")]."',";
	}
	unset($sql_rec_result);
	$rec_id_cond="";
	if ($data[9]!='')
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
	
	$sql= "select a.id, a.bill_no, a.prefix_no_num, $year_cond, a.location_id, a.bill_date, a.party_id, a.party_source, a.bill_for, $delivery_id_cond as delivery_id from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.process_id=2 $company_name $party_name_cond $source_cond $return_date $bill_id_cond $location_cond $year_id_cond $rec_id_cond group by a.id, a.bill_no, a.prefix_no_num, a.insert_date, a.location_id, a.bill_date, a.party_id, a.party_source, a.bill_for order by a.id DESC";
	
	//echo $sql; die;
	$company_id=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$party_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	?> 
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="70">Bill No</th>
                <th width="70">Year</th>
                <th width="120">Location</th>
                <th width="120">Party Source</th>
                <th width="80">Bill Date</th>
                <th width="120">Party</th>
                <th width="80">Bill For</th>
                <th>Challan No</th>
            </thead>
     	</table>
     </div>
     <div style="width:870px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" id="tbl_po_list">
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
	
	$nameArray= sql_select("select id, bill_no, company_id, location_id, bill_date, party_id, party_source, attention, bill_for, is_posted_account,post_integration_unlock from subcon_inbound_bill_mst where id='$data'");
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_bill_no').value 					= '".$row[csf("bill_no")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "load_drop_down( 'requires/knitting_bill_issue_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_location', 'location_td' );\n";
		echo "document.getElementById('cbo_location_name').value			= '".$row[csf("location_id")]."';\n"; 
		echo "document.getElementById('txt_bill_date').value 				= '".change_date_format($row[csf("bill_date")])."';\n";   
		echo "document.getElementById('cbo_party_source').value				= '".$row[csf("party_source")]."';\n"; 
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
		//echo "fnc_disable_mst_field(document.getElementById('cbo_party_name').value);\n";
	}	
	exit();
}

if ($action=="knitting_delivery_list_view")
{
	echo load_html_head_contents("Popup Info","../", 1, 1, $unicode,'','');
	//echo $data;
	$data=explode('***',$data);
	
	$ex_bill_for=$data[4];
	$date_from=$data[5];
	$date_to=$data[6];
	$challan_no=$data[7];
	$update_id=$data[8];
	$str_data=$data[9];

	$job_id=$data[10];
	if($job_id)
	{
		$po_ids="";
		$po_sql="SELECT b.id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.id=$job_id and a.status_active=1 and b.status_active=1";
		foreach (sql_select($po_sql) as $value) 
		{
			$po_ids .= $value[csf("id")].",";
		}
		$po_ids=chop($po_ids, ",");
	}
	//echo $po_ids;

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
	//echo $data[3];
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
				unset($order_sql_result);
				$rate_array=array();
				$rate_sql="select order_id, item_id, rate from subcon_ord_breakdown";
				$rate_sql_result =sql_select($rate_sql);
				foreach ($rate_sql_result as $row)
				{
					$rate_array[$row[csf("order_id")]][$row[csf("item_id")]]=$row[csf("rate")];
				}
				unset($rate_sql_result);
                $i=1;
				if(!$update_id)
				{
					$sql="select b.id, a.challan_no, a.delivery_date, b.process_id, b.item_id, b.delivery_qty, b.delivery_pcs, b.collar_cuff, b.order_id, 0 as roll_qty, 0 as type from  subcon_delivery_mst a, subcon_delivery_dtls b where a.company_id='$data[0]' and a.location_id='$data[1]' and b.bill_status=0 and a.id=b.mst_id and a.party_id='$data[3]' and a.process_id='2' and a.status_active=1 and a.is_deleted=0"; 
				}
				else
				{
					$sql="(select b.id, a.challan_no, a.delivery_date, b.process_id, b.item_id, b.delivery_qty, b.delivery_pcs, b.collar_cuff, b.order_id, 0 as roll_qty, 0 as type from subcon_delivery_mst a, subcon_delivery_dtls b where a.company_id='$data[0]' and a.location_id='$data[1]' and a.id=b.mst_id and a.party_id='$data[3]' and a.process_id=2 and a.status_active=1 and b.bill_status=0)
					 union 
					 	(select b.id, a.challan_no, a.delivery_date, b.process_id, b.item_id, b.delivery_qty, b.delivery_pcs, b.collar_cuff, b.order_id, 0 as roll_qty, 1 as type from subcon_delivery_mst a, subcon_delivery_dtls b where a.company_id='$data[0]' and a.location_id='$data[1]' and a.id=b.mst_id and a.party_id='$data[3]' and a.process_id=2 and b.id in ( $delv_id ) and a.status_active=1 and b.bill_status=1) order by type DESC";
				}
				//echo $sql;
				$sql_result =sql_select($sql);
				
				foreach($sql_result as $row) // for update row
				{
					$process_id_val=$row[csf('process_id')]; $item_name="";
                    if($process_id_val==1 || $process_id_val==5) 
                    {
                    	$item_name=$garments_item[$row[csf('item_id')]]; 
                    }
                    else 
                    {
                    	$item_name=$item_id_arr[$row[csf('item_id')]];
                    }
					$all_value=$row[csf('id')];


					//checking coller & cuff subprocess is present or not
					if(in_array(3, explode(",", $order_array[$row[csf("order_id")]]['process_id']))==false)
					{
						$subprocess_uom=0;
					}
					else
					{
						$subprocess_uom=1;
					}
		

					$str_val=$row[csf('id')].'_'.change_date_format($row[csf('delivery_date')]).'_'.$row[csf('challan_no')].'_'.$row[csf('order_id')].'_'.$order_array[$row[csf('order_id')]]['order_no'].'_'.$order_array[$row[csf('order_id')]]['cust_style_ref'].'_'.$order_array[$row[csf('order_id')]]['cust_buyer'].'_'.$row[csf('roll_qty')].'_0__'.$item_body_part_id[$row[csf('item_id')]].'_'.$body_part[$item_body_part_id[$row[csf('item_id')]]].'_'.$row[csf('item_id')].'_'.$item_name.'_'.$row[csf('delivery_qty')].'_0_0_'.$order_array[$row[csf('order_id')]]['order_uom'].'_'.$row[csf('delivery_pcs')].'_'.$subprocess_uom.'_'.$row[csf('collar_cuff')];
						
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
					?>
					<tr id="tr_<? echo $all_value; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."***".$order_array[$row[csf("order_id")]]['currency_id']; ?>');" >
						<td width="40" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="<? echo $checked_val; ?>" <? echo $ischeck; ?> ></td>
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="70"><p><? echo $row[csf('challan_no')]; ?></p></td>
						<td width="70"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
						<td width="110"><? echo $order_array[$row[csf('order_id')]]['order_no']; ?></td>
						<td width="180"><? echo $item_name; ?></td>
						<td width="100" align="right"><? echo $row[csf('delivery_qty')]; ?>&nbsp;</td>
						<td width="100" align="right"><? echo $row[csf('delivery_pcs')]; ?>&nbsp;</td>
						<td width="100"><? echo $production_process[$row[csf('process_id')]]; ?></td>
						<td><? echo $currency[$order_array[$row[csf("order_id")]]['currency_id']]; ?>
						<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
						<input type="hidden" id="currid<? echo $row[csf('id')]; ?>" style="width:50px" value="<? echo $order_array[$row[csf("order_id")]]['currency_id']; ?>"></td>
					</tr>
					<?php
					$i++;
				}
				
                
				?>
                </table>
         </div>
        <div>
            <table width="1000">
                <tr style="border:none">
                    <td align="center" colspan="11" >
                         <input type="button" id="show_button" align="middle" class="formbutton" style="width:100px" value="Close" onClick="window_close(0)" />
                    </td>
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
		if($ex_bill_for==3) $tbl_wight="820"; else $tbl_wight="1000";
		?>
		</head> 
		<body>
        <div id="list_view_body">
			<div>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_wight; ?>px" class="rpt_table">
					<thead>
                    <?
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
                        <th width="">Roll Qty</th>
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
                        <th width="60">Job</th>
                        <th width="100">Style</th>                    
						<th width="100">Order No</th>
                        <th width="90">Body Part</th>
						<th width="160">Fabric Description</th>
                        <th width="60">Color Type</th>
						<th width="80">Receive Qty</th>
                        <th>Number of Roll</th>
                        <?
					}?>
					</thead>
			 </table>
        </div>
        <div style="width:<? echo $tbl_wight; ?>px;max-height:180px; overflow-y:scroll" id="sewing_production_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_wight-20; ?>px" class="rpt_table" id="tbl_list_search">
            <?
				$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
				$product_dtls_arr=return_library_array( "select id, product_name_details from  product_details_master",'id','product_name_details');
				$currency_arr=return_library_array( "select b.id, a.currency_id from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst",'id','currency_id');
				$determ_arr = return_library_array( "select mst_id, copmposition_id from lib_yarn_count_determina_dtls",'mst_id','copmposition_id');
				
				$bill_qty_array=array();
				$sql_bill="select challan_no, order_id, febric_description_id, body_part_id, item_id, sum(packing_qnty) as roll_qty, sum(delivery_qty) as bill_qty from subcon_inbound_bill_dtls where status_active=1 and is_deleted=0 group by challan_no, order_id, febric_description_id, body_part_id, item_id";
				$sql_bill_result =sql_select($sql_bill);
				foreach($sql_bill_result as $row)
				{
					$bill_qty_array[$row[csf('challan_no')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['qty']=$row[csf('bill_qty')];
					$bill_qty_array[$row[csf('challan_no')]][$row[csf('order_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['roll']=$row[csf('roll_qty')];
				}
				unset($sql_bill_result);
				
				$color_type_array=array();
				$color_type_sql="select a.color_type_id, a.lib_yarn_count_deter_id, b.po_break_down_id from  wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id and a.status_active=1 and is_deleted=0 and a.company_id='$data[1]'";
				$color_type_sql_result =sql_select($color_type_sql);
				foreach($color_type_sql_result as $row)
				{
					$color_type_array[$row[csf('po_break_down_id')]][$row[csf('lib_yarn_count_deter_id')]]['color_type']=$row[csf('color_type_id')];
				}
				unset($color_type_sql_result);
				
				$job_order_arr=array();
				$sql_job="Select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
				$sql_job_result =sql_select($sql_job);
				foreach($sql_job_result as $row)
				{
					$job_order_arr[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
					$job_order_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
					$job_order_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
					$job_order_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
				}
				unset($sql_job_result);
				
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
				}
				else if ($db_type==2)
				{
					$booking_without_order="nvl(a.booking_without_order,0)";
					$booking_without_order_roll="nvl(f.booking_without_order,0)";
				}
                $i=1;
				if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2) $bill_for_id="SM";
				if($ex_bill_for!=3)
				{
					if($bill_for_id==0) $bill_for_id_cond=""; else $bill_for_id_cond="and d.booking_type='$bill_for_id'";
							
					$plan_booking_arr=array();
					$knit_booking="select b.id, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.status_active=1 and b.is_deleted=0";
					$knit_booking_result =sql_select($knit_booking);
					foreach($knit_booking_result as $row)
					{
						$plan_booking_arr[$row[csf('id')]]=$row[csf('booking_no')];
					}
					unset($knit_booking_result);
					
					$roll_dlv_arr=array();
					$sql_dlv="select a.id, a.sys_number, b.barcode_num, c.receive_basis, c.booking_no, c.booking_id from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, inv_receive_master c where a.id=b.mst_id and b.grey_sys_id=c.id and c.knitting_source=1 and c.company_id='$data[3]' and c.knitting_company='$data[0]' and c.entry_form=2";
					$sql_dlv_result =sql_select($sql_dlv);
					foreach($sql_dlv_result as $row)
					{
						/*$roll_dlv_arr[$row[csf('sys_number')]][$row[csf('barcode_num')]]['receive_basis']=$row[csf('receive_basis')];
						$roll_dlv_arr[$row[csf('sys_number')]][$row[csf('barcode_num')]]['booking_no']=$row[csf('booking_no')];
						$roll_dlv_arr[$row[csf('sys_number')]][$row[csf('barcode_num')]]['booking_id']=$row[csf('booking_id')];*/

						$roll_dlv_arr[$row[csf('sys_number')]]['receive_basis']=$row[csf('receive_basis')];
						$roll_dlv_arr[$row[csf('sys_number')]]['booking_no']=$row[csf('booking_no')];
						$roll_dlv_arr[$row[csf('sys_number')]]['booking_id']=$row[csf('booking_id')];
					}
					unset($sql_dlv_result);
					
					$man_challan_cond="";
					if($challan_no=="") $man_challan_cond=""; else $man_challan_cond="and a.challan_no='$challan_no'";
					/*select a.id, f.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, c.po_breakdown_id, sum(c.quantity) as quantity, sum(b.rate) as rate, sum(b.amount) as amount, f.receive_basis, f.booking_no, f.booking_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, pro_grey_prod_delivery_mst d, pro_grey_prod_delivery_dtls e, inv_receive_master f
						where a.id=b.mst_id and $booking_without_order_roll=0 and b.id=c.dtls_id and a.booking_id=d.id and d.id=e.mst_id and e.grey_sys_id=f.id 
						
						and a.knitting_source=1 and f.knitting_source=1 and a.company_id='$data[3]' and a.knitting_company='$data[0]' and a.location_id='$data[1]' and c.trans_type=1 and a.entry_form =58 and f.entry_form=2 and c.entry_form=58 and a.receive_basis=10 and a.item_category=13 and c.trans_id!=0
						and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond
						group by a.id, f.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id, f.receive_basis, f.booking_no, f.booking_id*/

					$po_breakdown_id_conds="";
					if($job_id)
					{
						$po_breakdown_id_conds= " and c.po_breakdown_id in ($po_ids)";
						$date_cond="";
					}
					
					$sql="(select a.id, d.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, c.po_breakdown_id, sum(c.quantity) as quantity, sum(b.rate) as rate, sum(b.amount) as amount, d.receive_basis, d.booking_no, d.booking_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, inv_receive_master d
						 where a.id=b.mst_id and $booking_without_order=0 and b.id=c.dtls_id and d.id=a.booking_id and a.knitting_source=1 and a.company_id='$data[3]' and a.knitting_company='$data[0]' and a.location_id='$data[1]' and c.trans_type=1 and a.entry_form=22 and c.entry_form=22 and a.receive_basis=9 and a.item_category=13 and d.entry_form=2 and c.trans_id!=0
						 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $man_challan_cond $po_breakdown_id_conds
						 group by a.id, d.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id, d.receive_basis, d.booking_no, d.booking_id)
						 union all
						 (select a.id, a.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, c.po_breakdown_id, sum(c.quantity) as quantity, sum(b.rate) as rate, sum(b.amount) as amount, a.receive_basis, a.booking_no, a.booking_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c
						where a.id=b.mst_id and $booking_without_order=0 and b.id=c.dtls_id and a.knitting_source=1 and a.company_id='$data[3]' and a.knitting_company='$data[0]' and a.location_id='$data[1]' and c.trans_type=1 and a.entry_form=2 and c.entry_form=2 and a.receive_basis in (1,2) and a.item_category=13 and c.trans_id!=0
						and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $man_challan_cond $po_breakdown_id_conds
						group by a.id, a.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id, a.receive_basis, a.booking_no, a.booking_id) 
						union all
						(select a.id, a.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, count(b.no_of_roll) as roll_qty, c.po_breakdown_id, sum(c.quantity) as quantity, sum(b.rate) as rate, sum(b.amount) as amount, a.receive_basis, a.booking_no, 0 booking_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, pro_roll_details d
						where a.id=b.mst_id and $booking_without_order=0 and b.id=c.dtls_id and b.id=d.dtls_id and a.id=d.mst_id 
						
						and a.knitting_source=1 and a.company_id='$data[3]' and a.knitting_company='$data[0]' and a.location_id='$data[1]' and c.trans_type=1 and a.entry_form =58 and d.entry_form=58 and c.entry_form=58 and a.receive_basis=10 and a.item_category=13 and c.trans_id!=0
						and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $man_challan_cond $po_breakdown_id_conds
						group by a.id, a.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id, a.receive_basis, a.booking_no)
						union all
						(select a.id, a.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, c.po_breakdown_id, sum(c.quantity) as quantity, sum(b.rate) as rate, sum(b.amount) as amount, a.receive_basis, a.booking_no, a.booking_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c
						where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=1 and a.company_id='$data[3]' and a.knitting_company='$data[0]' and a.location_id='$data[1]' and c.trans_type=1 and a.entry_form=22 and c.entry_form=22 and a.item_category=13 and a.receive_basis in (2,4,11) and c.trans_id!=0
						and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $man_challan_cond $po_breakdown_id_conds
						group by a.id, a.entry_form, a.recv_number_prefix_num, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id, a.receive_basis, a.booking_no, a.booking_id)
						
						order by recv_number_prefix_num ASC";
					//echo $sql;
					
					$sql_result=sql_select($sql);
				
					foreach($sql_result as $row) // for update row
					{
						$all_value=$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$row[csf('prod_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')];
						if(in_array($all_value,$str_arr))
						{
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
							else if ($row[csf('entry_form')]==58)
							{
								$rec_basis=0; $bookinNo=""; $bookingId=0;// booking id is barcode, and booking no is delv id

								/*$rec_basis=$roll_dlv_arr[$row[csf('booking_no')]][$row[csf('booking_id')]]['receive_basis'];
								$bookinNo=$roll_dlv_arr[$row[csf('booking_no')]][$row[csf('booking_id')]]['booking_no'];
								$bookingId=$roll_dlv_arr[$row[csf('booking_no')]][$row[csf('booking_id')]]['booking_id'];*/

								$rec_basis=$roll_dlv_arr[$row[csf('booking_no')]]['receive_basis'];
								$bookinNo=$roll_dlv_arr[$row[csf('booking_no')]]['booking_no'];
								$bookingId=$roll_dlv_arr[$row[csf('booking_no')]]['booking_id'];
								
								if($ex_bill_for==1) { if($rec_basis==0) $independent=4; } //else $independent='';
								if ($rec_basis==2) $booking_no=$plan_booking_arr[$bookinNo]; else if ($rec_basis==1) $booking_no=$bookinNo; else $booking_no=0;
								if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2 && $rec_basis!=0) $bill_for_id="SM";
							}
							
							$ex_booking="";
							if($booking_no!='') $ex_booking=explode('-',$booking_no); else $ex_booking="_";
							
							$str_val=$row[csf('id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$job_order_arr[$row[csf('po_breakdown_id')]]['po'].'_'.$job_order_arr[$row[csf('po_breakdown_id')]]['style'].'_'.$buyer_arr[$job_order_arr[$row[csf('po_breakdown_id')]]['buyer']].'_'.$row[csf('roll_qty')].'_'.$row[csf('febric_description_id')].'_'.$composition_arr[$determ_arr[$row[csf('febric_description_id')]]].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('prod_id')].'_'.$product_dtls_arr[$row[csf('prod_id')]].'_'.$row[csf('quantity')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_0';
							if($independent==4)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr id="tr_<? echo $all_value; ?>" bgcolor="yellow" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>');" >
									<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="1" checked ></td>
									
									<td width="30"><? echo $i; ?></td>
									<td width="60"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
									<td width="70"><p><? echo $row[csf('challan_no')]; ?></p></td>
									<td width="60"><? echo change_date_format($row[csf('receive_date')]); ?></td>
									<td width="60"><? echo $job_order_arr[$row[csf('po_breakdown_id')]]['job']; ?></td>
									<td width="100"><? echo $job_order_arr[$row[csf('po_breakdown_id')]]['style']; ?></td>
									<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $job_order_arr[$row[csf('po_breakdown_id')]]['po']; ?></div></td>
									<td width="90"><? echo $body_part[$row[csf("body_part_id")]]; ?></td>
									<td width="160"><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></td>
									<td width="60"><p><? echo $color_type[$color_type_array[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['color_type']]; ?></p></td>
									<td width="80" align="right"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
									<td align="center"><? echo $row[csf('roll_qty')]; ?>
									
									<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
									<input type="hidden" id="currid<? echo $all_value; ?>" value="<? echo '1'; ?>"></td>
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
									<tr id="tr_<? echo $all_value; ?>" bgcolor="yellow" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>');" >
										<td width="30" align="center"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="1" checked ></td>
										<td width="30"><? echo $i; ?></td>
										<td width="60"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
										<td width="70"><p><? echo $row[csf('challan_no')]; ?></p></td>
										<td width="60"><? echo change_date_format($row[csf('receive_date')]); ?></td>
										<td width="60"><? echo $job_order_arr[$row[csf('po_breakdown_id')]]['job']; ?></td>
										<td width="100"><? echo $job_order_arr[$row[csf('po_breakdown_id')]]['style']; ?></td>
										<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $job_order_arr[$row[csf('po_breakdown_id')]]['po']; ?></div></td>
										<td width="90"><? echo $body_part[$row[csf("body_part_id")]]; ?></td>
										<td width="160"><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></td>
										<td width="60"><p><? echo $color_type[$color_type_array[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['color_type']]; ?></p></td>
										<td width="80" align="right"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
										<td align="center"><? echo $row[csf('roll_qty')]; ?>
										
										<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
										<input type="hidden" id="currid<? echo $all_value; ?>" value="<? echo '1'; ?>"></td>
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
						else if ($row[csf('entry_form')]==58)
						{
							$rec_basis=0; $bookinNo=""; $bookingId=0;// booking id is barcode, and booking no is delv id
							/*$rec_basis=$roll_dlv_arr[$row[csf('booking_no')]][$row[csf('booking_id')]]['receive_basis'];
							$bookinNo=$roll_dlv_arr[$row[csf('booking_no')]][$row[csf('booking_id')]]['booking_no'];
							$bookingId=$roll_dlv_arr[$row[csf('booking_no')]][$row[csf('booking_id')]]['booking_id'];*/

							$rec_basis=$roll_dlv_arr[$row[csf('booking_no')]]['receive_basis'];
							$bookinNo=$roll_dlv_arr[$row[csf('booking_no')]]['booking_no'];
							$bookingId=$roll_dlv_arr[$row[csf('booking_no')]]['booking_id'];
							
							if($ex_bill_for==1) { if($rec_basis==0) $independent=4; } //else $independent='';
							if ($rec_basis==2) $booking_no=$plan_booking_arr[$bookinNo]; else if ($rec_basis==1) $booking_no=$bookinNo; else $booking_no=0;
							if($ex_bill_for==1) $bill_for_id="Fb"; else if($ex_bill_for==2 && $rec_basis!=0) $bill_for_id="SM";
						}
						$ex_booking="";
						if($booking_no!='') $ex_booking=explode('-',$booking_no); else $ex_booking="_";
						//echo $row[csf('booking_no')];
						//if($ex_booking[1]!='Fb') echo $ex_booking[1];
						$bill_qty=$bill_qty_array[$row[csf('recv_number_prefix_num')]][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['qty'];
						
						$avilable_qty=$row[csf('quantity')]-$bill_qty;
						$avilable_roll=$roll_no_arr[$row[csf('dtls_id')]]-$roll_qty;
						$all_value=$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$row[csf('prod_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')];
						$str_val=$row[csf('id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number_prefix_num')].'_'.$row[csf('po_breakdown_id')].'_'.$job_order_arr[$row[csf('po_breakdown_id')]]['po'].'_'.$job_order_arr[$row[csf('po_breakdown_id')]]['style'].'_'.$buyer_arr[$job_order_arr[$row[csf('po_breakdown_id')]]['buyer']].'_'.$row[csf('roll_qty')].'_'.$row[csf('febric_description_id')].'_'.$composition_arr[$determ_arr[$row[csf('febric_description_id')]]].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('prod_id')].'_'.$product_dtls_arr[$row[csf('prod_id')]].'_'.$row[csf('quantity')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_0';
						if($independent==4)
						{
							if($avilable_qty>0)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr id="tr_<? echo $all_value; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>');" >
									<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="2" ></td>
									<td width="30"><? echo $i; ?></td>
									<td width="60"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
									<td width="70"><p><? echo $row[csf('challan_no')]; ?></p></td>
									<td width="60"><? echo change_date_format($row[csf('receive_date')]); ?></td>
									<td width="60"><? echo $job_order_arr[$row[csf('po_breakdown_id')]]['job']; ?></td>
									<td width="100"><? echo $job_order_arr[$row[csf('po_breakdown_id')]]['style']; ?></td>
									<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $job_order_arr[$row[csf('po_breakdown_id')]]['po']; ?></div></td>
									<td width="90"><? echo $body_part[$row[csf("body_part_id")]]; ?></td>
									<td width="160"><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></td>
									<td width="60"><p><? echo $color_type[$color_type_array[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['color_type']]; ?></p></td>
									<td width="80" align="right"><? echo number_format($avilable_qty,2,'.',''); ?></td>
									<td align="center"><? echo $row[csf('roll_qty')]; ?>
									
									<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
									<input type="hidden" id="currid<? echo $all_value; ?>" value="<? echo '1'; ?>"></td>
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
									<tr id="tr_<? echo $all_value; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>');" >
										<td width="30" align="center"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="2" ></td>
										<td width="30"><? echo $i; ?></td>
										<td width="60"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
										<td width="70"><p><? echo $row[csf('challan_no')]; ?></p></td>
										<td width="60"><? echo change_date_format($row[csf('receive_date')]); ?></td>
										<td width="60"><? echo $job_order_arr[$row[csf('po_breakdown_id')]]['job']; ?></td>
										<td width="100"><? echo $job_order_arr[$row[csf('po_breakdown_id')]]['style']; ?></td>
										<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $job_order_arr[$row[csf('po_breakdown_id')]]['po']; ?></div></td>
										<td width="90"><? echo $body_part[$row[csf("body_part_id")]]; ?></td>
										<td width="160"><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></td>
										<td width="60"><p><? echo $color_type[$color_type_array[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['color_type']]; ?></p></td>
										<td width="80" align="right"><? echo number_format($avilable_qty,2,'.',''); ?></td>
										<td align="center"><? echo $row[csf('roll_qty')]; ?>
										
										<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
										<input type="hidden" id="currid<? echo $all_value; ?>" value="<? echo '1'; ?>"></td>
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
					$sql="(select a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, sum(b.grey_receive_qnty) as quantity, c.entry_form, c.receive_basis, c.booking_no, c.booking_id, 0 as order_id 
					from inv_receive_master a, pro_grey_prod_entry_dtls b, inv_receive_master c 
					where a.id=b.mst_id and c.id=a.booking_id and a.booking_without_order=1 and a.knitting_source=1 and a.company_id='$data[3]' and a.knitting_company='$data[0]' and a.location_id='$data[1]' and a.entry_form=22 and a.receive_basis=9 and a.item_category=13 and c.entry_form=2 and c.receive_basis in (0,1,2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.trans_id!=0 $date_cond 
					group by a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.entry_form, c.receive_basis, c.booking_no, c.booking_id)
					union all
					(select a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, sum(b.grey_receive_qnty) as quantity, a.entry_form, a.receive_basis, a.booking_no, a.booking_id, 0 as order_id 
					from inv_receive_master a, pro_grey_prod_entry_dtls b
					where a.id=b.mst_id and a.booking_without_order=1 and a.knitting_source=1 and a.company_id='$data[3]' and b.trans_id!=0 and a.knitting_company='$data[0]' and a.location_id='$data[1]' and a.entry_form=2 and a.item_category=13 and a.receive_basis in (0,1,2)
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond
					group by a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, a.entry_form, a.receive_basis, a.booking_no, a.booking_id)
					union all
					(select a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, sum(b.grey_receive_qnty) as quantity, e.entry_form, e.receive_basis, e.booking_no, e.booking_id, 0 as order_id 
					from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_grey_prod_delivery_mst c, pro_grey_prod_delivery_dtls d, inv_receive_master e
					where a.id=b.mst_id and a.booking_id=c.id and c.id=d.mst_id and d.grey_sys_id=e.id and e.booking_without_order=1
					and a.knitting_source=1 and e.knitting_source=1 and a.company_id='$data[3]' and a.knitting_company='$data[0]' and b.trans_id!=0 and a.location_id='$data[1]' and a.entry_form =58 and e.entry_form=2 and a.receive_basis=10 and a.item_category=13 and e.receive_basis in (0,1,2)
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond
					group by a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, e.entry_form, e.receive_basis, e.booking_no, e.booking_id)
					union all
					(select a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as roll_qty, sum(b.grey_receive_qnty) as quantity, a.entry_form, a.receive_basis, a.booking_no, a.booking_id, 0 as order_id 
					from inv_receive_master a, pro_grey_prod_entry_dtls b
					where a.id=b.mst_id and a.knitting_source=1 and a.company_id='$data[3]' and a.knitting_company='$data[0]' and b.trans_id!=0 and a.location_id='$data[1]' and a.entry_form=22 and a.item_category=13 and a.receive_basis in (2,11)
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond
					group by a.id, a.recv_number_prefix_num, a.buyer_id, a.challan_no, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, a.entry_form, a.receive_basis, a.booking_no, a.booking_id)
					order by recv_number_prefix_num ASC";
					
					//echo $sql; die;
					$sql_result =sql_select($sql);
					foreach($sql_result as $row) // for update row
					{
						$row[csf('order_id')]=0;
						$all_value=$row[csf('recv_number_prefix_num')].'_'.$row[csf('order_id')].'_'.$row[csf('prod_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')];
						if(in_array($all_value,$str_arr))
						{
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
							$ex_booking="";
							if($booking_no!='') $ex_booking=explode('-',$booking_no); else $ex_booking="_";
							
							$str_val=$row[csf('id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number_prefix_num')].'_'.$row[csf('order_id')].'_'.$job_order_arr[$row[csf('order_id')]]['po'].'_'.$job_order_arr[$row[csf('order_id')]]['style'].'_'.$buyer_arr[$job_order_arr[$row[csf('order_id')]]['buyer']].'_'.$row[csf('roll_qty')].'_'.$row[csf('febric_description_id')].'_'.$composition_arr[$determ_arr[$row[csf('febric_description_id')]]].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('prod_id')].'_'.$product_dtls_arr[$row[csf('prod_id')]].'_'.$row[csf('quantity')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_0';
							
							
							if($independent==4)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr id="tr_<?  echo $all_value; ?>" bgcolor="yellow" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>');" >
									<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="1" checked ></td>
									<td width="30" align="center"><? echo $i; ?></td>
                                    <td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
                                    <td width="60"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
                                    <td width="70"><p><? echo $row[csf('challan_no')]; ?></p></td>
                                    <td width="70" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                                    <td width="90"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
                                    <td width="160"><p><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></p></td>
                                    <td width="60"><p><? //echo $color_type[$color_type_array[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['color_type']]; ?></p></td>
                                    <td width="80" align="right"><? echo number_format($avilable_qty,2,'.',''); ?></td>
                                    <td width="" align="right"><? echo number_format($avilable_roll,2,'.',''); ?>
                                    <input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
                                    <input type="hidden" style="width:40px" id="currid<? echo $all_value; ?>" value="<? echo 1; ?>"></td>
								</tr>
								<?php
								$i++;
							}
							else
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr id="tr_<?  echo $all_value; ?>" bgcolor="yellow" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>');" >
									<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="1" checked ></td>
									<td width="30" align="center"><? echo $i; ?></td>
                                    <td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
                                    <td width="60"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
                                    <td width="70"><p><? echo $row[csf('challan_no')]; ?></p></td>
                                    <td width="70" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                                    <td width="90"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
                                    <td width="160"><p><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></p></td>
                                    <td width="60"><p><? //echo $color_type[$color_type_array[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['color_type']]; ?></p></td>
                                    <td width="80" align="right"><? echo number_format($avilable_qty,2,'.',''); ?></td>
                                    <td width="" align="right"><? echo number_format($avilable_roll,2,'.',''); ?>
                                    <input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
                                    <input type="hidden" style="width:40px" id="currid<? echo $all_value; ?>" value="<? echo 1; ?>"></td>
								</tr>
								<?php
								$i++;
							}
						}
					}
					
					foreach($sql_result as $row) // for new row
					{
						$row[csf('order_id')]=0; $independent='';
						if ($row[csf('entry_form')]==2)
						{
							if($row[csf('receive_basis')]==0) $independent=4; //else $independent='';
						}
						else if ($row[csf('entry_form')]==22)
						{
							if($row[csf('receive_basis')]==4) $independent=4; // else $independent='';
						}
						//echo $row[csf('booking_no')];
						//if($ex_booking[1]!='Fb') echo $ex_booking[1];
						$bill_qty=$bill_qty_array[$row[csf('recv_number_prefix_num')]][$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['qty'];
						
						$avilable_qty=$row[csf('quantity')]-$bill_qty;
						$avilable_roll=$roll_no_arr[$row[csf('dtls_id')]]-$roll_qty;
						$all_value=$row[csf('recv_number_prefix_num')].'_'.$row[csf('order_id')].'_'.$row[csf('prod_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')];
						$str_val=$row[csf('id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number_prefix_num')].'_'.$row[csf('order_id')].'_'.$job_order_arr[$row[csf('order_id')]]['po'].'_'.$job_order_arr[$row[csf('order_id')]]['style'].'_'.$buyer_arr[$job_order_arr[$row[csf('order_id')]]['buyer']].'_'.$row[csf('roll_qty')].'_'.$row[csf('febric_description_id')].'_'.$composition_arr[$determ_arr[$row[csf('febric_description_id')]]].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('prod_id')].'_'.$product_dtls_arr[$row[csf('prod_id')]].'_'.$row[csf('quantity')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_0';
						if($independent==4)
						{
							if($avilable_qty>0)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr id="tr_<?  echo $all_value; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>');" > 
									<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="2" ></td>
									<td width="30" align="center"><? echo $i; ?></td>
                                    <td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
                                    <td width="60"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
                                    <td width="70"><p><? echo $row[csf('challan_no')]; ?></p></td>
                                    <td width="70" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                                    <td width="90"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
                                    <td width="160"><p><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></p></td>
                                    <td width="60"><p><? //echo $color_type[$color_type_array[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['color_type']]; ?></p></td>
                                    <td width="80" align="right"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
                                    <td width="" align="right"><? echo number_format($avilable_roll,2,'.',''); ?>
                                    <input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
                                    <input type="hidden" style="width:40px" id="currid<? echo $all_value; ?>" value="<? echo 1; ?>"></td>
								</tr>
								<?php
								$i++;
							}
						}
						else
						{
							if($avilable_qty>0)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr id="tr_<?  echo $all_value; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>');" > 
									<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="2" ></td>
									<td width="30" align="center"><? echo $i; ?></td>
                                    <td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
                                    <td width="60"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
                                    <td width="70"><p><? echo $row[csf('challan_no')]; ?></p></td>
                                    <td width="70" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                                    <td width="90"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
                                    <td width="160"><p><? echo $product_dtls_arr[$row[csf('prod_id')]]; ?></p></td>
                                    <td width="60"><p><? //echo $color_type[$color_type_array[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]]['color_type']]; ?></p></td>
                                    <td width="80" align="right"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
                                    <td width="" align="right"><? echo number_format($avilable_roll,2,'.',''); ?>
                                    <input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
                                    <input type="hidden" style="width:40px" id="currid<? echo $all_value; ?>" value="<? echo 1; ?>"></td>
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
        <div>
            <table width="1000">
                <tr style="border:none">
                    <td align="center" colspan="11" >
                         <input type="button" id="show_button" align="middle" class="formbutton" style="width:100px" value="Close" onClick="window_close(0)" />
                    </td>
                </tr>
           </table>
      </div>
      </div>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?	
	}
	exit();
}

if ($action=="load_dtls_data") 
{
	$ex_data=explode("!^!",$data);

	$upid=$ex_data[0];

	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$roll_no_arr=return_library_array( "select id, no_of_roll from  pro_grey_prod_entry_dtls",'id','no_of_roll');
    
	if($ex_data[1]!=2)
	{
		$product_dtls_arr=return_library_array( "select id,product_name_details from  product_details_master",'id','product_name_details');

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
	
	$sql="select id as upd_id, delivery_id, delivery_date, challan_no, item_id, body_part_id, febric_description_id, uom, packing_qnty as carton_roll, delivery_qty, delivery_qtypcs, lib_rate_id, rate, amount, remarks, order_id, coller_cuff_measurement from subcon_inbound_bill_dtls where mst_id=$upid and process_id=2 and status_active=1 and is_deleted=0 and process_id=2 order by challan_no ASC";
	
	$sql_result_arr =sql_select($sql); $str_val="";
	foreach ($sql_result_arr as $row)
	{
		if($str_val=="") $str_val=$row[csf('delivery_id')].'_'.change_date_format($row[csf('delivery_date')]).'_'.$row[csf('challan_no')].'_'.$row[csf('order_id')].'_'.$job_order_arr[$row[csf('order_id')]]['po'].'_'.$job_order_arr[$row[csf('order_id')]]['style'].'_'.$buyer_arr[$job_order_arr[$row[csf('order_id')]]['buyer']].'_'.$row[csf('carton_roll')].'_'.$row[csf('febric_description_id')].'_'.$composition_arr[$determ_arr[$row[csf('febric_description_id')]]].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('item_id')].'_'.$product_dtls_arr[$row[csf('item_id')]].'_'.$row[csf('delivery_qty')].'_'.$row[csf('delivery_qtypcs')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('lib_rate_id')].'_'.$row[csf('upd_id')].'_'.$row[csf('remarks')].'_'.$row[csf('uom')].'_'.$row[csf('coller_cuff_measurement')];
		
		
		else $str_val.="###".$row[csf('delivery_id')].'_'.change_date_format($row[csf('delivery_date')]).'_'.$row[csf('challan_no')].'_'.$row[csf('order_id')].'_'.$job_order_arr[$row[csf('order_id')]]['po'].'_'.$job_order_arr[$row[csf('order_id')]]['style'].'_'.$buyer_arr[$job_order_arr[$row[csf('order_id')]]['buyer']].'_'.$row[csf('carton_roll')].'_'.$row[csf('febric_description_id')].'_'.$composition_arr[$determ_arr[$row[csf('febric_description_id')]]].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('item_id')].'_'.$product_dtls_arr[$row[csf('item_id')]].'_'.$row[csf('delivery_qty')].'_'.$row[csf('delivery_qtypcs')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('lib_rate_id')].'_'.$row[csf('upd_id')].'_'.$row[csf('remarks')].'_'.$row[csf('uom')].'_'.$row[csf('coller_cuff_measurement')];
		
		
		//$str_val=$row[csf('delivery_id')].'_'.change_date_format($row[csf('delivery_date')]).'_'.$row[csf('challan_no')].'_'.$row[csf('order_id')].'_'.$job_order_arr[$row[csf('order_id')]]['po'].'_'.$job_order_arr[$row[csf('order_id')]]['style'].'_'.$buyer_arr[$job_order_arr[$row[csf('order_id')]]['buyer']].'_'.$row[csf('carton_roll')].'_'.$row[csf('febric_description_id')].'_'.$composition_arr[$determ_arr[$row[csf('febric_description_id')]]].'_'.$row[csf('body_part_id')].'_'.$body_part[$row[csf("body_part_id")]].'_'.$row[csf('item_id')].'_'.$product_dtls_arr[$row[csf('item_id')]].'_'.$row[csf('delivery_qty')].'_'.$row[csf('delivery_qtypcs')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('lib_rate_id')].'_'.$row[csf('upd_id')].'_'.$row[csf('remarks')];
	}
	
	echo $str_val;
	exit();
}

if ($action=="load_php_dtls_form")  //new issue
{
	//echo $data;
	$data = explode("***",$data);
	//echo $data[4];
	
	$old_selected_id=explode(",",$data[0]); 
	$challan=""; $po_id=""; $item_id=""; $body_part_id=""; $febric_description_id="";// $selected_id_arr=array();
	foreach($old_selected_id as $val)
	{
		$selected_id_arr[]=$val;
		$ex_data=explode("_",$val);
		if($challan=="") $challan=$ex_data[0]; else $challan.=','.$ex_data[0];
		if($po_id=="") $po_id=$ex_data[1]; else $po_id.=','.$ex_data[1];
		if($item_id=="") $item_id=$ex_data[2]; else $item_id.=','.$ex_data[2];
		if($body_part_id=="") $body_part_id=$ex_data[3]; else $body_part_id.=','.$ex_data[3];
		if($febric_description_id=="") $febric_description_id=$ex_data[4]; else $febric_description_id.=','.$ex_data[4];
	}
	
	$old_issue_id=explode(",",$data[1]); 
	$old_challan=""; $old_po_id=""; $old_item_id=""; $old_body_part_id=""; $old_febric_description_id=""; 
	foreach($old_issue_id as $value)
	{
		$old_selected_id_arr[]=$value;
		$old_data=explode("_",$value);
		if($old_challan=="") $old_challan=$old_data[0]; else $old_challan.=','.$old_data[0];
		if($old_po_id=="") $old_po_id=$old_data[1]; else $old_po_id.=','.$old_data[1];
		if($old_item_id=="") $old_item_id=$old_data[2]; else $old_item_id.=','.$old_data[2];
		if($old_body_part_id=="") $old_body_part_id=$old_data[3]; else $old_body_part_id.=','.$old_data[3];
		if($old_febric_description_id=="") $old_febric_description_id=$old_data[4]; else $old_febric_description_id.=','.$old_data[4];
	}
	
	$bill_challan=implode(",",array_intersect(explode(",",$challan), explode(",",$old_challan)));
	$bill_po_id=implode(",",array_intersect(explode(",",$po_id), explode(",",$old_po_id)));
	$bill_item_id=implode(",",array_intersect(explode(",",$item_id), explode(",",$old_item_id)));
	$bill_body_part_id=implode(",",array_intersect(explode(",",$body_part_id), explode(",",$old_body_part_id)));
	
	$bill_febric_description_id=implode(",",array_intersect(explode(",",$febric_description_id), explode(",",$old_febric_description_id)));
	$dele_item_id="'".implode("','",explode(",",$bill_item_id))."'";
	//echo $body_part_id.'=='.$old_body_part_id;
	$del_challan=implode(",",array_diff(explode(",",$challan), explode(",",$old_challan)));
	$del_po_id=implode(",",array_diff(explode(",",$po_id), explode(",",$old_po_id)));
	$del_item_id=implode(",",array_diff(explode(",",$item_id), explode(",",$old_item_id)));
	$del_body_part_id=implode(",",array_diff(explode(",",$body_part_id), explode(",",$old_body_part_id)));
	$del_febric_description_id=implode(",",array_diff(explode(",",$febric_description_id), explode(",",$old_febric_description_id)));	
	//$add_del_item_id="'".implode("','",explode(",",$del_item_id))."'";
	if($del_item_id=="") $add_del_item_id="'".implode("','",explode(",",$old_item_id))."'"; else $add_del_item_id="'".implode("','",explode(",",$item_id))."'";
	//echo $item_id.'='.$old_item_id.'='.$bill_item_id.'='.$dele_item_id.'='.$del_item_id;
	
	$delete_challan=implode(",",array_diff(explode(",",$old_challan), explode(",",$challan)));
	$delete_po_id=implode(",",array_diff(explode(",",$old_po_id), explode(",",$po_id)));
	$delete_item_id=implode(",",array_diff(explode(",",$old_item_id), explode(",",$item_id)));
	$delete_body_part_id=implode(",",array_diff(explode(",",$old_body_part_id), explode(",",$body_part_id)));
	$delete_febric_description_id=implode(",",array_diff(explode(",",$old_febric_description_id), explode(",",$febric_description_id)));	
	
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
	
	$del_id=implode(",",$del_id); $bill_id=implode(",",$bill_id); $delete_id=implode(",",$delete_id);
	
	if ($data[3]==2)
	{
		/*$del_id=array_diff(explode(",",$data[0]), explode(",",$data[1]));
		$bill_id=array_intersect(explode(",",$data[0]), explode(",",$data[1]));
		$delete_id=array_diff(explode(",",$data[1]), explode(",",$data[0]));
		$del_id=implode(",",$del_id); $bill_id=implode(",",$bill_id);   $delete_id=implode(",",$delete_id);*/
		//echo $delete_id;
		$yarndesc_arr=return_library_array( "select id, material_description from sub_material_dtls",'id','material_description');
		$febricdesc_arr=return_library_array( "select id, const_comp from lib_subcon_charge",'id','const_comp');
		$collar_cuff_arr=return_library_array( "select id, collar_cuff from subcon_delivery_dtls",'id','collar_cuff');
		
		$order_array=array();
		$order_sql="Select b.id, b.order_no, b.order_uom, b.cust_buyer, b.cust_style_ref, b.rate, b.amount, a.currency_id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0";
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
		}
		$rate_array=array();
		$rate_sql="select order_id, item_id, rate from subcon_ord_breakdown";
		$rate_sql_result =sql_select($rate_sql);
		foreach ($rate_sql_result as $row)
		{
			$rate_array[$row[csf("order_id")]][$row[csf("item_id")]]=$row[csf("rate")];
		}
		//var_dump($order_array);die;
		//echo "select dyeing_fin_bill from variable_settings_subcon where company_id='$data[5]' and variable_list=3 order by id";
		
		
		$sql_bill_lib = sql_select("select dyeing_fin_bill from variable_settings_subcon where company_id='$data[5]' and variable_list=3 order by id");
		//echo $sql_result[0][csf("dyeing_fin_bill")];
		if($db_type==0)
		{
			if( $data[2]!="" )
			{
				$sql="select id as upd_id, delivery_id, delivery_date, challan_no, item_id, packing_qnty as carton_roll, uom, delivery_qty, delivery_qtypcs,  rate, amount, remarks, order_id from subcon_inbound_bill_dtls where mst_id in ($data[2]) and status_active=1 and process_id=2"; 
			}
			else
			{
				if($bill_id!="" && $del_id!="")
					$sql="(select id as upd_id, delivery_id, delivery_date, challan_no, group_concat(item_id  SEPARATOR '_') as item_id, packing_qnty as carton_roll, delivery_qty, delivery_qtypcs, rate, amount, remarks, order_id from subcon_inbound_bill_dtls where delivery_id in ($bill_id) and status_active=1 and is_deleted=0 and process_id=2 group by id, delivery_id, delivery_date, challan_no, item_id, packing_qnty, delivery_qty, rate, amount, remarks, order_id)
					 union
					 (select 0, group_concat(b.id  SEPARATOR '_') as delivery_id, a.delivery_date, a.challan_no, group_concat(b.item_id  SEPARATOR '_') as item_id, b.carton_roll, b.delivery_qty, 0 as delivery_qtypcs, 0, 0, null, b.order_id from  subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and b.id in ($del_id) and a.status_active=1 and b.process_id=2 group by b.id, a.delivery_date, a.challan_no, b.item_id, b.carton_roll, b.delivery_qty, b.order_id) order by delivery_id";
				else if($bill_id!="" && $del_id=="")
					$sql="select id as upd_id, delivery_id, delivery_date, challan_no, item_id, packing_qnty as carton_roll, delivery_qty, delivery_qtypcs, rate, amount, remarks, order_id from subcon_inbound_bill_dtls where delivery_id in ($bill_id) and status_active=1 and is_deleted=0 and process_id=2";
				else  if($bill_id=="" && $del_id!="")
					$sql="select 0, b.id as delivery_id, a.delivery_date, a.challan_no, b.item_id, b.carton_roll, b.delivery_qty, 0, 0, 0, b.order_id from  subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and b.id in ($del_id) and a.status_active=1 and a.is_deleted=0 and b.process_id=2"; 
			}		
		}
		else if ($db_type==2)
		{
			if( $data[2]!="" )
			{
				 $sql="select id as upd_id, delivery_id, delivery_date, challan_no, item_id, packing_qnty as carton_roll, uom, delivery_qty, delivery_qtypcs, rate, amount, remarks, order_id from subcon_inbound_bill_dtls where mst_id in ($data[2]) and status_active=1 and process_id=2 order by id"; 
			}
			else
			{
				if($bill_id!="" && $del_id!="")
					$sql="(select id as upd_id, delivery_id, delivery_date, challan_no, listagg(item_id,'_') within group (order by item_id) as item_id, packing_qnty as carton_roll, uom, delivery_qty, delivery_qtypcs, rate, amount, remarks, order_id from subcon_inbound_bill_dtls where delivery_id in ($bill_id) and status_active=1 and is_deleted=0 and process_id=2 group by id, delivery_id, delivery_date, challan_no, item_id, packing_qnty, uom, delivery_qty, rate, amount, remarks, order_id)
					 union
					 (select 0, listagg(b.id,'_') within group (order by b.id) as delivery_id, a.delivery_date, a.challan_no, listagg(b.item_id,'_') within group (order by b.item_id) as item_id, b.carton_roll, 0, b.delivery_qty, 0 as delivery_qtypcs, 0, 0, null, b.order_id from  subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and b.id in ($del_id) and a.status_active=1 and b.process_id=2 group by b.id, a.delivery_date, a.challan_no, b.item_id, b.carton_roll, b.delivery_qty, b.order_id) order by delivery_id";
				else if($bill_id!="" && $del_id=="")
					$sql="select id as upd_id, delivery_id, delivery_date, challan_no, item_id, packing_qnty as carton_roll, delivery_qty, delivery_qtypcs, rate, amount, remarks, order_id from subcon_inbound_bill_dtls where delivery_id in ($bill_id) and status_active=1 and is_deleted=0 and process_id=2";
				else  if($bill_id=="" && $del_id!="")
					$sql="select 0, b.id as delivery_id, a.delivery_date, a.challan_no, b.item_id, b.carton_roll, b.delivery_qty, 0, 0, 0, b.order_id from  subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and b.id in ($del_id) and a.status_active=1 and a.is_deleted=0 and b.process_id=2"; 
			}
		}
		//echo $sql;
		$sql_result =sql_select($sql);
		$k=0;
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
					<input type="hidden" name="issue_id_all" id="issue_id_all"  style="width:80px" value="<? echo $data[1]; ?>" />
					<input type="hidden" name="delete_id" id="delete_id"  style="width:80px" value="<? echo $delete_id; ?>" />
					<? } ?>
					<input type="hidden" name="curanci_<? echo $k; ?>" id="curanci_<? echo $k; ?>"  style="width:80px" value="<? echo $order_array[$row[csf("order_id")]]['currency_id']; ?>" />
					<input type="hidden" name="updateiddtls_<? echo $k; ?>" id="updateiddtls_<? echo $k; ?>" value="<? echo ($row[csf("upd_id")] != 0 ? $row[csf("upd_id")] : "") ?>">
					<input type="hidden" name="deliveryid_<? echo $k; ?>" id="deliveryid_<? echo $k; ?>" value="<? echo $row[csf("delivery_id")]; ?>">
					<input type="text" name="deleverydate_<? echo $k; ?>" id="deleverydate_<? echo $k; ?>"  class="datepicker" style="width:60px" value="<? echo change_date_format($row[csf("delivery_date")]); ?>" disabled />									
				</td>
				<td>
					<input type="text" name="challenno_<? echo $k; ?>" id="challenno_<? echo $k; ?>"  class="text_boxes" style="width:40px" value="<? echo $row[csf("challan_no")]; ?>" readonly />							 
				</td>
				<td>
					<input type="hidden" name="ordernoid_<? echo $k; ?>" id="ordernoid_<? echo $k; ?>" value="<? echo $row[csf("order_id")]; ?>">
					<input type="text" name="orderno_<? echo $k; ?>" id="orderno_<? echo $k; ?>"  class="text_boxes" style="width:70px" value="<? echo $order_array[$row[csf("order_id")]]['order_no']; ?>" readonly />										
				</td>
				<td>
					<input type="text" name="stylename_<? echo $k; ?>" id="stylename_<? echo $k; ?>"  class="text_boxes" style="width:80px;" value="<? echo $order_array[$row[csf("order_id")]]['cust_style_ref']; ?>" />
				</td>
				<td>
					<input type="text" name="buyername_<? echo $k; ?>" id="buyername_<? echo $k; ?>"  class="text_boxes" style="width:70px" value="<? echo $order_array[$row[csf("order_id")]]['cust_buyer']; ?>" />								
				</td>
				<td>			
					<input name="numberroll_<? echo $k; ?>" id="numberroll_<? echo $k; ?>" type="text" class="text_boxes" style="width:40px" value="<? echo $row[csf("carton_roll")]; ?>" />							
				</td> 
				<td style="display:none">
                    <input type="hidden" name="compoid_<? echo $k; ?>" id="compoid_<? echo $k; ?>" value="<? echo $row[csf("febric_description_id")]; ?>">
					<input type="text" name="yarndesc_<? echo $k; ?>" id="yarndesc_<? echo $k; ?>"  class="text_boxes" style="width:115px" value="<? echo $yarndesc_arr[$row[csf("order_id")]]; ?>" readonly/>
				</td>
                <td>
					<?
						$item=$row[csf("item_id")];
						$body=return_field_value("body_part","lib_subcon_charge", "id=$item","body_part");
					?>
                	<input type="hidden" name="bodypartid_<? echo $k; ?>" id="bodypartid_<? echo $k; ?>" value="<? echo $body; ?>">
					<input type="text" name="bodypartdesc_<? echo $k; ?>" id="bodypartdesc_<? echo $k; ?>"  class="text_boxes" style="width:80px" value="<? echo $body_part[$body]; ?>" readonly/>
				</td>
				<td>
                	<input type="hidden" name="itemid_<? echo $k; ?>" id="itemid_<? echo $k; ?>" value="<? echo $row[csf("item_id")]; ?>">
					<input type="text" name="febricdesc_<? echo $k; ?>" id="febricdesc_<? echo $k; ?>"  class="text_boxes" style="width:135px" title="<? echo $febricdesc_arr[$row[csf("item_id")]]; ?>" value="<? echo $febricdesc_arr[$row[csf("item_id")]]; ?>" readonly/>
				</td>
				<td>
                <?
				$is_disable="";
				if ($body==2 || $body==3)
				{
					if($order_array[$row[csf("order_id")]]['order_uom'] == 0)
					{
						$selected_uom=1;
					}
					$is_disable="";
				}
				else
				{
					if($order_array[$row[csf("order_id")]]['order_uom']== 0)
					{
						$selected_uom=12;
					}
					
					$is_disable="disabled";
				}
					echo create_drop_down( "cbouom_$k", 50, $unit_of_measurement,"", 1, "-UOM-",$order_array[$row[csf("order_id")]]['order_uom'],"",0,'1,2,12',"" );?>
				</td>
                <td>
					<input type="text" name="collarcuff_<? echo $k; ?>" id="collarcuff_<? echo $k; ?>"  class="text_boxes" style="width:65px" value="<? echo $collar_cuff_arr[$row[csf("delivery_id")]]; ?>" readonly/>
				</td>
				<td>
					<input type="text" name="deliveryqnty_<? echo $k; ?>" id="deliveryqnty_<? echo $k; ?>"  class="text_boxes_numeric" style="width:40px" value="<? echo $row[csf("delivery_qty")]; ?>" readonly />
				</td>
                <td>
                     <input type="text" name="deliveryqntypcs_<? echo $k; ?>" id="deliveryqntypcs_<? echo $k; ?>" class="text_boxes_numeric" style="width:40px" value="<? echo $row[csf("delivery_qtypcs")]; ?>" onBlur="qnty_caluculation(<? echo $k; ?>);" <? echo $is_disable; ?> />
                </td>
				<td>
                	<?
					if($sql_bill_lib[0][csf("dyeing_fin_bill")]==2) $readonly="readonly";
					else $readonly="";
					if($row[csf("upd_id")]==0)
					{
						if($sql_bill_lib[0][csf("dyeing_fin_bill")]==2) $rate=$rate_array[$row[csf("order_id")]][$row[csf("item_id")]];
						else $rate="";
					}
					else
					{
						$rate=$row[csf("rate")];
					}
					?>
					<input type="text" name="txtrate_<? echo $k; ?>" id="txtrate_<? echo $k; ?>" class="text_boxes_numeric" style="width:40px" value="<? echo $rate; ?>" onBlur="qnty_caluculation(<? echo $k; ?>);" <? echo $readonly; ?> />
                    <input type="hidden" name="libRateId_<? echo $k; ?>" id="libRateId_<? echo $k; ?>" value="<? //echo $row[csf("lib_rate_id")]; ?>">
				</td>
				<td>
                	<?
						$total_amount=$row[csf("delivery_qty")]*$rate;
					?>
					<input type="text" name="amount_<? echo $k; ?>" id="amount_<? echo $k; ?>" style="width:40px" class="text_boxes_numeric"  value="<? echo $total_amount; ?>" readonly />
				</td>
				<td>
					 <input type="button" name="remarks_<? echo $k; ?>" id="remarks_<? echo $k; ?>" class="formbuttonplasminus" style="width:20px" value="R" onClick="openmypage_remarks(<? echo $k; ?>);" />
                     <input type="hidden" name="remarksvalue_<? echo $k; ?>" id="remarksvalue_<? echo $k; ?>" class="text_boxes" value="<? echo $row[csf("remarks")]; ?>" />
				</td>
			</tr>
		<?	
		}
	}
	else if ($data[3]==1)
	{
		$job_order_arr=array();
		$sql_job="Select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0";
		$sql_job_result =sql_select($sql_job);
		foreach($sql_job_result as $row)
		{
			$job_order_arr[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$job_order_arr[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$job_order_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$job_order_arr[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		
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
		
		$buyer_id_arr=array();
		$sql_non_booking=sql_select("select recv_number_prefix_num, receive_date, buyer_id from inv_receive_master where entry_form=22  and status_active=1 and is_deleted=0 group by recv_number_prefix_num, receive_date, buyer_id");
		foreach($sql_non_booking as $row)
		{
			$buyer_id_arr[$row[csf('recv_number_prefix_num')]][change_date_format($row[csf('receive_date')])]=$row[csf('buyer_id')];
		}
		
		//var_dump($composition_arr);	
		$determ_arr = return_library_array( "select mst_id, copmposition_id from lib_yarn_count_determina_dtls",'mst_id','copmposition_id');	
		$byuer_arr=return_library_array( "select id, short_name from  lib_buyer",'id','short_name');
		$product_dtls_arr=return_library_array( "select id, product_name_details from  product_details_master",'id','product_name_details');
		//var_dump($order_array);die;
		if($del_body_part_id!="") $body_part_cond=" and b.body_part_id in ($body_part_id)"; else $body_part_cond="";
		if($del_challan!="") $del_challan_cond=" and a.recv_number_prefix_num in ($del_challan)"; else $del_challan_cond="";
		if($del_po_id!="") $del_po_id_cond="  and c.po_breakdown_id in ($po_id)"; else $del_po_id_cond="";
		if($add_del_item_id!="") $del_item_id_cond="  and c.prod_id in ($add_del_item_id)"; else $del_item_id_cond="";
		if($del_febric_description_id!="") $del_febric_id_cond="  and b.febric_description_id in ($febric_description_id)"; else $del_febric_id_cond="";
		if($add_del_item_id!="") $wout_item_id_cond="  and b.prod_id in ($add_del_item_id)"; else $wout_item_id_cond="";
		if($data[4]!=3)
		{
			if($db_type==0)
			{
				if( $data[2]!="" )
				{
					$sql="select id as upd_id, delivery_id, delivery_date, challan_no, item_id, body_part_id, febric_description_id, uom, packing_qnty as carton_roll, delivery_qty,delivery_qtypcs, rate, amount, remarks, order_id from subcon_inbound_bill_dtls where mst_id in ($data[2]) and status_active=1 order by id"; 
					$sql_result_arr =sql_select($sql);
					foreach ($sql_result_arr as $row)
					{
						$update_chk_str[]=$row[csf("challan_no")].'_'.$row[csf('order_id')].'_'.$row[csf('item_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')];
						$issue_chk_str[]=$row[csf("challan_no")].'_'.$row[csf('order_id')].'_'.$row[csf('item_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')];
					}
				}
				else
				{
					if($bill_id!="" && $del_id!="")
						$sql="(select id as upd_id, delivery_id as id, delivery_date, challan_no, item_id, body_part_id, febric_description_id, uom, packing_qnty as carton_roll, delivery_qty, rate, amount, remarks, order_id from subcon_inbound_bill_dtls where challan_no in ($bill_challan) and order_id in ($bill_po_id) and item_id in ($bill_item_id) and body_part_id in ($bill_body_part_id) and febric_description_id in ($bill_febric_description_id) and status_active=1 and is_deleted=0 and process_id=2)
						 union all
						 (select 0 as upd_id, a.id, a.receive_date as delivery_date, a.recv_number_prefix_num as challan_no, group_concat(b.prod_id SEPARATOR '_') as item_id, b.body_part_id, b.febric_description_id, null as uom, sum(b.no_of_roll) as carton_roll, sum(c.quantity) as delivery_qty, sum(b.rate) as rate, sum(b.amount) as amount, null as remarks, c.po_breakdown_id as order_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and a.booking_without_order=0 and b.id=c.dtls_id and a.knitting_source=1 and c.trans_type=1 and a.entry_form=22 and c.entry_form=22 and a.item_category=13 and a.receive_basis=9 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $del_challan_cond $del_po_id_cond $del_item_id_cond $body_part_cond $del_febric_id_cond  and c.trans_id!=0 group by a.id, a.recv_number_prefix_num, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id)
						 union all
						 (select 0 as upd_id, a.id, a.receive_date as delivery_date, a.recv_number_prefix_num as challan_no, group_concat(b.prod_id SEPARATOR '_') as item_id, b.body_part_id, b.febric_description_id, null as uom, sum(b.no_of_roll) as carton_roll, sum(c.quantity) as delivery_qty, sum(b.rate) as rate, sum(b.amount) as amount, null as remarks, c.po_breakdown_id as order_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and a.booking_without_order=0 and b.id=c.dtls_id and a.knitting_source=1 and c.trans_type=1 and a.entry_form=2 and c.entry_form=2 and a.item_category=13 and a.receive_basis in(1,2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $del_challan_cond $del_po_id_cond $del_item_id_cond $body_part_cond $del_febric_id_cond and c.trans_id!=0 group by a.id, a.recv_number_prefix_num, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id)
						 union all
						 (select 0 as upd_id, a.id, a.receive_date as delivery_date, a.recv_number_prefix_num as challan_no, group_concat(b.prod_id SEPARATOR '_') as item_id, b.body_part_id, b.febric_description_id, null as uom, sum(b.no_of_roll) as carton_roll, sum(c.quantity) as delivery_qty, sum(b.rate) as rate, sum(b.amount) as amount, null as remarks, c.po_breakdown_id as order_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and a.booking_without_order=0 and b.id=c.dtls_id and a.knitting_source=1 and c.trans_type=1 and a.entry_form=58 and c.entry_form=58 and a.item_category=13 and a.receive_basis=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $del_challan_cond $del_po_id_cond $del_item_id_cond $body_part_cond $del_febric_id_cond and c.trans_id!=0 group by a.id, a.recv_number_prefix_num, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id)
						  order by challan_no ASC";
					else if($bill_id!="" && $del_id=="")
						$sql="select id as upd_id, delivery_id, delivery_date, challan_no, item_id, body_part_id, febric_description_id, uom, packing_qnty as carton_roll, delivery_qty,delivery_qtypcs, rate, amount, remarks, order_id from subcon_inbound_bill_dtls where challan_no in ($bill_challan) and order_id in ($bill_po_id) and item_id in ($bill_item_id) and body_part_id in ($bill_body_part_id) and febric_description_id in ($bill_febric_description_id) and status_active=1 and is_deleted=0 and process_id=2 order by challan_no ASC";
					else  if($bill_id=="" && $del_id!="")
						//$sql="select 0 as upd_id, a.id, a.receive_date as delivery_date, a.recv_number_prefix_num as challan_no, b.prod_id as item_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as carton_roll, sum(c.quantity) as delivery_qty, sum(b.rate) as rate, sum(b.amount) as amount, null as remarks, c.po_breakdown_id as order_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=1 and c.trans_type=1 and a.entry_form=22 and c.entry_form=22 and a.item_category=13 and a.receive_basis=9 and a.recv_number_prefix_num in ($challan) and c.po_breakdown_id in ($po_id) and c.prod_id in ($item_id) and b.body_part_id in ($body_part_id) and b.febric_description_id in ($febric_description_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.recv_number_prefix_num, a.recv_number_prefix_num, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id order by a.recv_number_prefix_num";
						//$sql="select 0, b.id as delivery_id, a.delivery_date, a.challan_no, b.item_id, b.carton_roll, b.delivery_qty, 0, 0, 0, b.order_id from  subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and b.id in ($del_id) and a.status_active=1 and a.is_deleted=0 and b.process_id=2";
						$sql="(select 0 as upd_id, a.receive_date as delivery_date, a.recv_number_prefix_num as challan_no, c.prod_id as item_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as carton_roll, sum(c.quantity) as delivery_qty, 0 as delivery_qtypcs, null as lib_rate_id, sum(b.rate) as rate, sum(b.amount) as amount, null as remarks, c.po_breakdown_id as order_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=1 and c.trans_type=1 and a.entry_form=22 and c.entry_form=22 and a.item_category=13 and a.receive_basis=9 and a.recv_number_prefix_num in ($challan) and c.po_breakdown_id in ($po_id) and c.prod_id in ($item_id) and b.body_part_id in ($body_part_id) and b.febric_description_id in ($febric_description_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and c.trans_id!=0 group by a.id, a.recv_number_prefix_num, a.receive_date, c.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id)
union all
(select 0 as upd_id, a.receive_date as delivery_date, a.recv_number_prefix_num as challan_no, c.prod_id as item_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as carton_roll, sum(c.quantity) as delivery_qty, 0 as delivery_qtypcs, null as lib_rate_id, sum(b.rate) as rate, sum(b.amount) as amount, null as remarks, c.po_breakdown_id as order_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=1 and c.trans_type=1 and a.entry_form=2 and c.entry_form=2 and a.item_category=13 and a.receive_basis in (1,2) and a.recv_number_prefix_num in ($challan) and c.po_breakdown_id in ($po_id) and c.prod_id in ($item_id) and b.body_part_id in ($body_part_id) and b.febric_description_id in ($febric_description_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and c.trans_id!=0 group by a.id, a.recv_number_prefix_num, a.receive_date, c.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id)
union all
(select 0 as upd_id, a.receive_date as delivery_date, a.recv_number_prefix_num as challan_no, c.prod_id as item_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as carton_roll, sum(c.quantity) as delivery_qty, 0 as delivery_qtypcs, null as lib_rate_id, sum(b.rate) as rate, sum(b.amount) as amount, null as remarks, c.po_breakdown_id as order_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=1 and c.trans_type=1 and a.entry_form=58 and c.entry_form=58 and a.item_category=13 and a.receive_basis=10 and a.recv_number_prefix_num in ($challan) and c.po_breakdown_id in ($po_id) and c.prod_id in ($item_id) and b.body_part_id in ($body_part_id) and b.febric_description_id in ($febric_description_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and c.trans_id!=0 group by a.id, a.recv_number_prefix_num, a.receive_date, c.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id)
 order by challan_no ASC"; 
				}
			}
			else if ($db_type==2)
			{
				if( $data[2]!="" )
				{
					$sql="select id as upd_id, delivery_date, challan_no, item_id, body_part_id, febric_description_id, uom, packing_qnty as carton_roll, delivery_qty, delivery_qtypcs, lib_rate_id, rate, amount, remarks, order_id from subcon_inbound_bill_dtls where mst_id in ($data[2]) and status_active=1  order by id"; 
					
					$sql_result_arr =sql_select($sql);
					foreach ($sql_result_arr as $row)
					{
						$update_chk_str[]=$row[csf("challan_no")].'_'.$row[csf('order_id')].'_'.$row[csf('item_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')];
						$issue_chk_str[]=$row[csf("challan_no")].'_'.$row[csf('order_id')].'_'.$row[csf('item_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')];
					}
				}
				else
				{
					if($bill_id!="" && $del_id!="")
						/*$sql="(select id as upd_id, delivery_date, challan_no, item_id, body_part_id, febric_description_id, uom, packing_qnty as carton_roll, delivery_qty, delivery_qtypcs, lib_rate_id, rate, amount, remarks, order_id from subcon_inbound_bill_dtls where challan_no in ($bill_challan) and order_id in ($bill_po_id) and item_id in ($dele_item_id) and body_part_id in ($bill_body_part_id) and febric_description_id in ($bill_febric_description_id) and status_active=1 and is_deleted=0 and process_id=2)
						 union
						 (select 0 as upd_id, a.receive_date as delivery_date, a.recv_number_prefix_num as challan_no, listagg(b.prod_id,',') within group (order by b.prod_id) as item_id, b.body_part_id, b.febric_description_id, null as uom, sum(b.no_of_roll) as carton_roll, sum(c.quantity) as delivery_qty, 0 as delivery_qtypcs, null as lib_rate_id, sum(b.rate) as rate, sum(b.amount) as amount, null as remarks, c.po_breakdown_id as order_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=1 and c.trans_type=1 and a.entry_form=22 and c.entry_form=22 and a.item_category=13  and a.receive_basis=9 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $del_challan_cond $del_po_id_cond $del_item_id_cond $body_part_cond  $del_febric_id_cond group by a.id, a.recv_number_prefix_num, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id) order by challan_no DESC";*/
						 $sql="(select id as upd_id, delivery_date, challan_no, item_id, body_part_id, febric_description_id, uom, packing_qnty as carton_roll, delivery_qty, delivery_qtypcs, lib_rate_id, rate, amount, remarks, order_id from subcon_inbound_bill_dtls where challan_no in ($bill_challan) and order_id in ($bill_po_id) and item_id in ($dele_item_id) and body_part_id in ($bill_body_part_id) and febric_description_id in ($bill_febric_description_id) and status_active=1 and is_deleted=0 and process_id=2)
union all
(select 0 as upd_id, a.receive_date as delivery_date, a.recv_number_prefix_num as challan_no, listagg(b.prod_id,',') within group (order by b.prod_id) as item_id, b.body_part_id, b.febric_description_id, null as uom, sum(b.no_of_roll) as carton_roll, sum(c.quantity) as delivery_qty, 0 as delivery_qtypcs, null as lib_rate_id, sum(b.rate) as rate, sum(b.amount) as amount, null as remarks, c.po_breakdown_id as order_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=1 and c.trans_type=1 and a.entry_form=22 and c.entry_form=22 and a.item_category=13 and a.receive_basis=9 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $del_challan_cond $del_po_id_cond $del_item_id_cond $body_part_cond  $del_febric_id_cond and c.trans_id!=0 group by a.id, a.recv_number_prefix_num, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id)
union all
(select 0 as upd_id, a.receive_date as delivery_date, a.recv_number_prefix_num as challan_no, listagg(b.prod_id,',') within group (order by b.prod_id) as item_id, b.body_part_id, b.febric_description_id, null as uom, sum(b.no_of_roll) as carton_roll, sum(c.quantity) as delivery_qty, 0 as delivery_qtypcs, null as lib_rate_id, sum(b.rate) as rate, sum(b.amount) as amount, null as remarks, c.po_breakdown_id as order_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=1 and c.trans_type=1 and a.entry_form=2 and c.entry_form=2 and a.item_category=13  and a.receive_basis in (1,2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $del_challan_cond $del_po_id_cond $del_item_id_cond $body_part_cond  $del_febric_id_cond and c.trans_id!=0 group by a.id, a.recv_number_prefix_num, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id)
union all
(select 0 as upd_id, a.receive_date as delivery_date, a.recv_number_prefix_num as challan_no, listagg(b.prod_id,',') within group (order by b.prod_id) as item_id, b.body_part_id, b.febric_description_id, null as uom, sum(b.no_of_roll) as carton_roll, sum(c.quantity) as delivery_qty, 0 as delivery_qtypcs, null as lib_rate_id, sum(b.rate) as rate, sum(b.amount) as amount, null as remarks, c.po_breakdown_id as order_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=1 and c.trans_type=1 and a.entry_form=58 and c.entry_form=58 and a.item_category=13 and a.receive_basis=10 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $del_challan_cond $del_po_id_cond $del_item_id_cond $body_part_cond  $del_febric_id_cond and c.trans_id!=0 group by a.id, a.recv_number_prefix_num, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id




)

order by challan_no DESC";
					else if($bill_id!="" && $del_id=="")
						$sql="select id as upd_id, delivery_date, challan_no, item_id, body_part_id, febric_description_id, uom, packing_qnty as carton_roll, delivery_qty, delivery_qtypcs, lib_rate_id, rate, amount, remarks, order_id from subcon_inbound_bill_dtls where challan_no in ($bill_challan) and order_id in ($bill_po_id) and item_id in ($dele_item_id) and body_part_id in ($bill_body_part_id) and febric_description_id in ($bill_febric_description_id) and status_active=1 and is_deleted=0 and process_id=2 order by challan_no DESC";
					else  if($bill_id=="" && $del_id!="")
						/*$sql="select 0 as upd_id, a.receive_date as delivery_date, a.recv_number_prefix_num as challan_no, c.prod_id as item_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as carton_roll, sum(c.quantity) as delivery_qty, 0 as delivery_qtypcs, null as lib_rate_id, sum(b.rate) as rate, sum(b.amount) as amount, null as remarks, c.po_breakdown_id as order_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=1 and c.trans_type=1 and a.entry_form=22 and c.entry_form=22 and a.item_category=13 and a.receive_basis=9 and a.recv_number_prefix_num in ($challan) and c.po_breakdown_id in ($po_id) and c.prod_id in ($item_id) and b.body_part_id in ($body_part_id) and b.febric_description_id in ($febric_description_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.recv_number_prefix_num, a.receive_date, c.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id order by a.recv_number_prefix_num DESC"; */
						
						$sql="(select 0 as upd_id, a.receive_date as delivery_date, a.recv_number_prefix_num as challan_no, c.prod_id as item_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as carton_roll, sum(c.quantity) as delivery_qty, 0 as delivery_qtypcs, null as lib_rate_id, sum(b.rate) as rate, sum(b.amount) as amount, null as remarks, c.po_breakdown_id as order_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=1 and c.trans_type=1 and a.entry_form=22 and c.entry_form=22 and a.item_category=13 and a.receive_basis=9 and a.recv_number_prefix_num in ($challan) and c.po_breakdown_id in ($po_id) and c.prod_id in ($item_id) and b.body_part_id in ($body_part_id) and b.febric_description_id in ($febric_description_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.trans_id!=0 group by a.id, a.recv_number_prefix_num, a.receive_date, c.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id)
union all
(select 0 as upd_id, a.receive_date as delivery_date, a.recv_number_prefix_num as challan_no, c.prod_id as item_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as carton_roll, sum(c.quantity) as delivery_qty, 0 as delivery_qtypcs, null as lib_rate_id, sum(b.rate) as rate, sum(b.amount) as amount, null as remarks, c.po_breakdown_id as order_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=1 and c.trans_type=1 and a.entry_form=2 and c.entry_form=2 and a.item_category=13 and a.receive_basis in (1,2) and a.recv_number_prefix_num in ($challan) and c.po_breakdown_id in ($po_id) and c.prod_id in ($item_id) and b.body_part_id in ($body_part_id) and b.febric_description_id in ($febric_description_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.trans_id!=0 group by a.id, a.recv_number_prefix_num, a.receive_date, c.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id)
union all
(select 0 as upd_id, a.receive_date as delivery_date, a.recv_number_prefix_num as challan_no, c.prod_id as item_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as carton_roll, sum(c.quantity) as delivery_qty, 0 as delivery_qtypcs, null as lib_rate_id, sum(b.rate) as rate, sum(b.amount) as amount, null as remarks, c.po_breakdown_id as order_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.knitting_source=1 and c.trans_type=1 and a.entry_form=58 and c.entry_form=58 and a.item_category=13 and a.receive_basis=10 and a.recv_number_prefix_num in ($challan) and c.po_breakdown_id in ($po_id) and c.prod_id in ($item_id) and b.body_part_id in ($body_part_id) and b.febric_description_id in ($febric_description_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.trans_id!=0 group by a.id, a.recv_number_prefix_num, a.receive_date, c.prod_id, b.body_part_id, b.febric_description_id, c.po_breakdown_id)
 order by challan_no ASC";
				}
			}
		}
		else
		{
			if( $data[2]!="" )
			{
				$sql="select id as upd_id, delivery_date, challan_no, item_id, body_part_id, febric_description_id, uom, packing_qnty as carton_roll, delivery_qty, delivery_qtypcs, lib_rate_id, rate, amount, remarks, order_id from subcon_inbound_bill_dtls where mst_id in ($data[2]) and status_active=1 order by id"; 
				
				$sql_result_arr =sql_select($sql);
				foreach ($sql_result_arr as $row)
				{
					$update_chk_str[]=$row[csf("challan_no")].'_'.$row[csf('order_id')].'_'.$row[csf('item_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')];
					$issue_chk_str[]=$row[csf("challan_no")].'_'.$row[csf('order_id')].'_'.$row[csf('item_id')].'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')];
				}
			}
			else
			{
				if($bill_id!="" && $del_id!="")
					$sql="(select id as upd_id, delivery_date, challan_no, item_id, body_part_id, febric_description_id, uom, packing_qnty as carton_roll, delivery_qty, delivery_qtypcs, lib_rate_id, rate, amount, remarks, order_id from subcon_inbound_bill_dtls where challan_no in ($bill_challan) and item_id in ($dele_item_id) and body_part_id in ($bill_body_part_id) and febric_description_id in ($bill_febric_description_id) and status_active=1 and is_deleted=0 and process_id=2)
							 union all
							 (select 0 as upd_id, a.receive_date as delivery_date, a.recv_number_prefix_num as challan_no, listagg(b.prod_id,',') within group (order by b.prod_id) as item_id, b.body_part_id, b.febric_description_id, null as uom, sum(b.no_of_roll) as carton_roll, sum(b.grey_receive_qnty) as delivery_qty, 0 as delivery_qtypcs, null as lib_rate_id, sum(b.rate) as rate, sum(b.amount) as amount, null as remarks, 0 as order_id from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.knitting_source=1 and a.entry_form=22 and a.item_category=13  and a.receive_basis=9 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.trans_id!=0 $del_challan_cond $wout_item_id_cond $body_part_cond $del_febric_id_cond group by a.id, a.recv_number_prefix_num, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id)
							union all
							(select 0 as upd_id, a.receive_date as delivery_date, a.recv_number_prefix_num as challan_no, listagg(b.prod_id,',') within group (order by b.prod_id) as item_id, b.body_part_id, b.febric_description_id, null as uom, sum(b.no_of_roll) as carton_roll, sum(b.grey_receive_qnty) as delivery_qty, 0 as delivery_qtypcs, null as lib_rate_id, sum(b.rate) as rate, sum(b.amount) as amount, null as remarks, 0 as order_id from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.knitting_source=1 and a.entry_form=2 and a.item_category=13  and a.receive_basis in(1,2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.trans_id!=0 $del_challan_cond $wout_item_id_cond $body_part_cond $del_febric_id_cond group by a.id, a.recv_number_prefix_num, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id) order by challan_no ASC";
				else if($bill_id!="" && $del_id=="")
					$sql="select id as upd_id, delivery_date, challan_no, item_id, body_part_id, febric_description_id, uom, packing_qnty as carton_roll, delivery_qty, delivery_qtypcs, lib_rate_id, rate, amount, remarks, order_id from subcon_inbound_bill_dtls where challan_no in ($bill_challan) and order_id in ($bill_po_id) and item_id in ($dele_item_id) and body_part_id in ($bill_body_part_id) and febric_description_id in ($bill_febric_description_id) and status_active=1 and is_deleted=0 and process_id=2 order by challan_no ASC";
				else  if($bill_id=="" && $del_id!="")
					/*$sql="select 0 as upd_id, a.receive_date as delivery_date, a.recv_number_prefix_num as challan_no, b.prod_id as item_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as carton_roll, sum(b.grey_receive_qnty) as delivery_qty, 0 as delivery_qtypcs, null as lib_rate_id, sum(b.rate) as rate, sum(b.amount) as amount, null as remarks, 0 as order_id from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.knitting_source=1 and a.entry_form=22 and a.item_category=13 and a.receive_basis=9 and a.recv_number_prefix_num in ($challan)  and b.prod_id in ($item_id) and b.body_part_id in ($body_part_id) and b.febric_description_id in ($febric_description_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.recv_number_prefix_num, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id order by a.recv_number_prefix_num"; */
					$sql="(select 0 as upd_id, a.receive_date as delivery_date, a.recv_number_prefix_num as challan_no, b.prod_id as item_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as carton_roll, sum(b.grey_receive_qnty) as delivery_qty, 0 as delivery_qtypcs, null as lib_rate_id, sum(b.rate) as rate, sum(b.amount) as amount, null as remarks, 0 as order_id from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.knitting_source=1 and a.entry_form=22 and a.item_category=13 and a.receive_basis=9 and a.recv_number_prefix_num in ($challan)  and b.prod_id in ($item_id) and b.body_part_id in ($body_part_id) and b.febric_description_id in ($febric_description_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.recv_number_prefix_num, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id)
union all
(select 0 as upd_id, a.receive_date as delivery_date, a.recv_number_prefix_num as challan_no, b.prod_id as item_id, b.body_part_id, b.febric_description_id, sum(b.no_of_roll) as carton_roll, sum(b.grey_receive_qnty) as delivery_qty, 0 as delivery_qtypcs, null as lib_rate_id, sum(b.rate) as rate, sum(b.amount) as amount, null as remarks, 0 as order_id from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.knitting_source=1 and a.entry_form=2 and a.item_category=13 and a.receive_basis in(1,2) and a.recv_number_prefix_num in ($challan) and b.prod_id in ($item_id) and b.body_part_id in ($body_part_id) and b.febric_description_id in ($febric_description_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.trans_id!=0 group by a.id, a.recv_number_prefix_num, a.receive_date, b.prod_id, b.body_part_id, b.febric_description_id) order by challan_no ASC";
			}
		}
			//echo $sql; 
			$sql_result =sql_select($sql); 
			$k=0; $num_rowss=count($sql_result); $previous_chk_str="";
			foreach ($sql_result as $row)
			{
				if( $data[2]!="" )
				{
					//if($data[1]=="") $data[1]=$row[csf("delivery_id")]; else $data[1].=",".$row[csf("delivery_id")]; 
					$data[1]="";
					foreach ($issue_chk_str as $val)
					{
						if($data[1]=="") $data[1]=$val; else $data[1].=",".$val;
					}
				}
				$item_id=implode(",",array_unique(explode(",",$row[csf('item_id')])));
				$chk_str=$row[csf("challan_no")].'_'.$row[csf('order_id')].'_'.$item_id.'_'.$row[csf('body_part_id')].'_'.$row[csf('febric_description_id')];
				
				if($data[2]=="") $previous_chk_str=$selected_id_arr;  else $previous_chk_str=$update_chk_str;
				$count_selected_id_arr=count($selected_id_arr);
				
				//print_r ($selected_id_arr);
				if(in_array($chk_str,$previous_chk_str))
				{
					$k++;
					?>
					 <tr align="center">				
						<td>
						 <? if ($k==$count_selected_id_arr) { ?>
							<input type="hidden" name="issue_id_all" id="issue_id_all"  style="width:80px" value="<? echo $data[1]; ?>" />
							<input type="hidden" name="delete_id" id="delete_id"  style="width:80px" value="<? echo $delete_id; ?>" />
							
							<? } ?>
							<input type="hidden" name="curanci_<? echo $k; ?>" id="curanci_<? echo $k; ?>"  style="width:80px" value="<? echo '1'; ?>" />
							<input type="hidden" name="updateiddtls_<? echo $k; ?>" id="updateiddtls_<? echo $k; ?>" value="<? echo ($row[csf("upd_id")] != 0 ? $row[csf("upd_id")] : "") ?>">
							<input type="hidden" name="deliveryid_<? echo $k; ?>" id="deliveryid_<? echo $k; ?>" style="width:45px" value="<? echo $row[csf("delivery_id")]; ?>">
							<input type="text" name="deleverydate_<? echo $k; ?>" id="deleverydate_<? echo $k; ?>"  class="datepicker" style="width:60px" value="<? echo change_date_format($row[csf("delivery_date")]); ?>" disabled/>									
						</td>
						<td>
							<input type="text" name="challenno_<? echo $k; ?>" id="challenno_<? echo $k; ?>"  class="text_boxes" style="width:40px" value="<? echo $row[csf("challan_no")]; ?>" readonly />							 
						</td>
						<td>
							<input type="hidden" name="ordernoid_<? echo $k; ?>" id="ordernoid_<? echo $k; ?>" value="<? echo $row[csf("order_id")]; ?>">
							<input type="text" name="orderno_<? echo $k; ?>" id="orderno_<? echo $k; ?>"  class="text_boxes" style="width:70px" value="<? echo $job_order_arr[$row[csf('order_id')]]['po']; ?>" readonly />										
						</td>
						<td>
							<input type="text" name="stylename_<? echo $k; ?>" id="stylename_<? echo $k; ?>"  class="text_boxes" style="width:80px;" value="<? echo $job_order_arr[$row[csf('order_id')]]['style']; ?>" readonly />
						</td>
						<td>
                        <?
						if($row[csf('order_id')]==0) $buyer_id=$buyer_id_arr[$row[csf('challan_no')]][change_date_format($row[csf('delivery_date')])];
						else $buyer_id=$job_order_arr[$row[csf('order_id')]]['buyer_name'];
						?>
							<input type="text" name="buyername_<? echo $k; ?>" id="buyername_<? echo $k; ?>"  class="text_boxes" style="width:70px" value="<? echo $byuer_arr[$buyer_id]; ?>" readonly />								
						</td>
						<td>			
							<input name="numberroll_<? echo $k; ?>" id="numberroll_<? echo $k; ?>" type="text" class="text_boxes_numeric" style="width:40px" value="<? echo $row[csf("carton_roll")]; ?>" />							
						</td> 
						<td style="display:none">
							 <input type="hidden" name="compoid_<? echo $k; ?>" id="compoid_<? echo $k; ?>" value="<? echo $row[csf("febric_description_id")]; ?>">
							<input type="text" name="yarndesc_<? echo $k; ?>" id="yarndesc_<? echo $k; ?>"  class="text_boxes" style="width:115px" value="<? echo $composition_arr[$determ_arr[$row[csf('febric_description_id')]]]; ?>" readonly/>
						</td>
						<td>
							<input type="hidden" name="bodypartid_<? echo $k; ?>" id="bodypartid_<? echo $k; ?>" value="<? echo $row[csf("body_part_id")]; ?>">
							<input type="text" name="bodypartdesc_<? echo $k; ?>" id="bodypartdesc_<? echo $k; ?>"  class="text_boxes" style="width:80px" value="<? echo $body_part[$row[csf("body_part_id")]]; ?>" readonly/>
						</td>
						<td>
							<input type="hidden" name="itemid_<? echo $k; ?>" id="itemid_<? echo $k; ?>" value="<? echo $item_id; ?>">
							<input type="text" name="febricdesc_<? echo $k; ?>" id="febricdesc_<? echo $k; ?>"  class="text_boxes" style="width:135px" title="<? echo $product_dtls_arr[$item_id]; ?>" value="<? echo $product_dtls_arr[$item_id]; ?>" readonly/>
						</td>
						<td>
							<? 
							$is_disable="";
							if ($row[csf("body_part_id")]==2 || $row[csf("body_part_id")]==3 || $row[csf("body_part_id")]==40)
							{
								if($row[csf("uom")] == 0)
								{
									$selected_uom=1;
								}
								else
								{
									$selected_uom=$row[csf("uom")];
								}
								$is_disable="";
							}
							else
							{
								if($row[csf("uom")] == 0)
								{
									$selected_uom=12;
								}
								else
								{
									$selected_uom=$row[csf("uom")];
								}
								$is_disable="disabled";
							}
							echo create_drop_down( "cbouom_$k", 50, $unit_of_measurement,"", 1, "-UOM-",$selected_uom,"",0,'1,2,12',"" );?>
						</td>
						<td>
						<input type="text" name="collarcuff_<? echo $k; ?>" id="collarcuff_<? echo $k; ?>"  class="text_boxes" style="width:65px" value="<? //echo $febricdesc_arr[$row[csf("item_id")]]; ?>" readonly/>
						</td>
						<td>
							<input type="text" name="deliveryqnty_<? echo $k; ?>" id="deliveryqnty_<? echo $k; ?>"  class="text_boxes_numeric" style="width:40px" value="<? echo $row[csf("delivery_qty")]; ?>" onBlur="qnty_caluculation(<? echo $k; ?>);" readonly />
						</td>
                        <td>
                             <input type="text" name="deliveryqntypcs_<? echo $k; ?>" id="deliveryqntypcs_<? echo $k; ?>" class="text_boxes_numeric" style="width:40px" value="<? echo $row[csf("delivery_qtypcs")]; ?>" onBlur="qnty_caluculation(<? echo $k; ?>);" <? echo $is_disable; ?> />
                        </td>
						<td>
							<input type="text" name="txtrate_<? echo $k; ?>" id="txtrate_<? echo $k; ?>"  class="text_boxes_numeric" style="width:40px" value="<? echo $row[csf("rate")]; ?>" onBlur="qnty_caluculation(<? echo $k; ?>);" placeholder="Browse"  /><!--onDblClick="openmypage_rate(<?// echo $k; ?>)" readonly-->
                            <input type="hidden" name="libRateId_<? echo $k; ?>" id="libRateId_<? echo $k; ?>" value="<? echo $row[csf("lib_rate_id")]; ?>">
						</td>
							<?
								//$total_amount=$row[csf("delivery_qty")]*$row[csf("rate")];;
							?>
						<td>
							<input type="text" name="amount_<? echo $k; ?>" id="amount_<? echo $k; ?>" style="width:40px"  class="text_boxes_numeric"  value="<? echo $row[csf("amount")]; ?>" readonly />
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
				<input type="hidden" name="updateiddtls_1" id="updateiddtls_1">
				<input type="text" name="deleverydate_1" id="deleverydate_1"  class="datepicker" style="width:60px" readonly />									
			</td>
			<td>
				<input type="text" name="challenno_1" id="challenno_1"  class="text_boxes" style="width:40px" readonly />
			</td>
			<td>
				<input type="hidden" name="ordernoid_1" id="ordernoid_1" value="">
				<input type="text" name="orderno_1" id="orderno_1"  class="text_boxes" style="width:70px" readonly />
			</td>
			<td>
				<input type="text" name="stylename_1" id="stylename_1"  class="text_boxes" style="width:80px;" />
			</td>
			<td>
				<input type="text" name="buyername_1" id="buyername_1"  class="text_boxes" style="width:70px" />
			</td>
			<td>			
				<input name="numberroll_1" id="numberroll_1" type="text" class="text_boxes" style="width:40px" readonly />
			</td>  
			<td style="display:none">
				<input type="text" name="yarndesc_1" id="yarndesc_1"  class="text_boxes" style="width:115px" readonly/>
			</td>
			<td>
				<input type="text" name="bodypart_1" id="bodypart_1"  class="text_boxes" style="width:80px" readonly/>
			</td>
			<td>
				<input type="text" name="febricdesc_1" id="febricdesc_1"  class="text_boxes_numeric" style="width:135px" readonly/>
			</td>
			<td>
				<? echo create_drop_down( "cbouom_1", 50, $unit_of_measurement,"", 1, "-UOM-",0,"",0,"1,2,12" );?>
			</td>
			<td>
				<input type="text" name="deliveryqnty_1" id="deliveryqnty_1"  class="text_boxes_numeric" style="width:40px" />
			</td>
			<td>
				 <input type="text" name="deliveryqntypcs_1" id="deliveryqntypcs_1" class="text_boxes_numeric" style="width:40px" />
			</td>
			<td>
				<input type="text" name="txtrate_1" id="txtrate_1"  class="text_boxes_numeric" style="width:40px" />
			</td>
			<td>
				<input type="text" name="amount_1" id="amount_1" style="width:40px"  class="text_boxes"  readonly />
                <input type="hidden" name="libRateId_1" id="libRateId_1" value="">
			</td>
			<td>
				<input type="button" name="remarks_1" id="remarks_1"  class="formbuttonplasminus" value="R" onClick="openmypage_remarks(1);" />
				<input type="hidden" name="remarksvalue_1" id="remarksvalue_1" class="text_boxes" />
			</td>
		</tr>
	<?	
	}
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
                            <td >
                                <input type="text" id="sltd_<? echo $i;?>" name="sltd_<? echo $i;?>" style="width:100%;" value="<? echo $i; ?>" disabled/> 
                            </td>
                            <td>
                                <input type="text" id="termscondition_<? echo $i;?>" name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  /> 
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
                                <input type="text" id="sltd_<? echo $i;?>" name="sltd_<? echo $i;?>" style="width:100%;" value="<? echo $i; ?>" disabled /> 
                            </td>
                            <td>
                                <input type="text" id="termscondition_<? echo $i;?>" name="termscondition_<? echo $i;?>" style="width:95%" class="text_boxes" value="<? echo $row[csf('terms')]; ?>" /> 
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
						echo load_submit_buttons( $permission, "fnc_kniting_terms_condition", 0,0 ,"reset_form('termscondi_1','','','','')",1) ; 
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

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$bill_process_id="2";
	if ($operation==0)   // Insert Here========================================================================================delivery_id
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
		
		$new_bill_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'KNT', date("Y",time()), 5, "select prefix_no, prefix_no_num from  subcon_inbound_bill_mst where company_id=$cbo_company_id and process_id=$bill_process_id $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
			
		if(str_replace("'",'',$update_id)=="")
		{
			$id=return_next_id( "id", "subcon_inbound_bill_mst", 1 ) ; 	
			$field_array="id, prefix_no, prefix_no_num, bill_no, company_id, location_id, bill_date, party_id, party_source, attention, bill_for, process_id, inserted_by, insert_date";
			$data_array="(".$id.",'".$new_bill_no[1]."','".$new_bill_no[2]."','".$new_bill_no[0]."',".$cbo_company_id.",".$cbo_location_name.",".$txt_bill_date.",".$cbo_party_name.",".$cbo_party_source.",".$txt_attention.",".$cbo_bill_for.",'".$bill_process_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
			//echo "INSERT INTO subcon_inbound_bill_mst (".$field_array.") VALUES ".$data_array; die; 
			$rID=sql_insert("subcon_inbound_bill_mst",$field_array,$data_array,1);
			if($rID) $flag=1; else $flag=0;
			$return_no=$new_bill_no[0]; 
		}
		else
		{
			$id=str_replace("'",'',$update_id);
			$field_array="location_id*bill_date*party_id*party_source*attention*bill_for*updated_by*update_date";
			$data_array="".$cbo_location_name."*".$txt_bill_date."*".$cbo_party_name."*".$cbo_party_source."*".$txt_attention."*".$cbo_bill_for."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			$rID=sql_update("subcon_inbound_bill_mst",$field_array,$data_array,"id",$update_id,0);
			if($rID) $flag=1; else $flag=0;
			$return_no=str_replace("'",'',$txt_bill_no);
		}
		$id1=return_next_id( "id", "subcon_inbound_bill_dtls",1);
		$field_array1 ="id, mst_id, delivery_id, delivery_date, challan_no, order_id, item_id, febric_description_id, body_part_id, uom, packing_qnty, delivery_qty, delivery_qtypcs, lib_rate_id, rate, amount, remarks, currency_id, process_id, inserted_by, insert_date, coller_cuff_measurement";
		$field_array_up ="delivery_id*delivery_date*challan_no*order_id*item_id* febric_description_id*body_part_id*uom*packing_qnty*delivery_qty*delivery_qtypcs*lib_rate_id*rate*amount*remarks*currency_id*updated_by*update_date*coller_cuff_measurement";
		$field_array_delivery="bill_status";
		$field_array_order="cust_buyer*cust_style_ref";
		$process_id=2;
		$add_comma=0;
		for($i=1; $i<=$tot_row; $i++)
		{
			$delivery_id="deliveryid_".$i;
			$delevery_date="deleverydate_".$i;
			$challen_no="challenno_".$i;
			$orderid="ordernoid_".$i;
			$style_name="stylename_".$i;
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
			$updateid_dtls="updateiddtls_".$i;
			$collarcuff="collarcuff_".$i;
			
			if(str_replace("'",'',$$updateid_dtls)=="")  
			{
				if($$amount!="")
				{
					if ($add_comma!=0) $data_array1 .=",";
					$data_array1 .="(".$id1.",".$id.",".$$delivery_id.",".$$delevery_date.",".$$challen_no.",".$$orderid.",".$$item_id.",".$$compoid.",".$$bodypartid.",".$$cbouom.",".$$number_roll.",".$$quantity.",".$$deliveryqntypcs.",".$$libRateId.",".$$rate.",".$$amount.",".$$remarks.",".$$curanci.",'".$process_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$collarcuff.")";
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
				$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$delivery_id."*".$$delevery_date."*".$$challen_no."*".$$orderid."*".$$item_id."*".$$compoid."*".$$bodypartid."*".$$cbouom."*".$$number_roll."*".$$quantity."*".$$deliveryqntypcs."*".$$libRateId."*".$$rate."*".$$amount."*".$$remarks."*".$$curanci."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$$collarcuff.""));
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
		$rID1=execute_query(bulk_update_sql_statement("subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
		if($rID1) $flag=1; else $flag=0;
		if (str_replace("'",'',$cbo_party_source)==2)
		{
			//echo bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array_order,$data_array_order,$order_id_arr );die;
			$rID2=execute_query(bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_array_delivery,$id_arr_delivery ));
			if($rID2) $flag=1; else $flag=0;
			$rID4=execute_query(bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array_order,$data_array_order,$order_id_arr ));
			if($rID4) $flag=1; else $flag=0;
		}
		
		if($data_array1!="")
		{
			//echo "insert into subcon_inbound_bill_dtls (".$field_array1.") values ".$data_array1;die;
			$rID1=sql_insert("subcon_inbound_bill_dtls",$field_array1,$data_array1,1);
			if($rID1) $flag=1; else $flag=0;
		}
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
		if($db_type==2)
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
			exit();
		}
		
		$field_array="location_id*bill_date*party_id*party_source*attention*bill_for*updated_by*update_date";
		$data_array="".$cbo_location_name."*".$txt_bill_date."*".$cbo_party_name."*".$cbo_party_source."*".$txt_attention."*".$cbo_bill_for."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		//$dtls_update_id_array=array();
		$sql_dtls="Select id from subcon_inbound_bill_dtls where mst_id=$update_id and status_active=1 and is_deleted=0";
		$nameArray=sql_select( $sql_dtls );
		foreach($nameArray as $row)
		{
			$dtls_update_id_array[]=$row[csf('id')];
		}
		 
		$return_no=str_replace("'",'',$txt_bill_no);
		
		$id1=return_next_id( "id", "subcon_inbound_bill_dtls",1);
		$field_array1 ="id, mst_id, delivery_id, delivery_date, challan_no, order_id, item_id, febric_description_id, body_part_id, uom, packing_qnty, delivery_qty, delivery_qtypcs, lib_rate_id, rate, amount, remarks, currency_id, process_id, inserted_by, insert_date, coller_cuff_measurement";
		$field_array_up ="delivery_id*delivery_date*challan_no*order_id*item_id*febric_description_id*body_part_id*uom*packing_qnty*delivery_qty*delivery_qtypcs*lib_rate_id*rate*amount*remarks*currency_id*updated_by*update_date*coller_cuff_measurement";
		$field_array_delivery="bill_status";
		$field_array_order="cust_buyer*cust_style_ref";
		$process_id=2;
		$add_comma=0;
		for($i=1; $i<=$tot_row; $i++)
		{
			$delivery_id="deliveryid_".$i;
			$delevery_date="deleverydate_".$i;
			$challen_no="challenno_".$i;
			$orderid="ordernoid_".$i;
			$style_name="stylename_".$i;
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
			$updateid_dtls="updateiddtls_".$i;
			$collarcuff="collarcuff_".$i;
			//echo $up_id=str_replace("'",'',$$updateid_dtls);
				
			if(str_replace("'",'',$$updateid_dtls)=="")  
			{ 
				if ($add_comma!=0) $data_array1 .=",";
				$data_array1 .="(".$id1.",".$id.",".$$delivery_id.",".$$delevery_date.",".$$challen_no.",".$$orderid.",".$$item_id.",".$$compoid.",".$$bodypartid.",".$$cbouom.",".$$number_roll.",".$$quantity.",".$$deliveryqntypcs.",".$$libRateId.",".$$rate.",".$$amount.",".$$remarks.",".$$curanci.",'".$process_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$collarcuff.")";
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
				$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$delivery_id."*".$$delevery_date."*".$$challen_no."*".$$orderid."*".$$item_id."*".$$compoid."*".$$bodypartid."*".$$cbouom."*".$$number_roll."*".$$quantity."*".$$deliveryqntypcs."*".$$libRateId."*".$$rate."*".$$amount."*".$$remarks."*".$$curanci."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$$collarcuff.""));
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
		
		//echo bulk_update_sql_statement( "subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr ); die;
		$rID=sql_update("subcon_inbound_bill_mst",$field_array,$data_array,"id",$update_id,0);
		if($rID) $flag=1; else $flag=0;
		
		$rID1=execute_query(bulk_update_sql_statement("subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
		if($rID1) $flag=1; else $flag=0;
		
		if($data_array1!="")
		{
			//echo "insert into subcon_inbound_bill_dtls (".$field_array1.") values ".$data_array1;
			$rID1=sql_insert("subcon_inbound_bill_dtls",$field_array1,$data_array1,0);
			if($rID1) $flag=1; else $flag=0;
		}
		if (str_replace("'",'',$cbo_party_source)==1)
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
		}
		else
		{
			if(str_replace("'",'',$delete_id)!="")
			{
				$dele_id=str_replace("'",'',$delete_id);
				$delete_id_all="'".implode("','",explode(",",$dele_id))."'";
				//echo "delete from subcon_inbound_bill_dtls where delivery_id in ($delete_id_all)";die;  
				$rID3=execute_query( "delete from subcon_inbound_bill_dtls where delivery_id in ($delete_id_all)",0);
				
				if($rID3) $flag=1; else $flag=0;
				
				//echo "delete from subcon_inbound_bill_dtls where delivery_id in ($delete_id_all)";die;  
				//$new_delete_id=explode("_",str_replace("'",'',$delete_id));
				$new_delete_id=implode(",",explode("_",str_replace("'",'',$delete_id)));
				$all_delv_id=explode(",",$new_delete_id);
				for ($i=0;$i<count($all_delv_id);$i++)
				{
					$id_delivery[]=$all_delv_id[$i];
					$data_delivery[str_replace("'",'',$all_delv_id[$i])] =explode(",",("0"));
				}
				
				//echo bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_array_delivery,$id_arr ); die;
				if($id_delivery!='')
				{
					$rID6=execute_query(bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_delivery,$id_delivery ));
					if($rID6) $flag=1; else $flag=0;
				}
			}
		}
		
		if (str_replace("'",'',$cbo_party_source)==2)
		{
			//echo bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_array_delivery,$id_arr_delivery ); die;
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
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$posted_account);
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
 if($action=="knitting_bill_print") 
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
								echo show_company($data[0],'','');
								/*$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website, vat_number from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
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
								}*/
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
     <table width="915" cellspacing="0" align="" border="0">
            <tr>
                <td width="130"><strong>Bill No :</strong></td> <td width="175"><? echo $dataArray[0][csf('bill_no')]; ?></td>
                <td width="130"><strong>Bill Date: </strong></td><td width="175px"> <? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
                <td width="130"><strong>Source :</strong></td> <td width="175"><? echo $knitting_source[$dataArray[0][csf('party_source')]]; ?></td>
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
				$job_sql="select a.job_no, $job_year, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.total_set_qnty as ratio, b.id, b.po_number, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0";// and a.company_name=$data[0]
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
		/*			$terms_cond_id=$dataArray[0][csf('terms_and_condition')];  
					
					if ($terms_cond_id!='')
					{
		*/				$bill_no=$dataArray[0][csf('bill_no')];
				$sql_terms="Select id,terms from subcon_terms_condition where entry_form=1 and bill_no='$bill_no' ";
				$result_sql_terms =sql_select($sql_terms);
			//}

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
		 <?
            echo signature_table(47, $data[0], "930px");
         ?>
   </div>
   </div>
<?
}


//============================================ Bill Print ( 2 ) similar print report 1 but 2 colom discard in print report=========================
if($action=="knitting_bill_print_2") 
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
								echo show_company($data[0],'','');
								/*$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website, vat_number from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
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
								}*/
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
            <tr>
                <td width="130"><strong>Bill No :</strong></td> <td width="175"><? echo $dataArray[0][csf('bill_no')]; ?></td>
                <td width="130"><strong>Bill Date: </strong></td><td width="175px"> <? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
                <td width="130"><strong>Source :</strong></td> <td width="175"><? echo $knitting_source[$dataArray[0][csf('party_source')]]; ?></td>
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
	<div style="width:103%;">
		<table align="right" cellspacing="0" width="930"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center" style="font-size:16px">
                <th width="30">SL</th>
                <th width="50">Sys. Challan</th>
                <th width="50">Rec. Challan</th>
                <th width="55">D. Date</th>
                <th width="60">Order</th> 
                <th width="60">Buyer</th>
                <th width="60">Style</th>
                <th width="40">Job</th>
                <th width="35">Year</th>
                <th width="100">Fabric Description</th>
                <th width="25">Roll</th>
                <th width="55">D. Qty (W)</th>
                <th width="55">D. Qty (P)</th>
                <th width="30">UOM</th>
                <th width="40">Rate</th>
                <th width="70">Amount</th>
                <th>Currency</th>
            
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
				$const_comp_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
				$collar_cuff_arr=return_library_array( "select id,collar_cuff from subcon_delivery_dtls",'id','collar_cuff');
			}
			else if($dataArray[0][csf('party_source')]==1)
			{
				$order_array=array();
				$job_sql="select a.job_no, $job_year, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.total_set_qnty as ratio, b.id, b.po_number, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and a.company_name=$data[0]";
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
				$const_comp_arr=return_library_array( "select id,product_name_details from product_details_master",'id','product_name_details');
				$recChallan_arr=array();
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
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:16px"> 
                    <td><? echo $i; ?></td>
                    <td><p><? echo $row[csf('challan_no')]; ?></p></td>
                    <td><div style="word-wrap:break-word; width:50px"><? echo $recChallan_arr[$row[csf('challan_no')]][change_date_format($row[csf('delivery_date')])]; ?></div></td>
                    <td><p><? echo change_date_format($row[csf('delivery_date')]); ?></p></td>
                    <td><div style="word-wrap:break-word; width:60px"><? echo $order_array[$row[csf('order_id')]]['po_number']; ?></div></td>
                    <td><div style="word-wrap:break-word; width:60px"><? echo $buyer_id_name; ?></div></td>
                    <td><div style="word-wrap:break-word; width:60px"><? echo $order_array[$row[csf('order_id')]]['style_ref_no']; ?></div></td>
                    <td align="center"><div style="word-wrap:break-word; width:40px"><? echo $order_array[$row[csf('order_id')]]['job_no_prefix_num']; ?></div></td>
                     <td align="center"><div style="word-wrap:break-word; width:35px"><? echo $order_array[$row[csf('order_id')]]['year']; ?></div></td>
                    <td><div style="word-wrap:break-word; width:100px"><? echo $const_comp_arr[$row[csf('item_id')]]; ?></div></td>
                  
                    <td align="right"><p><? echo $row[csf('packing_qnty')]; $tot_packing_qty+=$row[csf('packing_qnty')]; ?>&nbsp;</p></td>
                    <td align="right"><p><? echo number_format($row[csf('delivery_qty')],2,'.',''); $tot_delivery_qty+=$row[csf('delivery_qty')]; ?>&nbsp;</p></td>
                    <td align="right"><p><? echo number_format($row[csf('delivery_qtypcs')],2,'.',''); $tot_delivery_qtypcs+=$row[csf('delivery_qtypcs')]; ?>&nbsp;</p></td>
                    <td><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf('rate')],4,'.',''); ?>&nbsp;</p></td>
                    <td align="right"><p><? echo number_format($row[csf('amount')],2,'.','');  $total_amount += $row[csf('amount')]; ?>&nbsp;</p></td>

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
        	<tr style="font-size:12px"> 
                <td align="right" colspan="10"><strong>Total</strong></td>
                <td align="right"><? echo $tot_packing_qty; ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_delivery_qty,2,'.',''); ?>&nbsp;</td>
                <td align="right"><? echo number_format($tot_delivery_qtypcs,2,'.',''); ?>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><? echo $format_total_amount=number_format($total_amount,2,'.',''); ?>&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
           <tr>
               <td colspan="17" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
           </tr>
        </table>
        <?
/*			$terms_cond_id=$dataArray[0][csf('terms_and_condition')];  
			
			if ($terms_cond_id!='')
			{
*/				$bill_no=$dataArray[0][csf('bill_no')];
				$sql_terms="Select id,terms from subcon_terms_condition where entry_form=1 and bill_no='$bill_no' ";
				$result_sql_terms =sql_select($sql_terms);
			//}

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
			}
			?>
        </table>
        <br>
       
        <? if($data[4]==1) 
		{ 
			if($dataArray[0][csf('bill_for')]!=3)
			{
			?>
			<table align="right" cellspacing="0" width="930"  border="1" rules="all" class="rpt_table" >
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
		 <?
            echo signature_table(47, $data[0], "930px");
         ?>
   </div>
   </div>
<?
}





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
								echo show_company($data[0],'','');
								/*$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website, vat_number from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
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
								}*/
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
            <tr>
                <td width="130"><strong>Bill No :</strong></td> <td width="175"><? echo $dataArray[0][csf('bill_no')]; ?></td>
                <td width="130"><strong>Bill Date: </strong></td><td width="175px"> <? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
                <td width="130"><strong>Source :</strong></td> <td width="175"><? echo $knitting_source[$dataArray[0][csf('party_source')]]; ?></td>
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
		 <?
            echo signature_table(47, $data[0], "930px");
         ?>
   </div>
   </div>
<?
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
		$budgetAmt=$conversion_costing_arr[$po_id][1]*$exc_rate_arr[$job_arr[$po_id]];
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
                        <tr>
                        <td align="center"> 
                            <input type="hidden" id="selected_job">
                            <? 
                               echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "load_drop_down( 'sub_contract_order_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1); 
                            ?>
                        </td>
                        <td align="center">
                            <? 
                               echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $data[1], "",1,"","","","",3 );
                            ?>
                        </td>
                        <td align="center">
                            <? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );  ?>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_search_job" id="txt_search_job" class="text_boxes" style="width:100px" placeholder="Search Job" />
                        </td> 
                        <td align="center">
                            <input type="text" name="txt_search_order" id="txt_search_order" class="text_boxes" style="width:100px" placeholder="Search Order" />
                        </td> 
                        <td align="center">
 							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_location_name').value+'_'+<? echo $party_name; ?>+'_'+<? echo $challan_no; ?>+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_search_job').value+'_'+document.getElementById('txt_search_order').value,'create_job_search_list_view','search_div','knitting_bill_issue_controller','setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
						</td>
                        </tr>
                        <tr>
                            <td colspan="6" align="center" valign="top" id=""><div id="search_div"></div></td>
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

	$sql="SELECT a.id as job_id, a.job_no, a.job_no_prefix_num, $date_sql, a.company_name, a.location_name, a.buyer_name as party_id, b.id, b.job_no_mst, b.po_number as order_no, b.shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $po_conds $search_job_cond $search_order_cond $date_cond order by a.id DESC";

	echo  create_list_view("list_view", "Job No,Year,Order No,Shipment Date","100,100,100,150","550","350",0,$sql, "js_set_value","job_no,job_id","",1,"0,0,0,0",$arr,"job_no_prefix_num,year,order_no,shipment_date", "",'','0,0,0,0') ;
	exit();		 
} 

?>
<?

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

/*if ($action=="load_drop_down_po_company")
{
	if($data ==1){
		echo create_drop_down( "cbo_lccompany_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Po Company-", $selected, "load_drop_down( 'requires/bill_processing_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
	}else{
		echo create_drop_down( "cbo_lccompany_id", 130, $blank_array,"", 1, "-Po Company-", $selected, "" );
	}

}
*/
if ($action == "load_drop_down_po_company") {
	$data = explode("_", $data);
	$company_id = $data[1];

	if ($data[0] == 1) {
		echo create_drop_down("cbo_party_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 0, "--Select Party--", "$company_id", "", "");
	} else if ($data[0] == 2) {

		$partysQl = sql_select("select id,tag_company,party_type from lib_buyer where status_active=1 and is_deleted=0 and tag_company='".$company_id."'");

		$buyerId = "";
		foreach ($partysQl as $row) {

			$partyTypeArr = explode(",", $row[csf('party_type')]);

			foreach ($partyTypeArr as $partyType) {
				if($partyType == 3)
				{
					$buyerId .=  $row[csf('id')].",";
				}
			}
		}

		$buyerIds = chop($buyerId,",");

		if($buyerIds!="")
		{
			echo create_drop_down("cbo_party_id", 130, "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 and id in($buyerIds)", "id,buyer_name", 1, "--Select Party--", 0, "");
		}


	}
	exit();
}
if ($action=="load_drop_down_buyer")
{
	$data = explode("_", $data);
	$within_group = $data[1];
	$company_id = $data[0];
	if ($within_group == 1) {
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-All Buyer-", $selected, "" );
	}
	exit();
}
if ($action == "load_drop_down_buyer_working") {
	$data = explode("_", $data);
	$company_id = $data[1];

	if ($company_id == 0) {
		echo create_drop_down("cbo_buyer_id", 120, $blank_array, "", 1, "--Select Buyer--", 0, "");
	} else {
		if ($data[0] == 1) {
			echo create_drop_down("cbo_buyer_id", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Buyer --", "0", "");
		} else if ($data[0] == 2) {
			echo create_drop_down("cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 0);
		}
	}
	exit();
}

if ($action=="load_drop_down_buyer_within_no")
{
	$dataArr = explode("_",$data);
	if($dataArr[0]==2)
	{
		echo create_drop_down("cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$dataArr[1]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");

	}else {
		echo create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "-All Buyer-", $selected, "",0,"" );
	}
	exit();
}


if($action=="fsoNo_popup")
{
  	echo load_html_head_contents("Job Info","../../../../", 1, 1, '','1','');
	extract($_REQUEST);
?>
	<script>
	
		function js_set_value(job_id,job_no,booking_no)
		{	
			document.getElementById('hidden_fso_id').value=job_id;
			document.getElementById('hidden_fso_no').value=job_no;
			document.getElementById('hidden_booking_no').value=booking_no;
			parent.emailwindow.hide();
		}
	
    </script>
</head>
<body>
<div align="center">
	<fieldset style="width:830px;margin-left:4px;">
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
                <thead>
                	<th>Within Group</th>
                    <th>Search By</th>
                    <th>Search</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="hidden_fso_id" id="hidden_fso_id" value="">
                         <input type="hidden" name="hidden_fso_no" id="hidden_fso_no" value="">
                          <input type="hidden" name="hidden_booking_no" id="hidden_booking_no" value="">
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">	
                        <?
                            echo create_drop_down( "cbo_within_group", 150, $yes_no,"",1, "--Select--", "",$dd,0 );
                        ?>
                    </td>   
                    <td align="center">	
                        <?
                            $search_by_arr=array(1=>"Sales Order No",2=>"Sales / Booking No",3=>"Style Ref.");
                            echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>                 
                    <td align="center">				
                        <input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('cbo_within_group').value, 'create_fso_search_list_view', 'search_div', 'bill_processing_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
            </table>
            <div id="search_div" style="margin-top:10px"></div>   
        </form>
    </fieldset>
</div>    
</body>           
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_fso_search_list_view")
{
	$data=explode('_',$data);
	
	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	
	$search_string=trim($data[0]);
	$search_by =$data[1];
	$company_id =$data[2];
	$within_group=$data[3];
	
	$search_field_cond='';
	if($search_string!="")
	{
		if($search_by==1)
		{
			$search_field_cond=" and job_no like '%".$search_string."'";
		}
		else if($search_by==2)
		{
			$search_field_cond=" and sales_booking_no like '%".$search_string."'";
		}
		else
		{
			$search_field_cond=" and style_ref_no like '".$search_string."%'";
		}
	}
		
	if($within_group==0) $within_group_cond=""; else $within_group_cond=" and within_group=$within_group";
	
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql = "select id, $year_field, job_no_prefix_num, job_no, within_group, sales_booking_no, booking_date, buyer_id, style_ref_no, location_id from fabric_sales_order_mst where status_active=1 and is_deleted=0 and company_id=$company_id $within_group_cond $search_field_cond order by id"; 
	//echo $sql;//die;
	$result = sql_select($sql);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="90">Sales Order No</th>
            <th width="60">Year</th>
            <th width="80">Within Group</th>
            <th width="70">Buyer</th>               
            <th width="120">Sales/ Booking No</th>
            <th width="80">Booking date</th>
            <th width="110">Style Ref.</th>
            <th>Location</th>
        </thead>
	</table>
	<div style="width:800px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					 
                if($row[csf('within_group')]==1)
					$buyer=$company_arr[$row[csf('buyer_id')]]; 
				else
					$buyer=$buyer_arr[$row[csf('buyer_id')]];
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>','<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('sales_booking_no')]; ?>');"> 
                    <td width="40"><? echo $i; ?></td>
                    <td width="90"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
                    <td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="80"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
                    <td width="70"><p><? echo $buyer; ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>               
                    <td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                    <td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                    <td><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
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




if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$store_arr=return_library_array( "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id  and a.status_active=1 and a.is_deleted=0 and a.company_id=$cbo_company_id and  b.category_type=2 order by a.store_name",'id','store_name');


	$company_name= str_replace("'","",$cbo_company_id);
	$within_group= str_replace("'","",$cbo_within_group);
	$lccompany_id= str_replace("'","",$cbo_lccompany_id);
	$buyer_id= str_replace("'","",$cbo_buyer_id);
	$txt_date_from= str_replace("'","",$txt_date_from);
	$txt_date_to= str_replace("'","",$txt_date_to);
	$txt_fso_no= str_replace("'","",$txt_fso_no);
	$hdn_fso_id= str_replace("'","",$hdn_fso_id);

	if($within_group==1)
	{
		if($buyer_id>0)
		{
			$buyer_id_cond=" and e.po_buyer=$buyer_id" ;
			$buyer_id_cond_2=" and c.po_buyer=$buyer_id" ;
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		if($buyer_id>0)
		{
			$buyer_id_cond=" and e.buyer_id=$buyer_id" ;
			$buyer_id_cond_2=" and c.buyer_id=$buyer_id" ;
		}
		else
		{
			$buyer_id_cond="";
		}
	}

	if($lccompany_id==0) $pocompany_cond=""; else $pocompany_cond="and a.company_id='$lccompany_id'";
	if($lccompany_id==0) $pocompany_cond_po_lc=""; else $pocompany_cond_po_lc="and c.po_company_id='$lccompany_id' and c.within_group=1";
	if($company_name==0) $company_cond=""; else $company_cond="and a.company_id='$company_name'";

	if($within_group>0)
	{
		$withinGroupCond = "and e.within_group=$within_group";
		$wg_cond    = "and c.within_group=$within_group";
		$wg_cond_2    = "and e.within_group=$within_group";
	}

	if($txt_date_from != "" && $txt_date_to != "")
	{
		$bill_date_cond = " and a.bill_date between '$txt_date_from' and '$txt_date_to' ";
	}
	if($hdn_fso_id!="")
	{
		$fso_id_cond= "and b.order_id in('$hdn_fso_id') and b.order_id in('$hdn_fso_id')";
	}else {
		$fso_id_cond = "";
	}

	if($txt_fso_no!="")
	{
		$fso_no_cond= "and e.job_no in('$txt_fso_no')";
	}else{
		$fso_no_cond="";
	}
	$booking_type_arr = array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");


	if($db_type==0)
	{
		$bill_id_str="group_concat(a.id) as bill_id";
		
	}
	else if($db_type==2)
	{
		$bill_id_str="listagg(a.id,',') within group (order by a.id) as bill_id ";
	}

   $mainQuery ="select a.id as mst_id,a.bill_date,a.upcharge,a.discount, b.order_id,b.id,b.delivery_id,b.delivery_dtls_id,b.remarks as detailsremarks,b.delivery_date,b.delivery_qty,b.rate,b.amount,b.color_id,b.febric_description_id,b.body_part_id,b.uom from subcon_inbound_bill_mst a,subcon_inbound_bill_dtls b,fabric_sales_order_mst c  where a.id=b.mst_id and b.order_id=c.id and b.status_active=1 and b.is_sales=1 and a.process_id=16 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $pocompany_cond_po_lc $buyer_id_cond_2 $wg_cond $company_cond $fso_id_cond $bill_date_cond order by b.order_id,b.delivery_id,b.delivery_dtls_id";
	

	$mainQueryResult = sql_select($mainQuery);
	$fso_nos="";$fso_nos_arr=array();
	foreach ($mainQueryResult as $row)
	{
		$fso_nos_arr[]=$row[csf("order_id")];
		$fso_nos.=$row[csf("order_id")].",";
		$fsoNoCount[$row[csf("order_id")]]++;// =$row[csf("fso_no")];
		$challan_ids .= "'".$row[csf('delivery_id')]."',";
		$fso_wise_order_qnty_arr[$row[csf('order_id')]]+=$row[csf('amount')];

		if($fsoOrder_idArr[$row[csf('mst_id')]]=="")
		{
			$fsoOrder_idArr[$row[csf('mst_id')]] =$row[csf('mst_id')];
			$already_billed_charge[$row[csf("order_id")]]['upcharge']+=$row[csf("upcharge")]; 
			$already_billed_charge[$row[csf("order_id")]]['discount']+=$row[csf("discount")];
		}
	}
	if(empty($mainQueryResult))
	{
		echo "<span style='color:red; font-weight:bold; font-size:14px;'><center>No Data Found</center></span>";
		exit();
	}
	$all_challan_id = chop($challan_ids,",");
	$fso_nos = chop($fso_nos,",");

	$dtls_data_qry = "select a.id delivery_id,a.issue_number challan_no,a.issue_date delevery_date,b.id dtls_id,b.prod_id product_id,b.batch_id,b.order_id,b.body_part_id bodypart_id,a.location_id,b.uom,b.fabric_shade,sum(b.issue_qnty) delivery_qty,b.no_of_roll roll_no,b.width_type,b.order_id,b.trans_id,c.batch_no,c.extention_no,c.color_id,d.detarmination_id determination_id,d.gsm,d.dia_width dia,e.job_no as fso_no,e.id as fso_id,e.sales_booking_no as booking_no,e.company_id,e.po_company_id,e.po_buyer,e.buyer_id,e.style_ref_no, e.season,e.within_group,e.booking_entry_form,f.order_rate,f.order_amount  
	from inv_issue_master a,inv_transaction f,inv_finish_fabric_issue_dtls b,pro_batch_create_mst c,product_details_master d,fabric_sales_order_mst e  
	where a.company_id=$company_name and a.entry_form=224 and a.id=f.mst_id and f.id=b.trans_id and b.batch_id=c.id and b.prod_id=d.id and b.order_id=TO_CHAR(e.id) and a.status_active='1' and a.is_deleted='0' and e.id in($fso_nos) $wg_cond_2 $challan_cond $withinGroupCond $fso_no_cond $buyer_id_cond and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and f.status_active=1 and f.is_deleted=0  group by a.id,a.issue_number,a.issue_date,b.id,b.prod_id,b.batch_id,b.order_id,b.body_part_id,a.location_id,b.uom,b.fabric_shade,b.no_of_roll,b.width_type,b.order_id,b.trans_id,c.batch_no,c.extention_no,c.color_id,d.detarmination_id,d.gsm,d.dia_width,e.job_no,e.id, e.sales_booking_no,e.company_id,e.po_company_id,e.po_buyer,e.buyer_id,e.style_ref_no,e.season,e.within_group,e.booking_entry_form,f.order_rate,f.order_amount order by e.id,a.id,b.id ";
	$dtls_sql=sql_select($dtls_data_qry);
	foreach ($dtls_sql as $row)
	{
		$booking_ids .= "'".$row[csf('booking_no')]."',";
		$color_id_arr[] = $row[csf("color_id")];
		$data_dtls_arr[$row[csf('fso_id')]][$row[csf('delivery_id')]][$row[csf('dtls_id')]]['challan_no'] = $row[csf('challan_no')];
		$data_dtls_arr[$row[csf('fso_id')]][$row[csf('delivery_id')]][$row[csf('dtls_id')]]['delevery_date'] = $row[csf('delevery_date')];
		$data_dtls_arr[$row[csf('fso_id')]][$row[csf('delivery_id')]][$row[csf('dtls_id')]]['style_ref_no'] = $row[csf('style_ref_no')];
		$data_dtls_arr[$row[csf('fso_id')]][$row[csf('delivery_id')]][$row[csf('dtls_id')]]['season'] = $row[csf('season')];
		$data_dtls_arr[$row[csf('fso_id')]][$row[csf('delivery_id')]][$row[csf('dtls_id')]]['booking_no'] = $row[csf('booking_no')];
		$data_dtls_arr[$row[csf('fso_id')]][$row[csf('delivery_id')]][$row[csf('dtls_id')]]['booking_entry_form'] = $row[csf('booking_entry_form')];
		$data_dtls_arr[$row[csf('fso_id')]][$row[csf('delivery_id')]][$row[csf('dtls_id')]]['fso_no'] = $row[csf('fso_no')];
		$data_dtls_arr[$row[csf('fso_id')]][$row[csf('delivery_id')]][$row[csf('dtls_id')]]['batch_no'] = $row[csf('batch_no')];
		$data_dtls_arr[$row[csf('fso_id')]][$row[csf('delivery_id')]][$row[csf('dtls_id')]]['extention_no'] = $row[csf('extention_no')];
		$data_dtls_arr[$row[csf('fso_id')]][$row[csf('delivery_id')]][$row[csf('dtls_id')]]['bodypart_id'] = $row[csf('bodypart_id')];
		$data_dtls_arr[$row[csf('fso_id')]][$row[csf('delivery_id')]][$row[csf('dtls_id')]]['determination_id'] = $row[csf('determination_id')];
		$data_dtls_arr[$row[csf('fso_id')]][$row[csf('delivery_id')]][$row[csf('dtls_id')]]['gsm'] = $row[csf('gsm')];
		$data_dtls_arr[$row[csf('fso_id')]][$row[csf('delivery_id')]][$row[csf('dtls_id')]]['dia'] = $row[csf('dia')];
		$data_dtls_arr[$row[csf('fso_id')]][$row[csf('delivery_id')]][$row[csf('dtls_id')]]['color_id'] = $row[csf('color_id')];
		$data_dtls_arr[$row[csf('fso_id')]][$row[csf('delivery_id')]][$row[csf('dtls_id')]]['fabric_shade'] = $row[csf('fabric_shade')];
		$data_dtls_arr[$row[csf('fso_id')]][$row[csf('delivery_id')]][$row[csf('dtls_id')]]['uom'] = $row[csf('uom')];
		$data_dtls_arr[$row[csf('fso_id')]][$row[csf('delivery_id')]][$row[csf('dtls_id')]]['po_buyer'] = $row[csf('po_buyer')];
		$data_dtls_arr[$row[csf('fso_id')]][$row[csf('delivery_id')]][$row[csf('dtls_id')]]['buyer_id'] = $row[csf('buyer_id')];
		$data_dtls_arr[$row[csf('fso_id')]][$row[csf('delivery_id')]][$row[csf('dtls_id')]]['po_company_id'] = $row[csf('po_company_id')];
		$data_dtls_arr[$row[csf('fso_id')]][$row[csf('delivery_id')]][$row[csf('dtls_id')]]['company_id'] = $row[csf('company_id')];
		$data_dtls_arr[$row[csf('fso_id')]][$row[csf('delivery_id')]][$row[csf('dtls_id')]]['within_group'] = $row[csf('within_group')];
		$data_dtls_arr[$row[csf('fso_id')]][$row[csf('delivery_id')]][$row[csf('dtls_id')]]['roll_no'] = $row[csf('roll_no')];
	}


	$all_booking_id = chop($booking_ids,",");
	if($all_challan_id!="")
	{
		$all_challan_idsArr=array_unique(explode(",",$all_challan_id));
		if($db_type==2 && count($all_challan_idsArr)>999)
		{
			$challan_cond=" and (";
			$all_challan_idsArr=array_chunk($all_challan_idsArr,999);
			foreach($all_challan_idsArr as $challan_id)
			{
				$challanids=implode(",",$challan_id);
				$challan_cond.="a.id in($challanids) or ";
			}

			$challan_cond=chop($challan_cond,'or ');
			$challan_cond.=")";
		}
		else
		{
			$challan_cond=" and a.id in (".implode(",",$all_challan_idsArr).")";
		}
	}




	$sql_booking_comp=sql_select("select booking_no,company_id,short_booking_type from wo_booking_mst where booking_no in($all_booking_id) and status_active=1 and is_deleted=0");
	foreach ($sql_booking_comp as $row)
	{
		$bookin_com_arr[$row[csf('booking_no')]]['booking_company'] = $row[csf('company_id')];
		$bookin_com_arr[$row[csf('booking_no')]]['short_booking_type'] = $row[csf('short_booking_type')];
	}
	

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
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

	$color_arr=array();
	if(!empty($color_id_arr)){
		$color_arr=return_library_array( "select id, color_name from lib_color where id in(".implode(",",$color_id_arr).") and status_active=1 and is_deleted=0",'id','color_name');
	}
	?>
	
	<?
	ob_start();
	?>
	<style type="text/css">
	.word_wrap_break{
		word-wrap: break-word;
		word-break: break-all;
	}
</style>

<fieldset style="width:3010px;">
	<table width="3110" cellspacing="0" cellpadding="0" border="0" rules="all" >
		<tr class="form_caption">
			<td colspan="28" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
		</tr>
		<tr class="form_caption">
			<td colspan="28" align="center"><? echo $company_library[$company_name]; ?></td>
		</tr>
	</table>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="3030" class="rpt_table" >
		<thead>
			
				<tr>
					<th width="30">SL</th>
					<th width="120">Challan No</th>
					<th width="80">Delivery Date</th>
					<th width="80">Bill Date</th>
					<th width="120">LC Company</th>
					<th width="120">Buyer</th>
					<th width="100">Style Ref</th>
					<th width="100">Season</th>
					<th width="120">Booking No</th>
					<th width="120">Booking Type</th>
					


					<th width="100">Short Booking Type</th>
					<th width="150">FSO No</th>
					<th width="120">Batch No</th>
					<th width="80">Ext. No</th>
					<th width="80">Body Part</th>
					<th width="200">Fabric Description</th>
					<th width="80">Actual GSM</th>
					<th width="80">Actual Dia</th>
					<th width="120">Fabric Color</th>
					<th width="80">Fabric Shade</th>
					

					<th width="80">Delivery Qty (Kg)</th>
					<th width="80">Delivery Qty (Yds)</th>
					<th width="80">Delivery Qty (Mtr)</th>
					<th width="80">Rate ($)</th>
					<th width="80">Amount ($)</th>
					<th width="80">Upcharge ($)</th>
					<th width="80">Discount ($)</th>
					<th width="80">Net Amount ($)</th>
					<th width="80">No Of Roll</th>
					<th>Remarks</th>
					</tr>
				</thead>
			</table>
			<div style="width:3050px; overflow-y:scroll; max-height:350px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="3030" class="rpt_table" id="tbl_list_search">
					<?
					$i=1;
					foreach ($mainQueryResult as $row)
					{
						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						if($data_dtls_arr[$row[csf('order_id')]][$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['within_group']==1)
						{
							$partyName = $data_dtls_arr[$row[csf('order_id')]][$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['po_company_id'];
							$buyerName = $buyer_arr[$data_dtls_arr[$row[csf('order_id')]][$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['po_buyer']];

						}else{
							$partyName = $data_dtls_arr[$row[csf('order_id')]][$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['company_id'];
							$buyerName = $buyer_arr[$data_dtls_arr[$row[csf('order_id')]][$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['buyer_id']];
						}
						$delivery_qty = number_format($row[csf('delivery_qty')],2,".","");
						$order_rate   = number_format($row[csf('rate')],2,".","");
						$order_amount = number_format($row[csf('amount')],2,".","");
						?>
						
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30" ><? echo $i; ?></td>
							<td width="120" class="word_wrap_break"  align="center"><p><? echo $data_dtls_arr[$row[csf('order_id')]][$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['challan_no'];?></p></td>
							<td width="80" class="word_wrap_break"  align="center"><p><? echo change_date_format($data_dtls_arr[$row[csf('order_id')]][$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['delevery_date']);?></p></td>
							<td width="80" class="word_wrap_break"  align="center"><p><? echo change_date_format($row[csf('bill_date')]);?></p></td>
							<td width="120" class="word_wrap_break"><p><? echo $company_arr[$bookin_com_arr[$data_dtls_arr[$row[csf('order_id')]][$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['booking_no']]['booking_company']];?></p></td>
							<td width="120" class="word_wrap_break"><p><? echo $buyerName;?></p></td>
							<td width="100" class="word_wrap_break"><p><? echo $data_dtls_arr[$row[csf('order_id')]][$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['style_ref_no'];?></p></td>
							<td width="100" class="word_wrap_break"><p><? echo $data_dtls_arr[$row[csf('order_id')]][$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['season'];?></p></td>
							<td width="120" class="word_wrap_break"  align="center"><p><? echo $data_dtls_arr[$row[csf('order_id')]][$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['booking_no'];?></p></td>
							<td width="120" class="word_wrap_break" align="center"><p><? echo $booking_type_arr[$data_dtls_arr[$row[csf('order_id')]][$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['booking_entry_form']];?></p></td>
							<td width="100" class="word_wrap_break" align="center"><p>
								<? 
								$bookingEntryForm=$data_dtls_arr[$row[csf('order_id')]][$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['booking_entry_form'];
								if($bookingEntryForm==88){echo $short_booking_type[$bookin_com_arr[$data_dtls_arr[$row[csf('order_id')]][$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['booking_no']]['short_booking_type']];} ?></p></td>
							<td width="150" class="word_wrap_break" align="center"><p><? echo $data_dtls_arr[$row[csf('order_id')]][$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['fso_no'];?></td>
							<td width="120" class="word_wrap_break" align="center"><p><? echo $data_dtls_arr[$row[csf('order_id')]][$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['batch_no'];?></p></td>
							<td width="80" class="word_wrap_break" align="center"><p><? echo $data_dtls_arr[$row[csf('order_id')]][$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['extention_no'];?></p></td>
							<td width="80" class="word_wrap_break" align="center"><p><? echo $body_part[$data_dtls_arr[$row[csf('order_id')]][$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['bodypart_id']];?></p></td>
							<td width="200" class="word_wrap_break" align="center"><p><? echo $composition_arr[$data_dtls_arr[$row[csf('order_id')]][$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['determination_id']];?></p></td>
							<td width="80" class="word_wrap_break" align="center"><p><? echo $data_dtls_arr[$row[csf('order_id')]][$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['gsm'] ; ?></p></td>
							<td width="80" class="word_wrap_break" align="center"><p><? echo $data_dtls_arr[$row[csf('order_id')]][$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['dia'];?></p></td>
							<td width="120" class="word_wrap_break" align="center"><p><? echo $color_arr[$row[csf('color_id')]];?></p></td>



							<td width="80" class="word_wrap_break" align="center"><p><?php echo $fabric_shade[$data_dtls_arr[$row[csf('order_id')]][$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['fabric_shade']];?></p></td>
							<td width="80" class="word_wrap_break" align="right"><p><? if($row[csf('uom')]==12){echo number_format($row[csf('delivery_qty')],2,".",""); $total_delv_qnty_kg+=$row[csf('delivery_qty')];} ?></p></td>
							<td width="80" class="word_wrap_break" align="right"><p><? if($row[csf('uom')]==27){echo number_format($row[csf('delivery_qty')],2,".",""); $total_delv_qnty_yds+=$row[csf('delivery_qty')];} ?></p></td>
							<td width="80" class="word_wrap_break" align="right"><p><? if($row[csf('uom')]==23){echo number_format($row[csf('delivery_qty')],2,".",""); $total_delv_qnty_mtr+=$row[csf('delivery_qty')];} ?></p></td>
							<td width="80"  class="word_wrap_break" align="right"><p><? echo number_format($row[csf('rate')],2,".","");?></p></td>
							<td width="80" class="word_wrap_break" align="right"><p><?  echo number_format($row[csf('amount')],2,".","");?></p></td>
							
							<?
							if($fsoNoArr[$row[csf('order_id')]]=="")
							{
								$fsoNoArr[$row[csf('order_id')]] =$row[csf('order_id')];
								$upcharge_total=$already_billed_charge[$row[csf('order_id')]]['upcharge']; 
								$discount_total=$already_billed_charge[$row[csf('order_id')]]['discount'];
								$net_amount=($fso_wise_order_qnty_arr[$row[csf('order_id')]]+$upcharge_total)-$discount_total;
								?>

							<td valign="middle" rowspan="<? echo $fsoNoCount[$row[csf('order_id')]]; ?>" width="80" class="word_wrap_break" align="right"><p><? echo number_format($upcharge_total,2,".",""); $total_upcharge+=$upcharge_total; ?></p></td>
							<td valign="middle" rowspan="<? echo $fsoNoCount[$row[csf('order_id')]]; ?>" width="80" class="word_wrap_break" align="right"><p><? echo number_format($discount_total,2,".",""); $total_discount+=$discount_total; ?></p></td>
							<td valign="middle" rowspan="<? echo $fsoNoCount[$row[csf('order_id')]]; ?>" width="80" class="word_wrap_break" align="right"><p><? echo number_format($net_amount,2,".",""); $total_net_amount+=$net_amount; ?></p></td>
							<?
							}	
							?>
							<td align="center" width="80" class="word_wrap_break" align="right"><p><?php echo $data_dtls_arr[$row[csf('order_id')]][$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['roll_no'];?></p></td>
							<td align="left" class="word_wrap_break" align="right" title=""><p><?php echo $row[csf('detailsremarks')];?></p></td>
						</tr>
						<?
						$i++;
						$total_order_rate+=$order_rate;
						$total_order_amount+=$order_amount; 
						$total_roll_no+=$data_dtls_arr[$row[csf('order_id')]][$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['roll_no'];
					}
						?>
						<tr style="background-color:grey;">
							<td colspan="20" align="right"><b>Total:</b></td>
							<td align="right"><b><? echo number_format($total_delv_qnty_kg,2,".",""); ?></b></td>
							<td align="right"><b><? echo number_format($total_delv_qnty_yds,2,".",""); ?></b></td>
							<td align="right"><b><? echo number_format($total_delv_qnty_mtr,2,".",""); ?></b></td>
							<td align="right"><b><? echo number_format($total_order_rate,2,".",""); ?></b></td>
							<td align="right"><b><? echo number_format($total_order_amount,2,".",""); ?></b></td>
							<td align="right"><b><? echo number_format($total_upcharge,2,".",""); ?></b></td>
							<td align="right"><b><? echo number_format($total_discount,2,".",""); ?></b></td>
							<td align="right"><b><? echo number_format($total_net_amount,2,".",""); ?></b></td>
							<td align="right"><b><? echo number_format($total_roll_no,2,".",""); ?></b></td>
							<td align="right"><b></b></td>
						</tr>
					</table>
				</div>
				<?
				$html = ob_get_contents();
				ob_clean();
				foreach (glob("*.xls") as $filename) {
					@unlink($filename);
				}
				$name=time();
				$filename=$user_id."_".$name.".xls";
				$create_new_doc = fopen($filename, 'w');
				$is_created = fwrite($create_new_doc, $html);
				echo "$html####$filename";
				exit();
}

?>
<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_library=array();
$sql=sql_select("select id, company_short_name, company_name from lib_company");
foreach($sql as $row)
{
	$company_library[$row[csf('id')]]['short']=$row[csf('company_short_name')];
	$company_library[$row[csf('id')]]['full']=$row[csf('company_name')];
}

$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count"  );
$brand_details=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name"  );
//--------------------------------------------------------------------------------------------------------------------
if($action=="booking_No_popup")
{
  	echo load_html_head_contents("Job Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
?>
	<script>
	
		function js_set_value(job_id,job_no,booking_no)
		{
			
			document.getElementById('hidden_job_id').value=job_id;
			document.getElementById('hidden_job_no').value=job_no;
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
                	<th>Company</th>
                    <th>Search By</th>
                    <th>Search</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="hidden_job_id" id="hidden_job_id" value="">
                         <input type="hidden" name="hidden_job_no" id="hidden_job_no" value="">
                          <input type="hidden" name="hidden_booking_no" id="hidden_booking_no" value="">
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">	
                       <?
                         echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $cbo_company_id, "" );
                    	?>
                    </td>   
                    <td align="center">	
                        <?
                            $search_by_arr=array(1=>"Sales Order No",2=>"Sales / Booking No",3=>"Style Ref.");
                            echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", 2,$dd,0 );
                        ?>
                    </td>                 
                    <td align="center">				
                        <input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>, 'create_booking_search_list_view', 'search_div', 'fabric_sales_order_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
            </table>
            <div id="search_div" style="margin-top:10px"></div>   
        </form>
    </fieldset>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_booking_search_list_view")
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
if($action=="jobNo_popup")
{
  	echo load_html_head_contents("Job Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
?>
	<script>
	
		function js_set_value(job_id,job_no,booking_no)
		{
			
			document.getElementById('hidden_job_id').value=job_id;
			document.getElementById('hidden_job_no').value=job_no;
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
                        <input type="hidden" name="hidden_job_id" id="hidden_job_id" value="">
                         <input type="hidden" name="hidden_job_no" id="hidden_job_no" value="">
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('cbo_within_group').value, 'create_job_search_list_view', 'search_div', 'fabric_sales_order_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
            </table>
            <div id="search_div" style="margin-top:10px"></div>   
        </form>
    </fieldset>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_job_search_list_view")
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

$cbo_company_name=str_replace("'","",$cbo_company_name);
$companyArr=return_library_array( "select id,company_name from lib_company",'id','company_name');
$addressArr=return_library_array( "select id,city from lib_company where id=$cbo_company_name",'id','city');
$buyerArr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
$departmentArr=return_library_array( "select id,department_name from lib_department",'id','department_name');
$dealing_marArr=return_library_array( "select id,team_member_name from lib_mkt_team_member_info",'id','team_member_name');
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
$supplier_arr=return_library_array( "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$cbo_company_name' and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id", "supplier_name"  );
$yarnCount_arr=return_library_array( "select id, yarn_count from lib_yarn_count order by yarn_count",'id','yarn_count');

	$season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0",'id','season_name');
	
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$txt_job_hidden_id=str_replace("'","",$txt_job_hidden_id);
	$txt_job_no=str_replace("'","",$txt_job_no);
	
	
if($txt_booking_no!='')
		{
			$booking_no_cond=" and a.booking_no='$txt_booking_no'";
		}
		else
		{
			$booking_no_cond=" ";
		}
		
if($txt_job_hidden_id!='')
		{
			$job_field_cond=" and d.id='$txt_job_hidden_id'";
		}
		else
		{
			$job_field_cond=" ";
		}

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id order by b.id asc";
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
	



if($db_type==0){$group_concat="group_concat(c.po_number) as po_number,";}
else{$group_concat="listagg(c.po_number,',') within group (order by c.po_number) as po_number,";}


$sql= "SELECT a.attention,b.job_no as wo_job_no,b.order_repeat_no,d.id as sales_id,a.fabric_composition,a.booking_no, d.booking_date, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id,b.style_description,b.dealing_marchant,min(c.pub_shipment_date) as shipment_date,max(c.pub_shipment_date) as max_shipment_date,min(c.po_received_date) as po_received_date,sum(c.po_quantity*b.total_set_qnty) as po_quantity, d.job_no,b.buyer_name,b.gmts_item_id, b.style_ref_no,b.team_leader, b.dealing_marchant, b.season_matrix as season,$group_concat b.product_dept,b.style_owner,d.currency_id,d.remarks,d.delivery_date FROM wo_booking_mst a, wo_po_details_master b,wo_po_break_down c,fabric_sales_order_mst d WHERE a.id=d.booking_id and a.job_no=b.job_no and b.job_no=c.job_no_mst and a.pay_mode=5 and a.fabric_source in(1,2) and a.supplier_id=$cbo_company_name and a.status_active =1 and a.is_deleted =0  $job_field_cond $booking_no_cond and a.item_category=2  group by a.booking_no, d.booking_date, a.company_id, a.delivery_date, a.currency_id, a.po_break_down_id,b.style_description, d.job_no,b.buyer_name,b.gmts_item_id, b.style_ref_no, b.dealing_marchant, b.season_matrix, b.product_dept,d.currency_id,b.team_leader,d.remarks,d.delivery_date,d.id,b.style_owner,a.fabric_composition,b.job_no,b.order_repeat_no,a.attention";

//echo $sql;

$mst_data=sql_select($sql);
$mst_data=array_change_key_case($mst_data[0], CASE_LOWER);
extract($mst_data);
ob_start();
$image_locationArr=return_library_array( "select id,image_location from common_photo_library where form_name ='fabric_sales_order_entry' and master_tble_id='$txt_job_hidden_id' and file_type=1",'id','image_location');
if(count($image_locationArr)==0){
$image_locationArr=return_library_array( "select id,image_location from common_photo_library where form_name ='knit_order_entry' and master_tble_id='$wo_job_no' and file_type=1",'id','image_location');
}


$lead_time=datediff("d",$po_received_date,date('d-M-Y',time()));

$gmts_item_id_arr=explode(',',$gmts_item_id);
foreach($gmts_item_id_arr as $item_id){
	if($item_string==''){$item_string=$garments_item[$item_id];}	
	else{$item_string.=','.$garments_item[$item_id];}	
}
 $max_shipment_date.'wew';
 
// ob_start();
?>
<div style="  overflow-y:scroll;" id="scroll_body">
 <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" >
	<tr>
        <td colspan="5" align="center"><strong style="font-size:20px;"><? echo $companyArr[$cbo_company_name];?></strong></td>
    </tr>
	<tr>
        <td colspan="5" align="center"><strong style="font-size:16px;" ><? echo $addressArr[$cbo_company_name];?></strong></td>
    </tr>
	<tr>
        <td colspan="5" align="center"><strong style="font-size:16px;" >Fabric Sales Order Report</strong></td>
    </tr>
</table>

<table width="100%" border="1" rules="all" class="rpt_table" style="table-layout: fixed;">
	<tr>
    	<td width="135"><strong>Buyer/Agent Name</strong></td><td><? echo $buyerArr[$buyer_name];?></td>
    	<td width="135"><strong>Dept.</strong></td><td><? echo $departmentArr[$product_dept];?></td>
    	<td width="135"><strong>Garments Item</strong></td><td><? echo $item_string;?></td>
        <td><strong>Sales Order No: <? echo $txt_job_no;?></strong></td>
    </tr>
	<tr>
    	<td><strong>Style Ref.</strong></td><td><? echo $style_ref_no;?></td>
    	<td><strong>Season</strong></td><td><? echo $season_arr[$season];?></td>
    	<td><strong>Order Qnty</strong></td><td><? echo $po_quantity;?></td>
        <td rowspan="9" valign="top" width="205"><? foreach($image_locationArr as $path){?><img src="../../<? echo $path;?>" height="100" width="100"><? } ?></td>
    </tr>
	<tr>
    	<td><strong>Style  Des.</strong></td><td><? echo $style_description;?></td>
    	<td><strong>Lead Time</strong></td><td><? echo $lead_time; ?></td>
    	<td><strong>Job No</strong></td><td><? echo $wo_job_no;?></td>
    </tr>
	<tr>
    	<td><strong>Order No</strong></td><td  style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap; font-weight:bold;"colspan="2"><? echo $po_number;?></td>
    	<td><strong>Booking No</strong></td><td><? echo $booking_no;?></td>
    </tr>
	<tr>
    	<td><strong>Repeat No</strong></td><td><? echo $order_repeat_no;?></td>
    	<td><strong>Shipment Date</strong></td><td><? echo 'First:'.change_date_format($shipment_date).','.'Last:'.change_date_format($max_shipment_date);?></td>
    	<td><strong>Booking Date</strong></td><td><? echo change_date_format($booking_date);?></td>
    </tr>
	<tr>
    	<td><strong>Po Received Date</strong></td><td><? echo change_date_format($po_received_date);?></td>
    	<td><strong>WO Prepared After</strong></td><td><? echo $row[csf('')];?></td>
    	<td><strong>Dealing Merchant</strong></td><td><? echo $dealing_marArr[$dealing_marchant];?></td>
    </tr>
	<tr>
    	<td><strong>Currency</strong></td><td><? echo $currency[$currency_id];?></td>
    	<td><strong>Quality Label</strong></td><td><? echo $row[csf('')];?></td>
    	<td><strong>Style Owner</strong></td><td><? echo $companyArr[$style_owner];?></td>
    </tr>
	<tr>
    	<td><strong>Attention</strong></td><td colspan="3"><? echo $attention;?></td>
    	<td><strong>Delivery Date</strong></td><td><? echo change_date_format($delivery_date);?></td>
    </tr>
	<tr>
    	<td><strong>Fabric Composition</strong></td><td colspan="3"><? echo $fabric_composition;?></td>
    	<td><strong>Revised No</strong></td><td><? 
			$nameArray_approved=sql_select( "select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='".$txt_booking_no."' and b.entry_form=7");
				echo $nameArray_approved[0][csf('approved_no')]-1;
		;?></td>
    </tr>
	<tr>
    	<td><strong>Remarks</strong></td><td colspan="5"><? echo $remarks;?></td>
    </tr>
</table>

<br>
<? 





/*$dtls_sql="select e.item_number_id,avg(b.requirment) as requirment, avg(b.cons) as cons,b.remarks,a.body_part_id, a.color_type_id, a.determination_id, a.gsm_weight, a.dia, a.width_dia_type, a.color_id, a.finish_qty, a.process_loss, a.grey_qty from fabric_sales_order_dtls a,wo_pre_cos_fab_co_avg_con_dtls b,wo_booking_dtls c,fabric_sales_order_mst d,wo_pre_cost_fabric_cost_dtls e where a.mst_id=$sales_id and a.status_active=1 and a.is_deleted=0
and e.id=b.pre_cost_fabric_cost_dtls_id
and e.body_part_id=a.body_part_id
and e.color_type_id=a.color_type_id
and a.mst_id=d.id
and d.sales_booking_no=c.booking_no
and b.po_break_down_id=c.po_break_down_id 
and b.color_size_table_id=c.color_size_table_id 
and b.pre_cost_fabric_cost_dtls_id=c.pre_cost_fabric_cost_dtls_id 
and c.booking_no ='$booking_no'
group by a.body_part_id, a.color_type_id, a.determination_id, a.gsm_weight, a.dia, a.width_dia_type, a.color_id, a.finish_qty, a.process_loss, a.grey_qty,e.item_number_id,b.remarks
 order by a.body_part_id";*/

$dtls_sql="select e.item_number_id,avg(b.requirment) as requirment, avg(b.cons) as cons,b.remarks,a.body_part_id, a.color_type_id,e.construction,e.composition, a.gsm_weight, a.dia, a.color_id, a.finish_qty, a.process_loss, a.grey_qty from 
	fabric_sales_order_dtls a,
	wo_pre_cos_fab_co_avg_con_dtls b,
	wo_booking_dtls c,
	fabric_sales_order_mst d,
	wo_pre_cost_fabric_cost_dtls e 

where a.job_no_mst='$txt_job_no' and a.status_active=1 and a.is_deleted=0
and e.id=b.pre_cost_fabric_cost_dtls_id
and e.body_part_id=a.body_part_id
and e.color_type_id=a.color_type_id
and a.color_id=b.color_number_id
and a.mst_id=d.id
and d.sales_booking_no=c.booking_no
and b.po_break_down_id=c.po_break_down_id 
and b.color_size_table_id=c.color_size_table_id 
and b.pre_cost_fabric_cost_dtls_id=c.pre_cost_fabric_cost_dtls_id 
and c.booking_no ='$booking_no'
group by e.item_number_id,a.body_part_id,a.color_type_id, e.construction,e.composition, a.gsm_weight, a.dia, a.finish_qty, a.process_loss, a.grey_qty,a.color_id,b.remarks
 order by a.body_part_id";


$dtls_sql_data=sql_select($dtls_sql);
foreach($dtls_sql_data as $rows){
	//$key=$rows[csf('body_part_id')].'_'.$rows[csf('color_type_id')].'_'.$composition_arr[$rows[csf('determination_id')]].'_'.$rows[csf('gsm_weight')].'_'.$rows[csf('dia')].'_'.$rows[csf('item_number_id')];
	$key=$rows[csf('body_part_id')].'_'.$rows[csf('color_type_id')].'_'.$rows[csf('construction')].','.$rows[csf('composition')].'_'.$rows[csf('gsm_weight')].'_'.$rows[csf('dia')].'_'.$rows[csf('item_number_id')];
	
	//$color_type_id_arr[$key]=$rows[csf('color_type_id')];
	$colorArr[$rows[csf('color_id')]]=$rows[csf('color_id')];
	
	$finish_qty_arr[$key][$rows[csf('color_id')]]+=$rows[csf('finish_qty')];
	$grey_qty_arr[$key][$rows[csf('color_id')]]+=$rows[csf('grey_qty')];
	$process_loss_arr[$key][$rows[csf('color_id')]]+=$rows[csf('process_loss')];
	
	$tot_finish_qty_arr+=$rows[csf('finish_qty')];
	$tot_grey_qty_arr+=$rows[csf('grey_qty')];
	//$tot_consumption_arr+=$rows[csf('cons')];
	
	
	
	//list($construction,$compositions)=explode(',',$composition_arr[$rows[csf('determination_id')]]);
	$body_part_data[$key]=$rows[csf('body_part_id')];
	$color_type_id_arr[$key]=$rows[csf('color_type_id')];
	/*$constructions_arr[$key]=$construction;
	$compositions_arr[$key]=$compositions;*/
	
	$constructions_arr[$key]=$rows[csf('construction')];
	$compositions_arr[$key]=$rows[csf('composition')];
	$gsm_weight_arr[$key]=$rows[csf('gsm_weight')];
	$dia_arr[$key]=$rows[csf('dia')];
	$remarks_arr[$key]=$rows[csf('remarks')];
	
	$item_number_id_arr[$key]=$rows[csf('item_number_id')];
	
	
	
}





	$costing_per="";
	$costing_per_qnty=0;
	$costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$wo_job_no'");
	if($costing_per_id==1)
	{
		$costing_per="1 Dzn";
		$costing_per_qnty=12;
	}
	elseif($costing_per_id==2)
	{
		$costing_per="1 Pcs";
		$costing_per_qnty=1;
	}
	elseif($costing_per_id==3)
	{
		$costing_per="2 Dzn";
		$costing_per_qnty=24;
	}
	elseif($costing_per_id==4)
	{
		$costing_per="3 Dzn";
		$costing_per_qnty=36;
	}
	elseif($costing_per_id==5)
	{
		$costing_per="4 Dzn";
		$costing_per_qnty=48;
	}


$gmt_color_data=sql_select("select gmts_color_id,contrast_color_id FROM wo_pre_cos_fab_co_color_dtls WHERE job_no ='$wo_job_no'");
		  foreach( $gmt_color_data as $gmt_color_row)
		  {
			$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]][$gmt_color_row[csf("gmts_color_id")]]=$color_library[$gmt_color_row[csf("gmts_color_id")]];
		  }



?>
<table width="100%" border="1" rules="all" class="rpt_table">
    <tr>
        <td colspan="3"><strong>Item Name</strong></td>
        <? foreach($item_number_id_arr as $val){echo '<td colspan="3" align="center">'.$garments_item[$val].'</td>';}?>
        <td align="center"><strong>Total Finish</strong></td>
        <td align="center"><strong>Total Grey</strong></td>
    </tr>		
    <tr>
        <td colspan="3"><strong>Body Part</strong></td>
        <? foreach($body_part_data as $val){echo '<td colspan="3" align="center">'.$body_part[$val].'</td>';}?>
        <td rowspan="9"><? echo $row[csf('')];?></td>
        <td rowspan="9"><? echo $row[csf('')];?></td>
    </tr>		
    <tr>
        <td colspan="3"><strong>Color Type</strong></td>
        <? foreach($color_type_id_arr as $val){echo '<td colspan="3" align="center">'.$color_type[$val].'</td>';}?>
    </tr>		
    <tr>
        <td colspan="3"><strong>Fabric Construction	</strong></td>
        <? foreach($constructions_arr as $val){echo '<td colspan="3" align="center">'.$val.'</td>';}?>
    </tr>		
    <tr>
        <td colspan="3"><strong>Yarn Composition</strong></td>
        <? foreach($compositions_arr as $val){echo '<td colspan="3" align="center">'.$val.'</td>';}?>
    </tr>		
    <tr>
        <td colspan="3"><strong>GSM	</strong></td>
        <? foreach($dia_arr as $val){echo '<td colspan="3" align="center">'.$val.'</td>';}?>
    </tr>		
    <tr>
        <td colspan="3"><strong>Dia/Width (Inch)</strong></td>
        <? foreach($gsm_weight_arr as $val){echo '<td colspan="3" align="center">'.$val.'</td>';}?>
    </tr>		
    <tr>
        <td colspan="3"><strong>Consumption For  <? echo $costing_per;?></strong></td>
        <? foreach($body_part_data as $key_ids=>$val){
			//$tot_consumption_arr+=$val;echo '<td colspan="3">'.number_format($val,2).'</td>';
			
list($body_part_id,$color_type_id,$fabric_desc,$gsm_weight,$dia,$item_number_id)=explode('_',$key_ids);
list($constrac_str,$compo_str)=explode(',',$fabric_desc);
 $sql="select avg(b.cons) as cons from wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d where a.job_no=b.job_no and
	a.id=b.pre_cost_fabric_cost_dtls_id and
	c.job_no_mst=a.job_no and 
	c.id=b.color_size_table_id and
	b.po_break_down_id=d.po_break_down_id and 
	b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and 
	d.booking_no ='$txt_booking_no' and 
	a.uom=12 and 
	d.status_active=1 and 
	d.is_deleted=0 and
	b.cons>0 and a.item_number_id='$item_number_id' and a.body_part_id='$body_part_id' and a.color_type_id='$color_type_id' and a.construction='$constrac_str' and a.composition='".trim($compo_str)."'  and a.gsm_weight='$gsm_weight' and b.dia_width='$dia'";
	
	//echo $sql.'**';
$con_sql_data=sql_select($sql);
	$conQTY=$con_sql_data[0][csf(cons)];
$tot_consumption_arr+=$conQTY;echo '<td colspan="3" align="center">'.number_format($conQTY,2).'</td>';	
			
			}
			?>
    </tr>
    
    <tr>
        <td colspan="3"><strong>Remarks</strong></td>
        <? foreach($remarks_arr as $val){echo '<td colspan="3" align="center">'.$val.'</td>';}?>
    </tr>		
    
    <tr bgcolor="#CCCCCC">
        <td align="center"><strong>Fabric Color</strong></td>
        <td align="center"><strong>Body Color</strong></td>
        <td align="center"><strong>Lab Dip No</strong></td>
       <? 
	    foreach($body_part_data as $val){
			echo '<td align="center"><strong>Finish Fab. Qty</strong></td>';
			echo '<td align="center"><strong>Pro. Loss %</strong></td>';
			echo '<td align="center"><strong>Grey Fab. Qty</strong></td>';
		}
	   ?>
    </tr>
    
  <? foreach($colorArr as $colorVal){
	  $i++;
	  $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
	  ?>
    <tr bgcolor="<? echo $bgcolor; ?>">
        <td align="center"><? echo $color_library[$colorVal];?></td>
        <td align="center"><? echo implode(",",$gmt_color_library[$colorVal]);
;?></td>
        <td align="center"><? echo return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$wo_job_no."' and approval_status=3 and color_name_id=".$colorVal."");?></td>
        <? foreach($body_part_data as $key_id=>$val){
			$color_wise_finish_tot_qty[$colorVal]+=$finish_qty_arr[$key_id][$colorVal];
			$color_wise_grey_tot_qty[$colorVal]+=$grey_qty_arr[$key_id][$colorVal];
			$all_color_finish_tot_qty[$key_id]+=$finish_qty_arr[$key_id][$colorVal];
			$all_color_grey_tot_qty[$key_id]+=$grey_qty_arr[$key_id][$colorVal];
			
				if(is_null($finish_qty_arr[$key_id][$colorVal]) || $finish_qty_arr[$key_id][$colorVal]==0){echo '<td align="right"></td>';}
				else{echo '<td align="right">'.number_format($finish_qty_arr[$key_id][$colorVal],2).'</td>';}
				if(is_null($process_loss_arr[$key_id][$colorVal]) || $process_loss_arr[$key_id][$colorVal]==0){echo '<td align="right"></td>';}
				else{echo '<td align="right">'.number_format($process_loss_arr[$key_id][$colorVal],2).'</td>';}
				if(is_null($grey_qty_arr[$key_id][$colorVal]) || $grey_qty_arr[$key_id][$colorVal]==0){echo '<td align="right"></td>';}
				else{echo '<td align="right">'.number_format($grey_qty_arr[$key_id][$colorVal],2).'</td>';}
			}
		?>
        <td align="right"><strong><? echo number_format($color_wise_finish_tot_qty[$colorVal],2);?></strong></td>
        <td align="right"><strong><? echo number_format($color_wise_grey_tot_qty[$colorVal],2);?></strong></td>
    </tr>
    <?
    }
    ?>
  
    <tr bgcolor="#EEEEEE">
        <td align="center"></td>
        <td align="center">Total</td>
        <td align="center"></td>
        <? foreach($body_part_data as $key_id=>$val){
			$grand_all_color_finish_tot_qty+=$all_color_finish_tot_qty[$key_id];
			$grand_all_color_grey_tot_qty+=$all_color_grey_tot_qty[$key_id];
			echo '<td align="right">'.number_format($all_color_finish_tot_qty[$key_id],2).'</td>';
			echo '<td></td>';
			echo '<td align="right">'.number_format($all_color_grey_tot_qty[$key_id],2).'</td>';
			}
		?>
        <td align="right"><strong><? echo number_format($grand_all_color_finish_tot_qty,2);?></strong></td>
        <td align="right"><strong><? echo number_format($grand_all_color_grey_tot_qty,2);?></strong></td>
    </tr>
    	
    <tr>
        <td align="center"></td>
        <td align="center"><strong>Consumption For <? echo $costing_per;?></strong></td>
        <td align="center"></td>
        <? foreach($body_part_data as $val){
			echo '<td></td>';
			echo '<td></td>';
			echo '<td></td>';
			}
		?>
        <td align="right"><strong><? echo number_format($tot_consumption_arr,2);?></strong></td>
        <td align="right"><strong></strong></td>
    </tr>
    	
</table>

<br>

<?
$yarn_dtls_sql="select yarn_count_id,color_id,composition_id,composition_perc,yarn_type,sum(cons_qty) as cons_qty,supplier_id from fabric_sales_order_yarn_dtls where mst_id=$txt_job_hidden_id and status_active=1 and is_deleted=0 group by yarn_count_id,color_id,composition_id,composition_perc,yarn_type,supplier_id";
$yarn_dtls_sql_data=sql_select($yarn_dtls_sql);
?>
<strong>Yarn Required Summary</strong>
<table border="1" rules="all" class="rpt_table" >
    <tr bgcolor="#CCCCCC">
        <td align="center"><strong>Sl No</strong></td>
        <td align="center"><strong>Count</strong></td>
        <td align="center"><strong>Composition</strong></td>	
        <td align="center"><strong>Color</strong></td>	
        <td align="center"><strong>Type</strong></td>	
        <td align="center"><strong>Req. Qty.</strong></td> 
        <td align="center"><strong>Supplier</strong></td>
    </tr>
 <?
 $i=1;
 foreach($yarn_dtls_sql_data as $rows){
   ?> 
	<tr>
        <td align="center"><? echo $i;?></td>
        <td><? echo $yarnCount_arr[$rows[csf(yarn_count_id)]];?></td>
        <td><? echo $composition[$rows[csf(composition_id)]];?></td>	
        <td><p><? echo $color_library[$rows[csf(color_id)]];?></p></td>	
        <td><p><? echo $yarn_type[$rows[csf(yarn_type)]];?></p></td>	
        <td align="right"><p><? echo $rows[csf(cons_qty)]; $tot_cons_qty+=$rows[csf(cons_qty)];?></p></td> 
        <td><p><? echo $supplier_arr[$rows[csf(supplier_id)]];?></p></td>
    </tr>
<?
$i++;
}

?>   
    <tr>
        <td align="center"><strong></strong></td>
        <td align="center"><strong></strong></td>
        <td align="center"><strong>Total Grey</strong></td>	
        <td align="center"><strong></strong></td>	
        <td align="center"><strong></strong></td>	
        <td align="right"><strong><? echo number_format($tot_cons_qty,2);?></strong></td> 
        <td align="center"><strong></strong></td>
    </tr>
    
    
</table>


<br>
<?

$terms_sql="select terms from wo_booking_terms_condition where booking_no='$booking_no'";
//$terms_sql="select terms from wo_booking_terms_condition where booking_no='$salesOrderNo'";
$terms_data=sql_select($terms_sql);
?>
<strong>Special Instruction</strong>
<table border="1" rules="all" class="rpt_table">
    <tr bgcolor="#CCCCCC">
        <td align="center"><strong>Sl</strong></td>
        <td align="center"><strong>Terms</strong></td>
    </tr>
 <?
 $i=1;
 foreach($terms_data as $rows){
   ?> 
	<tr>
        <td align="center"><? echo $i;?></td>
        <td><? echo $rows[csf(terms)];?></td>
    </tr>
<?
$i++;
}

?>   
</table>
</div>



<?
foreach (glob("$user_id*.xls") as $filename)
	{		
		@unlink($filename);

	}
	$name=$user_id.'_'.time().".xls";
	$create_new_excel = fopen($name, 'w');	
	$report_data=ob_get_contents();
	ob_clean();
	$is_created = fwrite($create_new_excel,$report_data);
	echo $report_data."####".$name;
	exit();

}
?>
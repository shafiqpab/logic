<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id,store_location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];

if ($company_id !='') {
    $company_credential_cond = "and comp.id in($company_id)";
}
if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)"; 
}

// ========== user credential end ==========
if ($action=="load_drop_down_buyer_popup")
{
	//echo $data;
	$data=explode("_",$data);
	$dropdown_type=$data[2];
	//if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	//else $load_function="";
	if($dropdown_type==2)
	{
		$cbo_party_name='cbo_party_name';
		$disabled=0;
		$width=150;
	} 
 
	if($data[1]==1)
	{
		echo create_drop_down( $cbo_party_name, $width, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --",'', "$load_function",$disabled);
	}
	else
	{
		//echo "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name";
		echo create_drop_down( $cbo_party_name, $width, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",'', "",$disabled );
	}	
	exit();	 
} 


if ($action=="load_drop_down_buyer")
{
	//echo $data;
	$data=explode("_",$data);
	$dropdown_type=$data[2];
	//if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	//else $load_function="";
	if($dropdown_type==2)
	{
		$cbo_party_name='cbo_to_buyer_name';
		$disabled=1;
		$width=162;
	} 
	else 
	{
		$cbo_party_name='cbo_from_buyer_name';
		$disabled=1;
		$width=162;
	}

	if($data[1]==1)
	{
		echo create_drop_down( $cbo_party_name, $width, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --",'', "$load_function",$disabled);
	}
	else
	{
		 
		echo create_drop_down( $cbo_party_name, $width, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",'', "",$disabled );
	}	
	exit();	 
}
if ($action=="order_popup")
{
	echo load_html_head_contents("Work Order Popup Info","../../../", 1, 1, $unicode,'','');
	//extract($_REQUEST);
	//echo $cbo_company_id."--".$type."--".$action; die;
	?>
	<script>
		function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_popup(type,within_group)
		{
			var company = $('#cbo_company_name').val();
			var party_name = $('#cbo_party_name').val();
			var location_name = $('#cbo_location_name').val();
			var within_group = $('#cbo_within_group').val();
			load_drop_down( 'trims_production_order_to_order_transfer_entry_controller', company+'_'+within_group+'_'+2, 'load_drop_down_buyer_popup', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0)
			{
				$('#search_by_td').html('System ID');
			}
			else if(val==2)
			{
				$('#search_by_td').html('W/O No');
			}
			else if(val==4)
			{
				$('#search_by_td').html('Buyer Po');
			}
			else if(val==5)
			{
				$('#search_by_td').html('Buyer Style');
			}
		}

          //FOR FIELD LAVEL EXCES
			<?
			if(isset($_SESSION['logic_erp']['data_arr'][485])){
			$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][485] );
			echo "var field_level_data= ". $data_arr . ";\n";
			}
			?>
			window.onload = function(){ 
				set_field_level_access(<?=$company_id;?>);
     
              } 

	</script>
</head>
<body>
<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="940" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
			<thead> 
				<tr>
					<th colspan="9"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",4 ); ?></th>
				</tr>
				<tr>               	 
					<th width="140" class="must_entry_caption">Company Name</th>
					<th width="60">Within Group</th>                           
					<th width="140">Party Name</th>
					<th width="80">Search By</th>
					<th width="100" id="search_by_td">System ID</th>
					<th width="60">Year</th>
					<th width="170">Date Range</th>                            
					<th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
				</tr>           
			</thead>
			<tbody>
				<tr class="general">
					<td><input type="hidden" id="selected_job"> <? $data=explode("_",$data); ?> 
						<? 
						$company_id=$data[0];
						$type=$data[1];
						$cboSection=$data[2];
						$cboSubSection=$data[3];
						$cboItemGroup=$data[4];
						$cbo_uom=$data[5];
						$hid_color_id=$data[6];
						$hid_size_id=$data[7];
						$item_description=$data[8];					
						
						echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value,2);",1); ?>
					</td>
					<td>
						<?php echo create_drop_down( "cbo_within_group", 60, $yes_no,"", 0, "--  --", "", "fnc_load_party_popup(1,this.value,2);",0 ); ?>
					</td>
					<td id="buyer_td">
						<? echo create_drop_down( "cbo_party_name", 150, "","", 1, "-- Select Party --", "", "fnc_load_party_popup(1,this.value,2);" );   	 
						?>
					</td>
					<td>
						<?
							$search_by_arr=array(1=>"System ID",2=>"W/O No",4=>"Buyer Po",5=>"Buyer Style");
							echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
						?>
					</td>
					<td align="center">
						<input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
					</td>
					<td align="center"><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
					<td align="center">
						<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
						<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
					</td>
					<td align="center">
						<? if ($type =='from'){ ?>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value, 'create_prod_search_list_view', 'search_div', 'trims_production_order_to_order_transfer_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
						<? 
						}
						else
						{
							?>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value+'_'+'<? echo $type;?>'+'_'+<? echo $cboSection;?>+'_'+<? echo $cboSubSection;?>+'_'+<? echo $cboItemGroup;?>+'_'+<? echo $cbo_uom;?>+'_'+<? echo $hid_color_id;?>+'_'+<? echo $hid_size_id;?>+'_'+'<? echo $item_description;?>', 'create_prod_search_list_view', 'search_div', 'trims_production_order_to_order_transfer_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
						<?
						} ?>
					</tr>
					<tr>
						<td colspan="9" align="center" valign="middle">
							<? echo load_month_buttons();  ?>
							<input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
						</td>
					</tr>
					<tr>
						<td colspan="9" align="center" valign="top" id=""><div id="search_div"></div></td>
					</tr>
				</tbody>
			</table>    
			</form>
		</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
	<?
	exit();
}

if($action=="create_prod_search_list_view")
{	
	$data=explode('_',$data);
	$party_id=str_replace("'","",$data[1]);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
 	$year_id=$data[8];
	
	
	if($db_type==0) $year_field="YEAR(c.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(c.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	if($year_id>0)
	{
		if($db_type==0)
		{
			$year_condition=" and YEAR(c.insert_date)='$year_id'";
		}
		else
		{
			$year_condition=" and to_char(c.insert_date,'YYYY')='$year_id'";
		}
	}
	
	$type=$data[9];
	if($type=='to')
	{
		$cboSection=$data[10];
		$cboSubSection=$data[11];
		$cboItemGroup=$data[12];
		$cbo_uom=$data[13];
		$hid_color_id=$data[14];
		$hid_size_id=$data[15];
		$item_description=$data[16];
	}
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}
	if($data[0]!=0) $company=" and c.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	//echo $search_type; die;
	
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com="and c.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no = '$search_str' ";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref = '$search_str' ";
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com="and c.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '$search_str%'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com="and c.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str'";  
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com="and c.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str%'"; 
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str%'";   
		}
	}

	if($search_str!="" && ($search_by==2 || $search_by==4 || $search_by==5))
	{
		if($db_type==0) $id_cond="group_concat(b.id) as id";
		else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as id";

		$job_dtls_ids = return_field_value("$id_cond", "trims_job_card_mst a, trims_job_card_dtls b", "a.entry_form=257 and a.id=b.mst_id and a.status_active=1 and b.status_active=1  $search_com_cond", "id");// and a.trims_job=b.job_no_mst
	}

	if($db_type==2 && $job_dtls_ids!="") $job_dtls_ids = $job_dtls_ids->load();
	if ($job_dtls_ids!="")
	{
		$job_dtls_ids=explode(",",$job_dtls_ids);
		$job_dtls_idsCond=""; $jobDtlsCond="";
		//echo count($job_dtls_ids); die;
		if($db_type==2 && count($job_dtls_ids)>=999)
		{
			$chunk_arr=array_chunk($job_dtls_ids,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",",$val);
				if($job_dtls_idsCond=="")
				{
					$job_dtls_idsCond.=" and ( b.job_dtls_id in ( $ids) ";
				}
				else
				{
					$job_dtls_idsCond.=" or  b.job_dtls_id in ( $ids) ";
				}
			}
			$job_dtls_idsCond.=")";
		}
		else
		{
			$ids=implode(",",$job_dtls_ids);
			$job_dtls_idsCond.=" and b.job_dtls_id in ($ids) ";
		}
	}
	else if($job_dtls_ids=='' && ($search_str!="" && ($search_by==2 || $search_by==4 || $search_by==5)))
	{
		echo "Not Found"; die;
	}

	if($party_id!=0) $party_id_cond=" and a.party_id='$party_id'"; else $party_id_cond="";

	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $receive_date = "and c.receive_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $receive_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $receive_date = "and c.receive_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $receive_date ="";
	}
	if ($within_group!=0) $withinGroup=" and c.within_group='$within_group'"; else $withinGroup="";
	if($within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}

	if($db_type==0) 
	{
		$ins_year_cond="year(c.insert_date)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(c.insert_date,'YYYY')";
	}
	if($type=='to')
	{
		if($cboSection!=0 && $cboSection!='') $item_cond .=" and e.section_id= $cboSection";
		if($cboSubSection!=0 && $cboSubSection!='') $item_cond .=" and d.sub_section= $cboSubSection";
		if($cboItemGroup!=0 && $cboItemGroup!='') $item_cond .=" and d.item_group_id= $cboItemGroup";
		if($hid_color_id!=0 && $hid_color_id!='') $item_cond .=" and d.color_id= $hid_color_id";
		if($hid_size_id!=0 && $hid_size_id!='') $item_cond .=" and d.size_id= $hid_size_id";
		if($item_description) $item_cond .=" and d.item_description= '$item_description'";

		/*$sql= "SELECT c.id,c.subcon_job, c.job_no_prefix_num, a.company_id , a.within_group,  a.party_id, a.party_location ,c.receive_date,  a.order_id, a.received_id, a.job_id, a.item, a.section_id ,$ins_year_cond as year, sum(a.quantity) as quantity
		from trims_production_mst a, trims_production_dtls b , subcon_ord_mst c, trims_job_card_dtls d , trims_job_card_mst e
		where a.entry_form=269 and a.id=b.mst_id and d.mst_id=e.id and d.id=b.job_dtls_id and c.id=e.received_id and e.received_id=a.received_id and a.received_id=c.id and c.entry_form=255 $receive_date $company $buyer $withinGroup $search_com $job_dtls_idscond $item_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 
		group by  c.id,c.subcon_job, c.job_no_prefix_num, a.company_id , a.within_group,  a.party_id, a.party_location ,c.receive_date,  a.order_id, a.received_id, a.job_id, a.item, a.section_id,c.insert_date
		order by c.id DESC";*/

	 	$sql= "SELECT c.id,c.subcon_job, c.job_no_prefix_num, c.company_id , c.within_group,  c.party_id, c.party_location ,c.receive_date,  c.order_id,c.order_no, c.id as received_id, e.id as job_id, d.item_description as item, e.section_id ,$ins_year_cond as year
		from subcon_ord_mst c, trims_job_card_dtls d , trims_job_card_mst e
		where  d.mst_id=e.id  and c.id=e.received_id and c.entry_form=255 $receive_date $company $buyer $withinGroup $search_com $job_dtls_idscond $item_cond $year_condition and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 
		group by c.id,c.subcon_job, c.job_no_prefix_num, c.company_id , c.within_group,  c.party_id, c.party_location ,c.receive_date,  c.order_id,c.order_no, c.id, e.id, d.item_description, e.section_id ,c.insert_date
		order by c.id DESC";

		//$sql= "select a.id, a.mst_id, a.job_no_mst, a.booking_dtls_id, a.receive_dtls_id , a.book_con_dtls_id, a.break_id, a.buyer_po_no, a.buyer_style_ref, a.item_description, a.color_id, a.size_id, a.uom, a.job_quantity,  a.impression,a.material_color,a.sub_section,a.item_group_id from trims_job_card_dtls a where a.status_active=1 and a.mst_id=$data[1]";

	}else{
		$sql= "SELECT c.id,c.subcon_job, c.job_no_prefix_num, a.company_id , a.within_group,  a.party_id, a.party_location ,c.receive_date,  a.order_id,c.order_no, a.received_id, a.job_id, a.item, a.section_id ,$ins_year_cond as year, sum(a.quantity) as quantity
		from trims_production_mst a, trims_production_dtls b, subcon_ord_mst c
		where a.entry_form=269 and a.id=b.mst_id and a.received_id=c.id and c.entry_form=255 and a.status_active=1 and b.status_active=1 $receive_date $company $buyer $withinGroup $search_com $job_dtls_idscond $year_condition 
		group by  c.id,c.subcon_job, c.job_no_prefix_num, a.company_id , a.within_group,  a.party_id, a.party_location ,c.receive_date,  a.order_id,c.order_no, a.received_id, a.job_id, a.item, a.section_id,c.insert_date
		order by c.id DESC";
	}
	
	//echo $sql; die;
	$data_array=sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="685" >
		<thead>
			<th width="30">SL</th>
			<th width="120">Order Receive No</th>
			<th width="60">Year</th>
			<th width="120">Section</th>
            <th width="120">W/O No</th>
			<th>Item</th>
		</thead>
		</table>
		<div style="width:685px; max-height:270px;overflow-y:scroll;" >	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="665" class="rpt_table" id="tbl_po_list">
		<tbody>
			<? 
			$i=1;
			foreach($data_array as $row)
			{  
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('subcon_job')].'_'.$row[csf('received_id')].'_'.$row[csf('job_id')]; ?>")' style="cursor:pointer" >
					<td width="30"><? echo $i; ?></td>
					<td width="120"><? echo $row[csf('subcon_job')]; ?></td>
					<td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
					<td width="120"><? echo $trims_section[$row[csf('section_id')]]; ?></td>
                    <td width="120"><? echo $row[csf('order_no')]; ?></td>
					<td style="text-align:center;"><? echo $row[csf('item')]; ?></td>
				</tr>
				<? 
				$i++; 
			} 
			?>
		</tbody>
	</table>
	<?
	exit();
}
/*if ($action=="load_drop_down_store")
{

	echo create_drop_down( "cbo_store_name", 160, "select a.id,a.store_name from lib_store_location a,lib_store_location_category  b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data and b.category_type in (4) $store_location_credential_cond group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "",0 );  	 
	exit();
}

if ($action=="load_drop_down_store_to")
{
	echo create_drop_down( "cbo_store_name_to", 160, "select a.id,a.store_name from lib_store_location a,lib_store_location_category  b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data and b.category_type in (4) $store_location_credential_cond group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "",0 );  	 
	exit();
}
*/



if($action=='create_po_search_list_view')
{
	$data=explode('_',$data);
	if ($data[0]==0) $buyer="%%"; else $buyer=$data[0];
	$search_string="%".trim($data[1])."%";
	$company_id=$data[2];
	
	//echo $company_id; die;
	
	if ($data[3]!="" &&  $data[4]!="")
	{
		if($db_type==0)
		{
			$shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3],'','',1)."' and '".change_date_format($data[4],'','',1)."'";
		} 
	}
	else $shipment_date ="";
	$type=$data[5]; 
	$arr=array (2=>$company_arr,3=>$buyer_arr);
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	if($type=="from") $status_cond=" and b.status_active in(1,3)"; else $status_cond=" and b.status_active=1";
	$sql= "select a.job_no_prefix_num, $year_field, a.job_no,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id and a.buyer_name like '$buyer' and b.po_number like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $status_cond $shipment_date order by b.id, b.pub_shipment_date";  
	 
	echo create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date", "70,60,70,80,120,90,110,90,80","850","200",0, $sql , "js_set_value", "id", "", 1, "0,0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date", "",'','0,0,0,0,0,1,0,1,3');
	exit();
}

if($action=='populate_data_from_order')
{
	$data=explode("**",$data);
	$po_id=$data[0];
	$which_order=$data[1];
	//$data_array=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$po_id");
	
	
	if($which_order=='from')
	{
		$data_array=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$po_id");
	}
	else
	{
		$data_array=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$po_id");
	}
	
	
	
	foreach ($data_array as $row)
	{ 
		$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
		foreach($gmts_item_id as $item_id)
		{
			if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
		}
		echo "document.getElementById('txt_".$which_order."_order_id').value 			= '".$po_id."';\n";
		echo "document.getElementById('txt_".$which_order."_order_no').value 			= '".$row[csf("po_number")]."';\n";
		echo "document.getElementById('txt_".$which_order."_po_qnty').value 			= '".$row[csf("po_quantity")]."';\n";
		echo "document.getElementById('cbo_".$which_order."_buyer_name').value 			= '".$row[csf("buyer_name")]."';\n";
		echo "document.getElementById('txt_".$which_order."_style_ref').value 			= '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('txt_".$which_order."_job_no').value 				= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_".$which_order."_gmts_item').value 			= '".$gmts_item."';\n";
		echo "document.getElementById('txt_".$which_order."_shipment_date').value 		= '".change_date_format($row[csf("shipment_date")])."';\n";

		exit();
	}
}

if($action=="load_drop_down_item_desc")
{
	$item_description=array();
	$sql="select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.po_breakdown_id=$data and b.entry_form in(24,485,12) and b.trans_type in(1,5) and b.status_active=1 and b.is_deleted=0";
	$dataArray=sql_select($sql);	
	foreach($dataArray as $row)
	{
		$item_description[$row[csf('id')]]=$row[csf('product_name_details')];
	}
	echo create_drop_down( "cbo_item_desc", 403, $item_description,'', 1, "--Select Item Description--",'0','','1');  
	exit();
}
if($action=="show_ile_load_uom")
{
	$data=explode("_",$data);
	$uom=$trim_group_arr[$data[0]]['uom'];
	echo "document.getElementById('cbo_uom').value 	= '".$data[1]."';\n";
	exit();	
}

if($action=="show_dtls_list_view")
{
	 
				
	$data_ref=explode("__",$data);
	$received_id=$data_ref[0];
	$store_id=$data_ref[1];
	 
	$group_arr=return_library_array( "select id, item_name from lib_item_group where status_active=1 and item_category=4", "id", "item_name");
	$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from  lib_size",'id','size_name');
				 

	$prod_sql= "select c.id,b.section_id,c.sub_section,c.item_group_id,c.item_description,c.color_id,c.size_id,c.uom, a.qc_qty from trims_production_dtls a ,trims_production_mst b, trims_job_card_dtls c where  a.mst_id=b.id and a.job_dtls_id=c.id and b.received_id=$received_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	//, a.comp_prod, a.reject_qty, a.prod_time, a.remarks, a.booking_dtls_id, a.receive_dtls_id, a.job_dtls_id, a.book_con_dtls_id, a.break_id
	//echo $sql;
	$data_array=sql_select($prod_sql);
	$prodn_arr=array(); $prodn_jobDtls_arr=array();
	foreach($data_array as $row)
	{
		$prodn_arr[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_group_id')]][$row[csf('item_description')]][$row[csf('color_id')]][$row[csf('size_id')]][$row[csf('uom')]]['qc_qty']+=$row[csf('qc_qty')];
		$prodn_arr[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_group_id')]][$row[csf('item_description')]][$row[csf('color_id')]][$row[csf('size_id')]][$row[csf('uom')]]['job_dtls_id'].=$row[csf('id')].',';
		
		//$jobDtls_arr[$row[csf('id')]]['material_color']=$row[csf('material_color')];
	}
	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="840">
    	<thead>
            <th width="30">Sl.</th>
            <th width="80">Section</th>
            <th width="80">Sub Section</th>
            <th width="80">Trims Group</th>
            <th width="100">Style</th>
            <th width="100">Item Description</th>
            <th width="80">Item Color</th>
            <th width="60">Item Size</th>
            <th width="50">Booked UOM</th>
            <th width="50">Prod. Qty</th>
            <th width="50">Cum. Transfer Qty</th> 
            <th width="">Yet to Transfer qty</th>
        </thead>
    </table>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="840" id="tbl_list_search">
        <tbody>
            <? 
            $i=1;
			foreach($prodn_arr as $section_id=> $section_id_data)
			{ 
				foreach($section_id_data as $sub_section=> $sub_section_data)
				{
					foreach($sub_section_data as $item_group_id=> $item_group_data)
					{ 
						foreach($item_group_data as $item_description=> $item_description_data)
						{
							foreach($item_description_data as $color_id=> $color_id_data)
							{ 
								foreach($color_id_data as $size_id=> $size_id_data)
								{
									foreach($size_id_data as $uom=> $row)
									{ 
									
									
									
									
										$job_dtls_ids=chop($row['job_dtls_id'],',');
										$job_dtls_id=implode(",",array_unique(explode(",",$job_dtls_ids)));
										
										
										
										//echo "select sum(b.quantity) as quantity from trims_item_transfer_dtls b where b.status_active=1 and b.to_job_dtls_id=$job_dtls_id"; die;
										$production_sql=sql_select(" select sum(b.quantity) as quantity from trims_item_transfer_dtls b where b.status_active=1 and b.to_job_dtls_id=$job_dtls_id");
									     $prev_production_qnty=$production_sql[0][csf("quantity")];
									
									//echo "10**".$prev_production_qnty; die;
									
									if($prev_production_qnty!="")
									{
									$prev_production_qnty=$prev_production_qnty;
									}
									else
									{
									$prev_production_qnty=0;
									}
									//$hid_production_qty=str_replace("'","",$hid_production_qty);
									$txt_transfer_qnty=$row['qc_qty'];
									
									$balance_prod_qty=$txt_transfer_qnty-$prev_production_qnty;
									
									
										$data=$job_dtls_id."**".$section_id."**".$sub_section."**".$item_group_id."**".$item_description."**".$color_id."**".$size_id."**".$color_arr[$color_id]."**".$size_arr[$size_id]."**".$uom."**".$row['qc_qty']."**".$prev_production_qnty."**".$balance_prod_qty;
										
										
										
									
										
										?>
								        <tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $data; ?>");change_color("tr_<? echo $i; ?>","<? echo $bgcolor;?>");' id="tr_<? echo $i; ?>" style="cursor:pointer" >
								            <td width="30"><? echo $i; ?>
								                <input type="hidden" name="txt_so_dtls_id" id="txt_so_dtls_id<?php echo $i ?>" value="<? echo $so_dtls_id; ?>"/>
								                <input type="hidden" name="txt_pre_cost_dtls_id" id="txt_pre_cost_dtls_id<?php echo $i ?>" value="<? echo $pre_cost_dtls_id; ?>"/>
								                <input type="hidden" name="txt_batch_id" id="txt_batch_id<?php echo $i ?>" value="<? echo $batch_id; ?>"/>
								                <input type="hidden" name="txt_batch_dtls_id" id="txt_batch_dtls_id<?php echo $i ?>" value="<? echo $batch_dtls_id; ?>"/>
								            </td>
								            <td width="80"><p><? echo $trims_section[$section_id]; ?></td>
								            <td width="80"><p><? echo $trims_sub_section[$sub_section]; ?></td>
								            <td width="80"><p><? echo $group_arr[$item_group_id]; ?></td>
								            <td width="100">Style</td>
								            <td width="100"><p><? echo $item_description; ?></p></td>
								            <td width="80"><? echo $color_arr[$color_id]; ?></td>
								            <td width="60"><? echo $size_arr[$size_id]; ?></td>
								            <td width="50"><? echo $unit_of_measurement[$uom]; ?></td>
								            <td width="50"><p><? echo $row['qc_qty']; ?></p></td>
                                            <td width="50"><p><? echo $prev_production_qnty; ?></p></td>
                                            <td width=""><p><? echo $balance_prod_qty; ?></p></td>
								        </tr>
								        <?
										$i++;
									}
								}
							}
						}
					}
				}
			}
            ?>
        </tbody>
    </table>
	<?
	exit();
}

if ($action=="load_mst_php_data_to_form")
{
	$data=explode('_',$data);
	$id=$data[0];
	$sql_mst="select a.id, a.trims_job, a.party_id, a.received_no, a.received_id, b.order_no, b.order_id ,b.within_group from trims_job_card_mst a , subcon_ord_mst b where a.received_id=b.id and b.id='$id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

	$nameArray=sql_select($sql_mst);
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_from_order_no').value 		= '".$row[csf("order_no")]."';\n";
		echo "document.getElementById('txt_from_order_id').value 		= '".$row[csf("order_id")]."';\n";
		echo "fnc_load_party(1,'".$row[csf("within_group")]."',1);\n";
		echo "document.getElementById('cbo_from_buyer_name').value 		= '".$row[csf("party_id")]."';\n";  
		echo "document.getElementById('txt_order_rcv_no').value 		= '".$row[csf("received_no")]."';\n";  
		echo "document.getElementById('txt_from_job_no').value 			= '".$row[csf("trims_job")]."';\n";  
		echo "document.getElementById('order_received_id').value 		= '".$row[csf("received_id")]."';\n";  
		echo "document.getElementById('job_id').value 					= '".$row[csf("id")]."';\n";  
		//echo "document.getElementById('production_id').value 			= '".$row[csf("item_description")]."';\n";  
	}
	exit();	
}

if ($action=="load_mst_php_data_to_form_to")
{
	$data=explode('_',$data);
	$id=$data[0];
	$sql_mst="select a.id, a.trims_job, a.party_id, a.received_no, a.received_id, b.order_no, b.order_id ,b.within_group from trims_job_card_mst a , subcon_ord_mst b where a.received_id=b.id and b.id='$id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

	$nameArray=sql_select($sql_mst);
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_to_order_no').value 		= '".$row[csf("order_no")]."';\n";
		echo "document.getElementById('txt_to_order_id').value 		= '".$row[csf("order_id")]."';\n";
		echo "fnc_load_party(2,'".$row[csf("within_group")]."',2);\n";
		echo "document.getElementById('cbo_to_buyer_name').value 	= '".$row[csf("party_id")]."';\n";  
		echo "document.getElementById('txt_to_order_rcv_no').value 	= '".$row[csf("received_no")]."';\n";  
		echo "document.getElementById('txt_to_job_no').value 		= '".$row[csf("trims_job")]."';\n";  
		echo "document.getElementById('to_order_received_id').value = '".$row[csf("received_id")]."';\n";  
		echo "document.getElementById('to_job_id').value 			= '".$row[csf("id")]."';\n";  
		//echo "document.getElementById('production_id').value 			= '".$row[csf("item_description")]."';\n";  
	}
	exit();	
}


if ($action=="orderToorderTransfer_popup")
{
	echo load_html_head_contents("Order To Order Transfer Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		
		function js_set_value(data)
		{
			var id = data.split("_");
			$('#transfer_id').val(id[0]);
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
<div align="center" style="width:800px;">
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:800px;margin-left:10px">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="800" class="rpt_table">
                <thead>
                 <tr>
                    <th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",4 ); ?></th>
                </tr>
                 <tr>
                    <th width="200">Search By</th>
                    <th width="200" id="search_by_td_up">Please Enter Transfer ID</th>
                    <th width="250">Date Range</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="transfer_id" id="transfer_id" class="text_boxes" value="">
                        <input type="hidden" name="hidden_posted_in_account" id="hidden_posted_in_account" class="text_boxes" value="">
                    </th>
                   </tr>
                </thead>
                <tr class="general">
                    <td>
						<?
							$search_by_arr=array(1=>"Transfer ID",2=>"Challan No.");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td id="search_by_td">
                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td>
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px">To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
                    </td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_transfer_search_list_view', 'search_div', 'trims_production_order_to_order_transfer_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                	<td colspan="4" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
            </table>
        	<div style="margin-top:10px" id="search_div"></div> 
		</fieldset>
	</form>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=='create_transfer_search_list_view')
{
	$data = explode("_",$data);
	$search_str="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$date_form=$data[3];
	$date_to =$data[4];
	$year_id=$data[5];
	$search_type =$data[6];
	
	if($db_type==0)
	{
		$date_form=change_date_format($date_form,'yyyy-mm-dd');
		$date_to=change_date_format($date_to,'yyyy-mm-dd');
	}
	else
	{
		$date_form=change_date_format($date_form,'','',1);
		$date_to=change_date_format($date_to,'','',1);
	}
	
	if($date_form!="" && $date_to!="") $date_cond=" and a.transfer_date between '$date_form' and '$date_to'";
	
	//echo $date_form."=".$date_to."=".$year_id;die;
	
	
	//if($search_by==1)
		//$search_field="transfer_system_id";	
	//else
		//$search_field="challan_no";
		
		//and $search_field like '$search_string' 
		
	$search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.transfer_system_id='$search_str'";
			else if($search_by==2) $search_com_cond="and a.challan_no='$search_str'"; 
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		
		if($search_str!="")
		{
			//echo $search_type; die;
			
			if($search_by==1) $search_com_cond="and a.transfer_system_id like '%$search_str%'"; 
			else if($search_by==2) $search_com_cond="and a.challan_no like '%$search_str%'"; 
			 
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.transfer_system_id like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.challan_no like '$search_str%'";  
			
			 
			 
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.transfer_system_id like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.challan_no like '%$search_str'";  
			 
			 
		}
	}
		
		
		
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	if($year_id>0)
	{
		if($db_type==0)
		{
			$year_condition=" and YEAR(a.insert_date)='$year_id'";
		}
		else
		{
			$year_condition=" and to_char(a.insert_date,'YYYY')='$year_id'";
		}
	}
	
 	$sql="select a.id, a.transfer_prefix, a.transfer_prefix_number, a.transfer_system_id, a.company_id, a.challan_no, a.transfer_date,b.from_order_no,b.to_order_no   from trims_item_transfer_mst a,trims_item_transfer_dtls b where  a.id=b.mst_id and a.company_id=$company_id $search_com_cond and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  $date_cond $year_condition group by a.id, a.transfer_prefix, a.transfer_prefix_number, a.transfer_system_id, a.company_id, a.challan_no, a.transfer_date,b.from_order_no,b.to_order_no order by a.id DESC";
	$mst_result=sql_select($sql);
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	if(count($mst_result)>0)
	{
		?>
		 <table width="800" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr> 
                	<th width="40">Sl</th>
                    <th width="120">Company</th>
                    <th width="120">Transfer System ID</th>
                    <th width="120">Challan No.</th>
                    <th width="120">From Work Order</th>
                    <th width="120">To Work Order</th>
                     <th>Transfer Date</th>
                </tr>
            </thead>
            <tbody id="tbl_list_search">
            <?
			$i=1;
          	foreach($mst_result as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF";
				else $bgcolor="#FFFFFF";
				?>
             	<tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf("id")] ?>")' style="cursor:pointer" >
                	<td align="center"><? echo $i; ?></td>
                    <td ><p><? echo $company_arr[$row[csf("company_id")]]; ?></p></td>
                    <td ><p><? echo $row[csf("transfer_system_id")]; ?></p></td>
                    <td ><p><? echo $row[csf("challan_no")]; ?></p></td>
                    <td ><p><? echo $row[csf("from_order_no")]; ?></p></td>
                    <td ><p><? echo $row[csf("to_order_no")]; ?></p></td>
                    <td ><p><? echo change_date_format($row[csf("transfer_date")]); ?></p></td>
                </tr>
          		<? 
				$i++; 
			} 
			?>
            </tbody>
        </table> 
   		<?	
	}
	exit();
}

if($action=='populate_data_from_transfer_master')
{
	$data_array=sql_select("select transfer_system_id, company_id, challan_no, transfer_date from trims_item_transfer_mst where id='$data' and entry_form=485 and status_active=1 and is_deleted=0");
	
	$company_id=$data_array[0][csf("company_id")];
	//$store_method=$variable_inventory_sql[0][csf("store_method")];
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		//echo "document.getElementById('cbo_company_id_to').value 			= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";
		//echo "get_php_form_data('".$row[csf("from_order_id")]."**from'".",'populate_data_from_order','requires/trims_production_order_to_order_transfer_entry_controller');\n";
		//echo "get_php_form_data('".$row[csf("to_order_id")]."**to'".",'populate_data_from_order','requires/trims_production_order_to_order_transfer_entry_controller');\n";
		//echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		//echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_trims_transfer_entry',1,1);\n"; 
		exit();
	}
}

if($action=="show_transfer_listview")
{
	//$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=4","id","product_name_details");
	$group_arr=return_library_array( "select id, item_name from lib_item_group where status_active=1 and item_category=4", "id", "item_name");
	$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$size_library_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	
	$sql="select id, mst_id, company_id ,from_order_id, from_order_no, from_party_id, from_received_id, from_job_id, from_job_dtls_id, to_order_id, to_order_no, to_party_id, to_received_id, to_job_id, to_job_dtls_id, entry_form, section_id, sub_section_id, trim_group_id, uom, color_id, size_id, item_description, quantity from trims_item_transfer_dtls where mst_id=$data and status_active = '1' and is_deleted = '0'";
	$item_result=sql_select($sql);
	if(count($item_result)>0)
	{
		?>
		 <table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr> 
                	<th width="20">Sl</th>
                    <th width="80">Section</th>
                    <th width="80">Sub Section</th>
                    <th width="80">Trims Group</th>
                    <th width="80">Style</th>
                    <th width="120">Item Description</th>
                    <th width="80">Item Color</th>
                    <th width="80">Item Size</th>
                    <th width="50">Booked UOM</th>
                    <th>Qty</th>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
          	foreach($item_result as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF";
				else $bgcolor="#FFFFFF";
				
				//echo str_replace("'","",$data);  
				
								
				 
				?>
             	<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("id")]."_".$row[csf("from_received_id")]."_".$row[csf("to_received_id")]."_".str_replace("'","",$data)."_".$row[csf("from_job_dtls_id")]."_".$row[csf("to_job_dtls_id")];?>","child_form_item_data","requires/trims_production_order_to_order_transfer_entry_controller")'  style="cursor:pointer" >
                	<td align="center"><? echo $i; ?></td>
                    <td ><p><? echo $trims_section[$row[csf("section_id")]]; ?></p></td>
                    <td ><p><? echo $trims_sub_section[$row[csf("sub_section_id")]]; ?></p></td>
                    <td ><p><? echo $group_arr[$row[csf("trim_group_id")]]; ?></p></td>
                    <td >Style</td>
                    <td ><p><? echo $row[csf("item_description")]; ?></p></td>
                    <td ><p><? echo $color_library_arr[$row[csf("color_id")]]; ?></p></td>
                    <td ><p><? echo $size_library_arr[$row[csf("size_id")]]; ?></p></td>
                    <td ><p><? echo $unit_of_measurement[$row[csf("uom")]]; ?></p></td>
                    <td ><p><? echo $row[csf("quantity")]; ?></p></td>
                </tr>
          		<? 
				$i++; 
			} 
			?>
            </tbody>
        </table> 
   		<?	
	}
}

if($action=="child_form_item_data")
{
    $data=explode("_",$data);
	$trans_id=$data[0];
	$from_rec_id=$data[1];
	$to_rec_id=$data[2];
	$mst_id=$data[3];
	$from_job_dtls_id=$data[4];
	$to_job_dtls_id=$data[5];

	$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$size_library_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	

	$sql_mst="select a.id, a.trims_job, a.party_id, a.received_no, a.received_id, b.order_no, b.order_id ,b.within_group from trims_job_card_mst a , subcon_ord_mst b where a.received_id=b.id and b.id in ($from_rec_id,$to_rec_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$mst_res = sql_select($sql_mst);
	foreach($mst_res as $row)
	{
		$rcv_job_arr[$row[csf("received_id")]][$row[csf("id")]]['received_no']=$row[csf("received_no")];
		$rcv_job_arr[$row[csf("received_id")]][$row[csf("id")]]['trims_job']=$row[csf("trims_job")];
		$rcv_job_arr[$row[csf("received_id")]][$row[csf("id")]]['within_group']=$row[csf("within_group")];
	}
	
	
				/*$job_dtls_ids=chop($row['job_dtls_id'],',');
				$job_dtls_id=implode(",",array_unique(explode(",",$job_dtls_ids)));
  				//echo "select sum(b.quantity) as quantity from trims_item_transfer_dtls b where b.status_active=1 and b.to_job_dtls_id=$job_dtls_id"; die;
				$production_sql=sql_select(" select sum(b.quantity) as quantity from trims_item_transfer_dtls b where b.status_active=1 and b.to_job_dtls_id=$job_dtls_id");
				$prev_production_qnty=$production_sql[0][csf("quantity")];
				*/
						
						
						
						$job_dtls_id=$data[4];
						
						//echo "select c.id,b.section_id,c.sub_section,c.item_group_id,c.item_description,c.color_id,c.size_id,c.uom, a.qc_qty from trims_production_dtls a ,trims_production_mst b, trims_job_card_dtls c where  a.mst_id=b.id and a.job_dtls_id=c.id and a.job_dtls_id=$job_dtls_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0"; die;
 						$prod_sql= sql_select("select c.id,b.section_id,c.sub_section,c.item_group_id,c.item_description,c.color_id,c.size_id,c.uom, a.qc_qty from trims_production_dtls a ,trims_production_mst b, trims_job_card_dtls c where  a.mst_id=b.id and a.job_dtls_id=c.id and a.job_dtls_id=$job_dtls_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
  						$production_qnty=$prod_sql[0][csf("qc_qty")];
					
				
				   // echo "select sum(b.quantity) as quantity from trims_item_transfer_dtls b where b.status_active=1 and b.to_job_dtls_id=$job_dtls_id and b.id!=$trans_id"; die;
				 
								$transfer_sql=sql_select("select sum(b.quantity) as quantity from trims_item_transfer_dtls b where b.status_active=1 and b.to_job_dtls_id=$job_dtls_id and b.id!=$trans_id");
									$prev_transfer_qnty=$transfer_sql[0][csf("quantity")];
									
									//echo "10**".$prev_production_qnty; die;
									
									if($prev_transfer_qnty!="")
									{
										$prev_transfer_qnty=$prev_transfer_qnty;
									}
									else
									{
										$prev_transfer_qnty=0;
									}  
 									$balance_prod_qty=$production_qnty-$prev_transfer_qnty;
	
	
	
	
	
	

	$sql="select id, mst_id, company_id ,from_order_id, from_order_no, from_party_id, from_received_id, from_job_id, from_job_dtls_id, to_order_id, to_order_no, to_party_id, to_received_id, to_job_id, to_job_dtls_id, entry_form, section_id, sub_section_id, trim_group_id, uom, color_id, size_id, item_description, quantity,remarks from trims_item_transfer_dtls where mst_id='$mst_id' and id='$trans_id' and status_active = '1' and is_deleted = '0'";
	$res = sql_select($sql);
	
	foreach($res as $row)
	{
		$from_within_group=$rcv_job_arr[$row[csf("from_received_id")]][$row[csf("from_job_id")]]['within_group'];
		$from_received_no=$rcv_job_arr[$row[csf("from_received_id")]][$row[csf("from_job_id")]]['received_no'];
		$from_job_no=$rcv_job_arr[$row[csf("from_received_id")]][$row[csf("from_job_id")]]['trims_job'];
		$to_within_group=$rcv_job_arr[$row[csf("to_received_id")]][$row[csf("to_job_id")]]['within_group'];
		$to_received_no=$rcv_job_arr[$row[csf("to_received_id")]][$row[csf("to_job_id")]]['received_no'];
		$to_job_no=$rcv_job_arr[$row[csf("to_received_id")]][$row[csf("to_job_id")]]['trims_job'];
		
		echo "$('#hid_production_qty').val('".$production_qnty."');\n";
		echo "$('#txt_prod_qty').val('".$production_qnty."');\n";
		echo "$('#txt_cum_prod_qty').val('".$prev_transfer_qnty."');\n";
		echo "$('#txt_yet_transfer_qty').val('".$balance_prod_qty."');\n";
		echo "$('#cboSection').val('".$row[csf("section_id")]."');\n";
		echo "$('#cboSubSection').val('".$row[csf("sub_section_id")]."');\n";
		echo "$('#cboItemGroup').val('".$row[csf("trim_group_id")]."');\n";
		echo "$('#txt_item_description').val('".$row[csf("item_description")]."');\n";
		echo "$('#hid_color_id').val('".$row[csf("color_id")]."');\n";
		echo "$('#hid_size_id').val('".$row[csf("size_id")]."');\n";
		echo "$('#txt_item_color').val('".$color_library_arr[$row[csf("color_id")]]."');\n";
		echo "$('#txt_item_size').val('".$size_library_arr[$row[csf("size_id")]]."');\n";
		echo "$('#cbo_uom').val('".$unit_of_measurement[$row[csf("uom")]]."');\n";
		echo "$('#hid_job_dtls_id').val('".$row[csf("to_job_dtls_id")]."');\n";
		echo "$('#txt_transfer_qnty').val('".$row[csf("quantity")]."');\n";
		echo "$('#txt_remark').val('".$row[csf("remarks")]."');\n";
		echo "$('#update_dtls_id').val('".$row[csf("id")]."');\n";

		echo "document.getElementById('txt_from_order_no').value 		= '".$row[csf("from_order_no")]."';\n";
		echo "document.getElementById('txt_from_order_id').value 		= '".$row[csf("from_order_id")]."';\n";
		
		echo "fnc_load_party(1,'".$from_within_group."',1);\n";
		
		echo "document.getElementById('cbo_from_buyer_name').value 		= '".$row[csf("from_party_id")]."';\n";  
		
		echo "document.getElementById('txt_order_rcv_no').value 		= '".$from_received_no."';\n";  
		echo "document.getElementById('txt_from_job_no').value 			= '".$from_job_no."';\n";  
		
		echo "document.getElementById('order_received_id').value 		= '".$row[csf("from_received_id")]."';\n";  
		echo "document.getElementById('job_id').value 					= '".$row[csf("from_job_id")]."';\n";

		echo "document.getElementById('txt_to_order_no').value 			= '".$row[csf("to_order_no")]."';\n";
		echo "document.getElementById('txt_to_order_id').value 			= '".$row[csf("to_order_id")]."';\n";
		
		echo "fnc_load_party(2,'".$to_within_group."',2);\n";

		echo "document.getElementById('cbo_to_buyer_name').value 		= '".$row[csf("to_party_id")]."';\n";  
		
		echo "document.getElementById('txt_to_order_rcv_no').value 		= '".$to_received_no."';\n";  
		echo "document.getElementById('txt_to_job_no').value 			= '".$to_job_no."';\n";  
		
		echo "document.getElementById('to_order_received_id').value 	= '".$row[csf("to_received_id")]."';\n";  
		echo "document.getElementById('to_job_id').value 				= '".$row[csf("to_job_id")]."';\n";
		echo "show_list_view('".$row[csf("from_received_id")]."','show_dtls_list_view','list_fabric_desc_container','requires/trims_production_order_to_order_transfer_entry_controller','setFilterGrid(\'tbl_list_search\',-1);');\n";
		
		 
		//echo "show_list_view('".$row[csf("from_received_id")]."**".$row[csf("id")]."','show_dtls_list_view','list_fabric_desc_container','requires/trims_production_order_to_order_transfer_entry_controller','');\n";
		//show_list_view(ex_data[0],'show_dtls_list_view','list_fabric_desc_container','requires/trims_production_order_to_order_transfer_entry_controller','setFilterGrid(\'tbl_list_search\',-1);');
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_trims_transfer_entry',1,1);\n"; 
		
   	}	
	exit();
}

if($action=='populate_transfer_details_form_data')
{
	
	//$data_array=sql_select("select transfer_system_id, challan_no, company_id, transfer_date, item_category, from_order_id,to_order_id, from_store_id, to_store_id from inv_item_transfer_mst where id='$data' and status_active=1 and is_deleted=0");
	
	$data_array=sql_select("select a.from_order_id, a.to_order_id, a.from_store_id, a.to_store_id, b.id, b.mst_id, b.item_group, b.from_prod_id, b.transfer_qnty, b.item_category, b.uom from inv_item_transfer_mst a, inv_item_transfer_dtls b 
	where a.id=b.mst_id and b.id='$data'");
	foreach ($data_array as $row)
	{ 
		
		//echo "select from_order_id from inv_item_transfer_mst where id=".$row[csf('mst_id')]." and  status_active=1 and is_deleted=0 ";
		
		
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_item_desc').value 				= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('txt_transfer_qnty').value 			= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('txt_item_id').value 					= '".$row[csf("item_group")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";
		
		
		/* $sql_sk =sql_select( "select 
		 				sum(case when b.entry_form in(24) then b.quantity else 0 end) as recv_qty,
		 				sum(case when b.entry_form in(25) then b.quantity else 0 end) as issue_qty
						from product_details_master a, order_wise_pro_details b, inv_transaction c
			where  
				a.id=b.prod_id and b.trans_id=c.id and a.item_category_id=4 and b.entry_form in(24,25) and b.po_breakdown_id=".$cond_po_id." and b.prod_id='".$row[csf("from_prod_id")]."'  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0");	*/
				
		/*
		$conversion_factor=return_field_value("b.conversion_factor as conversion_factor","product_details_master a, lib_item_group b","a.item_group_id=b.id and a.item_category_id=4 and a.entry_form=24 and a.id='".$row[csf("from_prod_id")]."'","conversion_factor");
				
		$sql_trim = sql_select("select 
		sum((case when b.entry_form in(24,73,485,112) and b.trans_type in(1,4,5) then b.quantity else 0 end)-(case when b.entry_form in(25,49,485,112) and b.trans_type in(2,3,6) then b.quantity else 0 end)) as balance_qnty 
		from product_details_master a, order_wise_pro_details b, inv_transaction c
		where  a.id=b.prod_id and b.trans_id=c.id and a.item_category_id=4 and c.item_category=4 and b.entry_form in(24,25,485,73,49,112) and b.trans_type in(1,2,3,4,5,6) and c.transaction_type in(1,2,3,4,5,6) and c.status_active=1  and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id=".$row[csf("from_order_id")]." and b.prod_id='".$row[csf("from_prod_id")]."' and c.store_id ='".$row[csf("from_store_id")]."' ");
		
		
		$curr_stock=($sql_trim[0][csf('balance_qnty')]*$conversion_factor)+$row[csf("transfer_qnty")];
		echo "document.getElementById('txt_current_stock').value 			= '".$curr_stock."';\n";
		$sql_trans=sql_select("select trans_id from order_wise_pro_details where dtls_id=".$row[csf('id')]." and entry_form=485 and trans_type in(5,6) order by trans_type DESC");
		echo "document.getElementById('update_trans_issue_id').value 		= '".$sql_trans[0][csf("trans_id")]."';\n";
		echo "document.getElementById('update_trans_recv_id').value 		= '".$sql_trans[1][csf("trans_id")]."';\n";*/
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_trims_transfer_entry',1,1);\n"; 
		
		exit();
	}
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		
		
		/*$transfer_recv_num=''; $transfer_update_id='';
		$sql_budge_check=sql_select("select a.id from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id and a.trim_group=$txt_item_id and b.po_break_down_id=$txt_from_order_id");
		if(count($sql_budge_check)<1)
		{
			echo "11**This Item Not Found In Budget";
			disconnect($con);
			die;
		}*/
		
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond=""; 
		
			$id=return_next_id( "id", "trims_item_transfer_mst", 1 ) ;
			$new_transfer_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'TFOTOT', date("Y",time()), 5, "select transfer_prefix, transfer_prefix_number from trims_item_transfer_mst where company_id=$cbo_company_id and entry_form=485 and $year_cond=".date('Y',time())." order by id desc ", "transfer_prefix", "transfer_prefix_number" ));
			
			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date ,entry_form , inserted_by, insert_date";
 			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",485,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//echo "insert into inv_item_transfer_mst (".$field_array.") values ".$data_array;die;
			$transfer_recv_num=$new_transfer_system_id[0];
			$transfer_update_id=$id;
		}
		else
		{
			$field_array_update="challan_no*transfer_date*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			/*$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; */
			
			$transfer_recv_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);
		}
 		$id_trans=return_next_id( "id", "trims_item_transfer_dtls", 1 ) ;
		//$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		
		$field_array_dtls=" id, mst_id, company_id ,from_order_id, from_order_no, from_party_id, from_received_id, from_job_id, from_job_dtls_id, to_order_id, to_order_no, to_party_id, to_received_id, to_job_id, to_job_dtls_id, entry_form, section_id, sub_section_id, trim_group_id, uom, color_id, size_id, item_description, quantity, remarks, inserted_by, insert_date";
		
		$data_array_dtls="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$txt_from_order_id.",".$txt_from_order_no.",".$cbo_from_buyer_name.",".$order_received_id.",".$job_id.",".$hid_job_dtls_id.",".$txt_to_order_id.",".$txt_to_order_no.",".$cbo_to_buyer_name.",".$to_order_received_id.",".$to_job_id.",".$hid_job_dtls_id.",485,".$cboSection.",".$cboSubSection.",".$cboItemGroup.",".$cbo_uom.",".$hid_color_id.",".$hid_size_id.",".$txt_item_description.",".$txt_transfer_qnty.",".$txt_remark.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$duplicate = is_duplicate_field("b.id","trims_item_transfer_mst a, trims_item_transfer_dtls b","a.id=b.mst_id and a.id=$transfer_update_id and b.from_order_no=$txt_from_order_no  and b.to_order_no=$txt_to_order_no  and b.to_job_dtls_id=$hid_job_dtls_id and  a.status_active=1 and b.status_active=1 and b.status_active=1 and b.status_active=1");
		
		//echo "10**".$duplicate; die; 
		if($duplicate==1) 
		{
			echo "20**Duplicate Item is Not Allow in Same MRR Number.";
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}
		
		
		
			$production_sql=sql_select(" select sum(b.quantity) as quantity from trims_item_transfer_dtls b where b.status_active=1 and b.to_job_dtls_id=$hid_job_dtls_id");
			$prev_production_qnty=$production_sql[0][csf("quantity")];
			
			//echo "10**".$prev_production_qnty; die;
			
			if($prev_production_qnty!="")
			{
				$prev_production_qnty=$prev_production_qnty;
			}
			else
			{
  				$prev_production_qnty=0;
			}
 			$hid_production_qty=str_replace("'","",$hid_production_qty);
			$txt_transfer_qnty=str_replace("'","",$txt_transfer_qnty);
  		 	$allow_qnty=($hid_production_qty-$prev_production_qnty);
			if($txt_transfer_qnty>$allow_qnty)
			{
 				echo "30** Transfer Quantity Not Allow More Then Production Quantity.";disconnect($con);die;
			}
		
		$rID=$rID2=true;
 		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("trims_item_transfer_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("trims_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; 
		}
		$rID2=sql_insert("trims_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		
		$txt_from_order_id=str_replace("'","",$txt_from_order_id);
		//echo "10**INSERT INTO trims_item_transfer_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls; die;
		//echo "10**".$rID."**".$rID2."**".$flag; die;//echo $flag;die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$transfer_update_id."**".$transfer_recv_num."**".$txt_from_order_id."**".$order_received_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 

				echo "5**0**"."&nbsp;"."**".$txt_from_order_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);   
				echo "0**".$transfer_update_id."**".$transfer_recv_num."**".$txt_from_order_id."**".$order_received_id;
			}
			else
			{
				oci_rollback($con);
				echo "5**0**"."&nbsp;"."**".$txt_from_order_id;
			}
		}
		
		disconnect($con);
		die;
	}	
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
 		$field_array_update="challan_no*transfer_date*updated_by*update_date";
		$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$field_array_dtls="quantity*remarks*updated_by*update_date";
		$data_array_dtls=$txt_transfer_qnty."*".$txt_remark."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$update_id=str_replace("'","",$update_id);
		$rID=$rID2=true;
		 $rID=sql_update("trims_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;
		
		$rID2=sql_update("trims_item_transfer_dtls",$field_array_dtls,$data_array_dtls,"id",$update_dtls_id,1);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}
		$txt_from_order_id=str_replace("'","",$txt_from_order_id);
		$txt_system_id=str_replace("'","",$txt_system_id);
		$hid_job_dtls_id=str_replace("'","",$hid_job_dtls_id);
		
		
			$production_sql=sql_select(" select sum(b.quantity) as quantity from trims_item_transfer_dtls b where b.status_active=1 and b.to_job_dtls_id=$hid_job_dtls_id and id!=$update_dtls_id");
			$prev_production_qnty=$production_sql[0][csf("quantity")];
			
			//echo "10**".$prev_production_qnty; die;
			
			if($prev_production_qnty!="")
			{
				$prev_production_qnty=$prev_production_qnty;
			}
			else
			{
  				$prev_production_qnty=0;
			}
			
			
 			$hid_production_qty=str_replace("'","",$hid_production_qty);
			
			//echo "10**".$hid_production_qty; die;
			$txt_transfer_qnty=str_replace("'","",$txt_transfer_qnty);
  		 	$allow_qnty=($hid_production_qty-$prev_production_qnty);
			if($txt_transfer_qnty>$allow_qnty)
			{
 				echo "30** Transfer Quantity Not Allow More Then Production Quantity.";disconnect($con);die;
			}
		
		//echo "10**$rID=$rID2";oci_rollback($con);die;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".$update_id."**".$txt_system_id."**".$txt_from_order_id."**".$order_received_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$update_id."**".$txt_system_id."**".$txt_from_order_id."**".$order_received_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);   
				echo "1**".$update_id."**".$txt_system_id."**".$txt_from_order_id."**".$order_received_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$update_id."**".$txt_system_id."**".$txt_from_order_id."**".$order_received_id;
			}
		}	
		disconnect($con);
		die;
 	}
	else if ($operation==2)   // Delete Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//if(str_replace("'","",$update_id)=="")
		$update_id=str_replace("'","",$update_id);
		//echo "10**$update_id"; die;
		$flag=1;
		$issue_trans_check_id=sql_select("select a.id from trims_item_transfer_dtls a  where a.mst_id=$update_id  and  a.status_active=1 and a.is_deleted=0  group by  a.id");
			
	   $no_of_issue_ids=count($issue_trans_check_id);
	   
	  // echo "10**".$no_of_issue_ids."sdsd"; die;
		
		 $field_array_update="status_active*is_deleted*updated_by*update_date";
		 $data_array_update="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
 		 $rID=$rID2=true;
		
		 
				if(1==$no_of_issue_ids)
				{	
 					$type=1;	 
 					$rID=sql_update("trims_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
					if($rID) $flag=1; else $flag=0;
					$rID2=sql_update("trims_item_transfer_dtls",$field_array_update,$data_array_update,"id",$update_dtls_id,1);
					if($flag==1) 
					{
						if($rID2) $flag=1; else $flag=0; 
					}
  				}
				else
				{
 					$type=2;	 
				    $rID2=sql_update("trims_item_transfer_dtls",$field_array_update,$data_array_update,"id",$update_dtls_id,1);
					if($flag==1) 
					{
						if($rID2) $flag=1; else $flag=0; 
					}
 				}
				
		 
		 
		 
		 $txt_from_order_id=str_replace("'","",$txt_from_order_id);
		 $txt_system_id=str_replace("'","",$txt_system_id);
		
 			 
			//echo "10**$flag";oci_rollback($con);disconnect($con);die;
			if($db_type==0)
			{
				if($flag==1)
				{
					mysql_query("COMMIT");  
					echo "2**".$update_id."**".$txt_system_id."**".$txt_from_order_id."**".$type;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$update_id."**".$txt_system_id."**".$txt_from_order_id."**".$type;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{
					oci_commit($con);   
					echo "2**".$update_id."**".$txt_system_id."**".$txt_from_order_id."**".$type;
				}
				else
				{
					oci_rollback($con);
					echo "10**".$update_id."**".$txt_system_id."**".$txt_from_order_id."**".$type;
				}
			}	
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
	 
	}
}


if ($action=="trims_store_order_to_order_transfer_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	 $mst_sql="select id, transfer_prefix, transfer_prefix_number, 
   transfer_system_id, company_id, challan_no, 
   transfer_date from trims_item_transfer_mst where id='$data[1]' and company_id='$data[0]'";
	$dataArray=sql_select($mst_sql);
	
	
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	
	  $sql_dtls="select id, mst_id, company_id ,from_order_id, from_order_no, from_party_id, from_received_id, from_job_id, from_job_dtls_id, to_order_id, to_order_no, to_party_id, to_received_id, to_job_id, to_job_dtls_id, entry_form, section_id, sub_section_id, trim_group_id, uom, color_id, size_id, item_description, quantity,remarks from trims_item_transfer_dtls where mst_id='$data[1]'  and status_active =1 and is_deleted =0";
	$sql_result= sql_select($sql_dtls);
	
	//$from_received_id=$sql_result[0][csf('from_received_id')];
	//$to_received_id=$sql_result[0][csf('to_received_id')];
	
	//$from_job_dtls_id=$sql_result[0][csf('from_job_dtls_id')];
	//$to_job_dtls_id=$sql_result[0][csf('to_job_dtls_id')];
	$from_order_no=$sql_result[0][csf('from_order_no')];
	$to_order_no=$sql_result[0][csf('to_order_no')];
	//echo $to_received_id; die;
	
	 // $from_sql_mst="select a.id, a.trims_job, a.party_id, a.received_no, a.received_id, b.order_no, b.order_id ,b.within_group from trims_job_card_mst a , subcon_ord_mst b where a.received_id=b.id and b.id='$from_received_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
 	 // $fromNameArray=sql_select($from_sql_mst); 
	
	//$to_sql_mst="select a.id, a.trims_job, a.party_id, a.received_no, a.received_id, b.order_no, b.order_id ,b.within_group from trims_job_card_mst a , subcon_ord_mst b where a.received_id=b.id and b.id='$to_received_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
 	//$toNameArray=sql_select($to_sql_mst);
	
	
	      $from_sql_date="select b.delivery_date,a.job_no_mst from subcon_ord_dtls a,subcon_ord_mst b where a.mst_id=b.id  and b.order_no='$from_order_no' and  a.status_active=1 and  a.is_deleted=0  and  b.status_active=1 and  b.is_deleted=0 group by b.delivery_date,a.job_no_mst";
 	  $fromDateNameArray=sql_select($from_sql_date); 
	
	  $to_sql_date="select b.delivery_date,a.job_no_mst from subcon_ord_dtls a,subcon_ord_mst b where a.mst_id=b.id  and b.order_no='$to_order_no' and  a.status_active=1 and  a.is_deleted=0  and  b.status_active=1 and  b.is_deleted=0 group by b.delivery_date,a.job_no_mst";
 	  $toDateNameArray=sql_select($to_sql_date);
	 
	 
	
?>
<div style="width:1130px;">
    <table width="1100" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">  
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
					?>
						Plot No: <? echo $result['plot_no']; ?> 
						Level No: <? echo $result['level_no']?>
						Road No: <? echo $result['road_no']; ?> 
						Block No: <? echo $result['block_no'];?> 
						City No: <? echo $result['city'];?> 
						Zip Code: <? echo $result['zip_code']; ?> 
						Province No: <?php echo $result['province'];?> 
						Country: <? echo $country_arr[$result['country_id']]; ?><br> 
						Email Address: <? echo $result['email'];?> 
						Website No: <? echo $result['website'];
					}
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Report</u></strong></td>
        </tr>
        <tr>
        	<td width="185"><strong>Transfer ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
            <td width="185"><strong>Transfer Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
            <td width="185"><strong>Challan No.:</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
        </tr>
        <tr>
            <td><strong>From order No:</strong></td> <td width="175px"><? echo  $sql_result[0][csf('from_order_no')]; ?></td>
            <td><strong>From Order Receive no:</strong></td> <td width="175px"><? echo $fromDateNameArray[0][csf('job_no_mst')]; ?></td>
            <td><strong>From party:</strong></td> <td width="175px"><? echo $buyer_library[$sql_result[0][csf('from_party_id')]]; ?></td>   
        </tr>
        <tr>
            <td><strong>To Work order No:</strong></td> <td width="175px"><? echo $sql_result[0][csf('to_order_no')];?></td>
            <td><strong>To Order Receive no:</strong></td> <td width="175px"><? echo $toDateNameArray[0][csf('job_no_mst')]; ?></td>
            <td><strong>To party:</strong></td> <td width="175px"><? echo $buyer_library[$sql_result[0][csf('to_party_id')]]; ?></td>
        </tr>
        <tr>
            <td><strong>From Order Delv. Date:</strong></td> <td width="175px"><? echo change_date_format($fromDateNameArray[0][csf('delivery_date')]); ?></td>
            <td><strong>To Order Delv. Date:</strong></td> <td width="175px"><? echo change_date_format($toDateNameArray[0][csf('delivery_date')]); ?></td>
        </tr>
    </table>
        <br>
    <div style="width:100%;">
    <table align="right" cellspacing="0" width="1100"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="120" >Section</th>
            <th width="100" >Sub Section</th>
            <th width="120" >Trims Group</th>
            <th width="300" >Item Description</th>
            <th width="100" >Item Color</th>
            <th width="120" >Item Size</th>
            <th width="80" >UOM</th>
            <th>Transfered Qnty</th>
        </thead>
        <tbody> 
   
<?
	//$sql_dtls="select id, item_category, item_group, from_prod_id, transfer_qnty, uom from inv_item_transfer_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";
	
	$group_arr=return_library_array( "select id, item_name from lib_item_group where status_active=1 and item_category=4", "id", "item_name");
	$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from  lib_size",'id','size_name');
	
	 
	$i=1;
	foreach($sql_result as $row)
	{
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			
			$transfer_qnty=$row[csf('quantity')];
			$transfer_qnty_sum += $transfer_qnty;
			
		?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td align="center"><? echo $i; ?></td>
                <td><? echo $trims_section[$row[csf("section_id")]]; ?></td>
                <td><? echo $trims_sub_section[$row[csf("sub_section_id")]]; ?></td>
                <td align="center"><? echo $group_arr[$row[csf("trim_group_id")]]; ?></td>
                <td align="center"><? echo $row[csf("item_description")]; ?></td>
                <td align="center"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
                <td align="center"><? echo $size_arr[$row[csf("size_id")]]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
                <td align="right"><? echo number_format($row[csf("quantity")],2); ?></td>
			</tr>
			<? $i++; } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8" align="right"><strong>Total :</strong></td>
                <td align="right"><?php echo number_format($transfer_qnty_sum,2); ?></td>
            </tr>                           
        </tfoot>
      </table>
        <br>
		 <?
            //echo signature_table(24, $data[0], "900px");
         ?>
      </div>
   </div>   
 <?
 exit();	
}
?>

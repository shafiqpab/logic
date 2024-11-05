<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.yarns.php');
require_once('../../../includes/class4/class.conversions.php');
require_once('../../../includes/class4/class.trims.php');
require_once('../../../includes/class4/class.emblishments.php');
require_once('../../../includes/class4/class.commisions.php');
require_once('../../../includes/class4/class.commercials.php');
require_once('../../../includes/class4/class.others.php');
require_once('../../../includes/class4/class.washes.php');
require_once('../../../includes/class4/class.fabrics.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];

//if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------

$user_name = $_SESSION['logic_erp']["user_id"];
$buyer_library=return_library_array( "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0","id","buyer_name");
$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$brand_arr=return_library_array( "select id,brand_name from lib_buyer_brand", "id", "brand_name"  );//*lib_buyer_brand*
$supplier_arr=return_library_array( "select id, short_name from lib_supplier", "id", "short_name");
$supplier_array=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team",'id','team_leader_name');
$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
$season_library=return_library_array( "select id,season_name from lib_buyer_season", "id", "season_name"  );
	


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );
	exit();
}

if ($action=="order_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $type;
	
	 
	  

	
	?>
	<script>
		function set_checkvalue(){
			if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
			else document.getElementById('chk_job_wo_po').value=0;
		}
		function js_set_value( str ){
			var data=str.split("_");
		//	alert(data[3]);
			document.getElementById('selected_job').value=data[0];
			document.getElementById('selected_year').value=data[1];
			document.getElementById('selected_company').value=data[2];
			document.getElementById('txt_job_id').value=data[3];

			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
         <input type="hidden" id="selected_job">
         <input type="hidden" id="selected_year">
         <input type="hidden" id="selected_company">
          <input type="hidden" id="txt_job_id">
            <table width="950" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
                    <tr>
                        <th colspan="12"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                    </tr>
                    <tr>
                        <th width="120">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="80">Job No</th>
                        <th width="80">Style Ref </th>
                        <th width="80">Internal Ref</th>
                        <th width="80">File No</th>
                        <th width="80">Order No</th>
                        <th width="80" colspan="2">Shipment Date</th>
                        <th width="80"><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                       
                     </tr>
                </thead>
                <tr class="general">
                    <td><? echo create_drop_down( "cbo_company_mst", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company_name,"load_drop_down( 'cad_bom_actual_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                    <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:70px"></td>
                   
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+'<? echo $type?>', 'create_po_search_list_view', 'search_div', 'cad_bom_actual_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="12" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
                <div id="search_div"></div>
            </form>
        </div>
    </body>
     <script>
        load_drop_down( 'cad_bom_actual_report_controller', <? echo $company_name;?>, 'load_drop_down_buyer', 'buyer_td' );
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}
			
if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);
	 
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	$date_cond="";
	if(str_replace("'","",$data[2])!="" && str_replace("'","",$data[3])!="")
	{
	 	if($db_type==0)
		{
			$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[5]";
			$start_date=change_date_format(str_replace("'","",$data[2]),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$data[3]),"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$data[5]";
			$start_date=change_date_format(str_replace("'","",$data[2]),"","",1);
			$end_date=change_date_format(str_replace("'","",$data[3]),"","",1);
		}
	   $date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
	}

	$typeId= $data[11];

	 
	 
	$internal_ref = str_replace("'","",$data[9]); $internal_ref_cond="";$file_no_cond="";
	$file_no = str_replace("'","",$data[10]);
	
	$order_cond=""; $job_cond=""; $style_cond="";
	//echo  $data[8];die;
	if($data[6]==1)
	{
		if ((str_replace("'","",$data[6])!="") && (str_replace("'","",$data[4])!="")) $job_cond=" and a.job_no_prefix_num='$data[4]'  $year_cond";
		if (trim($data[7])!="") $order_cond=" and b.po_number='$data[7]'  "; //else  $order_cond="";
		if (trim($data[8])!="") $style_cond=" and a.style_ref_no='$data[8]'  "; //else  $style_cond="";
		if ($internal_ref!="")  $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' ";
		if ($file_no!="")  $file_no_cond=" and b.file_no ='".trim($file_no)."' ";
	}
	else if($data[6]==4 || $data[6]==0)
	{
		if ((str_replace("'","",$data[6])!="") && (str_replace("'","",$data[4])!="")) $job_cond=" and a.job_no like '%$data[4]%'  $year_cond"; //else  $job_cond="";
		if (trim($data[7])!="") $order_cond=" and b.po_number like '%$data[7]%'  ";
		if (trim($data[8])!="") $style_cond=" and a.style_ref_no like '%$data[8]%'  "; //else  $style_cond="";
		if ($internal_ref!="")  $internal_ref_cond=" and b.grouping like '%".trim($internal_ref)."%' ";
		if ($file_no!="")  $file_no_cond=" and b.file_no like '%".trim($file_no)."%' ";
		//echo $internal_ref_cond.'FGG';
	}
	else if($data[6]==2)
	{
		if ((str_replace("'","",$data[6])!="") && (str_replace("'","",$data[4])!="")) $job_cond=" and a.job_no like '$data[4]%'  $year_cond"; //else  $job_cond="";
		if (trim($data[7])!="") $order_cond=" and b.po_number like '$data[7]%'  ";
		if (trim($data[8])!="") $style_cond=" and a.style_ref_no like '$data[8]%'  "; //else  $style_cond="";
		if ($internal_ref!="")  $internal_ref_cond=" and b.grouping like '".trim($internal_ref)."%' ";
		if ($file_no!="")  $file_no_cond=" and b.file_no like '".trim($file_no)."%' ";
	}
	else if($data[6]==3)
	{
		if ((str_replace("'","",$data[6])!="") && (str_replace("'","",$data[4])!="")) $job_cond=" and a.job_no like '%$data[4]'  $year_cond"; //else  $job_cond="";
		if (trim($data[7])!="") $order_cond=" and b.po_number like '%$data[7]'  ";
		if (trim($data[8])!="") $style_cond=" and a.style_ref_no like '%$data[8]'  "; //else  $style_cond="";
		if ($internal_ref!="")  $internal_ref_cond=" and b.grouping like '%".trim($internal_ref)."' ";
		if ($file_no!="")  $file_no_cond=" and b.file_no like '%".trim($file_no)."' ";
	}
	 

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (2=>$comp,3=>$buyer_arr,9=>$item_category);
		
	$sql="select a.id,a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.id as po_id,b.po_number,sum(b.po_quantity) as  po_quantity,b.shipment_date,a.garments_nature,b.grouping,b.file_no,(pub_shipment_date - po_received_date) as  date_diff,to_char(a.insert_date,'YYYY') as year from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and c.job_no_mst=a.job_no and c.po_break_down_id=b.id  and a.status_active=1 and b.status_active=1  $company $buyer ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_cond $order_cond $style_cond $shiping_status $file_no_cond $internal_ref_cond $date_cond $budget_version_cond group by a.id,a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.id, b.po_number,b.shipment_date,a.garments_nature,b.grouping,b.file_no,a.insert_date,b.pub_shipment_date,b.po_received_date order by a.job_no DESC";
	 
	$result=sql_select($sql);
	foreach ($result as $row)
	{
		
		$style_po_arr[$row[csf("job_no")]]["job_id"]= $row[csf("id")];
		$style_po_arr[$row[csf("job_no")]]["job_prefix"]= $row[csf("job_no_prefix_num")];
		$style_po_arr[$row[csf("job_no")]]["job_no"]= $row[csf("job_no")];
		$style_po_arr[$row[csf("job_no")]]["po_number"].= $row[csf("po_number")].',';
		$style_po_arr[$row[csf("job_no")]]["company_name"]= $row[csf("company_name")];
		$style_po_arr[$row[csf("job_no")]]["buyer_name"]= $row[csf("buyer_name")];
		$style_po_arr[$row[csf("job_no")]]["style_ref_no"]= $row[csf("style_ref_no")];
		$style_po_arr[$row[csf("job_no")]]["garments_nature"]= $row[csf("garments_nature")];
		$style_po_arr[$row[csf("job_no")]]["year"]= $row[csf("year")];
		$style_po_arr[$row[csf("job_no")]]["job_quantity"]= $row[csf("job_quantity")];
		$style_po_arr[$row[csf("job_no")]]["po_quantity"]+= $row[csf("po_quantity")];
		$style_po_arr[$row[csf("job_no")]]["shipment_date"]=$row[csf("shipment_date")];// $row[csf("ex_factory_date")];
		$style_po_arr[$row[csf("job_no")]]["grouping"].= $row[csf("grouping")].',';
		$style_po_arr[$row[csf("job_no")]]["date_diff"]+= $row[csf("date_diff")];
		$style_po_arr[$row[csf("job_no")]]["file_no"].= $row[csf("file_no")].',';
				
	}
	//print_r($style_po_arr);
	?>
	<div>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1180" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="80">	Job No</th>
			<th width="60">Year</th>
			<th width="120">Company</th>
			<th width="110">Buyer</th>
			<th width="110">Style Ref. No</th>
			<th width="100">Gmts Nature</th>
			<th width="80">Job Qty.</th>
			<th width="100">PO number</th>
			<th width="80">PO Qty</th>
			<th width="70"><? echo "Shipment Date";?></th>
			<th width="70">Ref no</th>
			<th width="70">File no</th>
			<th>Lead time</th>
		</thead>
	</table>
	<div style="width:1180px; max-height:270px;overflow-y:scroll;" >
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1160" class="rpt_table" id="list_view">
		<?

		$k=1;
		foreach($style_po_arr as $job_no=>$row)
		{
			if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		//	$exchange_rate=$exchange_rate_arr[$job_no];
			if($typeId==1)
			{
				$style_data=$row[("job_prefix")].'_'.$row[("year")].'_'.$row[('company_name')].'_'.$row[("job_id")];
			}
			else
			{
				$style_data=$row[("style_ref_no")].'_'.$row[("year")].'_'.$row[('company_name')].'_'.$row[("job_id")];
			}
			$po_number=rtrim($row[("po_number")],",");
			$po_number=implode(",",array_unique(explode(",",$po_number)));
			$grouping=rtrim($row[("grouping")],",");
			$grouping=implode(",",array_unique(explode(",",$grouping)));
			$file_no=rtrim($row[("file_no")],",");
			$file_no=implode(",",array_unique(explode(",",$file_no)));

		?>
			<tr bgcolor="<? echo $bgcolor;?>"   style="cursor:pointer;" id="tr_<? echo $k; ?>" onClick="js_set_value('<? echo $style_data; ?>');change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')">
                <td width="30"> <? echo $k; ?></td>
                <td width="80"><? echo $row[('job_prefix')]; ?></td>
                <td width="60"><? echo $row[('year')];; ?></td>
                <td width="120"><? echo $comp[$row[('company_name')]];//$garments_item[$row[csf('company_name')]]; ?></td>
                <td width="110"><p><? echo $buyer_arr[$row[('buyer_name')]]; ?></p></td>
                <td width="110"><p><? echo $row[('style_ref_no')]; ?></p></td>
                <td width="100"><? echo $item_category[$row[('garments_nature')]]; ?></p></td>
                <td width="80"><? echo $row[('job_quantity')]; ?></td>
                <td width="100"><p><? echo $po_number; ?></p></td>
                <td width="80"><? echo $row[('po_quantity')]; ?></td>
                <td width="70"><p><? echo change_date_format($row[('shipment_date')]); ?></td>
                <td width="70"><p><? echo $grouping; ?></p></td>
                <td width="70"><p><? echo $file_no; ?></p></td>
                <td width=""><? echo $row[('date_diff')]; ?></td>
			</tr>
			<?
			$k++;
		}
			?>
		</table>
	</div>
    </div>
    <?
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//echo str_replace("'","",$cbo_costcontrol_source);die;

		$company_name=str_replace("'","",$cbo_company_name);
		$buyer_id=str_replace("'","",$cbo_buyer_name);
		$job_no=str_replace("'","",$txt_job_no);
		$cbo_year=str_replace("'","",$cbo_year);
		$txt_job_id=str_replace("'","",$txt_job_id);
		if($job_no!="") $job_cond="and a.job_no_prefix_num in($job_no)";else $job_cond="";
		if($txt_job_id!="") $job_idcond="and a.id in($txt_job_id)";else $job_idcond="";
		if($buyer_id!=0) $buyers_cond="and a.buyer_name in('$buyer_id')";else $buyers_cond="";
		
		if($company_name==0){ echo "Select Company"; die; }
		//if($job_no==''){ echo "Select Job"; die; }
		if($cbo_year==0){ echo "Select Year"; die; }
	
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";

		$sql="select a.id as job_id,a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.brand_id,a.product_dept,a.team_leader,a.dealing_marchant,a.season_year,a.season,a.style_description,a.requisition_no from wo_po_details_master a where a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 $job_cond $job_idcond $buyers_cond $year_cond";
		$result=sql_select($sql);

		$jobNumber=""; $buyerName=""; $styleRefno=""; 
		$JobIdArr=array(); $JobNoArr=array(); $StyleNoArr=array();
		foreach($result as $row){
			$jobNumber=$row[csf('job_no')];
			 
			$JobIdArr[$row[csf('job_id')]]=$row[csf('job_id')];
			$JobNoArr[$row[csf('job_no')]]=$row[csf('job_no')];
			$StyleNoArr[$row[csf('style_ref_no')]]=$row[csf('style_ref_no')];

			$job_wiseArr[$row[csf('job_id')]]['job_no']=$row[csf('job_no')];
			$job_wiseArr[$row[csf('job_id')]]['buyer']=$buyer_arr[$row[csf('buyer_name')]];
			$job_wiseArr[$row[csf('job_id')]]['brand']=$brand_arr[$row[csf("brand_id")]];
			$job_wiseArr[$row[csf('job_id')]]['product']=$product_dept[$row[csf("product_dept")]];
			$job_wiseArr[$row[csf('job_id')]]['team']=$team_leader_arr[$row[csf("team_leader")]];
			$job_wiseArr[$row[csf('job_id')]]['dealing']=$marchentrArr[$row[csf("dealing_marchant")]];
			$job_wiseArr[$row[csf('job_id')]]['season']=$season_library[$row[csf('season')]];
			$job_wiseArr[$row[csf('job_id')]]['season_year']=$row[csf('season_year')];
			$job_wiseArr[$row[csf('job_id')]]['style']=$row[csf('style_ref_no')];
			$job_wiseArr[$row[csf('job_id')]]['description']=$row[csf('style_description')];
			$job_wiseArr[$row[csf('job_id')]]['requisition_no']=$row[csf('requisition_no')];
		}

		$sql_details= "select a.style_ref_no,b.gmts_item_id,(case when c.bulletin_type=2 then c.id else 0 end) as mkt_id,(case when c.bulletin_type=2 then c.tot_mc_smv else 0 end) as mkt_smv,(case when c.bulletin_type=4 then c.id else 0 end) as prod_id,(case when c.bulletin_type=4 then c.tot_mc_smv else 0 end) as prod_smv,d.sew_smv from wo_po_details_master a left join wo_pre_cost_mst d ON a.id = d.job_id and d.status_active=1 and d.is_deleted=0, wo_po_details_mas_set_details b,ppl_gsd_entry_mst c where a.id=b.job_id and b.gmts_item_id=c.gmts_item_id and a.style_ref_no=c.style_ref and  a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $job_cond $job_idcond $buyers_cond $year_cond";

		$result_details=sql_select($sql_details);
		$item_wiseArr=array();
		foreach($result_details as $row){
			$item_wiseArr[$row[csf('gmts_item_id')]]['gmts_item']=$garments_item[$row[csf('gmts_item_id')]];
			$item_wiseArr[$row[csf('gmts_item_id')]]['system_mkt']=$row[csf('mkt_id')];
			$item_wiseArr[$row[csf('gmts_item_id')]]['smv_mkt']=$row[csf("mkt_smv")];
			$item_wiseArr[$row[csf('gmts_item_id')]]['system_prod']=$row[csf('prod_id')];
			$item_wiseArr[$row[csf('gmts_item_id')]]['smv_prod']=$row[csf("prod_smv")];
			$item_wiseArr[$row[csf('gmts_item_id')]]['sew_smv']=$row[csf("sew_smv")];
		}
		
		$sql_cad_part= "select b.id as yarn_count_id,b.fabric_ref, b.type, b.construction, b.design, e.shrinkage_l, e.shrinkage_w, d.body_part_id, b.gsm_weight, b.weight_type, e.cuttable_width,e.full_width, e.fabric_cons,e.wastageper,e.final_cons,c.system_no,f.style_ref_no from wo_po_details_master a,lib_yarn_count_determina_mst b,consumption_la_costing_mst c , consumption_la_costing_dtls d,consumption_la_fabric_dtls e ,sample_development_mst f where a.style_ref_no=f.style_ref_no and f.id=c.inquiry_id and c.id= d.mst_id and d.id=dtls_id and d.yarn_count_id=b.id and d.id=e.dtls_id and  a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $job_cond $job_idcond $buyers_cond $year_cond group by  b.id,b.fabric_ref, b.type, b.construction, b.design, e.shrinkage_l, e.shrinkage_w, d.body_part_id, b.gsm_weight, b.weight_type, e.cuttable_width,e.full_width, e.fabric_cons,e.wastageper,e.final_cons,c.system_no,f.style_ref_no order by c.system_no,b.id";
		//echo $sql_cad_part;die;
		$result_cad_part=sql_select($sql_cad_part);
 		$cad_wiseArr=array();
		foreach($result_cad_part as $row){
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['system_no']=$row[csf('system_no')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['yarn_count_id']=$row[csf('yarn_count_id')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['fabric_ref']=$row[csf('fabric_ref')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['type']=$row[csf('type')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['construction']=$row[csf('construction')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['design']=$row[csf('design')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['shrinkage_l']=$row[csf('shrinkage_l')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['shrinkage_w']=$row[csf('shrinkage_w')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['body_part_id']=$lib_body_part[$row[csf('body_part_id')]];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['gsm_weight']=$row[csf('weight_type')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['weight_type']=$fabric_weight_type[$row[csf('weight_type')]];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['composition']=$composition_arr[$row[csf('yarn_count_id')]];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['cuttable_width']=$row[csf('cuttable_width')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['full_width']=$row[csf('full_width')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['fabric_cons']=$row[csf('fabric_cons')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['wastageper']=$row[csf('wastageper')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['final_cons']=$row[csf('final_cons')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['style_ref_no']=$row[csf('style_ref_no')];
/* 			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['bud_yarn_count_id']=$budget_wiseArr[$row[csf('yarn_count_id')]]['bud_yarn_count_id'];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['bud_fabric_ref']=$budget_wiseArr[$row[csf('yarn_count_id')]]['bud_fabric_ref'];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['bud_type']=$budget_wiseArr[$row[csf('yarn_count_id')]]['bud_type'];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['bud_construction']=$budget_wiseArr[$row[csf('yarn_count_id')]]['bud_construction'];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['bud_design']=$budget_wiseArr[$row[csf('yarn_count_id')]]['bud_design'];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['bud_shrinkage_l']=$budget_wiseArr[$row[csf('yarn_count_id')]]['bud_shrinkage_l'];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['bud_shrinkage_w']=$budget_wiseArr[$row[csf('yarn_count_id')]]['bud_shrinkage_w'];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['bud_body_part_id']=$budget_wiseArr[$row[csf('yarn_count_id')]]['bud_body_part_id'];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['bud_gsm_weight']=$budget_wiseArr[$row[csf('yarn_count_id')]]['bud_gsm_weight'];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['bud_weight_type']=$budget_wiseArr[$row[csf('yarn_count_id')]]['bud_weight_type'];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['bud_cutable_width']=$budget_wiseArr[$row[csf('yarn_count_id')]]['bud_cutable_width'];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['bud_full_width']=$budget_wiseArr[$row[csf('yarn_count_id')]]['bud_full_width'];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['bud_consump']+=$budget_wiseArr[$row[csf('yarn_count_id')]]['bud_cons'];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['bud_loss']=$budget_wiseArr[$row[csf('yarn_count_id')]]['bud_process_loss'];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('yarn_count_id')]]['bud_requre']+=$budget_wiseArr[$row[csf('yarn_count_id')]]['bud_requ']; */

		} 

		
		$sql_budget_part= "select c.lib_yarn_count_deter_id as count_id,b.id as bid,b.fabric_ref, b.type, b.construction, b.design, b.shrinkage_l, b.shrinkage_w, c.body_part_id, b.gsm_weight, b.weight_type, b.cutable_width,b.full_width, SUM(d.cons) as cons,AVG(d.process_loss_percent) as process_loss,SUM(d.requirment) as requ,e.system_no from wo_po_details_master a,lib_yarn_count_determina_mst b,wo_pre_cost_fabric_cost_dtls c,wo_pre_cos_fab_co_avg_con_dtls d,consumption_la_costing_mst e , consumption_la_costing_dtls f,sample_development_mst g,consumption_la_fabric_dtls h where a.id=c.job_id and c.id=d.pre_cost_fabric_cost_dtls_id and a.id= d.job_id and b.id=c.lib_yarn_count_deter_id and a.style_ref_no=g.style_ref_no and g.id=e.inquiry_id and e.id= f.mst_id and f.id=h.dtls_id and  a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $job_cond $job_idcond $buyers_cond $year_cond group by  c.lib_yarn_count_deter_id,b.fabric_ref, b.type, b.construction, b.design, b.shrinkage_l, b.shrinkage_w, c.body_part_id, b.gsm_weight, b.weight_type, b.cutable_width,b.full_width,e.system_no,b.id order by e.system_no,c.lib_yarn_count_deter_id,b.id";
		//echo $sql_budget_part;die;
		$result_budget_part=sql_select($sql_budget_part);
		$budget_wiseArr=array();
		foreach($result_budget_part as $row){
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('bid')]][deter_id][$row[csf('count_id')]]['bud_yarn_count_id']=$row[csf('count_id')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('bid')]][deter_id][$row[csf('count_id')]]['bud_fabric_ref']=$row[csf('fabric_ref')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('bid')]][deter_id][$row[csf('count_id')]]['bud_type']=$row[csf('type')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('bid')]][deter_id][$row[csf('count_id')]]['bud_construction']=$row[csf('construction')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('bid')]][deter_id][$row[csf('count_id')]]['bud_design']=$row[csf('design')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('bid')]][deter_id][$row[csf('count_id')]]['bud_shrinkage_l']=$row[csf('shrinkage_l')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('bid')]][deter_id][$row[csf('count_id')]]['bud_shrinkage_w']=$row[csf('shrinkage_w')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('bid')]][deter_id][$row[csf('count_id')]]['bud_body_part_id']=$row[csf('body_part_id')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('bid')]][deter_id][$row[csf('count_id')]]['bud_gsm_weight']=$row[csf('gsm_weight')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('bid')]][deter_id][$row[csf('count_id')]]['bud_weight_type']=$row[csf('weight_type')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('bid')]][deter_id][$row[csf('count_id')]]['bud_cutable_width']=$row[csf('cutable_width')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('bid')]][deter_id][$row[csf('count_id')]]['bud_full_width']=$row[csf('full_width')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('bid')]][deter_id][$row[csf('count_id')]]['bud_cons']=$row[csf('cons')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('bid')]][deter_id][$row[csf('count_id')]]['bud_process_loss']=$row[csf('process_loss')];
			$cad_wiseArr[$row[csf('system_no')]][$row[csf('bid')]][deter_id][$row[csf('count_id')]]['bud_requ']=$row[csf('requ')];
		}
		
		
		     /*  echo '<pre>';
		print_r($cad_wiseArr);die;   */  
 
	
		if($jobNumber=="")
		{
			echo "<div style='width:1000px;font-size:larger'; align='center'><font color='#FF0000'>No Data Found</font></div>"; die;
		}
		ob_start();
		if(count($result) > 0) {

		?>
		<table class="rpt_table" width="900" cellpadding="0" cellspacing="0" border="1" rules="all" style="margin-top: 10px">
			<thead>
				<tr>
					<th width="150">Buyer/Brand</th>
					<th width="100">Prod. Dept</th>
					<th width="100">Team Name</th>
					<th width="100">Dealing Merchant</th>
					<th width="100">Job No</th>
					<th width="100">Season/Year</th>
					<th width="150">Style Ref/Desc</th>
					<th width="100">Sample Req. No</th>				
				</tr>
			</thead>
			<tbody>
				<?
					foreach ($job_wiseArr as $job_id=>$value){ ?>
						<td align="center"><?= $value['buyer']."/".$value['brand']; ?></td>
						<td align="center"><?= $value['product']; ?></td>
						<td align="center"><?= $value['team']; ?></td>
						<td align="center"><?= $value['dealing']; ?></td>
						<td align="center"><?= $value['job_no']; ?></td>
						<td align="center"><?= $value['season']."/".$value['season_year']; ?></td>
						<td align="center"><?= $value['style']."/".$value['description']; ?></td>
						<td align="center"><?= $value['requisition_no']; ?></td>
						<?
					}
				?>
			</tbody>
		</table>
		<br><br>
	<?
		}

		if(count($result_details) > 0) {
			?>
			<table class="rpt_table" width="900" cellpadding="0" cellspacing="0" border="1" rules="all" style="margin-top: 10px">
				<thead>
					<tr>
						<th colspan="3"></th>
						<th colspan="2">Sample VS Budget Stage</th>
						<th colspan="2">Production VS Actual Stage</th>
					</tr>
					<tr>
						<th width="140">GMT Item</th>
						<th width="130">MKT GSD ID</th>
						<th width="130">Prod. GSD ID</th>
						<th width="125">WS</th>
						<th width="125">BOM</th>
						<th width="125">WS</th>
						<th width="125">Production</th>		
					</tr>
				</thead>
				<tbody>
					<?php
						$tsl=1;
					?>
					<?
						foreach ($item_wiseArr as $item_id=>$value){ ?>
							<td align="center"><?= $value['gmts_item']; ?></td>
							<td align="center"><?= $value['system_mkt']; ?></td>
							<td align="center"><?= $value['system_prod']; ?></td>
							<td align="right"><?= $value['smv_mkt']; ?></td>
							<td align="right"><?= $value['sew_smv']; ?></td>
							<td align="right"><?= $value['smv_prod']; ?></td>
							<td align="right"></td>
							<?
							$total_mst_smv+=$value['smv_mkt'];
							$total_budget_smv+=$value['sew_smv'];
							$total_prod_smv+=$value['smv_prod'];
							$tsl++;
						}
					?>
				</tbody>
				<tfoot>
					<tr style="font-size: 20px; font-weight: bold;">
						<td colspan="3" align="right">Total SMV:</td>
						<td align="right"><?= fn_number_format($total_mst_smv,2);  ?></td>
						<td align="right"><?= fn_number_format($total_budget_smv,2);  ?></td>
						<td align="right"><?= fn_number_format($total_prod_smv,2);  ?></td>
						<td align="right"></td>
					</tr>
			</tfoot>
			</table>
			<br><br>
		<?
		}
		?>
			<table class="rpt_table" width="3350" cellpadding="0" cellspacing="0" border="1" rules="all" style="margin-top: 10px">
				<thead>
					<tr>
						<? if(count($result_cad_part) > 0) {?>
						<th width="30" rowspan="2">Sl</th>
						<th width="100" rowspan="2">CAD System No</th>
						<th width="100" rowspan="2">Pattern Master Name</th>
						<th colspan="8">Sample Requisition Fabric Information</th>
						<th colspan="7">CAD Information</th>
						<? } ?>
						<th colspan="15">Budget Fabric Information</th>
					</tr>
					<tr>
					<? if(count($result_cad_part) > 0) {?>
						<th width="100">Fab. Sys Code</th>
						<th width="100">Fabric Ref</th>
						<th width="100">Fabric Type</th>	
						<th width="100">Fabric Description</th>
						<th width="100">Fab. Cons</th>	
						<th width="100">Design</th>
						<th width="100">Shrinkage l</th>
						<th width="100">Shrinkage W</th>	
						<th width="100">Fabric Usage</th>
						<th width="100">F.Weight</th>	
						<th width="100">Cad.Cut Width</th>
						<th width="100">Budget.Cut Width</th>
						<th width="100">Finish Cons [Yds]</th>	
						<th width="100">Wast. %</th>
						<th width="100">Final Cons [Yds]</th>	
						<? } ?>
 						<th width="100">Fab. Sys Code</th>
						<th width="100">Fabric Ref</th>
						<th width="100">Fabric Type</th>	
						<th width="100">Fabric Description</th>
						<th width="100">Fab. Cons</th>	
						<th width="100">Design</th>
						<th width="100">Shrinkage l</th>
						<th width="100">Shrinkage w</th>	
						<th width="100">Fabric Usage</th>
						<th width="100">F.Weight</th>	
						<th width="100">Cad.Cut Width</th>
						<th width="100">Budget.Cut Width</th>
						<th width="100">Finish Cons [Yds]</th>	
						<th width="100">Wast. %</th>
						<th width="100">Final Cons [Yds]</th>
					</tr>
				</thead>
				<tbody>
					<?php
					
						$csl=1;
						  foreach ($cad_wiseArr as $system_id=>$system_data){ 
							foreach ($system_data as $yarn_id=>$value){ 
								$k=1;
								/* foreach ($yarn_data as $dtls_id=>$value){  */
										
							?>
							<tr>
							<td align="center"><?= $csl; ?></td>
							<td align="center"><?= $value['system_no']; ?></td>
							<td align="center"><?= $value['style_ref_no']; ?></td>
							<td align="center"><?= $value['yarn_count_id']; ?></td>
							<td align="center"><?= $value['fabric_ref']; ?></td>
							<td align="center"><?= $value['type']; ?></td>
							<td align="center"><?= $value['construction']; ?></td>
							<td align="center"><?= $value['composition']; ?></td>
							<td align="center"><?= $value['design']; ?></td>
							<td align="center"><?= $value['shrinkage_l']; ?></td>
							<td align="center"><?= $value['shrinkage_w']; ?></td>
							<td align="center"><?= $value['body_part_id']; ?></td>
							<td align="center"><?= $value['gsm_weight']." ".$value['weight_type']; ?></td>
							<td align="center"><?= $value['cuttable_width']; ?></td>
							<td align="center"><?= $value['full_width']; ?></td>
							<td align="center"><?= $value['fabric_cons']; ?></td>
							<td align="center"><?= $value['wastageper']; ?></td>
							<td align="center"><?= $value['final_cons']; ?></td>
							<?
							foreach($value[deter_id] as $key_id=>$row)
                            {	
								if($k==1)
								{
								?>

									<td align="center"><?= $row['bud_yarn_count_id']; ?></td>
									<td align="center"><?= $row['bud_fabric_ref']; ?></td>
									<td align="center"><?= $row['bud_type']; ?></td>
									<td align="center"><?= $row['bud_construction']; ?></td>
									<td align="center"><?= $row['bud_construction']; ?></td>
									<td align="center"><?= $row['bud_design']; ?></td>
									<td align="center"><?= $row['bud_shrinkage_l']; ?></td>
									<td align="center"><?= $row['bud_shrinkage_w']; ?></td>
									<td align="center"><?= $row['bud_body_part_id']; ?></td>
									<td align="center"><?= $row['bud_gsm_weight']." ".$row['bud_weight_type']; ?></td>
									<td align="center"><?= $row['bud_cutable_width']; ?></td>
									<td align="center"><?= $row['bud_full_width']; ?></td>
									<td align="center"><?= $row['bud_cons']; ?></td>
									<td align="center"><?= $row['bud_process_loss']; ?></td>
									<td align="center"><?= $row['bud_requ']; ?></td>
                                    

                   
                        <?	
						}
						  else
						{
						?>
           
								<td align="center"><?= $row['bud_yarn_count_id']; ?></td>
								<td align="center"><?= $row['bud_fabric_ref']; ?></td>
								<td align="center"><?= $row['bud_type']; ?></td>
								<td align="center"><?= $row['bud_construction']; ?></td>
								<td align="center"><?= $row['bud_construction']; ?></td>
								<td align="center"><?= $row['bud_design']; ?></td>
								<td align="center"><?= $row['bud_shrinkage_l']; ?></td>
								<td align="center"><?= $row['bud_shrinkage_w']; ?></td>
								<td align="center"><?= $row['bud_body_part_id']; ?></td>
								<td align="center"><?= $row['bud_gsm_weight']." ".$row['bud_weight_type']; ?></td>
								<td align="center"><?= $row['bud_cutable_width']; ?></td>
								<td align="center"><?= $row['bud_full_width']; ?></td>
								<td align="center"><?= $row['bud_cons']; ?></td>
								<td align="center"><?= $row['bud_process_loss']; ?></td>
								<td align="center"><?= $row['bud_requ']; ?></td>
						
						<?
						} 
						?>
						</tr><? 
						$k++;
						
					}
							
					$csl++;
							
							
						
								 // }
							}
						} 

					?>
				</tbody>
			</table>
			<br><br>
		<?
	

	
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_name."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename"; 
	
	exit();
}
?>
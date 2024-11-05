<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

$salse_order_search = array(1=>'Sales order No',2=>'Sales/Booking No',3=>'Style Ref. No');
$knit_defect_array2=array(1=>"Fly Conta",2=>"PP conta",3=>"Patta/Barrie",4=>"Needle Mark",5=>"Sinker Mark",6=>"thick-thin",7=>"neps/knot",8=>"white speck",9=>"Black Speck",10=>"Star Mark",11=>"Dia/Edge Mark",12=>"Dead fibre",13=>"Running shade",14=>"Hairiness",15=>"crease mark",16=>"Uneven",17=>"Padder Crease",18=>"Absorbency",19=>"Bowing",20=>"Handfeel",21=>"Dia Up-down",22=>"Cut hole",23=>"Snagging/Pull out",24=>"Pin Hole",25=>"Bad Smell",26=>"Bend Mark");
$knit_defect_inchi_array2=array(1=>"Select",2=>"Present",3=>"Not Found",4=>"Major",5=>"Minor",6=>"Acceptable",7=>"Good");

function yds_to_kg($length,$width,$gsm){
	$farbic_length_inch = $length*36;
	$yds_to_kg = ($farbic_length_inch*$width*$gsm)/1550000;
	return number_format($yds_to_kg,3);
}

if ($action=="load_drop_down_buyer")
{
	if($data != 0)
	{
	echo create_drop_down( "cbo_po_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
	}
	else{
		echo create_drop_down( "cbo_po_buyer_name", 120,$blank_array,"", 1, "-- Select Buyer --", 0, "" );
		exit();
	}
}

if ($action=="salse_order_popup")
{
  	echo load_html_head_contents("Salse Order popup","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value( salse_order)
		{
			var data=salse_order.split("_");
			document.getElementById('salse_order_id').value=data[0];
			document.getElementById('salse_order_no').value=data[1];
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
    <table width="600" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
        <thead>
            <tr>
                <th width="120">Within Group</th>
                <th width="100">Year</th>
                <th width="160">Search By</th>
                <th width="160">Search Text</th>
                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
            </tr>
        </thead>
        <tr class="general">
            <td>
            	<input type="hidden" id="salse_order_no">
            	<input type="hidden" id="salse_order_id">
                <? echo create_drop_down("cbo_within_group", 120, $yes_no, "", 0, "--  --", 0,""); ?>
            </td>
            <td><? echo create_drop_down( "cbo_year", 100, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
            <td><? echo create_drop_down( "cbo_search_by", 160, $salse_order_search,'', 0, "-- --",0,""); ?></td>
            <td><input type="text" style="width:130px" class="text_boxes" id="cbo_search_text"></td>
            <td align="center" >
            	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('cbo_search_by').value+'_'+'<? echo $cbo_company_name; ?>'+'_'+document.getElementById('cbo_search_text').value, 'create_salse_order_list_view', 'search_div', 'finishing_qc_analysis_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        </tr>
    </table>
    </form>
    	<div id="search_div"></div>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="create_salse_order_list_view")
{
	$data=explode('_',$data);

	$within_group = $data[0]; $year = $data[1]; $search_by = $data[2]; $company_id = $data[3]; $search_string = trim($data[4]);
	$year_field = "";

	if ($within_group != 0) $within_group_cond=" and within_group='$within_group'"; else $within_group_cond="";
	if ($company_id != '') $company_cond=" and company_id='$company_id'"; else $company_cond="";

	if($db_type==0)
	{
		if ($year != 0) $year_cond  = ""; else $year_cond ="";
		$year_field = "YEAR(booking_date) as year";
	}
	if($db_type==2)
	{
		if ($year != 0) $year_cond  = "and extract( year from booking_date ) = $year"; else $year_cond ="";
		$year_field = "to_char(booking_date,'YYYY') as year";
	}

	if($search_string != ''){
		if ($search_by == 1) {
			$search_field_cond = " and job_no like '%" . $search_string . "'";
		} else if ($search_by == 2) {
			$search_field_cond = " and sales_booking_no like '%" . $search_string . "'";
		} else {
			$search_field_cond = " and style_ref_no like '" . $search_string . "%'";
		}
	}

	$buyer_arr = return_library_array("SELECT id, short_name from lib_buyer", 'id', 'short_name');
	$arr=array (3=>$buyer_arr);
	$sql = "SELECT id, $year_field, job_no_prefix_num, job_no, within_group, sales_booking_no, booking_date, po_buyer, style_ref_no, location_id from fabric_sales_order_mst where status_active=1 and is_deleted=0 and company_id=$company_id $within_group_cond $search_field_cond $year_cond order by id desc";

	echo create_list_view("list_view", "Sales order No,Year,Booking No,Buyer,Style Ref. No", "120,60,100,100,150","600","260",0, $sql , "js_set_value", "id,job_no", "", 1, "0,0,0,po_buyer,0", $arr , "job_no,year,sales_booking_no,po_buyer,style_ref_no", "",'') ;
	exit();
}
if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$fso_all_condition="";
	$batch_con ="";$yarn_information = '';
	$fso_master_array = array();
	//$batch_qty_array = array();

	$cbo_company=str_replace("'","",$cbo_company_name);
	$within_group=str_replace("'","",$cbo_within_group);
	$po_company_name=str_replace("'","",$cbo_po_company_name);
	$po_buyer_name=str_replace("'","",$cbo_po_buyer_name);
	$sales_order_no=str_replace("'","",$cbo_sales_order_no);
	$sales_order_id=str_replace("'","",$cbo_sales_order_id);
	$cbo_year=str_replace("'","",$cbo_year);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);

	if($cbo_company_name)$fso_all_condition.=" and a.company_id = $cbo_company";
	if($within_group !=0){$fso_all_condition.=" and a.within_group = $within_group";} else {$fso_all_condition.=" and a.within_group in (1,2)";}
	if($po_company_name){
		if($within_group ==1){
			$fso_all_condition.=" and a.po_company_id = $po_company_name";
		}
		elseif ($within_group ==2) {
			$fso_all_condition.=" and a.company_id = $po_company_name";
		}
		else{
			$fso_all_condition.=" and (a.po_company_id = $po_company_name or a.company_id = $po_company_name)";
		}
	}

	if($po_buyer_name){
		if($within_group ==1){
			$fso_all_condition.=" and a.po_buyer = $po_buyer_name";
		}
		elseif ($within_group ==2) {
			$fso_all_condition.=" and a.buyer_id = $po_buyer_name";
		}
		else{
			$fso_all_condition.=" and (a.buyer_id = $po_buyer_name or a.po_buyer = $po_buyer_name)";
		}
	}


	if($sales_order_no)$fso_all_condition.=" and a.job_no = '$sales_order_no'";
	if($sales_order_id)$fso_all_condition.=" and a.id = '$sales_order_id'";
	if($sales_order_id)$batch_con=" and a.sales_order_id = '$sales_order_id'";


	if($txt_date_from && $txt_date_to)
    {
        if($db_type==0)
        {
            $date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
            $date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
            $fso_all_condition.="and  g.receive_date between '$date_from' and '$date_to'";

        }
        if($db_type==2)
        {
            $date_from=change_date_format($txt_date_from,'','',1);
            $date_to=change_date_format($txt_date_to,'','',1);
            $fso_all_condition.=" and  g.receive_date between '$date_from' and '$date_to'";

        }
    }

	$company_library=return_library_array("SELECT id,company_name from lib_company", "id", "company_name");
	$yarn_count=return_library_array("SELECT id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$buyer_arr=return_library_array( "SELECT id, buyer_name from lib_buyer",'id','buyer_name');
	$season_arr=return_library_array( "SELECT id, season_name from lib_buyer_season",'id','season_name');
	$body_part_arr=return_library_array("SELECT id, body_part_type from lib_body_part","id","body_part_type");
	$defect_short_arr = return_library_array("SELECT DEFECT_NAME, short_name from  lib_defect_name", "DEFECT_NAME", "short_name");
	$yarncount = return_library_array("SELECT id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');
	$brand_name_arr = return_library_array("SELECT id, brand_name from  lib_brand", 'id', 'brand_name');
	$yarn_type_from_prod = return_library_array("SELECT id,yarn_type from  product_details_master where item_category_id=1 ", "id", "yarn_type");



	/*echo '<pre>';
	print_r($batch_qty_array); die;*/
	$batch_load_unload = array();
	$qc_data_arr = array();
	$qc_data_arr2 = array();
	$composition_arr=array();


	$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
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




	$fso_master_query = "SELECT a.id as sales_order_id, a.job_no, a.company_id, a.within_group, a.sales_booking_no, a.booking_id, a.booking_date, a.delivery_date, a.buyer_id, a.style_ref_no, a.season, a.po_buyer, a.po_job_no,
		a.po_company_id, a.booking_type, e.body_part_id, e.fabric_description_id, e.gsm, e.width, d.batch_no, d.extention_no,
		d.id as batch_id,f.length_percent,f.width_percent,f.actual_gsm, e.receive_qnty,
		e.reject_qty, e.uom, e.mst_id as rcv_id, e.remarks, e.id as pro_dtls_id, g.receive_date,g.id as recv_id, sum(f.roll_no) as roll_no, f.qc_date

		from fabric_sales_order_mst a
		join pro_batch_create_mst d on a.id = d.sales_order_id
		join pro_finish_fabric_rcv_dtls e on e.batch_id = d.id
		join pro_qc_result_mst f on e.id=f.pro_dtls_id
		join inv_receive_master g on e.mst_id = g.id

		where g.entry_form=7 and a.is_deleted=0 and a.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 and g.is_deleted=0 and g.status_active=1 $fso_all_condition
		group by  a.id, a.job_no, a.company_id, a.within_group, a.sales_booking_no, a.booking_id, a.booking_date, a.delivery_date, a.buyer_id, a.style_ref_no, a.season, a.po_buyer, a.po_job_no,
		a.po_company_id, a.booking_type, e.body_part_id, e.fabric_description_id, e.gsm, e.width, d.batch_no, d.extention_no,
		d.id,f.length_percent,f.width_percent,f.actual_gsm, e.receive_qnty,
		e.reject_qty, e.uom, e.mst_id, e.remarks, e.id, g.receive_date,g.id,f.qc_date order by a.id";//$fso_all_condition
	//echo $fso_master_query; die;

	$attribute = array('sales_order_id', 'job_no', 'company_id', 'within_group', 'sales_booking_no', 'booking_id', 'booking_date', 'delivery_date', 'buyer_id', 'style_ref_no', 'season', 'po_buyer', 'po_job_no', 'pro_dtls_id', 'po_company_id', 'booking_type', 'body_part_id', 'fabric_description_id', 'gsm', 'width', 'batch_no', 'extention_no','batch_id','length_percent','width_percent','actual_gsm','receive_qnty','recv_id','uom','reject_qty','remarks','receive_date','roll_no','qc_date');

	$fso_master_data = sql_select($fso_master_query);
	if(count($fso_master_data)>0){
		foreach ($fso_master_data as $row) {
			$type_composition = explode(',', $composition_arr[$row[csf('fabric_description_id')]]);
			$sub_key = $row[csf('batch_id')].'*'.$row[csf('body_part_id')].'*'.$row[csf('fabric_description_id')].'*'.$row[csf('uom')];
			$key = $row[csf('job_no')].'*'.$row[csf('sales_booking_no')].'*'.$row[csf('po_buyer')].'*'.$row[csf('style_ref_no')].'*'.$row[csf('season')].'*'.$row[csf('body_part_id')].'*'.$row[csf('gsm')].'*'.$row[csf('width')].'*'.$row[csf('batch_no')].'*'.$row[csf('extention_no')].'*'.$row[csf('fabric_description_id')].'*'.$row[csf('uom')].'*'.$row[csf('qc_date')];
			foreach ($attribute as $attr) {
				$fso_master_array[$key][$attr] = $row[csf($attr)];
			}
			//$fso_master_array[$key]['pro_dtls'][$row[csf('pro_dtls_id')]] = $row[csf('pro_dtls_id')];
			$fso_master_array[$key]['fabric_type'] = trim($type_composition[0]);
			$fso_master_array[$key]['fabric_composition'] = trim($type_composition[1]);
			$fso_master_array[$key]['yarn_information'] =$yarn_information;
			$fso_master_array[$key]['finish_roll'] +=1;
			$batch_id_arr[$row[csf('batch_id')]] = $row[csf('batch_id')];
			$pro_dtls_id[$row[csf('pro_dtls_id')]] = $row[csf('pro_dtls_id')];
			$recv_id[$row[csf('recv_id')]] = $row[csf('recv_id')];
			if($row[csf('uom')] == 27){
				$yds_to_kg_arr[$sub_key][] = yds_to_kg($row[csf('length_percent')],$row[csf('width_percent')],$row[csf('actual_gsm')]);
			}


		}
	}

	$shrinkage_data = sql_select("SELECT a.length_percent, a.width_percent, a.twisting_percent, b.fabric_description_id,b.body_part_id,b.batch_id,b.uom  from pro_qc_result_mst a join pro_finish_fabric_rcv_dtls b on b.id = a.pro_dtls_id  where pro_dtls_id in (".implode(',', $pro_dtls_id).") and a.status_active =1 and a.is_deleted=0 group by a.pro_dtls_id, b.fabric_description_id, b.body_part_id,b.batch_id,b.uom, a.length_percent, a.width_percent, a.twisting_percent");

	foreach ($shrinkage_data as $row) {
		$key = $row[csf('batch_id')].'*'.$row[csf('body_part_id')].'*'.$row[csf('fabric_description_id')].'*'.$row[csf('uom')];

		if($row[csf('length_percent')] != ''){
			$shrinkage_arr[$key]['length_percent'][] = $row[csf('length_percent')];
		}
		if($row[csf('width_percent')] != ''){
			$shrinkage_arr[$key]['width_percent'][] = $row[csf('width_percent')];
		}
		if($row[csf('twisting_percent')] != ''){
			$shrinkage_arr[$key]['twisting_percent'][] = $row[csf('twisting_percent')];
		}
	}


	$dyeing_load_unload = sql_select("SELECT production_date, entry_form, load_unload_id, end_hours, end_minutes, process_end_date, batch_id, batch_ext_no From pro_fab_subprocess where entry_form in (35,48,33) and batch_id in (".implode(',', $batch_id_arr).") and status_active=1 and is_deleted=0");

	foreach ($dyeing_load_unload as $row) {
		$batch_load_unload[$row[csf('batch_id')]][$row[csf('batch_ext_no')]][$row[csf('load_unload_id')]][$row[csf('entry_form')]]['process_end_date'] = change_date_format($row[csf('process_end_date')],'mm-dd-yyyy','/');
		$batch_load_unload[$row[csf('batch_id')]][$row[csf('batch_ext_no')]][$row[csf('load_unload_id')]][$row[csf('entry_form')]]['hours_min'] = $row[csf('end_hours')].':'.$row[csf('end_minutes')];
		$batch_load_unload[$row[csf('batch_id')]][$row[csf('batch_ext_no')]][$row[csf('load_unload_id')]][$row[csf('entry_form')]]['pro_date'] = $row[csf('production_date')];
	}


	$min_max_qc = sql_select("SELECT b.fabric_description_id,b.body_part_id,b.batch_id,b.uom ,min(a.actual_dia) as min_dia, max(a.actual_dia) as max_dia, min(a.actual_gsm) as min_gsm, max(a.actual_gsm) as max_gsm from pro_qc_result_mst a join pro_finish_fabric_rcv_dtls b on b.id = a.pro_dtls_id  where pro_dtls_id in (".implode(',', $pro_dtls_id).") and a.status_active =1 and a.is_deleted=0 group by b.fabric_description_id,b.body_part_id,b.batch_id,b.uom");

	foreach ($min_max_qc as $row) {
		$key = $row[csf('batch_id')].'*'.$row[csf('body_part_id')].'*'.$row[csf('fabric_description_id')].'*'.$row[csf('uom')];

		if($row[csf('min_dia')]==$row[csf('max_dia')]){
			$pro_qc_min_max[$key]['min_max_dia'] = $row[csf('max_dia')];
		}
		else{
			$pro_qc_min_max[$key]['min_max_dia'] = $row[csf('min_dia')].', '.$row[csf('max_dia')];
		}
		if($row[csf('min_gsm')] == $row[csf('max_gsm')]){
			$pro_qc_min_max[$key]['min_max_gsm'] = $row[csf('max_gsm')];
		}
		else{
			$pro_qc_min_max[$key]['min_max_gsm'] = $row[csf('min_gsm')].', '.$row[csf('max_gsm')];
		}
	}
	if(!empty($pro_dtls_id))
	{
		$pro_dtls_id_nos="'".implode("','",$pro_dtls_id)."'";
		$pro_dtls_id = explode(",", $pro_dtls_id_nos);

		$all_pro_dtls_cond=""; $proDtlsCond="";
		if($db_type==2 && count($pro_dtls_id)>999)
		{
			$all_search_book_arr_chunk=array_chunk($pro_dtls_id,999) ;
			foreach($all_search_book_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$proDtlsCond.="  pro_dtls_id in($chunk_arr_value) or ";
			}

			$all_pro_dtls_cond.="  (".chop($proDtlsCond,'or ').")";
		}
		else
		{
			$all_pro_dtls_cond="  pro_dtls_id in($pro_dtls_id_nos)";
		}
	}

	$avg_qc_value = sql_select("SELECT a.pro_dtls_id,b.fabric_description_id,b.body_part_id,b.batch_id,b.uom, b.receive_qnty, b.fabric_shade ,b.reject_qty, b.gsm, b.width  
	from pro_qc_result_mst a join pro_finish_fabric_rcv_dtls b on b.id = a.pro_dtls_id  
	where $all_pro_dtls_cond and a.status_active =1 and a.is_deleted=0 
	group by a.pro_dtls_id, b.fabric_description_id, b.body_part_id, b.batch_id, b.uom, b.receive_qnty, b.fabric_shade, b.reject_qty, b.gsm, b.width ");

	foreach ($avg_qc_value as $row) 
	{
		$key = $row[csf('batch_id')].'*'.$row[csf('body_part_id')].'*'.$row[csf('fabric_description_id')].'*'.$row[csf('uom')].'*'.$row[csf('gsm')].'*'.$row[csf('width')];
		$production_qty_data[$key]['rcv_qty'][$row[csf('fabric_shade')]] = $row[csf('receive_qnty')];
		$production_qty_data[$key]['reject_qty'][$row[csf('fabric_shade')]] = $row[csf('reject_qty')];
	}
	// echo "<pre>";print_r($production_qty_data);

	$yarn_Information_sql = "SELECT a.id as batch_id, b.batch_qnty, b.roll_no,  b.body_part_id,  LISTAGG(CAST(d.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) as yarn_lot, LISTAGG(CAST(d.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_count) as yarn_count, LISTAGG(CAST(d.brand_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.brand_id) as brand_id,LISTAGG(CAST(d.yarn_prod_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_prod_id) as yarn_prod_id, f.fabric_description_id, f.uom
		from pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e , pro_finish_fabric_rcv_dtls f
		where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.id=f.batch_id and a.company_id=$cbo_company_name and a.id in (".implode(',', $batch_id_arr).") and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
		group by b.body_part_id, b.batch_qnty, b.roll_no, a.id, f.fabric_description_id, f.uom  order by a.id";

	$yarn_Information_arr = sql_select($yarn_Information_sql);

	foreach ($yarn_Information_arr as $row){
		$key = $row[csf('batch_id')].'*'.$row[csf('body_part_id')].'*'.$row[csf('fabric_description_id')].'*'.$row[csf('uom')];
		$yarn_lot = implode(",", array_unique(explode(",", $row[csf('yarn_lot')])));
		$y_count = array_unique(explode(",", $row[csf('yarn_count')]));
		$brand_id = array_unique(explode(",", $row[csf('brand_id')]));
		$yarn_prod_id = array_unique(explode(",", $row[csf('yarn_prod_id')]));
		$yarn_count_value = "";
		foreach ($y_count as $val) {
			if ($val > 0) {
				if ($yarn_count_value == '') $yarn_count_value = $yarncount[$val]; else $yarn_count_value .= ", " . $yarncount[$val];
			}
		}
		$brand_value = "";
		foreach ($brand_id as $bid) {
			if ($bid > 0) {
				if ($brand_value == '') $brand_value = $brand_name_arr[$bid]; else $brand_value .= ", " . $brand_name_arr[$bid];
			}
		}
		$type_value = "";
		foreach ($yarn_prod_id as $tid) {
			if ($tid > 0) {
				if ($type_value == '') $type_value = $yarn_type[$yarn_type_from_prod[$tid]]; else $type_value .= ", " . $yarn_type[$yarn_type_from_prod[$tid]];
			}
		}
		$yern_info_data [$key]['yarn_info'] = $yarn_count_value.', '.$type_value.', '.$yarn_lot.', '.$brand_value;
		$batch_qty_data[$key]['batch_qnty'] += $row[csf('batch_qnty')];
		$batch_qty_data[$key]['roll_no'] += 1;
	}


	$qc_data = sql_select("SELECT a.uom,a.batch_id, a.body_part_id, a.fabric_description_id, a.mst_id as rcv_id, b.length_percent, b.width_percent, b.actual_gsm, c.defect_name, c.penalty_point, c.form_type,c.found_in_inch, a.receive_qnty,a.fabric_shade, b.comments   from pro_finish_fabric_rcv_dtls a join pro_qc_result_mst b on a.id = b.pro_dtls_id join pro_qc_result_dtls c on b.id = c.mst_id and a.batch_id in (".implode(',', $batch_id_arr).") group by a.uom,a.batch_id, a.body_part_id, a.fabric_description_id, a.mst_id, c.defect_name, c.penalty_point, c.form_type,c.found_in_inch,a.receive_qnty,b.comments, b.length_percent, b.width_percent, b.actual_gsm, a.fabric_shade order by c.defect_name asc");
	$comments_value = '';
	foreach ($qc_data as $row) {
		$key = $row[csf('batch_id')].'*'.$row[csf('body_part_id')].'*'.$row[csf('fabric_description_id')].'*'.$row[csf('uom')];

		if($row[csf('penalty_point')] !=0 && $row[csf('form_type')] != 2 && $row[csf('defect_name')]>0){
			$all_defect[$row[csf('defect_name')]] = $row[csf('defect_name')];
			$qc_data_arr[$key][$row[csf('defect_name')]]['defect_name'] = $row[csf('defect_name')];
			$qc_data_arr[$key][$row[csf('defect_name')]]['penalty_point'] += $row[csf('penalty_point')];
		}
		if($row[csf('found_in_inch')] !=1 && $row[csf('found_in_inch')] !=0 && $row[csf('form_type')] == 2){
			$qc_data_arr2[$key][$row[csf('found_in_inch')]] = $knit_defect_array2[$row[csf('defect_name')]].' '.$knit_defect_inchi_array2[$row[csf('found_in_inch')]];
		}

		$recv_qnty_arrr[$key]['rcv_qty'] = $row[csf('receive_qnty')];
		if($row[csf('comments')] != ''){
			$recv_qnty_arrr[$key]['qc_comm'][] = $row[csf('comments')];
		}

	}

	$qc_key = '';
	$max_qc='';
	foreach ($qc_data_arr as $key => $row) {
		$count = count($row);
		if($count>$max_qc){
			$qc_key = $key;
			$max_qc = $count;
		}
	}
	$form_to = '';
	if(str_replace("'","",$txt_date_from) !=''){
		$form_to = "<br> From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;
	}
	ob_start();

	?>
	<div style="width: 1930px; color: #000; margin-bottom: 10px">
        <table width="1900" cellpadding="0" cellspacing="0" id="caption" align="center">
            <tr>
               <td align="center" width="100%" colspan="6" class="form_caption" ><span style="font-size:16px; color: #000"><? echo $company_library[$cbo_company]; ?></span></td>
            </tr>
            <tr>
               <td align="center" width="100%" colspan="6" class="form_caption" ><span style="font-size:13px; color: #000"><? echo $report_title.' '.$form_to; ?></span></td>
            </tr>
        </table>
    </div>
    <? if($type ==1 ){  $calspan = count($all_defect)+1; ?>
		<fieldset style="width:4520px;">
			<table class="rpt_table" width="4500" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
				<thead>
					<tr>
						<th colspan="37">&nbsp;</th>
						<?  echo "<th colspan='".$calspan."' align='center'>Summary (Defect Name and Point)</th>"; ?>
						<th colspan="3" align='center'>Shrinkage %</th>
						<th></th>
					</tr>
					<tr>
						<th width="100">Qc Date</th>
						<th width="100">Buyer</th>
						<th width="100">Style</th>
						<th width="100">Season</th>
						<th width="150">Fabric Booking No</th>
						<th width="150">FSO NO</th>
						<th width="100">Body Part</th>
						<th width="100">Fabric Type</th>
						<th width="100">Fabric Composition</th>
						<th width="60">Req. Dia</th>
						<th width="100">Avg. Actual Dia</th>
						<th width="60">Req. GSM</th>
						<th width="100">Avg. Actual GSM</th>
						<th width="120">Yarn Information</th>
						<th width="100">Batch NO</th>
						<th width="80">EXT. No (Reprocess)</th>
						<th width="80">Batch Qty. (Kg)</th>
						<th width="80">Production Qty. (Kg)</th>
						<th width="80">EXT. Qty (Kg) Reprocess</th>
						<th width="80">Production Qty. (Yds)</th>
						<th width="80">EXT. Qty (Yds)</th>
						<th width="80">Reject Qty. (Kg)</th>
						<th width="60">Yds To Kg</th>
						<th width="80">Dyeing Unload Date</th>
						<th width="80">Dyeing Loading Time</th>
						<th width="80">Dyeing unloadingTime</th>
						<th width="80">Stentering End Date</th>
						<th width="80">Stentering End Time</th>
						<th width="80">Compacting end Date</th>
						<th width="80">Compacting end Time</th>
						<th width="100">Execution Days From Dyeing</th>
						<th width="100">Execution Days From Finishing</th>
						<th width="80">Finishing Prod. date</th>
						<th width="80">Grey No of Roll</th>
						<th width="80">Finish No of Roll</th>
						<th width="80">Process Loss %</th>
						<th width="100">Remarks</th>
						<th width="100">Inspection Comments</th>
						<? foreach ($all_defect as $key => $value) { ?>
							<th width="40"><? echo $defect_short_arr[$value] ?></th>
						<? } ?>
						<th width="60">Length(%)</th>
						<th width="60">Width(%)</th>
						<th width="60">Twisting(%)</th>
						<th>QC Comments</th>
					</tr>
				</thead>
				<tbody id="table_body">
					<?	$qc_match_key = ''; $qc_match_key_production = ''; $process_per = ''; $i=1;
					if(count($fso_master_array)>0)
					{
						foreach ($fso_master_array as $value) 
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$link_data = $cbo_company."*".$value['booking_id']."*".$value['sales_booking_no']."*".$value['job_no'];

							$qc_match_key_production = $value['batch_id'].'*'.$value['body_part_id'].'*'.$value['fabric_description_id'].'*'.$value['uom'].'*'.$value['gsm'].'*'.$value['width'];
							$qc_match_key = $value['batch_id'].'*'.$value['body_part_id'].'*'.$value['fabric_description_id'].'*'.$value['uom'];
							$batch_qty = $batch_qty_data[$qc_match_key]['batch_qnty'];
							?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td align="center"> <? echo change_date_format($value['qc_date']); ?></td>
									<td><?
										if($value['within_group'] == 1){
											echo $buyer_arr[$value['po_buyer']];
										}
										if($value['within_group'] == 2){
											echo $buyer_arr[$value['buyer_id']];
										}

									?></td>
									<td><? echo $value['style_ref_no'] ?></td>
									<td><? echo $value['season'] ?></td>
									<td><? echo $value['sales_booking_no'] ?></td>
									<td><a href="##" onclick="fso_report_generate('<? echo $link_data ?>','<? echo $value['within_group'] ?>')"><? echo $value['job_no'] ?></td>
									<td><? echo $body_part[$value['body_part_id']] ?></td>
									<td><? echo $value['fabric_type'] ?></td>
									<td><? echo $value['fabric_composition'] ?></td>
									<td><? echo $value['width'] ?></td>
									<td><? echo $pro_qc_min_max[$qc_match_key]['min_max_dia'] ?></td>
									<td><? echo $value['gsm'] ?></td>
									<td><? echo $pro_qc_min_max[$qc_match_key]['min_max_gsm'] ?></td>
									<td><? echo $yern_info_data[$qc_match_key]['yarn_info'] ?></td>
									<td><? echo $value['batch_no'] ?></td>
									<td><? echo $value['extention_no'] ?></td>
									<td align="right"><? echo number_format($batch_qty,2); ?></td>
									<? if($value['extention_no'] != '' && $value['uom'] == 12){ ?>
									<td></td>
									<td align="right"><?
										//echo $recv_qnty_arrr[$qc_match_key]['rcv_qty'];
										echo number_format(array_sum($production_qty_data[$qc_match_key_production]['rcv_qty']),2);
										$total_ext_production_qty_kg += array_sum($production_qty_data[$qc_match_key_production]['rcv_qty']);
									 ?></td>
									<? } elseif($value['extention_no'] == '' && $value['uom'] == 12){ ?>
									<td align="right"><?
										//echo $recv_qnty_arrr[$qc_match_key]['rcv_qty'];
										echo number_format(array_sum($production_qty_data[$qc_match_key_production]['rcv_qty']),2);
										$total_production_qty_kg += array_sum($production_qty_data[$qc_match_key_production]['rcv_qty']);
									?></td>
									<td></td>
									<? } else{ ?>
										<td></td>
										<td></td>
									<? } ?>
									<? if($value['extention_no'] != '' && $value['uom']==27){ ?>
									<td></td>
									<td align="right"><?
										echo number_format(array_sum($production_qty_data[$qc_match_key_production]['rcv_qty']),2);
										$total_ext_production_qty_yds += array_sum($production_qty_data[$qc_match_key_production]['rcv_qty']);
									?></td>
									<? } elseif($value['extention_no'] == '' && $value['uom']==27){ ?>
									<td align="right"><?
										echo number_format(array_sum($production_qty_data[$qc_match_key_production]['rcv_qty']),2);
										$total_production_qty_yds += array_sum($production_qty_data[$qc_match_key_production]['rcv_qty']);
										?>
									</td>
									<td></td>
									<? } else { ?>
										<td></td>
										<td></td>
									<? } ?>
									<td align="right"><?
									  if(array_sum($production_qty_data[$qc_match_key_production]['reject_qty']) !=0)echo array_sum($production_qty_data[$qc_match_key_production]['reject_qty']);
									  $total_reject_qty += array_sum($production_qty_data[$qc_match_key_production]['reject_qty']);
									 ?></td>

									<? if($value['uom']==27){ ?>
										<td align="right"><?
										$product_qty = array_sum($production_qty_data[$qc_match_key_production]['rcv_qty']);
										echo yds_to_kg($product_qty,$value['width'],$value['gsm']);
										?></td>
									<? } else { echo '<td></td>';  } ?>

									<td align="right"><? echo $batch_load_unload[$value['batch_id']][$value['extention_no']][2][35]['process_end_date'] ?></td>
									<td align="right"><? echo $batch_load_unload[$value['batch_id']][$value['extention_no']][1][35]['hours_min'] ?></td>
									<td align="right"><? echo $batch_load_unload[$value['batch_id']][$value['extention_no']][2][35]['hours_min'] ?></td>
									<td align="right"><? echo $batch_load_unload[$value['batch_id']][$value['extention_no']][0][48]['process_end_date'] ?></td>
									<td align="right"><? echo $batch_load_unload[$value['batch_id']][$value['extention_no']][0][48]['hours_min'] ?></td>
									<td align="right"><? echo $batch_load_unload[$value['batch_id']][$value['extention_no']][0][33]['process_end_date'] ?></td>
									<td align="right"><? echo $batch_load_unload[$value['batch_id']][$value['extention_no']][0][33]['hours_min'] ?></td>
									<td align="right"><? echo datediff('y',$batch_load_unload[$value['batch_id']][$value['extention_no']][2][35]['process_end_date'],$value['receive_date']); ?></td>
									<?
										if($batch_load_unload[$value['batch_id']][$value['extention_no']][0][48]['process_end_date']){
											$execution_days = datediff('y',$batch_load_unload[$value['batch_id']][$value['extention_no']][0][48]['process_end_date'],$value['receive_date']);
										}
										else{
											$execution_days = datediff('y',$batch_load_unload[$value['batch_id']][$value['extention_no']][0][33]['process_end_date'],$value['receive_date']);
										}
									?>
									<td align="right"><? echo $execution_days; ?></td>
									<td align="right"><? echo change_date_format($value['receive_date']); ?></td>
									<td align="right"><?
										echo $batch_qty_data[$qc_match_key]['roll_no'];
										$total_gray_no_roll += $batch_qty_data[$qc_match_key]['roll_no'];
									?></td>
									<td align="right"><?
										echo $value['finish_roll'];
										$total_finish_no_roll += $value['finish_roll'];
									 ?></td>
									<td align="right">
										<?

											if($batch_qty != '' and $value['uom'] != 27){
												$process_per = ($batch_qty-(array_sum($production_qty_data[$qc_match_key_production]['rcv_qty'])+array_sum($production_qty_data[$qc_match_key_production]['reject_qty'])))/$batch_qty;
											}
											if($batch_qty != '' and $value['uom'] == 27){
												$product_qty = array_sum($production_qty_data[$qc_match_key_production]['rcv_qty']);
										        $rcv_yds_qty = yds_to_kg($product_qty,$value['width'],$value['gsm']);
												$process_per = ($batch_qty-($rcv_yds_qty+array_sum($production_qty_data[$qc_match_key_production]['reject_qty'])))/$batch_qty;
											}
											if($process_per != ''){
												echo number_format($process_per*100,2).'%';
											}

										?>
									</td>
									<td><? echo $value['remarks']; ?></td>
									<td><?
										echo implode(", ", array_unique($recv_qnty_arrr[$qc_match_key]['qc_comm']));
									//echo $recv_qnty_arrr[$qc_match_key]['qc_comm']; ?></td>
									<?
										foreach ($all_defect as $main_key => $row) {
											echo '<td align="center">'.$qc_data_arr[$qc_match_key][$main_key]['penalty_point'].'</td>';
											$all_defect_sum[$main_key] += $qc_data_arr[$qc_match_key][$main_key]['penalty_point'];
										}
									?>
									<td><? echo implode(', ', $shrinkage_arr[$qc_match_key]['length_percent']) ?></td>
									<td><? echo implode(', ', $shrinkage_arr[$qc_match_key]['width_percent']) ?></td>
									<td><? echo implode(', ', $shrinkage_arr[$qc_match_key]['twisting_percent']) ?></td>
									<td><? echo implode(', ', $qc_data_arr2[$qc_match_key]) ?></td>

								</tr>

							<?
							$i++;
						}
							/*echo '<pre>';
							print_r($all_defect);*/
						?>
							<tr>
								<td colspan="17" align="right"><strong>Total</strong></td>
								<td align="right"><? echo number_format($total_production_qty_kg,2); ?></td>
								<td align="right"><? echo number_format($total_ext_production_qty_kg,2); ?></td>
								<td align="right"><? echo number_format($total_production_qty_yds,2); ?></td>
								<td align="right"><? echo $total_ext_production_qty_yds; ?></td>
								<td align="right"><? echo $total_reject_qty; ?></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td align="right"><? echo $total_gray_no_roll; ?></td>
								<td align="right"><? echo $total_finish_no_roll; ?></td>
								<td></td>
								<td></td>
								<td></td>
								<?
									foreach ($all_defect as $main_key => $row) {
										echo '<td align="center">'.$all_defect_sum[$main_key].'</td>';
									}
								?>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
						<?
					}
					?>
				</tbody>
			</table>
		</fieldset>
	<? } ?>
	<? if($type ==2 ){ $calspan = count($all_defect); ?>
		<fieldset style="width:3500px;">
			<table class="rpt_table" width="3500" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
				<thead>
					<tr>
						<th colspan="24">&nbsp;</th>
						<?  echo "<th colspan='".$calspan."' align='center'>Summary (Defect Name and Point)</th>"; ?>
						<th colspan="3" align='center'>Shrinkage %</th>
						<th></th>
					</tr>
					<tr>
						<th width="100">Buyer</th>
						<th width="100">Style</th>
						<th width="100">Season</th>
						<th width="150">Fabric Booking No</th>
						<th width="150">FSO NO</th>
						<th width="100">Body Part</th>
						<th width="100">Fabric Type</th>
						<th width="100">Fabric Composition</th>
						<th width="60">Req. Dia</th>
						<th width="100">Avg. Actual Dia</th>
						<th width="60">Req. GSM</th>
						<th width="100">Avg. Actual GSM</th>
						<th width="120">Yarn Information</th>
						<th width="100">Batch NO</th>
						<th width="80">EXT. No (Reprocess)</th>
						<th width="80">Batch Qty. (Kg)</th>
						<th width="80">Production Qty. (Kg)</th>
						<th width="80">EXT. Qty (Kg) Reprocess</th>
						<th width="80">Production Qty. (Yds)</th>
						<th width="80">EXT. Qty (Yds)</th>
						<th width="80">Reject Qty. (Kg)</th>
						<th width="60">Yds To Kg</th>
						<th width="80">Finish No of Roll</th>
						<th width="80">Process Loss %</th>
						<? foreach ($all_defect as $key => $value) { ?>
							<th width="40"><? echo $defect_short_arr[$value] ?></th>
						<? } ?>
						<th width="60">Length(%)</th>
						<th width="60">Width(%)</th>
						<th width="60">Twisting(%)</th>
						<th>QC Comments</th>
					</tr>
				</thead>
				<tbody>
					<?	$qc_match_key = ''; $qc_match_key_production = ''; $process_per = ''; $i=1;
					if(count($fso_master_array)>0)
					{
						foreach ($fso_master_array as $value) 
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$link_data = $cbo_company."*".$value['booking_id']."*".$value['sales_booking_no']."*".$value['job_no'];

							$qc_match_key_production = $value['batch_id'].'*'.$value['body_part_id'].'*'.$value['fabric_description_id'].'*'.$value['uom'].'*'.$value['gsm'].'*'.$value['width'];
							$qc_match_key = $value['batch_id'].'*'.$value['body_part_id'].'*'.$value['fabric_description_id'].'*'.$value['uom'];
							$batch_qty = $batch_qty_data[$qc_match_key]['batch_qnty'];
							?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td><?
										if($value['within_group'] == 1){
											echo $buyer_arr[$value['po_buyer']];
										}
										if($value['within_group'] == 2){
											echo $buyer_arr[$value['buyer_id']];
										}

									?></td>
									<td><? echo $value['style_ref_no'] ?></td>
									<td><? echo $value['season'] ?></td>
									<td><? echo $value['sales_booking_no'] ?></td>
									<td><a href="##" onclick="fso_report_generate('<? echo $link_data ?>','<? echo $value['within_group'] ?>')"><? echo $value['job_no'] ?></td>
									<td><? echo $body_part[$value['body_part_id']] ?></td>
									<td><? echo $value['fabric_type'] ?></td>
									<td><? echo $value['fabric_composition'] ?></td>
									<td><? echo $value['width'] ?></td>
									<td><? echo $pro_qc_min_max[$qc_match_key]['min_max_dia'] ?></td>
									<td><? echo $value['gsm'] ?></td>
									<td><? echo $pro_qc_min_max[$qc_match_key]['min_max_gsm'] ?></td>
									<td><? echo $yern_info_data[$qc_match_key]['yarn_info'] ?></td>
									<td><? echo $value['batch_no'] ?></td>
									<td><? echo $value['extention_no'] ?></td>
									<td align="right"><? echo number_format($batch_qty,2); ?></td>
									<? if($value['extention_no'] != '' && $value['uom'] == 12){ ?>
									<td></td>
									<td align="right"><?
										//echo $recv_qnty_arrr[$qc_match_key]['rcv_qty'];
										echo number_format(array_sum($production_qty_data[$qc_match_key_production]['rcv_qty']),2);
										$total_ext_production_qty_kg += array_sum($production_qty_data[$qc_match_key_production]['rcv_qty']);
									 ?></td>
									<? } elseif($value['extention_no'] == '' && $value['uom'] == 12){ ?>
									<td align="right"><?
										//echo $recv_qnty_arrr[$qc_match_key]['rcv_qty'];
										echo number_format(array_sum($production_qty_data[$qc_match_key_production]['rcv_qty']),2);
										$total_production_qty_kg += array_sum($production_qty_data[$qc_match_key_production]['rcv_qty']);
									?></td>
									<td></td>
									<? } else{ ?>
										<td></td>
										<td></td>
									<? } ?>
									<? if($value['extention_no'] != '' && $value['uom']==27){ ?>
									<td></td>
									<td align="right"><?
										echo number_format(array_sum($production_qty_data[$qc_match_key_production]['rcv_qty']),2);
										$total_ext_production_qty_yds += array_sum($production_qty_data[$qc_match_key_production]['rcv_qty']);
									?></td>
									<? } elseif($value['extention_no'] == '' && $value['uom']==27){ ?>
									<td align="right"><?
										echo number_format(array_sum($production_qty_data[$qc_match_key_production]['rcv_qty']),2);
										$total_production_qty_yds += array_sum($production_qty_data[$qc_match_key_production]['rcv_qty']);
										?>
									</td>
									<td></td>
									<? } else { ?>
										<td></td>
										<td></td>
									<? } ?>
									<td align="right"><?
									  if(array_sum($production_qty_data[$qc_match_key_production]['reject_qty']) !=0)echo array_sum($production_qty_data[$qc_match_key_production]['reject_qty']);
									  $total_reject_qty += array_sum($production_qty_data[$qc_match_key_production]['reject_qty']);
									 ?></td>

									<? if($value['uom']==27){ ?>
										<td align="right"><?
										$product_qty = array_sum($production_qty_data[$qc_match_key_production]['rcv_qty']);
										echo yds_to_kg($product_qty,$value['width'],$value['gsm']);
										?></td>
									<? } else { echo '<td></td>';  } ?>
									<td align="right"><?
										echo $value['finish_roll'];
										$total_finish_no_roll += $value['finish_roll'];
									 ?></td>
									<td align="right">
										<?

											if($batch_qty != '' and $value['uom'] != 27){
												$process_per = ($batch_qty-(array_sum($production_qty_data[$qc_match_key_production]['rcv_qty'])+array_sum($production_qty_data[$qc_match_key_production]['reject_qty'])))/$batch_qty;
											}
											if($batch_qty != '' and $value['uom'] == 27){
												$product_qty = array_sum($production_qty_data[$qc_match_key_production]['rcv_qty']);
										        $rcv_yds_qty = yds_to_kg($product_qty,$value['width'],$value['gsm']);
												$process_per = ($batch_qty-($rcv_yds_qty+array_sum($production_qty_data[$qc_match_key_production]['reject_qty'])))/$batch_qty;
											}
											if($process_per != ''){
												echo number_format($process_per*100,2).'%';
											}

										?>
									</td>
									<?
										foreach ($all_defect as $main_key => $row) {
											echo '<td align="center">'.$qc_data_arr[$qc_match_key][$main_key]['penalty_point'].'</td>';
											$all_defect_sum[$main_key] += $qc_data_arr[$qc_match_key][$main_key]['penalty_point'];
										}
									?>
									<td><? echo implode(', ', $shrinkage_arr[$qc_match_key]['length_percent']) ?></td>
									<td><? echo implode(', ', $shrinkage_arr[$qc_match_key]['width_percent']) ?></td>
									<td><? echo implode(', ', $shrinkage_arr[$qc_match_key]['twisting_percent']) ?></td>
									<td><? echo implode(', ', $qc_data_arr2[$qc_match_key]) ?></td>

								</tr>

							<?
							$i++;
						}
						?>
							<tr>
								<td colspan="16" align="right"><strong>Total</strong></td>
								<td align="right"><? echo number_format($total_production_qty_kg,2); ?></td>
								<td align="right"><? echo number_format($total_ext_production_qty_kg,2); ?></td>
								<td align="right"><? echo number_format($total_production_qty_yds,2); ?></td>
								<td align="right"><? echo $total_ext_production_qty_yds; ?></td>
								<td align="right"><? echo $total_reject_qty; ?></td>
								<td></td>
								<td align="right"><? echo $total_finish_no_roll; ?></td>
								<td></td>
								<?
									foreach ($all_defect as $main_key => $row) {
										echo '<td align="center">'.$all_defect_sum[$main_key].'</td>';
									}
								?>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
						<?
					}
					?>
				</tbody>
			</table>
		</fieldset>
	<?
	}
		/*ob_end_flush();
		$data = ob_get_contents();
		ob_end_clean();*/
	?>
	<div style="float:left; margin-top:25px; margin-left: 10px;" class="report-chart">
		<div style="width:950px; height:500px; border:solid 1px; float: left;">
			<table style="margin-left:10px; font-size:12px" align="left">
			<tr>
				<td align="left" bgcolor="#4f81bd" width="10"></td>
				<td>Summary of Defect Name and Point</td>
			</tr>
			</table>
			<div style="display: none;" id="canvas_div"></div>
			<canvas id="canvas" height="400" width="900"></canvas>
		</div>
		<div style="width:300px; height:500px;  margin-left:20px; border:solid 1px; float: left;">
			<table style="margin-left:60px; font-size:12px" align="left">
			<tr>
                <td align="left" bgcolor="#8064a2" width="10"></td>
				<td>Most Three Defect Name and Point</td>
			</tr>
			</table>
			<div style="display: none;" id="canvas2_div"></div>
			<canvas id="canvas2" height="350" width="250"></canvas>
		</div>
	</div>

	<?
	$bar_arr=array(); $val_arr=array(); $bar_arr2=array(); $val_arr2=array();



	foreach ($all_defect_sum as $key => $value) {
		$bar_arr[]= $defect_short_arr[$key];
		$val_arr[] = $value;
	}

	arsort($all_defect_sum);

	/*$get_key = array_keys($all_defect_sum);
	$firstKey = $get_key[0];
	$total_rem = count($all_defect_sum)-3;*/
	//$max_data = array_splice($all_defect_sum, 2, 3);



	foreach ($all_defect_sum as $key=>$value) {
		$defect_name_arr[] = $defect_short_arr[$key];
		$defect_point_arr[] = $value;
	}
	$bar_arr1 = array_splice($defect_name_arr, 0, 3);
	$val_arr1 = array_splice($defect_point_arr, 0, 3);


	$bar_arr= json_encode($bar_arr);
	$val_arr= json_encode($val_arr);
	$bar_arr1= json_encode($bar_arr1);
	$val_arr1= json_encode($val_arr1);
	?>
	<script>
			var barChartData = {
			labels : <? echo $bar_arr; ?>,
			datasets : [
					{
						fillColor : "#4f81bd",
						strokeColor : "rgba(220,220,220,0.8)",
						highlightFill: "rgb(255,99,71)",
						highlightStroke: "rgba(220,220,220,1)",
						data : <? echo $val_arr; ?>
					}

				]
			};
			var chartOptions = {
			    animation: false,
			    responsive : true,
			    display: false,
			    tooltipTemplate: "<%= value %>",
			    tooltipFillColor: "rgba(0,0,0,0)",
			    tooltipFontColor: "#444",
			    tooltipEvents: [],
			    tooltipCaretSize: 0,
			    onAnimationComplete: function()
			    {
			        this.showTooltip(this.datasets[0].bars, true);
			    }
			};

			var ctx = document.getElementById("canvas").getContext("2d");
			window.myBar = new Chart(ctx).Bar(barChartData, chartOptions);
			var barChartData2 = {
			labels : <? echo $bar_arr1; ?>,
			datasets : [
					{
						fillColor : "#8064a2",
						strokeColor : "rgba(220,220,220,0.8)",
						highlightFill: "rgb(255,99,71)",
						highlightStroke: "rgba(220,220,220,1)",
						data : <? echo $val_arr1; ?>
					}

				]
			}

			var ctx = document.getElementById("canvas2").getContext("2d");
			window.myBar = new Chart(ctx).Bar(barChartData2, chartOptions);
	</script>






	<?
	foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$data = ob_get_contents();
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_name."_".$name.".xls";
	echo "$total_data12****$filename";
	exit();

}
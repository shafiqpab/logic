<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');


if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "" );   	 
	exit();
}

if ($action=="job_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);
	
?>	
    <script>
	/*var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	 
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
	function js_set_value(id)
	{
		var str=id.split("_");
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		var strdt=str[2];
		str=str[1];
	
		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
			selected_name.push( strdt );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i,1 );
		}
		var id = '';
		var ddd='';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			ddd += selected_name[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		ddd = ddd.substr( 0, ddd.length - 1 );
		$('#txt_job_id').val( id );
		$('#txt_job_val').val( ddd );
	} */
	
	function js_set_value( job_id )
	{
		//alert(po_id)
		document.getElementById('txt_job_id').value=job_id;
		parent.emailwindow.hide();
	}
		  
	</script>
     <input type="hidden" id="txt_job_id" />
 <?
	if ($data[0]==0) $company_id=""; else $company_id=" and company_name=$data[0]";
	if ($data[1]==0) $buyer_id=""; else $buyer_id=" and buyer_name=$data[1]";
	//if ($data[2]==0) $year_id=""; else $year_id=" and buyer_name=$data[2]";
	if($db_type==0)
	{
		if(str_replace("'","",$data[2])!=0) $year_cond=" and year(insert_date)=".str_replace("'","",$data[2]).""; else $year_cond="";	
	}
	else
	{
		if(str_replace("'","",$data[2])!=0) $year_cond=" and to_char(insert_date,'YYYY')=".str_replace("'","",$data[2]).""; else $year_cond="";
	}
	
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$teamMemberArr = return_library_array("select id,team_leader_name from lib_marketing_team ","id","team_leader_name");
	
	 $sql= "select id, job_no,job_no_prefix_num, style_ref_no, product_dept, dealing_marchant, team_leader from wo_po_details_master where status_active=1 and is_deleted=0 $company_id $buyer_id $year_cond order by job_no";
	//echo $sql;
	
	$arr=array(2=>$product_dept,3=>$marchentrArr,4=>$teamMemberArr);
	echo  create_list_view("list_view", "Job No,Style Ref.,Prod. Dept.,Marchant,Team Name", "100,110,110,150,150","680","360",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0,0,product_dept,dealing_marchant,team_leader", $arr , "job_no_prefix_num,style_ref_no,product_dept,dealing_marchant,team_leader", "",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
	exit();
}

if ($action=="po_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);
	
?>	
    <script>
	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	 
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		function check_all_data()
		{
			var row_num=$('#list_view tr').length-1;
			for(var i=1;  i<=row_num;  i++)
			{
				$("#tr_"+i).click();
			}
			
		}
		
	function js_set_value(id)
	{
		var str=id.split("_");
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		var strdt=str[2];
		str=str[1];
	
		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
			selected_name.push( strdt );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i,1 );
		}
		var id = '';
		var ddd='';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			ddd += selected_name[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		ddd = ddd.substr( 0, ddd.length - 1 );
		$('#txt_po_id').val( id );
		$('#txt_po_val').val( ddd );
	} 
		  
	</script>
     <input type="hidden" id="txt_po_id" />
     <input type="hidden" id="txt_po_val" />
     <?
	 if ($data[0]==0) $company_name=""; else $job_num=" and a.company_name='$data[0]'";
	 if ($data[1]==0) $buyer_name=""; else $buyer_name=" and a.buyer_name='$data[1]'";
	 if ($data[2]=="") $job_num=""; else $job_num=" and a.job_no='$data[2]'";
	if($db_type==0)
	{
		if(str_replace("'","",$data[3])!=0) $year_cond=" and year(a.insert_date)=".str_replace("'","",$data[3]).""; else $year_cond="";	
	}
	else
	{
		if(str_replace("'","",$data[3])!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$data[3]).""; else $year_cond="";
	}
	
	  //$sql= "select b.id, b.po_number, b.job_no_mst, b.pub_shipment_date,c.gmts_item_id from wo_po_details_master a, wo_po_break_down  b, wo_po_details_mas_set_details c where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.status_active=1 and a.is_deleted=0 $job_num $company_name $buyer_name group by b.id order by po_number";
	  $sql= "select b.id, b.po_number, b.job_no_mst, b.pub_shipment_date,c.gmts_item_id from wo_po_details_master a, wo_po_break_down  b, wo_po_details_mas_set_details c where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.status_active=1 and a.is_deleted=0 $job_num $company_name $buyer_name $year_cond group by b.id, b.po_number, b.job_no_mst, b.pub_shipment_date,c.gmts_item_id order by po_number";
	
	$arr=array(3=>$garments_item);
	echo  create_list_view("list_view", "PO No.,Job No.,Pub Shipment Date,Item Name", "100,100,80,150","450","360",0, $sql , "js_set_value", "id,po_number", "", 1, "0,0,0,gmts_item_id", $arr , "po_number,job_no_mst,pub_shipment_date,gmts_item_id", "",'setFilterGrid("list_view",-1);','0,0,3,0','',1) ;
	exit();	 
}

if($action=="report_generate")
{
	extract($_REQUEST);
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$job_no=str_replace("'","",$txt_job_no);
	$hidd_job=str_replace("'","",$hidd_job_id);
	$hidd_po=str_replace("'","",$hidd_po_id);
	$txt_po_no=str_replace("'","",$txt_po_no);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	
	if ($cbo_company==0) $company_id=""; else $company_id=" and a.company_name=$cbo_company";
	if ($cbo_buyer==0) $buyer_id=""; else $buyer_id=" and a.buyer_name=$cbo_buyer";
	if ($job_no=="") $job_num=""; else $job_num=" and a.job_no='$job_no'";
	//if ($job_no=="") $job_num_mst=""; else $job_num_mst=" and b.job_no_mst=$job_no";
	if ($job_no=="") $job_num_mst=""; else $job_num_mst=" and a.job_no_prefix_num=$job_no";
	if ($txt_file_no=="") $file_cond=""; else $file_cond=" and c.file_no='$txt_file_no'";
	if ($txt_ref_no=="") $ref_cond=""; else $ref_cond=" and c.grouping='$txt_ref_no'";
	if ($hidd_job==0) $job_id=""; else $job_id=" and a.id in ($hidd_job)";
	//if ($hidd_po=="") $po_id=""; else $po_id=" and c.id in ( $hidd_po )";
	if($hidd_po!='')
	{
		if ($hidd_po=="") $po_id=""; else $po_id=" and c.id in ( $hidd_po )";
	}
	else
	{
		if ($txt_po_no=="") $po_id=""; else $po_id=" and c.po_number='$txt_po_no'";	
	}
	if($db_type==0)
	{
		if( $date_from=="" && $date_to=="" ) $pub_shipment_date=""; else $pub_shipment_date= " and d.cutup_date between '".$date_from."' and '".$date_to."'";
		if(str_replace("'","",$cbo_year)!=0) $year_cond=" and year(a.insert_date)=".str_replace("'","",$cbo_year).""; else $year_cond="";
	}
	if($db_type==2)
	{
		if( $date_from=="" && $date_to=="" ) $pub_shipment_date=""; else $pub_shipment_date= " and d.cutup_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		if(str_replace("'","",$cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$cbo_year).""; else $year_cond="";
	}
	
	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	//$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where file_type=1",'master_tble_id','image_location');
	$imge_arr=array();
	$imge_sql=sql_select( "select master_tble_id, image_location from common_photo_library where file_type=1");
	foreach($imge_sql as $row){
		$imge_arr[$row[csf('master_tble_id')]].=$row[csf('image_location')].'**';
	}
	//print_r($imge_arr);
	
	//$countryArr = return_library_array("select id,country_name from lib_country ","id","country_name");
	
	$sql_coun_data = sql_select("select id,country_name,short_name from lib_country");
	foreach($sql_coun_data as $row){
		if($row[csf('short_name')])$shortName=' [<b>'.$row[csf('short_name')].'</b>]';else $shortName='';
		$countryArr[$row[csf('id')]]=$row[csf('country_name')].$shortName;
	}
	unset($sql_coun_data);
	
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	
	$job_no_array=array();
	$po_arr=array();
	
	$po_details_array=array();
	$job_size_array=array();
	$po_item_array=array();
	$po_country_array=array();
	$po_country_ship_date_array=array();
	$po_color_array=array();
	$job_qnty_color_size_table_array=array();
	$job_size_tot_qnty_array=array();
	
	$po_cut_off_array=array();
	$po_color_size_qnty_array=array();
	$po_color_qnty_array=array();
	$po_qnty_array=array();
	$po_qnty_color_size_table_array=array();
	$po_size_tot_qnty_array=array();
	$po_item_qnty_array=array();
	$po_item_size_tot_qnty_array=array();
	$po_country_qnty_array=array();
	$po_country_size_tot_qnty_array=array();
	$po_ship_date_array=array();
	$po_file_no_array=array();
	$po_ref_no_array=array();
	
	ob_start();
	 $sql="SELECT distinct a.id as job_id, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity , a.order_uom, a.total_set_qnty, a.gmts_item_id, a.set_break_down, a.remarks, d.item_number_id as item_number_id, c.id as po_id, c.file_no, c.grouping, c.po_number, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price, d.id, d.cutup, d.country_id, d.country_ship_date, d.cutup_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty, d.assort_qty, d.solid_qty
	FROM wo_po_details_master a

	LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
	AND c.is_deleted =0
	AND c.status_active =1
	LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
	AND c.id = d.po_break_down_id
	AND d.is_deleted =0
	AND d.status_active =1
	WHERE 
	d.id!=0 and
	a.is_deleted =0
	AND a.status_active =1
	$company_id $buyer_id $job_id $po_id $pub_shipment_date $job_num_mst $year_cond $file_cond  $ref_cond order by a.job_no,c.id,d.cutup_date,d.cutup,d.id
	";
	//echo $sql;die;
	
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
		$job_no_array[$row[csf('job_no')]]=array("job_no"=>$row[csf('job_no')],"job_quantity"=>$row[csf('job_quantity')],"company_name"=>$row[csf('company_name')],"buyer_name"=>$row[csf('buyer_name')],"style_ref_no"=>$row[csf('style_ref_no')],"product_dept"=>$row[csf('product_dept')],"dealing_marchant"=>$row[csf('dealing_marchant')],"job_id"=>$row[csf('job_id')],"order_uom"=>$row[csf('order_uom')],"gmts_item_id"=>$row[csf('gmts_item_id')],"set_break_down"=>$row[csf('set_break_down')],"remarks"=>$row[csf('remarks')]);
		
		$po_arr[$row[csf('job_no')]][$row[csf('po_id')]]['po_no']=$row[csf('po_number')];
		$po_arr[$row[csf('job_no')]][$row[csf('po_id')]]['file']=$row[csf('file_no')];
		$po_arr[$row[csf('job_no')]][$row[csf('po_id')]]['ref']=$row[csf('grouping')];
		
		$po_color_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('country_id')]] [$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		
		$job_size_array[$row[csf('job_no')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$po_cutup_date_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]]=$row[csf('cutup_date')];
		$po_item_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]]=$row[csf('item_number_id')];
		$po_country_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('country_id')]]=$row[csf('country_id')];
		//$po_ship_date_array[$row[csf('job_no')]][$row[csf('po_id')]]=$row[csf('cutup_date')];
		$po_country_ship_date_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('country_id')]]=$row[csf('country_ship_date')];
		$assort_solid_qty=0;
		$assort_solid_qty=$row[csf('assort_qty')]+$row[csf('solid_qty')];
		
		$po_color_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('country_id')]] [$row[csf('color_number_id')]]+=$assort_solid_qty;
		$po_color_size_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('country_id')]] [$row[csf('color_number_id')]][$row[csf('size_number_id')]]['assort']=$row[csf('assort_qty')];
		$po_color_size_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('country_id')]] [$row[csf('color_number_id')]][$row[csf('size_number_id')]]['solid']=$row[csf('solid_qty')];
		$po_country_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('country_id')]][$row[csf('size_number_id')]]['assort']+=$row[csf('assort_qty')];
		$po_country_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('country_id')]][$row[csf('size_number_id')]]['solid']+=$row[csf('solid_qty')];
		$po_cut_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('size_number_id')]]['assort']+=$row[csf('assort_qty')];
		$po_cut_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('size_number_id')]]['solid']+=$row[csf('solid_qty')];
		
		$po_item_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('size_number_id')]]['assort']+=$row[csf('assort_qty')];
		$po_item_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('size_number_id')]]['solid']+=$row[csf('solid_qty')];
		$po_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('size_number_id')]]['assort']+=$row[csf('assort_qty')];
		$po_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('size_number_id')]]['solid']+=$row[csf('solid_qty')];
		$job_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('size_number_id')]]['assort']+=$row[csf('assort_qty')];
		$job_size_tot_qnty_array[$row[csf('job_no')]][$row[csf('size_number_id')]]['solid']+=$row[csf('solid_qty')];
		
		$po_country_id_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('country_id')]]=$row[csf('country_id')];
		
		$job_qnty_color_size_table_array[$row[csf('job_no')]]+=$assort_solid_qty;
		$po_item_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]]+=$assort_solid_qty;
		$po_cutoff_tot_qty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('cutup')]]+=$assort_solid_qty;
		$po_country_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('cutup')]][$row[csf('country_id')]]+=$assort_solid_qty;
		$po_qnty_color_size_table_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]]+=$assort_solid_qty;
		
		$po_qnty_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]]+=$row[csf('order_quantity')];
		$po_cut_off_array[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('cutup_date')]][$row[csf('item_number_id')]][$row[csf('cutup')]]=$row[csf('cutup')];
	}
	unset($sql_data);
	//print_r($po_cutup_date_array);
	
	$po_rowspan_arr=array();
	$item_rowspan_arr=array();
	$country_rowspan_arr=array();
	$cutoff_rowspan_arr=array();
	foreach($po_color_array as $job)
	{
		foreach($job as $po_id=>$po_value)
		{
			foreach($po_value as $cut_up_id=>$cut_up_value)
			{  
				$po_rowspan=0;
				foreach($cut_up_value as $item_id =>$item_value)
				{
					$item_rowspan=0;
					foreach($item_value as $cutoff_id =>$cutoff_value)
					{
						$cutoff_rowspan=0;
						foreach($cutoff_value as $country_id =>$country_value)
						{
							$country_rowspan=1;
							foreach($country_value as $color_id =>$color_value)
							{
								$po_rowspan++;
								$item_rowspan++;
								$cutoff_rowspan++;
								$country_rowspan_arr[$po_id][$cut_up_id][$item_id][$cutoff_id][$country_id]=$country_rowspan;
								$country_rowspan++;
							}
							$po_rowspan++;
							$po_rowspan_arr[$po_id][$cut_up_id]=$po_rowspan;
							$item_rowspan++;
							$cutoff_rowspan++;
							$cutoff_rowspan_arr[$po_id][$cut_up_id][$item_id][$cutoff_id]=$cutoff_rowspan;
						}
						$po_rowspan++;
						$po_rowspan_arr[$po_id][$cut_up_id]=$po_rowspan;
						$item_rowspan++;
						$item_rowspan_arr[$po_id][$cut_up_id][$item_id]=$item_rowspan;
					}
					$po_rowspan++;
					$po_rowspan_arr[$po_id][$cut_up_id]=$po_rowspan;
				}
			}//$po_rowspan++;
		}//$po_rowspan++;
	}
	//print_r($item_rowspan_arr)
	?>
    <table id="scroll_body" align="center" style="height:auto; width:1230px; margin:0 auto; padding:0;">
    <tr>
    <td width="1250"><div align="center" style="font-size:18px; color:#30C"><b><? echo $companyArr[$cbo_company]; ?></b><br /><i><u><? echo $report_title; ?></u></i></div>
	<?
	foreach($job_no_array as $rdata=>$det)
	{
		?>
        <br />
		<table width="1180px" align="center" rules="all" style="font-size:12px;" border="1" >
            <tr>
            	<td width="70">Buyer: </td><td width="100"><? echo $buyerArr[$det['buyer_name']]; ?></td>
                <td width="70">Job No: </td><td width="120" onclick="openmypage_job_color_size( 'requires/assortment_breakdown_report_controller.php?action=job_color_size_cut&job_no=<? echo $det["job_no"] ?>','Job Color Size')"><a href="##"><? echo $det['job_no']; ?></a></td>
                <td rowspan="4" style="word-wrap: break-word;word-break: break-all;" valign="top" align="center">
                	<?
						$image_all='';
                		$ex_img=array_filter(explode('**',$imge_arr[$det['job_no']]));
						foreach($ex_img as $img)
						{
							if($image_all=='') $image_all='<img src="../../'.$img.'" height="70" width="70" />'; else $image_all.=' '.'<img src="../../'.$img.'" height="70" width="65" />';
						}
						echo $image_all; ?>
                 </td>
            </tr>
            <tr>
                <td>Style Ref.: </td><td><? echo $det['style_ref_no']; ?></td>
                <td>Job Qty: </td><td><? echo $det['job_quantity']."(Pcs)"; ?></td>
            </tr>
            <tr style="background-color:#FFF">
            	<td>Merchant: </td><td><? echo $marchentrArr[$det['dealing_marchant']]; ?></td>
                <td>Prod. Dept.: </td><td><? echo $product_dept[$det['product_dept']]; ?></td>
            </tr>
            <tr style="background-color:#FFF">
                <td>Order Uom: </td><td><? echo $det['order_uom']; ?></td>
                <td>Item Details: </td><td style="word-break: break-all;"><? 
					$gmts_item_name=''; 
					$ex_item_id=explode(',',$det['gmts_item_id']); 
					foreach($ex_item_id as $gmts_item_id)
					{
						if($gmts_item_name=='') $gmts_item_name=$garments_item[$gmts_item_id]; else $gmts_item_name.=',<br> '.$garments_item[$gmts_item_id];
					}
					echo $gmts_item_name; ?></td>
            </tr>
            <tr style="background-color:#FFF">
                <td>Ratio: </td><td><?
					$item_ratio='';
					$ex_item_retio=explode('__',$det['set_break_down']);
					foreach($ex_item_retio as $ratio)
					{
						$ex_ratio='';
						$ex_ratio=explode('_',$ratio);
						
						if($item_ratio=='') $item_ratio=$ex_ratio[1]; else $item_ratio.=':'.$ex_ratio[1];
					}
					 echo $item_ratio; ?></td>
                <td>Remarks: </td><td colspan="2"><? echo $det['remarks']; ?></td>
            </tr>
        </table>
        <br/>
        <table width="1180px" align="center" border="1" rules="all" class="rpt_table" >
            <thead>
                <tr>
                    <th width="70" rowspan="2">PO No.</th> 
                    <th width="60" rowspan="2">File No</th> 
                    <th width="70" rowspan="2">Ref. No</th>
                    <th width="60" rowspan="2">PO Qty</th>
                    <th width="70" rowspan="2">Item </th>
                    <th width="70" rowspan="2">Cut-off </th>
                    <th width="70" rowspan="2">Country</th>
                    <th width="70" rowspan="2">Color</th>
                    <th width="60" rowspan="2">Color Total</th>
                    <?
                    foreach($job_size_array[$det['job_no']] as $key=>$value)
                    {
						if($value !="")
						{
							?>
							<th colspan="2"><? echo $itemSizeArr[$value];?></th>
							<?
						}
                    }
                    ?>
                </tr>
                <tr>
                	<?
                    foreach($job_size_array[$det['job_no']] as $key=>$value)
                    {
						if($value !="")
						{
							?>
							<th width="60">Assort</th>
                            <th width="60">Solid</th>
							<?
						}
                    }
                    ?>
                </tr>
            </thead>
            <?
				foreach($po_arr[$det['job_no']] as $key=>$value)
				{ 
					foreach($po_cutup_date_array[$det['job_no']][$key] as $cut_up_key=>$cut_up_value)
					{
						$posl=1;
						foreach($po_item_array [$det['job_no']][$key][$cut_up_value] as $item_key=>$item_value)
						{
							$itemsl=1;
							foreach($po_cut_off_array [$det['job_no']][$key][$cut_up_value][$item_value] as $cutoff_key=>$cutoff_value)
							{
								$cutoffsl=1;
								foreach($po_country_array [$det['job_no']][$key][$cut_up_value][$item_value][$cutoff_value] as $country_key=>$country_value)
								{
									//echo count($po_country_array [$det[csf('job_no')]][$key][$item_key]);
									$countrysl=1;
									foreach($po_color_array [$det['job_no']][$key][$cut_up_value][$item_value][$cutoff_value][$country_value] as $color_key=>$color_value)
									{
										if($countrysl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>
										<tr bgcolor="<? echo $bgcolor;?>" >
										<?
										if($posl==1)
										{
											?>
											<td  align="center" valign="middle" rowspan="<? echo $po_rowspan_arr[$key][$cut_up_key]; ?>" ><?  echo $value['po_no']; echo "<br/> Cut-off Date:<br/>".change_date_format($po_cutup_date_array[$det['job_no']][$key][$cut_up_value],"dd-mm-yyyy","-"); if($po_cutup_date_array[$det['job_no']][$key][$cut_up_value]!='0000-00-00') echo "<br/>".date('l', strtotime($po_cutup_date_array[$det['job_no']][$key][$cut_up_value])); ?></td>
											<td  align="center" valign="middle" rowspan="<? echo $po_rowspan_arr[$key][$cut_up_key]; ?>"><? echo $value['file']; ?></td>
                                            <td  align="center" valign="middle" rowspan="<? echo $po_rowspan_arr[$key][$cut_up_key]; ?>"><? echo $value['ref']; ?></td>
                                            <td align="center" valign="middle" rowspan="<? echo $po_rowspan_arr[$key][$cut_up_key]; ?>"><? echo $po_qnty_array[$det['job_no']][$key][$cut_up_value]; ?></td>
										<?
										}
										if($itemsl==1)
										{
											?>
											<td align="center" valign="middle" rowspan="<?  echo $item_rowspan_arr[$key][$cut_up_key][$item_key]; ?>"><? echo $garments_item[$item_value] ;?></td> 
											<?
										}
										if($cutoffsl==1)
										{
											?>
											<td align="center" valign="middle" rowspan="<?  echo $cutoff_rowspan_arr[$key][$cut_up_key][$item_key][$cutoff_key]; ?>"><? echo $cut_up_array[$po_cut_off_array[$det['job_no']][$key][$cut_up_value][$item_value][$cutoff_value]] ;?></td> 
											<?
										}
										if($countrysl==1)
										{
											?>
												<td align="center" valign="middle" rowspan="<?  echo $country_rowspan_arr[$key][$cut_up_key][$item_key][$cutoff_key][$country_key]; ?>"><?  echo $countryArr[$po_country_id_array[$det['job_no']][$key][$cut_up_value][$item_value][$cutoff_value][$country_value]]."<br/>".change_date_format($po_country_ship_date_array [$det['job_no']][$key][$cut_up_value][$item_value][$cutoff_value][$country_value],"dd-mm-yyyy","-")."<br/>".date('l', strtotime($po_country_ship_date_array [$det['job_no']][$key][$cut_up_value][$item_value][$cutoff_value][$country_value]));  ?></td>
											<?
										}
										?>
										<td><? echo $colorArr[$color_value] ;?></td>
										<td align="right"><? echo $po_color_qnty_array [$det['job_no']][$key][$cut_up_value][$item_value][$cutoff_value][$country_value][$color_value]; ?></td>
										<?
										foreach($job_size_array[$det['job_no']] as $key_s=>$value_s)
										{
											if($value_s !="")
											{
												?>
												<td align="right"><? echo $po_color_size_qnty_array [$det['job_no']][$key][$cut_up_value][$item_value][$cutoff_value][$country_value][$color_value][$value_s]['assort']; ?></td>
                                                <td align="right"><? echo $po_color_size_qnty_array [$det['job_no']][$key][$cut_up_value][$item_value][$cutoff_value][$country_value][$color_value][$value_s]['solid']; ?></td>
												<?
											}
										}
										?>
										</tr>
										<?
										$posl++;
										$itemsl++;
										$cutoffsl++;
										$countrysl++;
									}
									?>
									<tr style="font-weight:bold; font-size:12px; background-color:#CCC">
                                        <td colspan="2">Country Total:</td>
                                        <td colspan="" align="right"><? echo $po_country_qnty_array[$det['job_no']][$key][$cut_up_value][$item_key][$cutoff_value][$country_key] ?></td>
                                        <?
                                        foreach($job_size_array[$det['job_no']] as $key_s=>$value_s)
                                        {
											if($value_s !="")
											{
												?>
												<td align="right"><? echo $po_country_size_tot_qnty_array [$det['job_no']][$key][$cut_up_value][$item_value][$cutoff_value][$country_value][$value_s]['assort']; ?></td>
												<td align="right"><? echo $po_country_size_tot_qnty_array [$det['job_no']][$key][$cut_up_value][$item_value][$cutoff_value][$country_value][$value_s]['solid']; ?></td>
												<?
											}
                                        }
                                        ?>
									</tr>
									<?
									}
								?>
								<tr style="font-weight:bold; font-size:12px; background-color:#FF9">
									<td colspan="3"><? echo $cut_up_array[$po_cut_off_array[$det['job_no']][$key][$cut_up_value][$item_value][$cutoff_value]]; ?> Total:</td>
									<td colspan="" align="right"><? echo $po_cutoff_tot_qty_array[$det['job_no']][$key][$cut_up_value][$item_value][$cutoff_value] ?></td>
									<?
									foreach($job_size_array[$det['job_no']] as $key_s=>$value_s)
									{
										if($value_s !="")
										{
											?>
											<td align="right"><? echo $po_cut_size_tot_qnty_array[$det['job_no']][$key][$cut_up_value][$item_value][$cutoff_value][$value_s]['assort']; ?></td>
                                            <td align="right"><? echo $po_cut_size_tot_qnty_array[$det['job_no']][$key][$cut_up_value][$item_value][$cutoff_value][$value_s]['solid']; ?></td>
											<?
										}
									}
									?>
								</tr>
								<?
								}
							?>
							<tr style="font-weight:bold; font-size:12px; background-color:#FFCCFF">
								<td colspan="4">Item Total:</td>
								<td colspan="" align="right"><? echo $po_item_qnty_array[$det['job_no']][$key][$cut_up_value][$item_key] ?></td>
								<?
								foreach($job_size_array[$det['job_no']] as $key_s=>$value_s)
								{
									if($value_s !="")
									{
										?>
										<td align="right"><? echo $po_item_size_tot_qnty_array[$det['job_no']][$key][$cut_up_value][$item_value][$value_s]['assort']; ?></td>
                                        <td align="right"><? echo $po_item_size_tot_qnty_array[$det['job_no']][$key][$cut_up_value][$item_value][$value_s]['solid']; ?></td>
										<?
									}
								}
								?>
							</tr>
							<?
							}
						?>
						<tr style="font-weight:bold; font-size:12px; background-color:#CCCCFF">
                            <td colspan="8">Date wise Po Total:</td>
                            <td  align="right"><? echo $po_qnty_color_size_table_array[$det['job_no']][$key][$cut_up_value]?></td>
                            <?
                            foreach($job_size_array[$det['job_no']] as $key_s=>$value_s)
                            {
                                if($value_s !="")
                                {
									?>
									<td align="right"><? echo $po_size_tot_qnty_array [$det['job_no']][$key][$cut_up_value][$value_s]['assort']; ?></td>
                                    <td align="right"><? echo $po_size_tot_qnty_array [$det['job_no']][$key][$cut_up_value][$value_s]['solid']; ?></td>
									<?
                                }
                            }
                            ?>
						</tr>
						<?
					}
				}
				?>
				<tfoot>
				<tr style="font-weight:bold; font-size:12px">
					<th colspan="8" align="left">Grand Total:</th>
					<th align="right"><? echo $job_qnty_color_size_table_array [$det['job_no']];?></th>
					<?
                    foreach($job_size_array[$det['job_no']] as $key_s=>$value_s)
                    {
						if($value_s !="")
						{
							?>
							<th align="right"><? echo $job_size_tot_qnty_array [$det['job_no']][$value_s]['assort']; ?></th>
                            <th align="right"><? echo $job_size_tot_qnty_array [$det['job_no']][$value_s]['solid']; ?></th>
							<?
						}
                    }
                    ?>
                </tr>
            </tfoot>
		</table>
		<br/>
		<?
	}
	?>
   </td>
   </tr>
   </table>
    <?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}



if($action=="job_color_size_cut")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	 $sql="SELECT a.id as job_id, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity , a.order_uom, a.total_set_qnty,d.item_number_id as item_number_id, c.id as po_id, c.po_number, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price,  d.country_id,d.country_ship_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty
	FROM wo_po_details_master a
	LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
	AND c.is_deleted =0
	AND c.status_active =1
	LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
	AND c.id = d.po_break_down_id
	AND d.is_deleted =0
	AND d.status_active =1
	WHERE
	a.job_no='$job_no' and
	a.is_deleted =0 and
	a.status_active =1
	";
	$job_color_tot=0;
	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
    $itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$job_no_array=array();
	$job_size_array=array();
	$job_size_qnty_array=array();
	$job_color_array=array();
	$job_color_qnty_array=array();
	$job_color_size_qnty_array=array();
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
	$job_no_array[$row[csf('job_no')]]=array("job_no"=>$row[csf('job_no')],"job_quantity"=>$row[csf('job_quantity')],"company_name"=>$row[csf('company_name')],"buyer_name"=>$row[csf('buyer_name')],"style_ref_no"=>$row[csf('style_ref_no')],"product_dept"=>$row[csf('product_dept')],"dealing_marchant"=>$row[csf('dealing_marchant')],"job_id"=>$row[csf('job_id')],"order_uom"=>$row[csf('order_uom')]);
	$job_size_array[$row[csf('job_no')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
	$job_size_qnty_array[$row[csf('job_no')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
	$job_color_array[$row[csf('job_no')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
	$job_color_qnty_array[$row[csf('job_no')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
	$job_color_size_qnty_array[$row[csf('job_no')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
	}
	?>
    <table width="1030px" align="center" border="1" rules="all">
            <tr style="background-color:#FFF">
                <td width="60" align="right">Job No: </td><td width="90" ><? echo $job_no; ?></td>
                <td width="60" align="right">Job Qnty: </td><td width="90"><? echo $job_no_array[$job_no]['job_quantity']."(Pcs)"; ?></td>
                <td width="60" align="right">Company: </td><td width="90"><? echo $companyArr[$job_no_array[$job_no][csf('company_name')]]; ?></td>
                <td width="60" align="right">Buyer: </td><td width="85"><? echo $buyerArr[$job_no_array[$job_no][csf('buyer_name')]]; ?></td>
                <td width="65" align="right">Style Ref.: </td><td width="85"><? echo $job_no_array[$job_no][csf('style_ref_no')]; ?></td>
                <td width="70" align="right">Prod. Dept.: </td><td width="80"><? echo $product_dept[$job_no_array[$job_no][csf('product_dept')]]; ?></td>
                <td width="60" align="right">Merchant: </td><td width="90"><? echo $marchentrArr[$job_no_array[$job_no][csf('dealing_marchant')]]; ?></td>
            </tr>
        </table>
    <table width="1030px" align="center" border="1" rules="all" class="rpt_table" >
            <thead>
                <tr>
                    <th width="60">Color</th>
                    <th width="60">Color Total</th>
                    <?
					foreach($job_size_array[$job_no] as $key=>$value)
                    {
						if($value !="")
						{
					?>
                    <th width="60"><? echo $itemSizeArr[$value];?></th>
                    <?
						}
					}
					?>
                </tr>
            </thead>
            <?
			$i=1;
			foreach($job_color_array[$job_no] as $key_c=>$value_c)
            {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			if($value_c != "")
            {
			?>
           
             <tr bgcolor="<? echo $bgcolor;?>">
             <td><? echo  $colorArr[$value_c]; ?></td>
             <td align="right"><? echo  $job_color_qnty_array[$job_no][$value_c]; $job_color_tot+=$job_color_qnty_array[$job_no][$value_c]; ?></td>
             <?
					foreach($job_size_array[$job_no] as $key_s=>$value_s)
                    {
						if($value_s !="")
						{
					?>
                    <td width="60" align="right"><? echo $job_color_size_qnty_array[$job_no][$value_c][$value_s];?></td>
                    <?
						}
					}
					?>
             </tr>
            <?
			$i++;
			}
			}
			?>
            <tfoot>
             <tr bgcolor="<? // echo $bgcolor;?>">
             <th>Total</th>
             <th align="right"><? echo  $job_color_tot; ?></th>
             <?
					foreach($job_size_array[$job_no] as $key_s=>$value_s)
                    {
						if($value_s !="")
						{
					?>
                    <th width="60" align="right"><? echo $job_size_qnty_array[$job_no][$value_s];?></th>
                    <?
						}
					}
					?>
             </tr>
             </tfoot>
            </table>
    <?
}













if($action=="job_color_size")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	 $sql="SELECT a.id as job_id, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.product_dept, a.location_name, a.dealing_marchant, (a.job_quantity*a.total_set_qnty) as job_quantity , a.order_uom, a.total_set_qnty,d.item_number_id as item_number_id, c.id as po_id, c.po_number, c.pub_shipment_date, c.shipment_date, c.po_received_date, (c.po_quantity*a.total_set_qnty) as po_quantity, c.excess_cut, c.plan_cut, c.unit_price, c.po_total_price,  d.country_id,d.country_ship_date, d.size_number_id, d.color_number_id, d.order_quantity, d.order_rate, d.order_total, d.excess_cut_perc, d.plan_cut_qnty
	FROM wo_po_details_master a
	
	LEFT JOIN wo_po_break_down c ON a.job_no = c.job_no_mst
	AND c.is_deleted =0
	AND c.status_active =1
	LEFT JOIN wo_po_color_size_breakdown d ON c.job_no_mst = d.job_no_mst
	AND c.id = d.po_break_down_id
	AND d.is_deleted =0
	AND d.status_active =1
	WHERE
	a.job_no='$job_no' and
	a.is_deleted =0 and
	a.status_active =1
	";//LEFT JOIN wo_po_details_mas_set_details b ON a.job_no = b.job_no
	$job_color_tot=0;
	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
    $itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$job_no_array=array();
	$job_size_array=array();
	$job_size_qnty_array=array();
	$job_color_array=array();
	$job_color_qnty_array=array();
	$job_color_size_qnty_array=array();
	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
	$job_no_array[$row[csf('job_no')]]=array("job_no"=>$row[csf('job_no')],"job_quantity"=>$row[csf('job_quantity')],"company_name"=>$row[csf('company_name')],"buyer_name"=>$row[csf('buyer_name')],"style_ref_no"=>$row[csf('style_ref_no')],"product_dept"=>$row[csf('product_dept')],"dealing_marchant"=>$row[csf('dealing_marchant')],"job_id"=>$row[csf('job_id')],"order_uom"=>$row[csf('order_uom')]);
	$job_size_array[$row[csf('job_no')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
	$job_size_qnty_array[$row[csf('job_no')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
	$job_color_array[$row[csf('job_no')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
	$job_color_qnty_array[$row[csf('job_no')]][$row[csf('color_number_id')]]+=$row[csf('order_quantity')];
	$job_color_size_qnty_array[$row[csf('job_no')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
	}
	?>
    <table width="1030px" align="center" border="1" rules="all">
            <tr style="background-color:#FFF">
                <td width="60" align="right">Job No: </td><td width="90" ><? echo $job_no; ?></td>
                <td width="60" align="right">Job Qnty: </td><td width="90"><? echo $job_no_array[$job_no]['job_quantity']."(Pcs)"; ?></td>
                <td width="60" align="right">Company: </td><td width="90"><? echo $companyArr[$job_no_array[$job_no][csf('company_name')]]; ?></td>
                <td width="60" align="right">Buyer: </td><td width="85"><? echo $buyerArr[$job_no_array[$job_no][csf('buyer_name')]]; ?></td>
                <td width="65" align="right">Style Ref.: </td><td width="85"><? echo $job_no_array[$job_no][csf('style_ref_no')]; ?></td>
                <td width="70" align="right">Prod. Dept.: </td><td width="80"><? echo $product_dept[$job_no_array[$job_no][csf('product_dept')]]; ?></td>
                <td width="60" align="right">Merchant: </td><td width="90"><? echo $marchentrArr[$job_no_array[$job_no][csf('dealing_marchant')]]; ?></td>
            </tr>
        </table>
    <table width="1030px" align="center" border="1" rules="all" class="rpt_table" >
            <thead>
                <tr>
                    <th width="60">Color</th>
                    <th width="60">Color Total</th>
                    <?
					foreach($job_size_array[$job_no] as $key=>$value)
                    {
						if($value !="")
						{
					?>
                    <th width="60"><? echo $itemSizeArr[$value];?></th>
                    <?
						}
					}
					?>
                </tr>
            </thead>
            <?
			$i=1;
			foreach($job_color_array[$job_no] as $key_c=>$value_c)
            {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			if($value_c != "")
            {
			?>
           
             <tr bgcolor="<? echo $bgcolor;?>">
             <td><? echo  $colorArr[$value_c]; ?></td>
             <td align="right"><? echo  $job_color_qnty_array[$job_no][$value_c]; $job_color_tot+=$job_color_qnty_array[$job_no][$value_c]; ?></td>
             <?
					foreach($job_size_array[$job_no] as $key_s=>$value_s)
                    {
						if($value_s !="")
						{
					?>
                    <td width="60" align="right"><? echo $job_color_size_qnty_array[$job_no][$value_c][$value_s];?></td>
                    <?
						}
					}
					?>
             </tr>
            <?
			$i++;
			}
			}
			?>
            <tfoot>
             <tr bgcolor="<? // echo $bgcolor;?>">
             <th>Total</th>
             <th align="right"><? echo  $job_color_tot; ?></th>
             <?
					foreach($job_size_array[$job_no] as $key_s=>$value_s)
                    {
						if($value_s !="")
						{
					?>
                    <th width="60" align="right"><? echo $job_size_qnty_array[$job_no][$value_s];?></th>
                    <?
						}
					}
					?>
             </tr>
             </tfoot>
            </table>
    <?
}
?>

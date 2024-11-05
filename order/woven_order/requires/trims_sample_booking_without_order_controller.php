<? 
/*-------------------------------------------- Comments
Version          : V1
Purpose			 : This form will create Trims Booking
Functionality	 :	
JS Functions	 :
Created by		 : MONZU 
Creation date 	 : 27-12-2012
Requirment Client: Fakir Apperels
Requirment By    : 
Requirment type  : 
Requirment       : 
Affected page    : 
Affected Code    :              
DB Script        : 
Updated by 		 : 
Update date		 : 
QC Performed BY	 :		
QC Date			 :	
Comments		 : From this version oracle conversion is start
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

//---------------------------------------------------- Start---------------------------------------------------------------------------
//$po_number=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number"  );
$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$trim_group= return_library_array("select id, item_name from lib_item_group where item_category=4",'id','item_name');

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data'  $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/trims_sample_booking_without_order_controller', this.value.'_0', 'load_drop_down_buyer_tag_sample', 'sample_td' );" );
	exit();	
} 

if ($action=="load_drop_down_buyer_tag_sample")
{
	  // echo "select a.id, a.sample_name from lib_sample a,lib_buyer_tag_sample b,lib_buyer c where b.buyer_id=c.id and  b.tag_sample=a.id  and b.buyer_id=$data and b.sequ>0 and a.is_deleted=0";
	  $ex_data=explode("_",$data);
	  $buyer_id=$ex_data[0];
	  $style_id=$ex_data[1];//sample_development_fabric_acc
	  if($style_id>0)  $style_id_cond="and d.sample_mst_id=$style_id";else $style_id_cond="";
	  //echo $style_id_cond.'dd';
	  // echo create_drop_down( "cbo_sample_type", 172, "select a.id, a.sample_name from lib_sample a,lib_buyer_tag_sample b,lib_buyer c where b.buyer_id=c.id and  b.tag_sample=a.id  and b.buyer_id=$buyer_id and b.sequ>0 and a.is_deleted=0","id,sample_name", 1, "--Select--", $selected, "" ); 
	 // echo "select a.id, a.sample_name from lib_sample a,lib_buyer_tag_sample b,lib_buyer c,sample_development_fabric_acc d where b.buyer_id=c.id and  b.tag_sample=a.id  and a.id=d.sample_name and d.form_type=1 and b.buyer_id=$buyer_id and b.sequ>0 and a.is_deleted=0 $style_id_cond";
	//load_drop_down( 'requires/trims_sample_booking_without_order_controller', '$sample_trim', 'load_drop_down_trim_group', 'tgroup_td' );
	//echo "select a.id, a.sample_name from lib_sample a,lib_buyer_tag_sample b,lib_buyer c,sample_development_fabric_acc d where b.buyer_id=c.id and  b.tag_sample=a.id  and a.id=d.sample_name and d.form_type=1 and b.buyer_id=$buyer_id and b.sequ>0 and a.is_deleted=0 $style_id_cond";
	   echo create_drop_down( "cbo_sample_type", 172, "select a.id, a.sample_name from lib_sample a,lib_buyer_tag_sample b,lib_buyer c,sample_development_fabric_acc d where b.buyer_id=c.id and  b.tag_sample=a.id  and a.id=d.sample_name_ra and d.form_type=2 and b.buyer_id=$buyer_id and b.sequ>0 and a.is_deleted=0 and c.is_deleted=0 and d.is_deleted=0 $style_id_cond group by a.id, a.sample_name","id,sample_name", 1, "--Select--", $selected, "fnc_trimGroup(this.value)" ); 
	
exit();
}  

if ($action=="load_drop_down_trim_group")
{
	//echo "select a.id, a.item_name from lib_item_group a, sample_development_fabric_acc b where a.item_category=4 and b.trims_group_ra=a.id  and b.sample_mst_id=$data  and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$data=explode("_",$data);
	$sample_mst_id=$data[0];
	$sample_type_id=$data[1];
	//echo  "select a.id, a.item_name from lib_item_group a, sample_development_fabric_acc b where a.item_category=4 and b.trims_group_ra=a.id  and b.sample_mst_id=$sample_mst_id and b.sample_name_ra=$sample_type_id and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	//echo "select a.id, a.item_name from lib_item_group a, sample_development_fabric_acc b where a.item_category=4 and b.trims_group_ra=a.id  and b.sample_mst_id=$sample_mst_id and b.sample_name_ra=$sample_type_id and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	echo create_drop_down( "cbo_trim_group", 172, "select a.id, a.item_name from lib_item_group a, sample_development_fabric_acc b where a.item_category=4    and (b.fabric_source!=4 or b.fabric_source is null) and b.trims_group_ra=a.id  and b.sample_mst_id=$sample_mst_id and b.sample_name_ra=$sample_type_id and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","id,item_name", 1, "--Select--", $selected, "fnc_disable()" ); //set_trim_cons_uom(this.value);
	exit();
}


function load_drop_down_supplier($data){
	if($data==5 || $data==3){
	   echo create_drop_down( "cbo_supplier_name", 172, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Company --", "", "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/trims_sample_booking_without_order_controller');",0,"" );
	}
	else
	{
	$cbo_supplier_name= create_drop_down( "cbo_supplier_name", 172, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and b.party_type=4 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value, 'load_drop_down_attention', 'requires/trims_sample_booking_without_order_controller');","");
	}
	return $cbo_supplier_name;
	exit();
}
if ($action=="load_drop_down_supplier")
{
	echo $action($data);
	//echo create_drop_down( "cbo_supplier_name", 172, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type=4 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"get_php_form_data( this.value, 'load_drop_down_attention', 'requires/trims_booking_controller');","");
	exit();
}

if($action=="load_drop_down_attention")
{
	$supplier_name=return_field_value("contact_person","lib_supplier","id ='".$data."' and is_deleted=0 and status_active=1");
	echo "document.getElementById('txt_attention').value = '".$supplier_name."';\n";
	exit();	
}
if ($action=="set_cons_uom")
{
	$data=explode("_",$data);
	$trim_item=$data[0];
	$style_id=$data[1];
	$samp_type_id=$data[2];
	
	//$sql_item=sql_select("select sum(trim_qty) as  trim_qty from wo_non_ord_samp_booking_dtls where  style_id=$style_id and trim_group=$trim_item and status_active=1 and is_deleted=0");
	$prev_wo_qty=return_field_value("sum(trim_qty) as  trim_qty", "wo_non_ord_samp_booking_dtls", "style_id=$style_id and trim_group=$trim_item and status_active=1 and is_deleted=0","trim_qty");
	
	
	$cons_uom=return_field_value("order_uom", "lib_item_group", "id=$trim_item");
	$trims_req="select c.req_qty_ra,c.trims_group_ra,c.description_ra from sample_development_fabric_acc c,sample_development_mst a where a.id=c.sample_mst_id and c.form_type=2 and a.id=$style_id and c.trims_group_ra=$trim_item and a.status_active=1 and c.status_active=1";
	$trims_req_result=sql_select($trims_req,1);
	$req_qty_ra=$trims_req_result[0][csf('req_qty_ra')];
	$description_ra=$trims_req_result[0][csf('description_ra')];
	//$trims_req_result=$trims_req_result[0][csf('req_qty_ra')];
	echo $cons_uom.'_'.$req_qty_ra.'_'.$description_ra.'_'.$prev_wo_qty; die;
}
if($action=="show_fabric_booking")
{
	extract($_REQUEST);
	$sample_library=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name"  );
	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );

	$arr=array (0=>$style_library,2=>$sample_library,3=>$trim_group,4=>$unit_of_measurement,6=>$color_library,7=>$color_library,8=>$size_library);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$sql= "select style_id,style_des,sample_type,trim_group,uom,composition,barnd_sup_ref,gmts_color ,fabric_color,gmts_size,item_size,trim_qty,rate,amount,id FROM wo_non_ord_samp_booking_dtls  WHERE booking_no ='".$data."'  and status_active=1 and	is_deleted=0"; 
	 
	echo  create_list_view("list_view", "Style,Style Des,Sample,Trim Group,UOM,Brand/ Supp. Ref,Gmts Color,Item Color,Gmts Size,Item Size,Trim Qnty,Rate,Amount", "60,100,100,130,100,150,80,80,80,50,60,60","1300","220",0, $sql , "get_php_form_data", "id", "'populate_details_data_from_for_update'", 1, "style_id,0,sample_type,trim_group,uom,0,gmts_color,fabric_color,gmts_size,0,0,0,0", $arr , "style_id,style_des,sample_type,trim_group,uom,barnd_sup_ref,gmts_color,fabric_color,gmts_size,item_size,trim_qty,rate,amount", "requires/trims_sample_booking_without_order_controller",'','0,0,0,0,0,0,0,0,0,0,2,2,2') ;
}

if($action=="color_from_library")
{
  $color_from_library=return_field_value("color_from_library", "variable_order_tracking", "company_name=$data  and variable_list=23  and status_active=1 and is_deleted=0");
  echo trim($color_from_library);
  die;
}

if($action=="color_popup")
{
echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
extract($_REQUEST);
?>
<script> 
function js_set_value(data)
{
	document.getElementById('color_name').value=data;
    parent.emailwindow.hide();
}
</script> 
</head>
<body>
<body>
<div align="center">
<form>
<input type="hidden" id="color_name" name="color_name" />
<?
    if($buyer_name=="" || $buyer_name==0 )
	{
	$sql="select color_name,id FROM lib_color  WHERE status_active=1 and is_deleted=0";
	}
	else
	{
	$sql="select a.color_name,a.id FROM lib_color a, lib_color_tag_buyer b  WHERE a.id=b.color_id and b.buyer_id=$buyer_name and  status_active=1 and is_deleted=0"; 
	}
	echo  create_list_view("list_view", "Color Name", "160","210","420",0, $sql , "js_set_value", "color_name", "", 1, "0", $arr , "color_name", "requires/trims_sample_booking_without_order_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0,0,0,0,0,0,2,2,2,2,2') ;
	
	
	
	?>
    </form>
    </div>
    </body>
    </html>
    <?
}

if($action=="show_trim_booking_report")
{
	extract($_REQUEST);
	extract(check_magic_quote_gpc($_REQUEST));
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$id_approved_id=str_replace("'","",$id_approved_id);
	$report_type=str_replace("'","",$report_type);
	$show_comment=str_replace("'","",$show_comment);

	$color_library=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$size_library=return_library_array("select id, size_name from  lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$company_library=return_library_array("select id, company_name from lib_company", "id", "company_name");
	$group_library=return_library_array("select id, group_name from lib_group", "id", "group_name");
	$trim_group_library= return_library_array("select id, item_name from lib_item_group",'id','item_name');
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');

	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brandArr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	$order_uom_arr=return_library_array("select id,order_uom  from lib_item_group","id","order_uom");
	$deling_marcent_arr=return_library_array("select id,team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$team_leader_arr=return_library_array("select id,team_leader_name from lib_marketing_team","id","team_leader_name");
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$lib_user=return_library_array("select id,user_full_name from user_passwd","id","user_full_name");

	$nameArray_approved=sql_select("select max(b.approved_no) as approved_no from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='$txt_booking_no' and b.entry_form=8 and  a.status_active =1 and a.is_deleted=0");
	list($nameArray_approved_row)=$nameArray_approved;
	//$revised_no_data=sql_select("select a.revised_no  from wo_non_ord_samp_booking_mst a where  a.booking_no=$txt_booking_no and  a.status_active =1 and a.is_deleted=0");
	//list($revised_no_row)=$revised_no_data;
		$booking_grand_total=0; $currency_id="";

		$buyer_string=array(); $brand_string=array(); $style_owner=array(); $job_no=array(); $style_ref=array(); $all_dealing_marcent=array(); $season=array(); $req_string=array(); $po_id_arr=array();$job_no_arr=array();

		$nameArray_buyer=sql_select( "select  a.requisition_number,a.style_ref_no,  a.buyer_name, a.dealing_marchant, a.factory_merchant, a.season, a.season_buyer_wise,  a.brand_id,a.team_leader,a.internal_ref from sample_development_mst a,wo_non_ord_samp_booking_dtls b where a.id=b.style_id and b.booking_no='$txt_booking_no' and b.status_active =1 and a.status_active =1");


        foreach ($nameArray_buyer as $result_buy){
			$buyer_string[$result_buy[csf('buyer_name')]]=$buyer_name_arr[$result_buy[csf('buyer_name')]];
			$req_string[$result_buy[csf('requisition_number')]]=$result_buy[csf('requisition_number')];
			$brand_string[$result_buy[csf('brand_id')]]=$brandArr[$result_buy[csf('brand_id')]];
			$style_ref[$result_buy[csf('job_no')]]=$result_buy[csf('style_ref_no')];
			$all_dealing_marcent[$result_buy[csf('job_no')]]=$deling_marcent_arr[$result_buy[csf('dealing_marchant')]];
			$booking_style_ref[$txt_booking_no][$result_buy[csf('style_ref_no')]]=$result_buy[csf('style_ref_no')];
			$booking_req_ref[$txt_booking_no][$result_buy[csf('requisition_number')]]=$result_buy[csf('requisition_number')];
			$booking_int_ref[$txt_booking_no][$result_buy[csf('internal_ref')]]=$result_buy[csf('internal_ref')];
			$dealing_merchant_list[$txt_booking_no][$result_buy[csf('dealing_marchant')]]=$deling_marcent_arr[$result_buy[csf('dealing_marchant')]];
			$factory_merchant_list[$txt_booking_no][$result_buy[csf('factory_merchant')]]=$deling_marcent_arr[$result_buy[csf('factory_merchant')]];
			$team_leader_list[$txt_booking_no][$result_buy[csf('team_leader')]]=$team_leader_arr[$result_buy[csf('team_leader')]];
			$season_buyer_wise=$result_buy[csf('season_buyer_wise')];
		}
		//print_r($brand_string);
		$style_sting=implode(",",array_unique($style_ref));
		$job_no_str = "'" . implode( "','", $job_no ) . "'";
		$job_no=implode(",",$job_no);
		$seasons_names=rtrim($seasons_name,',');

		$seasons_names=implode(",",array_unique(explode(",",$seasons_names)));
		$poid_arr=array_unique($po_id_arr);

		$order_rept_no=rtrim($order_rept_no,',');
		$order_rept_no=implode(",",array_unique(explode(",",$order_rept_no)));
		$brand_str=implode(",",$brand_string);
		$main_fabric_approved = sql_select("select b.approved_date as approved_date from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.job_no in (".$job_no_str.") and b.entry_form=7 and a.status_active =1 and a.is_deleted=0 order by b.id asc");


		$po_no=array(); $file_no=array(); $ref_no=array(); $po_quantity=array();
		$pub_shipment_date='';$int_ref_no='';$tot_po_quantity=0;$po_idss='';
		$nameArray_job=sql_select( "select b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no, sum(b.po_quantity) as po_quantity  from wo_non_ord_samp_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_no='$txt_booking_no' and  a.status_active =1 and a.is_deleted=0 group by b.job_no_mst,b.id,b.pub_shipment_date, b.po_number,b.grouping, b.file_no ");
        foreach ($nameArray_job as $result_job){
			$po_no[$result_job[csf('job_no_mst')]][$result_job[csf('id')]]=$result_job[csf('po_number')];
			$file_no[$result_job[csf('id')]]=$result_job[csf('file_no')];
			$ref_no[$result_job[csf('id')]]=$result_job[csf('grouping')];
			$po_quantity[$result_job[csf('id')]]=$result_job[csf('po_quantity')];
		//	$tot_po_quantity+=$result_job[csf('po_quantity')];
			$job_ref_no[$result_job[csf('job_no_mst')]][$result_job[csf('grouping')]]=$result_job[csf('grouping')];
			$job_file_no[$result_job[csf('job_no_mst')]].=$result_job[csf('file_no')].',';
			$po_no_arr[$result_job[csf('job_no_mst')]]['po_id'].=$result_job[csf('id')].',';
			$pub_shipment_date.=$result_job[csf('pub_shipment_date')].',';
			$int_ref_no.=$result_job[csf('grouping')].',';
			if($po_idss=='') $po_idss=$result_job[csf('id')];else $po_idss.=",".$result_job[csf('id')];

		}
			//echo $po_idss.'DDDDDDDDD';
		$sql_job=sql_select( "select b.job_no_mst,b.id as po_id, b.po_quantity as po_quantity  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and  a.status_active =1 and a.is_deleted=0 and  b.status_active =1 and b.is_deleted=0 and b.id in(".$po_idss.") ");
		foreach ($sql_job as $row)
		{
			$job_po_qty_arr[$row[csf('job_no_mst')]][$row[csf('po_id')]]+=$row[csf('po_quantity')];
			$tot_po_quantity+=$row[csf('po_quantity')];
		}

        $nameArray=sql_select( "select a.booking_no, a.is_approved,a.pay_mode,a.buyer_id, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.source, a.remarks, a.revised_no,a.inserted_by,a.insert_date,a.DELIVERY_ADDRESS from wo_non_ord_samp_booking_mst a where a.booking_no='$txt_booking_no' and a.status_active =1 and a.is_deleted=0");
		foreach( $nameArray as $row)
		{
			$varcode_booking_no=$row[csf('booking_no')];
			$booking_date=$row[csf('booking_date')];
			$delivery_date=$row[csf('delivery_date')];
			$pay_mode_id=$row[csf('pay_mode')];
			$supplier_id=$row[csf('supplier_id')];
			$currency_id=$row[csf('currency_id')];
			$buyer_id=$row[csf('buyer_id')];
			$exchange_rate=$row[csf('exchange_rate')];
			$attention=$row[csf('attention')];
			$remarks=$row[csf('remarks')];
			$revised_no=$row[csf('revised_no')];
			$source_id=$row[csf('source')];
			$is_approved=$row[csf('is_approved')];
			$delivery_add=$row[csf('DELIVERY_ADDRESS')];
		}
		$approved_array=sql_select("select b.approved_by,b.approved_no, b.approved_date, c.user_full_name, b.id from  wo_non_ord_samp_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no='$txt_booking_no' and b.entry_form=8 and  a.status_active =1 and a.is_deleted=0 order by b.id asc");

	 $sql_date_update=sql_select("SELECT a.updated_by, a.booking_no,a.update_date FROM wo_non_ord_samp_booking_dtls a, wo_non_ord_samp_booking_dtls b WHERE a.booking_no = b.booking_no AND a.update_date >= b.update_date and a.booking_no='$txt_booking_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.update_date desc");
	 $sql_date_insert=sql_select("SELECT a.inserted_by, a.booking_no,a.insert_date FROM wo_non_ord_samp_booking_dtls a, wo_non_ord_samp_booking_dtls b WHERE a.booking_no = b.booking_no AND a.insert_date <= b.insert_date and a.booking_no='$txt_booking_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.insert_date");

	$nameArray=sql_select( "select id,plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website,contact_no from lib_company where id=$cbo_company_name");
		foreach ($nameArray as $result){              
			if($result[csf('plot_no')]!='')
			{
				$plot_no=$result[csf('plot_no')];
			}
			if($result[csf('level_no')]!='')
			{
				$level_no=$result[csf('level_no')];
			}
			if($result[csf('road_no')]!='')
			{
				$road_no=$result[csf('road_no')];
			}
			if($result[csf('block_no')]!='')
			{
				$block_no=$result[csf('block_no')];
			}
			if($result[csf('city')]!='')
			{
				$city=$result[csf('city')];
			}
			$company_address[$result[csf('id')]]['address']=$plot_no.'&nbsp'.$level_no.'&nbsp'.$road_no.'&nbsp'.$block_no.'&nbsp'.$city;
			$company_address[$result[csf('id')]]['tel']=$result[csf('contact_no')];
			$company_address[$result[csf('id')]]['email']=$result[csf('email')];

			
		}
		
	ob_start();
	
	?>
	    <table border="1" align="left" class="rpt_table container" cellpadding="0" width="100%" cellspacing="0" rules="all" style="padding-bottom: 10px">
   			<tr>
			   <td width="150px"  style="border-right:0" align="left"><? if($report_type==1)
                   {
                       if($link == 1)

                       {
                   ?>
                            <img  src='../../../<? echo $imge_arr[$cbo_company_name]; ?>' height='40%' width='50%' />

                   <?
                       }
                       else
                       {
                   ?>
                            <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='40%' width='50%' />
                   <?	}
                   }
                   else
                   { ?>
                     <img  src='../<? echo $imge_arr[$cbo_company_name]; ?>' height='40%' width='50%' />
                  <? }
                   ?></td>		
			   <td width="200px" colspan="2" align="center"><b style="font-size:22px;">
			   <?
				$nameArray=sql_select("select a.id,a.group_id,a.company_name,b.id,b.group_name from lib_company a,lib_group b  where a.id=$cbo_company_name and a.group_id=b.id");

				   
				foreach( $nameArray as $row)
				{
				$group_name=$row[csf('group_name')];
				}
				?>  
			   <?=$group_name;?></b></td>		  
			   <td  colspan="2" align="center"><b style="font-size:22px;">M&M DEPARTMENT</b></td>
			   <td  colspan="2" align="center"><b style="font-size:22px;">PURCHASE ORDER[SAMPLE] <br>(CODE: MMD/M&M/DMF-09)</b></td>
			   <td   align="center"><b style="font-size:22px;">BOOKING DATE :<?php echo change_date_format($booking_date); ?></b> </td>		   
			</tr>
		</table>
				<table border="1" align="left" class="rpt_table container"  cellpadding="0" width="100%" cellspacing="0" rules="all" style="padding-bottom: 10px">
				<tr>
				   <td colspan="2" align="left"><b style="font-size:20px;">Factory Name: <?=$company_library[$cbo_company_name];?></b> </td>				   
				   <td colspan="2" align="left"><b style="font-size:20px;">SUB: Accessories Purchase Order(Sample)</b></td>	
				   <td colspan="2" align="left"><b style="font-size:20px;">Booking No:</b></td>	
				   <td colspan="2"  align="left"><b style="font-size:20px;"><?=$txt_booking_no;?></b> </td>						   
				</tr>
				<tr>
				<?
				$nameArray=sql_select("select a.id,a.group_id,a.company_name,b.id,b.address from lib_company a,lib_group b  where a.id=$cbo_company_name and a.group_id=b.id");
				   
				foreach( $nameArray as $row)
				{
				$group_address=$row[csf('address')];
				}
				?>
				<td width="100" colspan="8" align="left"><b style="font-size:20px;">Head Office:</b><?=$group_address;?></td>				   
				</tr>
				<tr>
				   <td width="100" colspan="8" align="left"><b style="font-size:20px;">Factory:</b> <?
                            $nameArray=sql_select( "select id,plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            foreach ($nameArray as $result){
                            ?>
                              <?  if($result[csf('plot_no')]!='') echo $result[csf('plot_no')]; else echo '';?> &nbsp;
                                <? echo $result[csf('level_no')];?> &nbsp;
                                 <? echo $result[csf('road_no')]; ?>  &nbsp;
                                <? echo $result[csf('block_no')];?>  &nbsp;
                                <? echo $result[csf('city')];?>  &nbsp;
                                <? echo $result[csf('zip_code')]; ?>  &nbsp;
                                 <?php echo $result[csf('province')]; ?>  &nbsp;
                                <? echo $country_arr[$result[csf('country_id')]]; ?> &nbsp;<br/>
                                <? echo $result[csf('email')];?>  &nbsp;
                                <? echo $result[csf('website')];
                                if($result[csf('plot_no')]!='')
                                {
                                    $plot_no=$result[csf('plot_no')];
                                }
                                if($result[csf('level_no')]!='')
                                {
                                    $level_no=$result[csf('level_no')];
                                }
                                if($result[csf('road_no')]!='')
                                {
                                    $road_no=$result[csf('road_no')];
                                }
                                if($result[csf('block_no')]!='')
                                {
                                    $block_no=$result[csf('block_no')];
                                }
                                if($result[csf('city')]!='')
                                {
                                    $city=$result[csf('city')];
                                }
                                $company_address[$result[csf('id')]]=$plot_no.'&nbsp'.$level_no.'&nbsp'.$road_no.'&nbsp'.$block_no.'&nbsp'.$city;
                            }
                        ?></td>		   				  
				</tr>
				<tr>
				   <td width="100" align="left"> <b style="font-size:20px;">To :</b> </td>
				
				   <td width="350" colspan="2" align="left"> <b style="font-size:20px;"><?
                    if($pay_mode_id==5 || $pay_mode_id==3){
                        echo $company_library[$supplier_id];
                    }
                    else{
                        echo $supplier_name_arr[$supplier_id];
                    }
                    ?></b></td>
				 <td width="100" align="left"><b>Sample Req No :</b> </td>	
				 <td width="200" colspan="4" align="left"><b><?=implode(", ",$booking_req_ref[$txt_booking_no]);?></b></td>	
				 
				</tr>
				<tr>
				   <td width="100" align="left"><b style="font-size:20px;">  Attn.  :</b></td>
				   <td width="150" colspan="2" align="left"><b style="font-size:20px;"><? echo $attention;     ?></b></td>
				   <td width="150" align="left"> <b style="font-size:20px;">Buyer’s Name:</b></td>	
				   <td width="200" colspan="4" align="left"><b style="font-size:20px;"><? echo $buyer_name_arr[$buyer_id].'/'.$brand_str; ?></b></td>	
				 
				</tr>
				<tr>
				   <td width="100" align="left"><b style="font-size:20px;"> Team Leader  :</b></td>
				   <td width="350" colspan="2" align="left"><b style="font-size:20px;"><?=implode(",",$team_leader_list[$txt_booking_no]);?></b></td>
				   <td width="100" align="left"><b style="font-size:20px;">Style Ref  :</b></td>	
				   <td width="200" colspan="4" align="left"><b style="font-size:20px;"><?=implode(", ",$booking_style_ref[$txt_booking_no]);?> </b></td>	
				</tr>
				<tr>
				   <td width="100" align="left"><b style="font-size:20px;">Dealing Merchant  :</b></td>
				   <td width="350" colspan="2" align="left"><b style="font-size:20px;"><?=implode(",",$dealing_merchant_list[$txt_booking_no]);?> </b></td>
				   <td width="100" align="left"><b style="font-size:20px;">Delivery Place   :</b></td>	
				   <td width="200" colspan="4" align="left"><b style="font-size:20px;"><?=$delivery_add;?> </b></td>	
				</tr>
				<tr>
				  
				   <td width="100"  align="left"><b style="font-size:20px;">Factory Merchant  :</b></td>
				   <td width="200" colspan="2" align="left"><b style="font-size:20px;"><?=implode(",",$factory_merchant_list[$txt_booking_no]);?></b></td>
				   <td width="100" align="left"><b style="font-size:20px;">Delivery Date   :</b></td>	
				   <td width="200" colspan="4" align="left"><b style="font-size:20px;"><?= change_date_format($delivery_date,'dd-mm-yyyy','-') ;?> </b></td>	
				</tr>
				<tr>
				  
				   <td width="100"  align="left"><b style="font-size:20px;">Source  :</b></td>
				   <td width="200" colspan="2" align="left"><b style="font-size:20px;"><?=$source[$source_id];?></b></td>
				   <td width="100" align="left"><b style="font-size:20px;">Pay Mode   :</b></td>	
				   <td width="200" colspan="3" align="left"><b style="font-size:20px;"><?=$pay_mode[$pay_mode_id];?> </b></td>	
				</tr>
				<tr>
				  <td width="100"  align="left"><b style="font-size:20px;">Remarks  :</b></td>
				  <td width="200" colspan="6" align="left"><b style="font-size:20px;"><?=$remarks;?></b></td>
			   </tr>

          	</table>


          <!--==============================================AS PER GMTS COLOR START=========================================  -->
		<?

		$requisition_number="";
		$Style_ref="";
		$nameArray_job_po=sql_select( "select b.requisition_number,b.style_ref_no,b.internal_ref from wo_non_ord_samp_booking_dtls a,sample_development_mst b where booking_no='$txt_booking_no' and b.id=a.style_id and a.status_active =1 and b.status_active=1 group by b.requisition_number,b.style_ref_no,b.internal_ref order by b.requisition_number");

		

		foreach($nameArray_job_po as $nameArray_job_po_row){
			$requisition_number=$nameArray_job_po_row[csf('requisition_number')];
			$Style_ref=$nameArray_style_ref_row[csf('style_ref_no')];
		$nameArray_item=sql_select("SELECT a.style_id, a.trim_group from sample_development_mst c,wo_non_ord_samp_booking_dtls a, lib_item_group b where b.id=a.trim_group and c.id=a.style_id and a.booking_no='$txt_booking_no' and c.requisition_number='".$nameArray_job_po_row[csf('requisition_number')]."' and a.status_active =1 and a.is_deleted=0 group by a.style_id, a.trim_group order by a.trim_group");

		$nameseason=sql_select( "select b.sample_prod_qty  from  sample_development_mst a, sample_development_dtls b  where  a.id=b. sample_mst_id  and a.requisition_number='".$nameArray_job_po_row[csf('requisition_number')]."'");
				foreach ($nameseason as $season_row)
				{
					$sample_qty+=$season_row[csf('sample_prod_qty')];
				}
		if(count($nameArray_item)>0){
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('internal_ref')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
			$file_nos=rtrim($job_file_no[$nameArray_job_po_row[csf('requisition_number')]],',');
			$file_nos=implode(",",array_unique(explode(",",$file_nos)));
			$job_no=$nameArray_job_po_row[csf('requisition_number')];
        ?>

        <table border="1" align="left" class="rpt_table"  cellpadding="0" width="100%" cellspacing="0" rules="all" >
            <tr>
                <td colspan="13" align="">
                <table width="100%" style="table-layout: fixed;">
                <tr>
                <td style="font-size:22px;" width="60%" align="left"><strong><? echo "Style Ref:".$nameArray_job_po_row[csf('style_ref_no')];?>, <? echo "Int. Ref:".$nameArray_job_po_row[csf('internal_ref')]; if($file_nos!='' || $file_nos!=0 ) ; echo " &nbsp;  Sample Qty.:&nbsp;".$sample_qty; ?></strong></td>
                </tr>
                </table>
                </td>
            </tr>
            <tr>
                <td align="center" style="border:1px solid black;font-size:18px;"><strong>Sl</strong> </td>
                <td align="center" style="border:1px solid black;font-size:18px;"><strong>Item Group</strong> </td>
                <td align="center" style="border:1px solid black;font-size:18px;"><strong>Item Description</strong> </td>
				<td align="center" style="border:1px solid black;font-size:18px;"><strong>Gmts Color</strong></td>
                <td align="center" style="border:1px solid black;font-size:18px;"><strong>Item Color</strong></td>
				<td align="center" style="border:1px solid black;font-size:18px;"><strong>Gmts Size</strong></td>
                <td align="center" style="border:1px solid black;font-size:18px;"><strong>Item Size</strong></td>
                <td style="border:1px solid black;font-size:18px;" align="center"><strong>Qty</strong></td>
                <td style="border:1px solid black;font-size:18px;" align="center"><strong>UNIT</strong></td>
				  <? if($show_comment==1) {?>
                <td style="border:1px solid black;font-size:18px;" align="center"><strong>Rate</strong></td>
                <td style="border:1px solid black;font-size:18px;" align="center"><strong>Amount</strong></td>
				<td style="border:1px solid black;font-size:18px;" align="center"><strong>Currency</strong></td>
                <td style="border:1px solid black;font-size:18px;" align="center"><strong>Remarks</strong></td>
                <? }?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item){
			$i++;
            $nameArray_item_description=sql_select( "select a.style_id,min(b.id) as bid,a.GMTS_COLOR,a.FABRIC_COLOR,a.GMTS_SIZE,a.ITEM_SIZE,sum(a.TRIM_QTY) as cons,avg(a.rate) as rate, sum(a.AMOUNT) as amount,a.REMARKS,a.FABRIC_DESCRIPTION from wo_non_ord_samp_booking_dtls a,  sample_development_mst b where a.style_id= b.id and  a.booking_no='$txt_booking_no' and b.REQUISITION_NUMBER='".$nameArray_job_po_row[csf('REQUISITION_NUMBER')]."'    and a.trim_group=".$result_item[csf('trim_group')]." and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.style_id, a.GMTS_COLOR,a.FABRIC_COLOR,a.GMTS_SIZE,a.ITEM_SIZE,a.REMARKS,a.FABRIC_DESCRIPTION order by bid ");
            ?>
            <tr>
                <td align="center" style="border:1px solid black;font-size:18px;" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <? echo $i; ?>
                </td>
                <td align="center"  style="border:1px solid black;font-size:18px;" rowspan="<? echo count($nameArray_item_description)+1; ?>">
                <?
				echo $trim_group_library[$result_item[csf('trim_group')]]."<br/>";
				?>
                </td>
                <?
				$item_desctiption_total=0;
                $total_amount_as_per_gmts_color=0;
                foreach($nameArray_item_description as $result_itemdescription){
                ?>
                <td  style="border:1px solid black;text-align:center;font-size:18px;"><?  if($result_itemdescription[csf('FABRIC_DESCRIPTION')]){ echo $result_itemdescription[csf('FABRIC_DESCRIPTION')];} ?> </td>

                <td  style="border:1px solid black; text-align:center;font-size:18px;">
               <? echo $color_library[$result_itemdescription[csf('GMTS_COLOR')]]; ?>
                </td>
                <td  style="border:1px solid black; text-align:center;font-size:18px;">
               <? echo $color_library[$result_itemdescription[csf('FABRIC_COLOR')]]; ?>
                </td>
				<td  style="border:1px solid black; text-align:center;font-size:18px;">
               <? echo $size_library[$result_itemdescription[csf('GMTS_SIZE')]]; ?>
                </td>
                <td  style="border:1px solid black; text-align:center;font-size:18px;">
               <? echo $result_itemdescription[csf('ITEM_SIZE')]; ?>
                </td>
                <td  style="border:1px solid black; text-align:center;font-size:18px;">
				<?

				echo number_format($result_itemdescription[csf('cons')],4);
				$item_desctiption_total += $result_itemdescription[csf('cons')] ;
				$trims_item_arr[$result_item[csf('trim_group')]][$result_itemdescription[csf('FABRIC_DESCRIPTION')]][$order_uom_arr[$result_item[csf('trim_group')]]]['cons']+=$result_itemdescription[csf('cons')];
				$trims_item_arr[$result_item[csf('trim_group')]][$result_itemdescription[csf('FABRIC_DESCRIPTION')]][$order_uom_arr[$result_item[csf('trim_group')]]]['amount']+=$result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				$amount=number_format($result_itemdescription[csf('cons')],4)*$result_itemdescription[csf('rate')];
				?>
                </td>
                <td style="border:1px solid black;text-align:center;font-size:18px;"><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
				 <? if($show_comment==1) {?>
                <!-- <td style="border:1px solid black; text-align:right;font-size:18px;" title="Avg Rate"><? echo $result_itemdescription[csf('amount')]/$result_itemdescription[csf('cons')]; ?> </td> -->
				<td style="border:1px solid black; text-align:right;font-size:18px;" title="Avg Rate"><? echo $result_itemdescription[csf('rate')] ?> </td>

                <td style="border:1px solid black; text-align:right;font-size:18px;">
                <?
                // $amount_as_per_gmts_color = $result_itemdescription[csf('amount')];
				// $amount_as_per_gmts_color = $result_itemdescription[csf('cons')]*$result_itemdescription[csf('rate')];
				// echo number_format($amount_as_per_gmts_color,6);
				echo number_format($amount,6);
                // $total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
				$total_amount_as_per_gmts_color+=$amount;
				
                ?>
                </td>
				<td style="border:1px solid black; text-align:center;font-size:18px;"><p><? echo $currency[$currency_id]; ?> </p></td>
                 <td style="border:1px solid black; text-align:right;font-size:18px;"><p><? echo $result_itemdescription[csf('REMARKS')]; ?></p></td>
                 <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
                <td style="border:1px solid black;  text-align:right;font-size:18px;" colspan="5"><strong> Item Total</strong></td>
                <td style="border:1px solid black;  text-align:right;font-size:18px;; font-weight:bold;"><?

					echo number_format($item_desctiption_total ,4);
				 ?></td>
                <td style="border:1px solid black;  text-align:center;font-size:18px;"><? echo $uom_text; ?></td>
				 <? if($show_comment==1) {?>
                <td style="border:1px solid black; text-align:right;font-size:18px;"></td>

                <td style="border:1px solid black; text-align:right;font-size:18px;">
                <?
                echo number_format($total_amount_as_per_gmts_color,6);
                $grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
                ?>
                </td>

                <td>&nbsp;  </td>
                <? } ?>
            </tr>
            <?
            }
            ?>
            <tr>
			<? if($show_comment==1) {?>
                <td align="right" style="border:1px solid black;font-size:18px;"  colspan="10"><strong>Total</strong></td>
				 

                <td  style="border:1px solid black;  text-align:right;font-size:18px;"><?  echo number_format($grand_total_as_per_gmts_color,6); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>

                <td>&nbsp; </td>
				<td>&nbsp; </td>
                <? } ?>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <!--==============================================AS PER GMTS COLOR END=========================================  -->



         

    

        

         <!--==============================================NO NENSITIBITY START=========================================  -->
		<?
		$nameArray_item=sql_select("SELECT a.pre_cost_fabric_cost_dtls_id, a.trim_group, b.rate_cal_parameter from wo_non_ord_samp_booking_dtls a join lib_item_group b on b.id=a.trim_group where a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.rate_cal_parameter not in (2,14) group by a.pre_cost_fabric_cost_dtls_id, a.trim_group, b.rate_cal_parameter order by a.trim_group");
		if(count($nameArray_item)>0)
		{
			$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
			$po_ids=array_unique(explode(",",$po_ids));
			$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
			$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
			$file_nos=rtrim($job_file_no[$nameArray_job_po_row[csf('job_no')]],',');
			$file_nos=implode(",",array_unique(explode(",",$file_nos)));
			$po_no_qty=0;
			$job_no=$nameArray_job_po_row[csf('job_no')];
			foreach($po_ids as $poid)
			{
				//echo $poid.', ';
				$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
			} 

			//$po_quantity[$result_job[csf('id')]];
			if($show_comment==1) $tdColspan=6; else $tdColspan=3;
        ?>
        <table border="1" align="left" class="rpt_table" cellpadding="0" width="1320" cellspacing="0" style="table-layout: fixed;"  rules="all" >
            <tr>
                <td colspan="5" width="645" style="word-break:break-all;font-size:18px; "><strong>NO sensitive  (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Int. Ref:".implode(",",$job_ref_no[$job_no]); echo " &nbsp;Po Qty.:&nbsp;".$po_no_qty*$set_ratio[0][csf('set_item_ratio')]; ?></strong></td>
                <td width="530" colspan="<?=$tdColspan;?>" style="word-break:break-all; font-weight:bold;font-size:18px;">Po No: <? echo implode(", ",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
            </tr>
            <tr>
                <td width="25" style="border:1px solid black;font-size:18px;"><strong>Sl</strong> </td>
                <td width="120" style="border:1px solid black;font-size:18px;"><strong>Item Group</strong> </td>
                <td width="200" style="border:1px solid black;font-size:18px;"><strong>Item Description</strong> </td>
                <td width="150" style="border:1px solid black;font-size:18px;"><strong>Item Color</strong> </td>
                <td width="80" align="center" style="border:1px solid black;font-size:18px;"><strong>Qty per Unit</strong></td>
                <td width="100" align="center" style="border:1px solid black;font-size:18px;"><strong>Qty</strong></td>
                <td width="80" style="border:1px solid black;font-size:18px;" align="center"><strong>UOM</strong></td>
				<? if($show_comment==1) {?>
                <td width="80" style="border:1px solid black;font-size:18px;" align="center"><strong>Rate</strong></td>
                <td width="100" style="border:1px solid black;font-size:18px;" align="center"><strong>Amount</strong></td>
				<td width="220" style="border:1px solid black;font-size:18px;" align="center"><strong>Currency</strong></td>
                <td width="220" style="border:1px solid black;font-size:18px;" align="center"><strong>Remarks</strong></td>
                 <? } ?>
            </tr>
            <?
			$i=0;
            $grand_total_as_per_gmts_color=0;
            foreach($nameArray_item as $result_item)
            {
				$i++;
				$nameArray_item_description=sql_select( "select a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.remarks,b.item_color,avg(b.rate) as rate, sum(b.amount) as amount from wo_non_ord_samp_booking_dtls a, wo_trim_book_con_dtls b where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.trim_group=".$result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, b.description, b.brand_supplier,b.remarks,b.item_color");
			
				?>
				<tr>
					<td style="border:1px solid black;font-size:18px;" rowspan="<? echo count($nameArray_item_description)+1; ?>"><? echo $i; ?></td>
					<td align="center" style="word-break:break-all; border:1px solid black;font-size:18px;" rowspan="<? echo count($nameArray_item_description)+1; ?>">
						<?
						echo $trim_group_library[$result_item[csf('trim_group')]]."<br/>";
						echo implode(",",$booking_country_arr[$result_item[csf('pre_cost_fabric_cost_dtls_id')]][0]);
						?>
					</td>
					<?
					$color_tatal=0;
					$total_amount_as_per_gmts_color=0;
					foreach($nameArray_item_description as $result_itemdescription)
					{
						$item_desctiption_total=0;
						?>
						<td style="word-break:break-all; border:1px solid black;font-size:18px;"><? if($result_itemdescription[csf('description')]){ echo $result_itemdescription[csf('description')];} ?> </td>
					
						<td style="word-break:break-all; border:1px solid black;font-size:18px;"><? echo $color_library[$result_itemdescription[csf('item_color')]]; ?> </td>
						<td style="word-break:break-all; border:1px solid black; text-align:left;font-size:18px;">
						<?
						$trims_remark=$trims_remark_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]]['remark'];
						$calUom=$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][2];
						$calQty=explode("_",$trims_qtyPerUnit_arr[$result_itemdescription[csf('pre_cost_fabric_cost_dtls_id')]][1]);
						if($calUom && end($calQty)){
						echo "1".$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]."=".prev($calQty)." ".$calUom;
						?>
						</td>
						<?
					}

					if($db_type==0)
					{
					 $nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons from wo_non_ord_samp_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no  and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and  b.description='". $result_itemdescription[csf('description')]."' and b.brand_supplier='".$result_itemdescription[csf('brand_supplier')]."' and b.item_color='".$result_itemdescription[csf('item_color')]."'");
					}
					if($db_type==2)
					{
						$nameArray_color_size_qnty=sql_select("select sum(b.requirment) as cons, sum(b.amount) as amount from wo_non_ord_samp_booking_dtls a, wo_trim_book_con_dtls b  where a.id= b.wo_trim_booking_dtls_id  and a.booking_no=b.booking_no and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.sensitivity=0 and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trim_group=". $result_item[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$result_item[csf('pre_cost_fabric_cost_dtls_id')]." and nvl( b.description,0)=nvl('". $result_itemdescription[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$result_itemdescription[csf('brand_supplier')]."',0) and nvl(b.item_color,0)=nvl('".$result_itemdescription[csf('item_color')]."',0)");
					}

					foreach($nameArray_color_size_qnty as $result_color_size_qnty)
					{
						?>
						<td style="word-break:break-all; border:1px solid black; text-align:right">
						<?
						if($result_color_size_qnty[csf('cons')]!= "")
						{
							echo number_format($result_color_size_qnty[csf('cons')],4);
							$item_desctiption_total += $result_color_size_qnty[csf('cons')] ;
							$color_tatal+=$result_color_size_qnty[csf('cons')];
							$trims_item_arr[$result_item[csf('trim_group')]][$result_itemdescription[csf('description')]][$order_uom_arr[$result_item[csf('trim_group')]]]['cons']+=$result_color_size_qnty[csf('cons')];
				        	$trims_item_arr[$result_item[csf('trim_group')]][$result_itemdescription[csf('description')]][$order_uom_arr[$result_item[csf('trim_group')]]]['amount']+=$result_color_size_qnty[csf('amount')];
						}
						else echo "";
						?>
						</td>
						<?
					}
					?>
					<td style="word-break:break-all; border:1px solid black; text-align:center;font-size:18px; "><? echo $unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$result_item[csf('trim_group')]]]; ?></td>
					<? if($show_comment==1)
					{
						?>
						<td style="word-break:break-all; border:1px solid black; text-align:right;font-size:18px;"><? echo number_format($result_itemdescription[csf('rate')],6); ?> </td>
						<td style="word-break:break-all; border:1px solid black; text-align:right;font-size:18px;">
						<?
							$amount_as_per_gmts_color = $item_desctiption_total*  $result_itemdescription[csf('rate')];echo number_format($amount_as_per_gmts_color,2);
							$total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
							
						?>
						</td>
						<td style="word-break:break-all; border:1px solid black; text-align:right;font-size:18px;"><? echo $currency[$currency_id]; ?> </td>
						<td style="word-break:break-all; border:1px solid black; text-align:left;font-size:18px;"><? echo $result_itemdescription[csf('remarks')] ?> </td>
					<? } ?>
				</tr>
				<?
				}
				?>
				<tr>
					<td style="border:1px solid black;  text-align:right;font-size:18px;" colspan="3"><strong> Item Total</strong></td>
					<td style="border:1px solid black;  text-align:right; font-weight:bold;;font-size:18px;"><? if($color_tatal !='') echo number_format($color_tatal,4); ?></td>
					<td style="border:1px solid black;  text-align:center;font-size:18px;"><? echo $uom_text; ?></td>
					<? if($show_comment==1)
					{?>
						<td style="border:1px solid black; text-align:right;font-size:18px;"></td>
						<td style="border:1px solid black; text-align:right;font-size:18px;">
						<?
						echo number_format($total_amount_as_per_gmts_color,4);
						$grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
						?>
						</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					<? } ?>
				</tr>
				<?
				}
				?>
				<tr>
					<td align="right" style="border:1px solid black;font-size:18px;"  colspan="7"><strong>Total</strong></td>
					<? if($show_comment==1) {?>
					<td>&nbsp;</td>
					<td style="border:1px solid black;  text-align:right;font-size:18px;"><? echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
        <? } ?>
        </table>
        <?
		}

			$carton_nameArray_item=sql_select("SELECT a.pre_cost_fabric_cost_dtls_id, a.trim_group, b.rate_cal_parameter from wo_non_ord_samp_booking_dtls a join lib_item_group b on b.id=a.trim_group where a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."' and a.status_active =1 and a.is_deleted=0 and b.rate_cal_parameter in (2,14) group by a.pre_cost_fabric_cost_dtls_id, a.trim_group, b.rate_cal_parameter order by a.trim_group");
			
			//and a.sensitivity=0
			if(count($carton_nameArray_item)>0)
			{
				$po_ids=rtrim($po_no_arr[$nameArray_job_po_row[csf('job_no')]]['po_id']);
				$po_ids=array_unique(explode(",",$po_ids));
				$ref_nos=rtrim($job_ref_no[$nameArray_job_po_row[csf('job_no')]],',');
				$ref_nos=implode(",",array_unique(explode(",",$ref_nos)));
				$file_nos=rtrim($job_file_no[$nameArray_job_po_row[csf('job_no')]],',');
				$file_nos=implode(",",array_unique(explode(",",$file_nos)));
				$po_no_qty=0;
				$job_no=$nameArray_job_po_row[csf('job_no')];
				foreach($po_ids as $poid)
				{
					$po_no_qty+=$job_po_qty_arr[$job_no][$poid];
				}
		        ?>
		        <table border="1" align="left" class="rpt_table" cellpadding="0" width="1320" cellspacing="0" style="margin-top: 10px"  rules="all" >
		            <tr>
		                <td colspan="5" width="645" style="word-break:break-all;font-size:20px; "><strong>Carton Details  (<? echo "Job NO:".$nameArray_job_po_row[csf('job_no')];?>) <? echo "Int. Ref:".implode(",",$job_ref_no[$job_no]); echo " &nbsp;Po Qty.:&nbsp;".$po_no_qty; ?></strong></td>
		                <td width="530" colspan="10" style="word-break:break-all; font-weight:bold;font-size:20px;">Po No: <? echo implode(", ",$po_no[$nameArray_job_po_row[csf('job_no')]]); ?></td>
		            </tr>
		            <tr>
		                <td width="30" style="border:1px solid black;font-size:18px;"><strong>Sl</strong> </td>
		                <td width="90" style="border:1px solid black;font-size:18px;"><strong>Item Group</strong> </td>
		                <td width="80" style="border:1px solid black;font-size:18px;"><strong>Item Description</strong> </td>
		                <td width="90" style="border:1px solid black;font-size:18px;" colspan="3"><strong>Mesurmant (L x W x H) CM</strong> </td>
		                <td width="80" align="center" style="border:1px solid black;font-size:18px;"><strong>SQM/Pcs</strong></td>
		                <td width="80" align="center" style="border:1px solid black;font-size:18px;"><strong>WO Qty</strong></td>
		                <td width="80" style="border:1px solid black;font-size:18px;" align="center"><strong>UOM</strong></td>
		                <td width="80" style="border:1px solid black;font-size:18px;" align="center"><strong>Rate</strong></td>
		                <td width="80" style="border:1px solid black;font-size:18px;" align="center"><strong>Ttl Sqm</strong></td>
		                <td width="80" style="border:1px solid black;font-size:18px;" align="center"><strong>Rate/Sqm</strong></td>
		                <td width="80" style="border:1px solid black;font-size:18px;" align="center"><strong>Amount</strong></td>
						<td width="80" style="border:1px solid black;font-size:18px;" align="center"><strong>Currency</strong></td>
		                <td width="120" style="border:1px solid black;font-size:18px;" align="center"><strong>Remarks</strong></td>
		            </tr>
		            <?
					$i=0;
		            $grand_total_as_per_gmts_color=0;
		            foreach($carton_nameArray_item as $cartondata)
		            {
						$i++;
						$cartonarray_item_description=sql_select( "SELECT a.pre_cost_fabric_cost_dtls_id, a.description, b.brand_supplier,b.item_color,avg(b.rate) as rate, sum(b.amount) as amount,  b.rate_cal_data, b.remarks from wo_non_ord_samp_booking_dtls a, wo_trim_book_con_dtls b, wo_pre_cost_trim_co_cons_dtls c where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no and c.wo_pre_cost_trim_cost_dtls_id=a.pre_cost_fabric_cost_dtls_id  and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and a.trim_group=".$cartondata[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$cartondata[csf('pre_cost_fabric_cost_dtls_id')]."  and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pre_cost_fabric_cost_dtls_id, a.description, b.brand_supplier,b.item_color, b.rate_cal_data,b.remarks");
						
						?>
						<tr>
							<td style="border:1px solid black;font-size:18px;" rowspan="<? echo count($cartonarray_item_description)+1; ?>"><? echo $i; ?></td>
							<td align="center" style="word-break:break-all; border:1px solid black;font-size:18px;" rowspan="<? echo count($cartonarray_item_description)+1; ?>">
								<?
								echo $trim_group_library[$cartondata[csf('trim_group')]]."<br/>";
								echo implode(",",$booking_country_arr[$cartondata[csf('pre_cost_fabric_cost_dtls_id')]][0]);
								?>
							</td>
							<?
							$color_tatal=0;
							$total_amount_as_per_gmts_color=0;
							foreach($cartonarray_item_description as $cartonresult)
							{
								$item_desctiption_total=0;
								$rate_cal_arr=explode("~~",$cartonresult[csf('rate_cal_data')]);
								$sqmpcs=0;
								if($cartondata[csf('rate_cal_parameter')]==2){
									$sqmpcs=($rate_cal_arr[0]+$rate_cal_arr[1]+$rate_cal_arr[5])*($rate_cal_arr[1]+$rate_cal_arr[2]+$rate_cal_arr[6])/5000;
									$ttl_msg="(L($rate_cal_arr[0])+W($rate_cal_arr[1])+LW All.($rate_cal_arr[5]))*(W($rate_cal_arr[1])+H($rate_cal_arr[2])+HW All.($rate_cal_arr[6]))/5000";	
								}
								if($cartondata[csf('rate_cal_parameter')]==14){
									$sqmpcs=($rate_cal_arr[0]*$rate_cal_arr[1])/10000;
									$ttl_msg="(L($rate_cal_arr[0])+W($rate_cal_arr[1]))/10000";	
								}
								?>
								<td style="word-break:break-all; border:1px solid black;font-size:18px;"><? if($cartonresult[csf('description')]){ echo $cartonresult[csf('description')];} ?> </td>
							
								<td title="<? echo $cartondata[csf('rate_cal_parameter')];?>" style="word-break:break-all; border:1px solid black;font-size:18px;" width="30"><? echo $rate_cal_arr[0]; ?> </td>
								<td style="word-break:break-all; border:1px solid black;font-size:18px;" width="30"><? echo $rate_cal_arr[1]; ?> </td>
								<td style="word-break:break-all; border:1px solid black;;font-size:18px;" width="30">
								<? echo $rate_cal_arr[2];?></td>
								<td title="Formula=<? echo $ttl_msg;?>" style="word-break:break-all; border:1px solid black; text-align:left;font-size:18px;"><? echo number_format($sqmpcs,4);?></td>
								<?
							
							if($db_type==2)
							{
								$nameArray_color_size_qnty=sql_select("SELECT sum(b.requirment) as cons,sum(b.amount) as amount  from wo_non_ord_samp_booking_dtls a, wo_trim_book_con_dtls b,wo_pre_cost_trim_co_cons_dtls c  where a.id= b.wo_trim_booking_dtls_id  and a.pre_cost_fabric_cost_dtls_id= c.wo_pre_cost_trim_cost_dtls_id and b.color_size_table_id=c.color_size_table_id and a.po_break_down_id=c.po_break_down_id and b.po_break_down_id=c.po_break_down_id  and a.po_break_down_id=c.po_break_down_id  and a.booking_no=b.booking_no and a.booking_no='$txt_booking_no' and a.job_no='".$nameArray_job_po_row[csf('job_no')]."'  and  a.status_active =1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.trim_group=". $cartondata[csf('trim_group')]." and a.pre_cost_fabric_cost_dtls_id=".$cartondata[csf('pre_cost_fabric_cost_dtls_id')]." and nvl( a.description,0)=nvl('". $cartonresult[csf('description')]."',0) and nvl(b.brand_supplier,0)=nvl('".$cartonresult[csf('brand_supplier')]."',0) and nvl(b.item_color,0)=nvl('".$cartonresult[csf('item_color')]."',0) and nvl(b.rate_cal_data,0)=nvl('".$cartonresult[csf('rate_cal_data')]."',0)");
								//and a.sensitivity=0 
							}
								
							foreach($nameArray_color_size_qnty as $result_color_size_qnty)
							{
								?>
								<td style="word-break:break-all; border:1px solid black; text-align:right">
								<?
								$ttl_sqm=0;
								
								if($result_color_size_qnty[csf('cons')]!= "")
								{
									echo number_format($result_color_size_qnty[csf('cons')],4);
									$item_desctiption_total += $result_color_size_qnty[csf('cons')] ;
									$color_tatal+=$result_color_size_qnty[csf('cons')];
									$ttl_sqm=$result_color_size_qnty[csf('cons')]*$sqmpcs;
									$trims_item_arr[$cartondata[csf('trim_group')]][$cartonresult[csf('description')]][$order_uom_arr[$cartondata[csf('trim_group')]]]['cons']+=$result_color_size_qnty[csf('cons')];
				        			$trims_item_arr[$cartondata[csf('trim_group')]][$cartonresult[csf('description')]][$order_uom_arr[$cartondata[csf('trim_group')]]]['amount']+=$ttl_sqm*$rate_cal_arr[3];
								}
								else echo "";
								?>
								</td>
								<?
							}
							?>
							<td style="word-break:break-all; border:1px solid black; text-align:center;font-size:18px; "><? echo $unit_of_measurement[$order_uom_arr[$cartondata[csf('trim_group')]]]; $uom_text=$unit_of_measurement[$order_uom_arr[$cartondata[csf('trim_group')]]]; ?></td>
							<td style="word-break:break-all; border:1px solid black; text-align:right;font-size:18px;" title="<? echo $sqmpcs.'*'.$rate_cal_arr[3];?>"><? echo number_format($sqmpcs*$rate_cal_arr[3],4); ?> </td>
							<td style="word-break:break-all; border:1px solid black; text-align:right;font-size:18px;"><? echo number_format($ttl_sqm,4); ?> </td>
							<td style="word-break:break-all; border:1px solid black; text-align:right;font-size:18px;"><? echo number_format($rate_cal_arr[3],6); ?> </td>
							<td style="word-break:break-all; border:1px solid black; text-align:right;font-size:18px;" title="<? echo $ttl_sqm.'*'.$rate_cal_arr[3]?>">
							<?
								$amount_as_per_gmts_color = $ttl_sqm* $rate_cal_arr[3];
								echo number_format($amount_as_per_gmts_color,4);
								$total_amount_as_per_gmts_color+=$amount_as_per_gmts_color;
							?>
							</td>
							<td style="word-break:break-all; border:1px solid black;font-size:18px;"><? echo $currency[$currency_id]; ?> </td>
							<td style="word-break:break-all; border:1px solid black;font-size:18px;"><? echo $cartonresult[csf('remarks')]; ?> </td>
						</tr>
						<?
						}
						?>
						<tr>
							<td style="border:1px solid black;  text-align:right;font-size:18px;" colspan="5"><strong> Item Total</strong></td>
							<td style="border:1px solid black;  text-align:right; font-weight:bold;font-size:18px;"><? if($color_tatal !='') echo number_format($color_tatal,4); ?></td>
							<td style="border:1px solid black;  text-align:center"><? echo $uom_text; ?></td>
							<td style="border:1px solid black; text-align:right;font-size:18px;"></td>
							<td style="border:1px solid black; text-align:right;font-size:18px;"></td>
							<td style="border:1px solid black; text-align:right;font-size:18px;"></td>
							<td style="border:1px solid black; text-align:right;font-size:18px;">
							<?
							echo number_format($total_amount_as_per_gmts_color,4);
							$grand_total_as_per_gmts_color+=$total_amount_as_per_gmts_color;
							?>
							</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<?
						}
						?>
						<tr>
							<td align="right" style="border:1px solid black;font-size:18px;"  colspan="7"><strong>Total</strong></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td style="border:1px solid black;  text-align:right;font-size:18px;"><? echo number_format($grand_total_as_per_gmts_color,4); $booking_grand_total+=$grand_total_as_per_gmts_color; ?></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
		        
		        </table>
		        <?
			}
		}
		?>
        <!--==============================================NO NENSITIBITY END=========================================  -->

		<?
       $mcurrency="";
	   $dcurrency="";
	   if($currency_id==1)
	   {
		$mcurrency='Taka';
		$dcurrency='Paisa';
	   }
	   if($currency_id==2)
	   {
		$mcurrency='USD';
		$dcurrency='CENTS';
	   }
	   if($currency_id==3)
	   {
		$mcurrency='EURO';
		$dcurrency='CENTS';
	   }
	   ?>

<br>
<br>
<table border="1" align="left" class="rpt_table"  cellpadding="0" width="50%" cellspacing="0" rules="all" >
				<tr>
					<td style="border:1px solid black;font-size:18px;" align="center" colspan="6"><strong>Summary Report</strong> </td>
						
					</tr>
					<tr>
					<td style="border:1px solid black;font-size:18px;" align="center"><strong>Sl</strong> </td>
						<td style="border:1px solid black;font-size:18px;" align="center"><strong>Item Group</strong> </td>
						<td style="border:1px solid black;font-size:18px;" align="center"><strong>Description</strong> </td>
						<td style="border:1px solid black;font-size:18px;" align="center"><strong>Order Uom</strong> </td>
						
						<td style="border:1px solid black;font-size:18px;" align="center"><strong>Total Qnty</strong> </td>
						<? if($show_comment==1) {?>
						<td style="border:1px solid black;font-size:18px;" align="center"><strong>Total Amount</strong> </td>
						<? } ?>
					</tr>
					<?
					//print_r($trims_item_arr);
					$t=1;$total_item_cons=$total_item_amount=0;
					foreach($trims_item_arr as $trim_id=>$trim_data )
					{
						foreach($trim_data as $trimdesc =>$trim_desc )
						{
							foreach($trim_desc as $uom_id=>$val )
							{
					?>
					<tr>
						<td style="border:1px solid black;font-size:18px;"> <? echo $t; ?> </td>
							<td align="center" style="border:1px solid black;font-size:18px;"> <? echo $trim_group_library[$trim_id];?> </td>
							<td align="center" style="border:1px solid black;font-size:18px;"> <? echo $trimdesc;?> </td>
							<td align="center" style="border:1px solid black;font-size:18px;"> <? echo $unit_of_measurement[$uom_id];?> </td>
							<td align="right" style="border:1px solid black;font-size:18px;"> <? echo number_format($val['cons'],0);?> </td>
							<? if($show_comment==1) {?>
							<td align="right" style="border:1px solid black;font-size:18px;"> <? echo number_format($val['amount'],6);?> </td>
							<? } ?>
						</tr>
						<?
							$t++;
							$total_item_cons+=$val['cons'];
							$total_item_amount+=$val['amount'];
							}
					    	}
						}
						?>
					<tr>
					
					<td colspan="4" align="right" > <b style="font-size:20px;">Total</b> </td>
					<td align="right"><b style="font-size:20px;"> <? echo number_format($total_item_cons);?></b> </td>
					<? if($show_comment==1) {?>
					
					<td align="right"><b style="font-size:20px;"> <? echo number_format($total_item_amount,6);?></b> </td>
					<? } ?>
					</tr>

		</table>




    
           <table border="1" align="left" class="rpt_table" cellpadding="0" width="100%" cellspacing="0" rules="all" style="margin-top: 10px">
              	<tr style="border:1px solid black;">
                    <td width="30%" style="border:1px solid black;font-size:18px; text-align:left">Total Booking Amount</td>
                    <td width="70%" style="border:1px solid black;font-size:18px;"><? echo number_format($booking_grand_total,4);?></td>
                </tr>
                <tr style="border:1px solid black;">
                    <td width="30%" style="border:1px solid black;font-size:18px; text-align:left">Total Booking Amount (in word)</td>
                    <td width="70%" style="border:1px solid black;font-size:18px;"><? echo number_to_words(def_number_format($booking_grand_total,4,""),$mcurrency, $dcurrency);?></td>
                </tr>
           </table>
         <br/>
		  <!-- <table border="1" align="left" class="rpt_table" cellpadding="0" width="100%" cellspacing="0" rules="all" style="margin-top: 10px">
            <tr>
                <td  style="word-break:break-all"><? //echo get_spacial_instruction($txt_booking_no); ?></td>           
                
            </tr>
        </table>  -->

		<?
					
					$mst_id=$txt_booking_no; $width="100%"; $entry_form=143;
					
						if ($entry_form != '') {$entry_form_con = " and entry_form=$entry_form";}
					
						$data_array = sql_select("select id, terms from  wo_booking_terms_condition where   booking_no='" . str_replace("'", "", $mst_id) . "' $entry_form_con   order by id asc");
						$tot_row=count($data_array)/2;
						//echo $tot_row;
						$k=1;
						foreach($data_array as $row)
						{
							if($k<=$tot_row)
							{
							$term_bookingArr[$row[csf('id')]]['terms']=$row[csf('terms')];
							}
							else
							{
							$other_term_bookingArr[$row[csf('id')]]['terms']=$row[csf('terms')];	
							}
							$k++;
						}
						
			if (count($data_array) > 0) {
					?>
					<br>
				<table align="left"  width="<?=$width;?>" align="center"   border="0" cellpadding="0" cellspacing="0" >
					<tr>
						<td valign="top">
					
							<table   width="650" class="rpt_table"   align="center"  border="1" cellpadding="0" cellspacing="0" rules="all">
							<thead>
								<tr style="border:1px solid black;">
								<th width="3%" style="border:1px solid black;">Sl</th>
								<th width="45%" style="border:1px solid black;">Special Instruction</th>
								</tr>
							</thead>
							<tbody>
								<?
						
								//print_r($term_bookingArr);
								$sl=1;
								foreach ($term_bookingArr as $term=>$row) {
									?>
									<tr id="settr_1" align="" style="border:1px solid black;">
										<td align="center" style="border:1px solid black;text-align:center"><?=$sl;?></td>
										<td style="border:1px solid black; font-weight:bold"><?=$row['terms'];?></td>
										<?
									$sl++;
								}?>
							</tbody>
							</table>
						</td>
						<!--1st part end-->
						<?
						$sl2=$sl;
						if (count($other_term_bookingArr) > 0) {
						?>
							<td valign="top">
								<table  width="650" class="rpt_table"   align="center"  border="1" cellpadding="0" cellspacing="0" rules="all">
										<thead>
											<tr style="border:1px solid black;">
											<th width="3%" style="border:1px solid black;" >Sl</th>
											<th width="45%" style="border:1px solid black;">Special Instruction</th>
											</tr>
										</thead>
										<tbody>
										<?
										foreach ($other_term_bookingArr as $term2=>$row2) {
											?>
											<tr id="settr_2" align="" style="border:1px solid black;">
												<td align="center" style="border:1px solid black; text-align:center"><?=$sl2;?></td>
												<td style="border:1px solid black; font-weight:bold"><?=$row2['terms'];?></td>
											<?
											$sl2++;
										}
								
									?>
									</tbody>
								</table>
						
							</td> 
					<?	}?>   
				</tr>
			</table>
			<?	}?>	
		
    
	  <br>

    <div  style="margin-top:-50px;">
         <?
          echo signature_table(115, $cbo_company_name, "1330px", 1);
		 ?>
   </div>
	<?
  

    $html = ob_get_contents();
	  ob_clean();
	  list($is_mail_send,$mail,$mail_body)=explode('___',$mail_send_data);
	  if($is_mail_send==1){
		  require_once('../../../mailer/class.phpmailer.php');
		  require_once('../../../auto_mail/setting/mail_setting.php');
		  $mailBody = preg_replace("/<img[^>]+\>/i", " ", $html)."<br>".$mail_body; 
			  
		  $mailToArr=array();
		  $mailSql = "select b.EMAIL  from wo_non_ord_samp_booking_mst a,LIB_SUPPLIER b where b.id=a.supplier_id and a.booking_no='$txt_booking_no'";
		  //echo $mailSql;die;
		  $mailSqlRes=sql_select($mailSql);
		  foreach($mailSqlRes as $rows){
			  if($rows[EMAIL]){$mailToArr[]=$rows[EMAIL];}
		  }
		  
		  
		  $mailSql = "SELECT c.email_address as EMAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=97 and b.mail_user_setup_id=c.id  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
		  //echo $mailSql;die;
		  $mailSqlRes=sql_select($mailSql);
		  foreach($mailSqlRes as $rows){
			  if($rows[EMAIL]){$mailToArr[]=$rows[EMAIL];}
		  }
		  
		  if($mail!=''){$mailToArr[]=$mail;}

		  //Un-approve request mail......................................................
		$user_id=$_SESSION['logic_erp']['user_id'];
		$process_id=return_field_value("id", "wo_non_ord_samp_booking_mst", "BOOKING_NO='".str_replace("'","",$txt_booking_no)."'");
		$approved_no=return_field_value("MAX(approved_no) as approved_no","approval_history","entry_form=8 and mst_id=$process_id","approved_no");
		$unapproved_request=return_field_value("APPROVAL_CAUSE","fabric_booking_approval_cause","entry_form=8 and user_id=$user_id and booking_id=$process_id and approval_type=2 and approval_no='$approved_no'");//page_id=$page_id and
		
		if($unapproved_request){
			$mailToArr=array();
			if($msil_address){$mailToArr[]=$msil_address;}
			$final_app_user_mail=return_field_value("USER_EMAIL","user_passwd","id in(select APPROVED_BY from APPROVAL_HISTORY where id in(select max(id) from APPROVAL_HISTORY where mst_id=$process_id and ENTRY_FORM=8 and CURRENT_APPROVAL_STATUS=1))");
			$mailToArr[]= $final_app_user_mail;
		}
		$mailBody=$mail_body."<br>".$unapproved_request."<br><br>".$mailBody;
		//......................................................Un-approve request mail;

		  $to=implode(',',$mailToArr);
		  $subject="Trims Booking Multy Job";
		  $header=mailHeader();
		  echo sendMailMailer( $to, $subject, $mailBody, $from_mail,$att_file_arr );
		  
	  }
	  else{
		  echo $html;
	  }
	  exit();



}

if($action=="show_fabric_booking_report")
{
	extract($_REQUEST);
    $txt_booking_no=str_replace("'","",$txt_booking_no);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$id_approved_id=str_replace("'","",$id_approved_id);
	$img_path=str_replace("'","",$img_path);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<div style="width:1330px" align="center">       
    										<!--    Header Company Information         --> 
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100"> 
                <? if ($img_path==7) 
                { 
                    ?>
                    <img  src='../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
                    <?
                }
                else{
                    ?>
                    <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
                    <?
                }
                ?>
               
               </td>
               <td width="1000">                                     
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php      
                                    echo $company_library[$cbo_company_name];
                              ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">  
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,bin_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name"); 
                            foreach ($nameArray as $result)
                            { 
                            ?>
                                            Plot No: <? echo $result[csf('plot_no')]; ?> 
                                            Level No: <? echo $result[csf('level_no')]?>
                                            Road No: <? echo $result[csf('road_no')]; ?> 
                                            Block No: <? echo $result[csf('block_no')];?> 
                                            City No: <? echo $result[csf('city')];?> 
                                            Zip Code: <? echo $result[csf('zip_code')]; ?> 
                                            Province No: <?php echo $result[csf('province')];?> 
                                            Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
                                            Email Address: <? echo $result[csf('email')];?> 
                                            Website No: <? echo $result[csf('website')];
											if($result[csf('bin_no')]!='') echo "<br> BIN:".$result[csf('bin_no')];
                            }
                                            ?>   
                                         
                               </td> 
                            </tr>
                            <tr>
                             
                            <td align="center" style="font-size:20px">  
                                <strong><? if($report_title !=""){echo $report_title;} else {echo "Sample Fabric Booking -Without order";}?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if($id_approved_id==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
                             </td>
                              
                            </tr>
                      </table>
                </td>
                <td width="250" id="barcode_img_id"> 
               
               </td>       
            </tr>
       </table>
       
                <?
				$season="";
				$buyer_req_no=""; $requisition_number='';
				
				$nameseason=sql_select( "select a.season, b.buyer_req_no, a.requisition_number  from  sample_development_mst a, sample_development_dtls b, wo_non_ord_samp_booking_dtls c  where  a.id=b. sample_mst_id and a.id=c.style_id and c.booking_no=$txt_booking_no");
				foreach ($nameseason as $season_row)
				{
					$season=$season_row[csf('season')];
					$buyer_req_no=$season_row[csf('buyer_req_no')];
					$requisition_number.=$season_row[csf('requisition_number')].',';
				}
				$requisition_number=implode(",",array_filter(array_unique(explode(",",$requisition_number))));
				$txt_booking_no=str_replace("'","",$txt_booking_no);
				$fabric_source='';
                $nameArray=sql_select( "select buyer_id,pay_mode,booking_no,booking_date,supplier_id,currency_id,exchange_rate,attention,delivery_date,fabric_source from wo_non_ord_samp_booking_mst   where  booking_no='$txt_booking_no'"); 
				foreach ($nameArray as $result)
				{
					$fabric_source=$result[csf('fabric_source')];
					
					$varcode_booking_no=$result[csf('booking_no')];
					
				?>
       <table width="100%" style="border:1px solid black">                    	
            <tr>
                <td colspan="6" valign="top"></td>                             
            </tr>                                                
            <tr>
                <td width="100" style="font-size:12px"><b>Booking No </b>   </td>
                <td width="110">:&nbsp;<? echo $result[csf('booking_no')];?> </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>		
                <td width="100"><span style="font-size:12px"><b>Delivery Date</b></span></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>	
               			
            </tr>
            <tr>
                
                <td width="100"><span style="font-size:12px"><b>Buyer/Agent Name</b></span></td>
                <td width="110">:&nbsp;<? echo $buyer_name_arr[$result[csf('buyer_id')]]; ?></td>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<? 
				if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
					echo $company_library[$result[csf('supplier_id')]];
					}
					else{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
					}
				//echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
                <td width="100" style="font-size:12px"><b>Supplier Address</b></td>
               	<td width="110">:&nbsp;<? echo $supplier_address_arr[$result[csf('supplier_id')]];?></td> 
            </tr>
            
            
            <tr>
                <td width="100" style="font-size:12px"><b>Currency</b></td>
                <td width="110">:&nbsp;<? $currency_id=$result[csf('currency_id')];
				echo $currency[$currency_id]; ?></td>
             
                <td  width="100" style="font-size:12px"><b>Conversion Rate</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('exchange_rate')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
               
            </tr> 
            <tr>
                <td width="100" style="font-size:12px"><b>Season</b></td>
                <td width="110">:&nbsp;<? echo $season; ?></td>
                <td  width="100" style="font-size:12px"><b>Buyer Req. No</b></td>
                <td  width="110" >:&nbsp;<? echo $buyer_req_no; ?></td>
                <td  width="100" style="font-size:12px"><b>Req. No</b></td>
                <td  width="110" >:&nbsp;<? echo $requisition_number; ?></td>
                
            </tr> 
			<tr>
                <td width="100" style="font-size:12px"><b></b></td>
                <td width="110">:&nbsp</td>
                <td  width="100" style="font-size:12px"><b></b></td>
                <td  width="110" >:&nbsp;</td>
                <td  width="100" style="font-size:12px"><b>Pay Mode</b></td>
                <td  width="110" >:&nbsp;<? echo $pay_mode[$result[csf('pay_mode')]]; ?></td>
                
            </tr> 
        </table>  
        <?
			}
		?>
            
      <br/>  
      <? 
	 
    $sample_library=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name"  );
	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
	$sql= sql_select("select style_id, style_des, sample_type, trim_group, uom, barnd_sup_ref, gmts_color, fabric_color, gmts_size, item_size, trim_qty, rate, amount, fabric_description, id FROM wo_non_ord_samp_booking_dtls WHERE booking_no ='$txt_booking_no'  and status_active=1 and	is_deleted=0"); 
	//echo "select style_id, style_des, sample_type, trim_group, uom, barnd_sup_ref, gmts_color, fabric_color, gmts_size, item_size, trim_qty, rate, amount, fabric_description, id FROM wo_non_ord_samp_booking_dtls WHERE booking_no =$txt_booking_no  and status_active=1 and	is_deleted=0";
?>
<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
    <thead>
        <tr>
            <th width="20">Sl</th>
            <th width="60">Style Ref.</th>
            <th width="110">Style Des</th>
            <th width="100">Sample</th>
            <th width="140">Trim Group</th>
            <th width="150">Description</th>
            <th width="60">UOM</th>
            <th width="60">Brand/ Supp. Ref</th>
            <th width="80">Gmts Color</th>
            <th width="80">Item Color</th>
            <th width="80">Gmts Size</th>
            <th width="80">Item Size</th>
            <th width="60">Trim Qty</th>
            <th width="60">Rate</th>
            <th>Amount</th>
        </tr>
	</thead>
<?
$total_trim_qty=0;
$total_grey_fabric=0;
$toatl_rate=0;
$total_amount=0;

$i=1;
foreach ($sql as $row)
{
	$all_style_arr[]=$style_library[$row[csf('style_id')]];
	?>
	<tr>
        <td width="20"><? echo $i; ?></td>
        <td width="60"><? echo $style_library[$row[csf('style_id')]]; ?></td>
        <td width="110"><? echo $row[csf('style_des')]; ?></td>
        <td width="100"><? echo $sample_library[$row[csf('sample_type')]]; ?></td>
        <td width="140"><? echo $trim_group[$row[csf('trim_group')]]; ?></td>
        <td width="150"><? echo $row[csf('fabric_description')]; ?></td>
        <td width="60"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
        <td width="60"><? echo $row[csf('barnd_sup_ref')]; ?></td>
        <td width="80"><? echo $color_library[$row[csf('gmts_color')]]; ?></td>
        <td width="80"><? echo $color_library[$row[csf('fabric_color')]]; ?></td>
        <td width="80"><? echo $size_library[$row[csf('gmts_size')]]; ?></td>
        <td width="80"><? echo $row[csf('item_size')]; ?></td>
        <td width="60" align="right"><? echo number_format($row[csf('trim_qty')],4); $total_trim_qty+=$row[csf('trim_qty')]; ?></td>
        <td width="60" align="right"><? echo number_format($row[csf('rate')],4); $toatl_rate+=$row[csf('rate')]; ?></td>
        <td align="right"><? echo number_format($row[csf('amount')],4); $total_amount+=$row[csf('amount')];?></td>
	</tr>
	<?
	$i++;
}
		 $mcurrency="";
		$dcurrency="";
		if($currency_id==1)
		{
		$mcurrency='Taka';
		$dcurrency='Paisa'; 
		}
		if($currency_id==2)
		{
		$mcurrency='USD';
		$dcurrency='CENTS'; 
		}
		if($currency_id==3)
		{
		$mcurrency='EURO';
		$dcurrency='CENTS'; 
		}
?>
    <tfoot>
        <tr>
            <th width="50" colspan="12" align="right">Total </th>
            <th width="60" align="right"><? echo number_format($total_trim_qty,4); ?></th>
            <th width="60" align="right"><? echo number_format($toatl_rate,4);?></th>
            <th align="right"><? echo number_format($total_amount,4); ?></th>
        </tr>
        <tr>
            <th colspan="15" align="center">Total Amount (in word):&nbsp; <? echo number_to_words(def_number_format($total_amount,2,""),$mcurrency, $dcurrency);?></th>
            
        </tr>
    </tfoot>
</table>
        <br/>
        <br/>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="100%">
                <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
               <tr>
                <td>
					<?
                       echo get_spacial_instruction($txt_booking_no);
                    ?>
    			</td>
               </tr>
                </table>
                <!--Not Used-->
                    <table class="rpt_table" style="display:none" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all">
                	<thead>
                    	<tr>
                        	<th width="3%">Sl</th><th width="97%">Spacial Instruction</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no='$txt_booking_no'");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" align="">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <? echo $row[csf('terms')]; ?>
                                    </td>
                                </tr>
                            <?
						}
					}
					
					?>
                </tbody>
                </table>
                </td>
                
            </tr>
             <tr>
             <td width="2%">&nbsp; </td>
             </tr>
            <tr>
                 
                <td width="100%">
                  <?
	// $sql_array="select b.approved_by,b.approved_no, b.approved_date, c.user_full_name from  wo_non_ord_samp_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no=$txt_booking_no and b.entry_form=8 and  a.status_active =1 and a.is_deleted=0";
	 // $data_array=sql_select($sql_array);
	  
	$lib_designation_arr=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");
	$user_lib_designation_arr=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
	$user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	 $mst_id=return_field_value("id as mst_id","wo_non_ord_samp_booking_mst","booking_no='$txt_booking_no'","mst_id");
	 $approve_data_array=sql_select("select b.approved_by,min(b.approved_date) as approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form in(55)  group by  b.approved_by order by b.approved_by asc");
	 $unapprove_data_array=sql_select("select b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date,b.approved_no from   approval_history b where b.mst_id=$mst_id and b.entry_form in(55)  order by b.approved_date,b.approved_by");
	 
	
	
	?>
    <br/> <br/>
  		<?
          if(count($approve_data_array)>0)
			{
 		?>
       <table  width="850" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="5" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
				<th width="40%" style="border:1px solid black;">Name</th>
				<th width="30%" style="border:1px solid black;">Designation</th>
				<th width="27%" style="border:1px solid black;">Approval Date</th>
				
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($approve_data_array as $row){
			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="40%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="27%" style="border:1px solid black;text-align:center"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')]));?></td>
			
                </tr>
                <?
				$i++;
			}
				?>
            </tbody>
        </table>
		<?
		}
		?>
		<br>
		<?
		if(count($unapprove_data_array)>0)
		{
			//and approval_type=0
			$sql_unapproved=sql_select("select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form in(55,8)  and is_deleted=0 and status_active=1 and booking_id=$mst_id");
		//	echo "select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form=8  and is_deleted=0 and status_active=1 and booking_id=$mst_id";
			
			
			$unapproved_request_arr=array();
			foreach($sql_unapproved as $rowu)
			{
				if($rowu[csf('approval_cause')]!='')
				{
					$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
				}
			}
 		?>
       <table  width="850" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="6" style="border:1px solid black;">Approval/Un Approval Histry</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
				<th width="30%" style="border:1px solid black;">Name</th>
				<th width="20%" style="border:1px solid black;">Designation</th>
				<th width="5%" style="border:1px solid black;">Approval Status</th>
				<th width="20%" style="border:1px solid black;">Reason For Un Approval</th>
				<th width="22%" style="border:1px solid black;"> Date</th>
				
                </tr>
            </thead>
            <tbody>
            <?
			$i=1; 
			foreach($unapprove_data_array as $row){
			
			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="5%" style="border:1px solid black; text-align:center"><? echo 'Yes';?></td>
				<td width="20%" style="border:1px solid black;"><? echo '';?></td>
				<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); else echo "";?></td>
            </tr>
				<?
                $i++;
                $un_approved_date= explode(" ",$row[csf('un_approved_date')]);
                $un_approved_date=$un_approved_date[0];
                if($db_type==0) //Mysql
                {
                    if($un_approved_date=="" || $un_approved_date=="0000-00-00") $un_approved_date="";else $un_approved_date=$un_approved_date;
                }
                else
                {
                    if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;
                }
                
                if($un_approved_date!="")
                {
                ?>
			<tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="5%" style="border:1px solid black;text-align:center;"><? echo 'No';?></td>
				<td width="20%" style="border:1px solid black;text-align:center"><? echo $unapproved_request_arr[$mst_id];?></td>
				<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('un_approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('un_approved_date')])); else echo "";?></td>
              </tr>
				
                <?
				$i++;
				}
				
			}
				?>
            </tbody>
        </table>
		<?
		}
		?> 
                
                </td>
                </tr>
        </table>
      <?
      echo signature_table(115, $cbo_company_name, "1330px", 1);
			echo "****".custom_file_name($txt_booking_no,implode(',',$all_style_arr),$job_no);
		  ?>
       </div>
       
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>
    
       <?
      
}


if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	//var_dump($process);die();
	extract(check_magic_quote_gpc( $process )); 
	if(str_replace("'","",$txt_booking_no)!='')
	{
		$sql=sql_select("select subcon_job, embellishment_job from subcon_ord_mst where order_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($sql as $row){

			if($row[csf('subcon_job')]=="") $row[csf('subcon_job')]=$row[csf('embellishment_job')];
			$lock_another_process=$row[csf('subcon_job')];
		}
		if($lock_another_process!=''){
			echo "lockAnotherProcess**".$lock_another_process;
			die;
		}
	}
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if($db_type==0)
		{
			$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'TSN', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_non_ord_samp_booking_mst where company_id=$cbo_company_name and booking_type=5 and YEAR(insert_date)=".date('Y',time())." order by id desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		}
		else if($db_type==2)
		{
			$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'TSN', date("Y",time()), 5, "select booking_no_prefix, booking_no_prefix_num from wo_non_ord_samp_booking_mst where company_id=$cbo_company_name and booking_type=5 and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "booking_no_prefix", "booking_no_prefix_num" ));
		}
		
		$id=return_next_id( "id", "wo_non_ord_samp_booking_mst", 1 ) ;
		$field_array="id, booking_type, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, item_category, currency_id, exchange_rate, pay_mode, source, booking_date, delivery_date, supplier_id, attention, tenor,delivery_address,remarks, ready_to_approved, inserted_by, insert_date"; 
		 $data_array ="(".$id.",5,'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$cbo_company_name.",".$cbo_buyer_name.",4,".$cbo_currency.",".$txt_exchange_rate.",".$cbo_pay_mode.",".$cbo_source.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_supplier_name.",".$txt_attention.",".$txt_tenor.",".$delivery_address.",".$txt_remark.",".$cbo_ready_to_approved.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		 $rID=sql_insert("wo_non_ord_samp_booking_mst",$field_array,$data_array,0);
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");  
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_booking_no[0];
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);
				echo "0**".$new_booking_no[0];
			}
			else{
				oci_rollback($con); 
				echo "10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	
		/*if (is_duplicate_field( "sample_type_id", "wo_po_sample_approval_info", "job_no_mst=$txt_job_no and sample_type_id=$cbo_sample_type and id!=$update_id and is_deleted=0" ) == 1)
		{
			echo "11**0"; 
			die;
		}*/
			 
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==3) $is_approved=1;
		if($is_approved==1){
			echo "app1**".str_replace("'","",$txt_booking_no);
			die;
		}
		//echo "select pi_number from com_pi_master_details a,com_pi_item_details b where a.id=b.pi_id  and b.work_order_no=$txt_booking_no and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0";
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			die;
		}
		
		$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no  and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($recv_number){
			echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
			die;
		}
		
		$field_array="buyer_id*item_category*currency_id*exchange_rate*pay_mode*source*booking_date*delivery_date*supplier_id*attention*tenor*delivery_address*remarks*ready_to_approved*updated_by*update_date"; 
		$data_array ="".$cbo_buyer_name."*4*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$cbo_source."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_supplier_name."*".$txt_attention."*".$txt_tenor."*".$delivery_address."*".$txt_remark."*".$cbo_ready_to_approved."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_update("wo_non_ord_samp_booking_mst",$field_array,$data_array,"booking_no","".$txt_booking_no."",0);
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$txt_booking_no);
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
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==3) $is_approved=1;
		
		if($is_approved==1){
			echo "app1**".str_replace("'","",$txt_booking_no);
			die;
		}
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			die;
		}
		$recv_number=return_field_value( "recv_number", "inv_receive_master a,inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no  and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		if($recv_number){
			echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
			die;
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("wo_non_ord_samp_booking_mst",$field_array,$data_array,"booking_no","".$txt_booking_no."",1);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID){
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="sample_description_popup")
{
echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
extract($_REQUEST);

?>
<script> 
function js_set_value(data)
{
	var data=data.split('_');
	document.getElementById('style_id').value=trim(data[0]);
	document.getElementById('style_no').value=trim(data[1]);
	document.getElementById('sample_id').value=trim(data[2]);
    parent.emailwindow.hide();
}
</script> 
</head>
<body>
<div align="center">
<form>
<input type="hidden" id="style_id" name="style_id" />
<input type="hidden" id="style_no" name="style_no" />
<input type="hidden" id="sample_id" name="sample_id" />
<?
	//$sql= "select  a.id,a.style_ref_no,a.requisition_number, a.company_id,a.buyer_name, b.id as bid, b.sample_name, b.sample_color, b.working_factory, b.receive_date_from_factory, b.sent_to_factory_date, b.sent_to_buyer_date, b.approval_status, b.status_date, b.recieve_date_from_buyer from sample_development_mst a, sample_development_dtls b  where a.id=b.sample_mst_id and a.company_id ='$cbo_company_name' and buyer_name ='$cbo_buyer_name'  and a.is_deleted=0 and b.is_deleted=0 order by a.id DESC";
	
	  $sql= "select  a.id,a.style_ref_no, a.requisition_number, a.company_id, a.buyer_name, b.id as bid, b.sample_name, b.sample_color, b.working_factory, b.receive_date_from_factory, b.sent_to_factory_date, b.sent_to_buyer_date, b.approval_status, b.status_date, b.recieve_date_from_buyer from sample_development_mst a, sample_development_dtls b, sample_development_fabric_acc c where a.id=b.sample_mst_id and a.id=c.sample_mst_id  and (c.fabric_source!=4 or  c.fabric_source is null) and c.form_type=2 and a.company_id ='$cbo_company_name' and buyer_name ='$cbo_buyer_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.style_ref_no, a.requisition_number, a.company_id, a.buyer_name, b.id, b.sample_name, b.sample_color, b.working_factory, b.receive_date_from_factory, b.sent_to_factory_date, b.sent_to_buyer_date, b.approval_status, b.status_date, b.recieve_date_from_buyer order by a.id DESC";//ISD-23-16443
	
	$sample_name_arr=return_library_array( "select id, sample_name from lib_sample",'id','sample_name');
	$color_name_arr=return_library_array( "select id, color_name from  lib_color where status_active=1 and is_deleted=0",'id','color_name');
	
	//$arr=array (3=>$sample_name_arr,4=>$color_name_arr,9=>$approval_status);?>

	<table width="1080" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" >
        	<thead>
             <tr>
                    <th colspan="12">
                        <?
                        echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --","4" );
                        ?> 
                    </th>
                </tr>
                <tr>
                    <th width="40">SL</th>
                    <th width="80">Style Id</th>
                    <th width="100">Style</th>
                    <th width="100">Req. No</th>
                    <th width="80">Sample Name</th>
                    <th width="80">Sample Color</th>
                    <th width="100">Working Factory</th>
                    <th width="100">Buyer Recieve Date</th>
				 	<th width="100">Sent To factory</th>
					<th width="100">Submission to Buyer</th>
					<th width="100">Approval Status </th>
                    <th>Status Date</th>
                </tr> 
            </thead>
        </table>
		<div style="width:1200px; max-height:400px; overflow-y:scroll" id="">
        <table width="1080" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="tbl_list_search">
		<tbody>
		<?
		$sql_results=sql_select($sql);
			$i=1;
			//echo count($sql_results);
			foreach($sql_results as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";?>
				 <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('style_ref_no')]."_".$row[csf('sample_name')];?>')" id="search<? echo $i;?>; ?>" style="cursor:pointer">
                    <td align="center" width="40"> <? echo $i;?></td>
					<td align="center" width="80"><p><? echo $row[csf("id")];  ?></p></td>
                    <td align="center" width="100"><p><? echo $row[csf("style_ref_no")];  ?></p></td>
                    <td align="center" width="100"><p><? echo $row[csf("requisition_number")];  ?></p></td>
                    <td align="center" width="80"><p><? echo $sample_name_arr[$row[csf("sample_name")]];  ?></p></td>
                    <td align="center" width="80"><p><? echo $color_name_arr[$row[csf("sample_color")]];  ?></p></td>
                    <td align="center" width="100"><p><? echo $row[csf("working_factory")];  ?></p></td>
					<td align="center" width="100"><p><? echo $row[csf("recieve_date_from_buyer")];  ?></p></td>
                    <td align="center" width="100"><p><? echo change_date_format($row[csf("sent_to_factory_date")]);  ?></p></td>
                    <td align="center" width="100"><p><? echo change_date_format($row[csf("sent_to_buyer_date")]);  ?></p></td>
                    <td align="center" width="100"><p><? echo $approval_status[$row[csf("approval_status")]];  ?></p></td>
                    <td align="center" width="100"><p><? echo change_date_format($row[csf("status_date")]);  ?></p></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
                   
		</table>
        </div>
	<?
	// echo  create_list_view ( "list_view1", "Style Id,Style, Req. No,Sample Name,Sample Color,Working Factory,Buyer Recieve Date,Sent To factory,Submission to Buyer,Approval Status,Status Date", "60,100,100,100,90,100,80,80,80,85,80","1005","300",0, $sql, "js_set_value", "id,style_ref_no,sample_name","", 1, "0,0,0,sample_name,sample_color,0,0,0,0,approval_status,0", $arr , "id,style_ref_no,requisition_number,sample_name,sample_color,working_factory,recieve_date_from_buyer,sent_to_factory_date,sent_to_buyer_date,approval_status,status_date", "../woven_order/requires/trims_sample_booking_without_order_controller", 'setFilterGrid("list_view1",-1);','0,0,0,0,0,0,3,3,3,0,3,3' ) ;	
	 //exit();
?>
</form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
		setFilterGrid("tbl_list_search",-1);
	</script>
</html>
<?
exit();
}

if($action=="trim_description_popup")
{
echo load_html_head_contents("Trim Desc. List","../../../", 1, 1, $unicode);
extract($_REQUEST);

?>
<script> 
function js_set_value(data)
{
	
	var data=data.split('_');
	document.getElementById('description').value=trim(data[0]);
	document.getElementById('req_qty').value=trim(data[1]);
	document.getElementById('uom_id').value=trim(data[2]);
	document.getElementById('prev_qty').value=trim(data[3]);
    parent.emailwindow.hide();
	

}
</script> 
</head>
<body>
<div align="center">
<form>
<input type="hidden" id="description" name="description" />
<input type="hidden" id="req_qty" name="req_qty" />
<input type="hidden" id="uom_id" name="uom_id" />
<input type="hidden" id="prev_qty" name="prev_qty" />
<?
	
	
	if($cbo_sample_type>0) $sample_cond="and c.sample_name_ra=$cbo_sample_type ";else $sample_cond="";
	
	 $sql_trim="select a.requisition_number,a.style_ref_no,c.req_qty_ra,c.uom_id_ra,c.sample_name_ra,c.trims_group_ra,c.description_ra from sample_development_fabric_acc c,sample_development_mst a where a.id=c.sample_mst_id and c.form_type=2 and a.id=$txt_style_id and c.trims_group_ra=$cbo_trim_group and a.status_active=1 and c.status_active=1 $sample_cond";
$trims_req_result=sql_select($sql_trim);
$sample_name_arr=return_library_array( "select id, sample_name from lib_sample",'id','sample_name');
	
	?>
     <table  width="620" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
     <caption> Description dtls</caption>
            <thead>
                <tr style="border:1px solid black;">
                <th width="30">Sl</th>
				<th width="110">Req No</th>
				<th width="100">Style Ref.</th>
				<th width="100">Sample</th>
				<th width="100">Description</th>
				<th width="100"> Req.Qty</th>
                <th width=""> Uom</th>
                </tr>
            </thead>
            <tbody>
            <?
		$sql_trim_prv="select trim_qty as  trim_qty,fabric_description,trim_group from wo_non_ord_samp_booking_dtls where style_id=$txt_style_id and trim_group=$cbo_trim_group and status_active=1 and is_deleted=0"; 
		$trims_result=sql_select($sql_trim_prv);
		foreach($trims_result as $row)
		{
			$prev_qty_arr[$row[csf('trim_group')]][$row[csf('fabric_description')]]+=$row[csf('trim_qty')];
		}
			$i=1; 
			foreach($trims_req_result as $row){
			if ($i%2==0)
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";//description_ra,req_qty_ra,uom_id_ra,uom_id_ra
			$prev_qty=$prev_qty_arr[$row[csf('trims_group_ra')]][$row[csf('description_ra')]];
			//echo $prev_qty.'ssss';
			?>
          	<tr bgcolor="<? echo $bgcolor;  ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $row[csf('requisition_number')];?>" onClick="js_set_value('<? echo $row[csf('description_ra')].'_'.$row[csf('req_qty_ra')].'_'.$row[csf('uom_id_ra')].'_'.$prev_qty;?>')">
                <td width="20"><? echo $i;?></td>
				<td width="110"><? echo $row[csf('requisition_number')];?></td>
				<td width="100"><? echo $row[csf('style_ref_no')];?></td>
				<td width="100"><? echo $sample_name_arr[$row[csf('sample_name_ra')]];?></td>
				<td width="100"><? echo $row[csf('description_ra')];?></td>
				<td width="100" align="right"><? if($prev_qty!=''){ echo $prev_qty;}else{ echo $row[csf('req_qty_ra')] ;} ;?></td>
                <td width=""><? echo $unit_of_measurement[$row[csf('uom_id_ra')]];?></td>
            </tr>
                <?
				$i++;
			}
				?>
            </tbody>
        </table>
    <?
	 exit();
?>
</form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="save_update_delete_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if(str_replace("'","",$txt_booking_no)!='')
	{
		
		$sql=sql_select("select subcon_job, embellishment_job from subcon_ord_mst where order_no=$txt_booking_no and status_active=1 and is_deleted=0");
		foreach($sql as $row){

			if($row[csf('subcon_job')]=="") $row[csf('subcon_job')]=$row[csf('embellishment_job')];
			$lock_another_process=$row[csf('subcon_job')];
		}
		if($lock_another_process!=''){
			echo "lockAnotherProcess**".$lock_another_process;
			die;
		}		
		
	}
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}		
		 
		  $item_id=str_replace("'","",$cbo_trim_group);
		   $style_id=str_replace("'","",$txt_style);
		   $description=str_replace("'","",$txt_description);
		  $trim_req_qty=str_replace("'","",$txt_trim_req_qty);
		  $wo_trim_qty=str_replace("'","",$txt_trim_qty);
		  $new_booking_no=str_replace("'","",$txt_booking_no);
		if(str_replace("'","",$txt_booking_no)!='')
		{			
			 $booking_id=return_field_value( "id", "wo_non_ord_samp_booking_mst","booking_no=$txt_booking_no");
			$sql_item=sql_select("select booking_no, trim_group from wo_non_ord_samp_booking_dtls where style_id=$style_id  and trim_group=$item_id and fabric_description='$description' and status_active=1 and is_deleted=0");
			//comment line 2042-2052 for issue id 7457
			// foreach($sql_item as $row)
			// {
			// 	$prev_booking_no=$row[csf('booking_no')];
			// 	if($prev_booking_no==$new_booking_no)
			// 	{
			// 		if(count($sql_item)>0)
			// 		{
			// 			echo "17**Duplicate Item Not Allowed";die;
			// 		}
			// 	}
			// }
		}
		
		$prev_wo_qty=return_field_value("sum(trim_qty) as  trim_qty", "wo_non_ord_samp_booking_dtls", "style_id=$style_id and trim_group=$item_id and fabric_description='$description'  and status_active=1 and is_deleted=0 and booking_no is not null","trim_qty");
		$tot_wo_qty=$wo_trim_qty+$prev_wo_qty;
		
		if($tot_wo_qty>$trim_req_qty)
		{
			echo "13**Item over qty not allowed\n"."(Prev Qty=$prev_wo_qty,Req Qty=$trim_req_qty)";die;//(Prev Qty.".$prev_wo_qty.")"
		}
	
		
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}	
		 
		 $id=return_next_id( "id", "wo_non_ord_samp_booking_dtls", 1 ) ;
		 
		 $field_array="id,booking_no,booking_mst_id,style_id,style_des,sample_type,trim_group,uom, fabric_description,barnd_sup_ref,gmts_color,fabric_color,gmts_size,item_size,trim_qty,rate,amount,remarks,inserted_by,insert_date";
		 
		 $field_array2="id,wo_non_ord_samp_book_dtls_id,booking_no,count_id,copm_one_id,percent_one,type_id,cons_ratio,cons_qnty,inserted_by,insert_date"; 
		 $wo_non_ord_samp_yarn_dtls_id=return_next_id( "id", "wo_non_ord_samp_yarn_dtls", 1 ) ;
		 $add_comma_yarn=0;
		 $gmt_color_str = str_replace("'", "", $txt_gmt_color);
		$gmt_color_nam=strtoupper(trim($gmt_color_str));
		$fab_color_str = str_replace("'", "", $txt_color);
		$fab_color_nam=strtoupper(trim($fab_color_str));
					
		  $new_array_gmts_color=array();
		  if(str_replace("'","",$txt_gmt_color)!="")
		  {
			 if (!in_array(str_replace("'","",$txt_gmt_color),$new_array_gmts_color))
			 {
				  $gmts_color_id = return_id( str_replace("'","",$txt_gmt_color), $color_library, "lib_color", "id,color_name","143");  
				  $new_array_gmts_color[$gmts_color_id]=str_replace("'","",$txt_gmt_color);
			 }
			 else $gmts_color_id =  array_search(str_replace("'","",$txt_gmt_color), $new_array_gmts_color);
		 }
		 else $gmts_color_id =0;
		
			$new_array_color=array();
			if($gmt_color_nam==$fab_color_nam)
			{
				$color_id=$gmts_color_id;
			}
			else
			{
				if(str_replace("'","",$txt_color)!="")
				{
					if (!in_array(str_replace("'","",$txt_color),$new_array_color))
					{
						$color_id = return_id( str_replace("'","",$txt_color), $color_library, "lib_color", "id,color_name","90");
						$new_array_color[$color_id]=str_replace("'","",$txt_color);
					}
					else $color_id =  array_search(str_replace("'","",$txt_color), $new_array_color);
				}
				else $color_id =0;
			}				 
			$new_array_gmts_size=array();
			if(str_replace("'","",$txt_gmts_size)!="")	 
			{	 
				 if (!in_array(str_replace("'","",$txt_gmts_size),$new_array_gmts_size))
				 {
					  $gmts_size_id = return_id( str_replace("'","",$txt_gmts_size), $size_library, "lib_size", "id,size_name","143");   
					  $new_array_gmts_size[$gmts_size_id]=str_replace("'","",$txt_gmts_size);
				 }
				 else $gmts_size_id =  array_search(str_replace("'","",$txt_gmts_size), $new_array_gmts_size); 
			 }
			 else $gmts_size_id =0;
		
			$data_array="(".$id.",".$txt_booking_no.",".$booking_id.",".$txt_style.",".$txt_style_des.",".$cbo_sample_type.",".$cbo_trim_group.",".$cbo_uom.",".$txt_description.",".$txt_barnd_sup_ref.",'".$gmts_color_id."','".$color_id."','".$gmts_size_id."',".$txt_size.",".$txt_trim_qty.",".$txt_rate.",".$txt_amount.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		 
		 $rID=sql_insert("wo_non_ord_samp_booking_dtls",$field_array,$data_array,1);
		 $rID_in2=1;
		 if ($data_array2!="")
		 {
			$rID_in2=sql_insert("wo_non_ord_samp_yarn_dtls",$field_array2,$data_array2,0);
		 }
		// echo "10**".$rID.'=='.$rID_in2.'='.$prev_wo_qty.'='.$description;die;
		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $rID_in2){
				mysql_query("COMMIT");  
				echo "0**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_in2){
				oci_commit($con);
				echo "0**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	    if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
	    $booking_id=return_field_value( "id", "wo_non_ord_samp_booking_mst","booking_no=$txt_booking_no");
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==3) $is_approved=1;
		if($is_approved==1){
			echo "app1**".str_replace("'","",$txt_booking_no);
			die;
		}
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a, com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and b.item_group=$cbo_trim_group and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			check_table_status( $_SESSION['menu_id'],0);
			die;
		}
		
		$recv_number=return_field_value( "recv_number", "inv_receive_master a, inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.item_group_id=$cbo_trim_group and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($recv_number){
			echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
			check_table_status( $_SESSION['menu_id'],0);
			die;
		}
		
		  $item_id=str_replace("'","",$cbo_trim_group);
		   $style_id=str_replace("'","",$txt_style);
		  $trim_req_qty=str_replace("'","",$txt_trim_req_qty);
		  $wo_trim_qty=str_replace("'","",$txt_trim_qty);$new_booking_no=str_replace("'","",$txt_booking_no);
		  
		$prev_wo_qty=return_field_value("sum(trim_qty) as  trim_qty", "wo_non_ord_samp_booking_dtls", "style_id=$style_id and trim_group=$item_id and status_active=1 and is_deleted=0 and booking_no!=$txt_booking_no","trim_qty");
		$tot_wo_qty=$wo_trim_qty+$prev_wo_qty;
		
		if($tot_wo_qty>$trim_req_qty)
		{
			echo "13**Item over qty not allowed\n"."(Prev Qty=$prev_wo_qty,Req Qty=$trim_req_qty)";
			check_table_status( $_SESSION['menu_id'],0);
			die; 
		}
		
		$field_array_up="booking_no*style_id*style_des*sample_type*trim_group*uom*fabric_description*barnd_sup_ref*gmts_color*fabric_color*gmts_size*item_size*trim_qty*rate*amount*remarks*updated_by*update_date";
		 $field_array2="id,wo_non_ord_samp_book_dtls_id,booking_no,count_id,copm_one_id,percent_one,type_id,cons_ratio,cons_qnty,inserted_by,insert_date"; 
		 $wo_non_ord_samp_yarn_dtls_id=return_next_id( "id", "wo_non_ord_samp_yarn_dtls", 1 ) ;
		 $add_comma_yarn=0;		
		  $gmt_color_str = str_replace("'", "", $txt_gmt_color);
		$gmt_color_nam=strtoupper(trim($gmt_color_str));
		$fab_color_str = str_replace("'", "", $txt_color);
		$fab_color_nam=strtoupper(trim($fab_color_str));
		
		 $new_array_gmts_color=array();
		  if(str_replace("'","",$txt_gmt_color)!="")
		  {
			 if (!in_array(str_replace("'","",$txt_gmt_color),$new_array_gmts_color))
			 {
				  $gmts_color_id = return_id( str_replace("'","",$txt_gmt_color), $color_library, "lib_color", "id,color_name","143");  
				  $new_array_gmts_color[$gmts_color_id]=str_replace("'","",$txt_gmt_color);
			 }
			 else $gmts_color_id =  array_search(str_replace("'","",$txt_gmt_color), $new_array_gmts_color);
		 }
		 else $gmts_color_id =0;
		// echo "10**".$gmt_color_nam.'='.$fab_color_nam;die;	 
			$new_array_color=array();
			if($gmt_color_nam==$fab_color_nam)
			{
				$color_id=$gmts_color_id;
			}
			else
			{
				if(str_replace("'","",$txt_color)!="")
				{
					if (!in_array(str_replace("'","",$txt_color),$new_array_color))
					{
						$color_id = return_id( str_replace("'","",$txt_color), $color_library, "lib_color", "id,color_name","90");
						$new_array_color[$color_id]=str_replace("'","",$txt_color);
					}
					else $color_id =  array_search(str_replace("'","",$txt_color), $new_array_color);
				}
				else $color_id =0; //	echo "10**".$gmt_color_nam.'='.$fab_color_nam;die;
			}				 
			$new_array_gmts_size=array();
			if(str_replace("'","",$txt_gmts_size)!="")	 
			{	 
				 if (!in_array(str_replace("'","",$txt_gmts_size),$new_array_gmts_size))
				 {
					  $gmts_size_id = return_id( str_replace("'","",$txt_gmts_size), $size_library, "lib_size", "id,size_name","143");   
					  $new_array_gmts_size[$gmts_size_id]=str_replace("'","",$txt_gmts_size);
				 }
				 else $gmts_size_id =  array_search(str_replace("'","",$txt_gmts_size), $new_array_gmts_size); 
			 }
			 else $gmts_size_id =0;
		
		
			$data_array_up="".$txt_booking_no."*".$txt_style."*".$txt_style_des."*".$cbo_sample_type."*".$cbo_trim_group."*".$cbo_uom."*".$txt_description."*".$txt_barnd_sup_ref."*'".$gmts_color_id."'*'".$color_id."'*'".$gmts_size_id."'*".$txt_size."*".$txt_trim_qty."*".$txt_rate."*".$txt_amount."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//echo $data_array; die; 
		
			
	    $rID=sql_update("wo_non_ord_samp_booking_dtls",$field_array_up,$data_array_up,"id","".$update_id_details."",0);
		$rID_in2=1;
		if ($data_array2!="")
		 {
			$rID_in2=sql_insert("wo_non_ord_samp_yarn_dtls",$field_array2,$data_array2,0);
		 }
		//echo  $rID; die;
	    check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $rID_in2){
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_in2){
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$is_approved=return_field_value( "is_approved", "wo_booking_mst","booking_no=$txt_booking_no");
		if($is_approved==3) $is_approved=1;
		if($is_approved==1){
			echo "app1**".str_replace("'","",$txt_booking_no);
			die;
		}
		
		$pi_number=return_field_value( "pi_number", "com_pi_master_details a, com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and b.item_group=$cbo_trim_group and a.item_category_id=4 and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0");
		if($pi_number){
			echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
			//check_table_status( $_SESSION['menu_id'],0);
			die;
		}
		
		$recv_number=return_field_value( "recv_number", "inv_receive_master a, inv_trims_entry_dtls b"," a.id=b.mst_id and a.booking_no=b.booking_no and a.booking_no=$txt_booking_no and b.item_group_id=$cbo_trim_group and a.item_category=4 and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
		if($recv_number){
			echo "recv1**".str_replace("'","",$txt_booking_no)."**".$recv_number;
			//check_table_status( $_SESSION['menu_id'],0);
			die;
		}
		
		$rID=execute_query( "update wo_non_ord_samp_booking_dtls set status_active=0,is_deleted =1 where id =$update_id_details",0);	
		$rID_de2=execute_query( "delete from wo_non_ord_samp_yarn_dtls where wo_non_ord_samp_book_dtls_id =".$update_id_details."",0);
		if($db_type==0)
		{
			if($rID && $rID_de2){
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_de2){
				oci_commit($con);  
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con);  
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
}


if ($action=="trims_booking_popup")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
?>
	<script>
	function js_set_value(booking_no)
	{
		document.getElementById('selected_booking').value=booking_no;
		parent.emailwindow.hide();
	}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table  width="910" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
                        <th colspan="8">
                          <?
                           echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
                          ?>
                        </th>
                </thead>
                <thead>                	 
                    <th width="150">Company Name</th>
                    <th width="150">Buyer Name</th>
                     <th width="80">Booking No</th>
                     <th width="80">Style Ref.</th>
                     <th width="80">Req. No</th>
                    <th width="130" colspan="2">Date Range</th>
                    <th>&nbsp;</th>           
                </thead>
                <tr class="general">
                    <td> <input type="hidden" id="selected_booking">
                        <? 
                            echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'trims_sample_booking_without_order_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                        ?>
                    </td>
                <td id="buyer_td">
                 <? 
                    echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --" );
                ?>	</td>
                <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:80px"></td>
                 <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
                  <td><input name="txt_req_no" id="txt_req_no" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"></td> 
                 <td align="center">
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_req_no').value, 'create_booking_search_list_view', 'search_div', 'trims_sample_booking_without_order_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
            </tr>
                <tr>
                <td align="center" colspan="8" valign="middle">
                <? 
                echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
                ?>
                <? echo load_month_buttons();  ?>
                </td>
            </tr>
         </table>
    <div id="search_div"></div>
    </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else   $buyer=""; //{ echo "Please Select Buyer First."; die; }
	//echo $data[7].'XX';
	if($db_type==0)
	{
	//$booking_year_cond=" and SUBSTRING_INDEX(`a.insert_date`, '-', 1)=$data[4]";
	$booking_year_cond="and YEAR(a.insert_date)=$data[4]";
	if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
		
	$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]";	
	if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	 $style_cond="";
	if (str_replace("'","",$data[7])!="")
	{
		$style_cond=" and c.style_ref_no='$data[7]'";//requisition_number_prefix_num
	}
	if (str_replace("'","",$data[8])!="")
	{
		$style_cond.=" and c.requisition_number_prefix_num=$data[8]";//requisition_number_prefix_num
	}
	
	if($data[6]==4 || $data[6]==0)
		{
			if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  "; else $booking_cond="";
		}
    if($data[6]==1)
		{
			if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num ='$data[5]' "; else $booking_cond="";
		}
   if($data[6]==2)
		{
			if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[5]%'  $booking_year_cond  "; else $booking_cond="";
		}
	if($data[6]==3)
		{
			if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]'  $booking_year_cond  "; else $booking_cond="";
		}
	$po_array=array();
	 $sql_po= sql_select("select a.id as mst_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b,sample_development_mst c where a.booking_no=b.booking_no and b.style_id=c.id and c.entry_form_id=117 $style_cond $company $buyer $booking_date $booking_cond and a.booking_type=5 and a.status_active=1 and a.is_deleted=0 order by a.id DESC");
	 //"select a.id as mst_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b,sample_development_mst c  where a.booking_no=b.booking_no and b.style_id=c.id and c.entry_form_id=117 $style_cond $company $buyer $booking_date $booking_cond and a.booking_type=5 and a.status_active=1 and a.is_deleted=0  order by a.id ";
	foreach($sql_po as $row)
	{
		$mst_id.=$row[csf("mst_id")].',';
		//$po_array[$row[csf("po_break_down_id")]]=rtrim($po_number_string,",");
	}
	$mst_ids=rtrim($mst_id,",");
	$mst_ids=implode(",",array_unique(explode(",",$mst_ids)));
	
	if (str_replace("'","",$data[7])!="" || str_replace("'","",$data[8])!="")
	{
		if($mst_ids!="") $mst_ids_cond=" and a.id in($mst_ids)";else $mst_ids_cond="";
	}
	//echo $mst_id.'DD';
	 $approved=array(0=>"No",1=>"Yes",2=>"No",3=>"Yes");
	 $is_ready=array(0=>"No",1=>"Yes",2=>"No"); 
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,4=>$item_category,5=>$suplier,6=>$approved,7=>$is_ready);
	 $sql= "select a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.item_category, a.supplier_id, a.is_approved, a.ready_to_approved from wo_non_ord_samp_booking_mst a where a.booking_type=5 and a.status_active=1 and a.is_deleted=0 $company $buyer $booking_date $booking_cond  $mst_ids_cond order by a.id DESC"; 
	 
	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Item Category,Supplier,Approved,Is-Ready", "100,90,100,100,100,60,70","870","320",0, $sql , "js_set_value", "booking_no", "", 1, "0,0,company_id,buyer_id,item_category,supplier_id,is_approved,ready_to_approved", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,item_category,supplier_id,is_approved,ready_to_approved", '','','0,3,0,0,0,0,0,0,0,0','','');
	exit();
}

if($action=="terms_condition_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
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

function fnc_fabric_booking_terms_condition( operation )
{
	var row_num=$('#tbl_termcondi_details tr').length-1;
	var data_all="";
	for (var i=1; i<=row_num; i++)
	{
		
		if (form_validation('termscondition_'+i,'Term Condition')==false)
		{
			return;
		}
		
		data_all=data_all+get_submitted_data_string('txt_booking_no*termscondition_'+i,"");
	}
	var data="action=save_update_delete_fabric_booking_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
	//freeze_window(operation);
	http.open("POST","trims_sample_booking_without_order_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_fabric_booking_terms_condition_reponse;
}

function fnc_fabric_booking_terms_condition_reponse()
{
	if(http.readyState == 4) 
	{
	    var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
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
<? echo load_freeze_divs ("../../../",$permission);  ?>
<fieldset>
        	<form id="termscondi_1" autocomplete="off">
           <input type="text" id="txt_booking_no" name="txt_booking_no" value="<? echo str_replace("'","",$txt_booking_no) ?>"/>
            <table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
                	<thead>
                    	<tr>
                        	<th width="50">Sl</th><th width="530">Terms</th><th ></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" align="center">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  /> 
                                    </td>
                                    <td> 
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
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
							$i++;
					?>
                    <tr id="settr_1" align="center">
                                    <td>
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>"  /> 
                                    </td>
                                    <td>
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> );" />
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
									echo load_submit_buttons( $permission, "fnc_fabric_booking_terms_condition", 0,0 ,"reset_form('termscondi_1','','','','')",1) ; 
									?>
                        </td> 
                    </tr>
                </table>
            </form>
        </fieldset>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="save_update_delete_fabric_booking_terms_condition")
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
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}		
		 $id=return_next_id( "id", "wo_booking_terms_condition", 1 ) ;
		 $field_array="id,booking_no,terms";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $termscondition="termscondition_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_booking_no.",".$$termscondition.")";
			$id=$id+1;
		 }
		// echo  $data_array;
		$rID_de3=execute_query( "delete from wo_booking_terms_condition where  booking_no =".$txt_booking_no."",0);

		 $rID=sql_insert("wo_booking_terms_condition",$field_array,$data_array,1);
		 check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$new_booking_no[0];
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_booking_no[0];
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);  
				echo "0**".$new_booking_no[0];
			}
			else{
				oci_rollback($con);  
				echo "10**".$new_booking_no[0];
			}
		}
		disconnect($con);
		die;
	}	
}

if ($action=="populate_data_from_search_popup")
{
	 $sql= "select booking_no,booking_date,company_id,buyer_id,item_category,currency_id,exchange_rate,pay_mode,booking_month,supplier_id,attention,tenor,delivery_date,source,booking_year,is_approved,ready_to_approved,delivery_address,remarks from wo_non_ord_samp_booking_mst  where booking_no='$data'"; 
	
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
		$style_id=0;
		//$styleId=return_field_value("b.style_id", "wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b", "a.booking_no=b.booking_no and a.booking_no='$data'");
		
		echo "load_drop_down( 'requires/trims_sample_booking_without_order_controller', '".$row[csf("company_id")]."', 'load_drop_down_buyer', 'buyer_td' );\n";
		$buyer_data=$row[csf("buyer_id")].'_'.$style_id;
		echo "load_drop_down( 'requires/trims_sample_booking_without_order_controller', '".$buyer_data."', 'load_drop_down_buyer_tag_sample', 'sample_td' );\n";
		//load_drop_down( 'requires/trims_sample_booking_without_order_controller', this.value, 'load_drop_down_buyer_tag_sample', 'sample_td' )
        echo "color_from_library('".$row[csf("company_id")]."');\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";  
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "load_drop_down( 'requires/trims_sample_booking_without_order_controller', '".$row[csf("pay_mode")]."', 'load_drop_down_supplier', 'supplier_td' );\n";
		echo "document.getElementById('txt_booking_date').value = '".change_date_format($row[csf("booking_date")],'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('cbo_supplier_name').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_tenor').value = '".$row[csf("tenor")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")],'dd-mm-yyyy','-')."';\n";
	    echo "document.getElementById('cbo_source').value = '".$row[csf("source")]."';\n";
		echo "document.getElementById('delivery_address').value = '".$row[csf("delivery_address")]."';\n";
		echo "document.getElementById('txt_remark').value = '".$row[csf("remarks")]."';\n";
		if($row[csf("is_approved")]==1 || $row[csf("is_approved")]==3){
			$is_approved=1;
		}else{
			$is_approved=$row[csf("is_approved")];
		}
		echo "document.getElementById('id_approved_id').value = '".$is_approved."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";

		if($is_approved==1)
		{
			echo "document.getElementById('app_sms2').innerHTML = 'This booking is approved';\n";
		}
		else
		{
			echo "document.getElementById('app_sms2').innerHTML = '';\n";
		}
	 }
}

if($action=="populate_details_data_from_for_update")
{
	$data_array=sql_select("select id,trim_group,style_id,style_des,sample_type,uom,fabric_description,barnd_sup_ref,gmts_color,fabric_color,gmts_size,item_size,trim_qty,rate,amount,remarks FROM wo_non_ord_samp_booking_dtls WHERE id ='".$data."'  and status_active=1 and	is_deleted=0");
	foreach ($data_array as $row)
	{
		$style_id=$row[csf("style_id")];
		$style=return_field_value("style_ref_no", "sample_development_mst", "id=$style_id");
	$trims_req="select c.req_qty_ra,c.trims_group_ra,c.description_ra from sample_development_fabric_acc c,sample_development_mst a where a.id=c.sample_mst_id and c.form_type=2 and a.id=$style_id and c.trims_group_ra=".$row[csf("trim_group")]." and c.description_ra='".$row[csf("fabric_description")]."' and a.status_active=1 and c.status_active=1";
	$trims_req_result=sql_select($trims_req,1);
	$req_qty_ra=$trims_req_result[0][csf('req_qty_ra')];
	$description_ra=$trims_req_result[0][csf('description_ra')];
	
		$data_item=$row[csf("style_id")].'_'.$row[csf("sample_type")];
		//echo $data_item;
		echo "load_drop_down( 'requires/trims_sample_booking_without_order_controller', '".$data_item."', 'load_drop_down_trim_group', 'tgroup_td' );\n";
		echo "document.getElementById('cbo_trim_group').value = '".$row[csf("trim_group")]."';\n";  
		echo "document.getElementById('cbo_uom').value = '".$row[csf("uom")]."';\n"; 
		
		echo "document.getElementById('txt_style').value = '".$row[csf("style_id")]."';\n";
		echo "document.getElementById('txt_style_no').value = '".$style."';\n";
		echo "document.getElementById('txt_style_des').value = '".$row[csf("style_des")]."';\n";
		
		echo "document.getElementById('cbo_sample_type').value = '".$row[csf("sample_type")]."';\n";
		
		echo "document.getElementById('txt_description').value = '".$row[csf("fabric_description")]."';\n";
		echo "document.getElementById('txt_barnd_sup_ref').value = '".$row[csf("barnd_sup_ref")]."';\n";
		echo "document.getElementById('txt_gmt_color').value = '".$color_library[$row[csf("gmts_color")]]."';\n";
		echo "document.getElementById('txt_color').value = '".$color_library[$row[csf("fabric_color")]]."';\n";
		echo "document.getElementById('txt_gmts_size').value = '".$size_library[$row[csf("gmts_size")]]."';\n";
		echo "document.getElementById('txt_size').value = '".$row[csf("item_size")]."';\n";
		
		echo "document.getElementById('txt_trim_qty').value = '".$row[csf("trim_qty")]."';\n";
		echo "document.getElementById('txt_trim_req_qty').value = '".$req_qty_ra."';\n";
		echo "document.getElementById('txt_rate').value = '".$row[csf("rate")]."';\n";
		echo "document.getElementById('txt_amount').value = '".$row[csf("amount")]."';\n";	
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";	
		echo "$('#txt_style_no').attr('disabled',true);\n";	
		echo "$('#cbo_trim_group').attr('disabled',true);\n";	
		echo "$('#cbo_sample_type').attr('disabled',true);\n";		
		
		echo "document.getElementById('update_id_details').value = '".$row[csf("id")]."';\n";	
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_fabric_booking_dtls',2);\n";
		
		//set_button_status(1, permission, 'fnc_fabric_booking_dtls',2)
	}
}
?>
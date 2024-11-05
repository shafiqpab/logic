<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

include('../../../../includes/class4/class.conditions.php');
include('../../../../includes/class4/class.reports.php');
include('../../../../includes/class4/class.fabrics.php');


$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	if($data[1]==1) $party="1,3,21,90"; else $party="80";
	echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}
if ($action=="load_drop_down_store")
{
	$data=explode('_',$data);
	if($data[1]==1){$knitFinish=2;}else{$knitFinish=3;}
	echo create_drop_down( "cbo_store_name", 120, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id  and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] and  b.category_type=$knitFinish order by a.store_name","id,store_name", 1, "--Select Store--", 1, "",0 );
	exit();
	//select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] and b.category_type=$data[1] order by a.store_name
}
if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

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
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( ddd );
		}

		/*function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]);
			$("#hide_job_no").val(splitData[1]);
			parent.emailwindow.hide();
		}*/
	</script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:580px;">
				<table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Buyer</th>
						<th>Search Job</th>
						<th>Search Style</th>
						<!--<th>Search Order</th>-->
						<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:70px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
						<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								?>
							</td>
							<td align="center">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_job" id="txt_search_job" placeholder="Job No" />
							</td>
							<td align="center">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_style" id="txt_search_style" placeholder="Style Ref." />
							</td>
	                        <!--<td align="center">
	                            <input type="text" style="width:80px" class="text_boxes" name="txt_search_order" id="txt_search_order" placeholder="Order No" />
	                        </td> +'**'+document.getElementById('txt_search_order').value-->
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_search_job').value+'**'+document.getElementById('txt_search_style').value+'**'+'<? echo $cbo_year_id; ?>', 'create_job_no_search_list_view', 'search_div', 'order_wise_finish_fabric_stock_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:70px;" />
	                        </td>
	                    </tr>
	                </tbody>
	            </table>
	            <div style="margin-top:15px" id="search_div"></div> 
	        </fieldset>
	    </form>
	</div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	//$month_id=$data[5];
	//echo $month_id;

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
	}

	if($data[2]!='') $job_cond=" and job_no_prefix_num=$data[2]"; else $job_cond="";
	if($data[3]!='') $style_cond=" and style_ref_no like '$data[3]'"; else $style_cond="";
	//if($data[4]!='') $order_cond=" and po_number like '$data[4]'"; else $order_cond="";

	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by="year(insert_date)";
	else if($db_type==2) $year_field_by="to_char(insert_date,'YYYY')";
	else $year_field_by="";

	if($year_id!=0) $year_cond=" and $year_field_by='$year_id'"; else $year_cond="";
	//if($month_id!=0) $month_cond="$month_field_by=$month_id"; else $month_cond="";
	$arr=array (0=>$buyer_arr);
	$sql= "select id, job_no, job_no_prefix_num, buyer_name, style_ref_no, $year_field_by as year from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id $buyer_id_cond $job_cond $style_cond $year_cond order by id DESC";

	echo create_list_view("tbl_list_search", "Buyer Name,Job No,Year,Style Ref. No", "170,130,80,60","610","270",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "buyer_name,0,0,0", $arr , "buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0','',1) ;
	exit();
}

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );


if($action=="report_generate6")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name 	=$cbo_company_id;
	$txt_ref_no 		= str_replace("'","",$txt_ref_no);
	$cbo_search_by 		= str_replace("'","",$cbo_search_by);
	$txt_search_comm 	= str_replace("'","",$txt_search_comm);
	$cbo_report_type 	= str_replace("'","",$cbo_report_type);
	$cbo_value_range_by = str_replace("'","",$cbo_value_range_by);
	$company_name=str_replace("'","",$cbo_company_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_id);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_batch_no=str_replace("'","",$txt_batch_no);
	$txt_inter_ref=str_replace("'","",$txt_inter_ref);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_styleref_no=str_replace("'","",$txt_styleref_no);
	//$txt_season="%".trim(str_replace("'","",$txt_season))."%";
	//if($txt_batch_no!="")
	$company_arr=return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_arr=return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
  

	//FAL-17-00138
	//if($txt_file_no!="") $file_cond=" and e.file_no='$txt_file_no'";else $file_cond="";
	//if($txt_batch_no!="") $batch_cond=" and a.batch_no='$txt_batch_no'";else $batch_cond="";
	//if($txt_inter_ref!="") $ref_cond=" and e.grouping='$txt_inter_ref'";else $ref_cond="";
	//if($txt_inter_ref!="") $inter_ref_con=" and c.grouping='$txt_inter_ref'";else $inter_ref_con="";
	
	if($db_type==2)
	{
		$group_con="LISTAGG(cast(b.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.po_id) as po_id";
	}
	else
	{
		$group_con="group_concat(distinct(b.po_id)) as po_id";
	}
	if($cbo_buyer_name>0) $buyer_cond=" and d.buyer_name=$cbo_buyer_name"; else  $buyer_cond="";





	if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and a.buyer_name=$cbo_buyer_id";
	$job_no=str_replace("'","",$txt_job_no);
	$search_cond='';
	if($cbo_search_by==1)
	{
		if($txt_search_comm!="") $job_cond=" and d.job_no='$txt_search_comm'";else $job_cond="";
		//if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and d.job_no in ($txt_search_comm) ";
	}
	else if($cbo_search_by==2)
	{
		if($txt_search_comm!="") $styleRef_cond=" and d.style_ref_no='$txt_search_comm'";else $styleRef_cond="";
		//if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and d.style_ref_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==3)
	{
		if($txt_search_comm!="") $po_cond=" and e.po_number='$txt_search_comm'";else $po_cond="";
		//if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.po_number LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==4)
	{
		if($txt_search_comm!="") $file_cond=" and e.file_no='$txt_search_comm'";else $file_cond="";
		//if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and e.file_no='$txt_search_comm'";
	}
	else if($cbo_search_by==5)
	{
		if($txt_search_comm!="") $ref_cond=" and e.grouping='$txt_search_comm'";else $ref_cond="";
		if($txt_search_comm!="") $inter_ref_con=" and c.grouping='$txt_search_comm'";else $inter_ref_con="";
		if($txt_search_comm!="") $inter_ref_con2=" and b.grouping='$txt_search_comm'";else $inter_ref_con2="";
		//if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.grouping LIKE '$txt_search_comm'";
	}
	else
	{
		$search_cond.="";
	}

	///$date_from=str_replace("'","",$txt_date_from);
	//if( $date_from=="") $receive_date=""; else $receive_date= " and c.transaction_date <=".$txt_date_from."";

	//if( $date_from=="") $today_receive_date=""; else $today_receive_date= " c.transaction_date=".$txt_date_from."";

	$cbo_year_val=str_replace("'","",$cbo_year);
	$order_no=str_replace("'","",$txt_order_id);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and c.id in ($order_no)";

	//$date_from=str_replace("'","",$txt_date_from);
	//if( $date_from=="") $receive_date_trans=""; else $receive_date_trans= " and a.transfer_date <=".$txt_date_from."";
	if($db_type==0)
	{
		//$prod_id_cond=" group_concat(b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and year(a.insert_date)='$cbo_year_val'"; else $year_cond="";
	}
	else if($db_type==2)
	{
		//$prod_id_cond=" listagg(cast(b.from_prod_id as varchar2(4000)),',') within group (order by b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and to_char(a.insert_date,'YYYY')='$cbo_year_val'";  else $year_cond="";
	}

	if($db_type==0)
	{
		$select_fld= "year(a.insert_date)as year";
	}
	else if($db_type==2)
	{
		$select_fld= "TO_CHAR(a.insert_date,'YYYY') as year";
	}
	ob_start();

	if($cbo_report_type==1)
	{
		
		$constructtion_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent,c.composition_name from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b,lib_composition_array c where a.id=b.mst_id and b.copmposition_id=c.id";
		$data_array=sql_select($sql_deter);
		foreach( $data_array as $row )
		{
			$constructtion_arr[$row[csf('id')]]=$row[csf('construction')]." ".$row[csf('composition_name')]." ".$row[csf('percent')]."%";
		}
	 	/*$sql_res="SELECT a.batch_no,a.id,d.company_name,d.buyer_name,d.style_ref_no,e.file_no,e.grouping,a.color_id,$group_con,sum(b.batch_qnty) as batch_qnty,e.job_no_mst, b.item_description, f.detarmination_id,LISTAGG(cast(f.id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY f.id) as prod_id 
	 	from pro_batch_create_mst a,pro_batch_create_dtls b , wo_po_details_master d,wo_po_break_down e, product_details_master f 
	 	where b.po_id=e.id and d.id=e.job_id and a.id=b.mst_id and b.prod_id=f.id and a.entry_form in(0,37)  and a.status_active=1 and b.status_active=1  and d.company_name=$cbo_company_name and a.batch_against != 2 $buyer_cond  $batch_cond $file_cond $job_cond $ref_cond $styleRef_cond $po_cond 
	 	group by a.batch_no,a.id,d.buyer_name,a.color_id,d.company_name,e.file_no,e.grouping,e.job_no_mst,d.style_ref_no, b.item_description, f.detarmination_id,f.id order by a.color_id, e.job_no_mst";*/


	/* $sql_res=	"SELECT a.batch_no,a.id,d.company_name,d.buyer_name,d.style_ref_no,e.file_no,e.grouping,a.color_id,LISTAGG(cast(b.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.po_id) as po_id


		,sum(b.batch_qnty) as batch_qnty,
		e.job_no_mst, b.item_description, f.detarmination_id,LISTAGG(cast(f.id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY f.id) as prod_id 

		,g.body_part_id,g.item_number_id
		 from pro_batch_create_mst a,pro_batch_create_dtls b,wo_booking_dtls c,WO_PRE_COST_FABRIC_COST_DTLS g, wo_po_details_master d,wo_po_break_down e, product_details_master f 
		 where b.po_id=e.id and d.id=e.job_id
		 
		  and e.id=c.po_break_down_id and b.po_id=c.po_break_down_id  and c.job_no=d.job_no 
		  and c.pre_cost_fabric_cost_dtls_id=g.id and d.job_no=g.job_no 
		  
		  and a.id=b.mst_id  and b.prod_id=f.id and a.entry_form in(0,37) and a.status_active=1 and b.status_active=1
		  and d.company_name='9' and a.batch_against != 2 and e.grouping='reportdyeingaop99' 
		group by a.batch_no,a.id,d.buyer_name,a.color_id,d.company_name,e.file_no,e.grouping,e.job_no_mst,d.style_ref_no, b.item_description, f.detarmination_id,f.id

		,g.body_part_id,g.item_number_id
		order by a.color_id, e.job_no_mst ";*/


		$sql_res="SELECT b.job_no_mst,a.company_name,a.buyer_name,a.style_ref_no,b.file_no,b.grouping,b.id as po_id,d.body_part_id,d.item_number_id,d.uom,d.body_part_type,c.fabric_color_id as color_id,d.lib_yarn_count_deter_id,c.gsm_weight,c.booking_no,sum(c.fin_fab_qnty) as booking_qnty
		from wo_po_details_master a,wo_po_break_down b, wo_booking_dtls c,wo_pre_cost_fabric_cost_dtls d 
		where a.id=b.job_id and b.id=c.po_break_down_id and c.job_no=b.job_no_mst and c.pre_cost_fabric_cost_dtls_id=d.id and c.job_no=d.job_no 
		and a.company_name=$cbo_company_name   
		$inter_ref_con2  
		 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0
		group by b.job_no_mst ,a.company_name,a.buyer_name,a.style_ref_no,b.file_no,b.grouping,b.id,d.body_part_id,d.item_number_id,d.uom,d.body_part_type,c.fabric_color_id,d.lib_yarn_count_deter_id,c.gsm_weight,c.booking_no";




		//echo $sql_res; die;
		// and A.BATCH_NO='B400' 
		$header=sql_select($sql_res);
		$batchreport = array();
		$batchIdArray = array();
		$poIdArray = array();
		$contru_arr = array();
		$all_po_id="";
		$colorIDS="";
		foreach($header as $row)
		{
			if ($sub_group_arr[$row[csf('color_id')]]=='')
			{
				$i=0;
				$sub_group_arr[$row[csf('color_id')]]=$row[csf('color_id')];
			}
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['color_id']=$row[csf('color_id')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['po_id']=$row[csf('po_id')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['batch_no']=$row[csf('batch_no')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['body_part_id']=$row[csf('body_part_id')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['lib_yarn_count_deter_id']=$row[csf('lib_yarn_count_deter_id')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['job_no_mst']=$row[csf('job_no_mst')];
			// $batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]]['batch_qnty']=$row[csf('batch_qnty')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['production_date']=$row[csf('production_date')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['company_name']=$row[csf('company_name')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['buyer_name']=$row[csf('buyer_name')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['grouping']=$row[csf('grouping')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['file_no']=$row[csf('file_no')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['item_number_id']=$row[csf('item_number_id')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['body_part_type']=$row[csf('body_part_type')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['gsm_weight']=$row[csf('gsm_weight')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['uom']=$row[csf('uom')];
			$batchreport[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['booking_qnty']=$row[csf('booking_qnty')];

			$itemNumberArr[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['item_number_id']=$row[csf('item_number_id')];
			
			if($row[csf('body_part_type')]==40 || $row[csf('body_part_type')]==50)
			{
				$bodypartTypewiseColorArr[$row[csf('body_part_type')]]['color_id']=$row[csf('color_id')];
				$po_id_arr_colarCuffPcs[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['body_part_id']=$row[csf('body_part_id')];
				if($colorARR[$row[csf('color_id')]]=="")
				{
					$colorIDS.=$row[csf('color_id')].",";
					$colorARR[$row[csf('color_id')]]=$row[csf('color_id')];
				}
				if($bookingARR[$row[csf('booking_no')]]=="")
				{
					$bookingIDS.="'".$row[csf('booking_no')]."',";
					$bookingARR[$row[csf('booking_no')]]=$row[csf('booking_no')];
				}
			}
			if($row[csf('body_part_type')]==30)
			{
				$bodypartTypewiseColorArr2[$row[csf('body_part_type')]]['color_id']=$row[csf('color_id')];
			}
			
		
			$poIdArray[$row[csf('po_id')]] = $row[csf('po_id')];
			$fabricSlicing=explode(",", $row[csf('item_description')]);
			$fabricDes=$fabricSlicing[0];
			$gsm=$fabricSlicing[2];
			$dia=$fabricSlicing[3];

			$prodIdArr[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]]['prod_id']=$row[csf('prod_id')];
			$prodIdArr[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]]['detarmination_id']=$row[csf('detarmination_id')];
			$prodIdArr[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]]['item_description']=$fabricDes;
			$prodIdArr[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]]['gsm']=$gsm;
			$prodIdArr[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]]['dia']=$dia;

			// $constructionArray[$row[csf('item_description')]] = $row[csf('item_description')];
			$construction_name=$constructtion_arr[$row[csf('detarmination_id')]];
			//$contru_arr[$constructtion_arr[$row[csf('detarmination_id')]]]=$construction_name;
			$batch_qty_arr[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]][$construction_name]+=$row[csf('batch_qnty')];

			$prodIdArr2[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('id')]][$construction_name]['prod_id']=$row[csf('prod_id')];
			
			if($all_po_id=="") $all_po_id=$row[csf('po_id')]; else $all_po_id.=",".$row[csf('po_id')]; //echo $all_po_id;
			$i++;
		}	
		$colorIDS=chop($colorIDS,",");
		$bookingIDS=chop($bookingIDS,",");

		// echo "<pre>"; print_r($contru_arr);die;
		//echo "<pre>"; print_r($prodIdArr2);//die;

		/*$contru_arr=array();
		foreach ($constructionArray as $key => $value) 
		{
			$contru=explode(",", $value);
			$contru_arr[$contru[0]]=$contru[0];
		}
		$count_constr=count($contru_arr);*/
		//$count_constr=count($contru_arr);
		/*echo "<pre>";
		print_r($batchreport);
		echo "</pre>";*/
		if(count($batchreport)==0)
		{
			?>
			<div style="font-weight: bold;color: red;font-size: 20px;text-align: center;">Data not found! Please try again.</div>
			<?
			die();
		}

		$sql_additionalBooking=sql_select("SELECT b.job_no_mst,d.body_part_id,d.body_part_type,c.fabric_color_id as color_id,d.lib_yarn_count_deter_id,c.gsm_weight,c.booking_no,sum(c.fin_fab_qnty) as booking_qnty
		from wo_po_details_master a,wo_po_break_down b, wo_booking_dtls c,wo_pre_cost_fabric_cost_dtls d 
		where a.id=b.job_id and b.id=c.po_break_down_id and c.job_no=b.job_no_mst and c.pre_cost_fabric_cost_dtls_id=d.id and c.job_no=d.job_no 
		and a.company_name=$cbo_company_name   
		$inter_ref_con2  and c.is_short not in(2)
		and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0
		group by b.job_no_mst,d.body_part_id,d.body_part_type,c.fabric_color_id,d.lib_yarn_count_deter_id,c.gsm_weight,c.booking_no");
		foreach($sql_additionalBooking as $row)
		{
			$additonalBookingQntyArr[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]['additional_booking_qnty']=$row[csf('booking_qnty')];
		}

		$poIds=chop($all_po_id,','); 
		$po_ids=count(array_unique(explode(",",$all_po_id)));
		$poIds_cond="";$poIds_cond2="";$poIds_cond3="";$poIds_cond4="";$poIds_cond5="";
		if($db_type==2 && $po_ids>1000)
		{
			$poIds_cond=" and (";
			//$poIdsArr=array_chunk($poIds_Arr,990);
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_cond.=" b.po_break_down_id  in ($ids) or ";
				$poIds_cond2.=" a.po_break_down_id  in ($ids) or ";
				$poIds_cond3.=" b.to_order_id  in ($ids) or ";
				$poIds_cond4.=" po_break_down_id  in ($ids) or ";
				$poIds_cond5.=" b.po_id  in ($ids) or ";
			}
			$poIds_cond=chop($poIds_cond,'or ');
			$poIds_cond.=")";
			$poIds_cond2=chop($poIds_cond2,'or ');
			$poIds_cond2.=")";
			$poIds_cond3=chop($poIds_cond3,'or ');
			$poIds_cond3.=")";
			$poIds_cond4=chop($poIds_cond4,'or ');
			$poIds_cond4.=")";
			$poIds_cond5=chop($poIds_cond5,'or ');
			$poIds_cond5.=")";
		}
		else
		{
			$poIdsx=implode(",",array_unique(explode(",",$all_po_id)));
			$poIds_cond=" and  b.po_break_down_id  in ($poIdsx)";
			$poIds_cond2=" and  a.po_break_down_id  in ($poIdsx)";
			$poIds_cond3=" and  b.to_order_id  in ($poIdsx)";
			$poIds_cond4=" and  po_break_down_id  in ($poIdsx)";
			$poIds_cond5=" and  b.po_id  in ($poIdsx)";
		}
		$sql_batch_wise=	"SELECT a.batch_no,a.id,d.company_name,d.buyer_name,d.style_ref_no,e.file_no,e.grouping,a.color_id,e.job_no_mst, b.item_description, f.detarmination_id ,g.body_part_id,g.item_number_id
		 from pro_batch_create_mst a,pro_batch_create_dtls b,wo_booking_dtls c,WO_PRE_COST_FABRIC_COST_DTLS g, wo_po_details_master d,wo_po_break_down e, product_details_master f 
		 where b.po_id=e.id and d.id=e.job_id
		 
		  and e.id=c.po_break_down_id and b.po_id=c.po_break_down_id  and c.job_no=d.job_no 
		  and c.pre_cost_fabric_cost_dtls_id=g.id and d.job_no=g.job_no 
		  
		  and a.id=b.mst_id  and b.prod_id=f.id and a.entry_form in(0,37) and a.status_active=1 and b.status_active=1
		  and d.company_name=$cbo_company_name and a.batch_against != 2 $ref_cond $poIds_cond5
		group by a.batch_no,a.id,d.buyer_name,a.color_id,d.company_name,e.file_no,e.grouping,e.job_no_mst,d.style_ref_no, b.item_description, f.detarmination_id,f.id

		,g.body_part_id,g.item_number_id
		order by a.color_id, e.job_no_mst ";

		$sql_batch_data=sql_select($sql_batch_wise);
		$batchreportWise = array();
		
		foreach($sql_batch_data as $row)
		{
			$batchreportWise[$row[csf('job_no_mst')]][$row[csf('color_id')]][$row[csf('body_part_id')]][$row[csf('detarmination_id')]]['id']=$row[csf('id')];


			$batchIdArray[$row[csf('id')]] = $row[csf('id')];
		}
		/*echo "<pre>";
		print_r($batchreportWise);
		echo "</pre>";*/


		//===============================================================
		$batchIds = implode(",", $batchIdArray);
		if($db_type==2 && count($batchIdArray)>1000)
		{
			$batchIds_cond=" and (";
			$batchIdsArr=array_chunk($batchIdArray,990);
			foreach($batchIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$batchIds_cond.=" a.batch_id  in ($ids) or ";
			}
			$batchIds_cond=chop($batchIds_cond,'or ');
			$batchIds_cond.=")";
		}
		else
		{
			$batchIds_cond=" and  a.batch_id  in ($batchIds)";
		}
		//===========================================================






		$order_plan_qty_arr=array();
		$color_wise_wo_sql_qnty=sql_select( "select color_number_id, sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where   status_active=1 and is_deleted =0 $poIds_cond4 and color_number_id in($colorIDS) group by color_number_id");
		foreach($color_wise_wo_sql_qnty as $row)
		{
			$order_plan_qty_arr[$row[csf('color_number_id')]]['plan']=$row[csf('plan_cut_qnty')];
			$order_plan_qty_arr[$row[csf('color_number_id')]]['order']=$row[csf('order_quantity')];
		}

		// ====================== Req. Qty As Per Booking Color Wise ========================
		$grey_fabric_qnty=sql_select("SELECT a.job_no,a.fabric_color_id, d.construction, (a.grey_fab_qnty) as grey_fab_qnty,(a.fin_fab_qnty) as fin_fab_qnty
		from wo_booking_dtls a, wo_po_break_down c, wo_pre_cost_fabric_cost_dtls d
		where a.po_break_down_id=c.id and a.pre_cost_fabric_cost_dtls_id=d.id $inter_ref_con $poIds_cond2
		and a.booking_type=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ");
		//group by a.job_no,a.fabric_color_id, d.construction
		 
		$grey_fabric_qnty_array=array();
		$collar_cuff_percent_arr=array();
		foreach($grey_fabric_qnty as $row)
		{
			$grey_fabric_qnty_array[$row[csf("job_no")]][$row[csf("fabric_color_id")]][$row[csf("construction")]]['grey_fab_qnty']+=$row[csf("grey_fab_qnty")];
			$grey_fabric_qnty_array[$row[csf("job_no")]][$row[csf("fabric_color_id")]][$row[csf("construction")]]['fin_fab_qnty']+=$row[csf("fin_fab_qnty")];
		}

		$collar_cuff_sql="select b.color_number_id, d.colar_cuff_per, e.body_part_type
		FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c, wo_booking_dtls d, lib_body_part e

		WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no in($bookingIDS) and a.body_part_id=e.id and e.body_part_type in (40,50) and c.id=d.color_size_table_id and d.color_size_table_id=b.color_size_table_id  and d.po_break_down_id=c.po_break_down_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0";
		//echo $collar_cuff_sql;
		$collar_cuff_sql_res=sql_select($collar_cuff_sql);

		foreach($collar_cuff_sql_res as $collar_cuff_row)
		{
			$collar_cuff_percent_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('color_number_id')]]=$collar_cuff_row[csf('colar_cuff_per')];

		}

		// ==================== QC Pass Qty, Received qty and Issue Return qty and Transfer In and Out ==================
		$batchIds_cond_fin = str_replace("a.batch_id", "b.batch_id", $batchIds_cond); 
		$batchIds_cond_fin_out = str_replace("a.batch_id", "b.batch_id", $batchIds_cond); 
		$batchIds_cond_fin_in = str_replace("a.batch_id", "b.to_batch_id", $batchIds_cond); 
		
		$data_array_finish_qnty_transfer_in=sql_select("select a.entry_form,b.color_id, b.to_batch_id as batch_id, e.detarmination_id as fabric_description_id, sum(d.quantity) as trans_in_qnty,b.to_prod_id as prod_id,f.grouping as internal_ref,g.style_ref_no,g.buyer_name,g.job_no,e.item_description,b.to_order_id ,b.to_body_part  from inv_item_transfer_mst a,inv_item_transfer_dtls b,inv_transaction c, order_wise_pro_details d,product_details_master e,wo_po_break_down f,wo_po_details_master g where a.id=b.mst_id and b.to_trans_id=c.id and c.id=d.trans_id and b.to_prod_id=e.id and b.to_order_id=f.id and f.job_id=g.id and  d.entry_form in(14) and a.entry_form in(14) and c.item_category=2 and c.transaction_type=5 and d.trans_type=5 and a.to_company=$company_name $poIds_cond3 group by a.entry_form,b.color_id, b.to_batch_id, e.detarmination_id,b.to_prod_id,f.grouping,g.style_ref_no,g.buyer_name,g.job_no,e.item_description,b.to_order_id ,b.to_body_part  ");


	
		$data_array_finish_qnty_transfer_out=sql_select("select a.entry_form,b.color_id, b.batch_id, e.detarmination_id as fabric_description_id, sum(d.quantity) as trans_out_qnty,b.from_prod_id as prod_id,e.item_description,b.body_part_id  from inv_item_transfer_mst a,inv_item_transfer_dtls b,inv_transaction c, order_wise_pro_details d,product_details_master e where a.id=b.mst_id and b.trans_id=c.id and c.id=d.trans_id  and b.from_prod_id=e.id and  d.entry_form in(14) and a.entry_form in(14) and c.item_category=2 and c.transaction_type=6 and d.trans_type=6 and a.company_id=$company_name $batchIds_cond_fin_out group by a.entry_form,b.color_id, b.batch_id, e.detarmination_id,b.from_prod_id,e.item_description,b.body_part_id ");
		$finishing_trans_in_qty_array=array();$finishing_trans_out_qty_array=array();$trnsfInArr=array();$contru_arrx=array();$batchIDS="";
		foreach($data_array_finish_qnty_transfer_in as $row)
		{
			$finishing_trans_in_qty_array[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("to_body_part")]]+=$row[csf("trans_in_qnty")];
			$prodIdArrsx[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("to_body_part")]]['prod_id']=$row[csf("prod_id")];
			$trnsfInArr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]][$row[csf('fabric_description_id')]][$row[csf('to_body_part')]]['color_id']=$row[csf('color_id')];
			$trnsfInArr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]][$row[csf('fabric_description_id')]][$row[csf('to_body_part')]]['job_no']=$row[csf('job_no')];
			$trnsfInArr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]][$row[csf('fabric_description_id')]][$row[csf('to_body_part')]]['batch_id']=$row[csf('batch_id')];
			$trnsfInArr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]][$row[csf('fabric_description_id')]][$row[csf('to_body_part')]]['buyer_name']=$row[csf('buyer_name')];
			$trnsfInArr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]][$row[csf('fabric_description_id')]][$row[csf('to_body_part')]]['internal_ref']=$row[csf('internal_ref')];
			$trnsfInArr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]][$row[csf('fabric_description_id')]][$row[csf('to_body_part')]]['style_ref_no']=$row[csf('style_ref_no')];
			$trnsfInArr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]][$row[csf('fabric_description_id')]][$row[csf('to_body_part')]]['to_order_id']=$row[csf('to_order_id')];
			$trnsfInArr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]][$row[csf('fabric_description_id')]][$row[csf('to_body_part')]]['item_number_id']=$itemNumberArr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('batch_id')]][$row[csf('to_body_part')]][$row[csf('fabric_description_id')]]['item_number_id'];

			$fabricSlicing=explode(",", $row[csf('fabric_description_id')]);
			$fabricDes=$fabricSlicing[0];
			$gsm=$fabricSlicing[2];
			$dia=$fabricSlicing[3];

			$construction_namex=$constructtion_arr[$row[csf('fabric_description_id')]];
			$contru_arrx[$constructtion_arr[$row[csf('fabric_description_id')]]]=$construction_namex;
			$batchIDS.=$row[csf('batch_id')].",";
			//echo $row[csf('batch_id')].",";
		}
		//print_r($finishing_trans_in_qty_array);
		$batchIDS=chop($batchIDS,",");
		$batch_library=return_library_array("select id, batch_no from pro_batch_create_mst where id in($batchIDS)", "id", "batch_no");

		$data_array_transfer_in_issue=sql_select("SELECT c.color_id, c.id as batch_id, d.detarmination_id, (b.cons_quantity) as issue_qnty,b.prod_id,d.item_description,b.body_part_id 
		 from inv_issue_master a, inv_transaction b, pro_batch_create_mst c, product_details_master d
		where a.id=b.mst_id and b.pi_wo_batch_no=c.id  and b.prod_id=d.id and a.item_category=2 and a.entry_form in (71,18) and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.pi_wo_batch_no in ($batchIDS) ");//group by c.color_id, c.id, d.detarmination_id
		$issue_transIn_array=array();
		foreach($data_array_transfer_in_issue as $row)
		{
			$issue_transIn_array[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("body_part_id")]]+=$row[csf("issue_qnty")];
			$prodIdArrsTransIn[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("body_part_id")]]['prod_id']=$row[csf("prod_id")];
		}

		//for recv return
		$data_array_recv_rtn_issue=sql_select("SELECT c.color_id, c.id as batch_id, d.detarmination_id, (b.cons_quantity) as issue_qnty,b.prod_id,d.item_description,b.body_part_id 
		 from inv_issue_master a, inv_transaction b, pro_batch_create_mst c, product_details_master d
		where a.id=b.mst_id and b.pi_wo_batch_no=c.id  and b.prod_id=d.id and a.item_category=2 and a.entry_form in (46) and b.transaction_type=3 and a.status_active=1 and a.is_deleted=0 and b.pi_wo_batch_no in ($batchIDS) ");//group by c.color_id, c.id, d.detarmination_id
		$issue_recv_rtn_array=array();
		foreach($data_array_recv_rtn_issue as $row)
		{
			$issue_recv_rtn_array[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("body_part_id")]]+=$row[csf("issue_qnty")];
			$prodIdArrsRecvRtn[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("body_part_id")]]['prod_id']=$row[csf("prod_id")];
		}

		//print_r($issue_transIn_array);

		foreach($data_array_finish_qnty_transfer_out as $row)
		{
			$finishing_trans_out_qty_array[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("body_part_id")]]+=$row[csf("trans_out_qnty")];
			$prodIdArrs[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("body_part_id")]]['prod_id']=$row[csf("prod_id")];
		}


		$data_array_finsing_qty=sql_select("SELECT a.entry_form, c.color_id, b.batch_id, b.fabric_description_id, sum(b.receive_qnty) as receive_qnty,b.prod_id,d.item_description,b.body_part_id,a.issue_id 
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b,pro_batch_create_mst c ,product_details_master d  
		where a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.id=b.mst_id  and b.batch_id=c.id and b.prod_id=d.id and  a.entry_form in(37,7,52) and a.company_id=$company_name $batchIds_cond_fin group by a.entry_form,c.color_id, b.batch_id, b.fabric_description_id,b.prod_id,d.item_description,b.body_part_id,a.issue_id");

		$qc_pass_qnty_array=array();$finishing_rec_qty_array=array();
		foreach($data_array_finsing_qty as $row)
		{
			if($row[csf("entry_form")]==7) // finish fabric production entry page
			{
				//$qc_pass_qnty_array[$row[csf("color_id")]][$row[csf("batch_id")]][$constructtion_arr[$row[csf("fabric_description_id")]]]+=$row[csf("receive_qnty")];
				$qc_pass_qnty_array[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("body_part_id")]]+=$row[csf("receive_qnty")];
			}
			else if($row[csf("entry_form")]==37) // Knit Finish Fabric Receive By Garments
			{
				//$finishing_rec_qty_array[$row[csf("color_id")]][$row[csf("batch_id")]][$constructtion_arr[$row[csf("fabric_description_id")]]]+=$row[csf("receive_qnty")];
				//$prodIdArrs[$row[csf("color_id")]][$row[csf("batch_id")]][$constructtion_arr[$row[csf("fabric_description_id")]]]['prod_id']=$row[csf("prod_id")];
				$finishing_rec_qty_array[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("body_part_id")]]+=$row[csf("receive_qnty")];
				$prodIdArrs[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("body_part_id")]]['prod_id']=$row[csf("prod_id")];
			}
			else // 52 Knit Finish Fabric Issue Return
			{
				//$finish_issue_rtn_qty_array[$row[csf("color_id")]][$row[csf("batch_id")]][$constructtion_arr[$row[csf("fabric_description_id")]]]+=$row[csf("receive_qnty")];

				$finish_issue_rtn_qty_array[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("body_part_id")]]+=$row[csf("receive_qnty")];
				$issue_array_issue_rtn_id[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("fabric_description_id")]][$row[csf("body_part_id")]]['issue_id']=$row[csf("issue_id")];
			}
		}
		//echo "<pre>";print_r($finishing_rec_qty_array);die;

		// =============================== del. to store ============================
		$batchIds_cond_fin = str_replace("a.batch_id", "b.batch_id", $batchIds_cond); 
		$sql_del_store=sql_select("SELECT b.batch_id,a.color_id, b.determination_id,sum(b.current_delivery) as delivery from pro_grey_prod_delivery_dtls b,pro_batch_create_mst a where b.batch_id=a.id and  b.status_active=1 and b.is_deleted=0 and b.entry_form=54 $batchIds_cond_fin group by b.batch_id,a.color_id, b.determination_id");
		$delivery=array();
		foreach($sql_del_store as $row)
		{
			$delivery[$row[csf("color_id")]][$row[csf("batch_id")]][$constructtion_arr[$row[csf("determination_id")]]]+=$row[csf("delivery")];
			
		}
		// echo "<pre>";print_r($delivery);die;

		// =================================== issue =======================================
		$batchIds_cond_issue = str_replace("a.batch_id", "b.pi_wo_batch_no", $batchIds_cond);
		$data_array_issue=sql_select("SELECT a.id as issue_id,c.color_id, c.id as batch_id, d.detarmination_id, (b.cons_quantity) as issue_qnty,b.prod_id,b.body_part_id,d.item_description,a.issue_purpose  
		 from inv_issue_master a, inv_transaction b, pro_batch_create_mst c, product_details_master d
		where a.id=b.mst_id and b.pi_wo_batch_no=c.id  and b.prod_id=d.id and a.item_category=2 and a.entry_form in (71,18) and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 $batchIds_cond_issue ");//group by c.color_id, c.id, d.detarmination_id
		$issue_array=array();
		foreach($data_array_issue as $row)
		{
			//$issue_array[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("body_part_id")]]+=$row[csf("issue_qnty")];
			if($row[csf("issue_purpose")]==9)
			{
				$issue_array_swing[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("body_part_id")]]+=$row[csf("issue_qnty")];
				$issue_array_issue_id[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("body_part_id")]]['issue_id']=$row[csf("issue_id")];
				$issue_array_issue_purpose[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("body_part_id")]]['issue_purpose']=$row[csf("issue_purpose")];

			}
			else
			{
				$issue_array_others[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("body_part_id")]]+=$row[csf("issue_qnty")];
				$issue_array_issue_id[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("body_part_id")]]['issue_id']=$row[csf("issue_id")];
				$issue_array_issue_purpose[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("body_part_id")]]['issue_purpose']=$row[csf("issue_purpose")];


			}
			$prodIdArrs[$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("detarmination_id")]][$row[csf("body_part_id")]]['prod_id']=$row[csf("prod_id")];
		}
		// echo "<pre>";print_r($issue_array);die;

		//$table_width=2430;
		// $table_width=1030;
		$table_width=(6*330)+100;

		ob_start();
		?>
		
		<style type="text/css">
			/*#td_idss{border:none !important;}
			#td_color_idsss{border:none !important;}*/
			#change_size{font-size:12px;}
		</style>
		<fieldset style="width:<? echo $table_width;?>px" >
		    <table cellpadding="0" align="center" cellspacing="0" width="<? echo $table_width-20; ?>">
				<tr>
				   <td  width="100%" colspan="24" class="form_caption"><? echo $report_title; ?></td>
				   <!-- "<div style='color:red'> Ext. Batch not allowed.</div>". -->
				</tr>
			</table>
			<table width="915" align="center">
	            <tr>
		            <td id="change_size"><strong>Buyer Name:</strong>
	                <?php
					$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
					$all_buyer='';
					foreach($batchreport as $job_no=>$job_no_arr)
					{ 
	                    foreach ($job_no_arr as $color_id=>$colorId_arr)
	                    {
	                    	/*foreach ($colorId_arr as $batch_no=>$batchData)
	                    	{*/ 
	                    		foreach ($colorId_arr as $body_partID=>$bodyPartData)
	                    		{
	                    			foreach ($bodyPartData as $productDescription=>$row)
		                    		{
							  			if($all_buyer=='' ) $all_buyer= $row['buyer_name']; else $all_buyer.=",".$row['buyer_name'];
							  		}
						  		}
						  	//}
						}
					}
				   	$all_buyer_ids='';
				   	$buyer_ids=array_unique(explode(",",$all_buyer));
				   	foreach($buyer_ids as $bid)
				   	{
					 	if($all_buyer_ids=='' ) $all_buyer_ids=$buyer_arr[$bid]; else $all_buyer_ids.=",".$buyer_arr[$bid];  
				   	}
					echo $all_buyer_ids;//implode(",",array_unique(explode(",",$buyer_arr[$all_buyer])));
					?>
	                </td>
		            <td width="100"></td>
		            <td id="change_size"><strong>Style Ref. No:</strong>
	                <?php
						$all_style_ref='';
						foreach($batchreport as $job_no=>$job_no_arr)
						{ 
		                    foreach ($job_no_arr as $color_id=>$colorId_arr)
		                    {
		                    	/*foreach ($colorId_arr as $batch_no=>$batchData)
		                    	{*/
		                    		foreach ($colorId_arr as $body_partID=>$bodyPartData)
	                    			{
	                    				foreach ($bodyPartData as $productDescription=>$row)
		                    			{
							  				if($all_style_ref=='' ) $all_style_ref= $row['style_ref_no']; else $all_style_ref.=",".$row['style_ref_no'];
							  			}
							  		//}
							  	}
							}
						}
						echo implode(",",array_unique(explode(",",$all_style_ref)));
					?>
	                </td>
	                <td width="100"></td>
	                <td id="change_size"><strong>Internal Ref:</strong>
					<?php
					$all_in_ref='';
					foreach($batchreport as $job_no=>$job_no_arr)
					{ 
	                    foreach ($job_no_arr as $color_id=>$colorId_arr)
	                    {
	                    	/*foreach ($colorId_arr as $batch_no=>$batchData)
	                    	{ */
	                    		foreach ($colorId_arr as $body_partID=>$bodyPartData)
	                    		{
	                    			foreach ($bodyPartData as $productDescription=>$row)
		                    		{
										if($all_in_ref=='' ) $all_in_ref= $row['grouping']; else $all_in_ref.=",".$row['grouping'];
									}
								}
							//}
						}
					}
					echo implode(",",array_unique(explode(",",$all_in_ref)));
					?> 
	                </td>
	                <td width="100"></td>
	                <td><strong>Job No:</strong><?php
					$all_job='';
					foreach($batchreport as $job_no=>$job_no_arr)
					{ 
	                    foreach ($job_no_arr as $color_id=>$colorId_arr)
	                    {
	                    	/*foreach ($colorId_arr as $batch_no=>$batchData)
	                    	{*/
	                    		foreach ($colorId_arr as $body_partID=>$bodyPartData)
	                    		{
	                    			foreach ($bodyPartData as $productDescription=>$row)
		                    		{
	                    				if($all_job=='' ) $all_job= $row['job_no_mst']; else $all_job.=",".$row['job_no_mst'];
	                    			}
	                    		//}
	                    	}						  	
						}
					}
					echo implode(",",array_unique(explode(",",$all_job)));
					?></td>
					<td width="100"></td>
		        </tr>
	        </table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" width="<? echo $table_width-20; ?>">
				<thead>
					<tr>
	                	<th width="35">SL.</th>
						<th width="100">Buyer</th>
						<th width="100">Style</th>
						<th width="100">Internal Ref</th>
						<!-- <th width="100">Job No</th> -->
						<th width="100">Gmt Item</th>
						<th width="100">Color Name</th>
						<!-- <th width="100">Batch No</th> -->
						<th width="100">Body Part</th>
						<th width="200">Fabrication</th>
						<th width="50">GSM</th>

						<th width="50">Unit</th>
						<th width="100">Booking</th>
						<th width="100">Body to Trim Ratio</th>
						<th width="100">Additionl Booking</th>
						<th width="100">Total Booking</th>

	                  				
						<th width="80">Received</th>
						<th width="80">Transfer In</th>
						<th width="80">Transfer Out</th>
						<th width="80">Total Receive</th>
						<th width="80">Receive Balance</th>

						<th width="80">Issue to Cutting</th>
						<th width="80">Other's Dept Issue</th>
						<th width="80">Total Issue</th>
						<th>Stock In Hand</th>
	                </tr>
	                
				</thead>
	        </table> 
	        <div style="width:<? echo $table_width;?>px; overflow-y:scroll;  max-height:400px;" id="scroll_body">
	            <table id="tbl_list_search" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" width="<? echo $table_width-20; ?>">
	            	
		            <?
					$i=1;
					foreach($batchreport as $job_no=>$job_no_arr)
					{						
						foreach ($job_no_arr as $color_id=>$colorId_arr)
	                    {
	                    	$fab_finQty_color_bal_arr=array();

	                    	$color_total_recv_arr=array();
	                    	$color_total_issue_rtn_arr=array();
	                    	$color_total_trans_in_arr=array();
	                    	$color_total_trans_out_arr=array();
	                    	$color_total_additional_book_qnty_arr=array();
	                    	$color_total_recvTot_arr=array();
	                    	$color_total_BalancQntyArr=array();
	                    	$color_total_IssueToCuttingArr=array();
	                    	$color_total_OtherIssueArr=array();
	                    	$color_total_IssueArr=array();
	                    	$color_total_StockInHandArr=array();


	                    	$job_total_recv_arr=array();
	                    	$job_total_issue_rtn_arr=array();
	                    	$job_total_trans_in_arr=array();
	                    	$job_total_trans_out_arr=array();
	                    	$job_total_additional_book_qnty_arr=array();
	                    	$job_total_recvTot_arr=array();
	                    	$job_total_BalancQntyArr=array();
	                    	$job_total_IssueToCuttingArr=array();
	                    	$job_total_OtherIssueArr=array();
	                    	$job_total_IssueArr=array();
	                    	$job_total_StockInHandArr=array();

	                    	$color_total_issue_arr=array();
	               
	                    	$color_balance_issue_qty_arr=array();
	                    	$color_balance_trans_out_qty_arr=array();

	                    	$color_total_inHand_arr=array();
	                    	$color_total_inHand_transIn_arr=array();
	                    	$color_balance_inHand_qty_arr=array();

	                    	
		                    /*foreach ($colorId_arr as $batch_no=>$batchData)
		                    {*/

		                    	
		                    	$topBottomBookingQnty=array();$topBottomBookingQntyx=0;
		                    	foreach ($colorId_arr as $body_partID=>$bodyPartData)
	                    		{
	                    			foreach ($bodyPartData as $productDescription=>$row)
		                    		{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										$po_ids=array_unique(explode(",",$row[("po_id")]));

										$batchNoIds=$batchreportWise[$job_no][$row["color_id"]][$row[("body_part_id")]][$row[("lib_yarn_count_deter_id")]]['id'];
				                       	?>
				                        
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				    						<td width="35" align="left" id="td_id" valign="middle"><? echo $i; ?></td>
				                            
				                            <td width="100"><p><? echo $buyer_arr[$row[("buyer_name")]]; ?></p></td>
				                            <td width="100"><p><? echo $row[("style_ref_no")]; ?></p></td>
				                            <td width="100"><p><? echo $row[("grouping")]; ?></p></td>
				                            <td width="100"><p><? echo $garments_item[$row[("item_number_id")]]; ?></p></td>

				                           <!--  <td width="100"><p><? //echo $job_no; ?></p></td> -->

				                            <td width="100" id="td_color_id" title="ColorId=<? echo $color_id;?>" valign="middle"><div style="word-break:break-all"><? echo $color_library[$row["color_id"]]; ?></div></td>
				                           
				                            <!-- <td width="100" title="Batch ID:<? //echo $row[("id")]; ?>"><div style="word-break:break-all"><? //echo $row[("batch_no")];?></div></td> -->

				                            <td width="100" title="<? echo $row[("body_part_id")];?>"><p><? echo $body_part[$row[("body_part_id")]]; ?></p></td>
				                            <td width="200" title="<? echo $productDescription; ?>"><p><? echo $constructtion_arr[$productDescription]; ?></p></td>
				                            <td width="50" align="center"><p><? echo $row[("gsm_weight")]; ?></p></td>
				                            <td width="50" align="center"><p><?

				                            	if($row[("body_part_type")]==40 || $row[("body_part_type")]==50)
				                            	{
				                            		echo "Pcs";

				                            		$colrID=$bodypartTypewiseColorArr[$row[("body_part_type")]]['color_id'];

				                            		if($colrID!="")
													{
														$po_id_arr_colarCuffPcs[$job_no][$color_id][$row[("body_part_id")]][$row[("lib_yarn_count_deter_id")]]['body_part_id'];

														if($row[("body_part_type")]==40)
														{
															$plan_cut=$order_plan_qty_arr[$color_id]['plan'];

															$collar_ex_per=$collar_cuff_percent_arr[$row[("body_part_type")]][$color_id];

															if($row[("body_part_type")]==50) { if($collar_ex_per==0 || $collar_ex_per=="") $collar_ex_per=$cuff_excess_percent; else $collar_ex_per=$collar_ex_per; }
															else if($row[("body_part_type")]==40) { if($collar_ex_per==0 || $collar_ex_per=="") $collar_ex_per=$colar_excess_percent; else $collar_ex_per=$collar_ex_per; }
															$colar_excess_per=number_format(($plan_cut*$collar_ex_per)/100,6,".",",");
															$plan_cut_perc=($plan_cut+$colar_excess_per);

															$bodyPartWisePcs40[$row[("body_part_type")]][$colrID]=$plan_cut;
														}
														else if($row[("body_part_type")]==50 )
														{
															$plan_cut=($order_plan_qty_arr[$color_id]['plan'])*2;
															

															if($row[("body_part_type")]==50) { if($collar_ex_per==0 || $collar_ex_per=="") $collar_ex_per=$cuff_excess_percent; else $collar_ex_per=$collar_ex_per; }
															else if($row[("body_part_type")]==40) { if($collar_ex_per==0 || $collar_ex_per=="") $collar_ex_per=$colar_excess_percent; else $collar_ex_per=$collar_ex_per; }
															$colar_excess_per=number_format(($plan_cut*$collar_ex_per)/100,6,".",",");
															$plan_cut_perc=($plan_cut+$colar_excess_per);

															$bodyPartWisePcs50[$row[("body_part_type")]][$colrID]=$plan_cut_perc;
														} 
														
													}
				                            	}
				                            	else
				                            	{
				                            		echo $unit_of_measurement[$row[("uom")]]; 
				                            	}?>
											</p></td>
				                            <td width="100" align="right"><p><? 

				                            	if($row[("body_part_type")]==50 && ($colrID==$colrID))
				                            	{
				                            		$bookingQntys=	$bodyPartWisePcs50[$row[("body_part_type")]][$colrID];
				                            		echo $bookingQntys;
				                            	}
				                            	else if($row[("body_part_type")]==40 && ($colrID==$colrID))
				                            	{
				                            		$bookingQntys=$bodyPartWisePcs40[$row[("body_part_type")]][$colrID];
				                            		echo $bookingQntys;
				                            	}
				                            	else
				                            	{
				                            		if($row[("body_part_type")]==1 || $row[("body_part_type")]==20)
				                            		{
				                            			$topBottomBookingQnty[$row[("body_part_type")]][$color_id]+=$row[("booking_qnty")];

				                            		}

				                            		$bookingQntys= $row[("booking_qnty")]; 
				                            		echo $bookingQntys;
				                            	}



				                        		?></p></td>
				                            <td width="100" align="right"><p><?
												$topBottomBookingQntyx+=$topBottomBookingQnty[$row[("body_part_type")]][$color_id];
				                            	if($row[("body_part_type")]==30)
				                            	{
				                            		//$colrIDs2=$bodypartTypewiseColorArr[$row[("body_part_type")]]['color_id'];
				                            		$othersQntyPercent= ($row[("booking_qnty")]/$topBottomBookingQntyx)*100;
				                            		echo $othersQntyPercent."%";
				                            	}

				                            	 ?></p>
				                            </td>
				                            <td width="100" align="right"><p><? 

					                            $additionalBookingQnty= $additonalBookingQntyArr[$job_no][$row["color_id"]][$row[("body_part_id")]][$row[("lib_yarn_count_deter_id")]]['additional_booking_qnty'];
					                            echo $additionalBookingQnty;
					                            $color_total_additional_book_qnty_arr[$color_id]=$additionalBookingQnty;
					                            $job_total_additional_book_qnty_arr[$job_no]=$additionalBookingQnty;

				                             ?></p></td>
				                            <td width="100" align="right"><p><?
				                            	$totalBooking= $additionalBookingQnty+$bookingQntys;
				                            	echo $totalBooking;
				                             ?></p></td>
				                            
				                            <?
				                          
						                	//fin recv qty
						                		//$fin_recv_qnty=$finishing_rec_qty_array[$color_id][$row["id"]][$contruction];
						                		//$pordIDS=$prodIdArrs[$color_id][$row["id"]][$contruction]['prod_id'];
						                		$fin_recv_qnty=$finishing_rec_qty_array[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]];
						                		$pordIDS=$prodIdArrs[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]]['prod_id'];



						                	//Recv Rtn 
						                		$fin_recv_rtn_qnty=$issue_recv_rtn_array[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]];
												$pordIDS_recv_rtn=$prodIdArrsRecvRtn[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]]['prod_id'];

												$totRecvQnty=$fin_recv_qnty-$fin_recv_rtn_qnty;
												/*<a href='#report_details' onClick="openmypageShow6('<? echo $row["po_id"]; ?>','<? echo $pordIDS; ?>','<? echo $color_id; ?>','<? echo 1; ?>','total_receive_popup_show6','<? echo $batchNoIds; ?>','<? echo $job_no; ?>','<? echo $row[("buyer_name")]; ?>','<? echo $row[("style_ref_no")]; ?>','<? echo $row[("grouping")]; ?>','<? echo $prodIdArr[$job_no][$color_id][$batchNoIds]['detarmination_id']; ?>','<? echo $productDescription; ?>','<? echo $gsm; ?>','<? echo $dia; ?>');">*/
						                		
						                		?>
						                		<td width="80" align="right"><p><? echo number_format($fin_recv_qnty-$fin_recv_rtn_qnty,2,'.',''); ?></p></td>
						                		<?
						                		$color_total_recv_arr[$color_id]=$totRecvQnty;
						                		$job_total_recv_arr[$job_no]=$totRecvQnty;
						                	

						                	//transfer In row
						                		$fin_recv_qnty=$finishing_rec_qty_array[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]];
						                		$fin_issue_rtn_qnty=$finish_issue_rtn_qty_array[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]];
						                		
						                		$fin_trans_in_qnty=$finishing_trans_in_qty_array[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]];
						                		$pordIDS=$prodIdArrs[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]]['prod_id'];

						                		$recv_qnty_for_inHand_arr[$productDescription]=$fin_recv_qnty+$fin_issue_rtn_qnty+$fin_trans_in_qnty;

						                		/*<a href='#report_details' onClick="openmypageShow6('<? echo $row["po_id"]; ?>','<? echo $pordIDS; ?>','<? echo $color_id; ?>','<? echo 1; ?>','total_trans_in_popup_show6','<? echo $batchNoIds; ?>','<? echo $job_no; ?>','<? echo $row[("buyer_name")]; ?>','<? echo $row[("style_ref_no")]; ?>','<? echo $row[("grouping")]; ?>','<? echo $prodIdArr[$job_no][$color_id][$batchNoIds]['detarmination_id']; ?>','<? echo $productDescription; ?>','<? echo $gsm; ?>','<? echo $dia; ?>');">*/
						                		?>
						                		<td width="80" align="right"><p><? echo number_format($fin_trans_in_qnty,2,'.',''); ?></p></td>
						                		<?
						                		$color_total_trans_in_arr[$color_id]=$fin_trans_in_qnty;
						                		$job_total_trans_in_arr[$job_no]=$fin_trans_in_qnty;

						                		
						                	//transfer Out
						                		$pordIDS_issue=$prodIdArrs[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]]['prod_id'];
						                		$fin_issue_qnty=$issue_array[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]];
						                		$fin_trans_out_qnty=$finishing_trans_out_qty_array[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]];
						                		
						                		$issue_qty_for_inHand_arr[$productDescription]=$fin_issue_qnty+$fin_trans_out_qnty;

						                		/*<a href='#report_details' onClick="openmypageShow6('<? echo $row["po_id"]; ?>','<? echo $pordIDS_issue; ?>','<? echo $color_id; ?>','<? echo 1; ?>','total_trans_out_popup_show6','<? echo $batchNoIds; ?>','<? echo $job_no; ?>','<? echo $row[("buyer_name")]; ?>','<? echo $row[("style_ref_no")]; ?>','<? echo $row[("grouping")]; ?>','<? echo $prodIdArr[$job_no][$color_id][$batchNoIds]['detarmination_id']; ?>','<? echo $productDescription; ?>','<? echo $gsm; ?>','<? echo $dia; ?>');">*/
						                		?>
						                		<td width="80" align="right" title="fin ret=<? //echo $fin_issue_rtn_qnty;?>">
						                		<p><? echo "-".number_format($fin_trans_out_qnty,2,'.',''); ?></p>
						                		</td> 
						                		<?
						                		$color_total_trans_out_arr[$color_id]=$fin_trans_out_qnty;
						                		$job_total_trans_out_arr[$job_no]=$fin_trans_out_qnty;



						                	//Total Recv
						                		?>
						                		<td width="80" align="right"><p><? 
							                		$totalRecv=($fin_recv_qnty+$fin_trans_in_qnty)-($fin_recv_rtn_qnty+$fin_trans_out_qnty);

							                		echo number_format($totalRecv,2,'.',''); 
							                		$color_total_recvTot_arr[$color_id]=$totalRecv;
							                		$job_total_recvTot_arr[$job_no]=$totalRecv;
							                		
							                	?></p>
						                		</td>

						                		<?

						                	//Total Recv Balance
						                		?>
						                		<td width="80" align="right"><p><? 
						                		//$totalRecv=($fin_recv_qnty+$fin_trans_in_qnty)-$fin_trans_out_qnty;
						                		$totBalanceQnty=$totalBooking-$totalRecv;
						                		echo $totBalanceQnty;
						                		$color_total_BalancQntyArr[$color_id]=$totBalanceQnty;
						                		$job_total_BalancQntyArr[$job_no]=$totBalanceQnty;

						                		//echo number_format($totalRecv,2,'.',''); ?></p></td>

						                		<?


						                	//issue return row

						                		$issueID= $issue_array_issue_id[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]]['issue_id'];
						                		$issue_rtn_ID= $issue_array_issue_rtn_id[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]]['issue_id'];
												$issuePurpose=$issue_array_issue_purpose[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]]['issue_purpose'];

												if($issuePurpose==9)
												{
													if($issue_rtn_ID==$issueID)
													{
														$fin_issue_rtn_qnty_swingArr[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]]=$finish_issue_rtn_qty_array[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]];
						                				$pordIDS=$prodIdArrs[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]]['prod_id'];
													}

												}
												else
												{
													if($issue_rtn_ID==$issueID)
													{
														$fin_issue_rtn_qnty_othersArr[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]]=$finish_issue_rtn_qty_array[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]];
						                				$pordIDS=$prodIdArrs[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]]['prod_id'];
													}
												}
						                	
						                	// net issue qty Swing purupose
						                		$fin_issue_qnty_swing=$issue_array_swing[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]];
						                		$pordIDS_issue=$prodIdArrs[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]]['prod_id'];
						                		$fin_issue_rtn_qnty=$finish_issue_rtn_qty_array[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]];
						                		$fin_issue_rtn_qnty_swing=$fin_issue_rtn_qnty_swingArr[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]];
						                		$net_issued_qty_swing=$fin_issue_qnty_swing-$fin_issue_rtn_qnty_swing;
						                		
						                		/*<a href='#report_details' onClick="openmypageShow6('<? echo $row["po_id"]; ?>','<? echo $pordIDS_issue; ?>','<? echo $color_id; ?>','<? echo 1; ?>','total_issue_popup_show6','<? echo $batchNoIds; ?>','<? echo $job_no; ?>','<? echo $row[("buyer_name")]; ?>','<? echo $row[("style_ref_no")]; ?>','<? echo $row[("grouping")]; ?>','<? echo $prodIdArr[$job_no][$color_id][$batchNoIds]['detarmination_id']; ?>','<? echo $productDescription; ?>','<? echo $gsm; ?>','<? echo $dia; ?>');">*/
						                		?>
						                		<td width="80" align="right" title="">
						                		<p><? echo number_format($net_issued_qty_swing,2,'.',''); ?></p>
						                		</td> 
						                		<?
						                		$color_total_IssueToCuttingArr[$color_id]=$net_issued_qty_swing;
						                		$job_total_IssueToCuttingArr[$job_no]=$net_issued_qty_swing;
		                    	



						                	// net issue qty Others purupose
						                		$fin_issue_qnty_orthers=$issue_array_others[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]];
						                		$pordIDS_issue=$prodIdArrs[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]]['prod_id'];
						                		$fin_issue_rtn_qnty=$finish_issue_rtn_qty_array[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]];
						                		$fin_issue_rtn_qnty_others=$fin_issue_rtn_qnty_othersArr[$color_id][$batchNoIds][$productDescription][$row[("body_part_id")]];
						                		$net_issued_qty_others=$fin_issue_qnty_orthers-$fin_issue_rtn_qnty_others;
						                		

						                		/*<a href='#report_details' onClick="openmypageShow6('<? echo $row["po_id"]; ?>','<? echo $pordIDS_issue; ?>','<? echo $color_id; ?>','<? echo 1; ?>','total_issue_popup_show6','<? echo $batchNoIds; ?>','<? echo $job_no; ?>','<? echo $row[("buyer_name")]; ?>','<? echo $row[("style_ref_no")]; ?>','<? echo $row[("grouping")]; ?>','<? echo $prodIdArr[$job_no][$color_id][$batchNoIds]['detarmination_id']; ?>','<? echo $productDescription; ?>','<? echo $gsm; ?>','<? echo $dia; ?>');">*/
						                		?>
						                		<td width="80" align="right" title="">
						                		<p><? echo number_format($net_issued_qty_others,2,'.',''); ?></p>
						                		</td> 
						                		<?
						                		$color_total_OtherIssueArr[$color_id]=$net_issued_qty_others;
						                		$job_total_OtherIssueArr[$job_no]=$net_issued_qty_others;
		                    	
						                		?>
						                		<td width="80" align="right" title="">
						                			<p>
						                				<? 
						                				$totalIssueQnty=$net_issued_qty_swing+$net_issued_qty_others;
						                				echo number_format($totalIssueQnty,2);
						                				$color_total_IssueArr[$color_id]=$totalIssueQnty;
						                				$job_total_IssueArr[$job_no]=$totalIssueQnty;

						                				

						                				?>
						                			</p>
						                			
						                		</td>

						                		<?
						                	

						                	
						                	


						                	 // Stock In Hand
						                		$recv_qty=$recv_qnty_for_inHand_arr[$contruction];
						                		$net_issue=$issue_qty_for_inHand_arr[$contruction];
						                		//$stock_in_hand=$recv_qty-$net_issue;
						                		//echo $recv_qty.'-'.$net_issue;
						                		?>
						                		<td align="right"><? 
						                		
							                	$stock_in_hand=$totalRecv-$totalIssueQnty;
							                	$color_total_StockInHandArr[$color_id]=$stock_in_hand;
							                	$job_total_StockInHandArr[$job_no]=$stock_in_hand;
						                		echo number_format($stock_in_hand,2,'.',''); ?></td>
						                		<?

						                		
						                		//$color_total_inHand_arr[$contruction]+=$stock_in_hand;
						                	
						                	?>
						                	
										</tr>
							 			<?
			                            $i++;
			                        }



				                    $color_total_additionalBookingQnty+= $color_total_additional_book_qnty_arr[$color_id];
				                    $color_total_RecvQnty+=$color_total_recv_arr[$color_id];
			                    	$color_total_TransInQnty+=$color_total_trans_in_arr[$color_id];
			                    	$color_total_TransOutQnty+=$color_total_trans_out_arr[$color_id];
			                    	$color_total_RecvTotQnty+=$color_total_recvTot_arr[$color_id];
			                    	$color_total_BalanceQnty+=$color_total_BalancQntyArr[$color_id];

			                    	$color_total_IssueToCuttingQnty+=$color_total_IssueToCuttingArr[$color_id];
			                    	$color_total_OtherIssueQnty+=$color_total_OtherIssueArr[$color_id];
			                    	$color_total_IssueQnty+=$color_total_IssueArr[$color_id];

			                    	$color_total_StockInhandQnty+=$color_total_StockInHandArr[$color_id];





			                    	$job_total_additionalBookingQnty+= $job_total_additional_book_qnty_arr[$job_no];
				                    $job_total_RecvQnty+=$job_total_recv_arr[$job_no];
			                    	$job_total_TransInQnty+=$job_total_trans_in_arr[$job_no];
			                    	$job_total_TransOutQnty+=$job_total_trans_out_arr[$job_no];
			                    	$job_total_RecvTotQnty+=$job_total_recvTot_arr[$job_no];
			                    	$job_total_BalanceQnty+=$job_total_BalancQntyArr[$job_no];

			                    	$job_total_IssueToCuttingQnty+=$job_total_IssueToCuttingArr[$job_no];
			                    	$job_total_OtherIssueQnty+=$job_total_OtherIssueArr[$job_no];
			                    	$job_total_IssueQnty+=$job_total_IssueArr[$job_no];

			                    	$job_total_StockInhandQnty+=$job_total_StockInHandArr[$job_no];


			                    	
		                        }
		                    //} 

		                    //$finishing_trans_in_qty_array[$row[csf("job_no")]][$row[csf("color_id")]][$row[csf("batch_id")]][$constructtion_arr[$row[csf("fabric_description_id")]]][$row[csf("internal_ref")]][$row[csf("style_ref_no")]][$row[csf("buyer_name")]]

		                    //================
		                    	//transfer  in part in this block
		                   
		                    //================

		                    // color total below
		                    ?>
		                    <tr class="tbl_bottom">
		                        <td colspan="6"><strong>Color Total</strong></td>
		                        <td colspan="6"><strong></strong></td>
		                		<td  align="right"><? echo number_format($color_total_additionalBookingQnty,2,'.',''); ?></td>
		                		<td></td>
		                		<td  align="right"><? echo number_format($color_total_RecvQnty,2,'.',''); ?></td>
		                		<td  align="right"><? echo number_format($color_total_TransInQnty,2,'.',''); ?></td>
		                		<td  align="right"><? echo "-".number_format($color_total_TransOutQnty,2,'.',''); ?></td>
		                		<td  align="right"><? echo number_format($color_total_RecvTotQnty,2,'.',''); ?></td>
		                		<td  align="right"><? echo number_format($color_total_BalanceQnty,2,'.',''); ?></td>
		                		<td  align="right"><? echo number_format($color_total_IssueToCuttingQnty,2,'.',''); ?></td>
	                			<td  align="right"><? echo number_format($color_total_OtherIssueQnty,2,'.',''); ?></td>
	                			<td  align="right"><? echo number_format($color_total_IssueQnty,2,'.',''); ?></td>
	                			<td  align="right"><? echo number_format($color_total_StockInhandQnty,2,'.',''); ?></td>
		                    </tr>
		                    <?

			                    $color_total_additional_book_qnty_arr[$color_id]=0;
			                    $color_total_recv_arr[$color_id]=0;
		                    	$color_total_trans_in_arr[$color_id]=0;
		                    	$color_total_trans_out_arr[$color_id]=0;
		                    	$color_total_recvTot_arr[$color_id]=0;
		                    	$color_total_BalancQntyArr[$color_id]=0;


			                    $color_total_IssueToCuttingArr[$color_id]=0;
		                    	$color_total_OtherIssueArr[$color_id]=0;
		                    	$color_total_IssueArr[$color_id]=0;
		                    	$color_total_StockInHandArr[$color_id]=0;



			                    $color_total_additionalBookingQnty= 0;
			                    $color_total_RecvQnty=0;
		                    	$color_total_TransInQnty=0;
		                    	$color_total_TransOutQnty=0;
		                    	$color_total_RecvTotQnty=0;
		                    	$color_total_BalanceQnty=0;

		                    	$color_total_IssueToCuttingQnty=0;
		                    	$color_total_OtherIssueQnty=0;
		                    	$color_total_IssueQnty=0;
		                    	$color_total_StockInhandQnty=0;
		                }// job total below
		                ?>
				        <tr class="tbl_bottom">
	                        <td colspan="6"><strong>Job Total</strong></td>
	                        <td colspan="6"><strong></strong></td>
	                		<td  align="right"><? echo number_format($job_total_additionalBookingQnty,2,'.',''); ?></td>
	                		<td></td>
	                		<td  align="right"><? echo number_format($job_total_RecvQnty,2,'.',''); ?></td>
	                		<td  align="right"><? echo number_format($job_total_TransInQnty,2,'.',''); ?></td>
	                		<td  align="right"><? echo "-".number_format($job_total_TransOutQnty,2,'.',''); ?></td>
	                		<td  align="right"><? echo number_format($job_total_RecvTotQnty,2,'.',''); ?></td>
	                		<td  align="right"><? echo number_format($job_total_BalanceQnty,2,'.',''); ?></td>
	                		<td  align="right"><? echo number_format($job_total_IssueToCuttingQnty,2,'.',''); ?></td>
                			<td  align="right"><? echo number_format($job_total_OtherIssueQnty,2,'.',''); ?></td>
                			<td  align="right"><? echo number_format($job_total_IssueQnty,2,'.',''); ?></td>
                			<td  align="right"><? echo number_format($job_total_StockInhandQnty,2,'.',''); ?></td>
	                    </tr>
		                <?
					}
					?>
				</table> 
			</div>
		</fieldset>
		<?
	}
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename**$report_type";
	exit();
}


if($action=="receive_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,$unicode,'','');

	extract($_REQUEST);
	?>

	<fieldset style="width:1550px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		?>
		<div style="width:870px;" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="1545" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="18">Receive Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="70">Receive Date</th>
						<th width="80">Dyeing Source</th>
						<th width="110">Dyeing Company</th>
						<th width="100">Challan No</th>
						<th width="80">Color</th>
						<th width="80">Batch No</th>
						<th width="60">Rack No</th>
						<th width="80">Grey Used</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="70">Process Loss.</th>
						<th width="60">QC ID</th>
						<th width="80">QC Name</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th>Collar/Cuff Pcs</th>
					</tr>
				</thead>
				<tbody>
					<?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
					$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
					$po_number_no_arr=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number");

					$grey_sql="select a.id,b.prod_id, b.used_qty,b.dtls_id from inv_receive_master a,pro_material_used_dtls b where a.id=b.mst_id  and b.item_category=13 and b.entry_form=37";
					$dtlsgrey=sql_select($grey_sql);
					$grey_used_arr=array();
					foreach($dtlsgrey as $row)
					{
						$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty']+=$row[csf('used_qty')];
					}
				//print_r($grey_used_arr);
					$i=1;

					$mrr_sql="select a.id,a.recv_number, a.booking_no,a.receive_date,a.knitting_source,a.knitting_company,a.challan_no,a.emp_id,a.qc_name,b.rack_no as rack_no, b.prod_id,b.batch_id,b.body_part_id,b.gsm,b.width,c.dtls_id, sum(c.quantity) as quantity, sum(c.returnable_qnty) as returnable_qnty,c.color_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d
					where a.id=b.mst_id and b.id=c.dtls_id and b.batch_id = d.id and a.entry_form in (7,37,66,68) and c.entry_form in (7,37,66,68)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and d.status_active = 1 and d.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.prod_id in ( $prod_id ) and c.color_id='$color' and c.trans_id!=0 and c.trans_type=1 group by a.id,a.recv_number, a.receive_date,a.booking_no, a.emp_id,b.rack_no,b.prod_id,b.body_part_id,c.dtls_id,c.color_id,a.knitting_source,a.knitting_company,a.challan_no,a.qc_name,b.batch_id,b.gsm,b.width";

				//echo $mrr_sql;

					$dtlsArray=sql_select($mrr_sql);
					$tot_grey_qty=0;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];
						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}
						$grey_used_qty=$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty'];
						$process_loss=100-($row[csf('quantity')]/$grey_used_qty)*100;
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="80"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
							<td width="110"><p><? echo $knitting_company; ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="80"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td width="60"><p><? echo $row[csf('rack_no')]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($grey_used_qty,2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td width="70" title="Fin Recv Qty/Grey Qty*100" align="right"><p><? echo number_format($process_loss,2); ?></p></td>
							<td width="60" align="center"><p><? echo $row[csf('emp_id')]; ?></p></td>
							<td width="80" align="center"><p><? echo $row[csf('qc_name')]; ?></p></td>

							<td width="200" ><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td width="50" align="center"><p><? echo $row[csf('gsm')]; ?>&nbsp;</p></td>
							<td width="50" align="center"><p><? echo $row[csf('width')]; ?></p></td>
							<td><p><? //echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$tot_grey_qty+=$grey_used_qty;
						$tot_reject_qty+=$row[csf('returnable_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right"><? echo number_format($tot_grey_qty,2); ?> </td>
						<td align="right"><? echo number_format($tot_qty,2); ?> </td>
						<td colspan="5"> </td>
						<td align="right">&nbsp;<? //echo number_format($tot_qty,2); ?>&nbsp;</td>
						<td align="right">&nbsp;<? //echo number_format($tot_reject_qty,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			<table border="1" class="rpt_table" rules="all" width="1100" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="11">Issue Return Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="80">Ret. Date</th>
						<th width="100">Dyeing Source</th>
						<th width="120">Dyeing Company</th>
						<th width="100">Challan No</th>
						<th width="100">Color</th>
						<th width="100">Batch No</th>
						<th width="80">Rack</th>
						<th width="80">Ret. Qty</th>
						<th width="">Fabric Des.</th>

					</tr>
				</thead>
				<tbody>
					<?
					$sql_issue="select a.knitting_source,a.knitting_company,b.batch_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68)";
					$result_issue=sql_select($sql_issue);
					$issue_arr=array();
					foreach($result_issue as $row)
					{
						$issue_arr[$row[csf('batch_id')]]['rack']=$row[csf('rack')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_source']=$row[csf('knitting_source')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_company']=$row[csf('knitting_company')];
					}
					//print_r($issue_arr);
					$i=1;
				//and a.entry_form in (7,37,66,68)
					$ret_sql="select a.recv_number,a.challan_no,a.issue_id, a.receive_date,b.prod_id,b.pi_wo_batch_no, sum(c.quantity) as quantity,sum(c.returnable_qnty) as returnable_qnty,c.color_id from  inv_receive_master a, inv_transaction b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in (52) and c.entry_form in (52)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.color_id='$color' and c.prod_id in ( $prod_id ) and c.trans_id!=0 group by a.recv_number,a.challan_no, a.receive_date,b.prod_id,a.issue_id,b.pi_wo_batch_no,c.color_id";
				//echo $ret_sql;

					$retDataArray=sql_select($ret_sql);

					foreach($retDataArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$rack=$issue_arr[$row[csf('pi_wo_batch_no')]]['rack'];
					//echo $row[csf('pi_wo_batch_no')].'='.$batch_no_arr[$row[csf('pi_wo_batch_no')]];
						$knit_dye_source=$issue_arr[$row[csf('pi_wo_batch_no')]]['knit_dye_source'];
						$knit_dye_company=$issue_arr[$row[csf('pi_wo_batch_no')]]['knit_dye_company'];

						if($knit_dye_source==1)
						{
							$knitting_company=$company_arr[$knit_dye_company];
							//$knitting_company=$knit_dye_company;
						}
						else
						{
							$knitting_company=$supplier_name_arr[$knit_dye_company];
							//$knitting_company=$knit_dye_company;
						}
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="80"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="100"><p><? echo $knitting_source[$knit_dye_source]; ?></p></td>
							<td width="120" ><p><? echo $knitting_company; ?></p></td>
							<td width="100" ><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
							<td  width="100" align="right"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td  width="100" align="right"><p><? echo $batch_no_arr[$row[csf('pi_wo_batch_no')]]; ?></p></td>
							<td  width="80" align="right"><p><? echo $row[csf('Rack')]; ?></p></td>
							<td  width="80" align="right"><p><? echo $row[csf('quantity')]; ?></p></td>

							<td align="right"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
						</tr>
						<?
						$tot_issue_return_qty+=$row[csf('quantity')];
					//$tot_returnable_qnty+=$row[csf('returnable_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_issue_return_qty,2); ?>&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			<br>
			<table border="1" class="rpt_table" rules="all" width="1060" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="11">Transfer In Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="130">Transfer ID</th>
						<th width="80">Transfer Date</th>
						<th width="80">Trans. From Order</th>
						<th width="80">Trans. To Order</th>
						<th width="100">Challan</th>
						<th width="100">Color</th>
						<th width="100">Batch</th>
						<th width="70">Rack</th>
						<th width="70">Qty</th>
						<th width="">Fabric Des.</th>
					</tr>
				</thead>
				<tbody>
					<?

				//$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
					$i=1;
				//$sql_transfer_out="select a.transfer_system_id, a.challan_no,a.transfer_date, b.uom, b.from_prod_id,a.from_order_id,a.to_order_id,b.rack,b.batch_id,c.color_id,sum(b.transfer_qnty) as transfer_out_qnty from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=$companyID and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=15 and c.trans_type=5 and c.color_id='$color' and a.transfer_criteria=4 and a.to_order_id in($po_id) and c.prod_id in ( $prod_id )  and a.item_category=2 and b.status_active=1 and b.is_deleted=0 group by  a.transfer_system_id, a.challan_no,a.transfer_date, b.uom, b.from_prod_id,b.from_order_id,b.to_order_id,b.rack,b.batch_id,c.color_id";

					$sql_transfer_out=" select a.transfer_system_id, a.challan_no,a.transfer_date, b.uom, b.from_prod_id,a.from_order_id,a.to_order_id,b.rack,b.batch_id,c.color_id,sum(b.transfer_qnty) as transfer_out_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.company_id=3 and a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(14,15) and c.trans_type=5 and c.color_id='2' and a.transfer_criteria=4 and a.to_order_id in($po_id) and c.prod_id in ( $prod_id ) and a.item_category=2 and b.status_active=1 and b.is_deleted=0 group by a.transfer_system_id, a.challan_no,a.transfer_date,a.from_order_id, b.uom, b.from_prod_id,b.from_order_id,b.to_order_id,b.rack,b.batch_id,c.color_id,a.to_order_id";

					$transfer_out=sql_select($sql_transfer_out);

					foreach($transfer_out as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="130"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
							<td width="80"><p><? echo  change_date_format($row[csf('transfer_date')]); ?></p></td>
							<td width="80"><p><? echo $po_number_no_arr[$row[csf('from_order_id')]]; ?></p></td>
							<td width="80"><p><? echo $po_number_no_arr[$row[csf('to_order_id')]]; ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="100" ><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td width="70" ><p><? echo $row[csf('rack')]; ?> &nbsp;</p></td>
							<td width="70" align="right" ><p><? echo number_format($row[csf('transfer_out_qnty')],2); ?> &nbsp;</p></td>
							<td ><p><? echo $product_arr[$row[csf('from_prod_id')]]; ?></p></td>
						</tr>
						<?
						$tot_trans_qty+=$row[csf('transfer_out_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right"><? echo number_format($tot_trans_qty,2); ?>&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total Receive Balance</td>
						<td align="right">&nbsp;<? $tot_balance=$tot_qty+$tot_issue_return_qty+$tot_trans_qty; echo number_format($tot_balance,2); ?>&nbsp;</td>
						<td>&nbsp; </td>
					</tr>
				</tfoot>
			</table>
		</div>

		<?

		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}

		//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);

		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />


		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});
		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}

/*if($action=="total_receive_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,$unicode,'','');

	extract($_REQUEST);
	?>

	<fieldset style="width:1720px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}
		</script>
		<?
		ob_start();
		?>
		<div style="width:870px;" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="1720" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="18">Receive Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="70">Receive Date</th>
						<th width="80">Dyeing Source</th>
						<th width="110">Receive Company</th>
						<th width="100">Challan No</th>

						<th width="80">Style</th>
						<th width="80">Po No</th>
						<th width="80">Buyer</th>

						<th width="80">Color</th>
						<th width="80">Batch No</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>

						<th width="80">Fin. Rcv. Qty.</th>
						<th width="70">Process Loss.</th>
						<th width="60">QC ID</th>
						<th>QC Name</th>
					</thead>
					<tbody>
						<?
						$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
						$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
						$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
						$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
						$po_number_no_arr=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number");

						$grey_sql="select a.id,b.prod_id, b.used_qty,b.dtls_id from inv_receive_master a,pro_material_used_dtls b where a.id=b.mst_id  and b.item_category=13 and b.entry_form=37";
						$dtlsgrey=sql_select($grey_sql);
						$grey_used_arr=array();
						foreach($dtlsgrey as $row)
						{
							$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty']+=$row[csf('used_qty')];
						}
					//print_r($grey_used_arr);
						$i=1;

						$mrr_sql="select a.id,a.recv_number, a.booking_no,a.receive_date,a.knitting_source,a.knitting_company,a.challan_no,a.emp_id,a.qc_name,b.rack_no as rack_no, b.prod_id,b.batch_id,b.body_part_id,b.gsm,b.width,b.order_id,b.buyer_id,c.dtls_id, sum(c.quantity) as quantity, sum(c.returnable_qnty) as returnable_qnty,c.color_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d
						where a.id=b.mst_id and b.id=c.dtls_id and b.batch_id = d.id and a.entry_form in (7,37,66,68) and c.entry_form in (7,37,66,68)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and d.status_active = 1 and d.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.prod_id in ( $prod_id ) and c.color_id='$color' and c.trans_id!=0 and c.trans_type=1 group by a.id,a.recv_number, a.receive_date,a.booking_no, a.emp_id,b.rack_no,b.prod_id,b.body_part_id,c.dtls_id,c.color_id,a.knitting_source,a.knitting_company,a.challan_no,a.qc_name,b.batch_id,b.gsm,b.width,b.order_id,b.buyer_id";

					//echo $mrr_sql;

						$dtlsArray=sql_select($mrr_sql);
						$tot_grey_qty=0;
						foreach($dtlsArray as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$tot_reject=$row[csf('returnable_qnty')];
							if($row[csf('knitting_source')]==1)
							{
								$knitting_company=$company_arr[$row[csf('knitting_company')]];
							}
							else
							{
								$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
							}
							$grey_used_qty=$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty'];
							$process_loss=100-($row[csf('quantity')]/$grey_used_qty)*100;
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30"><p><? echo $i; ?></p></td>
								<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
								<td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
								<td width="80"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
								<td width="110"><p><? echo $knitting_company; ?></p></td>
								<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>

								<td width="80"><p><? echo $style_ref_no; ?></p></td>
								<td width="80"><p><? echo $po_number_no_arr[$row[csf('order_id')]]; ?></p></td>
								<td width="80"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>

								<td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
								<td width="80"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
								<td width="200" ><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
								<td width="50" align="center"><p><? echo $row[csf('gsm')]; ?>&nbsp;</p></td>
								<td width="50" align="center"><p><? echo $row[csf('width')]; ?></p></td>

								<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
								<td width="70" title="Fin Recv Qty/Grey Qty*100" align="right"><p><? echo number_format($process_loss,2); ?></p></td>
								<td width="60" align="center"><p><? echo $row[csf('emp_id')]; ?></p></td>
								<td align="center"><p><? echo $row[csf('qc_name')]; ?></p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('quantity')];
						//$tot_grey_qty+=$grey_used_qty;
						//$tot_reject_qty+=$row[csf('returnable_qnty')];
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="14" align="right">Total</td>
							<td align="right"><? echo number_format($tot_qty,2); ?> </td>
							<td colspan="5"> </td>

						</tr>
					</tfoot>
				</table>

			</table>
		</div>

		<?

		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}

			//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);

		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />


		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});
		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}*/
if($action=="total_receive_popup_show6")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);

	/*po_id
	prod_id
	job_no
	color
	type
	action
	style_ref_no 
	internalref
	buyer
	batchId
	determination
	itemdesc
	gsm
	dia
	*/
	//list($buyer_id,$job_no_pre,$job_year,$style_ref_no,$grouping,$fabric_type) = explode("_", $style_ref_no);

	$mrr_sql="SELECT a.id,a.recv_number, a.receive_date,a.knitting_source,a.knitting_company,a.location_id, b.prod_id, b.batch_id,b.body_part_id, b.gsm,b.width, e.store_id, sum(c.quantity) as quantity, d.color_id,b.remarks,a.challan_no
	from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d, inv_transaction e 
	where a.id=b.mst_id and b.id=c.dtls_id and b.batch_id = d.id and c.trans_id = e.id and a.entry_form in (37) and c.entry_form in (37)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and d.status_active = 1 and d.is_deleted=0 and a.company_id='$companyID'  and c.color_id='$color' and c.prod_id in ( $prod_id )   and c.trans_id!=0 and c.trans_type=1 and d.id=$batchId
	group by a.id, a.recv_number,a.knitting_source,a.knitting_company,a.location_id, a.receive_date, b.prod_id, b.body_part_id, d.color_id, b.batch_id,b.gsm, b.width, e.store_id,b.remarks,a.challan_no";
	//and c.prod_id in ( $prod_id )  and c.po_breakdown_id in($po_id)
	//and d.id=$batchId and b.width='$dia'  and b.gsm='$gsm'

	$dtlsArray=sql_select($mrr_sql);
	foreach($dtlsArray as $row)
	{
		$product_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
		$color_id_arr[$row[csf('color_id')]] = $row[csf('color_id')];
		$knitting_company_arr[$row[csf('knitting_company')]] = $row[csf('knitting_company')];
		$batch_id_arr[$row[csf('batch_id')]] = $row[csf('batch_id')];
	}

	if(!empty($product_id_arr))
		$product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$product_id_arr).")", "id", "product_name_details");

	if(!empty($knitting_company_arr))
		$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier where id in(".implode(",",$knitting_company_arr).")", "id", "supplier_name");

	if(!empty($batch_id_arr))
		$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")", "id", "batch_no");
	if(!empty($color_id_arr))
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".implode(",",$color_id_arr).")", "id", "color_name"  );
	?>
	<fieldset style="width:1000px; margin:0 auto;">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}
		</script>
		<?
		$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$store_name_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");

		$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

		ob_start();
		?>
		<div style="width:870px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center" style="padding: 10px 0;">
				<thead>
					<tr>
						<th colspan="11">Receive Details</th>
					</tr>
					<tr>
						<th width="110">Buyer</th>
						<th width="100">Job	No</th>
						<th width="110">Style</th>
						<th width="110">Int. Ref.</th>
						<th width="110">Finish Fab.Color</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><? echo $buyer_arr[$buyer];?></td>
						<td><? echo $job_no;?></td>
						<td><? echo $style_ref_no;?></td>
						<td><? echo $internalref;?></td>
						<td><? echo $color_arr[$color];?></td>
					</tr>
				</tbody>
			</table>
			<br>

			<table border="1" class="rpt_table" rules="all" width="1380" cellpadding="0" cellspacing="0" align="left">
				<thead>
					<tr>
						<th width="30">Sl</th>
						<th width="80">Product ID</th>
						<th width="110">Transection ID</th>
						<th width="70">Transection Date</th>
						<th width="70">Challan</th>
						<th width="80">Batch No</th>
						<th width="110">Service Company</th>
						<th width="110">Service Location</th>
						<th width="80">Batch Color</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="80">Store Name</th>
						<th width="">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$tot_grey_qty=0;$i=1;
					foreach($dtlsArray as $row)
					{
						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><p><? echo $i; ?></p></td>
							<td><p><? echo $row[csf('prod_id')]; ?></p></td>
							<td><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td><p><? echo $knitting_company; ?></p></td>
							<td><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
							<td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td align="center"><p><? echo $row[csf('gsm')]; ?></p></td>
							<td align="center"><p><? echo $row[csf('width')]; ?></p></td>
							<td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td align="right"><p><? echo $store_name_arr[$row[csf('store_id')]]; ?></p></td>
							<td align="right" style="word-wrap: break-word; word-break: break-all;"><p><? echo $row[csf('remarks')]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="12" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?> </td>
						<td colspan="2"></td>
					</tr>
				</tfoot>
			</table>
		</table>
	</div>
	<?

	$html=ob_get_contents();
	ob_flush();

	foreach (glob(""."*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');
	$is_created = fwrite($create_new_excel,$html);
	?>
	<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />

	<script>
		$(document).ready(function(e) {
			document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});
	</script>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}
if($action=="total_issue_rtn_popup_show6")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);

	//list($buyer_id,$job_no_pre,$job_year,$style_ref_no,$grouping,$fabric_type) = explode("_", $style_ref_no);

	$mrr_sql="SELECT a.id,a.recv_number, a.receive_date,a.knitting_source,a.knitting_company,a.location_id, b.prod_id, b.batch_id,b.body_part_id, b.gsm,b.width, e.store_id, sum(c.quantity) as quantity, d.color_id,b.remarks,a.challan_no
	from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d, inv_transaction e 
	where a.id=b.mst_id and b.trans_id=c.trans_id and b.batch_id = d.id and c.trans_id = e.id and a.entry_form in (52) and c.entry_form in (52)  and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and d.status_active = 1 and d.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID'  and c.color_id='$color' and c.prod_id in ( $prod_id )   and c.trans_id!=0 and c.trans_type=4
	group by a.id, a.recv_number,a.knitting_source,a.knitting_company,a.location_id, a.receive_date, b.prod_id, b.body_part_id, d.color_id, b.batch_id,b.gsm, b.width, e.store_id,b.remarks,a.challan_no";
	//and c.prod_id in ( $prod_id )
	//and d.id=$batchId and b.width='$dia'  and b.gsm='$gsm'

	$dtlsArray=sql_select($mrr_sql);
	foreach($dtlsArray as $row)
	{
		$product_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
		$color_id_arr[$row[csf('color_id')]] = $row[csf('color_id')];
		$knitting_company_arr[$row[csf('knitting_company')]] = $row[csf('knitting_company')];
		$batch_id_arr[$row[csf('batch_id')]] = $row[csf('batch_id')];
	}

	if(!empty($product_id_arr))
		$product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$product_id_arr).")", "id", "product_name_details");

	if(!empty($knitting_company_arr))
		$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier where id in(".implode(",",$knitting_company_arr).")", "id", "supplier_name");

	if(!empty($batch_id_arr))
		$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")", "id", "batch_no");
	if(!empty($color_id_arr))
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".implode(",",$color_id_arr).")", "id", "color_name"  );
	?>
	<fieldset style="width:1000px; margin:0 auto;">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}
		</script>
		<?
		$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$store_name_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");

		$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

		ob_start();
		?>
		<div style="width:870px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center" style="padding: 10px 0;">
				<thead>
					<tr>
						<th colspan="11">Issue Return Details</th>
					</tr>
					<tr>
						<th width="110">Buyer</th>
						<th width="100">Job	No</th>
						<th width="110">Style</th>
						<th width="110">Int. Ref.</th>
						<th width="110">Finish Fab.Color</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><? echo $buyer_arr[$buyer];?></td>
						<td><? echo $job_no;?></td>
						<td><? echo $style_ref_no;?></td>
						<td><? echo $internalref;?></td>
						<td><? echo $color_arr[$color];?></td>
					</tr>
				</tbody>
			</table>
			<br>

			<table border="1" class="rpt_table" rules="all" width="1080" cellpadding="0" cellspacing="0" align="left">
				<thead>
					<tr>
						<th width="30">Sl</th>
						<th width="80">Product ID</th>
						<th width="110">Transection ID</th>
						<th width="70">Transection Date</th>
						<th width="80">Batch No</th>
						<th width="80">Batch Color</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th width="80">Fin. Issue Return Qty.</th>
						<th width="80">Store Name</th>
						<th width="">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$tot_grey_qty=0;$i=1;
					foreach($dtlsArray as $row)
					{
						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><p><? echo $i; ?></p></td>
							<td><p><? echo $row[csf('prod_id')]; ?></p></td>
							<td><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td align="center"><p><? echo $row[csf('gsm')]; ?></p></td>
							<td align="center"><p><? echo $row[csf('width')]; ?></p></td>
							<td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td align="right"><p><? echo $store_name_arr[$row[csf('store_id')]]; ?></p></td>
							<td align="right" style="word-wrap: break-word; word-break: break-all;"><p><? echo $row[csf('remarks')]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?> </td>
						<td colspan="2"></td>
					</tr>
				</tfoot>
			</table>
		</table>
	</div>
	<?

	$html=ob_get_contents();
	ob_flush();

	foreach (glob(""."*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');
	$is_created = fwrite($create_new_excel,$html);
	?>
	<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />

	<script>
		$(document).ready(function(e) {
			document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});
	</script>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}
if($action=="total_trans_in_popup_show6")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);

	//list($buyer_id,$job_no_pre,$job_year,$style_ref_no,$grouping,$fabric_type) = explode("_", $style_ref_no);

	$mrr_sql="select a.id,a.transfer_system_id as recv_number, a.transfer_date as receive_date, b.to_prod_id as prod_id, b.to_batch_id as batch_id,b.body_part_id, e.gsm,e.dia_width as width, b.to_store as store_id, sum(b.transfer_qnty) as quantity, b.color_id,b.remarks from inv_item_transfer_mst a,inv_item_transfer_dtls b,inv_transaction c, order_wise_pro_details d,product_details_master e where a.id=b.mst_id and b.to_trans_id=c.id and c.id=d.trans_id and b.to_prod_id=e.id and  d.entry_form in(14) and a.entry_form in(14) and c.item_category=2 and c.transaction_type=5 and d.trans_type=5 and a.to_company=$companyID and b.color_id='$color' and b.to_batch_id= $batchId and b.to_order_id in($po_id) and b.to_prod_id in ( $prod_id ) group by a.id,a.transfer_system_id, a.transfer_date, b.to_prod_id, b.to_batch_id,b.body_part_id, e.gsm,e.dia_width, b.to_store, b.color_id,b.remarks";

	
		/*$finishing_trans_in_qty_array=array();
		foreach($data_array_finish_qnty_transfer_in as $row)
		{
			$finishing_trans_in_qty_array[$row[csf("color_id")]][$row[csf("batch_id")]][$constructtion_arr[$row[csf("fabric_description_id")]]]+=$row[csf("trans_in_qnty")];
				$prodIdArrs[$row[csf("color_id")]][$row[csf("batch_id")]][$constructtion_arr[$row[csf("fabric_description_id")]]]['prod_id']=$row[csf("prod_id")];
		}*/
		

	//and c.prod_id in ( $prod_id )
	//and d.id=$batchId and b.width='$dia'  and b.gsm='$gsm'

	$dtlsArray=sql_select($mrr_sql);
	foreach($dtlsArray as $row)
	{
		$product_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
		$color_id_arr[$row[csf('color_id')]] = $row[csf('color_id')];
		$knitting_company_arr[$row[csf('knitting_company')]] = $row[csf('knitting_company')];
		$batch_id_arr[$row[csf('batch_id')]] = $row[csf('batch_id')];
	}

	if(!empty($product_id_arr))
		$product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$product_id_arr).")", "id", "product_name_details");

	if(!empty($knitting_company_arr))
		$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier where id in(".implode(",",$knitting_company_arr).")", "id", "supplier_name");

	if(!empty($batch_id_arr))
		$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")", "id", "batch_no");
	if(!empty($color_id_arr))
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".implode(",",$color_id_arr).")", "id", "color_name"  );
	?>
	<fieldset style="width:1000px; margin:0 auto;">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}
		</script>
		<?
		$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$store_name_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");

		$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

		ob_start();
		?>
		<div style="width:870px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center" style="padding: 10px 0;">
				<thead>
					<tr>
						<th colspan="11">Transfer In Details</th>
					</tr>
					<tr>
						<th width="110">Buyer</th>
						<th width="100">Job	No</th>
						<th width="110">Style</th>
						<th width="110">Int. Ref.</th>
						<th width="110">Finish Fab.Color</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><? echo $buyer_arr[$buyer];?></td>
						<td><? echo $job_no;?></td>
						<td><? echo $style_ref_no;?></td>
						<td><? echo $internalref;?></td>
						<td><? echo $color_arr[$color];?></td>
					</tr>
				</tbody>
			</table>
			<br>

			<table border="1" class="rpt_table" rules="all" width="1380" cellpadding="0" cellspacing="0" align="left">
				<thead>
					<tr>
						<th width="30">Sl</th>
						<th width="80">Product ID</th>
						<th width="110">Transection ID</th>
						<th width="70">Transection Date</th>
						<th width="80">Batch No</th>
						<th width="80">Batch Color</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th width="80">Fin. Transfer In Qty.</th>
						<th width="80">Store Name</th>
						<th width="">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$tot_grey_qty=0;$i=1;
					foreach($dtlsArray as $row)
					{
						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><p><? echo $i; ?></p></td>
							<td><p><? echo $row[csf('prod_id')]; ?></p></td>
							<td><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td align="center"><p><? echo $row[csf('gsm')]; ?></p></td>
							<td align="center"><p><? echo $row[csf('width')]; ?></p></td>
							<td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td align="right"><p><? echo $store_name_arr[$row[csf('store_id')]]; ?></p></td>
							<td align="right" style="word-wrap: break-word; word-break: break-all;"><p><? echo $row[csf('remarks')]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?> </td>
						<td colspan="2"></td>
					</tr>
				</tfoot>
			</table>
		</table>
	</div>
	<?

	$html=ob_get_contents();
	ob_flush();

	foreach (glob(""."*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');
	$is_created = fwrite($create_new_excel,$html);
	?>
	<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />

	<script>
		$(document).ready(function(e) {
			document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});
	</script>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}
if($action=="total_issue_popup_show6")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$cutting_floor_library=return_library_array( "select id, floor_name from lib_prod_floor where production_process=1 ", "id", "floor_name"  );
	//list($buyer_id,$job_no_pre,$job_year,$style_ref_no,$grouping,$fabric_type) = explode("_", $style_ref_no);

	/*$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
	$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
	//$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	//$po_number_no_arr=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$color_arr=return_library_array( "select id,color_name from lib_color where id in($color)", "id", "color_name"  );*/



	/*$grey_sql="select a.id,b.prod_id, b.used_qty,b.dtls_id from inv_receive_master a,pro_material_used_dtls b where a.id=b.mst_id  and b.item_category=13 and b.entry_form=37";
	$dtlsgrey=sql_select($grey_sql);
	$grey_used_arr=array();
	foreach($dtlsgrey as $row)
	{
		$grey_used_arr[$row[csf('id')]][$row[csf('dtls_id')]]['grey_qty']+=$row[csf('used_qty')];
	}*/
	?>
	<fieldset style="width:1000px; margin:0 auto;">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}
		</script>
		<?
		ob_start();
		$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$store_name_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");
		$color_arr=return_library_array( "select id,color_name from lib_color where id in($color)", "id", "color_name"  );
		$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
		?>
		<div style="width:1000px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center" style="padding: 10px 0;">
				<thead>
					<tr>
						<th colspan="11">Issue Details</th>
					</tr>
					<tr>
						<th width="110">Buyer</th>
						<th width="100">Job	No</th>
						<th width="110">Style</th>
						<th width="110">Int. Ref.</th>
						<th width="110">Finish Fab.Color</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><? echo $buyer_arr[$buyer];?></td>
						<td><? echo $job_no;?></td>
						<td><? echo $style_ref_no;?></td>
						<td><? echo $internalref;?></td>
						<td><? echo $color_arr[$color];?></td>
					</tr>
				</tbody>
			</table>
			<br>
			<table border="1" class="rpt_table" rules="all" width="1400" cellpadding="0" cellspacing="0" align="center" style="padding: 10px 0;">
				<thead>
					<tr>
						<th colspan="17">Issue Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="80">Product ID</th>
						<th width="110">Transection ID</th>
						<th width="70">Transection Date</th>
						<th width="70">Challan No</th>
						<th width="80">Batch No</th>
						<th width="110">Service Company</th>
						<th width="110">Service Location</th>
						<th width="80">Batch Color</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="80">Store Name</th>
						<th width="">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					$mrr_sql="SELECT a.id,a.company_id, a.issue_number, a.issue_date,a.challan_no,a.knit_dye_source,a.knit_dye_company,a.location_id, b.prod_id,b.batch_id,b.rack_no, b.cutting_unit, c.quantity as quantity,c.color_id,c.po_breakdown_id,a.knit_dye_source,a.knit_dye_company,b.store_id, b.remarks,a.challan_no
					from  inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(18,71)  and c.entry_form in(18,71) and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.company_id='$companyID'  and c.prod_id in( $prod_id ) and c.color_id='$color' and b.batch_id=$batchId"; // and c.po_breakdown_id in ($po_id)
					//and a.issue_date <='$from_date'
					//echo $mrr_sql;
					$dtlsArray=sql_select($mrr_sql);
					foreach($dtlsArray as $row)
					{
						$product_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
						$color_id_arr[$row[csf('color_id')]] = $row[csf('color_id')];
						$batch_id_arr[$row[csf('batch_id')]] = $row[csf('batch_id')];
						$order_id_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
					}

					if(!empty($product_id_arr))
						$product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$product_id_arr).")", "id", "product_name_details");

					if(!empty($batch_id_arr))
					{
						$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")", "id", "batch_no");
					}

					if(!empty($batch_id_arr))
					{
						$batch_id_cond = " and b.batch_id in(".implode(",",$batch_id_arr).")";
					}

					$sql_issue="SELECT a.knitting_source,a.knitting_company,b.batch_id,b.gsm,b.width,b.buyer_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form in (7,37,66,68) $batch_id_cond";
					$result_issue=sql_select($sql_issue);
					$issue_arr=array();
					foreach($result_issue as $row)
					{
						$issue_arr[$row[csf('batch_id')]]['rack']=$row[csf('rack')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_source']=$row[csf('knitting_source')];
						$issue_arr[$row[csf('batch_id')]]['knit_dye_company']=$row[csf('knitting_company')];
						$issue_arr[$row[csf('batch_id')]]['gsm']=$row[csf('gsm')];
						$issue_arr[$row[csf('batch_id')]]['width']=$row[csf('width')];
						$issue_arr[$row[csf('batch_id')]]['buyer_id']=$row[csf('buyer_id')];
					}

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$rack=$issue_arr[$row[csf('batch_id')]]['rack'];
						$knit_dye_source=$row[csf('knit_dye_source')];
						$knit_dye_company=$row[csf('knit_dye_company')];
						$gsm=$issue_arr[$row[csf('batch_id')]]['gsm'];
						$width=$issue_arr[$row[csf('batch_id')]]['width'];

						if($knit_dye_source==1)
						{
							$knitting_company=$company_arr[$knit_dye_company];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knit_dye_company')]];
						}
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><p><? echo $i; ?></p></td>
							<td><p><? echo $row[csf('prod_id')]; ?></p></td>
							<td><p><? echo $row[csf('issue_number')]; ?></p></td>
							<td><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
							<td><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td align="center"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td align="center"><p><? echo $knitting_company; ?></p></td>
							<td align="center"><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
							<td align="center"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td align="left"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td align="center"><p><? echo $gsm; ?></p></td>
							<td align="center"><p><? echo $width; ?></p></td>
							<td align="right"><p><? echo $row[csf('quantity')]; ?></p></td>
							<td align="left"><p><? echo $store_name_arr[$row[csf('store_id')]]; ?></p></td>
							<td align="center"><p><? echo $row[csf('remarks')]; ?></p></td>
						</tr>
						<?
						$tot_issue_return_qty+=$row[csf('quantity')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="12" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_issue_return_qty,2); ?>&nbsp;</td>
						<td colspan="2"></td>
					</tr>
				</tfoot>
			</table>
		</table>
		<?
		$html=ob_get_contents();
		ob_flush();

		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}
		//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);
		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});

		</script>
		<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</div>
	</fieldset>
	<?
	exit();
} 
if($action=="total_trans_out_popup_show6")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);

	//list($buyer_id,$job_no_pre,$job_year,$style_ref_no,$grouping,$fabric_type) = explode("_", $style_ref_no);

	$poSql=sql_select("select b.id,b.grouping,c.mst_id from wo_po_details_master a,wo_po_break_down b,pro_batch_create_dtls c where a.id=b.job_id and b.id=c.po_id and b.id in($po_id) and c.mst_id=$batchId and b.grouping='$internalref' group by  b.id,b.grouping,c.mst_id");
	foreach($poSql as $row)
	{
		$po_info_arr[$row[csf("mst_id")]]['grouping']=$row[csf("grouping")];
	}

	$mrr_sql="select a.id,a.transfer_system_id as recv_number, a.transfer_date as receive_date, b.from_prod_id as prod_id, b.batch_id,b.body_part_id, e.gsm,e.dia_width as width, b.from_store as store_id, sum(b.transfer_qnty) as quantity, b.color_id,b.remarks from inv_item_transfer_mst a,inv_item_transfer_dtls b,inv_transaction c, order_wise_pro_details d,product_details_master e where a.id=b.mst_id and b.trans_id=c.id and c.id=d.trans_id  and b.from_prod_id=e.id and  d.entry_form in(14) and a.entry_form in(14) and c.item_category=2 and c.transaction_type=6 and d.trans_type=6 and a.company_id='$companyID' and b.color_id='$color' and b.from_order_id in($po_id) and b.from_prod_id in ( $prod_id ) group by a.id,a.transfer_system_id, a.transfer_date, b.from_prod_id, b.batch_id,b.body_part_id, e.gsm,e.dia_width, b.from_store, b.color_id,b.remarks";
		/*$finishing_trans_out_qty_array=array();
		
		foreach($data_array_finish_qnty_transfer_out as $row)
		{
			$finishing_trans_out_qty_array[$row[csf("color_id")]][$row[csf("batch_id")]][$constructtion_arr[$row[csf("fabric_description_id")]]]+=$row[csf("trans_out_qnty")];
				$prodIdArrs[$row[csf("color_id")]][$row[csf("batch_id")]][$constructtion_arr[$row[csf("fabric_description_id")]]]['prod_id']=$row[csf("prod_id")];
		}*/


	//and c.prod_id in ( $prod_id )
	//and d.id=$batchId and b.width='$dia'  and b.gsm='$gsm'

	$dtlsArray=sql_select($mrr_sql);
	foreach($dtlsArray as $row)
	{
		$product_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
		$color_id_arr[$row[csf('color_id')]] = $row[csf('color_id')];
		$knitting_company_arr[$row[csf('knitting_company')]] = $row[csf('knitting_company')];
		$batch_id_arr[$row[csf('batch_id')]] = $row[csf('batch_id')];
	}

	if(!empty($product_id_arr))
		$product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$product_id_arr).")", "id", "product_name_details");

	if(!empty($knitting_company_arr))
		$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier where id in(".implode(",",$knitting_company_arr).")", "id", "supplier_name");

	if(!empty($batch_id_arr))
		$batch_no_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst where id in(".implode(",",$batch_id_arr).")", "id", "batch_no");
	if(!empty($color_id_arr))
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".implode(",",$color_id_arr).")", "id", "color_name"  );
	?>
	<fieldset style="width:1000px; margin:0 auto;">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}
		</script>
		<?
		$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$store_name_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");

		$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

		ob_start();
		?>
		<div style="width:870px;padding: 10px 0;" align="center">
			<table border="0" class="" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
		</div>
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center" style="padding: 10px 0;">
				<thead>
					<tr>
						<th colspan="11">Transfer Out Details</th>
					</tr>
					<tr>
						<th width="110">Buyer</th>
						<th width="100">Job	No</th>
						<th width="110">Style</th>
						<th width="110">Int. Ref.</th>
						<th width="110">Finish Fab.Color</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><? echo $buyer_arr[$buyer];?></td>
						<td><? echo $job_no;?></td>
						<td><? echo $style_ref_no;?></td>
						<td><? echo $internalref;?></td>
						<td><? echo $color_arr[$color];?></td>
					</tr>
				</tbody>
			</table>
			<br>

			<table border="1" class="rpt_table" rules="all" width="1180" cellpadding="0" cellspacing="0" align="left">
				<thead>
					<tr>
						<th width="30">Sl</th>
						<th width="80">Product ID</th>
						<th width="110">Transection ID</th>
						<th width="70">Transection Date</th>
						<th width="100">From Int. Ref.</th>
						<th width="80">Batch No</th>
						<th width="80">Batch Color</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th width="80">Fin. Transfer Out Qty.</th>
						<th width="80">Store Name</th>
						<th width="">Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$tot_grey_qty=0;$i=1;
					foreach($dtlsArray as $row)
					{
						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td><p><? echo $i; ?></p></td>
							<td><p><? echo $row[csf('prod_id')]; ?></p></td>
							<td><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td><p><? echo $po_info_arr[$row[csf('batch_id')]]['grouping']; ?></p></td> 
							<td><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
							<td align="center"><p><? echo $row[csf('gsm')]; ?></p></td>
							<td align="center"><p><? echo $row[csf('width')]; ?></p></td>
							<td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td align="right"><p><? echo $store_name_arr[$row[csf('store_id')]]; ?></p></td>
							<td align="right" style="word-wrap: break-word; word-break: break-all;"><p><? echo $row[csf('remarks')]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="10" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?> </td>
						<td colspan="3"></td>
					</tr>
				</tfoot>
			</table>
		</table>
	</div>
	<?

	$html=ob_get_contents();
	ob_flush();

	foreach (glob(""."*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');
	$is_created = fwrite($create_new_excel,$html);
	?>
	<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />

	<script>
		$(document).ready(function(e) {
			document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});
	</script>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
	exit();
}


?>
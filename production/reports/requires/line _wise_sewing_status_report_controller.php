<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 140, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' 
	order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/daily_production_status_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 ); 
	//get_php_form_data( this.value, 'eval_multi_select', 'requires/daily_production_status_controller' );
	exit();    	 
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 120, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=5 
	and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );     	 	
	exit();    	 
}

if($action=="line_search_popup")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;
		
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ )
			 {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
			 }
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
			//alert(strCon)
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
			
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );					
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 ); 
			}
			var id = ''; var name = ''; var job = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ','; 
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 ); 
			
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name ); 
		}
		
		function fn_onClosed()
		{
			parent.emailwindow.hide();
		}
    </script>
	<?
	extract($_REQUEST);
	//echo $company;die;
	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name");
	if($company==0) $company_name=""; else $company_name=" and b.company_name=$company";//job_no
	
	if($buyer==0) $buyer_name=""; else $buyer_name="and b.buyer_name=$buyer";
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company and variable_list=23 and is_deleted=0 and status_active=1");
	$cond ="";
    if($prod_reso_allo==1)
	{
		$line_array=array();
		if($txt_date=="")
		{
			$data_format="";
		}
		else
		{
			if($db_type==0)	$data_format="and b.pr_date='".change_date_format($txt_date,'yyyy-mm-dd')."'";
			if($db_type==2)	$data_format="and b.pr_date='".change_date_format($txt_date,'','',1)."'";
		}
		if( $location!=0 ) $cond .= " and a.location_id= $location";
		if( $floor_id!=0 ) $cond.= " and a.floor_id= $floor_id";
		
		$line_sql="select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id $data_format and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number";
		$line_sql_result=sql_select($line_sql);
		
		?>
            <input type='hidden' id='txt_selected_id' />
            <input type='hidden' id='txt_selected' />
            <table cellspacing="0" width="250"  border="1" rules="all" class="rpt_table" >
            	<thead>
                	<th width="26">#</th>
                    <th width="200">Line Name</th>
                </thead>
            </table>
            <div style="width:250px; max-height:300px; overflow-y:scroll" id="scroll_body" >          
        		<table cellspacing="0" width="230"  border="1" rules="all" class="rpt_table" id="list_view" >
                <? $i=1;
				 foreach($line_sql_result as $row)
				 {
        			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
        
					$line_val='';
					$line_id=explode(",",$row[csf('line_number')]);
					foreach($line_id as $line_id)
					{
						if($line_val=="") $line_val=$line_library[$line_id]; else $line_val.=','.$line_library[$line_id];
					}
					?>
                	<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>" onClick="js_set_value('<? echo $i.'_'.$row[csf('id')].'_'.$line_val; ?>')" style="cursor:pointer;">
                    	<td  width="30"><? echo $i;?></td>
                        <td  width="200"><? echo $line_val;?></td>
                    </tr>
                 <?
				 $i++;
				 }
				 ?>
              </table>
           </div>
        <table width="250">
            <tr align="center">
                <td><input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fn_onClosed()" /></td>
            </tr>
        </table>
        <?
	}
	else
	{
		if( $location!=0  ) $cond = " and location_name= $location";
		if( $floor_id!=0 ) $cond.= " and floor_name= $floor_id";
		$line_data="select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name";
		echo create_list_view("list_view", "Line No","250","300","310",0, $line_data , "js_set_value", "id,line_name", "", 1, "0", $arr, "line_name", 
		"","setFilterGrid('list_view',-1)","0","",1) ;	
		echo "<input type='hidden' id='txt_selected_id' />";
		echo "<input type='hidden' id='txt_selected' />";
	}
	exit();
}

if($action=="report_generate") 
{
 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if(str_replace("'","",$cbo_company_id)==0) $company_cond=""; else $company_cond="and b.serving_company in(".str_replace("'","",$cbo_company_id).")";
	if(str_replace("'","",$cbo_company_id)==0) $company_cond_sub=""; else $company_cond_sub="and a.company_id in(".str_replace("'","",$cbo_company_id).")";
	if(str_replace("'","",$cbo_location_id)==0) $location_cond=""; else $location_cond="and b.location=".str_replace("'","",$cbo_location_id)."";
	if(str_replace("'","",$cbo_location_id)==0) $location_cond_sub=""; else $location_cond_sub="and a.location_id=".str_replace("'","",$cbo_location_id)."";
	if(str_replace("'","",$cbo_floor_id)==0) $floor_cond=""; else $floor_cond="and b.floor_id=".str_replace("'","",$cbo_floor_id)."";
	if(str_replace("'","",$cbo_floor_id)==0) $floor_cond_sub=""; else $floor_cond_sub="and a.floor_id=".str_replace("'","",$cbo_floor_id)."";
	if(str_replace("'","",$hidden_line_id)==0) $line_cond=""; else $line_cond="and b.sewing_line in(".str_replace("'","",$hidden_line_id).")";
	if(str_replace("'","",$hidden_line_id)==0) $line_cond_sub=""; else $line_cond_sub="and a.line_id in(".str_replace("'","",$hidden_line_id).")";
	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name"); 
	$countryArr = return_library_array("select id,country_name from lib_country","id","country_name"); 
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name"); 
	$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name"); 
	$locationArr = return_library_array("select id,location_name from lib_location","id","location_name"); 
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
	$lineArr = return_library_array("select a.id,a.line_name from lib_sewing_line a","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$buyer_id=str_replace("'", "", $cbo_buyer_id);
	$cbo_search_type=str_replace("'", "", $cbo_search_type);
	$txt_search_val=str_replace("'", "", $txt_search_val);
	$extra_cond="";
	if($buyer_id) $extra_cond.=" and e.buyer_name='$buyer_id' ";
	if($cbo_search_type && $txt_search_val)
	{
		if($cbo_search_type==1)
		{
			$extra_cond.=" and e.job_no_prefix_num='$txt_search_val' ";
		}
		else if($cbo_search_type==2)
		{
			$extra_cond.=" and e.style_ref_no like '%$txt_search_val%' ";
		}
		if($cbo_search_type==3)
		{
			$extra_cond.=" and d.po_number like '%$txt_search_val%' ";
		}
		
	}
	 
	
	$comapny_id=str_replace("'","",$cbo_company_id);
    $today_date=date("Y-m-d");
	$txt_producting_day="".str_replace("'","",$txt_date)."";
	//echo $txt_producting_day;die;
	//***************************************************************************************************************************


	
	//print_r($color_size_qty_arr);die;
	$sql = "SELECT e.style_ref_no,e.buyer_name,d.po_number,  a.job_no_mst,a.country_ship_date, a.item_number_id, a.country_id, a.color_number_id, b.location, b.floor_id, b.sewing_line, b.po_break_down_id,b.production_type,
	sum(CASE WHEN c.production_type =4 and b.production_date = $txt_date THEN production_qnty else 0 END) AS sewing_input,
	sum(CASE WHEN c.production_type =5 and b.production_date = $txt_date THEN production_qnty else 0 END) AS sewing_output,
	sum(CASE WHEN c.production_type =11 and b.production_date = $txt_date THEN production_qnty else 0 END) AS poly,
	 
	sum(CASE WHEN c.production_type =5 and c.is_rescan =0  THEN c.reject_qty else 0 END) AS ttl_reject,
	sum(CASE WHEN c.production_type =5 and c.is_rescan = 1  THEN production_qnty else 0 END) AS resc_qcpass
	 from  wo_po_color_size_breakdown a, pro_garments_production_mst b,  pro_garments_production_dtls c,wo_po_break_down d,wo_po_details_master e  where a.id=c.color_size_break_down_id and b.id=c.mst_id and b.po_break_down_id=d.id and d.job_no_mst=e.job_no and e.status_active=1 and d.status_active in(1,2,3) and d.is_deleted=0 and d.shiping_status<>3 and b.production_type in(4,5,11)  and c.production_type in(4,5,11)  and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond $location_cond $floor_cond $line_cond $extra_cond group by  e.style_ref_no,e.buyer_name,d.po_number, a.job_no_mst, a.item_number_id, a.country_id, a.color_number_id,a.country_ship_date, b.location, b.floor_id, b.sewing_line, b.po_break_down_id,b.production_type
	 order by b.sewing_line";
	//echo $sql;
	$sql_result = sql_select($sql);
	
	if(empty($sql_result)){
		echo	"<h1>No Production Found.</h1>";die;
	}

	
	$production_data_arr=array();
	foreach($sql_result as $val)
	{
		$production_data_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['buyer_name']=$val[csf('buyer_name')];
		$production_data_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['style_ref_no']=$val[csf('style_ref_no')];
		$production_data_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['po_number']=$val[csf('po_number')];

		$production_data_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['job_no_mst']=$val[csf('job_no_mst')];
		$production_data_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['country_ship_date']=$val[csf('country_ship_date')];		
		$production_data_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['sewing_input']+=$val[csf('sewing_input')];
		$production_data_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['sewing_output']+=$val[csf('sewing_output')];
		$production_data_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['poly']+=$val[csf('poly')];
		$production_data_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['ttl_reject']+=($val[csf('ttl_reject')] -$val[csf('resc_qcpass')]);

		$all_po_id[$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];	

	}

	if($db_type==2 && count($all_po_id)>999)
	{
		$all_po_id_chunk=array_chunk($all_po_id,999) ;
		$all_po_id_cond="";
		foreach($all_po_id_chunk as $chunk_arr)
		{
			$ids=implode(",",$chunk_arr);
			if(!$all_po_id_cond) $all_po_id_cond.=" and (a.po_break_down_id in($ids) ";
			else $all_po_id_cond.=" or a.po_break_down_id in($ids) ";
			 	
		}

		$all_po_id_cond.=" )";			

	}
	else
	{ 	
		$all_po_ids=implode(",",$all_po_id);
		$all_po_id_cond=" and a.po_break_down_id in($all_po_ids)";  
	}


	$subcon_sql_prod="SELECT a.line_id as sewing_line ,a.gmts_item_id as item_number_id, e.color_id as color_number_id, a.location_id as location,a.floor_id,c.delivery_date as country_ship_date, b.subcon_job as job_no_mst,b.job_no_prefix_num , c.order_no as po_number,c.id as po_break_down_id,'00' as country_id, c.order_quantity as po_quantity  ,b.party_id,c.cust_style_ref,
	sum(CASE WHEN a.production_type =7 THEN d.prod_qnty else 0 END) AS sewing_input,
	sum(CASE WHEN a.production_type =2 THEN d.prod_qnty else 0 END) AS sewing_output,
	sum(CASE WHEN a.production_type =5 THEN d.prod_qnty else 0 END) AS poly  from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d, subcon_ord_mst b,subcon_ord_dtls c,subcon_ord_breakdown e where a.production_type in(2,5,7) and d.production_type in(2,5,7) and a.id=d.dtls_id and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and e.id=d.ord_color_size_id and e.order_id=c.id and a.order_id=e.order_id and a.production_date = $txt_date $company_cond_sub  $location_cond_sub $floor_cond_sub $line_cond_sub  group by a.line_id   ,a.gmts_item_id  , e.color_id  , a.location_id  ,a.floor_id,c.delivery_date  , b.subcon_job  ,b.job_no_prefix_num   ,c.order_no ,c.id , c.order_quantity ,b.party_id,c.cust_style_ref";
	$sql_result_sub=sql_select($subcon_sql_prod);
	foreach($sql_result_sub as $val)
	{

		$production_data_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['job_no_mst']=$val[csf('job_no_mst')];
		$production_data_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['po_number']=$val[csf('po_number')];
		$production_data_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['po_quantity']+=$val[csf('po_quantity')];
		$production_data_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['party_id']=$val[csf('party_id')];
		$production_data_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['cust_style_ref']=$val[csf('cust_style_ref')];
		$production_data_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['country_ship_date']=$val[csf('country_ship_date')];		
		$production_data_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['sewing_input']+=$val[csf('sewing_input')];
		$production_data_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['sewing_output']+=$val[csf('sewing_output')];
		$production_data_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['poly']+=$val[csf('poly')];
		$all_po_id_sub[$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];

	}

	if($db_type==2 && count($all_po_id_sub)>999)
	{
		$all_po_id_chunk=array_chunk($all_po_id_sub,999) ;
		$all_po_id_sub_cond="";
		foreach($all_po_id_chunk as $chunk_arr)
		{
			$ids=implode(",",$chunk_arr);
			if(!$all_po_id_sub_cond) $all_po_id_sub_cond.=" and (c.id in($ids) ";
			else $all_po_id_sub_cond.=" or c.id in($ids) ";
			 	
		}

		$all_po_id_sub_cond.=" )";			

	}
	else
	{ 	
		$all_po_ids=implode(",",$all_po_id_sub);
		$all_po_id_sub_cond=" and c.id in($all_po_ids)";  
	}


	/*$all_sewing_poly_po_ids=implode(",", $all_po_id);
	if(!$all_sewing_poly_po_ids) {$all_sewing_poly_po_ids=0;}
	$sql_for_only_cutting = "SELECT  a.job_no_mst,a.country_ship_date, a.item_number_id, a.country_id, a.color_number_id, b.location, b.floor_id, b.sewing_line, b.po_break_down_id,b.production_type,
	sum(CASE WHEN c.production_type =4 THEN production_qnty else 0 END) AS sewing_input,
	sum(CASE WHEN c.production_type =5 THEN production_qnty else 0 END) AS sewing_output,
	sum(CASE WHEN c.production_type =11 THEN production_qnty else 0 END) AS poly
	 from wo_po_color_size_breakdown a, pro_garments_production_mst b,  pro_garments_production_dtls c where a.id=c.color_size_break_down_id and b.id=c.mst_id and b.production_type =1  and c.production_type =1 and b.production_date = $txt_date and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond $location_cond $floor_cond $line_cond and b.po_break_down_id not in($all_sewing_poly_po_ids) group by a.job_no_mst, a.item_number_id, a.country_id, a.color_number_id,a.country_ship_date, b.location, b.floor_id, b.sewing_line, b.po_break_down_id,b.production_type
	 order by b.sewing_line";
	 if(count(sql_select($sql_for_only_cutting))>0)
	 {
	 	foreach(sql_select($sql_for_only_cutting) as $val)
	 	{
	 		$production_data_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['job_no_mst']=$val[csf('job_no_mst')];
	 		$production_data_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['country_ship_date']=$val[csf('country_ship_date')];		
	 		$production_data_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['sewing_input']+=$val[csf('sewing_input')];
	 		$production_data_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['sewing_output']+=$val[csf('sewing_output')];
	 		$production_data_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['poly']+=$val[csf('poly')];
	 		$all_po_id[$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];	

	 	}

	 }*/

	


	
		$sql_color_size = sql_select("SELECT  a.job_no_mst,a.country_ship_date,a.po_break_down_id, a.item_number_id, a.country_id, a.color_number_id, sum(a.order_quantity) as order_quantity,  sum(a.plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown a where    a.status_active in(1,2,3) $all_po_id_cond and a.is_deleted=0  group by a.po_break_down_id, a.job_no_mst, a.item_number_id, a.country_id, a.color_number_id,a.country_ship_date");
	foreach($sql_color_size as $val)
	{
		$color_size_qty_arr[$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['order_quantity']=$val[csf('order_quantity')];
		$color_size_qty_arr[$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['plan_cut_qnty']=$val[csf('plan_cut_qnty')];
	}
	$all_po_id_cond2=str_replace("a.po_break_down_id", "b.po_break_down_id", $all_po_id_cond)  ;
	$sql_total = "SELECT a.item_number_id, a.country_id, a.color_number_id, b.location, b.floor_id, b.sewing_line, b.po_break_down_id,
	sum(CASE WHEN c.production_type =4  THEN production_qnty else 0 END) AS sewing_input_previous,
	sum(CASE WHEN c.production_type =5  THEN production_qnty else 0 END) AS sewing_output_previous,
	sum(CASE WHEN c.production_type =11  THEN production_qnty else 0 END) AS poly_previous
	 from wo_po_color_size_breakdown a, pro_garments_production_mst b,  pro_garments_production_dtls c 
	 where a.id=c.color_size_break_down_id and b.id=c.mst_id and b.production_type in(4,5,11)  and c.production_type in(4,5,11)  $all_po_id_cond2 and b.production_date< $txt_date and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond $location_cond $floor_cond $line_cond  group by  a.item_number_id, a.country_id, a.color_number_id, b.location, b.floor_id, b.sewing_line, b.po_break_down_id order by b.sewing_line";
	// echo $sql_total;
	$sql_total_sub="SELECT a.line_id as sewing_line ,a.gmts_item_id as item_number_id, e.color_id as color_number_id, a.location_id as location,a.floor_id ,c.id as po_break_down_id,'00' as country_id,  
	sum(CASE WHEN a.production_type =7 THEN d.prod_qnty else 0 END) AS sewing_input_previous,
	sum(CASE WHEN a.production_type =2 THEN d.prod_qnty else 0 END) AS sewing_output_previous,
	sum(CASE WHEN a.production_type =5 THEN d.prod_qnty else 0 END) AS poly_previous  from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d, subcon_ord_mst b,subcon_ord_dtls c,subcon_ord_breakdown e where a.production_type in(2,5,7) and d.production_type in(2,5,7) and a.id=d.dtls_id and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and e.id=d.ord_color_size_id and e.order_id=c.id and a.order_id=e.order_id $all_po_id_sub_cond and a.production_date < $txt_date $company_cond_sub  $location_cond_sub $floor_cond_sub $line_cond_sub  group by a.line_id   ,a.gmts_item_id  , e.color_id  , a.location_id  ,a.floor_id ,c.id";

	$sql_result_total = sql_select($sql_total);
	$sql_result_total_sub = sql_select($sql_total_sub);
	$ppd_arr=array();
	foreach($sql_result_total as $val)
	{
		$ppd_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['sewing_input_previous']+=$val[csf('sewing_input_previous')];
		$ppd_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['sewing_output_previous']+=$val[csf('sewing_output_previous')];
		$ppd_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['poly_previous']+=$val[csf('poly_previous')];
		
	}


	foreach($sql_result_total_sub as $val)
	{
		$ppd_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['sewing_input_previous']+=$val[csf('sewing_input_previous')];
		$ppd_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['sewing_output_previous']+=$val[csf('sewing_output_previous')];
		$ppd_arr[$val[csf('location')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['poly_previous']+=$val[csf('poly_previous')];
		//$cutting_data_arr[$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['total_cutting']=$val[csf('total_cutting')];
		
	}
	//echo "<pre>";
	//print_r($ppd_arr);
	
		$sql_total_cut = "SELECT a.item_number_id, a.country_id, a.color_number_id, b.location, b.floor_id, b.sewing_line, b.po_break_down_id,
	sum(CASE WHEN c.production_type =1 THEN production_qnty else 0 END) AS total_cutting
	 from wo_po_color_size_breakdown a, pro_garments_production_mst b,  pro_garments_production_dtls c 
	 where a.id=c.color_size_break_down_id and b.id=c.mst_id and b.production_type in(1)  and c.production_type in(1) $all_po_id_cond2 and b.production_date<= $txt_date and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond   group by  a.item_number_id, a.country_id, a.color_number_id, b.location, b.floor_id, b.sewing_line, b.po_break_down_id order by b.sewing_line";
	// echo $sql_total_cut;
	$sql_result_total_cut = sql_select($sql_total_cut);
	foreach($sql_result_total_cut as $val)
	{
		$cutting_data_arr[$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['total_cutting']+=$val[csf('total_cutting')];
	}

	$sql_total_cut_sub="SELECT a.line_id as sewing_line ,a.gmts_item_id as item_number_id, e.color_id as color_number_id, a.location_id as location,a.floor_id ,c.id as po_break_down_id,'00' as country_id,  
	sum(CASE WHEN a.production_type =1 THEN d.prod_qnty else 0 END) AS total_cutting
	   from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d, subcon_ord_mst b,subcon_ord_dtls c,subcon_ord_breakdown e where a.production_type in(2,5,7) and d.production_type in(2,5,7) and a.id=d.dtls_id and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and e.id=d.ord_color_size_id and e.order_id=c.id and a.order_id=e.order_id and  $all_po_id_sub_cond  and a.production_date < =$txt_date $company_cond_sub      group by a.line_id   ,a.gmts_item_id  , e.color_id  , a.location_id  ,a.floor_id ,c.id";
	$sql_result_total_cut_sub = sql_select($sql_total_cut_sub);
	foreach($sql_result_total_cut_sub as $val)
	{
		$cutting_data_arr[$val[csf('po_break_down_id')]][$val[csf('item_number_id')]][$val[csf('country_id')]][$val[csf('color_number_id')]]['total_cutting']+=$val[csf('total_cutting')];
	}


	$all_po_id_cond3=str_replace("a.po_break_down_id", "c.order_id", $all_po_id_cond)  ;
	$sql_cut=sql_select("SELECT c.country_id ,c.order_id,b.color_id,b.gmt_item_id,sum(c.size_qty) as lay_qty  from ppl_cut_lay_bundle c ,ppl_cut_lay_dtls b ,ppl_cut_lay_mst a
where a.id=b.mst_id and b.mst_id=c.mst_id and b.id=c.dtls_id $all_po_id_cond3 and a.working_company_id=$cbo_company_id group by c.country_id ,c.order_id,b.color_id,b.gmt_item_id");
	foreach($sql_cut as $val)
	{
		$cutting_data_arr[$val[csf('order_id')]][$val[csf('gmt_item_id')]][$val[csf('country_id')]][$val[csf('color_id')]]['total_lay']+=$val[csf('lay_qty')];
	}
	 
	foreach($production_data_arr as $location_id=>$location_value ){
		foreach($location_value as $floor_id=>$floor_value ){
			foreach($floor_value as $line_id=>$line_value ){
				$line_span=0;
				foreach($line_value as $po_id=>$po_value ){
					foreach($po_value as $item_id=>$item_value ){
						foreach($item_value as $country_id=>$country_value ){
							foreach($country_value as $color_id=>$row ){
								//$rowspan_arr[$location_id][$floor_id][$line_id]+=1;
								$line_span++;
							}
						}
					}
					$rowspan_arr[$location_id][$floor_id][$line_id] =$line_span;
				}
			}
		}
	}
								
	
	ob_start();
	//echo "<pre>";print_r($rowspan_arr);die;
	
	?>
	<div style="width: 2250px;">
       <table width="2250" cellpadding="0" cellspacing="0"> 
				<tr class="form_caption">
					<td colspan="27" align="center"><strong><? echo $report_title; ?></strong></td> 
				</tr>
				<tr class="form_caption">
					<td colspan="27" align="center"><strong><? echo $companyArr[$comapny_id]; ?></strong></td> 
				</tr>
				<tr class="form_caption">
					<td colspan="27" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date)) ); ?></strong></td> 
				</tr>
			</table>
	<? 
	
    foreach($production_data_arr as $location_id=>$location_value ){
        foreach($location_value as $floor_id=>$floor_value ){
        	 
			$grand_lay=0;
			$grand_cutting=0;
			$grand_previous_sewing_input=0;
			$grand_today_sewing_input=0;
			$grand_sewing_input=0;
			$grand_previous_sewing_output=0;
			$grand_today_sewing_output=0;
			$grand_sewing_output=0;
			$grand_sewing_wip=0;
			$grand_rej=0;
			$grand_previous_poly=0;
			$grand_poly=0;
			$grand_today_poly=0;
			$grand_poly_wip=0;
            ?>
            
            <strong>Location: <? echo $locationArr[$location_id]; ?> Floor: <? echo $floorArr[$floor_id]; ?></strong>
             <table id="table_header_1" class="rpt_table" width="2200" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="100">Line No</th>
                        <th width="80">Buyer</th>
                        <th width="100">Job</th>
                        <th width="120">Style</th>
                        <th width="120">PO</th>
                        <th width="120">Item</th>
                        <th width="100">Country</th>
                        <th width="80">Country Shipdate</th>
                        <th width="100">Color</th>
                        <th width="80">OR/Qty.</th>
                        <th width="80">Lay Qty</th>
                        <th width="80">Cutting Qty.</th>
                        <th width="80">Previous Input</th>
                        <th width="80">Today Input</th>
                        <th width="80">TTL Input</th>
                        <th width="80">Previous Sewing Output</th>
                        <th width="80">Today Sewing Output</th>
                        <th width="80">TTL Sewing Output</th>
                        <th width="50">TTL Rej</th>
                        <th width="80">Sewing WIP</th>
                        <th width="80">Previous Poly Output</th>
                        <th width="80">Today Poly Output</th>
                        <th width="80">TTL Poly Output</th>
                        <th width="">Poly WIP</th>
                    </tr>
                </thead>
            </table>
            
            <div style="width:2200px; max-height:400px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="2183" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                    <tbody>
            
            <?php
            
            foreach($floor_value as $line_id=>$line_value ){
				$kk=0;
				$line_lay=0;
				$line_cutting=0;
				$line_previous_sewing_input=0;
				$line_today_sewing_input=0;
				$line_sewing_input=0;
				$line_previous_sewing_output=0;
				$line_today_sewing_output=0;
				$line_sewing_output=0;
				$line_rej=0;
				$line_sewing_wip=0;
				$line_previous_poly=0;
				$line_today_poly=0;
				$line_poly=0;
				$line_poly_wip=0;
				
                foreach($line_value as $po_id=>$po_value ){
                	
                    foreach($po_value as $item_id=>$item_value ){
                        foreach($item_value as $country_id=>$country_value ){
							$total_sewing_input=0;
							$total_sewing_output=0;
							$total_poly=0;
							$sewing_wip=0;
							$poly_wip=0;
                            foreach($country_value as $color_id=>$row ){
        		?>
                                    <tr>
                                        <?php
                                        //echo "string".$rowspan_arr[$location_id][$floor_id][$line_id]." a ".$kk;
                                        if($kk==0)
                                        	{
                                        if($country_id!='00')
                                        {
                                        	//if(!in_array($line_id,$line_check_arr))

                                        		?>
                                        		<td width="100" rowspan="<?php echo $rowspan_arr[$location_id][$floor_id][$line_id]; ?>">
                                        			<?
                                        			
                                        			$resource_line=explode(",",$prod_reso_arr[$line_id]);
											//print_r($resource_line);die;
                                        			$line_name='';
                                        			foreach($resource_line as $actual_line)
                                        			{
                                        				$line_name.= $lineArr[$actual_line].",";
                                        			}
                                        			echo chop($line_name,",");
                                        			?></td>
                                        			<?php
                                        			$line_check_arr[]=$line_id;
                                        		 

                                        }
                                        
                                        if($country_id=='00')
                                        {
                                        	?>
                                        	<td width="100">&nbsp;</td>

                                        	<?
                                        }
                                        $kk++;
                                    }
                       
					           
										$total_sewing_input=$ppd_arr[$location_id][$floor_id][$line_id][$po_id][$item_id][$country_id][$color_id]['sewing_input_previous']+$row['sewing_input'];
										$total_sewing_output=$ppd_arr[$location_id][$floor_id][$line_id][$po_id][$item_id][$country_id][$color_id]['sewing_output_previous']+$row['sewing_output'];
										$previous_poly=$ppd_arr[$location_id][$floor_id][$line_id][$po_id][$item_id][$country_id][$color_id]['poly_previous'];

										$total_poly= $previous_poly+$row['poly'];
										//$sewing_wip=$total_sewing_input-($total_sewing_output+$row['ttl_reject']);
										$sewing_wip=$total_sewing_input-($total_sewing_output+($row['ttl_reject']));
										$poly_wip=$total_sewing_output-$total_poly;
										
										// line grand total
										$line_lay+=$cutting_data_arr[$po_id][$item_id][$country_id][$color_id]['total_lay'];
										$line_cutting+=$cutting_data_arr[$po_id][$item_id][$country_id][$color_id]['total_cutting'];
										$line_previous_sewing_input+=$ppd_arr[$location_id][$floor_id][$line_id][$po_id][$item_id][$country_id][$color_id]['sewing_input_previous'];
										$line_today_sewing_input+=$row['sewing_input'];
										$line_sewing_input+=$total_sewing_input;
										$line_previous_sewing_output+=$ppd_arr[$location_id][$floor_id][$line_id][$po_id][$item_id][$country_id][$color_id]['sewing_output_previous'];
										$line_today_sewing_output+=$row['sewing_output'];
										
										$line_sewing_output+=$total_sewing_output;
										$line_sewing_wip+=$sewing_wip;
										$line_previous_poly+=$ppd_arr[$location_id][$floor_id][$line_id][$po_id][$item_id][$country_id][$color_id]['poly_previous'];
										$line_today_poly+=$row['poly'];
										$line_poly+=$total_poly;
										$line_poly_wip+=$poly_wip;
										
										
										// Floor grand total
										$grand_lay+=$cutting_data_arr[$po_id][$item_id][$country_id][$color_id]['total_lay'];
										$grand_cutting+=$cutting_data_arr[$po_id][$item_id][$country_id][$color_id]['total_cutting'];
										$grand_previous_sewing_input+=$ppd_arr[$location_id][$floor_id][$line_id][$po_id][$item_id][$country_id][$color_id]['sewing_input_previous'];
										$grand_today_sewing_input+=$row['sewing_input'];
										$grand_sewing_input+=$total_sewing_input;
										
										$grand_previous_sewing_output+=$ppd_arr[$location_id][$floor_id][$line_id][$po_id][$item_id][$country_id][$color_id]['sewing_output_previous'];
										$grand_today_sewing_output+=$row['sewing_output'];
										$grand_sewing_output+=$total_sewing_output;
										$grand_sewing_wip+=$sewing_wip;
										$grand_previous_poly+=$ppd_arr[$location_id][$floor_id][$line_id][$po_id][$item_id][$country_id][$color_id]['poly_previous'];
										$grand_today_poly+=$row['poly'];
										$grand_poly+=$total_poly;
										$grand_poly_wip+=$poly_wip;
										$grand_rej+=$row['ttl_reject'];
										$line_rej+=$row['ttl_reject'];
										
                                        ?>
                                       
                                        <td width="80"><? if($country_id=="00"){ echo $buyerArr[$row['party_id']];} else { echo $buyerArr[$row['buyer_name']] ;} ?></td>
                                        <td width="100"><?  echo $row['job_no_mst']; ?></td>
                                        <td width="120"><? if($country_id=="00"){ echo $row['cust_style_ref'];} else {echo $row['style_ref_no'];} ?></td>
                                        <td width="120"><? if($country_id=="00"){ echo $row['po_number']."(In-Sub)";} else {echo $row['po_number'];} ?></td>
                                        <td width="120"><? echo $garments_item[$item_id]; ?></td>
                                        <td width="100"><? echo $countryArr[$country_id]; ?></td>
                                        <td width="80"><? echo change_date_format($row['country_ship_date']); ?></td>
                                        <td width="100"><? echo $colorArr[$color_id]; ?></td>
                                        <td width="80" align="right"><? if($country_id=="00"){ echo $row['po_quantity'];} else { echo $color_size_qty_arr[$po_id][$item_id][$country_id][$color_id]['order_quantity'];} ?></td>
                                        <td width="80" align="right"><? echo $cutting_data_arr[$po_id][$item_id][$country_id][$color_id]['total_lay']; ?></td>
                                        <td width="80" align="right"><? echo $cutting_data_arr[$po_id][$item_id][$country_id][$color_id]['total_cutting']; ?></td>
                                        <td width="80" align="right"><? echo $ppd_arr[$location_id][$floor_id][$line_id][$po_id][$item_id][$country_id][$color_id]['sewing_input_previous']; ?></td>
                                        <td width="80" align="right"><? echo $row['sewing_input']; ?></td>
                                        <td width="80" align="right"><? echo $total_sewing_input; ?></td>
                                        <td width="80" align="right"><? echo $ppd_arr[$location_id][$floor_id][$line_id][$po_id][$item_id][$country_id][$color_id]['sewing_output_previous']; ?></td>
                                        <td width="80" align="right"><? echo $row['sewing_output']; ?></td>
                                        <td width="80" align="right"><? echo $total_sewing_output; ?></td>
                                        <td width="50" align="right"><? echo $row['ttl_reject']; ?></td>
                                        <td width="80" align="right"><? echo $sewing_wip; ?></td>
                                        <td width="80" align="right"><? echo $previous_poly; ?></td>
                                        <td width="80" align="right"><? echo $row['poly']; ?></td>
                                        <td width="80" align="right"><? echo $total_poly; ?></td>
                                        <td width="" align="right"><? echo $poly_wip; ?></td>
                                    </tr>
        <? 
                                //}
                            }
                        }
                    }
                }
				
					?>
                	<tr class="tbl_bottom">
                    	<td width="" colspan="10" align="right">Line Total</td>
                        <td width="80" align="right"><? echo $line_lay; ?></td>
                        <td width="80" align="right"><? echo $line_cutting; ?></td>
                        <td width="80" align="right"><? echo $line_previous_sewing_input; ?></td>
                        <td width="80" align="right"><? echo $line_today_sewing_input; ?></td>
                        <td width="80" align="right"><? echo $line_sewing_input; ?></td>
                        <td width="80" align="right"><? echo $line_previous_sewing_output; ?></td>
                        <td width="80" align="right"><? echo $line_today_sewing_output; ?></td>
                        <td width="80" align="right"><? echo $line_sewing_output; ?></td>
                        <td width="50" align="right"><? echo $line_rej; ?></td>
                        <td width="80" align="right"><? echo $line_sewing_wip; ?></td>
                        <td width="80" align="right"><? echo $line_previous_poly; ?></td>
                        <td width="80" align="right"><? echo $line_today_poly; ?></td>
                        <td width="80" align="right"><? echo $line_poly; ?></td>
                        <td width="" align="right"><? echo $line_poly_wip; ?></td>
                    </tr>
                <?php
            }
            
            ?>
                </tbody>
                <tfoot>
                   <tr>
                        <th width="" colspan="10" align="right">Grand Total</th>
                        <th width="80"><? echo $grand_lay; ?></th>
                        <th width="80"><? echo $grand_cutting; ?></th>
                        <th width="80"><? echo $grand_previous_sewing_input; ?></th>
                        <th width="80"><? echo $grand_today_sewing_input; ?></th>
                        <th width="80"><? echo $grand_sewing_input; ?></th>
                        <th width="80"><? echo $grand_previous_sewing_output; ?></th>
                        <th width="80"><? echo $grand_today_sewing_output; ?></th>
                        <th width="80"><? echo $grand_sewing_output; ?></th>
                        <th width="50"><? echo $grand_rej; ?></th>
                        <th width="80"><? echo $grand_sewing_wip; ?></th>
                        <th width="80"><? echo $grand_previous_poly; ?></th>
                        <th width="80"><? echo $grand_today_poly; ?></th>
                        <th width="80"><? echo $grand_poly; ?></th>
                        <th width=""><? echo $grand_poly_wip; ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        </div>
        <br/>
        <br/>
        <?php
        }
    }
    
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename,'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename";
	exit();      

}

?>
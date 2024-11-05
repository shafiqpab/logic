<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

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
	echo create_drop_down( "cbo_store_id", 110, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id='$data' and  b.category_type in(13)  group by a.id,a.store_name","id,store_name", 1, "--Select Store--", 0, "",$disable );
	exit();
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
	
		var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
		
		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
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

    </script>
</head>
<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:580px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th>
                    	<input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                        <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    	<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                    </th> 					
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'order_wise_grey_fabric_stock_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$buyer_array=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	//echo $month_id;

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
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	
	if($db_type==0)
	{
		if($year_id!=0) $year_search_cond=" and year(insert_date)=$year_id"; else $year_search_cond="";
		$year_cond= "year(insert_date)as year";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_search_cond=" and TO_CHAR(insert_date,'YYYY')=$year_id"; else $year_search_cond="";
		$year_cond= "TO_CHAR(insert_date,'YYYY') as year";
	}
		
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_cond from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_search_cond $month_cond order by job_no DESC";
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,80,80,60","620","270",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
   exit(); 
} 

if ($action=="order_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data); 
?>	
    <script>
	 
	function js_set_value(str)
	{
		var splitData = str.split("_");
		//alert (splitData[1]);
		$("#order_no_id").val(splitData[0]); 
		$("#order_no_val").val(splitData[1]); 
		parent.emailwindow.hide();
	}
		  
	</script>
     <input type="hidden" id="order_no_id" />
     <input type="hidden" id="order_no_val" />
 <?
	if ($data[1]==0) $buyer_name=""; else $buyer_name=" and b.buyer_name=$data[1]";
	if ($data[2]=="") $order_no=""; else $order_no=" and a.po_number=$data[2]";
	$job_no=str_replace("'","",$txt_job_id);
	if ($data[2]=="") $job_no_cond=""; else $job_no_cond="  and b.job_no_prefix_num='$data[2]'";
	
	$sql="select a.id, a.po_number, b.job_no_prefix_num, b.job_no, b.buyer_name, b.style_ref_no from wo_po_details_master b, wo_po_break_down a  where b.job_no=a.job_no_mst and b.company_name=$data[0] and b.is_deleted=0 $buyer_name $job_no_cond ORDER BY b.job_no DESC";
	//echo $sql;
	$arr=array(1=>$buyer_arr);
	
	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No", "110,110,150,180","610","350",0, $sql, "js_set_value", "id,po_number", "", 1, "0,buyer_name,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number", "order_wise_grey_fabric_stock_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
	disconnect($con);
	exit();
}

$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$rpt_type=str_replace("'","",$rpt_type);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$txt_search_comm=str_replace("'","",$txt_search_comm);
	$cbo_sock_for=str_replace("'","",$cbo_sock_for);
	$cbo_value_with=str_replace("'","",$cbo_value_with);
	$cbo_store_id=str_replace("'","",$cbo_store_id);

	$company_cond ="";
    if(str_replace("'", "", $cbo_company_id) != 0){ $company_cond= " and b.company_name=$cbo_company_id";}
	
	//if($cbo_value_with==1) $zero_value=0;else  $zero_value=0;
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
			
			
				$constructionArr[$row[csf('id')]]=$constructionArr[$row[csf('id')]];
				list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
				$copmpositionArr[$row[csf('id')]]=$cps;
			
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				
				
				$constructionArr[$row[csf('id')]]=$row[csf('construction')];
				list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
				$copmpositionArr[$row[csf('id')]]=$cps;
				
			}
		}
	}
	
	
	$transaction_date_array=array();
	$sql_date="Select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where status_active=1 and is_deleted=0 and item_category=13 group by prod_id";
	$sql_date_result=sql_select($sql_date);
	foreach( $sql_date_result as $row )
	{
		$transaction_date_array[$row[csf('prod_id')]]['min_date']=$row[csf('min_date')];
		$transaction_date_array[$row[csf('prod_id')]]['max_date']=$row[csf('max_date')];
	}
	
	//if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and b.buyer_name=$cbo_buyer_id";
	if(str_replace("'","",$cbo_buyer_id)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and b.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and b.buyer_name=$cbo_buyer_id";//.str_replace("'","",$cbo_buyer_name)
	}

	if ($cbo_store_id!=0) $store_cond=" and a.store_id=$cbo_store_id";
	
	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and b.job_no_prefix_num in ($job_no) ";
	$year_id=str_replace("'","",$cbo_year);

	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(b.insert_date)=$year_id"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_cond=" and TO_CHAR(b.insert_date,'YYYY')=$year_id"; else $year_cond="";
	}
	
	/*$order_no=str_replace("'","",$txt_order_id);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond=" and a.id in ($order_no)";*/
	$search_cond='';
	if($cbo_search_by==1)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.style_ref_no LIKE '$txt_search_comm%'";
	}
	else if($cbo_search_by==2)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.po_number LIKE '$txt_search_comm%'";
	}
	else if($cbo_search_by==3)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.file_no LIKE '$txt_search_comm%'";
	}
	else if($cbo_search_by==4)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.grouping LIKE '$txt_search_comm%'";
	}
	else
	{
		$search_cond.="";
	}
	
	$order_cond="";
	if($cbo_sock_for==1)
	{
		$order_cond=" and a.shiping_status<>3 and a.status_active=1";
	}
	else if($cbo_sock_for==2)
	{
		$order_cond=" and a.status_active=3";
	}
	else if($cbo_sock_for==3)
	{
		$order_cond=" and a.shiping_status=3 and a.status_active=1";
	}
	else
	{
		$order_cond="";
	}
	
	if($rpt_type==1)
	{

		$lib_color_array=return_library_array( "select id,color_name from lib_color where is_deleted=0 and status_active=1", "id", "color_name" );

		
		if( str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.transaction_date <=".$txt_date_from."";

		$sql_po="SELECT  x.booking_no,b.job_no, b.buyer_name, b.style_ref_no, a.id, a.po_number, a.grouping,b.company_name,sum(x.grey_fab_qnty) as grey_fab_qnty,y.body_part_id,y.lib_yarn_count_deter_id  from wo_booking_dtls x,wo_pre_cost_fabric_cost_dtls y,wo_po_details_master b, wo_po_break_down a  where x.job_no=b.job_no  and x.pre_cost_fabric_cost_dtls_id=y.id and y.job_no=b.job_no and  b.id=a.job_id and b.status_active=1 and b.is_deleted=0  $year_cond $buyer_id_cond $job_no_cond $search_cond $order_cond $company_cond group by x.booking_no,b.job_no, b.buyer_name, b.style_ref_no, a.id, a.po_number, a.grouping,b.company_name,y.body_part_id,y.lib_yarn_count_deter_id  order  by b.company_name,b.buyer_name,b.style_ref_no,y.lib_yarn_count_deter_id ";


		//$sql_po="SELECT x.booking_no,b.job_no, b.buyer_name, b.style_ref_no, a.id, a.po_number, a.grouping, a.po_quantity,b.company_name ,x.fabric_color_id,sum(x.grey_fab_qnty) as grey_fab_qnty from wo_booking_dtls x,wo_po_details_master b, wo_po_break_down a where x.job_no=b.job_no and b.job_no=a.job_no_mst  and b.status_active=1 and b.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $search_cond $order_cond $company_cond group by x.booking_no,b.job_no, b.buyer_name, b.style_ref_no, a.id, a.po_number, a.grouping, a.po_quantity,b.company_name ,x.fabric_color_id order by b.company_name,a.id";




		$sql_po_arr=sql_select($sql_po);

		if(!empty($sql_po_arr))
		{
			$con = connect();	
			$r_id1=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id ");
			if($r_id1)
			{
				oci_commit($con);
			}

			foreach($sql_po_arr as $row)
			{
				//$tot_rows++;
				//$ref_file=$row[csf('grouping')]."_".$row[csf('job_no')]."_".$row[csf('style_ref_no')]."_".$row[csf('buyer_name')]."_".$row[csf('fabric_color_id')]."_".$row[csf('gsm_weight')]."_".$row[csf('dia_width')];
				$ref_file=$row[csf('grouping')]."_".$row[csf('job_no')]."_".$row[csf('style_ref_no')]."_".$row[csf('buyer_name')]."_".$row[csf('lib_yarn_count_deter_id')];
				$ref_file2=$row[csf('grouping')]."_".$row[csf('job_no')]."_".$row[csf('style_ref_no')]."_".$row[csf('buyer_name')];
				//$poIds.=$row[csf('id')].",";
				$all_po_id_arr[$row[csf("id")]] = $row[csf("id")];
				//$poArr[$row[csf('id')]]=$ref_file;

				//$fileRefArr[$ref_file].=$row[csf('id')].",";

				$job_arr[$row[csf("id")]][$row[csf('body_part_id')]]=$ref_file;
				$job_arr2[$ref_file2]=$row[csf("detarmination_id")];
			}
		}
		else
		{
			echo "Data Not Found";die;
		}
		/*echo "<pre>";
			print_r($job_arr);
		echo "</pre>";
		die;*/
		unset($sql_po_arr);
		$all_po_id_arr = array_filter($all_po_id_arr);
		$all_po_id_arr = array_unique(explode(",",implode(",", $all_po_id_arr)));
		if(!empty($all_po_id_arr))
		{	
			
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 1995, 1,$all_po_id_arr, $empty_arr);//PO ID
		}


		$product_array=array();	
		$prod_query="Select id, detarmination_id, gsm, dia_width, brand from product_details_master where item_category_id=13 and status_active=1 and is_deleted=0 ";
		$prod_query_sql=sql_select($prod_query);
		foreach( $prod_query_sql as $row )
		{
			$product_array[$row[csf('id')]]['detarmination_id']=$row[csf('detarmination_id')];
			$product_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
			$product_array[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
			$product_array[$row[csf('id')]]['brand']=$row[csf('brand')];
		}
		
		$product_id_arr=array(); $recvIssue_array=array(); $trans_arr=array();$tran_qnty_dataArr=array();
		/*$sql_trans="SELECT b.trans_type, b.po_breakdown_id, b.prod_id, sum(b.quantity) as qnty, a.store_id
		from inv_transaction a, order_wise_pro_details b ,GBL_TEMP_ENGINE c 
		where a.id=b.trans_id and b.po_breakdown_id=c.ref_val and c.user_id=$user_id and c.entry_form=1995 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2,22,13,16,45,51,58,61,80,81,83,84) and a.item_category=13 and a.transaction_type in(1,2,3,4,5,6) and b.trans_type  in(1,2,3,4,5,6) $trans_date $store_cond
		group by b.trans_type, b.po_breakdown_id, b.prod_id, a.store_id";*/

		$sql_trans_recv="SELECT b.trans_type, b.po_breakdown_id, b.prod_id,x.color_id,x.body_part_id, sum(b.quantity) as qnty, a.store_id,x.febric_description_id
		from pro_grey_prod_entry_dtls x,inv_transaction a, order_wise_pro_details b ,GBL_TEMP_ENGINE c 
		where x.trans_id=a.id and a.id=b.trans_id and b.po_breakdown_id=c.ref_val and c.user_id=$user_id and c.entry_form=1995 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2,22,58,51,84) and a.item_category=13 and a.transaction_type in(1,4) and b.trans_type  in(1,4) $trans_date $store_cond
		group by b.trans_type, b.po_breakdown_id, b.prod_id,x.color_id,x.body_part_id, a.store_id,x.febric_description_id";
		$result_trans_recv=sql_select( $sql_trans_recv );
		$recv_qnty_dataArr=array();
		foreach ($result_trans_recv as $row)
		{
			$trans_dataArrColor[$job_arr[$row[csf("po_breakdown_id")]][$row[csf("body_part_id")]]][$row[csf("body_part_id")]][$row[csf("febric_description_id")]]["color_id"].=$row[csf("color_id")].",";
			
			$trans_dataArrProd[$job_arr[$row[csf("po_breakdown_id")]][$row[csf("body_part_id")]]][$row[csf("body_part_id")]][$row[csf("febric_description_id")]]["prod_id"]=$row[csf("prod_id")];
			
			$recv_qnty_dataArr[$job_arr[$row[csf("po_breakdown_id")]][$row[csf("body_part_id")]]][$row[csf("body_part_id")]][$row[csf("febric_description_id")]][$row[csf("trans_type")]]+=$row[csf('qnty')];
			$porduct_dataArr[$job_arr[$row[csf("po_breakdown_id")]]]=$row[csf('prod_id')];
		}
		/*echo "<pre>";
		print_r($recv_qnty_dataArr);
		echo "</pre>"; die;*/
		$sql_trans_issue="SELECT b.trans_type, b.po_breakdown_id, b.prod_id,x.color_id,x.program_no, sum(b.quantity) as qnty, a.store_id,x.body_part_id,d.detarmination_id 
		from inv_grey_fabric_issue_dtls x,inv_transaction a, order_wise_pro_details b ,product_details_master d,GBL_TEMP_ENGINE c 
		where x.trans_id=a.id and a.id=b.trans_id and b.prod_id=d.id and b.po_breakdown_id=c.ref_val and c.user_id=$user_id and c.entry_form=1995 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(16,61) and a.item_category=13 and a.transaction_type in(2) and b.trans_type  in(2) $trans_date $store_cond
		group by b.trans_type, b.po_breakdown_id, b.prod_id,x.color_id,x.program_no, a.store_id,x.body_part_id,d.detarmination_id ";
		$result_trans_issue=sql_select( $sql_trans_issue );
		foreach ($result_trans_issue as $row)
		{
			//$trans_dataArr[$job_arr[$row[csf("po_breakdown_id")]]]["color_id"]=$row[csf("color_id")];
			//$trans_dataArr[$job_arr[$row[csf("po_breakdown_id")]]]["prod_id"]=$row[csf("prod_id")];
			$issue_qnty_dataArr[$job_arr[$row[csf("po_breakdown_id")]][$row[csf("body_part_id")]]][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$row[csf("trans_type")]]+=$row[csf('qnty')];
		}
		
		/*$sql_transfer_qnty="select b.trans_type, b.po_breakdown_id, a.prod_id, sum(a.cons_quantity) as trans_qnty,x.to_store as store,x.to_body_part as body_part_id from  inv_item_transfer_mst y,inv_item_transfer_dtls x,inv_transaction a,order_wise_pro_details b,GBL_TEMP_ENGINE c  where y.id=x.mst_id and x.to_trans_id=a.id and a.id=b.trans_id and b.po_breakdown_id=c.ref_val and c.user_id=$user_id and c.entry_form=1995 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and x.status_active=1 and x.is_deleted=0 and y.status_active=1 and y.is_deleted=0 and b.entry_form in(80,81,82,84,83) and a.item_category=13 and a.transaction_type in(5) and b.trans_type in(5)  $trans_date $store_cond group by b.trans_type, b.po_breakdown_id, a.prod_id,x.to_store,x.to_body_part
		union all   

		select b.trans_type, b.po_breakdown_id, a.prod_id, sum(a.cons_quantity) as trans_qnty,x.from_store as store,x.body_part_id
		from inv_item_transfer_mst y,inv_item_transfer_dtls x,inv_transaction a,order_wise_pro_details b,GBL_TEMP_ENGINE c 
		where y.id=x.mst_id and x.trans_id=a.id and a.id=b.trans_id and b.po_breakdown_id=c.ref_val and c.user_id=$user_id and c.entry_form=1995 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and x.status_active=1 and x.is_deleted=0 and y.status_active=1 and y.is_deleted=0 and b.entry_form in(80,81,82,84,83) and a.item_category=13 and a.transaction_type in(6) and b.trans_type in(6)  $trans_date $store_cond
		group by b.trans_type, b.po_breakdown_id, a.prod_id,x.from_store,x.body_part_id";*/

		$sql_transfer_qnty="select b.trans_type, b.po_breakdown_id, a.prod_id, sum(a.cons_quantity) as trans_qnty,x.to_store as store,x.to_body_part as body_part_id,d.detarmination_id  from  inv_item_transfer_mst y,inv_item_transfer_dtls x,inv_transaction a,order_wise_pro_details b,product_details_master d,GBL_TEMP_ENGINE c  where y.id=x.mst_id and x.to_trans_id=a.id and a.id=b.trans_id and b.prod_id=d.id and b.po_breakdown_id=c.ref_val and c.user_id=$user_id and c.entry_form=1995 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and x.status_active=1 and x.is_deleted=0 and y.status_active=1 and y.is_deleted=0 and b.entry_form in(80,81,82,84,83) and a.item_category=13 and a.transaction_type in(5) and b.trans_type in(5)  $trans_date $store_cond group by b.trans_type, b.po_breakdown_id, a.prod_id,x.to_store,x.to_body_part,d.detarmination_id 
		union all   

		select b.trans_type, b.po_breakdown_id, a.prod_id, sum(a.cons_quantity) as trans_qnty,x.from_store as store,x.body_part_id,d.detarmination_id 
		from inv_item_transfer_mst y,inv_item_transfer_dtls x,inv_transaction a,order_wise_pro_details b,product_details_master d,GBL_TEMP_ENGINE c 
		where y.id=x.mst_id and x.trans_id=a.id and a.id=b.trans_id and b.prod_id=d.id and b.po_breakdown_id=c.ref_val and c.user_id=$user_id and c.entry_form=1995 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and x.status_active=1 and x.is_deleted=0 and y.status_active=1 and y.is_deleted=0 and b.entry_form in(80,81,82,84,83) and a.item_category=13 and a.transaction_type in(6) and b.trans_type in(6)  $trans_date $store_cond
		group by b.trans_type, b.po_breakdown_id, a.prod_id,x.from_store,x.body_part_id,d.detarmination_id ";

		$data_transfer_in_array=sql_select($sql_transfer_qnty);
		foreach( $data_transfer_in_array as $row )
		{
			$transfer_in_qnty_dataArr[$job_arr[$row[csf("po_breakdown_id")]][$row[csf("body_part_id")]]][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$row[csf("trans_type")]]+=$row[csf('trans_qnty')];
			$transfer_out_qnty_dataArr[$job_arr[$row[csf("po_breakdown_id")]][$row[csf("body_part_id")]]][$row[csf("body_part_id")]][$row[csf("detarmination_id")]][$row[csf("trans_type")]]+=$row[csf('trans_qnty')];

			//$trans_arr[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('transaction_type')]]=$row[csf('trans_qnty')];
			//$product_id_arr[$row[csf('order_id')]].=$row[csf('prod_id')].",";
		}
		/*echo "<pre>";
			print_r($trans_dataArrProd);
		echo "</pre>";*/

		/*foreach( $data_transfer_in_array as $row )
		{
			$trans_arr[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('transaction_type')]]=$row[csf('trans_qnty')];
			$product_id_arr[$row[csf('order_id')]].=$row[csf('prod_id')].",";
		}*/
		//print_r($trans_arr[3593]);
		
		//$store_name_arr = return_library_array("select id, store_name from  lib_store_location", 'id', 'store_name');

		$r_id1=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id ");
		if($r_id1)
		{
			oci_commit($con);
		}
		ob_start();
		?>
		<fieldset style="width:1920px">
			<table cellpadding="0" cellspacing="0" width="1810">
				<tr  class="form_caption" style="border:none;">
				   <td align="center" width="100%" colspan="22" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
                   	<td align="center" width="100%" colspan="22" style="font-size:16px">
                       <strong>
                           <? if(str_replace("'", "", $cbo_company_id) != 0) 
                           	{ 
                                echo $company_arr[str_replace("'","",$cbo_company_id)];
                           	}else { 
                                echo "All Company";
                           	} ?>
                       </strong>
                   	</td>
				</tr>
				<tr  class="form_caption" style="border:none;">
				   <td align="center" width="100%" colspan="22" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
				</tr>
			</table>
			<table width="1920" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
				<thead>
					<tr>
                        <th width="40" rowspan="2">SL</th>
                        <? if(str_replace("'", "", $cbo_company_id) == 0) {?>
                        <th width="150" rowspan="2">Company</th>
                        <?}?>
                        <th width="100" rowspan="2">Ref. No</th>
                        <th width="100" rowspan="2">Job No</th>
                        <th width="100" rowspan="2">Style No</th>
                        <th width="100" rowspan="2">Buyer</th>
                        <th width="100" rowspan="2">Body Part</th>
                        
                        <th colspan="5">Fabric Details</th>
                        <th colspan="4">Receive Details</th>
                        <th colspan="4">Issue Details</th>
                        <th rowspan="2">Stock Qty.</th>

                        
					</tr>
					<tr>
						<th width="150">Const. & Comp</th>
						<th width="100" >Fabric Color</th>
						<th width="100" >Booking Qnty</th>
						<th width="70">GSM</th>
                        <th width="60">F/Dia</th>

                        
                        <th width="90">Recv. Qty.</th>
                        <th width="90">Issue Return Qty.</th>
                        <th width="90">Transf. In Qty.</th>
                        <th width="90">Total Recv.</th>
                        <th width="90">Issue Qty.</th>
                        <th width="90">Receive Return Qty.</th>
                        <th width="90">Transf. Out Qty.</th>
                        <th width="90">Total Issue</th>
					</tr>
				</thead>
			</table>
			<div style="width:1940px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="1920" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left"> 
					<?
					//and b.job_no in('D n C-17-00313','D n C-17-00314')

				 $sql="SELECT b.job_no, b.buyer_name, b.style_ref_no, a.grouping,b.company_name,sum(x.grey_fab_qnty) as grey_fab_qnty,y.body_part_id,y.lib_yarn_count_deter_id from wo_booking_dtls x,wo_pre_cost_fabric_cost_dtls y,wo_po_details_master b, wo_po_break_down a where x.job_no=b.job_no and x.pre_cost_fabric_cost_dtls_id=y.id and b.id=a.job_id and b.status_active=1 and b.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $search_cond $order_cond $company_cond group by b.job_no, b.buyer_name, b.style_ref_no, a.grouping,b.company_name,y.body_part_id,y.lib_yarn_count_deter_id order by b.company_name,b.buyer_name,b.style_ref_no,y.lib_yarn_count_deter_id";


					$result=sql_select( $sql );

					$i=1; $tot_recv_qty=0; $tot_iss_qty=0; $tot_trans_in_qty=0; $tot_trans_out_qty=0; $grand_tot_recv_qty=0; $grand_tot_iss_qty=0; $grand_stock_qty=0;$booking_req_qty=0;$recv_qty=0;
					foreach($result as $row)
					{
						//$dataProd=array_filter(array_unique(explode(",",substr($product_id_arr[$row[csf('id')]],0,-1))));
						/*if(count($dataProd)>0)
						{*/
							$order_recv_qty=0; $order_iss_ret_qty=0; $order_iss_qty=0; $order_rec_ret_qty=0; $order_trans_in_qty=0; $order_trans_out_qty=0; $order_tot_recv_qnty=0; $order_tot_iss_qnty=0; $order_stock_qnty=0;$p=1;
							/*foreach($dataProd as $prodId)
							{*/
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								
								$prodId=$trans_dataArrProd[$row[csf('grouping')]."_".$row[csf('job_no')]."_".$row[csf('style_ref_no')]."_".$row[csf('buyer_name')]."_".$row[csf('lib_yarn_count_deter_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]["prod_id"];
								//echo $prodId."<br/>";

								$colorIds=$trans_dataArrColor[$row[csf('grouping')]."_".$row[csf('job_no')]."_".$row[csf('style_ref_no')]."_".$row[csf('buyer_name')]."_".$row[csf('lib_yarn_count_deter_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]]["color_id"];

								$colorIDS=array_unique(explode(",",$colorIds));
								$colorName="";
								foreach ($colorIDS as $colorKey) {
									$colorName.=$lib_color_array[$colorKey].",";
								}
								$colorName=chop($colorName,",");

								//echo $row[csf('grouping')]."_".$row[csf('job_no')]."_".$row[csf('style_ref_no')]."_".$row[csf('buyer_name')]."_".$row[csf('lib_yarn_count_deter_id')]."_".$row[csf('body_part_id')]."_".$row[csf('lib_yarn_count_deter_id')] ."_".$prodId."<br/>";
								
								$recv_qty=$recv_qnty_dataArr[$row[csf('grouping')]."_".$row[csf('job_no')]."_".$row[csf('style_ref_no')]."_".$row[csf('buyer_name')]."_".$row[csf('lib_yarn_count_deter_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][1];

								//echo	$row[csf('grouping')]."_".$row[csf('job_no')]."_".$row[csf('style_ref_no')]."_".$row[csf('buyer_name')]."_".$row[csf('body_part_id')]."=".$colorIds."=".$prodId."=".$recv_qty."<br/>";

								$iss_qty=$issue_qnty_dataArr[$row[csf('grouping')]."_".$row[csf('job_no')]."_".$row[csf('style_ref_no')]."_".$row[csf('buyer_name')]."_".$row[csf('lib_yarn_count_deter_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][2];
								$iss_ret_qty=$recv_qnty_dataArr[$row[csf('grouping')]."_".$row[csf('job_no')]."_".$row[csf('style_ref_no')]."_".$row[csf('buyer_name')]."_".$row[csf('lib_yarn_count_deter_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][4];

								$trans_in_qty=$transfer_in_qnty_dataArr[$row[csf('grouping')]."_".$row[csf('job_no')]."_".$row[csf('style_ref_no')]."_".$row[csf('buyer_name')]."_".$row[csf('lib_yarn_count_deter_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][5];;
								$trans_out_qty=$transfer_out_qnty_dataArr[$row[csf('grouping')]."_".$row[csf('job_no')]."_".$row[csf('style_ref_no')]."_".$row[csf('buyer_name')]."_".$row[csf('lib_yarn_count_deter_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][6];

								$recv_tot_qty=$recv_qty+$iss_ret_qty+$trans_in_qty;
								$iss_tot_qty=$iss_qty+$trans_out_qty;
								$stock_qty=$recv_tot_qty-$iss_tot_qty;
								//echo $trans_in_qty.', '.$trans_out_qty;
								$store=$store_data_array[$row[csf('id')]][$prodId]['store_id'];
								//$program_no=implode(",",array_unique(explode(",",$program_no_array[$row[csf('id')]])));
								$stock_qtys=number_format($stock_qty,2);
								if($cbo_value_with==1 && $stock_qty>=0) 
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="40"><? echo $i; ?></td>
										<?
										if($p==1)
										{
                                            if($company_cond ==""){
											?>
                                            <td width="150"><p><? echo $company_arr[$row[csf('company_name')]]; ?>&nbsp;</p></td>
                                            <?}?>
											<td width="100"><p><? echo $row[csf('grouping')]; ?>&nbsp;</p></td>
											<td width="100"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
											<td width="100"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
											<td width="100"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
											<td width="100" title="<? echo $row[csf('body_part_id')]; ?>"><p><? echo $body_part[$row[csf('body_part_id')]]; ?>&nbsp;</p></td>
											<?
										}
										else
										{
                                            if($company_cond ==""){
											?>
                                            <td width="150"><p>&nbsp;</p></td>
                                            <? }?>
											<td width="100"><p>&nbsp;</p></td>
											<td width="100"><p>&nbsp;</p></td>
											<td width="100"><p>&nbsp;</p></td>
											<td width="100"><p>&nbsp;</p></td>
											<td width="100"><p>&nbsp;</p></td>
											<?
										}
										$p++;
										?>

										<td width="150"><p><? echo $composition_arr[$product_array[$prodId]['detarmination_id']]; ?></p></td>
										<td width="100" align="left" title="<? echo $colorIds; ?>"><p><? echo $colorName; ?></p></td>
										<td width="100" align="right"><? echo number_format($row[csf('grey_fab_qnty')],2); ?></td>
										<td width="70" align="center"><p><? echo $product_array[$prodId]['gsm']; ?>&nbsp;</p></td>
										<td width="60" align="center"><p><? echo  $product_array[$prodId]['dia_width']; ?>&nbsp;</p></td>

										<td width="90" align="right"><? echo number_format($recv_qty,2); ?></td>
										<td width="90" align="right"><? echo number_format($iss_ret_qty,2); ?></td>
										<td width="90" align="right"><? echo number_format($trans_in_qty,2);?></td>
										<td width="90" align="right"><? echo number_format($recv_tot_qty,2); ?></td>
										<td width="90" align="right"><? echo number_format($iss_qty,2); ?></p></td>
										<td width="90" align="right"><?echo number_format($recv_ret_qty,2);?></p></td>
										<td width="90" align="right"><? echo number_format($trans_out_qty,2); ?></td>
										<td width="90" align="right"><? echo number_format($iss_tot_qty,2); ?></td>
										<td align="right"><? echo number_format($stock_qty,2); ?></td>
										
										<?
										//$daysOnHand=datediff("d",change_date_format($transaction_date_array[$prodId]['max_date'],'','',1),date("Y-m-d")); 
										 //if($stock_qty>0) echo $daysOnHand; ?>
									</tr>
									<?	
									$i++;
									
									$booking_req_qty+=$row[csf('grey_fab_qnty')];
									$order_recv_qty+=$recv_qty;
									$order_iss_ret_qty+=$iss_ret_qty;  
									$order_iss_qty+=$iss_qty;
									$order_rec_ret_qty+=$recv_ret_qty; 
									$order_trans_in_qty+=$trans_in_qty; 
									$order_trans_out_qty+=$trans_out_qty; 
									$order_tot_recv_qnty+=$recv_tot_qty; 
									$order_tot_iss_qnty+=$iss_tot_qty;
									$order_stock_qnty+=$stock_qty;									
									
									$tot_recv_qty+=$recv_qty; 
									$tot_iss_ret_qty+=$iss_ret_qty; 
									$tot_iss_qty+=$iss_qty; 
									$tot_rec_ret_qty+=$recv_ret_qty; 
									$tot_trans_in_qty+=$trans_in_qty; 
									$tot_trans_out_qty+=$trans_out_qty; 
									$grand_tot_recv_qty+=$recv_tot_qty; 
									$grand_tot_iss_qty+=$iss_tot_qty;
									$grand_stock_qty+=$stock_qty;
	                            }
								else if($cbo_value_with==2 && $stock_qtys>0.00) 
								//if($cbo_value_with==24 && $stock_qty>04) 
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="40"><? echo $i; ?></td>
										<?
										if($p==1)
										{
                                            if($company_cond ==""){
											?>
                                            <td width="150"><p><? echo $company_arr[$row[csf('company_name')]]; ?>&nbsp;</p></td>
                                            <?}?>
											<td width="100"><p><? echo $row[csf('grouping')]; ?>&nbsp;</p></td>
											<td width="100"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
											<td width="100"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
											<td width="100"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
											<td width="100" title="<? echo $row[csf('body_part_id')]; ?>"><p><? echo $body_part[$row[csf('body_part_id')]]; ?>&nbsp;</p></td>
											<?
										}
										else
										{
                                            if($company_cond ==""){
											?>
                                            <td width="150"><p>&nbsp;</p></td>
                                            <?}?>
											<td width="100"><p>&nbsp;</p></td>
											<td width="100"><p>&nbsp;</p></td>
											<td width="100"><p>&nbsp;</p></td>
											<td width="100"><p>&nbsp;</p></td>
											<td width="100"><p>&nbsp;</p></td>
											<?
										}
										$p++;
										?>

										<td width="150"><p><? echo $composition_arr[$product_array[$prodId]['detarmination_id']]; ?></p></td>
										<td width="100" align="left" title="<? echo $colorIds; ?>"><p><? echo $colorName; ?></p></td>
										<td width="100" align="right"><? echo number_format($row[csf('grey_fab_qnty')],2); ?></td>
										<td width="70" align="center"><p><? echo $product_array[$prodId]['gsm']; ?>&nbsp;</p></td>
										<td width="60" align="center"><p><? echo  $product_array[$prodId]['dia_width']; ?>&nbsp;</p></td>

										<td width="90" align="right"><? echo number_format($recv_qty,2); ?></td>
										<td width="90" align="right"><? echo number_format($iss_ret_qty,2); ?></td>
										<td width="90" align="right"><? echo number_format($trans_in_qty,2); ?></td>
										<td width="90" align="right"><? echo number_format($recv_tot_qty,2); ?></td>
										<td width="90" align="right"><? echo number_format($iss_qty,2); ?></p></td>
										<td width="90" align="right"><? echo number_format($recv_ret_qty,2); ?></p></td>
										<td width="90" align="right"><? echo number_format($trans_out_qty,2); ?></td>
										<td width="90" align="right"><? echo number_format($iss_tot_qty,2); ?></td>
										<td align="right"><? echo number_format($stock_qty,2); ?></td>
										<?
										//$daysOnHand=datediff("d",change_date_format($transaction_date_array[$prodId]['max_date'],'','',1),date("Y-m-d"));
										//if($stock_qty>0) echo $daysOnHand; ?>
									</tr>
									
									<?
									$i++;

									$booking_req_qty+=$row[csf('grey_fab_qnty')];
									$order_recv_qty+=$recv_qty;
									$order_iss_ret_qty+=$iss_ret_qty;  
									$order_iss_qty+=$iss_qty;
									$order_rec_ret_qty+=$recv_ret_qty; 
									$order_trans_in_qty+=$trans_in_qty; 
									$order_trans_out_qty+=$trans_out_qty; 
									$order_tot_recv_qnty+=$recv_tot_qty; 
									$order_tot_iss_qnty+=$iss_tot_qty;
									$order_stock_qnty+=$stock_qty;									
									
									$tot_recv_qty+=$recv_qty; 
									$tot_iss_ret_qty+=$iss_ret_qty; 
									$tot_iss_qty+=$iss_qty; 
									$tot_rec_ret_qty+=$recv_ret_qty; 
									$tot_trans_in_qty+=$trans_in_qty; 
									$tot_trans_out_qty+=$trans_out_qty; 
									$grand_tot_recv_qty+=$recv_tot_qty; 
									$grand_tot_iss_qty+=$iss_tot_qty;
									$grand_stock_qty+=$stock_qty;	
								}								
							//}
						//}
					}
					?>
					<tfoot>
						<tr>
                            <th colspan="<? if($company_cond == ""){echo "7";}else {echo "8";} ?>" align="right"><b>Grand Total</b></th>
                            <th align="right"><? echo number_format($booking_req_qty,2,'.',''); ?></th>
                            <th align="right">&nbsp;</th>
                            <th align="right">&nbsp;</th>
                            <th align="right"><? echo number_format($tot_recv_qty,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($tot_iss_ret_qty,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($tot_trans_in_qty,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($grand_tot_recv_qty,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($tot_iss_qty,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($tot_rec_ret_qty,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($tot_trans_out_qty,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($grand_tot_iss_qty,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($grand_stock_qty,2,'.',''); ?></th>
                            
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}

    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename####$rpt_type"; 
    exit();
}

if($action=="fabric_booking_popup")
{
 	echo load_html_head_contents("Fabric Booking Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
?>
	<fieldset style="width:890px">
        <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
            <thead>
                <th width="40">SL</th>
                <th width="60">Booking No</th>
                <th width="50">Year</th>
                <th width="60">Type</th>
                <th width="80">Booking Date</th>
                <th width="90">Color</th>
                <th width="110">Fabric</th>
                <th width="150">Composition</th>
                <th width="70">GSM</th>
                <th width="70">Dia</th>
                <th>Grey Req. Qty.</th>
            </thead>
        </table>
        <div style="width:100%; max-height:320px; overflow-y:scroll">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
			<?
                if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
                else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
                else $year_field="";//defined Later
				
				$i=1; $tot_grey_qnty=0;
                $sql="select a.id, $year_field, a.booking_no_prefix_num, a.booking_type, a.is_short, a.booking_date, b.fabric_color_id, b.construction, b.copmposition, b.gsm_weight, b.dia_width, sum(b.grey_fab_qnty) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id) and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 group by a.id, a.booking_type, a.is_short, a.booking_date, a.insert_date, a.booking_no_prefix_num, b.fabric_color_id, b.construction, b.copmposition, b.gsm_weight, b.dia_width order by a.id";
               //echo $sql;//die;
                $result= sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					
					if($row[csf('booking_type')]==4) 
					{
						$booking_type="Sample";
					}
					else
					{
						if($row[csf('is_short')]==1) $booking_type="Short"; else $booking_type="Main"; 
					}
               	?>
                   <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="60">&nbsp;&nbsp;&nbsp;<? echo $row[csf('booking_no_prefix_num')]; ?></p></td>
                        <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                        <td width="60" align="center"><p><? echo $booking_type; ?></p></td>
                        <td width="80" align="center"><? if($row[csf('booking_date')]!="0000-00-00") echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</td>
                        <td width="90"><p><? echo $color_arr[$row[csf('fabric_color_id')]]; ?></p></td>
                        <td width="110"><p><? echo $row[csf('construction')]; ?></p></td>
                        <td width="150"><p><? echo $row[csf('copmposition')]; ?></p></td>
                        <td width="70"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
                        <td width="70"><p><? echo $row[csf('dia_width')]; ?></p></td>
                        <td align="right" style="padding-right:5px"><? echo number_format($row[csf('grey_fab_qnty')],2); ?></td>
                    </tr>
                <? 
					$tot_grey_qnty+=$row[csf('grey_fab_qnty')];
					$i++;
                } 
            ?>
            	<tfoot>
                	<th colspan="10">Total</th>
                    <th style="padding-right:5px"><? echo number_format($tot_grey_qnty,2); ?></th>
                </tfoot>
			</table>
		</div> 
    </fieldset>
<?
exit();
}


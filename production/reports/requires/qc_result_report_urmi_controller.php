<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$knit_defect_inchi_array2=array(1=>"Select",2=>"Present",3=>"Not Found",4=>"Major",5=>"Minor",6=>"Acceptable",7=>"Good");
$knit_defect_array2=array(1=>"Fly Conta",2=>"PP conta",3=>"Patta/Barrie",4=>"Needle Mark",5=>"Sinker Mark",6=>"thick-thin",7=>"neps/knot",8=>"white speck",9=>"Black Speck",10=>"Star Mark",11=>"Dia/Edge Mark",12=>"Dead fibre",13=>"Running shade",14=>"Hairiness",15=>"crease mark",16=>"Uneven",17=>"Padder Crease",18=>"Absorbency",19=>"Bowing",20=>"Handfeel",21=>"Dia Up-down",22=>"Cut hole",23=>"Snagging/Pull out",24=>"Pin Hole",25=>"Bad Smell",26=>"Bend Mark");

if($action=="system_id_popup")
{
	echo load_html_head_contents("System Id Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array; var selected_name = new Array;
		function check_all_data()
		{

			var tbl_row_count = document.getElementById( 'tbl_list' ).rows.length;			 
			for( var i = 1; i <= tbl_row_count; i++ )
			{
				
				$('#tr_'+i).trigger('click'); 
			}
		}
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) 
		{
			//alert(str);
			if (str!="") str=str.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_system_id').val( id );
			$('#hide_system_no').val( name );
		}
    </script>
	</head>
	<body>
	<div align="center">
		<form name="" id="">
			<fieldset style="width:760px;">
			 <input type="hidden" name="hide_system_no" id="hide_system_no" value="" />
	          <input type="hidden" name="hide_system_id" id="hide_system_id" value="" />
	             
	            <div style="margin-top:15px" id="search_div">
	            	<?

	            	$arr=array(1=>$color_library );
	            	$sqls="SELECT a.id, c.batch_no, a.recv_number ,a.recv_number_prefix_num, b.color_id from inv_receive_master a ,pro_finish_fabric_rcv_dtls b ,pro_batch_create_mst c   where   a.id=b.mst_id and b.batch_id=c.id    and c.batch_no='$batch_no' and a.entry_form=7  and  a.status_active=1 and b.status_active=1 and c.status_active=1 group by a.id, c.batch_no, a.recv_number , b.color_id,a.recv_number_prefix_num  order by a.id   ";
	            	echo  create_list_view("tbl_list", "System No,Color,Batch No ", "150,100,150,","500","350",0, $sqls, "js_set_value", "id,recv_number_prefix_num", "", 1, "0,color_id,0,0", $arr , "recv_number,color_id,batch_no", "",'setFilterGrid(\'list_view\',-1);','0','',1) ;
	            	?>
	            </div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}
 

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));	
	$all_cond=""; 
	if(str_replace("'", "",$cbo_company_name)) $all_cond.=" and a.knitting_company=$cbo_company_name";
	if(str_replace("'", "",$txt_batch_no)) $all_cond.=" and c.batch_no=$txt_batch_no";
	if(str_replace("'", "",$txt_system_hidden_id)) $all_cond.=" and a.id in(".str_replace("'", "", $txt_system_hidden_id).")";

	$lib_defect_name=return_library_array( "SELECT DEFECT_NAME, short_name from lib_defect_name", "DEFECT_NAME", "short_name"  ); 	
	$company_arr=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  ); 	
	$color_library=return_library_array( "SELECT id, color_name from lib_color", "id", "color_name"  );
	$buyer_library=return_library_array( "SELECT id, buyer_name from lib_buyer", "id", "buyer_name"  );
	//and (e.form_type is null or e.form_type=0) OFF issue id 11564
	$main_sql="SELECT b.receive_qnty, b.reject_qty, b.no_of_roll, d.length_percent, d.width_percent, d.twisting_percent,b.prod_id, b.order_id, b.buyer_id, b.id as pro_dtls, d.actual_dia,d.actual_gsm,c.booking_no, c.batch_no, b.fabric_description_id,a.id as sys_id,a.recv_number_prefix_num,b.original_gsm,b.gsm,b.original_width,b.width,b.batch_id,b.color_id,d.roll_no,d.roll_width,d.roll_weight,d.roll_length,d.total_penalty_point,d.total_point,d.fabric_grade,d.comments, d.id as qc_mst_id from inv_receive_master a ,pro_finish_fabric_rcv_dtls b ,pro_batch_create_mst c ,pro_qc_result_mst d ,pro_qc_result_dtls e where   a.id=b.mst_id and b.batch_id=c.id and b.id=d.pro_dtls_id and d.id=e.mst_id  and  a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and a.entry_form = 7  $all_cond group by  b.receive_qnty, b.reject_qty, b.no_of_roll,d.length_percent, d.width_percent, d.twisting_percent,b.prod_id,b.order_id, b.buyer_id, b.id,d.actual_dia,d.actual_gsm,c.booking_no, c.batch_no, b.fabric_description_id, a.id ,a.recv_number_prefix_num, b.original_gsm, b.original_width, b.gsm,b.width,b.batch_id,b.color_id,d.roll_no,d.roll_width,d.roll_weight,d.roll_length,d.total_penalty_point,d.total_point,d.fabric_grade,d.comments, d.id order by a.id, d.roll_no";	
	$def_array=array();
	$mst_id_arr=array();
	$data_arr=array();
	$all_wo_po=array();
	$all_product_id_arr=array();
	$all_comments_arr=array();
	$roll_wise_shr=array();
	$inspected_qty=0;
	$reject_qty=0;
	$no_of_roll=0;
	$fab_dtls_tbl=array();
	$booking_no_arr=array();
	 
	foreach(sql_select($main_sql) as $v)
	{
		if(!in_array($v[csf("pro_dtls")], $fab_dtls_tbl))
		{
			$inspected_qty+=$v[csf("receive_qnty")];
			$reject_qty+=$v[csf("reject_qty")];
			$no_of_roll+=$v[csf("no_of_roll")];
			$fab_dtls_tbl[$v[csf("pro_dtls")]]=$v[csf("pro_dtls")];
		}
		$batch_id=$v[csf("batch_id")];		 
		$booking_no_arr[$v[csf("booking_no")]]=$v[csf("booking_no")];
		$all_product_id_arr[$v[csf("prod_id")]]=$v[csf("prod_id")];

		$roll_wise_shr[$v[csf("qc_mst_id")]][$v[csf("roll_no")]]["length_percent"]=$v[csf("length_percent")];
		$roll_wise_shr[$v[csf("qc_mst_id")]][$v[csf("roll_no")]]["width_percent"]=$v[csf("width_percent")];
		$roll_wise_shr[$v[csf("qc_mst_id")]][$v[csf("roll_no")]]["twisting_percent"]=$v[csf("twisting_percent")];
		$roll_wise_shr[$v[csf("qc_mst_id")]][$v[csf("roll_no")]]["roll_no"]=$v[csf("roll_no")];

		$mst_id_arr[$v[csf("qc_mst_id")]]=$v[csf("qc_mst_id")];
		$all_wo_po[$v[csf("order_id")]]=$v[csf("order_id")];
		$dtls_index=$v[csf("color_id")]."**".$v[csf("fabric_description_id")]."**".$v[csf("gsm")]."**".$v[csf("width")] ;
		$qc_mst_index=$v[csf("roll_no")]."**".$v[csf("roll_width")]."**".$v[csf("roll_weight")]."**".$v[csf("roll_length")]."**".$v[csf("total_penalty_point")]."**".$v[csf("total_point")]."**".$v[csf("fabric_grade")]."**".$v[csf("defect_name")];
		$data_arr[$v[csf("batch_id")]][$v[csf("recv_number_prefix_num")]][$dtls_index][$qc_mst_index]["batch_no"]=$v[csf("batch_no")];

		$data_arr[$v[csf("batch_id")]][$v[csf("recv_number_prefix_num")]][$dtls_index][$qc_mst_index]["buyer_id"]=$v[csf("buyer_id")];

		$data_arr[$v[csf("batch_id")]][$v[csf("recv_number_prefix_num")]][$dtls_index][$qc_mst_index]["prod_id"]=$v[csf("prod_id")];

		$data_arr[$v[csf("batch_id")]][$v[csf("recv_number_prefix_num")]][$dtls_index][$qc_mst_index]["actual_dia"]=$v[csf("actual_dia")];

		$data_arr[$v[csf("batch_id")]][$v[csf("recv_number_prefix_num")]][$dtls_index][$qc_mst_index]["actual_gsm"]=$v[csf("actual_gsm")];

		$data_arr[$v[csf("batch_id")]][$v[csf("recv_number_prefix_num")]][$dtls_index][$qc_mst_index]["fabric_description_id"]=$v[csf("fabric_description_id")];

		$data_arr[$v[csf("batch_id")]][$v[csf("recv_number_prefix_num")]][$dtls_index][$qc_mst_index]["gsm"]=$v[csf("gsm")];
		$data_arr[$v[csf("batch_id")]][$v[csf("recv_number_prefix_num")]][$dtls_index][$qc_mst_index]["original_gsm"]=$v[csf("original_gsm")];

		$data_arr[$v[csf("batch_id")]][$v[csf("recv_number_prefix_num")]][$dtls_index][$qc_mst_index]["width"]=$v[csf("width")];
		$data_arr[$v[csf("batch_id")]][$v[csf("recv_number_prefix_num")]][$dtls_index][$qc_mst_index]["original_width"]=$v[csf("original_width")];

		$data_arr[$v[csf("batch_id")]][$v[csf("recv_number_prefix_num")]][$dtls_index][$qc_mst_index]["color_id"]=$color_library[$v[csf("color_id")]];

		$data_arr[$v[csf("batch_id")]][$v[csf("recv_number_prefix_num")]][$dtls_index][$qc_mst_index]["roll_no"]=$v[csf("roll_no")];

		$data_arr[$v[csf("batch_id")]][$v[csf("recv_number_prefix_num")]][$dtls_index][$qc_mst_index]["roll_width"]=$v[csf("roll_width")];

		$data_arr[$v[csf("batch_id")]][$v[csf("recv_number_prefix_num")]][$dtls_index][$qc_mst_index]["roll_weight"]=$v[csf("roll_weight")];

		$data_arr[$v[csf("batch_id")]][$v[csf("recv_number_prefix_num")]][$dtls_index][$qc_mst_index]["roll_length"]=$v[csf("roll_length")];

		$data_arr[$v[csf("batch_id")]][$v[csf("recv_number_prefix_num")]][$dtls_index][$qc_mst_index]["total_penalty_point"]=$v[csf("total_penalty_point")];

		$data_arr[$v[csf("batch_id")]][$v[csf("recv_number_prefix_num")]][$dtls_index][$qc_mst_index]["total_point"]=$v[csf("total_point")];

		$data_arr[$v[csf("batch_id")]][$v[csf("recv_number_prefix_num")]][$dtls_index][$qc_mst_index]["fabric_grade"]=$v[csf("fabric_grade")];

		$data_arr[$v[csf("batch_id")]][$v[csf("recv_number_prefix_num")]][$dtls_index][$qc_mst_index]["comments"]=$v[csf("comments")];

		$data_arr[$v[csf("batch_id")]][$v[csf("recv_number_prefix_num")]][$dtls_index][$qc_mst_index]["pro_dtls"]=$v[csf("pro_dtls")];

		$data_arr[$v[csf("batch_id")]][$v[csf("recv_number_prefix_num")]][$dtls_index][$qc_mst_index]["defect_name"]=$v[csf("defect_name")];    
		$data_arr[$v[csf("batch_id")]][$v[csf("recv_number_prefix_num")]][$dtls_index][$qc_mst_index]["qc_mst_id"]=$v[csf("qc_mst_id")];   

	}
	//print_r($roll_wise_shr); 
	$qc_mst_ids=implode(",",$mst_id_arr);
	if(!$qc_mst_ids)$qc_mst_ids=0;

	$all_book_nos="'".implode("','",$booking_no_arr)."'";
	if(!$all_book_nos) $all_book_nos='0';

	  $fso_sql="SELECT sales_booking_no,job_no,within_group,style_ref_no from fabric_sales_order_mst where   status_active=1   and sales_booking_no in($all_book_nos) ";
	foreach(sql_select($fso_sql) as $val)
	{
		$fso_book[$val[csf("sales_booking_no")]]["book"]= $val[csf("job_no")] ;
		$fso_book[$val[csf("sales_booking_no")]]["group"]= $val[csf("within_group")] ;
		$fso_book[$val[csf("sales_booking_no")]]["style"]= $val[csf("style_ref_no")] ;
	}
	  



	$qc_sub_part="SELECT a.id ,a.roll_no, b.defect_name, b.found_in_inch from pro_qc_result_mst a,pro_qc_result_dtls b where a.id=b.mst_id and a.status_active=1 and  b.form_type=2 and b.status_active=1 and b.found_in_inch<>1 and b.mst_id in($qc_mst_ids) order by a.id asc ";
	 
	foreach(sql_select($qc_sub_part) as $val)
	{
		if($roll_wise_comments[$val[csf("roll_no")]]=="")
		{

		 $roll_wise_comments[$val[csf("roll_no")]].=$knit_defect_array2[$val[csf("defect_name")]]." ". $knit_defect_inchi_array2[$val[csf("found_in_inch")]];
		}
		else
		{
			$roll_wise_comments[$val[csf("roll_no")]].=','.$knit_defect_array2[$val[csf("defect_name")]]." ". $knit_defect_inchi_array2[$val[csf("found_in_inch")]];
		}
	}
	//print_r($roll_wise_comments);
	$all_comments=implode(",",$all_comments_arr);
	$wo_po_ids=implode(",",$all_wo_po);
	if(!$wo_po_ids)$wo_po_ids=0;

	$all_product_ids=implode(",",$all_product_id_arr);
	if(!$all_product_ids)$all_product_ids=0;

	$product_library=return_library_array( "SELECT id, item_description from product_details_master where id in($all_product_ids)", "id", "item_description"  );


	$style_ref_no= return_field_value("style_ref_no","wo_po_details_master a,wo_po_break_down b"," a.job_no=b.job_no_mst and  b.id in($wo_po_ids) and b.status_active=1 ");

	$booking_nos= return_field_value("booking_no","pro_batch_create_mst"," id in($batch_id) and status_active=1 ");

	/*$dia_gsm_sql="SELECT a.batch_no, b.item_description, b.body_part_id FROM pro_batch_create_mst a, pro_batch_create_dtls b WHERE a.id=b.mst_id and a.batch_no=$txt_batch_no and a.company_id=$cbo_company_name";

	$dia_gsm_result = sql_select($dia_gsm_sql);
	$dia_gsm_arr=array();
	foreach ($dia_gsm_result as $row) 
	{
		$desc = explode(",", $row[csf('item_description')]);
		// print_r($desc);
		//echo $desc[2];
		$dia_gsm_arr[$row[csf("batch_no")]][$row[csf("body_part_id")]]=$desc[2];
	}*/
	/*echo "<pre>";
	print_r($dia_gsm_arr);*/

	$row_span_sql="SELECT e.penalty_point, b.id as pro_dtls, d.actual_dia,d.actual_gsm, c.batch_no, b.fabric_description_id,a.id as sys_id,a.recv_number_prefix_num,b.gsm,b.width,b.batch_id,b.color_id,d.roll_no,d.roll_width,d.roll_weight,d.roll_length,d.total_penalty_point,d.total_point,d.fabric_grade,d.comments, d.id as qc_mst_id,e.defect_name from inv_receive_master a ,pro_finish_fabric_rcv_dtls b ,pro_batch_create_mst c ,pro_qc_result_mst d ,pro_qc_result_dtls e where    a.id=b.mst_id and b.batch_id=c.id and b.id=d.pro_dtls_id and d.id=e.mst_id  and  a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and a.entry_form = 7 and (e.form_type is null or e.form_type=0) and e.mst_id in($qc_mst_ids)  group by e.penalty_point, b.id ,d.actual_dia,d.actual_gsm, c.batch_no, b.fabric_description_id, a.id ,a.recv_number_prefix_num,b.gsm,b.width,b.batch_id,b.color_id,d.roll_no,d.roll_width,d.roll_weight,d.roll_length,d.total_penalty_point,d.total_point,d.fabric_grade,d.comments, d.id,e.defect_name";	
	$roll_wise_qnty_arr=array();
	$defect_wise_qnty_arr=array();
	foreach(sql_select($row_span_sql) as $v)
	{
		if($v[csf("defect_name")]>0){
			$def_array[$v[csf("defect_name")]]=$v[csf("defect_name")];
		}
		
		$dtls_index=$v[csf("color_id")]."**".$v[csf("fabric_description_id")]."**".$v[csf("gsm")]."**".$v[csf("width")] ;
		$qc_mst_index=$v[csf("roll_no")]."**".$v[csf("roll_width")]."**".$v[csf("roll_weight")]."**".$v[csf("roll_length")]."**".$v[csf("total_penalty_point")]."**".$v[csf("total_point")]."**".$v[csf("fabric_grade")]."**".$v[csf("defect_name")];
		$data_arr2[$v[csf("batch_id")]][$v[csf("recv_number_prefix_num")]][$dtls_index][$qc_mst_index]["defect_name"]=$v[csf("defect_name")]; 
		  

		$roll_wise_qnty_arr[$v[csf("recv_number_prefix_num")]][$v[csf("batch_id")]][$v[csf("pro_dtls")]][$v[csf("qc_mst_id")]]["count"]+=1; 

		$roll_wise_qnty_arr[$v[csf("recv_number_prefix_num")]][$v[csf("batch_id")]][$v[csf("pro_dtls")]][$v[csf("qc_mst_id")]]["qnty"]+=$v[csf("penalty_point")]; 

		$defect_wise_qnty_arr[$v[csf("recv_number_prefix_num")]][$v[csf("batch_id")]][$v[csf("pro_dtls")]][$v[csf("qc_mst_id")]][$v[csf("defect_name")]]["qnty"]+=$v[csf("penalty_point")]; 

	}
	//print_r($defect_wise_qnty_arr);
	$row_span_arr=array();
	foreach($data_arr as $batch_id=>$sys_data)
	{
		$row_span=0;
		foreach($sys_data as $sys_id=>$qc_mst_data)
		{
			
			foreach($qc_mst_data as $ms_id=>$qc_dtls_data)
			{
				foreach($qc_dtls_data as $dt_id=>$row)
				{
					$row_span++;
				}
			}
			
		}
		$row_span_arr[$batch_id]=$row_span;
	}
	 //print_r($row_span_arr); 

	  $tbl_wid=1390+(count($def_array)*50);
		ob_start();
		?>
		<br>
		<br>
		<div style="width:<? echo $tbl_wid+20;?>px">		
			<table cellspacing="0" cellpadding="0"  border="0" rules=""  width="<? echo $tbl_wid;?>" class="" align="left">
				<tr>
					<td colspan="10" align="center"><strong> <? $cmp=str_replace("'","",$cbo_company_name); echo $company_arr[$cmp]; ?></strong></td>

				</tr>
				<tr style="margin-top: -20px;">
					<td colspan="10" align="center"   >
					<strong>
						<?
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cmp");
						$company_address="";
						foreach ($nameArray as $result)
						{
							?>
							<? if($result[csf('plot_no')]!="") $company_address.= $result[csf('plot_no')].", "; ?>
							<? if($result[csf('level_no')]!="") $company_address.= $result[csf('level_no')].", ";?>
							<? if($result[csf('road_no')]!="") $company_address.= $result[csf('road_no')].", "; ?>
							<? if($result[csf('block_no')]!="") $company_address.= $result[csf('block_no')].", ";?>
							<? if($result[csf('city')]!="") $company_address.= $result[csf('city')].", ";?>
							<? if($result[csf('zip_code')]!="") $company_address.= $result[csf('zip_code')].", "; ?>
							<? if($result[csf('province')]!="") $company_address.= $result[csf('province')];?>
							<? if($result[csf('country_id')]!=0) $company_address.= $country_arr[$result[csf('country_id')]].", "; ?><br>
							<? if($result[csf('email')]!="") $company_address.= $result[csf('email')].", ";?>
							<? if($result[csf('website')]!="") $company_address.= $result[csf('website')];
						}
						$company_address=chop($company_address," , ");
						echo $company_address;
						?>
						</strong>
					</td>
				</tr>
				<tr>
					<td height="10"></td>
				</tr>
			</table>	
			<table cellspacing="0" cellpadding="0"  border="1" rules="all"  width="<? echo $tbl_wid;?>" class="rpt_table" align="left">
				<thead>
					<tr>
						<th style="word-break: break-all;word-wrap: break-word;" width="150">Order Details No</th>
						<th  style="word-break: break-all;word-wrap: break-word;"  width="100">Fabric Type</th>
						<th  style="word-break: break-all;word-wrap: break-word;"  width="100">Color</th>
						<th  style="word-break: break-all;word-wrap: break-word;"  width="80">Check No</th>
						<th  style="word-break: break-all;word-wrap: break-word;"  width="80">Roll No</th>
						<th  style="word-break: break-all;word-wrap: break-word;"  width="80">Ac. Width</th>
						<th  style="word-break: break-all;word-wrap: break-word;"  width="80">Req. Width</th>
						<th  style="word-break: break-all;word-wrap: break-word;"  width="80">Ac. GSM</th>
						<th  style="word-break: break-all;word-wrap: break-word;"  width="80">Req. GSM</th>
						<th  style="word-break: break-all;word-wrap: break-word;"  width="80">Weight(Kg)</th>
						<th  style="word-break: break-all;word-wrap: break-word;"  width="80">Length(Yds)</th>
						<th  style="word-break: break-all;word-wrap: break-word;"  width="80">Defect</th>
						<?
						foreach($def_array as $id=>$vals)
						{
							?>
								<th  style="word-break: break-all;word-wrap: break-word;"  width="50"><? echo $lib_defect_name[$id]; ?></th>

							<?
						}


						?>
						<th  style="word-break: break-all;word-wrap: break-word;"  width="80">No Of Defects</th>
						<th  style="word-break: break-all;word-wrap: break-word;"  width="80">TTL Def. Point</th>
						<th  style="word-break: break-all;word-wrap: break-word;"  width="80" title="Total penalty point*3600/(Roll weight kg * Roll Weight yds)">Avg. Point</th>
						<th  style="word-break: break-all;word-wrap: break-word;"  width="80">Grade</th>
					</tr>
				</thead>
			</table>
			<div style="max-height:425px; overflow-y:scroll; width:<? echo $tbl_wid+20;?>px;" id="scroll_body">
				<table  border="1" class="rpt_table"  width="<? echo $tbl_wid;?>" rules="all" id="table_body" >
					<tbody>
						<?
						$kk=1;
						$roll_no_count=0;
						$def_qnty_summary=array();
						foreach($data_arr as $batch_id=>$sys_data)
						{
							$pp=0;
							foreach($sys_data as $sys_id=>$qc_mst_data)
							{
								
								foreach($qc_mst_data as $ms_id=>$qc_dtls_data)
								{
									foreach($qc_dtls_data as $dt_id=>$row)
									{
										$roll_no_count++;
										?>
										<tr valign="middle"> 
											<?
											if($pp==0)
											{
												//if($fso_book[$booking_nos]["group"]==2)
												//{
													$style_ref_no=$fso_book[$booking_nos]["style"];
												//}
												?>
												<td  style="word-break: break-all;word-wrap: break-word;"   valign="middle"  align="center" rowspan="<? echo $row_span_arr[$batch_id]; ?>"   width="150"><? echo "<b>Batch :</b> ".$row["batch_no"]."<br>"."<b>Buyer :</b> ".$buyer_library[$row["buyer_id"]]."<br>"."<b>Style:</b> ".$style_ref_no."<br>"."<b>FSO : </b>".$fso_book[$booking_nos]["book"]."<br>"."<b>FB : </b>".$booking_nos;?></td>

												<?
												$pp++;
											}
											?>
											
											<td  style="word-break: break-all;word-wrap: break-word;"   valign="middle"  align="center"  width="100"><? echo $product_library[$row["prod_id"]]; ?></td>
											<td  style="word-break: break-all;word-wrap: break-word;"   valign="middle"  align="center"  width="100"><? echo $row["color_id"];?></td>
											<td  style="word-break: break-all;word-wrap: break-word;"   valign="middle"  align="center"  width="80"><? echo $sys_id;?></td>
											<td  style="word-break: break-all;word-wrap: break-word;"   valign="middle"  align="center"  width="80"><? echo $row["roll_no"];?></td>
											<td  style="word-break: break-all;word-wrap: break-word;"   valign="middle"  align="center"  width="80"><? echo $actual_dia= $row["actual_dia"];?></td>
											<td  style="word-break: break-all;word-wrap: break-word;"   valign="middle"  align="center"  width="80"><? echo $row["original_width"];?></td>
											<td  style="word-break: break-all;word-wrap: break-word;"   valign="middle"  align="center"  width="80"><? echo $row["actual_gsm"];?></td>
											<td  style="word-break: break-all;word-wrap: break-word;"   valign="middle"  align="center"  width="80"><? echo $row["original_gsm"];?></td>
											<td  style="word-break: break-all;word-wrap: break-word;"    valign="middle" align="center"  width="80"><? echo $roll_weight=$row["roll_weight"];?></td>
											<td  style="word-break: break-all;word-wrap: break-word;"   valign="middle" align="center"  width="80"><? echo $roll_length=$row["roll_length"];?></td>
											<td  style="word-break: break-all;word-wrap: break-word;"   valign="middle"  align="center"  width="80">Deduct Point</td>
											<?
											foreach($def_array as $id=>$vals)
											{

												?>
												<td   style="word-break: break-all;word-wrap: break-word;"   valign="middle" align="center"  width="50"><? echo $qntys= $defect_wise_qnty_arr[$sys_id][$batch_id][$row["pro_dtls"]][$row["qc_mst_id"]][$id]["qnty"]; ?></td>

												<?
												$def_qnty_summary[$id]+=$qntys;
											}


											?>
											<td  style="word-break: break-all;word-wrap: break-word;"   valign="middle"  align="center"  width="80"><? echo $roll_wise_qnty_arr[$sys_id][$batch_id][$row["pro_dtls"]][$row["qc_mst_id"]]["count"]; ?></td>
											<td  style="word-break: break-all;word-wrap: break-word;"   valign="middle"  align="center"  width="80"><? echo $ttl_point= $roll_wise_qnty_arr[$sys_id][$batch_id][$row["pro_dtls"]][$row["qc_mst_id"]]["qnty"]; ?></td>
											 
											 
											<td   style="word-break: break-all;word-wrap: break-word;"  valign="middle"  align="center"  width="80">
												<? 
												echo number_format(($ttl_point*3600)/($roll_length*$actual_dia),2);
												//echo number_format(($ttl_point*3600)/($roll_weight*$row["roll_length"]),2);
												?></td>
											<td  style="word-break: break-all;word-wrap: break-word;"   valign="middle"  align="center"  width="80"><? echo $row["fabric_grade"];?></td>
										</tr>


										<?

									}
								}
							}
						}


						?>
					</tbody>
				</table>
			</div>

		
		<br> 
		<table align="left" style="background: #D8D8D8;font-weight: bold;" cellspacing="0" cellpadding="0" border="1" rules="all" width="1200" class="rpt_table" >
			<tr>
				<td width="200"><strong>&nbsp;Penalty Points Legends :</strong></td>
				<td width="400" colspan="5" align="right"><strong>&nbsp;Faulty Appearance:</strong></td>
				<td width="400" colspan="2"><strong>&nbsp;Four-Points System :</strong></td>
				<td width="200"><strong>&nbsp;Acceptance Point RA (Ind. Roll)</strong></td> 
			</tr>
			<tr>
				<td>D=Dirty Spot</td>
				<td colspan="2">H=Hole</td>
				<td colspan="2">HR=Hairiness</td>
				<td  >MY=Missing Yarn</td>
				<td>Size of Defect</td>
				<td>Penalty</td>
				<td></td>
			</tr>
			<tr>
				<td>DS=Dye spot</td>
				<td colspan="2">W=Water Spot</td>
				<td colspan="2">LO=Lycra Out</td>
				<td  >BR=Barrie</td>
				<td>3 inches or Less</td>
				<td>1 Point</td>
				<td>UP to 20 Points = A</td>
			</tr>

			<tr>
				<td>IS=Insect Spot</td>
				<td colspan="2">YC=Yarn Conta.	</td>
				<td colspan="2">N=Needle line</td>
				<td  >BW=Bowing	</td>
				<td>Over 3, but not over 6</td>
				<td>2 Point</td>
				<td>21 to 28 Points         = B	</td>
			</tr>


			<tr>
				<td>OS=Oil spot</td>
				<td colspan="2">L=Loop	</td>
				<td colspan="2">NP=Neps	</td>
				<td  >STM=Stop mark	</td>
				<td>Over 6, but not over 9</td>
				<td>3 Point</td>
				<td>ABOVE  28 Points  = REJECT ( R)</td>
			</tr>



			<tr>
				<td>SL = Slub</td>
				<td colspan="2">FR=Friction mark</td>
				<td colspan="2">P=Patches</td>
				<td  >CB=Compacting broken	</td>
				<td>Over 9 Inches</td>
				<td>4 Point</td>
				<td></td>
			</tr>


			<tr>
				<td>SM=Slinker mark</td>
				<td colspan="2">SP=Softener spot</td>
				<td colspan="2">RS=Running Shade</td>
				<td  >SS=Selvegde broken</td>
				<td>Hole <1 inch	</td>
				<td>2 Point</td>
				<td></td>
			</tr>

			<tr>
				<td>RS = Rust stail</td>
				<td colspan="2">CR=Crease</td>
				<td colspan="2">PT=Patta</td>
				<td  >PO=Pin out</td>
				<td>Hole>2Inches</td>
				<td>4 Point</td>
				<td></td>
			</tr>
		</table>
		<br> 
		<br>
		<br>

		<table align="left" width="1150" style="margin-top: 30px;">
			<tr>
				<td valign="top">
					<table width="380" border="1" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
						<thead>
							<tr>
								<th><strong>QC Comments</strong></th>
							</tr>

						</thead>
						<tbody>

							<?
							foreach($roll_wise_comments as $key=> $val)
							{
								  


										?>
										<tr>
											<td  align="left"><? echo "Roll No. ".$key." ".$val;?></td>
											 
										</tr>

										<? 
							}

							?>
						</tbody>
						
						
					</table>
				</td>
				<td  valign="top" width="40">&nbsp;</td>

				<td valign="top">
					<table width="220" border="1" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
						<thead>
							<tr>
								<th colspan="4"><strong>Shrinkage %</strong></th>
							</tr>
							<tr>
								<th>Roll No.</th>
								<th>Length(%)</th>
								<th>Width(%)</th>
								<th>Twisting(%)</th>
							</tr>

						</thead>
						<tbody>

							<?
							foreach($roll_wise_shr as $id=>$data)
							{
								foreach($data as $v)
								{


									if($v["length_percent"] || $v["width_percent"] || $v["twisting_percent"]  )
									{


										?>
										<tr>
											<td  align="center"><? echo $v["roll_no"];?></td>
											<td  align="center"><? echo $v["length_percent"];?></td>
											<td align="center"><? echo $v["width_percent"];?></td>
											<td align="center"><? echo $v["twisting_percent"];?></td>
										</tr>

										<?
									}
								}
							}

							?>
						</tbody>
					</table>
				</td>
				<td  valign="top" width="40">&nbsp;</td>

				<td  valign="top">
					<table width="500" border="1" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
						<thead>
							<tr>
								<th colspan="<? echo count($def_array)+1;?>"><strong>Summary</strong></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td align="center"><b>Defect Name</b></td>
								<?
								foreach($def_array as $id=>$vals)
								{
									?>
									<td width="50"><? echo $lib_defect_name[$id]; ?></td>

									<?
								}

								?>
								
							</tr>

							<tr>
								<td align="center"><b>Total Defect Point</b></td>
								<?
								foreach($def_array as $id=>$vals)
								{
									?>
									<td width="50"><? echo $def_qnty_summary[$id]; ?></td>

									<?
								}

								?>
								
							</tr>
							<tr>
								<td align="center"><b>No. Of Roll</b></td>
								<td colspan="<? echo count($def_array);?>"><? echo $roll_no_count; ?></td>
							</tr>

						</tbody>
						 
					</table>
				</td>
				<td  valign="top" width="40">&nbsp;</td>

				<td  valign="top">
					<table width="130" border="1" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
						 
						<thead>
							<tr>
								<th align="center"><b style="color:crimson;">Inspected Qty</b></th>
								<th  style="color:crimson;"><? echo number_format($inspected_qty,2); ?></th>
															
							</tr>

							<tr>
								<th align="center"><b style="color:crimson;">Reject Qty</b></th>
								<th  style="color:crimson;"><? echo number_format($reject_qty,2); ?></th>
															
							</tr>
 

						</thead>

					</table>
				</td>	 
			</tr>
		</table>
		<?
			echo signature_table(67, str_replace("'", "", $cbo_company_name), "1250px");
		?>
	</div>
           
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
	
	disconnect($con);
	exit();
}

if($action=="color_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	?>
	<fieldset style="width:840px; margin-left:5px">
        <table border="1" class="rpt_table" rules="all" width="830" cellpadding="0" cellspacing="0">
            <thead>
                <th width="40">SL</th>
                <th width="100">Boby Part</th>
                <th width="80">Color Type</th>
                <th width="110">Construction</th>
                <th width="150">Composition</th>
                <th width="70">GSM</th>
                <th width="70">Fin Dia</th>
                <th width="90">Open/Tube</th>
                <th>Qnty</th>
            </thead>
         </table>
         <div style="width:830px; max-height:270px; overflow-y:scroll" id="scroll_body">
             <table border="1" class="rpt_table" rules="all" width="812" cellpadding="0" cellspacing="0">
                <?
                $i=1; $total_qnty=0;
                $sql="select c.body_part_id, c.color_type_id, c.construction, c.composition, c.gsm_weight, c.width_dia_type, a.dia_width, sum(a.grey_fab_qnty) as qnty from wo_booking_dtls a, wo_booking_mst b, wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.item_category in(2,13) and a.pre_cost_fabric_cost_dtls_id=c.id and a.job_no=c.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.po_break_down_id=$po_id and a.fabric_color_id=$color_id group by c.body_part_id, c.color_type_id, c.construction, c.composition, c.gsm_weight, c.width_dia_type, a.dia_width";
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if ($i%2==0)  
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";	
                
                    $total_qnty+=$row[csf('qnty')];
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="100"><p><? echo $body_part[$row[csf('body_part_id')]]; ?>&nbsp;</td>
                        <td width="80"><p><? echo $color_type[$row[csf('color_type_id')]]; ?>&nbsp;</p></td>
                        <td width="110"><p><? echo $row[csf('construction')]; ?>&nbsp;</p></td>
                        <td width="150"><p><? echo $row[csf('composition')]; ?>&nbsp;</p></td>
                        <td width="70"><p><? echo $row[csf('gsm_weight')]; ?>&nbsp;</p></td>
                        <td width="70"><p><? echo $row[csf('dia_width')]; ?>&nbsp;</p></td>
                        <td width="90"><p><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf('qnty')],2,'.',''); ?>&nbsp;</td>
                    </tr>
                <?
                $i++;
                }
                ?>
                <tfoot>
                    <th colspan="8" align="right">Total</th>
                    <th align="right"><? echo number_format($total_qnty,2,'.',''); ?>&nbsp;</th>
                </tfoot>
            </table>
        </div>	
	</fieldset>   
	<?
	exit();
}
?>
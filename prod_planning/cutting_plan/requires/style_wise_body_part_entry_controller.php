<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//echo $action;
//die;
function pre($array){
	echo "<pre>";
	print_r($array);
	echo "</pre>";
}
/*
|--------------------------------------------------------------------------
| Library Array
|--------------------------------------------------------------------------
|
*/
// $size_arr		  = return_library_array( "select id, size_name from lib_size",'id','size_name');
// $order_number_arr = return_library_array( "select id, po_number from   wo_po_break_down",'id','po_number'); 
// $color_arr		  = return_library_array( "select id,color_name  from  lib_color", "id", "color_name"  ); 
$buyer_arr		  = return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
$company_arr 	  = return_library_array( "select id,company_name  from  lib_company", "id", "company_name"  ); 
$location_arr	  = return_library_array( "select id,location_name  from  lib_location", "id", "location_name"  );  
if ($action	==	"load_drop_down_buyer")
{    
     $data=explode("**",$data);
	 $sql="select distinct c.id,c.buyer_name from  wo_po_break_down a,wo_po_details_master b,lib_buyer c where  a.job_no_mst=b.job_no and b.company_name=".$data[2]."  and job_no_prefix_num='".$data[0]."' and $insert_year='".$data[1]."' and b.buyer_name=c.id and a.status_active=1";
	$result=sql_select($sql);
	foreach($result as $val)  
	{
		$buyer_value=$val[csf('buyer_name')];
	}
	echo create_drop_down( "txt_buyer_name", 140,$sql, "id,buyer_name", 0, "select Buyer" ,$buyer_value);
	exit();
} 
/*
|--------------------------------------------------------------------------
| create_job_no_search_list_view
|--------------------------------------------------------------------------
|
*/
if($action	==	"create_job_no_search_list_view")
{
	$data = explode('**',$data);
	$company_id = $data[0];
	$year_id = $data[4];
	$month_id = $data[5];
	$party = $data[6];
	$popupFor = $data[7]; 
	if ($popupFor == 1) //For job
	{
		$set_column_data = 'job_no_prefix_num'; 
	} 
	if ($popupFor == 2) //For Style
	{
		$set_column_data = 'style_ref_no'; 
	}

	/*
	|--------------------------------------------------------------------------
	| buyer checking
	|--------------------------------------------------------------------------
	|
	*/
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
				$buyer_id_cond=" AND buyer_name IN (".$_SESSION['logic_erp']["buyer_id"].")";
			else
				$buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" AND buyer_name = ".$data[1]."";
	}

	$search_by = $data[2];
	$search_string = "%".trim($data[3])."%";

	if($search_by == 2)
		$search_field = "style_ref_no";
	else
		$search_field = "job_no";

	if($db_type == 0)
	{
		if($year_id != 0)
			$year_search_cond = " AND year(insert_date) = ".$year_id."";
		else
			$year_search_cond = "";
		$year_cond = "year(insert_date) AS year";
	}
	else if($db_type==2)
	{
		if($year_id != 0)
			$year_search_cond = " AND TO_CHAR(insert_date,'YYYY') = ".$year_id."";
		else
			$year_search_cond="";
		$year_cond = "TO_CHAR(insert_date,'YYYY') AS year";
	}
	
	$company_arr=return_library_array( "SELECT id, company_name FROM lib_company WHERE id IN(".$company_id.")", "id", "company_name" );
	$buyer_arr=return_library_array( "SELECT buy.id, buy.buyer_name FROM lib_buyer buy, lib_buyer_tag_company b WHERE buy.status_active =1 AND buy.is_deleted=0 AND b.buyer_id=buy.id AND b.tag_company IN (".$company_id.") AND buy.id IN (SELECT buyer_id FROM lib_buyer_party_type WHERE party_type IN (".$party.")) GROUP BY buy.id, buy.buyer_name ORDER BY buy.buyer_name","id", "buyer_name");
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "SELECT id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, ".$year_cond." FROM wo_po_details_master WHERE status_active=1 AND is_deleted=0 AND company_name IN(".$company_id.") AND ".$search_field." LIKE '".$search_string."' ".$buyer_id_cond." ".$year_search_cond." ".$month_cond." ORDER BY job_no DESC";
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,80,80,60","620","270",0, $sql , "js_set_value", "id,$set_column_data", "$popupFor", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
	exit();
} 
/*
|--------------------------------------------------------------------------
| job_no_popup
|--------------------------------------------------------------------------
|
*/
if($action	==	"job_style_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);  
	?>

	<script>
		var selected_id = new Array();
		var selected_name = new Array();
		var selected_attach_id = new Array();
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style )
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
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

		function js_set_value(id,popupFor)
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
			$('#hide_popup_for').val(popupFor);
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
                            <th id="search_by_td_up" width="170">Please Enter <?= $popupFor == 1 ? "Job No" : "Style Ref" ?></th>
                            <th>
                                <input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                                <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                                <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                                <input type="hidden" name="hide_popup_for" id="hide_popup_for" value="" />
                            </th>
                        </thead>
                        <tbody>
                            <tr>
                                <td align="center">
                                    <?php
                                    $type = 1;
                                    if($type == 1)
                                        $party="1,3,21,90";
                                    else
                                        $party="80";
										
									//is_disabled
									$is_disabled = ($buyer_name != 0 ? '1' : '0');

                                    echo create_drop_down( "cbo_buyer_name", 140, "SELECT buy.id, buy.buyer_name FROM lib_buyer buy, lib_buyer_tag_company b WHERE buy.status_active =1 AND buy.is_deleted=0 AND b.buyer_id=buy.id AND b.tag_company IN (".$companyID.") AND buy.id IN (SELECT buyer_id FROM lib_buyer_party_type WHERE party_type IN (".$party.")) GROUP BY buy.id, buy.buyer_name ORDER BY buy.buyer_name","id,buyer_name", "1", "-- All Buyer--",$buyer_name, "", $is_disabled);
                                    ?>
                                </td>
                                <td align="center">
                                    <? 
									$search_by_arr=array(1=>"Job No",2=>"Style Ref"); 
                                    $dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
                                    echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", $popupFor,$dd,0 );
                                    ?>
                                </td>
                                <td align="center" id="search_by_td">
									
                                    <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                                </td>
                                <td align="center">
                                    <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<?php echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+'<? echo $party; ?>'+'**'+'<?= $popupFor?>', 'create_job_no_search_list_view', 'search_div', 'style_wise_body_part_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="margin-top:15px" id="search_div"></div>
                </fieldset>
            </form>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
} 
/*
|--------------------------------------------------------------------------
| style wise body part popup
|--------------------------------------------------------------------------
|
*/
if($action	==	"style_wise_bodypart_popup")
{
	echo load_html_head_contents("Body Part Info","../../../", 1, 1, $unicode); 
	$data_all=$data;
	$data=explode("***",$data);
	extract($_REQUEST);   
	?>
      <script> 
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_row = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#td_' + i).attr('onclick');
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
		function set_all(old)
		{
			 
			if(old!="")
			{
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{
					js_set_value( old[k] )
				}
			}
		}

		function js_set_value( strCon )
		{
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2]; 
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( selectID, selected_id,selected_row ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
				selected_row.push( strCon );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_row.splice( i, 1 );
			}
			
			var id = ''; var name = ''; var rows = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ','; 
				rows += selected_row[i] + ','; 
			}
			
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 );
			rows 	= rows.substr( 0, rows.length - 1 ); 

			$('#hidden_body_part_id').val( id );
			$('#hidden_body_part').val( name );
			$('#hidden_body_part_row').val( rows );

		}

		function fn_onClosed()
		{ 
		 parent.emailwindow.hide();
		}
		</script>
    <?
	 
    $sql_bundle_copy="SELECT id,bundle_use_for from ppl_bundle_title where company_id=$company_id order by bundle_use_for";
	// echo $sql_bundle_copy;die;
	$res = sql_select($sql_bundle_copy);
	// pre($res );  
	?>
	<table cellspacing="0" width="380"  border="1" rules="all" class="rpt_table" >
		<thead>
			<th width="30">Sl</th>
			<th width="170">BUndle User For</th> 
		</thead>
    </table>
	<div style="width:380px; max-height:300px; overflow-y:scroll" id="scroll_body" >
		<table> 
			<tr>
				<input type='hidden' id='hidden_body_part_id' />
				<input type='hidden' id='hidden_body_part' />
				<input type='hidden' id='hidden_body_part_row' />
			</tr>
		</table>
		<table cellspacing="0" width="380"  border="1" rules="all" class="rpt_table" id="tbl_list_search" >
			
		<?

		$i=1;
			foreach($res as $row)
			{
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>"  style="cursor:pointer;">
					<td onClick="js_set_value('<? echo $i.'_'.$row[csf('id')].'_'.$row[csf('BUNDLE_USE_FOR')]; ?>')" width="30"><? echo $i;?></td>
					<td id="td_<? echo $i; ?>" onClick="js_set_value('<? echo $i.'_'.$row[csf('id')].'_'.$row[csf('BUNDLE_USE_FOR')]; ?>')" width="170">
					<? echo $row['BUNDLE_USE_FOR'];?>
					</td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<table width="280">
		<tr align="center">
			<td>
				<div align="left" style="width:50%; float:left">
					<input id="check_all" type="checkbox" onclick="check_all_data()" name="check_all">
						Check / Uncheck All
				</div>
				<div align="left" style="width:50%; float:left">
					<input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fn_onClosed()" />
				</div>
			</td>
		</tr>
	</table>
	<script>
		let old_rows = '<?= $selected_rows ?>'
		setFilterGrid("tbl_list_search",-1);
		set_all(old_rows);
	</script>
	<?
 exit();

} 
/*
|--------------------------------------------------------------------------
| Order List
|--------------------------------------------------------------------------
|
*/
if ($action == 'order_list') 
{
	// echo load_html_head_contents('Search', '../../../', 1, 1, '', '', '');
	extract($_REQUEST);
	$company_id = 	str_replace("'",'',$cbo_company_name); 
	$buyer 		= 	str_replace("'",'',$cbo_buyer_name);  
	$job 		= 	str_replace("'",'',$txt_job_no); 
	$style 		= 	str_replace("'",'',$txt_style_no); 
	$int_ref 	= 	str_replace("'",'',$txt_inter_ref); 
	$job_year 	= 	str_replace("'",'',$txt_job_year);  
	// ======================================== All Body Part  ==============================================
	$body_part_lib = return_library_array( "select id,bundle_use_for from ppl_bundle_title where company_id=$company_id", "id", "bundle_use_for"); 

	$cond = ''; 
	if ($company_id ) 	$cond .="and a.company_name = $company_id" ;
	if ($buyer)   		$cond .="and a.buyer_name=$buyer" ; 
	if ($job )  		$cond .="and a.job_no_prefix_num in($job)" ;
	if ($style )   		$cond .="and a.style_ref_no in($style) " ; 
	if ($int_ref )   	$cond .="and c.grouping = $txt_inter_ref" ; 
	if ($job_year )   	$cond .="and to_char(a.insert_date,'YYYY') = $txt_job_year" ; 

	$sql = "select a.company_name,a.location_name,a.buyer_name,a.job_no,a.id as job_id,a.style_ref_no,b.item_number_id as item,b.order_quantity,a.order_uom as uom, to_char(a.insert_date,'YYYY') as job_year from wo_po_details_master a, wo_po_color_size_breakdown b,wo_po_break_down c where a.id=c.job_id and c.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $cond";
	// echo $sql; die;
	$sql_res = sql_select($sql);
	$data_arr = array();
	$job_id_arr = array();
	foreach ($sql_res as $v) {
		$job_id_arr [$v['JOB_ID']]= $v['JOB_ID'];
		$data_arr[$v['JOB_ID']][$v['ITEM']]['STYLE_REF_NO']		= $v['STYLE_REF_NO'];
		$data_arr[$v['JOB_ID']][$v['ITEM']]['COMPANY_NAME'] 	= $v['COMPANY_NAME'];
		$data_arr[$v['JOB_ID']][$v['ITEM']]['JOB_NO'] 			= $v['JOB_NO'];
		$data_arr[$v['JOB_ID']][$v['ITEM']]['LOCATION_NAME'] 	= $v['LOCATION_NAME'];
		$data_arr[$v['JOB_ID']][$v['ITEM']]['BUYER_NAME'] 		= $v['BUYER_NAME'];
		$data_arr[$v['JOB_ID']][$v['ITEM']]['JOB_YEAR'] 		= $v['JOB_YEAR']; 
		$data_arr[$v['JOB_ID']][$v['ITEM']]['ORDER_QUANTITY']  += $v['ORDER_QUANTITY'];
		$data_arr[$v['JOB_ID']][$v['ITEM']]['UOM'] 				= $v['UOM']; 
	}
	// echo "***".count($data_arr)."***";
	// pre($data_arr);die;
	// ====================================== Existing Body Part =========================================
	$cond2 ="";
	if ($company_id ) 	$cond2 .="and a.company_name = $company_id" ;
	if ($buyer)   		$cond2 .="and a.buyer_name=$buyer" ; 
	if ($style )   		$cond2 .="and a.style_ref_no in($style) " ; 
	// if ($int_ref )   	$cond2 .="and a.grouping $txt_inter_ref" ;  
	$job_con = where_con_using_array($job_id_arr,1,'a.job_id');
	$body_part_sql = "select id,job_id, body_part_ids, set_body_part, item_id from style_wise_body_part_mst a where a.status_active=1 and a.is_deleted=0 $cond2 $job_con";
	$body_part_res = sql_select($body_part_sql);
	foreach ($body_part_res as $v) { 
		$data_arr[$v['JOB_ID']][$v['ITEM_ID']]['BODY_PART_IDS'] = $v['BODY_PART_IDS']; 
		$data_arr[$v['JOB_ID']][$v['ITEM_ID']]['SET_BODY_PART'] = $v['SET_BODY_PART']; 
		$data_arr[$v['JOB_ID']][$v['ITEM_ID']]['MST_ID']		= $v['ID']; 
	}
	// echo $body_part_sql ;die;
	?>
	
	<style>
		.tableFixHead          { max-height: 250px; overflow: auto;}
		.tableFixHead thead th { position: sticky; top: -2px; z-index: 1; }
	</style>
	<body>
		<div align="center"  id="scroll_body">  
			<fieldset style="width:1025px;" class="tableFixHead"> 
				<table width="1000px" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="report_table_input">
					<thead>
						<th width="30">SL</th>
						<th width="120">Company</th>
						<th width="120">Location</th>
						<th width="100">Buyer</th>
						<th width="50">Job Year</th>
						<th width="80">Job</th>
						<th width="120">Style</th>
						<th width="140">Germ. Items</th>
						<th width="60">GMTt. Item Qty</th>
						<th width="40">UOM</th>
						<th width="120">Body Part</th>
					</thead>
					<tbody>
						<form id="body_part_entry_form">
							<?
								$i = 0;
								$is_update = 0;
								foreach ($data_arr as $job_id => $item_arr) 
								{ 
									foreach ($item_arr as $item => $v) 
									{  
										$i++;
										$body_part_names = '';
										foreach (explode(',', $v['BODY_PART_IDS']) as $k) {
											$body_part_names .= $body_part_lib[$k] .',';
											if ($v['MST_ID']) $is_update= 1; 
										}
										if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
										?>
											<tr  onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>'); set_deleted_column(<?= $i; ?>)" id="tr_1nd<? echo $i; ?>">
												<td><?= $i ?></td>
												<td title="<?= $v['COMPANY_NAME'] ?>"><?= $company_arr[$v['COMPANY_NAME']] ?></td>
												<td><?= $location_arr[$v['LOCATION_NAME']] ?></td> 
												<td><?= $buyer_arr[$v['BUYER_NAME']] ?></td> 
												<td align="center"><?= $v['JOB_YEAR'] ?></td>
												<td><?= $v['JOB_NO'] ?></td>
												<td><?= $v['STYLE_REF_NO'] ?></td> 
												<td><?= $garments_item[$item] ?></td>
												<td align="right"><?= $v['ORDER_QUANTITY'] ?></td>
												<td align="center"><?= $unit_of_measurement[$v['UOM']] ?></td>
												<td>
													<input value="<?= Rtrim($body_part_names,',') ?>" style='cursor:pointer' style="width:140px;" type="text"  onDblClick="browseBodyPart('<?= $i ?>')" class="text_boxes" autocomplete="off" placeholder="Browse" name="txt_body_part" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).ondblclick()" id="txt_body_part_<?= $i ?>"/>
													<input name='body_part_id'  id="hidden_body_part_<?= $i ?>"  value="<?= $v['BODY_PART_IDS'] ?>" type="hidden">
													<input name='hidden_body_part_row'  id="hidden_body_part_row_<?= $i ?>" value="<?= $v['SET_BODY_PART'] ?>"  type="hidden">
													<input name='hidden_company_name'  id="hidden_company_name_<?= $i ?>" value="<?= $v['COMPANY_NAME'] ?>" type="hidden">
													<input name='hidden_location_name'  id="hidden_location_name_<?= $i ?>" value="<?= $v['LOCATION_NAME'] ?>" type="hidden">
													<input name='hidden_buyer_name'  id="hidden_buyer_name_<?= $i ?>" value="<?= $v['BUYER_NAME'] ?>" type="hidden">
													<input name='hidden_job_year'  id="hidden_job_year_<?= $i ?>" value="<?= $v['JOB_YEAR'] ?>" type="hidden">
													<input name='hidden_job_no'  id="hidden_job_no_<?= $i ?>" value="<?= $v['JOB_NO'] ?>" type="hidden">
													<input name='hidden_job_id'  id="hidden_job_id_<?= $i ?>" value="<?= $job_id ?>" type="hidden">
													<input name='hidden_style_ref_no'  id="hidden_style_ref_no_<?= $i ?>" value="<?= $v['STYLE_REF_NO'] ?>" type="hidden">
													<input name='hidden_item'  id="hidden_item_<?= $i ?>" value="<?= $item ?>" type="hidden">
													<input name='hidden_order_quantity'  id="hidden_order_quantity_<?= $i ?>" value="<?= $v['ORDER_QUANTITY'] ?>" type="hidden">
													<input name='hidden_uom'  id="hidden_uom_<?= $i ?>" value="<?= $v['UOM'] ?>" type="hidden">
													<input name='hidden_mst_id'  id="mstId_<?= $i ?>" value="<?= $v['MST_ID'] ?>" type="hidden">
													<input name='hidden_mst_id'  id="enable_delete_<?= $i ?>" value="0" type="hidden">
												</td>
											</tr>
										<? 
									}
								}
							?>
						</form>	
						
					</tbody>
					
				</table>  
			</fieldset>
		</div>
		<table>
			<tr>
				<td align="center" valign="middle" class="button_container"><?=load_submit_buttons( $permission, "fnc_body_part_entry", $is_update,0,"refresh_page()",1); ?></td>
			</tr>
		</table>
	</body>
	<!-- <script src="../../../includes/functions_bottom.js" type="text/javascript"></script> -->
		
    </html>
    <?
    exit(); 
}
/*
|--------------------------------------------------------------------------
| Save Update Delete
|--------------------------------------------------------------------------
|
*/
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	// echo $action;die;
	// print_r($process);die;
	
	if ($operation==0)   // Insert Here========================================================================================
	{		
		$con = connect(); 

		$field_array		=	"id, company_name, job_id, job_no, buyer_name, location_name, style_ref_no, item_id, order_quantity, uom, body_part_ids, set_body_part, insert_by, insert_date,status_active,is_deleted";  
		$field_array_dtls	=	"id, mst_id, body_part_id, insert_by, insert_date,status_active,is_deleted"; 
		$data_array		 	= 	"";
		$data_array_dtls 	= 	"";
		$field_array_up	=	"company_name*job_id*job_no*buyer_name*location_name*style_ref_no*item_id*order_quantity*uom*body_part_ids*set_body_part*update_by*update_date";
		// print_r( $field_array_up);die;	
	
		
		$data_array_up = array();
		$mst_id_array  = array();
		// echo $row_num;die;
		
		for($j=1; $j<= $row_num; $j++)
		{ 
			$body_part_ids 	=	"body_part_ids_".$j;
			$body_part_row 	=	"body_part_row_".$j;
			$company      	=	"company_name_".$j;
			$buyer			=	"buyer_name_".$j;
			$location		=	"location_name_".$j;
			$job_year		=	"job_year_".$j;
			$job_no			=	"job_no_".$j;
			$job_id			=	"job_id_".$j;
			$style_ref_no	=	"style_ref_no_".$j;
			$item			=	"item_".$j;
			$order_quantity	=	"order_quantity_".$j;
			$uom			=	"uom_".$j; 
			$mstId			=	"mstId_".$j;
			 
			if($$mstId>0)
			{
				// echo  $$mstId."888";die;
				if(str_replace("'","",$$body_part_ids) !="")
				{  
					$mst_id_array[]=$$mstId;  
					$data_array_up[$$mstId]=explode("*",("".$$company."*".$$job_id."*'".$$job_no."'*'".$$buyer."'*".$$location."*'".$$style_ref_no."'*".$$item."*".$$order_quantity."*".$$uom."*'".$$body_part_ids."'*'".$$body_part_row."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					// echo "<pre>";
					// print_r($data_array_up);die;
					// =========== for dtls table ==============
					$bodyPartIdArr=explode(",", str_replace("'","",$$body_part_ids)); 
					
					foreach ($bodyPartIdArr as $body_part_id) 
					{ 
						$id = return_next_id_by_sequence("style_wise_body_part_dtls_seq", "style_wise_body_part_dtls", $con);
						
						if($data_array_dtls!="") $data_array_dtls.=",";
						$data_array_dtls.="(".$id.",".$$mstId.",".$body_part_id . ",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					}
				}
			}
			else
			{ 
				// echo $$body_part_ids; die;
				if(str_replace("'","",$$body_part_ids) !="")
				{
					$id = return_next_id_by_sequence("style_wise_body_part_mst_seq", "style_wise_body_part_mst", $con); 
					if($data_array!="") $data_array.=","; 
					$data_array.="(".$id.",'".$$company."',".$$job_id.",'".$$job_no."','".$$buyer."','".$$location."','".$$style_ref_no."',".$$item.",".$$order_quantity.",".$$uom.",'".$$body_part_ids."','".$$body_part_row."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

					// =========== for dtls table ==============
 
					$bodyPartIdArr=explode(",", str_replace("'","",$$body_part_ids)); 
					foreach ($bodyPartIdArr as $body_part_id) 
					{ 
						$mst_id = return_next_id_by_sequence("style_wise_body_part_dtls_seq", "style_wise_body_part_dtls", $con); 
						if($data_array_dtls!="") $data_array_dtls.=",";
						$data_array_dtls.="(".$mst_id.",".$id.",".$body_part_id . ",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					}
					
				}
			}
		}
		//    echobulk_update_sql_statement( "style_wise_body_part_mst", "id", $field_array_up, $data_array_up, $mst_id_array ); die;
		$rID1=$rID2=$rID3=$rID4=true;
		if(count($data_array_up)>0)
		{
			$rID1=execute_query(bulk_update_sql_statement( "style_wise_body_part_mst", "id", $field_array_up, $data_array_up, $mst_id_array ));
		}
	    if($data_array!="")
		{
			$rID2=sql_insert("style_wise_body_part_mst",$field_array,$data_array);
		} 
		// print_r($mst_id_array); die;
		$deleted_id = implode(",",$mst_id_array);
		if($deleted_id!="")
		{
			$rID3=execute_query( "delete from style_wise_body_part_dtls where mst_id in ($deleted_id)",0);
		}

		// echo "rID2***insert into style_wise_body_part_mst (".$field_array.") values ".$data_array;
		// echo "<br><br><br><br>";
		// echo "rID4***insert into style_wise_body_part_dtls (".$field_array_dtls.") values ".$data_array_dtls;
		// die;
	    if($data_array_dtls!="")
		{
			$rID4=sql_insert("style_wise_body_part_dtls",$field_array_dtls,$data_array_dtls);
		}	
		
		// echo "6**$rID1 = $rID2 = $rID3  = $rID4 <br>";
		// die; 
		if($rID1 && $rID2 && $rID3 && $rID4)
		{
			oci_commit($con);  
			echo "0**".$id."**".$mst_id;
		}
		else
		{
			oci_rollback($con);
			echo "10**".$id."**".$mst_id;
		} 
		disconnect($con);
		die;	
		
	}
	else if ($operation==1)   // Update Here
	{		
		$con = connect(); 

		$field_array		=	"id, company_name, job_id, job_no, buyer_name, location_name, style_ref_no, item_id, order_quantity, uom, body_part_ids, set_body_part, insert_by, insert_date,status_active,is_deleted";  
		$field_array_dtls	=	"id, mst_id, body_part_id, insert_by, insert_date,status_active,is_deleted"; 
		$data_array		 	= 	"";
		$data_array_dtls 	= 	"";
		$field_array_up	=	"company_name*job_id*job_no*buyer_name*location_name*style_ref_no*item_id*order_quantity*uom*body_part_ids*set_body_part*update_by*update_date";
		// print_r( $field_array_up);die;	
	
		
		$data_array_up = array();
		$mst_id_array  = array();
		// echo $row_num;die;
		
		for($j=1; $j<= $row_num; $j++)
		{ 
			$body_part_ids 	=	"body_part_ids_".$j;
			$body_part_row 	=	"body_part_row_".$j;
			$company      	=	"company_name_".$j;
			$buyer			=	"buyer_name_".$j;
			$location		=	"location_name_".$j;
			$job_year		=	"job_year_".$j;
			$job_no			=	"job_no_".$j;
			$job_id			=	"job_id_".$j;
			$style_ref_no	=	"style_ref_no_".$j;
			$item			=	"item_".$j;
			$order_quantity	=	"order_quantity_".$j;
			$uom			=	"uom_".$j; 
			$mstId			=	"mstId_".$j;
			 
			if($$mstId>0)
			{
				// echo  $$mstId."888";die;
				if(str_replace("'","",$$body_part_ids) !="")
				{  
					$mst_id_array[]=$$mstId;  
					$data_array_up[$$mstId]=explode("*",("".$$company."*".$$job_id."*'".$$job_no."'*'".$$buyer."'*".$$location."*'".$$style_ref_no."'*".$$item."*".$$order_quantity."*".$$uom."*'".$$body_part_ids."'*'".$$body_part_row."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'")); 
					// =========== for dtls table ==============
					$bodyPartIdArr=explode(",", str_replace("'","",$$body_part_ids)); 
					
					foreach ($bodyPartIdArr as $body_part_id) 
					{ 
						$id = return_next_id_by_sequence("style_wise_body_part_dtls_seq", "style_wise_body_part_dtls", $con);
						
						if($data_array_dtls!="") $data_array_dtls.=",";
						$data_array_dtls.="(".$id.",".$$mstId.",".$body_part_id . ",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					}
				}
			}
			else
			{ 
				// echo $$body_part_ids; die;
				if(str_replace("'","",$$body_part_ids) !="")
				{
					$id = return_next_id_by_sequence("style_wise_body_part_mst_seq", "style_wise_body_part_mst", $con); 
					if($data_array!="") $data_array.=","; 
					$data_array.="(".$id.",'".$$company."',".$$job_id.",'".$$job_no."','".$$buyer."','".$$location."','".$$style_ref_no."',".$$item.",".$$order_quantity.",".$$uom.",'".$$body_part_ids."','".$$body_part_row."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

					// =========== for dtls table ==============
 
					$bodyPartIdArr=explode(",", str_replace("'","",$$body_part_ids)); 
					foreach ($bodyPartIdArr as $body_part_id) 
					{ 
						$mst_id = return_next_id_by_sequence("style_wise_body_part_dtls_seq", "style_wise_body_part_dtls", $con); 
						if($data_array_dtls!="") $data_array_dtls.=",";
						$data_array_dtls.="(".$mst_id.",".$id.",".$body_part_id . ",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					}
					
				}
			}
		}
		//    echobulk_update_sql_statement( "style_wise_body_part_mst", "id", $field_array_up, $data_array_up, $mst_id_array ); die;
		//    echobulk_update_sql_statement( "style_wise_body_part_mst", "id", $field_array_up, $data_array_up, $mst_id_array ); die;
		$rID1=$rID2=$rID3=$rID4=true;
		if(count($data_array_up)>0)
		{
			$rID1=execute_query(bulk_update_sql_statement( "style_wise_body_part_mst", "id", $field_array_up, $data_array_up, $mst_id_array ));
		}
		if($data_array!="")
		{
			$rID2=sql_insert("style_wise_body_part_mst",$field_array,$data_array);
		} 
		// print_r($mst_id_array); die;
		$deleted_id = implode(",",$mst_id_array);
		if($deleted_id!="")
		{
			$rID3=execute_query( "delete from style_wise_body_part_dtls where mst_id in ($deleted_id)",0);
		}

		// echo "rID2***insert into style_wise_body_part_mst (".$field_array.") values ".$data_array;
		// echo "<br><br><br><br>";
		// echo "rID4***insert into style_wise_body_part_dtls (".$field_array_dtls.") values ".$data_array_dtls;
		// die;
		if($data_array_dtls!="")
		{
			$rID4=sql_insert("style_wise_body_part_dtls",$field_array_dtls,$data_array_dtls);
		}	
		
		// echo "6**$rID1 = $rID2 = $rID3  = $rID4 <br>";
		// die; 
		if($rID1 && $rID2 && $rID3 && $rID4)
		{
			oci_commit($con);  
			echo "1**".$id."**".$mst_id;
		}
		else
		{
			oci_rollback($con);
			echo "10**".$id."**".$mst_id;
		} 
		disconnect($con);
		die;
		
	}
	else if ($operation==2)  //Delete here======================================================================================
	{		
		$con = connect(); 
		$data_array_up = array();
		$mst_id_array  = array(); 
		
		for($j=1; $j<= $row_num; $j++)
		{ 
			$body_part_ids 	=	"body_part_ids_".$j;
			$body_part_row 	=	"body_part_row_".$j;
			$mstId			=	"mstId_".$j; 
			$enableDelete	=	"enable_delete_".$j;
			 
			if($$mstId>0 && $$enableDelete)
			{
				// echo  $$mstId."888";die;
				if(str_replace("'","",$$body_part_ids) !="")
				{  
					$mst_id_array[]=$$mstId;    
				}
			} 
		}
		 
		// print_r($mst_id_array); die;
		$deleted_id = implode(",",$mst_id_array);
		if($deleted_id!="")
		{
			// echo "hello"; die;
			$user_id = $_SESSION['logic_erp']['user_id']; 
			$rID1=execute_query( "update style_wise_body_part_mst SET status_active =0, is_deleted = 1,update_by='$user_id' , update_date='$pc_date_time' where id in ($deleted_id)",0);
			$rID2=execute_query( "delete from style_wise_body_part_dtls where mst_id in ($deleted_id)",0);
		}else{
			 echo "80**".$id."**".$mst_id;
		} 
		// die;
		
		if($rID1 && $rID2)
		{
			oci_commit($con);  
			echo "2**".$id."**".$mst_id;
		}
		else
		{
			oci_rollback($con);
			echo "6**".$id."**".$mst_id;
		} 
		disconnect($con);
		die;	
		
	}
}
?>
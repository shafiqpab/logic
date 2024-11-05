<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
/*
|--------------------------------------------------------------------------
					| CUSTOM FUNCTIONS 
|--------------------------------------------------------------------------
|
*/
if (!function_exists('pre'))
{
  function pre($array)
  {
    echo "<pre>";
    print_r($array);
    echo "</pre>";
  }
}
if (!function_exists('num_format'))
{
  function is_num($num)
  {
    return (is_infinite($num) || is_nan($num)) ? 0 : $num;
  }
}
$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="load_drop_down_location")
{
  extract($_REQUEST);
  echo create_drop_down( "cbo_location_id", 130, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in( $choosenCompany) group by id,location_name  order by location_name","id,location_name", 0, "-- Select location --", $selected, "" );
  exit();
}

if ($action=="load_drop_down_floor")
{
  extract($_REQUEST);
  echo create_drop_down( "cbo_floor_id", 130, "SELECT id,floor_name from lib_prod_floor where location_id in( $choosenLocation ) and status_active =1 and is_deleted=0 and production_process in (4,5) group by id,floor_name order by floor_name","id,floor_name", 0, "-- Select Floor --", $selected, "" );
  exit();
}
/*
|--------------------------------------------------------------------------
| Line popup
|--------------------------------------------------------------------------
|
*/
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
	if($company==0) $company_name=""; else $company_name=" and b.company_name=$company";

	if($buyer==0) $buyer_name=""; else $buyer_name="and b.buyer_name=$buyer";
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company and variable_list=23 and is_deleted=0 and status_active=1");
    // echo $prod_reso_allo; die;
	$cond ="";
    if($prod_reso_allo==1)
	{
		$line_array=array(); 
		if( $location!=0 ) $cond .= " and a.location_id in($location) ";
		if( $floor_id!=0 ) $cond.= " and a.floor_id in ($floor_id) ";

		$line_sql="select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id $data_format and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number";
		$line_sql_result=sql_select($line_sql);

		?>
            <input type='hidden' id='txt_selected_id' />
            <input type='hidden' id='txt_selected' />
            <table cellspacing="0" width="230"  border="1" rules="all" class="rpt_table" >
            	<thead>
                	<th width="50"></th>
                    <th width="280">Line Name</th>
                </thead>
            </table>
            <div style="width:250px; max-height:250px; overflow-y:scroll" id="scroll_body" >
        		<table cellspacing="0" width="230"  border="1" rules="all" class="rpt_table" id="list_view" >
                    <tbody>
                        <? 
                            $i=1;
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
                                        <td width="30" align="center"> <? echo $i;?></td>
                                        <td width="200" align="center"> <? echo $line_val;?> </td>
                                    </tr>
                                <?
                                $i++;
                            }
                        ?>
                    </tbody> 
              </table>
            </div>
            <table width="250">
                <tr align="center">
                    <td><input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fn_onClosed()" /></td>
                </tr>
            </table>
            <script>
                setFilterGrid('list_view',-1);
            </script>
        <?
	}
	else
	{
		if( $location!=0  ) $cond = " and location_name= $location";
		if( $floor_id!=0 ) $cond.= " and floor_name= $floor_id";
		$line_data="select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name";
		echo create_list_view("list_view", "Line No","230","230","310",0, $line_data , "js_set_value", "id,line_name", "", 1, "0", $arr, "line_name",
		"","setFilterGrid('list_view',-1)","0","",1) ;
		echo "<input type='hidden' id='txt_selected_id' />";
		echo "<input type='hidden' id='txt_selected' />";
	}
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
			for( var i = 0; i < selected_id.length; i++ ) 
			{
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			selected_str = ddd.substr( 0, ddd.length - 1 );

			let id_str = (popupFor == 1) ? 'txt_job_no' : (popupFor == 2) ? 'txt_style_no': 'txt_order_no' ; 
			parent.window.document.getElementById(id_str).value=selected_str; //Set Data

			parent.emailwindow.hide();  //For Single Select
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
                            <th>Year</th>
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
                                <td>
                                    <?
                                        echo create_drop_down( "txt_job_year", 140, $year,"", 1, "-- Select year --", date('Y'), "","");
                                    ?>
                                </td> 
                                <td align="center">
                                    <? 
									$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Order No"); 
                                    $dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
                                    echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", $popupFor,$dd,0 );
                                    ?>
                                </td>
                                <td align="center" id="search_by_td"> 
                                    <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                                </td>
                                <td align="center">
                                    <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<?php echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_job_year').value+'**'+'<?= $popupFor?>', 'create_job_no_search_list_view', 'search_div', 'size_wise_details_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action	==	"create_job_no_search_list_view")
{
  // echo  $data; die;
	$data = explode('**',$data);
	$company_id = $data[0];
	$buyer_id   = $data[1];
  	$search_by  = $data[2];
	$search_str = trim($data[3]);
	$year_id    = $data[4];
	$popupFor   = $data[5]; 

	if ($popupFor == 1) //For job
	{
		$set_column_data = 'job_no'; 
    	$id = 'a.id';
	} 
	if ($popupFor == 2) //For Style
	{
		$set_column_data = 'style_ref_no'; 
    	$id = 'a.id';
	} 
	if ($popupFor == 3) //For PO
	{
		$set_column_data = 'po_number'; 
    	$id = 'b.id';
	}

	if($search_by == 2)
	{
		$search_field = "a.style_ref_no";
	}
	else if($search_by == 3)
	{
		$search_field = "b.po_number";
	}
	else
	{
		$search_field = "a.job_no";
	} 

	$sql_cond = "";
	$sql_cond .= $company_id  ? " AND a.company_name=$company_id " : "";
	$sql_cond .= $buyer_id    ? " AND a.buyer_name=$buyer_id " : "";
	$sql_cond .= $year_id     ? " AND TO_CHAR(a.insert_date,'YYYY') = '$year_id' " : "";
	$sql_cond .= $search_str  ? " AND  $search_field LIKE '%$search_str%' " : "";

	
	$company_arr=return_library_array( "SELECT id, company_name FROM lib_company WHERE id IN(".$company_id.")", "id", "company_name" );
	$buyer_arr=return_library_array( "SELECT buy.id, buy.buyer_name FROM lib_buyer buy, lib_buyer_tag_company b WHERE buy.status_active =1 AND buy.is_deleted=0 AND b.buyer_id=buy.id AND b.tag_company IN (".$company_id.") AND buy.id IN (SELECT buyer_id FROM lib_buyer_party_type WHERE party_type IN (1,3,21,90) ) GROUP BY buy.id, buy.buyer_name ORDER BY buy.buyer_name","id", "buyer_name");
	$arr=array (0=>$company_arr,1=>$buyer_arr);

	$sql= "SELECT $id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,TO_CHAR(a.insert_date,'YYYY') AS year,b.po_number  FROM wo_po_details_master a,wo_po_break_down b WHERE a.id=b.job_id and  a.status_active=1 AND a.is_deleted=0 and b.status_active=1 AND b.is_deleted=0 $sql_cond  ORDER BY job_no DESC";
  	// echo $sql; die;
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No,PO No", "120,80,80,60,80,80","620","270",0, $sql , "js_set_value", "id,$set_column_data", "$popupFor", 1, "company_name,buyer_name,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no,po_number", "",'','0,0,0,0,0,0','',1) ;
	exit();
}  

/*
|--------------------------------------------------------------------------
								| REPORT START HERE
|--------------------------------------------------------------------------
|
*/
if ($action=='report_generate')
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$type  			= str_replace("'","",$type);
	$lc_company  	= str_replace("'","",$cbo_company_id);
	$wo_company 	= str_replace("'","",$wo_company_id);
	$job_no 		= str_replace("'","",$txt_job_no); 
	$style_no 		= str_replace("'","",$txt_style_no); 
	$order_no 		= str_replace("'","",$txt_order_no); 
	$location_ids 	= str_replace("'","",$cbo_location_id); 
	$floor_ids 		= str_replace("'","",$cbo_floor_id); 
	$line_ids 		= str_replace("'","",$hidden_line_id); 
	$year_id 		= str_replace("'","",$txt_job_year); 

	// ============================================================================================================
	//												Library
	// ============================================================================================================
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  ); 
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
	order by sewing_line_serial"); 
	foreach($lineDataArr as $lRow)
	{
		$lineArr[$lRow['ID']]=$lRow['LINE_NAME'];
		$lineSerialArr[$lRow['ID']]=$lRow['SEWING_LINE_SERIAL'];
		// $lastSlNo=$lRow['SEWING_Line_SERIAL'];
	}

	if ($type==1) //Show  //GBL REF FROM 1
	{ 
		$sql_cond  = ""; 
		$sql_cond .= $lc_company 	? " and c.company_name=$lc_company "		: "";
		$sql_cond .= $job_no 	 	? " and c.job_no LIKE '%$job_no%' "			: "";
		$sql_cond .= $style_no 		? " and c.style_ref_no='$style_no' "		: "";
		$sql_cond .= $order_no 		? " and b.po_number='$order_no' "			: "";
		$sql_cond .= $year_id     	? " AND TO_CHAR(c.insert_date,'YYYY') = '$year_id' " : "";


		$sql = "SELECT a.id as color_size_id, a.order_quantity as po_qty,a.size_number_id as size_id,a.color_number_id as color,a.article_number,a.item_number_id as item,b.id as po_id,b.po_number,c.style_ref_no as style,c.id as job_id from wo_po_color_size_breakdown a,wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_id=c.id $sql_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 order by b.id,a.id";
		// echo $sql; die;
		$sql_res = sql_select($sql);  
		if (count($sql_res) == 0 ) 
		{
			echo "<h1 style='color:red; font-size: 17px;text-align:center;margin-top:20px;'> ** Data Not Found ** </h1>" ;
			die();
		}
		$size_id_array = $po_id_array = $order_data_array = $po_qty_array = array();
		foreach ($sql_res as  $v) 
		{
			$size_id_array[$v['SIZE_ID']] 	= $v['SIZE_ID'];
			$po_id_array[$v['PO_ID']] 		= $v['PO_ID'];

			$order_data_array[$v['PO_ID']][$v['ITEM']][$v['COLOR']]['ARTICLE'][$v['ARTICLE_NUMBER']] = $v['ARTICLE_NUMBER'];  
			$order_data_array[$v['PO_ID']][$v['ITEM']][$v['COLOR']]['STYLE'] 		= $v['STYLE'];  
			$order_data_array[$v['PO_ID']][$v['ITEM']][$v['COLOR']]['PO_NUMBER'] 	= $v['PO_NUMBER'];  
			$po_qty_array[$v['PO_ID']][$v['ITEM']]['PO_QTY'] 	+= $v['PO_QTY'];  
			$size_wise_po_qty_array[$v['PO_ID']][$v['ITEM']][$v['COLOR']][$v['SIZE_ID']] += $v['PO_QTY'];  
		}   
		// pre($order_data_array); die;
		//=========================================================================================================
		//												CLEAR TEMP ENGINE
		// ==========================================================================================================
		$con = connect();
		execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form=161 and ref_from=1 ");
		oci_commit($con);   
		// =========================================================================================================
		//												INSERT DATA INTO TEMP ENGINE
		// =========================================================================================================
		fnc_tempengine("gbl_temp_engine", $user_id, 161, 1,$po_id_array, $empty_arr); 
		$prod_cond = "";
		$prod_cond.= $wo_company 	? " and b.serving_company in ($wo_company) ": "";
		$prod_cond.= $location_ids 	? " and b.location in($location_ids) "		: "";
		$prod_cond.= $floor_ids 	? " and b.floor_id in($floor_ids) "			: "";
		$prod_cond.= $line_ids 		? " and b.sewing_line in($line_ids) "		: "";

		$prod_sql = "SELECT a.production_qnty as prod_qty,a.production_type as prod_type,a.reject_qty,b.sewing_line,c.size_number_id as size_id,c.color_number_id as color,c.item_number_id as item,b.po_break_down_id as po_id,b.prod_reso_allo,c.article_number from pro_garments_production_dtls a,pro_garments_production_mst b,wo_po_color_size_breakdown c,gbl_temp_engine tmp where a.mst_id=b.id and a.color_size_break_down_id=c.id  and b.po_break_down_id=tmp.ref_val $prod_cond and a.production_type in(4,5) and tmp.entry_form=161 and tmp.ref_from=1 and tmp.user_id=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";
		// echo $prod_sql; die;
		$prod_sql_res = sql_select($prod_sql);  
		$prod_data_array = array();
		$line_wise_data_array = array();
		foreach ($prod_sql_res as  $v) 
		{
			if($v['PROD_RESO_ALLO']==1)
			{
				$line_name = "";
				// echo $prod_reso_arr[$v['SEWING_LINE']]."<br>";
				$sewing_line_id_arr=explode(",",$prod_reso_arr[$v['SEWING_LINE']]);
				foreach ($sewing_line_id_arr as $r) 
				{					
					// echo $prod_reso_arr[$v['SEWING_LINE']]."<br>";
					$line_name .= ($line_name=="") ? $lineArr[$r] : ",". $lineArr[$r];
				}
				$sewing_line_id = $sewing_line_id_arr[0];
			} 
			else
			{
				$sewing_line_id=$v['SEWING_LINE'];
				$line_name=$lineArr[$v['SEWING_LINE']];
			}

			$prod_data_array[$v['PO_ID']][$v['ITEM']][$v['COLOR']]['PROD_QTY'][$v['PROD_TYPE']][$v['SIZE_ID']] 	+= $v['PROD_QTY']; 
			$prod_data_array[$v['PO_ID']][$v['ITEM']][$v['COLOR']]['REJECT_QTY'][$v['SIZE_ID']] += $v['REJECT_QTY']; 
			$prod_data_array[$v['PO_ID']][$v['ITEM']][$v['COLOR']]['LINE'][$line_name] 	 = $line_name; 
			$prod_data_array[$v['PO_ID']][$v['ITEM']][$v['COLOR']]['ARTICLE'][$v['ARTICLE_NUMBER']] = $v['ARTICLE_NUMBER']; 


			 
			$line_wise_data_array[$v['PO_ID']][$v['ITEM']][$v['COLOR']][$line_name]['PROD_QTY'][$v['PROD_TYPE']][$v['SIZE_ID']] 	+= $v['PROD_QTY']; 
			$line_wise_data_array[$v['PO_ID']][$v['ITEM']][$v['COLOR']][$line_name]['REJECT_QTY'][$v['SIZE_ID']] += $v['REJECT_QTY']; 
			$line_wise_data_array[$v['PO_ID']][$v['ITEM']][$v['COLOR']][$line_name]['ARTICLE'][$v['ARTICLE_NUMBER']] = $v['ARTICLE_NUMBER']; 
			
  
		}  
		// pre($line_wise_data_array); die;
		$size_str = implode(',',$size_id_array);
		$size_array= return_library_array( "SELECT id,size_name from lib_size where id in($size_str) and status_active=1 and is_deleted=0  order by sequence asc,size_name asc", "id", "size_name"  );
		// pre($size_array); die;

		// ============================================================================================================
		//												ROWSPAN CALCULATION
		// ============================================================================================================
		$color_span_array = $item_span_array  = $po_wise_row_array = array(); 
		$no_of_po = 0;
		$no_of_color = 0;
		foreach ($order_data_array as $po_id => $item_data_arr) 
		{  
			$no_of_po ++;
			foreach ($item_data_arr as $item_id => $color_data_arr) 
			{ 
				$item_span_array[$po_id]++;
				foreach ($color_data_arr as $color_id => $v) 
				{ 
					$color_span_array[$po_id][$item_id]++;
					$po_wise_row_array[$po_id]++;
					$no_of_color++;
				}	
			}
		}
		// ============================================================================================================
		//												ROWSPAN FOR LINE WISE CALCULATION
		// ============================================================================================================
		$po_wise_row_array2 = $item_span_array2 = $color_span_array2 = array();
		$numOfRow = 0; 
		foreach ($line_wise_data_array as $po_id => $item_data_arr) 
		{   
			foreach ($item_data_arr as $item_id => $color_data_arr) 
			{  
				foreach ($color_data_arr as $color_id => $lineArray) 
				{  
					foreach ($lineArray as  $line_name => $v) 
					{ 
						$numOfRow++;
						$po_wise_row_array2[$po_id]++; 
						$item_span_array2[$po_id][$item_id]++;
						$color_span_array2[$po_id][$item_id][$color_id]++; 
						 
					}
				}	
			}
		}
		// pre($po_wise_row_array2); die;

		// ============================================================================================================
		//												CLEAR TEMP ENGINE
		// ============================================================================================================
		
		execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form=161 and ref_from=1 ");
		
		oci_commit($con);  
		disconnect($con);
		ob_start();
		$width = 900+( count($size_array)*50 );
		?>  
			<fieldset>
				<div align="left" style="height:auto; width:<? echo $width+20;?>px; margin:0; padding:10px 0 10px 0;"> 
					<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="tbl_caption" align="center">
						<thead class="form_caption" >
							<tr>
								<td colspan="<?= 9 + count($size_array) ?>" align="center" style="font-size:18px; font-weight:bold" >Size Wise Production Report</td>
							</tr>
							<tr>
								<td colspan="<?= 9 + count($size_array) ?>" align="center" style="font-size:18px; font-weight:bold" > <?= $company_library[$lc_company] ?></td>
							</tr>
						</thead>
					</table>  
					<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
						<thead class="form_caption" >	  
							<tr> 
								<th width="120">Style </th>
								<th width="120">PO </th>
								<th width="100">Item </th>
								<th width="80"> Po Qty </th> 
								<th width="100">Color </th> 
								<th width="100" title="Only Production Article ">Article </th> 
								<th width="100">Size </th> 
								<?
									foreach ($size_array as $size ) 
									{
										?>
											<th width="50"><?= $size ?> </th> 
										<?
									}
								?>
								<th width="80">Total </th> 
								<th width="100">Line </th> 
							</tr>
						</thead>
					</table>
					<div style="width:<?= $width+20;?>px; max-height:350px; float:left; overflow-y:scroll;" id="scroll_body">
						<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body" width="<?= $width; ?>" rules="all" align="left">
							<tbody>
								<?
								$i=$j=0;   
								foreach ($order_data_array as $po_id => $item_data_arr) 
								{  
									$k=0;
									foreach ($item_data_arr as $item_id => $color_data_arr) 
									{ 
										$l=0;
										foreach ($color_data_arr as $color_id => $v) 
										{ 
											//ROW SPAN VARIABLES
											$no_of_po_item = $item_span_array[$po_id]; 
											$no_of_po_color = $color_span_array[$po_id][$item_id];
											$po_wise_no_of_color = $po_wise_row_array[$po_id];
											$item_wise_no_of_color = $color_span_array[$po_id][$item_id];
											
											//DATA
											$prod_arr = $prod_data_array[$po_id][$item_id][$color_id]; 
											$po_qty   = $po_qty_array[$po_id][$item_id]['PO_QTY'];
											$size_wise_input  = $prod_arr['PROD_QTY'][4] ; 
											$size_wise_output = $prod_arr['PROD_QTY'][5] ;
											$size_wise_reject = $prod_arr['REJECT_QTY'];
											$size_wise_po_qty = $size_wise_po_qty_array[$po_id][$item_id][$color_id];
											$row_line_arr 	  = $prod_arr['LINE'];
											// pre($prod_arr);
											
											$row_lines = implode(',',$row_line_arr);

											if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
											?>
												<!-- ORDER -->
												<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
													<?   
														if ($j==0) 
														{
															?>
																<td rowspan="<?= 6 * $no_of_color ?>" width="120" valign ="middle" align="center"> <p> <?=  $v['STYLE'];?></p> </td>
															<? 
														} $j++;
													?>
													<?   
														if ($k==0) 
														{
															?> 
																<td rowspan="<?= 6*$po_wise_no_of_color ?>" width="120" valign ="middle" align="center"> <p> <?=  $v['PO_NUMBER']; ?> </p> </td>
																<? 
														} $k++;
													?>
													<?   
														if ($l==0) 
														{
															?> 
																<td rowspan="<?= 6*$item_wise_no_of_color ?>" width="100"  valign ="middle" align="center"> <p> <?= $garments_item[$item_id] ?> </p> </td>
																<td rowspan="<?= 6*$item_wise_no_of_color ?>" width="80" valign ="middle" align="center"  > <?=  number_format($po_qty,0) ?> </td> 
																<? 
														} $l++;
													?>
													<td rowspan="<?= 6 ?>" width="100" valign ="middle" align="center"> <p> <?= $color_library[$color_id] ?> </p></td>
													<td rowspan="<?= 6 ?>" width="100" valign ="middle" titlle="Only Production Article"> <p> <?= implode(',',$prod_arr['ARTICLE'])  ?> </p> </td>
													<td width="100" valign ="middle"> <b>  Order </b> </td>
													<?
														$total_po_qty = 0;
														foreach ($size_array as $size_id => $size ) 
														{
															$po_size_qty = $size_wise_po_qty[$size_id] ? $size_wise_po_qty[$size_id] : 0 ;
															$total_po_qty += $po_size_qty;
															?>
																<td width="50"  valign ="middle" align="right"><?= number_format($po_size_qty,0)?> </td> 
															<?
														}
													?> 
													<td width="80" valign ="middle" align="right" > <?= number_format($total_po_qty,0) ?> </td> 
													<td rowspan="<?= 6 ?>" width="100" valign ="middle" align="center" > <?= $row_lines?> </td> 
												</tr> 
												<!-- Input -->		
												<? if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";  $i+=2;?>
												<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo ++$i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
													
													<td width="100" valign ="middle"> <b>  Input </b> </td>
													<?	
														$total_size_input = 0;
														foreach ($size_array as $size_id => $size ) 
														{ 
															$size_input = $size_wise_input[$size_id] ? $size_wise_input[$size_id]: 0;
															$total_size_input += $size_input;
															?>
																<td width="50"  valign ="middle" align="right"><?= number_format($size_input,0)  ?> </td> 
															<?
														}
													?> 
													<td width="80" valign ="middle" align="right" > <?= number_format($total_size_input,0) ?>  </td>
												</tr> 
												<!-- Output -->
												<? if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";  $i+=2;?>
												<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo ++$i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
													<td width="100" valign ="middle"> <b>  Output </b> </td>
													<?
														$total_size_output =0;
														foreach ($size_array as $size_id => $size ) 
														{
															$size_output = $size_wise_output[$size_id] ? $size_wise_output[$size_id]: 0;
															$total_size_output += $size_output;
															?>
																<td width="50"  valign ="middle" align="right"><?= number_format($size_output,0) ?> </td> 
															<?
														}
													?> 
													<td width="80" valign ="middle" align="right" > <?=  number_format($total_size_output,0) ?> </td>  
												</tr> 
												<!-- Reject -->
												<? if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";  $i+=2;?>
												<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo ++$i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
													<td width="100" valign ="middle"> <b>  Reject </b> </td>
													<?
														$total_size_reject=0;
														foreach ($size_array as $size_id => $size ) 
														{
															$size_reject = $size_wise_reject[$size_id] ? $size_wise_reject[$size_id]: 0;
															$total_size_reject += $size_reject;
															?>
																<td width="50"  valign ="middle" align="right"><?= number_format($size_reject,0) ?> </td> 
															<?
														}
													?> 
													<td width="80" valign ="middle" align="right" > <?= number_format($total_size_reject,0) ?> </td>  
												</tr> 
												<!-- Line Balance -->
												<tr bgcolor="#8DAFDA"> 
													<td width="100" valign ="middle"> <b>  Line Balance </b> </td>
													<? 
														$total_line_balance = $total_size_input - ($total_size_output + $total_size_reject); 
														$total_line_balance_title="Total Input Qty -( Total Output Qty + Total Reject Qty = $total_size_input - ($total_size_output + $total_size_reject) )" ;
														foreach ($size_array as $size_id => $size ) 
														{
															$size_input = $size_wise_input[$size_id] ? $size_wise_input[$size_id]: 0;
															$size_output = $size_wise_output[$size_id] ? $size_wise_output[$size_id]: 0;
															$size_reject = $size_wise_reject[$size_id] ? $size_wise_reject[$size_id]: 0; 
															$line_balance = $size_input -($size_output+$size_reject);
															$line_balance_title="Input Qty -( Output Qty + Reject Qty = $size_input -($size_output+$size_reject) )" ;
															?>
																<td width="50"  valign ="middle" align="right" title="<?= $line_balance_title ?> "><b><?= number_format($line_balance,0)?> </b></td> 
															<?
														}
														
													?> 
													<td width="80" valign ="middle" align="right" title="<?= $total_line_balance_title ?> "><b> <?= number_format($total_line_balance,0)?> </b></td>  
												</tr>  
												<!--Balance --> 
												<tr bgcolor="#FFC">
													<td width="100" valign ="middle"> <b>  Balance </b> </td>
													<?
														$total_balance = $total_size_output - $total_po_qty;
														$total_balance_title="Total Output Qty - Total PO Qty  = $total_size_output - $total_po_qty" ;
														foreach ($size_array as $size_id => $size ) 
														{
															$po_size_qty = $size_wise_po_qty[$size_id] ? $size_wise_po_qty[$size_id] : 0 ; 
															$size_output = $size_wise_output[$size_id] ? $size_wise_output[$size_id]: 0; 
															$balance = $size_output - $po_size_qty;
															$balance_title="Output Qty - PO Qty = $size_output - $po_size_qty" ;
															?>
																<td width="50"  valign ="middle" align="right" title="<?= $balance_title ?> "><b><?= number_format($balance,0) ?> </b></td> 
															<?
														}
													?> 
													<td width="80" valign ="middle" align="right"title="<?= $total_balance_title ?> "><b><?= number_format($total_balance,0) ?>  </b> </td>  
												</tr> 
											<?
										}	
									}
								}
								?>
							</tbody> 
						</table> 
					</div> 
				</div> 
				<!-- ============================== LINE WISE BREAKDOWN ============================== -->  	
				<div align="left" style="height:auto; width:<? echo $width+20;?>px; margin:0; padding:20px 0 10px 0;">  	
					<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="tbl_caption" align="center">
						<thead class="form_caption" >
							<tr>
								<td colspan="<?= 9 + count($size_array) ?>" align="center" style="font-size:18px; font-weight:bold" >Line wise Breakdown</td>
							</tr>
						</thead>
					</table>			
					<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left" style="margin-top:20px"> 
						<thead class="form_caption" > 	  
							<tr> 
								<th width="120">Style </th>
								<th width="120">PO </th>
								<th width="100">Item </th>
								<th width="80"> Po Qty </th> 
								<th width="100">Color </th> 
								<th width="100" title="Only Production Article ">Article </th> 
								<th width="100">Size </th> 
								<?
									foreach ($size_array as $size ) 
									{
										?>
											<th width="50"><?= $size ?> </th> 
										<?
									}
								?>
								<th width="80">Total </th> 
								<th width="100">Line </th> 
							</tr>
						</thead>
					</table> 
					<div style="width:<?= $width+20;?>px; max-height:350px; float:left; overflow-y:scroll;" id="scroll_body">
						<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body" width="<?= $width; ?>" rules="all" align="left">
							<tbody>
								<?
								$i +=5;
								$j=0;   
								foreach ($line_wise_data_array as $po_id => $item_data_arr) 
								{  
									$k=0;
									foreach ($item_data_arr as $item_id => $color_data_arr) 
									{ 
										$l=0;
										foreach ($color_data_arr as $color_id => $lineArray) 
										{ 
											$m=0;
											foreach ($lineArray as  $line_name => $v) 
											{     
												$po_qty   		  = $po_qty_array[$po_id][$item_id]['PO_QTY'];
												$size_wise_po_qty = $size_wise_po_qty_array[$po_id][$item_id][$color_id]; 
												
												$size_wise_input  = $v['PROD_QTY'][4] ; 
												$size_wise_output = $v['PROD_QTY'][5] ;
												$size_wise_reject = $v['REJECT_QTY'];
												$article 		  = $v['ARTICLE'];


												$order_arr  = $order_data_array[$po_id][$item_id][$color_id];
												$style 		= $order_arr['STYLE'];
												$po_number 	= $order_arr['PO_NUMBER'];


												//ROW SPAN VARIABLES
												$po_row_span   = $po_wise_row_array2[$po_id];
												$item_row_span = $item_span_array2[$po_id][$item_id];
												$color_row_span= $color_span_array2[$po_id][$item_id][ $color_id];
												
												

												if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
												?>
													<!-- ORDER -->
													<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
														<?   
															if ($j==0) 
															{
																?>
																	<td rowspan="<?= 5 *$numOfRow ?>" width="120" valign ="middle" align="center"> <p> <?=  $style;?></p> </td>
																<? 
															} $j++;
														?>
														<?   
															if ($k==0)  
															{
																?> 
																	<td rowspan="<?= 5*$po_row_span ?>" width="120" valign ="middle" align="center"> <p> <?=  $po_number; ?> </p> </td>
																<? 
															} $k++;
														?>
														<?   
															if ($l==0) 
															{
																?> 
																	<td rowspan="<?= $item_row_span * 5 ?>" width="100"  valign ="middle" align="center"> <p> <?= $garments_item[$item_id] ?> </p> </td>
																	<td rowspan="<?= $item_row_span * 5 ?>" width="80" valign ="middle" align="center"  > <?= number_format($po_qty,0) ?> </td> 
																	<? 
															} $l++;
														?>
														<?   
															if ($m==0)
															{
																?> 
																	<td rowspan="<?= 5  * $color_row_span ?>" width="100" valign ="middle" align="center" > <p> <?= $color_library[$color_id] ?> </p></td>
																<?	
															} $m++;
														?>		
														<td rowspan="<?= 5 ?>" width="100" valign ="middle" > <p> <?= implode(',',$article)  ?> </p> </td>
														<td width="100" valign ="middle"> <b>  Order </b> </td>
														<?
															$total_po_qty = 0;
															foreach ($size_array as $size_id => $size ) 
															{
																$po_size_qty = $size_wise_po_qty[$size_id] ? $size_wise_po_qty[$size_id] : 0 ;
																$total_po_qty += $po_size_qty;
																?>
																	<td width="50"  valign ="middle" align="right"><?= number_format($po_size_qty,0) ?></td> 
																<?
															}
														?> 
														<td width="80" valign ="middle" align="right" > <?= number_format($total_po_qty,0) ?> </td> 
														<td rowspan="<?= 5 ?>" width="100" valign ="middle" align="center" > <?= $line_name ?> </td> 
													</tr> 
													<!-- Input -->		
													<?  if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF"; $i++;  ?>
													<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
														
														<td width="100" valign ="middle"> <b>  Input </b> </td>
														<?	
															$total_size_input = 0;
															foreach ($size_array as $size_id => $size ) 
															{ 
																$size_input = $size_wise_input[$size_id] ? $size_wise_input[$size_id]: 0;
																$total_size_input += $size_input;
																?>
																	<td width="50"  valign ="middle" align="right"><?= number_format($size_input,0)  ?> </td> 
																<?
															}
														?> 
														<td width="80" valign ="middle" align="right" > <?= number_format($total_size_input,0)  ?>  </td>
													</tr> 
													<!-- Output -->
													<?  if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF"; $i++;  ?>
													<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo ++$i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
														<td width="100" valign ="middle"> <b>  Output </b> </td>
														<?
															$total_size_output =0;
															foreach ($size_array as $size_id => $size ) 
															{
																$size_output = $size_wise_output[$size_id] ? $size_wise_output[$size_id]: 0;
																$total_size_output += $size_output;
																?>
																	<td width="50"  valign ="middle" align="right"><?= number_format($size_output,0) ?> </td> 
																<?
															}
														?> 
														<td width="80" valign ="middle" align="right" > <?= number_format($total_size_output,0) ?> </td>  
													</tr> 
													<!-- Reject -->
													<?  if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF"; $i++;  ?>
													<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo ++$i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
														<td width="100" valign ="middle"> <b>  Reject </b> </td>
														<?
															$total_size_reject = 0;
															foreach ($size_array as $size_id => $size ) 
															{
																$size_reject = $size_wise_reject[$size_id] ? $size_wise_reject[$size_id]: 0;
																$total_size_reject += $size_reject;
																?>
																	<td width="50"  valign ="middle" align="right"><?= number_format($size_reject,0) ?> </td> 
																<?
															}
														?> 
														<td width="80" valign ="middle" align="right" > <?= number_format($total_size_reject,0) ?> </td>  
													</tr> 
													<!-- Line Balance -->
													<tr bgcolor="#8DAFDA"> 
														<td width="100" valign ="middle"> <b>  Line Balance </b> </td>
														<? 
															$total_line_balance = $total_size_input - ($total_size_output + $total_size_reject); 
															$total_line_balance_title="Total Input Qty -( Total Output Qty + Total Reject Qty = $total_size_input - ($total_size_output + $total_size_reject) )" ;
															foreach ($size_array as $size_id => $size ) 
															{
																$size_input = $size_wise_input[$size_id] ? $size_wise_input[$size_id]: 0;
																$size_output = $size_wise_output[$size_id] ? $size_wise_output[$size_id]: 0;
																$size_reject = $size_wise_reject[$size_id] ? $size_wise_reject[$size_id]: 0; 
																$line_balance = $size_input -($size_output+$size_reject);
																$line_balance_title="Input Qty -( Output Qty + Reject Qty = $size_input -($size_output+$size_reject) )" ;
																?>
																	<td width="50"  valign ="middle" align="right" title="<?= $line_balance_title ?> "><b><?= number_format($line_balance,0) ?> </b></td> 
																<?
															}
															
														?> 
														<td width="80" valign ="middle" align="right" title="<?= $total_line_balance_title ?> "><b> <?= number_format($total_line_balance,0) ?> </b></td>  
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
				</div> 
			</fieldset> 
	    <?  
	}
	if ($type==2) //Color Wise //GBL REF FROM 2
	{ 
		$sql_cond  = ""; 
		$sql_cond .= $lc_company 	? " and c.company_name=$lc_company "		: "";
		$sql_cond .= $job_no 	 	? " and c.job_no LIKE '%$job_no%' "			: "";
		$sql_cond .= $style_no 		? " and c.style_ref_no='$style_no' "		: "";
		$sql_cond .= $order_no 		? " and b.po_number='$order_no' "			: "";
		$sql_cond .= $year_id     	? " and TO_CHAR(c.insert_date,'YYYY') = '$year_id' " : "";


		$sql = "SELECT a.id as color_size_id,a.order_quantity as po_qty,a.size_number_id as size_id,a.color_number_id as color,a.article_number,a.item_number_id as item,b.id as po_id,b.po_number,c.style_ref_no as style,c.id as job_id from wo_po_color_size_breakdown a,wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_id=c.id $sql_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 order by b.id,a.id";
		// echo $sql; die;
		$sql_res = sql_select($sql);  
		if (count($sql_res) == 0 ) 
		{
			echo "<h1 style='color:red; font-size: 17px;text-align:center;margin-top:20px;'> ** Data Not Found ** </h1>" ;
			die();
		}
		$size_id_array = $po_id_array = $order_data_array = $color_qty_array = array();
		foreach ($sql_res as  $v) 
		{
			$size_id_array[$v['SIZE_ID']] 	= $v['SIZE_ID'];
			$po_id_array[$v['PO_ID']] 		= $v['PO_ID'];

			$order_data_array[$v['ITEM']][$v['COLOR']]['ARTICLE'][$v['ARTICLE_NUMBER']] = $v['ARTICLE_NUMBER'];  
			$order_data_array[$v['ITEM']][$v['COLOR']]['PO_NUMBER'][$v['PO_ID']] 		= $v['PO_NUMBER']; 

			$order_data_array[$v['ITEM']][$v['COLOR']]['STYLE'] 		= $v['STYLE'];  
			// $order_data_array[$v['ITEM']][$v['COLOR']]['PO_NUMBER'] 	= $v['PO_NUMBER']; 

			$color_qty_array[$v['ITEM']][$v['COLOR']]['PO_QTY'] 		+= $v['PO_QTY'];  

			$size_wise_po_qty_array[$v['ITEM']][$v['COLOR']][$v['SIZE_ID']] += $v['PO_QTY'];  
		}   
		// pre($order_data_array); die;
		//=========================================================================================================
		//												CLEAR TEMP ENGINE
		// ==========================================================================================================
		$con = connect();
		execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form=161 and ref_from=2 ");
		oci_commit($con);   
		// =========================================================================================================
		//												INSERT DATA INTO TEMP ENGINE
		// =========================================================================================================
		fnc_tempengine("gbl_temp_engine", $user_id, 161, 2,$po_id_array, $empty_arr); 
		$prod_cond = "";
		$prod_cond.= $wo_company 	? " and b.serving_company in ($wo_company) ": "";
		$prod_cond.= $location_ids 	? " and b.location in($location_ids) "		: "";
		$prod_cond.= $floor_ids 	? " and b.floor_id in($floor_ids) "			: "";
		$prod_cond.= $line_ids 		? " and b.sewing_line in($line_ids) "		: "";

		$prod_sql = "SELECT a.production_qnty as prod_qty,a.production_type as prod_type,a.reject_qty,b.sewing_line,c.size_number_id as size_id,c.color_number_id as color,c.item_number_id as item,b.po_break_down_id as po_id,b.prod_reso_allo,c.article_number from pro_garments_production_dtls a,pro_garments_production_mst b,wo_po_color_size_breakdown c,gbl_temp_engine tmp where a.mst_id=b.id and a.color_size_break_down_id=c.id  and b.po_break_down_id=tmp.ref_val $prod_cond and a.production_type in(4,5) and tmp.entry_form=161 and tmp.ref_from=2 and tmp.user_id=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";
		// echo $prod_sql; die;
		$prod_sql_res = sql_select($prod_sql);  
		$prod_data_array = array();
		$line_wise_data_array = array();
		foreach ($prod_sql_res as  $v) 
		{  
			$prod_data_array[$v['ITEM']][$v['COLOR']]['PROD_QTY'][$v['PROD_TYPE']][$v['SIZE_ID']] 	+= $v['PROD_QTY']; 
			$prod_data_array[$v['ITEM']][$v['COLOR']]['REJECT_QTY'][$v['SIZE_ID']] += $v['REJECT_QTY'];   
			$prod_data_array[$v['ITEM']][$v['COLOR']]['ARTICLE'][$v['ARTICLE_NUMBER']] = $v['ARTICLE_NUMBER']; 
		}  
		// pre($prod_data_array); die;
		$size_str = implode(',',$size_id_array);
		$size_array= return_library_array( "SELECT id,size_name from lib_size where id in($size_str) and status_active=1 and is_deleted=0  order by sequence asc,size_name asc", "id", "size_name"  );
		// pre($size_array); die;

		// ============================================================================================================
		//												ROWSPAN CALCULATION
		// ============================================================================================================
		$color_span_array = $item_span_array  = $po_wise_row_array = array(); 
		$no_of_row = 0;
		$no_of_color = 0;
		foreach ($order_data_array as $color_id => $item_data_arr) 
		{   
			foreach ($item_data_arr as $item_id => $po_data_arr) 
			{  
				foreach ($po_data_arr as $po_id => $v) 
				{ 
					$item_span_array[$color_id][$item_id]++;
					$no_of_row ++;
					$color_span_array[$color_id]++; 
				}	
			}
		}
		// pre($item_span_array);

		// ============================================================================================================
		//												CLEAR TEMP ENGINE
		// ============================================================================================================
		
		execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form=161 and ref_from=2 ");
		
		oci_commit($con);  
		disconnect($con);
		ob_start();
		$width = 800+( count($size_array)*50 );
		?>  
			<fieldset>
				<div align="left" style="height:auto; width:<? echo $width+20;?>px; margin:0; padding:10px 0 10px 0;"> 
					<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="tbl_caption" align="center">
						<thead class="form_caption" >
							<tr>
								<td colspan="<?= 8 + count($size_array) ?>" align="center" style="font-size:18px; font-weight:bold" >Summary (Colour Wise)</td>
							</tr>
							<tr>
								<td colspan="<?= 8 + count($size_array) ?>" align="center" style="font-size:18px; font-weight:bold" > <?= $company_library[$lc_company] ?></td>
							</tr>
						</thead>
					</table>  
					<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
						<thead class="form_caption" >	  
							<tr> 
								<th width="120">Style </th>
								<th width="120">PO </th>
								<th width="100">Item </th>
								<th width="80"> Po Qty </th> 
								<th width="100">Color </th> 
								<th width="100" title="Only Production Article">Article </th> 
								<th width="100">Size </th> 
								<?
									foreach ($size_array as $size ) 
									{
										?>
											<th width="50"><?= $size ?> </th> 
										<?
									}
								?>
								<th width="80">Total </th>  
							</tr>
						</thead>
					</table>
					<div style="width:<?= $width+20;?>px; max-height:350px; float:left; overflow-y:scroll;" id="scroll_body">
						<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body" width="<?= $width; ?>" rules="all" align="left">
							<tbody>
								<?
								$i=$j=0;   
								foreach ($order_data_array as $item_id => $color_data_arr) 
								{  
									$k=0;$l=0; 
									foreach ($color_data_arr as $color_id => $v)  
									{ 
										//ROW SPAN VARIABLES
										
										//DATA
										$prod_arr = $prod_data_array[$item_id][$color_id]; 
										$po_qty   = $color_qty_array[$item_id][$color_id]['PO_QTY'];
										$size_wise_input  = $prod_arr['PROD_QTY'][4] ; 
										$size_wise_output = $prod_arr['PROD_QTY'][5] ;
										$size_wise_reject = $prod_arr['REJECT_QTY'];
										$size_wise_po_qty = $size_wise_po_qty_array[$item_id][$color_id];
											

										if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
										?>
											<!-- ORDER -->
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
												<?   
													if ($j==0) 
													{
														?> 
															<td rowspan="<?= 6* $no_of_row  ?>" width="120" valign ="middle" align="center" > <p> <?=  $v['STYLE'];?></p> </td>
														<? 
													} $j++;
												?>
												<td rowspan="<?= 6 ?>" width="120" valign ="middle" align="center" > <p> <?= implode(',',$v['PO_NUMBER']) ; ?> </p> </td>
												 
												<td rowspan="<?= 6  ?>" width="100"  valign ="middle" align="center" > <p> <?= $garments_item[$item_id] ?> </p> </td>    
													 
												<td rowspan="<?= 6 ?>" width="80" valign ="middle" align="center"> <?= number_format($po_qty,0)  ?> </td> 
												<td rowspan="<?= 6 ?>" width="100" valign ="middle" align="center" > <p> <?= $color_library[$color_id] ?> </p></td> 
												<td rowspan="<?= 6 ?>" width="100" valign ="middle" > <p> <?= implode(',',$prod_arr['ARTICLE'])  ?> </p> </td>
												<td width="100" valign ="middle"> <b>  Order </b> </td>
												<?
													$total_po_qty = 0;
													foreach ($size_array as $size_id => $size ) 
													{
														$po_size_qty = $size_wise_po_qty[$size_id] ? $size_wise_po_qty[$size_id] : 0 ;
														$total_po_qty += $po_size_qty;
														?>
															<td width="50"  valign ="middle" align="right"><?= number_format($po_size_qty,0)?> </td> 
														<?
													}
												?> 
												<td width="80" valign ="middle" align="right" > <?= number_format($total_po_qty,0) ?> </td> 
											</tr> 
											<!-- Input -->		
											<?  $i++; 
												if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF"; 
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
												
												<td width="100" valign ="middle"> <b>  Input </b> </td>
												<?	
													$total_size_input = 0;
													foreach ($size_array as $size_id => $size ) 
													{ 
														$size_input = $size_wise_input[$size_id] ? $size_wise_input[$size_id]: 0;
														$total_size_input += $size_input;
														?>
															<td width="50"  valign ="middle" align="right"><?= number_format($size_input,0) ?> </td> 
														<?
													}
												?> 
												<td width="80" valign ="middle" align="right" > <?= number_format($total_size_input,0) ?>  </td>
											</tr> 
											<!-- Output -->
											<?
												$i++;
												if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";  
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
												<td width="100" valign ="middle"> <b>  Output </b> </td>
												<?
													$total_size_output = 0;
													foreach ($size_array as $size_id => $size ) 
													{
														$size_output = $size_wise_output[$size_id] ? $size_wise_output[$size_id]: 0;
														$total_size_output += $size_output;
														?>
															<td width="50"  valign ="middle" align="right"><?= number_format($size_output,0) ?> </td> 
														<?
													}
												?> 
												<td width="80" valign ="middle" align="right" > <?= number_format($total_size_output,0) ?> </td>  
											</tr> 
											<!-- Reject -->
											<?
												$i++;
												if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";  
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
												<td width="100" valign ="middle"> <b>  Reject </b> </td>
												<?
													$total_size_reject = 0;
													foreach ($size_array as $size_id => $size ) 
													{
														$size_reject = $size_wise_reject[$size_id] ? $size_wise_reject[$size_id]: 0;
														$total_size_reject += $size_reject;
														?>
															<td width="50"  valign ="middle" align="right"><?= number_format($size_reject,0) ?> </td> 
														<?
													}
												?> 
												<td width="80" valign ="middle" align="right" > <?= number_format($total_size_reject,0) ?> </td>  
											</tr> 
											<!-- Line Balance -->
											<tr bgcolor="#8DAFDA"> 
												<td width="100" valign ="middle"> <b>  Line Balance </b> </td>
												<? 
													$total_line_balance = $total_size_input - ($total_size_output + $total_size_reject); 
													$total_line_balance_title="Total Input Qty -( Total Output Qty + Total Reject Qty = $total_size_input - ($total_size_output + $total_size_reject) )" ;
													foreach ($size_array as $size_id => $size ) 
													{
														$size_input = $size_wise_input[$size_id] ? $size_wise_input[$size_id]: 0;
														$size_output = $size_wise_output[$size_id] ? $size_wise_output[$size_id]: 0;
														$size_reject = $size_wise_reject[$size_id] ? $size_wise_reject[$size_id]: 0; 
														$line_balance = $size_input -($size_output+$size_reject);
														$line_balance_title="Input Qty -( Output Qty + Reject Qty = $size_input -($size_output+$size_reject) )" ;
														?>
															<td width="50"  valign ="middle" align="right" title="<?= $line_balance_title ?> "><b><?= number_format($line_balance,0) ?> </b></td> 
														<?
													}
													
												?> 
												<td width="80" valign ="middle" align="right" title="<?= $total_line_balance_title ?> "><b> <?= number_format($total_line_balance,0) ?> </b></td>  
											</tr>    
											<!--Balance --> 
											<tr bgcolor="#FFC">
												<td width="100" valign ="middle"> <b>  Balance </b> </td>
												<?
													$total_balance =  $total_size_output - $total_po_qty;
													$total_balance_title="Total Output Qty - Total PO Qty = $total_size_output - $total_po_qty" ;
													foreach ($size_array as $size_id => $size ) 
													{
														$po_size_qty = $size_wise_po_qty[$size_id] ? $size_wise_po_qty[$size_id] : 0 ; 
														$size_output = $size_wise_output[$size_id] ? $size_wise_output[$size_id]: 0; 
														$balance = $size_output - $po_size_qty;
														$balance_title="Output Qty - PO Qty = $size_output - $po_size_qty" ;
														?>
															<td width="50"  valign ="middle" align="right" title="<?= $balance_title ?> "><b><?= number_format($balance,0) ?> </b></td> 
														<?
													}
												?> 
												<td width="80" valign ="middle" align="right"title="<?= $total_balance_title ?> "><b><?= number_format($total_balance,0) ?>  </b> </td>  
											</tr> 
										<?
									} 
								}
								?>
							</tbody> 
						</table> 
					</div> 
				</div> 
			</fieldset> 
	    <?  
	}
	if ($type==3) //Style Wise //GBL REF FROM 3
	{ 
		$sql_cond  = ""; 
		$sql_cond .= $lc_company 	? " and c.company_name=$lc_company "		: "";
		$sql_cond .= $job_no 	 	? " and c.job_no LIKE '%$job_no%' "			: "";
		$sql_cond .= $style_no 		? " and c.style_ref_no='$style_no' "		: "";
		$sql_cond .= $order_no 		? " and b.po_number='$order_no' "			: "";
		$sql_cond .= $year_id     	? " and TO_CHAR(c.insert_date,'YYYY') = '$year_id' " : "";


		$sql = "SELECT  a.id as color_size_id,a.order_quantity as po_qty,a.size_number_id as size_id,a.color_number_id as color,a.article_number,a.item_number_id as item,b.id as po_id,b.po_number,c.style_ref_no as style,c.id as job_id from wo_po_color_size_breakdown a,wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_id=c.id $sql_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 order by a.id,b.id";
		// echo $sql; die;
		$sql_res = sql_select($sql);  
		if (count($sql_res) == 0 ) 
		{
			echo "<h1 style='color:red; font-size: 17px;text-align:center;margin-top:20px;'> ** Data Not Found ** </h1>" ;
			die();
		}
		$size_id_array = $po_id_array = $order_data_array = $item_qty_array = array();
		foreach ($sql_res as  $v) 
		{
			$size_id_array[$v['SIZE_ID']] 	= $v['SIZE_ID']; 
			$po_id_array[$v['PO_ID']] 		= $v['PO_ID'];
			$order_data_array[$v['ITEM']][$v['PO_ID']]['ARTICLE'][$v['ARTICLE_NUMBER']] = $v['ARTICLE_NUMBER'];  
			$order_data_array[$v['ITEM']][$v['PO_ID']]['STYLE'] 		= $v['STYLE'];  
			$order_data_array[$v['ITEM']][$v['PO_ID']]['PO_NUMBER'] 	= $v['PO_NUMBER']; 

			$item_qty_array[$v['ITEM']] 	+= $v['PO_QTY'];  

			$size_wise_po_qty_array[$v['ITEM']][$v['PO_ID']][$v['SIZE_ID']] += $v['PO_QTY'];  
		}   
		// pre($item_qty_array); die;
		//=========================================================================================================
		//												CLEAR TEMP ENGINE
		// ==========================================================================================================
		$con = connect();
		execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form=161 and ref_from=3 ");
		oci_commit($con);   
		// =========================================================================================================
		//												INSERT DATA INTO TEMP ENGINE
		// =========================================================================================================
		fnc_tempengine("gbl_temp_engine", $user_id, 161, 3,$po_id_array, $empty_arr); 
		$prod_cond = "";
		$prod_cond.= $wo_company 	? " and b.serving_company in ($wo_company) ": "";
		$prod_cond.= $location_ids 	? " and b.location in($location_ids) "		: "";
		$prod_cond.= $floor_ids 	? " and b.floor_id in($floor_ids) "			: "";
		$prod_cond.= $line_ids 		? " and b.sewing_line in($line_ids) "		: "";

		$prod_sql = "SELECT a.production_qnty as prod_qty,a.production_type as prod_type,a.reject_qty,b.sewing_line,c.size_number_id as size_id,c.color_number_id as color,c.item_number_id as item,b.po_break_down_id as po_id,b.prod_reso_allo,c.article_number from pro_garments_production_dtls a,pro_garments_production_mst b,wo_po_color_size_breakdown c,gbl_temp_engine tmp where a.mst_id=b.id and a.color_size_break_down_id=c.id  and b.po_break_down_id=tmp.ref_val $prod_cond and a.production_type in(4,5) and tmp.entry_form=161 and tmp.ref_from=3 and tmp.user_id=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";
		// echo $prod_sql; die;
		$prod_sql_res = sql_select($prod_sql);  
		$prod_data_array = array();
		$line_wise_data_array = array();
		foreach ($prod_sql_res as  $v) 
		{  
			$prod_data_array[$v['ITEM']][$v['PO_ID']]['PROD_QTY'][$v['PROD_TYPE']][$v['SIZE_ID']] += $v['PROD_QTY']; 
			$prod_data_array[$v['ITEM']][$v['PO_ID']]['REJECT_QTY'][$v['SIZE_ID']] += $v['REJECT_QTY'];   
		}  
		// pre($prod_data_array); die;
		$size_str = implode(',',$size_id_array);
		$size_array= return_library_array( "SELECT id,size_name from lib_size where id in($size_str) and status_active=1 and is_deleted=0  order by sequence asc,size_name asc", "id", "size_name"  );
		// pre($size_array); die;

		// ============================================================================================================
		//												ROWSPAN CALCULATION
		// ============================================================================================================
		$item_span_array  = $po_wise_row_array = array(); 
		$no_of_row = 0;
		$no_of_color = 0;
		foreach ($order_data_array as $item_id => $poArray) 
		{  
			foreach ($poArray as $po_id => $v) 
			{    
				$no_of_row ++; 
				$item_span_array[$item_id]++;
			}  
		}
		// pre($item_span_array);

		// ============================================================================================================
		//												CLEAR TEMP ENGINE
		// ============================================================================================================
		
		execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form=161 and ref_from=3 ");
		
		oci_commit($con);  
		disconnect($con);
		ob_start();
		$width = 800+( count($size_array)*50 );
		?>  
			<fieldset>
				<div align="left" style="height:auto; width:<? echo $width+20;?>px; margin:0; padding:10px 0 10px 0;"> 
					<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="tbl_caption" align="center">
						<thead class="form_caption" >
							<tr>
								<td colspan="<?= 8 + count($size_array) ?>" align="center" style="font-size:18px; font-weight:bold" >Summary (Style Wise)</td>
							</tr>
							<tr>
								<td colspan="<?= 8 + count($size_array) ?>" align="center" style="font-size:18px; font-weight:bold" > <?= $company_library[$lc_company] ?></td>
							</tr>
						</thead>
					</table>  
					<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
						<thead class="form_caption" >	  
							<tr> 
								<th width="120">Style </th>
								<th width="120">PO </th>
								<th width="100">Item </th>
								<th width="80"> Po Qty </th> 
								<th width="100">Size </th> 
								<?
									foreach ($size_array as $size ) 
									{
										?>
											<th width="50"><?= $size ?> </th> 
										<?
									}
								?>
								<th width="80">Total </th>  
							</tr>
						</thead>
					</table>
					<div style="width:<?= $width+20;?>px; max-height:350px; float:left; overflow-y:scroll;" id="scroll_body">
						<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body" width="<?= $width; ?>" rules="all" align="left">
							<tbody>
								<?
								$i=$j=0;   
								foreach ($order_data_array as $item_id => $poArray) 
								{  
									$k=0;
									foreach ($poArray as $po_id => $v) 
									{  
										//ROW SPAN VARIABLES 
										$item_rowspan = $item_span_array[$item_id];

										//DATA
										$prod_arr = $prod_data_array[$item_id][$po_id]; 
										$po_qty   = $item_qty_array[$item_id];
										$size_wise_input  = $prod_arr['PROD_QTY'][4] ; 
										$size_wise_output = $prod_arr['PROD_QTY'][5] ;
										$size_wise_reject = $prod_arr['REJECT_QTY'];
										$size_wise_po_qty = $size_wise_po_qty_array[$item_id][$po_id];
											

										if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
										?>
											<!-- ORDER -->
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
												
												<?   
													if ($j==0) 
													{
														?>
															<td rowspan="<?= 6* $no_of_row  ?>" width="120" valign ="middle" align="center" > <p> <?=  $v['STYLE'];?></p> </td>
														<? 
													} $j++;
												?> 	
													
												<td rowspan="<?= 6 ?>" width="120" valign ="middle"  align="center"> <p> <?=  $v['PO_NUMBER']; ?> </p> </td>
												<?   
													if ($k==0) 
													{
														?>
															<td rowspan="<?= 6 * $item_rowspan ?>" width="100"  valign ="middle"  align="center"> <p> <?= $garments_item[$item_id] ?> </p> </td>
															<td rowspan="<?= 6 * $item_rowspan ?>" width="80" valign ="middle" align="center"> <?= number_format($po_qty,0) ?> </td>  
														<? 
													} $k++;
												?>		 
												
												<td width="100" valign ="middle"> <b>  Order </b> </td>
												<?
													$total_po_qty = 0;
													foreach ($size_array as $size_id => $size ) 
													{
														$po_size_qty = $size_wise_po_qty[$size_id] ? $size_wise_po_qty[$size_id] : 0 ;
														$total_po_qty += $po_size_qty;
														?>
															<td width="50"  valign ="middle" align="right"><?=  number_format($po_size_qty,0)?> </td> 
														<?
													}
												?> 
												<td width="80" valign ="middle" align="right" > <?= number_format($total_po_qty,0) ?> </td> 
											</tr> 
											<!-- Input -->		
											<?  $i++; 
												if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF"; 
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
												
												<td width="100" valign ="middle"> <b>  Input </b> </td>
												<?	
													$total_size_input = 0;
													foreach ($size_array as $size_id => $size ) 
													{ 
														$size_input = $size_wise_input[$size_id] ? $size_wise_input[$size_id]: 0;
														$total_size_input += $size_input;
														?>
															<td width="50"  valign ="middle" align="right"><?= number_format($size_input,0) ?> </td> 
														<?
													}
												?> 
												<td width="80" valign ="middle" align="right" > <?= number_format($total_size_input,0) ?>  </td>
											</tr> 
											<!-- Output -->
											<?
												$i++;
												if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";  
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
												<td width="100" valign ="middle"> <b>  Output </b> </td>
												<?
													$total_size_output = 0;
													foreach ($size_array as $size_id => $size ) 
													{
														$size_output = $size_wise_output[$size_id] ? $size_wise_output[$size_id]: 0;
														$total_size_output += $size_output;
														?>
															<td width="50"  valign ="middle" align="right"><?= number_format($size_output,0) ?> </td> 
														<?
													}
												?> 
												<td width="80" valign ="middle" align="right" > <?= number_format($total_size_output,0) ?> </td>  
											</tr> 
											<!-- Reject -->
											<?
												$i++;
												if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";  
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
												<td width="100" valign ="middle"> <b>  Reject </b> </td>
												<?
													$total_size_reject = 0;
													foreach ($size_array as $size_id => $size ) 
													{
														$size_reject = $size_wise_reject[$size_id] ? $size_wise_reject[$size_id]: 0;
														$total_size_reject += $size_reject;
														?>
															<td width="50"  valign ="middle" align="right"><?= number_format($size_reject,0) ?> </td> 
														<?
													}
												?> 
												<td width="80" valign ="middle" align="right" > <?= number_format($total_size_reject,0) ?> </td>  
											</tr> 
											<!-- Line Balance -->
											<tr bgcolor="#8DAFDA"> 
												<td width="100" valign ="middle"> <b>  Line Balance </b> </td>
												<? 
													$total_line_balance = 0;
													$total_line_balance = $total_size_input - ($total_size_output + $total_size_reject); 
													$total_line_balance_title="Total Input Qty -( Total Output Qty + Total Reject Qty = $total_size_input - ($total_size_output + $total_size_reject) )" ; 
													foreach ($size_array as $size_id => $size ) 
													{
														$size_input = $size_wise_input[$size_id] ? $size_wise_input[$size_id]: 0;
														$size_output = $size_wise_output[$size_id] ? $size_wise_output[$size_id]: 0;
														$size_reject = $size_wise_reject[$size_id] ? $size_wise_reject[$size_id]: 0; 
														$line_balance = $size_input -($size_output+$size_reject);
														$line_balance_title="Input Qty -( Output Qty + Reject Qty = $size_input -($size_output+$size_reject) )" ;
														?>
															<td width="50"  valign ="middle" align="right" title="<?= $line_balance_title ?> "><b><?= number_format($line_balance,0) ?> </b></td> 
														<?
													}
													
												?> 
												<td width="80" valign ="middle" align="right" title="<?= $total_line_balance_title ?> "><b> <?= number_format($total_line_balance,0) ?> </b></td>  
											</tr>    
											<!--Balance --> 
											<tr bgcolor="#FFC">
												<td width="100" valign ="middle"> <b>  Balance </b> </td>
												<?
													$total_balance = $total_size_output - $total_po_qty;
													$total_balance_title="Total Output Qty - Total PO Qty = $total_size_output - $total_po_qty" ;
													foreach ($size_array as $size_id => $size ) 
													{
														$po_size_qty = $size_wise_po_qty[$size_id] ? $size_wise_po_qty[$size_id] : 0 ; 
														$size_output = $size_wise_output[$size_id] ? $size_wise_output[$size_id]: 0; 
														$balance = $size_output - $po_size_qty;
														$balance_title="Output Qty - PO Qty= $size_output - $po_size_qty" ;
														?>
															<td width="50"  valign ="middle" align="right" title="<?= $balance_title ?> "><b><?= number_format($balance,0) ?> </b></td> 
														<?
													}
												?> 
												<td width="80" valign ="middle" align="right"title="<?= $total_balance_title ?> "><b><?= number_format($total_balance,0) ?>  </b> </td>  
											</tr> 
										<?
									}
								}
								?>
							</tbody> 
						</table> 
					</div> 
				</div> 
			</fieldset> 
	    <?  
	}
	if ($type==4) //Shipment Summary //GBL REF FROM 4
	{ 
		$sql_cond  = ""; 
		$sql_cond .= $lc_company 	? " and c.company_name=$lc_company "		: "";
		$sql_cond .= $job_no 	 	? " and c.job_no LIKE '%$job_no%' "			: "";
		$sql_cond .= $style_no 		? " and c.style_ref_no='$style_no' "		: "";
		$sql_cond .= $order_no 		? " and b.po_number='$order_no' "			: "";
		$sql_cond .= $year_id     	? " and TO_CHAR(c.insert_date,'YYYY') = '$year_id' " : "";


		$sql = "SELECT a.order_quantity as po_qty,a.size_number_id as size_id,a.color_number_id as color,a.article_number,a.item_number_id as item,b.id as po_id,b.po_number,c.style_ref_no as style,c.id as job_id from wo_po_color_size_breakdown a,wo_po_break_down b, wo_po_details_master c where a.po_break_down_id=b.id and b.job_id=c.id $sql_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 ";
		// echo $sql; die;
		$sql_res = sql_select($sql);  
		if (count($sql_res) == 0 ) 
		{
			echo "<h1 style='color:red; font-size: 17px;text-align:center;margin-top:20px;'> ** Data Not Found ** </h1>" ;
			die();
		}
		$size_id_array = $po_id_array = $order_data_array = $item_qty_array = array();
		foreach ($sql_res as  $v) 
		{
			$size_id_array[$v['SIZE_ID']] 	= $v['SIZE_ID']; 
			$po_id_array[$v['PO_ID']] 		= $v['PO_ID'];
			$order_data_array[$v['ITEM']][$v['PO_ID']]['ARTICLE'][$v['ARTICLE_NUMBER']] = $v['ARTICLE_NUMBER'];  
			$order_data_array[$v['ITEM']][$v['PO_ID']]['STYLE'] 		= $v['STYLE'];  
			$order_data_array[$v['ITEM']][$v['PO_ID']]['PO_NUMBER'] 	= $v['PO_NUMBER']; 

			$item_qty_array[$v['ITEM']] 	+= $v['PO_QTY'];  

			$size_wise_po_qty_array[$v['ITEM']][$v['PO_ID']][$v['SIZE_ID']] += $v['PO_QTY'];  
		}   
		// pre($item_qty_array); die;
		//=========================================================================================================
		//												CLEAR TEMP ENGINE
		// ==========================================================================================================
		$con = connect();
		execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form=161 and ref_from=4 ");
		oci_commit($con);   
		// =========================================================================================================
		//												INSERT DATA INTO TEMP ENGINE
		// =========================================================================================================
		fnc_tempengine("gbl_temp_engine", $user_id, 161, 4,$po_id_array, $empty_arr); 


		$exfact_cond = "";
		$exfact_cond.= $wo_company 	? " and a.delivery_company_id in ($wo_company) ": ""; 

		$ex_fact_sql = "SELECT c.production_qnty as prod_qty, d.size_number_id as size_id,d.color_number_id as color,d.item_number_id as item,b.po_break_down_id as po_id from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b,pro_ex_factory_dtls c,wo_po_color_size_breakdown d,gbl_temp_engine tmp where b.delivery_mst_id=a.id and c.mst_id=b.id and c.color_size_break_down_id=d.id and b.po_break_down_id=tmp.ref_val $exfact_cond and tmp.entry_form=161 and tmp.ref_from=4 and tmp.user_id=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 ";
		// echo $ex_fact_sql; die;
		$ex_fact_sql_res = sql_select($ex_fact_sql);  
		$ex_fact_data_array = array(); 
		foreach ($ex_fact_sql_res as  $v) 
		{  
			$ex_fact_data_array[$v['ITEM']][$v['PO_ID']]['PROD_QTY'][$v['SIZE_ID']] += $v['PROD_QTY'];  
		}  
		// pre($ex_fact_data_array); die;
		$size_str = implode(',',$size_id_array);
		$size_array= return_library_array( "SELECT id,size_name from lib_size where id in($size_str) and status_active=1 and is_deleted=0 order by sequence asc,size_name asc", "id", "size_name"  );
		// pre($size_array); die;

		// ============================================================================================================
		//												ROWSPAN CALCULATION
		// ============================================================================================================
		$item_span_array  = $gt_size_wise_po_qty = array(); 
		$no_of_row = 0;
		$no_of_color = 0;
		foreach ($order_data_array as $item_id => $poArray) 
		{  
			foreach ($poArray as $po_id => $v) 
			{  
				$size_wise_po_qty = $size_wise_po_qty_array[$item_id][$po_id]; 
				foreach ($size_array as $size_id => $size ) 
				{ 
					$po_size_qty = $size_wise_po_qty[$size_id] ? $size_wise_po_qty[$size_id] : 0 ;
					$gt_size_wise_po_qty[$size_id] += $po_size_qty; 
				}

				// ROW SPAN
				$no_of_row ++; 
				$item_span_array[$item_id]++;
			}  
		}
		// pre($item_span_array);

		// ============================================================================================================
		//												CLEAR TEMP ENGINE
		// ============================================================================================================
		
		execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form=161 and ref_from=4 ");
		
		oci_commit($con);  
		disconnect($con);
		ob_start();
		$width = 800+( count($size_array)*50 );
		?>  
			<fieldset>
				<div align="left" style="height:auto; width:<? echo $width+20;?>px; margin:0; padding:10px 0 10px 0;"> 
					<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="tbl_caption" align="center">
						<thead class="form_caption" >
							<tr>
								<td colspan="<?= 8 + count($size_array) ?>" align="center" style="font-size:18px; font-weight:bold" >Summary (Shipment wise)</td>
							</tr>
							<tr>
								<td colspan="<?= 8 + count($size_array) ?>" align="center" style="font-size:18px; font-weight:bold" > <?= $company_library[$lc_company] ?></td>
							</tr>
						</thead>
					</table>  
					<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
						<thead class="form_caption" >	  
							<tr> 
								<th width="120">Style </th>
								<th width="120">PO </th>
								<th width="100">Item </th> 
								<th width="100">Size </th> 
								<?
									foreach ($size_array as $size ) 
									{
										?>
											<th width="50"><?= $size ?> </th> 
										<?
									}
								?>
								<th width="80">Total </th>  
							</tr>
						</thead>
					</table>
					<div style="width:<?= $width+20;?>px; max-height:350px; float:left; overflow-y:scroll;" id="scroll_body">
						<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body" width="<?= $width; ?>" rules="all" align="left">
							<tbody>
								<tr bgcolor="#FFC">
									<td colspan="4" align="right"><b>Total Order Quantity</b></td>
									<?
										$gt_po_qty = 0;
										foreach ($size_array as $size_id => $size ) 
										{
											
											$gt_po_size_qty = $gt_size_wise_po_qty[$size_id] ? $gt_size_wise_po_qty[$size_id] : 0 ;
											$gt_po_qty += $gt_po_size_qty;
											?>
												<td width="50"  valign ="middle" align="right"> <b><?= number_format($gt_po_size_qty,0)?></b></td> 
											<?
										}
									?> 
									<td width="80" valign ="middle" align="right" > <b> <?= number_format($gt_po_qty,0) ?> </b> </td> 
								</tr>
								<?
								$i=$j=0;   
								$gt_size_wise_ship_bal = $gt_size_wise_ship_bal_title = array();
								foreach ($order_data_array as $item_id => $poArray) 
								{  
									$k=0;
									foreach ($poArray as $po_id => $v) 
									{  
										//ROW SPAN VARIABLES 
										$item_rowspan = $item_span_array[$item_id];

										//DATA 
										$po_qty   = $item_qty_array[$item_id];
										$size_wise_ex_fact  =  $ex_fact_data_array[$item_id][$po_id]['PROD_QTY'];
										$size_wise_po_qty = $size_wise_po_qty_array[$item_id][$po_id];   
											

										if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
										?>
											<!-- ORDER -->
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
												
												<?   
													if ($j==0) 
													{
														?>
															<td rowspan="<?= 3* $no_of_row  ?>" width="120" valign ="middle" align="center" > <p> <?=  $v['STYLE'];?></p> </td>
														<? 
													} $j++;
												?> 	
													
												<td rowspan="3" width="120" valign ="middle"  align="center"> <p> <?=  $v['PO_NUMBER']; ?> </p> </td>
												<td rowspan="3" width="100"  valign ="middle"  align="center"> <p> <?= $garments_item[$item_id] ?> </p> </td> 
												<td width="100" valign ="middle"> <b>  Order </b> </td>
												<?
													$total_po_qty = 0;
													foreach ($size_array as $size_id => $size ) 
													{
														
														$po_size_qty = $size_wise_po_qty[$size_id] ? $size_wise_po_qty[$size_id] : 0 ;
														$total_po_qty += $po_size_qty;
														?>
															<td width="50"  valign ="middle" align="right"><?= number_format($po_size_qty,0)?> </td> 
														<?
													}
												?> 
												<td width="80" valign ="middle" align="right" > <b> <?= number_format($total_po_qty,0) ?></b> </td> 
											</tr> 
											<!-- Input -->		
											<?  $i++; 
												if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF"; 
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
												
												<td width="100" valign ="middle"> <b>  Shipment  </b> </td>
												<?	
													$total_ex_fact_qty = 0;
													foreach ($size_array as $size_id => $size ) 
													{ 
														$ex_fact_qty = $size_wise_ex_fact[$size_id] ? $size_wise_ex_fact[$size_id]: 0;
														$total_ex_fact_qty += $ex_fact_qty;
														?>
															<td width="50"  valign ="middle" align="right"><?= number_format($ex_fact_qty,0) ?></td> 
														<?
													}
												?> 
												<td width="80" valign ="middle" align="right" > <b><?= number_format($total_ex_fact_qty,0) ?></b> </td>
											</tr>     
											<!--Balance --> 
											<tr bgcolor="#FFC">
												<td width="100" valign ="middle"> <b>  Ship Bal. </b> </td>
												<?
													
													$total_balance = $total_po_qty - $total_ex_fact_qty;
													$total_balance_title="Total PO Qty - Total Shipment Qty = $total_po_qty - $total_ex_fact_qty" ;
													foreach ($size_array as $size_id => $size ) 
													{
														$po_size_qty = $size_wise_po_qty[$size_id] ? $size_wise_po_qty[$size_id] : 0 ; 
														$ex_fact_qty = $size_wise_ex_fact[$size_id] ? $size_wise_ex_fact[$size_id]: 0;
														$balance = $po_size_qty -$ex_fact_qty;
														$balance_title="PO Qty - Shipment  Qty = $po_size_qty-$ex_fact_qty" ;

														$gt_size_wise_ship_bal[$size_id] += $balance; 
														$gt_size_wise_ship_bal_title[$size_id] .= $balance ."+"; 
														?>
															<td width="50"  valign ="middle" align="right" title="<?= $balance_title ?> "><b><?= number_format($balance,0) ?> </b></td> 
														<?
													}
												?> 
												<td width="80" valign ="middle" align="right"title="<?= $total_balance_title ?> "><b><?= number_format($total_balance,0) ?>  </b> </td>  
											</tr> 
										<?
									}
								}
								?>
							</tbody> 
							 <tfoot>
								<tr>
									<th colspan="4" >Total Ship Bal. </th>
									<? 
										foreach ($size_array as $size_id => $size ) 
										{
											$gt_balance  		= $gt_size_wise_ship_bal[$size_id];
											$gt_balance_title  	= $gt_size_wise_ship_bal_title[$size_id];
											$gt_total_balance  += $gt_balance;
											?>
												<th width="50"  valign ="middle" align="right" title="<?= trim($gt_balance_title,'+')  ?> "> <b><?= number_format($gt_balance,0) ?></b> </th> 
											<?
										}
									?>
									<th width="80" valign ="middle" align="right" ><b><?= number_format($gt_total_balance,0) ?>  </b> </th>  
								</tr>
							 </tfoot>
						</table> 
					</div> 
				</div> 
			</fieldset> 
	    <?  
	}

  	foreach (glob($user_id."_*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=$user_id."_".time().".xls";
	$create_new_excel = fopen($name, 'w');
	$is_created = fwrite($create_new_excel,ob_get_contents());
	//$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
	echo "####".$name;
	exit();
}
 
?>
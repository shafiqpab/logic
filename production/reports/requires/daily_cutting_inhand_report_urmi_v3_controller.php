<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
require_once("../../../ext_resource/excel/excel/vendor/autoload.php");
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );$color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );	
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 
$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 order by a.buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );//load_drop_down( 'requires/daily_knitting_production_report_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_machine', 'machine_td' );$location_cond
  exit();	 
}
if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";
//item style------------------------------//

if ($action=="load_drop_down_location")
{
    extract($_REQUEST);
    $choosenCompany = $choosenCompany;  
	echo create_drop_down( "cbo_location_name", 200, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in( $choosenCompany) group by id,location_name  order by location_name","id,location_name", 0, "-- Select location --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_floor")
{
    extract($_REQUEST);
    $choosenLocation = $choosenLocation;  
	echo create_drop_down( "cbo_floor_name", 200, "SELECT id,floor_name from lib_prod_floor where location_id in( $choosenLocation ) and status_active =1 and is_deleted=0 group by id,floor_name order by floor_name","id,floor_name", 0, "-- Select Floor --", $selected, "" );
	exit();
}

if($action=="job_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $buyer;die;
	?>
	
	<script>
    function js_set_value(id)
    {
		//alert(id);
		document.getElementById('selected_id').value=id;
		parent.emailwindow.hide();
    }
    </script>
    </head>
    <body>
    <div align="center" style="width:820px;">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:800px;">
            <table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Company</th>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" id="selected_id" name="selected_id" />
                </thead>
                <tbody>
                	<tr class="general">
                    	<td align="center"> 
							<?
                                echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>
                        </td>
                        <td align="center">
                        	 <? 
								//echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								if($buyer>0) $buy_cond=" and a.id=$buyer";
								echo create_drop_down( "cbo_buyer_name", 140, "select a.id,a.buyer_name from lib_buyer a where a.status_active=1 and a.is_deleted=0 $buy_cond order by a.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0,"" );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>', 'job_popup_search_list_view', 'search_div', 'daily_cutting_inhand_report_urmi_v3_controller', 'setFilterGrid(\'table_body2\',-1)');" style="width:100px;" />
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

if ($action=="job_popup_search_list_view")
{
  	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($company_id,$buyer_id,$search_type,$search_value,$cbo_year)=explode('**',$data);
	if($company_id==0)
	{
		echo "Please Select Company Name";
		die;
	}
	//echo $company_id."==".$buyer_id."==".$search_type."==".$search_value."==".$cbo_year;die;
	if($search_type==1 && $search_value!=''){
		$search_con=" and a.job_no like('%$search_value')";	
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value%')";	
	}

	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_cond="";
		}
	}
	else
	{
		$buyer_cond=" and a.buyer_name=$buyer_id";
	}
	
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0)
		{
			$year_cond=" and YEAR(a.insert_date)=$cbo_year";
		}
		else
		{
			$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";	
		}
	}
	else $year_cond="";
	
	if($db_type==2)
	{
		// $group_field="LISTAGG(CAST(b.po_number AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY b.po_number) as po_number";
		$year_field="to_char(a.insert_date,'YYYY')";
	} 
	else if($db_type==0) 
	{
		// $group_field="group_concat(distinct b.po_number ) as po_number";
		$year_field="YEAR(a.insert_date)";
	}

	$arr=array (2=>$company_arr,3=>$buyer_arr);
	$sql= "SELECT a.ID, a.job_no_prefix_num, a.JOB_NO, a.COMPANY_NAME,a.BUYER_NAME,a.STYLE_REF_NO,$year_field as YEAR,b.PO_NUMBER
	from wo_po_details_master a,  wo_po_break_down b 
	where a.job_no=b.job_no_mst and b.status_active in(1,2,3) and a.company_name=$company_id $buyer_cond $year_cond $search_con 
	group by a.id, a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,a.insert_date,b.po_number
	order by a.id desc";
	//echo $sql;//die;
	$rows=sql_select($sql);
	$data_array = array();
	foreach ($rows as $row) 
	{
		$data_array[$row['JOB_NO']]['id'] 			= $row['ID'];
		$data_array[$row['JOB_NO']]['company_name'] = $row['COMPANY_NAME'];
		$data_array[$row['JOB_NO']]['buyer_name'] 	= $row['BUYER_NAME'];
		$data_array[$row['JOB_NO']]['year'] 		= $row['YEAR'];
		$data_array[$row['JOB_NO']]['style_ref_no'] = $row['STYLE_REF_NO'];
		$data_array[$row['JOB_NO']]['po_number'] 	.= $row['PO_NUMBER'].", ";
	}
	?>
    <table width="800" border="1" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="120">Company</th>
                <th width="120">Buyer</th>
                <th width="50">Year</th>
                <th width="120">Job no</th>
                <th width="120">Style</th>
                <th>Po number</th>
                
            </tr>
       </thead>
    </table>
    <div style="max-height:820px; overflow:auto;">
    <table id="table_body2" width="800" border="1" rules="all" class="rpt_table">
     <? 
         $i=1;
         foreach($data_array as $job_no=>$data)
         {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			// $po_num=implode(",",array_unique(explode(",",$data[csf('po_number')])));
			?>
			<tr bgcolor="<? echo  $bgcolor;?>" onClick="js_set_value('<? echo $data['id']; ?>'+'_'+'<? echo $job_no; ?>')" style="cursor:pointer;">
                <td valign="middle" width="30" align="center"><? echo $i; ?></td>
                <td valign="middle" width="120"><p><? echo $company_arr[$data['company_name']]; ?></p></td>
                <td valign="middle" width="120"><p><? echo $buyer_short_library[$data['buyer_name']]; ?></p></td>
                <td valign="middle" align="center" width="50"><p><? echo $data['year']; ?></p></td>
                <td valign="middle" width="120"><p><? echo $job_no; ?></p></td>
                <td valign="middle" width="120"><p><? echo $data['style_ref_no']; ?></p></td>
                <td><p><? echo chop($data['po_number'],','); ?></p></td>
			</tr>
			<? 
			$i++; 
		} 
		?>
    </table>
    </div>
    <?
	 
	
	exit();
}

 


//order wise browse------------------------------//
if($action=="order_wise_search")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
	<script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
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
		</script>
	<?
	extract($_REQUEST);
	//echo $job_no;die;
	if($buyer==0) $buyer_name=""; else $buyer_name="and b.buyer_name=$buyer";
	$job_cond='';
	if(str_replace("'","",$job_id)!="")  $job_cond="and b.id in(".str_replace("'","",$job_id).")";
    else  if (str_replace("'","",$job_no)!="") $job_cond="and a.job_no_mst='".$job_no."'";
	else if($cbo_year!=0)
	{
		if($db_type==0) $job_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=".str_replace("'","",$cbo_year)." ";
		if($db_type==2) $job_cond=" and extract( year from b.insert_date)=".str_replace("'","",$cbo_year)."";
	}
	
	$sql = "SELECT distinct a.id,a.po_number,b.style_ref_no,b.job_no_prefix_num,$insert_year as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active in(1,2,3)  $company_name $job_cond  $buyer_name $style_cond";
	//echo $sql;//die;
	echo create_list_view("list_view", "Year,Job No,Style Ref,Order Number","50,100,120,150,","550","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "year,job_no_prefix_num,style_ref_no,po_number", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}

$colorname_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
$country_arr=return_library_array( "select id, country_name from   lib_country", "id", "country_name");
$floor_arr=return_library_array( "select id, floor_name from   lib_prod_floor", "id", "floor_name");


if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$hidden_order_id=str_replace("'","",$hidden_order_id);
	//$txt_production_date=str_replace("'","",$txt_production_date);
	$job_po_id="";
	if(str_replace("'","",$txt_job_no)!="")
	{
		if($db_type==0)
		{
			$job_po_id=return_field_value("group_concat(b.id) as po_id","wo_po_break_down b","b.job_no_mst=$txt_job_no","po_id");
		}
		else
		{
			$job_po_id=return_field_value("listagg(cast(b.id as varchar(4000)),',') within group(order by id) as po_id","wo_po_break_down b","b.job_no_mst=$txt_job_no","po_id");
		}
	}
	
	//echo $job_po_id;die;
	
	$order_cond_lay="";
	$order_cond_prod="";
	
	if($hidden_order_id!="")
	{
		$order_cond.=" and a.po_break_down_id in($hidden_order_id)";
		$order_cond_lay.=" and c.order_id in($hidden_order_id)";
	}
	elseif($job_po_id!="")
	{
		$order_cond.=" and a.po_break_down_id in($job_po_id)";
		$order_cond_lay.=" and c.order_id in($job_po_id)";
	}
	 
	
	$cbo_work_company_name=str_replace("'","",$cbo_work_company_name);
	$cbo_location_name=str_replace("'","",$cbo_location_name);
	$cbo_floor_name=str_replace("'","",$cbo_floor_name);
	$gmts_loc_floor_cond="";
	$gmts_loc_floor_cond_cut="";
	$gmts_loc_floor_cond_delv="";
	$gmts_loc_floor_cond_exfac="";
	$gmts_loc_floor_cond_left="";
	if($cbo_location_name)
	{
		$gmts_loc_floor_cond_cut.= " and a.location_id in($cbo_location_name)  ";
		$gmts_loc_floor_cond_delv.= " and m.location_id in($cbo_location_name)  ";
		$gmts_loc_floor_cond.= " and a.location in($cbo_location_name)  ";
		$gmts_loc_floor_cond_exfac.= " and m.delivery_location_id in($cbo_location_name)  ";
		$gmts_loc_floor_cond_left.= " and a.WORKING_LOCATION_ID in($cbo_location_name)  ";
	}
	
	if($cbo_floor_name)
	{
		$gmts_loc_floor_cond.= " and a.floor_id in ($cbo_floor_name) ";
		$gmts_loc_floor_cond_cut.= " and a.floor_id in ($cbo_floor_name) ";
		$gmts_loc_floor_cond_delv.= " and m.floor_id in ($cbo_floor_name) ";
		$gmts_loc_floor_cond_exfac.= " and m.delivery_floor_id in ($cbo_floor_name) ";
		$gmts_loc_floor_cond_left.= " and a.WORKING_FLOOR_ID in ($cbo_floor_name) ";
	}
	$floor_group=trim(str_replace("'","",$txt_floor_group));
	if($floor_group)
	{
		$floor_group_sql=sql_select("SELECT   id  FROM lib_prod_floor where group_name ='$floor_group' and status_active=1 ");
		$floor_group_arr=array();
		$floor_group_arr[0]=0;
		foreach($floor_group_sql as $fl)
			$floor_group_arr[$fl[csf("id")]]=$fl[csf("id")];
		$all_floor_by_group=implode(",",$floor_group_arr);

		$gmts_loc_floor_cond.= " and a.floor_id in ($all_floor_by_group) ";
		$gmts_loc_floor_cond_cut.= " and a.floor_id in ($all_floor_by_group) ";
		$gmts_loc_floor_cond_delv.= " and m.floor_id in ($all_floor_by_group) ";
		$gmts_loc_floor_cond_exfac.= " and m.delivery_floor_id in ($all_floor_by_group) ";
		$gmts_loc_floor_cond_left.= " and a.WORKING_FLOOR_ID in ($all_floor_by_group) ";
		

	}

	 
	if($type==1)
	{
		/* $sql=sql_select( "SELECT  c.order_id from ppl_cut_lay_mst a, ppl_cut_lay_bundle c 
		where a.id=c.mst_id and a.status_active=1 and c.status_active=1 and a.working_company_id in($cbo_work_company_name) and a.entry_date=$txt_production_date");
		foreach ($sql as $v) 
		{
			$lay_order_arr[$v['ORDER_ID']] = $v['ORDER_ID'];
		}
		
		$sql=sql_select( "SELECT  po_break_down_id as ORDER_ID from PRO_GARMENTS_PRODUCTION_MST  
		where production_date=$txt_production_date and status_active=1 and serving_company in($cbo_work_company_name)");
		foreach ($sql as $v) 
		{
			$lay_order_arr[$v['ORDER_ID']] = $v['ORDER_ID'];
		}
		unset($sql);
		// print_r($lay_order_arr);die;
		$lay_po_id_conds = where_con_using_array($lay_order_arr,0,"d.id"); */
		 

		$sql_lay=" SELECT a.working_company_id, b.gmt_item_id, c.order_id, c.country_id, b.color_id,(case when a.entry_date=$txt_production_date then c.size_qty else 0 end )  as today_lay,  (c.size_qty )  as total_lay
		from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c,wo_po_break_down d 
		where a.id=b.mst_id and b.id=c.dtls_id and c.order_id=d.id and d.is_deleted=0 and d.shiping_status<>3   and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.working_company_id in($cbo_work_company_name) $gmts_loc_floor_cond_cut  $order_cond_lay ";  

		
		$sql_lay_result=sql_select($sql_lay);
		$production_data=$porduction_ord_id=$lay_order_id=array();
 		$row_generate_cond_array=array();

		foreach($sql_lay_result as $row)
		{
			$row_generate_cond_array[$row[csf("order_id")]]=$row[csf("order_id")];
			if($row[csf("order_id")])
			{
				$lay_order_id[$row[csf("order_id")]]=$row[csf("order_id")];
			}
 			
			$production_data[$row[csf("order_id")]][$row[csf("gmt_item_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_lay"] +=$row[csf("today_lay")];
			$production_data[$row[csf("order_id")]][$row[csf("gmt_item_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["total_lay"] +=$row[csf("total_lay")];
			 
		}
		unset($sql_lay_result);
		/* $lay_order_cond_prod="";
		if($db_type==2 && count($lay_order_id)>999)
		{
			$chunk=array_chunk($lay_order_id,999);
			foreach($chunk as $rows)
			{
				$vals=implode(",", $rows);
				if($lay_order_cond_prod=="") $lay_order_cond_prod.=" and ( a.po_break_down_id in ($vals) ";
				else  $lay_order_cond_prod.=" or   a.po_break_down_id in ($vals) ";

			}
			$lay_order_cond_prod.=" )";

		}
		else
		{
			$values=implode(",",$lay_order_id);
			if(!$values)$values=0;
			$lay_order_cond_prod=" and a.po_break_down_id in ($values) ";
		} */		

		// ============================= store data in gbl table ==============================
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from =1 and ENTRY_FORM=165");
		oci_commit($con);

		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 165, 1, $lay_order_id, $empty_arr);//Po ID
		disconnect($con);
		
		
		$production_sql=" SELECT a.production_date, c.po_break_down_id as order_id, c.item_number_id, c.country_id, c.color_number_id as color_id, (b.production_qnty) as all_today_production_qnty,
		(CASE WHEN   a.production_date=$txt_production_date and  b.production_type =1 THEN b.production_qnty  ELSE 0 END) AS today_cutting_qnty,
		(CASE WHEN      b.production_type =1 THEN b.production_qnty  ELSE 0 END) AS total_cutting_qnty,


		(CASE WHEN   a.production_date=$txt_production_date and  b.production_type =4 THEN b.production_qnty  ELSE 0 END) AS today_input_qnty,
		(CASE WHEN      b.production_type =4 THEN b.production_qnty  ELSE 0 END) AS total_input_qnty,


		(CASE WHEN   a.production_date=$txt_production_date and  b.production_type =5 THEN b.production_qnty  ELSE 0 END) AS today_output_qnty,
		(CASE WHEN      b.production_type =5 THEN b.production_qnty  ELSE 0 END) AS total_output_qnty, 

		(CASE WHEN   a.production_date=$txt_production_date and  b.production_type =11  THEN b.production_qnty  ELSE 0 END) AS today_poly_qnty,
		(CASE WHEN      b.production_type =11  THEN b.production_qnty  ELSE 0 END) AS total_poly_qnty,
		(CASE WHEN   a.production_date=$txt_production_date and  b.production_type =11  THEN b.reject_qty  ELSE 0 END) AS today_poly_rej_qnty,
		(CASE WHEN      b.production_type =11  THEN b.reject_qty  ELSE 0 END) AS total_poly_rej_qnty,

		(CASE WHEN   a.production_date=$txt_production_date and  b.production_type =8 THEN b.production_qnty  ELSE 0 END) AS today_pac_qnty,
		(CASE WHEN      b.production_type =8 THEN b.production_qnty  ELSE 0 END) AS total_pac_qnty,

		(CASE WHEN a.production_date=$txt_production_date and b.is_rescan=0 and b.production_type =5 THEN b.reject_qty ELSE 0 END)- (CASE WHEN a.production_date=$txt_production_date and b.is_rescan=1 and b.production_type =5 THEN b.production_qnty ELSE 0 END) AS today_sewing_reject_qty,
		(CASE WHEN  b.production_type =5 and b.is_rescan=0 THEN b.reject_qty ELSE 0 END)-(CASE WHEN  b.production_type =5 and b.is_rescan=1 THEN b.production_qnty ELSE 0 END) AS total_sewing_reject

		from  pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_color_size_breakdown c ,GBL_TEMP_ENGINE tmp 
		where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=tmp.ref_val and tmp.entry_form=165  and tmp.user_id=$user_id and tmp.ref_from=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0   and a.serving_company in($cbo_work_company_name) $gmts_loc_floor_cond ";

		// echo $production_sql;die;
		 
		
		$production_sql_result=sql_select($production_sql);
		foreach($production_sql_result as $row)
		{			   
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_cutting_qnty"]+=$row[csf("today_cutting_qnty")];
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["total_cutting_qnty"]+=$row[csf("total_cutting_qnty")];

			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_input_qnty"]+=$row[csf("today_input_qnty")];
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["total_input_qnty"]+=$row[csf("total_input_qnty")];

			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_output_qnty"]+=$row[csf("today_output_qnty")];
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["total_output_qnty"]+=$row[csf("total_output_qnty")];

			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_poly_qnty"]+=$row[csf("today_poly_qnty")];
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["total_poly_qnty"]+=$row[csf("total_poly_qnty")];
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_poly_rej_qnty"]+=$row[csf("today_poly_rej_qnty")];
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["total_poly_rej_qnty"]+=$row[csf("total_poly_rej_qnty")];

			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_pac_qnty"]+=$row[csf("today_pac_qnty")];
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["total_pac_qnty"]+=$row[csf("total_pac_qnty")];

			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_sewing_reject_qty"]+=$row[csf("today_sewing_reject_qty")];
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["total_sewing_reject"]+=$row[csf("total_sewing_reject")];
		
		}

		unset($production_sql_result);
		// =============================== leftover data ==========================
		$left_sql="SELECT b.po_break_down_id as order_id, b.item_number_id, b.country_id, d.color_number_id as color_id, 
		( case when a.leftover_date=$txt_production_date then  c.PRODUCTION_QNTY else 0 end ) as today_leftover_qnty,  
		(c.production_qnty) as total_leftover_qnty
		from PRO_LEFTOVER_GMTS_RCV_MST a, PRO_LEFTOVER_GMTS_RCV_DTLS b, PRO_LEFTOVER_GMTS_RCV_CLR_SZ c,wo_po_color_size_breakdown d,GBL_TEMP_ENGINE tmp
		where a.id=b.mst_id and b.id=c.dtls_id and c.color_size_break_down_id=d.id and b.po_break_down_id=tmp.ref_val and tmp.entry_form=165  and tmp.user_id=$user_id and tmp.ref_from=1 and d.is_deleted=0  and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.WORKING_COMPANY_ID in($cbo_work_company_name) $gmts_loc_floor_cond_left";	
		// echo $left_sql;die();
		 
		$left_sql_result=sql_select($left_sql);
		foreach($left_sql_result as $row)
		{ 			 
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_leftover_qnty"]+=$row[csf("today_leftover_qnty")];
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["total_leftover_qnty"]+=$row[csf("total_leftover_qnty")] ;				 
			 
		} 
		unset($left_sql_result);
		// ================================= ex-fact data ==================================
		 
		$ex_factory_sql="SELECT m.delivery_date, a.po_break_down_id as order_id, a.item_number_id, a.country_id, c.color_number_id as color_id, ( case when a.ex_factory_date=$txt_production_date then  b.production_qnty else 0 end ) as today_ex_fac,  (   b.production_qnty  ) as total_ex_fac
		from pro_ex_factory_delivery_mst m, pro_ex_factory_mst a, pro_ex_factory_dtls b, wo_po_color_size_breakdown c,GBL_TEMP_ENGINE tmp
		where m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=tmp.ref_val and tmp.entry_form=165  and tmp.user_id=$user_id and tmp.ref_from=1 and d.is_deleted=0  and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0 and m.entry_form!=85 and m.delivery_company_id in($cbo_work_company_name) $gmts_loc_floor_cond_exfac";
		
		
		 
		$ex_factory_sql_result=sql_select($ex_factory_sql);
		foreach($ex_factory_sql_result as $row)
		{
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_ex_fac"]+=$row[csf("today_ex_fac")];
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["total_ex_fac"]+=$row[csf("total_ex_fac")] ;			 
		} 
		unset($ex_factory_sql_result);

		 
		$po_id_conds=str_replace("a.po_break_down_id", "b.id", $lay_order_cond_prod);
		$buyer_cond="";
		if(str_replace("'","",$cbo_buyer_name))
		{
			$buyer_cond=" and a.buyer_name =$cbo_buyer_name ";
		}
		$others_cond="";
		if(str_replace("'","", $cbo_shipping_status))
			$others_cond.=" and b.shiping_status in (".str_replace("'","", $cbo_shipping_status).") ";
		$job_years=str_replace("'","", $cbo_year);
		$job_years_arr=str_split($job_years);
		$year_format=$job_years_arr[2].$job_years_arr[3];
		if($job_years)
			$others_cond.=" and a.job_no_prefix like '%-$year_format-%'";
			

		if(str_replace("'","", $hidden_job_id))
			$others_cond.=" and a.id in (".str_replace("'","", $hidden_job_id).") ";

		if(str_replace("'","", $hidden_order_id))
			$others_cond.=" and b.id in (".str_replace("'","", $hidden_order_id).") ";


		if(count($row_generate_cond_array)>0)
		{
			 $sql_color_size="SELECT a.job_no, a.style_ref_no, a.buyer_name,a.client_id,b.shiping_status,  a.job_no_prefix_num,   b.po_number, c.po_break_down_id, c.item_number_id, c.country_id, c.color_number_id, c.country_ship_date, c.order_quantity 
				from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c ,GBL_TEMP_ENGINE tmp
				where a.id=b.job_id and b.id=c.po_break_down_id and c.po_break_down_id=tmp.ref_val and tmp.entry_form=165  and tmp.user_id=$user_id and tmp.ref_from=1 and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and b.shiping_status<>3 and c.is_deleted=0 $buyer_cond ";

			$order_color_data=array();
			$sql_color_size_arr=sql_select($sql_color_size);
			foreach($sql_color_size_arr as $row)
			{
				$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["job_no"]=$row[csf("job_no")];
				$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
				$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];
				$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
				$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["job_year"]=$row[csf("job_year")];
				$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];
				$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["country_ship_date"]=$row[csf("country_ship_date")];
				$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["order_quantity"]+=$row[csf("order_quantity")];
				$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["client"] =$row[csf("client_id")];
				$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["status"] =$shipment_status[$row[csf("shiping_status")]];
			}
		}
		unset($sql_color_size_arr);
		
		//echo $sql_color_size;die;
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from =1 and ENTRY_FORM=165");
		oci_commit($con);
		disconnect($con);

		ob_start();
		?>
		<fieldset style="width:3080px;">
			<div style="width:3080px;">
				<table width="3080"  cellspacing="0"   >
					<tr class="form_caption" style="border:none;">
						<td colspan="31" align="center" style="border:none;font-size:14px; font-weight:bold" > Daily Cutting And Input Inhand Report</td>
					</tr>
					<tr style="border:none;">
						<td colspan="31" align="center" style="border:none; font-size:16px; font-weight:bold">
							Working Company Name:<? 
							$cbo_work_company_name_arr=explode(",",$cbo_work_company_name);
							$workingCompanyName="";
							foreach ($cbo_work_company_name_arr as $workig_cmp_name)
							{
								$workingCompanyName.= $company_arr[$workig_cmp_name].', '; 
							}
							echo chop($workingCompanyName,',');
							?>                                
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="31" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? echo "Date: ". str_replace("'","",$txt_production_date) ;?>
						</td>
					</tr>
				</table>
				<br />

				<fieldset style="width:3080px; float:left;">
					<legend>Report Details Part</legend>
					<table cellspacing="0" cellpadding="0"  border="1" rules="all"  width="3060" class="rpt_table" align="left">
						<thead>
							<tr >
								<th width="40" rowspan="2">SL</th>
								<th width="100" rowspan="2">Buyer</th>
								<th width="100" rowspan="2">Buyer Client</th>
								<th width="100" rowspan="2">Style Ref</th>
								<th width="60" rowspan="2">Job No</th>								 
								<th width="100" rowspan="2">Order No</th>
								<th width="100" rowspan="2">Country</th>
								<th width="70" rowspan="2">Country Shipdate</th>
								<th width="100" rowspan="2">Garment Item</th>
								<th width="100" rowspan="2">Color</th>
								<th width="70" rowspan="2">Order Qty.</th>

								 
								<th width="160" colspan="2">Lay/Cutting Qty</th>
								<th width="160" colspan="2">Cutting QC Qty</th>
								<th width="160" colspan="2">Sewing Input</th>
								<th width="160" colspan="2">Sewing Output</th>								
								<th width="160" colspan="2">Sewing Reject</th>
								<th width="80" rowspan="2" >Sewing WIP</th>
								<th width="160" colspan="2">Poly Entry</th>
								<th width="160" colspan="2">Poly Reject</th>
								<th width="80" rowspan="2" >Poly WIP</th>
								<th width="160" colspan="2">Packing & Finishing</th>
								<th width="80" rowspan="2" >Pac.& Fin.WIP</th>
								<th width="160" colspan="2">Ex-Factory</th>
								<th width="80" rowspan="2" >Ex-Fac. WIP</th> 
								<th width="80" rowspan="2" >Left Over Garments</th> 
								<th width="80" rowspan="2" >FG Stock in Hand</th> 
								<th width="70" rowspan="2">Status</th>
								<th width="70" rowspan="2">Remarks</th>
							</tr>
							<tr>
								 
								<th width="70">Today </th>
								<th width="70">Total </th>

								<th width="70">Today </th>
								<th width="70">Total </th>

								<th width="70">Today </th>
								<th width="70">Total </th>
								 
								<th width="70">Today </th>
								<th width="70">Total </th>
								 
								<th width="70">Today </th>
								<th width="70">Total </th>
								 
								<th width="70">Today </th>
								<th width="70">Total </th>
								 
								<th width="70">Today </th>
								<th width="70">Total </th>
								 
								<th width="70">Today </th>
								<th width="70">Total </th>

								 
								<th width="70">Today </th>
								<th width="70">Total </th>

								 
								 
							</tr>
						</thead>
					</table>
					<div style="max-height:425px; overflow-y:scroll; width:3080px;" id="scroll_body">
						<table  border="1" class="rpt_table"  width="3060" rules="all" id="table_body" >
							<tbody>
								<?
 								$i=1;
 								//print_r($order_color_data) ;die;
								foreach($order_color_data as $buyer_id=>$buyer_data)
								{
									foreach($buyer_data as $job_no=>$job_data)
									{
										foreach($job_data as $order_id=>$order_data)
										{
											foreach($order_data as $item_id=>$item_data)
											{
												foreach($item_data as $country_id=>$country_data)
												{
													foreach($country_data as $color_id=>$value)
													{

														
															$tot_lay_qnty=$tot_cutting_qnty=$tot_printing_qnty=$tot_printing_rcv_qnty=$tot_embroidery_qnty=$tot_embroidery_rcv_qnty=$tot_wash_qnty=$tot_wash_rcv_qnty=$tot_sp_work_qnty=$tot_sp_work_rcv_qnty=$tot_sewing_in_qnty=$tot_sewing_out_qnty=$tot_poly_qnty=$tot_paking_finish_qnty=$tot_ex_fact_qnty=$tot_left_qnty=0;
															$cut_qc_wip=$printing_wip=$emb_wip=$wash_wip=$sp_work_wip=$sewing_wip=$poly_wip=$finishing_wip=$ex_fact_wip=$ex_fact_wip=0;
															$total_cutting_reject=$total_printing_reject=$total_embroidery_reject=$total_wash_reject=$total_sp_work_reject=$total_sewing_reject=$total_poly_reject=$total_finish_reject=0;

															if ($i%2==0)
																$bgcolor="#E9F3FF";
															else
																$bgcolor="#FFFFFF";
															 
															?>
															<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
																<td width="40" align="left"><? echo $i; ?></td>
																<td width="100" align="left"><p><? echo $buyer_short_library[$buyer_id]; ?>&nbsp;</p></td>
																<td width="100" align="left"><p><? echo $buyer_short_library[$value["client"]]; ?>&nbsp;</p></td>
																<td width="100" align="left"><p><? echo $value["style_ref_no"]; ?>&nbsp;</p></td>
																<td width="60" align="left"><p><? echo $value["job_no_prefix_num"]; ?>&nbsp;</p></td>
																 
																<td width="100" align="left"><p><? echo $value["po_number"]; ?>&nbsp;</p></td>
																<td width="100" align="left"><p><? echo $country_arr[$country_id]; ?>&nbsp;</p></td>
																<td width="70" align="left"><p><? if($value["country_ship_date"]!="" && $value["country_ship_date"]!='0000-00-00') echo change_date_format($value["country_ship_date"]); ?>&nbsp;</p></td>
																<td width="100" align="left"><p><? echo $garments_item[$item_id]; ?>&nbsp;</p></td>
																<td width="100" align="left"><p><? echo $colorname_arr[$color_id]; ?>&nbsp;</p></td>
																<td width="70" align="right"><? echo number_format($value["order_quantity"],0); $job_order_qnty+=$value["order_quantity"];$buyer_order_qnty+=$value["order_quantity"]; $gt_order_qnty+=$value["order_quantity"]; $po_order_qnty+=$value["order_quantity"]; ?></td>
																<td width="80" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["today_lay"],0); $job_lay_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_lay"]; $buyer_lay_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_lay"]; $gt_lay_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_lay"];$po_lay_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_lay"];?></td>



																<td width="80" align="right"><? $tot_lay_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["total_lay"]; echo number_format($tot_lay_qnty,0); $job_tot_lay_qnty+=$tot_lay_qnty; $buyer_tot_lay_qnty+=$tot_lay_qnty; $gt_tot_lay_qnty+=$tot_lay_qnty;$po_tot_lay_qnty+=$tot_lay_qnty;?></td>


																 
																 



																<td width="80" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["today_cutting_qnty"],0); $job_cutting_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_cutting_qnty"]; $buyer_cutting_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_cutting_qnty"]; $gt_cutting_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_cutting_qnty"];$po_cutting_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_cutting_qnty"];?></td>



																<td width="80" align="right"><? $tot_cutting_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["total_cutting_qnty"]; echo number_format($tot_cutting_qnty,0); $job_tot_cutting_qnty+=$tot_cutting_qnty; $buyer_tot_cutting_qnty+=$tot_cutting_qnty; $gt_tot_cutting_qnty+=$tot_cutting_qnty;$po_tot_cutting_qnty+=$tot_cutting_qnty;?></td>

																 
																 
																 
																 
																<td width="80" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["today_input_qnty"],0); $job_sewing_in_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_input_qnty"]; $buyer_sewing_in_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_input_qnty"]; $gt_sewing_in_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_input_qnty"];$po_sewing_in_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_input_qnty"]?></td>
																<td width="80" align="right"><? $tot_sewing_in_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["total_input_qnty"]; echo number_format($tot_sewing_in_qnty,0); $job_tot_sewing_in_qnty+=$tot_sewing_in_qnty; $buyer_tot_sewing_in_qnty+=$tot_sewing_in_qnty; $gt_tot_sewing_in_qnty+=$tot_sewing_in_qnty; $po_tot_sewing_in_qnty+=$tot_sewing_in_qnty;?></td>
															 
																<td width="80" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["today_output_qnty"],0); $job_sewing_out_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_output_qnty"]; $buyer_sewing_out_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_output_qnty"]; $gt_sewing_out_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_output_qnty"];$po_sewing_out_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_output_qnty"];?></td>
																<td width="80" align="right"><? $tot_sewing_out_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["total_output_qnty"] ; echo number_format($tot_sewing_out_qnty,0); $job_tot_sewing_out_qnty+=$tot_sewing_out_qnty; $buyer_tot_sewing_out_qnty+=$tot_sewing_out_qnty; $gt_tot_sewing_out_qnty+=$tot_sewing_out_qnty;$po_tot_sewing_out_qnty+=$tot_sewing_out_qnty;?></td>
																<td width="80" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["today_sewing_reject_qty"],0); $job_sewing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_sewing_reject_qty"]; $buyer_sewing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_sewing_reject_qty"]; $gt_sewing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_sewing_reject_qty"];$po_sewing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_sewing_reject_qty"];?></td>
																<td width="80" align="right"><? $total_sewing_reject=$production_data[$order_id][$item_id][$country_id][$color_id]["total_sewing_reject"] ; echo number_format($total_sewing_reject,0); $job_total_sewing_reject+=$total_sewing_reject; $buyer_total_sewing_reject+=$total_sewing_reject; $gt_total_sewing_reject+=$total_sewing_reject;$po_total_sewing_reject+=$total_sewing_reject;?></td>
																<td width="80" align="right"><? $sewing_wip=(($tot_sewing_out_qnty+$total_sewing_reject)-$tot_sewing_in_qnty); echo number_format($sewing_wip,0); $job_sewing_wip+=$sewing_wip; $buyer_sewing_wip+=$sewing_wip; $gt_sewing_wip+=$sewing_wip;$po_sewing_wip+=$sewing_wip;?></td>
																 
																<td width="80" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["today_poly_qnty"],0); $job_poly_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_poly_qnty"]; $buyer_poly_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_poly_qnty"]; $gt_poly_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_poly_qnty"];$po_poly_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_poly_qnty"];?></td>
																<td width="80" align="right"><? $tot_poly_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["total_poly_qnty"] ; echo number_format($tot_poly_qnty,0); $job_tot_poly_qnty+=$tot_poly_qnty; $buyer_tot_poly_qnty+=$tot_poly_qnty; $gt_tot_poly_qnty+=$tot_poly_qnty;$po_tot_poly_qnty+=$tot_poly_qnty;?></td>

																<td width="80" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["today_poly_rej_qnty"],0); 
																$job_poly_rej_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_poly_rej_qnty"]; 
																$buyer_poly_rej_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_poly_rej_qnty"]; 
																$gt_poly_rej_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_poly_rej_qnty"];
																$po_poly_rej_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_poly_rej_qnty"];?></td>
																<td width="80" align="right"><? $tot_poly_rej_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["total_poly_rej_qnty"] ; 
																	echo number_format($tot_poly_rej_qnty,0); 
																	$job_tot_poly_rej_qnty+=$tot_poly_rej_qnty; 
																	$buyer_tot_poly_rej_qnty+=$tot_poly_rej_qnty; 
																	$gt_tot_poly_rej_qnty+=$tot_poly_rej_qnty;
																	$po_tot_poly_rej_qnty+=$tot_poly_rej_qnty;?>
																	
																</td>
																 
																 
																<td width="80" align="right"><? $poly_wip=($tot_sewing_in_qnty-$tot_poly_qnty)-$tot_poly_rej_qnty; echo number_format($poly_wip,0); $job_poly_wip+=$poly_wip; $buyer_poly_wip+=$poly_wip; $gt_poly_wip+=$poly_wip; $po_poly_wip+=$poly_wip;?></td>
																 
																<td width="80" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["today_pac_qnty"],0); $job_paking_finish_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_pac_qnty"]; $buyer_paking_finish_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_pac_qnty"]; $gt_paking_finish_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_pac_qnty"];$po_paking_finish_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_pac_qnty"];?></td>
																<td width="80" align="right"><? $tot_paking_finish_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["total_pac_qnty"] ; echo number_format($tot_paking_finish_qnty,0); $job_tot_paking_finish_qnty+=$tot_paking_finish_qnty; $buyer_tot_paking_finish_qnty+=$tot_paking_finish_qnty; $gt_tot_paking_finish_qnty+=$tot_paking_finish_qnty;$po_tot_paking_finish_qnty+=$tot_paking_finish_qnty;?></td>
																 
																<td width="80" align="right"><? $finishing_wip=(($tot_paking_finish_qnty)-$tot_poly_qnty); echo number_format($finishing_wip,0); $job_finishing_wip+=$finishing_wip; $buyer_finishing_wip+=$finishing_wip; $gt_finishing_wip+=$finishing_wip; $po_finishing_wip+=$finishing_wip;?></td>
																 
																<td width="80" align="right"><? echo number_format($production_data[$order_id][$item_id][$country_id][$color_id]["today_ex_fac"],0); $job_ex_fact_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_ex_fac"]; $buyer_ex_fact_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_ex_fac"]; $gt_ex_fact_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_ex_fac"]; $po_ex_fact_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_ex_fac"];?></td>
																<td width="80" align="right"><? $tot_ex_fact_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["total_ex_fac"] ; echo number_format($tot_ex_fact_qnty,0); $job_tot_ex_fact_qnty+=$tot_ex_fact_qnty; $buyer_tot_ex_fact_qnty+=$tot_ex_fact_qnty; $gt_tot_ex_fact_qnty+=$tot_ex_fact_qnty;$po_tot_ex_fact_qnty+=$tot_ex_fact_qnty;?></td>
																<td width="80" align="right"><? $ex_fact_wip=($tot_ex_fact_qnty-$tot_paking_finish_qnty); echo number_format($ex_fact_wip,0); $job_ex_fact_wip+=$ex_fact_wip; $buyer_ex_fact_wip+=$ex_fact_wip; $gt_ex_fact_wip+=$ex_fact_wip;$po_ex_fact_wip+=$ex_fact_wip;?></td>
																<td width="80" align="right"><? $tot_left_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["total_leftover_qnty"] ; echo number_format($tot_left_qnty,0); 
																	$job_tot_left_qnty+=$tot_left_qnty;
																	$buyer_tot_left_qnty+=$tot_left_qnty; 
																	$gt_tot_left_qnty+=$tot_left_qnty;
																	$po_tot_left_qnty+=$tot_left_qnty;
																	?></td>

																<td width="80" align="right"><? $tot_fg_inhand_qnty = $tot_poly_qnty - $tot_ex_fact_qnty - $tot_left_qnty; 
																	echo number_format($tot_fg_inhand_qnty,0);
																	$job_tot_fg_inhand_qnty+=$tot_fg_inhand_qnty; $buyer_tot_fg_inhand_qnty+=$tot_fg_inhand_qnty; $gt_tot_fg_inhand_qnty+=$tot_fg_inhand_qnty;$po_tot_fg_inhand_qnty+=$tot_fg_inhand_qnty;?></td>

																<td width="70" align="right"><? echo $value["status"];?></td>
																<td width="70" align="right"><a href="##" onClick="openmypage_remarks_popup('<? echo $order_id; ?>' ,'<? echo  $item_id; ?>', '<? echo $country_id; ?>', '<? echo $color_id; ?>', '<? echo $job_no; ?>','remarks_popup')">Remarks</a></td>
															</tr>
															<?
															$i++;
														

													}
												}
											}
											// 25

											?>
											<tr bgcolor="#92D050">
												<td align="right" colspan="10" style="font-weight:bold;">PO Total:</td>
												<td width="70" align="right"><? echo number_format($po_order_qnty,0); $po_order_qnty=0; ?></td>
												<td width="80" align="right"><? echo number_format($po_lay_qnty,0); $po_lay_qnty=0;?></td>
												<td width="80" align="right"><? echo number_format($po_tot_lay_qnty,0); $po_tot_lay_qnty=0;?></td>


												<td width="80" align="right"><? echo number_format($po_cutting_qnty,0); $po_cutting_qnty=0;?></td>
												<td width="80" align="right"><? echo number_format($po_tot_cutting_qnty,0); $po_tot_cutting_qnty=0;?></td>

												



												<td width="80" align="right"><? echo number_format($po_sewing_in_qnty,0); $po_sewing_in_qnty=0;?></td>
												<td width="80" align="right"><? echo number_format($po_tot_sewing_in_qnty,0); $po_tot_sewing_in_qnty=0;?></td>

												<td width="80" align="right"><? echo number_format($po_sewing_out_qnty,0); $po_sewing_out_qnty=0;?></td>
												<td width="80" align="right"><? echo number_format($po_tot_sewing_out_qnty,0); $po_tot_sewing_out_qnty=0;?></td>
												<td width="80" align="right"><? echo number_format($po_sewing_reject_qty,0); $po_sewing_reject_qty=0;?></td>
												<td width="80" align="right"><? echo number_format($po_total_sewing_reject,0); $po_total_sewing_reject=0;?></td>
												<td width="80" align="right"><? echo number_format($po_sewing_wip,0); $po_sewing_wip=0;?></td>

												<td width="80" align="right"><? echo number_format($po_poly_qnty,0); $po_poly_qnty=0;?></td>
												<td width="80" align="right"><? echo number_format($po_tot_poly_qnty,0); $po_tot_poly_qnty=0;?></td>

												<td width="80" align="right"><? echo number_format($po_poly_rej_qnty,0); $po_poly_rej_qnty=0;?></td>
												<td width="80" align="right"><? echo number_format($po_tot_poly_rej_qnty,0); $po_tot_poly_rej_qnty=0;?></td>

												<td width="80" align="right"><? echo number_format($po_poly_wip,0); $po_poly_wip=0;?></td>

												<td width="80" align="right"><? echo number_format($po_paking_finish_qnty,0); $po_paking_finish_qnty=0;?></td>
												<td width="80" align="right"><? echo number_format($po_tot_paking_finish_qnty,0); $po_tot_paking_finish_qnty=0;?></td>

												<td width="80" align="right"><? echo number_format($po_finishing_wip,0); $po_finishing_wip=0;?></td>

												<td width="80" align="right"><? echo number_format($po_ex_fact_qnty,0); $po_ex_fact_qnty=0;?></td>
												<td width="80" align="right"><? echo number_format($po_tot_ex_fact_qnty,0); $po_tot_ex_fact_qnty=0;?></td>
												<td width="80" align="right"><? echo number_format($po_ex_fact_wip,0); $po_ex_fact_wip=0;?></td>
												<td width="80" align="right"><? echo number_format($po_tot_left_qnty,0); $po_ex_fact_wip=0;?></td>
												<td width="80" align="right"><? echo number_format($po_tot_fg_inhand_qnty,0); $po_tot_fg_inhand_qnty=0;?></td>
												<td width="70" align="right">&nbsp;</td>
												<td width="70" align="right">&nbsp;</td>
											</tr>  

											<?
										}
										?>
										<tr bgcolor="#F4F3C4">
											<td align="right" colspan="10" style="font-weight:bold;">Job Total:</td>
											<td width="70" align="right"><? echo number_format($job_order_qnty,0); $job_order_qnty=0; ?></td>

											<td width="80" align="right"><? echo number_format($job_lay_qnty,0); $job_lay_qnty=0;?></td>
											<td width="80" align="right"><? echo number_format($job_tot_lay_qnty,0); $job_tot_lay_qnty=0;?></td>


											<td width="80" align="right"><? echo number_format($job_cutting_qnty,0); $job_cutting_qnty=0;?></td>
											<td width="80" align="right"><? echo number_format($job_tot_cutting_qnty,0); $job_tot_cutting_qnty=0;?></td>

											

											 

											<td width="80" align="right"><? echo number_format($job_sewing_in_qnty,0); $job_sewing_in_qnty=0;?></td>
											<td width="80" align="right"><? echo number_format($job_tot_sewing_in_qnty,0); $job_tot_sewing_in_qnty=0;?></td>
											 
											<td width="80" align="right"><? echo number_format($job_sewing_out_qnty,0); $job_sewing_out_qnty=0;?></td>
											<td width="80" align="right"><? echo number_format($job_tot_sewing_out_qnty,0); $job_tot_sewing_out_qnty=0;?></td>
											<td width="80" align="right"><? echo number_format($job_sewing_reject_qty,0); $job_sewing_reject_qty=0;?></td>
											<td width="80" align="right"><? echo number_format($job_total_sewing_reject,0); $job_total_sewing_reject=0;?></td>
											<td width="80" align="right"><? echo number_format($job_sewing_wip,0); $job_sewing_wip=0;?></td>

											<td width="80" align="right"><? echo number_format($job_poly_qnty,0); $job_poly_qnty=0;?></td>
											<td width="80" align="right"><? echo number_format($job_tot_poly_qnty,0); $job_tot_poly_qnty=0;?></td>

											<td width="80" align="right"><? echo number_format($job_poly_rej_qnty,0); $job_poly_rej_qnty=0;?></td>
											<td width="80" align="right"><? echo number_format($job_tot_poly_rej_qnty,0); $job_tot_poly_rej_qnty=0;?></td>

											<td width="80" align="right"><? echo number_format($job_poly_wip,0); $job_poly_wip=0;?></td>

											<td width="80" align="right"><? echo number_format($job_paking_finish_qnty,0); $job_paking_finish_qnty=0;?></td>
											<td width="80" align="right"><? echo number_format($job_tot_paking_finish_qnty,0); $job_tot_paking_finish_qnty=0;?></td>

											<td width="80" align="right"><? echo number_format($job_finishing_wip,0); $job_finishing_wip=0;?></td>

											<td width="80" align="right"><? echo number_format($job_ex_fact_qnty,0); $job_ex_fact_qnty=0;?></td>
											<td width="80" align="right"><? echo number_format($job_tot_ex_fact_qnty,0); $job_tot_ex_fact_qnty=0;?></td>
											<td width="80" align="right"><? echo number_format($job_ex_fact_wip,0); $job_ex_fact_wip=0;?></td>
											<td width="80" align="right"><? echo number_format($job_tot_left_qnty,0); $job_ex_fact_wip=0;?></td>
											<td width="80" align="right"><? echo number_format($job_tot_fg_inhand_qnty,0); $job_tot_fg_inhand_qnty=0;?></td>
											<td width="70" align="right">&nbsp;</td>
											<td width="70" align="right">&nbsp;</td>
										</tr>  
										<?
									}
									?>
									<tr bgcolor="#CCCCCC">
										<td align="right" colspan="10" style="font-weight:bold;">Buyer Total:</td>
										<td width="70" align="right"><? echo number_format($buyer_order_qnty,0); $buyer_order_qnty=0; ?></td>
										<td width="80" align="right"><? echo number_format($buyer_lay_qnty,0); $buyer_lay_qnty=0;?></td>
										<td width="80" align="right"><? echo number_format($buyer_tot_lay_qnty,0); $buyer_tot_lay_qnty=0;?></td> 

										 
										<td width="80" align="right"><? echo number_format($buyer_cutting_qnty,0); $buyer_cutting_qnty=0;?></td>
										<td width="80" align="right"><? echo number_format($buyer_tot_cutting_qnty,0); $buyer_tot_cutting_qnty=0;?></td> 

										

										<td width="80" align="right"><? echo number_format($buyer_sewing_in_qnty,0);  $buyer_sewing_in_qnty=0;?></td>
										<td width="80" align="right"><? echo number_format($buyer_tot_sewing_in_qnty,0);  $buyer_tot_sewing_in_qnty=0;?></td>
										 
										<td width="80" align="right"><? echo number_format($buyer_sewing_out_qnty,0);  $buyer_sewing_out_qnty=0;?></td>
										<td width="80" align="right"><? echo number_format( $buyer_tot_sewing_out_qnty,0); $buyer_tot_sewing_out_qnty=0;?></td>
										<td width="80" align="right"><? echo number_format($buyer_sewing_reject_qty,0); $buyer_sewing_reject_qty=0;?></td>
										<td width="80" align="right"><? echo number_format($buyer_total_sewing_reject,0); $buyer_total_sewing_reject=0;?></td>
										<td width="80" align="right"><? echo number_format($buyer_sewing_wip,0); $buyer_sewing_wip=0;?></td>
										 
										<td width="80" align="right"><? echo number_format($buyer_poly_qnty,0); $buyer_poly_qnty=0;?></td>
										<td width="80" align="right"><? echo number_format($buyer_tot_poly_qnty,0); $buyer_tot_poly_qnty=0;?></td>
										 
										<td width="80" align="right"><? echo number_format($buyer_poly_rej_qnty,0); $buyer_poly_rej_qnty=0;?></td>
										<td width="80" align="right"><? echo number_format($buyer_tot_poly_rej_qnty,0); $buyer_tot_poly_rej_qnty=0;?></td>
										 
										<td width="80" align="right"><? echo number_format($buyer_poly_wip,0); $buyer_poly_wip=0;?></td>
										 
										<td width="80" align="right"><? echo number_format($buyer_paking_finish_qnty,0);  $buyer_paking_finish_qnty=0;?></td>
										<td width="80" align="right"><? echo number_format($buyer_tot_paking_finish_qnty,0); $buyer_tot_paking_finish_qnty=0;?></td>
										 
										 
										<td width="80" align="right"><? echo number_format($buyer_finishing_wip,0);  $buyer_finishing_wip=0;?></td>
										 
										<td width="80" align="right"><? echo number_format($buyer_ex_fact_qnty,0); $buyer_ex_fact_qnty=0;?></td>
										<td width="80" align="right"><? echo number_format($buyer_tot_ex_fact_qnty,0); $buyer_tot_ex_fact_qnty=0;?></td>
										<td width="80" align="right"><? echo number_format($buyer_ex_fact_wip,0); $buyer_ex_fact_wip=0;?></td>
										<td width="80" align="right"><? echo number_format($buyer_tot_left_qnty,0); $buyer_ex_fact_wip=0;?></td>
										<td width="80" align="right"><? echo number_format($buyer_tot_fg_inhand_qnty,0); $buyer_tot_fg_inhand_qnty=0;?></td>
										<td width="70" align="right">&nbsp;</td>
										<td width="70" align="right">&nbsp;</td>
									</tr>
									<?
								}


								?>
							</tbody>


						</table> 
					</div>  
					 <table border="1" class="rpt_table"  width="3060" rules="all" style="margin-left: 2px;" align="left" id="">
						<tfoot>
							<tr>
								<th style="word-break: break-all;word-wrap: break-word;" width="40" align="center">&nbsp;</th>
								<th   style="word-break: break-all;word-wrap: break-word;" width="100"><p>&nbsp;</p></th>
								<th   style="word-break: break-all;word-wrap: break-word;" width="100"><p>&nbsp;</p></th>
								<th  style="word-break: break-all;word-wrap: break-word;"  width="100"><p>&nbsp;</p></th>
								<th   style="word-break: break-all;word-wrap: break-word;" width="60" align="center"><p>&nbsp;</p></th>
								 
								<th   style="word-break: break-all;word-wrap: break-word;" width="100"><p>&nbsp;</p></th>
								<th   style="word-break: break-all;word-wrap: break-word;" width="100"><p>&nbsp;</p></th>
								<th  style="word-break: break-all;word-wrap: break-word;"  width="70" align="center"><p>&nbsp;</p></th>
								<th  style="word-break: break-all;word-wrap: break-word;"  width="100"><p>&nbsp;</p></th>

								<th width="100" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">Grand Total</th>
								<th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_order_qnty,0);?></th>
								<th width="80" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_lay_qnty,0);?></th>
								<th width="80" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_tot_lay_qnty,0);?></th>
								 
								 
								<th width="80" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_cutting_qnty,0);?></th>
								<th width="80" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_tot_cutting_qnty,0);?></th>

								
								  
								<th width="80" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_sewing_in_qnty,0);?></th>
								<th width="80" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_tot_sewing_in_qnty,0); ?></th>
								 
								<th width="80" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_sewing_out_qnty,0); ?></th>
								<th width="80" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_tot_sewing_out_qnty,0); ?></th>
								<th width="80" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_sewing_reject_qty,0);?></th>
								<th width="80" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_total_sewing_reject,0);?></th>
								<th width="80" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_sewing_wip,0);?></th>
								 
								<th width="80" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_poly_qnty,0);?></th>
								<th width="80" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_tot_poly_qnty,0); ?></th>
								<th width="80" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_poly_rej_qnty,0);?></th>
								<th width="80" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_tot_poly_rej_qnty,0); ?></th>							 
								<th width="80" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><?  echo number_format($gt_poly_wip,0);?></th>
								 
								<th width="80" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_paking_finish_qnty,0);?></th>
								<th width="80" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_tot_paking_finish_qnty,0); ?></th>
								 
								<th width="80" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_finishing_wip,0);?></th>
								 
								<th width="80" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_ex_fact_qnty,0);?></th>
								<th width="80" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_tot_ex_fact_qnty,0); ?></th>
								<th width="80" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_ex_fact_wip,0);?></th>
								<th width="80" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_tot_left_qnty,0);?></th>
								<th width="80" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"><? echo number_format($gt_tot_fg_inhand_qnty,0);?></th>
								<th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"> </th>
								<th width="70" align="right" style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;"> </th>
							</tr>    
						</tfoot>
					</table>  
				</fieldset>  
			</div>     
		</fieldset>
		<?	
	}

 

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
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit(); 
}

if($action=="generate_report_excel")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$hidden_order_id=str_replace("'","",$hidden_order_id);
	//$txt_production_date=str_replace("'","",$txt_production_date);
	$job_po_id="";
	if(str_replace("'","",$txt_job_no)!="")
	{
		if($db_type==0)
		{
			$job_po_id=return_field_value("group_concat(b.id) as po_id","wo_po_break_down b","b.job_no_mst=$txt_job_no","po_id");
		}
		else
		{
			$job_po_id=return_field_value("listagg(cast(b.id as varchar(4000)),',') within group(order by id) as po_id","wo_po_break_down b","b.job_no_mst=$txt_job_no","po_id");
		}
	}
	
	//echo $job_po_id;die;
	
	$order_cond_lay="";
	$order_cond_prod="";
	
	if($hidden_order_id!="")
	{
		$order_cond.=" and a.po_break_down_id in($hidden_order_id)";
		$order_cond_lay.=" and c.order_id in($hidden_order_id)";
	}
	elseif($job_po_id!="")
	{
		$order_cond.=" and a.po_break_down_id in($job_po_id)";
		$order_cond_lay.=" and c.order_id in($job_po_id)";
	}
	 
	
	$cbo_work_company_name=str_replace("'","",$cbo_work_company_name);
	$cbo_location_name=str_replace("'","",$cbo_location_name);
	$cbo_floor_name=str_replace("'","",$cbo_floor_name);
	$gmts_loc_floor_cond="";
	$gmts_loc_floor_cond_cut="";
	$gmts_loc_floor_cond_delv="";
	$gmts_loc_floor_cond_exfac="";
	$gmts_loc_floor_cond_left="";
	if($cbo_location_name)
	{
		$gmts_loc_floor_cond_cut.= " and a.location_id in($cbo_location_name)  ";
		$gmts_loc_floor_cond_delv.= " and m.location_id in($cbo_location_name)  ";
		$gmts_loc_floor_cond.= " and a.location in($cbo_location_name)  ";
		$gmts_loc_floor_cond_exfac.= " and m.delivery_location_id in($cbo_location_name)  ";
		$gmts_loc_floor_cond_left.= " and a.WORKING_LOCATION_ID in($cbo_location_name)  ";
	}
	
	if($cbo_floor_name)
	{
		$gmts_loc_floor_cond.= " and a.floor_id in ($cbo_floor_name) ";
		$gmts_loc_floor_cond_cut.= " and a.floor_id in ($cbo_floor_name) ";
		$gmts_loc_floor_cond_delv.= " and m.floor_id in ($cbo_floor_name) ";
		$gmts_loc_floor_cond_exfac.= " and m.delivery_floor_id in ($cbo_floor_name) ";
		$gmts_loc_floor_cond_left.= " and a.WORKING_FLOOR_ID in ($cbo_floor_name) ";
	}
	$floor_group=trim(str_replace("'","",$txt_floor_group));
	if($floor_group)
	{
		$floor_group_sql=sql_select("SELECT   id  FROM lib_prod_floor where group_name ='$floor_group' and status_active=1 ");
		$floor_group_arr=array();
		$floor_group_arr[0]=0;
		foreach($floor_group_sql as $fl)
			$floor_group_arr[$fl[csf("id")]]=$fl[csf("id")];
		$all_floor_by_group=implode(",",$floor_group_arr);

		$gmts_loc_floor_cond.= " and a.floor_id in ($all_floor_by_group) ";
		$gmts_loc_floor_cond_cut.= " and a.floor_id in ($all_floor_by_group) ";
		$gmts_loc_floor_cond_delv.= " and m.floor_id in ($all_floor_by_group) ";
		$gmts_loc_floor_cond_exfac.= " and m.delivery_floor_id in ($all_floor_by_group) ";
		$gmts_loc_floor_cond_left.= " and a.WORKING_FLOOR_ID in ($all_floor_by_group) ";
		

	}
 

	$sql_lay=" SELECT a.working_company_id, b.gmt_item_id, c.order_id, c.country_id, b.color_id,(case when a.entry_date=$txt_production_date then c.size_qty else 0 end )  as today_lay,  (c.size_qty )  as total_lay
	from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c,wo_po_break_down d 
	where a.id=b.mst_id and b.id=c.dtls_id and c.order_id=d.id and d.is_deleted=0 and d.shiping_status<>3   and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.working_company_id in($cbo_work_company_name) $gmts_loc_floor_cond_cut  $order_cond_lay ";  

	
	$sql_lay_result=sql_select($sql_lay);
	$production_data=$porduction_ord_id=$lay_order_id=array();
	$row_generate_cond_array=array();

	foreach($sql_lay_result as $row)
	{
		$row_generate_cond_array[$row[csf("order_id")]]=$row[csf("order_id")];
		if($row[csf("order_id")])
		{
			$lay_order_id[$row[csf("order_id")]]=$row[csf("order_id")];
		}
		
		$production_data[$row[csf("order_id")]][$row[csf("gmt_item_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_lay"] +=$row[csf("today_lay")];
		$production_data[$row[csf("order_id")]][$row[csf("gmt_item_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["total_lay"] +=$row[csf("total_lay")];
			
	}
	unset($sql_lay_result);
	/* $lay_order_cond_prod="";
	if($db_type==2 && count($lay_order_id)>999)
	{
		$chunk=array_chunk($lay_order_id,999);
		foreach($chunk as $rows)
		{
			$vals=implode(",", $rows);
			if($lay_order_cond_prod=="") $lay_order_cond_prod.=" and ( a.po_break_down_id in ($vals) ";
			else  $lay_order_cond_prod.=" or   a.po_break_down_id in ($vals) ";

		}
		$lay_order_cond_prod.=" )";

	}
	else
	{
		$values=implode(",",$lay_order_id);
		if(!$values)$values=0;
		$lay_order_cond_prod=" and a.po_break_down_id in ($values) ";
	} */		

	// ============================= store data in gbl table ==============================
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from =1 and ENTRY_FORM=165");
	oci_commit($con);

	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 165, 1, $lay_order_id, $empty_arr);//Po ID
	disconnect($con);
	
	
	$production_sql=" SELECT a.production_date, c.po_break_down_id as order_id, c.item_number_id, c.country_id, c.color_number_id as color_id, (b.production_qnty) as all_today_production_qnty,
	(CASE WHEN   a.production_date=$txt_production_date and  b.production_type =1 THEN b.production_qnty  ELSE 0 END) AS today_cutting_qnty,
	(CASE WHEN      b.production_type =1 THEN b.production_qnty  ELSE 0 END) AS total_cutting_qnty,


	(CASE WHEN   a.production_date=$txt_production_date and  b.production_type =4 THEN b.production_qnty  ELSE 0 END) AS today_input_qnty,
	(CASE WHEN      b.production_type =4 THEN b.production_qnty  ELSE 0 END) AS total_input_qnty,


	(CASE WHEN   a.production_date=$txt_production_date and  b.production_type =5 THEN b.production_qnty  ELSE 0 END) AS today_output_qnty,
	(CASE WHEN      b.production_type =5 THEN b.production_qnty  ELSE 0 END) AS total_output_qnty, 

	(CASE WHEN   a.production_date=$txt_production_date and  b.production_type =11  THEN b.production_qnty  ELSE 0 END) AS today_poly_qnty,
	(CASE WHEN      b.production_type =11  THEN b.production_qnty  ELSE 0 END) AS total_poly_qnty,
	(CASE WHEN   a.production_date=$txt_production_date and  b.production_type =11  THEN b.reject_qty  ELSE 0 END) AS today_poly_rej_qnty,
	(CASE WHEN      b.production_type =11  THEN b.reject_qty  ELSE 0 END) AS total_poly_rej_qnty,

	(CASE WHEN   a.production_date=$txt_production_date and  b.production_type =8 THEN b.production_qnty  ELSE 0 END) AS today_pac_qnty,
	(CASE WHEN      b.production_type =8 THEN b.production_qnty  ELSE 0 END) AS total_pac_qnty,

	(CASE WHEN a.production_date=$txt_production_date and b.is_rescan=0 and b.production_type =5 THEN b.reject_qty ELSE 0 END)- (CASE WHEN a.production_date=$txt_production_date and b.is_rescan=1 and b.production_type =5 THEN b.production_qnty ELSE 0 END) AS today_sewing_reject_qty,
	(CASE WHEN  b.production_type =5 and b.is_rescan=0 THEN b.reject_qty ELSE 0 END)-(CASE WHEN  b.production_type =5 and b.is_rescan=1 THEN b.production_qnty ELSE 0 END) AS total_sewing_reject

	from  pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_color_size_breakdown c ,GBL_TEMP_ENGINE tmp 
	where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=tmp.ref_val and tmp.entry_form=165  and tmp.user_id=$user_id and tmp.ref_from=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0   and a.serving_company in($cbo_work_company_name) $gmts_loc_floor_cond ";

	// echo $production_sql;die;
		
	
	$production_sql_result=sql_select($production_sql);
	foreach($production_sql_result as $row)
	{			   
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_cutting_qnty"]+=$row[csf("today_cutting_qnty")];
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["total_cutting_qnty"]+=$row[csf("total_cutting_qnty")];

		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_input_qnty"]+=$row[csf("today_input_qnty")];
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["total_input_qnty"]+=$row[csf("total_input_qnty")];

		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_output_qnty"]+=$row[csf("today_output_qnty")];
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["total_output_qnty"]+=$row[csf("total_output_qnty")];

		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_poly_qnty"]+=$row[csf("today_poly_qnty")];
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["total_poly_qnty"]+=$row[csf("total_poly_qnty")];
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_poly_rej_qnty"]+=$row[csf("today_poly_rej_qnty")];
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["total_poly_rej_qnty"]+=$row[csf("total_poly_rej_qnty")];

		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_pac_qnty"]+=$row[csf("today_pac_qnty")];
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["total_pac_qnty"]+=$row[csf("total_pac_qnty")];

		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_sewing_reject_qty"]+=$row[csf("today_sewing_reject_qty")];
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["total_sewing_reject"]+=$row[csf("total_sewing_reject")];
	
	}

	unset($production_sql_result);
	// =============================== leftover data ==========================
	$left_sql="SELECT b.po_break_down_id as order_id, b.item_number_id, b.country_id, d.color_number_id as color_id, 
	( case when a.leftover_date=$txt_production_date then  c.PRODUCTION_QNTY else 0 end ) as today_leftover_qnty,  
	(c.production_qnty) as total_leftover_qnty
	from PRO_LEFTOVER_GMTS_RCV_MST a, PRO_LEFTOVER_GMTS_RCV_DTLS b, PRO_LEFTOVER_GMTS_RCV_CLR_SZ c,wo_po_color_size_breakdown d,GBL_TEMP_ENGINE tmp
	where a.id=b.mst_id and b.id=c.dtls_id and c.color_size_break_down_id=d.id and b.po_break_down_id=tmp.ref_val and tmp.entry_form=165  and tmp.user_id=$user_id and tmp.ref_from=1 and d.is_deleted=0  and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.WORKING_COMPANY_ID in($cbo_work_company_name) $gmts_loc_floor_cond_left";	
	// echo $left_sql;die();
		
	$left_sql_result=sql_select($left_sql);
	foreach($left_sql_result as $row)
	{ 			 
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_leftover_qnty"]+=$row[csf("today_leftover_qnty")];
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["total_leftover_qnty"]+=$row[csf("total_leftover_qnty")] ;				 
			
	} 
	unset($left_sql_result);
	// ================================= ex-fact data ==================================
		
	$ex_factory_sql="SELECT m.delivery_date, a.po_break_down_id as order_id, a.item_number_id, a.country_id, c.color_number_id as color_id, ( case when a.ex_factory_date=$txt_production_date then  b.production_qnty else 0 end ) as today_ex_fac,  (   b.production_qnty  ) as total_ex_fac
	from pro_ex_factory_delivery_mst m, pro_ex_factory_mst a, pro_ex_factory_dtls b, wo_po_color_size_breakdown c,GBL_TEMP_ENGINE tmp
	where m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=tmp.ref_val and tmp.entry_form=165  and tmp.user_id=$user_id and tmp.ref_from=1 and d.is_deleted=0  and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0 and m.entry_form!=85 and m.delivery_company_id in($cbo_work_company_name) $gmts_loc_floor_cond_exfac";
	
	
		
	$ex_factory_sql_result=sql_select($ex_factory_sql);
	foreach($ex_factory_sql_result as $row)
	{
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["today_ex_fac"]+=$row[csf("today_ex_fac")];
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["total_ex_fac"]+=$row[csf("total_ex_fac")] ;			 
	} 
	unset($ex_factory_sql_result);

		
	$po_id_conds=str_replace("a.po_break_down_id", "b.id", $lay_order_cond_prod);
	$buyer_cond="";
	if(str_replace("'","",$cbo_buyer_name))
	{
		$buyer_cond=" and a.buyer_name =$cbo_buyer_name ";
	}
	$others_cond="";
	if(str_replace("'","", $cbo_shipping_status))
		$others_cond.=" and b.shiping_status in (".str_replace("'","", $cbo_shipping_status).") ";
	$job_years=str_replace("'","", $cbo_year);
	$job_years_arr=str_split($job_years);
	$year_format=$job_years_arr[2].$job_years_arr[3];
	if($job_years)
		$others_cond.=" and a.job_no_prefix like '%-$year_format-%'";
		

	if(str_replace("'","", $hidden_job_id))
		$others_cond.=" and a.id in (".str_replace("'","", $hidden_job_id).") ";

	if(str_replace("'","", $hidden_order_id))
		$others_cond.=" and b.id in (".str_replace("'","", $hidden_order_id).") ";


	if(count($row_generate_cond_array)>0)
	{
			$sql_color_size="SELECT a.job_no, a.style_ref_no, a.buyer_name,a.client_id,b.shiping_status,  a.job_no_prefix_num,   b.po_number, c.po_break_down_id, c.item_number_id, c.country_id, c.color_number_id, c.country_ship_date, c.order_quantity 
			from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c ,GBL_TEMP_ENGINE tmp
			where a.id=b.job_id and b.id=c.po_break_down_id and c.po_break_down_id=tmp.ref_val and tmp.entry_form=165  and tmp.user_id=$user_id and tmp.ref_from=1 and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3)  and b.is_deleted=0 and c.status_active in(1,2,3) and b.shiping_status<>3 and c.is_deleted=0 $buyer_cond ";

		$order_color_data=array();
		$sql_color_size_arr=sql_select($sql_color_size);
		foreach($sql_color_size_arr as $row)
		{
			$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["job_no"]=$row[csf("job_no")];
			$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
			$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];
			$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["job_year"]=$row[csf("job_year")];
			$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];
			$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["country_ship_date"]=$row[csf("country_ship_date")];
			$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["order_quantity"]+=$row[csf("order_quantity")];
			$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["client"] =$row[csf("client_id")];
			$order_color_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["status"] =$shipment_status[$row[csf("shiping_status")]];
		}
	}
	unset($sql_color_size_arr);
	
	//echo $sql_color_size;die;
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from =1 and ENTRY_FORM=165");
	oci_commit($con);
	disconnect($con);
	$report_data = "";	
	
	$report_data .='<fieldset style="width:3080px;">
		<div style="width:3080px;">
			<table width="3080"  cellspacing="0"   >
				<tr class="form_caption" style="border:none;">
					<td colspan="31" align="center" style="border:none;font-size:14px; font-weight:bold" > Daily Cutting And Input Inhand Report</td>
				</tr>
				<tr style="border:none;">
					<td colspan="31" align="center" style="border:none; font-size:16px; font-weight:bold">
						Working Company Name:'. 
						$cbo_work_company_name_arr=explode(",",$cbo_work_company_name);
						$workingCompanyName="";
						foreach ($cbo_work_company_name_arr as $workig_cmp_name)
						{
							$workingCompanyName.= $company_arr[$workig_cmp_name].', '; 
						}
						echo chop($workingCompanyName,',').'
						                                
					</td>
				</tr>
				<tr style="border:none;">
					<td colspan="31">
						Date '. str_replace("'","",$txt_production_date).'
					</td>
				</tr>
			</table>
			<br />

			<fieldset>
				<legend>Report Details Part</legend>
				<table cellspacing="0" cellpadding="0"  border="1" rules="all" >
					<thead>
						<tr >
							<th rowspan="2">SL</th>
							<th rowspan="2">Buyer</th>
							<th rowspan="2">Buyer Client</th>
							<th rowspan="2">Style Ref</th>
							<th rowspan="2">Job No</th>								 
							<th rowspan="2">Order No</th>
							<th rowspan="2">Country</th>
							<th rowspan="2">Country Shipdate</th>
							<th rowspan="2">Garment Item</th>
							<th rowspan="2">Color</th>
							<th rowspan="2">Order Qty.</th>

								
							<th colspan="2">Lay/Cutting Qty</th>
							<th colspan="2">Cutting QC Qty</th>
							<th colspan="2">Sewing Input</th>
							<th colspan="2">Sewing Output</th>								
							<th colspan="2">Sewing Reject</th>
							<th rowspan="2" >Sewing WIP</th>
							<th colspan="2">Poly Entry</th>
							<th colspan="2">Poly Reject</th>
							<th rowspan="2" >Poly WIP</th>
							<th colspan="2">Packing & Finishing</th>
							<th rowspan="2" >Pac.& Fin.WIP</th>
							<th colspan="2">Ex-Factory</th>
							<th rowspan="2" >Ex-Fac. WIP</th> 
							<th rowspan="2" >Left Over Garments</th> 
							<th rowspan="2" >FG Stock in Hand</th> 
							<th rowspan="2">Status</th>
							<th rowspan="2">Remarks</th>
						</tr>
						<tr>
								
							<th>Today </th>
							<th>Total </th>

							<th>Today </th>
							<th>Total </th>

							<th>Today </th>
							<th>Total </th>
								
							<th>Today </th>
							<th>Total </th>
								
							<th>Today </th>
							<th>Total </th>
								
							<th>Today </th>
							<th>Total </th>
								
							<th>Today </th>
							<th>Total </th>
								
							<th>Today </th>
							<th>Total </th>

								
							<th>Today </th>
							<th>Total </th>

								
								
						</tr>
					</thead>
				</table>
				<div>
					<table  border="1">
						<tbody>'.
							
							$i=1;
							//print_r($order_color_data) ;die;
							foreach($order_color_data as $buyer_id=>$buyer_data)
							{
								foreach($buyer_data as $job_no=>$job_data)
								{
									foreach($job_data as $order_id=>$order_data)
									{
										foreach($order_data as $item_id=>$item_data)
										{
											foreach($item_data as $country_id=>$country_data)
											{
												foreach($country_data as $color_id=>$value)
												{

													
														$tot_lay_qnty=$tot_cutting_qnty=$tot_printing_qnty=$tot_printing_rcv_qnty=$tot_embroidery_qnty=$tot_embroidery_rcv_qnty=$tot_wash_qnty=$tot_wash_rcv_qnty=$tot_sp_work_qnty=$tot_sp_work_rcv_qnty=$tot_sewing_in_qnty=$tot_sewing_out_qnty=$tot_poly_qnty=$tot_paking_finish_qnty=$tot_ex_fact_qnty=$tot_left_qnty=0;
														$cut_qc_wip=$printing_wip=$emb_wip=$wash_wip=$sp_work_wip=$sewing_wip=$poly_wip=$finishing_wip=$ex_fact_wip=$ex_fact_wip=0;
														$total_cutting_reject=$total_printing_reject=$total_embroidery_reject=$total_wash_reject=$total_sp_work_reject=$total_sewing_reject=$total_poly_reject=$total_finish_reject=0;

														if ($i%2==0)
															$bgcolor="#E9F3FF";
														else
															$bgcolor="#FFFFFF";
															
														
														$report_data .='<tr bgcolor="'.$bgcolor.'">
															<td>'.$i.'</td>
															<td><p>'.$buyer_short_library[$buyer_id].'&nbsp;</p></td>
															<td><p>'.$buyer_short_library[$value["client"]].'&nbsp;</p></td>
															<td><p>'.$value["style_ref_no"].'&nbsp;</p></td>
															<td><p>'.$value["job_no_prefix_num"].'&nbsp;</p></td>
																
															<td><p>'.$value["po_number"].'&nbsp;</p></td>
															<td><p>'.$country_arr[$country_id].'&nbsp;</p></td>
															<td><p>';
															if($value["country_ship_date"]!="" && $value["country_ship_date"]!='0000-00-00')  $report_data .= change_date_format($value["country_ship_date"]).'&nbsp;</p></td>
															<td><p>'.$garments_item[$item_id].'&nbsp;</p></td>
															<td><p>'.$colorname_arr[$color_id].'&nbsp;</p></td>
															<td>'.number_format($value["order_quantity"],0);$job_order_qnty+=$value["order_quantity"];$buyer_order_qnty+=$value["order_quantity"]; $gt_order_qnty+=$value["order_quantity"]; $po_order_qnty+=$value["order_quantity"];
															$report_data .='</td>
															<td>'.number_format($production_data[$order_id][$item_id][$country_id][$color_id]["today_lay"],0); 
															$job_lay_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_lay"]; 
															$buyer_lay_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_lay"]; 
															$gt_lay_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_lay"];
															$po_lay_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_lay"];
															$report_data.='</td>

															<td>';$tot_lay_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["total_lay"]; $report_data.=number_format($tot_lay_qnty,0); 
															$job_tot_lay_qnty+=$tot_lay_qnty; $buyer_tot_lay_qnty+=$tot_lay_qnty; $gt_tot_lay_qnty+=$tot_lay_qnty;$po_tot_lay_qnty+=$tot_lay_qnty;
															$report_data.='</td>


															<td>'.number_format($production_data[$order_id][$item_id][$country_id][$color_id]["today_cutting_qnty"],0); 
															$job_cutting_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_cutting_qnty"]; 
															$buyer_cutting_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_cutting_qnty"]; 
															$gt_cutting_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_cutting_qnty"];
															$po_cutting_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_cutting_qnty"];$report_data.='</td>



															<td>';$tot_cutting_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["total_cutting_qnty"]; $report_data.=number_format($tot_cutting_qnty,0); 
															$job_tot_cutting_qnty+=$tot_cutting_qnty; $buyer_tot_cutting_qnty+=$tot_cutting_qnty; 
															$gt_tot_cutting_qnty+=$tot_cutting_qnty;$po_tot_cutting_qnty+=$tot_cutting_qnty;$report_data.='</td>															
																
															<td>'.number_format($production_data[$order_id][$item_id][$country_id][$color_id]["today_input_qnty"],0); 
															$job_sewing_in_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_input_qnty"]; 
															$buyer_sewing_in_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_input_qnty"]; 
															$gt_sewing_in_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_input_qnty"];
															$po_sewing_in_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_input_qnty"];$report_data.='</td>
															<td>'; 
															$tot_sewing_in_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["total_input_qnty"]; $report_data.=number_format($tot_sewing_in_qnty,0); $job_tot_sewing_in_qnty+=$tot_sewing_in_qnty; $buyer_tot_sewing_in_qnty+=$tot_sewing_in_qnty; 
															$gt_tot_sewing_in_qnty+=$tot_sewing_in_qnty; $po_tot_sewing_in_qnty+=$tot_sewing_in_qnty;$report_data.='</td>
															
															<td>'.number_format($production_data[$order_id][$item_id][$country_id][$color_id]["today_output_qnty"],0); 
															$job_sewing_out_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_output_qnty"]; 
															$buyer_sewing_out_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_output_qnty"]; 
															$gt_sewing_out_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_output_qnty"];
															$po_sewing_out_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_output_qnty"];
															$report_data.='</td>
															<td>';$tot_sewing_out_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["total_output_qnty"] ; $report_data.=number_format($tot_sewing_out_qnty,0); 
															$job_tot_sewing_out_qnty+=$tot_sewing_out_qnty; 
															$buyer_tot_sewing_out_qnty+=$tot_sewing_out_qnty; 
															$gt_tot_sewing_out_qnty+=$tot_sewing_out_qnty;
															$po_tot_sewing_out_qnty+=$tot_sewing_out_qnty;
															$report_data.='</td>
															<td>'.number_format($production_data[$order_id][$item_id][$country_id][$color_id]["today_sewing_reject_qty"],0); 
															$job_sewing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_sewing_reject_qty"]; 
															$buyer_sewing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_sewing_reject_qty"]; 
															$gt_sewing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_sewing_reject_qty"];
															$po_sewing_reject_qty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_sewing_reject_qty"];
															
															$report_data.='</td>
															<td>'; $total_sewing_reject=$production_data[$order_id][$item_id][$country_id][$color_id]["total_sewing_reject"] ; $report_data.=number_format($total_sewing_reject,0); 
															$job_total_sewing_reject+=$total_sewing_reject; 
															$buyer_total_sewing_reject+=$total_sewing_reject; 
															$gt_total_sewing_reject+=$total_sewing_reject;$po_total_sewing_reject+=$total_sewing_reject;
															
															$report_data.='</td>
															<td>';$sewing_wip=(($tot_sewing_out_qnty+$total_sewing_reject)-$tot_sewing_in_qnty); $report_data.=number_format($sewing_wip,0); 
															$job_sewing_wip+=$sewing_wip; 
															$buyer_sewing_wip+=$sewing_wip; 
															$gt_sewing_wip+=$sewing_wip;$po_sewing_wip+=$sewing_wip;
															
															$report_data.='</td>
																
															<td>'.number_format($production_data[$order_id][$item_id][$country_id][$color_id]["today_poly_qnty"],0); 
															$job_poly_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_poly_qnty"]; 
															$buyer_poly_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_poly_qnty"]; 
															$gt_poly_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_poly_qnty"];
															$po_poly_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_poly_qnty"];
															
															$report_data.='</td>
															<td>'.$tot_poly_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["total_poly_qnty"] ; $report_data.=number_format($tot_poly_qnty,0); 
															$job_tot_poly_qnty+=$tot_poly_qnty; 
															$buyer_tot_poly_qnty+=$tot_poly_qnty; 
															$gt_tot_poly_qnty+=$tot_poly_qnty;
															$po_tot_poly_qnty+=$tot_poly_qnty;
															
															$report_data.='</td>
															<td>'.number_format($production_data[$order_id][$item_id][$country_id][$color_id]["today_poly_rej_qnty"],0); 
															$job_poly_rej_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_poly_rej_qnty"]; 
															$buyer_poly_rej_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_poly_rej_qnty"]; 
															$gt_poly_rej_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_poly_rej_qnty"];
															$po_poly_rej_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_poly_rej_qnty"];
															
															$report_data.='</td>
															<td>';$tot_poly_rej_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["total_poly_rej_qnty"] ; 
															$report_data.= number_format($tot_poly_rej_qnty,0); 
																$job_tot_poly_rej_qnty+=$tot_poly_rej_qnty; 
																$buyer_tot_poly_rej_qnty+=$tot_poly_rej_qnty; 
																$gt_tot_poly_rej_qnty+=$tot_poly_rej_qnty;
																$po_tot_poly_rej_qnty+=$tot_poly_rej_qnty;
																
																$report_data.='</td>
																
																
															<td>';$poly_wip=($tot_sewing_in_qnty-$tot_poly_qnty)-$tot_poly_rej_qnty; 
															$report_data.=number_format($poly_wip,0); 
															$job_poly_wip+=$poly_wip; 
															$buyer_poly_wip+=$poly_wip; 
															$gt_poly_wip+=$poly_wip; 
															$po_poly_wip+=$poly_wip;
															
															$report_data.='</td>
																
															<td>'.number_format($production_data[$order_id][$item_id][$country_id][$color_id]["today_pac_qnty"],0); 
															$job_paking_finish_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_pac_qnty"]; 
															$buyer_paking_finish_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_pac_qnty"]; 
															$gt_paking_finish_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_pac_qnty"];
															$po_paking_finish_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_pac_qnty"];
															
															$report_data.='</td>
															<td>'; $tot_paking_finish_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["total_pac_qnty"] ; 
															
															$report_data.=number_format($tot_paking_finish_qnty,0); 
															$job_tot_paking_finish_qnty+=$tot_paking_finish_qnty;
															$buyer_tot_paking_finish_qnty+=$tot_paking_finish_qnty; 
															$gt_tot_paking_finish_qnty+=$tot_paking_finish_qnty;
															$po_tot_paking_finish_qnty+=$tot_paking_finish_qnty;
															
															$report_data.='</td>
																
															<td>';$finishing_wip=(($tot_paking_finish_qnty)-$tot_poly_qnty); 
															$report_data.=number_format($finishing_wip,0); 
															$job_finishing_wip+=$finishing_wip; 
															$buyer_finishing_wip+=$finishing_wip; 
															$gt_finishing_wip+=$finishing_wip; 
															$po_finishing_wip+=$finishing_wip;
															
															$report_data.='</td>
																
															<td>'.number_format($production_data[$order_id][$item_id][$country_id][$color_id]["today_ex_fac"],0);
															$job_ex_fact_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_ex_fac"];
															$buyer_ex_fact_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_ex_fac"];
															$gt_ex_fact_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_ex_fac"];
															$po_ex_fact_qnty+=$production_data[$order_id][$item_id][$country_id][$color_id]["today_ex_fac"];
															
															$report_data.='</td>
															<td>'.$tot_ex_fact_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["total_ex_fac"] ; 
															$report_data.=number_format($tot_ex_fact_qnty,0); 
															$job_tot_ex_fact_qnty+=$tot_ex_fact_qnty; $buyer_tot_ex_fact_qnty+=$tot_ex_fact_qnty; 
															$gt_tot_ex_fact_qnty+=$tot_ex_fact_qnty;
															$po_tot_ex_fact_qnty+=$tot_ex_fact_qnty;
															
															$report_data.='</td>
															<td>'.$ex_fact_wip=($tot_ex_fact_qnty-$tot_paking_finish_qnty); $report_data.=number_format($ex_fact_wip,0); $job_ex_fact_wip+=$ex_fact_wip; $buyer_ex_fact_wip+=$ex_fact_wip; $gt_ex_fact_wip+=$ex_fact_wip;$po_ex_fact_wip+=$ex_fact_wip;
															
															$report_data.='</td>
															<td>'.$tot_left_qnty=$production_data[$order_id][$item_id][$country_id][$color_id]["total_leftover_qnty"] ; $report_data.=number_format($tot_left_qnty,0); 
																$job_tot_left_qnty+=$tot_left_qnty;
																$buyer_tot_left_qnty+=$tot_left_qnty; 
																$gt_tot_left_qnty+=$tot_left_qnty;
																$po_tot_left_qnty+=$tot_left_qnty;
																
																$report_data.='</td>

															<td>'.$tot_fg_inhand_qnty = $tot_poly_qnty - $tot_ex_fact_qnty - $tot_left_qnty; 
															$report_data.=number_format($tot_fg_inhand_qnty,0);
																$job_tot_fg_inhand_qnty+=$tot_fg_inhand_qnty; $buyer_tot_fg_inhand_qnty+=$tot_fg_inhand_qnty; $gt_tot_fg_inhand_qnty+=$tot_fg_inhand_qnty;$po_tot_fg_inhand_qnty+=$tot_fg_inhand_qnty;
																
																$report_data.='</td>

															<td>'.$value["status"].'</td>
															<td>Remarks</td>
														</tr>';
														
														$i++;
													

												}
											}
										}
										// 25

										
										$report_data.='<tr bgcolor="#92D050">
											<td colspan="10">PO Total:</td>
											<td>'.number_format($po_order_qnty,0); $po_order_qnty=0; $report_data.='</td>
											<td>'.number_format($po_lay_qnty,0); $po_lay_qnty=0;$report_data.='</td>
											<td>'.number_format($po_tot_lay_qnty,0); $po_tot_lay_qnty=0;$report_data.='</td>


											<td>'.number_format($po_cutting_qnty,0); $po_cutting_qnty=0;$report_data.='</td>
											<td>'.number_format($po_tot_cutting_qnty,0); $po_tot_cutting_qnty=0;$report_data.='</td>

											



											<td>'.number_format($po_sewing_in_qnty,0); $po_sewing_in_qnty=0;$report_data.='</td>
											<td>'.number_format($po_tot_sewing_in_qnty,0); $po_tot_sewing_in_qnty=0;$report_data.='</td>

											<td>'.number_format($po_sewing_out_qnty,0); $po_sewing_out_qnty=0;$report_data.='</td>
											<td>'.number_format($po_tot_sewing_out_qnty,0); $po_tot_sewing_out_qnty=0;$report_data.='</td>
											<td>'.number_format($po_sewing_reject_qty,0); $po_sewing_reject_qty=0;$report_data.='</td>
											<td>'.number_format($po_total_sewing_reject,0); $po_total_sewing_reject=0;$report_data.='</td>
											<td>'.number_format($po_sewing_wip,0); $po_sewing_wip=0;$report_data.='</td>

											<td>'.number_format($po_poly_qnty,0); $po_poly_qnty=0;$report_data.='</td>
											<td>'.number_format($po_tot_poly_qnty,0); $po_tot_poly_qnty=0;$report_data.='</td>

											<td>'.number_format($po_poly_rej_qnty,0); $po_poly_rej_qnty=0;$report_data.='</td>
											<td>'.number_format($po_tot_poly_rej_qnty,0); $po_tot_poly_rej_qnty=0;$report_data.='</td>

											<td>'.number_format($po_poly_wip,0); $po_poly_wip=0;$report_data.='</td>

											<td>'.number_format($po_paking_finish_qnty,0); $po_paking_finish_qnty=0;$report_data.='</td>
											<td>'.number_format($po_tot_paking_finish_qnty,0); $po_tot_paking_finish_qnty=0;$report_data.='</td>

											<td>'.number_format($po_finishing_wip,0); $po_finishing_wip=0;$report_data.='</td>

											<td>'.number_format($po_ex_fact_qnty,0); $po_ex_fact_qnty=0;$report_data.='</td>
											<td>'.number_format($po_tot_ex_fact_qnty,0); $po_tot_ex_fact_qnty=0;$report_data.='</td>
											<td>'.number_format($po_ex_fact_wip,0); $po_ex_fact_wip=0;$report_data.='</td>
											<td>'.number_format($po_tot_left_qnty,0); $po_ex_fact_wip=0;$report_data.='</td>
											<td>'.number_format($po_tot_fg_inhand_qnty,0); $po_tot_fg_inhand_qnty=0;$report_data.='</td>
											<td>&nbsp;</td>
											<td>&nbsp;</td>
										</tr>';  

										
									}
									
									$report_data.='<tr bgcolor="#F4F3C4">
										<td colspan="10">Job Total:</td>
										<td>'.number_format($job_order_qnty,0); $job_order_qnty=0; $report_data.='</td>

										<td>'.number_format($job_lay_qnty,0); $job_lay_qnty=0;$report_data.='</td>
										<td>'.number_format($job_tot_lay_qnty,0); $job_tot_lay_qnty=0;$report_data.='</td>


										<td>'.number_format($job_cutting_qnty,0); $job_cutting_qnty=0;$report_data.='</td>
										<td>'.number_format($job_tot_cutting_qnty,0); $job_tot_cutting_qnty=0;$report_data.='</td>

										

											

										<td>'.number_format($job_sewing_in_qnty,0); $job_sewing_in_qnty=0;$report_data.='</td>
										<td>'.number_format($job_tot_sewing_in_qnty,0); $job_tot_sewing_in_qnty=0;$report_data.='</td>
											
										<td>'.number_format($job_sewing_out_qnty,0); $job_sewing_out_qnty=0;$report_data.='</td>
										<td>'.number_format($job_tot_sewing_out_qnty,0); $job_tot_sewing_out_qnty=0;$report_data.='</td>
										<td>'.number_format($job_sewing_reject_qty,0); $job_sewing_reject_qty=0;$report_data.='</td>
										<td>'.number_format($job_total_sewing_reject,0); $job_total_sewing_reject=0;$report_data.='</td>
										<td>'.number_format($job_sewing_wip,0); $job_sewing_wip=0;$report_data.='</td>

										<td>'.number_format($job_poly_qnty,0); $job_poly_qnty=0;$report_data.='</td>
										<td>'.number_format($job_tot_poly_qnty,0); $job_tot_poly_qnty=0;$report_data.='</td>

										<td>'.number_format($job_poly_rej_qnty,0); $job_poly_rej_qnty=0;$report_data.='</td>
										<td>'.number_format($job_tot_poly_rej_qnty,0); $job_tot_poly_rej_qnty=0;$report_data.='</td>

										<td>'.number_format($job_poly_wip,0); $job_poly_wip=0;$report_data.='</td>

										<td>'.number_format($job_paking_finish_qnty,0); $job_paking_finish_qnty=0;$report_data.='</td>
										<td>'.number_format($job_tot_paking_finish_qnty,0); $job_tot_paking_finish_qnty=0;$report_data.='</td>

										<td>'.number_format($job_finishing_wip,0); $job_finishing_wip=0;$report_data.='</td>

										<td>'.number_format($job_ex_fact_qnty,0); $job_ex_fact_qnty=0;$report_data.='</td>
										<td>'.number_format($job_tot_ex_fact_qnty,0); $job_tot_ex_fact_qnty=0;$report_data.='</td>
										<td>'.number_format($job_ex_fact_wip,0); $job_ex_fact_wip=0;$report_data.='</td>
										<td>'.number_format($job_tot_left_qnty,0); $job_ex_fact_wip=0;$report_data.='</td>
										<td>'.number_format($job_tot_fg_inhand_qnty,0); $job_tot_fg_inhand_qnty=0;$report_data.='</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>';  
									
								}
								
								$report_data.='<tr bgcolor="#CCCCCC">
									<td colspan="10">Buyer Total:</td>
									<td>'.number_format($buyer_order_qnty,0); $buyer_order_qnty=0; $report_data.='</td>
									<td>'.number_format($buyer_lay_qnty,0); $buyer_lay_qnty=0;$report_data.='</td>
									<td>'.number_format($buyer_tot_lay_qnty,0); $buyer_tot_lay_qnty=0;$report_data.='</td> 

										
									<td>'.number_format($buyer_cutting_qnty,0); $buyer_cutting_qnty=0;$report_data.='</td>
									<td>'.number_format($buyer_tot_cutting_qnty,0); $buyer_tot_cutting_qnty=0;$report_data.='</td> 

									

									<td>'.number_format($buyer_sewing_in_qnty,0);  $buyer_sewing_in_qnty=0;$report_data.='</td>
									<td>'.number_format($buyer_tot_sewing_in_qnty,0);  $buyer_tot_sewing_in_qnty=0;$report_data.='</td>
										
									<td>'.number_format($buyer_sewing_out_qnty,0);  $buyer_sewing_out_qnty=0;$report_data.='</td>
									<td>'.number_format( $buyer_tot_sewing_out_qnty,0); $buyer_tot_sewing_out_qnty=0;$report_data.='</td>
									<td>'.number_format($buyer_sewing_reject_qty,0); $buyer_sewing_reject_qty=0;$report_data.='</td>
									<td>'.number_format($buyer_total_sewing_reject,0); $buyer_total_sewing_reject=0;$report_data.='</td>
									<td>'.number_format($buyer_sewing_wip,0); $buyer_sewing_wip=0;$report_data.='</td>
										
									<td>'.number_format($buyer_poly_qnty,0); $buyer_poly_qnty=0;$report_data.='</td>
									<td>'.number_format($buyer_tot_poly_qnty,0); $buyer_tot_poly_qnty=0;$report_data.='</td>
										
									<td>'.number_format($buyer_poly_rej_qnty,0); $buyer_poly_rej_qnty=0;$report_data.='</td>
									<td>'.number_format($buyer_tot_poly_rej_qnty,0); $buyer_tot_poly_rej_qnty=0;$report_data.='</td>
										
									<td>'.number_format($buyer_poly_wip,0); $buyer_poly_wip=0;$report_data.='</td>
										
									<td>'.number_format($buyer_paking_finish_qnty,0);  $buyer_paking_finish_qnty=0;$report_data.='</td>
									<td>'.number_format($buyer_tot_paking_finish_qnty,0); $buyer_tot_paking_finish_qnty=0;$report_data.='</td>
										
										
									<td>'.number_format($buyer_finishing_wip,0);  $buyer_finishing_wip=0;$report_data.='</td>
										
									<td>'.number_format($buyer_ex_fact_qnty,0); $buyer_ex_fact_qnty=0;$report_data.='</td>
									<td>'.number_format($buyer_tot_ex_fact_qnty,0); $buyer_tot_ex_fact_qnty=0;$report_data.='</td>
									<td>'.number_format($buyer_ex_fact_wip,0); $buyer_ex_fact_wip=0;$report_data.='</td>
									<td>'.number_format($buyer_tot_left_qnty,0); $buyer_ex_fact_wip=0;$report_data.='</td>
									<td>'.number_format($buyer_tot_fg_inhand_qnty,0); $buyer_tot_fg_inhand_qnty=0;$report_data.='</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
								</tr>';
								
							}


							
							$report_data.='</tbody>


					</table> 
				</div>  
					<table border="1">
					<tfoot>
						<tr>
							<th>&nbsp;</th>
							<th><p>&nbsp;</p></th>
							<th><p>&nbsp;</p></th>
							<th><p>&nbsp;</p></th>
							<th><p>&nbsp;</p></th>
								
							<th><p>&nbsp;</p></th>
							<th><p>&nbsp;</p></th>
							<th><p>&nbsp;</p></th>
							<th><p>&nbsp;</p></th>

							<th>Grand Total</th>
							<th>'.number_format($gt_order_qnty,0).'</th>
							<th>'.number_format($gt_lay_qnty,0).'</th>
							<th>'.number_format($gt_tot_lay_qnty,0).'</th>
								
								
							<th>'.number_format($gt_cutting_qnty,0).'</th>
							<th>'.number_format($gt_tot_cutting_qnty,0).'</th>

							
								
							<th>'.number_format($gt_sewing_in_qnty,0).'</th>
							<th>'.number_format($gt_tot_sewing_in_qnty,0).'</th>
								
							<th>'.number_format($gt_sewing_out_qnty,0).'</th>
							<th>'.number_format($gt_tot_sewing_out_qnty,0).'</th>
							<th>'.number_format($gt_sewing_reject_qty,0).'</th>
							<th>'.number_format($gt_total_sewing_reject,0).'</th>
							<th>'.number_format($gt_sewing_wip,0).'</th>
								
							<th>'.number_format($gt_poly_qnty,0).'</th>
							<th>'.number_format($gt_tot_poly_qnty,0).'</th>
							<th>'.number_format($gt_poly_rej_qnty,0).'</th>
							<th>'.number_format($gt_tot_poly_rej_qnty,0).'</th>							 
							<th>'.number_format($gt_poly_wip,0).'</th>
								
							<th>'.number_format($gt_paking_finish_qnty,0).'</th>
							<th>'.number_format($gt_tot_paking_finish_qnty,0).'</th>
								
							<th>'.number_format($gt_finishing_wip,0).'</th>
								
							<th>'.number_format($gt_ex_fact_qnty,0).'</th>
							<th>'.number_format($gt_tot_ex_fact_qnty,0).'</th>
							<th>'.number_format($gt_ex_fact_wip,0).'</th>
							<th>'.number_format($gt_tot_left_qnty,0).'</th>
							<th>'.number_format($gt_tot_fg_inhand_qnty,0).'</th>
							<th> </th>
							<th> </th>
						</tr>    
					</tfoot>
				</table>  
			</fieldset>  
		</div>     
	</fieldset>';
		
	

 

	foreach (glob("excel_$user_id*.xlsx") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename="excel_".$user_id."_".$name.".xlsx";
	// $create_new_doc = fopen($filename, 'w');
	// $is_created = fwrite($create_new_doc,ob_get_contents());
	//$filename=$user_id."_".$name.".xls";
	$reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
	$spreadsheet = $reader->loadFromString($report_data);
	$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
	$writer->save($filename);
	echo "$filename####$filename####$type";
	exit(); 
}


 

if($action=="remarks_popup")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
	list($po,$item,$country,$color,$job_no)= explode("**", $data);
    extract($_REQUEST);
	$col_size_arr=return_library_array( "SELECT size_number_id,size_number_id from  wo_po_color_size_breakdown where po_break_down_id='$po' and color_number_id='$color' and is_deleted=0 ", "size_number_id", "size_number_id"  );
	$size_arr=return_library_array( "select id, size_name from  lib_size", "id", "size_name"  );
	$color_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
	$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name"  );
	$line_arr = return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
	$resource_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 and status_active=1");

		$sql_lay=" SELECT a.entry_date,a.remarks, a.working_company_id, b.gmt_item_id, c.order_id, c.country_id, b.color_id,c.size_id,   sum(c.size_qty )  as total_lay
		from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c
		where a.id=b.mst_id and b.id=c.dtls_id  and c.order_id='$po' and b.gmt_item_id='$item' and b.color_id='$color' and c.country_id='$country' and a.job_no='$job_no'   and a.status_active=1 and b.status_active=1 and c.status_active=1 
		group by a.working_company_id, b.gmt_item_id, c.order_id, c.country_id, b.color_id,c.size_id, a.entry_date,a.remarks";  
		 
		
		$sql_lay_result=sql_select($sql_lay);
		$type_arr=array(0=>"Cut and Lay",1=>"Cutting",4=>"Sewing Input",5=>"Sewing Output",11=>"Poly",8=>"Packing & Finishing",22=>"Ex-Factory" );
		 
		

		foreach($sql_lay_result as $row)
		{
			 
 			$index=$row[csf("order_id")]."**".$row[csf("entry_date")]."**".$row[csf("remarks")];
			$production_data_0[$index][0][$row[csf("color_id")]][$row[csf("size_id")]]=$row[csf("total_lay")];
			$row_data_0[$index][$row[csf("color_id")]]["dates"] =$row[csf("entry_date")];
			$row_data_0[$index][$row[csf("color_id")]]["remarks"] =$row[csf("remarks")];
			$row_data_0[$index][$row[csf("color_id")]]["order_id"] =$row[csf("order_id")];
			$row_count[0] =111;
			 
 			 
		}

		 $ex_factory_sql="SELECT a.remarks, a.ex_factory_date as entry_date,m.sys_number, a.po_break_down_id as order_id, a.item_number_id, a.country_id, c.color_number_id as color_id,c.size_number_id as size_id,    sum(   b.production_qnty  ) as total_ex_fac
		from pro_ex_factory_delivery_mst m, pro_ex_factory_mst a, pro_ex_factory_dtls b, wo_po_color_size_breakdown c
		where m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0 and m.entry_form!=85 and a.po_break_down_id ='$po' and a.item_number_id='$item' and c.color_number_id='$color' and a.country_id='$country'
		group by a.remarks,  a.ex_factory_date,m.sys_number, a.po_break_down_id, a.item_number_id,c.size_number_id, a.country_id, c.color_number_id";
		
		
		 
		$ex_factory_sql_result=sql_select($ex_factory_sql);
		foreach($ex_factory_sql_result as $row)
		{
			 
 			$index=$row[csf("order_id")]."**".$row[csf("entry_date")]."**".$row[csf("sys_number")]."**".$row[csf("remarks")];
			$production_data_22[$index][22][$row[csf("color_id")]][$row[csf("size_id")]]=$row[csf("total_ex_fac")];
			$row_data_22[$index][$row[csf("color_id")]]["dates"] =$row[csf("entry_date")];
			$row_data_22[$index][$row[csf("color_id")]]["remarks"] =$row[csf("remarks")];
			$row_data_22[$index][$row[csf("color_id")]]["order_id"] =$row[csf("order_id")];
			$row_data_22[$index][$row[csf("color_id")]]["sys_number"] =$row[csf("sys_number")];
			$row_count[22] =311;
			 
 			 
		} 
		

		 $production_sql=" SELECT a.production_type ,a.floor_id,a.sewing_line, a.production_date as entry_date,a.remarks, c.po_break_down_id as order_id, c.item_number_id, c.country_id, c.color_number_id as color_id,c.size_number_id as size_id,sum(b.production_qnty) as qnty 	from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c 
		where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0  and c.po_break_down_id='$po' and c.color_number_id='$color' and c.country_id='$country' and c.item_number_id='$item'
		group by  a.production_type ,a.floor_id,a.sewing_line, a.production_date  ,a.remarks, c.po_break_down_id  , c.item_number_id, c.country_id, c.color_number_id  ,c.size_number_id   ";
		$production_sql_result=sql_select($production_sql);
		 
		foreach($production_sql_result as $row)
		{
			$type=$row[csf("production_type")];
			if($type==1) 
			{
				$index=$row[csf("order_id")]."**".$row[csf("entry_date")]."**".$row[csf("remarks")];
				$production_data_1[$index][$row[csf("production_type")]][$row[csf("color_id")]][$row[csf("size_id")]]=$row[csf("qnty")];
				$row_data_1[$index][$row[csf("color_id")]]["dates"] =$row[csf("entry_date")];
				$row_data_1[$index][$row[csf("color_id")]]["remarks"] =$row[csf("remarks")];
				$row_data_1[$index][$row[csf("color_id")]]["floor_id"] =$row[csf("floor_id")];
				$row_data_1[$index][$row[csf("color_id")]]["sewing_line"] =$row[csf("sewing_line")];
				$row_data_1[$index][$row[csf("color_id")]]["order_id"] =$row[csf("order_id")];

			}

			if($type==4) 
			{
				$index=$row[csf("order_id")]."**".$row[csf("entry_date")]."**".$row[csf("floor_id")]."**".$row[csf("sewing_line")]."**".$row[csf("remarks")];
				$production_data_4[$index][$row[csf("production_type")]][$row[csf("color_id")]][$row[csf("size_id")]]=$row[csf("qnty")];
				$row_data_4[$index][$row[csf("color_id")]]["dates"] =$row[csf("entry_date")];
				$row_data_4[$index][$row[csf("color_id")]]["remarks"] =$row[csf("remarks")];
				$row_data_4[$index][$row[csf("color_id")]]["floor_id"] =$row[csf("floor_id")];
				$row_data_4[$index][$row[csf("color_id")]]["sewing_line"] =$row[csf("sewing_line")];
				$row_data_4[$index][$row[csf("color_id")]]["order_id"] =$row[csf("order_id")];

			}

			if($type==5) 
			{
				$index=$row[csf("order_id")]."**".$row[csf("entry_date")]."**".$row[csf("floor_id")]."**".$row[csf("sewing_line")]."**".$row[csf("remarks")];
				$production_data_5[$index][$row[csf("production_type")]][$row[csf("color_id")]][$row[csf("size_id")]]=$row[csf("qnty")];
				$row_data_5[$index][$row[csf("color_id")]]["dates"] =$row[csf("entry_date")];
				$row_data_5[$index][$row[csf("color_id")]]["remarks"] =$row[csf("remarks")];
				$row_data_5[$index][$row[csf("color_id")]]["floor_id"] =$row[csf("floor_id")];
				$row_data_5[$index][$row[csf("color_id")]]["sewing_line"] =$row[csf("sewing_line")];
				$row_data_5[$index][$row[csf("color_id")]]["order_id"] =$row[csf("order_id")];

			}

			if($type==8) 
			{
				$index=$row[csf("order_id")]."**".$row[csf("entry_date")]."**".$row[csf("floor_id")]."**".$row[csf("sewing_line")]."**".$row[csf("remarks")];
				$production_data_8[$index][$row[csf("production_type")]][$row[csf("color_id")]][$row[csf("size_id")]]=$row[csf("qnty")];
				$row_data_8[$index][$row[csf("color_id")]]["dates"] =$row[csf("entry_date")];
				$row_data_8[$index][$row[csf("color_id")]]["remarks"] =$row[csf("remarks")];
				$row_data_8[$index][$row[csf("color_id")]]["floor_id"] =$row[csf("floor_id")];
				$row_data_8[$index][$row[csf("color_id")]]["sewing_line"] =$row[csf("sewing_line")];
				$row_data_8[$index][$row[csf("color_id")]]["order_id"] =$row[csf("order_id")];

			}

			if($type==11) 
			{
				$index=$row[csf("order_id")]."**".$row[csf("entry_date")]."**".$row[csf("floor_id")]."**".$row[csf("sewing_line")]."**".$row[csf("remarks")];
				$production_data_11[$index][$row[csf("production_type")]][$row[csf("color_id")]][$row[csf("size_id")]]=$row[csf("qnty")];
				$row_data_11[$index][$row[csf("color_id")]]["dates"] =$row[csf("entry_date")];
				$row_data_11[$index][$row[csf("color_id")]]["remarks"] =$row[csf("remarks")];
				$row_data_11[$index][$row[csf("color_id")]]["floor_id"] =$row[csf("floor_id")];
				$row_data_11[$index][$row[csf("color_id")]]["sewing_line"] =$row[csf("sewing_line")];
				$row_data_11[$index][$row[csf("color_id")]]["order_id"] =$row[csf("order_id")];

			}

 			
			$row_count[$row[csf("production_type")]] =$row[csf("production_type")];
			 
 			 
		}
		//print_r($row_count);




	 
     
	?>
    <div id="data_panel" align="center" style="width:100%">

    <?
    foreach($type_arr as $kk =>$v )
    {
    	 
    	if($row_count[$kk] )
    	{
    		if($kk==0){$row_data=$row_data_0;}
    		if($kk==1){$row_data=$row_data_1;}
    		if($kk==22){$row_data=$row_data_22;}
    		if($kk==4){$row_data=$row_data_4;}
    		if($kk==5){$row_data=$row_data_5;}
    		if($kk==8){$row_data=$row_data_8;}
    		if($kk==11){$row_data=$row_data_11;}




		    ?>
		     
				 
		    	<div id="data_panel" align="" style="width:100%">
		    		<label> <strong><? echo $v;?> </strong><label/>

		    			<table width="" align="center" border="1" rules="all" class="rpt_table" >
		    				<thead>
		    					<tr>
		    						<th width="30">SL</th>
		    						<th width="70">Date</th>
		    						<?
		    						if($kk==22)
		    						{
		    							?>
		    							<th width="100">Challan</th>

		    							<?
		    						}

		    						?>
		    						<th width="70">Color</th>

		    						<?
		    						foreach($col_size_arr as $key=>$value)
		    						{

		    							?>
		    							<th width="40"><? echo $size_arr[$value];?></th>
		    							<?

		    						}
		    						?>
		    						<th width="80">Production Qnty</th>
		    						<?
		    						if($kk>2 && $kk!=22 )
		    						{
		    							?>
		    							<th width="80">Floor</th>
		    							<th width="80">Line No</th>

		    							<?
		    						}
		    						?>
		    						<th width="80">Remarks</th>

		    					</tr>
		    				</thead>
		    				<tbody>
		    					<?
		    					$k=1;
		    					foreach($row_data as $key=>$index_val)
		    					{
		    						foreach($index_val as $color_id=>$row)
		    						{
		    							?>
		    							<tr>

		    								<td align="center" width="30"><? echo $k++;?></td>
		    								<td align="center" width="70"><? echo $row["dates"];?></td>
		    								<?
		    								if($kk==22)
		    								{
		    									?>
		    									<td width="100"><? echo $row["sys_number"];?></td>

		    									<?
		    								}

		    								?>

		    								<td align="center" width="70"><? echo $color_arr[$color_id];?></td>

		    								<?
		    								$index=$row["order_id"]."**".$row["dates"]."**".$row["remarks"];
		    								if($kk>2 && $kk!=22)$index=$row["order_id"]."**".$row["dates"]."**".$row["floor_id"]."**".$row["sewing_line"]."**".$row["remarks"];
		    								if($kk==22)$index=$row["order_id"]."**".$row["dates"]."**".$row["sys_number"]."**".$row["remarks"];
		    								//if($kk==1)$index=$row["order_id"]."**".$row["dates"]."**".$row[csf("floor_id")]."**0"."**".$row["remarks"];

		    								$tot=0;
		    								foreach($col_size_arr as $key=>$value)
		    								{
		    									if($kk==0){$row_val=$production_data_0[$index][$kk][$color_id][$key];}
		    									if($kk==1){$row_val=$production_data_1[$index][$kk][$color_id][$key];}
		    									if($kk==22){$row_val=$production_data_22[$index][$kk][$color_id][$key];}
		    									if($kk==4){$row_val=$production_data_4[$index][$kk][$color_id][$key];}
		    									if($kk==5){$row_val=$production_data_5[$index][$kk][$color_id][$key];}
		    									if($kk==8){$row_val=$production_data_8[$index][$kk][$color_id][$key];}
		    									if($kk==11){$row_val=$production_data_11[$index][$kk][$color_id][$key];}


		    									?>
		    									<td align="center" width="40"><? echo $row_val;?></td>
		    									<?
		    									$tot+=$row_val;

		    								}
		    								?>
		    								<td align="center" width="80"><? echo $tot;?></td>
		    								<?
		    								if($kk>2 && $kk!=22)
		    								{
		    									?>
		    									<td width="80"><? echo $floor_arr[$row["floor_id"]];?></td>
		    									<td width="80">
		    									<?  
		    									  $lines=explode(",", $resource_arr[$row["sewing_line"]]);
		    									  $line_name="";
		    									  foreach($lines as $line_val)
		    									  {
		    									  	if($line_name=="")$line_name.=$line_arr[$line_val];
		    									  	else $line_name.=','.$line_arr[$line_val];
		    									  }
		    									  echo $line_name;
		    									  ?>
		    									  	
		    									  </td>

		    									<?
		    								}
		    								?>
		    								<td align="center" width="80"><? echo $row["remarks"];?></td>

		    							</tr>
		    							<?


		    						}
		    					}

		    					?>

		    				</tbody>
		    			</table>


		    	</div>
		    	 
		    	</br>
			 <?
		}
	}
}

 
?>
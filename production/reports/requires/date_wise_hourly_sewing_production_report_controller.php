<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
if (!function_exists('pre')) 
{
	function pre($array)
	{
		echo "<pre>";
		print_r($array);
		echo "</pre>";
	}
 
}
if (!function_exists('fn_num_frmt')) 
{
	function fn_num_frmt($val)
	{
		if(is_nan($val) || is_infinite($val)) { return 0; } 
		return $val; 
	}
}
if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 0, "-- Select Buyer --", $selected, "","");
	exit();
}
if ($action=="load_drop_down_buyer2")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --",$selected, "","");
	exit();	 
}

if ($action=="load_drop_down_location")
{
    extract($_REQUEST);
    $choosenCompany = $choosenCompany;
	echo create_drop_down( "cbo_location_id", 150, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in( $choosenCompany) group by id,location_name  order by location_name","id,location_name", 0, "-- Select location --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_floor")
{
    extract($_REQUEST);
    $choosenLocation = $choosenLocation;
	echo create_drop_down( "cbo_floor_id", 130, "SELECT id,floor_name from lib_prod_floor where location_id in( $choosenLocation ) and status_active =1 and is_deleted=0 group by id,floor_name order by floor_name","id,floor_name", 0, "-- Select Floor --", $selected, "" );
	exit();
}

if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."'  and module_id=7 and report_id=239 and is_deleted=0 and status_active=1");
	//echo $print_report_format.jahid;die;
	//$field_name, $table_name, $query_cond, $return_fld_name, $new_conn
	$print_report_format_arr=explode(",",$print_report_format);

	echo "$('#Show').hide();\n";
	echo "$('#Show2').hide();\n";

	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==147){echo "$('#Show').show();\n";}
			if($id==259){echo "$('#Show2').show();\n";}
			if($id==242){echo "$('#Show3').show();\n";}
		}
	}
	else
	{
		echo "$('#Show').show();\n";
		echo "$('#Show2').show();\n";
		echo "$('#Show3').show();\n";
	}
	exit();
}

if($action=="line_popup")
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
	//echo $company;die;
	if($company=="") $company_name=""; else $company_name=" and b.company_name=$company";//job_no

	// if($buyer==0) $buyer_name=""; else $buyer_name="and b.buyer_name=$buyer";
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company and variable_list=23 and is_deleted=0 and status_active=1");
   	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();

		if($txt_date_from=="")
		{
			if(  $location!=0 ) $cond = " and a.location_id in($location)";
			if( $floor_id!=0 ) $cond.= " and a.floor_id in($floor_id)";
			$line_data="select a.id, b.line_name from prod_resource_mst a,lib_sewing_line b where a.is_deleted=0 and REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=b.id $cond order by b.line_name";
		}
		else
		{
			if(  $location!="" ) $cond = " and a.location_id in($location)";
			if( $floor_id!="" ) $cond.= " and a.floor_id in($floor_id)";

			$line_data="select a.id, c.line_name from prod_resource_mst a, prod_resource_dtls b,lib_sewing_line c where a.id=b.mst_id and REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=c.id and b.pr_date between '".date('d-M-Y',strtotime($txt_date_from))."' and '".date('d-M-Y',strtotime($txt_date_to))."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id,c.line_name order by c.line_name";
		}
		// echo $line_data;

      	echo create_list_view("list_view", "Line ","250","300","310",0, $line_data , "js_set_value", "id,line_name", "", 1, "0", $arr, "line_name", "","setFilterGrid('list_view',-1)","0","",1);
		echo "<input type='hidden' id='txt_selected_id' />";
		echo "<input type='hidden' id='txt_selected' />";

	}
	else
	{
		if( $location!=""  ) $cond = " and location_name= $location";
		if( $floor_id!="" ) $cond.= " and floor_name= $floor_id";
		$line_data="select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name";

	echo create_list_view("list_view", "Line No","250","300","310",0, $line_data , "js_set_value", "id,line_name", "", 1, "0", $arr, "line_name", "","setFilterGrid('list_view',-1)","0","",1) ;
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	}
	exit();
}


if($action=="job_wise_search")
{
	echo load_html_head_contents("Search Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		
		var selected_id = new Array; var selected_name = new Array;var selected_style = new Array;var selected_id_arr = new Array;
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

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
		
		function js_set_value( str ) {
			
			if (str!="") str=str.split("_");
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[0], selected_id_arr ) == -1 ) {
				selected_id_arr.push( str[0] );
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				selected_style.push( str[3] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_style.splice( i, 1 );
			}
			var id = ''; var name = '';var style = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
				style += selected_style[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			style = style.substr( 0, style.length - 1 );
			
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
			$('#hide_style_no').val( style );
			parent.emailwindow.hide();
		}
	
    </script>

	</head>

	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form"> 
			<table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
				<thead>
					<th class="must_entry_caption">Company Name</th>
					<th>Buyer</th>
					<th>Year</th>
					<th>Search By</th>
					<th id="search_by_td_up" width="100">Job No</th>
					<th>
						<input type="reset" name="button" class="formbutton" value="Reset"  style="width:80px;"> 
						<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
						<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
						<input type="hidden" name="hide_style_no" id="hide_style_no" value="" />
					</th>
				</thead>
				<tbody>
					<tr>
						<td align="center">
								<? 
								echo create_drop_down( "cbo_company_name", 130, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'date_wise_hourly_sewing_production_report_controller', this.value, 'load_drop_down_buyer2', 'buyer_td2' );" );
							?>
						</td>
						<td align="center" id="buyer_td2">
								<? 
								echo create_drop_down( "cbo_buyer_name", 130,array(),'',1, "-- All Buyer--",0,"",1 );
							?>
						</td>                  
						<td align="center">	
						<?						
							echo create_drop_down( "cbo_year", 110, $year,"",1, "--Select--", "",'',0 );
						?>
						</td>                 
						<td align="center">	
						<?
							$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
						</td>     
						<td align="center" id="search_by_td">				
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
						</td> 
						<td align="center">
							<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year').value, 'search_list_view', 'search_div', 'date_wise_hourly_sewing_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
						</td>
					</tr>
				</tbody>
			</table>
			<div style="margin-top:15px" id="search_div"></div> 
		</form>
	</div>
	</body>        
	<script type="text/javascript">
		$("#cbo_year").val('<?=$cbo_year;?>');
	</script>   
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}//JobNumberShow

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
	if($company==0) $company_name=""; else $company_name=" and b.company_name in($company)";
	if($buyer==0) $buyer_name=""; else $buyer_name="and b.buyer_name in($buyer)";
	$job_cond='';
	if(str_replace("'","",$job_id)!="")  $job_cond="and b.id in(".str_replace("'","",$job_id).")";
    else  if (str_replace("'","",$job_no)!="") $job_cond="and a.job_no_mst='".$job_no."'";


	if(($txt_date_from!=0 || $txt_date_from!='') && ($txt_date_to!=0 || $txt_date_to!=''))
	{
		$job_year_cond="and b.insert_date between '".change_date_format($txt_date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($txt_date_to,'dd-mm-yyyy','-',1)."'" ;
	}
	else if($cbo_year!=0)
	{
		$job_year_cond=" and extract( year from b.insert_date)=".str_replace("'","",$cbo_year).""; 
	}
	$insert_year="to_char(a.insert_date,'YYYY') as year";
	$sql = "select distinct a.id,a.po_number,b.style_ref_no,b.job_no,$insert_year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $company_name $job_cond $job_year_cond $buyer_name ";
	// echo $sql;die;
	echo create_list_view("list_view", "Year,Job No,Style Ref,Order Number","50,100,120,150,","550","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "year,job_no,style_ref_no,po_number", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}


if($action=="search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}
	
	$search_by=$data[2];
	if(str_replace("'", "", $data[3])!="")
	{
		$search_string="".trim($data[3])."";
	}

	if($search_by==1) 
		$search_field="a.job_no_prefix_num"; 
	else if($search_by==2) 
		$search_field="a.style_ref_no";
	$search_cond="";
	if($search_string!="")	{$search_cond=" and $search_field='$search_string'";}
	$job_year =$data[4];
	
	if($job_year!=0)
	{
		if($db_type==0)
		{
			$job_year_cond=" and year(a.insert_date)='$job_year'";
		}
		else
		{
			$job_year_cond=" and to_char(a.insert_date,'YYYY')='$job_year'";	
		}
	}
	else
	{
		$job_year_cond="";
	}
	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$arr=array (0=>$company_library,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";
	
	
	$sql= "SELECT a.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no from wo_po_details_master a where a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $search_cond $buyer_id_cond $job_no_cond $job_year_cond group by a.id,
         a.job_no, a.insert_date, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no order by a.id desc"; 
    // echo $sql;
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No", "100,100,50,100","550","220",0, $sql , "js_set_value", "id,job_no,style_ref_no","",1,"company_name,buyer_name,0,0,0,0",$arr,"company_name,buyer_name,year,job_no,style_ref_no","",'','0,0,0,0,0','',1) ;
   exit(); 
}

if($action=="report_generate")
{
	?>
	<style type="text/css">
            .block_div {
                    width:auto;
                    height:auto;
                    text-wrap:normal;
                    vertical-align:bottom;
                    display: inline-block;
                    position: !important;
                    -webkit-transform: rotate(-90deg);
                    -moz-transform: rotate(-90deg);
            }

        </style>

	<?
	$process = array( &$_POST );
	// pre($process); die;
	extract(check_magic_quote_gpc( $process ));
	// ============================================================
	if($rptType==1) // floor wise button
	{
		ob_start();
		$companyArr = return_library_array("select id,company_name from lib_company","id","company_name");
		$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name");
		$locationArr = return_library_array("select id,location_name from lib_location","id","location_name");
		$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
		$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
		$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
		$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
		order by sewing_line_serial");
		$job_id = str_replace("'","",$hidden_job_id);
		$hidden_order_id = str_replace("'","",$hidden_order_id);
		// echo $po_id."**".$hidden_order_id; die;
		foreach($lineDataArr as $lRow)
		{
			$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
			$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
			$lastSlNo=$lRow[csf('sewing_line_serial')];
		}
		$company_id=str_replace("'","",$cbo_company_id);
		$rptType=str_replace("'","",$rptType);

		//echo $prod_reso_allo."eee";die;
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
		}

		/*===================================================================================== /
		/									chk	shift time 										/
		/===================================================================================== */
		if($db_type==0)
		{
			$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id in($company_id) and shift_id=1 and pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
		}
		else
		{
			$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id in($company_id) and shift_id=1 and pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
		}

		if($min_shif_start=="")
		{
			echo "<p style='font-size:20px', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";
			disconnect($con);
			die;

		}
		
		$wo_com_arr = explode(",",$company_id);
		// print_r($wo_com_arr);die;
		foreach ($wo_com_arr as $com_id)
		{
			$com_key = $com_id;
			/*===================================================================================== /
			/									get	shift time 										/
			/===================================================================================== */

			$start_time_arr=array();
			if($db_type==0)
			{
				$start_time_data_arr=sql_select("select company_name, shift_id, TIME_FORMAT( prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( lunch_start_time, '%H:%i' ) as lunch_start_time from variable_settings_production where company_name in($com_id) and shift_id=1  and variable_list=26 and status_active=1 and is_deleted=0");
			}
			else
			{
				$start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time,TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($com_id) and  shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
			}
			$lunch_start_time_arr = array();
			foreach($start_time_data_arr as $row)
			{
				$start_time_arr[$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
				$start_time_arr[$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
				$exp = explode(":",$row[csf('lunch_start_time')]);
				$lunch_start_time_arr[$row[csf('company_name')]] = $exp[0]*1;
			}
			$prod_start_hour=$start_time_arr[1]['pst'];
			$global_start_lanch=$start_time_arr[1]['lst'];
			if($prod_start_hour=="") $prod_start_hour="08:00";
			$start_time=explode(":",$prod_start_hour);
			$hour=$start_time[0]*1;
			$minutes=$start_time[1];
			$last_hour=23;
			$lineWiseProd_arr=array();
			$prod_arr=array();
			$start_hour_arr=array();
			$start_hour=$prod_start_hour;
			$start_hour_arr[$hour]=$start_hour;
			for($j=$hour;$j<$last_hour;$j++)
			{
				$start_hour=add_time($start_hour,60);
				$start_hour_arr[$j+1]=substr($start_hour,0,5);
			}
			//echo $pc_date_time;die;
			$start_hour_arr[$j+1]='23:59';
			if($prod_start_hour>$min_shif_start)  $prod_start_hour=$min_shif_start;
			$actual_date=date("Y-m-d");
			$actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$txt_date_from)));
			$actual_time=substr(date("Y-m-d H:i:s",strtotime($pc_date_time)),11,2);
			$acturl_hour_minute=date("H:i",strtotime($pc_date_time));
			$generated_hourarr=array();
			$first_hour_time=explode(":",$min_shif_start);
			$hour_line=$first_hour_time[0]*1;
			$minutes_one=$start_time[1];
			$line_start_hour_arr[$hour_line]=$min_shif_start;

			for($l=$hour_line;$l<$last_hour;$l++)
			{
				$min_shif_start=add_time($min_shif_start,60);
				$line_start_hour_arr[$l+1]=substr($min_shif_start,0,5);
			}

			$line_start_hour_arr[$j+1]='23:59';
			// print_r($start_hour_arr);die;
			/*===================================================================================== /
			/										query condition									/
			/===================================================================================== */
			if(str_replace("'","",$com_id)=="") $company_name=""; else $company_name="and a.serving_company in(".str_replace("'","",$com_id).")";
			if(str_replace("'","",$cbo_location_id)=="") $location=""; else $location="and a.location in(".str_replace("'","",$cbo_location_id).")";
			if(str_replace("'","",$cbo_floor_id)=="") $floor=""; else $floor="and a.floor_id in(".str_replace("'","",$cbo_floor_id).")";
			if(str_replace("'","",$hidden_line_id)=="") $line=""; else $line="and a.sewing_line in(".str_replace("'","",$hidden_line_id).")";
			if(str_replace("'","",$hidden_line_id)=="") $acc_line=""; else $acc_line="and a.id in(".str_replace("'","",$hidden_line_id).")";
			if(str_replace("'","",$cbo_buyer_name)=="") $buyer_id_cond=""; else $buyer_id_cond="and c.buyer_name in(".str_replace("'","",$cbo_buyer_name).")";
			if($job_id=="") $job_cond=""; else $job_cond="and c.id =$job_id";
			if($hidden_order_id=="") $po_cond=""; else $po_cond="and d.id in ($hidden_order_id)";
			// echo $po_cond;
			if(str_replace("'","",trim($txt_date_from))=="") $date_cond=""; else $date_cond=" and a.production_date between $txt_date_from and $txt_date_to";
			// echo $job_cond; die;
			/*===================================================================================== /
			/								get actual resource data								/
			/===================================================================================== */
			$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name in($com_id) and variable_list=23 and is_deleted=0 and status_active=1");

			if($prod_reso_allo==1)
			{
				if(str_replace("'","",$cbo_location_id)==0) $location2=""; else $location2="and a.location_id in(".str_replace("'","",$cbo_location_id).")";
				if(str_replace("'","",$cbo_floor_id)==0) $floor2=""; else $floor2="and a.floor_id in(".str_replace("'","",$cbo_floor_id).")";
				$date_cond2 = str_replace("a.production_date","b.pr_date",$date_cond);
				$prod_resource_array=array();
				$dataArray=sql_select("SELECT a.company_id,a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity,c.target_efficiency,b.smv_adjust,b.smv_adjust_type from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=b.mst_id and a.id=c.mst_id and  a.company_id in($company_id) $date_cond2 $location2 $floor2 $acc_line");
				// echo "SELECT a.company_id,a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=b.mst_id and a.id=c.mst_id and  a.company_id in($company_id) $date_cond2 $location2 $floor2";die;
				$resource_id_arr = array();
				foreach($dataArray as $val)
				{
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['man_power']=$val[csf('man_power')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['operator']=$val[csf('operator')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['helper']=$val[csf('helper')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['terget_hour']=$val[csf('target_per_hour')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['working_hour']=$val[csf('working_hour')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['day_start']=$val[csf('from_date')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['day_end']=$val[csf('to_date')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['capacity']=$val[csf('capacity')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['target_effi']=$val[csf('target_efficiency')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['smv_adjust']=$val[csf('smv_adjust')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['smv_adjust_type']=$val[csf('smv_adjust_type')];
					$resource_id_arr[$val[csf('id')]] = $val[csf('id')];
				}
			}
			// print_r($prod_resource_array);die;

			/* =====================================================================================================/
			/												Gmts Production data									/
			/===================================================================================================== */
			$sql="SELECT a.serving_company as wo_com, a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line,c.id as job_id,c.buyer_name,c.style_ref_no,c.job_no, a.po_break_down_id, a.item_number_id,d.po_number,d.file_no,d.unit_price,d.grouping as ref,b.color_type_id,a.remarks,sum(b.production_qnty) as good_qnty,";
			$first=1;
			for($h=$hour;$h<$last_hour;$h++)
			{
				$bg=$start_hour_arr[$h];
				$end=substr(add_time($start_hour_arr[$h],60),0,5);
				$prod_hour="prod_hour".substr($bg,0,2);
				if($first==1)
				{
					$sql.="sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN b.production_qnty else 0 END) AS $prod_hour,";
				}
				else
				{
					$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5
					THEN b.production_qnty else 0 END) AS $prod_hour,";
				}
				$first++;
			}
			$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN b.production_qnty else 0 END) AS prod_hour23

			FROM  pro_garments_production_mst a ,pro_garments_production_dtls b, wo_po_details_master c, wo_po_break_down d,wo_po_color_size_breakdown e
			WHERE a.id=b.mst_id and a.po_break_down_id=d.id and d.job_id=c.id and d.job_id=e.job_id and d.id=e.po_break_down_id and b.color_size_break_down_id=e.id and a.production_type=5 and b.production_qnty>0 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.serving_company=$com_id $location $floor $line $buyer_id_cond $job_cond $po_cond  $date_cond
			GROUP BY a.serving_company,c.job_no, a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,c.id,c.buyer_name,c.style_ref_no,a.item_number_id,d.po_number,d.unit_price,d.file_no,d.grouping ,b.color_type_id,a.remarks
			ORDER BY a.production_date";
			// echo $sql;
			$res = sql_select($sql);
			$data_array = array();
			$lc_com_array = array();
			$style_wise_po_arr = array();
			$poIdArr=array();
			$jobArr=array();
			$jobIdArr=array();
			$all_style_arr=array();
			$po_item_wise_prod_qty_arr=array();
			$po_item_wise_hourly_prod_qty_arr=array();
			$job_wise_prod_qty_arr=array();
			$po_unit_price_array = array();
			foreach($res as $val)
			{
				$prod_line_array[$val[csf('sewing_line')]] = $val[csf('sewing_line')];
				$poIdArr[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
				$jobArr[$val[csf('job_no')]] = $val[csf('job_no')];
				$jobIdArr[$val[csf('job_id')]] = $val[csf('job_id')];
				$lc_com_array[$val[csf('company_id')]] = $val[csf('company_id')];
				$all_style_arr[$val[csf('style_ref_no')]] = $val[csf('style_ref_no')];
				$style_wise_po_arr[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
				// $line_prod_hour_array[$val[csf('sewing_line')]]++;

				if($val[csf('prod_reso_allo')]==1)
				{
					$sewing_line_id=$prod_reso_arr[$val[csf('sewing_line')]];
					$reso_line_ids.=$val[csf('sewing_line')].',';
				}
				else
				{
					$sewing_line_id=$val[csf('sewing_line')];
				}

				if($lineSerialArr[$sewing_line_id]=="")
				{
					$lastSlNo++;
					$slNo=$lastSlNo;
					$lineSerialArr[$sewing_line_id]=$slNo;
				}
				else $slNo=$lineSerialArr[$sewing_line_id];

				if($rptType==1) // floor wise
				{
					$data_array[$val[csf('wo_com')]][strtotime($val[csf('production_date')])][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]['company_id'].=$val[csf('company_id')]."**";
					$data_array[$val[csf('wo_com')]][strtotime($val[csf('production_date')])][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]['buyer_name'].=$val[csf('buyer_name')]."**";
					$data_array[$val[csf('wo_com')]][strtotime($val[csf('production_date')])][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]['style_ref_no'].=$val[csf('style_ref_no')]."**";
					$data_array[$val[csf('wo_com')]][strtotime($val[csf('production_date')])][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]['job_no'].=$val[csf('job_no')]."**";
					$data_array[$val[csf('wo_com')]][strtotime($val[csf('production_date')])][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]['po_break_down_id'].=$val[csf('po_break_down_id')]."**";
					$data_array[$val[csf('wo_com')]][strtotime($val[csf('production_date')])][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]['po_number'].=$val[csf('po_number')]."**";
					$data_array[$val[csf('wo_com')]][strtotime($val[csf('production_date')])][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]['item_name'].=$garments_item[$val[csf('item_number_id')]]."**";
					$data_array[$val[csf('wo_com')]][strtotime($val[csf('production_date')])][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]['po_item'].=$val[csf('po_break_down_id')]."__".$val[csf('item_number_id')]."__".$val[csf('job_no')]."__".$val[csf('style_ref_no')]."**";
					$data_array[$val[csf('wo_com')]][strtotime($val[csf('production_date')])][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]['prod_reso_allo']=$val[csf('prod_reso_allo')];

					$po_item_wise_prod_qty_arr[$val[csf('wo_com')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][strtotime($val[csf('production_date')])][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]+=$val[csf('good_qnty')];

					$job_wise_prod_qty_arr[$val[csf('wo_com')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][strtotime($val[csf('production_date')])][$val[csf('job_no')]]+=$val[csf('good_qnty')];

					for($h=$hour;$h<=$last_hour;$h++)
					{
						$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2)."";
						$data_array[$val[csf('wo_com')]][strtotime($val[csf('production_date')])][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]]['qty'][$prod_hour]+=$val[csf($prod_hour)];
						$po_item_wise_hourly_prod_qty_arr[$val[csf('wo_com')]][strtotime($val[csf('production_date')])][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]['qty'][$prod_hour]+=$val[csf($prod_hour)];

						/* if($val[csf($prod_hour)]>0)
						{
							$line_prod_hour_array[$val[csf('sewing_line')]]++;
						} */

					}

					$po_unit_price_array[$val[csf('po_break_down_id')]]=$val[csf('UNIT_PRICE')];
				}
				else // order wise
				{

				}
			}
			// pre($poIdArr); die;
			// echo "<pre>";print_r($data_array);die;
			/*===================================================================================== /
			/										NPT Min 										/
			/===================================================================================== */			
			if(str_replace("'","",trim($txt_date_from))=="") $idle_date_cond=""; else $idle_date_cond=" and c.idle_date between $txt_date_from and $txt_date_to";
			$line_id_cond = where_con_using_array($resource_id_arr,0,"a.id");
			$sql = "SELECT c.prod_resource_id, c.id as idle_mst_id, d.id as idle_dtls_id, c.LINE_IDS as line_number,c.string_data, c.location_id, c.floor_id, c.idle_date, d.category_id, d.cause_id, d.duration_hour, d.end_hour, d.end_minute, d.manpower, d.start_hour, d.start_minute, c.remarks
  			from sewing_line_idle_mst c,sewing_line_idle_dtls d,prod_resource_mst a
          	where c.id = d.mst_id and c.prod_resource_id=a.id and c.is_deleted = 0 and a.is_deleted = 0 and c.status_active = 1 and d.status_active = 1 and d.status_active = 1  and c.idle_date = ".$txt_date_from."
 
 			and c.company_id=$com_id  $floor $line_id_cond $idle_date_cond and c.is_deleted = 0 and c.is_deleted = 0";
			// echo $sql;die;
			$res = sql_select($sql);
			$npt_min_array = array();
			foreach ($res as $r) 
			{
				$manpower = $r[csf('manpower')];
				$duration = $r[csf('duration_hour')];
				$idle_date_chk = strtotime($r[csf('idle_date')]);
				
				$idle_mnt = $duration*$manpower*60;
				$npt_min_array[strtotime($r[csf('idle_date')])][$r[csf('prod_resource_id')]] += $idle_mnt;
			}
			// echo "<pre>";print_r($npt_min_array);die;
			/*===================================================================================== /
			/									Operation Bulletin 									/
			/===================================================================================== */
			$style_cond = where_con_using_array($all_style_arr,1,"a.style_ref");
			$sqlgsd="SELECT a.PROCESS_ID,a.style_ref,a.gmts_item_id,b.id, b.mst_id, b.row_sequence_no, b.body_part_id, b.lib_sewing_id, b.resource_gsd, b.attachment_id, b.efficiency, b.total_smv, b.target_on_full_perc from PPL_GSD_ENTRY_MST a,ppl_gsd_entry_dtls b where a.id=b.mst_id $style_cond and a.bulletin_type=4 and b.is_deleted=0 order by b.row_sequence_no asc";
			// echo $sqlgsd;die;
			$gsd_res=sql_select($sqlgsd);
			$mst_id_arr = array();
			foreach($gsd_res as $row)
			{
				$mst_id_arr[$row['MST_ID']] = $row['MST_ID'];
			}
			$mst_id_cond = where_con_using_array($mst_id_arr,0,"a.gsd_mst_id");
			// ======================================================================
			$balanceDataArray=array();
			$blData=sql_select("SELECT a.id, gsd_dtls_id, smv, layout_mp,a.EFFICIENCY from ppl_balancing_mst_entry a, ppl_balancing_dtls_entry b where a.id=b.mst_id and a.balancing_page=1 $mst_id_cond and a.is_deleted=0 and b.is_deleted=0");
			// echo "SELECT a.id, gsd_dtls_id, smv, layout_mp,a.EFFICIENCY from ppl_balancing_mst_entry a, ppl_balancing_dtls_entry b where a.id=b.mst_id and a.balancing_page=1 $mst_id_cond and a.is_deleted=0 and b.is_deleted=0";
			foreach($blData as $row)
			{
				$balanceDataArray[$row[csf('gsd_dtls_id')]]['smv']=$row[csf('smv')];
				$balanceDataArray[$row[csf('gsd_dtls_id')]]['layout_mp']=$row[csf('layout_mp')];
				$balanceDataArray[$row[csf('gsd_dtls_id')]]['efficiency']=$row[csf('efficiency')];
			}
			
			$gsd_data_array = array();

			foreach($gsd_res as $slectResult)
			{
				if($balanceDataArray[$slectResult[csf('id')]]['smv']>0)	
				{
					$smv=$balanceDataArray[$slectResult[csf('id')]]['smv'];
				}
				else
				{
					$smv=$slectResult[csf('total_smv')];
				}
				
				$rescId=$slectResult[csf('resource_gsd')];
				$layOut=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
				$efficiency = $balanceDataArray[$slectResult[csf('id')]]['efficiency'];
				// echo $slectResult[csf('id')]."_".$efficiency.","; 
				if($rescId==40 || $rescId==41 || $rescId==43 || $rescId==44 || $rescId==48 || $rescId==68 || $rescId==69 || $rescId==70 || $rescId==147)
				{
					$helperSmv=$helperSmv+$smv;
					$helperMp=$helperMp+$layOut;
				}
				else if($rescId==53)
				{
					$fIMSmv=$fIMSmv+$smv;
					$fImMp=$fImMp+$layOut;
				}
				else if($rescId==54)
				{
					$fQISmv=$fQISmv+$smv;
					$fQiMp=$fQiMp+$layOut;
				}
				else if($rescId==55)
				{
					$polyHelperSmv=$polyHelperSmv+$smv;
					$polyHelperMp=$polyHelperMp+$layOut;
				}
				else if($rescId==56)
				{
					$pkSmv=$pkSmv+$smv;
					$pkMp=$pkMp+$layOut;
				}
				else if($rescId==90)
				{
					$htSmv=$htSmv+$smv;
					$htMp=$htMp+$layOut;
				}
				else if($rescId==176)
				{
					$imSmv=$imSmv+$smv;
					$imMp=$imMp+$layOut;
				}
				else
				{
					$machineSmv=$machineSmv+$smv;
					$machineMp=$machineMp+$layOut;
					
					$mpSumm[$rescId]+= $layOut;
				}
				$i++;
				$totMpSumm = $helperMp + $machineMp + $sQiMp + $fImMp + $fQiMp + $polyHelperMp + $pkMp + $htMp + $imMp;
				$totHpSumm = $helperMp + $sQiMp + $fImMp + $fQiMp + $polyHelperMp + $pkMp + $htMp + $imMp;
				// echo $helperMp."<br>";
				
				$gsd_data_array[$slectResult['STYLE_REF']][$slectResult['GMTS_ITEM_ID']]['operator'] = $machineMp;
				$gsd_data_array[$slectResult['STYLE_REF']][$slectResult['GMTS_ITEM_ID']]['sew_helper'] = $totHpSumm;
				$gsd_data_array[$slectResult['STYLE_REF']][$slectResult['GMTS_ITEM_ID']]['plan_man'] = $totMpSumm;
				if (!$gsd_data_array[$slectResult['STYLE_REF']][$slectResult['GMTS_ITEM_ID']]['efficiency']) 
				{
					$gsd_data_array[$slectResult['STYLE_REF']][$slectResult['GMTS_ITEM_ID']]['efficiency'] = $efficiency;
					 
				}
				$gsd_data_array[$slectResult['STYLE_REF']][$slectResult['GMTS_ITEM_ID']]['smv'] += $smv;
			}
			// pre($gsd_data_array); die;
			
			// echo "<pre>";print_r($balanceDataArray);echo "</pre>";die;
			/*===================================================================================== /
			/										smv sorce 										/
			/===================================================================================== */
			$lc_com_ids = implode(",",$lc_com_array);
			$poIds_cond = where_con_using_array($poIdArr,0,"b.id");
			$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($lc_com_ids) and variable_list=25 and   status_active=1 and is_deleted=0");
			// echo $smv_source;die;

			if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
			if($smv_source==3) // from gsd enrty
			{
				$style_cond = where_con_using_array($all_style_arr,1,"a.style_ref");
				$sql_item="SELECT a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= $txt_date_to and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and a.BULLETIN_TYPE=4 $style_cond  group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID ORDER BY a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC";//and a.APPROVED=1
				$gsdSqlResult=sql_select($sql_item);
				//echo $sql_item;die;

				foreach($gsdSqlResult as $rows)
				{
					foreach($style_wise_po_arr[$rows['STYLE_REF']] as $po_id)
					{
						if($item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]=='')
						{
							$item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]=$rows['TOTAL_SMV'];
						}
					}
				}
			}
			else
			{
				$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost,c.smv_set from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c where b.job_id=a.id and b.job_id=c.job_id and a.company_name in($lc_com_ids) $poIds_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
				// echo $sql_item;
				$resultItem=sql_select($sql_item);

				foreach($resultItem as $itemData)
				{
					if($smv_source==1)
					{
						$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
					}
					if($smv_source==2)
					{
						$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs_precost')];
					}
				}
			}
			// echo "<pre>";print_r($item_smv_array);echo "</pre>";

			/*===================================================================================== /
			/										po active days									/
			/===================================================================================== */
			$poIds_cond2 = where_con_using_array($poIdArr,0,"c.id");
			$po_active_sql="SELECT a.sewing_line,a.production_date,b.job_no from  pro_garments_production_mst a , wo_po_break_down c,wo_po_details_master b where a.po_break_down_id=c.id and c.job_id=b.id and a.production_type=5 and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and a.serving_company=$com_id $location $floor $line $buyer_id_cond  $poIds_cond2 group by  a.sewing_line,a.production_date,b.job_no";
			//echo $po_active_sql;die;
			foreach(sql_select($po_active_sql) as $vals)
			{
				$prod_dates=strtotime($vals[csf('production_date')]);
				if($duplicate_date_arr[$vals[csf('sewing_line')]][$vals[csf('job_no')]][$prod_dates]=="")
				{
					$active_days_arr[$vals[csf('sewing_line')]][$vals[csf('job_no')]]++;
					$active_days_arr_powise[$vals[csf('po_break_down_id')]][$vals[csf('item_number_id')]]+=1;
					$duplicate_date_arr[$vals[csf('sewing_line')]][$vals[csf('job_no')]][$prod_dates]=$prod_dates;
				}
			}
			// echo "<pre>"; print_r($active_days_arr);

			/*===============================================================================/
			/                                  Booking Data                                  /
			/============================================================================== */
			$po_id_cond = where_con_using_array($poIdArr,0,"b.po_break_down_id");
			$sql = "SELECT a.booking_no, b.po_break_down_id as po_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_type=1 $po_id_cond";
			$res= sql_select($sql);
			$booking_no_arr = array();
			foreach ($res as $v)
			{
				$booking_no_arr[$v['PO_ID']] .= $v['BOOKING_NO'].",";
			}

			$tot_td = 0;
			for($k=$hour; $k<=$last_hour; $k++)
			{
				$tot_td++;
			}
			// ========================== costing per and cm =====================
			$job_id_cond = where_con_using_array($jobIdArr,0,"job_id");
			$costing_per_arr = return_library_array("SELECT job_no, costing_per from wo_pre_cost_mst where status_active=1 $job_id_cond","job_no","costing_per");
			$effi_per_arr = return_library_array("SELECT job_no, sew_effi_percent from wo_pre_cost_mst where status_active=1 $job_id_cond","job_no","sew_effi_percent");
			$costing_date_arr = return_library_array("SELECT job_no, costing_date from wo_pre_cost_mst where status_active=1 $job_id_cond","job_no","costing_date");
			$exchange_rate_arr = return_library_array("SELECT job_no, exchange_rate from wo_pre_cost_mst where status_active=1 $job_id_cond","job_no","exchange_rate");
			$cm_arr = return_library_array("SELECT job_no, cm_cost from wo_pre_cost_dtls where status_active=1 $job_id_cond","job_no","cm_cost");
			$fob_price_arr = return_library_array("SELECT job_no, price_pcs_or_set from wo_pre_cost_dtls where status_active=1 $job_id_cond","job_no","price_pcs_or_set");
			// echo "<pre>"; print_r($effi_per_arr);die;
			// ================================= no production line ====================================
			$prod_lines = implode(",",$prod_line_array);
			if(str_replace("'","",$cbo_line_status)!=2)
			{
				if(str_replace("'","",$cbo_location_id)==0) $location2=""; else $location2="and a.location_id in(".str_replace("'","",$cbo_location_id).")";
				if(str_replace("'","",$cbo_floor_id)==0) $floor2=""; else $floor2="and a.floor_id in(".str_replace("'","",$cbo_floor_id).")";
				$date_cond2 = str_replace("a.production_date","b.pr_date",$date_cond);
				// $prod_resource_array=array();
				$dataArray=sql_select("SELECT a.company_id,a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity,c.target_efficiency,2 as type_line from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=b.mst_id and a.id=c.mst_id and b.MAST_DTL_ID=c.id and a.id not in($prod_lines) and  a.company_id in($company_id) $date_cond2 $location2 $floor2 $acc_line"); // and c.po_id in ($poIdArr)
				// echo "SELECT a.company_id,a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=b.mst_id and b.MAST_DTL_ID=c.id and a.id=c.mst_id and a.id not in($prod_lines) and  a.company_id in($company_id) $date_cond2 $location2 $floor2";die;
				foreach($dataArray as $val)
				{
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['type_line']=$val[csf('type_line')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['man_power']=$val[csf('man_power')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['operator']=$val[csf('operator')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['helper']=$val[csf('helper')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['terget_hour']=$val[csf('target_per_hour')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['working_hour']=$val[csf('working_hour')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['day_start']=$val[csf('from_date')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['day_end']=$val[csf('to_date')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['capacity']=$val[csf('capacity')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['target_effi']=$val[csf('target_efficiency')];

					$sewing_line_arr=explode(",",$prod_reso_arr[$val[csf('id')]]);
					if($lineSerialArr[$sewing_line_arr[0]]=="")
					{
						$lastSlNo++;
						$slNo=$lastSlNo;
						$lineSerialArr[$sewing_line_arr[0]]=$slNo;
					}
					else $slNo=$lineSerialArr[$sewing_line_arr[0]];

					$data_array[$val[csf('company_id')]][strtotime($val[csf('pr_date')])][$val[csf('floor_id')]][$slNo][$val[csf('id')]]['no_prod'] = 1;
				}
			}
			// pre($data_array); die;
			// =================================== cost per min ==================================
			$sql = "SELECT id,APPLYING_PERIOD_DATE, APPLYING_PERIOD_TO_DATE,COST_PER_MINUTE from LIB_STANDARD_CM_ENTRY where status_active=1 and is_deleted=0 and company_id=$com_id order by APPLYING_PERIOD_DATE";
			// echo $sql;die;
			$res = sql_select($sql);
			$cpm_app_period_arr = array();

			foreach($res as $row )
			{
				$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
				$diff=datediff('m',$applying_period_date,$applying_period_to_date);
				// echo $diff."<br>";
				for($j=0;$j<=$diff;$j++)
				{
					$newMonth = date('m-Y', strtotime($applying_period_date.' + '.$j.' months'));
					$cpm_app_period_arr[$newMonth]=$row[csf('cost_per_minute')];
					// echo $newMonth."<br>";
				}
			}

			/* foreach ($res as $v)
			{
				$cpm_app_period_arr[$v['ID']]['applying_period_date'] = $v['APPLYING_PERIOD_DATE'];
				$cpm_app_period_arr[$v['ID']]['applying_period_to_date'] = $v['APPLYING_PERIOD_TO_DATE'];
				$cpm_app_period_arr[$v['ID']]['cpm'] = $v['COST_PER_MINUTE'];
			} */
			// datediff('')
			/* $date_wise_cpm_arr = array();
			foreach ($cpm_app_period_arr as $cpm_id => $v)
			{
				$dt = GetDays($v['applying_period_date'],$v['applying_period_to_date']);
				// echo "<pre>"; print_r($dt);die;

				$datediff = datediff('d',$v['applying_period_date'],$v['applying_period_to_date']);
				for ($i=0; $i < $datediff; $i++)
				{
					// echo $i."<br>";
					$date_wise_cpm_arr[date('Y-m-d', strtotime("+".$i." day", strtotime($v['applying_period_date'])))] = $v['cpm'];
				}
			} */
			// echo "<pre>"; print_r($cpm_app_period_arr);die;
			// =========================== conversion rate ============================
			$sql = "SELECT conversion_rate,con_date from currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 and company_id=$com_id order by con_date desc";
			// echo $sql;die;
			$res = sql_select($sql);
			foreach ($res as $v)
			{
				# code...
			}
			// =========count rowspand ====================
			$rowspan_arr = array();
			$floor_wise_tot_line = array();
			$gr_tot_line = 0;
			foreach ($data_array[$com_id] as $date_key => $date_data)
			{
				// foreach ($com_data as $date_key => $date_data)
				// {
					foreach ($date_data as $flr_id => $flr_data)
					{
						ksort($flr_data);
						foreach ($flr_data as $li_sl => $sl_data)
						{
							foreach ($sl_data as $l_id => $r)
							{

								$floor_wise_tot_line[$com_key][$date_key][$flr_id]++;
								$rowspan_arr[$com_key][$date_key][$flr_id]++;
								$gr_tot_line++;
							}
						}
					}
				// }
			}
			// echo "<pre>"; print_r($floor_wise_tot_line);die;
			// ====================== current hour ================
			/* $time1 = strtotime($hour.":00");
			$time2 = strtotime(date('H:i:s'));
			// echo $time2."==".strtotime(str_replace("'","",$txt_date));die;

			if(substr($global_start_lanch,0,2) < substr(date('H:i:s'),0,2))
			{
				// $difference_hour = round(((abs($time2 - $time1) / 3600)),0);
				$cur_difference_hour = (int)(abs($time2 - $time1) / 3600);
				$cur_difference_hour = $cur_difference_hour - 1;
				// echo $cur_difference_hour."==SSSSSSSS";
			}
			else
			{
				$cur_difference_hour = round(((abs($time2 - $time1) / 3600)),0);
			} */


			$time1 = $hour;
			$time2 = date('H');
			// echo $time2."==".strtotime(str_replace("'","",$txt_date));die;

			if(substr($global_start_lanch,0,2) < $time2)
			{
				// $difference_hour = round(((abs($time2 - $time1) / 3600)),0);
				$cur_difference_hour = (int) $time2 - $time1;
				$cur_difference_hour = $cur_difference_hour;// - 1;
				// echo $cur_difference_hour."==SSSSSSSS";
			}
			else
			{
				$cur_difference_hour = (int) $time2 - $time1;
			}
			// echo $cur_cur_difference_hour;die;
			// print_r($rowspan_arr);
			$tbl_width = 4390+($tot_td*50);
			// pre($data_array );die;
			
			
			$l=0;
			?>
			<fieldset style="width:<?=$tbl_width+20;?>px">
				<table width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0">
					<tr class="form_caption">
						<td colspan="58" align="center" style="font-size: 20px;"><? echo $report_title; ?></td>
					</tr>
					<tr class="form_caption">
						<td colspan="58" align="center" style="font-size: 17px;"><? echo $companyArr[$com_key]; ?></td>
					</tr>
					<tr class="form_caption">
						<td colspan="58" align="center" style="font-size: 15px;"><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date_from)) ); ?></td>
					</tr>
				</table>
				<br />
				<table id="table_header_1" class="rpt_table" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<caption style="text-align: left;color:red;font-weight:bold;font-size:16px;">"You will obtain accurate eff% data after the current production date."</caption>
					<thead>
						<tr>
							<th width="30">SL</th>
							<th width="60">Date</th>
							<th width="120">Working Company</th>
							<th width="120">Floor Name</th>
							<th width="80">Line No</th>
							<th width="120">LC Company</th>
							<th width="120">Buyer</th>
							<th width="120">Job</th>
							<th width="120">Fab. Booking</th>
							<th width="120">Order No</th>
							<th width="120">Style</th>
							<th width="120">Garments Item</th>
							<th width="80" title="From Operation Buletin">SMV</th>
							<th width="80">Avg. SMV</th>
							<th width="80">Operator</th>
							<th width="80">Helper</th>
							<th width="80">Man Power</th>
							<th width="80">Active Prod Days</th>
							<th width="80">Day Line Capacity Pcs</th>
							<th width="80">Plan Working Hour</th>
							<th width="80">Line Prod. Working Hour</th>
							<th width="80">Hourly Target Pcs</th>
							<th width="80">Current Hour</th>
							<th width="80">As on Current Hourly Target Pcs</th>
							<th width="80" title="[As on Current Prod - As on Current Hourly Target Pcs]">Hourly Prod Varience</th>
							<th width="80">Total Target</th>
							<th width="80">General Prod.</th>
							<th width="80">OT Prod.</th>
							<th width="80">Total Prod.</th>
							<th width="80">Total Varience</th>
							<th width="80" title=" [Current Hourx60xTTL Manpower]">Available Min. Till Current Hr.</th>
							<th width="80" title=" [SMV Adjustment + Man Power x Plan Working Hour x 60]">Plan Available Min.</th>
							<th width="80">NPT Min.</th>
							<th width="80" title="[General Production x SMV">Gen. Prod. Min.</th>
							<th width="80" title="[OT Production x SMV]">OT Prod. Min.</th>
							<th width="80">Tot Prod. Min.</th>
							<th width="80" title="[TTL Production/(Target per hour*Working Hour)]">Target Hit Rate%</th>
							<th width="80" title="{From Operation Buletin}">Target Effi%</th>
							<th width="80" title="[TTL Produce Min/Available Min. Till Current Hour]">Line Effi % Till Current Hour</th>
							<th width="80">Effi Gap</th>
							<th width="80" title="[Total Prod. Min. / Plan Available Min]">Effi% on Plan Hour</th>
							<th width="80">Style Change</th>
							<th width="80" title="[From Pre-Costing)]">CM Pcs</th>
							<th width="80" title="[CM/PC * TTL Production]">Total CM</th>
							<th width="80" title="[((CPM/Production Bulletin Eff%)*Production SMV * Costing Per)/Exchange Rate]">Prod CM Pcs</th>
							<th width="80" title="[Prod CM Pcs X TTL Production]">Total Prod. CM</th>
							<th width="80" title="[Total CM * ER]">CM Earned in BDT</th>
							<th width="80" title="[Target Production * CM per PC]">Target CM</th>
							<!-- <th width="80">Avg Unit Price</th>
							<th width="80" title="(TTL. Prod.*Unit Price)">Tot Prod Value FOB</th>
							<th width="80" title="(Unit price*Total Target Pcs)">Target Value FOB</th> -->
							<th width="80" title="[CPM*Available Min till current Hr.]">Line Cost BDT</th>
							<th width="80" title="[Line Cost BDT/ (CM Pcs*ER)]">BEP Units</th>
							<!-- <th width="80" title="[Total Production x Fob value x 15%]">Earn Value FOB USD</th>
							<th width="80" title="[Earnvalue * convertion rate dollar]">Earn Value FOB BDT</th> -->
							<th width="80" title="[CM Earned in BDT - Line Cost BDT]">Line Profit</th>
							<?
							for($k=$hour; $k<=$last_hour; $k++)
							{
								?>
								<th width="50" style="vertical-align:middle"></p><div class="block_div"><?=substr($start_hour_arr[$k],0,5)."-<br>".substr($start_hour_arr[$k+1],0,5);?></div></p></th>
								<?
							}
							?>
						</tr>
					</thead>
				</table>
				<div style="width:<?=$tbl_width+20;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<tbody>
							<?
							$i=1;
							$floor_tot_array = array();
							$gr_tot_array = array();
							$gr_tot_smv = 0;
							$gr_tot_avg_smv = 0;
							$gr_tot_operator = 0;
							$gr_tot_helper = 0;
							$gr_tot_man_power = 0;
							$gr_tot_act_days = 0;
							$gr_tot_cap_pcs = 0;
							$gr_tot_plan_wo_hour = 0;
							$gr_tot_prod_hour = 0;
							$gr_tot_hour_trg_pcs = 0;
							$gr_tot_cur_hour = 0;
							$gr_tot_as_on_cur_hour_trg_pcs = 0;
							$gr_tot_hour_prod_varience = 0;
							$gr_tot_target = 0;
							$gr_tot_gen_prod = 0;
							$gr_tot_ot_prod = 0;
							$gr_tot_prod = 0;
							$gr_tot_varience = 0;
							$gr_tot_avl_min = 0;
							$gr_tot_man_min_used = 0;
							$gr_tot_npt = 0;
							$gr_tot_gen_prod_min = 0;
							$gr_tot_ot_prod_min = 0;
							$gr_tot_prod_min = 0;
							$gr_tot_target_hit = 0;
							$gr_tot_target_effi = 0;
							$gr_tot_achv_effi = 0;
							$gr_tot_effi_gap = 0;
							$gr_tot_line_effi = 0;
							$gr_tot_style_cng = 0;
							$gr_tot_cm_pcs = 0;
							$gr_tot_cm = 0;
							$gr_tot_prod_cm_pcs = 0;
							$gr_tot_prod_cm = 0;
							$gr_tot_target_cm = 0;
							$gr_tot_fob_val = 0;
							$gr_tot_target_fob_val = 0;
							$gr_tot_bep_unit = 0;
							$gr_tot_earn_val_fob_usd = 0;
							$gr_tot_earn_val_fob_bdt = 0;
							$gr_tot_line_cost = 0;
							$gr_tot_line_profit = 0;
							foreach ($data_array[$com_id] as $date_key => $date_data)
							{
								foreach ($date_data as $flr_id => $flr_data)
								{
									$flCount = 0;
									ksort($flr_data);
									$flr_tot_operator = 0;
									$flr_tot_helper = 0;
									$flr_tot_man_power = 0;
									$flr_tot_act_days = 0;
									$flr_tot_cap_pcs = 0;
									$flr_tot_plan_wo_hour = 0;
									$flr_tot_prod_hour = 0;
									$flr_tot_hour_trg_pcs = 0;
									$flr_tot_cur_hour = 0;
									$flr_tot_as_on_cur_hour_trg_pcs = 0;
									$flr_tot_hour_prod_varience = 0;
									$flr_tot_target = 0;
									$flr_tot_gen_prod = 0;
									$flr_tot_ot_prod = 0;
									$flr_tot_prod = 0;
									$flr_tot_varience = 0;
									$flr_tot_avl_min = 0;
									$flr_tot_man_min_used = 0;
									$flr_tot_npt = 0;
									$flr_tot_gen_prod_min = 0;
									$flr_tot_ot_prod_min = 0;
									$flr_tot_prod_min = 0;
									$flr_tot_target_hit = 0;
									$flr_tot_target_effi = 0;
									$flr_tot_achv_effi = 0;
									$flr_tot_effi_gap = 0;
									$flr_tot_line_effi = 0;
									$flr_tot_style_cng = 0;
									$flr_tot_cm_pcs = 0;
									$flr_tot_cm_earn = 0;
									$flr_tot_target_cm = 0;
									$flr_tot_cm = 0;
									$flr_tot_prod_cm_pcs = 0;
									$flr_tot_prod_cm = 0;
									$flr_tot_fob_val = 0;
									$flr_tot_target_fob_val = 0;
									$flr_tot_bep_unit = 0;
									$flr_tot_earn_val_fob_usd = 0;
									$flr_tot_earn_val_fob_bdt = 0;
									$flr_tot_line_cost = 0;
									$flr_tot_line_profit = 0;

									$flr_tot_smv = 0;
									$flr_tot_avg_smv = 0;

									foreach ($flr_data as $li_sl => $sl_data)
									{
										foreach ($sl_data as $l_id => $r)
										{

											$sewing_line='';
											if($r['prod_reso_allo']==1)
											{
												$sewing_line_ids=$prod_reso_arr[$l_id];
												$sl_ids_arr = explode(",", $sewing_line_ids);
												foreach($sl_ids_arr as $val)
												{
													if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
												}
											}
											else
											{
												$sewing_line=$lineArr[$l_id];
											}
											// ======================= smv =================
											$item_smv = '';
											$tot_smv = 0;
											$item_smv_count=0;
											$produce_minit = 0;
											$booking_no = "";
											$general_prod_min = 0;
											$ot_prod_min = 0;
											$po_count = 0;
											// $tot_cm = 0;
											$gsd_efficiency = '';
											$line_tot_smv = 0;
											$line_tot_min = 0;
											// $cm_counter = 0;
											$po_item_arr = array_unique(array_filter(explode("**",$r['po_item'])));
											// print_r($po_item_arr);
											$po_chk_arr = array();
											$unit_price_arr = array();
											$style_running_arr = array();
											$job_wise_gsd_efficiency_arr = array();
											$job_itm_chk_arr = array();
											$style_prod_qty_arr = array();
											$style_prod_min_arr = array();
											foreach ($po_item_arr as $po_item_data)
											{
												// echo $po_item_data."dddd<br>";
												$po_item_ex_arr = explode("__",$po_item_data);
												// echo $po_item_ex_arr[2]."<br>";
												$job = $po_item_ex_arr[2];
												$line_tot_hour = 0;
												if($po_chk_arr[$po_item_ex_arr[0].$po_item_ex_arr[1].$po_item_ex_arr[2]]=="")
												{
													$general_prod_qty = 0;
													$ot_prod_qty = 0;
													$gen_last_prod_hour = "";
													$ot_last_prod_hour = "";
													$m=1;
													for($k=$hour; $k<=$last_hour; $k++)
													{
														$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
														if($m<=9)
														{
															// $general_prod_qty += $r['qty'][$prod_hour];
															$general_prod_qty += $po_item_wise_hourly_prod_qty_arr[$com_key][$date_key][$flr_id][$li_sl][$l_id][$po_item_ex_arr[0]][$po_item_ex_arr[1]]['qty'][$prod_hour];
															if($r['qty'][$prod_hour]>0)
															{
																$gen_last_prod_hour=substr($start_hour_arr[$k],0,2);
																// $line_tot_hour++;
															}
														}
														else
														{
															// $ot_prod_qty += $r['qty'][$prod_hour];
															$ot_prod_qty += $po_item_wise_hourly_prod_qty_arr[$com_key][$date_key][$flr_id][$li_sl][$l_id][$po_item_ex_arr[0]][$po_item_ex_arr[1]]['qty'][$prod_hour];
															if($r['qty'][$prod_hour]>0)
															{
																$ot_last_prod_hour=substr($start_hour_arr[$k],0,2);
																// $line_tot_hour++;
															}
														}
														$m++;
													}
													// $line_tot_min += $item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]]*$line_tot_hour;
													// echo $po_item_ex_arr[0]."=".$po_item_ex_arr[1]."=".$item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]]."*".$line_tot_hour."<br>";

													$item_smv .= ($item_smv=="") ? number_format($item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]],2) : "/".number_format($item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]],2);
													$tot_smv += $item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]];
													$flr_tot_smv += $item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]];
													$gr_tot_smv += $item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]];
													$item_smv_count++;
													$line_tot_smv += $item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]];

													$general_prod_min += $general_prod_qty*$item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]];
													// echo $general_prod_qty."*".$item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]]."<br>";
													// echo $po_item_ex_arr[0]."*".$po_item_ex_arr[1]."*".$item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]]."<br>";

													$ot_prod_min += $ot_prod_qty*$item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]];
													// echo $ot_prod_qty."*".$item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]]."<br>";

													// $po_chk_arr[$po_item_ex_arr[0].$po_item_ex_arr[1].$po_item_ex_arr[2]] = "AA";
													$po_chk_arr[$po_item_ex_arr[0].$po_item_ex_arr[1].$po_item_ex_arr[2]] = "AA";

													// $tot_cm += ($effi_per_arr[$job]) ? ($item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]]*$cpm_app_period_arr[date('m-Y',strtotime($costing_date_arr[$job]))])/$effi_per_arr[$job] : 0;
													// $tot_cm += ($effi_per_arr[$job]) ? ($item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]]*$cpm_app_period_arr[date('m-Y',strtotime($costing_date_arr[$job]))])/$effi_per_arr[$job] : 0;

													// echo $item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]]."*".$cpm_app_period_arr[date('m-Y',strtotime($costing_date_arr[$job]))]."/".$effi_per_arr[$job]."<br>";
													// $cm_counter++;
												}
												

												$produce_minit+=$po_item_wise_prod_qty_arr[$com_key][$flr_id][$l_id][$date_key][$po_item_ex_arr[0]][$po_item_ex_arr[1]]*$item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]];
												// echo $po_item_wise_prod_qty_arr[$com_key][$flr_id][$l_id][$date_key][$po_item_ex_arr[0]][$po_item_ex_arr[1]]."*".$gsd_data_array[$po_item_ex_arr[3]][$po_item_ex_arr[1]]."<br>";

												$booking_no .= $booking_no_arr[$po_item_ex_arr[0]].",";

												

												// echo $gen_last_prod_hour-$ot_last_prod_hour;die;
												// $ot_prod_min += $ot_prod_qty*$gsd_data_array[$po_item_ex_arr[3]][$po_item_ex_arr[1]]['smv'];
												// $general_prod_min += $general_prod_qty*$gsd_data_array[$po_item_ex_arr[3]][$po_item_ex_arr[1]]['smv'];
												$po_count++;
												// $tot_fob_val+=($general_prod_qty+$ot_prod_qty)*$unit_price;

												$gsd_efficiency .= ($gsd_efficiency=='') ? $gsd_data_array[$po_item_ex_arr[3]][$po_item_ex_arr[1]]['efficiency'] : ", ".$gsd_data_array[$po_item_ex_arr[3]][$po_item_ex_arr[1]]['efficiency'];
												if($job_itm_chk_arr[$l_id][$job][$po_item_ex_arr[1]]=="")
												{
													$job_wise_gsd_efficiency_arr[$l_id][$job]['effi'] += $gsd_data_array[$po_item_ex_arr[3]][$po_item_ex_arr[1]]['efficiency'];
													$job_wise_gsd_efficiency_arr[$l_id][$job]['itm_count']++;
													$job_itm_chk_arr[$l_id][$job][$po_item_ex_arr[1]] = $po_item_ex_arr[1];
												}
												
												// pre($job_wise_gsd_efficiency_arr);die;
												// calculate job/style wise line running hour ===================
												$m=1;
												for($k=$hour; $k<=$last_hour; $k++)
												{
													$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
													if($m<=9)
													{
														if($po_item_wise_hourly_prod_qty_arr[$com_key][$date_key][$flr_id][$li_sl][$l_id][$po_item_ex_arr[0]][$po_item_ex_arr[1]]['qty'][$prod_hour]>0)
														{
															$line_tot_hour++;
															$style_running_arr[$job][$prod_hour]++;
														}
													}
													else
													{
														if($po_item_wise_hourly_prod_qty_arr[$com_key][$date_key][$flr_id][$li_sl][$l_id][$po_item_ex_arr[0]][$po_item_ex_arr[1]]['qty'][$prod_hour]>0)
														{
															$line_tot_hour++;
															$style_running_arr[$job][$prod_hour]++;
														}
													}
													$m++;
													$style_prod_qty_arr[$l_id][$job] += $po_item_wise_hourly_prod_qty_arr[$com_key][$date_key][$flr_id][$li_sl][$l_id][$po_item_ex_arr[0]][$po_item_ex_arr[1]]['qty'][$prod_hour];
													$style_prod_min_arr[$l_id][$job] += $po_item_wise_hourly_prod_qty_arr[$com_key][$date_key][$flr_id][$li_sl][$l_id][$po_item_ex_arr[0]][$po_item_ex_arr[1]]['qty'][$prod_hour]*$item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]];
												}
												// $style_running_arr[$job] += $line_tot_hour;
												$style_smv_arr[$job] = $item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]];

											}
											// echo "<pre>";print_r($style_prod_qty_arr);
											// $avg_unit_price = $unit_price/$po_count;
											$job_tot_smv = 0;
											$job_tot_min = 0;
											$job_tot_prod_qty = 0;
											foreach ($style_smv_arr as $j_key => $job_smv) 
											{
												$running_hour = count($style_running_arr[$j_key]);
												// $job_tot_min += $running_hour * $style_smv_arr[$j_key];
												// $job_tot_smv += $style_smv_arr[$j_key];
												// echo $running_hour ."*". $style_smv_arr[$j_key]."<br>";
												$job_tot_prod_qty += $style_prod_qty_arr[$l_id][$j_key];
												$job_tot_min += $style_prod_min_arr[$l_id][$j_key];

											}
											$style_count = array_unique(array_filter(explode("**",$r['job_no'])));
											$avg_smv = 0;
											if(count($style_count)>1) // when multiple style prod in a line
											{
												// $avg_smv = number_format(($job_tot_min/$job_tot_smv),2);
												$avg_smv = number_format(($job_tot_min/$job_tot_prod_qty),2);
												// echo $line_tot_hour."/".$line_tot_smv."<br>";
											}
											else
											{
												$avg_smv = ($item_smv_count>0) ? number_format(($tot_smv/$item_smv_count),2) : '0.00';
											}
											$flr_tot_avg_smv += $avg_smv;
											$gr_tot_avg_smv += $avg_smv;

											$job_no = implode(",",array_unique(array_filter(explode("**",$r['job_no']))));
											$po_number = implode(", ",array_unique(array_filter(explode("**",$r['po_number']))));
											$style_ref_no = implode(", ",array_unique(array_filter(explode("**",$r['style_ref_no']))));
											$item_name = implode(", ",array_unique(array_filter(explode("**",$r['item_name']))));
											$booking_no = implode(", ",array_unique(array_filter(explode(",",$booking_no))));

											$company_id_arr = array_unique(array_filter(explode("**",$r['company_id'])));
											$company_name = "";
											foreach ($company_id_arr as $v)
											{
												$company_name .= ($company_name=="") ? $companyArr[$v]: ", ".$companyArr[$v];
											}
											$buyer_name_arr = array_unique(array_filter(explode("**",$r['buyer_name'])));
											$buyer_name = "";
											foreach ($buyer_name_arr as $v)
											{
												$buyer_name .= ($buyer_name=="") ? $buyerArr[$v]: ", ".$buyerArr[$v];
											}

											$current_hour = 0;

											$line_prod_hour_array = array();
											if(strtotime(date('d-M-Y')) != $date_key)
											{
												$working_hour = $prod_resource_array[$com_key][$l_id][$date_key]['working_hour']-1;
												// $difference_hour = $line_prod_hour_array[$l_id];
												for($k=$hour; $k<=$last_hour; $k++)
												{
													$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
													// echo $r['qty'][$prod_hour]."<br>";
													if($r['qty'][$prod_hour]>0)
													{
														$line_prod_hour_array[$l_id]++;
													}

												}
												$difference_hour = $line_prod_hour_array[$l_id];


												if($ot_last_prod_hour!="")
												{
													$current_hour = $ot_last_prod_hour - $hour;
												}
												else
												{
													if(substr($global_start_lanch,0,2) < substr(date('H:i:s'),0,2))
													{
														$current_hour = $prod_resource_array[$com_key][$l_id][$date_key]['working_hour'];//-1;
													}
													else
													{
														$current_hour = $prod_resource_array[$com_key][$l_id][$date_key]['working_hour'];
													}
												}
											}
											else // for current date
											{
												for($k=$hour; $k<=$last_hour; $k++)
												{
													$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
													// echo $r['qty'][$prod_hour]."<br>";
													if($r['qty'][$prod_hour]>0)
													{
														$line_prod_hour_array[$l_id]++;
													}

												}

												$difference_hour = $line_prod_hour_array[$l_id];
												if($ot_last_prod_hour!="")
												{
													$current_hour = $ot_last_prod_hour - $hour;
												}
												else
												{
													$current_hour = $cur_difference_hour;
												}

												// chk lunch hour
												if(substr($global_start_lanch,0,2) < substr(date('H:i:s'),0,2))
												{
													$working_hour = $prod_resource_array[$com_key][$l_id][$date_key]['working_hour']-1;
												}
												else
												{
													$working_hour = $prod_resource_array[$com_key][$l_id][$date_key]['working_hour'];
												}

											}

											// $available_min=($prod_resource_array[$com_key][$l_id][$date_key]['man_power'])*$working_hour*60;
											// $available_min=($prod_resource_array[$com_key][$l_id][$date_key]['man_power'])*$prod_resource_array[$com_key][$l_id][$date_key]['working_hour']*60;
											$available_min=$prod_resource_array[$com_key][$l_id][$date_key]['man_power']*($current_hour*60);
											// $man_min_used = $prod_resource_array[$com_key][$l_id][$date_key]['man_power']*($difference_hour*60);
											$total_adjustment = 0;
											$smv_adjustmet_type=$prod_resource_array[$com_key][$l_id][$date_key]['smv_adjust_type'];
											if(str_replace("'","",$smv_adjustmet_type)==1)
											{
												$total_adjustment=$prod_resource_array[$com_key][$l_id][$date_key]['smv_adjust'];
												//$total_adjustment_summary=$no_prod_line_arr[$f_id]['smv_adjust'];
											}
											if(str_replace("'","",$smv_adjustmet_type)==2)
											{
												$total_adjustment=($prod_resource_array[$com_key][$l_id][$date_key]['smv_adjust'])*(-1);
												//$total_adjustment_summary=($no_prod_line_arr[$f_id]['smv_adjust'])*(-1);
											}
											$man_min_used = $total_adjustment+$prod_resource_array[$com_key][$l_id][$date_key]['man_power']*($prod_resource_array[$com_key][$l_id][$date_key]['working_hour']*60);
											$ot_prod_hour = ($ot_last_prod_hour>0) ? $ot_last_prod_hour - $gen_last_prod_hour : 0;
											// $ot_prod_min = ($prod_resource_array[$com_key][$l_id][$date_key]['man_power'])*$ot_prod_hour*60;
											// $general_prod_min = ($prod_resource_array[$com_key][$l_id][$date_key]['man_power'])*8*60;
											// echo $working_hour;die;
											// $general_prod_min = $general_prod_qty*$avg_smv;
											// $ot_prod_min = $ot_prod_qty*$avg_smv;
											$tot_produce_minit = $general_prod_min+$ot_prod_min;

											// ==================================
											$m=1;
											$general_prod_qty = 0;
											$ot_prod_qty = 0;
											$tot_prod_qty = 0;
											$line_prod_hour = 0;
											$total_eff_hour = 0;

											for($k=$hour; $k<=$last_hour; $k++)
											{
												$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
												if($m<=9)
												{
													$general_prod_qty += $r['qty'][$prod_hour];
													// echo "string<br>";
												}
												else
												{
													$ot_prod_qty += $r['qty'][$prod_hour];
												}
												$tot_prod_qty += $r['qty'][$prod_hour];
												$line_prod_hour++;
												$m++;

												if($r['qty'][$prod_hour]>0)
												{
													$total_eff_hour++;
												}
											}
											$eff_target2=($prod_resource_array[$com_key][$l_id][$date_key]['terget_hour']*$total_eff_hour);
											$target_hit = ($prod_resource_array[$com_key][$l_id][$date_key]['terget_hour']*$current_hour>0) ? $tot_prod_qty/($prod_resource_array[$com_key][$l_id][$date_key]['terget_hour']*$current_hour)*100 : 0;
											// echo "$l_id==$tot_prod_qty/(".$prod_resource_array[$com_key][$l_id][$date_key]['terget_hour']."*".$current_hour.")*100<br>";
											$total_varience = $prod_resource_array[$com_key][$l_id][$date_key]['tpd'] - $tot_prod_qty;

											// $efficiency_min=($prod_resource_array[$com_key][$l_id][$date_key]['man_power'])*$current_hour*60;
											

											$efficiency_min=$prod_resource_array[$com_key][$l_id][$date_key]['man_power']*($difference_hour*60);
											$efficiency_min2=($prod_resource_array[$com_key][$l_id][$date_key]['man_power'])*$current_hour*60;
											// echo "(".$prod_resource_array[$com_key][$l_id][$date_key]['man_power'].")*".$current_hour."*60<br>";
											$eff_target=($prod_resource_array[$com_key][$l_id][$date_key]['terget_hour']*$difference_hour);
											$act_target_effi = 	$prod_resource_array[$com_key][$l_id][$date_key]['target_effi'];
											$target_min = $eff_target * $avg_smv;
											$target_effi = ($efficiency_min>0) ? ($target_min / $efficiency_min)*100 : 0;
											$achive_effi = ($efficiency_min>0) ? ($tot_produce_minit / $man_min_used)*100 : 0;
											// $line_efficiency=($efficiency_min>0) ? (($produce_minit)*100)/$efficiency_min : 0;
											$line_efficiency=($efficiency_min2>0) ? (($tot_produce_minit)*100)/$efficiency_min2 : 0;
											$efficiency_gap = $gsd_efficiency - $line_efficiency;
											// echo $tot_produce_minit."/".$man_min_used."<br>";


											$job_no_arr = array_unique(array_filter(explode("**",$r['job_no'])));
											$active_days = "";
											$style_change = 0;
											$tot_cm = 0;
											$prod_cm_pcs2 = 0;
											$prod_cm_pcs = 0;
											$job_tot_prod = 0;
											$cm_counter = 0;
											$dzn_qnty = 0;
											$tot_amount = 0;
											$tot_qty = 0;
											$dzn_qnty = 0;
											$tot_cpm = 0;
											$exchane_rate = 0;
											$k=0;
											$ttl_cm_earn=0;
											$effi = 0;
											$itm_count = 0;
											$prod_smv = 0;
											$total_prod_qty = 0;
											$job_wise_prod_cm = array();
											$prod_cm_pcs_title="";
											foreach ($job_no_arr as $j_key => $job)
											{
												$active_days .= ($active_days=="") ? $active_days_arr[$l_id][$job] : "/".$active_days_arr[$l_id][$job];
												$flr_tot_act_days += $active_days_arr[$l_id][$job];
												$gr_tot_act_days += $active_days_arr[$l_id][$job];
												if($k>0)
												{
													$style_change++;
												}
												$k++;

												// ======================================
												$costing_per=$costing_per_arr[$job];
												if($costing_per==1) $dzn_qnty=12;
												else if($costing_per==3) $dzn_qnty=12*2;
												else if($costing_per==4) $dzn_qnty=12*3;
												else if($costing_per==5) $dzn_qnty=12*4;
												else $dzn_qnty=1;
												$tot_cm += ($cm_arr[$job]/$dzn_qnty)*$style_prod_qty_arr[$l_id][$job];
												// echo $cm_arr[$job]."/".$dzn_qnty."<br>";
												$job_tot_prod += $style_prod_qty_arr[$l_id][$job];
												$ttl_cm_earn += (($cm_arr[$job]/$dzn_qnty)*$style_prod_qty_arr[$l_id][$job])*$exchange_rate_arr[$job];
												$cm_counter++;


												$tot_amount += $fob_price_arr[$job]*$job_wise_prod_qty_arr[$com_key][$flr_id][$l_id][$date_key][$job];
												$tot_qty += $job_wise_prod_qty_arr[$com_key][$flr_id][$l_id][$date_key][$job];
												// echo $fob_price_arr[$job]."/".$job_wise_prod_qty_arr[$com_key][$flr_id][$l_id][$date_key][$job]."<br>";

												$tot_cpm += $cpm_app_period_arr[date('m-Y',strtotime($costing_date_arr[$job]))];
												$exchane_rate += $exchange_rate_arr[$job];

												$cpm = $cpm_app_period_arr[date('m-Y',strtotime($costing_date_arr[$job]))];
												$itm_count = $job_wise_gsd_efficiency_arr[$l_id][$job]['itm_count'];
												$avg_effi = 0;
												if($job_wise_gsd_efficiency_arr[$l_id][$job]['effi']!="")
												{
													$avg_effi = $job_wise_gsd_efficiency_arr[$l_id][$job]['effi'] / $itm_count;
												}
												// echo $job_wise_gsd_efficiency_arr[$l_id][$job]['effi'] ."/". $itm_count."<br>";die;
												// pre($job_wise_gsd_efficiency_arr); die;
												$total_prod_qty += $style_prod_qty_arr[$l_id][$job];
												$job_tot_prod_qnty = $style_prod_qty_arr[$l_id][$job];
												$job_tot_mins = $style_prod_min_arr[$l_id][$job];
												$prod_smv = $job_tot_mins / $job_tot_prod_qnty;


												$ex_rate = $exchange_rate_arr[$job];
												$prod_cm_pcs2 = (((($cpm/$avg_effi)*100)*$prod_smv*$dzn_qnty)/$ex_rate)/$dzn_qnty;
												$prod_cm_pcs += fn_num_frmt($prod_cm_pcs2);
												$prod_cm_pcs_title .= "(((($cpm/$avg_effi)*100)*$prod_smv*$dzn_qnty)/$ex_rate)/$dzn_qnty+";
												// echo $job."=".$l_id."=((".$cpm."/".$avg_effi.")*".$prod_smv."*".$dzn_qnty.")/".$exchange_rate_arr[$job]."<br>";
												// $job_wise_prod_cm[$job] = (((($cpm/$avg_effi)*100)*$prod_smv*$dzn_qnty)/$exchange_rate_arr[$job])/$dzn_qnty;
												$job_wise_prod_cm[$job] = fn_num_frmt($prod_cm_pcs2);
											}
											// =========== when run multi job in a line
											$tot_prod_cms = 0;
											$tot_prod_qtys = 0;
											// pre($style_prod_qty_arr); die;
											if(count($style_prod_qty_arr[$l_id])>1)
											{
												$prod_cm_pcs_title = "";
												foreach ($style_prod_qty_arr[$l_id] as $jkey => $v) 
												{
													$tot_prod_cms += $job_wise_prod_cm[$jkey]*$v; 
													$tot_prod_qtys += $v;
													// echo $job_wise_prod_cm[$jkey]."*".$v."<br>";
												}
												$prod_cm_pcs = $tot_prod_cms/$tot_prod_qtys;
												$prod_cm_pcs_title = "tot prod cms ($tot_prod_cms)/tot prod qtys($tot_prod_qtys)";
												// echo $tot_prod_cm."/".$tot_prod_qty."<br>";
											}
											$total_prod_cm = $prod_cm_pcs*$total_prod_qty;
											$total_prod_cm_title = "$prod_cm_pcs*$total_prod_qty";
											$avg_cpm = ($cm_counter) ? $tot_cpm/$cm_counter : 0;
											$avg_exchane_rate = ($cm_counter) ? $exchane_rate/$cm_counter : 0;
											$avg_unit_price = ($tot_qty) ?  $tot_amount/$tot_qty : 0;
											$tot_fob_val = ($general_prod_qty+$ot_prod_qty)*$avg_unit_price;
											$avg_cm = ($job_tot_prod) ? $tot_cm/$job_tot_prod : 0;
											// echo $tot_cm."/".$job_tot_prod."<br>";
											$ttl_cm = $tot_prod_qty*$avg_cm;
											// $ttl_cm = $tot_cpm*$man_min_used;
											$target_cm = $prod_resource_array[$com_key][$l_id][$date_key]['tpd']*$avg_cm;
											$target_fob_val = $prod_resource_array[$com_key][$l_id][$date_key]['tpd']*$avg_unit_price;


											$current_targrt = $current_hour*$prod_resource_array[$com_key][$l_id][$date_key]['terget_hour'];
											$varience_as_on_cur_hr = ($prod_resource_array[$com_key][$l_id][$date_key]['terget_hour']*$current_hour) - $tot_prod_qty;//$r['qty']['prod_hour'.$difference_hour];

											// $line_cost_bdt = ($line_efficiency>0) ? ($tot_cpm/$line_efficiency)*$efficiency_min2 : 0;
											$line_cost_bdt = $tot_cpm*$available_min;
											// echo $available_min."*".$avg_cpm."*".$avg_exchane_rate."<br>";
											$cm_rate = 0;
											foreach ($job_no_arr as $j_key => $job)
											{
												$costing_per=$costing_per_arr[$job];
												if($costing_per==1) $dzn_qnty=12;
												else if($costing_per==3) $dzn_qnty=12*2;
												else if($costing_per==4) $dzn_qnty=12*3;
												else if($costing_per==5) $dzn_qnty=12*4;
												else $dzn_qnty=1;
												
												$cm_rate += ($cm_arr[$job]/$dzn_qnty)*$exchange_rate_arr[$job];
											}
											$bep_units = ($cm_rate) ? $line_cost_bdt/$cm_rate : 0;
											$earn_val_usd = ($tot_qty*$avg_unit_price*15)/100;
											$earn_val_bdt = $earn_val_usd*$avg_exchane_rate;
											// echo $earn_val_bdt."*".$avg_exchane_rate."<br>";
											$line_profit = $ttl_cm_earn - $line_cost_bdt;

											$npt_min = $npt_min_array[$date_key][$l_id];

											if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
											<tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
												<td width="30"><?=$i;?></td>
												<td width="60"><p><?=($date_key!="") ? date("d-m-Y",$date_key) : "";?></p></td>
												<td width="120"><p><?=$companyArr[$com_key];?></p></td>
												<td width="120"><p><?=$floorArr[$flr_id];?></p></td>
												<td width="80"><p><?=$sewing_line;?></p></td>
												<td width="120"><p><?=$company_name;?></p></td>
												<td width="120"><p><?=$buyer_name;?></p></td>
												<td width="120"><p><?=$job_no;?></p></td>
												<td width="120"><p><?=$booking_no;?></p></td>
												<td width="120"><p><?=$po_number;?></p></td>
												<td width="120"><p><?=$style_ref_no;?></p></td>
												<td width="120"><p><?=$item_name;?></p></td>
												<td width="80"><p><?=$item_smv;?></p></td>
												<td width="80"><p><?=$avg_smv;?></p></td>
												<td align="right" width="80"><p><?=$prod_resource_array[$com_key][$l_id][$date_key]['operator'];?></p></td>
												<td align="right" width="80"><p><?=$prod_resource_array[$com_key][$l_id][$date_key]['helper'];?></p></td>
												<td align="right" width="80"><p><?=$prod_resource_array[$com_key][$l_id][$date_key]['man_power'];?></p></td>
												<td align="right" width="80"><p>&nbsp;<?=$active_days;?></p></td>
												<td align="right" width="80"><p><?=$prod_resource_array[$com_key][$l_id][$date_key]['capacity'];?></p></td>
												<td align="right" width="80"><p><?=$prod_resource_array[$com_key][$l_id][$date_key]['working_hour'];?></p></td>
												<td align="right" width="80"><p><?=number_format($difference_hour,0);?></p></td>
												<td align="right" width="80"><p><?=$prod_resource_array[$com_key][$l_id][$date_key]['terget_hour'];?></p></td>
												<td align="right" width="80"><p><?=number_format($current_hour,1);?></p></td>
												<td align="right" width="80"><p><?=$current_targrt;?></p></td>
												<td align="right" width="80"><p><?=number_format($varience_as_on_cur_hr,0);?></p></td>
												<td align="right" width="80"><p><?=$prod_resource_array[$com_key][$l_id][$date_key]['tpd'];?></p></td>
												<td align="right" width="80"><p><?=number_format($general_prod_qty,0);?></p></td>
												<td align="right" width="80"><p><?=number_format($ot_prod_qty,0);?></p></td>
												<td align="right" width="80"><p><?=number_format($tot_prod_qty,0);?></p></td>
												<td align="right" width="80"><p><?=number_format($total_varience,0);?></p></td>
												<td align="right" width="80"><p><?=number_format($available_min,0);?></p></td>
												<td align="right" width="80"><p><?=number_format($man_min_used,0);?></p></td>
												<td align="right" width="80"><p><?=number_format($npt_min,0);?></p></td>
												<td align="right" width="80"><p><?=number_format($general_prod_min,0);?></p></td>
												<td align="right" width="80"><p><?=number_format($ot_prod_min,0);?></p></td>
												<td align="right" width="80"><p><?=number_format($tot_produce_minit,0);?></p></td>
												<td align="right" width="80"><p><?=number_format($target_hit,2);?></p></td>
												<td align="right" width="80"><p><?=number_format($gsd_efficiency,2);?></p></td>
												<td align="right" width="80"><p><?=number_format($line_efficiency,2);?></p></td>
												<td align="right" width="80"><p><?=number_format($efficiency_gap,2);?></p></td>
												<td align="right" width="80"><p><?=number_format($achive_effi,2);?></p></td>
												<td align="right" width="80"><p><?=$style_change;?></p></td>
												<td align="right" width="80">
													<a href="javascript:void(0)" onclick="open_avg_cm_popup('<?=$date_key;?>','<?=$l_id;?>','<?=$job_no;?>','open_avg_cm_popup')">
														<?=number_format($avg_cm,2);?>
													</a>
												</td>
												<td align="right" width="80"><p><?=number_format($ttl_cm,2);?></p></td>
												<td align="right" width="80" title="<?= trim($prod_cm_pcs_title,'+')  ?>"><p><?=number_format($prod_cm_pcs,2);?></p></td>
												<td align="right" width="80" title="<?= $total_prod_cm_title ?>"><p><?=number_format($total_prod_cm,2);?></p></td>
												<td align="right" width="80"><p><?=number_format($ttl_cm_earn,2);?></p></td>
												<td align="right" width="80"><p><?=number_format($target_cm,2);?></p></td>
												<!-- <td align="right" width="80"><p><?=number_format($avg_unit_price,2);?></p></td>
												<td align="right" width="80"><p><?=number_format($tot_fob_val,2);?></p></td>
												<td align="right" width="80"><p><?=number_format($target_fob_val,2);?></p></td> -->
												<td align="right" width="80"><p><?=number_format($line_cost_bdt,2);?></p></td>
												<td align="right" width="80"><p><?=number_format($bep_units,2);?></p></td>
												<!-- <td align="right" width="80"><p><?=number_format($earn_val_usd,2);?></p></td>
												<td align="right" width="80"><p><?=number_format($earn_val_bdt,2);?></p></td> -->
												<td align="right" width="80"><p><?=number_format($line_profit,2);?></p></td>
												<?
												$line_tot = 0;
												for($k=$hour; $k<=$last_hour; $k++)
												{
													$rowspan = 0;
													if($k==$lunch_start_time_arr[$com_key]) // lunch hour
													{
														if($flCount==0)
														{
															?>
															<td title="Lunch Hour" rowspan="<?=$rowspan_arr[$com_key][$date_key][$flr_id];?>" width="50" style="background-color:#8B7E74;"></td>
															<!-- <td valign="middle" width="50" style="background:red;"></td> -->
															<?
															$flCount++;
														}
													}
													else
													{
														$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
														$color="";
														if($r['qty'][$prod_hour] == $prod_resource_array[$com_key][$l_id][$date_key]['terget_hour'])
														{
															$color="green";
														}
														elseif ($r['qty'][$prod_hour] > $prod_resource_array[$com_key][$l_id][$date_key]['terget_hour'])
														{
															$color="#2037df";
														}
														elseif ($r['qty'][$prod_hour] < $prod_resource_array[$com_key][$l_id][$date_key]['terget_hour'] && $r['qty'][$prod_hour] > 0)
														{
															$color="#f50a10";
														}
														?>
														<td bgcolor="<?=$color;?>" valign="middle" align="right" width="50"><?=number_format($r['qty'][$prod_hour],0);?></td>
														<?
														$line_tot += $r['qty'][$prod_hour];
														$floor_tot_array[$com_key][$flr_id][$prod_hour] += $r['qty'][$prod_hour];
														$gr_tot_array[$com_key][$prod_hour] += $r['qty'][$prod_hour];
													}
												}
												?>
											</tr>
											<?
											$i++;
											$flr_tot_operator += $prod_resource_array[$com_key][$l_id][$date_key]['operator'];
											$flr_tot_helper += $prod_resource_array[$com_key][$l_id][$date_key]['helper'];
											$flr_tot_man_power +=$prod_resource_array[$com_key][$l_id][$date_key]['man_power'];
											// $flr_tot_act_days += $active_days;
											$flr_tot_cap_pcs += $prod_resource_array[$com_key][$l_id][$date_key]['capacity'];
											$flr_tot_plan_wo_hour += $prod_resource_array[$com_key][$l_id][$date_key]['working_hour'];
											$flr_tot_prod_hour += $difference_hour;
											$flr_tot_hour_trg_pcs += $prod_resource_array[$com_key][$l_id][$date_key]['terget_hour'];
											$flr_tot_cur_hour += $current_hour;
											$flr_tot_as_on_cur_hour_trg_pcs += $current_targrt;
											$flr_tot_hour_prod_varience += $varience_as_on_cur_hr;
											$flr_tot_target += $prod_resource_array[$com_key][$l_id][$date_key]['tpd'];
											$flr_tot_gen_prod += $general_prod_qty;
											$flr_tot_ot_prod += $ot_prod_qty;
											$flr_tot_prod += $tot_prod_qty;
											$flr_tot_varience += $total_varience;
											$flr_tot_avl_min += $available_min;
											$flr_tot_man_min_used += $man_min_used;
											$flr_tot_npt += $npt_min;
											$flr_tot_gen_prod_min += $general_prod_min;
											$flr_tot_ot_prod_min += $ot_prod_min;
											$flr_tot_prod_min += $tot_produce_minit;
											$flr_tot_target_hit += $target_hit;
											// $flr_tot_target_effi += $act_target_effi;
											$flr_tot_target_effi += $gsd_efficiency;
											$flr_tot_achv_effi += $achive_effi;
											$flr_tot_effi_gap += $efficiency_gap;
											$flr_tot_line_effi += $line_efficiency;
											$flr_tot_style_cng += $style_change;
											$flr_tot_cm_pcs += $avg_cm;
											$flr_tot_cm += $ttl_cm;
											$flr_tot_prod_cm_pcs += $prod_cm_pcs;
											$flr_tot_prod_cm += $total_prod_cm;
											$flr_tot_cm_earn += $ttl_cm_earn; 
											$flr_tot_target_cm += $target_cm;
											$flr_tot_fob_val += $tot_fob_val;
											$flr_tot_target_fob_val += $target_fob_val;
											$flr_tot_bep_unit += $bep_units;
											$flr_tot_earn_val_fob_usd += $earn_val_usd;
											$flr_tot_earn_val_fob_bdt += $earn_val_bdt;
											$flr_tot_line_cost += $line_cost_bdt;
											$flr_tot_line_profit += $line_profit;


											$gr_tot_operator += $prod_resource_array[$com_key][$l_id][$date_key]['operator'];
											$gr_tot_helper += $prod_resource_array[$com_key][$l_id][$date_key]['helper'];
											$gr_tot_man_power +=$prod_resource_array[$com_key][$l_id][$date_key]['man_power'];
											// $gr_tot_act_days += $active_days;
											$gr_tot_cap_pcs += $prod_resource_array[$com_key][$l_id][$date_key]['capacity'];
											$gr_tot_plan_wo_hour += $prod_resource_array[$com_key][$l_id][$date_key]['working_hour'];
											$gr_tot_prod_hour += $difference_hour;
											$gr_tot_hour_trg_pcs += $prod_resource_array[$com_key][$l_id][$date_key]['terget_hour'];
											$gr_tot_cur_hour += $current_hour;
											$gr_tot_as_on_cur_hour_trg_pcs += $current_targrt;
											$gr_tot_hour_prod_varience += $varience_as_on_cur_hr;
											$gr_tot_target += $prod_resource_array[$com_key][$l_id][$date_key]['tpd'];
											$gr_tot_gen_prod += $general_prod_qty;
											$gr_tot_ot_prod += $ot_prod_qty;
											$gr_tot_prod += $tot_prod_qty;
											$gr_tot_varience += $total_varience;
											$gr_tot_avl_min += $available_min;
											$gr_tot_man_min_used += $man_min_used;
											$gr_tot_npt += $npt_min;
											$gr_tot_gen_prod_min += $general_prod_min;
											$gr_tot_ot_prod_min += $ot_prod_min;
											$gr_tot_prod_min += $tot_produce_minit;
											$gr_tot_target_hit += $target_hit;
											// $gr_tot_target_effi += $act_target_effi;
											$gr_tot_target_effi += $gsd_efficiency;
											$gr_tot_achv_effi += $achive_effi;
											$gr_tot_effi_gap += $efficiency_gap;
											$gr_tot_line_effi += $line_efficiency;
											$gr_tot_style_cng += $style_change;
											$gr_tot_cm_pcs += $avg_cm;
											$gr_tot_cm += $ttl_cm;
											$gr_tot_prod_cm_pcs += $prod_cm_pcs;
											$gr_tot_prod_cm += $total_prod_cm;
											$gr_tot_target_cm += $target_cm;
											$gr_tot_cm_earn += $ttl_cm_earn;
											$gr_tot_fob_val += $tot_fob_val;
											$gr_tot_target_fob_val += $target_fob_val;
											$gr_tot_bep_unit += $bep_units;
											$gr_tot_earn_val_fob_usd += $earn_val_usd;
											$gr_tot_earn_val_fob_bdt += $earn_val_bdt;
											$gr_tot_line_cost += $line_cost_bdt;
											$gr_tot_line_profit += $line_profit;
										}
									}

									?>
									<tr style="text-align: right;font-weight:bold;background:#dccddc;">
										<td width="30"></td>
										<td width="60"></td>
										<td width="120"></td>
										<td width="120"></td>
										<td width="80"></td>
										<td width="120"></td>
										<td width="120"></td>
										<td width="120"></td>
										<td width="120"></td>
										<td width="120"></td>
										<td width="120"></td>
										<td width="120">Floor Wise Total</td>
										<td width="80"><?=number_format(($flr_tot_smv/$floor_wise_tot_line[$com_key][$date_key][$flr_id]),2);?></td>
										<td width="80"><?=number_format(($flr_tot_avg_smv/$floor_wise_tot_line[$com_key][$date_key][$flr_id]),2);?></td>
										<td width="80"><?=number_format($flr_tot_operator,0);?></td>
										<td width="80"><?=number_format($flr_tot_helper,0);?></td>
										<td width="80"><?=number_format($flr_tot_man_power,0);?></td>
										<td width="80"><?=number_format($flr_tot_act_days,0);?></td>
										<td width="80"><?=number_format($flr_tot_cap_pcs,0);?></td>
										<td width="80"><?=number_format($flr_tot_plan_wo_hour,0);?></td>
										<td width="80"><?=number_format($flr_tot_prod_hour,0);?></td>
										<td width="80"><?=number_format($flr_tot_hour_trg_pcs,0);?></td>
										<td width="80"><?=number_format($flr_tot_cur_hour,2);?></td>
										<td width="80"><?=number_format($flr_tot_as_on_cur_hour_trg_pcs,0);?></td>
										<td width="80"><?=number_format($flr_tot_hour_prod_varience,0);?></td>
										<td width="80"><?=number_format($flr_tot_target,0);?></td>
										<td width="80"><?=number_format($flr_tot_gen_prod,0);?></td>
										<td width="80"><?=number_format($flr_tot_ot_prod,0);?></td>
										<td width="80"><?=number_format($flr_tot_prod,0);?></td>
										<td width="80"><?=number_format($flr_tot_varience,0);?></td>
										<td width="80"><?=number_format($flr_tot_avl_min,0);?></td>
										<td width="80"><?=number_format($flr_tot_man_min_used,0);?></td>
										<td width="80"><?=number_format($flr_tot_npt,0);?></td>
										<td width="80"><?=number_format($flr_tot_gen_prod_min,0);?></td>
										<td width="80"><?=number_format($flr_tot_ot_prod_min,0);?></td>
										<td width="80"><?=number_format($flr_tot_prod_min,0);?></td>
										<td width="80"><?=number_format(($flr_tot_target_hit/$floor_wise_tot_line[$com_key][$date_key][$flr_id]),2);?></td>
										<td width="80"><?=number_format(($flr_tot_target_effi/$floor_wise_tot_line[$com_key][$date_key][$flr_id]),2);?></td>
										<td width="80"><?=number_format((($flr_tot_prod_min/$flr_tot_avl_min)*100),2);?></td>
										<td width="80"><?=number_format(($flr_tot_effi_gap/$floor_wise_tot_line[$com_key][$date_key][$flr_id]),2);?></td>
										<td width="80"><?=number_format(($flr_tot_achv_effi/$floor_wise_tot_line[$com_key][$date_key][$flr_id]),2);?></td>
										<td width="80"><?=number_format($flr_tot_style_cng,0);?></td>
										<td width="80"><?=number_format($flr_tot_cm_pcs,2);?></td>
										<td width="80"><?=number_format($flr_tot_cm,2);?></td>
										<td width="80"><?=number_format($flr_tot_prod_cm_pcs,2);?></td>
										<td width="80"><?=number_format($flr_tot_prod_cm,2);?></td>
										<td width="80"><?=number_format($flr_tot_cm_earn,2);?></td>
										<td width="80"><?=number_format($flr_tot_target_cm,2);?></td>
										<!-- <td width="80"></td>
										<td width="80"><?=number_format($flr_tot_fob_val,2);?></td>
										<td width="80"><?=number_format($flr_tot_target_fob_val,2);?></td> -->
										<td width="80"><?=number_format($flr_tot_line_cost,2);?></td>
										<td width="80"><?=number_format($flr_tot_bep_unit,2);?></td>
										<!-- <td width="80"><?=number_format($flr_tot_earn_val_fob_usd,2);?></td>
										<td width="80"><?=number_format($flr_tot_earn_val_fob_bdt,2);?></td> -->
										<td width="80"><?=number_format($flr_tot_line_profit,2);?></td>
										<?
										for($k=$hour; $k<=$last_hour; $k++)
										{
											$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
											?>
											<td width="50" ><?=$floor_tot_array[$com_key][$flr_id][$prod_hour];?></td>
											<?
										}
										?>
									</tr>
									<?
								}
							}
							?>
						</tbody>
					</table>
				</div>
				<table class="rpt_table" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<tfoot>
						<tr>
							<th width="30"></th>
							<th width="60"></th>
							<th width="120"></th>
							<th width="120"></th>
							<th width="80"></th>
							<th width="120"></th>
							<th width="120"></th>
							<th width="120"></th>
							<th width="120"></th>
							<th width="120"></th>
							<th width="120"></th>
							<th width="120">Grand Total</th>
							<th width="80"><?=number_format(($gr_tot_smv/$gr_tot_line),2);?></th>
							<th width="80"><?=number_format(($gr_tot_avg_smv/$gr_tot_line),2);?></th>
							<th width="80"><?=number_format($gr_tot_operator,0);?></th>
							<th width="80"><?=number_format($gr_tot_helper,0);?></th>
							<th width="80"><?=number_format($gr_tot_man_power,0);?></th>
							<th width="80"><?=number_format($gr_tot_act_days,0);?></th>
							<th width="80"><?=number_format($gr_tot_cap_pcs,0);?></th>
							<th width="80"><?=number_format($gr_tot_plan_wo_hour,0);?></th>
							<th width="80"><?=number_format($gr_tot_prod_hour,0);?></th>
							<th width="80"><?=number_format($gr_tot_hour_trg_pcs,0);?></th>
							<th width="80"><?=number_format($gr_tot_cur_hour,2);?></th>
							<th width="80"><?=number_format($gr_tot_as_on_cur_hour_trg_pcs,0);?></th>
							<th width="80"><?=number_format($gr_tot_hour_prod_varience,0);?></th>
							<th width="80"><?=number_format($gr_tot_target,0);?></th>
							<th width="80"><?=number_format($gr_tot_gen_prod,0);?></th>
							<th width="80"><?=number_format($gr_tot_ot_prod,0);?></th>
							<th width="80"><?=number_format($gr_tot_prod,0);?></th>
							<th width="80"><?=number_format($gr_tot_varience,0);?></th>
							<th width="80"><?=number_format($gr_tot_avl_min,0);?></th>
							<th width="80"><?=number_format($gr_tot_man_min_used,0);?></th>
							<th width="80"><?=number_format($gr_tot_npt,0);?></th>
							<th width="80"><?=number_format($gr_tot_gen_prod_min,0);?></th>
							<th width="80"><?=number_format($gr_tot_ot_prod_min,0);?></th>
							<th width="80"><?=number_format($gr_tot_prod_min,0);?></th>
							<th width="80"><?=number_format(($gr_tot_target_hit/$gr_tot_line),2);?></th>
							<th width="80"><?=number_format(($gr_tot_target_effi/$gr_tot_line),2);?></th>
							<th width="80"><?=number_format((($gr_tot_prod_min/$gr_tot_avl_min)*100),2);?></th>
							<th width="80"><?=number_format(($gr_tot_effi_gap/$gr_tot_line),2);?></th>
							<th width="80"><?=number_format(($gr_tot_achv_effi/$gr_tot_line),2);?></th>
							<th width="80"><?=number_format($gr_tot_style_cng,0);?></th>
							<th width="80"><?=number_format($gr_tot_cm_pcs,2);?></th>
							<th width="80"><?=number_format($gr_tot_cm,2);?></th>
							<th width="80"><?=number_format($gr_tot_prod_cm_pcs,2);?></th>
							<th width="80"><?=number_format($gr_tot_prod_cm,2);?></th>
							<th width="80"><?=number_format($gr_tot_cm_earn,2);?></th>
							<th width="80"><?=number_format($gr_tot_target_cm,2);?></th>
							<!-- <th width="80"></th>
							<th width="80"><?=number_format($gr_tot_fob_val,2);?></th>
							<th width="80"><?=number_format($gr_tot_target_fob_val,2);?></th> -->
							<th width="80"><?=number_format($gr_tot_line_cost,2);?></th>
							<th width="80"><?=number_format($gr_tot_bep_unit,2);?></th>
							<!-- <th width="80"><?=number_format($gr_tot_earn_val_fob_usd,2);?></th>
							<th width="80"><?=number_format($gr_tot_earn_val_fob_bdt,2);?></th> -->
							<th width="80"><?=number_format($gr_tot_line_profit,2);?></th>
							<?
							for($k=$hour; $k<=$last_hour; $k++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
								?>
								<th width="50" ><?=$gr_tot_array[$com_key][$prod_hour];?></th>
								<?
							}
							?>
						</tr>
					</tfoot>
				</table>

			</fieldset>
			<br clear="all">
			<?
		} //end loop
		
	}
	else // order wise button
	{
		ob_start();
		$companyArr = return_library_array("select id,company_name from lib_company","id","company_name");
		$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name");
		$locationArr = return_library_array("select id,location_name from lib_location","id","location_name");
		$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
		$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
		$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
		$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
		order by sewing_line_serial");
		$job_id = str_replace("'","",$hidden_job_id);
		$hidden_order_id = str_replace("'","",$hidden_order_id);
		foreach($lineDataArr as $lRow)
		{
			$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
			$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
			$lastSlNo=$lRow[csf('sewing_line_serial')];
		}
		$company_id=str_replace("'","",$cbo_company_id);
		$rptType=str_replace("'","",$rptType);

		//echo $prod_reso_allo."eee";die;
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
		}

		/*===================================================================================== /
		/									chk	shift time 										/
		/===================================================================================== */
		if($db_type==0)
		{
			$min_shif_start=return_field_value("min(TIME_FORMAT(d.prod_start_time, '%H:%i' ))  as line_start_time","prod_resource_mst a,prod_resource_dtls  b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id in($company_id) and shift_id=1 and pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
		}
		else
		{
			$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id in($company_id) and shift_id=1 and pr_date between $txt_date_from and $txt_date_to and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
		}

		if($min_shif_start=="")
		{
			echo "<p style='font-size:20px', align='center'>No Line Engage for the selected Date.Please Check Actual Production Resource Entry.<p/>";
			disconnect($con);
			die;

		}
		$wo_com_arr = explode(",",$company_id);
		foreach ($wo_com_arr as $com_id)
		{
			$com_key = $com_id;
			/*===================================================================================== /
			/									get	shift time 										/
			/===================================================================================== */

			$start_time_arr=array();
			if($db_type==0)
			{
				$start_time_data_arr=sql_select("select company_name, shift_id, TIME_FORMAT( prod_start_time, '%H:%i' ) as prod_start_time,TIME_FORMAT( lunch_start_time, '%H:%i' ) as lunch_start_time from variable_settings_production where company_name in($com_id) and shift_id=1  and variable_list=26 and status_active=1 and is_deleted=0");
			}
			else
			{
				$start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time,TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($com_id) and  shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
			}
			$lunch_start_time_arr = array();
			foreach($start_time_data_arr as $row)
			{
				$start_time_arr[$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
				$start_time_arr[$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
				$exp = explode(":",$row[csf('lunch_start_time')]);
				$lunch_start_time_arr[$row[csf('company_name')]] = $exp[0]*1;
			}
			$prod_start_hour=$start_time_arr[1]['pst'];
			$global_start_lanch=$start_time_arr[1]['lst'];
			if($prod_start_hour=="") $prod_start_hour="08:00";
			$start_time=explode(":",$prod_start_hour);
			$hour=$start_time[0]*1;
			$minutes=$start_time[1];
			$last_hour=23;
			$lineWiseProd_arr=array();
			$prod_arr=array();
			$start_hour_arr=array();
			$start_hour=$prod_start_hour;
			$start_hour_arr[$hour]=$start_hour;
			for($j=$hour;$j<$last_hour;$j++)
			{
				$start_hour=add_time($start_hour,60);
				$start_hour_arr[$j+1]=substr($start_hour,0,5);
			}
			//echo $pc_date_time;die;
			$start_hour_arr[$j+1]='23:59';
			if($prod_start_hour>$min_shif_start)  $prod_start_hour=$min_shif_start;
			$actual_date=date("Y-m-d");
			$actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$txt_date_from)));
			$actual_time=substr(date("Y-m-d H:i:s",strtotime($pc_date_time)),11,2);
			$acturl_hour_minute=date("H:i",strtotime($pc_date_time));
			$generated_hourarr=array();
			$first_hour_time=explode(":",$min_shif_start);
			$hour_line=$first_hour_time[0]*1;
			$minutes_one=$start_time[1];
			$line_start_hour_arr[$hour_line]=$min_shif_start;

			for($l=$hour_line;$l<$last_hour;$l++)
			{
				$min_shif_start=add_time($min_shif_start,60);
				$line_start_hour_arr[$l+1]=substr($min_shif_start,0,5);
			}

			$line_start_hour_arr[$j+1]='23:59';
			// print_r($start_hour_arr);die;
			/*===================================================================================== /
			/										query condition									/
			/===================================================================================== */
			if(str_replace("'","",$com_id)=="") $company_name=""; else $company_name="and a.serving_company in(".str_replace("'","",$com_id).")";
			if(str_replace("'","",$cbo_location_id)=="") $location=""; else $location="and a.location in(".str_replace("'","",$cbo_location_id).")";
			if(str_replace("'","",$cbo_floor_id)=="") $floor=""; else $floor="and a.floor_id in(".str_replace("'","",$cbo_floor_id).")";
			if(str_replace("'","",$hidden_line_id)=="") $line=""; else $line="and a.sewing_line in(".str_replace("'","",$hidden_line_id).")";
			if(str_replace("'","",$hidden_line_id)=="") $acc_line=""; else $acc_line="and a.id in(".str_replace("'","",$hidden_line_id).")";
			if(str_replace("'","",$cbo_buyer_name)=="") $buyer_id_cond=""; else $buyer_id_cond="and c.buyer_name in(".str_replace("'","",$cbo_buyer_name).")";
			if(str_replace("'","",trim($txt_date_from))=="") $date_cond=""; else $date_cond=" and a.production_date between $txt_date_from and $txt_date_to";
			if($job_id==0) $job_cond=""; else $job_cond="and c.id =$job_id";
			if($hidden_order_id==0) $po_cond=""; else $po_cond="and d.id in ($hidden_order_id)";
			if(str_replace("'","",trim($txt_date_from))=="") $date_cond=""; else $date_cond=" and a.production_date between $txt_date_from and $txt_date_to";

			/*===================================================================================== /
			/								get actual resource data								/
			/===================================================================================== */
			$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name in($com_id) and variable_list=23 and is_deleted=0 and status_active=1");

			if($prod_reso_allo==1)
			{
				if(str_replace("'","",$cbo_location_id)==0) $location2=""; else $location2="and a.location_id in(".str_replace("'","",$cbo_location_id).")";
				if(str_replace("'","",$cbo_floor_id)==0) $floor2=""; else $floor2="and a.floor_id in(".str_replace("'","",$cbo_floor_id).")";
				$date_cond2 = str_replace("a.production_date","b.pr_date",$date_cond);
				$prod_resource_array=array();
				$dataArray=sql_select("SELECT a.company_id,a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity,c.target_efficiency from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=b.mst_id and a.id=c.mst_id and  a.company_id in($com_id) $date_cond2 $location2 $floor2 $acc_line");
				// echo "SELECT a.company_id,a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=b.mst_id and a.id=c.mst_id and  a.company_id in($company_id) $date_cond2 $location2 $floor2";die;
				$resource_id_arr = array();
				foreach($dataArray as $val)
				{
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['man_power']=$val[csf('man_power')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['operator']=$val[csf('operator')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['helper']=$val[csf('helper')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['terget_hour']=$val[csf('target_per_hour')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['working_hour']=$val[csf('working_hour')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['day_start']=$val[csf('from_date')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['day_end']=$val[csf('to_date')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['capacity']=$val[csf('capacity')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['target_effi']=$val[csf('target_efficiency')];
					$resource_id_arr[$val[csf('id')]] = $val[csf('id')];
				}
			}
			// print_r($prod_resource_array);die;

			/* =====================================================================================================/
			/												Gmts Production data									/
			/===================================================================================================== */
			$sql="SELECT a.serving_company as wo_com, a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line,c.id as job_id,c.buyer_name,c.style_ref_no,c.job_no, a.po_break_down_id, a.item_number_id,d.po_number,d.file_no,d.unit_price,d.grouping as ref,b.color_type_id,a.remarks,sum(b.production_qnty) as good_qnty,";
			$first=1;
			for($h=$hour;$h<$last_hour;$h++)
			{
				$bg=$start_hour_arr[$h];
				$end=substr(add_time($start_hour_arr[$h],60),0,5);
				$prod_hour="prod_hour".substr($bg,0,2);
				if($first==1)
				{
					$sql.="sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN b.production_qnty else 0 END) AS $prod_hour,";
				}
				else
				{
					$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5
					THEN b.production_qnty else 0 END) AS $prod_hour,";
				}
				$first++;
			}
			$sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN b.production_qnty else 0 END) AS prod_hour23

			FROM  pro_garments_production_mst a ,pro_garments_production_dtls b, wo_po_details_master c, wo_po_break_down d,wo_po_color_size_breakdown e
			WHERE a.id=b.mst_id and a.po_break_down_id=d.id and d.job_id=c.id and d.job_id=e.job_id and d.id=e.po_break_down_id and b.color_size_break_down_id=e.id and a.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.serving_company=$com_id $location $floor $line $buyer_id_cond $job_cond $po_cond   $date_cond
			GROUP BY a.serving_company,c.job_no, a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,c.id,c.buyer_name,c.style_ref_no,a.item_number_id,d.po_number,d.unit_price,d.file_no,d.grouping ,b.color_type_id,a.remarks
			ORDER BY a.production_date";
			// echo $sql;die;
			$res = sql_select($sql);
			$data_array = array();
			$lc_com_array = array();
			$style_wise_po_arr = array();
			$poIdArr=array();
			$jobArr=array();
			$jobIdArr=array();
			$all_style_arr=array();
			$po_item_wise_prod_qty_arr=array();
			$job_wise_prod_qty_arr=array();
			$po_unit_price_array = array();
			foreach($res as $val)
			{
				$prod_line_array[$val[csf('sewing_line')]] = $val[csf('sewing_line')];
				$poIdArr[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
				$jobArr[$val[csf('job_no')]] = $val[csf('job_no')];
				$jobIdArr[$val[csf('job_id')]] = $val[csf('job_id')];
				$lc_com_array[$val[csf('company_id')]] = $val[csf('company_id')];
				$all_style_arr[$val[csf('style_ref_no')]] = $val[csf('style_ref_no')];
				$style_wise_po_arr[$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
				// $line_prod_hour_array[$val[csf('sewing_line')]]++;

				if($val[csf('prod_reso_allo')]==1)
				{
					$sewing_line_id=$prod_reso_arr[$val[csf('sewing_line')]];
					$reso_line_ids.=$val[csf('sewing_line')].',';
				}
				else
				{
					$sewing_line_id=$val[csf('sewing_line')];
				}

				if($lineSerialArr[$sewing_line_id]=="")
				{
					$lastSlNo++;
					$slNo=$lastSlNo;
					$lineSerialArr[$sewing_line_id]=$slNo;
				}
				else $slNo=$lineSerialArr[$sewing_line_id];


				$data_array[$val[csf('wo_com')]][strtotime($val[csf('production_date')])][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['company_id'].=$val[csf('company_id')]."**";
				$data_array[$val[csf('wo_com')]][strtotime($val[csf('production_date')])][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['buyer_name'].=$val[csf('buyer_name')]."**";
				$data_array[$val[csf('wo_com')]][strtotime($val[csf('production_date')])][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['style_ref_no'].=$val[csf('style_ref_no')]."**";
				$data_array[$val[csf('wo_com')]][strtotime($val[csf('production_date')])][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['job_no'].=$val[csf('job_no')]."**";
				$data_array[$val[csf('wo_com')]][strtotime($val[csf('production_date')])][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['po_break_down_id'].=$val[csf('po_break_down_id')]."**";
				$data_array[$val[csf('wo_com')]][strtotime($val[csf('production_date')])][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['po_number'].=$val[csf('po_number')]."**";
				$data_array[$val[csf('wo_com')]][strtotime($val[csf('production_date')])][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['item_name'].=$garments_item[$val[csf('item_number_id')]]."**";
				$data_array[$val[csf('wo_com')]][strtotime($val[csf('production_date')])][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['po_item'].=$val[csf('po_break_down_id')]."__".$val[csf('item_number_id')]."__".$val[csf('job_no')]."__".$val[csf('style_ref_no')]."**";
				$data_array[$val[csf('wo_com')]][strtotime($val[csf('production_date')])][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['prod_reso_allo']=$val[csf('prod_reso_allo')];

				$po_item_wise_prod_qty_arr[$val[csf('wo_com')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][strtotime($val[csf('production_date')])][$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]+=$val[csf('good_qnty')];

				$job_wise_prod_qty_arr[$val[csf('wo_com')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][strtotime($val[csf('production_date')])][$val[csf('po_break_down_id')]]+=$val[csf('good_qnty')];

				for($h=$hour;$h<=$last_hour;$h++)
				{
					$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2)."";
					$data_array[$val[csf('wo_com')]][strtotime($val[csf('production_date')])][$val[csf('floor_id')]][$slNo][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['qty'][$prod_hour]+=$val[csf($prod_hour)];

					/* if($val[csf($prod_hour)]>0)
					{
						$line_prod_hour_array[$val[csf('sewing_line')]]++;
					} */

				}

				$po_unit_price_array[$val[csf('po_break_down_id')]]=$val[csf('UNIT_PRICE')];

			}

			/*===================================================================================== /
			/										NPT Min 										/
			/===================================================================================== */			
			if(str_replace("'","",trim($txt_date_from))=="") $idle_date_cond=""; else $idle_date_cond=" and c.idle_date between $txt_date_from and $txt_date_to";
			$line_id_cond = where_con_using_array($resource_id_arr,0,"a.id");
			$sql = "SELECT c.prod_resource_id, c.id as idle_mst_id, d.id as idle_dtls_id, c.LINE_IDS as line_number,c.string_data, c.location_id, c.floor_id, c.idle_date, d.category_id, d.cause_id, d.duration_hour, d.end_hour, d.end_minute, d.manpower, d.start_hour, d.start_minute, c.remarks
			  from sewing_line_idle_mst c,sewing_line_idle_dtls d,prod_resource_mst a
			  where c.id = d.mst_id and c.prod_resource_id=a.id and c.is_deleted = 0 and a.is_deleted = 0 and c.status_active = 1 and d.status_active = 1 and d.status_active = 1  and c.idle_date = ".$txt_date_from."
	
			 and c.company_id=$com_id  $floor $line_id_cond $idle_date_cond and c.is_deleted = 0 and c.is_deleted = 0";
			// echo $sql;die;
			$res = sql_select($sql);
			$npt_min_array = array();
			foreach ($res as $r) 
			{
				$manpower = $r[csf('manpower')];
				$duration = $r[csf('duration_hour')];
				$idle_date_chk = strtotime($r[csf('idle_date')]);
				
				$idle_mnt = $duration*$manpower*60;
				$npt_min_array[strtotime($r[csf('idle_date')])][$r[csf('prod_resource_id')]] += $idle_mnt;
			}
			// echo "<pre>";print_r($npt_min_array);die;
			
			/*===================================================================================== /
			/									Operation Bulletin 									/
			/===================================================================================== */
			$style_cond = where_con_using_array($all_style_arr,1,"a.style_ref");
			$sqlgsd="SELECT a.PROCESS_ID,a.style_ref,a.gmts_item_id,b.id, b.mst_id, b.row_sequence_no, b.body_part_id, b.lib_sewing_id, b.resource_gsd, b.attachment_id, b.efficiency, b.total_smv, b.target_on_full_perc from PPL_GSD_ENTRY_MST a,ppl_gsd_entry_dtls b where a.id=b.mst_id $style_cond and a.bulletin_type=4 and b.is_deleted=0 order by b.row_sequence_no asc";
			// echo $sqlgsd;die;
			$gsd_res=sql_select($sqlgsd);
			$mst_id_arr = array();
			foreach($gsd_res as $row)
			{
				$mst_id_arr[$row['MST_ID']] = $row['MST_ID'];
			}
			$mst_id_cond = where_con_using_array($mst_id_arr,0,"a.gsd_mst_id");
			// ======================================================================
			$balanceDataArray=array();
			$blData=sql_select("SELECT a.id, gsd_dtls_id, smv, layout_mp,a.EFFICIENCY from ppl_balancing_mst_entry a, ppl_balancing_dtls_entry b where a.id=b.mst_id and a.balancing_page=1 $mst_id_cond and a.is_deleted=0 and b.is_deleted=0");
			// echo "SELECT a.id, gsd_dtls_id, smv, layout_mp,a.EFFICIENCY from ppl_balancing_mst_entry a, ppl_balancing_dtls_entry b where a.id=b.mst_id and a.balancing_page=1 $mst_id_cond and a.is_deleted=0 and b.is_deleted=0";die;
			foreach($blData as $row)
			{
				$balanceDataArray[$row[csf('gsd_dtls_id')]]['smv']=$row[csf('smv')];
				$balanceDataArray[$row[csf('gsd_dtls_id')]]['layout_mp']=$row[csf('layout_mp')];
				$balanceDataArray[$row[csf('gsd_dtls_id')]]['efficiency']=$row[csf('efficiency')];
			}

			$gsd_data_array = array();

			foreach($gsd_res as $slectResult)
			{
				if($balanceDataArray[$slectResult[csf('id')]]['smv']>0)	
				{
					$smv=$balanceDataArray[$slectResult[csf('id')]]['smv'];
				}
				else
				{
					$smv=$slectResult[csf('total_smv')];
				}
				
				$rescId=$slectResult[csf('resource_gsd')];
				$layOut=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
				$efficiency = $balanceDataArray[$slectResult[csf('id')]]['efficiency'];
				
				if($rescId==40 || $rescId==41 || $rescId==43 || $rescId==44 || $rescId==48 || $rescId==68 || $rescId==69 || $rescId==70 || $rescId==147)
				{
					$helperSmv=$helperSmv+$smv;
					$helperMp=$helperMp+$layOut;
				}
				else if($rescId==53)
				{
					$fIMSmv=$fIMSmv+$smv;
					$fImMp=$fImMp+$layOut;
				}
				else if($rescId==54)
				{
					$fQISmv=$fQISmv+$smv;
					$fQiMp=$fQiMp+$layOut;
				}
				else if($rescId==55)
				{
					$polyHelperSmv=$polyHelperSmv+$smv;
					$polyHelperMp=$polyHelperMp+$layOut;
				}
				else if($rescId==56)
				{
					$pkSmv=$pkSmv+$smv;
					$pkMp=$pkMp+$layOut;
				}
				else if($rescId==90)
				{
					$htSmv=$htSmv+$smv;
					$htMp=$htMp+$layOut;
				}
				else if($rescId==176)
				{
					$imSmv=$imSmv+$smv;
					$imMp=$imMp+$layOut;
				}
				else
				{
					$machineSmv=$machineSmv+$smv;
					$machineMp=$machineMp+$layOut;
					
					$mpSumm[$rescId]+= $layOut;
				}
				$i++;
				$totMpSumm = $helperMp + $machineMp + $sQiMp + $fImMp + $fQiMp + $polyHelperMp + $pkMp + $htMp + $imMp;
				$totHpSumm = $helperMp + $sQiMp + $fImMp + $fQiMp + $polyHelperMp + $pkMp + $htMp + $imMp;
				// echo $helperMp."<br>";
				
				$gsd_data_array[$slectResult['STYLE_REF']][$slectResult['GMTS_ITEM_ID']]['operator'] = $machineMp;
				$gsd_data_array[$slectResult['STYLE_REF']][$slectResult['GMTS_ITEM_ID']]['sew_helper'] = $totHpSumm;
				$gsd_data_array[$slectResult['STYLE_REF']][$slectResult['GMTS_ITEM_ID']]['plan_man'] = $totMpSumm;
				$gsd_data_array[$slectResult['STYLE_REF']][$slectResult['GMTS_ITEM_ID']]['efficiency'] = $efficiency;
				$gsd_data_array[$slectResult['STYLE_REF']][$slectResult['GMTS_ITEM_ID']]['smv'] += $smv;
			}

			// echo "<pre>";print_r($gsd_data_array);die;
			/*===================================================================================== /
			/										smv sorce 										/
			/===================================================================================== */
			$lc_com_ids = implode(",",$lc_com_array);
			$poIds_cond = where_con_using_array($poIdArr,0,"b.id");
			$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($lc_com_ids) and variable_list=25 and   status_active=1 and is_deleted=0");
			// echo $smv_source;

			if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
			if($smv_source==3) // from gsd enrty
			{
				$style_cond = where_con_using_array($all_style_arr,1,"a.style_ref");
				$sql_item="SELECT a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= $txt_date_to and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 and a.BULLETIN_TYPE=4 $style_cond group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID ORDER BY a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC"; //a.APPROVED=1 
				$gsdSqlResult=sql_select($sql_item);
				//echo $sql_item;die;

				foreach($gsdSqlResult as $rows)
				{
					foreach($style_wise_po_arr[$rows['STYLE_REF']] as $po_id)
					{
						if($item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]=='')
						{
							$item_smv_array[$po_id][$rows['GMTS_ITEM_ID']]=$rows['TOTAL_SMV'];
						}
					}
				}
			}
			else
			{
				$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost,c.smv_set from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c where b.job_id=a.id and b.job_id=c.job_id and a.company_name in($lc_com_ids) $poIds_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
				// echo $sql_item;
				$resultItem=sql_select($sql_item);

				foreach($resultItem as $itemData)
				{
					if($smv_source==1)
					{
						$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
					}
					if($smv_source==2)
					{
						$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs_precost')];
					}
				}
			}
			// echo "<pre>";print_r($item_smv_array);echo "</pre>";

			/*===================================================================================== /
			/										po active days									/
			/===================================================================================== */
			$poIds_cond2 = where_con_using_array($poIdArr,0,"c.id");
			$po_active_sql="SELECT a.sewing_line,a.production_date,c.id as po_id from  pro_garments_production_mst a , wo_po_break_down c,wo_po_details_master b where a.po_break_down_id=c.id and c.job_id=b.id and a.production_type=5 and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and a.serving_company=$com_id $location $floor $line $buyer_id_cond  $poIds_cond2 group by  a.sewing_line,a.production_date,c.id";
			//echo $po_active_sql;die;
			foreach(sql_select($po_active_sql) as $vals)
			{
				$prod_dates=strtotime($vals[csf('production_date')]);
				if($duplicate_date_arr[$vals[csf('sewing_line')]][$vals[csf('po_id')]][$prod_dates]=="")
				{
					$active_days_arr[$vals[csf('sewing_line')]][$vals[csf('po_id')]]++;
					$active_days_arr_powise[$vals[csf('po_id')]][$vals[csf('item_number_id')]]+=1;
					$duplicate_date_arr[$vals[csf('sewing_line')]][$vals[csf('po_id')]][$prod_dates]=$prod_dates;
				}
			}
			// echo "<pre>"; print_r($active_days_arr);die;

			/*===============================================================================/
			/                                  Booking Data                                  /
			/============================================================================== */
			$po_cond = where_con_using_array($poIdArr,0,"b.po_break_down_id");
			$sql = "SELECT a.booking_no, b.po_break_down_id as po_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_type=1 $po_cond";
			$res= sql_select($sql);
			$booking_no_arr = array();
			foreach ($res as $v)
			{
				$booking_no_arr[$v['PO_ID']] .= $v['BOOKING_NO'].",";
			}

			$tot_td = 0;
			for($k=$hour; $k<=$last_hour; $k++)
			{
				$tot_td++;
			}
			// ========================== costing per and cm =====================
			$job_id_cond = where_con_using_array($jobIdArr,0,"job_id");
			$costing_per_arr = return_library_array("SELECT job_no, costing_per from wo_pre_cost_mst where status_active=1 $job_id_cond","job_no","costing_per");
			$effi_per_arr = return_library_array("SELECT job_no, sew_effi_percent from wo_pre_cost_mst where status_active=1 $job_id_cond","job_no","sew_effi_percent");
			$costing_date_arr = return_library_array("SELECT job_no, costing_date from wo_pre_cost_mst where status_active=1 $job_id_cond","job_no","costing_date");
			$exchange_rate_arr = return_library_array("SELECT job_no, exchange_rate from wo_pre_cost_mst where status_active=1 $job_id_cond","job_no","exchange_rate");
			$cm_arr = return_library_array("SELECT job_no, cm_cost from wo_pre_cost_dtls where status_active=1 $job_id_cond","job_no","cm_cost");
			$fob_price_arr = return_library_array("SELECT job_no, price_pcs_or_set from wo_pre_cost_dtls where status_active=1 $job_id_cond","job_no","price_pcs_or_set");

			// ================================= no production line ====================================
			$prod_lines = implode(",",$prod_line_array);
			if(str_replace("'","",$cbo_line_status)!=2)
			{
				if(str_replace("'","",$cbo_location_id)==0) $location2=""; else $location2="and a.location_id in(".str_replace("'","",$cbo_location_id).")";
				if(str_replace("'","",$cbo_floor_id)==0) $floor2=""; else $floor2="and a.floor_id in(".str_replace("'","",$cbo_floor_id).")";
				$date_cond2 = str_replace("a.production_date","b.pr_date",$date_cond);
				// $prod_resource_array=array();
				$dataArray=sql_select("SELECT a.company_id,a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity,c.target_efficiency,2 as type_line from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=b.mst_id and a.id=c.mst_id and b.MAST_DTL_ID=c.id and a.id not in($prod_lines) and  a.company_id=$com_id  $date_cond2 $location2 $floor2 $acc_line");//and c.po_id in ($poIdArr)
				// echo "SELECT a.company_id,a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=b.mst_id and a.id=c.mst_id and a.id not in($prod_lines) and  a.company_id in($company_id) $date_cond2 $location2 $floor2";die;
				foreach($dataArray as $val)
				{
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['type_line']=$val[csf('type_line')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['man_power']=$val[csf('man_power')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['operator']=$val[csf('operator')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['helper']=$val[csf('helper')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['terget_hour']=$val[csf('target_per_hour')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['working_hour']=$val[csf('working_hour')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['day_start']=$val[csf('from_date')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['day_end']=$val[csf('to_date')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['capacity']=$val[csf('capacity')];
					$prod_resource_array[$val[csf('company_id')]][$val[csf('id')]][strtotime($val[csf('pr_date')])]['target_effi']=$val[csf('target_efficiency')];

					$sewing_line_arr=explode(",",$prod_reso_arr[$val[csf('id')]]);
					if($lineSerialArr[$sewing_line_arr[0]]=="")
					{
						$lastSlNo++;
						$slNo=$lastSlNo;
						$lineSerialArr[$sewing_line_arr[0]]=$slNo;
					}
					else $slNo=$lineSerialArr[$sewing_line_arr[0]];

					$data_array[$val[csf('company_id')]][strtotime($val[csf('pr_date')])][$val[csf('floor_id')]][$slNo][$val[csf('id')]]['0']['no_prod'] = 1;
				}
			}

			// =================================== cost per min ==================================
			$sql = "SELECT id,APPLYING_PERIOD_DATE, APPLYING_PERIOD_TO_DATE,COST_PER_MINUTE from LIB_STANDARD_CM_ENTRY where status_active=1 and is_deleted=0 and company_id=$com_id order by APPLYING_PERIOD_DATE";
			// echo $sql;die;
			$res = sql_select($sql);
			$cpm_app_period_arr = array();

			foreach($res as $row )
			{
				$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
				$diff=datediff('m',$applying_period_date,$applying_period_to_date);
				// echo $diff."<br>";
				for($j=0;$j<=$diff;$j++)
				{
					$newMonth = date('m-Y', strtotime($applying_period_date.' + '.$j.' months'));
					$cpm_app_period_arr[$newMonth]=$row[csf('cost_per_minute')];
					// echo $newMonth."<br>";
				}
			}

			/* foreach ($res as $v)
			{
				$cpm_app_period_arr[$v['ID']]['applying_period_date'] = $v['APPLYING_PERIOD_DATE'];
				$cpm_app_period_arr[$v['ID']]['applying_period_to_date'] = $v['APPLYING_PERIOD_TO_DATE'];
				$cpm_app_period_arr[$v['ID']]['cpm'] = $v['COST_PER_MINUTE'];
			} */
			// datediff('')
			/* $date_wise_cpm_arr = array();
			foreach ($cpm_app_period_arr as $cpm_id => $v)
			{
				$dt = GetDays($v['applying_period_date'],$v['applying_period_to_date']);
				// echo "<pre>"; print_r($dt);die;

				$datediff = datediff('d',$v['applying_period_date'],$v['applying_period_to_date']);
				for ($i=0; $i < $datediff; $i++)
				{
					// echo $i."<br>";
					$date_wise_cpm_arr[date('Y-m-d', strtotime("+".$i." day", strtotime($v['applying_period_date'])))] = $v['cpm'];
				}
			} */
			// echo "<pre>"; print_r($cpm_app_period_arr);die;
			// =========================== conversion rate ============================
			$sql = "SELECT conversion_rate,con_date from currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 and company_id=$com_id order by con_date desc";
			// echo $sql;die;
			$res = sql_select($sql);
			foreach ($res as $v)
			{
				# code...
			}
			// =========count rowspand ====================
			$rowspan_arr = array();
			$floor_wise_tot_line = array();
			$gr_tot_line = 0;
			foreach ($data_array[$com_id] as $date_key => $date_data)
			{
				foreach ($date_data as $flr_id => $flr_data)
				{
					ksort($flr_data);
					foreach ($flr_data as $li_sl => $sl_data)
					{
						foreach ($sl_data as $l_id => $l_data)
						{
							foreach ($l_data as $po_id => $r)
							{
								$floor_wise_tot_line[$com_key][$date_key][$flr_id]++;
								$rowspan_arr[$com_key][$date_key][$flr_id]++;
								$gr_tot_line++;
							}
						}
					}
				}
			}
			// echo "<pre>"; print_r($floor_wise_tot_line);die;
			// ====================== current hour ================
			/* $time1 = strtotime($hour.":00");
			$time2 = strtotime(date('H:i:s'));
			// echo $time2."==".strtotime(str_replace("'","",$txt_date));die;

			if(substr($global_start_lanch,0,2) < substr(date('H:i:s'),0,2))
			{
				// $difference_hour = round(((abs($time2 - $time1) / 3600)),0);
				$cur_difference_hour = (int)(abs($time2 - $time1) / 3600);
				$cur_difference_hour = $cur_difference_hour - 1;
				// echo $cur_difference_hour."==SSSSSSSS";
			}
			else
			{
				$cur_difference_hour = round(((abs($time2 - $time1) / 3600)),0);
			} */


			$time1 = $hour;
			$time2 = date('H');
			// echo $time2."==".strtotime(str_replace("'","",$txt_date));die;

			if(substr($global_start_lanch,0,2) < $time2)
			{
				// $difference_hour = round(((abs($time2 - $time1) / 3600)),0);
				$cur_difference_hour = (int) $time2 - $time1;
				$cur_difference_hour = $cur_difference_hour;// - 1;
				// echo $cur_difference_hour."==SSSSSSSS";
			}
			else
			{
				$cur_difference_hour = (int) $time2 - $time1;
			}
			// echo $cur_cur_difference_hour;die;
			// echo "<pre>";print_r($data_array);echo "</pre>";
			$tbl_width = 3830+($tot_td*50);
			// ob_start();
			
			$l=0;
			?>
			<fieldset style="width:<?=$tbl_width+20;?>px">
			<table width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0">
					<tr class="form_caption">
						<td colspan="57" align="center" style="font-size: 20px;"><? echo $report_title; ?></td>
					</tr>
					<tr class="form_caption">
						<td colspan="57" align="center" style="font-size: 17px;"><? echo $companyArr[$com_key]; ?></td>
					</tr>
					<tr class="form_caption">
						<td colspan="57" align="center" style="font-size: 15px;"><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date_from)) ); ?></td>
					</tr>
				</table>
				<br />
				<table id="table_header_1" class="rpt_table" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<caption style="text-align: left;color:red;font-weight:bold;font-size:16px;">"You will obtain accurate eff% data after the current production date."</caption>
					<thead>
						<tr>
							<th width="30">SL</th>
							<th width="60">Date</th>
							<th width="120">Working Company</th>
							<th width="120">Floor Name</th>
							<th width="80">Line No</th>
							<th width="120">LC Company</th>
							<th width="120">Buyer</th>
							<th width="120">Job</th>
							<th width="120">Fab. Booking</th>
							<th width="120">Order No</th>
							<th width="120">Style</th>
							<th width="120">Garments Item</th>
							<th width="80">SMV</th>
							<!-- <th width="80">Avg. SMV</th> -->
							<th width="80">Operator</th>
							<th width="80">Helper</th>
							<th width="80">Man Power</th>
							<th width="80">Active Prod Days</th>
							<th width="80">Day Line Capacity Pcs</th>
							<th width="80">Plan Working Hour</th>
							<th width="80">Line Prod. Working Hour</th>
							<th width="80">Hourly Target Pcs</th>
							<th width="80">Current Hour</th>
							<th width="80">As on Current Hourly Target Pcs</th>
							<!-- <th width="80" title="[As on Current Prod - As on Current Hourly Target Pcs]">Hourly Prod Varience</th> -->
							<th width="80">Total Target</th>
							<th width="80">General Prod.</th>
							<th width="80">OT Prod.</th>
							<th width="80">Total Prod.</th>
							<th width="80">Total Varience</th>
							<th width="80" title=" [Working Hourx60xTTL Manpower]">Available Min.</th>
							<th width="80">NPT Min.</th>
							<th width="80" title=" [Man Power x Line Prod. Working Hour x 60]">Man Min Used</th>
							<th width="80" title="[General Production x SMV">Gen. Prod. Min.</th>
							<th width="80" title="[OT Production x SMV]">OT Prod. Min.</th>
							<th width="80">Tot Prod. Min.</th>
							<th width="80" title="[TTL Production/(Target per hour*Working Hour)]">Target Hit Rate%</th>
							<th width="80" title="{From Operation Buletin}">Target Effi%</th>
							<th width="80" title="[Total Prod. Min. / Man Min Used]">Achv Effi%</th>
							<th width="80">Effi Gap</th>
							<th width="80" title="[TTL Production*SMV/(Total Man Power*60*Working Hour]">Line Effi %</th>
							<!-- <th width="80">Style Change</th> -->
							<th width="80" title="[From Pre-Costing]">CM Pcs</th>
							<th width="80" title="[CM/PC * TTL Production]">Total CM</th>
							<th width="80" title="[Total CM * ER]">CM Earned in BDT</th>
							<!-- <th width="80" title="[Target Production * CM per PC]">Target CM</th> -->
							<!-- <th width="80">Avg Unit Price</th> -->
							<!-- <th width="80" title="(TTL. Prod.*Unit Price)">Tot Prod Value FOB</th> -->
							<!-- <th width="80" title="(Unit price*Total Target Pcs)">Target Value FOB</th> -->
							<th width="80" title="[(CPM/Eff%)*Man Min Used]">Line Cost BDT</th>
							<!-- <th width="80" title="[TTL Line Cost / CM per PC]">BEP Units</th> -->
							<!-- <th width="80" title="[Total Production x Fob value x 15%]">Earn Value FOB USD</th> -->
							<!-- <th width="80" title="[Earnvalue * convertion rate dollar]">Earn Value FOB BDT</th> -->
							<th width="80" title="[CM Earned in BDT - Line Cost BDT]">Line Profit</th>
							<?
							for($k=$hour; $k<=$last_hour; $k++)
							{
								?>
								<th width="50" style="vertical-align:middle"></p><div class="block_div"><?=substr($start_hour_arr[$k],0,5)."-<br>".substr($start_hour_arr[$k+1],0,5);?></div></p></th>
								<?
							}
							?>
						</tr>
					</thead>
				</table>
				<div style="width:<?=$tbl_width+20;?>px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<tbody>
							<?
							$i=1;
							$floor_tot_array = array();
							$gr_tot_array = array();
							$gr_tot_smv = 0;
							$gr_tot_avg_smv = 0;
							$gr_tot_operator = 0;
							$gr_tot_helper = 0;
							$gr_tot_man_power = 0;
							$gr_tot_act_days = 0;
							$gr_tot_cap_pcs = 0;
							$gr_tot_plan_wo_hour = 0;
							$gr_tot_prod_hour = 0;
							$gr_tot_hour_trg_pcs = 0;
							$gr_tot_cur_hour = 0;
							$gr_tot_as_on_cur_hour_trg_pcs = 0;
							$gr_tot_hour_prod_varience = 0;
							$gr_tot_target = 0;
							$gr_tot_gen_prod = 0;
							$gr_tot_ot_prod = 0;
							$gr_tot_prod = 0;
							$gr_tot_varience = 0;
							$gr_tot_avl_min = 0;
							$gr_tot_npt_min = 0;
							$gr_tot_man_min_used = 0;
							$gr_tot_gen_prod_min = 0;
							$gr_tot_ot_prod_min = 0;
							$gr_tot_prod_min = 0;
							$gr_tot_target_hit = 0;
							$gr_tot_target_effi = 0;
							$gr_tot_achv_effi = 0;
							$gr_tot_effi_gap = 0;
							$gr_tot_line_effi = 0;
							$gr_tot_style_cng = 0;
							$gr_tot_cm_pcs = 0;
							$gr_tot_cm = 0;
							$gr_tot_cm_earn_bdt = 0;
							$gr_tot_target_cm = 0;
							$gr_tot_fob_val = 0;
							$gr_tot_target_fob_val = 0;
							$gr_tot_bep_unit = 0;
							$gr_tot_earn_val_fob_usd = 0;
							$gr_tot_earn_val_fob_bdt = 0;
							$gr_tot_line_cost = 0;
							$gr_tot_line_profit = 0;
							foreach ($data_array[$com_id] as $date_key => $date_data)
							{
								foreach ($date_data as $flr_id => $flr_data)
								{
									$flCount = 0;
									ksort($flr_data);
									$flr_tot_operator = 0;
									$flr_tot_helper = 0;
									$flr_tot_man_power = 0;
									$flr_tot_act_days = 0;
									$flr_tot_cap_pcs = 0;
									$flr_tot_plan_wo_hour = 0;
									$flr_tot_prod_hour = 0;
									$flr_tot_hour_trg_pcs = 0;
									$flr_tot_cur_hour = 0;
									$flr_tot_as_on_cur_hour_trg_pcs = 0;
									$flr_tot_hour_prod_varience = 0;
									$flr_tot_target = 0;
									$flr_tot_gen_prod = 0;
									$flr_tot_ot_prod = 0;
									$flr_tot_prod = 0;
									$flr_tot_varience = 0;
									$flr_tot_avl_min = 0;
									$flr_tot_npt_min = 0;
									$flr_tot_man_min_used = 0;
									$flr_tot_gen_prod_min = 0;
									$flr_tot_ot_prod_min = 0;
									$flr_tot_prod_min = 0;
									$flr_tot_target_hit = 0;
									$flr_tot_target_effi = 0;
									$flr_tot_achv_effi = 0;
									$flr_tot_effi_gap = 0;
									$flr_tot_line_effi = 0;
									$flr_tot_style_cng = 0;
									$flr_tot_cm_pcs = 0;
									$flr_tot_target_cm = 0;
									$flr_tot_cm = 0;
									$flr_tot_cm_earn_bdt = 0;
									$flr_tot_fob_val = 0;
									$flr_tot_target_fob_val = 0;
									$flr_tot_bep_unit = 0;
									$flr_tot_earn_val_fob_usd = 0;
									$flr_tot_earn_val_fob_bdt = 0;
									$flr_tot_line_cost = 0;
									$flr_tot_line_profit = 0;

									$flr_tot_smv = 0;
									$flr_tot_avg_smv = 0;

									foreach ($flr_data as $li_sl => $sl_data)
									{
										foreach ($sl_data as $l_id => $l_data)
										{
											foreach ($l_data as $po_id => $r)
											{
												$sewing_line='';
												if($r['prod_reso_allo']==1)
												{
													$sewing_line_ids=$prod_reso_arr[$l_id];
													$sl_ids_arr = explode(",", $sewing_line_ids);
													foreach($sl_ids_arr as $val)
													{
														if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line.=",".$lineArr[$val];
													}
												}
												else
												{
													$sewing_line=$lineArr[$l_id];
												}
												// ======================= smv =================
												$item_smv = '';
												$gsd_efficiency = '';
												$tot_smv = 0;
												$item_smv_count=0;
												$produce_minit = 0;
												$booking_no = "";
												$general_prod_min = 0;
												$ot_prod_min = 0;
												$po_count = 0;
												// $tot_cm = 0;
												$po_item_arr = array_unique(array_filter(explode("**",$r['po_item'])));
												$po_chk_arr = array();
												$unit_price_arr = array();
												foreach ($po_item_arr as $po_item_data)
												{
													// echo $po_item_data."dddd<br>";
													$po_item_ex_arr = explode("__",$po_item_data);
													$job = $po_item_ex_arr[2];
													if($po_chk_arr[$po_item_ex_arr[1].$po_item_ex_arr[2]]=="")
													{
														$item_smv .= ($item_smv=="") ? number_format($item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]],2) : "/".number_format($item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]],2);
														$tot_smv += $item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]];
														$flr_tot_smv += $item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]];
														$gr_tot_smv += $item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]];
														$item_smv_count++;
														$po_chk_arr[$po_item_ex_arr[1].$po_item_ex_arr[2]] = "AA";

														$tot_cm += ($effi_per_arr[$job]) ? ($item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]]*$cpm_app_period_arr[date('m-Y',strtotime($costing_date_arr[$job]))])/$effi_per_arr[$job] : 0;
														// echo $item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]]."*".$cpm_app_period_arr[date('m-Y',strtotime($costing_date_arr[$job]))]."/".$effi_per_arr[$job]."<br>";
													}

													$produce_minit+=$po_item_wise_prod_qty_arr[$com_key][$flr_id][$l_id][$date_key][$po_item_ex_arr[0]][$po_item_ex_arr[1]]*$item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]];
													// echo $po_item_wise_prod_qty_arr[$com_key][$flr_id][$l_id][$date_key][$po_item_ex_arr[0]][$po_item_ex_arr[1]]."*".$item_smv_array[$po_item_ex_arr[0]][$po_item_ex_arr[1]]."<br>";

													$booking_no .= $booking_no_arr[$po_item_ex_arr[0]].",";

													$general_prod_qty = 0;
													$ot_prod_qty = 0;
													$gen_last_prod_hour = "";
													$ot_last_prod_hour = "";
													$m=1;
													for($k=$hour; $k<=$last_hour; $k++)
													{
														$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
														if($m<=9)
														{
															$general_prod_qty += $r['qty'][$prod_hour];
															if($r['qty'][$prod_hour]>0)
															{
																$gen_last_prod_hour=substr($start_hour_arr[$k],0,2);
															}
														}
														else
														{
															$ot_prod_qty += $r['qty'][$prod_hour];
															if($r['qty'][$prod_hour]>0)
															{
																$ot_last_prod_hour=substr($start_hour_arr[$k],0,2);
															}
														}
														$m++;
													}

													// echo $gen_last_prod_hour-$ot_last_prod_hour;die;
													// $ot_prod_min += $ot_prod_qty*$gsd_data_array[$po_item_ex_arr[3]][$po_item_ex_arr[1]];
													// $general_prod_min += $general_prod_qty*$gsd_data_array[$po_item_ex_arr[3]][$po_item_ex_arr[1]];
													$po_count++;
													// $tot_fob_val+=($general_prod_qty+$ot_prod_qty)*$unit_price;
													$gsd_efficiency .= ($gsd_efficiency=='') ? $gsd_data_array[$po_item_ex_arr[3]][$po_item_ex_arr[1]]['efficiency'] : ", ".$gsd_data_array[$po_item_ex_arr[3]][$po_item_ex_arr[1]]['efficiency'];
													// echo $gsd_efficiency."<br>";

												}
												// $avg_unit_price = $unit_price/$po_count;

												$avg_smv = ($item_smv_count>0) ? number_format(($tot_smv/$item_smv_count),2) : '0.00';
												$flr_tot_avg_smv += $avg_smv;
												$gr_tot_avg_smv += $avg_smv;

												$job_no = implode(", ",array_unique(array_filter(explode("**",$r['job_no']))));
												$po_number = implode(", ",array_unique(array_filter(explode("**",$r['po_number']))));
												$style_ref_no = implode(", ",array_unique(array_filter(explode("**",$r['style_ref_no']))));
												$item_name = implode(", ",array_unique(array_filter(explode("**",$r['item_name']))));
												$booking_no = implode(", ",array_unique(array_filter(explode(",",$booking_no))));

												$company_id_arr = array_unique(array_filter(explode("**",$r['company_id'])));
												$company_name = "";
												foreach ($company_id_arr as $v)
												{
													$company_name .= ($company_name=="") ? $companyArr[$v]: ", ".$companyArr[$v];
												}
												$buyer_name_arr = array_unique(array_filter(explode("**",$r['buyer_name'])));
												$buyer_name = "";
												foreach ($buyer_name_arr as $v)
												{
													$buyer_name .= ($buyer_name=="") ? $buyerArr[$v]: ", ".$buyerArr[$v];
												}

												$current_hour = 0;

												$line_prod_hour_array = array();
												if(strtotime(date('d-M-Y')) != $date_key)
												{
													$working_hour = $prod_resource_array[$com_key][$l_id][$date_key]['working_hour']-1;
													// $difference_hour = $line_prod_hour_array[$l_id];
													for($k=$hour; $k<=$last_hour; $k++)
													{
														$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
														// echo $r['qty'][$prod_hour]."<br>";
														if($r['qty'][$prod_hour]>0)
														{
															$line_prod_hour_array[$l_id]++;
														}

													}
													$difference_hour = $line_prod_hour_array[$l_id];


													if($ot_last_prod_hour!="")
													{
														$current_hour = $ot_last_prod_hour - $hour;
													}
													else
													{
														if(substr($global_start_lanch,0,2) < substr(date('H:i:s'),0,2))
														{
															$current_hour = $prod_resource_array[$com_key][$l_id][$date_key]['working_hour']-1;
														}
														else
														{
															$current_hour = $prod_resource_array[$com_key][$l_id][$date_key]['working_hour'];
														}
													}
												}
												else // for current date
												{
													for($k=$hour; $k<=$last_hour; $k++)
													{
														$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
														// echo $r['qty'][$prod_hour]."<br>";
														if($r['qty'][$prod_hour]>0)
														{
															$line_prod_hour_array[$l_id]++;
														}

													}

													$difference_hour = $line_prod_hour_array[$l_id];
													if($ot_last_prod_hour!="")
													{
														$current_hour = $ot_last_prod_hour - $hour;
													}
													else
													{
														$current_hour = $cur_difference_hour;
													}

													// chk lunch hour
													if(substr($global_start_lanch,0,2) < substr(date('H:i:s'),0,2))
													{
														$working_hour = $prod_resource_array[$com_key][$l_id][$date_key]['working_hour'];//-1;
													}
													else
													{
														$working_hour = $prod_resource_array[$com_key][$l_id][$date_key]['working_hour'];
													}

												}

												// $available_min=($prod_resource_array[$com_key][$l_id][$date_key]['man_power'])*$working_hour*60;
												$available_min=($prod_resource_array[$com_key][$l_id][$date_key]['man_power'])*$prod_resource_array[$com_key][$l_id][$date_key]['working_hour']*60;
												$man_min_used = $prod_resource_array[$com_key][$l_id][$date_key]['man_power']*($difference_hour*60);
												$ot_prod_hour = ($ot_last_prod_hour>0) ? $ot_last_prod_hour - $gen_last_prod_hour : 0;
												// $ot_prod_min = ($prod_resource_array[$com_key][$l_id][$date_key]['man_power'])*$ot_prod_hour*60;
												// $general_prod_min = ($prod_resource_array[$com_key][$l_id][$date_key]['man_power'])*8*60;

												$general_prod_min = $general_prod_qty*$avg_smv;
												$ot_prod_min = $ot_prod_qty*$avg_smv;
												$tot_produce_minit = $general_prod_min+$ot_prod_min;
												// echo $working_hour;die;

												// ==================================
												$m=1;
												$general_prod_qty = 0;
												$ot_prod_qty = 0;
												$tot_prod_qty = 0;
												$line_prod_hour = 0;
												$total_eff_hour = 0;

												for($k=$hour; $k<=$last_hour; $k++)
												{
													$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
													if($m<=9)
													{
														$general_prod_qty += $r['qty'][$prod_hour];
														// echo "string<br>";
													}
													else
													{
														$ot_prod_qty += $r['qty'][$prod_hour];
													}
													$tot_prod_qty += $r['qty'][$prod_hour];
													$line_prod_hour++;
													$m++;

													if($r['qty'][$prod_hour]>0)
													{
														$total_eff_hour++;
													}
												}
												$eff_target2=($prod_resource_array[$com_key][$l_id][$date_key]['terget_hour']*$total_eff_hour);
												$target_hit = ($prod_resource_array[$com_key][$l_id][$date_key]['terget_hour']*$current_hour>0) ? $tot_prod_qty/($prod_resource_array[$com_key][$l_id][$date_key]['terget_hour']*$current_hour)*100 : 0;
												$total_varience = $prod_resource_array[$com_key][$l_id][$date_key]['tpd'] - $tot_prod_qty;

												// $efficiency_min=($prod_resource_array[$com_key][$l_id][$date_key]['man_power'])*$current_hour*60;
												$efficiency_min=($prod_resource_array[$com_key][$l_id][$date_key]['man_power'])*$difference_hour*60;
												$efficiency_min2=($prod_resource_array[$com_key][$l_id][$date_key]['man_power'])*$current_hour*60;
												$eff_target=($prod_resource_array[$com_key][$l_id][$date_key]['terget_hour']*$difference_hour);
												$act_target_effi = 	$prod_resource_array[$com_key][$l_id][$date_key]['target_effi'];
												$target_min = $eff_target * $avg_smv;
												$target_effi = ($efficiency_min>0) ? ($target_min / $efficiency_min)*100 : 0;
												$achive_effi = ($efficiency_min>0) ? ($produce_minit / $efficiency_min)*100 : 0;
												// echo $produce_minit ."/". $efficiency_min."<br>";
												$efficiency_gap = $act_target_effi - $achive_effi;
												$line_efficiency=($efficiency_min>0) ? (($produce_minit)*100)/$efficiency_min2 : 0;


												$job_no_arr = array_unique(array_filter(explode("**",$r['job_no'])));
												$active_days = "";
												$style_change = 0;
												$tot_cm = 0;
												$cm_counter = 0;
												$dzn_qnty = 0;
												$tot_amount = 0;
												$tot_qty = 0;
												$dzn_qnty = 0;
												$tot_cpm = 0;
												$exchane_rate = 0;
												$k=0;
												foreach ($job_no_arr as $j_key => $job)
												{
													$active_days .= ($active_days=="") ? $active_days_arr[$l_id][$po_id] : "/".$active_days_arr[$l_id][$po_id];
													if($k>0)
													{
														$style_change++;
													}
													$k++;

													// ======================================
													$costing_per=$costing_per_arr[$job];
													if($costing_per==1) $dzn_qnty=12;
													else if($costing_per==3) $dzn_qnty=12*2;
													else if($costing_per==4) $dzn_qnty=12*3;
													else if($costing_per==5) $dzn_qnty=12*4;
													else $dzn_qnty=1;
													$cm_counter++;
													$tot_cm += $cm_arr[$job]/$dzn_qnty;


													$tot_amount += $fob_price_arr[$job]*$job_wise_prod_qty_arr[$com_key][$flr_id][$l_id][$date_key][$po_id];
													$tot_qty += $job_wise_prod_qty_arr[$com_key][$flr_id][$l_id][$date_key][$po_id];
													// echo $fob_price_arr[$job]."/".$job_wise_prod_qty_arr[$com_key][$flr_id][$l_id][$date_key][$job]."<br>";

													$tot_cpm += $cpm_app_period_arr[date('m-Y',strtotime($costing_date_arr[$job]))];
													$exchane_rate += $exchange_rate_arr[$job];


												}
												$avg_cpm = ($cm_counter) ? $tot_cpm/$cm_counter : 0;
												$avg_exchane_rate = ($cm_counter) ? $exchane_rate/$cm_counter : 0;
												$avg_unit_price = ($tot_qty) ?  $tot_amount/$tot_qty : 0;
												$tot_fob_val =($general_prod_qty+$ot_prod_qty)*$avg_unit_price;
												$avg_cm = ($cm_counter) ? $tot_cm/$cm_counter : 0;
												$ttl_cm = $tot_prod_qty*$avg_cm;
												// $ttl_cm = $tot_cpm*$man_min_used;
												$target_cm = $prod_resource_array[$com_key][$l_id][$date_key]['tpd']*$avg_cm;
												$target_fob_val = $prod_resource_array[$com_key][$l_id][$date_key]['tpd']*$avg_unit_price;
												$ttl_cm_earn_bdt = $ttl_cm*$avg_exchane_rate;


												$current_targrt = $current_hour*$prod_resource_array[$com_key][$l_id][$date_key]['terget_hour'];
												$varience_as_on_cur_hr = ($prod_resource_array[$com_key][$l_id][$date_key]['terget_hour']*$current_hour) - $tot_prod_qty;//$r['qty']['prod_hour'.$difference_hour];

												$line_cost_bdt = ($tot_cpm / $line_efficiency)*$man_min_used;
												// echo $available_min."*".$avg_cpm."*".$avg_exchane_rate."<br>";
												$bep_units = ($avg_cm) ? $line_cost_bdt/$avg_cm : 0;
												$earn_val_usd = ($tot_qty*$avg_unit_price*15)/100;
												$earn_val_bdt = $earn_val_usd*$avg_exchane_rate;
												// echo $earn_val_bdt."*".$avg_exchane_rate."<br>";
												$line_profit = $ttl_cm_earn_bdt - $line_cost_bdt;
												$npt_min = $npt_min_array[$date_key][$l_id];

												if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>
												<tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
													<td width="30"><?=$i;?></td>
													<td width="60"><p><?=($date_key!="") ? date("d-m-Y",$date_key) : "";?></p></td>
													<td width="120"><p><?=$companyArr[$com_key];?></p></td>
													<td width="120"><p><?=$floorArr[$flr_id];?></p></td>
													<td width="80" title="sl=<?=$li_sl;?>"><p><?=$sewing_line;?></p></td>
													<td width="120"><p><?=$company_name;?></p></td>
													<td width="120"><p><?=$buyer_name;?></p></td>
													<td width="120"><p><?=$job_no;?></p></td>
													<td width="120"><p><?=$booking_no;?></p></td>
													<td width="120"><p><?=$po_number;?></p></td>
													<td width="120"><p><?=$style_ref_no;?></p></td>
													<td width="120"><p><?=$item_name;?></p></td>
													<td width="80"><p><?=$item_smv;?></p></td>
													<!-- <td width="80"><p><?=$avg_smv;?></p></td> -->
													<td align="right" width="80"><p><?=$prod_resource_array[$com_key][$l_id][$date_key]['operator'];?></p></td>
													<td align="right" width="80"><p><?=$prod_resource_array[$com_key][$l_id][$date_key]['helper'];?></p></td>
													<td align="right" width="80"><p><?=$prod_resource_array[$com_key][$l_id][$date_key]['man_power'];?></p></td>
													<td align="right" width="80"><p>&nbsp;<?=$active_days;?></p></td>
													<td align="right" width="80"><p><?=$prod_resource_array[$com_key][$l_id][$date_key]['capacity'];?></p></td>
													<td align="right" width="80"><p><?=$prod_resource_array[$com_key][$l_id][$date_key]['working_hour'];?></p></td>
													<td align="right" width="80"><p><?=number_format($difference_hour,0);?></p></td>
													<td align="right" width="80"><p><?=$prod_resource_array[$com_key][$l_id][$date_key]['terget_hour'];?></p></td>
													<td align="right" width="80"><p><?=number_format($current_hour,1);?></p></td>
													<td align="right" width="80"><p><?=$current_targrt;?></p></td>
													<!-- <td align="right" width="80"><p><?=number_format($varience_as_on_cur_hr,0);?></p></td> -->
													<td align="right" width="80"><p><?=$prod_resource_array[$com_key][$l_id][$date_key]['tpd'];?></p></td>
													<td align="right" width="80"><p><?=number_format($general_prod_qty,0);?></p></td>
													<td align="right" width="80"><p><?=number_format($ot_prod_qty,0);?></p></td>
													<td align="right" width="80"><p><?=number_format($tot_prod_qty,0);?></p></td>
													<td align="right" width="80"><p><?=number_format($total_varience,0);?></p></td>
													<td align="right" width="80"><p><?=number_format($available_min,0);?></p></td>
													<td align="right" width="80"><p><?=number_format($npt_min,0);?></p></td>
													<td align="right" width="80"><p><?=number_format($man_min_used,0);?></p></td>
													<td align="right" width="80"><p><?=number_format($general_prod_min,0);?></p></td>
													<td align="right" width="80"><p><?=number_format($ot_prod_min,0);?></p></td>
													<td align="right" width="80"><p><?=number_format($tot_produce_minit,0);?></p></td>
													<td align="right" width="80"><p><?=number_format($target_hit,2);?></p></td>
													<td align="right" width="80"><p><?=number_format($gsd_efficiency,2);?></p></td>
													<td align="right" width="80"><p><?=number_format($achive_effi,2);?></p></td>
													<td align="right" width="80"><p><?=number_format($efficiency_gap,2);?></p></td>
													<td align="right" width="80"><p><?=number_format($line_efficiency,2);?></p></td>
													<!-- <td align="right" width="80"><p><?=$style_change;?></p></td> -->
													<td align="right" width="80"><p><?=number_format($avg_cm,2);?></p></td>
													<td align="right" width="80"><p><?=number_format($ttl_cm,2);?></p></td>
													<td align="right" width="80"><p><?=number_format($ttl_cm_earn_bdt,2);?></p></td>
													<!-- <td align="right" width="80"><p><?=number_format($target_cm,2);?></p></td> -->
													<!-- <td align="right" width="80"><p><?=number_format($avg_unit_price,2);?></p></td> -->
													<!-- <td align="right" width="80"><p><?=number_format($tot_fob_val,2);?></p></td> -->
													<!-- <td align="right" width="80"><p><?=number_format($target_fob_val,2);?></p></td> -->
													<td align="right" width="80"><p><?=number_format($line_cost_bdt,2);?></p></td>
													<!-- <td align="right" width="80"><p><?=number_format($bep_units,2);?></p></td> -->
													<!-- <td align="right" width="80"><p><?=number_format($earn_val_usd,2);?></p></td> -->
													<!-- <td align="right" width="80"><p><?=number_format($earn_val_bdt,2);?></p></td> -->
													<td align="right" width="80"><p><?=number_format($line_profit,2);?></p></td>
													<?
													$line_tot = 0;
													for($k=$hour; $k<=$last_hour; $k++)
													{
														$rowspan = 0;
														if($k==$lunch_start_time_arr[$com_key]) // lunch hour
														{
															if($flCount==0)
															{
																?>
																<td title="Lunch Hour" rowspan="<?=$rowspan_arr[$com_key][$date_key][$flr_id];?>" width="50" style="background-color:#8B7E74;"></td>
																<!-- <td valign="middle" width="50" style="background:red;"></td> -->
																<?
																$flCount++;
															}
														}
														else
														{
															$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
															$color="";
															if($r['qty'][$prod_hour] == $prod_resource_array[$com_key][$l_id][$date_key]['terget_hour'])
															{
																$color="green";
															}
															elseif ($r['qty'][$prod_hour] > $prod_resource_array[$com_key][$l_id][$date_key]['terget_hour'])
															{
																$color="#2037df";
															}
															elseif ($r['qty'][$prod_hour] < $prod_resource_array[$com_key][$l_id][$date_key]['terget_hour'] && $r['qty'][$prod_hour] > 0)
															{
																$color="#f50a10";
															}
															?>
															<td bgcolor="<?=$color;?>"  valign="middle" align="right" width="50"><?=number_format($r['qty'][$prod_hour],0);?></td>
															<?
															$line_tot += $r['qty'][$prod_hour];
															$floor_tot_array[$com_key][$flr_id][$prod_hour] += $r['qty'][$prod_hour];
															$gr_tot_array[$com_key][$prod_hour] += $r['qty'][$prod_hour];
														}
													}
													?>
												</tr>
												<?
												$i++;
												$flr_tot_operator += $prod_resource_array[$com_key][$l_id][$date_key]['operator'];
												$flr_tot_helper += $prod_resource_array[$com_key][$l_id][$date_key]['helper'];
												$flr_tot_man_power +=$prod_resource_array[$com_key][$l_id][$date_key]['man_power'];
												$flr_tot_act_days += $active_days;
												$flr_tot_cap_pcs += $prod_resource_array[$com_key][$l_id][$date_key]['capacity'];
												$flr_tot_plan_wo_hour += $prod_resource_array[$com_key][$l_id][$date_key]['working_hour'];
												$flr_tot_prod_hour += $difference_hour;
												$flr_tot_hour_trg_pcs += $prod_resource_array[$com_key][$l_id][$date_key]['terget_hour'];
												$flr_tot_cur_hour += $difference_hour;
												$flr_tot_as_on_cur_hour_trg_pcs += $current_targrt;
												$flr_tot_hour_prod_varience += $varience_as_on_cur_hr;
												$flr_tot_target += $prod_resource_array[$com_key][$l_id][$date_key]['tpd'];
												$flr_tot_gen_prod += $general_prod_qty;
												$flr_tot_ot_prod += $ot_prod_qty;
												$flr_tot_prod += $tot_prod_qty;
												$flr_tot_varience += $total_varience;
												$flr_tot_avl_min += $available_min;
												$flr_tot_npt_min += $npt_min;
												$flr_tot_man_min_used += $man_min_used;
												$flr_tot_gen_prod_min += $general_prod_min;
												$flr_tot_ot_prod_min += $ot_prod_min;
												$flr_tot_prod_min += $tot_produce_minit;
												$flr_tot_target_hit += $target_hit;
												$flr_tot_target_effi += $gsd_efficiency;
												$flr_tot_achv_effi += $achive_effi;
												$flr_tot_effi_gap += $efficiency_gap;
												$flr_tot_line_effi += $line_efficiency;
												$flr_tot_style_cng += $style_change;
												$flr_tot_cm_pcs += $avg_cm;
												$flr_tot_cm += $ttl_cm;
												$flr_tot_cm_earn_bdt += $ttl_cm_earn_bdt;
												$flr_tot_target_cm += $target_cm;
												$flr_tot_fob_val += $tot_fob_val;
												$flr_tot_target_fob_val += $target_fob_val;
												$flr_tot_bep_unit += $bep_units;
												$flr_tot_earn_val_fob_usd += $earn_val_usd;
												$flr_tot_earn_val_fob_bdt += $earn_val_bdt;
												$flr_tot_line_cost += $line_cost_bdt;
												$flr_tot_line_profit += $line_profit;


												$gr_tot_operator += $prod_resource_array[$com_key][$l_id][$date_key]['operator'];
												$gr_tot_helper += $prod_resource_array[$com_key][$l_id][$date_key]['helper'];
												$gr_tot_man_power +=$prod_resource_array[$com_key][$l_id][$date_key]['man_power'];
												$gr_tot_act_days += $active_days;
												$gr_tot_cap_pcs += $prod_resource_array[$com_key][$l_id][$date_key]['capacity'];
												$gr_tot_plan_wo_hour += $prod_resource_array[$com_key][$l_id][$date_key]['working_hour'];
												$gr_tot_prod_hour += $difference_hour;
												$gr_tot_hour_trg_pcs += $prod_resource_array[$com_key][$l_id][$date_key]['terget_hour'];
												$gr_tot_cur_hour += $difference_hour;
												$gr_tot_as_on_cur_hour_trg_pcs += $current_targrt;
												$gr_tot_hour_prod_varience += $varience_as_on_cur_hr;
												$gr_tot_target += $prod_resource_array[$com_key][$l_id][$date_key]['tpd'];
												$gr_tot_gen_prod += $general_prod_qty;
												$gr_tot_ot_prod += $ot_prod_qty;
												$gr_tot_prod += $tot_prod_qty;
												$gr_tot_varience += $total_varience;
												$gr_tot_avl_min += $available_min;
												$gr_tot_npt_min += $npt_min;
												$gr_tot_man_min_used += $man_min_used;
												$gr_tot_gen_prod_min += $general_prod_min;
												$gr_tot_ot_prod_min += $ot_prod_min;
												$gr_tot_prod_min += $tot_produce_minit;
												$gr_tot_target_hit += $target_hit;
												$gr_tot_target_effi += $gsd_efficiency;
												$gr_tot_achv_effi += $achive_effi;
												$gr_tot_effi_gap += $efficiency_gap;
												$gr_tot_line_effi += $line_efficiency;
												$gr_tot_style_cng += $style_change;
												$gr_tot_cm_pcs += $avg_cm;
												$gr_tot_cm += $ttl_cm;
												$gr_tot_cm_earn_bdt += $ttl_cm_earn_bdt;
												$gr_tot_target_cm += $target_cm;
												$gr_tot_fob_val += $tot_fob_val;
												$gr_tot_target_fob_val += $target_fob_val;
												$gr_tot_bep_unit += $bep_units;
												$gr_tot_earn_val_fob_usd += $earn_val_usd;
												$gr_tot_earn_val_fob_bdt += $earn_val_bdt;
												$gr_tot_line_cost += $line_cost_bdt;
												$gr_tot_line_profit += $line_profit;
											}
										}
									}
									?>
									<tr style="text-align: right;font-weight:bold;background:#dccddc;">
										<td width="30"></td>
										<td width="60"></td>
										<td width="120"></td>
										<td width="120"></td>
										<td width="80"></td>
										<td width="120"></td>
										<td width="120"></td>
										<td width="120"></td>
										<td width="120"></td>
										<td width="120"></td>
										<td width="120"></td>
										<td width="120">Floor Wise Total</td>
										<td width="80"><?=number_format(($flr_tot_smv/$floor_wise_tot_line[$com_key][$date_key][$flr_id]),2);?></td>
										<!-- <td width="80"><?=number_format(($flr_tot_avg_smv/$floor_wise_tot_line[$com_key][$date_key][$flr_id]),2);?></td> -->
										<td width="80"><?=number_format($flr_tot_operator,0);?></td>
										<td width="80"><?=number_format($flr_tot_helper,0);?></td>
										<td width="80"><?=number_format($flr_tot_man_power,0);?></td>
										<td width="80"><?=number_format($flr_tot_act_days,0);?></td>
										<td width="80"><?=number_format($flr_tot_cap_pcs,0);?></td>
										<td width="80"><?=number_format($flr_tot_plan_wo_hour,0);?></td>
										<td width="80"><?=number_format($flr_tot_prod_hour,0);?></td>
										<td width="80"><?=number_format($flr_tot_hour_trg_pcs,0);?></td>
										<td width="80"><?=number_format($flr_tot_cur_hour,0);?></td>
										<!-- <td width="80"><?=number_format($flr_tot_as_on_cur_hour_trg_pcs,0);?></td> -->
										<td width="80"><?=number_format($flr_tot_hour_prod_varience,0);?></td>
										<td width="80"><?=number_format($flr_tot_target,0);?></td>
										<td width="80"><?=number_format($flr_tot_gen_prod,0);?></td>
										<td width="80"><?=number_format($flr_tot_ot_prod,0);?></td>
										<td width="80"><?=number_format($flr_tot_prod,0);?></td>
										<td width="80"><?=number_format($flr_tot_varience,0);?></td>
										<td width="80"><?=number_format($flr_tot_avl_min,0);?></td>
										<td width="80"><?=number_format($flr_tot_npt_min,0);?></td>
										<td width="80"><?=number_format($flr_tot_man_min_used,0);?></td>
										<td width="80"><?=number_format($flr_tot_gen_prod_min,0);?></td>
										<td width="80"><?=number_format($flr_tot_ot_prod_min,0);?></td>
										<td width="80"><?=number_format($flr_tot_prod_min,0);?></td>
										<td width="80"><?=number_format(($flr_tot_target_hit/$floor_wise_tot_line[$com_key][$date_key][$flr_id]),2);?></td>
										<td width="80"><?=number_format(($flr_tot_target_effi/$floor_wise_tot_line[$com_key][$date_key][$flr_id]),2);?></td>
										<td width="80"><?=number_format(($flr_tot_achv_effi/$floor_wise_tot_line[$com_key][$date_key][$flr_id]),2);?></td>
										<td width="80"><?=number_format(($flr_tot_effi_gap/$floor_wise_tot_line[$com_key][$date_key][$flr_id]),2);?></td>
										<td width="80"><?=number_format(($flr_tot_line_effi/$floor_wise_tot_line[$com_key][$date_key][$flr_id]),2);?></td>
										<!-- <td width="80"><?=number_format($flr_tot_style_cng,0);?></td> -->
										<td width="80"><?=number_format($flr_tot_cm_pcs,2);?></td>
										<td width="80"><?=number_format($flr_tot_cm,2);?></td>
										<td width="80"><?=number_format($flr_tot_cm_earn_bdt,2);?></td>
										<!-- <td width="80"><?=number_format($flr_tot_target_cm,2);?></td> -->
										<!-- <td width="80"></td> -->
										<!-- <td width="80"><?=number_format($flr_tot_fob_val,2);?></td> -->
										<!-- <td width="80"><?=number_format($flr_tot_target_fob_val,2);?></td> -->
										<td width="80"><?=number_format($flr_tot_line_cost,2);?></td>
										<!-- <td width="80"><?=number_format($flr_tot_bep_unit,2);?></td> -->
										<!-- <td width="80"><?=number_format($flr_tot_earn_val_fob_usd,2);?></td> -->
										<!-- <td width="80"><?=number_format($flr_tot_earn_val_fob_bdt,2);?></td> -->
										<td width="80"><?=number_format($flr_tot_line_profit,2);?></td>
										<?
										for($k=$hour; $k<=$last_hour; $k++)
										{
											$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
											?>
											<td width="50" ><?=$floor_tot_array[$com_key][$flr_id][$prod_hour];?></td>
											<?
										}
										?>
									</tr>
									<?
								}
							}
							?>
						</tbody>
					</table>
				</div>
				<table class="rpt_table" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<tfoot>
						<tr>
							<th width="30"></th>
							<th width="60"></th>
							<th width="120"></th>
							<th width="120"></th>
							<th width="80"></th>
							<th width="120"></th>
							<th width="120"></th>
							<th width="120"></th>
							<th width="120"></th>
							<th width="120"></th>
							<th width="120"></th>
							<th width="120">Grand Total</th>
							<th width="80"><?=number_format(($gr_tot_smv/$gr_tot_line),2);?></th>
							<!-- <th width="80"><?=number_format(($gr_tot_avg_smv/$gr_tot_line),2);?></th> -->
							<th width="80"><?=number_format($gr_tot_operator,0);?></th>
							<th width="80"><?=number_format($gr_tot_helper,0);?></th>
							<th width="80"><?=number_format($gr_tot_man_power,0);?></th>
							<th width="80"><?=number_format($gr_tot_act_days,0);?></th>
							<th width="80"><?=number_format($gr_tot_cap_pcs,0);?></th>
							<th width="80"><?=number_format($gr_tot_plan_wo_hour,0);?></th>
							<th width="80"><?=number_format($gr_tot_prod_hour,0);?></th>
							<th width="80"><?=number_format($gr_tot_hour_trg_pcs,0);?></th>
							<th width="80"><?=number_format($gr_tot_cur_hour,0);?></th>
							<!-- <th width="80"><?=number_format($gr_tot_as_on_cur_hour_trg_pcs,0);?></th> -->
							<th width="80"><?=number_format($gr_tot_hour_prod_varience,0);?></th>
							<th width="80"><?=number_format($gr_tot_target,0);?></th>
							<th width="80"><?=number_format($gr_tot_gen_prod,0);?></th>
							<th width="80"><?=number_format($gr_tot_ot_prod,0);?></th>
							<th width="80"><?=number_format($gr_tot_prod,0);?></th>
							<th width="80"><?=number_format($gr_tot_varience,0);?></th>
							<th width="80"><?=number_format($gr_tot_avl_min,0);?></th>
							<th width="80"><?=number_format($gr_tot_npt_min,0);?></th>
							<th width="80"><?=number_format($gr_tot_man_min_used,0);?></th>
							<th width="80"><?=number_format($gr_tot_gen_prod_min,0);?></th>
							<th width="80"><?=number_format($gr_tot_ot_prod_min,0);?></th>
							<th width="80"><?=number_format($gr_tot_prod_min,0);?></th>
							<th width="80"><?=number_format(($gr_tot_target_hit/$gr_tot_line),2);?></th>
							<th width="80"><?=number_format(($gr_tot_target_effi/$gr_tot_line),2);?></th>
							<th width="80"><?=number_format(($gr_tot_achv_effi/$gr_tot_line),2);?></th>
							<th width="80"><?=number_format(($gr_tot_effi_gap/$gr_tot_line),2);?></th>
							<th width="80"><?=number_format(($gr_tot_line_effi/$gr_tot_line),2);?></th>
							<!-- <th width="80"><?=number_format($gr_tot_style_cng,0);?></th> -->
							<th width="80"><?=number_format($gr_tot_cm_pcs,2);?></th>
							<th width="80"><?=number_format($gr_tot_cm,2);?></th>
							<th width="80"><?=number_format($gr_tot_cm_earn_bdt,2);?></th>
							<!-- <th width="80"><?=number_format($gr_tot_target_cm,2);?></th> -->
							<!-- <th width="80"></th> -->
							<!-- <th width="80"><?=number_format($gr_tot_fob_val,2);?></th> -->
							<!-- <th width="80"><?=number_format($gr_tot_target_fob_val,2);?></th> -->
							<th width="80"><?=number_format($gr_tot_line_cost,2);?></th>
							<!-- <th width="80"><?=number_format($gr_tot_bep_unit,2);?></th> -->
							<!-- <th width="80"><?=number_format($gr_tot_earn_val_fob_usd,2);?></th> -->
							<!-- <th width="80"><?=number_format($gr_tot_earn_val_fob_bdt,2);?></th> -->
							<th width="80"><?=number_format($gr_tot_line_profit,2);?></th>
							<?
							for($k=$hour; $k<=$last_hour; $k++)
							{
								$prod_hour="prod_hour".substr($start_hour_arr[$k],0,2)."";
								?>
								<th width="50" ><?=$gr_tot_array[$com_key][$prod_hour];?></th>
								<?
							}
							?>
						</tr>
					</tfoot>
				</table>

			</fieldset>
			<br clear="all">
			<?
		} //end loop		
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

if($action=="open_avg_cm_popup")
{
	echo load_html_head_contents("Fabric Received Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$job_no =  "'".implode("','", explode(",",$job))."'";
	$pr_date = date('d-M-Y',$date);
	// echo $pr_date; die;
	$cm_arr = return_library_array("SELECT job_no, cm_cost from wo_pre_cost_dtls where status_active=1 and job_no in($job_no)","job_no","cm_cost");
	$costing_per_arr = return_library_array("SELECT job_no, costing_per from wo_pre_cost_mst where status_active=1 and job_no in($job_no)","job_no","costing_per");
	$sql = "SELECT a.job_no, a.style_ref_no,c.production_qnty from WO_PO_DETAILS_MASTER a,WO_PO_COLOR_SIZE_BREAKDOWN b,PRO_GARMENTS_PRODUCTION_DTLS c,PRO_GARMENTS_PRODUCTION_MST d where a.id=b.job_id and b.id=c.COLOR_SIZE_BREAK_DOWN_ID and c.mst_id=d.id and a.job_no in($job_no) and c.PRODUCTION_TYPE=5 and d.production_date='$pr_date' and d.sewing_line=$line_id and a.status_active=1 and a.is_deleted = 0 and b.status_active=1 and b.is_deleted = 0 and c.status_active=1 and c.is_deleted = 0 and d.status_active=1 and d.is_deleted = 0";  
	// echo $sql; die;
	$res = sql_select($sql);
	$data_array = array();
	foreach ($res as $v) 
	{
		$data_array[$v['JOB_NO']]['style'] = $v['STYLE_REF_NO'];
		$data_array[$v['JOB_NO']]['qty'] += $v['PRODUCTION_QNTY'];
	}
	?>
	<fieldset style="width:370px;">
		<div>
			<table border="1" class="rpt_table" rules="all" width="100%" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<th width="40%">Style</th>
						<th width="20%">CM Pcs</th>
						<th width="20%">Production</th>
						<th width="20%">Total CM</th>
					</tr>
				</thead>
				<tbody>
					<?
					$tot_prod = 0;
					$tot_cm = 0;
					foreach ($data_array as $job => $r) 
					{
						$costing_per=$costing_per_arr[$job];
						if($costing_per==1) $dzn_qnty=12;
						else if($costing_per==3) $dzn_qnty=12*2;
						else if($costing_per==4) $dzn_qnty=12*3;
						else if($costing_per==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;

						$cm_pcs = $r['qty']*($cm_arr[$job]/$dzn_qnty);
						?>
						<tr>
							<td><?=$r['style'];?></td>
							<td align="right"><?=number_format(($cm_arr[$job]/$dzn_qnty),2);?></td>
							<td align="right"><?=number_format($r['qty'],0);?></td>
							<td align="right"><?=number_format($cm_pcs,2);?></td>
						</tr>
						<?
						$tot_prod += $r['qty'];
						$tot_cm += $cm_pcs;
					}
					$avg_cm = $tot_cm / $tot_prod;
					?>
				</tbody>
				<tfoot>
					<tr>
						<th></th>
						<th>Total</th>
						<th><?=number_format($tot_prod,0);?></th>
						<th><?=number_format($tot_cm,2);?></th>
					</tr>
					<tr>
						<th></th>
						<th>Avg. CM</th>
						<th><?=number_format($avg_cm,2);?></th>
						<th></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
}
?>

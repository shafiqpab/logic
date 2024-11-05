<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$colorname_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

if ($action=="load_drop_down_buyer")
{
	
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   	 
} 
if ($action=="print_report_button_setting")
{
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id=59 and is_deleted=0 and status_active=1");
        echo $print_report_format; 	
} 
if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";
if ($action == "job_no_search_popup") 
{
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1, '', '', '');

	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array;
		var selected_name = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++)
			{
				$('#tr_' + i).trigger('click');
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value_job(str) {
			

			if (str != "")
				str = str.split("_");

			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

			if (jQuery.inArray(str[1], selected_id) == -1) {
				selected_id.push(str[1]);
				selected_name.push(str[2]);

			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == str[1])
						break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hide_job_id').val(id);
			$('#hide_job_no').val(name);
		}

	</script>

	</head>

	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:780px;">
				<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Company</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Job No</th>
						<th>Insert Date</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
						<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
						<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								echo create_drop_down("company_id", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0  order by company_name", "id,company_name", 1, "--Select--", $company, "", 0);
								?>
							</td>                 
							<td align="center">	
								<?
								// $search_by_arr = array(1 => "Job No", 2 => "Style Ref",3=> "Lot Ratio No");
								$search_by_arr = array(1 => "Job No", 2 => "Style Ref");
								$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>     
							<td align="center" id="search_by_td">				
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
							</td> 
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
							</td>	
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('company_id').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+'**'+<?echo $style; ?>, 'create_job_no_search_list_view', 'search_div', 'operation_tracking_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
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
if ($action == "create_job_no_search_list_view") 
{
	$data = explode('**', $data);
	$company_id = $data[0];
	$cbo_year = "";


	$company_con='';
	if(empty($company_id))
	{
		echo "Select Company First";die;
	}else{
		$company_con=" and b.company_name=$company_id";
	}

	$search_by = $data[1];
	$search_string = "'%" . trim($data[2]) . "%'";
	$search_field='';
	if(!empty($data[2]))
	{
		if ($search_by == 1)
			$search_field = " and b.job_no_prefix_num =$data[2]";
		else if ($search_by == 2)
			$search_field = " and b.style_ref_no like ".$search_string;
		else if($search_by == 3)
			$search_field = " and c.cut_num_prefix_no like ".$search_string;
	}
	$start_date = $data[3];
	$end_date = $data[4];
	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = " and b.insert_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
		} else {
			$date_cond = " and b.insert_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}
	$arr = array(0 => $company_arr, 1 => $buyer_short_library);
	if ($db_type == 0)
	{
		$year_field = "YEAR(b.insert_date) as year";
    	$year_cond = " and YEAR(b.insert_date) = $cbo_year ";
	}
	else if ($db_type == 2)
	{
		$year_field = "to_char(b.insert_date,'YYYY') as year";
    	$year_cond = " and to_char(b.insert_date,'YYYY') = $cbo_year ";
	}
	else
	{$year_field = "";
   	 $year_cond = "";
    } //defined Later
        	
	$sql = "SELECT  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num,b.company_name, b.buyer_name,$year_field 
		from wo_po_details_master b where b.status_active=1 and b.is_deleted=0 $company_con $date_cond   $search_field  order by job_no desc";
  	
	//  echo $sql;
	$conclick="id,job_no_prefix_num";
    echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "150,130,140,100", "760", "320", 0, $sql, "js_set_value_job", $conclick, "", 1, "company_name,buyer_name,0,0,0", $arr, "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "", '', '0,0,0,0,0,0,3', '',1);
    exit();
}
if($action=="cutting_number_popup")
{
  	echo load_html_head_contents("Yarn Lot Ratio No","../../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
		<script>
			function js_set_cutting_value(strCon ) 
			{
				
			document.getElementById('update_mst_id').value=strCon;
			// document.getElementById('txt_cut_no').value=strCon;
			parent.emailwindow.hide();
			}
	    </script>
	</head>
	<body>
	<div align="center" style="width:100%; overflow-y:hidden;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="950" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
			<thead>
				<tr>                	 
					<th width="140">Company name</th>
					<th width="130">System No</th>
					<th width="130">Style Ref.</th>
					<th width="130">Job No</th>
					<th width="130" style="display:none">Order No</th>
					<th width="250">Date Range</th>
					<th width="120"><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
				</tr>
			</thead>
			<tbody>
				<tr class="general">
					<td>
						<? 
						echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 order by company_name","id,company_name", 0, "-- Select Company --",$company_id, "",1);
						?>
					</td>
					<td align="center" >
						<input type="text" id="txt_cut_no" name="txt_cut_no" style="width:120px"  class="text_boxes_numeric"/>
						<input type="hidden" id="update_mst_id" name="update_mst_id" />
					</td>
					<td align="center">
						<input name="txt_style_search" id="txt_style_search" class="text_boxes" style="width:120px"  />
					</td>
					<td align="center">
						<input name="txt_job_search" id="txt_job_search" class="text_boxes_numeric" style="width:120px"  />
					</td>
					<td align="center" style="display:none">
							<input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:120px"  />
					</td>
	                        
					<td align="center" width="250">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" />
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date" />
					</td>
					<td align="center">
						<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style_search').value,'create_cutting_search_list_view', 'search_div','operation_tracking_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
					</td>
	            </tr>
				<tr> 
					<td align="center"  valign="middle" colspan="6">
	                    <? echo load_month_buttons(1);  ?>
					</td>
	            </tr>   
	        </tbody>
	    </table>
		<div align="center" valign="top" id="search_div"> </div>  
	</form>
	</div>    
	</body>           
	<script src="../../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_cutting_search_list_view")
{
    $ex_data = explode("_",$data);

	$company = $ex_data[0];	
	$cutting_no = $ex_data[1];
	$job_no = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$cut_year= $ex_data[5];
	$order_no= $ex_data[6];
	$style_serch_no= $ex_data[7];

    if($db_type==2) { $year_cond=" and extract(year from a.insert_date)=$cut_year"; $year=" extract(year from a.insert_date) as year ";}
    if($db_type==0) {$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cut_year"; $year=" SUBSTRING_INDEX(a.insert_date, '-', 1) as year ";}
	
	if(str_replace("'","",$company)==0) $conpany_cond=""; else $conpany_cond="and a.company_id=".str_replace("'","",$company)."";
	if(str_replace("'","",$cutting_no)=="") $cut_cond=""; else $cut_cond="and a.cut_num_prefix_no='".str_replace("'","",$cutting_no)."'  $year_cond";
	if(str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and b.job_no_prefix_num='".str_replace("'","",$job_no)."'";
	if(str_replace("'","",$style_serch_no)=="") $style_cond=""; else $style_cond="and b.style_ref_no  like '%".$style_serch_no."%' ";
	
	if( $from_date!="" && $to_date!="" )
	{
		if($db_type==0)
		{
			$sql_cond= " and entry_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		}
		if($db_type==2)
		{
			$sql_cond= " and entry_date  between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		}
	}
	

	
	$sql_order="SELECT a.id,a.cut_num_prefix_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.cad_marker_cons, a.marker_width, a.fabric_width,b.style_ref_no,c.color_id, c.marker_qty, c.order_cut_no,$year FROM ppl_cut_lay_mst a,wo_po_details_master b,ppl_cut_lay_dtls c where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  c.mst_id=a.id and a.job_no=b.job_no and a.entry_form=253 $conpany_cond $cut_cond $job_cond $sql_cond $style_cond order by id DESC";
	// echo $sql_order;die;
	$table_no_arr=return_library_array( "select id,table_no from lib_cutting_table",'id','table_no');
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	
	$arr=array(5=>$color_arr);//,4=>$order_number_arr,5=>$color_arr,Order NO,Color
	echo create_list_view("list_view", "System No,Year,Order Cut No,Job No,Style Ref.,Color,Ratio Qty,Cons/Dzn(Lbs),Entry Date","60,50,60,90,140,200,80,90,80","950","270",0, $sql_order , "js_set_cutting_value", "id,cut_num_prefix_no", "", 1, "0,0,0,0,0,color_id,0,0,0,0", $arr, "cut_num_prefix_no,year,order_cut_no,job_no,style_ref_no,color_id,marker_qty,cad_marker_cons,entry_date", "","setFilterGrid('list_view',-1)","0,0,0,0,0,0,1,2,3") ;
	exit();
}



if ($action == "bundle_qr_code_popup") 
{
    extract($_REQUEST);
    echo load_html_head_contents("Popup Info", "../../../../", 1, 1, $unicode);
    ?>
    <script>

        function check_all_data() {
            var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
            for (var i = 1; i <= tbl_row_count; i++) {
                if ($("#search" + i).css("display") != 'none') {
                    js_set_value(i);
                }
            }
        }
        var selected_id = new Array();

        function toggle(x, origColor) {
            var newColor = 'yellow';
            if (x.style) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
            }
        }

        function js_set_value(str) {
            toggle(document.getElementById('search' + str), '#FFFFCC');

            if (jQuery.inArray($('#txt_individual' + str).val(), selected_id) == -1) {
                selected_id.push($('#txt_individual' + str).val());

            }
            else {
                for (var i = 0; i < selected_id.length; i++) {
                    if (selected_id[i] == $('#txt_individual' + str).val()) break;
                }
                selected_id.splice(i, 1);
            }
            var id = '';
            for (var i = 0; i < selected_id.length; i++) {
                id += selected_id[i] + ',';
            }
            id = id.substr(0, id.length - 1);

            $('#hidden_bundle_nos').val(id);
        }

        function fnc_close() {
            parent.emailwindow.hide();
        }

        function reset_hide_field() {
            $('#hidden_bundle_nos').val('');
            selected_id = new Array();
        }

    </script>
    </head>
    <body>
    <div align="center" style="width:100%;">
        <form name="searchwofrm" id="searchwofrm">
            <fieldset style="width:1010px;">
                <legend>Enter search words <input type="checkbox" value="1" name="is_exact" id="is_exact" > is exact</legend>
                <table cellpadding="0" cellspacing="0" width="850" border="1" rules="all" class="rpt_table">
                    <thead>
                        <!-- <th>Cut Year</th> -->
                        <th>Lot ratio year</th>
                        <th>Job No</th>
                        <th>Buyer</th>
                        <th>Style ref.</th>
                        <th>Order No</th>
                        <!-- <th class="must_entry_caption">Cut No</th> -->
                        <th class="must_entry_caption">Lot ratio no</th>
                        <th>Bundle No</th>
                        <th>Qr code</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton"/>
                            <input type="hidden" name="hidden_bundle_nos" id="hidden_bundle_nos">
                            <input type="hidden" name="hidden_source_cond" id="hidden_source_cond"> 
                        </th>
                    </thead>
                    <tr class="general">
                        <td><? echo create_drop_down( "cbo_cut_year", 80, $year,'', "", '-- Select --',date("Y",time()), "",'','','','' ); ?></td>               
                        <td><input type="text" style="width:110px" class="text_boxes" name="txt_job_no" id="txt_job_no"/></td>
                        <td><? echo create_drop_down( "cbo_buyer_id", 110, "SELECT buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select Buyer --" ); ?></td>
                        <td><input type="text" style="width:110px" class="text_boxes" name="txt_style_ref" id="txt_style_ref"/></td>
                        <td id="search_by_td"><input type="text" style="width:100px" class="text_boxes" name="txt_order_no" id="txt_order_no"/></td>
                        <td><input type="text" name="txt_cut_no" id="txt_cut_no" style="width:100px" class="text_boxes"/></td>
                        <td><input type="text" name="bundle_no" id="bundle_no" style="width:100px" class="text_boxes"/> </td>
                        <td><input type="text" name="txt_qr_code" id="txt_qr_code" style="width:100px" class="text_boxes"/> </td>
                        <td>
                        <input type="button" name="button2" class="formbutton" value="Show"
                        onClick="show_list_view (document.getElementById('txt_order_no').value+'_'+'<? echo $company_id; ?>'+'_'+document.getElementById('bundle_no').value+'_'+'<? echo $bundleNo; ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('cbo_cut_year').value+'_'+$('#is_exact').is(':checked')+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_style_ref').value+'_'+document.getElementById('txt_qr_code').value, 'create_bundle_search_list_view', 'search_div', 'operation_tracking_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')"
                        style="width:100px;"/>
                        </td>
                    </tr>
                </table>
                <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
            </fieldset>
        </form>
    </div>
    </body>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action == "create_bundle_search_list_view") 
{
    $ex_data = explode("_", $data);
    $txt_order_no = "%" . trim($ex_data[0]) . "%";
    $company = $ex_data[1];
    //$bundle_no = "%".trim($ex_data[2])."%";
    if (trim($ex_data[2]))  $bundle_no = "" . trim($ex_data[2]) . ""; else $bundle_no = "%" . trim($ex_data[2]) . "%";

    $selectedBuldle = $ex_data[3];
    $job_no = $ex_data[4];
    $order_no =str_replace("'","", $ex_data[0]);
    $bndl_no =str_replace("'","", $ex_data[2]);
    $cut_no = $ex_data[5];
    $syear = substr($ex_data[6],2); 
    $is_exact=$ex_data[7];
    $cbo_buyer_id=$ex_data[8];
    $txt_style_ref=$ex_data[9];
    $txt_qr_code=$ex_data[10];

    // ============================ getting variable setting ===========================
    $input_data_source = 1;
    $input_data_source = return_field_value("production_entry", "variable_settings_production", "status_active=1 and is_deleted=0 and company_name=$company and variable_list=65");

    $production_type = 0;
    switch ($input_data_source) 
    {
        case 1:
            $production_type = " and a.production_type=52";
            break;
        case 2:
            $production_type = " and a.production_type=53";
            break;
        
        default:
            $production_type = " and a.production_type=54";
            break;
    }
    
    $company_short_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
    $cutConvertToInt = convertToInt('c.cut_no', array($company_short_arr[$company], '-'), 'cut_no');
    $bundleConvertToInt = convertToInt('c.bundle_no', array($company_short_arr[$company], '-', "/"), 'order_bundle_no');

    $size_arr = return_library_array("select id, size_name from lib_size", 'id', 'size_name');
    $color_arr = return_library_array("select id, color_name from lib_color", "id", "color_name");
    $country_arr = return_library_array("select id, country_name from lib_country", 'id', 'country_name');
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

   // $scanned_bundle_arr = return_library_array("select b.bundle_no, b.bundle_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=55 and  b.status_active=1 and b.is_deleted=0", 'bundle_no', 'bundle_no');
    $cutting_no =trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT));
    $where_con = '';
    if ($ex_data[2])  $where_con .= " and c.bundle_no like '%" . trim($ex_data[2]) . "'";

    if ($ex_data[0]) 
    {
        if($is_exact=='true') $where_con .= " and e.po_number='" . trim($ex_data[0]) . "'";
        else $where_con .= " and e.po_number like  '%" . trim($ex_data[0]) . "%'";    
    }
    $tmp_cut=trim($company_short_arr[$company].'-'.$syear.'-'.str_pad($cut_no,6,"0",STR_PAD_LEFT));
    if ($cut_no != '')
    {
        if($is_exact=='true')
        {
            $cutCon = " and c.cut_no = '$cut_no'";
            $cutCon_a = " and b.cut_no = '$cut_no'";
        }
        else
        {
            $cutCon = " and c.cut_no like '%".$cut_no."%'";
            $cutCon_a = " and b.cut_no like '%".$cut_no."%'";
        }
    }
    if($job_no!='')
    {
        if($is_exact=='true') $jobCon=" and f.job_no = '$job_no'"; else  $jobCon=" and f.job_no like '%$job_no%'";
    }
    $orderCon="";
    if($order_no)
    {
        if($is_exact=='true') $orderCon=" and e.po_number = '$order_no'"; else  $orderCon=" and e.po_number like '%$order_no%'";
    }
    $buyerCon="";
    if($cbo_buyer_id)
    {
        if($is_exact=='true') $buyerCon=" and f.buyer_name = '$cbo_buyer_id'"; else  $buyerCon=" and f.buyer_name like '%$cbo_buyer_id%'";
    }
    $styleCon="";
    if($txt_style_ref)
    {
        if($is_exact=='true') $styleCon=" and f.style_ref_no = '$txt_style_ref'"; else  $styleCon=" and f.style_ref_no like '%$txt_style_ref%'";
    }
    $qrCodeCon="";
    if($txt_qr_code)
    {
        if($is_exact=='true') $qrCodeCon=" and c.barcode_no = '$txt_qr_code'"; else  $qrCodeCon=" and c.barcode_no like '%$txt_qr_code%'";
    }

    $bndlCon="";
    if($bndl_no)
    {
        if($is_exact=='true') $bndlCon=" and c.bundle_no = '$bndl_no'"; else  $bndlCon=" and c.bundle_no like '%$bndl_no%'";
    }
    $year_cond="";
    if($syear) $year_cond .= " and c.cut_no like '%-$syear-%' ";
    
  // echo $tmp_cut;
   $scanned_bundle_arr = return_library_array("select bundle_no, bundle_no from pro_garments_production_dtls where production_type=55 and cut_no='".$tmp_cut."' and status_active=1 and is_deleted=0", 'bundle_no', 'bundle_no');
    foreach (explode(",", $selectedBuldle) as $bn) {
        $scanned_bundle_arr[$bn] = $bn;
    }

    $scanne=sql_select( "select b.bundle_no, sum(b.production_qnty) as production_qnty,a.sewing_line from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and b.production_type=55  and b.status_active=1 and b.is_deleted=0 $cutCon_a group by b.bundle_no,a.sewing_line");
    foreach($scanne as $row)
    {
        $duplicate_bundle[$row[csf("bundle_no")]] +=$row[csf("production_qnty")];
    }
    //print_r($scanned_bundle_arr);
    //$cutting_no=return_field_value("cut_no", "pro_garments_production_dtls", "barcode_no in ($bundle_nos)");
     
    // echo $cutting_no;
    $last_operation=gmt_production_validation_script( 55, 1,'', $cutting_no, $production_squence);
    //$last_operation=gmt_production_validation_script( 4, 1 );
    // print_r($last_operation);

    ?>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1030" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="40">Year</th>
            <th width="50">Job No</th>
            <th width="80">Buyer</th>
            <th width="80">Style ref.</th>
            <th width="90">Order No</th>
            <th width="130">Gmts Item</th>
            <th width="90">Country</th>
            <th width="100">Color</th>
            <th width="50">Size</th>
            <!-- <th width="70">Cut No</th> -->
            <th width="70">Lot ratio no</th>
            <th width="80">Bundle No</th>
            <th width="80">Qr code</th>
        </thead>
    </table>
    <div style="width:1050px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">    
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1030" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;                
			//echo $last_operation_string;
			$sql="SELECT c.cut_no,d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, sum(c.production_qnty) as qty, sum(c.replace_qty) as replace_qty, e.po_number,c.barcode_no, f.buyer_name,f.style_ref_no 
			from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e,wo_po_details_master f 
			where d.job_no_mst=f.job_no and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and a.company_id=$company $bndlCon $year_cond $jobCon $buyerCon $styleCon $orderCon $cutCon $qrCodeCon  and a.status_active=1 and a.is_deleted=0 $production_type 
			group by c.cut_no, c.bundle_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, e.po_number,c.barcode_no, f.buyer_name,f.style_ref_no 
			order by  c.cut_no,length(c.bundle_no) asc, c.bundle_no asc";
			
			//echo $sql;
			$result = sql_select($sql); 
			foreach ($result as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);   
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
					<td width="30"><? echo $i; ?>
							<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
					</td>
					<td width="40" align="center"><p><? echo $year; ?></p></td>
					<td width="50" align="center"><p><? echo $job*1; ?></p></td>
					<td width="80" style="word-break: break-all;"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
					<td width="80" style="word-break: break-all;"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<td width="90"><p><? echo $row[csf('po_number')]; ?></p></td>
					<td width="130"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
					<td width="90"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
					<td width="100"><p><? echo $color_arr[$row[csf('color_number_id')]]; ?></p></td>
					<td width="50"><p><? echo $size_arr[$row[csf('size_number_id')]]; ?></p></td>
					<td width="70"><? echo $row[csf('cut_no')]; ?></td>
					<td width="80"><? echo $row[csf('bundle_no')]; ?></td>
					<td width="80"><? echo $row[csf('barcode_no')]; ?></td>
				</tr>
				<?
				$i++;
				
			}
            
            ?>
        </table>
    </div>
    <table width="830">
        <tr>
            <td align="center" >
               <span  style="float:left;"> <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All</span>
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
    <?
    exit();
}



if($action=="operation_qr_code_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	list($shortName,$ryear,$lot_prifix)=explode('-',$lot_ratio);
	if($ryear=="") $ryear=date("Y",time()); else $ryear=("20$ryear")*1;
	//echo $company_id;die;
	?>
	<script>
		var selected_id = new Array();
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str) 
		{
			var strs=str.split("__");

			toggle( document.getElementById( 'search' + strs[0] ), '#FFFFCC' );
			
			if( jQuery.inArray( strs[1], selected_id ) == -1 ) {
				selected_id.push( strs[1] );
				$('#hidden_lot_ratio').val( strs[1] );	
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == strs[1] ) break;
				}
				selected_id.splice( i, 1 );
				
				/*if(selected_id.length==0 && $('#hidden_lot_ratio_pre').val()=="")
					$('#hidden_lot_ratio').val('');*/

			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			$('#hidden_bundle_nos').val( id );
		}
		
		function fnc_close()
		{	
			//return;
			parent.emailwindow.hide();
			//alert($('#hidden_bundle_nos').val())
		}
		
		function reset_hide_field()
		{
			selected_id = new Array();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:810px;">
			<legend></legend>           
	            <table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
	                <thead>
	                	<th width="140">Company</th>
	                    <th width="60">Lot Ratio Year</th>
	                    <th width="90">Job No</th>                  
	                    <th width="90" class="must_entry_caption">Ratio No</th>
	                    <th width="90">Bundle No</th>
	                    <th>
	                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton" />
                            <input type="hidden" name="hidden_bundle_nos" id="hidden_bundle_nos"> 
	                    </th>
	                </thead>
	                <tr class="general">
	                	<td><? echo create_drop_down( "cbo_company_name",140, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1,"-- Select --", $company_id, "",0 ); ?></td>
	                    <td><? echo create_drop_down( "cbo_lot_year", 60, $year,'', "", '-- Select --',$ryear, "" ); ?></td>  				
	                    <td><input type="text" style="width:80px" class="text_boxes" name="txt_job_no" id="txt_job_no" />	</td> 				
	                    <td><input type="text" name="txt_lot_no" id="txt_lot_no" style="width:80px" value="<?php if($lot_prifix) echo $lot_prifix*1; ?>" class="text_boxes" /></td>
	                    <td>
                        	<input type="hidden" name="hidden_lot_ratio" value="<?php echo $lot_ratio; ?>" id="hidden_lot_ratio"  />
                        	<input type="hidden" name="hidden_lot_ratio_pre"  value="<?php echo $lot_ratio; ?>" id="hidden_lot_ratio_pre"  />
				            <input type="text" name="bundle_no" id="bundle_no" style="width:80px" class="text_boxes" />
	                    </td>  		
	            		<td>
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'_'+document.getElementById('bundle_no').value+'_'+'<? echo trim($ticketNo,','); ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_lot_no').value+'_'+document.getElementById('cbo_lot_year').value+'_'+'<? echo trim($lot_ratio,','); ?>','create_operation_qr_code_list_view','search_div','operation_tracking_report_controller','setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:70px;" />
	                     </td>
	                </tr>
	           </table>
	           <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		if($("#hidden_lot_ratio").val()!="")
		{
			show_list_view (document.getElementById('cbo_company_name').value+'_'+document.getElementById('bundle_no').value+'_'+'<? echo trim($bundleNo,','); ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_lot_no').value+'_'+document.getElementById('cbo_lot_year').value+'_'+'<? echo trim($lot_ratio,','); ?>','create_operation_qr_code_list_view','search_div','bundle_linking_operation_controller','setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')
		}
	</script>
	</html>
	<?
	exit();
}

if($action=="create_operation_qr_code_list_view")
{
 	$ex_data 				= explode("_",$data);
	$company 				= $ex_data[0];
	$selectedBuldle			="'".implode("','",explode(",",$ex_data[2]))."'";
	$job_no					=$ex_data[3];
	$lot_no					=$ex_data[4];
	$syear 					= substr($ex_data[5],2);
	$full_lot_no			=$ex_data[7];
	
	if(trim($ex_data[1])) $bundle_no_cond=" and c.bundle_no='".trim($ex_data[1])."'";

	
	$cutCon=''; $receiveCon=''; $cutCon='';
	if ($lot_no != '') $cutCon = " and c.cut_no like'%".$lot_no."%'";
    //if ($full_lot_no != '') $cutpCon = " and b.cut_no='".$full_lot_no."'";
	
	if($job_no!='') $jobCon=" and f.job_no like '%$job_no%'"; else $jobCon="";
	if(str_replace("'","",$selectedBuldle)!="") $selected_bundle_cond=" and b.barcode_no not in (".$selectedBuldle.")";
	
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1030" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="50">Year</th>
            <th width="50">Job No</th>
            <th width="90">Order No</th>
            <th width="120">Gmts Item</th>
            <th width="120">Country</th>
            <th width="120">Color</th>
            <th width="50">Size</th>
            <th width="90">Lot Ratio No</th>
            <th width="85">Bundle No</th>
            <th width="120">Operation</th>
            <th>Ticket No</th>
        </thead>
	</table>
	<div style=" width:1050px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1030" class="rpt_table" id="tbl_list_search">  
        	<?
			$i=1;
				
			$sql="SELECT a.floor_id, a.sewing_line, b.mst_barcode_no, b.operation_id, b.barcode_no, max(c.id) as prdid, c.cut_no as cutting_no, c.bundle_no, sum(c.production_qnty) as production_qnty, d.id, d.job_no_mst, d.country_id, d.color_number_id, d.item_number_id, d.size_number_id, e.id as order_id, e.po_number, f.buyer_name, f.style_ref_no 
			
			from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f, ppl_cut_lay_bundle_operation b where a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no and c.barcode_no=b.mst_barcode_no and a.production_type=55 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $selected_bundle_cond $jobCon $cutCon $bundle_no_cond 
	 		group by a.floor_id, a.sewing_line, b.mst_barcode_no, b.operation_id, b.barcode_no, c.cut_no, c.bundle_no, d.id, d.job_no_mst, d.country_id, d.color_number_id, d.item_number_id, d.size_number_id, e.id, e.po_number, f.buyer_name, f.style_ref_no order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";
			//echo $sql;
			$result = sql_select($sql);	
			foreach ($result as $val)
			{
				//$po_id_arr[$val[csf('po_break_down_id')]] 		=$val[csf('po_break_down_id')];
				$color_id_arr[$val[csf('color_number_id')]] 	=$val[csf('color_number_id')];
				$size_id_arr[$val[csf('size_number_id')]] 		=$val[csf('size_number_id')];
				$country_id_arr[$val[csf('country_id')]] 		=$val[csf('country_id')];
				$cutting_id_arr[$val[csf('mst_id')]] 			=$val[csf('mst_id')];
				$operation_id_arr[$val[csf('operation_id')]] 	=$val[csf('operation_id')];
				$bundle_no_arr[$val[csf('bundle_no')]] 			="'".$val[csf('bundle_no')]."'";
			}

			$size_arr=return_library_array( "select id, size_name from lib_size where id in (".implode(',', $size_id_arr).")",'id','size_name');
			$color_arr=return_library_array( "select id, color_name from lib_color where id in (".implode(',', $color_id_arr).")", "id", "color_name");
			$country_arr=return_library_array( "select id, country_name from lib_country where id in (".implode(',', $country_id_arr).")",'id','country_name');
			//$po_number_arr=return_library_array( "select id, po_number from wo_po_break_down where id in (".implode(',', $po_id_arr).")",'id','po_number');
			
			$operation_name_arr=return_library_array( "select id, operation_name from lib_sewing_operation_entry where id in (".implode(',', $operation_id_arr).")",'id','operation_name');
			
			
			if(count($result)==0) { echo "<h2 style='color:#D00; text-align:center;'>Linking Output Not Found. </h2>"; }

			foreach ($result as $row)
			{ 
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);
				?>
				<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" onClick="js_set_value('<?=$i.'__'.$row[csf('barcode_no')].'__'.$row[csf('cutting_no')]; ?>')"> 
					<td width="30" align="center"><?=$i; ?>
						<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
						<input type="hidden" name="txt_individual_name" id="txt_individual_name<?php echo $i; ?>" value="<?php echo $row[csf('cutting_no')]; ?>"/>
					</td>
					<td width="50" align="center"><?=$year; ?></td>
					<td width="50" align="center" title="<?=$row[csf('job_no_mst')]; ?>"><?=$job*1; ?></td>
					<td width="90" style="word-break:break-all"><?=$row[csf('po_number')]; ?></td>
					<td width="120" style="word-break:break-all"><?=$garments_item[$row[csf('item_number_id')]]; ?></td>
					<td width="120" style="word-break:break-all"><?=$country_arr[$row[csf('country_id')]]; ?></td>
					<td width="120" style="word-break:break-all"><?=$color_arr[$row[csf('color_number_id')]]; ?></td>
					<td width="50" style="word-break:break-all"><?=$size_arr[$row[csf('size_number_id')]]; ?></td>
					<td width="90" style="word-break:break-all"><?=$row[csf('cutting_no')]; ?></td>
					<td width="85" style="word-break:break-all"><?=$row[csf('bundle_no')]; ?></td>
					<td width="120" style="word-break:break-all"><?=$operation_name_arr[$row[csf('operation_id')]]; ?></td>
					<td style="word-break:break-all"><?=$row[csf('barcode_no')]; ?></td>
				</tr>
				<?
				$i++;
			}
        	?>
        </table>
    </div>
    <table width="1000">
        <tr>
            <td align="center" >
               <span style="float:left;"><input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();" />Check / Uncheck All </span>
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
	<?	
	exit();	
}

if($action=="generate_report")
{ 
	$process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	$operation_name_arr=return_library_array( "select id, operation_name from lib_sewing_operation_entry",'id','operation_name');
	$company_name = str_replace("'","",$cbo_company_name);
	$job_no = str_replace("'","",$txt_job_no);
	$job_id = str_replace("'","",$hidden_job_id);
	$cutting_no = str_replace("'","",$txt_cutting_no);
	$ratio_id = str_replace("'","",$hidden_ratio_id);
	$qr_code = (str_replace("'","",$txt_qr_code)!="") ? "'".implode("','", explode(",",str_replace("'","",$txt_qr_code)))."'" : "";
	$op_code = (str_replace("'","",$txt_op_code)!="") ? "'".implode("','", explode(",",str_replace("'","",$txt_op_code)))."'" : "";

	$sql_cond = "";
	$sql_cond .= ($company_name!=0) ? " and a.company_name=$company_name": "";
	$sql_cond .= ($job_no!="") ? " and a.job_no_prefix_num in($job_no)": "";
	$sql_cond .= ($cutting_no!="") ? " and b.cut_num_prefix_no in($cutting_no)": "";
	$sql_cond .= ($job_id!="") ? " and a.id in($job_id)": "";
	$sql_cond .= ($ratio_id!="") ? " and b.id in($ratio_id)": "";
	$sql_cond .= ($qr_code!="") ? " and d.barcode_no in($qr_code)": "";
	$sql_cond .= ($op_code!="") ? " and e.barcode_no in($op_code)": "";

	// ======================= main query ===============================
	$sql = "SELECT a.job_no,a.buyer_name,a.style_ref_no,to_char(a.insert_date,'YYYY') as job_year,d.order_id,c.gmt_item_id,c.color_id,d.size_id,d.size_qty,e.operation_id,b.cutting_no,d.bundle_no,d.barcode_no,e.barcode_no as ticket_no from wo_po_details_master a, ppl_cut_lay_mst b, ppl_cut_lay_dtls c,ppl_cut_lay_bundle d, ppl_cut_lay_bundle_operation e where a.job_no=b.job_no and b.id=c.mst_id and c.id=d.dtls_id and b.id=d.mst_id and e.dtls_id=c.id and e.mst_barcode_no=d.barcode_no and d.id=e.bundle_id and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 $sql_cond";
	// echo $sql;die;
	$res = sql_select($sql);
	$order_id_arr = array();
	$ticket_id_arr = array();
	foreach($res as $val)
	{
		$order_id_arr[$val['ORDER_ID']] = $val['ORDER_ID'];
		$ticket_id_arr[$val['TICKET_NO']] = $val['TICKET_NO'];
	}
	$po_id_cond = where_con_using_array($order_id_arr,0,"id");
	$ticket_cond = where_con_using_array($ticket_id_arr,1,"ticket_no");

	$po_no_arr = return_library_array( "select id,po_number from wo_po_break_down where status_active=1 $po_ids_cond ", "id", "po_number"  );
	$ticket_qty_arr = return_library_array( "select ticket_no,qc_pass_qty from pro_linking_operation_dtls where status_active=1 $ticket_cond ", "ticket_no", "qc_pass_qty"  );
	$tbl_width = 1490;
	?>
	<fieldset style="width:<?=$tbl_width;?>px;">
		<table  cellspacing="0" style="justify-content: center;text-align: center;width: <?=$tbl_width;?>px;" >
			<tr class="form_caption" style="border:none;justify-content: center;text-align: center;">
				<td colspan="17" align="center" style="border:none; font-size:16px; font-weight:bold">
				Company Name:<? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>                                
				</td>
			</tr>
			<tr style="border:none;justify-content: center;text-align: center;">
				<td colspan="17" align="center" style="border:none; font-size:16px; font-weight:bold">
				Operation Tracking Report                          
				</td>
			</tr>
		</table>
		<br />	
		<br>
		<table cellspacing="0" border="1" class="rpt_table" width="<?=$tbl_width;?>" rules="all" align="left">
			<thead>				
				<tr>
					<th width="30">Sl</th>
					<th width="80">Operation QR Code [TN] </th>
					<th width="80">Bundle QR Code</th>
					<th width="80">Bundle No</th>
					<th width="70" >Ratio No</th>
					<th width="120">Buyer</th>
					<th width="50">Job Year</th>
					<th width="80" >Job</th>
					<th width="120" >Style</th>                        
					<th width="120" >Order</th>
					<th width="120">GMT Item</th>
					<th width="120" >Gmts. Color</th>
					<th width="80" >Size</th>
					<th width="80" >Bundle Qty. (Pcs)</th>
					<th width="100" >Opetation Name</th>
					<th width="80" >Production Done</th>
					<th width="80" >Balance</th>
				</tr>
			</thead>
		</table>
		<div style="width:<?=$tbl_width+20;?>px; max-height:350px; overflow-y:auto;" >
			<table cellspacing="0" border="1" class="rpt_table"  width="<?=$tbl_width;?>" rules="all"   style="max-height: 400px;overflow-y: auto;overflow-x: hidden;"  id="scroll_body" align="left">
				<tbody>
					<?
					$i=1;
					$tot_qty = 0;
					$tot_prod_qty = 0;
					$tot_balance = 0;
					foreach($res as $val)
					{
						$balance = $val['SIZE_QTY'] - $ticket_qty_arr[$val['TICKET_NO']];
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor;?>" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" title="<?=$title;?>">					
							<td width="30"><?=$i;?></td>
							<td width="80"><?=$val['TICKET_NO'];?></td>
							<td width="80"><?=$val['BARCODE_NO'];?></td>
							<td width="80"><?=$val['BUNDLE_NO'];?></td>
							<td width="70"><?=$val['CUTTING_NO'];?></td>
							<td width="120"><?=$buyer_arr[$val['BUYER_NAME']];?></td>
							<td width="50" align="center"><?=$val['JOB_YEAR'];?></td>
							<td width="80"><?=$val['JOB_NO'];?></td>
							<td width="120"><p><?=$val['STYLE_REF_NO'];?></p></td>
							<td width="120"><p><?=$po_no_arr[$val['ORDER_ID']];?></p></td>
							<td width="120"><P><?=$garments_item[$val['GMT_ITEM_ID']];?></p></td>
							<td width="120"><P><?=$color_library[$val['COLOR_ID']];?></p></td>
							<td width="80"><?=$size_library[$val['SIZE_ID']];?></td>
							<td width="80" align="right"><?=$val['SIZE_QTY'];?></td>
							<td width="100"><p><?=$operation_name_arr[$val['OPERATION_ID']];?></p></td>
							<td width="80" align="right"><?=number_format($ticket_qty_arr[$val['TICKET_NO']],0);?></td>
							<td width="80" align="right"><?=number_format($balance,0);?></td>
						</tr>
						<?
						$i++;
						$tot_qty += $val['SIZE_QTY'];
						$tot_prod_qty += $ticket_qty_arr[$val['TICKET_NO']];
						$tot_balance += $balance;
					}
					?>
				</tbody>    
			</table>

		</div>
			<table cellspacing="0" border="1" class="rpt_table" width="<?=$tbl_width;?>" rules="all" align="left">
			<tfoot>				
				<tr>						
					<th width="30"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="70"></th>
					<th width="120"></th>
					<th width="50"></th>
					<th width="80"></th>
					<th width="120"></th>                        
					<th width="120"></th>
					<th width="120"></th>
					<th width="120">Total</th>
					<th width="80"></th>
					<th width="80" id="tot_qty"><?=number_format($tot_qty,0);?></th>
					<th width="100"></th>
					<th width="80" id="tot_prod"><?=number_format($tot_prod_qty,0);?></th>
					<th width="80" id="tot_balance"><?=number_format($tot_balance,0);?></th>
				</tr>
			</tfoot>
			</table>
		</div>

	</fieldset>
	
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
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit(); 
	
}
if($action=="job_popup")
{
	echo load_html_head_contents("Job Wise Info", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where file_type=1",'master_tble_id','image_location');
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	//echo $job_key."<br>".$sys_key."<br>".$color_key."<br>".$size_key;
	$sql_cond="";
	if(!empty($job_key))
	{
		$sql_cond.=" and A.JOB_NO='$job_key'";
	}
	
	$sql_job="SELECT C.ID,A.COMPANY_NAME,A.STYLE_REF_NO,A.JOB_NO,A.SEASON,A.STYLE_DESCRIPTION,C.PO_NUMBER,B.COLOR_NUMBER_ID,B.SIZE_NUMBER_ID,D.CUT_NO
		FROM WO_PO_DETAILS_MASTER A, WO_PO_COLOR_SIZE_BREAKDOWN B,WO_PO_BREAK_DOWN C,PRO_GARMENTS_PRODUCTION_DTLS D,PRO_GARMENTS_PRODUCTION_MST E 
			WHERE
				A.ID=B.JOB_ID			
				AND C.JOB_ID=A.ID
				AND C.ID=B.PO_BREAK_DOWN_ID
				AND B.ID=D.COLOR_SIZE_BREAK_DOWN_ID
				AND D.MST_ID=E.ID
				AND D.PRODUCTION_TYPE IN (50,51) 
				AND E.PRODUCTION_TYPE IN (50,51) 
				AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 
				AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 
				AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 
				AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 
				AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0
 				$sql_cond";
			 //echo $sql_job;
			 $job_result=sql_select($sql_job);
			 $job_data_arr=array();
			 foreach ($job_result as $row) 
			 {
				$job_data_arr[$row["JOB_NO"]][$row["ID"]]["STYLE"]=$row["STYLE_REF_NO"];
				$job_data_arr[$row["JOB_NO"]][$row["ID"]]["STYLE_DEC"]=$row["STYLE_DESCRIPTION"];
				$job_data_arr[$row["JOB_NO"]][$row["ID"]]["JOB"]=$row["JOB_NO"];
				$job_data_arr[$row["JOB_NO"]][$row["ID"]]["SEASON"]=$row["SEASON"];
				$job_data_arr[$row["JOB_NO"]][$row["ID"]]["COMPANY_NAME"]=$row["COMPANY_NAME"];
				$job_data_arr[$row["JOB_NO"]][$row["ID"]]["PO"]=$row["PO_NUMBER"];
				$all_cut_no[$row["CUT_NO"]]=$row["CUT_NO"];
				$all_job_no[$row["JOB_NO"]]=$row["JOB_NO"];
				$all_order_no[$row["ID"]]=$row["ID"];
			 }
			//  echo "<pre>";
			//  print_r($job_data_arr);
			$all_cut_in=where_con_using_array($all_cut_no,1,'A.CUTTING_NO');
			$all_job_in=where_con_using_array($all_job_no,1,'A.JOB_NO');
			$all_order_in=where_con_using_array($all_order_no,1,'C.ORDER_ID');
			$sql_lay="SELECT A.JOB_NO,A.CUTTING_NO,B.COLOR_ID,C.SIZE_ID,SUM(C.SIZE_QTY) AS SIZE_QTY,C.ORDER_ID 
			FROM PPL_CUT_LAY_MST A, PPL_CUT_LAY_DTLS B,PPL_CUT_LAY_BUNDLE C 
			WHERE 
			A.ID=B.MST_ID
			AND B.ID=C.DTLS_ID
			AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 
			AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 
			AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0
			$all_cut_in $all_job_in $all_order_in
			GROUP BY A.JOB_NO,A.CUTTING_NO,B.COLOR_ID,C.SIZE_ID,C.ORDER_ID ORDER BY C.SIZE_ID";
			//echo $sql_lay;
			$sql_lay_data=sql_select($sql_lay);
			$job_color_size_arr=array();
			foreach ($sql_lay_data as $row) 
			{
				$job_color_size_arr[$row["JOB_NO"]][$row["ORDER_ID"]][$row["COLOR_ID"]]["S_QTY"]=$row["SIZE_QTY"];
				$size_arr[$row["SIZE_ID"]]=$row["SIZE_ID"];
				$size_qty_arr[$row["ORDER_ID"]][$row["COLOR_ID"]][$row["SIZE_ID"]]["S_QTY"]=$row["SIZE_QTY"];
			}
			// echo "<pre>";
			// print_r($job_color_size_arr);
			$job_chk=array();
			$po_chk=array();
			$color_chk=array();
			$size_chk=array();
			foreach ($job_color_size_arr as $job_key => $order_data) 
				{
					foreach ($order_data as $order_key => $color_data) 
					{
						foreach ($color_data as $color_key => $size_data) 
						{
								$job_count[$job_key]++;
								$po_count[$job_key][$order_key]++;
								$color_count[$job_key][$order_key][$color_key]++;
								//$size_count[$job_key][$order_key][$color_key][$size_key]++;
						}
					}
				}
	?>
	<style>
		.center{text-align: center;}
	</style>
	<div  style="width:1050px" >
	<fieldset style="width:1040px">
		<table style="width:1040px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th rowspan="2" width="40">SL</th>
					<th rowspan="2" width="100">Company</th>
					<th rowspan="2" width="90">IMAGE</th>
					<th rowspan="2" width="80">JOB NO</th>
					<th rowspan="2" width="80">STYLE</th>
					<th rowspan="2" width="60">SEASON</th>
					<th rowspan="2" width="100">STYLE DESCRIPTION</th>
					<th rowspan="2" width="80">PO NO.</th>
					<th rowspan="2" width="80">GMTS. COLOR</th>
					<th colspan="<?=count($size_arr)+1;?>" >SIZE QTY</th>
				</tr>
				<tr>
					<? 
					foreach ($size_arr as $size_key => $row) 
					{
						?>
						<th ><?=$size_library[$size_key];?></th>
						<?
					}		
					?>
					<th>Total</th>
				</tr>
			</thead>
			
			<tbody>
				<?
				$i=1;
				foreach ($job_color_size_arr as $job_key => $order_data) 
				{
					
					$total_size_qty=0;
					$total_row_size_qty=0;
					foreach ($order_data as $order_key => $color_data) 
					{
						foreach ($color_data as $color_key => $size_data) 
						{
								$job_rowspan=$job_count[$job_key];
								$po_rowspan=$po_count[$job_key][$order_key];
								$color_rowspan=$job_data_arr[$job_key][$order_key]["PO"];
								// echo $color_rowspan;
								?>
							<tr>
								<?
								if(!in_array($job_key,$job_chk))
								{
								$job_chk[]=$job_key;
								?>
								<td rowspan="<?=$job_rowspan?>" valign="middle" align="center"><?=$i;?></td>
								<td rowspan="<?=$job_rowspan?>" valign="middle" align="center"><?=$company_arr[$job_data_arr[$job_key][$order_key]["COMPANY_NAME"]];?></td>
								<td rowspan="<?=$job_rowspan?>" align="center">
									<img src='../../../../../<? echo $imge_arr[$job_data_arr[$job_key][$order_key]["JOB"]];?>' height='60' width="80" />
								</td>
								<td rowspan="<?=$job_rowspan?>" valign="middle" align="center">
									<?=$job_data_arr[$job_key][$order_key]["JOB"];?>
								</td>
								<td rowspan="<?=$job_rowspan?>" valign="middle" align="center"><?=$job_data_arr[$job_key][$order_key]["STYLE"];?></td>
								<td rowspan="<?=$job_rowspan?>" valign="middle" align="center"><?=$job_data_arr[$job_key][$order_key]["SEASON"];?></td>
								<td rowspan="<?=$job_rowspan?>" valign="middle" align="center"><?=$job_data_arr[$job_key][$order_key]["STYLE_DEC"];?></td>
								<?}?>
								<?
								if(!in_array($order_key,$po_chk))
								{
									$po_chk[]=$order_key;
									
							
								?>
								<td rowspan="<?=$po_rowspan;?>" valign="middle" align="center"><?=$job_data_arr[$job_key][$order_key]["PO"]?></td>
								<?
								}
								
								// if(!in_array($color_key,$color_chk))
								// {
								// 	$color_chk[]=$color_key;
								// 	?>
								 	<!-- <td rowspan="<?=$color_rowspan?>" valign="middle" align="center"><?=$color_library[$color_key];?></td> -->
									 <td  valign="middle" align="center"><?=$color_library[$color_key];?></td>
								 	<?
								// }
								$total_sizw_qty=0;
								foreach ($size_arr as $size_key => $row) 
									{
										
										?>
										<td valign="middle" style="text-align:right;"><?
										$size_qty=$size_qty_arr[$order_key][$color_key][$size_key]["S_QTY"];
										echo $size_qty;
										?></td>
										<?
										$total_sizw_qty+=$size_qty;
										$row_total_size_qty[$size_key]+=$size_qty;
										
									}
								?>
								<td valign="middle" style="text-align:right;"><strong><?=$total_sizw_qty;?></strong></td>
							</tr>
							<?

						}
						// $total_size_qty+=$size_qty;	
						// $total_row_size_qty+=$row_total_size_qty;
					}

					
				}
				?>
				<tr>
					<td colspan="9" style="text-align:right;"><strong>Total</strong></td>
					<?
					$total_row_size_qty=0;
					foreach ($size_arr as $size_key => $row) 
						{
							
							?>
							<td valign="middle" style="text-align:right;"><strong>
							<? echo $row_total_size_qty[$size_key];?></strong></td>
							<?
							$total_row_size_qty+=$row_total_size_qty[$size_key];
						}	

					?>
					<td style="text-align:right;"><strong><?=$total_row_size_qty?></strong></td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	</div>
	<?
	exit();
}
if($action=="issue_popup")
{
	//echo load_html_head_contents("Item Details Info", "../../../../../", 1, 1, '', '', '');
	echo load_html_head_contents("Knitting Issue", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	list($job_no,$cut_no,$color_id,$size_id,$company,$date_from,$date_to)=explode("**",$search_string);

	//echo "Job".$job_no."<br>".$cut_No."<br>".$color_key."<br>".$size_key."<br>";die();
	$sql_cond="";
	if(!empty($job_no))
	{
		$sql_cond.=" and A.JOB_NO='$job_no'";
	}
	if(!empty($cut_No))
	{
		$sql_cond.=" and D.CUT_NO='$cut_No'";
	}
	if(!empty($color_id))
	{
		$sql_cond.=" and B.COLOR_NUMBER_ID='$color_id'";
	}
	if(!empty($size_id))
	{
		$sql_cond.=" and B.SIZE_NUMBER_ID='$size_id'";
	}
	if(!empty($company))
	{
		$sql_cond.=" and A.COMPANY_NAME='$company'";
	}

	if(str_replace("'","",trim($date_from))!="" || str_replace("'","",trim($date_to))!="")$production_date=" AND E.PRODUCTION_DATE between '$date_from' and '$date_to'";
	else  $production_date="";

	//echo $sql_cond;

		$sql_job="SELECT C.ID,A.COMPANY_NAME,A.JOB_NO,B.COLOR_NUMBER_ID,B.SIZE_NUMBER_ID,D.PRODUCTION_QNTY,D.CUT_NO,E.PRODUCTION_DATE 
		FROM WO_PO_DETAILS_MASTER A, WO_PO_COLOR_SIZE_BREAKDOWN B,WO_PO_BREAK_DOWN C,PRO_GARMENTS_PRODUCTION_DTLS D,PRO_GARMENTS_PRODUCTION_MST E 
		WHERE
			A.ID=B.JOB_ID			
			AND C.JOB_ID=A.ID
			AND C.ID=B.PO_BREAK_DOWN_ID
			AND B.ID=D.COLOR_SIZE_BREAK_DOWN_ID
			AND D.MST_ID=E.ID
			AND E.PO_BREAK_DOWN_ID=C.ID
			AND D.PRODUCTION_TYPE IN (50) 
			AND E.PRODUCTION_TYPE IN (50) 
			AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 
			AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 
			AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 
			AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 
			AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0
			$sql_cond $production_date";
			//echo $sql_job."<br>";
			 $job_result=sql_select($sql_job);
			 $job_data_arr=array();
			 foreach ($job_result as $row) 
			 {
				$job_data_arr[$row["JOB_NO"]][$row["COLOR_NUMBER_ID"]][$row["SIZE_NUMBER_ID"]][$row["PRODUCTION_DATE"]]["P_DATE"]=$row["PRODUCTION_DATE"];

				$job_data_arr[$row["JOB_NO"]][$row["COLOR_NUMBER_ID"]][$row["SIZE_NUMBER_ID"]][$row["PRODUCTION_DATE"]]["QTY"]+=$row["PRODUCTION_QNTY"];
				
			 }
	?>
	<style>
		.center{text-align: center;}
	</style>
	<div  style="width:360px" >
	<fieldset style="width:350px">
		<table style="width:350px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th colspan="4">Knitting Issue Pop </th>
				</tr>
				<tr>
					<th rowspan="2" width="80">Issue. Date</th>
					<th rowspan="2" width="100">Gmts Color</th>
					<th rowspan="2" width="60">Size</th>
					<th rowspan="2" width="100">Knitting Qty</th>
				</tr>
			</thead>
			<tbody>
				<? 
				foreach ($job_data_arr as $job_key => $color_data) 
				{
					foreach ($color_data as $color_key => $size_data) 
					{
						foreach ($size_data as $size_key => $date_data) 
						{
							$total_Qty=0;
							foreach ($date_data as $date_key => $row) 
							{
								?>
								<tr>
									<td><?=$date_key;?></td>
									<td><?=$colorname_arr[$color_key];?></td>
									<td><?=$size_library[$size_key];?></td>
									
									<td style="text-align:right;"><?=$row["QTY"];?></td>
								</tr>
								<?
								$total_Qty+=$row["QTY"];
							}
						}
					}
					
				}			
				
				?>
				<tr>
					<td colspan="3" style="text-align:right;"><strong>Total</strong></td>
					<td style="text-align:right;"><strong><?=$total_Qty;?></strong></td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	</div>
	<?
	exit();
}
if($action=="receive_popup")
{
	//echo load_html_head_contents("Item Details Info", "../../../../../", 1, 1, '', '', '');
	echo load_html_head_contents("Knitting Issue", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	list($job_no,$cut_no,$color_id,$size_id,$company,$date_from,$date_to)=explode("**",$search_string);

	//echo "Job".$job_no."<br>".$cut_No."<br>".$color_key."<br>".$size_key."<br>";die();
	$sql_cond="";
	if(!empty($job_no))
	{
		$sql_cond.=" and A.JOB_NO='$job_no'";
	}
	if(!empty($cut_No))
	{
		$sql_cond.=" and D.CUT_NO='$cut_No'";
	}
	if(!empty($color_id))
	{
		$sql_cond.=" and B.COLOR_NUMBER_ID='$color_id'";
	}
	if(!empty($size_id))
	{
		$sql_cond.=" and B.SIZE_NUMBER_ID='$size_id'";
	}
	if(!empty($company))
	{
		$sql_cond.=" and A.COMPANY_NAME='$company'";
	}

	if(str_replace("'","",trim($date_from))!="" || str_replace("'","",trim($date_to))!="")$production_date=" AND E.PRODUCTION_DATE between '$date_from' and '$date_to'";
	else  $production_date="";

	//echo $sql_cond;

		$sql="SELECT C.ID,A.COMPANY_NAME,A.JOB_NO,B.COLOR_NUMBER_ID,B.SIZE_NUMBER_ID,D.PRODUCTION_QNTY,D.CUT_NO,E.PRODUCTION_DATE 
		FROM WO_PO_DETAILS_MASTER A, WO_PO_COLOR_SIZE_BREAKDOWN B,WO_PO_BREAK_DOWN C,PRO_GARMENTS_PRODUCTION_DTLS D,PRO_GARMENTS_PRODUCTION_MST E 
		WHERE
			A.ID=B.JOB_ID			
			AND C.JOB_ID=A.ID
			AND C.ID=B.PO_BREAK_DOWN_ID
			AND B.ID=D.COLOR_SIZE_BREAK_DOWN_ID
			AND D.MST_ID=E.ID
			AND E.PO_BREAK_DOWN_ID=C.ID
			AND D.PRODUCTION_TYPE IN (51) 
			AND E.PRODUCTION_TYPE IN (51) 
			AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 
			AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 
			AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 
			AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 
			AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0
			$sql_cond $production_date";
		//echo $sql."<br>";
			 $job_result=sql_select($sql);
			 $job_data_arr=array();
			 foreach ($job_result as $row) 
			 {
				$job_data_arr[$row["JOB_NO"]][$row["COLOR_NUMBER_ID"]][$row["SIZE_NUMBER_ID"]][$row["PRODUCTION_DATE"]]["P_DATE"]=$row["PRODUCTION_DATE"];
				$job_data_arr[$row["JOB_NO"]][$row["COLOR_NUMBER_ID"]][$row["SIZE_NUMBER_ID"]][$row["PRODUCTION_DATE"]]["QTY"]+=$row["PRODUCTION_QNTY"];
				
			 }
	?>
	<style>
		.center{text-align: center;}
	</style>
	<div  style="width:360px" >
	<fieldset style="width:350px">
		<table style="width:350px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th colspan="4">Knitting Receive Pop</th>
				</tr>
				<tr>
					<th rowspan="2" width="80">Issue. Date</th>
					<th rowspan="2" width="100">Gmts Color</th>
					<th rowspan="2" width="60">Size</th>
					<th rowspan="2" width="100">Knitting Qty</th>
				</tr>
			</thead>
			<tbody>
				<? 
				foreach ($job_data_arr as $job_key => $color_data) 
				{
					foreach ($color_data as $color_key => $size_data) 
					{
						foreach ($size_data as $size_key => $date_data) 
						{
							$total_Qty=0;
							foreach ($date_data as $date_key => $row) 
							{
								?>
								<tr>
									<td><?=$date_key;?></td>
									<td><?=$colorname_arr[$color_key];?></td>
									<td><?=$size_library[$size_key];?></td>
									
									<td style="text-align:right;"><?=$row["QTY"];?></td>
								</tr>
								<?
								$total_Qty+=$row["QTY"];
							}
						}
					}
					
				}			
				
				?>
				<tr>
					<td colspan="3" style="text-align:right;"><strong>Total</strong></td>
					<td style="text-align:right;"><strong><?=$total_Qty;?></strong></td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	</div>
	<?
	exit();
}





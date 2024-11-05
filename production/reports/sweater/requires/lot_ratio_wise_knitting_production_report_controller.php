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
	echo load_html_head_contents("Yarn Lot Ratio No","../../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
		<script>
			//function js_set_value_job(str ) 
			//{
				//str = str.split("_");
				
			//document.getElementById('update_mst_id').value=strCon;
			//$('#hide_job_id').val(str[0]);
			//$('#hide_job_no').val(str[1]);
			// document.getElementById('txt_cut_no').value=strCon;
			//parent.emailwindow.hide();
			//}
	    </script>
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
	<div align="center" style="width:100%; overflow-y:hidden;" >
	<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:780px;">
				<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Company</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Job No</th>
						<th> Date</th>
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('company_id').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+'**'+<?echo $style; ?>, 'create_job_no_search_list_view', 'search_div', 'lot_ratio_wise_knitting_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	<script src="../../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
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
		//else if($search_by == 3)
			//$search_field = " and c.cut_num_prefix_no like ".$search_string;
	}
	$start_date = $data[3];
	$end_date = $data[4];
	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = " and a.shipment_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
		} else {
			$date_cond = " and a.shipment_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}
	$arr = array(0 => $company_arr, 1 => $buyer_short_library);
	if ($db_type == 0)
	{
		$year_field = "YEAR(b.insert_date) as year";
    	$year_cond = " and YEAR(a.insert_date) = $cbo_year ";
	}
	else if ($db_type == 2)
	{
		$year_field = "to_char(b.insert_date,'YYYY') as year";
    	$year_cond = " and to_char(a.insert_date,'YYYY') = $cbo_year ";
	}
	else
	{$year_field = "";
   	 $year_cond = "";
    } //defined Later
    
  
  	if($search_by == 3)
  	{
  		$sql = "SELECT  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name,$year_field 
		    from wo_po_break_down a, wo_po_details_master b  where a.job_no_mst=b.job_no and  a.is_deleted=0 and a.status_active=1 and 
	        b.status_active=1 and b.is_deleted=0 $company_con $date_cond   $search_field  group by  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name,b.insert_date order by job_no";
  	}
  	else
  	{
  		$sql = "SELECT  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num,b.company_name, b.buyer_name,$year_field 
		    from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and  a.is_deleted=0 and a.status_active=1 and 
	        b.status_active=1 and b.is_deleted=0 $company_con $date_cond   $search_field  group by  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name,b.insert_date order by job_no";

  	}
	 //echo $sql;
	$conclick="id,job_no";
	 $style=$data[5];
	if($style==1)
	{
		$conclick="id,style_ref_no";
	}
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
						<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style_search').value,'create_cutting_search_list_view', 'search_div','lot_ratio_wise_knitting_production_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
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
	

	
	$sql_order="select a.id,a.cutting_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.cad_marker_cons, a.marker_width, a.fabric_width,b.style_ref_no,c.color_id, c.marker_qty, c.order_cut_no,$year FROM ppl_cut_lay_mst a,wo_po_details_master b,ppl_cut_lay_dtls c where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  c.mst_id=a.id and a.job_no=b.job_no and a.entry_form=253 $conpany_cond $cut_cond $job_cond $sql_cond $style_cond order by id DESC";
	//echo $sql_order;die;
	$table_no_arr=return_library_array( "select id,table_no from lib_cutting_table",'id','table_no');
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	
	$arr=array(5=>$color_arr);//,4=>$order_number_arr,5=>$color_arr,Order NO,Color
	echo create_list_view("list_view", "System No,Year,Order Cut No,Job No,Style Ref.,Color,Ratio Qty,Cons/Dzn(Lbs),Entry Date","60,50,60,90,140,200,80,90,80","950","270",0, $sql_order , "js_set_cutting_value", "id,cutting_no", "", 1, "0,0,0,0,0,color_id,0,0,0,0", $arr, "cutting_no,year,order_cut_no,job_no,style_ref_no,color_id,marker_qty,cad_marker_cons,entry_date", "","setFilterGrid('list_view',-1)","0,0,0,0,0,0,1,2,3") ;
	exit();
}
if($action=="generate_report")
{ 
	$process = array( &$_POST );
    // echo "<pre>";
    // print_r($process);
    // echo "</pre>";
    extract(check_magic_quote_gpc( $process ));

	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
	$supplier_arr=return_library_array( "select id,supplier_name from lib_supplier ",'id','supplier_name');
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	$job_cond_id=""; 
	$style_cond="";
	$order_cond="";
	$type=str_replace("'","",$type);
	$txt_style_no=str_replace("'","",$txt_style_no);
	echo $hidden_job_id;

   	if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and a.company_name=".str_replace("'","",$cbo_company_name)."";

   	if(str_replace("'","",$txt_cutting_no)=="") $cutting_no=""; else $cutting_no=" AND D.CUT_NO=$txt_cutting_no";

	if(str_replace("'","",$cbo_buyer_name)==0)  $buyer_name=""; else $buyer_name="and a.buyer_name=".str_replace("'","",$cbo_buyer_name)."";

	if(str_replace("'","",$hidden_job_id)!="")  $job_cond_id=where_con_using_array(explode(",", str_replace("'","",$hidden_job_id)),0,"b.id");

	else  if (str_replace("'","",$txt_job_no)=="") $job_cond_id=""; else $job_cond_id="AND A.JOB_NO=$txt_job_no";

	if ($txt_style_no=="") $style_cond=""; 
	else 
	{
		//$style_cond="and a.style_ref_no=$txt_style_no";
		$txt_style_arr=explode(",",$txt_style_no);
		foreach($txt_style_arr as $val)
		{
		$txt_style_no_arr[$val]=$val;
		}
		$style_cond=where_con_using_array($txt_style_no_arr,1,'a.style_ref_no');
	}
	$working_company_cond="";
	$company_cond="";

	if(!empty(str_replace("'","",$cbo_working_company)))
	{
		$working_company_cond=" and d.working_company_id=$cbo_working_company";
	}
	if(!empty($cbo_company_name))
	{
		$company_cond=" and d.company_id=$cbo_company_name ";
	}

	$company_id=str_replace("'","",trim($cbo_company_name));
	$date_from=str_replace("'","",trim($txt_date_from));
	$date_to=str_replace("'","",trim($txt_date_to));
	//echo $date_from."**".$date_to;
  	if($type==1)
  	{
		 $po_number_data=array();
		 $po_number_id=array();
			  if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $production_date="";
			  else $production_date=" AND E.PRODUCTION_DATE between $txt_date_from and $txt_date_to";

			 if ($db_type == 0)
			{
				$year_field = ",YEAR(a.insert_date) as year";
			}
			else if ($db_type == 2)
			{
				$year_field = ",to_char(a.insert_date,'YYYY') as year";
			}
			else
			{	
				$year_field = "";
		    } 

			$sql_job="SELECT A.ID as Job_id,A.BUYER_NAME,A.COMPANY_NAME,A.STYLE_REF_NO,A.JOB_NO,A.GAUGE,B.PCS_PACK AS YRN_LOT_QTY,SUM(D.PRODUCTION_QNTY) AS PRODUCTION_QNTY,D.CUT_NO,B.COLOR_NUMBER_ID,B.SIZE_NUMBER_ID,E.PRODUCTION_TYPE,B.ID,C.ID AS PO_ID 
			FROM WO_PO_DETAILS_MASTER A, WO_PO_COLOR_SIZE_BREAKDOWN B,WO_PO_BREAK_DOWN C,PRO_GARMENTS_PRODUCTION_DTLS D,PRO_GARMENTS_PRODUCTION_MST E 
			WHERE A.ID=B.JOB_ID AND C.JOB_ID=A.ID AND C.ID=B.PO_BREAK_DOWN_ID AND B.ID=D.COLOR_SIZE_BREAK_DOWN_ID AND D.MST_ID=E.ID AND E.PRODUCTION_TYPE IN (50,51) AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0  AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0
			$company_name $buyer_name $style_cond $production_date $cutting_no $job_cond_id
			group by A.BUYER_NAME, A.COMPANY_NAME, A.STYLE_REF_NO, A.JOB_NO, A.GAUGE, B.PCS_PACK ,D.CUT_NO, B.COLOR_NUMBER_ID, B.SIZE_NUMBER_ID, E.PRODUCTION_TYPE, B.ID, C.ID,A.job_quantity,A.ID  ";
			// echo $sql_job."<br>";

			 $sql_job_data=sql_select($sql_job);
			 $job_data_arr=array();
			 $job_data_arr2=array();
			 $color_size_chk_arr=array();
			 $po_id_chk_arr=array();
			 foreach ($sql_job_data as $row) 
			 {
				$job_data_arr[$row["JOB_NO"]]["BUYER"]=$row["BUYER_NAME"];
				$job_data_arr[$row["JOB_NO"]]["STYLE"]=$row["STYLE_REF_NO"];
				$job_data_arr[$row["JOB_NO"]]["JOB"]=$row["JOB_NO"];
				$job_data_arr[$row["JOB_NO"]]["GAUGE"]=$row["GAUGE"];
					if ($color_size_chk_arr[$row["ID"]]=="")
					{
						$color_size_chk_arr[$row["ID"]]=$row["ID"];
					}
					if ($po_id_chk_arr[$row["PO_ID"]]=="")
					{
						$po_id_chk_arr[$row["PO_ID"]]=$row["PO_ID"];
					}
				$job_data_arr2[$row["JOB_NO"]][$row["CUT_NO"]][$row["COLOR_NUMBER_ID"]][$row["SIZE_NUMBER_ID"]][$row["PRODUCTION_TYPE"]]=$row["PRODUCTION_QNTY"];

				$job_data_arr[$row["JOB_NO"]]["COMPANY_NAME"]=$row["COMPANY_NAME"];
				$job_data_arr[$row["JOB_NO"]]["CUT_NO"]=$row["CUT_NO"];
				$all_cut_no[$row["CUT_NO"]]=$row["CUT_NO"];
				$all_job_id[$row["JOB_ID"]]=$row["JOB_ID"];
				
			 }
			 $all_cut_in=where_con_using_array($all_cut_no,1,'A.CUTTING_NO');
			 $all_job_id=where_con_using_array($all_job_id,1,'A.ID');

			 $sql_qty="SELECT A.ID,A.JOB_NO,SUM(B.PO_QUANTITY) as ORDER_QTY,SUM(B.PLAN_CUT) as PLAN_CUT_QTY from WO_PO_DETAILS_MASTER A, WO_PO_BREAK_DOWN B Where A.ID=B.JOB_ID 
			 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0
			 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0
			 $all_job_id Group by A.id,A.JOB_NO";

			 //echo "<br>".$sql_qty;
			 $sql_job_qty=sql_select($sql_qty);
			 $job_wise_arr=array();
			 foreach ($sql_job_qty as $val)
			 {
				$job_wise_arr[$val["JOB_NO"]]["ORDER_QTY"]= $val["ORDER_QTY"];
				$job_wise_arr[$val["JOB_NO"]]["PLAN_CUT_QTY"]= $val["PLAN_CUT_QTY"];
			 }
			//echo "<pre>";
			// print_r($job_data_arr);
			// echo "</pre>";

			$sql_lay="SELECT A.JOB_NO,A.CUTTING_NO,A.CUT_NUM_PREFIX_NO,B.COLOR_ID,B.MARKER_QTY,B.ORDER_QTY,C.SIZE_ID,C.SIZE_QTY 
			FROM PPL_CUT_LAY_MST A, PPL_CUT_LAY_DTLS B,PPL_CUT_LAY_BUNDLE C
			WHERE 
			A.ID=B.MST_ID
			AND B.ID=C.DTLS_ID
			AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 
			AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 
			AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0
			$all_cut_in 
			";
			//echo $sql_lay;
			$sql_lay_data=sql_select($sql_lay);
			$job_color_size_arr=array();
			$job_no_arr=array();
			foreach ($sql_lay_data as $row) 
			{
				$job_color_size_arr[$row["JOB_NO"]][$row["CUTTING_NO"]][$row["COLOR_ID"]][$row["SIZE_ID"]]["lot_QTY"]+=$row["SIZE_QTY"];
			}
			?>
  		<fieldset style="width:1730px;">
        	   <table  cellspacing="0" style="justify-content: center;text-align: center;width: 1720px;" >
                    <!-- <tr class="form_caption" style="border:none;justify-content: center;text-align: center;">
                           <td colspan="14" align="center" style="border:none;font-size:14px; font-weight:bold" > Date Wise Knitting Production Report</td>
                    </tr> -->
                    <tr class="form_caption" style="border:none;justify-content: center;text-align: center;">
                           <td colspan="15" align="center" style="border:none; font-size:16px; font-weight:bold">
                            Company Name:<? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>                                
                           </td>
                     </tr>
                    <tr style="border:none;justify-content: center;text-align: center;">
                           <td colspan="15" align="center" style="border:none; font-size:16px; font-weight:bold">
                           Lot Roatio Wise Knitting Production Details                            
                           </td>
                     </tr>
                     <tr style="border:none;justify-content: center;text-align: center;">
                           <td colspan="15" align="center" style="border:none;font-size:12px; font-weight:bold">
                            <? echo "Production date ".change_date_format(str_replace("'","",$txt_date_from))." to ". change_date_format(str_replace("'","",$txt_date_to)) ;?>
                           </td>
                     </tr>
              </table>
             <br />	
             <br>
			 <table cellspacing="0" border="1" class="rpt_table" width="1720" rules="all">
             			<thead>
							
             				<tr >
		                       <th width="120" valign="middle">Buyer</th>
		                       <th width="120" valign="middle">Style Ref. </th>
		                       <th width="120" valign="middle">Job No </th>
		                       <th width="100" valign="middle">Gauge</th>
		                       <th width="70"  valign="middle">Order Qty </th>
		                       <th width="120" valign="middle">Plan Knit Qty</th>
		                       <th width="130" valign="middle">Y.Lot Ratio</th>
		                       <th width="80"  valign="middle">Gmts. Color</th>
		                       <th width="80" >Size</th>                        
		                       <th width="100" >Y.Lot Ratio Qty</th>
		                       <th width="115">Knitting Issue [Body]</th>
		                       <th width="100" >Knitting Issue [Body] Bal.</th>
		                       <th width="80" >Knitting Receive [Body]</th>
		                       <th width="80" >Knit.Rec [Body] Bal. Floor</th>
		                       <th width="80" >Knit. Bal</th>
		                    </tr>
             			</thead>
             		</table>
				<div style="width:1730px; " >
                    <table cellspacing="0" border="1" class="rpt_table"  width="1720" rules="all"   style="max-height: 400px;overflow-y: auto;overflow-x: hidden;"  id="scroll_body">
					  <tbody>
					<? 
						$job_chk=array();
						$sys_chk=array();
						$size_chk=array();
						$color_chk=array();
						foreach ($job_color_size_arr as $job_key => $sys_color_size_data) 
						{
						// echo "<pre>";
						// print_r($sys_color_size_data);
							
							foreach ($sys_color_size_data as $sys_key => $color_size_data) 
							{
								
								foreach ($color_size_data as $color_key => $size_data) 
								{
									
									foreach ($size_data as $size_key => $row)
									{
										$job_count[$job_key]++;
										$sys_count[$job_key][$sys_key]++;
										$color_count[$job_key][$sys_key][$color_key]++;
										$size_count[$job_key][$sys_key][$color_key][$size_key]++;
										
									}
								}
							}
						}
						foreach ($color_count as $job_key => $job_data) {
							foreach ($job_data as $sys_key => $sys_data) {
								foreach ($sys_data as $color_key => $row) {
									$job_count[$job_key]++;
								}
							}
						}
						foreach ($job_color_size_arr as $job_key => $sys_color_size_data) 
						{
							// echo "<pre>";
							// print_r($job_key);
							$total_order_qty=0;
							$total_plan_cut_qty=0;
							$style_total_ylotratio_Qty=0;
							$style_total_KnittingIssue=0;
							$style_total_yrn_kintIssuel=0;
							$style_total_knitting_receive=0;
							$style_total_knit_issue_rcv=0;
							$style_total_yrnLot_kniting_rcv=0;
							foreach ($sys_color_size_data as $sys_key => $color_size_data) 
							{
								foreach ($color_size_data as $color_key => $size_data) 
								{
									$total_KnittingIssue=0;
									$total_ylotratio_Qty=0;
									$total_yrn_kintIssuel=0;
									$total_knitting_receive=0;
									$total_knit_issue_rcv=0;
									$total_yrnLot_kniting_rcv=0;
									foreach ($size_data as $size_key => $row)
									{
										
										$cut_No=$job_data_arr[$job_key]["CUT_NO"];
										$job_no=$job_data_arr[$job_key]["JOB"];
										$search_string=$job_no."**".$sys_key."**".$color_key."**".$size_key."**".$company_id."**".$date_from."**".$date_to;

										$job_rowspan=$job_count[$job_key];

										$sys_rowspan=$sys_count[$job_key][$sys_key];

										$color_rowspan=$color_count[$job_key][$sys_key][$color_key];
										

										$KnittingIssue=$job_data_arr2[$job_key][$sys_key][$color_key][$size_key][50];
										
										//echo "<pre>";
										//print_r($job_data_arr2);
										//echo "</pre>";

										$knitting_receive=$job_data_arr2[$job_key][$sys_key][$color_key][$size_key][51];
										?>
										<tr>
										<?
										if(!in_array($job_key,$job_chk))
											{											
												$job_chk[]=$job_key;
												?>
												<td align="center" rowspan="<?=$job_rowspan;?>" width="120">
													<?=$buyer_arr[$job_data_arr[$job_key]["BUYER"]];?>
												</td>
												<td align="center" rowspan="<?=$job_rowspan;?>" width="120">
													<?=$job_data_arr[$job_key]["STYLE"];?>
												</td>
												<td align="center" rowspan="<?=$job_rowspan;?>" width="120">
													<a href='#report_details' onClick="openmypage_job('<?=$job_key?>','<?=$sys_key;?>','<?=$color_key?>','<?=$size_key?>')">
													<?=$job_no;
													?>
													</a>
												</td>
												<td align="center" rowspan="<?=$job_rowspan;?>" width="100">
													<?=$gauge_arr[$job_data_arr[$job_key]["GAUGE"]];?>
												</td>
												<td align="center" rowspan="<?=$job_rowspan;?>" width="70">
													<?
													$order_qty=$job_wise_arr[$job_key]["ORDER_QTY"];
													echo $order_qty;
													?>
												</td>
												<td align="center" rowspan="<?=$job_rowspan;?>" width="120">
													<?
													$plan_cut_qty=$job_wise_arr[$job_key]["PLAN_CUT_QTY"];
													echo $plan_cut_qty;
													?>
												</td>
												<?
												$total_order_qty+=$order_qty;
												$total_plan_cut_qty+=$plan_cut_qty;
											}
											if(!in_array($job_key."**".$sys_key,$sys_chk))
											{
												$sys_chk[]=$job_key."**".$sys_key;
												?>
												<td align="center" rowspan="<?=$sys_rowspan+1;?>" width="130"><?=$sys_key;?></td>
												
											<?
											}
											if (!in_array($job_key."**".$sys_key."**".$color_key,$color_chk)) {
												$color_chk[]=$job_key."**".$sys_key."**".$color_key;
												?>
												<td align="center" rowspan="<?=$color_rowspan+1;?>" width="80"><?=$color_library[$color_key];?></td>
												<?
											}
											?>										
											<td align="left" width="80"><?=$size_library[$size_key];?></td>
											<td style="text-align:right;" width="100"><?
											$ylotratio_Qty=$row["lot_QTY"];
											echo $ylotratio_Qty;?></td>
											<td style="text-align:right;" width="115" >
												<a href='#report_details' onClick="openmypage_issue('<?=$search_string?>')">
													<? 
													echo $KnittingIssue;
													?>
												</a>
											</td>
											<td style="text-align:right;" width="100">
												<?$yrn_kintIssue=$ylotratio_Qty-$KnittingIssue;
												echo $yrn_kintIssue;
												?>
											</td>
											<td style="text-align:right;" width="80">
												<a href='#report_details' onClick="openmypage_rcv('<?=$search_string?>')"><?echo $knitting_receive;?>
												</a>
											</td>
											<td style="text-align:right;" width="80">
												<? $knit_issue_rcv=$KnittingIssue-$knitting_receive;
												echo $knit_issue_rcv;
												?>
											</td>
											<td style="text-align:right;" width="80">
												<?$yrnLot_kniting_rcv=$ylotratio_Qty-$knitting_receive;
												echo $yrnLot_kniting_rcv;
												?>
											</td>
										</tr>
										<?
										$total_KnittingIssue+=$KnittingIssue;
										$total_ylotratio_Qty+=$ylotratio_Qty;
										$total_yrn_kintIssuel+=$yrn_kintIssue;
										$total_knitting_receive+=$knitting_receive;
										$total_knit_issue_rcv+=$knit_issue_rcv;
										$total_yrnLot_kniting_rcv+=$yrnLot_kniting_rcv;
									}						
								}
								?>
									<tr>
										<td style="text-align:right;"><strong>Total</strong></td>
										<td style="text-align:right;"><strong><?=$total_ylotratio_Qty;?></strong></td>
										<td style="text-align:right;"><strong><?=$total_KnittingIssue;?></strong></td>
										<td style="text-align:right;"><strong><?=$total_yrn_kintIssuel?></strong></td>
										<td style="text-align:right;"><strong><?=$total_knitting_receive?></strong></td>
										<td style="text-align:right;"><strong><?=$total_knit_issue_rcv?></strong></td>
										<td style="text-align:right;"><strong><?=$total_yrnLot_kniting_rcv?></strong></td>
									</tr>
								<?
								

								$style_total_ylotratio_Qty+=$total_ylotratio_Qty;
								$style_total_KnittingIssue+=$total_KnittingIssue;
								$style_total_yrn_kintIssuel+=$total_yrn_kintIssuel;
								$style_total_knitting_receive+=$total_knitting_receive;
								$style_total_knit_issue_rcv+=$total_knit_issue_rcv;
								$style_total_yrnLot_kniting_rcv+=$total_yrnLot_kniting_rcv;
							}

							?>
							<tr>
								<td colspan="4" style="text-align:right;"><strong>Style Total</strong></td>
								<td style="text-align:right;"><strong><?=$total_order_qty?></strong></td>
								<td style="text-align:right;"><strong><?=$total_plan_cut_qty?></strong></td>

								<td style="text-align:right;"><strong>&nbsp;</strong></td>
								<td style="text-align:right;"><strong>&nbsp;</strong></td>
								<td style="text-align:right;"><strong>&nbsp;</strong></td>
								<td style="text-align:right;">
									<strong>
									<?=$style_total_ylotratio_Qty?>
									</strong>
								</td>
								<td style="text-align:right;">
									<strong>
									<?=$style_total_KnittingIssue?>
									</strong>
								</td>
								<td style="text-align:right;"><strong><?=$style_total_yrn_kintIssuel;?></strong></td>
								<td style="text-align:right;"><strong><?=$style_total_knitting_receive;?></strong></td>
								<td style="text-align:right;"><strong><?=$style_total_knit_issue_rcv;?></strong></td>
								<td style="text-align:right;"><strong><?=$style_total_yrnLot_kniting_rcv;?></strong></td>
								
							</tr>
						<?
						}
					?>
	                    </tbody>    
	                </table>

				</div>
	                <table cellspacing="0" border="1" class="rpt_table" width="1700" rules="all">
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

	//echo "Job".$cut_no."<br>".$color_key."<br>".$size_key."<br>";die();
	$sql_cond="";
	if(!empty($job_no))
	{
		$sql_cond.=" and A.JOB_NO='$job_no'";
	}
	if(!empty($cut_no))
	{
		$sql_cond.=" and D.CUT_NO='$cut_no'";
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





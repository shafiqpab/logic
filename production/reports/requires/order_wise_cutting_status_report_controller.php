<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------


if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 80, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","");
	exit();
}

//item style------------------------------//
if($action=="style_wise_search")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$data=explode('_',$data);
	// $report_type=$data[3];
	// print_r($data);
	//echo $batch_type."AAZZZ";
	?>
	<script type="text/javascript">
	  function js_set_value(id)
		  {
			//alert(id);
			document.getElementById('selected_id').value=id;
			  parent.emailwindow.hide();
		  }
	</script>
	<input type="hidden" id="selected_id" name="selected_id" />
	<?
	if(str_replace("'","",$job_id)!="")  $job_cond="and a.id in(".str_replace("'","",$job_id).")";
	    else  if (str_replace("'","",$job_no)!="") $job_cond="and b.job_no_mst='".$job_no."'";
		if($buyer==0) $buyer_name=""; else $buyer_name="and a.buyer_name=$buyer";
		$job_year_cond="";
		if($cbo_year!=0)
		{
		if($db_type==0) $job_year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=".str_replace("'","",$cbo_year)." ";
	    if($db_type==2) $job_year_cond=" and extract( year from b.insert_date)=".str_replace("'","",$cbo_year)."";
		}
		if($db_type==0) $year_field="SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
		else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";

		if($db_type==2) $group_field="LISTAGG(CAST(b.po_number AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY b.po_number) as po_number";
		else if($db_type==0) $group_field="group_concat(distinct b.po_number ) as po_number";

		$sql="select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num as job_prefix,$year_field,$group_field from wo_po_details_master a,wo_po_break_down b where b.job_no_mst=a.job_no and a.company_name=$company  and a.is_deleted=0 group by  a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,a.insert_date ";
		// echo $sql;


	//$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

	?>
	<table width="500" border="1" rules="all" class="rpt_table">
		<thead>
	        <tr>
	            <th width="30">SL</th>
	             <th width="40">Year</th>
	             <th width="50">Job no</th>
	            <th width="100">Style</th>
	            <th width="">Po number</th>

	        </tr>
	   </thead>
	</table>
	<div style="max-height:300px; overflow:auto;">
	<table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
	 <? $rows=sql_select($sql);
		 $i=1;
		 foreach($rows as $data)
		 {
			 	if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$po_num=implode(",",array_unique(explode(",",$data[csf('po_number')])));
	  ?>
		<tr bgcolor="<? echo  $bgcolor;?>" onclick="js_set_value('<? echo $data[csf('id')]; ?>'+'_'+'<? echo $data[csf('style_ref_no')]; ?>')" style="cursor:pointer;">
			<td width="30"><? echo $i; ?></td>
	        <td align="center" width="40"><p><? echo $data[csf('year')]; ?></p></td>
			<td align="center"  width="50"><p><? echo $data[csf('job_prefix')]; ?></p></td>
			<td width="100"><p><? echo $data[csf('style_ref_no')]; ?></p></td>
	        <td width=""><p><? echo $po_num; ?></p></td>

		</tr>
	    <? $i++; } ?>
	</table>
	</div>
	<script> setFilterGrid("table_body2",-1); </script>
	<?
	disconnect($con);
	exit();
}
if($action=="job_popup")
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
			// $('#hide_job_no').val( name );
			 $('#hide_style_no').val( style );
			$("#hide_job_no").val(name);
			  parent.emailwindow.hide();
		}

    </script>
	</head>

	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:710px;">
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
									echo create_drop_down( "cbo_company_name", 130, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'order_wise_cutting_status_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
								?>
	                        </td>
	                        <td align="center" id="buyer_td">
	                        	 <?
									echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_name $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year').value, 'search_list_view', 'search_div', 'order_wise_cutting_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
	                    	</td>
	                    </tr>
	            	</tbody>
	           	</table>
	            <div style="margin-top:15px" id="search_div"></div>
			</fieldset>
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
	if($search_string!="")	{$search_cond=" and $search_field like '%$search_string%'";}
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
if($action=="color_popup")
{

	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
	<script type="text/javascript">
		function js_set_value(id)
		{
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" id="selected_id" name="selected_id" />
	<?
	$job_id=str_replace("'","",$txt_style_id);

	$job_id_arr = explode(",", $job_id);
	if(count($job_id_arr)>999 && $db_type==2)
    {
     	$po_chunk=array_chunk($job_id_arr, 999);
     	$job_ids_cond= "";
     	foreach($po_chunk as $vals)
     	{
     		$imp_ids=implode(",", $vals);
     		if($job_ids_cond=="")
     		{
     			$job_ids_cond.=" and ( b.id in ($imp_ids) ";
     		}
     		else
     		{
     			$job_ids_cond.=" or b.id in ($imp_ids) ";
     		}
     	}
     	 $job_ids_cond.=" )";
    }
    else
    {
     	$job_ids_cond= " and b.id in($job_id) ";
    }
	$sql="SELECT d.id,d.color_name from wo_po_break_down a, wo_po_details_master b,wo_po_color_size_breakdown c,lib_color d where a.job_id=b.id and a.id=c.po_break_down_id and b.id=c.job_id and d.id=c.color_number_id $job_ids_cond and a.status_active in(1,2,3) and b.status_active=1 and c.status_active=1  and d.status_active=1 group by d.id,d.color_name order by b.id desc";
	$arr=array(1=>$color_library);
	echo  create_list_view("list_view", "ID,Color Name", "50,200","300","300",0, $sql, "js_set_value", "id,color_name", "", 1, "0,0", $arr , "id,color_name", "",'setFilterGrid("list_view",-1)','0') ;

	exit();
}
if ($action == "order_wise_search")
 {
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById('list_view').rows.length;
			tbl_row_count = tbl_row_count - 0;
			for (var i = 1; i <= tbl_row_count; i++) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value(functionParam);
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value(strCon) {
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			if ($('#tr_' + str).css("display") != 'none') {
				toggle(document.getElementById('tr_' + str), '#FFFFCC');

				if (jQuery.inArray(selectID, selected_id) == -1) {
					selected_id.push(selectID);
					selected_name.push(selectDESC);
				} else {
					for (var i = 0; i < selected_id.length; i++) {
						if (selected_id[i] == selectID) break;
					}
					selected_id.splice(i, 1);
					selected_name.splice(i, 1);
				}
			}
			var id = '';
			var name = '';
			var job = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);
			$('#txt_selected_id').val(id);
			$('#txt_selected').val(name);
		}
	</script>
	<?
		$job_id=str_replace("'","",$txt_style_id);

		$job_id_arr = explode(",", $job_id);
		if(count($job_id_arr)>999 && $db_type==2)
		{
			$po_chunk=array_chunk($job_id_arr, 999);
			$job_ids_cond= "";
			foreach($po_chunk as $vals)
			{
				$imp_ids=implode(",", $vals);
				if($job_ids_cond=="")
				{
					$job_ids_cond.=" and ( b.id in ($imp_ids) ";
				}
				else
				{
					$job_ids_cond.=" or b.id in ($imp_ids) ";
				}
			}
			$job_ids_cond.=" )";
		}
		else
		{
			$job_ids_cond= " and b.id in($job_id) ";
		}


    $sql = "SELECT  a.id ,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num
    from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and  a.is_deleted=0 and a.status_active=1 and
    b.status_active=1 and b.is_deleted=0 $job_ids_cond order by job_no_mst";
	//  echo $sql;//die;
	echo create_list_view("list_view", "Order Number,Job No, Style Ref", "150,100,150", "550", "310", 0, $sql, "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_no_prefix_num,style_ref_no", "", "setFilterGrid('list_view',-1)", "0", "", 1);
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}
if($action=="cutting_lay_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	?>

<?
	 $job_cond_id = "and b.job_no_mst='" . str_replace("'", "", $job_id) . "'";
	 $prod_con .= ($po_id=="") ? "" : " and b.id=".$po_id;
	 $color_con .= ($color_id=="") ? "" : " and c.color_number_id=".$color_id;
	 $color_con1 .= ($color_id=="") ? "" : " and f.color_id=".$color_id;
	
	 $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	 $location_library=return_library_array( "select id,location_name from  lib_location", "id", "location_name" );
	 $color_library=return_library_array( "select id,color_name from lib_color ", "id", "color_name");
	 $size_library=return_library_array( "select id,size_name from lib_size ", "id", "size_name");
	 $table_no_library = return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");

		$cutting_popup_sql="SELECT b.id as po_id,b.job_no_mst as job_id, f.color_id as color_number_id,e.size_id as size_number_id,f.gmt_item_id as item_number_id,d.working_company_id,d.location_id,d.table_no,d.batch_id,d.entry_date,d.cutting_no,b.po_number,a.style_ref_no from wo_po_details_master a,wo_po_break_down b,ppl_cut_lay_mst d,ppl_cut_lay_bundle e,ppl_cut_lay_dtls f WHERE a.id=b.job_id  and b.job_no_mst=d.job_no and d.id=e.mst_id and b.id=e.order_id and d.id = f.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and f.status_active=1 and f.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $job_cond_id $prod_con $color_con1";

		$cutting_prod_popup_sql=sql_select($cutting_popup_sql);
		$cutting_popup_arr=array();
		$size_number_arr=array();
		$color_item_arr=array();


		foreach($cutting_prod_popup_sql as $row)
		{
			 $size_number_arr[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			 $cutting_popup_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('style_ref_no')]][$row[csf('working_company_id')]][$row[csf('cutting_no')]]['po_number']=$row[csf('po_number')];
			 $cutting_popup_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('style_ref_no')]][$row[csf('working_company_id')]][$row[csf('cutting_no')]]['location_id']=$row[csf('location_id')];
			 $cutting_popup_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('style_ref_no')]][$row[csf('working_company_id')]][$row[csf('cutting_no')]]['table_no']=$row[csf('table_no')];
			 $cutting_popup_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('style_ref_no')]][$row[csf('working_company_id')]][$row[csf('cutting_no')]]['batch_id']=$row[csf('batch_id')];
			 $cutting_popup_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('style_ref_no')]][$row[csf('working_company_id')]][$row[csf('cutting_no')]]['entry_date']=$row[csf('entry_date')];
			 $color_item_arr[$row[csf('style_ref_no')]][$row[csf('color_number_id')]][$row[csf('item_number_id')]]=$row[csf('color_number_id')];
			 $color_item_arr[$row[csf('style_ref_no')]][$row[csf('color_number_id')]][$row[csf('item_number_id')]]=$row[csf('item_number_id')];



		}
		//  echo '<pre>';print_r($color_item_arr); echo'</pre>';


	  	$order_qnty_sql="SELECT b.id as po_id,b.job_no_mst as job_id,c.color_number_id,c.size_number_id,c.item_number_id,c.order_quantity,c.plan_cut_qnty from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c WHERE a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 $job_cond_id $prod_con $color_con";

        $m_order_sql=sql_select($order_qnty_sql);
		$order_arr=array();
        foreach($m_order_sql as $row)
		{
			$order_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty']+=$row[csf('order_quantity')];
			$order_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['plan_qty']+=$row[csf('plan_cut_qnty')];
		}
		//  echo '<pre>';print_r($order_arr); echo'</pre>';

		$cutting_sql="SELECT b.job_no_mst as job_id,b.id as po_id,c.size_qty,c.size_id,f.gmt_item_id,d.cutting_no,f.color_id from wo_po_details_master a, wo_po_break_down b,ppl_cut_lay_bundle c,ppl_cut_lay_mst d,ppl_cut_lay_dtls f WHERE a.id=b.job_id and b.id=c.order_id and f.id=c.dtls_id  and b.job_no_mst=d.job_no and d.id=c.mst_id and d.id=f.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and f.status_active=1 and f.is_deleted=0 $job_cond_id $prod_con $color_con1";

		$c_sql=sql_select($cutting_sql);
		$cut_qnty_arr=array();



		foreach($c_sql as $row)
		{
			$cut_qnty_arr[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]][$row[csf('cutting_no')]]['size_qty']+=$row[csf('size_qty')];


		}
		//   echo '<pre>';print_r($size_total_arr); echo'</pre>';



		          $rowspan_arr=array();
		            foreach($cutting_popup_arr as $item_id=>$item_val)
				    {
						foreach($item_val as $color_id=>$color_val)
						{
							foreach($color_val as $style_ref=>$style_val)
							{
								foreach($style_val as $work_comp=>$work_val)
								{
									foreach($work_val as $cut_no=>$row)
									{

										$rowspan_arr[$item_id][$color_id][$style_ref]++;


									}
								}
							}
						}
				    }
					// $style_rowspan+=count($rowspan_arr);
					// echo $style_rowspan;
					$tbl_width=950+(count($size_number_arr)*60);

?>
 <br>

	   	<div style="width:100%" align="center" id="">
	   	<?
	   	$i=1;
		$size_total_arr=array();

		foreach($cutting_popup_arr as $item_id=>$item_val)
		{
			foreach($item_val as $color_id=>$color_val)
			{
				?>
				<table id="tbl_id" class="rpt_table" width="<?=$tbl_width;?>"  border="1" rules="all" >
					<thead>
						<tr>
							<th width="100">Style</th>
							<th width="150">Working Company</th>
							<th width="100">Working Location</th>
							<th width="100">Table</th>
							<th width="100">Batch</th>
							<th width="100">Date</th>
							<th width="100">Cut No</th>
							<th width="200">Color:<? echo $color_library[$color_id];?> Item:<? echo $garments_item[$item_id];?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td>Size</td>
							<?
							foreach ($size_number_arr as $skey => $v)
							{
								?>
								<td width="60" align="center"><?=$size_library[$skey];?></td>
								<?
							}
							?>
							<td width="100" align="center">Total</td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td>Order Qty</td>
							<?
							$total_order_qty=0;
							foreach ($size_number_arr as $skey => $v)
							{
								?>
								<td width="60" align="right"><?=$order_arr[$item_id][$color_id][$skey]['qty'];$total_order_qty+=$order_arr[$item_id][$color_id][$skey]['qty'];?></td>
								<?
							}
							?>
                           <td align="right"><? echo $total_order_qty; ?></td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td>Plan cut</td>
							<?
							$total_plan_qty=0;
							foreach ($size_number_arr as $skey => $v)
							{
								?>
								<td width="60" align="right"><?=$order_arr[$item_id][$color_id][$skey]['plan_qty']; $total_plan_qty+=$order_arr[$item_id][$color_id][$skey]['plan_qty'];?></td>
								<?
							}
							?>
							<td align="right"><?=$total_plan_qty;?></td>
						</tr>

					<?
					foreach($color_val as $style_ref=>$style_val)
					{
						$l=0;
						foreach($style_val as $work_comp=>$work_val)
						{
							foreach($work_val as $cut_no=>$row)
							{

								if ($i%2==0)
								$bgcolor="#E9F3FF";
								else
								$bgcolor="#FFFFFF";
								//  print_r($color_data);


								?>
								<tr bgcolor="<?=$bgcolor;?>">

									<?
									if($l==0)
									{
									?>
									<td align="center" valign="middle" rowspan="<? echo $rowspan_arr[$item_id][$color_id][$style_ref];?>"><p><? echo $style_ref."<br>".$row['po_number'];?></p></td>
									<?
									}
									?>
									<td><p><? echo $company_library[$work_comp]; ?></p></td>
									<td><p><? echo $location_library[$row['location_id']];?></p></td>
									<td><p><? echo $table_no_library[$row['table_no']];?></p></td>
									<td><p><? echo $row['batch_id'];?></p></td>
									<td><p><? echo $row['entry_date'];?></p></td>
									<td><p><? echo $cut_no;?></p></td>
									 <td></td>
									 <?
									 $total_cut_qnty=0;

									foreach ($size_number_arr as $skey => $v)
									{
										?>
										<td width="60" align="right"><?=$cut_qnty_arr[$item_id][$color_id][$skey][$cut_no]['size_qty'];$total_cut_qnty+=$cut_qnty_arr[$item_id][$color_id][$skey][$cut_no]['size_qty']; $size_total_arr[$item_id][$color_id][$skey]+=$cut_qnty_arr[$item_id][$color_id][$skey][$cut_no]['size_qty']; ?></td>
										<?
									}
									 ?>
									 <td align="right"><?=$total_cut_qnty;?></td>

								</tr>
								<?
								$i++;
								$l++;

								?>
                              <?
							}

						}

					}
                    ?>
				</tbody>
				<tfoot>
					<tr>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th>Cuttig Qty</th>
						<?
                        $gr_size_cut_qty=0;
						foreach ($size_number_arr as $skey => $v)
						{
							?>
							<th width="60" align="center"><? echo $size_total_arr[$item_id][$color_id][$skey];$gr_size_cut_qty+=$size_total_arr[$item_id][$color_id][$skey]; ?></th>
							<?
						}
						?>
						<th><?=$gr_size_cut_qty;?></th>

					</tr>
					<tr>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th>Cuttig Balance</th>
						<?
						$gr_cut_balance=0;
						foreach ($size_number_arr as $skey => $v)
						{
							?>
							<th width="60" align="center"><? $cut_balance=$order_arr[$item_id][$color_id][$skey]['plan_qty']- $size_total_arr[$item_id][$color_id][$skey]; echo $cut_balance;$gr_cut_balance+=$cut_balance; ?></th>
							<?
						}
						?>
						<th><?=$gr_cut_balance;?></th>
					</tr>
				</tfoot>
	        </table>
			<?
			}


		}
		?>
	</div>

<?
}

if($action=="report_generate")
{
    $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyerArr = return_library_array("select id,short_name from lib_buyer where status_active=1 and is_deleted=0","id","short_name");
	$color_library=return_library_array( "select id,color_name from lib_color ", "id", "color_name");
	$job_cond_id = "";
	$style_cond = "";
	$order_cond = "";
	$company_id = str_replace("'","",$cbo_company_name);
	$job_year=str_replace("'","",$cbo_year);
	$job_id =str_replace("'","",$txt_job_id);
	$hdn_color = str_replace("'","",$hdn_color);
	$txt_color = str_replace("'","",$txt_color);
	$sql_cond="";
	$sql_cond .= ($company_id!=0) ? " and a.company_name in($company_id)" : "";
	// $sql_cond .= ($buyer_name!=0) ? " and a.buyer_name in($buyer_name)" : "";
	if (str_replace("'", "", $cbo_buyer_name) == 0)  $buyer_name = "";
	else $buyer_name = "and a.buyer_name=" . str_replace("'", "", $cbo_buyer_name) . "";
	if (str_replace("'", "", $txt_job_no) == "") $job_cond_id = "";
	else $job_cond_id = "and a.job_no='" . str_replace("'", "", $txt_job_no) . "'";
	if (str_replace("'", "", $txt_order_no) == "") $order_cond = "";
	else $order_cond = "and b.po_number like '%" . str_replace("'", "", $txt_order_no) . "%' ";
	if (str_replace("'", "", $hidden_style_id) != "")  $style_cond = "and b.id in(" . str_replace("'", "", $hidden_style_id) . ")";
	else  if (str_replace("'", "", $txt_style_no) == "") $style_cond = "";
	else $style_cond = "and a.style_ref_no like '%" . str_replace("'", "", $txt_style_no) . "%' ";
	if($job_year!=0) $year_cond.=" and to_char(a.insert_date,'YYYY')='$job_year'";
	if($hdn_color!='') $color_cond="and e.color_number_id in($hdn_color)";else $color_cond="";
	if($hdn_color!='') $color_cond2="and f.color_id in($hdn_color)";else $color_cond2="";

    if($type==1)
    {
        $main_sql="SELECT a.buyer_name,a.style_ref_no,b.job_no_mst as job_id,b.id as po_id,e.order_quantity,e.plan_cut_qnty,b.po_number,e.color_number_id from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown e WHERE a.id=b.job_id and a.id=e.job_id and b.id=e.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $sql_cond $buyer_name  $job_cond_id  $order_cond $style_cond $year_cond  $color_cond ";

		 $m_sql=sql_select($main_sql);
		 $main_arr=array();

		 foreach($m_sql as $row)
		 {
             $main_arr[$row[csf('po_id')]][$row[csf('job_id')]][$row[csf('color_number_id')]]['buyer_name']=$row[csf('buyer_name')];
             $main_arr[$row[csf('po_id')]][$row[csf('job_id')]][$row[csf('color_number_id')]]['style_ref_no']=$row[csf('style_ref_no')];
             $main_arr[$row[csf('po_id')]][$row[csf('job_id')]][$row[csf('color_number_id')]]['po_quantity']+=$row[csf('order_quantity')];
             $main_arr[$row[csf('po_id')]][$row[csf('job_id')]][$row[csf('color_number_id')]]['plan_cut']+=$row[csf('plan_cut_qnty')];
             $main_arr[$row[csf('po_id')]][$row[csf('job_id')]][$row[csf('color_number_id')]]['po_number']=$row[csf('po_number')];

		 }

		//  echo '<pre>'; print_r($main_arr); echo'</pre>';

	   	 $cut_lay_sql="SELECT b.job_no_mst as job_id,b.id as po_id,c.size_qty,f.color_id from wo_po_details_master a, wo_po_break_down b,ppl_cut_lay_bundle c,ppl_cut_lay_dtls f WHERE a.id=b.job_id and b.id=c.order_id and f.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and f.status_active=1 and f.is_deleted=0 $sql_cond $buyer_name  $job_cond_id  $order_cond $style_cond $year_cond $color_cond2 ";

		 $c_sql=sql_select($cut_lay_sql);
         $cut_arr=array();
		 foreach($c_sql as $row)
		 {
            $cut_arr[$row[csf('po_id')]][$row[csf('job_id')]][$row[csf('color_id')]]['size_qty']+=$row[csf('size_qty')];
		 }

		//  echo '<pre>'; print_r($cut_arr); echo'</pre>';
     ?>
        <br>
		<div style="width:960px">
		<fieldset width="100%">
		<table class="rpt_table" width="940" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
				<tr>
					<th width="40">SL</th>
					<th width="100">Buyer Name</th>
					<th width="100">Style</th>
					<th width="100">Order No</th>
					<th width="100">Job No</th>
					<th width="100">Color</th>
					<th width="100">Order Qty</th>
					<th width="100">Plan Cut Qty</th>
					<th width="100">Cutting Lay</th>
					<th width="100">Cutting Balance</th>
				</tr>
			</thead>
			<tbody id="table_body_id">
				<?
					 $i=1;
					 foreach($main_arr as $po_id=>$po_val)
					 {
						foreach($po_val as $job_id=>$job_val)
						{
						  foreach($job_val as $color_id=>$row)
						  {	
								if ($i%2==0)
								$bgcolor="#E9F3FF";
								else
								$bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo  $bgcolor; ?>')" id="tr_<? echo $i; ?>">

								<td><? echo $i;?></td>
								<td><p><? echo $buyerArr[$row['buyer_name']];?></p></td>
								<td><p><? echo $row['style_ref_no'];?></p></td>
								<td><p><? echo $row['po_number'];?></p></td>
								<td><? echo $job_id;?></td>
								<td><? echo $color_library[$color_id];?></td>
								<td align="right"><? echo  $row['po_quantity']; $gr_po_qty+= $row['po_quantity'];?></td>
								<td align="right"><? echo  $row['plan_cut']; $gr_plan_cut+=$row['plan_cut'];?></td>
								<td align="right"><a href="##" onclick="openmypage_cutting_lay('<? echo  $po_id; ?>','<? echo $job_id; ?>','<? echo $color_id; ?>','cutting_lay_popup',1050,400)"><? echo $cut_arr[$po_id][$job_id][$color_id]['size_qty']; $gr_total_cut+=$cut_arr[$po_id][$job_id][$color_id]['size_qty']; ?></a></td>
								<td align="right"><? $cut_balance=$row['plan_cut']-$cut_arr[$po_id][$job_id][$color_id]['size_qty']; echo number_format($cut_balance,2); $gr_cut_balance+=$cut_balance; ?></td>
							</tr>
							<?
							$i++;
						  }
						}
					 }


               ?>
			</tbody>
			<tfoot>
				<th colspan="6">G Total</th>
				<th align="right"><? echo $gr_po_qty;?></th>
				<th align="right"><? echo $gr_plan_cut;?></th>
				<th align="right"><? echo $gr_total_cut; ?></th>
				<th align="right"><? echo $gr_cut_balance;?></th>
			</tfoot>

		</table>
		</fieldset>
		</div>



    <?

	}
}
?>
<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 order by a.buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );//load_drop_down( 'requires/daily_knitting_production_report_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_machine', 'machine_td' );$location_cond
  exit();	 
}
if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";
//item style------------------------------//


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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>', 'job_popup_search_list_view', 'search_div', 'daily_cutting_inhand_report_urmi_controller', 'setFilterGrid(\'table_body2\',-1)');" style="width:100px;" />
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
		$group_field="LISTAGG(CAST(b.po_number AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY b.po_number) as po_number";
		$year_field="to_char(a.insert_date,'YYYY')";
	} 
	else if($db_type==0) 
	{
		$group_field="group_concat(distinct b.po_number ) as po_number";
		$year_field="YEAR(a.insert_date)";
	}

	$arr=array (2=>$company_arr,3=>$buyer_arr);
	$sql= "select a.id, a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,$year_field as year , $group_field
	from wo_po_details_master a,  wo_po_break_down b 
	where a.job_no=b.job_no_mst and a.company_name=$company_id $buyer_cond $year_cond $search_con 
	group by a.id, a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,a.insert_date
	order by a.id";
	//echo $sql;//die;
	$rows=sql_select($sql);
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
     <? $rows=sql_select($sql);
         $i=1;
         foreach($rows as $data)
         {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$po_num=implode(",",array_unique(explode(",",$data[csf('po_number')])));
			?>
			<tr bgcolor="<? echo  $bgcolor;?>" onClick="js_set_value('<? echo $data[csf('id')]; ?>'+'_'+'<? echo $data[csf('job_no')]; ?>')" style="cursor:pointer;">
                <td width="30" align="center"><? echo $i; ?></td>
                <td width="120"><p><? echo $company_arr[$data[csf('company_name')]]; ?></p></td>
                <td width="120"><p><? echo $buyer_short_library[$data[csf('buyer_name')]]; ?></p></td>
                <td align="center" width="50"><p><? echo $data[csf('year')]; ?></p></td>
                <td width="120"><p><? echo $data[csf('job_no')]; ?></p></td>
                <td width="120"><p><? echo $data[csf('style_ref_no')]; ?></p></td>
                <td><p><? echo $po_num; ?></p></td>
			</tr>
			<? 
			$i++; 
		} 
		?>
    </table>
    </div>
    <?
	
	//echo $sql;
	//echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No", "70,70,120,100,100","570","230",0, $sql , "js_set_value", "year,job_no", "", 1, "0,0,company_name,buyer_name,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no", "","setFilterGrid('list_view',-1)",'0,0,0,0,0');
	//echo "<input type='hidden' id='hide_job_no' />";
	
	exit();
}

if($action=="job_wise_search")
{/*
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

	$sql="select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num as job_prefix,$year_field,$group_field from wo_po_details_master a,wo_po_break_down b where b.job_no_mst=a.job_no and a.company_name=$company $buyer_name $year_cond $job_cond and a.is_deleted=0 group by  a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,a.insert_date ";	


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
	<tr bgcolor="<? echo  $bgcolor;?>" onclick="js_set_value('<? echo $data[csf('id')]; ?>'+'_'+'<? echo $data[csf('job_no')]; ?>')" style="cursor:pointer;">
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
*/}//JobNumberShow


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
	
	$sql = "select distinct a.id,a.po_number,b.style_ref_no,b.job_no_prefix_num,$insert_year as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $company_name $job_cond  $buyer_name $style_cond";
	//echo $sql;//die;
	echo create_list_view("list_view", "Year,Job No,Style Ref,Order Number","50,100,120,150,","550","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "year,job_no_prefix_num,style_ref_no,po_number", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}

$colorname_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
$country_arr=return_library_array( "select id, country_name from   lib_country", "id", "country_name");


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
	if($job_po_id!="")
	{
		$order_cond.=" and a.po_break_down_id in($job_po_id)";
		$order_cond_lay.=" and c.order_id in($job_po_id)";
	}
	if($hidden_order_id!="")
	{
		$order_cond.=" and a.po_break_down_id in($hidden_order_id)";
		$order_cond_lay.=" and c.order_id in($hidden_order_id)";
	}
	
	
	
	$sql_lay=" select a.working_company_id, b.gmt_item_id, c.order_id, c.country_id, c.color_id, c.marker_qty as production_qnty  
	from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_size c
	where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=99 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.working_company_id=$cbo_work_company_name and entry_date=$txt_production_date $order_cond_lay ";
	
	$sql_lay_result=sql_select($sql_lay);
	$production_data=$porduction_ord_id=$lay_order_id=array();
	
	foreach($sql_lay_result as $row)
	{
		$porduction_ord_id[$row[csf("order_id")]]=$row[csf("order_id")];
		$lay_order_id[$row[csf("order_id")]]=$row[csf("order_id")];
		$production_data[$row[csf("order_id")]][$row[csf("gmt_item_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["lay_qnty"]+=$row[csf("production_qnty")];
	
	}
	
	$lay_order_id=implode(',',$lay_order_id);
	if($lay_order_id!="")
	{
		$sql_lay_prev=" select a.working_company_id, b.gmt_item_id, c.order_id, c.country_id, c.color_id, c.marker_qty as production_qnty  
		from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_size c
		where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=99 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.working_company_id=$cbo_work_company_name and a.entry_date<$txt_production_date and  c.order_id in($lay_order_id)";
		
		//echo $sql_lay_prev;die;
		
		$sql_lay_prev_result=sql_select($sql_lay_prev);
		foreach($sql_lay_prev_result as $row)
		{
			$production_data[$row[csf("order_id")]][$row[csf("gmt_item_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["lay_prev_qnty"]+=$row[csf("production_qnty")];
		}
	}
	
	
	
	$production_sql="select a.production_date, a.po_break_down_id as order_id, a.item_number_id, a.country_id, c.color_number_id as color_id,
	sum(CASE WHEN b.production_type =1 THEN b.production_qnty ELSE 0 END) AS cutting_qnty,
	sum(CASE WHEN b.production_type =4 THEN b.production_qnty ELSE 0 END) AS sewing_in_qnty,
	sum(CASE WHEN b.production_type =5 THEN b.production_qnty ELSE 0 END) AS sewing_out_qnty,
	sum(CASE WHEN b.production_type =7 THEN b.production_qnty ELSE 0 END) AS iron_qnty,
	sum(CASE WHEN b.production_type =8 THEN b.production_qnty ELSE 0 END) AS paking_finish_qnty,
	sum(CASE WHEN b.production_type =11 THEN b.production_qnty ELSE 0 END) AS poly_qnty 
	from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c 
	where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.production_source=1 and b.production_type in(1,4,5,7,8,11) and a.production_type in(1,4,5,7,8,11) and a.serving_company=$cbo_work_company_name and a.production_date=".$txt_production_date." $order_cond
	group by a.production_date, a.po_break_down_id, a.item_number_id, a.country_id, c.color_number_id";
	
	// echo $production_sql;die;
	
	$production_sql_result=sql_select($production_sql);
	foreach($production_sql_result as $row)
	{
		$porduction_ord_id[$row[csf("order_id")]]=$row[csf("order_id")];
		$gmt_order_id[$row[csf("order_id")]]=$row[csf("order_id")];
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["cutting_qnty"]+=$row[csf("cutting_qnty")];
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sewing_in_qnty"]+=$row[csf("sewing_in_qnty")];
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sewing_out_qnty"]+=$row[csf("sewing_out_qnty")];
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["iron_qnty"]+=$row[csf("iron_qnty")];
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["paking_finish_qnty"]+=$row[csf("paking_finish_qnty")];
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["poly_qnty"]+=$row[csf("poly_qnty")];
	}
	
	$gmt_order_id=implode(',',$gmt_order_id);
	if($gmt_order_id!="")
	{
		$production_prev_sql="select a.production_date, a.po_break_down_id as order_id, a.item_number_id, a.country_id, c.color_number_id as color_id,
		sum(CASE WHEN b.production_type =1 THEN b.production_qnty ELSE 0 END) AS cutting_prev_qnty,
		sum(CASE WHEN b.production_type =4 THEN b.production_qnty ELSE 0 END) AS sewing_in_prev_qnty,
		sum(CASE WHEN b.production_type =5 THEN b.production_qnty ELSE 0 END) AS sewing_out_prev_qnty,
		sum(CASE WHEN b.production_type =7 THEN b.production_qnty ELSE 0 END) AS iron_prev_qnty,
		sum(CASE WHEN b.production_type =8 THEN b.production_qnty ELSE 0 END) AS paking_finish_prev_qnty,
		sum(CASE WHEN b.production_type =11 THEN b.production_qnty ELSE 0 END) AS poly_prev_qnty 
		from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c 
		where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.production_source=1 and b.production_type in(1,4,5,7,8,11) and a.production_type in(1,4,5,7,8,11) and a.serving_company=$cbo_work_company_name and a.production_date<".$txt_production_date." and a.po_break_down_id in($gmt_order_id)
		group by a.production_date, a.po_break_down_id, a.item_number_id, a.country_id, c.color_number_id";
		
		// echo $production_sql;die;
		
		$production_prev_sql_result=sql_select($production_prev_sql);
		foreach($production_prev_sql_result as $row)
		{
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["cutting_prev_qnty"]+=$row[csf("cutting_prev_qnty")];
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sewing_in_prev_qnty"]+=$row[csf("sewing_in_prev_qnty")];
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sewing_out_prev_qnty"]+=$row[csf("sewing_out_prev_qnty")];
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["iron_prev_qnty"]+=$row[csf("iron_prev_qnty")];
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["paking_finish_prev_qnty"]+=$row[csf("paking_finish_prev_qnty")];
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["poly_prev_qnty"]+=$row[csf("poly_prev_qnty")];
		}
	}
	
	
	
	$print_embro_sql="select m.delivery_date, a.po_break_down_id as order_id, a.item_number_id, a.country_id, c.color_number_id as color_id,
	sum(CASE WHEN b.production_type =2 and a.embel_name=1 THEN b.production_qnty ELSE 0 END) AS printing_qnty,
	sum(CASE WHEN b.production_type =3 and a.embel_name=1 THEN b.production_qnty ELSE 0 END) AS printing_rcv_qnty,
	sum(CASE WHEN b.production_type =2 and a.embel_name=2 THEN b.production_qnty ELSE 0 END) AS embroidery_qnty,
	sum(CASE WHEN b.production_type =3 and a.embel_name=2 THEN b.production_qnty ELSE 0 END) AS embroidery_rcv_qnty,
	sum(CASE WHEN b.production_type =2 and a.embel_name=3 THEN b.production_qnty ELSE 0 END) AS wash_qnty,
	sum(CASE WHEN b.production_type =3 and a.embel_name=3 THEN b.production_qnty ELSE 0 END) AS wash_rcv_qnty,
	sum(CASE WHEN b.production_type =2 and a.embel_name=4 THEN b.production_qnty ELSE 0 END) AS sp_work_qnty,
	sum(CASE WHEN b.production_type =3 and a.embel_name=4 THEN b.production_qnty ELSE 0 END) AS sp_work_rcv_qnty
	from  pro_gmts_delivery_mst m, pro_garments_production_dtls b, pro_garments_production_mst a, wo_po_color_size_breakdown c 
	where m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0 and b.production_type in(2,3) and a.production_type in(2,3) and m.working_company_id=$cbo_work_company_name and m.delivery_date=".$txt_production_date." $order_cond
	group by m.delivery_date, a.po_break_down_id, a.item_number_id, a.country_id, c.color_number_id";
	
	//echo $print_embro_sql;die;
	
	$print_embro_sql_result=sql_select($print_embro_sql);
	$print_embro_order_id=array();
	foreach($print_embro_sql_result as $row)
	{
		$porduction_ord_id[$row[csf("order_id")]]=$row[csf("order_id")];
		$print_embro_order_id[$row[csf("order_id")]]=$row[csf("order_id")];
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["printing_qnty"]+=$row[csf("printing_qnty")];
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["printing_rcv_qnty"]+=$row[csf("printing_rcv_qnty")];
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["embroidery_qnty"]+=$row[csf("embroidery_qnty")];
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["embroidery_rcv_qnty"]+=$row[csf("embroidery_rcv_qnty")];
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["wash_qnty"]+=$row[csf("wash_qnty")];
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["wash_rcv_qnty"]+=$row[csf("wash_rcv_qnty")];
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["wash_qnty"]+=$row[csf("sp_work_qnty")];
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["wash_rcv_qnty"]+=$row[csf("sp_work_rcv_qnty")];
	}
	
	$print_embro_order_id=implode(',',$print_embro_order_id);
	if($print_embro_order_id!="")
	{
		$print_embro_sql="select m.delivery_date, a.po_break_down_id as order_id, a.item_number_id, a.country_id, c.color_number_id as color_id,
		sum(CASE WHEN b.production_type =2 and a.embel_name=1 THEN b.production_qnty ELSE 0 END) AS printing_prev_qnty,
		sum(CASE WHEN b.production_type =3 and a.embel_name=1 THEN b.production_qnty ELSE 0 END) AS printing_rcv_prev_qnty,
		sum(CASE WHEN b.production_type =2 and a.embel_name=2 THEN b.production_qnty ELSE 0 END) AS embroidery_prev_qnty,
		sum(CASE WHEN b.production_type =3 and a.embel_name=2 THEN b.production_qnty ELSE 0 END) AS embroidery_rcv_prev_qnty,
		sum(CASE WHEN b.production_type =2 and a.embel_name=3 THEN b.production_qnty ELSE 0 END) AS wash_prev_qnty,
		sum(CASE WHEN b.production_type =3 and a.embel_name=3 THEN b.production_qnty ELSE 0 END) AS wash_rcv_prev_qnty,
		sum(CASE WHEN b.production_type =2 and a.embel_name=4 THEN b.production_qnty ELSE 0 END) AS sp_work_prev_qnty,
		sum(CASE WHEN b.production_type =3 and a.embel_name=4 THEN b.production_qnty ELSE 0 END) AS sp_work_rcv_prev_qnty
		from  pro_gmts_delivery_mst m, pro_garments_production_dtls b, pro_garments_production_mst a, wo_po_color_size_breakdown c 
		where m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0 and b.production_type in(2,3) and a.production_type in(2,3) and m.working_company_id=$cbo_work_company_name and m.delivery_date<".$txt_production_date." and a.po_break_down_id in($print_embro_order_id)
		group by m.delivery_date, a.po_break_down_id, a.item_number_id, a.country_id, c.color_number_id";
		
		//echo $print_embro_sql;die;
		
		$print_embro_sql_result=sql_select($print_embro_sql);
		foreach($print_embro_sql_result as $row)
		{
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["printing_prev_qnty"]+=$row[csf("printing_prev_qnty")];
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["printing_rcv_prev_qnty"]+=$row[csf("printing_rcv_prev_qnty")];
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["embroidery_prev_qnty"]+=$row[csf("embroidery_prev_qnty")];
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["embroidery_rcv_prev_qnty"]+=$row[csf("embroidery_rcv_prev_qnty")];
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["wash_prev_qnty"]+=$row[csf("wash_prev_qnty")];
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["wash_rcv_prev_qnty"]+=$row[csf("wash_rcv_prev_qnty")];
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sp_work_prev_qnty"]+=$row[csf("sp_work_prev_qnty")];
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["sp_work_rcv_prev_qnty"]+=$row[csf("sp_work_rcv_prev_qnty")];
		}
	}
	
	
	$ex_factory_sql="select m.delivery_date, a.po_break_down_id as order_id, a.item_number_id, a.country_id, c.color_number_id as color_id, sum(b.production_qnty) as ex_fact_qnty 
	from pro_ex_factory_delivery_mst m, pro_ex_factory_mst a, pro_ex_factory_dtls b, wo_po_color_size_breakdown c
	where m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0 and m.entry_form!=85 and m.delivery_company_id=$cbo_work_company_name and m.delivery_date=".$txt_production_date." $order_cond
	group by m.delivery_date, a.po_break_down_id, a.item_number_id, a.country_id, c.color_number_id";
	//echo $ex_factory_sql;die;
	$ex_factory_sql_result=sql_select($ex_factory_sql);
	foreach($ex_factory_sql_result as $row)
	{
		$porduction_ord_id[$row[csf("order_id")]]=$row[csf("order_id")];
		$ex_fact_order_id[$row[csf("order_id")]]=$row[csf("order_id")];
		$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["ex_fact_qnty"]+=$row[csf("ex_fact_qnty")];
	}
	
	$ex_fact_order_id=implode(",",$ex_fact_order_id);
	if($ex_fact_order_id!="")
	{
		$ex_factory_prev_sql="select m.delivery_date, a.po_break_down_id as order_id, a.item_number_id, a.country_id, c.color_number_id as color_id, sum(b.production_qnty) as ex_fact_qnty 
		from pro_ex_factory_delivery_mst m, pro_ex_factory_mst a, pro_ex_factory_dtls b, wo_po_color_size_breakdown c
		where m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0 and m.entry_form!=85 and m.delivery_company_id=$cbo_work_company_name and m.delivery_date<".$txt_production_date." and a.po_break_down_id in($ex_fact_order_id)
		group by m.delivery_date, a.po_break_down_id, a.item_number_id, a.country_id, c.color_number_id";
		//echo $ex_factory_sql;die;
		$ex_factory_prev_sql_result=sql_select($ex_factory_prev_sql);
		foreach($ex_factory_prev_sql_result as $row)
		{
			$production_data[$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_id")]]["ex_fact_prev_qnty"]+=$row[csf("ex_fact_qnty")];
		}
	}
	
	
	if($db_type==0)
	{
		$select_year=" year(a.insert_date) as job_year";
	}
	else
	{
		$select_year=" to_char(a.insert_date,'YYYY') as job_year";
	}
	$porduction_ord_id=implode(",",$porduction_ord_id);
	if($porduction_ord_id!="")
	{
		$sql_color_size=sql_select("select a.job_no, a.style_ref_no, a.buyer_name, a.job_no_prefix_num, $select_year, b.po_number, c.po_break_down_id, c.item_number_id, c.country_id, c.color_number_id, c.country_ship_date, c.order_quantity 
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c 
		where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_break_down_id in($porduction_ord_id)");
		$order_color_data=array();
		foreach($sql_color_size as $row)
		{
			$order_color_data[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["job_no"]=$row[csf("job_no")];
			$order_color_data[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
			$order_color_data[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["buyer_name"]=$row[csf("buyer_name")];
			$order_color_data[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$order_color_data[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["job_year"]=$row[csf("job_year")];
			$order_color_data[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["po_number"]=$row[csf("po_number")];
			$order_color_data[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["country_ship_date"]=$row[csf("country_ship_date")];
			$order_color_data[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("country_id")]][$row[csf("color_number_id")]]["order_quantity"]+=$row[csf("order_quantity")];
		}
	}
	
	//echo $sql_color_size;die;
	   
	ob_start();
 ?>
  <fieldset style="width:5350px;">
  <div style="width:5350px;">
  	<table width="1880"  cellspacing="0"   >
            <tr class="form_caption" style="border:none;">
                   <td colspan="29" align="center" style="border:none;font-size:14px; font-weight:bold" > Daily Cutting And Input Inhand Report</td>
             </tr>
            <tr style="border:none;">
                    <td colspan="29" align="center" style="border:none; font-size:16px; font-weight:bold">
                    Working Company Name:<? echo $company_arr[str_replace("'","",$cbo_work_company_name)]; ?>                                
                    </td>
              </tr>
              <tr style="border:none;">
                    <td colspan="29" align="center" style="border:none;font-size:12px; font-weight:bold">
                    <? echo "Date: ". str_replace("'","",$txt_production_date) ;?>
                    </td>
              </tr>
        </table>
     <br />	
     <table cellspacing="0" cellpadding="0"  border="1" rules="all"  width="5330" class="rpt_table" align="left">
        <thead>
            <tr >
                <th width="40" rowspan="2">SL</th>
                <th width="100" rowspan="2">Buyer</th>
                <th width="100" rowspan="2">Style Ref</th>
                <th width="60" rowspan="2">Job No</th>
                <th width="50" rowspan="2">Year</th>
                <th width="100" rowspan="2">Order No</th>
                <th width="100" rowspan="2">Country</th>
                <th width="70" rowspan="2">Country Shipdate</th>
                <th width="100" rowspan="2">Garment Item</th>
                <th width="100" rowspan="2">Color</th>
                <th width="70" rowspan="2">Order Qty.</th>
                
                <th width="210" colspan="3">Lay Quantity</th>
                <th width="210" colspan="3">Cutting QC</th>
                
                <th width="70" rowspan="2">Cutting Reject Total</th>
                <th width="70" rowspan="2">QC WIP</th>
                
                <th width="210" colspan="3">Delivery to Print</th>
                <th width="210" colspan="3">Receive from Print</th>
                
                <th width="70" rowspan="2">Printing Reject Total</th>
                <th width="70" rowspan="2">Printing WIP</th>
                
                <th width="210" colspan="3">Delivery to Emb.</th>
                <th width="210" colspan="3">Receive from Emb.</th>
                
                <th width="70" rowspan="2">Emb. Reject Total</th>
                <th width="70" rowspan="2">Emb. WIP</th>
                
                <th width="210" colspan="3">Delivery to Wash</th>
                <th width="210" colspan="3">Receive from Wash</th>
                
                <th width="70" rowspan="2">Wash Reject Total</th>
                <th width="70" rowspan="2">Wash WIP</th>
                
                <th width="210" colspan="3">Delivery to S.Work</th>
                <th width="210" colspan="3">Receive from S.Work</th>
                
                <th width="70" rowspan="2">S. Work Reject Total</th>
                <th width="70" rowspan="2">S.Works WIP</th>
                
                <th width="210" colspan="3">Sewing Input</th>
                <th width="210" colspan="3">Sewing Output</th>
                
                <th width="70" rowspan="2">Sewing Reject</th>
                <th width="70" rowspan="2">Sewing WIP</th>
                
                <th width="210" colspan="3">Poly Entry</th>
                
                <th width="70" rowspan="2">Poly Reject</th>
                <th width="70" rowspan="2">Poly WIP</th>
                
                <th width="210" colspan="3">Packing & Finishing</th>
                
                <th width="70" rowspan="2">Finishing Reject</th>
                <th width="70" rowspan="2">Pac &Fin. WIP</th>
                
                <th width="210" colspan="3">Ex-Factory</th>
                
                <th width="70" rowspan="2">Ex-Fac. WIP</th>
                <th  rowspan="2" width="100">Remarks</th>
            </tr>
            <tr>
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
            </tr>
        </thead>
    </table>
    <div style="max-height:425px; overflow-y:scroll; width:5350px;" id="scroll_body">
    <table  border="1" class="rpt_table"  width="5330" rules="all" id="table_body" >
        <tbody>
        <?
		//echo "<pre>";print_r($production_data);
		$i=1;
		foreach($production_data as $order_id=>$order_data)
		{
			foreach($order_data as $item_id=>$item_data)
			{
				foreach($item_data as $country_id=>$country_data)
				{
					foreach($country_data as $color_id=>$value)
					{
						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                            <td width="40" align="center"><? echo $i; ?></td>
                            <td width="100"><? echo $order_color_data[$order_id][$item_id][$country_id][$color_id]["buyer_name"]; ?></td>
                            <td width="100"><? echo $order_color_data[$order_id][$item_id][$country_id][$color_id]["style_ref_no"]; ?></td>
                            <td width="60" align="center"><? echo $order_color_data[$order_id][$item_id][$country_id][$color_id]["job_no_prefix_num"]; ?></td>
                            <td width="50" align="center"><? echo $order_color_data[$order_id][$item_id][$country_id][$color_id]["job_year"]; ?></td>
                            <td width="100"><? echo $order_color_data[$order_id][$item_id][$country_id][$color_id]["po_number"]; ?></td>
                            <td width="100"><? echo $country_id; ?></td>
                            <td width="70"><? echo $order_color_data[$order_id][$item_id][$country_id][$color_id]["country_ship_date"]; ?></td>
                            <td width="100"><? echo $item_id; ?></td>
                            <td width="100"><? echo $color_id; ?></td>
                            <td width="70" align="right"><? echo $order_color_data[$order_id][$item_id][$country_id][$color_id]["order_quantity"]; ?></td>
                            
                            <td width="70">Prev.</td>
                            <td width="70">Today </td>
                            <td width="70">Total </td>
                            <td width="70">Prev.</td>
                            <td width="70">Today </td>
                            <td width="70">Total </td>
                            <td width="70">Cutting Reject Total </td>
                            <td width="70">QC WIP </td>
                            
                            <td width="70">Prev.</td>
                            <td width="70">Today </td>
                            <td width="70">Total </td>
                            <td width="70">Prev.</td>
                            <td width="70">Today </td>
                            <td width="70">Total </td>
                            <td width="70">Printing Reject Total</td>
                            <td width="70">Printing WIP</td>
                            
                            <td width="70">Prev.</td>
                            <td width="70">Today </td>
                            <td width="70">Total </td>
                            <td width="70">Prev.</td>
                            <td width="70">Today </td>
                            <td width="70">Total </td>
                            <td width="70">Emb. Reject Total</td>
                            <td width="70">Emb. WIP</td>
                            
                            <td width="70">Prev.</td>
                            <td width="70">Today </td>
                            <td width="70">Total </td>
                            <td width="70">Prev.</td>
                            <td width="70">Today </td>
                            <td width="70">Total </td>
                            <td width="70">Wash Reject Total</td>
                            <td width="70">Wash WIP</td>
                            
                            <td width="70">Prev.</td>
                            <td width="70">Today </td>
                            <td width="70">Total </td>
                            <td width="70">Prev.</td>
                            <td width="70">Today </td>
                            <td width="70">Total </td>
                            <td width="70">S. Work Reject Total</td>
                            <td width="70">S.Works WIP</td>
                            
                            <td width="70">Prev.</td>
                            <td width="70">Today </td>
                            <td width="70">Total </td>
                            <td width="70">Prev.</td>
                            <td width="70">Today </td>
                            <td width="70">Total </td>
                            <td width="70">Sewing Reject</td>
                            <td width="70">Sewing WIP</td>
                            
                            <td width="70">Prev.</td>
                            <td width="70">Today </td>
                            <td width="70">Total </td>
                            <td width="70">Poly Reject</td>
                            <td width="70">Poly WIP</td>
                            
                            <td width="70">Prev.</td>
                            <td width="70">Today </td>
                            <td width="70">Total </td>
                            <td width="70">Finishing Reject</td>
                            <td width="70">Pac &Fin. WIP</td>
                            
                            <td width="70">Prev.</td>
                            <td width="70">Today </td>
                            <td width="70">Total </td>
                            <td width="70">Ex-Fac. WIP</td>
                            <td width="100">Remarks</td>
                        </tr>
                        <?
						$i++;
					}
				}
			}
		}
        
        ?>
        </tbody>
        <tfoot>
        	<tr>
            	<th width="40">SL</th>
                <th width="100">Buyer</th>
                <th width="100">Style Ref</th>
                <th width="60">Job No</th>
                <th width="50">Year</th>
                <th width="100">Order No</th>
                <th width="100">Country</th>
                <th width="70">Country Shipdate</th>
                <th width="100">Garment Item</th>
                <th width="100">Color</th>
                <th width="70">Order Qty.</th>
                
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                <th width="70">Cutting Reject Total </th>
                <th width="70">QC WIP </th>
                
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                <th width="70">Printing Reject Total</th>
                <th width="70">Printing WIP</th>
                
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                <th width="70">Emb. Reject Total</th>
                <th width="70">Emb. WIP</th>
                
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                <th width="70">Wash Reject Total</th>
                <th width="70">Wash WIP</th>
                
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                <th width="70">S. Work Reject Total</th>
                <th width="70">S.Works WIP</th>
                
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                <th width="70">Sewing Reject</th>
                <th width="70">Sewing WIP</th>
                
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                <th width="70">Poly Reject</th>
                <th width="70">Poly WIP</th>
                
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                <th width="70">Finishing Reject</th>
                <th width="70">Pac &Fin. WIP</th>
                
                <th width="70">Prev.</th>
                <th width="70">Today </th>
                <th width="70">Total </th>
                <th width="70">Ex-Fac. WIP</th>
                <th width="100">Remarks</th>
            </tr>    
        </tfoot>
    
    </table> 
    </div>     
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


if($action=="finish_fabric")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	 
	  $insert_cond="   and  d.production_date='$insert_date'";
    // if($type==2)  $insert_cond="   and  d.production_date<='$insert_date'";
$sql_job=sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	  sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no 
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and 
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id");  
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="200">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="100">Country</th>
              <th width="100">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
				 // if($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
                        <td align="right"><? echo $row[csf('order_qty')]; $total_qty+=$row[csf('order_qty')];?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          <tfoot>
               <tr>
               <th colspan="6">Total</th>
               <th><? echo $total_qty; ?></th>
               </tr>
          </tfoot> 
       </table>
      </fieldset>
       <br />
    <? 
	
	$sql_fabric="SELECT a.po_breakdown_id,a.color_id,
		sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =2  AND a.entry_form =16 and b.item_category=13 THEN a.quantity
	    ELSE 0 END ) AS grey_fabric_issue,
		sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =4  AND a.entry_form =16 and b.item_category=13 THEN a.quantity
	    ELSE 0 END ) AS grey_fabric_issue_return,
		sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =1  AND a.entry_form =37 and b.item_category=2 THEN a.quantity
	    ELSE 0 END ) AS finish_fabric_rece,
		sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =2  AND a.entry_form =37 and b.item_category=2 THEN a.quantity
	    ELSE 0 END ) AS finish_fabric_rece_return,
		sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =2  AND a.entry_form =18 and b.item_category=2 THEN a.quantity
	    ELSE 0 END ) AS fabric_qty,
		sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =2 and b.item_category=2  AND a.entry_form =18 THEN a.quantity 
		ELSE 0 END ) AS fabric_qty_pre,
		sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_qty,
		sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_pre,
		sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_qty,
		sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_pre
		FROM order_wise_pro_details a,inv_transaction b
	    WHERE a.trans_id = b.id 
		and b.status_active=1 and a.entry_form in(18,15,16,37) and a.quantity!=0 and  b.is_deleted=0 AND a.po_breakdown_id 
		in (".str_replace("'","",$po_number_id).") group by a.po_breakdown_id,a.color_id";

			
	
		//echo $sql_cutting_delevery;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql_cutting_delevery);
		$production_details_arr=array();
		$production_size_details_arr=array();
		foreach( $sql_data as $row)
		{
		$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('size_number_id')]]+=$row[csf('production_qnty')];
		$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array['color_total']+=$row[csf('production_qnty')];
		//$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		
		$production_details_arr[$row[csf('id')]]['country']=$row[csf('country_id')];
		$production_details_arr[$row[csf('id')]]['color']=$row[csf('color_number_id')];
		$production_details_arr[$row[csf('id')]]['production_date']=$row[csf('cut_delivery_date')];
		$production_details_arr[$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
		$production_details_arr[$row[csf('id')]]['product_qty']+=$row[csf('production_qnty')];
		//$production_details_arr[$row[csf('id')]]['size']=$row[csf('size_number_id')];
		$production_size_details_arr[$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('production_qnty')];
		}
		// print_r($production_size_details_arr);die;
		 $job_color_tot=0;
		 ?> 
        <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">  
			<label> <strong>Po Number: <? echo $order_number; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">ID</th>
                        <th width="70">Date</th>
                        <th width="70">Fabric Qty.</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($production_details_arr as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//if($value_c != "")
				//{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
                 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
                 <td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
				 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							
						?>
						<td width="60" align="right"><? echo $production_size_details_arr[$key_c][$key_s]['product_qty'] ;?></td>
						<?
							
						}
				 ?>
				 <td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
				<?
				$i++;
				//}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
                 <th></th>
                 <th></th>
                 <th></th>
				 <th>Total</th>
				
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$key_s];?></th>
						<?
							}
						}
				?>
                 <th align="right"><? echo  $job_color_qnty_array['color_total']; ?></th>
				 </tr>
			  </tfoot>
		</table>
	    <br />
     </fieldset>
 </div>
 <?
}


if($action=="cutting_delivery_popup")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
    if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	$sql_job=sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	  sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no 
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and 
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id");  
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="200">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="100">Country</th>
              <th width="100">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
				 // if($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
                        <td align="right"><? echo $row[csf('order_qty')]; $total_qty+=$row[csf('order_qty')];?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          <tfoot>
               <tr>
               <th colspan="6">Total</th>
               <th><? echo $total_qty; ?></th>
               </tr>
          </tfoot>
       </table>
      </fieldset>
       <br />
    <? 
		$sql_cutting_delevery="select a.id,a.cut_delivery_date,a.challan_no ,b.production_qnty,c.size_number_id,c.color_number_id,c.country_id
		from pro_cut_delivery_order_dtls a,pro_cut_delivery_color_dtls b ,wo_po_color_size_breakdown c 
	    where a.id=b.mst_id 
	    and b.color_size_break_down_id=c.id 
		and a.po_break_down_id=c.po_break_down_id  
		and a.po_break_down_id=$order_id
		and c.color_number_id=$color_id 
		and a.status_active=1 and a.is_deleted=0
		and  b.status_active=1  and b.is_deleted=0
		and c.status_active=1 ";
		//echo $sql_cutting_delevery;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql_cutting_delevery);
		$production_details_arr=array();
		$production_size_details_arr=array();
		foreach( $sql_data as $row)
		{
		$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('size_number_id')]]+=$row[csf('production_qnty')];

		$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array['color_total']+=$row[csf('production_qnty')];
		//$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		
		$production_details_arr[$row[csf('id')]]['country']=$row[csf('country_id')];
		$production_details_arr[$row[csf('id')]]['color']=$row[csf('color_number_id')];
		$production_details_arr[$row[csf('id')]]['production_date']=$row[csf('cut_delivery_date')];
		$production_details_arr[$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
		$production_details_arr[$row[csf('id')]]['product_qty']+=$row[csf('production_qnty')];
		//$production_details_arr[$row[csf('id')]]['size']=$row[csf('size_number_id')];
		$production_size_details_arr[$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('production_qnty')];
		}
		// print_r($production_size_details_arr);die;
		 $job_color_tot=0;
		 ?> 
        <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">  
			<label> <strong>Po Number: <? echo $order_number; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
                        <th width="70">Country</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($production_details_arr as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//if($value_c != "")
				//{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
                 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
                 <td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
				 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							
						?>
						<td width="60" align="right"><? echo $production_size_details_arr[$key_c][$key_s]['product_qty'] ;?></td>
						<?
							
						}
				 ?>
				 <td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
				<?
				$i++;
				//}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
                 <th></th>
                 <th></th>
                 <th></th>
				 <th>Total</th>
				
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$key_s];?></th>
						<?
							}
						}
				?>
                 <th align="right"><? echo  $job_color_qnty_array['color_total']; ?></th>
				 </tr>
			  </tfoot>
		</table>
				  <br />
     </fieldset>
 </div>
 <?
}

if($action=="cutting_and_sewing_remarks")
{	
	extract($_REQUEST); 
 	echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'',''); 
    if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	?>
    <div align="center">
        <fieldset style="width:480px">
        <legend>Cutting</legend>
		<? 
        $sql="SELECT  d.id,sum(e.production_qnty) as product_qty,f.color_number_id,d.remarks,d.production_date
        FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
        WHERE 
        d.po_break_down_id=$order_id  and
        d.id=e.mst_id and
        e.color_size_break_down_id=f.id and
        f.po_break_down_id=$order_id and
        e.production_type=1  and
        f.color_number_id=$color_id and
        e.is_deleted =0 and
        e.status_active =1 and
		d.is_deleted =0 and
		d.status_active =1 and
		f.is_deleted =0 and
		f.status_active =1   $insert_cond group by d.id,f.color_number_id,d.remarks,d.production_date order by d.id";
        //echo $sql;
        echo  create_list_view ( "list_view_1", "ID,Date,Production Qnty,Remarks", "80,70,70,280","600","220",1, $sql, "", "","", 1, '0,0,0,0', $arr, "id,production_date,product_qty,remarks", "../requires/daily_cutting_inhand_report_urmi_controller", '','0,3,1,0','0,0,0,product_qty,0');
        ?>
        </fieldset>
        <br/>
        <fieldset style="width:480px">
        <legend>Cutting Delivery to Input</legend>
		<? 
	    $sql_cutting_delevery="select a.id,a.cut_delivery_date ,a.remarks,
		sum(b.production_qnty) AS cut_delivery_qnty
		from pro_cut_delivery_order_dtls a,pro_cut_delivery_color_dtls b ,wo_po_color_size_breakdown c 
	    where a.id=b.mst_id 
	    and b.color_size_break_down_id=c.id 
		and a.po_break_down_id=c.po_break_down_id  
		and a.po_break_down_id=$order_id
		and c.color_number_id=$color_id 
	    group by a.id,a.cut_delivery_date ,a.remarks";
       // echo $sql_cutting_delevery;
        echo  create_list_view ( "list_view_1", "ID,Date,Production Qnty,Remarks", "80,70,70,280","600","220",1, $sql_cutting_delevery, "", "","", 1, '0,0,0,0', $arr, "id,cut_delivery_date,cut_delivery_qnty,remarks", "../requires/daily_cutting_inhand_report_urmi_controller", '','0,3,1,0','0,0,0,cut_delivery_qnty,0');
                
        ?>
        </fieldset>
        <br/>
        <fieldset style="width:480px">
        <legend>Print/Embr Issue</legend>
		<? 
        $sql="SELECT  d.id,d.embel_name,sum(e.production_qnty) as product_qty,f.color_number_id,d.remarks,d.production_date
        FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
        WHERE 
        d.po_break_down_id=$order_id  and
        d.id=e.mst_id and
        e.color_size_break_down_id=f.id and
        f.po_break_down_id=$order_id and
        e.production_type=2 and
        f.color_number_id=$color_id and
        e.is_deleted =0 and
        e.status_active =1 and
		d.is_deleted =0 and
		d.status_active =1 and
		f.is_deleted =0 and
		f.status_active =1   $insert_cond group by d.id,d.embel_name,f.color_number_id,d.remarks,d.production_date order by d.embel_name";
        $arr=array(1=>$emblishment_name_array);
        echo  create_list_view ( "list_view_1", "ID,Embel. Name,Date,Production Qnty,Remarks", "80,100,70,70,180","600","220",1, $sql, "", "","", 1, '0,embel_name,0,0,0', $arr, "id,embel_name,production_date,product_qty,remarks", "../requires/daily_cutting_inhand_report_urmi_controller", '','0,0,3,1,0','0,0,0,0,product_qty,0');
        ?>
        </fieldset>
        <br/>
        <fieldset style="width:480px">
        <legend>Print/Embr Receive</legend>
		<? 
        $sql="SELECT  d.id,d.embel_name,sum(e.production_qnty) as product_qty,f.color_number_id,d.remarks,d.production_date
        FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
        WHERE 
        d.po_break_down_id=$order_id  and
        d.id=e.mst_id and
        e.color_size_break_down_id=f.id and
        f.po_break_down_id=$order_id and
        e.production_type=3 and
        f.color_number_id=$color_id and
        e.is_deleted =0 and
        e.status_active =1  and
		f.is_deleted =0 and
		f.status_active =1 and
		d.is_deleted =0 and
		d.status_active =1 
		
		 $insert_cond group by d.id,d.embel_name,f.color_number_id,d.remarks,d.production_date order by d.embel_name";
        $arr=array(1=>$emblishment_name_array);
        echo  create_list_view ( "list_view_1", "ID,Embel. Name,Date,Production Qnty,Remarks", "80,100,70,70,180","600","220",1, $sql, "", "","", 1, '0,embel_name,0,0,0', $arr, "id,embel_name,production_date,product_qty,remarks", "../requires/daily_cutting_inhand_report_urmi_controller", '','0,0,3,1,0','0,0,0,0,product_qty,0');
        ?>
        </fieldset>
        <br/>
        
        <fieldset style="width:480px">
        <legend>Sewing Input</legend>
        <?
        $sql="SELECT  d.id,sum(e.production_qnty) as product_qty,f.color_number_id,d.remarks,d.production_date
        FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
        WHERE 
        d.po_break_down_id=$order_id  and
        d.id=e.mst_id and
        e.color_size_break_down_id=f.id and
        f.po_break_down_id=$order_id and
        e.production_type=4  and
        f.color_number_id=$color_id and
        e.is_deleted =0 and
        e.status_active =1  and
		d.is_deleted =0 and
		d.status_active =1 and
		f.is_deleted =0 and
		f.status_active =1 
		 $insert_cond group by d.id,f.color_number_id,d.remarks,d.production_date order by d.id";
        //echo $sql;
        echo  create_list_view ( "list_view_1", "ID,Date,Production Qnty,Remarks", "80,70,70,280","600","220",1, $sql, "", "","", 1, '0,0,0,0', $arr, "id,production_date,product_qty,remarks", "../requires/daily_cutting_inhand_report_urmi_controller", '','0,3,1,0','0,0,0,product_qty,0');
		?>
        </fieldset>
	</div>  
<?
exit();
}



if($action=="emblishment_popup")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name"  ); 
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
	if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	$sql_job=sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	  sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no 
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and 
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id");  
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:810px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="150">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="100">Country</th>
              <th width="100">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
				 // if($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
                        <td align="right"><? echo $row[csf('order_qty')]; $total_qty+=$row[csf('order_qty')];?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          <tfoot>
               <tr>
               <th colspan="6">Total</th>
               <th><? echo $total_qty; ?></th>
               </tr>
          </tfoot>
       </table>
      </fieldset>
       <br />
    <? 
		$sql="SELECT  d.id,d.floor_id,d.production_source,d.serving_company,e.production_qnty as product_qty,f.size_number_id,f.color_number_id,
		    d.challan_no,d.production_date,f.country_id
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type=$type  and
		    f.color_number_id=$color_id and
			d.embel_name=$embl_type  and
		    e.is_deleted =0 and
			e.status_active =1 and
		    d.is_deleted =0 and
			d.status_active =1 and
		    f.is_deleted =0 and
			f.status_active =1   $insert_cond order by d.production_date,f.id";
		//echo $sql;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		$production_details_arr=array();
		$production_size_details_arr=array();
		$floor_qty_arr=array();
		$grand_size_qty=array();
		//$grand_color_qty=array();
		foreach( $sql_data as $row)
		{
			if($row[csf('production_source')]==1)
			{
				$job_size_array[$row[csf('production_source')]][$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_size_qnty_array[$row[csf('production_source')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$floor_qty_arr[$row[csf('production_source')]][$row[csf('floor_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$job_color_array[$row[csf('production_source')]][$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$job_color_qnty_array[$row[csf('production_source')]]['color_total']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['production_date']=$row[csf('production_date')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['sewing_line']=$row[csf('sewing_line')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];
				$production_size_details_arr[$row[csf('production_source')]][$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];
			}
			else
			{
				$job_size_array[$row[csf('production_source')]][$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_size_qnty_array[$row[csf('production_source')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$job_color_array[$row[csf('production_source')]][$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$job_color_qnty_array[$row[csf('production_source')]]['color_total']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['production_date']=$row[csf('production_date')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['serving_company']=$row[csf('serving_company')];
				$production_size_details_arr[$row[csf('production_source')]][$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];	
			}
			$grand_size_qty[$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$grand_color_qty+=$row[csf('product_qty')];
		}
		//print_r($job_size_array);die;
		 $job_color_tot=0;
		 ?> 
        <div id="data_panel" align="" style="width:100%">
			<label> <strong>In House <strong><label/>
            
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="150">Color</th>
                        <th width="70">Country</th>
                        <th width="70">Unit Name</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[1][$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?
			
				$i=1;
				$inhouse_floor=array();
				foreach($production_details_arr[1] as $key_c=>$value_c)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if($i!=1)
						{
							
							if(!in_array($value_c['floor_id'],$inhouse_floor))
								{
								?>
								 <tr bgcolor="#FFFFE8">
									 <td colspan="5" align="right"> Floor Total</td>
									
									 <?
											foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
											{
												if($value_s !="")
												{
											?>
											<td width="60" align="right"><? echo $floor_qty_arr[1][$floor_id][$key_s];?></td>
											<?
												}
											}
									?>
									 <td align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></td>
								 </tr>		
								<?	
								}
							}
							
							?>
                            
                            
                            
							 <tr bgcolor="<? echo $bgcolor;?>">
							 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
							 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
							 <td align="center"><? echo  $floor_arr[$value_c['floor_id']]; ?></td>
							 <td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
							 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
							 <?
									foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
									{
										
									?>
									<td width="60" align="right"><? echo $production_size_details_arr[1][$key_c][$key_s]['product_qty'] ;?></td>
									<?
										
									}
							 ?>
							 <td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[1][$value_po][$value_c]; ?></td>
			
							 </tr>
							<?
							
							$i++;
							$inhouse_floor[]=$value_c['floor_id'];
							$floor_id=$value_c['floor_id'];;
					}
					?>
                    
                    
                    		 <tr bgcolor="#FFFFE8">
									 <td colspan="5" align="right"> Floor Total</td>
									
									 <?
											foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
											{
												if($value_s !="")
												{
											?>
											<td width="60" align="right"><? echo $floor_qty_arr[1][$floor_id][$key_s];?></td>
											<?
												}
											}
									?>
									 <td align="right"><? echo  $job_color_qnty_array[1][$floor_id]['color_total']; ?></td>
								 </tr>	
                                 
                                 
                            <tfoot>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                             <th></th>
                             <th></th>
                             <th></th>
                          
                             <th></th>
                             <th>Total</th>
                            
                             <?
                                    foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $job_size_qnty_array[1][$key_s];?></th>
                                    <?
                                        }
                                    }
                            ?>
                             <th align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></th>
                             </tr>
                          </tfoot>
					</table>
                   
                <label > <strong>Out Bound:<strong><label/>    
                <table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="120">Color</th>
                        <th width="130">Company</th>
                        <th width="70">Country</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[3][$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?
			
				$j=1;
				$inhouse_floor=array();
				foreach($production_details_arr[3] as $key_c=>$value_c)
					{
					if($prod_reso_allo==1)	
						{
						$line_name= $lineArr[$prod_reso_arr[$value_c['sewing_line']]];
					    }
						else 
						{
						$line_name= $lineArr[$value_c['sewing_line']];
						}	
						if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
							?>
                            
                            
                            
							 <tr bgcolor="<? echo $bgcolor;?>">
							 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
                             <td align="center"><? echo  $supplier_arr[$value_c['serving_company']]; ?></td>
							 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
							 <td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
							 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
							 <?
									foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
									{
										
									?>
									<td width="60" align="right"><? echo $production_size_details_arr[3][$key_c][$key_s]['product_qty'] ;?></td>
									<?
										
									}
							 ?>
							 <td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[3][$value_po][$value_c]; ?></td>
			
							 </tr>
							<?
							$j++;
							
					}
					?>
                    
                            <tfoot>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                             <th></th>
                             <th></th>
                             <th></th>
                              <th></th>
                             <th>Total</th>
                            
                            		 <?
                                    foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $job_size_qnty_array[3][$key_s];?></th>
                                    <?
                                        }
                                    }
                            		?>
                             <th align="right"><? echo  $job_color_qnty_array[3]['color_total']; ?></th>
                             </tr>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                            
                             <th colspan="5"> Grand Total</th>
                            
                            		 <?
                                    foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $grand_size_qty[$key_s];?></th>
                                    <?
                                        }
                                    }
                            		?>
                             <th align="right"><? echo  $grand_color_qty; ?></th>
                             </tr>
                             
                          </tfoot>
					
					</table>    
	 </div>
 <?
}


if($action=="cutting_and_sewing_popup")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name"  );
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 and status_active=1");
	//print_r($supplier_arr);
    if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	$sql_job=sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	  sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no 
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and 
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id");  
	?>
    <div id="data_panel" align="center" style="width:100%">
      
    <? 
	 
	

		$sql="SELECT  d.production_source,d.serving_company,d.floor_id,d.sewing_line,d.id,e.production_qnty as product_qty,f.size_number_id,f.color_number_id,d.challan_no,
		    d.production_date,f.country_id,d.challan_no
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type=4  and
			f.color_number_id=$color_id and
		    e.is_deleted =0 and
			e.status_active =1 and
		    f.is_deleted =0 and
			f.status_active =1 and
		    d.is_deleted =0 and
			d.status_active =1 
			 $insert_cond order by d.production_date,f.id";
		//echo $sql;die;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		$production_details_arr=array();
		$production_size_details_arr=array();
		$floor_qty_arr=array();
		$grand_size_qty=array();
		//$grand_color_qty=array();
		foreach( $sql_data as $row)
		{
			if($row[csf('production_source')]==1)
			{
				$job_size_array[$row[csf('production_source')]][$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_size_qnty_array[$row[csf('production_source')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$floor_qty_arr[$row[csf('production_source')]][$row[csf('floor_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$job_color_array[$row[csf('production_source')]][$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$job_color_qnty_array[$row[csf('production_source')]]['color_total']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['production_date']=$row[csf('production_date')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['sewing_line']=$row[csf('sewing_line')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];
				$production_size_details_arr[$row[csf('production_source')]][$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];
			}
			else
			{
				$job_size_array[$row[csf('production_source')]][$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_size_qnty_array[$row[csf('production_source')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$job_color_array[$row[csf('production_source')]][$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$job_color_qnty_array[$row[csf('production_source')]]['color_total']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['production_date']=$row[csf('production_date')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['serving_company']=$row[csf('serving_company')];
				$production_size_details_arr[$row[csf('production_source')]][$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];	
			}
			$grand_size_qty[$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$grand_color_qty+=$row[csf('product_qty')];
		}
		//print_r($job_size_array);die;
		 $job_color_tot=0;
		 ?> 
        <div id="data_panel" align="" style="width:100%">
			<label> <strong>In House <strong><label/>
            
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
                        <th width="70">Country</th>
                        <th width="70">Unit Name</th>
                        <th width="70">Line No</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[1][$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?
			
				$i=1;
				$inhouse_floor=array();
				foreach($production_details_arr[1] as $key_c=>$value_c)
					{
						
					if($prod_reso_allo==1)	
						{
						$line_name= $lineArr[$prod_reso_arr[$value_c['sewing_line']]];
					    }
						else 
						{
						$line_name= $lineArr[$value_c['sewing_line']];
						}	
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if($i!=1)
						{
							
							if(!in_array($value_c['floor_id'],$inhouse_floor))
								{
								?>
								 <tr bgcolor="#FFFFE8">
									 <td colspan="6" align="right"> Floor Total</td>
									
									 <?
											foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
											{
												if($value_s !="")
												{
											?>
											<td width="60" align="right"><? echo $floor_qty_arr[1][$floor_id][$key_s];?></td>
											<?
												}
											}
									?>
									 <td align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></td>
								 </tr>		
								<?	
								}
							}
							
							?>
                            
                            
                            
							 <tr bgcolor="<? echo $bgcolor;?>">
							 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
							 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
							 <td align="center"><? echo  $floor_arr[$value_c['floor_id']]; ?></td>
							 <td align="center"><? echo  $line_name; ?></td>
							 
							 <td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
							 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
							 <?
									foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
									{
										
									?>
									<td width="60" align="right"><? echo $production_size_details_arr[1][$key_c][$key_s]['product_qty'] ;?></td>
									<?
										
									}
							 ?>
							 <td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[1][$value_po][$value_c]; ?></td>
			
							 </tr>
							<?
							
							$i++;
							$inhouse_floor[]=$value_c['floor_id'];
							$floor_id=$value_c['floor_id'];;
					}
					?>
                    
                    
                    		 <tr bgcolor="#FFFFE8">
									 <td colspan="6" align="right"> Floor Total</td>
									
									 <?
											foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
											{
												if($value_s !="")
												{
											?>
											<td width="60" align="right"><? echo $floor_qty_arr[1][$floor_id][$key_s];?></td>
											<?
												}
											}
									?>
									 <td align="right"><? echo  $job_color_qnty_array[1][$floor_id]['color_total']; ?></td>
								 </tr>	
                                 
                                 
                            <tfoot>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th>Total</th>
                            
                             <?
                                    foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $job_size_qnty_array[1][$key_s];?></th>
                                    <?
                                        }
                                    }
                            ?>
                             <th align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></th>
                             </tr>
                          </tfoot>
					</table>
                   
                <label > <strong>Out Bound:<strong><label/>    
                <table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
                        <th width="70">Company</th>
                        <th width="70">Country</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[3][$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?
			
				$j=1;
				$inhouse_floor=array();
				foreach($production_details_arr[3] as $key_c=>$value_c)
					{
					if($prod_reso_allo==1)	
						{
						$line_name= $lineArr[$prod_reso_arr[$value_c['sewing_line']]];
					    }
						else 
						{
						$line_name= $lineArr[$value_c['sewing_line']];
						}	
						if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
							?>
                            
                            
                            
							 <tr bgcolor="<? echo $bgcolor;?>">
							 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
                             <td align="center"><? echo  $supplier_arr[$value_c['serving_company']]; ?></td>
							 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
							 <td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
							 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
							 <?
									foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
									{
										
									?>
									<td width="60" align="right"><? echo $production_size_details_arr[3][$key_c][$key_s]['product_qty'] ;?></td>
									<?
										
									}
							 ?>
							 <td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[3][$value_po][$value_c]; ?></td>
			
							 </tr>
							<?
							$j++;
							
					}
					?>
                    
                            <tfoot>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                             <th></th>
                             <th></th>
                             <th></th>
                              <th></th>
                             <th>Total</th>
                            
                            		 <?
                                    foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $job_size_qnty_array[3][$key_s];?></th>
                                    <?
                                        }
                                    }
                            		?>
                             <th align="right"><? echo  $job_color_qnty_array[3]['color_total']; ?></th>
                             </tr>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                            
                             <th colspan="5"> Grand Total</th>
                            
                            		 <?
                                    foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $grand_size_qty[$key_s];?></th>
                                    <?
                                        }
                                    }
                            		?>
                             <th align="right"><? echo  $grand_color_qty; ?></th>
                             </tr>
                             
                          </tfoot>
					
					</table>    
	 </div>
	 <?
}



if($action=="cutting_popup")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	$sql_job=sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	  sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no 
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and 
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id");  
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="200">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="100">Country</th>
              <th width="100">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
				 // if($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
                        <td align="right"><? echo $row[csf('order_qty')]; $total_qty+=$row[csf('order_qty')]; ?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
             <tfoot>
               <tr>
               <th colspan="6">Total</th>
               <th><? echo $total_qty; ?></th>
               </tr>
            </tfoot>
       </table>
      </fieldset>
       <br />
    <? 
	 
	

		$sql="SELECT  d.id,e.production_qnty as product_qty,f.size_number_id,f.color_number_id,d.challan_no,d.production_date,f.country_id
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type=$type  and
			f.color_number_id=$color_id and
		    e.is_deleted =0 and
			e.status_active =1 and
			f.is_deleted =0 and
			f.status_active =1 and
			d.is_deleted =0 and
			d.status_active=1  
			  $insert_cond
			order by d.production_date";
		//echo $sql;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		$production_details_arr=array();
		$production_size_details_arr=array();
		foreach( $sql_data as $row)
		{
		$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array['color_total']+=$row[csf('product_qty')];
		//$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		
		$production_details_arr[$row[csf('id')]]['country']=$row[csf('country_id')];
		$production_details_arr[$row[csf('id')]]['color']=$row[csf('color_number_id')];
		$production_details_arr[$row[csf('id')]]['production_date']=$row[csf('production_date')];
		$production_details_arr[$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
		$production_details_arr[$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];
		//$production_details_arr[$row[csf('id')]]['size']=$row[csf('size_number_id')];
		$production_size_details_arr[$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];
		}
		// print_r($production_size_details_arr);die;
		 $job_color_tot=0;
		 ?> 
        <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">  
			<label> <strong>Po Number: <? echo $order_number; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
                        <th width="70">Country</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($production_details_arr as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//if($value_c != "")
				//{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
                 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
                 <td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
				 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							
						?>
						<td width="60" align="right"><? echo $production_size_details_arr[$key_c][$key_s]['product_qty'] ;?></td>
						<?
							
						}
				 ?>
				 <td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
				<?
				$i++;
				//}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
                 <th></th>
                 <th></th>
                 <th></th>
				 <th>Total</th>
				
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$key_s];?></th>
						<?
							}
						}
				?>
                 <th align="right"><? echo  $job_color_qnty_array['color_total']; ?></th>
				 </tr>
			  </tfoot>
		</table>
				  <br />
     </fieldset>
 </div>
 <?
}

if($action=="total_fabric_recv_qty")//total_fabric_recv_qty
{
	echo load_html_head_contents("Today Fabric Recv Qty","../../../", 1, 1, $unicode);
    extract($_REQUEST);
/*	echo $prod_date.'_';
	echo $order_id.'_';
	echo $color_id.'_';*/
	
	$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name"  );
	$batch_noArr = return_library_array("select id,batch_no from pro_batch_create_mst","id","batch_no"); 
	$color_name_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
	//$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 and status_active=1");
	//print_r($supplier_arr);
	//echo $prod_date;
    if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	
	  $sql_fabric_qty=("SELECT a.po_breakdown_id,a.color_id,c.issue_number,c.issue_date,b.pi_wo_batch_no,
	
		CASE WHEN b.transaction_date <= '".$prod_date."' AND a.trans_type =2  AND a.entry_form =18 and b.item_category=2 THEN a.quantity
	    ELSE 0 END  AS fabric_qty
		
		FROM order_wise_pro_details a,inv_transaction b,inv_issue_master c
	    WHERE a.trans_id = b.id 
		and b.status_active=1 and a.entry_form in(18,15,16,37) and c.id=b.mst_id and a.quantity!=0 and  b.is_deleted=0  and a.color_id=$color_id AND a.po_breakdown_id in (".str_replace("'","",$order_id).") order by c.issue_number ");
		//AND a.po_breakdown_id in (".str_replace("'","",$po_number_id).")
		//echo $sql_fabric_qty;
		$result=sql_select($sql_fabric_qty);
		$fabric_pre_qty=array();
		$fabric_today_qty=array();  
		$total_fabric=array();
		$fabric_balance=array();
		$fabric_wip=array();
		/*foreach($result as $value)
		{
			//$fabric_wip[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]['issue']=$value[csf("grey_fabric_issue")]-$value[csf("grey_fabric_issue_return")];
			//$fabric_wip[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]['receive']=$value[csf("finish_fabric_rece")]-$value[csf("finish_fabric_rece_return")];
			
		//	$fabric_pre_qty[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]=$value[csf("fabric_qty_pre")]+$value[csf("trans_in_pre")]
		//	-$value[csf("trans_out_pre")];
		
			$fabric_pre_qty[$value[csf("color_id")]]['fab_qty']+=$value[csf("fabric_qty_pre")]+$value[csf("trans_in_pre")]-$value[csf("trans_out_pre")];
			//-$value[csf("trans_out_pre")];
			$fabric_pre_qty[$value[csf("color_id")]]['fabric_qty']+=$value[csf("fabric_qty")];
			$fabric_pre_qty[$value[csf("color_id")]]['issue_id']=$value[csf("issue_number")];
			$fabric_pre_qty[$value[csf("color_id")]]['issue_date']=$value[csf("issue_date")];
			$fabric_pre_qty[$value[csf("color_id")]]['batch_no']=$value[csf("pi_wo_batch_no")];
				
		}*/
	//	print_r($fabric_pre_qty);
	?>
	
    
     <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:540px">  
		<table width="540" align="center" border="1" rules="all" class="rpt_table"   >
		<thead>
			<tr>
                <th width="30">SL</th>
                <th width="130">ISSUE ID</th>
                <th width="80">Issue Date</th>
                <th width="100">BATCH NO</th>
                <th width="100">COLOR NAME</th>
                <th width="80">Recv. Qty</th>
            </tr>
         </thead>
         <?
		 $total_fab_qty=0;
		 $k=1;
        // foreach($fabric_today_qty as $order_id=>$order_data)
		 //{
			 //foreach($fabric_pre_qty as $color_key=>$color_val)
			 
			 foreach($result as $value)
			 {
				  if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		 ?>
         <tr style="font:'Arial Narrow'" align="center" bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
         	<td width="30"><? echo  $k;?> </td>
            <td width="130"><? echo  $value[csf("issue_number")];//$color_val['issue_id'];?>  </td>
            <td width="80"><? echo   change_date_format($value[csf("issue_date")]);//change_date_format($color_val['issue_date']);?> </td>
            <td width="100"> <? echo  $batch_noArr[$value[csf("pi_wo_batch_no")]];//$batch_noArr[$color_val['batch_no']];?></td>
            <td width="100"> <? echo  $color_name_arr[$value[csf("color_id")]];//$color_name_arr[$color_key];?></td>
            <td width="80" align="right"><? echo  number_format($value[csf("fabric_qty")],2);//number_format($color_val['fab_qty']+$color_val['fabric_qty'],2);?> </td>
            
         </tr>
         <?
		 $total_fab_qty+=$value[csf("fabric_qty")];//$color_val['fab_qty']+$color_val['fabric_qty'];
		 $k++;
			 }
		 //}
		 ?>
         <tr>
         <tfoot>
         <th align="right" colspan="5">Total</th><th align="right"> <? echo number_format($total_fab_qty,2);?></th> 
         </tr>
         </table>
                        
  </fieldset>
  </div>
<?
	//exit();
	
}
if($action=="today_fabric_recv_qty")//
{
	echo load_html_head_contents("Today Fabric Recv Qty","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name"  );
	$batch_noArr = return_library_array("select id,batch_no from pro_batch_create_mst","id","batch_no"); 
	$color_name_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
	//$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 and status_active=1");
	//print_r($supplier_arr);
	//echo $prod_date;
    if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	
	   $sql_fabric_qty=("SELECT a.po_breakdown_id,a.color_id,c.issue_number,c.issue_date,b.pi_wo_batch_no,
	
		CASE WHEN b.transaction_date = '".$prod_date."' AND a.trans_type =2  AND a.entry_form =18 and b.item_category=2 THEN a.quantity
	    ELSE 0 END  AS fabric_qty
		
		FROM order_wise_pro_details a,inv_transaction b,inv_issue_master c
	    WHERE a.trans_id = b.id 
		and b.status_active=1 and a.entry_form in(18,15,16,37) and c.id=b.mst_id and a.quantity>0 and  b.is_deleted=0 and a.color_id=$color_id  AND a.po_breakdown_id in (".str_replace("'","",$order_id).") ");
		//AND a.po_breakdown_id in (".str_replace("'","",$po_number_id).")
		//echo  $sql_fabric_qty;
		$result=sql_select($sql_fabric_qty);
		$fabric_pre_qty=array();
		$fabric_today_qty=array();  
		$total_fabric=array();
		$fabric_balance=array();
		$fabric_wip=array();
		/*foreach($result as $value)
		{
			//$fabric_wip[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]['issue']=$value[csf("grey_fabric_issue")]-$value[csf("grey_fabric_issue_return")];
			//$fabric_wip[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]['receive']=$value[csf("finish_fabric_rece")]-$value[csf("finish_fabric_rece_return")];
			
			//$fabric_pre_qty[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]=$value[csf("fabric_qty_pre")]+$value[csf("trans_in_pre")]
			//-$value[csf("trans_out_pre")];
			$fabric_today_qty[$value[csf("color_id")]]['fabric_qty']+=$value[csf("fabric_qty")]+$value[csf("trans_in_qty")]
			-$value[csf("trans_out_qty")];
			//-$value[csf("trans_out_pre")];
			$fabric_today_qty[$value[csf("color_id")]]['issue_id']=$value[csf("issue_number")];
			$fabric_today_qty[$value[csf("color_id")]]['issue_date']=$value[csf("issue_date")];
			$fabric_today_qty[$value[csf("color_id")]]['batch_no']=$value[csf("pi_wo_batch_no")];
				
		}*/
		
	?>
	
    
     <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:550px">  
		<table width="520" align="center" border="1" rules="all" class="rpt_table"   >
		<thead>
			<tr>
                <th width="30">SL</th>
                <th width="130">ISSUE ID</th>
                <th width="80">Issue Date</th>
                <th width="100">BATCH NO</th>
                <th width="100">COLOR NAME</th>
                <th width="80">Recv. Qty</th>
            </tr>
         </thead>
         <?
		 $total_fab_qty=0;
		 $k=1;
        
			 //foreach($fabric_today_qty as $color_key=>$color_val)
			 foreach($result as $value)
			 {
				 if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";  
				
				 if($value[csf("fabric_qty")]>0)
				 {
				
		 ?>
        <tr style="font:'Arial Narrow'" align="center" bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
         	<td width="30"><?   echo  	$k;?> </td>
            <td width="130"><? 	echo    $value[csf("issue_number")];//$color_val['issue_id'];?>  </td>
            <td width="80"><? 	echo   change_date_format($value[csf("issue_date")]);//change_date_format($color_val['issue_date']);?> </td>
            <td width="100"><? 	echo   $batch_noArr[$value[csf("pi_wo_batch_no")]];//$batch_noArr[$color_val['batch_no']];?></td>
            <td width="100"><? 	echo   $color_name_arr[$value[csf("color_id")]];//$color_name_arr[$color_key];?></td>
            <td width="80" align="right"><? echo  number_format($value[csf("fabric_qty")],2);// number_format($color_val['fabric_qty'],2);?> </td>
            
         </tr>
         <?
		  $total_fab_qty+=$value[csf("fabric_qty")]; //new
		  //$total_fab_qty+=$color_val['fabric_qty'];//old
		 $k++;
			 	}
			  }
		 ?>
         <tr>
         <tfoot>
         <th align="right" colspan="5">Total</th><th align="right"> <? echo number_format($total_fab_qty,2);?></th> 
         </tr>
         </table>
                        
  </fieldset>
  </div>
<?
//	exit();
	
}
<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
 




if ($action=="load_drop_down_buyer")  
{

	echo create_drop_down( "cbo_buyer_name", 150, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data'   order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 100, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/line_wise_dhu_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/line_wise_dhu_report_controller' );",0 );     	 
}

if ($action=="load_drop_down_floor")  
{ 
	echo create_drop_down( "cbo_floor", 100, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (5) order by floor_name","id,floor_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/line_wise_dhu_report_controller', this.value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'load_drop_down_line', 'line_td' );",0 ); 
	exit();    	 
}

if ($action=="load_drop_down_line")
{
    $explode_data = explode("_",$data);
	
    $prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$explode_data[2] and variable_list=23 and is_deleted=0 and status_active=1");
    $txt_date = $explode_data[3];
    
    $cond="";
    if($prod_reso_allo==1)
    {
        $line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
        $line_array=array();
        
        if($txt_date=="")
        {
            if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_id= $explode_data[1]";
            if( $explode_data[0]!=0 ) $cond = " and floor_id= $explode_data[0]";
            $line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");
        }
        else
        {
            if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and a.location_id= $explode_data[1]";
            if( $explode_data[0]!=0 ) $cond = " and a.floor_id= $explode_data[0]";
         if($db_type==0)    $data_format="and b.pr_date='".change_date_format($txt_date,'yyyy-mm-dd')."'";
         if($db_type==2)    $data_format="and b.pr_date='".change_date_format($txt_date,'','',1)."'";

             $line_data=sql_select( "select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id $data_format and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
        }
        
        foreach($line_data as $row)
        {
            $line='';
            $line_number=explode(",",$row[csf('line_number')]);
            foreach($line_number as $val)
            {
                if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
            }
            $line_array[$row[csf('id')]]=$line;
        }

        echo create_drop_down( "cbo_line_id", 100,$line_array,"", 1, "-- Select --", $selected, "",0,0 );
    }
    else
    {
        if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name= $explode_data[1]";
        if( $explode_data[0]!=0 ) $cond = " and floor_name= $explode_data[0]";

        echo create_drop_down( "cbo_line_id", 100, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "-- Select --", $selected, "",0,0);
    }
    exit();
	
}

//item style-------------------------------------------------------------------------//
if($action=="style_wise_search")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
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
	if($company==0) $company_name=""; else $company_name="and a.company_name=$company";
	if($buyer==0) $buyer_name=""; else $buyer_name="and a.buyer_name=$buyer";
	/*if(str_replace("'","",$job_id)!="")  $job_cond="and b.id in(".str_replace("'","",$job_id).")";
    else  */if (str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and a.job_no_mst='".$job_no."'";
    if($db_type==2) $year_cond="  extract(year from a.insert_date) as year";
	if($db_type==0) $year_cond="  SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
	
	if($db_type==0)
	{
		$year_field_con=" and SUBSTRING_INDEX(a.insert_date, '-', 1)";
		if($cbo_year!=0) $year_cond_id=" $year_field_con=$cbo_year"; else $year_cond_id="";	
	}
	else
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($cbo_year!=0) $year_cond_id=" $year_field_con=$cbo_year"; else $year_cond_id="";	
	}
	
	$sql = "select a.id, a.style_ref_no, a.job_no_prefix_num, $year_cond from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and  b.shiping_status!=3 $company_name $buyer_name $year_cond_id  group by a.id, a.style_ref_no, a.job_no_prefix_num, a.insert_date order by a.id DESC"; 
	
	echo create_list_view("list_view", "Style Refference,Job no,Year","190,100,100","440","310",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}
if($action=="job_wise_search")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $txt_job_no;
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");

			 
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
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
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					
                    <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                      	<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Order No");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );//txt_job_no
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>'+'**'+'<? echo $type; ?>'+'**'+'<? echo $txt_job_no; ?>', 'create_job_no_search_list_view', 'search_div', 'style_wise_dhu_report_urmi_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$type_id=$data[5];
	$job=$data[6];
	
	//echo $month_id;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
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
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}
	if($job!='') $job_cond="and a.job_no_prefix_num='$job'";else $job_cond="";
	$search_by=$data[2];
	//$search_string="%".trim($data[3])."%";
	$search_value=$data[3];
	//if($search_by==2) $search_field="a.style_ref_no"; else $search_field="a.job_no";
	if($search_by==1 && $search_value!=''){
		$search_con=" and a.job_no like('%$search_value')";	
	}
	else if($search_by==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value%')";	
	}
	else if($search_by==3 && $search_value!=''){
		$search_con=" and b.po_number like('%$search_value%')";	
	}
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";
	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	if($type_id==1)
	{
		$field_type="id,job_no_prefix_num";
	}
	else if($type_id==2)
	{
		$field_type="id,style_ref_no";
	}
	else if($type_id==3)
	{
		$field_type="id,po_number";
	}
	 $sql= "select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.po_number,b.pub_shipment_date, $year_field from wo_po_details_master a,wo_po_break_down b where  b.job_no_mst=a.job_no and a.status_active=1 and a.is_deleted=0 and a.company_name in($company_id) $search_con $buyer_id_cond $year_cond $job_cond  order by a.id desc";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref.,PO No,Ship Date", "120,130,80,50,120,80","750","240",0, $sql , "js_set_value", "$field_type", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no,po_number,pub_shipment_date", "",'','0,0,0,0,0,0,3','') ;
	exit(); 
} // 

//order wise browse------------------------------//
if($action=="order_wise_search")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
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
	
	if($company==0) $company_name=""; else $company_name=" and b.company_name=$company";
	if($buyer==0) $buyer_name=""; else $buyer_name="and b.buyer_name=$buyer";
	if(str_replace("'","",$job_id)!="")  $job_cond="and a.id in(".str_replace("'","",$job_id).")";
    else  if (str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and a.job_no_mst='".$job_no."'";
	if(str_replace("'","",$style_id)!="")  $style_cond="and b.id in(".str_replace("'","",$style_id).")";
    else  if (str_replace("'","",$style_no)=="") $style_cond=""; else $style_cond="and b.style_ref_no='".$style_no."'";
	if($db_type==2) $year_cond="  extract(year from b.insert_date) as year";
	if($db_type==0) $year_cond="  SUBSTRING_INDEX(b.insert_date, '-', 1) as year";
	if($db_type==0)
	{
		$year_field_con=" and SUBSTRING_INDEX(b.insert_date, '-', 1)";
		if($cbo_year!=0) $year_cond_id=" $year_field_con=$cbo_year"; else $year_cond_id="";	
	}
	else
	{
		$year_field_con=" and to_char(b.insert_date,'YYYY')";
		if($cbo_year!=0) $year_cond_id=" $year_field_con=$cbo_year"; else $year_cond_id="";	
	}
	
	$sql = "select distinct a.id,a.po_number,b.style_ref_no,b.job_no_prefix_num,$year_cond from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $company_name $job_cond  $buyer_name $style_cond $year_cond_id";
	echo create_list_view("list_view", "Style Ref,Order Number,Job No, Year","150,150,100,100,","550","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "style_ref_no,po_number,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	// echo "<pre>";
	// print_r($process);
	extract(check_magic_quote_gpc( $process ));	

	$client_arr=return_library_array( "SELECT  a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id   ", "id", "buyer_name");


	$company_library=return_library_array( "SELECT id,company_name from lib_company", "id", "company_name");
	$buyer_arr = return_library_array("SELECT id,short_name from lib_buyer where status_active=1 and is_deleted=0","id","short_name"); 
	$color_arr = return_library_array("SELECT id,color_name from lib_color  where status_active=1 and is_deleted=0","id","color_name"); 
	$season_arr = return_library_array("SELECT id,season_name from lib_buyer_season  where status_active=1 and is_deleted=0","id","season_name"); 
	$floor_arr=return_library_array( "select id, floor_name from   lib_prod_floor", "id", "floor_name");
	$floor_group_arr=return_library_array( "select id, group_name from   lib_prod_floor", "id", "group_name");

	$cbo_company_name=str_replace("'","", $cbo_company_name);

	$cbo_wo_company_name=str_replace("'","", $cbo_wo_company_name);
	$cbo_location=str_replace("'","", $cbo_location);
	$cbo_floor=str_replace("'","", $cbo_floor);
	$cbo_line_id=str_replace("'","", $cbo_line_id);
	$cbo_shift_name=str_replace("'","", $cbo_shift_name);
	$cbo_floor_group_name=str_replace("'","", $cbo_floor_group_name);
	//echo $cbo_floor_group_name;die;

	$cbo_buyer_name=str_replace("'","", $cbo_buyer_name);

	$txt_int_ref=str_replace("'","", $txt_int_ref);

	$txt_job_no=str_replace("'","", $txt_job_no);
	$txt_style_ref=str_replace("'","", $txt_style_ref);
	$txt_po_no=str_replace("'","", $txt_po_no);

	$cbo_production_type=str_replace("'","", $cbo_production_status);
	$txt_date_from=str_replace("'","", $txt_date_from)."";
	$txt_date_to=str_replace("'","", $txt_date_to);
	

	$report_type=str_replace("'","", $report_type); 

	$all_cond="";
	if($cbo_company_name)$all_cond.=" and a.company_name='$cbo_company_name'";
	if($cbo_buyer_name)$all_cond.=" and a.buyer_name='$cbo_buyer_name'";
	if($txt_job_no)$all_cond.=" and a.job_no_prefix_num='$txt_job_no'";
	if($txt_style_ref)$all_cond.=" and a.style_ref_no like '%$txt_style_ref%'";
	if($txt_po_no)$all_cond.=" and b.po_number like '%$txt_po_no%'";

	if($cbo_wo_company_name)$all_cond.=" and c.serving_company='$cbo_wo_company_name'";
	if($cbo_location)$all_cond.=" and c.location='$cbo_location'";
	if($cbo_floor)$all_cond.=" and c.floor_id='$cbo_floor'";
	if($cbo_line_id)$all_cond.=" and c.sewing_line='$cbo_line_id'";
	if($txt_int_ref)$all_cond.=" and b.grouping='$txt_int_ref'";
	if($cbo_shift_name)$all_cond.=" and c.shift_name='$cbo_shift_name'";


	if($txt_date_from !="" && $txt_date_to !="" ){
		$all_cond.=" and c.production_date between'$txt_date_from' and '$txt_date_to'";
		$date_cond_dft = " and a.production_date between '$txt_date_from' and '$txt_date_to'";
	}	  
	
	if($report_type==1) // floor wise
	{
		   $sql="SELECT a.company_name,a.style_ref_no,a.location_name,b.grouping,b.id as po_id ,c.floor_id,c.production_date,c.shift_name, c.serving_company,to_char(c.production_hour,'HH24') as prod_hour,c.sewing_line,c.location,d.production_qnty,d.spot_qty,d.alter_qty,d.reject_qty,c.prod_reso_allo,c.production_type ,c.po_break_down_id   from wo_po_details_master a,wo_po_break_down b,pro_garments_production_mst c ,pro_garments_production_dtls d where a.id=b.job_id and b.id=c.po_break_down_id and c.id=d.mst_id and c.production_type =5  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active = 1 and c.is_deleted = 0  and d.status_active = 1 and d.is_deleted = 0 $all_cond  ";
			//  echo $sql;die;

			$sql_result=sql_select($sql);
			if(count($sql_result)==0)
			{
				echo " <div align='center'><h2  style='color:red'> No Data Found. </h2></div>";die;
			}

			$po_id_arr=array();

			$data_arr=array();
			foreach ($sql_result as  $row) 
			{
				$po_id_arr[$row['PO_ID']]=$row['PO_ID'];

				$data_arr[$row['FLOOR_ID']]['check_qty']+=($row['PRODUCTION_QNTY']+$row['REJECT_QTY']);
				$data_arr[$row['FLOOR_ID']]['pass_qty']+=$row['PRODUCTION_QNTY'];
				$data_arr[$row['FLOOR_ID']]['alter_qnty']+=$row['ALTER_QTY'];
				$data_arr[$row['FLOOR_ID']]['spot_qnty']+=$row['SPOT_QTY'];
				$data_arr[$row['FLOOR_ID']]['reject_qty']+=$row['REJECT_QTY'];
			}
			// echo"<pre>";print_r($po_id_arr);die;

			$con = connect();
			execute_query("DELETE from GBL_TEMP_ENGINE where user_id=$user_id and entry_form =94 and ref_from in(1)");
			oci_commit($con);
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 94, 1,$po_id_arr, $empty_arr);


			$defect_sql="SELECT a.po_break_down_id,a.floor_id,a.production_type,a.production_quantity,b.defect_point_id,b.defect_type_id,b.defect_qty from pro_garments_production_mst a,pro_gmts_prod_dft b, gbl_temp_engine c where a.id=b.mst_id  and b.po_break_down_id=c.ref_val and c.user_id=$user_id  and c.entry_form =94 and c.ref_from=1 and a.production_type=5 and b.defect_type_id in(3,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and defect_qty>0 and  a.company_id in($cbo_company_name)  $date_cond_dft
			";
			// echo $defect_sql;die;
			$result_sql=sql_select($defect_sql);
			$defect_type_arr=array();
			foreach ($result_sql as  $val) 
			{
				$data_Arr[$val['FLOOR_ID']]['defect_qty']+=$val['DEFECT_QTY'];

				$data_Arr[$val['FLOOR_ID']][$val["DEFECT_TYPE_ID"]][$val["DEFECT_POINT_ID"]]['defect_qty']+=$val["DEFECT_QTY"];	

				if($val["DEFECT_QTY"] > 0)
				{
					$defect_type_arr[$val["DEFECT_TYPE_ID"]][$val["DEFECT_POINT_ID"]]=$val["DEFECT_POINT_ID"];
				}
			
			}
			// echo"<pre>";print_r($data_Arr);die;
			// echo"<pre>";print_r($defect_type_arr);die;		 		 
			execute_query("DELETE from GBL_TEMP_ENGINE where user_id=$user_id and entry_form =94 and ref_from in(1,2)");
			oci_commit($con);
			disconnect($con);	
			
			
			$tbl_width = 630+(count($defect_type_arr[2])*60)+(count($defect_type_arr[3])*60)+(count($defect_type_arr[4])*60);
			
			

			 ob_start();
			 ?>
		 
		 <div>
			 <style>
				.block_div {
				width: auto;
				height: auto;
				text-wrap: normal;
				vertical-align: bottom;
				display: block;
				position: !important;
				-webkit-transform: rotate(-90deg);
				-moz-transform: rotate(-90deg);
				}
			 </style>			
			 
		 	<table width="1600" cellspacing="0" >
		 		<tr class="form_caption" style="border:none;">
		 			<td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" >
		 				<strong>Line Wise DHU Report</strong>
		 			</td>
		 		</tr>
		 		<tr class="form_caption" style="border:none;">
		 			<td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" >
		 				<strong><? echo $company_library[$cbo_company_name];?></strong>
		 			</td>
		 		</tr>
		 		<tr class="form_caption" style="border:none;">
					 <td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" >
						<? echo "From:".change_date_format( str_replace("'","",trim($txt_date_from)) )." To ".change_date_format( str_replace("'","",trim($txt_date_to)) ); ?></p>
					</td> 
		 			
		 		</tr>
		 	</table>
			<?
			$defect_sum_array = array();

			$total_check_qty =0;
			$total_pass_qty =0;
			$total_defective_qty =0;						
			$total_defect_qty = 0;
			$total_rej_qty = 0;
			$defective_per=0;

			foreach($data_arr as $flr_key =>$r)
			{

				$check_qty = $r['check_qty']??0;
				$defective_qty =($r['alter_qnty'] + $r['spot_qnty']);
				$defective_per=(($defective_qty *100) / ($check_qty));
				$dhu_per=(($r['defect_qty'] *100)/ ($check_qty));
				$rej_per=(($r['reject_qty'] *100)/ ($check_qty));
				

				$total_check_qty +=$check_qty;
				$total_pass_qty +=$r['pass_qty'];
				$total_defective_qty +=$defective_qty;						
				$total_defect_qty += $data_Arr[$flr_key]['defect_qty'];
				$total_rej_qty += $r['reject_qty'];
				$defective_per=(($total_defective_qty*100) / $total_check_qty);
				$total_dhu_per=(($total_defect_qty*100) / $total_check_qty);
				$total_rej_per=(($total_rej_qty *100) / $total_check_qty);
				//echo ($defective_qty *100)."**" .($check_qty);
	
			
				foreach ($defect_type_arr[3] as $alter_point_id) 
				{
					$defect_sum_array[3][$alter_point_id]+=$data_Arr[$flr_key][3][$alter_point_id]['defect_qty'];
					
				}
			
				foreach ($defect_type_arr[4] as $spot_point_id) 
				{
					$defect_sum_array[4][$spot_point_id]+=$data_Arr[$flr_key][4][$spot_point_id]['defect_qty'];
				}							
						
			}
			
			?>			

		 	<table cellspacing="0" cellpadding="0"  border="1" rules="all"  width="<?=$tbl_width;?>"   class="rpt_table" align="left">
		 		<thead>
					<tr bgcolor="#cddcdc">
		 				<td  width="40"></td>
		 				<td  align="center" width="100"><strong>Sub Total</strong></td>
		 				<td  align="right" width="60"><?=$total_check_qty; ?></td>
		 				<td  align="right" width="60"><?=$total_pass_qty; ?></td>
		 				<td   align="right" width="60"><?=$total_defective_qty;?></td>
		 				<td   align="right" width="60"><?= number_format($defective_per,2);?></td>
		 				<td   align="right" width="60"><?= $total_defect_qty?></td>
		 				<td   align="right" width="60"><?= number_format($total_dhu_per,2);?></td>
						<?
						
						
						foreach ($defect_type_arr[3] as $alter_point_id) 
						{	
							?>
							<td width="60" align="right"><?=$defect_sum_array[3][$alter_point_id];?></td>
							<?
														
						}
						
						foreach ($defect_type_arr[4] as $spot_point_id) 
						{	
							?>
							<td width="60" align="right"><?= $defect_sum_array[4][$spot_point_id]; ?></td>
							<?							
							
						}
						?>
						<td width="60" align="right"><?=$total_rej_qty?></td>
						<td align="right"><?=number_format($total_rej_per,3);?></td>
		 				
		 			</tr>
		 			<tr height="100">
		 				<th class="alignment_css" width="40"><div class="block_div">SL</div></th>
		 				<th class="alignment_css" width="100"><div class="block_div">Unit</div></th>
		 				<th class="alignment_css" width="60"><div class="block_div">Check Qty</div></th>
		 				<th class="alignment_css" width="60"><div class="block_div">Pass Qty</div></th>
		 				<th class="alignment_css" width="60"><div class="block_div">Defective Qty</div></th>
		 				<th class="alignment_css" width="60"><div class="block_div">Defective %</div></th>
		 				<th class="alignment_css" width="60"><div class="block_div">Total Defect Qty</div></th>
		 				<th class="alignment_css" width="60"><div class="block_div">Total DHU %</div></th>
						<?
						
							foreach ($defect_type_arr[3] as $alter_point_id) 
							{
								?>
								<th width="60"><div class="block_div"><?=$sew_fin_alter_defect_type[$alter_point_id];?></div></th>
								<?	
							}

							foreach ($defect_type_arr[4] as $spot_point_id) 
							{
								?>
								<th width="60"><div class="block_div"><?=$sew_fin_spot_defect_type[$spot_point_id];?></div ></th>
								<?								
							}
						?>											
						<th class="alignment_css" width="60"><div class="block_div">Rejection</div></th>
						<th class="alignment_css" ><div class="block_div">Rejection %</div></th>
		 				
		 			</tr>
		 		</thead>		 		 		
				<tbody>
					<?
						$l=1;

						$defect_sum_array = array();

						$total_check_qty =0;
						$total_pass_qty =0;
						$total_defective_qty =0;						
						$total_defect_qty = 0;
						$total_rej_qty = 0;
						$defective_per=0;

						foreach($data_arr as $flr_key =>$r)
						{							
							?>
								<?
									$check_qty = $r['check_qty']??0;
									$defective_qty =($r['alter_qnty'] + $r['spot_qnty']);
									$defective_per=(($defective_qty *100) / ($check_qty));
									$dhu_per=(($data_Arr[$flr_key]['defect_qty'] *100)/ ($check_qty));
									
									$rej_per=(($r['reject_qty'] *100)/ ($check_qty));

									if ($l%2==0)
				 						$bgcolor="#E9F3FF";
				 					else
				 						$bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $l; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $l; ?>">
									<td width="40"><?=$l;?></td>
									<td  align="center" width="100"><?=$floor_arr[$flr_key];?></td>
									<td  align="right" width="60"><?=$check_qty;?></td>
									<td  align="right" width="60"><?=$r['pass_qty'];?></td>
									<td  align="right" width="60"><?=$defective_qty;?></td>
									<td  align="right" width="60"><?=number_format($defective_per,2);?></td>
									<td  align="right" width="60"><?=number_format($data_Arr[$flr_key]['defect_qty'],0);?></td>
									<td  align="right"width="60"><?=number_format($dhu_per,2);?></td>
									<?
																				
									foreach ($defect_type_arr[3] as $alter_point_id) 
									{
										?>
										<td width="60" align="right"><?=number_format($data_Arr[$flr_key][3][$alter_point_id]['defect_qty'],0);?></td>
										<?
										
										
									}									
									foreach ($defect_type_arr[4] as $spot_point_id) 
									{
										?>
										<td width="60" align="right"><?=number_format($data_Arr[$flr_key][4][$spot_point_id]['defect_qty'],0);?></td>
										<?
									
										
									}
									?>
									<td  align="right" width="60"><?=$r['reject_qty'];?></td>
									<td  align="right" ><?=number_format($rej_per,2);?></td>
									
								</tr>
								
							<?	
							$l++;
							$total_check_qty +=$check_qty;
							$total_pass_qty +=$r['pass_qty'];
							$total_defective_qty +=$defective_qty;						
							$total_defect_qty += $data_Arr[$flr_key]['defect_qty'];
							$total_rej_qty += $r['reject_qty'];


						}	

					?>
				</tbody>
		 	</table>
		</div>
		 <?

	}
	else if($report_type==2) // hour wise
	{
			
			$prod_reso_arr=return_library_array( "SELECT id, line_number from prod_resource_mst",'id','line_number');
			//$line_arr =return_library_array("SELECTselect id, line_name from lib_sewing_line",'id','line_name');
			$lineDataArr = sql_select("select id, line_name, sewing_line_serial from lib_sewing_line where is_deleted=0 and status_active=1
			order by sewing_line_serial"); 

			$min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and  a.company_id=$cbo_company_name and shift_id=1 and pr_date='$txt_date_from'   and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
			// echo $min_shif_start;die;

			$start_time_arr=array();
		
	
			$start_time_data_arr=("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time,TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($cbo_company_name) and  shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");	
			// echo $start_time_data_arr;die;
	
			foreach(sql_select($start_time_data_arr) as $row)
			{
				$start_time_arr[$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
				$start_time_arr[$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
			}
			// echo"<pre>";print_r($start_time_arr);die;
			$prod_start_hour=$start_time_arr[1]['pst'];
			$global_start_lanch=$start_time_arr[1]['lst'];
			if($prod_start_hour=="") $prod_start_hour="08:00";
			$start_time=explode(":",$prod_start_hour);
			$hour=(int)substr($start_time[0],1,1); $minutes=$start_time[1]; $last_hour=23;
			$lineWiseProd_arr=array(); $prod_arr=array(); $start_hour_arr=array();
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
			$actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$txt_date)));
			$actual_time=substr(date("Y-m-d H:i:s",strtotime($pc_date_time)),11,2);	
			$acturl_hour_minute=date("H:i",strtotime($pc_date_time));	
			$generated_hourarr=array();
			$first_hour_time=explode(":",$min_shif_start);
			$hour_line=substr($first_hour_time[0],1,1); $minutes_one=$start_time[1];
			$line_start_hour_arr[$hour_line]=$min_shif_start;
			
			for($l=$hour_line;$l<$last_hour;$l++)
			{
				$min_shif_start=add_time($min_shif_start,60);
				$line_start_hour_arr[$l+1]=substr($min_shif_start,0,5);
			}
			
			$line_start_hour_arr[$j+1]='23:59';

			foreach($lineDataArr as $lRow)
			{
				$lineArr[$lRow[csf('id')]]=$lRow[csf('line_name')];
				$lineSerialArr[$lRow[csf('id')]]=$lRow[csf('sewing_line_serial')];
				$lastSlNo=$lRow[csf('sewing_line_serial')];
				$line_n=$lRow[csf('line_name')];
			}
		

			$sql_cond="";
			if($cbo_company_name)$sql_cond.=" and a.company_name='$cbo_company_name'";
			if($cbo_buyer_name)$sql_cond.=" and a.buyer_name='$cbo_buyer_name'";
			if($txt_job_no)$sql_cond.=" and a.job_no_prefix_num='$txt_job_no'";
			if($txt_style_ref)$sql_cond.=" and a.style_ref_no like '%$txt_style_ref%'";
			if($txt_po_no)$sql_cond.=" and b.po_number like '%$txt_po_no%'";

			if($cbo_wo_company_name)$sql_cond.=" and c.serving_company='$cbo_wo_company_name'";
			if($cbo_location)$sql_cond.=" and c.location='$cbo_location'";
			if($cbo_floor)$sql_cond.=" and c.floor_id='$cbo_floor'";
			if($txt_int_ref)$sql_cond.=" and b.grouping='$txt_int_ref'";

			if($cbo_shift_name)$sql_cond.=" and c.shift_name='$cbo_shift_name'";
			if($cbo_line_id)$sql_cond.=" and c.sewing_line='$cbo_line_id'";
			
			
			if($txt_date_from !="" && $txt_date_to !="" ){
				$sql_cond.=" and c.production_date between '$txt_date_from' and '$txt_date_to'";
				$date_cond_dft = " and a.production_date between '$txt_date_from' and '$txt_date_to'";
			}	  

			if($cbo_floor_group_name!='0' && $cbo_floor=="")
			{
				$floor_group_sql=sql_select("SELECT   id  FROM lib_prod_floor where group_name ='$cbo_floor_group_name' and status_active=1 ");
				
				$floor_group_arr=array();
				foreach($floor_group_sql as $fl)
				{
					$floor_group_arr[$fl[csf("id")]]=$fl[csf("id")];
				}
				$all_floor_by_group=implode(",",$floor_group_arr);

				$floor_cond.= " and c.floor_id in ($all_floor_by_group) ";
				// $sql_cond.= " and c.floor_id in ($all_floor_by_group) ";	

			}
			
			$sql="SELECT a.company_name,a.style_ref_no,a.location_name,b.grouping,b.id as po_id,c.floor_id,c.production_date, c.serving_company,c.sewing_line,d.production_qnty,d.spot_qty,d.alter_qty,d.reject_qty,c.prod_reso_allo,c.production_type,"; //c.shift_name,
			$first=1;
			for($h=$hour;$h<$last_hour;$h++)
			{
				$bg=$start_hour_arr[$h];
				$end=substr(add_time($start_hour_arr[$h],60),0,5);
				$prod_hour="prod_hour".substr($bg,0,2);
				if($first==1)
				{
					$sql.="sum(CASE WHEN  TO_CHAR(c.production_hour,'HH24:MI')<'$end' and c.production_type=5 THEN production_qnty else 0 END) AS $prod_hour,";
				}
				else
				{
					$sql.="sum(CASE WHEN TO_CHAR(c.production_hour,'HH24:MI')>='$bg' and TO_CHAR(c.production_hour,'HH24:MI')<'$end' and c.production_type=5 
					THEN production_qnty else 0 END) AS $prod_hour,";
				}
				$first++;
			}
			$sql.="sum(CASE WHEN TO_CHAR(c.production_hour,'HH24:MI')>='$start_hour_arr[$last_hour]' and TO_CHAR(c.production_hour,'HH24:MI')<='$start_hour_arr[24]' and c.production_type=5 THEN production_qnty else 0 END) AS prod_hour23,";

			$first=1;
			for($h=$hour;$h<$last_hour;$h++)
			{
				$bg=$start_hour_arr[$h];
				$end=substr(add_time($start_hour_arr[$h],60),0,5);
				$prod_hour="prod_hour_rej".substr($bg,0,2);
				if($first==1)
				{
					$sql.="sum(CASE WHEN  TO_CHAR(c.production_hour,'HH24:MI')<'$end' and c.production_type=5 THEN reject_qty else 0 END) AS $prod_hour,";
				}
				else
				{
					$sql.="sum(CASE WHEN TO_CHAR(c.production_hour,'HH24:MI')>='$bg' and TO_CHAR(c.production_hour,'HH24:MI')<'$end' and c.production_type=5 
					THEN reject_qty else 0 END) AS $prod_hour,";
				}
				$first++;
			}
			$sql.="sum(CASE WHEN TO_CHAR(c.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(c.production_hour,'HH24:MI')<='$start_hour_arr[24]' and c.production_type=5 THEN reject_qty else 0 END) AS prod_hour_rej23,";

			$first=1;
			for($h=$hour;$h<$last_hour;$h++)
			{
				$bg=$start_hour_arr[$h];
				$end=substr(add_time($start_hour_arr[$h],60),0,5);
				$prod_hour="prod_hour_alt".substr($bg,0,2);
				if($first==1)
				{
					$sql.="sum(CASE WHEN  TO_CHAR(c.production_hour,'HH24:MI')<'$end' and c.production_type=5 THEN alter_qty else 0 END) AS $prod_hour,";
				}
				else
				{
					$sql.="sum(CASE WHEN TO_CHAR(c.production_hour,'HH24:MI')>='$bg' and TO_CHAR(c.production_hour,'HH24:MI')<'$end' and c.production_type=5 
					THEN alter_qty else 0 END) AS $prod_hour,";
				}
				$first++;
			}
			$sql.="sum(CASE WHEN TO_CHAR(c.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(c.production_hour,'HH24:MI')<='$start_hour_arr[24]' and c.production_type=5 THEN alter_qty else 0 END) AS prod_hour_alt23,";

			$first=1;
			for($h=$hour;$h<$last_hour;$h++)
			{
				$bg=$start_hour_arr[$h];
				$end=substr(add_time($start_hour_arr[$h],60),0,5);
				$prod_hour="prod_hour_spt".substr($bg,0,2);
				if($first==1)
				{
					$sql.="sum(CASE WHEN  TO_CHAR(c.production_hour,'HH24:MI')<'$end' and c.production_type=5 THEN spot_qty else 0 END) AS $prod_hour,";
				}
				else
				{
					$sql.="sum(CASE WHEN TO_CHAR(c.production_hour,'HH24:MI')>='$bg' and TO_CHAR(c.production_hour,'HH24:MI')<'$end' and c.production_type=5 
					THEN spot_qty else 0 END) AS $prod_hour,";
				}
				$first++;
			}
			$sql.="sum(CASE WHEN TO_CHAR(c.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(c.production_hour,'HH24:MI')<='$start_hour_arr[24]' and c.production_type=5 THEN spot_qty else 0 END) AS prod_hour_spt23";

			$sql .= " from wo_po_details_master a,wo_po_break_down b,pro_garments_production_mst c ,pro_garments_production_dtls d where a.id=b.job_id and b.id=c.po_break_down_id and c.id=d.mst_id and c.production_type=5  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active = 1 and c.is_deleted = 0  and d.status_active = 1 and d.is_deleted = 0 and d.production_qnty>0 $sql_cond  $floor_cond 
			GROUP BY a.company_name,a.style_ref_no,a.location_name,b.grouping,b.id,c.floor_id,c.production_date, c.serving_company,c.sewing_line,d.production_qnty,d.spot_qty,d.alter_qty,d.reject_qty,c.prod_reso_allo,c.production_type
			";//c.shift_name,
			// echo $sql;die;
			$sql_result=sql_select($sql);
			if(count($sql_result)==0)
			{
				echo " <div align='center'><h2  style='color:red'> No Data Found. </h2></div>";die;
			}

			$po_id_arr=array();

			$data_arr=array();

			foreach ($sql_result as  $row) 
			{
				if($row[csf('prod_reso_allo')]==1)
				{
					$sewing_line_ids=$prod_reso_arr[$row["SEWING_LINE"]];
					$sl_ids_arr = explode(",", $sewing_line_ids);
					foreach($sl_ids_arr as $val)
					{
						if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line=$lineArr[$val];
					}
				}
				else
				{
					$sewing_line=$lineArr[$row["SEWING_LINE"]];
				}
				$sewing_line_serial='';
				if($row[csf('prod_reso_allo')]==1)
				{
					$sewing_line_ids=$prod_reso_arr[$row["SEWING_LINE"]];
					$sl_ids_arr = explode(",", $sewing_line_ids);
					$sewing_line_serial=$lineSerialArr[$sl_ids_arr[0]]; // always take 1st line serial
					
					/* foreach($sl_ids_arr as $val)
					{
						if($sewing_line_serial=='') $sewing_line_serial=$lineSerialArr[$val]; else $sewing_line_serial=$lineSerialArr[$val];
					} */
				}
				else
				{
					$sewing_line_serial=$lineSerialArr[$row["SEWING_LINE"]];
				}
			// $prodHour = explode(":",$row["PROD_HOUR"]);
				for($h=$hour;$h<=$last_hour;$h++)
				{
					$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2)."";
					$prod_hour_rej="prod_hour_rej".substr($start_hour_arr[$h],0,2)."";
					$prod_hour_alt="prod_hour_alt".substr($start_hour_arr[$h],0,2)."";
					$prod_hour_spt="prod_hour_spt".substr($start_hour_arr[$h],0,2)."";

					$data_arr[$prod_hour][$sewing_line_serial][$sewing_line][$prod_hour]+=$row[csf($prod_hour)]; 
					$data_arr[$prod_hour][$sewing_line_serial][$sewing_line]['check_qty'] += ($row[csf($prod_hour)] +$row[csf($prod_hour_rej)]);
					$data_arr[$prod_hour][$sewing_line_serial][$sewing_line]['pass_qty']+=$row[csf($prod_hour)];
					$data_arr[$prod_hour][$sewing_line_serial][$sewing_line]['alter_qnty']+=$row[csf($prod_hour_alt)];
					$data_arr[$prod_hour][$sewing_line_serial][$sewing_line]['spot_qnty']+=$row[csf($prod_hour_spt)];
					$data_arr[$prod_hour][$sewing_line_serial][$sewing_line]['reject_qty']+=$row[csf($prod_hour_rej)];
					$data_arr[$prod_hour][$sewing_line_serial][$sewing_line]['floor_id']=$row['FLOOR_ID'];
					// $data_arr[$prod_hour][$sewing_line_serial][$sewing_line]['shift_name'].=$shift_name[$row['SHIFT_NAME']].',';
				}
				

				$po_id_arr[$row['PO_ID']]=$row['PO_ID'];

				
				}
				// echo"<pre>";print_r($data_arr);die;
				$con = connect();
				execute_query("DELETE from GBL_TEMP_ENGINE where user_id=$user_id and entry_form =94 and ref_from in(2)");
				oci_commit($con);
				fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 94, 2,$po_id_arr, $empty_arr);
	
				
				$sql_cond="";
				if($cbo_company_name)$sql_cond.=" and a.company_id='$cbo_company_name'";

				if($cbo_wo_company_name)$sql_cond.=" and a.serving_company='$cbo_wo_company_name'";
				if($cbo_location)$sql_cond.=" and a.location='$cbo_location'";
				if($cbo_floor)$sql_cond.=" and a.floor_id='$cbo_floor'";

				if($cbo_shift_name)$sql_cond.=" and a.shift_name='$cbo_shift_name'";
				if($cbo_line_id)$sql_cond.=" and a.sewing_line='$cbo_line_id'";

				$defect_sql="SELECT a.po_break_down_id,a.SEWING_LINE,a.PROD_RESO_ALLO,a.floor_id,a.production_type,a.production_quantity,b.DEFECT_POINT_ID,b.DEFECT_TYPE_ID,b.DEFECT_QTY,a.shift_name,"; 
				$first=1;
				for($h=$hour;$h<=$last_hour;$h++)
				{
					$bg=$start_hour_arr[$h];
					$end=substr(add_time($start_hour_arr[$h],60),0,5);
					$prod_hour="prod_hour".substr($bg,0,2);
					if($first==1)
					{
						$defect_sql.="sum(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type=5 THEN DEFECT_QTY else 0 END) AS $prod_hour,";
					}
					else
					{
						$defect_sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg' and TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type=5 
						THEN DEFECT_QTY else 0 END) AS $prod_hour,";
					}
					$first++;
				}
				// $first=1;
				// for($h=$hour;$h<=$last_hour;$h++)
				// {
				// 	$bg=$start_hour_arr[$h];
				// 	$end=substr(add_time($start_hour_arr[$h],60),0,5);
				// 	$prod_hour="prod_hour".substr($bg,0,2);
				// 	if($first==1)
				// 	{
				// 		$defect_sql.="(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 THEN SHIFT_NAME else 0 END) AS $prod_hour,";
				// 	}
				// 	else
				// 	{
				// 		$defect_sql.="(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$bg' and TO_CHAR(a.production_hour,'HH24:MI')<='$end' and a.production_type=5 
				// 		THEN SHIFT_NAME else 0 END) AS $prod_hour,";
				// 	}
				// 	$first++;
				// }
				$defect_sql.="sum(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=5 THEN DEFECT_QTY else 0 END) AS prod_hour23 
				 from pro_garments_production_mst a,pro_gmts_prod_dft b, gbl_temp_engine c where a.id=b.mst_id  and b.po_break_down_id=c.ref_val and c.user_id=$user_id  and c.entry_form =94 and c.ref_from=2 and a.production_type=5 and b.defect_type_id in(3,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id in($cbo_company_name) and b.DEFECT_QTY>0  $sql_cond $date_cond_dft GROUP BY a.prod_reso_allo,a.po_break_down_id, a.SEWING_LINE, a.floor_id,a.production_type,a.production_quantity,b.DEFECT_POINT_ID,b.DEFECT_TYPE_ID,b.DEFECT_QTY,a.shift_name";
				// echo $defect_sql;die;
				$result_sql=sql_select($defect_sql);
				$defect_type_arr=array();
				// $shift_id_arr=array();
				$defect_data_arr=array();
				foreach ($result_sql as  $row) 
				{				
					if($row['PROD_RESO_ALLO']==1)
					{
						$sewing_line_ids=$prod_reso_arr[$row["SEWING_LINE"]];
						$sl_ids_arr = explode(",", $sewing_line_ids);
						foreach($sl_ids_arr as $val)
						{
							if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line=$lineArr[$val];
						}
					}
					else $sewing_line=$lineArr[$row["SEWING_LINE"]];

					$sewing_line_serial='';
					if($row['PROD_RESO_ALLO']==1)
					{
						$sewing_line_ids=$prod_reso_arr[$row["SEWING_LINE"]];
						$sl_ids_arr = explode(",", $sewing_line_ids);
						$sewing_line_serial=$lineSerialArr[$sl_ids_arr[0]]; // always take 1st line serial
					}
					else $sewing_line_serial=$lineSerialArr[$row["SEWING_LINE"]];

					for($h=$hour;$h<=$last_hour;$h++)
					{
						$bg=$start_hour_arr[$h];
						$end=substr(add_time($start_hour_arr[$h],60),0,5);
						$prod_hour="prod_hour".substr($start_hour_arr[$h],0,2)."";
						$defect_data_arr[$prod_hour][$sewing_line_serial][$sewing_line][$prod_hour]+=$row[csf($prod_hour)]; 
						$defect_data_arr[$prod_hour][$sewing_line_serial][$sewing_line]['defect_qty']+=$row[csf($prod_hour)];
						// if($shift_name[$row[csf($prod_hour)]]!='')
						// {
						// 	$defect_data_arr[$prod_hour][$sewing_line_serial][$sewing_line]['shift_name'][$row[csf($prod_hour)]]=$shift_name[$row[csf($prod_hour)]];
						// }
						
												
						$defect_data_arr[$prod_hour][$sewing_line_serial][$sewing_line][$row["DEFECT_TYPE_ID"]][$row["DEFECT_POINT_ID"]]['defect_qty']+=$row[csf($prod_hour)];						
					}
					

					if($row["DEFECT_QTY"] > 0)
					{
						$defect_type_arr[$row["DEFECT_TYPE_ID"]][$row["DEFECT_POINT_ID"]]=$row["DEFECT_POINT_ID"];
					}
				
				}
				
			// echo"<pre>";print_r($defect_data_arr);die;
			// echo"<pre>";print_r($defect_type_arr);die;

			$sql = "SELECT a.PROD_RESO_ALLO,a.SEWING_LINE,a.shift_name,TO_CHAR(a.production_hour,'HH24') as HOUR from pro_garments_production_mst a, gbl_temp_engine c where a.po_break_down_id=c.ref_val and c.user_id=$user_id  and c.entry_form =94 and c.ref_from=2 and a.production_type=5 and a.status_active=1 $sql_cond $date_cond_dft";
			// echo $sql;die;
			$res = sql_select($sql);
			$shift_id_arr = array();

			foreach ($res as $v) 
			{
				if($v['PROD_RESO_ALLO']==1)
				{
					$sewing_line_ids=$prod_reso_arr[$v["SEWING_LINE"]];
					$sl_ids_arr = explode(",", $sewing_line_ids);
					foreach($sl_ids_arr as $val)
					{
						if($sewing_line=='') $sewing_line=$lineArr[$val]; else $sewing_line=$lineArr[$val];
					}
				}
				else $sewing_line=$lineArr[$v["SEWING_LINE"]];
				if($v['HOUR']*1<7)
				{
					$v['HOUR'] = "06";
				}
				$shift_id_arr[$v['HOUR']][$sewing_line] .= $shift_name[$v['SHIFT_NAME']].",";
			}
			// echo"<pre>";print_r($shift_id_arr);die;

			execute_query("DELETE from GBL_TEMP_ENGINE where user_id=$user_id and entry_form =94 and ref_from in(2)");
			oci_commit($con);
			disconnect($con);	 		 
			
		
		
			
			$tbl_width = 1030+(count($defect_type_arr[2])*60)+(count($defect_type_arr[3])*60)+(count($defect_type_arr[4])*60);

		ob_start();
		?>
		 
		<div id="scroll_body">
			 <style>
				.block_div {
				width: auto;
				height: auto;
				text-wrap: normal;
				vertical-align: bottom;
				display: block;
				position: !important;
				-webkit-transform: rotate(-90deg);
				-moz-transform: rotate(-90deg);
				}
			 </style>		
			 <table width="1600" cellspacing="0" >
			 	<tr class="form_caption" style="border:none;">
			 		<td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" >
			 			<strong>Line Wise DHU Report</strong>
			 		</td>
			 	</tr>
			 	<tr class="form_caption" style="border:none;">
			 		<td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" >
			 			<strong><? echo $company_library[$cbo_company_name];?></strong>
			 		</td>
			 	</tr>
			 	<tr class="form_caption" style="border:none;">
				 <td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" >
						<? echo "From:".change_date_format( str_replace("'","",trim($txt_date_from)) )." To ".change_date_format( str_replace("'","",trim($txt_date_to)) ); ?></p>
					</td> 
			 	</tr>
			 </table>
			 <?
			$defect_sum_array = array();			 
			$total_check_qty =0;
			$total_pass_qty =0;
			$total_defective_qty =0;						
			$total_defect_qty = 0;
			$total_rej_qty = 0;
			$defective_per=0;
			foreach($data_arr as $pr_hour_key =>$pr_hour)
			{
				foreach($pr_hour as $sl_key =>$sl_val)
				{	
					foreach($sl_val as $line =>$r)
					{		
						$check_qty = $r['check_qty']??0;
						$defective_qty =($r['alter_qnty'] + $r['spot_qnty']);
						$defect_qty=$defect_data_arr[$pr_hour_key][$sl_key][$line]['defect_qty'];
						$defective_per=(($defective_qty *100) / ($check_qty));
						$dhu_per=(($defect_qty *100)/ ($check_qty));
						$rej_per=(($r['reject_qty'] *100)/ ($check_qty));						

						$total_check_qty +=$check_qty;
						$total_pass_qty +=$r['pass_qty'];
						$total_defective_qty +=$defective_qty;						
						$total_defect_qty += $defect_qty;
						$total_rej_qty += $r['reject_qty'];
						$defective_per=(($total_defective_qty*100) / $total_check_qty);
						$total_dhu_per=(($total_defect_qty*100) / $total_check_qty);
						$total_rej_per=(($total_rej_qty *100) / $total_check_qty);							
					
						foreach ($defect_type_arr[3] as $alter_point_id) 
						{
							$defect_sum_array[3][$alter_point_id]+=$defect_data_arr[$pr_hour_key][$sl_key][$line][3][$alter_point_id]['defect_qty'];							
						}
					
						foreach ($defect_type_arr[4] as $spot_point_id) 
						{
							$defect_sum_array[4][$spot_point_id]+=$defect_data_arr[$pr_hour_key][$sl_key][$line][4][$spot_point_id]['defect_qty'];
						}							
								
					}
				}
			}		
			?>			


			<table cellspacing="0" cellpadding="0"  border="1" rules="all"  width="<?=$tbl_width;?>"  class="rpt_table" align="left">
			 	<thead>
				 	<tr bgcolor="#cddcdc">
		 			
						<td  align="center" width="100"></td>
						<td  align="center" width="100"></td>
						<td  align="center" width="100"></td>
						<td  align="center" width="100"></td>
						<td  align="center" width="100">Sub Total</td>
						<td  align="right" width="60"><?=$total_check_qty; ?></td>
		 				<td  align="right" width="60"><?=$total_pass_qty; ?></td>
		 				<td   align="right" width="60"><?=$total_defective_qty;?></td>
		 				<td   align="right" width="60"><?= number_format($defective_per,2);?></td>
		 				<td   align="right" width="60"><?= $total_defect_qty?></td>
		 				<td   align="right" width="60"><?= number_format($total_dhu_per,2);?></td>
						<?
												
						foreach ($defect_type_arr[3] as $alter_point_id) 
						{	
							?>
							<td width="60" align="right"><?=$defect_sum_array[3][$alter_point_id];?></td>
							<?
														
						}
						
						foreach ($defect_type_arr[4] as $spot_point_id) 
						{	
							?>
							<td width="60" align="right"><?= $defect_sum_array[4][$spot_point_id]; ?></td>
							<?							
							
						}
						?>
						<td width="60" align="right"><?=$total_rej_qty?></td>
						<td align="right"><?=number_format($total_rej_per,3);?></td>
		 				
		 			</tr>
				 	<tr height="100">
						<th class="alignment_css" width="100"><div class="block_div">Hours</div></th>
						<th class="alignment_css" width="100"><div class="block_div">Line</div></th>
						<th class="alignment_css" width="100"><div class="block_div">Unit</div></th>						
		 				<th class="alignment_css" width="100"><div class="block_div">Group</div></th>
						<th class="alignment_css" width="100"><div class="block_div">Shift</div></th>
		 				<th class="alignment_css" width="60"><div class="block_div">Check Qty</div></th>
		 				<th class="alignment_css" width="60"><div class="block_div">Pass Qty</div></th>
		 				<th class="alignment_css" width="60"><div class="block_div">Defective Qty</div></th>
		 				<th class="alignment_css" width="60"><div class="block_div">Defective %</div></th>
		 				<th class="alignment_css" width="60"><div class="block_div">Total Defect Qty</div></th>
		 				<th class="alignment_css" width="60"><div class="block_div">Total DHU %</div></th>
						<?
							
							foreach ($defect_type_arr[3] as $alter_point_id) 
							{
								?>
								<th width="60"><div class="block_div"><?=$sew_fin_alter_defect_type[$alter_point_id];?></div></th>
								<?	
							}

							foreach ($defect_type_arr[4] as $spot_point_id) 
							{
								?>
								<th width="60"><div class="block_div"><?=$sew_fin_spot_defect_type[$spot_point_id];?></div ></th>
								<?								
							}
						?>											
						<th class="alignment_css" width="60"><div class="block_div">Rejection</div></th>
						<th class="alignment_css" ><div class="block_div">Rejection %</div></th>
		 				
		 			</tr>
			 	</thead>
				<tbody>
					<?
						$l=1;
						$defect_sum_array = array();
						$total_check_qty =0;
						$total_pass_qty =0;
						$total_defective_qty =0;						
						$total_defect_qty = 0;
						$total_rej_qty = 0;
						$defective_per=0;
						//echo '<pre>';print_r($data_arr);die;
						foreach($data_arr as $pr_hour_key =>$pr_hour)
						{	
							$spot = 0;
							$alter = 0;
							foreach($pr_hour as $sl_key =>$sl_val)
							{	
								foreach($sl_val as $line =>$r)
								{
									$check_qty = $r['check_qty']??0;
									if($check_qty)
									{
										$defective_qty =($r['alter_qnty'] + $r['spot_qnty']);
										/* foreach ($defect_type_arr[3] as $alter_point_id) 
										{
											$alter += $defect_data_arr[$pr_hour_key][$sl_key][$line][3][$alter_point_id]['defect_qty'];
											
										}									
										foreach ($defect_type_arr[4] as $spot_point_id) 
										{
											$spot += $defect_data_arr[$pr_hour_key][$sl_key][$line][4][$alter_point_id]['defect_qty'];
										} */
										// $defective_qty = $alter + $spot;
										// $defective_qty = $defect_data_arr[$pr_hour_key][$sl_key][$line]['defect_qty'];
										$defective_per= ($check_qty>0) ? (($defective_qty *100) / ($check_qty)) : 0 ;
										$defect_qty=$defect_data_arr[$pr_hour_key][$sl_key][$line]['defect_qty'];
										
										$dhu_per=(($defect_qty *100)/ ($check_qty));
										$dhu_per = is_infinite($dhu_per)||is_nan($dhu_per)?0:$dhu_per;
										$rej_per=(($r['reject_qty'] *100)/ ($check_qty));
										$rej_per = is_infinite($rej_per)||is_nan($rej_per)?0:$rej_per;

										if ($l%2==0) $bgcolor="#E9F3FF";									
										else $bgcolor="#FFFFFF";									
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $l; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $l; ?>">
											<td  align="center" width="100"><?=substr($pr_hour_key,9,22);?></td>
											<td  align="center" width="100"><?=$line;?></td>
											<td  align="center" width="100"><?=$floor_arr[$r['floor_id']];?></td>
											<td  align="center" width="100"><?=$floor_group_arr[$r['floor_id']]?></td>
											
											<td  align="center" width="100">
												<?=implode(",",array_unique(array_filter(explode(",",$shift_id_arr[substr($pr_hour_key,9,22)][$line]))));?></td>

											<td  align="right" width="60"><?=$check_qty;?></td>
											<td  align="right" width="60"><?=$r['pass_qty'];?></td>
											<td  align="right" width="60"><?=number_format($defective_qty,0);?></td>
											<td  align="right" width="60"><?=number_format($defective_per,2);?></td>
											<td  align="right" width="60"><?=number_format($defect_qty,0);?></td>
											<td  align="right"width="60"><?=number_format($dhu_per,2);?></td>
											<?
											// $defect_data_arr[$prod_hour][$sewing_line_serial][$sewing_line][$val["DEFECT_TYPE_ID"]][$val["DEFECT_POINT_ID"]]['defect_qty']+=$val["DEFECT_QTY"];									
											foreach ($defect_type_arr[3] as $alter_point_id) 
											{
												?>
												<td width="60" align="right"><? echo number_format($defect_data_arr[$pr_hour_key][$sl_key][$line][3][$alter_point_id]['defect_qty'],0); ?></td>
												<?
											}									
											foreach ($defect_type_arr[4] as $spot_point_id) 
											{
												?>
												<td width="60" align="right"><? echo number_format($defect_data_arr[$pr_hour_key][$sl_key][$line][4][$spot_point_id]['defect_qty'],0); ?></td>
												<?
											}
											?>
											<td  align="right" width="60"><?=$r['reject_qty'];?></td>
											<td  align="right" ><?=number_format($rej_per,2);?></td>
											
										</tr>										
										<?	
										$l++;
										$total_check_qty +=$check_qty;
										$total_pass_qty +=$r['pass_qty'];
										$total_defective_qty +=$defective_qty;						
										$total_defect_qty += $defect_qty;
										$total_rej_qty += $r['reject_qty'];		
									}				
								}	
							}	
						}
					?>
				</tbody>
			</table>
		</div>
		<?
	}
	// echo"<pre>";print_r($defect_data_arr);die;
	
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("$user_id*.xls") as $filename) {
        @unlink($filename);
    }
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename****$type";

	exit();
}


 
?>
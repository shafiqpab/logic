<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$user_name			= $_SESSION['logic_erp']['user_id'];
$data				= $_REQUEST['data'];
$action				= $_REQUEST['action'];

$company_arr		= return_library_array( "select id, company_name from lib_company",'id','company_name');
$color_library		= return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$order_no_library	= return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number"  );
$buyer_arr			= return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$table_arr			= return_library_array( "select id, table_no from lib_cutting_table",'id','table_no');
$floor_arr			= return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');
$brand_arr 			= return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
$count_arr			= return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');

//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select Buyer--", $selected, "" );     	 
	exit();
}

if($action=="style_search_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	//echo $style_id;die;

	?>
   <script>
		function js_set_value( str ) 
		{
			
			$('#txt_selected_no').val(str);
			parent.emailwindow.hide();
		}
    </script>
		

    <?
	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	//$job_year=str_replace("'","",$job_year);
	if($buyer!=0) $buyer_cond=" and a.buyer_name=$buyer"; else $buyer_cond="";
	if($db_type==0)
	{
		if($job_year!=0) $job_year_cond=" and year(a.insert_date)=$job_year"; else $job_year_cond="";
		$select_date=" year(a.insert_date)";
	}
	else if($db_type==2)
	{
		if($job_year!=0) $job_year_cond=" and to_char(a.insert_date,'YYYY')=$job_year"; else $job_year_cond="";
		$select_date=" to_char(a.insert_date,'YYYY')";
	}
	
	 $sql = "SELECT a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$select_date as year from wo_po_details_master a where a.company_name=$company $buyer_cond  and is_deleted=0 order by job_no_prefix_num DESC,$select_date"; 
	//echo $sql; die;
	echo create_list_view("list_view", "Style Ref No,Job No,Year","160,90,100","410","300",0, $sql , "js_set_value", "id,job_no,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","","") ;	
	//echo "<input type='hidden' id='txt_selected_id' />";
	//echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	
	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_ref_no;?>';
	var style_id='<? echo $txt_style_ref_id;?>';
	var style_des='<? echo $txt_style_ref_no;?>';
	//alert(style_id);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k];
			js_set_value(str_ref);
		}
	}
	</script>
    
    <?
	
	exit();
}

if($action=="intref_search_popup")
{
	echo load_html_head_contents("production Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array; var selected_job = new Array; var selected_grouping = new Array; var selected_file_no = new Array;
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
			//alert('Id :'+str[1]);
			//alert('job :'+str[2]);
			//alert('grouping :'+str[3]);
			//alert('File no :'+str[4]);
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {

				selected_id.push( str[1] );
				selected_job.push( str[2] );
				selected_grouping.push( str[3] );
				selected_file_no.push( str[4] );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_job.splice( i, 1 );
				selected_grouping.splice( i, 1 );
				selected_file_no.splice( i, 1 );
			}
			var id = ''; var job = ''; var grouping = ''; var file_no = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				job += selected_job[i] + ',';
				grouping += selected_grouping[i] + ',';
				file_no += selected_file_no[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			job = job.substr( 0, job.length - 1 );
			grouping = grouping.substr( 0, grouping.length - 1 );
			file_no = file_no.substr( 0, file_no.length - 1 );
			
			
			$('#hide_int_ref_id').val(id);
			$('#hide_job').val(job);
			$('#hide_grouping').val(grouping);
			$('#hide_file_no').val(file_no);
		}
    </script>
	
		<input type="hidden" name="hide_int_ref_id" id="hide_int_ref_id" value="" />
        <input type="hidden" name="hide_job" id="hide_job" value="" />
        <input type="hidden" name="hide_grouping" id="hide_grouping" value="" />
        <input type="hidden" name="hide_file_no" id="hide_file_no" value="" />
	<?

		$buyer=str_replace("'","",$buyer);
		$company=str_replace("'","",$company);
		//$job_year=str_replace("'","",$job_year);
		if($buyer!=0) $buyer_cond=" and a.buyer_name=$buyer"; else $buyer_cond="";
		if($db_type==0)
		{
			if($job_year!=0) $job_year_cond=" and year(a.insert_date)=$job_year"; else $job_year_cond="";
			$select_date=" year(a.insert_date)";
		}
		else if($db_type==2)
		{
			if($job_year!=0) $job_year_cond=" and to_char(a.insert_date,'YYYY')=$job_year"; else $job_year_cond="";
			$select_date=" to_char(a.insert_date,'YYYY')";
		}
		$arr=array (0=>$buyer_arr);
		$sql = "SELECT a.id,a.buyer_name,b.grouping,b.file_no,a.job_no,a.job_no_prefix_num,$select_date as year from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company $buyer_cond and b.grouping is not null and a.is_deleted=0 and a.status_active=1 and b.status_active in(1,3) order by job_no_prefix_num DESC,$select_date"; 
		//echo $sql; die;
		echo create_list_view("tbl_list_search", "Buyer,Int. Ref. No,Job No,Year","100,160,90,100","480","400",0, $sql , "js_set_value", "id,job_no,grouping,file_no", "", 1, "buyer_name,0,0,0", $arr, "buyer_name,grouping,job_no_prefix_num,year", "","setFilterGrid('tbl_list_search',-1)","0","",1) ;	
	
  		exit(); 
} 

if($action=="intref_search_popup_old")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	//echo $style_id;die;

	?>
   <script>
		function js_set_value( str ) 
		{			
			$('#txt_selected_no').val(str);
			parent.emailwindow.hide();
		}
    </script>
		

    <?
	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	//$job_year=str_replace("'","",$job_year);
	if($buyer!=0) $buyer_cond=" and a.buyer_name=$buyer"; else $buyer_cond="";
	if($db_type==0)
	{
		if($job_year!=0) $job_year_cond=" and year(a.insert_date)=$job_year"; else $job_year_cond="";
		$select_date=" year(a.insert_date)";
	}
	else if($db_type==2)
	{
		if($job_year!=0) $job_year_cond=" and to_char(a.insert_date,'YYYY')=$job_year"; else $job_year_cond="";
		$select_date=" to_char(a.insert_date,'YYYY')";
	}
	$arr=array (0=>$buyer_arr);
	$sql = "SELECT a.id,a.buyer_name,b.grouping,b.file_no,a.job_no,a.job_no_prefix_num,$select_date as year from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company $buyer_cond  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 order by job_no_prefix_num DESC,$select_date"; 
	//echo $sql; die;
	echo create_list_view("list_view", "Buyer,Int. Ref. No,Job No,Year","100,160,90,100","480","400",0, $sql , "js_set_value", "id,job_no,grouping,file_no", "", 1, "buyer_name,0,0,0", $arr, "buyer_name,grouping,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","","") ;	
	//echo "<input type='hidden' id='txt_selected_id' />";
	//echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	
	exit();
}

if($action=="fileno_search_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	//echo $style_id;die;

	?>
   <script>
		function js_set_value( str ) 
		{			
			$('#txt_selected_no').val(str);
			parent.emailwindow.hide();
		}
    </script>
		

    <?
	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	//$job_year=str_replace("'","",$job_year);
	if($buyer!=0) $buyer_cond=" and a.buyer_name=$buyer"; else $buyer_cond="";
	if($db_type==0)
	{
		if($job_year!=0) $job_year_cond=" and year(a.insert_date)=$job_year"; else $job_year_cond="";
		$select_date=" year(a.insert_date)";
	}
	else if($db_type==2)
	{
		if($job_year!=0) $job_year_cond=" and to_char(a.insert_date,'YYYY')=$job_year"; else $job_year_cond="";
		$select_date=" to_char(a.insert_date,'YYYY')";
	}
	
	$sql = "SELECT a.id,b.grouping,b.file_no,a.job_no,a.job_no_prefix_num,$select_date as year from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company $buyer_cond  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 order by job_no_prefix_num DESC,$select_date"; 
	//echo $sql; die;
	echo create_list_view("list_view", "File. No,Job No,Year","160,90,100","410","300",0, $sql , "js_set_value", "id,job_no,grouping,file_no", "", 1, "0", $arr, "file_no,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","","") ;	
	//echo "<input type='hidden' id='txt_selected_id' />";
	//echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	
	exit();
}


if($action=="create_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$job_no=$data[6];
	if($job_no!='') $job_no_cond="and a.job_no='$job_no'";else $job_no_cond="";
	
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
	$search_string="%".trim($data[3])."%";

	if($search_by==1) 
		$search_field="b.po_number"; 
	else if($search_by==2) 
		$search_field="a.style_ref_no"; 	
	else 
		$search_field="a.job_no";
		
	$start_date =$data[4];
	$end_date =$data[5];	
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),"yyyy-mm-dd")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd")."'";
		}
		else
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$arr=array (0=>$company_library,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql= "select b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $job_no_cond $date_cond order by b.id, b.pub_shipment_date";
	//echo $sql; die;
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,80,50,70,140,170","760","220",0, $sql , "js_set_value", "id,po_number","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3','',1) ;
   exit(); 
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');

	extract($_REQUEST);
	?>
	<script>
	
		function js_set_value(str)
		{
			$("#hide_job_no").val(str); 
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
                    <th>
                        <input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"> 					
                        <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                    </th>
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $sytle_ref_no; ?>', 'create_job_no_search_list_view', 'search_div', 'unique_code_wise_yarn_process_loss_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:5px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	
	
	//var_dump($data);
	
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
		$buyer_id_cond=" and buyer_name=$data[1]";
	}
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";
	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}

	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field  like '$search_string' $buyer_id_cond $year_cond order by job_no";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','','') ;
	exit();
} // Job Search end


if ($action == "report_generate" )
{
	// var_dump($_REQUEST);die;
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$rept_type			= str_replace( "'", "", $type );
	$company_name		= str_replace( "'", "", $cbo_company_name );
	$buyer_name			= str_replace( "'", "", $cbo_buyer_name );
	$ref_no				= str_replace( "'", "", $txt_ref_no);
	$txt_style_ref_id	= str_replace( "'", "", $txt_style_ref_id);
	$job_no				= str_replace( "'", "", $txt_job_no );
	$int_ref			= str_replace( "'", "", $int_ref);
	$file_no			= str_replace( "'", "", $file_no );	
	$from_date		    = str_replace( "'", "", $txt_date_from);
    $to_date		    = str_replace( "'", "", $txt_date_to);

    if($buyer_name>0){$buyerCond="and d.buyer_id=$buyer_name";}	
	
	$sql_date_cond	= "";
	if ($db_type == 0) {
		if ($from_date != "" && $to_date != "")
			$sql_date_cond .= " and a.program_date  between '" . change_date_format($from_date, 'yyyy-mm-dd') . "' and '" . change_date_format($to_date, 'yyyy-mm-dd') . "'";
	}
	else {
		if ($from_date != "" && $to_date != "")
			$sql_date_cond .= " and a.program_date between '" . date("j-M-Y", strtotime($from_date)) . "' and '" . date("j-M-Y", strtotime($to_date)) . "'";
	}

	$sql_cond	= "";
	
	if($company_name>0) $sql_cond=" AND b.company_id=$company_name";
	if($buyer_name>0) $sql_cond.=" AND b.buyer_id=$buyer_name";
	if($job_no !="") $sql_cond.=" AND d.job_no in('". implode("','",explode(",",$job_no)) ."') ";
	if($job_no !="") $sql_cond.=" AND c.file_no in('". implode("','",array_filter(explode(",",$file_no)))."') ";
	if($int_ref !="") $sql_cond.=" AND c.grouping in('". implode("','",explode(",",$int_ref)) ."') ";
	if($ref_no !="") $sql_cond.=" AND d.style_ref_no='$ref_no' ";
	
	

    if ($rept_type == 1) // Show Button
    {

		$MainQuery ="SELECT  a.id,sum(b.program_qnty) as program_qnty, a.program_date, b.buyer_id, b.company_id, c.file_no, c.grouping, d.style_ref_no, d.job_no,b.booking_no 
		from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b, wo_po_break_down c, wo_po_details_master d 
		where a.id=b.dtls_id and b.po_id=c.id and c.job_no_mst=d.job_no and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,3) and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $sql_cond $sql_date_cond
		group by a.id, a.program_date, b.buyer_id, b.company_id, c.file_no, c.grouping, d.style_ref_no, d.job_no,b.booking_no ";
		//echo $MainQuery;die;
     	$MainQueryResult=sql_select($MainQuery);
		
		$MainQueryArr=array();
		$JobArr=array();
		$bookingArr=array();
		$progIdArr=array();
		foreach ($MainQueryResult as $rows) 
		{
			//$all_prog_id.=$rows[csf('id')].",";
			//$MainQueryArr[$rows[csf('job_no')]]['program_qnty'] +=$rows[csf('program_qnty')];
			$MainQueryArr[$rows[csf('job_no')]]['buyer_id'] .=$rows[csf('buyer_id')].',';
			$MainQueryArr[$rows[csf('job_no')]]['file_no'] .=$rows[csf('file_no')].',';
			$MainQueryArr[$rows[csf('job_no')]]['file_no'] .=$rows[csf('file_no')].',';
			$MainQueryArr[$rows[csf('job_no')]]['grouping'] .=$rows[csf('grouping')].',';
			$MainQueryArr[$rows[csf('job_no')]]['style_ref_no'] .=$rows[csf('style_ref_no')].',';
			$MainQueryArr[$rows[csf('job_no')]]['id'] .=$rows[csf('id')].',';
			if($jobNOS!=$rows[csf('job_no')])
			{
				array_push($JobArr,$rows[csf('job_no')]);
				$jobNOS=$rows[csf('job_no')];
			}
			if($bookingNOS!=$rows[csf('booking_no')])
			{
				array_push($bookingArr,$rows[csf('booking_no')]);
				$bookingNOS=$rows[csf('booking_no')];
			}
			array_push($progIdArr,$rows[csf('id')]);
		}
		unset($MainQueryResult);
		//var_dump($MainQueryArr);

		/*$progQntySql ="SELECT  a.id,sum(b.program_qnty) as program_qnty, d.job_no
		from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b, wo_po_break_down c, wo_po_details_master d 
		where a.id=b.dtls_id and b.po_id=c.id and c.job_no_mst=d.job_no and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
		".where_con_using_array($JobArr,'1','d.job_no')." 
		group by a.id, d.job_no";*/

		$progQntySql = "select a.booking_no,c.job_no,
			b.program_qnty as program_qnty, b.id program_id
			from ppl_planning_entry_plan_dtls a,ppl_planning_info_entry_dtls b,wo_booking_dtls c ,ppl_planning_info_entry_mst d 
			where d.id=a.mst_id and a.dtls_id = b.id and a.booking_no = c.booking_no  and a.company_id=$company_name $buyerCond
			and a.status_active=1 and a.is_deleted=0 and a.is_sales!=1
			and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 ".where_con_using_array($bookingArr,'1','a.booking_no')."  ".where_con_using_array($JobArr,'1','c.job_no')."  group by a.booking_no,c.job_no, b.id,b.program_qnty order by b.id";  

		//echo $progQntySql;
		$progQntySqlResult=sql_select($progQntySql);
		$progQntyArr= array();
		foreach ($progQntySqlResult as $row) 
		{
			$progQntyArr[$row[csf('job_no')]]['program_qnty'] +=$row[csf('program_qnty')];
		}
		unset($progQntySqlResult);
		
		$sql_yarn_issue="SELECT a.po_breakdown_id,a.prod_id,sum(a.quantity) as quantity, c.job_no,f.yarn_type
			FROM order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c, inv_transaction d, inv_issue_master e,
			product_details_master f WHERE a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.trans_id=d.id and d.mst_id=e.id and d.prod_id=f.id and d.item_category=1 and a.trans_type=2 and e.issue_purpose=1 and e.issue_basis=3 and e.entry_form=3 and a.entry_form=3 and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.company_id='$company_name' ".where_con_using_array($JobArr,'1','c.job_no')." 
			GROUP BY c.job_no, a.po_breakdown_id,a.prod_id,f.yarn_type ORDER BY c.job_no 
		";
		//echo $sql_yarn_issue;
		$result_sql_yarn_issue=sql_select($sql_yarn_issue);
		$po_arr=array();
		foreach($result_sql_yarn_issue as $row)
		{
			array_push($po_arr,$row[csf('po_breakdown_id')]);
		}

		
		$ret_sql="SELECT c.prod_id, c.po_breakdown_id,d.yarn_type,
			 sum(case when a.knitting_source=1 then c.quantity end ) as inside_return,
			 sum(case when a.knitting_source=3 then c.quantity end ) as outside_return
			 from inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master d where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=d.id and c.trans_id!=0 and a.entry_form=9 and c.entry_form=9 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ".where_con_using_array($po_arr,'0','c.po_breakdown_id')." group by c.prod_id, c.po_breakdown_id,d.yarn_type"; //,sum(case when a.knitting_source in(1,3) then b.cons_quantity end ) as return_qnty
		// echo $ret_sql;
		$ret_sql_result=sql_select($ret_sql); 
		$rtnYarnArr=array();
		$LycrtnYarnArr=array();
		foreach($ret_sql_result as $row)
		{
			if($row[csf('yarn_type')] ==11 || $row[csf('yarn_type')] ==188)
			{	
				$LycrtnYarnArr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_type')]]['return_qnty']+=($row[csf('inside_return')]+$row[csf('outside_return')]);
			}
			else
			{
				$rtnYarnArr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_type')]]['return_qnty']+=($row[csf('inside_return')]+$row[csf('outside_return')]);
			}
		}
		unset($ret_sql_result);
		
		//$issue_purpose_total_issued=0; 
		$yarnIssueArr=array();
		$LycyarnIssueArr=array();
		foreach($result_sql_yarn_issue as $row)
		{
			if($row[csf('yarn_type')] ==11 || $row[csf('yarn_type')] ==188)
			{
				$LycyarnIssueArr[$row[csf('job_no')]]['issue_qnty'] +=$row[csf('quantity')];
				$LycyarnIssueArr[$row[csf('job_no')]]['return_qnty'] += $LycrtnYarnArr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_type')]]['return_qnty'];
			}
			else
			{
				$yarnIssueArr[$row[csf('job_no')]]['issue_qnty'] +=$row[csf('quantity')];
				$yarnIssueArr[$row[csf('job_no')]]['return_qnty'] += $rtnYarnArr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_type')]]['return_qnty'];
			}
			
		}
		unset($result_sql_yarn_issue);

		// echo "select a.job_no, d.id as dtls_id from wo_po_details_master a,wo_po_break_down b,ppl_planning_entry_plan_dtls c, ppl_planning_info_entry_dtls d where a.id=b.job_id and b.id=c.po_id  and c.dtls_id=d.id and a.company_name=$company_name ".where_con_using_array($JobArr,'1','a.job_no')." and
		// a.status_active=1 and a.is_deleted=0 and b.status_active in (1,3) and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
		// group by  a.job_no, d.id";

		$job_result = sql_select("select a.job_no, d.id as dtls_id from wo_po_details_master a,wo_po_break_down b,ppl_planning_entry_plan_dtls c, ppl_planning_info_entry_dtls d where a.id=b.job_id and b.id=c.po_id  and c.dtls_id=d.id and a.company_name=$company_name ".where_con_using_array($JobArr,'1','a.job_no')." and
		a.status_active=1 and a.is_deleted=0 and b.status_active in (1,3) and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
		group by  a.job_no, d.id");
		
		$planInfoDtls = array();
		$knittingQntyArr = array();
		foreach ($job_result as $row)
		{
			array_push($planInfoDtls,$row[csf('dtls_id')]);
		}

		// echo "select a.booking_id, b.grey_receive_qnty as knitting_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($planInfoDtls,'0','a.booking_id')." ";
		$knitting_dataArray = sql_select("select a.booking_id, b.grey_receive_qnty as knitting_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($planInfoDtls,'0','a.booking_id')." ");
	
		$knittingArr=array();
		foreach($knitting_dataArray as $row)
		{
			$knittingArr[$row[csf('booking_id')]]['qnty'] +=$row[csf('knitting_qnty')];
		}
		unset($knitting_dataArray );
	

		foreach ($job_result as $row)
		{
			$knittingQntyArr[$row[csf('job_no')]]['qnty'] += $knittingArr[$row[csf('dtls_id')]]['qnty'];
		}
		unset($job_result );
		//var_dump($knittingQntyArr);

		// ========================================= grey fab ==================================

	
		$grey_fabric_qnty=sql_select("SELECT a.fabric_source,b.job_no,sum(b.grey_fab_qnty) as grey_fab_qnty,sum(b.fin_fab_qnty) as fin_fab_qnty, b.is_short,b.booking_type from wo_booking_mst a, wo_booking_dtls b,  wo_po_break_down c 
		where a.booking_no = b.booking_no and b.po_break_down_id=c.id and a.fabric_source=1 and b.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and a.company_id='$company_name' ".where_con_using_array($JobArr,'1','b.job_no')."  group by a.fabric_source, b.job_no, b.is_short,b.booking_type");
		
		
		$grey_fabric_qnty_array=array();
		foreach($grey_fabric_qnty as $row)
		{
			$grey_fabric_qnty_array[$row[csf("job_no")]][$row[csf("is_short")]][$row[csf("booking_type")]]['grey_fab_qnty'] +=$row[csf("grey_fab_qnty")];
		}
		unset($grey_fabric_qnty);


		// $job_ord_sql="select a.id as job_id, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.remarks, b.id as po_id, b.po_number, b.file_no, b.grouping as ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($JobArr,'1','a.job_no')."";

		// echo $job_ord_sql;

		$sql_job="SELECT b.job_no, a.id from wo_po_details_master b, wo_po_break_down a where b.id=a.job_id and b.company_name=$company_name and a.status_active in (1,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($JobArr,'1','b.job_no')." order by a.id";

		//echo $sql_job;
		$result_job=sql_select( $sql_job );
		$po_id_arr = array();
		foreach ($result_job as $row)
		{
			if($po_id_chaeck[$row[csf('id')]]=="")
			{
				$po_id_chaeck[$row[csf('id')]]=$row[csf('id')];
				$all_po_id.=$row[csf('id')].",";
			}

			$po_id_arr[$row[csf('job_no')]][$row[csf('po_id')]] .=$row[csf('id')].',';
		}

		$all_po_id=chop($all_po_id,",");

	
		if($all_po_id!="")
		{
			$trans_array=array(); 
			$sql_trans="SELECT b.trans_type, b.po_breakdown_id, sum(b.quantity) as qnty from inv_transaction a, order_wise_pro_details b, inv_item_transfer_mst c  where a.id=b.trans_id and c.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(13,82,83) and a.item_category=13 and a.transaction_type in(5,6) and b.trans_type in(5,6) and  c.transfer_criteria=4 and b.po_breakdown_id in($all_po_id)  group by b.trans_type, b.po_breakdown_id";
			//echo $sql_trans;
			
			$result_trans=sql_select( $sql_trans );
			
			foreach ($result_trans as $row)
			{
				$trans_array[$row[csf('po_breakdown_id')]][$row[csf('trans_type')]] +=$row[csf('qnty')];
			}
		}

		


	    ?>
        <fieldset style="width:1220px">
    	<div style="width:100%; " align="center">
    
            <table width="1210" cellpadding="0" cellspacing="0" id="caption">
                <thead>
                    <tr class="" style="border:none;">
                        <td colspan="17" align="center" style="border:none;font-size:16px; font-weight:bold" >Unique No. Wise Yarn Process Loss status </td>
                    </tr>
                   
                </thead>
            </table>
            
            <table width="1210" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                        <th width="30">SL.</th>
                        <th width="160">Buyer</th>
                        <th width="100">File No</th>
                        <th width="130">Style Ref</th>
                        <th width="80">Unique/Ref No</th>
                        <th width="100">Fab Book Qty</th>
                        <th width="130">Prog. Qty</th>
                        <th width="100"> Yarn Issue</th>
                        <th width="100"> Lyc Issue</th>
                        <th width="100">TL Yarn Issue</th>
                        <th width="100">Knit Qty</th>
                        <th width="100">Transfer In</th>
                        <th width="100">Transfer Out</th>
                        <th width="100">TL Grey Qty</th>
                        <th width="80">Yarn Short/Exc</th>
                        <th>Yarn PL%</th>
                    </tr>
                </thead>
                 <?

                 
					//echo $sql_recv;

                    $i=1;$issued_tot_qnty=$return_tot_qnty=$net_yrn_issue_qnty=$lyc_issued_tot_qnty=$lyc_return_tot_qnty=$lyc_net_yrn_issue_qnty=$tl_net_yrn_issue_qnty=$knitting_qnty=$short_ex_qnty=$proces_loss=$fab_greyQty=$program_qnty=$tot_fab_greyQty=$tot_issued_tot_qnty=$tot_return_tot_qnty=$tot_net_yrn_issue_qnty=$tot_lyc_issued_tot_qnty=$tot_lyc_return_tot_qnty=$tot_lyc_net_yrn_issue_qnty=$tot_tl_net_yrn_issue_qnty=$tot_knitting_qnty=$tot_short_ex_qnty=$tot_proces_loss=$tot_trans_in_qty=$tot_trans_out_qty=$tot_tl_knit_qty = 0;


                    foreach($MainQueryArr as $job_no=>$job_data)
                    {
						
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

					
						$buyer_id=array_unique(explode(",",$job_data['buyer_id']));
						$buyer='';
						foreach($buyer_id as $val)
						{
							if($buyer=='') $buyer=$buyer_arr[$val]; else $buyer.=','.$buyer_arr[$val];
						}

						$file_no=array_unique(explode(",",$job_data['file_no']));
						$file='';
						foreach($file_no as $val)
						{
							if($file=='') $file=$val; else $file.=','.$val;
						}

						$style_ref_no=array_unique(explode(",",$job_data['style_ref_no']));
						$style_ref='';
						foreach($style_ref_no as $val)
						{
							if($style_ref=='') $style_ref=$val; else $style_ref.=','.$val;
						}

						$grouping=array_unique(explode(",",$job_data['grouping']));
					
						$internal_ref='';
						foreach($grouping as $val)
						{
							if($internal_ref=='') $internal_ref=$val; else $internal_ref.=','.$val;
						}

						// $progIdsArr=array_unique(explode(",",$job_data['id']));
						// //var_dump($progIdArr);
						// $knittingQnty=0;
						// foreach($progIdsArr as $val)
						// {
						// 	$knittingQnty +=$knittingArr[$val]['qnty'];
						
						// }

						$fab_greyMainQty=$grey_fabric_qnty_array[$job_no][2][1]['grey_fab_qnty'];
						$fab_greyShortQty=$grey_fabric_qnty_array[$job_no][1][1]['grey_fab_qnty'];
						$fab_greySampleQty=$grey_fabric_qnty_array[$job_no][2][4]['grey_fab_qnty'];

						$fab_greyQty=$fab_greyMainQty+$fab_greyShortQty+$fab_greySampleQty;

						//$po_id_arr[$job_no][$row[csf('po_id')]];

						$po_id_no=array_unique(explode(",",chop($po_id_arr[$job_no][$row[csf('po_id')]],',')));
						//var_dump($po_id_no);
						$trans_in_qty=0;
						$trans_out_qty=0;
						$po_ids='';
						foreach($po_id_no as $val)
						{
							$trans_in_qty+=$trans_array[$val][5];
							$trans_out_qty+=$trans_array[$val][6];
							if($po_ids=='') $po_ids=$val; else $po_ids.=','.$val;
						}
					
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="160" align="center" title="<? echo rtrim($buyer,',');?>"><p><? echo rtrim(substr($buyer,0,14),','); ?></p></td>
							<td width="110" align="center" title="<? echo rtrim($file,',');?>"><p><? echo rtrim(substr($file,0,10),','); ?></p></td>
							<td width="130" align="center" title="<? echo rtrim($style_ref,',');?>"><p><? echo rtrim(substr($style_ref,0,14),','); ?></p></td>
							<td width="80" align="center" title="<? echo rtrim($internal_ref,',');?>"><p><? echo rtrim(substr($internal_ref,0,8),','); ?></p></td>
							<td width="100" align="right" title="<? echo 'Main Qnty='.number_format($fab_greyMainQty,2).' & Short Qnty='.number_format($fab_greyShortQty,2).' & Sample Qnty='.number_format($fab_greySampleQty,2);?>"><p>&nbsp;<? echo number_format($fab_greyQty,2); ?></p></td>
							<td width="130" align="right"><p><? echo number_format($progQntyArr[$job_no]['program_qnty'],2); ?></p></td>
							<td width="80" align="right" style="background-color: #d0ece7; ">
							<a href="##" onClick="openmypage_dtls('<? echo $job_no; ?>','yarn_issue','net_yarn_issue')" style="text-decoration: none;">
								<? 
								$issued_tot_qnty= $yarnIssueArr[$job_no]['issue_qnty'];
								$return_tot_qnty = $yarnIssueArr[$job_no]['return_qnty'];
								$net_yrn_issue_qnty = ($issued_tot_qnty-$return_tot_qnty);
							
								echo  number_format($net_yrn_issue_qnty,2);
								?>
							</a>
							</td>
						
							<td width="100" align="right" style="background-color: #d5dbdb;text-decoration: none; ">
							<a href="##" onClick="openmypage_dtls('<? echo $job_no; ?>','yarn_issue','lyc_net_yarn_issue')" style="text-decoration: none;">
								<? 
							$lyc_issued_tot_qnty= $LycyarnIssueArr[$job_no]['issue_qnty'];
							$lyc_return_tot_qnty= $LycyarnIssueArr[$job_no]['return_qnty'];
							$lyc_net_yrn_issue_qnty = ($lyc_issued_tot_qnty-$lyc_return_tot_qnty);
							
							echo  number_format($lyc_net_yrn_issue_qnty,2); 
							?></a></td>
							<td width="100" align="right">
								<? 
								$tl_net_yrn_issue_qnty = ($net_yrn_issue_qnty+$lyc_net_yrn_issue_qnty);
								echo  number_format($tl_net_yrn_issue_qnty,2);  
								?>&nbsp;</td>
							<td width="100" align="right">
								<? 
								$knitting_qnty = $knittingQntyArr[$job_no]['qnty'];
								echo  number_format($knitting_qnty,2);  
								?>&nbsp;</td>
								<td width="100" align="right" style="background-color: #c4d7df    ; ">
								<a href="##" onClick="trans_dtls('<? echo $po_ids; ?>','knit_trans',5)" style="text-decoration: none;">
									<? echo number_format($trans_in_qty,2); ?>
								</a>
								</td>
								<td width="100" align="right" style="background-color: #ebdef0   ; ">
								<a href="##" onClick="trans_dtls('<? echo $po_ids; ?>','knit_trans',6)" style="text-decoration: none;">
									<? echo number_format($trans_out_qty,2); ?>
								</a>
								</td>
								<td width="100" align="right">
									<? $tl_knit_qty =  ($knitting_qnty+$trans_in_qty)-$trans_out_qty;
									echo number_format($tl_knit_qty,2);
									?>
								</td>
							<td width="100" align="right"><?
							$short_ex_qnty = ($tl_net_yrn_issue_qnty-$knitting_qnty);
							echo  number_format($short_ex_qnty,2);
							 ?>&nbsp;</td>
							<td align="right"><? 
							$proces_loss = ($short_ex_qnty/$tl_net_yrn_issue_qnty)*100;
							echo  number_format($proces_loss,2);
							?>&nbsp;</td>
						</tr>
						<?
				

					$program_qnty += $progQntyArr[$job_no]['program_qnty'];
					$tot_fab_greyQty += $fab_greyQty;
					
					$tot_net_yrn_issue_qnty += $net_yrn_issue_qnty;
					$tot_lyc_net_yrn_issue_qnty += $lyc_net_yrn_issue_qnty;
					$tot_tl_net_yrn_issue_qnty += $tl_net_yrn_issue_qnty;
					$tot_knitting_qnty += $knitting_qnty;
					$tot_trans_in_qty += $trans_in_qty;
					$tot_trans_out_qty += $trans_out_qty;
					$tot_tl_knit_qty += $tl_knit_qty;
					$tot_short_ex_qnty += $short_ex_qnty;
					$tot_proces_loss += $proces_loss;

					$i++;
						
                    }
                ?>
                <tfoot>
                    <th colspan="5" align="right"><b>Grand Total :</b></th>
                    <th align="right"><? echo number_format($tot_fab_greyQty,2,'.','');?></th>
                    <th align="right"><? echo number_format($program_qnty,2,'.','');?></th>
                    <th align="right"><? echo number_format($tot_net_yrn_issue_qnty,2,'.','');?></th>
                    <th align="right"><? echo number_format($tot_lyc_net_yrn_issue_qnty,2,'.','');?></th>
                    <th align="right"><? echo number_format($tot_tl_net_yrn_issue_qnty,2,'.','');?></th>
                    <th align="right"><? echo number_format($tot_knitting_qnty,2,'.','');?></th>
                    <th align="right"><? echo number_format($tot_trans_in_qty,2,'.','');?></th>
                    <th align="right"><? echo number_format($tot_trans_out_qty,2,'.','');?></th>
                    <th align="right"><? echo number_format($tot_tl_knit_qty,2,'.','');?></th>
                    <th align="right"><? echo number_format($tot_short_ex_qnty,2,'.','');?></th>
                    <th align="right"><? echo number_format($tot_proces_loss,2,'.','');?></th>
                   
                </tfoot>
            </table>
          
        </div>
        </fieldset>
        <?

    }



	foreach (glob("*.xls") as $filename)
	{		
		@unlink($filename);

	}
	$name=time().".xls";
	$create_new_excel = fopen($name, 'w');	
	$report_data=ob_get_contents();
	ob_clean();
	$is_created = fwrite($create_new_excel,$report_data);
	echo $report_data."####".$name;
	exit();
}

if($action=="yarn_issue")
{
	echo load_html_head_contents("Yarn Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
	</script>	
	<div style="width:700px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:695px; margin-left:3px">
		<div id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="680" cellpadding="0" cellspacing="0">
            	<thead>
					<th colspan="10"><b>
						
						<?
						if($type=='net_yarn_issue')
						{
							echo 'Lot Wise Yarn Issue Summarry without Lycra';
						}
						else if($type=='lyc_net_yarn_issue')
						{
							echo 'Lot Wise Lycra Yarn Issue Summary';	
						} 
						
						?>
					</b></th>
				</thead>
				<thead>
                    <th width="30">SL</th>
                    <th width="165">Yarn Desc</th>
                    <th width="105">Yarn Type</th>
                    <th width="80">Brand</th>
                    <th width="80">Lot</th>
                    <th width="60">Yarn Issue</th>
                    <th width="65">Yarn Iss Rtn</th>
                    <th >Net Yarn Issue</th>
				</thead>
                <?
			
                $i=1; $total_yarn_issue_qnty=0; $total_yarn_issue_qnty_out=0;
				$sql_knitting="SELECT a.po_breakdown_id,a.prod_id,sum(a.quantity) as quantity, c.job_no,f.yarn_type,f.lot, f.brand ,f.yarn_count_id,f.yarn_comp_type1st,f.yarn_comp_percent1st,f.color
				FROM order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c, inv_transaction d, inv_issue_master e,
				product_details_master f WHERE a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.trans_id=d.id and d.mst_id=e.id and d.prod_id=f.id and d.item_category=1 and a.trans_type=2 and e.issue_purpose=1 and e.issue_basis=3 and e.entry_form=3 and a.entry_form=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and e.company_id='$company' and c.job_no ='$job_no' GROUP BY c.job_no, a.po_breakdown_id,a.prod_id,f.yarn_type,f.lot, f.brand ,f.yarn_count_id,f.yarn_comp_type1st,f.yarn_comp_percent1st,f.color ORDER BY c.job_no 
				";
				//echo $sql_knitting;
				$result_knitting=sql_select($sql_knitting);
				$po_arr=array();
				foreach($result_knitting as $row)
				{
					array_push($po_arr,$row[csf('po_breakdown_id')]);
				}
		
				
				$ret_sql="SELECT c.prod_id, c.po_breakdown_id,d.yarn_type,d.lot, d.brand ,d.yarn_count_id,d.yarn_comp_type1st,d.yarn_comp_percent1st,d.color,
					 sum(case when a.knitting_source=1 then c.quantity end ) as inside_return,
					 sum(case when a.knitting_source=3 then c.quantity end ) as outside_return
					 from inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master d where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=d.id and c.trans_id!=0 and a.entry_form=9 and c.entry_form=9 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 ".where_con_using_array($po_arr,'0','c.po_breakdown_id')." group by c.prod_id, c.po_breakdown_id,d.yarn_type,d.lot, d.brand ,d.yarn_count_id,d.yarn_comp_type1st,d.yarn_comp_percent1st,d.color"; 
					//echo $ret_sql;
				$ret_sql_result=sql_select($ret_sql); 
				$rtnYarnArr=array();
				$LycrtnYarnArr=array();
				foreach($ret_sql_result as $row)
				{
					if($row[csf('yarn_type')] ==11 || $row[csf('yarn_type')] ==188)
					{	
						$LycrtnYarnArr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_type')]]['return_qnty']=($row[csf('inside_return')]+$row[csf('outside_return')]);
					}
					else
					{
						$rtnYarnArr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_type')]]['return_qnty']=($row[csf('inside_return')]+$row[csf('outside_return')]);
					}
				}
				unset($ret_sql_result);

				$yarnIssueArr=array();
				$LycyarnIssueArr=array();
				foreach($result_knitting as $row)
				{
					if($row[csf('yarn_type')] ==11 || $row[csf('yarn_type')] ==188)
					{
						$LycyarnIssueArr[$row[csf('lot')]]['lot'] =$row[csf('lot')];
						$LycyarnIssueArr[$row[csf('lot')]]['brand'] =$row[csf('brand')];
						$LycyarnIssueArr[$row[csf('lot')]]['yarn_count_id'] =$row[csf('yarn_count_id')];
						$LycyarnIssueArr[$row[csf('lot')]]['yarn_comp_type1st'] =$row[csf('yarn_comp_type1st')];
						$LycyarnIssueArr[$row[csf('lot')]]['color'] =$row[csf('color')];
						$LycyarnIssueArr[$row[csf('lot')]]['yarn_type'] =$row[csf('yarn_type')];
						$LycyarnIssueArr[$row[csf('lot')]]['issue_qnty'] +=$row[csf('quantity')];
						$LycyarnIssueArr[$row[csf('lot')]]['return_qnty'] += $LycrtnYarnArr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_type')]]['return_qnty'];
					}
					else
					{
						$yarnIssueArr[$row[csf('lot')]]['lot'] =$row[csf('lot')];
						$yarnIssueArr[$row[csf('lot')]]['brand'] =$row[csf('brand')];
						$yarnIssueArr[$row[csf('lot')]]['yarn_count_id'] =$row[csf('yarn_count_id')];
						$yarnIssueArr[$row[csf('lot')]]['yarn_comp_type1st'] =$row[csf('yarn_comp_type1st')];
						$yarnIssueArr[$row[csf('lot')]]['color'] =$row[csf('color')];
						$yarnIssueArr[$row[csf('lot')]]['yarn_type'] =$row[csf('yarn_type')];
						$yarnIssueArr[$row[csf('lot')]]['issue_qnty'] +=$row[csf('quantity')];
						$yarnIssueArr[$row[csf('lot')]]['return_qnty'] += $rtnYarnArr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_type')]]['return_qnty'];
					}
					
				}
				unset($result_knitting);
				
				$IssueMainQuery=array();
			
				if($type=='net_yarn_issue')
				{
					array_push($IssueMainQuery,$yarnIssueArr);
				
				}
				else if($type=='lyc_net_yarn_issue')
				{
					array_push($IssueMainQuery,$LycyarnIssueArr);
				}
				//print_r($IssueMainQuery);
			


				foreach($IssueMainQuery as $row=>$lotdata)
				{
					foreach ($lotdata as $key => $val) 
					{
						//var_dump($val);
					
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="165" align="center"><p>
								<? 
								echo $count_arr[$val['yarn_count_id']].", ".$composition[$val['yarn_comp_type1st']].", ".$color_library[$val['color']];
								?></p></td>
							<td width="105" align="center"><p><? echo $yarn_type[$val['yarn_type']];?></p></td>
							<td width="80" align="center"><p><? echo $brand_arr[$val['brand']]; ?></p></td>
							<td width="80" align="center"><p><? echo $val['lot']; ?></p></td>
							<td width="60" align="right" ><? $issue_qnty = $val['issue_qnty']; echo number_format($issue_qnty,2);?></td>
							<td width="65" align="right"><? $return_qnty = $val['return_qnty']; echo number_format($return_qnty,2); ?></td>
							<td align="right"><? $net_issue_qnty = ($issue_qnty-$return_qnty); echo number_format($net_issue_qnty,2); ?></td>
						</tr>
						<?

					$i++;
					$tot_issue_qnty +=$issue_qnty;
					$tot_return_qnty +=$return_qnty;
					$tot_net_issue_qnty +=$net_issue_qnty;
					}
                }
                ?>
                <tr style="font-weight:bold" >
                    <td align="right"colspan="5" >Total : </td>
                    <td align="right"><? echo number_format($tot_issue_qnty,2);?></td>
                    <td align="right"><? echo number_format($tot_return_qnty,2);?></td>
                    <td align="right"><? echo number_format($tot_net_issue_qnty,2);?></td>
                </tr>
            </table>	
		</div>
	</fieldset>  
	<?
    exit();
}

if($action=="knit_trans")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$sqlWO="select a.fabric_source,b.po_break_down_id,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($order_id)";
	 $resultWo=sql_select($sqlWO);
	 $fab_source_ids="";
		foreach($resultWo as $woRow)
		{
			 $fab_source_ids.=$woRow[csf('fabric_source')].',';
		}
		$fab_source=rtrim($fab_source_ids,',');
		$fab_source_id=array_unique(explode(",",$fab_source));
	?>

	<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
	</script>	
		<div style="width:425px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
		<fieldset style="width:420px; margin-left:7px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="400" cellpadding="0" cellspacing="0">
					<?
					if($type==5){
					?>
					<thead>
						<tr>
							<th colspan="6">Transfer In</th>
						</tr>
						<tr>
							<th width="40">SL</th>
							<th width="115">Transfer Id</th>
							<th width="80">Transfer Date</th>
							<th width="100">From Internal ref</th>
							<th>Grey Qnty</th>
						</tr>
					</thead>
					<?
					
					$i=1; $total_trans_in_qnty=0;
					$sql="SELECT a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details,a.from_samp_dtls_id 
					from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d 
					where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=13 and c.trans_type=5 and c.entry_form in (13,83) and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transfer_criteria=4 
					group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, d.product_name_details,a.from_samp_dtls_id 
					union all
					SELECT a.transfer_system_id, a.transfer_date, a.challan_no, b.from_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details,a.from_samp_dtls_id 
					from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d 
					where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=13 and c.trans_type=5 and c.entry_form in (82) and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transfer_criteria=4
					group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, b.from_order_id, b.from_prod_id, d.product_name_details,a.from_samp_dtls_id 

					";
					
					// $sql="SELECT a.transfer_system_id, a.transfer_date, a.challan_no, b.from_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details,a.from_samp_dtls_id 
					// from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d 
					// where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=13 and c.trans_type=5 and c.entry_form in (82) and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
					// group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, b.from_order_id, b.from_prod_id, d.product_name_details,a.from_samp_dtls_id";
					//echo $sql;
					$result=sql_select($sql);
					$trans_out_from_booking_id = array();
					$fromOrders="";
					foreach($result as $row)
					{
						if(isset($row[csf('from_samp_dtls_id')]))
						{
							$trans_out_from_booking_id[$row[csf('from_samp_dtls_id')]] = $row[csf('from_samp_dtls_id')];
						}
						$fromOrders.=$row[csf('from_order_id')].",";
					}
					$fromOrders=chop($fromOrders,",");

					$po_sql="SELECT id, po_number,grouping from wo_po_break_down where id in($fromOrders)";
					$po_data=sql_select($po_sql);
					foreach($po_data as $row)
					{
						$po_arr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
						$po_arr[$row[csf('id')]]['grouping']=$row[csf('grouping')];

					}

					$non_ord_booking_id_from = implode(",", $trans_out_from_booking_id);
					$wo_non_ord_bookin_no_from = return_library_array( "SELECT id, booking_no from wo_non_ord_samp_booking_dtls where id in($non_ord_booking_id_from)", "id", "booking_no"  );

					foreach($result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
							
						foreach($fab_source_id as $fsid)
						{
							if($fsid==1)
							{
								$row[csf('transfer_qnty')]=$row[csf('transfer_qnty')];
							}
						}
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="40"><? echo $i; ?></td>
							<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
							<td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
							<td width="100"><p><? echo $po_arr[$row[csf('from_order_id')]]['grouping']; ?></p></td>
							<td align="right"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
						</tr>
					<?
						$total_trans_in_qnty+=$row[csf('transfer_qnty')];
					$i++;
					}
					?>
					<tr style="font-weight:bold">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right">Total</td>
						<td align="right"><? echo number_format($total_trans_in_qnty,2);?></td>
					</tr>

					<?
					}
					else
					{
					?>
					<thead>
						<tr>
							<th colspan="6">Transfer Out</th>
						</tr>
						<tr>
							<th width="40">SL</th>
							<th width="115">Transfer Id</th>
							<th width="80">Transfer Date</th>
							<th width="100">To Internal Ref</th>
							<th>Grey Qnty</th>
						</tr>
					</thead>
					<?
					$total_trans_out_qnty=0;

					$sql="SELECT a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id,b.to_prod_id, sum(c.quantity) as transfer_qnty,a.to_samp_dtls_id 
					from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.dtls_id and a.item_category=13 and c.trans_type=6 and c.entry_form in (13,83) and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transfer_criteria=4
					group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, b.to_prod_id,a.to_samp_dtls_id
					union all
					SELECT a.transfer_system_id, a.transfer_date, a.challan_no, b.to_order_id, b.from_prod_id,b.to_prod_id, sum(c.quantity) as transfer_qnty,a.to_samp_dtls_id 
					from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.dtls_id and a.item_category=13 and c.trans_type=6 and c.entry_form in (82) and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transfer_criteria=4
					group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, b.to_order_id, b.from_prod_id, b.to_prod_id,a.to_samp_dtls_id
					";

					// $sql="SELECT a.transfer_system_id, a.transfer_date, a.challan_no, b.to_order_id, b.from_prod_id,b.to_prod_id, sum(c.quantity) as transfer_qnty,a.to_samp_dtls_id 
					// from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c
					// where a.id=b.mst_id and b.id=c.dtls_id and a.item_category=13 and c.trans_type=6 and c.entry_form in (82) and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
					// group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, b.to_order_id, b.from_prod_id, b.to_prod_id,a.to_samp_dtls_id";
					//echo $sql;
					$result=sql_select($sql);
					// ================= getting non order booking id ===================
					$trans_out_to_booking_id = array();
					$toOrders="";
					foreach($result as $row)
					{
						if(isset($row[csf('to_samp_dtls_id')]))
						{
							$trans_out_to_booking_id[$row[csf('to_samp_dtls_id')]] = $row[csf('to_samp_dtls_id')];
						}
						$toOrders.=$row[csf('to_order_id')].",";
					}
					$toOrders=chop($toOrders,",");

					$po_sql_out="SELECT id, po_number,grouping from wo_po_break_down where id in($toOrders)";
					$po_data_out=sql_select($po_sql_out);
					foreach($po_data_out as $row)
					{
						$po_arrs[$row[csf('id')]]['po_number']=$row[csf('po_number')];
						$po_arrs[$row[csf('id')]]['grouping']=$row[csf('grouping')];

					}


					$non_ord_booking_id_to = implode(",", $trans_out_to_booking_id);
					$wo_non_ord_bookin_no_to = return_library_array( "SELECT id, booking_no from wo_non_ord_samp_booking_dtls where id in($non_ord_booking_id_to)", "id", "booking_no"  );

					foreach($result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						foreach($fab_source_id as $fsid)
						{
							if($fsid==1)
							{
								$row[csf('transfer_qnty')]=$row[csf('transfer_qnty')];
							}
							
						}
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="40"><? echo $i; ?></td>
							<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
							<td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
							<td width="100"><p><? echo $po_arrs[$row[csf('to_order_id')]]['grouping']; ?></p></td>
							<td align="right"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
						</tr>
					<?
						$total_trans_out_qnty+=$row[csf('transfer_qnty')];
					$i++;
					}
					?>
					<tr style="font-weight:bold">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right">Total</td>
						<td align="right"><? echo number_format($total_trans_out_qnty,2); ?></td>
					</tr>
					<? } ?>
				</table>	
			</div>
		</fieldset>  
	<?
	exit();
}


?>
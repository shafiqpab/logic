<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');


$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name" );	
$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
$company_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 
$supplier_arr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name"  ); 
$location_arr=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );     	 
	exit(); 
}

if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";
//item style------------------------------//

if ($action=="load_drop_down_location")
{
    extract($_REQUEST);
	echo create_drop_down( "cbo_location_name", 120, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id=$data group by id,location_name  order by location_name","id,location_name", 1, "-- Select --", $selected, "" );
	exit();
}

if($action=="order_popup")
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
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					
					<input type="hidden" id="selected_id" name="selected_id" />
                </thead>
                <tbody>
                	<tr class="general">
                    	<td align="center"> 
							<?
                                echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company_id, "" );
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
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Order No");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>', 'order_search_list_view', 'search_div', 'color_wise_rmg_production_status_v2_controller', 'setFilterGrid(\'table_body2\',-1)');" style="width:100px;" />
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

if ($action=="order_search_list_view")
{
  	//echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($company_id,$buyer_id,$search_type,$search_value,$cbo_year)=explode('**',$data);
	if($company_id==0)
	{
		echo "Please Select Company Name";
		die;
	}
	if($search_value=="")
	{
		echo "Please enter Job, Style or PO Number";
		die;
	}
	//echo $company_id."==".$buyer_id."==".$search_type."==".$search_value."==".$cbo_year;die;
	if($search_type==1 && $search_value!=''){
		$search_con=" and a.job_no like('%$search_value')";	
	}
	else if($search_type==2 && $search_value!=''){
		$search_con=" and a.style_ref_no like('%$search_value%')";	
	}
	else if($search_type==3 && $search_value!=''){
		$search_con=" and b.po_number like('%$search_value%')";	
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
	$sql= "SELECT b.id, a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,$year_field as year ,b.po_number
	from wo_po_details_master a,  wo_po_break_down b 
	where a.id=b.job_id and b.status_active in(1,2,3) and a.company_name=$company_id $buyer_cond $year_cond $search_con 
	group by b.id, a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,a.insert_date,b.po_number
	order by b.id desc";
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
			$po_num=$data[csf('po_number')];
			?>
			<tr bgcolor="<? echo  $bgcolor;?>" onClick="js_set_value('<? echo $data[csf('id')]; ?>'+'_'+'<? echo $data[csf('po_number')]; ?>')" style="cursor:pointer;">
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
                                echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company_id, "" );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>', 'job_popup_search_list_view', 'search_div', 'color_wise_rmg_production_status_v2_controller', 'setFilterGrid(\'table_body2\',-1)');" style="width:100px;" />
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
	if($search_value=="")
	{
		echo "Please enter Job or Style Ref No";
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
	$sql= "SELECT a.id, a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,$year_field as year , $group_field
	from wo_po_details_master a,  wo_po_break_down b 
	where a.job_no=b.job_no_mst and b.status_active in(1,2,3) and a.company_name=$company_id $buyer_cond $year_cond $search_con 
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
			<tr bgcolor="<? echo  $bgcolor;?>" onClick="js_set_value('<? echo $data[csf('id')]; ?>'+'_'+'<? echo $data[csf('style_ref_no')]; ?>')" style="cursor:pointer;">
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

//order wise browse------------------------------//
if($action=="order_wise_search__")
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
$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where file_type=1 and form_name='knit_order_entry'",'master_tble_id','image_location');
$lineArr = return_library_array("select id,line_name from lib_sewing_line where status_active=1","id","line_name");
$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number'); 
$process_arr = array(1,4,5,2,80,8,9);


if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	// ============================= getting form value =============================
	$company_id 		= str_replace("'", "", $cbo_company_name);
	$working_company_id = str_replace("'", "", $cbo_work_company_name);
	// $location_id 		= str_replace("'", "", $cbo_location_name);
	$buyer_id 			= str_replace("'", "", $cbo_buyer_name);
	$style_no 			= str_replace("'", "", $txt_job_no);
	$hidden_job_id 		= str_replace("'", "", $hidden_job_id);
	$order_no 			= str_replace("'", "", $txt_order_no);
	$hidden_order_id 	= str_replace("'", "", $hidden_order_id);
	$process 			= str_replace("'", "", $cbo_process);
	$shipment_status	= str_replace("'", "", $cbo_shipment_status);
	if($type==1) // show button
	{
		$new_process_arr = array();
		if($process=="")
		{			
			$new_process_arr = $process_arr;
		}
		else
		{
			$pa = explode(",", $process);
			$new_process_arr = array_intersect($process_arr, $pa);
		}
		// print_r($new_process_arr);die();

		$sql_cond 		= "";
		$sql_cond .= ($company_id !="") ? " and a.company_name in($company_id)" : "";
		$sql_cond .= ($working_company_id !="") ? " and d.serving_company in($working_company_id)" : "";
		// $sql_cond .= ($location_id !=0) ? " and d.location=$location_id" : "";
		$sql_cond .= ($buyer_id !=0) ? " and a.buyer_name=$buyer_id" : "";
		$sql_cond .= ($style_no !="") ? " and a.style_ref_no='$style_no'" : "";
		$sql_cond .= ($hidden_job_id !="") ? " and a.id=$hidden_job_id" : "";
		$sql_cond .= ($order_no !="") ? " and b.po_number='$order_no'" : "";
		$sql_cond .= ($hidden_order_id !="") ? " and b.id=$hidden_order_id" : "";
		$sql_cond .= ($company_id !=0) ? " and a.company_name=$company_id" : "";
		
		$sql_cond .= ($shipment_status ==3) ? " and b.shiping_status=$shipment_status" : "";
		$sql_cond .= ($shipment_status ==2) ? " and b.shiping_status<3" : "";

		$sql_cond2 		= "";
		$sql_cond2 .= ($company_id !="") ? " and a.company_name in($company_id)" : "";
		$sql_cond2 .= ($working_company_id !="") ? " and d.sending_company in($working_company_id)" : "";
		// $sql_cond2 .= ($location_id !=0) ? " and d.location=$location_id" : "";
		$sql_cond2 .= ($buyer_id !=0) ? " and a.buyer_name=$buyer_id" : "";
		$sql_cond2 .= ($style_no !="") ? " and a.style_ref_no='$style_no'" : "";
		$sql_cond2 .= ($hidden_job_id !="") ? " and a.id=$hidden_job_id" : "";
		$sql_cond2 .= ($order_no !="") ? " and b.po_number='$order_no'" : "";
		$sql_cond2 .= ($hidden_order_id !="") ? " and b.id=$hidden_order_id" : "";
		$sql_cond2 .= ($company_id !=0) ? " and a.company_name=$company_id" : "";
		
		$sql_cond2 .= ($shipment_status ==3) ? " and b.shiping_status=$shipment_status" : "";
		$sql_cond2 .= ($shipment_status ==2) ? " and b.shiping_status<3" : "";

		// ============================== get today prod po ==========================
		$prod_po_arr=return_library_array( "SELECT po_break_down_id,po_break_down_id as po_id from  pro_garments_production_mst where status_active=1 and is_deleted=0 and production_date=$txt_production_date and production_type in(1,2,3,4,5,80,8)", "po_break_down_id", "po_id"  );
		// print_r($prod_po_arr);die;	
		$po_id_cond = where_con_using_array($prod_po_arr,0,"b.id");
		
		// ============================================ FOR PRODUCTION ================================================
		
		$sql="SELECT a.JOB_NO, a.COMPANY_NAME, a.BUYER_NAME, a.STYLE_REF_NO, b.id as PO_ID, b.PO_NUMBER,  c.item_number_id as ITEM_ID,c.color_number_id as COLOR_ID,d.serving_company,d.location,min(d.production_date) as START_DATE,max(d.production_date) as END_DATE,max(b.pub_Shipment_date) as SHIP_DATE,d.production_type as TYPE,d.sewing_line,d.prod_reso_allo,

		sum(case when d.production_type=1 and e.production_type=1 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as TODAY_CUTTING ,
		sum(case when d.production_type=1 and e.production_type=1 and d.production_date<=$txt_production_date then e.production_qnty else 0 end ) as TOTAL_CUTTING ,

		sum(case when d.production_type=4 and e.production_type=4 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as TODAY_SEWING_INPUT ,
		sum(case when d.production_type=4 and e.production_type=4  and d.production_date<=$txt_production_date then e.production_qnty else 0 end ) as TOTAL_SEWING_INPUT ,

		sum(case when d.production_type=5 and e.production_type=5 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as TODAY_SEWING_OUTPUT ,
		sum(case when d.production_type=5 and e.production_type=5  and d.production_date<=$txt_production_date then e.production_qnty else 0 end ) as TOTAL_SEWING_OUTPUT ,

		sum(case when d.production_type=80 and e.production_type=80 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as TODAY_WVN_FINISHING ,
		sum(case when d.production_type=80 and e.production_type=80  and d.production_date<=$txt_production_date then e.production_qnty else 0 end ) as TOTAL_WVN_FINISHING,

		sum(case when d.production_type=8 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as TODAY_FINISHING ,
		sum(case when d.production_type=8  and d.production_date<=$txt_production_date then e.production_qnty else 0 end ) as TOTAL_FINISHING

		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
		where a.id=b.job_id and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.id=c.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0  $sql_cond and d.production_type in(1,4,5,80,8) $po_id_cond
		group by a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id, b.po_number,  c.item_number_id,c.color_number_id,d.serving_company,d.location,d.production_type,d.sewing_line,d.prod_reso_allo order by b.id desc";

		// echo $sql;die;
		$sql_res = sql_select($sql);
		if(count($sql_res)==0)
		{
			?>
			<div style="margin:20px auto; width: 90%">
				<div class="alert alert-error">
				  <strong>Data not found!</strong> Please try again.
				</div>
			</div>
			<?
			disconnect($con);
			die();
		}  

		$data_array = array();
		$prod_date_array = array();
		$po_id_array = array();
		$chk_arr = array();
		foreach($sql_res as $vals)
		{
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["job_no"] = $vals["JOB_NO"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["buyer_name"] = $vals["BUYER_NAME"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["style_ref_no"] = $vals["STYLE_REF_NO"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["style_ref_no"] = $vals["STYLE_REF_NO"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["ship_date"] = $vals["SHIP_DATE"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["job_no"] = $vals["JOB_NO"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["po_number"] = $vals["PO_NUMBER"];

			if($chk_arr[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]=="")
			{			 
				$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["order_quantity"] += $vals["ORDER_QUANTITY"];			 
				$chk_arr[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]] = "azuba";
			}

			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["today_cutting"]+=$vals["TODAY_CUTTING"];			 

			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["total_cutting"]+=$vals["TOTAL_CUTTING"];
			 

			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["today_issue_to_wash"]+=$vals["TODAY_ISSUE_TO_WASH"];
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["total_issue_to_wash"]+=$vals["TOTAL_ISSUE_TO_WASH"];
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["today_rcv_frm_wash"]+=$vals["TODAY_RCV_FRM_WASH"];
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["total_rcv_frm_wash"]+=$vals["TOTAL_RCV_FRM_WASH"];
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["wash_reject"]+=$vals["WASH_REJECT"];


			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["today_sewing_input"]+=$vals["TODAY_SEWING_INPUT"];

			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["total_sewing_input"]+=$vals["TOTAL_SEWING_INPUT"];

			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["today_sewing_output"]+=$vals["TODAY_SEWING_OUTPUT"];

			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["total_sewing_output"]+=$vals["TOTAL_SEWING_OUTPUT"];


			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["today_wvn_finishing"]+=$vals["TODAY_WVN_FINISHING"];

			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["total_wvn_finishing"]+=$vals["TOTAL_WVN_FINISHING"];


			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["today_finishing"]+=$vals["TODAY_FINISHING"];

			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["total_finishing"]+=$vals["TOTAL_FINISHING"];

			$prod_date_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$vals["TYPE"]]['start_date'] = $vals["START_DATE"];
			$prod_date_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$vals["TYPE"]]['end_date'] = $vals["END_DATE"];

			$po_id_array[$vals["PO_ID"]] = $vals["PO_ID"];
		}

		$sql="SELECT a.JOB_NO, a.COMPANY_NAME, a.BUYER_NAME, a.STYLE_REF_NO, b.id as PO_ID, b.PO_NUMBER,  c.item_number_id as ITEM_ID,c.color_number_id as COLOR_ID,d.serving_company,d.location,min(d.production_date) as START_DATE,max(d.production_date) as END_DATE,max(b.pub_Shipment_date) as SHIP_DATE,d.production_type as TYPE,d.sewing_line,d.prod_reso_allo,

		sum(case when d.production_type=2 and e.production_type=2 and d.embel_name=3 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as TODAY_ISSUE_TO_WASH ,
		sum(case when d.production_type=2 and e.production_type=2 and d.embel_name=3 and d.production_date<=$txt_production_date then e.production_qnty else 0 end ) as TOTAL_ISSUE_TO_WASH ,
		sum(case when d.production_type=3 and e.production_type=3 and d.embel_name=3 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as TODAY_RCV_FRM_WASH ,
		sum(case when d.production_type=3 and e.production_type=3 and d.embel_name=3 and d.production_date<=$txt_production_date then e.production_qnty else 0 end ) as TOTAL_RCV_FRM_WASH 

		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
		where a.id=b.job_id and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.id=c.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0  $sql_cond2 and d.production_type in(2,3) $po_id_cond
		group by a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id, b.po_number,  c.item_number_id,c.color_number_id,d.serving_company,d.location,d.production_type,d.sewing_line,d.prod_reso_allo order by b.id desc";
		// echo $sql;
		$sql_res = sql_select($sql);
		$chk_arr = array();
		foreach($sql_res as $vals)
		{
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["job_no"] = $vals["JOB_NO"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["buyer_name"] = $vals["BUYER_NAME"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["style_ref_no"] = $vals["STYLE_REF_NO"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["style_ref_no"] = $vals["STYLE_REF_NO"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["ship_date"] = $vals["SHIP_DATE"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["job_no"] = $vals["JOB_NO"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["po_number"] = $vals["PO_NUMBER"];

			if($chk_arr[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]=="")
			{			 
				$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["order_quantity"] += $vals["ORDER_QUANTITY"];			 
				$chk_arr[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]] = "azuba";
			}			 

			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["today_issue_to_wash"]+=$vals["TODAY_ISSUE_TO_WASH"];
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["total_issue_to_wash"]+=$vals["TOTAL_ISSUE_TO_WASH"];
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["today_rcv_frm_wash"]+=$vals["TODAY_RCV_FRM_WASH"];
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["total_rcv_frm_wash"]+=$vals["TOTAL_RCV_FRM_WASH"];
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["wash_reject"]+=$vals["WASH_REJECT"];

			$prod_date_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$vals["TYPE"]]['start_date'] = $vals["START_DATE"];
			$prod_date_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$vals["TYPE"]]['end_date'] = $vals["END_DATE"];

			$po_id_array[$vals["PO_ID"]] = $vals["PO_ID"];
		}
		// echo "<pre>";print_r($data_array);die;
		$poIds = implode(",", $po_id_array);

		if(count($po_id_array)>999 && $db_type==2)
	    {
	     	$po_chunk=array_chunk($po_id_array, 999);
	     	$po_ids_cond= "";
	     	foreach($po_chunk as $vals)
	     	{
	     		$imp_ids=implode(",", $vals);
	     		if($po_ids_cond=="") 
	     		{
	     			$po_ids_cond.=" and ( a.po_break_down_id in ($imp_ids) ";
	     		}
	     		else
	     		{
	     			$po_ids_cond.=" or a.po_break_down_id in ($imp_ids) ";
	     		}
	     	}
	     	 $po_ids_cond.=" )";
	    }
	    else
	    {
	     	$po_ids_cond= " and a.po_break_down_id in($poIds) ";
	    }

	    // ======================================== order qty =================================================
	    $po_ids_conds = str_replace("a.po_break_down_id", "po_break_down_id", $po_ids_cond);
	    $sql = "SELECT item_number_id as item_id,color_number_id as color_id ,po_break_down_id as po_id, order_quantity from wo_po_color_size_breakdown where status_active in(1,2,3) and is_deleted=0 $po_ids_conds";

	    $sql_res = sql_select($sql);
	    $order_qty_array = array();
	    foreach ($sql_res as $val) 
	    {
	    	$order_qty_array[$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']] += $val['ORDER_QUANTITY'];
	    }
	    // print_r($order_qty_array);
		// ========================================= FOR EX-FACTORY QTY ==========================================
		$ex_factory_arr=array();
		$ex_factory_data="SELECT a.po_break_down_id, a.item_number_id,c.color_number_id, 
		sum(CASE WHEN a.entry_form!=85 and ex_factory_date=$txt_production_date THEN b.production_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN production_qnty ELSE 0 END) AS today_ex_fac , 
		sum(CASE WHEN a.entry_form!=85 THEN b.production_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN production_qnty ELSE 0 END) AS total_ex_fac 
		from pro_ex_factory_delivery_mst d, pro_ex_factory_mst a,pro_ex_factory_dtls b,wo_po_color_size_breakdown c where d.id=a.delivery_mst_id  and  a.id=b.mst_id and b.color_size_break_down_id=c.id   and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_ids_cond group by a.po_break_down_id, a.item_number_id,c.color_number_id";
		// echo $ex_factory_data;die();
		$ex_factory_data_res = sql_select($ex_factory_data);
		foreach($ex_factory_data_res as $exRow)
		{
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('color_number_id')]]['today_ex_fac']+=$exRow[csf('today_ex_fac')];
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('color_number_id')]]['total_ex_fac']=+$exRow[csf('total_ex_fac')];
		}
		// echo "<pre>";
		// print_r($ex_factory_arr);
		// echo "</pre>";
		// die();
		$tbl_width = 760;
		$colspan = 8;
		foreach ($new_process_arr as $key => $val) 
		{
			$tbl_width += ($val==1) ? 320 : 0;
			$tbl_width += ($val==4) ? 320 : 0;
			$tbl_width += ($val==5) ? 400 : 0;
			$tbl_width += ($val==8) ? 240 : 0;
			$tbl_width += ($val==80) ? 240 : 0;
			$tbl_width += ($val==2) ? 480 : 0;
			$tbl_width += ($val==9) ? 240 : 0;

			$colspan += ($val==1) ? 4 : 0;
			$colspan += ($val==4) ? 4 : 0;
			$colspan += ($val==5) ? 5 : 0;
			$colspan += ($val==8) ? 3 : 0;
			$colspan += ($val==80) ? 3 : 0;
			$colspan += ($val==2) ? 6 : 0;
			$colspan += ($val==9) ? 3 : 0;
		}
		// echo $tbl_width;die();
		// =======================================
		$rowspan = array();
		foreach($data_array as $style=>$style_data)
		{
			foreach ($style_data as $job => $job_data) 
			{
				foreach ($job_data as $po_id => $po_data) 
				{
					foreach ($po_data as $item_id => $item_data) 
					{
						foreach ($item_data as $color_id => $row) 
						{
							if($row['total_cutting'] >0 || $row['today_cutting'] >0 || $row['today_sewing_input'] >0 || $row['today_sewing_output'] >0 || $row['today_issue_to_wash'] >0 || $row['today_rcv_frm_wash'] >0)
							{
								$rowspan[$po_id][$item_id]++;
							}
						}
					}
				}
			}
		}
		
		ob_start();	
		?>
		<fieldset style="width:<? echo $tbl_width+20;?>px;">	
			<!-- ============================ Title part ============================ -->
			<div>		
		        <table width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0"> 
		            <tr class="form_caption">
		            	<td colspan="<? echo $colspan;?>" align="center"><strong>Color Wise RMG Production Status V2</strong></td> 
		            </tr>
		            <tr class="form_caption">
		            	<td colspan="<? echo $colspan;?>" align="center"><strong><? echo $company_arr[$company_id]; ?></strong></td> 
		            </tr>
		            <tr class="form_caption">
		            	<td colspan="<? echo $colspan;?>" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_production_date)) ); ?></strong></td> 
		            </tr>
		        </table>
		    </div>
		    <!-- =========================== header part =============================== -->
		    <div>
		    	<table cellspacing="0" width="<? echo $tbl_width;?>" cellpadding="0" border="1" class="rpt_table" rules="all" align="left">
		    		<thead>
		    			<tr>
		    				<th width="100" rowspan="2">Buyer</th>
		    				<th width="100" rowspan="2">Job No</th>
		    				<th width="100" rowspan="2">Style</th>
		    				<th width="100" rowspan="2">Order No.</th>
		    				<th width="100" rowspan="2">Garments Item</th>
		    				<th width="60" rowspan="2">PO Ship Date</th>
		    				<th width="100" rowspan="2">Color</th>
		    				<th width="100" rowspan="2">Order Qty</th>
		    				<? foreach ($new_process_arr as $key => $val) 
		    				{
		    					if($val==1)
		    					{
			    					?>		    					
				    				<th width="320" colspan="4">Cutting</th>
				    				<?
			    				}
			    				if($val==4)
				    			{
				    				?>
				    				<th width="320" colspan="4">Sewing Input</th>
				    				<?
				    			}
			    				if($val==5)
				    			{
				    				?>
				    				<th width="400" colspan="5">Sewing Output</th>
				    				<?
				    			}
			    				if($val==2)
				    			{
				    				?>
				    				<th width="240" colspan="3">Wash Send</th>
				    				<th width="240" colspan="3">Wash Receive</th>
				    				<?
				    			}
			    				if($val==80)
				    			{
				    				?>
				    				<th width="240" colspan="3">Finishing</th>
				    				<?
				    			}
			    				if($val==8)
				    			{
				    				?>
				    				<th width="240" colspan="3">Packing & Finishing</th>
				    				<?
				    			}
			    				if($val==9)
				    			{
				    				?>
				    				<th width="240" colspan="3">Shipment</th>
				    				<?
				    			}
		    				} 
		    				?>
		    			</tr>
		    			<tr>
		    				<? foreach ($new_process_arr as $key => $val) 
		    				{
		    					if($val==1)
		    					{
			    					?>		    					
				    				<th width="80">Start Date</th>
				    				<th width="80">Today</th>
				    				<th width="80">Total</th>
				    				<th width="80">End Date</th>
				    				<?
			    				}
			    				if($val==4)
				    			{
				    				?>
				    				<th width="80">1st Input Date</th>
				    				<th width="80">Today</th>
				    				<th width="80">Total</th>
				    				<th width="80">Balance</th>
				    				<?
				    			}
			    				if($val==5)
				    			{
				    				?>
				    				<th width="80">1st Output Date</th>
				    				<th width="80">Today</th>
				    				<th width="80">Total</th>
				    				<th width="80">Last Output Date</th>
				    				<th width="80">WIP</th>
				    				<?
				    			}
			    				if($val==2)
				    			{
				    				?>
				    				<th width="80">Today</th>
				    				<th width="80">Total</th>
				    				<th width="80">Balance</th>
				    				
				    				<th width="80">Today</th>
				    				<th width="80">Total</th>
				    				<th width="80">Balance</th>
				    				<?
				    			}
			    				if($val==8)
				    			{
				    				?>
				    				<th width="80">Today</th>
				    				<th width="80">Total</th>
				    				<th width="80" title="Finish - Wash Rcv">Balance</th>
				    				<?
				    			}
			    				if($val==8)
				    			{
				    				?>
				    				<th width="80">Today</th>
				    				<th width="80">Total</th>
				    				<th width="80" title="Finish - Wash Rcv">Balance</th>
				    				<?
				    			}
			    				if($val==9)
				    			{
				    				?>
				    				<th width="80">Today</th>
				    				<th width="80">Total</th>
				    				<th width="80">Balance</th>
				    				<?
				    			}
		    				} 
		    				?>		    				
		    			</tr>
		    		</thead>
		    	</table>
		    </div>
		    <!-- ============================== body part ========================= -->
			<div style="max-height:400px; overflow-y:scroll; width:<? echo $tbl_width+20;?>px;float: left;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="<? echo $tbl_width;?>" rules="all" id="table_body" align="left">
					<tbody>
					<?
					$i=1;
					$gt_order_qty = 0;
					$gt_today_cut_qty = 0;
					$gt_total_cut_qty = 0;
					$gt_today_in_qty = 0;
					$gt_total_in_qty = 0;
					$gt_in_bal_qty = 0;
					$gt_today_out_qty = 0;
					$gt_total_out_qty = 0;
					$gt_out_wip_qty = 0;
					$gt_today_wash_iss_qty = 0;
					$gt_total_wash_iss_qty = 0;
					$gt_wash_iss_bal_qty = 0;
					$gt_today_wash_rcv_qty = 0;
					$gt_total_wash_rcv_qty = 0;
					$gt_wash_rcv_bal_qty = 0;
					$gt_today_fin_qty = 0;
					$gt_total_fin_qty = 0;
					$gt_fin_bal_qty = 0;
					$gt_today_ship_qty = 0;
					$gt_total_ship_qty = 0;
					$gt_ship_bal_qty = 0;

					foreach($data_array as $style=>$style_data)
					{
						$style_order_qty = 0;
						$style_today_cut_qty = 0;
						$style_total_cut_qty = 0;
						$style_today_in_qty = 0;
						$style_total_in_qty = 0;
						$style_in_bal_qty = 0;
						$style_today_out_qty = 0;
						$style_total_out_qty = 0;
						$style_out_wip_qty = 0;
						$style_today_wash_iss_qty = 0;
						$style_total_wash_iss_qty = 0;
						$style_wash_iss_bal_qty = 0;
						$style_today_wash_rcv_qty = 0;
						$style_total_wash_rcv_qty = 0;
						$style_wash_rcv_bal_qty = 0;
						$style_today_fin_qty = 0;
						$style_total_fin_qty = 0;
						$style_fin_bal_qty = 0;
						$style_today_ship_qty = 0;
						$style_total_ship_qty = 0;
						$style_ship_bal_qty = 0;
						foreach ($style_data as $job => $job_data) 
						{
							foreach($job_data as $po_id=>$po_data)
							{
								foreach ($po_data as $item_id => $item_data) 
								{
									$itm = 0;
									foreach ($item_data as $color_id => $row) 
									{
										//echo $row['total_cutting'].'---';
										
										if($row['total_cutting'] >0 || $row['today_cutting'] >0 || $row['today_sewing_input'] >0 || $row['today_sewing_output'] >0 || $row['today_issue_to_wash'] >0 || $row['today_rcv_frm_wash'] >0)
										{
											$order_quantity = $order_qty_array[$po_id][$item_id][$color_id];
											$today_ex_fact = $ex_factory_arr[$po_id][$item_id][$color_id]['today_ex_fac'];
											$total_ex_fact = $ex_factory_arr[$po_id][$item_id][$color_id]['total_ex_fac'];
											$ship_bal = $total_ex_fact - $row['total_finishing'];
											$fin_bal = $row['total_finishing'] - $row['total_rcv_frm_wash'];
											$wash_rcv_bal = $row['total_rcv_frm_wash'] - $row['total_issue_to_wash'];
											$wash_iss_bal = $row['total_issue_to_wash'] - $row['total_sewing_output'];
											$out_wip = $row['total_sewing_output'] - $row['total_sewing_input'];
											$input_bal = $row['total_sewing_input'] - $row['total_cutting'];

											$cut_start_date = $prod_date_array[$style][$job][$po_id][$item_id][$color_id][1]['start_date'];
											$cut_end_date = $prod_date_array[$style][$job][$po_id][$item_id][$color_id][1]['end_date'];

											$input_start_date = $prod_date_array[$style][$job][$po_id][$item_id][$color_id][4]['start_date'];
											$input_end_date = $prod_date_array[$style][$job][$po_id][$item_id][$color_id][4]['end_date'];

											$output_start_date = $prod_date_array[$style][$job][$po_id][$item_id][$color_id][5]['start_date'];
											$output_end_date = $prod_date_array[$style][$job][$po_id][$item_id][$color_id][5]['end_date'];

											$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
											// echo "$row[total_sewing_input]<br>";
											?>
											<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
												<? if($itm==0){?>
												<td rowspan="<? echo $rowspan[$po_id][$item_id];?>" width="100"><? echo $buyer_arr[$row['buyer_name']];?></td>
												<td rowspan="<? echo $rowspan[$po_id][$item_id];?>" width="100"><? echo $row['job_no'];?></td>
												<td rowspan="<? echo $rowspan[$po_id][$item_id];?>" width="100"><p><? echo $row['style_ref_no'];?></p></td>
												<td rowspan="<? echo $rowspan[$po_id][$item_id];?>" width="100"><p><? echo $row['po_number'];?></p></td>
												<td rowspan="<? echo $rowspan[$po_id][$item_id];?>" width="100"><p><? echo $garments_item[$item_id];?></p></td>
												<td rowspan="<? echo $rowspan[$po_id][$item_id];?>" width="60" align="center"><? echo change_date_format($row['ship_date']);?></td>
												<? $itm++;}?>
												<td width="100"><p><? echo $color_arr[$color_id];?></p></td>
												<td width="100" align="right"><? echo number_format($order_quantity,0);?></td>
												<? foreach ($new_process_arr as $key => $val) 
							    				{
							    					if($val==1)
							    					{
								    					?>		    					
									    				<td align="center" width="80"><? echo change_date_format($cut_start_date);?></td>
									    				<td align="right" width="80"><? echo $row['today_cutting'];?></td>
									    				<td align="right" width="80"><? echo $row['total_cutting'];?></td>
									    				<td align="center" width="80"><? echo change_date_format($cut_end_date);?></td>
									    				<?
								    				}
								    				if($val==4)
									    			{
									    				?>
									    				<td align="center" width="80"><? echo change_date_format($input_start_date);?></td>
									    				<td align="right" width="80"><? echo $row['today_sewing_input'];?></td>
									    				<td align="right" width="80"><? echo $row['total_sewing_input'];?></td>
									    				<td align="right" width="80"><? echo $input_bal;?></td>
									    				<?
									    			}
								    				if($val==5)
									    			{
									    				?>
									    				<td align="center" width="80"><? echo change_date_format($output_start_date);?></td>
									    				<td align="right" width="80">
									    					<a href="##" onClick="open_report_popup('<? echo $po_id;?>_<? echo $item_id;?>_<? echo $color_id; ?>_<? echo str_replace("'", "", $txt_production_date); ?>','1','open_gmts_popup');">
										    					<? echo $row['today_sewing_output'];?>
										    				</a>						    						
									    				</td>
									    				<td align="right" width="80">
									    					<a href="##" onClick="open_report_popup('<? echo $po_id;?>_<? echo $item_id;?>_<? echo $color_id; ?>_<? echo str_replace("'", "", $txt_production_date); ?>','1_','open_gmts_popup');">
										    					<? echo $row['total_sewing_output'];?>
										    				</a>					    						
									    				</td>
									    				<td align="center" width="80"><? echo change_date_format($output_end_date);?></td>
									    				<td align="right" width="80"><? echo $out_wip;?></td>
									    				<?
									    			}
								    				if($val==2)
									    			{
									    				?>
									    				<td align="right" width="80"><? echo $row['today_issue_to_wash'];?></td>
									    				<td align="right" width="80">
									    					<a href="##" onClick="open_report_popup('<? echo $po_id;?>_<? echo $item_id;?>_<? echo $color_id; ?>_<? echo str_replace("'", "", $txt_production_date); ?>','2','open_wash_popup');">
										    					<? echo $row['total_issue_to_wash'];?>
										    				</a>						    						
									    				</td>
									    				<td align="right" width="80"><? echo $wash_iss_bal;?></td>

									    				<td align="right" width="80"><? echo $row['today_rcv_frm_wash'];?></td>
									    				<td align="right" width="80">
									    					<a href="##" onClick="open_report_popup('<? echo $po_id;?>_<? echo $item_id;?>_<? echo $color_id; ?>_<? echo str_replace("'", "", $txt_production_date); ?>','3','open_wash_popup');">
										    					<? echo $row['total_rcv_frm_wash'];?>
										    				</a>							    						
									    				</td>
									    				<td align="right" width="80"><? echo $wash_rcv_bal;?></td>
									    				<?
									    			}
								    				if($val==80)
									    			{
									    				?>
									    				<td align="right" width="80"><? echo $row['today_wvn_finishing'];?></td>
									    				<td align="right" width="80"><? echo $row['total_wvn_finishing'];?></td>
									    				<td align="right" width="80"><? echo $fin_bal;?></td>
									    				<?
									    			}
								    				if($val==8)
									    			{
									    				?>
									    				<td align="right" width="80"><? echo $row['today_finishing'];?></td>
									    				<td align="right" width="80"><? echo $row['total_finishing'];?></td>
									    				<td align="right" width="80"><? echo $fin_bal;?></td>
									    				<?
									    			}
								    				if($val==9)
									    			{
									    				?>
									    				<td align="right" width="80"><? echo $today_ex_fact;?></td>
									    				<td align="right" width="80"><? echo $total_ex_fact;?></td>
									    				<td align="right" width="80"><? echo $ship_bal;?></td>
									    				<?
									    			}
							    				} 
							    				?>
											</tr>
											<?
											$i++;	
											$style_order_qty += $order_quantity;
											$style_today_cut_qty += $row['today_cutting'];
											$style_total_cut_qty += $row['total_cutting'];
											$style_today_in_qty += $row['today_sewing_input'];
											$style_total_in_qty += $row['total_sewing_input'];
											$style_in_bal_qty += $input_bal;
											$style_today_out_qty += $row['today_sewing_output'];
											$style_total_out_qty += $row['total_sewing_output'];
											$style_out_wip_qty += $out_wip;
											$style_today_wash_iss_qty += $row['today_issue_to_wash'];
											$style_total_wash_iss_qty += $row['total_issue_to_wash'];
											$style_wash_iss_bal_qty += $wash_iss_bal;
											$style_today_wash_rcv_qty += $row['today_rcv_frm_wash'];
											$style_total_wash_rcv_qty += $row['total_rcv_frm_wash'];
											$style_wash_rcv_bal_qty += $wash_rcv_bal;
											$style_today_fin_qty += $row['today_finishing'];
											$style_total_fin_qty += $row['total_finishing'];
											$style_fin_bal_qty += $fin_bal;
											$style_today_ship_qty += $today_ex_fact;
											$style_total_ship_qty += $total_ex_fact;
											$style_ship_bal_qty += $ship_bal;

											$gt_order_qty += $order_quantity;
											$gt_today_cut_qty += $row['today_cutting'];
											$gt_total_cut_qty += $row['total_cutting'];
											$gt_today_in_qty += $row['today_sewing_input'];
											$gt_total_in_qty += $row['total_sewing_input'];
											$gt_in_bal_qty += $input_bal;
											$gt_today_out_qty += $row['today_sewing_output'];
											$gt_total_out_qty += $row['total_sewing_output'];
											$gt_out_wip_qty += $out_wip;
											$gt_today_wash_iss_qty += $row['today_issue_to_wash'];
											$gt_total_wash_iss_qty += $row['total_issue_to_wash'];
											$gt_wash_iss_bal_qty += $wash_iss_bal;
											$gt_today_wash_rcv_qty += $row['today_rcv_frm_wash'];
											$gt_total_wash_rcv_qty += $row['total_rcv_frm_wash'];
											$gt_wash_rcv_bal_qty += $wash_rcv_bal;
											$gt_today_fin_qty += $row['today_finishing'];
											$gt_total_fin_qty += $row['total_finishing'];
											$gt_fin_bal_qty += $fin_bal;
											$gt_today_ship_qty += $today_ex_fact;
											$gt_total_ship_qty += $total_ex_fact;
											$gt_ship_bal_qty += $ship_bal;
										}
									}
								}
							}
						}
						if($style_total_cut_qty>0 || $style_today_in_qty>0 || $style_today_out_qty>0 || $style_today_wash_iss_qty>0 || $style_today_wash_rcv_qty>0)
						{
						?>
						<!-- ======================== style wise subtotal =========================== -->
						<tr bgcolor="#dccdcd" style="font-weight: bold;text-align: right;">
							<td width="100"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="60"></td>
							<td width="100" align="right">Style Total</td>
							<td width="100"><? echo number_format($style_order_qty,0);?></td>
							<? foreach ($new_process_arr as $key => $val) 
		    				{
		    					if($val==1)
		    					{
			    					?>		    					
				    				<td width="80"></td>
				    				<td width="80"><? echo number_format($style_today_cut_qty,0);?></td>
				    				<td width="80"><? echo number_format($style_total_cut_qty,0);?></td>
				    				<td width="80"></td>
				    				<?
			    				}
			    				if($val==4)
				    			{
				    				?>
				    				<td width="80"></td>
				    				<td width="80"><? echo number_format($style_today_in_qty,0);?></td>
				    				<td width="80"><? echo number_format($style_total_in_qty,0);?></td>
				    				<td width="80"></td>
				    				<?
				    			}
			    				if($val==5)
				    			{
				    				?>
				    				<td width="80"></td>
				    				<td width="80"><? echo number_format($style_today_out_qty,0);?></td>
				    				<td width="80"><? echo number_format($style_total_out_qty,0);?></td>
				    				<td width="80"></td>
				    				<td width="80"><? echo number_format($style_out_wip_qty,0);?></td>
				    				<?
				    			}
			    				if($val==2)
				    			{
				    				?>
				    				<td width="80"><? echo number_format($style_today_wash_iss_qty,0);?></td>
				    				<td width="80"><? echo number_format($style_total_wash_iss_qty,0);?></td>
				    				<td width="80"><? echo number_format($style_wash_iss_bal_qty,0);?></td>

				    				<td width="80"><? echo number_format($style_today_wash_rcv_qty,0);?></td>
				    				<td width="80"><? echo number_format($style_total_wash_rcv_qty,0);?></td>
				    				<td width="80"><? echo number_format($style_wash_rcv_bal_qty,0);?></td>
				    				<?
				    			}
			    				if($val==80)
				    			{
				    				?>
				    				<td width="80"><? echo number_format($style_today_fin_qty,0);?></td>
				    				<td width="80"><? echo number_format($style_total_fin_qty,0);?></td>
				    				<td width="80"><? echo number_format($style_fin_bal_qty,0);?></td>
				    				<?
				    			}
			    				if($val==8)
				    			{
				    				?>
				    				<td width="80"><? echo number_format($style_today_fin_qty,0);?></td>
				    				<td width="80"><? echo number_format($style_total_fin_qty,0);?></td>
				    				<td width="80"><? echo number_format($style_fin_bal_qty,0);?></td>
				    				<?
				    			}
			    				if($val==9)
				    			{
				    				?>
				    				<td width="80"><? echo number_format($style_today_ship_qty,0);?></td>
				    				<td width="80"><? echo number_format($style_total_ship_qty,0);?></td>
				    				<td width="80"><? echo number_format($style_ship_bal_qty,0);?></td>
				    				<?
				    			}
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
			<!-- ============================== footer part =============================== -->
			<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="<? echo $tbl_width;?>" rules="all" align="left">
				<tfoot>
					<tr>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="60"></th>
						<th width="100">Grand Total</th>
						<th width="100"><? echo number_format($gt_order_qty,0);?></th>
						<? foreach ($new_process_arr as $key => $val) 
	    				{
	    					if($val==1)
	    					{
		    					?>		    					
			    				<th width="80"></th>
			    				<th width="80"><? echo number_format($gt_today_cut_qty,0);?></th>
			    				<th width="80"><? echo number_format($gt_total_cut_qty,0);?></th>
			    				<th width="80"></th>
			    				<?
		    				}
		    				if($val==4)
			    			{
			    				?>
			    				<th width="80"></th>
			    				<th width="80"><? echo number_format($gt_today_in_qty,0);?></th>
			    				<th width="80"><? echo number_format($gt_total_in_qty,0);?></th>
			    				<th width="80"></th>
			    				<?
			    			}
		    				if($val==5)
			    			{
			    				?>
			    				<th width="80"></th>
			    				<th width="80"><? echo number_format($gt_today_out_qty,0);?></th>
			    				<th width="80"><? echo number_format($gt_total_out_qty,0);?></th>
			    				<th width="80"></th>
			    				<th width="80"><? echo number_format($gt_out_wip_qty,0);?></th>
			    				<?
			    			}
		    				if($val==2)
			    			{
			    				?>
			    				<th width="80"><? echo number_format($gt_today_wash_iss_qty,0);?></th>
			    				<th width="80"><? echo number_format($gt_total_wash_iss_qty,0);?></th>
			    				<th width="80"><? echo number_format($gt_wash_iss_bal_qty,0);?></th>

			    				<th width="80"><? echo number_format($gt_today_wash_rcv_qty,0);?></th>
			    				<th width="80"><? echo number_format($gt_total_wash_rcv_qty,0);?></th>
			    				<th width="80"><? echo number_format($gt_wash_rcv_bal_qtya,0);?></th>
			    				<?
			    			}
		    				if($val==80)
			    			{
			    				?>
			    				<th width="80"><? echo number_format($gt_today_fin_qty,0);?></th>
			    				<th width="80"><? echo number_format($gt_total_fin_qty,0);?></th>
			    				<th width="80"><? echo number_format($gt_fin_bal_qty,0);?></th>
			    				<?
			    			}
		    				if($val==8)
			    			{
			    				?>
			    				<th width="80"><? echo number_format($gt_today_fin_qty,0);?></th>
			    				<th width="80"><? echo number_format($gt_total_fin_qty,0);?></th>
			    				<th width="80"><? echo number_format($gt_fin_bal_qty,0);?></th>
			    				<?
			    			}
		    				if($val==9)
			    			{
			    				?>
			    				<th width="80"><? echo number_format($gt_today_ship_qty,0);?></th>
			    				<th width="80"><? echo number_format($gt_total_ship_qty,0);?></th>
			    				<th width="80"><? echo number_format($gt_ship_bal_qty,0);?></th>
			    				<?
			    			}
	    				} 
	    				?>
					</tr>
				</tfoot>										
			</table>
		</fieldset>
    	<?
	}
	if($type==2) // show2 button
	{
		$new_process_arr = array();
		if($process=="")
		{			
			$new_process_arr = $process_arr;
		}
		else
		{
			$pa = explode(",", $process);
			$new_process_arr = array_intersect($process_arr, $pa);
		}
		// print_r($new_process_arr);die();

		$sql_cond 		= "";
		$sql_cond .= ($company_id !="") ? " and a.company_name in($company_id)" : "";
		$sql_cond .= ($working_company_id !="") ? " and d.serving_company in($working_company_id)" : "";
		// $sql_cond .= ($location_id !=0) ? " and d.location=$location_id" : "";
		$sql_cond .= ($buyer_id !=0) ? " and a.buyer_name=$buyer_id" : "";
		$sql_cond .= ($style_no !="") ? " and a.style_ref_no='$style_no'" : "";
		$sql_cond .= ($hidden_job_id !="") ? " and a.id=$hidden_job_id" : "";
		$sql_cond .= ($order_no !="") ? " and b.po_number='$order_no'" : "";
		$sql_cond .= ($hidden_order_id !="") ? " and b.id=$hidden_order_id" : "";
		$sql_cond .= ($company_id !=0) ? " and a.company_name=$company_id" : "";
		
		$sql_cond .= ($shipment_status ==3) ? " and b.shiping_status=$shipment_status" : "";
		$sql_cond .= ($shipment_status ==2) ? " and b.shiping_status<3" : "";
		
		//echo $sql_cond;die;

		// ============================== get today prod po ==========================
		$prod_po_arr=return_library_array( "SELECT po_break_down_id,po_break_down_id as po_id from  pro_garments_production_mst where status_active=1 and is_deleted=0 and production_date=$txt_production_date and production_type in(1,2,3,4,5,80)", "po_break_down_id", "po_id"  );
		// print_r($prod_po_arr);die;	
		$po_id_cond = where_con_using_array($prod_po_arr,0,"b.id");


		// ============================================ FOR PRODUCTION ================================================
		
		$sql="SELECT a.JOB_NO, a.COMPANY_NAME, a.BUYER_NAME, a.STYLE_REF_NO, b.id as PO_ID, b.PO_NUMBER,  c.item_number_id as ITEM_ID,c.color_number_id as COLOR_ID,d.serving_company,d.location,min(d.production_date) as START_DATE,max(d.production_date) as END_DATE,max(b.pub_Shipment_date) as SHIP_DATE,d.production_type as TYPE,d.sewing_line,d.prod_reso_allo, 
		sum(case when d.production_type=4 and e.production_type=4 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as TODAY_SEWING_INPUT ,
		sum(case when d.production_type=4 and e.production_type=4  and d.production_date<=$txt_production_date then e.production_qnty else 0 end ) as TOTAL_SEWING_INPUT ,

		sum(case when d.production_type=5 and e.production_type=5 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as TODAY_SEWING_OUTPUT ,
		sum(case when d.production_type=5 and e.production_type=5  and d.production_date<=$txt_production_date then e.production_qnty else 0 end ) as TOTAL_SEWING_OUTPUT,
		sum(case when d.production_type=1 and e.production_type=1 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as TODAY_CUTTING,
		sum(case when d.production_type=1 and e.production_type=1 and d.production_date<=$txt_production_date then e.production_qnty else 0 end ) as TOTAL_CUTTING ,

		sum(case when d.production_type=2 and e.production_type=2 and d.embel_name=3 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as TODAY_ISSUE_TO_WASH ,
		sum(case when d.production_type=2 and e.production_type=2 and d.embel_name=3 and d.production_date<=$txt_production_date then e.production_qnty else 0 end ) as TOTAL_ISSUE_TO_WASH ,
		sum(case when d.production_type=3 and e.production_type=3 and d.embel_name=3 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as TODAY_RCV_FRM_WASH ,
		sum(case when d.production_type=3 and e.production_type=3 and d.embel_name=3 and d.production_date<=$txt_production_date then e.production_qnty else 0 end ) as TOTAL_RCV_FRM_WASH ,

		sum(case when d.production_type=80 and e.production_type=80 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as TODAY_WVN_FINISHING ,
		sum(case when d.production_type=80 and e.production_type=80  and d.production_date<=$txt_production_date then e.production_qnty else 0 end ) as TOTAL_WVN_FINISHING ,

		sum(case when d.production_type=8 and e.production_type=8 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as TODAY_FINISHING ,
		sum(case when d.production_type=8 and e.production_type=8  and d.production_date<=$txt_production_date then e.production_qnty else 0 end ) as TOTAL_FINISHING 

		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
		where a.id=b.job_id and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.id=c.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0  $sql_cond $po_id_cond and d.production_type in(1,2,3,4,5,8,80)
		group by a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id, b.po_number,  c.item_number_id,c.color_number_id,d.serving_company,d.location,d.production_type,d.sewing_line,d.prod_reso_allo order by b.id desc";

		// echo $sql;die;
		$sql_res = sql_select($sql);
		if(count($sql_res)==0)
		{
			?>
			<div style="margin:20px auto; width: 90%">
				<div class="alert alert-error">
				  <strong>Data not found!</strong> Change a few things then try submitting again.
				</div>
			</div>
			<?
			disconnect($con);
			die();
		} 

		$data_array = array();
		$others_data_array = array();
		$prod_date_array = array();
		$prod_date_array2 = array();
		$po_id_array = array();
		$chk_arr = array();
		foreach($sql_res as $vals)
		{
			$sewing_line='';
			if($vals['PROD_RESO_ALLO']==1)
			{
				$line_number=explode(",",$prod_reso_arr[$vals['SEWING_LINE']]);
				foreach($line_number as $value)
				{
					if($sewing_line=='') $sewing_line=$lineArr[$value]; else $sewing_line.=",".$lineArr[$value];
				}
			}
			else
			{ 
				$sewing_line=$lineArr[$vals['SEWING_LINE']];
			}
			
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["job_no"] = $vals["JOB_NO"];			
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["buyer_name"] = $vals["BUYER_NAME"];	$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["style_ref_no"] = $vals["STYLE_REF_NO"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["style_ref_no"] = $vals["STYLE_REF_NO"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["ship_date"] = $vals["SHIP_DATE"];	
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["job_no"] = $vals["JOB_NO"];		
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["po_number"] = $vals["PO_NUMBER"];

			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["today_sewing_input"]+=$vals["TODAY_SEWING_INPUT"];
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["total_sewing_input"]+=$vals["TOTAL_SEWING_INPUT"];
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["today_sewing_output"]+=$vals["TODAY_SEWING_OUTPUT"];
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["total_sewing_output"]+=$vals["TOTAL_SEWING_OUTPUT"];
			$prod_date_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line][$vals["TYPE"]]['start_date'] = $vals["START_DATE"];
			$prod_date_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line][$vals["TYPE"]]['end_date'] = $vals["END_DATE"];
			
			$others_data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["today_cutting"]+=$vals["TODAY_CUTTING"];	
			$others_data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["total_cutting"]+=$vals["TOTAL_CUTTING"];
			$others_data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["today_issue_to_wash"]+=$vals["TODAY_ISSUE_TO_WASH"];
			$others_data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["total_issue_to_wash"]+=$vals["TOTAL_ISSUE_TO_WASH"];
			$others_data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["today_rcv_frm_wash"]+=$vals["TODAY_RCV_FRM_WASH"];
			$others_data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["total_rcv_frm_wash"]+=$vals["TOTAL_RCV_FRM_WASH"];
			$others_data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["wash_reject"]+=$vals["WASH_REJECT"];
			$others_data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["today_wvn_finishing"]+=$vals["TODAY_WVN_FINISHING"];
			$others_data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["total_wvn_finishing"]+=$vals["TOTAL_WVN_FINISHING"];
			$others_data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["today_finishing"]+=$vals["TODAY_FINISHING"];
			$others_data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["total_finishing"]+=$vals["TOTAL_FINISHING"];
			$others_data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["total_sewing_output"]+=$vals["TOTAL_SEWING_OUTPUT"];

			$prod_date_array2[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$vals["TYPE"]]['start_date'] = $vals["START_DATE"];
			$prod_date_array2[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$vals["TYPE"]]['end_date'] = $vals["END_DATE"];
			$po_id_array[$vals["PO_ID"]] = $vals["PO_ID"];
			
		}

		$poIds = implode(",", $po_id_array);

		if(count($po_id_array)>999 && $db_type==2)
	    {
	     	$po_chunk=array_chunk($po_id_array, 999);
	     	$po_ids_cond= "";
	     	foreach($po_chunk as $vals)
	     	{
	     		$imp_ids=implode(",", $vals);
	     		if($po_ids_cond=="") 
	     		{
	     			$po_ids_cond.=" and ( a.po_break_down_id in ($imp_ids) ";
	     		}
	     		else
	     		{
	     			$po_ids_cond.=" or a.po_break_down_id in ($imp_ids) ";
	     		}
	     	}
	     	 $po_ids_cond.=" )";
	    }
	    else
	    {
	     	$po_ids_cond= " and a.po_break_down_id in($poIds) ";
	    }	    
	  
	    // ======================================== order qty =================================================
	    $po_ids_conds = str_replace("a.po_break_down_id", "po_break_down_id", $po_ids_cond);
	    $sql = "SELECT item_number_id as item_id,color_number_id as color_id ,po_break_down_id as po_id, order_quantity from wo_po_color_size_breakdown where status_active in(1,2,3) and is_deleted=0 $po_ids_conds";

	    $sql_res = sql_select($sql);
	    $order_qty_array = array();
	    foreach ($sql_res as $val) 
	    {
	    	$order_qty_array[$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']] += $val['ORDER_QUANTITY'];
	    }
	    // print_r($order_qty_array);
		// ========================================= FOR EX-FACTORY QTY ==========================================
		$ex_factory_arr=array();
		$ex_factory_data="SELECT a.po_break_down_id, a.item_number_id,c.color_number_id, 
		sum(CASE WHEN a.entry_form!=85 and ex_factory_date=$txt_production_date THEN b.production_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN production_qnty ELSE 0 END) AS today_ex_fac , 
		sum(CASE WHEN a.entry_form!=85 THEN b.production_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN production_qnty ELSE 0 END) AS total_ex_fac 
		from pro_ex_factory_delivery_mst d, pro_ex_factory_mst a,pro_ex_factory_dtls b,wo_po_color_size_breakdown c where d.id=a.delivery_mst_id  and  a.id=b.mst_id and b.color_size_break_down_id=c.id   and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_ids_cond group by a.po_break_down_id, a.item_number_id,c.color_number_id";
		// echo $ex_factory_data;die();
		$ex_factory_data_res = sql_select($ex_factory_data);
		foreach($ex_factory_data_res as $exRow)
		{
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('color_number_id')]]['today_ex_fac']+=$exRow[csf('today_ex_fac')];
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('color_number_id')]]['total_ex_fac']=+$exRow[csf('total_ex_fac')];
		}
		// echo "<pre>";
		// print_r($ex_factory_arr);
		// echo "</pre>";
		// die();
		$tbl_width = 760;
		$colspan = 8;
		foreach ($new_process_arr as $key => $val) 
		{
			$tbl_width += ($val==1) ? 320 : 0;
			$tbl_width += ($val==4) ? 400 : 0;
			$tbl_width += ($val==5) ? 400 : 0;
			$tbl_width += ($val==8) ? 240 : 0;
			$tbl_width += ($val==80) ? 240 : 0;
			$tbl_width += ($val==2) ? 480 : 0;
			$tbl_width += ($val==9) ? 240 : 0;

			$colspan += ($val==1) ? 4 : 0;
			$colspan += ($val==4) ? 5 : 0;
			$colspan += ($val==5) ? 5 : 0;
			$colspan += ($val==8) ? 3 : 0;
			$colspan += ($val==80) ? 3 : 0;
			$colspan += ($val==2) ? 6 : 0;
			$colspan += ($val==9) ? 3 : 0;
		}
		// echo $tbl_width;die();
		// =======================================
		$rowspan = array();
		$rowspan_color = array();
		foreach($data_array as $style=>$style_data)
		{
			foreach ($style_data as $job => $job_data) 
			{
				foreach ($job_data as $po_id => $po_data) 
				{
					foreach ($po_data as $item_id => $item_data) 
					{
						foreach ($item_data as $color_id => $color_data) 
						{
							foreach ($color_data as $line => $row) 
							{
								 //if($row['total_cutting'] >0 || $row['today_cutting'] >0 || $row['today_sewing_input'] >0 || $row['today_sewing_output'] >0 || $row['today_issue_to_wash'] >0 || $row['today_rcv_frm_wash'] >0)
								 // $row['today_sewing_output']=1;
								
								 if($others_data_array[$style][$job][$po_id][$item_id][$color_id]['total_cutting'] >0 || $others_data_array[$style][$job][$po_id][$item_id][$color_id]['today_cutting']>0 || $row['today_sewing_input'] >0 || $row['today_sewing_output'] >0 || $row['today_issue_to_wash'] >0 || $row['today_rcv_frm_wash'] >0)
								 {
									$rowspan[$po_id][$item_id]++;
									$rowspan_color[$po_id][$item_id][$color_id]++;
								 }
							}
						}
					}
				}
			}
		}
		
		ob_start();	
		?>
		<fieldset style="width:<? echo $tbl_width+20;?>px;">	
			<!-- ============================ Title part ============================ -->
			<div>		
		        <table width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0"> 
		            <tr class="form_caption">
		            	<td colspan="<? echo $colspan;?>" align="center"><strong>Color Wise RMG Production Status V2</strong></td> 
		            </tr>
		            <tr class="form_caption">
		            	<td colspan="<? echo $colspan;?>" align="center"><strong><? echo $company_arr[$company_id]; ?></strong></td> 
		            </tr>
		            <tr class="form_caption">
		            	<td colspan="<? echo $colspan;?>" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_production_date)) ); ?></strong></td> 
		            </tr>
		        </table>
		    </div>
		    <!-- =========================== header part =============================== -->
		    <div>
		    	<table cellspacing="0" width="<? echo $tbl_width;?>" cellpadding="0" border="1" class="rpt_table" rules="all" align="left">
		    		<thead>
		    			<tr>
		    				<th width="100" rowspan="2">Buyer</th>
		    				<th width="100" rowspan="2">Job No</th>
		    				<th width="100" rowspan="2">Style</th>
		    				<th width="100" rowspan="2">Order No.</th>
		    				<th width="100" rowspan="2">Garments Item</th>
		    				<th width="60" rowspan="2">PO Ship Date</th>
		    				<th width="100" rowspan="2">Color</th>
		    				<th width="100" rowspan="2">Order Qty</th>
		    				<? foreach ($new_process_arr as $key => $val) 
		    				{
		    					if($val==1)
		    					{
			    					?>		    					
				    				<th width="320" colspan="4">Cutting</th>
				    				<?
			    				}
			    				if($val==4)
				    			{
				    				?>
				    				<th width="400" colspan="5">Sewing Input</th>
				    				<?
				    			}
			    				if($val==5)
				    			{
				    				?>
				    				<th width="400" colspan="5">Sewing Output</th>
				    				<?
				    			}
			    				if($val==2)
				    			{
				    				?>
				    				<th width="240" colspan="3">Wash Send</th>
				    				<th width="240" colspan="3">Wash Receive</th>
				    				<?
				    			}
			    				if($val==80)
				    			{
				    				?>
				    				<th width="240" colspan="3">Finishing</th>
				    				<?
				    			}
			    				if($val==8)
				    			{
				    				?>
				    				<th width="240" colspan="3">Pack & Fin</th>
				    				<?
				    			}
			    				if($val==9)
				    			{
				    				?>
				    				<th width="240" colspan="3">Shipment</th>
				    				<?
				    			}
		    				} 
		    				?>
		    			</tr>
		    			<tr>
		    				<? foreach ($new_process_arr as $key => $val) 
		    				{
		    					if($val==1)
		    					{
			    					?>		    					
				    				<th width="80">Start Date</th>
				    				<th width="80">Today</th>
				    				<th width="80">Total</th>
				    				<th width="80">End Date</th>
				    				<?
			    				}
			    				if($val==4)
				    			{
				    				?>
				    				<th width="80">Line No.</th>
				    				<th width="80">1st Input Date</th>
				    				<th width="80">Today</th>
				    				<th width="80">Total</th>
				    				<th width="80">Balance</th>
				    				<?
				    			}
			    				if($val==5)
				    			{
				    				?>
				    				<th width="80">1st Output Date</th>
				    				<th width="80">Today</th>
				    				<th width="80">Total</th>
				    				<th width="80">Last Output Date</th>
				    				<th width="80">WIP</th>
				    				<?
				    			}
			    				if($val==2)
				    			{
				    				?>
				    				<th width="80">Today</th>
				    				<th width="80">Total</th>
				    				<th width="80">Balance</th>
				    				
				    				<th width="80">Today</th>
				    				<th width="80">Total</th>
				    				<th width="80">Balance</th>
				    				<?
				    			}
			    				if($val==80)
				    			{
				    				?>
				    				<th width="80">Today</th>
				    				<th width="80">Total</th>
				    				<th width="80">Balance</th>
				    				<?
				    			}
			    				if($val==8)
				    			{
				    				?>
				    				<th width="80">Today</th>
				    				<th width="80">Total</th>
				    				<th width="80">Balance</th>
				    				<?
				    			}
			    				if($val==9)
				    			{
				    				?>
				    				<th width="80">Today</th>
				    				<th width="80">Total</th>
				    				<th width="80">Balance</th>
				    				<?
				    			}
		    				} 
		    				?>		    				
		    			</tr>
		    		</thead>
		    	</table>
		    </div>
		    <!-- ============================== body part ========================= -->
			<div style="max-height:400px; overflow-y:scroll; width:<? echo $tbl_width+20;?>px;float: left;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="<? echo $tbl_width;?>" rules="all" id="table_body" align="left">
					<tbody>
					<?
					$i=1;
					$gt_order_qty = 0;
					$gt_today_cut_qty = 0;
					$gt_total_cut_qty = 0;
					$gt_today_in_qty = 0;
					$gt_total_in_qty = 0;
					$gt_in_bal_qty = 0;
					$gt_today_out_qty = 0;
					$gt_total_out_qty = 0;
					$gt_out_wip_qty = 0;
					$gt_today_wash_iss_qty = 0;
					$gt_total_wash_iss_qty = 0;
					$gt_wash_iss_bal_qty = 0;
					$gt_today_wash_rcv_qty = 0;
					$gt_total_wash_rcv_qty = 0;
					$gt_wash_rcv_bal_qty = 0;
					$gt_today_wvn_fin_qty = 0;
					$gt_total_wvn_fin_qty = 0;
					$gt_wvn_fin_bal_qty = 0;
					$gt_today_fin_qty = 0;
					$gt_total_fin_qty = 0;
					$gt_fin_bal_qty = 0;
					$gt_today_ship_qty = 0;
					$gt_total_ship_qty = 0;
					$gt_ship_bal_qty = 0;

					foreach($data_array as $style=>$style_data)
					{
						$style_order_qty = 0;
						$style_today_cut_qty = 0;
						$style_total_cut_qty = 0;
						$style_today_in_qty = 0;
						$style_total_in_qty = 0;
						$style_in_bal_qty = 0;
						$style_today_out_qty = 0;
						$style_total_out_qty = 0;
						$style_out_wip_qty = 0;
						$style_today_wash_iss_qty = 0;
						$style_total_wash_iss_qty = 0;
						$style_wash_iss_bal_qty = 0;
						$style_today_wash_rcv_qty = 0;
						$style_total_wash_rcv_qty = 0;
						$style_wash_rcv_bal_qty = 0;
						$style_today_wvn_fin_qty = 0;
						$style_total_wvn_fin_qty = 0;
						$style_wvn_fin_bal_qty = 0;
						$style_today_fin_qty = 0;
						$style_total_fin_qty = 0;
						$style_fin_bal_qty = 0;
						$style_today_ship_qty = 0;
						$style_total_ship_qty = 0;
						$style_ship_bal_qty = 0;
						foreach ($style_data as $job => $job_data) 
						{
							foreach($job_data as $po_id=>$po_data)
							{
								foreach ($po_data as $item_id => $item_data) 
								{
									$itm = 0;
									foreach ($item_data as $color_id => $color_data) 
									{
										$col = 0;
										foreach ($color_data as $line_name => $row) 
										{
											 //$row['today_sewing_output']=1;
											 
											 if($others_data_array[$style][$job][$po_id][$item_id][$color_id]['total_cutting'] >0 || $others_data_array[$style][$job][$po_id][$item_id][$color_id]['today_cutting']>0 || $row['today_sewing_input'] >0 || $row['today_sewing_output'] >0 || $row['today_issue_to_wash'] >0 || $row['today_rcv_frm_wash'] >0)
											 {
												$order_quantity = $order_qty_array[$po_id][$item_id][$color_id];
												$today_ex_fact = $ex_factory_arr[$po_id][$item_id][$color_id]['today_ex_fac'];
												$total_ex_fact = $ex_factory_arr[$po_id][$item_id][$color_id]['total_ex_fac'];

												$today_cutting = $others_data_array[$style][$job][$po_id][$item_id][$color_id]['today_cutting'];
												$total_cutting = $others_data_array[$style][$job][$po_id][$item_id][$color_id]['total_cutting'];

												$today_issue_to_wash = $others_data_array[$style][$job][$po_id][$item_id][$color_id]['today_issue_to_wash'];
												$total_issue_to_wash = $others_data_array[$style][$job][$po_id][$item_id][$color_id]['total_issue_to_wash'];

												$today_rcv_frm_wash = $others_data_array[$style][$job][$po_id][$item_id][$color_id]['today_rcv_frm_wash'];
												$total_rcv_frm_wash = $others_data_array[$style][$job][$po_id][$item_id][$color_id]['total_rcv_frm_wash'];

												$today_finishing = $others_data_array[$style][$job][$po_id][$item_id][$color_id]['today_finishing'];
												$total_finishing = $others_data_array[$style][$job][$po_id][$item_id][$color_id]['total_finishing'];

												$today_wvn_finishing = $others_data_array[$style][$job][$po_id][$item_id][$color_id]['today_wvn_finishing'];
												$total_wvn_finishing = $others_data_array[$style][$job][$po_id][$item_id][$color_id]['total_wvn_finishing'];


												$ship_bal = $total_ex_fact - $total_finishing;
												$fin_bal = $total_finishing - $total_rcv_frm_wash;
												$wash_rcv_bal = $total_rcv_frm_wash - $total_issue_to_wash;
												//$wash_iss_bal = $total_issue_to_wash - $row['total_sewing_output'];
												$wash_iss_bal = $total_issue_to_wash - $others_data_array[$style][$job][$po_id][$item_id][$color_id]["total_sewing_output"];
												
												
												
												$out_wip = $row['total_sewing_output'] - $row['total_sewing_input'];
												$input_bal = $row['total_sewing_input'] - $total_cutting;

												$cut_start_date = $prod_date_array2[$style][$job][$po_id][$item_id][$color_id][1]['start_date'];
												$cut_end_date = $prod_date_array2[$style][$job][$po_id][$item_id][$color_id][1]['end_date'];

												$input_start_date = $prod_date_array[$style][$job][$po_id][$item_id][$color_id][$line_name][4]['start_date'];
												$input_end_date = $prod_date_array[$style][$job][$po_id][$item_id][$color_id][$line_name][4]['end_date'];

												$output_start_date = $prod_date_array[$style][$job][$po_id][$item_id][$color_id][$line_name][5]['start_date'];
												$output_end_date = $prod_date_array[$style][$job][$po_id][$item_id][$color_id][$line_name][5]['end_date'];

												$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
												?>
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
													<? if($itm==0){?>
													<td rowspan="<? echo $rowspan[$po_id][$item_id];?>" width="100"><? echo $buyer_arr[$row['buyer_name']];?></td>
													<td rowspan="<? echo $rowspan[$po_id][$item_id];?>" width="100"><? echo $row['job_no'];?></td>
													<td rowspan="<? echo $rowspan[$po_id][$item_id];?>" width="100"><p><? echo $row['style_ref_no'];?></p></td>
													<td rowspan="<? echo $rowspan[$po_id][$item_id];?>" width="100"><p><? echo $row['po_number'];?></p></td>
													<td rowspan="<? echo $rowspan[$po_id][$item_id];?>" width="100"><p><? echo $garments_item[$item_id];?></p></td>
													<td rowspan="<? echo $rowspan[$po_id][$item_id];?>" width="60" align="center"><? echo change_date_format($row['ship_date']);?></td>
													<? $itm++;} if($col==0){?>
													<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" width="100"><p><? echo $color_arr[$color_id];?></p></td>
													<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" width="100" align="right"><? echo number_format($order_quantity,0);?></td>
													<?
													$style_order_qty += $order_quantity;
													$gt_order_qty += $order_quantity;
													} 
													foreach ($new_process_arr as $key => $val) 
								    				{
								    					if($val==1)
								    					{
								    						if($col==0)
								    						{
										    					?>		    					
											    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="center" width="80"><? echo change_date_format($cut_start_date);?></td>
											    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="right" width="80"><? echo $today_cutting;?></td>
											    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="right" width="80"><? echo $total_cutting;?></td>
											    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="center" width="80"><? echo change_date_format($cut_end_date);?></td>
											    				<?
											    				$style_today_cut_qty += $today_cutting;
																$style_total_cut_qty += $total_cutting;
																$gt_today_cut_qty += $today_cutting;
																$gt_total_cut_qty += $total_cutting;
											    			}
									    				}
									    				if($val==4)
										    			{
										    				?>
										    				<td align="left" width="80" align="center" title="<?= $line_name;?>"><p><? echo $line_name; ?></p></td>
										    				<td align="center" width="80"><? echo change_date_format($input_start_date);?></td>
										    				<td align="right" width="80"><? echo $row['today_sewing_input'];?></td>
										    				<td align="right" width="80"><? echo $row['total_sewing_input'];?></td>
										    				<td align="right" width="80"><? echo $input_bal;?></td>
										    				<?
										    			}
									    				if($val==5)
										    			{
										    				?>
										    				<td align="center" width="80"><? echo change_date_format($output_start_date);?></td>
										    				<td align="right" width="80">
										    					<a href="##" onClick="open_report_popup('<? echo $po_id;?>_<? echo $item_id;?>_<? echo $color_id; ?>_<? echo str_replace("'", "", $txt_production_date); ?>','1','open_gmts_popup');">
											    					<? echo $row['today_sewing_output'];?>
											    				</a>						    						
										    				</td>
										    				<td align="right" width="80">
										    					<a href="##" onClick="open_report_popup('<? echo $po_id;?>_<? echo $item_id;?>_<? echo $color_id; ?>_<? echo str_replace("'", "", $txt_production_date); ?>','1_','open_gmts_popup');">
											    					<? echo $row['total_sewing_output'];?>
											    				</a>					    						
										    				</td>
										    				<td align="center" width="80"><? echo change_date_format($output_end_date);?></td>
										    				<td align="right" width="80"><? echo $out_wip;?></td>
										    				<?
										    			}
									    				if($val==2)
										    			{
										    				if($col==0)
								    						{
											    				?>
											    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="right" width="80"><? echo $today_issue_to_wash;?></td>
											    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="right" width="80">
											    					<a href="##" onClick="open_report_popup('<? echo $po_id;?>_<? echo $item_id;?>_<? echo $color_id; ?>_<? echo str_replace("'", "", $txt_production_date); ?>','2','open_wash_popup');">
												    					<? echo $total_issue_to_wash;?>
												    				</a>						    						
											    				</td>
											    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="right" width="80"><? echo $wash_iss_bal;?></td>

											    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="right" width="80"><? echo $today_rcv_frm_wash;?></td>
											    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="right" width="80">
											    					<a href="##" onClick="open_report_popup('<? echo $po_id;?>_<? echo $item_id;?>_<? echo $color_id; ?>_<? echo str_replace("'", "", $txt_production_date); ?>','3','open_wash_popup');">
												    					<? echo $total_rcv_frm_wash;?>
												    				</a>							    						
											    				</td>
											    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="right" width="80"><? echo $wash_rcv_bal;?></td>
											    				<?
																$style_today_wash_iss_qty += $today_issue_to_wash;
																$style_total_wash_iss_qty += $total_issue_to_wash;
																$style_wash_iss_bal_qty += $wash_iss_bal;	
																$style_today_wash_rcv_qty += $today_rcv_frm_wash;
																$style_total_wash_rcv_qty += $total_rcv_frm_wash;
																$style_wash_rcv_bal_qty += $wash_rcv_bal;

																$gt_today_wash_iss_qty += $today_issue_to_wash;
																$gt_total_wash_iss_qty += $total_issue_to_wash;
																$gt_wash_iss_bal_qty += $wash_iss_bal;
																$gt_today_wash_rcv_qty += $today_rcv_frm_wash;
																$gt_total_wash_rcv_qty += $total_rcv_frm_wash;
																$gt_wash_rcv_bal_qty += $wash_rcv_bal;
											    			}
										    			}
									    				if($val==80)
										    			{
										    				if($col==0)
								    						{
											    				?>
											    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="right" width="80"><? echo $today_wvn_finishing;?></td>
											    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="right" width="80"><? echo $total_wvn_finishing;?></td>
											    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="right" width="80"><? echo $fin_wvn_bal;?></td>
											    				<?

																$style_today_wvn_fin_qty += $today_wvn_finishing;
																$style_total_wvn_fin_qty += $total_wvn_finishing;
																$style_wvn_fin_bal_qty += $fin_wvn_bal;

																$gt_today_wvn_fin_qty += $today_wvn_finishing;
																$gt_total_wvn_fin_qty += $total_wvn_finishing;
																$gt_wvn_fin_bal_qty += $fin_wvn_bal;
											    			}
										    			}
									    				if($val==8)
										    			{
										    				if($col==0)
								    						{
											    				?>
											    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="right" width="80"><? echo $today_finishing;?></td>
											    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="right" width="80"><? echo $total_finishing;?></td>
											    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="right" width="80"><? echo $fin_bal;?></td>
											    				<?

																$style_today_fin_qty += $today_finishing;
																$style_total_fin_qty += $total_finishing;
																$style_fin_bal_qty += $fin_bal;

																$gt_today_fin_qty += $today_finishing;
																$gt_total_fin_qty += $total_finishing;
																$gt_fin_bal_qty += $fin_bal;
											    			}
										    			}
									    				if($val==9)
										    			{
										    				if($col==0)
								    						{
											    				?>
											    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="right" width="80"><? echo $today_ex_fact;?></td>
											    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="right" width="80"><? echo $total_ex_fact;?></td>
											    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="right" width="80"><? echo $ship_bal;?></td>
											    				<?

																$style_today_ship_qty += $today_ex_fact;
																$style_total_ship_qty += $total_ex_fact;
																$style_ship_bal_qty += $ship_bal;

																$gt_today_ship_qty += $today_ex_fact;
																$gt_total_ship_qty += $total_ex_fact;
																$gt_ship_bal_qty += $ship_bal;
											    				
											    			}
										    			}

								    				} 
								    				if($col==0){$col++;}
								    				?>
												</tr>
												<?
												$i++;	
												
												
												$style_today_in_qty += $row['today_sewing_input'];
												$style_total_in_qty += $row['total_sewing_input'];
												$style_in_bal_qty += $input_bal;
												$style_today_out_qty += $row['today_sewing_output'];
												$style_total_out_qty += $row['total_sewing_output'];
												$style_out_wip_qty += $out_wip;

												
												
												$gt_today_in_qty += $row['today_sewing_input'];
												$gt_total_in_qty += $row['total_sewing_input'];
												$gt_in_bal_qty += $input_bal;
												$gt_today_out_qty += $row['today_sewing_output'];
												$gt_total_out_qty += $row['total_sewing_output'];
												$gt_out_wip_qty += $out_wip;
											 }
										}
									}
								}
							}
						}
						if($style_total_cut_qty>0 || $style_today_in_qty>0 || $style_today_out_qty>0 || $style_today_wash_iss_qty>0 || $style_today_wash_rcv_qty>0)
						{
						?>
						<!-- ======================== style wise subtotal =========================== -->
						<tr bgcolor="#dccdcd" style="font-weight: bold;text-align: right;">
							<td width="100"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="60"></td>
							<td width="100" align="right">Style Total</td>
							<td width="100"><? echo number_format($style_order_qty,0);?></td>
							<? foreach ($new_process_arr as $key => $val) 
		    				{
		    					if($val==1)
		    					{
			    					?>		    					
				    				<td width="80"></td>
				    				<td width="80"><? echo number_format($style_today_cut_qty,0);?></td>
				    				<td width="80"><? echo number_format($style_total_cut_qty,0);?></td>
				    				<td width="80"></td>
				    				<?
			    				}
			    				if($val==4)
				    			{
				    				?>
				    				<td width="80"></td>
				    				<td width="80"></td>
				    				<td width="80"><? echo number_format($style_today_in_qty,0);?></td>
				    				<td width="80"><? echo number_format($style_total_in_qty,0);?></td>
				    				<td width="80"></td>
				    				<?
				    			}
			    				if($val==5)
				    			{
				    				?>
				    				<td width="80"></td>
				    				<td width="80"><? echo number_format($style_today_out_qty,0);?></td>
				    				<td width="80"><? echo number_format($style_total_out_qty,0);?></td>
				    				<td width="80"></td>
				    				<td width="80"><? echo number_format($style_out_wip_qty,0);?></td>
				    				<?
				    			}
			    				if($val==2)
				    			{
				    				?>
				    				<td width="80"><? echo number_format($style_today_wash_iss_qty,0);?></td>
				    				<td width="80"><? echo number_format($style_total_wash_iss_qty,0);?></td>
				    				<td width="80"><? echo number_format($style_wash_iss_bal_qty,0);?></td>

				    				<td width="80"><? echo number_format($style_today_wash_rcv_qty,0);?></td>
				    				<td width="80"><? echo number_format($style_total_wash_rcv_qty,0);?></td>
				    				<td width="80"><? echo number_format($style_wash_rcv_bal_qty,0);?></td>
				    				<?
				    			}
			    				if($val==80)
				    			{
				    				?>
				    				<td width="80"><? echo number_format($style_today_wvn_fin_qty,0);?></td>
				    				<td width="80"><? echo number_format($style_total_wvn_fin_qty,0);?></td>
				    				<td width="80"><? echo number_format($style_wvn_fin_bal_qty,0);?></td>
				    				<?
				    			}
			    				if($val==8)
				    			{
				    				?>
				    				<td width="80"><? echo number_format($style_today_fin_qty,0);?></td>
				    				<td width="80"><? echo number_format($style_total_fin_qty,0);?></td>
				    				<td width="80"><? echo number_format($style_fin_bal_qty,0);?></td>
				    				<?
				    			}
			    				if($val==9)
				    			{
				    				?>
				    				<td width="80"><? echo number_format($style_today_ship_qty,0);?></td>
				    				<td width="80"><? echo number_format($style_total_ship_qty,0);?></td>
				    				<td width="80"><? echo number_format($style_ship_bal_qty,0);?></td>
				    				<?
				    			}
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
			<!-- ============================== footer part =============================== -->
			<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="<? echo $tbl_width;?>" rules="all" align="left">
				<tfoot>
					<tr>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="60"></th>
						<th width="100">Grand Total</th>
						<th width="100"><? echo number_format($gt_order_qty,0);?></th>
						<? foreach ($new_process_arr as $key => $val) 
	    				{
	    					if($val==1)
	    					{
		    					?>		    					
			    				<th width="80"></th>
			    				<th width="80"><? echo number_format($gt_today_cut_qty,0);?></th>
			    				<th width="80"><? echo number_format($gt_total_cut_qty,0);?></th>
			    				<th width="80"></th>
			    				<?
		    				}
		    				if($val==4)
			    			{
			    				?>
			    				<th width="80"></th>
			    				<th width="80"></th>
			    				<th width="80"><? echo number_format($gt_today_in_qty,0);?></th>
			    				<th width="80"><? echo number_format($gt_total_in_qty,0);?></th>
			    				<th width="80"></th>
			    				<?
			    			}
		    				if($val==5)
			    			{
			    				?>
			    				<th width="80"></th>
			    				<th width="80"><? echo number_format($gt_today_out_qty,0);?></th>
			    				<th width="80"><? echo number_format($gt_total_out_qty,0);?></th>
			    				<th width="80"></th>
			    				<th width="80"><? echo number_format($gt_out_wip_qty,0);?></th>
			    				<?
			    			}
		    				if($val==2)
			    			{
			    				?>
			    				<th width="80"><? echo number_format($gt_today_wash_iss_qty,0);?></th>
			    				<th width="80"><? echo number_format($gt_total_wash_iss_qty,0);?></th>
			    				<th width="80"><? echo number_format($gt_wash_iss_bal_qty,0);?></th>

			    				<th width="80"><? echo number_format($gt_today_wash_rcv_qty,0);?></th>
			    				<th width="80"><? echo number_format($gt_total_wash_rcv_qty,0);?></th>
			    				<th width="80"><? echo number_format($gt_wash_rcv_bal_qtya,0);?></th>
			    				<?
			    			}
		    				if($val==80)
			    			{
			    				?>
			    				<th width="80"><? echo number_format($gt_today_wvn_fin_qty,0);?></th>
			    				<th width="80"><? echo number_format($gt_total_wvn_fin_qty,0);?></th>
			    				<th width="80"><? echo number_format($gt_wvn_fin_bal_qty,0);?></th>
			    				<?
			    			}
		    				if($val==8)
			    			{
			    				?>
			    				<th width="80"><? echo number_format($gt_today_fin_qty,0);?></th>
			    				<th width="80"><? echo number_format($gt_total_fin_qty,0);?></th>
			    				<th width="80"><? echo number_format($gt_fin_bal_qty,0);?></th>
			    				<?
			    			}
		    				if($val==9)
			    			{
			    				?>
			    				<th width="80"><? echo number_format($gt_today_ship_qty,0);?></th>
			    				<th width="80"><? echo number_format($gt_total_ship_qty,0);?></th>
			    				<th width="80"><? echo number_format($gt_ship_bal_qty,0);?></th>
			    				<?
			    			}
	    				} 
	    				?>
					</tr>
				</tfoot>										
			</table>
		</fieldset>
    	<?
	}
	elseif ($type==3) // short button
	{
		$sql_cond 		= "";
		$sql_cond .= ($company_id !="") ? " and a.company_name in($company_id)" : "";
		$sql_cond .= ($working_company_id !="") ? " and d.serving_company in($working_company_id)" : "";
		// $sql_cond .= ($location_id !=0) ? " and d.location=$location_id" : "";
		$sql_cond .= ($buyer_id !=0) ? " and a.buyer_name=$buyer_id" : "";
		$sql_cond .= ($style_no !="") ? " and a.style_ref_no='$style_no'" : "";
		$sql_cond .= ($hidden_job_id !="") ? " and a.id=$hidden_job_id" : "";
		$sql_cond .= ($order_no !="") ? " and b.po_number='$order_no'" : "";
		$sql_cond .= ($hidden_order_id !="") ? " and b.id=$hidden_order_id" : "";
		$sql_cond .= ($company_id !=0) ? " and a.company_name=$company_id" : "";
		//$sql_cond .= ($shipment_status !=0) ? " and b.shiping_status=$shipment_status" : "";
		$sql_cond .= ($shipment_status ==3) ? " and b.shiping_status=$shipment_status" : "";
		$sql_cond .= ($shipment_status ==2) ? " and b.shiping_status<3" : "";

		// ============================== get today prod po ==========================
		$prod_po_arr=return_library_array( "SELECT po_break_down_id,po_break_down_id as po_id from  pro_garments_production_mst where status_active=1 and is_deleted=0 and production_date=$txt_production_date and production_type in(1,2,3,4,5,80)", "po_break_down_id", "po_id"  );
		// print_r($prod_po_arr);die;	
		$po_id_cond = where_con_using_array($prod_po_arr,0,"b.id");
		
		// ============================================ FOR PRODUCTION ================================================
		
		$sql="SELECT a.JOB_NO, a.COMPANY_NAME, a.BUYER_NAME, a.STYLE_REF_NO, b.id as PO_ID, b.PO_NUMBER,  c.item_number_id as ITEM_ID,c.color_number_id as COLOR_ID,d.serving_company,d.location,d.sewing_line,d.prod_reso_allo,max(b.pub_Shipment_date) as SHIP_DATE,
		sum(c.order_quantity) as ORDER_QUANTITY, 
		sum(case when d.production_type=1 and e.production_type=1 and d.production_date<=$txt_production_date then e.production_qnty else 0 end ) as TOTAL_CUTTING ,
		sum(case when d.production_type=2 and e.production_type=2 and d.embel_name=3 and d.production_date<=$txt_production_date then e.production_qnty else 0 end ) as TOTAL_ISSUE_TO_WASH ,
		sum(case when d.production_type=3 and e.production_type=3 and d.embel_name=3 and d.production_date<=$txt_production_date then e.production_qnty else 0 end ) as TOTAL_RCV_FRM_WASH ,

		sum(case when d.production_type=4 and e.production_type=4  and d.production_date<=$txt_production_date then e.production_qnty else 0 end ) as TOTAL_SEWING_INPUT ,
		sum(case when d.production_type=5 and e.production_type=5  and d.production_date<=$txt_production_date then e.production_qnty else 0 end ) as TOTAL_SEWING_OUTPUT ,
		sum(case when d.production_type=80 and e.production_type=80  and d.production_date<=$txt_production_date then e.production_qnty else 0 end ) as TOTAL_WVN_FINISHING,
		sum(case when d.production_type=8 and e.production_type=8  and d.production_date<=$txt_production_date then e.production_qnty else 0 end ) as TOTAL_FINISHING

		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
		where a.id=b.job_id and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.id=c.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0  $sql_cond $po_id_cond and d.production_type in(1,2,3,4,5,8,80)
		group by a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id, b.po_number,  c.item_number_id,c.color_number_id,d.serving_company,d.location,d.sewing_line,d.prod_reso_allo order by b.id desc";

		// echo $sql;die;
		$sql_res = sql_select($sql);
		if(count($sql_res)==0)
		{
			?>
			<div style="margin:20px auto; width: 90%">
				<div class="alert alert-error">
				  <strong>Data not found!</strong> Change a few things then try submitting again.
				</div>
			</div>
			<?
			disconnect($con);
			die();
		} 

		$data_array = array();
		$po_id_array = array();
		$others_data_array = array();
		foreach($sql_res as $vals)
		{
			$sewing_line='';
			if($vals['PROD_RESO_ALLO']==1)
			{
				$line_number=explode(",",$prod_reso_arr[$vals['SEWING_LINE']]);
				foreach($line_number as $value)
				{
					if($sewing_line=='') $sewing_line=$lineArr[$value]; else $sewing_line.=",".$lineArr[$value];
				}
			}
			else
			{ 
				$sewing_line=$lineArr[$vals['SEWING_LINE']];
			}

			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["job_no"] = $vals["JOB_NO"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["buyer_name"] = $vals["BUYER_NAME"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["style_ref_no"] = $vals["STYLE_REF_NO"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["style_ref_no"] = $vals["STYLE_REF_NO"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["ship_date"] = $vals["SHIP_DATE"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["job_no"] = $vals["JOB_NO"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["po_number"] = $vals["PO_NUMBER"];

			if($sewing_line !='')
			{
				$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["total_sewing_input"]+=$vals["TOTAL_SEWING_INPUT"];

				$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["total_sewing_output"]+=$vals["TOTAL_SEWING_OUTPUT"];

			}


			$others_data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["total_cutting"]+=$vals["TOTAL_CUTTING"];
			$others_data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["total_issue_to_wash"]+=$vals["TOTAL_ISSUE_TO_WASH"];
			$others_data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["total_rcv_frm_wash"]+=$vals["TOTAL_RCV_FRM_WASH"];
			$others_data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["total_wvn_finishing"]+=$vals["TOTAL_WVN_FINISHING"];
			$others_data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["total_finishing"]+=$vals["TOTAL_FINISHING"];

			$po_id_array[$vals["PO_ID"]] = $vals["PO_ID"];
		}
		// echo "<pre>";print_r($prod_date_array);die;
		$poIds = implode(",", $po_id_array);

		if(count($po_id_array)>999 && $db_type==2)
	    {
	     	$po_chunk=array_chunk($po_id_array, 999);
	     	$po_ids_cond= "";
	     	foreach($po_chunk as $vals)
	     	{
	     		$imp_ids=implode(",", $vals);
	     		if($po_ids_cond=="") 
	     		{
	     			$po_ids_cond.=" and ( a.po_break_down_id in ($imp_ids) ";
	     		}
	     		else
	     		{
	     			$po_ids_cond.=" or a.po_break_down_id in ($imp_ids) ";
	     		}
	     	}
	     	 $po_ids_cond.=" )";
	    }
	    else
	    {
	     	$po_ids_cond= " and a.po_break_down_id in($poIds) ";
	    }
	    // ======================================== order qty =================================================
	    $po_ids_conds = str_replace("a.po_break_down_id", "po_break_down_id", $po_ids_cond);
	    $sql = "SELECT item_number_id as item_id,color_number_id as color_id ,po_break_down_id as po_id, order_quantity from wo_po_color_size_breakdown where status_active in(1,2,3) and is_deleted=0 $po_ids_conds";

	    $sql_res = sql_select($sql);
	    $order_qty_array = array();
	    foreach ($sql_res as $val) 
	    {
	    	$order_qty_array[$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']] += $val['ORDER_QUANTITY'];
	    }
	    // print_r($order_qty_array);

		// ========================================= FOR EX-FACTORY QTY ==========================================
		$ex_factory_arr=array();
		$ex_factory_data="SELECT a.po_break_down_id, a.item_number_id,c.color_number_id, 
		sum(CASE WHEN a.entry_form!=85 and ex_factory_date=$txt_production_date THEN b.production_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN production_qnty ELSE 0 END) AS today_ex_fac , 
		sum(CASE WHEN a.entry_form!=85 THEN b.production_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN production_qnty ELSE 0 END) AS total_ex_fac 
		from pro_ex_factory_delivery_mst d, pro_ex_factory_mst a,pro_ex_factory_dtls b,wo_po_color_size_breakdown c where d.id=a.delivery_mst_id  and  a.id=b.mst_id and b.color_size_break_down_id=c.id   and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_ids_cond group by a.po_break_down_id, a.item_number_id,c.color_number_id";
		// echo $ex_factory_data;die();
		$ex_factory_data_res = sql_select($ex_factory_data);
		foreach($ex_factory_data_res as $exRow)
		{
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('color_number_id')]]['today_ex_fac']+=$exRow[csf('today_ex_fac')];
			$ex_factory_arr[$exRow[csf('po_break_down_id')]][$exRow[csf('item_number_id')]][$exRow[csf('color_number_id')]]['total_ex_fac']=+$exRow[csf('total_ex_fac')];
		}
		// echo "<pre>";
		// print_r($ex_factory_arr);
		// echo "</pre>";
		// die();
		$tbl_width = 1620;
		$colspan = 17;
		// echo $tbl_width;die();
		// =======================================
		$rowspan = array();
		$rowspan_color = array();
		foreach ($data_array as $style => $style_data) 
		{
			foreach ($style_data as $job => $job_data) 
			{				
				foreach($job_data as $po_id=>$po_data)
				{
					foreach ($po_data as $item_id => $item_data) 
					{
						foreach ($item_data as $color_id => $color_data) 
						{
							foreach ($color_data as $line_id => $row) 
							{
								if($others_data_array[$style][$job][$po_id][$item_id][$color_id]['total_cutting']>0)
								{
									$rowspan[$po_id][$item_id]++;
									$rowspan_color[$po_id][$item_id][$color_id]++;
								}
							}
						}
					}
				}
			}
		}
		
		ob_start();	
		?>
		<fieldset style="width:<? echo $tbl_width+20;?>px;">	
			<!-- ============================ Title part ============================ -->
			<div>		
		        <table width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0"> 
		            <tr class="form_caption">
		            	<td colspan="<? echo $colspan;?>" align="center"><strong>Color Wise RMG Production Status V2</strong></td> 
		            </tr>
		            <tr class="form_caption">
		            	<td colspan="<? echo $colspan;?>" align="center"><strong><? echo $company_arr[$company_id]; ?></strong></td> 
		            </tr>
		            <tr class="form_caption">
		            	<td colspan="<? echo $colspan;?>" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_production_date)) ); ?></strong></td> 
		            </tr>
		        </table>
		    </div>
		    <!-- =========================== header part =============================== -->
		    <div>
		    	<table cellspacing="0" width="<? echo $tbl_width;?>" cellpadding="0" border="1" class="rpt_table" rules="all" align="left">
		    		<thead>
		    			<tr>
		    				<th width="100">Buyer</th>
		    				<th width="100">Job No</th>
		    				<th width="100">Style</th>
		    				<th width="100">Order No.</th>
		    				<th width="100">Garments Item</th>
		    				<th width="60">PO Ship Date</th>
		    				<th width="100">Color</th>
		    				<th width="60">Order Qty</th>
		    				<th width="100">Cutting</th>		    				
		    				<th width="100">Line Name</th>		    				
		    				<th width="100">Sewing Input</th>		    				
		    				<th width="100">Sewing Output</th>		    				
		    				<th width="100">Wash Send</th>
		    				<th width="100">Wash Receive</th>		    				
		    				<th width="100">Finishing</th>		    				
		    				<th width="100">Pack & Fin</th>		    				
		    				<th width="100">Shipment</th>
		    			</tr>
		    		</thead>
		    	</table>
		    </div>
		    <!-- ============================== body part ========================= -->
			<div style="max-height:400px; overflow-y:scroll; width:<? echo $tbl_width+20;?>px;float: left;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="<? echo $tbl_width;?>" rules="all" id="table_body" align="left">
					<tbody>
					<?
					$i=1;
					$gt_order_qty = 0;					
					$gt_total_cut_qty = 0;					
					$gt_total_in_qty = 0;					
					$gt_total_out_qty = 0;					
					$gt_total_wash_iss_qty = 0;					
					$gt_total_wash_rcv_qty = 0;					
					$gt_total_wvn_fin_qty = 0;					
					$gt_total_fin_qty = 0;					
					$gt_total_ship_qty = 0;
					foreach ($data_array as $style => $style_data) 
					{

						$style_order_qty = 0;					
						$style_total_cut_qty = 0;					
						$style_total_in_qty = 0;					
						$style_total_out_qty = 0;					
						$style_total_wash_iss_qty = 0;					
						$style_total_wash_rcv_qty = 0;					
						$style_total_wvn_fin_qty = 0;					
						$style_total_fin_qty = 0;					
						$style_total_ship_qty = 0;
						foreach ($style_data as $job => $job_data) 
						{
							foreach($job_data as $po_id=>$po_data)
							{
								foreach ($po_data as $item_id => $item_data) 
								{
									$itm = 0;
									foreach ($item_data as $color_id => $color_data) 
									{
										$clr = 0;
										foreach ($color_data as $line_name => $row) 
										{
											if($others_data_array[$style][$job][$po_id][$item_id][$color_id]['total_cutting']>0)
											{
												$total_ex_fact = $ex_factory_arr[$po_id][$item_id][$color_id]['total_ex_fac'];
												$order_quantity = $order_qty_array[$po_id][$item_id][$color_id];

												$total_cutting = $others_data_array[$style][$job][$po_id][$item_id][$color_id]['total_cutting'];
												$total_issue_to_wash = $others_data_array[$style][$job][$po_id][$item_id][$color_id]['total_issue_to_wash'];
												$total_rcv_frm_wash = $others_data_array[$style][$job][$po_id][$item_id][$color_id]['total_rcv_frm_wash'];
												$total_wvn_finishing = $others_data_array[$style][$job][$po_id][$item_id][$color_id]['total_wvn_finishing'];
												$total_finishing = $others_data_array[$style][$job][$po_id][$item_id][$color_id]['total_finishing'];

												$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
												// echo "$i<br>";
												?>
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
													<? if($itm==0){?>
													<td rowspan="<? echo $rowspan[$po_id][$item_id];?>" width="100"><? echo $buyer_arr[$row['buyer_name']];?></td>
													<td rowspan="<? echo $rowspan[$po_id][$item_id];?>" width="100"><? echo $row['job_no'];?></td>
													<td rowspan="<? echo $rowspan[$po_id][$item_id];?>" width="100"><p><? echo $row['style_ref_no'];?></p></td>
													<td rowspan="<? echo $rowspan[$po_id][$item_id];?>" width="100"><p><? echo $row['po_number'];?></p></td>
													<td rowspan="<? echo $rowspan[$po_id][$item_id];?>" width="100"><p><? echo $garments_item[$item_id];?></p></td>
													<td rowspan="<? echo $rowspan[$po_id][$item_id];?>" width="60" align="center"><? echo change_date_format($row['ship_date']);?></td>
													<? $itm++;} if($clr==0){?>

													<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" width="100"><p><? echo $color_arr[$color_id];?></p></td>
													<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" width="60" align="right"><? echo number_format($order_quantity,0);?></td>
													
										    		<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="right" width="100"><? echo $total_cutting;?></td>			
										    		<? }?>		    				
										    		<td width="100" align="center"><? echo $line_name;?></td>					    				
								    				<td align="right" width="100"><? echo $row['total_sewing_input'];?></td>
								    				<td align="right" width="100"><? echo $row['total_sewing_output'];?></td>
								    				<? if($clr==0){?>
								    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="right" width="100"><? echo $total_issue_to_wash;?></td>
								    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="right" width="100"><? echo $total_rcv_frm_wash;?></td>
								    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="right" width="100"><? echo $total_wvn_finishing;?></td>
								    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="right" width="100"><? echo $total_finishing;?></td>
								    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="right" width="100"><? echo number_format($total_ex_fact,0);?></td>
								    				<? 
								    				$clr++;
								    				$style_order_qty += $order_quantity;
													$style_total_cut_qty += $total_cutting;								
													$style_total_wash_iss_qty += $total_issue_to_wash;
													$style_total_wash_rcv_qty += $total_rcv_frm_wash;
													$style_total_wvn_fin_qty += $total_wvn_finishing;		
													$style_total_fin_qty += $total_finishing;							
													$style_total_ship_qty += $total_ex_fact;

													$gt_order_qty += $order_quantity;
													$gt_total_cut_qty += $total_cutting;								
													$gt_total_wash_iss_qty += $total_issue_to_wash;
													$gt_total_wash_rcv_qty += $total_rcv_frm_wash;
													$gt_total_fin_qty += $total_finishing;		
													$gt_total_wvn_fin_qty += $total_wvn_finishing;								
													$gt_total_ship_qty += $total_ex_fact;

								    				}
								    				?>	
										    				
												</tr>
												<?
												$i++;

																			
												$style_total_in_qty += $row['total_sewing_input'];
												$style_total_out_qty += $row['total_sewing_output'];
																				
												$gt_total_in_qty += $row['total_sewing_input'];							
												$gt_total_out_qty += $row['total_sewing_output'];
											}
										}
									}
								}
							}
						}
						?>						
						<tr bgcolor="#dccdcd" style="font-weight: bold;text-align: right;">
							<td width="100"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="60"></td>
							<td width="100">Style Total</td>
							<td width="60"><? echo number_format($style_order_qty,0);?></td>
		    				<td width="100"><? echo number_format($style_total_cut_qty,0);?></td>
		    				<td width="100"></td>
		    				<td width="100"><? echo number_format($style_total_in_qty,0);?></td>
		    				<td width="100"><? echo number_format($style_total_out_qty,0);?></td>
		    				<td width="100"><? echo number_format($style_total_wash_iss_qty,0);?></td>
		    				<td width="100"><? echo number_format($style_total_wash_rcv_qty,0);?></td>
		    				<td width="100"><? echo number_format($style_total_wvn_fin_qty,0);?></td>
		    				<td width="100"><? echo number_format($style_total_fin_qty,0);?></td>
		    				<td width="100"><? echo number_format($style_total_ship_qty,0);?></td>		
						</tr>
						<?
					}

					?>	
					</tbody>										
				</table>										  
			</div>	
			<!-- ============================== footer part =============================== -->
			<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="<? echo $tbl_width;?>" rules="all" align="left">
				<tfoot>
					<tr>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="60"></th>
						<th width="100">Total</th>
						<th width="60"><? echo number_format($gt_order_qty,0);?></th>
	    				<th width="100"><? echo number_format($gt_total_cut_qty,0);?></th>
	    				<th width="100"></th>
	    				<th width="100"><? echo number_format($gt_total_in_qty,0);?></th>
	    				<th width="100"><? echo number_format($gt_total_out_qty,0);?></th>
	    				<th width="100"><? echo number_format($gt_total_wash_iss_qty,0);?></th>
	    				<th width="100"><? echo number_format($gt_total_wash_rcv_qty,0);?></th>
	    				<th width="100"><? echo number_format($gt_total_wvn_fin_qty,0);?></th>
	    				<th width="100"><? echo number_format($gt_total_fin_qty,0);?></th>
	    				<th width="100"><? echo number_format($gt_total_ship_qty,0);?></th>			    				
					</tr>
				</tfoot>										
			</table>
		</fieldset>
    	<?
	}
	elseif ($type==4) // wash status button
	{
		$sql_cond 		= "";
		$sql_cond .= ($company_id !="") ? " and a.company_name in($company_id)" : "";
		$sql_cond .= ($working_company_id !="") ? " and d.serving_company in($working_company_id)" : "";
		// $sql_cond .= ($location_id !=0) ? " and d.location=$location_id" : "";
		$sql_cond .= ($buyer_id !=0) ? " and a.buyer_name=$buyer_id" : "";
		$sql_cond .= ($style_no !="") ? " and a.style_ref_no='$style_no'" : "";
		$sql_cond .= ($hidden_job_id !="") ? " and a.id=$hidden_job_id" : "";
		$sql_cond .= ($order_no !="") ? " and b.po_number='$order_no'" : "";
		$sql_cond .= ($hidden_order_id !="") ? " and b.id=$hidden_order_id" : "";
		$sql_cond .= ($company_id !=0) ? " and a.company_name=$company_id" : "";
		//$sql_cond .= ($shipment_status !=0) ? " and b.shiping_status=$shipment_status" : "";
		$sql_cond .= ($shipment_status ==3) ? " and b.shiping_status=$shipment_status" : "";
		$sql_cond .= ($shipment_status ==2) ? " and b.shiping_status<3" : "";
		
		// =====================================================
		$sql_cond2 		= "";
		$sql_cond2 .= ($company_id !="") ? " and a.company_name in($company_id)" : "";
		$sql_cond2 .= ($working_company_id !="") ? " and d.sending_company in($working_company_id)" : "";
		// $sql_cond2 .= ($location_id !=0) ? " and d.location=$location_id" : "";
		$sql_cond2 .= ($buyer_id !=0) ? " and a.buyer_name=$buyer_id" : "";
		$sql_cond2 .= ($style_no !="") ? " and a.style_ref_no='$style_no'" : "";
		$sql_cond2 .= ($hidden_job_id !="") ? " and a.id=$hidden_job_id" : "";
		$sql_cond2 .= ($order_no !="") ? " and b.po_number='$order_no'" : "";
		$sql_cond2 .= ($hidden_order_id !="") ? " and b.id=$hidden_order_id" : "";
		$sql_cond2 .= ($company_id !=0) ? " and a.company_name=$company_id" : "";
		//$sql_cond2 .= ($shipment_status !=0) ? " and b.shiping_status=$shipment_status" : "";
		$sql_cond2 .= ($shipment_status ==3) ? " and b.shiping_status=$shipment_status" : "";
		$sql_cond2 .= ($shipment_status ==2) ? " and b.shiping_status<3" : "";

		// ============================== get today prod po ==========================
		$prod_po_arr=return_library_array( "SELECT po_break_down_id,po_break_down_id as po_id from  pro_garments_production_mst where status_active=1 and is_deleted=0 and production_date=$txt_production_date and production_type in(1,2,3,5)", "po_break_down_id", "po_id"  );
		// print_r($prod_po_arr);die;	
		$po_id_cond = where_con_using_array($prod_po_arr,0,"b.id");	
		
		// ============================================ FOR PRODUCTION ================================================
		
		$sql="SELECT a.JOB_NO, a.COMPANY_NAME, a.BUYER_NAME, a.STYLE_REF_NO, b.id as PO_ID, b.PO_NUMBER,c.color_number_id as COLOR_ID,d.serving_company,d.location,max(b.pub_Shipment_date) as SHIP_DATE,
		sum(c.order_quantity) as ORDER_QUANTITY, 

		sum(case when d.production_type=1 and e.production_type=1 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as TODAY_CUTTING ,
		sum(case when d.production_type=1 and e.production_type=1 and d.production_date<=$txt_production_date then e.production_qnty else 0 end ) as TOTAL_CUTTING ,

		sum(case when d.production_type=5 and e.production_type=5 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as TODAY_SEWING_OUTPUT ,
		sum(case when d.production_type=5 and e.production_type=5  and d.production_date<=$txt_production_date then e.production_qnty else 0 end ) as TOTAL_SEWING_OUTPUT

		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_pre_cost_embe_cost_dtls f
		where a.id=b.job_id and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.id=c.job_id and a.id=f.job_id and f.emb_name=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0  $sql_cond $po_id_cond and d.production_type in(1,5)
		group by a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id, b.po_number,c.color_number_id,d.serving_company,d.location";

		// echo $sql;die;
		$sql_res = sql_select($sql);
		if(count($sql_res)==0)
		{
			?>
			<div style="margin:20px auto; width: 90%">
				<div class="alert alert-error">
				  <strong>Data not found!</strong> Change a few things then try submitting again.
				</div>
			</div>
			<?
			disconnect($con);
			die();
		} 

		$data_array = array();
		$prod_date_array = array();
		$po_id_array = array();
		$chk_arr = array();
		foreach($sql_res as $vals)
		{
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["COLOR_ID"]]["job_no"] = $vals["JOB_NO"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["COLOR_ID"]]["buyer_name"] = $vals["BUYER_NAME"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["COLOR_ID"]]["style_ref_no"] = $vals["STYLE_REF_NO"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["COLOR_ID"]]["style_ref_no"] = $vals["STYLE_REF_NO"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["COLOR_ID"]]["ship_date"] = $vals["SHIP_DATE"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["COLOR_ID"]]["job_no"] = $vals["JOB_NO"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["COLOR_ID"]]["po_number"] = $vals["PO_NUMBER"];

			if($chk_arr[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["COLOR_ID"]]=="")
			{			 
				$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["COLOR_ID"]]["order_quantity"] += $vals["ORDER_QUANTITY"];			 
				$chk_arr[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["COLOR_ID"]] = "kakku";
			}			 

			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["COLOR_ID"]]["total_cutting"]+=$vals["TOTAL_CUTTING"];

			
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["COLOR_ID"]]["total_sewing_output"]+=$vals["TOTAL_SEWING_OUTPUT"];
			$po_id_array[$vals["PO_ID"]]=$vals["PO_ID"];
		}
		// echo "<pre>";print_r($data_array);die;

		

		// ============================== getting was data as per sending location ============================
		$sql="SELECT a.JOB_NO, a.COMPANY_NAME, a.BUYER_NAME, a.STYLE_REF_NO, b.id as PO_ID, b.PO_NUMBER,c.color_number_id as COLOR_ID,d.serving_company,d.location,max(b.pub_Shipment_date) as SHIP_DATE,
		sum(c.order_quantity) as ORDER_QUANTITY, 

		sum(case when d.production_type=2 and e.production_type=2 and d.embel_name=3 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as TODAY_ISSUE_TO_WASH ,
		sum(case when d.production_type=2 and e.production_type=2 and d.embel_name=3 and d.production_date<=$txt_production_date then e.production_qnty else 0 end ) as TOTAL_ISSUE_TO_WASH ,
		sum(case when d.production_type=3 and e.production_type=3 and d.embel_name=3 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as TODAY_RCV_FRM_WASH ,
		sum(case when d.production_type=3 and e.production_type=3 and d.embel_name=3 and d.production_date<=$txt_production_date then e.production_qnty else 0 end ) as TOTAL_RCV_FRM_WASH

		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_pre_cost_embe_cost_dtls f
		where a.id=b.job_id and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.id=c.job_id and a.id=f.job_id and f.emb_name=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0  $sql_cond2 $po_id_cond and d.production_type in(2,3)
		group by a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id, b.po_number,c.color_number_id,d.serving_company,d.location";

		$sql_res = sql_select($sql);
		$chk_arr = array();
		foreach($sql_res as $vals)
		{
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["COLOR_ID"]]["job_no"] = $vals["JOB_NO"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["COLOR_ID"]]["buyer_name"] = $vals["BUYER_NAME"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["COLOR_ID"]]["style_ref_no"] = $vals["STYLE_REF_NO"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["COLOR_ID"]]["style_ref_no"] = $vals["STYLE_REF_NO"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["COLOR_ID"]]["ship_date"] = $vals["SHIP_DATE"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["COLOR_ID"]]["job_no"] = $vals["JOB_NO"];			 
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["COLOR_ID"]]["po_number"] = $vals["PO_NUMBER"];

			if($chk_arr[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["COLOR_ID"]]=="")
			{			 
				$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["COLOR_ID"]]["order_quantity"] += $vals["ORDER_QUANTITY"];			 
				$chk_arr[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["COLOR_ID"]] = "kakku";
			}	
			

			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["COLOR_ID"]]["today_issue_to_wash"]+=$vals["TODAY_ISSUE_TO_WASH"];
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["COLOR_ID"]]["total_issue_to_wash"]+=$vals["TOTAL_ISSUE_TO_WASH"];
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["COLOR_ID"]]["today_rcv_frm_wash"]+=$vals["TODAY_RCV_FRM_WASH"];
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["COLOR_ID"]]["total_rcv_frm_wash"]+=$vals["TOTAL_RCV_FRM_WASH"];
			$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["COLOR_ID"]]["wash_reject"]+=$vals["WASH_REJECT"];
			$po_id_array[$vals["PO_ID"]]=$vals["PO_ID"];
		}
		
		$poIds = implode(",", $po_id_array);

		if(count($po_id_array)>999 && $db_type==2)
	    {
	     	$po_chunk=array_chunk($po_id_array, 999);
	     	$po_ids_cond= "";
	     	foreach($po_chunk as $vals)
	     	{
	     		$imp_ids=implode(",", $vals);
	     		if($po_ids_cond=="") 
	     		{
	     			$po_ids_cond.=" and ( po_break_down_id in ($imp_ids) ";
	     		}
	     		else
	     		{
	     			$po_ids_cond.=" or po_break_down_id in ($imp_ids) ";
	     		}
	     	}
	     	 $po_ids_cond.=" )";
	    }
	    else
	    {
	     	$po_ids_cond= " and po_break_down_id in($poIds) ";
	    }
		
		$tbl_width = 1460;
		$colspan = 15;
		// =======================================
		$rowspan = array();
		foreach($data_array as $style=>$style_data)
		{
			foreach($style_data as $job=>$job_data)
			{
				foreach($job_data as $po_id=>$po_data)
				{
					foreach ($po_data as $color_id => $row) 
					{
						if($row['total_cutting']>0 || $row['total_issue_to_wash']>0 || $row['total_rcv_frm_wash']>0)
						{
							$rowspan[$po_id]++;
						}
					}
				}
			}
		}
	    // ======================================== order qty =================================================
	    // $po_ids_conds = str_replace("a.po_break_down_id", "po_break_down_id", $po_ids_cond);
	    $sql = "SELECT item_number_id as item_id,color_number_id as color_id ,po_break_down_id as po_id, order_quantity from wo_po_color_size_breakdown where status_active in(1,2,3) and is_deleted=0 $po_ids_cond";
	    // echo $sql;
	    $sql_res = sql_select($sql);
	    $order_qty_array = array();
	    foreach ($sql_res as $val) 
	    {
	    	$order_qty_array[$val['PO_ID']][$val['COLOR_ID']] += $val['ORDER_QUANTITY'];
	    }
	    // print_r($order_qty_array);
		
		ob_start();	
		?>
		<fieldset style="width:<? echo $tbl_width+20;?>px;">	
			<!-- ============================ Title part ============================ -->
			<div>		
		        <table width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0"> 
		            <tr class="form_caption">
		            	<td colspan="<? echo $colspan;?>" align="center"><strong>Color Wise RMG Production Status V2</strong></td> 
		            </tr>
		            <tr class="form_caption">
		            	<td colspan="<? echo $colspan;?>" align="center"><strong><? echo $company_arr[$company_id]; ?></strong></td> 
		            </tr>
		            <tr class="form_caption">
		            	<td colspan="<? echo $colspan;?>" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_production_date)) ); ?></strong></td> 
		            </tr>
		        </table>
		    </div>
		    <!-- =========================== header part =============================== -->
		    <div>
		    	<table cellspacing="0" width="<? echo $tbl_width;?>" cellpadding="0" border="1" class="rpt_table" rules="all" align="left">
		    		<thead>
		    			<tr>
		    				<th width="100">Buyer</th>
		    				<th width="100">Job No</th>
		    				<th width="100">Style</th>
		    				<th width="100">Order No.</th>
		    				<th width="60">PO Ship Date</th>
		    				<th width="100">Color</th>
		    				<th width="100">Order Qty</th>
		    				<th width="100">Cutting</th>
		    				<th width="100">Sewing Output</th>

		    				<th width="100">Today Send</th>
		    				<th width="100">Total Send</th>
		    				<th width="100">Send Balance</th>
		    				
		    				<th width="100">Today Receive</th>
		    				<th width="100">Total Receive</th>
		    				<th width="100">Receive Balance</th>
				    				
		    			</tr>
		    		</thead>
		    	</table>
		    </div>
		    <!-- ============================== body part ========================= -->
			<div style="max-height:400px; overflow-y:scroll; width:<? echo $tbl_width+20;?>px;float: left;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="<? echo $tbl_width;?>" rules="all" id="table_body" align="left">
					<tbody>
					<?
					$i=1;
					$gt_order_qty = 0;
					$gt_total_cut_qty = 0;
					$gt_total_out_qty = 0;
					$gt_today_wash_iss_qty = 0;
					$gt_total_wash_iss_qty = 0;
					$gt_wash_iss_bal_qty = 0;
					$gt_today_wash_rcv_qty = 0;
					$gt_total_wash_rcv_qty = 0;
					$gt_wash_rcv_bal_qty = 0;
					foreach($data_array as $style=>$style_data)
					{						
						$style_order_qty = 0;
						$style_total_cut_qty = 0;
						$style_total_out_qty = 0;
						$style_today_wash_iss_qty = 0;
						$style_total_wash_iss_qty = 0;
						$style_wash_iss_bal_qty = 0;
						$style_today_wash_rcv_qty = 0;
						$style_total_wash_rcv_qty = 0;
						$style_wash_rcv_bal_qty = 0;
						foreach($style_data as $job=>$job_data)
						{
							foreach($job_data as $po_id=>$po_data)
							{
								$p = 0;
								foreach ($po_data as $color_id => $row) 
								{
									if($row['total_cutting']>0 || $row['total_issue_to_wash']>0 || $row['total_rcv_frm_wash']>0)
									{
										$wash_rcv_bal = $row['total_rcv_frm_wash'] - $row['total_issue_to_wash'];
										$wash_iss_bal = $row['total_issue_to_wash'] - $row['total_sewing_output'];
										$order_quantity = $order_qty_array[$po_id][$color_id];

										$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
											<? if($p==0){?>
											<td rowspan="<? echo $rowspan[$po_id];?>" width="100"><? echo $buyer_arr[$row['buyer_name']];?></td>
											<td rowspan="<? echo $rowspan[$po_id];?>" width="100"><? echo $row['job_no'];?></td>
											<td rowspan="<? echo $rowspan[$po_id];?>" width="100"><p><? echo $row['style_ref_no'];?></p></td>
											<td rowspan="<? echo $rowspan[$po_id];?>" width="100"><p><? echo $row['po_number'];?></p></td>
											<td rowspan="<? echo $rowspan[$po_id];?>" width="60" align="center"><? echo change_date_format($row['ship_date']);?></td>
											<? $p++;}?>
											<td width="100"><p><? echo $color_arr[$color_id];?></p></td>
											<td width="100" align="right"><? echo number_format($order_quantity,0);?></td>
						    				<td align="right" width="100"><? echo $row['total_cutting'];?></td>
						    				<td align="right" width="100"><? echo $row['total_sewing_output'];?></td>

						    				<td align="right" width="100"><? echo $row['today_issue_to_wash'];?></td>
						    				<td align="right" width="100">
						    					<a href="##" onClick="open_report_popup('<? echo $po_id;?>_<? echo $item_id;?>_<? echo $color_id; ?>_<? echo str_replace("'", "", $txt_production_date); ?>','2','open_wash_popup');">
							    					<? echo $row['total_issue_to_wash'];?>
							    				</a>			    						
						    				</td>
						    				<td align="right" width="100"><? echo $wash_iss_bal;?></td>

						    				<td align="right" width="100"><? echo $row['today_rcv_frm_wash'];?></td>
						    				<td align="right" width="100">
						    					<a href="##" onClick="open_report_popup('<? echo $po_id;?>_<? echo $item_id;?>_<? echo $color_id; ?>_<? echo str_replace("'", "", $txt_production_date); ?>','3','open_wash_popup');">
							    					<? echo $row['total_rcv_frm_wash'];?>
							    				</a>			    						
						    				</td>
						    				<td align="right" width="100"><? echo $wash_rcv_bal;?></td>
						    				
										</tr>
										<?
										$i++;								
										$style_order_qty += $order_quantity;
										$style_total_cut_qty += $row['total_cutting'];
										$style_total_out_qty += $row['total_sewing_output'];
										$style_today_wash_iss_qty += $row['today_issue_to_wash'];
										$style_total_wash_iss_qty += $row['total_issue_to_wash'];
										$style_wash_iss_bal_qty += $wash_iss_bal;
										$style_today_wash_rcv_qty += $row['today_rcv_frm_wash'];
										$style_total_wash_rcv_qty += $row['total_rcv_frm_wash'];
										$style_wash_rcv_bal_qty += $wash_rcv_bal;


										$gt_order_qty += $order_quantity;
										$gt_total_cut_qty += $row['total_cutting'];
										$gt_total_out_qty += $row['total_sewing_output'];
										$gt_today_wash_iss_qty += $row['today_issue_to_wash'];
										$gt_total_wash_iss_qty += $row['total_issue_to_wash'];
										$gt_wash_iss_bal_qty += $wash_iss_bal;
										$gt_today_wash_rcv_qty += $row['today_rcv_frm_wash'];
										$gt_total_wash_rcv_qty += $row['total_rcv_frm_wash'];
										$gt_wash_rcv_bal_qty += $wash_rcv_bal;
									}
								}
								
							}							
						}
						if($style_order_qty>0)
						{
						?>
						<tr bgcolor="dccdcd" style="text-align: right;font-weight: bold;">
							<td width="100"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="60"></td>
							<td width="100">Total</td>
							<td width="100"><? echo number_format($style_order_qty,0);?></td>
		    				<td width="100"><? echo number_format($style_total_cut_qty,0);?></td>
		    				<td width="100"><? echo number_format($style_total_out_qty,0);?></td>
		    				<td width="100"><? echo number_format($style_today_wash_iss_qty,0);?></td>
		    				<td width="100"><? echo number_format($style_total_wash_iss_qty,0);?></td>
		    				<td width="100"><? echo number_format($style_wash_iss_bal_qty,0);?></td>

		    				<td width="100"><? echo number_format($style_today_wash_rcv_qty,0);?></td>
		    				<td width="100"><? echo number_format($style_total_wash_rcv_qty,0);?></td>
		    				<td width="100"><? echo number_format($style_wash_rcv_bal_qty,0);?></td>	
						</tr>
						<?
					}
					}

					?>	
					</tbody>										
				</table>										  
			</div>	
			<!-- ============================== footer part =============================== -->
			<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="<? echo $tbl_width;?>" rules="all" align="left">
				<tfoot>
					<tr>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="60"></th>
						<th width="100">Total</th>
						<th width="100"><? echo number_format($gt_order_qty,0);?></th>
	    				<th width="100"><? echo number_format($gt_total_cut_qty,0);?></th>
	    				<th width="100"><? echo number_format($gt_total_out_qty,0);?></th>
	    				<th width="100"><? echo number_format($gt_today_wash_iss_qty,0);?></th>
	    				<th width="100"><? echo number_format($gt_total_wash_iss_qty,0);?></th>
	    				<th width="100"><? echo number_format($gt_wash_iss_bal_qty,0);?></th>

	    				<th width="100"><? echo number_format($gt_today_wash_rcv_qty,0);?></th>
	    				<th width="100"><? echo number_format($gt_total_wash_rcv_qty,0);?></th>
	    				<th width="100"><? echo number_format($gt_wash_rcv_bal_qty,0);?></th>	    				
					</tr>
				</tfoot>										
			</table>
		</fieldset>
    	<?
	}
	elseif ($type==5) // Cutting status button
	{
		$sql_cond 		= "";
		$sql_cond .= ($company_id !="") ? " and a.company_name in($company_id)" : "";
		$sql_cond .= ($working_company_id !="") ? " and d.serving_company in($working_company_id)" : "";
		// $sql_cond .= ($location_id !=0) ? " and d.location=$location_id" : "";
		$sql_cond .= ($buyer_id !=0) ? " and a.buyer_name=$buyer_id" : "";
		$sql_cond .= ($style_no !="") ? " and a.style_ref_no='$style_no'" : "";
		$sql_cond .= ($hidden_job_id !="") ? " and a.id=$hidden_job_id" : "";
		$sql_cond .= ($order_no !="") ? " and b.po_number='$order_no'" : "";
		$sql_cond .= ($hidden_order_id !="") ? " and b.id=$hidden_order_id" : "";
		$sql_cond .= ($company_id !=0) ? " and a.company_name=$company_id" : "";
		//$sql_cond .= ($shipment_status !=0) ? " and b.shiping_status=$shipment_status" : "";
		$sql_cond .= ($shipment_status ==3) ? " and b.shiping_status=$shipment_status" : "";
		$sql_cond .= ($shipment_status ==2) ? " and b.shiping_status<3" : "";	

		// ============================== get today prod po ==========================
		$prod_po_arr=return_library_array( "SELECT po_break_down_id,po_break_down_id as po_id from  pro_garments_production_mst where status_active=1 and is_deleted=0 and production_date=$txt_production_date and production_type in(1,4)", "po_break_down_id", "po_id"  );
		// print_r($prod_po_arr);die;	
		$po_id_cond = where_con_using_array($prod_po_arr,0,"b.id");	
		
		// ============================================ FOR PRODUCTION ================================================
		
		$sql="SELECT a.JOB_NO, a.COMPANY_NAME, a.BUYER_NAME, a.STYLE_REF_NO, b.id as PO_ID, b.PO_NUMBER,c.item_number_id as ITEM_ID,c.color_number_id as COLOR_ID,d.serving_company,d.location,d.sewing_line,d.prod_reso_allo,max(b.pub_Shipment_date) as SHIP_DATE,
		sum(c.order_quantity) as ORDER_QUANTITY, 

		sum(case when d.production_type=1 and e.production_type=1 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as TODAY_CUTTING ,
		sum(case when d.production_type=1 and e.production_type=1 and d.production_date<=$txt_production_date then e.production_qnty else 0 end ) as TOTAL_CUTTING ,
		(case when d.production_type=1  then d.remarks end ) as REMARKS ,

		sum(case when d.production_type=4 and e.production_type=4 and d.production_date=$txt_production_date then e.production_qnty else 0 end ) as TODAY_SEWING_INPUT ,
		sum(case when d.production_type=4 and e.production_type=4  and d.production_date<=$txt_production_date then e.production_qnty else 0 end ) as TOTAL_SEWING_INPUT

		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e
		where a.id=b.job_id and b.id=c.po_break_down_id and d.po_break_down_id=b.id and d.po_break_down_id=c.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and a.id=c.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1  and e.is_deleted=0  $sql_cond $po_id_cond and d.production_type in(1,4) and e.production_qnty>0
		group by a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id, b.po_number,c.item_number_id,c.color_number_id,d.serving_company,d.location,d.production_type,d.remarks,d.sewing_line,d.prod_reso_allo";

		// echo $sql;die;
		$sql_res = sql_select($sql);
		if(count($sql_res)==0)
		{
			?>
			<div style="margin:20px auto; width: 90%">
				<div class="alert alert-error">
				  <strong>Data not found!</strong> Change a few things then try submitting again.
				</div>
			</div>
			<?
			disconnect($con);
			die();
		} 

		$data_array = $data_array_without_line = array();
		$others_date_array = array();
		$po_id_array = array();
		$chk_arr = array();
		foreach($sql_res as $vals)
		{
			$sewing_line='';
			if($vals['PROD_RESO_ALLO']==1)
			{
				$line_number=explode(",",$prod_reso_arr[$vals['SEWING_LINE']]);
				foreach($line_number as $value)
				{
					if($sewing_line=='') $sewing_line=$lineArr[$value]; else $sewing_line.=",".$lineArr[$value];
				}
			}
			else
			{ 
				$sewing_line=$lineArr[$vals['SEWING_LINE']];
			}
			
			if($sewing_line !='')
			{
				$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["job_no"] = $vals["JOB_NO"];			
				$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["buyer_name"] = $vals["BUYER_NAME"];	
				$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["style_ref_no"] = $vals["STYLE_REF_NO"];			 
				$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["ship_date"] = $vals["SHIP_DATE"];	
				$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["job_no"] = $vals["JOB_NO"];			
				$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["po_number"] = $vals["PO_NUMBER"];
				$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["remarks"] = $vals["REMARKS"];

				$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["today_sewing_input"]+=$vals["TODAY_SEWING_INPUT"];
				$data_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["total_sewing_input"]+=$vals["TOTAL_SEWING_INPUT"];
			}
			else
			{
				$data_array_without_line[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["job_no"] = $vals["JOB_NO"];			
				$data_array_without_line[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["buyer_name"] = $vals["BUYER_NAME"];	
				$data_array_without_line[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["style_ref_no"] = $vals["STYLE_REF_NO"];			 
				$data_array_without_line[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["ship_date"] = $vals["SHIP_DATE"];	
				$data_array_without_line[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["job_no"] = $vals["JOB_NO"];			
				$data_array_without_line[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["po_number"] = $vals["PO_NUMBER"];
				$data_array_without_line[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["remarks"] = $vals["REMARKS"];

				$data_array_without_line[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["today_sewing_input"]+=$vals["TODAY_SEWING_INPUT"];
				$data_array_without_line[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]][$sewing_line]["total_sewing_input"]+=$vals["TOTAL_SEWING_INPUT"];
			}				 
			// if($chk_arr[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]=="")
			$others_date_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["today_cutting"]+=$vals["TODAY_CUTTING"];
			$others_date_array[$vals["STYLE_REF_NO"]][$vals["JOB_NO"]][$vals["PO_ID"]][$vals["ITEM_ID"]][$vals["COLOR_ID"]]["total_cutting"]+=$vals["TOTAL_CUTTING"];			
			
			$po_id_array[$vals["PO_ID"]] = $vals["PO_ID"];
		}

		$poIds = implode(",", $po_id_array);

		if(count($po_id_array)>999 && $db_type==2)
	    {
	     	$po_chunk=array_chunk($po_id_array, 999);
	     	$po_ids_cond= "";
	     	foreach($po_chunk as $vals)
	     	{
	     		$imp_ids=implode(",", $vals);
	     		if($po_ids_cond=="") 
	     		{
	     			$po_ids_cond.=" and ( po_break_down_id in ($imp_ids) ";
	     		}
	     		else
	     		{
	     			$po_ids_cond.=" or po_break_down_id in ($imp_ids) ";
	     		}
	     	}
	     	 $po_ids_cond.=" )";
	    }
	    else
	    {
	     	$po_ids_cond= " and po_break_down_id in($poIds) ";
	    }
		
		$tbl_width = 1280;
		$colspan = 13;
		// =======================================
		$rowspan = $rowspan_color = $rowspan_without_line = $rowspan_color_without_line = array();
		$color_wise_tot_input = array();
		foreach($data_array as $style=>$style_data)
		{
			foreach($style_data as $job=>$job_data)
			{
				foreach($job_data as $po_id=>$po_data)
				{
					foreach($po_data as $item_id=>$item_data)
					{
						foreach ($item_data as $color_id => $color_data) 
						{
							foreach ($color_data as $line => $row) 
							{
								if($others_date_array[$style][$job][$po_id][$item_id][$color_id]['total_cutting']>0 || $row['total_sewing_input']>0 || $row['today_sewing_input']>0)
								{
									$rowspan[$po_id][$item_id]++;
									$rowspan_color[$po_id][$item_id][$color_id]++;
									$color_wise_tot_input[$style][$job][$po_id][$item_id][$color_id] += $row['total_sewing_input'];
								}
							}
						}
					}
				}
			}
		}

		foreach($data_array_without_line as $style=>$style_data)
		{
			foreach($style_data as $job=>$job_data)
			{
				foreach($job_data as $po_id=>$po_data)
				{
					foreach($po_data as $item_id=>$item_data)
					{
						foreach ($item_data as $color_id => $color_data) 
						{
							foreach ($color_data as $line => $row) 
							{
								if($others_date_array[$style][$job][$po_id][$item_id][$color_id]['total_cutting']>0 || $row['total_sewing_input']>0 || $row['today_sewing_input']>0)
								{
									$rowspan_without_line[$po_id][$item_id]++;
									$rowspan_color_without_line[$po_id][$item_id][$color_id]++;
									$color_wise_tot_input[$style][$job][$po_id][$item_id][$color_id] += $row['total_sewing_input'];
								}
							}
						}
					}
				}
			}
		}
		// echo "<pre>";print_r($data_array);die;
		// ======================================== order qty =================================================
	    // $po_ids_conds = str_replace("a.po_break_down_id", "po_break_down_id", $po_ids_cond);
	    $sql = "SELECT item_number_id as ITEM_ID,color_number_id as COLOR_ID ,po_break_down_id as PO_ID, ORDER_QUANTITY from wo_po_color_size_breakdown where status_active in(1,2,3) and is_deleted=0 $po_ids_cond";
	    // echo $sql;
	    $sql_res = sql_select($sql);
	    $order_qty_array = array();
	    foreach ($sql_res as $val) 
	    {
	    	$order_qty_array[$val['PO_ID']][$val['ITEM_ID']][$val['COLOR_ID']] += $val['ORDER_QUANTITY'];
	    }
	    // print_r($order_qty_array);
		
		ob_start();	
		?>
		<fieldset style="width:<? echo $tbl_width+20;?>px;">	
			<!-- ============================ Title part ============================ -->
			<div>		
		        <table width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0"> 
		            <tr class="form_caption">
		            	<td colspan="<? echo $colspan;?>" align="center"><strong>Color Wise RMG Production Status V2</strong></td> 
		            </tr>
		            <tr class="form_caption">
		            	<td colspan="<? echo $colspan;?>" align="center"><strong><? echo $company_arr[$company_id]; ?></strong></td> 
		            </tr>
		            <tr class="form_caption">
		            	<td colspan="<? echo $colspan;?>" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_production_date)) ); ?></strong></td> 
		            </tr>
		        </table>
		    </div>
		    <!-- =========================== header part =============================== -->
		    <div>
		    	<table cellspacing="0" width="<? echo $tbl_width;?>" cellpadding="0" border="1" class="rpt_table" rules="all" align="left">
		    		<thead>
		    			<tr>
		    				<th rowspan="2" width="100">Buyer</th>
		    				<th rowspan="2" width="100">Job No</th>
		    				<th rowspan="2" width="100">Style</th>
		    				<th rowspan="2" width="100">Order No.</th>
		    				<th rowspan="2" width="100">Gmts Item</th>
		    				<th rowspan="2" width="100">Color</th>
		    				<th rowspan="2" width="100">Order Qty</th>
		    				<th colspan="6" width="480">Cutting(Pcs)</th>
		    				<th rowspan="2" width="100">Remarks</th>
		    			</tr>
		    			<tr>
		    				<th width="80">Today (Cut)</th>
		    				<th width="80">Total (Cut)</th>
		    				<th width="80">Line Name</th>
		    				<th width="80">Today Input</th>
		    				<th width="80">Total Input</th>
		    				<th width="80">Balance</th>
		    			</tr>
		    		</thead>
		    	</table>
		    </div>
		    <!-- ============================== body part ========================= -->
			<div style="max-height:400px; overflow-y:scroll; width:<? echo $tbl_width+20;?>px;float: left;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="<? echo $tbl_width;?>" rules="all" id="table_body" align="left">
					<tbody>
					<?
					$i=1;
					$gt_order_qty = 0;
					$gt_today_cut_qty = 0;
					$gt_total_cut_qty = 0;
					$gt_today_in_qty = 0;
					$gt_total_in_qty = 0;
					$gt_bal_qty = 0;
					foreach($data_array as $style=>$style_data)
					{
						$style_order_qty = 0;
						$style_today_cut_qty = 0;
						$style_total_cut_qty = 0;
						$style_today_in_qty = 0;
						$style_total_in_qty = 0;
						$style_bal_qty = 0;
						foreach($style_data as $job=>$job_data)
						{
							foreach($job_data as $po_id=>$po_data)
							{
								foreach($po_data as $item_id=>$item_data)
								{
									$p = 0;
									foreach ($item_data as $color_id => $color_data) 
									{
										$clr = 0;
										foreach ($color_data as $line_name => $row) 
										{
											if($others_date_array[$style][$job][$po_id][$item_id][$color_id]['total_cutting']>0 || $row['total_sewing_input']>0 || $row['today_sewing_input']>0)
											{
												
												$order_quantity = $order_qty_array[$po_id][$item_id][$color_id];

												$today_cutting = $others_date_array[$style][$job][$po_id][$item_id][$color_id]['today_cutting'];
												$total_cutting = $others_date_array[$style][$job][$po_id][$item_id][$color_id]['total_cutting'];
												$color_wise_tot_input_qty = $color_wise_tot_input[$style][$job][$po_id][$item_id][$color_id];

												$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
												?>
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
													<? if($p==0){?>
													<td valign="middle" rowspan="<? echo $rowspan[$po_id][$item_id];?>" width="100"><p><? echo $buyer_arr[$row['buyer_name']];?></p></td>
													<td valign="middle" rowspan="<? echo $rowspan[$po_id][$item_id];?>" width="100"><p><? echo $row['job_no'];?></p></td>
													<td valign="middle" rowspan="<? echo $rowspan[$po_id][$item_id];?>" width="100"><p><? echo $row['style_ref_no'];?></p></td>
													<td valign="middle" rowspan="<? echo $rowspan[$po_id][$item_id];?>" width="100"><p><? echo $row['po_number'];?></p></td>
													
													<td valign="middle" rowspan="<? echo $rowspan[$po_id][$item_id];?>" width="100"><p><? echo $garments_item[$item_id];?></p></td>
													<? $p++;} if($clr==0){ ?>
													<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" width="100"><p><? echo $color_arr[$color_id];?></p></td>
													<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" width="100" align="right"><? echo number_format($order_quantity,0);?></td>

								    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="right" width="80"><? echo number_format($today_cutting,0);?></td>
								    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="right" width="80"><? echo number_format($total_cutting,0);?></td>
								    				<? } ?>
								    				<td width="80" align="center"><p><? echo $line_name;?></p></td>
								    				<td align="right" width="80"><? echo number_format($row['today_sewing_input'],0);?></td>
								    				
								    				<td align="right" width="80"><? echo number_format($row['total_sewing_input'],0);?></td>
								    				<? if($clr==0)
								    				{ 								    					
								    					$balance = $total_cutting - $color_wise_tot_input_qty;
								    					?>
								    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" align="right" width="80"><? echo number_format($balance,0);
								    				// echo $total_cutting ."-". $color_wise_tot_input_qty;	?></td>
								    		
								    				<td rowspan="<? echo $rowspan_color[$po_id][$item_id][$color_id];?>" width="100"><p><? echo $row['remarks'];?></p></td>
								    				<? 
								    				$clr++;
													$style_order_qty += $order_quantity;
													$style_today_cut_qty += $today_cutting;
													$style_total_cut_qty += $total_cutting;
													$style_bal_qty += $balance;

													$gt_order_qty += $order_quantity;
													$gt_today_cut_qty += $today_cutting;
													$gt_total_cut_qty += $total_cutting;
													$gt_bal_qty += $balance;
													$others_date_array[$style][$job][$po_id][$item_id][$color_id]['today_cutting'] = 0;
													$others_date_array[$style][$job][$po_id][$item_id][$color_id]['total_cutting'] = 0;
													}
								    				?>
								    				
												</tr>
												<?
												$i++;
												$style_today_in_qty += $row['today_sewing_input'];
												$style_total_in_qty += $row['total_sewing_input'];
												
												$gt_today_in_qty += $row['today_sewing_input'];
												$gt_total_in_qty += $row['total_sewing_input'];
											}
										}
									}
								}
							}
						}
						?>
						<tr bgcolor="#dccdcd" style="text-align: right;font-weight: bold;">
							<td width="100"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100">Style Total</td>
							<td width="100"><? echo number_format($style_order_qty,0);?></td>
		    				<td width="80"><? echo number_format($style_today_cut_qty,0);?></td>
		    				<td width="80"><? echo number_format($style_total_cut_qty,0);?></td>
		    				<td width="80"></td>
		    				<td width="80"><? echo number_format($style_today_in_qty,0);?></td>
		    				<td width="80"><? echo number_format($style_total_in_qty,0);?></td>
		    				<td width="80"><? echo number_format($style_bal_qty,0);?></td>
		    				<td width="100"></td>
						</tr>
						<?
					}

					foreach($data_array_without_line as $style=>$style_data)
					{
						$style_order_qty = 0;
						$style_today_cut_qty = 0;
						$style_total_cut_qty = 0;
						$style_today_in_qty = 0;
						$style_total_in_qty = 0;
						$style_bal_qty = 0;
						foreach($style_data as $job=>$job_data)
						{
							foreach($job_data as $po_id=>$po_data)
							{
								foreach($po_data as $item_id=>$item_data)
								{
									$p = 0;
									foreach ($item_data as $color_id => $color_data) 
									{
										$clr = 0;
										foreach ($color_data as $line_name => $row) 
										{
											if($others_date_array[$style][$job][$po_id][$item_id][$color_id]['total_cutting']>0 || $row['total_sewing_input']>0 || $row['today_sewing_input']>0)
											{
												
												$order_quantity = $order_qty_array[$po_id][$item_id][$color_id];

												$today_cutting = $others_date_array[$style][$job][$po_id][$item_id][$color_id]['today_cutting'];
												$total_cutting = $others_date_array[$style][$job][$po_id][$item_id][$color_id]['total_cutting'];
												$color_wise_tot_input_qty = $color_wise_tot_input[$style][$job][$po_id][$item_id][$color_id];

												$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
												?>
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
													<? if($p==0){?>
													<td valign="middle" rowspan="<? echo $rowspan_without_line[$po_id][$item_id];?>" width="100"><p><? echo $buyer_arr[$row['buyer_name']];?></p></td>
													<td valign="middle" rowspan="<? echo $rowspan_without_line[$po_id][$item_id];?>" width="100"><p><? echo $row['job_no'];?></p></td>
													<td valign="middle" rowspan="<? echo $rowspan_without_line[$po_id][$item_id];?>" width="100"><p><? echo $row['style_ref_no'];?></p></td>
													<td valign="middle" rowspan="<? echo $rowspan_without_line[$po_id][$item_id];?>" width="100"><p><? echo $row['po_number'];?></p></td>
													
													<td valign="middle" rowspan="<? echo $rowspan_without_line[$po_id][$item_id];?>" width="100"><p><? echo $garments_item[$item_id];?></p></td>
													<? $p++;} if($clr==0){ ?>
													<td rowspan="<? echo $rowspan_color_without_line[$po_id][$item_id][$color_id];?>" width="100"><p><? echo $color_arr[$color_id];?></p></td>
													<td rowspan="<? echo $rowspan_color_without_line[$po_id][$item_id][$color_id];?>" width="100" align="right"><? echo number_format($order_quantity,0);?></td>

								    				<td rowspan="<? echo $rowspan_color_without_line[$po_id][$item_id][$color_id];?>" align="right" width="80"><? echo number_format($today_cutting,0);?></td>
								    				<td rowspan="<? echo $rowspan_color_without_line[$po_id][$item_id][$color_id];?>" align="right" width="80"><? echo number_format($total_cutting,0);?></td>
								    				<? } ?>
								    				<td width="80" align="center"><p><? echo $line_name;?></p></td>
								    				<td align="right" width="80"><? echo number_format($row['today_sewing_input'],0);?></td>
								    				
								    				<td align="right" width="80"><? echo number_format($row['total_sewing_input'],0);?></td>
								    				<? if($clr==0)
								    				{ 								    					
								    					$balance = $total_cutting - $color_wise_tot_input_qty;
								    					?>
								    				<td rowspan="<? echo $rowspan_color_without_line[$po_id][$item_id][$color_id];?>" align="right" width="80"><? echo number_format($balance,0); ?></td>
								    		
								    				<td rowspan="<? echo $rowspan_color_without_line[$po_id][$item_id][$color_id];?>" width="100"><p><? echo $row['remarks'];?></p></td>
								    				<? 
								    				$clr++;
													$style_order_qty += $order_quantity;
													$style_today_cut_qty += $today_cutting;
													$style_total_cut_qty += $total_cutting;
													$style_bal_qty += $balance;

													$gt_order_qty += $order_quantity;
													$gt_today_cut_qty += $today_cutting;
													$gt_total_cut_qty += $total_cutting;
													$gt_bal_qty += $balance;
													}
								    				?>
								    				
												</tr>
												<?
												$i++;
												$style_today_in_qty += $row['today_sewing_input'];
												$style_total_in_qty += $row['total_sewing_input'];
												
												$gt_today_in_qty += $row['today_sewing_input'];
												$gt_total_in_qty += $row['total_sewing_input'];
											}
										}
									}
								}
							}
						}
						if($style_today_cut_qty>0 || $style_total_cut_qty>0)
						{
							?>
							<tr bgcolor="#dccdcd" style="text-align: right;font-weight: bold;">
								<td width="100"></td>
								<td width="100"></td>
								<td width="100"></td>
								<td width="100"></td>
								<td width="100"></td>
								<td width="100">Style Total</td>
								<td width="100"><? echo number_format($style_order_qty,0);?></td>
								<td width="80"><? echo number_format($style_today_cut_qty,0);?></td>
								<td width="80"><? echo number_format($style_total_cut_qty,0);?></td>
								<td width="80"></td>
								<td width="80"><? echo number_format($style_today_in_qty,0);?></td>
								<td width="80"><? echo number_format($style_total_in_qty,0);?></td>
								<td width="80"><? echo number_format($style_bal_qty,0);?></td>
								<td width="100"></td>
							</tr>
							<?
						}
					}

					?>	
					</tbody>										
				</table>										  
			</div>	
			<!-- ============================== footer part =============================== -->
			<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="<? echo $tbl_width;?>" rules="all" align="left">
				<tfoot>
					<tr>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100">Total</th>
						<th width="100"><? echo number_format($gt_order_qty,0);?></th>
	    				<th width="80"><? echo number_format($gt_today_cut_qty,0);?></th>
	    				<th width="80"><? echo number_format($gt_total_cut_qty,0);?></th>
	    				<th width="80"></th>
	    				<th width="80"><? echo number_format($gt_today_in_qty,0);?></th>
	    				<th width="80"><? echo number_format($gt_total_in_qty,0);?></th>
	    				<th width="80"><? echo number_format($gt_bal_qty,0);?></th>
	    				<th width="100"></th>  				
					</tr>
				</tfoot>										
			</table>
		</fieldset>
    	<?
	}

    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name2=time();
    $summary_filename="summary_".$name2.".xls";
    $create_new_doc2 = fopen($summary_filename, 'w');	
    $is_created2 = fwrite($create_new_doc2, $summary_html);
    //======================================================
	
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$summary_filename";
	disconnect($con);
	exit();	 
	
}

if($action=="open_gmts_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	// print_r($data);
	$ex_data 	= explode("_", $data);
	$po_id 		= $ex_data[0];
	$item_id 	= $ex_data[1];
	$color_id 	= $ex_data[2];
	$date 		= $ex_data[3];	

	$lineArr = return_library_array("select id, line_name from lib_sewing_line","id","line_name");
	$prod_reso_arr=return_library_array( "SELECT id, line_number from prod_resource_mst",'id','line_number');

	// ========================== load library =======================
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$date_cond = "";
	if($type=='1')
	{
		$date_cond = " and d.production_date='$date'";
	}
	else
	{
		$date_cond = " and d.production_date<='$date'";
	}
	
	$sql = "SELECT a.JOB_NO,a.BUYER_NAME,a.STYLE_REF_NO,b.PO_NUMBER,c.COLOR_NUMBER_ID,c.ITEM_NUMBER_ID,d.PRODUCTION_DATE,d.SEWING_LINE,e.PRODUCTION_QNTY,d.PROD_RESO_ALLO from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d, pro_garments_production_dtls e where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and b.id=$po_id and c.color_number_id=$color_id and c.item_number_id=$item_id and d.production_type=5 and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $date_cond";
	// echo $sql;
	$sql_res = sql_select($sql);

	$data_array = array();
	foreach ($sql_res as $val) 
	{
		$sewing_line='';
		if($val['PROD_RESO_ALLO']==1)
		{
			$line_number=explode(",",$prod_reso_arr[$val['SEWING_LINE']]);
			foreach($line_number as $value)
			{
				if($sewing_line=='') $sewing_line=$lineArr[$value]; else $sewing_line.=",".$lineArr[$value];
			}
		}
		else
		{ 
			$sewing_line=$lineArr[$val['SEWING_LINE']];
		}
		$data_array[$sewing_line][$val['PRODUCTION_DATE']] += $val['PRODUCTION_QNTY'];
	}

	// echo "<pre>";print_r($data_array);
	
	$tbl_width = 330;
	$colspan = count($size_arr);
	?>
	<style type="text/css">
		hr{
			border-top: 1px solid #8DAFDA;
			border-width: 1px;
		}
	</style>
	<div style="width:100%" align="center">
		<fieldset style="width:330px"> 
		<div class="form_caption" align="center"><strong>Total Sewing Output</strong></div><br />
		<div>
	        <table cellpadding="0" width="<? echo $tbl_width;?>" rules="all">
	        	<thead>
	        		<tr>
	        			<th align="left">Job No:</th><th align="left"><? echo $sql_res[0]['JOB_NO'];?></th>
	        			<th align="left">Buyer:</th><th align="left"><? echo $buyer_arr[$sql_res[0]['BUYER_NAME']];?></th>
	        		</tr>
	        		<tr>
	        			<th align="left">Style:</th><th align="left"><? echo $sql_res[0]['STYLE_REF_NO'];?></th>
	        			<th align="left">Item:</th><th align="left"><? echo $garments_item[$sql_res[0]['ITEM_NUMBER_ID']];?></th>
	        		</tr>
	        		<tr>
	        			<th align="left">PO No:</th><th align="left"><? echo $sql_res[0]['PO_NUMBER'];?></th>
	        			<th align="left">Color:</th><th align="left"><? echo $color_arr[$sql_res[0]['COLOR_NUMBER_ID']];?></th>
	        		</tr>
	        	</thead>
	        </table>
        </div>	
        <br />
            <table cellpadding="0" width="<? echo $tbl_width;?>" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Line</th>
                        <th width="100">Date</th>
                        <th width="100">Sewing Qty</th>
                     </tr>
                </thead>
                <tbody>	 	
					<?
					$i=1;
					$total = 0;
					foreach ($data_array as $line_name => $line_data) 
					{
						foreach ($line_data as $date_key => $val) 
						{						
							?>
							<tr>
								<td width="30"><? echo $i;?></td>
								<td width="100"><? echo $line_name;?></td>
								<td width="100" align="center" ><? echo change_date_format($date_key);?></td>
								<td width="100" align="right"><? echo $val;?></td>
							</tr>
							<?
							$i++;
							$total += $val;
						}
					}
					?>						
                </tbody>
                <tfoot>
                	<tr>
                		<th></th>
                		<th></th>
                		<th></th>
                		<th><? echo number_format($total,0); ?></th>
                	</tr>
                </tfoot>
            </table>
        </fieldset>
    </div>    
    <?	
}

if($action=="open_wash_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	// print_r($data);
	$ex_data 	= explode("_", $data);
	$po_id 		= $ex_data[0];
	$item_id 	= $ex_data[1];
	$color_id 	= $ex_data[2];
	$date 		= $ex_data[3];	

	$lineArr = return_library_array("select id, line_name from lib_sewing_line","id","line_name");
	$prod_reso_arr=return_library_array( "SELECT id, line_number from prod_resource_mst",'id','line_number');

	// ========================== load library =======================
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$item_cond = "";
	if($item_id !="")
	{
		$item_cond = " and c.item_number_id=$item_id";
	}

	if($type=='2')
	{
		$date_cond = " and d.production_date<='$date'";
	}
	else
	{
		$date_cond = " and d.production_date<='$date'";
	}
		
	$sql = "SELECT a.JOB_NO,a.BUYER_NAME,a.STYLE_REF_NO,b.PO_NUMBER,c.COLOR_NUMBER_ID,c.ITEM_NUMBER_ID,d.PRODUCTION_DATE,d.CHALLAN_NO,e.PRODUCTION_QNTY,d.SERVING_COMPANY,d.PRODUCTION_SOURCE from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,pro_garments_production_mst d, pro_garments_production_dtls e where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and b.id=$po_id and c.color_number_id=$color_id $item_cond and b.id=d.po_break_down_id and d.id=e.mst_id and c.id=e.color_size_break_down_id and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and d.production_type=$type and d.embel_name=3 $date_cond"; // and d.production_date<='$date'
	// echo $sql;
	$sql_res = sql_select($sql);

	$data_array = array();
	foreach ($sql_res as $val) 
	{
		$data_array[$val['PRODUCTION_DATE']][$val['CHALLAN_NO']][$val['SERVING_COMPANY']]['qty'] += $val['PRODUCTION_QNTY'];
		$data_array[$val['PRODUCTION_DATE']][$val['CHALLAN_NO']][$val['SERVING_COMPANY']]['source'] = $val['PRODUCTION_SOURCE'];
	}

	// echo "<pre>";print_r($data_array);
	
	$tbl_width = 430;
	$colspan = count($size_arr);
	?>
	<style type="text/css">
		hr{
			border-top: 1px solid #8DAFDA;
			border-width: 1px;
		}
	</style>
	<div style="width:100%" align="center">
		<fieldset style="width:430px"> 
		<div class="form_caption" align="center"><strong>Embellishment <? echo ($type==2) ? "Send" : "Receive";?> Info</strong></div><br />
		<div>
	        <table cellpadding="0" width="<? echo $tbl_width;?>" rules="all">
	        	<thead>
	        		<tr>
	        			<th align="left">Job No:</th><th align="left"><? echo $sql_res[0]['JOB_NO'];?></th>
	        			<th align="left">Buyer:</th><th align="left"><? echo $buyer_arr[$sql_res[0]['BUYER_NAME']];?></th>
	        		</tr>
	        		<tr>
	        			<th align="left">Style:</th><th align="left"><? echo $sql_res[0]['STYLE_REF_NO'];?></th>
	        			<th align="left">Item:</th><th align="left"><? echo $garments_item[$sql_res[0]['ITEM_NUMBER_ID']];?></th>
	        		</tr>
	        		<tr>
	        			<th align="left">PO No:</th><th align="left"><? echo $sql_res[0]['PO_NUMBER'];?></th>
	        			<th align="left">Color:</th><th align="left"><? echo $color_arr[$sql_res[0]['COLOR_NUMBER_ID']];?></th>
	        		</tr>
	        	</thead>
	        </table>
        </div>	
        <br />
            <table cellpadding="0" width="<? echo $tbl_width;?>" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Date</th>
                        <th width="100">System ID</th>
                        <th width="100">Wash Qty</th>
                        <th width="100">Emb. Company</th>
                     </tr>
                </thead>
                <tbody>	 	
					<?
					$i=1;
					$total = 0;
					foreach ($data_array as $date_key => $date_data) 
					{
						foreach ($date_data as $sys_key => $sys_data) 
						{						
							foreach ($sys_data as $com_id => $val) 
							{
								?>
								<tr>
									<td width="30"><? echo $i;?></td>
									<td width="100"><? echo change_date_format($date_key);?></td>
									<td width="100" align="center" ><? echo $sys_key;?></td>
									<td width="100" align="right"><? echo $val['qty'];?></td>
									<td width="100">
									<?										
									echo ($val['source']==1) ? $company_arr[$com_id] : $supplier_arr[$com_id];
									?>											
									</td>
								</tr>
								<?
								$i++;
								$total += $val['qty'];
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
                		<th><? echo number_format($total,0); ?></th>
                		<th></th>
                	</tr>
                </tfoot>
            </table>
        </fieldset>
    </div>    
    <?	
}
disconnect($con);
?>
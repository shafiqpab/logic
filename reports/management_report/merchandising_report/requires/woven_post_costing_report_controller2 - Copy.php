<?
session_start();
//ini_set('memory_limit','3072M');
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.commisions.php');
require_once('../../../../includes/class4/class.commercials.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.washes.php');
require_once('../../../../includes/class4/class.fabrics.php');


/*require_once('../../../../includes/class.reports.php');
require_once('../../../../includes/class.yarns.php');*/

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------

$user_name = $_SESSION['logic_erp']["user_id"];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/woven_post_costing_report_controller', this.value, 'load_drop_down_brand', 'brand_td');" );     	 
	exit();
}
if ($action=="load_drop_down_brand")
{
	 //echo "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_id_cond order by brand_name ASC";
	echo create_drop_down( "cbo_brand_id", 100, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}
if($action=="search_season_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Season No Info", "../../../../", 1, 1,'','','');
	?>
	<script>
		$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });
		
		var selected_id = new Array; var selected_name = new Array;
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
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_season_id').val( id );
			$('#hide_season').val( name );
		}
    </script>
    </head>
    <body>
        <div align="center">
            <form name="styleRef_form" id="styleRef_form">
                <fieldset style="width:350px;">
                    <input type="hidden" name="hide_season" id="hide_season" value="" />
                     <input type="hidden" name="hide_season_id" id="hide_season_id" value="" />
                    <?
					$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
                        if($buyerID==0)
                        {
                            if ($_SESSION['logic_erp']["data_level_secured"]==1)
                            {
                                if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
                            }
                            else $buyer_id_cond="";
                        }
                        else $buyer_id_cond=" and buyer_id=$buyerID";
                        
                     
							
							$arr=array (1=>$buyer_name_arr);
						
						  $sql="select id,season_name,buyer_id from lib_buyer_season where status_active=1 and is_deleted=0 $buyer_id_cond order by season_name";
                        //echo $sql;	
                        echo create_list_view("tbl_list_search", "season_name,Buyer", "100,200","300","280",0, $sql , "js_set_value", "id,season_name", "", 1, "0,buyer_id", $arr , "season_name,buyer_id", "","",'0,0','',1) ;
                        ?>
                </fieldset>
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}
if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		
		var selected_id = new Array; var selected_name = new Array;
		
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
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_order_id').val( id );
			$('#hide_order_no').val( name );
		}
	
    </script>

</head>

<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:780px;">
            <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Order No</th>
                    <th>Shipment Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
					<input type="hidden" name="hide_pre_cost_ver_id" id="hide_pre_cost_ver_id" value="<? echo $cbo_pre_cost_class;?>" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('hide_pre_cost_ver_id').value, 'create_order_no_search_list_view', 'search_div', 'woven_post_costing_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    	</td>
                    </tr>
                    <tr>
                        <td colspan="5" valign="middle"><? echo load_month_buttons(1); ?></td>
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

if($action=="create_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$pre_cost_ver_id=$data[6];

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
	
	$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$arr=array (0=>$company_short_arr,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	if($pre_cost_ver_id==1)
	{
		//$entry_form_cond="and c.entry_from=111";
	}
	else
	{
		//$entry_form_cond="and c.entry_from=158";
	}
	$sql="select b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and  b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and c.garments_nature=3 and $search_field like '$search_string' $buyer_id_cond $date_cond $entry_form_cond order by b.id, b.pub_shipment_date";
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "70,70,50,70,150,180","760","210",0, $sql , "js_set_value", "id,po_number","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3','',1) ;
   exit(); 
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$brand_library=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name");
	$season_library=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");
	

	$company_name=str_replace("'","",$cbo_company_name);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$cbo_status=str_replace("'","",$cbo_status);
	$cbo_brand_id=str_replace("'","",$cbo_brand_id);
	$cbo_season_year=str_replace("'","",$cbo_season_year);
	$txt_season_id=str_replace("'","",$txt_season_id);
			
	//*cbo_brand_id*cbo_season_year*txt_season_id
	if($txt_ref_no!='') $ref_cond="and b.grouping='$txt_ref_no'";else $ref_cond="";
	if($txt_file_no!='') $file_cond="and b.file_no=$txt_file_no";else $file_cond="";
	if($cbo_brand_id>0) $brand_cond="and a.brand_id in($cbo_brand_id)";else  $brand_cond="";
	//if($txt_season_id>0) $season_cond="and a.season in($txt_season_id)";else  $season_cond="";
	if($txt_season_id>0) $season_cond="and a.season_buyer_wise in($txt_season_id)";else  $season_cond="";
	//echo $season_cond.'DDDD';;
	if($cbo_season_year>0) $season_year_cond="and a.season_year=$cbo_season_year";else  $season_year_cond="";
	
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
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if(trim($txt_job_no)!="") $job_no=trim($txt_job_no); else $job_no="%%";
	
	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		else $year_cond="";
	}
	else $year_cond="";
	
	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$date_cond=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
	}
	
	$po_id_cond="";
	if(trim(str_replace("'","",$txt_order_no))!="")
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id_cond=" and b.id in(".str_replace("'","",$hide_order_id).")";
		}
		else
		{
			$po_id_cond=" and LOWER(b.po_number) like LOWER('%".trim(str_replace("'","",$txt_order_no))."%')";
		}
	}
	
	$shipping_status_cond='';
	if(str_replace("'","",$shipping_status)!=0)
	{
		$shipping_status_cond=" and b.shiping_status=$shipping_status";
	}
	$cbo_status_cond='';
	if($cbo_status!=0)
	{
		$cbo_status_cond=" and b.is_confirmed in($cbo_status)";
	}
	
	$exchange_rate=76; $po_ids_array=array();
	
	
		
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
		
	$sql="select a.id as job_id,a.job_no_prefix_num, a.job_no, $year_field, a.company_name,a.season_buyer_wise as season,a.brand_id,a.season_year, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.set_smv, b.id, b.po_number,b.grouping,b.file_no, b.pub_shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.job_no=b.job_no_mst and c.garments_nature=3 and a.company_name='$company_name' and a.job_no_prefix_num like '$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=$cbo_order_status and b.is_deleted=0 $date_cond $buyer_id_cond $po_id_cond $ref_cond $file_cond $shipping_status_cond $year_cond $cbo_status_cond $brand_cond $season_cond $season_year_cond order by b.pub_shipment_date, a.job_no_prefix_num, b.id";
	//echo $sql; //die;
	$result=sql_select($sql);
	$all_po_id="";  $all_jobs='';
	foreach($result as $row)
	{
		if($all_po_id=="") $all_po_id=$row[csf("id")]; else $all_po_id.=",".$row[csf("id")];
		if($all_jobs=="") $all_jobs="'".$row[csf("job_no")]."'"; else $all_jobs.=",'".$row[csf("job_no")]."'";
		
		$job_idArr[$row[csf("job_id")]]=$row[csf("job_id")];
	}
	$all_jobs=implode(",",array_unique(explode(",",$all_jobs)));
	
	$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2="";$po_cond_for_in3=""; $po_cond_for_in4=""; $po_cond_for_in5=""; 
	$po_ids=count(array_unique(explode(",",$all_po_id)));
		if($db_type==2 && $po_ids>1000)
		{
			$po_cond_for_in=" and (";
			$po_cond_for_in2=" and (";
			$po_cond_for_in3=" and (";
			$po_cond_for_in4=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$po_cond_for_in.=" b.po_break_down_id in($ids) or"; 
				$po_cond_for_in2.=" po_id in($ids) or"; 
				$po_cond_for_in3.=" b.order_id in($ids) or";
				$po_cond_for_in4.=" b.po_breakdown_id in($ids) or"; 
				$po_cond_for_in5.=" c.po_break_down_id in($ids) or"; 
			}
			$po_cond_for_in=chop($po_cond_for_in,'or ');
			$po_cond_for_in.=")";
			
			$po_cond_for_in2=chop($po_cond_for_in2,'or ');
			$po_cond_for_in2.=")";
			$po_cond_for_in3=chop($po_cond_for_in3,'or ');
			$po_cond_for_in3.=")";
			$po_cond_for_in4=chop($po_cond_for_in4,'or ');
			$po_cond_for_in4.=")";
			$po_cond_for_in5=chop($po_cond_for_in5,'or ');
			$po_cond_for_in5.=")";
		}
		else
		{
			$poIds=implode(",",array_unique(explode(",",$all_po_id)));
			
			if($poIds==""){$po_cond_for_in="";}else{$po_cond_for_in=" and b.po_break_down_id in($poIds)";}
			if($poIds==""){$po_cond_for_in2="";}else{$po_cond_for_in2=" and po_id in($poIds)";}
			if($poIds==""){$po_cond_for_in3="";}else{$po_cond_for_in3=" and b.order_id in($poIds)";}
			if($poIds==""){$po_cond_for_in4="";}else{$po_cond_for_in4=" and b.po_breakdown_id in($poIds)";}
			if($poIds==""){$po_cond_for_in5="";}else{$po_cond_for_in5=" and c.po_break_down_id in($poIds)";}
			
			/*$po_cond_for_in=" and b.po_break_down_id in($poIds)";
			$po_cond_for_in2=" and po_id in($poIds)";
			$po_cond_for_in3=" and b.order_id in($poIds)";
			$po_cond_for_in4=" and b.po_breakdown_id in($poIds)";*/
		}
		
		$ex_factory_arr=return_library_array( "select b.po_break_down_id, 
	sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as qnty 
	from pro_ex_factory_mst b where b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_break_down_id", "po_break_down_id", "qnty");	
	
	
	ob_start();
	?>
    <fieldset>
    	<table width="2330">
            <tr class="form_caption">
                <td colspan="24" align="center"><strong>Post Costing Report</strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="24" align="center"><strong><? echo $company_arr[$company_name];?></strong>
                    <br>
                    <strong><? echo change_date_format(str_replace("'","",$txt_date_from)) .' To '. change_date_format(str_replace("'","",$txt_date_to)); ?></strong>
                </td>
            </tr>
        </table>
        <table style="margin-top:10px" id="table_header_1" class="rpt_table" width="3740" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="70">Buyer</th>
                <th width="70">Brand</th>
                
                <th width="60">Job Year</th>
                <th width="70">Job No</th>
                <th width="100">PO No</th>
                <th width="70">File No</th>
                <th width="80">Ref. No</th>
                <th width="110">Style Name</th>
                <th width="70">Season</th>
                <th width="70">Season year</th>
                    
                <th width="120">Garments Item</th>
                <th width="90">PO Quantity</th>
                <th width="50">UOM</th>
                <th width="70">Unit Price</th>
                <th width="110">PO Value</th>
                <th width="100">SMV</th>
                <th width="80">Shipment Date</th>
                <th width="100">Shipping Status</th>
                <th width="100">Cost Source</th>
                <th width="100">PO/Ex-Factory Qnty</th>
                <th width="110">PO/Ex-Factory Value</th>
                <th width="110">Fabric Purchase Cost</th>
                <th width="110">Trims Cost</th>
                <th width="100">Embellishment Cost</th>
                <th width="100">Wash Cost</th>
                <th width="100">Commission Cost</th>
                <th width="100">Commercial Cost</th>
                <th width="100">Freight Cost</th>
                <th width="100">Testing Cost</th>
                <th width="100">Inspection Cost</th>
                <th width="100">Courier Cost</th>
                <th width="100">CM Cost</th>
                <th width="100">Toal Cost</th>
                <th width="100">Margin</th>
                <th width="100">% to Ex-Factory Value</th>
            </thead>
        </table>
    	<div style="width:3762px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="3740" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
            <?
				$fabriccostArray=array(); $yarncostArray=array(); $trimsCostArray=array(); $prodcostArray=array(); $actualCostArray=array(); $actualTrimsCostArray=array(); 
				$subconCostArray=array(); $embellCostArray=array(); $washCostArray=array(); $aopCostArray=array(); $yarnTrimsCostArray=array(); 
				//$job_idArr
				$job_id_cond=where_con_using_array($job_idArr,0,'job_id');
				$job_id_condA=where_con_using_array($job_idArr,0,'a.job_id');
				$yarncostDataArray=sql_select("select job_no, sum(amount) as amnt, sum(rate*avg_cons_qnty) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 $job_id_cond  group by job_no");
				foreach($yarncostDataArray as $yarnRow)
				{
				   $yarncostArray[$yarnRow[csf('job_no')]]=$yarnRow[csf('amount')];
				}
				
				$fabriccostDataArray=sql_select("select job_no, costing_per_id, embel_cost, wash_cost, cm_cost, commission, currier_pre_cost, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where status_active=1 and is_deleted=0 $job_id_cond");
				foreach($fabriccostDataArray as $fabRow)
				{
					 $fabriccostArray[$fabRow[csf('job_no')]]['costing_per_id']=$fabRow[csf('costing_per_id')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['embel_cost']=$fabRow[csf('embel_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['wash_cost']=$fabRow[csf('wash_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['cm_cost']=$fabRow[csf('cm_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['commission']=$fabRow[csf('commission')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['currier_pre_cost']=$fabRow[csf('currier_pre_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['lab_test']=$fabRow[csf('lab_test')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['inspection']=$fabRow[csf('inspection')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['freight']=$fabRow[csf('freight')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['comm_cost']=$fabRow[csf('comm_cost')];
				}
				unset($fabriccostDataArray);
				
				$trimscostDataArray=sql_select("select b.po_break_down_id, sum(b.cons*a.rate) as total from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id and a.status_active=1 and a.is_deleted=0 $po_cond_for_in group by b.po_break_down_id");
				foreach($trimscostDataArray as $trimsRow)
				{
					 $trimsCostArray[$trimsRow[csf('po_break_down_id')]]=$trimsRow[csf('total')];
				}
				unset($trimscostDataArray);
				 
				$prodcostDataArray=sql_select("select job_no, 
									  sum(CASE WHEN cons_process=1 THEN amount END) AS knit_charge,
									  sum(CASE WHEN cons_process=30 THEN amount END) AS yarn_dye_charge,
									  sum(CASE WHEN cons_process=35 THEN amount END) AS aop_charge,
									  sum(CASE WHEN cons_process not in(1,2,30,35) THEN amount END) AS dye_finish_charge
									  from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0 group by job_no");
				foreach($prodcostDataArray as $prodRow)
				{
					$prodcostArray[$prodRow[csf('job_no')]]['knit_charge']=$prodRow[csf('knit_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['yarn_dye_charge']=$prodRow[csf('yarn_dye_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['aop_charge']=$prodRow[csf('aop_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['dye_finish_charge']=$prodRow[csf('dye_finish_charge')];
				}	
				unset($prodcostDataArray);
				$actualCostDataArray=sql_select("select cost_head,po_id,sum(amount_usd) as amount_usd from wo_actual_cost_entry where company_id=$company_name and status_active=1 and is_deleted=0 $po_cond_for_in2 group by cost_head,po_id");
				foreach($actualCostDataArray as $actualRow)
				{
				   $actualCostArray[$actualRow[csf('cost_head')]][$actualRow[csf('po_id')]]=$actualRow[csf('amount_usd')];
				}
				unset($actualCostDataArray);
				$subconInBillDataArray=sql_select("select b.order_id, 
									  sum(CASE WHEN a.process_id=2 THEN b.amount END) AS knit_bill,
									  sum(CASE WHEN a.process_id=4 THEN b.amount END) AS dye_finish_bill
									  from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id in(2,4) $po_cond_for_in3  group by b.order_id");
				foreach($subconInBillDataArray as $subRow)
				{
					$subconCostArray[$subRow[csf('order_id')]]['knit_bill']=$subRow[csf('knit_bill')];
					$subconCostArray[$subRow[csf('order_id')]]['dye_finish_bill']=$subRow[csf('dye_finish_bill')];
				}	
				unset($subconInBillDataArray);
				$subconOutBillDataArray=sql_select("select b.order_id, 
									  sum(CASE WHEN a.process_id=2 THEN b.amount END) AS knit_bill,
									  sum(CASE WHEN a.process_id=4 THEN b.amount END) AS dye_finish_bill
									  from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.process_id in(2,4) $po_cond_for_in3 group by b.order_id");
				foreach($subconOutBillDataArray as $subRow)
				{
					$subconCostArray[$subRow[csf('order_id')]]['knit_bill']+=$subRow[csf('knit_bill')];
					$subconCostArray[$subRow[csf('order_id')]]['dye_finish_bill']+=$subRow[csf('dye_finish_bill')];
				}
				unset($subconOutBillDataArray);
				$embell_type_arr=return_library_array( "select id, emb_name from wo_pre_cost_embe_cost_dtls where status_active=1 $job_id_cond", "id", "emb_name");	
				
				$bookingDataArray=sql_select("select a.booking_type, a.item_category, a.currency_id, a.exchange_rate, b.po_break_down_id, b.process, b.amount, b.pre_cost_fabric_cost_dtls_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.item_category in(4,12,25) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in");
				foreach($bookingDataArray as $woRow)
				{
					$amount=0; $trimsAmnt=0;
					if($woRow[csf('currency_id')]==1) { $amount=$woRow[csf('amount')]/$exchange_rate; } else { $amount=$woRow[csf('amount')]; }
					
					if($woRow[csf('item_category')]==25 && ($woRow[csf('booking_type')]==3 || $woRow[csf('booking_type')]==6)) 
					{ 
						if($embell_type_arr[$woRow[csf('pre_cost_fabric_cost_dtls_id')]]==3)
						{
							$washCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
						}
						else
						{
							$embellCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
						}
					}
					else if($woRow[csf('item_category')]==12 && $woRow[csf('process')]==35 && ($woRow[csf('booking_type')]==3 || $woRow[csf('booking_type')]==6)) 
					{ 
						$aopCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
					}
					else if($woRow[csf('item_category')]==4)
					{
						if($woRow[csf('currency_id')]==1) { $trimsAmnt=$woRow[csf('amount')]/$woRow[csf('exchange_rate')]; } else { $trimsAmnt=$woRow[csf('amount')]; }
						$actualTrimsCostArray[$woRow[csf('po_break_down_id')]]+=$trimsAmnt; 
					}
				}	
				
				$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master where item_category_id=1", "id", "avg_rate_per_unit"  );
				$receive_array=array();
				$sql_receive="select a.currency_id,a.receive_purpose,b.prod_id, (b.order_qnty) as qty, (b.order_amount) as amnt,b.cons_quantity,b.cons_amount from inv_receive_master a, inv_transaction b where  a.id=b.mst_id and b.transaction_type=1 and b.item_category=1 and b.status_active=1 and b.is_deleted=0";
				$resultReceive = sql_select($sql_receive);
				foreach($resultReceive as $invRow)
				{
					if($invRow[csf('currency_id')]==1)//Taka
					{
						$avg_rate=$invRow[csf('cons_amount')]/$invRow[csf('cons_quantity')];
						$receive_array[$invRow[csf('prod_id')]]=$avg_rate/$exchange_rate;
					}
					else if($invRow[csf('currency_id')]==2)//USD
					{
						$avg_rate=$invRow[csf('amnt')]/$invRow[csf('qty')];
						$receive_array[$invRow[csf('prod_id')]]=$avg_rate;
					}
					else
					{
						
						$receive_array[$invRow[csf('prod_id')]]=$avg_rate;
					}
				}
				
				$yarnTrimsDataArray=sql_select("select b.po_breakdown_id, b.prod_id, a.item_category, 
					sum(CASE WHEN a.transaction_type=2 and b.entry_form ='3' and b.trans_type=2 and b.issue_purpose!=2 THEN b.quantity ELSE 0 END) AS yarn_iss_qty,
					sum(CASE WHEN a.transaction_type=4 and b.entry_form ='9' and b.trans_type=4 THEN b.quantity ELSE 0 END) AS yarn_iss_return_qty,
					sum(CASE WHEN a.transaction_type=5 and b.entry_form ='11' and b.trans_type=5 THEN b.quantity ELSE 0 END) AS trans_in_qty_yarn,
					sum(CASE WHEN a.transaction_type=6 and b.entry_form ='11' and b.trans_type=6 THEN b.quantity ELSE 0 END) AS trans_out_qty_yarn,
					sum(CASE WHEN a.transaction_type=2 and b.entry_form ='25' and b.trans_type=2 THEN a.cons_rate*b.quantity ELSE 0 END) AS trims_issue_amnt
					from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category in(1,4) and a.transaction_type in(2,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in4  group by b.po_breakdown_id, b.prod_id, a.item_category");
				foreach($yarnTrimsDataArray as $invRow)
				{
					if($invRow[csf('item_category')]==1)
					{
						$iss_qty=$invRow[csf('yarn_iss_qty')]+$invRow[csf('trans_in_qty_yarn')]-$invRow[csf('yarn_iss_return_qty')]-$invRow[csf('trans_out_qty_yarn')];
						$rate='';
						if($receive_array[$invRow[csf('prod_id')]]>0)
						{
							$rate=$receive_array[$invRow[csf('prod_id')]]/$exchange_rate;
						}
						else
						{
							$rate=$avg_rate_array[$invRow[csf('prod_id')]]/$exchange_rate;
						}
						
						$iss_amnt=$iss_qty*$rate;
						$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]][1]+=$iss_amnt;
					}
					else
					{
						$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]][4]+=$invRow[csf('trims_issue_amnt')];
					}
				}
				$ex_rate=80;
				$pi_number_check=array();
				
				$sqlPi=sql_select("select c.po_break_down_id, sum(b.quantity) as quantity from  com_pi_master_details a, com_pi_item_details b,wo_booking_dtls c where a.id=b.pi_id and b.work_order_no=c.booking_no  and a.item_category_id in(3,2) and a.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0 $po_cond_for_in5  group by c.po_break_down_id");
				//echo "select c.po_break_down_id, sum(b.quantity) as quantity from  com_pi_master_details a, com_pi_item_details b,wo_booking_dtls c where a.id=b.pi_id and b.work_order_no=c.booking_no  and a.item_category_id in(3,2) and a.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0 $po_cond_for_in5  group by c.po_break_down_id";
				
				foreach($sqlPi as $rowPi){
					
						$pi_number_check[$rowPi[csf('po_break_down_id')]]=$rowPi[csf('po_break_down_id')];
				}
				
				 $sql_fin_purchase="select  c.currency_id,b.po_breakdown_id, sum(a.cons_rate*b.quantity) as finish_purchase_amnt,sum(a.order_rate*b.quantity) as order_purchase_amnt,sum(a.order_amount) as order_amount from inv_receive_master c,inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and c.id=a.mst_id and a.receive_basis in(1,2) and a.item_category in(2,3) and a.transaction_type=1 and b.entry_form in(17,37) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in4 group by b.po_breakdown_id, c.currency_id";
				$dataArrayFinPurchase=sql_select($sql_fin_purchase);
				foreach($dataArrayFinPurchase as $finRow)
				{
					if($pi_number_check[$finRow[csf('po_breakdown_id')]]==$finRow[csf('po_breakdown_id')])
					{
						$currency_id=$finRow[csf('currency_id')];
						if($currency_id==2)//USD
						{
							$finish_purchase_amnt_arr[$finRow[csf('po_breakdown_id')]]=$finRow[csf('order_purchase_amnt')];
						}
						else
						{
							$finish_purchase_amnt_arr[$finRow[csf('po_breakdown_id')]]+=$finRow[csf('finish_purchase_amnt')]/$ex_rate;
						}
					}
				}
				//print_r($finish_purchase_amnt_arr);
				
				 
				/* $sql_fin_purchase_wv="SELECT a.id, a.booking_no,a.booking_date,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.booking_type,b.job_no,b.po_break_down_id as po_id,sum(b.grey_fab_qnty) as grey_fab_qnty,sum(b.amount) as amount
				from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c  
				where a.booking_no=b.booking_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.booking_type=1 and b.fin_fab_qnty>0
				 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1 $po_cond_for_in
				 group by a.id, a.booking_no,a.booking_date,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.booking_type,b.job_no,b.po_break_down_id";

				$dataArrayFinPurchaseW=sql_select($sql_fin_purchase_wv);
				foreach($dataArrayFinPurchaseW as $finRow)
				{
					$currency_id=$finRow[csf('currency_id')];
					if($currency_id==2)//USD
					{
						$finish_purchase_amnt_arr[$finRow[csf('po_id')]]+=$finRow[csf('amount')];
					}
					else
					{
						$finish_purchase_amnt_arr[$finRow[csf('po_id')]]+=$finRow[csf('amount')]/$ex_rate;
					}
				}*/
				
				
				$i=1; $tot_po_qnty=0; $tot_po_value=0; $tot_ex_factory_qnty=0; $tot_ex_factory_val=0; $tot_yarn_cost_mkt=0; $tot_knit_cost_mkt=0; $tot_dye_finish_cost_mkt=0; $tot_yarn_dye_cost_mkt=0; $tot_aop_cost_mkt=0; $tot_trims_cost_mkt=0; $tot_embell_cost_mkt=0; $tot_wash_cost_mkt=0; $tot_commission_cost_mkt=0; $tot_comm_cost_mkt=0; $tot_freight_cost_mkt=0; $tot_test_cost_mkt=0; $tot_inspection_cost_mkt=0; $tot_currier_cost_mkt=0; $tot_cm_cost_mkt=0; $tot_mkt_all_cost=0; $tot_mkt_margin=0; $tot_yarn_cost_actual=0; $tot_knit_cost_actual=0; $tot_dye_finish_cost_actual=0; $tot_yarn_dye_cost_actual=0; $tot_aop_cost_actual=0; $tot_trims_cost_actual=0; $tot_embell_cost_actual=0; $tot_wash_cost_actual=0; $tot_commission_cost_actual=0; $tot_comm_cost_actual=0; $tot_freight_cost_actual=0; $tot_test_cost_actual=0; $tot_inspection_cost_actual=0; $tot_currier_cost_actual=0; $tot_cm_cost_actual=0; $tot_actual_all_cost=0; $tot_actual_margin=0; $tot_fabric_purchase_cost_mkt=0; $tot_fabric_purchase_cost_actual=0;
				
				$buyer_name_array=array();

				$mkt_po_val_array = array(); $mkt_yarn_array = array(); $mkt_knit_array = array(); $mkt_dy_fin_array = array(); $mkt_yarn_dy_array = array();
				$mkt_aop_array = array(); $mkt_trims_array = array(); $mkt_emb_array = array(); $mkt_wash_array=array(); $mkt_commn_array=array(); $mkt_commercial_array=array();
				$mkt_freight_array = array(); $mkt_test_array = array(); $mkt_ins_array = array(); $mkt_courier_array = array(); $mkt_cm_array = array();
				$mkt_total_array = array(); $mkt_margin_array = array(); $mkt_fabric_purchase_array = array();
				
				$ex_factory_val_array= array(); $yarn_cost_array= array(); $knit_cost_array= array(); $dye_cost_array= array(); $yarn_dyeing_cost_array= array();
				$aop_n_others_cost_array= array(); $trims_cost_array= array(); $enbellishment_cost_array= array(); $wash_cost_array= array(); $commission_cost_array= array(); 
				$commercial_cost_array= array(); $freight_cost_array= array(); $testing_cost_array= array(); $inspection_cost_array= array(); $courier_cost_array= array();
				$cm_cost_array= array(); $total_cost_array= array(); $margin_array= array(); $ex_factory_array=array(); $actual_cm_amnt=array(); $actual_fabric_purchase_array = array();
				/*$JobArr=array();
				foreach($result as $row_yarn){
					$JobArr[]=$row_yarn[csf('job_no')];
				}
				$yarn= new yarn($JobArr,'job');
				$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
				$yarn->unsetDataArray();*/
				$po_id_cond="";
				if(trim(str_replace("'","",$txt_order_no))!="")
				{
					if(str_replace("'","",$hide_order_id)!="")
					{
						$po_id_cond=" and b.id in(".str_replace("'","",$hide_order_id).")";
					}
					else
					{
						$po_id_cond=" and LOWER(b.po_number) like LOWER('%".trim(str_replace("'","",$txt_order_no))."%')";
					}
				}
				
				 $condition= new condition();
				 $condition->company_name("=$company_name");
				 if(str_replace("'","",$cbo_buyer_name)>0){
					  $condition->buyer_name("=$cbo_buyer_name");
				 }
				 if(str_replace("'","",$txt_job_no) !=''){
					  $condition->job_no_prefix_num("=$txt_job_no");
				 }
				 if(str_replace("'","",$txt_job_no)=='')
				 {
					$condition->job_no("in($all_jobs)");
				 }
				if(trim(str_replace("'","",$txt_order_no))!='' && str_replace("'","",$hide_order_id)=='') 
				 {
					$condition->po_number(" like '%".trim(str_replace("'","",$txt_order_no))."%'"); 
				 }
				$order_id=str_replace("'","",$hide_order_id);
				 if(trim(str_replace("'","",$txt_order_no))!='' && str_replace("'","",$hide_order_id)!='') 
				 {
					$condition->po_id_in("$order_id"); 
				 }
				 if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
				 {
					 $start_date=str_replace("'","",$txt_date_from);
					 $end_date=str_replace("'","",$txt_date_to);
					 $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
					 //and b.po_received_date between '$start_date' and '$end_date' 
					// echo 'FFGG';
				 }
				 if(str_replace("'","",$cbo_status) >0){
					  $condition->is_confirmed("=$cbo_status");
				 }
				 if(str_replace("'","",$cbo_status)==0){
					  $condition->is_confirmed("in(1,2)");
				 }
				 /*if($txt_order_id!='' || $txt_order_id!=0)
				 {
					$condition->po_id("in($txt_order_id)"); 
				 }*/
				 
				 
				 $condition->init();
				
				 $yarn= new yarn($condition);
				
				 $yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
				 $conversion= new conversion($condition);
				 //  echo $conversion->getQuery();
				 $conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
				 $trims= new trims($condition);
				 $trims_costing_arr=$trims->getAmountArray_by_order();
				$emblishment= new emblishment($condition);
				$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
				$commission= new commision($condition);
				$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
				$commercial= new commercial($condition);
				$commercial_costing_arr=$commercial->getAmountArray_by_order();
				$other= new other($condition);
				$other_costing_arr=$other->getAmountArray_by_order();
				$wash= new wash($condition);
				$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();
				$fabric= new fabric($condition);
				$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
				
				//print_r($fabric_costing_arr);die;
				$not_yarn_dyed_cost_arr=array(1,2,30,35);
				foreach($result as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$po_ids_array[]=$row[csf('id')];
					$gmts_item='';
					$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
					foreach($gmts_item_id as $item_id)
					{
						if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
					}
					
					$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
					$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
					$unit_price=$row[csf('unit_price')]/$row[csf('ratio')];
					$po_value=$order_qnty_in_pcs*$unit_price;
					
					$tot_po_qnty+=$order_qnty_in_pcs; 
					$tot_po_value+=$po_value;
					
					$ex_factory_qty=$ex_factory_arr[$row[csf('id')]];
					$ex_factory_value=$ex_factory_qty*$unit_price;
					
					$tot_ex_factory_qnty+=$ex_factory_qty; 
					$tot_ex_factory_val+=$ex_factory_value; 
					
					$dzn_qnty=0;
					$costing_per_id=$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
					if($costing_per_id==1)
					{
						$dzn_qnty=12;
					}
					else if($costing_per_id==3)
					{
						$dzn_qnty=12*2;
					}
					else if($costing_per_id==4)
					{
						$dzn_qnty=12*3;
					}
					else if($costing_per_id==5)
					{
						$dzn_qnty=12*4;
					}
					else
					{
						$dzn_qnty=1;
					}

					$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
					$yarn_cost_mkt=$yarn_costing_arr[$row[csf('id')]];
					//$yarn_cost_mkt_data+=$yarn_costing_arr[$row[csf('id')]];
					//echo $row[csf('id')].'aa';die;
					//($plan_cut_qnty/$dzn_qnty)*$yarncostArray[$row[csf('job_no')]];
					//$knit_cost_mkt=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['knit_charge'];
					$knit_cost_mkt=array_sum($conversion_costing_arr_process[$row[csf('id')]][1]);//($plan_cut_qnty/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['knit_charge'];
					//echo $plan_cut_qnty."__".$dzn_qnty."__".$prodcostArray[$row[csf('job_no')]]['knit_charge'];
					//$dye_finish_cost_mkt=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['dye_finish_charge'];
					//$dye_finish_cost_mkt=($plan_cut_qnty/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['dye_finish_charge'];
					
					$dye_finish_cost_mkt=0;
					foreach($conversion_cost_head_array as $process_id=>$val)
					{
						if(!in_array($process_id,$not_yarn_dyed_cost_arr))
						{
							$dye_finish_cost_mkt+=array_sum($conversion_costing_arr_process[$row[csf('id')]][$process_id]);
						}
					}					//$yarn_dye_cost_mkt=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['yarn_dye_charge'];
					//$yarn_dye_cost_mkt=($plan_cut_qnty/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['yarn_dye_charge'];
					$yarn_dye_cost_mkt=array_sum($conversion_costing_arr_process[$row[csf('id')]][30]);
					//$aop_cost_mkt=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['aop_charge'];
					$aop_cost_mkt=array_sum($conversion_costing_arr_process[$row[csf('id')]][35]);//($plan_cut_qnty/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['aop_charge'];
					$trims_cost_mkt=$trims_costing_arr[$row[csf('id')]];//($order_qnty_in_pcs/$dzn_qnty)*$trimsCostArray[$row[csf('id')]];
					
					$print_amount=$emblishment_costing_arr_name[$row[csf('id')]][1];
					$embroidery_amount=$emblishment_costing_arr_name[$row[csf('id')]][2];
					$special_amount=$emblishment_costing_arr_name[$row[csf('id')]][4];
					$wash_cost=$emblishment_costing_arr_name_wash[$row[csf('id')]][3];
					$other_amount=$emblishment_costing_arr_name[$row[csf('id')]][5];
					$foreign_cost=$commission_costing_arr[$row[csf('id')]][1];
					$local_cost=$commission_costing_arr[$row[csf('id')]][2];
					
					$test_cost=$other_costing_arr[$row[csf('id')]]['lab_test'];
					$freight_cost=$other_costing_arr[$row[csf('id')]]['freight'];
					$inspection_cost=$other_costing_arr[$row[csf('id')]]['inspection'];
					$certificate_cost=$other_costing_arr[$row[csf('id')]]['certificate_pre_cost'];
					//$common_oh_cost=$other_costing_arr[$row[csf('id')]]['common_oh'];
					$currier_cost=$other_costing_arr[$row[csf('id')]]['currier_pre_cost'];
					$cm_cost=$other_costing_arr[$row[csf('id')]]['cm_cost'];
					
					//$embell_cost_mkt=($order_qnty_in_pcs/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['embel_cost'];
					//$wash_cost_mkt=($order_qnty_in_pcs/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['wash_cost'];
					$embell_cost_mkt=$print_amount+$embroidery_amount+$special_amount+$other_amount;
					//$embell_cost_mkt=($plan_cut_qnty/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['embel_cost'];
					//$wash_cost_mkt=($plan_cut_qnty/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['wash_cost'];
					//$comm_cost_mkt=($order_qnty_in_pcs/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['comm_cost'];
					//$freight_cost_mkt=($order_qnty_in_pcs/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['freight'];
					//$test_cost_mkt=($order_qnty_in_pcs/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['lab_test'];
					//$inspection_cost_mkt=($order_qnty_in_pcs/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['inspection'];
					//$currier_cost_mkt=($order_qnty_in_pcs/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['currier_pre_cost'];					//$cm_cost_mkt=($order_qnty_in_pcs/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['cm_cost'];
					//$commission_cost_mkt=($order_qnty_in_pcs/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['commission'];
					$wash_cost_mkt=$wash_cost;
					$commission_cost_mkt=$foreign_cost+$local_cost;
					$comm_cost_mkt=$commercial_costing_arr[$row[csf('id')]];
					$freight_cost_mkt=$freight_cost;
					$test_cost_mkt=$test_cost;
					$inspection_cost_mkt=$inspection_cost;
					$currier_cost_mkt=$currier_cost;
					$cm_cost_mkt=$cm_cost;
					
					$fabric_purchase_cost_mkt=array_sum($fabric_costing_arr['knit']['grey'][$row[csf('id')]])+array_sum($fabric_costing_arr['woven']['grey'][$row[csf('id')]]);
					
					$mkt_all_cost=$yarn_cost_mkt+$knit_cost_mkt+$dye_finish_cost_mkt+$yarn_dye_cost_mkt+$aop_cost_mkt+$trims_cost_mkt+$embell_cost_mkt+$wash_cost_mkt+$commission_cost_mkt+$comm_cost_mkt+$freight_cost_mkt+$test_cost_mkt+$inspection_cost_mkt+$currier_cost_mkt+$cm_cost_mkt+$fabric_purchase_cost_mkt;
					
					$mkt_margin=$po_value-$mkt_all_cost;
					if($mkt_margin>0 && $po_value>0)  $mkt_margin_perc=($mkt_margin/$po_value)*100;else $mkt_margin_perc="";
					
					//$yarn_cost_actual=($order_qnty_in_pcs/$dzn_qnty)*$yarncostArray[$row[csf('job_no')]];
					//$knit_cost_actual=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['knit_charge'];
					//$dye_finish_cost_actual=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['dye_finish_charge'];
					//$yarn_dye_cost_actual=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['yarn_dye_charge'];
					//$aop_cost_actual=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['aop_charge'];
					//$trims_cost_actual=($order_qnty_in_pcs/$dzn_qnty)*$trimsCostArray[$row[csf('id')]];
					
					$yarn_cost_actual=$yarnTrimsCostArray[$row[csf('id')]][1];
					//$trims_cost_actual=$yarnTrimsCostArray[$row[csf('id')]][4]/$exchange_rate;
					$trims_cost_actual=$actualTrimsCostArray[$row[csf('id')]];
					$yarn_dye_cost_actual=0;
					$aop_cost_actual=$aopCostArray[$row[csf('id')]];
					$embell_cost_actual=$embellCostArray[$row[csf('id')]];
					$wash_cost_actual=$washCostArray[$row[csf('id')]];
					$knit_cost_actual=$subconCostArray[$row[csf('id')]]['knit_bill']/$exchange_rate;
					$dye_finish_cost_actual=$subconCostArray[$row[csf('id')]]['dye_finish_bill']/$exchange_rate;
					$commission_cost_actual=($ex_factory_qty/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['commission'];
					$comm_cost_actual=$actualCostArray[6][$row[csf('id')]];
					$freight_cost_actual=$actualCostArray[2][$row[csf('id')]];
					$test_cost_actual=$actualCostArray[1][$row[csf('id')]];
					$inspection_cost_actual=$actualCostArray[3][$row[csf('id')]];
					$currier_cost_actual=$actualCostArray[4][$row[csf('id')]];
					$cm_cost_actual=$actualCostArray[5][$row[csf('id')]];
					
					$fabric_purchase_cost_actual=$finish_purchase_amnt_arr[$row[csf('id')]];
					// echo $fabric_purchase_cost_actual.', ';
					
					$actual_all_cost=$yarn_cost_actual+$knit_cost_actual+$dye_finish_cost_actual+$yarn_dye_cost_actual+$aop_cost_actual+$trims_cost_actual+$embell_cost_actual+$wash_cost_actual+$commission_cost_actual+$comm_cost_actual+$freight_cost_actual+$test_cost_actual+$inspection_cost_actual+$currier_cost_actual+$cm_cost_actual+$fabric_purchase_cost_actual;
					
					$actual_margin=$ex_factory_value-$actual_all_cost;
					if($ex_factory_value>0 && $actual_margin>0)  $actual_margin_perc=($actual_margin/$ex_factory_value)*100;else $actual_margin_perc="";
					
					$tot_yarn_cost_mkt+=$yarn_cost_mkt; 
					$tot_knit_cost_mkt+=$knit_cost_mkt; 
					$tot_dye_finish_cost_mkt+=$dye_finish_cost_mkt; 
					$tot_yarn_dye_cost_mkt+=$yarn_dye_cost_mkt; 
					$tot_aop_cost_mkt+=$aop_cost_mkt; 
					$tot_trims_cost_mkt+=$trims_cost_mkt; 
					$tot_embell_cost_mkt+=$embell_cost_mkt; 
					$tot_wash_cost_mkt+=$wash_cost_mkt; 
					$tot_commission_cost_mkt+=$commission_cost_mkt; 
					$tot_comm_cost_mkt+=$comm_cost_mkt; 
					$tot_freight_cost_mkt+=$freight_cost_mkt; 
					$tot_test_cost_mkt+=$test_cost_mkt; 
					$tot_inspection_cost_mkt+=$inspection_cost_mkt; 
					$tot_currier_cost_mkt+=$currier_cost_mkt; 
					$tot_cm_cost_mkt+=$cm_cost_mkt; 
					$tot_mkt_all_cost+=$mkt_all_cost; 
					$tot_mkt_margin+=$mkt_margin; 
					$tot_yarn_cost_actual+=$yarn_cost_actual; 
					$tot_knit_cost_actual+=$knit_cost_actual; 
					$tot_dye_finish_cost_actual+=$dye_finish_cost_actual;
					$tot_yarn_dye_cost_actual+=$yarn_dye_cost_actual; 
					$tot_aop_cost_actual+=$aop_cost_actual; 
					$tot_trims_cost_actual+=$trims_cost_actual; 
					$tot_embell_cost_actual+=$embell_cost_actual;
					$tot_wash_cost_actual+=$wash_cost_actual; 
					$tot_commission_cost_actual+=$commission_cost_actual; 
					$tot_comm_cost_actual+=$comm_cost_actual; 
					$tot_freight_cost_actual+=$freight_cost_actual; 
					$tot_test_cost_actual+=$test_cost_actual; 
					$tot_inspection_cost_actual+=$inspection_cost_actual; 
					$tot_currier_cost_actual+=$currier_cost_actual; 
					$tot_cm_cost_actual+=$cm_cost_actual; 
					$tot_actual_all_cost+=$actual_all_cost; 
					$tot_actual_margin+=$actual_margin;
					
					$tot_fabric_purchase_cost_mkt+=$fabric_purchase_cost_mkt;
					$tot_fabric_purchase_cost_actual+=$fabric_purchase_cost_actual;
					
					$buyer_name_array[$row[csf('buyer_name')]]=$buyer_arr[$row[csf('buyer_name')]];
					$mkt_po_val_array[$row[csf('buyer_name')]]+=$po_value;
					$mkt_yarn_array[$row[csf('buyer_name')]]+=$yarn_cost_mkt+$yarn_dye_cost_mkt;
					$mkt_knit_array[$row[csf('buyer_name')]]+=$knit_cost_mkt;
					$mkt_dy_fin_array[$row[csf('buyer_name')]]+=$dye_finish_cost_mkt;
					//$mkt_yarn_dy_array[$row[csf('buyer_name')]]+=$yarn_dye_cost_mkt;
					$mkt_aop_array[$row[csf('buyer_name')]]+=$aop_cost_mkt;
					$mkt_trims_array[$row[csf('buyer_name')]]+=$trims_cost_mkt;
					$mkt_emb_array[$row[csf('buyer_name')]]+=$embell_cost_mkt;
					$mkt_wash_array[$row[csf('buyer_name')]]+=$wash_cost_mkt;
					$mkt_commn_array[$row[csf('buyer_name')]]+=$commission_cost_mkt;
					$mkt_commercial_array[$row[csf('buyer_name')]]+=$comm_cost_mkt;
					$mkt_freight_array[$row[csf('buyer_name')]]+=$freight_cost_mkt;
					$mkt_test_array[$row[csf('buyer_name')]]+=$test_cost_mkt;
					$mkt_ins_array[$row[csf('buyer_name')]]+=$inspection_cost_mkt;
					$mkt_courier_array[$row[csf('buyer_name')]]+=$currier_cost_mkt;
					$mkt_cm_array[$row[csf('buyer_name')]]+=$cm_cost_mkt;
					$mkt_total_array[$row[csf('buyer_name')]]+=$mkt_all_cost;
					$mkt_margin_array[$row[csf('buyer_name')]]+=$mkt_margin;
					$mkt_fabric_purchase_array[$row[csf('buyer_name')]]+=$fabric_purchase_cost_mkt;
					
					$ex_factory_val_array[$row[csf('buyer_name')]]+=$ex_factory_value;
					$yarn_cost_array[$row[csf('buyer_name')]]+=$yarn_cost_actual;
					$knit_cost_array[$row[csf('buyer_name')]]+=$knit_cost_actual;
					$dye_cost_array[$row[csf('buyer_name')]]+=$dye_finish_cost_actual;
					//$yarn_dyeing_cost_array[$row[csf('buyer_name')]]+=$yarn_dye_cost_actual;
					$aop_n_others_cost_array[$row[csf('buyer_name')]]+=$aop_cost_actual;
					$trims_cost_array[$row[csf('buyer_name')]]+=$trims_cost_actual;
					$enbellishment_cost_array[$row[csf('buyer_name')]]+=$embell_cost_actual;
					$wash_cost_array[$row[csf('buyer_name')]]+=$wash_cost_actual;
					$commission_cost_array[$row[csf('buyer_name')]]+=$commission_cost_actual;
					$commercial_cost_array[$row[csf('buyer_name')]]+=$comm_cost_actual;
					$freight_cost_array[$row[csf('buyer_name')]]+=$freight_cost_actual;
					$testing_cost_array[$row[csf('buyer_name')]]+=$test_cost_actual;
					$inspection_cost_array[$row[csf('buyer_name')]]+=$inspection_cost_actual;
					$courier_cost_array[$row[csf('buyer_name')]]+=$currier_cost_actual;
					$cm_cost_array[$row[csf('buyer_name')]]+=$cm_cost_actual;
					$total_cost_array[$row[csf('buyer_name')]]+=$actual_all_cost;
					$margin_array[$row[csf('buyer_name')]]+=$actual_margin;
					$actual_fabric_purchase_array[$row[csf('buyer_name')]]+=$fabric_purchase_cost_actual;
					
					
				 $button_po="<a href='#' onClick=\"generate_po_report('".$company_name."','".$row[csf('id')]."','".$row[csf('job_no')]."','show_po_detail_report','1')\" '> ".$row[csf('po_number')]."<a/>";
					
				?>
                	<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                        <td width="40" rowspan="4"><? echo $i; ?></td>
                        <td width="70" rowspan="4"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                         <td width="70" rowspan="4"><p><? echo $brand_library[$row[csf('brand_id')]]; ?></p></td>
                        <td width="60" align="center" rowspan="4"><? echo $row[csf('year')]; ?></td>
                        <td width="70" align="center" rowspan="4"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                        <td width="100" rowspan="4" style="word-break:break-all;"><? echo  $button_po;//$row[csf('po_number')]; ?></td>
                        <td width="70" rowspan="4" style="word-break:break-all;"><? echo $row[csf('file_no')]; ?></td>
                        <td width="80" rowspan="4" style="word-break:break-all;"><? echo $row[csf('grouping')]; ?></td>
                        <td width="110" rowspan="4" style="word-break:break-all;"><? echo $row[csf('style_ref_no')]; ?></td>
                        <td width="70" rowspan="4"><p><? echo $season_library[$row[csf('season')]]; ?></p></td>
               			<td width="70" rowspan="4"><p><? echo $row[csf('season_year')]; ?></p></td>
                        <td width="120" rowspan="4" style="word-break:break-all;"><? echo $gmts_item; ?></td>
                        <td width="88" align="right" style="padding-right:2px" rowspan="4"><? echo $order_qnty_in_pcs; ?></td>
                        <td width="50" align="center" rowspan="4"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                        <td width="68" align="right" style="padding-right:2px" rowspan="4"><? echo number_format($unit_price,2,'.',''); ?></td>
                        <td width="108" align="right" style="padding-right:2px" rowspan="4"><? echo number_format($po_value,2,'.',''); ?></td>
                        <td width="98" align="right" style="padding-right:2px" rowspan="4"><? echo number_format($row[csf('set_smv')],2,'.',''); ?></td>
                        <td width="80" align="center" rowspan="4">
                        	
                        		<p><? if($row[csf("pub_shipment_date")] !="" && $row[csf("pub_shipment_date")] !="0000-00-00")  echo change_date_format($row[csf("pub_shipment_date")]); ?>&nbsp;</p>
                        	</td>
                        <td width="100" rowspan="4" style="word-break:break-all;"><? echo $shipment_status[$row[csf('shiping_status')]]; ?></td>
                        <td width="100" align="left"><b>Pre Costing</b></td>
                        <td width="98" align="right" style="padding-right:2px"><? echo number_format($order_qnty_in_pcs,0,'.',''); ?></td>
                        <td width="108" align="right" style="padding-right:2px"><? echo number_format($po_value,2,'.',''); ?></td>
                        <td width="108" style="padding-right:2px" align="right"><? echo number_format($fabric_purchase_cost_mkt,2,'.',''); ?></td>
                        <td width="108" style="padding-right:2px" align="right"><? echo number_format($trims_cost_mkt,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($embell_cost_mkt,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($wash_cost_mkt,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($commission_cost_mkt,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($comm_cost_mkt,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($freight_cost_mkt,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($test_cost_mkt,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($inspection_cost_mkt,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($currier_cost_mkt,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($cm_cost_mkt,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($mkt_all_cost,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($mkt_margin,2,'.',''); ?></td>
                        <td width="100" style="padding-right:2px" align="right"><? echo number_format($mkt_margin_perc,2,'.',''); ?></td>
                    </tr>
                    <tr bgcolor="#F5F5F5" onClick="change_color('tr_a<? echo $i; ?>','#F5F5F5')" id="tr_a<? echo $i; ?>">
                    	<td width="100" align="left"><b>Actual</b></td>
                        <td width="98" align="right" style="padding-right:2px">
						<a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $row[csf('job_no_prefix_num')];?>','<? echo $row[csf('id')]; ?>','650px')"><? echo  number_format($ex_factory_qty,0,'.',''); ?></a></td>
                        <td width="108" align="right" style="padding-right:2px"><? echo number_format($ex_factory_value,2,'.',''); ?></td>               
                                               
                        <td width="108" style="padding-right:2px" align="right" title="Exchange rate=80"> <a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','fabric_purchase_cost_actual','Fabric Purchase Cost Details','900px','<?=$company_name;?>')"><? echo number_format($fabric_purchase_cost_actual,2,'.',''); ?></a></td>
                        <td width="108" style="padding-right:2px" align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','trims_cost_actual','Trims Cost Details','800px')"><? echo number_format($trims_cost_actual,2,'.',''); ?></a></td>
                        <td width="98" style="padding-right:2px" align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','embell_cost_actual','Embellishment Cost Details','800px')"><? echo number_format($embell_cost_actual,2,'.',''); ?></a></td>
                        <td width="98" style="padding-right:2px" align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','wash_cost_actual','Wash Cost Details','800px')"><? echo number_format($wash_cost_actual,2,'.',''); ?></a></td>
                        <td width="98" style="padding-right:2px" align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]."_".$row[csf('job_no')]."_".$ex_factory_qty."_".$dzn_qnty; ?>','commission_cost_actual','Commission Cost Details','600px')"><? echo number_format($commission_cost_actual,2,'.',''); ?></a></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($comm_cost_actual,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($freight_cost_actual,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($test_cost_actual,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($inspection_cost_actual,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($currier_cost_actual,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($cm_cost_actual,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($actual_all_cost,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($actual_margin,2,'.',''); ?></td>
                        <td width="100" style="padding-right:2px" align="right"><? echo number_format($actual_margin_perc,2,'.',''); ?></td>
                    </tr>
                    <tr bgcolor="#E9E9E9" onClick="change_color('tr_v<? echo $i; ?>','#E9E9E9')" id="tr_v<? echo $i; ?>">
                    	<td width="100" align="left"><b>Variance</b></td>
                    	<td width="98" align="right" style="padding-right:2px">
							<? 
								$variance_qnty=$order_qnty_in_pcs-$ex_factory_qty;
								if($variance_qnty>0 && $order_qnty_in_pcs>0) $variance_qnty_per=($variance_qnty/$order_qnty_in_pcs)*100; 
								echo number_format($variance_qnty,0,'.',''); 
							?>
                        </td>
                    	<td width="108" align="right" style="padding-right:2px">
                        	<? 
								$variance_po_value=$po_value-$ex_factory_value;
								if($variance_po_value>0 && $po_value>0) $variance_po_value_per=($variance_po_value/$po_value)*100; 
								echo number_format($variance_po_value,2,'.',''); 
							?>
                        </td>
                        <td width="108" style="padding-right:2px" align="right">
                        	<? 
								$variance_finish_purchase_cost=$fabric_purchase_cost_mkt-$fabric_purchase_cost_actual;
								if($variance_finish_purchase_cost>0 && $fabric_purchase_cost_mkt>0)  $variance_finish_purchase_cost_per=($variance_finish_purchase_cost/$fabric_purchase_cost_mkt)*100; else  $variance_finish_purchase_cost_per="";
								echo number_format($variance_finish_purchase_cost,2,'.','');
							?>
                        </td>
                        <td width="108" style="padding-right:2px" align="right">
                        	<? 
								$variance_trims_cost=$trims_cost_mkt-$trims_cost_actual;
								if($variance_trims_cost>0 && $trims_cost_mkt>0)  $variance_trims_cost_per=($variance_trims_cost/$trims_cost_mkt)*100; 
								echo number_format($variance_trims_cost,2,'.','');
							?>
                        </td>
                        <td width="98" style="padding-right:2px" align="right">
                        	<? 
								$variance_embell_cost=$embell_cost_mkt-$embell_cost_actual;
								if($variance_embell_cost>0 && $embell_cost_mkt>0)  $variance_embell_cost_per=($variance_embell_cost/$embell_cost_mkt)*100; 
								echo number_format($variance_embell_cost,2,'.',''); 
							?>
                        </td>
                        <td width="98" style="padding-right:2px" align="right">
                        	<? 
								$variance_wash_cost=$wash_cost_mkt-$wash_cost_actual;
								if($variance_wash_cost>0 && $wash_cost_mkt>0) $variance_wash_cost_per=($variance_wash_cost/$wash_cost_mkt)*100; 
								echo number_format($variance_wash_cost,2,'.',''); 
							?>
                        </td>
                        <td width="98" style="padding-right:2px" align="right">
                        	<? 
								$variance_commission_cost=$commission_cost_mkt-$commission_cost_actual;
								if($variance_commission_cost>0 && $commission_cost_mkt>0) $variance_commission_cost_per=($variance_commission_cost/$commission_cost_mkt)*100; 
								echo number_format($variance_commission_cost,2,'.',''); 
							?>
                        </td>
                        <td width="98" style="padding-right:2px" align="right">
                        	<? 
								$variance_comm_cost=$comm_cost_mkt-$comm_cost_actual;
							if($variance_comm_cost>0 && $comm_cost_mkt>0) 	$variance_comm_cost_per=($variance_comm_cost/$comm_cost_mkt)*100; else 	$variance_comm_cost_per="";
								echo number_format($variance_comm_cost,2,'.',''); 
							?>
                        </td>
                        <td width="98" style="padding-right:2px" align="right">
                        	<? 
								$variance_freight_cost=$freight_cost_mkt-$freight_cost_actual;
								if($variance_freight_cost>0 && $freight_cost_mkt>0) $variance_freight_cost_per=($variance_freight_cost/$freight_cost_mkt)*100; else $variance_freight_cost_per="";
								echo number_format($variance_freight_cost,2,'.',''); 
							?>
                        </td>
                        <td width="98" style="padding-right:2px" align="right">
                        	<? 
								$variance_test_cost=$test_cost_mkt-$test_cost_actual;
								if($variance_test_cost>0 && $test_cost_mkt>0)  $variance_test_cost_per=($variance_test_cost/$test_cost_mkt)*100; else  $variance_test_cost_per="";
								echo number_format($variance_test_cost,2,'.','');
							?>
                        </td>
                        <td width="98" style="padding-right:2px" align="right">
                        	<? 
								$variance_inspection_cost=$inspection_cost_mkt-$inspection_cost_actual;
								if($variance_inspection_cost>0 && $inspection_cost_mkt>0) $variance_inspection_cost_per=($variance_inspection_cost/$inspection_cost_mkt)*100; else $variance_inspection_cost_per="";
								echo number_format($variance_inspection_cost,2,'.','');
							?>
                        </td>
                        <td width="98" style="padding-right:2px" align="right">
                        	<? 
								$variance_currier_cost=$currier_cost_mkt-$currier_cost_actual;
								if($currier_cost_mkt>0 && $variance_currier_cost>0) $variance_currier_cost_per=($variance_currier_cost/$currier_cost_mkt)*100;else $variance_currier_cost_per=""; 
								echo number_format($variance_currier_cost,2,'.','');
							?>
                        </td>
                        <td width="98" style="padding-right:2px" align="right">
                        	<? 
								$variance_cm_cost=$cm_cost_mkt-$cm_cost_actual;
								if($variance_cm_cost>0 && $cm_cost_mkt>0)  $variance_cm_cost_per=($variance_cm_cost/$cm_cost_mkt)*100; 
								echo number_format($variance_cm_cost,2,'.',''); 
							?>
                        </td>
                        <td width="98" style="padding-right:2px" align="right">
                        	<? 
								$variance_all_cost=$mkt_all_cost-$actual_all_cost;
								if($mkt_all_cost>0 && $variance_all_cost>0) $variance_all_cost_per=($variance_all_cost/$mkt_all_cost)*100; 
								echo number_format($variance_all_cost,2,'.','');
							?>
                        </td>
                        <td width="98" style="padding-right:2px" align="right">
                        	<? 
								$variance_margin_cost=$mkt_margin-$actual_margin;
								if($variance_margin_cost>0 && $mkt_margin>0) $variance_margin_cost_per=($variance_margin_cost/$mkt_margin)*100; else $variance_margin_cost_per="";
								echo number_format($variance_margin_cost,2,'.','');
							?>
                        </td>
                        <td width="100" style="padding-right:2px" align="right">
                        	<? 
								$variance_per_cost=$mkt_margin_perc-$actual_margin_perc;
								if($variance_per_cost>0 && $mkt_margin_perc>0) $variance_per_cost_per=($variance_per_cost/$mkt_margin_perc)*100; else $variance_per_cost_per="";
								echo number_format($variance_per_cost,2,'.','');
							?>
                        </td>
                    </tr>
                    <tr bgcolor="#DFDFDF" onClick="change_color('tr_vp<? echo $i; ?>','#DFDFDF')" id="tr_vp<? echo $i; ?>">
                    	<td width="100" align="left"><b>Variance (%)</b></td>
                    	<td width="98" align="right" style="padding-right:2px"><? echo number_format($variance_qnty_per,2,'.',''); ?></td>
                    	<td width="108" align="right" style="padding-right:2px"><? echo number_format($variance_po_value_per,2,'.',''); ?></td>
                        <td width="108" style="padding-right:2px" align="right"><? echo number_format($variance_finish_purchase_cost_per,2,'.',''); ?></td>
                        <td width="108" style="padding-right:2px" align="right"><? echo number_format($variance_trims_cost_per,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($variance_embell_cost_per,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($variance_wash_cost_per,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($variance_commission_cost_per,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($variance_comm_cost_per,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($variance_freight_cost_per,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($variance_test_cost_per,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($variance_inspection_cost_per,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($variance_currier_cost_per,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($variance_cm_cost_per,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($variance_all_cost_per,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($variance_margin_cost_per,2,'.',''); ?></td>
                        <td width="100" style="padding-right:2px" align="right"><? echo number_format($variance_per_cost_per,2,'.',''); ?></td>
                    </tr>
				<?
					$i++;
				}
				
				if($tot_mkt_margin>0 && $tot_po_value>0)  $tot_mkt_margin_perc=($tot_mkt_margin/$tot_po_value)*100;else $tot_mkt_margin_perc="";
				if($tot_actual_margin>0 && $tot_ex_factory_val>0)  $tot_actual_margin_perc=($tot_actual_margin/$tot_ex_factory_val)*100;else $tot_actual_margin_perc="";
				
				?>
            </table>
		</div>
		
        <table class="rpt_table" width="3740" cellpadding="0" cellspacing="0" border="1" rules="all">
            <tr bgcolor="#CCDDEE" onClick="change_color('tr_pt','#CCDDEE')" id="tr_pt" style="font-weight:bold;">
                <td colspan="11" style="padding-right:4px" align="right">Total</td>
                <td width="100" style="padding-right:2px" align="right"><? echo number_format($tot_po_qnty,0,'.',''); ?></td>
                <td width="57">&nbsp;</td>
                <td width="79">&nbsp;</td>
                <td width="123" style="padding-right:2px" align="right"><? echo number_format($tot_po_value,2,'.',''); ?></td>
                <td width="111" style="padding-right:2px" align="right">&nbsp;</td>
                <td width="92">&nbsp;</td>
                <td width="113">&nbsp;</td>
                <td width="114">Pre Costing Total</td>
                <td width="111" style="padding-right:2px" align="right"><? echo number_format($tot_po_qnty,0,'.',''); ?></td>
                <td width="123" style="padding-right:2px" align="right"><? echo number_format($tot_po_value,2,'.',''); ?></td>                
                <td width="123" style="padding-right:2px" align="right"><? echo number_format($tot_fabric_purchase_cost_mkt,2,'.',''); ?></td>
                <td width="123" style="padding-right:2px" align="right"><? echo number_format($tot_trims_cost_mkt,2,'.',''); ?></td>
                <td width="111" style="padding-right:2px" align="right"><? echo number_format($tot_embell_cost_mkt,2,'.',''); ?></td>
                <td width="112" style="padding-right:2px" align="right"><? echo number_format($tot_wash_cost_mkt,2,'.',''); ?></td>
                <td width="111" style="padding-right:2px" align="right"><? echo number_format($tot_commission_cost_mkt,2,'.',''); ?></td>
                <td width="111" style="padding-right:2px" align="right"><? echo number_format($tot_comm_cost_mkt,2,'.',''); ?></td>
                <td width="112" style="padding-right:2px" align="right"><? echo number_format($tot_freight_cost_mkt,2,'.',''); ?></td>
                <td width="112" style="padding-right:2px" align="right"><? echo number_format($tot_test_cost_mkt,2,'.',''); ?></td>
                <td width="112" style="padding-right:2px" align="right"><? echo number_format($tot_inspection_cost_mkt,2,'.',''); ?></td>
                <td width="110" style="padding-right:2px" align="right"><? echo number_format($tot_currier_cost_mkt,2,'.',''); ?></td>
                <td width="111" style="padding-right:2px" align="right"><? echo number_format($tot_cm_cost_mkt,2,'.',''); ?></td>
                <td width="113" style="padding-right:2px" align="right"><? echo number_format($tot_mkt_all_cost,2,'.',''); ?></td>
                <td width="112" style="padding-right:2px" align="right"><? echo number_format($tot_mkt_margin,2,'.',''); ?></td>
                <td width="113" style="padding-right:2px" align="right"><? echo number_format($tot_mkt_margin_perc,2,'.',''); ?></td>
            </tr>
            <tr bgcolor="#CCCCFF" onClick="change_color('tr_at','#CCCCFF')" id="tr_at" style="font-weight:bold;">
                <td colspan="11" style="padding-right:4px" align="right"></td>
                <td  style="padding-right:2px" align="right">&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td  style="padding-right:2px" align="right">&nbsp;</td>
                <td  style="padding-right:2px" align="right">&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >Actual Total</td>
                <td  align="right" style="padding-right:2px"><? echo number_format($tot_ex_factory_qnty,0,'.',''); ?></td>
                <td  align="right" style="padding-right:2px"><? echo number_format($tot_ex_factory_val,2,'.',''); ?></td>                
                <td style="padding-right:2px" align="right"><? echo number_format($tot_fabric_purchase_cost_actual,2,'.',''); ?></td>
                <td  align="right" style="padding-right:2px"><? echo number_format($tot_trims_cost_actual,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_embell_cost_actual,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_wash_cost_actual,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_commission_cost_actual,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_comm_cost_actual,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_freight_cost_actual,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_test_cost_actual,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_inspection_cost_actual,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_currier_cost_actual,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_cm_cost_actual,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_actual_all_cost,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_actual_margin,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_actual_margin_perc,2,'.',''); ?></td>
            </tr>
            <tr bgcolor="#FFEEFF" onClick="change_color('tr_vt','#FFEEFF')" id="tr_vt" style="font-weight:bold;">
                <td colspan="11" style="padding-right:4px" align="right"></td>
                <td  style="padding-right:2px" align="right">&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td  style="padding-right:2px" align="right">&nbsp;</td>
                <td  style="padding-right:2px" align="right">&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >Variance Total</td>
                <td  align="right" style="padding-right:2px">
					<? 
                        $tot_variance_qnty=$tot_po_qnty-$tot_ex_factory_qnty;
                       if($tot_variance_qnty>0 && $tot_po_qnty>0)  $tot_variance_qnty_per=($tot_variance_qnty/$tot_po_qnty)*100; else  $tot_variance_qnty_per="";
                        echo number_format($tot_variance_qnty,0,'.',''); 
                    ?>
                </td>
                <td  align="right" style="padding-right:2px">
                    <? 
                        $tot_variance_po_value=$tot_po_value-$tot_ex_factory_val;
                       if($tot_variance_po_value>0 && $tot_po_value>0)     $tot_variance_po_value_per=($tot_variance_po_value/$tot_po_value)*100; else  $tot_variance_po_value_per="";
                        echo number_format($tot_variance_po_value,2,'.',''); 
                    ?>
                </td>
                
                <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_fabric_purchase_cost=$tot_fabric_purchase_cost_mkt-$tot_fabric_purchase_cost_actual;
                       if($tot_variance_fabric_purchase_cost>0 && $tot_fabric_purchase_cost_mkt>0)    $tot_variance_fabric_purchase_cost_per=($tot_variance_fabric_purchase_cost/$tot_fabric_purchase_cost_mkt)*100; else  $tot_variance_fabric_purchase_cost_per="";
                        echo number_format($tot_variance_fabric_purchase_cost,2,'.','');
                    ?>
                </td>
                <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_trims_cost=$tot_trims_cost_mkt-$tot_trims_cost_actual;
                      if($tot_variance_trims_cost>0 && $tot_trims_cost_mkt>0)    $tot_variance_trims_cost_per=($tot_variance_trims_cost/$tot_trims_cost_mkt)*100;else  $tot_variance_trims_cost_per="";
                        echo number_format($tot_variance_trims_cost,2,'.','');
                    ?>
                </td>
                <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_embell_cost=$tot_embell_cost_mkt-$tot_embell_cost_actual;
                        if($tot_variance_embell_cost>0 && $tot_embell_cost_mkt>0)  $tot_variance_embell_cost_per=($tot_variance_embell_cost/$tot_embell_cost_mkt)*100; else $tot_variance_embell_cost_per="";
                        echo number_format($tot_variance_embell_cost,2,'.',''); 
                    ?>
                </td>
                <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_wash_cost=$tot_wash_cost_mkt-$tot_wash_cost_actual;
                       if($tot_variance_wash_cost>0 && $tot_wash_cost_mkt>0)  $tot_variance_wash_cost_per=($tot_variance_wash_cost/$tot_wash_cost_mkt)*100; else $tot_variance_wash_cost_per="";
                        echo number_format($tot_variance_wash_cost,2,'.',''); 
                    ?>
                </td>
                <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_commission_cost=$tot_commission_cost_mkt-$tot_commission_cost_actual;
                     if($tot_variance_commission_cost>0 && $tot_commission_cost_mkt>0) $tot_variance_commission_cost_per=($tot_variance_commission_cost/$tot_commission_cost_mkt)*100; else $tot_variance_commission_cost_per="";
                        echo number_format($tot_variance_commission_cost,2,'.',''); 
                    ?>
                </td>
                <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_comm_cost=$tot_comm_cost_mkt-$tot_comm_cost_actual;
                       if($tot_variance_comm_cost>0 && $tot_comm_cost_mkt>0)    $tot_variance_comm_cost_per=($tot_variance_comm_cost/$tot_comm_cost_mkt)*100;else   $tot_variance_comm_cost_per="";
                        echo number_format($tot_variance_comm_cost,2,'.',''); 
                    ?>
                </td>
                <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_freight_cost=$tot_freight_cost_mkt-$tot_freight_cost_actual;
                        if($tot_variance_freight_cost>0 && $tot_freight_cost_mkt>0)  $tot_variance_freight_cost_per=($tot_variance_freight_cost/$tot_freight_cost_mkt)*100; else  $tot_variance_freight_cost_per="";
                        echo number_format($tot_variance_freight_cost,2,'.',''); 
                    ?>
                </td>
                <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_test_cost=$tot_test_cost_mkt-$tot_test_cost_actual;
                        if($tot_variance_test_cost>0 && $tot_test_cost_mkt>0)   $tot_variance_test_cost_per=($tot_variance_test_cost/$tot_test_cost_mkt)*100;else  $tot_variance_test_cost_per="";
                        echo number_format($tot_variance_test_cost,2,'.','');
                    ?>
                </td>
                <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_inspection_cost=$tot_inspection_cost_mkt-$tot_inspection_cost_actual;
                       if($tot_variance_inspection_cost>0 && $tot_inspection_cost_mkt>0)  $tot_variance_inspection_cost_per=($tot_variance_inspection_cost/$tot_inspection_cost_mkt)*100; else $tot_variance_inspection_cost_per="";
                        echo number_format($tot_variance_inspection_cost,2,'.','');
                    ?>
                </td>
                <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_currier_cost=$tot_currier_cost_mkt-$tot_currier_cost_actual;
                       if($tot_variance_currier_cost>0 && $tot_currier_cost_mkt>0)   $tot_variance_currier_cost_per=($tot_variance_currier_cost/$tot_currier_cost_mkt)*100; else  $tot_variance_currier_cost_per="";
                        echo number_format($tot_variance_currier_cost,2,'.','');
                    ?>
                </td>
                <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_cm_cost=$tot_cm_cost_mkt-$tot_cm_cost_actual;
                       if($tot_variance_cm_cost>0 && $tot_cm_cost_mkt>0)   $tot_variance_cm_cost_per=($tot_variance_cm_cost/$tot_cm_cost_mkt)*100; else  $tot_variance_cm_cost_per="";
                        echo number_format($tot_variance_cm_cost,2,'.',''); 
                    ?>
                </td>
                <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_all_cost=$tot_mkt_all_cost-$tot_actual_all_cost;
                       if($tot_variance_all_cost>0 && $tot_mkt_all_cost>0)  $tot_variance_all_cost_per=($tot_variance_all_cost/$tot_mkt_all_cost)*100; else  $tot_variance_all_cost_per="";
                        echo number_format($variance_all_cost,2,'.','');
                    ?>
                </td>
                <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_margin_cost=$tot_mkt_margin-$tot_actual_margin;
                        if($tot_variance_margin_cost>0 && $tot_mkt_margin>0)   $tot_variance_margin_cost_per=($tot_variance_margin_cost/$tot_mkt_margin)*100;else  $tot_variance_margin_cost_per=""; 
                        echo number_format($tot_variance_margin_cost,2,'.','');
                    ?>
                </td>
                <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_per_cost=$tot_mkt_margin_perc-$tot_actual_margin_perc;
                       if($tot_variance_per_cost>0 && $tot_mkt_margin_perc>0)  $tot_variance_per_cost_per=($tot_variance_per_cost/$tot_mkt_margin_perc)*100; else $tot_variance_per_cost_per="";
                        echo number_format($tot_variance_per_cost,2,'.','');
                    ?>
                </td>
            </tr>
            <tr bgcolor="#CCCCEE" onClick="change_color('tr_vpt','#CCCCEE')" id="tr_vpt" style="font-weight:bold;">
                <td colspan="11" style="padding-right:4px" align="right"></td>
                <td  style="padding-right:2px" align="right">&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td  style="padding-right:2px" align="right">&nbsp;</td>
                <td  style="padding-right:2px" align="right">&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >Variance (%) Total</td>
                <td  align="right" style="padding-right:2px"><? echo number_format($tot_variance_qnty_per,0,'.',''); ?></td>
                <td  align="right" style="padding-right:2px"><? echo number_format($tot_variance_po_value_per,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_fabric_purchase_cost_per ,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_trims_cost_per,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_embell_cost_per,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_wash_cost_per,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_commission_cost_per,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_comm_cost_per,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_freight_cost_per,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_test_cost_per,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_inspection_cost_per,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_currier_cost_per,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_cm_cost_per,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_all_cost_per,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_margin_cost_per,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_per_cost_per,2,'.',''); ?></td>
            </tr>
        </table>
        <br />
        <table width="3370">
            <tr>
                <td width="760" valign="top">
                    <div align="center" style="width:450px" id="div_summary"><input type="button" value="Print Preview" class="formbutton" onClick="new_window2('company_div','summary_full')" /></div>
                    <br /><? $po_ids=implode(',',$po_ids_array); ?>
                    <div id="summary_full"> <font color="#FF0000" style="display:none">*Yarn Dyeing Charge included with actual Yarn Cost</font>
                        <div align="center" id="company_div" style="visibility:hidden; font-size:24px;width:700px"><b><? echo $company_arr[$company_name].'<br>'; 
						echo change_date_format(str_replace("'","",$txt_date_from)) .' To '. change_date_format(str_replace("'","",$txt_date_to));
						?></b></div>
                        <table width="760" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
                            <thead>
                            <tr>
                                <th colspan="7">Summary</th>
                            </tr>
                            <tr>
                                <th width="30">SL</th>
                                <th width="180">Particulars</th>
                                <th width="130">Pre Costing</th>
                                <th width="80">%</th>
                                <th width="130">At Actual</th>
                                <th width="80">%</th>
                                <th>Variance</th>
                            </tr> 
							<?
								$bgcolor1='#E9F3FF';
								$bgcolor2='#FFFFFF';
								//echo $tot_ex_factory_val.'TT';
							?>
                            </thead>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_1','<? echo $bgcolor1; ?>')" id="trtd_1">
                                <td align="center">1</td>
                                <td>PO/Shipment Value</td>
                                <td align="right"><? echo number_format($tot_po_value,2); ?></td>
                                <td align="right">&nbsp;</td>
                                <td align="right"><? echo number_format($tot_ex_factory_val,2); ?></td>
                                <td align="right">&nbsp;</td>
                                <td align="right"><? echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <!-- <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_2','<? echo $bgcolor2; ?>')" id="trtd_2">
                                <td colspan="7"><b>Cost</b></td>
                            </tr> -->
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_3','<? echo $bgcolor1; ?>')" id="trtd_3">
                                <td align="center">2</td>
                                <td>Fabric Purchase Cost</td>
                                <td align="right"><? echo number_format($tot_fabric_purchase_cost_mkt,2); ?></td>
                                <td align="right"><? if($tot_po_value>0 && $tot_fabric_purchase_cost_mkt>0)  echo number_format(($tot_fabric_purchase_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><a href="#report_details" title="Exchange rate=80" onClick="openmypage('<? echo $po_ids; ?>','fabric_purchase_cost_actual','Fabric Purchase Cost Details')"><? echo number_format($tot_fabric_purchase_cost_actual,2); ?></a></td>
                                <td align="right"><? if($tot_ex_factory_val>0) echo number_format(($tot_fabric_purchase_cost_actual/$tot_ex_factory_val)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_fabric_purchase_cost_mkt-$tot_fabric_purchase_cost_actual,2); ?></td>
                            </tr>
                            <!-- <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_4','<? echo $bgcolor2; ?>')" id="trtd_4">
                                <td align="center">3</td>
                                <td>Yarn Cost+Yarn Dyeing Cost</td>
                                <td align="right"><a href="#report_details" onClick="openmypage_mkt('<? echo $tot_yarn_cost_mkt."**".$tot_yarn_dye_cost_mkt; ?>','mkt_yarn_cost','Grey And Dyed Yarn Mkt. Cost Details')"><? echo number_format($tot_yarn_cost_mkt+$tot_yarn_dye_cost_mkt,2); ?></a></td>
                                <td align="right"><? echo number_format((($tot_yarn_cost_mkt+$tot_yarn_dye_cost_mkt)/$tot_po_value)*100,2); ?></td>
                                <td align="right"><a href="#report_details" onClick="openmypage('<? echo $po_ids; ?>','yarn_cost','Yarn Cost Details')"><? echo number_format($tot_yarn_cost_actual,2); ?></a></td>
                                <td align="right"><? echo number_format(($tot_yarn_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format(($tot_yarn_cost_mkt+$tot_yarn_dye_cost_mkt)-$tot_yarn_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_5','<? echo $bgcolor2; ?>')" id="trtd_5">
                                <td align="center">4</td>
                                <td>Knitting Cost</td>
                                <td align="right"><? echo number_format($tot_knit_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_knit_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><a href="#report_details" onClick="openmypage('<? echo $po_ids; ?>','knitting_cost','Knitting Cost Details')"><? echo number_format($tot_knit_cost_actual,2); ?></a></td>
                                <td align="right"><? echo number_format(($tot_knit_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_knit_cost_mkt-$tot_knit_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>"  onclick="change_color('trtd_6','<? echo $bgcolor2; ?>')" id="trtd_6">
                                <td align="center">5</td>
                                <td>Dye & Fin Cost</td>
                                <td align="right"><? echo number_format($tot_dye_finish_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_dye_finish_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><a href="#report_details" onClick="openmypage('<? echo $po_ids; ?>','dye_fin_cost','Dye & Fin Cost Details')"><? echo number_format($tot_dye_finish_cost_actual,2); ?></a></td>
                                <td align="right"><? echo number_format(($tot_dye_finish_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_dye_finish_cost_mkt-$tot_dye_finish_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_7','<? echo $bgcolor1; ?>')" id="trtd_7">
                                <td align="center">6</td>
                                <td>AOP & Others Cost</td>
                                <td align="right"><? echo number_format($tot_aop_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_aop_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_aop_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_aop_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_aop_cost_mkt-$tot_aop_cost_actual,2); ?></td>
                            </tr> -->
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_8','<? echo $bgcolor2; ?>')" id="trtd_8">
                                <td align="center">3</td>
                                <td>Trims Cost</td>
                                <td align="right"><? echo number_format($tot_trims_cost_mkt,2); ?></td>
                                <td align="right"><? if($tot_po_value>0 && $tot_trims_cost_mkt>0) echo number_format(($tot_trims_cost_mkt/$tot_po_value)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_trims_cost_actual,2); ?></td>
                                <td align="right"><? if($tot_ex_factory_val>0) echo number_format(($tot_trims_cost_actual/$tot_ex_factory_val)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_trims_cost_mkt-$tot_trims_cost_actual,2); ?></td>
                            </tr> 
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_9','<? echo $bgcolor1; ?>')" id="trtd_9">
                                <td align="center">4</td>
                                <td>Embellishment Cost</td>
                                <td align="right"><? echo number_format($tot_embell_cost_mkt,2); ?></td>
                                <td align="right"><? if($tot_embell_cost_mkt>0 && $tot_po_value>0) echo number_format(($tot_embell_cost_mkt/$tot_po_value)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_embell_cost_actual,2); ?></td>
                                <td align="right"><? if($tot_ex_factory_val>0) echo number_format(($tot_embell_cost_actual/$tot_ex_factory_val)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_embell_cost_mkt-$tot_embell_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_10','<? echo $bgcolor2; ?>')" id="trtd_10">
                                <td align="center">5</td>
                                <td>Wash Cost</td>
                                <td align="right"><? echo number_format($tot_wash_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_wash_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_wash_cost_actual,2); ?></td>
                                <td align="right"><? if($tot_ex_factory_val>0) echo number_format(($tot_wash_cost_actual/$tot_ex_factory_val)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_wash_cost_mkt-$tot_wash_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_11','<? echo $bgcolor1; ?>')" id="trtd_11">
                                <td align="center">6</td>
                                <td>Commission Cost</td>
                                <td align="right"><? echo number_format($tot_commission_cost_mkt,2); ?></td>
                                <td align="right"><? if($tot_po_value>0 && $tot_commission_cost_mkt>0) echo number_format(($tot_commission_cost_mkt/$tot_po_value)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_commission_cost_actual,2); ?></td>
                                <td align="right"><?  if($tot_ex_factory_val>0) echo number_format(($tot_commission_cost_actual/$tot_ex_factory_val)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_commission_cost_mkt-$tot_commission_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_12','<? echo $bgcolor2; ?>')" id="trtd_12">
                                <td align="center">7</td>
                                <td>Commercial Cost</td>
                                <td align="right"><? echo number_format($tot_comm_cost_mkt,2); ?></td>
                                <td align="right"><? if($tot_comm_cost_mkt>0 && $tot_po_value>0) echo number_format(($tot_comm_cost_mkt/$tot_po_value)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_comm_cost_actual,2); ?></td>
                                <td align="right"><?  if($tot_ex_factory_val>0) echo number_format(($tot_comm_cost_actual/$tot_ex_factory_val)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_comm_cost_mkt-$tot_comm_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_13','<? echo $bgcolor1; ?>')" id="trtd_13">
                                <td align="center">8</td>
                                <td>Freight Cost</td>
                                <td align="right"><? echo number_format($tot_freight_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_freight_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_freight_cost_actual,2); ?></td>
                                <td align="right"><?  if($tot_ex_factory_val>0) echo number_format(($tot_freight_cost_actual/$tot_ex_factory_val)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_freight_cost_mkt-$tot_freight_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>"  onclick="change_color('trtd_14','<? echo $bgcolor2; ?>')" id="trtd_14">
                                <td align="center">9</td>
                                <td>Testing Cost</td>
                                <td align="right"><? echo number_format($tot_test_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_test_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_test_cost_actual,2); ?></td>
                                <td align="right"><?  if($tot_ex_factory_val>0) echo number_format(($tot_test_cost_actual/$tot_ex_factory_val)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_test_cost_mkt-$tot_test_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_15','<? echo $bgcolor1; ?>')" id="trtd_15">
                                <td align="center">10</td>
                                <td>Inspection Cost</td>
                                <td align="right"><? echo number_format($tot_inspection_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_inspection_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_inspection_cost_actual,2); ?></td>
                                <td align="right"><?  if($tot_ex_factory_val>0) echo number_format(($tot_inspection_cost_actual/$tot_ex_factory_val)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_inspection_cost_mkt-$tot_inspection_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_16','<? echo $bgcolor2; ?>')" id="trtd_16">
                                <td align="center">11</td>
                                <td>Courier Cost</td>
                                <td align="right"><? echo number_format($tot_currier_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_currier_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_currier_cost_actual,2); ?></td>
                                <td align="right"><?  if($tot_ex_factory_val>0) echo number_format(($tot_currier_cost_actual/$tot_ex_factory_val)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_currier_cost_mkt-$tot_currier_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_17','<? echo $bgcolor1; ?>')" id="trtd_17">
                                <td align="center">12</td>
                                <td>CM</td>
                                <td align="right"><? echo number_format($tot_cm_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_cm_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_cm_cost_actual,2); ?></td>
                                <td align="right"><?  if($tot_ex_factory_val>0) echo number_format(($tot_cm_cost_actual/$tot_ex_factory_val)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_cm_cost_mkt-$tot_cm_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_18','<? echo $bgcolor2; ?>')" id="trtd_18">
                                <td align="center">13</td>
                                <td>Total Cost</td>
                                <td align="right"><? echo number_format($tot_mkt_all_cost,2); ?></td>
                                <td align="right"><? if($tot_mkt_all_cost>0 && $tot_po_value>0) echo number_format(($tot_mkt_all_cost/$tot_po_value)*100,2); else echo "";?></td>
                                <td align="right"><? echo number_format($tot_actual_all_cost,2); ?></td>
                                <td align="right"><?  if($tot_ex_factory_val>0) echo number_format(($tot_actual_all_cost/$tot_ex_factory_val)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_mkt_all_cost-$tot_actual_all_cost,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_19','<? echo $bgcolor1; ?>')" id="trtd_19">
                                <td align="center">14</td>
                                <td>Margin/Loss</td>
                                <td align="right"><? echo number_format($tot_mkt_margin,2); ?></td>
                                <td align="right"><? echo number_format(($tot_mkt_margin/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_actual_margin,2); ?></td>
                                <td align="right"><?  if($tot_ex_factory_val>0) echo number_format(($tot_actual_margin/$tot_ex_factory_val)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_mkt_margin-$tot_actual_margin,2); ?></td>
                            </tr>
                        </table>
                    </div>
                </td>
                <td width="525" align="center" valign="top">
                    <div align="center" style="width:500px; height:53px" id="graph">&nbsp;</div>
                    <fieldset style="text-align:center; width:450px" > 
                    	<legend>Chart</legend>
                    </fieldset>
                </td>   
                <td width="" valign="top">
                    <div align="center" style="width:600px" id="div_buyer"><input type="button" value="Print Preview" class="formbutton" onClick="new_window2('company_div_b','summary_buyer')" /></div>
                    <br />
                    <div id="summary_buyer">
                        <div align="center" id="company_div_b" style="visibility:hidden; font-size:24px;width:1105px"><b><? echo $company_arr[$company_name].'<br>';echo change_date_format(str_replace("'","",$txt_date_from)) .' To '. change_date_format(str_replace("'","",$txt_date_to)); ?></b></div>
                        <table width="100%" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
                            <thead>
                                 <tr>
                                    <th colspan="22">Buyer Level Summary</th>
                                </tr>
                                <tr>
                                    <th width="35">SL</th>
                                    <th width="70">Buyer name</th>
                                    <th width="110">Cost Source</th>
                                    <th width="110">Ex-Factory Value</th>
                                    <th width="110">Fabric Purchase Cost</th>
                                    <th width="110">Trims Cost</th>
                                    <th width="110">Embellishment Cost</th>
                                    <th width="110">Wash Cost</th>
                                    <th width="110">Commission Cost</th>
                                    <th width="100">Commercial Cost</th>
                                    <th width="110">Freight Cost</th>
                                    <th width="100">Testing Cost</th>
                                    <th width="100">Inspection Cost</th>
                                    <th width="100">Courier Cost</th>
                                    <th width="110">CM Cost</th>
                                    <th width="110">Toal Cost</th>
                                    <th width="110">Margin</th>
                                    <th width="90">Margin %</th>
                                </tr>
                            </thead>
                            <?
                                $j=1;
                                foreach($buyer_name_array as $key=>$value)
                                {
                                    if ($j%2==0)  
                                        $bgcolor="#E9F3FF";
                                    else
                                        $bgcolor="#FFFFFF";	
                                ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trTD_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trTD_<? echo $j; ?>">
                                        <td rowspan="4"><? echo $j ;?></td>
                                        <td rowspan="4"><? echo $value ;?></td>
                                        <td><b>Pre Costing</b></td>
                                        <td align="right"><? echo number_format($mkt_po_val_array[$key],2); $tot_mkt_po_val+=$mkt_po_val_array[$key];  ?></td>
                                        <td align="right"><? echo number_format($mkt_fabric_purchase_array[$key],2); $tot_mkt_fin_pur_cost+=$mkt_fabric_purchase_array[$key];?></td>
                                        <td align="right"><? echo number_format($mkt_trims_array[$key],2); $tot_mkt_trims_cost+=$mkt_trims_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_emb_array[$key],2); $tot_mkt_emb_cost+=$mkt_emb_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_wash_array[$key],2); $tot_mkt_wash_cost+=$mkt_wash_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_commn_array[$key],2); $tot_mkt_commn_cost+=$mkt_commn_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_commercial_array[$key],2); $tot_mkt_commercial_cost+=$mkt_commercial_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_freight_array[$key],2); $tot_mkt_freight_cost+=$mkt_freight_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_test_array[$key],2); $tot_mkt_test_cost+=$mkt_test_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_ins_array[$key],2); $tot_mkt_ins_cost+=$mkt_ins_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_courier_array[$key],2); $tot_mkt_courier_cost+=$mkt_courier_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_cm_array[$key],2); $tot_mkt_cm_cost+=$mkt_cm_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_total_array[$key],2); $tot_mkt_total_cost+=$mkt_total_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_margin_array[$key],2); $tot_mkt_margin_cost+=$mkt_margin_array[$key]; ?></td>
                                        <td align="right"><? if($mkt_po_val_array[$key]>0 && $mkt_margin_array[$key]>0) $mkt_margin_perc=($mkt_margin_array[$key]/$mkt_po_val_array[$key])*100;else  $mkt_margin_perc=""; echo number_format($mkt_margin_perc,2); $tot_mkt_margin_perc_cost+=$mkt_margin_perc; ?></td> 
                                    </tr>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trTD2_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trTD2_<? echo $j; ?>">
                                        <td><b>Actual</b></td>
                                        <td align="right"><? echo number_format($ex_factory_val_array[$key],2); $tot_buyer_ex_factory_val+=$ex_factory_val_array[$key];  ?></td>                                        
                                        <td align="right"><? echo number_format($actual_fabric_purchase_array[$key],2); $tot_buyer_fin_pur_act_cost+=$actual_fabric_purchase_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($trims_cost_array[$key],2); $tot_buyer_trims_cost+=$trims_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($enbellishment_cost_array[$key],2); $tot_buyer_embell_cost+=$enbellishment_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($wash_cost_array[$key],2); $tot_buyer_wash_cost+=$wash_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($commission_cost_array[$key],2); $tot_buyer_commi_cost+=$commission_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($commercial_cost_array[$key],2); $tot_buyer_commercial_cost+=$commercial_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($freight_cost_array[$key],2); $tot_buyer_freight_cost+=$freight_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($testing_cost_array[$key],2); $tot_buyer_testing_cost+=$testing_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($inspection_cost_array[$key],2); $tot_buyer_inspection_cost+=$inspection_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($courier_cost_array[$key],2); $tot_buyer_courier_cost+=$courier_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($cm_cost_array[$key],2); $tot_buyer_cm_cost+=$cm_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($total_cost_array[$key],2); $tot_buyer_total_cost+=$total_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($margin_array[$key],2); $tot_buyer_margin_cost+=$margin_array[$key]; ?></td>
                                        <td align="right"><? if($ex_factory_val_array[$key]>0 && $margin_array[$key]>0) $margin_perc=($margin_array[$key]/$ex_factory_val_array[$key])*100;else  $margin_perc=""; echo number_format($margin_perc,2); $tot_buyer_margin_perc_cost+=$margin_perc; ?></td> 
                                    </tr>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trTD3_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trTD3_<? echo $j; ?>">
                                        <td><b>Variance</b></td>
                                        <td align="right">
                                        <?
                                            $ex_var= $mkt_po_val_array[$key]-$ex_factory_val_array[$key];
                                            echo number_format($ex_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $fin_pur_var= $mkt_fabric_purchase_array[$key]-$actual_fabric_purchase_array[$key];
                                            echo number_format($fin_pur_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $trims_var= $mkt_trims_array[$key]-$trims_cost_array[$key];
                                            echo number_format($trims_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $emb_var= $mkt_emb_array[$key]-$enbellishment_cost_array[$key];
                                            echo number_format($emb_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $wash_var= $mkt_wash_array[$key]-$wash_cost_array[$key];
                                            echo number_format($wash_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $com_var= $mkt_commn_array[$key]-$commission_cost_array[$key];
                                            echo number_format($com_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $commer_var= $mkt_commercial_array[$key]-$commercial_cost_array[$key];
                                            echo number_format($commer_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $fr_var= $mkt_freight_array[$key]-$freight_cost_array[$key];
                                            echo number_format($fr_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $test_var= $mkt_test_array[$key]-$testing_cost_array[$key];
                                            echo number_format($test_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $ins_var= $mkt_ins_array[$key]-$inspection_cost_array[$key];
                                            echo number_format($ins_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $cour_var= $mkt_courier_array[$key]-$courier_cost_array[$key];
                                            echo number_format($cour_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $cm_var= $mkt_cm_array[$key]-$cm_cost_array[$key];
                                            echo number_format($cm_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $tot_var= $mkt_total_array[$key]-$total_cost_array[$key];
                                            echo number_format($tot_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $mar_var= $mkt_margin_array[$key]-$margin_array[$key];
                                            echo number_format($mar_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $margin_perc_to= $mkt_margin_perc-$margin_perc;
                                            echo number_format($margin_perc_to,2); 
                                        ?>
                                        </td>
                                    </tr>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trTD4_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trTD4_<? echo $j; ?>">
                                        <td><b>Variance (%)</b></td>
                                        <td align="right"><? if($ex_var>0 && $mkt_po_val_array[$key]>0) echo number_format(($ex_var/$mkt_po_val_array[$key]*100),2);else echo ""; ?></td>
                                        <td align="right"><? if($fin_pur_var>0 && $mkt_fabric_purchase_array[$key]>0) echo number_format(($fin_pur_var/$mkt_fabric_purchase_array[$key]*100),2);else echo ""; ?></td>
                                        <td align="right"><? if($trims_var>0 && $mkt_trims_array[$key]>0) echo number_format(($trims_var/$mkt_trims_array[$key]*100),2);else echo ""; ?></td>
                                        <td align="right"><? if($emb_var>0 && $mkt_emb_array[$key]>0) echo number_format(($emb_var/$mkt_emb_array[$key]*100),2);else echo ""; ?></td>
                                        <td align="right"><? if($wash_var>0 && $mkt_wash_array[$key]>0) echo number_format(($wash_var/$mkt_wash_array[$key]*100),2);else echo ""; ?></td>
                                        <td align="right"><? if($com_var>0 && $mkt_commn_array[$key]>0) echo number_format(($com_var/$mkt_commn_array[$key]*100),2);else echo ""; ?></td>
                                        <td align="right"><? if($commer_var>0 && $mkt_commercial_array[$key]>0) echo number_format(($commer_var/$mkt_commercial_array[$key]*100),2);else echo ""; ?></td>
                                        <td align="right"><? if($fr_var>0 && $mkt_freight_array[$key]>0) echo number_format(($fr_var/$mkt_freight_array[$key]*100),2);else echo ""; ?></td>
                                        <td align="right"><? if($test_var>0 && $mkt_test_array[$key]>0) echo number_format(($test_var/$mkt_test_array[$key]*100),2);else echo ""; ?></td>
                                        <td align="right"><? if($ins_var>0 && $mkt_ins_array[$key]>0) echo number_format(($ins_var/$mkt_ins_array[$key]*100),2);else echo ""; ?></td>
                                        <td align="right"><? if($cour_var>0 && $mkt_courier_array[$key]>0) echo number_format(($cour_var/$mkt_courier_array[$key]*100),2); ?></td>
                                        <td align="right"><? if($cm_var>0 && $mkt_cm_array[$key]>0) echo number_format(($cm_var/$mkt_cm_array[$key]*100),2);else echo ""; ?></td>
                                        <td align="right"><? if($tot_var>0 && $mkt_total_array[$key]>0) echo number_format(($tot_var/$mkt_total_array[$key]*100),2);else echo ""; ?></td>
                                        <td align="right"><? if($margin_perc_to>0 && $mkt_margin_array[$key]>0) echo number_format(($mar_var/$mkt_margin_array[$key]*100),2);else echo ""; ?></td>
                                        <td align="right"><? if($margin_perc_to>0 && $mkt_margin_perc>0) echo number_format(($margin_perc_to/$mkt_margin_perc*100),2);else echo ""; ?></td> 
                                    </tr>
                           	<?
                                $j++;
                                }
								
								$bgcolor5='#CCDDEE';
								$bgcolor6='#CCCCFF';
								$bgcolor7='#FFEEFF';
                            ?>
                            <tr bgcolor="<? echo $bgcolor5; ?>" onClick="change_color('trTD5_<? echo $j; ?>','<? echo $bgcolor5; ?>')" id="trTD5_<? echo $j; ?>">
                                <th colspan="3">Pre Costing</th>
                                <th align="right"><? echo number_format($tot_mkt_po_val,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_fin_pur_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_trims_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_emb_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_wash_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_commn_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_commercial_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_freight_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_test_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_ins_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_courier_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_cm_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_total_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_margin_cost,2); ?></th>
                                <th align="right"><? if($tot_mkt_margin_cost>0 && $tot_mkt_po_val>0) $mm=$tot_mkt_margin_cost/$tot_mkt_po_val*100;else $mm=""; echo number_format($mm,2) ?></th>   
                            </tr>
                            <tr bgcolor="<? echo $bgcolor6; ?>" onClick="change_color('trTD6_<? echo $j; ?>','<? echo $bgcolor6; ?>')" id="trTD6_<? echo $j; ?>">
                                <th colspan="3">Actual</th>
                                <th align="right"><? echo number_format($tot_buyer_ex_factory_val,2); ?></th>                                
                                <th align="right"><? echo number_format($tot_buyer_fin_pur_act_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_trims_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_embell_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_wash_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_commi_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_commercial_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_freight_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_testing_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_inspection_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_courier_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_cm_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_total_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_margin_cost,2); ?></th>
                                <th align="right"><? if($tot_buyer_margin_cost>0 && $tot_buyer_ex_factory_val>0) $pp=$tot_buyer_margin_cost/$tot_buyer_ex_factory_val*100;else $pp=""; echo number_format($pp,2); ?></th>   
                            </tr>
                            <tr bgcolor="<? echo $bgcolor7; ?>" onClick="change_color('trTD7_<? echo $j; ?>','<? echo $bgcolor7; ?>')" id="trTD7_<? echo $j; ?>">
                                <th colspan="3">Variance</th>
                                <th align="right"><? $evar=$tot_mkt_po_val-$tot_buyer_ex_factory_val;  echo number_format($evar,2); ?></th>
                                <th align="right"><? $fpfvar=$tot_mkt_fin_pur_cost-$tot_buyer_fin_pur_act_cost;  echo number_format($fpfvar,2); ?></th>
                                <th align="right"><? $trimvar=$tot_mkt_trims_cost-$tot_buyer_trims_cost;  echo number_format($trimvar,2); ?></th>
                                <th align="right"><? $embvar=$tot_mkt_emb_cost-$tot_buyer_embell_cost;  echo number_format($embvar,2); ?></th>
                                <th align="right"><? $washvar=$tot_mkt_wash_cost-$tot_buyer_wash_cost;  echo number_format($washvar,2); ?></th>
                                <th align="right"><? $comvar=$tot_mkt_commn_cost-$tot_buyer_commi_cost;  echo number_format($comvar,2); ?></th>
                                <th align="right"><? $commercialvar=$tot_mkt_commercial_cost-$tot_buyer_commercial_cost;  echo number_format($commercialvar,2); ?></th>
                                <th align="right"><? $fvar=$tot_mkt_freight_cost-$tot_buyer_freight_cost;  echo number_format($fvar,2); ?></th>
                                <th align="right"><? $tvar=$tot_mkt_test_cost-$tot_buyer_testing_cost;  echo number_format($tvar,2); ?></th>
                                <th align="right"><? $ivar=$tot_mkt_ins_cost-$tot_buyer_inspection_cost;  echo number_format($ivar,2); ?></th>
                                <th align="right"><? $courvar=$tot_mkt_courier_cost-$tot_buyer_courier_cost;  echo number_format($courvar,2); ?></th>
                                <th align="right"><? $cmvar=$tot_mkt_cm_cost-$tot_buyer_cm_cost;  echo number_format($cmvar,2); ?></th>
                                <th align="right"><? $totvar=$tot_mkt_total_cost-$tot_buyer_total_cost;  echo number_format($totvar,2); ?></th>
                                <th align="right"><? $mvar=$tot_mkt_margin_cost-$tot_buyer_margin_cost;  echo number_format($mvar,2); ?></th>
                                <th align="right"><?  $mpvar=$mm-$pp;  echo number_format($mpvar,2); ?></th>   
                            </tr>
                            <tr bgcolor="<? echo $bgcolor6; ?>" onClick="change_color('trTD8_<? echo $j; ?>','<? echo $bgcolor6; ?>')" id="trTD8_<? echo $j; ?>">
                                <th colspan="3">Variance (%)</th>
                                <th align="right"><? if($evar>0 && $tot_mkt_po_val>0) echo number_format(($evar/$tot_mkt_po_val*100),2);else echo ""; ?></th>                                
                                <th align="right"><? if($fpfvar>0 && $tot_mkt_fin_pur_cost>0) echo number_format(($fpfvar/$tot_mkt_fin_pur_cost*100),2);else echo ""; ?></th>
                                <th align="right"><? if($trimvar>0 && $tot_mkt_trims_cost>0) echo number_format(($trimvar/$tot_mkt_trims_cost*100),2);else echo ""; ?></th>
                                <th align="right"><? if($embvar>0 && $tot_mkt_emb_cost>0) echo number_format(($embvar/$tot_mkt_emb_cost*100),2);else echo ""; ?></th>
                                <th align="right"><? if($washvar>0 && $tot_mkt_wash_cost>0) echo number_format(($washvar/$tot_mkt_wash_cost*100),2);else echo ""; ?></th>
                                <th align="right"><? if($comvar>0 && $tot_mkt_commn_cost>0) echo number_format(($comvar/$tot_mkt_commn_cost*100),2);else echo ""; ?></th>
                                <th align="right"><? if($commercialvar>0 && $tot_mkt_commercial_cost>0) echo number_format(($commercialvar/$tot_mkt_commercial_cost*100),2);else echo ""; ?></th>
                                <th align="right"><? if($fvar>0 && $tot_mkt_freight_cost>0) echo number_format(($fvar/$tot_mkt_freight_cost*100),2);else echo ""; ?></th>
                                <th align="right"><? if($tvar>0 && $tot_mkt_test_cost>0) echo number_format(($tvar/$tot_mkt_test_cost*100),2);else echo ""; ?></th>
                                <th align="right"><? if($ivar>0 && $tot_mkt_ins_cost>0)echo number_format(($ivar/$tot_mkt_ins_cost*100),2);else echo ""; ?></th>
                                <th align="right"><? if($courvar>0 && $tot_mkt_courier_cost>0) echo number_format(($courvar/$tot_mkt_courier_cost*100),2);else echo ""; ?></th>
                                <th align="right"><? if($cmvar>0 && $tot_mkt_cm_cost>0) echo number_format(($cmvar/$tot_mkt_cm_cost*100),2); else echo "";?></th>
                                <th align="right"><? if($totvar>0 && $tot_mkt_total_cost>0) echo number_format(($totvar/$tot_mkt_total_cost*100),2);else echo ""; ?></th>
                                <th align="right"><? if($mvar>0 && $tot_mkt_margin_cost>0) echo number_format(($mvar/$tot_mkt_margin_cost*100),2);else echo ""; ?></th>
                                <th align="right"><? if($mpvar>0 && $mm>0) echo number_format(($mpvar/$mm*100),2);else echo ""; ?></th>   
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
	</fieldset>
 <?
	/*foreach (glob("../../../../ext_resource/tmp_report/$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$html=ob_get_contents();
	$name=time();
	$filename="../../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	$filename="../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";*/
	$html = ob_get_contents();
		ob_clean();
		//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
		foreach (glob("*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_name."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');	
		$is_created = fwrite($create_new_doc, $html);
		echo "$html****$filename****$report_type"; 
	//echo "$total_data****$filename";
	exit();
}
if($action=="report_generate2")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$brand_library=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name");
	$season_library=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");
	

	$company_name=str_replace("'","",$cbo_company_name);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$cbo_status=str_replace("'","",$cbo_status);
	$cbo_brand_id=str_replace("'","",$cbo_brand_id);
	$cbo_season_year=str_replace("'","",$cbo_season_year);
	$txt_season_id=str_replace("'","",$txt_season_id);
			
	//*cbo_brand_id*cbo_season_year*txt_season_id
	if($txt_ref_no!='') $ref_cond="and b.grouping='$txt_ref_no'";else $ref_cond="";
	if($txt_file_no!='') $file_cond="and b.file_no=$txt_file_no";else $file_cond="";
	if($cbo_brand_id>0) $brand_cond="and a.brand_id in($cbo_brand_id)";else  $brand_cond="";
	//if($txt_season_id>0) $season_cond="and a.season in($txt_season_id)";else  $season_cond="";
	if($txt_season_id>0) $season_cond="and a.season_buyer_wise in($txt_season_id)";else  $season_cond="";
	//echo $season_cond.'DDDD';;
	if($cbo_season_year>0) $season_year_cond="and a.season_year=$cbo_season_year";else  $season_year_cond="";
	
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
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if(trim($txt_job_no)!="") $job_no=trim($txt_job_no); else $job_no="%%";
	
	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		else $year_cond="";
	}
	else $year_cond="";
	
	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$date_cond=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
	}
	
	$po_id_cond="";
	if(trim(str_replace("'","",$txt_order_no))!="")
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id_cond=" and b.id in(".str_replace("'","",$hide_order_id).")";
		}
		else
		{
			$po_id_cond=" and LOWER(b.po_number) like LOWER('%".trim(str_replace("'","",$txt_order_no))."%')";
		}
	}
	
	$shipping_status_cond='';
	if(str_replace("'","",$shipping_status)!=0)
	{
		$shipping_status_cond=" and b.shiping_status=$shipping_status";
	}
	$cbo_status_cond='';
	if($cbo_status!=0)
	{
		$cbo_status_cond=" and b.is_confirmed in($cbo_status)";
	}
	
	$exchange_rate=76; $po_ids_array=array();
	
	
		
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
		
	$sql="select a.job_no_prefix_num, a.job_no, $year_field, a.company_name,a.season_buyer_wise as season,a.brand_id,a.season_year, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.set_smv, b.id, b.po_number,b.grouping,b.file_no, b.pub_shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.job_no=b.job_no_mst and c.garments_nature=3 and a.company_name='$company_name' and a.job_no_prefix_num like '$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=$cbo_order_status and b.is_deleted=0 $date_cond $buyer_id_cond $po_id_cond $ref_cond $file_cond $shipping_status_cond $year_cond $cbo_status_cond $brand_cond $season_cond $season_year_cond order by b.pub_shipment_date, a.job_no_prefix_num, b.id";
	//echo $sql; //die;
	$result=sql_select($sql);
	$all_po_id="";  $all_jobs='';
	foreach($result as $row)
	{
		if($all_po_id=="") $all_po_id=$row[csf("id")]; else $all_po_id.=",".$row[csf("id")];
		if($all_jobs=="") $all_jobs="'".$row[csf("job_no")]."'"; else $all_jobs.=",'".$row[csf("job_no")]."'";
	}
	$all_jobs=implode(",",array_unique(explode(",",$all_jobs)));
	
	$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2="";$po_cond_for_in3=""; $po_cond_for_in4=""; $po_cond_for_in5="";$po_cond_for_in6="";  
	$po_ids=count(array_unique(explode(",",$all_po_id)));
		if($db_type==2 && $po_ids>1000)
		{
			$po_cond_for_in=" and (";
			$po_cond_for_in2=" and (";
			$po_cond_for_in3=" and (";
			$po_cond_for_in4=" and (";
			$po_cond_for_in6=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$po_cond_for_in.=" b.po_break_down_id in($ids) or"; 
				$po_cond_for_in2.=" po_id in($ids) or"; 
				$po_cond_for_in3.=" b.order_id in($ids) or";
				$po_cond_for_in4.=" b.po_breakdown_id in($ids) or"; 
				$po_cond_for_in5.=" c.po_break_down_id in($ids) or"; 
				$po_cond_for_in6.=" b.po_break_down_id in($ids) or"; 
			}
			$po_cond_for_in=chop($po_cond_for_in,'or ');
			$po_cond_for_in.=")";
			
			$po_cond_for_in2=chop($po_cond_for_in2,'or ');
			$po_cond_for_in2.=")";
			$po_cond_for_in3=chop($po_cond_for_in3,'or ');
			$po_cond_for_in3.=")";
			$po_cond_for_in4=chop($po_cond_for_in4,'or ');
			$po_cond_for_in4.=")";
			$po_cond_for_in5=chop($po_cond_for_in5,'or ');
			$po_cond_for_in5.=")";
			$po_cond_for_in6=chop($po_cond_for_in6,'or ');
			$po_cond_for_in6.=")";
		}
		else
		{
			$poIds=implode(",",array_unique(explode(",",$all_po_id)));
			
			if($poIds==""){$po_cond_for_in="";}else{$po_cond_for_in=" and b.po_break_down_id in($poIds)";}
			if($poIds==""){$po_cond_for_in2="";}else{$po_cond_for_in2=" and po_id in($poIds)";}
			if($poIds==""){$po_cond_for_in3="";}else{$po_cond_for_in3=" and b.order_id in($poIds)";}
			if($poIds==""){$po_cond_for_in4="";}else{$po_cond_for_in4=" and b.po_breakdown_id in($poIds)";}
			if($poIds==""){$po_cond_for_in5="";}else{$po_cond_for_in5=" and c.po_break_down_id in($poIds)";}
			if($poIds==""){$po_cond_for_in6="";}else{$po_cond_for_in6=" and b.po_break_down_id in($poIds)";}
			
			/*$po_cond_for_in=" and b.po_break_down_id in($poIds)";
			$po_cond_for_in2=" and po_id in($poIds)";
			$po_cond_for_in3=" and b.order_id in($poIds)";
			$po_cond_for_in4=" and b.po_breakdown_id in($poIds)";*/
		}
		
		$ex_factory_arr=return_library_array( "select b.po_break_down_id, 
	sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as qnty 
	from pro_ex_factory_mst b where b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_break_down_id", "po_break_down_id", "qnty");	
	
	
	ob_start();
	?>
    <fieldset>
    	<table width="2830">
            <tr class="form_caption">
                <td colspan="26" align="center"><strong>Post Costing Report</strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="26" align="center"><strong><? echo $company_arr[$company_name];?></strong>
                    <br>
                    <strong><? echo change_date_format(str_replace("'","",$txt_date_from)) .' To '. change_date_format(str_replace("'","",$txt_date_to)); ?></strong>
                </td>
            </tr>
        </table>
        <table style="margin-top:10px" id="table_header_1" class="rpt_table" width="3840" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="70">Buyer</th>
                <th width="70">Brand</th>
                
                <th width="60">Job Year</th>
                <th width="70">Job No</th>
                <th width="100">PO No</th>
                <th width="70">File No</th>
                <th width="80">Ref. No</th>
                <th width="110">Style Name</th>
                <th width="70">Season</th>
                <th width="70">Season year</th>
                    
                <th width="120">Garments Item</th>
                <th width="90">PO Quantity</th>
                <th width="50">UOM</th>
                <th width="70">Unit Price</th>
                <th width="110">PO Value</th>
                <th width="100">SMV</th>
                <th width="80">Shipment Date</th>
                <th width="100">Shipping Status</th>
                <th width="100">Cost Source</th>
                <th width="100">PO/Ex-Factory Qnty</th>
                <th width="110">PO/Ex-Factory Value</th>
                <th width="110">Fabric Purchase Cost</th>
                <th width="110">Trims Cost</th>
                <th width="100">Embellishment Cost</th>
                <th width="100">Wash Cost</th>
                <th width="100">Commission Cost</th>
                <th width="100">Commercial Cost</th>
                <th width="100">Freight Cost</th>
                <th width="100">Testing Cost</th>
                <th width="100">Inspection Cost</th>
                <th width="100">Courier Cost</th>
                <th width="100">Tot Mat. <br> Serv. Cost </th>
  				<th width="100">CM Value<br>(Contribution)</th>
                <th width="100">CM Cost</th>
             
                <th width="100">Margin</th>
                <th width="100">% to Ex-Factory Value</th>
            </thead>
        </table>
    	<div style="width:3862px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="3840" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
            <?
				$fabriccostArray=array(); $yarncostArray=array(); $trimsCostArray=array(); $prodcostArray=array(); $actualCostArray=array(); $actualTrimsCostArray=array(); 
				$subconCostArray=array(); $embellCostArray=array(); $washCostArray=array(); $aopCostArray=array(); $yarnTrimsCostArray=array(); 
				$yarncostDataArray=sql_select("select job_no, sum(amount) as amnt, sum(rate*avg_cons_qnty) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 group by job_no");
				foreach($yarncostDataArray as $yarnRow)
				{
				   $yarncostArray[$yarnRow[csf('job_no')]]=$yarnRow[csf('amount')];
				}
				
				$fabriccostDataArray=sql_select("select job_no, costing_per_id, embel_cost, wash_cost, cm_cost, commission, currier_pre_cost, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where status_active=1 and is_deleted=0");
				foreach($fabriccostDataArray as $fabRow)
				{
					 $fabriccostArray[$fabRow[csf('job_no')]]['costing_per_id']=$fabRow[csf('costing_per_id')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['embel_cost']=$fabRow[csf('embel_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['wash_cost']=$fabRow[csf('wash_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['cm_cost']=$fabRow[csf('cm_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['commission']=$fabRow[csf('commission')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['currier_pre_cost']=$fabRow[csf('currier_pre_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['lab_test']=$fabRow[csf('lab_test')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['inspection']=$fabRow[csf('inspection')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['freight']=$fabRow[csf('freight')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['comm_cost']=$fabRow[csf('comm_cost')];
				}
				
				$trimscostDataArray=sql_select("select b.po_break_down_id, sum(b.cons*a.rate) as total from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id and a.status_active=1 and a.is_deleted=0 group by b.po_break_down_id");
				foreach($trimscostDataArray as $trimsRow)
				{
					 $trimsCostArray[$trimsRow[csf('po_break_down_id')]]=$trimsRow[csf('total')];
				}
				 
				$prodcostDataArray=sql_select("select job_no, 
									  sum(CASE WHEN cons_process=1 THEN amount END) AS knit_charge,
									  sum(CASE WHEN cons_process=30 THEN amount END) AS yarn_dye_charge,
									  sum(CASE WHEN cons_process=35 THEN amount END) AS aop_charge,
									  sum(CASE WHEN cons_process not in(1,2,30,35) THEN amount END) AS dye_finish_charge
									  from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0 group by job_no");
				foreach($prodcostDataArray as $prodRow)
				{
					$prodcostArray[$prodRow[csf('job_no')]]['knit_charge']=$prodRow[csf('knit_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['yarn_dye_charge']=$prodRow[csf('yarn_dye_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['aop_charge']=$prodRow[csf('aop_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['dye_finish_charge']=$prodRow[csf('dye_finish_charge')];
				}	
				
				$actualCostDataArray=sql_select("select cost_head,po_id,sum(amount_usd) as amount_usd from wo_actual_cost_entry where company_id=$company_name and status_active=1 and is_deleted=0 $po_cond_for_in2 group by cost_head,po_id");
				foreach($actualCostDataArray as $actualRow)
				{
				   $actualCostArray[$actualRow[csf('cost_head')]][$actualRow[csf('po_id')]]=$actualRow[csf('amount_usd')];
				}
				
				$subconInBillDataArray=sql_select("select b.order_id, 
									  sum(CASE WHEN a.process_id=2 THEN b.amount END) AS knit_bill,
									  sum(CASE WHEN a.process_id=4 THEN b.amount END) AS dye_finish_bill
									  from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id in(2,4) $po_cond_for_in3  group by b.order_id");
				foreach($subconInBillDataArray as $subRow)
				{
					$subconCostArray[$subRow[csf('order_id')]]['knit_bill']=$subRow[csf('knit_bill')];
					$subconCostArray[$subRow[csf('order_id')]]['dye_finish_bill']=$subRow[csf('dye_finish_bill')];
				}	
				
				$subconOutBillDataArray=sql_select("select b.order_id, 
									  sum(CASE WHEN a.process_id=2 THEN b.amount END) AS knit_bill,
									  sum(CASE WHEN a.process_id=4 THEN b.amount END) AS dye_finish_bill
									  from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.process_id in(2,4) $po_cond_for_in3 group by b.order_id");
				foreach($subconOutBillDataArray as $subRow)
				{
					$subconCostArray[$subRow[csf('order_id')]]['knit_bill']+=$subRow[csf('knit_bill')];
					$subconCostArray[$subRow[csf('order_id')]]['dye_finish_bill']+=$subRow[csf('dye_finish_bill')];
				}
				
				$embell_type_arr=return_library_array( "select id, emb_name from wo_pre_cost_embe_cost_dtls", "id", "emb_name");	
				
				$bookingDataArray=sql_select("select a.booking_type, a.item_category, a.currency_id, a.exchange_rate, b.po_break_down_id, b.process, b.amount, b.pre_cost_fabric_cost_dtls_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.item_category in(4,12,25) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in");
				foreach($bookingDataArray as $woRow)
				{
					$amount=0; $trimsAmnt=0;
					if($woRow[csf('currency_id')]==1) { $amount=$woRow[csf('amount')]/$exchange_rate; } else { $amount=$woRow[csf('amount')]; }
					
					if($woRow[csf('item_category')]==25 && ($woRow[csf('booking_type')]==3 || $woRow[csf('booking_type')]==6)) 
					{ 
						if($embell_type_arr[$woRow[csf('pre_cost_fabric_cost_dtls_id')]]==3)
						{
							$washCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
						}
						else
						{
							$embellCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
						}
					}
					else if($woRow[csf('item_category')]==12 && $woRow[csf('process')]==35 && ($woRow[csf('booking_type')]==3 || $woRow[csf('booking_type')]==6)) 
					{ 
						$aopCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
					}
					else if($woRow[csf('item_category')]==4)
					{
						if($woRow[csf('currency_id')]==1) { $trimsAmnt=$woRow[csf('amount')]/$woRow[csf('exchange_rate')]; } else { $trimsAmnt=$woRow[csf('amount')]; }
						$actualTrimsCostArray[$woRow[csf('po_break_down_id')]]+=$trimsAmnt; 
					}
				}	
				
				$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master where item_category_id=1", "id", "avg_rate_per_unit"  );
				$receive_array=array();
				$sql_receive="select a.currency_id,a.receive_purpose,b.prod_id, (b.order_qnty) as qty, (b.order_amount) as amnt,b.cons_quantity,b.cons_amount from inv_receive_master a, inv_transaction b where  a.id=b.mst_id and b.transaction_type=1 and b.item_category=1 and b.status_active=1 and b.is_deleted=0";
				$resultReceive = sql_select($sql_receive);
				foreach($resultReceive as $invRow)
				{
					if($invRow[csf('currency_id')]==1)//Taka
					{
						$avg_rate=$invRow[csf('cons_amount')]/$invRow[csf('cons_quantity')];
						$receive_array[$invRow[csf('prod_id')]]=$avg_rate/$exchange_rate;
					}
					else if($invRow[csf('currency_id')]==2)//USD
					{
						$avg_rate=$invRow[csf('amnt')]/$invRow[csf('qty')];
						$receive_array[$invRow[csf('prod_id')]]=$avg_rate;
					}
					else
					{
						
						$receive_array[$invRow[csf('prod_id')]]=$avg_rate;
					}
				}
				
				$yarnTrimsDataArray=sql_select("select b.po_breakdown_id, b.prod_id, a.item_category, 
					sum(CASE WHEN a.transaction_type=2 and b.entry_form ='3' and b.trans_type=2 and b.issue_purpose!=2 THEN b.quantity ELSE 0 END) AS yarn_iss_qty,
					sum(CASE WHEN a.transaction_type=4 and b.entry_form ='9' and b.trans_type=4 THEN b.quantity ELSE 0 END) AS yarn_iss_return_qty,
					sum(CASE WHEN a.transaction_type=5 and b.entry_form ='11' and b.trans_type=5 THEN b.quantity ELSE 0 END) AS trans_in_qty_yarn,
					sum(CASE WHEN a.transaction_type=6 and b.entry_form ='11' and b.trans_type=6 THEN b.quantity ELSE 0 END) AS trans_out_qty_yarn,
					sum(CASE WHEN a.transaction_type=2 and b.entry_form ='25' and b.trans_type=2 THEN a.cons_rate*b.quantity ELSE 0 END) AS trims_issue_amnt
					from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category in(1,4) and a.transaction_type in(2,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in4  group by b.po_breakdown_id, b.prod_id, a.item_category");
				foreach($yarnTrimsDataArray as $invRow)
				{
					if($invRow[csf('item_category')]==1)
					{
						$iss_qty=$invRow[csf('yarn_iss_qty')]+$invRow[csf('trans_in_qty_yarn')]-$invRow[csf('yarn_iss_return_qty')]-$invRow[csf('trans_out_qty_yarn')];
						$rate='';
						if($receive_array[$invRow[csf('prod_id')]]>0)
						{
							$rate=$receive_array[$invRow[csf('prod_id')]]/$exchange_rate;
						}
						else
						{
							$rate=$avg_rate_array[$invRow[csf('prod_id')]]/$exchange_rate;
						}
						
						$iss_amnt=$iss_qty*$rate;
						$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]][1]+=$iss_amnt;
					}
					else
					{
						$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]][4]+=$invRow[csf('trims_issue_amnt')];
					}
				}
				$ex_rate=80;
				$pi_number_check=array();
				
				$sqlPi=sql_select("select c.po_break_down_id, sum(b.quantity) as quantity from  com_pi_master_details a, com_pi_item_details b,wo_booking_dtls c where a.id=b.pi_id and b.work_order_no=c.booking_no  and a.item_category_id=2 and a.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0 $po_cond_for_in5  group by c.po_break_down_id");
				
				foreach($sqlPi as $rowPi){
					
						$pi_number_check[$rowPi[csf('po_break_down_id')]]=$rowPi[csf('po_break_down_id')];
				}
				
				$sql_fin_purchase="select  c.currency_id,b.po_breakdown_id, sum(a.cons_rate*b.quantity) as finish_purchase_amnt,sum(a.order_amount) as order_amount from inv_receive_master c,inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.receive_basis in(1,2) and a.item_category=2 and a.transaction_type=1 and b.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in4 group by b.po_breakdown_id, c.currency_id";
				$dataArrayFinPurchase=sql_select($sql_fin_purchase);
				foreach($dataArrayFinPurchase as $finRow)
				{
					if($pi_number_check[$finRow[csf('po_breakdown_id')]]==$finRow[csf('po_breakdown_id')])
					{
						$currency_id=$finRow[csf('currency_id')];
						if($currency_id==2)//USD
						{
							$finish_purchase_amnt_arr[$finRow[csf('po_breakdown_id')]]=$finRow[csf('order_amount')];
						}
						else
						{
							$finish_purchase_amnt_arr[$finRow[csf('po_breakdown_id')]]+=$finRow[csf('woven_purchase_amnt')]/$ex_rate;
						}
					}
				}
				
				//  $sql_fin_purchase_wv="select c.currency_id, b.po_breakdown_id, sum(a.cons_rate*b.quantity) as woven_purchase_amnt,sum(a.order_amount) as order_amount from inv_receive_master c,inv_transaction a, order_wise_pro_details b where c.id=a.mst_id and a.id=b.trans_id and a.item_category=3 and a.transaction_type=1 and c.entry_form=17 and b.entry_form=17 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $po_cond_for_in4 group by b.po_breakdown_id,c.currency_id";
				// $dataArrayFinPurchaseW=sql_select($sql_fin_purchase_wv);
				// foreach($dataArrayFinPurchaseW as $finRow)
				// {
				// 	$currency_id=$finRow[csf('currency_id')];
				// 	if($currency_id==2)//USD
				// 	{
				// 		$finish_purchase_amnt_arr[$finRow[csf('po_breakdown_id')]]+=$finRow[csf('order_amount')];
				// 	}
				// 	else
				// 	{
				// 		$finish_purchase_amnt_arr[$finRow[csf('po_breakdown_id')]]+=$finRow[csf('woven_purchase_amnt')]/$ex_rate;
				// 	}
				// }

				 $sql_fin_purchase_wv="SELECT a.id, a.booking_no,a.booking_date,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.booking_type,b.job_no,b.po_break_down_id as po_id,sum(b.grey_fab_qnty) as grey_fab_qnty,sum(b.amount) as amount
				from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c  
				where a.booking_no=b.booking_no and c.id=b.pre_cost_fabric_cost_dtls_id and a.booking_type=1 and b.fin_fab_qnty>0
				 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1 $po_cond_for_in6
				 group by a.id, a.booking_no,a.booking_date,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.booking_type,b.job_no,b.po_break_down_id";

				$dataArrayFinPurchaseW=sql_select($sql_fin_purchase_wv);
				foreach($dataArrayFinPurchaseW as $finRow)
				{
					$currency_id=$finRow[csf('currency_id')];
					if($currency_id==2)//USD
					{
						$finish_purchase_amnt_arr[$finRow[csf('po_id')]]+=$finRow[csf('amount')];
					}
					else
					{
						$finish_purchase_amnt_arr[$finRow[csf('po_id')]]+=$finRow[csf('amount')]/$ex_rate;
					}
				}
				// print_r($finishs_purchase_amnt_arr);
				$i=1; $tot_po_qnty=0; $tot_po_value=0; $tot_ex_factory_qnty=0; $tot_ex_factory_val=0; $tot_yarn_cost_mkt=0; $tot_knit_cost_mkt=0; $tot_dye_finish_cost_mkt=0; $tot_yarn_dye_cost_mkt=0; $tot_aop_cost_mkt=0; $tot_trims_cost_mkt=0; $tot_embell_cost_mkt=0; $tot_wash_cost_mkt=0; $tot_commission_cost_mkt=0; $tot_comm_cost_mkt=0; $tot_freight_cost_mkt=0; $tot_test_cost_mkt=0; $tot_inspection_cost_mkt=0; $tot_currier_cost_mkt=0; $tot_cm_cost_mkt=0; $tot_mkt_all_cost=0; $tot_mkt_margin=0; $tot_yarn_cost_actual=0; $tot_knit_cost_actual=0; $tot_dye_finish_cost_actual=0; $tot_yarn_dye_cost_actual=0; $tot_aop_cost_actual=0; $tot_trims_cost_actual=0; $tot_embell_cost_actual=0; $tot_wash_cost_actual=0; $tot_commission_cost_actual=0; $tot_comm_cost_actual=0; $tot_freight_cost_actual=0; $tot_test_cost_actual=0; $tot_inspection_cost_actual=0; $tot_currier_cost_actual=0; $tot_cm_cost_actual=0; $tot_actual_all_cost=0; $tot_actual_margin=0; $tot_fabric_purchase_cost_mkt=0; $tot_fabric_purchase_cost_actual=0;$tot_CM_Value_Contribution_value_mkt=0;$tot_CM_Value_Contribution_value_actual=0;$tot_cm_variance_contribution_value=$tot_tot_variance_cm_value_cost=0;
				
				$buyer_name_array=array();

				$mkt_po_val_array = array(); $mkt_yarn_array = array(); $mkt_knit_array = array(); $mkt_dy_fin_array = array(); $mkt_yarn_dy_array = array();
				$mkt_aop_array = array(); $mkt_trims_array = array(); $mkt_emb_array = array(); $mkt_wash_array=array(); $mkt_commn_array=array(); $mkt_commercial_array=array();
				$mkt_freight_array = array(); $mkt_test_array = array(); $mkt_ins_array = array(); $mkt_courier_array = array(); $mkt_cm_array = array();
				$mkt_total_array = array(); $mkt_margin_array = array(); $mkt_fabric_purchase_array = array();
				
				$ex_factory_val_array= array(); $yarn_cost_array= array(); $knit_cost_array= array(); $dye_cost_array= array(); $yarn_dyeing_cost_array= array();
				$aop_n_others_cost_array= array(); $trims_cost_array= array(); $enbellishment_cost_array= array(); $wash_cost_array= array(); $commission_cost_array= array(); 
				$commercial_cost_array= array(); $freight_cost_array= array(); $testing_cost_array= array(); $inspection_cost_array= array(); $courier_cost_array= array();
				$cm_cost_array= array(); $total_cost_array= array(); $margin_array= array(); $ex_factory_array=array(); $actual_cm_amnt=array(); $actual_fabric_purchase_array = array();
				/*$JobArr=array();
				foreach($result as $row_yarn){
					$JobArr[]=$row_yarn[csf('job_no')];
				}
				$yarn= new yarn($JobArr,'job');
				$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
				$yarn->unsetDataArray();*/
				
				 $condition= new condition();
				 $condition->company_name("=$company_name");
				 if(str_replace("'","",$cbo_buyer_name)>0){
					  $condition->buyer_name("=$cbo_buyer_name");
				 }
				 if(str_replace("'","",$txt_job_no) !=''){
					  $condition->job_no_prefix_num("=$txt_job_no");
				 }
				 if(str_replace("'","",$txt_job_no)=='')
				 {
					$condition->job_no("in($all_jobs)");
				 }
				 if(trim(str_replace("'","",$txt_order_no))!='' && str_replace("'","",$hide_order_id)=='') 
				 {
					$condition->po_number(" like '%".trim(str_replace("'","",$txt_order_no))."%'"); 
				 }
				$order_id=str_replace("'","",$hide_order_id);
				 if(trim(str_replace("'","",$txt_order_no))!='' && str_replace("'","",$hide_order_id)!='') 
				 {
					$condition->po_id_in("$order_id"); 
				 }
				 if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
				 {
					 $start_date=str_replace("'","",$txt_date_from);
					 $end_date=str_replace("'","",$txt_date_to);
					 $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
					 //and b.po_received_date between '$start_date' and '$end_date' 
					// echo 'FFGG';
				 }
				 if(str_replace("'","",$cbo_status) >0){
					  $condition->is_confirmed("=$cbo_status");
				 }
				 if(str_replace("'","",$cbo_status)==0){
					  $condition->is_confirmed("in(1,2)");
				 }
				 /*if($txt_order_id!='' || $txt_order_id!=0)
				 {
					$condition->po_id("in($txt_order_id)"); 
				 }*/
				 
				 
				 $condition->init();
				
				 $yarn= new yarn($condition);
				// echo $yarn->getQuery();
				 $yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
				 $conversion= new conversion($condition);
				 $conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
				 $trims= new trims($condition);
				 $trims_costing_arr=$trims->getAmountArray_by_order();
				$emblishment= new emblishment($condition);
				$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
				$commission= new commision($condition);
				$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
				$commercial= new commercial($condition);
				$commercial_costing_arr=$commercial->getAmountArray_by_order();
				$other= new other($condition);
				$other_costing_arr=$other->getAmountArray_by_order();
				$wash= new wash($condition);
				$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();
				$fabric= new fabric($condition);
				$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
				
				//print_r($fabric_costing_arr);die;
				$not_yarn_dyed_cost_arr=array(1,2,30,35);
				foreach($result as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$po_ids_array[]=$row[csf('id')];
					$gmts_item='';
					$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
					foreach($gmts_item_id as $item_id)
					{
						if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
					}
					
					$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
					$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
					$unit_price=$row[csf('unit_price')]/$row[csf('ratio')];
					$po_value=$order_qnty_in_pcs*$unit_price;
					
					$tot_po_qnty+=$order_qnty_in_pcs; 
					$tot_po_value+=$po_value;
					
					$ex_factory_qty=$ex_factory_arr[$row[csf('id')]];
					$ex_factory_value=$ex_factory_qty*$unit_price;
					
					$tot_ex_factory_qnty+=$ex_factory_qty; 
					$tot_ex_factory_val+=$ex_factory_value; 
					
					$dzn_qnty=0;
					$costing_per_id=$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
					if($costing_per_id==1)
					{
						$dzn_qnty=12;
					}
					else if($costing_per_id==3)
					{
						$dzn_qnty=12*2;
					}
					else if($costing_per_id==4)
					{
						$dzn_qnty=12*3;
					}
					else if($costing_per_id==5)
					{
						$dzn_qnty=12*4;
					}
					else
					{
						$dzn_qnty=1;
					}

					$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
					$yarn_cost_mkt=$yarn_costing_arr[$row[csf('id')]];
					//$yarn_cost_mkt_data+=$yarn_costing_arr[$row[csf('id')]];
					//echo $row[csf('id')].'aa';die;
					//($plan_cut_qnty/$dzn_qnty)*$yarncostArray[$row[csf('job_no')]];
					//$knit_cost_mkt=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['knit_charge'];
					$knit_cost_mkt=array_sum($conversion_costing_arr_process[$row[csf('id')]][1]);//($plan_cut_qnty/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['knit_charge'];
					//echo $plan_cut_qnty."__".$dzn_qnty."__".$prodcostArray[$row[csf('job_no')]]['knit_charge'];
					//$dye_finish_cost_mkt=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['dye_finish_charge'];
					//$dye_finish_cost_mkt=($plan_cut_qnty/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['dye_finish_charge'];
					
					$dye_finish_cost_mkt=0;
					foreach($conversion_cost_head_array as $process_id=>$val)
					{
						if(!in_array($process_id,$not_yarn_dyed_cost_arr))
						{
							$dye_finish_cost_mkt+=array_sum($conversion_costing_arr_process[$row[csf('id')]][$process_id]);
						}
					}					//$yarn_dye_cost_mkt=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['yarn_dye_charge'];
					//$yarn_dye_cost_mkt=($plan_cut_qnty/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['yarn_dye_charge'];
					$yarn_dye_cost_mkt=array_sum($conversion_costing_arr_process[$row[csf('id')]][30]);
					//$aop_cost_mkt=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['aop_charge'];
					$aop_cost_mkt=array_sum($conversion_costing_arr_process[$row[csf('id')]][35]);//($plan_cut_qnty/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['aop_charge'];
					$trims_cost_mkt=$trims_costing_arr[$row[csf('id')]];//($order_qnty_in_pcs/$dzn_qnty)*$trimsCostArray[$row[csf('id')]];
					
					$print_amount=$emblishment_costing_arr_name[$row[csf('id')]][1];
					$embroidery_amount=$emblishment_costing_arr_name[$row[csf('id')]][2];
					$special_amount=$emblishment_costing_arr_name[$row[csf('id')]][4];
					$wash_cost=$emblishment_costing_arr_name_wash[$row[csf('id')]][3];
					$other_amount=$emblishment_costing_arr_name[$row[csf('id')]][5];
					$foreign_cost=$commission_costing_arr[$row[csf('id')]][1];
					$local_cost=$commission_costing_arr[$row[csf('id')]][2];
					
					$test_cost=$other_costing_arr[$row[csf('id')]]['lab_test'];
					$freight_cost=$other_costing_arr[$row[csf('id')]]['freight'];
					$inspection_cost=$other_costing_arr[$row[csf('id')]]['inspection'];
					$certificate_cost=$other_costing_arr[$row[csf('id')]]['certificate_pre_cost'];
					//$common_oh_cost=$other_costing_arr[$row[csf('id')]]['common_oh'];
					$currier_cost=$other_costing_arr[$row[csf('id')]]['currier_pre_cost'];
					$cm_cost=$other_costing_arr[$row[csf('id')]]['cm_cost'];
					
					//$embell_cost_mkt=($order_qnty_in_pcs/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['embel_cost'];
					//$wash_cost_mkt=($order_qnty_in_pcs/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['wash_cost'];
					$embell_cost_mkt=$print_amount+$embroidery_amount+$special_amount+$other_amount;
					//$embell_cost_mkt=($plan_cut_qnty/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['embel_cost'];
					//$wash_cost_mkt=($plan_cut_qnty/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['wash_cost'];
					//$comm_cost_mkt=($order_qnty_in_pcs/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['comm_cost'];
					//$freight_cost_mkt=($order_qnty_in_pcs/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['freight'];
					//$test_cost_mkt=($order_qnty_in_pcs/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['lab_test'];
					//$inspection_cost_mkt=($order_qnty_in_pcs/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['inspection'];
					//$currier_cost_mkt=($order_qnty_in_pcs/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['currier_pre_cost'];					//$cm_cost_mkt=($order_qnty_in_pcs/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['cm_cost'];
					//$commission_cost_mkt=($order_qnty_in_pcs/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['commission'];
					$wash_cost_mkt=$wash_cost;
					$commission_cost_mkt=$foreign_cost+$local_cost;
					$comm_cost_mkt=$commercial_costing_arr[$row[csf('id')]];
					$freight_cost_mkt=$freight_cost;
					$test_cost_mkt=$test_cost;
					$inspection_cost_mkt=$inspection_cost;
					$currier_cost_mkt=$currier_cost;
					$cm_cost_mkt=$cm_cost;
					
					$fabric_purchase_cost_mkt=array_sum($fabric_costing_arr['knit']['grey'][$row[csf('id')]])+array_sum($fabric_costing_arr['woven']['grey'][$row[csf('id')]]);
					
					$mkt_all_cost=$yarn_cost_mkt+$knit_cost_mkt+$dye_finish_cost_mkt+$yarn_dye_cost_mkt+$aop_cost_mkt+$trims_cost_mkt+$embell_cost_mkt+$wash_cost_mkt+$commission_cost_mkt+$comm_cost_mkt+$freight_cost_mkt+$test_cost_mkt+$inspection_cost_mkt+$currier_cost_mkt+$cm_cost_mkt+$fabric_purchase_cost_mkt;
					
					$mkt_margin=$po_value-$mkt_all_cost;
					if($mkt_margin>0 && $po_value>0) 	$mkt_margin_perc=($mkt_margin/$po_value)*100;else $mkt_margin_perc="";
					
					//$yarn_cost_actual=($order_qnty_in_pcs/$dzn_qnty)*$yarncostArray[$row[csf('job_no')]];
					//$knit_cost_actual=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['knit_charge'];
					//$dye_finish_cost_actual=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['dye_finish_charge'];
					//$yarn_dye_cost_actual=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['yarn_dye_charge'];
					//$aop_cost_actual=($order_qnty_in_pcs/$dzn_qnty)*$prodcostArray[$row[csf('job_no')]]['aop_charge'];
					//$trims_cost_actual=($order_qnty_in_pcs/$dzn_qnty)*$trimsCostArray[$row[csf('id')]];
					
					$yarn_cost_actual=$yarnTrimsCostArray[$row[csf('id')]][1];
					//$trims_cost_actual=$yarnTrimsCostArray[$row[csf('id')]][4]/$exchange_rate;
					$trims_cost_actual=$actualTrimsCostArray[$row[csf('id')]];
					$yarn_dye_cost_actual=0;
					$aop_cost_actual=$aopCostArray[$row[csf('id')]];
					$embell_cost_actual=$embellCostArray[$row[csf('id')]];
					$wash_cost_actual=$washCostArray[$row[csf('id')]];
					$knit_cost_actual=$subconCostArray[$row[csf('id')]]['knit_bill']/$exchange_rate;
					$dye_finish_cost_actual=$subconCostArray[$row[csf('id')]]['dye_finish_bill']/$exchange_rate;
					$commission_cost_actual=($ex_factory_qty/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['commission'];
					$comm_cost_actual=$actualCostArray[6][$row[csf('id')]];
					$freight_cost_actual=$actualCostArray[2][$row[csf('id')]];
					$test_cost_actual=$actualCostArray[1][$row[csf('id')]];
					$inspection_cost_actual=$actualCostArray[3][$row[csf('id')]];
					$currier_cost_actual=$actualCostArray[4][$row[csf('id')]];
					$cm_cost_actual=$actualCostArray[5][$row[csf('id')]];
					
					$fabric_purchase_cost_actual=$finish_purchase_amnt_arr[$row[csf('id')]];
					
					$actual_all_cost=$yarn_cost_actual+$knit_cost_actual+$dye_finish_cost_actual+$yarn_dye_cost_actual+$aop_cost_actual+$trims_cost_actual+$embell_cost_actual+$wash_cost_actual+$commission_cost_actual+$comm_cost_actual+$freight_cost_actual+$test_cost_actual+$inspection_cost_actual+$currier_cost_actual+$cm_cost_actual+$fabric_purchase_cost_actual;
					
					$actual_margin=$ex_factory_value-$actual_all_cost;
					if($actual_margin>0 && $ex_factory_value>0)  $actual_margin_perc=($actual_margin/$ex_factory_value)*100;else  $actual_margin_perc="";
					
					$tot_yarn_cost_mkt+=$yarn_cost_mkt; 
					$tot_knit_cost_mkt+=$knit_cost_mkt; 
					$tot_dye_finish_cost_mkt+=$dye_finish_cost_mkt; 
					$tot_yarn_dye_cost_mkt+=$yarn_dye_cost_mkt; 
					$tot_aop_cost_mkt+=$aop_cost_mkt; 
					$tot_trims_cost_mkt+=$trims_cost_mkt; 
					$tot_embell_cost_mkt+=$embell_cost_mkt; 
					$tot_wash_cost_mkt+=$wash_cost_mkt; 
					$tot_commission_cost_mkt+=$commission_cost_mkt; 
					$tot_comm_cost_mkt+=$comm_cost_mkt; 
					$tot_freight_cost_mkt+=$freight_cost_mkt; 
					$tot_test_cost_mkt+=$test_cost_mkt; 
					$tot_inspection_cost_mkt+=$inspection_cost_mkt; 
					$tot_currier_cost_mkt+=$currier_cost_mkt; 
					$tot_cm_cost_mkt+=$cm_cost_mkt; 
					$tot_mkt_all_cost+=$mkt_all_cost; 
					$tot_mkt_margin+=$mkt_margin; 
					$tot_yarn_cost_actual+=$yarn_cost_actual; 
					$tot_knit_cost_actual+=$knit_cost_actual; 
					$tot_dye_finish_cost_actual+=$dye_finish_cost_actual;
					$tot_yarn_dye_cost_actual+=$yarn_dye_cost_actual; 
					$tot_aop_cost_actual+=$aop_cost_actual; 
					$tot_trims_cost_actual+=$trims_cost_actual; 
					$tot_embell_cost_actual+=$embell_cost_actual;
					$tot_wash_cost_actual+=$wash_cost_actual; 
					$tot_commission_cost_actual+=$commission_cost_actual; 
					$tot_comm_cost_actual+=$comm_cost_actual; 
					$tot_freight_cost_actual+=$freight_cost_actual; 
					$tot_test_cost_actual+=$test_cost_actual; 
					$tot_inspection_cost_actual+=$inspection_cost_actual; 
					$tot_currier_cost_actual+=$currier_cost_actual; 
					$tot_cm_cost_actual+=$cm_cost_actual; 
					$tot_actual_all_cost+=$actual_all_cost; 
					$tot_actual_margin+=$actual_margin;
					
					$tot_fabric_purchase_cost_mkt+=$fabric_purchase_cost_mkt;
					$tot_fabric_purchase_cost_actual+=$fabric_purchase_cost_actual;
					
					$buyer_name_array[$row[csf('buyer_name')]]=$buyer_arr[$row[csf('buyer_name')]];
					$mkt_po_val_array[$row[csf('buyer_name')]]+=$po_value;
					$mkt_yarn_array[$row[csf('buyer_name')]]+=$yarn_cost_mkt+$yarn_dye_cost_mkt;
					$mkt_knit_array[$row[csf('buyer_name')]]+=$knit_cost_mkt;
					$mkt_dy_fin_array[$row[csf('buyer_name')]]+=$dye_finish_cost_mkt;
					//$mkt_yarn_dy_array[$row[csf('buyer_name')]]+=$yarn_dye_cost_mkt;
					$mkt_aop_array[$row[csf('buyer_name')]]+=$aop_cost_mkt;
					$mkt_trims_array[$row[csf('buyer_name')]]+=$trims_cost_mkt;
					$mkt_emb_array[$row[csf('buyer_name')]]+=$embell_cost_mkt;
					$mkt_wash_array[$row[csf('buyer_name')]]+=$wash_cost_mkt;
					$mkt_commn_array[$row[csf('buyer_name')]]+=$commission_cost_mkt;
					$mkt_commercial_array[$row[csf('buyer_name')]]+=$comm_cost_mkt;
					$mkt_freight_array[$row[csf('buyer_name')]]+=$freight_cost_mkt;
					$mkt_test_array[$row[csf('buyer_name')]]+=$test_cost_mkt;
					$mkt_ins_array[$row[csf('buyer_name')]]+=$inspection_cost_mkt;
					$mkt_courier_array[$row[csf('buyer_name')]]+=$currier_cost_mkt;
					$mkt_cm_array[$row[csf('buyer_name')]]+=$cm_cost_mkt;
					$mkt_total_array[$row[csf('buyer_name')]]+=$mkt_all_cost;
					$mkt_margin_array[$row[csf('buyer_name')]]+=$mkt_margin;
					$mkt_fabric_purchase_array[$row[csf('buyer_name')]]+=$fabric_purchase_cost_mkt;
					
					$ex_factory_val_array[$row[csf('buyer_name')]]+=$ex_factory_value;
					$yarn_cost_array[$row[csf('buyer_name')]]+=$yarn_cost_actual;
					$knit_cost_array[$row[csf('buyer_name')]]+=$knit_cost_actual;
					$dye_cost_array[$row[csf('buyer_name')]]+=$dye_finish_cost_actual;
					//$yarn_dyeing_cost_array[$row[csf('buyer_name')]]+=$yarn_dye_cost_actual;
					$aop_n_others_cost_array[$row[csf('buyer_name')]]+=$aop_cost_actual;
					$trims_cost_array[$row[csf('buyer_name')]]+=$trims_cost_actual;
					$enbellishment_cost_array[$row[csf('buyer_name')]]+=$embell_cost_actual;
					$wash_cost_array[$row[csf('buyer_name')]]+=$wash_cost_actual;
					$commission_cost_array[$row[csf('buyer_name')]]+=$commission_cost_actual;
					$commercial_cost_array[$row[csf('buyer_name')]]+=$comm_cost_actual;
					$freight_cost_array[$row[csf('buyer_name')]]+=$freight_cost_actual;
					$testing_cost_array[$row[csf('buyer_name')]]+=$test_cost_actual;
					$inspection_cost_array[$row[csf('buyer_name')]]+=$inspection_cost_actual;
					$courier_cost_array[$row[csf('buyer_name')]]+=$currier_cost_actual;
					$cm_cost_array[$row[csf('buyer_name')]]+=$cm_cost_actual;
					$total_cost_array[$row[csf('buyer_name')]]+=$actual_all_cost;
					$margin_array[$row[csf('buyer_name')]]+=$actual_margin;
					$actual_fabric_purchase_array[$row[csf('buyer_name')]]+=$fabric_purchase_cost_actual;
					
					
				 $button_po="<a href='#' onClick=\"generate_po_report('".$company_name."','".$row[csf('id')]."','".$row[csf('job_no')]."','show_po_detail_report','1')\" '> ".$row[csf('po_number')]."<a/>";
					
				?>
                	<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                        <td width="40" rowspan="4"><? echo $i; ?></td>
                        <td width="70" rowspan="4"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                        <td width="70" rowspan="4"><p><? echo $brand_library[$row[csf('brand_id')]]; ?></p></td>
                        <td width="60" align="center" rowspan="4"><? echo $row[csf('year')]; ?></td>
                        <td width="70" align="center" rowspan="4"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                        <td width="100" rowspan="4" style="word-break:break-all;"><? echo  $button_po;//$row[csf('po_number')]; ?></td>
                        <td width="70" rowspan="4" style="word-break:break-all;"><? echo $row[csf('file_no')]; ?></td>
                        <td width="80" rowspan="4" style="word-break:break-all;"><? echo $row[csf('grouping')]; ?></td>
                        <td width="110" rowspan="4" style="word-break:break-all;"><? echo $row[csf('style_ref_no')]; ?></td>
                        <td width="70" rowspan="4"><p><? echo $season_library[$row[csf('season')]]; ?></p></td>
               			<td width="70" rowspan="4"><p><? echo $row[csf('season_year')]; ?></p></td>
                        <td width="120" rowspan="4" style="word-break:break-all;"><? echo $gmts_item; ?></td>
                        <td width="88" align="right" style="padding-right:2px" rowspan="4"><? echo $order_qnty_in_pcs; ?></td>
                        <td width="50" align="center" rowspan="4"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                        <td width="68" align="right" style="padding-right:2px" rowspan="4"><? echo number_format($unit_price,2,'.',''); ?></td>
                        <td width="108" align="right" style="padding-right:2px" rowspan="4"><? echo number_format($po_value,2,'.',''); ?></td>
                        <td width="98" align="right" style="padding-right:2px" rowspan="4"><? echo number_format($row[csf('set_smv')],2,'.',''); ?></td>
                        <td width="80" align="center" rowspan="4">
                        	
                        		<p><? if($row[csf("pub_shipment_date")] !="" && $row[csf("pub_shipment_date")] !="0000-00-00")  echo change_date_format($row[csf("pub_shipment_date")]); ?>&nbsp;</p>
                        	</td>
                        <td width="100" rowspan="4" style="word-break:break-all;"><? echo $shipment_status[$row[csf('shiping_status')]]; ?></td>
                        <td width="100" align="left"><b>Pre Costing</b></td>
                        <td width="98" align="right" style="padding-right:2px"><? echo number_format($order_qnty_in_pcs,0,'.',''); ?></td>
                        <td width="108" align="right" style="padding-right:2px"><? echo number_format($po_value,2,'.',''); ?></td>
                        <td width="108" style="padding-right:2px" align="right"><? echo number_format($fabric_purchase_cost_mkt,2,'.',''); ?></td>
                        <td width="108" style="padding-right:2px" align="right"><? echo number_format($trims_cost_mkt,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($embell_cost_mkt,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($wash_cost_mkt,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($commission_cost_mkt,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($comm_cost_mkt,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($freight_cost_mkt,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($test_cost_mkt,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($inspection_cost_mkt,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($currier_cost_mkt,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($mkt_all_cost,2,'.',''); ?></td>
                        <?
                        $Net_PO_ex_Fact_Value_mkt=$po_value-$commission_cost_mkt;
						
						$CM_Value_Contribution_value_mkt=$Net_PO_ex_Fact_Value_mkt-$mkt_all_cost;
						$tot_CM_Value_Contribution_value_mkt+=$CM_Value_Contribution_value_mkt;
						?>
                        <td width="98" style="padding-right:2px" title="PO Value-Commission-All Cost" align="right"><?  echo fn_number_format($CM_Value_Contribution_value_mkt,2,'.',''); ?></td>
                        
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($cm_cost_mkt,2,'.',''); ?></td>
                      
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($mkt_margin,2,'.',''); ?></td>
                        <td width="100" style="padding-right:2px" align="right"><? echo number_format($mkt_margin_perc,2,'.',''); ?></td>
                    </tr>
                    <tr bgcolor="#F5F5F5" onClick="change_color('tr_a<? echo $i; ?>','#F5F5F5')" id="tr_a<? echo $i; ?>">
                    	<td width="100" align="left"><b>Actual</b></td>
                        <td width="98" align="right" style="padding-right:2px">
						<a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $row[csf('job_no_prefix_num')];?>','<? echo $row[csf('id')]; ?>','650px')"><? echo  number_format($ex_factory_qty,0,'.',''); ?></a></td>
                        <td width="108" align="right" style="padding-right:2px"><? echo number_format($ex_factory_value,2,'.',''); ?></td>              
                                               
                        <td width="108" style="padding-right:2px" align="right" title="Exchange rate=80"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','fabric_purchase_cost_actual2','Fabric Purchase Cost Details','1100px','<?=$company_name ; ?>')"><? echo number_format($fabric_purchase_cost_actual,2,'.',''); ?></a></td>
                        <td width="108" style="padding-right:2px" align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','trims_cost_actual','Trims Cost Details','800px',<?=$company_name; ?>)"><? echo number_format($trims_cost_actual,2,'.',''); ?></a></td>
                        <td width="98" style="padding-right:2px" align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','embell_cost_actual','Embellishment Cost Details','800px')"><? echo number_format($embell_cost_actual,2,'.',''); ?></a></td>
                        <td width="98" style="padding-right:2px" align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','wash_cost_actual','Wash Cost Details','800px')"><? echo number_format($wash_cost_actual,2,'.',''); ?></a></td>
                        <td width="98" style="padding-right:2px" align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]."_".$row[csf('job_no')]."_".$ex_factory_qty."_".$dzn_qnty; ?>','commission_cost_actual','Commission Cost Details','600px')"><? echo number_format($commission_cost_actual,2,'.',''); ?></a></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($comm_cost_actual,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($freight_cost_actual,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($test_cost_actual,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($inspection_cost_actual,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($currier_cost_actual,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($actual_all_cost,2,'.',''); ?></td>
                        <?
                        $Net_PO_ex_Fact_Value_actual=$ex_factory_value-$commission_cost_actual;
						$CM_Value_Contribution_actual=$Net_PO_ex_Fact_Value_actual-$actual_all_cost;
						$tot_CM_Value_Contribution_value_actual+=$CM_Value_Contribution_actual;
						?>
                        <td width="98" style="padding-right:2px" title="Ex-Fact Value-Commission-All Cost" align="right"><? echo number_format($CM_Value_Contribution_actual,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($cm_cost_actual,2,'.',''); ?></td>
                       
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($actual_margin,2,'.',''); ?></td>
                        <td width="100" style="padding-right:2px" align="right"><? echo number_format($actual_margin_perc,2,'.',''); ?></td>
                    </tr>
                    <tr bgcolor="#E9E9E9" onClick="change_color('tr_v<? echo $i; ?>','#E9E9E9')" id="tr_v<? echo $i; ?>">
                    	<td width="100" align="left"><b>Variance</b></td>
                    	<td width="98" align="right" title="Ex Fact-PoQty" style="padding-right:2px">
							<? 
								$variance_qnty=$ex_factory_qty-$order_qnty_in_pcs;
								if($variance_qnty>0 && $order_qnty_in_pcs>0)   $variance_qnty_per=($variance_qnty/$order_qnty_in_pcs)*100; else  $variance_qnty_per="";
								echo number_format($variance_qnty,0,'.',''); 
							?>
                        </td>
                    	<td width="108" align="right" style="padding-right:2px">
                        	<? 
								$variance_po_value=$ex_factory_value-$po_value;
								if($po_value>0 && $variance_po_value>0)   $variance_po_value_per=($variance_po_value/$po_value)*100; else  $variance_po_value_per="";
								echo number_format($variance_po_value,2,'.',''); 
							?>
                        </td>
                        <td width="108" style="padding-right:2px" align="right">
                        	<? 
								$variance_finish_purchase_cost=$fabric_purchase_cost_mkt-$fabric_purchase_cost_actual;
								if($variance_finish_purchase_cost>0 && $fabric_purchase_cost_mkt>0)  $variance_finish_purchase_cost_per=($variance_finish_purchase_cost/$fabric_purchase_cost_mkt)*100; else  $variance_finish_purchase_cost_per="";
								echo number_format($variance_finish_purchase_cost,2,'.','');
							?>
                        </td>
                        <td width="108" style="padding-right:2px" align="right">
                        	<? 
								$variance_trims_cost=$trims_cost_mkt-$trims_cost_actual;
								if($variance_trims_cost>0 && $trims_cost_mkt>0)  $variance_trims_cost_per=($variance_trims_cost/$trims_cost_mkt)*100; else  $variance_trims_cost_per="";
								echo number_format($variance_trims_cost,2,'.','');
							?>
                        </td>
                        <td width="98" style="padding-right:2px" align="right">
                        	<? 
								$variance_embell_cost=$embell_cost_mkt-$embell_cost_actual;
								if($variance_embell_cost>0 && $embell_cost_mkt>0)  $variance_embell_cost_per=($variance_embell_cost/$embell_cost_mkt)*100; else  $variance_embell_cost_per="";
								echo number_format($variance_embell_cost,2,'.',''); 
							?>
                        </td>
                        <td width="98" style="padding-right:2px" align="right">
                        	<? 
								$variance_wash_cost=$wash_cost_mkt-$wash_cost_actual;
								if($variance_wash_cost>0 && $wash_cost_mkt>0) $variance_wash_cost_per=($variance_wash_cost/$wash_cost_mkt)*100; else  $variance_wash_cost_per="";
								echo number_format($variance_wash_cost,2,'.',''); 
							?>
                        </td>
                        <td width="98" style="padding-right:2px" align="right">
                        	<? 
								$variance_commission_cost=$commission_cost_mkt-$commission_cost_actual;
								if($variance_commission_cost>0 && $commission_cost_mkt>0) $variance_commission_cost_per=($variance_commission_cost/$commission_cost_mkt)*100;  else  $variance_commission_cost_per="";
								echo number_format($variance_commission_cost,2,'.',''); 
							?>
                        </td>
                        <td width="98" style="padding-right:2px" align="right">
                        	<? 
								$variance_comm_cost=$comm_cost_mkt-$comm_cost_actual;
								if($comm_cost_mkt>0 && $variance_comm_cost>0)  $variance_comm_cost_per=($variance_comm_cost/$comm_cost_mkt)*100;  else  $variance_comm_cost_per="";
								echo number_format($variance_comm_cost,2,'.',''); 
							?>
                        </td>
                        <td width="98" style="padding-right:2px" align="right">
                        	<? 
								$variance_freight_cost=$freight_cost_mkt-$freight_cost_actual;
								if($variance_freight_cost>0 && $freight_cost_mkt>0)  $variance_freight_cost_per=($variance_freight_cost/$freight_cost_mkt)*100; else  $variance_freight_cost_per="";
								echo number_format($variance_freight_cost,2,'.',''); 
							?>
                        </td>
                        <td width="98" style="padding-right:2px" align="right">
                        	<? 
								$variance_test_cost=$test_cost_mkt-$test_cost_actual;
								if($variance_test_cost>0 && $test_cost_mkt>0)  $variance_test_cost_per=($variance_test_cost/$test_cost_mkt)*100;else  $variance_test_cost_per=""; 
								echo number_format($variance_test_cost,2,'.','');
							?>
                        </td>
                        <td width="98" style="padding-right:2px" align="right">
                        	<? 
								$variance_inspection_cost=$inspection_cost_mkt-$inspection_cost_actual;
								if($variance_inspection_cost>0 && $inspection_cost_mkt>0) $variance_inspection_cost_per=($variance_inspection_cost/$inspection_cost_mkt)*100; else  $variance_inspection_cost_per=""; 
								echo number_format($variance_inspection_cost,2,'.','');
							?>
                        </td>
                        <td width="98" style="padding-right:2px" align="right">
                        	<? 
								$variance_currier_cost=$currier_cost_mkt-$currier_cost_actual;
								if($variance_currier_cost>0 && $currier_cost_mkt>0) $variance_currier_cost_per=($variance_currier_cost/$currier_cost_mkt)*100; else  $variance_currier_cost_per="";
								echo number_format($variance_currier_cost,2,'.','');
							?>
                        </td>
                        
                         <td width="98" style="padding-right:2px" align="right">
                        	<? 
								$variance_all_cost=$mkt_all_cost-$actual_all_cost;
							 if($variance_all_cost>0 && $mkt_all_cost>0) 	$variance_all_cost_per=($variance_all_cost/$mkt_all_cost)*100; else  $variance_all_cost_per="";
								echo number_format($variance_all_cost,2,'.','');
							?>
                        </td>
                         <td width="98" style="padding-right:2px" align="right">
                        	<? 
						$Net_PO_ex_Fact_Value_actual=$ex_factory_value-$commission_cost_actual;
						
						$CM_Value_Contribution_actual=$Net_PO_ex_Fact_Value_actual-$actual_all_cost;
							if($CM_Value_Contribution_value_mkt<0 && $CM_Value_Contribution_actual<0)
							{
								$cm_variance_contribution_value=($CM_Value_Contribution_value_mkt)+($CM_Value_Contribution_actual);
								echo number_format($cm_variance_contribution_value,2);
							}
							else if($CM_Value_Contribution_value_mkt>0)//Is greater than 0
							{
								$cm_variance_contribution_value=($CM_Value_Contribution_actual)-($CM_Value_Contribution_value_mkt);
								echo number_format($cm_variance_contribution_value,2);
							}
							else if($CM_Value_Contribution_value_mkt<0)//Is Less than 0
							{
								$cm_variance_contribution_value=($CM_Value_Contribution_actual)+($CM_Value_Contribution_value_mkt);
								echo number_format($cm_variance_contribution_value,2);
							}
							else
							{
								$cm_variance_contribution_value=$CM_Value_Contribution_value_mkt-$CM_Value_Contribution_actual;
								echo number_format($cm_variance_contribution_value,2);
							}
							if($CM_Value_Contribution_actual>0 && $CM_Value_Contribution_actual>0) $CM_Value_Contribution_actual_per=($CM_Value_Contribution_actual/$actual_all_cost)*100; else  $variance_cm_cost_per="";
							$tot_cm_variance_contribution_value+=$cm_variance_contribution_value;
							?>
                        </td>
                        
                        <td width="98" style="padding-right:2px" align="right">
                        	<? 
								$variance_cm_cost=$cm_cost_mkt-$cm_cost_actual;
								if($variance_cm_cost>0 && $cm_cost_mkt>0) $variance_cm_cost_per=($variance_cm_cost/$cm_cost_mkt)*100; else  $variance_cm_cost_per="";
								
								echo number_format($variance_cm_cost,2,'.',''); 
							?>
                        </td>
                       
                        <td width="98" style="padding-right:2px" title="Actual-Pre Cost" align="right">
                        	<? 
								$variance_margin_cost=$actual_margin-$mkt_margin;
								 if($mkt_margin>0 && $variance_margin_cost>0) $variance_margin_cost_per=($variance_margin_cost/$mkt_margin)*100; else  $variance_margin_cost_per="";
								echo number_format($variance_margin_cost,2,'.','');
							?>
                        </td>
                        <td width="100" style="padding-right:2px" align="right">
                        	<? 
								$variance_per_cost=$mkt_margin_perc-$actual_margin_perc;
								 if($variance_per_cost>0 && $mkt_margin_perc>0)  $variance_per_cost_per=($variance_per_cost/$mkt_margin_perc)*100; else  $variance_per_cost_per="";
								echo number_format($variance_per_cost,2,'.','');
							?>
                        </td>
                    </tr>
                    <tr bgcolor="#DFDFDF" onClick="change_color('tr_vp<? echo $i; ?>','#DFDFDF')" id="tr_vp<? echo $i; ?>">
                    	<td width="100" align="left"><b>Variance (%)</b></td>
                    	<td width="98" align="right" style="padding-right:2px"><? echo number_format($variance_qnty_per,2,'.',''); ?></td>
                    	<td width="108" align="right" style="padding-right:2px"><? echo number_format($variance_po_value_per,2,'.',''); ?></td>
                        <td width="108" style="padding-right:2px" align="right"><? echo number_format($variance_finish_purchase_cost_per,2,'.',''); ?></td>
                        <td width="108" style="padding-right:2px" align="right"><? echo number_format($variance_trims_cost_per,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($variance_embell_cost_per,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($variance_wash_cost_per,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($variance_commission_cost_per,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($variance_comm_cost_per,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($variance_freight_cost_per,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($variance_test_cost_per,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($variance_inspection_cost_per,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($variance_currier_cost_per,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($variance_all_cost_per,2,'.',''); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($CM_Value_Contribution_actual_per,2,'.',''); ?></td>
                          
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($variance_cm_cost_per,2,'.',''); ?></td>
                        
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($variance_margin_cost_per,2,'.',''); ?></td>
                        <td width="100" style="padding-right:2px" align="right"><? echo number_format($variance_per_cost_per,2,'.',''); ?></td>
                    </tr>
				<?
					$i++;
				}
				
				if($tot_mkt_margin>0 && $tot_po_value>0 ) $tot_mkt_margin_perc=($tot_mkt_margin/$tot_po_value)*100;else  $tot_mkt_margin_perc="";
				if($tot_actual_margin>0 && $tot_ex_factory_val>0 ) $tot_actual_margin_perc=($tot_actual_margin/$tot_ex_factory_val)*100;else  $tot_actual_margin_perc="";
				
				?>
            </table>
		</div>
		
        <table class="rpt_table" width="3840" cellpadding="0" cellspacing="0" border="1" rules="all">
            <tr bgcolor="#CCDDEE" onClick="change_color('tr_pt','#CCDDEE')" id="tr_pt" style="font-weight:bold;">
                <td colspan="11" style="padding-right:4px" align="right">Total</td>
                <td width="100" style="padding-right:2px" align="right"><? echo number_format($tot_po_qnty,0,'.',''); ?></td>
                <td width="57">&nbsp;</td>
                <td width="79">&nbsp;</td>
                <td width="123" style="padding-right:2px" align="right"><? echo number_format($tot_po_value,2,'.',''); ?></td>
                <td width="111" style="padding-right:2px" align="right">&nbsp;</td>
                <td width="92">&nbsp;</td>
                <td width="113">&nbsp;</td>
                <td width="114">Pre Costing Total</td>
                <td width="111" style="padding-right:2px" align="right"><? echo number_format($tot_po_qnty,0,'.',''); ?></td>
                <td width="123" style="padding-right:2px" align="right"><? echo number_format($tot_po_value,2,'.',''); ?></td>                
                <td width="123" style="padding-right:2px" align="right"><? echo number_format($tot_fabric_purchase_cost_mkt,2,'.',''); ?></td>
                <td width="123" style="padding-right:2px" align="right"><? echo number_format($tot_trims_cost_mkt,2,'.',''); ?></td>
                <td width="111" style="padding-right:2px" align="right"><? echo number_format($tot_embell_cost_mkt,2,'.',''); ?></td>
                <td width="112" style="padding-right:2px" align="right"><? echo number_format($tot_wash_cost_mkt,2,'.',''); ?></td>
                <td width="111" style="padding-right:2px" align="right"><? echo number_format($tot_commission_cost_mkt,2,'.',''); ?></td>
                <td width="111" style="padding-right:2px" align="right"><? echo number_format($tot_comm_cost_mkt,2,'.',''); ?></td>
                <td width="112" style="padding-right:2px" align="right"><? echo number_format($tot_freight_cost_mkt,2,'.',''); ?></td>
                <td width="112" style="padding-right:2px" align="right"><? echo number_format($tot_test_cost_mkt,2,'.',''); ?></td>
                <td width="112" style="padding-right:2px" align="right"><? echo number_format($tot_inspection_cost_mkt,2,'.',''); ?></td>
                <td width="110" style="padding-right:2px" align="right"><? echo number_format($tot_currier_cost_mkt,2,'.',''); ?></td>
                <td width="110" style="padding-right:2px" align="right"><? echo number_format($tot_mkt_all_cost,2,'.',''); ?></td>
                <td width="110" style="padding-right:2px" align="right"><? echo number_format($tot_CM_Value_Contribution_value_mkt,2,'.',''); ?></td>
                <td width="111" style="padding-right:2px" align="right"><? echo number_format($tot_cm_cost_mkt,2,'.',''); ?></td>
                
                <td width="112" style="padding-right:2px" align="right"><? echo number_format($tot_mkt_margin,2,'.',''); ?></td>
                <td width="113" style="padding-right:2px" align="right"><? echo number_format($tot_mkt_margin_perc,2,'.',''); ?></td>
            </tr>
            <tr bgcolor="#CCCCFF" onClick="change_color('tr_at','#CCCCFF')" id="tr_at" style="font-weight:bold;">
                <td colspan="11" style="padding-right:4px" align="right"></td>
                <td  style="padding-right:2px" align="right">&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td  style="padding-right:2px" align="right">&nbsp;</td>
                <td  style="padding-right:2px" align="right">&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >Actual Total</td>
                <td  align="right" style="padding-right:2px"><? echo number_format($tot_ex_factory_qnty,0,'.',''); ?></td>
                <td  align="right" style="padding-right:2px"><? echo number_format($tot_ex_factory_val,2,'.',''); ?></td>                
                <td style="padding-right:2px" align="right"><? echo number_format($tot_fabric_purchase_cost_actual,2,'.',''); ?></td>
                <td  align="right" style="padding-right:2px"><? echo number_format($tot_trims_cost_actual,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_embell_cost_actual,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_wash_cost_actual,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_commission_cost_actual,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_comm_cost_actual,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_freight_cost_actual,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_test_cost_actual,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_inspection_cost_actual,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_currier_cost_actual,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_actual_all_cost,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_CM_Value_Contribution_value_actual,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_cm_cost_actual,2,'.',''); ?></td>
                
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_actual_margin,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_actual_margin_perc,2,'.',''); ?></td>
            </tr>
            <tr bgcolor="#FFEEFF" onClick="change_color('tr_vt','#FFEEFF')" id="tr_vt" style="font-weight:bold;">
                <td colspan="11" style="padding-right:4px" align="right"></td>
                <td  style="padding-right:2px" align="right">&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td  style="padding-right:2px" align="right">&nbsp;</td>
                <td  style="padding-right:2px" align="right">&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >Variance Total</td>
                <td  align="right" style="padding-right:2px">
					<? 
                        $tot_variance_qnty=$tot_ex_factory_qnty-$tot_po_qnty;
                        if($tot_variance_qnty>0 && $tot_po_qnty>0)   $tot_variance_qnty_per=($tot_variance_qnty/$tot_po_qnty)*100; else $tot_variance_qnty_per="";
                        echo number_format($tot_variance_qnty,0,'.',''); 
                    ?>
                </td>
                <td  align="right" style="padding-right:2px">
                    <? 
                        $tot_variance_po_value=$tot_ex_factory_val-$tot_po_value;
                       if($tot_po_value>0 && $tot_variance_po_value>0)   $tot_variance_po_value_per=($tot_variance_po_value/$tot_po_value)*100;else  $tot_variance_po_value_per=""; 
                        echo number_format($tot_variance_po_value,2,'.',''); 
                    ?>
                </td>
                
                <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_fabric_purchase_cost=$tot_fabric_purchase_cost_mkt-$tot_fabric_purchase_cost_actual;
                         if($tot_variance_fabric_purchase_cost>0 && $tot_fabric_purchase_cost_mkt>0) $tot_variance_fabric_purchase_cost_per=($tot_variance_fabric_purchase_cost/$tot_fabric_purchase_cost_mkt)*100; else $tot_variance_fabric_purchase_cost_per="";
                        echo number_format($tot_variance_fabric_purchase_cost,2,'.','');
                    ?>
                </td>
                <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_trims_cost=$tot_trims_cost_mkt-$tot_trims_cost_actual;
                       if($tot_variance_trims_cost>0 && $tot_trims_cost_mkt>0)  $tot_variance_trims_cost_per=($tot_variance_trims_cost/$tot_trims_cost_mkt)*100; else  $tot_variance_trims_cost_per="";
                        echo number_format($tot_variance_trims_cost,2,'.','');
                    ?>
                </td>
                <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_embell_cost=$tot_embell_cost_mkt-$tot_embell_cost_actual;
                       if($tot_variance_embell_cost>0 && $tot_embell_cost_mkt>0)  $tot_variance_embell_cost_per=($tot_variance_embell_cost/$tot_embell_cost_mkt)*100; else $tot_variance_embell_cost_per="";
                        echo number_format($tot_variance_embell_cost,2,'.',''); 
                    ?>
                </td>
                <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_wash_cost=$tot_wash_cost_mkt-$tot_wash_cost_actual;
                        if($tot_variance_wash_cost>0 && $tot_wash_cost_mkt>0) $tot_variance_wash_cost_per=($tot_variance_wash_cost/$tot_wash_cost_mkt)*100; else  $tot_variance_wash_cost_per="";
                        echo number_format($tot_variance_wash_cost,2,'.',''); 
                    ?>
                </td>
                <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_commission_cost=$tot_commission_cost_mkt-$tot_commission_cost_actual;
                       if($tot_variance_commission_cost>0 && $tot_commission_cost_mkt>0)  $tot_variance_commission_cost_per=($tot_variance_commission_cost/$tot_commission_cost_mkt)*100; else $tot_variance_commission_cost_per="";
                        echo number_format($tot_variance_commission_cost,2,'.',''); 
                    ?>
                </td>
                <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_comm_cost=$tot_comm_cost_mkt-$tot_comm_cost_actual;
                        if($tot_variance_comm_cost>0 && $tot_comm_cost_mkt>0)  $tot_variance_comm_cost_per=($tot_variance_comm_cost/$tot_comm_cost_mkt)*100; else $tot_variance_comm_cost_per="";
                        echo number_format($tot_variance_comm_cost,2,'.',''); 
                    ?>
                </td>
                <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_freight_cost=$tot_freight_cost_mkt-$tot_freight_cost_actual;
                       if($tot_variance_freight_cost>0 && $tot_freight_cost_mkt>0)  $tot_variance_freight_cost_per=($tot_variance_freight_cost/$tot_freight_cost_mkt)*100;else  $tot_variance_freight_cost_per="";
                        echo number_format($tot_variance_freight_cost,2,'.',''); 
                    ?>
                </td>
                <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_test_cost=$tot_test_cost_mkt-$tot_test_cost_actual;
                      if($tot_variance_test_cost>0 && $tot_test_cost_mkt>0)   $tot_variance_test_cost_per=($tot_variance_test_cost/$tot_test_cost_mkt)*100; else $tot_variance_test_cost_per="";
                        echo number_format($tot_variance_test_cost,2,'.','');
                    ?>
                </td>
                <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_inspection_cost=$tot_inspection_cost_mkt-$tot_inspection_cost_actual;
                        if($tot_variance_inspection_cost>0 && $tot_inspection_cost_mkt>0)  $tot_variance_inspection_cost_per=($tot_variance_inspection_cost/$tot_inspection_cost_mkt)*100;else $tot_variance_inspection_cost_per=""; 
                        echo number_format($tot_variance_inspection_cost,2,'.','');
                    ?>
                </td>
                <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_currier_cost=$tot_currier_cost_mkt-$tot_currier_cost_actual;
                       if($tot_variance_currier_cost>0 && $tot_currier_cost_mkt>0)  $tot_variance_currier_cost_per=($tot_variance_currier_cost/$tot_currier_cost_mkt)*100; else  $tot_variance_currier_cost_per="";
                        echo number_format($tot_variance_currier_cost,2,'.','');
                    ?>
                </td>
                
                 <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_all_cost=$tot_mkt_all_cost-$tot_actual_all_cost;
                        if($tot_variance_all_cost>0 && $tot_mkt_all_cost)  $tot_variance_all_cost_per=($tot_variance_all_cost/$tot_mkt_all_cost)*100;else $tot_variance_all_cost_per=""; 
                        echo number_format($variance_all_cost,2,'.','');
                    ?>
                </td>
                 <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_cm_value_cost=$tot_CM_Value_Contribution_value_mkt-$tot_CM_Value_Contribution_value_actual;
                      if($tot_variance_cm_value_cost>0 && $CM_Value_Contribution_value_mkt>0)  $tot_variance_cm_value_cost_per=($tot_variance_cm_value_cost/$CM_Value_Contribution_value_mkt)*100; else  $tot_variance_cm_value_cost_per="";
                        echo number_format($tot_variance_cm_value_cost,2,'.','');
						//$tot_tot_variance_cm_value_cost+=$tot_variance_cm_value_cost;
                    ?>
                </td>
                
                <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_cm_cost=$tot_cm_cost_mkt-$tot_cm_cost_actual;
                      if($tot_variance_cm_cost>0 && $tot_cm_cost_mkt>0)   $tot_variance_cm_cost_per=($tot_variance_cm_cost/$tot_cm_cost_mkt)*100; else  $tot_variance_cm_cost_per="";
                        echo number_format($tot_variance_cm_cost,2,'.',''); 
                    ?>
                </td>
              
                <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_margin_cost=$tot_mkt_margin-$tot_actual_margin;
                        if($tot_mkt_margin>0 && $tot_variance_margin_cost) $tot_variance_margin_cost_per=($tot_variance_margin_cost/$tot_mkt_margin)*100;else $tot_variance_margin_cost_per=""; 
                        echo number_format($tot_variance_margin_cost,2,'.','');
                    ?>
                </td>
                <td  style="padding-right:2px" align="right">
                    <? 
                        $tot_variance_per_cost=$tot_mkt_margin_perc-$tot_actual_margin_perc;
                       if($tot_mkt_margin_perc>0)  $tot_variance_per_cost_per=($tot_variance_per_cost/$tot_mkt_margin_perc)*100; else  $tot_variance_per_cost_per="";
                        echo number_format($tot_variance_per_cost,2,'.','');
                    ?>
                </td>
            </tr>
            <tr bgcolor="#CCCCEE" onClick="change_color('tr_vpt','#CCCCEE')" id="tr_vpt" style="font-weight:bold;">
                <td colspan="11" style="padding-right:4px" align="right"></td>
                <td  style="padding-right:2px" align="right">&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td  style="padding-right:2px" align="right">&nbsp;</td>
                <td  style="padding-right:2px" align="right">&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >Variance (%) Total</td>
                <td  align="right" style="padding-right:2px"><? echo number_format($tot_variance_qnty_per,0,'.',''); ?></td>
                <td  align="right" style="padding-right:2px"><? echo number_format($tot_variance_po_value_per,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_fabric_purchase_cost_per ,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_trims_cost_per,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_embell_cost_per,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_wash_cost_per,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_commission_cost_per,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_comm_cost_per,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_freight_cost_per,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_test_cost_per,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_inspection_cost_per,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_currier_cost_per,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_all_cost_per,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_cm_value_cost_per,2,'.',''); ?></td>
                  
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_cm_value_cost_per,2,'.',''); ?></td>
                
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_margin_cost_per,2,'.',''); ?></td>
                <td  style="padding-right:2px" align="right"><? echo number_format($tot_variance_per_cost_per,2,'.',''); ?></td>
            </tr>
        </table>
        <br />
        <table width="3370">
            <tr>
                <td width="760" valign="top">
                    <div align="center" style="width:450px" id="div_summary"><input type="button" value="Print Preview" class="formbutton" onClick="new_window2('company_div','summary_full')" /></div>
                    <br /><? $po_ids=implode(',',$po_ids_array); ?>
                    <div id="summary_full"> <font color="#FF0000" style="display:none">*Yarn Dyeing Charge included with actual Yarn Cost</font>
                        <div align="center" id="company_div" style="visibility:hidden; font-size:24px;width:700px"><b><? echo $company_arr[$company_name].'<br>'; 
						echo change_date_format(str_replace("'","",$txt_date_from)) .' To '. change_date_format(str_replace("'","",$txt_date_to));
						?></b></div>
                        <table width="760" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
                            <thead>
                            <tr>
                                <th colspan="7">Summary</th>
                            </tr>
                            <tr>
                                <th width="30">SL</th>
                                <th width="180">Particulars</th>
                                <th width="130">Pre Costing</th>
                                <th width="80">%</th>
                                <th width="130">At Actual</th>
                                <th width="80">%</th>
                                <th>Variance</th>
                            </tr> 
							<?
								$bgcolor1='#E9F3FF';
								$bgcolor2='#FFFFFF';
							?>
                            </thead>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_1','<? echo $bgcolor1; ?>')" id="trtd_1">
                                <td align="center">1</td>
                                <td>PO/Shipment Value</td>
                                <td align="right"><? echo number_format($tot_po_value,2); ?></td>
                                <td align="right">&nbsp;</td>
                                <td align="right"><? echo number_format($tot_ex_factory_val,2); ?></td>
                                <td align="right">&nbsp;</td>
                                <td align="right"><? echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <!-- <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_2','<? echo $bgcolor2; ?>')" id="trtd_2">
                                <td colspan="7"><b>Cost</b></td>
                            </tr> -->
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_3','<? echo $bgcolor1; ?>')" id="trtd_3">
                                <td align="center">2</td>
                                <td>Fabric Purchase Cost</td>
                                <td align="right"><? echo number_format($tot_fabric_purchase_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_fabric_purchase_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><a href="#report_details" title="Exchange rate=80" onClick="openmypage('<? echo $po_ids; ?>','fabric_purchase_cost','Fabric Purchase Cost Details')"><? echo number_format($tot_fabric_purchase_cost_actual,2); ?></a></td>
                                <td align="right"><? if($tot_ex_factory_val>0) echo number_format(($tot_fabric_purchase_cost_actual/$tot_ex_factory_val)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_fabric_purchase_cost_mkt-$tot_fabric_purchase_cost_actual,2); ?></td>
                            </tr>
                            <!-- <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_4','<? echo $bgcolor2; ?>')" id="trtd_4">
                                <td align="center">3</td>
                                <td>Yarn Cost+Yarn Dyeing Cost</td>
                                <td align="right"><a href="#report_details" onClick="openmypage_mkt('<? echo $tot_yarn_cost_mkt."**".$tot_yarn_dye_cost_mkt; ?>','mkt_yarn_cost','Grey And Dyed Yarn Mkt. Cost Details')"><? echo number_format($tot_yarn_cost_mkt+$tot_yarn_dye_cost_mkt,2); ?></a></td>
                                <td align="right"><? echo number_format((($tot_yarn_cost_mkt+$tot_yarn_dye_cost_mkt)/$tot_po_value)*100,2); ?></td>
                                <td align="right"><a href="#report_details" onClick="openmypage('<? echo $po_ids; ?>','yarn_cost','Yarn Cost Details')"><? echo number_format($tot_yarn_cost_actual,2); ?></a></td>
                                <td align="right"><? echo number_format(($tot_yarn_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format(($tot_yarn_cost_mkt+$tot_yarn_dye_cost_mkt)-$tot_yarn_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_5','<? echo $bgcolor2; ?>')" id="trtd_5">
                                <td align="center">4</td>
                                <td>Knitting Cost</td>
                                <td align="right"><? echo number_format($tot_knit_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_knit_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><a href="#report_details" onClick="openmypage('<? echo $po_ids; ?>','knitting_cost','Knitting Cost Details')"><? echo number_format($tot_knit_cost_actual,2); ?></a></td>
                                <td align="right"><? echo number_format(($tot_knit_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_knit_cost_mkt-$tot_knit_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>"  onclick="change_color('trtd_6','<? echo $bgcolor2; ?>')" id="trtd_6">
                                <td align="center">5</td>
                                <td>Dye & Fin Cost</td>
                                <td align="right"><? echo number_format($tot_dye_finish_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_dye_finish_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><a href="#report_details" onClick="openmypage('<? echo $po_ids; ?>','dye_fin_cost','Dye & Fin Cost Details')"><? echo number_format($tot_dye_finish_cost_actual,2); ?></a></td>
                                <td align="right"><? echo number_format(($tot_dye_finish_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_dye_finish_cost_mkt-$tot_dye_finish_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_7','<? echo $bgcolor1; ?>')" id="trtd_7">
                                <td align="center">6</td>
                                <td>AOP & Others Cost</td>
                                <td align="right"><? echo number_format($tot_aop_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_aop_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_aop_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_aop_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_aop_cost_mkt-$tot_aop_cost_actual,2); ?></td>
                            </tr> -->
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_8','<? echo $bgcolor2; ?>')" id="trtd_8">
                                <td align="center">3</td>
                                <td>Trims Cost</td>
                                <td align="right"><? echo number_format($tot_trims_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_trims_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_trims_cost_actual,2); ?></td>
                                <td align="right"><? if($tot_ex_factory_val>0) echo number_format(($tot_trims_cost_actual/$tot_ex_factory_val)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_trims_cost_mkt-$tot_trims_cost_actual,2); ?></td>
                            </tr> 
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_9','<? echo $bgcolor1; ?>')" id="trtd_9">
                                <td align="center">4</td>
                                <td>Embellishment Cost</td>
                                <td align="right"><? echo number_format($tot_embell_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_embell_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_embell_cost_actual,2); ?></td>
                                <td align="right"><? if($tot_ex_factory_val>0) echo number_format(($tot_embell_cost_actual/$tot_ex_factory_val)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_embell_cost_mkt-$tot_embell_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_10','<? echo $bgcolor2; ?>')" id="trtd_10">
                                <td align="center">5</td>
                                <td>Wash Cost</td>
                                <td align="right"><? echo number_format($tot_wash_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_wash_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_wash_cost_actual,2); ?></td>
                                <td align="right"><? if($tot_ex_factory_val>0) echo number_format(($tot_wash_cost_actual/$tot_ex_factory_val)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_wash_cost_mkt-$tot_wash_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_11','<? echo $bgcolor1; ?>')" id="trtd_11">
                                <td align="center">6</td>
                                <td>Commission Cost</td>
                                <td align="right"><? echo number_format($tot_commission_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_commission_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_commission_cost_actual,2); ?></td>
                                <td align="right"><? if($tot_ex_factory_val>0) echo number_format(($tot_commission_cost_actual/$tot_ex_factory_val)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_commission_cost_mkt-$tot_commission_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_12','<? echo $bgcolor2; ?>')" id="trtd_12">
                                <td align="center">7</td>
                                <td>Commercial Cost</td>
                                <td align="right"><? echo number_format($tot_comm_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_comm_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_comm_cost_actual,2); ?></td>
                                <td align="right"><? if($tot_ex_factory_val>0) echo number_format(($tot_comm_cost_actual/$tot_ex_factory_val)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_comm_cost_mkt-$tot_comm_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_13','<? echo $bgcolor1; ?>')" id="trtd_13">
                                <td align="center">8</td>
                                <td>Freight Cost</td>
                                <td align="right"><? echo number_format($tot_freight_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_freight_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_freight_cost_actual,2); ?></td>
                                <td align="right"><? if($tot_ex_factory_val>0) echo number_format(($tot_freight_cost_actual/$tot_ex_factory_val)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_freight_cost_mkt-$tot_freight_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>"  onclick="change_color('trtd_14','<? echo $bgcolor2; ?>')" id="trtd_14">
                                <td align="center">9</td>
                                <td>Testing Cost</td>
                                <td align="right"><? echo number_format($tot_test_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_test_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_test_cost_actual,2); ?></td>
                                <td align="right"><? if($tot_ex_factory_val>0) echo number_format(($tot_test_cost_actual/$tot_ex_factory_val)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_test_cost_mkt-$tot_test_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_15','<? echo $bgcolor1; ?>')" id="trtd_15">
                                <td align="center">10</td>
                                <td>Inspection Cost</td>
                                <td align="right"><? echo number_format($tot_inspection_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_inspection_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_inspection_cost_actual,2); ?></td>
                                <td align="right"><? if($tot_ex_factory_val>0) echo number_format(($tot_inspection_cost_actual/$tot_ex_factory_val)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_inspection_cost_mkt-$tot_inspection_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_16','<? echo $bgcolor2; ?>')" id="trtd_16">
                                <td align="center">11</td>
                                <td>Courier Cost</td>
                                <td align="right"><? echo number_format($tot_currier_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_currier_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_currier_cost_actual,2); ?></td>
                                <td align="right"><? if($tot_ex_factory_val>0) echo number_format(($tot_currier_cost_actual/$tot_ex_factory_val)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_currier_cost_mkt-$tot_currier_cost_actual,2); ?></td>
                            </tr>
                             <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_18','<? echo $bgcolor2; ?>')" id="trtd_18">
                                <td align="center">13</td>
                                <td>Tot Mat. Serv. Cost</td>
                                <td align="right"><? echo number_format($tot_mkt_all_cost,2); ?></td>
                                <td align="right"><? echo number_format(($tot_mkt_all_cost/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_actual_all_cost,2); ?></td>
                                <td align="right"><? if($tot_ex_factory_val>0) echo number_format(($tot_actual_all_cost/$tot_ex_factory_val)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_mkt_all_cost-$tot_actual_all_cost,2); ?></td>
                            </tr>
                            
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_17','<? echo $bgcolor1; ?>')" id="trtd_17">
                                <td align="center">12</td>
                                <td>CM Value(Contribution)</td>
                                <td align="right"><? echo number_format($tot_CM_Value_Contribution_value_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_CM_Value_Contribution_value_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_CM_Value_Contribution_actual,2); ?></td>
                                <td align="right"><? if($tot_ex_factory_val>0) echo number_format(($tot_CM_Value_Contribution_actual/$tot_ex_factory_val)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_CM_Value_Contribution_value_mkt-$tot_CM_Value_Contribution_actual,2); ?></td>
                            </tr>
                              <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_17','<? echo $bgcolor1; ?>')" id="trtd_17">
                                <td align="center">12</td>
                                <td>CM</td>
                                <td align="right"><? echo number_format($tot_cm_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_cm_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_cm_cost_actual,2); ?></td>
                                <td align="right"><? if($tot_ex_factory_val>0) echo number_format(($tot_cm_cost_actual/$tot_ex_factory_val)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_cm_cost_mkt-$tot_cm_cost_actual,2); ?></td>
                            </tr>
                           
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_19','<? echo $bgcolor1; ?>')" id="trtd_19">
                                <td align="center">14</td>
                                <td>Margin/Loss</td>
                                <td align="right"><? echo number_format($tot_mkt_margin,2); ?></td>
                                <td align="right"><? echo number_format(($tot_mkt_margin/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_actual_margin,2); ?></td>
                                <td align="right"><? if($tot_ex_factory_val>0) echo number_format(($tot_actual_margin/$tot_ex_factory_val)*100,2);else echo ""; ?></td>
                                <td align="right"><? echo number_format($tot_mkt_margin-$tot_actual_margin,2); ?></td>
                            </tr>
                        </table>
                    </div>
                </td>
                <td width="525" align="center" valign="top">
                    <div align="center" style="width:500px; height:53px" id="graph">&nbsp;</div>
                    <fieldset style="text-align:center; width:450px" > 
                    	<legend>Chart</legend>
                    </fieldset>
                </td>   
                <td width="" valign="top">
                    <div align="center" style="width:600px" id="div_buyer"><input type="button" value="Print Preview" class="formbutton" onClick="new_window2('company_div_b','summary_buyer')" /></div>
                    <br />
                    <div id="summary_buyer">
                        <div align="center" id="company_div_b" style="visibility:hidden; font-size:24px;width:1105px"><b><? echo $company_arr[$company_name].'<br>';echo change_date_format(str_replace("'","",$txt_date_from)) .' To '. change_date_format(str_replace("'","",$txt_date_to)); ?></b></div>
                        <table width="100%" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
                            <thead>
                                 <tr>
                                    <th colspan="22">Buyer Level Summary</th>
                                </tr>
                                <tr>
                                    <th width="35">SL</th>
                                    <th width="70">Buyer name</th>
                                    <th width="110">Cost Source</th>
                                    <th width="110">Ex-Factory Value</th>
                                    <th width="110">Fabric Purchase Cost</th>
                                    <th width="110">Trims Cost</th>
                                    <th width="110">Embellishment Cost</th>
                                    <th width="110">Wash Cost</th>
                                    <th width="110">Commission Cost</th>
                                    <th width="100">Commercial Cost</th>
                                    <th width="110">Freight Cost</th>
                                    <th width="100">Testing Cost</th>
                                    <th width="100">Inspection Cost</th>
                                    <th width="100">Courier Cost</th>
                                    <th width="110">CM Cost</th>
                                    <th width="110">Toal Cost</th>
                                    <th width="110">Margin</th>
                                    <th width="90">Margin %</th>
                                </tr>
                            </thead>
                            <?
                                $j=1;
                                foreach($buyer_name_array as $key=>$value)
                                {
                                    if ($j%2==0)  
                                        $bgcolor="#E9F3FF";
                                    else
                                        $bgcolor="#FFFFFF";	
                                ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trTD_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trTD_<? echo $j; ?>">
                                        <td rowspan="4"><? echo $j ;?></td>
                                        <td rowspan="4"><? echo $value ;?></td>
                                        <td><b>Pre Costing</b></td>
                                        <td align="right"><? echo number_format($mkt_po_val_array[$key],2); $tot_mkt_po_val+=$mkt_po_val_array[$key];  ?></td>
                                        <td align="right"><? echo number_format($mkt_fabric_purchase_array[$key],2); $tot_mkt_fin_pur_cost+=$mkt_fabric_purchase_array[$key];?></td>
                                        <td align="right"><? echo number_format($mkt_trims_array[$key],2); $tot_mkt_trims_cost+=$mkt_trims_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_emb_array[$key],2); $tot_mkt_emb_cost+=$mkt_emb_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_wash_array[$key],2); $tot_mkt_wash_cost+=$mkt_wash_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_commn_array[$key],2); $tot_mkt_commn_cost+=$mkt_commn_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_commercial_array[$key],2); $tot_mkt_commercial_cost+=$mkt_commercial_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_freight_array[$key],2); $tot_mkt_freight_cost+=$mkt_freight_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_test_array[$key],2); $tot_mkt_test_cost+=$mkt_test_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_ins_array[$key],2); $tot_mkt_ins_cost+=$mkt_ins_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_courier_array[$key],2); $tot_mkt_courier_cost+=$mkt_courier_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_cm_array[$key],2); $tot_mkt_cm_cost+=$mkt_cm_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_total_array[$key],2); $tot_mkt_total_cost+=$mkt_total_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($mkt_margin_array[$key],2); $tot_mkt_margin_cost+=$mkt_margin_array[$key]; ?></td>
                                        <td align="right"><? if($mkt_po_val_array[$key]>0) $mkt_margin_perc=($mkt_margin_array[$key]/$mkt_po_val_array[$key])*100;else $mkt_margin_perc="";
										 if($mkt_po_val_array[$key]>0) echo number_format($mkt_margin_perc,2);else echo ""; $tot_mkt_margin_perc_cost+=$mkt_margin_perc; ?></td> 
                                    </tr>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trTD2_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trTD2_<? echo $j; ?>">
                                        <td><b>Actual</b></td>
                                        <td align="right"><? echo number_format($ex_factory_val_array[$key],2); $tot_buyer_ex_factory_val+=$ex_factory_val_array[$key];  ?></td>                                        
                                        <td align="right"><? echo number_format($actual_fabric_purchase_array[$key],2); $tot_buyer_fin_pur_act_cost+=$actual_fabric_purchase_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($trims_cost_array[$key],2); $tot_buyer_trims_cost+=$trims_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($enbellishment_cost_array[$key],2); $tot_buyer_embell_cost+=$enbellishment_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($wash_cost_array[$key],2); $tot_buyer_wash_cost+=$wash_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($commission_cost_array[$key],2); $tot_buyer_commi_cost+=$commission_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($commercial_cost_array[$key],2); $tot_buyer_commercial_cost+=$commercial_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($freight_cost_array[$key],2); $tot_buyer_freight_cost+=$freight_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($testing_cost_array[$key],2); $tot_buyer_testing_cost+=$testing_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($inspection_cost_array[$key],2); $tot_buyer_inspection_cost+=$inspection_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($courier_cost_array[$key],2); $tot_buyer_courier_cost+=$courier_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($cm_cost_array[$key],2); $tot_buyer_cm_cost+=$cm_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($total_cost_array[$key],2); $tot_buyer_total_cost+=$total_cost_array[$key]; ?></td>
                                        <td align="right"><? echo number_format($margin_array[$key],2); $tot_buyer_margin_cost+=$margin_array[$key]; ?></td>
                                        <td align="right"><?  if($ex_factory_val_array[$key]>0) $margin_perc=($margin_array[$key]/$ex_factory_val_array[$key])*100; else  $margin_perc="";
										 if($ex_factory_val_array[$key]>0) echo number_format($margin_perc,2);else echo ""; $tot_buyer_margin_perc_cost+=$margin_perc; ?></td> 
                                    </tr>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trTD3_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trTD3_<? echo $j; ?>">
                                        <td><b>Variance</b></td>
                                        <td align="right">
                                        <?
                                            $ex_var= $ex_factory_val_array[$key]-$mkt_po_val_array[$key];
                                            echo number_format($ex_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $fin_pur_var= $mkt_fabric_purchase_array[$key]-$actual_fabric_purchase_array[$key];
                                            echo number_format($fin_pur_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $trims_var= $mkt_trims_array[$key]-$trims_cost_array[$key];
                                            echo number_format($trims_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $emb_var= $mkt_emb_array[$key]-$enbellishment_cost_array[$key];
                                            echo number_format($emb_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $wash_var= $mkt_wash_array[$key]-$wash_cost_array[$key];
                                            echo number_format($wash_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $com_var= $mkt_commn_array[$key]-$commission_cost_array[$key];
                                            echo number_format($com_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $commer_var= $mkt_commercial_array[$key]-$commercial_cost_array[$key];
                                            echo number_format($commer_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $fr_var= $mkt_freight_array[$key]-$freight_cost_array[$key];
                                            echo number_format($fr_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $test_var= $mkt_test_array[$key]-$testing_cost_array[$key];
                                            echo number_format($test_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $ins_var= $mkt_ins_array[$key]-$inspection_cost_array[$key];
                                            echo number_format($ins_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $cour_var= $mkt_courier_array[$key]-$courier_cost_array[$key];
                                            echo number_format($cour_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $cm_var= $mkt_cm_array[$key]-$cm_cost_array[$key];
                                            echo number_format($cm_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $tot_var= $mkt_total_array[$key]-$total_cost_array[$key];
                                            echo number_format($tot_var,2); 
                                        ?>
                                        </td>
                                        <td align="right" title="Actual-Pre Cost">
                                        <?
                                            $mar_var= $margin_array[$key]-$mkt_margin_array[$key];
                                            echo number_format($mar_var,2); 
                                        ?>
                                        </td>
                                        <td align="right">
                                        <?
                                            $margin_perc_to= $mkt_margin_perc-$margin_perc;
                                            echo number_format($margin_perc_to,2); 
                                        ?>
                                        </td>
                                    </tr>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trTD4_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trTD4_<? echo $j; ?>">
                                        <td><b>Variance (%)</b></td>
                                        <td align="right"><? if($mkt_po_val_array[$key]>0) echo number_format(($ex_var/$mkt_po_val_array[$key]*100),2);else echo ""; ?></td>
                                        <td align="right"><? if($mkt_fabric_purchase_array[$key]>0) echo number_format(($fin_pur_var/$mkt_fabric_purchase_array[$key]*100),2);else echo ""; ?></td>
                                        <td align="right"><? if($mkt_trims_array[$key]>0) echo number_format(($trims_var/$mkt_trims_array[$key]*100),2);else echo ""; ?></td>
                                        <td align="right"><? if($mkt_emb_array[$key]>0) echo number_format(($emb_var/$mkt_emb_array[$key]*100),2);else echo ""; ?></td>
                                        <td align="right"><? if($mkt_wash_array[$key]>0) echo number_format(($wash_var/$mkt_wash_array[$key]*100),2);else echo ""; ?></td>
                                        <td align="right"><? if($mkt_commn_array[$key]>0) echo number_format(($com_var/$mkt_commn_array[$key]*100),2); else echo "";?></td>
                                        <td align="right"><? if($mkt_commercial_array[$key]>0) echo number_format(($commer_var/$mkt_commercial_array[$key]*100),2);else echo ""; ?></td>
                                        <td align="right"><? if($mkt_freight_array[$key]>0) echo number_format(($fr_var/$mkt_freight_array[$key]*100),2);else echo ""; ?></td>
                                        <td align="right"><?  if($mkt_test_array[$key]>0) echo number_format(($test_var/$mkt_test_array[$key]*100),2);else echo ""; ?></td>
                                        <td align="right"><? if($mkt_ins_array[$key]>0) echo number_format(($ins_var/$mkt_ins_array[$key]*100),2);else echo ""; ?></td>
                                        <td align="right"><? if($mkt_courier_array[$key]>0) echo number_format(($cour_var/$mkt_courier_array[$key]*100),2);else echo ""; ?></td>
                                        <td align="right"><? if($mkt_cm_array[$key]>0) echo number_format(($cm_var/$mkt_cm_array[$key]*100),2);else echo ""; ?></td>
                                        <td align="right"><? if($mkt_total_array[$key]>0) echo number_format(($tot_var/$mkt_total_array[$key]*100),2);else echo ""; ?></td>
                                        <td align="right"><? if($mkt_margin_array[$key]>0) echo number_format(($mar_var/$mkt_margin_array[$key]*100),2);else echo ""; ?></td>
                                        <td align="right">  <? if($mkt_margin_perc>0) echo number_format(($margin_perc_to/$mkt_margin_perc*100),2);else echo ""; ?></td> 
                                    </tr>
                           	<?
                                $j++;
                                }
								
								$bgcolor5='#CCDDEE';
								$bgcolor6='#CCCCFF';
								$bgcolor7='#FFEEFF';
                            ?>
                            <tr bgcolor="<? echo $bgcolor5; ?>" onClick="change_color('trTD5_<? echo $j; ?>','<? echo $bgcolor5; ?>')" id="trTD5_<? echo $j; ?>">
                                <th colspan="3">Pre Costing</th>
                                <th align="right"><? echo number_format($tot_mkt_po_val,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_fin_pur_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_trims_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_emb_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_wash_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_commn_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_commercial_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_freight_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_test_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_ins_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_courier_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_cm_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_total_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_mkt_margin_cost,2); ?></th>
                                <th align="right"><? if($tot_mkt_po_val) $mm=$tot_mkt_margin_cost/$tot_mkt_po_val*100; $mm="";
								if($tot_mkt_po_val>0)  echo number_format($mm,2);else echo ""; ?></th>   
                            </tr>
                            <tr bgcolor="<? echo $bgcolor6; ?>" onClick="change_color('trTD6_<? echo $j; ?>','<? echo $bgcolor6; ?>')" id="trTD6_<? echo $j; ?>">
                                <th colspan="3">Actual</th>
                                <th align="right"><? echo number_format($tot_buyer_ex_factory_val,2); ?></th>                                
                                <th align="right"><? echo number_format($tot_buyer_fin_pur_act_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_trims_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_embell_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_wash_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_commi_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_commercial_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_freight_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_testing_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_inspection_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_courier_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_cm_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_total_cost,2); ?></th>
                                <th align="right"><? echo number_format($tot_buyer_margin_cost,2); ?></th>
                                <th align="right"><? 
								if($tot_buyer_ex_factory_val) $pp=$tot_buyer_margin_cost/$tot_buyer_ex_factory_val*100;else $pp=""; 
								 
								 if($tot_buyer_ex_factory_val>0) echo number_format($pp,2);else echo ""; ?></th>   
                            </tr>
                            <tr bgcolor="<? echo $bgcolor7; ?>" onClick="change_color('trTD7_<? echo $j; ?>','<? echo $bgcolor7; ?>')" id="trTD7_<? echo $j; ?>">
                                <th colspan="3">Variance</th>
                                <th align="right"><? $evar=$tot_buyer_ex_factory_val-$tot_mkt_po_val;  echo number_format($evar,2); ?></th>
                                <th align="right"><? $fpfvar=$tot_mkt_fin_pur_cost-$tot_buyer_fin_pur_act_cost;  echo number_format($fpfvar,2); ?></th>
                                <th align="right"><? $trimvar=$tot_mkt_trims_cost-$tot_buyer_trims_cost;  echo number_format($trimvar,2); ?></th>
                                <th align="right"><? $embvar=$tot_mkt_emb_cost-$tot_buyer_embell_cost;  echo number_format($embvar,2); ?></th>
                                <th align="right"><? $washvar=$tot_mkt_wash_cost-$tot_buyer_wash_cost;  echo number_format($washvar,2); ?></th>
                                <th align="right"><? $comvar=$tot_mkt_commn_cost-$tot_buyer_commi_cost;  echo number_format($comvar,2); ?></th>
                                <th align="right"><? $commercialvar=$tot_mkt_commercial_cost-$tot_buyer_commercial_cost;  echo number_format($commercialvar,2); ?></th>
                                <th align="right"><? $fvar=$tot_mkt_freight_cost-$tot_buyer_freight_cost;  echo number_format($fvar,2); ?></th>
                                <th align="right"><? $tvar=$tot_mkt_test_cost-$tot_buyer_testing_cost;  echo number_format($tvar,2); ?></th>
                                <th align="right"><? $ivar=$tot_mkt_ins_cost-$tot_buyer_inspection_cost;  echo number_format($ivar,2); ?></th>
                                <th align="right"><? $courvar=$tot_mkt_courier_cost-$tot_buyer_courier_cost;  echo number_format($courvar,2); ?></th>
                                <th align="right"><? $cmvar=$tot_mkt_cm_cost-$tot_buyer_cm_cost;  echo number_format($cmvar,2); ?></th>
                                <th align="right"><? $totvar=$tot_mkt_total_cost-$tot_buyer_total_cost;  echo number_format($totvar,2); ?></th>
                                <th align="right"><? $mvar=$tot_mkt_margin_cost-$tot_buyer_margin_cost;  echo number_format($mvar,2); ?></th>
                                <th align="right"><? $mpvar=$mm-$pp;  echo number_format($mpvar,2); ?></th>   
                            </tr>
                            <tr bgcolor="<? echo $bgcolor6; ?>" onClick="change_color('trTD8_<? echo $j; ?>','<? echo $bgcolor6; ?>')" id="trTD8_<? echo $j; ?>">
                                <th colspan="3">Variance (%)</th>
                                <th align="right"><?  if($tot_mkt_po_val>0) echo number_format(($evar/$tot_mkt_po_val*100),2);else echo ""; ?></th>                                
                                <th align="right"><?  if($tot_mkt_fin_pur_cost>0) echo number_format(($fpfvar/$tot_mkt_fin_pur_cost*100),2);else echo ""; ?></th>
                                <th align="right"><?  if($tot_mkt_trims_cost>0) echo number_format(($trimvar/$tot_mkt_trims_cost*100),2);else echo ""; ?></th>
                                <th align="right"><?  if($tot_mkt_emb_cost>0) echo number_format(($embvar/$tot_mkt_emb_cost*100),2);else echo ""; ?></th>
                                <th align="right"><?  if($tot_mkt_wash_cost>0) echo number_format(($washvar/$tot_mkt_wash_cost*100),2);else echo ""; ?></th>
                                <th align="right"><?  if($tot_mkt_commn_cost>0) echo number_format(($comvar/$tot_mkt_commn_cost*100),2);else echo ""; ?></th>
                                <th align="right"><?  if($tot_mkt_commercial_cost>0) echo number_format(($commercialvar/$tot_mkt_commercial_cost*100),2);else echo ""; ?></th>
                                <th align="right"><? if($tot_mkt_freight_cost>0)  echo number_format(($fvar/$tot_mkt_freight_cost*100),2);else echo ""; ?></th>
                                <th align="right"><?  if($tot_mkt_test_cost>0) echo number_format(($tvar/$tot_mkt_test_cost*100),2);else echo ""; ?></th>
                                <th align="right"><? if($tot_mkt_ins_cost>0)  echo number_format(($ivar/$tot_mkt_ins_cost*100),2);else echo ""; ?></th>
                                <th align="right"><?  if($tot_mkt_courier_cost>0) echo number_format(($courvar/$tot_mkt_courier_cost*100),2);else echo ""; ?></th>
                                <th align="right"><?  if($tot_mkt_cm_cost>0) echo number_format(($cmvar/$tot_mkt_cm_cost*100),2);else echo ""; ?></th>
                                <th align="right"><? if($tot_mkt_total_cost>0) echo number_format(($totvar/$tot_mkt_total_cost*100),2);else echo ""; ?></th>
                                <th align="right"><? if($tot_mkt_margin_cost>0) echo number_format(($mvar/$tot_mkt_margin_cost*100),2);else echo ""; ?></th>
                                <th align="right" title="<?=$mm.'='.$mpvar;?>"><?  if($mm>0 && $mpvar>0) echo number_format(($mpvar/$mm*100),2);else echo ""; ?></th>   
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
	</fieldset>
 <?
	/*foreach (glob("../../../../ext_resource/tmp_report/$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$html=ob_get_contents();
	$name=time();
	$filename="../../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	$filename="../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";*/
	$html = ob_get_contents();
		ob_clean();
		//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
		foreach (glob("*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_name."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');	
		$is_created = fwrite($create_new_doc, $html);
		echo "$html****$filename****$report_type"; 
	//echo "$total_data****$filename";
	exit();
}


if($action=="report_generate_actual_cost")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	//echo $cbo_order_status;die;
	
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$team_leader_arr=return_library_array( "select id, team_leader_name from lib_marketing_team", "id", "team_leader_name"  );
	
	$company_name=str_replace("'","",$cbo_company_name);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$cbo_status=str_replace("'","",$cbo_status);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$cbo_brand_id=str_replace("'","",$cbo_brand_id);
	$cbo_season_year=str_replace("'","",$cbo_season_year);
	$txt_season_id=str_replace("'","",$txt_season_id);
	if($cbo_brand_id>0) $brand_cond="and a.brand_id in($cbo_brand_id)";else  $brand_cond="";
	//if($txt_season_id>0) $season_cond="and a.season in($txt_season_id)";else  $season_cond="";
	if($txt_season_id>0) $season_cond="and a.season_buyer_wise in($txt_season_id)";else  $season_cond="";
	//echo $season_cond.'DDDD';;
	if($cbo_season_year>0) $season_year_cond="and a.season_year=$cbo_season_year";else  $season_year_cond="";
	
	if($txt_ref_no!='') $ref_cond="and b.grouping='$txt_ref_no'";else $ref_cond="";
	if($txt_file_no!='') $file_cond="and b.file_no=$txt_file_no";else $file_cond="";
	
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
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	$cbo_status_cond='';
	if($cbo_status!=0)
	{
		$cbo_status_cond=" and b.is_confirmed in($cbo_status)";
	}
	$txt_job_no=str_replace("'","",$txt_job_no);
	if(trim($txt_job_no)!="") $job_no=trim($txt_job_no); else $job_no="%%";
	
	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		else $year_cond="";
	}
	else $year_cond="";
	
	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$date_cond=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
	}
	
	$po_id_cond="";
	if(trim(str_replace("'","",$txt_order_no))!="")
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id_cond=" and b.id in(".str_replace("'","",$hide_order_id).")";
		}
		else
		{
			$po_id_cond=" and LOWER(b.po_number) like LOWER('%".trim(str_replace("'","",$txt_order_no))."%')";
		}
	}
	
	$shipping_status_cond='';
	if(str_replace("'","",$shipping_status)!=0)
	{
		$shipping_status_cond=" and b.shiping_status=$shipping_status";
	}
	
	$exchange_rate=76; $po_ids_array=array();
	
	
		
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
		
	$sql="select a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name,a.team_leader,a.season_buyer_wise as season, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, a.set_smv, b.id, b.po_number,b.grouping,b.file_no, b.pub_shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.job_no=b.job_no_mst and c.garments_nature=3 and a.company_name='$company_name' and a.job_no_prefix_num like '$job_no' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=$cbo_order_status $date_cond $brand_cond $season_cond $season_year_cond $buyer_id_cond $po_id_cond $ref_cond $file_cond $shipping_status_cond $year_cond $cbo_status_cond order by b.pub_shipment_date, a.job_no_prefix_num, b.id";
	//echo $sql; //die;
	$result=sql_select($sql);
	$all_po_id="";  $all_jobs="";
	foreach($result as $row)
	{
		if($all_po_id=="") $all_po_id=$row[csf("id")]; else $all_po_id.=",".$row[csf("id")];
		if($all_jobs=="") $all_jobs="'".$row[csf("job_no")]."'"; else $all_jobs.=",'".$row[csf("job_no")]."'";
	}
	$all_jobs=implode(",",array_unique(explode(",",$all_jobs)));
	
	$poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2="";$po_cond_for_in3=""; 
	$po_ids=count(array_unique(explode(",",$all_po_id)));
		if($db_type==2 && $po_ids>1000)
		{
			$po_cond_for_in=" and (";
			$po_cond_for_in2=" and (";
			$po_cond_for_in3=" and (";
			$po_cond_for_in4=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$po_cond_for_in.=" b.po_break_down_id in($ids) or"; 
				$po_cond_for_in2.=" po_id in($ids) or"; 
				$po_cond_for_in3.=" b.order_id in($ids) or";
				$po_cond_for_in4.=" b.po_breakdown_id in($ids) or"; 
			}
			$po_cond_for_in=chop($po_cond_for_in,'or ');
			$po_cond_for_in.=")";
			
			$po_cond_for_in2=chop($po_cond_for_in2,'or ');
			$po_cond_for_in2.=")";
			$po_cond_for_in3=chop($po_cond_for_in3,'or ');
			$po_cond_for_in3.=")";
			$po_cond_for_in4=chop($po_cond_for_in4,'or ');
			$po_cond_for_in4.=")";
		}
		else
		{
			$poIds=implode(",",array_unique(explode(",",$all_po_id)));
			if($poIds==""){$po_cond_for_in="";}else{$po_cond_for_in=" and b.po_break_down_id in($poIds)";}
			if($poIds==""){$po_cond_for_in2="";}else{$po_cond_for_in2=" and po_id in($poIds)";}
			if($poIds==""){$po_cond_for_in3="";}else{$po_cond_for_in3=" and b.order_id in($poIds)";}
			if($poIds==""){$po_cond_for_in4="";}else{$po_cond_for_in4=" and b.po_breakdown_id in($poIds)";}
			
			/*$po_cond_for_in=" and b.po_break_down_id in($poIds)";
			$po_cond_for_in2=" and po_id in($poIds)";
			$po_cond_for_in3=" and b.order_id in($poIds)";
			$po_cond_for_in4=" and b.po_breakdown_id in($poIds)";*/
		}
		
		$ex_factory_arr=return_library_array( "select b.po_break_down_id, 
	sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as qnty 
	from pro_ex_factory_mst b where b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_break_down_id", "po_break_down_id", "qnty");	
	
	
	ob_start();
	?>
    <fieldset>
    	<table width="3690">
            <tr class="form_caption">
                <td colspan="26" align="center"><strong>Post Costing Report</strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="26" align="center"><strong><? echo $company_arr[$company_name];?></strong>
                    <br>
                    <strong><? echo change_date_format(str_replace("'","",$txt_date_from)) .' To '. change_date_format(str_replace("'","",$txt_date_to)); ?></strong>
                </td>
            </tr>
        </table>
        <table style="margin-top:10px" id="table_header_1" class="rpt_table" width="3690" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="70">Buyer</th>
				<th width="100">Team Leader</th>
                <th width="70">Job No</th>
                <th width="60">Job Year</th>
                <th width="110">Style Name</th>
                <th width="120">Garments Item</th>
                <th width="100">PO No</th>
                <th width="90">PO Quantity(Pcs)</th>
                <th width="70">Unit Price</th>
                <th width="110">PO Value</th>
                <th width="100">SMV</th>
                <th width="80">Shipment Date</th>
                <th width="100">Shipping Status</th>
                <th width="100">Cost Source</th>
                <th width="100">PO/Ex-Factory Qnty</th>
                <th width="110">PO/Ex-Factory Value</th>
                <th width="110">Fabric Purchase Cost</th>
                <th width="110">Trims Cost</th>
                <th width="100">Embellishment Cost</th>
                <th width="100">Wash Cost</th>
                <th width="100">Commercial Cost</th>
                <th width="100">Freight Cost</th>
                <th width="100">Testing Cost</th>
                <th width="100">Inspection Cost</th>
                <th width="100">Courier Cost</th>
				<th width="100">Total Mat & Serv.Cost</th>
                <th width="100">Commission Cost</th>
				<th width="100">Net P.O/Ex-Fact. Value</th>                
				<th width="100">CM Value (Contribution)</th> 
                <th width="100">CM Cost</th>
                <th width="100">Margin</th>
                <th width="100">% to Ex-Factory Value</th>
				<th width="58">Company</th>
            </thead>
        </table>
    	<div style="width:3712px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="3690" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
            <?
				$fabriccostArray=array(); $yarncostArray=array(); $trimsCostArray=array(); $prodcostArray=array(); $actualCostArray=array(); $actualTrimsCostArray=array(); 
				$subconCostArray=array(); $embellCostArray=array(); $washCostArray=array(); $aopCostArray=array(); $yarnTrimsCostArray=array(); 
				$yarncostDataArray=sql_select("select job_no, sum(amount) as amnt, sum(rate*avg_cons_qnty) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 group by job_no");
				foreach($yarncostDataArray as $yarnRow)
				{
				   $yarncostArray[$yarnRow[csf('job_no')]]=$yarnRow[csf('amount')];
				}
				
				$fabriccostDataArray=sql_select("select job_no, costing_per_id, embel_cost, wash_cost, cm_cost, commission, currier_pre_cost, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where status_active=1 and is_deleted=0");
				foreach($fabriccostDataArray as $fabRow)
				{
					 $fabriccostArray[$fabRow[csf('job_no')]]['costing_per_id']=$fabRow[csf('costing_per_id')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['embel_cost']=$fabRow[csf('embel_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['wash_cost']=$fabRow[csf('wash_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['cm_cost']=$fabRow[csf('cm_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['commission']=$fabRow[csf('commission')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['currier_pre_cost']=$fabRow[csf('currier_pre_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['lab_test']=$fabRow[csf('lab_test')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['inspection']=$fabRow[csf('inspection')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['freight']=$fabRow[csf('freight')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['comm_cost']=$fabRow[csf('comm_cost')];
				}
				
				$trimscostDataArray=sql_select("select b.po_break_down_id, sum(b.cons*a.rate) as total from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id and a.status_active=1 and a.is_deleted=0 group by b.po_break_down_id");
				foreach($trimscostDataArray as $trimsRow)
				{
					 $trimsCostArray[$trimsRow[csf('po_break_down_id')]]=$trimsRow[csf('total')];
				}
				 
				$prodcostDataArray=sql_select("select job_no, 
									  sum(CASE WHEN cons_process=1 THEN amount END) AS knit_charge,
									  sum(CASE WHEN cons_process=30 THEN amount END) AS yarn_dye_charge,
									  sum(CASE WHEN cons_process=35 THEN amount END) AS aop_charge,
									  sum(CASE WHEN cons_process not in(1,2,30,35) THEN amount END) AS dye_finish_charge
									  from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0 group by job_no");
				foreach($prodcostDataArray as $prodRow)
				{
					$prodcostArray[$prodRow[csf('job_no')]]['knit_charge']=$prodRow[csf('knit_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['yarn_dye_charge']=$prodRow[csf('yarn_dye_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['aop_charge']=$prodRow[csf('aop_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['dye_finish_charge']=$prodRow[csf('dye_finish_charge')];
				}	
				
				$actualCostDataArray=sql_select("select cost_head,po_id,sum(amount_usd) as amount_usd from wo_actual_cost_entry where company_id=$company_name and status_active=1 and is_deleted=0 $po_cond_for_in2 group by cost_head,po_id");
				foreach($actualCostDataArray as $actualRow)
				{
				   $actualCostArray[$actualRow[csf('cost_head')]][$actualRow[csf('po_id')]]=$actualRow[csf('amount_usd')];
				}
				
				$subconInBillDataArray=sql_select("select b.order_id, 
									  sum(CASE WHEN a.process_id=2 THEN b.amount END) AS knit_bill,
									  sum(CASE WHEN a.process_id=4 THEN b.amount END) AS dye_finish_bill
									  from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id in(2,4) $po_cond_for_in3  group by b.order_id");
				foreach($subconInBillDataArray as $subRow)
				{
					$subconCostArray[$subRow[csf('order_id')]]['knit_bill']=$subRow[csf('knit_bill')];
					$subconCostArray[$subRow[csf('order_id')]]['dye_finish_bill']=$subRow[csf('dye_finish_bill')];
				}	
				
				$subconOutBillDataArray=sql_select("select b.order_id, 
									  sum(CASE WHEN a.process_id=2 THEN b.amount END) AS knit_bill,
									  sum(CASE WHEN a.process_id=4 THEN b.amount END) AS dye_finish_bill
									  from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.process_id in(2,4) $po_cond_for_in3 group by b.order_id");
				foreach($subconOutBillDataArray as $subRow)
				{
					$subconCostArray[$subRow[csf('order_id')]]['knit_bill']+=$subRow[csf('knit_bill')];
					$subconCostArray[$subRow[csf('order_id')]]['dye_finish_bill']+=$subRow[csf('dye_finish_bill')];
				}
				
				$embell_type_arr=return_library_array( "select id, emb_name from wo_pre_cost_embe_cost_dtls", "id", "emb_name");	
				
				$bookingDataArray=sql_select("select a.booking_type, a.item_category, a.currency_id, a.exchange_rate, b.po_break_down_id, b.process, b.amount, b.pre_cost_fabric_cost_dtls_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.item_category in(4,12,25) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in");
				foreach($bookingDataArray as $woRow)
				{
					$amount=0; $trimsAmnt=0;
					if($woRow[csf('currency_id')]==1) { $amount=$woRow[csf('amount')]/$exchange_rate; } else { $amount=$woRow[csf('amount')]; }
					
					if($woRow[csf('item_category')]==25 && ($woRow[csf('booking_type')]==3 || $woRow[csf('booking_type')]==6)) 
					{ 
						if($embell_type_arr[$woRow[csf('pre_cost_fabric_cost_dtls_id')]]==3)
						{
							$washCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
						}
						else
						{
							$embellCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
						}
					}
					else if($woRow[csf('item_category')]==12 && $woRow[csf('process')]==35 && ($woRow[csf('booking_type')]==3 || $woRow[csf('booking_type')]==6)) 
					{ 
						$aopCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
					}
					else if($woRow[csf('item_category')]==4)
					{
						if($woRow[csf('currency_id')]==1) { $trimsAmnt=$woRow[csf('amount')]/$woRow[csf('exchange_rate')]; } else { $trimsAmnt=$woRow[csf('amount')]; }
						$actualTrimsCostArray[$woRow[csf('po_break_down_id')]]+=$trimsAmnt; 
					}
				}	
				
				$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master where item_category_id=1", "id", "avg_rate_per_unit"  );
				$receive_array=array();
				$sql_receive="select prod_id, sum(order_qnty) as qty, sum(order_amount) as amnt from inv_transaction where transaction_type=1 and item_category=1 and status_active=1 and is_deleted=0 group by prod_id";
				$resultReceive = sql_select($sql_receive);
				foreach($resultReceive as $invRow)
				{
					$avg_rate=$invRow[csf('amnt')]/$invRow[csf('qty')];
					$receive_array[$invRow[csf('prod_id')]]=$avg_rate;
				}
				
				
				
				$yarnTrimsDataArray=sql_select("select b.po_breakdown_id, b.prod_id, a.item_category, 
					sum(CASE WHEN a.transaction_type=2 and b.entry_form ='3' and b.trans_type=2 and b.issue_purpose!=2 THEN b.quantity ELSE 0 END) AS yarn_iss_qty,
					sum(CASE WHEN a.transaction_type=4 and b.entry_form ='9' and b.trans_type=4 THEN b.quantity ELSE 0 END) AS yarn_iss_return_qty,
					sum(CASE WHEN a.transaction_type=5 and b.entry_form ='11' and b.trans_type=5 THEN b.quantity ELSE 0 END) AS trans_in_qty_yarn,
					sum(CASE WHEN a.transaction_type=6 and b.entry_form ='11' and b.trans_type=6 THEN b.quantity ELSE 0 END) AS trans_out_qty_yarn,
					sum(CASE WHEN a.transaction_type=2 and b.entry_form ='25' and b.trans_type=2 THEN a.cons_rate*b.quantity ELSE 0 END) AS trims_issue_amnt
					from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category in(1,4) and a.transaction_type in(2,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in4  group by b.po_breakdown_id, b.prod_id, a.item_category");
				foreach($yarnTrimsDataArray as $invRow)
				{
					if($invRow[csf('item_category')]==1)
					{
						$iss_qty=$invRow[csf('yarn_iss_qty')]+$invRow[csf('trans_in_qty_yarn')]-$invRow[csf('yarn_iss_return_qty')]-$invRow[csf('trans_out_qty_yarn')];
						$rate='';
						if($receive_array[$invRow[csf('prod_id')]]>0)
						{
							$rate=$receive_array[$invRow[csf('prod_id')]];
						}
						else
						{
							$rate=$avg_rate_array[$invRow[csf('prod_id')]]/$exchange_rate;
						}
						
						$iss_amnt=$iss_qty*$rate;
						$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]][1]+=$iss_amnt;
					}
					else
					{
						$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]][4]+=$invRow[csf('trims_issue_amnt')];
					}
				}
				$ex_rate=80;
				$sql_fin_purchase="select b.po_breakdown_id, sum(a.cons_rate*b.quantity) as finish_purchase_amnt from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.receive_basis<>9 and a.item_category=2 and a.transaction_type=1 and b.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in4 group by b.po_breakdown_id";
				$dataArrayFinPurchase=sql_select($sql_fin_purchase);
				foreach($dataArrayFinPurchase as $finRow)
				{
					$finish_purchase_amnt_arr[$finRow[csf('po_breakdown_id')]]=$finRow[csf('finish_purchase_amnt')]/$ex_rate;
				}
				
				$sql_fin_purchase_wv="select b.po_breakdown_id, sum(a.cons_rate*b.quantity) as woven_purchase_amnt from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category=3 and a.transaction_type=1 and b.entry_form=17 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $po_cond_for_in4 group by b.po_breakdown_id";
				$dataArrayFinPurchaseW=sql_select($sql_fin_purchase_wv);
				foreach($dataArrayFinPurchaseW as $finRow)
				{
					$finish_purchase_amnt_arr[$finRow[csf('po_breakdown_id')]]+=$finRow[csf('woven_purchase_amnt')]/$ex_rate;
				}
				
				$i=1; $tot_po_qnty=0; $tot_po_value=0; $tot_ex_factory_qnty=0; $tot_ex_factory_val=0; $tot_yarn_cost_mkt=0; $tot_knit_cost_mkt=0; $tot_dye_finish_cost_mkt=0; $tot_yarn_dye_cost_mkt=0; $tot_aop_cost_mkt=0; $tot_trims_cost_mkt=0; $tot_embell_cost_mkt=0; $tot_wash_cost_mkt=0; $tot_commission_cost_mkt=0; $tot_comm_cost_mkt=0; $tot_freight_cost_mkt=0; $tot_test_cost_mkt=0; $tot_inspection_cost_mkt=0; $tot_currier_cost_mkt=0; $tot_cm_cost_mkt=0; $tot_mkt_all_cost=0; $tot_mkt_margin=0; $tot_yarn_cost_actual=0; $tot_knit_cost_actual=0; $tot_dye_finish_cost_actual=0; $tot_yarn_dye_cost_actual=0; $tot_aop_cost_actual=0; $tot_trims_cost_actual=0; $tot_embell_cost_actual=0; $tot_wash_cost_actual=0; $tot_commission_cost_actual=0; $tot_comm_cost_actual=0; $tot_freight_cost_actual=0; $tot_test_cost_actual=0; $tot_inspection_cost_actual=0; $tot_currier_cost_actual=0; $tot_cm_cost_actual=0; $tot_actual_all_cost=0; $tot_actual_margin=0; $tot_fabric_purchase_cost_mkt=0; $tot_fabric_purchase_cost_actual=0;
				
				$buyer_name_array=array();

				$mkt_po_val_array = array(); $mkt_yarn_array = array(); $mkt_knit_array = array(); $mkt_dy_fin_array = array(); $mkt_yarn_dy_array = array();
				$mkt_aop_array = array(); $mkt_trims_array = array(); $mkt_emb_array = array(); $mkt_wash_array=array(); $mkt_commn_array=array(); $mkt_commercial_array=array();
				$mkt_freight_array = array(); $mkt_test_array = array(); $mkt_ins_array = array(); $mkt_courier_array = array(); $mkt_cm_array = array();
				$mkt_total_array = array(); $mkt_margin_array = array(); $mkt_fabric_purchase_array = array();
				
				$ex_factory_val_array= array(); $yarn_cost_array= array(); $knit_cost_array= array(); $dye_cost_array= array(); $yarn_dyeing_cost_array= array();
				$aop_n_others_cost_array= array(); $trims_cost_array= array(); $enbellishment_cost_array= array(); $wash_cost_array= array(); $commission_cost_array= array(); 
				$commercial_cost_array= array(); $freight_cost_array= array(); $testing_cost_array= array(); $inspection_cost_array= array(); $courier_cost_array= array();
				$cm_cost_array= array(); $total_cost_array= array(); $margin_array= array(); $ex_factory_array=array(); $actual_cm_amnt=array(); $actual_fabric_purchase_array = array();
				
				 $condition= new condition();
				 $condition->company_name("=$company_name");
				 if(str_replace("'","",$cbo_buyer_name)>0){
					  $condition->buyer_name("=$cbo_buyer_name");
				 }
				 if(str_replace("'","",$txt_job_no) !=''){
					  $condition->job_no_prefix_num("=$txt_job_no");
				 }
				if(trim(str_replace("'","",$txt_order_no))!='')
				 {
					$condition->po_number(" like '%".trim(str_replace("'","",$txt_order_no))."%'"); 
				 }
				 if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
				 {
					 $start_date=str_replace("'","",$txt_date_from);
					 $end_date=str_replace("'","",$txt_date_to);
					 $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
				 }
				 if(str_replace("'","",$cbo_order_status) >0){
					  $condition->is_confirmed("=$cbo_order_status");
				 }
				 if(str_replace("'","",$cbo_order_status)==0){
					  $condition->is_confirmed("in(1,2,3)");
				 }
				 if(str_replace("'","",$txt_job_no)=='')
				 {
					$condition->job_no("in($all_jobs)");
				 }
				 
				 $condition->init();
				
				 $yarn= new yarn($condition);
				 $yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
				 $conversion= new conversion($condition);
				 $conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
				 $trims= new trims($condition);
				 $trims_costing_arr=$trims->getAmountArray_by_order();
				$emblishment= new emblishment($condition);
				$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
				$commission= new commision($condition);
				$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
				$commercial= new commercial($condition);
				$commercial_costing_arr=$commercial->getAmountArray_by_order();
				$other= new other($condition);
				$other_costing_arr=$other->getAmountArray_by_order();
				$wash= new wash($condition);
				$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();
				$fabric= new fabric($condition);
				$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
				
				//print_r($fabric_costing_arr);die;
				$not_yarn_dyed_cost_arr=array(1,2,30,35);
				foreach($result as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$po_ids_array[]=$row[csf('id')];
					$gmts_item='';
					$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
					foreach($gmts_item_id as $item_id)
					{
						if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
					}
					
					$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
					$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
					$unit_price=$row[csf('unit_price')]/$row[csf('ratio')];
					$po_value=$order_qnty_in_pcs*$unit_price;
					
					$tot_po_qnty+=$order_qnty_in_pcs; 
					$tot_po_value+=$po_value;
					
					$ex_factory_qty=$ex_factory_arr[$row[csf('id')]];
					$ex_factory_value=$ex_factory_qty*$unit_price;
					
					$tot_ex_factory_qnty+=$ex_factory_qty; 
					$tot_ex_factory_val+=$ex_factory_value; 
					
					$dzn_qnty=0;
					$costing_per_id=$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
					if($costing_per_id==1)
					{
						$dzn_qnty=12;
					}
					else if($costing_per_id==3)
					{
						$dzn_qnty=12*2;
					}
					else if($costing_per_id==4)
					{
						$dzn_qnty=12*3;
					}
					else if($costing_per_id==5)
					{
						$dzn_qnty=12*4;
					}
					else
					{
						$dzn_qnty=1;
					}

					$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
					$yarn_cost_mkt=$yarn_costing_arr[$row[csf('id')]];
					$knit_cost_mkt=array_sum($conversion_costing_arr_process[$row[csf('id')]][1]);
					$dye_finish_cost_mkt=0;
					foreach($conversion_cost_head_array as $process_id=>$val)
					{
						if(!in_array($process_id,$not_yarn_dyed_cost_arr))
						{
							$dye_finish_cost_mkt+=array_sum($conversion_costing_arr_process[$row[csf('id')]][$process_id]);
						}
					}					

					$yarn_dye_cost_mkt=array_sum($conversion_costing_arr_process[$row[csf('id')]][30]);
					$aop_cost_mkt=array_sum($conversion_costing_arr_process[$row[csf('id')]][35]);
					$trims_cost_mkt=$trims_costing_arr[$row[csf('id')]];
					
					$print_amount=$emblishment_costing_arr_name[$row[csf('id')]][1];
					$embroidery_amount=$emblishment_costing_arr_name[$row[csf('id')]][2];
					$special_amount=$emblishment_costing_arr_name[$row[csf('id')]][4];
					$wash_cost=$emblishment_costing_arr_name_wash[$row[csf('id')]][3];
					$other_amount=$emblishment_costing_arr_name[$row[csf('id')]][5];
					$foreign_cost=$commission_costing_arr[$row[csf('id')]][1];
					$local_cost=$commission_costing_arr[$row[csf('id')]][2];
					
					$test_cost=$other_costing_arr[$row[csf('id')]]['lab_test'];
					$freight_cost=$other_costing_arr[$row[csf('id')]]['freight'];
					$inspection_cost=$other_costing_arr[$row[csf('id')]]['inspection'];
					$certificate_cost=$other_costing_arr[$row[csf('id')]]['certificate_pre_cost'];
					$currier_cost=$other_costing_arr[$row[csf('id')]]['currier_pre_cost'];
					$cm_cost=$other_costing_arr[$row[csf('id')]]['cm_cost'];
					
					$embell_cost_mkt=$print_amount+$embroidery_amount+$special_amount+$other_amount;
					
					$wash_cost_mkt=$wash_cost;
					$commission_cost_mkt=$foreign_cost+$local_cost;
					$comm_cost_mkt=$commercial_costing_arr[$row[csf('id')]];
					$freight_cost_mkt=$freight_cost;
					$test_cost_mkt=$test_cost;
					$inspection_cost_mkt=$inspection_cost;
					$currier_cost_mkt=$currier_cost;
					$cm_cost_mkt=$cm_cost;
					
					$fabric_purchase_cost_mkt=array_sum($fabric_costing_arr['knit']['grey'][$row[csf('id')]])+array_sum($fabric_costing_arr['woven']['grey'][$row[csf('id')]]);
					
					//$mkt_all_cost=$yarn_cost_mkt+$knit_cost_mkt+$dye_finish_cost_mkt+$yarn_dye_cost_mkt+$aop_cost_mkt+$trims_cost_mkt+$embell_cost_mkt+$wash_cost_mkt+$commission_cost_mkt+$comm_cost_mkt+$freight_cost_mkt+$test_cost_mkt+$inspection_cost_mkt+$currier_cost_mkt+$cm_cost_mkt+$fabric_purchase_cost_mkt;
					
					
					$mkt_all_cost=$yarn_cost_mkt+$yarn_dye_cost_mkt+$knit_cost_mkt+$dye_finish_cost_mkt+$fabric_purchase_cost_mkt+$aop_cost_mkt+$trims_cost_mkt+$embell_cost_mkt+$wash_cost_mkt+$comm_cost_mkt+$freight_cost_mkt+$test_cost_mkt+$inspection_cost_mkt+$currier_cost_mkt;
					
					
					
					$mkt_margin=$po_value-$mkt_all_cost;
					$mkt_margin_perc=($mkt_margin/$po_value)*100;
					
					
					
					$yarn_cost_actual=$yarnTrimsCostArray[$row[csf('id')]][1];
					$trims_cost_actual=$actualTrimsCostArray[$row[csf('id')]];
					$yarn_dye_cost_actual=0;
					$aop_cost_actual=$aopCostArray[$row[csf('id')]];
					$embell_cost_actual=$embellCostArray[$row[csf('id')]];
					$wash_cost_actual=$washCostArray[$row[csf('id')]];
					$knit_cost_actual=$subconCostArray[$row[csf('id')]]['knit_bill']/$exchange_rate;
					$dye_finish_cost_actual=$subconCostArray[$row[csf('id')]]['dye_finish_bill']/$exchange_rate;
					$commission_cost_actual=($ex_factory_qty/$dzn_qnty)*$fabriccostArray[$row[csf('job_no')]]['commission'];
					$comm_cost_actual=$actualCostArray[6][$row[csf('id')]];
					$freight_cost_actual=$actualCostArray[2][$row[csf('id')]];
					$test_cost_actual=$actualCostArray[1][$row[csf('id')]];
					$inspection_cost_actual=$actualCostArray[3][$row[csf('id')]];
					$currier_cost_actual=$actualCostArray[4][$row[csf('id')]];
					$cm_cost_actual=$actualCostArray[5][$row[csf('id')]];
					
					$fabric_purchase_cost_actual=$finish_purchase_amnt_arr[$row[csf('id')]];
					
					/*$actual_all_cost=$yarn_cost_actual+$knit_cost_actual+$dye_finish_cost_actual+$yarn_dye_cost_actual+$aop_cost_actual+$trims_cost_actual+$embell_cost_actual+$wash_cost_actual+$commission_cost_actual+$comm_cost_actual+$freight_cost_actual+$test_cost_actual+$inspection_cost_actual+$currier_cost_actual+$cm_cost_actual+$fabric_purchase_cost_actual;*/
					
					
					$actual_all_cost=$yarn_cost_actual+$knit_cost_actual+$dye_finish_cost_actual+$yarn_dye_cost_actual+$aop_cost_actual+$trims_cost_actual+$embell_cost_actual+$wash_cost_actual+$comm_cost_actual+$freight_cost_actual+$test_cost_actual+$inspection_cost_actual+$currier_cost_actual+$fabric_purchase_cost_actual;
					
					
					
					$actual_margin_perc=0;
					
					
					
					$actual_margin=$ex_factory_value-$actual_all_cost;
					$actual_margin_perc=($actual_margin/$ex_factory_value)*100;
					
					$tot_yarn_cost_mkt+=$yarn_cost_mkt; 
					$tot_knit_cost_mkt+=$knit_cost_mkt; 
					$tot_dye_finish_cost_mkt+=$dye_finish_cost_mkt; 
					$tot_yarn_dye_cost_mkt+=$yarn_dye_cost_mkt; 
					$tot_aop_cost_mkt+=$aop_cost_mkt; 
					$tot_trims_cost_mkt+=$trims_cost_mkt; 
					$tot_embell_cost_mkt+=$embell_cost_mkt; 
					$tot_wash_cost_mkt+=$wash_cost_mkt; 
					$tot_commission_cost_mkt+=$commission_cost_mkt; 
					$tot_comm_cost_mkt+=$comm_cost_mkt; 
					$tot_freight_cost_mkt+=$freight_cost_mkt; 
					$tot_test_cost_mkt+=$test_cost_mkt; 
					$tot_inspection_cost_mkt+=$inspection_cost_mkt; 
					$tot_currier_cost_mkt+=$currier_cost_mkt; 
					$tot_cm_cost_mkt+=$cm_cost_mkt; 
					$tot_mkt_all_cost+=$mkt_all_cost; 
					$tot_mkt_margin+=$mkt_margin; 
					$tot_yarn_cost_actual+=$yarn_cost_actual; 
					$tot_knit_cost_actual+=$knit_cost_actual; 
					$tot_dye_finish_cost_actual+=$dye_finish_cost_actual;
					$tot_yarn_dye_cost_actual+=$yarn_dye_cost_actual; 
					$tot_aop_cost_actual+=$aop_cost_actual; 
					$tot_trims_cost_actual+=$trims_cost_actual; 
					$tot_embell_cost_actual+=$embell_cost_actual;
					$tot_wash_cost_actual+=$wash_cost_actual; 
					$tot_commission_cost_actual+=$commission_cost_actual; 
					$tot_comm_cost_actual+=$comm_cost_actual; 
					$tot_freight_cost_actual+=$freight_cost_actual; 
					$tot_test_cost_actual+=$test_cost_actual; 
					$tot_inspection_cost_actual+=$inspection_cost_actual; 
					$tot_currier_cost_actual+=$currier_cost_actual; 
					$tot_cm_cost_actual+=$cm_cost_actual; 
					$tot_actual_all_cost+=$actual_all_cost; 
					$tot_actual_margin+=$actual_margin;
					
					$tot_fabric_purchase_cost_mkt+=$fabric_purchase_cost_mkt;
					$tot_fabric_purchase_cost_actual+=$fabric_purchase_cost_actual;
					
					$buyer_name_array[$row[csf('buyer_name')]]=$buyer_arr[$row[csf('buyer_name')]];
					$mkt_po_val_array[$row[csf('buyer_name')]]+=$po_value;
					$mkt_yarn_array[$row[csf('buyer_name')]]+=$yarn_cost_mkt+$yarn_dye_cost_mkt;
					$mkt_knit_array[$row[csf('buyer_name')]]+=$knit_cost_mkt;
					$mkt_dy_fin_array[$row[csf('buyer_name')]]+=$dye_finish_cost_mkt;
					//$mkt_yarn_dy_array[$row[csf('buyer_name')]]+=$yarn_dye_cost_mkt;
					$mkt_aop_array[$row[csf('buyer_name')]]+=$aop_cost_mkt;
					$mkt_trims_array[$row[csf('buyer_name')]]+=$trims_cost_mkt;
					$mkt_emb_array[$row[csf('buyer_name')]]+=$embell_cost_mkt;
					$mkt_wash_array[$row[csf('buyer_name')]]+=$wash_cost_mkt;
					$mkt_commn_array[$row[csf('buyer_name')]]+=$commission_cost_mkt;
					$mkt_commercial_array[$row[csf('buyer_name')]]+=$comm_cost_mkt;
					$mkt_freight_array[$row[csf('buyer_name')]]+=$freight_cost_mkt;
					$mkt_test_array[$row[csf('buyer_name')]]+=$test_cost_mkt;
					$mkt_ins_array[$row[csf('buyer_name')]]+=$inspection_cost_mkt;
					$mkt_courier_array[$row[csf('buyer_name')]]+=$currier_cost_mkt;
					$mkt_cm_array[$row[csf('buyer_name')]]+=$cm_cost_mkt;
					$mkt_total_array[$row[csf('buyer_name')]]+=$mkt_all_cost;
					$mkt_margin_array[$row[csf('buyer_name')]]+=$mkt_margin;
					$mkt_fabric_purchase_array[$row[csf('buyer_name')]]+=$fabric_purchase_cost_mkt;
					
					$ex_factory_val_array[$row[csf('buyer_name')]]+=$ex_factory_value;
					$yarn_cost_array[$row[csf('buyer_name')]]+=$yarn_cost_actual;
					$knit_cost_array[$row[csf('buyer_name')]]+=$knit_cost_actual;
					$dye_cost_array[$row[csf('buyer_name')]]+=$dye_finish_cost_actual;
					
					$aop_n_others_cost_array[$row[csf('buyer_name')]]+=$aop_cost_actual;
					$trims_cost_array[$row[csf('buyer_name')]]+=$trims_cost_actual;
					$enbellishment_cost_array[$row[csf('buyer_name')]]+=$embell_cost_actual;
					$wash_cost_array[$row[csf('buyer_name')]]+=$wash_cost_actual;
					$commission_cost_array[$row[csf('buyer_name')]]+=$commission_cost_actual;
					$commercial_cost_array[$row[csf('buyer_name')]]+=$comm_cost_actual;
					$freight_cost_array[$row[csf('buyer_name')]]+=$freight_cost_actual;
					$testing_cost_array[$row[csf('buyer_name')]]+=$test_cost_actual;
					$inspection_cost_array[$row[csf('buyer_name')]]+=$inspection_cost_actual;
					$courier_cost_array[$row[csf('buyer_name')]]+=$currier_cost_actual;
					$cm_cost_array[$row[csf('buyer_name')]]+=$cm_cost_actual;
					$total_cost_array[$row[csf('buyer_name')]]+=$actual_all_cost;
					$margin_array[$row[csf('buyer_name')]]+=$actual_margin;
					$actual_fabric_purchase_array[$row[csf('buyer_name')]]+=$fabric_purchase_cost_actual;
					
					
				 $button_po="<a href='#' onClick=\"generate_po_report('".$company_name."','".$row[csf('id')]."','".$row[csf('job_no')]."','show_po_detail_report','1')\" '> ".$row[csf('po_number')]."<a/>";
					
				?>
                	<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="70"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
						<td width="100"><? echo $team_leader_arr[$row[csf('team_leader')]]; ?></td>
                        <td width="70" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                        <td width="60" align="center"><? echo $row[csf('year')]; ?></td>
                        <td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                        <td width="120"><p><? echo $gmts_item; ?></p></td>
                        <td width="100"><p><? echo  $button_po; ?></p></td>
                        <td width="88" align="right" style="padding-right:2px"><? echo $order_qnty_in_pcs; ?></td>
                        <td width="68" align="right" style="padding-right:2px"><? echo number_format($unit_price,2); ?></td>
                        <td width="108" align="right" style="padding-right:2px"><? echo number_format($po_value,2); ?></td>
                        <td width="98" align="right" style="padding-right:2px"><? echo number_format($row[csf('set_smv')],2); ?></td>
                        <td width="80" align="center">
                        	<p><? if($row[csf("pub_shipment_date")] !="" && $row[csf("pub_shipment_date")] !="0000-00-00")  echo change_date_format($row[csf("pub_shipment_date")]); ?>&nbsp;</p>
                        </td>
                        <td width="100"><p><? echo $shipment_status[$row[csf('shiping_status')]]; ?></p></td>
                        
                        <td width="100" align="left"><b>Pre Costing</b></td>
                        <td width="98" align="right" style="padding-right:2px"><? echo number_format($order_qnty_in_pcs,0); ?></td>
                        <td width="108" align="right" style="padding-right:2px"><? echo number_format($po_value,2); ?></td>
                        <td width="108" align="right" style="padding-right:2px"><? echo number_format($fabric_purchase_cost_mkt,2); ?></td>
                        <td width="108" style="padding-right:2px" align="right"><? echo number_format($trims_cost_mkt,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($embell_cost_mkt,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($wash_cost_mkt,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($comm_cost_mkt,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($freight_cost_mkt,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($test_cost_mkt,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($inspection_cost_mkt,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($currier_cost_mkt,2); ?></td>                        
                        <td width="100" align="right"><? echo number_format($mkt_all_cost,2);?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($commission_cost_mkt,2); ?></td>

                        <td width="100" align="right">
						<? 	
							$Net_PO_ex_Fact_Value_mkt=$po_value-$commission_cost_mkt;
							echo number_format($Net_PO_ex_Fact_Value_mkt,2);
							$tot_Net_PO_ex_Fact_Value_mkt+= $Net_PO_ex_Fact_Value_mkt;
						?>
                        </td>
                        <td width="100" align="right">
						<? 
							$CM_Value_Contribution_value_mkt=$Net_PO_ex_Fact_Value_mkt-$mkt_all_cost;
							echo number_format($CM_Value_Contribution_value_mkt,2); 
							$tot_CM_Value_Contribution_value_mkt+= $CM_Value_Contribution_value_mkt;
						?>
                        </td>
                        
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($cm_cost_mkt,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right">
						<? 
							$mkt_margin=$CM_Value_Contribution_value_mkt-$cm_cost_mkt; 
							echo number_format($mkt_margin,2); 
							$total_mkt_margin+=$mkt_margin;
						?>
                        </td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($mkt_margin_perc,2); ?></td>
						 <td width="58"><? echo $company_short_arr[$row[csf('company_name')]]; ?></td>

                    </tr>
                    
                    <tr bgcolor="#F5F5F5" onClick="change_color('tr_a<? echo $i; ?>','#F5F5F5')" id="tr_a<? echo $i; ?>">
                    	
                        <td width="40"><? echo $i; ?></td>
                        <td width="70"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
						<td width="100"><? echo $team_leader_arr[$row[csf('team_leader')]]; ?></td>
                        <td width="70" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                        <td width="60" align="center"><? echo $row[csf('year')]; ?></td>
                        <td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                        <td width="120"><p><? echo $gmts_item; ?></p></td>
                        <td width="100"><p><? echo  $button_po; ?></p></td>
                        <td width="88" align="right" style="padding-right:2px"><? echo $order_qnty_in_pcs; ?></td>
                        <td width="68" align="right" style="padding-right:2px"><? echo number_format($unit_price,2); ?></td>
                        <td width="108" align="right" style="padding-right:2px"><? echo number_format($po_value,2); ?></td>
                        <td width="98" align="right" style="padding-right:2px"><? echo number_format($row[csf('set_smv')],2); ?></td>
                        <td width="80" align="center">
                        	<p><? if($row[csf("pub_shipment_date")] !="" && $row[csf("pub_shipment_date")] !="0000-00-00")  echo change_date_format($row[csf("pub_shipment_date")]); ?>&nbsp;</p>
                        </td>
                        <td width="100"><p><? echo $shipment_status[$row[csf('shiping_status')]]; ?></p></td>
                        <td width="100" align="left"><b>Actual</b></td>
                        <td width="98" align="right" style="padding-right:2px">
						<a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $row[csf('job_no_prefix_num')];?>','<? echo $row[csf('id')]; ?>','650px')"><? echo  number_format($ex_factory_qty,0); ?></a>
						</td>
                        <td width="108" align="right" style="padding-right:2px"><? echo number_format($ex_factory_value,2); ?></td>
                        <td width="108" style="padding-right:2px" align="right"><a href="#report_details" title="Exchange rate=80" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','fabric_purchase_cost_actual','Fabric Purchase Cost Details','900px')"><? echo number_format($fabric_purchase_cost_actual,2); ?></a></td>
                        <td width="108" style="padding-right:2px" align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','trims_cost_actual','Trims Cost Details','800px')"><? echo number_format($trims_cost_actual,2); ?></a></td>
                        <td width="98" style="padding-right:2px" align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','embell_cost_actual','Embellishment Cost Details','800px')"><? echo number_format($embell_cost_actual,2); ?></a></td>
                        <td width="98" style="padding-right:2px" align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]; ?>','wash_cost_actual','Wash Cost Details','800px')"><? echo number_format($wash_cost_actual,2); ?></a></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($comm_cost_actual,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($freight_cost_actual,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($test_cost_actual,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($inspection_cost_actual,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($currier_cost_actual,2); ?></td>
                        <td width="100" align="right"><? echo number_format($actual_all_cost,2);?></td>
                        <td width="98" style="padding-right:2px" align="right"><a href="#report_details" onClick="openmypage_actual('<? echo $row[csf('id')]."_".$row[csf('job_no')]."_".$ex_factory_qty."_".$dzn_qnty; ?>','commission_cost_actual','Commission Cost Details','600px')"><? echo number_format($commission_cost_actual,2); ?></a></td>
                        <td width="100" align="right">
						<? 
							
							
							//$Net_PO_ex_Fact_Value_actual=$po_value-$commission_cost_actual;
							$Net_PO_ex_Fact_Value_actual=$ex_factory_value-$commission_cost_actual;
							echo number_format($Net_PO_ex_Fact_Value_actual,2);
							$tot_Net_PO_ex_Fact_Value_actual+=$Net_PO_ex_Fact_Value_actual;
						?>
                        </td>
                        <td width="100" align="right">
						<? 
							$CM_Value_Contribution_actual=$Net_PO_ex_Fact_Value_actual-$actual_all_cost;
							echo number_format($CM_Value_Contribution_actual,2);
							$tot_CM_Value_Contribution_actual+=$CM_Value_Contribution_actual;
						?>
                        </td>
                        
                        
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($cm_cost_actual,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right">
						<? 
							$actual_margin=$CM_Value_Contribution_actual-$cm_cost_actual;
							echo number_format($actual_margin,2);
							$total_actual_margin+=$actual_margin;
						?>
                        </td>
                        <td width="100" style="padding-right:2px" align="right">
						<? 
						echo  number_format(($actual_margin/$ex_factory_value)*100,2);
						
						 //echo number_format($actual_margin_perc,2); ?>
                        
                        </td>
						 <td width="58"><? echo $company_short_arr[$row[csf('company_name')]]; ?></td>
                    </tr>
                    
                    <tr bgcolor="#F5F5F5" onClick="change_color('tr_v<? echo $i; ?>','#F5F5F5')" id="tr_v<? echo $i; ?>">
                    	
                        <td width="40"><? echo $i; ?></td>
                        <td width="70"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
						<td width="100"><? echo $team_leader_arr[$row[csf('team_leader')]]; ?></td>
                        <td width="70" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                        <td width="60" align="center"><? echo $row[csf('year')]; ?></td>
                        <td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                        <td width="120"><p><? echo $gmts_item; ?></p></td>
                        <td width="100"><p><? echo  $button_po; ?></p></td>
                        <td width="88" align="right" style="padding-right:2px"><? echo $order_qnty_in_pcs; ?></td>
                        <td width="68" align="right" style="padding-right:2px"><? echo number_format($unit_price,2); ?></td>
                        <td width="108" align="right" style="padding-right:2px"><? echo number_format($po_value,2); ?></td>
                        <td width="98" align="right" style="padding-right:2px"><? echo number_format($row[csf('set_smv')],2); ?></td>
                        <td width="80" align="center">
                        	<p><? if($row[csf("pub_shipment_date")] !="" && $row[csf("pub_shipment_date")] !="0000-00-00")  echo change_date_format($row[csf("pub_shipment_date")]); ?>&nbsp;</p>
                        </td>
                        <td width="100"><p><? echo $shipment_status[$row[csf('shiping_status')]]; ?></p></td>
                        <td width="100" align="left"><b>Variance</b></td>
                        <td width="98" align="right" style="padding-right:2px"><? echo  number_format($order_qnty_in_pcs-$ex_factory_qty,0); ?></td>
                        <td width="108" align="right" style="padding-right:2px"><? echo number_format($po_value-$ex_factory_value,2); ?></td>
                        <td width="108" style="padding-right:2px" align="right"><? echo number_format($fabric_purchase_cost_mkt-$fabric_purchase_cost_actual,2); ?></td>
                        <td width="108" style="padding-right:2px" align="right"><? echo number_format($trims_cost_mkt-$trims_cost_actual,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($embell_cost_mkt-$embell_cost_actual,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($wash_cost_mkt-$wash_cost_actual,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($comm_cost_mkt-$comm_cost_actual,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($freight_cost_mkt-$freight_cost_actual,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($test_cost_mkt-$test_cost_actual,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($inspection_cost_mkt-$inspection_cost_actual,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($currier_cost_mkt-$currier_cost_actual,2); ?></td>
                        <td width="100" align="right"><? echo number_format($mkt_all_cost-$actual_all_cost,2);?></td>
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($commission_cost_mkt-$commission_cost_actual,2); ?></td>
                        <td width="100" align="right">
						<? 
							$Net_PO_ex_Fact_Value_actual=$ex_factory_value-$commission_cost_actual;
							echo number_format($Net_PO_ex_Fact_Value_mkt-$Net_PO_ex_Fact_Value_actual,2);
							//$tot_Net_PO_ex_Fact_Value_actual+=$Net_PO_ex_Fact_Value_actual;
						?>
                        </td>
                        <td width="100" align="right">
						<? 
							$CM_Value_Contribution_actual=$Net_PO_ex_Fact_Value_actual-$actual_all_cost;
							if($CM_Value_Contribution_value_mkt<0 && $CM_Value_Contribution_actual<0)
							{
								$cm_variance_contribution_value=($CM_Value_Contribution_value_mkt)+($CM_Value_Contribution_actual);
								echo number_format($cm_variance_contribution_value,2);
							}
							else if($CM_Value_Contribution_value_mkt>0)//Is greater than 0
							{
								$cm_variance_contribution_value=($CM_Value_Contribution_actual)-($CM_Value_Contribution_value_mkt);
								echo number_format($cm_variance_contribution_value,2);
							}
							else if($CM_Value_Contribution_value_mkt<0)//Is Less than 0
							{
								$cm_variance_contribution_value=($CM_Value_Contribution_actual)+($CM_Value_Contribution_value_mkt);
								echo number_format($cm_variance_contribution_value,2);
							}
							else
							{
								echo number_format($CM_Value_Contribution_value_mkt-$CM_Value_Contribution_actual,2);
							}
							//$tot_CM_Value_Contribution_actual+=$CM_Value_Contribution_actual;
						?>
                        </td>
                        
                        
                        <td width="98" style="padding-right:2px" align="right"><? echo number_format($cm_cost_mkt-$cm_cost_actual,2); ?></td>
                        <td width="98" style="padding-right:2px" align="right">
						<? 
							$actual_margin=$CM_Value_Contribution_actual-$cm_cost_actual;
							if($mkt_margin<0 && $actual_margin<0)
							{
								$margin_variance_value=($mkt_margin)+($actual_margin);
								echo number_format($margin_variance_value,2);
							}
							else if($mkt_margin>0) //Is greater than 0
							{
								$margin_variance_value=($actual_margin)-($mkt_margin);
								echo number_format($margin_variance_value,2);
							}
							else if($mkt_margin<0) //Is Less than 0
							{
								$margin_variance_value=($actual_margin)+($mkt_margin);
								echo number_format($margin_variance_value,2);
							}
							else
							{
								echo number_format($mkt_margin-$actual_margin,2);
							}
							//$total_actual_margin+=$Net_PO_ex_Fact_Value_mkt-$actual_margin;
						?>
                        </td>
                        <td width="98" style="padding-right:2px" align="right">
						<? 
						echo  number_format($mkt_margin_perc-(($actual_margin/$ex_factory_value)*100),2);
						
						 //echo number_format($actual_margin_perc,2); ?>
                        
                        </td>
						 <td width="58"><? echo $company_short_arr[$row[csf('company_name')]]; ?></td>
                    </tr>
                    
				<?
					$i++;
				}
				
				//$tot_mkt_margin_perc=($tot_mkt_margin/$tot_po_value)*100;
				//$tot_actual_margin_perc=($tot_actual_margin/$tot_ex_factory_val)*100;
				
				?>
            </table>
		</div>
        <table class="rpt_table" width="3690" cellpadding="0" cellspacing="0" border="1" rules="all">
            <tr bgcolor="#CCDDEE" onClick="change_color('tr_pt','#CCDDEE')" id="tr_pt" style="font-weight:bold;">
                <td colspan="8" style="padding-right:4px" align="right">Total</td>
                <td width="102"><? echo number_format($tot_po_qnty,0); ?></td>
                <td width="80">&nbsp;</td>
                <td width="123" style="padding-right:2px" align="right"><? echo number_format($tot_po_value,2); ?></td>
                <td width="112" style="padding-right:2px" align="right">&nbsp;</td>
                <td width="90">&nbsp;</td>
                <td width="115">&nbsp;</td>
                <td width="114">Pre Costing Total</td>
                <td width="112" style="padding-right:2px" align="right"><? echo number_format($tot_po_qnty,0); ?></td>
                <td width="123" style="padding-right:2px" align="right"><? echo number_format($tot_po_value,2); ?></td>                
                <td width="123" style="padding-right:2px" align="right"><? echo number_format($tot_fabric_purchase_cost_mkt,2); ?></td>
                <td width="124" style="padding-right:2px" align="right"><? echo number_format($tot_trims_cost_mkt,2); ?></td>
                <td width="112" style="padding-right:2px" align="right"><? echo number_format($tot_embell_cost_mkt,2); ?></td>
                <td width="111" style="padding-right:2px" align="right"><? echo number_format($tot_wash_cost_mkt,2); ?></td>
                <td width="112" style="padding-right:2px" align="right"><? echo number_format($tot_comm_cost_mkt,2); ?></td>
                <td width="112" style="padding-right:2px" align="right"><? echo number_format($tot_freight_cost_mkt,2); ?></td>
                <td width="112" style="padding-right:2px" align="right"><? echo number_format($tot_test_cost_mkt,2); ?></td>
                <td width="112" style="padding-right:2px" align="right"><? echo number_format($tot_inspection_cost_mkt,2); ?></td>
                <td width="112" style="padding-right:2px" align="right"><? echo number_format($tot_currier_cost_mkt,2); ?></td>
                <td width="113" align="right"><? echo number_format($tot_mkt_all_cost,2);?></td>
                <td width="112" style="padding-right:2px" align="right"><? echo number_format($tot_commission_cost_mkt,2); ?></td>
                <td width="114" align="right"><? echo number_format($tot_Net_PO_ex_Fact_Value_mkt,2);?></td>
                <td width="114" align="right"><? echo number_format($tot_CM_Value_Contribution_value_mkt,2);?></td>

                <td width="112" style="padding-right:2px" align="right"><? echo number_format($tot_cm_cost_mkt,2); ?></td>
                <td width="112" style="padding-right:2px" align="right"><? echo number_format($total_mkt_margin,2); ?></td>
                <td width="114" style="padding-right:2px" align="right"><? echo number_format($tot_mkt_margin_perc,2); ?></td>
				<td width="64" style="padding-right:2px" align="right"><? //echo number_format($tot_mkt_margin_perc,2); ?></td>
            </tr>
            <tr bgcolor="#CCCCFF" onClick="change_color('tr_at','#CCCCFF')" id="tr_at" style="font-weight:bold;">
                <td colspan="8" style="padding-right:4px" align="right"></td>
                <td width="90">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="108" style="padding-right:2px" align="right">&nbsp;</td>
                <td width="98" style="padding-right:2px" align="right">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">Actual Total</td>
                <td width="98" align="right" style="padding-right:2px"><? echo number_format($tot_ex_factory_qnty,0); ?></td>
                <td width="108" align="right" style="padding-right:2px"><? echo number_format($tot_ex_factory_val,2); ?></td>                
                <td width="108" style="padding-right:2px" align="right"><? echo number_format($tot_fabric_purchase_cost_actual,2); ?></td>
                <td width="108" align="right" style="padding-right:2px"><? echo number_format($tot_trims_cost_actual,2); ?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo number_format($tot_embell_cost_actual,2); ?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo number_format($tot_wash_cost_actual,2); ?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo number_format($tot_comm_cost_actual,2); ?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo number_format($tot_freight_cost_actual,2); ?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo number_format($tot_test_cost_actual,2); ?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo number_format($tot_inspection_cost_actual,2); ?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo number_format($tot_currier_cost_actual,2); ?></td>
                
                <td width="100" align="right"><? echo number_format($tot_actual_all_cost,2);?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo number_format($tot_commission_cost_actual,2); ?></td>
                <td width="100" align="right"><? echo number_format($tot_Net_PO_ex_Fact_Value_actual,2);?></td>
                <td width="100" align="right"><? echo number_format($tot_CM_Value_Contribution_actual,2);?></td>
                
                <td width="98" style="padding-right:2px" align="right"><? echo number_format($tot_cm_cost_actual,2); ?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo number_format($total_actual_margin,2); ?></td>
                <td width="98" style="padding-right:2px" align="right"><? echo number_format($tot_actual_margin_perc,2); ?></td>
				<td width="58" style="padding-right:2px" align="right"><? //echo number_format($tot_actual_margin_perc,2); ?></td>
            </tr>
            
        </table>

        
	</fieldset>
<?
	/*foreach (glob("../../../../ext_resource/tmp_report/$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$html=ob_get_contents();
	$name=time();
	$filename="../../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	$filename="../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	echo "$total_data****$filename";*/
	$html = ob_get_contents();
	ob_clean();
	//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html****$filename****$report_type"; 
	exit();
}



if($action=="yarn_cost")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	
	$exchange_rate=76;
	$costing_per_arr=return_library_array( "select job_no, costing_per_id from wo_pre_cost_dtls where status_active=1 and is_deleted=0", "job_no", "costing_per_id");
	$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master where item_category_id=1", "id", "avg_rate_per_unit"  );
	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );

	$dataArrayYarn=array();
	$yarn_sql="select job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, sum(cons_qnty) as qnty, sum(amount) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 group by job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id";
	$resultYarn=sql_select($yarn_sql);
	foreach($resultYarn as $yarnRow)
	{
		$dataArrayYarn[$yarnRow[csf('job_no')]].=$yarnRow[csf('count_id')]."**".$yarnRow[csf('copm_one_id')]."**".$yarnRow[csf('percent_one')]."**".$yarnRow[csf('copm_two_id')]."**".$yarnRow[csf('percent_two')]."**".$yarnRow[csf('type_id')]."**".$yarnRow[csf('qnty')]."**".$yarnRow[csf('amount')].",";
	}
	
	$receive_array=array();
	$sql_receive="select prod_id, sum(order_qnty) as qty, sum(order_amount) as amnt from inv_transaction where transaction_type=1 and item_category=1 and status_active=1 and is_deleted=0 group by prod_id";
	$resultReceive = sql_select($sql_receive);
	foreach($resultReceive as $invRow)
	{
		$avg_rate=$invRow[csf('amnt')]/$invRow[csf('qty')];
		$receive_array[$invRow[csf('prod_id')]]=$avg_rate;
	}
	
	$yarnIssDataArray=sql_select("select po_breakdown_id, prod_id,
					sum(CASE WHEN entry_form ='3' and trans_type=2 and issue_purpose!=2 THEN quantity ELSE 0 END) AS yarn_iss_qty,
					sum(CASE WHEN entry_form ='9' and trans_type=4 THEN quantity ELSE 0 END) AS yarn_iss_return_qty,
					sum(CASE WHEN entry_form ='11' and trans_type=5 THEN quantity ELSE 0 END) AS trans_in_qty_yarn,
					sum(CASE WHEN entry_form ='11' and trans_type=6 THEN quantity ELSE 0 END) AS trans_out_qty_yarn
					from order_wise_pro_details where trans_type in(2,4,5,6) and status_active=1 and is_deleted=0 and po_breakdown_id in($po_id) group by po_breakdown_id,prod_id");
	foreach($yarnIssDataArray as $invRow)
	{
		$iss_qty=$invRow[csf('yarn_iss_qty')]+$invRow[csf('trans_in_qty_yarn')]-$invRow[csf('yarn_iss_return_qty')]-$invRow[csf('trans_out_qty_yarn')];
		$dataArrayYarnIssue[$invRow[csf('po_breakdown_id')]][1]+=$iss_qty;
		$rate='';
		if($receive_array[$invRow[csf('prod_id')]]>0)
		{
			$rate=$receive_array[$invRow[csf('prod_id')]]/$exchange_rate;
		}
		else
		{
			$rate=$avg_rate_array[$invRow[csf('prod_id')]]/$exchange_rate;
		}
		$dataArrayYarnIssue[$invRow[csf('po_breakdown_id')]][2]+=$iss_qty*$rate;
	}

?>
	<style>
		hr
		{
			color: #676767;
			background-color: #676767;
			height: 1px;
		}
	</style> 
    <div>
        <fieldset style="width:1033px;">
        	<table class="rpt_table" width="1030" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="40">SL</th>
                    <th width="60">Buyer</th>
                    <th width="80">PO No</th>
                    <th width="50">Year</th>
                    <th width="60">Job No</th>
                    <th width="90">Order Qty</th>
                    <th width="70">Count</th>
                    <th width="100">Composition</th>
                    <th width="70">Type</th>
                    <th width="90">Required<br/><font style="font-size:9px; font-weight:100">(As Per Pre-Cost)</font></th>
                    <th width="100">Cost ($)</th>
                    <th width="90">Issued</th>
                    <th>Cost ($)</th>
                </thead>
            </table>	
            <div style="width:1030px; max-height:310px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="1010" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
					else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
					else $year_field="";//defined Later
						
					$sql="select a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id, b.po_number, b.pub_shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_id) order by b.pub_shipment_date, a.job_no_prefix_num, b.id";
					$result=sql_select($sql);
					$i=1; $tot_po_qnty=0; $tot_mkt_required=0; $tot_required_cost=0; $tot_yarn_iss_qty=0; $tot_yarn_iss_cost=0;
				
					 $condition= new condition();
						if($po_id!='')
						{
							$condition->po_id("in($po_id)"); 
						}
					
					 $condition->init();
				
					 $yarn= new yarn($condition);
					 	// echo $yarn->getQuery(); die;
					$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
					foreach($result as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$dzn_qnty=0; $job_mkt_required=0; $yarn_issued=0; $yarn_data_array=array(); //$job_mkt_required_cost=0;
						$costing_per_id=$costing_per_arr[$row[csf('job_no')]];
						if($costing_per_id==1)
						{
							$dzn_qnty=12;
						}
						else if($costing_per_id==3)
						{
							$dzn_qnty=12*2;
						}
						else if($costing_per_id==4)
						{
							$dzn_qnty=12*3;
						}
						else if($costing_per_id==5)
						{
							$dzn_qnty=12*4;
						}
						else
						{
							$dzn_qnty=1;
						}
						
						$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
						$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
						$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
						$tot_po_qnty+=$order_qnty_in_pcs;
						$job_mkt_required_cost=$yarn_costing_arr[$row[csf('id')]];
						
						$dataYarn=explode(",",substr($dataArrayYarn[$row[csf('job_no')]],0,-1));
						foreach($dataYarn as $yarnRow)
						{
							$yarnRow=explode("**",$yarnRow);
							$count_id=$yarnRow[0];
							$copm_one_id=$yarnRow[1];
							$percent_one=$yarnRow[2];
							$copm_two_id=$yarnRow[3];
							$percent_two=$yarnRow[4];
							$type_id=$yarnRow[5];
							$qnty=$yarnRow[6];
							$amnt=$yarnRow[7];
							
							$mkt_required=$plan_cut_qnty*($qnty/$dzn_qnty);
							//$mkt_required_cost=$plan_cut_qnty*($amnt/$dzn_qnty);
							$job_mkt_required+=$mkt_required;
							//$job_mkt_required_cost+=$mkt_required_cost;
							
							$yarn_data_array['count'][]=$yarn_count_details[$count_id];
							$yarn_data_array['type'][]=$yarn_type[$type_id];
							
							if($percent_two!=0)
							{
								$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id]." ".$percent_two." %";
							}
							else
							{
								$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id];
							}

							$yarn_data_array['comp'][]=$compos;
						}
						
						$yarn_iss_qty=$dataArrayYarnIssue[$row[csf('id')]][1];
						$yarn_iss_cost=$dataArrayYarnIssue[$row[csf('id')]][2];
						
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="60" style="word-break:break-all;"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                            <td width="80" style="word-break:break-all;"><? echo $row[csf('po_number')]; ?></td>
                            <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                            <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                            <td width="90" align="right"><? echo $order_qnty_in_pcs; ?></td>
                            <td width="70">
								<? 
                                    $d=1;
                                    foreach($yarn_data_array['count'] as $yarn_count_value)
                                    {
                                        if($d!=1)
                                        {
                                            echo "<hr/>";
                                        }
                                        echo $yarn_count_value;
                                        $d++;
                                    }
                                ?>
                            </td>
                            <td width="100">
                                <div style="word-wrap:break-word; width:100px">
                                    <? 
                                         $d=1;
                                         foreach($yarn_data_array['comp'] as $yarn_composition_value)
                                         {
                                            if($d!=1)
                                            {
                                                echo "<hr/>";
                                            }
                                            echo $yarn_composition_value;
                                            $d++;
                                         }
                                    ?>
                                </div>
                            </td>
                            <td width="70">
                                <p>
                                    <? 
                                         $d=1;
                                         foreach($yarn_data_array['type'] as $yarn_type_value)
                                         {
                                            if($d!=1)
                                            {
                                               echo "<hr/>";
                                            }
                                            
                                            echo $yarn_type_value; 
                                            $d++;
                                         }
                                    ?>
                                </p>
                            </td>
							<td width="90" align="right"><? echo number_format($job_mkt_required,2,'.',''); ?></td>
                            <td width="100" align="right"><? echo number_format($job_mkt_required_cost,2,'.',''); ?></td>
                            <td width="90" align="right"><? echo number_format($yarn_iss_qty,2,'.',''); ?></td>
                            <td align="right" style="padding-right:2px"><? echo number_format($yarn_iss_cost,2,'.',''); ?></td>
						</tr>
					<?
						$i++;
						$tot_mkt_required+=$job_mkt_required; 
						$tot_required_cost+=$job_mkt_required_cost;
						$tot_yarn_iss_qty+=$yarn_iss_qty; 
						$tot_yarn_iss_cost+=$yarn_iss_cost; 
					}
					?>
                	<tfoot>
                        <th colspan="5">Total</th>
                        <th><? echo $tot_po_qnty; ?></th>
                        <th colspan="3">&nbsp;</th>
                        <th><? echo number_format($tot_mkt_required,2,'.',''); ?></th>
                        <th><? echo number_format($tot_required_cost,2,'.',''); ?></th>
                        <th><? echo number_format($tot_yarn_iss_qty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_yarn_iss_cost,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
<?
	exit();
}

if($action=="mkt_yarn_cost")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	
	$data=explode("**",$mkt_data);
	$grey_yarn_cost=$data[0];
	$dyed_yarn_cost=$data[1];
	$total=$grey_yarn_cost+$dyed_yarn_cost;
?>

    <div align="center">
        <fieldset style="width:620px;">
        	<table class="rpt_table" width="600" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                    <th width="200">Grey Yarn Cost</th>
                    <th width="200">Dyed Yarn Cost</th>
                    <th width="">Total</th>
                </thead>
                <tr bgcolor="#FFFFFF">
                	<td align="right" style="padding-right:3px"><? echo number_format($grey_yarn_cost,2); ?></td>
                    <td align="right" style="padding-right:3px"><? echo number_format($dyed_yarn_cost,2); ?></td>
                    <td align="right" style="padding-right:3px"><? echo number_format($total,2); ?></td>
                </tr>
            </table>	
        </fieldset>
    </div>
<?
	exit();
}

if($action=="knitting_cost")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	
	$exchange_rate=76;
	$costing_per_arr=return_library_array( "select job_no, costing_per_id from wo_pre_cost_dtls where status_active=1 and is_deleted=0", "job_no", "costing_per_id");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	
	$subconInBillDataArray=sql_select("select b.order_id, sum(b.amount) AS knit_bill, sum(b.delivery_qty) as qty from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id=2 group by b.order_id");
	foreach($subconInBillDataArray as $subRow)
	{
		$subconCostArray[$subRow[csf('order_id')]]['amnt']=$subRow[csf('knit_bill')];
		$subconCostArray[$subRow[csf('order_id')]]['qty']=$subRow[csf('qty')];
	}	
	
	$subconOutBillDataArray=sql_select("select b.order_id, sum(b.amount) AS knit_bill, sum(b.receive_qty) as qty from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.process_id=2 and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id");
	foreach($subconOutBillDataArray as $subRow)
	{
		//$subconCostArray[$subRow[csf('order_id')]]['amnt']+=$subRow[csf('knit_bill')];
		//$subconCostArray[$subRow[csf('order_id')]]['qty']+=$subRow[csf('qty')];
	}
	
	$bookingArray=return_library_array( "select b.po_break_down_id, sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id) and a.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id", "po_break_down_id", "qnty");
	
	$greyProdArray=return_library_array( "select po_breakdown_id, sum(quantity) as qnty from order_wise_pro_details where po_breakdown_id in($po_id) and entry_form=2 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "qnty");
	
	$knitCostArray=return_library_array( "select job_no, sum(amount) AS knit_charge from wo_pre_cost_fab_conv_cost_dtls where cons_process=1 and status_active=1 and is_deleted=0 group by job_no", "job_no", "knit_charge");
?>
	<script>
    function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('main_body').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="290px";
	}
	function openmypage_bill(po_id,type,tittle)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'woven_post_costing_report_controller.php?po_id='+po_id+'&action='+type, tittle, 'width=560px, height=350px, center=1, resize=0, scrolling=0', '../../../');
	}
    </script>
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="main_body" style="width:1050px;">
        <fieldset style="width:1050px;">
        	<table class="rpt_table" width="1030" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="40">SL</th>
                    <th width="60">Buyer</th>
                    <th width="80">PO No</th>
                    <th width="50">Year</th>
                    <th width="60">Job No</th>
                    <th width="80">Style Name</th>
                    <th width="110">Gmts Item</th>
                    <th width="90">Order Qty</th>
                    <th width="80">Booking Qty</th>
                    <th width="80">Grey Prod.</th>
                    <th width="90">Knitting Cost</th>
                    <th width="80">Fabric Bill Qty</th>
                    <th>Bill Amount (USD)</th>
                </thead>
            </table>	
            <div style="width:1050px; max-height:290px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="1030" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
					else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
					else $year_field="";//defined Later
						
					$sql="select a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id, b.po_number, b.pub_shipment_date, b.po_quantity, b.unit_price, b.po_total_price, b.shiping_status from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_id) order by b.pub_shipment_date";
					$result=sql_select($sql);
					$i=1; $tot_po_qnty=0; $tot_booking_qnty=0; $tot_greyProd_qnty=0; $tot_knitCost=0; $tot_knitbill=0; $tot_knitQty=0;
					foreach($result as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$gmts_item='';
						$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
						}
						
						$dzn_qnty=0;
						$costing_per_id=$costing_per_arr[$row[csf('job_no')]];
						if($costing_per_id==1)
						{
							$dzn_qnty=12;
						}
						else if($costing_per_id==3)
						{
							$dzn_qnty=12*2;
						}
						else if($costing_per_id==4)
						{
							$dzn_qnty=12*3;
						}
						else if($costing_per_id==5)
						{
							$dzn_qnty=12*4;
						}
						else
						{
							$dzn_qnty=1;
						}
						
						$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
						$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
						$tot_po_qnty+=$order_qnty_in_pcs;
						$bookingQty=$bookingArray[$row[csf('id')]];
						$tot_booking_qnty+=$bookingQty;
						$greyProdQty=$greyProdArray[$row[csf('id')]];
						$tot_greyProd_qnty+=$greyProdQty;
						$knitCost=($order_qnty_in_pcs/$dzn_qnty)*$knitCostArray[$row[csf('job_no')]];
						$tot_knitCost+=$knitCost;
						$knitQty=$subconCostArray[$row[csf('id')]]['qty'];
						$knitbill=$subconCostArray[$row[csf('id')]]['amnt']/$exchange_rate;
						$tot_knitQty+=$knitQty;
						$tot_knitbill+=$knitbill;
						
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="60" style="word-break:break-all;"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                            <td width="80" style="word-break:break-all;"><? echo $row[csf('po_number')]; ?></td>
                            <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                            <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                            <td width="80" style="word-break:break-all;"><? echo $row[csf('style_ref_no')]; ?></td>
                            <td width="110" style="word-break:break-all;"><? echo $gmts_item; ?></td>
                            <td width="90" align="right"><? echo $order_qnty_in_pcs; ?></td>
							<td width="80" align="right"><? echo number_format($bookingQty,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($greyProdQty,2,'.',''); ?></td>
                            <td width="90" align="right"><? echo number_format($knitCost,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($knitQty,2,'.',''); ?></td>
							<td align="right"><a href="##" onClick="openmypage_bill('<? echo $row[csf('id')]; ?>','knitting_bill','Knitting bill Details')"><? echo number_format($knitbill,2,'.',''); ?></a></td>
						</tr>
					<?
					$i++;
					}
					?>
                	<tfoot>
                        <th colspan="7">Total</th>
                        <th><? echo $tot_po_qnty; ?></th>
                        <th><? echo number_format($tot_booking_qnty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_greyProd_qnty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_knitCost,2,'.',''); ?></th>
                        <th><? echo number_format($tot_knitQty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_knitbill,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
<?
	exit();
}

if($action=="knitting_bill")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$exchange_rate=76;
	$costing_per_arr=return_library_array( "select job_no, costing_per_id from wo_pre_cost_dtls where status_active=1 and is_deleted=0", "job_no", "costing_per_id");
	
	$subconInBillDataArray=sql_select("select a.id as mst_id, a.bill_no, a.company_id, a.party_source, sum(b.delivery_qty) as qty, sum(b.amount) AS knit_bill from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id=2 group by a.id, a.bill_no, a.company_id, a.party_source");// b.order_id, b.currency_id
	/*foreach($subconInBillDataArray as $subRow)
	{
		$subconCostArray[$subRow[csf('order_id')]]['amnt']=$subRow[csf('knit_bill')];
		$subconCostArray[$subRow[csf('order_id')]]['qty']=$subRow[csf('qty')];
	}*/	
	
	$subconOutBillDataArray=sql_select("select a.id as mst_id, a.bill_no, sum(b.receive_qty) as qty, sum(b.amount) AS knit_bill from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.process_id=2 and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.bill_no");// b.order_id, b.currency_id
	/*foreach($subconOutBillDataArray as $subRow)
	{
		$subconCostArray[$subRow[csf('order_id')]]['amnt']+=$subRow[csf('knit_bill')];
		$subconCostArray[$subRow[csf('order_id')]]['qty']+=$subRow[csf('qty')];
	}*/
	
	
	$bookingArray=return_library_array( "select b.po_break_down_id, sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id) and a.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id", "po_break_down_id", "qnty");
	
	$greyProdArray=return_library_array( "select po_breakdown_id, sum(quantity) as qnty from order_wise_pro_details where po_breakdown_id in($po_id) and entry_form=2 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "qnty");
	
	$knitCostArray=return_library_array( "select job_no, sum(amount) AS knit_charge from wo_pre_cost_fab_conv_cost_dtls where cons_process=1 and status_active=1 and is_deleted=0 group by job_no", "job_no", "knit_charge");
?>
	<script>
    function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		document.getElementById('scroll_body2').style.overflow="auto";
		document.getElementById('scroll_body2').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('main_body').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="290px";
		document.getElementById('scroll_body2').style.overflowY="scroll";
		document.getElementById('scroll_body2').style.maxHeight="290px";
	}
	
	function print_report(data,party_source)
	{
		//alert("su..re");
		var report_title="Knitting Bill";
		var show_val_column='';
		if(party_source==1)
		{
			var r=confirm("Press \"OK\" to open with Order Comments\nPress \"Cancel\" to open without Order Comments");
			if (r==true)
			{
				show_val_column="1";
			}
			else
			{
				show_val_column="0";
			}
		}
		else show_val_column="0";
		var data=data+"*"+report_title+"*"+show_val_column;
		window.open("../../../../subcontract_bill/requires/knitting_bill_issue_controller.php?data="+data+'&action=knitting_bill_print', true );
	}
    </script>
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="main_body" style="width:470px;">
    
        <fieldset style="width:470px;">
        <legend>In Bound Bill</legend>
        	<table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="50">SL</th>
                    <th width="150">Bill No</th>
                    <th width="100">Bill Quantity</th>
                    <th>Bill Amount (USD)</th>
                </thead>
            </table>	
            <div style="width:470px; max-height:290px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					$i=1;
					$tot_bill_qnty=$tot_bill_amt=0;
					foreach($subconInBillDataArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$knitbill=$row[csf('knit_bill')]/$exchange_rate;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="50"><? echo $i; ?></td>
							<td width="150" style="word-break:break-all;"><a href="##" onClick="print_report('<?php echo $row[csf('company_id')]."*".$row[csf('mst_id')]."*".$row[csf('bill_no')]; ?>','<?php echo $row[csf('party_source')]; ?>')"><? echo $row[csf('bill_no')]; ?></a></td>
                            <td width="100" align="right"><? echo number_format($row[csf('qty')],2,'.',''); $tot_bill_qnty+=$row[csf('qty')]; ?></td>
							<td align="right"><? echo number_format($knitbill,2,'.',''); $tot_bill_amt+=$knitbill; ?></td>
						</tr>
						<?
                        $i++;
					}
					?>
                	<tfoot>
                        <th colspan="2">Total</th>
                        <th><? echo number_format($tot_bill_qnty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_bill_amt,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
            <br>
        <legend>Out Bound Bill</legend>
        	<table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="50">SL</th>
                    <th width="150">Bill No</th>
                    <th width="100">Bill Quantity</th>
                    <th>Bill Amount (USD)</th>
                </thead>
            </table>	
            <div style="width:470px; max-height:290px; overflow-y:scroll" id="scroll_body2">
                <table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					$i=1;
					$tot_bill_qnty=$tot_bill_amt=0;
					foreach($subconOutBillDataArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$knitbill=$row[csf('knit_bill')]/$exchange_rate;	
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="50"><? echo $i; ?></td>
							<td width="150" style="word-break:break-all;"><? echo $row[csf('bill_no')]; ?></td>
                            <td width="100" align="right"><? echo number_format($row[csf('qty')],2,'.',''); $tot_bill_qnty+=$row[csf('qty')]; ?></td>
							<td align="right"><? echo number_format($knitbill,2,'.',''); $tot_bill_amt+=$knitbill; ?></td>
						</tr>
						<?
                        $i++;
					}
					?>
                	<tfoot>
                        <th colspan="2">Total</th>
                        <th><? echo number_format($tot_bill_qnty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_bill_amt,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
<?
	exit();
}

if($action=="dye_fin_cost")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	
	$exchange_rate=76;
	$costing_per_arr=return_library_array( "select job_no, costing_per_id from wo_pre_cost_dtls where status_active=1 and is_deleted=0", "job_no", "costing_per_id");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	
	$subconInBillDataArray=sql_select("select b.order_id, sum(b.amount) AS knit_bill, sum(b.delivery_qty) as qty from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id=4 group by b.order_id");
	foreach($subconInBillDataArray as $subRow)
	{
		$subconCostArray[$subRow[csf('order_id')]]['amnt']=$subRow[csf('knit_bill')];
		$subconCostArray[$subRow[csf('order_id')]]['qty']=$subRow[csf('qty')];
	}	
	
	$subconOutBillDataArray=sql_select("select b.order_id, sum(b.amount) AS knit_bill, sum(b.receive_qty) as qty from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.process_id=4 and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id");
	foreach($subconOutBillDataArray as $subRow)
	{
		$subconCostArray[$subRow[csf('order_id')]]['amnt']+=$subRow[csf('knit_bill')];
		$subconCostArray[$subRow[csf('order_id')]]['qty']+=$subRow[csf('qty')];
	}
	
	$bookingArray=array();
	$bookingDataArray=sql_select( "select b.po_break_down_id, sum(b.grey_fab_qnty) as grey_qnty, sum(b.fin_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id) and a.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id");
	foreach($bookingDataArray as $bokRow)
	{
		$bookingArray[$bokRow[csf('po_break_down_id')]]['grey']=$bokRow[csf('grey_qnty')];
		$bookingArray[$bokRow[csf('po_break_down_id')]]['fin']=$bokRow[csf('qnty')];
	}
	
	$finProdArray=return_library_array( "select po_breakdown_id, sum(quantity) as qnty from order_wise_pro_details where po_breakdown_id in($po_id) and entry_form=7 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "qnty");
	
	$finCostArray=return_library_array( "select job_no, sum(amount) AS dye_fin_charge from wo_pre_cost_fab_conv_cost_dtls where cons_process not in(1,2,30,35) and status_active=1 and is_deleted=0 group by job_no", "job_no", "dye_fin_charge");

?>
<script>
	function openmypage_bill(po_id,type,tittle)
	{
		//alert("su..re"); return;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'woven_post_costing_report_controller.php?po_id='+po_id+'&action='+type, tittle, 'width=560px, height=350px, center=1, resize=0, scrolling=0', '../../../');
	}
</script>
    <div>
        <fieldset style="width:1113px;">
        	<table class="rpt_table" width="1110" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="40">SL</th>
                    <th width="60">Buyer</th>
                    <th width="80">PO No</th>
                    <th width="50">Year</th>
                    <th width="60">Job No</th>
                    <th width="80">Style Name</th>
                    <th width="110">Gmts Item</th>
                    <th width="90">Order Qty</th>
                    <th width="80">Booking Qty(Grey)</th>
                    <th width="80">Booking Qty(Fin)</th>
                    <th width="80">Finish Prod.</th>
                    <th width="90">Dye & Fin Cost</th>
                    <th width="80">Fabric Bill Qty</th>
                    <th>Bill Amount (USD)</th>
                </thead>
            </table>	
            <div style="width:1110px; max-height:290px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="1090" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					/*$po_ids=count(array_unique(explode(",",$po_id)));
					//echo $all_po_id;
					$all_po_ids=chop($all_po_id,','); $poIds_cond="";
					//print_r($all_po_ids);
					if($db_type==2 && $po_ids>990)
					{
						$poIds_cond=" and (";
						$poIdsArr=array_chunk(explode(",",$all_po_ids),990);
						//print_r($gate_outIds);
						foreach($poIdsArr as $ids)
						{
							$ids=implode(",",$ids);
							$poIds_cond.=" po_id  in($ids) or ";
						}
						$poIds_cond=chop($poIds_cond,'or ');
						$poIds_cond.=")";
					}
					else
					{
						$poIds_cond=" and  po_id  in($all_po_id)";
					}*/
					if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
					else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
					else $year_field="";//defined Later
						
					$sql="select a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id, b.po_number, b.pub_shipment_date, b.po_quantity, b.plan_cut , b.unit_price, b.po_total_price, b.shiping_status from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_id) order by b.pub_shipment_date";
					$result=sql_select($sql);
					$i=1; $tot_po_qnty=0; $tot_booking_qnty=0; $tot_finProd_qnty=0; $tot_finCost=0; $tot_finbill=0; $tot_knitQty=0;
					$condition= new condition();
					
					/* $poids=explode(",",$po_id);
					 foreach( $poids as $po)
					 {
						$condition->po_id(" in($po)");  
					 }*/
					if(trim(str_replace("'","",$po_id))!='')
					 {
						$condition->po_id(" in($po_id)"); 
					 }
					  $condition->init();
					  $conversion= new conversion($condition);
					//  echo $conversion->getQuery();
				 	$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
					
					  
					foreach($result as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$gmts_item='';
						$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
						}
						
						$dzn_qnty=0;
						$costing_per_id=$costing_per_arr[$row[csf('job_no')]];
						if($costing_per_id==1)
						{
							$dzn_qnty=12;
						}
						else if($costing_per_id==3)
						{
							$dzn_qnty=12*2;
						}
						else if($costing_per_id==4)
						{
							$dzn_qnty=12*3;
						}
						else if($costing_per_id==5)
						{
							$dzn_qnty=12*4;
						}
						else
						{
							$dzn_qnty=1;
						}
						
						$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
						$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
						$tot_po_qnty+=$order_qnty_in_pcs;
						
						$plun_qnty_in_pcs=$row[csf('plan_cut')]*$row[csf('ratio')];
						
						$bookingQty=$bookingArray[$row[csf('id')]]['fin'];
						$tot_booking_qnty+=$bookingQty;
						
						$bookingQtyGrey=$bookingArray[$row[csf('id')]]['grey'];
						$tot_booking_grey_qnty+=$bookingQtyGrey;
						
						$finProdQty=$finProdArray[$row[csf('id')]];
						$tot_finProd_qnty+=$finProdQty;
						
						$finCost=0;$not_yarn_dyed_cost_arr=array(1,2,30,35);
						foreach($conversion_cost_head_array as $process_id=>$val)
						{
							if(!in_array($process_id,$not_yarn_dyed_cost_arr))
							{
								$finCost+=$conversion_costing_arr_process[$row[csf('id')]][$process_id];
							}
						}	
						//$finCost=($order_qnty_in_pcs/$dzn_qnty)*$finCostArray[$row[csf('job_no')]];
						//$finCost=($plun_qnty_in_pcs/$dzn_qnty)*$finCostArray[$row[csf('job_no')]];
						$tot_finCost+=$finCost;
						
						$finQty=$subconCostArray[$row[csf('id')]]['qty'];
						$finbill=$subconCostArray[$row[csf('id')]]['amnt']/$exchange_rate;
						
						$tot_finQty+=$finQty;
						$tot_finbill+=$finbill;
						
						
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="60" style="word-break:break-all;"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                            <td width="80" style="word-break:break-all;"><? echo $row[csf('po_number')]; ?></td>
                            <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                            <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                            <td width="80" style="word-break:break-all;"><? echo $row[csf('style_ref_no')]; ?></td>
                            <td width="110" style="word-break:break-all;"><? echo $gmts_item; ?></td>
                            <td width="90" align="right"><? echo $order_qnty_in_pcs; ?></td>
							<td width="80" align="right"><? echo number_format($bookingQtyGrey,2,'.',''); ?></td>
                            <td width="80" align="right"><? echo number_format($bookingQty,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($finProdQty,2,'.',''); ?></td>
                            <td width="90" align="right"><? echo number_format($finCost,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($finQty,2,'.',''); ?></td>
							<td align="right"><a href="##" onClick="openmypage_bill('<? echo $row[csf('id')]; ?>','dyeing_bill','Dyeing bill Details')"><? echo number_format($finbill,2,'.',''); ?></a></td>
						</tr>
					<?
					$i++;
					}
					?>
                	<tfoot>
                        <th colspan="7">Total</th>
                        <th><? echo $tot_po_qnty; ?></th>
                        <th><? echo number_format($tot_booking_grey_qnty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_booking_qnty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_finProd_qnty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_finCost,2,'.',''); ?></th>
                        <th><? echo number_format($tot_finQty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_finbill,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
<?
	exit();
}

if($action=="dyeing_bill")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	$exchange_rate=76;
	$costing_per_arr=return_library_array( "select job_no, costing_per_id from wo_pre_cost_dtls where status_active=1 and is_deleted=0", "job_no", "costing_per_id");
	
	$subconInBillDataArray=sql_select("select a.id as mst_id, a.bill_no, a.company_id, a.party_source, sum(b.delivery_qty) as qty, sum(b.amount) AS knit_bill from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id=4 group by a.id, a.bill_no, a.company_id, a.party_source");//, b.order_id, b.currency_id
	
	$subconOutBillDataArray=sql_select("select a.id as mst_id, a.bill_no, sum(b.receive_qty) as qty, sum(b.amount) AS knit_bill from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.process_id=4 and b.order_id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.bill_no");//, b.currency_id, b.order_id
	
	$bookingArray=return_library_array( "select b.po_break_down_id, sum(b.grey_fab_qnty) as qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id) and a.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id", "po_break_down_id", "qnty");
	
	$greyProdArray=return_library_array( "select po_breakdown_id, sum(quantity) as qnty from order_wise_pro_details where po_breakdown_id in($po_id) and entry_form=2 and status_active=1 and is_deleted=0 group by po_breakdown_id", "po_breakdown_id", "qnty");
	
	$knitCostArray=return_library_array( "select job_no, sum(amount) AS knit_charge from wo_pre_cost_fab_conv_cost_dtls where cons_process=1 and status_active=1 and is_deleted=0 group by job_no", "job_no", "knit_charge");
?>
	<script>
    function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		document.getElementById('scroll_body2').style.overflow="auto";
		document.getElementById('scroll_body2').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('main_body').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="290px";
		document.getElementById('scroll_body2').style.overflowY="scroll";
		document.getElementById('scroll_body2').style.maxHeight="290px";
	}
	
	function print_report(data)
	{
		//alert("su..re");
		var report_title="Dyeing And Finishing Bill";
		var show_val_column=1;
		var data=data+"*"+report_title+"*"+show_val_column;
		window.open("../../../../subcontract_bill/requires/sub_fabric_finishing_bill_issue_controller.php?data="+data+'&action=fabric_finishing_print', true );
	}
	
    </script>
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
    <div id="main_body" style="width:470px;">
        <fieldset style="width:470px;">
        <legend>In Bound Bill</legend>
        	<table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="50">SL</th>
                    <th width="150">Bill No</th>
                    <th width="100">Bill Quantity</th>
                    <th>Bill Amount (USD)</th>
                </thead>
            </table>	
            <div style="width:470px; max-height:290px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					$i=1;
					$tot_bill_qnty=$tot_bill_amt=0;
					foreach($subconInBillDataArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$knitbill=$row[csf('knit_bill')]/$exchange_rate;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="50"><? echo $i; ?></td>
							<td width="150" style="word-break:break-all;"><a href="##" onClick="print_report('<?php echo $row[csf('company_id')]."*".$row[csf('mst_id')]."*".$row[csf('bill_no')]; ?>')"><? echo $row[csf('bill_no')]; ?></a></td>
                            <td width="100" align="right"><? echo number_format($row[csf('qty')],2,'.',''); $tot_bill_qnty+=$row[csf('qty')]; ?></td>
							<td align="right"><? echo number_format($knitbill,2,'.',''); $tot_bill_amt+=$knitbill; ?></td>
						</tr>
						<?
                        $i++;
					}
					?>
                	<tfoot>
                        <th colspan="2">Total</th>
                        <th><? echo number_format($tot_bill_qnty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_bill_amt,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
            <br>
        <legend>Out Bound Bill</legend>
        	<table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="50">SL</th>
                    <th width="150">Bill No</th>
                    <th width="100">Bill Quantity</th>
                    <th>Bill Amount (USD)</th>
                </thead>
            </table>	
            <div style="width:470px; max-height:290px; overflow-y:scroll" id="scroll_body2">
                <table class="rpt_table" width="450" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					$i=1;
					$tot_bill_qnty=$tot_bill_amt=0;
					foreach($subconOutBillDataArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$knitbill=$row[csf('knit_bill')]/$exchange_rate;	
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="50"><? echo $i; ?></td>
							<td width="150" style="word-break:break-all;"><? echo $row[csf('bill_no')]; ?></td>
                            <td width="100" align="right"><? echo number_format($row[csf('qty')],2,'.',''); $tot_bill_qnty+=$row[csf('qty')]; ?></td>
							<td align="right"><? echo number_format($knitbill,2,'.',''); $tot_bill_amt+=$knitbill; ?></td>
						</tr>
						<?
                        $i++;
					}
					?>
                	<tfoot>
                        <th colspan="2">Total</th>
                        <th><? echo number_format($tot_bill_qnty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_bill_amt,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
	<?
	exit();
}

if($action=="yarn_cost_actual")
{
	echo load_html_head_contents("Yarn Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	
	$supplier_array=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
	$receive_array=array();
	$exchange_rate=76;
	$receive_array=array();
	$sql_receive="select a.currency_id,a.receive_purpose,b.prod_id, (b.order_qnty) as qty, (b.order_amount) as amnt,b.cons_quantity,b.cons_amount from inv_receive_master a, inv_transaction b where  a.id=b.mst_id and b.transaction_type=1 and b.item_category=1 and b.status_active=1 and b.is_deleted=0";
	$resultReceive = sql_select($sql_receive);
	foreach($resultReceive as $invRow)
	{
		
		if($invRow[csf('currency_id')]==1)//Taka
		{
			$avg_rate=$invRow[csf('cons_amount')]/$invRow[csf('cons_quantity')];
			$receive_array[$invRow[csf('prod_id')]]=$avg_rate/$exchange_rate;
		}
		else if($invRow[csf('currency_id')]==2)//USD
		{
			$avg_rate=$invRow[csf('amnt')]/$invRow[csf('qty')];
			$receive_array[$invRow[csf('prod_id')]]=$avg_rate;
		}
		else
		{
			
			$receive_array[$invRow[csf('prod_id')]]=$avg_rate;
		}
	}
	
	
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
	<div style="width:860px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:860px; margin-left:7px">
		<div id="report_container">
            <table class="rpt_table" width="855" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
					<th colspan="8"><b>Yarn Issue</b></th>
				</thead>
				<thead>
                	<th width="40">SL</th>
                    <th width="110">Issue Id</th>
                    <th width="80">Issue Date</th>
                    <th width="70">Supplier</th>
                    <th width="180">Yarn Description</th>
                    <th width="90">Issue Qty.</th>
                    <th width="80">Avg. Rate (USD)</th>
                    <th>Cost ($)</th>
				</thead>
                <?
                $i=1; $total_yarn_issue_qnty=0; $total_yarn_cost=0;
				$sql="select a.issue_number, a.issue_date, c.supplier_id, sum(b.quantity) as issue_qnty, c.id as prod_id, c.product_name_details, c.avg_rate_per_unit from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and c.item_category_id=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id=$po_id and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.issue_number, a.issue_date, c.supplier_id, c.product_name_details, c.avg_rate_per_unit";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
				
                    $yarn_issued=$row[csf('issue_qnty')];
					$total_yarn_issue_qnty+=$yarn_issued;
					
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                        <td width="70"><p><? echo $supplier_array[$row[csf('supplier_id')]]; ?></p></td>
                        <td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="90">
							<? 
								echo number_format($yarn_issued,2); 
                            ?>
                        </td>
                        <td align="right" width="80">
							<? 
								if($receive_array[$row[csf('prod_id')]]>0)
								{
									$avg_rate=$receive_array[$row[csf('prod_id')]];
								}
								else
								{
									$avg_rate=$row[csf('avg_rate_per_unit')]/$exchange_rate;
								}
								echo number_format($avg_rate,2); 
                            ?>&nbsp;
                        </td>
                        <td align="right">
							<?
								$yarn_cost=$yarn_issued*$avg_rate;
								echo number_format($yarn_cost,2); 
								$total_yarn_cost+=$yarn_cost;
                            ?>
                        </td>
                    </tr>
                <?
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty,2);?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($total_yarn_cost,2);?></td>
                </tr>
                <thead>
                    <th colspan="8"><b>Yarn Return</b></th>
                </thead>
                <thead>
                	<th>SL</th>
                	<th width="110">Return Id</th>
                    <th width="80">Return Date</th>
                    <th width="70">Supplier</th>
                    <th width="180">Yarn Description</th>
                    <th width="90">Return Qty.</th>
                    <th width="80">Avg. Rate (USD)</th>
                    <th>Cost ($)</th>
               </thead>
                <?
                $total_yarn_return_qnty=0; $total_yarn_return_cost=0;
				$sql="select a.recv_number, a.receive_date, sum(b.quantity) as returned_qnty, c.id as prod_id, c.supplier_id, c.product_name_details, c.avg_rate_per_unit from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and c.item_category_id=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id=$po_id and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.recv_number, a.receive_date, c.supplier_id, c.product_name_details, c.avg_rate_per_unit";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
				
                    $yarn_returned=$row[csf('returned_qnty')];
					$total_yarn_return_qnty+=$yarn_returned;
					
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
						<td width="70"><p><? echo $supplier_array[$row[csf('supplier_id')]]; ?></p></td>
                        <td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="90">
							<? 
								echo number_format($yarn_returned,2); 
                            ?>
                        </td>
                        <td align="right" width="80">
							<? 
								if($receive_array[$row[csf('prod_id')]]>0)
								{
									$avg_rate=$receive_array[$row[csf('prod_id')]];
								}
								else
								{
									$avg_rate=$row[csf('avg_rate_per_unit')]/$exchange_rate;
								}
								echo number_format($avg_rate,2); 
                            ?>&nbsp;
                        </td>
                        <td align="right" width="80">
							<?
								$yarn_return_cost=$yarn_returned*$avg_rate;
								echo number_format($yarn_return_cost,2); 
								$total_yarn_return_cost+=$yarn_return_cost;
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_return_qnty,2);?></td>
                     <td>&nbsp;</td>
                    <td align="right"><? echo number_format($total_yarn_return_cost,2);?></td>
                </tr>
                <thead>
                    <th colspan="8"><b>Transfer In</b></th>
                </thead>
                <thead>
                	<th>SL</th>
                	<th width="110">Transfer Id</th>
                    <th width="80">Transfer Date</th>
                    <th width="70">Supplier</th>
                    <th width="180">Yarn Description</th>
                    <th width="90">Transfer Qty.</th>
                    <th width="80">Avg. Rate (USD)</th>
                    <th>Cost ($)</th>
               	</thead>
                <?
                $i=1; $total_trans_in_qnty=0; $total_trans_in_cost=0;
				$sql="select a.transfer_system_id, a.transfer_date, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.supplier_id, d.product_name_details, d.avg_rate_per_unit from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=1 and a.transfer_criteria=4 and c.trans_type=5 and c.entry_form=11 and c.po_breakdown_id=$po_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.transfer_system_id, a.transfer_date, b.from_prod_id, d.supplier_id, d.product_name_details, d.avg_rate_per_unit";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="180"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="70"><p><? echo $supplier_array[$row[csf('supplier_id')]]; ?></p></td>
                        <td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="90"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                        <td align="right" width="80">
							<? 
								if($receive_array[$row[csf('from_prod_id')]]>0)
								{
									$avg_rate=$receive_array[$row[csf('from_prod_id')]];
								}
								else
								{
									$avg_rate=$row[csf('avg_rate_per_unit')]/$exchange_rate;
								}
								echo number_format($avg_rate,2); 
                            ?>&nbsp;
                        </td>
                        <td>
							<?
								$yarn_trans_cost=$row[csf('transfer_qnty')]*$avg_rate;
								echo number_format($yarn_trans_cost,2); 
                            ?>
                        </td>
                    </tr>
                <?
					$total_trans_in_qnty+=$row[csf('transfer_qnty')];
					$total_trans_in_cost+=$yarn_trans_cost;
                	$i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_in_qnty,2);?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($total_trans_in_cost,2);?></td>
                </tr>
                <thead>
                    <th colspan="8"><b>Transfer Out</b></th>
                </thead>
                <thead>
                	<th>SL</th>
                	<th width="110">Transfer Id</th>
                    <th width="80">Transfer Date</th>
                    <th width="70">Supplier</th>
                    <th width="180">Yarn Description</th>
                    <th width="90">Transfer Qty.</th>
                    <th width="80">Avg. Rate (USD)</th>
                    <th>Cost ($)</th>
               	</thead>
                <?
                $i=1; $total_trans_out_qnty=0; $total_trans_out_cost=0;
				$sql="select a.transfer_system_id, a.transfer_date, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details, d.supplier_id, d.avg_rate_per_unit from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=1 and a.transfer_criteria=4 and c.trans_type=6 and c.entry_form=11 and c.po_breakdown_id=$po_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, b.from_prod_id, d.product_name_details, d.supplier_id, d.avg_rate_per_unit";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="180"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="70"><p><? echo $supplier_array[$row[csf('supplier_id')]]; ?></p></td>
                        <td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="90"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                        <td align="right" width="80">
							<? 
								if($receive_array[$row[csf('from_prod_id')]]>0)
								{
									$avg_rate=$receive_array[$row[csf('from_prod_id')]];
								}
								else
								{
									$avg_rate=$row[csf('avg_rate_per_unit')]/$exchange_rate;
								}
								echo number_format($avg_rate,2); 
                            ?>&nbsp;
                        </td>
                        <td align="right">
							<?
								$yarn_trans_cost_out=$row[csf('transfer_qnty')]*$avg_rate;
								echo number_format($yarn_trans_cost_out,2); 
                            ?>
                        </td>
                    </tr>
                <?
					$total_trans_out_qnty+=$row[csf('transfer_qnty')];
					$total_trans_out_cost+=$yarn_trans_cost_out;
                	$i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td >&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_out_qnty,2);?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($total_trans_out_cost,2);?></td>
                </tr>
               	<tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th align="right">Balance</th>
                    <th align="right"><? echo number_format(($total_yarn_issue_qnty+$total_trans_in_qnty)-($total_yarn_return_qnty+$total_trans_out_qnty),2); ?></th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format(($total_yarn_cost+$total_trans_in_cost)-($total_yarn_return_cost+$total_trans_out_cost),2); ?></th>
                </tfoot>
            </table>	
		</div>
	</fieldset>  
<?
exit();
}

if($action=="knit_cost_actual")
{
	echo load_html_head_contents("Knitting Bill Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

	$product_array=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13", "id", "product_name_details");
?>
	<fieldset style="width:870px; margin-left:7px">
        <table class="rpt_table" width="865" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="110">Bill No.</th>
                <th width="80">Bill Date</th>
                <th width="180">Fabric Description</th>
                <th width="110">Fabric Qty (Kg)</th>
                <th width="80">Rate/Kg</th>
                <th width="120">Amount (Taka)</th>
                <th>Amount (USD)</th>
            </thead>
        </table>
        <div style="width:865px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="845" cellpadding="0" cellspacing="0">  
            	<tr bgcolor="#CCDDEE">
					<td colspan="8"><b>Inside Knitting Bill</b></td>
				</tr>  
				<?
                $i=1; $avg_rate=76;
				$total_in_knit_qnty=0; $total_in_knit_cost_tk=0; $total_in_knit_cost_usd=0;
				$total_out_knit_qnty=0; $total_out_knit_cost_tk=0; $total_out_knit_cost_usd=0;
				$total_knit_qnty=0; $total_knit_cost_tk=0; $total_knit_cost_usd=0;
				
                $sql="select a.id, a.bill_no, a.bill_date, b.item_id, b.rate, b.delivery_qty, b.amount as amount from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and b.order_id=$po_id and a.process_id=2 and a.party_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id";
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('bill_no')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('bill_date')]); ?></td>
                        <td width="180"><p><? echo $product_array[$row[csf('item_id')]]; ?></p></td>
                        <td align="right" width="110"><? echo number_format($row[csf('delivery_qty')],2); ?></td>
                        <td align="right" width="80"><? echo number_format($row[csf('rate')],2); ?>&nbsp;</td>
                        <td align="right" width="120"><? echo number_format($row[csf('amount')],2); ?></td>
                        <td align="right">
                            <?
								$amount_usd=$row[csf('amount')]/$avg_rate;
                                echo number_format($amount_usd,2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_in_knit_qnty+=$row[csf('delivery_qty')];
					$total_in_knit_cost_tk+=$row[csf('amount')];
					$total_in_knit_cost_usd+=$amount_usd;
                }
                ?>
                <tr bgcolor="#CCCCCC">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><b>Total</b></td>
                    <td align="right"><? echo number_format($total_in_knit_qnty,2); ?></td>
                    <td align="right"><? echo number_format($total_in_knit_cost_tk/$total_in_knit_qnty,2); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($total_in_knit_cost_tk,2); ?></td>
                    <td align="right"><? echo number_format($total_in_knit_cost_usd,2); ?></td>
                </tr>
                <tr bgcolor="#CCDDEE">
					<td colspan="8"><b>Outside Knitting Bill</b></td>
				</tr>
                <?
                $sql="select a.id, a.bill_no, a.bill_date, b.item_id, b.rate, b.receive_qty, b.amount as amount from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and b.order_id=$po_id and a.process_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id";
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('bill_no')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('bill_date')]); ?></td>
                        <td width="180"><p><? echo $product_array[$row[csf('item_id')]]; ?></p></td>
                        <td align="right" width="110"><? echo number_format($row[csf('receive_qty')],2); ?></td>
                        <td align="right" width="80"><? echo number_format($row[csf('rate')],2); ?>&nbsp;</td>
                        <td align="right" width="120"><? echo number_format($row[csf('amount')],2); ?></td>
                        <td align="right">
                            <?
								$amount_usd=$row[csf('amount')]/$avg_rate;
                                echo number_format($amount_usd,2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_out_knit_qnty+=$row[csf('receive_qty')];
					$total_out_knit_cost_tk+=$row[csf('amount')];
					$total_out_knit_cost_usd+=$amount_usd;
                }
                ?>
                <tr bgcolor="#CCCCCC">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><b>Total</b></td>
                    <td align="right"><? echo number_format($total_out_knit_qnty,2); ?></td>
                    <td align="right"><? echo number_format($total_out_knit_cost_tk/$total_out_knit_qnty,2); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($total_out_knit_cost_tk,2); ?></td>
                    <td align="right"><? echo number_format($total_out_knit_cost_usd,2); ?></td>
                </tr>
                <tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Grand Total</th>
                    <th align="right"><? echo number_format($total_out_knit_qnty+$total_in_knit_qnty,2); ?></th>
                    <th align="right"><? echo number_format(($total_out_knit_cost_tk+$total_in_knit_cost_tk)/($total_out_knit_qnty+$total_in_knit_qnty),2); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($total_out_knit_cost_tk+$total_in_knit_cost_tk,2); ?></th>
                    <th align="right"><? echo number_format($total_out_knit_cost_usd+$total_in_knit_cost_usd,2); ?></th>
                </tfoot>
			</table>
        </div>	
	</fieldset>  
<?
exit();
}

if($action=="dye_finish_cost_actual")
{
	echo load_html_head_contents("Dyeing Bill Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

	$product_array=return_library_array( "select id, product_name_details from product_details_master where item_category_id=2", "id", "product_name_details");
?>
	<fieldset style="width:1070px; margin-left:7px">
        <table class="rpt_table" width="1065" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="110">Bill No.</th>
                <th width="80">Bill Date</th>
                <th width="180">Fabric Description</th>
                <th width="110">Fabric Qty (Kg)</th>
                <th width="80">Main Rate/Kg</th>
                
                <th width="80">Process name</th>
                <th width="80">Additonal Rate</th>
                <th width="80" title="Main Rate/Kg+Additonal Rate">Total Rate</th>
                
                <th width="120" title="Fabric Qty (Kg)*Total Rate">Amount (Taka)</th>
                <th>Amount (USD)</th>
            </thead>
        </table>
        <div style="width:1065px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="1045" cellpadding="0" cellspacing="0">  
            	<tr bgcolor="#CCDDEE">
					<td colspan="11"><b>Inside Dyeing Bill</b></td>
				</tr>  
				<?
                $i=1; $avg_rate=76;
				$total_in_knit_qnty=0; $total_in_knit_cost_tk=0; $total_in_knit_cost_usd=0;
				$total_out_knit_qnty=0; $total_out_knit_cost_tk=0; $total_out_knit_cost_usd=0;
				$total_knit_qnty=0; $total_knit_cost_tk=0; $total_knit_cost_usd=0;
				
                $sql="select a.id, a.bill_no, a.bill_date, b.item_id, b.rate, b.delivery_qty,b.add_process_name,b.add_rate, b.amount as amount from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and b.order_id=$po_id and a.process_id=4 and a.party_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id";
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('bill_no')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('bill_date')]); ?></td>
                        <td width="180"><p><? echo $product_array[$row[csf('item_id')]]; ?></p></td>
                        <td align="right" width="110"><? echo number_format($row[csf('delivery_qty')],2); ?></td>
                        <td align="right" width="80"><? echo number_format($row[csf('rate')],2); ?>&nbsp;</td>
                        
                        <td align="right" width="80"><? echo  $row[csf('add_process_name')]; ?>&nbsp;</td>
                        <td align="right" width="80"><? echo number_format($row[csf('add_rate')],2); ?>&nbsp;</td>
                        <td align="right" width="80">
						<? 
							$totalRate=$row[csf('rate')]+$row[csf('add_rate')]; 
							echo number_format($totalRate,2); 
						?>
                        &nbsp;</td>
                        
                        <td align="right" width="120">
						<? 
							$amount=($totalRate*$row[csf('delivery_qty')]);
							$row[csf('amount')]=$amount;
							echo number_format($row[csf('amount')],2); 
						?>
                        </td>
                        <td align="right">
                            <?
								$amount_usd=$row[csf('amount')]/$avg_rate;
                                echo number_format($amount_usd,2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_in_knit_qnty+=$row[csf('delivery_qty')];
					$total_in_knit_cost_tk+=$row[csf('amount')];
					$total_in_knit_cost_usd+=$amount_usd;
                }
                ?>
                <tr bgcolor="#CCCCCC">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><b>Total</b></td>
                    <td align="right"><? echo number_format($total_in_knit_qnty,2); ?></td>
                    <td align="right"><? echo number_format($total_in_knit_cost_tk/$total_in_knit_qnty,2); ?>&nbsp;</td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td align="right"><? echo number_format($total_in_knit_cost_tk,2); ?></td>
                    <td align="right"><? echo number_format($total_in_knit_cost_usd,2); ?></td>
                </tr>
                <tr bgcolor="#CCDDEE">
					<td colspan="11"><b>Outside Dyeing Bill</b></td>
				</tr>
                <?
                $sql="select a.id, a.bill_no, a.bill_date, b.item_id, b.rate, b.receive_qty, b.amount as amount from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and b.order_id=$po_id and a.process_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id";
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('bill_no')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('bill_date')]); ?></td>
                        <td width="180"><p><? echo $product_array[$row[csf('item_id')]]; ?></p></td>
                        <td align="right" width="110"><? echo number_format($row[csf('receive_qty')],2); ?></td>
                        <td align="right" width="80"><? echo number_format($row[csf('rate')],2); ?>&nbsp;</td>
                        
                        <td align="right" width="80"><? //echo number_format($row[csf('rate')],2); ?>&nbsp;</td>
                        <td align="right" width="80"><? //echo number_format($row[csf('rate')],2); ?>&nbsp;</td>
                        <td align="right" width="80"><? //echo number_format($row[csf('rate')],2); ?>&nbsp;</td>
                        
                        <td align="right" width="120"><? echo number_format($row[csf('amount')],2); ?></td>
                        <td align="right">
                            <?
								$amount_usd=$row[csf('amount')]/$avg_rate;
                                echo number_format($amount_usd,2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_out_knit_qnty+=$row[csf('receive_qty')];
					$total_out_knit_cost_tk+=$row[csf('amount')];
					$total_out_knit_cost_usd+=$amount_usd;
                }
                ?>
                <tr bgcolor="#CCCCCC">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><b>Total</b></td>
                    <td align="right"><? echo number_format($total_out_knit_qnty,2); ?></td>
                    <td align="right"><? echo number_format($total_out_knit_cost_tk/$total_out_knit_qnty,2); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($total_out_knit_cost_tk,2); ?></td>
                    
                    <td align="right"><? //echo number_format($total_out_knit_cost_tk,2); ?></td>
                    <td align="right"><? //echo number_format($total_out_knit_cost_tk,2); ?></td>
                    <td align="right"><? //echo number_format($total_out_knit_cost_tk,2); ?></td>
                    
                    
                    <td align="right"><? echo number_format($total_out_knit_cost_usd,2); ?></td>
                </tr>
                <tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Grand Total</th>
                    <th align="right"><? echo number_format($total_out_knit_qnty+$total_in_knit_qnty,2); ?></th>
                    <th align="right"><? echo number_format(($total_out_knit_cost_tk+$total_in_knit_cost_tk)/($total_out_knit_qnty+$total_in_knit_qnty),2); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($total_out_knit_cost_tk+$total_in_knit_cost_tk,2); ?></th>
                    <th> </th>
                    <th> </th>
                    <th> </th>
                    
                    <th align="right"><? echo number_format($total_out_knit_cost_usd+$total_in_knit_cost_usd,2); ?></th>
                </tfoot>
			</table>
        </div>	
	</fieldset>  
<?
exit();
}

if($action=="fabric_purchase_cost_actual")
{
	echo load_html_head_contents("Fabric Purchase Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	$ex_rate=80;
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
	<div style="width:860px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:860px; margin-left:7px">
		<div id="report_container">
        	<u><b>Knit Fabric Purchase</b></u>
            <table class="rpt_table" width="855" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
                	<th width="40">SL</th>
                    <th width="110">Receive Id</th>
                    <th width="80">Receive Date</th>
                    <th width="280">Fabric Description</th>
                    <th width="110">Receive Qty.</th>
                    <th width="110">Avg. Rate (USD)</th>
                    <th>Cost ($)</th>
				</thead>
                <?
                $i=1; $total_recv_qnty=0; $total_recv_cost=0;
				$sql="select a.id, a.recv_number, a.receive_date, sum(b.quantity) as recv_qnty, sum(d.cons_rate*b.quantity) as amnt, c.id, c.product_name_details from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=1 and d.item_category=2 and c.item_category_id=2 and d.id=b.trans_id and b.trans_type=1 and b.entry_form=37 and a.entry_form=37 and b.po_breakdown_id=$po_id and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.receive_basis!=9 group by a.id, c.id, a.recv_number, a.receive_date, c.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
				
                    $total_recv_qnty+=$row[csf('recv_qnty')];
					
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="280"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="110">
							<? 
								echo number_format($row[csf('recv_qnty')],2); 
                            ?>
                        </td>
                        <td align="right" width="110">
							<? 
								$avg_rate=($row[csf('amnt')]/$row[csf('recv_qnty')])/$ex_rate;
								echo number_format($avg_rate,2); 
                            ?>&nbsp;
                        </td>
                        <td align="right">
							<?
								$cost=$row[csf('recv_qnty')]*$avg_rate;
								//$cost=$row[csf('amnt')]/$ex_rate;
								$total_recv_cost+=$cost;
								echo number_format($cost,2); 
                            ?>
                        </td>
                    </tr>
                <?
                $i++;
                }
                ?>
               	<tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th align="right">Total</th>
                    <th align="right"><? echo number_format($total_recv_qnty,2); ?></th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format($total_recv_cost,2); ?></th>
                </tfoot>
            </table>
            <br>
            <u><b>Woven Fabric Purchase</b></u>
            <table class="rpt_table" width="855" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
                	<th width="40">SL</th>
                    <th width="110">Receive Id</th>
                    <th width="80">Receive Date</th>
                    <th width="280">Fabric Description</th>
                    <th width="110">Receive Qty.</th>
                    <th width="110">Avg. Rate (USD)</th>
                    <th>Cost ($)</th>
				</thead>
                <?
                $i=1; $total_recv_qnty_w=0; $total_recv_cost_w=0;
				$sql="select a.id, a.recv_number, a.receive_date, sum(b.quantity) as recv_qnty, sum(d.cons_rate*b.quantity) as amnt, c.id, c.product_name_details from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=1 and d.item_category=3 and c.item_category_id=3 and d.id=b.trans_id and b.trans_type=1 and b.entry_form=17 and a.entry_form=17 and b.po_breakdown_id=$po_id and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.id, a.recv_number, a.receive_date, c.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
				
                    $total_recv_qnty_w+=$row[csf('recv_qnty')];
					
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="280"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="110">
							<? 
								echo number_format($row[csf('recv_qnty')],2); 
                            ?>
                        </td>
                        <td align="right" width="110">
							<? 
								$avg_rate=($row[csf('amnt')]/$row[csf('recv_qnty')])/$ex_rate;
								echo number_format($avg_rate,2); 
                            ?>&nbsp;
                        </td>
                        <td align="right">
							<?
								$cost=$row[csf('recv_qnty')]*$avg_rate;
								//$cost=$row[csf('amnt')]/$ex_rate;
								$total_recv_cost_w+=$cost;
								echo number_format($cost,2); 
                            ?>
                        </td>
                    </tr>
                <?
                $i++;
                }
                ?>
               	<tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th align="right">Total</th>
                    <th align="right"><? echo number_format($total_recv_qnty_w,2); ?></th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format($total_recv_cost_w,2); ?></th>
                </tfoot>
            </table>
            <table class="tbl_bottom" width="855" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<tr>
                	<td width="40">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="280">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="110">Grand Total</td>
                    <td align="right"><? echo number_format($total_recv_cost+$total_recv_cost_w,2); ?></td>
                </tr>
            </table>	
		</div>
	</fieldset>  
<?
exit();
}

if($action=="fabric_purchase_cost")
{
	echo load_html_head_contents("Fabric Purchase Cost Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	
	$exchange_rate=76;
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$sql_fin_purchase="select b.po_breakdown_id, sum(b.quantity) as qty, sum(a.cons_rate*b.quantity) as finish_purchase_amnt from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.receive_basis<>9 and a.item_category=2 and a.transaction_type=1 and b.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id in($po_id) group by b.po_breakdown_id";
	$dataArrayFinPurchase=sql_select($sql_fin_purchase);
	foreach($dataArrayFinPurchase as $finRow)
	{
		$finish_purchase_arr[$finRow[csf('po_breakdown_id')]]['qty']=$finRow[csf('qty')];
		$finish_purchase_arr[$finRow[csf('po_breakdown_id')]]['amnt']=$finRow[csf('finish_purchase_amnt')]/$exchange_rate;
	}
	
	$sql_fin_purchase="select b.po_breakdown_id, sum(b.quantity) as qty, sum(a.cons_rate*b.quantity) as woven_purchase_amnt from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category=3 and a.transaction_type=1 and b.entry_form=17 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id in($po_id) group by b.po_breakdown_id";
	$dataArrayFinPurchase=sql_select($sql_fin_purchase);
	foreach($dataArrayFinPurchase as $finRow)
	{
		$finish_purchase_arr[$finRow[csf('po_breakdown_id')]]['qty']+=$finRow[csf('qty')];
		$finish_purchase_arr[$finRow[csf('po_breakdown_id')]]['amnt']+=$finRow[csf('woven_purchase_amnt')]/$exchange_rate;
	}
?>
    <div>
        <fieldset style="width:710px; margin-left:8px">
        	<table class="rpt_table" width="680" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="40">SL</th>
                    <th width="60">Buyer</th>
                    <th width="100">PO No</th>
                    <th width="50">Year</th>
                    <th width="60">Job No</th>
                    <th width="110">Order Qty.</th>
                    <th width="110">Received</th>
                    <th>Cost ($)</th>
                </thead>
            </table>	
            <div style="width:700px; max-height:310px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="680" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
					else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
					else $year_field="";//defined Later
						
					$sql="select a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id, b.po_number, b.pub_shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, b.shiping_status from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_id) order by b.pub_shipment_date, a.job_no_prefix_num, b.id";
					$result=sql_select($sql);
					$i=1; $tot_po_qnty=0; $tot_recv_qty=0; $tot_cost=0;
					foreach($result as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
						$tot_po_qnty+=$order_qnty_in_pcs;
						
						$recv_qty=$finish_purchase_arr[$row[csf('id')]]['qty'];
						$recv_cost=$finish_purchase_arr[$row[csf('id')]]['amnt'];
						
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="60" style="word-break:break-all;"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
                            <td width="100" style="word-break:break-all;"><? echo $row[csf('po_number')]; ?></td>
                            <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                            <td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                            <td width="110" align="right"><? echo $order_qnty_in_pcs; ?></td>
							<td width="110" align="right"><? echo number_format($recv_qty,2,'.',''); ?></td>
                            <td align="right" style="padding-right:2px"><? echo number_format($recv_cost,2,'.',''); ?></td>
						</tr>
					<?
						$i++;
						$tot_recv_qty+=$recv_qty; 
						$tot_cost+=$recv_cost; 
					}
					?>
                	<tfoot>
                        <th colspan="5">Total</th>
                        <th><? echo $tot_po_qnty; ?></th>
                        <th><? echo number_format($tot_recv_qty,2,'.',''); ?></th>
                        <th><? echo number_format($tot_cost,2,'.',''); ?></th>
                    </tfoot>    
                </table>
            </div>
        </fieldset>
    </div>
<?
	exit();
}

if($action=="aop_cost_actual")
{
	echo load_html_head_contents("AOP Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

?>
	<fieldset style="width:760px; margin-left:7px">
        <table class="rpt_table" width="755" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="110">WO NO.</th>
                <th width="80">WO Date</th>
                <th width="80">Currency</th>
                <th width="120">Amount (Taka)</th>
                <th width="120">Conversion rate</th>
                <th>Amount (USD)</th>
            </thead>
        </table>
        <div style="width:755px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="735" cellpadding="0" cellspacing="0">    
				<?
                $i=1; $total_aop_cost=0; $avg_rate=76;
                $sql="select a.booking_no, a.booking_date, a.currency_id, a.exchange_rate, sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id=$po_id and a.item_category=12 and a.booking_type in(3,6) and b.process=35 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no, a.booking_date, a.currency_id, a.exchange_rate";
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('booking_no')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                        <td width="80"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
                        <td align="right" width="120">
                            <? 
								$amnt_tk=$row[csf('amount')]*$row[csf('exchange_rate')];
                                echo number_format($amnt_tk,2); 
                            ?>
                        </td>
                        <td align="right" width="120"><? echo number_format($row[csf('exchange_rate')],2); ?>&nbsp;</td>
                        <td align="right">
                            <?
								if($row[csf('currency_id')]==1)
								{
                                	$amount=$row[csf('amount')]/$row[csf('exchange_rate')];
								}
								else
								{
									$amount=$row[csf('amount')];
								}
                                echo number_format($amount,2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_aop_cost+=$amount;
                }
                ?>
                <tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Total</th>
                    <th align="right"><? echo number_format($total_aop_cost,2); ?></th>
                </tfoot>
			</table>
        </div>	
	</fieldset>  
<?
exit();
}

if($action=="trims_cost_actual")
{
	echo load_html_head_contents("AOP Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

?>
	<fieldset style="width:760px; margin-left:7px">
        <table class="rpt_table" width="755" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="110">WO NO.</th>
                <th width="80">WO Date</th>
                <th width="80">Currency</th>
                <th width="120">Amount (Taka)</th>
                <th width="120">Conversion rate</th>
                <th>Amount (USD)</th>
            </thead>
        </table>
        <div style="width:755px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="735" cellpadding="0" cellspacing="0">    
				<?
                $i=1; $total_trims_cost=0; $avg_rate=76;
                $sql="select a.booking_no, a.booking_date, a.currency_id, a.exchange_rate, sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id=$po_id and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no, a.booking_date, a.currency_id, a.exchange_rate";
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('booking_no')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                        <td width="80"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
                        <td align="right" width="120">
                            <? 
								$amnt_tk=$row[csf('amount')]*$row[csf('exchange_rate')];
                                echo number_format($amnt_tk,2); 
                            ?>
                        </td>
                        <td align="right" width="120"><? echo number_format($row[csf('exchange_rate')],2); ?>&nbsp;</td>
                        <td align="right">
                            <?
								if($row[csf('currency_id')]==1)
								{
                                	$amount=$row[csf('amount')]/$row[csf('exchange_rate')];
								}
								else
								{
									$amount=$row[csf('amount')];
								}
                                echo number_format($amount,2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_trims_cost+=$amount;
                }
                ?>
                <tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Total</th>
                    <th align="right"><? echo number_format($total_trims_cost,2); ?></th>
                </tfoot>
			</table>
        </div>	
	</fieldset>  
<?
exit();
}

if($action=="embell_cost_actual")
{
	echo load_html_head_contents("Embellishment Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

?>
	<fieldset style="width:760px; margin-left:7px">
        <table class="rpt_table" width="755" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="110">WO NO.</th>
                <th width="80">WO Date</th>
                <th width="80">Currency</th>
                <th width="120">Amount (Taka)</th>
                <th width="120">Conversion rate</th>
                <th>Amount (USD)</th>
            </thead>
        </table>
        <div style="width:755px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="735" cellpadding="0" cellspacing="0">    
				<?
                $i=1; $total_aop_cost=0; $avg_rate=76;
                $sql="select a.booking_no, a.booking_date, a.currency_id, a.exchange_rate, sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and c.emb_name!=3 and b.po_break_down_id=$po_id and a.item_category=25 and a.booking_type in(3,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no, a.booking_date, a.currency_id, a.exchange_rate";
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('booking_no')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                        <td width="80"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
                        <td align="right" width="120">
                            <? 
								$amnt_tk=$row[csf('amount')]*$row[csf('exchange_rate')];
                                echo number_format($amnt_tk,2); 
                            ?>
                        </td>
                        <td align="right" width="120"><? echo number_format($row[csf('exchange_rate')],2); ?>&nbsp;</td>
                        <td align="right">
                            <?
								if($row[csf('currency_id')]==1)
								{
                                	$amount=$row[csf('amount')]/$row[csf('exchange_rate')];
								}
								else
								{
									$amount=$row[csf('amount')];
								}
                                echo number_format($amount,2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_aop_cost+=$amount;
                }
                ?>
                <tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Total</th>
                    <th align="right"><? echo number_format($total_aop_cost,2); ?></th>
                </tfoot>
			</table>
        </div>	
	</fieldset>  
<?
exit();
}

if($action=="wash_cost_actual")
{
	echo load_html_head_contents("Wash Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);

?>
	<fieldset style="width:760px; margin-left:7px">
        <table class="rpt_table" width="755" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="110">WO NO.</th>
                <th width="80">WO Date</th>
                <th width="80">Currency</th>
                <th width="120">Amount (Taka)</th>
                <th width="120">Conversion rate</th>
                <th>Amount (USD)</th>
            </thead>
        </table>
        <div style="width:755px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="735" cellpadding="0" cellspacing="0">    
				<?
                $i=1; $total_aop_cost=0; $avg_rate=76;
                $sql="select a.booking_no, a.booking_date, a.currency_id, a.exchange_rate, sum(b.amount) as amount from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and c.emb_name=3 and b.po_break_down_id=$po_id and a.item_category=25 and a.booking_type in(3,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no, a.booking_date, a.currency_id, a.exchange_rate";
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[csf('booking_no')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                        <td width="80"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
                        <td align="right" width="120">
                            <? 
								$amnt_tk=$row[csf('amount')]*$row[csf('exchange_rate')];
                                echo number_format($amnt_tk,2); 
                            ?>
                        </td>
                        <td align="right" width="120"><? echo number_format($row[csf('exchange_rate')],2); ?>&nbsp;</td>
                        <td align="right">
                            <?
								if($row[csf('currency_id')]==1)
								{
                                	$amount=$row[csf('amount')]/$row[csf('exchange_rate')];
								}
								else
								{
									$amount=$row[csf('amount')];
								}
                                echo number_format($amount,2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_aop_cost+=$amount;
                }
                ?>
                <tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Total</th>
                    <th align="right"><? echo number_format($total_aop_cost,2); ?></th>
                </tfoot>
			</table>
        </div>	
	</fieldset>  
<?
exit();
}

if($action=="commission_cost_actual")
{
	echo load_html_head_contents("Commission Cost Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	$data=explode("_",$po_id);
	$poId=$data[0];
	$job_no=$data[1];
	$ex_factory_qty=$data[2];
	$dzn_qnty=$data[3];

?>
	<fieldset style="width:560px; margin-left:10px">
        <table class="rpt_table" width="555" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="40">SL</th>
                <th width="170">Commission Type</th>
                <th width="170">Commission Base</th>
                <th>Amount (USD)</th>
            </thead>
        </table>
        <div style="width:555px; max-height:330px; overflow-y:scroll" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="535" cellpadding="0" cellspacing="0">    
				<?
                $i=1; $total_comm_cost=0;
                $sql="select particulars_id, commission_base_id, commission_amount as amount from wo_pre_cost_commiss_cost_dtls where job_no='$job_no' and status_active=1 and is_deleted=0 and commission_amount>0";
                $result=sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="170"><p><? echo $commission_particulars[$row[csf('particulars_id')]]; ?></p></td>
                        <td width="170"><p><? echo $commission_base_array[$row[csf('commission_base_id')]]; ?></p></td>
                        <td align="right">
                            <?
								$amount=($ex_factory_qty/$dzn_qnty)*$row[csf('amount')];
                                echo number_format($amount,2); 
                            ?>
                        </td>
                    </tr>
                <?
                	$i++;
					$total_comm_cost+=$amount;
                }
                ?>
                <tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>Total</th>
                    <th align="right"><? echo number_format($total_comm_cost,2); ?></th>
                </tfoot>
			</table>
        </div>	
	</fieldset>  
<?
exit();
}
//Ex-Factory Delv. and Return
if($action=="ex_factory_popup")
{
 	echo load_html_head_contents("Ex-Factory Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $id;//$job_no;
	
	$unit_price_arr=return_library_array( "select id, unit_price 
	from wo_po_break_down where status_active=1 and is_deleted=0 and id=$id ", "id", "unit_price");	
	?>
    
     <script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
</script>
	<div style="width:100%" align="center" id="report_container">
    <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<fieldset style="width:550px"> 
        <div class="form_caption" align="center"><strong>Ex-Factory Details</strong></div><br />
            <div style="width:100%"> 
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="90">Ex-fac. Date</th>
                        <th width="120">System /Challan no</th>
                        <th width="100">Ex-Fact. Del.Qty.</th>
                        <th width="100">Ex-Fact. Return Qty.</th>
                        <th width="">Ex-Fact. Value</th>
                       
                     </tr>   
                </thead> 	 	
            </table>  
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1;
              
				$exfac_sql=("select b.challan_no,b.ex_factory_date, 
				CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
				CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty 
				from   pro_ex_factory_mst b  where  b.status_active=1 and b.is_deleted=0 and b.po_break_down_id in($id) ");
                $sql_dtls=sql_select($exfac_sql);
                
                foreach($sql_dtls as $row_real)
                { 
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF"; 
					$tot_exfact_qty=$row_real[csf("ex_factory_qnty")]-$row_real[csf("ex_factory_return_qnty")]; 
					$unit_price=$unit_price_arr[$id]; 
					$tot_exfact_val=$tot_exfact_qty*$unit_price;                            
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="35"><? echo $i; ?></td> 
                        <td width="90"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
                        <td width="120"><? echo $row_real[csf("challan_no")]; ?></td>
                        <td width="100" align="right"><? echo $row_real[csf("ex_factory_qnty")]; ?></td>
                        <td width="100" align="right"><? echo $row_real[csf("ex_factory_return_qnty")]; ?></td>
                        <td width="" align="right"><? echo number_format($tot_exfact_val,2); ?></td>
                    </tr>
                    <? 
                    $rec_qnty+=$row_real[csf("ex_factory_qnty")];
					 $rec_return_qnty+=$row_real[csf("ex_factory_return_qnty")];
					  $total_exfact_val+=$tot_exfact_val;
                    $i++;
                }
                ?>
                <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                     <th><? echo number_format($rec_qnty,2); ?></th>
                    <th><? echo number_format($rec_return_qnty,2); ?></th>
                    <th><? echo number_format($total_exfact_val,2); ?></th>
                </tr>
                <tr>
                 <th colspan="3">Total Balance</th>
                 <th colspan="2" align="right"><? echo number_format($rec_qnty-$rec_return_qnty,2); ?></th>
                 <th><? echo number_format($total_exfact_val,2); ?></th>
                </tr>
                </tfoot>
            </table>
        </div> 
		</fieldset>
	</div>    
	<?
    exit();	
}
//Ex-Factory Delv. and Return
if($action=="show_po_detail_report")
{
	?>
    <script>
	
	function new_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('html_print_data').innerHTML+'</body</html>');
		d.close(); 
	}
	function openmypage_mkt(mkt_data,type,tittle)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'woven_post_costing_report_controller.php?mkt_data='+mkt_data+'&action='+type, tittle, 'width=660px, height=200px, center=1, resize=0, scrolling=0', '../../../');
	}
	
	function openmypage(po_id,type,tittle)
	{
		var popup_width='';
		if(type=="dye_fin_cost") 
		{
			popup_width='1140px';
		}
		else if(type=="fabric_purchase_cost") 
		{
			popup_width='740px';
		}
		else popup_width='1060px';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'woven_post_costing_report_controller.php?po_id='+po_id+'&action='+type, tittle, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../../');
	}
	
	
</script>
<?	
		
		
		echo load_html_head_contents("Po Detail", "../../../../", 1, 1,$unicode,'','');
		extract($_REQUEST);
		$company_short_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
		
		
	  	$bgcolor="#E9F3FF";  $bgcolor2="#FFFFFF";	
		
	  	 $gsm_weight_top=return_field_value("gsm_weight", "wo_pre_cost_fabric_cost_dtls", "job_no='$job_no' and body_part_id=1");
		 $gsm_weight_bottom=return_field_value("gsm_weight", "wo_pre_cost_fabric_cost_dtls", "job_no='$job_no' and body_part_id=20");
		 $costing_date=return_field_value("costing_date", "wo_pre_cost_mst", "job_no='$job_no'","costing_date");
		 $po_qty=0;
		 $po_plun_cut_qty=0;
		 $total_set_qnty=0;$tot_po_value=0;
		 $sql_po="select a.job_no,a.total_set_qnty,b.id,c.order_total,b.unit_price,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst   and b.id=c.po_break_down_id and b.id =$po_id  and a.job_no ='$job_no'  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id";
		$sql_po_data=sql_select($sql_po);
		foreach($sql_po_data as $sql_po_row)
		{
		$po_qty+=$sql_po_row[csf('order_quantity')];
		$po_plun_cut_qty+=$sql_po_row[csf('plan_cut_qnty')];
		$tot_po_value+=$sql_po_row[csf('order_total')];
		$total_set_qnty=$sql_po_row[csf('total_set_qnty')];
		$unit_price=$sql_po_row[csf('unit_price')]/$total_set_qnty;
		$tot_po_value=$po_qty*$unit_price;
		}
		$fab_knit_req_kg_avg=0;
		$fab_woven_req_yds_avg=0;
		if($db_type==0)
		{
		$sql = "select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.gmts_item_id,a.order_uom,a.job_quantity,a.avg_unit_price,b.costing_per,b.budget_minute,b.approved,b.updated_by,b.sew_smv,b.sew_effi_percent,b.incoterm,b.exchange_rate,c.fab_knit_req_kg,c.fab_knit_fin_req_kg,c.fab_woven_req_yds,c.fab_woven_fin_req_yds,c.fab_yarn_req_kg from wo_po_details_master a, wo_pre_cost_mst b left join wo_pre_cost_sum_dtls c on   b.job_no=c.job_no where a.job_no=b.job_no and a.job_no='$job_no' and a.company_name=$company_name  and a.status_active=1  order by a.job_no";  
		}
		if($db_type==2)
		{
		 $sql = "select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.gmts_item_id,a.order_uom,a.job_quantity,a.avg_unit_price,b.costing_per,b.budget_minute,b.approved,b.updated_by,b.sew_smv,b.sew_effi_percent,b.incoterm,b.exchange_rate,c.fab_knit_req_kg,c.fab_knit_fin_req_kg,c.fab_woven_req_yds,c.fab_woven_fin_req_yds,c.fab_yarn_req_kg from wo_po_details_master a, wo_pre_cost_mst b left join wo_pre_cost_sum_dtls c on   b.job_no=c.job_no where a.job_no=b.job_no and a.status_active=1 and a.job_no='$job_no' and a.company_name=$company_name  order by a.job_no";  
		}
		
		$data_array=sql_select($sql);
		
		
		$condition= new condition();
		if(str_replace("'","",$job_no) !='')
		{
		  $condition->job_no("='$job_no'");
		}
		if(str_replace("'","",$po_id) !='')
		{
		  $condition->po_id("=$po_id");
		}
		 $condition->init();
		
		$fabric= new fabric($condition);
		$fabric_costing_qty_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
		$yarn= new yarn($condition);
	    $yarn_req_arr=$yarn->getOrderWiseYarnQtyArray();
				
		
		 $fab_knit_qty_gray=($fabric_costing_qty_arr['knit']['grey'][$po_id]/$po_qty)*12;
		 $fab_woven_qty_gray=($fabric_costing_qty_arr['woven']['grey'][$po_id]/$po_qty)*12;
		 
		 $fab_knit_qty_finish=($fabric_costing_qty_arr['knit']['finish'][$po_id]/$po_qty)*12;
		 $fab_woven_qty_finish=($fabric_costing_qty_arr['woven']['finish'][$po_id]/$po_qty)*12;
		 $yarn_req_qty_avg=($yarn_req_arr[$po_id]/$po_qty)*12;
		 //echo  $fab_knit_qty_gray.'='.$fab_woven_qty_gray;
		 
	?>
	<div style="width:100%" align="center"> <input type="button" onClick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:120px;"/>
		<fieldset style="width:850px" id="html_print_data">
       
         <table width="840px"   cellpadding="0" cellspacing="0" border="0">
        	<tr>
            	<td rowspan="3" width="25%">
                   <?
                $data_array2=sql_select("select image_location  from common_photo_library  where master_tble_id='$company_name' and form_name='company_details' and is_deleted=0 and file_type=1");
              	foreach($data_array2 as $img_row)
                {
					?>
                    <img src='../../../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50' align="middle" />	
                    <? 
                }
                ?>
                </td>
            	<td align="center"><b style="font-size:20px;"><? echo $company_short_arr[$company_name]; ?></b></td>
            	<td rowspan="3" width="25%" align="right">
                <?
                   $data_array_img=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");               
                foreach($data_array_img as $img_row)
                {
					?>
                    <img src='../../../../<? echo $img_row[csf('image_location')]; ?>' height='50' width='80' align="middle" />	
                    <? 
                }
                ?>
               
                </td>
            </tr>
        	<tr>
            	<td align="center"><b style="font-size:14px;">Pre and Post Cost Comparison</b></td>
            </tr>
           <tr>
                <td align="center">
                    
					<? if( $data_array[0][csf("approved")]==1){echo "<div style='font-size:18px; color:#F00; background:#CCC;'>THIS COST SHEET IS APPROVED </div>";
					
					} else {echo "&nbsp;";} 
					
					$prepared_app_data = $data_array[0][csf("updated_by")];		

					?> 
                     
                </td>
            </tr>
        
        </table>
       			 <? 
				$uom="";
				foreach ($data_array as $row)
				{	
					$order_price_per_dzn=0;
					$order_job_qnty=0;
					$avg_unit_price=0;
					
					$order_values = $row[csf("job_quantity")]*$row[csf("avg_unit_price")];
					$result =sql_select("select po_number,grouping,file_no,pub_shipment_date from wo_po_break_down where job_no_mst='$job_no'  and id=$po_id and status_active=1 and is_deleted=0 order by pub_shipment_date DESC");
					$job_in_orders = '';$pulich_ship_date='';$job_in_file = '';$job_in_ref = '';
					foreach ($result as $val)
					{
						$job_in_orders= $val[csf('po_number')];
						$pulich_ship_date = $val[csf('pub_shipment_date')];
						$job_in_ref.=$val[csf('grouping')].",";
						$job_in_file.=$val[csf('file_no')].",";
						
					}
					$job_in_orders = $job_in_orders;
					
					$job_ref=array_unique(explode(",",rtrim($job_in_ref,", ")));
					$job_file=array_unique(explode(",",rtrim($job_in_file,", ")));
					
					foreach ($job_ref as $ref)
					{
						$ref_cond.=", ".$ref;
					}
					$file_con='';
					foreach ($job_file as $file)
					{
						if($file_con=='') $file_cond=$file; else $file_cond.=", ".$file;
					}
					if($row[csf("costing_per")]==1){$order_price_per_dzn=12;$costing_for=" For 1 DZN";}
					else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;$costing_for=" For PCS";}
					else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;$costing_for=" For 2 DZN";}
					else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;$costing_for="For 3 DZN";}
					else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;$costing_for=" For 4 DZN";}
		
		?>
            	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px" rules="all">
                	<tr bgcolor="<? echo $bgcolor2;?>">
                    	<td width="130">Job Number</td>
                        <td width="100"><b><? echo $row[csf("job_no")]; ?></b></td>
                        <td width="130">Buyer</td>
                        <td width="110"><b><? echo $buyer_arr[$row[csf("buyer_name")]]; $buyer_id=$row[csf("buyer_name")];?></b></td>
                        <td>Garments Item</td>
                        <? 
							$grmnt_items = "";
							if($garments_item[$row[csf("gmts_item_id")]]=="")
							{
								
								$grmts_sql = sql_select("select job_no,gmts_item_id,set_item_ratio from wo_po_details_mas_set_details where job_no=$job_no");
								foreach($grmts_sql as $key=>$val){
									$grmnt_items .=$garments_item[$val[csf("gmts_item_id")]].", ";
								}
								$grmnt_items = substr_replace($grmnt_items,"",-1,1);
							}else{
								$grmnt_items = $garments_item[$row[csf("gmts_item_id")]];
							}
							
						?>
                        <td width="160"><b><? echo $grmnt_items; ?></b></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                    	<td>Style Ref. No </td>
                        <td><b><? echo $row[csf("style_ref_no")]; ?></b></td>
                         <td>Costing Date</td><td><? echo $costing_date;?></td>
                        <td>PO Qnty</td>
                        <td><b><? $uom=$row[csf("order_uom")]; echo number_format($po_qty)." ". $unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2;?>">
                    	<td>Order Numbers</td>
                        <td colspan="3"><? echo $job_in_orders; ?></td>
                        <td>Plan Cut Qnty [Cut % <? echo number_format((($po_plun_cut_qty/$total_set_qnty-$po_qty)/$po_qty)*100,2);?>]</td>
                        <td><b><? $uom=$row[csf("order_uom")]; echo number_format($po_plun_cut_qty/$total_set_qnty)." ". $unit_of_measurement[$row[csf("order_uom")]]; ?></b></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor;?>">
                    	<td>Knit Fabric Cons</td>
                        <td><b><? echo $fab_knit_qty_gray;$fab_knit_req_kg_avg+=$fab_knit_qty_gray; ?> (Kg)</b></td>
	
                        <td>Woven Fabric Cons</td>
                        <td><b><? echo $fab_woven_qty_gray;$fab_woven_req_yds_avg+= $fab_woven_qty_gray;?> (Yds)</b></td>
                        <td>Price Per Unit</td>
                        <td><b><? echo number_format($row[csf("avg_unit_price")],2); ?> USD</b></td>
                    </tr>
                    
                    <tr bgcolor="<? echo $bgcolor2;?>">
                    	<td>Avg Yarn Req</td>
                        <td><b><? echo $yarn_req_qty_avg; ?> (Kg)</b></td>
                        <td>Woven Fin Fabric Cons</td>
                        <td><b><? echo $fab_woven_qty_finish; ?>(Yds)</b></td>
                        
                        <td>Order Value</td>
                        <td><b><? echo number_format($po_qty*$row[csf("avg_unit_price")],2); ?></b></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor;?>">
                    	<td>Knit Fin Fabric Cons</td>
                        <td><b><? echo $fab_knit_qty_finish; ?> (Kg)</b></td>
                        <td>Costing Per</td>
                        <td><b><? echo $costing_for; ?></b></td>
                        <td>Inco Term</td>
                        <td><b><? echo $incoterm[$row[csf("incoterm")]]; ?></b></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2;?>">
                        <td>GSM</td>
                        <td><b><? echo $gsm_weight_top.",".$gsm_weight_bottom ?></b></td>
                        <td>SMV</td>
                        <td><b><? $sew_smv=$row[csf("sew_smv")]; echo number_format($sew_smv,2); ?> </b></td>
                        <td>Efficiency %</td>
                        <td colspan="1"><b><? echo $sew_effi_percent=$row[csf("sew_effi_percent")] ?> </b></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor;?>">
                        <td>Exchange Rate</td>
                        <td><b><? echo $exchange_rate=$row[csf("exchange_rate")]; ?></b></td>
                        <td>Budget SAH</td>
                        <td><b><? echo number_format(($row[csf("sew_smv")]*$po_qty)/60,2); ?></b></td>
                        <td>Shipment Date </td>
                        <td><b><? echo $pulich_ship_date;?></b></td>
                    </tr>
                    
                    
                    
                    
                </table>
            <?	
			
			
	}//end first foearch
	
				$condition= new condition();
				 $condition->company_name("=$company_name");
				 if(str_replace("'","",$job_no) !=''){
					  $condition->job_no("='$job_no'");
				 }
				if(str_replace("'","",$po_id)!='')
				 {
					 $condition->po_id("=$po_id");
				 }
				 $condition->init();
				$fabric= new fabric($condition);
				$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
				 $yarn= new yarn($condition);
				 $yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
				 $conversion= new conversion($condition);
				 $conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
				// print_r($conversion_costing_arr_process);
				 $trims= new trims($condition);
				 $trims_costing_arr=$trims->getAmountArray_by_order();
				$emblishment= new emblishment($condition);
				$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
				$commission= new commision($condition);
				$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
				$commercial= new commercial($condition);
				$commercial_costing_arr=$commercial->getAmountArray_by_order();
				$other= new other($condition);
				$other_costing_arr=$other->getAmountArray_by_order();
				$wash= new wash($condition);
				$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();
				
				$exchange_rate=76; 
				$sql_fin_purchase="select b.po_breakdown_id, sum(a.cons_rate*b.quantity) as finish_purchase_amnt from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.receive_basis<>9 and a.item_category=2 and a.transaction_type=1 and b.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id=$po_id group by b.po_breakdown_id";
				$dataArrayFinPurchase=sql_select($sql_fin_purchase);
				foreach($dataArrayFinPurchase as $finRow)
				{
					$finish_purchase_amnt_arr[$finRow[csf('po_breakdown_id')]]=$finRow[csf('finish_purchase_amnt')]/$exchange_rate;
				}
				
				 $subconInBillDataArray=sql_select("select b.order_id, 
									  sum(CASE WHEN a.process_id=2 THEN b.amount END) AS knit_bill,
									  sum(CASE WHEN a.process_id=4 THEN b.amount END) AS dye_finish_bill
									  from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_source=1 and a.process_id in(2,4) and b.order_id=$po_id group by b.order_id");
				foreach($subconInBillDataArray as $subRow)
				{
					$subconCostArray[$subRow[csf('order_id')]]['knit_bill']=$subRow[csf('knit_bill')];
					$subconCostArray[$subRow[csf('order_id')]]['dye_finish_bill']=$subRow[csf('dye_finish_bill')];
				}	
				$prodcostDataArray=sql_select("select job_no, 
									  sum(CASE WHEN cons_process=1 THEN amount END) AS knit_charge,
									  sum(CASE WHEN cons_process=30 THEN amount END) AS yarn_dye_charge,
									  sum(CASE WHEN cons_process=35 THEN amount END) AS aop_charge,
									  sum(CASE WHEN cons_process not in(1,2,30,35) THEN amount END) AS dye_finish_charge
									  from wo_pre_cost_fab_conv_cost_dtls where status_active=1 and is_deleted=0 group by job_no");
				foreach($prodcostDataArray as $prodRow)
				{
					$prodcostArray[$prodRow[csf('job_no')]]['knit_charge']=$prodRow[csf('knit_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['yarn_dye_charge']=$prodRow[csf('yarn_dye_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['aop_charge']=$prodRow[csf('aop_charge')];
					$prodcostArray[$prodRow[csf('job_no')]]['dye_finish_charge']=$prodRow[csf('dye_finish_charge')];
				}
				$actualCostDataArray=sql_select("select cost_head,po_id,sum(amount_usd) as amount_usd from wo_actual_cost_entry where company_id=$company_name and status_active=1 and is_deleted=0 group by cost_head,po_id");
				foreach($actualCostDataArray as $actualRow)
				{
				   $actualCostArray[$actualRow[csf('cost_head')]][$actualRow[csf('po_id')]]=$actualRow[csf('amount_usd')];
				}
					
				$bookingDataArray=sql_select("select a.booking_type, a.item_category, a.currency_id, a.exchange_rate, b.po_break_down_id, b.process, b.amount, b.pre_cost_fabric_cost_dtls_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id=$po_id and  a.company_id=$company_name and a.item_category in(4,12,25) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				foreach($bookingDataArray as $woRow)
				{
					$amount=0; $trimsAmnt=0;
					if($woRow[csf('currency_id')]==1) { $amount=$woRow[csf('amount')]/$exchange_rate; } else { $amount=$woRow[csf('amount')]; }
					
					if($woRow[csf('item_category')]==25 && ($woRow[csf('booking_type')]==3 || $woRow[csf('booking_type')]==6)) 
					{ 
						if($embell_type_arr[$woRow[csf('pre_cost_fabric_cost_dtls_id')]]==3)
						{
							$washCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
						}
						else
						{
							$embellCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
						}
					}
					else if($woRow[csf('item_category')]==12 && $woRow[csf('process')]==35 && ($woRow[csf('booking_type')]==3 || $woRow[csf('booking_type')]==6)) 
					{ 
						$aopCostArray[$woRow[csf('po_break_down_id')]]+=$amount; 
					}
					else if($woRow[csf('item_category')]==4)
					{
						if($woRow[csf('currency_id')]==1) { $trimsAmnt=$woRow[csf('amount')]/$woRow[csf('exchange_rate')]; } else { $trimsAmnt=$woRow[csf('amount')]; }
						$actualTrimsCostArray[$woRow[csf('po_break_down_id')]]+=$trimsAmnt; 
					}
				}
				$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master where item_category_id=1", "id", "avg_rate_per_unit"  );
				$receive_array=array();
				$sql_receive="select prod_id, sum(order_qnty) as qty, sum(order_amount) as amnt from inv_transaction where transaction_type=1 and item_category=1 and status_active=1 and is_deleted=0 group by prod_id";
				$resultReceive = sql_select($sql_receive);
				foreach($resultReceive as $invRow)
				{
					$avg_rate=$invRow[csf('amnt')]/$invRow[csf('qty')];
					$receive_array[$invRow[csf('prod_id')]]=$avg_rate;
				}
				$yarnTrimsDataArray=sql_select("select b.po_breakdown_id, b.prod_id, a.item_category, 
					sum(CASE WHEN a.transaction_type=2 and b.entry_form ='3' and b.trans_type=2 and b.issue_purpose!=2 THEN b.quantity ELSE 0 END) AS yarn_iss_qty,
					sum(CASE WHEN a.transaction_type=4 and b.entry_form ='9' and b.trans_type=4 THEN b.quantity ELSE 0 END) AS yarn_iss_return_qty,
					sum(CASE WHEN a.transaction_type=5 and b.entry_form ='11' and b.trans_type=5 THEN b.quantity ELSE 0 END) AS trans_in_qty_yarn,
					sum(CASE WHEN a.transaction_type=6 and b.entry_form ='11' and b.trans_type=6 THEN b.quantity ELSE 0 END) AS trans_out_qty_yarn,
					sum(CASE WHEN a.transaction_type=2 and b.entry_form ='25' and b.trans_type=2 THEN a.cons_rate*b.quantity ELSE 0 END) AS trims_issue_amnt
					from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category in(1,4) and a.transaction_type in(2,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and b.po_breakdown_id=$po_id group by b.po_breakdown_id, b.prod_id, a.item_category");
				foreach($yarnTrimsDataArray as $invRow)
				{
					if($invRow[csf('item_category')]==1)
					{
						$iss_qty=$invRow[csf('yarn_iss_qty')]+$invRow[csf('trans_in_qty_yarn')]-$invRow[csf('yarn_iss_return_qty')]-$invRow[csf('trans_out_qty_yarn')];
						$rate='';
						if($receive_array[$invRow[csf('prod_id')]]>0)
						{
							$rate=$receive_array[$invRow[csf('prod_id')]];
						}
						else
						{
							$rate=$avg_rate_array[$invRow[csf('prod_id')]]/$exchange_rate;
						}
						
						$iss_amnt=$iss_qty*$rate;
						$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]][1]+=$iss_amnt;
					}
					else
					{
						$yarnTrimsCostArray[$invRow[csf('po_breakdown_id')]][4]+=$invRow[csf('trims_issue_amnt')];
					}
				}
				$yarncostDataArray=sql_select("select job_no, sum(amount) as amnt, sum(rate*avg_cons_qnty) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 and job_no='$job_no' group by job_no");
				foreach($yarncostDataArray as $yarnRow)
				{
				   $yarncostArray[$yarnRow[csf('job_no')]]=$yarnRow[csf('amount')];
				}
				
				$fabriccostDataArray=sql_select("select job_no, costing_per_id, embel_cost, wash_cost, cm_cost, commission, currier_pre_cost, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where status_active=1 and is_deleted=0 and job_no='$job_no'");
				foreach($fabriccostDataArray as $fabRow)
				{
					 $fabriccostArray[$fabRow[csf('job_no')]]['costing_per_id']=$fabRow[csf('costing_per_id')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['embel_cost']=$fabRow[csf('embel_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['wash_cost']=$fabRow[csf('wash_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['cm_cost']=$fabRow[csf('cm_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['commission']=$fabRow[csf('commission')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['currier_pre_cost']=$fabRow[csf('currier_pre_cost')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['lab_test']=$fabRow[csf('lab_test')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['inspection']=$fabRow[csf('inspection')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['freight']=$fabRow[csf('freight')];
					 $fabriccostArray[$fabRow[csf('job_no')]]['comm_cost']=$fabRow[csf('comm_cost')];
				}
				
				$ex_factory_arr=return_library_array( "select po_break_down_id, 
				sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as qnty 
				from pro_ex_factory_mst where status_active=1 and is_deleted=0 and po_break_down_id =$po_id group by po_break_down_id", "po_break_down_id", "qnty");
				
					$dzn_qnty=0;
					$costing_per_id=$fabriccostArray[$job_no]['costing_per_id'];
					if($costing_per_id==1)
					{
						$dzn_qnty=12;
					}
					else if($costing_per_id==3)
					{
						$dzn_qnty=12*2;
					}
					else if($costing_per_id==4)
					{
						$dzn_qnty=12*3;
					}
					else if($costing_per_id==5)
					{
						$dzn_qnty=12*4;
					}
					else
					{
						$dzn_qnty=1;
					}
					
				 $ex_factory_qty=$ex_factory_arr[$po_id];
				 $unit_price=$unit_price/$total_set_qnty;
				$tot_ex_factory_val=$ex_factory_qty*$unit_price;
				$tot_fabric_purchase_cost_mkt=$fabric_costing_arr['knit']['grey'][$po_id]+$fabric_costing_arr['woven']['grey'][$po_id];
				$tot_fabric_purchase_cost_actual=$finish_purchase_amnt_arr[$po_id];
				$tot_yarn_cost_mkt=$yarn_costing_arr[$po_id];
				$tot_yarn_dye_cost_mkt=$conversion_costing_arr_process[$po_id][30];
				$tot_knit_cost_mkt=$conversion_costing_arr_process[$po_id][1];
				
					$tot_dye_finish_cost_mkt=0;$not_yarn_dyed_cost_arr=array(1,2,30,35);
					foreach($conversion_cost_head_array as $process_id=>$val)
					{
						if(!in_array($process_id,$not_yarn_dyed_cost_arr))
						{
							$tot_dye_finish_cost_mkt+=$conversion_costing_arr_process[$po_id][$process_id];
						}
					}
					$tot_dye_finish_cost_actual=$subconCostArray[$po_id]['dye_finish_bill']/$exchange_rate;
					$tot_knit_cost_actual=$subconCostArray[$po_id]['knit_bill']/$exchange_rate;
					
					$tot_aop_cost_actual=$aopCostArray[$po_id];
					$tot_aop_cost_mkt=$conversion_costing_arr_process[$po_id][35];
					$tot_trims_cost_mkt=$trims_costing_arr[$po_id];
					$tot_trims_cost_actual=$actualTrimsCostArray[$po_id];
					
					$print_amount=$emblishment_costing_arr_name[$po_id][1];
					$embroidery_amount=$emblishment_costing_arr_name[$po_id][2];
					$special_amount=$emblishment_costing_arr_name[$po_id][4];
					$wash_cost=$emblishment_costing_arr_name_wash[$po_id][3];
					$other_amount=$emblishment_costing_arr_name[$po_id][5];
					
					$tot_embell_cost_mkt=$print_amount+$embroidery_amount+$special_amount+$other_amount;
					$tot_embell_cost_actual=$embellCostArray[$po_id];
					$tot_wash_cost_mkt=$wash_cost;
					$tot_wash_cost_actual=$washCostArray[$po_id];
					
					$foreign_cost=$commission_costing_arr[$po_id][1];
					$local_cost=$commission_costing_arr[$po_id][2];
					
					
					
					$tot_commission_cost_mkt=$foreign_cost+$local_cost;
					$tot_commission_cost_actual=($ex_factory_qty/$dzn_qnty)*$fabriccostArray[$job_no]['commission'];
					$tot_comm_cost_actual=$actualCostArray[6][$po_id];
					$tot_comm_cost_mkt=$commercial_costing_arr[$po_id];
					
					$tot_test_cost_mkt=$other_costing_arr[$po_id]['lab_test'];
					$tot_freight_cost_mkt=$other_costing_arr[$po_id]['freight'];
					$tot_inspection_cost_mkt=$other_costing_arr[$po_id]['inspection'];
					$tot_certificate_cos_mktt=$other_costing_arr[$po_id]['certificate_pre_cost'];
					//$common_oh_cost=$other_costing_arr[$row[csf('id')]]['common_oh'];
					$tot_currier_cost_mkt=$other_costing_arr[$po_id]['currier_pre_cost'];
					$tot_cm_cost_mkt=$other_costing_arr[$po_id]['cm_cost'];
					
					$tot_yarn_cost_actual=$yarnTrimsCostArray[$po_id][1];
					
					$tot_freight_cost_actual=$actualCostArray[2][$po_id];
					$tot_test_cost_actual=$actualCostArray[1][$po_id];
					$tot_inspection_cost_actual=$actualCostArray[3][$po_id];
					$tot_currier_cost_actual=$actualCostArray[4][$po_id];
					$tot_cm_cost_actual=$actualCostArray[5][$po_id];
					
					//$tot_mkt_all_cost=$tot_mkt_all_cost;
					$tot_mkt_all_cost=$tot_yarn_cost_mkt+$tot_knit_cost_mkt+$tot_dye_finish_cost_mkt+$tot_yarn_dye_cost_mkt+$tot_aop_cost_mkt+$tot_trims_cost_mkt+$tot_embell_cost_mkt+$tot_wash_cost_mkt+$tot_commission_cost_mkt+$tot_comm_cost_mkt+$tot_freight_cost_mkt+$tot_test_cost_mkt+$tot_inspection_cost_mkt+$tot_currier_cost_mkt+$tot_cm_cost_mkt+$tot_fabric_purchase_cost_mkt;
					
					$tot_mkt_margin=$tot_po_value-$tot_mkt_all_cost;
					$tot_mkt_margin_perc=($mkt_margin/$tot_po_value)*100;
					
					$tot_actual_all_cost=$tot_yarn_cost_actual+$tot_knit_cost_actual+$tot_dye_finish_cost_actual+$tot_yarn_dye_cost_actual+$tot_aop_cost_actual+$tot_trims_cost_actual+$tot_embell_cost_actual+$tot_wash_cost_actual+$tot_commission_cost_actual+$tot_comm_cost_actual+$tot_freight_cost_actual+$tot_test_cost_actual+$tot_inspection_cost_actual+$tot_currier_cost_actual+$tot_cm_cost_actual+$tot_fabric_purchase_cost_actual;
					
					$tot_actual_margin=$tot_ex_factory_val-$tot_actual_all_cost;
					$tot_actual_margin_perc=($tot_actual_margin/$tot_ex_factory_val)*100;
					
	?>
         <br/> <br/>
        <table width="760" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
      
                            <thead>
                                <th width="30">SL</th>
                                <th width="180">Particulars</th>
                                <th width="130">Pre Costing</th>
                                <th width="80">%</th>
                                <th width="130">Post-Costing</th>
                                <th width="80">%</th>
                                <th>Variance</th>
                            </thead> 
							<?
								$bgcolor1='#E9F3FF';
								$bgcolor2='#FFFFFF';
							?>
                            </thead>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_1','<? echo $bgcolor1; ?>')" id="trtd_1">
                                <td align="center">1</td>
                                <td>PO/Shipment Value</td>
                                <td align="right"><? echo number_format($tot_po_value,2); ?></td>
                                <td align="right">&nbsp;</td>
                                <td align="right"><? echo number_format($tot_ex_factory_val,2); ?></td>
                                <td align="right">&nbsp;</td>
                                <td align="right"><? echo number_format($tot_ex_factory_val-$tot_po_value,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_2','<? echo $bgcolor2; ?>')" id="trtd_2">
                                <td colspan="7"><b>Cost</b></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_3','<? echo $bgcolor1; ?>')" id="trtd_3">
                                <td align="center">2</td>
                                <td>Fabric Purchase Cost</td>
                                <td align="right"><? echo number_format($tot_fabric_purchase_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_fabric_purchase_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_fabric_purchase_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_fabric_purchase_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_fabric_purchase_cost_mkt-$tot_fabric_purchase_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_4','<? echo $bgcolor2; ?>')" id="trtd_4">
                                <td align="center">3</td>
                                <td>Yarn Cost+Yarn Dyeing Cost</td>
                                <td align="right"><a href="#report_details" onClick="openmypage_mkt('<? echo $tot_yarn_cost_mkt."**".$tot_yarn_dye_cost_mkt; ?>','mkt_yarn_cost','Grey And Dyed Yarn Mkt. Cost Details')"></a><? echo number_format($tot_yarn_cost_mkt+$tot_yarn_dye_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format((($tot_yarn_cost_mkt+$tot_yarn_dye_cost_mkt)/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_yarn_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_yarn_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format(($tot_yarn_cost_mkt+$tot_yarn_dye_cost_mkt)-$tot_yarn_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_5','<? echo $bgcolor2; ?>')" id="trtd_5">
                                <td align="center">4</td>
                                <td>Knitting Cost</td>
                                <td align="right"><? echo number_format($tot_knit_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_knit_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_knit_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_knit_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_knit_cost_mkt-$tot_knit_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>"  onclick="change_color('trtd_6','<? echo $bgcolor2; ?>')" id="trtd_6">
                                <td align="center">5</td>
                                <td>Dye & Fin Cost</td>
                                <td align="right"><? echo number_format($tot_dye_finish_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_dye_finish_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><a href="#report_details" onClick="openmypage('<? echo $po_ids; ?>','dye_fin_cost','Dye & Fin Cost Details')"></a><? echo number_format($tot_dye_finish_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_dye_finish_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_dye_finish_cost_mkt-$tot_dye_finish_cost_actual,2); ?></td>
                            </tr>
                            
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_7','<? echo $bgcolor1; ?>')" id="trtd_7">
                                <td align="center">6</td>
                                <td>AOP & Others Cost</td>
                                <td align="right"><? echo number_format($tot_aop_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_aop_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_aop_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_aop_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_aop_cost_mkt-$tot_aop_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_8','<? echo $bgcolor2; ?>')" id="trtd_8">
                                <td align="center">7</td>
                                <td>Trims Cost</td>
                                <td align="right"><? echo number_format($tot_trims_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_trims_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_trims_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_trims_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_trims_cost_mkt-$tot_trims_cost_actual,2); ?></td>
                            </tr> 
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_9','<? echo $bgcolor1; ?>')" id="trtd_9">
                                <td align="center">8</td>
                                <td>Embellishment Cost</td>
                                <td align="right"><? echo number_format($tot_embell_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_embell_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_embell_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_embell_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_embell_cost_mkt-$tot_embell_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_10','<? echo $bgcolor2; ?>')" id="trtd_10">
                                <td align="center">9</td>
                                <td>Wash Cost</td>
                                <td align="right"><? echo number_format($tot_wash_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_wash_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_wash_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_wash_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_wash_cost_mkt-$tot_wash_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('trtd_11','<? echo $bgcolor1; ?>')" id="trtd_11">
                                <td align="center">10</td>
                                <td>Commission Cost</td>
                                <td align="right"><? echo number_format($tot_commission_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_commission_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_commission_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_commission_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_commission_cost_mkt-$tot_commission_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_12','<? echo $bgcolor2; ?>')" id="trtd_12">
                                <td align="center">11</td>
                                <td>Commercial Cost</td>
                                <td align="right"><? echo number_format($tot_comm_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_comm_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_comm_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_comm_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_comm_cost_mkt-$tot_comm_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_13','<? echo $bgcolor1; ?>')" id="trtd_13">
                                <td align="center">12</td>
                                <td>Freight Cost</td>
                                <td align="right"><? echo number_format($tot_freight_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_freight_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_freight_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_freight_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_freight_cost_mkt-$tot_freight_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>"  onclick="change_color('trtd_14','<? echo $bgcolor2; ?>')" id="trtd_14">
                                <td align="center">13</td>
                                <td>Testing Cost</td>
                                <td align="right"><? echo number_format($tot_test_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_test_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_test_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_test_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_test_cost_mkt-$tot_test_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_15','<? echo $bgcolor1; ?>')" id="trtd_15">
                                <td align="center">14</td>
                                <td>Inspection Cost</td>
                                <td align="right"><? echo number_format($tot_inspection_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_inspection_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_inspection_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_inspection_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_inspection_cost_mkt-$tot_inspection_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_16','<? echo $bgcolor2; ?>')" id="trtd_16">
                                <td align="center">15</td>
                                <td>Courier Cost</td>
                                <td align="right"><? echo number_format($tot_currier_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_currier_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_currier_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_currier_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_currier_cost_mkt-$tot_currier_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_17','<? echo $bgcolor1; ?>')" id="trtd_17">
                                <td align="center">16</td>
                                <td>CM</td>
                                <td align="right"><? echo number_format($tot_cm_cost_mkt,2); ?></td>
                                <td align="right"><? echo number_format(($tot_cm_cost_mkt/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_cm_cost_actual,2); ?></td>
                                <td align="right"><? echo number_format(($tot_cm_cost_actual/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_cm_cost_mkt-$tot_cm_cost_actual,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('trtd_18','<? echo $bgcolor2; ?>')" id="trtd_18">
                                <td align="center">17</td>
                                <td>Total Cost</td>
                                <td align="right"><? echo number_format($tot_mkt_all_cost,2); ?></td>
                                <td align="right"><? echo number_format(($tot_mkt_all_cost/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_actual_all_cost,2); ?></td>
                                <td align="right"><? echo number_format(($tot_actual_all_cost/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_mkt_all_cost-$tot_actual_all_cost,2); ?></td>
                            </tr>
                            <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('trtd_19','<? echo $bgcolor1; ?>')" id="trtd_19">
                                <td align="center">18</td>
                                <td>Margin/Loss</td>
                                <td align="right"><? echo number_format($tot_mkt_margin,2); ?></td>
                                <td align="right"><? echo number_format(($tot_mkt_margin/$tot_po_value)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_actual_margin,2); ?></td>
                                <td align="right"><? echo number_format(($tot_actual_margin/$tot_ex_factory_val)*100,2); ?></td>
                                <td align="right"><? echo number_format($tot_mkt_margin-$tot_actual_margin,2); ?></td>
                            </tr>
                        </table>
		</fieldset>
	</div>    
	<?
    exit();	
}
//disconnect($con);
?>
<script>
function openmypage(po_id,type,tittle)
	{
		//alert(po_id);
		var popup_width='';
		if(type=="dye_fin_cost") 
		{
			popup_width='1140px';
		}
		else if(type=="fabric_purchase_cost") 
		{
			popup_width='740px';
		}
		else popup_width='1060px';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'woven_post_costing_report_controller.php?po_id='+po_id+'&action='+type, tittle, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '');
	}
</script>